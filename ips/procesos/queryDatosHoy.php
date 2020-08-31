<?php
include ("conex.php");
$fechaActual = date('Y-m-d');
$horaActual  = date('H:i:s');
if( isset( $ejecutar ) ){

    $registros         = array();
    $nombreDocumento   = "archivoDatosHoy{$fechaActual}.sql";
    $tablasModificadas = array();
    $tablasXdump       = array();
    $tablasInserts     = array();
    $registrosInsertar = 0;
    $registrosDump     = 0;
    $registrosDumpReal = 0;
    $datos             = array( "nombreArchivo"=> $nombreDocumento, "tablasPendientesDump"=> array(), "horaInicio"=>"{$fechaActual} {$horaActual}", "horaFinalizacion"=>"", "error"=> 0 );
    $queryDrop           = "DROP TABLE IF EXISTS matrix.tablasModificadas";
    $rs                  = mysql_query( $queryDrop, $conex );
    $queryModifiedTables = "CREATE TABLE matrix.tablasModificadas
                            SELECT TABLE_NAME,  TABLE_ROWS
                              FROM information_schema.tables
                             WHERE TABLE_SCHEMA = 'matrix'
                               AND UPDATE_TIME between '{$fechaInicioLinea} {$horaInicioLinea}' AND '{$fechaActual} {$horaActual}'
                             ORDER BY TABLE_NAME";
    $results   = mysql_query( $queryModifiedTables, $conex );

    mysql_select_db("matrix");

    $query = " SELECT a.*
                 FROM tablasModificadas, tablasClasificadas a
                WHERE a.nombre = TABLE_NAME
                  AND omitir = 'off'";
    $rs    = mysql_query( $query, $conex );
    while ( $row = mysql_fetch_assoc( $rs ) ) {

        if( $row['hacerDump'] == "off" ){

            array_push( $tablasInserts, $row['nombre'] );
            $sql = " SELECT *
                       FROM {$row['nombre']}
                      WHERE {$row['fechaInsert']} >= '{$fechaInicioLinea}'";
            $rs2 = mysql_query( $sql, $conex );

            while( @$r = mysql_fetch_assoc( $rs2 ) ){

                $tablasModificadas[$row['nombre']] = 0;
                $registrosInsertar++;
                unset( $r['id'] );
                $campos2                                     = array_keys( $r );
                $arregloKeys                                 = array_keys( $r );
                $campos                                      = implode( ',', $arregloKeys );

                $insert  = " INSERT INTO {$row['nombre']} ( $campos ) VALUES ( ";
                $valores = "";
                    foreach( $r as $keycampo => $datoCampo ){
                        $valores .= ( $valores == "" ) ? " '".utf8_encode($datoCampo)."' " : ", '".utf8_encode($datoCampo)."'";
                    }
                    $insert .= $valores;
                $insert .= ");\n";
                array_push( $registros, $insert );

            }

        }else{
            $registrosDump += $row['registros'];
            array_push( $tablasXdump, $row['nombre'] );
        }
    }

    /*echo "<br> total registros Necesarios x dump: {$registrosDump}<br>";
    echo "<br> total registros reales x dump: {$registrosDumpReal}<br>";
    echo "<br> total registros x insert: {$registrosInsertar}<br>";
    echo "<br> tablasInserts: <br><pre>".print_r( $tablasInserts, true )."</pre>";
    echo "<br> tablasDumps: <br><pre>".print_r( $tablasXdump, true )."</pre>";*/
    imprimirDatos( $registros, $nombreDocumento);
    $tablasDump = implode(" ", $tablasXdump);
    $datos['tablasPendientesDump'] = $tablasDump;
    if( $tipoAlmacenamiento == "i" ){
        $datos['dumpEncabezados']      = "mysqldump -u root -p --no-data matrix ".$tablasDump." | sed -e 's/^) ENGINE=MyISAM/) ENGINE=InnoDB/'> dumpEncabezados{$fechaActual}.sql";
    }else{
        $datos['dumpEncabezados']      = "mysqldump -u root -p --no-data matrix ".$tablasDump." | sed -e 's/^) ENGINE=InnoDB/) ENGINE=MyISAM/'> dumpEncabezados{$fechaActual}.sql";
    }
    $datos['dumpDatos']            = "mysqldump -u root -p   --no-create-info  matrix ".$tablasDump." > dumpdatos{$fechaActual}.sql ";
    $datos['registrosDump']        = number_format($registrosDump,0,'.',',');
    $datos['horaFinalizacion']     = date('Y-m-d')." ".date('H:i:s');
    $datos['registrosInsertar']    = number_format($registrosInsertar,0,'.',',');
    //echo "<br> {$nombreDocumento} final: ".date('Y-m-d')." ".date('H:i:s')." ";
    echo json_encode( $datos );
    return;
}

