<?php
include_once("conex.php");
    
	//generate the headers to help a browser choose the correct application
    header( "Content-type: application/msword" );
    header( "Content-Disposition: inline, filename=cert.rtf");
    

	

	$query="select * from hemo_000012 where Paciente='".$paciente."' and Fecha='".$fecha."' ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num>0)
	{
		$row = mysql_fetch_array($err);
		$cardiologo=$row[5];
		$clopidrogel=$row[6];
		$aspirina=$row[7];
		//$horaasp=$row[7];
		$metoprolol=$row[8];
		$horamet=$row[9];
		$ieca=$row[10];
		$enalapril=$row[11];
		$horaena=$row[12];
		$igp=$row[13];
		$tirofiban=$row[14];
		$clexane=$row[15];
		$horacle=$row[16];
	}   // fin $num>0
    $ini1=strpos($paciente,"-");
    $documento=substr($paciente,$ini1+1);
    $paciente=substr($paciente,0,$ini1);
    $ini1=strpos($documento,"-");
    $historia=substr($documento,$ini1+1);
    $documento=substr($documento,0,$ini1);
    $ini1=strpos($cardiologo,"-");
    $registro=substr($cardiologo,0,$ini1);     
    $cardiologo=substr($cardiologo,$ini1+1);
    $filename= "000012_hd03.rtf";
    $fp = fopen ( $filename, "r" );
    //read our template into a variable
    $output = fread( $fp, filesize( $filename ) );
  
    fclose ( $fp );
  
    // replace the place holders in the template with our data
    $ini1=strpos($ieca,"-");
    $output = str_replace( "<<ieca>>",substr($ieca,$ini1+1), $output );
    $ini1=strpos($igp,"-");
    $output = str_replace( "<<igp>>",substr($igp,$ini1+1), $output );
    $output = str_replace( "<<horacle>>",$horacle, $output );
    $output = str_replace( "<<horamor>>",$horamor, $output );
    $output = str_replace( "<<traslado>>",$traslado, $output );
    $output = str_replace( "<<dieta>>",$dieta, $output );
    $output = str_replace( "<<miembro>>", $miembro, $output );
    $output = str_replace( "<<o2>>", $o2, $output );
    $output = str_replace( "<<liquidos>>", $liquidos, $output );
    $output = str_replace( "<<valorliq>>", $valorliq, $output );
    $output = str_replace( "<<infusionliq>>", $infusionliq, $output );
    $output = str_replace( "<<nitro>>", $nitro, $output );
     $output = str_replace( "<<clopidrogel>>", $clopidrogel, $output );
    $output = str_replace( "<<aspirina>>", $aspirina, $output );
    $output = str_replace( "<<morfina>>", $morfina, $output );    
    $output = str_replace( "<<ranitidina>>", $ranitidina, $output );
    $output = str_replace( "<<horaran>>", $horaran, $output );
    $output = str_replace( "<<metoprolol>>", $metoprolol, $output );
    $output = str_replace( "<<horamet>>", $horamet, $output );
    $output = str_replace( "<<enalapril>>", $enalapril, $output );
    $output = str_replace( "<<horaena>>", $horaena, $output);
    $output = str_replace( "<<cpk>>", $cpk, $output );
    $output = str_replace( "<<introductor>>", $introductor, $output );
    $output = str_replace( "<<tpt>>", $tpt, $output );
    $output = str_replace( "<<tirofiban>>", $tirofiban, $output );
    $output = str_replace( "<<clexane>>", $clexane, $output );
    $output = str_replace( "<<fecha>>", $fecha, $output );
    $output = str_replace( "<<paciente>>", $paciente, $output );
    $output = str_replace( "<<documento>>", $documento, $output );
    $output = str_replace( "<<historia>>", $historia, $output );
    $output = str_replace( "<<cardiologo>>", $cardiologo, $output );
     $output = str_replace( "<<registro>>", $registro, $output );
    // send the generated document to the browser
    echo $output;

?>
