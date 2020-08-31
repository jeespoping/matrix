<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Reportes x Formularios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> reportes.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
	$key = substr($user,2,strlen($user));
	echo "<form action='reportes.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'prioridad' value='".$prioridad."'>";
	echo "<input type='HIDDEN' name= 'grupo' value='".$grupo."'>";	
	if(!isset($tipo))
	{
		echo "<table border=0 align=center cellpadding=3>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Tipo de Formulario</td>";
		echo "<td bgcolor=#cccccc><input type='radio' name= 'tipo' value='C'>Compartidos - "; 
		echo "<input type='radio' name= 'tipo' value='P' checked>Propios</td>"; 
		$Form="0";
	}
	else
	{
		echo "<input type='HIDDEN' name= 'tipo' value='".$tipo."'>";
		if($tipo=="P")
			$query = "select * from formulario where medico='".$key."' order by codigo";
		else
			if(!isset($Form))
				$query = "select formulario.medico,codigo,nombre,tipo,seguridad.medico,grabacion,modificacion,lectura,reportes from seguridad,formulario where seguridad.usuario='".$key."' and seguridad.medico=formulario.medico and seguridad.formulario=formulario.codigo order by codigo";
			else
				$query = "select formulario.medico,codigo,nombre,tipo,seguridad.medico,grabacion,modificacion,lectura,reportes from seguridad,formulario where seguridad.usuario='".$key."' and seguridad.medico=formulario.medico and seguridad.formulario=formulario.codigo and formulario.codigo='".substr($Form,0,6)."' order by codigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$USER=$key;
		if(!isset($Form))
			$Form="0";
		if(!isset($owner))
			$owner=$key;
		$ini = strpos($Form,"-");
		echo "<table border=0 align=center cellpadding=3>";
		echo "<tr>";
		echo "<td bgcolor='#cccccc'><font size=2><b>Formularios :</b></font></td>";
		echo "<td bgcolor='#cccccc'><select name='Form'>";
		for ($i=0;$i<$num;$i++)
		{
			if ($row[1] == substr($Form,0,$ini) and $row[0]==$owner)
				echo "<option selected>".$row[1]."-".$row[0]."-".$row[3]."-".$row[2]."</option>";
			else
				echo "<option>".$row[1]."-".$row[0]."-".$row[3]."-".$row[2]."</option>";
			$row = mysql_fetch_array($err);
		}
		echo "</td>";
	}
	if(isset($Form)and $Form!="0")
	{
		$ini = strpos($Form,"-");
		for ($i=$ini+1;$i<strlen($Form);$i++)
		{
			if(substr($Form,$i,1)=="-")
			{
				$ini1=$i;
				$i=strlen($Form);
			}
		}
		$owner=substr($Form,$ini+1,$ini1-$ini-1);
	}
	echo "<td bgcolor='#cccccc' rowspan=4><input type='submit' value='IR'></tr>";
	echo "</table><br><br>";
	if($Form != "0")
	{	
		$query = "select * from reportes where medico='".$owner."' and formulario='".substr($Form,0,$ini)."' and nivel <= ".$prioridad;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			$color="#999999";
			echo "<table border=0 align=center>";
			echo "<tr>";
			echo "<td bgcolor=".$color."><b><font size=2>Codigo</font></b></td>";
			echo "<td bgcolor=".$color."><b><font size=2>Descripcion</font></b></td>";
  			echo "<td bgcolor=".$color."><b><font size=2>Seleccion</font></b></td>";
			echo "</tr>";
			$r = 0;
			for ($i=0;$i<$num;$i++)
			{
				if ($r == 0)
				{
					$color="#CCCCCC";
					$r = 1;
				}
				else
				{
					$color="#999999";
					$r = 0;
				}
				$row = mysql_fetch_array($err);
				echo "<tr>";
				echo "<td bgcolor=".$color."><font size=2>".$row[2]."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>".$row[3]."</font></td>";
				echo "<td bgcolor=".$color." align=center><A HREF='/matrix/".$grupo."/reportes/".$row[4]."' target = '_blank'><font size=2>Ejecutar</font></td></tr>";
			}
			echo "</table>";
			echo "<input type='HIDDEN' name= 'owner' value='".$owner."'>";	
		}
		else
	  		echo "Sin Reportes Asociados";
	}
	if(isset($err))
	{
		mysql_free_result($err);
		mysql_close($conex);
	}
  }
?>
</body>
</html>