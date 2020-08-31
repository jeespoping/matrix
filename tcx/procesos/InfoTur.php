<html>
<head>
  <title>MATRIX - INFORMACION GENERAL DEL TURNO QUIRURGICO</title>
  <style type="text/css">
    	
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2em;}
    	#tipoG02{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:left;height:2em;}
    	#tipoG03{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2em;}
    	#tipoG04{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:left;height:2em;}
    	#tipoM01{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:2em;}
    	#tipoM02{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:center;height:2em;}
    	
    </style>
    <SCRIPT>
      function Cerrar() {
        setTimeout("close();",30000);
      }
      </SCRIPT>
</head>
<body onLoad="Cerrar();" BGCOLOR="#FFFFFF">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='InfoTur' action='InfoTur.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	//                 0      1       2         3       4      5      6        7       8       9       10      11     12       13     14       15     16      17       18     19      20      21      22      23       24      25      26
	$query = "SELECT Turtur, Turqui, Turhin, Turhfi, Turest, Turord, Turcir, Turtcx, Turtip, Turnom, Turfna, Turtel, Turtcx, Turtip, Turtan, Tureps, Turmed, Turequ, Turuci, Turbio, Turinf, Turmat, Turban, Turins, Entdes, Turpre, Turmdo  from ".$empresa."_000011, ".$empresa."_000003 ";
	$query .= " where Turtur = '".$MENSAGE."' ";
	$query .= "   and Tureps = Entcod ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		echo "<center><table border=0 align=center id=tipoG00>";
		echo "<tr><td id=tipoM01>ITEM</td><td id=tipoM02>DESCRIPCION</td></tr>";
		$row = mysql_fetch_array($err);
		echo "<tr><td id=tipoG01>NOMBRE</td><td id=tipoG02>".$row[9]."</td></tr>";
		echo "<tr><td id=tipoG03>FECHA DE NACIMIENTO</td><td id=tipoG04>".$row[10]."</td></tr>";
		echo "<tr><td id=tipoG01>TELEFONO</td><td id=tipoG02>".$row[11]."</td></tr>";
		echo "<tr><td id=tipoG03>CIRUGIA</td><td id=tipoG04>".$row[6]."</td></tr>";
		echo "<tr><td id=tipoG01>MEDICOS</td><td id=tipoG02>".$row[16]."</td></tr>";
		echo "<tr><td id=tipoG03>EQUIPOS</td><td id=tipoG04>".$row[17]."</td></tr>";
		echo "<tr><td id=tipoG01>RESPONSABLE</td><td id=tipoG02>".$row[24]."</td></tr>";
		$query = "SELECT Seldes from ".$empresa."_000013 ";
		$query .= " where Seltip = '02' ";
		$query .= "   and Selcod = '".$row[23]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<tr><td id=tipoG03>INSTRUMENTADORA</td><td id=tipoG04>".$row1[0]."</td></tr>";
		$query = "SELECT Seldes from ".$empresa."_000013 ";
		$query .= " where Seltip = '03' ";
		$query .= "   and Selcod = '".$row[12]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<tr><td id=tipoG01>TIPO DE CIRUGIA</td><td id=tipoG02>".$row1[0]."</td></tr>";
		$query = "SELECT Seldes from ".$empresa."_000013 ";
		$query .= " where Seltip = '04' ";
		$query .= "   and Selcod = '".$row[13]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<tr><td id=tipoG03>TIPO DE PROGRAMACION</td><td id=tipoG04>".$row1[0]."</td></tr>";
		$query = "SELECT Seldes from ".$empresa."_000013 ";
		$query .= " where Seltip = '05' ";
		$query .= "   and Selcod = '".$row[14]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		echo "<tr><td id=tipoG01>TIPO DE ANESTESIA</td><td id=tipoG02>".$row1[0]."</td></tr>";
		$bool="NO";
		if($row[18] == "on")
			$bool="SI";
		echo "<tr><td id=tipoG03>UCI</td><td id=tipoG04>".$bool."</td></tr>";
		$bool="NO";
		if($row[19] == "on")
			$bool="SI";
		echo "<tr><td id=tipoG01>BIOPSIA X CONGELACION</td><td id=tipoG02>".$bool."</td></tr>";
		$bool="NO";
		if($row[20] == "on")
			$bool="SI";
		echo "<tr><td id=tipoG03>CIRUGIA INFECTADA</td><td id=tipoG04>".$bool."</td></tr>";
		$bool="NO";
		if($row[21] == "on")
			$bool="SI";
		echo "<tr><td id=tipoG01>MATERIALES</td><td id=tipoG02>".$bool."</td></tr>";
		$bool="NO";
		if($row[22] == "on")
			$bool="SI";
		echo "<tr><td id=tipoG03>COMPONENTES SANGUINEOS</td><td id=tipoG04>".$bool."</td></tr>";
		echo "</table></center>";
	}
	else
		echo "LA IDENTIFICACION DEL TURNO NO EXISTE EN LA BASE DE DATOS<BR> CONSULTE CON SISTEMAS";
	mysql_free_result($err);
	mysql_close($conex);
}
?>
</body>
</html>