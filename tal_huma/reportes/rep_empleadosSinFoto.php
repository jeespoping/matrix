<?php
include_once("conex.php");
if(!isset($accion))
{
    echo '<!DOCTYPE html>';
}

// $path="../../images/medical/tal_huma/";
// $directorio=dir($path);
/**
 PROGRAMA                   : rep_empleadosSinFoto.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 14 Febreo de 2014

 DESCRIPCION:
 Este reporte de encarga de encontrar a los empleados que aún les falta foto para el perfil de talento humano.

 ACTUALIZACIONES:
 *  Marzo 15 de 2016
    Jessica Madrid	    : Se realizan modificaciones para reemplezar archivos de fotos ya existentes eliminando la foto anterior.
	
  *  Julio 15 de 2014
    Edwar Jaramillo     : Se realizan modificaciones para cargar fotos de empleados o reemplezar archivos de fotos ya existentes
                            Esta nueva funcionalidad permite agregar varios archivos de fotos al mismo tiempo.
  *  Febrero 14 de 2014
    Edwar Jaramillo     : Fecha de la creación del reporte.

**/

/**
    Este condicional es ejecutado para exportar el resultado del reporte a excel, se hace por medio de jquery
*/
if(isset($accion) && isset($form))
{
    if(isset($accion) && $accion == 'exportar_excel') // se debe diferenciar por los dos o por otro diferente a $accion puesto que desde talento.php ya esta seteado $accion
    {
        header("Content-type: application/vnd.ms-excel; name='excel'");
        header("Content-Disposition: filename=empleados_sin_foto_".date("Ymd").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $_POST['datos_a_enviar'];
        return;
    }
}

$path="../../images/medical/tal_huma/";
$directorioRuta=dir($path);

$wactualiz = "(Marzo 15 de 2016)";




include_once("../procesos/funciones_talhuma.php");

if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}

$userCargo = explode('-',$_SESSION['user']);
$wuse      = $userCargo[1];

$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

$directorio = opendir("../../images/medical/tal_huma"); //ruta actual
$arr_fotos = array();
while ($archivo = readdir($directorio)) //obtenemos un archivo y luego otro sucesivamente
{
    if (is_dir($archivo))//verificamos si es o no un directorio
    {
        //echo "[".$archivo . "]<br />"; //de ser un directorio lo envolvemos entre corchetes
    }
    else
    {
        //echo $archivo . "<br />";
        $archivo_foto = strtolower($archivo);
        $archivo_foto_exp = explode(".", $archivo_foto);
        $archivo_foto = $archivo_foto_exp[0];
        if(!array_key_exists($archivo_foto, $arr_fotos))
        {
            $arr_fotos[$archivo_foto] = $archivo;
        }
    }
}

function empleadosSinFoto($conex, $wbasedato, $arr_fotos)
{
    $arr_empleados_sin_foto = array();
    $cont_sin_cedula = 1;
    if(count($arr_fotos) > 0)
    {
        $sql = "SELECT  Ideced, Ideuse, Ideno1, Ideno2, Ideap1, Ideap2
                FROM    {$wbasedato}_000013
                WHERE   Ideest = 'on'
                ORDER BY Ideap1, Ideap2, Ideno1, Ideno2";
        $result = mysql_query($sql, $conex);
        while ($row = mysql_fetch_array($result))
        {
            $num_cedula = $row['Ideced'];
            $num_cedula = preg_replace('/\s+/', '', $num_cedula);
            $nombres_empleado = $row['Ideap1'].' '.$row['Ideap2'].' '.$row['Ideno1'].' '.$row['Ideno2'];
            $nombres_empleado = trim(preg_replace('/\s+/', ' ', $nombres_empleado));
            if(empty($num_cedula))
            {
                $num_cedula = "NO_REGISTRA_".$cont_sin_cedula;
                $cont_sin_cedula++;
            }

            if(empty($nombres_empleado))
            {
                $nombres_empleado = "NO_REGISTRA_NOMBRE";
            }

            if(!array_key_exists($num_cedula, $arr_fotos))
            {
                $nombre = $nombres_empleado;
                $arr_empleados_sin_foto[$num_cedula] = array("cedula"=>$num_cedula, "nombres"=>$nombre, "codigo"=>"[".$row['Ideuse']."]");
            }
        }
    }
    return $arr_empleados_sin_foto;
}

