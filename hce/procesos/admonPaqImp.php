<?php
include_once("conex.php");
/**
2014-01-13: Frederick Aguirre, Se agregan los formularios externos a la lista de formularios posibles para el paquete      
**/

if( !isset($_SESSION['user']) && isset($peticionAjax) )
{
  if( $tipoPeticion == "json" ){
    $data = array( 'error'=>"error" );
    echo json_encode($data);
    return;
  }
    echo 'error';
    return;
}





global $conex;
$hoy  = date("Y-m-d");
$hora = date("H:i:s");
DEFINE( "ORIGEN","Admon paquetes" );
DEFINE( "LOG","000007" );

$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
$caracteres = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");


/* MANEJO DE LOG */
function insertLog( $conex, $wcenimp, $user_session, $accion, $tabla, $err, $descripcion, $identificacion, $sql_error = "", $wmodalidad ){
	$descripcion = str_replace("'",'"',$descripcion);
	$sql_error = ereg_replace('([ ]+)',' ',$sql_error);

	$insert = " INSERT INTO ".$wcenimp."_".LOG."
					(Medico, Fecha_data, Hora_data, logori, Logcdu, Logmod, Logacc, Logtab, Logerr, Logsqe, Logdes, Loguse, Logest, Seguridad)
				VALUES
					('".$wcenimp."','".date("Y-m-d")."','".date("H:i:s")."', '".ORIGEN."', '".utf8_decode($identificacion)."', '".$wmodalidad."', '".utf8_decode($accion)."','".$tabla."','".$err."', '".$sql_error."','".$descripcion."','".$user_session."','on','C-".$user_session."')";

	$res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());
}


/* FUNCIONES LLAMADAS DESDE PETICIONES AJAX */

function armarHtmlPaquetes( $paquetes ){
    global $wusuario;
	$bordeArriba='';
     $respuesta = "<div align='right'><input type='checkbox' name='chk_verInactivos' onclick='verPaquetesInactivos( this );'><b>   VER PAQUETES INACTIVOS</b></div>";
    $respuesta .= "<br><br><table id='tbl_paquetesCreados' style='border: 3px solid; {$bordeArriba} border-color:#FFFFFF;''>";
    $respuesta .= "<tr class='encabezadotabla'>";
        $respuesta .= "<td style=' border-color:#FFFFFF;' align='center'>N&Uacute;MERO<br>PAQUETE</td>";
        $respuesta .= "<td style=' border-color:#FFFFFF;' align='center'>DESCRIPCION</td>";
        $respuesta .= "<td style=' border-color:#FFFFFF;' align='center'>C. DE COSTOS</td>";
        $respuesta .= "<td style=' border-color:#FFFFFF;' align='center'>GRUPO EMP.</td>";
        $respuesta .= "<td style=' border-color:#FFFFFF;' align='center'>USUARIO</td>";
        $respuesta .= "<td style=' border-color:#FFFFFF;' align='center'>ACTIVO</td>";
    $respuesta .="</tr>";
    $numPaquetes = count( $paquetes );
    $i = 0;
        foreach ($paquetes as $codigo => $datos) {
            $i++;

            ( $datos['grupo']  == "*" )  ? $nombreGrupo = "SIN FILTRAR" : $nombreGrupo = consultarGrupoEmpresarial( $datos['grupo'] );
            ( $datos['cco']    == "*" )  ? $nombreCco = "SIN FILTRAR" : $nombreCco = consultarCco( $datos['cco'] );
            ( $datos['estado'] == "on" ) ? $activo = "checked" : $activo = "";
            ( $datos['estado'] == "on" ) ? $display = "" : $display = " display:none; ";
            ( $i == $numPaquetes ) ? $bordeAbajo    = "  border-bottom: 3px solid; border-color:#FFFFFF; " : $bordeAbajo = "" ;
            $nombreUsuario  = consultarNombreUsuario( $datos['usuario'] );
            $funcionEdicion = "  onclick='verPaquete( \"{$datos['id']}\", \"{$codigo}\", \"{$datos['grupo']}\", \"{$datos['cco']}\", \"{$datos['descripcion']}\", \"{$datos['formularios']}\");' ";
            $wclass = 'fila1';
             $respuesta  .= "<tr class='{$wclass}' style='height:20px; {$display}' estado='{$datos['estado']}'>";
                $respuesta .= "<td align='center'    style ='cursor:pointer; {$bordeAbajo} border-color:#FFFFFF;' {$funcionEdicion} >{$codigo}</td>";
                $respuesta .= "<td align='left'      style ='cursor:pointer; {$bordeAbajo} border-color:#FFFFFF;' {$funcionEdicion} >{$datos['descripcion']}</td>";
                $respuesta .= "<td align='center'    style ='cursor:pointer; {$bordeAbajo} border-color:#FFFFFF;' {$funcionEdicion} >{$nombreCco}</td>";
                $respuesta .= "<td align='left'      style ='cursor:pointer; {$bordeAbajo} border-color:#FFFFFF;' {$funcionEdicion} >{$nombreGrupo}</td>";
                $respuesta .= "<td align='left'      style ='cursor:pointer; {$bordeAbajo} border-color:#FFFFFF;' {$funcionEdicion} >({$datos['usuario']}) ".$nombreUsuario."</td>";
                $respuesta .= "<td align='center'    style ='cursor:pointer; {$bordeAbajo}'><input type='checkbox' {$activo} id='input_anular' registro='{$datos['id']}' onclick='anularPaquete( this )'></td>";
               $respuesta .="</tr>";
        }
    $respuesta .= "</table><br><br>";
    return($respuesta);
}

function consultarCco( $codigo ){
    global $conex, $wbasedato;

    $nombre = $codigo;
    $q      = " SELECT Ccocod, UPPER( Cconom ) nombre
                  FROM {$wbasedato}_000011
                 WHERE Ccocod = '{$codigo}'
                   AND Ccoest = 'on' ";

    $rs     = mysql_query( $q, $conex ) or die( mysql_error() );
    $row    = mysql_fetch_array( $rs );
    $nombre =  $row['nombre'];
    return( $nombre );
}

