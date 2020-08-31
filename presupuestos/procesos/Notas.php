<html>
<head>
  <title>MATRIX - NOTAS Y EXPLICACIONES A LOS RESULTADOS COMPARATIVOS X UNIDAD</title>
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
    	
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:2em;}
    	#tipoG02{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:2em;}
    	#tipoG03{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:2em;}
    	#tipoG04{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:2em;}
    	#tipoM01{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}
    	#tipoM02{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}
    	#tipoM03{color:#000066;background:#999999;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}
    	#tipoG05{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:12em;}
    	#tipoG06{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;height:12em;}
    	.tipo3A{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	
    </style>
    <SCRIPT>
	    function enter()
		{
			document.forms.Notas.submit();
		}
	    function Cerrar() 
	    {
	       window.close();
	    }
    </SCRIPT>
</head>
<body BGCOLOR="#FFFFFF">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Notas' action='Notas.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'wanop' value=".$wanop.">";
	echo "<center><input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
	if($ok == 1)
	{
		if($num > 0 and strlen($wcom) > 0)
		{
			$query =  " update costosyp_000078 set ";
			$query .=  "  Aeffec = '".$wfecha."',"; 
			$query .=  "  Aefdet = '".$wcom."' ";
			$query .=  "  where Aefano=".$wanop." and Aefcco = '".$wcco."'";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO EXPLICACIONES T78 : ".mysql_errno().":".mysql_error());
		}
		elseif(strlen($wcom) > 0)
			{
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert costosyp_000078 (medico,fecha_data,hora_data, Aefano, Aefcco, Aeffec, Aefdet, seguridad) values ('costosyp','".$fecha."','".$hora."',".$wanop.",'".$wcco."','".$wfecha."','".$wcom."','C-costosyp')";
				$err1 = mysql_query($query,$conex) or die("ERROR INSERTANDO EXPLICACIONES T78 : ".mysql_errno().":".mysql_error());
			}
	}
	if($ok != 1)
		$wcom="";
	//                 0       1       2       3        4
	$query = "SELECT Aefano, Aefcco, Aeffec, Aefdet, Cconom from costosyp_000078, costosyp_000005 ";
	$query .= " where Aefcco = '".$wcco."' ";
	$query .= "   and Aefano = ".$wanop;
	$query .= "   and Aefcco = Ccocod ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num > 0)
	{
		echo "<center><input type='HIDDEN' name= 'num' value=".$num.">";
		$row = mysql_fetch_array($err);
		$wfecha = $row[2];
		echo "<center><table border=0 align=center id=tipoG00>";
		echo "<tr><td id=tipoM03 colspan=2>NOTAS Y EXPLICACIONES</td></tr>";
		echo "<tr><td id=tipoG01>CENTRO DE COSTOS : </td><td id=tipoG02>".$row[1]." - ".$row[4]."</td></tr>";
		echo "<tr><td id=tipoG03>FECHA </td><td id=tipoG04><input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha.">&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1' style='vertical-align:middle;' ></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td id=tipoG05>TEXTO EXPLICACION</td><td id=tipoG06><textarea name='wcom' cols=80 rows=8 class=tipo3A>".$row[3]."</textarea></td></tr>";
		echo "<tr><td id=tipoM01><input type='RADIO' name=ok value=1 onclick='enter()'><b>[GRABAR]</b></td><td id=tipoM02><input type='RADIO' name=ok value=2 onclick='Cerrar()'><b>[CERRAR]</b></td>";
		echo "</table></center>";
	}
	else
	{
		$num=0;
		$wfecha = date("Y-m-d");
		echo "<center><input type='HIDDEN' name= 'num' value=".$num.">";
		$wexp="";
		$query = "SELECT Cconom from costosyp_000005 ";
		$query .= " where Ccocod = '".$wcco."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		echo "<center><table border=0 align=center id=tipoG00>";
		echo "<tr><td id=tipoM03 colspan=2>NOTAS Y EXPLICACIONES</td></tr>";
		echo "<tr><td id=tipoG01>CENTRO DE COSTOS : </td><td id=tipoG02>".$wcco." - ".$row[0]."</td></tr>";
		echo "<tr><td id=tipoG03>FECHA </td><td id=tipoG04><input type='TEXT' name='wfecha' size=10 maxlength=10 id='wfecha' readonly='readonly' value=".$wfecha.">&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1' style='vertical-align:middle;' ></td></tr>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecha',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		//]]></script>
		<?php
		echo "<tr><td id=tipoG05>TEXTO EXPLICACION</td><td id=tipoG06><textarea name='wcom' cols=80 rows=8 class=tipo3A>".$wexp."</textarea></td></tr>";
		echo "<tr><td id=tipoM01><input type='RADIO' name=ok value=1 onclick='enter()'><b>[GRABAR]</b></td><td id=tipoM02><input type='RADIO' name=ok value=2 onclick='Cerrar()'><b>[CERRAR]</b></td>";
		echo "</table></center>";
	}
}
?>
</body>
</html>
