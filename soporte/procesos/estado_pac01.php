<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta charset="utf-8">
    <title>MATRIX - Estado de Pacientes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="estado_pac03.js" type="text/javascript"></script>
    <style>
        .alternar:hover{ background-color:#e1edf7;}
    </style>
    <script language="javascript">
        function modificar(idRegistro,tabla)
        {
            miPopup4 = window.open("estado_pac02.php?idRegistro="+idRegistro.value+"&tabla="+tabla.value,"miwin1","width=750,height=310,top=300,left=550");
            miPopup4.focus()
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
    ?>
</head>

<body onload="mostrarop2('Divhistoria','Divnombre')" onfocus="location.reload(true)">
    <div class="container" style="margin-top: -30px">
        <div id="loginbox" style="margin-top:50px" class="">
            <div class="panel panel-info" style="width: 1300px">
                <div class="panel-heading">
                    <div class="panel-title">Soporte Matrix - Estado de Pacientes</div>
                </div>

                <div style="padding-top:10px; position: relative; min-height: 1100px" class="panel-body" >
                    <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="estado_pac01.php">
                        <table align="center">
                            <tr>
                                <td colspan="5" align="center">
                                    <h5 class="text-primary"><strong>Parametros de busqueda: </strong></h5>
                                </td>
                            </tr>
                            <tr style="background-color: #EEEEEE; border-radius: 50px 50px">
                                <td colspan="6">
                                    <div class="input-group">
                                        <label title="Archivo de Pacientes">cliame_000100</label>&nbsp;
                                        <input type="checkbox" name="cliame100" value="1" checked readonly>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label title="Archivo de Ingreso de Pacientes">cliame_000101</label>&nbsp;
                                        <input type="checkbox" name="cliame101" value="1" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label title="Tabla Unica de Pacientes">root_000036</label>&nbsp;
                                        <input type="checkbox" name="root36" value="1" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label title="Origen Historia del Paciente">root_000037</label>&nbsp;
                                        <input type="checkbox" name="root37" value="1" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label title="Medicos Tratantes por Historia">hce_000022</label>&nbsp;
                                        <input type="checkbox" name="hce22" value="1" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <label title="Formularios Firmados X Historia Ingreso">hce_000036</label>&nbsp;
                                        <input type="checkbox" name="hce36" value="1">&nbsp;&nbsp;
                                        <label title="Movimiento de Turnos">tcx_000011</label>&nbsp;
                                        <input type="checkbox" name="tcx11" value="1" checked>
                                    </div>
                                </td>
                            </tr>
                            <tr style="background-color: #EEEEEE">
                                <td colspan="6">
                                    <div class="input-group">
                                        <label title="Saldos Articulos por Pacientes">movhos_000004</label>&nbsp;
                                        <input type="checkbox" name="movhos4" value="1">&nbsp;&nbsp;
                                        <label title="Movimiento Aplicacion Medic. y Mqx">movhos_000015</label>&nbsp;
                                        <input type="checkbox" name="movhos15" value="1">&nbsp;&nbsp;
                                        <label title="Ingresos de Pacientes">movhos_000016</label>&nbsp;
                                        <input type="checkbox" name="movhos16" value="1" checked>&nbsp;&nbsp;
                                        <label title="Ubicacion de Paciente">movhos_000018</label>&nbsp;
                                        <input type="checkbox" name="movhos18" value="1" checked>&nbsp;&nbsp;
                                        <label title="Estado de Habitaciones">movhos_000020</label>&nbsp;
                                        <input type="checkbox" name="movhos20" value="1" checked>&nbsp;&nbsp;
                                        <label title="Egresos - CENSO">movhos_000033</label>&nbsp;
                                        <input type="checkbox" name="movhos33" value="1">&nbsp;&nbsp;
                                        <label title="Movimiento Insumos por Auxiliar">movhos_000228</label>&nbsp;
                                        <input type="checkbox" name="movhos228" value="1">
                                    </div>
                                </td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td align="center">
                                    <label for="selparam">Nombre &nbsp;</label>
                                    <input type="radio" name="selparam" id="selparam" value="0" onclick="mostrarop1('Divnombre','Divhistoria')">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <label>Historia e Ingreso &nbsp;</label>
                                    <input type="radio" checked name="selparam" id="selparam" value="1" onclick="mostrarop2('Divhistoria','Divnombre')">
                                </td>
                            </tr>
                        </table>

                        <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 40%" id="Divnombre">
                            <div class="input-group" style="margin: auto; border: none">
                                <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                                <span class="input-group-addon"><label for="nombre">Nombre</label></span>
                                <input id="nombre" type="text" class="form-control" style="width: 300px" name="Hnombre" value="">
                            </div>
                            <div class="input-group-addon" style="background-color: #ffffff; border: none">
                                <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                            </div>
                        </div>

                        <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 45%" id="Divhistoria">
                            <div class="input-group" style="margin-left: -50px; border: none">
                                <div class="input-group-addon" style="background-color: #ffffff; width: 70px; border: none"></div>
                                <span class="input-group-addon" style="width: 123px"><label for="historia">Historia</label></span>
                                <input id="historia" type="text" class="form-control" style="width: 200px" name="Hhistoria" value="">

                                <span class="input-group-addon"><label for="ingreso">Ingreso</label></span>
                                <input id="ingreso" type="text" class="form-control" style="width: 100px" name="Hingreso" value="">
                            </div>
                            <div class="input-group-addon" style="background-color: #ffffff; border: none">
                                <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                            </div>
                        </div>
                    </form>
                    <br><br>

                    <?php
                    $parametro = $_POST['selparam'];    $nombrePac = $_POST['Hnombre'];     $historia = $_POST['Hhistoria'];    $ingreso = $_POST['Hingreso'];
                    $cliame100 = $_POST['cliame100'];   $cliame101 = $_POST['cliame101'];   $root36 = $_POST['root36'];         $root37 = $_POST['root37'];
                    $movhos16 = $_POST['movhos16'];     $movhos18 = $_POST['movhos18'];     $movhos20 = $_POST['movhos20'];     $movhos4 = $_POST['movhos4'];
                    $hce22 = $_POST['hce22'];           $hce36 = $_POST['hce36'];           $movhos15 = $_POST['movhos15'];     $movhos228 = $_POST['movhos228'];
                    $movhos33 = $_POST['movhos33'];     $tcx11 = $_POST['tcx11'];

                    if($historia != null)
                    {
                        if($cliame100 == 1)
                        {
                            ?>
                            <label>CLIAME_000100 - [Archivo de Pacientes]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Número de HC</td>
                                        <td>Tipo Documento</td>
                                        <td>Número de Documento</td>
                                        <td>Primer Apellido</td>
                                        <td>Segundo Apellido</td>
                                        <td>Primer Nombre</td>
                                        <td>Segundo Nombre</td>
                                        <td>Paciente Activo</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null)
                                        {
                                            $query=mysql_query("select * from cliame_000100 WHERE Pachis = '$historia'");

                                            while($dato=mysql_fetch_array($query))
                                            {
                                                ?>
                                                <form id="formDATOS" method="post" action="estado_pac02.php">
                                                    <?php
                                                    $idCliame100 = $dato['id'];
                                                    $hcFind = $dato['Pachis'];

                                                    if($hcFind != null)
                                                    {
                                                        if($dato['Pacact'] == 'on')
                                                        {
                                                            ?>
                                                            <tr id="rowDATOS" class="alternar">
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <tr id="rowDATOS" class="alternar" style="background-color: #FFA283">
                                                            <?php
                                                        }
                                                        ?>
                                                            <td><?php echo $dato['Pachis'] ?></td>
                                                            <td><?php echo $dato['Pactdo']?></td>
                                                            <td><?php echo $dato['Pacdoc'] ?></td>
                                                            <td><?php echo $dato['Pacap1'] ?></td>
                                                            <td><?php echo $dato['Pacap2'] ?></td>
                                                            <td><?php echo $dato['Pacno1'] ?></td>
                                                            <td><?php echo $dato['Pacno2'] ?></td>
                                                            <td><?php echo $dato['Pacact'] ?></td>
                                                            <input type="hidden" name="idCliame100" value="<?php echo $idCliame100 ?>">
                                                            <input type="hidden" name="tabla" value="cliame_000100">
                                                            <td><button type="button" class="btn btn-default" onclick="modificar(idCliame100,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                                <?php
                                                $documentoPac = $dato['Pacdoc'];
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($cliame101 == 1)
                        {
                            ?>
                            <label>CLIAME_000101 - [Archivo de Ingreso de Pacientes]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Hora_data</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Fecha de Ingreso</td>
                                        <td>Servicio de Ingreso</td>
                                        <td>Codigo del Responsable</td>
                                        <td>Nombre del Responsable</td>
                                        <td>Intentos UNIX</td>
                                        <td>Guardado en unix</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso == null)
                                        {
                                            $query2 = mysql_query("select * from cliame_000101 WHERE Inghis = '$historia'");
                                        }
                                        elseif($historia != null and $ingreso != null)
                                        {
                                            $query2 = mysql_query("select * from cliame_000101 WHERE Inghis = '$historia' AND Ingnin = '$ingreso'");
                                        }

                                        while($dato2=mysql_fetch_array($query2))
                                        {
                                            ?>
                                            <form id="formDATOS" method="post" action="estado_pac02.php">
                                                <?php
                                                $idCliame101 = $dato2['id'];
                                                $hcFind2 = $dato2['Inghis'];

                                                if($hcFind2 != null)
                                                {
                                                    ?>
                                                    <tr id="rowDATOS" class="alternar">
                                                        <td><?php echo $dato2['Fecha_data'] ?></td>
                                                        <td><?php echo $dato2['Hora_data'] ?></td>
                                                        <td><?php echo $dato2['Inghis'] ?></td>
                                                        <td><?php echo $dato2['Ingnin']?></td>
                                                        <td><?php echo $dato2['Ingfei'] ?></td>
                                                        <td><?php echo $dato2['Ingsei'] ?></td>
                                                        <td><?php echo $dato2['Ingcem'] ?></td>
                                                        <td><?php echo $dato2['Ingent'] ?></td>
                                                        <?php
                                                        if($dato2['Ingniu'] == '20')
                                                        {
                                                            ?>
                                                            <td style="background-color: #FD9F84" align="center"><?php echo $dato2['Ingniu'] ?></td>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <td align="center"><?php echo $dato2['Ingniu'] ?></td>
                                                            <?php
                                                        }

                                                        if($dato2['Ingunx'] == 'off')
                                                        {
                                                            ?>
                                                            <td style="background-color: #FD9F84" align="center"><?php echo $dato2['Ingunx'] ?></td>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <td align="center"><?php echo $dato2['Ingunx'] ?></td>
                                                            <?php
                                                        }
                                                        ?>
                                                        <input type="hidden" name="idCliame101" value="<?php echo $idCliame101 ?>">
                                                        <input type="hidden" name="tabla" value="cliame_000101">
                                                        <td><button type="button" class="btn btn-default" onclick="modificar(idCliame101,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </form>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($root36 == 1)
                        {
                            ?>
                            <label>ROOT_000036 - [Tabla Unica de Pacientes]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Número de Documento</td>
                                        <td>Tipo Documento</td>
                                        <td>Primer Apellido</td>
                                        <td>Segundo Apellido</td>
                                        <td>Primer Nombre</td>
                                        <td>Segundo Nombre</td>
                                        <td>Fecha Nacimiento</td>
                                        <td>Sexo</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null)
                                        {
                                            $query3=mysql_query("select * from root_000036 WHERE Pacced = '$documentoPac'");

                                            while($dato3=mysql_fetch_array($query3))
                                            {
                                                ?>
                                                <form id="formDATOS" method="post" action="estado_pac02.php">
                                                    <?php
                                                    $idRoot36 = $dato3['id'];
                                                    $documentoPac = $dato3['Pacced'];

                                                    if($documentoPac != null)
                                                    {
                                                        ?>
                                                        <tr id="rowDATOS" class="alternar">
                                                            <td><?php echo $dato3['Pacced'] ?></td>
                                                            <td><?php echo $dato3['Pactid'] ?></td>
                                                            <td><?php echo $dato3['Pacap1'] ?></td>
                                                            <td><?php echo $dato3['Pacap2'] ?></td>
                                                            <td><?php echo $dato3['Pacno1'] ?></td>
                                                            <td><?php echo $dato3['Pacno2'] ?></td>
                                                            <td><?php echo $dato3['Pacnac'] ?></td>
                                                            <td><?php echo $dato3['Pacsex'] ?></td>
                                                            <input type="hidden" name="idRoot36" value="<?php echo $idRoot36 ?>">
                                                            <input type="hidden" name="tabla" value="root_000036">
                                                            <td><button type="button" class="btn btn-default" onclick="modificar(idRoot36,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($root37 == 1)
                        {
                            ?>
                            <label>ROOT_000037 - [Origen Historia del Paciente]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Número de Documento</td>
                                        <td>Tipo Documento</td>
                                        <td>Numero de Historia</td>
                                        <td>Numero de Ingreso</td>
                                        <td>Empresa Origen</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso == null)
                                        {
                                            $query4 = mysql_query("select * from root_000037 WHERE Oriced = '$documentoPac'");
                                        }
                                        elseif($historia != null and $ingreso != null)
                                        {
                                            $query4 = mysql_query("select * from root_000037 WHERE Oriced = '$documentoPac' AND Oriing = $ingreso");
                                        }

                                        while($dato4=mysql_fetch_array($query4))
                                        {
                                            ?>
                                            <form id="formDATOS" method="post" action="estado_pac02.php">
                                                <?php
                                                $idRoot37 = $dato4['id'];
                                                $documentoPac2 = $dato4['Oriced'];

                                                if($documentoPac2 != null)
                                                {
                                                    ?>
                                                    <tr id="rowDATOS" class="alternar">
                                                        <td><?php echo $dato4['Oriced'] ?></td>
                                                        <td><?php echo $dato4['Oritid'] ?></td>
                                                        <td><?php echo $dato4['Orihis'] ?></td>
                                                        <td><?php echo $dato4['Oriing'] ?></td>
                                                        <td><?php echo $dato4['Oriori'] ?></td>
                                                        <input type="hidden" name="idRoot37" value="<?php echo $idRoot37 ?>">
                                                        <input type="hidden" name="tabla" value="root_000037">
                                                        <td><button type="button" class="btn btn-default" onclick="modificar(idRoot37,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </form>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($hce22 == 1)
                        {
                            ?>
                            <label>HCE_000022 - [Medicos Tratantes por Historia]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Médico Asociado</td>
                                        <td>Tratante</td>
                                        <td>Especialidad Medico Asociado</td>
                                        <td>Ultima Conducta</td>
                                        <td>Consulta Urgencias</td>
                                        <td>Centro de costo de ingreso</td>
                                        <td>Código cubículo Asignado</td>
                                        <td>Turno</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso == null)
                                        {
                                            $query9 = mysql_query("select * from hce_000022 WHERE Mtrhis = '$historia'");
                                        }
                                        elseif($historia != null and $ingreso != null)
                                        {
                                            $query9 = mysql_query("select * from hce_000022 WHERE Mtrhis = '$historia' AND Mtring = $ingreso");
                                        }

                                        while($dato9=mysql_fetch_array($query9))
                                        {
                                            ?>
                                            <form id="formDATOS" method="post" action="estado_pac02.php">
                                                <?php
                                                $hce22 = $dato9['id'];
                                                $hcFind9 = $dato9['Mtrhis'];

                                                if($hcFind9 != null)
                                                {
                                                    ?>
                                                    <tr id="rowDATOS" class="alternar">
                                                        <td><?php echo $dato9['Fecha_data'] ?></td>
                                                        <td><?php echo $dato9['Mtrhis'] ?></td>
                                                        <td><?php echo $dato9['Mtring'] ?></td>
                                                        <td><?php echo $dato9['Mtrmed'] ?></td>
                                                        <td><?php echo $dato9['Mtrtra'] ?></td>
                                                        <td><?php echo $dato9['Mtreme'] ?></td>
                                                        <td><?php echo $dato9['Mtrcon'] ?></td>
                                                        <td><?php echo $dato9['Mtrcur'] ?></td>
                                                        <td><?php echo $dato9['Mtrcci'] ?></td>
                                                        <td><?php echo $dato9['Mtrccu'] ?></td>
                                                        <td><?php echo $dato9['Mtrtur'] ?></td>
                                                        <input type="hidden" name="idhce22" value="<?php echo $hce22 ?>">
                                                        <input type="hidden" name="tabla" value="hce_000022">
                                                        <td><button type="button" class="btn btn-default" onclick="modificar(idhce22,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </form>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($hce36 == 1)
                        {
                            ?>
                            <label>HCE_000036 - [Formularios Firmados X Historia Ingreso]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Formulario</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Usuario que Grabó</td>
                                        <td>Firmado</td>
                                        <td>Rol de Quien Graba</td>
                                        <td>Centro de Costos Graba</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso != null)
                                        {
                                            $query10 = mysql_query("select * from hce_000036 WHERE Firhis = '$historia' AND Firing = $ingreso");

                                            while($dato10=mysql_fetch_array($query10))
                                            {
                                                ?>
                                                <form id="formDATOS" method="post" action="estado_pac02.php">
                                                    <?php
                                                    $hce36 = $dato10['id'];
                                                    $hcFind10 = $dato10['Firhis'];

                                                    if($hcFind10 != null)
                                                    {
                                                        ?>
                                                        <tr id="rowDATOS" class="alternar">
                                                            <td><?php echo $dato10['Fecha_data'] ?></td>
                                                            <td><?php echo $dato10['Firpro'] ?></td>
                                                            <td><?php echo $dato10['Firhis'] ?></td>
                                                            <td><?php echo $dato10['Firing'] ?></td>
                                                            <td><?php echo $dato10['Firusu'] ?></td>
                                                            <td><?php echo $dato10['Firfir'] ?></td>
                                                            <td><?php echo $dato10['Firrol'] ?></td>
                                                            <td><?php echo $dato10['Fircco'] ?></td>
                                                            <input type="hidden" name="idhce36" value="<?php echo $hce36 ?>">
                                                            <input type="hidden" name="tabla" value="hce_000036">
                                                            <td>
                                                                <!--
                                                                <button type="button" class="btn btn-default" onclick="modificar(idhce36,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button>
                                                                -->
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <label style="color: firebrick">Debe ingresar numero de HISTORIA e INGRESO para consultar esta tabla</label>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($movhos16 == 1)
                        {
                            ?>
                            <label>MOVHOS_000016 - [Ingresos de Pacientes]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Hora_data</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Codigo Responsable</td>
                                        <td>Nombre Responsable</td>
                                        <td>Tipo de Ingreso</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso == null)
                                        {
                                            $query5 = mysql_query("select * from movhos_000016 WHERE Inghis = '$historia'");
                                        }
                                        elseif($historia != null and $ingreso != null)
                                        {
                                            $query5 = mysql_query("select * from movhos_000016 WHERE Inghis = '$historia' AND Inging = '$ingreso'");
                                        }

                                        while($dato5=mysql_fetch_array($query5))
                                        {
                                            ?>
                                            <form id="formDATOS" method="post" action="estado_pac02.php">
                                                <?php
                                                $idMovhos16 = $dato5['id'];
                                                $hcFind3 = $dato5['Inghis'];

                                                if($hcFind3 != null)
                                                {
                                                    ?>
                                                    <tr id="rowDATOS" class="alternar">
                                                        <td><?php echo $dato5['Fecha_data'] ?></td>
                                                        <td><?php echo $dato5['Hora_data'] ?></td>
                                                        <td><?php echo $dato5['Inghis'] ?></td>
                                                        <td><?php echo $dato5['Inging'] ?></td>
                                                        <td><?php echo $dato5['Ingres'] ?></td>
                                                        <td><?php echo $dato5['Ingnre'] ?></td>
                                                        <td><?php echo $dato5['Ingtip']; ?></td>
                                                        <input type="hidden" name="idMovhos16" value="<?php echo $idMovhos16 ?>">
                                                        <input type="hidden" name="tabla" value="movhos_000016">
                                                        <td><button type="button" class="btn btn-default" onclick="modificar(idMovhos16,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </form>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($movhos18 == 1)
                        {
                            ?>
                            <label>MOVHOS_000018 - [Ubicacion de Paciente]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Cco Actual</td>
                                        <td>Cco Anterior</td>
                                        <td>Habitacion Actual</td>
                                        <td>Habitacion Anterior</td>
                                        <td>Alta en Proceso</td>
                                        <td>Alta Definitiva</td>
                                        <td>Fecha Alta en Proceso</td>
                                        <td>Hora Alta en Proceso</td>
                                        <td>Fecha Alta Definitiva</td>
                                        <td>Hora Alta Definitiva</td>
                                        <td>Proceso de Traslado</td>
                                        <td>Muerte</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso == null)
                                        {
                                            $query6 = mysql_query("select * from movhos_000018 WHERE Ubihis = '$historia'");
                                        }
                                        elseif($historia != null and $ingreso != null)
                                        {
                                            $query6 = mysql_query("select * from movhos_000018 WHERE Ubihis = '$historia' AND Ubiing = '$ingreso'");
                                        }

                                        while($dato6=mysql_fetch_array($query6))
                                        {
                                            ?>
                                            <form id="formDATOS" method="post" action="estado_pac02.php">
                                                <?php
                                                $idMovhos18 = $dato6['id'];
                                                $hcFind4 = $dato6['Ubihis'];

                                                if($hcFind4 != null)
                                                {
                                                    ?>
                                                    <tr id="rowDATOS" class="alternar">
                                                        <td><?php echo $dato6['Fecha_data'] ?></td>
                                                        <td><?php echo $dato6['Ubihis'] ?></td>
                                                        <td><?php echo $dato6['Ubiing'] ?></td>
                                                        <td><?php echo $dato6['Ubisac'] ?></td>
                                                        <td><?php echo $dato6['Ubisan'] ?></td>
                                                        <td><?php echo $dato6['Ubihac'] ?></td>
                                                        <td><?php echo $dato6['Ubihan'] ?></td>
                                                        <td><?php echo $dato6['Ubialp'] ?></td>
                                                        <td><?php echo $dato6['Ubiald'] ?></td>
                                                        <td><?php echo $dato6['Ubifap'] ?></td>
                                                        <td><?php echo $dato6['Ubihap'] ?></td>
                                                        <td><?php echo $dato6['Ubifad'] ?></td>
                                                        <td><?php echo $dato6['Ubihad'] ?></td>
                                                        <td><?php echo $dato6['Ubiptr'] ?></td>
                                                        <td><?php echo $dato6['Ubimue'] ?></td>
                                                        <input type="hidden" name="idMovhos18" value="<?php echo $idMovhos18 ?>">
                                                        <input type="hidden" name="tabla" value="movhos_000018">
                                                        <td><button type="button" class="btn btn-default" onclick="modificar(idMovhos18,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </form>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($movhos20 == 1)
                        {
                            ?>
                            <label>MOVHOS_000020 - [Estado de Habitaciones]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Codigo</td>
                                        <td>Piso</td>
                                        <td>Historia Actual</td>
                                        <td>Ingreso</td>
                                        <td>Listo el Aseo</td>
                                        <td>Disponible</td>
                                        <td>Estado del Registro</td>
                                        <td>Proceso de Ocupacion</td>
                                        <td>Codigo en pantalla</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso == null)
                                        {
                                            $query7 = mysql_query("select * from movhos_000020 WHERE Habhis = '$historia'");
                                        }
                                        elseif($historia != null and $ingreso != null)
                                        {
                                            $query7 = mysql_query("select * from movhos_000020 WHERE Habhis = '$historia' AND Habing = '$ingreso'");
                                        }

                                        while($dato7=mysql_fetch_array($query7))
                                        {
                                            ?>
                                            <form id="formDATOS" method="post" action="estado_pac02.php">
                                                <?php
                                                $idMovhos20 = $dato7['id'];
                                                $hcFind5 = $dato7['Habhis'];

                                                if($hcFind5 != null)
                                                {
                                                    ?>
                                                    <tr id="rowDATOS" class="alternar">
                                                        <td><?php echo $dato7['Habcod'] ?></td>
                                                        <td><?php echo $dato7['Habcco'] ?></td>
                                                        <td><?php echo $dato7['Habhis'] ?></td>
                                                        <td><?php echo $dato7['Habing'] ?></td>
                                                        <td><?php echo $dato7['Habali'] ?></td>
                                                        <td><?php echo $dato7['Habdis'] ?></td>
                                                        <td><?php echo $dato7['Habest'] ?></td>
                                                        <td><?php echo $dato7['habpro'] ?></td>
                                                        <td><?php echo $dato7['Habcpa'] ?></td>
                                                        <input type="hidden" name="idMovhos20" value="<?php echo $idMovhos20 ?>">
                                                        <input type="hidden" name="tabla" value="movhos_000020">
                                                        <td><button type="button" class="btn btn-default" onclick="modificar(idMovhos20,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </form>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($movhos4 == 1)
                        {
                            ?>
                            <label>MOVHOS_000004 - [Saldos Articulos por Pacientes]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_Data</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Centro de costos</td>
                                        <td>Codigo del articulo</td>
                                        <td>Entrada Total Unix</td>
                                        <td>Salida Total Unix</td>
                                        <td>Entrada Aprovechamientos</td>
                                        <td>Salida Aprovechamientos</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso != null)
                                        {
                                            $query8 = mysql_query("select * from movhos_000004 WHERE Spahis = '$historia' AND Spaing = '$ingreso'");

                                            while($dato8=mysql_fetch_array($query8))
                                            {
                                                ?>
                                                <form id="formDATOS" method="post" action="estado_pac02.php">
                                                    <?php
                                                    $idMovhos4 = $dato8['id'];
                                                    $hcFind6 = $dato8['Spahis'];

                                                    if($hcFind6 != null)
                                                    {
                                                        ?>
                                                        <tr id="rowDATOS" class="alternar">
                                                            <td><?php echo $dato8['Fecha_data'] ?></td>
                                                            <td><?php echo $dato8['Spahis'] ?></td>
                                                            <td><?php echo $dato8['Spaing'] ?></td>
                                                            <td><?php echo $dato8['Spacco'] ?></td>
                                                            <td><?php echo $dato8['Spaart'] ?></td>
                                                            <td><?php echo $dato8['Spauen'] ?></td>
                                                            <td><?php echo $dato8['Spausa'] ?></td>
                                                            <td><?php echo $dato8['Spaaen'] ?></td>
                                                            <td><?php echo $dato8['Spaasa'] ?></td>
                                                            <input type="hidden" name="idMovhos4" value="<?php echo $idMovhos4 ?>">
                                                            <input type="hidden" name="tabla" value="movhos_000004">
                                                            <td><button type="button" class="btn btn-default" onclick="modificar(idMovhos4,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <label style="color: firebrick">Debe ingresar numero de HISTORIA e INGRESO para consultar esta tabla</label>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($movhos15 == 1)
                        {
                            ?>
                            <label>MOVHOS_000015 - [Movimiento Aplicacion Medic. y Mqx]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Ronda</td>
                                        <td>Codigo Articulo</td>
                                        <td>Descripcion Articulo</td>
                                        <td>Cantidad Aplicada</td>
                                        <td>Centro costos aplica</td>
                                        <td>Usuario que Aplica</td>
                                        <td>Estado</td>
                                        <td>Fecha de Aplicacion</td>
                                        <td>Unidad de fraccion</td>
                                        <td>Dosis</td>
                                        <td>Via de administracion</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso != null)
                                        {
                                            $query11 = mysql_query("select * from movhos_000015 WHERE Aplhis = '$historia' AND Apling = $ingreso");

                                            while($dato11=mysql_fetch_array($query11))
                                            {
                                                ?>
                                                <form id="formDATOS" method="post" action="estado_pac02.php">
                                                    <?php
                                                    $movhos15 = $dato11['id'];
                                                    $hcFind11 = $dato11['Aplhis'];

                                                    if($hcFind11 != null)
                                                    {
                                                        ?>
                                                        <tr id="rowDATOS" class="alternar">
                                                            <td><?php echo $dato11['Fecha_data'] ?></td>
                                                            <td><?php echo $dato11['Aplhis'] ?></td>
                                                            <td><?php echo $dato11['Apling'] ?></td>
                                                            <td><?php echo $dato11['Aplron'] ?></td>
                                                            <td><?php echo $dato11['Aplart'] ?></td>
                                                            <td><?php echo $dato11['Apldes'] ?></td>
                                                            <td><?php echo $dato11['Aplcan'] ?></td>
                                                            <td><?php echo $dato11['Aplcco'] ?></td>
                                                            <td><?php echo $dato11['Aplusu'] ?></td>
                                                            <td><?php echo $dato11['Aplest'] ?></td>
                                                            <td><?php echo $dato11['Aplfec'] ?></td>
                                                            <td><?php echo $dato11['Aplufr'] ?></td>
                                                            <td><?php echo $dato11['Apldos'] ?></td>
                                                            <td><?php echo $dato11['Aplvia'] ?></td>
                                                            <input type="hidden" name="idmovhos15" value="<?php echo $movhos15 ?>">
                                                            <input type="hidden" name="tabla" value="movhos_000015">
                                                            <!--
                                                            <td>
                                                                <button type="button" class="btn btn-default" onclick="modificar(idmovhos15,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button>
                                                            </td>
                                                            -->
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <label style="color: firebrick">Debe ingresar numero de HISTORIA e INGRESO para consultar esta tabla</label>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($movhos228 == 1)
                        {
                            ?>
                            <label>MOVHOS_000228 - [Movimiento Insumos por Auxiliar]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Hora_data</td>
                                        <td>Codigo botiquin</td>
                                        <td>Codigo auxiliar de enfermeria</td>
                                        <td>Codigo insumo</td>
                                        <td>Fecha del cargo</td>
                                        <td>Codigo del movimiento</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Centro costos paciente</td>
                                        <td>Habitacion</td>
                                        <td>Cantidad</td>
                                        <td>Usuario</td>
                                        <td>Justificacion</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso != null)
                                        {
                                            $query12 = mysql_query("select * from movhos_000228 WHERE Movhis = '$historia' AND Moving = $ingreso");

                                            while($dato12=mysql_fetch_array($query12))
                                            {
                                                ?>
                                                <form id="formDATOS" method="post" action="estado_pac02.php">
                                                    <?php
                                                    $movhos228 = $dato12['id'];
                                                    $hcFind12 = $dato12['Movhis'];

                                                    if($hcFind12 != null)
                                                    {
                                                        ?>
                                                        <tr id="rowDATOS" class="alternar">
                                                            <td><?php echo $dato12['Fecha_data'] ?></td>
                                                            <td><?php echo $dato12['Hora_data'] ?></td>
                                                            <td><?php echo $dato12['Movbot'] ?></td>
                                                            <td><?php echo $dato12['Movaux'] ?></td>
                                                            <td><?php echo $dato12['Movins'] ?></td>
                                                            <td><?php echo $dato12['Movfec'] ?></td>
                                                            <td><?php echo $dato12['Movmov'] ?></td>
                                                            <td><?php echo $dato12['Movhis'] ?></td>
                                                            <td><?php echo $dato12['Moving'] ?></td>
                                                            <td><?php echo $dato12['Movcco'] ?></td>
                                                            <td><?php echo $dato12['Movhab'] ?></td>
                                                            <td><?php echo $dato12['Movcmo'] ?></td>
                                                            <td><?php echo $dato12['Movumo'] ?></td>
                                                            <td><?php echo $dato12['Movjmo'] ?></td>
                                                            <input type="hidden" name="idmovhos228" value="<?php echo $movhos228 ?>">
                                                            <input type="hidden" name="tabla" value="movhos_000228">
                                                            <!--
                                                            <td>
                                                                <button type="button" class="btn btn-default" onclick="modificar(idmovhos15,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button>
                                                            </td>
                                                            -->
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <label style="color: firebrick">Debe ingresar numero de HISTORIA e INGRESO para consultar esta tabla</label>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($movhos33 == 1)
                        {
                            ?>
                            <label>MOVHOS_000033 - [Egresos - CENSO]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Número de HC</td>
                                        <td>Número de Ingreso</td>
                                        <td>Servicio</td>
                                        <td>Número Ingreso al Servicio</td>
                                        <td>Fecha Egreso del Servicio</td>
                                        <td>Hora Egreso del Servicio</td>
                                        <td>Tipo Egreso</td>
                                        <td>Dias Estancia en el Servicio</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($historia != null and $ingreso == null)
                                        {
                                            $query13 = mysql_query("select * from movhos_000033 WHERE Historia_clinica = '$historia'");
                                        }
                                        elseif($historia != null and $ingreso != null)
                                        {
                                            $query13 = mysql_query("select * from movhos_000033 WHERE Historia_clinica = '$historia' AND Num_ingreso = '$ingreso'");
                                        }

                                        while($dato13=mysql_fetch_array($query13))
                                        {
                                            ?>
                                            <form id="formDATOS" method="post" action="estado_pac02.php">
                                                <?php
                                                $idMovhos33 = $dato13['id'];
                                                $hcFind = $dato13['Historia_clinica'];

                                                if($hcFind != null)
                                                {
                                                    ?>
                                                    <tr id="rowDATOS" class="alternar">
                                                    <td><?php echo $dato13['Fecha_data'] ?></td>
                                                    <td><?php echo $dato13['Historia_clinica']?></td>
                                                    <td><?php echo $dato13['Num_ingreso'] ?></td>
                                                    <td><?php echo $dato13['Servicio'] ?></td>
                                                    <td><?php echo $dato13['Num_ing_serv'] ?></td>
                                                    <td><?php echo $dato13['Fecha_egre_serv'] ?></td>
                                                    <td><?php echo $dato13['Hora_egr_serv'] ?></td>
                                                    <td><?php echo $dato13['Tipo_egre_serv'] ?></td>
                                                    <td><?php echo $dato13['Dias_estan_serv'] ?></td>
                                                    <input type="hidden" name="idMovhos33" value="<?php echo $idMovhos33 ?>">
                                                    <input type="hidden" name="tabla" value="movhos_000033">
                                                    <td><button type="button" class="btn btn-default" onclick="modificar(idMovhos33,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                            </form>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }

                        if($tcx11 == 1)
                        {
                            ?>
                            <label>TCX_000011 - [Movimiento de Turnos CX]</label>
                            <form id="encabezado">
                                <table class="table" style="min-width: 1280px">
                                    <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                                    <tr>
                                        <td>Fecha_data</td>
                                        <td>Codigo Turno</td>
                                        <td>Quirofano</td>
                                        <td>Fecha Cirugia</td>
                                        <td>Fecha Solicitud de Turno</td>
                                        <td>Tipo de Documento</td>
                                        <td>Identificacion</td>
                                        <td>Historia</td>
                                        <td>Ingreso</td>
                                        <td>Nombre</td>
                                        <td>Cirugias</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if($parametro == '1')
                                    {
                                        if($documentoPac != null)
                                        {
                                            $query14=mysql_query("select * from tcx_000011 WHERE Turdoc = '$documentoPac'");

                                            while($dato14=mysql_fetch_array($query14))
                                            {
                                                ?>
                                                <form id="formDATOS" method="post" action="estado_pac02.php">
                                                    <?php
                                                    $idTcx11 = $dato14['id'];
                                                    $documentoPac = $dato14['Turdoc'];

                                                    if($documentoPac != null)
                                                    {
                                                        ?>
                                                        <tr id="rowDATOS" class="alternar">
                                                            <td><?php echo $dato14['Fecha_data'] ?></td>
                                                            <td><?php echo $dato14['Turtur'] ?></td>
                                                            <td><?php echo $dato14['Turqui'] ?></td>
                                                            <td><?php echo $dato14['Turfec'] ?></td>
                                                            <td><?php echo $dato14['Turndt'] ?></td>
                                                            <td><?php echo $dato14['Turtdo'] ?></td>
                                                            <td><?php echo $dato14['Turdoc'] ?></td>
                                                            <td><?php echo $dato14['Turhis'] ?></td>
                                                            <td><?php echo $dato14['Turnin'] ?></td>
                                                            <td><?php echo $dato14['Turnom'] ?></td>
                                                            <td><?php echo $dato14['Turcir'] ?></td>
                                                            <input type="hidden" name="idTcx11" value="<?php echo $idTcx11 ?>">
                                                            <input type="hidden" name="tabla" value="tcx_000011">
                                                            <td><button type="button" class="btn btn-default" onclick="modificar(idTcx11,tabla)" title="Modificar Registro"><span class="fa fa-book"></span></button></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>