<html>
<head>
  	<title>MATRIX Reporte de Balance de Liquidos UCI-UCE</title>
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
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
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
    	
    	
    	
    </style>
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.BalanceUCI.submit();
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
				titulo : 'Balance de Liquidos UCI-UCE ' ,
				tituloy: 'Mililitros',
				divgrafica: 'amcharts',
				filaencabezado : [0,1],
				datosadicionales : [2,3],
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
/*
*********************************************************************************************************************  
[DOC]
	   PROGRAMA : BalanceUCI.php
	   Fecha de Liberacion : 2011-11-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2016-12-27
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario la impresion del Balance de Liquidos a partir de los registros
	   realizados en la HCE, en los formularios destinados para este proposito en las unidades de UCI-UCE
	   
	   REGISTRO DE MODIFICACIONES :
	   .2016-12-27
			se corrige la sumatoria total de ingresos y egresos, teniendo en cuenta los regrabados.
	   .2016-11-25
			se corrige la grafica, para mostrar los eliminados como valores negativos.
	   .2016-10-07
			se corrige el query para abarcar el horario 8am a 7am y no de 7am a 6am-
	   .2016-03-14
	   		Release de Version Beta.
	   
[*DOC]   		
**********************************************************************************************************************
*/
function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return 1;
	elseif ($vec1[0] < $vec2[0])
				return -1;
			else
				return 0;
}
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,strpos($chain,"-")+1);
}

function diferencia($fechaf,$fechai)
{
	$a1=(integer)substr($fechaf,0,4)*360 +(integer)substr($fechaf,5,2)*30 + (integer)substr($fechaf,8,2);
	$a2=(integer)substr($fechai,0,4)*360 +(integer)substr($fechai,5,2)*30 + (integer)substr($fechai,8,2);
	$diff=$a1 - $a2;
	return $diff;
}

function buscarxid(&$DATA2,$hora,$id)
{
	$wsw = -1;
	for ($g=0;$g<count($DATA2);$g++)
		if($DATA2[$g][3] == $hora and $DATA2[$g][4] < $id)
			$wsw = $g;
		elseif($DATA2[$g][3] == $hora and $DATA2[$g][4] > $id)
				$wsw = -2;
	//echo $hora." ".$id." ".$wsw." ".$DATA2[$g][3]."<br>";
	return $wsw;
}

function deep_grid1($data,$tipo,$pos,&$GTA,&$GTE,&$GBA)
{
	if($data != "0*")
	{
		$Gdataseg=explode("*",$data);
		if($tipo == 1)
		{
			for ($g=1;$g<=$Gdataseg[0];$g++)
			{
				$Gdatadata=explode("|",$Gdataseg[$g]);
				$GTA += $Gdatadata[$pos];
				$GBA += $Gdatadata[$pos];
			}
		}
		elseif($tipo == 2)
		{
			for ($g=1;$g<=$Gdataseg[0];$g++)
			{
				$Gdatadata=explode("|",$Gdataseg[$g]);
				$GTE += $Gdatadata[$pos];
				$GBA -= $Gdatadata[$pos];
			}
		}
	}
}

