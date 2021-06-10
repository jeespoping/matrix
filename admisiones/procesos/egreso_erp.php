<?php
include_once("conex.php");
/***********************************************************************************************************************************************
 * Actualizaciones
 * 2021-04-27 - Johan Córdoba:
 * 2020-03-25 Camilo Zapata: Dado que un procedimiento puede repetirse siempre y cuando se realicen en diferentes dias, se corrige la validación realizada en este aspecto
 * incluyendo el campo de fecha del procedimiento como criterio diferenciador.
 * 2020-01-22 Camilo Zapata: se valida que haya conexión en unix antes de continuar. para evitar el uso del programa durante las ventanas de mantenimiento.
 * 2019-11-25 : se agrega funcionalidad para que verifique si la empresa tiene la opcion de egreso automatico.
 * 2019-11-08 Andres Alvarez: * Se cambia funcionalidad de fecha y hora de egreso para que compruebe si la fecha de la table mv-000018 se encuentra vacia y coloca la fecha actual y hora actual en el encabezado del egreso, de lo contrario coloca la fecha de mv-000018.
 * 2019-11-01 Andres Alvarez: * Se cambia la hora y fecha de egreso por la fecha y hora  de la tabla movhos18.
 * Se quita validacion para que se puedan agregar especialista sin tener amarrado un servicio, por ende ya puede existir especialistas sin tener ningun servicio.
 * Se quita la clase que pinta de amarrillo cuando un especialista no tiene un servicio amarrado.
 *
 * 2019-10-15 Camilo Zapata: -Se traen todos los diagnosticos que recibió el paciente durante su estancia, tabla 000272 de movhos.
 * -Se modifica el programa para que en los centros de costos que realicen egresos automáticos, deje editar las autorizaciones.
 * -El medico de ingreso se elige según el que haya firmado cualquiera de los formularios configurados como historia de ingreso en root_51
 * parámetro:"formulariosDiagnosticosIngreso"
 * -El médico tratante y de egreso se selecciona por defecto, el último que haya firmado algún formulario.
 * -El campo EISP se diligenciará automáticamente a partir del campo notificar de la tabla de diagnósticos "root_000011"
 *
 *
 * 2019-03-01 camilo zapata: adicion de la condición "root_000012 r on ( r.codigo = c.procup and r.egreso = 'on')", en el query que trae los procedimientos quirurgicos realizados a un paciente, de tal manera que para aquellos procedimientos que se liquiden desde un paquete, se les valide que correspondan a un código cups valido que debe ser registrado, evitando así que codigos incluidos en el paquete que no correspondan a procedimientos, sean incluidos.
 * 2018-10-29 camilo zapata: agrupación en el subquery que consulta los procedimientos quirurgicos empaquetados.
 * 2018-10-23 camilo zapata: -se adicionan los procedimientos no quirurgicos ( cargados en cliame_000106 ) validados por cups.
 * -se valida el estado de la variable "egreso_automatico_urgencias" para que inhabilite inputs en caso de que el egreso se realice desde urgencias por los facturadores a partir del programa de cargos.
 * -se toman los diagnosticos a traves de la función consultarDiagnosticoPaciente(), la cual consulta en la tabla movhos_000243 los diagnosticos de la última evolución, en caso de que no haya ningun registro allí, se tomará el diagnóstico de ingreso.
 * 2018-09-11 Camilo zapata: restricción de codigos de procedimiento a 6 digitos o mas, ya que a estos corresponde la codificación cups
 * 2018-09-07 camilo zapata: modificación de mensajes de alerta para que no puedan darle click en "no volver a mostrar estos mensajes"
 * 2018-09-06 camilo zapata: se modifica el query de procedimientos para que incluya los procedimientos que están empaquetados,
 * tambien se permite que haya varios procedimientos principales en matrix
 * 2018-08-29 Jessica Madrid:    Se le agregan comillas a los filtros por historia e ingreso de los quieries que no las tenían.
 * 2018-06-26: camilo zapata: corrección en la función que busca los diagnósticos, para que no permita elegir diagnostícos inactivos(estado = off)
 * 2018-05-24: camilo zapata: se toma como fecha de egreso por defecto la que viene desde el programa de activos y egresados el cual tiene
 * encuenta la fecha y la hora del alta definitiva
 * 2018-05-17 ( camilo zz) se adiciona un control de verificación de grabado en la tabla inpac cuando se anula un egreso, en caso de que
 * este proceso no se realice correctamente se interrumpe la anulación de egreso y se notifica al usuario que debe intentarlo mas tarde o comunicarse con informatica.
 * 2017-09-10 ( Camilo zz) se valida que el egreso sea de un paciente que ya estè grabado en unix, en caso de que no sea asì, se notifica al usuario con posibilidad de llamar a informatica
 * 2017-06-20 ( Camilo zz) adición de diagnosticos automáticos, por medio de la consulta al  formulario hce epicrisis, adicional a eso se recibe el error de bloqueo en la tabla inpac para
 * evitar el egreso hasta que el registro esté desbloqueado.
 * 2017-06-12 ( Camilo zz) se cambia la pregunta de autorización de paciente a un parametro en la 51 llamado "preguntaPermisoEgreso"
 * 2016-12-14 ( Camilo zz) se anula el alta definitiva(movhos_000018) si se anula un ingreso, y para los servicios de ayudas se da el alta definitiva al realizar el egreso.
 * 2016-12-05 ( Camilo zz) modificación se modifica el script para que cargue automáticamente los datos del egreso en los servicios de ayuda, cuando el servicio es de ayuda diagnóstica.
 * 2016-06-02 ( Camilo zz) modificación que permite cargar los datos del último ingreso cuando este está anulado, para facilitar que este vuelva  activarse cuando sea necesario. buscar "activacionEgresoAnulado" de ser necesario.
 * 2016-05-31 ( Camilo zz) se corrige el software para que borre los datos en las actualizaciones puesto que la variable $data[error] no existía buscar 2016-05-31 de ser necesario   ";
 * 2016-05-20 ( Camilo zz) se modificó el software para inhabilite los botones mientras se termina la acción de guardado o anulación del egreso. y se cambiaron las llamadas ajax para que sean síncronas, buscar "#btnEgresar"
 * 2014-11-25 ( Camilo zz) se modificó el software para que valide las causas de egreso que incluyen límite de tiempo, con el tiempo de estancia del paciente
 * 2015-09-03 ( Camilo zz) - se modificó el programa para que guarde bn en unix el servicio de egreso
 * - trae información desde el programa de pacientes activos y egresados( cco de egreso y tipo de paciente ambulatorio - hospt)
 * - valida fecha de egreso <= a hoy solamente
 * - señala servicios faltantes en el detalle de diagnóstico, y especialidad.
 * - agrega servicios solo al dar click en ok
 * - Agrega automática servicios a especialistas( si seleccionan un servicio en el diagnótico, se agregará este al médico pertinente en la zona de especialidades )
 * 2015-09-10 ( Camilo zz) - se modifica el programa para que no agregue nunca el médico de ingreso. buscar la fecha( 2015-09-10 )
 * 2015-11-26 ( Camilo zz) - se modifica el software corrigiendo el comportamiento de tipo primario o secundario de los procedimientos cargados desde la liquidación de cirugia e historia clínica.
 * 2016-02-09 ( Camilo zz) - se modifica el software agregando el servicio de egreso automáticamente al diagnóstico principal. buscar si es necesario "$(contenedorServiciosOscultosDiagnosticoPpal).html"
 * ==================================================================================================================================================*/
header("Content-Type: text/html;charset=ISO-8859-1");

/****************************************************************************
 * acciones
 *****************************************************************************/
include_once("root/comun.php");
include_once("root/erp_unix_egreso.php");

