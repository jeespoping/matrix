<html>
<head>
  	<title>MATRIX Registro Intraoperatorio de Anestesia Ver.2018-05-22</title>
  	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
	<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>
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
    	.saltopagina{page-break-after: always}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo3A{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13A{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:normal;text-align:justify;border-style:solid;border-width:1px;}
    	#tipo13B{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:normal;text-align:justify;border-style:solid;border-width:1px;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	
    	#tipoT00{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:left;border-width:4px;border-collapse:collapse;}
    	#tipoT01{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:5em;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoT02{color:#000066;background:#DDDDDD;font-size:11pt;font-family:Tahoma;font-weight:bold;text-align:center;height:3em;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoT02A{color:#000066;background:#E8EEF7;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;height:3em;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoT02B{color:#000066;background:#C3D9FF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;height:3em;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoT02C{color:#000066;background:#999999;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoT02D{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;height:1em;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoT03{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:right;height:2em;border-style:solid;border-width:1px;border-collapse:collapse;}
    	
    	#tipoG00{color:#000066;background:#999999;font-size:7pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG01{color:#000066;background:#CCCCCC;font-size:7pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG02{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:bold;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG02A{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:bold;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03{color:#000066;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03D{color:#000066;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	
    	#tipoG03B{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03BD{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03BH{color:#000066;background:#999999;font-size:12pt;font-family:Arial;font-weight:bold;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03BX{color:#000066;background:#FFDDDD;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-width:1px;height:2em;border-collapse:collapse;}
    	#tipoG03BHD{color:#000066;background:#999999;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	
    	#tipoG03DN{color:#FF0000;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03WDN{color:#FF0000;background:#E8EEF7;font-size:12pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03BDN{color:#FF0000;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03BHDN{color:#FF0000;background:#999999;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	
    	#tipoG03W{color:#000066;background:#E8EEF7;font-size:12pt;font-family:Tahoma;font-weight:normal;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG03WD{color:#000066;background:#E8EEF7;font-size:12pt;font-family:Tahoma;font-weight:normal;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG04W{color:#000066;background:#C3D9FF;font-size:12pt;font-family:Tahoma;font-weight:normal;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG04WD{color:#000066;background:#C3D9FF;font-size:12pt;font-family:Tahoma;font-weight:normal;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG05W{color:#000066;background:#DDDDDD;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG05WD{color:#000066;background:#DDDDDD;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG06W{color:#000066;background:#999999;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG06WB{color:#0B610B;background:#999999;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG06WR{color:#B40404;background:#999999;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG07W{color:#000066;background:#EFFBEF;font-size:12pt;font-family:Tahoma;font-weight:normal;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}

    	
    	#tipoG05{color:#000066;background:#DDDDDD;font-size:12pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoG05D{color:#000066;background:#DDDDDD;font-size:12pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	
    	#tipoVerde1{color:#0B610B;background:#DDDDDD;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoRojo1{color:#B40404;background:#DDDDDD;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	
    	#tipoVerde2{color:#0B610B;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	#tipoRojo2{color:#B40404;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;border-style:solid;border-width:1px;border-collapse:collapse;}
    	
    	#tipoT02H{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;height:1em;border-style:solid;border-width:1px;border-collapse:collapse;}
    	
    	.tipoTABLE{font-family:Arial;border-style:none;border-collapse:collapse;}
    	.tipoTABLE1{font-family:Arial;border-style:solid;border-collapse:collapse;}
    	.tipo3GRID{color:#E8EEF7;background:#E8EEF7;font-size:1pt;font-family:Arial;font-weight:bold;text-align:center;border-style:none;display:none;}
    	
    	.tipoL02M{color:#000066;background:#E8EEF7;font-size:8.5pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	.tipoL02MO{color:#000066;background:#E0E0E0;font-size:8.5pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	.tipoL02MW{color:#000066;background:#CCCCCC;font-size:8.5pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	#tipoL01{color:#000000;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;width:15em;text-align:center;height:2em;}
    	#tipoL01C{color:#000000;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;width:15em;text-align:center;height:2em;}
    	#tipoL02C{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;width:50em;text-align:center;height:2em;}
    	#tipoL03C{color:#000000;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;width:250em;text-align:center;height:2em;}
    	#tipoL04C{color:#000000;background:#C3D9FF;font-size:12pt;font-family:Arial;font-weight:bold;width:250em;text-align:center;height:2em;}
    	#tipoL01A{color:#000000;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;width:15em;align:center;text-align:center;height:5em;}
    	#tipoL02A{color:#000000;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;width:15em;align:center;text-align:center;height:5em;}
    	#tipoL01B{color:#000000;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;width:15em;align:center;text-align:center;}
    	#tipoL02B{color:#000000;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;width:15em;align:center;text-align:center;}
    	#tipoL03{color:#000066;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:bold;width:90em;text-align:center;height:2em;}
    	#tipoL03M{color:#FF0000;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:bold;width:90em;text-align:center;height:2em;}
    	#tipoL03X{color:#FFFFFF;background:#FF0000;font-size:12pt;font-family:Arial;font-weight:bold;width:90em;text-align:center;height:2em;}
    	#tipoL04{color:#000000;background:#E8EEF7;font-size:12pt;font-family:Arial;font-weight:bold;width:75em;text-align:center;height:2em;}
    	#tipoL04A{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;width:75em;text-align:center;height:2em;}
    	#tipoL05{color:#000000;background:#C3D9FF;font-size:8pt;font-family:Arial;font-weight:bold;width:25em;text-align:center;height:2em;}
    	#tipoL06{color:#000066;background:#999999;font-size:12pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;cursor: hand;cursor: pointer;}
    	#tipoL07{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;width:90em;text-align:left;height:1em;}
    	#tipoL07A{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;width:90em;text-align:left;height:1em;}
    	#tipoL07B{color:#000066;background:#CCCCCC;font-size:8pt;font-family:Arial;font-weight:bold;width:90em;text-align:left;height:1em;}
    	#tipoL08{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}
    	#tipoL09{color:#CC0000;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}
    	#tipoL09A{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;vertical-align:middle;}
    	#tipoFILA{color:#FFFFFF;background:#FFFFFF;width:390em;}
    	#tipoLGOK{color:#000000;background:#C3D9FF;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	#tipoLGOL{color:#000000;background:#CC99FF;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	#tipoLIN1{color:#000066;background:#E8EEF7;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}
    	#tipoLIN2{color:#000066;background:#C3D9FF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}
    	
    	
    	
    </style>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.AnestesiaI.submit();
	}
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13)) event.returnValue = false;
	}
	function teclado1()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function salto1()
	{
		window.print();
	}
	function Graficar()
	{
		$('#tablaresultados').LeerTablaAmericas(
		{ 
				empezardesdefila: 1,
				titulo : 'Intraoperatorio de Anestesia ' ,
				tituloy: 'Unidades',
				divgrafica: 'amcharts',
				filaencabezado : [0,1],
				datosadicionales : [2,3,4,5,6,7,8,9,10,11],
				tipografico : 'smoothedLine'
		});
	}
	function toggleDisplay(id)	
	{
		if (id.style.display=="none")
		{
			id.style.display="";
		}
		else 
		{
			id.style.display="none";
		}
	}
	
