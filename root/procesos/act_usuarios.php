<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de Usuarios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> act_usuarios.php Ver. 3.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	if(!isset($key))
		$key = substr($user,2,strlen($user));
	

	

	echo "<form action='act_usuarios.php' enctype='multipart/form-data' method=post>";
	echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	if(!isset($files))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION DE USUARIOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
		echo "<td bgcolor=#cccccc><input type='file' name='files'  size=60 maxlength=60 /></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'></td></tr></table>";
	}
	else
	{
		$files=$HTTP_POST_FILES['files']['tmp_name'];
		$numrec=0;
		$errrec=0;
		$file = fopen($files,"r");
		$k=0;
		while (!feof($file) and file_exists ( $files))
		{
			$size = filesize($files)+1;
			$data=fgetcsv($file,$size,",");
			$query = "select * from usuarios where codigo='".$data[0]."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num == 0)
			{
				$k++;
				echo $k." NO EXISTE EMPLEADO : ".$data[0]."  ".$data[1]."<BR>";
			}
			else
				echo " ACTUALIZADO  : ".$data[0]."  ".$data[1]."<BR>";
			$query = "update usuarios set descripcion='".$data[1]."' where codigo='".$data[0]."'";
			#echo $query;
			if ($data[0] != "")
			{
				$err1 = mysql_query($query,$conex);
			 	if ($err1 != 1)
			 	{
				 	$errrec++;
					echo mysql_errno().":".mysql_error()."<br>";
					echo $query."<br>";
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>YA EXISTE EL CODIGO DEL CAMPO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "</table><br><br>";
				}
				else
				{
					$numrec=$numrec+1;
					//echo "NUMERO DE REGISTROS INSERTADOS : ".$numrec."<br>";
				}
			}
		}	
		echo "<BR><B>NUMERO DE REGISTROS TOTALES : ".$numrec."</B><br>";
		$numrec=$numrec-$errrec;
		echo "<B>NUMERO DE REGISTROS ERRONEOS : ".$errrec."</B><br>";
		echo "<B>NUMERO DE REGISTROS ADICIONADOS : ".$numrec."</B><br>";
	}
}
?>
</body>
</html>