if ($_SESSION['user']) {
    $user2 = explode("-", $user);
    (isset($user2[1])) ? $key = $user2[1] : $key = $user2[0];
    $hay_unix = consultarAplicacion($conex, $wemp_pmla, "conexionUnix");
    $graba_unix = consultarAplicacion($conex, $wemp_pmla, "grabarUnix");
    if ($hay_unix == "off" && $graba_unix == "on") {
        echo '  <br /><br /><br /><br />
                    <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                        [?] PROGRAMA NO DISPONIBLE...<br />Nos encontramos en una ventana de mantenimiento, por favor intente ingresar mas tarde, Disculpas por las molestias.
                    </div>';
        return;
    }


    //$key=substr($_SESSION['user'],2,strlen($_SESSION['user']));
} else {
    if (!isset($accion)) {
        echo "<script language='JavaScript'>
                    alerta(\"Ingresar a Matrix nuevamente. Se cerro la session.\");
            </script>";
    } else {
        $data = array('error' => 1, 'mensaje' => 'Ingresar a Matrix nuevamente. Se cerro la session', 'html' => '');
        echo json_encode($data);
    }
    exit;
}


//$key = substr( $user, 2 );

define("NOMBRE_BORRAR", '<IMG id="imgBorrar" SRC="../../images/medical/root/borrar.png" WIDTH=17 HEIGHT=17 title="Se eliminara esta fila." />');
define("NOMBRE_ADICIONAR", '<IMG id="imgAdicionar" SRC="../../images/medical/root/adicionar2.png" WIDTH=18 HEIGHT=18 title="Se adicionara una fila nueva." />');

if (isset($accion)) {
    global $wbasedato;
    global $conex;

    $data = array('error' => 0, 'html' => '', 'mensaje' => '', 'valor' => '', 'usu' => '', 'historia' => '', 'ingreso' => '', 'documento' => '', 'numRegistrosIng' => '', 'numRegistrosPac' => '');
    switch ($accion) {
        case 'consultarMedico':

            $json = consultarMedicos($q, $wbasedato, $aplicacion);
            echo $json;
            break;

        case 'consultarAnestesiologo':
            $wanestesia = consultarAliasPorAplicacion($conex, $wemp_pmla, "codigo_anestesiologos");
            $json = consultarMedicos($q, $wbasedato, $aplicacion, $wanestesia);
            echo $json;
            break;

        case 'consultarDiagnostico':

            $json = consultarDiagnosticos($q);
            echo $json;
            break;

        case 'consultarProcedimiento':

            $json = consultarProcedimientos($q, $wbasedato);
            echo $json;
            break;

        case 'consultarEspecialidad':
            $aplicacionHCE = consultarAplicacion($conex, $wemp_pmla, "hce");
            $json = consultarEspecialidades($q, $wbasedato, $aplicacion, $aplicacionHCE);
            echo $json;
            break;

        case 'consultarServicio':

            $json = consultarServicios($q, $wbasedato, $aplicacion);
            echo $json;
            break;

        case 'guardarDatos':

            global $ing_histxtNumHis;
            global $ing_cemhidCodAse;
            global $cco_egreso;

            $guardoEgresoUnix = false;
            $medicoEgreso = "";
            $saveUnix = consultarAliasPorAplicacion($conex, $wemp_pmla, 'conexionUnix');

            /** Código de la especialidad del médico que tiene el diagnostico principal **/
            $codEspMedDiagPrincipal = null;
            /** Código de la especialidad del médico tratante **/
            $codEspMedTratante = null;

            //CARGAR LOS DATOS QUE SE DEBEN ENVIAR A UNIX Y ALGUNOS QUE SE TRAEN DEL INGRESO
            //_ux_dxegr -> El diagnostico ppal es ON
            foreach ($_POST['diagnosticosux'] as $keDiaqq => &$valueDiaqq) {
                foreach ($valueDiaqq as $keDiaq => &$valueDiaq) {
                    $clave_sub = substr($keDiaq, 0, 7);
                    if ($clave_sub == "dia_tip" && $valueDiaq == "P") {
                        $valueDiaq['_ux_dxegr'] = "on";
                    } else if ($clave_sub == "dia_tip" && $valueDiaq != "P") {
                        $valueDiaq['_ux_dxegr'] = "off";
                    }
                }
            }
            //_ux_egrmei y egrmei->Es el medico principal, el primer medico de los especialistas
            //_ux_egrmed -> Medico de egreso=$medicoEgreso
            foreach ($especialidades as $keDiadd => &$valueDiadd) {
                unset($datosEnc); //se borra el array
                $datosEnc = crearArrayDatos($wbasedato, "esp", "esp_", 3, $valueDiadd);

                if (isset($valueDiadd['med_egrradio'])) {
                    if ($valueDiadd['med_egrradio'] == 'on') {
                        $medicoEgreso = $datosEnc['espmed'];
                        $_POST['_ux_egrmed'] = $datosEnc['espmed'];
                        $_POST['egr_mee'] = $datosEnc['espmed'];
                    }
                }
                //2014-07-30 para el medico tratante
                if (isset($valueDiadd['med_traradio'])) {
                    if ($valueDiadd['med_traradio'] == 'on') {
                        $codEspMedTratante = $datosEnc['espcod'];
                        $medicoTratante = $datosEnc['espmed'];
                        $_POST['_ux_infmed'] = $datosEnc['espmed'];
                        $_POST['egr_met'] = $datosEnc['espmed'];
                    }
                }
                //2014-09-15 para el medico de ingreso
                if (isset($valueDiadd['med_meiradio'])) {
                    if ($valueDiadd['med_meiradio'] == 'on') {
                        $medicoDeIngreso = $datosEnc['espmed'];
                        $_POST['_ux_egrmei'] = $datosEnc['espmed'];
                        $_POST['egr_mei'] = $datosEnc['espmed'];
                    }
                }
            }

            foreach ($diagnosticos as $keDiass => &$valueDiass) {
                unset($datosEnc); //se borra el array
                $datosEnc = crearArrayDatos($wbasedato, "dia", "dia_", 3, $valueDiass);

                /** Si es el diagnostico principal**/
                if ($datosEnc['diatip'] == "P") {

                    /** Si es el diagnostico principal verificamos si el código es válido**/
                    if (!validarCodigoDiagnosticoPrincipal($datosEnc['diacod'])) {
                        $data['error'] = 1;
                        $data['mensaje'] = 'Codigo no valido para el diagnostico principal';
                        echo json_encode($data);
                        return;
                    }

                    /** Asignamos el valor de la especialidad del médico del diagnostico principal **/
                    $codEspMedDiagPrincipal = $datosEnc['diaesm'];

                    if ($datosEnc['dianue'] == "S" || $datosEnc['dianue'] == "on") {
                        $_POST['egr_tdp'] = "2";
                    } else {
                        $_POST['egr_tdp'] = "3";
                    }
                    if ($datosEnc['diacom'] == "S" || $datosEnc['diacom'] == "on") {
                        $_POST['egr_com'] = "on";
                    } else {
                        $_POST['egr_com'] = "off";
                    }
                }
            }

            /** Verificamos si la especialidad del médico que da el diagnóstico principal es la misma del médico tratante **/
            if ($codEspMedDiagPrincipal != $codEspMedTratante) {
                $data['error'] = 1;
                $data['mensaje'] = 'Debe seleccionar un medico tratante con la misma especialidad del médico del diagnóstico principal';
                echo json_encode($data);
                return;
            }

            /** Validar campos de los procedimientos **/
            $respuestaValidarProc = validarProcedimientosFormulario($_POST['procedimientos']);

            if (!$respuestaValidarProc['respuesta']) {
                $data['error'] = 1;
                $data['mensaje'] = $respuestaValidarProc['mensaje'];

                echo json_encode($data);
                return;
            }

            /** Validar códigos cups activos en la***/

            //_ux_egrdin_ux_hosdxi, egr_dxitxtDiaIng -> DIAGNOSTICO DE INGRESO
            //egr_cex, _ux_hoscex -> CAUSA DE INGRESO
            $sql1 = "select Ingcai,Ingdig, Ingunx
                                        from " . $wbasedato . "_000101
                                        where  Inghis='" . $historia . "'
                                          and Ingnin='" . $ingreso . "' ";


            $res1 = mysql_query($sql1, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
            $num1 = mysql_num_rows($res1);

            if ($num1 > 0) {
                while ($rows = mysql_fetch_array($res1)) {
                    $_POST['_ux_egrdin_ux_hosdxi'] = $rows['Ingdig'];
                    $_POST['egr_dxi'] = $rows['Ingdig'];
                    $_POST['_ux_hoscex'] = $rows['Ingcai'];
                    //$_POST['egr_cex'] = $rows['Ingcai'];
                    $grabadoUnix = $rows['Ingunx'];

                }
            }


            //FIN DE CARGAR LOS DATOS QUE SE DEBEN ENVIAR A UNIX Y ALGUNOS QUE SE TRAEN DEL INGRESO
            /* se verifica si ya està grabado en unix*/
            if ($saveUnix == 'on' && $grabadoUnix == "off") {
                $data['error'] = 1;
                $data['mensaje'] = " Error en unix - tabla inpac, El egreso no puede ser realizado, intentelo en unos minutos o comuniquese con informatica ";
                echo json_encode($data);
                break;
            }


            //se consulta si existe esa aplicacion
            $alias = "movhos";
            $aplicacion = consultarAplicacion($conex, $wemp_pmla, $alias);

            $alias1 = "hce";
            $aplicacionHce = consultarAplicacion($conex, $wemp_pmla, $alias1);

            /***se guardan o se actualizan los datos***/
            if (!empty($historia) && !empty($ingreso)) {
                $tieneConexionUnix = consultarAliasPorAplicacion($conex, $wemp_pmla, 'conexionUnix');
                //$tieneConexionUnix  = "off";
                $ping_unix = ping_unix();


                if ($saveUnix == 'on') {
                    if ($hay_unix && $tieneConexionUnix == 'on' && $ping_unix) //se descomento
                    {
                        $a = new egreso_erp();
                        if ($a->conex_u) {
                            $a->realizarEgreso($historia, $ingreso);
                            //echo json_encode( $a->data );
                            if ($a->data['error'] == 1) //si hay errores guardando en unix
                            {
                                $data['mensaje'] = "Error al grabar en UNIX " . $data['mensaje'];
                                echo json_encode($a->data);
                                exit;
                                return;
                            }
                            if ($a->data['error'] == 2) //si hay errores guardando en unix
                            {
                                $data['mensaje'] = " El paciente esta siendo modificado en unix, por lo tanto no se puede realizar el egreso en este momento ";
                                echo json_encode($a->data);
                                exit;
                                return;
                            }
                            $guardoEgresoUnix = true;
                        }
                    }
                }

                //Consulto si existe el registo
                $sql = "select Egrhis,Egring,id,Egract
                            from " . $wbasedato . "_000108
                            where Egrhis = '" . utf8_decode($historia) . "'
                            and Egring = '" . utf8_decode($ingreso) . "'";

                $res = mysql_query($sql, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error consultando la tabla de egresos - " . mysql_error()));

                if ($res) {
                    $num = mysql_num_rows($res);

                    //Si no se encontraron los datos, significa que es un registro nuevo
                    if ($num == 0) //hace el insert
                    {
                        //insert en la tabla 108
                        $datosEnc = crearArrayDatos($wbasedato, "egr", "egr_", 3);
                        $datosEnc["Egract"] = 'on';
                        $datosEnc["Egrunx"] = 'off';

                        if ($guardoEgresoUnix == true)
                            $datosEnc["Egrunx"] = 'on';

                        $sqlInsert = crearStringInsert($wbasedato . "_000108", $datosEnc);

                        $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de egresos - " . mysql_error()));

                        //si inserto la 108
                        if ($resEnc) {
                            /**Diagnosticos**/
                            if ($data['error'] == 0) {
                                //para pasar de diagnostico en diagnostico
                                foreach ($diagnosticos as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los diagnosticos
                                    $datosEnc = crearArrayDatos($wbasedato, "dia", "dia_", 3, $valueDia);
                                    $datosEnc["diahis"] = $historia; //histiria
                                    $datosEnc["diaing"] = $ingreso; //ingreso
                                    //El diagnostico de egreso es el principal
                                    if ($datosEnc["diatip"] == "P") {
                                        $datosEnc["diaegr"] = "on";
                                    } else {
                                        $datosEnc["diaegr"] = "off";
                                    }
                                    $sqlInsert = crearStringInsert($wbasedato . "_000109", $datosEnc);

                                    $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de diagnosticos - " . mysql_error()));

                                    if (!$resEnc) {
                                        $data['error'] = 1;
                                    }
                                    if (isset($servDianosticos[$keDia])) {

                                        foreach ($servDianosticos[$keDia] as $keSer => $valueSerDia) {
                                            unset($datosEncSer); //se borra el array

                                            //se guardan los Servicios por Diagnostico
                                            $datosEncSer = crearArrayDatos($wbasedato, "sed", "sed_", 3, $valueSerDia);
                                            $datosEncSer["Sedhis"] = $historia; //histiria
                                            $datosEncSer["Seding"] = $ingreso; //ingreso
                                            //El diagnostico de egreso es el principal
                                            $datosEncSer["Seddia"] = $datosEnc['diacod'];
                                            $datosEncSer["Sedest"] = "on";
                                            $sqlInsert = crearStringInsert($wbasedato . "_000238", $datosEncSer);

                                            $resEncSer = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de servicios por diagnostico - " . mysql_error()));

                                            if (!$resEncSer) {
                                                $data['error'] = 1;
                                            }
                                        }
                                    }
                                } //foreach
                            }
                            /**Fin Diagnosticos**/

                            /**Procedimientos**/
                            if ($data['error'] == 0) {
                                //para pasar de procedimiento en procedimiento
                                foreach ($procedimientos as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los procedimientos
                                    $datosEnc = crearArrayDatos($wbasedato, "pro", "pro_", 3, $valueDia);
                                    $datosEnc["prohis"] = $historia; //histiria
                                    $datosEnc["proing"] = $ingreso; //ingreso

                                    if ($datosEnc["procod"] != "") {
                                        $sqlInsert = crearStringInsert($wbasedato . "_000110", $datosEnc);

                                        $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de procedimientos - " . mysql_error()));

                                        if (!$resEnc) {
                                            $data['error'] = 1;
                                        }
                                    }
                                } //foreach
                            }
                            /**Fin Procedimientos**/

                            /**Especialidades**/
                            if ($data['error'] == 0) {
                                //para pasar de especialidad en especialidad
                                foreach ($especialidades as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todas las especialidades

                                    $datosEnc = crearArrayDatos($wbasedato, "esp", "esp_", 3, $valueDia);

                                    $datosEnc["esphis"] = $historia; //histiria
                                    $datosEnc["esping"] = $ingreso; //ingreso
                                    //unset( $datosEnc[ "espegr" ] ); //este campo no existe en la base de datos, se usa para detectar el medico de egreso que viaja a UNIX
                                    $sqlInsert = crearStringInsert($wbasedato . "_000111", $datosEnc);

                                    $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de especialidades - " . mysql_error()));

                                    if (!$resEnc) {
                                        $data['error'] = 1;
                                    }
                                    if (isset($servEspecialidad[$keDia])) {
                                        foreach ($servEspecialidad[$keDia] as $keSer => $valueSeresp) {
                                            unset($datosEncSer); //se borra el array

                                            //se guardan los Servicios por Diagnostico
                                            $datosEncSer = crearArrayDatos($wbasedato, "see", "see_", 3, $valueSeresp);
                                            $datosEncSer["Seehis"] = $historia; //histiria
                                            $datosEncSer["Seeing"] = $ingreso; //ingreso
                                            //El diagnostico de egreso es el principal
                                            $datosEncSer["Seeesp"] = $datosEnc['espcod'];
                                            $datosEncSer["Seemed"] = $datosEnc['espmed'];
                                            $datosEncSer["Seeest"] = "on";
                                            $sqlInsert = crearStringInsert($wbasedato . "_000239", $datosEncSer);

                                            $resEncSer = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de servicios por especialidad - " . mysql_error()));

                                            if (!$resEncSer) {
                                                $data['error'] = 1;
                                            }
                                        }

                                    }
                                } //foreach
                            }
                            /**Fin Especialidades**/


                            /**Servicios**/
                            if ($data['error'] == 0) {
                                //para pasar de servicio en servicio

                                /*$data['mensaje']= $servicios;
                                    $data['error']=1;
                                    echo json_encode( $data );
                                    return;*/
                                foreach ($servicios as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los servicios
                                    //unset();
                                    $datosEnc = crearArrayDatos($wbasedato, "ser", "ser_", 3, $valueDia);
                                    $datosEnc["serhis"] = $historia; //historia
                                    $datosEnc["sering"] = $ingreso; //ingreso

                                    $sqlInsert = crearStringInsert($wbasedato . "_000112", $datosEnc);
                                    /*$data['mensaje']= " por 2 -->   ".$sqlInsert;
                                        $data['error']=1;
                                        echo json_encode( $data );
                                        return;*/

                                    $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de servicios - " . mysql_error()));

                                    if (!$resEnc) {
                                        $data['error'] = 1;
                                    }
                                } //foreach
                            }
                            /**Fin Servicios**/

                            /**AUTORIZACIONES**/
                            $datosEnc = crearArrayDatos($wbasedato, "aut", "aut_", 3);
                            $datosEnc["authis"] = $historia;
                            $datosEnc["auting"] = $ingreso;
                            $datosEnc["autest"] = 'on';
                            $sqlInsert = crearStringInsert($wbasedato . "_000219", $datosEnc);
                            $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de autorizaciones - " . mysql_error()));
                            if (!$resEnc) {
                                $data['error'] = 1;
                            }

                            /**PERSONAS AUTORIZADAS**/
                            if ($data['error'] == 0) {
                                foreach ($personasautorizadas as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los servicios
                                    $datosEnc = crearArrayDatos($wbasedato, "dau", "dau_", 3, $valueDia);
                                    $datosEnc["dauhis"] = $historia; //historia
                                    $datosEnc["dauing"] = $ingreso; //ingreso

                                    if ($datosEnc["daudoc"] != "") {
                                        $sqlInsert = crearStringInsert($wbasedato . "_000220", $datosEnc);
                                        $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de personas autorizadas - " . mysql_error()));
                                        if (!$resEnc) {
                                            $data['error'] = 1;
                                        }
                                    }
                                    //echo "<br>QUERY: ".$sqlInsert;
                                }
                            }
                            /**PERSONAS QUE RECLAMAN**/
                            if ($data['error'] == 0) {
                                foreach ($personasreclaman as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los servicios
                                    $datosEnc = crearArrayDatos($wbasedato, "dau", "dau_", 3, $valueDia);
                                    $datosEnc["dauhis"] = $historia; //historia
                                    $datosEnc["dauing"] = $ingreso; //ingreso

                                    if ($datosEnc["daudoc"] != "") {
                                        $sqlInsert = crearStringInsert($wbasedato . "_000220", $datosEnc);
                                        $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de personas que reclaman - " . mysql_error()));
                                        if (!$resEnc) {
                                            $data['error'] = 1;
                                        }
                                    }
                                    //echo "<br>QUERY: ".$sqlInsert;
                                }
                            }

                            if ($data['error'] == 0) {
                                $data['mensaje'] = "Se guardo el egreso correctamente";
                            }
                        } else //si no inserto en la 108
                        {
                            $data["error"] = 1;
                        }

                        if ($data['error'] == 0) {
                            logEgreso('Egreso guardado', $historia, $ingreso, "");
                        }
                    } //if num = 0 de la primera consulta fin insert
                    else //hace la actualizacion
                    {
                        $data["error"] = 0;//2016-05-31 se agrega esta linea para que si borre los datos anteriores en las actualizaciones
                        $rowsEnc = mysql_fetch_array($res);

                        //Si se encontraron datos, significa que es una actualización
                        $datosTabla = crearArrayDatos($wbasedato, "egr", "egr_", 3);

                        $datosTabla['id'] = $rowsEnc['id'];
                        $datosTabla['Egract'] = 'on';
                        // $datosTabla[ "ingtar" ] = $tarifa; //tarifa
                        // var_dump($datosTabla);
                        $sqlUpdate = crearStringUpdate($wbasedato . "_000108", $datosTabla);

                        $res1 = mysql_query($sqlUpdate, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlUpdate - " . mysql_error()));

                        if ($res1) {
                            // if( mysql_affected_rows() > 0 ){
                            // $data[ "mensaje" ] = utf8_encode( "Se actualizo correctamente" );
                            // }
                        } else {
                            $data["error"] = 1;
                            $data["mensaje"] = utf8_encode(mysql_errno() . " - Error en el query $sqlUpdate - " . mysql_error());
                        }

                        /*Se hace la actualizacion de diagnosticos, procedimientos, especialidades y servicios
                                  para no recorrerlos se insertan nuevamente*/

                        /**Diagnosticos**/
                        if ($data['error'] == 0) {
                            /*se borran los registros para volver a insertarlos*/
                            $sqlDel = "delete from " . $wbasedato . "_000109
                                             where Diahis = '" . $historia . "'
                                             and Diaing = '" . $ingreso . "'";
                            $resDel = mysql_query($sqlDel, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlDel - " . mysql_error()));

                            $sqlDelser = "delete from " . $wbasedato . "_000238
                                             where Sedhis = '" . $historia . "'
                                             and Seding = '" . $ingreso . "'";
                            $resDelser = mysql_query($sqlDelser, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlDelser - " . mysql_error()));
                            if (!$resDel) {
                                $data['error'] = 1;
                            } else {
                                /*fin borrado de registros*/

                                //para pasar de diagnostico en diagnostico
                                foreach ($diagnosticos as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los diagnosticos
                                    $datosEnc = crearArrayDatos($wbasedato, "dia", "dia_", 3, $valueDia);
                                    $datosEnc["diahis"] = $historia; //histiria
                                    $datosEnc["diaing"] = $ingreso; //ingreso

                                    $sqlInsert = crearStringInsert($wbasedato . "_000109", $datosEnc);

                                    $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de diagnosticos - " . mysql_error()));

                                    if (!$resEnc) {
                                        $data['error'] = 1;
                                    }
                                    if (isset($servDianosticos[$keDia])) {
                                        foreach ($servDianosticos[$keDia] as $keSer => $valueSerDia) {
                                            unset($datosEncSer); //se borra el array

                                            //se guardan los Servicios por Diagnostico
                                            $datosEncSer = crearArrayDatos($wbasedato, "sed", "sed_", 3, $valueSerDia);
                                            $datosEncSer["Sedhis"] = $historia; //histiria
                                            $datosEncSer["Seding"] = $ingreso; //ingreso
                                            //El diagnostico de egreso es el principal
                                            $datosEncSer["Seddia"] = $datosEnc['diacod'];
                                            $datosEncSer["Sedest"] = "on";
                                            $sqlInsert = crearStringInsert($wbasedato . "_000238", $datosEncSer);

                                            $resEncSer = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de servicios por diagnostico - " . mysql_error()));

                                            if (!$resEncSer) {
                                                $data['error'] = 1;
                                            }
                                        }

                                    }
                                } //foreach
                            }
                        }
                        /**Fin Diagnosticos**/

                        /**Procedimientos**/
                        if ($data['error'] == 0) {

                            /*se borran los registros para volver a insertarlos*/
                            $sqlDel = "delete from " . $wbasedato . "_000110
                                             where Prohis = '" . $historia . "'
                                             and Proing = '" . $ingreso . "'";
                            $resDel = mysql_query($sqlDel, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlDel - " . mysql_error()));
                            if (!$resDel) {
                                $data['error'] = 1;
                            } else {
                                /*fin borrado de registros*/

                                //para pasar de procedimiento en procedimiento
                                foreach ($procedimientos as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los procedimientos
                                    $datosEnc = crearArrayDatos($wbasedato, "pro", "pro_", 3, $valueDia);
                                    $datosEnc["prohis"] = $historia; //histiria
                                    $datosEnc["proing"] = $ingreso; //ingreso

                                    if ($datosEnc["procod"] != "") {

                                        $sqlInsert = crearStringInsert($wbasedato . "_000110", $datosEnc);

                                        $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de procedimientos - " . mysql_error()));

                                        if (!$resEnc) {
                                            $data['error'] = 1;
                                        }
                                    }
                                } //foreach
                            }
                        }
                        /**Fin Procedimientos**/


                        /**Especialidades**/
                        if ($data['error'] == 0) {
                            /*se borran los registros para volver a insertarlos*/
                            $sqlDel = "delete from " . $wbasedato . "_000111
                                             where Esphis = '" . $historia . "'
                                             and Esping = '" . $ingreso . "'";
                            $resDel = mysql_query($sqlDel, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlDel - " . mysql_error()));
                            $sqlDel = "delete from " . $wbasedato . "_000239
                                             where Seehis = '" . $historia . "'
                                             and Seeing = '" . $ingreso . "'";
                            $resDel = mysql_query($sqlDel, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlDel - " . mysql_error()));
                            if (!$resDel) {
                                $data['error'] = 1;
                            } else {
                                /*fin borrado de registros*/

                                //para pasar de especialidad en especialidad
                                foreach ($especialidades as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todas las especialidades

                                    $datosEnc = crearArrayDatos($wbasedato, "esp", "esp_", 3, $valueDia);

                                    $datosEnc["esphis"] = $historia; //histiria
                                    $datosEnc["esping"] = $ingreso; //ingreso

                                    $sqlInsert = crearStringInsert($wbasedato . "_000111", $datosEnc);

                                    $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de especialidades - " . mysql_error()));

                                    if (!$resEnc) {
                                        $data['error'] = 1;
                                    }
                                    if (isset($servEspecialidad[$keDia])) {
                                        foreach ($servEspecialidad[$keDia] as $keSer => $valueSeresp) {
                                            unset($datosEncSer); //se borra el array

                                            //se guardan los Servicios por Diagnostico
                                            $datosEncSer = crearArrayDatos($wbasedato, "see", "see_", 3, $valueSeresp);
                                            $datosEncSer["Seehis"] = $historia; //histiria
                                            $datosEncSer["Seeing"] = $ingreso; //ingreso
                                            //El diagnostico de egreso es el principal
                                            $datosEncSer["Seeesp"] = $datosEnc['espcod'];
                                            $datosEncSer["Seemed"] = $datosEnc['espmed'];
                                            $datosEncSer["Seeest"] = "on";
                                            $sqlInsert = crearStringInsert($wbasedato . "_000239", $datosEncSer);

                                            $resEncSer = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de servicios por especialidad - " . mysql_error()));

                                            if (!$resEncSer) {
                                                $data['error'] = 1;
                                            }
                                        }
                                    }
                                } //foreach
                            }
                        }
                        /**Fin Especialidades**/

                        /**Servicios**/
                        if ($data['error'] == 0) {
                            /*se borran los registros para volver a insertarlos*/
                            $sqlDel = "delete from " . $wbasedato . "_000112
                                             where Serhis = '" . $historia . "'
                                             and Sering = '" . $ingreso . "'";
                            $resDel = mysql_query($sqlDel, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlDel - " . mysql_error()));
                            if (!$resDel) {
                                $data['error'] = 1;
                            } else {
                                /*fin borrado de registros*/

                                //para pasar de servicio en servicio
                                foreach ($servicios as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los servicios
                                    $datosEnc = crearArrayDatos($wbasedato, "ser", "ser_", 3, $valueDia);
                                    $datosEnc["serhis"] = $historia; //historia
                                    $datosEnc["sering"] = $ingreso; //ingreso

                                    $sqlInsert = crearStringInsert($wbasedato . "_000112", $datosEnc);
                                    /*$data['mensaje']= "por 1  --->  ".$sqlInsert;
                                            $data['error']=1;
                                            echo json_encode( $data );
                                            return;*/
                                    $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de servicios - " . mysql_error()));

                                    if (!$resEnc) {
                                        $data['error'] = 1;
                                    }
                                } //foreach
                            }
                        }
                        /**Fin Servicios**/

                        /**AUTORIZACIONES**/
                        $sqlDel = "delete from " . $wbasedato . "_000219
                                             where authis = '" . $historia . "'
                                             and auting = '" . $ingreso . "'";
                        $resDel = mysql_query($sqlDel, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlDel - " . mysql_error()));
                        if (!$resDel) {
                            $data['error'] = 1;
                        } else {
                            unset($datosEnc);
                            $datosEnc = crearArrayDatos($wbasedato, "aut", "aut_", 3);
                            $datosEnc["authis"] = $historia;
                            $datosEnc["auting"] = $ingreso;
                            $datosEnc["autest"] = 'on';
                            $sqlInsert = crearStringInsert($wbasedato . "_000219", $datosEnc);
                            $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de autorizaciones - " . mysql_error()));
                            if (!$resEnc) {
                                $data['error'] = 1;
                            }
                        }

                        /**PERSONAS AUTORIZADAS**/
                        if ($data['error'] == 0) {

                            /*se borran los registros para volver a insertarlos*/
                            $sqlDel = "delete from " . $wbasedato . "_000220
                                             where dauhis = '" . $historia . "'
                                             and dauing = '" . $ingreso . "'";
                            $resDel = mysql_query($sqlDel, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlDel - " . mysql_error()));
                            if (!$resDel) {
                                $data['error'] = 1;
                            } else {
                                foreach ($personasautorizadas as $keDia => $valueDia) {
                                    unset($datosEnc); //se borra el array

                                    //se guardan todos los servicios
                                    $datosEnc = crearArrayDatos($wbasedato, "dau", "dau_", 3, $valueDia);
                                    $datosEnc["dauhis"] = $historia; //historia
                                    $datosEnc["dauing"] = $ingreso; //ingreso

                                    $sqlInsert = crearStringInsert($wbasedato . "_000220", $datosEnc);
                                    $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de personas autorizadas - " . mysql_error()));
                                    if (!$resEnc) {
                                        $data['error'] = 1;
                                    }
                                    //echo "<br>QUERY: ".$sqlInsert;
                                }
                            }
                        }
                        /**PERSONAS QUE RECLAMAN**/
                        if ($data['error'] == 0) {
                            foreach ($personasreclaman as $keDia => $valueDia) {
                                unset($datosEnc); //se borra el array

                                //se guardan todos los servicios
                                $datosEnc = crearArrayDatos($wbasedato, "dau", "dau_", 3, $valueDia);
                                $datosEnc["dauhis"] = $historia; //historia
                                $datosEnc["dauing"] = $ingreso; //ingreso

                                $sqlInsert = crearStringInsert($wbasedato . "_000220", $datosEnc);
                                $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de personas que reclaman - " . mysql_error()));
                                if (!$resEnc) {
                                    $data['error'] = 1;
                                }
                                //echo "<br>QUERY: ".$sqlInsert;
                            }
                        }

                        if ($data['error'] == 0) {
                            logEgreso('Egreso actualizado', $historia, $ingreso, "");
                        }

                        if ($data['error'] == 0) {
                            if ($rowsEnc['Egract'] == 'on') {
                                $data['mensaje'] = 'Egreso actualizado correctamente';
                            } else {
                                $data['mensaje'] = 'Se guardo el egreso correctamente';
                            }
                        }

                    }//fin actualizacion


                    /*Se hace la parte de poner en estado off esa historia en la 100*/
                    if (!empty($historia) && $data['error'] == 0) {
                        $sqlUpdate = "UPDATE " . $wbasedato . "_000100
                                    SET  Pacact = 'off',
                                         Pacno1 = '" . utf8_decode($_POST['pac_no1txtPriNom']) . "',
                                         Pacno2 = '" . utf8_decode($_POST['pac_no2txtSegNom']) . "',
                                         Pacap1 = '" . utf8_decode($_POST['pac_ap1txtPriApe']) . "',
                                         Pacap2 = '" . utf8_decode($_POST['pac_ap2txtSegApe']) . "'
                                    WHERE Pachis='" . $historia . "' ";
                        $resUpdate = mysql_query($sqlUpdate, $conex) or ($data['mensaje'] = utf8_encode("Error actualizando " . $wbasedato . "_000100 " . mysql_errno() . " - Error en el query $sqlUpdate - " . mysql_error()));

                        if (!$resUpdate) {
                            //  $data['error'] = 1;
                        }

                        $sqlUpdate = "UPDATE root_000036, root_000037
                                    SET  Pacno1 = '" . utf8_decode($_POST['pac_no1txtPriNom']) . "',
                                         Pacno2 = '" . utf8_decode($_POST['pac_no2txtSegNom']) . "',
                                         Pacap1 = '" . utf8_decode($_POST['pac_ap1txtPriApe']) . "',
                                         Pacap2 = '" . utf8_decode($_POST['pac_ap2txtSegApe']) . "'
                                    WHERE Orihis = '" . $historia . "'
                                      AND Oriori = '" . $wemp_pmla . "'
                                      AND Oritid = Pactid
                                      AND Oriced = Pacced";
                        $resUpdate = mysql_query($sqlUpdate, $conex) or ($data['mensaje'] = utf8_encode("Error actualizando " . $wbasedato . "_000100 " . mysql_errno() . " - Error en el query $sqlUpdate - " . mysql_error()));
                        if (mysql_affected_rows() > 0) {
                            logEgreso('Nombre cambiado', $historia, $ingreso, "");
                        }
                    }

                    $ccoAyuda = consultarCcoAyuda($cco_egreso);

                    //if( $ccoAyuda ){//--> si el servicio de egreso es ayuda diagnóstica.
                    $wmovhos = consultarAplicacion($conex, $wemp_pmla, "movhos");

                    $sqlFad = " SELECT Ubifad
                                           FROM {$wmovhos}_000018
                                          WHERE ubihis = '{$historia}'
                                            AND ubiing = '{$ingreso}'
                                            AND ubiald = 'off' ";

                    $rsfad = mysql_query($sqlFad, $conex);
                    $rowfad = mysql_fetch_assoc($rsfad);
                    $hoy = date("Y-m-d");
                    $hora = date("H:i:s");
                    $actFad = ($rowfad['Ubifad'] == "0000-00-00") ? ", Ubifad = '{$hoy}', ubihad = '{$hora}' " : "";
                    $sql = "UPDATE {$wmovhos}_000018
                                        SET ubiald = 'on' {$actFad}
                                      WHERE ubihis = '{$historia}'
                                        AND ubiing = '{$ingreso}'
                                        AND ubiald = 'off'";

                    $resAld = mysql_query($sql, $conex) or ($data['mensaje'] = mysql_errno() . " - Error en el query $sql - " . mysql_error());

                    if (!$resAld) {
                        $data['mensaje'] = 1;
                    }
                    //}

                    /*Fin de la parte de poner en estado off esa historia en la 100*/
                } else //no se ejecuto la consulta de la 108
                {
                    $data["error"] = 1;
                }
                /**fin ingreso**/

            } else //no se ejecuto la consulta de la 108
            {
                $data["error"] = 1;
                $data["mensaje"] = "La historia y el ingreso esta vacios por favor verificar";
            }

            echo json_encode($data);
            break;
        /****/

        case 'mostrarDatosAlmacenados':
            /*****mostrar datos almacenados antes del egreso******/

            //se consulta si existe esa aplicacion
            $alias = "movhos";
            $aplicacion = consultarAplicacion($conex, $wemp_pmla, $alias);
            $alias1 = "hce";
            $aplicacionHce = consultarAplicacion($conex, $wemp_pmla, $alias1);
            $wbasedato_tcx = consultarAplicacion($conex, $wemp_pmla, "tcx");
            $aplicacionHce = consultarAplicacion($conex, $wemp_pmla, $alias1);
            $ccoIngresoCir = consultarAplicacion($conex, $wemp_pmla, "ccoIngresoCirugia");
            $ccosProcsCirugia = array();
            $ccoAyuda = consultarCcoAyuda($cco_egreso);
            $egresoUrgencias = consultarCcoUrgencias($cco_egreso);

            if (!empty($historia) || !empty($documento) || !empty($priApe) || !empty($segApe) || !empty($priNom) || !empty($segNom)) {
                /***se consulta si la persona ha venido antes en la tabla 100***/
                $sql = "select Pachis,Pactdo,Pacdoc,Pacap1,Pacap2,Pacno1,Pacno2,Pacact,Pacsex, Pacfna, a.Fecha_data
                            from " . $wbasedato . "_000100 a
                            where
                            ";
                if (!empty($historia)) {
                    $sql .= " Pachis = '" . $historia . "' ";
                } else {
                    $sql .= " Pachis != '0' ";
                }
                if (!empty($documento)) {
                    $sql .= " and Pacdoc = '" . $documento . "'  ";
                }
                if (!empty($priApe)) {
                    $sql .= " and Pacap1 like '" . $priApe . "'  ";
                }
                if (!empty($segApe)) {
                    $sql .= " and Pacap2 like '" . $segApe . "'  ";
                }
                if (!empty($priNom)) {
                    $sql .= " and Pacno1 like '" . $priNom . "'  ";
                }
                if (!empty($segNom)) {
                    $sql .= " and Pacno2 like '" . $segNom . "'  ";
                }
                //$sql .=" Group by  Pachis  ";
                $sql .= " Order by  Pacdoc  ";

                $res = mysql_query($sql, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000100 " . mysql_errno() . " - Error en el query $sql - " . mysql_error()));
                if ($res) {
                    $num = mysql_num_rows($res);

                    if ($num == 0) { //Buscar el paciente en root_000036 y 37
                        $sql = "select Orihis as Pachis,Pactid as Pactdo,Pacced as Pacdoc,Pacap1,Pacap2,Pacno1,Pacno2,'' as Pacact,Pacsex, Pacnac as Pacfna, a.Fecha_data
                                    from root_000036 a, root_000037
                                    where Pactid = Oritid
                                      and Pacced = Oriced
                                    ";
                        if (!empty($historia)) {
                            $sql .= " and Orihis = '" . $historia . "' ";
                        }
                        if (!empty($documento)) {
                            $sql .= " and Pacced = '" . $documento . "'  ";
                        }
                        if (!empty($priApe)) {
                            $sql .= " and Pacap1 like '" . $priApe . "'  ";
                        }
                        if (!empty($segApe)) {
                            $sql .= " and Pacap2 like '" . $segApe . "'  ";
                        }
                        if (!empty($priNom)) {
                            $sql .= " and Pacno1 like '" . $priNom . "'  ";
                        }
                        if (!empty($segNom)) {
                            $sql .= " and Pacno2 like '" . $segNom . "'  ";
                        }

                        $sql .= " Order by  Pacced  ";
                        $res = mysql_query($sql, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla root 000036 " . mysql_errno() . " - Error en el query $sql - " . mysql_error()));
                        if ($res) {
                            $num = mysql_num_rows($res);
                        }
                    }
                    $data['numRegistrosPac'] = $num;
                    if ($num > 0) {
                        /*se inicializa la i en el for de la consulta de la 100 pero se incrementa en el for de la
                            consulta de la 101
                            */
                        for ($i = 0, $j = 0; $rows = mysql_fetch_array($res, MYSQL_ASSOC); $j++) { //solo se puede buscar por el nombre del campo

                            //echo "DE 100: ".json_encode($rows);
                            //posicion de historia
                            $data['numPosicionHistorias'][$rows['Pachis']] = $j;

                            foreach ($rows as $key => $value) {
                                //se guarda en data con el prefijo pac_ y empezando en la posicion 3 hasta el final
                                $data['infopac']["pac_" . substr($key, 3)] = utf8_encode($value);
                            }

                            /***busqueda del paciente en la tabla de ingreso 101***/

                            $sql1 = "select Inghis,Ingnin,Ingfei,Inghin,Ingcai,Ingusu,Ingdig,Ingcem,Ingmei, a.Fecha_data, Ingdig
                                        from " . $wbasedato . "_000101 a INNER JOIN " . $wbasedato . "_000100 ON (Pachis=Inghis)
                                        where ";
                            if (!empty($rows['Pachis'])) {
                                $sql1 .= "Inghis='" . $rows['Pachis'] . "' ";
                            } else {
                                $sql1 .= " Inghis != '0'";
                            }
                            if (!empty($ingreso)) {
                                $sql1 .= "and Ingnin='" . $ingreso . "' ";
                            }
                            $sql1 .= " and Ingcem != '' ";

                            $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000101 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                            if ($res1) {
                                $num1 = mysql_num_rows($res1);

                                if ($num1 == 0 && $aplicacion != "") {
                                    $sql1 = "select Inghis,Inging as Ingnin,Fecha_data as Ingfei,Hora_data as Inghin, '' as Ingcai, substr(Seguridad,3) as Ingusu, '' as Ingdig, Ingres as Ingcem, '' as Ingmei, Fecha_data
                                                from " . $aplicacion . "_000016
                                                where ";
                                    if (!empty($rows['Pachis'])) {
                                        $sql1 .= "Inghis='" . $rows['Pachis'] . "' ";
                                    } else {
                                        $sql1 .= " Inghis != '0'";
                                    }
                                    if (!empty($ingreso)) {
                                        $sql1 .= "and Inging='" . $ingreso . "' ";
                                    }

                                    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000101 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                    if ($res1) {
                                        $num1 = mysql_num_rows($res1);
                                    }
                                }

                                $data['numRegistrosIng'][$rows['Pachis']] = $num1;
                                if ($num1 > 0) {
                                    $arr_validar_especialidad = array();
                                    for ($i; $rows1 = mysql_fetch_array($res1, MYSQL_ASSOC); $i++)  //solo se puede buscar por el nombre del campo
                                    {
                                        $data['infoing'][$i] = $data['infopac'];
                                        //echo "<br>DE 101: ".json_encode($rows1);
                                        foreach ($rows1 as $key => $value) {
                                            //se guarda en data con el prefijo ing_ y empezando en la posicion 3 hasta el final
                                            $data['infoing'][$i]["ing_" . substr($key, 3)] = $value;
                                        }

                                        $data['infoing'][$i]['egr_cexselCauExt'] = $rows1['Ingcai'];
                                        $data['infoing'][$i]['egr_fiatxtFecInA'] = $rows1['Ingfei'];
                                        $data['infoing'][$i]['egr_histxtNumHis'] = $rows1['Inghis'];
                                        $data['infoing'][$i]['egr_ingtxtNumIng'] = $rows1['Ingnin'];

                                        //$data[ 'infoing' ][$i][ 'pac_sextxtSexo' ] = $rows1['Ingnin'];
                                        $data['infoing'][$i]['pac_epstxtEps'] = $rows1['Ingcem'];
                                        $data['infoing'][$i]['pac_epshidEps'] = $rows1['Ingcem'];
                                        $data['infoing'][$i]['pac_edatxtEdad'] = "" . calcularEdad($rows['Pacfna']);

                                        if (!empty($rows1['Ingcem'])) {
                                            $res4 = consultaNombreEntidad($rows1['Ingcem']);
                                            if ($res4) {
                                                $num4 = mysql_num_rows($res4);
                                                if ($num4 > 0) {
                                                    $rows4 = mysql_fetch_array($res4); //se cambio el campo por el de egreso
                                                    $data['infoing'][$i]['pac_epstxtEps'] = utf8_encode($rows4['Codigo'] . "-" . $rows4['Descripcion']);
                                                }
                                            } else {
                                                $data['error'] = 1;
                                                $data['mensaje'] = "No se ejecuto la consulta de busqueda del codigo de la empresa";
                                            }
                                        }

                                        /**Busqueda de diagnosticos**/
                                        if (!empty($historia) && !empty($ingreso) && $data['error'] == 0) {
                                            $tieneDiagnosticos = false;
                                            if ($aplicacionHce != "") //cliame
                                            {
                                                $arregloDiagnosticos = array();
                                                $arregloDiagnosticosAux = consultarDiagnosticosPaciente($historia, $ingreso);
                                                //var_dump( $arregloDiagnosticosAux );
                                                $arregloDiagnosticos = $arregloDiagnosticosAux['diagnosticos'];
                                                $codMedicoIngreso = $arregloDiagnosticosAux['medicoIngreso'];
                                                $codMedicoEgreso = $arregloDiagnosticosAux['medicoDeEgreso'];

                                                $m = 0;
                                                if (count($arregloDiagnosticos)) {
                                                    foreach ($arregloDiagnosticos as $keyDiagnostico => $datosDiagnosticos) {
                                                        $tieneDiagnosticos = true;
                                                        $data['infoing'][$i]['diagnosticos'][$m]['dia_cod'] = $keyDiagnostico;
                                                        $data['infoing'][$i]['diagnosticos'][$m]['DesDia'] = utf8_encode($arregloDiagnosticos[$keyDiagnostico]['descripcion']);
                                                        $data['infoing'][$i]['diagnosticos'][$m]['DesDia'] = utf8_encode($arregloDiagnosticos[$keyDiagnostico]['descripcion']);
                                                        if ($egresoUrgencias) {
                                                            $data['infoing'][$i]['diagnosticos'][$m]['dia_nue'] = "S";
                                                            $data['infoing'][$i]['diagnosticos'][$m]['dia_com'] = "N";
                                                        }
                                                        //--> asignación de médico y centro de costos a cada diagnostico
                                                        $datosMedicoFinal = consultarMedicoDiagnostico($historia, $ingreso, "", $arregloDiagnosticos[$keyDiagnostico]['medico']);

                                                        if ($datosMedicoFinal) {
                                                            $nombreMedicoIngreso = utf8_encode($datosMedicoFinal['Medno1'] . " " . $datosMedicoFinal['Medno2'] . " " . $datosMedicoFinal['Medap1'] . " " . $datosMedicoFinal['Medap2']);
                                                            $especialidadIngreso = $datosMedicoFinal['Espnom'];
                                                            $codigoEspecialidadIngreso = $datosMedicoFinal['Medesp'];
                                                            $medicoUsuario = $datosMedicoFinal['Meddoc'];
                                                            if ($codMedicoIngreso == $arregloDiagnosticos[$keyDiagnostico]['medico'])
                                                                $codMedicoIngreso = $datosMedicoFinal['Meddoc'];

                                                            if ($codMedicoEgreso == $arregloDiagnosticos[$keyDiagnostico]['medico'])
                                                                $codMedicoEgreso = $datosMedicoFinal['Meddoc'];
                                                        } else {
                                                            $nombreMedicoIngreso = "Revisar";
                                                            $especialidadIngreso = "Revisar";
                                                            $codigoEspecialidadIngreso = "";
                                                            $medicoUsuario = "";
                                                        }
                                                        $data['infoing'][$i]['diagnosticos'][$m]['dia_med'] = $medicoUsuario;
                                                        $data['infoing'][$i]['diagnosticos'][$m]['dia_esm'] = $codigoEspecialidadIngreso;
                                                        $data['infoing'][$i]['diagnosticos'][$m]['DesMed'] = utf8_encode($nombreMedicoIngreso . "");
                                                        $data['infoing'][$i]['diagnosticos'][$m]['dia_inf'] = $arregloDiagnosticos[$keyDiagnostico]['notificar'];
                                                        $data['infoing'][$i]['diagnosticos'][$m]['Desesm'] = $especialidadIngreso;
                                                        $data['infoing'][$i]['diagnosticos'][$m]['servicios'][0]['Sed_ser'] = $arregloDiagnosticos[$keyDiagnostico]['centroCostos'];
                                                        $m++;
                                                    }
                                                }
                                                if ($tieneDiagnosticos)
                                                    $m--;

                                                // consultamos la epicrisis para definir el valor del select de egresos, en caso que no tenga epicrisis retornará null
                                                $data['infoing'][$i]['egr_caeselCauEgr'] = consultarCausaEgresoEpicrisis($historia, $ingreso);

                                                if (!$tieneDiagnosticos) {//-->2016-12-02 //-->no se encontraron diagnosticos automáticos se asigna el de la admisión

                                                    $data['infoing'][$i]['egr_caeselCauEgr'] = "A";
                                                    $diagnosticoingreso = "";
                                                    $nombreMedicoIngreso = "";
                                                    $resDiagnostico = consultaNombreImpDiag($rows1['Ingdig']);
                                                    $rowDiagnosticoing = mysql_fetch_array($resDiagnostico);

                                                    if ($aplicacion != "") {
                                                        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2, Medesp, Espnom, Meduma
                                                                    FROM " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
                                                                    WHERE Meddoc = '" . $rows1['Ingmei'] . "'";

                                                        $res4 = mysql_query($sql, $conex);
                                                        if ($res4) {
                                                            $num4 = mysql_num_rows($res4);
                                                            if ($num4 > 0) {
                                                                $rows4 = mysql_fetch_array($res4);
                                                                $nombreMedicoIngreso = $rows4['Medno1'] . " " . $rows4['Medno2'] . " " . $rows4['Medap1'] . " " . $rows4['Medap2'];
                                                                $especialidadIngreso = $rows4['Espnom'];
                                                                $codigoEspecialidadIngreso = $rows4['Medesp'];
                                                                $medicoUsuario = $rows4['Meddoc'];
                                                            } else {
                                                                $nombreMedicoIngreso = "Revisar";
                                                                $especialidadIngreso = "Revisar";
                                                                $codigoEspecialidadIngreso = "";
                                                                $medicoUsuario = "";
                                                            }
                                                        }
                                                    } else {
                                                        $medico = consultarMedicoEspecifico($rows1['Ingmei'], $wbasedato, $aplicacion);
                                                        if ($medico) {
                                                            $nombreMedicoIngreso = $medico['valor']['des'];
                                                            $especialidadIngreso = $medico['valor']['desesp'];
                                                            $codigoEspecialidadIngreso = $medico['valor']['codesp'];
                                                            $medicoUsuario = "";
                                                        } else {
                                                            $nombreMedicoIngreso = "Revisar";
                                                            $especialidadIngreso = "Revisar";
                                                            $codigoEspecialidadIngreso = "";
                                                            $medicoUsuario = "";
                                                        }
                                                    }

                                                    $data['infoing'][$i]['diagnosticos'][$m]['dia_cod'] = $rows1['Ingdig'];
                                                    $data['infoing'][$i]['diagnosticos'][$m]['dia_tip'] = "P";
                                                    $data['infoing'][$i]['diagnosticos'][$m]['dia_nue'] = "N";
                                                    $data['infoing'][$i]['diagnosticos'][$m]['dia_com'] = "N";
                                                    $data['infoing'][$i]['diagnosticos'][$m]['dia_med'] = $medicoUsuario;
                                                    $data['infoing'][$i]['diagnosticos'][$m]['dia_esm'] = $codigoEspecialidadIngreso;
                                                    $data['infoing'][$i]['diagnosticos'][$m]['DesDia'] = utf8_encode($rowDiagnosticoing['Descripcion']);
                                                    $data['infoing'][$i]['diagnosticos'][$m]['DesMed'] = utf8_encode($nombreMedicoIngreso);
                                                    $data['infoing'][$i]['diagnosticos'][$m]['Desesm'] = $especialidadIngreso;
                                                    $data['infoing'][$i]['diagnosticos'][$m]['servicios'][0]['Sed_ser'] = $cco_egreso;
                                                    $servicioIngreso = $cco_egreso;

                                                    if ($medicoUsuario != "") {

                                                        $data['infoing'][$i]['especialidades'][$m]['esp_cod'] = $codigoEspecialidadIngreso;
                                                        $data['infoing'][$i]['especialidades'][$m]['DesEsp'] = $especialidadIngreso;
                                                        $data['infoing'][$i]['especialidades'][$m]['esp_med'] = $medicoUsuario;
                                                        $data['infoing'][$i]['especialidades'][$m]['DesMed'] = utf8_encode($nombreMedicoIngreso);
                                                        $data['infoing'][$i]['especialidades'][$m]['med_mei'] = "on";
                                                        $data['infoing'][$i]['especialidades'][$m]['med_tra'] = "on";
                                                        $data['infoing'][$i]['especialidades'][$m]['med_egr'] = "on";
                                                        $data['infoing'][$i]['especialidades'][$m]['servicios'] = array();
                                                        $aux = array('See_ser' => $servicioIngreso);
                                                        array_push($data['infoing'][$i]['especialidades'][$m]['servicios'], $aux);
                                                    }
                                                }


                                                if ($egresoUrgencias) {

                                                    //--> seleccionar alta, por muerte y tiempo de estancia o alta simple
                                                    $data['infoing'][$i]['egr_caeselCauEgr'] = consultarCausaEgresoUrgencias($historia, $ingreso, $rows1['Ingfei'], $rows1['Inghin']);

                                                    /*$datosMedicoFinal = consultarMedicoDiagnostico( $historia, $ingreso, consultarAliasPorAplicacion($conex, $wemp_pmla, 'formularioEvolucionUrgencias') );

                                                       if(  $datosMedicoFinal ){
                                                            $nombreMedicoIngreso       = utf8_encode($datosMedicoFinal['Medno1']." ".$datosMedicoFinal['Medno2']." ".$datosMedicoFinal['Medap1']." ".$datosMedicoFinal['Medap2']);
                                                            $especialidadIngreso       = $datosMedicoFinal['Espnom'];
                                                            $codigoEspecialidadIngreso = $datosMedicoFinal['Medesp'];
                                                            $medicoUsuario             = $datosMedicoFinal['Meddoc'];
                                                        }else{
                                                            $nombreMedicoIngreso       = "Revisar";
                                                            $especialidadIngreso       = "Revisar";
                                                            $codigoEspecialidadIngreso = "";
                                                            $medicoUsuario             = "";
                                                        }
                                                        $medicoDiagnosticoUrgencias = $medicoUsuario;

                                                        if( $tieneDiagnosticos ){
                                                            for( $m2 = 0; $m2 <= $m; $m2++ ){

                                                                $data[ 'infoing' ][$i]['diagnosticos'][$m2]['dia_med']                 = $medicoUsuario;
                                                                $data[ 'infoing' ][$i]['diagnosticos'][$m2]['dia_esm']                 = $codigoEspecialidadIngreso;
                                                                $data[ 'infoing' ][$i]['diagnosticos'][$m2]['DesMed']                  = utf8_encode($nombreMedicoIngreso."");
                                                                $data[ 'infoing' ][$i]['diagnosticos'][$m2]['Desesm']                  = $especialidadIngreso;
                                                                $data[ 'infoing' ][$i]['diagnosticos'][$m2]['servicios'][0]['Sed_ser'] = $cco_egreso;
                                                                $servicioIngreso                                                       = $cco_egreso;

                                                            }
                                                        }*/

                                                }

                                                $data['infoing'][$i]['egr_caeselCauEgr_text'] = consultarNombreOpcionEgreso($data['infoing'][$i]['egr_caeselCauEgr']);

                                            } else //traer diagnosticos de la clinica del sur
                                            {
                                                $sqlconf = " select Coefor,Coecon,Coetip,Coeest
                                                               from " . $wbasedato . "_000184
                                                               where Coeest = 'on'
                                                               and Coetip = 'D'";

                                                $resconf = mysql_query($sqlconf, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000184 " . mysql_errno() . " - Error en el query $sqlconf - " . mysql_error()));
                                                if ($resconf) {
                                                    $numconf = mysql_num_rows($resconf);
                                                    if ($numconf > 0) {
                                                        $m = 0;
                                                        $m1 = 0;
                                                        for ($l = 0; $rowsconf = mysql_fetch_array($resconf); $l++) {
                                                            if ($rowsconf['Coefor'] == "000139") {
                                                                $sqldia = "select " . $rowsconf['Coecon'] . "
                                                                            from " . $wbasedato . "_" . $rowsconf['Coefor'] . "
                                                                            where Hclhis = '" . $historia . "'
                                                                            and Hcling = '" . $ingreso . "'
                                                                            ";

                                                                $resdia = mysql_query($sqldia, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "_" . $rowsconf['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                                if ($resdia) {
                                                                    $numdia = mysql_num_rows($resdia);
                                                                    if ($numdia > 0) {
                                                                        while ($rowsdia = mysql_fetch_array($resdia)) {
                                                                            if (trim($rowsdia[0]) != "NO APLICA" and $rowsdia[0] != "") {
                                                                                $res5 = consultaNombreImpDiag($rowsdia[0]);
                                                                                if ($res5) {
                                                                                    $num5 = mysql_num_rows($res5);
                                                                                    if ($num5 > 0) {
                                                                                        $rows5 = mysql_fetch_array($res5);
                                                                                        $data['infoing'][$i]['diagnosticos'][$m]['dia_cod'] = $rows5['Codigo'];
                                                                                        $data['infoing'][$i]['diagnosticos'][$m]['DesDia'] = utf8_encode($rows5['Descripcion']);
                                                                                    } else {
                                                                                        //$data[ 'error' ] = 1;
                                                                                        $data['mensaje'] .= "No se encontro el codigo del diagnostico " . $rowsdia[0] . " " . $sqldia . "";
                                                                                    }
                                                                                } else {
                                                                                    $data['error'] = 1;
                                                                                    $data['mensaje'] = "No se ejecuto la consulta de diagnosticos";
                                                                                }
                                                                            }
                                                                            $m++;
                                                                        }

                                                                    }
                                                                }
                                                            } else if ($rowsconf['Coefor'] == "000140") {
                                                                $sqldia = "select " . $rowsconf['Coecon'] . "
                                                                            from " . $wbasedato . "_" . $rowsconf['Coefor'] . "
                                                                            where Inthis = '" . $historia . "'
                                                                            and Inting = '" . $ingreso . "'
                                                                            ";

                                                                $resdia = mysql_query($sqldia, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "_" . $rowsconf['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                                if ($resdia) {
                                                                    $numdia = mysql_num_rows($resdia);
                                                                    if ($numdia > 0) {
                                                                        while ($rowsdia = mysql_fetch_array($resdia)) {
                                                                            if ($rowsdia[0] != "NO APLICA" and $rowsdia[0] != "") {
                                                                                $res6 = consultaNombreImpDiag($rowsdia[0]);
                                                                                if ($res6) {
                                                                                    $num6 = mysql_num_rows($res6);
                                                                                    if ($num6 > 0) {
                                                                                        $rows6 = mysql_fetch_array($res6);
                                                                                        $data['infoing'][$i]['diagnosticos'][$m1]['dia_cod'] = $rows6['Codigo'];
                                                                                        $data['infoing'][$i]['diagnosticos'][$m1]['DesDia'] = utf8_encode($rows6['Descripcion']);
                                                                                    } else {
                                                                                        //$data[ 'error' ] = 1;
                                                                                        $data['mensaje'] = "No se encontro el codigo del diagnostico";
                                                                                    }
                                                                                } else {
                                                                                    $data['error'] = 1;
                                                                                    $data['mensaje'] = "No se ejecuto la consulta de diagnosticos";
                                                                                }
                                                                            }
                                                                            $m1++;
                                                                        }

                                                                    }
                                                                }
                                                            }
                                                        } //for
                                                    }
                                                }
                                            }
                                            /********** fin diagnosticos en cada fila **********/
                                        }
                                        /**Fin busqueda diagnosticos**/

                                        /**Busqueda de procedimientos**/
                                        $serviciosMedicoCirugia = array();
                                        $formularios_especificos = array();
                                        $serviciosCirugia = consultarServiciosCirugia();

                                        if (!empty($historia) && !empty($ingreso) && $data['error'] == 0) {
                                            $arrayAuxIndicesProcedimiento = array();
                                            $tblInfoCir = consultarAplicacion($conex, $wemp_pmla, 'datosQuirurgicosHceFormulario');
                                            $campPS = consultarAplicacion($conex, $wemp_pmla, 'campoQuirurgicoPrincipalSecundario');
                                            if ($aplicacionHce != "") //cliame
                                            {
                                                $sqlconf1 = " select Coefor,Coecon,Coetip,Coeest
                                                               from " . $wbasedato . "_000184
                                                               where Coeest = 'on'
                                                               and Coetip = 'P'";

                                                $resconf1 = mysql_query($sqlconf1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000184 " . mysql_errno() . " - Error en el query $sqlconf1 - " . mysql_error()));
                                                if ($resconf1) {
                                                    $numconf1 = mysql_num_rows($resconf1);
                                                    if ($numconf1 > 0) {
                                                        $n = 0;
                                                        for ($l = 0; $rowsconf1 = mysql_fetch_array($resconf1); $l++) {
                                                            $sqlpro = "select movpro,movcon,movhis,moving,movtip,movdat,movusu
                                                                            from " . $alias1 . "_" . $rowsconf1['Coefor'] . "
                                                                            where movcon = " . $rowsconf1['Coecon'] . "
                                                                            and movhis = '" . $historia . "'
                                                                            and moving = '" . $ingreso . "'
                                                                            ";
                                                            //echo $sqlpro;

                                                            $respro = mysql_query($sqlpro, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $alias1 . "_" . $rowsconf1['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                            if ($respro) {
                                                                $numpro = mysql_num_rows($respro);
                                                                if ($numpro > 0) {
                                                                    while ($rowspro = mysql_fetch_array($respro)) {

                                                                        if ($rowspro['movtip'] == 'Seleccion') {
                                                                            $proce = quitarEtiquetasCadena($rowspro['movdat']);
                                                                        } else {
                                                                            $proce = $rowspro['movdat'];
                                                                        }
                                                                        $medico = consultarMedicoEspecifico($rowspro['movusu'], $wbasedato, $aplicacion);
                                                                        if ($medico) {
                                                                            $proce .= " -Med: " . $rowspro['movusu'] . " " . $medico['valor']['des'];
                                                                            $proce .= " - Esp: " . $medico['valor']['codesp'] . " " . $medico['valor']['desesp'];
                                                                        }
                                                                        $data['infoing'][$i]['procedimientos'][$n]['txtaObsPro'] = trim(utf8_encode($proce));
                                                                        // $var2.= $data[ 'infoing' ][$i]['procedimientos'][$n]['txtaObsPro'] = $rowspro['movdat'];
                                                                        $n++;
                                                                    }
                                                                } else {

                                                                }
                                                            }
                                                        }
                                                    }


                                                    $query = " SELECT Enlhis, Enling, Enlpro as 'Procod', c.Pronom as 'Pronom', Enlter, Meduma as 'Promed', CONCAT( d.Medno1, ' ', d.Medno2, ' ', d.Medap1, ' ', d.Medap2 ) as 'nombreMedico' , Enlesp as 'Proesm', e.Espnom as 'Espnom',
                                                                          Enltur, Turtur, Turqui, f.Quicco as 'Proser', Turfec as 'Profec', 'S' as 'Proqui'
                                                                     FROM {$wbasedato}_000199 a
                                                                     INNER JOIN
                                                                          {$wbasedato_tcx}_000011    b on ( a.Enlhis = '{$historia}' and a.Enling = '{$ingreso}' and b.turtur = a.Enltur and Enlest = 'on' and Enlpqt != 'on' )
                                                                     INNER JOIN
                                                                          {$wbasedato}_000103 c on ( a.Enlpro = c.Procod AND char_length(procod) >= 6 )
                                                                     INNER JOIN
                                                                          {$wbasedato_tcx}_000012    f on ( b.Turqui = f.Quicod )
                                                                      LEFT JOIN
                                                                          {$aplicacion}_000048 d on ( d.Meddoc = a.Enlter and d.Medesp = a.Enlesp and d.medest = 'on')
                                                                      LEFT JOIN
                                                                          {$aplicacion}_000044 e on ( e.Espcod = a.Enlesp )
                                                                     GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15
                                                                     UNION ALL
                                                                    SELECT Enlhis, Enling, c.Procod as 'Procod', c.Pronom as 'Pronom', Enlter, Meduma as 'Promed', CONCAT( d.Medno1, ' ', d.Medno2, ' ', d.Medap1, ' ', d.Medap2 ) as 'nombreMedico' , Enlesp as 'Proesm', e.Espnom as 'Espnom',
                                                                          Enltur, Turtur, Turqui, f.Quicco as 'Proser', Turfec as 'Profec', 'S' as 'Proqui'
                                                                     FROM {$wbasedato}_000199  a
                                                                     INNER JOIN
                                                                          {$wbasedato}_000114 p ON ( Enlpro = p.Paqdetcod  AND Enlpqt = 'on' AND paqdetest = 'on' )
                                                                     INNER JOIN
                                                                          {$wbasedato_tcx}_000011    b on ( a.Enlhis = '{$historia}' and a.Enling = '{$ingreso}' and b.turtur = a.Enltur and Enlest = 'on' )
                                                                     INNER JOIN
                                                                          {$wbasedato}_000103 c on ( p.paqdetpro = c.Procod AND char_length( c.procod ) >= 6 )
                                                                     INNER JOIN
                                                                           root_000012 r on ( r.codigo = c.procup and r.egreso = 'on')
                                                                     INNER JOIN
                                                                          {$wbasedato_tcx}_000012    f on ( b.Turqui = f.Quicod )
                                                                      LEFT JOIN
                                                                          {$aplicacion}_000048 d on ( d.Meddoc = a.Enlter and d.medest = 'on' )
                                                                      LEFT JOIN
                                                                          {$aplicacion}_000044 e on ( e.Espcod = d.Medesp )
                                                                     GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15
                                                                      UNION ALL
                                                                     SELECT Tcarhis as 'Enlhis',  Tcaring as 'Enling', Tcarprocod as 'Procod', mc.nombre as 'Pronom', Tcartercod as 'Enlter', '' as 'Promed', Tcarternom 'nombreMedico' ,
                                                                            '' as 'Proesm', e.Espnom as 'Espnom', '' as 'Enltur', '' as 'Turtur', '' as 'Turqui', Tcarser as 'Proser', Tcarfec as 'Profec', 'N' as 'Proqui'
                                                                      FROM {$wbasedato}_000106 p
                                                                     INNER JOIN
                                                                           root_000012 mc on (     Tcarhis = '{$historia}'
                                                                                                      AND Tcaring = '{$ingreso}'
                                                                                                      AND p.Tcarprocod = mc.codigo
                                                                                                      AND p.Tcartercod != ''
                                                                                                      AND p.Tcarser not in ( {$serviciosCirugia} )
                                                                                                      AND mc.egreso = 'on' )
                                                                      LEFT JOIN
                                                                          {$aplicacion}_000048 d on ( d.Meddoc = p.Tcartercod AND d.medest = 'on' )
                                                                      LEFT JOIN
                                                                          {$aplicacion}_000044 e on ( e.Espcod = d.Medesp )
                                                                     WHERE 1
                                                                     GROUP BY 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13 ";

                                                    $rsprocir = mysql_query($query, $conex) or die(mysql_error() . " - " . $query);
                                                    while ($rowprocir = mysql_fetch_assoc($rsprocir)) {
                                                        if ($n == 0) {
                                                            $data['infoing'][$i]['procedimientos'][$n]['pro_tip'] = "P";
                                                        } else {
                                                            $data['infoing'][$i]['procedimientos'][$n]['pro_tip'] = "S";
                                                        }
                                                        //$numeroprocedimientos++;

                                                        $data['infoing'][$i]['procedimientos'][$n]['pro_cod'] = $rowprocir['Procod'];
                                                        //$data[ 'infoing' ][$i]['procedimientos'][$n]['pro_tip'] = $rowprocir['Protip'];
                                                        $data['infoing'][$i]['procedimientos'][$n]['pro_med'] = $rowprocir['Enlter'];
                                                        $data['infoing'][$i]['procedimientos'][$n]['pro_esm'] = $rowprocir['Proesm'];
                                                        $data['infoing'][$i]['procedimientos'][$n]['pro_ane'] = $rowprocir['Proane'];
                                                        $data['infoing'][$i]['procedimientos'][$n]['pro_qui'] = $rowprocir['Proqui'];
                                                        $data['infoing'][$i]['procedimientos'][$n]['pro_fec'] = $rowprocir['Profec'];
                                                        $data['infoing'][$i]['procedimientos'][$n]['pro_ser'] = $rowprocir['Proser'];
                                                        $data['infoing'][$i]['procedimientos'][$n]['ProDes'] = utf8_encode($rowprocir['Pronom']);
                                                        $data['infoing'][$i]['procedimientos'][$n]['DesMed'] = utf8_encode($rowprocir['nombreMedico']);
                                                        $data['infoing'][$i]['procedimientos'][$n]['Desesm'] = $rowprocir['Espnom'];
                                                        if (!in_array($rowprocir['proSer'], $ccosProcsCirugia)) {
                                                            array_push($ccosProcsCirugia, $rowprocir['proSer']);
                                                        }

                                                        $res5 = consultaNombreServ($rowprocir['Proser'], $aplicacion);
                                                        if ($res5) {

                                                            $num5 = mysql_num_rows($res5);
                                                            if ($num5 > 0) {
                                                                $rows5 = mysql_fetch_array($res5);
                                                                if ($aplicacion == "") {
                                                                    $data['infoing'][$i]['procedimientos'][$n]['proSer'] = $rowprocir['Proser'] . "-" . $rows5['Ccodes'];
                                                                } else {
                                                                    $data['infoing'][$i]['procedimientos'][$n]['proSer'] = $rowprocir['Proser'] . "-" . $rows5['Cconom'];
                                                                }
                                                            } else {
                                                            }
                                                        }

                                                        /*--> en esta sección se verifica si el procedimiento es principal o secundario, para lograr esto, debe
                                                                  debe consultarse en la tabla hce_000077 donde el movcon sea 99; por medio de esta consulta se obtendrá
                                                                  un string que almacena los datos del procedimiento, luego por medio de funciones de strings, se debe
                                                                  filtrar la información para obtener el tipo( principal o secundario ).
                                                            */
                                                        $qinfo = " SELECT movdat
                                                                          FROM {$aplicacionHce}_{$tblInfoCir}
                                                                         WHERE movhis = '{$historia}'
                                                                           AND moving = '{$ingreso}'
                                                                           AND movcon = '{$campPS}'
                                                                           AND movdat like '%{$rowprocir['Procod']}%'";

                                                        $rsinfo = mysql_query($qinfo, $conex);

                                                        while ($rowinfo = mysql_fetch_assoc($rsinfo)) {
                                                            $datos = $rowinfo['movdat'];
                                                            $aux2 = explode("</option>", $datos);

                                                            for ($key = 0; $key < count($aux2); $key++) {
                                                                $pos1 = "";
                                                                $pos2 = "";
                                                                $datosAux = "";
                                                                $string = $aux2[$key];
                                                                if (trim($string) != "") {
                                                                    $pos1 = strpos($string, "=>");
                                                                    $pos2 = strpos($string, " ", $pos1);
                                                                    $length = $pos2 - $pos1;
                                                                    $datosAux = substr($aux2[$key], $pos1, $length);
                                                                    $datosAux = trim($datosAux);
                                                                    // en este punto debe haber un string similar a este 'S-P-02820'
                                                                    $datosAux = explode("-", $datosAux);

                                                                    if ($datosAux[2] == $rowprocir['Procod']) {//si es el detalle del codigo de procedimiento buscado en el momento
                                                                        $data['infoing'][$i]['procedimientos'][$n]['pro_tip'] = $datosAux[1];
                                                                    }

                                                                }

                                                            }
                                                        }

                                                        if (!isset($serviciosMedicoCirugia[$rowprocir['Enlter']])) {
                                                            $serviciosMedicoCirugia[$rowprocir['Enlter']] = array();
                                                        }
                                                        // se guardan los servicios donde el cirujano atendió al paciente, para aderir la   información de una vez
                                                        // en la zona de especialistas.
                                                        array_push($serviciosMedicoCirugia[$rowprocir['Enlter']], $rowprocir['Proser']);
                                                        $n++;
                                                    }
                                                } else {
                                                    $data['error'] = 1;
                                                    $data['mensaje'] = "No se ejecuto la consulta a la tabla de configuracion para los diagnosticos";
                                                }
                                            }
                                        }

                                        /**Fin busqueda procedimientos**/

                                        /**Busqueda de especialidades**/
                                        if (!empty($historia) && !empty($ingreso) && $data['error'] == 0) {
                                            $arr_validar_especialista = array();
                                            $arr_indices_especialista = array();
                                            $in = 0;

                                            //2014-08-04 El primer medico es el de ingreso, se trae de la tabla 101
                                            $med_ingreso = $codMedicoIngreso;

                                            //$med_ingreso = ( $egresoUrgencias ) ? $rows1['Ingmei'] : "";//2018-09-20//comentada 2019-10-08 porque el medico de ingreso siempre debe estar presente con las directrices que indican traerlo desde el formulario 360 o 367
                                            if ($med_ingreso != "") {
                                                $medico = consultarMedicoEspecifico($med_ingreso, $wbasedato, $aplicacion);
                                                if ($medico) {
                                                    array_push($arr_validar_especialista, $med_ingreso);
                                                    $arr_indices_especialista[$med_ingreso]['i'] = $i;
                                                    $arr_indices_especialista[$med_ingreso]['in'] = $in;
                                                    $data['infoing'][$i]['especialidades'][$in]['esp_cod'] = $medico['valor']['codesp'];
                                                    $data['infoing'][$i]['especialidades'][$in]['DesEsp'] = utf8_encode($medico['valor']['desesp']);
                                                    $data['infoing'][$i]['especialidades'][$in]['esp_med'] = $med_ingreso;
                                                    $data['infoing'][$i]['especialidades'][$in]['DesMed'] = utf8_encode($medico['valor']['des']);
                                                    $data['infoing'][$i]['especialidades'][$in]['med_mei'] = "on";

                                                    if ($codMedicoEgreso == $med_ingreso) {
                                                        $data['infoing'][$i]['especialidades'][$in]['med_tra'] = "on";
                                                        $data['infoing'][$i]['especialidades'][$in]['med_egr'] = "on";
                                                    }

                                                    if (!isset($data['infoing'][$i]['especialidades'][$in]['servicios']))
                                                        $data['infoing'][$i]['especialidades'][$in]['servicios'] = array();

                                                    if ($rowsesp['Fircco'] != "") {
                                                        $aux = array('See_ser' => $rowsesp['Fircco']);
                                                        array_push($data['infoing'][$i]['especialidades'][$in]['servicios'], $aux);
                                                    }

                                                    if ($egresoUrgencias) {
                                                        $aux = array('See_ser' => $cco_egreso);
                                                        array_push($data['infoing'][$i]['especialidades'][$in]['servicios'], $aux);
                                                    }

                                                    if (isset($serviciosMedicoCirugia[$med_ingreso])) {

                                                        foreach ($serviciosMedicoCirugia[$med_ingreso] as $key => $servicioCirugia) {
                                                            $aux = array('See_ser' => $servicioCirugia);
                                                            array_push($data['infoing'][$i]['especialidades'][$in]['servicios'], $aux);
                                                        }
                                                    }
                                                    $in++;
                                                }
                                            }

                                            $sqlconf2 = " select Coefor,Coecon,Coetip,Coeest
                                                               from " . $wbasedato . "_000184
                                                               where Coeest = 'on'
                                                               and Coetip = 'E'";

                                            $resconf2 = mysql_query($sqlconf2, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000184 " . mysql_errno() . " - Error en el query $sqlconf2 - " . mysql_error()));

                                            array_push($formularios_especificos, $rowsconf1['Coecon']);
                                            while ($rowsconf1 = mysql_fetch_array($resconf2)) {
                                                if (trim($rowsconf1['Coecon']) != "")
                                                    array_push($formularios_especificos, $rowsconf1['Coecon']);
                                            }

                                            if ($aplicacionHce != "") //cliame
                                            {
                                                $lista_hce_forms = array();
                                                $sqlesp = "SELECT Firpro,Firhis,Firing,Firusu,Firfir,Fircco,Meddoc,Medno1,Medno2,Medap1,Medap2, Medesp, Espnom
                                                                 FROM {$aplicacionHce}_000036
                                                                INNER JOIN
                                                                      {$aplicacion}_000048    ON ( Firhis = '{$historia}' AND Firing = '{$ingreso}' AND Firfir = 'on' AND Meduma = Firusu )
                                                                INNER JOIN
                                                                      {$aplicacionHce}_000020 ON ( usucod = Firusu )
                                                                INNER JOIN
                                                                      {$aplicacionHce}_000019 ON ( Rolcod = Usurol AND Rolmed = 'on' )
                                                                LEFT JOIN
                                                                      {$aplicacion}_000044    ON ( Medesp = Espcod )
                                                               GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12";
                                                $res4 = mysql_query($sqlesp, $conex);

                                                if ($res4) {
                                                    while ($rows4 = mysql_fetch_array($res4)) {

                                                        if (!in_array($rows4['Firpro'], $lista_hce_forms))
                                                            array_push($lista_hce_forms, $rows4['Firpro']);

                                                        $b = 1;
                                                        //validacion para que no hayan codigos repetidos en el array
                                                        if (in_array($rows4['Meddoc'], $arr_validar_especialista) == false) {
                                                            array_push($arr_validar_especialista, $rows4['Meddoc']);
                                                            $arr_indices_especialista[$rows4['Meddoc']]['i'] = $i;
                                                            $arr_indices_especialista[$rows4['Meddoc']]['in'] = $in;
                                                            $data['infoing'][$i]['especialidades'][$in]['esp_cod'] = $rows4['Medesp'];
                                                            $data['infoing'][$i]['especialidades'][$in]['DesEsp'] = utf8_encode($rows4['Espnom']);
                                                            $data['infoing'][$i]['especialidades'][$in]['esp_med'] = $rows4['Meddoc'];
                                                            $data['infoing'][$i]['especialidades'][$in]['DesMed'] = utf8_encode($rows4['Medno1'] . " " . $rows4['Medno2'] . " " . $rows4['Medap1'] . " " . $rows4['Medap2']);
                                                            $data['infoing'][$i]['especialidades'][$in]['servicios'] = array();

                                                            if ($codMedicoEgreso == $rows4['Meddoc']) {
                                                                $data['infoing'][$i]['especialidades'][$in]['med_tra'] = "on";
                                                                $data['infoing'][$i]['especialidades'][$in]['med_egr'] = "on";
                                                            }

                                                            if ($rows4['Fircco'] != '') {
                                                                $aux = array('See_ser' => $rows4['Fircco']);
                                                                array_push($data['infoing'][$i]['especialidades'][$in]['servicios'], $aux);
                                                            }
                                                            // se verifica si es un medico que realizó atención en cirugía, para adicionar dicha información
                                                            if (isset($serviciosMedicoCirugia[$rows4['Meddoc']])) {

                                                                foreach ($serviciosMedicoCirugia[$rows4['Meddoc']] as $key => $servicioCirugia) {
                                                                    $aux = array('See_ser' => $servicioCirugia);
                                                                    array_push($data['infoing'][$i]['especialidades'][$in]['servicios'], $aux);
                                                                }
                                                            }
                                                            $in++;
                                                            //podriamos apilar los servicios visitados por el especialista en este punto
                                                        } else {
                                                            //---> apilar ccos
                                                            if ($rows4['Fircco'] != '') {
                                                                $aux = array('See_ser' => $rows4['Fircco']);
                                                                array_push($data['infoing'][$arr_indices_especialista[$rows4['Meddoc']]['i']]['especialidades'][$arr_indices_especialista[$rows4['Meddoc']]['in']]['servicios'], $aux);
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $data['error'] = 1;
                                                    $data['mensaje'] = "No se ejecuto la consulta a la tabla de formularios firmados " . $alias1 . "_000036";
                                                }

                                                $array_formularios_sin_firmar = array();
                                                foreach ($formularios_especificos as $key => $dato) {
                                                    if (!in_array($dato, $lista_hce_forms)) {
                                                        array_push($array_formularios_sin_firmar, "'" . $dato . "'");
                                                        $b = 0;
                                                    }
                                                }

                                                if ($b == 0) {
                                                    $nombres_formularios = ":";
                                                    $sqlpro = "SELECT Encdes
                                                                     FROM " . $alias1 . "_000001
                                                                    WHERE Encpro IN (" . implode(",", $array_formularios_sin_firmar) . ")
                                                                    GROUP BY Encpro ORDER BY Encdes";

                                                    $respro = mysql_query($sqlpro, $conex);
                                                    if ($respro) {
                                                        $numpro = mysql_num_rows($respro);
                                                        if ($numpro > 0) {
                                                            while ($rowspro = mysql_fetch_assoc($respro)) {
                                                                $nombres_formularios .= "\n" . $rowspro["Encdes"];
                                                            }
                                                        }
                                                    }
                                                    /*$data['mensaje']="No existen los siguientes formularios firmados para la historia ".$historia." con ingreso ".$ingreso." ".utf8_encode($nombres_formularios);*/
                                                }
                                            } else //para la clinica del sur
                                            {
                                                $sqlconf2 = " select Coefor,Coecon,Coetip,Coeest
                                                               from " . $wbasedato . "_000184
                                                               where Coeest = 'on'
                                                               and Coetip = 'E'";

                                                $resconf2 = mysql_query($sqlconf2, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000184 " . mysql_errno() . " - Error en el query $sqlconf2 - " . mysql_error()));
                                                if ($resconf2) {
                                                    $numconf2 = mysql_num_rows($resconf2);
                                                    if ($numconf2 > 0) {
                                                        $m = 0;
                                                        for ($l = 0; $rowsconf2 = mysql_fetch_array($resconf2); $l++) {
                                                            if ($rowsconf2['Coefor'] == "000139") {
                                                                $sqlesp = "select " . $rowsconf2['Coecon'] . "
                                                                            from " . $wbasedato . "_" . $rowsconf2['Coefor'] . "
                                                                            where Hclhis = '" . $historia . "'
                                                                            and Hcling = '" . $ingreso . "'
                                                                            ";

                                                                $resesp = mysql_query($sqlesp, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "_" . $rowsconf2['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                                if ($resesp) {
                                                                    $numesp = mysql_num_rows($resesp);
                                                                    if ($numesp > 0) {
                                                                        while ($rowsesp = mysql_fetch_array($resesp)) {
                                                                            if (trim($rowsesp[0]) != "NO APLICA" and $rowsesp[0] != "") {
                                                                                $codMed = explode("-", $rowsesp[0]);
                                                                                $codMed = $codMed[0];

                                                                                $sqlMedEsp = "select Medcod,Medesp
                                                                                            from " . $wbasedato . "_000051
                                                                                            where Medcod = '" . $codMed . "'
                                                                                            and Medest = 'on'";

                                                                                $resMedEsp = mysql_query($sqlMedEsp, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $alias1 . "_" . $rowsconf2['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                                                if ($resMedEsp) {
                                                                                    $numMedEsp = mysql_num_rows($resMedEsp);
                                                                                    if ($numMedEsp > 0) {
                                                                                        $rowsMedEsp = mysql_fetch_array($resMedEsp);

                                                                                        $codEsp = explode("-", $rowsMedEsp['Medesp']);
                                                                                        $codEsp = $codEsp[0];

                                                                                        $sqlEsp1 = "select Espcod,Espnom
                                                                                            from " . $wbasedato . "_000053
                                                                                            where Espcod = '" . $codEsp . "'";

                                                                                        $resEsp1 = mysql_query($sqlEsp1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $alias1 . "_" . $rowsconf2['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                                                        if ($resEsp1) {
                                                                                            $numEsp1 = mysql_num_rows($resEsp1);
                                                                                            if ($numEsp1 > 0) {
                                                                                                $rowsEsp1 = mysql_fetch_array($resEsp1);
                                                                                                //validacion para que no hayan codigos repetidos en el array
                                                                                                if (!array_key_exists($rowsEsp1['Espcod'], $arr_validar_especialidad)) {
                                                                                                    $arr_validar_especialidad[$rowsEsp1['Espcod']] = $rowsEsp1['Espcod'];
                                                                                                    $data['infoing'][$i]['especialidades'][$m]['esp_cod'] = $rowsEsp1['Espcod'];
                                                                                                    $data['infoing'][$i]['especialidades'][$m]['DesEsp'] = $rowsEsp1['Espnom'];
                                                                                                    // $var5.=$data[ 'infoing' ][$i]['especialidades'][$o]['esp_cod'] = $rowsEsp1['Espcod'];
                                                                                                    $m++;
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    } else {
                                                                                        //$data[ 'error' ] = 1;
                                                                                        $data['mensaje'] = "No se encontro el codigo de la especialidad";
                                                                                    }
                                                                                } else {
                                                                                    $data['error'] = 1;
                                                                                    $data['mensaje'] = "No se ejecuto la consulta de las especialidades";
                                                                                }
                                                                            }
                                                                            //$m++;
                                                                        }
                                                                    }
                                                                }
                                                            } else if ($rowsconf2['Coefor'] == "000140") {
                                                                $sqlesp = "select " . $rowsconf2['Coecon'] . "
                                                                            from " . $wbasedato . "_" . $rowsconf2['Coefor'] . "
                                                                            where Inthis = '" . $historia . "'
                                                                            and Inting = '" . $ingreso . "'";

                                                                $resesp = mysql_query($sqlesp, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "_" . $rowsconf['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                                if ($resesp) {
                                                                    $numesp = mysql_num_rows($resesp);
                                                                    if ($numesp > 0) {
                                                                        while ($rowsesp = mysql_fetch_array($resesp)) {
                                                                            if (trim($rowsesp[0]) != "NO APLICA" and $rowsesp[0] != "") {
                                                                                $codMed = explode("-", $rowsesp[0]);
                                                                                $codMed = $codMed[0];

                                                                                $sqlMedEsp = "select Medcod,Medesp
                                                                                            from " . $wbasedato . "_000051
                                                                                            where Medcod = " . $codMed . "
                                                                                            and Medest = 'on'";

                                                                                $resMedEsp = mysql_query($sqlMedEsp, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $alias1 . "_" . $rowsconf2['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                                                if ($resMedEsp) {
                                                                                    $numMedEsp = mysql_num_rows($resMedEsp);
                                                                                    if ($numMedEsp > 0) {
                                                                                        $rowsMedEsp = mysql_fetch_array($resMedEsp);

                                                                                        $codEsp = explode("-", $rowsMedEsp['Medesp']);
                                                                                        $codEsp = $codEsp[0];

                                                                                        $sqlEsp1 = "select Espcod,Espnom
                                                                                            from " . $wbasedato . "_000053
                                                                                            where Espcod = '" . $codEsp . "'";

                                                                                        $resEsp1 = mysql_query($sqlEsp1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $alias1 . "_" . $rowsconf2['Coefor'] . "" . mysql_errno() . " - Error en el query $sqldia - " . mysql_error()));
                                                                                        if ($resEsp1) {
                                                                                            $numEsp1 = mysql_num_rows($resEsp1);
                                                                                            if ($numEsp1 > 0) {
                                                                                                $rowsEsp1 = mysql_fetch_array($resEsp1);
                                                                                                //validacion para que no hayan codigos repetidos en el array
                                                                                                if (!array_key_exists($rowsEsp1['Espcod'], $arr_validar_especialidad)) {
                                                                                                    $arr_validar_especialidad[$rowsEsp1['Espcod']] = $rowsEsp1['Espcod'];
                                                                                                    $data['infoing'][$i]['especialidades'][$m]['esp_cod'] = $rowsEsp1['Espcod'];
                                                                                                    $data['infoing'][$i]['especialidades'][$m]['DesEsp'] = $rowsEsp1['Espnom'];
                                                                                                    // $var5.=$data[ 'infoing' ][$i]['especialidades'][$o]['esp_cod'] = $rowsEsp1['Espcod'];
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    } else {
                                                                                        //$data[ 'error' ] = 1;
                                                                                        $data['mensaje'] = "No se encontro el codigo de la especialidad";
                                                                                    }
                                                                                } else {
                                                                                    $data['error'] = 1;
                                                                                    $data['mensaje'] = "No se ejecuto la consulta de las especialidades";
                                                                                }
                                                                            }
                                                                            $m++;
                                                                        }//while
                                                                    }
                                                                }
                                                            }
                                                        }//for
                                                    } //numconf2 > 0
                                                }
                                            }//clisur
                                        }
                                        // $sqlesp = "select Espcod,Espnom
                                        // from ".$aplicacion."_000044
                                        // limit 5";

                                        // $resesp = mysql_query( $sqlesp, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( "Error consultando la tabla ".$aplicacion."000017 ".mysql_errno()." - Error en el query $sql1 - ".mysql_error() ) );
                                        // if ($resesp)
                                        // {
                                        // $numesp=mysql_num_rows($resesp);
                                        // if ($numesp>0)
                                        // {
                                        // for ($k=0; $rowsesp=mysql_fetch_array($resesp);$k++)
                                        // {
                                        // $data[ 'infoing' ][$i]['especialidades'][$k]['esp_cod'] = $rowsesp['Espcod'];
                                        // $data[ 'infoing' ][$i]['especialidades'][$k]['DesEsp'] = $rowsesp['Espnom'];
                                        // }
                                        // }
                                        // else
                                        // {
                                        // $data[ 'error' ] = 1;
                                        // $data['mensaje']="No se encontraron especialidades asociadas a la historia ".$historia."";
                                        // }

                                        // }
                                        // else
                                        // {
                                        // // $data[ 'error' ] = 1;
                                        // $data['mensaje']="No se ejecuto la consulta de busqueda de las especialidades";
                                        // }

                                        /**Fin busqueda especialidades**/


                                        /**Busqueda de servicios**/

                                        if (!empty($historia) && !empty($ingreso) && $data['error'] == 0) {
                                            if ($aplicacionHce != "") {
                                                $sqlser = "select Eyrsor as servicio, Cconom
                                                            from " . $aplicacion . "_000017, " . $aplicacion . "_000011
                                                            where Eyrest ='on'
                                                            and Eyrtip = 'Recibo'
                                                            and Eyrhis = '" . $historia . "'
                                                            and Eyring = '" . $ingreso . "'
                                                            and Eyrsor = Ccocod";
                                                $sqlser .= " union";
                                                $sqlser .= " select Eyrsde as servicio, Cconom
                                                            from " . $aplicacion . "_000017, " . $aplicacion . "_000011
                                                            where Eyrest ='on'
                                                            and Eyrtip = 'Entrega'
                                                            and Eyrhis = '" . $historia . "'
                                                            and Eyring = '" . $ingreso . "'
                                                            and Eyrsde = Ccocod";
                                                $sqlser .= " union";
                                                $sqlser .= " select Ubisac as servicio, Cconom
                                                            from " . $aplicacion . "_000018, " . $aplicacion . "_000011
                                                            where Ubihis = '" . $historia . "'
                                                            and Ubiing   = '" . $ingreso . "'
                                                            and Ubisac = Ccocod";
                                                $sqlser .= " group by servicio"; //no se coloca Ubisan porque si no tiene registros en la 17 no tiene servicio anterior


                                                $resser = mysql_query($sqlser, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $aplicacion . "000017 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                                if ($resser) {
                                                    $numser = mysql_num_rows($resser);
                                                    if ($numser > 0) {
                                                        for ($k = 0; $rowsser = mysql_fetch_array($resser); $k++) {
                                                            if (!in_array($rowsser['servicio'], $ccosProcsCirugia) && $rowsser['servicio'] == $ccoIngresoCir) {
                                                                //--> si dice que es igual a el cco 1016 de cirugia pero no está entre los servicios visitados
                                                                // por procedimientos, no se agrega en la lista de servicios visitados
                                                            } else {
                                                                $data['infoing'][$i]['servicios'][$k]['ser_cod'] = $rowsser['servicio'];
                                                                $data['infoing'][$i]['servicios'][$k]['DesSer'] = $rowsser['Cconom'];
                                                            }
                                                        }
                                                    } else {
                                                        // $data[ 'error' ] = 1;                                                           // $var --<br>$var1.. <br>$var2 **<br>$var3 variable n:$n  <br>  $sqlconf2 $vari $vari1 $sqlEsp1 $var5
                                                        $data['mensaje'] = "No se encontraron servicios asociados a la historia " . $historia . " e ingreso " . $ingreso;
                                                    }
                                                } else {
                                                    // $data[ 'error' ] = 1;
                                                    $data['mensaje'] = "No se ejecuto la consulta de busqueda de los servicios";
                                                }
                                            } //FALTA PARA LA CLINICA DEL SUR
                                        }
                                        /**Fin busqueda servicios**/

                                    }//for 101

                                }//$num1>0
                                else {
                                    $data['error'] = 1;
                                    $data['mensaje'] = "No se encontraron registros del ingreso " . $ingreso;
                                }
                            } else {
                                $data['error'] = 1;
                            }
                            /***fin busqueda en la tabla 101***/
                        } //fin for 100
                    } //si trae registros de la 100
                    else {
                        $data['mensaje'] = "No se encontro informacion para los datos ingresados ";
                    }
                } else {
                    $data['error'] = 1;
                }
            } else { //no se ejecuto la consulta de la 108

                $data["error"] = 1;
                $data["mensaje"] = "No se encontraron datos para realizar la busqueda ";
            }
            /***fin busqueda en la tabla 100***/
            if ($mostrarSalida == "on")
                echo "<pre>" . print_r($data, true) . "</pre>";
            echo json_encode($data);
            return;

        /*****fin mostrar datos almacenados antes del egreso******/

        case 'mostrarDatosAlmacenadosEgreso':
            /*****mostrar datos almacenados despues del egreso******/

            //se consulta si existe esa aplicacion
            $alias = "movhos";
            $aplicacion = consultarAplicacion($conex, $wemp_pmla, $alias);

            $alias1 = "hce";
            $aplicacionHce = consultarAplicacion($conex, $wemp_pmla, $alias1);
            $medicoDeEgreso = "";
            $medicoTratante = "";
            if (!empty($historia) || !empty($ingreso) || !empty($documento) || !empty($priApe) || !empty($segApe) || !empty($priNom) || !empty($segNom)) {
                /***se consulta si la persona ha venido antes en la tabla 100***/
                $sql = "select Pacdoc, Pactdo, Pacap1, Pacap2, Pacno1, Pacno2,Pachis, Pacact, Egring,Pacsex, Pacfna
                            from " . $wbasedato . "_000108, " . $wbasedato . "_000100
                            where Pachis !='0'
                            and Egrhis = Pachis
                            ";
                if (!empty($historia)) {
                    $sql .= " and Egrhis = '" . $historia . "' ";
                }
                if (!empty($ingreso)) {
                    $sql .= " and Egring = '" . $ingreso . "'  ";
                }
                if (!empty($documento)) {
                    $sql .= " and Pacdoc = '" . $documento . "'  ";
                }
                if (!empty($priApe)) {
                    $sql .= " and Pacap1 like '" . $priApe . "'  ";
                }
                if (!empty($segApe)) {
                    $sql .= " and Pacap2 like '" . $segApe . "'  ";
                }
                if (!empty($priNom)) {
                    $sql .= " and Pacno1 like '" . $priNom . "'  ";
                }
                if (!empty($segNom)) {
                    $sql .= " and Pacno2 like '" . $segNom . "'  ";
                }
                $sql .= " order by  Egrhis  ";


                $res = mysql_query($sql, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "_000108 con la " . $wbasedato . "_000100  " . mysql_errno() . " - Error en el query $sql - " . mysql_error()));
                if ($res) {
                    $num = mysql_num_rows($res);

                    if ($num == 0) {

                        $sql = "select Pacced as Pacdoc, Pactid as Pactdo, Pacap1, Pacap2, Pacno1, Pacno2, Orihis as Pachis, '' as Pacact, Egring, Pacsex, Pacnac as Pacfna
                                    from " . $wbasedato . "_000108, root_000036 a, root_000037
                                    where Pactid = Oritid
                                      and Pacced = Oriced
                                      and Egrhis = Orihis
                                    ";
                        if (!empty($historia)) {
                            $sql .= " and Egrhis = '" . $historia . "' ";
                        }
                        if (!empty($ingreso)) {
                            $sql .= " and Egring = '" . $ingreso . "'  ";
                        }
                        if (!empty($documento)) {
                            $sql .= " and Pacced = '" . $documento . "'  ";
                        }
                        if (!empty($priApe)) {
                            $sql .= " and Pacap1 like '" . $priApe . "'  ";
                        }
                        if (!empty($segApe)) {
                            $sql .= " and Pacap2 like '" . $segApe . "'  ";
                        }
                        if (!empty($priNom)) {
                            $sql .= " and Pacno1 like '" . $priNom . "'  ";
                        }
                        if (!empty($segNom)) {
                            $sql .= " and Pacno2 like '" . $segNom . "'  ";
                        }
                        $sql .= " order by  Egrhis  ";
                        $res = mysql_query($sql, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "_000108 con la " . $wbasedato . "_000036  " . mysql_errno() . " - Error en el query $sql - " . mysql_error()));
                        if ($res) {
                            $num = mysql_num_rows($res);
                        }
                    }
                    $data['numRegistrosPac'] = $num;
                    if ($num > 0) {
                        /*se inicializa la i en el for de la consulta de la 100 pero se incrementa en el for de la
                            consulta de la 101
                            */
                        for ($i = 0, $j = 0; $rows = mysql_fetch_array($res, MYSQL_ASSOC); $j++) { //solo se puede buscar por el nombre del campo

                            //posicion de historia
                            $data['numPosicionHistorias'][$rows['Pachis']] = $j;

                            foreach ($rows as $key => $value) {
                                //se guarda en data con el prefijo pac_ y empezando en la posicion 3 hasta el final
                                $data['infopac']["pac_" . substr($key, 3)] = utf8_encode($value);
                            }

                            /***busqueda del paciente en la tabla de ingreso 101***/

                            $sql1 = "select Egrhis,Egring,Egrmei,Egrdxi,Egrfee,Egrhoe,Egrest,Egrcae,Egrmee,Egrmet,Egrcex,Egrtdp,Egrcom,Egrfia,Egrfta,Egruex,
                                              Inghis,Ingnin,Ingfei,Inghin,Ingcai,Ingdig,Ingcem,Egrobg
                                            from " . $wbasedato . "_000101," . $wbasedato . "_000108
                                            where Inghis !='0'
                                            and Ingnin= Egring
                                            and Egrhis = Inghis";
                            if (!empty($rows['Pachis'])) {
                                $sql1 .= " and Inghis='" . $rows['Pachis'] . "' ";
                                $sql1 .= " and Ingnin='" . $rows['Egring'] . "' ";
                            }
                            //echo $sql1;
                            // $data['mensaje']=$sql."-".$sql1 ;
                            $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000101, con la " . $wbasedato . "000108" . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                            if ($res1) {
                                $num1 = mysql_num_rows($res1);

                                if ($num1 == 0 && $aplicacion != "") {

                                    $sql1 = "select Egrhis,Egring,Egrmei,Egrdxi,Egrfee,Egrhoe,Egrest,Egrcae,Egrmee,Egrmet,Egrcex,Egrtdp,Egrcom,Egrfia,Egrfta,Egruex,
                                                            Inghis,Inging as Ingnin,a.Fecha_data as Ingfei,a.Hora_data as Inghin, '' as Ingcai, '' as Ingdig, Ingres as Ingcem,Egrobg
                                                    from " . $aplicacion . "_000016 a, " . $wbasedato . "_000108
                                                    where Inghis !='0'
                                                    and Inging= Egring
                                                    and Egrhis = Inghis";
                                    if (!empty($rows['Pachis'])) {
                                        $sql1 .= " and Inghis='" . $rows['Pachis'] . "' ";
                                        $sql1 .= " and Inging='" . $rows['Egring'] . "' ";
                                    }
                                    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000101 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                    if ($res1) {
                                        $num1 = mysql_num_rows($res1);
                                    }
                                }

                                if ($num1 > 0) {
                                    for ($i; $rows1 = mysql_fetch_array($res1, MYSQL_ASSOC); $i++)  //solo se puede buscar por el nombre del campo
                                    {
                                        $data['numRegistrosIng'][$rows['Pachis']] = 1;//max( $data['numRegistrosIng'][ $rows['Pachis'] ], $rows1['Egring'] );
                                        $data['infoing'][$i] = $data['infopac'];

                                        foreach ($rows1 as $key => $value) {
                                            //se guarda en data con el prefijo ing_ y empezando en la posicion 3 hasta el final
                                            $data['infoing'][$i]["ing_" . substr($key, 3)] = utf8_encode($value);
                                        }

                                        $data['infoing'][$i]['egr_obg'] = utf8_encode($rows1['Egrobg']);
                                        $data['infoing'][$i]['egr_cexselCauExt'] = $rows1['Ingcai'];
                                        $data['infoing'][$i]['egr_fiatxtFecInA'] = $rows1['Ingfei'];
                                        $data['infoing'][$i]['egr_histxtNumHis'] = $rows1['Inghis'];
                                        $data['infoing'][$i]['egr_ingtxtNumIng'] = $rows1['Ingnin'];
                                        $data['infoing'][$i]['egr_caeselCauEgr'] = $rows1['Egrcae'];
                                        $data['infoing'][$i]['egr_caeselCauEgr_text'] = consultarNombreOpcionEgreso($data['infoing'][$i]['egr_caeselCauEgr']);
                                        $data['infoing'][$i]['egr_tdpselTipDiP'] = $rows1['Egrtdp'];
                                        $data['infoing'][$i]['egr_uexradUbiExp'] = $rows1['Egruex']; //ubicacion del expediente revisar
                                        $data['infoing'][$i]['egr_comradCon'] = $rows1['Egrcom']; //si tiene complicaciones
                                        $medicoDeEgreso = $rows1['Egrmee'];
                                        $medicoTratante = $rows1['Egrmet'];
                                        $medicoDeIngreso = $rows1['Egrmei'];
                                        $data['infoing'][$i]['pac_epstxtEps'] = $rows1['Ingcem'];
                                        $data['infoing'][$i]['pac_epshidEps'] = $rows1['Ingcem'];
                                        $data['infoing'][$i]['pac_edatxtEdad'] = "" . calcularEdad($rows['Pacfna']);

                                        $data['infoing'][$i]['egr_fee'] = $rows1['Egrfee'];
                                        $data['infoing'][$i]['egr_hoe'] = $rows1['Egrhoe'];


                                        if (!empty($rows1['Ingcem'])) {
                                            $res4 = consultaNombreEntidad($rows1['Ingcem']);
                                            if ($res4) {
                                                $num4 = mysql_num_rows($res4);
                                                if ($num4 > 0) {
                                                    $rows4 = mysql_fetch_array($res4); //se cambio el campo por el de egreso
                                                    $data['infoing'][$i]['pac_epstxtEps'] = utf8_encode($rows4['Codigo'] . "-" . $rows4['Descripcion']);
                                                }
                                            } else {
                                                $data['error'] = 1;
                                                $data['mensaje'] = "No se ejecuto la consulta de busqueda del codigo de la empresa";
                                            }
                                        }

                                        /**Busqueda de diagnosticos**/
                                        if (!empty($rows1['Egrhis']) && !empty($rows1['Egring']) && $data['error'] == 0) {
                                            $sqldia = "select Diahis,Diaing,Diacod,Diatip,Dianue,Diainf,Diacom,Diaegr,Diamed,Diaesm,Diacbm
                                                        from " . $wbasedato . "_000109
                                                        where Diahis = '" . $rows1['Egrhis'] . "'
                                                        and Diaing = '" . $rows1['Egring'] . "'
                                                        order by Diatip
                                                        ";

                                            $resdia = mysql_query($sqldia, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "0000109 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                            if ($resdia) {
                                                $numdia = mysql_num_rows($resdia);
                                                if ($numdia > 0) {
                                                    for ($k = 0; $rowsdia = mysql_fetch_array($resdia); $k++) {
                                                        if ($rowsdia['Diainf'] == "S")
                                                            $rowsdia['Diainf'] = "on";
                                                        if ($rowsdia['Diainf'] == "N")
                                                            $rowsdia['Diainf'] = "off";
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_cod'] = $rowsdia['Diacod'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_cod'] = $rowsdia['Diacod'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_tip'] = $rowsdia['Diatip'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_nue'] = $rowsdia['Dianue'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_inf'] = $rowsdia['Diainf'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_com'] = $rowsdia['Diacom'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_egr'] = $rowsdia['Diaegr'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_med'] = $rowsdia['Diamed'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_esm'] = $rowsdia['Diaesm'];
                                                        $data['infoing'][$i]['diagnosticos'][$k]['dia_cbm'] = $rowsdia['Diacbm'];
                                                        //$data[ 'infoing' ][$i]['diagnosticos'][$k]['dia_ser'] = $rowsdia['Diaser'];

                                                        /*nombres de los diagnosticos*/
                                                        $res5 = consultaNombreImpDiag($rowsdia['Diacod']);
                                                        if ($res5) {
                                                            $num5 = mysql_num_rows($res5);
                                                            if ($num5 > 0) {
                                                                $rows5 = mysql_fetch_array($res5);
                                                                $data['infoing'][$i]['diagnosticos'][$k]['DesDia'] = utf8_encode($rows5['Descripcion']);
                                                            } else {
                                                                //$data[ 'error' ] = 1;
                                                                $data['mensaje'] = "No se encontro el codigo del diagnostico";
                                                            }
                                                        } else {
                                                            $data['error'] = 1;
                                                            $data['mensaje'] = "No se ejecuto la consulta de diagnosticos";
                                                        }
                                                        /*fin nombres de los diagnosticos*/

                                                        /*Medico y especialidad*/
                                                        if (!empty($rowsdia['Diamed'])) {
                                                            if ($aplicacion != "") //cliame
                                                            {
                                                                //medico ingreso
                                                                $res4 = consultaNombreMedicos2($rowsdia['Diamed'], $aplicacion);
                                                                if ($res4['n_medico'] != "") {
                                                                    $data['infoing'][$i]['diagnosticos'][$k]['DesMed'] = utf8_decode($res4['n_medico']);
                                                                } else {
                                                                    $data['mensaje'] = "No se encontro el codigo del medico";
                                                                }
                                                            }
                                                        }
                                                        if (!empty($rowsdia['Diaesm'])) {
                                                            //if ($aplicacion != "") //cliame
                                                            //{
                                                            $res4 = consultaNombreEspe($rowsdia['Diaesm'], $aplicacion);
                                                            if ($res4) {
                                                                $num4 = mysql_num_rows($res4);
                                                                if ($num4 > 0) {
                                                                    $rows4 = mysql_fetch_array($res4);
                                                                    $data['infoing'][$i]['diagnosticos'][$k]['Desesm'] = $rows4['Espnom'];
                                                                } else {
                                                                    $data['mensaje'] = "No se encontro el nombre de la especialidad";
                                                                }
                                                            } else {
                                                                $data['mensaje'] = "No se encontro el nombre de la especialidad";
                                                            }
                                                            //}
                                                        }
                                                        //$data[ 'infoing' ][$i]['diagnosticos'][$k][ 'Desesm' ] = "gato";
                                                        /*$res5=consultaNombreServ($rowsdia['Diaser'], $aplicacion);
                                                                if ($res5)
                                                                {
                                                                    $num5=mysql_num_rows($res5);
                                                                    if ($num5>0)
                                                                    {
                                                                        $rows5=mysql_fetch_array($res5);
                                                                        if ($aplicacion == "")
                                                                        {
                                                                            $data[ 'infoing' ][$i]['diagnosticos'][$k]['diaSer'] = $rowsdia['Diaser']."-".$rows5['Ccodes'];
                                                                        }
                                                                        else
                                                                        {
                                                                            $data[ 'infoing' ][$i]['diagnosticos'][$k]['diaSer'] = $rowsdia['Diaser']."-".$rows5['Cconom'];
                                                                        }
                                                                    }else{
                                                                        //$data['mensaje']="No se encontro el nombre del servicio del diagnostico";
                                                                    }
                                                                }*/
                                                        /*FIN Medico y especialidad*/

                                                        //-->
                                                        $sqlSerDia = " SELECT Sedser
                                                                                  FROM {$wbasedato}_000238
                                                                                 WHERE Sedhis = '{$rows1['Egrhis']}'
                                                                                   AND Seding = '{$rows1['Egring']}'
                                                                                   AND Seddia = '{$rowsdia['Diacod']}'";
                                                        $resSerDia = mysql_query($sqlSerDia, $conex);
                                                        /*$data['mensaje'] = $sqlSerDia;
                                                                json_encode($data);*/
                                                        for ($l = 0; $rowsSerDia = mysql_fetch_array($resSerDia); $l++) {
                                                            $data['infoing'][$i]['diagnosticos'][$k]['servicios'][$l]['Sed_ser'] = $rowsSerDia['Sedser'];
                                                        }
                                                    }
                                                } else {
                                                    // $data[ 'error' ] = 1;
                                                    $data['mensaje'] = "No se encontraron diagnosticos asociados a la historia " . $historia . " e ingreso " . $ingreso . "";
                                                }
                                            } else {
                                                $data['error'] = 1;
                                                $data['mensaje'] = "No se ejecuto la consulta de busqueda de las diagnosticos";
                                            }
                                        }
                                        /**Fin busqueda diagnosticos**/

                                        /**Busqueda de procedimientos**/
                                        if (!empty($rows1['Egrhis']) && !empty($rows1['Egring']) && $data['error'] == 0) {
                                            $sqlpro = "select Prohis,Proing,Procod,Protip,Promed,Proesm,Proane,Proqui,Profec, Proser
                                                        from " . $wbasedato . "_000110
                                                        where Prohis = '" . $rows1['Egrhis'] . "'
                                                        and Proing = '" . $rows1['Egring'] . "'
                                                        order by Protip";

                                            $respro = mysql_query($sqlpro, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000110 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                            if ($respro) {
                                                $numpro = mysql_num_rows($respro);
                                                if ($numpro > 0) {
                                                    for ($k = 0; $rowspro = mysql_fetch_array($respro); $k++) {
                                                        $data['infoing'][$i]['procedimientos'][$k]['pro_cod'] = $rowspro['Procod'];
                                                        $data['infoing'][$i]['procedimientos'][$k]['pro_tip'] = $rowspro['Protip'];
                                                        $data['infoing'][$i]['procedimientos'][$k]['pro_med'] = $rowspro['Promed'];
                                                        $data['infoing'][$i]['procedimientos'][$k]['pro_esm'] = $rowspro['Proesm'];
                                                        $data['infoing'][$i]['procedimientos'][$k]['pro_ane'] = $rowspro['Proane'];
                                                        $data['infoing'][$i]['procedimientos'][$k]['pro_qui'] = $rowspro['Proqui'];
                                                        $data['infoing'][$i]['procedimientos'][$k]['pro_fec'] = $rowspro['Profec'];
                                                        $data['infoing'][$i]['procedimientos'][$k]['pro_ser'] = $rowspro['Proser'];

                                                        /*nombres de los procedimientos*/
                                                        $res5 = consultaNombreProce($rowspro['Procod']);
                                                        if ($res5) {
                                                            $num5 = mysql_num_rows($res5);
                                                            if ($num5 > 0) {
                                                                $rows5 = mysql_fetch_array($res5);
                                                                $data['infoing'][$i]['procedimientos'][$k]['ProDes'] = utf8_encode($rows5['Pronom']);
                                                            } else {
                                                                // $data[ 'error' ] = 1;
                                                                $data['mensaje'] = "No se encontro el codigo del procedimiento ";
                                                            }
                                                        } else {
                                                            $data['error'] = 1;
                                                            $data['mensaje'] = "No se ejecuto la consulta de procedimientos";
                                                        }
                                                        /*fin nombres de los procedimientos*/

                                                        /*Medico y especialidad*/
                                                        if (!empty($rowspro['Promed'])) {
                                                            if ($aplicacion != "") //cliame
                                                            {
                                                                //medico ingreso
                                                                $res4 = consultaNombreMedicos2($rowspro['Promed'], $aplicacion);
                                                                if ($res4['n_medico'] != "") {
                                                                    $data['infoing'][$i]['procedimientos'][$k]['DesMed'] = utf8_encode($res4['n_medico']);
                                                                } else {
                                                                    $data['mensaje'] = "No se encontro el codigo del medico";
                                                                }
                                                            }
                                                        }
                                                        if (!empty($rowspro['Proesm'])) {
                                                            //if ($aplicacion != "") //cliame
                                                            //{
                                                            $res4 = consultaNombreEspe($rowspro['Proesm'], $aplicacion);
                                                            if ($res4) {
                                                                $num4 = mysql_num_rows($res4);
                                                                if ($num4 > 0) {
                                                                    $rows4 = mysql_fetch_array($res4);
                                                                    $data['infoing'][$i]['procedimientos'][$k]['Desesm'] = $rows4['Espnom'];
                                                                } else {
                                                                    $data['mensaje'] = "No se encontro el nombre de la especialidad";
                                                                }
                                                            } else {
                                                                $data['mensaje'] = "No se encontro el nombre de la especialidad";
                                                            }
                                                            //}
                                                        }
                                                        /*FIN Medico y especialidad*/
                                                        /*Anestesiologo*/
                                                        if (!empty($rowspro['Proane'])) {
                                                            if ($aplicacion != "") //cliame
                                                            {
                                                                //medico ingreso
                                                                $res4 = consultaNombreMedicos2($rowspro['Proane'], $aplicacion);
                                                                if ($res4['n_medico'] != "") {
                                                                    $data['infoing'][$i]['procedimientos'][$k]['DesAne'] = utf8_encode($res4['n_medico']);
                                                                } else {
                                                                    $data['mensaje'] = "No se encontro el codigo del medico";
                                                                }
                                                            }
                                                        }
                                                        $res5 = consultaNombreServ($rowspro['Proser'], $aplicacion);
                                                        if ($res5) {
                                                            $num5 = mysql_num_rows($res5);
                                                            if ($num5 > 0) {
                                                                $rows5 = mysql_fetch_array($res5);
                                                                if ($aplicacion == "") {
                                                                    $data['infoing'][$i]['procedimientos'][$k]['proSer'] = $rowspro['Proser'] . "-" . $rows5['Ccodes'];
                                                                } else {
                                                                    $data['infoing'][$i]['procedimientos'][$k]['proSer'] = $rowspro['Proser'] . "-" . $rows5['Cconom'];
                                                                }
                                                            } else {
                                                                //$data['mensaje']="No se encontro el nombre del servicio del diagnostico";
                                                            }
                                                        }
                                                        /*FIN Medico y especialidad*/
                                                    }
                                                } else {
                                                    // $data[ 'error' ] = 1;
                                                    $data['mensaje'] = "No se encontraron procedimientos asociados a la historia " . $historia . " e ingreso " . $ingreso . "";
                                                }
                                            } else {
                                                $data['error'] = 1;
                                                $data['mensaje'] = "No se ejecuto la consulta de busqueda de las procedimientos";
                                            }
                                        }
                                        /**Fin busqueda procedimientos**/

                                        /**Busqueda de especialidades**/

                                        if (!empty($rows1['Egrhis']) && !empty($rows1['Egring']) && $data['error'] == 0) {
                                            $sqlesp = "select Esphis,Esping,Espcod,Esptip,Espmed
                                                        from " . $wbasedato . "_000111
                                                        where Esphis = '" . $rows1['Egrhis'] . "'
                                                        and Esping = '" . $rows1['Egring'] . "'
                                                        order by Esptip
                                                        ";

                                            $resesp = mysql_query($sqlesp, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000111 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                            if ($resesp) {
                                                $numesp = mysql_num_rows($resesp);
                                                if ($numesp > 0) {
                                                    for ($k = 0; $rowsesp = mysql_fetch_array($resesp); $k++) {
                                                        $data['infoing'][$i]['especialidades'][$k]['esp_cod'] = $rowsesp['Espcod'];
                                                        $data['infoing'][$i]['especialidades'][$k]['esp_tip'] = $rowsesp['Esptip'];
                                                        $data['infoing'][$i]['especialidades'][$k]['esp_med'] = $rowsesp['Espmed'];
                                                        //$data[ 'infoing' ][$i]['especialidades'][$k]['esp_ser'] = $rowsesp['Espser'];

                                                        //Para chekear el medico de egreso que consulto de cliame_000101
                                                        if ($rowsesp['Espmed'] == $medicoDeEgreso)
                                                            $data['infoing'][$i]['especialidades'][$k]['med_egrradio'] = "on";

                                                        //Para chekear el medico tratante que consulto de cliame_000101 2014-07-30
                                                        if ($rowsesp['Espmed'] == $medicoTratante)
                                                            $data['infoing'][$i]['especialidades'][$k]['med_traradio'] = "on";

                                                        if ($rowsesp['Espmed'] == $medicoDeIngreso)
                                                            $data['infoing'][$i]['especialidades'][$k]['med_meiradio'] = "on";
                                                        /*nombres de las especialidades*/
                                                        $res5 = consultaNombreEspe($rowsesp['Espcod'], $aplicacion);
                                                        if ($res5) {
                                                            $num5 = mysql_num_rows($res5);
                                                            if ($num5 > 0) {
                                                                $rows5 = mysql_fetch_array($res5);
                                                                $data['infoing'][$i]['especialidades'][$k]['DesEsp'] = $rows5['Espnom'];
                                                            } else {
                                                                //$data[ 'error' ] = 1;
                                                                $data['mensaje'] = "No se encontro el nombre de la especialidad";
                                                            }
                                                        } else {
                                                            $data['error'] = 1;
                                                            $data['mensaje'] = "No se ejecuto la consulta de especialidades ";
                                                        }
                                                        /*fin nombres de las especialidades*/

                                                        /*Medico y especialidad*/
                                                        if (!empty($rowsesp['Espmed'])) {
                                                            if ($aplicacion != "") //cliame
                                                            {
                                                                //medico ingreso
                                                                $res4 = consultaNombreMedicos2($rowsesp['Espmed'], $aplicacion);
                                                                if ($res4['n_medico'] != "") {
                                                                    $data['infoing'][$i]['especialidades'][$k]['DesMed'] = utf8_encode($res4['n_medico']);
                                                                } else {
                                                                    $data['mensaje'] = "No se encontro el codigo del medico";
                                                                }
                                                            }
                                                        }
                                                        $sqlSerEsp = " SELECT Seeser
                                                                                  FROM {$wbasedato}_000239
                                                                                 WHERE Seehis = '{$rows1['Egrhis']}'
                                                                                   AND Seeing = '{$rows1['Egring']}'
                                                                                   AND Seeesp = '{$rowsesp['Espcod']}'
                                                                                   AND Seemed = '{$rowsesp['Espmed']}'";
                                                        $resSerEsp = mysql_query($sqlSerEsp, $conex);
                                                        /*$data['mensaje'] = $sqlSerDia;
                                                                json_encode($data);*/
                                                        for ($l = 0; $rowsSerEsp = mysql_fetch_array($resSerEsp); $l++) {
                                                            $data['infoing'][$i]['especialidades'][$k]['servicios'][$l]['See_ser'] = $rowsSerEsp['Seeser'];
                                                        }
                                                        /*$res5=consultaNombreServ($rowsesp['Espser'], $aplicacion);
                                                                if ($res5)
                                                                {
                                                                    $num5=mysql_num_rows($res5);
                                                                    if ($num5>0)
                                                                    {
                                                                        $rows5=mysql_fetch_array($res5);
                                                                        if ($aplicacion == "")
                                                                        {
                                                                            $data[ 'infoing' ][$i]['especialidades'][$k]['espSer'] = $rowsesp['Espser']."-".$rows5['Ccodes'];
                                                                        }
                                                                        else
                                                                        {
                                                                            $data[ 'infoing' ][$i]['especialidades'][$k]['espSer'] = $rowsesp['Espser']."-".$rows5['Cconom'];
                                                                        }
                                                                    }else{
                                                                        //$data['mensaje']="No se encontro el nombre del servicio del diagnostico";
                                                                    }
                                                                }*/
                                                        /*FIN Medico y especialidad*/

                                                    }
                                                } else {
                                                    // $data[ 'error' ] = 1;
                                                    $data['mensaje'] = "No se encontraron especialidades asociadas a la historia " . $historia . " e ingreso " . $ingreso . "";
                                                }

                                            } else {
                                                $data['error'] = 1;
                                                $data['mensaje'] = "No se ejecuto la consulta de busqueda de las especialidades ";
                                            }
                                        }
                                        /**Fin busqueda especialidades**/


                                        /**Busqueda de servicios**/

                                        if (!empty($rows1['Egrhis']) && !empty($rows1['Egring']) && $data['error'] == 0) {
                                            $sqlser = "select Serhis,Sering,Sercod,Seregr
                                                        from " . $wbasedato . "_000112
                                                        where Serhis = '" . $rows1['Egrhis'] . "'
                                                        and Sering = '" . $rows1['Egring'] . "'";

                                            $resser = mysql_query($sqlser, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000112 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                            if ($resser) {
                                                $numser = mysql_num_rows($resser);
                                                if ($numser > 0) {
                                                    for ($k = 0; $rowsser = mysql_fetch_array($resser); $k++) {
                                                        $data['infoing'][$i]['servicios'][$k]['ser_cod'] = $rowsser['Sercod'];
                                                        $data['infoing'][$i]['servicios'][$k]['ser_egrradio'] = $rowsser['Seregr'];

                                                        /*nombres de los servicios*/
                                                        $res5 = consultaNombreServ($rowsser['Sercod'], $aplicacion);
                                                        if ($res5) {
                                                            $num5 = mysql_num_rows($res5);
                                                            if ($num5 > 0) {
                                                                $rows5 = mysql_fetch_array($res5);
                                                                if ($aplicacion == "") {
                                                                    $data['infoing'][$i]['servicios'][$k]['DesSer'] = $rows5['Ccodes'];
                                                                } else {
                                                                    $data['infoing'][$i]['servicios'][$k]['DesSer'] = $rows5['Cconom'];
                                                                }
                                                            } else {
                                                                $data['error'] = 1;
                                                                $data['mensaje'] = "No se encontro el codigo del servicio";
                                                            }

                                                        } else {
                                                            $data['error'] = 1;
                                                            $data['mensaje'] = "No se ejecuto la consulta de servicios ";
                                                        }
                                                        /*fin nombres de las servicios*/

                                                    }
                                                } else {
                                                    // $data[ 'error' ] = 1;
                                                    $data['mensaje'] = "No se encontraron servicios asociados a la historia " . $historia . " e ingreso " . $ingreso . "";
                                                }

                                            } else {
                                                $data['error'] = 1;
                                                $data['mensaje'] = "No se ejecuto la consulta de busqueda de los servicios";
                                            }
                                        }
                                        /**Fin busqueda servicios**/

                                        /** AUTORIZACIONES**/
                                        $sqlser = "select Authis,Auting,Autobs,Autinf
                                                        from " . $wbasedato . "_000219
                                                        where Authis = '" . $rows1['Egrhis'] . "'
                                                        and Auting = '" . $rows1['Egring'] . "'
                                                        and Autest = 'on'";

                                        $resser = mysql_query($sqlser, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000112 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                        if ($resser) {
                                            $numser = mysql_num_rows($resser);
                                            if ($numser > 0) {
                                                $rowsser = mysql_fetch_array($resser);

                                                $data['infoing'][$i]['aut_obs'] = utf8_encode($rowsser['Autobs']);
                                                $data['infoing'][$i]['aut_inf'] = $rowsser['Autinf'];

                                            }
                                        }
                                        /** FIN AUTORIZACIONES**/


                                        /**Busqueda de personas autorizadas**/

                                        if (!empty($rows1['Egrhis']) && !empty($rows1['Egring']) && $data['error'] == 0) {
                                            $sqlser = "select Dauhis,Dauing,Dautdo,Daudoc,Daunom,Daupar
                                                        from " . $wbasedato . "_000220
                                                        where Dauhis = '" . $rows1['Egrhis'] . "'
                                                        and Dauing = '" . $rows1['Egring'] . "'
                                                        and Dautip = '1'
                                                        and Dauest = 'on' ";

                                            $resser = mysql_query($sqlser, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000112 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                            if ($resser) {
                                                $numser = mysql_num_rows($resser);
                                                if ($numser > 0) {
                                                    for ($k = 0; $rowsser = mysql_fetch_array($resser); $k++) {
                                                        $data['infoing'][$i]['personasautorizadas'][$k]['dau_tdo'] = $rowsser['Dautdo'];
                                                        $data['infoing'][$i]['personasautorizadas'][$k]['dau_doc'] = $rowsser['Daudoc'];
                                                        $data['infoing'][$i]['personasautorizadas'][$k]['dau_nom'] = utf8_encode($rowsser['Daunom']);
                                                        $data['infoing'][$i]['personasautorizadas'][$k]['dau_par'] = $rowsser['Daupar'];
                                                    }
                                                } else {
                                                    // $data[ 'error' ] = 1;
                                                    //$data['mensaje']="No se encontraron servicios asociados a la historia ".$historia." e ingreso ".$ingreso."";
                                                }

                                            } else {
                                                $data['error'] = 1;
                                                $data['mensaje'] = "No se ejecuto la consulta de busqueda de los servicios";
                                            }
                                        }
                                        /**Fin busqueda personas autorizadas**/

                                        /**Busqueda de personas que reclaman**/

                                        if (!empty($rows1['Egrhis']) && !empty($rows1['Egring']) && $data['error'] == 0) {
                                            $sqlser = "select Dauhis,Dauing,Dautdo,Daudoc,Daunom,Daupar
                                                        from " . $wbasedato . "_000220
                                                        where Dauhis = '" . $rows1['Egrhis'] . "'
                                                        and Dauing = '" . $rows1['Egring'] . "'
                                                        and Dautip = '2'
                                                        and Dauest = 'on' ";

                                            $resser = mysql_query($sqlser, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $wbasedato . "000112 " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));
                                            if ($resser) {
                                                $numser = mysql_num_rows($resser);
                                                if ($numser > 0) {
                                                    for ($k = 0; $rowsser = mysql_fetch_array($resser); $k++) {
                                                        $data['infoing'][$i]['personasreclaman'][$k]['dau_tdo'] = $rowsser['Dautdo'];
                                                        $data['infoing'][$i]['personasreclaman'][$k]['dau_doc'] = $rowsser['Daudoc'];
                                                        $data['infoing'][$i]['personasreclaman'][$k]['dau_nom'] = utf8_encode($rowsser['Daunom']);
                                                        $data['infoing'][$i]['personasreclaman'][$k]['dau_par'] = $rowsser['Daupar'];
                                                    }
                                                } else {
                                                    // $data[ 'error' ] = 1;
                                                    //$data['mensaje']="No se encontraron servicios asociados a la historia ".$historia." e ingreso ".$ingreso."";
                                                }

                                            } else {
                                                $data['error'] = 1;
                                                $data['mensaje'] = "No se ejecuto la consulta de busqueda de los servicios";
                                            }
                                        }
                                        /**Fin busqueda personas que reclaman**/


                                    }//for 101


                                }//$num1>0
                                else {
                                    $data['error'] = 1;
                                    $data['mensaje'] = "No se encontraron registros del ingreso para los datos ingresados";
                                }
                            } else {
                                $data['error'] = 1;
                            }
                            /***fin busqueda en la tabla 101***/
                        } //fin for 100

                    } //si trae registros de la 100
                    else {
                        $data['mensaje'] = "No se encontro informacion para los datos ingresados o el paciente se encuentra activo";
                    }
                } else {
                    $data['error'] = 1;
                }
            } else //no se ejecuto la consulta de la 108
            {
                $data["error"] = 1;
                $data["mensaje"] = "No se encontraron datos para realizar la busqueda ";

            }
            /***fin busqueda en la tabla 100***/
            if ($mostrarSalida == "on")
                echo "<pre>" . print_r($data, true) . "</pre>";
            echo json_encode($data);
            return;

        /**fin datos almacenados despues del egreso**/
        case 'anularEgreso':

            //Consulto que el paciente este inactivo
            $sql = "SELECT
                        *
                    FROM
                        " . $wbasedato . "_000100
                    WHERE
                        pachis = '$historia'
                        AND pacact = 'off'
                    ";

            $res = mysql_query($sql, $conex) or ($data['mensaje'] = mysql_errno() . " - Error en el query $sql - " . mysql_error());

            if ($res) {

                $tieneConexionUnix = consultarAliasPorAplicacion($conex, $wemp_pmla, 'conexionUnix');
                //$tieneConexionUnix = 'off';
                if ($hay_unix && $tieneConexionUnix == 'on') //se descomento
                {
                    $a = new egreso_erp();
                    $a->anularEgreso($historia, $ingreso);
                    if ($a->data['error'] == 1) //si hay errores anulando en unix
                    {
                        echo json_encode($a->data);
                        return;
                    }

                    if ($a->data['error'] == 8) {
                        $data['error'] = $a->data['error'];
                        $data['mensaje'] = utf8_encode("En este momento no puede anular el egreso en unix, por favor intentelo luego o comuniquese con informatica.");
                        echo json_encode($data);
                        return;
                    }
                } else {
                    if ($a->data['error'] == 8) {
                        $a->data['mensaje'] = utf8_encode("En este momento no puede anular el egreso en unix, por favor intentelo luego o comuniquese con informatica.");
                        echo json_encode($data);
                        return;
                    }
                }

                //Desactivo el paciente en la tabla de egreso de pacientes (108)
                $sql = "UPDATE
                            " . $wbasedato . "_000108
                        SET
                            egract = 'off'
                        WHERE
                            egrhis = '$historia'
                            AND egring = '$ingreso'
                            AND egract = 'on'
                        ";

                $resUptEgr = mysql_query($sql, $conex) or ($data['mensaje'] = mysql_errno() . " - Error en el query $sql - " . mysql_error());


                if ($resUptEgr) {

                    //Activo el paciente en la tabla de datos demograficos del paciente (000100)
                    $sql = "UPDATE
                                " . $wbasedato . "_000100
                            SET
                                pacact = 'on'
                            WHERE
                                pachis = '$historia'
                                AND pacact = 'off'
                            ";

                    $resUptEgr = mysql_query($sql, $conex) or ($data['mensaje'] = mysql_errno() . " - Error en el query $sql - " . mysql_error());

                    if (!$resUptEgr) {
                        $data['mensaje'] = 1;
                    }
                } else {
                    $data['mensaje'] = 1;
                }

                //Activo el paciente clinicamente ( movhos_000018 )2016-12-14
                $wmovhos = consultarAplicacion($conex, $wemp_pmla, "movhos");
                $sql = "UPDATE {$wmovhos}_000018
                           SET ubiald = 'off'
                        WHERE ubihis = '{$historia}'
                          AND ubiing = '{$ingreso}'";

                $resAld = mysql_query($sql, $conex) or ($data['mensaje'] = mysql_errno() . " - Error en el query $sql - " . mysql_error());

                if (!$resAld) {
                    $data['mensaje'] = 1;
                } else {
                    $data['mensaje'] = 1;
                }
            } else {
                $data['error'] = 1;
            }

            if ($data['error'] == 0) {
                logEgreso('Egreso anulado', $historia, $ingreso, "");
            }
            if ($data['error'] == 0) {
                $data['mensaje'] = "Egreso anulado correctamente";
            }

            echo json_encode($data);
            break;

        case 'validarCirugiaSinLiquidar':
            $data = array();
            $data['respuesta'] = 'no';
            $wbasedato_tcx = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
            $sql = "SELECT  count(*) cirugiasSinLiquidar
                       FROM  {$wbasedato}_000207 AS c207
                             LEFT JOIN
                             {$wbasedato_tcx}_000011 AS tcx11 ON (tcx11.Turtur = c207.Mpatur)
                             LEFT JOIN
                             {$wbasedato_tcx}_000007 AS tcx7 ON ( tcx7.Mcatur = c207.Mpatur )
                       WHERE c207.Mpahis = '{$historia}'
                         AND c207.Mpaing = '{$ingreso}'
                         AND c207.Mpaest = 'on'
                         AND c207.Mpaliq <> 'on'
                         AND c207.Mpalux <> 'on'
                         AND (c207.Mpacan - c207.Mpadev) > 0
                         AND tcx7.id is null
                       GROUP BY c207.Mpatur
                       ORDER BY tcx11.Turfec, tcx11.Turhin";
            /*$data['respuesta'] = $sql;
             echo json_encode($data);
             break;*/
            $rs = mysql_query($sql, $conex);
            while ($row = mysql_fetch_assoc($rs)) {
                if ($row['cirugiasSinLiquidar'] > 0) {
                    $data['respuesta'] = "si";
                }
            }
            echo json_encode($data);
            break;

        default:
            break;
    }
    return;
}

?>
<html lang="es-ES">
<!DOCTYPE html>
<head>
    <title>Egreso de Pacientes</title>
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">

    <!--<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
    <link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet"/>        <!-- Nucleo jquery -->

    <!--<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>-->
    <!--se debe colocar antes del tooltip.js-->
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet"/> <!-- Tooltip -->
    <link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet"/> <!-- Autocomplete -->
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css"/>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>


    <script src="../../../include/root/toJson.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.easyAccordion.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>

    <script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script> <!-- Autocomplete -->
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script> <!-- buscador -->

    <script src="./funcionesAdmisiones.js" type="text/javascript"></script>

    <script src="../../../include/root/toJson.js"></script>
    <script src="../../../include/root/jquery.validate.js"></script>

    <script type="text/javascript" src="egreso_erp.js"></script>
    <link rel="stylesheet" href="egreso_erp.css">
</head>
<body>
<?php
/*======================================================DOCUMENTACION APLICACION==========================================================================
#99CCFF   #FFFFCC #99FFFF  #CCFFFF  #FFFFCC
APLICACION LA ADMISION DE PACIENTES

1. DESCRIPCION:
Este software se desarrolla para la admision y el ingreso de pacientes en la clinica las americas, clinica del sur y el IDC, la aplicacion
se realiza con las especificaciones necesarias de acuerdo con las normas que son exigidas por el ministerio de salud, este debe validar automanticamente
a que empresa se le esta haciendo la admision, ademas el resto de validaciones que se necesitan.

Este formulario permite el ingreso de los datos del ingreso, personales, del acompañante, del responsable, del pagador, de la autorizacion y otros
datos del ingreso.

/****************************************************************************
* Funciones
*****************************************************************************/
function consultarDiagnosticosPaciente($historia, $ingreso)
{

    global $conex, $aplicacion, $medicosIngEgr, $wemp_pmla;
    $respuesta = array("diagnosticos" => array(), "medicoIngreso" => "", "medicoDeEgreso" => "");
    $diagnosticos = array();

    $query = " SELECT Detval
                 FROM root_000051
                WHERE Detapl = 'formulariosDiagnosticosIngreso'
                  AND Detemp = '{$wemp_pmla}'";

    $rs = mysql_query($query, $conex);
    $rowFi = mysql_fetch_assoc($rs);
    $formsIng = $rowFi['Detval'];
    $formsIng = explode(",", $formsIng);


    $query = " SELECT Diacod, Diacco, Diausu, Diafhc, Diafor, id
                 FROM {$aplicacion}_000272
                WHERE Diahis = '{$historia}'
                  AND Diaing = '{$ingreso}'
                  AND Diaest = 'on'
                 ORDER BY id asc";

    $rs = mysql_query($query, $conex);
    while ($row = mysql_fetch_assoc($rs)) {
        if (!isset($respuesta['diagnosticos'][$row['Diacod']])) {
            $rsNombre = mysql_fetch_assoc(consultaNombreImpDiag($row['Diacod']));
            $respuesta['diagnosticos'][$row['Diacod']] = array(
                "descripcion" => $rsNombre['Descripcion'],
                "centroCostos" => $row['Diacco'],
                "medico" => $row['Diausu'],
                "notificar" => $rsNombre['Notificar']
            );
            if ($respuesta['medicoIngreso'] == "" and in_array($row['Diafor'], $formsIng)) {
                $respuesta['medicoIngreso'] = $row['Diausu'];
            }
        }
        $respuesta['medicoDeEgreso'] = $row['Diausu'];
    }
    return ($respuesta);
}


function consultaMaestros($tabla, $campos, $where, $group, $order, $cant = 1)
{
    global $conex;
    global $wbasedato;
    global $prueba;


    if ($cant == 1) {
        $q = " SELECT " . $campos . "
                    FROM " . $tabla . "";
        if ($where != "") {
            $q .= " WHERE " . $where . "";
        }

    } else {

        $q = " SELECT " . $campos . "
                FROM " . $wbasedato . "_" . $tabla . "";
        if ($where != "") {
            $q .= " WHERE " . $where . "";
        }


    }

    if ($group != "") {
        $q .= "   GROUP BY " . $group . " ";
    }
    if ($order != "") {
        $q .= " ORDER BY " . $order . " ";
    }

    $res1 = mysql_query($q, $conex) or die (" Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
    // $num1 = mysql_num_rows($res1);

    return $res1;
}


/**********************************************************************************
 * Crea un select con el id y name
 **********************************************************************************/
function crearSelectHTMLAcc($res, $id, $name, $style = "", $atributos = "")
{

    $select = "<SELECT id='$id' name='$name' $atributos $style>";
    $select .= "<option value=''>Seleccione...</option>";

    $num = mysql_num_rows($res);

    if ($num > 0) {

        while ($rows = mysql_fetch_assoc($res)) {

            $value = "";
            $des = "";

            $i = 0;
            foreach ($rows as $key => $val) {

                if ($i == 0) {
                    $value = $val;
                } else {
                    $des .= "-" . $val;
                }

                $i++;
            }

            $select .= "<option value='{$value}'>" . substr($des, 1) . "</option>";
        }
    }

    $select .= "</SELECT>";

    return $select;
}


function consultarAplicacion($conexion, $codigoInstitucion, $nombreAplicacion)
{
    $q = " SELECT Detval
             FROM root_000051
            WHERE Detemp = '" . $codigoInstitucion . "'
              AND Detapl = '" . $nombreAplicacion . "'";

    $res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
    $num = mysql_num_rows($res);

    $alias = "";
    if ($num > 0) {
        $rs = mysql_fetch_array($res);
        $alias = $rs['Detval'];
    }
    return $alias;
}

function consultarCC($alias, $where)
{

    global $conex;

    $q = " SELECT Ccocod,Cconom, Ccorel
            FROM " . $alias . "_000011
            WHERE " . $where . "
            order by Cconom";


    $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

    return $res;
}


function consultarMedicos($med, $wbasedato, $aplicacion, $especialidad = "")
{

    global $conex;
    global $wemp_pmla;


    $val = "";
    $data = "";

    $parametroEspecialidades = "FILTRO_ESP_MED_EGRESOS";

    /** Realizar consulta al parámetro de la root_000051 **/
    $filtroEspecialidades = consultarAliasPorAplicacion($conex, $wemp_pmla, $parametroEspecialidades);

    if ($aplicacion == "") {
        //medico
        $and_especialidad = "";
        if ($especialidad != "")
            $and_especialidad = " AND Espcod = '" . $especialidad . "'  AND Espcod NOT IN (" . $filtroEspecialidades . ")";

        $sql = "SELECT Medcod, Mednom,Medesp,Espnom
                    FROM " . $wbasedato . "_000051 LEFT JOIN " . $wbasedato . "_000053 ON (Medesp=Espcod)
                    WHERE (Medcod LIKE '%" . utf8_decode($med) . "%' or Mednom like '%" . utf8_decode($med) . "%')
                    " . $and_especialidad . "
                    AND Espcod NOT IN (" . $filtroEspecialidades . ")
                    AND Medest ='on'
                    ORDER BY Mednom
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Medcod'] = trim(utf8_encode($rows['Medcod']));
                $rows['Mednom'] = trim(utf8_encode($rows['Mednom']));
                $rows['Medesp'] = trim(utf8_encode($rows['Medesp']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                $pos = strpos($rows['Medesp'], "-");
                if ($pos !== false) {
                    $aux = explode("-", $rows['Medesp']);
                    $rows['Medesp'] = $aux[0];
                    $rows['Espnom'] = $aux[1];
                }

                if ($rows['Medesp'] == "") $rows['Espnom'] = "00000";
                if ($rows['Espnom'] == "") $rows['Espnom'] = "SIN DATOS";

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Medcod'], "des" => $rows['Mednom'], "codesp" => $rows['Medesp'], "desesp" => $rows['Espnom']);    //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows[ 'Medcod' ]}-{$rows[ 'Mednom' ]}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";
            }
        }
    } else {

        $med = str_replace(" ", ".*", $med);

        $and_especialidad = "";
        if ($especialidad != "")
            $and_especialidad = " AND Espcod = '" . $especialidad . "'";

        //WHERE (Medno1 LIKE '%".utf8_decode($med)."%' or Meddoc LIKE '%".utf8_decode($med)."%' or Medno2 like '%".utf8_decode($med)."%' or Medap1 LIKE '%".utf8_decode($med)."%' or Medap2 like '%".utf8_decode($med)."%')

        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2,Medesp,Espnom
                    FROM " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
                    WHERE ( concat(Medno1,' ',Medno2,' ',Medap1,' ',Medap2) regexp '" . utf8_decode($med) . "' or Meddoc LIKE '%" . utf8_decode($med) . "%' )
                    " . $and_especialidad . "
                    AND Espcod NOT IN (" . $filtroEspecialidades . ")
                    AND Medest ='on'
                    ORDER BY Medno1,Medno2,Medap1,Medap2
                    LIMIT 30
                    "; //AND Meduma != ''

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);
        if (!isset($data) or trim($data) == "")
            $data = array();

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Meddoc'] = trim(utf8_encode($rows['Meddoc']));
                $rows['Medno1'] = trim(utf8_encode($rows['Medno1']));
                $rows['Medno2'] = trim(utf8_encode($rows['Medno2']));
                $rows['Medap1'] = trim(utf8_encode($rows['Medap1']));
                $rows['Medap2'] = trim(utf8_encode($rows['Medap2']));
                $rows['Medesp'] = trim(utf8_encode($rows['Medesp']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                $pos = strpos($rows['Medesp'], "-");
                if ($pos !== false) {
                    $aux = explode("-", $rows['Medesp']);
                    $rows['Medesp'] = $aux[0];
                    $rows['Espnom'] = $aux[1];
                }

                if ($rows['Medesp'] == "") $rows['Espnom'] = "00000";
                if ($rows['Espnom'] == "") $rows['Espnom'] = "SIN DATOS";

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Meddoc'],
                    "des" => $rows['Medno1'] . " " . $rows['Medno2'] . " " . $rows['Medap1'] . " " . $rows['Medap2'],
                    "codesp" => $rows['Medesp'],
                    "desesp" => $rows['Espnom']);  //Este es el dato a procesar en javascript
                $data['usu'] = $rows['Medno1'] . " " . $rows['Medno2'] . " " . $rows['Medap1'] . " " . $rows['Medap2'];   //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";

            }
        }
    }

    return $val;
}

function consultarMedicoEspecifico($med, $wbasedato, $aplicacion, $especialidad = "")
{

    global $conex;


    $val = "";
    $data = "";

    if ($aplicacion == "") {
        //medico
        $and_especialidad = "";
        if ($especialidad != "")
            $and_especialidad = " AND Espcod = '" . $especialidad . "'";

        $sql = "SELECT Medcod, Mednom,Medesp,Espnom
                    FROM " . $wbasedato . "_000051 LEFT JOIN " . $wbasedato . "_000053 ON (Medesp=Espcod)
                    WHERE Medcod = '" . utf8_decode($med) . "'
                    " . $and_especialidad . "
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Medcod'] = trim(utf8_encode($rows['Medcod']));
                $rows['Mednom'] = trim(utf8_encode($rows['Mednom']));
                $rows['Medesp'] = trim(utf8_encode($rows['Medesp']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                $pos = strpos($rows['Medesp'], "-");
                if ($pos !== false) {
                    $aux = explode("-", $rows['Medesp']);
                    $rows['Medesp'] = $aux[0];
                    $rows['Espnom'] = $aux[1];
                }

                if ($rows['Medesp'] == "") $rows['Espnom'] = "00000";
                if ($rows['Espnom'] == "") $rows['Espnom'] = "SIN DATOS";

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Medcod'], "des" => $rows['Mednom'], "codesp" => $rows['Medesp'], "desesp" => $rows['Espnom']);    //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows[ 'Medcod' ]}-{$rows[ 'Mednom' ]}"; //Este es el que ve el usuario
            }
        }
    } else {
        //medico
        $and_especialidad = "";
        if ($especialidad != "")
            $and_especialidad = " AND Espcod = '" . $especialidad . "'";

        if (!isset($data) or trim($data) == "")
            $data = array();

        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2,Medesp,Espnom
                    FROM " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
                    WHERE Meddoc = '" . utf8_decode($med) . "'
                    " . $and_especialidad . "
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Meddoc'] = trim(utf8_encode($rows['Meddoc']));
                $rows['Medno1'] = trim(utf8_encode($rows['Medno1']));
                $rows['Medno2'] = trim(utf8_encode($rows['Medno2']));
                $rows['Medap1'] = trim(utf8_encode($rows['Medap1']));
                $rows['Medap2'] = trim(utf8_encode($rows['Medap2']));
                $rows['Medesp'] = trim(utf8_encode($rows['Medesp']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                $pos = strpos($rows['Medesp'], "-");
                if ($pos !== false) {
                    $aux = explode("-", $rows['Medesp']);
                    $rows['Medesp'] = $aux[0];
                    $rows['Espnom'] = $aux[1];
                }

                if ($rows['Medesp'] == "") $rows['Espnom'] = "00000";
                if ($rows['Espnom'] == "") $rows['Espnom'] = "SIN DATOS";

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Meddoc'],
                    "des" => $rows['Medno1'] . " " . $rows['Medno2'] . " " . $rows['Medap1'] . " " . $rows['Medap2'],
                    "codesp" => $rows['Medesp'],
                    "desesp" => $rows['Espnom']);  //Este es el dato a procesar en javascript
                $data['usu'] = $rows['Medno1'] . " " . $rows['Medno2'] . " " . $rows['Medap1'] . " " . $rows['Medap2'];   //Este es el que ve el usuario
            }
        }
    }

    return $data;
}

function consultarDiagnosticos($diag)
{

    global $conex;
    global $sexoPaciente;


    $val = "";

    $condicionSexo = ($sexoPaciente == "M" or $sexoPaciente == "F") ? " AND ( Sexo = 'A' or Sexo = '{$sexoPaciente}' ) " : "";
    //Diagnostico
    $sql = "SELECT Codigo, Descripcion
                FROM root_000011
                WHERE (Descripcion LIKE '%" . utf8_decode($diag) . "%' or Codigo like '%" . utf8_decode($diag) . "%') {$condicionSexo}
                  AND estado = 'on' ";

    $sql .= " ORDER BY Descripcion LIMIT 30";

    $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0) {

        for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

            $rows['Codigo'] = trim(utf8_encode($rows['Codigo']));
            $rows['Descripcion'] = trim(utf8_encode($rows['Descripcion']));

            //Creo el resultado como un json
            //Primero creo un array con los valores necesarios
            $data['valor'] = array("cod" => $rows['Codigo'], "des" => $rows['Descripcion']);  //Este es el dato a procesar en javascript
            $data['usu'] = "{$rows[ 'Codigo' ]}-{$rows[ 'Descripcion' ]}";    //Este es el que ve el usuario
            $dat = array();
            $dat[] = $data;

            $val .= json_encode($dat) . "\n";

        }
    }

    return $val;
}

function consultarProcedimientos($proc, $wbasedato)
{

    global $conex;


    $val = "";

    //Diagnostico
    $sql = "SELECT Procod,Pronom,Procup
                FROM " . $wbasedato . "_000103 MP
                INNER JOIN root_000012 CUPS ON CUPS.Codigo = MP.Procup 
                WHERE (MP.Pronom LIKE '%" . utf8_decode($proc) . "%' or MP.Procod like '%" . utf8_decode($proc) . "%')
                AND MP.Proest = 'on'
                AND CUPS.egreso != 'off'
                AND char_length(MP.Procod) >= 6
                ORDER BY MP.Pronom
                LIMIT 30
                ";

    $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    $num = mysql_num_rows($res);

    if ($num > 0) {

        for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

            $rows['Procod'] = trim(utf8_encode($rows['Procod']));
            $rows['Pronom'] = trim(utf8_encode($rows['Pronom']));

            //Creo el resultado como un json
            //Primero creo un array con los valores necesarios
            $data['valor'] = array("cod" => $rows['Procod'], "des" => $rows['Pronom']);   //Este es el dato a procesar en javascript
            $data['usu'] = "{$rows[ 'Procod' ]}-{$rows[ 'Pronom' ]}"; //Este es el que ve el usuario
            $dat = array();
            $dat[] = $data;

            $val .= json_encode($dat) . "\n";

        }
    }

    return $val;
}

function consultarEspecialidades($espe, $wbasedato, $aplicacion, $aplicacionHCE)
{

    global $conex;
    global $wemp_pmla;

    $val = "";

    if ($aplicacion == "") {
        //especialidad
        $sql = "SELECT Selcod,Seldes
                    FROM " . $wbasedato . "_000105
                    WHERE (Seldes LIKE '%" . utf8_decode($espe) . "%' or Selcod like '%" . utf8_decode($espe) . "%')
                    AND Seltip='11'
                    AND Selest='on'
                    ORDER BY Seldes
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Selcod'] = trim(utf8_encode($rows['Selcod']));
                $rows['Seldes'] = trim(utf8_encode($rows['Seldes']));

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Selcod'], "des" => $rows['Seldes']);   //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows[ 'Selcod' ]}-{$rows[ 'Seldes' ]}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";

            }
        }
    } else {

        $parametroEspecialidades = "FILTRO_ESP_MED_EGRESOS";

        /** Realizar consulta al parámetro de la root_000051 **/
        $filtroEspecialidades = consultarAliasPorAplicacion($conex, $wemp_pmla, $parametroEspecialidades);

        $sql = "SELECT
                    esp.Espcod,
                    esp.Espnom
                FROM
                    {$aplicacion}_000044 esp
                INNER JOIN {$aplicacion}_000048 med ON
                    med.Medesp = esp.Espcod
                INNER JOIN {$aplicacionHCE}_000020 usu ON
                    usu.Usucod = med.Meduma
                INNER JOIN {$aplicacionHCE}_000019 rol ON
                    rol.Rolcod = usu.Usurol
                WHERE
                    rol.Rolmed = 'on'
                    AND (esp.Espcod LIKE '%" . utf8_decode($espe) . "%'
                        or esp.Espnom like '%" . utf8_decode($espe) . "%')
                    AND esp.Espcod NOT IN (" . $filtroEspecialidades . ")   
                GROUP BY
                    esp.Espcod
                ORDER BY
                    esp.Espnom ASC
                LIMIT 30";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Espcod'] = trim(utf8_encode($rows['Espcod']));
                $rows['Espnom'] = trim(utf8_encode($rows['Espnom']));

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Espcod'], "des" => $rows['Espnom']);   //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows[ 'Espcod' ]}-{$rows[ 'Espnom' ]}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";

            }
        }
    }

    return $val;
}

function consultarServicios($serv, $wbasedato, $aplicacion)
{

    global $conex;


    $val = "";

    if ($aplicacion == "") {
        //servicios
        $sql = "SELECT Ccocod,Ccodes
                    FROM " . $wbasedato . "_000003
                    WHERE (Ccodes LIKE '%" . utf8_decode($serv) . "%' or Ccocod like '%" . utf8_decode($serv) . "%')
                    AND (Ccotip='A' or Ccotip='H')
                    ORDER BY Ccodes
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Ccocod'] = trim(utf8_encode($rows['Ccocod']));
                $rows['Ccodes'] = trim(utf8_encode($rows['Ccodes']));

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Ccocod'], "des" => $rows['Ccodes']);   //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows[ 'Ccocod' ]}-{$rows[ 'Ccodes' ]}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";

            }
        }
    } else {
        $sql = "SELECT  Ccocod, Cconom
                    FROM " . $aplicacion . "_000011
                    WHERE (Ccocod LIKE '%" . utf8_decode($serv) . "%' or Cconom like '%" . utf8_decode($serv) . "%')
                    AND (Ccohos = 'on' or Ccourg = 'on' or Ccoing = 'on' or Ccocir = 'on' or Ccoayu ='on')
                    AND Ccoest='on'
                    ORDER BY Cconom
                    LIMIT 30
                    ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $rows['Ccocod'] = trim(utf8_encode($rows['Ccocod']));
                $rows['Cconom'] = trim(utf8_encode($rows['Cconom']));

                //Creo el resultado como un json
                //Primero creo un array con los valores necesarios
                $data['valor'] = array("cod" => $rows['Ccocod'], "des" => $rows['Cconom']);   //Este es el dato a procesar en javascript
                $data['usu'] = "{$rows[ 'Ccocod' ]}-{$rows[ 'Cconom' ]}"; //Este es el que ve el usuario
                $dat = array();
                $dat[] = $data;

                $val .= json_encode($dat) . "\n";
            }
        }
    }

    return $val;
}

/************************************************************************************************
 * Crea un array de datos que hace los siguiente.
 *
 * Toma todas las variables enviadas por Post, y las convierte en un array. Este array puede ser
 * procesado por las funciones crearStringInsert y crearStringInsert
 *
 * Explicacion:
 * Toma todas las variables enviadas por Post que comiencen con $prefijoHtml, creando un array
 * donde su clave o posicion comiencen con $prefijoBD concatenado con $longitud de caracteres
 * despues del $prefijoHtml y dandole como valor el valor de la variable enviada por Post
 *
 * Ejemplo:
 *
 * La variable Post es: indpersonas = 'Armando Calle'
 * Ejecutando la funcion: $a = crearArrayDatos( 'movhos', 'Per', 'ind', 3 );
 *
 * El array que retorna la función es:
 *                      $a[ 'Perper' ] = 'Armando Calle'
 *                      $a[ 'Medico' ] = 'movhos'
 *                      $a[ 'Fecha_data' ] = '2013-05-22'
 *                      $a[ 'Hora_data' ] = '05:30:24'
 *                      $a[ 'Seguridad' ] = 'C-movhos'
 ************************************************************************************************/
function crearArrayDatos($wbasedato, $prefijoBD, $prefijoHtml, $longitud, $datos = '')
{

    $val = array();

    if (empty($datos)) {
        $datos = $_POST;
    }

    $crearDatosExtras = false;

    $lenHtml = strlen($prefijoHtml);

    foreach ($datos as $keyPost => $valuePost) {

        if (substr($keyPost, 0, $lenHtml) == $prefijoHtml) {

            if (substr($keyPost, $lenHtml, $longitud) != 'id') {
                $val[$prefijoBD . substr($keyPost, $lenHtml, $longitud)] = utf8_decode($valuePost);
            } else {
                $val[substr($keyPost, $lenHtml, $longitud)] = utf8_decode($valuePost);
            }
            $crearDatosExtras = true;
        }
    }

    //Estos campos se llenan automáticamente y toda tabla debe tener esots campos
    if ($crearDatosExtras) {
        global $user;
        $user2 = explode("-", $user);
        (isset($user2[1])) ? $user2 = $user2[1] : $user2 = $user2[0];
        if ($user2 == "")
            $user2 = $wbasedato;
        $val['Medico'] = $wbasedato;
        $val['Fecha_data'] = date("Y-m-d");
        $val['Hora_data'] = date("H:i:s");
        $val['Seguridad'] = "C-$user2";
    }


    return $val;
}

/***************************************************************************************
 * inserta los datos a la tabla
 *
 * $datos   Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla   Nombre de la tabla a la que se va a insertar los datos
 ***************************************************************************************/
function crearStringInsert($tabla, $datos)
{

    $stPartInsert = "";
    $stPartValues = "";

    foreach ($datos as $keyDatos => $valueDatos) {

        $stPartInsert .= ",$keyDatos";
        $stPartValues .= ",'$valueDatos'";
    }


    $stPartInsert = "INSERT INTO $tabla(" . substr($stPartInsert, 1) . ")";   //quito la coma inicial
    $stPartValues = " VALUES (" . substr($stPartValues, 1) . ")";

    return $stPartInsert . $stPartValues;
}

function consultaNombreImpDiag($codImpDiag)
{
    global $conex;

    //consultar codigo impresion diagnostica
    $sql1 = "select Codigo, Descripcion, Notificar
                FROM root_000011
                where Codigo = '" . $codImpDiag . "'
                ";
    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla de diagnosticos " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));

    return $res1;
}

function consultaNombreEntidad($codImpDiag)
{
    global $conex;
    global $wbasedato;

    //consultar codigo impresion diagnostica
    $sql1 = "select Empcod as Codigo, Empnom as Descripcion
                FROM " . $wbasedato . "_000024
                where Empcod = '" . $codImpDiag . "'
                ";
    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla de diagnosticos " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));

    return $res1;
}

function consultaNombreProce($codProce)
{
    global $conex;
    global $wbasedato;

    //consultar codigo impresion diagnostica
    $sql1 = "select Procod,Pronom
                FROM " . $wbasedato . "_000103
                where Procod = '" . $codProce . "'
                ";
    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla de procedimientos " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));

    return $res1;
}

function consultaNombreEspe($codEspe, $aplicacion = "movhos")
{
    global $conex;
    global $wbasedato;
    $aplicacion = "movhos";
    $res = "";
    if ($aplicacion == "") {
        //especialidad
        $sql = "SELECT Selcod as Espcod,Seldes as Espnom
                FROM " . $wbasedato . "_000105
                WEHRE Selcod = '" . $codEspe . "'
                AND Seltip='11'
                AND Selest='on'
                ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    } else {
        $sql = "SELECT  Espcod, Espnom
                FROM " . $aplicacion . "_000044
                WHERE Espcod = '" . $codEspe . "'
                ";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    }

    return $res;
}

function consultarAnestesiologo($codigoAntestesiologo, $wbasedato)
{
    global $conex;

    $query = "SELECT Medcod, Mednom,Medesp,Espnom
                    FROM {$wbasedato}_000051 LEFT JOIN {$wbasedato}_000053 ON (Medesp=Espcod)
                    WHERE Medcod = '$codigoAntestesiologo'
                    ORDER BY Mednom";
    $rs = mysql_query($query, $conex);

    while ($row = mysql_fetch_array($rs)) {
        $pos = strpos($row['Medesp'], "-");
        if ($pos !== false) {
            $aux = explode("-", $row['Medesp']);
            $row['Medesp'] = $aux[0];
            $row['Espnom'] = $aux[1];
        }

        if ($row['Medesp'] == "") $row['Espnom'] = "00000";
        if ($row['Espnom'] == "") $row['Espnom'] = "SIN DATOS";
    }
    return ($row);
}

function consultaNombreServ($codServ, $aplicacion)
{
    global $conex;
    global $aplicacion;
    global $wbasedato;

    if ($aplicacion == "") {
        //consultar codigo del servicio
        $sql1 = "SELECT Ccocod,Ccodes
                FROM " . $wbasedato . "_000003
                WHERE Ccocod = '" . $codServ . "'";
    } else {
        $sql1 = " SELECT  Ccocod, Cconom
                    FROM " . $aplicacion . "_000011
                    WHERE Ccocod = '" . $codServ . "'";
    }
    $res1 = mysql_query($sql1, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla de servicios " . mysql_errno() . " - Error en el query $sql1 - " . mysql_error()));

    return $res1;
}

function consultaNombreMedicos2($codMed, $aplicacion)
{
    global $conex;
    global $wbasedato;
    global $mostrarSalida;

    $wmedico = array('n_medico' => '',
        'c_especialidad' => '',
        'n_especialidad' => '');

    $sql = "";

    if ($aplicacion == "") {
        $sql = "SELECT Medcod, Mednom, Medesp, Espnom
                FROM " . $wbasedato . "_000051 LEFT JOIN " . $wbasedato . "_000053 ON (Medesp=Espcod)
                WHERE Medcod = '" . $codMed . "'";
    } else {
        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2, Medesp, Espnom
                FROM " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
                WHERE Meddoc = '" . $codMed . "'";
    }
    $res4 = mysql_query($sql, $conex);
    if ($res4) {
        $num4 = mysql_num_rows($res4);
        if ($num4 > 0) {
            $rows4 = mysql_fetch_array($res4);
            if ($aplicacion != "")
                $wmedico['n_medico'] = $rows4['Medno1'] . " " . $rows4['Medno2'] . " " . $rows4['Medap1'] . " " . $rows4['Medap2'];
            else
                $wmedico['n_medico'] = utf8_encode($rows4['Mednom']);
            $wmedico['c_especialidad'] = $rows4['Medesp'];
            $wmedico['n_especialidad'] = utf8_encode($rows4['Espnom']);
        }
    }
    return $wmedico;
}

function consultaNombreMedicos($codMed, $aplicacion)
{
    global $conex;
    global $wbasedato;

    $sql = "";
    if ($aplicacion == "") {
        $sql = "SELECT Medcod, Mednom
                FROM " . $wbasedato . "_000051
                WHERE Medcod = '" . $codMed . "'
                AND Medest ='on'
                ORDER BY Mednom
                ";
    } else {
        $sql = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2
                FROM " . $aplicacion . "_000048
                WHERE Meddoc = '" . $codMed . "'
                AND Medest ='on'
                AND Meddoc != ''
                ORDER BY Medno1,Medno2,Medap1,Medap2
                ";
    }

    $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
    return $res;
}

function crearStringUpdate($tabla, $datos)
{

    $stPartInsert = "";
    $stPartValues = "";

    //campos que no se actualizan
    $prohibidos["Medico"] = true;
    $prohibidos["Fecha_data"] = true;
    $prohibidos["Hora_data"] = true;
    $prohibidos["Seguridad"] = true;
    $prohibidos["id"] = true;

    foreach ($datos as $keyDatos => $valueDatos) {

        if (!isset($prohibidos[$keyDatos])) {
            $stPartInsert .= ",$keyDatos = '$valueDatos' ";
        }
    }

    $stPartInsert = "UPDATE $tabla SET " . substr($stPartInsert, 1);    //quito la coma inicial
    $stPartValues = " WHERE id = '{$datos[ 'id' ]}'";

    return $stPartInsert . $stPartValues;

    //UPDATE  `matrix`.`movhos_000138` SET  `Dprest` =  'off' WHERE  `movhos_000138`.`id` =82;
}

function logEgreso($des, $historia, $ingreso, $documento)
{
    global $key;
    global $conex;
    global $wbasedato;

    $data = array('error' => 0, 'mensaje' => '', 'html' => '');

    $fecha = date("Y-m-d");
    $hora = (string)date("H:i:s");

    $sql = "INSERT INTO " . $wbasedato . "_000185 (     medico     ,      fecha_data         ,       hora_data        ,        Logusu         ,         Logdes        ,            Loghis          ,           Loging          ,            Logdoc           , Logest, seguridad )
                                       VALUES ('" . $wbasedato . "','" . utf8_decode($fecha) . "','" . utf8_decode($hora) . "','" . utf8_decode($key) . "','" . utf8_decode($des) . "','" . utf8_decode($historia) . "','" . utf8_decode($ingreso) . "','" . utf8_decode($documento) . "',  'on' , 'C-root'  )";

    $res = mysql_query($sql, $conex) or ($data['mensaje'] = utf8_encode("Error insertando en la tabla de log egreso " . $wbasedato . " 178 " . mysql_errno() . " - Error en el query $sql - " . mysql_error()));
    if (!$res) {
        $data['error'] = 1; //sale el mensaje de error
    }

    return $data;
}

function quitarEtiquetasCadena($cadena)
{
    // $cadena = ' <OPTION selected>C-.infeccion tracto urinario</OPTION><OPTION value=C-.sindrome de intestino irritable>C-.sindrome de intestino irritable</OPTION><OPTION value=C-.Gastritis>C-.Gastritis</OPTION>';
    $cadena = str_replace("><", ">\n<", $cadena);
    $cadena = strip_tags($cadena);

    return $cadena;

}

function consultarCcoAyuda($cco_buscado)
{
    global $conex, $wemp_pmla, $aplicacion;

    $query = "SELECT ccoayu
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$cco_buscado}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    $ccoAyu = ($row['ccoayu'] == "on") ? true : false;
    return ($ccoAyu);
}

function consultarCcoHos($cco_buscado)
{
    global $conex, $wemp_pmla, $aplicacion;

    $query = "SELECT ccohos
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$cco_buscado}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    $ccoHos = ($row['ccohos'] == "on") ? true : false;
    return ($ccoHos);
}

function consultarNombreDiagnostico($diagnostico)
{
    global $conex, $wemp_pmla, $aplicacion;

    $query = "SELECT ccoayu
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$cco_buscado}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    $ccoAyu = ($row['ccoayu'] == "on") ? true : false;
    return ($ccoAyu);
}

function consultarCcoUrgencias($cco_buscado)
{
    global $conex, $wemp_pmla, $aplicacion;

    $query = "SELECT ccourg
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$cco_buscado}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    $ccourg = ($row['ccourg'] == "on") ? true : false;
    return ($ccourg);
}

function consultarCausaEgresoUrgencias($historia, $ingreso, $fechaIngreso, $horaIngreso)
{
    global $conex, $wemp_pmla, $aplicacion;
    $causaEgreso = "A";

    $query = "SELECT Ubihis, Ubiing, Ubifap, Ubihap, Ubimue
                FROM {$aplicacion}_000018
               WHERE Ubihis = '{$historia}'
                 AND Ubiing = '{$ingreso}'";
    $rs = mysql_query($query, $conex);
    $row = mysql_fetch_assoc($rs);
    if ($row['Ubimue'] == "on") {
        //--> calcular tiempo estancia.
        $tiempoUnixEgreso = strtotime($row['Ubifap'] . " " . $row['Ubihap']);
        $tiempoUnixIngreso = strtotime($fechaIngreso . " " . $horaIngreso);
        $tiempoDiferencia = ceil(($tiempoUnixEgreso - $tiempoUnixIngreso) / 3600);
        $causaEgreso = ($tiempoDiferencia >= 48) ? "+48" : "-48";
    }
    return ($causaEgreso);
}

//--> aca en medicos tratantes podríamos consultar los centros de costos por los que pasó el paciente
function consultarMedicosTratantes($historia, $ingreso, $aplicacion)
{
    global $conex;
    $arr_medicos = array();
    $query = "SELECT Meddoc as cod, CONCAT( Medno1,' ',Medno2,' ',Medap1,' ',Medap2) as nom, Medesp as esp_cod, Espnom as esp_nom
              FROM " . $aplicacion . "_000047, " . $aplicacion . "_000048 LEFT JOIN " . $aplicacion . "_000044 ON (Medesp=Espcod)
             WHERE Methis = '" . $historia . "'
               AND Meting = '" . $ingreso . "'
               AND Metdoc = Meddoc
               AND (Medgen = 'on' or Medees = 'on')
               GROUP BY Meddoc";

    $res = mysql_query($query, $conex) or ($data['mensaje'] = utf8_encode("Error consultando la tabla " . $aplicacionHce . "_000022 y " . $aplicacion . "_000048 " . mysql_errno() . " - Error en el query $sql - " . mysql_error()));
    if ($res) {
        $num = mysql_num_rows($res);
        if ($num > 0) {
            while ($rows = mysql_fetch_assoc($res)) {
                array_push($arr_medicos, $rows);
            }
        }
    }
    return $arr_medicos;
}

function calcularEdad($fecha_nacimiento)
{
    list($y, $m, $d) = explode("-", $fecha_nacimiento);
    $y_dif = date("Y") - $y;
    $m_dif = date("m") - $m;
    $d_dif = date("d") - $d;
    if ((($d_dif < 0) && ($m_dif == 0)) || ($m_dif < 0))
        $y_dif--;
    return $y_dif;
}

function ping_unix()
{
    global $conex;
    global $wemp_pmla;

    $ret = false;

    $direccion_ipunix = consultarAliasPorAplicacion($conex, $wemp_pmla, "ipdbunix");
    if ($direccion_ipunix != "") {
        $cmd_result = shell_exec("ping -c 1 -w 1 " . $direccion_ipunix);
        $result = explode(",", $cmd_result);
        if (preg_match('/(1 received)/', $result[1])) {
            $ret = true;
        }
    }
    return $ret;
}

function consultarServiciosDiagnosticos($servicioEgreso)
{
    global $conex, $wemp_pmla, $wbasedato, $aplicacion;

    $data = array();

    if ($aplicacion == "") {
        $sql = "SELECT Ccocod,Ccodes
                FROM " . $wbasedato . "_000003
                WHERE (Ccodes LIKE '%" . utf8_decode($serv) . "%' or Ccocod like '%" . utf8_decode($serv) . "%')
                AND (Ccotip='A' or Ccotip='H')
                ORDER BY Ccodes";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $data[$rows['Ccocod']]['codigo'] = trim(utf8_encode($rows['Ccocod']));
                $data[$rows['Ccocod']]['descripcion'] = trim(utf8_encode($rows['Ccodes']));
            }
        }
    } else {
        $queryEgreso = " SELECT Ccoayu
                           FROM {$aplicacion}_000011
                          WHERE Ccocod = '{$servicioEgreso}'";
        $rsEgre = mysql_query($queryEgreso, $conex);
        $rowEgreso = mysql_fetch_assoc($rsEgre);

        ($rowEgreso['Ccoayu'] == "on") ? $condicionCcos = " AND Ccoayu ='on' " : $condicionCcos = " AND (Ccohos = 'on' or Ccourg = 'on' or Ccocir = 'on' or Ccoayu ='on') ";
        $sql = "SELECT  Ccocod, Cconom
                FROM " . $aplicacion . "_000011
                WHERE (Ccocod LIKE '%" . utf8_decode($serv) . "%' or Cconom like '%" . utf8_decode($serv) . "%')
                {$condicionCcos}
                AND Ccoest='on'
                ORDER BY Cconom";

        $res = mysql_query($sql, $conex) or die(mysql_errno() . " - Error en el query $sql - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0) {

            for ($i = 0; $rows = mysql_fetch_array($res); $i++) {

                $data[$rows['Ccocod']]['codigo'] = trim(utf8_encode($rows['Ccocod']));
                $data[$rows['Ccocod']]['descripcion'] = trim(utf8_encode($rows['Cconom']));
            }
        }
    }
    return $data;
}

function consultarEgresoAnulado($historia, $ingreso)
{
    global $conex, $wemp_pmla, $wbasedato, $aplicacion;

    $sql = "SELECT Egrhis,Egring,id,Egract
               FROM {$wbasedato}_000108
              WHERE Egrhis = '{$historia}'
                AND Egring = '{$ingreso}'";

    $rs = mysql_query($sql, $conex);
    $row = mysql_fetch_assoc($rs);
    return (($row['Egract'] == "off") ? true : false);
}

function consultarServiciosCirugia()
{

    global $conex, $aplicacion;
    $ccoCir = array();
    $query = "SELECT ccocod
                 FROM {$aplicacion}_000011
                WHERE ccocir = 'on'
                UNION ALL
               SELECT ccocod
                 FROM {$aplicacion}_000011
                WHERE Ccopeg = 'off'";
    $rs = mysql_query($query, $conex);
    while ($row = mysql_fetch_assoc($rs)) {
        array_push($ccoCir, "'" . $row['ccocod'] . "'");
    }
    array_push($ccoCir, "''");
    $ccoCir = implode(",", $ccoCir);
    return ($ccoCir);
}

function consultarMedicoDiagnostico($historia, $ingreso, $formularioEvolucionUrgencias, $usuario = "")
{

    global $conex, $wemp_pmla, $wbasedato, $aplicacionHce, $aplicacion;

    if ($usuario == "") {
        $query = " SELECT Firusu, id
                     FROM {$aplicacionHce}_000036
                    WHERE firhis = '{$historia}'
                      AND firing = '{$ingreso}'
                      AND firpro = '{$formularioEvolucionUrgencias}'
                      AND firfir = 'on'
                    ORDER BY id desc
                    LIMIT 1";
        $rs = mysql_query($query, $conex);
        $row = mysql_fetch_assoc($rs);

        $codigoMedicoUrgencias = $row['Firusu'];
    } else {
        $codigoMedicoUrgencias = $usuario;
    }

    $query = "SELECT Meddoc,Medno1,Medno2,Medap1,Medap2, Medesp, Espnom, Meduma
                FROM {$aplicacion}_000048
                LEFT JOIN
                     {$aplicacion}_000044 ON (Medesp=Espcod)
               WHERE Meduma = '{$codigoMedicoUrgencias}'";
    $rs = mysql_query($query, $conex);

    return (mysql_fetch_assoc($rs));

}

/**
 * Permite comparar los valores de las opciones que se muestra para el egreso con las registradas en la epicrisis del paciente
 * esto con el fin de mostrar seleccionada la opción que se encuentra en la epicrisis
 * @autor Johan Córdoba
 * @param $historia
 * @param $ingreso
 * @return mixed|null opción que deberia estar por defecto, si no hay coincidencias returna null
 */
function consultarCausaEgresoEpicrisis($historia, $ingreso)
{
    global $conex, $wbasedato, $aplicacionHce;

    /** Valor de retorno para el select **/
    $opcion = null;

    /*** Leemos las opciones disponibles para el egreso **/
    $sqlOpcionesEgreso = " SELECT Selcod,Seldes FROM {$wbasedato}_000105 WHERE Seltip = '10' and Selest='on' ORDER BY Seldes";
    $queryOpcionesEgreso = mysql_query($sqlOpcionesEgreso, $conex);

    while ($rowsOpcionesEgreso = mysql_fetch_array($queryOpcionesEgreso)) {

        /** Consultamos el valor de egreso de la epicrisis si existe **/
        $sqlEpicrisis = "SELECT movdat FROM {$aplicacionHce}_000353 ";
        $sqlEpicrisis .= " WHERE movtip='Seleccion' and movhis='{$historia}' and moving='{$ingreso}'";
        $sqlEpicrisis .= " AND UPPER(movdat) LIKE '%" . strtoupper($rowsOpcionesEgreso['Seldes']) . "%'";

        $queryEpicrisis = mysql_query($sqlEpicrisis, $conex);

        if (mysql_num_rows($queryEpicrisis)) {
            $opcion = $rowsOpcionesEgreso['Selcod'];
            break;
        }
    }

    return $opcion;
}

/**
 * Permite consultar el nombre de una opción de causa de egreso recibiendo la llave
 * @param $codigoOpcion llave de la opción de egreso
 * @return mixed|null
 */
function consultarNombreOpcionEgreso($codigoOpcion)
{
    global $conex, $wbasedato;

    $textoOpcion = null;

    /*** Leemos las opciones disponibles para el egreso **/
    $sqlOpcionesEgreso = " SELECT Selcod,Seldes FROM {$wbasedato}_000105 ";
    $sqlOpcionesEgreso .= " WHERE Seltip = '10' and Selest='on' AND Selcod='{$codigoOpcion}' ORDER BY Seldes";
    $queryOpcionesEgreso = mysql_query($sqlOpcionesEgreso, $conex);


    if (mysql_num_rows($queryOpcionesEgreso)) {
        $row = mysql_fetch_array($queryOpcionesEgreso);
        $textoOpcion = $row['Seldes'];
    }

    return $textoOpcion;
}

/**
 * Valida si un código de diagnostico pertenece a los diagnosticos de causas externas
 * @param $codigoDiagnostico codigo del diagnostico
 * @return bool
 */
function validarCodigoDiagnosticoPrincipal($codigoDiagnostico)
{
    global $conex;
    global $sexoPaciente;
    global $wemp_pmla;

    $parametroCUPS = "FILTRO_CAP_EGRESOS";

    $validoPrincipal = true;
    $condicionSexo = ($sexoPaciente == "M" or $sexoPaciente == "F") ? " AND ( Sexo = 'A' or Sexo = '{$sexoPaciente}' ) " : "";

    /** Realizar consulta al parámetro de la root_000051 **/
    $filtroCapitulos = consultarAliasPorAplicacion($conex, $wemp_pmla, $parametroCUPS);

    $sql = "SELECT Codigo FROM root_000011 ";
    $sql .= "WHERE Codigo = '{$codigoDiagnostico}' AND Descripcion NOT LIKE '%*%' {$condicionSexo}";
    $sql .= " AND estado = 'on' ";

    /** Si se está buscando el diagnostico principal filtramos los de causas externas **/
    $sql .= " AND Cod_cap IN ({$filtroCapitulos})"; // Codigo del capitulo de causas externas

    $query = mysql_query($sql, $conex);
    $totalRegistros = mysql_num_rows($query);

    if ($totalRegistros) {
        $validoPrincipal = false;
    }

    return $validoPrincipal;
}

function validarProcedimientosFormulario($procedimientosPOST)
{
    global $wbasedato;

    $procedimientosValidos = ['respuesta' => true, 'mensaje' => ''];

    /** Leemos los procedimientos recibidos en el formulario **/

    foreach ($procedimientosPOST as $procedimiento) {
        $codigoProc = $procedimiento['pro_cod_txtCodPro'];
        $nombreProc = $procedimiento['ProDes_txtProDes'];
        $fechaProc = $procedimiento['pro_fec_txtFecPro'];
        $codigoMedProc = $procedimiento['pro_med_txtCodMed'];

        /** Validamos los campos solo en caso que se tenga un código de procedimento o se ponga un código de médico**/
        if (strlen($codigoProc) or strlen($codigoMedProc)) {
            /** Validar fecha, el código del médico o el código del procedimiento **/
            if ((!strlen($fechaProc) or $fechaProc == "0000-00-00") or
                (!strlen($codigoMedProc) or !strlen($codigoProc))) {

                $procedimientosValidos['respuesta'] = false;
                $procedimientosValidos['mensaje'] = 'Por favor, verifique que los campos de procedimientos estén completos y bien diligenciados';

                break;
            }
        }

        /** Validar que el código del procedimiento esté activo en la tabla root_000012 **/
        $procedimientoValido = strlen(consultarProcedimientos($codigoProc, $wbasedato));

        if (!$procedimientoValido) {
            $procedimientosValidos['respuesta'] = false;
            $procedimientosValidos['mensaje'] = 'El codigo ' . $codigoProc . '-' . $nombreProc . ' no es un procedimiento';
            break;
        }
    }


    return $procedimientosValidos;
}

/********************* Fin funciones **********************/

/******************************************************************************
 * INICIO DEL PROGRAMA
 ******************************************************************************/


//


$conex = obtenerConexionBD("matrix");

$wactualiz = "2020-03-25";

if (!isset($wemp_pmla)) {
    terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO . "wemp_pmla");
}

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
$wbasedato1 = strtolower($institucion->baseDeDatos);
/* consulta para saber si es cliame o clisur */
$alias = "movhos";
$aplicacion = consultarAplicacion($conex, $wemp_pmla, $alias);
$aplicacionhce = consultarAplicacion($conex, $wemp_pmla, "hce");

$tieneConexionUnix = consultarAliasPorAplicacion($conex, $wemp_pmla, 'conexionUnix');
//$tieneConexionUnix = "off";
if ($hay_unix && $tieneConexionUnix == 'on') {
    /*****************************************************************************
     * Ejecutando crones
     *
     * Tablas que se mueven con estos crones
     *
     * maestroTarifas                       000025
     * maestroEmpresa                       000024
     * maestroEventosCatastroficos          000155
     * maestroTiposVehiculos                000162
     *****************************************************************************/
    //Este archivo contiene los crones que se pueden ejecutar
    include_once("root/kron_maestro_unix.php");

    //Ejecuto los crones correspondientes
    //$cron = Array( 'maestroTarifas', 'maestroEmpresa', 'maestroEventosCatastroficos', 'maestroDepartamentos', 'maestroMunicipios' );
    //$cron = Array( 'maestroTarifas', 'maestroEventosCatastroficos', 'maestroDepartamentos', 'maestroMunicipios' );
    //$ejCron = new datosDeUnix();
    //foreach( $cron as $key => $value ){
    //$ejCron->$value();
    //}
    /*****************************************************************************/
    $cron = array('kron_egresoMatrix_Unix');
    $ejCron = new datosDeUnix();
    foreach ($cron as $key => $value) {
        //$ejCron->$value();
    }

    if (isset($anular_historia_debug)) {
        $a = new egreso_erp();
        $a->anularEgreso('6888', '3');
        echo json_encode($a->data);
    }
}

@session_start();
//el usuario se encuentra registrado
if (false && !isset($_SESSION['user']))
    echo "error";
else {
    $fechaAct = date("Y-m-d");
    $horaAct = date("H:i:s");

    if ($wemp_pmla == 01) {
        encabezado("EGRESO DE PACIENTES ", $wactualiz, $wbasedato1);
    } else if ($wemp_pmla == 02) {
        encabezado("EGRESO DE PACIENTES ", $wactualiz, "logo_" . $wbasedato1);
    }


    if (isset($historia) and isset($ingreso)) {

        $query = "SELECT Ubifad, Ubihad from movhos_000018 where  Ubihis ='" . $historia . "' and ubiing = '" . $ingreso . "' ";
        $result = mysql_query($query, $conex);
        $row_result = mysql_fetch_assoc($result);

        if (date('Y-m-d', strtotime($row_result['Ubifad'])) == $row_result['Ubifad']) {
            $fechaAct = $row_result['Ubifad'];
            $horaAct = $row_result['Ubihad'];
        }
        $query = " SELECT pacsex, ingfei
                 FROM {$wbasedato}_000101, {$wbasedato}_000100
                WHERE inghis = '{$historia}'
                  and ingnin = '{$ingreso}'
                  and pachis = inghis";
        $rs = mysql_query($query, $conex);
        $row = mysql_fetch_assoc($rs);
        $sexo = $row['pacsex'];
        $wfei = $row['ingfei'];
        echo "<input type='hidden' name='fechaIngresoAdm' id='fechaIngresoAdm' value='" . $wfei . "'>";
        echo "<input type='hidden' name='sexoAdm' id='sexoAdm' value='" . $sexo . "'>";
        $fechaIngresoSugerida = $wfei;
    }
    $fechaIngresoSugerida = $fechaAct;
    if (isset($historia) and isset($ingreso)) {

        if (!consultarCcoHos($ccoEgreso)) {// el servicio es hospitalario
            $fechaAltDefinitiva = $fechaAct;
            $horaAltDefinitiva = $horaAct;
            $pacienteHospitalizado = "off";
        } else {
            $pacienteHospitalizado = "on";
        }

    } else {
        $fechaAltDefinitiva = $fechaAct;
        $horaAltDefinitiva = $horaAct;
        $pacienteHospitalizado = "off";
    }

    $ccoRegistrosMedicos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoRegistrosMedicos');
    $funcionariosRegistros = array();

    $query = " SELECT Ccostos
             FROM usuarios
            WHERE codigo = '{$user2[1]}'
              and Empresa = '{$wemp_pmla}'
              and Activo  = 'A' ";

    $rs = mysql_query($query, $conex);

    while ($row = mysql_fetch_assoc($rs)) {
        if ($row['Ccostos'] == $ccoRegistrosMedicos) {
            $funcionarioRegistros = "on";
        } else {
            $funcionarioRegistros = "off";
        }
    }

    if (!isset($c_param)) $c_param = 0;
    echo "<input type='hidden' name='wbasedato' id='wbasedato' value='" . $wbasedato . "'>";
    echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='" . $wemp_pmla . "'>";
    echo "<input type='hidden' name='fechaAct' id='fechaAct' value='" . $fechaAct . "'>";
    echo "<input type='hidden' name='horaAct' id='horaAct' value='" . $horaAct . "'>";
    echo "<input type='hidden' name='fechaAltDefinitiva' id='fechaAltDefinitiva' value='" . $fechaAltDefinitiva . "'>";
    echo "<input type='hidden' name='horaAltDefinitiva' id='horaAltDefinitiva' value='" . $horaAltDefinitiva . "'>";
    echo "<input type='hidden' name='pacienteHospitalizado' id='pacienteHospitalizado' value='" . $pacienteHospitalizado . "'>";
    echo "<input type='hidden' name='key' id='key' value='" . $key . "'>";
    echo "<input type='hidden' name='aplicacion' id='aplicacion' value='" . $aplicacion . "'>";
    $cco_ayu = (consultarCcoAyuda($ccoEgreso)) ? "on" : "off";
    echo "<input type='hidden' name='ccoAyuda' id='ccoAyuda' value='" . $cco_ayu . "'>";
    echo "<input type='hidden' name='mostrarSalida' id='mostrarSalida' value='" . $mostrarSalida . "'>";
    $cco_urg = (consultarCcoUrgencias($ccoEgreso)) ? "on" : "off";
    echo "<input type='hidden' name='egresoUrgencias' id='egresoUrgencias' value='" . $cco_urg . "'>";
    echo "<input type='hidden' name='funcionarioRegistros' id='funcionarioRegistros' value='" . $funcionarioRegistros . "'>";

    $egreso_automatico = consultarValidateEgreso($conex, $aplicacion, $ccoEgreso);
    echo "<input type='hidden' name='egreso_automatico' id='egreso_automatico' value='" . $egreso_automatico . "'>";


    $activacionEgresoAnulado = "off";//-->activacionEgresoAnulado
    if ($c_param == "0" and isset($historia) and isset($ingreso)) {
        $tieneEgresoAnulado = consultarEgresoAnulado($historia, $ingreso);
        if ($tieneEgresoAnulado) {
            $c_param = "1";
            echo "<input type='hidden' name='consultandoAnulado' value='{$tieneEgresoAnulado}'>";
            echo "<div id='msjAlerta_3' style='display:;'>
                <br>
                <img src='../../images/medical/root/Advertencia.png'/> Este paciente tiene un egreso anulado,<br> para reactivar  el egreso solo debe darle click en \"Actualizar Egreso\"
            </div>";
            $activacionEgresoAnulado = "on";
        }
    }
    echo "<input type='hidden' name='consultar_egreso' id='consultar_egreso' value='" . $c_param . "'>";
    echo "<input type='hidden' name='activacionEgresoAnulado' id='activacionEgresoAnulado' value='" . $activacionEgresoAnulado . "'>";

    echo "<FORM METHOD='POST' ACTION='' id='forEgresos'>";

    echo "<div id='div_egresos'>";

//menu navegacion superior
    echo "<div id='bot_navegacion1' style='display:none'>";
    echo "<center><table style='width:500;' border='0'>";
    echo "<th colspan='3' class='encabezadotabla'>Resultados de la busqueda</th>";
    echo "<tr class='fila1'>";
//echo "<td align='center' colspan='3'>Total Resultados:<span id='spTotalReg1'></span>&nbsp;&nbsp;</td>";
    echo "</tr>";
    echo "<tr>";
//echo "<td align='center' colspan='3' class='fila1'>Resultado:<span id='spRegAct1'></span>&nbsp;con historia: &nbsp;<span id='spHisAct1'></span>&nbsp;Ingreso:&nbsp;<span id='spIngAct1'></span>&nbsp;de&nbsp;<span id='spTotalIng11'></span></td>";
    echo "<td align='center' colspan='3' class='fila1'>Resultado:<span id='spRegAct1'></span>&nbsp;de&nbsp;<span id='spTotalIng11'></span></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align='center' colspan='3'><img src='../../images/medical/citas/atras.jpg' height='30' width='30' border=0 onClick=\"navegacionIngresos(-1);\"/>";
    echo "&nbsp;<img src='../../images/medical/citas/adelante.jpg' height='30' width='30' border=0 onClick=\"navegacionIngresos(+1);\"/></td>";
    echo "</tr>";
    echo "</table></center>";
    echo "</div>";//div botones navegacion

    $path = "/matrix/hce/procesos/TableroAnt.php?empresa=" . $aplicacion . "&codemp=" . $wemp_pmla . "&historia=" . $aplicacionhce . "&accion=I&whis=<HIS>";
//$path = "/matrix/HCE/procesos/HCE_iFrames.php?accion=M&ok=0&empresa=".$aplicacionhce."&wcedula=<DOC>&wtipodoc=<TDOC>";
    echo "<span style='float:right;' id='enlace_hce'><A style='cursor:pointer; color: blue;' url='" . $path . "' onClick='ejecutar2(this)'><b>Ir a la HCE</b></A></span><br>";
    //DATOS INGRESO - DATOS EGRESO
    echo "<div id='div_datos_ing_egr'>";
    echo "<h3>DATOS DEL PACIENTE</h3>";
    echo "<div id='div_int_ing_egr'>";
//datos de ingreso
    echo "<div id='div_datos_ingreso'>";

    echo "<center><table width='75%'>";
    echo "<tr><th class='encabezadotabla' colspan='8'>Datos de basicos</th></tr>";
    echo "<tr class='fila1'>";
    echo "<td style='width:100px'>Historia</td>";
    echo "<td style='width:80px'>Ingreso</td>";
    echo "<td>Primer Nombre</td>";
    echo "<td>Segundo Nombre</td>";
    echo "<td>Primer Apellido</td>";
    echo "<td>Segundo Apellido</td>";
    echo "</tr>";

    echo "<tr class='fila1'>";
    echo "<td><input type='text' name='egr_histxtNumHis' id='egr_histxtNumHis' class=''  ux='_ux_egrhis' onblur='' value='" . $historia . "'></td>";
    echo "<td><input type='text' name='egr_ingtxtNumIng' id='egr_ingtxtNumIng' class=''  ux='_ux_egrnum' onblur='' value='" . $ingreso . "'></td>";
    echo "<td><input type='text' msgcampo='Primer Nombre' name='pac_no1txtPriNom' id='pac_no1txtPriNom' class='reset' msgError='' alfabetico ux='_ux_pnom1_ux_midno1' onblur=''></td>";
    echo "<td><input type='text' name='pac_no2txtSegNom' id='pac_no2txtSegNom' class='reset' msgError='' alfabetico ux='_ux_pnom2_ux_midno2' onblur=''></td>";
    echo "<td><input type='text' msgcampo='Primer Apellido' name='pac_ap1txtPriApe' id='pac_ap1txtPriApe' class='reset' msgError='' alfabetico ux='_ux_pacap1_ux_midap1' onblur=''></td>";
    echo "<td><input type='text' name='pac_ap2txtSegApe' id='pac_ap2txtSegApe' class='reset' msgError='' alfabetico ux='_ux_pacap2_ux_midap2' onblur=''></td>";
    echo "</tr>";

    echo "<tr class='fila1'>";
    echo "<td colspan=2 align='center'>Tipo Documento</td>";
    echo "<td align='center'>N&uacute;mero Documento</td>";
    echo "<td>Edad</td>";
    echo "<td>Sexo</td>";
    echo "<td colspan=2>Entidad</td>";
    echo "</tr>";

    echo "<tr class='fila1'>";
    $param = "class='reset' msgError ux='_ux_pactid_ux_midtii' ";
    echo "<td colspan=2>";
    $resTiposDoc = consultaMaestros('root_000007', 'Codigo,Descripcion', $where = "Estado='on'", '', '');
    echo crearSelectHTMLAcc($resTiposDoc, 'pac_tdoselTipoDoc', 'pac_tdoselTipoDoc', $param);
    echo "</td>";
    echo "<td><input type='text' name='pac_doctxtNumDoc' id='pac_doctxtNumDoc' class='reset' msgError='' ux='_ux_pacced_ux_midide' onblur=''></td>";
    echo "<td><input type='text' msgcampo='Edad' name='pac_edatxtEdad' id='pac_edatxtEdad' class=''  onblur='' value=''></td>";
    echo "<td><input type='text' msgcampo='Sexo' name='pac_sextxtSexo' id='pac_sextxtSexo' class=''  onblur='' value=''></td>";
    echo "<td colspan=2><input type='text' msgcampo='Entidad' name='pac_epstxtEps' id='pac_epstxtEps' class='' onblur='' value=''>
<input type='hidden' name='pac_epshidEps' id='pac_epshidEps'></td>";
    echo "</tr>";

    echo "<tr class='fila1'>";
    echo "<td>Fecha de Ingreso</td>";
    echo "<td>Hora de Ingreso</td>";
    echo "<td>Fecha de Egreso</td>";
    echo "<td>Hora de Egreso</td>";
    echo "<td>Dias de Estancia</td>";
    echo "<td align='center'>Causa de Egreso</td>";
    echo "</tr>";

    echo "<tr class='fila1'>";
    echo "<td><input type='text' name='ing_feitxtFecIng' id='ing_feitxtFecIng' fecha onChange='calcularEstancia( \"si\");' value='" . $fechaIngresoSugerida . "' class=''></td>";
    echo "<td><input type='text' name='ing_hintxtHorIng' id='ing_hintxtHorIng' hora onChange='calcularEstancia( \"si\");' value='" . $horaAct . "' class=''></td>";
    echo "<td><input type='text' name='egr_feetxtFecEgr' id='egr_feetxtFecEgr' fecha onChange='calcularEstancia( \"si\");' ux='_ux_pacnac_ux_midnac' value='" . $fechaAltDefinitiva . "'></td>";
    echo "<td class='fila2'><input type='text' name='egr_hoetxtHorEgr' id='egr_hoetxtHorEgr' onChange='calcularEstancia( \"si\");' hora value='" . $horaAltDefinitiva . "' class=''></td>";
    echo "<td class='fila2'><input type='text' name='egr_esttxtestan' id='egr_esttxtestan' class='' disabled msgError='' ux='_ux_egrdes_ux_egrdfa_ux_hosdes'></td>";
    $param = "class='reset' ux='_ux_egrcau' msgcampo='Causa de Egreso' onChange='validarTiempoEgreso( this );' ";
    echo "<td>";
    $res1 = consultaMaestros('000105', 'Selcod,Seldes', $where = "Seltip = '10' and Selest='on'", '', 'Seldes', '2');
    $atributosCausa = " onchange =habilitarDeshabilitarCBM(this.id) ";
    echo crearSelectHTMLAcc($res1, 'egr_caeselCauEgr', 'egr_caeselCauEgr', $param, $atributosCausa);
    echo "</td>";
    echo "</tr>";
    echo "</table></center>";
    echo "</div>"; //datos ingreso
    /*REVISAR FRODO QUITAR CODIGO JS QUE TENGA QUE VER CON ESTOS CAMPOS
$param="class='reset' msgError ux='_ux_hoscex'";
echo "<td colspan='2'>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '12' and Selest='on'",'','Selcod','2');
echo crearSelectHTMLAcc($res1,'egr_cexselCauExt','egr_cexselCauExt',$param);
echo "</td>";
echo "<td><input type='text' style='width:80px;' name='egr_fiatxtFecInA' id='egr_fiatxtFecInA' fecha value='".$fechaAct."' class=''></td>";

echo "<td><input type='text' name='egr_meitxtMedIng' id='egr_meitxtMedIng' value='' class='' msgError='Digite el m&eacute;dico de ingreso'>";
echo "<input type='hidden' name='egr_meihidMedIng' id='egr_meihidMedIng' ux='_ux_egrmei'></td>"; //autocompletar
echo "<td><input type='text' name='egr_dxitxtDiaIng' id='egr_dxitxtDiaIng' value='' class='' msgError='Digite el diagn&oacute;stico'>";
echo "<input type='hidden' name='egr_dxihidDiaIng' id='egr_dxihidDiaIng' ux='_ux_egrdin_ux_hosdxi'></td>";//autocompletar
*/

    /*
echo "</tr>";
echo "</table></center>";
echo "</div>"; //datos ingreso

//datos egreso
echo "<div id='div_datos_egreso'>";
echo "<center><table id='datos_basicos_egreso'>";
echo "<th class='encabezadotabla' colspan='8'>Datos de Egreso</th>";
echo "<tr>";
echo "<td class='fila1' style='width:7%'>Fecha de Egreso</td>";
echo "<td class='fila1' style='width:7%'>Hora egreso</td></td>";
echo "<td class='fila1'>M&eacute;dico de Egreso</td>";
echo "<td class='fila1' style='width:5%'>Estancia</td>";
echo "<td class='fila1' >Causa Egreso</td></td>";
echo "<td class='fila1' style='width:7%'>Fecha Terminaci&oacute;n Atenci&oacute;n</td>";
echo "<td class='fila1'>Tipo de diagn&oacute;stico Principal</td>";
echo "<td class='fila1' style='width:5%'>Complicaciones</td>";
echo "</tr>";
echo "<tr>";
echo "<td>";
echo "<input type='text' name='egr_meetxtMedEgr' id='egr_meetxtMedEgr' class='' msgError='Digite el m&eacute;dico de egreso'>";
echo "<input type='hidden' name='egr_meehidMedEgr' id='egr_meehidMedEgr' ux='_ux_egrmed'>";
echo "</td>";
echo "<td>";


echo "<td><input type='text' name='egr_ftatxtFecTeA' id='egr_ftatxtFecTeA' fecha onChange='' ux='_ux_pacnac_ux_midnac' value='".$fechaAct."'></td>";
$param="class='reset' ux='_ux_pacest'";
echo "<td>";$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '13' and Selest='on'",'','Selpri','2');
echo crearSelectHTMLAcc($res1,'egr_tdpselTipDiP','egr_tdpselTipDiP',$param);
echo "</td>";
echo "<td>";
echo "<table width='100%' border='0'>";
echo "<tr><td width='50%' align='center' style='font-size: 11px;' nowrap>Si &nbsp; <input type='radio' style='width:14px;height:12px;' name='egr_comradCon' id='egr_comradConS' value='on' onclick='' ux='_ux_pacsex_ux_midsex'>";
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No &nbsp; <input type='radio' style='width:14px;height:12px;' name='egr_comradCon' id='egr_comradConN' value='off' onclick='' ux='_ux_pacsex_ux_midsex'></td></tr>";
echo "</table>";
echo "</td>";
echo "</tr>";
echo "</table></center>";
echo "</div>"; //datos egreso
*/
    echo "</div>"; //interno
    echo "</div>"; //externo


//DIAGNOSTICOS
    echo "<div id='div_datos_diagnosticos'>";
    echo "<h3>DIAGN&Oacute;STICOS</h3>";
//datos diagnosticos
    echo "<div id='div_int_datos_diag'>";
    echo "<center><table width='40%' id='tabla_observacion_diagnostico'>";
    echo "<th class='encabezadotabla' colspan='5'>Observaci&oacute;n Diagn&oacute;sticos</th>";
    echo "<tr>";
    echo "<td><textarea rows='3' cols='70' name='txtaObsDia' id='txtaObsDia'></textarea></td>";
    echo "</tr>";
    echo "</table></center>";
    echo "<br>";
    echo "<center><table width='90%' id='tabla_diagnostico'>";
    echo "<th style='width=5%;'>&nbsp;</th>
      <th class='encabezadotabla' colspan='6'><font size=4>LISTA DE DIAGN&Oacute;STICOS</font></th>
      <th style='width:3%;' class='encabezadotabla'>
        <span id=\"spn_tabla_diagnostico\" onclick=\"addFila2('tabla_diagnostico');\" class=''>" . NOMBRE_ADICIONAR . "</span>
      </th>";
    echo "<tr class='fila_diagnosticos fila_principal'>"; //INICIA FILA DE UN DIAGNOSTICO
    echo "<td colspan=8>";

//INICIA TABLA CON TODA LA INFORMACION DE UN DIAGNOSTICO
    echo "<table>";
    echo "<tr>
        <td style='width=5%;'>&nbsp;</td>
        <td class='encabezadotabla' align='center' colspan=7>Diagn&oacute;stico</td>
    </tr>";
    echo "<tr>";
    echo "<td style='width:5%;' class='numerodiagnostico corchete' rowspan=6>DX1</td>";
    echo "<td class='fila1' width='14%'>C&oacute;digo DX</td>";
    echo "<td class='fila1' width='35%'>Nombre DX</td>";
    echo "<td class='fila1' width='12%'>Principal/Secundario</td>";
    echo "<td class='fila1' width='13%'>Nuevo</td>";
    echo "<td class='fila1' width='7%'>EISP</td>";
    echo "<td class='fila1' width='10%'>Complicaciones</td>";
    echo "<td class='fila1' width='10%'>CBM</td>";
    echo "<td class='fila2' rowspan='6' align='center' width='4%' ><span class='' onclick='removerFila2(this,\"fila_diagnosticos\",\"tabla_diagnostico\");'>" . NOMBRE_BORRAR . "</span></td>";
    echo "</tr>";
    /***************************************************************
     * SE HACEN LAS CONSULTAS DE TODO LO QUE YA EXISTA DE DIAGNOSTICOS
     * TAMBIEN SE IMPRIMEN TODAS LAS FILAS LEIDAS DE LA BASE DE DATOS
     ***************************************************************/
    /*si no tiene registros*/

    echo "<tr class='fila2'>";
    echo "<td>
    <input type='text' msgcampo='Codigo diagnostico' name='dia_cod_txtCodDia' ux='_ux_diadia' msgError='C&oacute;digo del diagn&oacute;stico'>
    <input type='hidden' name='dia_codhidCodDia' ux='_ux_plug'>
    </td>";
    echo "<td>
    <input type='text' msgcampo='Descripcion diagnostico' name='DesDia_txtDesDia' msgError='Descricpi&oacute;n del diagn&oacute;stico'>
    </td>";

    $param = "class='reset principalsecundario' ux='_ux_diatip' onChange='validacionPrinSecu2( \"fila_diagnosticos\", this )' ";
    echo "<td><input type='hidden' id=_bd name=_bd value='' >";
    $res1 = consultaMaestros('000105', 'Selcod,Seldes', $where = "Seltip = '29' and Selest='on'", '', 'Seldes', '2');
    echo crearSelectHTMLAcc($res1, 'dia_tip_selTipDia', 'dia_tip_selTipDia', $param);
    echo "</td>";

    $param = "class='reset'";
    echo "<td>";
    echo "<table width='100%' border='0'>";
    echo "<tr><td width='50%' align='center' style='font-size: 11px;' nowrap>
            Si &nbsp; <input type='radio' msgcampo='Diagnostico Nuevo (S/N)' style='width:14px;height:12px;' name='dia_nue_selNueDia' id='dia_nue_radS' ux='_ux_diainf' value='S' onclick='' msgError=''>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            No &nbsp; <input type='radio' msgcampo='Diagnostico Nuevo (S/N)' style='width:14px;height:12px;' name='dia_nue_selNueDia' id='dia_nue_radN' ux='_ux_diainf' value='N' onclick='' msgError=''>
        </td></tr>";
    echo "</table>";
//<input type='hidden' id=_bd name=_bd value='' >";
//$res1=consultaMaestros('000105','Selcod,Seldes',$where="Seltip = '30' and Selest='on'",'','Seldes','2');
//echo crearSelectHTMLAcc($res1,'dia_nue_selNueDia','dia_nue_selNueDia',$param);
    echo "</td>";

    echo " <td><input type='checkbox' name='dia_inf_chInfDia'  ux='_ux_diainf'></td>";
    echo "<td>";
    echo "<table width='100%' border='0'>";
    echo "<tr><td width='50%' align='center' style='font-size: 11px;' nowrap>
            Si &nbsp; <input type='radio' msgcampo='Diagnostico Complicaciones (S/N)' style='width:14px;height:12px;' name='dia_com_radCom' id='dia_com_radComS' value='S' onclick='' ux='_ux_diacom' msgError=''>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            No &nbsp; <input type='radio' msgcampo='Diagnostico Complicaciones (S/N)' style='width:14px;height:12px;' name='dia_com_radCom' id='dia_com_radConN' value='N' onclick='' ux='_ux_diacom' msgError=''>
        </td></tr>";
    echo "</table>";
    echo "</td>";
    echo " <td><input type='checkbox' name='dia_cbm_checkbox'id='dia_cbm_checkbox' class='checkbox-unico cbm_checkbox_class'  onclick='marcarUnicoCheckbox(this)'></td>"; // Causa basica de muerte (CBM)

//PENDIENTE FRODO echo" <td><input type='checkbox' name='dia_egr_chDiaEgr' id='dia_egr_chDiaEgr' ux='_ux_dxegr' onClick='validacionDiagnosticoEgreso(this)'></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td class='fila1'>Cod. M&eacute;dico</td>";
    echo "<td class='fila1'>Nombre del m&eacute;dico</td>";
    echo "<td colspan=5 class='fila1'>Especialidad</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>
    <input type='text' name='dia_med_txtCodMed' msgcampo='Diagnostico Codigo medico' msgError='C&oacute;digo del medico'>
    <input type='hidden' name='dia_medhidCodmed'>
    </td>";
    echo "<td>
    <input type='text' name='DesMed_txtDesMed' msgcampo='Diagnostico Nombre medico' msgError='Nombre del medico'>
    </td>";
    echo "<td colspan=5>
    <input type='text' name='Desesm_txtDesesm' msgcampo='Diagnostico Especialidad medico' msgError='Especialidad del medico'>
    <input type='hidden' name='dia_esmhidCodesm'>
    </td>";
    echo "</tr>";

//--><input type='hidden' id='sed_ser_hidCodSer' name='sed_ser_hidCodSer' value='' > esto va en el contenedor_servicios_ocultos
    echo "<tr>";
    echo "<td class='fila1' colspan='7' tipo='td_adicionar'>Servicio origen del Diagn&oacute;stico&nbsp;<img src='../../images/medical/root/adicionar2.png' tipo='td_adicionar' height='20' width='20' border=0 onClick=\"mostrarServiciosDiag(this);\"/></td><td colspan='6' name='contenedor_servicios_ocultos' nombreCampos='sed_ser_hidCodSer' style='display:none;'>
      </td>";
    echo "</tr>";

//--><input type='text' msgcampo='Nombre del servicio' style='font-size:12px;' name='diaSer_txtdiaSer' id='diaSer_txtdiaSer' msgError='Descripci&oacute;n del servicio'>


    echo "</table>"; //FIN TABLA CON INFO DE UN SOLO DIAGNOSTICO
    echo "</td>";
    echo "</tr>";

    echo "</table>";
    echo "</center>";
    echo "<div id='divMenDiag' style='display:none' align='center' class='div_error'></div>";
    echo "</div>";
    echo "</div>";


//PROCEDIMIENTOS
    echo "<div id='div_datos_procedimientos'>";
    echo "<h3>PROCEDIMIENTOS</h3>";
    echo "<div id='div_int_datos_proc'>";
    echo "<center><table width='40%' id='tabla_observacion_procedimiento'>";
    echo "<th class='encabezadotabla' colspan='5'>Observaci&oacute;n Procedimientos</th>";
    echo "<tr>";
    echo "<td><textarea rows='3' cols='70' name='txtaObsPro' id='txtaObsPro'></textarea></td>";
    echo "</tr>";
    echo "</table></center>";
    echo "<br>";


    echo "<center>
        <table width='60%' id='tabla_procedimiento'>";
    echo "<tr>";
    echo "<th style='width=5%;'>&nbsp;</th>
      <th class='encabezadotabla' colspan='5'>LISTA DE PROCEDIMIENTOS</th>";
    echo "<th class='encabezadotabla'>
        <span id=\"spn_tabla_procedimiento\" onclick=\"addFila2('tabla_procedimiento');\" class='' >" . NOMBRE_ADICIONAR . "</span>
      </th>
      </tr>";

    echo "<tr class='fila_procedimientos fila_principal'>";
    echo "<td colspan=8>";

//INICIA TABLA CON TODA LA INFORMACION DE UN PROCEDIMIENTO
    echo "<table>";
    echo "<tr>
        <td style='width=10%;'>&nbsp;</td>
        <td class='encabezadotabla' align='center' colspan=6>Procedimiento</td>
    </tr>";

    echo "<tr>";
    echo "<td style='width:10%;' class='numeroprocedimiento corchete' rowspan=8>P1</td>";
    echo "<td class='fila1' width='15%'>C&oacute;digo Proc.</td>";
    echo "<td class='fila1' width='40%'>Nombre Proc.</td>";
    echo "<td class='fila1' width='40%'>Fecha</td>";
    echo "<td class='fila1' width='10%'>Principal/Secundario</td>";
    echo "<td class='fila1' width='10%'>Quirurgico</td>";
    echo "<td class='fila2' rowspan=8 align='center' width='4%' ><span class='' onclick='removerFila2(this,\"fila_procedimientos\",\"tabla_procedimiento\");'>" . NOMBRE_BORRAR . "</span></td>";
    echo "</tr>";
    /***************************************************************
     * SE HACEN LAS CONSULTAS DE TODO LO QUE YA EXISTA DE DIAGNOSTICOS
     * TAMBIEN SE IMPRIMEN TODAS LAS FILAS LEIDAS DE LA BASE DE DATOS
     ***************************************************************/

    echo "<tr class='fila2'>";
    echo " <td><input type='text' name='pro_cod_txtCodPro' id='pro_cod_txtCodPro' ux='_ux_propro' placeholder='C&oacute;digo del procedimiento'>
<input type='hidden' name='pro_cod_hidCodPro' id='pro_cod_hidCodPro'></td>";
    echo " <td><input type='text' name='ProDes_txtProDes' id='ProDes_txtProDes' placeholder='Descripci&oacute;n del procedimiento'></td>";
    echo "<td><input type='text' name='pro_fec_txtFecPro' fecha value='" . $fechaAct . "' class=''></td>";
//$param="class='reset principalsecundario' ux='_ux_protip' onChange='validacionPrinSecu2( \"fila_procedimientos\", this )'";--> permitir varios principales 2018-09-04
    $param = "class='reset principalsecundario' ux='_ux_protip'";
    echo "<td class='fila2'><input type='hidden' id=_bd name=_bd value='' >";
    $res1 = consultaMaestros('000105', 'Selcod,Seldes', $where = "Seltip = '29' and Selest='on'", '', 'Seldes', '2');
    echo crearSelectHTMLAcc($res1, 'pro_tip_selTipPro', 'pro_tip_selTipPro', $param);
    echo "</td>";

    echo "<td>";
    echo "<table width='100%' border='0'>";
    echo "<tr><td width='50%' align='center' style='font-size: 11px;' nowrap>
            Si &nbsp; <input type='radio' style='width:14px;height:12px;' name='pro_qui_radPro' id='pro_qui_radProS' value='S' onclick='' placeholder=''>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            No &nbsp; <input type='radio' style='width:14px;height:12px;' name='pro_qui_radPro' id='pro_qui_radProN' value='N' onclick='' placeholder=''>
        </td></tr>";
    echo "</table>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td class='fila1'>Cod. M&eacute;dico</td>";
    echo "<td class='fila1'>Nombre del m&eacute;dico</td>";
    echo "<td colspan=3 class='fila1'>Especialidad</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>
    <input type='text' name='pro_med_txtCodMed' placeholder='C&oacute;digo del medico'>
    <input type='hidden' name='pro_medhidCodmed'>
    </td>";
    echo "<td>
    <input type='text' name='DesMed_txtDesMedP' placeholder='Nombre del medico'>
    </td>";
    echo "<td colspan=3>
    <input type='text' name='Desesm_txtCodesm' placeholder='Especialidad del medico'>
    <input type='hidden' name='pro_esmhidCodesm'>
    </td>";
    echo "</tr>";


    echo "<tr>";
    echo "<td class='fila1'>Cod. Anestesi&oacute;logo</td>";
    echo "<td colspan=4 class='fila1'>Nombre del Anestesi&oacute;logo</td>";
    echo "</tr>";
    echo "<tr>";

    echo "<td>
    <input type='text' name='pro_ane_txtCodMed' placeholder='C&oacute;digo del anestesiologo'>
    <input type='hidden' name='pro_anehidCodmed'>
    </td>";
    echo "<td colspan=4>
    <input type='text' name='DesAne_txtDesMed' placeholder='Nombre del anestesiologo'>
    </td>";
//--> Servicio de procedimientos (serpro)
    echo "</tr>";
    echo "<td class='fila1' width='10%' colspan='5'>Servicio de Realizaci&oacute;n</td>";
    echo "<tr>";
    echo "<td colspan='5'><input type='text' msgcampo='Nombre del servicio' name='proSer_txtDesSer' id='proSer_txtDesSer' style='font-size:12px;' msgError='Descripci&oacute;n del servicio'>
      <input type='hidden' id='pro_ser_hidCodSer' name='pro_ser_hidCodSer' value='' ></td>";
    echo "</tr>";

    echo "</table>";
    echo "</td>";
    echo "</tr>";

    echo "</table>";
    echo "</center>";
    echo "<div id='divMenProc' style='display:none' align='center' class='div_error'></div>";
    echo "</div>";
    echo "</div>";


//ESPECIALIDADES
    echo "<div id='div_datos_especialidades'>";
    echo "<h3>ESPECIALISTAS</h3>";
    echo "<div id='div_int_datos_espe'>"; //interno

    echo "<center>";
    echo "<table width='100%'><tr><th class='encabezadotabla' colspan='7'>Especialidades</th></tr></table>";
    echo "<table width='100%' id='tabla_especialidad'>";
    echo "<tr>";
    echo "<td class='fila1' width='15%'>C&oacute;digo del medico</td>";
    echo "<td class='fila1' width='30%'>Nombre del medico</td>";
    echo "<td class='fila1' width='10%'>C&oacute;digo especialidad</td>";
    echo "<td class='fila1' width='20%'>Nombre especialidad</td>";
    echo "<td class='fila1' width='10%'>Principal/Secundario</td>";
    echo "<td class='fila1' width='5%' align='center'>Servicios</td>";
    echo "<td class='fila1' width='10%'>Medico de Ingreso</td>";
    echo "<td class='fila1' width='10%'>Medico Tratante</td>";
    echo "<td class='fila1' width='10%'>Medico de Egreso</td>";
    echo "<td width='3%' class='fila2' align='center'><span id=\"spn_tabla_especialidad\" onclick=\"addFila2('tabla_especialidad');\" class=''>" . NOMBRE_ADICIONAR . "</span></td>
</tr>";
    /***************************************************************
     * SE HACEN LAS CONSULTAS DE TODO LO QUE YA EXISTA DE DIAGNOSTICOS
     * TAMBIEN SE IMPRIMEN TODAS LAS FILAS LEIDAS DE LA BASE DE DATOS
     ***************************************************************/

    echo "<tr class='fila_especialidades fila_principal'>";
    echo " <td><input type='text' msgcampo='Especialidad Codigo medico' name='esp_med_txtCodEsp'msgError='C&oacute;digo del medico'>
        <input type='hidden' name='esp_med_hidCodEsp' id='esp_med_hidCodEsp'></td>";
    echo " <td><input type='text' msgcampo='Especialidad Nombre medico' name='DesMed_txtDesEsp' msgError='Nombre del medico'></td>";
    echo " <td><input type='text' msgcampo='Especialidad Codigo especialidad' name='esp_cod_txtCodEsp' id='esp_cod_txtCodEsp' ux='_ux_espesp' msgError='C&oacute;digo de la especialidad'>
<input type='hidden' name='esp_cod_hidCodEsp' id='esp_cod_hidCodEsp'></td>";
    echo " <td><input type='text' msgcampo='Especialidad Nombre especialidad' name='DesEsp_txtDesEsp' id='DesEsp_txtDesEsp' msgError='Nombre de la especialidad'></td>";

    $param = "class='reset principalsecundario' ux='_ux_esptip' onChange='validacionPrinSecu2( \"fila_especialidades\", this )'";
    echo "<td class='fila2'><input type='hidden' id=_bd name=_bd value='' >";
    $res1 = consultaMaestros('000105', 'Selcod,Seldes', $where = "Seltip = '29' and Selest='on'", '', 'Seldes', '2');
    echo crearSelectHTMLAcc($res1, 'esp_tip_selTipEsp', 'esp_tip_selTipEsp', $param);
    echo "</td>";
//--> Servicio de especialidades (seresp)
    echo "<td align='center' tipo='td_adicionar'><IMG id='imgAdicionar_servicio' SRC='../../images/medical/root/adicionar2.png' WIDTH='18' HEIGHT='18' onclick='mostrarServiciosDiag(this);' /></td><td name='contenedor_servicios_ocultos' nombreCampos='see_ser_hidCodSer' style='display:none;'></td>";
//--><input type='hidden' id='esp_ser_hidCodSer' name='esp_ser_hidCodSer' value='' >para agregar servicios

    /*echo" <td><input type='text' msgcampo='Nombre del servicio' name='espSer_txtDesSer' style='font-size:10px;' id='espSer_txtDesSer' msgError='Descripci&oacute;n del servicio'>
      <input type='hidden' id='esp_ser_hidCodSer' name='esp_ser_hidCodSer' value='' ></td>";*/

    echo "<td><input type='radio' msgcampo='Especialidad Medico de Ingreso (Radio)' name='med_meiradio'></td>";
    echo "<td><input type='radio' msgcampo='Especialidad Medico Tratante (Radio)' name='med_traradio'></td>";
    echo "<td><input type='radio' msgcampo='Especialidad Medico de Egreso (Radio)' name='med_egrradio'></td>";
    echo "<td align='center' width='3%' class='fila2'><span class='' onclick='removerFila2(this,\"fila_especialidades\",\"tabla_especialidad\");'>" . NOMBRE_BORRAR . "</span></td>";
    echo "</tr>";
    // } si no tiene registros

    echo "</tr>"; //tr despues del th
    echo "</table>";
    echo "</center>";
    echo "<div id='divMenEspe' style='display:none' align='center' class='div_error'></div>";
    echo "</div>";
    echo "</div>";

//SERVICIOS
    echo "<input type='hidden' name='servicioEgreso' ux='_ux_egrseg' value=''>";
    echo "<div id='div_datos_servicios'>";
    echo "<h3>SERVICIOS</h3>";
    echo "<div id='div_int_datos_serv'>";
    echo "<center>";
    echo "<table width='50%'><tr><th class='encabezadotabla' colspan='3'>Servicios</th></tr></table>";
    echo "<table width='50%' id='tabla_servicio'>";
//echo "<th class='encabezadotabla' colspan='3'>Servicios</th>";
    echo "<tr>";
    echo "<td class='fila1' width='15%'>C&oacute;digo</td>";
    echo "<td class='fila1' width='40%'>Descripci&oacute;n</td>";
    echo "<td class='fila1' width='10%'> serv. Egreso</td>";
    echo "<td width='3%' class='fila2' align='center'><span id=\"spn_tabla_servicio\" onclick=\"addFila2('tabla_servicio');\" class=''>" . NOMBRE_ADICIONAR . "</span></td>";
    /***************************************************************
     * SE HACEN LAS CONSULTAS DE TODO LO QUE YA EXISTA DE DIAGNOSTICOS
     * TAMBIEN SE IMPRIMEN TODAS LAS FILAS LEIDAS DE LA BASE DE DATOS
     ***************************************************************/
    echo "</tr>";
    echo " <tr class='fila_servicios fila_principal'>";
    echo "<td>
<input type='text' msgcampo='Codigo del servicio' name='ser_cod_txtCodSer' id='ser_cod_txtCodSer' msgError='C&oacute;digo del servicio'>
<input type='hidden' id='ser_cod_hidCodSer' name='ser_cod_hidCodSer' value='' ></td>";
    echo " <td><input type='text' msgcampo='Nombre del servicio' name='DesSer_txtDesSer' id='DesSer_txtDesSer' msgError='Descripci&oacute;n del servicio'></td>";
    echo " <td align='center'><input type='radio' name='ser_egrradio' id='ser_egrradio' value='' onclick='seleccionarComoServicioEgreso( this );'></td>";
    echo " <td align='center' width='3%' class='fila2'><span class='' onclick='removerFila2(this,\"fila_servicios\",\"tabla_servicio\");'>" . NOMBRE_BORRAR . "</span></td>";
    echo "</tr>";
    // } si no tiene registros

    echo "</tr>"; //tr despues del th
    echo "</table>";
    echo "</center>";
    echo "<div id='divMenServ' style='display:none' align='center' class='div_error'></div>";
    echo "</div>";
    echo "</div>";


//UBICACION DEL EXPEDIENTE FISICO
    echo "<div id='div_datos_expediente_fisico'>";
    echo "<h3>UBICACI&Oacute;N DEL EXPEDIENTE F&Iacute;SICO</h3>";
    echo "<div id='div_int_datos_expe'>";
    echo "<center><table width='50%' id='tabla_expediente'>";
    echo "<th class='encabezadotabla' colspan='8'>Ubicaci&oacute;n del expediente f&iacute;sico</th>";
    echo "<tr>";
    echo "<td class='fila1' width='8%'>Gesti&oacute;n";
    echo "&nbsp; <input type='radio' msgcampo='Ubicacion del expediente' style='width:14px;height:12px;' name='egr_uexradUbiExp' id='egr_uexradUbiExpG' value='Gestion' onclick='' checked='checked'></td>";
    echo "<td class='fila1' width='8%'>Fallecidos";
    echo "&nbsp; <input type='radio' msgcampo='Ubicacion del expediente' style='width:14px;height:12px;' name='egr_uexradUbiExp' id='egr_uexradUbiExpF' value='Fallecidos' onclick='' ></td>";
    echo "<td class='fila1' width='8%'>Reserva";
    echo "&nbsp; <input type='radio' msgcampo='Ubicacion del expediente' style='width:14px;height:12px;' name='egr_uexradUbiExp' id='egr_uexradUbiExpR' value='Reserva' onclick='' ></td>";
    echo "<td class='fila1' width='8%'>Central";
    echo "&nbsp; <input type='radio' msgcampo='Ubicacion del expediente' style='width:14px;height:12px;' name='egr_uexradUbiExp' id='egr_uexradUbiExpC' value='Central' onclick='' ></td>";
    echo "<td class='fila1' width='8%'>Hist&oacute;rico";
    echo "&nbsp; <input type='radio' msgcampo='Ubicacion del expediente' style='width:14px;height:12px;' name='egr_uexradUbiExp' id='egr_uexradUbiExpH' value='Historico' onclick='' ></td>";
    echo "</tr>"; //tr despues del th
    echo "</table>";
    echo "</center>";
    echo "</div>";
    echo "</div>";

//AUTORIZACIONES
    $msjPermisoUsuario = consultarAliasPorAplicacion($conex, $wemp_pmla, "preguntaPermisoEgreso");
    echo "<div id='div_datos_autorizaciones'>";
    echo "<h3>AUTORIZACIONES PARA ACCEDER A LA HISTORIA </h3>";
    echo "<div id='div_int_datos_autorizaciones'>";
    echo "<center><table width='50%' id='tabla_autorizaciones'>";
    echo "<tr><th class='encabezadotabla' colspan='8'>Autorizaciones</th></tr>";
    echo "<tr>";
    echo "<td class='fila1' colspan=3>{$msjPermisoUsuario}</td>";
    echo "<td class='fila2'>";
    echo "<table width='100%' border='0'>";
    echo "<tr><td width='50%' align='center' style='font-size: 11px;' nowrap>
            Si &nbsp; <input egresoAutomatico='no' type='radio' msgcampo='Autoriza informacion o publicidad' style='width:14px;height:12px;' name='aut_inf_radAut' id='aut_inf_radAutS' value='on' onclick='' msgaqua=''>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            No &nbsp; <input egresoAutomatico='no' type='radio' msgcampo='Autoriza informacion o publicidad' style='width:14px;height:12px;' name='aut_inf_radAut' id='aut_inf_radAutN' value='off' onclick='' msgaqua=''>
        </td></tr>";
    echo "</table>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td class='encabezadotabla' colspan=4 align='center'>
    Personas Autorizadas";
    echo "<span style='float:right' id=\"spn_tabla_diagnostico\" onclick=\"addFila2('tabla_personas_autorizadas');\" class=''>" . NOMBRE_ADICIONAR . "</span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr><td colspan=4><table width='100%' id='tabla_personas_autorizadas'>";

//personas autorizadas
    echo "<tr class='encabezadotabla'>";
    echo "<td>Tipo Doc.</td><td>Documento</td><td>Nombre</td><td>Parentesco</td>";
    echo "</tr>";
    echo "<tr class='fila2 fila_personas_autorizadas'>";
    echo "<td>";
    mysql_data_seek($resTiposDoc, 0);
    echo crearSelectHTMLAcc($resTiposDoc, 'dau_tdo', 'dau_tdo', "", "egresoAutomatico='no'");
    echo "<input  type='hidden' name='dau_tip'  egresoAutomatico='no' value='1' >"; //dautip es el tipo de persona, 1 para personas autorizadas
    echo "</td>";
    echo "<td><input type='text' name='dau_doc' msgaqua='Documento' egresoAutomatico='no'></td>";
    echo "<td><input type='text' name='dau_nom' msgaqua='Nombre' egresoAutomatico='no'></td>";

    $resPare = consultaMaestros('root_000103', 'Parcod,Pardes', $where = "Parest='on'", '', '');
    echo "<td>";
    echo crearSelectHTMLAcc($resPare, 'dau_par', 'dau_par', "", "egresoAutomatico='no'");
    echo "</td>";
    echo "</tr>";

    echo "</table>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td class='encabezadotabla' colspan=4 align='center'>
    Personas que reclaman historia";
    echo "<span style='float:right' id=\"spn_tabla_diagnostico\" onclick=\"addFila2('tabla_personas_reclaman');\" class=''>" . NOMBRE_ADICIONAR . "</span>";
    echo "</td>";
    echo "</tr>";

    echo "<tr><td colspan=4><table width='100%' id='tabla_personas_reclaman'>";
//personas que reclaman historia

    echo "<tr class='encabezadotabla'>";
    echo "<td>Tipo Doc.</td><td>Documento</td><td>Nombre</td><td>Parentesco</td>";
    echo "</tr>";
    echo "<tr class='fila2 fila_personas_reclaman'>";
    echo "<td>";
    mysql_data_seek($resTiposDoc, 0);
    echo crearSelectHTMLAcc($resTiposDoc, 'dau_tdo', 'dau_tdo', "", "egresoAutomatico='no'");
    echo "<input type='hidden' name='dau_tip' value='2'  egresoAutomatico='no'>"; //dautip es el tipo de persona, 2 para personas que reclaman
    echo "</td>";
    echo "<td><input type='text' name='dau_doc' msgaqua='Documento' egresoAutomatico='no'></td>";
    echo "<td><input type='text' name='dau_nom' msgaqua='Nombre'    egresoAutomatico='no'></td>";

    mysql_data_seek($resPare, 0);
    echo "<td> caraio";
    echo crearSelectHTMLAcc($resPare, 'dau_par', 'dau_par', "", "egresoAutomatico='no'");
    echo "</td>";
    echo "</tr>";

    echo "</table>";
    echo "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td class='encabezadotabla' colspan=4 align='center'>Observaciones</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td colspan=4 align='center' class='fila2'><textarea rows='3' cols='70' name='aut_obs' id='aut_obs' egresoAutomatico='no'></textarea></td>";
    echo "</tr>";

    echo "</table>";
    echo "</center>";
    echo "</div>";
    echo "</div>";


//OBSERVACIONES GENERALES
    echo "<div id='div_datos_observaciones_generales'>";
    echo "<h3>OBSERVACIONES GENERALES</h3>";
    echo "<div id='div_int_datos_observaciones_generales'>";
    echo "<center>";
    echo "<table><tr><td class='encabezadotabla' align='center'>Observaciones generales</td></tr>";
    echo "<tr><td><textarea rows='4' cols='80' name='egr_obg' ux='_ux_mepides' id='egr_obg'></textarea></td></tr></table>";
    echo "</center>";
    echo "</div>";
    echo "</div>";


//BOTONES
    echo "<div id='div_botones'>";
    echo "<center><table class='fondoamarillo' style='width:100%'>";
    echo "<tr>";
// echo "<td class='' align='center' ><input type='button' value='Iniciar' style='width:100;height:25' onClick=\"resetear();\">";
    echo "<td class='' align='center' ><input type='button' value='Iniciar' style='width:100;height:25' onClick=\"resetear();\">";
    echo "<input type='button' value='Consultar Egreso' style='width:120;height:25' onClick=\"mostrarDatosEgresos()\">";
    echo "<input type='button' id='btnEgresar' value='Egresar' style='width:120;height:25' onClick=\"enviarDatos();\">";
//boton de prueba
// echo "<input type='button' value='consultar1' style='width:100;height:25' onClick=\"mostrarDatos()\">";

// echo "<input type='button' value='Log' style='width:100;height:25'  onclick='llenarDatosLog();'></td>";
    echo "<input type='button' id='btnAnular' value='Anular Egreso' style='width:120;height:25' onClick=\"anularEgreso();\"></td>";
    echo "</tr>";
    echo "</table></center>";
    echo "</div>";

    echo "<br><br>";
    echo "<center><input type='button' value='Cerrar' style='width:120;height:25'  onclick='javascript: cerrarVentana();'></center>";

    echo "<br>";
    echo "<div id='bot_navegacion' style='display:none'>";
    echo "<center><table style='width:500;' border='0'>";
    echo "<th colspan='3' class='encabezadotabla'>Resultados de la busqueda</th>";
    echo "<tr class='fila1'>"; //Total Ingresos:<span id='spTotalIng'></span>
//echo "<td align='center' colspan='3'>Total Resultados:<span id='spTotalReg'></span>&nbsp;&nbsp;</td>";
    echo "</tr>";
    echo "<tr>";
//echo "<td align='center' colspan='3' class='fila1'>Resultado:<span id='spRegAct'></span>&nbsp;con historia: &nbsp;<span id='spHisAct'></span>&nbsp;Ingreso:&nbsp;<span id='spIngAct'></span>&nbsp;de&nbsp;<span id='spTotalIng1'></span></td>";
    echo "<td align='center' colspan='3' class='fila1'>Resultado:<span id='spRegAct'></span>&nbsp;de&nbsp;<span id='spTotalIng1'></span></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align='center' colspan='3'><img src='../../images/medical/citas/atras.jpg' height='30' width='30' border=0 onClick=\"navegacionIngresos(-1);\"/>";
    echo "&nbsp;<img src='../../images/medical/citas/adelante.jpg' height='30' width='30' border=0 onClick=\"navegacionIngresos(+1);\"/></td>";
    echo "</tr>";
    echo "</table></center>";
    echo "</div>";//div botones navegacion

    if ($aplicacion != "" and isset($aplicacion)) {
        $wbasedato_tcx = consultarAplicacion($conex, $wemp_pmla, "tcx");
        $query = " SELECT Ubisac
                 FROM {$aplicacion}_000018
                WHERE Ubihis = '{$historia}'
                  AND Ubiing = '{$ingreso}'
                  AND Ubiald = 'on'
                  AND Ubifad != '0000-00-00'";
        $rs = mysql_query($query, $conex) or die(mysql_error());
        $row = mysql_fetch_assoc($rs);
        if ($row['Ubisac'] != "")
            $ccoEgreso = $row['Ubisac'];

        $query = "SELECT ccocod, ccoing, ccohos, ccocir, ccourg
                FROM {$aplicacion}_000011
               WHERE ccocod = '{$row['Ubisac']}'";
        $rs = mysql_query($query, $conex) or die(mysql_error());
        $row = mysql_fetch_assoc($rs);

        if ($row['ccoing'] == "on" and $row['ccohos'] == "off" and $row['ccocir'] == "off" and $row['ccourg'] == "off") {
            //--> buscar en la tabla de turnos.
            $query = "SELECT Turtur, Turqui, quicco
                    FROM {$wbasedato_tcx}_000011 b
                   INNER JOIN
                         {$wbasedato_tcx}_000012 f on ( b.Turhis='$historia' and b.turnin ='$ingreso' and b.Turqui = f.Quicod and  turest = 'on' )
                    ORDER BY Turtur asc
                    LIMIT 1";
            //echo $query;
            $rs = mysql_query($query, $conex) or die(mysql_error());
            $row = mysql_fetch_assoc($rs);
            if ($row['quicco'] != "")
                $ccoEgreso = $row['ccocod'];
        }


    }
//echo " <br><br>cco de egreso -->: "+$ccoEgreso;
    echo "</div>"; //div_egresos

    echo "<div style='display:none;' id='div_parent_ccoEgreso'><input type='hidden' id='cco_egreso' value='$ccoEgreso'></div>";
    $serviciosDia = consultarServiciosDiagnosticos($ccoEgreso);
    echo "<div id='div_servicios_diagnostico' class='fila2' align='center' style='width:70%;display:none;'><br>";
    echo "<table id='tbl_servicios_diagnostico'>";
    echo "<tr tipo='titulo'><td align='right'>&nbsp;</td><td colspan='4' align='right'><img src='../../images/medical/root/lupa.png' height='20' width='20' border='0'/></td><td nowrap='nowrap' align='right'><input type='text' id='input_buscador_servicios' width='50%'></td></tr>";
    echo "<tr class='encabezadotabla' tipo='titulo'><th align='center' colspan='6'> SELECCI&Oacute;N DE SERVICIOS </th></tr>";
    echo "<tr class='encabezadotabla' tipo='titulo'>";
    echo "<th>&nbsp;</th>";
    echo "<th>Codigo</th>";
    echo "<th>Nombre</th>";
    echo "<th>&nbsp;</th>";
    echo "<th>Codigo</th>";
    echo "<th>Nombre</th>";
    $i = 0;
    foreach ($serviciosDia as $keyCodigoServicio => $datosServicio) {
        $i++;
        if (!is_int($i / 2))
            echo "<tr class='fila1'>";
        echo "<td align='center'><input type='checkbox' name='chk_servicio_dia' value='$keyCodigoServicio' onChange='agregarQuitarMultiplesServicios( this );'></td>";
        //echo "<td align='center'><input type='checkbox' name='chk_servicio_dia' value='$keyCodigoServicio'></td>";
        echo "<td align='center'>$keyCodigoServicio</td>";
        echo "<td align='left'>{$datosServicio['descripcion']}</td>";
        if (is_int($i / 2))
            echo "</tr>";
    }
    if (!is_int($i / 2))
        echo "</tr>";

    echo "</table>";
    echo "</div>";
    echo "</form>";
    echo "<div id='div_pruebas'>";
    echo "</div>";
    echo "<div id='msjAlerta' style='display:none;'>
            <br>
            <center><img src='../../images/medical/root/Advertencia.png'/></center>
            <br><br><center><div id='textoAlerta' style='font-size: 12pt;'></div></center><br>
        </div>";
    echo "<div id='msjAlerta2' style='display:none;'>
            <br>
            <center><img src='../../images/medical/root/Advertencia.png'/></center><br>
            <center><p>&#161; ATENCI&Oacute;N &#33;</p></center>
            <br><center><div id='textoAlerta2' style='font-size: 12pt;'></div></center><br>
        </div>";
    echo "</body>";
    echo "</html>";
}


?>
