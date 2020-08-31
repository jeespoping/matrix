<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<A NAME="Arriba"><h1>Detalle de Selecciones Ver. 2008-11-06</h1></a>
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
	echo "<form action='det_detsel.php' method=post>";
	echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	

	

	if (isset($wpar))
	{
		switch ($wpar)
		{
			case 1:
			if(strlen($Descrip) > 0 and strlen($Activo) > 0)
			{
				$ini = strpos($Codigo,"-");
				$query = "update det_selecciones set descripcion='".$Descrip."', activo='".substr($Activo,0,1)."' where medico='".$pos1."' and codigo='".substr($Codigo,0,$ini)."' and subcodigo='".$Subcod."'";
				$err = mysql_query($query,$conex);
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>INFORMACION INCOMPLETA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
			break;
			case 2:
			if(strlen($Codigo) > 0 and strlen($Subcod) > 0 and strlen($Descrip) > 0 and strlen($Activo) > 0)
			{
				$ini = strpos($Codigo,"-");
				$query = "insert det_selecciones values ('".$pos1."','".substr($Codigo,0,$ini)."','".$Subcod."','".$Descrip."','".substr($Activo,0,1)."')";
				$err = mysql_query($query,$conex);
				if ($err != 1)
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>YA EXISTE EL CODIGO DEL CAMPO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				$pos3=0;
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>INFORMACION INCOMPLETA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
			break;
		}
	}
	$ini = strpos($pos2,"-");
	if ($ini == 0)
		$query = "select * from det_selecciones where medico='".$pos1."' and codigo='".$pos2."'";
	else
		$query = "select * from det_selecciones where medico='".$pos1."' and codigo='".substr($pos2,0,$ini)."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);
	}
	if($num>0)
	{
		echo "<li>Ultima Seleccion Grabada : ".$row[2]."-".$row[3];
		echo "<li>Numero de Selecciones Actuales : ".$num;
	}
	$ini = strpos($pos2,"-");
	if ($ini == 0)
		echo "<li><A HREF='detsel.php?Sel=".$pos2."-".$pos4."&key=".$key."' target='main'>Retornar</A>";
	else
		echo "<li><A HREF='detsel.php?Sel=".$pos2."&key=".$key."' target='main'>Retornar</A>";
	$query = "select * from det_selecciones where medico='".$pos1."' and codigo='".$pos2."' and subcodigo='".$pos3."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	echo "<table border=0 align=center>";	
	if ($num > 0)
	{	
		echo "<input type='HIDDEN' name= 'pos1' value='".$pos1."'>";	
		echo "<input type='HIDDEN' name= 'pos2' value='".$pos2."'>";
		echo "<input type='HIDDEN' name= 'pos3' value='".$pos3."'>";
		echo "<input type='HIDDEN' name= 'pos4' value='".$pos4."'>";
		echo "<tr>";
		echo "<td bgcolor=#999999><b>Item</td></b>";
		echo "<td bgcolor=#999999><b>Valor</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Codigo</td>";	
		echo "<input type='HIDDEN' name= 'Codigo' value='".$row[1]."-".$pos4."'>";		
		echo "<td bgcolor=#cccccc>".$row[1]."-".$pos4."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Subcodigo</td>";
		echo "<input type='HIDDEN' name= 'Subcod' value='".$row[2]."'>";			
		echo "<td bgcolor=#cccccc>".$row[2]."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Descripcion</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Descrip' size=50 maxlength=50 value='".$row[3]."'></td>";
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
		echo "<input type='HIDDEN' name= 'pos1' value='".$pos1."'>";	
		echo "<input type='HIDDEN' name= 'pos2' value='".$pos2."'>";
		echo "<tr>";
		echo "<td bgcolor=#999999><b>Item</td></b>";
		echo "<td bgcolor=#999999><b>Valor</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Codigo</td>";	
		echo "<input type='HIDDEN' name= 'Codigo' value='".$pos2."'>";		
		echo "<td bgcolor=#cccccc>".$pos2."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Subcodigo</td>";			
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Subcod' size=20 maxlength=20></td>";
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