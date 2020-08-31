<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
	/*****************************************************
	 *		APLICACION PARA CARGA DE DATOS HEMODINAMICOS *
	 *          	PROVENIENTES DE TEXTO PLANO			 *
	 *				     CONEX, FREE => OK				 *
	 *****************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='acarga.php' enctype='multipart/form-data' method=post>";
		if(!isset($files))
		{
			echo "<input type='HIDDEN' name= 'medico' value='".$medico."'>";
			echo "<input type='HIDDEN' name= 'fechad' value='".$fechad."'>";
			echo "<input type='HIDDEN' name= 'paciente' value='".$paciente."'>";
			echo "<input type='HIDDEN' name= 'horai' value='".$horai."'>";
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE ANESTESIA</td></tr>";
			echo "<tr><td align=center colspan=2>CARGA DE ARCHIVOS DE DATOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
			echo "<td bgcolor=#cccccc><input type='file' name='files' size=60 maxlength=60 /></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'></td></tr></table>";
		}
		else
		{
			$files1=$HTTP_POST_FILES['files']['name'];
			$files=$HTTP_POST_FILES['files']['tmp_name'];
			#echo $files1."<br>";
			$numrec=0;
			$errrec=0;
			$id=0;
			#$files=stripslashes($files);
			#echo $files."<br>";
			if($files1 == "DATOS.TXT")
			{
				$file = fopen($files,"r");
				while (!feof($file) and file_exists ( $files))
				{
					$size = filesize($files)+1;
					$data=fgetcsv($file,$size,",");
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$seguridad="C-salam";
					$query = "insert  into salam_000002";
					$query = $query." (medico,fecha_data,hora_data,anestesiologo,fecha,hora_inicio,paciente,hora,parametro,codigo,valor,seguridad)";
					$query = $query." values('".$key."','".$fecha."','".$hora."','".$medico."','".$fechad."','".$horai."','".$paciente."','".$data[0]."','".$data[1]."','".$data[2]."','".$data[3]."','".$seguridad."')";
					#echo $query;
					if ($data[0] != "")
					{
						$err1 = mysql_query($query,$conex);
					 	if ($err1 != 1)
							 $errrec++;
						else
							$numrec=$numrec+1;
					}
				}	
				echo "NUMERO DE REGISTROS TOTALES : ".$numrec."<br>";
				$numrec=$numrec-$errrec;
				echo "NUMERO DE REGISTROS ERRONEOS O EXISTENTES : ".$errrec."<br>";
				echo "NUMERO DE REGISTROS ADICIONADOS : ".$numrec."<br>";
		}
		else
			echo " ***ERROR***    DEBE ESCOGER EL ARCHIVO ---> C:\DIGITAL\RESOK.TXT !!!";
	}
	include_once("free.php");
}
?>
</body>
</html>