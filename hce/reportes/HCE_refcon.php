<html><input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'><head><title>MATRIX</title><title>Zapatec DHTML Calendar</title><!-- Loading Theme file(s) -->    <link rel="stylesheet" href="../../zpcal/themes/winter.css" /><!-- Loading Calendar JavaScript files -->    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>    <!-- Loading language definition file -->    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>	<style type="text/css">		#tipo1{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-width: 1px;}		#tipo2{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;text-align:left;border-style:solid;border-collapse:collapse;border-width: 1px;}		#tipo3{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-width: 1px;}		#tipo4{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:solid;border-collapse:collapse;border-width: 1px;}		#tipo5{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}		#tipo6{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}		#tipo7{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:right;}		.tipoTABLE{font-family:Arial;table-layout:fixed;border-style:solid;border-collapse:collapse;border-width: 1px;}	</style></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>INFORME DE REFERENCIA Y CONTRARREFERENCIA DE PACIENTES</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b> HCE_refcon.php Ver. 2015-04-14</b></font></tr></td></table><br><br><br></center><?php
include_once("conex.php");session_start();if(!isset($_SESSION['user']))echo "error";else{ 	$key = substr($user,2,strlen($user));	
	
	echo "<form action='HCE_refcon.php?wemp_pmla=".$wemp_pmla."' method=post>";	if(!isset($v0) or !isset($v1))	{		echo  "<center><table border=0>";		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";		echo "<tr><td colspan=2 align=center><b>INFORME DE REFERENCIA Y CONTRARREFERENCIA DE PACIENTES</b></td></tr>";		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial</td>";		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' id='v0' readonly='readonly' size=10 maxlength=10 value='".date("Y-m-d")."'>&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td></tr>";		?>		<script type="text/javascript">//<![CDATA[			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'v0',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});			//]]></script>		<?php		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final</td>";		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' id='v1' readonly='readonly' size=10 maxlength=10 value='".date("Y-m-d")."'>&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td></tr>";		?>		<script type="text/javascript">//<![CDATA[			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'v1',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});			//]]></script>		<?php		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";	}	else	{		include_once("root/comun.php");		$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");		$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");				//                               0                    1                       2                     3                   4                  5       6       7     8      9      10     11     12     13             		$query  = "select ".$whce."_000070.fecha_data,".$whce."_000070.hora_data,".$whce."_000070.movhis,".$whce."_000070.moving,".$whce."_000070.movcon,".$whce."_000070.movdat,Oritid,Oriced,Pacno1,Pacno2,Pacap1,Pacap2,Ingres,Ingnre ";		$query .= "  from ".$whce."_000070,root_000037,root_000036,".$wmovhos."_000016 ";		$query .= "  where ".$whce."_000070.fecha_data between '".$v0."' and '".$v1."' "; 		$query .= "    and ".$whce."_000070.movcon in (56,69,58,60) ";		$query .= "    and ".$whce."_000070.movhis = Orihis ";		$query .= "    and Oriori = '02' ";		$query .= "    and Oritid = Pactid ";		$query .= "    and Oriced = Pacced ";		$query .= "    and ".$whce."_000070.movhis = Inghis ";		$query .= "    and ".$whce."_000070.moving = Inging ";		$query .= "  order by 1,2,3,4,5 ";		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());		$num = mysql_num_rows($err);		echo "<center><table id='tipoTABLE' CELLSPACING=0 CELLPADDING=2>";		echo "<tr><td colspan=15 id='tipo4'><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";		echo "<tr><td colspan=15 id='tipo4'><b>DIRECCION DE INFORMATICA</b></td></tr>";		echo "<tr><td colspan=15 id='tipo4'><b>INFORME DE REFERENCIA Y CONTRARREFERENCIA DE PACIENTES</b></td></tr>";		echo "<tr><td colspan=15 id='tipo4'><b>DESDE : ".$v0." HASTA : ".$v1."</b></td></tr>";		echo "<tr>";		echo "<td id='tipo3'><b>Primer<br>Nombre</b></td>";		echo "<td id='tipo3'><b>Segundo<br>Nombre</b></td>";		echo "<td id='tipo3'><b>Primer<br>Apellido</b></td>";		echo "<td id='tipo3'><b>Segundo<br>Apellido</b></td>";		echo "<td id='tipo3'><b>Tipo de <br>Identificaci&oacute;n</b></td>";		echo "<td id='tipo3'><b>Identificaci&oacute;n</b></td>";		echo "<td id='tipo3'><b>Historia</b></td>";		echo "<td id='tipo3'><b>Ingreso</b></td>";		echo "<td id='tipo3'><b>Entidad</b></td>";		echo "<td id='tipo3'><b>Fecha</b></td>";		echo "<td id='tipo3'><b>Hora</b></td>";		echo "<td id='tipo3'><b>Origen</b></td>";		echo "<td id='tipo3'><b>Destino<br>Del Servicio</b></td>";		echo "<td id='tipo3'><b>Tipo<br>De Servicio</b></td>";		echo "<td id='tipo3'><b>Personal<br>Que Atiende</b></td>";		echo "</tr>"; 		$kla = "";		$k = -1;		for ($i=0;$i<$num;$i++)		{			$row = mysql_fetch_array($err);			if($kla != $row[0].$row[1].$row[2].$row[3])			{				$kla = $row[0].$row[1].$row[2].$row[3];				if($i > 0)				{					$k++;					if($k % 2 == 0)						$tipo = "tipo1";					else						$tipo = "tipo2";					echo "<tr>";					echo "<td id='".$tipo."'>".$D[0]."</td>";					echo "<td id='".$tipo."'>".$D[1]."</td>";					echo "<td id='".$tipo."'>".$D[2]."</td>";					echo "<td id='".$tipo."'>".$D[3]."</td>";					echo "<td id='".$tipo."'>".$D[4]."</td>";					echo "<td id='".$tipo."'>".$D[5]."</td>";					echo "<td id='".$tipo."'>".$D[6]."</td>";					echo "<td id='".$tipo."'>".$D[7]."</td>";					echo "<td id='".$tipo."'>".$D[8]."</td>";					echo "<td id='".$tipo."'>".$D[9]."</td>";					echo "<td id='".$tipo."'>".$D[10]."</td>";					echo "<td id='".$tipo."'>".$D[11]."</td>";					echo "<td id='".$tipo."'>".$D[12]."</td>";					echo "<td id='".$tipo."'>".$D[13]."</td>";					echo "<td id='".$tipo."'>".$D[14]."</td>";					echo "</tr>"; 				}				$D=array();				$D[0]  = $row[8];				$D[1]  = $row[9];				$D[2]  = $row[10];				$D[3]  = $row[11];				$D[4]  = $row[6];				$D[5]  = $row[7];				$D[6]  = $row[2];				$D[7]  = $row[3];				$D[8]  = $row[12]."-".$row[13];				$D[9]  = $row[0];				$D[10] = $row[1];			}			switch ($row[4])			{				case 56:					$D[11] = $row[5];				break;				case 69:					$D[12] = $row[5];				break;				case 58:					$D[13] = $row[5];				break;				case 60:					$D[14] = $row[5];				break;			}		}		$k++;		if($k % 2 == 0)			$tipo = "tipo1";		else			$tipo = "tipo2";		echo "<tr>";		echo "<td id='".$tipo."'>".$D[0]."</td>";		echo "<td id='".$tipo."'>".$D[1]."</td>";		echo "<td id='".$tipo."'>".$D[2]."</td>";		echo "<td id='".$tipo."'>".$D[3]."</td>";		echo "<td id='".$tipo."'>".$D[4]."</td>";		echo "<td id='".$tipo."'>".$D[5]."</td>";		echo "<td id='".$tipo."'>".$D[6]."</td>";		echo "<td id='".$tipo."'>".$D[7]."</td>";		echo "<td id='".$tipo."'>".$D[8]."</td>";		echo "<td id='".$tipo."'>".$D[9]."</td>";		echo "<td id='".$tipo."'>".$D[10]."</td>";		echo "<td id='".$tipo."'>".$D[11]."</td>";		echo "<td id='".$tipo."'>".$D[12]."</td>";		echo "<td id='".$tipo."'>".$D[13]."</td>";		echo "<td id='".$tipo."'>".$D[14]."</td>";		echo "</tr>"; 		echo "</table></center>"; 	}}?></body></html>