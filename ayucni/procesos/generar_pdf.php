<?php
include_once("conex.php");
/**
 PROGRAMA                   : generar_pdf.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 18 Marzo de 2016

 DESCRIPCION:
  Basado en example_006.php de TCPDF, librería creada por @author Nicola Asuni.

 ACTUALIZACIONES:
 * Septiembre 8 de 2020 
    David Henao se crea otro archivo shell (/generarPdfCardioLogo.sh) para asi cuando se genere el pdf en transcripcion.php se guarde en la ruta especificada con un logo y se movio la margen del archivo
 * 
 *  Marzo 18 de 2016
    Edwar Jaramillo     : Fecha de la creación del archivo de configuración.
**/

$arr_encabezado = unserialize(base64_decode($arr_encabezado));
$contenido_pdf = base64_decode($contenido_pdf);
// echo "<pre>".print_r($arr_encabezado,true)."</pre>";
$dir = 'resultados';

if(is_dir($dir)){ }
else { mkdir($dir,0777); }

$arr_archivos_r = array();
// Abrir un directorio conocido, y proceder a leer sus contenidos
/*
    Esta sección se encarga de leer todos los archivos generados para resultados y verifíca si la fecha de creación es menor
    a la fecha actual, si es así entonces elimina esos archivos, esto con el fin de liberar el disco duro de estos archivos no necesarios.
*/
if (is_dir($dir)) {
    if ($gd = opendir($dir)) {
        while ($archivo = readdir($gd)) {
            //echo "nombre de archivo: $archivo : tipo de archivo: " . filetype($dir . $archivo) . "\n";
            if($archivo != '.' && $archivo != '..'){
                $arr_archivos_r[] = $dir."/".$archivo;
            }
        }
        closedir($gd);
    }

    foreach ($arr_archivos_r as $key => $archivo) {
        $fecha_creado = date("Ymd", filectime($archivo));
        $fecha_modifi = date("Ymd", filemtime($archivo));
        $fecha_Actual = date("Ymd");
        if($fecha_creado < $fecha_Actual || $fecha_modifi < $fecha_Actual) { unlink($archivo); }
    }
}

// $datos_paciente = $wtipodoc.". ".$wcedula." | ".$wfuente." ".$worden;
$datos_paciente = "";
$wnombrePDF = "resultado_examen_".$wconsecutivo;

if(!isset($logo)){
    $wnombrePDF = "resultado_examen_".$wconsecutivo;
}else{
    $wnombrePDF = $wconsecutivo;
}

$whistoria = $arr_encabezado["whistoria"];
$wingreso  = $arr_encabezado["wingreso"];
$his_ing   = $whistoria.'-'.$wingreso;
$wtipodoc  = $arr_encabezado["wtipodoc"];
$wcedula   = $arr_encabezado["wdocumento"];
$wspedad   = $arr_encabezado["wedad"];

$nombres_apellidos = $arr_encabezado["wnombre1"].' '.$arr_encabezado["wnombre2"].' '.$arr_encabezado["wapellido1"].' '.$arr_encabezado["wapellido2"];
$wnombre_pac       = trim(preg_replace('/[ ]+/', ' ', $nombres_apellidos));

$archivo_dir = $dir."/".$wnombrePDF.".pdf";
if(file_exists($archivo_dir))
{
    unlink($archivo_dir);
}

$wtapa = 'off';
$wnompac = '';
$whis = '';
$wing = '';

$fechaEncabezado = date("Y-m-d");

