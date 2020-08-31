<?php
include_once("conex.php");
    
	//generate the headers to help a browser choose the correct application
    header( "Content-type: image/jpeg" );
    //header( "Content-Disposition: inline, filename=$imagen");
    header( "Content-Disposition: attachment, filename=$imagen");
    $filename= "c:/inetpub/wwwroot/matrix/images/medical/nuclear/".$imagen;
    $imagen=imageCreateFromJpeg($filename);
    imageJpeg($imagen);
?>
