<?php
    //=========================================================================================================================================\\
    //       	WEBSERVICE PARA TURNEROS PAP, ENDOSCOPIA
    //=========================================================================================================================================\\
    //DESCRIPCION:  Minimo 3 parametros:
    //              wemp_pmla, tema, FUNCION: listarTurnos o listarAlertas, solucionCitas, tipoTur: ESTANDAR o ENDOSCOPIA o URGENCIAS
    // 
    //                      
    //AUTOR:				    TAITO
    //FECHA DE CREACION:	2022-06-04
    //2022-03-02 - Carlos Lora - Se agrega el cierre de la conexion a la base de datos.
    include_once("conex.php");
    include("root/comun.php");
    include_once("citas/funcionesAgendaCitas.php");
    $conexcar;
    ob_end_clean();
    if ($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        $wemp_pmla = $_GET['wemp_pmla'];
        $value = "0".$_GET['tema'];
        $tema = substr($value,-2,2);
        $funcion = $_GET['funcion'];
        $solucionCitas = $_GET['solucionCitas'];
        $tipoTurnero = $_GET['tipoTur'];
        $conexcar = obtenerConexionBD("matrix");
        switch($tipoTurnero)
        {
            case 'ESTANDAR':          // SALA DE ATENCION, PAP
                if ($funcion == 'listaTurnos')
                {        
                    $objTurnos = new listarTurnos($wemp_pmla, $tema);
                    header("HTTP/1.1 200 OK");
                    // echo Requerido para devolver el resultado
                    echo json_encode($objTurnos->arrFilasTur);
                }
                else
                {
                    $objAlertas = new listarAlertas($wemp_pmla, $tema);
                    header("HTTP/1.1 200 OK");
                    echo json_encode($objAlertas->arrAlerta);
                }
                liberarConexionBD($conexcar);
                exit();
            case 'ENDOSCOPIA':      //ENDOSCOPIA
                // ENDOSCOPIA RECIBE UN PARAMETRO ADICIONAL solucionCitas
                if ($funcion == 'listaTurnos')
                {        
                    $objTurnosEn = new listarTurnosEn($wemp_pmla, $tema, $solucionCitas);
                    header("HTTP/1.1 200 OK");
                    echo json_encode($objTurnosEn->arrFilasTur);
                }
                else
                {
                    $objAlertas = new listarAlertasEN($wemp_pmla, $tema, $solucionCitas);
                    header("HTTP/1.1 200 OK");
                    echo json_encode($objAlertas->arrAlerta);
                }
                liberarConexionBD($conexcar);
                exit();
            case 'URGENCIAS':      // URGENCIAS 
                if ($funcion == 'listaTurnos')
                {
                    $objTurnos = new listarTurnosUr($wemp_pmla, $tema);
                    header("HTTP/1.1 200 OK");
                    echo json_encode($objTurnos->arrFilasTur);
                }
                else
                {
                    $objAlertas = new listarAlertasUR($wemp_pmla, $tema);
                    header("HTTP/1.1 200 OK");
                    echo json_encode($objAlertas->arrAlerta);
                }
                liberarConexionBD($conexcar);
                exit();
        }
    } 
    // En caso de que ninguna de las opciones anteriores se haya ejecutado
    header("HTTP/1.1 400 Bad Request"); 
    exit();
    class turno
    {
        public $Turno;
        public $Modulo;
        public $Prioridad;
        public $Estado;       
        public $Sala; 
        public $Ubicacion;               
    }
    class alertas
    {
        public $turnoAlerta;
        public $moduloAlerta;
        public $prioridadAlerta;
        public $Estado;
    }

    // LISTAR ALERTAS PARA URGENCIAS
    class listarAlertasUR
    {
        public $wemp_pmla;
        public $tema;
        public $arrAlerta = Array();
        public function __construct ($wemp_pmla, $tema)
        {
            // --> 	Consultar turnos de maximo 24 horas atras.
            // 		1 SELECT: Alerta de llamado para el triage
            // 		2 SELECT: Alerta de rellamado para el triage
            // 		3 SELECT: Alerta de llamado para la admisión
            // 		4 SELECT: Alerta de llamado para la consulta	
            $wbasedato = consultarAliasPorAplicacion($conexcar, $wemp_pmla, 'movhos');
            $sqlAlertas = "
            SELECT A.Fecha_data, A.Hora_data, Atutur, Puenom
              FROM ".$wbasedato."_000178 AS A, ".$wbasedato."_000180
             WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
               AND Atuest = 'on'
               AND Atullt = 'on'
               AND Atutem = '".$tema."'
           "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
               AND Atuctl = Puecod
             UNION
            SELECT A.Fecha_data, A.Hora_data, Atutur, Puenom
              FROM ".$wbasedato."_000178 AS A, ".$wbasedato."_000180
             WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
               AND Atuest = 'on'
               AND Atuart = 'on'
               AND Atutem = '".$tema."'
           "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
               AND Atuctl = Puecod
             UNION
            SELECT A.Fecha_data, A.Hora_data, Atutur, Puenom
              FROM ".$wbasedato."_000178 AS A, ".$wbasedato."_000180
             WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
               AND Atuest = 'on'
               AND Atullv = 'on'
               AND Atutem = '".$tema."'
           "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
               AND Atuven = Puecod
             UNION
            SELECT A.Fecha_data, A.Hora_data, Atutur, Puenom
              FROM ".$wbasedato."_000178 AS A, ".$wbasedato."_000180
             WHERE A.Fecha_data >= '".date('Y-m-d',strtotime('-1 day'))."'
               AND Atuest = 'on'
               AND Atullc = 'on'
               AND Atutem = '".$tema."'
           "./*AND (Atusea = '".$monitorSala."' OR Atusea = '*')*/"
               AND Atucon = Puecod"; 
            // echo($sqlAlertas);
            $resAlertas = mysql_query($sqlAlertas, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlAlertas):</b><br>".mysql_error());
            while($rowAlertas = mysql_fetch_array($resAlertas))
            {
                $objAlerta = new alertas();
                $objAlerta->turnoAlerta = "";
                $objAlerta->moduloAlerta = "";
                $objAlerta->prioridadAlerta = "";
                $objAlerta->Estado = "";
                // --> Solo mostrar turnos de maximo 6 horas atras 
                if(strtotime($rowAlertas['Fecha_data']." ".$rowAlertas['Hora_data']) < strtotime('-6 hours'))
                continue;   
                $objAlerta->turnoAlerta = substr($rowAlertas['Atutur'], 7);
                $objAlerta->moduloAlerta = $rowAlertas['Puenom'];
                $this->arrAlerta[]= $objAlerta;
            }
        }

    }

    // LISTAR TURNOS PARA URGENCIAS
    class listarTurnosUr
    {
        public $wemp_pmla;
        public $tema;
        public $arrFilasTur = Array();
        public function __construct ($wemp_pmla, $tema)
        {
            // AND (Atusea = '".$monitorSala."' OR Atusea = '*') REVISAR ESTO EN LA CONDICION ORIGINAL
            $wbasedato = consultarAliasPorAplicacion($conexcar, $wemp_pmla, 'movhos');
            // OJO Condicion original Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
            $sqlTurnos = "SELECT Atutur, Atuetr, Atucta, Atupad, Atuadm, Fecha_data, Hora_data
            FROM ".$wbasedato."_000178 WHERE Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
             AND Atuest = 'on'
             AND Atuaor != 'on'
             AND Atutem = '".$tema."'
             ORDER BY REPLACE(Atutur, '-', '')*1 ASC"; 
            $resTurnos = mysql_query($sqlTurnos, $conexcar ) or die("<b>ERROR EN QUERY MATRIX(sqlturnos):</b><br>".mysql_error());
            while($rowTurnos = mysql_fetch_array($resTurnos))
            {
                // --> Solo mostrar turnos de maximo 6 horas atras. 
			    if(strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']) < strtotime('-6 hours'))
                     continue;
                $objTurno = new turno();
                $objTurno->Turno = substr($rowTurnos['Atutur'], 7);
                $objTurno->Modulo = "";
                $objTurno->Sala = "";
                $objTurno->Ubicacion = "";
                $objTurno->Estado = "";
                // --> Si no tiene triage
                if($rowTurnos['Atucta'] != "on")
                {
                    if($rowTurnos['Atuetr'] == "on")
                        {
                        $objTurno->Estado = "En triage";
                        //echo("En triage");
                        }
                    else
                        {
                        $objTurno->Estado = "Pendiente de triage";
                        //echo("Pendiente de triage");
                        }
                }
                else
                {
                    if($rowTurnos['Atupad'] == "on" && $rowTurnos['Atuadm'] != "on")
                        {
                        $objTurno->Estado =  "En admision";
                        //echo("En admisi&oacute;n");
                        }
                    elseif($rowTurnos['Atuetr'] == "on")
                        {
                        $objTurno->Estado = "En triage";	// --> En triage por reasignacion
                        //echo("En triage x reasignacion");
                        }
                        else
                        {
                            $objTurno->Estado = "En sala de espera";
                            // Obtener el estado de un paciente
                            $infoTurnoHce22	= array();
                            $basedatoshce = consultarAliasPorAplicacion($conexcar, $wemp_pmla, 'hce');
                            $sqlInfo22 = "SELECT A.*, B.Medtri
                            FROM ".$basedatoshce."_000022 AS A 
                            LEFT JOIN ".$wbasedato."_000048 AS B ON A.Mtrmed = Meduma
                            INNER JOIN ".$wbasedato."_000178 AS C ON (A.Mtrtur = C.Atutur AND C.Atutem = '".$tema."')
                            WHERE Mtrtur = '".$rowTurnos['Atutur']."'
                            AND Mtrest = 'on'";
                            //echo($sqlInfo22);
                            $resInfo22 = mysql_query($sqlInfo22, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlInfo22):</b><br>".mysql_error());
                            if (mysql_num_rows($resInfo22) > 0)
                            {
                                $rowInfo22 = mysql_fetch_array($resInfo22, MYSQL_ASSOC);
                                $infoTurnoHce22 = $rowInfo22;
                                // --> Si el paciente tiene una entrega en la 17 no lo muestro, ya que me indica que el paciente va para hospitalizacion.
                                $sqlEntrega = "SELECT Eyrnum FROM ".$wbasedato."_000017
                                WHERE Eyrhis = '".$infoTurnoHce22['Mtrhis']."'
                                AND Eyring = '".$infoTurnoHce22['Mtring']."'
                                AND Eyrsor = '".$infoTurnoHce22['Mtrcci']."'
                                AND Eyrtip = 'Entrega'
                                AND Eyrest = 'on'";
                                //echo($sqlEntrega);
                                $objTurno->Estado = "En sala de espera";
                                $resEntrega = mysql_query($sqlEntrega, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlEntrega):</b><br>".mysql_error());
                                if (mysql_num_rows($resEntrega) == 0)
                                {
                                    // --> Cosultar si el turno tiene muerte o alta definitiva.
                                    $sqlMuerteAlta = "SELECT id FROM ".$wbasedato."_000018
                                    WHERE Ubihis  = '".$infoTurnoHce22['Mtrhis']."'
                                    AND Ubiing  = '".$infoTurnoHce22['Mtring']."'
                                    AND (Ubiald = 'on' OR Ubimue = 'on')";
                                    //echo($sqlMuerteAlta);
                                    $resMuerteAlta = mysql_query($sqlMuerteAlta, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlMuerteAlta):</b><br>".mysql_error());
                                    if (mysql_num_rows($resEntrega) == 0)
                                    {
                                        // --> Si el turno tiene una conducta asociada
                                        if(trim($infoTurnoHce22['Mtrcon'] != ''))
                                        {
                                            $sqlConducta = "SELECT Condes, Conmue, Conalt FROM ".$basedatoshce."_000035
                                            WHERE Concod = '".$infoTurnoHce22['Mtrcon']."'";
                                            $resConducta = mysql_query($sqlConducta, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlConducta):</b><br>".mysql_error());
                                            if($rowConducta = mysql_fetch_array($resConducta))
                                            {
                                                if($rowConducta['Conmue'] != 'on')
                                                {
                                                    $objTurno->Estado = ucfirst(strtolower($rowConducta['Condes']));
                                                    // --> Si el estado es de alta.
					                                $enAltaMedica = (($rowConducta['Conalt'] == 'on') ? TRUE : FALSE);
                                                    // --> Obtener ubicacion
                                                    $sqlUbicacion = "SELECT Habcpa, Habzon FROM ".$wbasedato."_000020
                                                    WHERE Habhis = '".$infoTurnoHce22['Mtrhis']."'
                                                    AND Habing = '".$infoTurnoHce22['Mtring']."'AND Habcub = 'on'";
                                                    $resUbicacion = mysql_query($sqlUbicacion, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlUbicacion):</b><br>".mysql_error());
                                                    if($rowUbicacion = mysql_fetch_array($resUbicacion))
                                                    {
                                                        $objTurno->Sala = ucfirst(strtolower($rowUbicacion['Habzon']));
                                                        $objTurno->Ubicacion = ucfirst(strtolower($rowUbicacion['Habcpa']));
                                                    }
                                                }
                                                else
                                                {
                                                    $altaOmuerte = "true";
                                                }
                                                
                                            } 
                                        }
                                        else
                                        {
                                            if($infoTurnoHce22['Mtrcur'] == 'on')
                                            {
                                                $objTurno->Estado = "En consulta ".(($infoTurnoHce22['Medtri'] == 'on') ? "triage" : "");
                                                // --> obtengo el puesto de trabajo del medico que lo esta atendiendo
                                                $sqlEnConsulta = "SELECT Puenom FROM ".$wbasedato."_000180
                                                WHERE Pueusu = '".$infoTurnoHce22['Mtrmed']."'";
                                                $resEnConsulta = mysql_query($sqlEnConsulta, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlEnConsulta):</b><br>".mysql_error());
                                                if($rowEnConsulta = mysql_fetch_array($resEnConsulta))
                                                    $objTurno->Ubicacion = ucfirst(strtolower(utf8_encode($rowEnConsulta['Puenom'])));
                                            }
                                            else
                                            {
                                                // --> Si ya tiene un triagge asignado y no tiene fecha de consulta.
		                                        if(trim($infoTurnoHce22['Mtrtri'] != '') && $infoTurnoHce22['Mtrfco'] == '0000-00-00')
                                                {
                                                    // --> Indica que el turno esta en espera de consulta
                                                    $objTurno->Estado = "Pendiente de consulta";
                                                }
                                                elseif(trim($infoTurnoHce22['Mtrtri'] == ''))
                                                {
                                                    // --> Indica que el turno esta en espera de triagge
			                                        $objTurno->Estado = "Pendiente de triage";
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $altaOmuerte = "true";
                                    }
                                }
                                else
                                {
                                    $altaOmuerte = "true";
                                }
                            }
                            else
                                $objTurno->Estado = "Pendiente de admision";
                        }
                }
                $this->arrFilasTur[]= $objTurno;
            }   // cierre del while
        }
    } 
    // PAP, SALA DE ATENCION
    class listarAlertas
    {
        public $wemp_pmla;
        public $tema;   
        public $arrAlerta = Array();
        public function __construct ($wemp_pmla, $tema)
        {
            $wbasedato = consultarAliasPorAplicacion($conexcar, $wemp_pmla, 'cliame');
            // condicion original A.Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
            $sqlAlertas = "SELECT A.Fecha_data, A.Hora_data, Turtur, Puenom, Conpri, Puetem, Turllv
            FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000301 ON(Turtem = Puetem AND Turven = Puecod)
			INNER JOIN ".$wbasedato."_000299 ON(Turupr = Concod) 
            WHERE A.Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
            AND Turtem = '".$tema."'
            AND Turest = 'on'
            AND Turllv = 'on'";
            $resAlertas = mysql_query($sqlAlertas, $conexcar ) or die("<b>ERROR EN QUERY MATRIX(sqlAlertas):</b><br>".mysql_error());
            //echo ($sqlAlertas);
            //return;
            while ($rowAlerta = mysql_fetch_array($resAlertas))
            {
                // --> Solo mostrar turnos de maximo 12 horas atras
                if(strtotime($rowAlerta['Fecha_data']." ".$rowAlerta['Hora_data']) < strtotime('-12 hours'))
				     continue;
                $objAlerta = new alertas();
                $tmpturno = substr($rowAlerta['Turtur'], 7);
                $tmpturno = substr($tmpturno, 0, 2)." ".substr($tmpturno, 2, 5);
                // se llena cada campo del objeto temporal de alerta de la clase alerta
                $objAlerta->turnoAlerta = $tmpturno;
                $objAlerta->moduloAlerta = utf8_encode($rowAlerta["Puenom"]);
                $objAlerta->prioridadAlerta = utf8_encode($rowAlerta["Conpri"]);
                $this->arrAlerta[]= $objAlerta;
            }
        }
    }
    // PAP, SALA DE ATENCION
    class listarTurnos 
    {	
        public $wemp_pmla;
        public $tema;
        public $arrFilasTur = Array();
        public function __construct ($wemp_pmla, $tema) 
        {
            $wbasedato = consultarAliasPorAplicacion($conexcar, $wemp_pmla, 'cliame');
            // original ojo A.Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
            $sqlTurnos = "SELECT A.Fecha_data, A.Hora_data, Turtur, Conpri, Puenom
            FROM ".$wbasedato."_000304 AS A INNER JOIN ".$wbasedato."_000299 ON(Turupr = Concod)
                   INNER JOIN ".$wbasedato."_000301 ON(Turtem = Puetem AND Turven = Puecod)
            WHERE A.Fecha_data >= '".date("Y-m-d",strtotime("-1 day"))."'
             AND Turtem = '".$tema."'
             AND Turest = 'on'
             AND Turpat = 'on'
             ORDER BY REPLACE(Turtur, '-', '')*1 ASC";
            $resTurnos = mysql_query($sqlTurnos, $conexcar ) or die("<b>ERROR EN QUERY MATRIX(sqlturnos):</b><br>".mysql_error());
            // echo ($sqlTurnos);
            // return;
            while ($rowTurnos = mysql_fetch_array($resTurnos))
            {
                // --> Solo mostrar turnos de maximo 12 horas atras
			    if(strtotime($rowTurnos['Fecha_data']." ".$rowTurnos['Hora_data']) < strtotime('-12 hours'))
                    continue;
                $objTurno = new turno();
                $tmpturno = substr($rowTurnos['Turtur'], 7);
                $tmpturno = substr($tmpturno, 0, 2)." ".substr($tmpturno, 2, 5);
                // se llena cada campo del objeto temporal de turno de la clase turno
                $objTurno->Turno = $tmpturno;
                $objTurno->Modulo = utf8_encode($rowTurnos["Puenom"]);
                $objTurno->Prioridad = utf8_encode($rowTurnos["Conpri"]);
                // echo (utf8_encode($rowTurnos["Conpri"])."<BR>");
                // se lleva al arreglo el objeto turno
                $this->arrFilasTur[]= $objTurno;
                // echo ($tmpturno."<BR>");
            }  
             // var_dump($arrFilasTur); 
        }
    }
    // LISTAR TURNOS PARA ENDOSCOPIA
    class listarTurnosEn
    {
        public $wemp_pmla;
        public $tema;
        public $solucionCitas;
        public $arrFilasTur = Array(); 
        public function __construct ($wemp_pmla, $tema, $solucionCitas)
        {
            $wbasedato = $solucionCitas;
            $wcencam = consultarAliasPorAplicacion($conexcar, $wemp_pmla, "camilleros");
            $newPrefix = getPrefixTables($wbasedato);  
            $fecha_actual = date('Y-m-d');
            $infoMaxTimeAcc = getConfigurationCcpTiempoMaximo($wbasedato);
            // condicion original cts23.Fecha_data = '{$fecha_actual}' 
            $sqlTurnos = "SELECT {$newPrefix}acp, cts23.{$newPrefix}tur, cts23.{$newPrefix}doc, c.RacNam, c.Raclis,
            c.Ractex, c.Raccod, c.Racact,  cts23.idSolcam, cts23.".$newPrefix."acp as ultimaAccion, 
            cts23.".$newPrefix."hua as horaUltimaAccion
            FROM {$wbasedato}_000023 cts23
                LEFT JOIN {$wbasedato}_000032 c ON c.Raccod = cts23.{$newPrefix}acp
                INNER JOIN {$wbasedato}_000009 CE09 ON (cts23.".$newPrefix."doc = replace( replace( CE09.cedula, '\t', '' ) , ' ', '' )  AND cts23.fecha_data = CE09.Fecha)
            WHERE cts23.Fecha_data = '{$fecha_actual}' 
                AND cts23.{$newPrefix}fpr = 'off'                         
                AND cts23.{$newPrefix}est = 'on' 
                AND CE09.Activo = 'A'
                AND c.Raclis = 'off' 
            GROUP BY  cts23.{$newPrefix}tur 
            ORDER by cts23.".$newPrefix."hua desc  ";
            $resTurnos  = mysql_query($sqlTurnos, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());
            // echo($sqlTurnos);
            // return;
            while($rowTurnos = mysql_fetch_array($resTurnos))
            {
                $mostrarEnListado = true;
                if(isset($rowTurnos["idSolcam"]) && $rowTurnos["idSolcam"] != 0)
                {
                    $sqlCam = "SELECT fecha_llegada, hora_llegada FROM ".$wcencam."_000003 WHERE id = ".$rowTurnos["idSolcam"];
                    $resCam = executeQuery($sqlCam);
                    $rowCam = mysql_fetch_assoc($resCam);
                    if($rowCam["fecha_llegada"] != "0000-00-00" && $rowCam["hora_llegada"] != "00:00:00")
                    {
                        $mostrarEnListado = false;
                    }
                }else if($rowTurnos["ultimaAccion"] != "" && in_array($rowTurnos["ultimaAccion"] , $infoMaxTimeAcc["arrCCoFinPro"]))
                {
                    //Se le suma el tiempo configurado para el centro de costo a la hora en que se realizó la ultima acitividad 
                    $nuevafecha = strtotime ( '+' . $infoMaxTimeAcc["maxTime"].' hour' , strtotime ( $rowTurnos["horaUltimaAccion"] ) ) ;
                    $nuevafecha = date ( 'H:i:s' , $nuevafecha );
                    
                    if(date("H:i:s") >= $nuevafecha)
                    {
                        $mostrarEnListado = false;									
                    }
                } else
                {
                    $mostrarEnListado = true;
                }
                if($mostrarEnListado)
                {
                    $turno = $rowTurnos[$newPrefix.'tur'] != "" ? $rowTurnos[$newPrefix.'tur'] : $rowTurnos[$newPrefix.'doc'] ;
                    //Espera de Admision
                    $objTurno = new turno();
                    $objTurno->Turno = substr($rowTurnos[$newPrefix.'tur'], 7);
                    if($rowTurnos['RacNam'] != '')
                    {
                        $texto = $rowTurnos["Ractex"] != "" ? $rowTurnos['Ractex'] : $rowTurnos['RacNam'];
                        $objTurno->Estado = utf8_encode($texto);
                    } else
                    {
                        $objTurno->Estado = "En espera de Atencion";
                    }
                    // echo($objTurno->Turno.$objTurno->Estado."<BR>");
                    $this->arrFilasTur[] = $objTurno;
                }
            }
        } 
    }
    // LISTAR ALERTAS PARA ENDOSCOPIA
    class listarAlertasEn
    {
        public $wemp_pmla;
        public $tema;
        public $solucionCitas;     
        public $arrAlerta = Array(); 
        public function __construct ($wemp_pmla, $tema, $solucionCitas)
        {
            $wbasedato = $solucionCitas;
            $newPrefix = getPrefixTables($wbasedato); 
            $sqlTurnos = "SELECT l.".$newPrefix."tur,  ".$newPrefix."acp, c.RacNam, t.Actnma, m.Ubides, l.".$newPrefix."els as llamadoSinCita, l.".$newPrefix."ecs as colgadoSinCita
            FROM ".$wbasedato."_000023 l
            LEFT JOIN ".$wbasedato."_000032 c on c.Raccod = l.".$newPrefix."acp
            LEFT JOIN ".$wbasedato."_000031 t on t.Actcod = c.Racacn
            LEFT JOIN ".$wbasedato."_000027 m on m.Ubicod = l.".$newPrefix."ubi
            INNER JOIN {$wbasedato}_000009 CE09 ON (l.".$newPrefix."doc = replace( replace( CE09.cedula, '\t', '' ) , ' ', '' )  AND l.fecha_data = CE09.Fecha)
            WHERE l.Fecha_data = '".date("Y-m-d")."'
            AND CE09.Activo = 'A'
            AND l.".$newPrefix."fpr = 'off'
            AND l.".$newPrefix."est = 'on' ";
            $resTurnosAlertas  = mysql_query($sqlTurnos, $conexcar) or die("<b>ERROR EN QUERY MATRIX(sqlTurnos):</b><br>".mysql_error());  
            // echo($sqlTurnos);
            while($rowTurnosAlertas = mysql_fetch_array($resTurnosAlertas))
            {
                if(($rowTurnosAlertas['RacNam'] != '' && $rowTurnosAlertas['Actnma'] == "Llamar") || ($rowTurnosAlertas['llamadoSinCita'] == "on" && $rowTurnosAlertas['colgadoSinCita']  == "off" ))
                {
                    $objAlerta = new alertas();
                    $objAlerta->turnoAlerta = substr($rowTurnosAlertas[$newPrefix.'tur'], 7);
                    $objAlerta->Estado = utf8_encode($rowTurnosAlertas['Ubides']);
                    $this->arrAlerta[]= $objAlerta;
                }
            }
        }
    }   
?>