function consultarGrupoEmpresarial( $codigo ){
    global $conex, $whcebasedato, $wcenimp ;
    $query = " SELECT Empdes nombre
                 FROM {$wcenimp}_000008
                WHERE Empcod = '{$codigo}'";
    $rs    = mysql_query( $query, $conex ) or die( mysql_error() );
    $row   = mysql_fetch_array( $rs );
    $nombre =  $row['nombre'];
    return( $nombre );
}

function consultarNombreUsuario( $codigo ){
    global $conex;
    $queryUsuario  = "SELECT descripcion
                        FROM usuarios
                       WHERE codigo = '{$codigo}'";
    $rsUsuario     =   mysql_query( $queryUsuario, $conex );
    $rowUsuario    =   mysql_fetch_array( $rsUsuario );
    return( $rowUsuario['descripcion'] );
}

function buscarEntidadesGrupo( $codigoGrupo ){

    global $whcebasedato, $conex, $wcenimp, $wcliame;
    global $caracteres, $caracteres2;
    $listaEntidades = "";

    ( $codigoGrupo == "*" ) ? $buscarCodigo = "" : $buscarCodigo = " Empcod = '{$codigoGrupo}' AND ";
    $query = " SELECT Empemp empresas, Empcod codigoGrupo, Empdes nombreGrupo
           FROM {$wcenimp}_000008
          WHERE {$buscarCodigo}
                Empest = 'on'";
    //echo $query;
    $rs     = mysql_query( $query, $conex ) or die( mysql_error() );
    $numGru = mysql_num_rows( $rs );

    while( $row  = mysql_fetch_array( $rs ) ){
      $aux   = explode( ",", $row['empresas'] );

      foreach ($aux as $i => $empresa) {
        $aux[$i] = "'".$aux[$i]."'";
      }
      $condicionEmpresas = implode( ",", $aux );

      $query  = " SELECT Empcod codigo, Empnit nit, Empnom nombre
                    FROM {$wcliame}_000024
                   WHERE Empcod in ({$condicionEmpresas})
                   GROUP BY codigo, nombre";
        $rs2    = mysql_query( $query, $conex );
        $num    = mysql_num_rows( $rs );
      //echo $query;
      if( $num > 0 ){
       // $listaEntidades .= "<div align='left' style=' cursor:pointer; width:100%; font-size: 7pt;color:#2A5DB0;font-weight:bold; '>";
        /*$listaEntidades .= "<div style=' cursor:pointer; '>";
        $listaEntidades .= $row['codigoGrupo'].", ".$row['nombreGrupo'];
        $listaEntidades .= "</div></div>";*/
        ( $numGru > 1 ) ? $displayGrupo = " display:none;" : $displayGrupo = "";
        $listaEntidades .= "<div id='div_empresas_{$row['codigoGrupo']}' style='width:100%; {$displayGrupo} '><table style='border: 1px solid; border-color:#2A5DB0; font-size:12; width:100%;'>";
       // if( ( $numGru > 1 ) ) $listaEntidades .= "<tr class='encabezadotabla'><td colspan='2'><input type='checkbox' checked id='chk_{$row['codigoGrupo']}' onclick='elegirTodos( \"{$row['codigoGrupo']}\" , this )' />&nbsp;&nbsp;TODOS</td>";

        while( $row2 = mysql_fetch_array( $rs2 ) ){

          //$nombre = str_replace($caracteres, $caracteres2, $row2['nombre']);
          $nombre = utf8_encode( $row2['nombre'] );
          $listaEntidades .= "<tr><td>{$row2['codigo']} - {$nombre} </td></tr>";
        }

        $listaEntidades .= "</table><br></div>";
      }
    }
    return( $listaEntidades );
}

function traerGruposEmpresa(){

    global $conex, $wcenimp ;
    $query = " SELECT Empcod codigo, Empdes nombre
                 FROM {$wcenimp}_000008
                WHERE Empest = 'on'";
    $rs    = mysql_query( $query, $conex );

    ( count( $rs ) > 0 ) ? $select = "<select name='input_grupo' id='input_grupo' style='font-size:11;' onchange='busarEntidadesEnGrupo( this )'>" : $select = "" ;
    ( count( $rs ) > 0 ) ? $select .= "<option value='*' selected >TODAS LAS EMPRESAS</option>" : $select .= "" ;

    while ( $row = mysql_fetch_array( $rs ) ) {

          ( $row['codigo'] == "*" ) ? $seleccionado = "selected" : $seleccionado = "";
          $select .= "<option value='{$row['codigo']}' {$seleccionado}>".utf8_encode($row['nombre'])."</option>";

    }

    ( count( $rs ) > 0 ) ? $select .= "</select>" : $select .= "" ;
    ( count( $rs ) > 0 ) ? $select .= "<img title='ver empresas' id='img_detallar' src='../../images/medical/movhos/info.png' style='display:none' width='15px' height='15px' onClick='verEmpresasDiv();'>" : $select .= "" ;
    return( $select );
}

/* FINAL DE FUNCIONES LLAMADAS DESDE PETICIONES AJAX */

/* PETICIONES AJAX */

if( !isset( $peticionAjax ) ) $peticionAjax='';

if( $peticionAjax == "buscarPaquetes" ){

    $paquetes = array();
    $query    = " SELECT id codigo, paqdes descripcion, paqgru grupo, paqfor formularios, Paqcco cco, Paqest estado, id, Seguridad
                    FROM {$wcenimp}_000004
                   WHERE paqmod = '{$wmod}'";
    $rs       = mysql_query( $query, $conex ) or die( mysql_error() );

    while( $row = mysql_fetch_array( $rs ) ){

        $paquetes[$row['codigo']]['descripcion'] = $row['descripcion'];
        $paquetes[$row['codigo']]['formularios'] = $row['formularios'];
        $paquetes[$row['codigo']]['grupo']       = $row['grupo'];
        $paquetes[$row['codigo']]['cco']         = $row['cco'];
        $paquetes[$row['codigo']]['id']          = $row['id'];
        $paquetes[$row['codigo']]['usuario']     = $row['Seguridad'];
        $paquetes[$row['codigo']]['estado']      = $row['estado'];

    }

    ( count($paquetes) <= 0 ) ? $respuesta =  "<span class='subtituloPagina2'> Sin Paquetes Configurados </span>" : $respuesta = armarHtmlPaquetes( $paquetes );
    $data = array( "paquetes"=>$respuesta );
    echo json_encode( $data );
    return;
}