function htmlLista($arr_empleados_sin_foto)
{
    $html = '<table align="center" id="Exportar_a_Excel">
                <tr class="encabezadoTabla">
                    <td>&nbsp;</td>
                    <td>Cédula</td>
                    <td>Nombres</td>
                    <td>Código</td>
                </tr>';

    if(count($arr_empleados_sin_foto) == 0)
    {
        $html .= '  <tr class="fila2">
                        <td colspan="3" >NO SE ENCONTRARON REGISTROS</td>
                    </tr>';
    }
    else
    {
        $cont = 1;
        foreach ($arr_empleados_sin_foto as $cedula => $datos)
        {
            $css = ($cont % 2 == 0) ? "fila1": "fila2";
            $html .= '  <tr class="'.$css.'" >
                        <td>'.$cont.'</td>
                        <td>'.$datos['cedula'].'</td>
                        <td>'.utf8_encode($datos['nombres']).'</td>
                        <td>'.$datos['codigo'].'</td>
                    </tr>';
            $cont++;
        }
    }
    $html .= '</table>';
    return $html;
}

if(isset($accion) && isset($form))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'');
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';
    switch ($accion) {
        case 'load':
                switch ($form) {
                    case 'recargar_lista':
                            $arr_empleados_sin_foto = empleadosSinFoto($conex, $wbasedato, $arr_fotos);
                            $data['html'] = htmlLista($arr_empleados_sin_foto);
							
							$arr_fotos_existentes = array();
							$arr_ced_fotos_existentes = array();
						
							while ($archivo = $directorioRuta->read())
							{
								$array_cedula = explode(".",$archivo);	
							   
								if(!array_key_exists($array_cedula[0], $arr_fotos_existentes))
								{
									$arr_ced_fotos_existentes[$array_cedula[0]] = $array_cedula[0];
									$arr_fotos_existentes[$array_cedula[0]] = $archivo;
								}	

							}
							$data["cedFotosExistentes"]=$arr_fotos_existentes;
							$data["fotosExistentes"]=$arr_ced_fotos_existentes;
							
                        break;

                    case 'validar_cedula':
                            // print_r($cedula);
                            $data['existe'] = 0;
                            $expl           = explode(".", $cedulas);
                            $cedula         = $expl[0];
                            $sql = "SELECT  Ideced, Ideuse, Ideno1, Ideno2, Ideap1, Ideap2
                                    FROM    {$wbasedato}_000013
                                    WHERE   Ideced = '{$cedula}'
                                    ORDER BY Ideap1, Ideap2, Ideno1, Ideno2";
                            $result = mysql_query($sql, $conex);
                            if(mysql_num_rows($result) > 0)
                            {
                                $data['existe'] = 1;
                            }
                            // $arr_empleados_sin_foto = empleadosSinFoto($conex, $wbasedato, $arr_fotos);
                            // $data['html'] = htmlLista($arr_empleados_sin_foto);
                        break;
							
					case 'eliminar':
					
							foreach( $extensiones as $key => $valueExt )
							{
								$cedBuscar = $cedula.".".$valueExt;
								if (file_exists("../../images/medical/tal_huma/".$cedBuscar."") && $cedBuscar != $foto_subida) {
									// echo "El fichero ".$cedBuscar." existe";
									unlink("../../images/medical/tal_huma/".$cedBuscar."");
								}
							}
							
                        break;	
					default:
                            $data['error'] = 1;
                            $data['mensaje'] = $no_exec_sub;
                        break;
                }
                echo json_encode($data);
            break;

        default:
                $data['error'] = 1;
                $data['mensaje'] = $no_exec_sub;
            break;
    }
    return;
}

$arr_empleados_sin_foto = empleadosSinFoto($conex, $wbasedato, $arr_fotos);
$html_tabla = htmlLista($arr_empleados_sin_foto);

