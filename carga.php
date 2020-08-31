<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Carga de Archivos Planos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> carga.php Ver. 2014-04-25</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function Stablas($usuario1, $usuario2, &$Tablas,$conex)
{
	$query = "SELECT tablas from usuarios ";
	$query = $query." where codigo = '".$usuario2."'";
	$err = mysql_query($query,$conex);
	$row = mysql_fetch_array($err);
	$tabla=$row[0];
	$Tablas=explode(chr(13).chr(10),$tabla);
}
function Sbuscar($criterio, &$Tablas)
{
	for ($j=0;$j<sizeof($Tablas);$j++)
		if($criterio == $Tablas[$j])
			return true;
	return false;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		if(!isset($key))
			$key = substr($user,2,strlen($user));
		

		

		echo "<form action='carga.php' enctype='multipart/form-data' method=post>";
		echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
		if(!isset($files) or !isset($Form))
		{
			if($key != $usera)
			{
				$Tablas=array();
				Stablas($key,$usera,$Tablas,$conex);
			}
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CARGA DE ARCHIVOS PLANOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nombre de la Tabla</td>";
			$query = "SELECT * from formulario ";
			$query = $query." where medico = '".$key."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<td bgcolor='#cccccc'><select name='Form'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($key == $usera or ($key != $usera and Sbuscar($row[0]."-".$row[1], $Tablas)))
						echo "<option>".$row[1]."-".$row[2]."</option>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Nombre del Archivo</td>";
			echo "<td bgcolor=#cccccc><input type='file' name='files'  size=60 maxlength=60 /></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='Send File'></td></tr></table>";
		}
		else
		{
			$files=$_FILES['files']['tmp_name'];
			// echo $HTTP_POST_FILES['files']['name'];
			// echo $_FILES['files']['name'];
			#$files=$_FILES['files']['tmp_name'];
			$numrec=0;
			$errrec=0;
			$ini=strpos($Form,"-");
			$query = "SELECT * from det_formulario ";
			$query = $query." where codigo = '".substr($Form,0,$ini)."'";
			$query = $query."   and medico = '".$key."'";
			$query = $query."   and tipo != '13' "  ;
			$query = $query."   and tipo != '17' "  ;
			$query= $query." order by posicion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$campos=array();
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$campos[$i]=$row[3];
			}
			$query = "SELECT * from det_formulario ";
			$query = $query." where codigo = '".substr($Form,0,$ini)."'";
			$query = $query."   and medico = '".$key."'";
			$query = $query."   and tipo != '13' "  ;
			$query = $query."   and tipo != '17' "  ;
			$query = $query." order by posicion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$id=0;
				#$files=stripslashes($files);
				#echo $files."<br>";
				$file = fopen($files,"r");
				while (!feof($file) and file_exists ( $files))
				{
					$size = filesize($files)+1;
					$data=fgetcsv($file,$size,",");
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$seguridad="C-".$key;
					$query = "insert  into ".$key."_".substr($Form,0,$ini);
					$query = $query." (medico,fecha_data,hora_data,";
					for ($i=0;$i<$num;$i++)
					{
						$query=$query.$campos[$i].",";
					}
					$query=$query."seguridad) ";
					$query=$query." values ('".$key."','".$fecha."','".$hora."'";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($row[4]== 1 or $row[4]== 2 or $row[4]== 6 or $row[4]== 8)
						{
							$query=$query.",";
							$query=$query.$data[$i];
						}
						else
						{
							$query=$query.",'";
							$query=$query.$data[$i]."'";	
						}
					}
					$query =$query.",'C-".$key."')";
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
							echo "NUMERO DE REGISTROS INSERTADOS : ".$numrec."<br>";
						}
					}
				}	
				echo "<BR><B>NUMERO DE REGISTROS TOTALES : ".$numrec."</B><br>";
				$numrec=$numrec-$errrec;
				echo "<B>NUMERO DE REGISTROS ERRONEOS : ".$errrec."</B><br>";
				echo "<B>NUMERO DE REGISTROS ADICIONADOS : ".$numrec."</B><br>";
			}
		}
}
?>
</body>
</html>
