<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Control de Archivos en Unix</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000018_file.php Ver. 1.00</b></font></tr></td></table>
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
			echo "<form action='000018_file.php' method=post >";
			$FTP_HOST ="132.1.18.2"; 
			$FTP_USER ="root";
			$FTP_PW   ="sco";
			$FTP_ROOT_DIR="/";
			$LOCAL_SERVER_DIR  = "/";
			$FTP_DIR = "/";
			$mode = FTP_BINARY; // or FTP_ASCII
			$conn_id = ftp_connect($FTP_HOST) or die("No se ralizo Conexion"); 
			if(ftp_login($conn_id, $FTP_USER, $FTP_PW))
			{
				chdir("../../planos");
				ftp_pwd($conn_id);
				ftp_chdir($conn_id,$FTP_DIR);  
				$files="/u2/u2.txt";
				$u2="u2.txt";
				 if(ftp_get($conn_id, $u2, $files, $mode))
					 echo "u2 TRANSFERIDO ! <br>";
				$files="/u3/u3.txt";
				$u3="u3.txt";
				 if(ftp_get($conn_id, $u3, $files, $mode))
					 echo "u3 TRANSFERIDO ! <br>";
				$files="/u4/u4.txt";
				$u4="u4.txt";
				 if(ftp_get($conn_id, $u4, $files, $mode))
					 echo "u4 TRANSFERIDO ! <br>";
				$files="/u5/u5.txt";
				$u5="u5.txt";
				 if(ftp_get($conn_id, $u5, $files, $mode))
					 echo "u5 TRANSFERIDO ! <br>";
				$files="/u8/u8.txt";
				$u8="u8.txt";
				 if(ftp_get($conn_id, $u8, $files, $mode))
					 echo "u8 TRANSFERIDO ! <br>";
				$files="/u9/u9.txt";
				$u9="u9.txt";
				 if(ftp_get($conn_id, $u9, $files, $mode))
					 echo "u9 TRANSFERIDO ! <br>";
				ftp_quit($conn_id);
			}
			include_once("free.php");
	}
?>
</body>
</html>