$arr_cedulas_validas = array();
$sql = "SELECT  Ideced
        FROM    {$wbasedato}_000013
        WHERE   Ideest = 'on'";
$result = mysql_query($sql, $conex);
while ($row = mysql_fetch_array($result))
{
    if(!array_key_exists($row['Ideced'], $arr_cedulas_validas))
    {
        $arr_cedulas_validas[$row['Ideced']] = $row['Ideced'];
    }
}

// echo count($arr_empleados_sin_foto);
// echo "<pre>".print_r($arr_empleados_sin_foto,true)."</pre>";




$arr_fotos_existentes = array();
$arr_ced_fotos_existentes = array();
while ($archivo = $directorioRuta->read()){

	$array_cedula = explode(".",$archivo);	
   // echo $archivo."<br>";
   
	if(!array_key_exists($array_cedula[0], $arr_fotos_existentes))
    {
        $arr_ced_fotos_existentes[$array_cedula[0]] = $array_cedula[0];
        $arr_fotos_existentes[$array_cedula[0]] = $archivo;
    }	
}
?>
<html lang="es-ES">
<head>
    <title>Empleados sin foto</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
</head>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/upload.css" rel="stylesheet" />
    <script type="text/javascript" src="../../../include/root/ajaxupload-min.js"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>

    <script type="text/javascript">

        var grupo = $('#grupo').val();
        var carpeta = $('#carpeta').val();
        var usuario = $('#usuario').val();
        var extensiones = $('#extensiones').val();

        var array_extensiones = extensiones.split(',');
        var limite_max_archivo = $("#limite_max_archivo").val();

        if(grupo != ""){
        var ruta = '../../'+grupo+'/publicaciones/';
        }else{
            var array_carpeta = carpeta.split(":");
            var ruta = array_carpeta[0];
        }

        // console.log(array_extensiones);

        // http://www.albanx.com/ajaxuploader/doc.php?e=2#methods
        // http://www.albanx.com/ajaxuploader/examples.php?e=2
        // DOCUMENTACION OPCIONES DE "ajaxupload"
        $('#uploader_div').ajaxupload({
            url:'../../root/procesos/upload.php?grupo=tal_huma&wusuario='+usuario,
            remotePath: ruta,
            maxFileSize: '10M',
            allowExt: array_extensiones,
            checkFileExists:true,
            editFilename:true,
            async: true,
            removeOnSuccess: true,
            finish:function(fn){ recargarListado(); },
            beforeUpload: function(files)
            {
                var datos  = eval('(' + $('#arr_cedulas_validas').val() + ')');
                var spt = files.split(".");
                if( spt[0] in datos )
                {
                    return true;
                }
                // alert("La cedula ["+files+"] no corresponde a un empleado");
                return false;

            },
            beforeUploadAll: function(files)
            {
                var esok = true;
                var datos  = eval('(' + $('#arr_cedulas_validas').val() + ')');
                // console.log('Added file: ' + file.name);
                $.each(files, function (index, file)
                {
                    var spt = file.name.split(".");
                    if( spt[0] in datos ) {
                    }
                    else{
                        // if(cont <= 1) { alert("La cedula ["+file.name+"] no corresponde a un empleado"); }
                        return false;
                    }
                });
                return true;
            },
            success: function(files)
            {
				fotosExistentes  = eval('(' + $('#arr_fotos_existentes').val() + ')');				
				cedFotosExistentes  = eval('(' + $('#arr_ced_fotos_existentes').val() + ')');		
				
				var cedula = files.split(".");
			
				// if(cedula[0] in cedFotosExistentes && fotosExistentes[cedula[0]] != files )				
				if(cedula[0] in cedFotosExistentes )				
				{
					
					$.post("../reportes/rep_empleadosSinFoto.php",
					{
						consultaAjax 			: '',
						accion       			: 'load',
						form         			: 'eliminar',
						wemp_pmla    			: $("#wemp_pmla").val(),
						wtema        			: $("#wtema").val(),
						// foto_borrar			    : fotosExistentes[cedula[0]]
						extensiones			    : array_extensiones,
						cedula				    : cedula[0],
						foto_subida			    : files
						
						
						
					}, function(respuesta){
						
						
					},"json").done(function(){
											
					});
				}
				
            }
        });


        $(document).ready(function() {
            /**
                Inicializa la funcionalidad para generar la exportación a excel.
            */
            $(".botonExcel").click(function(event) {
                $("#datos_a_enviar").val( $("<div>").append( $("#Exportar_a_Excel").eq(0).clone()).html());
                $("#FormularioExportacion").submit();
            });

            var extensiones_html = $("#extensiones_html").val();
            $('.ax-main-title').html('Seleccionar fotos '+extensiones_html);
        });

        // No se logró hacer funcionar esta rutina de forma sincrona, por el momento se descarta su uso.
        function validarCedula(cedulas)
        {
            $.post("../reportes/rep_empleadosSinFoto.php",
                {
                    consultaAjax : '',
                    accion       : 'load',
                    form         : 'validar_cedula',
                    wemp_pmla    : $("#wemp_pmla").val(),
                    wtema        : $("#wtema").val(),
                    cedulas      : cedulas
                },
                function(data){
                    if(data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        if(data.existe == 1) { return true; }
                        else { alert("Una cedula no existe en la base de datos de empleados."); return false; }
                    }
                    return data;
                },
                "json"
            ).done(function(data){
            });
        }


        function recargarListado()
        {
            $.post("../reportes/rep_empleadosSinFoto.php",
                {
                    consultaAjax : '',
                    accion       : 'load',
                    form         : 'recargar_lista',
                    wemp_pmla    : $("#wemp_pmla").val(),
                    wtema        : $("#wtema").val()
                },
                function(data){
                    if(data.error == 1)
                    {
                        alert(data.mensaje);
                    }
                    else
                    {
                        $("#seccion_reporte").html(data.html);
						$("#cedFotosExistentes").val(data.cedFotosExistentes);
						$("#fotosExistentes").val(data.fotosExistentes);
                    }
                },
                "json"
            ).done(function(){
                //
            });
			
		}

    </script>
