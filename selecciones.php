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
	$key = substr($user,2,strlen($user));
	

	

	if (isset($wpar))
	{
		switch ($wpar)
		{
			case 1:
			$query = "update selecciones set descripcion='".$Descrip."', activo='".substr($Activo,0,1)."' where medico='".$key."' and codigo='".$Codigo."'";
			$err = mysql_query($query,$conex);
			break;
			case 2:
			$query = "insert selecciones values ('".$key."','".$Codigo."','".$Descrip."','".substr($Activo,0,1)."')";
			$err = mysql_query($query,$conex);
			break;
		}
	}
	echo "<table border=0 align=left>";
	echo "<tr><td align=center><A HREF='det_selecciones.php?pos1=0&amp;pos2=0'>Nuevo</td></tr></table><BR>\n";
	$query = "select * from selecciones where medico='".$key."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr>";
  		echo "<td bgcolor=".$color."><b>Codigo</b></td>";
  		echo "<td bgcolor=".$color."><b>Descripcion</b></td>";
  		echo "<td bgcolor=".$color."><b>Activo</b></td>";
  		echo "<td bgcolor=".$color."><b>Selecion</b></td>"; 		
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
			echo "<td bgcolor=".$color.">".$row[1]."</td>";
			echo "<td bgcolor=".$color.">".$row[2]."</td>";
			switch ($row[3])
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
			echo "<td bgcolor=".$color." align=center><A HREF='det_selecciones.php?pos1=".$row[0]."&amp;pos2=".$row[1]."'>Editar</td>";
			echo "</tr>";
		}
		echo "</tabla>";
		echo "<table border=0 align=center>";
		echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";	
	}
	else
	{
		echo " Tabla Vacia";
	}
	mysql_free_result($err);
	mysql_close($conex);
}
?>
</body>
</html>