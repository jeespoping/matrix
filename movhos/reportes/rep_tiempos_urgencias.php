<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}

    /**
        PROGRAMA                   : rep_tiempos_urgencias.php
        AUTOR                      : Eimer Wilfer Castro Hincapié.
        FECHA CREACION             : 03 Diciembre de 2015

        DESCRIPCION: En este reporte se puede visualizar en un rango de fechas los tiempos promedio de las Admisiones, los Triages y las Consultas por Especialidad.
        Adicional a esto se debe mostrar los tiempos promedio que le toma a cada uno de los usuarios realizar las Admisiones, Triages y Consultas por Especialidad.
        También se debe mostrar la lista detallada de cada registro según si es Admisiones, Triages o Consulta por Especialidad junto con el usuario y el tiempo que le tomo realizar el proceso. Para las Consultas por Especialidad también se debe mostrar el promedio total de todas estas.

        Notas:
        --
        */ $wactualiza = "(Diciembre 03 de 2015)"; /*
        ACTUALIZACIONES:

        *   Diciembre 03 de 2015
        Eimer Castro:           :   Fecha de la creación del programa.
    **/

        $fecha_actual = date("Y-m-d");
        $hora_actual  = date("H:i:s");
        

        


        if(!isset($accion) && !isset($_SESSION['user']) && !isset($accion))
        {
            echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
            [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
            return;
        }
        elseif(isset($accion) && !array_key_exists('user',$_SESSION))
        {
            $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
            $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
            echo json_encode($data);
            return;
        }
        $user_session      = explode('-',$_SESSION['user']);
        $user_session      = $user_session[1];

    //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
        $sql_init_data = "  SELECT Detapl, Detval, Empdes
        FROM root_000050, root_000051
        WHERE Empcod = '" . $wemp_pmla. "'
        AND Empest = 'on'
        AND Empcod = Detemp";

        $res = mysql_query($sql_init_data, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql_init_data . " - " . mysql_error());
        $num = mysql_num_rows($res);

        if ($num > 0 )
        {
            for ($i=1;$i<=$num;$i++)
            {
                $row = mysql_fetch_array($res);

                if ($row[0] == "cenmez")
                    $wcenmez=$row[1];

                if ($row[0] == "afinidad")
                    $wafinidad=$row[1];

                if ($row[0] == "movhos")
                    $wbasedato=$row[1];

                if ($row[0] == "tabcco")
                    $wtabcco=$row[1];

                if ($row[0] == "tcx")
                    $wtcx=$row[1];

                if ($row[0] == "cliame")
                    $wcliame=$row[1];
            }
        }
        else
        {
            echo "NO EXISTE NINGUNA EMPRESA DEFINIDA PARA ESTE CODIGO";
        }

        /**
         * Cálcula la diferencia de tiempo entre dos horas.
         *
         * @author Eimer Castro
         * @return date Contiene la diferencia entre dos horas en formato H:i:s
         * @param date $inicio Hora inicial
         * @param date $fin Hora final
         */
        function restarHoras($inicio, $fin)
        {
            if($fin >= $inicio)
            {
                //Se coloca la fecha 1970-01-01 puesto que es la fecha a partir de la cual inicia el conteo de  segundos a nivel computacional.
                $dif = gmdate("d H:i:s", strtotime("1970-01-01 00:00:00 UTC") + strtotime($fin . " UTC") - strtotime($inicio . " UTC"));
            }
            else
            {
                //Se coloca la fecha 1970-01-01 puesto que es la fecha a partir de la cual inicia el conteo de  segundos a nivel computacional.
                $dif = gmdate("d H:i:s", strtotime("1970-01-01 00:00:00 UTC"));
            }
            $explFecha = explode(" ", $dif);
            $xplHoras = explode(":",$explFecha[1]);
            $dias_a_horas = (($explFecha[0]*1) > 1) ? (($explFecha[0]*1)-1)*24: 0;
            if($dias_a_horas > 0)
            {
                $xplHoras[0] = $xplHoras[0]+$dias_a_horas;
            }

            $dif = $xplHoras[0].':'.$xplHoras[1].':'.$xplHoras[2];
            return $dif;
        }

        /**
         * Retorna el tiempo máximo en un proceso.
         *
         * @author Eimer Castro
         * @return date Contiene el tiempo máximo de un proceso en formato H:i:s
         * @param array $array Array con todos los tiempos de un proceso
         */
        function tiempoMaximo($array)
        {
            $tiempo_maximo = $array[0];
            for($i = 0; $i < sizeof($array); $i++)
            {
                if($array[$i] > $tiempo_maximo)
                {
                    $tiempo_maximo = $array[$i];
                }
            }
            return $tiempo_maximo;
        }

        /**
         * Retorna el tiempo mínimo en un proceso.
         *
         * @author Eimer Castro
         * @return date Contiene el tiempo mínimo de un proceso en formato H:i:s
         * @param array $array Array con todos los tiempos de un proceso
         */
        function tiempoMinimo($array)
        {
            $tiempo_minimo = $array[0];
            for($i = 0; $i < sizeof($array); $i++)
            {
                if($array[$i] < $tiempo_minimo)
                {
                    $tiempo_minimo = $array[$i];
                }
            }
            return $tiempo_minimo;
        }

        /**
         * Retorna el tiempo promedio en un proceso.
         *
         * @author Eimer Castro
         * @return date Contiene el timepo promedio de un proceso en formato H:i:s
         * @param array $array Array con todos los tiempos de un proceso
         */
        function tiempoPromedio($array)
        {
            $tiempo_total = strtotime("1970-01-01 00:00:00 UTC");
            $tiempoMayor = strtotime("1970-01-01 " . $array[0] . " UTC");
            $tiempoMenor = strtotime("1970-01-01 " . $array[0] . " UTC");
            for ($i = 0; $i < count($array); $i++) {

                $fechaAux = gmdate("Y-m-d H:i:s", strtotime("1970-01-01 00:00:00+05:00 UTC"));
                $explodeTiempo = explode(":", $array[$i]);
                $dias = floor($explodeTiempo[0] / 24);
                $horas = $explodeTiempo[0] % 24;
                $minutos = $explodeTiempo[1] * 1;
                $segundos = $explodeTiempo[2] * 1;
                //$tiempoNuevo = $horas . ":" . $minutos . ":" . $segundos;
                $tiempo = strtotime("+" . $dias . " day", strtotime($fechaAux));
                $tiempo = strtotime("+" . $horas . " hour", $tiempo);
                $tiempo = strtotime("+" . $minutos . " minute", $tiempo);
                $tiempo = strtotime("+" . $segundos . " second", $tiempo);

                //Esta validación se puede usar en el caso de que se necesite eliminar los extremos(Valor mayor y menor)
                //Para calcular el promedio sin tenerlos en cuenta.
                /*if($tiempo > $tiempoMayor)
                {
                    $tiempoMayor = $tiempo;
                }
                if($tiempo < $tiempoMenor)
                {
                    $tiempoMenor = $tiempo;
                }*/
                $tiempo_total = sumahoras($tiempo_total, $tiempo);
            }

            //$tiempo_promedio = (sizeof($array) > 0) ? gmdate("H:i:s", ($tiempo_total / sizeof($array)) . " UTC") : '00:00:00';
            //$tiempo_promedio = (sizeof($array) > 0) ? gmdate("H:i:s", ($tiempo_total - $tiempoMayor - $tiempoMenor) / sizeof($array)) : '00:00:00';
            ////Esta validación se puede usar en el caso de que se necesite eliminar los extremos(Valor mayor y menor)
            //Para calcular el promedio sin tenerlos en cuenta.
            /*if(count($array) >= 3)
            {
                $tiempo_promedio = gmdate("H:i:s", ($tiempo_total - $tiempoMayor - $tiempoMenor) / count($array));
            }
            elseif(count($array) >= 1)
            {
                $tiempo_promedio = gmdate("H:i:s", ($tiempo_total) / count($array));
            }
            else
            {
                $tiempo_promedio = '00:00:00';
            }*/
            $tiempo_promedio = (count($array) > 0) ? gmdate("H:i:s", ($tiempo_total) / count($array)) : '00:00:00';
            return $tiempo_promedio;
        }

        /**
         * Cálcula la diferencia de tiempo entre dos horas.
         *
         * @author Eimer Castro
         * @return date Contiene la suma entre dos horas en formato H:i:s
         * @param date $hora1 Hora a sumar
         * @param date $hora2 Hora a sumar
         */
        function sumahoras($hora1,$hora2)
        {
            /*$hora1=explode(":",$hora1);
            $hora2=explode(":",$hora2);
            $horas=(int)$hora1[0]+(int)$hora2[0];
            $minutos=(int)$hora1[1]+(int)$hora2[1];
            $segundos=(int)$hora1[2]+(int)$hora2[2];
            $horas+=(int)($minutos/60);
            $minutos=(int)($minutos%60)+(int)($segundos/60);
            $segundos=(int)($segundos%60);
            $hora_resultante = (intval($horas)<10?'0'.intval($horas):intval($horas)).':'.($minutos<10?'0'.$minutos:$minutos).':'.($segundos<10?'0'.$segundos:$segundos);
            */
           $hora_resultante = $hora1 + $hora2;
           return $hora_resultante;
        }

        /**
         * Convuerte una hora en segundos.
         *
         * @author Eimer Castro
         * @return int Es la cantidad de segundos equivalentes a una hora
         * @param date $hour Hora a convertir a segundos
         */
        function horasSegundos($hour)
        {
            $parse = array();
            if (!preg_match ('#^(?<hours>[\d]{1,}):(?<mins>[\d]{2}):(?<secs>[\d]{2})$#',$hour,$parse))
            {
                throw new RuntimeException ("Hour Format not valid: " . $hour);
            }
            $segundos = (int) $parse['hours'] * 3600 + (int) $parse['mins'] * 60 + (int) $parse['secs'];
            return $segundos;

        }

        function generarArrProcesos($arr_procesosIndex, $fecha_index, &$array_reporte)
        {
            foreach ($arr_procesosIndex as $key => $proceso)
            {
                if(!array_key_exists($fecha_index, $array_reporte))
                {
                    $array_reporte[$fecha_index] = array();
                }

                if(!array_key_exists($proceso, $array_reporte[$fecha_index]))
                {
                    $array_reporte[$fecha_index][$proceso] = array("detalles" => array());
                }
            }
        }

        function agregarDetalle($arr_procesosIndex, $row, $fecha, $proceso, &$array_reporte, $procesoPpal)
        {
            if($proceso == "consultas")
            {
                $turno = $row['Turno'];
                // if(!array_key_exists($turno, $array_reporte[$fecha][$proceso]["detalles"]))
                {
                    $tiempo_inicial = $row['FecIniCon'] . " " . $row['HorIniCon'];
                    $tiempo_final = $row['FecFinCon'] . " " . $row['HorFinCon'];
                    $tiempo_total = restarHoras($tiempo_inicial, $tiempo_final);
                    $array_reporte[$fecha][$proceso]["detalles"][] = array(
                                                                            "turno" => $row['Turno'],
                                                                            "historia_clinica" => $row['hce'],
                                                                            "ingreso" => $row['ingreso'],
                                                                            "usuario_matrix" => $row['usuario'],
                                                                            "fecha_inicio" => $row['FecIniCon'],
                                                                            "hora_inicio" => $row['HorIniCon'],
                                                                            "fecha_fin" => $row['FecFinCon'],
                                                                            "hora_fin" => $row['HorFinCon'],
                                                                            "tiempo_total" => $tiempo_total,
                                                                            "procesoPpal" => $procesoPpal
                                                                            );
                    $codigo_especialidad = $row['Cod_Especialidad'];
                    if(!array_key_exists("especialidades", $array_reporte[$fecha][$proceso]))
                    {
                        $array_reporte[$fecha][$proceso]["especialidades"] = array();
                    }
                    if(!array_key_exists($codigo_especialidad, $array_reporte[$fecha][$proceso]["especialidades"]))
                    {
                        $array_reporte[$fecha][$proceso]["especialidades"][$codigo_especialidad] = array();
                    }

                    $tiempo_inicialEsp = $row['FecIniCon'] . " " . $row['HorIniCon'];
                    $tiempo_finalEsp = $row['FecFinCon'] . " " . $row['HorFinCon'];
                    $tiempo_totalEsp = restarHoras($tiempo_inicialEsp, $tiempo_finalEsp);

                    $array_reporte[$fecha][$proceso]["especialidades"][$codigo_especialidad][] = array(
                                                                                                        "especialidad" => $row['Especialidad'],
                                                                                                        "turno" => $row['Turno'],
                                                                                                        "historia_clinica" => $row['hce'],
                                                                                                        "ingreso" => $row['ingreso'],
                                                                                                        "usuario_matrix" => $row['usuario'],
                                                                                                        "fecha_inicio" => $row['FecIniCon'],
                                                                                                        "hora_inicio" => $row['HorIniCon'],
                                                                                                        "fecha_fin" => $row['FecFinCon'],
                                                                                                        "hora_fin" => $row['HorFinCon'],
                                                                                                        "tiempo_total" => $tiempo_totalEsp,
                                                                                                        "procesoPpal" => $procesoPpal
                                                                                                        );
                }
            }
            elseif($proceso == "espera_consultas")
            {
                $turno = $row['Turno'];
                // if(!array_key_exists($turno, $array_reporte[$fecha][$proceso]["detalles"]))
                {
                    $tiempo_inicial = $row['FecIniEspCon'] . " " . $row['HorIniEspCon'];
                    $tiempo_final = $row['FecFinEspCon'] . " " . $row['HorFinEspCon'];
                    $tiempo_total = restarHoras($tiempo_inicial, $tiempo_final);
                    $array_reporte[$fecha][$proceso]["detalles"][] = array(
                                                                            "turno" => $row['Turno'],
                                                                            "historia_clinica" => $row['hce'],
                                                                            "ingreso" => $row['ingreso'],
                                                                            "usuario_matrix" => '',
                                                                            "fecha_inicio" => $row['FecIniEspCon'],
                                                                            "hora_inicio" => $row['HorIniEspCon'],
                                                                            "fecha_fin" => $row['FecFinEspCon'],
                                                                            "hora_fin" => $row['HorFinEspCon'],
                                                                            "tiempo_total" => $tiempo_total,
                                                                            "procesoPpal" => $procesoPpal
                                                                            );
                }
            }
            elseif($proceso == "triages")
            {
                $turno = $row['Turno'];
                // if(!array_key_exists($turno, $array_reporte[$fecha][$proceso]["detalles"]))
                {
                    $tiempo_inicial = $row['FecIniTri'] . " " . $row['HorIniTri'];
                    $tiempo_final = $row['FecFinTri'] . " " . $row['HorFinTri'];
                    $tiempo_total = restarHoras($tiempo_inicial, $tiempo_final);
                    $array_reporte[$fecha][$proceso]["detalles"][] = array(
                                                                            "turno" => $row['Turno'],
                                                                            "historia_clinica" => $row['hce'],
                                                                            "ingreso" => $row['ingreso'],
                                                                            "usuario_matrix" => $row['usuario'],
                                                                            "fecha_inicio" => $row['FecIniTri'],
                                                                            "hora_inicio" => $row['HorIniTri'],
                                                                            "fecha_fin" => $row['FecFinTri'],
                                                                            "hora_fin" => $row['HorFinTri'],
                                                                            "tiempo_total" => $tiempo_total,
                                                                            "procesoPpal" => $procesoPpal
                                                                            );
                }
            }
            elseif($proceso == "espera_triages")
            {
                $turno = $row['Turno'];
                // if(!array_key_exists($turno, $array_reporte[$fecha][$proceso]["detalles"]))
                {
                    $tiempo_inicial = $row['FecIniEspTri'] . " " . $row['HorIniEspTri'];
                    $tiempo_final = $row['FecFinEspTri'] . " " . $row['HorFinEspTri'];
                    $tiempo_total = restarHoras($tiempo_inicial, $tiempo_final);
                    $array_reporte[$fecha][$proceso]["detalles"][] = array(
                                                                            "turno" => $row['Turno'],
                                                                            "historia_clinica" => $row['hce'],
                                                                            "ingreso" => $row['ingreso'],
                                                                            "usuario_matrix" => '',
                                                                            "fecha_inicio" => $row['FecIniEspTri'],
                                                                            "hora_inicio" => $row['HorIniEspTri'],
                                                                            "fecha_fin" => $row['FecFinEspTri'],
                                                                            "hora_fin" => $row['HorFinEspTri'],
                                                                            "tiempo_total" => $tiempo_total,
                                                                            "procesoPpal" => $procesoPpal
                                                                            );
                }
            }
            elseif($proceso == "admisiones")
            {
                $turno = $row['Turno'];
                // if($turno != '' && !array_key_exists($turno, $array_reporte[$fecha][$proceso]["detalles"]))
                {
                    $tiempo_inicial = $row['FecIniAdm'] . " " . $row['HorIniAdm'];
                    $tiempo_final = $row['FecFinAdm'] . " " . $row['HorFinAdm'];
                    $tiempo_total = restarHoras($tiempo_inicial, $tiempo_final);
                    $array_reporte[$fecha][$proceso]["detalles"][] = array(
                                                                            "turno" => $row['Turno'],
                                                                            "historia_clinica" => $row['hce'],
                                                                            "ingreso" => $row['ingreso'],
                                                                            "usuario_matrix" => $row['usuario'],
                                                                            "fecha_inicio" => $row['FecIniAdm'],
                                                                            "hora_inicio" => $row['HorIniAdm'],
                                                                            "fecha_fin" => $row['FecFinAdm'],
                                                                            "hora_fin" => $row['HorFinAdm'],
                                                                            "tiempo_total" => $tiempo_total,
                                                                            "procesoPpal" => $procesoPpal
                                                                            );
                }
            }
            elseif($proceso == "espera_admisiones")
            {
                $turno = $row['Turno'];
                // if($turno != '' && !array_key_exists($turno, $array_reporte[$fecha][$proceso]["detalles"]))
                {
                    $tiempo_inicial = $row['FecIniEspAdm'] . " " . $row['HorIniEspAdm'];
                    $tiempo_final = $row['FecFinEspAdm'] . " " . $row['HorFinEspAdm'];
                    $tiempo_total = restarHoras($tiempo_inicial, $tiempo_final);
                    $array_reporte[$fecha][$proceso]["detalles"][] = array(
                                                                            "turno" => $row['Turno'],
                                                                            "historia_clinica" => $row['hce'],
                                                                            "ingreso" => $row['ingreso'],
                                                                            "usuario_matrix" => '',
                                                                            "fecha_inicio" => $row['FecIniEspAdm'],
                                                                            "hora_inicio" => $row['HorIniEspAdm'],
                                                                            "fecha_fin" => $row['FecFinEspAdm'],
                                                                            "hora_fin" => $row['HorFinEspAdm'],
                                                                            "tiempo_total" => $tiempo_total,
                                                                            "procesoPpal" => $procesoPpal
                                                                            );
                }
            }
        }

        function indiceFecha(&$fecha)
        {
            $fecha = str_replace("-", "_", $fecha);
        }

        function recorrerProceso($result, &$array_reporte, $proceso, $fecha_inicio, $fecha_final)
        {

            while($row = mysql_fetch_array($result))
            {
                $fecha_index = '';

                if($proceso == "admisiones")
                {
                    $fecha_index = $row['FecFinAdm'];
                    indiceFecha($fecha_index);
                    $arr_procesosIndex = array("consultas","espera_consultas","triages","espera_triages","admisiones");
                    if(!array_key_exists($fecha_index, $array_reporte))
                    {
                        $array_reporte[$fecha_index] = array();
                        generarArrProcesos($arr_procesosIndex, $fecha_index, $array_reporte);
                    }
                    foreach ($arr_procesosIndex as $key => $idx_proceso)
                    {
                        $row2 =$row;
                        // echo print_r($idx_proceso, true);
                        // echo PHP_EOL;
                        // echo print_r($row, true);
                        // echo PHP_EOL;
                        $row2["FecIniAdm"]    = ($row2["FecIniAdm"] == '') ? '0000-00-00' : $row2["FecIniAdm"];
                        $row2["FecFinAdm"]    = ($row2["FecFinAdm"] == '') ? '0000-00-00' : $row2["FecFinAdm"];
                        $row2["HorIniAdm"]    = ($row2["HorIniAdm"] == '') ? '00:00:00' : $row2["HorIniAdm"];
                        $row2["HorFinAdm"]    = ($row2["HorFinAdm"] == '') ? '00:00:00' : $row2["HorFinAdm"];
                        $row2["FecIniEspAdm"] = ($row2["FecIniEspAdm"] == '') ? '0000-00-00' : $row2["FecIniEspAdm"];
                        $row2["FecFinEspAdm"] = ($row2["FecFinEspAdm"] == '') ? '0000-00-00' : $row2["FecFinEspAdm"];
                        $row2["HorIniEspAdm"] = ($row2["HorIniEspAdm"] == '') ? '00:00:00' : $row2["HorIniEspAdm"];
                        $row2["HorFinEspAdm"] = ($row2["HorFinEspAdm"] == '') ? '00:00:00' : $row2["HorFinEspAdm"];

                        // Validaciones para el caso en el cual se efectua el triage y la consulta al mismo tiempo
                        /*if(($row2['FecIniCon'] != '0000-00-00') &&
                            ($row2['FecIniTri'] == '0000-00-00' && $row2['FecFinTri'] != '0000-00-00'))
                        {
                            // $row2["FecIniTri"] = $row2["FecIniCon"];
                            // $row2["HorIniTri"] = $row2["HorIniCon"];

                            // $row2["FecIniEspTri"] = $row2["FecFinAdm"];
                            // $row2["FecFinEspTri"] = $row2["FecIniTri"];
                            // $row2["HorIniEspTri"] = $row2["HorFinAdm"];
                            // $row2["HorFinEspTri"] = $row2["HorIniTri"];

                            // Asignaciones de valores para la espera de triages
                            $row2["FecIniEspTri"] = $row2["FecFinAdm"];
                            $row2["HorIniEspTri"] = $row2["HorFinAdm"];
                            $row2["FecFinEspTri"] = $row2["FecIniCon"];
                            $row2["HorFinEspTri"] = $row2["HorIniCon"];

                            // Asignaciones de valores para la espera de consultas
                            $row2["FecIniEspCon"] = '0000-00-00';
                            $row2["HorIniEspCon"] = '00:00:00';
                            $row2["FecFinEspCon"] = '0000-00-00';
                            $row2["HorFinEspCon"] = '00:00:00';

                            // Asignaciones de valores para los triages
                            $row2["FecIniTri"] = $row2["FecIniCon"];
                            $row2["HorIniTri"] = $row2["HorIniCon"];
                        }
                        elseif($row2['FecIniTri'] == '0000-00-00' && $row2['FecFinTri'] != '0000-00-00')
                        {
                            // Asignaciones de valores para los triages
                            $row2["FecFinTri"] = '0000-00-00';
                            $row2["HorFinTri"] = '00:00:00';
                        }*/
                        // Validaciones para el caso en el cual se efectua el triage y la consulta al mismo tiempo
                        if($row2['FecIniTri'] == '0000-00-00' && $row2["FecIniCon"] != '0000-00-00')
                        {
                            // Asignaciones de valores para la espera de triages
                            $row2["FecIniEspTri"] = $row2["FecFinAdm"];
                            $row2["HorIniEspTri"] = $row2["HorFinAdm"];
                            $row2["FecFinEspTri"] = $row2["FecIniCon"];
                            $row2["HorFinEspTri"] = $row2["HorIniCon"];

                            // Asignaciones de valores para la espera de consultas
                            $row2["FecIniEspCon"] = '0000-00-00';
                            $row2["HorIniEspCon"] = '00:00:00';
                            $row2["FecFinEspCon"] = '0000-00-00';
                            $row2["HorFinEspCon"] = '00:00:00';

                            // Asignaciones de valores para los triages
                            $row2["FecIniTri"] = $row2["FecIniCon"];
                            $row2["HorIniTri"] = $row2["HorIniCon"];
                        }
                        if($row2["FecFinCon"] == '0000-00-00')
                        {
                            $row2["FecFinCon"] = $row2["FecIniCon"];
                            $row2["HorFinCon"] = $row2["HorIniCon"];
                        }
                        if($row2["FecIniAdm"] == '0000-00-00')
                        {
                            $row2["FecIniAdm"] = $row2["FecFinAdm"];
                            $row2["HorIniAdm"] = $row2["HorFinAdm"];
                        }
                        if($row2["FecIniTri"] == '0000-00-00' && $row2["FecFinTri"] != '0000-00-00')
                        {
                            $row2["FecIniTri"] = $row2["FecFinTri"];
                            $row2["HorIniTri"] = $row2["HorFinTri"];
                            $row2["FecFinEspTri"] = $row2["FecIniTri"];
                            $row2["HorFinEspTri"] = $row2["HorIniTri"];
                        }

                        $insertar = true;
                        if(($idx_proceso == 'admisiones') &&
                            ($row2["FecIniAdm"] == '0000-00-00' && $row2["FecFinAdm"] == '0000-00-00'))
                        {
                            $insertar = false;
                        }
                        elseif(($idx_proceso == 'espera_triages') &&
                            ($row2["FecFinEspTri"] == '0000-00-00' && $row2["FecIniCon"] == '0000-00-00'))
                        {
                            $insertar = false;
                        }
                        elseif(($idx_proceso == 'triages') &&
                            ($row2["FecIniTri"] == '0000-00-00' && $row2["FecFinTri"] == '0000-00-00') &&
                            ($row2["FecIniCon"] == '0000-00-00'))
                        {
                            $insertar = false;
                        }
                        elseif(($idx_proceso == 'espera_consultas') &&
                            ($row2["FecIniCon"] == '0000-00-00'))
                        {
                            $insertar = false;
                        }
                        elseif(($idx_proceso == 'consultas') &&
                            ($row2["FecIniCon"] == '0000-00-00' && $row2["FecFinCon"] == '0000-00-00'))
                        {
                            $insertar = false;
                        }

                        if($insertar == true)
                        {
                            agregarDetalle($arr_procesosIndex, $row2, $fecha_index, $idx_proceso, $array_reporte, $proceso);
                        }
                    }
                }
                elseif($proceso == "espera_admisiones")
                {
                    /*foreach ($arr_procesosIndex as $key => $idx_proceso)
                    {*/
                       // echo "> ".print_r($row,true);
                        $fecha_index = $row['FecFinEspAdm'];
                        indiceFecha($fecha_index);
                        $arr_procesosIndex = array("espera_admisiones");
                        if(!array_key_exists($fecha_index, $array_reporte))
                        {
                            $array_reporte[$fecha_index] = array();
                        }
                        if(!array_key_exists("espera_admisiones",$array_reporte[$fecha_index]))
                        {
                            // $array_reporte[$fecha_index] = array();
                            generarArrProcesos($arr_procesosIndex, $fecha_index, $array_reporte);
                        }
                        //if (($proceso == 'espera_admisiones') && ($row['FecFinAdm'] == '0000-00-00'))
                        {
                            // $fecha_index = $row['FecFinEspAdm'];
                            // indiceFecha($fecha_index);
                            // if(!array_key_exists($fecha_index, $array_reporte))
                            // {
                            //     $array_reporte[$fecha_index] = array();
                            // }

                            // $arr_procesosIndex = array("espera_admisiones");
                            // generarArrProcesos($arr_procesosIndex, $fecha_index, $array_reporte);

                            // foreach ($arr_procesosIndex as $key => $idx_proceso)
                            {
                                $row["FecIniAdm"] = '0000-00-00';
                                $row["FecFinAdm"] = '0000-00-00';
                                $row["HorIniAdm"] = '00:00:00';
                                $row["HorFinAdm"] = '00:00:00';

                                $row["FecIniAdm"]    = ($row["FecIniAdm"] == '') ? '0000-00-00' : $row["FecIniAdm"];
                                $row["FecFinAdm"]    = ($row["FecFinAdm"] == '') ? '0000-00-00' : $row["FecFinAdm"];
                                $row["HorIniAdm"]    = ($row["HorIniAdm"] == '') ? '00:00:00' : $row["HorIniAdm"];
                                $row["HorFinAdm"]    = ($row["HorFinAdm"] == '') ? '00:00:00' : $row["HorFinAdm"];
                                $row["FecIniEspAdm"] = ($row["FecIniEspAdm"] == '') ? '0000-00-00' : $row["FecIniEspAdm"];
                                $row["FecFinEspAdm"] = ($row["FecFinEspAdm"] == '') ? '0000-00-00' : $row["FecFinEspAdm"];
                                $row["HorIniEspAdm"] = ($row["HorIniEspAdm"] == '') ? '00:00:00' : $row["HorIniEspAdm"];
                                $row["HorFinEspAdm"] = ($row["HorFinEspAdm"] == '') ? '00:00:00' : $row["HorFinEspAdm"];

                                // echo "idx_proceso: $idx_proceso, fecha_index: $fecha_index: ".print_r($row,true);
                                agregarDetalle($arr_procesosIndex, $row, $fecha_index, $proceso, $array_reporte, $proceso);
                            }
                        }
                    //}
                }
            }



/*
                if($proceso == 'triages' && ($row['FecIni'] == '0000-00-00' && $row['HorIni'] == '00:00:00'))
                {
                    $row['FecIni'] = $row['FecIniCon'];
                    $row['HorIni'] = $row['HorIniCon'];
                    // $row['FecFin'] = $row['FecFinCon'];
                    // $row['HorFin'] = $row['HorFinCon'];
                    $fecha_index = str_replace ('-' , '_' , $row['FecFin']);
                }
                elseif(($proceso == 'espera_triages' && ($row['FecFin'] == '0000-00-00' && $row['HorFin'] == '00:00:00'))
                        || ($proceso == 'espera_triages' && ($row['FecFin'] < $row['FecIni'] && $row['HorFin'] < $row['HorFin'])))
                {
                    $row['FecFin'] = $row['FecIni'];
                    $row['HorFin'] = $row['HorIni'];
                    $fecha_index = str_replace ('-' , '_' , $row['FecIni']);
                }
                else
                {
                    $fecha_index = str_replace ('-' , '_' , $row['FecFin']);
                }


                if($fecha_inicio != $fecha_final)
                {
                    //
                }
                else
                {
                    //
                }

                $fecha = str_replace ('-' , '_' , $fecha_index);
                if(!array_key_exists($fecha, $array_reporte))
                {
                    $array_reporte[$fecha] = array();
                    $array_reporte[$fecha][$proceso] = array("detalles" => array());
                }

                $tiempo_inicial = $row['FecIni'] . " " . $row['HorIni'];
                $tiempo_final = $row['FecFin'] . " " . $row['HorFin'];
                $tiempo_total = restarHoras($tiempo_inicial, $tiempo_final);

                $array_reporte[$fecha][$proceso]["detalles"][] = array(
                                                                        "turno" => $row['Turno'],
                                                                        "historia_clinica" => $row['hce'],
                                                                        "usuario_matrix" => $row['usuario'],
                                                                        "fecha_inicio" => $row['FecIni'],
                                                                        "hora_inicio" => $row['HorIni'],
                                                                        "fecha_fin" => $row['FecFin'],
                                                                        "hora_fin" => $row['HorFin'],
                                                                        "tiempo_total" => $tiempo_total
                                                                        );*/
                /*if($proceso == 'consultas')
                {
                    $codigo_especialidad = $row['Cod_Especialidad'];
                    if(!array_key_exists("especialidades", $array_reporte[$fecha][$proceso]))
                    {
                        $array_reporte[$fecha][$proceso]["especialidades"] = array();
                    }
                    if(!array_key_exists($codigo_especialidad, $array_reporte[$fecha][$proceso]["especialidades"]))
                    {
                        $array_reporte[$fecha][$proceso]["especialidades"][$codigo_especialidad] = array();
                    }

                    $tiempo_inicialEsp = $row['FecIni'] . " " . $row['HorIni'];
                    $tiempo_finalEsp = $row['FecFin'] . " " . $row['HorFin'];
                    $tiempo_totalEsp = restarHoras($tiempo_inicialEsp, $tiempo_finalEsp);

                    $array_reporte[$fecha][$proceso]["especialidades"][$codigo_especialidad][] = array(
                                                                                                        "especialidad" => $row['Especialidad'],
                                                                                                        "turno" => $row['Turno'],
                                                                                                        "historia_clinica" => $row['hce'],
                                                                                                        "usuario_matrix" => $row['usuario'],
                                                                                                        "fecha_inicio" => $row['FecIni'],
                                                                                                        "hora_inicio" => $row['HorIni'],
                                                                                                        "fecha_fin" => $row['FecFin'],
                                                                                                        "hora_fin" => $row['HorFin'],
                                                                                                        "tiempo_total" => $tiempo_totalEsp
                                                                                                        );
                }*/
            //}
        }

        function llenarArrayEspecialidadTotal(&$array_consultaEsp_total, $cod_esp, $especialidad, $cantidad, $array_tiempos_totales_especialidades)
        {
            if(!array_key_exists($especialidad, $array_consultaEsp_total))
            {
                $array_consultaEsp_total[$cod_esp] = array();
                $array_consultaEsp_total[$cod_esp] = array(
                                                                "especialidad" => $especialidad,
                                                                "cantidad" => $cantidad,
                                                                "tiempos" => array()
                                                                );
                // $array_consultaEsp_total[$especialidad]
            }
            // else
            {
                $array_consultaEsp_total[$cod_esp]["cantidad"] += ($cantidad)*1;
                $array_consultaEsp_total[$cod_esp]["tiempos"] = array_merge($array_consultaEsp_total[$cod_esp]["tiempos"], $array_tiempos_totales_especialidades);
            }
        }
    /**
     * Lógica de los llamados AJAX de todo el programa
     */
    if(isset($accion) && isset($form))
    {
        include_once("root/comun.php");

        $wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
        $wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
        $wbasedato_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');

        //Centros de costos
        $sqlCco = " SELECT Ccocod, Ccocir, Ccourg
                    FROM {$wbasedato_movhos}_000011
                        WHERE Ccoest = 'on'
                        AND Ccourg ='on'";
        $arr_ccos = array();
        if($resultccos = mysql_query($sqlCco, $conex))
        {
            while($rowccos = mysql_fetch_array($resultccos))
            {
                // if(!array_key_exists($rowccos['Ccocod'], $arr_ccos))
                {
                    $arr_ccos[] = $rowccos['Ccocod'];
                }
            }
        }
        else
        {
            echo mysql_error();
        }
        $in_cco = implode("','",$arr_ccos);

        $data = array('error'=>0, 'mensaje'=>'', 'html'=>'', 'sql'=>'');
        $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

        switch($accion)
        {
            case 'load' :
                switch($form)
                {

                    //Carga los optiones de los selects de usuarios de acuerdo al proceso y
                    //las especialidades del proceso de consulta
                    case 'cargar_selects_usuarios_especialidades':

                        $wmovhos = $wbasedato;
                        $sql = "";
                        if($promedio == 'por_usuario')
                        {
                            //Consulta los usuarios que realizan admisiones
                            if($proceso == 'admision')
                            {
                                $sql = "SELECT  DISTINCT mh179.logusu AS codigo,
                                                            u.descripcion AS nombre
                                        FROM    {$wbasedato_movhos}_000179 AS mh179,
                                                usuarios AS u
                                        WHERE   mh179.logusu = u.codigo
                                                AND mh179.logacc = 'iniciaAdmision'";
                            }
                            //Consulta los usuarios que realizan triages
                            elseif($proceso == 'triage')
                            {
                                $sql = "SELECT  mh48.meduma AS codigo,
                                                CONCAT(mh48.medno1, ' ',
                                                        mh48.medno2, ' ',
                                                        mh48.medap1, ' ',
                                                        mh48.medap2) AS nombre
                                        FROM    {$wbasedato_movhos}_000048 AS mh48
                                        WHERE   mh48.medurg = 'on'
                                                AND mh48.medtri = 'on'
                                                ORDER BY nombre";
                            }
                            //Consulta los usuarios que realizan consultas
                            elseif($proceso == 'consulta')
                            {
                                $sql = "SELECT  mh48.meduma AS codigo,
                                                CONCAT(mh48.medno1, ' ',
                                                       mh48.medno2, ' ',
                                                       mh48.medap1, ' ',
                                                       mh48.medap2) AS nombre
                                        FROM    {$wbasedato_movhos}_000048 AS mh48
                                        WHERE   mh48.medurg = 'on'
                                                AND mh48.medtri <> 'on'
                                                ORDER BY nombre";
                            }
                        }
                        //Consulta las especialidades del proceso de consulta
                        elseif($promedio == 'por_especialidad')
                        {
                            $sql = "SELECT  hce35.conesp AS codigo,
                                            hce35.condes AS nombre
                                    FROM    {$wbasedato_hce}_000035 AS hce35
                                    WHERE   hce35.conesp NOT IN ('*', '')";
                        }

                        //Muestra las opciones que se cargan según el proceso seleccionado
                        $opciones = '<option value="">Seleccionar</option>';
                        if(!empty($sql))
                        {
                            if($result = mysql_query($sql, $conex))
                            {
                                while($row = mysql_fetch_array($result))
                                {
                                    $opciones .= '<option value="'.$row['codigo'].'">'.utf8_encode($row['nombre']).'</option>';
                                }
                            }
                            else
                            {
                                $data["error"] = 1;
                                $data["mensaje"] = 'No se pudo consultar';
                            }
                        }

                        $data["opciones"] = $opciones;
                        break;

                    //Carga los datos del reporte en una tabla con la siguiente información:
                    //Fecha, Usuario, Proceso(Admisión, Triage, Consulta)
                    //Especialidad(Sólo para el proceso Consulta), Hora incio, Hora Fin
                    //Tiempo Total, Tiempo Máximo y Tiempo Mínimo y Promedio de Tiempos
                    case 'cargar_procesos_fecha_malo':

                        $wmovhos = $wbasedato;
                        $filtro_admision_total = "";
                        $filtro_admision_por_usuario = "";
                        $filtro_triage_total = "";
                        $filtro_triage_por_usuario = "";
                        $filtro_consulta_total = "";
                        $filtro_consulta_por_usuario = "";
                        $filtro_consulta_especialidad = "";

                        $sql_tiempos = "";
                        $wespecialidad = "";

                        //Carga los datos del proceso de admisión
                        if($proceso == 'admision')
                        {
                            $sql_tiempos = "  SELECT mh179.fecha_data AS FecIni,
                                                       mh179.hora_data AS HorIni,
                                                       hc22.mtrfam As FecFin,
                                                       hc22.mtrham AS HorFin,
                                                       mh179.logusu,
                                                       u.descripcion AS usuario
                                                FROM   {$wmovhos}_000178 AS mh178,
                                                       {$wmovhos}_000179 AS mh179,
                                                       {$wbasedato_hce}_000022 AS hc22,
                                                       usuarios AS u
                                                WHERE  mh179.fecha_data BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_final . "'
                                                       AND mh178.Atutur = mh179.Logtur
                                                       AND mh178.atutur = hc22.mtrtur
                                                       AND hc22.mtrfam <> '0000-00-00'
                                                       AND mh179.logusu = u.codigo
                                                       AND mh179.logacc = 'iniciaAdmision'";

                            if($promedio == 'total')
                            {
                                $filtro_admision_total = " ORDER BY mh179.fecha_data, mh179.hora_data";
                            }
                            elseif($promedio == 'por_usuario')
                            {
                                $filtro_admision_por_usuario = " AND u.codigo = '" . $codigo_usuario . "'
                                                                    ORDER BY mh179.fecha_data, mh179.hora_data";
                            }
                        }
                        //Carga los datos del proceso de triage
                        elseif($proceso == 'triage')
                        {
                            $sql_tiempos = "  SELECT hce22.mtrfco AS FecIni,
                                                        hce22.mtrhco AS HorIni,
                                                        hce22.mtrftr AS FecFin,
                                                        hce22.mtrhtr AS HorFin,
                                                        u.descripcion AS usuario
                                                FROM    {$wbasedato_hce}_000022 AS hce22,
                                                        usuarios AS u
                                                WHERE   hce22.mtrfco BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_final . "'
                                                        AND u.codigo = '0104014'
                                                        AND hce22.mtrfco <> '0000-00-00'
                                                        AND hce22.mtrftr <> '0000-00-00'";

                            if($promedio == 'total')
                            {
                                $filtro_triage_total = " ORDER BY hce22.mtrfco, hce22.mtrhco";
                            }
                            elseif($promedio == 'por_usuario')
                            {
                                $filtro_triage_por_usuario = "AND u.codigo = '" . $codigo_usuario . "'
                                                                ORDER BY hce22.mtrfco, hce22.mtrhco";
                            }
                        }
                        //Carga los datos del proceso de consulta
                        elseif($proceso == 'consulta')
                        {
                            $sql_tiempos = "  SELECT    hce22.mtrfco AS FecIni,
                                                        hce22.mtrhco AS HorIni,
                                                        hce22.mtrftc AS FecFin,
                                                        hce22.mtrhtc AS HorFin,
                                                        hce35.condes AS Especialidad,
                                                        u.descripcion AS usuario
                                                FROM    {$wbasedato_hce}_000022 AS hce22,
                                                        {$wbasedato_hce}_000035 AS hce35,
                                                        usuarios AS u
                                                WHERE   hce22.mtrfco BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_final . "'
                                                        AND mtrmed = u.codigo
                                                        AND hce22.mtrfco <> '0000-00-00'
                                                        AND hce22.mtrhco <> '00:00:00'
                                                        AND hce22.mtrftc <> '0000-00-00'
                                                        AND hce22.mtrhtc <> '00:00:00'
                                                        AND hce22.mtretr = hce35.conesp";

                            if($promedio == 'total')
                            {
                                $filtro_consulta_total = " ORDER BY hce22.mtrfco, hce22.mtrhco";
                            }
                            elseif($promedio == 'por_usuario')
                            {
                                $filtro_consulta_por_usuario = "AND u.codigo = '" . $codigo_usuario . "'
                                                                ORDER BY hce22.mtrfco, hce22.mtrhco";
                            }
                            elseif ($promedio == 'por_especialidad') {
                                $filtro_consulta_especialidad = "AND hce22.mtreme = '" . $codigo_especialidad . "'
                                                                ORDER BY hce22.mtrfco, hce22.mtrhco";
                            }
                        }

                        $sql_tiempos = $sql_tiempos . " " . $filtro_admision_total . " " . $filtro_admision_por_usuario . " "
                                        . $filtro_triage_total . " " . $filtro_triage_por_usuario
                                        . $filtro_consulta_total . " " . $filtro_consulta_por_usuario . " " . $filtro_consulta_especialidad;
                        $reg_tiempos = mysql_query($sql_tiempos, $conex) or die($sql_tiempos.' - '.mysql_error());
                        $array_tiempos = array();
                        while($row = mysql_fetch_array($reg_tiempos))
                        {
                            $array_tiempos[] = $row;
                        }
                        //Muestra la tabla con los datos arrojados por la consulta
                        $html = '<tr class="encabezadoTabla encabezadoTabla2">
                                    <th>Fecha Proceso</th>
                                    <th>Usuario</th>
                                    <th>Proceso</th>
                                    <th>Especialidad</th>
                                    <th>Hora inicio</th>
                                    <th>Hora fin</th>
                                    <th>Tiempo total</th>
                                </tr>';
                        if(count($array_tiempos) > 0)
                        {
                            $cont = 0;
                            $array_duraciones = array();
                            foreach($array_tiempos as $index => $row) {
                                $cont++;
                                $tiempo_inicial = $row['FecIni'] . " " . $row['HorIni'];
                                $tiempo_final = $row['FecFin'] . " " . $row['HorFin'];
                                //$tiempo_total = restarHoras($row['HorIni'], $row['HorFin']);
                                $tiempo_total = restarHoras($tiempo_inicial, $tiempo_final);
                                array_push($array_duraciones, $tiempo_total);
                                $css = ($cont % 2 == 0) ? 'fila1': 'fila2';
                                if($row['FecIni'] != $row['FecFin'])
                                {
                                    $hora_inicio = $row['FecIni'] . " " . $row['HorIni'];
                                    $hora_fin = $row['FecFin'] . " " . $row['HorFin'];
                                } else
                                {
                                    $hora_inicio = $row['HorIni'];
                                    $hora_fin = $row['HorFin'];
                                }
                                if($row['Especialidad'] != '' || $row['Especialidad'] != null)
                                {
                                    $wespecialidad = $row['Especialidad'];
                                }
                                $html .= '<tr class="' . $css . ' find">
                                    <td>' . $row['FecIni'] . '</td>
                                    <td>' . utf8_encode($row['usuario']) . '</td>
                                    <td>' . utf8_encode($proceso) . '</td>
                                    <td>' . $wespecialidad . '</td>
                                    <td>' . $hora_inicio . '</td>
                                    <td>' . $hora_fin . '</td>
                                    <td>' . $tiempo_total . '</td>
                                </tr>';
                            }
                            $tiempo_maximo = tiempoMaximo($array_duraciones);
                            $tiempo_minimo = tiempoMinimo($array_duraciones);
                            $tiempo_promedio = tiempoPromedio($array_duraciones);
                            //Muestra la información de los tiempos máximo, mínimo y promedio
                            $html .= '<tr class="encabezadoTabla encabezadoTabla2">
                                            <td style="text-align:center;"></td>
                                            <td style="text-align:center;">Tiempo Máximo</td>
                                            <td style="text-align:center;">' . $tiempo_maximo . '</td>
                                            <td style="text-align:center;">Tiempo Mínimo</td>
                                            <td style="text-align:center;">' . $tiempo_minimo . '</td>
                                            <td style="text-align:center;">Tiempo Promedio</td>
                                            <td style="text-align:center;">' . $tiempo_promedio . '</td>
                                        </tr>';

                        }
                        else
                        {
                            $html .= '<tr class="fila1 find">
                                            <td colspan="7" style="text-align:center;">NO SE ENCONTRARON DATOS!</td>
                                        </tr>';
                        }
                        $data["html"] = $html;
                        $data["sql"] = $sql_tiempos;

                        break;

                    case 'cargar_procesos_fecha':

                        $wmovhos = $wbasedato;
                        $sql_admisiones = "";
                        $sql_triages = "";
                        $sql_consultas = "";
                        $array_reporte = array();


                        $proceso = "";

                        $proceso = 'espera_admisiones';

                        $filtros_clasificacion = "";
                        if($clasificacion_atencion == '00')
                        {
                            $filtros_clasificacion = "";
                        }
                        elseif($clasificacion_atencion == '06')
                        {
                            $filtros_clasificacion = "
                                                    LEFT JOIN      {$wbasedato_cliame}_000101 AS cl101 ON (rt37.orihis = cl101.Inghis AND rt37.oriing = cl101.Ingnin AND cl101.Ingtpa='P')";
                        }
                        else
                        {
                            $filtros_clasificacion = "
                                                    LEFT JOIN      {$wbasedato_cliame}_000101 AS cl101 ON (rt37.orihis = cl101.Inghis AND rt37.oriing = cl101.Ingnin AND cl101.Ingtpa='E')
                                                    LEFT JOIN      {$wbasedato_cliame}_000024 AS cl24 ON (cl24.Empcod = cl101.Ingcem
                                                                                                  AND cl24.Empcla = '{$clasificacion_atencion}'
                                                                                                )";
                        }

                        $where_entre_fechas = "";
                        if($fecha_inicio != $fecha_final)
                        {
                            $where_entre_fechas = "
                                                    WHERE mh178.atufll BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_final . "'";
                        }
                        else
                        {
                            $where_entre_fechas = "
                                                    WHERE mh178.atufll = '" . $fecha_final . "'";
                        }

                        $sql_tiempos_espera_admisiones = "  SELECT              mh178.Fecha_data AS FecIniEspAdm,
                                                                                mh178.Hora_data AS HorIniEspAdm,
                                                                                mh178.Atufll AS FecFinEspAdm,
                                                                                mh178.Atuhll AS HorFinEspAdm,
                                                                                mh178.Atutur AS Turno,
                                                                                hce22.mtrhis AS hce,
                                                                                hce22.mtring AS ingreso,
                                                                                '' AS usuario

                                                            FROM                {$wbasedato_movhos}_000178 AS mh178
                                                                    -- LEFT JOIN  root_000037 AS rt37 ON (rt37.oriced = mh178.atudoc AND rt37.oritid = mh178.atutdo AND rt37.Oriori = '{$wemp_pmla}')
                                                                    -- LEFT JOIN  {$wbasedato_hce}_000022 AS hce22 ON (rt37.orihis = hce22.mtrhis AND rt37.oriing = hce22.mtring)
                                                                    LEFT JOIN  {$wbasedato_hce}_000022 AS hce22 ON (mh178.Atutur = hce22.Mtrtur AND hce22.mtrcci IN ('{$in_cco}'))
                                                            {$where_entre_fechas}
                                                                    ORDER BY    mh178.fecha_data, mh178.hora_data
                                                    ";
                                                            // WHERE               mh178.fecha_data BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_final . "'

                            // Propuesta 2: El fin de la espera se da con el inicio de la admisión
                            /*$sql_tiempos_espera_admisiones = " SELECT               mh178.fecha_data AS FecIni,
                                                                                    mh178.hora_data AS HorIni,
                                                                                    mh178.atufll AS FecFin,
                                                                                    mh178.atuhll AS HorFin,
                                                                                    mh178.atutur AS Turno,
                                                                                    rt37.orihis AS hce,
                                                                                    '' AS usuario
                                                                FROM                movhos_000178 AS mh178
                                                                        INNER JOIN  root_000037 AS rt37 ON (rt37.oriced = mh178.atudoc
                                                                                                            AND mh178.fecha_data <> '0000-00-00'
                                                                                                            AND mh178.hora_data <> '00:00:00'
                                                                                                            AND mh178.atufll <> '0000-00-00'
                                                                                                            AND mh178.atuhll <> '00:00:00')
                                                                WHERE               mh178.fecha_data BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_final . "'
                                                                        ORDER BY    mh178.fecha_data, mh178.hora_data
                                                        ";*/

                        $reg_tiempos_espera_admisiones = mysql_query($sql_tiempos_espera_admisiones, $conex) or die($sql_tiempos_espera_admisiones.' - '.mysql_error());
                        recorrerProceso($reg_tiempos_espera_admisiones, $array_reporte, $proceso, $fecha_inicio, $fecha_final);
                        $num = mysql_num_rows($reg_tiempos_espera_admisiones);


                        $proceso = 'admisiones';

                        $filtros_clasificacion = "";
                        if($clasificacion_atencion == '00')
                        {
                            $filtros_clasificacion = "";
                        }
                        elseif($clasificacion_atencion == '06')
                        {
                            $filtros_clasificacion = "
                                                    INNER JOIN      {$wbasedato_cliame}_000101 AS cl101 ON (hce22.mtrhis = cl101.Inghis AND hce22.mtring = cl101.Ingnin AND cl101.Ingtpa='P')";
                        }
                        else
                        {
                            $filtros_clasificacion = "
                                                    INNER JOIN      {$wbasedato_cliame}_000101 AS cl101 ON (hce22.mtrhis = cl101.Inghis AND hce22.mtring = cl101.Ingnin AND cl101.Ingtpa='E')
                                                    INNER JOIN      {$wbasedato_cliame}_000024 AS cl24 ON (cl24.Empcod = cl101.Ingcem
                                                                                                  AND cl24.Empcla = '{$clasificacion_atencion}'
                                                                                                )";
                        }

                        $where_entre_fechas = "";
                        // if($fecha_inicio != $fecha_final)
                        {
                            $where_entre_fechas = "
                                                    WHERE hce22.Fecha_data BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_final . "'";
                        }
                        /*else
                        {
                            $where_entre_fechas = "
                                                    WHERE mh178.atufad = '" . $fecha_final . "'";
                        }*/

                        /*$sql_tiempos_admisiones = " SELECT                  MAX(mh179.fecha_data) AS FecIniAdm,
                                                                            mh179.hora_data AS HorIniAdm,
                                                                            mh178.atufad AS FecFinAdm,
                                                                            mh178.atuhad AS HorFinAdm,
                                                                            mh178.fecha_data AS FecIniEspAdm,
                                                                            mh178.hora_data AS HorIniEspAdm,
                                                                            mh178.atufll AS FecFinEspAdm,
                                                                            mh178.atuhll AS HorFinEspAdm,
                                                                            hce22.mtrfco AS FecIniCon,
                                                                            hce22.mtrhco AS HorIniCon,
                                                                            hce22.mtrfac AS FecFinCon,
                                                                            hce22.mtrhac AS HorFinCon,
                                                                            mh178.atutur AS Turno,
                                                                            mh179.logusu,
                                                                            u.descripcion AS usuario,
                                                                            -- rt37.orihis AS hce
                                                                            hce22.mtrhis AS hce
                                                    FROM    movhos_000178 AS mh178
                                                            INNER JOIN      movhos_000179 AS mh179 ON (mh178.atutur = mh179.logtur  AND mh179.logacc = 'iniciaAdmision')
                                                            -- INNER JOIN      root_000037 AS rt37 ON (rt37.oriced = mh178.atudoc AND rt37.oritid = mh178.atutdo AND rt37.Oriori = '{$wemp_pmla}')
                                                            INNER JOIN      hce_000022 AS hce22 ON (mh178.atutur = hce22.mtrtur)
                                                            INNER JOIN      usuarios AS u ON (u.codigo = mh179.logusu
                                                                                                        -- AND mh179.fecha_data <> '0000-00-00'
                                                                                                        -- AND mh179.hora_data <> '00:00:00'
                                                                                                        )
                                                            INNER JOIN      movhos_000011 AS mh11 ON (mh11.Ccocod = hce22.mtrcci AND mh11.Ccourg='on')
                                                            {$filtros_clasificacion}
                                                            {$where_entre_fechas}
                                                                                                        AND mh178.atufad <> '0000-00-00'
                                                            GROUP BY        mh178.atutur
                                                            ORDER BY        mh179.fecha_data, mh179.hora_data
                                                    ";*/
                                                            // -- WHERE           mh179.fecha_data BETWEEN '" . $fecha_inicio . "' AND '" . $fecha_final . "'
                                                    // echo $sql_tiempos_admisiones;

                        $sql_tiempos_admisiones = " SELECT                      hce22.Mtrfia        AS  FecIniAdm,
                                                                                hce22.Mtrhia        AS  HorIniAdm,
                                                                                hce22.Fecha_data    AS  FecFinAdm,
                                                                                hce22.hora_data     AS  HorFinAdm,
                                                                                hce22.Fecha_data    AS  FecIniEspTri,
                                                                                hce22.Hora_data     AS  HorIniEspTri,
                                                                                hce22.Mtrfit        AS  FecFinEspTri,
                                                                                hce22.Mtrhit        AS  HorFinEspTri,
                                                                                hce22.Mtrfit        AS  FecIniTri,
                                                                                hce22.Mtrhit        AS  HorIniTri,
                                                                                hce22.Mtrftr        AS  FecFinTri,
                                                                                hce22.Mtrhtr        AS  HorFinTri,
                                                                                hce22.Mtrftr        AS  FecIniEspCon,
                                                                                hce22.Mtrhtr        AS  HorIniEspCon,
                                                                                hce22.Mtrfco        AS  FecFinEspCon,
                                                                                hce22.Mtrhco        AS  HorFinEspCon,
                                                                                hce22.Mtrfco        AS  FecIniCon,
                                                                                hce22.Mtrhco        AS  HorIniCon,
                                                                                hce22.Mtrfac        AS  FecFinCon,
                                                                                hce22.Mtrhac        AS  HorFinCon,

                                                                                hce22.mtrtur        AS Turno,
                                                                                hce22.mtrhis        AS hce,
                                                                                hce22.mtring        AS ingreso,
                                                                                u.descripcion       AS usuario,
                                                                                hce22.mtrcci,
                                                                                hce22.mtretr        AS Cod_Especialidad,
                                                                                hce35.condes AS Especialidad

                                                    FROM                        {$wbasedato_hce}_000022          AS  hce22
                                                                LEFT JOIN      {$wbasedato_hce}_000035          AS  hce35   ON (hce22.Mtretr = hce35.Conesp)
                                                                -- INNER JOIN      {$wbasedato_movhos}_000011       AS  mh11    ON (mh11.Ccocod = hce22.Mtrcci AND mh11.Ccourg='on')
                                                                left JOIN       usuarios            AS  u       ON (u.Codigo = hce22.Mtrmed)
                                                    {$filtros_clasificacion}
                                                    {$where_entre_fechas}
                                                                AND hce22.Mtrcci IN ('{$in_cco}')
                                                                -- LEFT JOIN   {$wbasedato_movhos}_000178        AS  mh178   ON (mh178.Atutur = hce22.Mtrtur)
                                                                -- LEFT JOIN   {$wbasedato_movhos}_000179        AS  mh179   ON (mh178.Atutur = mh179.Logtur)

                                                    -- WHERE       hce22.Fecha_data    BETWEEN '2015-11-16' AND '2015-11-16'
                                                                -- GROUP BY    mh178.atutur
                                                                GROUP BY    hce22.Mtrhis, hce22.Mtring
                                                                ORDER BY    hce22.Fecha_data, hce22.Hora_data;
                                                    ";

                        $reg_tiempos_admisiones = mysql_query($sql_tiempos_admisiones, $conex) or die($sql_tiempos_admisiones.' - '.mysql_error());
                        recorrerProceso($reg_tiempos_admisiones, $array_reporte, $proceso, $fecha_inicio, $fecha_final);
                        $num = mysql_num_rows($reg_tiempos_admisiones);

                        // echo print_r($array_reporte,true);

                        $html = '<tr class=encabezadoTabla encabezadoTabla2>
                                    <td align=center rowspan=4><font size=4><b>DIA</b></font></td>
                                    <td align=center colspan=12><font size=4><b>Tiempo Promedio en cada proceso de Urgencias</b></font></td>
                                </tr>
                                <tr class=encabezadoTabla encabezadoTabla2>
                                    <td align=center colspan=12 class=fila1><font size=4><b>Tiempo en HH:MM:SS</b></font></td>
                                </tr>

                                <tr>
                                    <td align=center colspan=2 rowspan=1 class=fila2><font size=4><b>ESPERA DE ADMISIÓN</b></font></td>
                                    <td align=center colspan=2 rowspan=1 class=fila1><font size=4><b>ADMISIÓN</b></font></td>
                                    <td align=center colspan=2 rowspan=1 class=fila2><font size=4><b>ESPERA DE TRIAGE</b></font></td>
                                    <td align=center colspan=2 rowspan=1 class=fila1><font size=4><b>TRIAGE</b></font></td>
                                    <td align=center colspan=2 rowspan=1 class=fila2><font size=4><b>ESPERA DE CONSULTA</b></font></td>
                                    <td align=center colspan=2 rowspan=1 class=fila1><font size=4><b>CONSULTA</b></font></td>
                                </tr>


                                <tr class=fila1>
                                    <td class=fila2 align=center><b>Cantidad</b></td>
                                    <td class=fila2 align=center><b>Tiempo Promedio</b></td>
                                    <td class=fila1 align=center><b>Cantidad</b></td>
                                    <td class=fila1 align=center><b>Tiempo Promedio</b></td>
                                    <td class=fila2 align=center><b>Cantidad</b></td>
                                    <td class=fila2 align=center><b>Tiempo Promedio</b></td>
                                    <td class=fila1 align=center><b>Cantidad</b></td>
                                    <td class=fila1 align=center><b>Tiempo Promedio</b></td>
                                    <td class=fila2 align=center><b>Cantidad</b></td>
                                    <td class=fila2 align=center><b>Tiempo Promedio</b></td>
                                    <td class=fila1 align=center><b>Cantidad</b></td>
                                    <td class=fila1 align=center><b>Tiempo Promedio</b></td>
                                </tr>';

                            if(count($array_reporte) > 0)
                            {
                                $array_duraciones = array();
                                $colorF         = 'fila2';
                                $primeraVez     = true;
                                $tiempoTotGen   = 0;
                                $totalPacGen    = 0;
                                $html_dias = '';
                                $cont_dias = 0;

                                $array_tiempos_espera_admision_totales = array();
                                $array_tiempos_admision_totales = array();
                                $array_tiempos_espera_triage_totales = array();
                                $array_tiempos_triage_totales = array();
                                $array_tiempos_espera_consulta_totales = array();
                                $array_tiempos_consulta_totales = array();
                                $array_tiempos_consulta_totalesEsp = array();

                                $html_espera_admision_total = ' <table id="tab_espera_admision_total" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th hidden="true">Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th colspan="2">Tiempo total</th>
                                                                </tr>';

                                $html_admision_total = '<table id="tab_admision_total" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th>Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th>Tiempo total</th>
                                                                </tr>';

                                $html_espera_triage_total = ' <table id="tab_espera_triage_total" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th hidden="true">Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th colspan="2">Tiempo total</th>
                                                                </tr>';

                                $html_triage_total = '<table id="tab_triage_total" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th>Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th>Tiempo total</th>
                                                                </tr>';

                                $html_espera_consulta_total = ' <table id="tab_espera_consulta_total" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th hidden="true">Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th colspan="2">Tiempo total</th>
                                                                </tr>';

                                $html_consulta_total = '<table id="tab_consulta_total" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th>Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th>Tiempo total</th>
                                                                </tr>';

                                $html_consultaEsp_total .= ' <table id="tab_consultaEsp_total" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Especialidad</th>
                                                                    <th>Cantidad</th>
                                                                    <th>Promedio</th>
                                                                </tr>';

                                $array_consultaEsp_total = array();

                                $cont_esp_adm_tot = 0;
                                $cont_adm_tot = 0;
                                $cont_esp_tri_tot = 0;
                                $cont_tri_tot = 0;
                                $cont_esp_con_tot = 0;
                                $cont_con_tot = 0;
                                $cont_conEsp_tot = 0;

                                $css_esp_adm_tot = 0;
                                $css_adm_tot = 0;
                                $css_esp_tri_tot = 0;
                                $css_tri_tot = 0;
                                $css_esp_con_tot = 0;
                                $css_con_tot = 0;
                                $css_conEsp_tot = 0;

                                foreach($array_reporte as $fecha_proceso => $info_proceso)
                                {
                                    $html_admision = '';
                                    $html_triage = '';
                                    $html_consulta = '';
                                    $html_consultaEsp = '';

                                    $html_espera_admision = '';
                                    $html_espera_triage = '';
                                    $html_espera_consulta = '';

                                    $cant_admision = 0;
                                    $prom_admision = 0;
                                    $cant_triage = 0;
                                    $prom_triage = 0;
                                    $cant_consulta = 0;
                                    $prom_consulta = 0;
                                    $cant_consultaEsp = 0;
                                    $prom_consultaEsp = 0;

                                    $cant_espera_admision = 0;
                                    $prom_espera_admision = 0;
                                    $cant_espera_triage = 0;
                                    $prom_espera_triage = 0;
                                    $cant_espera_consulta = 0;
                                    $prom_espera_consulta = 0;
                                    $cant_espera_consultaEsp = 0;
                                    $prom_espera_consultaEsp = 0;

                                    if(array_key_exists('admisiones', $info_proceso))
                                    {
                                        $array_tiempos_admision = array();
                                        $cant_admision = count($info_proceso['admisiones']['detalles']);
                                        $html_admision .= ' <table id="tab_admision_'. $fecha_proceso . '" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th>Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th>Tiempo total</th>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center" colspan="6">
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'todos\', \'tab_admision_'. $fecha_proceso . '\', this)" value="todos" checked="checked"><b>Todos</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'con_turno\', \'tab_admision_'. $fecha_proceso . '\', this)" value="con_turno"><b>Con Turno</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'sin_turno\', \'tab_admision_'. $fecha_proceso . '\', this)" value="sin_turno"><b>Sin Turno</b>
                                                                    </td>
                                                                </tr>'
                                                            ;
                                        $cont_adm = 0;
                                        foreach ($info_proceso['admisiones']['detalles'] as $key => $info_detalle) {
                                            array_push($array_tiempos_admision, $info_detalle['tiempo_total']);
                                            array_push($array_tiempos_admision_totales, $info_detalle['tiempo_total']);
                                            $hora_inicio = '';
                                            $hora_fin = '';
                                            $cont_adm++;
                                            $cont_adm_tot++;
                                            $css_adm = ($cont_adm % 2 == 0) ? 'fila1': 'fila2';
                                            $css_adm_tot = ($cont_adm_tot % 2 == 0) ? 'fila1': 'fila2';
                                            if($info_detalle['fecha_inicio'] != $info_detalle['fecha_fin'])
                                            {
                                                $hora_inicio = $info_detalle['fecha_inicio'] . " " . $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['fecha_fin'] . " " . $info_detalle['hora_fin'];
                                            } else
                                            {
                                                $hora_inicio = $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['hora_fin'];
                                            }

                                             $turnoPosible = "";

                                            // --> JERSON
                                            if($info_detalle['turno'] == '')
                                            {
                                                $SQLPosibleTurno = "
                                                SELECT Atutur
                                                  FROM {$wbasedato_cliame}_000100, movhos_000178 AS B
                                                 WHERE Pachis = '".$info_detalle['historia_clinica']."'
                                                   AND Pacdoc = Atudoc
                                                   AND Pactdo = Atutdo
                                                   AND B.Fecha_data = '".$info_detalle['fecha_fin']."'
                                                ";
                                                 $RESPosibleTurno = mysql_query($SQLPosibleTurno, $conex) or die($SQLPosibleTurno.' - '.mysql_error());
                                                 if($rowPosibleTurno = mysql_fetch_array($RESPosibleTurno))
                                                {
                                                    $turnoPosible =  $rowPosibleTurno['Atutur'];
                                                }

                                            }

                                            $html_admision .= '<tr class="' . $css_adm . ' find" turno="'.(($info_detalle['turno'] != '') ? 'si' : 'no').'">
                                                                    <td align="center" ppal="'.$info_detalle['procesoPpal'].'">' .(($info_detalle['turno'] != '') ? $info_detalle['turno'] : "<span style='color:red;'>".$turnoPosible."</span>") . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';
                                            $html_admision_total .= '<tr class="' . $css_adm_tot . ' find">
                                                                    <td align="center">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';
                                        }

                                        $tiempo_maximo_admision = tiempoMaximo($array_tiempos_admision);
                                        $tiempo_minimo_admision = tiempoMinimo($array_tiempos_admision);
                                        $tiempo_promedio_admision = tiempoPromedio($array_tiempos_admision);
                                        //Muestra la información de los tiempos máximo, mínimo y promedio
                                        $html_admision .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_admision . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_admision . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_admision . '</td>
                                                    </tr>';

                                        $html_admision .= '</table>';
                                        $prom_admision = tiempoPromedio($array_tiempos_admision);
                                    }

                                    if(array_key_exists('triages', $info_proceso))
                                    {
                                        $array_tiempos_triage = array();
                                        $cant_triage = count($info_proceso['triages']['detalles']);
                                        $html_triage .= ' <table id="tab_triage_'. $fecha_proceso . '" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th>Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th>Tiempo total</th>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center" colspan="6">
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'todos\', \'tab_triage_'. $fecha_proceso . '\')" value="todos" checked="checked"><b>Todos</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'con_turno\', \'tab_triage_'. $fecha_proceso . '\')" value="con_turno"><b>Con Turno</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'sin_turno\', \'tab_triage_'. $fecha_proceso . '\')" value="sin_turno"><b>Sin Turno</b>
                                                                    </td>
                                                                </tr>'
                                                            ;
                                        $cont_tri = 0;
                                        foreach ($info_proceso['triages']['detalles'] as $key => $info_detalle) {
                                            array_push($array_tiempos_triage, $info_detalle['tiempo_total']);
                                            array_push($array_tiempos_triage_totales, $info_detalle['tiempo_total']);
                                            $hora_inicio = '';
                                            $hora_fin = '';
                                            $cont_tri++;
                                            $cont_tri_tot++;
                                            $css_tri = ($cont_tri % 2 == 0) ? 'fila1': 'fila2';
                                            $css_tri_tot = ($cont_tri_tot % 2 == 0) ? 'fila1': 'fila2';
                                            if($info_detalle['fecha_inicio'] != $info_detalle['fecha_fin'])
                                            {
                                                $hora_inicio = $info_detalle['fecha_inicio'] . " " . $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['fecha_fin'] . " " . $info_detalle['hora_fin'];
                                            } else
                                            {
                                                $hora_inicio = $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['hora_fin'];
                                            }

                                            $html_triage .= '<tr class="' . $css_tri . ' find" turno="'.(($info_detalle['turno'] != '') ? 'si' : 'no').'">
                                                                    <td align="center" ppal="'.$info_detalle['procesoPpal'].'">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';

                                            $html_triage_total .= '<tr class="' . $css_tri_tot . ' find">
                                                                    <td align="center">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';
                                        }

                                        $tiempo_maximo_triage = tiempoMaximo($array_tiempos_triage);
                                        $tiempo_minimo_triage = tiempoMinimo($array_tiempos_triage);
                                        $tiempo_promedio_triage = tiempoPromedio($array_tiempos_triage);
                                        //Muestra la información de los tiempos máximo, mínimo y promedio
                                        $html_triage .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_triage . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_triage . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_triage . '</td>
                                                    </tr>';

                                        $html_triage .= '</table>';
                                        $prom_triage = tiempoPromedio($array_tiempos_triage);
                                    }

                                    if(array_key_exists('consultas', $info_proceso))
                                    {
                                        $array_tiempos_consulta = array();
                                        $cant_consulta = count($info_proceso['consultas']['detalles']);
                                        $html_consulta .= ' <table id="tab_consulta_'. $fecha_proceso . '" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th>Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th>Tiempo total</th>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center" colspan="6">
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'todos\', \'tab_consulta_'. $fecha_proceso . '\')" value="todos" checked="checked"><b>Todos</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'con_turno\', \'tab_consulta_'. $fecha_proceso . '\')" value="con_turno"><b>Con Turno</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'sin_turno\', \'tab_consulta_'. $fecha_proceso . '\')" value="sin_turno"><b>Sin Turno</b>
                                                                    </td>
                                                                </tr>'
                                                            ;
                                        $cont_con = 0;
                                        foreach ($info_proceso['consultas']['detalles'] as $key => $info_detalle) {
                                            array_push($array_tiempos_consulta, $info_detalle['tiempo_total']);
                                            array_push($array_tiempos_consulta_totales, $info_detalle['tiempo_total']);
                                            $hora_inicio = '';
                                            $hora_fin = '';
                                            $cont_con++;
                                            $cont_con_tot++;
                                            $css_con = ($cont_con % 2 == 0) ? 'fila1': 'fila2';
                                            $css_con_tot = ($cont_con_tot % 2 == 0) ? 'fila1': 'fila2';
                                            if($info_detalle['fecha_inicio'] != $info_detalle['fecha_fin'])
                                            {
                                                $hora_inicio = $info_detalle['fecha_inicio'] . " " . $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['fecha_fin'] . " " . $info_detalle['hora_fin'];
                                            } else
                                            {
                                                $hora_inicio = $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['hora_fin'];
                                            }
                                            $html_consulta .= '<tr class="' . $css_con . ' find" turno="'.(($info_detalle['turno'] != '') ? 'si' : 'no').'">
                                                                    <td align="center" ppal="'.$info_detalle['procesoPpal'].'">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';

                                            $html_consulta_total .= '<tr class="' . $css_con_tot . ' find">
                                                                    <td align="center">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';
                                        }

                                        $tiempo_maximo_consulta = tiempoMaximo($array_tiempos_consulta);
                                        $tiempo_minimo_consulta = tiempoMinimo($array_tiempos_consulta);
                                        $tiempo_promedio_consulta = tiempoPromedio($array_tiempos_consulta);
                                        //Muestra la información de los tiempos máximo, mínimo y promedio
                                        $html_consulta .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_consulta . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_consulta . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_consulta . '</td>
                                                    </tr>';

                                        $html_consulta .= '</table>';
                                        $prom_consulta = tiempoPromedio($array_tiempos_consulta);
                                    }

                                    if(array_key_exists('consultas', $info_proceso) && array_key_exists('especialidades', $info_proceso['consultas']))
                                    {
                                        $cant_consultaEsp = count($info_proceso['consultas']['especialidades']);

                                        $cont_con = 0;
                                        $tr_especialidad = '';
                                        foreach ($info_proceso['consultas']['especialidades'] as $cod_esp => $arr_info_detalle) // Especialidades
                                        {
                                            if(!array_key_exists($cod_esp, $array_consultaEsp_total))
                                            {
                                                $array_consultaEsp_total[$cod_esp] = array( "especialidad" => '',
                                                                                            "cantidad" => count($arr_info_detalle),
                                                                                            "tiempos" => array());
                                            }
                                            else
                                            {
                                                $array_consultaEsp_total[$cod_esp]['cantidad'] += count($arr_info_detalle);
                                            }

                                            $array_tiempos_consultaEsp = array();
                                            $cont_con++;
                                            $css_con = ($cont_con % 2 == 0) ? 'fila1': 'fila2';
                                            $cont_conEsp_tot++;
                                            $css_conEsp_tot = ($cont_conEsp_tot % 2 == 0) ? 'fila1': 'fila2';

                                            $nombres_especialidad = '';
                                            foreach ($arr_info_detalle as $key => $info_detalle) // Detalle especialidades
                                            {
                                                $nombres_especialidad = $info_detalle['especialidad'];
                                                array_push($array_consultaEsp_total[$cod_esp]["tiempos"], $info_detalle['tiempo_total']);

                                                array_push($array_tiempos_consultaEsp, $info_detalle['tiempo_total']);
                                                array_push($array_tiempos_consulta_totalesEsp, $info_detalle['tiempo_total']);
                                                $hora_inicio = '';
                                                $hora_fin = '';
                                                if($info_detalle['fecha_inicio'] != $info_detalle['fecha_fin'])
                                                {
                                                    $hora_inicio = $info_detalle['fecha_inicio'] . " " . $info_detalle['hora_inicio'];
                                                    $hora_fin = $info_detalle['fecha_fin'] . " " . $info_detalle['hora_fin'];
                                                } else
                                                {
                                                    $hora_inicio = $info_detalle['hora_inicio'];
                                                    $hora_fin = $info_detalle['hora_fin'];
                                                }
                                            }

                                            $array_consultaEsp_total[$cod_esp]['especialidad'] = $nombres_especialidad;
                                            $prom_consultaEspDlle = tiempoPromedio($array_tiempos_consultaEsp);

                                            $tr_especialidad .= '<tr class="' . $css_con . ' find">
                                                                    <td align="left">' . utf8_encode($nombres_especialidad) . '</td>
                                                                    <td align="right">' . count($arr_info_detalle) . '</td>
                                                                    <td align="center">' . $prom_consultaEspDlle . '</td>
                                                                </tr>';

                                            // llenarArrayEspecialidadTotal($array_consultaEsp_total, $cod_esp, $info_detalle['especialidad'], count($arr_info_detalle), $array_tiempos_consultaEsp);

                                            /*$html_consultaEsp_total .= '<tr class="' . $css_conEsp_tot . ' find">
                                                                    <td align="left">' . utf8_encode($info_detalle['especialidad']) . '</td>
                                                                    <td align="center">' . count($arr_info_detalle) . '</td>
                                                                    <td align="center">' . $prom_consultaEspDlle . '</td>
                                                                </tr>';*/
                                        }

                                        $tiempo_maximo_consulta   = 'x';//tiempoMaximo($array_tiempos_consultaEsp);
                                        $tiempo_minimo_consulta   = 'x';//tiempoMinimo($array_tiempos_consultaEsp);
                                        $tiempo_promedio_consulta = 'x';//tiempoPromedio($array_tiempos_consultaEsp);
                                        //Muestra la información de los tiempos máximo, mínimo y promedio
                                        $html_consultaEsp .= '<br> <table id="tab_consultaEsp_'. $fecha_proceso . '" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Especialidad</th>
                                                                    <th>Cantidad</th>
                                                                    <th>Promedio</th>
                                                                </tr>
                                                                '.$tr_especialidad.'
                                                            </table><br>';
                                        // $prom_consultaEsp = tiempoPromedio($array_tiempos_consultaEsp);
                                    }


                                    if(array_key_exists('espera_admisiones', $info_proceso))
                                    {
                                        $array_tiempos_espera_admision = array();
                                        $cant_espera_admision = count($info_proceso['espera_admisiones']['detalles']);
                                        $html_espera_admision .= ' <table id="tab_espera_admision_'. $fecha_proceso . '" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th hidden="true">Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th colspan="2">Tiempo total</th>
                                                                </tr>
                                                                <tr>'
                                                            ;

                                        $cont_esp_adm = 0;
                                        foreach ($info_proceso['espera_admisiones']['detalles'] as $key => $info_detalle) {
                                            array_push($array_tiempos_espera_admision, $info_detalle['tiempo_total']);
                                            array_push($array_tiempos_espera_admision_totales, $info_detalle['tiempo_total']);
                                            $hora_inicio = '';
                                            $hora_fin = '';
                                            $cont_esp_adm++;
                                            $cont_esp_adm_tot++;
                                            $css_esp_adm = ($cont_esp_adm % 2 == 0) ? 'fila1': 'fila2';
                                            $css_esp_adm_tot = ($cont_esp_adm_tot % 2 == 0) ? 'fila1': 'fila2';
                                            if($info_detalle['fecha_inicio'] != $info_detalle['fecha_fin'])
                                            {
                                                $hora_inicio = $info_detalle['fecha_inicio'] . " " . $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['fecha_fin'] . " " . $info_detalle['hora_fin'];
                                            } else
                                            {
                                                $hora_inicio = $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['hora_fin'];
                                            }

                                            $historiaPosible = '';
                                            if($info_detalle['historia_clinica'] == '')
                                            {
                                                $SQLPosibleHistoria = "
                                                SELECT Mtrhis, Mtring
                                                  FROM movhos_000178 AS A INNER JOIN {$wbasedato_cliame}_000100 AS B ON (Atudoc = Pacdoc AND Atutdo = Pactdo) INNER JOIN {$wbasedato_hce}_000022 AS C ON B.Pachis = C.Mtrhis
                                                 WHERE Atutur = '".$info_detalle['turno']."'
												   AND C.Fecha_data = A.Fecha_data
                                                ";
                                                 $RESPosibleHistoria = mysql_query($SQLPosibleHistoria, $conex) or die($SQLPosibleHistoria.' - '.mysql_error());
                                                 if($ROWPosibleHistoria = mysql_fetch_array($RESPosibleHistoria))
                                                {
                                                    $historiaPosible 	=  $ROWPosibleHistoria['Mtrhis'];
                                                    $ingresoPosible 	=  $ROWPosibleHistoria['Mtring'];
                                                }

                                            }

                                            $html_espera_admision .= '<tr class="' . $css_esp_adm . ' find">
                                                                    <td align="center" ppal="'.$info_detalle['procesoPpal'].'">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . (($historiaPosible == '') ? $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] : "<span style='color:red'>".$historiaPosible."-".$ingresoPosible."</span>") . '</td>
                                                                    <td align="center" hidden="true">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center" colspan="2">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';

                                            $html_espera_admision_total .= '<tr class="' . $css_esp_adm_tot . ' find">
                                                                    <td align="center">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center" hidden="true">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center" colspan="2">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';
                                        }

                                        $tiempo_maximo_espera_admision = tiempoMaximo($array_tiempos_espera_admision);
                                        $tiempo_minimo_espera_admision = tiempoMinimo($array_tiempos_espera_admision);
                                        $tiempo_promedio_espera_admision = tiempoPromedio($array_tiempos_espera_admision);
                                        //Muestra la información de los tiempos máximo, mínimo y promedio
                                        $html_espera_admision .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_espera_admision . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_espera_admision . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_espera_admision . '</td>
                                                    </tr>';

                                        $html_espera_admision .= '</table>';
                                        $prom_espera_admision = tiempoPromedio($array_tiempos_espera_admision);
                                    }

                                    if(array_key_exists('espera_triages', $info_proceso))
                                    {
                                        $array_tiempos_espera_triage = array();
                                        $cant_espera_triage = count($info_proceso['espera_triages']['detalles']);
                                        $html_espera_triage .= ' <table id="tab_espera_triage_'. $fecha_proceso . '" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th hidden="true">Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th colspan="2">Tiempo total</th>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center" colspan="6">
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'todos\', \'tab_espera_triage_'. $fecha_proceso . '\')" value="todos" checked="checked"><b>Todos</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'con_turno\', \'tab_espera_triage_'. $fecha_proceso . '\')" value="con_turno"><b>Con Turno</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'sin_turno\', \'tab_espera_triage_'. $fecha_proceso . '\')" value="sin_turno"><b>Sin Turno</b>
                                                                    </td>
                                                                </tr>'
                                                            ;
                                        $cont_esp_tri = 0;
                                        foreach ($info_proceso['espera_triages']['detalles'] as $key => $info_detalle) {
                                            array_push($array_tiempos_espera_triage, $info_detalle['tiempo_total']);
                                            array_push($array_tiempos_espera_triage_totales, $info_detalle['tiempo_total']);
                                            $hora_inicio = '';
                                            $hora_fin = '';
                                            $cont_esp_tri++;
                                            $cont_esp_tri_tot++;
                                            $css_esp_tri = ($cont_esp_tri % 2 == 0) ? 'fila1': 'fila2';
                                            $css_esp_tri_tot = ($cont_esp_tri_tot % 2 == 0) ? 'fila1': 'fila2';
                                            if($info_detalle['fecha_inicio'] != $info_detalle['fecha_fin'])
                                            {
                                                $hora_inicio = $info_detalle['fecha_inicio'] . " " . $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['fecha_fin'] . " " . $info_detalle['hora_fin'];
                                            } else
                                            {
                                                $hora_inicio = $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['hora_fin'];
                                            }
                                            $html_espera_triage .= '<tr class="' . $css_esp_tri . ' find" turno="'.(($info_detalle['turno'] != '') ? 'si' : 'no').'">
                                                                    <td align="center" ppal="'.$info_detalle['procesoPpal'].'">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center" hidden="true">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center" colspan="2">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';

                                            $html_espera_triage_total .= '<tr class="' . $css_esp_tri_tot . ' find">
                                                                    <td align="center">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center" hidden="true">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center" colspan="2">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';
                                        }

                                        $tiempo_maximo_espera_triage = tiempoMaximo($array_tiempos_espera_triage);
                                        $tiempo_minimo_espera_triage = tiempoMinimo($array_tiempos_espera_triage);
                                        $tiempo_promedio_espera_triage = tiempoPromedio($array_tiempos_espera_triage);
                                        //Muestra la información de los tiempos máximo, mínimo y promedio
                                        $html_espera_triage .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_espera_triage . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_espera_triage . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_espera_triage . '</td>
                                                    </tr>';

                                        $html_espera_triage .= '</table>';
                                        $prom_espera_triage = tiempoPromedio($array_tiempos_espera_triage);
                                    }

                                    if(array_key_exists('espera_consultas', $info_proceso))
                                    {
                                        $array_tiempos_espera_consulta = array();
                                        $cant_espera_consulta = count($info_proceso['espera_consultas']['detalles']);
                                        $html_espera_consulta .= ' <table id="tab_espera_consulta_'. $fecha_proceso . '" align="center">
                                                                <tr class="encabezadoTabla encabezadoTabla2">
                                                                    <th>Turno</th>
                                                                    <th>Historia Clínica</th>
                                                                    <th hidden="true">Usuario Matrix</th>
                                                                    <th>Hora Inicio</th>
                                                                    <th>Hora Fin</th>
                                                                    <th colspan="2">Tiempo total</th>
                                                                </tr>
                                                                <tr>
                                                                    <td align="center" colspan="6">
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'todos\', \'tab_espera_consulta_'. $fecha_proceso . '\')" value="todos" checked="checked"><b>Todos</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'con_turno\', \'tab_espera_consulta_'. $fecha_proceso . '\')" value="con_turno"><b>Con Turno</b>&nbsp;&nbsp;&nbsp;&nbsp;
                                                                        <input type="radio" style="display: inline;" name="filtro_turnos" onClick="filtrarTurnos(\'sin_turno\', \'tab_espera_consulta_'. $fecha_proceso . '\')" value="sin_turno"><b>Sin Turno</b>
                                                                    </td>
                                                                </tr>'
                                                            ;
                                        $cont_esp_con = 0;
                                        foreach ($info_proceso['espera_consultas']['detalles'] as $key => $info_detalle) {
                                            array_push($array_tiempos_espera_consulta, $info_detalle['tiempo_total']);
                                            array_push($array_tiempos_espera_consulta_totales, $info_detalle['tiempo_total']);
                                            $hora_inicio = '';
                                            $hora_fin = '';
                                            $cont_esp_con++;
                                            $cont_esp_con_tot++;
                                            $css_esp_con = ($cont_esp_con % 2 == 0) ? 'fila1': 'fila2';
                                            $css_esp_con_tot = ($cont_esp_con_tot % 2 == 0) ? 'fila1': 'fila2';
                                            if($info_detalle['fecha_inicio'] != $info_detalle['fecha_fin'])
                                            {
                                                $hora_inicio = $info_detalle['fecha_inicio'] . " " . $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['fecha_fin'] . " " . $info_detalle['hora_fin'];
                                            } else
                                            {
                                                $hora_inicio = $info_detalle['hora_inicio'];
                                                $hora_fin = $info_detalle['hora_fin'];
                                            }
                                            $html_espera_consulta .= '<tr class="' . $css_esp_con . ' find" turno="'.(($info_detalle['turno'] != '') ? 'si' : 'no').'">
                                                                    <td align="center" ppal="'.$info_detalle['procesoPpal'].'">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center" hidden="true">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center" colspan="2">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';

                                            $html_espera_consulta_total .= '<tr class="' . $css_esp_con_tot . ' find">
                                                                    <td align="center">' . $info_detalle['turno'] . '</td>
                                                                    <td align="center">' . $info_detalle['historia_clinica'] . '-' . $info_detalle['ingreso'] . '</td>
                                                                    <td align="center" hidden="true">' . utf8_encode($info_detalle['usuario_matrix']) . '</td>
                                                                    <td align="center">' . $hora_inicio . '</td>
                                                                    <td align="center">' . $hora_fin . '</td>
                                                                    <td align="center" colspan="2">' . $info_detalle['tiempo_total'] . '</td>
                                                                </tr>';
                                        }

                                        $tiempo_maximo_espera_consulta = tiempoMaximo($array_tiempos_espera_consulta);
                                        $tiempo_minimo_espera_consulta = tiempoMinimo($array_tiempos_espera_consulta);
                                        $tiempo_promedio_espera_consulta = tiempoPromedio($array_tiempos_espera_consulta);
                                        //Muestra la información de los tiempos máximo, mínimo y promedio
                                        $html_espera_consulta .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_espera_consulta . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_espera_consulta . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_espera_consulta . '</td>
                                                    </tr>';

                                        $html_espera_consulta .= '</table>';
                                        $prom_espera_consulta = tiempoPromedio($array_tiempos_espera_consulta);
                                    }

                                    $cont_dias++;
                                    $css_dias = ($cont_dias % 2 == 0) ? 'fila1': 'fila2';
                                    $html_dias .= '
                                        <tr id="tr_dia_' . $fecha_proceso . '" class="' . $css_dias . ' find">
                                            <td>' . str_replace ('_' , '-' , $fecha_proceso) . '</td>
                                            <td align="right" class="td_'.$fecha_proceso.'_espera_adminEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_espera_adminEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_espera_adminEnc\');" onclick="mostrarTablaDetalles(\'espera_admision\', \'' . $fecha_proceso . '\')">' . $cant_espera_admision . '</td>
                                            <td align="center" class="td_'.$fecha_proceso.'_espera_adminEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_espera_adminEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_espera_adminEnc\');" onclick="mostrarTablaDetalles(\'espera_admision\', \'' . $fecha_proceso . '\')">' . $prom_espera_admision . '</td>
                                            <td align="right" class="td_'.$fecha_proceso.'_adminEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_adminEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_adminEnc\');" onclick="mostrarTablaDetalles(\'admision\', \'' . $fecha_proceso . '\')">' . $cant_admision . '</td>
                                            <td align="center" class="td_'.$fecha_proceso.'_adminEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_adminEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_adminEnc\');" onclick="mostrarTablaDetalles(\'admision\', \'' . $fecha_proceso . '\')">' . $prom_admision . '</td>
                                            <td align="right" class="td_'.$fecha_proceso.'_espera_triaEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_espera_triaEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_espera_triaEnc\');" onclick="mostrarTablaDetalles(\'espera_triage\', \'' . $fecha_proceso . '\')">' . $cant_espera_triage . '</td>
                                            <td align="center" class="td_'.$fecha_proceso.'_espera_triaEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_espera_triaEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_espera_triaEnc\');" onclick="mostrarTablaDetalles(\'espera_triage\', \'' . $fecha_proceso . '\')">' . $prom_espera_triage . '</td>
                                            <td align="right" class="td_'.$fecha_proceso.'_triaEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_triaEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_triaEnc\');" onclick="mostrarTablaDetalles(\'triage\', \'' . $fecha_proceso . '\')">' . $cant_triage . '</td>
                                            <td align="center" class="td_'.$fecha_proceso.'_triaEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_triaEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_triaEnc\');" onclick="mostrarTablaDetalles(\'triage\', \'' . $fecha_proceso . '\')">' . $prom_triage . '</td>
                                            <td align="right" class="td_'.$fecha_proceso.'_espera_consEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_espera_consEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_espera_consEnc\');" onclick="mostrarTablaDetalles(\'espera_consulta\', \'' . $fecha_proceso . '\')">' . $cant_espera_consulta . '</td>
                                            <td align="center" class="td_'.$fecha_proceso.'_espera_consEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_espera_consEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_espera_consEnc\');" onclick="mostrarTablaDetalles(\'espera_consulta\', \'' . $fecha_proceso . '\')">' . $prom_espera_consulta . '</td>
                                            <td align="right" class="td_'.$fecha_proceso.'_consEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_consEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_consEnc\');" onclick="mostrarTablaDetalles(\'consulta\', \'' . $fecha_proceso . '\')">' . $cant_consulta . '</td>
                                            <td align="center" class="td_'.$fecha_proceso.'_consEnc" onmouseover="trOver(\'.td_'.$fecha_proceso.'_consEnc\');" onmouseout="trOut(\'.td_'.$fecha_proceso.'_consEnc\');" onclick="mostrarTablaDetalles(\'consulta\', \'' . $fecha_proceso . '\')">' . $prom_consulta . '</td>
                                        </tr>
                                        <tr id="tr_detalle_' . $fecha_proceso . '" hidden="true" style="background-color:#f2f2f2;">
                                            <td></td>
                                            <td id="td_espera_admision_' . $fecha_proceso . '" colspan="2" valign="top"><div hidden="true" id="div_' . $fecha_proceso . '_espera_admision">' . $html_espera_admision . '</div></td>
                                            <td id="td_admision_' . $fecha_proceso . '" colspan="2" valign="top"><div hidden="true" id="div_' . $fecha_proceso . '_admision">' . $html_admision . '</div></td>
                                            <td id="td_espera_triage_' . $fecha_proceso . '" colspan="2" valign="top"><div hidden="true" id="div_' . $fecha_proceso . '_espera_triage">' . $html_espera_triage . '</div></td>
                                            <td id="td_triage_' . $fecha_proceso . '" colspan="2" valign="top"><div hidden="true" id="div_' . $fecha_proceso . '_triage">' . $html_triage . '</div></td>
                                            <td id="td_espera_consulta_' . $fecha_proceso . '" colspan="2" valign="top"><div hidden="true" id="div_' . $fecha_proceso . '_espera_consulta">' . $html_espera_consulta . '</div></td>
                                            <td id="td_consulta_' . $fecha_proceso . '" colspan="2" valign="top"><div hidden="true" id="div_' . $fecha_proceso . '_consulta">' . $html_consulta . $html_consultaEsp . '</div></td>
                                        </tr>
                                        ';
                                }

                                $cant_espera_admision_total = sizeof($array_tiempos_espera_admision_totales);
                                $tiempo_promedio_espera_admision_total = tiempoPromedio($array_tiempos_espera_admision_totales);
                                $cant_admision_total = sizeof($array_tiempos_admision_totales);
                                $tiempo_promedio_admision_total = tiempoPromedio($array_tiempos_admision_totales);
                                $cant_espera_triage_total = sizeof($array_tiempos_espera_triage_totales);
                                $tiempo_promedio_espera_triage_total = tiempoPromedio($array_tiempos_espera_triage_totales);
                                $cant_triage_total = sizeof($array_tiempos_triage_totales);
                                $tiempo_promedio_triage_total = tiempoPromedio($array_tiempos_triage_totales);
                                $cant_espera_consulta_total = sizeof($array_tiempos_espera_consulta_totales);
                                $tiempo_promedio_espera_consulta_total = tiempoPromedio($array_tiempos_espera_consulta_totales);
                                $cant_consulta_total = sizeof($array_tiempos_consulta_totales);
                                $tiempo_promedio_consulta_total = tiempoPromedio($array_tiempos_consulta_totales);

                                $tiempo_maximo_espera_admision_total = tiempoMaximo($array_tiempos_espera_admision_totales);
                                $tiempo_maximo_admision_total = tiempoMaximo($array_tiempos_admision_totales);
                                $tiempo_maximo_espera_triage_total = tiempoMaximo($array_tiempos_espera_triage_totales);
                                $tiempo_maximo_triage_total = tiempoMaximo($array_tiempos_triage_totales);
                                $tiempo_maximo_espera_consulta_total = tiempoMaximo($array_tiempos_espera_consulta_totales);
                                $tiempo_maximo_consulta_total = tiempoMaximo($array_tiempos_consulta_totales);

                                $tiempo_minimo_espera_admision_total = tiempoMinimo($array_tiempos_espera_admision_totales);
                                $tiempo_minimo_admision_total = tiempoMinimo($array_tiempos_admision_totales);
                                $tiempo_minimo_espera_triage_total = tiempoMinimo($array_tiempos_espera_triage_totales);
                                $tiempo_minimo_triage_total = tiempoMinimo($array_tiempos_triage_totales);
                                $tiempo_minimo_espera_consulta_total = tiempoMinimo($array_tiempos_espera_consulta_totales);
                                $tiempo_minimo_consulta_total = tiempoMinimo($array_tiempos_consulta_totales);

                                $html_espera_admision_total .= '<tr id="oe" class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_espera_admision_total . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_espera_admision_total . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_espera_admision_total . '</td>
                                                    </tr>
                                                </table>';

                                $html_admision_total .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_admision_total . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_admision_total . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_admision_total . '</td>
                                                    </tr>
                                                </table>';

                                $html_espera_triage_total .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_espera_triage_total . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_espera_triage_total . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_espera_triage_total . '</td>
                                                    </tr>
                                                </table>';

                                $html_triage_total .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_triage_total . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_triage_total . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_triage_total . '</td>
                                                    </tr>
                                                </table>';

                                $html_espera_consulta_total .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_espera_consulta_total . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_espera_consulta_total . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_espera_consulta_total . '</td>
                                                    </tr>
                                                </table>';

                                $html_consulta_total .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">Tiempo Máximo</td>
                                                        <td style="text-align:center;">' . $tiempo_maximo_consulta_total . '</td>
                                                        <td style="text-align:center;">Tiempo Mínimo</td>
                                                        <td style="text-align:center;">' . $tiempo_minimo_consulta_total . '</td>
                                                        <td style="text-align:center;">Tiempo Promedio</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_consulta_total . '</td>
                                                    </tr>
                                                </table>';

                                // echo print_r($array_consultaEsp_total);
                                foreach ($array_consultaEsp_total as $key => $info_detalle) {

                                    $cont_conEsp_tot++;
                                    $css_conEsp_tot = ($cont_conEsp_tot % 2 == 0) ? 'fila1': 'fila2';
                                    $html_consultaEsp_total .= '<tr class="' . $css_conEsp_tot . ' find">
                                                                    <td align="left">' . utf8_encode($info_detalle['especialidad']) . '</td>
                                                                    <td align="right">' . $info_detalle['cantidad'] . '</td>
                                                                    <td align="center">' . tiempoPromedio($info_detalle['tiempos']) . '</td>
                                                                </tr>';
                                }
                                $html_consultaEsp_total .= '<tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">TOTALES</td>
                                                        <td style="text-align:center;">' . $cant_consulta_total . '</td>
                                                        <td style="text-align:center;">' . $tiempo_promedio_consulta_total . '</td>
                                                    </tr>
                                                </table>';

                                $html_totales = '   <tr class="encabezadoTabla encabezadoTabla2">
                                                        <td style="text-align:center;">TOTALES</td>
                                                        <td class="td_detalles_espera_admision" style="text-align:center;" onmouseover="trOver(\'.td_detalles_espera_admision\');" onmouseout="trOut(\'.td_detalles_espera_admision\');" onclick="mostrarTablaDetalles(\'espera_admision\', \'todos\')">' . $cant_espera_admision_total .'</td>
                                                        <td class="td_detalles_espera_admision" style="text-align:center;" onmouseover="trOver(\'.td_detalles_espera_admision\');" onmouseout="trOut(\'.td_detalles_espera_admision\');" onclick="mostrarTablaDetalles(\'espera_admision\', \'todos\')">' . $tiempo_promedio_espera_admision_total . '</td>
                                                        <td class="td_detalles_admision" style="text-align:center;" onmouseover="trOver(\'.td_detalles_admision\');" onmouseout="trOut(\'.td_detalles_admision\');" onclick="mostrarTablaDetalles(\'admision\', \'todos\')">' . $cant_admision_total . '</td>
                                                        <td class="td_detalles_admision" style="text-align:center;" onmouseover="trOver(\'.td_detalles_admision\');" onmouseout="trOut(\'.td_detalles_admision\');" onclick="mostrarTablaDetalles(\'admision\', \'todos\')">' . $tiempo_promedio_admision_total . '</td>
                                                        <td class="td_detalles_espera_triage" style="text-align:center;" onmouseover="trOver(\'.td_detalles_espera_triage\');" onmouseout="trOut(\'.td_detalles_espera_triage\');" onclick="mostrarTablaDetalles(\'espera_triage\', \'todos\')">' . $cant_espera_triage_total . '</td>
                                                        <td class="td_detalles_espera_triage" style="text-align:center;" onmouseover="trOver(\'.td_detalles_espera_triage\');" onmouseout="trOut(\'.td_detalles_espera_triage\');" onclick="mostrarTablaDetalles(\'espera_triage\', \'todos\')">' . $tiempo_promedio_espera_triage_total . '</td>
                                                        <td class="td_detalles_triage" style="text-align:center;" onmouseover="trOver(\'.td_detalles_triage\');" onmouseout="trOut(\'.td_detalles_triage\');" onclick="mostrarTablaDetalles(\'triage\', \'todos\')">' . $cant_triage_total . '</td>
                                                        <td class="td_detalles_triage" style="text-align:center;" onmouseover="trOver(\'.td_detalles_triage\');" onmouseout="trOut(\'.td_detalles_triage\');" onclick="mostrarTablaDetalles(\'triage\', \'todos\')">' . $tiempo_promedio_triage_total . '</td>
                                                        <td class="td_detalles_espera_consulta" style="text-align:center;" onmouseover="trOver(\'.td_detalles_espera_consulta\');" onmouseout="trOut(\'.td_detalles_espera_consulta\');" onclick="mostrarTablaDetalles(\'espera_consulta\', \'todos\')">' . $cant_espera_consulta_total . '</td>
                                                        <td class="td_detalles_espera_consulta" style="text-align:center;" onmouseover="trOver(\'.td_detalles_espera_consulta\');" onmouseout="trOut(\'.td_detalles_espera_consulta\');" onclick="mostrarTablaDetalles(\'espera_consulta\', \'todos\')">' . $tiempo_promedio_espera_consulta_total . '</td>
                                                        <td class="td_detalles_consulta" style="text-align:center;" onmouseover="trOver(\'.td_detalles_consulta\');" onmouseout="trOut(\'.td_detalles_consulta\');" onclick="mostrarTablaDetalles(\'consulta\', \'todos\')">' . $cant_consulta_total . '</td>
                                                        <td class="td_detalles_consulta" style="text-align:center;" onmouseover="trOver(\'.td_detalles_consulta\');" onmouseout="trOut(\'.td_detalles_consulta\');" onclick="mostrarTablaDetalles(\'consulta\', \'todos\')">' . $tiempo_promedio_consulta_total . '</td>
                                                    </tr>
                                                    <tr id="tr_detalle_todos" hidden="true" style="background-color:#f2f2f2;">
                                                        <td></td>
                                                        <td id="td_espera_admision_todos" colspan="2" valign="top"><div hidden="true" id="div_todos_espera_admision">' . $html_espera_admision_total . '</div></td>
                                                        <td id="td_admision_todos" colspan="2" valign="top"><div hidden="true" id="div_todos_admision">' . $html_admision_total . '</div></td>
                                                        <td id="td_espera_triage_todos" colspan="2" valign="top"><div hidden="true" id="div_todos_espera_triage">' . $html_espera_triage_total . '</div></td>
                                                        <td id="td_triage_todos" colspan="2" valign="top"><div hidden="true" id="div_todos_triage">' . $html_triage_total . '</div></td>
                                                        <td id="td_espera_consulta_todos" colspan="2" valign="top"><div hidden="true" id="div_todos_espera_consulta">' . $html_espera_consulta_total . '</div></td>
                                                        <td id="td_consulta_todos" colspan="2" valign="top"><div hidden="true" id="div_todos_consulta">' . $html_consulta_total . '<br>' . $html_consultaEsp_total . '</div></td>
                                                    </tr>
                                                ';
                            }
                            else
                            {
                                $html = '  <tr class="fila1 find">
                                                <td colspan="13" style="text-align:center;">NO SE ENCONTRARON DATOS!</td>
                                            </tr>';
                                 $html_dias = "";
                                 $html_totales = "";
                            }

                        $data["html"] = $html . $html_dias . $html_totales;
                        $data["sql"] = $sql_tiempos_espera_admisiones . ' - ' .
                                         $sql_tiempos_admisiones . ' - ' .
                                         $sql_tiempos_triages . ' - ' .
                                         $sql_tiempos_espera_triages . ' - ' .
                                         $sql_tiempos_consultas . ' - ' .
                                         $sql_tiempos_espera_consultas;
                    break;

                    default:
                        $data['mensaje'] = $no_exec_sub;
                        $data['error'] = 1;
                        break;
                }
                echo json_encode($data);
                break;

            default : break;
        }
        return;
    }

    /*
    Parametros iniciales
    */
    $wemp_pmla = (!isset($wemp_pmla)) ? "": $wemp_pmla;
    include_once("root/comun.php");
    $wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $wbasedato_cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
    $wbasedato_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