// ************* ENCABEZADO *************
// ($agregarFecha and $fechaConsultada != "") ? $fechaEncabezado = "Fecha de Atenci&oacute;n: <br> {$fechaConsultada}" : $fechaEncabezado = "";
$html = "<!DOCTYPE html>
        <head>
            <script>
                function subst() {
                  var vars={};
                  var x=document.location.search.substring(1).split('&');
                  var inicio = 1;
                  if( document.getElementById('incluyeTapa').value == 'on' )
                      inicio = 2;
                  for(var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
                  var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
                  for(var i in x) {
                    var y = document.getElementsByClassName(x[i]);
                    for(var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];

                         if ( vars[x[2]] == 1 && inicio == 2 ){
                            document.getElementById('encabezado').style.display='none';
                         }

                         if (vars[x[2]] == inicio) {
                            /*var logo = document.getElementById('imagenClinica');
                            if( logo != undefined ){
                                logo.style.display='';
                            }*/
                            var td_nombre = document.getElementById('td_nombre');
                            if( td_nombre != undefined ){
                                td_nombre.style.display='none';
                            }
                         }else{
                            /*var logo = document.getElementById('imagenClinica');
                            if( logo != undefined )
                                logo.parentNode.removeChild(logo);*/
                         }
                  }
                }
            </script>
        </head>
        <body style='border:0; margin: 0;' onload='subst()'>
            <input type='hidden' id='incluyeTapa' value='off'>
            <table id='encabezado' style='width: 100% background-color: blue;'>
                <tr>
                    <td align='left' width='25%' id='imagenClinica' style='display:;'>
                        <!-- <img width='90' heigth='70' src='../../../images/medical/root/".$nombre_logo.".JPG' /> -->
                    </td>
                    <td style='text-align:right' width='10%'><font size='2'>
                        P&aacute;g <span class='page'></span> de <span class='topage'></span></font>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        <br>";

$archivo_dir = $dir."/encabezado_".$wnombrePDF.".html";

if(file_exists($archivo_dir)){
    unlink($archivo_dir);
}
$f           = fopen( $archivo_dir, "w+" );
fwrite( $f, $html );
fclose( $f );

// Como el html se crea en una subcarpeta, entonces se hace necesario retornar un nivel más en los directorios para poder ingresar por ejemplo a images
$contenido_pdf = str_replace("../../", "../../../", $contenido_pdf);

// ************* CUERPO *************
$html = '<!DOCTYPE html>
            <head></head>
            <body style="border:0; margin:2px;">
                '.$contenido_pdf.'
            </body>
        </html>
        <br>';

//CREAR UN ARCHIVO .HTML CON EL CONTENIDO CREADO
$archivo_dir = $dir."/cuerpo_".$wnombrePDF.".html";
if(file_exists($archivo_dir)){
    unlink($archivo_dir);
}
$f           = fopen( $archivo_dir, "w+" );
fwrite( $f, $html);
fclose( $f );

$transcriptor = ($usuario_trancribe != '') ? $usuario_trancribe: $usuario_modifica;

// ************* PIE DE PÁGINA *************
$html = '<!DOCTYPE html>
            <head></head>
            <body style="border:0; margin: 0;">
                <table id="encabezado" style="width: 100%; ">
                    <!-- <tr>
                        <td style="text-align:right"  width="100%"><font size="2">
                            Generaci&oacute;n: Fecha: ".date("Y-m-d")." - Hora: ".date("H:i:s")."</span></font>
                        </td>
                    </tr> -->
                    <tr>
                        <td style="text-align:left; font-size: 7pt; color: #969696;" width="15%">
                            Transcribe: '.$transcriptor.'
                        </td>
                        <td style="text-align:right; font-size: 7pt; color: #969696;" width="15%">
                            Paciente: '.$wnombre_pac.'
                        </td>
                    </tr>
                </table>
            </body>
        </html>
        <br>';

$archivo_dir = $dir."/pie_".$wnombrePDF.".html";

if(file_exists($archivo_dir)){
    unlink($archivo_dir);
}
$f = fopen( $archivo_dir, "w+" );
fwrite( $f, $html );
fclose( $f );

// generarPdfCardio.sh resultado_examen_7 ----- codigo para generar dos pdf con margen diferente
//$respuesta = shell_exec( "./generarPdfCardio.sh ".$wnombrePDF);
//$respuesta = shell_exec( "./generarPdfCardioLogo.sh ".$wnombrePDF);

if(!isset($logo)){
    $respuesta = shell_exec( "./generarPdfCardio.sh ".$wnombrePDF);

}else{
    $respuesta = shell_exec( "./generarPdfCardioLogo.sh \"".$wnombrePDF.'"');

}


// $dir = '/var/www/matrix/ayucni/procesos/resultados';
$archivo_dir = $dir."/encabezado_".$wnombrePDF.".html";
if(file_exists($archivo_dir))
{
    unlink($archivo_dir);
}

$archivo_dir = $dir."/cuerpo_".$wnombrePDF.".html";
if(file_exists($archivo_dir))
{
    unlink($archivo_dir);
}

$archivo_dir = $dir."/pie_".$wnombrePDF.".html";
if(file_exists($archivo_dir))
{
    unlink($archivo_dir);
}
