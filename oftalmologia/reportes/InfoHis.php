<html>
<head>
  <title>MATRIX - INFORMACION GENERAL DE HISTORIA CLINICA</title>
  <style type="text/css">
    	
  		A	{text-decoration: none;color: #000066;}
  		.tipo3V{color:#000066;background:#dddddd;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#000066;background:#99CCFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	#tipoG02{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:3em;}
    	#tipoG03{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	#tipoG04{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
    	#tipoM01{color:#000066;background:#cccccc;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	#tipoM02{color:#000066;background:#dddddd;font-size:8pt;font-family:Arial;font-weight:bold;text-align:center;height:2.5em;}
    	#tipoM03{color:#000066;background:#FFFFFF;font-size:14pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	
    </style>
    <!-- BEGIN: load jqplot -->
	 <script language="javascript" type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script> 
	<script language="javascript" type="text/javascript" src="../../../include/root/excanvas.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	
	 <!--[if IE 6]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot.css" /> 
	 <![endif]--> 
	 <!--[if IE 7]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	 <![endif]--> 
	 <!--[if IE 8]>
	 <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.jqplot1.css" /> 
	 <![endif]--> 
  
  	<script language="javascript" type="text/javascript" src="../../../include/root/jquery.jqplot.min.js"></script> 
  	<script language="javascript" type="text/javascript" src="../../../include/root/jqplot.dateAxisRenderer.min.js"></script>
  	<script type="text/javascript" src="../plugins/jqplot.barRenderer.min.js"></script>
  	
  	<script type="text/javascript" src="../../../include/root/jqplot.barRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.pointLabels.min.js"></script>
	
	<script type="text/javascript" src="../../../include/root/jqplot.dateAxisRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.highlighter.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqplot.cursor.min.js"></script>
  	
	<!-- END: load jqplot -->  
    <SCRIPT>
      function Cerrar() {
        setTimeout("close();",30000);
      }
	  </SCRIPT>
	   <script language="javascript" type="text/javascript"> 
	    // nice unique id function generator which I don't use anymore.
	    var uID = (function() 
	    {
	      var id = 1;
	      return function(){return id++};
	    })();
	 
	    function run() 
	    {
		  //alert(document.getElementById('grafica').value);
	      eval(document.getElementById('grafica').value);
	    }

	</script> 
</head>
<script type="text/javascript">
<!--

	function enter()
	{
		document.forms.InfoHis.submit();
	}	
	
//-->
</script>
<body onLoad="Cerrar();" BGCOLOR="#FFFFFF">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='InfoHis' action='InfoHis.php' method=post>";
	echo "<input type='HIDDEN' name= 'pac' value='".$pac."'>";
	

	

	//                 0       1       2      3      4      5      6      7       8            9
	$query  = "select Fecha, Paciente, Sc_od, Sc_os, Cc_od, Cc_os, St_od, St_os, Pi_od_mmhg, Pi_os_mmhg,'0', Fecha from oftalmo_000003 "; 
	$query .= " where oftalmo_000003.Paciente = '".$pac."' ";
	$query .= " UNION ALL ";
	$query .= " select Fecha, Paciente, Sc_od, Sc_os, Cc_od, Cc_os, 0, 0, Pr_od_mmhg, Pr_oi_mmhg,'1', Fecha from oftalmo_000009 "; 
	$query .= " where oftalmo_000009.Paciente = '".$pac."' ";
	$query .= "   order by 1";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		$line=array();
		$wtitulo="VARIACION DE LA PRESION EN EL TIEMPO";
		for ($h=0;$h<2;$h++)
		{
			$kl=$h+1;
			$line[$h]="line".$kl."=[";
		}
		echo "<center><table border=0 align=center id=tipoG00>";
		echo "<tr><td id=tipoM03 colspan=10>HISTORIA CLINICA DE OFTALMOLOGIA</td></tr>";
		echo "<tr><td id=tipoM01 colspan=10>PACIENTE: ".$pac."</td></tr>";
		echo "<tr><td id=tipoM02></td><td id=tipoM02 colspan=2>AV CS</td><td id=tipoM02 colspan=2>AV CC</td><td id=tipoM02 colspan=2>AV SC</td><td id=tipoM02 colspan=2>PImmHg</td><td id=tipoM02>Tipo</td></tr>";
		echo "<tr><td id=tipoM02>FECHA</td><td id=tipoM02>OD</td><td id=tipoM02>OI</td><td id=tipoM02>OD</td><td id=tipoM02>OI</td><td id=tipoM02>OD</td><td id=tipoM02>OI</td><td id=tipoM02>OD</td><td id=tipoM02>OI</td><td id=tipoM02>Registro</td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<tr><td id=tipoG02>".$row[11]."</td><td id=tipoG02>".$row[2]."</td><td id=tipoG02>".$row[3]."</td><td id=tipoG02>".$row[4]."</td><td id=tipoG02>".$row[5]."</td><td id=tipoG02>".$row[6]."</td><td id=tipoG02>".$row[7]."</td><td id=tipoG02>".$row[8]."</td><td id=tipoG02>".$row[9]."</td>";
			if($row[10] == "0")
				echo "<td id=tipoG02>HISTORIA</td></tr>";
			else
				echo "<td id=tipoG02>SEGUIMIENTO</td></tr>";
			if($i == 0)
			{
				$line[0] .= "['".$row[11]."',".$row[8]."]";
				$line[1] .= "['".$row[11]."',".$row[9]."]";
			}
			else
			{
				$line[0] .= ",['".$row[11]."',".$row[8]."]";
				$line[1] .= ",['".$row[11]."',".$row[9]."]";
			}
		}
		for ($h=0;$h<2;$h++)
			$line[$h] .= "];";
		echo "<tr><td colspan=10 id=tipoM02><A HREF='#' class=tipo3V onClick='enter()'>GRAFICAR</A></td></tr>";
		echo "</table></center>";
		if(isset($graficar))
		{
			echo "<br><center><table border=0 cellspacing=0><tr><td>";
			$LINEAS="line1,line2";
			$DATOS="";
			$label=array();
			$label[0]="{label:'Pi OD mmHg'},";
			$label[1]="{label:'Pi OI mmHg'}";
			$LABELS="";
			$NL=2;
			$knum=$num;
			for ($h=0;$h<2;$h++)
			{
				$DATOS .= $line[$h];
				$LABELS .= $label[$h];
			}
			$XAL=$NL * 70;
			if($XAL < 350)
				$XAL=350;
				
			$XAN=$knum * 60;
			if($knum < 1200)
				$XAN=1200;
			
			echo "<div class='jqPlot' id='chart1' style='height:".$XAL."px; width:".$XAN."px;'></div>";
			echo "</td></tr></table></center>";
			
			echo "<input type=hidden id='grafica' value=\"".$DATOS." plot10 = $.jqplot('chart1', [".$LINEAS."], { title:'".$wtitulo."', seriesDefaults: {showMarker:true},series:[".$LABELS."],axes:{ xaxis:{renderer:$.jqplot.DateAxisRenderer,rendererOptions:{tickRenderer:$.jqplot.CanvasAxisTickRenderer},tickOptions:{formatString:'%b %#d, %Y',fontSize:'8pt',fontFamily:'Arial',angle:-30} }, yaxis:{tickOptions:{fontSize:'8pt',fontFamily:'Arial'}}},highlighter: {sizeAdjust: 7.5},cursor: {show: true},legend:{show:true, location: 'ne',xoffset: -110 }});\"> ";
			echo "<script>run();</script>";
			$graficar=1;
			echo "<input type='HIDDEN' name= 'graficar' value='".$graficar."'>";
		}
		else
		{
			$graficar=1;
			echo "<input type='HIDDEN' name= 'graficar' value='".$graficar."'>";
		}
	}
	else
		echo "LA IDENTIFICACION DEL PACIENTE NO EXISTE EN LA BASE DE DATOS<BR> CONSULTE CON SISTEMAS";
	mysql_free_result($err);
	mysql_close($conex);
}
?>
</body>
</html>