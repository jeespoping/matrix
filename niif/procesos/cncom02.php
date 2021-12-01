<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre e�es y tildes -->
    <title>MATRIX - CONCEPTOS QUE NO VAN EN EL COMPROBANTE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
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
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexi�n con la BD de Facturaci�n");
        $wactualiz = "1.0 11-abril-2017";
    }
    session_start();

    $valorRadio = $_POST['valorRadio']; if($valorRadio == ''){$valorRadio = $_GET['valorRadio'];}
    $concepto = $_POST['concepto']; if($concepto == ''){$concepto = $_GET['concepto'];}
    ?>
</head>

<body>
<?php
encabezado("<font style='font-size: x-large; font-weight: bold'>"."CONCEPTOS QUE NO VAN EN EL COMPROBANTE"."</font>",$wactualiz,"clinica");
?>
<div id="container" align="center">
    <div id="loginbox" style="margin-top:1px; width: 950px">
        <div id="panel-info">
            <div class="panel-heading"></div>
            <div style="padding-top:5px" class="panel-body" >
                <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="cncom02.php">
                    <table align="center">
                        <tr align="center">
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td><label>Nombre</label></td>

                            <td><label>Codigo</label></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr align="center">
                            <td>&nbsp;&nbsp;&nbsp;</td>
                            <td><input type="radio" id="radio" name="valorRadio" value="1" checked /></td>

                            <td><input type="radio" id="radio" name="valorRadio" value="2" /></td>
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
                            $query_o1 = "SELECT a.concod, a.connom"
                                . "	FROM facon a, ameconncom b"
                                . "	WHERE a.connom like '%$concepto%'"
                                . " AND b.noconc = a.concod";
                        }
                        elseif($valorRadio == 2)
                        {
                            $query_o1 = "SELECT a.concod, a.connom"
                                . "	FROM facon a, ameconncom b"
                                . "	WHERE a.concod like '%$concepto%'"
                                . " AND a.concod = b.noconc";
                        }

                        $err_o = odbc_do($conex_o, $query_o1);
                        $Num_Filas = 0;
                        ?>
                        <table class="table table-bordered table-list">
                            <thead style="background-color: #3276b1; color: lightcyan; font-weight: bold">
                            <tr>
                                <th class="hidden-xs">Codigo del Concepto</th>
                                <th>Nombre</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            while (odbc_fetch_row($err_o))
                            {
                                ?>
                                <form id="formDATOS" method="post" action="cncom01.php" onsubmit="return confirm('Esta seguro de eliminar este registro?')">
                                    <?php
                                    $Num_Filas++;
                                    $codigoFind = odbc_result($err_o, 1);
                                    $nombreFind = odbc_result($err_o, 2);
                                    ?>
                                    <tr class="alternar">
                                        <td><label><?php echo $codigoFind ?></label></td>
                                        <td><label><?php echo $nombreFind ?></label></td>
                                        <td><input type="image" id="btnVer" src="/matrix/images/medical/nomina/imageEFR.png" width="20" height="20" title="Eliminar Registro"></td>
                                        <input type="hidden" name="valorRadio" value="<?php echo 1 ?>">
                                        <input type="hidden" name="idInforme" value="<?php echo $codigoFind ?>">
                                        <input type="hidden" name="concepto" value="<?php echo $nombreFind ?>">
                                        <input type="hidden" name="accion" value="eliminar">
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

    <div class="col-sm-12 controls" style="margin-top: 10px">
        <form id="formSelected" method="post" action="cncom01.php">
            <input type="hidden" name="valorRadio" value="<?php echo $valorRadio ?>">
            <input type="hidden" name="accion" value="crear">
            <input type="submit" class="btn btn-primary" id="btnCrear" name="btnCrear" title="" value="Adicionar Concepto">
        </form>
    </div>
</div>
</body>
</html>