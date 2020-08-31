<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Busqueda de Modulos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc97.php Ver. 1.01</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc97.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($criterio))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>SISTEMA DE INFORMACION DE COSTOS Y PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>BUSQUEDA DE MODULOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Criterio de Busqueda</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='criterio' size=60 maxlength=60></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2>X Nombre<input type='RADIO' name=tipo value=1 checked> X Programa<input type='RADIO' name=tipo value=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
			echo "<tr><td align=center  colspan=5><b>SISTEMA DE INFORMACION DE COSTOS Y PRESUPUESTOS</b></td></tr>";
			echo "<tr><td align=center  colspan=5><b>BUSQUEDA DE MODULOS</b></td></tr>";
			$query = "SELECT Codigo, root_000002.Descripcion, Codigo_Opcion,root_000003.Descripcion,programa    from root_000002,root_000003 ";
			if ($tipo == "1")
				$query = $query." where root_000003.Descripcion like '%".$criterio."%'";
			else
				$query = $query." where programa like '%".$criterio."%'";
			$query = $query."   and Codigo_grupo= Codigo";
			$query = $query."   order by Codigo,Codigo_Opcion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<tr><td align=center  colspan=5 bgcolor=#cccccc><b>PORTAL SIG</b></td></tr>";
			    echo "<tr><td><b>CODIGO GRUPO</b></td><td><b>NOMBRE GRUPO</b></td><td><b>CODIGO OPCION</b></td><td><b>NOMBRE OPCION</b></td><td><b>PROGRAMA</b></td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					 echo "<tr>";
   					 echo "<td>".$row[0]."</td>";
   					 echo "<td>".$row[1]."</td>";
   					 echo "<td>".$row[2]."</td>";
   					 echo "<td>".$row[3]."</td>";
   					 echo "<td>".$row[4]."</td>";
				}
			}
			$query = "SELECT Codigo, root_000013.Descripcion, Codigo_Opcion,root_000014.Descripcion,programa    from root_000013,root_000014 ";
			if ($tipo == "1")
				$query = $query." where root_000014.Descripcion like '%".$criterio."%'";
			else
				$query = $query." where programa like '%".$criterio."%'";
			$query = $query."   and Codigo_grupo= Codigo";
			$query = $query."   order by Codigo,Codigo_Opcion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<tr><td align=center  colspan=5 bgcolor=#cccccc><b>PORTAL SIF</b></td></tr>";
				echo "<tr><td><b>CODIGO GRUPO</b></td><td><b>NOMBRE GRUPO</b></td><td><b>CODIGO OPCION</b></td><td><b>NOMBRE OPCION</b></td><td><b>PROGRAMA</b></td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					 echo "<tr>";
   					 echo "<td>".$row[0]."</td>";
   					 echo "<td>".$row[1]."</td>";
   					 echo "<td>".$row[2]."</td>";
   					 echo "<td>".$row[3]."</td>";
   					 echo "<td>".$row[4]."</td>";
				}
			}
			$query = "SELECT Codigo, root_000015.Descripcion, Codigo_Opcion,root_000016.Descripcion,programa    from root_000015,root_000016 ";
			if ($tipo == "1")
				$query = $query." where root_000016.Descripcion like '%".$criterio."%'";
			else
				$query = $query." where programa like '%".$criterio."%'";
			$query = $query."   and Codigo_grupo= Codigo";
			$query = $query."   order by Codigo,Codigo_Opcion";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<tr><td align=center  colspan=5 bgcolor=#cccccc><b>PORTAL SIC</b></td></tr>";
				echo "<tr><td><b>CODIGO GRUPO</b></td><td><b>NOMBRE GRUPO</b></td><td><b>CODIGO OPCION</b></td><td><b>NOMBRE OPCION</b></td><td><b>PROGRAMA</b></td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					 echo "<tr>";
   					 echo "<td>".$row[0]."</td>";
   					 echo "<td>".$row[1]."</td>";
   					 echo "<td>".$row[2]."</td>";
   					 echo "<td>".$row[3]."</td>";
   					 echo "<td>".$row[4]."</td>";
				}
			}
		}
}
?>
</body>
</html>