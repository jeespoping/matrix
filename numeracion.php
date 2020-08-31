<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<A NAME="Arriba"><h1>Control de Numeracion</h1></a>
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
		//VALIDACION
		$ini=strpos($Campo,"-");
		if(substr($Formulario,0,6) !=substr($Campo,0,6) or strlen($Secuencia)==0)
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR EN LOS DATOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
			echo "<br><br>";
		}
		else
		{
			$Formulario=substr($Formulario,0,6);
			$Campo=substr($Campo,$ini+1,4);
			switch ($wpar)
			{
				case 1:
				$query = "update numeracion set secuencia=".$Secuencia." where medico='".$Medico."' and formulario='".$Formulario."' and campo='".$Campo."'";
				$err = mysql_query($query,$conex);
				break;
				case 2:
				$query = "insert numeracion values ('".$Medico."','".$Formulario."','".$Campo."',".$Secuencia.")";
				$err = mysql_query($query,$conex);
				if ($err != 1)
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>YA EXISTE EL CODIGO DEL CAMPO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				break;
			}
		}	
	}
	echo "<table border=0 align=left>";
	echo "<tr><td align=center><A HREF='det_numeracion.php?pos1=".$key."&amp;pos2=0&amp;pos3=0'>Nuevo</td></tr></table><BR>\n";
	$query = "select * from numeracion where medico='".$key."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		$color="#999999";
		echo "<table border=0 align=center>";
		echo "<tr>";
  		echo "<td bgcolor=".$color."><font size=2><b>Formulario</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Campo</b></font></td>";
  		echo "<td bgcolor=".$color."><font size=2><b>Secuencia</b></font></td>";
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
			$query = "select * from formulario where medico='".$key."' and codigo='".$row[1]."'";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);			
			echo "<td bgcolor=".$color."><font size=2>".$row1[1]."-".$row1[2]."</font></td>";
			$query = "select * from det_formulario where medico='".$key."' and codigo='".$row[1]."' and campo='".$row[2]."'";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);		
			echo "<td bgcolor=".$color."><font size=2>".$row1[2]."-".$row1[3]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font size=2>".$row[3]."</font></td>";
			echo "<td bgcolor=".$color." align=center><font size=2><A HREF='det_numeracion.php?pos1=".$key."&amp;pos2=".$row[1]."&amp;pos3=".$row[2]."'>Editar</font></td>";
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