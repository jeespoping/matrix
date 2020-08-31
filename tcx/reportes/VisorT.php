<html>
<head>
  	<title>MATRIX Impresion de Turnos de Cirugia Impretur.php</title>
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
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo20{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    	#tipo00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoT01{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:4.5em;}
    	#tipoT02{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT03{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT04{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:4.5em;}
    	#tipoT05{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	#tipoT06{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT07{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT08{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:4.5em;}
    	#tipoT09{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:4.5em;}
    	#tipoT10{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:4.5em;}
    	#tipoT11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT12{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT13{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT14{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1.5em;text-align:center;height:4.5em;}
    	#tipoT15{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:4.5em;}
    	#tipoT16{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:4.5em;}

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
    	#tipoD17{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}

    	#tipoL01{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:4.5em;}
    	#tipoLE01{color:#000066;background:#CC99FF;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:3em;}
    	#tipoL02{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL03{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL04{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL05{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL06{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL07{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL08{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL09{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL10{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL11{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL12{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL13{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL14{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:4.5em;}
    	#tipoL15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:4.5em;}
    	#tipoL16{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:4.5em;}
    	#tipoL17{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}

    </style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.VisorT.submit();
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

//-->
</script>
<?php
include_once("conex.php");
/**********************************************************************************************************************
	   PROGRAMA : VisorT.php
	   Fecha de Liberacion : 2007-12-07
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2014-11-24

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite visualizar de forma matricial
	   la programacion de turnos en cirugia en una fecha determinada.


	   REGISTRO DE MODIFICACIONES :

	   .2014-11-24
	   		Se adiciona el documento del paciente al reporte.

	   .2007-12-14
	   		Release de Version Beta se cambia la presentacion de la impresion mostrando si el paciente trajo la orden
	   		y ademas se muestra la separacion de quirofanos.

	   .2007-12-07
	   		Release de Version Beta.

***********************************************************************************************************************/
function validar_hora($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="/^([[:digit:]]{1,2}):([[:digit:]]{1,2})$/";
	if(preg_match($hora,$chain,$occur))
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
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='VisorT' action='VisorT.php' method=post>";




	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($whint) or !isset($whfit) or !validar_hora($whint) or !validar_hora($whfit) or $whfit < $whint)
	{
		echo "<table border=0 align=center>";
		echo "<td align=center><table border=0 align=center id=tipo5>";
		?>
		<script>
			function ira(){document.VisorT.wfecha.focus();}
		</script>
		<?php
		echo "<tr><td align=center colspan=4><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center colspan=4 id=tipo19> Ver. 2014-11-24</td></tr>";
		echo "<tr><td align=center colspan=4 id=tipo14>CUADRO DE TURNOS EN CIRUGIA </td></tr>";
		if (!isset($wfecha))
			$wfecha=date("Y-m-d");
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
		echo "<td bgcolor='#cccccc' align=center valign=center>A&ntilde;o - Mes - Dia<br><input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
		//]]></script>
		<?php
		echo "</tr><tr><td bgcolor=#cccccc align=center colspan=2>Hora Inicial</td>";
		echo "<td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='whint' size=5 maxlength=5></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center colspan=2>Hora Final</td>";
		echo "<td bgcolor=#cccccc align=center colspan=2><input type='TEXT' name='whfit' size=5 maxlength=5></td></tr>";
		echo "<tr><td bgcolor='#cccccc' align=center colspan=4><input type='submit' value='IR'></td></tr>";
		echo "</table><br>";
	}
	else
	{
		$query = "SELECT Quicod  from ".$empresa."_000012 where Quiest='on' order by quicod ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$nroqui=$num;
		$wimpre=array();
        for ($i=1;$i<49;$i++)
        	for ($j=1;$j<=$nroqui;$j++)
        	{
				$wimpre[$i][$j]["wcod"] = "";
				$wimpre[$i][$j]["wpof"] = 0;
				$wimpre[$i][$j]["wnom"] = "";
				$wimpre[$i][$j]["wins"] = "";
				$wimpre[$i][$j]["wtel"] = "";
				$wimpre[$i][$j]["wtcx"] = "";
				$wimpre[$i][$j]["weda"] = 0;
				$wimpre[$i][$j]["wted"] = "";
				$wimpre[$i][$j]["weps"] = "";
				$wimpre[$i][$j]["wmed"] = "";
				$wimpre[$i][$j]["wane"] = "";
				$wimpre[$i][$j]["wtan"] = "";
				$wimpre[$i][$j]["wuci"] = "";
				$wimpre[$i][$j]["wbio"] = "";
				$wimpre[$i][$j]["winf"] = "";
				$wimpre[$i][$j]["wcir"] = "";
				$wimpre[$i][$j]["wequ"] = "";
				$wimpre[$i][$j]["wmat"] = "";
				$wimpre[$i][$j]["west"]= "0";
				$wimpre[$i][$j]["word"]= "";
			}
		//                  0       1       2      3       4        5      6       7       8       9       10      11      12      13      14      15      16      17      18      19      20      21      22      23      24      25      26      27      28      29      30      31
		$query = "SELECT Turtur, Turqui, Turhin, Turhfi, Turfec, Turndt, Turdoc, Turhis, Turnin, Turnom, Turfna, Tursex, Turins, Turtcx, Turtip, Turtan, Tureps, Turuci, Turbio, Turinf, Turmat, Turmok, Turban, Turtel, Turord, Turcom, Turcir, Turmed, Turequ, Turusg, Turusm, Turest FROM ".$empresa."_000011 ";
        $query .= " where turfec = '".$wfecha."' ";
        $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
        $num = mysql_num_rows($err);
        for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$woptimo = "0";
			$wrani=(integer)(substr($row[2], 0, 2)) * 2 + 1 + (1 * ((integer)(substr($row[2], 3, 2)) / 30));
			$wranf=(integer)(substr($row[3], 0, 2)) * 2 + 1 + (1 * ((integer)(substr($row[3], 3, 2)) / 30)) - 1;
			For ($k = $wrani;$k<=$wranf;$k++)
			{
				$wimpre[$k][$row[1]]["west"] = $row[31];
				if($wimpre[$k][$row[1]]["west"] == "on")
				{
					$wimpre[$k][$row[1]]["wcod"] = $row[2]."-".$row[3];
					$wimpre[$k][$row[1]]["wpof"] = (integer)(substr($row[3], 0, 2)) * 2 + 1 + (1 * ((integer)(substr($row[3], 3, 2)) / 30));
					$wimpre[$k][$row[1]]["wnom"] = $row[9];
					$wimpre[$k][$row[1]]["wdoc"] = $row[6];
					$wimpre[$k][$row[1]]["wins"] = $row[12];
					$wimpre[$k][$row[1]]["wcir"] = $row[26];
					$wimpre[$k][$row[1]]["wtel"] = $row[23];
					$wimpre[$k][$row[1]]["wtcx"] = $row[13];
					$wimpre[$k][$row[1]]["wtan"] = $row[15];
					if($row[17] == "on")
						$wimpre[$k][$row[1]]["wuci"] = "S";
					else
						$wimpre[$k][$row[1]]["wuci"] = "N";
					if($row[18] == "on")
						$wimpre[$k][$row[1]]["wbio"] = "S";
					else
						$wimpre[$k][$row[1]]["wbio"] = "N";
					if($row[19] == "on")
						$wimpre[$k][$row[1]]["winf"] = "S";
					else
						$wimpre[$k][$row[1]]["winf"] = "N";
					if($row[24] == "on")
						$wimpre[$k][$row[1]]["word"] = "S";
					else
						$wimpre[$k][$row[1]]["word"] = "N";
					$equipos = explode("-", $row[28]);
					$tequ="";
					for ($w=0;$w<count($equipos);$w++)
        			{
	        			if($equipos[$w] != "R" and $equipos[$w] != "S")
	        				$tequ .= $equipos[$w]."-";
        			}
					$wimpre[$k][$row[1]]["wequ"] = $tequ;
					$wimpre[$k][$row[1]]["wmat"] = $row[20];
					$ann=(integer)substr($row[10],0,4)*360 +(integer)substr($row[10],5,2)*30 + (integer)substr($row[10],8,2);
					$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
					$years=($aa - $ann)/360;
					$months=(($aa - $ann) % 360)/30;
					$days=(($aa - $ann) % 360) % 30;
					//echo $years." ".$months." ".$days."<br>";
					if ($years > 1)
					{
						$wimpre[$k][$row[1]]["weda"] = $years;
					    $wimpre[$k][$row[1]]["wted"] = "A";
					}
					elseif($months > 1)
						{
							$wimpre[$k][$row[1]]["weda"] = $months;
						    $wimpre[$k][$row[1]]["wted"] = "M";
						}
						else
						{
							$wimpre[$k][$row[1]]["weda"] = $days;
						    $wimpre[$k][$row[1]]["wted"] = "D";
						}
					if($woptimo == "0")
	                {
						$woptimo = "1";
						$query = "SELECT Entdes FROM ".$empresa."_000003  ";
						$query .= " where Entcod = '".$row[16]."'";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row1 = mysql_fetch_array($err1);
						$wimpre[$k][$row[1]]["weps"] = $row1[0];

						$query = "SELECT Mednom FROM ".$empresa."_000010, ".$empresa."_000006 ";
						$query .= "where Mmetur = ".$row[0];
						$query .= "  and Mmemed = Medcod ";
						$query .= "  and Medane = 'off' ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row1 = mysql_fetch_array($err1);
						$wimpre[$k][$row[1]]["wmed"] = $row1[0];

						$query = "SELECT Mednom FROM ".$empresa."_000010, ".$empresa."_000006 ";
						$query .= "where Mmetur = ".$row[0];
						$query .= "  and Mmemed = Medcod ";
						$query .= "  and Medane = 'on' ";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row1 = mysql_fetch_array($err1);
						$wimpre[$k][$row[1]]["wane"] = $row1[0];
	                }
	                Else
	                {
	                    $wimpre[$k][$row[1]]["weps"] = $wimpre[$k - 1][$row[1]]["weps"];
	                    $wimpre[$k][$row[1]]["wmed"] = $wimpre[$k - 1][$row[1]]["wmed"];
	                    $wimpre[$k][$row[1]]["wane"] = $wimpre[$k - 1][$row[1]]["wane"];
	                    $wimpre[$k][$row[1]]["wcir"] = $wimpre[$k - 1][$row[1]]["wcir"];
	                    $wimpre[$k][$row[1]]["wequ"] = $wimpre[$k - 1][$row[1]]["wequ"];
	                    $wimpre[$k][$row[1]]["wmat"] = $wimpre[$k - 1][$row[1]]["wmat"];
	                }
				}
			}
		}
		$lin=0;
		$wquia=0;
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center id=tipo20>CLINICA LAS AMERICAS</td></tr>";
		echo "<tr><td align=center id=tipo20>CUADRO DE TURNOS EN CIRUGIA DE : ".$wfecha."</td></tr>";
		echo "<tr><td align=left id=tipo20>Ver. 2014-11-24</td></tr></table>";
		echo "<table border=0 align=center id=tipo00>";
		echo "<tr><td id=tipoT01>HORA</td><td id=tipoT02>Q</td><td id=tipoT02>O</td><td id=tipoT03>I</td><td id=tipoT04>DOCUMENTO</td><td id=tipoT04>PACIENTE</td><td id=tipoT05>TEL/HAB</td><td id=tipoT06>H</td><td id=tipoT07>ED</td><td id=tipoT08>RESPONSABLE</td><td id=tipoT09>CIRUGIA</td><td id=tipoT10>CIRUJANO</td><td id=tipoT11>A</td><td id=tipoT12>U</td><td id=tipoT13>B</td><td id=tipoT14>I</td><td id=tipoT15>ANESTESIOLOGO</td><td id=tipoT16>EQUIPOS</td></tr>";
		for ($j=1;$j<=$nroqui;$j++)
        {
	        $esp=0;
	        if($wquia != $j)
	        {
		        $esp=1;
		        $wquia=$j;
	        }
			$i = (integer)(substr($whint, 0, 2)) * 2 + 1 + (1 * ((integer)(substr($whint, 3, 2)) / 30));
			$fin=(integer)(substr($whfit, 0, 2)) * 2 + 1 + (1 * ((integer)(substr($whfit, 3, 2)) / 30));
			While( $i < $fin)
			{
				if($lin % 2 == 0)
					$tipo="tipoD";
				else
					$tipo="tipoL";
				switch ($wimpre[$i][$j]["west"])
				{
					case "0":
						if($wimpre[$i + 1][$j]["west"] == "0" And substr(horas((string)$i), 3, 1) == "0")
						{
							if($esp == 1)
							{
								echo "<tr><td id=".$tipo."E01>".horas((integer)$i)."-".horas((integer)($i+2))."</td>";
								$esp=0;
							}
							else
								echo "<tr><td id=".$tipo."01>".horas((integer)$i)."-".horas((integer)($i+2))."</td>";
							echo "<td id=".$tipo."02>".$j."</td><td id=".$tipo."17>&nbsp</td><td id=".$tipo."03>&nbsp</td><td id=".$tipo."04>&nbsp</td><td id=".$tipo."04>&nbsp</td><td id=".$tipo."05>&nbsp</td><td id=".$tipo."06>&nbsp</td><td id=".$tipo."07>&nbsp</td><td id=".$tipo."08>&nbsp</td><td id=".$tipo."09>&nbsp</td><td id=".$tipo."10>&nbsp</td><td id=".$tipo."11>&nbsp</td><td id=".$tipo."12>&nbsp</td><td id=".$tipo."13>&nbsp</td><td id=".$tipo."14>&nbsp</td><td id=".$tipo."15>&nbsp</td><td id=".$tipo."16>&nbsp</td></tr>";
							$lin++;
						    $i = $i + 2;
					    }
						Else
						{
							if($esp == 1)
							{
								$esp=0;
								echo "<tr><td id=".$tipo."E01>".horas((integer)$i)."-".horas((integer)($i+1))."</td>";
							}
							else
								echo "<tr><td id=".$tipo."01>".horas((integer)$i)."-".horas((integer)($i+1))."</td>";
							echo "<td id=".$tipo."02>".$j."</td><td id=".$tipo."17>&nbsp</td><td id=".$tipo."03>&nbsp</td><td id=".$tipo."04>&nbsp</td><td id=".$tipo."04>&nbsp</td><td id=".$tipo."05>&nbsp</td><td id=".$tipo."06>&nbsp</td><td id=".$tipo."07>&nbsp</td><td id=".$tipo."08>&nbsp</td><td id=".$tipo."09>&nbsp</td><td id=".$tipo."10>&nbsp</td><td id=".$tipo."11>&nbsp</td><td id=".$tipo."12>&nbsp</td><td id=".$tipo."13>&nbsp</td><td id=".$tipo."14>&nbsp</td><td id=".$tipo."15>&nbsp</td><td id=".$tipo."16>&nbsp</td></tr>";
							$lin++;
						    $i = $i + 1;
					    }
					break;
					Case "on":
						if($esp == 1)
						{
							$esp=0;
							echo "<tr><td id=".$tipo."E01>".$wimpre[$i][$j]["wcod"]."</td>";
						}
						else
							echo "<tr><td id=".$tipo."01>".$wimpre[$i][$j]["wcod"]."</td>";
						echo "<td id=".$tipo."02>".$j."</td><td id=".$tipo."17>".$wimpre[$i][$j]["word"]."</td><td id=".$tipo."03>".$wimpre[$i][$j]["wins"]."</td><td id=".$tipo."04>".$wimpre[$i][$j]["wdoc"]."</td><td id=".$tipo."04>".$wimpre[$i][$j]["wnom"]."</td><td id=".$tipo."05>".$wimpre[$i][$j]["wtel"]."</td><td id=".$tipo."06>".$wimpre[$i][$j]["wtcx"]."</td><td id=".$tipo."07>".number_format((double)$wimpre[$i][$j]["weda"],0,'.',',').$wimpre[$i][$j]["wted"]."</td><td id=".$tipo."08>".$wimpre[$i][$j]["weps"]."</td><td id=".$tipo."09>".$wimpre[$i][$j]["wcir"]."</td><td id=".$tipo."10>".$wimpre[$i][$j]["wmed"]."</td><td id=".$tipo."11>".$wimpre[$i][$j]["wtan"]."</td><td id=".$tipo."12>".$wimpre[$i][$j]["wuci"]."</td><td id=".$tipo."13>".$wimpre[$i][$j]["wbio"]."</td><td id=".$tipo."14>".$wimpre[$i][$j]["winf"]."</td><td id=".$tipo."15>".$wimpre[$i][$j]["wane"]."</td><td id=".$tipo."16>".$wimpre[$i][$j]["wequ"]."</td></tr>";
						$lin++;
						$i = $wimpre[$i][$j]["wpof"];
					break;
					Case "off":
						if($esp == 1)
						{
							$esp=0;
							echo "<tr><td id=".$tipo."E01>".horas((integer)$i)."-".horas((integer)($i+1))."</td>";
						}
						else
							echo "<tr><td id=".$tipo."01>".horas((integer)$i)."-".horas((integer)($i+1))."</td>";
						echo "<td id=".$tipo."02>".$j."</td><td id=".$tipo."17>&nbsp</td><td id=".$tipo."03>&nbsp</td><td id=".$tipo."04>&nbsp</td><td id=".$tipo."04>&nbsp</td><td id=".$tipo."05>&nbsp</td><td id=".$tipo."06>&nbsp</td><td id=".$tipo."07>&nbsp</td><td id=".$tipo."08>&nbsp</td><td id=".$tipo."09>&nbsp</td><td id=".$tipo."10>&nbsp</td><td id=".$tipo."11>&nbsp</td><td id=".$tipo."12>&nbsp</td><td id=".$tipo."13>&nbsp</td><td id=".$tipo."14>&nbsp</td><td id=".$tipo."15>&nbsp</td><td id=".$tipo."16>&nbsp</td></tr>";
						$lin++;
						$i = $i + 1;
					break;
				}
			}
		}
		echo "</table>";
	}
}
?>
</body>
</html>
