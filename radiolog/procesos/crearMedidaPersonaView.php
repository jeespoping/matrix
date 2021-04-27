<html>
    <head>
        <title>Medida - Agregar Medida</title>
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
        <script type="text/javascript" src="medidapersona.js"></script>
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
        <div class="container" style="text-align:center; align:center;">
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
            <form method="post">
                <input type='HIDDEN' name= 'wemp_pmla' id= 'wemp_pmla' value='<?php echo $wemp_pmla?>'>
                <table style="width:100%;">
                    <thead>
                        <tr class="fila1">
                            <th colspan="5"><h2>Agregar medida a persona<h2></th>
                        </tr>
                        <tr class="encabezadoTabla">
                            <th>C&oacute;digo Medida</th>
                            <th style="width:40%">Persona</th>
                            <th>Fecha medida</th>
                            <th>Hora medida</th>
                            <th>Valor Medida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="fila2" style="text-align:center;">
                            <td>
                                <select id="idmedida" name="idmedida" required>
                                    <option value="">--Seleccione una medida--</option>
                                    <?php
                                        $sMedidaSelect = (isset($_SESSION['idmedida'])) ? $_SESSION['idmedida'] : $aMedidas[0]['codigo'];
                                        foreach ($aMedidas as $oMedida) {
                                            $sSelected = ($sMedidaSelect == $oMedida['codigo']) ? 'selected' : '';
                                            echo "<option value='".$oMedida['codigo']."' ".$sSelected.">".$oMedida['codigo']." - ".$oMedida['nombre']."</option>";
                                        }
                                    ?>
                                </select>
                            </td>
                            <td style="width:40%">
                                </br>B&uacute;squeda:
                                <?php $sTipoBusqueda = (isset($_SESSION['tipobusqueda'])) ? $_SESSION['tipobusqueda'] : "documento";  ?>
                                <select name="tipobusqueda" id="tipobusqueda">
                                    <option value="documento" <?= ($sTipoBusqueda == "documento") ? "selected" : "" ?> >Documento</option>
                                    <option value="codigo" <?= ($sTipoBusqueda == "codigo") ? "selected" : "" ?> >C&oacute;digo</option>
                                    <option value="nombre" <?= ($sTipoBusqueda == "nombre") ? "selected" : "" ?>>Nombre</option>
                                </select>
                                <input type="text" name="codigopersona" id="codigopersona" value="<?= (isset($_SESSION['codigopersona'])) ? $_SESSION['codigopersona'] : null  ?>">
                                <!-- <input type="submit" name="busqueda" id="busqueda" value="Buscar" onclick="buscarPersona()"> -->
                                <a onclick="buscarPersona(false)">Buscar</a> | 
                                <a onclick="buscarPersona(true)">Limpiar</a>
                                </br></br>
                                Seleccione una persona:
                                <select style="max-width:60%; width:60%" id="personasselect" name="personasselect" required>
                                    <option value="" >--Seleccione una persona--</option>
                                    <?php
                                        $sCodigoPersonaSelect = (isset($_SESSION['personasselect'])) ? $_SESSION['personasselect'] : null;
                                        foreach ($aPersonas as $oPersona) {
                                            $sSelected = ($sCodigoPersonaSelect == $oPersona['codigo']) ? 'selected' : '';
                                            echo "<option value='".$oPersona['codigo']."' ".$sSelected.">".$oPersona['codigo']." - ".$oPersona['nombre']." (".$oPersona['documento'].")"."</option>";
                                        }
                                    ?>
                                </select>
                                </br></br>
                            </td>
                            <td>
                                <input type="date" name="fechamedida" id="fechamedida" required value="<?= (isset($_SESSION['fechamedida'])) ? $_SESSION['fechamedida'] : null  ?>">
                            </td>
                            <td>
                                <input type="time" name="horamedida" id="horamedida" required value="<?= isset($_SESSION['horamedida']) ? $_SESSION['horamedida'] : "00:00"  ?>">
                            </td>
                            <td>
                                <input type="number" name="valormedida" id="valormedida" required step="0.01" value="<?= (isset($_SESSION['valormedida'])) ? $_SESSION['valormedida'] : null  ?>">
                            </td>
                        </tr>
                    </tbody>
                </table>
                </br>
                <center>
                    <input type="checkbox" name="seguiringresando" id="seguiringresando" <?= (isset($_SESSION['seguiringresando'])) ? $_SESSION['seguiringresando'] : null ?>>
                    <label for="seguiringresando"> Continuar ingresando medidas al pulsar "Guardar"</label><br>
                </center>
                </br>
                <input type="submit" name="add" id="add" value="Guardar">
                <a href='medidas.php?wemp_pmla=<?php echo $wemp_pmla; ?>'>Cancelar</a>
            </form>
        </div>
    </body>
</html>