if( $peticionAjax == "traerFormularios" ){

    $respuesta = imprimirArbolCompleto();
    $data      =  array( 'formularios' => $respuesta );
    echo json_encode( $data );
    return;
}

if( $peticionAjax == "crearPaquete" ){

    $wmod        = $_POST['wmod'];
    $wdes        = $_POST['wdes'];
    $wgru        = $_POST['wgru'];
    $wfrs        = $_POST['wfrs'];
    $wcco        = $_POST['wcco'];
    $wuser       = $_POST['wusuario'];
    $waccion     = $_POST['waccion'];
    $id          = $_POST['id'];
    $numRegistro = "";
    $codigos = array();

    ( $wcco == "*" ) ? $wcco = "%" : $wcco = $wcco;
    ( $wgru == "*" ) ? $wgru = "%" : $wgru = $wgru;
    if( $waccion == "crear" ){

        $formulariosAux = explode( ",", $wfrs );
        foreach ( $formulariosAux as $i => $value ) {
          if( !in_array( $formulariosAux[$i], $codigos ) ){
            array_push( $codigos, $value );
          }
        }

        $formulariosAux = implode( ",", $codigos );

        $query = " INSERT INTO {$wcenimp}_000004 ( Medico, Fecha_data, Hora_data, Paqdes, Paqmod, Paqgru, Paqcco, Paqfor, Seguridad)
                                             VALUES  ( '{$wcenimp}', '{$hoy}', '{$hora}', '{$wdes}', '{$wmod}', '{$wgru}', '{$wcco}', '{$formulariosAux}', '{$wuser}')";
        $rs = mysql_query( $query, $conex ) or die( mysql_error() );
        $id = mysql_insert_id();
    }

    if( $waccion == "actualizar" ){
        $query = "UPDATE {$wcenimp}_000004
                     SET Fecha_data = '{$hoy}',
                         Hora_data  = '{$hora}',
                         Paqdes     = '{$wdes}',
                         Paqgru     = '{$wgru}',
                         Paqcco     = '{$wcco}',
                         Paqfor     = '{$wfrs}',
                         Paqued     = '{$wuser}'
                  WHERE id = '{$id}'";
        $rs          = mysql_query( $query, $conex ) or die( mysql_error() );
        $numRegistro = $id;
        $id          = mysql_affected_rows();

		if( $id ){
			$accion         = "update";
			$tabla          = "000005";
			$descripcion    = "Cambio de Datos";
			insertLog( $conex, $wcenimp, $wuser, $accion, $tabla, $err, $descripcion, $id, $sql_error, $wmodalidad );
		}
    }

    if( $id > 0 ){
        ( $waccion == "crear" ) ? $respuesta = "<br><br><span class='subtituloPagina2'> PAQUETE N&Uacute;MERO {$id} CREADO EXITOSAMENTE</span><br><br><br>" : $respuesta = "<br><br><span class='subtituloPagina2'> PAQUETE N&Uacute;MERO {$id} ACTUALIZADO EXITOSAMENTE</span><br><br><br>";
    }
    $data = array( "error"=>$id, "respuesta"=>$respuesta );
    echo json_encode( $data );
    return;
}

if( $peticionAjax == "anularPaquete" ){

  $id          = $_POST['wid'];
  $wusuario    = $_POST['wusuario'];
  $westado     = $_POST['westado'];

  $query     = "UPDATE {$wcenimp}_000004
                   SET Paqest = '{$westado}',
                       Paqued = '{$wusuario}'
                 WHERE     id = '{$id}'";
  $rs        =  mysql_query( $query, $conex );
  $afectados =  mysql_affected_rows();
  if( $afectados*1 > 0 ){
	$accion         = "update";
	$tabla          = "000005";
	$descripcion    = "Anulacion de Paquete";
	insertLog( $conex, $wcenimp, $wuser, $accion, $tabla, $err, $descripcion, $id, $sql_error, $wmodalidad );
  }
  $data = array( 'afectados'=> $afectados );
  echo json_encode( $data );
  return;
}

if( $peticionAjax == "buscarEntidadesEnGrupo" ){

    $entidades = buscarEntidadesGrupo( $_REQUEST['wcodgru'] );

    ($entidades == "") ? $respuesta = 1 : $respuesta = $entidades;
    //$data = array( "entidades"=>$entidades, "error"=>$error );
    echo $respuesta;
    return;
}

if( $peticionAjax == "actualizarSelectGrupos" ){
  $select  = traerGruposEmpresa();
  echo $select;
  return;
}

/* FINAL PETICIONES AJAX */
?>

