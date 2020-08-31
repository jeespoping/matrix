<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>MATRIX - Gestion de Stickers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="StyleStkCenest.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <script src="JsStkCenest.js" type="text/javascript"></script>
    <style>
        .alternar:hover{ background-color:#CADCFF;}
    </style>
    <script>
        function enfocar()
        {
            document.getElementById('codArticulo').focus(true);
        }
    </script>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
        });
    </script>
    <script>
        $(function() {
            $( "#etq1Fv" ).datepicker();
            $( "#etq2Fv" ).datepicker();
            $( "#etq3Fv" ).datepicker();
            $( "#etq4Fv" ).datepicker();
        });
    </script> <!--Calendarios-->
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
        


        $wactualiz = "1.0 02-febrero-2018";
    }
    session_start();

    echo 'USUARIO= '.$_SESSION['user'];
    $usua = str_replace("01","", $wuse);
    $usuario = $usua.'-01';
    $queryUsuario = mysql_query("SELECT Ideno1,Ideno2,Ideap1,Ideap2 from talhuma_000013 WHERE Ideuse LIKE '$usuario'");
    $datoUsuario = mysql_fetch_array($queryUsuario);
    $usuarioF = $datoUsuario[0].' '.$datoUsuario[2].' '.$datoUsuario[3];

    if($usuarioF == null)
    {
        $usuario = $_SESSION['user'];
        $queryUsuario = mysql_query("SELECT Ideno1,Ideno2,Ideap1,Ideap2 from talhuma_000013 WHERE Ideuse LIKE '$usuario'");
        $datoUsuario = mysql_fetch_array($queryUsuario);
        $usuarioF = $datoUsuario[0].' '.$datoUsuario[2].' '.$datoUsuario[3];
    }

    $fecha_actual=date("Y-m-d");    $hora_actual = date('H:i:s')
    ?>
</head>

