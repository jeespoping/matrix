<?php
include_once("conex.php");
    
	//generate the headers to help a browser choose the correct application
    header( "Content-type: application/msword" );
    header( "Content-Disposition: inline, filename=cert.rtf");

    $date = date( "F d, Y" );
    $filename= "CONTRATO.rtf";
    
  
    // open our template file
   //$filename = "PHPCe";
    $fp = fopen ( $filename, "r" );
    //read our template into a variable
    $output = fread( $fp, filesize( $filename ) );
  
    fclose ( $fp );
  
    // replace the place holders in the template with our data
    
   
    $output = str_replace( "<<nombre_paciente>>",strtoupper($paci), $output );
    $output = str_replace( "<<aaaa-mm-dd>>",$fecha, $output );
   	$output = str_replace( "<<numero_contrato>>",$codigo, $output );
    $output = str_replace( "<<t_tratamiento>>", $trata, $output );
    $output = str_replace( "<<especilidad>>", $espe, $output );
    $output = str_replace( "<<numero_histoclinica>>", $nuhis, $output );
    $output = str_replace( "<<nombre_odontologo>>", strtoupper( $odonto), $output );
    $output = str_replace( "<<uso>>", $uso, $output );
    $output = str_replace( "<<mantenimiento>>", $mantenimiento, $output );
    $output = str_replace( "<<incluye>>", $incluye, $output );
    $output = str_replace( "<<no_incluye>>", $noincluye, $output );
    $output = str_replace( "<<valor_presupuesto>>", $vppto, $output );
    $output = str_replace( "<<tiempo_tratamiento>>", $ttrata, $output );
    $output = str_replace( "<<tiempo_antes>>", $antes, $output );
  
     /*$output = str_replace( "<<nombre_odontologo>>", strtoupper( $odonto), $output );
   
    // send the generated document to the browser
    */
    echo $output;

?>