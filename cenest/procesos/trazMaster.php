<?php
/**
 * Vista principal de funcionalidad para la trazabilidad de dispositivos en la central de esterilización
 * Autor inicial: William Atehortua
 * @author final Julian Mejia - julian.mejia@lasamericas.com.co
 */

    include_once("conex.php");
    include_once("root/comun.php");
    include_once("trazFunctions.php");
    
?>
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TRAZABILIDAD DE DISPOSITIVOS - MATRIX</title>
    <!-- Librerias de Jquery -->
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>
    <!-- Libreria Bootstrap para la interfaz -->
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
    <link href="trazStyle.css" rel="stylesheet">
    <!-- <link href="../../../include/root/bootstrap4/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="../../../matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet"><!-- PESTAÑAS -->
    <link href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" rel="stylesheet"/><!--Estilo para el calendario-->
    <script src="../../../include/root/jquery.min.js"></script>
    <script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <script src="../../../include/root/bootstrap.min.js"></script>
    <!--------------------------------------------->
  
    <!-- Se incluye archivo js donde se hacen la mayoria de funciones Javascript -->
    <script src="trazJs.js" type="text/javascript"></script>
    <script>
        // funcion que permite estar escuchando constantemente para el datepicker y para el autocomplete.
        $(function() {
            $( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
        
            $( "#autocomplete" ).autocomplete({
                source: function( request, response ) {
                    $.ajax({
                    url: "trazProcess.php",
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: request.term,
                        wemp_pmla : $("#wemp_pmla").val(),
                        codCcoDispo : $("#filtroCco").val(),
                        Coddispo : $("#filtroDispo").val(),
                        accion   : "autocomplete",
                    },
                    success: function( data ) {
                        response( data );
                    }
                    });
                },
                select: function (event, ui) {
                    $('#autocomplete').val(ui.item.label); // display the selected textt
                    $('#filtroCodReu').val(ui.item.value); // save selected id to input
                    return false;
                },
                focus: function(event, ui){
                    ui.item.label.todos = 'TODOSREUSOS';
                    $( "#autocomplete" ).val( ui.item.label );
                    $( "#filtroCodReu" ).val( ui.item.value );
                    return false;
                },
            });
        });

function habilitarCod(id) {
    //cboxEdit
    let checkBox = document.getElementById("cboxEdit" + id);
    let element = document.getElementById("codReusoIns" + id);
    if (checkBox.checked){
        element.readOnly = false; 
    }else{
        element.readOnly = true; 
    }
}

function traerUsuario(){
    //numCcUsuario
    wemp_pmla = document.getElementById('wemp_pmla').value;
    docUsuario = document.getElementById('numCcUsuario').value;
    accion = 'findUserName';
    let jsonParameters = {
        wemp_pmla,
        docUsuario,
        accion  
    };
    $.ajax({
        type: 'POST',
        url: 'trazProcess.php',
        data: jsonParameters,
            success: function(respuesta) {
                data = JSON.parse(respuesta);
                if (data.usuario != ''){
                    let text = $('#nomUsuario').html(data.usuario).text();
                    $("#nomUsuario").val(text);
                }
            },
            error: function() {
                alert("Error al traer datos de usuario");
            }
        });
}

function llenarSelectReu(jsonCodes, codItCriterio,ccoUnidadCriterio){
    wemp_pmla = document.getElementById('wemp_pmla').value;
    criterio = document.getElementById('filtroReuTraz').value;
    accion = 'findCodReuCriterio';
    let jsonParameters = {
        wemp_pmla,
        criterio,
        codItCriterio,
        ccoUnidadCriterio,
        jsonCodes,
        accion  
    };
    $.ajax({
        type: 'POST',
        url: 'trazProcess.php',
        data: jsonParameters,
            success: function(respuesta) {
                console.log(respuesta);
                $("#reusoSel").html(respuesta);
                $("#reusoSel").append("<option selected disabled>Seleccione...</option>");
                $("#reusoSel").css("border-color","#c23616");
                $("#reusoSel").css("backgroundColor","#f5f6fa");
            },
            error: function() {
                alert("Error al traer datos de usuario");
            }
        });
}


    </script> 
       <!-- CSS para los switches -->
       <style type="text/css" media="screen">
            :root {
                --color-green: #3ae374;
                --color-red: #ff3838;
                --color-button: #dfe4ea;
                --color-black: #000;
            }
            .switch-button {
                display: inline-block;
            }
            .switch-button .switch-button__checkbox {
                display: none;
            }
            .switch-button .switch-button__label {
                background-color: var(--color-red);
                width: 4rem;
                height: 2rem;
                border-radius: 2rem;
                display: inline-block;
                position: relative;
            }
            .switch-button .switch-button__label:before {
                transition: .2s;
                display: block;
                position: absolute;
                width: 2rem;
                height: 2rem;
                background-color: var(--color-button);
                content: '';
                border-radius: 50%;
                box-shadow: inset 0px 0px 0px 0px var(--color-black);
            }
            .switch-button .switch-button__checkbox:checked + .switch-button__label {
                background-color: var(--color-green);
            }
            .switch-button .switch-button__checkbox:checked + .switch-button__label:before {
                transform: translateX(2rem);
            }
        </style>
      
<?php

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

    if (isset($_REQUEST['wemp_pmla']))$wemp_pmla = $_REQUEST['wemp_pmla'];
    $bdCenest = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenest');
    $bdMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    if(!isset($wemp_pmla)){
        terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
    }
   // Se verifica si el usuario que esta logueado, si tenga permisos para ingresar a la funcionalidad.
    $qryUserExist = "select count(Codigo) from {$bdCenest}_000015 WHERE Codigo = '$wuse' AND Activo = 'on'";
    $resUserExist = mysql_query($qryUserExist,$conex) or die (mysql_errno()." - en el query: ".$qryUserExist." - ".mysql_error());
    $datoUserExist = mysql_fetch_array($resUserExist); $conteoUserExist = $datoUserExist[0];
    if($conteoUserExist == 0){
        ?>
        <div align="center">
            <label>USUARIO NO AUTORIZADO PARA CONSULTAR EN EL SISTEMA DE TRAZABILIDAD CENTRO DE ESTERILIZACION<br/>
            Ingrese de nuevo a Matrix con un usuario válido</label>
        </div>
        <?php
        return;
    }
    // Se inicializan centros de costos que aun no estan en movhos 11 para que se pueda hacer la trazabilidad
    $ccoBraquiterapia = '1202';
    $ccoNomBraquiterapia = 'RT-UNIDAD RADIOTERAPIA-BRAQUITERAPIA';
    $ccoCirugiaCardio = '10162';
    $ccoNomCirugiaCardio = 'CIRUGIA CARDIO';
    $fecha_Actual = date('Y-m-d');  $hora_Actual = date('H:m:s');
    // Se consulta quien tiene prioridad de Coordinador (1) para que pueda acceder a funcionalidades privilegiadas
    $qryCkUser = "SELECT count(Codigo) FROM {$bdCenest}_000015 WHERE Codigo = '$wuse' AND Prioridad = '1' AND Activo = 'on'";
    $comQryCkUser = mysql_query($qryCkUser,$conex) or die (mysql_errno()." - en el query: ".$qryCkUser." - ".mysql_error());
    $datoCkUser = mysql_fetch_array($comQryCkUser); $conteoUser = $datoCkUser[0];
    if($conteoUser > 0){$administrador = '1';}
    // se consulta si tiene prioridad Master (10) para que pueda agregar o quitar usuarios
    $qryCkUserMaster = "SELECT count(Codigo) FROM {$bdCenest}_000015 WHERE Codigo = '$wuse' AND Prioridad = '10' AND Activo = 'on'";
    $comQryCkUserMaster = mysql_query($qryCkUserMaster,$conex) or die (mysql_errno()." - en el query: ".$qryCkUserMaster." - ".mysql_error());
    $datoCkUserMaster = mysql_fetch_array($comQryCkUserMaster); $conteoUserMaster = $datoCkUserMaster[0];
    if($conteoUserMaster > 0){$administradorMaster = '1'; $administrador = '1';}
    ?>
