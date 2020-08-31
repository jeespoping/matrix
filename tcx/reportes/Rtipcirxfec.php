<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Tipos de Cirugias x Fecha X Uci</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Rtipcirxfec.php Ver 2009-01-05</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Rtipcirxfec.php' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($v0) or !isset($v1))
	{
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>Tipos de Cirugias x Fecha</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "select Turtcx, Turuci, count(*) from ".$empresa."_000011 where turfec between  '".$v0."' and  '".$v1."' group by turtcx, turuci";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<center><table border=0>";
		echo "<tr><td colspan=3 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>TIPOS DE CIRUGIAS X FECHA</b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>X UCI DESDE ".$v0." HASTA ".$v1."</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc><b>Tipo<br>Cirugia</b></td>";
		echo "<td bgcolor=#cccccc><b>Uci</b></td>";
		echo "<td align=right bgcolor=#cccccc><b>Total<br>Cirugias</b></td>";
		echo "</tr>"; 
		$t=array();
		$t[0] = 0;
		$t[1] = 0;
		$t[2] = 0;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$tipo="#99CCFF";
			else
				$tipo="#FFFFFF";
			echo "<tr>";
			switch ($row[0])
			{
				case "A":
					echo "<td bgcolor=".$tipo.">AMBULATORIO</td>";
				break;	
				case "H":
					echo "<td bgcolor=".$tipo.">HOSPITALIZADO</td>";
				break;	
				case "E":
					echo "<td bgcolor=".$tipo.">ESPECIAL</td>";
				break;	
			}
			switch ($row[1])
			{
				case "on":
					echo "<td bgcolor=".$tipo.">SI</td>";
				break;
				case "off":
					echo "<td bgcolor=".$tipo.">NO</td>";
				break;
			}
			$t[2]+=$row[2];
			echo "<td bgcolor=".$tipo." align=right>".number_format($row[2],0,'.',',')."</td>";
			echo "</tr>"; 
		}
		echo "<tr><td  bgcolor=#999999 colspan=2 align=center><b>TOTALES</b></td><td bgcolor=#999999 align=right><b>".number_format($t[2],0,'.',',')."</b></td></tr>";
		echo "</table></center>"; 
	}
	}
?>
</body>
</html>