<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ALTA TEMPRANA GRD - MATRIX</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="http://mx.lasamericas.com.co/matrix/calidad/procesos/GrdStyle.css" rel="stylesheet">
    <script src="GrdJs.js"></script>
    <script>
        function modificar(idRegistro, accion)
        {
            // definimos la anchura y altura de la ventana
            var altura=300;
            var anchura=800;
            // calculamos la posicion x e y para centrar la ventana
            var y=parseInt((window.screen.height/2)-(altura/2));
            var x=parseInt((window.screen.width/2)-(anchura/2));
            // mostramos la ventana centrada

            window.open("GrdProcess.php?accion="+accion.value+'&idRegistro='+idRegistro,
            target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
        }

        function crear(accion,subaccion)
        {
            // definimos la anchura y altura de la ventana
            var altura=350;
            var anchura=800;
            // calculamos la posicion x e y para centrar la ventana
            var y=parseInt((window.screen.height/2)-(altura/2));
            var x=parseInt((window.screen.width/2)-(anchura/2));
            // mostramos la ventana centrada

            window.open("GrdProcess.php?accion="+accion.value+'&subaccion='+subaccion,
                target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
        }
    </script>
    <?php
    //PUBLICACION MATRIX:
    ///*
    include("conex.php");
    include("root/comun.php");
    
    $institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
    $wactualiz = "1";
    encabezado( "Matrix - Predicción del Alta - GRD", $wactualiz, $institucion->baseDeDatos );

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
        mysql_select_db("matrix");
        $conex = obtenerConexionBD("matrix");
    }
    //*/
    //include('../MATRIX/include/root/conex.php'); //publicacion local
    //include('../MATRIX/include/root/comun.php'); //publicacion local
    //mysql_select_db('matrix'); //publicacion local
    //$conex = obtenerConexionBD('matrix'); //publicacion local

    $tipoAtencion = $_POST['tipoAtencion']; $selector1 = $_POST['selector1'];   $tipoat = $_POST['toggle_option']; $prodx2 = $_POST['codigo'];
    //CONSULTAR SI EL USUARIO TIENE AUTORIZACION PARA MODIFICAR (calidad_000013):
    $queryUser = "select count(id) from calidad_000013 WHERE CodUsuario = '$wuse' AND Codest = 'on'";
    $commitUser = mysql_query($queryUser) or die (mysql_errno()." - en el query: ".$queryUser." - ".mysql_error());
    $datoUser = mysql_fetch_array($commitUser, $conex);
    $conteoUser = $datoUser[0];

    $anoActual = date('Y');                 $anoPasado = $anoActual - 1;
    $fecha_inicial = $anoPasado.'-01-01';   $fecha_final = $anoPasado.'-12-31';
    ?>
</head>

<body>
<div class="container">
    <div class="panel panel-info contenido">
        
            
        </div>

        <form method="post">
            <div class="input-group selectDispo">
                <span class="input-group-addon input-sm">
                    <label for="tipoAtencion">&nbsp;&nbsp;&nbsp;&nbsp;TIPO DE ATENCION:&nbsp;&nbsp;&nbsp;</label>
                </span>
                <select id="tipoAtencion" name="tipoAtencion" class="form-control form-sm" style="height: 100%" onchange="this.form.submit()">
                    <option value="1">Medica</option>
                    <option value="2">Quirurgica</option>
                    <option selected disabled>Seleccione...</option>
                </select>
            </div>
            <input type="hidden" id="selector1" name="selector1" value="0">
        </form>

        <!-- MOSTRAR TIPO ATENCION SELECCIONADA: -->
        <section style="text-align: center">
            <?php
            if($selector1 != null)
            {
                if($tipoAtencion != null)
                {
                    if($tipoAtencion == 1){ $descAtencion = 'Medica';}
                    else($descAtencion = 'Quirurgica')
                    ?>
                    <h3>TIPO DE ATENCION SELECCIONADA: <?php echo ' '.$descAtencion ?></h3>
                    <?php
                }
            }
            else
            {
                ?>
                <h3>Debe seleccionar el tipo de atencion...</h3>
                <?php
            }
            ?>
        </section>

        <?php
        if($selector1 != null)
        {
            ?>
            <form method="post">
                <div id="divDefault" class="input-group" style="margin-top: 10px; border: none">
                    <table class="tblParametros" style="margin-top: 5px; width: 100%; border: none; margin-right: auto; margin-left: -80px" border="0">
                        <tr align="right" style="line-height: 0">
                            <td colspan="4"><label style="text-align: center; margin-right: 50px">Servicio de Ingreso:</label></td>
                        </tr>
                        <tr>
                            <td style="width: 25%"></td>
                            <td>
                                <?php
                                switch($tipoAtencion)
                                {
                                    case '1':
                                    ?>
                                    <table style="margin-top: 5px">
                                        <tr>
                                            <td>
                                                <input type="hidden" name="tipoAten" id="tipoAten" value="<?php echo $tipoAtencion ?>">
                                                <?php
                                                if($prodx2 != null)
                                                {
                                                    ?>
                                                    <input id="country_id" name="codigo" class="form-control input-sm" style="width: 650px; margin-top: -5px"
                                                           value="<?php echo $prodx2 ?>" />
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <input id="country_id" name="codigo" class="form-control input-sm" style="width: 650px; margin-top: -5px"
                                                           placeholder="DESCRIPCION CIE-10" list="dx" autocomplete="off" />
                                                    <?php
                                                }
                                                ?>
                                                <datalist style="height: 100px" id="dx">
                                                    <?php
                                                    $consulta = "select Codigo,Descripcion from root_000011 WHERE Estado = 'on' ORDER BY Descripcion ASC";
                                                    $commit = mysql_query($consulta, $conex) or die (mysql_errno()." - en el query: ".$consulta." - ".mysql_error());
                                                    while($dato = mysql_fetch_assoc($commit))
                                                    {
                                                        $descripcion = $dato['Descripcion']; $codigoR11 = $dato['Codigo'];
                                                        $descripcion = $codigoR11.'-'.$descripcion;
                                                        ?><option value="<?php echo $descripcion  ?>"><?php
                                                    }
                                                    ?>
                                                </datalist>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php
                                    break;
                                    case '2':
                                    ?>
                                    <table style="margin-top: 5px" border="0">
                                        <tr>
                                            <td>
                                                <input type="hidden" name="tipoAten" id="tipoAten" value="<?php echo $tipoAtencion ?>">
                                                <?php
                                                if($prodx2 != null)
                                                {
                                                    ?>
                                                    <input type="text" id="country_id" name="codigo" class="form-control input-sm"
                                                           style="width: 650px; margin-top: -5px" value="<?php echo $prodx2 ?>">
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <input type="text" id="country_id" name="codigo" class="form-control input-sm"
                                                           style="width: 650px; margin-top: -5px" placeholder="DESCRIPCION CUPS:" list="prodx" autocomplete="off">
                                                    <?php
                                                }
                                                ?>
                                                <datalist id="prodx">
                                                    <?php
                                                    $consultaa = "select Codigo,Nombre from root_000012 WHERE Estado = 'on' ORDER BY Nombre ASC";
                                                    $commita = mysql_query($consultaa, $conex) or die (mysql_errno()." - en el query: ".$consultaa." - ".mysql_error());
                                                    while($datoa = mysql_fetch_assoc($commita))
                                                    {
                                                        $descripciona = $datoa['Nombre'];   $codigoR12 = $datoa['Codigo'];
                                                        $descripciona = $codigoR12.'-'.$descripciona;
                                                        ?><option value="<?php echo $descripciona  ?>"><?php
                                                    }
                                                    ?>
                                                </datalist>
                                            </td>
                                        </tr>
                                    </table>
                                    <?php
                                    break;
                                }
                                ?>
                            </td>
                            <td>&ensp;</td>
                            <td>
                                <div class="wrapper">
                                    <div class="toggle_radio">
                                        <?php
                                        if($tipoat == 'U')
                                        {
                                            ?>
                                            <input type="radio" class="toggle_option" id="first_toggle" name="toggle_option" value="U" checked>
                                            <input type="radio" class="toggle_option" id="second_toggle" name="toggle_option" VALUE="P">
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <input type="radio" class="toggle_option" id="first_toggle" name="toggle_option" value="U">
                                            <input type="radio" class="toggle_option" id="second_toggle" name="toggle_option" VALUE="P" checked>
                                            <?php
                                        }
                                        ?>
                                        <label for="first_toggle"><p>Urgencias</p></label>
                                        <label for="second_toggle"><p>Otro Servicio</p></label>
                                        <div class="toggle_option_slider">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="input-group" style="margin-top: 5px; margin-bottom: 5px; text-align: center; margin-left: 15px">
                                    &ensp;
                                    <input type="submit" class="btn btn-info btn-sm" value="> > >" title="Proceder" style="width: 120px">
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <input type="hidden" name="selector1" value="<?php echo $selector1 ?>">
                <input type="hidden" name="tipoAtencion" value="<?php echo $tipoAtencion ?>">
            </form>
            <?php
        }
        ?>
    </div>

    <?php
    $prodx = $_POST['codigo'];
    $urgprog = $_POST['toggle_option'];
    $cod = explode('-',$prodx); $codigo = $cod[0]; //$codigo = 'T671';
    $desc = $cod[1];
    if($tipoAtencion == 1){$tipoAtent = 'M'; $loSelecciondo = 'DIAGONOSTICO';}
    else{$tipoAtent = 'Q'; $loSelecciondo = 'PROCEDIMIENTO';};

    if($codigo != null)
    {
        ?>
        <div class="panel panel-info contenido">
            <h4 style="text-align: center"><?php echo $loSelecciondo.' ' ?> SELECCIONADO:<br><?php echo $prodx ?></h4>

            <table align="center" border="0" style="margin-bottom: 10px">
                <?php
                $queryCodGrd = "select Codigogrd from calidad_000010 WHERE Codigo = '$codigo'";
                $commitCodGrd = mysql_query($queryCodGrd, $conex) or die (mysql_errno()." - en el query: ".$queryCodGrd." - ".mysql_error());
                $datoCodGrd = mysql_fetch_array($commitCodGrd);
                $codGrd = $datoCodGrd['Codigogrd'];
                $codGrd = trim($codGrd);
                //echo 'QUERY CALIDAD 10:'.$queryCodGrd.'<br>';

                $queryCalidad_11 = "select * from calidad_000011 WHERE Tipogrd = '$tipoAtent' AND Tipoate = '$urgprog' AND Codgrd = '$codGrd'";
                $commitCalidad_11 = mysql_query($queryCalidad_11, $conex) or die (mysql_errno()." - en el query: ".$queryCalidad_11." - ".mysql_error());
                $conteo_11 = mysql_num_rows($commitCalidad_11);  //NUMERO REGISTROS
                //echo 'QUERY CALIDAD 11:'.$queryCalidad_11;

                if($conteo_11 > 0)
                {
                    ?>
                    <thead style="background-color: #C3D9FF">
                    <tr align="center" style="font-weight: bold">
                        <td>NIVEL DE SEVERIDAD &ensp;</td>
                        <td>NOMBRE GRD &ensp;</td>
                        <td>&ensp;&ensp;&ensp;&ensp;ALTAS &ensp;&ensp;&ensp;&ensp;</td>
                        <td>&ensp;&ensp;&ensp;&ensp;ESTANCIA MEDIA &ensp;&ensp;&ensp;&ensp;</td>
                        <td>&ensp;</td>
                    </tr>
                    </thead>
                    <?php
                    while($datosCalidad_11 = mysql_fetch_array($commitCalidad_11))
                    {
                        $codGrd1 = $datosCalidad_11['Codgrd1']; $altDep = $datosCalidad_11['Altdep'];   $estMedDep = $datosCalidad_11['Estmeddep'];
                        $codSeveridad = substr($codGrd1,-1);    $idCalidad11 = $datosCalidad_11['id'];

                        $queryCalidad_12 = "select * from calidad_000012 WHERE Codgrd1 = '$codGrd1'";
                        $commitCalidad_12 = mysql_query($queryCalidad_12) or die (mysql_errno()." - en el query: ".$queryCalidad_12." - ".mysql_error());
                        $datosCalidad_12 = mysql_fetch_array($commitCalidad_12, $conex);
                        $descCalidad_12 = $datosCalidad_12['Grdnom1'];
                        ?>
                        <tr align="left" class="alternar" style="border-bottom: groove; border-bottom-color: #EEEEEE">
                            <td align="center"><?php echo $codSeveridad ?>&ensp;</td>
                            <td><?php echo $codGrd1.'-'.$descCalidad_12 ?>&ensp;</td>
                            <td align="center"><?php echo $altDep ?></td>
                            <td align="center"><?php echo $estMedDep ?>&ensp;</td>
                            <td>
                                <?php
                                //si el usuario esta autorizado para modificar:
                                if($conteoUser > 0)
                                {
                                    ?>
                                    <input type="hidden" id="accion" name="accion" value="modificar">
                                    <input type="button" class="btn btn-info btn-sm" value="<" title="Modificar" style="width: -1px"
                                           onclick="modificar(<?php echo $idCalidad11 ?>, accion)">
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                else
                {
                    ?>
                    <h4 style="text-align: center; color: #C60000">NO SE ENCONTRARON ANALISIS PREVIOS PARA ESTE <?php echo $loSelecciondo ?></h4>
                    <h3 style="text-align: center; color: #428BCA">EL ANALISIS AUTOMATICO DESDE MATRIX ES EL SIGUIENTE:</h3>

                    <?php
                    //TIPO ATENCION M:
                    if($tipoAtencion == 1)
                    {
                        $queryCliame = "select count(*) CONTEO, SUM(Egrest) ESTANCIA from cliame_000109, cliame_000108 where Diacod = '$codigo'
                                        and Diatip = 'P'
                                        and Diaegr = 'on'
                                        and Diahis = Egrhis
                                        and Diaing = Egring
                                        and Egrfee BETWEEN '$fecha_inicial' AND '$fecha_final'
                                        and Egrcae = 'A'";
                        //echo 'QUERY CLIAME MEDICO ='.$queryCliame.'<br>';
                    }
                    //TIPO ATENCION Q:
                    elseif($tipoAtencion == 2)
                    {
                        $queryCliame = "select count(*) CONTEO, SUM(Egrest) ESTANCIA from cliame_000110, cliame_000108 WHERE Procod = '$codigo'
                                        and Protip = 'P'
                                        and Prohis = Egrhis
                                        and Proing = Egring
                                        and Egrfee BETWEEN '$fecha_inicial' AND '$fecha_final'";
                        //echo 'QUERY CLIAME QUIRURGICO ='.$queryCliame.'<br>';
                    }
                    $commitCliame108 = mysql_query($queryCliame, $conex) or die (mysql_errno()." - en el query: ".$queryCliame." - ".mysql_error());
                    $datosCliame108 = mysql_fetch_array($commitCliame108, $conex);
                    $conteo108 = $datosCliame108['CONTEO']; $estancia108 = $datosCliame108['ESTANCIA'];
                    if($conteo108 > 0)
                    {
                        $promEstacia = ($estancia108/$conteo108);
                        $promEstacia = round($promEstacia,2);
                    }
                    else
                    {
                        $promEstacia = 0;
                    }
                    ?>
                    <thead style="font-weight: bold">
                    <tr style="border-bottom: groove; border-bottom-color: #EEEEEE">
                        <td align="center">
                            <label style="font-size: medium">AÑO DE ANALISIS:</label>
                            <label style="color: #0078D4; font-size: 30px">
                                <?php
                                //echo date('Y');
                                echo $anoPasado
                                ?>
                            </label>
                        </td>
                        <td>&ensp;</td>
                        <td align="left">&ensp;</td>
                    </tr>
                    <tr class="alternar" style="border-bottom: groove; border-bottom-color: #EEEEEE">
                        <td><label>TOTAL ALTAS <br> ESTE <?php echo $loSelecciondo ?>:</label></td>
                        <td>&ensp;</td>
                        <td align="right"><h4 style="color: #0078D4"><?php echo $conteo108 ?></h4></td>
                    </tr>
                    <tr class="alternar" style="border-bottom: groove; border-bottom-color: #EEEEEE">
                        <td><label>ESTANCIA PROMEDIO <br> ESTE <?php echo $loSelecciondo ?>:</label></td>
                        <td>&ensp;</td>
                        <td align="right"><h4 style="color: #0078D4"><?php echo $promEstacia ?></h4></td>
                    </tr>
                    </thead>
                    <?php
                }
                ?>
            </table>
        </div>
        <?php
    }
    //si el usuario esta autorizado para modificar:
    if($conteoUser > 0)
    {
        ?>
        <div class="panel panel-info maestros">
            <br>
            <h4 style="text-align: center">MANTENIMIENTO DE MAESTROS:</h4>
            <input type="hidden" id="accion2" name="accion2" value="maestros">
            <input type="button" class="btn btn-primary btn-xs" value="Maestro de Codigos GRD (calidad_000009)" onclick="crear(accion2,'calidad09')">
            <br>
            <input type="button" class="btn btn-success btn-xs" value="Relacion Dx - CUPs / GRD (calidad_000010)" onclick="crear(accion2,'calidad10')">
            <br>
            <input type="button" class="btn btn-info btn-xs" value="Nivel de Severidad (calidad_000011)" onclick="crear(accion2,'calidad11')">
            <br>
            <input type="button" class="btn btn-warning btn-xs" value="Maestro de Codigos GRD1 (calidad_000012)" onclick="crear(accion2,'calidad12')">
            <br><br>
        </div>
        <?php
    }
    ?>
</div>
</body>
</html>