</head>
    <body>
    <div class="container">
        <div class="panel panel-info contenido">
            <?php
                $wactualiz = '2021-04-23';
                encabezado("Trazabilidad de Dispositivos",$wactualiz,"clinica"); 
            ?>
            <div align="center" class="panel panel-info contenido" style="border: none;margin-left: 0px;">
            <!-- Formulario para elegir el centro de costos al cual quiero acceder -->
            <form method="post" action="trazMaster.php?wemp_pmla=<?=$wemp_pmla?>">
                <table class="tblParametros" style="width: 60%" border="0">
                    <tr>
                        <td>
                            <div class="input-group selectPpal">
                                <span class="input-group-addon input-sm"><label for="unidad" style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;UNIDAD:&nbsp;&nbsp;&nbsp;</label></span>
                                <select id="unidad" name="unidad" class="form-control form-sm">
                                    <?php
                                        $braquiterapiaAux = false;
                                        // Se consulta la movhos 11 con el campo ccocen para saber si ese CCo pertenece a la funcionalidad de central de est.
                                        $queryCco = "SELECT Ccocod,Cconom FROM {$bdMovhos}_000011 WHERE Ccocen = 'on' ORDER BY Cconom ASC";
                                        $commitCco = mysql_query($queryCco, $conex) or die (mysql_errno()." - en el query: ".$queryCco." - ".mysql_error());
                                        while($datoempresa = mysql_fetch_assoc($commitCco))
                                        {   
                                            $codigoCco = $datoempresa['Ccocod'];    $nombreCco = $datoempresa['Cconom'];
                                            echo "<option value='".$codigoCco."'>".$codigoCco.' - '.$nombreCco."</option>";
                                            if ($codigoCco == $ccoBraquiterapia )$braquiterapiaAux = true;
                                        }
                                        if (!$braquiterapiaAux)echo "<option value='".$ccoBraquiterapia."'>".$ccoBraquiterapia.' - '.$ccoNomBraquiterapia."</option>";
                                        echo "<option value='".$ccoCirugiaCardio."'>".$ccoCirugiaCardio.' - '.$ccoNomCirugiaCardio."</option>";

                                        if ($administradorMaster == '1')echo "<option value='adminMaster'>MAESTRO ADMINISTRADOR</option>";?>
                                    
                                    <?php
                                        $ccoUnidad = $_POST['unidad'];
                                        if($ccoUnidad != null){
                                            if ($ccoUnidad == 'adminMaster'){
                                                echo '<option selected value="adminMaster">';
                                                echo "MAESTRO ADMINISTRADOR";
                                            }else{
                                                echo '<option selected value="'.$ccoUnidad.'">';
                                                $ccoUnidadMostrar = $ccoUnidad.' - ' . datosUnidadxCco($ccoUnidad,$conex);
                                                echo $ccoUnidadMostrar;
                                            }
                                        }else{
                                            echo '<option selected disabled>';
                                            echo "Seleccione...";
                                        }
                                        
                                    ?>
                                    </option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="input-group" style="margin-top: 20px; margin-left: 10px">
                                <input type="submit" class="btn btn-info btn-sm" value="Consultar" title="Proceder" style="width: 120px">
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
            </div>
        
        
        <section style="text-align: center">
            <?php
            // Titulo una vez seleccionado
            if($ccoUnidad != null)
            {
                if ($ccoUnidad == 'adminMaster'){
                ?>
                <h3><?php echo "MAESTRO ADMINISTRADOR"; ?></h3>
                <?php 
                } else {
                    ?>
                         <h3><?php //echo $ccoUnidad.' - '; datosUnidadxCco($ccoUnidad,$conex,1);
                          echo $ccoUnidadMostrar; ?></h3>
                    <?php 
                }

            }
            else
            {
                ?>
                <h3>Debe seleccionar Unidad...</h3>
                <?php
            }
            ?>
        </section>

        <div align="center" class="panel panel-info contenidoTab">
        <?php
        // Se asignan las variables request segun las opciones escogidas.
            $dispoSelected = $_POST['dispositivo']; $pestana = $_POST['pestana'];           $dispoGest = $_POST['dispoGest'];   $numCcUsuario = $_POST['numCcUsuario'];
            $reusoSel = $_POST['reusoSel'];         $accion = $_POST['accion'];             $nomUsuario = $_POST['nomUsuario']; $fecUtiliza = $_POST['fecUtiliza'];
            $coReSel = explode('_',$reusoSel);      $obserReuso = $_POST['obserReuso'];     $funEntrega = $_POST['funEntrega']; $funRecibe = $_POST['funRecibe'];
            $numQuirofano = $_POST['numQuirofano']; $fecEsteril = $_POST['fecEsteril'];     $eqEsteril = $_POST['eqEsteril'];   $metEsteril = $_POST['metEsteril'];
            $cicEsteril = $_POST['cicEsteril'];     $respEsteril = $_POST['respEsteril'];   $novDispo = $_POST['novDispo'];
            $codReusoSel = $coReSel[0];             $idReusoSel = $coReSel[1];              if($idReusoSel == null){$idReusoSel = $_POST['idReuso'];}

            if($ccoUnidad != null)
            {
                ?>
               
                <!-- Tab links para acceder a los menus -->
                <div class="tab" style="background-color: #EEEEEE;">
                    <?php if ($ccoUnidad != 'adminMaster'){
                        if($administrador == '1'){?>    <button class="tablinks" onclick="openCity(event, 'London')" id="tab1">MAESTROS</button> <?php }?>
                                                        <button class="tablinks" onclick="openCity(event, 'Paris')">TRAZABILIDAD</button>
                        <?php if($administrador == '1'){?> <button class="tablinks" onclick="openCity(event, 'Tokyo')">REPORTES</button> <?php }
                          }else{
                                if($administradorMaster == '1'){?> 
                                <button class="tablinks" onclick="openCity(event, 'Madrid')">MAESTRO DE USUARIOS</button> 
                                <button class="tablinks" onclick="openCity(event, 'Bogota')">MAESTRO CENTROS DE COSTOS</button> 
                                <?php }
                          }?>
                </div>

               <!-- Tab5 content: ADMIN CENTRO DE COSTOS -->
               <?php
                if($pestana == 5){ ?><div id="Bogota" class="tabcontent" style="display: block;width:100%;margin-left: 10px;"><?php }
                else{ ?><div id="Bogota" class="tabcontent" style="display: none; width:100%;margin-left: 10px;"><?php }
                ?>
                     <div class="input-group" style="margin-top: 20px; margin-left: 10px; margin-bottom: 20px; ">
                        <input type="hidden" id="'accionaddCco'" name="accionaddCco" value="agregarCco">
                        <input type="button" class="btn btn-primary" value="Agregar Centro de Costos" title="Agregar Centro de Costos" 
                        onclick="showModal('modalAddCco')">
                    </div>
                    <div class="modal fade bs-example-modal-lg" id="modalAddCco" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="removerClase('divAddCcoApp')"><span aria-hidden="true">&times;</span></button>
                                        <?php construirModalAddCco(); ?>
                                </div>
                                <div class="modal-body" id="modalBodyAddCco">
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class='table table-bordered'>
                        <thead class='encabezadoTabla'>
                            <th class='text-center'>Centro de Costos</th>
                            <th class='text-center'>Nombre Centro de Costos</th>
                            <th class='text-center'>Estado</th>
                            <th class='text-center'>Acciones</th>
                        </thead>
                        <?php
                        // Construye la tabla con todos los Centros de costos y a su vez los botones y sus modales.
                        getCcoCenest();
                        ?>

                    </table>
                </div>
               
                  <!-- Tab4 content: ADMINISTRACION DE USUARIOS -->
                  <?php
                if($pestana == 4){ ?><div id="Madrid" class="tabcontent" style="display: block;width:100%;margin-left: 10px;"><?php }
                else{ ?><div id="Madrid" class="tabcontent" style="display: none; width:100%;margin-left: 10px;"><?php }
                ?>
                        <div class="input-group" style="margin-top: 20px; margin-left: 10px; margin-bottom: 20px; ">
                            <input type="hidden" id="'accionadd'" name="accionadd" value="agregarUsuario">
                            <input type="button" class="btn btn-primary" value="Agregar Usuario" title="Agregar Usuario" 
                            onclick="showModal('modalAdd')">
                        </div>
                        <table class='table table-bordered'>
                            <thead class='encabezadoTabla'>
                                <th class='text-center'>C&oacute;digo</th>
                                <th class='text-center'>Nombre de Usuario</th>
                                <th class='text-center'>Prioridad</th>
                                <th class='text-center'>Activo</th>
                                <th class='text-center'>Acciones</th-->
                            </thead>
                            <?php
                            // Construye la tabla con todos los usuarios y a su vez los botones y sus modales.
                                getUsuariosPrioridad();
                            ?>

                         </table>
                        <input type="hidden" name="pestana" value="4">
                    </div>
                </div>
        </div>
        <!-- Modal para adicionar un usuario nuevo -->
        <div class="modal fade bs-example-modal-lg" id="modalAdd" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="removerClase('divAddUserApp')"><span aria-hidden="true">&times;</span></button>
                        <!-- Construye el modal para adicionar un usuario -->
                            <?php construirModalAddUser(); ?>
                    </div>
                    <div class="modal-body" id="modalBodyAdd">
                    </div>
                </div>
            </div>
        </div>


    <!-- Tab1 content: MAESTROS DE DISPOSITIVOS -->
        <?php  if($pestana == 1){ ?><div id="London" class="tabcontent" style="display: block; width:100%;margin-left: 10px;"><?php }
                else{ ?><div id="London" class="tabcontent" style="display: none; width:100%;margin-left: 10px;"><?php }
                ?>
                    <div class="titulo" style="margin-top: -6px">
                        <label>MAESTRO DE DISPOSITIVOS</label>
                    </div>
                    <form name="frmDispositivo" method="post">
                        <div class="input-group selectDispo">
                            <span class="input-group-addon input-sm"><label for="dispositivo">&nbsp;&nbsp;&nbsp;&nbsp;DISPOSITIVO:&nbsp;&nbsp;&nbsp;</label></span>
                            <select id="dispositivo" name="dispositivo" class="form-control form-sm" onchange="this.form.submit()">
                                <?php
                                // Se realiza una consulta para averiguar los dispositivos asociados al cco seleccionado
                                $query2 = "SELECT * FROM {$bdCenest}_000011 WHERE Codcco = '$ccoUnidad' AND Estado = 'on'";
                                $commitQuery2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
                                while($datoDispo = mysql_fetch_array($commitQuery2))
                                {
                                    $codDispo = $datoDispo['Codigo'];   $descDispo = $datoDispo['Descripcion'];
                                    echo "<option value='".$codDispo.' - '.$descDispo."'>".$codDispo.' - '.$descDispo."</option>";
                                }
                                ?>
                                <option selected disabled>Seleccione...</option>
                            </select>
                        </div>
                        <input type="hidden" name="unidad" value="<?php echo $ccoUnidad ?>">
                        <input type="hidden" name="pestana" value="1">
                        <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="<?=$wemp_pmla?>">
                    </form>

                    <h3 style="text-align: center; margin-top: 5px"><?php echo $dispoSelected ?></h3>
                    <?php
                    $codI = explode('-',$dispoSelected);    $codIt = $codI[0];
                    // si existen dispositivos asociados al cco 
                    // Se llenan los array para ser enviados al constructor de modales
                    unset($datosArray);
                    $ReusoButton = '';
                    $MaestroButton = '';
                    $editButton = 'display: block;';
                    $datosArray = array();
                    $datosArrayReuso = array();
                    $datosModificarDispo = array();
                    $datosModificarDispo['datos']['idModal'] = 'modificarDispoMaestro';
                    $datosModificarDispo['datos']['idCco'] = $ccoUnidad;
                    $datosModificarDispo['datos']['codIt'] = $codIt;
                    $datosArray['datos']['idModal'] = 'maestroDispo';
                    $datosArray['datos']['idCco'] = $ccoUnidad;
                    $datosArrayReuso['datos']['idModal'] = 'insertarReusoModal';
                    $datosArrayReuso['datos']['idCco'] = $ccoUnidad;
                    $datosArrayReuso['datos']['codIt'] = $codIt;
                    $datosArrayReuso['datos']['id'] = 'undefined';
                    if ($dispoSelected != '')$MaestroButton = 'disabled';
                    if (!$codIt){
                        $ReusoButton = 'disabled';
                        $editButton = 'display: none;';
                    }
                    ?>
                    <div id="divButtons">
                        <div class="input-group" style="margin-bottom: 5px; margin-top: 10px; text-align: center; border: none; display: block;">
                            <input type="hidden" id="accion3" name="accion3" value="insertarDispo">
                            <input type="submit" class="btn btn-info btn-sm" value="Maestro de Dispositivos" title="Maestro de Dispositivos" style="width: 195px"
                                onclick='buildModalHeader(<?=json_encode($datosArray,JSON_UNESCAPED_SLASHES)?>,"insertarDispo")' <?=$MaestroButton?>>
                            <input type="hidden" id="accion2" name="accion2" value="insertarReuso">
                            <input type="submit" class="btn btn-success btn-sm" value="Apertura de Codigos" title="Apertura de Codigos" style="width: 195px"
                                onclick='buildModalHeader(<?=json_encode($datosArrayReuso,JSON_UNESCAPED_SLASHES)?>,"insertarReuso")' <?=$ReusoButton?>>
                        </div>
                    </div>
                    <div class="modal fade bs-example-modal-lg" id="maestroDispo" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                    </div>
                    <div class="modal fade bs-example-modal-lg" id="insertarReusoModal" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                    </div>
                   <!-- si es administrador que le muestre el boton para modificar los dispositivos -->
                    <?php if($administradorMaster == '1'){?> 
                    <div style="<?=$editButton?> margin-top: 20px; margin-bottom: 10px; text-align:center" class="input-group">
                        <input type="submit" class="btn btn-danger btn-sm" value="Modificar Dispositivo" title="Modificar Dispositivo" style="width: 195px;"
                                onclick='buildModalHeader(<?=json_encode($datosModificarDispo,JSON_UNESCAPED_SLASHES)?>,"modificarDispoMaestro")'>
                    </div>
                    <div class="modal fade bs-example-modal-lg" id="modificarDispoMaestro" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                    </div>
                    <?php 
                    }


                    // si selecciono algun dispositivo
                    if($codIt != null)
                    {
                        $qryExisteItem = "SELECT count(id) FROM {$bdCenest}_000012 WHERE Coddispo = '$codIt' AND Codcco = '$ccoUnidad'";
                        $commitExisteItem = mysql_query($qryExisteItem,$conex) or die (mysql_errno()." - en el query: ".$qryExisteItem." - ".mysql_error());
                        $datoExisteItem = mysql_fetch_array($commitExisteItem);
                        $conteoItem = $datoExisteItem[0];
                        // se consulta cuantos dispositivos de reuso hay asociados
                        if($conteoItem > 0)
                        {   
                            // Se consultan los dispositivos que esten en estado 'on' y 'st' y se despliegan
                            $query3 = "SELECT * FROM {$bdCenest}_000012 WHERE Coddispo = '$codIt' AND Estado != 'off' AND Codcco = '$ccoUnidad'";
                            $commitQuery3 = mysql_query($query3, $conex) or die (mysql_errno()." - en el query: ".$query3." - ".mysql_error());
                            ?>
                            <table id="tableMaestros" align="center" style="width: 100%;" border="1">
                                <thead style="background-color: #C3D9FF">
                                <tr style="font-weight: bold;" class="encabezadoTabla">
                                    <td align="center" style="min-width: 100px;">CODIGO REUSO</td>
                                    <td align="center" style="min-width: 120px;">Nro CALIBRE</td>
                                    <td align="center" style="min-width: 110px;">REFERENCIA</td>
                                    <td align="center" style="min-width: 110px;">INVIMA</td>
                                    <td align="center" style="min-width: 240px;">USUARIO QUE MODIFICA</td>
                                    <td align="center" style="min-width: 50px;"># USOS</td>
                                    <td align="center" style="min-width: 50px;">LIMITE</td>
                                    <td align="center" style="min-width: 110px;">MOD.</td>
                                    <td align="center" style="min-width: 50px;">EST</td>
                                </tr>
                                </thead>
                                        <?php
                                        $cntAccion = 0;
                                        $fila = '';
                                        $matrixClass = "";
                                        unset($datosArray);
                                        while($datoReuso = mysql_fetch_assoc($commitQuery3))
                                        {
                                            $datoReuso = validateHtmlEntities($datoReuso); // valida cada campo del array con entidades html
                                            $codReuso = $datoReuso['Codreuso']; $numCalibre = $datoReuso['Ncalibre'];       $codItem = $datoReuso['Coditem'];
                                            $invima = $datoReuso['Invima'];     $observacion = $datoReuso['Seguridad'];   $numUso = $datoReuso['Numuso'];
                                            $idCenest12 = $datoReuso['id'];     $codDispo = $datoReuso['Coddispo'];         $limite = $datoReuso['limite'];
                                            $observacion = datosUsuarioXseg($observacion);
                                            // Se inicializan las variables para construir los modales y para los id de cada uno
                                                $disable = '';
                                                $vartr = "idtrmaestro";
                                                $varswitch = "switchL";
                                                $varAccion = "accionx";
                                                $varAccion2 = "accion2x";
                                                $idModalModificarReuso = 'modalModificarReuso';
                                                $varAccion2 .=  $cntAccion;
                                                $varAccion .=  $cntAccion;
                                                $varswitch .=  $cntAccion;
                                                $vartr .=  $cntAccion;
                                                $idModalModificarReuso .=  $cntAccion;
                                                $cntAccion++;
                                                $datosArray = array();
                                                // se obtienen los datos necesarios para enviarlos para construir cada modal
                                                $datosArray['datos'] = $datoReuso;
                                                $datosArray['datos']['idDatos'] = 'modificarReuso';
                                                $datosArray['datos']['idModal'] = $idModalModificarReuso;
                                                $estadoPaciente = esDispoMaloOfinfec($idCenest12,$ccoUnidad); // 1 si es malo, 2 si esta infectado
                                                // se realizan validaciones para saber que boton mostrar, deshabilitar o habilitar segun el estado del dispositivo
                                                if($numUso >= $limite)
                                                {$disable = 'disabled';?><tr id="<?=$vartr?>" style="border-bottom: groove; border-bottom-color: #FFABA8; background-color: #FFABA8"><?php }
                                                else if ($estadoPaciente == 1)
                                                {$disable = 'disabled';?><tr id="<?=$vartr?>" style="border-bottom: groove; border-bottom-color: #b2bec3; background-color: #b2bec3">
                                                <?php 
                                                }
                                                else if ($estadoPaciente == 2)
                                                {$disable = 'disabled';?><tr id="<?=$vartr?>" style="border-bottom: groove; border-bottom-color: #f6b93b; background-color: #f6b93b">
                                                <?php 
                                                }
                                                else if ($datoReuso['Estado'] == 'on')
                                                {?><tr id="<?=$vartr?>" style="border-bottom: groove; border-bottom-color: #ffeaa7; background-color: #ffeaa7">
                                                <?php 
                                                }else {?><tr id="<?=$vartr?>" class="<?=$matrixClass?>" style="border-bottom: groove; border-bottom-color: #EEEEEE">
                                                    <?php 
                                                }
                                            ?>
                                                <td align="center" style="min-width: 100px;"><?=$codReuso?></td>
                                                <td align="center" style="min-width: 120px;"><?=$numCalibre?></td>
                                                <td align="center" style="min-width: 110px;"><?=$codItem?></td>
                                                <td align="center" style="min-width: 110px;"><?=$invima?></td>
                                                <td align="center" style="min-width: 240px;"><?=$observacion?></td>
                                                <td align="center" style="min-width: 50px;"><?=$numUso?></td>
                                                <td align="center" style="min-width: 50px;"><?=$limite?></td>
                                                <td align="center" style="min-width: 110px;">
                                                    <!-- boton que abre modal para modificar reuso -->
                                                    <input type="hidden" id="<?php echo $varAccion?>" name="<?php echo $varAccion?>" value="modificar">
                                                    <input type="button" class="btn btn-info btn-sm" value="M" title="Modificar" style="width: -1px" 
                                                    onclick='buildModalHeader(<?=json_encode($datosArray,JSON_UNESCAPED_SLASHES)?>,"Modificar")' <?=$disable?>>
                                                <!-- div para los modales de cada reuso -->
                                                <div class="modal fade bs-example-modal-lg" id="<?= $idModalModificarReuso?>" class="text-center" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false" style="width:100%">
                                                </div>
                                                    <?php
                                                    // si cumplio el numero de usos o el paciente estaba infectado o el dispo se dano, habilitar boton para actualizar reuso
                                                    if($numUso >= $limite || $estadoPaciente == 1 || $estadoPaciente == 2)
                                                    { 
                                                        ?>
                                                        <input type="hidden" id="<?=$varAccion2?>" name="<?=$varAccion2?>" value="insertarReuso">
                                                        <input type="button" class="btn btn-danger btn-sm" value="A" title="Adicionar" style="width: -1px"
                                                            onclick='buildModalHeader(<?=json_encode($datosArray,JSON_UNESCAPED_SLASHES)?>,"insertarReuso")'>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                                    $estado = '';
                                                    $check = '';
                                                    switch ($datoReuso['Estado']) {
                                                        case 'on':
                                                            $estado = "A";
                                                            $title = 'Activo';
                                                            $check = 'checked';
                                                            break;
                                                        case 'off':
                                                            $estado = "I";
                                                            $title = 'Inactivo';
                                                            break;
                                                        case 'st':
                                                            $estado = "P";
                                                            $title = 'Pausado';
                                                        break;
                                                    }
                                                            ?>
                                                <td align="center" style="min-width: 50px;" title="<?=$title?>">
                                                <!-- <?=$estado?> -->
                                                <div class="switch-button">
                                                    <input type="checkbox" name="switch-button" id="<?=$varswitch?>" class="switch-button__checkbox" <?=$check?>
                                                    onclick="changeCheckBox('<?=$varswitch?>','<?=$idCenest12?>','<?=$vartr?>')" <?=$disable?>>
                                                    <label for="<?=$varswitch?>" class="switch-button__label"></label>
                                                </div>
                                                </td>
                                            </tr>

                                            <?php
                                        }
                                        ?>
                                    </table>
                            <?php
                        }
                        else
                        {
                            ?>
                            <h3>No existen registros para este item en el Maestro de Dispositivos</h3>
                            <?php
                        }
                    }
                   ?>
                </div>
                
            <!-- Tab2 content: TRAZABILIDAD -->
                <?php
            if($pestana == 2){ ?><div id="Paris" class="tabcontent" style="display: block; width:100%;margin-left: 10px;"><?php }
            else{ ?><div id="Paris" class="tabcontent" style="display: none; width:100%;margin-left: 10px;"><?php }
                ?>
                <div class="encabezadoTabla" style="margin-top: 0; text-align:center">
                    <label>FORMATO DE TRAZABILIDAD PARA DISPOSITIVOS MEDICOS DE USO REPETIDO AUTORIZADOS</label>
                </div>

                <!-- SELECCIONAR DISPOSITIVO: -->
                <form method="post">
                    <div class="input-group selectDispo">
                        <span class="input-group-addon input-sm"><label for="dispoGest">&nbsp;&nbsp;&nbsp;&nbsp;DISPOSITIVO:&nbsp;&nbsp;&nbsp;</label></span>
                        <select id="dispoGest" name="dispoGest" class="form-control form-sm" onchange="this.form.submit()">
                            <?php
                            // se llena el select de dispositivos
                            $query2 = "SELECT * FROM {$bdCenest}_000011 WHERE Codcco = '$ccoUnidad' AND Estado = 'on'";
                            $commitQuery2 = mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());
                            while($datoDispo = mysql_fetch_array($commitQuery2))
                            {
                                $codDispo = $datoDispo['Codigo'];   $descDispo = $datoDispo['Descripcion'];
                                echo "<option value='".$codDispo.' - '.$descDispo."'>".$codDispo.' - '.$descDispo."</option>";
                            }
                            ?>
                            <option selected disabled>Seleccione...</option>
                        </select>
                    </div>
                    <input type="hidden" name="unidad" value="<?php echo $ccoUnidad ?>">
                    <input type="hidden" name="pestana" value="2">
                    <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="<?=$wemp_pmla?>">
                </form>

                <h3 style="text-align: center; margin-top: 5px"><?php echo $dispoGest ?></h3>
                <?php
                $codI = explode('-',$dispoGest);    $codIt = $codI[0];
                // si es seleccionado uno en especifico
                if($codIt != null)
                {
                    $qryExisteItem = "SELECT count(id) FROM {$bdCenest}_000012 WHERE Coddispo = '$codIt' AND Codcco = '$ccoUnidad'";
                    $commitExisteItem = mysql_query($qryExisteItem,$conex) or die (mysql_errno()." - en el query: ".$qryExisteItem." - ".mysql_error());
                    $datoExisteItem = mysql_fetch_array($commitExisteItem);
                    $conteoItem = $datoExisteItem[0];
                    $arrCodigosMostrar = [];
                    // conteo para saber si hay dispositivos con codigo de reuso creado
                    if($conteoItem > 0)
                    {
                        ?>
                        <!-- SELECCIONAR REUSO: solo muestra los que estan activos -->
                        <form method="post">
                            <div class="input-group selectDispo">
                                <span class="input-group-addon input-sm"><label for="reusoSel">CODIGO DE DISPOSITIVO MEDICO</label></span>
                                <select id="reusoSel" name="reusoSel" class="form-control form-sm" onchange="this.form.submit()">
                                    <?php
                                    $arrCodigosMostrar = codigosMostrarTraz($codIt,$ccoUnidad);
                                    foreach($arrCodigosMostrar as $idReuso => $codReuso){
                                        echo "<option value='".$codReuso.'_'.$idReuso."'>".$codReuso."</option>";
                                    }
                                    ?>
                                    <option selected disabled>Seleccione...</option>
                                </select>
                                <span class="input-group-addon input-sm"><label for="filtroReuTraz">BUSCAR:</label></span>
                                <input type='text' id='filtroReuTraz' class="form-control form-sm" style="min-width:150px" onblur='llenarSelectReu(<?=json_encode($arrCodigosMostrar,JSON_UNESCAPED_SLASHES)?>,"<?=$codIt?>","<?=$ccoUnidad?>")'>

                            </div>
                            <input type="hidden" name="unidad" value="<?php echo $ccoUnidad ?>">
                            <input type="hidden" name="dispoGest" value="<?php echo $dispoGest ?>">
                            <input type="hidden" name="pestana" value="2">
                            <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="<?=$wemp_pmla?>">
                        </form>

                        <?php
                        // Query para la info general del dispositivo
                        $query3 = "SELECT * FROM {$bdCenest}_000012 WHERE id = '$idReusoSel' AND Estado = 'on'";
                        $commitQuery3 = mysql_query($query3, $conex) or die (mysql_errno()." - en el query: ".$query3." - ".mysql_error());
                        // si ya selecciono el dispositivo con codigo de reuso especifico
                        if($idReusoSel != null)
                        {
                            ?><h3 style="text-align: center; margin-top: 5px"><?php echo $codReusoSel ?></h3><?php

                            //GRABAR MOVIMIENTO REUSO DESDE UNIDADES:
                            if($accion == 'grabarReuso')
                            {
                                $seguridad = 'C-'.wUser();
                                $queryInsRe = "INSERT INTO {$bdCenest}_000014
                                                        VALUES('cenest','$fecha_Actual','$hora_Actual','$idReusoSel','$nomUsuario','$numCcUsuario',
                                                        '$fecUtiliza','$numQuirofano','$obserReuso','$funEntrega','$ccoUnidad','$fecEsteril','$eqEsteril',
                                                        '$metEsteril','$cicEsteril','$funRecibe','$respEsteril','$wuse','$novDispo','$seguridad','')";
                                $commInsRe = mysql_query($queryInsRe, $conex) or die (mysql_errno()." - en el query: ".$queryInsRe." - ".mysql_error());

                                if($commInsRe == true)
                                {
                                    // se actualiza el numero de usos luego de grabacion de reusos
                                    $querySel12 = "SELECT Numuso, limite, Codreuso FROM {$bdCenest}_000012 WHERE id = '$idReusoSel'";
                                    $commSel12 = mysql_query($querySel12, $conex) or die (mysql_errno()." - en el query: ".$querySel12." - ".mysql_error());
                                    $datoSel12 = mysql_fetch_array($commSel12);
                                    $numeroUsos = $datoSel12[0];    $numeroUsos = $numeroUsos + 1;  $limUsos = $datoSel12[1];   $codR = $datoSel12['2'];

                                    $queryUp12 = "UPDATE {$bdCenest}_000012 SET Numuso = '$numeroUsos' WHERE id = '$idReusoSel'";
                                    mysql_query($queryUp12, $conex) or die (mysql_errno()." - en el query: ".$queryUp12." - ".mysql_error());

                                    if($numeroUsos >= $limUsos)
                                    {
                                        ?>
                                        <div id="divAlerta" align="center" style="border: groove; background-color: #FC4136; border-color: #FC4136; border-radius: 10px">
                                            <h3 style="color: #1A3659">ESTE DISPOSITIVO HA ALCANZADO SU LIMITE MAXIMO DE USOS</h3>
                                            <h4 style="color: #1A3659">POR LA TANTO DEBE SER DESCARTADO</h4>
                                            <h4 style="color: #1A3659">CODIGO: <?php echo $codR ?></h4>
                                        </div>
                                        <?php
                                    }
                                    if($novDispo == 'MALO')
                                    {
                                        ?>
                                        <div id="divAlerta" align="center" style="border: groove; background-color: #BFBFBF; border-color: #BFBFBF; border-radius: 10px">
                                            <h3 style="color: #1A3659">LA NOVEDAD PARA ESTE DISPOSITIVO ES: MALO</h3>
                                            <h4 style="color: #1A3659">POR LA TANTO DEBE SER DESCARTADO</h4>
                                            <h4 style="color: #1A3659">CODIGO: <?php echo $codR ?></h4>
                                        </div>
                                        <?php
                                    }
                                    if($novDispo == 'PACIENTE INFECTADO')
                                    {
                                        ?>
                                        <div id="divAlerta" align="center" style="border: groove; background-color: #FFC000; border-color: #FFC000; border-radius: 10px">
                                            <h3 style="color: #1A3659">LA NOVEDAD PARA ESTE DISPOSITIVO ES: PACIENTE INFECTADO</h3>
                                            <h4 style="color: #1A3659">POR LA TANTO DEBE SER DESCARTADO</h4>
                                            <h4 style="color: #1A3659">CODIGO: <?php echo $codR ?></h4>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                    <h4 style="text-align: center">Se ha guardado correctamente</h4>
                                    <?php
                                }
                            }
                            //FORMATO DILIGENCIAR REUSO:
                            else
                            {
                                ?>
                                <div style="max-height: 250px; overflow-y: visible; border: none">
                                    <table align="center" style="width: 100%;" border="dotted" class="encabezadoTabla">
                                        <thead style="background-color: #C3D9FF;color: black;" border="dotted">
                                        <tr style="font-weight: bold">
                                            <td align="center" style="width: 80px">NUMERO CALIBRE</td>
                                            <td align="center" style="width: 140px">REFERENCIA &ensp;</td>
                                            <td align="center" style="width: 170px">INVIMA &ensp;</td>
                                            <td align="center" style="width: 380px">OBSERVACION</td>
                                            <td align="center" style="width: 80px">NUMERO DE USOS</td>
                                        </tr>
                                        </thead>
                                            <?php
                                            while($datoReuso = mysql_fetch_array($commitQuery3))
                                            {
                                                $codReuso = $datoReuso['Codreuso']; $numCalibre = $datoReuso['Ncalibre'];       $codItem = $datoReuso['Coditem'];
                                                $invima = $datoReuso['Invima'];     $observacion = $datoReuso['Observacion'];   $numUso = $datoReuso['Numuso'];
                                                $idCenest12 = $datoReuso['id'];     $codDispo = $datoReuso['Coddispo'];         $limite = $datoReuso['limite'];
                                                if($numUso >= $limite)
                                                {?><tr style="border-bottom: groove; border-bottom-color: #EEEEEE; background-color: #CCFFCC"><?php }
                                                else
                                                {?><tr class="alternar" style="border-bottom: groove; border-bottom-color: #EEEEEE"><?php }
                                                ?>
                                                <td align="center" style="width: 80px"><?php echo $numCalibre ?>&ensp;</td>
                                                <td align="center" style="width: 140px"><?php echo $codItem ?>&ensp;</td>
                                                <td align="center" style="width: 170px"><?php echo $invima ?>&ensp;</td>
                                                <td align="center" style="width: 380px"><?php echo $observacion ?>&ensp;</td>
                                                <td align="center" style="width: 80px">&ensp;&ensp;&ensp;&ensp;<?php echo $numUso ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                    </table>
                                </div>
                                <!-- Formulario de diligenciamiento de trazabilidad -->
                                <form name="frmTrazabilidad" method="post" action="trazMaster.php?wemp_pmla=<?=$wemp_pmla?>">
                                    <div class="divDatosGestion input-group">

                                        <!--PARTE 1:-->
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 20px">
                                            <span class="input-group-addon input-sm"><label for="nomUsuario">&ensp;NOMBRE DE USUARIO:&ensp;</label></span>
                                            <input type="text" id="nomUsuario" name="nomUsuario" class="form-control form-sm" style="width: 350px" required>

                                            <span class="input-group-addon input-sm"><label for="numCcUsuario">&ensp;NUMERO DE IDENTIFICACION:</label></span>
                                            <input type="text" id="numCcUsuario" name="numCcUsuario" class="form-control form-sm" style="width: 200px" onblur="traerUsuario()" required>
                                        </div>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px" align="left">
                                            <span class="input-group-addon input-sm"><label for="datepicker1">FECHA DE UTILIZACION:</label></span>
                                            <input type="date" id="datepicker1" name="fecUtiliza" class="form-control form-sm" style="width: 230px" value="<?=date('Y-m-d')?>">

                                            <span class="input-group-addon input-sm"><label for="numQuirofano">&nbsp;&nbsp;&nbsp;&nbsp;NUMERO DE QUIROFANO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label></span>
                                            <input type="text" id="numQuirofano" name="numQuirofano" class="form-control form-sm" style="width: 100px" required>

                                            <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 58px"></span>
                                        </div>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                            <span class="input-group-addon input-sm"><label for="obserReuso">&nbsp;OBSERVACION:&nbsp;</label></span>
                                            <input type="text" id="obserReuso" name="obserReuso" class="form-control form-sm" style="width: 920px" required>
                                        </div>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                            <span class="input-group-addon input-sm"><label for="funEntrega">FUNCIONARIO ENTREGA A ESTERILIZACION:</label></span>
                                            <input type="text" id="funEntrega" name="funEntrega" class="form-control form-sm" style="width: 732px" required>
                                        </div>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                            <span class="input-group-addon input-sm"><label for="funRecibe">FUNCIONARIO RECIBE EN ESTERILIZACION:</label></span>
                                            <select id="funRecibe" name="funRecibe" class="form-control form-sm" style="width: 800px" required>
                                                <?php
                                                    $selectUsers = createSelectUsers();
                                                    echo $selectUsers
                                                ?>
                                                <!-- <option selected disabled>Seleccione...</option> -->
                                                <option value="CASA COMERCIAL">CASA COMERCIAL</option>;
                                                <option value="NO APLICA">NO APLICA</option>;
                                            </select>
                                        </div>

                                        <!--PARTE 2:-->
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                            <span class="input-group-addon input-sm"><label for="fecEsteril">&ensp;FECHA DE ESTERILIZACION:&ensp;&ensp;&ensp;&ensp;</label></span>
                                            <input type="date" id="fecEsteril" name="fecEsteril" class="form-control form-sm" style="width: 230px" value="<?=date('Y-m-d')?>">

                                            <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 20px"></span>

                                            <span class="input-group-addon input-sm"><label for="eqEsteril">&ensp;EQUIPO  ESTERILIZADOR:&ensp;&ensp;&ensp;</label></span>
                                            <input type="number" min="0" id="eqEsteril" name="eqEsteril" class="form-control form-sm" style="width: 100px" required>

                                            <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 82px"></span>
                                        </div>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px" align="left">
                                            <span class="input-group-addon input-sm"><label for="datepicker2">&ensp;METODO DE ESTERILIZACION:&ensp;&ensp;</label></span>
                                            <select id="datepicker2" name="metEsteril" class="form-control form-sm" style="width: 230px" required>
                                                <option value="">Seleccione...</option>
                                                <option>FORMOL</option>
                                                <option>OXIDO ETILENO</option>
                                                <option>PEROXIDO</option>
                                                <option>VAPOR</option>
                                                <option>DESINFECCION</option>
                                                <option>CASA COMERCIAL</option>
                                                <!-- <option selected disabled>Seleccione...</option> -->
                                            </select>

                                            <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 20px"></span>

                                            <span class="input-group-addon input-sm"><label for="cicEsteril">&ensp;CICLO DE ESTERILIZACION:&ensp;</label></span>
                                            <input type="number" min="0" id="cicEsteril" name="cicEsteril" class="form-control form-sm" style="width: 100px" required>

                                            <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 82px"></span>
                                        </div>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                            <span class="input-group-addon input-sm"><label for="respEsteril">RESPONSABLE ESTERILIZACION:</label></span>
                                            <select id="respEsteril" name="respEsteril" class="form-control form-sm" style="width: 800px" required>
                                                <?=$selectUsers?>
                                                <option value="CASA COMERCIAL">CASA COMERCIAL</option>;
                                                <!-- <option selected disabled>Seleccione...</option> -->
                                            </select>
                                        </div>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                            <span class="input-group-addon input-sm">
                                                <label for="novDispo" style="color: #ac2925">NOVEDADES DISPOSITIVO: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&ensp;</label>
                                            </span>
                                            <select id="novDispo" name="novDispo" class="form-control form-sm" style="width: 800px" required>
                                                <option value="">Seleccione...</option>
                                                <option>NINGUNA</option>
                                                <option>NUEVO</option>
                                                <option style="background-color: #BFBFBF;font-weight: bold;">MALO</option>
                                                <option style="background-color: #fdcb6e;font-weight: bold;">PACIENTE INFECTADO</option>
                                                <?php 
                                                $newUso = $numUso + 1;
                                                if($newUso == $limite){
                                                    echo '<option style="background-color: #fab1a0;font-weight: bold;" selected>USOS CUMPLIDOS</option>';
                                                }else{
                                                    echo '<option style="background-color: #fab1a0;font-weight: bold;">USOS CUMPLIDOS</option>';
                                                    
                                                } 
                                                ?>
                                            </select>
                                        </div>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 5px">
                                            <span class="input-group-addon input-sm"><label for="funDiligencia">FUNCIONARIO QUE DILIGENCIA:</label></span>
                                            <input type="text" id="funDiligencia" name="funDiligencia" class="form-control form-sm" style="width: 732px"
                                                value="<?php userLogin();?>" readonly>
                                        </div>

                                        <!--BOTON GUARDAR:-->
                                        <div class="input-group" style="margin-top: 20px; text-align: center; width: 100%">
                                            <input type="submit" class="btn btn-info btn-sm" value="Guardar" title="Guardar" style="width: 120px">
                                        </div>
                                    </div>
                                    <input type="hidden" name="accion" value="grabarReuso">
                                    <input type="hidden" name="unidad" value="<?php echo $ccoUnidad ?>">
                                    <input type="hidden" name="dispoGest" value="<?php echo $dispoGest ?>">
                                    <input type="hidden" name="idReuso" value="<?php echo $idReusoSel ?>">
                                    <input type="hidden" name="pestana" value="2">
                                </form>
                                <?php
                            }
                        }
                    }
                    else
                    {
                        ?>
                        <h3>No existen codigos de reuso para este item en el Maestro de Dispositivos</h3>
                        <?php
                    }
                }
                ?>
                </div>
                
                 <!-- Tab3 content: REPORTES CENTRAL DE ESTERILIZACION -->
                 <?php
                $fecIniReporte = $_POST['fecIniReporte'];   $fecFinReporte = $_POST['fecFinReporte'];
                $filtroCco = $_POST['filtroCco'];   $filtroDispo = $_POST['filtroDispo'];
                $filtroCodReu = $_POST['filtroCodReu'];


                if($pestana == 3){ ?><div id="Tokyo" class="tabcontent" style="display: block; width:100%;margin-left: 10px;"><?php }
                else{ ?><div id="Tokyo" class="tabcontent" style="display: none; width:100%;margin-left: 10px;"><?php }
                    ?>
                    <div class="titulo" style="margin-top: 0">
                        <label>REPORTES DE REUSO DE DISPOSITIVOS MEDICOS</label>
                    </div>

                    <!-- SELECCION DE PARAMETROS PARA LA GENERACION DE REPORTES -->
                    <div class="input-group" style="display: block">
                        <form method="post" action="trazMaster.php?wemp_pmla=<?=$wemp_pmla?>">
                            <table>
                                <tr>
                                    <td>
                                        <div class="input-group selectDispo" style="margin: 10px 0 10px 0px">
                                            <span class="input-group-addon input-sm"><label for="fecIniReporte">FECHA INICIAL:</label></span>
                                            <?php
                                            if($fecIniReporte != null){?><input type="date" id="fecIniReporte" name="fecIniReporte" class="form-control form-sm" value="<?php echo $fecIniReporte ?>"><?php }
                                            else{?><input type="date" id="fecIniReporte" name="fecIniReporte" class="form-control form-sm" required><?php }
                                            ?>

                                            <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 20px"></span>

                                            <span class="input-group-addon input-sm"><label for="fecFinReporte">FECHA FINAL:</label></span>
                                            <?php
                                            if($fecIniReporte != null){?><input type="date" id="fecFinReporte" name="fecFinReporte" class="form-control form-sm" value="<?php echo $fecFinReporte ?>"><?php }
                                            else{?><input type="date" id="fecFinReporte" name="fecFinReporte" class="form-control form-sm" value="<?=date('Y-m-d')?>" required><?php }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group selectDispo" style="margin-left: 0px; margin-top: 10px">
                                            <span class="input-group-addon input-sm"><label for="filtroCco">CENTRO DE COSTOS:</label></span>
                                            <!-- <input type="text" id="funEntrega" name="funEntrega" class="form-control form-sm" style="width: 732px"> -->
                                            <select id="filtroCco" name="filtroCco" class="form-control form-sm" onchange="createSelectDispo()">
                                                <?php
                                                    createSelectCco();
                                                    echo "<option value='TODOSCCO'>TODOS LOS CENTROS DE COSTOS</option>";
                                                ?>
                                                <option selected value="<?=$resultado = ($filtroCco == '') ? $ccoUnidad:$filtroCco?>">
                                                    <?php
                                                        if($filtroCco == '')echo $ccoUnidadMostrar;
                                                        else echo $filtroCco.' - ' . datosUnidadxCco($filtroCco,$conex);
                                                    ?>
                                                </option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group selectDispo" style="margin-left: 10px; margin-top: 10px">
                                            <span class="input-group-addon input-sm"><label for="filtroDispo">DISPOSITIVOS:</label></span>
                                            <!-- <input type="text" id="funEntrega" name="funEntrega" class="form-control form-sm" style="width: 732px"> -->
                                            <select id="filtroDispo" name="filtroDispo" class="form-control form-sm" style="min-width:150px" onchange="createSelectCodReu()">
                                                
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group selectDispo" id="buscador" style="margin-left: 10px; margin-top: 10px;">
                                            <span class="input-group-addon input-sm"><label for="filtroCodReu">CODIGO REUSO:</label></span>
                                            <input type='text' id='autocomplete' class="form-control form-sm" style="min-width:150px" >
                                            <input type="hidden" id="filtroCodReu" name="filtroCodReu" value="">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="input-group" style="margin-top: 15px; text-align:center; display:block;">
                                <input type="submit" class="btn btn-primary btn-sm" value="Consultar" title="Consultar" style="width: 80px; margin-left: 10px">
                            </div>
                            
                            <input type="hidden" name="unidad" value="<?php echo $ccoUnidad ?>">
                            <input type="hidden" name="pestana" value="3">
                        </form>
                    </div>
                    <div>
                    <!-- CONSTRUYE LA TABLA CON EL REPORTE -->
                        <?php $builResumeTable = buildResumeTable($fecIniReporte, $fecFinReporte, $filtroCco, $filtroDispo, $filtroCodReu);
                        if($fecIniReporte != null and $fecFinReporte != null){
                            if ($builResumeTable){?>
                                <form method="POST" action="trazExport.php?wemp_pmla=<?=$wemp_pmla?>">
                                    <div id="divExcel" class="divExcel">
                                        <table style="width: 100%">
                                            <tr align="center">
                                                <td>
                                                    <input type="hidden" id="fecIniExcel" name="fecIniExcel" value="<?=$fecIniReporte?>">
                                                    <input type="hidden" id="fecFinExcel" name="fecFinExcel" value="<?=$fecFinReporte?>">
                                                    <input type="hidden" id="CcounidadExcel" name="CcounidadExcel" value="<?=$filtroCco?>">
                                                    <input type="hidden" id="DispoExcel" name="DispoExcel" value="<?=$filtroDispo?>">
                                                    <input type="hidden" id="CodReuExcel" name="CodReuExcel" value="<?=$filtroCodReu?>">
                                                    <input type="hidden" id="accion" name="accion" value="excel">
                                                    <input type="hidden" id="wemp_pmla" name="wemp_pmla" value="<?=$wemp_pmla?>">
                                                    <h4>Exportar a EXCEL: 
                                                    <input type="image" id="imprimir" title="Exportar a EXCEL" src="../../../matrix/images/medical/root/export_to_excel.gif" 
                                                            onclick="this.form.submit()">
                                                    </h4>
                                                            <!-- onclick="exportarEx(fecIniExcel,fecFinExcel,CcounidadExcel,DispoExcel,CodReuExcel,accion); return false"> -->
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </form>
                                <table class='table table-bordered'>
                                <thead class='encabezadoTabla'>
                                    <th class='text-center'>C&oacute;digo Reuso</th>
                                    <th class='text-center'>FECHA DE USO</th>
                                    <th class='text-center'>NOMBRE DE USUARIO</th>
                                    <th class='text-center'>DOCUMENTO</th>
                                    <th class='text-center'>QUIROFANO</th>
                                    <th class='text-center'>OBSEVACIONES</th>
                                    <th class='text-center'>FUNCIONARIO QUE ENTREGA EST.</th>
                                    <th class='text-center'>DETALLES</th>
                                </thead>
                                
                                <?=$builResumeTable?> 

                                </table>
                                
                        <?php 
                            }else{
                                echo '<h4>No existen reusos gestionados para el servicio SELECCIONADO</h4>';
                            }
                        }else{
                            ?><h3>Debe Seleccionar Fecha Inicial y Fecha Final...</h3><?php
                        }
                        ?>
                    </div> 
        </div>
    </div>
</div>
<?php
     }

?>
</body>
</html>