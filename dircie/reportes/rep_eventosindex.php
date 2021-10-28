<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reporte de Eventos Adversos Serios Institucionales</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="estilosevent.css" rel="stylesheet" type="text/css">
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="calendarioevent.js" type="text/javascript"></script>
    <script src="JsProcesosevent.js" type="text/javascript"></script>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
        });
    </script>
    <style>
        .alternar:hover{ background-color:#e1edf7;}
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
    //include_once("paf/librarypaf.php");
    ?>
</head>

<body onload="window.resizeTo(1000,850);mostrarop1('idpaciente','fechas','protocolo')">
<div id="container">
    <div id="loginbox" style="margin-top:1px; width: 950px">
        <div id="panel-info">
            <div class="panel-heading">
            </div>
            <div style="padding-top:5px" class="panel-body" >

                <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="rep_eventosindex.php">
                    <table align="center">
                        <tr>
                            <td colspan="5" align="center">
                                <h5 class="text-primary"><strong>Parametro de busqueda: </strong></h5>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-group">
                                    <label>ID Paciente &nbsp;</label><input type="radio" checked name="selparam" id="selparam" value="0" onclick="mostrarop1('idpaciente','fechas','protocolo')">
                                </div>
                            </td>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                            <td>
                                <div class="input-group">
                                    <label>Fecha Notificacion &nbsp;</label><input type="radio" name="selparam" id="selparam" value="1" onclick="mostrarop2('idpaciente','fechas','protocolo')">
                                </div>
                            </td>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                            <td>
                                <div class="input-group">
                                    <label>Codigo Protocolo &nbsp;</label><input type="radio" name="selparam" id="selparam" value="2" onclick="mostrarop3('idpaciente','fechas','protocolo')">
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 40%" id="idpaciente">
                        <div class="input-group" style="margin: auto; border: none">
                            <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                            <span class="input-group-addon"><label>ID Paciente</label></span>
                            <input id="habitacion" type="text" class="form-control" style="width: 200px" name="IdPaciente" value="">
                        </div>
                        <div class="input-group-addon" style="background-color: #ffffff; border: none">
                            <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                        </div>
                    </div>

                    <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 80%" id="fechas">
                        <div class="input-group" style="margin: auto; border: none">
                            <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                            <span class="input-group-addon"><label>Fecha Inicial</label></span>
                            <input id="datepicker1" type="text" class="form-control" style="width: 200px" name="fechai" value="">

                            <span class="input-group-addon"><label>Fecha Final</label></span>
                            <input id="datepicker2" type="text" class="form-control" style="width: 200px" name="fechaf" value="">
                        </div>
                        <div class="input-group-addon" style="background-color: #ffffff; border: none">
                            <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                        </div>
                    </div>

                    <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 40%" id="protocolo">
                        <div class="input-group" style="margin: auto; border: none">
                            <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                            <span class="input-group-addon"><label>Codigo Protocolo</label></span>
                            <input id="CodProtocolo" type="text" class="form-control" style="width: 200px" name="CodProtocolo" value="">
                        </div>
                        <div class="input-group-addon" style="background-color: #ffffff; border: none">
                            <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                        </div>
                    </div>
                </form>

                <br><br>

                <form id="selector" name="selector">
                    <table class="table">
                        <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                        <tr>
                            <td>ID Paciente</td>
                            <td>Fecha Notificacion al Patrocinador del Reporte</td>
                            <td>Codigo Protocolo</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $parametro = $_POST['selparam'];
                        $IdPaciente = $_POST['IdPaciente'];
                        $fechai = $_POST['fechai'];
                        $fechaf = $_POST['fechaf'];
                        $CodProtocolo = $_POST['CodProtocolo'];
                        $fechaActual = date('d-m-Y');

                        if($parametro == null)
                        {
                            $query=mysql_queryV("select * from dircie_000012 ORDER BY Fecha_data ASC");
                        }
                        if($parametro == '0')
                        {
                            $query=mysql_queryV("select * from dircie_000012 WHERE Dcidpac = '$IdPaciente'");
                        }
                        if($parametro == '1')
                        {
                            if($fechai != null and $fechaf != null)
                            {
                                $query=mysql_queryV("select * from dircie_000012 WHERE Dcfecnotpat BETWEEN '$fechai' AND '$fechaf'");
                            }
                            elseif($fechai != null and $fechaf == null)
                            {
                                $query=mysql_queryV("select * from dircie_000012 WHERE Dcfecnotpat BETWEEN '$fechai' AND '$fechaActual'");
                            }
                        }
                        if($parametro == '2')
                        {
                            $query=mysql_queryV("select * from dircie_000012 WHERE Dccodpro = '$CodProtocolo'");
                        }
                        while($dato=mysql_fetch_array($query))
                        {
                            ?>
                            <form id="formDATOS" method="post" action="rep_eventosindex.php">
                                <tr id="rowDATOS" class="alternar">
                                    <td><?php echo $dato['Dcidpac']?></td>
                                    <td><?php echo $dato['Dcfecnotpat']?></td>
                                    <td><?php echo $dato['Dccodpro'] ?></td>
                                    <td><input type="image" id="btnVer" src="/matrix/images/medical/paf/selecPaf.png" width="20" height="20" title="Ver Informe"></td>
                                </tr>
                                <input type="hidden" name="selparam" value="<?php echo $parametro ?>">
                                <input type="hidden" name="IdPaciente" value="<?php echo $IdPaciente ?>">
                                <input type="hidden" name="fechai" value="<?php echo $fechai ?>">
                                <input type="hidden" name="fechaf" value="<?php echo $fechaf ?>">
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
            $consultaRegistro=mysql_queryV("select * from dircie_000012 WHERE id = '$idR'");
            $datoRegistro=mysql_fetch_array($consultaRegistro);
            $fechaEmision = date('Y-m-d');
            $fechaEmision = explode('-',$fechaEmision);
            $fechaEmision = "$fechaEmision[2]-$fechaEmision[1]-$fechaEmision[0]";
            $Dcceninvrep1 = $datoRegistro['Dcceninvrep'];
            $Dcceninvrep = explode("-", $Dcceninvrep1); //Centro de Investigacion Reportante
            $Dcfecnotpat = $datoRegistro['Dcfecnotpat']; //Fecha Notificacion al Patrocinador de éste reporte
            $Dcfecnotpat = explode("-",$Dcfecnotpat);
            $Dcfecnotpat = "$Dcfecnotpat[2]-$Dcfecnotpat[1]-$Dcfecnotpat[0]";
            $Dcfecsitent = $datoRegistro['Dcfecsitent']; //Fecha en el que el sitio se entero de éste reporte
            $Dcfecsitent = explode("-",$Dcfecsitent);
            $Dcfecsitent = "$Dcfecsitent[2]-$Dcfecsitent[1]-$Dcfecsitent[0]";
            $Dctitproinv = $datoRegistro['Dctitproinv']; //Titulo del Protocolo de Investigacion
            $Dccodinv = $datoRegistro['Dccodinv']; //Codigo Invima
            $Dccodpro = $datoRegistro['Dccodpro']; //Codigo Protocolo
            $Dcpatrocina = $datoRegistro['Dcpatrocina']; //Patrocinador
            $Dcdisest = $datoRegistro['Dcdisest']; //Diseño del Estudio
            $Dcidpac = $datoRegistro['Dcidpac']; //Id Paciente
            $Dcedad = $datoRegistro['Dcedad']; //Edad
            $DcedadO = $datoRegistro['Dcedad1'];
            $Dcedad1 = explode("-",$DcedadO); //Años - Meses - Dias - Horas
            $Dcsexo1 = $datoRegistro['Dcsexo'];
            $Dcsexo = explode("-",$Dcsexo1); //Sexo
            $Dccie10 = $datoRegistro['Dccie10']; //Diagnostico del Evento Adverso CIE10
            $Dcfecineas = $datoRegistro['Dcfecineas']; //Fecha Inicial EAS
            $Dcfecineas = explode("-",$Dcfecineas);
            $Dcfecineas = "$Dcfecineas[2]-$Dcfecineas[1]-$Dcfecineas[0]";
            if($Dcfecineas == '00-00-0000'){$Dcfecineas = 'NO APLICA';}
            $Dctiprep1 = $datoRegistro['Dctiprep'];
            $Dctiprep = explode("-",$Dctiprep1); //Tipo de Reporte
            $Dcnumseg = $datoRegistro['Dnumseg']; //Numero Seguimiento
            if($Dcnumseg == null){$Dcnumseg = 'NO APLICA';}
            $Dcfecfineas = $datoRegistro['Dcfecfineas']; //Fecha Final EAS
            $Dcfecfineas = explode("-",$Dcfecfineas);
            $Dcfecfineas = "$Dcfecfineas[2]-$Dcfecfineas[1]-$Dcfecfineas[0]";
            if($Dcfecfineas == '00-00-0000'){$Dcfecfineas = 'NO APLICA';}
			$Dcestdiag1 = $datoRegistro['Dcestdiag'];
            $Dcestdiag = explode("-",$Dcestdiag1); //Estado del Diagnostico
            $Dcdeseveadv = $datoRegistro['Dcdeseveadv']; //Descripcion EAS
            $Dccriser1 = $datoRegistro['Dccriser'];
            $Dccriser = explode("-",$Dccriser1); //Criterio de Seriedad
            $Dcaccinv1 = $datoRegistro['Dcaccinv'];
            $Dcaccinv = explode("-",$Dcaccinv1); //Accion tomada por el Investigador
            $Dcevodes1 = $datoRegistro['Dcevodes'];
            $Dcevodes = explode("-",$Dcevodes1); //Evolucion y/o Desenlace
            $Dceasmaninv1 = $datoRegistro['Dceasmaninv'];
            $Dceasmaninv = explode("-",$Dceasmaninv1); //EAS Listado en el Manual del Investigador
            $Dcdespro1 = $datoRegistro['Dcdespro'];
            $Dcdespro = explode("-",$Dcdespro1); //Se presento Desviacion al Protocolo
            $Dcromcie1 = $datoRegistro['Dcromcie'];
            $Dcromcie = explode("-",$Dcromcie1); //Se Rompio el Ciego
            $Dcromciedes1 = $datoRegistro['Dcromciedes'];
            $Dcromciedes = explode("-",$Dcromciedes1); //Si se rompio el ciego, Cual es el Brazo del Sujeto
            $Dcnomcometiinv1 = $datoRegistro['Dcnomcometiinv'];
            $Dcnomcometiinv = explode("-",$Dcnomcometiinv1); //Nombre del Comité de Etica en Investigacion (CEI)
            $Dcfecnotcei = $datoRegistro['Dcfecnotcei']; //Fecha de Notificacion al CEI
            $Dcfecnotcei = explode("-",$Dcfecnotcei);
            $Dcfecnotcei = "$Dcfecnotcei[2]-$Dcfecnotcei[1]-$Dcfecnotcei[0]";
            $Dccrisev1 = $datoRegistro['Dccrisev'];
            $Dccrisev = explode("-",$Dccrisev1); //Criterio de Severidad
			$Dcnomproinv = $datoRegistro['Dcnomproinv']; //Nombre del Producto de Investigacion
            $Dcnumlot = $datoRegistro['Dcnumlot']; //No de Lote
            $Dcfecven = $datoRegistro['Dcfecven']; //Fecha de Vencimiento
            $Dcfecven = explode("-",$Dcfecven);
            $Dcfecven = "$Dcfecven[2]-$Dcfecven[1]-$Dcfecven[0]";
            if($Dcfecven == '00-00-0000'){$Dcfecven = 'NO APLICA';}
            $Dcdosfre = $datoRegistro['Dcdosfre']; //Dosis / Frecuencia
            $Dcnumdos = $datoRegistro['Dcnumdos']; //Numero de Dosis Recibidas
            $Dcfecinadm = $datoRegistro['Dcfecinadm']; //Fecha de Inicio de Administracion
            $Dcfecinadm = explode("-",$Dcfecinadm);
            $Dcfecinadm = "$Dcfecinadm[2]-$Dcfecinadm[1]-$Dcfecinadm[0]";
            if($Dcfecinadm == '00-00-0000'){$Dcfecinadm = 'NO APLICA';}
            $Dcfecteradm = $datoRegistro['Dcfecteradm']; //Fecha de Terminacion de Administracion
            $Dcfecteradm = explode("-",$Dcfecteradm);
            $Dcfecteradm = "$Dcfecteradm[2]-$Dcfecteradm[1]-$Dcfecteradm[0]";
            if($Dcfecteradm == '00-00-0000'){$Dcfecteradm = 'NO APLICA';}
            $Dcsusproinv1 = $datoRegistro['Dcsusproinv'];
            $Dcsusproinv = explode("-",$Dcsusproinv1); //Se suspendio el Producto de Investigacion
            $Dccauinv1 = $datoRegistro['Dccauinv'];
            $Dccauinv = explode("-",$Dccauinv1); //Causalidad determinada por el Investigador
            $Dcreveprod1 = $datoRegistro['Dcreveprod'];
            $Dcreveprod = explode("-",$Dcreveprod1); //Relacion causal entre el evento y el producto de investigacion
			$Dcreveproc1 = $datoRegistro['Dcreveproc'];
            $Dcreveproc = explode("-",$Dcreveproc1); //Relacion causal entre el evento y el proceso de investigacion
			$Dccie101 = $datoRegistro['Dccie101']; //CIE-10
            $Dcpatcie101 = $datoRegistro['Dcpatcie101']; //1. Patologia de Base CIE-10
            $Dcfecdx1 = $datoRegistro['Dcfecdx1']; //1. Fecha Dx
            $Dcfecdx1 = explode("-",$Dcfecdx1);
            $Dcfecdx1 = "$Dcfecdx1[2]-$Dcfecdx1[1]-$Dcfecdx1[0]";
            if($Dcfecdx1 == '00-00-0000'){$Dcfecdx1 = 'DESC';}
            $Dcactin1 = $datoRegistro['Dcactin1']; //1. Activa / Inactiva
            $Dctrarec1 = $datoRegistro['Dctrarec1']; //1. Tratamiento Recibo
            $Dcpatcie102 = $datoRegistro['Dcpatcie102']; //2. Patologia de Base CIE-10
            $Dcfecdx2 = $datoRegistro['Dcfecdx2']; //2. Fecha Dx
            $Dcfecdx2 = explode("-",$Dcfecdx2);
            $Dcfecdx2 = "$Dcfecdx2[2]-$Dcfecdx2[1]-$Dcfecdx2[0]";
            if($Dcfecdx2 == '00-00-0000'){$Dcfecdx2 = 'DESC';}
            $Dcactin2 = $datoRegistro['Dcactin2']; //2. Activa / Inactiva
            $Dctrarec2 = $datoRegistro['Dctrarec2']; //2. Tratamiento Recibo
            $Dcpatcie103 = $datoRegistro['Dcpatcie103']; //3. Patologia de Base CIE-10
            $Dcfecdx3 = $datoRegistro['Dcfecdx3']; //3. Fecha Dx
            $Dcfecdx3 = explode("-",$Dcfecdx3);
            $Dcfecdx3 = "$Dcfecdx3[2]-$Dcfecdx3[1]-$Dcfecdx3[0]";
            if($Dcfecdx3 == '00-00-0000'){$Dcfecdx3 = 'DESC';}
            $Dcactin3 = $datoRegistro['Dcactin3']; //3. Activa / Inactiva
            $Dctrarec3 = $datoRegistro['Dctrarec3']; //3. Tratamiento Recibo
            $Dcpatcie104 = $datoRegistro['Dcpatcie104']; //4. Patologia de Base CIE-10
            $Dcfecdx4 = $datoRegistro['Dcfecdx4']; //4. Fecha Dx
            $Dcfecdx4 = explode("-",$Dcfecdx4);
            $Dcfecdx4 = "$Dcfecdx4[2]-$Dcfecdx4[1]-$Dcfecdx4[0]";
            if($Dcfecdx4 == '00-00-0000'){$Dcfecdx4 = 'DESC';}
            $Dcactin4 = $datoRegistro['Dcactin4']; //4. Activa / Inactiva
            $Dctrarec4 = $datoRegistro['Dctrarec4']; //4. Tratamiento Recibo
            $Dcpatcie105 = $datoRegistro['Dcpatcie105']; //5. Patologia de Base CIE-10
            $Dcfecdx5 = $datoRegistro['Dcfecdx5']; //5. Fecha Dx
            $Dcfecdx5 = explode("-",$Dcfecdx5);
            $Dcfecdx5 = "$Dcfecdx5[2]-$Dcfecdx5[1]-$Dcfecdx5[0]";
            if($Dcfecdx5 == '00-00-0000'){$Dcfecdx5 = 'DESC';}
            $Dcactin5 = $datoRegistro['Dcactin5']; //5. Activa / Inactiva
            $Dctrarec5 = $datoRegistro['Dctrarec5']; //5. Tratamiento Recibo
            $Dcpatcie106 = $datoRegistro['Dcpatcie106']; //6. Patologia de Base CIE-10
            $Dcfecdx6 = $datoRegistro['Dcfecdx6']; //6. Fecha Dx
            $Dcfecdx6 = explode("-",$Dcfecdx6);
            $Dcfecdx6 = "$Dcfecdx6[2]-$Dcfecdx6[1]-$Dcfecdx6[0]";
            if($Dcfecdx6 == '00-00-0000'){$Dcfecdx6 = 'DESC';}
            $Dcactin6 = $datoRegistro['Dcactin6']; //6. Activa / Inactiva
            $Dctrarec6 = $datoRegistro['Dctrarec6']; //6. Tratamiento Recibo
            $Dcnominv = $datoRegistro['Dcnominv']; //Nombre del Investigador Principal
            $Dcnominvrepea = $datoRegistro['Dcnominvrepea']; //Nombre del Investigador Reportante del EA
			$Dccodigo = $datoRegistro['Dccodigo']; //Codigo formulario EA
			$Dcversion = $datoRegistro['Dcversion']; //Version formulario EA
            ?>
                <div style="width: 900px" class="table-bordered">
                    <table style="border: groove; width: 900px">
                        <tr>
                            <td align="center" style="border: groove; width: 20%">
                                <input type="image" id="btnVer" src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80">
                            </td>
                            <td align="center" style="border: groove; width: 55%">
                                <b>FORMATO DE REPORTE DE EVENTOS<br>ADVERSOS SERIOS INSTITUCIONALES</b>
                            </td>
                            <td style="border: groove; width: 35%">
                                <table>
                                    <?php
										echo "<tr>";
										if ($Dccodigo == 'NO APLICA'){
											echo "<td>Código: F-EC-7</td>";
										}else{
											echo "<td>Código:".$Dccodigo."</td>";
										}
										echo "</tr>";
										echo "<tr>";
										if ($Dcversion == 'NO APLICA'){
											echo "<td>Versión: 01</td>";
										}else{
											echo "<td>Versión:".$Dcversion."</td>";
										}
										echo "</tr>";
									?>
                                    <tr>
                                        <td>Página: 1</td>
                                    </tr>
                                    <tr>
                                        <td>Fecha de Emisión: <?php echo $fechaEmision; ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="2;" align="center">
                                <b>1. INFORMACION DEL REPORTANTE</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 50%">
                                <b>Centro de Investigación Reportante</b>
                            </td>
                            <td style="border: groove; width: 50%">
                                <?php echo $Dcceninvrep[1] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove">
                                <b>Fecha Notificación al Patrocinador del Reporte</b>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dcfecnotpat ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove">
                                <b>Fecha en el que el sitio se enteró del reporte</b>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dcfecsitent ?>
                            </td>
                        </tr>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="3;" align="center">
                                <b>2. INFORMACION DEL ESTUDIO CLINICO</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <b>Titulo del Protocolo de Investigación</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3"><?php echo $Dctitproinv ?></td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 25%">
                                <b>Código Invima</b>
                            </td>
                            <td style="border: groove; width: 25%">
                                <b>Código Protocolo</b>
                            </td>
                            <td style="border: groove; width: 50%">
                                <b>Patrocinador</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 25%">
                                <?php echo $Dccodinv ?>
                            </td>
                            <td style="border: groove; width: 25%">
                                <?php echo $Dccodpro ?>
                            </td>
                            <td style="border: groove; width: 50%">
                                <?php echo $Dcpatrocina ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <b>Diseño del Estudio</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3"><?php echo $Dcdisest ?></td>
                        </tr>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="6;" align="center">
                                <b>3. INFORMACION DEL PACIENTE</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 16%">
                                <b>Id Paciente</b>
                            </td>
                            <td style="border: groove; width: 16%">
                                <?php echo $Dcidpac ?>
                            </td>
                            <td style="border: groove; width: 16%">
                                <b>Edad</b>
                            </td>
                            <td style="border: groove; width: 16%">
                                <?php echo $Dcedad ?>&nbsp;<?php echo $Dcedad1[1] ?>
                            </td>
                            <td style="border: groove; width: 16%">
                                <b>Sexo</b>
                            </td>
                            <td style="border: groove; width: 16%">
                                <?php echo $Dcsexo[1] ?>
                            </td>
                        </tr>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="6;" align="center">
                                <b>4. INFORMACION DEL EVENTO ADVERSO SERIO</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 33%">
                                <b>Diagnóstico del Evento Adverso CIE-10</b>
                            </td>
                            <td style="border: groove; width: 67%" colspan="4">
                                <?php echo $Dccie10 ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove">
                                <b>Fecha Inicial EAS</b>
                            </td>
                            <td style="border: groove">
                                <b>Tipo de Reporte</b>
                            </td>
                            <td style="border: groove">
                                <b>Número Seguimiento</b>
                            </td>
                            <td style="border: groove">
                                <b>Fecha Final EAS</b>
                            </td>
							<td style="border: groove">
                                <b>Estado Diagnostico</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove">
                                <?php echo $Dcfecineas ?>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dctiprep[1] ?>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dcnumseg ?>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dcfecfineas ?>
                            </td>
							<td style="border: groove">
                                <?php echo $Dcestdiag[1] ?>
                            </td>
                        </tr>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="6;" align="center">
                                <b>5. DESCRIPCION DEL EVENTO ADVERSO SERIO</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="5">
                                <?php echo $Dcdeseveadv ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 50%" colspan="3">
                                <b>Criterio de Seriedad:</b>
                            </td>
                            <td style="border: groove; width: 50%" colspan="2">
                                <?php echo $Dccriser[1] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <b>Acción Tomada por el Investigador:</b>
                            </td>
                            <td style="border: groove" colspan="2">
                                <?php echo $Dcaccinv[1] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <b>Evolución y/o Desenlace:</b>
                            </td>
                            <td style="border: groove" colspan="2">
                                <?php echo $Dcevodes[1] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <b>EAS Serio Listado en el Manual del Investigador:</b>
                            </td>
                            <td style="border: groove" colspan="2">
                                <?php echo $Dceasmaninv[1] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <b>Se Presentó Desviación al Protocolo?:</b>
                            </td>
                            <td style="border: groove" colspan="2">
                                <?php echo $Dcdespro[1] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 20%">
                                <b>Se Rompió el Ciego?</b>
                            </td>
                            <td style="border: groove; width: 18%">
                                <?php echo $Dcromcie[1] ?>
                            </td>
                            <td style="border: groove; width: 30%" colspan="2">
                                <b>Si se Rompió el Ciego, cual es el brazo del sujeto?</b>
                            </td>
                            <td style="border: groove; width: 20%">
                                <?php echo $Dcromciedes[1] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; background-color: lightgray" colspan="5">
                                &nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 60%" colspan="3" align="center">
                                <b>Nombre del Comité de Ética en Investigacion (CEI)</b>
                            </td>
                            <td style="border: groove" colspan="1" align="center">
                                <b>Fecha de Notificación al CEI:</b>
                            </td>
							<td style="border: groove" colspan="1" align="center">
                                <b>Criterio Severidad:</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <?php echo $Dcnomcometiinv[1] ?>
                            </td>
                            <td style="border: groove" colspan="1">
                                <?php echo $Dcfecnotcei ?>
                            </td>
							<td style="border: groove" colspan="1">
                                <?php echo $Dccrisev[1] ?>
                            </td>
                        </tr>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="6;" align="center">
                                <b>6. INFORMACION DEL PRODUCTO DE INVESTIGACION</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="4">
                                <b>Nombre del Producto de Investigación</b>
                            </td>
                            <td style="border: groove">
                                <b>No de Lote</b>
                            </td>
                            <td style="border: groove">
                                <b>Fecha de Vencimiento</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="4">
                                <?php echo $Dcnomproinv ?>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dcnumlot ?>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dcfecven ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove">
                                <b>Dosis/Frecuencia</b>
                            </td>
                            <td style="border: groove">
                                <b>Num Dosis Recibidas</b>
                            </td>
                            <td style="border: groove" colspan="3">
                                <b>Fecha de Inicio de Administración</b>
                            </td>
                            <td style="border: groove" colspan="2">
                                <b>Fecha de Terminación de Administración</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove">
                                <?php echo $Dcdosfre ?>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dcnumdos ?>
                            </td>
                            <td style="border: groove" colspan="3">
                                <?php echo $Dcfecinadm ?>
                            </td>
                            <td style="border: groove" colspan="2">
                                <?php echo $Dcfecteradm ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <b>Se Suspendió el Producto de Investigación</b>
                            </td>
                            <td style="border: groove" colspan="3">
                                <?php echo $Dcsusproinv[1] ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove" colspan="3">
                                <b>Causalidad Determinada por el Investigador</b>
                            </td>
                            <td style="border: groove" colspan="3">
                                <?php echo $Dccauinv[1] ?>
                            </td>
                        </tr>
						<tr>
                            <td style="border: groove; width: 65%" colspan="3">
                                <b>Relacion Causal Evento y Producto de Investigación</b>
                            </td>
                            <td style="border: groove; width: 35%" colspan="3">
                                <?php echo $Dcreveprod[1] ?>
                            </td>
                        </tr>
						<tr>
                            <td style="border: groove; width: 65%" colspan="3">
                                <b>Relacion Causal Evento y Proceso de Investigación</b>
                            </td>
                            <td style="border: groove; width: 35%" colspan="3">
                                <?php echo $Dcreveproc[1] ?>
                            </td>
                        </tr>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="6;" align="center">
                                <b>7. DIAGNOSTICO DE PATOLOGIA DE BASE</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 17%">
                                <b>CIE-10</b>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dccie101 ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; background-color: lightgray" colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="6;" align="center">
                                <b>8. ANTECEDENTES DE IMPORTANCIA EN EL SUJETO</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 35%">
                                <b>Patologias de Base CIE - 10</b>
                            </td>
                            <td style="border: groove; width: 15%">
                                <b>Fecha Dx</b>
                            </td>
                            <td style="border: groove; width: 15%">
                                <b>Activa/Inactiva</b>
                            </td>
                            <td style="border: groove; width: 35%">
                                <b>Tratamiento Recibo</b>
                            </td>
                        </tr>
                        <?php
                        if($Dcpatcie101 != 'NO APLICA')
                        {
                            ?>
                            <tr>
                                <td style="border: groove">
                                    <?php echo $Dcpatcie101 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcfecdx1 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcactin1 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dctrarec1 ?>
                                </td>
                            </tr>
                            <?php
                        }
                        if($Dcpatcie102 != 'NO APLICA')
                        {
                            ?>
                            <tr>
                                <td style="border: groove">
                                    <?php echo $Dcpatcie102 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcfecdx2 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcactin2 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dctrarec2 ?>
                                </td>
                            </tr>
                            <?php
                        }
                        if($Dcpatcie103 != 'NO APLICA')
                        {
                            ?>
                            <tr>
                                <td style="border: groove">
                                    <?php echo $Dcpatcie103 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcfecdx3 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcactin3 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dctrarec3 ?>
                                </td>
                            </tr>
                            <?php
                        }
                        if($Dcpatcie104 != 'NO APLICA')
                        {
                            ?>
                            <tr>
                                <td style="border: groove">
                                    <?php echo $Dcpatcie104 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcfecdx4 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcactin4 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dctrarec4 ?>
                                </td>
                            </tr>
                            <?php
                        }
                        if($Dcpatcie105 != 'NO APLICA')
                        {
                            ?>
                            <tr>
                                <td style="border: groove">
                                    <?php echo $Dcpatcie105 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcfecdx5 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcactin5 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dctrarec5 ?>
                                </td>
                            </tr>
                            <?php
                        }
                        if($Dcpatcie106 != 'NO APLICA')
                        {
                            ?>
                            <tr>
                                <td style="border: groove">
                                    <?php echo $Dcpatcie106 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcfecdx6 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dcactin6 ?>
                                </td>
                                <td style="border: groove">
                                    <?php echo $Dctrarec6 ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>

                    <table style="border: groove; width: 900px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="6;" align="center">
                                <b>9. INFORMACION DEL REPORTANTE</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; width: 50%">
                                <b>Nombre del Investigador Principal</b>
                            </td>
                            <td style="border: groove">
                                <?php echo  $Dcnominv ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove">
                                <b>Nombre del Investigador Reportante del Evento Adverso</b>
                            </td>
                            <td style="border: groove">
                                <?php echo $Dcnominvrepea ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="border: groove; background-color: lightgray" colspan="2">
                                &nbsp;
                            </td>
                        </tr>
                    </table>
                </div>

                <div style="margin-top: 20px">
                    <table>
                        <tr>
                            <td><input type="image" id="imprimir" title="IMPRIMIR" onclick="imprimir()" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25" style="margin-left: 440px"></td>
                        </tr>
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