<html>
<head>
  	<title>Generacion de Peticiones de Camas Para Pacientes de Cirugia</title>
<!-- Loading Theme file(s) -->


<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />

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
    	#tipo20{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo21{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    	#tipo00{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoTCK{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:2.5em;}
    	#tipoT00{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:2.5em;}
    	#tipoT01{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:2.5em;}
    	#tipoT02{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:2.5em;}
    	#tipoT03{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:2.5em;}
    	#tipoT04{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:2.5em;}
    	#tipoT05{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoT06{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:2.5em;}
    	#tipoT07{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:3.0em;text-align:center;height:2.5em;}
    	#tipoT08{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:2.5em;}
    	#tipoT09{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:2.5em;}
    	#tipoT10{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:2.5em;}
    	#tipoT11{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:2.5em;}
    	#tipoT12{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:2.5em;}
    	#tipoT13{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:2.5em;}
    	#tipoT14{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:2.5em;}
    	#tipoT15{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:2.5em;}
    	#tipoT16{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:2.5em;}
    	#tipoT17{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:50em;text-align:center;height:2.5em;}
    	
    	#tipoD00{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoDCK{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoD01{color:#000066;background:#99CCFF;font-size:8pt;font-family:Arial;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoDE01{color:#000066;background:#CC99FF;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:2.5em;}
    	#tipoD02{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoD03{color:#000066;background:#99CCFF;font-size:8pt;font-family:Arial;font-weight:bold;width:20em;text-align:center;height:2.5em;}
    	#tipoD04{color:#000066;background:#99CCFF;font-size:8pt;font-family:Arial;font-weight:bold;width:30em;text-align:left;height:2.5em;}
    	#tipoD05{color:#000066;background:#99CCFF;font-size:8pt;font-family:Arial;font-weight:bold;width:7em;text-align:center;height:2.5em;}
    	#tipoD06{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoD07{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoD08{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoD09{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoD10{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoD11{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoD12{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoD13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoD14{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoD15{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoD16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:2.5em;}
    	#tipoD17{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	
    	#tipoL00{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoLCK{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoL01{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoL001{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:1em;}
    	#tipoLE01{color:#000066;background:#CC99FF;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:3em;}
    	#tipoL02{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;width:10em;text-align:center;height:2.5em;}
    	#tipoL03{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;width:20em;text-align:center;height:2.5em;}
    	#tipoL04{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;width:30em;text-align:left;height:2.5em;}
    	#tipoL05{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;width:7em;text-align:center;height:2.5em;}
    	#tipoL06{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoL07{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoL08{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoL09{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoL10{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoL11{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoL12{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoL13{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoL14{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:2.5em;}
    	#tipoL15{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoL16{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:2.5em;}
    	#tipoL17{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;width:40em;text-align:left;height:3em;}
    	
    	#tipoW{color:#000066;background:#99CCFF;font-size:10pt;font-family:Arial;font-weight:bold;width:50em;text-align:center;height:2.5em;vertical-align:middle;}
    	
    </style>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.CamasCX.submit();
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
	
	$.datepicker.regional['esp'] = {
			closeText: 'Cerrar',
			prevText: 'Antes',
			nextText: 'Despues',
			monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
			'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
			'Jul','Ago','Sep','Oct','Nov','Dic'],
			dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
			dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
			dayNamesMin: ['D','L','M','M','J','V','S'],
			weekHeader: 'Sem.',
			dateFormat: 'yy-mm-dd',
			yearSuffix: ''
		};
$.datepicker.setDefaults($.datepicker.regional['esp']);

$(document).ready( function () {

	$("#wfecha").datepicker({
      showOn: "button",
      buttonImage: "/matrix/images/medical/TCX/calendario.jpg",
      buttonImageOnly: true,
      buttonText: "Select date"
    });

});

