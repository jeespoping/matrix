<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<A NAME="Arriba"><h1>Definicion de Procesos</h1></a>
</center>
<?php
include_once("conex.php");
	echo "<form action='defpro.php' method=post>";
	

	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}

	$query = "select * from procesos where medico='".$pos1."' and formulario='".$pos2."' and codigo='".$pos3."'";
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
		echo "<td bgcolor=#cccccc>Formulario</td>";	
		$query = "select * from formulario where medico='".$pos1."' and codigo='".$pos2."'";
		$err1 = mysql_query($query,$conex);	
		$row1 = mysql_fetch_array($err1);	
		echo "<td bgcolor=#cccccc>".$row1[1]."-".$row1[2]."</td>";
		echo "<input type='HIDDEN' name= 'Formulario' value='".$row1[1]."-".$row1[2]."'>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Codigo</td>";	
		echo "<td bgcolor=#cccccc>".$row[2]."</td>";		
		echo "<input type='HIDDEN' name='Codigo'  value='".$row[2]."'>";
		echo "</tr>";	
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Descripcion</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Descripcion' size=60 maxlength=60 value='".$row[3]."'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Nombre</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Nombre' size=80 maxlength=80 value='".$row[4]."'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Nivel</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Nivel' size=10 maxlength=10 value='".$row[5]."'></td>";
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
		echo "<td bgcolor=#cccccc>Formulario</td>";	
		$query = "select codigo,nombre from formulario where medico='".$pos1."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Formulario'>";
		for ($j=0;$j<$num1;$j++)
		{	
			$row1 = mysql_fetch_array($err1);
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
		}
		echo "</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Codigo</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Codigo' size=4 maxlength=4></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Descripcion</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Descripcion' size=60 maxlength=60></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Nombre</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Nombre' size=80 maxlength=80></td>";
		echo "</tr>";
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