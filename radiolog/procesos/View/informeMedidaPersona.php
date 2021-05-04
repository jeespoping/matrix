<html>
    <head>
        <title>Medidas de personal - B&uacute;squeda</title>
        <meta charset="utf-8">
        <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
        <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
        <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
        <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
        <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
        <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
        <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
        <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

        <!-- Inicio estilos css -->
        <style type="text/css">
            .blinkProcesoActual {
                background-color: #FF9664;
            }
            .btn-primary:hover {
                color: #fff;
                background-color: #286090;
                border-color: #204d74;
            }

            a {
                color: #337ab7;
                text-decoration: none;
            }

            body{
                width: 95%;
                height: 95%;
                font-size: 11px
            }

            .page-header {
                margin: -10px 0 20px;
            }

            table {
            border-collapse: separate;
            border-spacing: 2px;
            }
            .ui-multiselect { background:white; background-color:white; color: black; font-weight: normal; font-family: verdana; border-color: gray; border: 3px; height:20px; width:450px; overflow-x:hidden;text-align:left;font-size: 10pt;}

            .ui-multiselect-menu { background:white; background-color:white; color: black; font-weight: normal; font-size: 10pt;height: 450px;}

            .ui-multiselect-header { background:white; background-color:lightgray; color: black;font-weight: normal;}


            .ui-multiselect-checkboxes {
                max-height: 400px;
            }


        </style>
    </head>
    <body>
        <?php
            //Validación de la variable de sesión
            if(!isset($_SESSION['user']) )
            {
                echo "<br /><br /><br /><br />
                    <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
                            [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
                    </div>";
                return;
            }
            include_once("root/comun.php");
            $wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
            global $conex;

            //----------------------------------------------------------ENCABEZADO--------------------------------------------------------
            $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
            $wbasedato = strtolower( $institucion->baseDeDatos );

            $wentidad = $institucion->nombre;

            if ($wemp_pmla == 01)
            {
                encabezado("MEDIDAS", "2021-04-21", $wbasedato );
            }
            else
            {
                encabezado("MEDIDAS", "2021-04-21", "logo_".$wbasedato );
            }
		    //--------------------------------------------------------- FIN ENCABEZADO ---------------------------------------------------
        ?>
        <div class="container" style="text-align:center">
            <h1>B&uacute;squeda de Medidas por Persona</h1>
            <input type='HIDDEN' name= 'wemp_pmla' id= 'wemp_pmla' value='<?php echo $wemp_pmla?>'>
            <?php
                //Llamo los mensajes flash
                if(isset($_SESSION['error']))
                {
                    echo "<center><div align='center' class='fondoRojo' style='width:510px'><b><blink>".$_SESSION['error']."</blink></b></div></center>"; 
                    echo "<br><br>";
                    unset($_SESSION['error']);
                }
                elseif(isset($_SESSION['success']))
                {
                    echo "<center><div align='center' class='fondoVerde' style='width:510px'><b><blink>".$_SESSION['success']."</blink></b></div></center>"; 
                    echo "<br><br>";
                    unset($_SESSION['success']);
                }
            ?>

            <!-- Tabla de Medidas-->
            <center>
                <form method="post">
                    <table>
                        <thead>
                            <tr class="encabezadoTabla">
                                <th>C&oacute;digo Medida</th>
                                <th>Persona</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="fila2" style="text-align:center;">
                                <td>
                                    <select id="idmedida" name="idmedida" required>
                                        <option value="">--Seleccione una medida--</option>
                                        <?php
                                            $sMedidaSelect = (isset($_SESSION['idmedida'])) ? $_SESSION['idmedida'] : $aMedidas[0]['id'];
                                            foreach ($aMedidas as $oMedida) {
                                                $sSelected = ($sMedidaSelect == $oMedida['id']) ? 'selected' : '';
                                                echo "<option value='".$oMedida['id']."' ".$sSelected.">".$oMedida['codigo']." - ".$oMedida['nombre']."</option>";
                                            }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    </br>B&uacute;squeda:
                                    <?php $sTipoBusqueda = (isset($_SESSION['tipobusqueda'])) ? $_SESSION['tipobusqueda'] : "documento";  ?>
                                    <select name="tipobusqueda" id="tipobusqueda">
                                        <option value="documento" <?= ($sTipoBusqueda == "documento") ? "selected" : "" ?> >Documento</option>
                                        <option value="codigo" <?= ($sTipoBusqueda == "codigo") ? "selected" : "" ?> >C&oacute;digo</option>
                                    </select>
                                    <input type="text" name="codigopersona" id="codigopersona" value="<?= (isset($_SESSION['codigopersona'])) ? $_SESSION['codigopersona'] : null  ?>">
                                    </br></br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </br>
                    <input type="submit" name="buscar" id="buscar" value="Buscar">
                    <input type="submit" name="limpiar" id="limpiar" value="Limpiar">
                </form>
            </center>
            <table style="width:100%; text-align:center;">
                <?php
                    //No muestro tabla si no hay medidas
                    if(isset($aMedidasPersonal) && count($aMedidasPersonal)>0)
                    {
                        //Títulos de tabla
                        echo("<tr class='encabezadoTabla'>
                                <th>Fecha</th>
                                <th>Medida</th>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Valor medida</th>
                                <th>Fecha de registro</th>
                                <th>Persona de registro</th>
                            </tr>");

                        //Elementos de la tabla de Medidas
                        $iIndice = 0;
                        foreach ( $aMedidasPersonal as $oMedidaPersonal ) 
                        {
                            //Defino la clase a partir del índice.
                            $iIndice++;
                            $sClaseFila = ($iIndice % 2 == 0) ? "fila2" : "fila1";
                            //Muestro la fila
                            echo "<tr class='".$sClaseFila."'>
                                    <td>
                                        ".$oMedidaPersonal['fechahoramedida']."
                                    </td>
                                    <td>
                                        ".$oMedidaPersonal['medida']."
                                    </td>
                                    <td>
                                        ".$oMedidaPersonal['documento']."
                                    </td>
                                    <td>
                                        ".$oMedidaPersonal['nombreusuario']."
                                    </td>
                                    <td>
                                        ".$oMedidaPersonal['valor']."
                                    </td>
                                    <td>
                                        ".$oMedidaPersonal['fecharegistro']."
                                    </td>
                                    <td>
                                        ".$oMedidaPersonal['personaregistro']."
                                    </td>
                                </tr>";
                        }
                        
                    }
                ?>
            </table>
        </div>
    </body>
</html>