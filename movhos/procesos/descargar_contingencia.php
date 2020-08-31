<?php
include_once("conex.php");
/* ***********************************************************************************
   * PROGRAMA PARA GENERAR EL KARDEX DE CONTINGENCIA
   ***********************************************************************************/
if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");
//==================================================================================================================================
//PROGRAMA                   : descargar_contingencia.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : Noviembre 27 de 2013
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Diciembre 05 de 2013";
//DESCRIPCION
//====================================================================================================================================\\
//Este programa permite la descarga del kardex de contingencia desde el programa Contingencia_Kardex_de_Enfermeria.php																						  \\
//====================================================================================================================================\\

//Nombre final del archivo
$nombre_archivo = $wcco."_".$wfecha."_".$whora;

$dir = "contingencia_kardex";

$f = "contingencia_kardex/".$nombre_archivo.".html";
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$nombre_archivo.".html");
$fp=fopen("$f", "r");
fpassthru($fp);

if (is_dir($dir)) {
   if ($gd = opendir($dir)) {
       while ($archivo = readdir($gd)) {
           
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

?>