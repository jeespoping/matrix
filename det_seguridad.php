<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<A NAME="Arriba"><h1>Control de Acceso</h1></a>
</center>
<?php
include_once("conex.php");
	echo "<form action='det_seguridad.php' method=post>";
	
	$query = "select * from seguridad where medico='".$pos1."' and usuario='".$pos2."' and formulario='".$pos3."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	echo "<table border=0 align=center>";
	echo "<input type='HIDDEN' name='Medico'  value='".$pos1."'>";
	if ($num > 0)
	{	
		echo "<tr>";
		echo "<td bgcolor=#999999><b>Item</td></b>";
		echo "<td bgcolor=#999999><b>Valor</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Usuario</td>";	
		echo "<td bgcolor=#cccccc>".$row[1]."</td>";
		echo "<input type='HIDDEN' name= 'Usuario' value='".$row[1]."'>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Formulario</td>";	
		$query = "select * from formulario where medico='".$pos1."' and codigo='".$pos3."'";
		$err1 = mysql_query($query,$conex);	
		$row1 = mysql_fetch_array($err1);	
		echo "<td bgcolor=#cccccc>".$row1[1]."-".$row1[2]."</td>";		
		echo "<input type='HIDDEN' name='Formulario'  value='".$row1[1]."-".$row1[2]."'>";
		echo "</tr>";	
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Grabacion</td>";	
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Grabacion'>";
		if($row[3] == "1")
		{
			echo "<option selected>on</option>";
			echo "<option>off</option>";
		}
		else
		{
			echo "<option>on</option>";
			echo "<option selected>off</option>";
		}
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Modificacion</td>";	
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Modificacion'>";
		if($row[4] == "1")
			{
			echo "<option selected>on</option>";
			echo "<option>off</option>";
		}
		else
		{
			echo "<option>on</option>";
			echo "<option selected>off</option>";
		}
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Lectura</td>";	
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Lectura'>";
		if($row[5] == "1")
		{
			echo "<option selected>on</option>";
			echo "<option>off</option>";
		}
		else
		{
			echo "<option>on</option>";
			echo "<option selected>off</option>";
		}
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Reportes</td>";	
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Reportes'>";
		if($row[6] == "1")
		{
			echo "<option selected>on</option>";
			echo "<option>off</option>";
		}
		else
		{
			echo "<option>on</option>";
			echo "<option selected>off</option>";
		}
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Nivel</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Nivel' size=10 maxlength=10 value='".$row[7]."'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='GRABAR'></td>";
		echo "<input type='HIDDEN' name= 'wpar' value='1'>";
		echo "</tr>";
		echo "</tabla>";
	}
	else
	{
		echo "<tr>";
		echo "<td bgcolor=#999999><b>Item</b></td>";
		echo "<td bgcolor=#999999><b>Valor</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Usuario</td>";
		$query = "select grupo from usuarios where codigo='".$pos1."' ";
		$err1 = mysql_query($query,$conex);
		$grupo = mysql_fetch_array($err1);
		$query = "select codigo from usuarios where grupo='".$grupo[0]."' and codigo != '".$pos1."' ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Usuario'>";
		for ($j=0;$j<$num1;$j++)
		{	
			$row1 = mysql_fetch_array($err1);
			echo "<option>".$row1[0]."</option>";
		}
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Formulario</td>";			
		$query = "select * from formulario where medico='".$pos1."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Formulario'>";
		for ($j=0;$j<$num1;$j++)
		{	
			$row1 = mysql_fetch_array($err1);
			echo "<option>".$row1[1]."-".$row1[2]."</option>";
		}
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Grabacion</td>";	
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Grabacion'>";
		echo "<option>on</option>";
		echo "<option>off</option>";
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Modificacion</td>";	
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Modificacion'>";
		echo "<option>on</option>";
		echo "<option>off</option>";
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Lectura</td>";	
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Lectura'>";
		echo "<option>on</option>";
		echo "<option>off</option>";
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Reportes</td>";	
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Reportes'>";
		echo "<option>on</option>";
		echo "<option>off</option>";
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Nivel</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Nivel' size=10 maxlength=10></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='GRABAR'></td>";
		echo "<input type='HIDDEN' name= 'wpar' value='2'>";
		echo "</tr>";
		echo "</tabla>";
	}
	echo "</tabla>";
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
	mysql_free_result($err);
	mysql_close($conex);
?>
</body>
</html>