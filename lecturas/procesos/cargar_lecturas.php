<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
	/********************************************************
	 *  APLICACION PARA CARGA DE TRANSCRIPCION DE LECTURAS  *
	 *                    DE IMAGINOLOGIA                   *
	 *          	  DESDE UN ARCHIVO PLANO                *
	 *				     CONEX, FREE => OK				    *
	 ********************************************************/

session_start();
if (!isset($user))
	{
		if(!isset($_SESSION['user']))
			session_register("user");
			//$user="1-".strtolower($codigo);
	}

if(!isset($_SESSION['user']))
	echo "error";
else
{
		//$key = substr($user,2,strlen($user));
		

		

		
		echo "<form action='cargar_lecturas.php' enctype='multipart/form-data' method=post>";
		if(!isset($files))
		{
			//echo "<input type='HIDDEN' name= 'medico' value='".$medico."'>";
			//echo "<input type='HIDDEN' name= 'fechad' value='".$fechad."'>";
			//echo "<input type='HIDDEN' name= 'paciente' value='".$paciente."'>";
			//echo "<input type='HIDDEN' name= 'horai' value='".$horai."'>";
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>CLINICA LAS AMERICAS<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION TRANSCRIPCION DE LECTURAS WEB</td></tr>";
			echo "<tr><td align=center colspan=2>CARGA DE ARCHIVO DE DATOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
			echo "<td bgcolor=#cccccc><input type='file' name='files' size=60 maxlength=60 /></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'></td></tr></table>";
		}
		else
		  {
			//echo "Pasoooooo";  
			  
			$files1=$HTTP_POST_FILES['files']['name'];
			$files=$HTTP_POST_FILES['files']['tmp_name'];
			//$files="c:/lecturas/".$files1;
			///$files="c:/lecturas/cargarweb.txt";
			///$files1="c:/lecturas/cargarweb.txt";
			
			$numrec=0;
			$errrec=0;
			$id=0;
			#$files=stripslashes($files);
			#echo $files."<br>";
			
			ECHO "File1 : ".$files1."<br>";
			echo "Files : ".$files."<br>";
		
			if($files1 == "cargarweb.txt")
			{
				$file = fopen($files,"r");
				while (!feof($file) and file_exists ( $files))
			      {
				   $size = filesize($files)+1;
				   $data=fgetcsv($file,$size,"|");
								   			   
				   //Borro lo que existia del estudio e inserto lo nuevo
				   $query = "        DELETE FROM lecturas_000001 ";
				   $query = $query."  WHERE lecfue = '".$data[4]."'";  //Fuente
				   $query = $query."    AND lecing = '".$data[5]."'";  //Nro de ingreso
				   $query = $query."    AND lecexa = '".$data[7]."'";  //Examen
				   $err1 = mysql_query($query,$conex);
				   
				   //echo $err1."<br>";
				   
				   //echo $query;
				   
				   //Inserto el nuevo registro, ya sea por modificacion o por que es nuevo
				   // echo $data[0]."'|'".$data[1]."'|'".$data[2]."'|'".$data[3]."'|'".$data[4]."'|'".$data[5]."'|'".$data[6]."'|'".$data[7]."'|'".$data[8]."'|'".$data[9]."'|'".$data[10]."'|'".$data[11]."'|'".$data[12]."'|'".$data[13]."'|'".$data[14]."'|'".$data[15]."')<br>";
				   $query = "insert into lecturas_000001";
				   $query = $query."           (Medico,       Fecha_data,    Hora_data,     lecdoc,        lecfue,        lecing,        lecpac,        lecexa,        lecnom,        lecmem,        lecfec,         lecusu,         lecmed,         lecrem,         Seguridad,      Id)";
				   $query = $query." values('".$data[0]."','".$data[1]."','".$data[2]."','".$data[3]."','".$data[4]."','".$data[5]."','".$data[6]."','".$data[7]."','".$data[8]."','".$data[9]."','".$data[10]."','".$data[11]."','".$data[12]."','".$data[13]."','".$data[14]."','".$data[15]."')";
					   
				   //echo $query."<br><br>";
					   
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
				echo "Termina de cargar la informacion";
		    }
		   else
		      echo " ***ERROR***    DEBE ESCOGER EL ARCHIVO ---> C:\LECTURAS\CARGARWEB.TXT !!!";
	      }
	      include_once("free.php");
}
?>
</body>
</html>