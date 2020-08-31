<html>
<head>
  	<title>MATRIX Lista de Turnos de Cirugia y Actualizacion de Estados ListaWC.php</title>
  	<!-- UTF-8 is the recommended encoding for your pages -->
 
    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo3A{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo4A{color:#000066;background:#99CCFF;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo20{color:#000066;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	#tipo21{color:#000066;background:#DDDDDD;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}

    	#tipo00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoTCK{color:#000066;background:#999999;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:4.5em;}
    	#tipoT00{color:#000066;background:#999999;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:center;height:4.5em;}
    	#tipoT01{color:#000066;background:#999999;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:4.5em;}
    	#tipoT02{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT03{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT04{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:4.5em;}
    	#tipoT05{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	#tipoT06{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT07{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3.0em;text-align:center;height:4.5em;}
    	#tipoT08{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:4.5em;}
    	#tipoT09{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:4.5em;}
    	#tipoT10{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:4.5em;}
    	#tipoT11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT12{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT13{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT14{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT15{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:4.5em;}
    	#tipoT16{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:4.5em;}
    	#tipoT17{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:50em;text-align:center;height:4.5em;}
    	
    	#tipoD00{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:center;height:4.5em;}
    	#tipoDCK{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	#tipoD01{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	#tipoDE01{color:#000066;background:#CC99FF;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:3em;}
    	#tipoD02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoD03{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoD04{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoD05{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoD06{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoD07{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoD08{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoD09{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoD10{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoD11{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoD12{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoD13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoD14{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoD15{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoD16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:4.5em;}
    	#tipoD17{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	
    	#tipoL00{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:center;height:4.5em;}
    	#tipoLCK{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	#tipoL01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	#tipoL001{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:1em;}
    	#tipoLE01{color:#000066;background:#CC99FF;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:3em;}
    	#tipoL02{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL03{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL04{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL05{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL06{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL07{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL08{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL09{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL10{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL11{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL12{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL13{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL14{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL15{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL16{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:4.5em;}
    	#tipoL17{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	
    	#tipoF00{color:#000066;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:bold;width:40em;text-align:left;height:4.5em;}
    	#tipoF01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Arial;font-weight:bold;width:40em;text-align:left;height:4.5em;}
    	#tipoF02{color:#000066;background:#FFFFCC;font-size:7pt;font-family:Arial;font-weight:bold;width:40em;text-align:left;height:4.5em;}
    	#tipoF03{color:#000066;background:#FEAAA4;font-size:7pt;font-family:Arial;font-weight:bold;width:40em;text-align:left;height:4.5em;}
    	#tipoF04{color:#000066;background:#C3D9FF;font-size:7pt;font-family:Arial;font-weight:bold;width:40em;text-align:left;height:4.5em;}
    	#tipoF05{color:#000066;background:#CCFFCC;font-size:7pt;font-family:Arial;font-weight:bold;width:40em;text-align:left;height:4.5em;}
    	
    </style>
</head>
<body  BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.ListaWC.submit();
	}
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function teclado2()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13) event.returnValue = false;
	}
	function teclado3()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13 & event.keyCode != 45) event.returnValue = false;
	}
	function ejecutar(path,tipo)
	{
		if(tipo == 1)
		{
			mywindow = window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=1200,height=580');
			mywindow.moveTo(10,10);
		}
		else
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=580');
	}

//-->
</script>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : ListaWC.php
	   Fecha de Liberacion : 2010-03-03
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2015-02-04
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite visualizar de forma matricial
	   la programacion General de turnos en cirugia en una fecha determinada y la actualizacion de los estados del paciente
	   de acuerdo a su ubicacion en cirugia.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   
	   .2015-02-04
	   		Se modifico el programa para que permitiera cambiar de fecha con el proposito de modificar estados de cirugias
	   		de dias anteriores.
	   		Se organizo la presentacion de los estados de la cirugia.
	   
	   .2010-09-22
	   		Se modifico el cambio de estado del turno para que solo cambiara si se selecciona el checkbox de cambio que
	   		se adiciono. 
	   
	   .2010-03-03
	   		Release de Version Beta.
	   		
	   		
