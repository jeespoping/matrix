<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre e�es y tildes -->
    <title>MATRIX - CONCEPTOS QUE NO VAN EN EL COMPROBANTE - PROCESOS -</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <style>
        .alternar:hover{ background-color:#CADCFF;}
    </style>
    <script type="text/javascript">
        function copiarvalor(codigoConcepto)
        {
            miPopup2 = window.open("ccpn03.php?codconcepto="+codigoConcepto.value+"&accion="+1,"miwin","width=10,height=10,left=200,top=250,scrollbars=no,location=no,status=no,resizable=no");
            miPopup2.focus()
        }

        function copiarvalor2(codigoLinea)
        {
            miPopup2 = window.open("ccpn03.php?codLinea="+codigoLinea.value+"&accion="+2,"miwin","width=10,height=10,left=200,top=250,scrollbars=no,location=no,status=no,resizable=no");
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
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        

        include_once("root/comun.php");
        


        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexi�n con la BD de Facturaci�n");
        $wactualiz = "1.0 11-abril-2017";
    }
    session_start();

    $accion = $_POST['accion'];
    $valorRadio = 2;
    $codigoSelected = $_POST['idInforme'];     $codigoInsert = $_POST['codigoConcepto'];
    //$nombreSelected = $_POST['concepto'];      $lineaInsert = $_POST['codigoLinea'];
    ?>
</head>

<body>
<?php
encabezado("<font style='font-size: x-large; font-weight: bold'>"."CONCEPTOS QUE NO VAN EN EL COMPROBANTE"."</font>",$wactualiz,"clinica");
?>
<div id="container" align="center">
    <div id="loginbox" style="margin-top:1px; width: 950px">
        <div id="panel-info">
            <div class="panel-heading">
            </div>
            <div style="padding-top:5px" class="panel-body" >
                <?php
                if($accion == 'eliminar')
                {
                    $query_o1 = "DELETE FROM ameconncom"
                        . "	WHERE noconc = '$codigoSelected'";

                    odbc_do($conex_o, $query_o1);

                    if($query_o1)
                    {
                        ?>
                        <label>El registro se elimin� correctamente</label>
                        <br>
                        <a href="cncom02.php?valorRadio=<?php echo $valorRadio ?>&concepto=<?php echo $codigoSelected ?>">ACEPTAR</a>
                        <?php
                    }
                }

                if($accion == 'crear')
                {
                    ?>
                    <div style="width: 900px" class="table-bordered" align="left">
                        <form id="formNuevo" name="formNuevo" method="post" action="cncom01.php">
                            <div align="center"><label for="a" style="vertical-align: middle">ADICION DE CONCEPTO</label></div>
                            <div style="margin-bottom: 10px; margin-top: 5px; margin-left: 5px" class="input-group">
                                <div class="input-group">
                                    <span class="input-group-addon" style="width: 120px"><label>Codigo</label></span>
                                    <select id="codigoConcepto" name="codigoConcepto" class="form-control" style="width: 100px" required onchange="copiarvalor(codigoConcepto)">
                                        <?php
                                        $query_o2 = "SELECT concod,connom FROM facon WHERE concod not in (SELECT noconc FROM ameconncom) AND conact = 'S' ORDER BY concod ASC";
                                        $err_o = odbc_do($conex_o, $query_o2);
                                        while($datoconcepto = odbc_fetch_row($err_o))
                                        {
                                            echo "<option value='".odbc_result($err_o, 1)."'>".odbc_result($err_o, 1)."</option>";
                                            $descripcionconcepto=odbc_result($err_o, 2);
                                        }
                                        ?>
                                        <option selected="selected" disabled value="">Seleccione...</option>
                                    </select>

                                    <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                    <span class="input-group-addon" style="width: 120px"><label>Nombre:</label></span>
                                    <input id="nombreconcepto" type="text" class="form-control" style="width: 510px" name="nombreconcepto" readonly>
                                </div>

                                <br>
                                <div style="margin-bottom: 10px" class="input-group">

                                    <div class="input-group-addon" style="background-color: #ffffff; width: 77.5%; border: none"></div>

                                    <div class="col-sm-12 controls">
                                        <input type="hidden" name="valorRadio" value="<?php echo $valorRadio ?>">
                                        <input type="hidden" name="accion" value="insertar">
                                        <input type="submit" class="btn btn-success" id="bntIr" name="btnIr" title="Guardar Registro" value="Guardar">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-sm-12 controls" style="margin-top: 20px">
                        <form id="formSelected" method="post" action="cncom02.php">
                            <input type="submit" class="btn btn-primary" id="btnCrear" name="btnCrear" title="" value="Cancelar">
                        </form>
                    </div>
                    <?php
                }

                if($accion == 'insertar')
                {
                    $query_o1 = "INSERT INTO ameconncom (noconc) VALUES ('$codigoInsert')";

                    odbc_do($conex_o, $query_o1);

                    if($query_o1)
                    {
                        ?>
                        <label>El registro se guard� correctamente</label>
                        <br>
                        <a href="cncom02.php?valorRadio=<?php echo $valorRadio ?>&concepto=<?php echo $codigoInsert ?>">ACEPTAR</a>
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