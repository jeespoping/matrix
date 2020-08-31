<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7.5pt;font-family:Tahoma;font-weight:normal;}
  </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center id=tipo1>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba">Reporte de Costos Reales x Programas Detalle</a></tr></td>
<tr><td align=center bgcolor="#cccccc"><b>000001_rc126.php Ver. 2007-05-10</b></tr></td></table>
</center>

<?php
include_once("conex.php");

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc126.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wmesi))
			$wmesi=$wmesf;
		$query  = "select Prgdes,Expcco,Cconom,Mganom,Expnte, Expexp, Expper,sum(Expmon) ";
		$query .= " from ".$empresa."_000011,".$empresa."_000127,".$empresa."_000005,".$empresa."_000028 "; 
		$query .= " where expano = ".$wano;
		$query .= "   and expper between ".$wmesi." and ".$wmesf;
		$query .= "   and exppro = '".$wpro."'";
		$query .= "   and exppro = prgcod  ";
		$query .= "   and expcco = ccocod ";
		if(isset($wrub))
			$query .= "   and expcpr = '".$wrub."'";
		$query .= "   and expcpr = mgacod "; 
		$query .= " group by Exppro,Prgdes,Expcco,Cconom,Expcpr,Mganom,Expnte, Expexp, Expper "; 
		$query .= " order by exppro,expcpr,Expcco,Expnte,Expper ";
		$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$ncol=8;
			echo "<table border=0 align=center id=tipo1>";
			echo "<tr><td colspan=".$ncol." align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>REPORTE DE COSTOS REALES X PROGRAMAS DETALLE</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>DESDE EL MES ".$wmesi." A�O ".$wano." HASTA EL MES ".$wmesf." A�O ".$wano."</td></tr>";
			echo "<tr><td colspan=".$ncol." align=center>PROGRAMA : ".$wpro."</td></tr>";
			echo "<tr><td bgcolor=#cccccc><b>PROGRAMA</b></td><td bgcolor=#cccccc><b>C.C.</b></td><td bgcolor=#cccccc><b>DESCRIPCION</b></td><td bgcolor=#cccccc><b>RUBRO</b></td><td bgcolor=#cccccc><b>TERCERO</b></td><td bgcolor=#cccccc><b>EXPLICACION</b></td><td bgcolor=#cccccc><b>MES</b></td><td bgcolor=#cccccc><b>MONTO</b></td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				echo "<tr><td bgcolor=".$color.">".$row[0]."</td><td bgcolor=".$color.">".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td><td bgcolor=".$color.">".$row[3]."</td><td bgcolor=".$color.">".$row[4]."</td><td bgcolor=".$color.">".$row[5]."</td><td bgcolor=".$color.">".$row[6]."</td><td bgcolor=".$color." align=right>".number_format((double)$row[7],0,'.',',')."</td></tr>";
			}
		}
	}
?>
</body>
</html>