<html>
<head>
    <title>ADMINISTRACI&Oacute;N PAQUETES  DE IMPRESI&Oacute;N </title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1;" />
    <style type="text/css">
        .pestanaSelected{
            font-size:13px;
            font-weight:bold;
            background:#638cb5;
            border:0px;
            margin-left: 1%;
         }

         .pestana{
            font-family:Verdana,Helvetica;
            color:white;
            border:0px;
            width:180px;
            text-align: center;
            cursor: pointer;
         }

         .categoriaSelected{
            background:#638cb5;
            color:white;
         }

       ul{
            list-style-type: circle;
            padding-left: 14px;
            margin: 10px;
         }

       .modal{
            display:none;
            cursor:default;
            background:none;
            repeat scroll 0 0;
            position:relative;
            width:98%;
            height:98%;
            overflow:auto;
        }
    </style>
    <script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" ></script>
    <script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
    <script type="text/javascript">

        <!-- VARIABLES JAVASCRIPT GLOBALES -->
        var modalidadSeleccionada;
        var facturacionActual;
        var whcebasedato;
        var wbasedato;
        var wemp_pmla;
        var wusuario;
        var wcenimp;
        var wcliame;

        $( document ).ready(function(){

            modalidadSeleccionada = $("#modalidadSeleccionada");
            facturacionActual     = $("#facturacionActual");
            whcebasedato          = $("#whcebasedato").val();
            wbasedato             = $("#wbasedato").val();
            wcenimp               = $("#wcenimp").val();
            wemp_pmla             = $("#wemp_pmla").val();
            wusuario              = $("#wusuario").val();
            wcliame               = $("#wcliame").val();

        });

        function nuevaCategoria( modalidad ){
            modalidad = jQuery( modalidad );
            categoriaAnterior = modalidadSeleccionada.val();

            if( modalidad.attr( "categoria" ) == categoriaAnterior )
            {
                modalidadSeleccionada.val( "sin" );
                facturacionActual.val( "sin" );
                $("#div_EdicionCreacion").hide();
                $("#div_sinSeleccion").show();
                modalidad.removeClass("categoriaSelected");

            }else{
                 modalidadSeleccionada.val( modalidad.attr( "categoria" ) );
                 facturacionActual.val( modalidad.attr( "facturacion" ) );
                 modalidad.siblings().removeClass("categoriaSelected");
                 modalidad.addClass("categoriaSelected");
                 $("#pest_Editar").attr("actual", "no");
                 $("#pest_Crear").attr("actual", "no");
                 $("#pest_editar_grupos_empresas").attr("actual", "no");
                 $("#pest_Editar").click();
                 $("#div_sinSeleccion").hide();
                 $("#div_EdicionCreacion").show();
             }
             if( modalidad.attr( "facturacion" ) == "on" ){
                $("#pest_editar_grupos_empresas").show();
                $("#td_relleno").width('40%');
             }else{
                $("#pest_editar_grupos_empresas").hide();
                $("#td_relleno").width('60%');
             }
        }

        function mostrarPaquetes( codigo, facturacion ){
            $.ajax({
                url: "admonPaqImp.php",
                type: "POST",
                async: false,
                before: $.blockUI({ message: $('#msjEspere') }),
                data: {
                     peticionAjax: "buscarPaquetes",
                     whcebasedato: whcebasedato,
                        wbasedato: wbasedato,
                          wcenimp: wcenimp,
                             wmod: codigo,
                            wfact: facturacion,
                         wusuario: wusuario,
                     tipoPeticion: "json"
                      },
                success: function(data)
                {
                    if( data.error == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                      return;
                    }
                    $("#div_contenedor_paquetes").html(data.paquetes);
                    $("#div_contenedor_crearPaquete").hide();
                    $("#div_contenedor_admon_grupos").hide();
                    $("#div_respuestasAjax").hide();
                    $("#div_contenedor_paquetes").show();
                    $.unblockUI();
                },
                dataType: "json"
            });
        }

        function mostrarCrearPaquete( codigo, facturacion ){

            $("#div_contenedor_paquetes").hide();
            $("#div_respuestasAjax").hide();
            $("#div_contenedor_admon_grupos").hide();
            $("#div_contenedor_crearPaquete").show();
        }

        function cambiarAccion( pestana ){

            pestana = jQuery( pestana );

            if(  pestana.attr( "actual" ) == "no" ){
                     pestana.attr( "actual", "si" );
                     pestana.removeClass( "fila1" );
                     pestana.addClass( "pestanaSelected" );
                     pestana.siblings(".pestana").attr( "actual", "no" );
                     pestana.siblings(".pestana").removeClass( "pestanaSelected" );
                     pestana.siblings(".pestana").addClass( "fila1" );
                     reiniciarCampos();
                }else{
                     return;
                }
            $('#iframe_empresas_cenimp').attr("src", "");
            if( pestana.attr("accion") == "editar"){
                  $( "#waccion" ).val( "actualizar" );
                  actualizarSelectGrupos();
                  mostrarPaquetes( modalidadSeleccionada.val(), facturacionActual.val() );
             }

            if( pestana.attr("accion") == "crear" ){
               $( "#waccion" ).val( "crear" );
               $( "#wid" ).val( "" );
               actualizarSelectGrupos();
               mostrarCrearPaquete( modalidadSeleccionada.val(), facturacionActual.val() );
            }

            if( pestana.attr("accion") == "editar_grupos_empresas" ){
              $("#div_contenedor_crearPaquete").hide();
              $("#div_respuestasAjax").hide();
              $("#div_contenedor_paquetes").hide();
              $('#iframe_empresas_cenimp').attr("src", "EmpresasCenimp.php?wemp_pmla="+wemp_pmla);
             /* include = 'EmpresasCenimp.php?wemp_pmla='+wemp_pmla;
              $.post(include, function(data) {
                  $('#div_contenedor_admon_grupos').html(function() {
                                    return data;
                      }
                  );
              });*/
              $("#div_contenedor_admon_grupos").show();
            }
        }

        function crearPaquete(){

            var formularios   = "";
            var descripcion   = $( "#input_desc" ).val();
            var grupoEmpresas = $( "#input_grupo" ).val();
            var centroCostos  = $( "#input_cco" ).val();
            var accion        = $( "#waccion" ).val();
            var identificador = $( "#wid" ).val();

            if( $.trim(descripcion) == ""){
                    alerta( "Agregue una descripcion por favor");
                    return;
            }

            var j=0;
            $("#tabla_arbol_completo").find( ".formulario_arbol_completo[esPadre='off']:checked" ).each( function(){
                if(j>0)
                    formularios+=",";
                formularios += $(this).val();
                j++;
            });

            if( $.trim( formularios ) == "" )
            {
              alerta( " Debe elegir por lo menos un formulario para el paquete ");
              return;
            }

            //muestra el mensaje de cargando
           // $.blockUI({ message: $('#msjEspere') });

            //Realiza el llamado ajax con los parametros de busqueda
            $.ajax({
                url: "admonPaqImp.php",
                type: "POST",
                async: true,
                before: $.blockUI({ message: $('#msjEspere') }),
                data: {
                     peticionAjax: "crearPaquete",
                          wcenimp: wcenimp,
                          waccion: accion,
                             wmod: modalidadSeleccionada.val(),
                             wdes: descripcion,
                             wgru: grupoEmpresas,
                             wfrs: formularios,
                             wcco: centroCostos,
                               id: identificador,
                         wusuario: wusuario,
                     tipoPeticion: "json"
                      },
                success: function(data)
                {
                    $.unblockUI();
                    if( data.error == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                      return;
                    }
                    if(data.error != 0)
                    {
                        if( accion == "actualizar" )
                        {
                            cerrarClonEditarPaquete();
                            alerta(  " Actualizaci&oacute;n exitosa " );

                            setTimeout( function(){
                              $("#div_contenedor_paquetes").attr( "actual", "no");
                              $("#div_contenedor_paquetes").click();
                              mostrarPaquetes( modalidadSeleccionada.val(), facturacionActual.val() );
                            }, 1600 );

                        }
                        $("#div_respuestasAjax").html( data.respuesta );
                        $("#div_contenedor_crearPaquete").hide();
                        $("#div_contenedor_admon_grupos").hide();
                        if( accion == "crear" )
                        {
                            $("#div_contenedor_paquetes").hide();
                            $("#div_respuestasAjax").show();
                        }
                        reiniciarCampos();
                    }
                },
                dataType: "json"
            });
        }

        function checkearColumna( check ){

            check  = jQuery( check );
            codigo = check.val();

            if( check.is( ":checked" ) ){
                $("input[type=checkbox][codigoRelacion^="+codigo+"]:not(:checked)").each(function(){

                    codigoAuxiliar = $(this).val();
                    $("input[type=checkbox][value='"+codigoAuxiliar+"']:not(:checked)").each(function(){

                       if( $(this) != check )
                         $(this).attr("checked", true);

                       if( $(this).attr( "esPadre" ) != "on" )
                        $(this).parent().next().addClass("fondoAmarillo");

                    });
                });
            }else{
                  $("input[type=checkbox][codigoRelacion^="+codigo+"]:checked").each(function(){

                      codigoAuxiliar = $(this).val();
                      $("input[type=checkbox][value='"+codigoAuxiliar+"']:checked").each(function(){
                        if( $(this) != check )
                          $(this).attr("checked", false);
                          $(this).parent().next().removeClass("fondoAmarillo");
                      });

                  })
            }
        }

        function reiniciarCampos(){

            $( "#input_desc" ).val( "" );
            $("#input_grupo option[value='*']").attr("selected",true);
            $("#input_cco option[value='*']").attr("selected",true);
            $("#tabla_arbol_completo").find( ".formulario_arbol_completo:checked" ).parent().next().removeClass( "fondoAmarillo" );
            $("#tabla_arbol_completo").find( ".formulario_arbol_completo:checked" ).attr( "checked", false );
            $("#tabla_arbol_completo").find( ".nodo_arbol_completo:checked" ).attr( "checked", false );
            $( "#img_detallar" ).hide("");
        }

        function verPaquete( id, codigoPaquete, codigoGrupo, codigoCco, descripcion, formus ){

            $( "#input_desc" ).val( descripcion );
            $( "#wid" ).val( id );
            $( "#waccion" ).val( "actualizar" );
            $("#input_grupo option[value='"+codigoGrupo+"']").attr("selected",true);
            $("#input_cco option[value='"+codigoCco+"']").attr("selected",true);
            $( "#contenido_empresas_grupo" ).html("");
            $( "#img_detallar" ).hide("");

            var forms          = formus.split(",");
            var numFormularios = forms.length;
            for( var i = 0; i < numFormularios; i++ ){
               cod = forms[i];
               $("input[type=checkbox][value="+cod+"]:not(:checked)").parent().next().addClass("fondoAmarillo");
               $("input[type=checkbox][value="+cod+"]:not(:checked)").attr("checked",true);
            }

            $("#div_contenedor_crearPaquete").addClass("modal");
            $("input[name='btn_cerrar_modal']").show();
            mostrarClonEditarPaquete();
            formularios = "";
        }

        function mostrarClonEditarPaquete(){
            div = "div_contenedor_crearPaquete";
            $.blockUI({
                        message: $("#"+div),
                        css: { left: '5%',
                                top: '5%',
                              width: '90%',
                             height: '90%'
                            }
              });
        }

        function cerrarClonEditarPaquete(){
            $.unblockUI();
            $("#div_contenedor_crearPaquete").hide();
            $("#div_contenedor_admon_grupos").hide();
            $("input[name='btn_cerrar_modal']").hide();
            $("#div_contenedor_crearPaquete").addClass("modal");
            reiniciarCampos();
        }

        function chequearFormulario( check ){
           var chk = jQuery( check );
           var codigoFormulario = chk.val();
           if( chk.attr("checked") )
           {
             chk.parent().next().addClass("fondoAmarillo");
             $("input[type=checkbox][value='"+codigoFormulario+"']:not(:checked)").each(function(){

                if( $(this) != chk )
                    $(this).attr("checked", true);
                $(this).parent().next().addClass("fondoAmarillo");

              });
           }else
              {

                chk.parent().next().removeClass("fondoAmarillo");
                $("input[type=checkbox][value='"+codigoFormulario+"']:checked").each(function(){
                  if( $(this) != chk )
                    $(this).attr("checked", false);
                     $(this).parent().next().removeClass("fondoAmarillo");
                });

              }
        }

        function anularPaquete( check ){

          chk = jQuery( check );
          wid = chk.attr("registro");
          if( chk.is(":checked") )
              westado = "on";
            else
              westado = "off";
          $.ajax({
                url: "admonPaqImp.php",
                type: "POST",
                async: false,
                before: $.blockUI({ message: $('#msjEspere') }),
                data: {
                     peticionAjax: "anularPaquete",
                          wcenimp: wcenimp,
                         wusuario: wusuario,
                              wid: wid,
                          westado: westado,
                         wusuario: wusuario,
                     tipoPeticion: "json"
                      },
                success: function(data)
                {
                    if( data.error == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                      return;
                    }
                    $.unblockUI();
                    //console.log( data.afectados );
                },
                dataType: "json"
            });
        }

        function verPaquetesInactivos( check ){

          if( $(check).is(":checked") ){
            $("#tbl_paquetesCreados tr[estado='off']").show();
          }else{
            $("#tbl_paquetesCreados tr[estado='off']").hide();
          }
        }

        function alerta( txt ){
          $("#textoAlerta").html( txt );
          $.blockUI({ message: $('#msjAlerta') });
            setTimeout( function(){
                    $.unblockUI();
                  }, 1600 );
        }

        function busarEntidadesEnGrupo( slt_grupo ){
          var codigoGrupo = $(slt_grupo).val();
          var accion        = $( "#waccion" ).val();
          if( codigoGrupo == "*" ){
            $( "#contenido_empresas_grupo" ).html("");
            $( "#img_detallar" ).hide("");
            return;
          }

          $.ajax({
                      url: "admonPaqImp.php",
                      type: "POST",
                      async: false,
                      //before: $.blockUI({ message: $('#msjEspere') }),
                      data: {
                        peticionAjax: "buscarEntidadesEnGrupo",
                           wemp_pmla: wemp_pmla,
                             wcodgru: codigoGrupo,
                        whcebasedato: whcebasedato,
                             wcenimp: wcenimp,
                             wcliame: wcliame,
                        tipoPeticion: "normal"
                                },
                      success: function(data)
                      {
                        if( data == "error" ){
                          validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                          return;
                        }
                        if( ( data ) != 1 ){
                          $("#contenido_empresas_grupo").html( data );
                          $("#img_detallar").show();
                        }else{
                          $( "#contenido_empresas_grupo" ).html("");
                          $( "#img_detallar" ).hide("");
                          alerta( "No hay Entidades en el grupo seleccionado" );
                        }
                      }
                  });
        }

        function verEmpresasDiv(){
          $("#div_empresas_grupo").toggle();
        }

        function actualizarSelectGrupos(){
          var td = jQuery( $("#td_grupos_empresas") );
          td.html('');
          $.ajax({
                url: "admonPaqImp.php",
                type: "POST",
                data: {
                     peticionAjax: "actualizarSelectGrupos",
                          wcenimp: wcenimp,
                     tipoPeticion: "normal"
                      },
                success: function(data)
                {
                    if( data == "error" ){
                      validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
                      return;
                    }
                    td.html( data );
                }
            });
        }

        function validarExistenciaParametros( txt ){
          $("div [id!='div_sesion_muerta']").hide();
          $("#div_sesion_muerta").show();
        }
    </script>
