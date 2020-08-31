<?php
include_once("conex.php");
$FTP_HOST ="131.1.18.7"; 
$FTP_USER ="imacmicro";
$FTP_PW   ="root";
$FTP_ROOT_DIR="/";
$LOCAL_SERVER_DIR  = "/users/imacmicro/Documents";
$FTP_DIR = "/users/imacmicro/Documents";

$mode = FTP_BINARY; // or FTP_ASCII
$conn_id = ftp_connect($FTP_HOST); 
echo "conexion : ".$conn_id."<br>";
if(ftp_login($conn_id, $FTP_USER, $FTP_PW))
{
    echo "CONECTADO !<br>";
    ftp_pwd($conn_id);
    //ftp_mkdir($conn_id,$FTP_DIR);
    ftp_chdir($conn_id,$FTP_DIR);  
    $files="Cirugia.txt";
    $path="C:/Inetpub/wwwroot/MATRIX/planos/";
    $from = fopen($path.$files,"r");
    if(ftp_fput($conn_id, $files, $from, $mode))
    {
       echo "ARCHIVO TRANSFERIDO ! <br>";
    }
    ftp_quit($conn_id);
}
?>