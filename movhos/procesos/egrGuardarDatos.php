<?php

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

function validarCirugiaSinLiquidar($historia, $ingreso)
{
    global $conex;
    global $wemp_pmla;
    global $wbasedato;

    $tieneCirugiaSinLiquidar = false;

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
            $tieneCirugiaSinLiquidar = true;
        }
    }
    return $tieneCirugiaSinLiquidar;

}

/**
 * Consulta los saldos de articulos de pacientes
 * La función retorna true si el paciente tiene articulos pendientes de lo contrario retorna false
 * @param $historia
 * @param $ingreso
 * @return bool
 */
function validarArticulosPaciente($historia, $ingreso)
{

    global $conex;
    global $aplicacion;

    $tienenArticulosPendientes = false;

    /*** Obtenemos el saldo de movhos_000004 de la historia y el ingreso**/
    $sql = "SELECT (Spauen-Spausa) AS saldo FROM {$aplicacion}_000004 SP WHERE SP.Spahis='{$historia}' AND SP.Spaing='{$ingreso}' AND (Spauen-Spausa)>0";
    $query = mysql_query($sql, $conex);

    $totalRegistros = mysql_num_rows($query);

    /** Si tiene registros con saldo **/
    if ($totalRegistros > 0) {
        $tienenArticulosPendientes = true;
    }

    return $tienenArticulosPendientes;

}

/**
 * @param $historia
 * @param $ingreso
 * @return bool
 */
function validarInsumosPaciente($historia, $ingreso)
{

    global $conex;
    global $aplicacion;

    $tieneInsumosPendientes = false;

    /*** Obtenemos el saldo de movhos_000227 de la historia y el ingreso**/
    $sql = "SELECT (Carcca-Carcap-Carcde) As saldo FROM {$aplicacion}_000227 IPA WHERE IPA.Carhis='{$historia}' AND IPA.Caring='{$ingreso}' AND (Carcca-Carcap-Carcde)>0";
    $query = mysql_query($sql, $conex);

    $totalRegistros = mysql_num_rows($query);

    /** Si tiene registros con saldo **/
    if ($totalRegistros > 0) {
        $tieneInsumosPendientes = true;
    }

    return $tieneInsumosPendientes;
}

function mostrarDatosAlmacenados($pacienteEgresar)
{
    global $conex;
    global $wemp_pmla;
    global $wbasedato;
    global $mostrarSalida;

    global $rowsesp;


    /***----- DEFINICION DE VARIABLES LOCALES ---------------------**/
    $historia = $pacienteEgresar->historia;
    $documento = $pacienteEgresar->documento;
    $cco_egreso = $pacienteEgresar->ccoEgreso;
    $ingreso = $pacienteEgresar->ingreso;
    $priApe = null;
    $segApe = null;
    $priNom = null;
    $segNom = null;

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

                                        }

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

    return $data;
}

