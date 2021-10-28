<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <title>Registro de Reclamos - Habeas Data</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="estilosHabeas.css" rel="stylesheet">
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
        $institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
        $wactualiz = 1;
        encabezado( "Registro de Reclamos - Habeas Data", $wactualiz, $institucion->baseDeDatos );


        $conex = obtenerConexionBD("matrix");
    }
    include_once("habeasdb/libreriaHabeas.php");

    $fecha_actual = date('Y-m-d');
    ?>
</head>
<body>
<div class="container" style="margin-top: -30px; margin-left: 15px">
    <div id="loginbox" style="margin-top:50px;" class="">
        <div class="panel panel-info" >
            <div class="panel-heading">
            </div>

            <div style="padding-top:30px" class="panel-body" >

                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                </div>

                <form id="loginform" name="loginform" class="form-horizontal" role="form" method="post" action="guardarHabeas.php" onsubmit="return confirm('Esta seguro de guardar en este momento?')">

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>FECHA DE REGISTRO</label></span>
                            <input id="fecha" type="date" class="form-control" style="width: 140px" name="fecha" value="<?php echo $fecha_actual ?>" readonly>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 190px"><label>TIPO</label></span>
                            <select id="tipo" name="tipo" class="form-control" style="width: 750px" required>
                                <?php
                                $consespe2 = mysql_queryV("select Codigo,Nombre from habeasdb_000009 WHERE Tipo = 'P'");
                                while($datoespe2 = mysql_fetch_array($consespe2))
                                {
                                    echo "<option value='".$datoespe2['Nombre']."'>".$datoespe2['Nombre']."</option>";
                                    $descripcionespe2=$datoespe2['Nombre'];
                                }
                                ?>
                                <option selected="selected" disabled value="">Seleccione...</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px; width: 1070px" class="input-group">
                        <span class="input-group-addon">
                            <label>DESCRIPCION</label>
                            <br>
                            <textarea id="descripcion" name="descripcion" rows="2" cols="110"></textarea>
                        </span>
                    </div>

                    <br>

                    <div style="margin-bottom: 25px; width: 1070px" class="input-group">
                        <span class="input-group-addon"><label>DATOS DE QUIEN RECLAMA</label></span>
                    </div>

                    <div style="margin-bottom: 25px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 110px"><label>NOMBRE</label></span>
                            <input id="nombre" type="text" class="form-control" style="width: 470px" name="nombre" value="<?php echo $nombre ?>">

                            <div class="input-group-addon" style="background-color: #ffffff; width: 55px; border: none"></div>

                            <span class="input-group-addon"><label>CEDULA</label></span>
                            <input id="cedula" type="text" class="form-control" style="width: 200px" name="cedula" value="<?php echo $edad ?>">

                            <div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 25px; width: 1070px" class="input-group">
                        <span class="input-group-addon">
                            <label>DATOS DE CONTACTO</label>
                            <br>
                            <textarea id="contacto" name="contacto" rows="2" cols="110"></textarea>
                        </span>
                    </div>

                    <br>

                    <div style="margin-bottom: 25px" class="input-group">
                        <span class="input-group-addon" style="width: 190px"><label>ESTADO</label></span>
                        <select id="estado" name="estado" class="form-control" style="width: 300px" required>
                            <option>En Proceso</option>
                            <option>Finalizado</option>
                            <option selected="selected" disabled value="">Seleccione...</option>
                        </select>

                        <div class="input-group-addon" style="background-color: #ffffff; width: 55px; border: none"></div>

                        <div class="input-group">
                            <span class="input-group-addon" style="width: 110px"><label>USUARIO QUE REGISTRA</label></span>
                            <input id="usuarioRegistra" type="text" class="form-control" style="width: 300px" name="usuarioRegistra" value="<?php echo $wuse ?>" readonly>
                        </div>
                    </div>

                    <div style="margin-top:20px" class="form-group" align="center">
                        <div class="col-sm-12 controls">
                            <input type="submit" class="btn btn-success" value="GUARDAR">
                            <br><br>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>