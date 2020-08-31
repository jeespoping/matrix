<html>
<head>
  	<title>MATRIX FLUSH Base De Datos</title>
  	<style type="text/css">
		.tipoTABLE{font-family:Arial;border-style:solid;border-collapse:collapse;}
		#tipoT01L{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
		#tipoT01C{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipoT01R{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
		#tipoT02L{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
		#tipoT02C{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipoT02R{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
		#tipoT03L{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:left;}
		#tipoT03C{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipoT03R{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:right;}
		#tipoT04C{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipoT05R{color:#000066;background:#F5A9A9;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}
		#tipoT06C{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:normal;text-align:center;}
		#tipoT06R{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:normal;text-align:right;}
	</style>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return false" onselectstart = "return true" ondragstart = "return false">
</script>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
	/**********************************************************************************************************************  
		   PROGRAMA : flush.php
		   Fecha de Liberación : 2015-03-06
		   Autor : Ing. Pedro Ortiz Tamayo
		   Version Actual : 2015-03-06
		   
		   OBJETIVO GENERAL : 
		   Se implementa el comando FLUSH QUERY CACHE para inicializar el cache de querys.
		   Fisicamente.
		   
		   REGISTRO DE MODIFICACIONES :
				
			.2015-03-06
				Inicio del Programa a Produccion.
	***********************************************************************************************************************/
	echo "<form name='flush' action='flush.php' method=post>";
	

	

	$query = "FLUSH QUERY CACHE";
	$err = mysql_query($query,$conex);
	$query = "show full processlist ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$wcolor="#cccccc";
	echo "<table border=0 align=center>";
	echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
	echo "<tr><td align=center bgcolor=#000066 colspan=1><font color=#ffffff size=4><b>MANTENIMIENTO  BASE DE DATOS</font></b></font></td></tr>";
	echo "<tr><td  align=center bgcolor=".$wcolor."> Lista de Procesos Corriendo en la Base de Datos </td></tr>";
	echo "</table><br><br>";  
	echo "<table border=0 align=center>";
	echo "<tr><td align=center bgcolor=#999999 colspan=6><font color=#000066 size=3><b>DETALLE DE PROCESOS</b></font></td></tr>";
	echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >ID</font></td><td align=center bgcolor=#000066><font color=#ffffff >BASE DE<BR> DATOS</font></td><td align=center bgcolor=#000066><font color=#ffffff >COMANDO</font></td><td align=center bgcolor=#000066><font color=#ffffff >TIEMPO<br> Segundos</font></td><td align=center bgcolor=#000066><font color=#ffffff >ESTADO</font></td><td align=center bgcolor=#000066><font color=#ffffff >INFORMACION</font></td></tr>";				
	for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);
		if($i % 2 == 0)
			$color="#dddddd";
		else
			$color="#cccccc";
		echo "<tr>";
		echo "<td   align=center bgcolor=".$color.">".$row[0]."</td>";
		echo "<td   align=center bgcolor=".$color.">".$row[3]."</td>";
		echo "<td   align=center bgcolor=".$color.">".$row[4]."</td>";
		echo "<td   align=center bgcolor=".$color.">".$row[5]."</td>";
		echo "<td bgcolor=".$color.">".$row[6]."</td>";
		echo "<td bgcolor=".$color.">".$row[7]."</td>";
		echo "</tr>";
		echo "<input type='HIDDEN' name= 'num' value=".$num.">";
		echo "<input type='HIDDEN' name= 'id[".$i."]' value='".$row[0]."'>";
	}
	echo "<tr><td align=center bgcolor=#CCCCCC colspan=7><font color=#000066 size=3><b>TOTAL PROCESOS : ".$num."</b></font></td></tr>";
	echo "</table><br><br>"; 
?>
