<?php
include_once("conex.php");
$registro  = "<html>";
$registro .= "<head>";
$registro .= "  	<title>MATRIX</title>";
$registro .= "  	<style type='text/css'>";
$registro .= "		.tipoTABLE{font-family:Arial;border-style:solid;border-collapse:collapse;}";
$registro .= "		#tipoT01L{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}";
$registro .= "		#tipoT01C{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}";
$registro .= "		#tipoT01R{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}";
$registro .= "		#tipoT02L{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}";
$registro .= "		#tipoT02C{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}";
$registro .= "		#tipoT02R{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}";
$registro .= "		#tipoT03L{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:left;}";
$registro .= "		#tipoT03C{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}";
$registro .= "		#tipoT03R{color:#000066;background:#DDDDDD;font-size:10pt;font-family:Arial;font-weight:bold;text-align:right;}";
$registro .= "		#tipoT04C{color:#000066;background:#CCCCCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}";
$registro .= "		#tipoT05R{color:#000066;background:#F5A9A9;font-size:9pt;font-family:Arial;font-weight:bold;text-align:right;}";
$registro .= "		#tipoT06C{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:normal;text-align:center;}";
$registro .= "		#tipoT06R{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:normal;text-align:right;}";
$registro .= "	</style>";
$registro .= "</head>";

	echo "<form name='size1' action='size1.php' method=post>";
	

	

	$tfil=0;
	$tram=0;
	$ram=array();
	$ram[0]="bytes";
	$ram[1]="KB";
	$ram[2]="MB";
	$ram[3]="GB";
	$ram[4]="TB";
	$query = "show table status ";
	$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num1 = mysql_num_rows($err1);
	if($num1 > 0)
	{
		$registro .= "<center><table border=1 class='tipoTABLE'>";
		$registro .= "<tr><td colspan=4 id='tipoT04C'>STATUS: BASE DE DATOS MATRIX ".date("Y-m-d")."</td></tr>";
		$registro .= "<tr><td id='tipoT03C'>INDICE</td><td id='tipoT03C'>NOMBRE<br>TABLA</td><td id='tipoT03C'>NUMERO DE<br>FILAS</td><td id='tipoT03C'>TAMA&Ntilde;O</td></tr>";
		for ($i=0;$i<$num1;$i++)
		{
			$j=0;
			$row1 = mysql_fetch_array($err1);
			$dim=$row1[6]+$row1[8];
			$tfil += $row1[4];
			$tram += $dim;
			while($dim > 1024)
			{
				$j++;
				$dim=$dim/1024;
			}
			$z=$i+1;
			if($z % 2 == 0)
				$color="tipoT01";
			else
				$color="tipoT02";
			if(($j == 2 and $dim > 500) or $j > 2)
				$color1="tipoT05";
			else
				$color1=$color;
			$registro .= "<tr><td id=".$color."C>".$z."</td><td id=".$color."L>".$row1[0]."</td><td id=".$color."R>".number_format((double)$row1[4],0,',','.')."</td><td id=".$color1."R>".number_format((double)$dim,1,'.',',')." ".$ram[$j]."</td></tr>";
		}
		$vm=0;
		$j=0;
		while($tram > 1024)
		{
			if($j == 2)
				$vm = $tram;
			$j++;
			$tram=$tram/1024;
		}
		$registro .= "<tr><td id='tipoT06C'>TOTAL GENERAL</td><td id='tipoT06R'>".number_format((double)$vm,2,',','.')." ".$ram[2]."</td><td id='tipoT06R'>".number_format((double)$tfil,0,',','.')."</td><td id='tipoT06R'>".number_format((double)$tram,1,'.',',')." ".$ram[$j]."</td></tr>";
		$registro .= "</table></center>";
	}
	$registro .= "</body>";
	$registro .= "</html>";
	$datafile="/var/www/matrix/planos/root/size.html";
	$file = fopen($datafile,"w+");
	fwrite ($file,$registro);
	fclose ($file);
?>