function guardarDatos2($pacienteEgresar){
    global $conex;
    global $wemp_pmla;
    global $wbasedato;
    global $hay_unix;
    global $user;

    /***----- DEFINICION DE VARIABLES LOCALES ---------------------**/
    $historia = $pacienteEgresar->historia;
    $cco_egreso = $pacienteEgresar->ccoEgreso;
    $ingreso = $pacienteEgresar->ingreso;
    $documento = $pacienteEgresar->documento;
    $tipo_documento = $pacienteEgresar->tipo_documento;
    $paciente = $pacienteEgresar->paciente;

    $priApe = null;
    $segApe = null;
    $priNom = null;
    $segNom = null;

    $datosEnc = [];
    $data["error"] = 0;
    $data['mensaje'] = "";

    // Valida si tiene cirugias sin liquidar
    $tieneCirugiaSinLiquidar = validarCirugiaSinLiquidar($historia, $ingreso);

    /** Tiene articulos pendientes **/
    $tieneArticulosPendientes = validarArticulosPaciente($historia, $ingreso);

    /** Tiene insumos pendientes **/
    $tieneInsumosPendientes = validarInsumosPaciente($historia, $ingreso);

    /** Si tiene cirugias sin liquidar */
    if ($tieneCirugiaSinLiquidar) {
        $data["error"] = 1;
        $data["mensaje"] = "La historia " . $historia . " con el ingreso " . $ingreso . " tiene cirugias pendientes sin liquidar";

        return $data;
        exit;
    }

    /** Si tiene artituculos pendientes **/
    if ($tieneArticulosPendientes) {
        $data["error"] = 1;
        $data["mensaje"] = "La historia " . $historia . " con el ingreso " . $ingreso . " tiene articulos pendientes";

        return $data;
        exit;
    }

    /** Si tiene insumos pendientes **/
    if ($tieneInsumosPendientes) {
        $data["error"] = 1;
        $data["mensaje"] = "La historia " . $historia . " con el ingreso " . $ingreso . " tiene insumos pendientes";

        return $data;
        exit;
    }

    $guardoEgresoUnix = false;
    $medicoEgreso = "";
    $saveUnix = consultarAliasPorAplicacion($conex, $wemp_pmla, 'conexionUnix');

    /** Código de la especialidad del médico que tiene el diagnostico principal **/
    $codEspMedDiagPrincipal = null;
    /** Código de la especialidad del médico tratante **/
    $codEspMedTratante = null;

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
            $datosEnc['Egrdxi'] = $rows['Ingdig'];
            $grabadoUnix = $rows['Ingunx'];
        }
    }

    //FIN DE CARGAR LOS DATOS QUE SE DEBEN ENVIAR A UNIX Y ALGUNOS QUE SE TRAEN DEL INGRESO
    /* se verifica si ya està grabado en unix*/
    if ($saveUnix == 'on' && $grabadoUnix == "off") {
        $data['error'] = 1;
        $data['mensaje'] = " Error en unix - tabla inpac, El egreso no puede ser realizado, intentelo en unos minutos o comuniquese con informatica ";
        return $data;
    }

    //se consulta si existe esa aplicacion
    $alias = "movhos";
    $aplicacion = consultarAplicacion($conex, $wemp_pmla, $alias);

    $alias1 = "hce";
    $aplicacionHce = consultarAplicacion($conex, $wemp_pmla, $alias1);

    /***se guardan o se actualizan los datos***/
    if (!empty($historia) && !empty($ingreso)) {
        //Estructura para el insert
        $user2 = explode("-", $user);
        (isset($user2[1])) ? $user2 = $user2[1] : $user2 = $user2[0];
        if ($user2 == "")
            $user2 = $wbasedato;
        $fechaActual = date('Y-m-d');
        $horaActual = date('G:i:s');

        $infoing = $pacienteEgresar->data['infoing'][0];
        $datosEnc['egrhis'] = $pacienteEgresar->historia;
        $datosEnc['egring'] = $pacienteEgresar->ingreso;
        $datosEnc['egrfee'] = $pacienteEgresar->fechaAltDefinitiva;
        $datosEnc['egrhoe'] = $pacienteEgresar->horaAltDefinitiva;
        $datosEnc['egrest'] = dias_pasados($infoing['ing_ha_data'], $fechaActual);
        $datosEnc['Fecha_data'] = $fechaActual;
        $datosEnc['Hora_data'] = $horaActual;
        $datosEnc['Egrcae'] = $infoing['egr_caeselCauEgr'];
        $datosEnc['Medico'] = $wbasedato;
        $datosEnc['Seguridad'] = 'C-' . $user2;

        /** Variables para guardar arreglos de la información del ingreso **/
        $especialidadesIng = [];
        $serviciosIng = [];
        $diagnosticosIng = [];


        /*** Leer toda la información del ingreso **/
        foreach ($infoing as $key => $informacionIngreso) {
            if ($key == "especialidades") {
                $especialidadesIng = $informacionIngreso;
            }

            if ($key == "servicios") {
                $serviciosIng = $informacionIngreso;
            }

            if ($key == "diagnosticos") {
                $diagnosticosIng = $informacionIngreso;
            }

            if ($key == "procedimientos") {
                $procedimientos = $informacionIngreso;
            }
        }

        // Leemos las especialidades
        foreach ($especialidadesIng as $especialidades) {
            $datosEnc['egrmei'] = $especialidades['esp_med'];
            $datosEnc['Egrmee'] = $especialidades['esp_med'];
            $datosEnc['Egrmet'] = $especialidades['esp_med'];
        }

        // Leemos los diagnosticos
        foreach ($diagnosticosIng as $diagnosticos) {

            if ($diagnosticos['dia_nue'] == "S" || $diagnosticos['dia_nue'] == "on") {
                $datosEnc['Egrtdp'] = "2";
            } else {
                $datosEnc['Egrtdp'] = "3";
            }
            if ($diagnosticos['dia_com'] == "S" || $diagnosticos['dia_com'] == "on") {
                $datosEnc['Egrcom'] = "on";
            } else {
                $datosEnc['Egrcom'] = "off";
            }
        }

        /** Definición de datos para guardar en unix **/
        $_POST['egr_dxi'] = $infoing['ing_dig'];
        $_POST['_ux_egrdin_ux_hosdxi'] = $infoing['ing_dig'];
        $_POST['_ux_hoscex'] = $infoing['ing_cai'];
        $_POST['egr_feetxtFecEgr'] = $pacienteEgresar->fechaAltDefinitiva;
        $_POST['ing_feitxtFecIng'] = $infoing['ing_ha_data'];
        $_POST['egr_hoetxtHorEgr'] = $pacienteEgresar->horaAltDefinitiva;
        $_POST['ing_hintxtHorIng'] = $infoing['ing_hin'];
        $_POST['_ux_infmed'] = $infoing['ing_mei'];
        $_POST['_ux_mepides'] = "";


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
                        $data['error'] = 1;
                        $data['mensaje'] = "Error al grabar en UNIX " . $a->data['mensaje'];
                        return $data;
                        exit;
                    }
                    if ($a->data['error'] == 2) //si hay errores guardando en unix
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = " El paciente esta siendo modificado en unix, por lo tanto no se puede realizar el egreso en este momento ";
                        return $data;
                        exit;
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

        if ($res){
            $num = mysql_num_rows($res);

            //Si no se encontraron los datos, significa que es un registro nuevo
            if ($num == 0){
                //insert en la tabla 108
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
                        $x = 0;
                        foreach ($diagnosticosIng as $keDia => $valueDia){
                            unset($datosEnc); //se borra el array

                            $valueDia['dia_nue'] = 'N';
                            $valueDia['dia_com'] = 'N';
                            //se guardan todos los diagnosticos
                            $datosEnc = crearArrayDatos($wbasedato, "dia", "dia_", 3, $valueDia);
                            $datosEnc["diahis"] = $historia; //histiria
                            $datosEnc["diaing"] = $ingreso; //ingreso
                            if ($x == 0 ){
                                $datosEnc["diatip"] = "P";
                                $datosEnc["diaegr"] = "on";
                            }else{
                                $datosEnc["diatip"] = "S";
                                $datosEnc["diaegr"] = "off";
                            }
                            $sqlInsert = crearStringInsert($wbasedato . "_000109", $datosEnc);

                            $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de diagnosticos - " . mysql_error()));

                            if (!$resEnc) {
                                $data['error'] = 1;
                            }

                            if ( isset($valueDia['servicios']) ) {

                                foreach ( $valueDia['servicios'] as $keSer => $valueSerDia) {
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
                            $x++;
                        } //foreach
                    }
                    /**Fin Diagnosticos**/

                    /**Procedimientos**/
                    if ($data['error'] == 0) {
                        //para pasar de procedimiento en procedimiento
                        $x = 0;
                        foreach ($procedimientos as $keDia => $valueDia) {
                            unset($datosEnc); //se borra el array

                            //se guardan todos los procedimientos
                            $datosEnc = crearArrayDatos($wbasedato, "pro", "pro_", 3, $valueDia);
                            $datosEnc["prohis"] = $historia; //histiria
                            $datosEnc["proing"] = $ingreso; //ingreso
                            $datosEnc["proqui"] = "N";
                            if ($x == 0)
                                $datosEnc["pro_tip"] = "P";
                            else
                                $datosEnc["pro_tip"] = "S";

                            if ($datosEnc["procod"] != "") {
                                $sqlInsert = crearStringInsert($wbasedato . "_000110", $datosEnc);

                                $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de procedimientos - " . mysql_error()));

                                if (!$resEnc) {
                                    $data['error'] = 1;
                                }
                            }
                            $x++;
                        } //foreach
                    }
                    /**Fin Procedimientos**/

                    /**Especialidades**/
                    if ($data['error'] == 0) {
                        //para pasar de especialidad en especialidad
                        $x = 0;
                        foreach ($especialidadesIng as $keDia => $valueDia) {
                            unset($datosEnc); //se borra el array

                            //se guardan todas las especialidades

                            $datosEnc = crearArrayDatos($wbasedato, "esp", "esp_", 3, $valueDia);

                            $datosEnc["esphis"] = $historia; //histiria
                            $datosEnc["esping"] = $ingreso; //ingreso
                            if ($x == 0) $datosEnc["esptip"] = "P";
                            else $datosEnc["esptip"] = "S";
                            //unset( $datosEnc[ "espegr" ] ); //este campo no existe en la base de datos, se usa para detectar el medico de egreso que viaja a UNIX
                            $sqlInsert = crearStringInsert($wbasedato . "_000111", $datosEnc);

                            $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de especialidades - " . mysql_error()));

                            if (!$resEnc) {
                                $data['error'] = 1;
                            }
                            if ( isset($valueDia['servicios']) ) {
                                foreach ( isset($valueDia['servicios']) as $keSer => $valueSeresp) {
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
                                $x++;
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
                        foreach ($serviciosIng as $keDia => $valueDia) {
                            unset($datosEnc); //se borra el array

                            //se guardan todos los servicios
                            //unset();
                            $datosEnc = crearArrayDatos($wbasedato, "ser", "ser_", 3, $valueDia);
                            $datosEnc["serhis"] = $historia; //historia
                            $datosEnc["sering"] = $ingreso; //ingreso
                            $datosEnc["seregr"] = 'on';

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

                    if ($data['error'] == 0) {
                        logEgreso('Proceso automatico guardado', $historia, $ingreso, $documento, $tipo_documento, $paciente);
                        $data['mensaje'] = "Se guardo el egreso correctamente";
                    }
                }else {
                    $data["error"] = 1;
                    $data['mensaje'] = "No se guardo el egreso correctamente, ya existe un egreso de la historia " . $historia . " con ingreso " . $ingreso;
                }
            }//hace la actualizacion
            else{
                $data["error"] = 0;//2016-05-31 se agrega esta linea para que si borre los datos anteriores en las actualizaciones
                $rowsEnc = mysql_fetch_array($res);

                unset($datosEnc); //se borra el array

                $datosEnc['egrhis'] = $pacienteEgresar->historia;
                $datosEnc['egring'] = $pacienteEgresar->ingreso;
                $datosEnc['egrfee'] = $pacienteEgresar->fechaAltDefinitiva;
                $datosEnc['egrhoe'] = $pacienteEgresar->horaAltDefinitiva;
                $datosEnc['egrest'] = dias_pasados($infoing['ing_ha_data'], $fechaActual);
                $datosEnc['Fecha_data'] = $fechaActual;
                $datosEnc['Hora_data'] = $horaActual;
                $datosEnc['Egrcae'] = $infoing['egr_caeselCauEgr'];
                $datosEnc['Medico'] = $wbasedato;
                $datosEnc['Seguridad'] = 'C-' . $user2;
                // Leemos las especialidades
                foreach ($especialidadesIng as $especialidades) {
                    $datosEnc['egrmei'] = $especialidades['esp_med'];
                    $datosEnc['Egrmee'] = $especialidades['esp_med'];
                    $datosEnc['Egrmet'] = $especialidades['esp_med'];
                }

                // Leemos los diagnosticos
                foreach ($diagnosticosIng as $diagnosticos) {

                    if ($diagnosticos['dia_nue'] == "S" || $diagnosticos['dia_nue'] == "on") {
                        $datosEnc['Egrtdp'] = "2";
                    } else {
                        $datosEnc['Egrtdp'] = "3";
                    }
                    if ($diagnosticos['dia_com'] == "S" || $diagnosticos['dia_com'] == "on") {
                        $datosEnc['Egrcom'] = "on";
                    } else {
                        $datosEnc['Egrcom'] = "off";
                    }
                }

                $datosEnc['id'] = $rowsEnc['id'];
                $datosEnc['Egract'] = 'on';

                $sqlUpdate = crearStringUpdate($wbasedato . "_000108", $datosEnc);

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
                        $x = 0;
                        foreach ($diagnosticosIng as $keDia => $valueDia){
                            unset($datosEnc); //se borra el array

                            $valueDia['dia_nue'] = 'N';
                            $valueDia['dia_com'] = 'N';
                            //se guardan todos los diagnosticos
                            $datosEnc = crearArrayDatos($wbasedato, "dia", "dia_", 3, $valueDia);
                            $datosEnc["diahis"] = $historia; //histiria
                            $datosEnc["diaing"] = $ingreso; //ingreso
                            if ($x == 0 ){
                                $datosEnc["diatip"] = "P";
                                $datosEnc["diaegr"] = "on";
                            }else{
                                $datosEnc["diatip"] = "S";
                                $datosEnc["diaegr"] = "off";
                            }
                            $sqlInsert = crearStringInsert($wbasedato . "_000109", $datosEnc);

                            $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de diagnosticos - " . mysql_error()));

                            if (!$resEnc) {
                                $data['error'] = 1;
                            }

                            if ( isset($valueDia['servicios']) ) {

                                foreach ( $valueDia['servicios'] as $keSer => $valueSerDia) {
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
                            $x++;
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
                        $x = 0;
                        foreach ($procedimientos as $keDia => $valueDia) {
                            unset($datosEnc); //se borra el array

                            //se guardan todos los procedimientos
                            $datosEnc = crearArrayDatos($wbasedato, "pro", "pro_", 3, $valueDia);
                            $datosEnc["prohis"] = $historia; //histiria
                            $datosEnc["proing"] = $ingreso; //ingreso
                            $datosEnc["proqui"] = "N";
                            if ($x == 0)
                                $datosEnc["pro_tip"] = "P";
                            else
                                $datosEnc["pro_tip"] = "S";

                            if ($datosEnc["procod"] != "") {
                                $sqlInsert = crearStringInsert($wbasedato . "_000110", $datosEnc);

                                $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de procedimientos - " . mysql_error()));

                                if (!$resEnc) {
                                    $data['error'] = 1;
                                }
                            }
                            $x++;
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
                        $x = 0;
                        foreach ($especialidadesIng as $keDia => $valueDia) {
                            unset($datosEnc); //se borra el array

                            //se guardan todas las especialidades

                            $datosEnc = crearArrayDatos($wbasedato, "esp", "esp_", 3, $valueDia);

                            $datosEnc["esphis"] = $historia; //histiria
                            $datosEnc["esping"] = $ingreso; //ingreso
                            if ($x == 0) $datosEnc["esptip"] = "P";
                            else $datosEnc["esptip"] = "S";
                            //unset( $datosEnc[ "espegr" ] ); //este campo no existe en la base de datos, se usa para detectar el medico de egreso que viaja a UNIX
                            $sqlInsert = crearStringInsert($wbasedato . "_000111", $datosEnc);

                            $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de especialidades - " . mysql_error()));

                            if (!$resEnc) {
                                $data['error'] = 1;
                            }
                            if ( isset($valueDia['servicios']) ) {
                                foreach ( isset($valueDia['servicios']) as $keSer => $valueSeresp) {
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
                                $x++;
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
                        foreach ($serviciosIng as $keDia => $valueDia) {
                            unset($datosEnc); //se borra el array

                            //se guardan todos los servicios
                            //unset();
                            $datosEnc = crearArrayDatos($wbasedato, "ser", "ser_", 3, $valueDia);
                            $datosEnc["serhis"] = $historia; //historia
                            $datosEnc["sering"] = $ingreso; //ingreso
                            $datosEnc["seregr"] = 'on';

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
                }
                /**Fin Servicios**/

                if ($data['error'] == 0) {
                    logEgreso('Proceso automatico actualizado', $historia, $ingreso, $documento, $tipo_documento, $paciente);
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
                                    SET  Pacact = 'off'
                                    WHERE Pachis='" . $historia . "' ";
                $resUpdate = mysql_query($sqlUpdate, $conex) or ($data['mensaje'] = utf8_encode("Error actualizando " . $wbasedato . "_000100 " . mysql_errno() . " - Error en el query $sqlUpdate - " . mysql_error()));
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
        }else //no se ejecuto la consulta de la 108
        {
            $data["error"] = 1;
        }
    }else //no se ejecuto la consulta de la 108
    {
        $data["error"] = 1;
        $data["mensaje"] = "La historia y el ingreso esta vacios por favor verificar";
    }

    return $data;
}

function guardarDatos($pacienteEgresar)
{
    global $conex;
    global $wemp_pmla;
    global $wbasedato;
    global $hay_unix;
    global $user;

    /***----- DEFINICION DE VARIABLES LOCALES ---------------------**/
    $historia = $pacienteEgresar->historia;
    $cco_egreso = $pacienteEgresar->ccoEgreso;
    $ingreso = $pacienteEgresar->ingreso;
    $documento = $pacienteEgresar->documento;
    $tipo_documento = $pacienteEgresar->tipo_documento;
    $paciente = $pacienteEgresar->paciente;

    $priApe = null;
    $segApe = null;
    $priNom = null;
    $segNom = null;

    $datosEnc = [];
    $data["error"] = 0;
    $data['mensaje'] = "";

    // Valida si tiene cirugias sin liquidar
    $tieneCirugiaSinLiquidar = validarCirugiaSinLiquidar($historia, $ingreso);

    /** Tiene articulos pendientes **/
    $tieneArticulosPendientes = validarArticulosPaciente($historia, $ingreso);

    /** Tiene insumos pendientes **/
    $tieneInsumosPendientes = validarInsumosPaciente($historia, $ingreso);

    /** Si tiene cirugias sin liquidar */
    if ($tieneCirugiaSinLiquidar) {
        $data["error"] = 1;
        $data["mensaje"] = "La historia " . $historia . " con el ingreso " . $ingreso . " tiene cirugias pendientes sin liquidar";

        return $data;
        exit;
    }

    /** Si tiene artituculos pendientes **/
    if ($tieneArticulosPendientes) {
        $data["error"] = 1;
        $data["mensaje"] = "La historia " . $historia . " con el ingreso " . $ingreso . " tiene articulos pendientes";

        return $data;
        exit;
    }

    /** Si tiene insumos pendientes **/
    if ($tieneInsumosPendientes) {
        $data["error"] = 1;
        $data["mensaje"] = "La historia " . $historia . " con el ingreso " . $ingreso . " tiene insumos pendientes";

        return $data;
        exit;
    }

    $guardoEgresoUnix = false;
    $medicoEgreso = "";
    $saveUnix = consultarAliasPorAplicacion($conex, $wemp_pmla, 'conexionUnix');

    /*** Consultar el ingreso en la tabla _000101 **/
    $sql1 = "select Ingcai,Ingdig, Ingunx
             from " . $wbasedato . "_000101
             where  Inghis='" . $historia . "'
             and Ingnin='" . $ingreso . "' ";

    $res1 = mysql_query($sql1, $conex) or die(mysql_errno() . " - Error en el query $sql1 - " . mysql_error());
    $num1 = mysql_num_rows($res1);

    if ($num1 > 0) {
        while ($rows = mysql_fetch_array($res1)) {
            $datosEnc['Egrdxi'] = $rows['Ingdig'];
            $grabadoUnix = $rows['Ingunx'];

        }
    }

//FIN DE CARGAR LOS DATOS QUE SE DEBEN ENVIAR A UNIX Y ALGUNOS QUE SE TRAEN DEL INGRESO
    /* se verifica si ya està grabado en unix*/
    if ($saveUnix == 'on' && $grabadoUnix == "off") {
        $data['error'] = 1;
        $data['mensaje'] = " Error en unix - tabla inpac, El egreso no puede ser realizado, intentelo en unos minutos o comuniquese con informatica ";
        return $data;
    }


//se consulta si existe esa aplicacion
    $alias = "movhos";
    $aplicacion = consultarAplicacion($conex, $wemp_pmla, $alias);

    $alias1 = "hce";
    $aplicacionHce = consultarAplicacion($conex, $wemp_pmla, $alias1);

    /***se guardan o se actualizan los datos***/
    if (!empty($historia) && !empty($ingreso)) {


        //Estructura para el insert
        $user2 = explode("-", $user);
        (isset($user2[1])) ? $user2 = $user2[1] : $user2 = $user2[0];
        if ($user2 == "")
            $user2 = $wbasedato;
        $fechaActual = date('Y-m-d');
        $horaActual = date('G:i:s');

        $infoing = $pacienteEgresar->data['infoing'][0];
        $datosEnc['egrhis'] = $pacienteEgresar->historia;
        $datosEnc['egring'] = $pacienteEgresar->ingreso;
        $datosEnc['egrfee'] = $pacienteEgresar->fechaAltDefinitiva;
        $datosEnc['egrhoe'] = $pacienteEgresar->horaAltDefinitiva;
        $datosEnc['egrest'] = dias_pasados($infoing['ing_ha_data'], $fechaActual);
        $datosEnc['Fecha_data'] = $fechaActual;
        $datosEnc['Hora_data'] = $horaActual;
        $datosEnc['Egrcae'] = $infoing['egr_caeselCauEgr'];
        $datosEnc['Medico'] = $wbasedato;
        $datosEnc['Seguridad'] = 'C-' . $user2;

        /** Variables para guardar arreglos de la información del ingreso **/
        $especialidadesIng = [];
        $serviciosIng = [];
        $diagnosticosIng = [];


        /*** Leer toda la información del ingreso **/
        foreach ($infoing as $key => $informacionIngreso) {
            if ($key == "especialidades") {
                $especialidadesIng = $informacionIngreso;
            }

            if ($key == "servicios") {
                $serviciosIng = $informacionIngreso;
            }

            if ($key == "diagnosticos") {
                $diagnosticosIng = $informacionIngreso;
            }
        }

        // Leemos las especialidades
        foreach ($especialidadesIng as $especialidades) {
            $datosEnc['egrmei'] = $especialidades['esp_med'];
            $datosEnc['Egrmee'] = $especialidades['esp_med'];
            $datosEnc['Egrmet'] = $especialidades['esp_med'];
        }

        // Leemos los diagnosticos
        foreach ($diagnosticosIng as $diagnosticos) {

            if ($diagnosticos['dia_nue'] == "S" || $diagnosticos['dia_nue'] == "on") {
                $datosEnc['Egrtdp'] = "2";
            } else {
                $datosEnc['Egrtdp'] = "3";
            }
            if ($diagnosticos['dia_com'] == "S" || $diagnosticos['dia_com'] == "on") {
                $datosEnc['Egrcom'] = "on";
            } else {
                $datosEnc['Egrcom'] = "off";
            }
        }

        /** Definición de datos para guardar en unix **/
        $_POST['egr_dxi'] = $infoing['ing_dig'];
        $_POST['_ux_egrdin_ux_hosdxi'] = $infoing['ing_dig'];
        $_POST['_ux_hoscex'] = $infoing['ing_cai'];
        $_POST['egr_feetxtFecEgr'] = $pacienteEgresar->fechaAltDefinitiva;
        $_POST['ing_feitxtFecIng'] = $infoing['ing_ha_data'];
        $_POST['egr_hoetxtHorEgr'] = $pacienteEgresar->horaAltDefinitiva;
        $_POST['ing_hintxtHorIng'] = $infoing['ing_hin'];
        $_POST['_ux_infmed'] = $infoing['ing_mei'];
        $_POST['_ux_mepides'] = "";


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
                        $data['error'] = 1;
                        $data['mensaje'] = "Error al grabar en UNIX " . $a->data['mensaje'];
                        return $data;
                        exit;
                    }
                    if ($a->data['error'] == 2) //si hay errores guardando en unix
                    {
                        $data['error'] = 1;
                        $data['mensaje'] = " El paciente esta siendo modificado en unix, por lo tanto no se puede realizar el egreso en este momento ";
                        return $data;
                        exit;
                    }
                    $guardoEgresoUnix = true;
                }
            }
        }

        /** Consultamos si el egreso ya existe **/
        $sql = "select Egrhis,Egring,id,Egract
                from " . $wbasedato . "_000108
                where Egrhis = '" . utf8_decode($historia) . "'
                and Egring = '" . utf8_decode($ingreso) . "'";

        $res = mysql_query($sql, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error consultando la tabla de egresos - " . mysql_error()));

        if ($res) {
            $num = mysql_num_rows($res);

            /*** Si no se encontraron los datos, significa que es un registro nuevo ***/
            if ($num == 0) //hace el insert
            {
                //insert en la tabla 108
                $datosEnc["Egract"] = 'on';
                $datosEnc["Egrunx"] = 'off';

                if ($guardoEgresoUnix == true)
                    $datosEnc["Egrunx"] = 'on';

                $sqlInsert = crearStringInsert($wbasedato . "_000108", $datosEnc);

                $resEnc = mysql_query($sqlInsert, $conex) or ($data['mensaje'] = utf8_encode(mysql_errno() . " - Error grabando en la tabla de egresos - " . mysql_error()));

                //si inserto la 108
                if ($resEnc) {
                    logEgreso('Proceso automatico guardado', $historia, $ingreso, $documento, $tipo_documento, $paciente);
                    $data['mensaje'] = "Se guardo el egreso correctamente";

                } else {
                    $data["error"] = 1;
                    $data['mensaje'] = "No se guardo el egreso correctamente, ya existe un egreso de la historia " . $historia . " con ingreso " . $ingreso;
                }
            } else //hace la actualizacion
            {
                $data["error"] = 0;//2016-05-31 se agrega esta linea para que si borre los datos anteriores en las actualizaciones
                $rowsEnc = mysql_fetch_array($res);

                $datosEnc['id'] = $rowsEnc['id'];
                $datosEnc['Egract'] = 'on';

                $sqlUpdate = crearStringUpdate($wbasedato . "_000108", $datosEnc);

                $res1 = mysql_query($sqlUpdate, $conex) or ($data['error'] = utf8_encode(mysql_errno() . " - Error en el query $sqlUpdate - " . mysql_error()));

                if (!$res1) {
                    $data["error"] = 1;
                    $data["mensaje"] = utf8_encode(mysql_errno() . " - Error en el query $sqlUpdate - " . mysql_error());
                }

                if ($data['error'] == 0) {
                    logEgreso('Proceso automatico actualizado', $historia, $ingreso, $documento, $tipo_documento, $paciente);
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
                                    SET  Pacact = 'off'
                                    WHERE Pachis='" . $historia . "' ";
                $resUpdate = mysql_query($sqlUpdate, $conex) or ($data['mensaje'] = utf8_encode("Error actualizando " . $wbasedato . "_000100 " . mysql_errno() . " - Error en el query $sqlUpdate - " . mysql_error()));
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


    return $data;
}

?>