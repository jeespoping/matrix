<?php
//=========================================================================================================================================\\
//       	webservice para sistema de wicalling y enfermeri. Trae las camas de todos los pisos.
//=========================================================================================================================================\\
//DESCRIPCION:  parametros:
//              wemp_pmla
//                      
//AUTOR:				TAITO
//FECHA DE CREACION:	2021-03-30
    include_once("conex.php");
    include("root/comun.php");
    ob_end_clean();
    $wemp_pmla;
    $conex;
    $wbasedatomovhos;
    $wbasedatocliame;
    $wbdhcecs;
    if ($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        if (empty($wemp_pmla))
        {
            header("HTTP/1.1 400 Bad Request"); 
            exit();
        }
        $wemp_pmla = $_GET['wemp_pmla'];
        $conex = obtenerConexionBD("matrix");
        $wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
        $wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');	
        $wbdhcecs = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
        $objClinica = new clinica();
        $objClinica->poblarPisos();
        header("HTTP/1.1 200 OK");
        echo json_encode($objClinica);
        exit();
    } 
    //En caso de que ninguna de las opciones anteriores se haya ejecutado
    header("HTTP/1.1 400 Bad Request"); 
    exit();

    // PISOS 
    class clinica
    {
        public $Pisos = Array();
        public function poblarPisos ()
        {
            $qycentrocostos = "Select Ccocod,Cconom,Ccohos,Ccoest,Ccopis,Ccotor,Cconoc from ".$GLOBALS['wbasedatomovhos']."_000011 
            where Ccohos = 'on' or Ccourg='on' and Ccoest = 'on' order by ccocod";
            $reg0 = mysql_query($qycentrocostos, $conex ) or die("<b>ERROR EN QUERY MATRIX(qycentrocostos):</b><br>".mysql_error()); 
            //echo($qycentrocostos);
            //recorrer cada piso para ir y poblar las zonas
            while ($regcco = mysqli_fetch_array($reg0))
            {
                $objPiso = new piso();
                $objPiso->NombrePiso = utf8_encode($regcco["Cconom"]);
                $objPiso->centroCostos = $regcco["Ccocod"];
                $tmpPiso = $regcco["Ccocod"];
                $objPiso->poblarZonas($tmpPiso);
                $this->Pisos[]=$objPiso;
            }
            return;
        }
    }
    class piso
    {
        public $NombrePiso;
        public $centroCostos;
        public $Zonas = Array();
        public function poblarZonas($tmpPiso)
        {
            $qyzonaspiso = "Select Ccocod,Cconom,Ccohos,Ccoest,Ccopis,Ccotor,Ccozon,Ccourg
            from ".$GLOBALS['wbasedatomovhos']."_000011 where  Ccocod = '".$tmpPiso."' and Ccoest='on' and Ccohos='on' or Ccourg='on' ";
            // echo($qyzonaspiso."</b><br>");
            $reg1 = mysql_query($qyzonaspiso, $conex ) or die("<b>ERROR EN QUERY MATRIX(qyzonaspiso):</b><br>".mysql_error()); 
            $regzon = mysql_fetch_array($reg1);
            $nomZonas = NULL;
            $nomZonas = explode(",",$regzon["Ccozon"]);                 
            //echo("PISO:".$tmpPiso." Zona:".$regzon["Cconom"]." LONG:".strlen(trim($regzon["Ccozon"]))."</b><br>");
            if (strlen(trim( $regzon["Ccozon"])) == 0) 
            {
                // echo("Piso sin Zonas:".$tmpPiso."</b><br>");
                $objZonas = new zona();
                $tmpZona = "Unica P".$tmpPiso;
                $objZonas->idZona = "0";
                $objZonas->NombreZona = $tmpZona;
                $objZonas->poblarCamas($tmpPiso,$tmpZona);
                $this->Zonas[] = $objZonas;
                return;
            }
            else
            {
                $num = count($nomZonas);
                for ($i = 0; $i < $num; ++$i)
                {
                    $objZonas = new zona();
                    $objZonas->idZona = $i;
                    $objZonas->NombreZona = $nomZonas[$i];
                    $tmpZona = $nomZonas[$i];
                    $objZonas->poblarCamas($tmpPiso,$tmpZona);
                    $this->Zonas[] = $objZonas;
                }    
            }
        }
    }

    // ZONAS
    class zona
    {
        public $idZona;
        public $NombreZona;
        public $Camas = Array(); 
        public function poblarCamas($tmpPiso,$tmpZona)
        {
            if ($tmpZona == "Unica")
            {
                $condZona = "";
            }
            else
            {
                $condZona = "and Ccozon LIKE '%".$tmpZona."%'";
            }
            $qycamasxzona = "Select A.Habcco,A.Habhis,A.Habing,A.Habcod,A.Habali,A.Habdis,A.Habest,A.habpro,A.Fecha_Data,Ccocod,
            Cconom,Ccozon from ".$GLOBALS['wbasedatomovhos']."_000020 as A
            INNER JOIN ".$GLOBALS['wbasedatomovhos']."_000011 ON(A.Habcco = Ccocod)
            where  A.Habcco='".$tmpPiso."' ".$condZona." order by Habcod"; 
            //echo($qycamasxzona."</b><br>");
            $reg2 = mysql_query($qycamasxzona, $conex ) or die("<b>ERROR EN QUERY MATRIX(qycamasxzona):</b><br>".mysql_error()); 
            if (mysql_num_rows($reg2) == 0)
            {
                $objdatoscama = new cama();
                $this->Camas[] = $objdatoscama;
                return;
            }  
            while ($regcam = mysqli_fetch_array($reg2))
            {
                $objdatoscama = new cama();
                $objdatoscama->NumeroCama = $regcam["Habcod"];
                // Regla de negocio: Habali=off and Habdis=off and Habest=on or Habpro=on OCUPADA 
                $estCam = NULL;
                if ($regcam[Habdis] == 'off' && $regcam[Habali] == 'off' && $regcam[Habest] == 'on' || $regcam[habpro] == 'on')
                    {
                        $estCam = "Ocupada";
                    }
                if (is_null($estCam) && $regcam[Habali] == 'off' && $regcam[Habdis] == 'on' && $regcam[Habest] == 'on' && $regcam[habpro] == 'off')
                    {
                        $estCam = "Disponible";
                    }
                if (is_null($estCam) && $regcam[Habali] == 'on')
                    {
                        $estCam = "Alistamiento";
                    } 
                if (is_null($estCam) && $regcam[Habdis] == 'on')
                    {
                        $estCam = "No habilitada";
                    }
                $objdatoscama->EstadoCama = $estCam;
                //if (intval($regcam["Habhis"]) != 0)
                if ($estCam == "Ocupada")
                {
                    $nroHistoria = $regcam["Habhis"];
                    $nroIngreso = $regcam["Habing"];
                    $objdatoscama->poblarPaciente($nroHistoria,$nroIngreso);
                    $objdatoscama->DatosPaciente = $arrdatPaciente;
                }
                $this->Camas[] = $objdatoscama;
            }
        }
    }

    // // CAMAS
    class cama
    {
        public $NumeroCama;
        public $EstadoCama;
        public $Paciente;
        public function poblarPaciente($nroHistoria,$nroIngreso)
        {
            $objdatosPac = new paciente();
            // Qry para buscar existencia paciente y sacar datos Noms,Apells,Historia clinica
            $qydatcli = "Select Pachis,Pactdo,Pacdoc,Pacap1,Pacap2,Pacno1,Pacno2,Pacfna
            from ".$GLOBALS['wbasedatocliame']."_000100 where Pachis='".$nroHistoria."'";
            // echo ($qydatcli."</b><br>");
            $reg3 = mysql_query($qydatcli, $conex ) or die("<b>ERROR EN QUERY MATRIX(qydatcli):</b><br>".mysql_error());
            $regpac = mysql_fetch_array($reg3);
            $objdatosPac->Nombre = utf8_encode($regpac["Pacno1"]." ".$regpac["Pacno2"]." ".$regpac["Pacap1"]." ".$regpac["Pacap2"]);
            $objdatosPac->Documento = $regpac["Pacdoc"];
            $objdatosPac->Ingreso = $nroIngreso;
            // Calcular edad del paciente
            $fecha_nacimiento = $regpac["Pacfna"];
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($fecha_nacimiento), date_create($dia_actual));
            $objdatosPac->Edad = $edad_diff->format('%y');
            //
            // Qry buscar EPS
            $qyeps = "Select Inghis,Ingent,Fecha_data from ".$GLOBALS['wbasedatocliame']."_000101 where Inghis='".$nroHistoria."'
            ORDER BY Fecha_data desc LIMIT 1";
            // echo ($qyeps);
            $reg4 = mysql_query($qyeps, $conex ) or die("<b>ERROR EN QUERY MATRIX(qyeps):</b><br>".mysql_error());
            $regeps = mysql_fetch_array($reg4);
            $objdatosPac->Eps = utf8_encode($regeps["Ingent"]);
            // //
            // // Qry POR DECISION DE CLA NO SE PASA. buscar medico del paciente y especialidad. 
            // // $qymed = "SELECT A.Mtrhis,A.Mtrmed,A.Fecha_data,Medesp,Medno1,Medno2,Medap1,Medap2,Espcod,Espnom
            // //         FROM ".$GLOBALS['wbdhcecs']."_000022 AS A
            // //         INNER JOIN ".$GLOBALS['wbasedatomovhos']."_000048 ON(Mtrmed = Meduma) 
            // //         INNER JOIN ".$GLOBALS['wbasedatomovhos']."_000044 ON(Medesp = Espcod)
            // //         WHERE A.Mtrhis='".$nroHistoria."' ORDER BY A.Fecha_data  desc LIMIT 1";
            // // $reg5 = mysql_query($qymed, $conex ) or die("<b>ERROR EN QUERY MATRIX(qymed):</b><br>".mysql_error());
            // // $regmed = mysql_fetch_array($reg5);
            // // $objdatosPac->Medico = utf8_encode($regmed["Medno1"]." ".$regmed["Medno2"]." ".$regmed["Medap1"]." ".$regmed["Medap2"]);
            // // $objdatosPac->Especialidad = utf8_encode($regmed["Espnom"]);
            // // echo ($qymed);
            // //
            // Qry buscar alergias y aislamiento y/o condicion especial
            $qyalerais = "SELECT A.Daahis,A.Daacod,Maacod,Maades,Maatip FROM ".$GLOBALS['wbasedatomovhos'].
                        "_000220 AS A INNER JOIN ".$GLOBALS['wbasedatomovhos'].
                        "_000217 ON(A.Daacod = Maacod) 
                        WHERE A.Daahis='".$nroHistoria."'";
            $reg6 = mysql_query($qyalerais, $conex ) or die("<b>ERROR EN QUERY MATRIX(qyalerais):</b><br>".mysql_error());
            // echo ($qyalerais);
            while ($regalerais = mysqli_fetch_array($reg6))
            {
                if ($regalerais["Maatip"] == "AG")
                {
                    $objdatosPac->Alergias[] = utf8_encode($regalerais["Maades"]);
                }
                else
                {
                    $objdatosPac->Aislamiento[] = utf8_encode($regalerais["Maades"]);
                }
            }
            // // No es posible extraer de la BD. Se acuerda con personal wicalling dejarlo en ''
            // // $objdatosPac->Rh = "";     
            $objdatosPac->Formacion = "";   
            $this->Paciente = $objdatosPac;          
        }
    }
    class paciente
    {
        public $Nombre;        
        public $Documento;      
        public $Ingreso;        
        public $Eps;            
        public $Alergias = Array();                
        public $Aislamiento = Array();   
        public $Formacion;       
        public $Edad; 
    }
?>