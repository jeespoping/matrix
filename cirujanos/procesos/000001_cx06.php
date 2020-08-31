<?php
include_once("conex.php");
    
	//generate the headers to help a browser choose the correct application
    header( "Content-type: application/msword" );
    header( "Content-Disposition: inline, filename=cert.rtf");

    $date = date( "m" );
    switch ($date)
    {
	  case "01":$date=date("d")." de Enero de ".date("Y");    
	  break;
	  case "02":$date=date("d")." de Febrero de ".date("Y");    
	  break;
	  case "03":$date=date("d")." de Marzo de ".date("Y");    
	  break;
	  case "04":$date=date("d")." de Abril de ".date("Y");    
	  break;
 	  case "05":$date=date("d")." de Mayo de ".date("Y");    
	  break;
	  case "06":$date=date("d")." de Junio de ".date("Y");    
	  break;
	  case "07":$date=date("d")." de Julio de ".date("Y");    
	  break;
	  case "08":$date=date("d")." de Agosto de ".date("Y");    
	  break;
	  case "09":$date=date("d")." de Septiembre de ".date("Y");    
	  break;
	  case "10":$date=date("d")." de Octubre de ".date("Y");    
	  break;
	  case "11":$date=date("d")." de Noviembre de ".date("Y");    
	  break;
  	  case "12":$date=date("d")." de Diciembre de ".date("Y");    
	  break;
    };
    $filename= "000001_cx08.rtf";
    
  
    // open our template file
   //$filename = "PHPCe";
    $fp = fopen ( $filename, "r" );
    //read our template into a variable
    $output = fread( $fp, filesize( $filename ) );
  
    fclose ( $fp );
    $ini1=strpos($medico,"-");
    $reg=substr($medico,0,$ini1);
    $medico=strtoupper(substr($medico,$ini1+1,1)).strtolower(substr($medico,$ini1+2)); 
    $pos=strpos($medico," ");
    if(is_int(strpos($medico," ")))
	{
		do
		{
			$medico=substr($medico,0,$pos+1).strtoupper(substr($medico,$pos+1,1)).substr($medico,$pos+2);
			$pos=strpos($medico," ",$pos+1);
		}while(is_int($pos));
	}
    
    $output = str_replace( "<<date>>", $date, $output );
    $output = str_replace( "<<paciente>>",$pac, $output );
    $output = str_replace( "<<remite>>",$remite, $output );
    $output = str_replace( "<<idpac>>",$idpac, $output );
    $output = str_replace( "<<esofago>>",$esofago, $output );
    $output = str_replace( "<<estomago>>",$estomago, $output );
    $output = str_replace( "<<duodeno>>",$duodeno, $output );
    $output = str_replace( "<<esofago1>>",$esofago1, $output );
    $output = str_replace( "<<estomago1>>",$estomago1, $output );
    $output = str_replace( "<<duodeno1>>",$duodeno1, $output );
    $output = str_replace( "<<diagnostico>>",$diagnostico, $output );
    $output = str_replace( "<<medico>>", $medico, $output );
    $output = str_replace( "<<reg>>", $reg, $output );
    //$output = str_replace( "<<examen>>", $examen, $output );
   
    // send the generated document to the browser
    echo $output;
?>
