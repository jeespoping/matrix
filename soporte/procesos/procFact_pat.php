<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GRABACION DE FACTURAS PATOLOGIA - MATRIX</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> <!-- INDICADOR DE CARGA DE PAGINA-->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="cssFact_pat.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <link href="http://mx.lasamericas.com.co/matrix/paf/procesos/CssAcordeonpaf.css" rel="stylesheet">
    <script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="jsFact_pat.js"></script>
    <script>
        function copiar()
        {
            //valor1 = document.getElementById('codigos').value;
            //alert(valor1);
            document.getElementById('historias').value = opener.document.formNuevo.planSelected.value;
            document.getElementById('idCargos').value = opener.document.formNuevo.codigoCargo.value;
        }
    </script>
    <?php
    include_once("conex.php"); //publicacion en matrix
    include_once("root/comun.php"); //publicacion en matrix
    ///*
    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente7777.</label>
        </div>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wusuario = $user_session[1];
        mysql_select_db("matrix");

        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }
    //*/

    $wemp = '11';
    switch($wemp)
    {
        case '11': $wbasedato = 'patol';break;
        case '02': $wbasedato = 'clisur';break;
    }
    $tablaFE = 'root';
    $res = mysql_query("SELECT cjecco, cjecaj, cjetin, cjetem from".' '."$wbasedato"."_000030 WHERE cjeusu = '$wusuario' AND cjeest = 'on'");
    $num = mysql_num_rows($res);

    $accion = $_GET['accion'];
    $fechaActual = date('Y-m-d');       $horaActual = date('H:i:s');        $añoActual = date('Y');                 $mesActual = date('m');
    $fechaInicial = $_GET['fechaI'];    $fechaFinal = $_GET['fechaF'];      $valTotCargos = $_GET['valTotCar'];     $responsable = $_GET['responsable'];
    $valTotIva = 0;                     $valCopago = 0;                     $valCuotMod = 0;                        $valDescto = 0;
    $valAbono = 0;                      $valNotDeb = 0;                     $valNotCre = 0;                         $valSaldo = $valTotCargos;
    $estCargo = 'on';                   $cantResp = 1;                      $porcReco = 100;                        $valTope = 0;
    $valRecibo = 0;                     $usuario = 'C-'.$wusuario;
    $historia = $_GET['hisPac'];
    $ingreso = $_GET['ingPac'];         $totalResp = $_GET['totalResp'];    $accion2 = $_GET['accion2'];            $totalResp = str_replace(',','',$totalResp);
    $historias = $_POST['historias'];   $idCargos = $_POST['idCargos'];
    ?>
</head>