//-->
</script>
<?php
include_once("conex.php");
$wemp_pmla = $_REQUEST['wemp_pmla'];
/*
*********************************************************************************************************************  
[DOC]
	   PROGRAMA : AnestesiaI.php
	   Fecha de Liberacion : 2017-01-10
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2018-05-22
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica en donde puede registrar los datos
	   hemodinamicos de anestesia de un paciente en el quirofano.
	   
	   REGISTRO DE MODIFICACIONES :
	   2021-11-16 Daniel CB.
			- Archivo no maneja variable wemp_pmla, no aparece en la root_000021 y no se llama desde otro archivo 
			  por lo cual no se puede trabajar con multiempresa

	   .2018-05-22
	   		Release de Version Beta.
			   
	   
[*DOC]   		
**********************************************************************************************************************
*/

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,strpos($chain,"-")+1);
}

function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$/";
	// if (ereg($decimal,$chain,$occur))
	if (preg_match($decimal,$chain))
	{
		// if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			// return false;
		// else
			return true;
	}
	else
	{
		return false;
	}
		
}

function validar2($chain)
{
	// Funcion que permite validar la estructura de un numero Entero
	$regular="/^(\+|-)?([[:digit:]]+)$/";
	// if (ereg($regular,$chain,$occur))
	if (preg_match($regular,$chain))
	{
		// if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			// return false;
		// else
			return true;
	}
	else
	{
		return false;
	}	
}

function validar3($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="/^([=a-zA-Z0-9' 'ñÑ@?\/*#-.:;_<>])+$/";
	return (preg_match($regular,$chain));
}

function validar4($chain)
{
	// Funcion que permite validar la estructura de un campo Hora Especial
	$hora="/^([[:digit:]]{1,2}):([[:digit:]]{1,2})$/";
	// if(ereg($hora,$chain,$occur))
	if(preg_match($hora,$chain))
	{
		// if($occur[1] < 0 or $occur[1] > 24 or $occur[2] < 0 or $occur[2] > 55)
			// return false;
		// else
			return true;
	}
	else
		return false;
}

function VAL_DAT($key,$conex,$whorai,$wpas,$wpad,$wpam,$wpvc,$wfca,$wfre,$wso2,$wtem,$wglu,$wdiu,$wcap,$wacc,&$werr,&$e)
{
	if(!validar4($whorai))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA HORA DE LA CIRUGIA";
		$wsw=1;
	}
	if(!validar2($wpas) or $wpas > 500)
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA PRESION ARTERIAL SISTOLICA O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if(!validar2($wpad) or $wpad > 300)
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA PRESION ARTERIAL DISTOLICA O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if($wpam != "" and (!validar2($wpam) or $wpam > 150))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA PRESION ARTERIAL MEDIA O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if(!validar2($wfca) or $wfca > 400)
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA FRECUENCIA CARDIACA O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if($wfre != "" and (!validar2($wfre) or $wfre > 150))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA FRECUENCIA RESPIRATORIA O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if(!validar2($wso2) or $wso2 > 101)
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA SATURACION DE OXIGENO O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if($wtem != "" and (!validar1($wtem) or $wtem < 19 or $wtemp > 46))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA TEMPERATURA O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if($wglu != "" and (!validar2($wglu) or $wglu > 601))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA GLUCOMETRIA O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if($wdiu != "" and (!validar2($wdiu) or $wdiu > 10000))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA DIURESIS O ESTA FUERA DE RANGO";
		$wsw=1;
	}
	if($wcap != "" and (!validar2($wcap) or $wcap > 71))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO LA CAPNOGRAFIA O ESTA FUERA DE RANGO";
		$wsw=1;
	}

	if($e == -1)
		return true;
	else
		return false;
}

