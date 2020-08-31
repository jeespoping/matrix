<?php
include_once("conex.php");
	//generate the headers to help a browser choose the correct application
    header( "Content-type: application/msword" );
    header( "Content-Disposition: inline, filename=cert.rtf");

    $date = date( "F d, Y" );
    $name= "Ana Maria Betancur Vargas";
    $filename= "000003_nu02.rtf";
    
  
    // open our template file
   //$filename = "PHPCe";
    $fp = fopen ( $filename, "r" );
    //read our template into a variable
    $output = fread( $fp, filesize( $filename ) );
  
    fclose ( $fp );
  
    // replace the place holders in the template with our data
    //$output = str_replace( "<<NAME>>", strtoupper( $name ), $output );
    $output = str_replace( "<<paciente>>",strtoupper($paciente), $output );
    $output = str_replace( "<<estudio>>",$estudio, $output );
    $output = str_replace( "<<plantilla>>",$plantilla, $output );
    $output = str_replace( "<<entidad>>", $entidad, $output );
     $output = str_replace( "<<ingreso>>", $ingreso, $output );
    $output = str_replace( "<<resultado>>", $opinion, $output );
    $output = str_replace( "<<medico>>", strtoupper( $medico), $output );
   
    // send the generated document to the browser
    echo $output;

?>
