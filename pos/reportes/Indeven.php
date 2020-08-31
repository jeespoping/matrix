<html>
<head>
  <title>MATRIX</title>
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
  <style>
		.tipoTABLE1{font-family:Arial;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;}
		.tipo1{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo2{color:#000066;background:#DDDDDD;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;}
	    .tipo4{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;}
	    .tipo5{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo6{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:25em;}
	    .tipo7{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:3em;}
	    .tipo8{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo9{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:25em;}
	    
	    .tipo12{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo13{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:normal;text-align:right;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    
	    .tipo10{color:#000066;background:#32CD32;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipo11{color:#000066;background:#FF0000;font-size:7pt;font-family:Arial;font-weight:normal;text-align:center;border-style:solid;border-collapse:collapse;border-color: #000000;border-width: 1px;height:4em;width:15em;}
	    .tipoF{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
  	</style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Vencimientos de Articulos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b>Indeven.php Ver. 2017-08-25</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function dias_transcurridos($fecha_i,$fecha_f)
{
	$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
	$dias = floor($dias);		
	return $dias;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='Indeven.php' method=post>";
		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wfecha1) or !isset($wfecha2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>INFORME DE VENCIMIENTOS DE ARTICULOS</td></tr>";
			if (!isset($wfecha1) or !isset($wfecha2))
			{
				$wfecha1=date("Y-m-d");
				$wfecha2=date("Y-m-d");
			}
			echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA INICIAL:</b></td>";
			echo "<td bgcolor='#cccccc' align=center valign=center>A&ntilde;o - Mes - Dia<br><input type='TEXT' name='wfecha1' size=10 maxlength=10 id='wfecha1' readonly='readonly' value=".$wfecha1." class=tipoF></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td>";
			?>
			<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
			//]]></script>
			<?php
			echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA FINAL:</b></td>";
			echo "<td bgcolor='#cccccc' align=center valign=center>A&ntilde;o - Mes - Dia<br><input type='TEXT' name='wfecha2' size=10 maxlength=10 id='wfecha2' readonly='readonly' value=".$wfecha2." class=tipoF></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td>";
			?>
			<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
			//]]></script>
			<?php
			echo "<tr><td bgcolor=#cccccc  colspan=3 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<center><table class=tipoTABLE1>";
			echo "<tr><td class=tipo7 colspan=12>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td class=tipo7 colspan=12>LAS AMERICAS CLINICA DEL SUR</td></tr>";
			echo "<tr><td class=tipo7 colspan=12>INFORME DE VENCIMIENTOS DE ARTICULOS</td></tr>";
			echo "<tr><td class=tipo7 colspan=12>DESDE : ".$wfecha1." HASTA ".$wfecha2."</td></tr>";
			echo "<tr><td class='tipo2'>CODIGO</td><td class='tipo2'>NOMBRE<br>COMERCIAL</td><td class='tipo2'>CODIGO<br>NACIONAL</td><td class='tipo2'>PRINCIPIO<br>ACTIVO</td><td class='tipo2'>FORMA<br>FARMACOLOGICA</td><td class='tipo2'>CONCENTRACION</td><td class='tipo2'>REGISTRO<br>INVIMA</td><td class='tipo2'>FECHA VEN.<br>REG. INVIMA</td><td class='tipo2'>CLASIFICACION<br>RIESGO</td><td class='tipo2'>FECHA<br>VENCIMIENTO</td><td class='tipo2'>NRO<br>LOTE</td><td class='tipo2'>EXISTENCIAS<br>TOTALES</td><td class='tipo2'>VIGENCIA<br>EN DIAS</td></tr>";
			//                  0       1       2       3       4        5       6       7       8       9      10      11
			$query  = "select Artcod, Artnom, Artgen, Artffa, Artcon, Artima, Artfvi, Artrie, Mdefve, Mdenlo, Karexi, Artcna ";
			$query .= "from ".$empresa."_000010,".$empresa."_000011,".$empresa."_000001,".$empresa."_000007 ";
			$query .= " where Menfec between '".$wfecha1."' and '".$wfecha2."'";
			$query .= "   and mencon = '001' "; 
			$query .= "   and mendoc = mdedoc ";
			$query .= "   and mencon = mdecon ";
			$query .= "   and mdeart = artcod ";
			$query .= "   and mdeart = Karcod ";
			$query .= "   and Karcco = '1070' ";
			$query .= "  order by 1 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
				{
					$colorc="tipo5";
					$colorl="tipo6";
					$colorr="tipo12";
				}
				else
				{
					$colorc="tipo8";
					$colorl="tipo9";
					$colorr="tipo13";
				}
				$row = mysql_fetch_array($err);
				echo "<tr>";
				echo "<td class=".$colorc.">".$row[0]."</td>";
				echo "<td class=".$colorl.">".$row[1]."</td>";
				echo "<td class=".$colorl.">".$row[11]."</td>";
				echo "<td class=".$colorl.">".$row[2]."</td>";
				echo "<td class=".$colorl.">".$row[3]."</td>";
				echo "<td class=".$colorl.">".$row[4]."</td>";
				echo "<td class=".$colorl.">".$row[5]."</td>";
				echo "<td class=".$colorc.">".$row[6]."</td>";
				echo "<td class=".$colorc.">".$row[7]."</td>";
				echo "<td class=".$colorc.">".$row[8]."</td>";
				echo "<td class=".$colorc.">".$row[9]."</td>";
				echo "<td class=".$colorr.">".number_format((double)$row[10],4,'.',',')."</td>";
				$numero=dias_transcurridos($row[8],date("Y-m-d"));
				if($numero > -1)
				{
					$numero = $numero." Dias para Vencer";
					$colorl = "tipo10";
				}
				else
				{
					$numero = abs($numero);
					$numero = $numero." Dias de Vencido";
					$colorl = "tipo11";
				}
				echo "<td class=".$colorl.">".$numero."</td>";
				echo "</tr>";
			}
			echo "<tr><td class='tipo2' colspan=11>NUMERO DE ARTICULOS : </td><td class='tipo2'>".number_format((double)$num,0,'.',',')."</td></tr>";
			echo "</table></center>";
		}
	}
?>
</body>
</html>