<body>
<div id="actualiza" class="version" style="text-align:right;" >Subversi&oacute;n: <?=$wactualiz;?></div>

    <input type='hidden' id='grupo' value=''>
    <input type='hidden' id='usuario' value='<?=$wuse?>'>
    <input type='hidden' id='extensiones' value="jpg,png,gif">
    <input type='hidden' id='extensiones_html' value="jpg,png,gif,jpeg">
    <input type='hidden' id='limite_max_archivo' value="10">
    <input type='hidden' id='carpeta' value="../../images/medical/tal_huma/">
    <input type='hidden' id='wemp_pmla' value="<?=$wemp_pmla?>">
    <input type='hidden' id='wtema' value="<?=$wtema?>">
	<input type='hidden' id='arr_fotos_existentes' value='<?=json_encode($arr_fotos_existentes)?>'>
	<input type='hidden' id='arr_ced_fotos_existentes' value='<?=json_encode($arr_ced_fotos_existentes)?>'>
    <input type='hidden' id='arr_cedulas_validas' value='<?=json_encode($arr_cedulas_validas)?>'
    }>
	


    <div id="uploader_div" align="center"></div><div align="center" ><img width="15" height="15" border="0" onclick="recargarListado();" title="Actualizar lista de publicaciones" style="cursor:pointer" src="../../images/medical/sgc/Refresh-128.png"></div>

    <div align="left" style="text-align:left;">
        <span style="color:#999999;font-size:14pt;">Resultado de la consulta:</span>
        <div id="div_exportar" style="display:;text-align:right;">
            <form action="../reportes/rep_empleadosSinFoto.php?form=&accion=exportar_excel" method="post" target="_blank" id="FormularioExportacion">
                <span style="color:#999999;">Exportar</span>  <img width="28" height="14" border="0" src="../../images/medical/root/export_to_excel.gif" class="botonExcel" style="cursor:pointer;" />
                <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
            </form>
        </div>
    </div>

    <div id="seccion_reporte" style="">
        <?=$html_tabla?>
    </div>
</body>
</html>