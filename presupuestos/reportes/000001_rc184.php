<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}
    	.tipotot{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;}
    	.tipotit{color:#000066;background:#dddddd;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tipouti{color:#000066;background:#C3D9FF;font-size:10pt;font-family:Arial;font-weight:bold;}
    	.tipolin1{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;}
    	.tipolin2{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Arial;font-weight:normal;}
    	.tiponak{color:#000066;background:#F5A9A9;font-size:9pt;font-family:Arial;font-weight:normal;}
    </style>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Detalle Evaluacion x Linea - Tipo</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc184.php Ver. 2017-08-30</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_rc184.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wano) or !isset($wcco1) or !isset($wcco2) or !isset($wper1) or !isset($wper2) or $wper1 < 1 or $wper1 > 12 or $wper2 < 1 or $wper2 > 12 or $wper1 > $wper2)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>DETALLE EVALUACION X LINEA - TIPO</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Final de Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wempt = $wemp;
		$wemp = substr($wemp,0,2);
		
		//                  0       1      2          3           4           5           6          7            8   
		$query  = "select Moslin,Lindes,Mostip,sum(Mosipr),sum(Mosite),sum(Mosctt),sum(Mosutt),sum(Mosctv),sum(Mosutv) ";
		$query .= "     from ".$empresa."_000108, ".$empresa."_000107 ";
		$query .= "  	  where mosano = ".$wano;
		$query .= "  	    and mosmes between ".$wper1." and ".$wper2." ";
		$query .= "  	    and moscco between '".$wcco1."' and '".$wcco2."' ";
		$query .= "  	    and moslin = Lincod ";
		$query .= "  group by 1,2,3 ";
		$query .= "  order by CAST(moslin as UNSIGNED),3 ";


		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			echo "<table border=1 class=tipoTABLEGRID>";
			echo "<tr><td colspan=9 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=9 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=9 align=center>DETALLE EVALUACION X LINEA - TIPO</td></tr>";
			echo "<tr><td colspan=9 align=center>A&Ntilde;O : ".$wano."</td></tr>";
			echo "<tr><td colspan=9 align=center>MES INICIAL : ".$wper1. " MES FINAL : ".$wper2."</td></tr>";
			echo "<tr><td colspan=9 align=center>UNIDAD INICIAL : ".$wcco1. " UNIDAD FINAL : ".$wcco2."</td></tr>";
			echo "<tr class='tipotit'><td>LINEA</td><td>DESCRIPCION</td><td>TIPO</td><td>INGRESO<br>PROPIO</td><td>INGRESO<br>TERCEROS</td><td>COSTO<br>TOTAL TOTAL</td><td>UTILIDAD<br>TOTAL TOTAL</td><td>COSTO<br>TOTAL VARIABLE</td><td>UTILIDAD<br>TOTAL VARIABLE</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<tr class='".$clase."'><td>".$row[0]."</td><td>".$row[1]."</td><td>".$row[2]."</td><td align=right>".number_format((double)$row[3],0,'.',',')."</td><td align=right>".number_format((double)$row[4],0,'.',',')."</td><td align=right>".number_format((double)$row[5],0,'.',',')."</td><td align=right>".number_format((double)$row[6],0,'.',',')."</td><td align=right>".number_format((double)$row[7],0,'.',',')."</td><td align=right>".number_format((double)$row[8],0,'.',',')."</td></tr>";
				
			}
		}
	}
}
?>
</body>
</html>
