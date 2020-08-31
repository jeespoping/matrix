<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td rowspan=2 align=center><IMG SRC="/matrix/images/medical/laboratorio/labmed.gif"></td>
<td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Consulta de Documentacion Legal</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> consulta.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='consulta.php' method=post>";
	$conex = mysql_pconnect('localhost','root','')
		or die("No se ralizo Conexion");
	

	if(!isset($cod) or !isset($tip) or !isset($des) or !isset($ent))
	{
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q1></td><td bgcolor=#cccccc>CODIGO</td><td bgcolor=#cccccc><input type='TEXT' name='cod' size=12 maxlength=12></td></tr> "; 
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q2></td><td bgcolor=#cccccc align=center>TIPO DOCUMENTO</td>";
		echo "<td bgcolor=#cccccc align=center>";
		$query = "SELECT subcodigo,descripcion from det_selecciones where medico = 'document' and codigo='002' order by subcodigo";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='tip'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q3></td><td bgcolor=#cccccc>DESCRIPCION</td><td bgcolor=#cccccc><input type='TEXT' name='des' size=20 maxlength=20></td></tr> "; 
		echo "<tr><td bgcolor=#cccccc><input type='checkbox' name=q4></td><td bgcolor=#cccccc>ENTIDAD</td><td bgcolor=#cccccc><input type='TEXT' name='ent' size=15 maxlength=15></td></tr> "; 
		echo "<tr><td bgcolor='#cccccc' align=center colspan=3><input type='submit' value='IR'></td></tr></table> ";
	}
	else
	{
		$query = "select Docdec, Doctip, Docapl, Docfec, Docdes, Docent, Docccs, Docest, Docfpv, Docobs  from document_000001 where ";
		$var=0;
		if (isset($q1))
		{
			$var++;
			$query=$query." Docdec = '".$cod."'";
		}
		if (isset($q2))
		{
			$ini=strpos($tip,"-");
			if($var > 0)
				$query=$query." and Doctip like '%".substr($tip,0,$ini)."%' ";
			else
				$query=$query."  Doctip like '%".substr($tip,0,$ini)."%' ";
			$var++;
		}
		if (isset($q3))
		{
			if($var > 0)
				$query=$query." and Docdes like '%".$des."%' ";
			else
				$query=$query." Docdes like '%".$des."%' ";
			$var++;
		}
		if (isset($q4))
		{
			if($var > 0)
				$query=$query." and Docent like '%".$ent."%' ";
			else
				$query=$query." Docent like '%".$ent."%' ";
			$var++;
		}
		if($var > 0)
		{
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$color="#999999";
			if ($num>0)
			{
				echo "<table border=0 align=center>";
	  			echo "<tr><td bgcolor=".$color."><b>Codigo</b></td>";
	  			echo "<td bgcolor=".$color." align=center><b>Tipo</b></td>";
	  			echo "<td bgcolor=".$color." align=center><b>C.C. <br>al que Aplica</b></td>";
	  			echo "<td bgcolor=".$color." align=center><b>Fecha <br>Documento</b></td>";
	  			echo "<td bgcolor=".$color." align=center><b>Descripcion</b></td>";
				echo "<td bgcolor=".$color." align=center><b>Entidad</b></td>";
				echo "<td bgcolor=".$color." align=center><b>En <br> Custodia De</b></td>";
				echo "<td bgcolor=".$color." align=center><b>Estado</b></td>";
				echo "<td bgcolor=".$color." align=center><b>Fecha <br>Publicacion</b></td>";
				echo "<td bgcolor=".$color." align=center><b>Observaciones</b></td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if ($i % 2 === 0)
						$color="#CCCCCC";
					else
						$color="#dddddd";
					echo "<tr>";
					echo "<td bgcolor=".$color.">".$row[0]."</td>";
					echo "<td bgcolor=".$color." align=center>".$row[1]."</td>";
					echo "<td bgcolor=".$color." align=center>".$row[2]."</td>";
					echo "<td bgcolor=".$color." align=center>".$row[3]."</td>";
					echo "<td bgcolor=".$color." align=center>".$row[4]."</td>";
					echo "<td bgcolor=".$color." align=center>".$row[5]."</td>";
					echo "<td bgcolor=".$color." align=center>".$row[6]."</td>";
					echo "<td bgcolor=".$color." align=center>".$row[7]."</td>";
					echo "<td bgcolor=".$color." align=center>".$row[8]."</td>";
					echo "<td bgcolor=".$color.">".$row[9]."</td>";
					echo "</tr>";
				}
				echo "</tabla>";
			}
		}
	}
}
?>
</body>
</html>