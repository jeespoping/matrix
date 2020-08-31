<html>
<head>
  	<title>MATRIX Programa de Turnos de Fisioterapia x Paciente</title>
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
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;border-style:none;}
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
    	
    	
    	#tipoG01{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;width:9em;text-align:center;height:3em;}
    	#tipoG11{color:#FFFFFF;background:#99CCFF;font-size:9pt;font-family:Arial;font-weight:bold;width:17em;text-align:center;height:15em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Arial;font-weight:bold;width:17em;text-align:center;height:3em;}
    	#tipoG55{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Arial;font-weight:bold;width:9em;text-align:center;height:15em;}
    	#tipoG21A{color:#000066;background:#FFFFFF;font-size:7.5pt;font-family:Arial;font-weight:bold;width:17em;text-align:left;height:15em;}
    	#tipoG21B{color:#000066;background:#C3D9FF;font-size:7.5pt;font-family:Arial;font-weight:bold;width:17em;text-align:left;height:15em;}
    	
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG54A{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG11{color:#FFFFFF;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG11A{color:#FFFFFF;background:#9999FF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG32A{color:#FF0000;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG41A{color:#FFFFFF;background:#66FF00;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:7pt;font-family:Tahoma;font-weight:bold;width:8.5em;text-align:center;height:3em;}
    	
    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	#tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	
    </style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.turfispac.submit();
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
	   PROGRAMA : turfispac.php
	   Fecha de LiberaciÛn : 2010-04-12
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2010-04-12
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gr·fica que permite visualizar en forma matricial
	   los pacientes que tienen turno de fisioterapia.
	   
	   REGISTRO DE MODIFICACIONES :
	   .2010-04-12
	   		Release de VersiÛn Beta.
	   		
***********************************************************************************************************************/
function date_format_fis($fecha)
{
	$dia=substr($fecha,0,strpos($fecha,"/"));
	if(strlen($dia) < 2)
		$dia = "0".$dia;
	$mes=substr($fecha,strpos($fecha,"/")+1,strrpos($fecha,"/") - (strpos($fecha,"/")+1));
	if(strlen($mes) < 2)
		$mes = "0".$mes;
	$ano=substr($fecha,strrpos($fecha,"/")+1);
	$fecha=$ano."-".$mes."-".$dia;
	return $fecha;
}
function ultimoDia($mes,$ano){ 
    $udia=28; 
    while (checkdate($mes,$udia + 1,$ano)){ 
       $udia++; 
    } 
    return $udia; 
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

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	if (ereg($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar2($chain)
{
	// Funcion que permite validar la estructura de un numero Entero
	$regular="^(\+|-)?([[:digit:]]+)$";
	if (ereg($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar3($chain)
{
	// Funcion que permite validar la estructura de una fecha
	$fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	if(ereg($fecha,$chain,$occur))
	{
		if($occur[2] < 0 or $occur[2] > 12)
			return false;
		if(($occur[3] < 0 or $occur[3] > 31) or 
		  ($occur[2] == 4 and  $occur[3] > 30) or 
		  ($occur[2] == 6 and  $occur[3] > 30) or 
		  ($occur[2] == 9 and  $occur[3] > 30) or 
		  ($occur[2] == 11 and $occur[3] > 30) or 
		  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or 
		  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
			return false;
		return true;
	}
	else
		return false;
}
function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="^([=a-zA-Z0-9' 'Ò—@?/*#-.:;_<>])+$";
	return (ereg($regular,$chain));
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="^([0-9:])+$";
	return (ereg($regular,$chain));
}
function validar6($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >23 or $occur[2]<0 or $occur[2]>59)
			return false;
		else
			return true;
	else
		return false;
}
function validar7($chain)
{
	// Funcion que permite validar la estructura de un campo Hora Especial
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >23 or ($occur[2]!=0 and $occur[2]!=30))
			return false;
		else
			return true;
	else
		return false;
}

		
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='turfispac' action='turfispac.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if($ok == 99)
	{
		echo "<table border=0 align=center id=tipo5>";
		?>
		<script>
			function ira(){document.turfispac.wfecha.focus();}
		</script>
		<?php
		echo "<tr><td align=center rowspan=5><IMG SRC='/matrix/images/medical/citas/logo_".$empresa.".png'></td></tr>";
		echo "<td align=right colspan=5><font size=2>Ver. 2010-02-09 </font></td></tr>";
		echo "<tr><td align=center colspan=5 id=tipo14><b>TURNOS DE FISIOTERAPIA POR PACIENTE</td></tr>";
		if (!isset($wfecha))
			$wfecha=date("Y-m-d");
		if (!isset($wcedT))
			$wcedT="";
		if (!isset($wnomT))
			$wnomT="";
		if (!isset($wresT))
			$wresT="";
		if (isset($wini))
		{
			$wcedT="";
			$wnomT="";
			$wresT="";
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
		echo "<td bgcolor='#cccccc' align=center valign=bottom>AÒo - Mes - Dia<br><input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha." class=tipo6></td><td bgcolor='#cccccc' align=center valign=center><IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'></td>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<td bgcolor='#cccccc' rowspan=2><input type='submit' value='IR'></td></tr>";
		echo "<tr><td align=center colspan=4 bgcolor='#cccccc'><b>Terapeuta : ";
		$query = "SELECT Codigo, Nombre  from  ".$empresa."_000015 where estado='on' order by Nombre";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wter' id=tipo1 OnChange='enter()'>";
		if ($num>0)
		{
			echo "<option>SELECCIONAR</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wter=ver($wter);
				if($wter == $row[0])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo"</td></tr>";
		echo "</table>";
		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		$Grid=array();
		$Data=array();
		$Grid[0][0]["tex"]="";
		$Grid[0][0]["bac"]=0;
		$Grid[0][0]["for"]=1;
		$Grid[0][0]["alt"]="";
		$Grid[0][0]["url"]="";
		$fase="07:00-08:00";
		for ($i=1;$i<13;$i++)
		{
			$Grid[$i][0]["tex"]=$fase;
			$Grid[$i][0]["bac"]=5;
			$Grid[$i][0]["for"]=4;
			$Grid[$i][0]["alt"]="";
			$Grid[$i][0]["url"]="";
			$hor1=(integer)substr($fase,0,2) + 1;
			if($hor1 < 10)
				$hor1="0".$hor1;
			$min1="00";
			$hor2=(integer)substr($fase,6,2) + 1;
			if($hor2 < 10)
				$hor2="0".$hor2;
			$min2="00";
			$fase=$hor1.":".$min1."-".$hor2.":".$min2;
		}
		// Quicod Quides Quiest 
		if($month == 1)
		{
			$montha = 12;
			$yeara = $year - 1;
		}
		else
		{
			$montha = $month - 1;
			$yeara = $year;
		}
		$udiama=ultimoDia($montha,$yeara);
		$udiam=ultimoDia($month,$year);
		$dias=array();
		$dias[1]="DOM";
		$dias[2]="LUN";
		$dias[3]="MAR";
		$dias[4]="MIE";
		$dias[5]="JUE";
		$dias[6]="VIE";
		$dias[7]="SAB";
		for ($i=0;$i<7;$i++)
		{
			$dia=$day - $nomdia + $i;
			if($dia < 1)
			{
				$dia = $udiama + $dia;
				$dia=$dia."/".$montha."/".$yeara;
			}
			elseif($dia > $udiam)
				{
					$dia = $dia - $udiam;
					if($month == 12)
					{
						$monthn = 1;
						$yearn = $year + 1;
					}
					else
					{
						$monthn = $month + 1;
						$yearn = $year;
					}
					$dia=$dia."/".$monthn."/".$yearn;
				}
				else
					$dia=$dia."/".$month."/".$year;
			$Grid[0][$i+1]["tex"]=date_format_fis($dia);
			$Grid[0][$i+1]["bac"]=5;
			$Grid[0][$i+1]["for"]=4;
			$Grid[0][$i+1]["alt"]="";
			$Grid[0][$i+1]["url"]="";
		}
		for ($i=1;$i<=12;$i++)
		{
			for ($j=1;$j<=7;$j++)
			{
				$Grid[$i][$j]["tex"]="";
				$Grid[$i][$j]["bac"]=1;
				$Grid[$i][$j]["for"]=1;
				$Grid[$i][$j]["alt"]="";
				$Grid[$i][$j]["url"]="";
			}
		}
		for ($i=0;$i<=12;$i++)
		{
			for ($j=0;$j<=7;$j++)
			{
				$Data[$i][$j]["npa"]=0;
				$Data[$i][$j]["tpa"]=0;
				$Data[$i][$j]["msg"]="0";
			}
		}
		$wfechas=array();
		
		$diacambio=0;
		$mt=(integer)substr($Grid[0][1]["tex"],5,2);
		
		for ($i=0;$i<7;$i++)
		{
			if($mt != (integer)substr($Grid[0][$i+1]["tex"],5,2))
			{
				$diacambio=$i+1;
				$i=8;
			}
		}
		$kfec=0;
		$udiat=ultimoDia((integer)substr($Grid[0][1]["tex"],5,2),(integer)substr($Grid[0][1]["tex"],0,4));
		$wfechas[$kfec][1]=date_format_fis("1/".(integer)substr($Grid[0][1]["tex"],5,2)."/".(integer)substr($Grid[0][1]["tex"],0,4));
		$wfechas[$kfec][2]=date_format_fis($udiat."/".(integer)substr($Grid[0][1]["tex"],5,2)."/".(integer)substr($Grid[0][1]["tex"],0,4));
		if($diacambio > 0)
		{
			$kfec++;
			$udiat=ultimoDia((integer)substr($Grid[0][$diacambio]["tex"],5,2),(integer)substr($Grid[0][$diacambio]["tex"],0,4));
			$wfechas[$kfec][1]=date_format_fis("1/".(integer)substr($Grid[0][$diacambio]["tex"],5,2)."/".(integer)substr($Grid[0][$diacambio]["tex"],0,4));
			$wfechas[$kfec][2]=date_format_fis($udiat."/".(integer)substr($Grid[0][$diacambio]["tex"],5,2)."/".(integer)substr($Grid[0][$diacambio]["tex"],0,4));
			$wfechas[$kfec-1][0]="1-".(string)($diacambio-1);
			$wfechas[$kfec][0]=$diacambio."-7";
		}
		else
			$wfechas[$kfec][0]="1-7";
		
		
		if(!isset($wter) OR $wter == "")
			$wter="SELECCIONAR";
		//                 0          1          2      3        4   
		$query = "SELECT Fecha, Hora_Inicial, Cedula, Nombre, Actividad from ".$empresa."_000017 ";
		$query .= " where Terapeuta = '".$wter."' ";
		$query .= "   and Fecha between '".$Grid[0][1]["tex"]."' and '".$Grid[0][7]["tex"]."' ";
		$query .= "   and Actividad IN (0,1,9) ";
		$query .= "   order by Fecha, Hora_Inicial";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$year = (integer)substr($row[0],0,4);
				$month = (integer)substr($row[0],5,2);
				$day = (integer)substr($row[0],8,2);
				$col = mktime(0,0,0,$month,$day,$year);
				$col = strftime("%w",$col)+1;
				$fil = (integer)substr($row[1],0,2) - 6;
				if($row[4] == 9)
				{
					$Data[$fil][$col]["tpa"] = 99;
					$Data[$fil][$col]["npa"] = 0;
				}
				else
					$Data[$fil][$col]["npa"]++;
				if($Data[$fil][$col]["msg"] != "0")
					$Data[$fil][$col]["msg"] .= $Data[$fil][$col]["npa"].".".$row[3]."<br>";
				else
					$Data[$fil][$col]["msg"] = $Data[$fil][$col]["npa"].".".$row[3]."<br>";
			}
		}
		if(isset($wter) and $wter != "SELECCIONAR")
		{
			echo "<br><center><table border=1 align=center id=tipoG00>";
			echo "<tr><td id=tipoG01></td>";
			for ($j=0;$j<7;$j++)
				echo "<td id=tipoG54>".$dias[$j+1]."</td>";
			echo "</tr>";
			for ($i=0;$i<=12;$i++)
			{
				echo "<tr>";
				for ($j=0;$j<=7;$j++)
				{
					$tipo="tipoG".$Grid[$i][$j]["bac"].$Grid[$i][$j]["for"];
					if($i > 0 and $j > 0)
					{
						if($i % 2 == 0)
							echo "<td id=tipoG21B>".$Data[$i][$j]["msg"]."</td>"; 
						else
							echo "<td id=tipoG21A>".$Data[$i][$j]["msg"]."</td>"; 
					}
					else
					{
						if($i == 0)
							$tipo="tipoG54";
						else
							$tipo="tipoG55";
						echo "<td id=".$tipo.">".$Grid[$i][$j]["tex"]."</td>"; 
					}
				}
				echo "</tr>";
			}
			echo "</table></td>";
		}
	}
}
?>
</body>
</html>