//-->
</script>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : CamasCX.php
	   Fecha de Liberacion : 2011-08-25
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2015-03-17
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite realizar la peticion de camas
	   para paceintes que van a ser sometidos a intervenciones quirurgicas.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2016-09-20: Jonatan Lopez
			Se registra la historia del paciente en caso de tenerla en el campo historia de la tabla cencam_000003, ademas se vlaida
			si la historia ya tiene solicitudes de cama activas para que no se den registros repetidos.
	   .2015-03-17
			Se modifica el query principal para cambiar UNION ALL por UNION.
			
	   .2015-03-10
			Se modifica el programa para selecionar las empresas de cliame 24 en el query principal.
			
	   .2011-11-15
			Se modifica en el programa para incluir en los tipos de cama la opcion de NO ASIGNAR.
			
	   .2011-10-07
			Se modifica en el programa para grabar en cencam 3  la fecha de la cirugia resaltada.
			
	   .2011-10-06
			Se modifica en el programa la fecha del la tabla 21 del log para que tome la fecha de operacion.
			
	   .2011-10-05
			Se modifica en el programa la fecha de operacion, ya que no estaba calculando el dia siguiente.
	   
	   .2011-09-20
	   		Release de Version Beta.
	   		
***********************************************************************************************************************/

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	include_once("root/comun.php");
	$key = substr($user,2,strlen($user));
	echo "<form name='CamasCX' action='CamasCX.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	
	$wcentral_camas = consultarAliasPorAplicacion($conex, '01', 'CentralCamas');
	$wcencam = consultarAliasPorAplicacion($conex, '01', 'camilleros');
	
	if(isset($ok) and isset($num) and $num > 0)
	{
		$query = "SELECT count(*)  from cencam_000004 where Nombre='CAMAS CX' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		if($row[0] > 0)
		{
			$query = "SELECT count(*)  from ".$empresa."_000021 where Camfec='".$wfecha."' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			if($row[0] == 0)
			{
				$query = "lock table ".$empresa."_000021  LOW_PRIORITY WRITE, cencam_000003 LOW_PRIORITY WRITE ";
				$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVOS  : ".mysql_errno().":".mysql_error());
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000021 (medico, fecha_data, hora_data, Camfec, Camusu, Seguridad) values ('";
				$query .=  "tcx','";
				$query .=  $fecha."','";
				$query .=  $hora."','";
				$query .=  $wfecha."','";
				$query .=  $key."',";
				$query .=  "'C-tcx')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO REGISTRO DE CAMAS : ".mysql_errno().":".mysql_error());
				for ($i=0;$i<$num;$i++)
				{
					if(substr($tcam[$i],strpos($tcam[$i],"-")+1) != "NO ASIGNAR")
					{
						if($wimpre[$i][2] == 0 ){
							$wimpre[$i][2] = "";
						}
						
						$q2 = " SELECT id "
							."    FROM ".$wcencam."_000003"
							."   WHERE Fecha_cumplimiento = '0000-00-00' "
							."     AND Hora_Cumplimiento = '00:00:00' "
							."     AND Anulada           = 'No' "
							."     AND Historia          = '".$wimpre[$i][2]."'"
							."     AND Central           = '".$wcentral_camas."'";
						$res2 = mysql_query($q2,$conex) or die(mysql_error());
						$numsolicitudes = mysql_num_rows($res2);
						
						if($numsolicitudes == 0 or $wimpre[$i][2] == ''){
						
							$wobser="<b>Fecha CX : <font face=arial size=5.2 color=#000066>".$wimpre[$i][11]."</font></b><br><b>Hora Inicio CX : </b>".$wimpre[$i][7]."<br><b>Hora Final CX : </b>".$wimpre[$i][8]."<br><b>Paciente : </b>".$wimpre[$i][4]."<br>"."<b>Historia : </b>".$wimpre[$i][2]."<br>"."<b>Cirugia : </b>".$wimpre[$i][6]."<br>"."<b>Responsable : </b>".$wimpre[$i][5]."<br><b>Telefono : </b>".$wimpre[$i][9];
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert cencam_000003 (medico, fecha_data, hora_data, Origen, Motivo, Habitacion, Observacion, Historia, Destino, Solicito, Ccosto, Camillero, Hora_respuesta, Hora_llegada, Hora_cumplimiento, Anulada, Observ_central, Central, Usu_central, Seguridad) values ('";
							$query .=  "cencam','";
							$query .=  $fecha."','";
							$query .=  $hora."','";
							$query .=  "CAMAS CX','";
							$query .=  "SOLICITUD DE CAMA','";
							//$query .=  "Cama : ".$wimpre[$i][4]."','";
							$query .=  "Tipo Cama : ".substr($tcam[$i],strpos($tcam[$i],"-")+1)."','";
							$query .=  $wobser."','";
							$query .=  $wimpre[$i][2]."','";
							$query .=  "ADMISIONES','".$key."','CIRUGIA','".$tcam[$i]."','00:00:00','00:00:00','00:00:00','No','','CAMAS',";
							$query .=  "'','C-cencam')";
							$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO PETICIONES DE CAMAS : ".mysql_errno().":".mysql_error());
							
						}
					}
				}
				$query = " UNLOCK TABLES";
				$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());	
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td align=center><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td></tr>";
			echo "<tr><td><font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>EL DESTINO CAMAS CX NO HA SIDO CREADO. POR FAVOR HABLE CON SISTEMAS!!!</MARQUEE></FONT></td><tr></table></center>";
		}
	}
	echo "<table border=0 align=center>";
	echo "<td align=center><table border=0 align=center id=tipo5>";
	echo "<tr><td align=center colspan=4><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
	echo "<tr><td align=center colspan=4 id=tipo19> Ver. 2016-09-20</td></tr>";
	echo "<tr><td align=center colspan=4 id=tipo14>GENERACION DE PETICIONES DE CAMAS EN CX</td></tr>";
	if (!isset($wfecha))
	{
		$dateB=strtotime("+1 day");
		$wfecha=strftime("%Y-%m-%d",$dateB);
	}
	$year = (integer)substr($wfecha,0,4);
	$month = (integer)substr($wfecha,5,2);
	$day = (integer)substr($wfecha,8,2);
	$nomdia=mktime(0,0,0,$month,$day,$year);
	$nomdia = strftime("%w",$nomdia);
	$wsw=0;
	switch ($nomdia)
	{
		case 0:
			$diasem = "DOMINGO";
			break;
		case 1:
			$diasem = "LUNES";
			break;
		case 2:
			$diasem = "MARTES";
			break;
		case 3:
			$diasem = "MIERCOLES";
			break;
		case 4:
			$diasem = "JUEVES";
			break;
		case 5:
			$diasem = "VIERNES";
			break;
		case 6:
			$diasem = "SABADO";
			break;
	}

	
	echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA :</b></td>";
	echo "<td bgcolor='#cccccc' align=center>Dia de la Semana<br><b>".$diasem."</b></td>";
	echo "<td bgcolor='#cccccc' align=center valign=center>A&ntilde;o - Mes - Dia<br><input type='TEXT' name='wfecha' id='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6></td></tr>";
	
	echo "<tr><td bgcolor='#cccccc' align=center colspan=4><input type='submit' value='IR'></td></tr>";
	echo "</table><br>";
	
	$query = "SELECT count(*)  from ".$empresa."_000021 where Camfec='".$wfecha."' ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	if($row[0] == 0)
	{
		$TC=array();
		//                  0       1       2    
		$query = "SELECT Tipcod, Tipdes, Tipdef  FROM cencam_000007 ";
		$query .= " where Tipest = 'on' ";
		$query .= "  ORDER BY 3 DESC,1 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$numTC = mysql_num_rows($err);
		for ($i=0;$i<$numTC;$i++)
		{
			$row = mysql_fetch_array($err);
			$TC[$i] = $row[0]."-".$row[1];
		}
		$wimpre=array();
		echo "<table border=0 align=center>";
		echo "<td id=tipoT01>TURNO NRo.</td><td id=tipoT02>DOCUMENTO</td><td id=tipoT00>HISTORIA</td><td id=tipoT02>INGRESO</td><td id=tipoT04>PACIENTE</td><td id=tipoT05>RESPONSABLE</td><td id=tipoT06>CIRUGIA</td><td id=tipoT06>TIPO DE<br>CAMA</td></tr>";
		//                  0       1       2      3       4        5      6       7        8       9      10      11      12
		$query = "SELECT * FROM (SELECT Turtur, Turdoc, Turhis, Turnin, Turnom, Tureps, Turcir, Entdes, Turhin, Turhfi, Turtel, Turuci, Turfec FROM ".$empresa."_000011,".$empresa."_000003 ";
		$query .= " where turfec = '".$wfecha."' ";
		$query .= "   and turest = 'on' ";
		$query .= "   and Turtcx = 'H' ";
		$query .= "   and Tureps = Entcod ";
		$query .= "   UNION ALL";
		$query .= " SELECT Turtur, Turdoc, Turhis, Turnin, Turnom, Tureps, Turcir, Empnom, Turhin, Turhfi, Turtel, Turuci, Turfec FROM ".$empresa."_000011,cliame_000024 ";
		$query .= " where turfec = '".$wfecha."' ";
		$query .= "   and turest = 'on' ";
		$query .= "   and Turtcx = 'H' ";
		$query .= "   and Tureps = Empcod) as t ";
		$query .= "  GROUP BY Turtur ";
		$query .= "  ORDER BY 1 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$wtur = "";
		$k = 0;
		
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($wtur != $row[0])
			{
				$wtur = $row[0];
				
				$wimpre[$k][0] = $row[0]; 
				$wimpre[$k][1] = $row[1];
				$wimpre[$k][2] = $row[2];
				$wimpre[$k][3] = $row[3];
				$wimpre[$k][4] = $row[4]; 
				$wimpre[$k][5] = $row[5]."-".$row[7]; 
				$wimpre[$k][6] = $row[6]; 
				$wimpre[$k][7] = $row[8]; 
				$wimpre[$k][8] = $row[9]; 
				$wimpre[$k][9] = $row[10]; 
				$wimpre[$k][10] = $row[11]; 
				$wimpre[$k][11] = $row[12]; 
				$k++;
			}
		}
		$num = $k;
		echo "<input type='HIDDEN' name= 'num' value='".$k."'>";
		for ($i=0;$i<=$num;$i++)
		{
			//echo "<input type='HIDDEN' name= 'wfecha' value='".$wfecha."'>";
			
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][0]' value='".$wimpre[$i][0]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][1]' value='".$wimpre[$i][1]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][2]' value='".$wimpre[$i][2]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][3]' value='".$wimpre[$i][3]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][4]' value='".$wimpre[$i][4]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][5]' value='".$wimpre[$i][5]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][6]' value='".$wimpre[$i][6]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][7]' value='".$wimpre[$i][7]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][8]' value='".$wimpre[$i][8]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][9]' value='".$wimpre[$i][9]."'>";
			echo "<input type='HIDDEN' name= 'wimpre[".$i."][11]' value='".$wimpre[$i][11]."'>";
		}		
		$i = 0;
		foreach($wimpre as $key=> $value)
		{
			if($i % 2 == 0)
				$tipo="tipoD";
			else
				$tipo="tipoL";
			//                                 Turno                                   Documento                                 Historia                                  Ingreso                                  Paciente                                   Responsable                                CX      
			echo "<tr><td id=".$tipo."01>".$wimpre[$i][0]."</td><td id=".$tipo."02>".$wimpre[$i][1]."</td><td id=".$tipo."00>".$wimpre[$i][2]."</td><td id=".$tipo."05>".$wimpre[$i][3]."</td><td id=".$tipo."17>".$wimpre[$i][4]."</td><td id=".$tipo."03>".$wimpre[$i][5]."</td><td id=".$tipo."04>".$wimpre[$i][6]."</td>";
			echo "<td id=".$tipo."03><select name='tcam[".$i."]'>";
			for ($j=0;$j<$numTC;$j++)
			{
				if($wimpre[$i][10] == "on" and substr($TC[$j],strpos($TC[$i],"-")+1) == "UCI")
					echo "<option selected>".$TC[$j]."</option>";
				else
					echo "<option>".$TC[$j]."</option>";
			}
			echo "</select></td></tr>";
			$i++;
		}
		if($num > 0)
			echo "<tr><td align=center colspan=8 bgcolor=#999999><b>GRABAR</b><input type='checkbox' name='ok' onclick='enter()'></td></tr>";
		echo "</table>";
	}
	else
	{
		echo "<center><table border=0 aling=center>";
		echo "<tr><td id=tipoW><IMG SRC='/matrix/images/medical/root/FELIZ.PNG' style='vertical-align:middle;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LA PETICION DE CAMAS PARA EL DIA : ".$wfecha." YA HA SIDO GENERADA !!!</td><tr></table></center>";
	}
}
?>
</body>
</html>