function ING_DAT($wtur,$whis,$wing,$wfechai,$whorai,$key,$conex,$wpas,$wpad,$wpam,$wpvc,$wfca,$wfre,$wso2,$wtem,$wglu,$wdiu,$wcap,$wacc,&$werr,&$e)
{
	global $empresa;
	$query  = "select count(*) from ".$empresa."_000223 ";
	$query .= "  where ".$empresa."_000223.Reatur = ".$wtur;
	$query .= "    and ".$empresa."_000223.Reahis = '".$whis."' ";
	$query .= "    and ".$empresa."_000223.Reaing = '".$wing."' ";
	$query .= "    and ".$empresa."_000223.Reafec = '".$wfechai."' ";
	$query .= "    and ".$empresa."_000223.Reahor = '".$whorai."' ";
	$query .= "    and ".$empresa."_000223.Reausu = '".$key."' ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	if($row[0] == 0)
	{
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000223 (medico,fecha_data,hora_data,Reatur,Reahis,Reaing,Reafec,Reahor,Reausu,Reapas,Reapad,Reapam,Reapvc,Reafca,Reafre,Reaso2,Reatem,Reaglu,Readiu,Reacap,Reaacc,Reaest,Seguridad) values ('";
		$query .=  $empresa."','";
		$query .=  $fecha."','";
		$query .=  $hora."',";
		$query .=  $wtur.",'";
		$query .=  $whis."','";
		$query .=  $wing."','";
		$query .=  $wfechai."','";
		$query .=  $whorai."','";
		$query .=  $key."','";
		$query .=  $wpas."','";
		$query .=  $wpad."','";
		$query .=  $wpam."','";
		$query .=  $wpvc."','";
		$query .=  $wfca."','";
		$query .=  $wfre."','";
		$query .=  $wso2."','";
		$query .=  $wtem."','";
		$query .=  $wglu."','";
		$query .=  $wdiu."','";
		$query .=  $wcap."','";
		$query .=  $wacc."','on',";
		$query .=  "'C-".$empresa."')";
		$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS : ".mysql_errno().":".mysql_error());	
		$e=$e+1;
		$werr[$e]="OK! DATOS GRABADOS";
		return true;
	}
	else
	{
		$query =  " update ".$empresa."_000223 set Reapas = '".$wpas."',";
		$query .=  "  Reapad = '".$wpad."',";
		$query .=  "  Reapam = '".$wpam."',";
		$query .=  "  Reapvc = '".$wpvc."',";
		$query .=  "  Reafca = '".$wfca."',";
		$query .=  "  Reafre =  ".$wfre.",";
		$query .=  "  Reaso2 = '".$wso2."',";
		$query .=  "  Reatem = '".$wtem."',";
		$query .=  "  Reaglu = '".$wglu."',"; 
		$query .=  "  Readiu = '".$wdiu."',"; 
		$query .=  "  Reacap = '".$wcap."',"; 
		$query .=  "  Reaacc = '".$wacc."' "; 
		$query .=  "  where Reatur=".$wtur." and Reahis='".$whis."' and Reaing='".$wing."' and Reafec='".$wfechai."' and Reahor='".$whorai."' and Reausu='".$key."'";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS : ".mysql_errno().":".mysql_error());
		$e=$e+1;
		$werr[$e]="OK! DATOS ACTUALIZADOS";
		return true;
	}
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='AnestesiaI' action='AnestesiaI.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	
	if(!isset($wfecha))
		$wfecha=date("Y-m-d");
	if(!isset($wtur) or !isset($whis) or !isset($wing) or $whis == "" or $wing == "")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2><b>CLINICA LAS AMERICAS<b></td></tr>";
		echo "<tr><td align=center colspan=2>REGISTRO INTRAOPERATORIO DE ANESTESIA</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nro. de Historia</td>";
		if(isset($whis))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=12 value=".$whis." maxlength=12></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nro. de Ingreso</td>";
		if(isset($wing))
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wing' size=12 value=".$wing." maxlength=12></td></tr>";
		else
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wing' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nro. de Turno Quirurgico</td>";
		if(isset($wtur))
		{
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtur' size=12 value=".$wtur." maxlength=12></td></tr>";
		}
		else 
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtur' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{	
		echo "<input type='HIDDEN' name='whis' value='".$whis."'>";	
		echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
		echo "<input type='HIDDEN' name='wtur' value='".$wtur."'>";
		$query = "select oriced,oritid from root_000037 ";
		$query .= " where Orihis = '".$whis."'";
		$query .= "   and Oriing = '".$wing."'";
		$query .= "   and oriori = '".$wemp_pmla."'";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wcedula = $row[0];
			$wtipodoc = $row[1];
		}
					
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,movhos_000016,movhos_000018,movhos_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and  pacced = oriced ";
		$query .= "   and  pactid = oritid ";
		$query .= "   and oriori = '".$wemp_pmla."'";
		$query .= "   and inghis = orihis ";
		$query .= "   and  inging = oriing ";
		$query .= "   and ubihis = inghis "; 
		$query .= "   and ubiing = inging ";
		$query .= "   and ccocod = ubisac ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$wsex="M";
		$sexo="MASCULINO";
		if($row[5] == "F")
		{
			$sexo="FEMENINO";
			$wsex="F";
		}
		$ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
		$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		$ann1=($aa - $ann)/360;
		$meses=(($aa - $ann) % 360)/30;
		if ($ann1<1)
		{
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
		}
		else
		{
			$dias1=(($aa - $ann) % 360) % 30;
			$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		}
		$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		//$whis=$row[6];
		if(!isset($wing))
			$wing=$row[7];
		$dia=array();
		$dia["Mon"]="Lun";
		$dia["Tue"]="Mar";
		$dia["Wed"]="Mie";
		$dia["Thu"]="Jue";
		$dia["Fri"]="Vie";
		$dia["Sat"]="Sab";
		$dia["Sun"]="Dom";
		$mes["Jan"]="Ene";
		$mes["Feb"]="Feb";
		$mes["Mar"]="Mar";
		$mes["Apr"]="Abr";
		$mes["May"]="May";
		$mes["Jun"]="Jun";
		$mes["Jul"]="Jul";
		$mes["Aug"]="Ago";
		$mes["Sep"]="Sep";
		$mes["Oct"]="Oct";
		$mes["Nov"]="Nov";
		$mes["Dec"]="Dic";
		$fechal=strftime("%a %d de %b del %Y");
		$fechal=$dia[substr($fechal,0,3)].substr($fechal,3);
		$fechal=substr($fechal,0,10).$mes[substr($fechal,10,3)].substr($fechal,13);
		$color="#dddddd";
		$color1="#C3D9FF";
		$color2="#E8EEF7";
		$color3="#CC99FF";
		$color4="#99CCFF";
		$wintitulo="Historia:".$row[6]." Ingreso:".$row[7]." Paciente:".$wpac;
		$Hgraficas=" |";
		echo "<table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/matrix/images/medical/root/lmatrix.jpg' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table><br>";
		$query  = "select Turfec from tcx_000011 ";
		$query .= "  where Turtur = ".$wtur;
		$query .= "    and Turhis = '".$whis."'";
		$query .= "    and Turnin = '".$wing."'";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			$query  = "select Reafec,Reahor from ".$empresa."_000223 ";
			$query .= "  where ".$empresa."_000223.Reatur = ".$wtur;
			$query .= "    and ".$empresa."_000223.Reahis = '".$whis."' ";
			$query .= "    and ".$empresa."_000223.Reaing = '".$wing."' ";
			$query .= "  Order by id ";
			$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num2 = mysql_num_rows($err2);
			if($num2 > 0)
			{
				$row2 = mysql_fetch_array($err2);
				$wfechai = $row2[0];
				$whor = substr($row2[1],0,2);
				$wmin = substr($row2[1],3,2);
			}
			else
				$wfechai = $row1[0];
			if(isset($ok))
			{
				switch ($ok) 
				{
					case 0:
						$wpas = "";
						$wpad = "";
						$wpam = "";
						$wpvc = "";
						$wfca = "";
						$wfre = "";
						$wso2 = "";
						$wtem = "";
						$wglu = "";
						$wdiu = "";
						$wcap = "";
						$wacc = "";
					break;
					case 1:
						$werr=array();
						$e=-1;
						if(VAL_DAT($key,$conex,$whorai,$wpas,$wpad,$wpam,$wpvc,$wfca,$wfre,$wso2,$wtem,$wglu,$wdiu,$wcap,$wacc,$werr,$e))
							if(ING_DAT($wtur,$whis,$wing,$wfechaw,$whorai,$key,$conex,$wpas,$wpad,$wpam,$wpvc,$wfca,$wfre,$wso2,$wtem,$wglu,$wdiu,$wcap,$wacc,$werr,$e))
							{
								$wpas = "";
								$wpad = "";
								$wpam = "";
								$wpvc = "";
								$wfca = "";
								$wfre = "";
								$wso2 = "";
								$wtem = "";
								$wglu = "";
								$wdiu = "";
								$wcap = "";
								$wacc = "";
								unset($ok);
							}							
					break;
					case 2:
						$query  = "select Reafec,Reahor,Reapas,Reapad,Reapam,Reapvc,Reafca,Reafre,Reaso2,Reatem,Reaglu,Readiu,Reacap,Reaacc,".$empresa."_000223.id,Medno1,Medno2,Medap1,Medap2  from ".$empresa."_000223,".$empresa."_000048 ";
						$query .= "  where ".$empresa."_000223.Reatur = ".$wtur;
						$query .= "    and ".$empresa."_000223.Reahis = '".$whis."' ";
						$query .= "    and ".$empresa."_000223.Reaing = '".$wing."' ";
						$query .= "    and ".$empresa."_000223.Reausu = Meduma ";
						$query .= "  Order by ".$empresa."_000223.id desc ";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$num = mysql_num_rows($err);
						if($num > 0)
						{
							$row = mysql_fetch_array($err);
							$wpas = $row[2];
							$wpad = $row[3];
							$wpam = $row[4];
							$wpvc = $row[5];
							$wfca = $row[6];
							$wfre = $row[7];
							$wso2 = $row[8];
							$wtem = $row[9];
							$wglu = $row[10];
							$wdiu = $row[11];
							$wcap = $row[12];
							$wacc = $row[13];
						}
					break;
				}		
			}
			if(isset($ok1))
			{
				//                   0      1      2      3      4      5      6      7      8     9      10     11     12     13
				$query  = "select Reafec,Reahor,Reapas,Reapad,Reapam,Reapvc,Reafca,Reafre,Reaso2,Reatem,Reaglu,Readiu,Reaacc,Reacap from ".$empresa."_000223 ";
				$query .= "  where id = ".$ok1;
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$wfechaw = $row[0];
					$whorai = $row[1];
					$wpas = $row[2];
					$wpad = $row[3];
					$wpam = $row[4];
					$wpvc = $row[5];
					$wfca = $row[6];
					$wfre = $row[7];
					$wso2 = $row[8];
					$wtem = $row[9];
					$wglu = $row[10];
					$wdiu = $row[11];
					$wacc = $row[12];
					$wcap = $row[13];
				}
			}
			//                  0      1      2       3      4      5      6      7      8     9      10     11     12     13
			$query  = "select Reafec,Reahor,Reausu,Reapas,Reapad,Reapam,Reapvc,Reafca,Reafre,Reaso2,Reatem,Reaglu,Readiu,Reacap from ".$empresa."_000223 ";
			$query .= "  where ".$empresa."_000223.Reatur = ".$wtur;
			$query .= "    and ".$empresa."_000223.Reahis = '".$whis."' ";
			$query .= "    and ".$empresa."_000223.Reaing = '".$wing."' ";
			$query .= "  Order by id ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				echo "<table border=1 id='tablaresultados' class=tipo3GRID>";
				echo "<tr><td>FECHA-HORA</td><td>P. SISTOLE</td><td>P. DISTOLE</td><td>P. MEDIA</td><td>P. VENOSA CENTRAL</td><td>F. CARDIACA</td><td>F. RESPIRATORIA</td><td>SATURACION O2</td><td>TEMPERATURA</td><td>GLUCOMETRIA</td><td>DIURESIS</td><td>CAPNOGRAFIA</td></tr>";
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					echo "<tr><td>".$row[0]."-".$row[1]."</td><td>".$row[3]."</td><td>".$row[4]."</td><td>".$row[5]."</td><td>".$row[6]."</td><td>".$row[7]."</td><td>".$row[8]."</td><td>".$row[9]."</td><td>".$row[10]."</td><td>".$row[11]."</td><td>".$row[12]."</td><td>".$row[13]."</td></tr>";
				}
				echo "</table>";
			}
			
			
			//                  0      1      2       3      4      5      6      7      8     9      10     11     12     13                       14   15     16     17     18     19
			$query  = "select Reafec,Reahor,Reausu,Reapas,Reapad,Reapam,Reapvc,Reafca,Reafre,Reaso2,Reatem,Reaglu,Readiu,Reaacc,".$empresa."_000223.id,Medno1,Medno2,Medap1,Medap2,Reacap  from ".$empresa."_000223,".$empresa."_000048 ";
			$query .= "  where ".$empresa."_000223.Reatur = ".$wtur;
			$query .= "    and ".$empresa."_000223.Reahis = '".$whis."' ";
			$query .= "    and ".$empresa."_000223.Reaing = '".$wing."' ";
			$query .= "    and ".$empresa."_000223.Reausu = Meduma ";
			$query .= "  Order by ".$empresa."_000223.id desc ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			
			if(isset($werr) and isset($e) and $e > -1)
			{
				$color4="#CC99FF";
				$color5="#99CCFF";
				echo "<br><center><table border=0 aling=center id=tipo2>";
				for ($i=0;$i<=$e;$i++)
					if(substr($werr[$i],0,3) == "OK!")
						echo "<tr><td align=center bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color5."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
					else
						echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
				echo "</table><br></center>";
			}
			
			echo "<center><table border=1>";
			echo "<tr><td colspan=4><button type='button' onclick='toggleDisplay(seg);Graficar();'><IMG SRC='/matrix/images/medical/root/chart.png'></button></td></tr>";
			echo "<tr id='seg' style='display: none'><td colspan=4><table align='center'>";
			echo "<tr>";
			echo "<td><div id='amcharts' style='width:1300px; height:600px;'></div></td>";
			echo "</tr>";
			echo "</table>";
			echo "</td></tr>";
			echo "</table></center>";
			
			
			echo "<br><center><table border=1 align=center class=tipoTABLE1>";
			echo "<tr><td id=tipoT02C colspan=17>CLINICA LAS AMERICAS<BR>REGISTRO INTRAOPERATORIO DE ANESTESIA</td></tr>";
			echo "<tr>";
			echo "<td id=tipoT02 colspan=6>Numero de Turno CX : <br>".$wtur."</td>";
			echo "<td id=tipoT02 colspan=5 valign=center>Fecha de Inicio CX :<br> <input type='TEXT' name='wfechai' size=10 maxlength=10 id='wfechai' readonly='readonly' value='".$wfechai."' class=tipo6></td>";
			if($num == 0)
			{
				if(!isset($whor))
				{
					$whor = date('H');
					$wmin = date('i');
					$wmin = ((integer)($wmin / 5) + 1) * 5;
				}
				echo "<td id=tipoT02 colspan=6>Hora de Inicio Anestesia :<br> ";
				echo "<select name='whor' id=tipo1>";
				for ($k=0;$k<24;$k++)
				{
					if(strlen($k) < 2)
						$H = "0".$k;
					else
						$H = $k;
					if(isset($whor) and $whor == $H)
						echo "<option selected>".$H."</option>";
					else
						echo "<option>".$H."</option>";
				}
				echo "</select>";
				echo "<select name='wmin' id=tipo1>";
				$M=0;
				for ($k=0;$k<12;$k++)
				{
					if(strlen($M) < 2)
						$M = "0".$M;
					if(isset($wmin) and $wmin == $M)
						echo "<option selected>".$M."</option>";
					else
						echo "<option>".$M."</option>";
					$M = $M + 5;
				}
				echo "</select><input type='submit' value='IR'>&nbsp; <b>VERIFIQUE LA HORA DE INICIO DE LA ANESTESIA</b></td></tr>";
				echo "<tr>";
				echo "<td id=tipoL01C>Fecha</td>";
				echo "<td id=tipoL01C>Hora</td>";
				echo "<td id=tipoL01C>Presion<br>Arterial<br>Sistolica</td>";
				echo "<td id=tipoL01C>Presion<br>Arterial<br>Diastolica</td>";
				echo "<td id=tipoL01C>Presion<br>Arterial<br>Media</td>";
				echo "<td id=tipoL01C>Presion<br>Venosa<br>Central</td>";
				echo "<td id=tipoL01C>Frecuencia<br>Cardiaca</td>";
				echo "<td id=tipoL01C>Frecuencia<br>Respiratoria</td>";
				echo "<td id=tipoL01C>Saturacion de <br>Oxigeno</td>";
				echo "<td id=tipoL01C>Temperatura</td>";
				echo "<td id=tipoL01C>Glucometria</td>";
				echo "<td id=tipoL01C>Diuresis</td>";
				echo "<td id=tipoL01C>Capnografia</td>";
				echo "<td id=tipoL01C>Acciones</td>";
				echo "<td id=tipoL01C>Medico</td>";
				echo "<td id=tipoL01C colspan=3>Operacion</td>";
				echo "<tr>";
				$wfechaw = $wfechai;
				echo "<td id=tipoLIN1><input type='TEXT' name='wfechaw' size=5 maxlength=5 value='".$wfechaw."' class=tipo3 readonly=readonly></td>";
				$whorai = $whor.":".$wmin;
				echo "<td id=tipoLIN1><input type='TEXT' name='whorai' size=5 maxlength=5 value='".$whorai."' class=tipo3 readonly=readonly></td>";
				if(isset($wpas))
					echo "<td id=tipoLIN1><input type='TEXT' name='wpas' size=3 maxlength=3 value='".$wpas."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wpas' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wpad))
					echo "<td id=tipoLIN1><input type='TEXT' name='wpad' size=3 maxlength=3 value='".$wpad."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wpad' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wpam))
					echo "<td id=tipoLIN1><input type='TEXT' name='wpam' size=3 maxlength=3 value='".$wpam."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wpam' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";	
				if(isset($wpvc))
					echo "<td id=tipoLIN1><input type='TEXT' name='wpvc' size=3 maxlength=3 value='".$wpvc."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wpvc' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wfca))
					echo "<td id=tipoLIN1><input type='TEXT' name='wfca' size=3 maxlength=3 value='".$wfca."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wfca' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wfre))
					echo "<td id=tipoLIN1><input type='TEXT' name='wfre' size=2 maxlength=2 value='".$wfre."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wfre' size=2 maxlength=2 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wso2))
					echo "<td id=tipoLIN1><input type='TEXT' name='wso2' size=3 maxlength=3 value='".$wso2."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wso2' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wtem))
					echo "<td id=tipoLIN1><input type='TEXT' name='wtem' size=4 maxlength=4 value='".$wtem."' class=tipo3 onKeypress='teclado1(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wtem' size=4 maxlength=4 class=tipo3 onKeypress='teclado1(event)'></td>";
				if(isset($wglu))
					echo "<td id=tipoLIN1><input type='TEXT' name='wglu' size=3 maxlength=3 value='".$wglu."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wglu' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wdiu))
					echo "<td id=tipoLIN1><input type='TEXT' name='wdiu' size=3 maxlength=3 value='".$wdiu."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wdiu' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wcap))
					echo "<td id=tipoLIN1><input type='TEXT' name='wcap' size=3 maxlength=3 value='".$wcap."' class=tipo3 onKeypress='teclado(event)'></td>";
				else
					echo "<td id=tipoLIN1><input type='TEXT' name='wcap' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
				if(isset($wacc))
					echo "<td id=tipoLIN1><textarea name='wacc' cols=30 rows=3 class='tipo3'>".$wacc."</textarea></td>";
				else
					echo "<td id=tipoLIN1><textarea name='wacc' cols=30 rows=3 class='tipo3'></textarea></td>";
				$query  = "select Medno1,Medno2,Medap1,Medap2 from ".$empresa."_000048 ";
				$query .= "  where Meduma = '".$key."'";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);	
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					echo "<td id=tipoLIN1>".$row1[0]." ".$row1[1]." ".$row1[2]." ".$row1[3]."</td>";
				}
				else
					echo "<td id=tipoLIN1>USUARIO NO REGISTRADO EN MATRIX !!!</td>";
				//echo "<td id=tipoLIN2><input type='RADIO' name=ok value=0 onclick='enter()'>Iniciar</td><td id=tipoLIN1><input type='RADIO' name=ok value=1 onclick='enter()'>Grabar</td><td id=tipoLIN2><input type='RADIO' name=ok value=2 onclick='enter()'>Repetir</td></tr>";
				echo "<td id=tipoLIN2><input type='RADIO' name=ok value=0 onclick='enter()'>Iniciar</td><td id=tipoLIN1 colspan=2><input type='RADIO' name=ok value=1 onclick='enter()'>Grabar</td></tr>";
			}
			else
			{
				echo "<td id=tipoT02 colspan=6>Hora de Inicio CX :<br> <input type='TEXT' name='whor' size=2 maxlength=2 id='whor' readonly='readonly' value='".$whor."' class=tipo6>:<input type='TEXT' name='wmin' size=2 maxlength=2 id='wmin' readonly='readonly' value='".$wmin."' class=tipo6></td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($i == 0)
					{
						if(!isset($ok1))
						{
							$wfechaw = $row[0];
							$whori = substr($row[1],0,2);
							$wmini = substr($row[1],3,2);
							if($wmini == 55)
							{
								$wmini = "00";
								$whori = $whori + 1;
								if($whori > 23)
								{
									$whori = "00";
									$wfecham1= strtotime ( '+1 day' , strtotime ( $wfechaw ) ) ;
									$wfechaw = date ( 'Y-m-d' , $wfecham1 );
								}
								if(strlen($whori) < 2)
									$whori = "0".$whori;
							}
							else
							{
								$wmini = $wmini + 5;	
								if(strlen($wmini) < 2)
									$wmini = "0".$wmini;
							}
							$whorai = $whori.":".$wmini;
						}
						
						echo "<tr>";
						echo "<td id=tipoL01C>Fecha</td>";
						echo "<td id=tipoL01C>Hora</td>";
						echo "<td id=tipoL01C>Presion<br>Arterial<br>Sistolica</td>";
						echo "<td id=tipoL01C>Presion<br>Arterial<br>Diastolica</td>";
						echo "<td id=tipoL01C>Presion<br>Arterial<br>Media</td>";
						echo "<td id=tipoL01C>Presion<br>Venosa<br>Central</td>";
						echo "<td id=tipoL01C>Frecuencia<br>Cardiaca</td>";
						echo "<td id=tipoL01C>Frecuencia<br>Respiratoria</td>";
						echo "<td id=tipoL01C>Saturacion de <br>Oxigeno</td>";
						echo "<td id=tipoL01C>Temperatura</td>";
						echo "<td id=tipoL01C>Glucometria</td>";
						echo "<td id=tipoL01C>Diuresis</td>";
						echo "<td id=tipoL01C>Capnografia</td>";
						echo "<td id=tipoL01C>Acciones</td>";
						echo "<td id=tipoL01C>Medico</td>";
						echo "<td id=tipoL01C colspan=3>Operacion</td>";
						echo "<tr>";
						echo "<td id=tipoLIN1><input type='TEXT' name='wfechaw' size=5 maxlength=5 value='".$wfechaw."' class=tipo3 readonly=readonly></td>";
						echo "<td id=tipoLIN1><input type='TEXT' name='whorai' size=5 maxlength=5 value='".$whorai."' class=tipo3 readonly=readonly></td>";
						if(isset($wpas))
							echo "<td id=tipoLIN1><input type='TEXT' name='wpas' size=3 maxlength=3 value='".$wpas."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wpas' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wpad))
							echo "<td id=tipoLIN1><input type='TEXT' name='wpad' size=3 maxlength=3 value='".$wpad."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wpad' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wpam))
							echo "<td id=tipoLIN1><input type='TEXT' name='wpam' size=3 maxlength=3 value='".$wpam."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wpam' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";	
						if(isset($wpvc))
							echo "<td id=tipoLIN1><input type='TEXT' name='wpvc' size=3 maxlength=3 value='".$wpvc."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wpvc' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wfca))
							echo "<td id=tipoLIN1><input type='TEXT' name='wfca' size=3 maxlength=3 value='".$wfca."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wfca' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wfre))
							echo "<td id=tipoLIN1><input type='TEXT' name='wfre' size=2 maxlength=2 value='".$wfre."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wfre' size=2 maxlength=2 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wso2))
							echo "<td id=tipoLIN1><input type='TEXT' name='wso2' size=3 maxlength=3 value='".$wso2."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wso2' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wtem))
							echo "<td id=tipoLIN1><input type='TEXT' name='wtem' size=4 maxlength=4 value='".$wtem."' class=tipo3 onKeypress='teclado1(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wtem' size=4 maxlength=4 class=tipo3 onKeypress='teclado1(event)'></td>";
						if(isset($wglu))
							echo "<td id=tipoLIN1><input type='TEXT' name='wglu' size=3 maxlength=3 value='".$wglu."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wglu' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wdiu))
							echo "<td id=tipoLIN1><input type='TEXT' name='wdiu' size=3 maxlength=3 value='".$wdiu."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wdiu' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wcap))
							echo "<td id=tipoLIN1><input type='TEXT' name='wcap' size=3 maxlength=3 value='".$wcap."' class=tipo3 onKeypress='teclado(event)'></td>";
						else
							echo "<td id=tipoLIN1><input type='TEXT' name='wcap' size=3 maxlength=3 class=tipo3 onKeypress='teclado(event)'></td>";
						if(isset($wacc))
							echo "<td id=tipoLIN1><textarea name='wacc' cols=30 rows=3 class='tipo3'>".$wacc."</textarea></td>";
						else
							echo "<td id=tipoLIN1><textarea name='wacc' cols=30 rows=3 class='tipo3'></textarea></td>";
						$query  = "select Medno1,Medno2,Medap1,Medap2 from ".$empresa."_000048 ";
						$query .= "  where Meduma = '".$key."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$num1 = mysql_num_rows($err1);	
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							echo "<td id=tipoLIN1>".$row1[0]." ".$row1[1]." ".$row1[2]." ".$row1[3]."</td>";
						}
						else
							echo "<td id=tipoLIN1>USUARIO NO REGISTRADO EN MATRIX !!!</td>";
						//echo "<td id=tipoLIN2><input type='RADIO' name=ok value=0 onclick='enter()'>Iniciar</td><td id=tipoLIN1><input type='RADIO' name=ok value=1 onclick='enter()'>Grabar</td><td id=tipoLIN2><input type='RADIO' name=ok value=2 onclick='enter()'>Repetir</td></tr>";
						echo "<td id=tipoLIN2><input type='RADIO' name=ok value=0 onclick='enter()'>Iniciar</td><td id=tipoLIN1 colspan=2><input type='RADIO' name=ok value=1 onclick='enter()'>Grabar</td></tr>";
						echo "<tr><td id=tipoT02C colspan=17>REGISTRO HEMODINAMICO</td></tr>";
					}
					if($i % 2 == 0)
						$color = "tipoLIN2";
					else
						$color = "tipoLIN1";
					$wuser = $row[2];
					$row[2] = $row[15]." ".$row[16]." ".$row[17]." ".$row[18];
					echo "<tr><td id=".$color.">".$row[0]."</td>";
					echo "<td id=".$color.">".$row[1]."</td>";
					echo "<td id=".$color.">".$row[3]."</td>";
					echo "<td id=".$color.">".$row[4]."</td>";
					echo "<td id=".$color.">".$row[5]."</td>";
					echo "<td id=".$color.">".$row[6]."</td>";
					echo "<td id=".$color.">".$row[7]."</td>";
					echo "<td id=".$color.">".$row[8]."</td>";
					echo "<td id=".$color.">".$row[9]."</td>";
					echo "<td id=".$color.">".$row[10]."</td>";
					echo "<td id=".$color.">".$row[11]."</td>";
					echo "<td id=".$color.">".$row[12]."</td>";
					echo "<td id=".$color.">".$row[19]."</td>";
					echo "<td id=".$color.">".$row[13]."</td>";
					echo "<td id=".$color.">".$row[2]."</td>";
					if($wuser == $key)
						echo "<td id=".$color." colspan=3><input type='RADIO' name=ok1 value=".$row[14]." onclick='enter()'></td></tr>";
					else
						echo "<td id=".$color." colspan=3><input type='RADIO' name=ok1 value=".$row[14]." disabled></td></tr>";
					
				}
			}
			echo"</table><br><br>";
			echo"</form>";
		}
		else
		{
			$color4="#CC99FF";
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4." colspan=3><font color=#000000 face='tahoma'><b>EL TURNO QUIRURGICO NO EXISTE. REVISE O LLAME A INFORMATICA!!!</b></font></td></tr>";
			echo "</table><br>";
		}
	}
}
?>
</body>
</html>
