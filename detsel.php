<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle Selecciones</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> detsel.php Ver. 2008-11-06</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}
	echo "<form action='detsel.php' method=post>";
	if(!isset($key))
		$key = substr($user,2,strlen($user));
	else
		echo "<input type='HIDDEN' name= 'key' value='".$key."'>";

	if(isset($Sel))
		$ini = strpos($Sel,"-");
	else
		$ini=0;
	

	

	$query = "select * from selecciones where medico='".$key."' order by descripcion";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	echo "<table border=0 align=center cellpadding=3>";
	echo "<tr>";
	echo "<td bgcolor='#cccccc'><font size=2><b>Seleccion :</b></font></td>";
	echo "<td bgcolor='#cccccc'><select name='Sel'>";
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);
		if($ini != 0 and $row[1] == substr($Sel,0,$ini) and $row[2] == substr($Sel,$ini+1))
			echo "<option selected>".$row[1]."-".$row[2]."</option>";
		else
			echo "<option>".$row[1]."-".$row[2]."</option>";
	}
	echo "<td bgcolor='#cccccc'><input type='submit' value='IR'>";
	echo "</td></tr></table><br>";
	if(!isset($Sel))
		$Sel="0";
	else
	{
	$ini = strpos($Sel,"-");
	echo "<table border=0 align=left>";
	echo "<tr><td align=center><A HREF='det_detsel.php?pos1=".$key."&amp;pos2=".$Sel."&amp;pos3=0&amp;key=".$key."'>Nuevo</td></tr></table><BR>\n";
	$query = "select * from det_selecciones where medico='".$key."' and codigo='".substr($Sel,0,$ini)."' order by codigo,subcodigo";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr>";
  		echo "<td bgcolor=".$color."><font size=2><b>Codigo</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Subcodigo</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Descripcion</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Activo</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Selecion</b></font></td>"; 		
		echo "</tr>";
		$r = 0;
		for ($i=0;$i<$num;$i++)
		{
			$r = $i/2;
			if ($r*2 === $i)
				$color="#CCCCCC";
			else
				$color="#999999";
			$row = mysql_fetch_array($err);
			echo "<tr>";	
			$query = "select * from selecciones where medico='".$key."' and codigo='".$row[1]."'";
			$err1 = mysql_query($query,$conex);	
			$row1 = mysql_fetch_array($err1);	
			echo "<td bgcolor=".$color."><font size=2>".$row1[2]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[2]."</font></td>";
			echo "<td bgcolor=".$color."><font size=2>".$row[3]."</font></td>";
			switch ($row[4])
			{
				case "A":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
					break;
				case "I":
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/inactivo.gif' ></td>";
					break;
				default:
					echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
					break;
			}
			echo "<td bgcolor=".$color." align=center><A HREF='det_detsel.php?pos1=".$row[0]."&amp;pos2=".$row[1]."&amp;pos3=".$row[2]."&amp;pos4=".$row1[2]."&amp;key=".$key."'><font size=2>Editar</font></td>";
			echo "</tr>";
		}
		echo "</tabla>";
		echo "<table border=0 align=center>";
		echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";	
	}
	else
		echo " Tabla Vacia";
	}
	mysql_free_result($err);
	mysql_close($conex);
}
?>
</body>
</html>