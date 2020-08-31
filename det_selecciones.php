<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<A NAME="Arriba"><h1>Registro de Selecciones</h1></a>
</center>
<?php
include_once("conex.php");
	echo "<form action='selecciones.php' method=post>";
	

	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}

	$query = "select * from selecciones where medico='".$pos1."' and codigo='".$pos2."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	echo "<table border=0 align=center>";
	
	if ($num > 0)
	{	
		echo "<input type='HIDDEN' name= 'Codigo' value='".$row[1]."'>";
		echo "<input type='HIDDEN' name= 'Descrip' value='".$row[2]."'>";
		echo "<tr>";
		echo "<td bgcolor=#999999><b>Item</td></b>";
		echo "<td bgcolor=#999999><b>Valor</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Codigo</td>";			
		echo "<td bgcolor=#cccccc>".$row[1]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Descripcion</td>";			
		echo "<td bgcolor=#cccccc>".$row[2]."</td>";
		echo "</tr>";	
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Activo</td>";
		echo "<td bgcolor=#cccccc>";			
		echo "<select name='Activo'>";
		if ($row[3] == substr("A-Activo", 0, 1))
			echo "<option selected>A-Activo</option>";
		else
			echo "<option>A-Activo</option>";
		if ($row[3] == substr("I-Inactivo", 0, 1))
			echo "<option selected>I-Inactivo</option>";
		else
			echo "<option>I-Inactivo</option>";	
		echo "</td>";	
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
		echo "<td bgcolor=#999999><b>Item</td></b>";
		echo "<td bgcolor=#999999><b>Valor</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Codigo</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Codigo' size=10 maxlength=10></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Descripcion</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Descrip' size=50 maxlength=50></td>";
		echo "</tr>";	
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Activo</td>";
		echo "<td bgcolor=#cccccc>";			
		echo "<select name='Activo'>";
		echo "<option>A-Activo</option>";
		echo "<option>I-Inactivo</option>";	
		echo "</td>";	
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