function deep_grid(&$DATA,&$CLASES,&$HORAS,$data,$tipo,$pos,&$C,&$FA,&$FE,&$GU)
{
	if($data != "0*")
	{
		$Gdataseg=explode("*",$data);
		if($tipo == 1)
		{
			for ($g=1;$g<=$Gdataseg[0];$g++)
			{
				$Gdatadata=explode("|",$Gdataseg[$g]);
				$clase = substr($Gdatadata[0],strpos($Gdatadata[0],"-")+1);
				$clave = array_search("(A)".$clase, $CLASES);
				if($clave !== false)
					$DATA[$clave][$C] += $Gdatadata[$pos];
				else
				{
					$FA++;
					$CLASES[$FA] = "(A)".$clase;
					$DATA[$FA][$C] = $Gdatadata[$pos];
				}
				$HORAS[$C][1]++;
			}
		}
		elseif($tipo == 2)
		{
			for ($g=1;$g<=$Gdataseg[0];$g++)
			{
				$Gdatadata=explode("|",$Gdataseg[$g]);
				if($pos == 2 and $Gdatadata[1] != "Seleccione")
					$clase = substr($Gdatadata[0],strpos($Gdatadata[0],"-")+1)."-".substr($Gdatadata[1],strpos($Gdatadata[1],"-")+1);
				else
					$clase = substr($Gdatadata[0],strpos($Gdatadata[0],"-")+1);
				$clave = array_search("(E)".$clase, $CLASES);
				if($clave !== false)
					$DATA[$clave][$C] += $Gdatadata[$pos];
				else
				{
					$FE++;
					$CLASES[$FE] = "(E)".$clase;
					$DATA[$FE][$C] = $Gdatadata[$pos];
				}
				$HORAS[$C][1]++;
			}
		}
		else
		{
			$numero = 0;
			for ($g=1;$g<=$Gdataseg[0];$g++)
			{
				$Gdatadata=explode("|",$Gdataseg[$g]);
				$numero++;
				$GU[$C] += $Gdatadata[$pos];
				$HORAS[$C][1]++;
			}
			if($numero > 0)
				$GU[$C] = $GU[$C] / $numero;
		}
	}
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='BalanceUCI' action='BalanceUCI.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	
	if(!isset($wfecha))
		$wfecha=date("Y-m-d");
	if(!isset($wfecha) or !isset($whis) or !isset($wing) or $whis == "" or $wing == "")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2><b>CLINICA LAS AMERICAS<b></td></tr>";
		echo "<tr><td align=center colspan=2>INFORME DE BALANCE DE LIQUIDOS UCI-UCE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nro. de Historia</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' ' size=12  value='".$whis."' readonly='readonly' maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nro. de Ingreso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wing' size=12 value='".$wing."' readonly='readonly' maxlength=12></td></tr>";
		echo "<tr><td bgcolor='#cccccc' align=center valign=center>Fecha Inicial</td><td bgcolor='#cccccc' align=center valign=center><input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{	
		echo "<input type='HIDDEN' name='whis' value='".$whis."'>";	
		echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
		$query = "select oriced,oritid from root_000037 ";
		$query .= " where Orihis = '".$whis."'";
		$query .= "   and Oriing = '".$wing."'";
		$query .= "   and oriori = '01' ";
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
		$query .= "   and oriori = '01' ";
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
		$whis=$row[6];
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
		$wformulario="000327";
		$DATA_0 = array();
		$DATA_1 = array();
		$query  = "select ".$empresa."_".$wformulario.".Fecha_data,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".id from ".$empresa."_".$wformulario." ";
		$query .= "  where ".$empresa."_".$wformulario.".movpro = '".$wformulario."'";
		$query .= "    and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
		$query .= "    and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
		$query .= "    and ".$empresa."_".$wformulario.".Movcon in (27) ";
		$query .= "  Order by 3 ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$K = -1;
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$pos=array_search($row[0].":".$row[1],$DATA_0);
				if($pos === false)
				{
					$k++;
					$DATA_0[$k] = $row[0].":".$row[1];
					$DATA_1[$k] = $row[2];
				}
				else
					$DATA_1[$pos] = $row[2];
			}
		}
		$query  = "select ".$empresa."_".$wformulario.".Fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.detorp,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".id from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
		$query .= "  where ".$empresa."_".$wformulario.".movpro = '".$wformulario."'";
		$query .= "    and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
		$query .= "    and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
		$query .= "    and ".$empresa."_".$wformulario.".Movcon in (7,9,11,14,20,22,24,26,27,29,41) ";
		$query .= "    and ".$empresa."_".$wformulario.".movpro = ".$empresa."_000002.Detpro ";
		$query .= "    and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.Detcon ";
		$query .= "  Order by 1,2,3 ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$GTA = 0;
			$GTE = 0;
			$GBA = 0;
			$WSS = 0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				switch($row[3])
				{
					case 7:
						if($WSS == 1)
							deep_grid1($row[4],1,1,$GTA,$GTE,$GBA);
					break;
					case 9:
						if($WSS == 1)
							deep_grid1($row[4],1,1,$GTA,$GTE,$GBA);
					break;
					case 11:
						if($WSS == 1)
							deep_grid1($row[4],1,1,$GTA,$GTE,$GBA);
					break;
					case 20:
						if($WSS == 1)
							deep_grid1($row[4],1,1,$GTA,$GTE,$GBA);
					break;
					case 22:
						if($WSS == 1)
							deep_grid1($row[4],1,1,$GTA,$GTE,$GBA);
					break;
					case 24:
						if($WSS == 1)
							deep_grid1($row[4],1,1,$GTA,$GTE,$GBA);
					break;
					case 27:
						$pos=array_search($row[5],$DATA_1);
						if($pos === false)
							$WSS = 0;
						else
							$WSS = 1;
					break;
					case 29:
						if($WSS == 1)
							deep_grid1($row[4],1,1,$GTA,$GTE,$GBA);
					break;
					case 14:
						if($WSS == 1)
							deep_grid1($row[4],2,2,$GTA,$GTE,$GBA);
					break;
					case 26:
						if($WSS == 1)
							deep_grid1($row[4],2,1,$GTA,$GTE,$GBA);
					break;
					case 41:
						if($WSS == 1)
							deep_grid1($row[4],1,1,$GTA,$GTE,$GBA);
					break;
				}
			}
		}					
		$DATA1=array();
		$DATA2=array();
		$k1=-1;
		$klave="";
		
		$wtotA = 0;
		$wtotE = 0;
		$WH1 = "(";
		$WH2 = "(";
		$wfecham1= strtotime ( '+1 day' , strtotime ( $wfecha ) ) ;
		$wfecham1 = date ( 'Y-m-d' , $wfecham1 );
		$query  = "select '1' AS Codigo,".$empresa."_".$wformulario.".Fecha_data,".$empresa."_".$wformulario.".Hora_data from ".$empresa."_".$wformulario." ";
		$query .= "  where ".$empresa."_".$wformulario.".movpro = '".$wformulario."'";
		$query .= "    and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
		$query .= "    and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
		$query .= "    and ".$empresa."_".$wformulario.".movcon = 40 ";
		$query .= "    and ".$empresa."_".$wformulario.".movdat = '".$wfecha."' ";
		$query .= " union all ";
		$query .= " select '2' AS Codigo,".$empresa."_".$wformulario.".Fecha_data,".$empresa."_".$wformulario.".Hora_data from ".$empresa."_".$wformulario." ";
		$query .= "  where ".$empresa."_".$wformulario.".movpro = '".$wformulario."'";
		$query .= "    and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
		$query .= "    and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
		$query .= "    and ".$empresa."_".$wformulario.".movcon = 40 ";
		$query .= "    and ".$empresa."_".$wformulario.".movdat = '".$wfecham1."' ";
		// echo $query."<br>";
		$err = mysql_query($query,$conex) or die("1 :".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				// $DATA1[$i][0] = $row[1];
				// $DATA1[$i][1] = $row[2];
				// $DATA1[$i][2] = $row[0];
				$DATA1[$i][0] = $row['Fecha_data'];
				$DATA1[$i][1] = $row['Hora_data'];
				$DATA1[$i][2] = $row['Codigo'];
			}
		}
		$K1 = $num;
		$K2 = -1;
		for ($i=0;$i<$K1;$i++)
		{
			if($DATA1[$i][2] == "1")
			{
				$query  = "select ".$empresa."_".$wformulario.".Fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movdat,id from ".$empresa."_".$wformulario." ";
				$query .= "  where ".$empresa."_".$wformulario.".movpro = '".$wformulario."'";
				$query .= "    and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
				$query .= "    and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
				$query .= "    and ".$empresa."_".$wformulario.".movcon = 27 ";
				$query .= "    and SUBSTRING(".$empresa."_".$wformulario.".movdat,1,2) >= '08' ";
				$query .= "    and ".$empresa."_".$wformulario.".fecha_data = '".$DATA1[$i][0]."'"; 
				$query .= "    and ".$empresa."_".$wformulario.".hora_data = '".$DATA1[$i][1]."'"; 
				// echo$query;
				$err = mysql_query($query,$conex) or die("2: ".mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$pos = buscarxid($DATA2,$row[2],$row[3]);
					if($pos == -1)
					{
						$K2++;
						$DATA2[$K2][0] = $row[0];
						$DATA2[$K2][1] = $row[1];
						$DATA2[$K2][2] = $DATA1[$i][2];
						$DATA2[$K2][3] = $row[2];
						$DATA2[$K2][4] = $row[3];
					}
					elseif($pos > -1)
					{
						$DATA2[$pos][0] = $row[0];
						$DATA2[$pos][1] = $row[1];
						$DATA2[$pos][2] = $DATA1[$i][2];
						$DATA2[$pos][3] = $row[2];
						$DATA2[$pos][4] = $row[3];
					}
				}
			}
			else
			{
				$query  = "select ".$empresa."_".$wformulario.".Fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movdat,id  from ".$empresa."_".$wformulario." ";
				$query .= "  where ".$empresa."_".$wformulario.".movpro = '".$wformulario."'";
				$query .= "    and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
				$query .= "    and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
				$query .= "    and ".$empresa."_".$wformulario.".movcon = 27 ";
				$query .= "    and SUBSTRING(".$empresa."_".$wformulario.".movdat,1,2) < '08' ";
				$query .= "    and ".$empresa."_".$wformulario.".fecha_data = '".$DATA1[$i][0]."'"; 
				$query .= "    and ".$empresa."_".$wformulario.".hora_data = '".$DATA1[$i][1]."'"; 
				$err = mysql_query($query,$conex) or die("3 :".mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$row = mysql_fetch_array($err);
					$pos = buscarxid($DATA2,$row[2],$row[3]);
					if($pos == -1)
					{
						$K2++;
						$DATA2[$K2][0] = $row[0];
						$DATA2[$K2][1] = $row[1];
						$DATA2[$K2][2] = $DATA1[$i][2];
						$DATA2[$K2][3] = $row[2];
						$DATA2[$K2][4] = $row[3];
					}
					elseif($pos > -1)
					{
						$DATA2[$pos][0] = $row[0];
						$DATA2[$pos][1] = $row[1];
						$DATA2[$pos][2] = $DATA1[$i][2];
						$DATA2[$pos][3] = $row[2];
						$DATA2[$pos][4] = $row[3];
					}
				}
			}
		}
		
		$query  = "";
		for ($i=0;$i<=$K2;$i++)
		{
			switch($i)
				{
					case 0:
						//                                                  0                                        1                            2                                   3                                   4             5
						$query  = "select ".$empresa."_".$wformulario.".Fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.detorp,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,'".$DATA2[$i][2]."' from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
						$query .= "  where ".$empresa."_".$wformulario.".movpro = '".$wformulario."'";
						$query .= "    and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
						$query .= "    and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
						$query .= "    and ".$empresa."_".$wformulario.".fecha_data = '".$DATA2[$i][0]."'"; 
						$query .= "    and ".$empresa."_".$wformulario.".hora_data = '".$DATA2[$i][1]."'"; 
						$query .= "    and ".$empresa."_".$wformulario.".Movcon in (7,9,11,14,18,20,22,24,26,27,29,41) ";
						$query .= "    and ".$empresa."_".$wformulario.".movpro = ".$empresa."_000002.Detpro ";
						$query .= "    and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.Detcon ";
						
					break;
					default:
						//                                                  0                                        1                            2                                   3                                   4             5
						$query .= " union all ";
						$query .= " select ".$empresa."_".$wformulario.".Fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.detorp,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,'".$DATA2[$i][2]."' from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
						$query .= "  where ".$empresa."_".$wformulario.".movpro = '".$wformulario."'";
						$query .= "    and ".$empresa."_".$wformulario.".movhis = '".$whis."' ";
						$query .= "    and ".$empresa."_".$wformulario.".moving = '".$wing."' ";
						$query .= "    and ".$empresa."_".$wformulario.".fecha_data = '".$DATA2[$i][0]."'"; 
						$query .= "    and ".$empresa."_".$wformulario.".hora_data = '".$DATA2[$i][1]."'"; 
						$query .= "    and ".$empresa."_".$wformulario.".Movcon in (7,9,11,14,18,20,22,24,26,27,29,41) ";
						$query .= "    and ".$empresa."_".$wformulario.".movpro = ".$empresa."_000002.Detpro ";
						$query .= "    and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.Detcon ";
					break;
				}
			}
			$query .= " order by 1,2,3 ";
		// echo $query."<br>";
		if($K2 > -1)
		{
			$err = mysql_query($query,$conex) or die("4:".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$TA = 0;
				$TE = 0;
				$GRAPH = array();
				$DATA = array();
				$HORAS = array();
				$CLASES = array();
				$GU = array();
				$FA=-1;
				$FE=99;
				$D1=0;
				$D2=0;
				$HI=25;
				$HF=0;
				for ($i=0;$i<24;$i++)
				{
					$HORAS[$i][1] = 0;
				}
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					switch($row[3])
					{
						case 27:
							if($row[5] == "1")
							{
								$hora = substr($row[4],0,2);
								if((integer)$hora < $HI)
									$HI = (integer)$hora;
								if((integer)$hora > $HF)
									$HF = (integer)$hora;
								$C = (integer)$hora - 8;
								$HORAS[$C][0] = $hora;
								$D1++;
							}
							else
							{
								$hora = substr($row[4],0,2);
								if(((integer)$hora + 23) < $HI)
									$HI = (integer)$hora + 23;
								if(((integer)$hora + 23) > $HF)
									$HF = (integer)$hora + 23;
								$C = (integer)$hora + 16;
								$HORAS[$C][0] = $hora;
								$D2++;
							}
						break;
						case 7:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],1,1,$C,$FA,$FE,$GU);
						break;
						case 9:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],1,1,$C,$FA,$FE,$GU);
						break;
						case 11:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],1,1,$C,$FA,$FE,$GU);
						break;
						case 18:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],3,2,$C,$FA,$FE,$GU);
						break;
						case 20:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],1,1,$C,$FA,$FE,$GU);
						break;
						case 22:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],1,1,$C,$FA,$FE,$GU);
						break;
						case 24:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],1,1,$C,$FA,$FE,$GU);
						break;
						case 29:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],1,1,$C,$FA,$FE,$GU);
						break;
						case 14:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],2,2,$C,$FA,$FE,$GU);
						break;
						case 26:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],2,1,$C,$FA,$FE,$GU);
						break;
						case 41:
							deep_grid($DATA,$CLASES,$HORAS,$row[4],1,1,$C,$FA,$FE,$GU);
						break;
					}
				}
			}
		}
		echo "<br><center><table border=1 align=center class=tipoTABLE1>";
		echo "<tr><td id=tipoT02 colspan=2>CLINICA LAS AMERICAS<BR>BALANCE DE LIQUIDOS UCI-UCE ACUMULADO</td></tr>";
		$color = "tipoG03W";
		echo "<tr><td id=".$color.">TOTAL ADMINISTRADOS</td>";
		echo "<td id=".$color."D>".number_format((double)$GTA,1,'.',',')."</td></tr>";
		$color = "tipoG04W";
		echo "<tr><td id=".$color.">TOTAL ELIMINADOS</td>";
		echo "<td id=".$color."D>".number_format((double)$GTE,1,'.',',')."</td></tr>";
		$color = "tipoG06W";
		echo "<tr><td id=".$color.">BALANCE TOTAL</td>";
		if($GBA > 0)
			echo "<td id='".$color."B'>".number_format((double)$GBA,1,'.',',')."</td>";
		else
			echo "<td id='".$color."R'>".number_format((double)$GBA,1,'.',',')."</td>";
		echo "</table><br>";
		
		echo "<br><center><table border=1 align=center class=tipoTABLE1>";
		if($D2 > 0)
			$D2++;
		else
			$D1++;
		//echo $D1." ".$D2."<br>";
		$CT = $D1 + $D2 +1;
		echo "<tr><td id=tipoT02 colspan=".$CT.">CLINICA LAS AMERICAS<BR>BALANCE DE LIQUIDOS UCI-UCE</td></tr>";
		echo "<tr><td id=tipoT02 valign=center colspan=".$CT.">Fecha&nbsp;&nbsp;<input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6>&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'>&nbsp;&nbsp;<button onclick='enter()'>Ir</button></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td id=tipoT02C>DIAS</td>";
		if($D1 > 0)
			echo "<td colspan=".$D1." id=tipoT02C>".$wfecha."</td>";
		if($D2 > 0)
			echo "<td colspan=".$D2." id=tipoT02C>".$wfecham1."</td>";
		echo "</tr>";
		echo "<tr><td id=tipoT02D>HORAS</td>";
		for ($i=0;$i<24;$i++)
		{
			$GRAPH[$i][1] = 0;
			$GRAPH[$i][2] = 0;
			$GRAPH[$i][3] = 0;
		}
		
		
		for ($j=0;$j<24;$j++)
		{
			$TA=0;
			if(isset($HORAS[$j][0]))
			{
				for ($i=0;$i<=$FA;$i++)
				{
					if(isset($DATA[$i][$j]))
					{
						$TA += (double)$DATA[$i][$j];
					}
				}
				$GRAPH[$j][1] = $TA;
			}
		}
		
		
		for ($j=0;$j<24;$j++)
		{
			$TE=0;
			if(isset($HORAS[$j][0]))
			{
				for ($i=100;$i<=$FE;$i++)
				{
					if(isset($DATA[$i][$j]))
					{
						$TE += (double)$DATA[$i][$j];
					}
				}
				$GRAPH[$j][2] = $TE;
			}
		}
		
		for ($i=0;$i<24;$i++)
		{
			if(isset($HORAS[$i][0]))
			{
				echo "<td id=tipoT02D>".$HORAS[$i][0]."</td>";
				$GRAPH[$i][0] = $HORAS[$i][0];
			}
		}
		echo "<td id=tipoT02D>SUBTOTAL</td>";
		echo "</tr>";
		$colorL = "tipoG07W";
		for ($i=0;$i<=$FA;$i++)
		{
			$color = "tipoG03W";
			echo "<tr><td id=".$color.">".$CLASES[$i]."</td>";
			$DATA[$i][24] = 0;
			for ($j=0;$j<24;$j++)
			{
				if(isset($HORAS[$j][0]))
				{
					if(isset($DATA[$i][$j]))
					{
						echo "<td id=".$color."D>".number_format((double)$DATA[$i][$j],1,'.',',')."</td>";
						$DATA[$i][24] += $DATA[$i][$j];
					}
					else
						echo "<td id=".$color."D></td>";
				}
			}
			echo "<td id=".$color."D>".number_format((double)$DATA[$i][24],1,'.',',')."</td>";
			echo "</tr>";
		}
		
		for ($i=1;$i<=1;$i++)
		{
			$color = "tipoG05W";
			echo "<tr><td id=".$color.">ADMINISTRADOS</td>";
			$GRAPH[24][$i]=0;
			for ($j=0;$j<24;$j++)
			{
				if(isset($HORAS[$j][0]))
				{
					if(isset($GRAPH[$j][$i]))
					{
						echo "<td id=".$color."D>".number_format((double)$GRAPH[$j][$i],1,'.',',')."</td>";
						$GRAPH[24][$i] += $GRAPH[$j][$i];
					}
					else
						echo "<td id=".$colorL."></td>";
				}
			}
			echo "<td id=".$color."D>".number_format((double)$GRAPH[24][$i],1,'.',',')."</td>";
			echo "</tr>";
		}
		
		for ($i=100;$i<=$FE;$i++)
		{
			$color = "tipoG04W";
			echo "<tr><td id=".$color.">".$CLASES[$i]."</td>";
			$DATA[$i][24] = 0;
			for ($j=0;$j<24;$j++)
			{
				if(isset($HORAS[$j][0]))
				{
					if(isset($DATA[$i][$j]))
					{
						echo "<td id=".$color."D>".number_format((double)$DATA[$i][$j],1,'.',',')."</td>";
						$DATA[$i][24] += $DATA[$i][$j];
					}
					else
						echo "<td id=".$color."D></td>";
				}
			}
			echo "<td id=".$color."D>".number_format((double)$DATA[$i][24],1,'.',',')."</td>";
			echo "</tr>";
		}
		
		for ($i=0;$i<24;$i++)
		{
			if(isset($HORAS[$i][0]))
			{
				$GRAPH[$i][3] = $GRAPH[$i][1] - $GRAPH[$i][2];
			}
		}
		
		for ($i=2;$i<=3;$i++)
		{
			$color = "tipoG05W";
			switch ($i)
			{
				case 2:
					echo "<tr><td id=".$color.">ELIMINADOS</td>";
				break;
				case 3:
					$color = "tipoG06W";
					echo "<tr><td id=".$color.">BALANCE</td>";
				break;
			}
			$GRAPH[24][$i]=0;
			for ($j=0;$j<24;$j++)
			{
				if(isset($HORAS[$j][0]))
				{
					if(isset($GRAPH[$j][$i]))
					{
						if($i < 3)
							echo "<td id=".$color."D>".number_format((double)$GRAPH[$j][$i],1,'.',',')."</td>";
						else
						{
							if($GRAPH[$j][$i] > 0)
								echo "<td id='".$color."B'>".number_format((double)$GRAPH[$j][$i],1,'.',',')."</td>";
							else
								echo "<td id='".$color."R'>".number_format((double)$GRAPH[$j][$i],1,'.',',')."</td>";
						}
						$GRAPH[24][$i] += $GRAPH[$j][$i];
					}
					else
						echo "<td id=".$colorL."></td>";
				}
			}
			if($i < 3)
				echo "<td id=".$color."D>".number_format((double)$GRAPH[24][$i],1,'.',',')."</td>";
			else
			{
				if($GRAPH[24][$i] > 0)
					echo "<td id='".$color."B'>".number_format((double)$GRAPH[24][$i],1,'.',',')."</td>";
				else
					echo "<td id='".$color."R'>".number_format((double)$GRAPH[24][$i],1,'.',',')."</td>";
			}
			echo "</tr>";
		}
		for ($i=1;$i<=1;$i++)
		{
			$numero = 0;
			$color = "tipoG05W";
			echo "<tr><td id=".$color.">GASTO URINARIO</td>";
			$GU[24]=0;
			for ($j=0;$j<24;$j++)
			{
				if(isset($HORAS[$j][0]))
				{
					//$numero++;
					if(isset($GU[$j]))
					{
						$numero++;
						echo "<td id=".$color."D>".number_format((double)$GU[$j],1,'.',',')."</td>";
						$GU[24] += $GU[$j];
						//$numero++;
					}
					else
						echo "<td id=".$color."D></td>";
				}
			}
			$numero = $HF - $HI + 1;
			if($numero > 0)
				$GU[24] = $GU[24] / $numero;
			echo "<td id=".$color."D>".number_format((double)$GU[24],2,'.',',')."</td>";
			echo "</tr>";
		}
		echo "<tr><td id=tipoT02 colspan=".$CT." onclick='toggleDisplay(seg);Graficar();'>Graficar&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/root/chart.png'></td></tr>";
		echo "</table><br>";
		
		$NG=-1;
		for ($i=0;$i<24;$i++)
		{
			if(isset($HORAS[$i][0]))
			{
				$NG++;
				$GRAPH1[$NG][0] = $GRAPH[$i][0];
				$GRAPH1[$NG][1] = $GRAPH[$i][1];
				$GRAPH1[$NG][2] = $GRAPH[$i][2] * (-1);
				$GRAPH1[$NG][3] = $GRAPH[$i][3];
			}
		}

		echo "<tr><td colspan=4><center><table border=1 id='tablaresultados' class=tipo3GRID>";
		echo "<tr><td>HORA</td><td>ADMINISTRADOS</td><td>ELIMINADOS</td><td>BALANCE</td></tr>";
		for ($j=0;$j<=$NG;$j++)
		{
			echo "<tr><td>".$GRAPH1[$j][0]."</td><td>".$GRAPH1[$j][1]."</td><td>".$GRAPH1[$j][2]."</td><td>".$GRAPH1[$j][3]."</td><td>".$GRAPH1[$j][4]."</td></tr>";
		}
		echo "</table>";
		echo "<center><table border=1>";
		//echo "<tr><td colspan=4><button type='button' onclick='toggleDisplay(seg);Graficar();'><IMG SRC='/matrix/images/medical/root/chart.png'></button></td></tr>";
		echo "<tr id='seg' style='display: none'><td colspan=4><table align='center' >";
		echo "<tr>";
		echo "<td><div id='amcharts' style='width:900px; height:600px;'></div></td>";
		echo "</tr>";
		echo "</table>";
		echo "</td></tr>";
		echo "</table></center><br><br>";
		echo"</form>";
	}
}
?>
</body>
</html>
