<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head>
  	<title>MATRIX Cirugias Realizadas x Medicos del IDC Basados en la Descripcion Operatoria CXIDCDO.php</title>
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
    	#tipo20{color:#000066;background:#FFFFFF;font-size:11pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo21{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    	#tipo00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoTCK{color:#000066;background:#999999;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:50em;text-align:center;height:4.5em;}
    	#tipoT00{color:#000066;background:#999999;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:4.5em;}
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
    	
    	#tipoD00{color:#000066;background:#99CCFF;font-size:6pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:6em;}
    	#tipoDCK{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:50em;text-align:center;height:6em;}
    	#tipoD01{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:6em;}
    	#tipoDE01{color:#000066;background:#CC99FF;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:6em;}
    	#tipoD02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoD03{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoDA03{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoDB03{color:#000066;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoDC03{color:#000066;background:#FF0000;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoD04{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoD05{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoD06{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoD07{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoD08{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoD09{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoD10{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoD11{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoD12{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoD13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoD14{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoD15{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoD16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:6em;}
    	#tipoD17{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:6em;}
    	
    	#tipoL00{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:8em;text-align:center;height:6em;}
    	#tipoLCK{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:50em;text-align:center;height:6em;}
    	#tipoL01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:6em;}
    	#tipoL001{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:1em;}
    	#tipoLE01{color:#000066;background:#CC99FF;font-size:7.2pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:6em;}
    	#tipoL02{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoL03{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoLA03{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoLB03{color:#000066;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoLC03{color:#000066;background:#FF0000;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoL04{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoL05{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoL06{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoL07{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoL08{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoL09{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoL10{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoL11{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoL12{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoL13{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoL14{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:3em;text-align:center;height:6em;}
    	#tipoL15{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:6em;}
    	#tipoL16{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:6em;}
    	#tipoL17{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:6em;}
    	
    	#tipoTT01{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;height:4.5em;}
    	
    </style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.CXIDCDO.submit();
	}


