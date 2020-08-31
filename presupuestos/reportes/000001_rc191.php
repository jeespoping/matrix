<html>
<head>
	<title>MATRIX Analisis de Unidades Ver. 2018-02-01</title>
	<style type="text/css">
		.tipo3{color:#FFFFFF;background:#2A5DB0;font-size:10pt;font-family:Ubuntu;font-weight:bold;text-align:left;border-style:none;}
		.tipo3GRID{color:#E8EEF7;background:#E8EEF7;font-size:1pt;font-family:Arial;font-weight:bold;text-align:center;border-style:none;display:none;}
		.tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}
		.tipoL02GRID1{color:#000066;background:#C3D9FF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;}
		.tipoL02GRID2{color:#000066;background:#C3D9FF;font-size:8pt;font-family:Arial;font-weight:bold;text-align:right;height:1em;}
		.tipoL02GRID1A{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;}
		.tipoL02GRID2A{color:#000066;background:#E8EEF7;font-size:8pt;font-family:Arial;font-weight:bold;text-align:right;height:1em;}
		#tipoL06GRID{color:#000066;background:#999999;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}
		#tipoL06GRIDB{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;}
		#tipoL06GRIDL{color:#000066;background:#999999;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;}
		#tipoL06GRIDR{color:#000066;background:#999999;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;height:1em;}
		.tipoL01{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL01R{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		.tipoL02R{color:#000066;background:#E8EEF7;font-size:11pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		.tipoL02L{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		
		.tipoL011{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL051{color:#000066;background:#CCCCCC;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL05R1{color:#000066;background:#CCCCCC;font-size:11pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
		
		.tipoL02R1{color:#000066;background:#E8EEF7;font-size:11pt;font-family:Arial;font-weight:normal;text-align:right;height:2em;}
		.tipoL02L1{color:#000066;background:#E8EEF7;font-size:11pt;font-family:Arial;font-weight:normal;text-align:left;height:2em;}
		
		.tipoL02R2{color:#000066;background:#FFFFFF;font-size:11pt;font-family:Arial;font-weight:normal;text-align:right;height:2em;}
		.tipoL02L2{color:#000066;background:#FFFFFF;font-size:11pt;font-family:Arial;font-weight:normal;text-align:left;height:2em;}
		
		.tipoL02M{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		.tipoL02MC{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL03L{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:1.5em;}
		.tipoL03{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL04{color:#000066;background:#DDDDDD;foval2nt-size:12pt;font-family:Arial;font-weight:bold;text-align:left;height:2em;}
		.tipoL05{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
		.tipoL05R{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:right;height:2em;}
    	.tipotot{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;}
    	.tipotit{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;}
    	.tipouti{color:#000066;background:#81F781;font-size:12pt;font-family:Arial;font-weight:bold;}
	</style>
</head>
	<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.analisis.submit();
	}
//-->
</script>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name='analisis' action='000001_rc191.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		echo "<center><input type='HIDDEN' name= 'wanoi' value='".$wanoi."'>";
		echo "<center><input type='HIDDEN' name= 'wperi1' value='".$wperi1."'>";
		echo "<center><input type='HIDDEN' name= 'wperi2' value='".$wperi2."'>";
		echo "<center><input type='HIDDEN' name= 'wanof' value='".$wanof."'>";
		echo "<center><input type='HIDDEN' name= 'wperf1' value='".$wperf1."'>";
		echo "<center><input type='HIDDEN' name= 'wperf2' value='".$wperf2."'>";
		echo "<center><input type='HIDDEN' name= 'wccoi' value='".$wccoi."'>";
		echo "<center><input type='HIDDEN' name= 'wccof' value='".$wccof."'>";
		echo "<center><input type='HIDDEN' name= 'wgru' value='".$wgru."'>";
		echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
		if($call == "SIF")
		{
			echo "<center><input type='HIDDEN' name= 'call' value='".$call."'>";
			echo "<center><input type='HIDDEN' name= 'wrango' value='".$wrango."'>";
			$path = "000001_rc190.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."";
			$path1 = "000001_rc190.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&call=".$call."&wrango=".$wrango." ";
		}
		else
		{
			echo "<center><input type='HIDDEN' name= 'call' value='".$call."'>";
			$path = "000001_rc190.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."";
			$path1 = "000001_rc190.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&call=".$call."";
		}
		if(isset($TEC1) or isset($TEC2) or isset($TEC3))
		{
			if(isset($TEC1))
				$path .=  "&TEC1=".$TEC1;
			if(isset($TEC2))
				$path .=  "&TEC2=".$TEC2;
			if(isset($TEC3))
				$path .=  "&TEC3=".$TEC3;
		}
		
		echo "<center><table border=0>";
		echo "<tr><td align=center rowspan=2><IMG SRC='/matrix/images/medical/root/HCE01.jpg'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center colspan=3 class=tipoL03L>ANALISIS DE UNIDADES</td></tr>";
		echo "<tr><td class=tipoL02M align=center OnClick='enter()'>";
		if(isset($TEC1))
			echo "<input type='checkbox' name='TEC1' checked>Ingresos ";
		else
			echo "<input type='checkbox' name='TEC1'>Ingresos ";
		if(isset($TEC2))
			echo "&nbsp;&nbsp;<input type='checkbox' name='TEC2' checked>Resultados";
		else
			echo "&nbsp;&nbsp;<input type='checkbox' name='TEC2'>Resultados";
		if(isset($TEC3))
			echo "&nbsp;&nbsp;<input type='checkbox' name='TEC3' checked>Indicadores";
		else
			echo "&nbsp;&nbsp;<input type='checkbox' name='TEC3'>Indicadores";
		echo "<td class=tipoL02M align=center OnClick='enter()'><A HREF='".$path."' target='_top'><button type='button' class=tipoL03L>Generar</button></A></td>";
		echo "<td class=tipoL02M align=center OnClick='enter()'><A HREF='".$path1."' target='_top'><button type='button' class=tipoL03L>Retornar</button></A></td></tr></table>";
	}		
?>
</body>
</html>