function imprimirDatos($datos, $nombre_archivo){//funcion que agrega cada registro al archivo.
    $cont = 0;
    $regs = sizeof($datos);
    for($i = 0; $i<($regs); $i++)//empieza en -1 para que la primer vez que entre cree el archivo.
    {
      if($i==$regs-1)
        $contenido = $datos[$i];
      else
        $contenido = $datos[$i]."
";

      if($contenido != '')
      {
        $cont++;
        crear_archivo($nombre_archivo,$contenido,$cont); // lo crea en el mismo directorio.
      }
    }
}

function crear_archivo($filename,$content,$cont){//funcion que crea el archivo.
  if($cont==1){
    if (file_exists($filename)){
      unlink($filename);
    }
    $modo1 = 'w';
    $modo2 = 'a';
  }else{
    $modo1 = 'w+';
    $modo2 = 'a';
  }

  if (!file_exists($filename))
     $reffichero = fopen($filename, $modo1);

  // Let's make sure the file exists and is writable first.
  if (is_writable($filename)){

     // In our example we're opening $filename in append mode.
     // The file pointer is at the bottom of the file hence
     // that's where $content will go when we fwrite() it.
     if (!$handle = fopen($filename, $modo2)){
        //echo "Cannot open file ($filename)";
        exit;
     }

     // Write $content to our opened file.
     if (fwrite($handle, $content) === FALSE){
         //echo "Cannot write to file ($filename)";
         exit;
     }

     //echo "Success, wrote ($content) to file ($filename)";

     fclose($handle);

  }else{
     //echo "The file $filename is not writable";
  }
}
include_once("root/comun.php");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title></title>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
    <link rel="stylesheet" href="">
    <style type="text/css" media="screen">
        .tb_resultado{
            width: 40%;
        }
        .tb_resultado tr:last-child td:first-child{
            border-bottom-left-radius: 10px;
        }
        .tb_resultado tr:last-child td:last-child{
            border-bottom-right-radius: 10px;
        }
        .tb_resultado tr:first-child th:first-child{
            border-top-left-radius: 10px;
        }
        .tb_resultado tr:first-child th:last-child{
            border-top-right-radius: 10px;
        }
        .celdaResultado{
            padding: 5px;
            font-family: verdana;
            font-size: 8pt;
            font-weight: bold;
        }
        .celdaMenu{
            padding: 5px;
            font-family: sans-serif;
            font-size: 8pt;
            font-weight: bold;
        }
        .celdaResultadoEncabezado{
            padding: 10px;
        }
        textarea {
          width: 100%;
          height: 150px;
          padding: 12px 20px;
          box-sizing: border-box;
          border: 2px solid #ccc;
          border-radius: 4px;
          background-color: #f8f8f8;
          font-size: 16px;
          resize: none;
        }
    </style>
    <script src="../../../include/root/jquery.min.js"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#fechaInicioLinea").datepicker({
                    closeText: 'Cerrar',
                    prevText: 'Antes',
                    nextText: 'Despues',
                    monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                    'Jul','Ago','Sep','Oct','Nov','Dic'],
                    dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
                    dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
                    dayNamesMin: ['D','L','M','M','J','V','S'],
                    weekHeader: 'Sem.',
                    dateFormat: 'yy-mm-dd',
                    yearSuffix: '',
                    showOn: "button",
                    buttonImage: "../../images/medical/root/calendar.gif",
                    buttonImageOnly: false,
                    changeMonth: true,
                    changeYear: true,
                    maxDate:$("#fecha_actual").val()
                });
            $('[name="copy-button"]').tooltip();
            $('[name="copy-button"]').bind('click', function(){
                var input = $("#"+$(this).attr("input-linked"));
                input.attr("disabled", false);
                input.select();
                input.attr("disabled", true);
                //input.setSelectionRange(0, input.value.length + 1);
                try {
                    var success = document.execCommand('copy');
                    if(success){
                        $(this).trigger('copied', ['Copiado!']);
                    }else{
                        $(this).trigger('copied', ['Copiar con Ctrl-c']);
                    }
                }catch(err){
                    $(this).trigger('copied', ['Copiar con Ctrl-c']);
                }
            });
            $('[name="copy-button"]').bind('copied', function(event, message) {
              $(this).attr('title', message)
                  .tooltip('fixTitle')
                  .tooltip('show')
                  .attr('title', "Copiado a Clipboard")
                  .tooltip('fixTitle');
            });
        });

        function ejecutar(){
            $("#div_respuesta").hide();
            alerta("Espere un momento, El proceso puede tardar.")
            $.post('queryDatosHoy.php',
                   {
                    ejecutar:           "ejecutar",
                    fechaInicioLinea:   $("#fechaInicioLinea").val(),
                    horaInicioLinea:    $("#horaInicioLinea").val(),
                    tipoAlmacenamiento: $("input[name='tipoAlmacenamiento']:checked").val()
                   },
                function(data) {
                    link = '<br><a href="'+data.nombreArchivo+'" target="_blank">ARCHIVO</a><br>';
                    $("#td_link").html(link);

                    $("#td_registrosInsertar").html(data.registrosInsertar);
                    $("#td_inicio").html(data.horaInicio);
                    $("#td_termina").html(data.horaFinalizacion);
                    $("#td_registrosDump").html(data.registrosDump);
                    $("#txt_tablas_dump").val(data.tablasPendientesDump);
                    $("#inp_dump_encabezados").val(data.dumpEncabezados);
                    $("#inp_dump_datos").val(data.dumpDatos);
                    $("#div_respuesta").toggle();
                    $( '#msjAlerta2').dialog('destroy');
                },
                'json'
            );
        }

        function alerta( txt ){
            $("#textoAlerta2").text( txt  );
            $( '#msjAlerta2').dialog({
                width: "auto",
                height: 200,
                modal: true,
                dialogClass: 'noTitleStuff'
            });
            $(".ui-dialog-titlebar").hide();
        }
    </script>
