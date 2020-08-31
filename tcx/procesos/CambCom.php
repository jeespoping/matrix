<html>
<head>
  <title>MATRIX - ACTUALIZACION DE COMENTARIOS</title>
  <style type="text/css">
    	
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2em;}
    	#tipoG02{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:left;height:2em;}
    	#tipoG03{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2em;}
    	#tipoG04{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:left;height:2em;}
    	#tipoM01{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:2em;}
    	#tipoM02{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:center;height:2em;}
    	#tipoG05{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:12em;}
    	#tipoG06{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:left;height:12em;}
    	.tipo3A{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	
    </style>
    <SCRIPT>
	    function enter()
		{
			document.forms.CambCom.submit();
		}
	    function Cerrar() 
	    {
	       window.close();
	    }
    </SCRIPT>
</head>
<body BGCOLOR="#FFFFFF">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='CambCom' action='CambCom.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'wndt' value='".$wndt."'>";
	if($ok == 1)
	{
		if(strlen($wcom) > 0)
		{
			$wcomb = chr(10).chr(13)."------------------------------------------".chr(10).chr(13);
			$wcomb .= "***ACTUALIZACION DE COMENTARIOS*** : ".chr(10).chr(13);
			$wcom=$wcoma.$wcomb.chr(10).chr(13).date("Y-m-d H:i")." : ".$wcom." |Modificado x : ".$key."|";
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query =  " update ".$empresa."_000011 set ";
			$query .=  "  Turcom = '".$wcom."',"; 
			$query .=  "  Turusm = '".$key."' ";
			$query .=  "  where Turtur=".$wndt;
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO TURNOS : ".mysql_errno().":".mysql_error());
			$wcom = "COMENTARIO ACTUALIZADO";
		}
	}
	if($ok != 1)
		$wcom="";
	//                 0      1       2         3       4      5      6        7       8       9       10      11     12       13     14       15     16      17       18     19      20      21      22      23       24      25      26      27
	$query = "SELECT Turtur, Turqui, Turhin, Turhfi, Turest, Turord, Turcir, Turtcx, Turtip, Turnom, Turfna, Turtel, Turtcx, Turtip, Turtan, Tureps, Turmed, Turequ, Turuci, Turbio, Turinf, Turmat, Turban, Turins, Entdes, Turpre, Turmdo, Turcom  from ".$empresa."_000011, ".$empresa."_000003 ";
	$query .= " where Turtur = '".$wndt."' ";
	$query .= "   and Tureps = Entcod ";
	$query .= " UNION ALL  ";
	$query .= " SELECT Turtur, Turqui, Turhin, Turhfi, Turest, Turord, Turcir, Turtcx, Turtip, Turnom, Turfna, Turtel, Turtcx, Turtip, Turtan, Tureps, Turmed, Turequ, Turuci, Turbio, Turinf, Turmat, Turban, Turins, Empnom, Turpre, Turmdo, Turcom  from ".$empresa."_000011, cliame_000024 ";
	$query .= " where Turtur = '".$wndt."' ";
	$query .= "   and Tureps = Empcod ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		echo "<center><table border=0 align=center id=tipoG00>";
		echo "<tr><td id=tipoM01>ITEM</td><td id=tipoM02>DESCRIPCION</td></tr>";
		$row = mysql_fetch_array($err);
		echo "<center><input type='HIDDEN' name= 'wcoma' value='".$row[27]."'>";
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
		echo "<tr><td id=tipoG05>NUEVOS COMENTARIOS</td><td id=tipoG06><textarea name='wcom' cols=80 rows=8 class=tipo3A>".$wcom."</textarea></td></tr>";
		echo "<tr><td id=tipoM01><input type='RADIO' name=ok value=1 onclick='enter()'><b>[GRABAR]</b></td><td id=tipoM02><input type='RADIO' name=ok value=2 onclick='Cerrar()'><b>[CERRAR]</b></td>";
		echo "</table></center>";
	}
	else
		echo "LA IDENTIFICACION DEL TURNO NO EXISTE EN LA BASE DE DATOS<BR> CONSULTE CON SISTEMAS";
	//mysql_free_result($err);
	//mysql_close($conex);
}
?>
</body>
</html>