<?php
switch($accion)
{
    case 'updateTotResp':
        ?>
        <body>
        <section>
            <?php
            if($accion2 == 'restar')
            {
                $queryTotalCargos = mysql_query("select SUM(Tcarvto) from".' '."$wbasedato"."_000106
                                                             where Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                             AND Tcarres LIKE '$responsable%'
                                                             AND Tcarhis = '$historia'
                                                             AND Tcaring = '$ingreso'
                                                             AND tcarest = 'on'
                                                             AND tcarcan > 0
                                                             AND Tcarvun > 0
                                                             AND Tcarfre = 0
                                                             AND tcarfac = 'S'
                                                             AND Tcarfre = 0
                                                             AND Tcarfex = 0");
                $datoTotalCargos = mysql_fetch_array($queryTotalCargos);
                $valorTotalCargosPaciente = $datoTotalCargos[0];

                $valorTotalEps = $totalResp-$valorTotalCargosPaciente;
                ?>
                <script>
                    opener.document.frmPrincipal.totalResp.value = '<?php echo number_format($valorTotalEps) ?>';
                    opener.document.frmPrincipal.valTotCar.value = '<?php echo $valorTotalEps ?>';
                    window.close();
                </script>
                <?php
            }
            if($accion2 == 'sumar')
            {
                $queryTotalCargos = mysql_query("select SUM(Tcarvto) from".' '."$wbasedato"."_000106
                                                             where Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                                             AND Tcarres LIKE '$responsable%'
                                                             AND Tcarhis = '$historia'
                                                             AND Tcaring = '$ingreso'
                                                             AND tcarest = 'on'
                                                             AND tcarcan > 0
                                                             AND Tcarvun > 0
                                                             AND Tcarfre = 0
                                                             AND tcarfac = 'S'
                                                             AND Tcarfre = 0
                                                             AND Tcarfex = 0");
                $datoTotalCargos = mysql_fetch_array($queryTotalCargos);
                $valorTotalCargosPaciente = $datoTotalCargos[0];

                $valorTotalEps = $valorTotalCargosPaciente+$totalResp;
                ?>
                <script>
                    opener.document.frmPrincipal.totalResp.value = '<?php echo number_format($valorTotalEps) ?>';
                    opener.document.frmPrincipal.valTotCar.value = '<?php echo $valorTotalEps ?>';
                    window.close();
                </script>
                <?php
            }
            ?>
        </section>
        </body>
        <?php
    break;
    case 'facturar':
        ?>
        <body style="overflow: hidden">
        <?php
        if($num > 0)
        {
            $row = mysql_fetch_array($res);

            $pos = strpos($row[0],"-");
            $wcco = substr($row[0],0,$pos);
            $wnomcco = substr($row[0],$pos+1,strlen($row[0]));

            $pos = strpos($row[1],"-");
            $wcaja = substr($row[1],0,$pos);
            $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));

            $wtiping = $row[2];
            if (!isset($wtipcli)) $wtipcli = $row[3];

            ?>
            <!-- FUNCIONES: -->
            <section>
                <?php
                function obtenerNumFac($wbasedato,$wcco,$posicion)
                {
                    $queryNumFac = mysql_query("SELECT * from".' '."$wbasedato"."_000003 WHERE ccocod = '$wcco'");
                    $datoNumFac = mysql_fetch_array($queryNumFac);

                    $wprefac = $datoNumFac[0];             //Prefijo
                    $wfueffa = $datoNumFac[10];             //Fuente
                    $wnrofac = $datoNumFac[2];             //Factura con el consecutivo
                    $numeroFac = $datoNumFac[9].'-'.$datoNumFac[11];

                    switch($posicion)
                    {
                        case 0: return $wprefac; break;
                        case 2: return $wnrofac; break;
                        case 3: return $numeroFac; break;
                        case 10: return $wfueffa; break;
                    }
                }

                function obtenerDatosEmpresa($wbasedato,$responsable,$posicion)
                {
                    $queryDatosEmpresa = mysql_query("select * from".' '."$wbasedato"."_000024 WHERE Empcod LIKE '$responsable%'");
                    $datosEmpresa = mysql_fetch_array($queryDatosEmpresa);

                    $empCod = $datosEmpresa[3];     $Empnit = $datosEmpresa[4];         $codEmpResCar = $datosEmpresa['Empres'];   $nomEmp = $datosEmpresa[6];
                    $tipoEmp = $datosEmpresa[18];   $porDescTarifa = $datosEmpresa[16];
                    //if($codEmpResCar == $responsable){ $estFactura = 'GE';} else{ $estFactura = 'RD';}
                    $estFactura = 'GE';

                    switch($posicion)
                    {
                        case 3: return $empCod; break;
                        case 4: return $Empnit; break;
                        case 5: return $codEmpResCar; break;
                        case 6: return $nomEmp; break;
                        case 18: return $tipoEmp; break;
                        case 16: return $porDescTarifa; break;
                        case 17: return $estFactura; break;
                    }
                }

                function generarConsFac($wbasedato,$wcco,$posicion)
                {
                    $queryNumFac = mysql_query("SELECT ccofai from".' '."$wbasedato"."_000003 WHERE ccocod = '$wcco'");
                    $datoNumFac = mysql_fetch_array($queryNumFac);
                    $wnrofac   =$datoNumFac[0];             //Consecutivo
                    $wnrofacNew = $wnrofac + 1;

                    mysql_query("update".' '."$wbasedato"."_000003 set ccofai = '$wnrofacNew' WHERE ccocod = '$wcco'");

                    $queryNumFac2 = mysql_query("SELECT ccopfa, ccoffa, ccofai from".' '."$wbasedato"."_000003 WHERE ccocod = '$wcco'");
                    $datoNumFac2 = mysql_fetch_array($queryNumFac2);

                    $numeroFac = $datoNumFac2[0].'-'.$datoNumFac2[2]; // PREFIJO DE EMPRESA Y CONSECUTIVO DE FACTURA
                    //$numeroFac = $datoNumFac2[2]; // SOLO CONSECUTIVO DE FACTURA (patologia no utiliza prefijo)
                    $prefijo = $datoNumFac2[0];     $newFact = $datoNumFac2[2];

                    switch($posicion)
                    {
                        case 0: return $prefijo; break;
                        case 1: return $numeroFac; break;
                        case 3: return $newFact; break;
                    }
                }

                function obtenerDatosPaciente($wbasedato,$historia,$ingreso,$posicion)
                {
                    $queryPaciente = mysql_query("SELECT pachis,pacno1,pacno2,pacap1,pacap2,pacdoc,ingcem,empnit,ingent,ingfei,ingsei,ingnin,empres,emptem,emptar,emppdt
                                              from".' '."$wbasedato"."_000100, ".' '."$wbasedato"."_000101, ".' '."$wbasedato"."_000024
                                              WHERE pachis = '$historia'
                                              AND pachis = inghis
                                              AND ingnin = '$ingreso'
                                              AND ingcem = empcod");
                    $datosPaciente = mysql_fetch_array($queryPaciente);
                    $pacdoc = $datosPaciente['5'];

                    switch($posicion)
                    {
                        case 5: return $pacdoc; break;
                    }
                }

                //ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LA FACTURA DESDE LA TABLA DE CONFIGURACION
                $q = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgfran, cfgfian, cfgffan, cfgran, cfgfcar, cfgfrac, cfgfiac, cfgffac, cfgrac, cfgpin, cfgmai, cfgdom "
                    ."   FROM ".$wbasedato."_000049 "
                    ."  WHERE cfgcco = '".$wcco."'";
                $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $row = mysql_fetch_array($res);

                $wnit_pos  =$row[0];
                $wnomemppos=$row[1];
                $wtipregiva=$row[2];
                $wtel_pos  =$row[3];
                $wdir_pos  =$row[4];

                if ($row[9] > $wfecha)
                {
                    $wnrores=$row[8];  //Nro Resolucion Anterior
                    $wfecres=$row[5];  //Fecha Resolucion Anterior
                    $wfacini=$row[6];  //Factura Inicial Anterior
                    $wfacfin=$row[7];  //Factura Final Anterior
                }
                else
                {
                    $wnrores=$row[13];  //Nro Resolucion Actual
                    $wfecres=$row[10];  //Fecha Resolucion Actual
                    $wfacini=$row[11];  //Factura Inicial Anterior
                    $wfacfin=$row[12];  //Factura Final Anterior
                }
                $wpagintern=$row[14];
                $wemail_pos=$row[15];
                $wteldompos=$row[16];

                $wresolucion= "Documento oficial de autorización de numeración: ".$wnrores." del ".$wfecres.", "
                    ."factura ".$wfacini." a la factura ".$wfacfin."."
                    ."Esta factura cambiaria de compraventa se asimila en "
                    ."todos sus efectos a una letra de cambio, Art. 621 y "
                    ."SS, 671 y SS 772, 773, 770 y SS del código de comercio.<BR>"
                    ."Factura impresa por computador cumpliendo con los "
                    ."requisitos del Art. 617 del E.T.<BR>";
                ?>
            </section>
            <!-- ////////// -->
            <?php
            $tipoEmp = obtenerDatosEmpresa($wbasedato,$responsable,18);     $empCode = obtenerDatosEmpresa($wbasedato,$responsable,3);
            $codEmpRes = obtenerDatosEmpresa($wbasedato,$responsable,5);    $porDescTarifa = obtenerDatosEmpresa($wbasedato,$responsable,16);
            $estFactura = obtenerDatosEmpresa($wbasedato,$responsable,17);  $pacdoc = obtenerDatosPaciente($wbasedato,$historia,$ingreso,5);
            $nomEmpresa = obtenerDatosEmpresa($wbasedato,$responsable,6);   $nitEmpresa = obtenerDatosEmpresa($wbasedato,$responsable,4)
            ?>

            <div class="panel panel-info" align="center" style="width: 60%; border: none">
                <h3>Va a generar una factura para:</h3>
                <h4 style="color: #428BCA; font-size: large; font-weight: bold"><? echo $codEmpRes.' '.$nomEmpresa ?></h4>
                <h3>Por valor de: </h3>
                <h3>$ <label><? echo number_format($valTotCargos,0) ?></label></h3>
                <br><br>
                <form method="post">
                    <input type="hidden" name="historias" id="historias">
                    <input type="hidden" name="idCargos" id="idCargos">
                    <input id="btnProceed" name="btnProceed" type="submit" class="btn btn-success btn-sm" value="Aceptar" onclick="copiar()" style="margin-bottom: 10px">
                    &ensp;&ensp;
                    <button id="btnCancelar" class="btn btn-warning btn-sm" style="margin-bottom: 10px" onclick="window.close()">Cancelar</button>
                </form>
            </div>
            <?php
            if(isset($_POST['btnProceed']))
            {
                ?><script>opener.document.frmParametros.submit(true);</script><?php //RECARGAR PAGINA PRINCIPAL
                $fueFact = obtenerNumFac($wbasedato,$wcco,10);  $newCons = generarConsFac($wbasedato,$wcco,1);
                list($prefijo,$newFact) = explode("-", $newCons);
                $prefijo = trim($prefijo);  $newFact = trim($newFact);
                //$prefijo = generarConsFac($wbasedato,$wcco,0);  $newFact = generarConsFac($wbasedato,$wcco,3);

                $array = explode(",", $historias); //CONVERTIR STRING EN ARRAY SEPARADO POR ','
                $longitud = count($array); //OBTENER LA LONGITUD DEL ARRAY (numero de historias pasadas)

                //GRABAR LA FACTURA EN TABLA_000018:
                ///*
                mysql_query("insert into".' '."$wbasedato"."_000018(Medico,Fecha_data,Hora_data,fenano,fenmes,fenfec,fenffa,fenfac,fentip,fennit,fencod,
                                                   fenres,fenval,fenviv,fencop,fencmo,fendes,fenabo,fenvnd,fenvnc,fensal,fenest,fencre,
                                                   fenpde,fenrec,fentop,fenhis,fening,fenesf,fenrln,fencco,fenrbo,fenimp,fendpa,fennpa,
                                                   fendev,fennac,fenobs,Seguridad)
                 VALUES('clisur','$fechaActual','$horaActual','$añoActual','$mesActual','$fechaActual','$fueFact','$newCons','$tipoEmp','$nitEmpresa','$empCode',
                        '$codEmpRes','$valTotCargos','$valTotIva','$valCopago','$valCuotMod','$valDescto','$valAbono','$valNotDeb','$valNotCre','$valTotCargos','$estCargo','$cantResp',
                        '$porDescTarifa','$porcReco','$valTope','VARIAS','NO APLICA','$estFactura','$wresolucion','$wcco','$valRecibo','off','NO APLICA','NO APLICA',
                        '0','','','$usuario')");
                //*/

                //SEPARAR LAS HISTORIAS DE SUS RESPECTIVOS INGRESOS
                for($i = 0; $i < $longitud; $i++)
                {
                    $historiaP = $array[$i];
                    $newArray = explode("-", $historiaP);
                    $his = $newArray[0];     $ing = $newArray[1];

                    //SABER CUANTOS CARGOS SE SELECCIONARON PARA GRABAR:
                    $query1 = mysql_query("select id,Tcarvto,Tcarser,Tcarconcod,Tcartercod,Tcarterpor
                                       from".' '."$wbasedato"."_000106
                                           where Tcarfec BETWEEN '$fechaInicial' AND '$fechaFinal'
                                           and Tcarres LIKE '$codResponsable%'
                                           and Tcarhis = '$his'
                                           and Tcaring = '$ing'
                                           AND tcarest = 'on'
                                           AND tcarcan > 0
                                           AND Tcarvun > 0
                                           AND tcarfac = 'S'
                                           AND Tcarfre = 0");
                    $conteo = mysql_num_rows($query1);  //NUMERO DE CARGOS POR HISTORIA

                    //OBTENER LOS ID DE LOS CARGOS QUE SE SELECCIONARON PARA CADA HISTORIA Y CADA INGRESO:
                    for($j = 0; $j < $conteo; $j++)
                    {
                        $dato2 = mysql_fetch_array($query1);
                        $numeroid = $dato2[0];  $valTotEsteCargo = $dato2[1]; //ID Y VALOR DEL CARGO DE TABLA_000106
                        $ccosGrab = $dato2[2];  $codConcepto = $dato2[3];    //CENTRO DE COSTOS DE GRABACION y CODIGO CONCEPTO DEL CARGO
                        $codTerce = $dato2[4];  $porcTerce = $dato2[5];     //CODIGO DEL TERCERO Y PORCENTAJE DE TERCERO

                        //echo 'HISTORIA :'.$his.' INGRESO :'.$ing.'<br>';
                        //echo  ' ID CARGO = '.$numeroid.' VALOR TOTAL ESTE CARGO = '.$valTotEsteCargo.' <br>';

                        //GRABAR LA AUDITORIA EN TABLA_000107:
                        $query107 = mysql_query("insert into".' '."$wbasedato"."_000107(Medico,Fecha_data,Hora_data,Audhis,Auding,Audreg,Audacc,Audusu,Seguridad)
                                            values('$wbasedato','$fechaActual','$horaActual','$his','$ing','$numeroid','Grabo','$wusuario','$usuario')");

                        //ACTUALIZAR CADA CARGO EN TABLA_000106 PARA LOS VALORES GRABADOS EN EL QUERY ANTERIOR (ACTUALIZAR Tcarfre = VALOR DE CADA CARGO):
                        $query106 = mysql_query("update ".' '."$wbasedato"."_000106 SET tcarfre = tcarfre + '$valTotEsteCargo' where id = '$numeroid'");

                        //GRABAR RELACION DE CARGOS CON FACTURAS, DE CADA PACIENTE, TABLA_000066
                        $query66 = mysql_query("insert into".' '."$wbasedato"."_000066(Medico,Fecha_data,Hora_data,Rcfffa,Rcffac,Rcfreg,Rcfval,Rcfest,Rcftip,Seguridad)
                                            values('$wbasedato','$fechaActual','$horaActual','$fueFact','$newCons','$numeroid','$valTotEsteCargo','on','R','$usuario')");

                        //GRABAR EN LA TABLA DE FACTURAS DETALLADAS (TABLA_000065), POR CONCEPTO, CCO Y TERCERO
                        $query65 = mysql_query("insert into".' '."$wbasedato"."_000065(Medico,Fecha_data,Hora_data,Fdefue,Fdedoc,Fdecco,Fdecon,Fdevco,Fdeter,
                                            Fdepte,Fdevde,Fdesal,Fdeest,Fdeffa,Fdefac,fdeviv,Seguridad)
                                            values('$wbasedato','$fechaActual','$horaActual','$fueFact','$newCons','$ccosGrab','$codConcepto','$valTotEsteCargo','$codTerce',
                                            '$porcTerce','0','$valTotEsteCargo','on','$fueFact','$newCons','0','$usuario')");

                        $query122 = mysql_query("insert into".' '."$tablaFE"."_000122(Medico,Fecha_data,Hora_data,Faeemp,Faefue,Faedoc,Faepre,Faetdo,Faeccf,Faeest,
                                              Faexml,Faexmf,Faedir,Faearc,Faexme,Faewen,Faewsf,Faewsr,Faewss,Faepdf,Faenid,Faefid,Seguridad)
                                              values('root','$fechaActual','$horaActual','$wemp','$fueFact','$newFact','$prefijo','FV','$wcco','on',
                                              '','','','','','','','','','','','','$usuario')");
                    }
                }
                ?>
                <div align="center" style="width: 60%; border: none">
                    <script>
                        document.getElementById('btnProceed').style.display = 'none';
                        document.getElementById('btnCancelar').style.display = 'none';
                    </script>
                    <h3>El numero de la factura generada es :</h3><h2><?echo $newCons ?></h2>
                    <br><br>
                    <button class="btn btn-info btn-sm" onclick="window.close()">CERRAR</button>
                </div>
                <?php
            }
        }
        else
        {
            ?>
            <h3>EL USUARIO ESTA INACTIVO O NO TIENE AUTORIZACION PARA FACTURAR</h3>
            <?php
        }
        ?>
        </body>
        <?php
    break;
}
?>
</html>