<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - MAESTRO DE EMPRESAS POR TIPO</title>
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
encabezado("<font style='font-size: x-large; font-weight: bold'>"."MAESTRO DE EMPRESAS POR TIPO"."</font>",$wactualiz,"clinica");
?>
<div id="container" align="center">
    <div id="loginbox" style="margin-top:1px; width: 950px">
        <div id="panel-info">
            <div class="panel-heading">
            </div>
            <div style="padding-top:5px" class="panel-body" >

                <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="mept02.php">
                    <table align="center">
                        <tr align="center">
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td><label>Nombre</label></td>

                            <td><label>Tipo</label></td>
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
                        <label>Ingrese Tipo o Nombre de la empresa para generar la consulta</label>
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
                            $query_o1 = "SELECT a.temcod, a.temdes, b.ctacphnc, b.ctacphde, b.ctacdeter, b.ctacvpn, b.ctaingfin, b.ctagasfin, b.tipnit, b.tipcco"
                                . "	FROM intem a, ameemptip b"
                                . "	WHERE a.temdes like '%$concepto%'"
                                . " AND b.tipemp = a.temcod";
                        }
                        elseif($valorRadio == 2)
                        {
                            $query_o1 = "SELECT a.temcod, a.temdes, b.ctacphnc, b.ctacphde, b.ctacdeter, b.ctacvpn, b.ctaingfin, b.ctagasfin, b.tipnit, b.tipcco"
                                . "	FROM intem a, ameemptip b"
                                . "	WHERE a.temcod like '%$concepto%'"
                                . " AND b.tipemp = a.temcod";
                        }

                        $err_o = odbc_do($conex_o, $query_o1);
                        $Num_Filas = 0;
                        ?>
                        <table class="table table-bordered table-list">
                            <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                            <tr>
                                <th class="hidden-xs">Tipo Empresa</th>
                                <th>Nombre Empresa</th>
                                <th>Cuenta Comprobante Porcentaje Histórico Nota Crédito</th>
                                <th>Cuenta Comprobante Porcentaje Historico Descuento</th>
                                <th>Cuenta Comprobante Deterioro</th>
                                <th>Cuenta Comprobante VPN</th>
                                <th>Cuenta Ingreso Fin</th>
                                <th>Cuenta Gasto Fin</th>
                                <th>Tipo NIT</th>
                                <th>Tipo Centro Costos</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            while (odbc_fetch_row($err_o))
                            {
                                ?>
                                <form id="formDATOS" method="post" action="mept02.php">
                                    <?php
                                    $Num_Filas++;
                                    $codigoFind = odbc_result($err_o, 1);
                                    $nombreFind = odbc_result($err_o, 2);
                                    $cuecphncFind = odbc_result($err_o, 3);
                                    $cuecphdesFind = odbc_result($err_o, 4);
                                    $cuecdetFind = odbc_result($err_o, 5);
                                    $cuecvpnFind = odbc_result($err_o, 6);
                                    $cueinfinFind = odbc_result($err_o, 7);
                                    $cuegafinFind = odbc_result($err_o, 8);
                                    //$tipnitFind = odbc_result($err_o, 9);
                                    //$tipccosFind = odbc_result($err_o, 10);
                                    ?>
                                    <tr class="alternar">
                                        <td><label><?php echo $codigoFind ?></label></td>
                                        <td><label><?php echo $nombreFind ?></label></td>
                                        <td><label><?php echo $cuecphncFind ?></label></td>
                                        <td><label><?php echo $cuecphdesFind ?></label></td>
                                        <td><label><?php echo $cuecdetFind ?></label></td>
                                        <td><label><?php echo $cuecvpnFind ?></label></td>
                                        <td><label><?php echo $cueinfinFind ?></label></td>
                                        <td><label><?php echo $cuegafinFind ?></label></td>
                                        <td><label><?php// echo $tipnitFind ?></label></td>
                                        <td><label><?php// echo $tipccosFind ?></label></td>
                                        <td><input type="image" id="btnVer" src="/matrix/images/medical/paf/selecPaf.png" width="20" height="20" title="Seleccionar"></td>
                                        <input type="hidden" name="valorRadio" value="<?php echo 1 ?>">
                                        <input type="hidden" name="idInforme" value="<?php echo $codigoFind ?>">
                                        <input type="hidden" name="concepto" value="<?php echo $nombreFind ?>">
                                        <input type="hidden" name="cuecphncFind" value="<?php echo $cuecphncFind ?>">
                                        <input type="hidden" name="cuecphdesFind" value="<?php echo $cuecphdesFind ?>">
                                        <input type="hidden" name="cuecdetFind" value="<?php echo $cuecdetFind ?>">
                                        <input type="hidden" name="cuecvpnFind" value="<?php echo $cuecvpnFind ?>">
                                        <input type="hidden" name="cueinfinFind" value="<?php echo $cueinfinFind ?>">
                                        <input type="hidden" name="cuegafinFind" value="<?php echo $cuegafinFind ?>">
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
        $cuecphncSelected = $_POST['cuecphncFind'];
        $cuecphdesSelected = $_POST['cuecphdesFind'];
        $cuecdetSelected = $_POST['cuecdetFind'];
        $cuecvpnSelected = $_POST['cuecvpnFind'];
        $cueinfinSelected = $_POST['cueinfinFind'];
        $cuegafinSelected = $_POST['cuegafinFind'];


        if($idR != null)
        {
            ?>
            <script>foco()</script>
            <div style="width: 900px" class="table-bordered" align="left">
                <form id="formSelected" name="formNuevo" method="post" action="mept01.php">
                    <div style="margin-bottom: 10px; margin-top: 5px; margin-left: 5px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 120px"><label>Tipo</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 100px" name="codigoSelected" value="<?php echo $idR ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 120px"><label>Nombre:</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 510px" name="nombreSelected" value="<?php echo $nombreSelected ?>" readonly>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px; margin-left: 5px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>Cuenta Comprobante Porcentaje Histórico Nota Crédito:</label></span>
                            <select id="cuecphncSelected" name="cuecphncSelected" class="form-control" style="width: 190px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cuecphncSelected ?></option>
                            </select>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px; margin-left: 5px" class="input-group">
                        <div class="input-group">

                            <span class="input-group-addon" style="width: 462px"><label>Cuenta Comprobante Porcentaje Historico Descuento:</label></span>
                            <select id="cuecphdesSelected" name="cuecphdesSelected" class="form-control" style="width: 190px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cuecphdesSelected ?></option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px; margin-left: 5px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>Cuenta Comprobante Deterioro:</label></span>
                            <select id="cuecdetSelected" name="cuecdetSelected" class="form-control" style="width: 160px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cuecdetSelected ?></option>
                            </select>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon"><label>Cuenta Comprobante VPN:</label></span>
                            <select id="cuecvpnSelected" name="cuecvpnSelected" class="form-control" style="width: 160px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cuecvpnSelected ?></option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px; margin-left: 5px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 275px"><label>Cuenta Ingreso Fin:</label></span>
                            <select id="cueinfinSelected" name="cueinfinSelected" class="form-control" style="width: 160px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cueinfinSelected ?></option>
                            </select>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 235px"><label>Cuenta Gasto Fin:</label></span>
                            <select id="cuegafinSelected" name="cuegafinSelected" class="form-control" style="width: 160px">
                                <?php
                                $query_o5 = "SELECT cuecod FROM cocuen WHERE cueniv = '5' ORDER BY cuecod ASC";
                                $err_o5 = odbc_do($conex_o, $query_o5);
                                while($datoporh = odbc_fetch_row($err_o5))
                                {
                                    echo "<option value='".odbc_result($err_o5,1)."'>".odbc_result($err_o5, 1)."</option>";
                                }
                                ?>
                                <option selected="selected"><?php echo $cuegafinSelected ?></option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 10px" class="input-group">

                        <div class="input-group-addon" style="background-color: #ffffff; width: 75.7%; border: none"></div>

                        <div class="col-sm-12 controls">
                            <input type="hidden" name="valorRadio" value="<?php echo $valorRadio ?>">
                            <input type="hidden" name="accion" value="actualizar">
                            <input type="submit" class="btn btn-success" id="bntIr" name="btnIr" title="Actualizar Registro" value="Actualizar">
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }
        ?>
    </div class="panel-body">

    <div class="col-sm-12 controls" style="margin-top: 10px">
        <form id="formSelected" method="post" action="mept01.php">
            <input type="hidden" name="valorRadio" value="<?php echo $valorRadio ?>">
            <input type="hidden" name="accion" value="crear">
            <input type="submit" class="btn btn-primary" id="btnCrear" name="btnCrear" title="" value="Crear Empresa">
        </form>
    </div>
</div>
</body>
</html>