//-->
</script>
<?php
include_once("conex.php");
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}
/**********************************************************************************************************************  
	   PROGRAMA : CXIDCDO.php
	   Fecha de Liberacion : 2016-06-29
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2016-06-29
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite visualizar de forma matricial
	   las cirugias realizadas x medicos del IDC y su relacion con la Descripcion Operatoria.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   	2019-12-5, Jerson Trujillo: Se modifica el query para consultar los medicos dependiendo de nuevo parametro que inica si es medico del IDC.
	   .2016-06-29
	   		Release de Version Beta.
	   		
***********************************************************************************************************************/

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='CXIDCDO' action='CXIDCDO.php?wemp_pmla=".$wemp_pmla."' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfecha1) or !isset($wfecha2))
	{
		echo "<table border=0 align=center>";
		echo "<td align=center><table border=0 align=center id=tipo5>";
		echo "<tr><td align=center colspan=3><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center colspan=3 id=tipo19> Ver. 2019-12-5</td></tr>";
		echo "<tr><td align=center colspan=3 id=tipo14>CIRUGIAS REALIZADAS X MEDICOS DEL IDC</td></tr>";
		if (!isset($wfecha1) or !isset($wfecha2))
		{
			$wfecha1=date("Y-m-d");
			$wfecha2=date("Y-m-d");
		}
		echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA INICIAL:</b></td>";
		echo "<td bgcolor='#cccccc' align=center valign=center>A&ntilde;o - Mes - Dia<br><input type='TEXT' name='wfecha1' size=10 maxlength=10 id='wfecha1' readonly='readonly' value=".$wfecha1." class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA FINAL:</b></td>";
		echo "<td bgcolor='#cccccc' align=center valign=center>A&ntilde;o - Mes - Dia<br><input type='TEXT' name='wfecha2' size=10 maxlength=10 id='wfecha2' readonly='readonly' value=".$wfecha2." class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td bgcolor=#cccccc align=center>Medico</td><td bgcolor=#cccccc align=center colspan=2>";
		$query  = "SELECT Medcod, Mednom from ".$empresa."_000006 ";
		$query .= "  where Medidc = 'on' ";
		$query .= "  Order by 2 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wmed'>";
			echo "<option>Todos</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		} 
		echo "</td></tr>";
		echo "<tr><td bgcolor='#cccccc' align=center colspan=4><input type='submit' value='IR'></td></tr>";
		echo "</table><br>";
	}
	else
	{
		include_once("root/comun.php");
		
		$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
		$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
		
		$CX=array();
		$query  = "SELECT Movdat, Fecha_data, Hora_data, movhis, moving, movusu FROM ".$whce."_000077 ";
		$query .= "  where Fecha_data between '".$wfecha1."' and '".$wfecha2."'";
		$query .= "    and Movcon = 69 ";
		$query .= "  Order by 1 ";

		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				$CX[$i][0] = trim($row1[0]);
				$CX[$i][1] = $row1[1];
				$CX[$i][2] = $row1[2];
				$CX[$i][3] = $row1[3];
				$CX[$i][4] = $row1[4];
				$CX[$i][5] = $row1[5];
			}
		}
		$numcx = $num1;

		$k = 0;
		echo "<table border=0 align=center>";
		echo "<tr><td align=center><IMG SRC='/matrix/images/medical/TCX/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center id=tipo20>CLINICA LAS AMERICAS</td></tr>";
		echo "<tr><td align=center id=tipo20>DESDE : ".$wfecha1." HASTA ".$wfecha2."</td></tr>";	
		echo "<tr><td align=center id=tipo21>CIRUGIAS CON DESCRIPCION OPERATORIOPOR MEDICOS DEL IDC</td></tr>";
		echo "</table>";
		echo "<table border=0 align=center id=tipo00>";
		echo "<tr><td id=tipoT01>TURNO QX</td>";
		echo "<td id=tipoT01>FECHA CX</td>";
		echo "<td id=tipoT02>HORA<br>INICIO</td>";
		echo "<td id=tipoT02>HORA<br>FINALIZACION</td>";
		echo "<td id=tipoT01>NIT<br>ENTIDAD</td>";
		echo "<td id=tipoT00>NOMBRE<br>ENTIDAD</td>";
		echo "<td id=tipoT02>HISTORIA</td>";
		echo "<td id=tipoT03>INGRESO</td>";
		echo "<td id=tipoT03>IDENTIFICACION</td>";
		echo "<td id=tipoT05>NOMBRE<br>PACIENTE</td>";
		echo "<td id=tipoT04>NRO<br>QUIROFANO</td>";
		echo "<td id=tipoT04>NRO<br>FACTURA</td>";
		echo "<td id=tipoT04>IDENTIFICACION<br>MEDICO</td>";
		echo "<td id=tipoT04>NOMBRE<br>MEDICO</td>";
		echo "<td id=tipoT04>REGISTRO<br>MEDICO</td>";
		echo "<td id=tipoT04>CIRUJANO<br>PRINCIPAL</td>";
		echo "<td id=tipoT04>CIRUJANO<br>DOS</td>";
		echo "<td id=tipoT04>AYUDANTE<br>UNO</td>";
		echo "<td id=tipoT04>AYUDANTE<br>DOS</td>";
		echo "<td id=tipoT04>PROCEDIMIENTO</td>";
		echo "<td id=tipoT04>AMBULATORIO/HOSPITALIZADO</td></tr>";
        
        //                   0       1       2       3      4       5       6       7       8       9       10      11      12
		$query  = "SELECT Turtur, Turfec, Turhin, Turhfi, Tureps, Entdes, Turhis, Turnin, Turdoc, Turnom, Turqui, Turtcx, Turcir 
					 FROM ".$empresa."_000010 INNER JOIN ".$empresa."_000006 ON(Mmemed = Medcod), ".$empresa."_000011,".$empresa."_000003";
		$query .= " where Mmefec between '".$wfecha1."' and '".$wfecha2."'";
		if($wmed == "Todos")
			$query .= "   and  Medidc = 'on'";
		else
			$query .= "   and Mmemed IN ('".substr($wmed,0,strpos($wmed,"-"))."')";
		$query .= "   and Mmetur = Turtur ";
		$query .= "   and Turfec between '".$wfecha1."' and '".$wfecha2."'";
		$query .= "   and Tureps = Entcod ";
		$query .= "   Order by 1 ";
		
        $err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
        $num = mysql_num_rows($err);
        for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$pos=bi($CX,$numcx,$row[0]);
			if($pos != -1)
			{
				$query  = "SELECT Movcon, Movdat FROM ".$whce."_000077 ";
				$query .= " where Fecha_data = '".$CX[$pos][1]."'";
				$query .= "   and Hora_data = '".$CX[$pos][2]."'";
				$query .= "   and Movpro = '000077'";
				$query .= "   and Movcon in (3,4,5,23,64,99) ";
				$query .= "   and Movhis = '".$CX[$pos][3]."'";
				$query .= "   and Moving = '".$CX[$pos][4]."'";
				$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num2 = mysql_num_rows($err2);
				if($num2 > 0)
				{
					for ($j=0;$j<$num2;$j++)
					{
						$row2 = mysql_fetch_array($err2);
						if($row2[0] == "11")
							$DXing = strip_tags($row2[1]);
						else
							$Comp = $row2[1];
						$PCX = $row[12];
						switch($row2[0])
						{
							case 3:
								$CXP = $row2[1];
							break;
							case 23:
								$CXD = $row2[1];
							break;
							case 4:
								$AYP = $row2[1];
							break;
							case 5:
								$AYD = $row2[1];
							break;
							case 64:
							    if(strip_tags($row2[1]) != "" and strip_tags($row2[1]) != " ")
									$PCX = strip_tags($row2[1]);
							break;
							case 99:
								if(strip_tags($row2[1]) != "" and strip_tags($row2[1]) != " ")
									$PCX .= "<br>".strip_tags($row2[1]);
							break;
						}
					}
				}
				else
				{
					$CXP = "SIN DATO";
					$CXD = "SIN DATO";
					$AYP = "SIN DATO";
					$AYD = "SIN DATO";
					$PCX = "SIN DATO";
				}
				$query  = "SELECT Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg FROM ".$wmovhos."_000048 ";
				$query .= " where Meduma = '".$CX[$pos][5]."'";
				$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num2 = mysql_num_rows($err2);
				if($num2 > 0)
				{
					$row2 = mysql_fetch_array($err2);
					$MDD = $row2[0];
					$MDN = $row2[1]." ".$row2[2]." ".$row2[3]." ".$row2[4];
					$MDR = $row2[5];
				}
				else
				{
					$MDD = "SIN DATO";
					$MDN = "SIN DATO";
					$MDR = "SIN DATO";
				}
			}
			else
			{
				$CXP = "SIN DATO";
				$CXD = "SIN DATO";
				$AYP = "SIN DATO";
				$AYD = "SIN DATO";
				$PCX = "SIN DATO";
				$MDD = "SIN DATO";
				$MDN = "SIN DATO";
				$MDR = "SIN DATO";
			}
			$conex_o = odbc_connect('facturacion','','');
			$query = "select cardethis his,cardetnum num,carfacfue,carfacdoc from facardet,facarfac where cardethis=".$row[6]." and cardetnum=".$row[7]." and cardetfec between '".$wfecha1."' and '".$wfecha2."' and cardetcco in ('1016','1191') and cardetfac='S' and cardetanu='0' and cardetreg=carfacreg and carfacanu='0' group by 1,2,3,4";
			$err_o = odbc_do($conex_o,$query);
			$campos= odbc_num_fields($err_o);
			$NFAC = "";
			while (odbc_fetch_row($err_o))
			{
				$odbc = array();
				$NFAC .= odbc_result($err_o,$campos)."<br>";
			}
			odbc_close($conex_o);
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
			if($row[11] == "A")
				$row[11] = "Ambulatorio";
			else
				$row[11] = "Hospitalizado";
			$k++;
			echo "<td id=".$tipo."01>".$row[0]."</td><td id=".$tipo."01>".$row[1]."</td><td id=".$tipo."17>".$row[2]."</td><td id=".$tipo."17>".$row[3]."</td><td id=".$tipo."01>".$row[4]."</td><td id=".$tipo."03>".$row[5]."</td><td id=".$tipo."17>".$row[6]."</td><td id=".$tipo."17>".$row[7]."</td><td id=".$tipo."04>".$row[8]."</td><td id=".$tipo."01>".$row[9]."</td><td id=".$tipo."01>".$row[10]."</td><td id=".$tipo."01>".$NFAC."</td><td id=".$tipo."01>".$MDD."</td><td id=".$tipo."01>".$MDN."</td><td id=".$tipo."01>".$MDR."</td><td id=".$tipo."01>".$CXP."</td><td id=".$tipo."01>".$CXD."</td><td id=".$tipo."01>".$AYP."</td><td id=".$tipo."01>".$AYD."</td><td id=".$tipo."01>".$PCX."</td><td id=".$tipo."17>".$row[11]."</td></tr>";
		}
		$tipo = "tipoTT01";
		echo "<td id=".$tipo." colspan=20>TOTAL CIRUGIAS</td><td id=".$tipo.">".number_format((double)$k,0,'.',',')."</td></tr>";
		echo "</table>";
		echo '<input type="button" onclick="history.back()" name="volver atrás" value="Retornar">';
	}
}
?>
</body>
</html>
