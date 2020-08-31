<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Mapas Sensibles</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> mapa.php Ver. 1.00</b></font></tr></td></table>
</center>

<BODY TEXT="#000066">
<?php
include_once("conex.php");
		session_start();
		if(!isset($_SESSION['user']))
			echo"error";
		else
		{
			$key = substr($user,2,strlen($user));
			

			mysql_select_db("MATRIX");
			echo "<form action='mapa.php' method=post >";
			echo "<MAP NAME='procesos'>";
			$query = "SELECT  Codigo, Posicion   from proceso_000011 ";
			$query .= "     ORDER BY  Codigo  ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<AREA SHAPE=RECT COORDS='".str_replace("-",",",$row[1])."' HREF='/matrix/procesos/procesos/mapa.php?unidad=".$row[0]."'>";
			}
			echo "</MAP>";
			echo "<center>";
			echo "<A HREF='procesos.map'><IMG SRC='/matrix/images/medical/procesos/mapap.gif' ISMAP USEMAP='#procesos'> </A><br><br>";
			if(isset($unidad))
			{
				$query = "SELECT   Nombre   from proceso_000011 ";
				$query .= " where  Codigo = ".$unidad;
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				echo "<table border=0>";
				echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/root/matrix6.gif'></td></tr>";
				echo "<tr><td align=right colspan=2><font size=2>Powered by :  Ing. Pedro Ortiz Tamayo</font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066 colspan=2><font color=#ffffff size=6><b>DOCUMENTACION ISO 9000 -- UNIDAD : ".$row[0]."</font><font color=#33CCFF size=4>&nbsp&nbsp&nbspVer. 1.00</font></b></font></td></tr>";
				echo "</table><br><br>";  
				$query = "SELECT   Codigo, Descripcion from proceso_000010 ";
				$query .= " where  indice = ".$unidad;
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				echo "<table border=0>";
				echo "<tr><td align=center bgcolor=#999999 colspan=12><font color=#000066 size=5><b>DETALLE DE LOS DOCUMENTOS</b></font></td></tr>";
				echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >CODIGO</font></td><td align=center bgcolor=#000066><font color=#ffffff >DESCRIPCION</font></td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($i % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					echo "<tr>";
					echo "<td bgcolor=".$color.">".$row[0]."</td>";	
					echo "<td bgcolor=".$color.">".$row[1]."</td>";	
				}
				echo "</table><br><br>"; 
				echo "</center>";
			}
			include_once("free.php");
		}
?>
</body>
</html>