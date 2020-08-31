<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Registro de Formularios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> det_formularios.php Ver. 2006-11-14</b></font></tr></td></table>
</center>
<center>
<A NAME="Arriba"><h1>Registro de Formularios</h1></a>
</center>
<?php
include_once("conex.php");
	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}
	echo "<form action='formularios.php' method=post>";
	

	

	$query = "select * from formulario where medico='".$pos1."' and codigo='".$pos2."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	echo "<table border=0 align=center>";
	if ($num > 0)
	{	
		echo "<input type='HIDDEN' name= 'Codigo' value='".$row[1]."'>";
		echo "<input type='HIDDEN' name= 'Nombre' value='".$row[2]."'>";
		echo "<tr>";
		echo "<td bgcolor=#999999><b>Item</td></b>";
		echo "<td bgcolor=#999999><b>Valor</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Codigo</td>";			
		echo "<td bgcolor=#cccccc>".$row[1]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Nombre</td>";			
		echo "<td bgcolor=#cccccc>".$row[2]."</td>";
		echo "</tr>";	
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Tipo</td>";
		echo "<td bgcolor=#cccccc>";			
		echo "<select name='Tipo'>";
				if ($row[3] == substr("C-Cerrado", 0, 1))
			echo "<option selected>C-Cerrado</option>";
		else
			echo "<option>C-Cerrado</option>";	
		if ($row[3] == substr("A-Abierto", 0, 1))
			echo "<option selected>A-Abierto</option>";
		else
			echo "<option>A-Abierto</option>";
		echo "</td>";	
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Activo</td>";
		echo "<td bgcolor=#cccccc>";			
		echo "<select name='Activo'>";
		if ($row[4] == substr("A-Activo", 0, 1))
			echo "<option selected>A-Activo</option>";
		else
			echo "<option>A-Activo</option>";
		if ($row[4] == substr("I-Inactivo", 0, 1))
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
		$query = "select * from formulario where medico='".$pos1."'and codigo not like 't%' ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		$totalc=$num1+1;
		$long=strlen($totalc);
		for ($w=0;$w<6-$long;$w++)
			$totalc="0".$totalc;
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Codigo</td>";			
		echo "<td bgcolor=#cccccc>".$totalc."</td>";
		echo "<input type='HIDDEN' name= 'Codigo' value='".$totalc."'>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Nombre</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Nombre' size=50 maxlength=50></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Tipo</td>";
		echo "<td bgcolor=#cccccc>";			
		echo "<select name='Tipo'>";
		echo "<option>C-Cerrado</option>";
		echo "<option>A_Abierto</option>";
		echo "</td>";	
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
