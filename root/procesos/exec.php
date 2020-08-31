<?php
include_once("conex.php");
/*system('c:\windows\system32\cmd.exe /c start c:\windows\NOTEPAD.exe',$error);
//echo $sis;
echo $error;*/
 header( "Content-type: application/WORDPAD" );
 header( "Content-Disposition: inline, filename=cert.rtf");
 $filename = "PHPCertification.rtf";
 $fp = fopen ( $filename, "r" );

    //read our template into a variable
    $output = fread( $fp, filesize( $filename ) );
  
    fclose ( $fp );
    $output = str_replace( "<<NAME>>", "ana", $output );
    $output = str_replace( "<<Name>>", "ana", $output );
    $output = str_replace( "<<score>>", "100", $output );
    $output = str_replace( "<<mm/dd/yyyy>>", "01/01/2004", $output );
    echo $output;
?>