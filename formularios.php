<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
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

@session_start();
if(!isset($_SESSION['user']))
echo "error";
else
	{
	$key = substr($user,2,strlen($user));
	
	 
	
	if (isset($wpar))
	{
		switch ($wpar)
		{
			case 1:
			$query = "update formulario set nombre='".$Nombre."', tipo='".substr($Tipo,0,1)."', activo='".substr($Activo,0,1)."' where medico='".$key."' and codigo='".$Codigo."'";
			$err = mysql_query($query,$conex);
			break;
			case 2:
			if((integer)$Codigo < 1000000)
			{
				$query = "insert formulario values ('".$key."','".$Codigo."','".$Nombre."','".substr($Tipo,0,1)."','".substr($Activo,0,1)."')";
				$err = mysql_query($query,$conex);
				if ($err != 1)
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>YA EXISTE EL FORMULARIO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>NO PUEDE CREAR EL FORMULARIO - SOBREPASA EL LIMITE DE 999.999 FORMULARIOS X USUARIO !!!!</MARQUEE></FONT>";
				echo "<br><br>";			
			}
			break;
		}
	}
	echo "<table border=0 align=left>";
	echo "<tr><td align=center><A HREF='det_formularios.php?pos1=".$key."&amp;pos2=0'>Nuevo</td></tr></table><BR>\n";
	$query = "select * from formulario where medico='".$key."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr>";
  		echo "<td bgcolor=".$color."><b>Codigo</b></td>";
  		echo "<td bgcolor=".$color."><b>Nombre</b></td>";
  		echo "<td bgcolor=".$color."><b>Tipo</b></td>";
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
			echo "<td bgcolor=".$color." align=center>".$row[3]."</td>";
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
			echo "<td bgcolor=".$color." align=center><A HREF='det_formularios.php?pos1=".$row[0]."&amp;pos2=".$row[1]."'>Editar</td>";
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
