<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - CONCEPTOS CLINICA PARA NIIF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <style>
        .alternar:hover{ background-color:#CADCFF;}
    </style>
    <script type="text/javascript">
        function copiarvalor2(lineaSelected)
        {
            miPopup2 = window.open("ccpn03.php?codLinea="+lineaSelected.value+"&accion="+2,"miwin","width=10,height=10,left=200,top=250,scrollbars=no,location=no,status=no,resizable=no");
            miPopup2.focus()
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
        //include_once("niif/ccpn01.php");
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        

        include_once("root/comun.php");
        


        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
        $wactualiz = "1.0 11-abril-2017";
    }
    session_start();

    $valorRadio = $_POST['valorRadio']; if($valorRadio == ''){$valorRadio = $_GET['valorRadio'];}
    $concepto = $_POST['concepto']; if($concepto == ''){$concepto = $_GET['concepto'];}
    ?>
</head>

<body>
<?php
encabezado("<font style='font-size: x-large; font-weight: bold'>"."CONCEPTOS CLINICA PARA NIIF"."</font>",$wactualiz,"clinica");
?>
<div id="container" align="center">
    <div id="loginbox" style="margin-top:1px; width: 950px">
        <div id="panel-info">
            <div class="panel-heading">
            </div>
            <div style="padding-top:5px" class="panel-body" >

                <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="ccpn02.php">
                    <table align="center">
                        <tr align="center">
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td><label>Nombre</label></td>

                            <td><label>Codigo</label></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr align="center">
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td><input type="radio" id="radio" name="valorRadio" value="1" /></td>

                            <td><input type="radio" id="radio" name="valorRadio" value="2" checked /></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                        <tr>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                            <td colspan="2">
                                <div class="input-group">
                                    <input id="concepto" name="concepto" type="text" class="form-control" style="width: 300px" value="">
                                </div>
                            </td>
                            <td>
                                <div class="col-sm-12 controls">
                                    <input type="submit" class="btn btn-info btn-sm" id="bntIr" name="btnIr" title="Buscar" value="> > >">
                                </div>
                            </td>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 20px; border: none"></div></td>
                        </tr>
                    </table>
                </form>

                <br><br>
                <!-------------------- PANEL DE DATO ENCONTRADO --------------------------------------->
                <?php
                if($valorRadio == '' and $concepto == '')
                {
                    ?>
                    <br>
                    <br>
                    <div align="center">
                        <label>Ingrese Codigo o Nombre del Concepto para generar la consulta</label>
                    </div>
                    <?php
                }
                else
                {
                    $valorRadio = $_POST['valorRadio']; if($valorRadio == ''){$valorRadio = $_GET['valorRadio'];}
                    $concepto = $_POST['concepto']; if($concepto == ''){$concepto = $_GET['concepto'];}
                    $concepto = strtoupper($concepto);

                    if($concepto != null)
                    {
                        if ($valorRadio == 1)
                        {
                            $query_o1 = "SELECT a.concod, a.connom, b.linea, b.ctaphnc, b.ctaphdes, b.ctadeteri, b.ctavpn"
                                . "	FROM facon a, ameconclin b"
                                . "	WHERE a.connom like '%$concepto%'"
                                . " AND b.conc = a.concod";
                        }
                        elseif($valorRadio == 2)
                        {
                            $query_o1 = "SELECT a.concod, a.connom, b.linea, b.ctaphnc, b.ctaphdes, b.ctadeteri, b.ctavpn"
                                . "	FROM facon a, ameconclin b"
                                . "	WHERE a.concod like '%$concepto%'"
                                . " AND a.concod = b.conc";
                        }

                        $err_o = odbc_do($conex_o, $query_o1);
                        $Num_Filas = 0;
                        ?>
                        <table class="table table-bordered table-list">
                            <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                            <tr>
                                <th class="hidden-xs">Codigo del Concepto</th>
                                <th>Nombre del Concepto</th>
                                <th>Linea</th>
                                <th>Cuenta de Porcentaje Histórico Nota Crédito</th>
                                <th>Cuenta de porcentaje Histórico de Descuento</th>
                                <th>Cuenta de Deterioro</th>
                                <th>Cuenta VPN</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            while (odbc_fetch_row($err_o))
                            {
                                ?>
                                <form id="formDATOS" method="post" action="ccpn02.php">
                                    <?php
                                    $Num_Filas++;
                                    $codigoFind = odbc_result($err_o, 1);
                                    $nombreFind = odbc_result($err_o, 2);
                                    $lineaFind = odbc_result($err_o, 3);
                                    $porhFind = odbc_result($err_o, 4);
                                    $cuehFind = odbc_result($err_o, 5);
                                    $cuedFind = odbc_result($err_o, 6);
                                    $cuevpnFind = odbc_result($err_o, 7);
                                    ?>
                                    <tr class="alternar">
                                        <td><label><?php echo $codigoFind ?></label></td>
                                        <td><label><?php echo $nombreFind ?></label></td>
                                        <td><label><?php echo $lineaFind ?></label></td>
                                        <td><label><?php echo $porhFind ?></label></td>
                                        <td><label><?php echo $cuehFind ?></label></td>
                                        <td><label><?php echo $cuedFind ?></label></td>
                                        <td><label><?php echo $cuevpnFind ?></label></td>
                                        <td><input type="image" id="btnVer" src="/matrix/images/medical/paf/selecPaf.png" width="20" height="20" title="Seleccionar"></td>
                                        <input type="hidden" name="valorRadio" value="<?php echo 1 ?>">
                                        <input type="hidden" name="idInforme" value="<?php echo $codigoFind ?>">
                                        <input type="hidden" name="concepto" value="<?php echo $nombreFind ?>">
                                        <input type="hidden" name="lineaconcepto" value="<?php echo $lineaFind ?>">
                                        <input type="hidden" name="porhconcepto" value="<?php echo $porhFind ?>">
                                        <input type="hidden" name="cuehconcepto" value="<?php echo $cuehFind ?>">
                                        <input type="hidden" name="cuedconcepto" value="<?php echo $cuedFind ?>">
                                        <input type="hidden" name="cuevpnconcepto" value="<?php echo $cuevpnFind ?>">
                                    </tr>
                                </form>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <!-------------------- PANEL DE DATO SELECCIONADO --------------------------------------->

    <div id="divInforme" class="panel-body" style="padding-top: 1px">
        <?php
        $idR = $_POST['idInforme']; if($idR == null){$idR = $_GET['idInforme'];}
        $nombreSelected = $_POST['concepto'];
        $lineaSelected = $_POST['lineaconcepto'];
        $porhSelected = $_POST['porhconcepto'];
        $cuehSelected = $_POST['cuehconcepto'];
        $cuedSelected = $_POST['cuedconcepto'];
        $cuevpnSelected = $_POST['cuevpnconcepto'];


        if($idR != null)
        {
            ?>
            <script>foco()</script>
            <div style="width: 900px" class="table-bordered" align="left">
                <form id="formSelected" name="formNuevo" method="post" action="ccpn01.php">
                    <div style="margin-bottom: 10px; margin-top: 5px; margin-left: 5px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 120px"><label>Codigo</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 100px" name="codigoSelected" value="<?php echo $idR ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 120px"><label>Nombre:</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 510px" name="nombreSelected" value="<?php echo $nombreSelected ?>" readonly>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px; margin-top: 5px; margin-left: 5px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 120px"><label>Linea:</label></span>
                            <select id="lineaSelected" name="lineaSelected" class="form-control" style="width: 100px" onchange="copiarvalor2(lineaSelected)">
                                <?php
                                $query_o3 = "SELECT codi,nombre FROM amelinea ORDER BY codi ASC";
                                $err_o3 = odbc_do($conex_o, $query_o3);
                                while($datolinea = odbc_fetch_row($err_o3))
                                {
                                    echo "<option value='".odbc_result($err_o3,1)."'>".odbc_result($err_o3, 1)."</option>";
                                    $descripcionlinea=odbc_result($err_o3, 2);
                                }
                                ?>
                                <option selected="selected"><?php echo $lineaSelected ?></option>
                            </select>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 103px"><label>Descripcion:</label></span>
                            <?php
                            $query_o4 = "SELECT nombre FROM amelinea WHERE codi = '$lineaSelected'";
                            $err_o4 = odbc_do($conex_o, $query_o4);
                            $nombrelinea = odbc_fetch_row($err_o4);
                            ?>
                            <input id="nombrelinea" type="text" class="form-control" style="width: 510px" name="nombrelinea" value="<?php echo odbc_result($err_o4, 1) ?>" readonly>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px; margin-left: 5px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>Cuenta Porcentaje Histórico Nota Crédito:</label></span>
                            <select id="porhSelected" name="porhSelected" class="form-control" style="width: 190px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $porhSelected ?></option>
                            </select>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px; margin-left: 5px" class="input-group">
                        <div class="input-group">

                            <span class="input-group-addon" style="width: 352px"><label>Cuenta porcentaje Histórico Descuento:</label></span>
                            <select id="cuehSelected" name="cuehSelected" class="form-control" style="width: 190px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cuehSelected ?></option>
                            </select>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon"><label>Cuenta de Deterioro:</label></span>
                            <select id="cuedSelected" name="cuedSelected" class="form-control" style="width: 120px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cuedSelected ?></option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px; margin-left: 5px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>Cuenta VPN:</label></span>
                            <select id="cuevpnSelected" name="cuevpnSelected" class="form-control" style="width: 230px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cuevpnSelected ?></option>
                            </select>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <div class="col-sm-12 controls">
                                <input type="hidden" name="valorRadio" value="<?php echo $valorRadio ?>">
                                <input type="hidden" name="accion" value="actualizar">
                                <input type="submit" class="btn btn-success" id="bntIr" name="btnIr" title="Actualizar Registro" value="Actualizar">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }
        ?>
    </div class="panel-body">

    <div class="col-sm-12 controls" style="margin-top: 10px">
        <form id="formSelected" method="post" action="ccpn01.php">
            <input type="hidden" name="valorRadio" value="<?php echo $valorRadio ?>">
            <input type="hidden" name="accion" value="crear">
            <input type="submit" class="btn btn-primary" id="btnCrear" name="btnCrear" title="" value="Crear Concepto">
        </form>
    </div>
</div>
</body>
</html>