?>

<html lang="es-ES">
<head>
    <title>REPORTE DE TIEMPOS URGENCIAS</title>
    <meta charset = "utf-8">

    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <!-- Librería para detectar compatibilidad HTML5 con varios navegadores -->
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>

    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>

    <script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
    <link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

    <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

    <script type="text/javascript">

    $(document).ready( function (){

        initSearch();

        /*$("#fecha_inicio_rep").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
                maxDate:"+0D",
                onSelect: function (date) {
                    var dt1 = $('#fecha_inicio_rep').datepicker('getDate');
                    var dt2 = $('#fecha_final_rep').datepicker('getDate');
                    if (dt1 > dt2) {
                        $('#fecha_final_rep').datepicker('setDate', dt1);
                    }
                    $('#fecha_final_rep').datepicker('option', 'minDate', dt1);
                }
            });

            $("#fecha_final_rep").datepicker({
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonImageOnly: true,
                maxDate:"+0D",
                onClose: function () {
                    var dt1 = $('#fecha_inicio_rep').datepicker('getDate');
                    var dt2 = $('#fecha_final_rep').datepicker('getDate');
                    //check to prevent a user from entering a date below date of dt1
                    if (dt2 <= dt1) {
                        var minDate = $('#fecha_final_rep').datepicker('option', 'minDate');
                        $('#fecha_final_rep').datepicker('setDate', minDate);
                    }
                }
            });*/

        $("#fecha_inicio_rep").datepicker({
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage: "../../images/medical/root/calendar.gif",
            buttonImageOnly: true,
            maxDate:"+0D",
            buttonText:"Fecha inicial de la consulta",
            onSelect: function (date) {
                    var dt1 = $('#fecha_inicio_rep').datepicker('getDate');
                    var dt2 = $('#fecha_final_rep').datepicker('getDate');
                    if (dt1 > dt2) {
                        $('#fecha_final_rep').datepicker('setDate', dt1);
                    }
                    $('#fecha_final_rep').datepicker('option', 'minDate', dt1);
                }
        });

        $("#fecha_final_rep").datepicker({
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage: "../../images/medical/root/calendar.gif",
            buttonImageOnly: true,
            maxDate:"+0D",
            buttonText:"Fecha final de la consulta",
            onClose: function () {
                    var dt1 = $('#fecha_inicio_rep').datepicker('getDate');
                    var dt2 = $('#fecha_final_rep').datepicker('getDate');
                    //check to prevent a user from entering a date below date of dt1
                    if (dt2 <= dt1) {
                        var minDate = $('#fecha_final_rep').datepicker('option', 'minDate');
                        $('#fecha_final_rep').datepicker('setDate', minDate);
                    }
                }
        });

        $('input:radio[value=admision]').change(function () {
            $('input:radio[value=por_especialidad]:checked').removeAttr("checked");
            $("#div_especialidad").hide();
            $("#select_especialidad").val("");
            $("#select_especialidad").hide();
            if($('input:radio[name=radio_promedio]:checked').val() == "" || $('input:radio[name=radio_promedio]:checked').val() == '' || $('input:radio[name=radio_promedio]:checked').val() == null) {
                $("#filtro_especifico").hide();
            }

            var proceso ='admision';
            var promedio = $('input:radio[name=radio_promedio]:checked').val();
            consultarUsuarioEspecialidad(proceso, promedio);
        });

        $('input:radio[value=triage]').change(function () {
            $('input:radio[value=por_especialidad]:checked').removeAttr("checked");
            $("#div_especialidad").hide();
            $("#select_especialidad").val("");
            $("#select_especialidad").hide();
            if($('input:radio[name=radio_promedio]:checked').val() == "" || $('input:radio[name=radio_promedio]:checked').val() == '' || $('input:radio[name=radio_promedio]:checked').val() == null) {
                $("#filtro_especifico").hide();
            }

            var proceso ='triage';
            var promedio = $('input:radio[name=radio_promedio]:checked').val();
            consultarUsuarioEspecialidad(proceso, promedio);
        });

        $('input:radio[value=consulta]').change(function () {
            $("#div_especialidad").show();

            var proceso ='consulta';
            var promedio = $('input:radio[name=radio_promedio]:checked').val();
            consultarUsuarioEspecialidad(proceso, promedio);
        });

        $('input:radio[value=por_usuario]').change(function () {
            $("#filtro_especifico").show();
            $("#select_usuarios").show();
            $("#select_especialidad").val("");
            $("#select_especialidad").hide();

            var proceso = $('input:radio[name=radio_proceso]:checked').val();
            var promedio = 'por_usuario';
            consultarUsuarioEspecialidad(proceso, promedio);
        });

        $('input:radio[value=por_especialidad]').change(function () {
            $("#filtro_especifico").show();
            $("#select_especialidad").show();
            $("#select_usuarios").val("");
            $("#select_usuarios").hide();

            var proceso = $('input:radio[name=radio_proceso]:checked').val();
            var promedio = 'por_especialidad';
            consultarUsuarioEspecialidad(proceso, promedio);
        });

        $('input:radio[value=total]').change(function () {
            $("#filtro_especifico").hide();
            $("#select_especialidad").val("");
            $("#select_usuarios").val("");
        });

    });

    function trOver(grupo)
    {
        // $("#"+grupo.id).addClass('classOver');
        $(grupo).addClass('classOver');
    }

    function trOut(grupo)
    {
        // $("#"+grupo.id).removeClass('classOver');
        $(grupo).removeClass('classOver');
    }

    /**
     * Consulta los usuarios o las especialidades.
     *
     * @author Eimer Castro
     * @param {string} proceso Indica el proceso al cual se le desea consultar el usuario o la especialidad(Sólo proceso de consulta)
     * @param {string} promedio Indica el promedio al cual se le desea consultar el usuario o la especialidad(Sólo proceso de consulta)
    */
    function consultarUsuarioEspecialidad(proceso, promedio)
    {
        if(proceso == undefined || proceso == null)
        {
            proceso = '';
        }

        if(promedio == undefined || promedio == null)
        {
            promedio = '';
        }

        console.log("proceso: "+proceso+", promedio: "+promedio);

        if(proceso != '' && promedio != '')
        {
            var objson         = parametrosComunes();
            objson['accion']   = 'load';
            objson['form']     = 'cargar_selects_usuarios_especialidades';
            objson['proceso']  = proceso;
            objson['promedio'] = promedio;
            $.post("rep_tiempos_urgencias.php",
                    objson,
                function(data){
                    if(data.error == 1)
                    {
                        jAlert(data.mensaje, "Alerta");
                    }
                    else
                    {
                        if(promedio == "por_usuario")
                        {
                            $("#select_usuarios").html(data.opciones);
                        }
                        else if(promedio == "por_especialidad")
                        {
                            $("#select_especialidad").html(data.opciones);
                        }
                    }
                },
                "json"
                ).done(function() {
                    initSearch();
            });
        }
    }

    /**
     * Carga los párametros iniciales para usar en los objetos JSON de los métodos post.
     *
     * @author Eimer Castro
     * @param {json} Objeto JSON
    */
    function parametrosComunes()
    {
        var objson              = {};
        objson['consultaAjax']  = '';
        objson['wemp_pmla']     = $("#wemp_pmla").val();
        objson['wbasedato']     = $("#wbasedato").val();
        return objson;
    }

    /**
     * Cierra la ventana en la cual se abre el reporte.
     *
     * @author Eimer Castro
    */
    function cerrarVentanaPpal()
    {
        window.close();
    }

    /**
     * Permite buscar dentro de la tabla del reporte un string en particular.
    */
    function initSearch()
    {
        $('#id_search_rep_tiempos_urg').quicksearch('#tablaTiempos .find');
    }

    /**
     * Realiza el llamado por el método POST para cargar los datos del reporte
     * en una tabla con la siguiente información:
     * Fecha, Usuario, Proceso(Admisión, Triage, Consulta)
     * Especialidad(Sólo para el proceso Consulta), Hora incio, Hora Fin
     * Tiempo Total, Tiempo Máximo y Tiempo Mínimo y Promedio de Tiempos
     *
     * @author Eimer Castro
     */
    function filtrarPorFechas() {

        var objson                      = parametrosComunes();
        objson['accion']                = 'load';
        objson['form']                  = 'cargar_procesos_fecha';
        objson['fecha_inicio']          = $("#fecha_inicio_rep").val();
        objson['fecha_final']           = $("#fecha_final_rep").val();
        objson['clasificacion_atencion']= $("#select_clasificacion").val();

        if($("#fecha_inicio_rep").val() <= $("#fecha_final_rep").val())
        {
            $("#div_tablaTiempos").hide();
            //$("#div_reporte").hide();
            $("#tablaTiempos").html("");

            $("#btn_filtrar_fechas").attr("disabled", "disabled");
            $("#btn_filtrar_fechas").html("Consultando... <img style='cursor:pointer;' width='20' height='20' src='../../images/medical/ajax-loader11.gif'>");
            $("#resConsulta").html("<h3><b>Espere un momento por favor... </b></h3><center><img style='cursor:pointer;' src='../../images/medical/ajax-loader11.gif'></center>");

            $.post("rep_tiempos_urgencias.php",
            objson,
            function(data){
                if(data.error == 1)
                {
                    jAlert(data.mensaje, "Alerta");
                }
                else
                {
                    $("#div_tablaTiempos").find(".find").remove();
                    $("#div_tablaTiempos").show();
                    //$("#div_reporte").show();
                    $("#tablaTiempos").html(data.html);

                    $("#resConsulta").html(data);
                    $("#btn_filtrar_fechas").removeAttr("disabled");
                    $("#btn_filtrar_fechas").text("Consultar");
                }
            },
                "json"
                ).done(function() {
                    initSearch();
            });
        }
        else
        {
            jAlert("La fecha de inicio debe ser menor o igual a la fecha final.", "Alerta");
        }

        function desplegar(fecha)
        {
            $("."+fecha).toggle();
            var imagen = $("#img"+fecha).attr("src");
            if(imagen == "../../images/medical/hce/mas.PNG")
                $("#img"+fecha).attr("src", "../../images/medical/hce/menos.PNG");
            else
                $("#img"+fecha).attr("src", "../../images/medical/hce/mas.PNG");
        }
    }

    function mostrarTablaDetalles(proceso, fecha_proceso)
    {
        if(!$('#tr_detalle_' + fecha_proceso).is(':visible'))
        {
            $('#tr_detalle_' + fecha_proceso).show();
            $('#div_' + fecha_proceso + '_' + proceso).show();
        }
        else
        {
            if(!$('#div_' + fecha_proceso + '_' + proceso).is(':visible'))
            {
                $('#div_' + fecha_proceso + '_' + proceso).show();
            }
            else
            {
                $('#div_' + fecha_proceso + '_' + proceso).hide();
            }
        }
        if($('div[id^=div_' + fecha_proceso + ']:visible').length == 0)
        {
            $('#tr_detalle_' + fecha_proceso).hide();
        }
    }

    $.datepicker.regional['esp'] = {
        closeText: 'Cerrar',
        prevText: 'Antes',
        nextText: 'Despues',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        yearSuffix: '',
        changeYear: true,
        changeMonth: true,
        yearRange: '-100:+0'
    };
    $.datepicker.setDefaults($.datepicker.regional['esp']);

    function filtrarTurnos(tipoTurno, id_tabla, elemento)
    {
        switch(tipoTurno)
        {
            case 'sin_turno':
            {
                $("#" + id_tabla).find("tr[turno=no]").show();
                $("#" + id_tabla).find("tr[turno=si]").hide();
                cantidad = $("#" + id_tabla).find("tr[turno=no]").length;
                break;
            }
            case 'con_turno':
            {
                $("#" + id_tabla).find("tr[turno=no]").hide();
                $("#" + id_tabla).find("tr[turno=si]").show();
                cantidad = $("#" + id_tabla).find("tr[turno=si]").length;
                break;
            }
            case 'todos':
            {
                $("#" + id_tabla).find("tr[turno=no]").show();
                $("#" + id_tabla).find("tr[turno=si]").show();
                 cantidad = $("#" + id_tabla).find("tr[turno]").length;
                break;
            }
        }
        console.log(cantidad);

        $(elemento).attr("title", cantidad);
    }

    </script>

    <style type="text/css">
    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }

    .placeholder
    {
      color: #aaa;
  }

  .encTabla{
    text-align: center;
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
        width: 200px; /*must have*/
        height: 200px; /*must have*/
    }

    .classOver{
        background-color: #CCCCCC;
        color: black;
    }
    A   {text-decoration: none;color: #000066;}
    .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px }
    .tipo3V:hover {color: #000066; background: #999999;}

    .brdtop {
        border-top-style: solid; border-top-width: 2px;
        border-color: #2A5BD0;
    }
    .brdleft{
        border-left-style: solid; border-left-width: 2px;
        border-color: #2A5BD0;
    }
    .brdright{
        border-right-style: solid; border-right-width: 2px;
        border-color: #2A5BD0;
    }
    .brdbottom{
        border-bottom-style: solid; border-bottom-width: 2px;
        border-color: #2A5BD0;
    }

    .alto{
        height: 140px;
    }

    .vr
    {
        display:inline;
        height:50px;
        width:1px;
        border:1px inset;
        /*margin:5px*/
        border-color: #2A5BD0;
    }

    .bgGris1{
        background-color:#F6F6F6;
    }

    .tbold{
        font-weight:bold;
        text-align:left;
    }
    .alng{
        text-align:left;
    }
    .img_fondo{
        background: url('../../images/medical/tal_huma/fondo.png');
        background-repeat: no-repeat;
    }
    .disminuir{
        font-size:11pt;
    }
    .imagen { width: 250px; height: auto;}
    .btnActivo { background-color: #0033ff; }
    .padding_info{
        padding-bottom: 4px;
    }
    .border_ppal{
        border: 2px solid #2A5DB0;
    }
    .txt1{
        /*color:#2A5DB0;*/
        font-weight:bold;
    }
    .fondoEncabezado{
        background-color: #2A5DB0;
        color: #FFFFFF;
        font-size: 10pt;
        font-weight: bold;
    }

    .campoRequerido{
        border: 1px orange solid;
        background-color:lightyellow;
    }

    .st_boton{
            /*font-size:10px;
            font-family:Verdana,Helvetica;
            font-weight:bold;
            color:white;
            background:#638cb5;
            border:0px;
            width:80px;
            height:19px;*/

            background-color: #4D90FE;
            background-image: -webkit-gradient(linear,left top,left bottom,from(#4D90FE),to(#4787ED));
            background-image: -moz-linear-gradient(top,#4D90FE,#4787ED);
            background-image: -ms-linear-gradient(top,#4D90FE,#4787ED);
            background-image: -o-linear-gradient(top,#4D90FE,#4787ED);
            background-image: -webkit-linear-gradient(top,#4D90FE,#4787ED);
            background-image: linear-gradient(top,#4D90FE,#4787ED);
            filter: progid:DXImageTransform.Microsoft.gradient
            (startColorStr='#4d90fe',EndColorStr='#4787ed');
            border: 1px solid #3079ED;
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            -moz-user-select: none;
            -webkit-user-select: none;
            color: white;
            display: inline-block;
            font-weight: bold;
            height: 25px;
            line-height: 20px;
            text-align: center;
            text-decoration: none;
            padding: 0 8px;
            margin: 0px auto;
            font: 13px/27px Arial,sans-serif;
            cursor:pointer;
        }

        .parrafo1{
            color: #333333;
            background-color: #cccccc;
            font-family: verdana;
            font-weight: bold;
            font-size: 10pt;
            text-align: left;
        }
        .no_save{
            border: red 1px solid;
        }
        .mayuscula{
            text-transform: uppercase;
        }

        #tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        /*#tooltip{
            color: #FE2E2E;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
            }*/
            #tooltip h3, #tooltip div{
                margin:0; width:auto
            }

            #tooltip_pro{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}

        /*#tooltip{
            color: #FE2E2E;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;
            }*/
            #tooltip_pro h3, #tooltip_pro div{
                margin:0; width:auto
            }

            .error{
                font-weight: bold;
                color: red;
            }
            .correct{
                font-weight: bold;
                color: green;
            }
            .endlog{
                font-weight: bold;
                color: orange;
            }

            #caja_flotante{
                position: absolute;
                /*top:0;*/
                /*left: 10px;*/
                border: 1px solid #CCC;
                background-color: #F2F2F2;
                /*width:150px;*/
            }

            .caja_flotante{
                position: absolute;
                /*top:0;*/
                /*left: 10px;*/
                border: 1px solid #CCC;
                background-color: #F2F2F2;
                /*width:150px;*/
            }

            /* TABS */
            ul.pestania {
                border-bottom: 1px solid #E5E5E5;
                float: left;
                font-size: 0;
                margin: 10px 0 -1px;
                padding: 0;
                width: 100%;
            }
            ul.pestania.left {
                text-align: left;
            }
            ul.pestania.center {
                text-align: center;
            }
            ul.pestania.right {
                text-align: right;
            }
            ul.pestania.right li {
                margin: 0 0 0 -2px;
            }
            ul.pestania li {
                display: inline-block;
                font-size: 14px;
                left: 0;
                list-style-type: none;
                margin: 0 -2px 0 0;
                padding: 0;
                position: relative;
                top: 0;
            }
            ul.pestania li a {
                -moz-border-bottom-colors: none;
                -moz-border-left-colors: none;
                -moz-border-right-colors: none;
                -moz-border-top-colors: none;
                background: none repeat scroll 0 0 #F5F5F5;
                border-color: #E5E5E5 #E5E5E5 -moz-use-text-color;
                border-image: none;
                border-style: solid solid none;
                border-width: 1px 1px 0;
                box-shadow: 0 -3px 3px rgba(0, 0, 0, 0.03) inset;
                color: #666666;
                display: inline-block;
                font-size: 0.9em;
                left: 0;
                line-height: 100%;
                padding: 9px 15px;
                position: relative;
                text-decoration: none;
                top: 0;
            }
            ul.pestania li a:hover {
                background: none repeat scroll 0 0 #FFFFFF;
            }
            ul.pestania li.current a {
                background: none repeat scroll 0 0 #FFFFFF;
                box-shadow: none;
                color: #222222;
                left: 0;
                position: relative;
                top: 1px;
            }

            .tab-content {
                background: none repeat scroll 0 0 #FFFFFF;
                border: 1px solid #E5E5E5;
                clear: both;
                margin: 0 0 3px;
                padding: 3px;
            /*margin: 0 0 40px;
            padding: 20px;*/
        }
        /* TABS */

        .ui-autocomplete{
            max-width:  230px;
            max-height: 150px;
            overflow-y: auto;
            overflow-x: hidden;
            font-size:  9pt;
        }

        /* NOTIFICACIÓN */
        #notificacion {
            background-color: #F2F2F2;
            background-repeat: no-repeat;
            font-family: Helvetica;
            font-size: 20px;
            line-height: 30px;
            position: absolute;
            text-align: center;
            width: 30%;
            left: 35%;
            top: -30px;
        }
        .chat {
            background-image: url("../../images/medical/root/info.png");
        }

        /*.notificar {
            background-color: #59AADA;
            border-radius: 6px;
            border: 1px solid #60B4E5;
            color: #FFFFFF;
            display: block;
            font-size: 30px;
            font-weight: bold;
            letter-spacing: -2px;
            margin: 60px auto;
            padding: 20px;
            text-align: center;
            text-shadow: 1px 1px 0 #145982;
            width: 350px;
            cursor: pointer;
            }*/

        /*.notificar:hover {
            background-color: #4a94bf;
            }*/
            /* NOTIFICACIÓN */

            .fixed-dialog{
             position: fixed;
             top: 100px;
             left: 100px;
         }

         .ui-dialog
         {
            background: #FFFEEB;
        }

        .texto_add{
            font-size: 8pt;
        }

        .submit{
            text-align: center;
            background: #C3D9FF;
        }
        .pad{
            padding:    4px;
        }

        .margen-superior-eventos{
            margin-top:15px;
            border:2px #2A5DB0 solid;
        }

        .datos-adds-eventos{
            text-align:left; border: 1px solid #cccccc;
        }

        ul{
            margin:0;
            padding:0;
            list-style-type:none;
        }

        table[id^='tabla_lista_cxs_'] td {
            font-size: 8.5pt;
        }

        .alinear_derecha {
            display: block;
            float:right;
            width: 70px;
            text-align: center;
            /*color: #FF2F00;*/
        }

        .div_alinear{
            margin-left: 10px;
        }

        .td_noTarifa{
            background-color: #ffffcc;
        }
        .titulopagina2
        {
            border-bottom-width: 1px;
            border-color: <?=$bordemenu?>;
            border-left-width: 1px;
            border-top-width: 1px;
            font-family: verdana;
            font-size: 18pt;
            font-weight: bold;
            height: 30px;
            margin: 2pt;
            overflow: hidden;
            text-transform: uppercase;
        }


        .classOver{
            background-color: #ffffcc;
            cursor: pointer;
        }

        </style>

    </head>

    <body>
        <input type="hidden" id="wemp_pmla" value="<?=$wemp_pmla?>" >
        <input type='hidden' id="wbasedato" value="<?=$wbasedato?>" name='wbasedato' >
        <?php
        encabezado("<div class='titulopagina2'>Promedios Tiempos en Urgencias</div>", $wactualiza, "clinica");
        ?>

        <br />
        <table align="center" style="width:95%;">
            <tr>
                <td style="text-align:left;">
                    <div id="contenedor_programa_reporte" align="left">
                        <div id="div_filtros" style="width:100%;" align="center">
                            <table id="tabla_filtros" align="center" >
                                <tr>
                                    <td colspan="4" class="encabezadoTabla" style="text-align:center;">Filtros del reporte</td>
                                </tr>
                                <tr class="tooltip" title="Fechas de los procesos en urgencias">
                                    <td class="encabezadoTabla">Fecha inicial de la admisión</td>
                                    <td class="fila2"><input type="text" class="datoreq" id="fecha_inicio_rep" name="fecha_inicio_rep" value="<?=date("Y-m-d")?>" size="8" disabled="true" style="display: inline;"></td>
                                    <td class="encabezadoTabla">Fecha final de la admisión</td>
                                    <td class="fila2"><input type="text" class="datoreq" id="fecha_final_rep" name="fecha_final_rep" value="<?=date("Y-m-d")?>" size="8" disabled="true" style="display: inline;"></td>
                                </tr>
                                <tr>
                                    <td class="encabezadoTabla" colspan="2">Clasificación de la atención</td>
                                    <td colspan="2">
                                        <select id="select_clasificacion">
                                        <?php
                                            $opciones = '<option value="00">Todos</option>';
                                            //Consulta para traer los tipos de atención de los pacientes
                                            $sql_tipos_pacientes = "SELECT * FROM {$wbasedato_cliame}_000246";
                                            $result_tipos_pacientes = mysql_query($sql_tipos_pacientes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql_tipos_pacientes . " - " . mysql_error());
                                            $num_tipos_pacientes = mysql_num_rows($result_tipos_pacientes);
                                            if ($num_tipos_pacientes > 0 )
                                            {
                                                for ($i=0; $i<$num_tipos_pacientes; $i++)
                                                {
                                                    $row = mysql_fetch_array($result_tipos_pacientes);
                                                    $opciones .= '<option value="'.$row['3'].'">'.$row['3'].' - '.$row['4'].'</option>';
                                                }
                                            }
                                            echo $opciones;
                                        ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="encabezadoTabla" align="center"><input id="btn_filtrar_fechas" type="button" onclick="filtrarPorFechas();" value="Consultar" name="btn_filtrar_fechas" ></td>
                                </tr>
                            </table>
                        </div>
                        <!-- <div id="div_reporte" style="width:100%;" align="center" hidden="true">
                            <table id="tabla_contenedor_rep">
                                <tr>
                                    <td id="td_contenedor_rep">
                                        <table id="tabla_resultado_reporte" align="center">
                                            <tr class="encabezadoTabla">
                                                <td>
                                                    USE LOS FILTROS DEL REPORTE PARA CONSULTAR
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr class="encabezadoTabla" align="center">
                                    <td>Buscar: <input id="id_search_rep_tiempos_urg" type="text" value="" name="id_search_rep_tiempos_urg" ></td>
                                </tr>
                            </table>
                        </div> -->
                    </div>
                </td>
            </tr>
        </table>
        <br />
        <div style="" id="div_tablaTiempos" hidden="true">
            <table align="center" style="width:95%;" id="tablaTiempos">
            </table>
        </div>
        <br />
        <br />
        <table align='center'>
            <tr><td><div id='resConsulta'></div></td></tr>
            <tr><td align="center" colspan="9"><input type="button" value="Cerrar Ventana" onclick="cerrarVentanaPpal();"></td></tr>
        </table>
        <br />
        <br />
    </body>
</html>