</head>
<body>
<?php
    encabezado("<div class='titulopagina2'> DUMP PARCIAL A PARTIR DE UNA FECHA </div>", $wactualiza, "clinica");
?>
<br>
<input type="hidden" name="fecha_actual" id="fecha_actual" value="<?=$fechaActual?>">
<center>
    <table table class=" tb_resultado ">
        <thead>
            <tr>
                <th colspan="2" class='encabezadoTabla' align="center"> CRITERIO DE GENERACIÓN </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class='fila1 celdaMenu'> Fecha linea </td>
                <td class='fila2 celdaMenu'> <input type="text" name="fechaInicioLinea" id="fechaInicioLinea" value="<?=$fechaActual?>" placeholder="YYYY/MM/DD"> </td>
            </tr>
            <tr>
                <td class='fila1 celdaMenu'> Hora linea </td>
                <td class='fila2 celdaMenu'> <input type="text" name="horaInicioLinea" id="horaInicioLinea"  value="<?=$horaActual?>" placeholder="HH:mm:ss"> </td>
            </tr>
            <tr>
                <td class='fila1 celdaMenu'> Mécanismo Almacenamiento </td>
                <td class='fila2 celdaMenu'>
                    <input type="radio" name="tipoAlmacenamiento" value="m" placeholder=""> MyISAM
                    <input type="radio" name="tipoAlmacenamiento" value="i" checked placeholder=""> InnoDB
                </td>
            </tr>
        </tbody>
    </table><br>
    <div id="div_button">
        <input type="button" name="btn_ejecutar" value="Ejecutar" onclick="ejecutar()">
    </div><br>
    <div id="div_respuesta" align='center' style="display:none;">
        <table class=" tb_resultado ">
            <thead>
                <tr class="encabezadoTabla">
                    <td colspan="2" class="celdaResultadoEncabezado" align="center"> RESULTADOS </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fila1 celdaResultado">Hora Inicio</td><td id="td_inicio" class=" fila2 celdaResultado"></td>
                </tr>
                <tr>
                    <td class="fila1 celdaResultado">Hora Terminacion</td><td id="td_termina" class=" fila2 celdaResultado"></td>
                </tr>
                <tr>
                    <td class="fila1 celdaResultado">Requieren Dump</td><td class=" fila2 celdaResultado"><textarea name="txt_tablas_dump" id="txt_tablas_dump"></textarea></td>
                </tr>
                <tr>
                    <td class="fila1 celdaResultado">Total registros Dump</td><td class=" fila2 celdaResultado" name="td_registrosDump" id="td_registrosDump"></td>
                </tr>
                <tr>
                    <td class="fila1 celdaResultado">Dump datos</td><td class=" fila2 celdaResultado">
                        <div class="input-group">
                            <input type="text" class="form-control" id="inp_dump_encabezados" disabled="disabled" value="" placeholder="Comando dump encabezados" >
                           <span class="input-group-btn">
                             <button class="btn btn-default" type="button" name="copy-button" input-linked="inp_dump_encabezados" data-toggle="tooltip" data-placement="button" title="copiar">Copiar
                             </button>
                           </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="fila1 celdaResultado">Dump datos</td><td class=" fila2 celdaResultado">
                        <div class="input-group">
                            <input type="text" class="form-control" id="inp_dump_datos" disabled="disabled" value="" placeholder="Comando dump datos">
                           <span class="input-group-btn">
                             <button class="btn btn-default" type="button" name="copy-button" input-linked="inp_dump_datos" data-toggle="tooltip" data-placement="button" title="copiar">Copiar
                             </button>
                           </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="fila1 celdaResultado">Registros a Insertar</td><td id="td_registrosInsertar" class=" fila2 celdaResultado"></td>
                </tr>
                <tr>
                    <td class="fila1 celdaResultado">Link Inserts</td><td id="td_link" class=" fila2 celdaResultado"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div id='msjAlerta2' style='display:none;'>
            <br>
            <img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >
            <br><br><center><div id='textoAlerta2' style='font-size: 12pt;'></div></center><br>
    </div>
</center>
<br>
</body>
</html>