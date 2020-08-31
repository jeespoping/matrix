<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Programa de Publicacion en Portales WEB</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> pubport.php Ver. 1.01</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='pubport.php' method=post>";
	

	

	if(isset($ok))
	{
		$wsw_sif=3;
		$wsw_sig=3;
		$wsw_sic=3;
		if(isset($sif))
		{
			$ini=strpos($grupo_sif,"-");
			$query="SELECT Codigo_Opcion FROM  root_000014 where Codigo_Grupo='".substr($grupo_sif,0,$ini)."' order by Codigo_Opcion desc";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$op=$row[0];
			$op++;
			while(strlen($op) < 3)
				$op="0".$op;
			if($tip == 1)
			{
				$datafile="/matrix/".$grupo."/reportes/"; 
				$path="./".$grupo."/reportes/"; 
			}
			elseif($tip == 2)
					{
						$datafile="/matrix/".$grupo."/procesos/"; 
						$path="./".$grupo."/procesos/"; 
					}
				else
					{
						$datafile="/matrix/"; 
						$path="./"; 
					}
			$pos=strpos($lin_sif,"?");
			if($pos === false)
				$file = $lin_sif;
			else
				$file = substr($lin_sif,0,$pos);
			$wsw_sif=0;
			if(file_exists($path.$file))
			{
				$query="SELECT count(*)  FROM  root_000014 where Programa like '%".$file."%'";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				if($row[0] == 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert root_000014 (medico,Fecha_data, Hora_data, Codigo_Grupo, Codigo_Opcion, Descripcion, Programa, Ruta, Seguridad ) values ('root','".$fecha."','".$hora."','".substr($grupo_sif,0,$ini)."','".$op."','".$Titulo."','".$lin_sif."','".$datafile."','C-root')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Opcion");
				}
				else
					$wsw_sif=1;
			}
			else
				$wsw_sif=2;
		}
		if(isset($sig))
		{
			$ini=strpos($grupo_sig,"-");
			$query="SELECT Codigo_Opcion FROM  root_000003 where Codigo_Grupo='".substr($grupo_sig,0,$ini)."' order by Codigo_Opcion desc";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$op=$row[0];
			$op++;
			while(strlen($op) < 3)
				$op="0".$op;
			if($tip == 1)
			{
				$datafile="/matrix/".$grupo."/reportes/"; 
				$path="./".$grupo."/reportes/"; 
			}
			elseif($tip == 2)
					{
						$datafile="/matrix/".$grupo."/procesos/"; 
						$path="./".$grupo."/procesos/"; 
					}
				else
					{
						$datafile="/matrix/"; 
						$path="./"; 
					}
			$pos=strpos($lin_sig,"?");
			if($pos === false)
				$file = $lin_sig;
			else
				$file = substr($lin_sig,0,$pos);
			$wsw_sig=0;
			if(file_exists($path.$file))
			{
				$query="SELECT count(*)  FROM  root_000003 where Programa like '%".$file."%'";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				if($row[0] == 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert root_000003 (medico,Fecha_data, Hora_data, Codigo_Grupo, Codigo_Opcion, Descripcion, Programa, Ruta, Seguridad ) values ('root','".$fecha."','".$hora."','".substr($grupo_sig,0,$ini)."','".$op."','".$Titulo."','".$lin_sig."','".$datafile."','C-root')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Opcion");
				}
				else
					$wsw_sig=1;
			}
			else
				$wsw_sig=2;
		}
		if(isset($sic))
		{
			$ini=strpos($grupo_sic,"-");
			$query="SELECT Codigo_Opcion FROM  root_000016 where Codigo_Grupo='".substr($grupo_sic,0,$ini)."' order by Codigo_Opcion desc";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$op=$row[0];
			$op++;
			while(strlen($op) < 3)
				$op="0".$op;
			if($tip == 1)
			{
				$datafile="/matrix/".$grupo."/reportes/"; 
				$path="./".$grupo."/reportes/"; 
			}
			elseif($tip == 2)
					{
						$datafile="/matrix/".$grupo."/procesos/"; 
						$path="./".$grupo."/procesos/"; 
					}
				else
					{
						$datafile="/matrix/"; 
						$path="./"; 
					}
			$pos=strpos($lin_sic,"?");
			if($pos === false)
				$file = $lin_sic;
			else
				$file = substr($lin_sic,0,$pos);
			$wsw_sic=0;
			if(file_exists($path.$file))
			{
				$query="SELECT count(*)  FROM  root_000016 where Programa like '%".$file."%'";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				if($row[0] == 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert root_000016 (medico,Fecha_data, Hora_data, Codigo_Grupo, Codigo_Opcion, Descripcion, Programa,Usuarios, Ruta, Seguridad ) values ('root','".$fecha."','".$hora."','".substr($grupo_sic,0,$ini)."','".$op."','".$Titulo."','".$lin_sic."','".$Usuarios."','".$datafile."','C-root')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Opcion");
				}
				else
					$wsw_sic=1;
			}
			else
				$wsw_sic=2;
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#cccccc align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROGRAMA DE PUBLICACION EN PORTALES WEB</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>RESULTADO DE LA PUBLICACION</b></td></tr>";
		echo "<tr><td bgcolor=#dddddd><b>SISTEMA DE INFORMACION EN UNIDADES (SIF)</b></td>";
		switch ($wsw_sif)
		{
			case 0:
				echo "<td bgcolor=#dddddd align=center><font color=#006600><b>PUBLICACION OK!!</b></font></td></tr>";
			break;
			case 1:
				echo "<td bgcolor=#dddddd align=center><font color=#FF9900><b>PROGRAMA PUBLICADO EN OTRA OPCION</b></font></td></tr>";
			break;
			case 2:
				echo "<td bgcolor=#dddddd align=center><font color=#CC0000><b>ARCHIVO NO EXISTE</b></td></font></tr>";
			break;
			case 3:
				echo "<td bgcolor=#dddddd align=center><font color=#000066><b>OPCION NO USADA</b></td></font></tr>";
			break;
		}
		echo "<tr><td bgcolor=#dddddd><b>SISTEMA DE INFORMACION GERENCIAL (SIG)</b></td>";
		switch ($wsw_sig)
		{
			case 0:
				echo "<td bgcolor=#dddddd align=center><font color=#006600><b>PUBLICACION OK!!</b></font></td></tr>";
			break;
			case 1:
				echo "<td bgcolor=#dddddd align=center><font color=#FF9900><b>PROGRAMA PUBLICADO EN OTRA OPCION</b></font></td></tr>";
			break;
			case 2:
				echo "<td bgcolor=#dddddd align=center><font color=#CC0000><b>ARCHIVO NO EXISTE</b></td></font></tr>";
			break;
			case 3:
				echo "<td bgcolor=#dddddd align=center><font color=#000066><b>OPCION NO USADA</b></td></font></tr>";
			break;
		}
		echo "<tr><td bgcolor=#dddddd><b>SISTEMA DE INFORMACION DE COSTOS (SIC)</b></td>";
		switch ($wsw_sic)
		{
			case 0:
				echo "<td bgcolor=#dddddd align=center><font color=#006600><b>PUBLICACION OK!!</b></font></td></tr>";
			break;
			case 1:
				echo "<td bgcolor=#dddddd align=center><font color=#FF9900><b>PROGRAMA PUBLICADO EN OTRA OPCION</b></font></td></tr>";
			break;
			case 2:
				echo "<td bgcolor=#dddddd align=center><font color=#CC0000><b>ARCHIVO NO EXISTE</b></td></font></tr>";
			break;
			case 3:
				echo "<td bgcolor=#dddddd align=center><font color=#000066><b>OPCION NO USADA</b></td></font></tr>";
			break;
		}
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#cccccc align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' ></td><td  align=center bgcolor=#cccccc colspan=3><font size=5><b>PROGRAMA DE PUBLICACION EN PORTALES WEB</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=4 align=center><b>DATOS GENERALES</b></td></tr>";
		echo "<tr><td bgcolor=#dddddd colspan=4 align=center><b>DESCRIPCION </b><input type='TEXT' name='Titulo' size=80 maxlength=80></td></tr>";
		echo "<tr><td bgcolor=#dddddd colspan=4 align=center><input type='RADIO' name=tip value=1 checked> REPORTE <input type='RADIO' name=tip value=2> PROCESO<input type='RADIO' name=tip value=3> PROGRAMA GENERAL</td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center colspan=4> Grupo Generador ";
		$query = "SELECT descripcion from det_selecciones where codigo='grupos' order by descripcion";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='grupo'>";
			echo "<option>Ninguno</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=4 align=center><b>PORTALES</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center><b>PORTAL</b></td><td bgcolor=#cccccc align=center><b>GRUPO</b></td><td bgcolor=#cccccc align=center><b>LINEA DE COMANDO</b></td><td bgcolor=#cccccc align=center><b>USUARIOS</b></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center>SIF <input type='checkbox' name='sif'></td>";
		$query="SELECT codigo,descripcion FROM  root_000013  order by codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<td bgcolor=#dddddd>";
		echo "<select name='grupo_sif'>";
		for($f=0;$f<$num;$f++)
		{
			$row = mysql_fetch_array($err);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</td><td bgcolor=#dddddd><input type='TEXT' name='lin_sif' size=30 maxlength=40></td><td bgcolor=#dddddd></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center>SIG <input type='checkbox' name='sig'></td>";
		$query="SELECT codigo,descripcion FROM  root_000002  order by codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<td bgcolor=#dddddd>";
		echo "<select name='grupo_sig'>";
		for($f=0;$f<$num;$f++)
		{
			$row = mysql_fetch_array($err);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</td><td bgcolor=#dddddd><input type='TEXT' name='lin_sig' size=30 maxlength=40></td><td bgcolor=#dddddd></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center>SIC <input type='checkbox' name='sic'></td>";
		$query="SELECT codigo,descripcion FROM  root_000015  order by codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<td bgcolor=#dddddd>";
		echo "<select name='grupo_sic'>";
		for($f=0;$f<$num;$f++)
		{
			$row = mysql_fetch_array($err);
			echo "<option>".$row[0]."-".$row[1]."</option>";
		}
		echo "</td><td bgcolor=#dddddd><input type='TEXT' name='lin_sic' size=30 maxlength=40></td><td bgcolor=#dddddd align=center><input type='TEXT' name='Usuarios' size=20 maxlength=40 value='gustavo-jaime-girlesa-rita-beatriz'></td></tr>";
		$ok="on";
		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		echo "<td bgcolor=#cccccc colspan=4 align=center><input type='submit' value='GENERAR'></td><tr>";
		echo"</table>";
	}
}
?>
</body>
</html>