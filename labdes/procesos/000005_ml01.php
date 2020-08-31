<html>

<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000005_ml01.php' method=post>";
		if(!isset($placa)  or !isset($descripcion))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
			echo "<tr><td align=center colspan=2>HISTORIA DE EL EQUIPO</td></tr>";
			echo "<tr> </tr>";
			echo "<tr><td bgcolor=#cccccc>Placa</td>";	
			echo "<td bgcolor=#cccccc><input type='text'";
			   if(isset($placa))
			   		echo"value='".$placa."' ";
			echo "  name='placa'>";
			echo"</td></tr>";
			echo "<tr><td bgcolor=#cccccc>Seleccione según la descripción:</td>";
			echo "<td bgcolor=#cccccc><select name='descripcion'>";
			$query = "select No_activo, Descripcion from manlab_000004 where placa='".$placa."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}	
			echo"</td></tr>";
			echo "<tr><td  align=center bgcolor=#cccccc colspan=2><input type='submit' name='ACEPTAR' value='ACEPTAR'></td></tr>";
		}
		else 
		{
			$ini1=strpos($descripcion,"-");	
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=4><b>CLINICA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=4><b>HISTORIA DEL EQUIPO: </b>'".substr($descripcion,0,$ini1)."-".$placa."'</td></tr>";
  	    	$query = "select * from manlab_000005 where Placa='".substr($descripcion,0,$ini1)."-".$placa.substr($descripcion,$ini1)."'   ";
	    	$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo"<tr></tr>";
			if($num>0)
			{
			     for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					echo"<tr></tr>";
					echo "<tr><td><b> Fecha: </b>".$row[4]."</td><td><b> Hora: </b>".substr($row[5],0,5)."</td><td><b> Asunto: </b>".$row[6]."</td><td><b> Categoria: </b>".substr($row[7],3)."</td></tr>";
					echo "<tr><td colspan=4><b> Descripción:</b><br><fr>".$row[8]."</fr></td></tr>";
					echo "<tr><td colspan=4><b> Nuevo estado: </b>".substr($row[9],3)."</td></tr>";															
				}
			}	
			else
			echo "<tr><td align=center colspan=4>NO EXISTE HISTORIAL PARA ESTE EQUIPO</td></tr>";
			
		}	
			
}
?>		