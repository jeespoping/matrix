<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Total Cirugias x Especialidad x Cirugia x Medico</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2><b> Totxexcxm.php Ver 2014-12-19</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
 @session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Totxexcxm.php' method=post>";
	if(!isset($v0) or !isset($v1))
	{
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>TOTAL CIRUGIAS X ESPECIALIDAD X CIRUGIA X MEDICO</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		//                  0       1       2       3       4       5       6     
		$query  = "select Espcod, Espdet, Mcicod, Cirdes,Mmemed, Mednom, count(*) from tcx_000008, tcx_000002, tcx_000010, tcx_000006, tcx_000005 "; 
		$query .= " where Mcifec between '".$v0."' and  '".$v1."' "; 
		$query .= "   and Mcicod = Circod ";
		$query .= "   and Circod != '0733' ";
		$query .= "   and Mcitur = Mmetur "; 
		$query .= "   and Mmemed = Medcod "; 
		$query .= "   and Medesp != '021' "; 
		$query .= "   and Medesp = Espcod ";  
		$query .= " Group by Espcod, Espdet, Mcicod, Cirdes,Mmemed, Mednom ";
		$query .= " UNION ALL ";
		$query .= "select Espcod, Espdet, Mcicod, Cirdes,Mmemed, Mednom, count(*) from tcx_000008, tcx_000002, tcx_000010, tcx_000006, tcx_000005 "; 
		$query .= " where Mcifec between '".$v0."' and  '".$v1."' "; 
		$query .= "   and Mcicod = Circod ";
		$query .= "   and Circod != '0733' ";
		$query .= "   and Circod IN ('0123C','0123B','0123A','0123','0122A','0121A','0120C','0120B','0120','0119F','0119E','0119D','0119C','0119B','0119A','0119','0065C','0065B','0065A','0065')";
		$query .= "   and Mcitur = Mmetur "; 
		$query .= "   and Mmemed = Medcod "; 
		$query .= "   and Medesp = '021' "; 
		$query .= "   and Medesp = Espcod ";  
		$query .= " Group by Espcod, Espdet, Mcicod, Cirdes,Mmemed, Mednom ";
		$query .= "  order by 1,3,7 desc ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<center><table border=1>";
		echo "<tr><td colspan=7 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>TOTAL CIRUGIAS X ESPECIALIDAD X CIRUGIA X MEDICO</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>Desde : ".$v0." Hasta ".$v1."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>COD. EPECIALIDAD</b></td>";
		echo "<td bgcolor=#cccccc><b>DES. EPECIALIDAD</b></td>";
		echo "<td bgcolor=#cccccc><b>COD. CIRUGIA</b></td>";
		echo "<td bgcolor=#cccccc><b>DES. CIRUGIA</b></td>";
		echo "<td bgcolor=#cccccc><b>IDENTIFICACION</b></td>";
		echo "<td bgcolor=#cccccc><b>MEDICO</b></td>";
		echo "<td align=right bgcolor=#cccccc><b>CANTIDAD</b></td>";
		echo "</tr>"; 
		$TotE=0;
		$TotC=0;
		$TotG=0;
		$wesp="";
		$wcir="";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($row[2] != $wcir)
			{
				if($i > 0)
					echo "<tr><td bgcolor=#cccccc colspan=6>TOTAL CIRUGIA</td><td bgcolor=#cccccc align=right>".number_format($TotC,0,'.',',')."</td></tr>";
				$TotC=0;
				$wcir=$row[2];
			}
			if($row[0] != $wesp)
			{
				if($i > 0)
					echo "<tr><td bgcolor=#cccccc colspan=6>TOTAL ESPECIALIDAD</td><td bgcolor=#cccccc align=right>".number_format($TotE,0,'.',',')."</td></tr>";
				$TotE=0;
				$wesp=$row[0];
			}
			if($i % 2 == 0)
				$color="#FFFFFF";
			else
				$color="#FFFFFF";
			$TotE += $row[6];
			$TotC += $row[6];
			$TotG += $row[6];
			echo "<tr><td bgcolor=".$color.">".$row[0]."</td><td bgcolor=".$color.">".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td><td bgcolor=".$color.">".$row[3]."</td><td bgcolor=".$color.">".$row[4]."</td><td bgcolor=".$color.">".$row[5]."</td><td bgcolor=".$color." align=right>".number_format($row[6],0,'.',',')."</td></tr>";
		}
		if($i > 0)
			echo "<tr><td bgcolor=#cccccc colspan=6>TOTAL CIRUGIA</td><td bgcolor=#cccccc align=right>".number_format($TotC,0,'.',',')."</td></tr>";
		if($i > 0)
			echo "<tr><td bgcolor=#cccccc colspan=6>TOTAL ESPECIALIDAD</td><td bgcolor=#cccccc align=right>".number_format($TotE,0,'.',',')."</td></tr>";
		if($i > 0)
			echo "<tr><td bgcolor=#cccccc colspan=6>TOTAL GENERAL</td><td bgcolor=#cccccc align=right>".number_format($TotG,0,'.',',')."</td></tr>";
		echo "</table></center>"; 
	}
}
?>
</body>
</html>
