<html>
<head>
  	<title>MATRIX Asignacion de Anestesiologos Anestesiologos.php</title>
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
    	#tipo15{color:#000066;background:#FF0000;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo20{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    </style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Anestesiologos.submit();
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
	   PROGRAMA : Anestesiologos.php
	   Fecha de Liberación : 2007-12-10
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2008-01-03

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite la asignacion rapida de
	   anestesiologos a los turnos de cirugia.


	   REGISTRO DE MODIFICACIONES :

	   .2008-01-03
	   		Se adiciono la validacion de disponibilidad del tiempo del anestesiologo y que el quirofano no este fuera
	   		de uso.

	   .2007-12-10
	   		Release de Versión Beta.

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
function validar_medicos($conex,$wquix,$whinx,$whfix,$wfecx,$med)
{
	global $empresa;
	$wsw=0;
	$query = "SELECT Mmehin, Mmehfi  FROM ".$empresa."_000010 ";
	$query .= " where Mmefec = '".$wfecx."' ";
	$query .= "  and Mmemed = '".$med."'";
	$query .= "  and ((Mmehin <= '".$whinx."'";
	$query .= "  and   Mmehfi >= '".$whinx."')";
	$query .= "   or  (Mmehin <= '".$whfix."'";
	$query .= "  and   Mmehfi >= '".$whfix."')";
	$query .= "   or  (Mmehin >= '".$whinx."'";
	$query .= "  and   Mmehfi <= '".$whfix."'))";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		for ($j=0;$j<$num;$j++)
		{
			$row = mysql_fetch_array($err);
        	if ($row[0] != $whfix And $row[1] != $whinx)
        		$wsw = 1;
        }
	}
	if($wsw > 0)
		return false;
	else
		return true;
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Anestesiologos' action='Anestesiologos.php' method=post>";




	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	$query = "SELECT Quicod  from ".$empresa."_000012 where Quiest='on' order by quicod ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	$nroqui=$num;
	if(!isset($whin) or !isset($whfi) or !isset($wqui) or $wqui < 1 or $wqui > $nroqui or !validar_hora($whin) or !validar_hora($whfi) or $whfi < $whin)
	{
		echo "<table border=0 align=center>";
		echo "<td align=center><table border=0 align=center id=tipo5>";
		?>
		<script>
			function ira(){document.Anestesiologos.wfecha.focus();}
		</script>
		<?php
		echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=2 id=tipo19> Ver. 2008-01-03</td></tr>";
		echo "<tr><td align=center colspan=2 id=tipo14>ASIGNACION DE ANESTESIOLOGOS</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Anestesiologo</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Medcod, Mednom FROM ".$empresa."_000006 ";
		$query .= " where Medane = 'on' ";
		$query .= " order by Mednom  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wane'>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
		echo "</td></tr>";
		if (!isset($wfecha))
			$wfecha=date("Y-m-d");
		echo "<tr><td bgcolor='#cccccc' align=center><b>Fecha :</b></td>";
		echo "<td bgcolor='#cccccc' align=center valign=center><input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6>&nbsp&nbsp<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
		//]]></script>
		<?php
		echo "</tr><tr><td bgcolor=#cccccc align=center>Quirofano :</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wqui' size=2 maxlength=2></td></tr>";
		echo "</tr><tr><td bgcolor=#cccccc align=center>Hora Inicial :</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whin' size=5 maxlength=5></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Hora Final :</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whfi' size=5 maxlength=5></td></tr>";
		echo "<tr><td bgcolor='#cccccc' align=center colspan=2><input type='submit' value='IR'>&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
		echo "</table><br>";
	}
	else
	{
		$lin=-1;
		echo "<table border=0 align=center>";
		echo "<tr><td align=center colspan=7><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=7 id=tipo19> Ver. 2008-01-03</td></tr>";
		echo "<tr><td colspan=7 id=tipo14>ASIGNACION DE ANESTESIOLOGOS</td></tr>";
		echo "<tr><td id=tipo11>Nro Turno</td><td id=tipo11>Quirofano</td><td id=tipo11>Hora Inicial</td><td id=tipo11>Hora Final</td><td id=tipo11>Fecha</td><td id=tipo11>Cirugia</td><td id=tipo11>Observacion</td></tr>";
		$query = "SELECT Turtur, Turqui, Turhin, Turhfi, Turfec, Turcir, Turest FROM ".$empresa."_000011 ";
		$query .= " where turqui = ".$wqui;
		$query .= "   and turhin >= '".$whin."' ";
		$query .= "   and turhfi <= '".$whfi."' ";
		$query .= "   and turfec = '".$wfecha."' ";
		$query .= " order by Turtur ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$query = "SELECT Mmetur, Mmemed  FROM ".$empresa."_000010 ";
			$query .= " where Mmetur = ".$row[0];
			$query .= "   and Mmemed = '".substr($wane,0,strpos($wane,"-"))."' ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 == 0 and $row[6]=="on" and validar_medicos($conex,$row[1],$row[2],$row[3],$row[4],substr($wane,0,strpos($wane,"-"))))
			{
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000010 (medico,fecha_data,hora_data, Mmetur, Mmequi, Mmehin, Mmehfi, Mmefec, Mmemed, Seguridad) values ('";
				$query .=  $empresa."','";
				$query .=  $fecha."','";
				$query .=  $hora."',";
				$query .=  $row[0].",";
				$query .=  $row[1].",'";
				$query .=  $row[2]."','";
				$query .=  $row[3]."','";
				$query .=  $row[4]."','";
				$query .=  substr($wane,0,strpos($wane,"-"))."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO ANESTESIOLOGOS : ".mysql_errno().":".mysql_error());
				$lin = $lin + 1;
				if($lin % 2 == 0)
					$tipo="tipo13";
				else
					$tipo="tipo12";
				echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$row[2]."</td><td id=".$tipo.">".$row[3]."</td><td id=".$tipo.">".$row[4]."</td><td id=".$tipo.">".$row[5]."</td><td id=".$tipo.">&nbsp</td></tr>";
			}
			else
			{
				$lin = $lin + 1;
				if($lin % 2 == 0)
					$tipo="tipo13";
				else
					$tipo="tipo12";
				echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$row[2]."</td><td id=".$tipo.">".$row[3]."</td><td id=".$tipo.">".$row[4]."</td><td id=".$tipo.">".$row[5]."</td><td id=tipo15>ERROR ANESTESIOLOGO NO ASIGNADO<BR>O QUIROFANO FUERA DE USO</td></tr>";
			}
		}
		echo "</table>";
	}
}
?>
</body>
</html>