</head>
<body>
<?php
include_once('root/comun.php');

session_start();
if(!isset($_SESSION['user']))
{
  die('error');
}

$wactualiz = "2014-01-13";
 $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$empresa = strtolower($institucion->baseDeDatos);
encabezado("ADMINISTRACI&Oacute;N PAQUETES DE IMPRESION",$wactualiz, $empresa);

/** FUNCIONES **/
FUNCTION consultarModalidadesImpresion( $arrayModalidades ){
    global $wcenimp, $conex;

    $query = "SELECT modcod codigo, moddes nombre, modfac facturacion
                FROM {$wcenimp}_000001
               WHERE modest = 'on'";
    $rs = mysql_query( $query, $conex ) or die( mysql_error() );

    while ( $row = mysql_fetch_array( $rs ) ) {
        $arrayModalidades[ $row['codigo'] ][ 'nombre' ] = $row[ 'nombre' ];
        $arrayModalidades[ $row['codigo'] ][ 'facturacion' ] = $row[ 'facturacion' ];
    }
}

function imprimirArbolCompleto(){
        global $conex;
        global $wbasedato,$wcenimp;
        global $whcebasedato;
        global $caracteres, $caracteres2;
               $formularios = "";
               $padreActual = "";

        $q = " SELECT Precod ,Predes, prenod, '' as Encpro
                 FROM {$whcebasedato}_000009
                WHERE prenod = 'on'
                  AND preest = 'on'
                UNION ALL
               SELECT Precod,Predes,prenod, Encpro
                 FROM {$whcebasedato}_000009, {$whcebasedato}_000001
                WHERE prenod = 'off'
                  AND mid(Preurl,1,1) = 'F'
                  AND Preurl = CONCAT( 'F=', Encpro )
                  AND Preest = 'on'
                ORDER BY 1 ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

        $k=round(($num/4),0);

        //=======================================================================================================================================
        //Lleno una matriz tal como se debe de mostrar, es decir se debe mostrar por columnas no por filas
        ///=======================================================================================================================================
        for ($j=1; $j<=4; $j++)
        {
            for ($i=1; $i<=$k+2; $i++)
            {
                $row = mysql_fetch_array($res);

                $matriz[$i][$j][1]=$row[0];
                $matriz[$i][$j][2]=$row[1];
                $matriz[$i][$j][3]=$row[2];
                $matriz[$i][$j][4]=$row[3];
            }
        }

       $formularios .= "<div id='tabla_arbol_completo'>";
       $formularios .= "<table style='border: 1px solid blue' id='tabla_arbol_completo'>";
       $formularios .= "<tr class=fila1><td align=center colspan=8><b><font size=5>ARBOL DE FORMULARIOS HCE</font></b></td></tr>";

       $formularios .= "<tr class=encabezadoTabla>";
       $formularios .= "<th>Sel.</th>";
       $formularios .= "<th>Opci&oacute;n</th>";
       $formularios .= "<th>Sel.</th>";
       $formularios .= "<th>Opci&oacute;n</th>";
       $formularios .= "<th>Sel.</th>";
       $formularios .= "<th>Opci&oacute;n</th>";
       $formularios .= "<th>Sel.</th>";
       $formularios .= "<th>Opci&oacute;n</th>";
       $formularios .= "</tr>";

       $wini = 'on';

        if ($wini=="on")
        {
            $wcolor="";
            $wini="off";
        }
        else
            $wcolor="FFFF99";

       $fila_color = false;
        for ($i=1; $i<=($k); $i++)
        {
            $formularios .= "<tr class=fila2>";
            for ($j=1; $j<=4; $j++)
            {
                if ($matriz[$i][$j][3]=='on')  //Si es un nodo
                {
                    //Sel.  Graba   Imp.
                    $filaa   = "";
                    $colspan = "2";
                    if( strlen( $matriz[$i][$j][1] ) > 1 ){
                       /* $filaa = "<br><table width='100%'>";
                        $filaa.="<tr>";
                        $filaa.="<td class='encabezadoTabla' width=24><input class='nodo_arbol_completo' esPadre='on' codigoRelacion='{$matriz[$i][$j][1]}' type='checkbox' value='".$matriz[$i][$j][1]."' onclick='checkearColumna( this )' /></td>";
                        $filaa.="<td>&nbsp;</td>";
                        $filaa.="</tr></table>";*/
                        $filaa.="<td class='encabezadoTabla' width=24><input class='nodo_arbol_completo' esPadre='on' codigoRelacion='{$matriz[$i][$j][1]}' type='checkbox' value='".$matriz[$i][$j][1]."' onclick='checkearColumna( this )' /></td>";
                        $colspan = "1";
                    }
                    $nombre      =  $matriz[$i][$j][2];
                    //$formularios .= "<td class='fila1' colspan=2><b>".$nombre."</b>".$filaa."</td>";
                    $formularios .= $filaa."<td class='fila1' colspan='{$colspan}'><b>".$nombre."</b></td>";
                }
                else
                {
                    $nombre      =  $matriz[$i][$j][2];
                    $formularios .= "<td><input class='formulario_arbol_completo' esPadre='off' codigoRelacion='{$matriz[$i][$j][1]}' type='checkbox' value='".$matriz[$i][$j][4]."' onclick='chequearFormulario( this );' /></td>";
                    $formularios .= "<td>". $nombre."</td>";
                }
            }
            $formularios .= "</tr>";
        }
		
		//2014-01-13 Se agregan los formularios externos a la lista de formularios posibles para el paquete      
	    $q = " SELECT Fexcod ,Fexdes, Fexurl
                 FROM {$wcenimp}_000009
                WHERE fexest = 'on'               
                ORDER BY 1 ";
        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);		
		if( $num > 0 ){
			$formularios .= "<tr class='encabezadotabla'><td align=center colspan=8>&nbsp;</td></tr>";
			$formularios .= "<tr class=fila1><td align=center colspan=8><b><font size=5>FORMULARIOS EXTERNOS DE HCE</font></b></td></tr>";
			$formularios .= "<tr class=encabezadoTabla>";
			$formularios .= "<th>Sel.</th>";
			$formularios .= "<th>Opci&oacute;n</th>";
			$formularios .= "<th>Sel.</th>";
			$formularios .= "<th>Opci&oacute;n</th>";
			$formularios .= "<th>Sel.</th>";
			$formularios .= "<th>Opci&oacute;n</th>";
			$formularios .= "<th>Sel.</th>";
			$formularios .= "<th>Opci&oacute;n</th>";
			$formularios .= "</tr>";
			$i=1;
			while( $row3 = mysql_fetch_array($res) ){
				if($i == 1){
					$formularios .= "<tr class=fila2>";	
				}
				$nombre      =  $row3[1];
				$formularios .= "<td><input class='formulario_arbol_completo' externo='on' esPadre='off' codigoRelacion='".$row3[0]."' type='checkbox' value='".$row3[0]."' onclick='chequearFormulario( this );' /></td>";
				$formularios .= "<td>".$nombre."</td>";	
				if($i == 4){
					$formularios .= "</tr>";
					$i=0;
				}
				$i++;
			}	
		}
		$formularios .= "</table>";
        //$formularios .= "<center><input type='button' onclick='crearPaquete()' value='Guardar' />";
        $formularios .= "</div>";
		
        return( $formularios );
}