***********************************************************************************************************************/
function validar_hora($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >24 or ($occur[2] != 0 and $occur[2] != 30))
			return false;
		else
			return true;
	else
		return false;
}
Function horas($pos) 
{
	$khor = (string)((integer)(((integer)($pos) - 1) / 2) * 100 + 30 * (((integer)($pos) - 1) % 2));
	while(strlen($khor) < 4)
		$khor = "0".$khor;
	$horas = substr($khor, 0, 2).":".substr($khor, 2, 2);
	return $horas;
}
function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return 1;
	elseif ($vec1[0] < $vec2[0])
				return -1;
			else
				return 0;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='ListaWC' action='ListaWC.php' method=post>";
	//echo "<meta http-equiv='refresh' content='60;url=/matrix/TCX/reportes/ListaWC.php?empresa=".$empresa."'>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(isset($est))
	{
		for ($i=0;$i<$num;$i++)
		{
			if(isset($ACT[$i]))
			{
				switch ($est[$i])
				{
					case 0:
						//                 0        1       2      3       4
						$query = "SELECT Turpes, Turpep, Turpeq, Turper, Turpea  FROM ".$empresa."_000011 ";
						$query .=  "  where Turtur=".$wimpre[$i][27];
					    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row = mysql_fetch_array($err);
						if($row[0] == "on")
						{
							$query =  " update ".$empresa."_000011 set Turpes='off', Turpep='off', Turpeq='off', Turper='off', Turpea='off', Turubi=''  ";
							$query .=  "  where Turtur=".$wimpre[$i][27];
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ESTADOS : ".mysql_errno().":".mysql_error());
						}
					break;
					case 1:
						//                 0        1       2      3       4
						$query = "SELECT Turpes, Turpep, Turpeq, Turper, Turpea  FROM ".$empresa."_000011 ";
						$query .=  "  where Turtur=".$wimpre[$i][27];
					    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row = mysql_fetch_array($err);
						if($row[1] == "on" or ($row[0] == "off" and $row[1] == "off" and $row[2] == "off" and $row[3] == "off" and $row[4] == "off"))
						{
							$query =  " update ".$empresa."_000011 set Turpes='on', Turpep='off', Turpeq='off', Turper='off', Turpea='off', Turubi=''  ";
							$query .=  "  where Turtur=".$wimpre[$i][27];
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ESTADOS : ".mysql_errno().":".mysql_error());
						}
					break;
					case 2:
						//                 0        1       2      3       4
						$query = "SELECT Turpes, Turpep, Turpeq, Turper, Turpea  FROM ".$empresa."_000011 ";
						$query .=  "  where Turtur=".$wimpre[$i][27];
					    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row = mysql_fetch_array($err);
						if($row[0] == "on" or $row[2] == "on")
						{
							$query =  " update ".$empresa."_000011 set Turpes='off', Turpep='on', Turpeq='off', Turper='off', Turpea='off', Turubi=''  ";
							$query .=  "  where Turtur=".$wimpre[$i][27];
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ESTADOS : ".mysql_errno().":".mysql_error());
						}
					break;
					case 3:
						//                 0        1       2      3       4
						$query = "SELECT Turpes, Turpep, Turpeq, Turper, Turpea  FROM ".$empresa."_000011 ";
						$query .=  "  where Turtur=".$wimpre[$i][27];
					    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row = mysql_fetch_array($err);
						if($row[1] == "on" or $row[3] == "on")
						{
							$query =  " update ".$empresa."_000011 set Turpes='off', Turpep='off', Turpeq='on', Turper='off', Turpea='off', Turubi=''  ";
							$query .=  "  where Turtur=".$wimpre[$i][27];
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ESTADOS : ".mysql_errno().":".mysql_error());
						}
					break;
					case 4:
						//                 0        1       2      3       4
						$query = "SELECT Turpes, Turpep, Turpeq, Turper, Turpea  FROM ".$empresa."_000011 ";
						$query .=  "  where Turtur=".$wimpre[$i][27];
					    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row = mysql_fetch_array($err);
						if($row[2] == "on" or $row[4] == "on")
						{
							$query =  " update ".$empresa."_000011 set Turpes='off', Turpep='off', Turpeq='off', Turper='on', Turpea='off', Turubi=''  ";
							$query .=  "  where Turtur=".$wimpre[$i][27];
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ESTADOS : ".mysql_errno().":".mysql_error());
						}
					break;
					case 5:
						$wubi[$i]=strtoupper($wubi[$i]);
						//                 0        1       2      3       4
						$query = "SELECT Turpes, Turpep, Turpeq, Turper, Turpea  FROM ".$empresa."_000011 ";
						$query .=  "  where Turtur=".$wimpre[$i][27];
					    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row = mysql_fetch_array($err);
						if($row[3] == "on" or $row[4] == "on")
						{
							$query =  " update ".$empresa."_000011 set Turpes='off', Turpep='off', Turpeq='off', Turper='off', Turpea='on', Turubi='".$wubi[$i]."'  ";
							$query .=  "  where Turtur=".$wimpre[$i][27];
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ESTADOS : ".mysql_errno().":".mysql_error());
						}
					break;
				}
			}
		}
	}
	$query = "SELECT Quicod  from ".$empresa."_000012 where Quiest='on' order by quicod ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	$nroqui=$num;
	$wimpre=array();
	if(!isset($wfecha))
		$wfecha = date("Y-m-d");
	echo "<table border=0 align=center>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
	echo "<tr><td align=center id=tipo20>CLINICA LAS AMERICAS</td></tr>";
	echo "<tr><td align=center id=tipo20>PROGRAMA DE CIRUGIA</td></tr>";
	echo "<tr><td align=center id=tipo21>INFORMACION GENERAL Y ACTUALIZACION DE ESTADO Ver. 2015-02-04</td></tr>";
	echo "<tr><td align=left id=tipo21>FECHA : <input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6>&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1' style='vertical-align:middle;'></td></tr>";
	?>
		<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	//]]></script>
	<?php
	echo "<tr><td align=left id=tipo21><input type='submit' value='ACTUALIZAR'></td></tr></table>";
	echo "<br><table border=0 align=center id=tipo00>";
	echo "<tr><td id=tipoT01>HORA</td><td id=tipoT02>Q</td><td id=tipoT00>ESTADO</td><td id=tipoT02>O</td><td id=tipoT03>I</td><td id=tipoT04>PACIENTE</td><td id=tipoT05>TEL/HAB</td><td id=tipoT06>H</td><td id=tipoT07>ED</td><td id=tipoT08>RESPONSABLE</td><td id=tipoT09>CIRUGIA</td><td id=tipoT10>CIRUJANO</td><td id=tipoT11>A</td><td id=tipoT12>U</td><td id=tipoT13>B</td><td id=tipoT14>I</td><td id=tipoT15>ANESTESIOLOGO</td><td id=tipoT16>EQUIPOS</td><td id=tipoT17>COMENTARIOS</td></tr>";
	
	//                  0       1       2      3       4        5      6       7       8       9       10      11      12      13      14      15      16      17      18      19      20      21      22      23      24      25      26      27      28      29      30      31      32      33      34      35      36      37
	$query = "SELECT Turtur, Turqui, Turhin, Turhfi, Turfec, Turndt, Turdoc, Turhis, Turnin, Turnom, Turfna, Tursex, Turins, Turtcx, Turtip, Turtan, Tureps, Turuci, Turbio, Turinf, Turmat, Turmok, Turban, Turtel, Turord, Turcom, Turcir, Turmed, Turequ, Turusg, Turusm, Turest, Turpes, Turpep, Turpeq, Turper, Turpea, Turubi  FROM ".$empresa."_000011 ";
	$query .= " where turfec = '".$wfecha."' ";
    $query .= "   and turest = 'on' ";
    $query .= "   Order by Turqui, Turhin ";
    $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
    $num = mysql_num_rows($err);
    for ($i=0;$i<$num;$i++)
	{
		$row = mysql_fetch_array($err);
		if(strlen($row[1]) < 2)
			$wimpre[$i][0] = "0".$row[1].$row[2];  
		else
			$wimpre[$i][0] = $row[1].$row[2]; 
		$wimpre[$i][1] = $row[1];  //wqui = 1
		$wimpre[$i][2] = $row[2]."-".$row[3];  //wcod = 2
		$wimpre[$i][3] = $row[9]; // wnom = 3
		$wimpre[$i][4] = $row[12]; // wins = 4
		$wimpre[$i][5] = $row[26]; // wcir = 5
		$wimpre[$i][6] = $row[23]; // wtel = 6
		$wimpre[$i][7] = $row[13]; // wtcx = 7
		$wimpre[$i][8] = $row[15]; // wtan = 8
		$wimpre[$i][9] = $row[25]; // wcom = 9
		if($row[17] == "on")
			$wimpre[$i][10] = "S"; // wuci =10
		else
			$wimpre[$i][10] = "N"; // wuci = 10
		if($row[18] == "on")
			$wimpre[$i][11] = "S"; // wbio = 11
		else
			$wimpre[$i][11] = "N"; // wbio = 11
		if($row[19] == "on")
			$wimpre[$i][12] = "S"; // winf = 12
		else
			$wimpre[$i][12] = "N"; // winf = 12
		if($row[24] == "on")
			$wimpre[$i][13] = "S"; // word = 13
		else
			$wimpre[$i][13] = "N"; // word = 13
		$equipos = explode("-", $row[28]);
		$tequ="";
		for ($w=0;$w<count($equipos);$w++)
		{
			if($equipos[$w] != "R" and $equipos[$w] != "S")
				$tequ .= $equipos[$w]."-";
		}
		$wimpre[$i][14] = $tequ;// wequ = 14
		$wimpre[$i][15] = $row[20];// wmat = 15
		$ann=(integer)substr($row[10],0,4)*360 +(integer)substr($row[10],5,2)*30 + (integer)substr($row[10],8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$years=($aa - $ann)/360;
		$months=(($aa - $ann) % 360)/30;
		$days=(($aa - $ann) % 360) % 30;
		//echo $years." ".$months." ".$days."<br>";
		if ($years > 1)
		{
			$wimpre[$i][16] = $years; // weda = 16
		    $wimpre[$i][17] = "A"; // wted = 17
		}
		elseif($months > 1)
			{
				$wimpre[$i][16] = $months;
			    $wimpre[$i][17] = "M";
			}
			else
			{
				$wimpre[$i][16] = $days;
			    $wimpre[$i][17] = "D";
			}
		$query = "SELECT Entdes FROM ".$empresa."_000003  ";
		$query .= " where Entcod = '".$row[16]."'";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		$wimpre[$i][18] = $row1[0]; // weps = 18
		
		$query = "SELECT Mednom FROM ".$empresa."_000010, ".$empresa."_000006 ";
		$query .= "where Mmetur = ".$row[0];
		$query .= "  and Mmemed = Medcod ";
		$query .= "  and Medane = 'off' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		$wimpre[$i][19] = $row1[0]; // wmed = 19
		
		$query = "SELECT Mednom FROM ".$empresa."_000010, ".$empresa."_000006 ";
		$query .= "where Mmetur = ".$row[0];
		$query .= "  and Mmemed = Medcod ";
		$query .= "  and Medane = 'on' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row1 = mysql_fetch_array($err1);
		$wimpre[$i][20] = $row1[0]; // wane = 20
		$wimpre[$i][21] = $row[32];
		$wimpre[$i][22] = $row[33];
		$wimpre[$i][23] = $row[34];
		$wimpre[$i][24] = $row[35];
		$wimpre[$i][25] = $row[36];
		$wimpre[$i][26] = $row[37];
		$wimpre[$i][27] = $row[0];
	}
	usort($wimpre,'comparacion');
	$wquia="";
	
	for ($i=0;$i<$num;$i++)
	{
		echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
		echo "<input type='HIDDEN' name= 'wimpre[".$i."][27]' value='".$wimpre[$i][27]."'>";
	}
	for ($i=0;$i<$num;$i++)
	{
		if($wquia != $wimpre[$i][1])
		{
			if($i > 0)
				echo "<tr><td id=tipoL001 colspan=20>&nbsp</td></tr>";
			$wquia=$wimpre[$i][1];
		}
		if($i % 2 == 0)
		{
			$tipo="tipoD";
			$class="tipo4A";
		}
		else
		{
			$tipo="tipoL";
			$class="tipo4";
		}
		$estados=array();
		if($wimpre[$i][21] == "on")
		{
			$grado="tipoF01";
			$estados[0]="";
			$estados[1]="checked";
			$estados[2]="";
			$estados[3]="";
			$estados[4]="";
			$estados[5]="";
			
		}
		elseif($wimpre[$i][22] == "on")
			{
				$grado="tipoF02";
				$estados[0]="";
				$estados[1]="";
				$estados[2]="checked";
				$estados[3]="";
				$estados[4]="";
				$estados[5]="";
			}
			elseif($wimpre[$i][23] == "on")
				{
					$grado="tipoF03";
					$estados[0]="";
					$estados[1]="";
					$estados[2]="";
					$estados[3]="checked";
					$estados[4]="";
					$estados[5]="";
				}
				elseif($wimpre[$i][24] == "on")
					{
						$grado="tipoF04";
						$estados[0]="";
						$estados[1]="";
						$estados[2]="";
						$estados[3]="";
						$estados[4]="checked";
						$estados[5]="";
					}
					elseif($wimpre[$i][25] == "on")
						{
							$grado="tipoF05";
							$estados[0]="";
							$estados[1]="";
							$estados[2]="";
							$estados[3]="";
							$estados[4]="";
							$estados[5]="checked";
						}
						else
						{
							$grado="tipoF00";
							$estados[0]="checked";
							$estados[1]="";
							$estados[2]="";
							$estados[3]="";
							$estados[4]="";
							$estados[5]="";
						}
		echo "<tr ondblclick='ejecutar(".chr(34)."/MATRIX/tcx/Procesos/CancelTur.php?empresa=".$empresa."&MENSAGE=".$wimpre[$i][27].chr(34).",1)'><td id=".$tipo."01>".$wimpre[$i][2]."</td><td id=".$tipo."02>".$wimpre[$i][1]."</td><td id=".$grado.">";
		echo "<input type='RADIO' name=est[".$i."] value=0 ".$estados[0].">SIN INGRESO<br> ";
		echo "<input type='RADIO' name=est[".$i."] value=1 ".$estados[1].">EN ESPERA<br> ";
		echo "<input type='RADIO' name=est[".$i."] value=2 ".$estados[2].">PREPARACION<br> ";
		echo "<input type='RADIO' name=est[".$i."] value=3 ".$estados[3].">QUIROFANO<br> ";
		echo "<input type='RADIO' name=est[".$i."] value=4 ".$estados[4].">RECUPERACION<br> ";
		echo "<input type='RADIO' name=est[".$i."] value=5 ".$estados[5].">DE ALTA&nbsp;&nbsp;<input type='TEXT' name='wubi[".$i."]' size=15 maxlength=30 value='".$wimpre[$i][26]."' class=tipo3>&nbsp;&nbsp;<input type='checkbox' name='ACT[".$i."]' onclick='enter()'>OK!";
		echo "</td><td id=".$tipo."17>".$wimpre[$i][13]."</td><td id=".$tipo."03>".$wimpre[$i][4]."</td><td id=".$tipo."04>".$wimpre[$i][3]."</td><td id=".$tipo."05>".$wimpre[$i][6]."</td><td id=".$tipo."06>".$wimpre[$i][7]."</td><td id=".$tipo."07>".number_format((double)$wimpre[$i][16],0,'.',',').$wimpre[$i][17]."</td><td id=".$tipo."08>".$wimpre[$i][18]."</td><td id=".$tipo."09>".$wimpre[$i][5]."</td><td id=".$tipo."10>".$wimpre[$i][19]."</td><td id=".$tipo."11>".$wimpre[$i][8]."</td><td id=".$tipo."12>".$wimpre[$i][10]."</td><td id=".$tipo."13>".$wimpre[$i][11]."</td><td id=".$tipo."14>".$wimpre[$i][12]."</td><td id=".$tipo."15>".$wimpre[$i][20]."</td><td id=".$tipo."16>".$wimpre[$i][14]."</td><td id=".$tipo."17><textarea name='wcoma' cols=88 readonly='readonly' rows=2 class=tipo3A>".$wimpre[$i][9]."</textarea></td></tr>";
	}
	echo "</table>";
}
?>
</body>
</html>
