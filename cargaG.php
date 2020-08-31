<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Carga de Archivos Planos Grandes</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> cargaG.php Ver. 2012-09-27</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		if(!isset($key))
			$key = substr($user,2,strlen($user));
		

		

		echo "<form action='cargaG.php' enctype='multipart/form-data' method=post>";
		echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
		if(!isset($files) or !isset($Form) or !isset($Sep))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CARGA DE ARCHIVOS PLANOS GRANDES</td></tr>";
			echo "<tr><td align=center colspan=2><u>Recuerde que este archivo ya debe contener los campos de:</u></td></tr>";
			echo "<tr><td align=center colspan=2><u>(medico, Fecha_data, Hora_data y seguridad)</u></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre de la Tabla</td>";
			$query = "SHOW TABLES ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<td bgcolor='#cccccc'><select name='Form'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($key == "root" or ($key != "root" and $key == substr($row[0],0,strpos($row[0],"_"))))
						echo "<option>".$row[0]."</option>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Separador de Campos</td>";
			echo "<td bgcolor=#cccccc><input type='TEXT' name='Sep' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
			echo "<td bgcolor=#cccccc><input type='file' name='files'  size=60 maxlength=60 /></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'></td></tr></table>";
		}
		else
		{
			$files=$_FILES['files']['tmp_name'];
			
			chmod($files, 0644);
			$count=0;
			$query = "select count(*) from ".$Form." ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$inicial = $row[0];
			// $query = "LOAD DATA INFILE '".$files."'  INTO TABLE ".$Form." FIELDS TERMINATED BY '".$Sep."' ";
			$query = "LOAD DATA LOCAL INFILE '".$files."'  INTO TABLE ".$Form." FIELDS TERMINATED BY '".$Sep."' ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$query = "select count(*) from ".$Form." ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$final = $row[0];
			$total = $final - $inicial;
			echo "Numero de Registros Iniciales :".$inicial."<br>";
			echo "Numero de Finales :".$final."<br>";
			echo "Carga Exitosa Numero de Registros Almacenados :".$total;
		}
}
?>
</body>
</html>