function selectCco( $centros ){
    $select = "";
    $select .= "<select name='input_cco' id='input_cco' style='font-size:11;'>";
    foreach ($centros as $key => $centroCostos){
        $select .= "<option value='".$centroCostos->codigo."'>".$centroCostos->nombre."</option>";
    }
    $select .= "</select>";
    return( $select );
}


/** FINAL DE FUNCIONES **/

/** CREAR PARAMETRO EN LA 51 PARA LA APLICACION DE CENIMP **/
//variables
$user_session     = explode('-',$_SESSION['user']);
$user_session     = $user_session[1];
$whcebasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wbasedato        = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcenimp          = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenimp");
$wcliame          = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$arrayModalidades = array();
$centrosCostos    = consultaCentrosCostos( "Todos", "" );
consultarModalidadesImpresion( &$arrayModalidades );

?>
<!-- ESTA ES EL HTML INICIAL( MAQUETA DONDE SE CONSTRUYE LA FORMA DE DISTRIBUCIÓN EN PANTALLA )-->
<br><br>
<div id='menu_ppal' style='width:1600px;'>

    <input type='hidden' name='wemp_pmla' id='wemp_pmla' value='<?php echo trim($wemp_pmla) ?>'>
    <input type='hidden' name='modalidadSeleccionada' id='modalidadSeleccionada' value='sin'>
    <input type='hidden' name='facturacionActual' id='facturacionActual' value='sin'>
    <input type='hidden' name='whcebasedato' id='whcebasedato' value='<?php echo trim($whcebasedato) ?>'>
    <input type='hidden' name='wbasedato' id='wbasedato' value='<?php echo trim($wbasedato) ?>'>
    <input type='hidden' name='wcenimp' id='wcenimp' value='<?php echo trim($wcenimp) ?>'>
    <input type='hidden' name='wcliame' id='wcliame' value='<?php echo trim($wcliame) ?>'>
    <input type='hidden' name='wusuario' id='wusuario' value='<?php echo trim($user_session) ?>'>
    <input type='hidden' name='wid' id='wid' value=''>

    <table width='100%' style='border: 1px solid; border-color:#2A5DB0;'>
        <tr>
          <td colspan='2' class='encabezadoTabla'> PANEL DE ADMINISTRACI&Oacute;N</td>
        </tr>
        <tr>
          <td style='border-right: 1px solid; border-color:#2A5DB0; vertical-align:top;' width='25%' align='left' class='fila2'>
            <span class='subtituloPagina2'><b>Modalidades de Impresi&oacute;n</b></span>
            <br><br>
            <div style='width:100%; height:100%;'>
                <ul><?php
                        foreach ( $arrayModalidades as $key => $datos ) {
                                echo "<li style='font-size:13; height:25px; cursor:pointer; list-style-position:outside;' categoria='{$key}' facturacion='{$datos['facturacion']}' onclick=' nuevaCategoria( this ) '>".$datos[ 'nombre' ]."</li>";
                        }
                ?><ul>
            </div>
            </td>
            <td width='75%' style='vertical-align:top;' class='fila2' >
                 <span class='subtituloPagina2'><b>Administraci&oacute;n de Paquetes</b></span><br><br>
                 <div id='div_ContendorPpal' style='width:100%; height:100%;'>
                    <div id='div_sinSeleccion' style='width:100%;'><br><span class='subtituloPagina2'> <-- SELECCIONE UNA CLASIFICACI&Oacute;N</span></div>
                    <div id='div_EdicionCreacion' style='width:100%; display:none;'>
                        <input type='hidden' name='waccion' id='waccion' value='crear'>
                        <br>
                        <table style='border: 1px solid; border-color:#2A5DB0;' width='100%'>
                            <tr>
                              <td  id='pest_Editar' accion='editar' actual='no' class='pestanaSelected pestana' height='10%' width='20%' onclick='cambiarAccion( this );'> EDITAR PAQUETES </td><td  id='pest_Crear' accion='crear' actual='no' class='fila1 pestana' width='20%' onclick='cambiarAccion( this );' > CREAR PAQUETE </td><td  id='pest_editar_grupos_empresas' accion='editar_grupos_empresas' actual='no' class='fila1 pestana' style='display:none;' width='20%' onclick='cambiarAccion( this );' > ADMINISTRACI&Oacute;N DE GRUPOS </td><td id='td_relleno' width='60%'>&nbsp;</td>
                            </tr>
                            <tr>
                              <td colspan='4' height='90%' style='border-top: 1px solid; border-color:#2A5DB0;' align='center'>
								                <div id='div_contenedor_paquetes' style='width:100%; height:100%; display:none;'>editar</div>
                                <div id='div_contenedor_crearPaquete'  class='fila2' align='center' style='width:100%; height:100%; display:none;'>
                                    <br>
                                    <input type='button' name='btn_cerrar_modal' style='display:none;' value='Cerrar' onclick='cerrarClonEditarPaquete();'>
                                    <br>
                                      <div align='center'><input type='button' class='botona' value='GUARDAR' onclick='crearPaquete()'></div>
                                    <br>
                                    <table width='90%'>
                                        <tr><td class='encabezadotabla' align='left' style='font-size:11;'> DESCRIPCION: </td><td class='fila1' colspan='3'><input type='text' name='input_desc' id='input_desc' size='120' value=''></td></tr>
                                        <tr>
                                            <td class='encabezadotabla' align='left' style='font-size:11;'> GRUPO DE EMPRESAS: </td><td class='fila1' id='td_grupos_empresas'><?php echo traerGruposEmpresa() ?></td>
                                            <td class='encabezadotabla' style='font-size:11;'>Centro Costos:</td><td class='fila2'><?php echo selectCco( $centrosCostos ) ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan='2' align='left'>
                                              <div id='div_empresas_grupo' class='fila2' style='display:none; over-flow:scroll;' align='center'>
                                                <div id='contenido_empresas_grupo'></div>
                                              </div>
                                            </td>
                                        </tr>
                                        <tr><td colspan='4' align='center'>
                                            <br>
                                            <span class='subtituloPagina2'>SELECCION DE FORMULARIOS PARA EL PAQUETE</span>
                                            <div id='div_formularios' style='width:100%; height:100%' class='fila1' align='center'><?php echo imprimirArbolCompleto() ?></div><br>
                                            <br>
                                            <div align='center'><input type='button' class='botona' value='GUARDAR' onclick='crearPaquete()'></div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <br>
                                    <input type='button' name='btn_cerrar_modal' style='display:none;' value='Cerrar' onclick='cerrarClonEditarPaquete();'>
                                    <br>
                                </div>
                                <div id='div_contenedor_admon_grupos' align='center' style='background-color:white;' class='ui-tabs-panel ui-widget-content ui-corner-bottom '>
                                    <center>
                                    <iframe width='100%' id='iframe_empresas_cenimp' scrolling='yes' align='center' height='800' frameborder='1' marginheiht='0' border='0' framespacing='0' marginwidth='1' src=''>
                                    </iframe>
                                  </center>
                                </div>
                                <div id='div_respuestasAjax' align='center' name='div_respuestasAjax' style='display:none;'></div>
                              </td>
                             </tr>
                        </table>
                    </div>
                    <br>
                 </div>
            </td>
        </tr>
    </table>
</div>
<div id='msjAlerta' style='display:none;'>
  <br>
  <img src='../../images/medical/root/Advertencia.png'/>
  <br><br><div id='textoAlerta'></div><br><br>
</div>
<div id='msjEspere' style='display:none;'>
  <br>
    <img src='../../images/medical/ajax-loader5.gif'/>
  <br><br> Por favor espere un momento ... <br><br>
</div>
<br><br>
<div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center; display:none;' id='div_sesion_muerta'>
      <br /><br /><br /><br />
      [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
</div>
<center><input type=button id='cerrar_ventana' value='Cerrar Ventana' bloquear='no' onClick='javascript:cerrarVentana()' /></center>

</body>
</html>
