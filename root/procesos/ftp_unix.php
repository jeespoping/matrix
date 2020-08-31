<html>
<head>
  <title>TRANSFERENCIAS DE ARCHIVOS A UNIX</title>
</head>
<body BGCOLOR="">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Ordenes de Laboratorio</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> ftp_unix.php Ver. 2006-03-30</b></font></tr></td></table>
</center>

<BODY TEXT="#000066">
<?php
include_once("conex.php");
	session_start();
	if(!isset($_SESSION['user']))
		echo"error";
	else
	{
		$key = substr($user,2,strlen($user));
		

		mysql_select_db("MATRIX");
		echo "<form action='ftp_unix.php' enctype='multipart/form-data' method=post>";
		if(!isset($files))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>TRANSFERENCIAS DE ARCHIVOS A UNIX</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
			echo "<td bgcolor=#cccccc><input type='file' name='files'  size=60 maxlength=60 /></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'></td></tr></table>";
		}
		else
		{
			$real= $HTTP_POST_FILES['files']['name'];
			$files=$HTTP_POST_FILES['files']['tmp_name'];
			$FTP_HOST ="132.1.18.2"; 
			$FTP_USER ="conlab97";
			$FTP_PW   ="lab3000";
			$FTP_ROOT_DIR="/";
			$LOCAL_SERVER_DIR  = "/u8/programas/contab";
			$FTP_DIR = "/u8/programas/contab";
			 $mode = FTP_BINARY; // or FTP_ASCII
			$conn_id = ftp_connect($FTP_HOST) or die("No se ralizo Conexion"); 
			if(ftp_login($conn_id, $FTP_USER, $FTP_PW))
			{
				ftp_pwd($conn_id);
				ftp_chdir($conn_id,$FTP_DIR); 
				$from = fopen($files,"r");
				if(ftp_fput($conn_id, $real, $from, $mode))
  					 echo "ARCHIVO TRANSMITIDO !!! <br>";
				ftp_quit($conn_id);
			}
		}
	}
	include_once("free.php");
?>
</body>
</html>