<body>
    <div id="divEncabezado" class="encabezado">
        <?php
        encabezado("<font style='font-size: x-large; font-weight: bold'>"."Impresion de Stickers Central de Esterilizacion"."</font>",$wactualiz,"clinica");
        ?>
    </div>

    <div class="contenido">
        <div class="panel-body divselecciones" align="center" >
            <table align="center" class="tblselecciones">
                <tr>
                    <td colspan="7">
                        <h5 class="text-primary"><strong>Selección de Etiqueta: </strong></h5>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="input-group">
                            <label for="selparam">Material Osteosintesis &nbsp;</label>
                            <input type="radio" name="selparam" id="selparam" value="0" onclick="mostrarop1('etiqueta1','etiqueta2','etiqueta3','etiqueta4','divInstruccion')">
                        </div>
                    </td>
                    <td><div class="input-group-addon separaradio"></div></td>
                    <td>
                        <div class="input-group">
                            <label>Material Quirurgico y Hospitalario &nbsp;</label>
                            <input type="radio" name="selparam" id="selparam" value="1" onclick="mostrarop2('etiqueta1','etiqueta2','etiqueta3','etiqueta4','divInstruccion')">
                        </div>
                    </td>
                    <td><div class="input-group-addon separaradio"></div></td>
                    <td>
                        <div class="input-group">
                            <label>Detergente &nbsp;</label>
                            <input type="radio" name="selparam" id="selparam" value="2" onclick="mostrarop3('etiqueta1','etiqueta2','etiqueta3','etiqueta4','divInstruccion')">
                        </div>
                    </td>
                    <td><div class="input-group-addon separaradio"></div></td>
                    <td>
                        <div class="input-group">
                            <label>Desinfeccion &nbsp;</label>
                            <input type="radio" name="selparam" id="selparam" value="3" onclick="mostrarop4('etiqueta1','etiqueta2','etiqueta3','etiqueta4','divInstruccion')">
                        </div>
                    </td>
                </tr>
            </table>
            <div id="divInstruccion" class="divInstruccion">
                <h3>Seleccione la etiqueta que desea imprimir.</h3>
            </div>
        </div>

        <div class="divetiquetas" id="container">
            <div class="input-group divEtiqueta1" id="etiqueta1">
                <h3>Etiqueta Para Material de Osteosintesis</h3>
                <div class="divContainerEtq1">
                    <table align="center" border="0">
                        <tr>
                            <td colspan="6"><label for="etq1Titulo">MATERIAL DE OSTEOSINTESIS</label></td>
                            <td colspan="3" rowspan="3"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
                        </tr>
                        <tr>
                            <td><label for="etq1Nombre">NOMBRE: </label></td>
                            <td colspan="6"><input type="text" style="width: 200px" id="etq1Nombre" name="etq1Nombre"></td>
                        </tr>
                        <tr>
                            <td><label for="etq1Cirujano">CIRUJANO: </label></td>
                            <td colspan="6"><input type="text" style="width: 200px" id="etq1Cirujano" name="etq1Cirujano"></td>
                        </tr>
                        <tr>
                            <td><label for="etq1Casa">CASA COMERCIAL: </label></td>
                            <td colspan="6"><input type="text" style="width: 200px" id="etq1Casa" name="etq1Casa"></td>
                        </tr>
                        <tr>
                            <td><label for="etq1NomSistema">NOMBRE SISTEMA: </label></td>
                            <td colspan="6"><input type="text" style="width: 350px" id="etq1NomSistema" name="etq1NomSistema"></td>
                        </tr>
                        <tr>
                            <td><label for="etq1NumCajas">NUMERO DE CAJAS: </label></td>
                            <td><input type="number" style="width: 90px" min="1" id="etq1NumCajas" name="etq1NumCajas"></td>
                            <td>&ensp;</td>
                            <td><label for="etq1Lote">LOTE: </label></td>
                            <td><input type="text" style="width: 70px" id="etq1Lote" name="etq1Lote"></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                        </tr>
                        <tr>
                            <td><label for="etq1Fp">FP: </label></td>
                            <td><input type="text" style="width: 90px" id="etq1Fp" name="etq1Fp" value="<?php echo $fecha_actual ?>" readonly></td>
                            <td>&ensp;</td>
                            <td><label for="etq1Hora">HORA: </label></td>
                            <td><input type="text" style="width: 70px" id="etq1Hora" name="etq1Hora" value="<?php echo $hora_actual ?>" readonly></td>
                            <td><label for="etq1Fv">F.V: </label></td>
                            <td><input type="text" style="width: 90px" id="etq1Fv" name="etq1Fv" placeholder="aaaa-mm-dd" readonly></td>
                        </tr>
                        <tr>
                            <td><label for="etq1Metodo">METODO: </label></td>
                            <td colspan="6">
                                <select id="etq1Metodo" name="etq1Metodo" class="etq1Metodo" style="border-top: none;border-right: none;border-left: none;border-color: #0d0d0d;width: 180px">
                                    <option>PEROXIDO</option>
                                    <option>VAPOR</option>
                                    <option>FORMOL</option>
                                    <option selected disabled>Seleccione...</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="etq1Responsable">RESPONSABLE: </label></td>
                            <td colspan="6">
                                <input type="text" style="width: 350px" id="etq1Responsable" name="etq1Responsable" value="<?php echo $usuarioF ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" class="lblLeyenda">
                                <label>MANTENGASE EN LUGAR FRESCO Y SECO<br>
                                       TEMPERATURA: 17°C - 25°C<br>
                                       NO USAR SI EL EMPAQUE SE ENCUENTRA DETERIORADO.
                                </label>
                            </td>
                        </tr>
                        <tr><td colspan="7"></td></tr>
                    </table>
                </div>

                <div class="divPrint">
                    <table>
                        <tr>
                            <td>
                                <input type="hidden" name="idE" id="idE" value="1">
                                <input type="image" id="imprimir" title="IMPRIMIR" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25" style="margin-left: 350px"
                                       onclick="verificarNulos(); return false">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="input-group divEtiqueta2" id="etiqueta2">
                <h3>Etiqueta Para Material Quirurgico y Hospitalario</h3>
                <div class="divContainerEtq2">
                    <table align="center" border="0">
                        <tr>
                            <td colspan="5"><label for="etq1Titulo">MATERIAL QUIRURGICO Y HOSPITALARIO</label></td>
                            <td>&ensp;</td>
                            <td colspan="2" rowspan="3"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
                        </tr>
                        <tr>
                            <td><label for="etq2Unidad">UNIDAD: </label></td>
                            <td colspan="6"><input type="text" style="width: 200px" id="etq2Unidad" name="etq2Unidad"></td>
                        </tr>
                        <tr>
                            <td><label for="etq2Equipo">EQUIPO: </label></td>
                            <td colspan="6"><input type="text" style="width: 200px" id="etq2Equipo" name="etq2Equipo"></td>
                        </tr>
                        <tr>
                            <td><label for="etq2Metodo">METODO: </label></td>
                            <td colspan="6">
                                <select id="etq2Metodo" name="etq2Metodo" class="etq1Metodo" style="border-top: none;border-right: none;border-left: none;border-color: #0d0d0d;width: 180px">
                                    <option>PEROXIDO</option>
                                    <option>VAPOR</option>
                                    <option>FORMOL</option>
                                    <option selected disabled>Seleccione...</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="etq2Fp">FP: </label></td>
                            <td><input type="text" style="width: 90px" id="etq2Fp" name="etq2Fp" value="<?php echo $fecha_actual ?>" readonly></td>
                            <td>&ensp;</td>
                            <td><label for="etq2Hora">HORA P: </label></td>
                            <td><input type="text" style="width: 70px" id="etq2Hora" name="etq2Hora" value="<?php echo $hora_actual ?>" readonly></td>
                            <td>&ensp;</td>
                            <td><label for="etq2Fv">F.V: </label></td>
                            <td><input type="text" style="width: 90px" id="etq2Fv" name="etq2Fv" placeholder="aaaa-mm-dd" readonly></td>
                        </tr>
                        <tr>
                            <td><label for="etq2Lote">LOTE: </label></td>
                            <td colspan="7"><input type="text" style="width: 200px" id="etq2Lote" name="etq2Lote"></td>
                        </tr>
                        <tr>
                            <td><label for="etq2Responsable">RESPONSABLE: </label></td>
                            <td colspan="7">
                                <input type="text" style="width: 350px" id="etq2Responsable" name="etq2Responsable" value="<?php echo $usuarioF ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="etq2NReproceso"># REPROCESO: </label></td>
                            <td colspan="7">
                                <input type="number" style="width: 90px" id="etq2NReproceso" name="etq2NReproceso" min="0" max="99" value="0">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" class="lblLeyenda">
                                <label>MANTENGASE EN LUGAR FRESCO Y SECO<br>
                                    TEMPERATURA: 17°C - 25°C<br>
                                    NO USAR SI EL EMPAQUE SE ENCUENTRA DETERIORADO.
                                </label>
                            </td>
                        </tr>
                        <tr><td colspan="7"></td></tr>
                    </table>
                </div>

                <div class="divPrint">
                    <table>
                        <tr>
                            <td>
                                <input type="hidden" name="idE2" id="idE2" value="2">
                                <input type="image" id="imprimir" title="IMPRIMIR" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25" style="margin-left: 350px"
                                       onclick="verificarNulos2(); return false">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="input-group divEtiqueta3" id="etiqueta3">
                <h3>Etiqueta Para Detergente</h3>
                <div class="divContainerEtq3">
                    <table align="center" border="0">
                        <tr>
                            <td><label for="etq3Detergente">DETERGENTE: </label></td>
                            <td><input type="text" style="width: 200px" name="etq3Detergente" id="etq3Detergente" value="ENDOZYNE"></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td colspan="2" rowspan="3"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
                        </tr>
                        <tr>
                            <td><label for="etq3Fp">FP: </label></td>
                            <td><input type="text" id="etq3Fp" name="etq3Fp" value="<?php echo $fecha_actual ?>" readonly></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                        </tr>
                        <tr>
                            <td><label for="etq3Hora">HORA P: </label></td>
                            <td><input type="text" style="width: 136px" id="etq3Hora" name="etq3Hora" value="<?php echo $hora_actual ?>" readonly></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                        </tr>
                        <tr>
                            <td><label for="etq3Fv">F.V: </label></td>
                            <td><input type="text" id="etq3Fv" name="etq3Fv" placeholder="aaaa-mm-dd" readonly></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                        </tr>
                        <tr>
                            <td><label for="etq3Responsable">OPERARIO: </label></td>
                            <td colspan="4">
                                <input type="text" style="width: 350px" id="etq3Responsable" name="etq3Responsable" value="<?php echo $usuarioF ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="lblLeyenda">
                                <label>MANTENGASE EN LUGAR FRESCO Y SECO <br> NO USAR PASADA LA FECHA DE PREPARACION</label>
                            </td>
                        </tr>
                        <tr><td colspan="5"></td></tr>
                    </table>
                </div>

                <div class="divPrint">
                    <table>
                        <tr>
                            <td>
                                <input type="hidden" name="idE3" id="idE3" value="3">
                                <input type="image" id="imprimir" title="IMPRIMIR" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25" style="margin-left: 350px"
                                       onclick="verificarNulos3(); return false">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="input-group divEtiqueta4" id="etiqueta4">
                <h3>Etiqueta Para Desinfeccion</h3>
                <div class="divContainerEtq4">
                    <table align="center" border="0">
                        <tr>
                            <td><label for="etq4Desinfeccion">DESINFECCION </label></td>
                            <td>&ensp;</td>
                            <!--
                            <td><input type="text" style="width: 200px" name="etq4Desinfeccion" id="etq4Desinfeccion" required></td>
                            -->
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td colspan="2" rowspan="3"><img src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80"></td>
                        </tr>
                        <tr>
                            <td><label for="etq4Unidad">UNIDAD: </label></td>
                            <td><input type="text" id="etq4Unidad" name="etq4Unidad" required></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                        </tr>
                        <tr>
                            <td><label for="etq4Fv">F.V: </label></td>
                            <td><input type="text" id="etq4Fv" name="etq4Fv" required placeholder="aaaa-mm-dd" readonly></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                        </tr>
                        <tr>
                            <td><label for="etq4Fp">FP: </label></td>
                            <td><input type="text" id="etq4Fp" name="etq4Fp" value="<?php echo $fecha_actual ?>" readonly></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                        </tr>
                        <tr>
                            <td><label for="etq4Hora">HORA: </label></td>
                            <td><input type="text" style="width: 136px" id="etq4Hora" name="etq4Hora" value="<?php echo $hora_actual ?>" readonly></td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                            <td>&ensp;</td>
                        </tr>
                        <tr>
                            <td><label for="etq4Responsable">OPERARIO: </label></td>
                            <td colspan="4">
                                <input type="text" style="width: 350px" id="etq4Responsable" name="etq4Responsable" value="<?php echo $usuarioF ?>" readonly>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="etq4NReproceso"># REPROCESO: </label></td>
                            <td colspan="5">
                                <input type="number" style="width: 90px" id="etq4NReproceso" name="etq4NReproceso" min="0" max="99" value="0">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="lblLeyenda">
                                <label>MANTENGASE EN LUGAR FRESCO Y SECO <br> NO USAR PASADA LA FECHA DE DESINFECCION</label>
                            </td>
                        </tr>
                        <tr><td colspan="5"></td></tr>
                    </table>
                </div>

                <div class="divPrint">
                    <table>
                        <tr>
                            <td>
                                <input type="hidden" name="idE4" id="idE4" value="4">
                                <input type="image" id="imprimir" title="IMPRIMIR" src="/matrix/images/medical/dircie/printventosad.png" width="25" height="25" style="margin-left: 350px"
                                       onclick="verificarNulos4(); return false">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
