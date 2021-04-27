<html>
    <head>
        <title>Medidas - Listado</title>
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
            <h1>Listado de Medidas</h1>
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
            <table style="width:100%; text-align:center;">
                <?php
                    //No muestro tabla si no hay medidas
                    if(isset($aMedidas) && count($aMedidas)>0)
                    {
                        //Títulos de tabla
                        echo("<tr class='encabezadoTabla'>
                                <th>C&oacute;digo</th>
                                <th>Nombre</th>
                                <th>Descripci&oacute;n</th>
                                <th>Unidad</th>
                                <th>Enviar Notificaci&oacute;n</th>
                                <th>Acci&oacute;n</th>
                            </tr>");

                        //Elementos de la tabla de Medidas
                        $iIndice = 0;
                        foreach ( $aMedidas as $oMedida ) 
                        {
                            //Defino la clase a partir del índice.
                            $iIndice++;
                            $sClaseFila = ($iIndice % 2 == 0) ? "fila2" : "fila1";
                            //Muestro la fila
                            echo "<tr class='".$sClaseFila."'>
                                    <td>
                                        <a href='medidas.php?action=viewMedida&id=".$oMedida['id']."'>".$oMedida['codigo']."</a>
                                    </td>
                                    <td>
                                        ".$oMedida['nombre']."
                                    </td>
                                    <td>
                                        ".$oMedida['descripcion']."
                                    </td>
                                    <td>
                                        ".$oMedida['unidad']."
                                    </td>
                                    <td>
                                        ".$oMedida['enviarnotificaciontexto']."
                                    </td>
                                    <td>
                                        <a href='medidas.php?wemp_pmla=".$wemp_pmla."&action=editMedida&id=".$oMedida['id']."'>Editar</a> / 
                                        <a href='medidas.php?wemp_pmla=".$wemp_pmla."&action=deleteMedida&id=".$oMedida['id']."'>Eliminar</a>
                                    </td>
                                </tr>";
                        }
                        
                    }
                    else
                    {
                        echo "<p>No se enontraron registros</p>";
                    }
                ?>
            </table>
            <p>
                <br><br>
                <a href='medidas.php?wemp_pmla=<?php echo $wemp_pmla; ?>&action=createMedida'>Agregar nuevo</a>
            </p>
        </div>
    </body>
</html>