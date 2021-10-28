<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta charset="utf-8">
    <title>Historico - PAF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="botonespaf.css" rel="stylesheet" type="text/css">
    <link href="estilospaf.css" rel="stylesheet" type="text/css">
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="calendariopaf.js" type="text/javascript"></script>
    <script src="JsProcesospaf.js" type="text/javascript"></script>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
        });
    </script>
    <style>
        .alternar:hover{ background-color:#e1edf7;}
    </style>
    <style>
        .alternar2:hover{ background-color:#cd0a0a;}
    </style>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
    <?php
include_once("conex.php");
    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
        </div>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        

        include_once("root/comun.php");
        


        $conex = obtenerConexionBD("matrix");
    }
    include_once("paf/librarypaf.php");
    ?>
</head>

<body onload="window.resizeTo(1000,850);mostrarop1('fechaini','historia','contenido')">
<div id="container">
    <div id="loginbox" style="margin-top:1px; width: 950px">
        <div id="panel-info">
            <div class="panel-heading">
            </div>
            <div style="padding-top:5px" class="panel-body" >

                <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="historicopaf.php">
                    <table align="center">
                        <tr>
                            <td colspan="5">
                                <h5 class="text-primary"><strong>Parametros de busqueda: </strong></h5>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-group">
                                    <label>Nombre &nbsp;</label><input type="radio" checked name="selparam" id="selparam" value="0" onclick="mostrarop1('fechaini','historia','contenido')">
                                </div>
                            </td>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                            <td>
                                <div class="input-group">
                                    <label>Historia e Ingreso &nbsp;</label><input type="radio" name="selparam" id="selparam" value="1" onclick="mostrarop2('historia','fechaini','contenido')">
                                </div>
                            </td>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                            <td>
                                <div class="input-group">
                                    <label>Pacientes sin Egreso &nbsp;</label><input type="radio" name="selparam" id="selparam" value="2">
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 40%" id="fechaini">
                        <div class="input-group" style="margin: auto; border: none">
                            <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                            <span class="input-group-addon"><label>Nombre</label></span>
                            <input id="habitacion" type="text" class="form-control" style="width: 200px" name="Hnombre" value="">
                        </div>
                        <div class="input-group-addon" style="background-color: #ffffff; border: none">
                            <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                        </div>
                    </div>

                    <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 80%" id="historia">
                        <div class="input-group" style="margin: auto; border: none">
                            <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                            <span class="input-group-addon" style="width: 123px"><label>Historia</label></span>
                            <input id="historia" type="text" class="form-control" style="width: 200px" name="Hhistoria" value="">

                            <span class="input-group-addon"><label>Ingreso</label></span>
                            <input id="ingreso" type="text" class="form-control" style="width: 200px" name="Hingreso" value="">
                        </div>
                        <div class="input-group-addon" style="background-color: #ffffff; border: none">
                            <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                        </div>
                    </div>
                </form>

                <br><br>

                <form id="encabezado">
                    <table class="table">
                        <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                        <tr>
                            <td>Nombre Paciente</td>
                            <td>Historia</td>
                            <td>Ingreso</td>
                            <td>Ultima Ubicacion</td>
                            <td>Fecha Ronda</td>
                            <td>Auditor</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $parametro = $_POST['selparam'];
                        $nombre = $_POST['Hnombre'];
                        $historia = $_POST['Hhistoria'];
                        $ingreso = $_POST['Hingreso'];
                        $fecha_actual = date('Y-m-d');
                        $fechaanterior = strtotime ( '-1 day' , strtotime ( $fecha_actual ) ) ; // Mostrar desde 1 dias antes de la fecha actual //
                        $fechaanterior = date ( 'Y-m-d' , $fechaanterior );

                        if($parametro == null)
                        {
                            $query=mysql_queryV("select * from paf_000004 WHERE fecha_Ronda BETWEEN '$fechaanterior' AND '$fecha_actual' ORDER BY fecha_Ronda ASC");
                            $num_total_registros = mysql_num_rows($query);  //TOTAL DE REGISTROS EN PAF_000004 DESDE HACE 2 DIAS
                        }

                        if($parametro == '0')
                        {
                            $query=mysql_queryV("select * from paf_000004 WHERE nombre_Pac LIKE '%$nombre%' ORDER BY fecha_Ronda ASC ");
                        }
                        if($parametro == '1')
                        {
                            if($historia != null and $ingreso != null)
                            {
                                $query=mysql_queryV("select * from paf_000004 WHERE hc = '$historia' AND ingreso = '$ingreso' ORDER BY fecha_Ronda ASC");
                            }
                            elseif($historia != null and $ingreso == null)
                            {
                                $query=mysql_queryV("select * from paf_000004 WHERE hc = '$historia' ORDER BY fecha_Ronda ASC");
                            }
                        }
                        if($parametro == '2') // PACIENTES SIN EGRESO, PERO CON AL MENOS UNA RONDA REGISTRADA EN PAF_000004
                        {
                            $query=mysql_queryV("select * from paf_000004 WHERE fecha_Egreso = '' GROUP BY hc ORDER BY fecha_Ronda ASC");
                            //$query=mysql_queryV("select * from paf_000004 WHERE hc NOT IN (SELECT hc FROM paf_000004 WHERE fecha_Egreso != '') GROUP BY hc ORDER BY fecha_Ronda ASC");
                        }
                        while($dato=mysql_fetch_array($query))
                        {
                            ?>
                            <form id="formDATOS" method="post" action="historicopaf.php">
                                <?php
                                $hc = $dato['hc'];
                                $ing = $dato['ingreso'];
                                $query2=mysql_queryV("select * from movhos_000020 WHERE Habhis = '$hc' AND Habing = '$ing'");
                                $dato2=mysql_fetch_array($query2);
                                $hcFinded = $dato2['Habhis'];
                                if($hcFinded != null)
                                {
                                    ?>
                                    <tr id="rowDATOS" class="alternar">
                                        <td><?php echo $dato['nombre_Pac']?></td>
                                        <td><?php echo $dato['hc']?></td>
                                        <td><?php echo $dato['ingreso'] ?></td>
                                        <td><?php echo $dato['habitacion'] ?></td>
                                        <td><?php echo $dato['fecha_Ronda'] ?></td>
                                        <td><?php echo $dato['Seguridad'] ?></td>
                                        <td><input type="image" id="btnVer" src="/matrix/images/medical/paf/selecPaf.png" width="20" height="20" title="Ver Informe"></td>
                                    </tr>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <tr id="rowDATOS" class="alternar2" style="background-color: #FFAFAA" title="Paciente Inactivo en Matrix">
                                        <td><?php echo $dato['nombre_Pac']?></td>
                                        <td><?php echo $dato['hc']?></td>
                                        <td><?php echo $dato['ingreso'] ?></td>
                                        <td><?php echo $dato['habitacion'] ?></td>
                                        <td><?php echo $dato['fecha_Ronda'] ?></td>
                                        <td><?php echo $dato['Seguridad'] ?></td>
                                        <td><input type="image" id="btnVer" src="/matrix/images/medical/paf/selecPaf.png" width="20" height="20" title="Ver Informe"></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <input type="hidden" name="selparam" value="<?php echo $parametro ?>">
                                <input type="hidden" name="Hnombre" value="<?php echo $nombre ?>">
                                <input type="hidden" name="Hhistoria" value="<?php echo $historia ?>">
                                <input type="hidden" name="Hingreso" value="<?php echo $ingreso ?>">
                                <input type="hidden" name="idInforme" value="<?php echo $dato['id'] ?>">
                            </form>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <!---------------------------------------------------------------------------->

    <div id="divInforme" class="panel-body" style="padding-top: 1px">
        <?php $idR = $_POST['idInforme']; if($idR == null){$idR = $_GET['idInforme'];} ?>
        <form id="frmInforme">
            <?php
            if($idR != null)
            {
                ?><script>foco()</script><?php
                $consultaRegistro=mysql_queryV("select * from paf_000004 WHERE id = '$idR'");
                $datoRegistro=mysql_fetch_array($consultaRegistro);
                $habitacion = $datoRegistro['habitacion'];
                $fecha_ronda = $datoRegistro['fecha_Ronda'];
                $hc = $datoRegistro['hc'];
                $ingreso = $datoRegistro['ingreso'];
                $nombre = $datoRegistro['nombre_Pac'];
                $diagnostico = $datoRegistro['dx'];
                $comorbilidad = $datoRegistro['comorb'];
                //$cirugias = $datoRegistro['qx'];
                $ingresoPAF = $datoRegistro['fecha_Paf'];
                $retiroPAF = $datoRegistro['retiro_Paf'];
                $reintegroPAF = $datoRegistro['reintegro_Paf'];
                $retiroPAF2 = $datoRegistro['retiro_Paf2'];
                $progAmbu = $datoRegistro['prog_Ambu'];
                $indicacionCX = $datoRegistro['indicacion_Cx'];
                $fechaCX = $datoRegistro['fecha_Cx'];
                $cx1 = $datoRegistro['cx1'];
                $cx2 = $datoRegistro['cx2'];
                $cx3 = $datoRegistro['cx3'];
                $fechaReint = $datoRegistro['fecha_Reint'];
                $reint1 = $datoRegistro['reint1'];
                $reint2 = $datoRegistro['reint2'];
                $reint3 = $datoRegistro['reint3'];
                $ingresoUCI = $datoRegistro['ingreso_Uci'];
                $egresoUCI = $datoRegistro['egreso_Uci'];
                $reingresoUCI = $datoRegistro['reingreso_Uci'];
                $egresoUCI2 = $datoRegistro['egreso2_Uci'];
                $progHemo = $datoRegistro['prog_hemo'];
                $indicacionHemo = $datoRegistro['indicacion_Hemod'];
                $fechaHemo = $datoRegistro['fecha_Hemod'];
                $intervencionHemo = $datoRegistro['interv_Hemod'];
                $progElectrof = $datoRegistro['prog_electrof'];
                $indicacionElectro = $datoRegistro['indicacion_Electrof'];
                $fechaElectro = $datoRegistro['fecha_Electrof'];
                $intervencionElectro = $datoRegistro['interv_Electrof'];
                $iso = $datoRegistro['iso'];
                $observacion = $datoRegistro['observacion'];
                $nota = $datoRegistro['nota'];
                ?>
                <div style="width: 900px" class="table-bordered">
                    <table>
                        <tbody>
                        <tr>
                            <td><label class="labels" style="margin-top: 10px">Fecha de la Ronda: </label></td>
                            <td><input type="text" id="txtFecha" name="txtFecha" class="textbox" disabled value="<?php echo $fecha_ronda ?>"></td>

                            <td><label class="labels" style="margin-top: 5px">Habitacion: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $habitacion ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Numero de Historia: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $hc ?>"></td>

                            <td><label class="labels" style="margin-top: 5px">Ingreso: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $ingreso ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Nombre: </label></td>
                            <td colspan="3"><input type="text" class="textbox" style="width: 370px" disabled value="<?php echo $nombre ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Diagnostico: </label></td>
                            <?php
                            if($diagnostico != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $diagnostico ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $diagnostico ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Comorbilidades: </label></td>
                            <?php
                            if($comorbilidad != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $comorbilidad ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $comorbilidad ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Ingreso al PAF: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $ingresoPAF ?>"></td>

                            <td><label class="labels" style="margin-top: 5px">Retiro del PAF: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $retiroPAF ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Reintegro al PAF: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $reintegroPAF ?>"></td>

                            <td><label class="labels" style="margin-top: 5px">Segundo Retiro PAF: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $retiroPAF2 ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels"style="margin-top: 5px">Cirugia Programada: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $progAmbu ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Indicacion Cirugia: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $indicacionCX ?>"></td>

                            <td><label class="labels">Fecha Cirugia: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $fechaCX ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Cirugia 1: </label></td>
                            <?php
                            if($cx1 != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $cx1 ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $cx1 ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Cirugia 2: </label></td>
                            <?php
                            if($cx2 != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $cx2 ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $cx2 ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Cirugia 3: </label></td>
                            <?php
                            if($cx3 != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $cx3 ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $cx3 ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Fecha Reintervencion: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $fechaReint ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Reintervencion 1: </label></td>
                            <?php
                            if($reint1 != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $reint1 ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $reint1 ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Reintervencion 2: </label></td>
                            <?php
                            if($reint2 != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $reint2 ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $reint2 ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Reintervencion 3: </label></td>
                            <?php
                            if($reint3 != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $reint3 ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $reint3 ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Ingreso a UCI: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $ingresoUCI ?>"></td>

                            <td><label class="labels" style="margin-top: 5px">Egreso de UCI: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $egresoUCI ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Reingreso a UCI: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $reingresoUCI ?>"></td>

                            <td><label class="labels" style="margin-top: 5px">Segundo Egreso de UCI: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $egresoUCI2 ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels"style="margin-top: 5px">Hemodinamia Programada: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $progHemo ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Indicacion Hemodinamia: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $indicacionHemo ?>"></td>

                            <td><label class="labels" style="margin-top: 5px">Fecha Hemodinamia: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $fechaHemo ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Intervencion Hemodinamia: </label></td>
                            <?php
                            if($intervencionHemo != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $intervencionHemo ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $intervencionHemo ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels"style="margin-top: 5px">Electrofisiologia Programada: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $progElectrof ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Indicacion Electrofisiologia: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $indicacionElectro ?>"></td>

                            <td><label class="labels" style="margin-top: 5px">Fecha Electrofisiologia: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $fechaElectro ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">Intervencion Electrofisiologia: </label></td>
                            <?php
                            if($intervencionElectro != null)
                            {
                                ?>
                                <td colspan="3"><textarea disabled rows="1" cols="78" style="margin-top: 3px"><?php echo $intervencionElectro ?></textarea></td>
                                <?php
                            }
                            else
                            {
                                ?>
                                <td colspan="3"><input type="text" class="textbox" style="width: 589px" disabled value="<?php echo $intervencionElectro ?>"></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <tr>
                            <td><label class="labels" style="margin-top: 5px">ISO: </label></td>
                            <td><input type="text" class="textbox" disabled value="<?php echo $iso ?>"></td>
                        </tr>
                        <tr>
                            <td><label class="labels">Observaciones: </label></td>
                        </tr>
                        <tr>
                            <td colspan="4"><textarea disabled rows="5" cols="115" style="margin-left: 20px"><?php echo $observacion ?></textarea></td>
                        </tr>
                        <tr>
                            <td><label class="labels">Notas del Auditor: </label></td>
                        </tr>
                        <tr>
                            <td colspan="4"><textarea disabled rows="5" cols="115" style="margin-left: 20px"><?php echo $nota ?></textarea></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            ?>
        </form>
    </div>
</div>
</body>
</html>