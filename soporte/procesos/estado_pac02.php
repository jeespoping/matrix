<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
<link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
<style>
    .alternar:hover{ background-color:#e1edf7;}
</style>

<?php
include_once("conex.php");
$tabla = $_GET['tabla'];
$idRegistro = $_GET['idRegistro'];


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

if($tabla == 'cliame_000100')
{
    $query = mysql_query("select * from cliame_000100 where id = '$idRegistro'");
    while($dato = mysql_fetch_array($query))
    {
        $historia = $dato['Pachis'];
        $tipodoc = $dato['Pactdo'];
        $numdoc = $dato['Pacdoc'];
        $primapel = $dato['Pacap1'];
        $segapel = $dato['Pacap2'];
        $primnom = $dato['Pacno1'];
        $segnom = $dato['Pacno2'];
        $pacact = $dato['Pacact'];
        $id = $dato['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <table>
                    <tr class="alternar">
                        <td><label>Número de HC : </label></td>
                        <td><input type="text" style="border: none" name="historia" value="<?php echo $historia ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Tipo Documento : </label></td>
                        <td>
                            <select name="tipodoc" style="width: 150px">
                                <option>CC</option>
                                <option>CE</option>
                                <option>PA</option>
                                <option>TI</option>
                                <option>RC</option>
                                <option>MS</option>
                                <option selected><?php echo $tipodoc ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Número de Documento : </label></td>
                        <td><input type="text" style="border: none" name="numdoc" value="<?php echo $numdoc  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Primer Apellido : </label></td>
                        <td><input type="text" style="border: none" name="primapel" value="<?php echo $primapel  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Segundo Apellido : </label></td>
                        <td><input type="text" style="border: none" name="segapel" value="<?php echo $segapel  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Primer Nombre : </label></td>
                        <td><input type="text" style="border: none" name="primnom" value="<?php echo $primnom  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Segundo Nombre : </label></td>
                        <td><input type="text" style="border: none" name="segnom" value="<?php echo $segnom  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Paciente Activo : </label></td>
                        <td>
                            <select name="pacact" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $pacact ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="cliame100">
                <input type="hidden" name="id" value="<?php echo $id ?>">
                <input type="submit" class="btn btn-primary" value="Actualizar">
            </form>
        </div>
        <?php
        $accion = $_POST['accion'];     $historia = $_POST['historia']; $tipodoc = $_POST['tipodoc'];
        $numdoc = $_POST['numdoc'];     $primapel = $_POST['primapel']; $segapel = $_POST['segapel'];
        $primnom = $_POST['primnom'];   $segnom = $_POST['segnom'];     $pacact = $_POST['pacact'];
        $id = $_POST['id'];

        if($accion == 'cliame100')
        {
            $queryCliame100 = mysql_query("update cliame_000100 set Pachis='$historia', Pactdo='$tipodoc', Pacdoc='$numdoc',
                                       Pacap1='$primapel', Pacap2='$segapel', Pacno1='$primnom', Pacno2='$segnom', Pacact='$pacact'
                                       WHERE id = '$id'");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'cliame_000101')
{
    $query2 = mysql_query("select * from cliame_000101 WHERE id = '$idRegistro'");
    while($dato2 = mysql_fetch_array($query2))
    {
        $Medico = $dato2['Medico']; $Fecha_data = $dato2['Fecha_data']; $Hora_data = $dato2['Hora_data'];
        $Inghis = $dato2['Inghis']; $Ingnin = $dato2['Ingnin']; $Ingfei = $dato2['Ingfei']; $Inghin = $dato2['Inghin']; $Ingsei = $dato2['Ingsei']; $Ingtin = $dato2['Ingtin'];
        $Ingcai = $dato2['Ingcai']; $Ingtpa = $dato2['Ingtpa']; $Ingcem = $dato2['Ingcem']; $Ingent = $dato2['Ingent']; $Ingord = $dato2['Ingord']; $Ingpol = $dato2['Ingpol'];
        $Ingnco = $dato2['Ingnco']; $Ingdie = $dato2['Ingdie']; $Ingtee = $dato2['Ingtee']; $Ingtar = $dato2['Ingtar']; $Ingusu = $dato2['Ingusu']; $Inglug = $dato2['Inglug'];
        $Ingdig = $dato2['Ingdig']; $Ingdes = $dato2['Ingdes']; $Ingpla = $dato2['Ingpla']; $Ingfha = $dato2['Ingfha']; $Ingnpa = $dato2['Ingnpa']; $Ingcac = $dato2['Ingcac'];
        $Ingpco = $dato2['Ingpco']; $Inghoa = $dato2['Inghoa']; $Ingcla = $dato2['Ingcla']; $Ingvre = $dato2['Ingvre']; $Ingunx = $dato2['Ingunx']; $Ingmei = $dato2['Ingmei'];
        $Ingtut = $dato2['Ingtut']; $Ingniu = $dato2['Ingniu']; $Ingfiu = $dato2['Ingfiu']; $Inghiu = $dato2['Inghiu']; $id2 = $dato2['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <input type="hidden" name="Medico" value="<?php echo $Medico ?>"><input type="hidden" name="Inghin" value="<?php echo $Inghin ?>"><input type="hidden" name="Ingtin" value="<?php echo $Ingtin ?>">
                <input type="hidden" name="Ingcai" value="<?php echo $Ingcai ?>"><input type="hidden" name="Ingtpa" value="<?php echo $Ingtpa ?>"><input type="hidden" name="Ingord" value="<?php echo $Ingord ?>">
                <input type="hidden" name="Ingpol" value="<?php echo $Ingpol ?>"><input type="hidden" name="Ingdie" value="<?php echo $Ingdie ?>"><input type="hidden" name="Ingtee" value="<?php echo $Ingtee ?>">
                <input type="hidden" name="Ingnco" value="<?php echo $Ingnco ?>"><input type="hidden" name="Ingtar" value="<?php echo $Ingtar ?>"><input type="hidden" name="Ingusu" value="<?php echo $Ingusu ?>">
                <input type="hidden" name="Inglug" value="<?php echo $Inglug ?>"><input type="hidden" name="Ingdes" value="<?php echo $Ingdes ?>"><input type="hidden" name="Ingpla" value="<?php echo $Ingpla ?>">
                <input type="hidden" name="Ingdig" value="<?php echo $Ingdig ?>"><input type="hidden" name="Ingfha" value="<?php echo $Ingfha ?>"><input type="hidden" name="Ingnpa" value="<?php echo $Ingnpa ?>">
                <input type="hidden" name="Ingcac" value="<?php echo $Ingcac ?>"><input type="hidden" name="Ingpco" value="<?php echo $Ingpco ?>"><input type="hidden" name="Inghoa" value="<?php echo $Inghoa ?>">
                <input type="hidden" name="Ingcla" value="<?php echo $Ingcla ?>"><input type="hidden" name="Ingvre" value="<?php echo $Ingvre ?>"><input type="hidden" name="Ingmei" value="<?php echo $Ingmei ?>">
                <input type="hidden" name="Ingtut" value="<?php echo $Ingtut ?>"><input type="hidden" name="Ingfiu" value="<?php echo $Ingfiu ?>"><input type="hidden" name="Inghiu" value="<?php echo $Inghiu ?>">
                <table>
                    <tr class="alternar">
                        <td><label>Fecha_data : </label></td>
                        <td><input type="text" style="border: none" name="Fecha_data" value="<?php echo $Fecha_data ?>"></td>
                        <td>&ensp;</td>
                        <td><label>Hora_data : </label></td>
                        <td><input type="text" style="border: none" name="Hora_data" value="<?php echo $Hora_data ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Número de HC : </label></td>
                        <td><input type="text" style="border: none" name="Inghis" value="<?php echo $Inghis ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Número de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Ingnin" value="<?php echo $Ingnin  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Fecha de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Ingfei" value="<?php echo $Ingfei  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Servicio de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Ingsei" value="<?php echo $Ingsei  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Codigo del Responsable : </label></td>
                        <td><input type="text" style="border: none" name="Ingcem" value="<?php echo $Ingcem  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Nombre del Responsable : </label></td>
                        <td><input type="text" style="border: none" name="Ingent" value="<?php echo $Ingent  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Intentos Unix : </label></td>
                        <?php
                        if($Ingniu == '20')
                        {
                            ?>
                            <td><input type="text" style="border: none; background-color: #FD9F84" name="Ingniu" value="<?php echo $Ingniu  ?>"></td>
                            <?php
                        }
                        else
                        {
                            ?>
                            <td><input type="text" style="border: none" name="Ingniu" value="<?php echo $Ingniu  ?>"></td>
                            <?php
                        }
                        ?>
                    </tr>
                    <tr class="alternar">
                        <td><label>Guardado en Unix : </label></td>
                        <td>
                            <select name="Ingunx" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Ingunx ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="cliame101">
                <input type="hidden" name="id2" value="<?php echo $id2 ?>">
                <input type="submit" class="btn btn-primary" name="btnActualizar" value="Actualizar">

                <input type="text" style="border: none; width: 140px" readonly>

                <input type="submit" class="btn btn-success" name="btnInsertar" value="Insertar como nueva fila">
            </form>
        </div>
        <?php
        $accion = $_POST['accion']; $id2 = $_POST['id2'];
        $Medico = $_POST['Medico']; $Fecha_data = $_POST['Fecha_data']; $Hora_data = $_POST['Hora_data'];
        $Inghis = $_POST['Inghis']; $Ingnin = $_POST['Ingnin']; $Ingfei = $_POST['Ingfei']; $Inghin = $_POST['Inghin']; $Ingsei = $_POST['Ingsei']; $Ingtin = $_POST['Ingtin'];
        $Ingcai = $_POST['Ingcai']; $Ingtpa = $_POST['Ingtpa']; $Ingcem = $_POST['Ingcem']; $Ingent = $_POST['Ingent']; $Ingord = $_POST['Ingord']; $Ingpol = $_POST['Ingpol'];
        $Ingnco = $_POST['Ingnco']; $Ingdie = $_POST['Ingdie']; $Ingtee = $_POST['Ingtee']; $Ingtar = $_POST['Ingtar']; $Ingusu = $_POST['Ingusu']; $Inglug = $_POST['Inglug'];
        $Ingdig = $_POST['Ingdig']; $Ingdes = $_POST['Ingdes']; $Ingpla = $_POST['Ingpla']; $Ingfha = $_POST['Ingfha']; $Ingnpa = $_POST['Ingnpa']; $Ingcac = $_POST['Ingcac'];
        $Ingpco = $_POST['Ingpco']; $Inghoa = $_POST['Inghoa']; $Ingcla = $_POST['Ingcla']; $Ingvre = $_POST['Ingvre']; $Ingunx = $_POST['Ingunx']; $Ingmei = $_POST['Ingmei'];
        $Ingtut = $_POST['Ingtut']; $Ingniu = $_POST['Ingniu']; $Ingfiu = $_POST['Ingfiu']; $Inghiu = $_POST['Inghiu'];

        if(isset($_POST['btnActualizar']))
        {
            if($accion == 'cliame101')
            {
                $queryCliame101 = mysql_query("update cliame_000101 set Fecha_data='$Fecha_data', Hora_data='$Hora_data', Inghis='$Inghis', Ingnin='$Ingnin', Ingfei='$Ingfei',
                                           Ingsei='$Ingsei', Ingcem='$Ingcem', Ingent='$Ingent', Ingunx='$Ingunx', Ingniu='$Ingniu'
                                           WHERE id = '$id2'");

                ?>
                <script language="javascript">
                    window.close();
                </script>
                <?php
            }
        }
        if(isset($_POST['btnInsertar']))
        {
            $queryInsertCliame101 = mysql_query("insert into cliame_000101(Medico,Fecha_data,Hora_data,Inghis,Ingnin,Ingfei,Inghin,Ingsei,Ingtin,Ingcai,Ingtpa,Ingcem,Ingent,Ingord,
                                                                            Ingpol,Ingnco,Ingdie,Ingtee,Ingtar,Ingusu,Inglug,Ingdig,Ingdes,Ingpla,Ingfha,Ingnpa,Ingcac,Ingpco,Inghoa,
                                                                            Ingcla,Ingvre,Ingunx,Ingmei,Ingtut,Ingniu,Ingfiu,Inghiu,Seguridad,id)
                                                                    VALUES('cliame','$Fecha_data','$Hora_data','$Inghis','$Ingnin','$Ingfei','$Inghin','$Ingsei','$Ingtin','$Ingcai',
                                                                            '$Ingtpa','$Ingcem','$Ingent','$Ingord','$Ingpol','$Ingnco','$Ingdie','$Ingtee','$Ingtar','$Ingusu','$Inglug',
                                                                            '$Ingdig','$Ingdes','$Ingpla','$Ingfha','$Ingnpa','$Ingcac','$Ingpco','$Inghoa','$Ingcla','$Ingvre','$Ingunx',
                                                                            '$Ingmei','$Ingtut','$Ingniu','$Ingfiu','$Inghiu','C-cliame','')");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'root_000036')
{
    $query3 = mysql_query("select * from root_000036 WHERE id = '$idRegistro'");
    while($dato3 = mysql_fetch_array($query3))
    {
        $Pacced = $dato3['Pacced']; $Pactid = $dato3['Pactid']; $Pacap1 = $dato3['Pacap1'];
        $Pacap2 = $dato3['Pacap2']; $Pacno1 = $dato3['Pacno1']; $Pacno2 = $dato3['Pacno2'];
        $Pacnac = $dato3['Pacnac']; $Pacsex = $dato3['Pacsex']; $id3 = $dato3['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <table>
                    <tr class="alternar">
                        <td><label>Número de Documento : </label></td>
                        <td><input type="text" style="border: none" name="Pacced" value="<?php echo $Pacced ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Tipo Documento : </label></td>
                        <td>
                            <select name="Pactid" style="width: 150px">
                                <option>CC</option>
                                <option>CE</option>
                                <option>PA</option>
                                <option>TI</option>
                                <option>RC</option>
                                <option>MS</option>
                                <option selected><?php echo $Pactid ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Primer Apellido : </label></td>
                        <td><input type="text" style="border: none" name="Pacap1" value="<?php echo $Pacap1  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Segundo Apellido : </label></td>
                        <td><input type="text" style="border: none" name="Pacap2" value="<?php echo $Pacap2  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Primer Nombre : </label></td>
                        <td><input type="text" style="border: none" name="Pacno1" value="<?php echo $Pacno1  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Segundo Nombre : </label></td>
                        <td><input type="text" style="border: none" name="Pacno2" value="<?php echo $Pacno2  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Fecha Nacimiento : </label></td>
                        <td><input type="text" style="border: none" name="Pacnac" value="<?php echo $Pacnac  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Sexo : </label></td>
                        <td>
                            <select name="Pacsex" style="width: 150px">
                                <option>M</option>
                                <option>F</option>
                                <option selected><?php echo $Pacsex ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="root36">
                <input type="hidden" name="id3" value="<?php echo $id3 ?>">
                <input type="submit" class="btn btn-primary" value="Actualizar">
            </form>
        </div>
        <?php
        $accion = $_POST['accion']; $Pacced = $_POST['Pacced']; $Pactid = $_POST['Pactid'];
        $Pacap1 = $_POST['Pacap1']; $Pacap2 = $_POST['Pacap2']; $Pacno1 = $_POST['Pacno1'];
        $Pacno2 = $_POST['Pacno2']; $Pacnac = $_POST['Pacnac']; $Pacsex = $_POST['Pacsex'];
        $id3 = $_POST['id3'];

        if($accion == 'root36')
        {
            $queryCliame101 = mysql_query("update root_000036 set Pacced='$Pacced', Pactid='$Pactid', Pacap1='$Pacap1',
                                       Pacap2='$Pacap2', Pacno1='$Pacno1', Pacno2='$Pacno2', Pacnac='$Pacnac', Pacsex='$Pacsex'
                                       WHERE id = '$id3'");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'root_000037')
{
    $query4 = mysql_query("select * from root_000037 WHERE id = '$idRegistro'");
    while($dato4 = mysql_fetch_array($query4))
    {
        $Oriced = $dato4['Oriced']; $Oritid = $dato4['Oritid']; $Orihis = $dato4['Orihis'];
        $Oriing = $dato4['Oriing']; $Oriori = $dato4['Oriori']; $id4 = $dato4['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <table>
                    <tr class="alternar">
                        <td><label>Número de Documento : </label></td>
                        <td><input type="text" style="border: none" name="Oriced" value="<?php echo $Oriced ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Tipo Documento : </label></td>
                        <td>
                            <select name="Oritid" style="width: 150px">
                                <option>CC</option>
                                <option>CE</option>
                                <option>PA</option>
                                <option>TI</option>
                                <option>RC</option>
                                <option>MS</option>
                                <option selected><?php echo $Oritid ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Historia : </label></td>
                        <td><input type="text" style="border: none" name="Orihis" value="<?php echo $Orihis  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Oriing" value="<?php echo $Oriing  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Empresa Origen : </label></td>
                        <td><input type="text" style="border: none" name="Oriori" value="<?php echo $Oriori  ?>"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="root37">
                <input type="hidden" name="id4" value="<?php echo $id4 ?>">
                <input type="submit" class="btn btn-primary" value="Actualizar">
            </form>
        </div>
        <?php
        $accion = $_POST['accion']; $Oriced = $_POST['Oriced']; $Oritid = $_POST['Oritid'];
        $Orihis = $_POST['Orihis']; $Oriing = $_POST['Oriing']; $Oriori = $_POST['Oriori'];
        $id4 = $_POST['id4'];

        if($accion == 'root37')
        {
            $queryCliame101 = mysql_query("update root_000037 set Oriced='$Oriced', Oritid='$Oritid', Orihis='$Orihis',
                                       Oriing='$Oriing', Oriori='$Oriori'
                                       WHERE id = '$id4'");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'movhos_000016')
{
    $query5 = mysql_query("select * from movhos_000016 WHERE id = '$idRegistro'");
    while($dato5 = mysql_fetch_array($query5))
    {
        $Medico = $dato5['Medico']; $Fecha_data = $dato5['Fecha_data']; $Hora_data = $dato5['Hora_data'];
        $Inghis = $dato5['Inghis']; $Inging = $dato5['Inging'];         $Ingres = $dato5['Ingres'];
        $Ingnre = $dato5['Ingnre']; $Ingtip = $dato5['Ingtip'];         $Ingtel = $dato5['Ingtel'];
        $Ingdir = $dato5['Ingdir']; $Ingmun = $dato5['Ingmun'];         $Seguridad = $dato5['Seguridad'];
        $id5 = $dato5['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <input type="hidden" name="Medico" value="<?php echo $Medico ?>"><input type="hidden" name="Ingtel" value="<?php echo $Ingtel ?>">
                <input type="hidden" name="Ingdir" value="<?php echo $Ingdir ?>"><input type="hidden" name="Ingmun" value="<?php echo $Ingmun ?>">
                <input type="hidden" name="Seguridad" value="<?php echo $Seguridad ?>">
                <table>
                    <tr class="alternar">
                        <td><label>Fecha_data : </label></td>
                        <td><input type="text" style="border: none" name="Fecha_data" value="<?php echo $Fecha_data  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Hora_data : </label></td>
                        <td><input type="text" style="border: none" name="Hora_data" value="<?php echo $Hora_data  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Historia : </label></td>
                        <td><input type="text" style="border: none" name="Inghis" value="<?php echo $Inghis  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Inging" value="<?php echo $Inging  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Codigo Responsable : </label></td>
                        <td><input type="text" style="border: none" name="Ingres" value="<?php echo $Ingres  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Nombre Responsable : </label></td>
                        <td><input type="text" style="border: none" name="Ingnre" value="<?php echo $Ingnre  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Tipo de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Ingtip" value="<?php echo $Ingtip  ?>"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="movhos16">
                <input type="hidden" name="id5" value="<?php echo $id5 ?>">
                <input type="submit" class="btn btn-primary" name="btnActualizar" value="Actualizar">

                <input type="text" style="border: none; width: 140px" readonly>

                <input type="submit" class="btn btn-success" name="btnInsertar" value="Insertar como nueva fila">
            </form>
        </div>
        <?php
        $accion = $_POST['accion']; $id5 = $_POST['id5'];
        $Medico = $_POST['Medico']; $Fecha_data = $_POST['Fecha_data']; $Hora_data = $_POST['Hora_data'];
        $Inghis = $_POST['Inghis']; $Inging = $_POST['Inging'];         $Ingres = $_POST['Ingres'];
        $Ingnre = $_POST['Ingnre']; $Ingtip = $_POST['Ingtip'];         $Ingtel = $_POST['Ingtel'];
        $Ingdir = $_POST['Ingdir']; $Ingmun = $_POST['Ingmun'];         $Seguridad = $_POST['Seguridad'];

        if(isset($_POST['btnActualizar']))
        {
            if($accion == 'movhos16')
            {
                $queryCliame101 = mysql_query("update movhos_000016 set Fecha_data='$Fecha_data', Inghis='$Inghis', Inging='$Inging', Ingres='$Ingres', Ingnre='$Ingnre', Ingtip='$Ingtip'
                                          WHERE id = '$id5'");

                ?>
                <script language="javascript">
                    window.close();
                </script>
                <?php
            }
        }
        if(isset($_POST['btnInsertar']))
        {
            $queryCliame101 = mysql_query("insert into movhos_000016(Medico,Fecha_data,Hora_data,Inghis,Inging,Ingres,Ingnre,Ingtip,Ingtel,Ingdir,Ingmun,Seguridad,id)
                                                              VALUES('movhos','$Fecha_data','$Hora_data','$Inghis','$Inging','$Ingres','$Ingnre','$Ingtip','$Ingtel','$Ingdir',
                                                              '$Ingmun','$Seguridad','') ");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'movhos_000018')
{
    $query6 = mysql_query("select * from movhos_000018 WHERE id = '$idRegistro'");
    while($dato6 = mysql_fetch_array($query6))
    {
        $Medico = $dato6['Medico']; $Fecha_data = $dato6['Fecha_data']; $Hora_data = $dato6['Hora_data'];
        $Ubihis = $dato6['Ubihis']; $Ubiing = $dato6['Ubiing']; $Ubisac = $dato6['Ubisac'];
        $Ubisan = $dato6['Ubisan']; $Ubihac = $dato6['Ubihac']; $Ubihan = $dato6['Ubihan'];
        $Ubialp = $dato6['Ubialp']; $Ubiald = $dato6['Ubiald']; $Ubifap = $dato6['Ubifap'];
        $Ubihap = $dato6['Ubihap']; $Ubifad = $dato6['Ubifad']; $Ubihad = $dato6['Ubihad'];
        $Ubiptr = $dato6['Ubiptr']; $Ubitmp = $dato6['Ubitmp']; $Ubimue = $dato6['Ubimue'];
        $Ubiprg = $dato6['Ubiprg']; $Ubifho = $dato6['Ubifho']; $Ubihho = $dato6['Ubihho'];
        $Ubihot = $dato6['Ubihot']; $Ubiuad = $dato6['Ubiuad']; $Ubiamd = $dato6['Ubiamd'];
        $Ubijus = $dato6['Ubijus']; $Ubidie = $dato6['Ubidie']; $Ubiste = $dato6['Ubiste'];
        $id6 = $dato6['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <input type="hidden" name="Medico" value="<?php echo $Medico ?>"><input type="hidden" name="Hora_data" value="<?php echo $Hora_data ?>"><input type="hidden" name="Ubitmp" value="<?php echo $Ubitmp ?>">
                <input type="hidden" name="Ubiprg" value="<?php echo $Ubiprg ?>"><input type="hidden" name="Ubifho" value="<?php echo $Ubifho ?>"><input type="hidden" name="Ubihho" value="<?php echo $Ubihho ?>">
                <input type="hidden" name="Ubihot" value="<?php echo $Ubihot ?>"><input type="hidden" name="Ubiuad" value="<?php echo $Ubiuad ?>"><input type="hidden" name="Ubiamd" value="<?php echo $Ubiamd ?>">
                <input type="hidden" name="Ubijus" value="<?php echo $Ubijus ?>"><input type="hidden" name="Ubidie" value="<?php echo $Ubidie ?>"><input type="hidden" name="Ubiste" value="<?php echo $Ubiste ?>">
                <table>
                    <tr class="alternar">
                        <td><label>Fecha_data : </label></td>
                        <td><input type="text" style="border: none" name="Fecha_data" value="<?php echo $Fecha_data  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Historia : </label></td>
                        <td><input type="text" style="border: none" name="Ubihis" value="<?php echo $Ubihis  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Ubiing" value="<?php echo $Ubiing  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Cco Actual : </label></td>
                        <td><input type="text" style="border: none" name="Ubisac" value="<?php echo $Ubisac  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Cco Anterior : </label></td>
                        <td><input type="text" style="border: none" name="Ubisan" value="<?php echo $Ubisan  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Habitacion Actual : </label></td>
                        <td><input type="text" style="border: none" name="Ubihac" value="<?php echo $Ubihac  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Habitacion Anterior : </label></td>
                        <td><input type="text" style="border: none" name="Ubihan" value="<?php echo $Ubihan  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Alta en Proceso: </label></td>
                        <td>
                            <select name="Ubialp" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Ubialp ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Alta Definitiva : </label></td>
                        <td>
                            <select name="Ubiald" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Ubiald ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Fecha Alta en Proceso : </label></td>
                        <td><input type="text" style="border: none" name="Ubifap" value="<?php echo $Ubifap  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Hora Alta en Proceso : </label></td>
                        <td><input type="text" style="border: none" name="Ubihap" value="<?php echo $Ubihap  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Fecha Alta Definitiva : </label></td>
                        <td><input type="text" style="border: none" name="Ubifad" value="<?php echo $Ubifad  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Hora Alta Definitiva : </label></td>
                        <td><input type="text" style="border: none" name="Ubihad" value="<?php echo $Ubihad  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>En proceso de Traslado : </label></td>
                        <td>
                            <select name="Ubiptr" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Ubiptr ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Muerte : </label></td>
                        <td>
                            <select name="Ubimue" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Ubimue ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="movhos18">
                <input type="hidden" name="id6" value="<?php echo $id6 ?>">
                <input type="submit" class="btn btn-primary" name="btnActualizar" value="Actualizar">

                <input type="text" style="border: none; width: 140px" readonly>

                <input type="submit" class="btn btn-success" name="btnInsertar" value="Insertar como nueva fila">
            </form>
        </div>
        <?php
        $accion = $_POST['accion']; $id6 = $_POST['id6'];
        $Medico = $_POST['Medico']; $Fecha_data = $_POST['Fecha_data']; $Hora_data = $_POST['Hora_data'];
        $Ubihis = $_POST['Ubihis']; $Ubiing = $_POST['Ubiing']; $Ubisac = $_POST['Ubisac'];
        $Ubisan = $_POST['Ubisan']; $Ubihac = $_POST['Ubihac']; $Ubihan = $_POST['Ubihan'];
        $Ubialp = $_POST['Ubialp']; $Ubiald = $_POST['Ubiald']; $Ubifap = $_POST['Ubifap'];
        $Ubihap = $_POST['Ubihap']; $Ubifad = $_POST['Ubifad']; $Ubihad = $_POST['Ubihad'];
        $Ubiptr = $_POST['Ubiptr']; $Ubitmp = $_POST['Ubitmp']; $Ubimue = $_POST['Ubimue'];
        $Ubiprg = $_POST['Ubiprg']; $Ubifho = $_POST['Ubifho']; $Ubihho = $_POST['Ubihho'];
        $Ubihot = $_POST['Ubihot']; $Ubiuad = $_POST['Ubiuad']; $Ubiamd = $_POST['Ubiamd'];
        $Ubijus = $_POST['Ubijus']; $Ubidie = $_POST['Ubidie']; $Ubiste = $_POST['Ubiste'];

        if(isset($_POST['btnActualizar']))
        {
            if($accion == 'movhos18')
            {
                $queryCliame101 = mysql_query("update movhos_000018 set Ubihis='$Ubihis', Ubiing='$Ubiing', Ubisac='$Ubisac', Ubisan='$Ubisan',
                                           Ubihac='$Ubihac', Ubihan='$Ubihan', Ubialp='$Ubialp', Ubiald='$Ubiald', Ubifap='$Ubifap', Ubihap='$Ubihap',
                                           Ubifad='$Ubifad', Ubihad='$Ubihad', Ubiptr='$Ubiptr', Ubimue='$Ubimue'
                                          WHERE id = '$id6'");

                ?>
                <script language="javascript">
                    window.close();
                </script>
                <?php
            }
        }
        if(isset($_POST['btnInsertar']))
        {
            $queryCliame101 = mysql_query("insert into movhos_000018 (Medico,Fecha_data,Hora_data,Ubihis,Ubiing,Ubisac,Ubisan,Ubihac,Ubihan,Ubialp,Ubiald,Ubifap,Ubihap,Ubifad,Ubihad,
                                                                      Ubiptr,Ubitmp,Ubimue,Ubiprg,Ubifho,Ubihho,Ubihot,Ubiuad,Ubiamd,Ubijus,Ubidie,Ubiste,Seguridad,id)
                                                              VALUES ('movhos','$Fecha_data','$Hora_data','$Ubihis','$Ubiing','$Ubisac','$Ubisan','$Ubihac','$Ubihan','$Ubialp','$Ubiald',
                                                                      '$Ubifap','$Ubihap','$Ubifad','$Ubihad','$Ubiptr','$Ubitmp','$Ubimue','$Ubiprg','$Ubifho','$Ubihho','$Ubihot','$Ubiuad',
                                                                      '$Ubiamd','$Ubijus','$Ubidie','$Ubiste','A-movhos','')");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'movhos_000020')
{
    $query7 = mysql_query("select * from movhos_000020 WHERE id = '$idRegistro'");
    while($dato7 = mysql_fetch_array($query7))
    {
        $Habcod = $dato7['Habcod']; $Habcco = $dato7['Habcco']; $Habhis = $dato7['Habhis'];
        $Habing = $dato7['Habing']; $Habali = $dato7['Habali']; $Habdis = $dato7['Habdis'];
        $Habest = $dato7['Habest']; $habpro = $dato7['habpro']; $Habcpa = $dato7['Habcpa'];
        $id7 = $dato7['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <table>
                    <tr class="alternar">
                        <td><label>Codigo : </label></td>
                        <td><input type="text" style="border: none" name="Habcod" value="<?php echo $Habcod  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Piso : </label></td>
                        <td><input type="text" style="border: none" name="Habcco" value="<?php echo $Habcco  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Historia Actual : </label></td>
                        <td><input type="text" style="border: none" name="Habhis" value="<?php echo $Habhis  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Habing" value="<?php echo $Habing  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Listo el Aseo : </label></td>
                        <td>
                            <select name="Habali" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Habali ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Disponible : </label></td>
                        <td>
                            <select name="Habdis" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Habdis ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Estado del Registro : </label></td>
                        <td>
                            <select name="Habest" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Habest ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Proceso de Ocupacion : </label></td>
                        <td>
                            <select name="habpro" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $habpro ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Codigo en pantalla : </label></td>
                        <td><input type="text" style="border: none" name="Habcpa" value="<?php echo $Habcpa  ?>"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="movhos20">
                <input type="hidden" name="id7" value="<?php echo $id7 ?>">
                <input type="submit" class="btn btn-primary" value="Actualizar">
            </form>
        </div>
        <?php
        $accion = $_POST['accion']; $Habcod = $_POST['Habcod']; $Habcco = $_POST['Habcco'];
        $Habhis = $_POST['Habhis']; $Habing = $_POST['Habing']; $Habali = $_POST['Habali'];
        $Habdis = $_POST['Habdis']; $Habest = $_POST['Habest']; $habpro = $_POST['habpro'];
        $Habcpa = $_POST['Habcpa']; $id7 = $_POST['id7'];

        if($accion == 'movhos20')
        {
            $queryCliame101 = mysql_query("update movhos_000020 set Habcod='$Habcod', Habcco='$Habcco', Habhis='$Habhis', Habing='$Habing',
                                           Habali='$Habali', Habdis='$Habdis', Habest='$Habest', habpro='$habpro', Habcpa='$Habcpa'
                                          WHERE id = '$id7'");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'movhos_000004')
{
    $query8 = mysql_query("select * from movhos_000004 WHERE id = '$idRegistro'");
    while($dato8 = mysql_fetch_array($query8))
    {
        $Spahis = $dato8['Spahis']; $Spaing = $dato8['Spaing']; $Spacco = $dato8['Spacco'];
        $Spaart = $dato8['Spaart']; $Spauen = $dato8['Spauen']; $Spausa = $dato8['Spausa'];
        $Spaaen = $dato8['Spaaen']; $Spaasa = $dato8['Spaasa']; $id8 = $dato8['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <table>
                    <tr class="alternar">
                        <td><label>Numero de Historia : </label></td>
                        <td><input type="text" style="border: none" name="Spahis" value="<?php echo $Spahis  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Spaing" value="<?php echo $Spaing  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Centro de costos : </label></td>
                        <td><input type="text" style="border: none" name="Spacco" value="<?php echo $Spacco  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Codigo del articulo : </label></td>
                        <td><input type="text" style="border: none" name="Spaart" value="<?php echo $Spaart  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Entrada Total Unix : </label></td>
                        <td><input type="text" style="border: none" name="Spauen" value="<?php echo $Spauen  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Salida Total Unix : </label></td>
                        <td><input type="text" style="border: none" name="Spausa" value="<?php echo $Spausa  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Entrada Aprovechamientos : </label></td>
                        <td><input type="text" style="border: none" name="Spaaen" value="<?php echo $Spaaen  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Salida Aprovechamientos : </label></td>
                        <td><input type="text" style="border: none" name="Spaasa" value="<?php echo $Spaasa  ?>"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="movhos4">
                <input type="hidden" name="id8" value="<?php echo $id8 ?>">
                <input type="submit" class="btn btn-primary" value="Actualizar">
            </form>
        </div>
        <?php
        $accion = $_POST['accion']; $Spahis = $_POST['Spahis']; $Spaing = $_POST['Spaing'];
        $Spacco = $_POST['Spacco']; $Spaart = $_POST['Spaart']; $Spauen = $_POST['Spauen'];
        $Spausa = $_POST['Spausa']; $Spaaen = $_POST['Spaaen']; $Spaasa = $_POST['Spaasa'];
        $id8 = $_POST['id8'];

        if($accion == 'movhos4')
        {
            $queryCliame101 = mysql_query("update movhos_000004 set Spahis='$Spahis', Spaing='$Spaing', Spacco='$Spacco', Spaart='$Spaart',
                                           Spauen='$Spauen', Spausa='$Spausa', Spaaen='$Spaaen', Spaasa='$Spaasa'
                                          WHERE id = '$id8'");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'hce_000022')
{
    $query9 = mysql_query("select * from hce_000022 WHERE id = '$idRegistro'");
    while($dato9 = mysql_fetch_array($query9))
    {
        $Fecha_data = $dato9['Fecha_data'];
        $Mtrhis = $dato9['Mtrhis']; $Mtring = $dato9['Mtring']; $Mtrmed = $dato9['Mtrmed'];
        $Mtrtra = $dato9['Mtrtra']; $Mtreme = $dato9['Mtreme']; $Mtrcon = $dato9['Mtrcon'];
        $Mtrcur = $dato9['Mtrcur']; $Mtrcci = $dato9['Mtrcci']; $Mtrccu = $dato9['Mtrccu'];
        $Mtrtur = $dato9['Mtrtur']; $id9 = $dato9['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <table>
                    <tr class="alternar">
                        <td><label>Fecha_data : </label></td>
                        <td><input type="text" style="border: none" name="Fecha_data" value="<?php echo $Fecha_data  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Historia : </label></td>
                        <td><input type="text" style="border: none" name="Mtrhis" value="<?php echo $Mtrhis  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Mtring" value="<?php echo $Mtring  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Médico Asociado : </label></td>
                        <td><input type="text" style="border: none" name="Mtrmed" value="<?php echo $Mtrmed  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Tratante : </label></td>
                        <td>
                            <select name="Mtrtra" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Mtrtra ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Especialidad Medico Asociado : </label></td>
                        <td><input type="text" style="border: none" name="Mtreme" value="<?php echo $Mtreme  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Ultima Conducta : </label></td>
                        <td><input type="text" style="border: none" name="Mtrcon" value="<?php echo $Mtrcon  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Consulta Urgencias : </label></td>
                        <td>
                            <select name="Mtrcur" style="width: 150px">
                                <option>on</option>
                                <option>off</option>
                                <option selected><?php echo $Mtrcur ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Centro de costo de ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Mtrcci" value="<?php echo $Mtrcci  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Código cubículo Asignado : </label></td>
                        <td><input type="text" style="border: none" name="Mtrccu" value="<?php echo $Mtrccu  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Turno : </label></td>
                        <td><input type="text" style="border: none" name="Mtrtur" value="<?php echo $Mtrtur  ?>"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="hce22">
                <input type="hidden" name="id9" value="<?php echo $id9 ?>">
                <input type="submit" class="btn btn-primary" value="Actualizar">
            </form>
        </div>
        <?php
        $accion = $_POST['accion']; $Fecha_data = $_POST['Fecha_data'];
        $Mtrhis = $_POST['Mtrhis']; $Mtring = $_POST['Mtring']; $Mtrmed = $_POST['Mtrmed'];
        $Mtrtra = $_POST['Mtrtra']; $Mtreme = $_POST['Mtreme']; $Mtrcon = $_POST['Mtrcon'];
        $Mtrcur = $_POST['Mtrcur']; $Mtrcci = $_POST['Mtrcci']; $Mtrccu = $_POST['Mtrccu'];
        $Mtrtur = $_POST['Mtrtur']; $id9 = $_POST['id9'];

        if($accion == 'hce22')
        {
            $queryCliame101 = mysql_query("update hce_000022 set Mtrhis='$Mtrhis', Mtring='$Mtring', Mtrmed='$Mtrmed', Mtrtra='$Mtrtra',
                                           Mtreme='$Mtreme', Mtrcon='$Mtrcon', Mtrcur='$Mtrcur', Mtrcci='$Mtrcci', Mtrccu='$Mtrccu', Mtrtur='$Mtrtur'
                                          WHERE id = '$id9'");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'hce_000036')
{
    $query10 = mysql_query("select * from hce_000036 WHERE id = '$idRegistro'");
    while($dato10 = mysql_fetch_array($query10))
    {
        $Fecha_data = $dato10['Fecha_data'];
        $Firpro = $dato10['Firpro']; $Firhis = $dato10['Firhis']; $Firing = $dato10['Firing'];
        $Firusu = $dato10['Firusu']; $Firfir = $dato10['Firfir']; $Firrol = $dato10['Firrol'];
        $Fircco = $dato10['Fircco']; $id10 = $dato10['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <table>
                    <tr class="alternar">
                        <td><label>Fecha_data : </label></td>
                        <td><input type="text" style="border: none" name="Fecha_data" value="<?php echo $Fecha_data  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Formulario : </label></td>
                        <td><input type="text" style="border: none" name="Firpro" value="<?php echo $Firpro  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Historia : </label></td>
                        <td><input type="text" style="border: none" name="Firhis" value="<?php echo $Firhis  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Numero de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Firing" value="<?php echo $Firing  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Usuario que Grabó : </label></td>
                        <td><input type="text" style="border: none" name="Firusu" value="<?php echo $Firusu  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Firmado : </label></td>
                        <td><input type="text" style="border: none" name="Firfir" value="<?php echo $Firfir  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Rol de Quien Graba : </label></td>
                        <td><input type="text" style="border: none" name="Firrol" value="<?php echo $Firrol  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Centro de Costos Graba : </label></td>
                        <td><input type="text" style="border: none" name="Fircco" value="<?php echo $Fircco  ?>"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="hce36">
                <input type="hidden" name="id10" value="<?php echo $id10 ?>">
                <input type="submit" class="btn btn-primary" value="Actualizar">
            </form>
        </div>
        <?php
        /*
        $accion = $_POST['accion']; $Fecha_data = $_POST['Fecha_data'];
        $Firpro = $_POST['Firpro']; $Firhis = $_POST['Firhis']; $Firing = $_POST['Firing'];
        $Firusu = $_POST['Firusu']; $Firfir = $_POST['Firfir']; $Firrol = $_POST['Firrol'];
        $Fircco = $_POST['Fircco']; $id10 = $_POST['id10'];

        if($accion == 'hce36')
        {
            $queryCliame101 = mysql_query("update hce_000036 set Mtrhis='$Mtrhis', Mtring='$Mtring', Mtrmed='$Mtrmed', Mtrtra='$Mtrtra',
                                           Mtreme='$Mtreme', Mtrcon='$Mtrcon', Mtrcur='$Mtrcur', Mtrcci='$Mtrcci', Mtrccu='$Mtrccu', Mtrtur='$Mtrtur'
                                          WHERE id = '$id9'");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
        */
    }
}

if($tabla == 'movhos_000033')
{
    $query11 = mysql_query("select * from movhos_000033 WHERE id = '$idRegistro'");
    while($dato11 = mysql_fetch_array($query11))
    {
        $Medico = $dato11['Medico'];                        $Fecha_data = $dato11['Fecha_data'];            $Hora_data = $dato11['Hora_data'];
        $Historia_clinica = $dato11['Historia_clinica'];    $Num_ingreso = $dato11['Num_ingreso'];          $Servicio = $dato11['Servicio'];
        $Num_ing_serv = $dato11['Num_ing_serv'];            $Fecha_egre_serv = $dato11['Fecha_egre_serv'];  $Hora_egr_serv = $dato11['Hora_egr_serv'];
        $Tipo_egre_serv = $dato11['Tipo_egre_serv'];        $Dias_estan_serv = $dato11['Dias_estan_serv'];  $Seguridad = $dato11['Seguridad'];
        $id11 = $dato11['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <input type="hidden" name="Medico" value="<?php echo $Medico ?>">
                <input type="hidden" name="Hora_data" value="<?php echo $Hora_data ?>">
                <input type="hidden" name="Seguridad" value="<?php echo $Seguridad ?>">
                <table>
                    <tr class="alternar">
                        <td><label>Fecha_data : </label></td>
                        <td><input type="text" style="border: none" name="Fecha_data" value="<?php echo $Fecha_data  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Número de HC : </label></td>
                        <td><input type="text" style="border: none" name="Historia_clinica" value="<?php echo $Historia_clinica  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Número de Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Num_ingreso" value="<?php echo $Num_ingreso  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Servicio : </label></td>
                        <td><input type="text" style="border: none" name="Servicio" value="<?php echo $Servicio  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Número Ingreso al Servicio : </label></td>
                        <td><input type="text" style="border: none" name="Num_ing_serv" value="<?php echo $Num_ing_serv  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Fecha Egreso del Servicio : </label></td>
                        <td><input type="text" style="border: none" name="Fecha_egre_serv" value="<?php echo $Fecha_egre_serv  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Hora Egreso del Servicio : </label></td>
                        <td><input type="text" style="border: none" name="Hora_egr_serv" value="<?php echo $Hora_egr_serv  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Tipo Egreso : </label></td>
                        <td><input type="text" style="border: none" name="Tipo_egre_serv" value="<?php echo $Tipo_egre_serv  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Dias Estancia en el Servicio : </label></td>
                        <td><input type="text" style="border: none" name="Dias_estan_serv" value="<?php echo $Dias_estan_serv  ?>"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="movhos33">
                <input type="hidden" name="id11" value="<?php echo $id11 ?>">
                <input type="submit" class="btn btn-primary" name="btnActualizar" value="Actualizar">

                <input type="text" style="border: none; width: 140px" readonly>

                <input type="submit" class="btn btn-success" name="btnInsertar" value="Insertar como nueva fila">
            </form>
        </div>
        <?php
        $accion = $_POST['accion'];                        $id11 = $_POST['id11'];
        $Medico = $_POST['Medico'];                        $Fecha_data = $_POST['Fecha_data'];            $Hora_data = $_POST['Hora_data'];
        $Historia_clinica = $_POST['Historia_clinica'];    $Num_ingreso = $_POST['Num_ingreso'];          $Servicio = $_POST['Servicio'];
        $Num_ing_serv = $_POST['Num_ing_serv'];            $Fecha_egre_serv = $_POST['Fecha_egre_serv'];  $Hora_egr_serv = $_POST['Hora_egr_serv'];
        $Tipo_egre_serv = $_POST['Tipo_egre_serv'];        $Dias_estan_serv = $_POST['Dias_estan_serv'];

        if(isset($_POST['btnActualizar']))
        {
            if($accion == 'movhos33')
            {
                $queryCliame101 = mysql_query("update movhos_000033 set Fecha_data='$Fecha_data', Historia_clinica='$Historia_clinica', Num_ingreso='$Num_ingreso',
                                           Servicio='$Servicio', Num_ing_serv='$Num_ing_serv', Fecha_egre_serv='$Fecha_egre_serv', Hora_egr_serv='$Hora_egr_serv',
                                           Tipo_egre_serv='$Tipo_egre_serv', Dias_estan_serv='$Dias_estan_serv'
                                          WHERE id = '$id11'");

                ?>
                <script language="javascript">
                    window.close();
                </script>
                <?php
            }
        }
        if(isset($_POST['btnInsertar']))
        {
            $queryCliame101 = mysql_query("insert into movhos_000033(Medico,Fecha_data,Hora_data,Historia_clinica,Num_ingreso,Servicio,Num_ing_serv,Fecha_egre_serv,Hora_egr_serv,
                                          Tipo_egre_serv,Dias_estan_serv,Seguridad,id)
                                    VALUES('movhos','$Fecha_data','$Hora_data','$Historia_clinica','$Num_ingreso','$Servicio','$Num_ing_serv','$Fecha_egre_serv','$Hora_egr_serv',
                                          '$Tipo_egre_serv','$Dias_estan_serv','C-Movhos','') ");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}

if($tabla == 'tcx_000011')
{
    $query12 = mysql_query("select * from tcx_000011 WHERE id = '$idRegistro'");
    while($dato12 = mysql_fetch_array($query12))
    {
        $Fecha_data = $dato12['Fecha_data'];
        $Turtur = $dato12['Turtur'];    $Turqui = $dato12['Turqui'];    $Turfec = $dato12['Turfec'];
        $Turndt = $dato12['Turndt'];    $Turtdo = $dato12['Turtdo'];    $Turdoc = $dato12['Turdoc'];
        $Turhis = $dato12['Turhis'];    $Turnin = $dato12['Turnin'];    $Turnom = $dato12['Turnom'];
        $Turcir = $dato12['Turcir'];    $id12 = $dato12['id'];
        ?>
        <div style="margin-top: 20px; margin-left: 20px">
            <form name="procedimientos" method="post" action="#">
                <table>
                    <tr class="alternar">
                        <td><label>Fecha_data : </label></td>
                        <td><input type="text" style="border: none" name="Fecha_data" value="<?php echo $Fecha_data  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Codigo Turno : </label></td>
                        <td><input type="text" style="border: none" name="Turtur" value="<?php echo $Turtur  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Quirofano : </label></td>
                        <td><input type="text" style="border: none" name="Turqui" value="<?php echo $Turqui  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Fecha Cirugia : </label></td>
                        <td><input type="text" style="border: none" name="Turfec" value="<?php echo $Turfec  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Fecha Solicitud de Turno : </label></td>
                        <td><input type="text" style="border: none" name="Turndt" value="<?php echo $Turndt  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Tipo de Documento : </label></td>
                        <td><input type="text" style="border: none" name="Turtdo" value="<?php echo $Turtdo  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Identificacion : </label></td>
                        <td><input type="text" style="border: none" name="Turdoc" value="<?php echo $Turdoc  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Historia : </label></td>
                        <td><input type="text" style="border: none" name="Turhis" value="<?php echo $Turhis  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Ingreso : </label></td>
                        <td><input type="text" style="border: none" name="Turnin" value="<?php echo $Turnin  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Nombre : </label></td>
                        <td><input type="text" style="border: none" name="Turnom" value="<?php echo $Turnom  ?>"></td>
                    </tr>
                    <tr class="alternar">
                        <td><label>Cirugias : </label></td>
                        <td><input type="text" style="border: none" name="Turcir" value="<?php echo $Turcir  ?>"></td>
                    </tr>
                </table>
                <br>
                <input type="hidden" name="accion" value="tcx11">
                <input type="hidden" name="id12" value="<?php echo $id12 ?>">
                <input type="submit" class="btn btn-primary" value="Actualizar">
            </form>
        </div>
        <?php
        $accion = $_POST['accion'];     $Fecha_data = $_POST['Fecha_data'];
        $Turtur = $_POST['Turtur'];     $Turqui = $_POST['Turqui'];         $Turfec = $_POST['Turfec'];
        $Turndt = $_POST['Turndt'];     $Turtdo = $_POST['Turtdo'];         $Turdoc = $_POST['Turdoc'];
        $Turhis = $_POST['Turhis'];     $Turnin = $_POST['Turnin'];         $Turnom = $_POST['Turnom'];
        $Turcir = $_POST['Turcir'];     $id12 = $_POST['id'];

        if($accion == 'tcx11')
        {
            $queryCliame101 = mysql_query("update tcx_000011 set Fecha_data='$Fecha_data', Turtur='$Turtur', Turqui='$Turqui',
                                           Turfec='$Turfec', Turndt='$Turndt', Turtdo='$Turtdo', Turdoc='$Turdoc',
                                           Turhis='$Turhis', Turnin='$Turnin', Turnom='$Turnom', Turcir='$Turcir'
                                          WHERE id = '$id12'");

            ?>
            <script language="javascript">
                window.close();
            </script>
            <?php
        }
    }
}
?>