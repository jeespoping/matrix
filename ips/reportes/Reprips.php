<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>INFORME DE MOVIMIENTO DE RIPS</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Reprips.php Ver. 2006-10-24</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	echo "<form action='Reprips.php' method=post>";




	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($v0))
	{
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>INFORME DE MOVIMIENTO DE RIPS</b></td></tr>";
		echo "<tr><td bgcolor=#999999 align=center colspan=2><input type='RADIO' name=ti value=1  checked><b>X ENVIO</b><input type='RADIO' name=ti value=2><b>X REMISION</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center> Envio / Remision</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr></table>";

	}
	else
	{
		$ruta="../rips/".$empresa."/";
		$query = "select Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, Empnom from ".$empresa."_000118, ".$empresa."_000024 ";
		switch($ti)
		{
			case 1:
				$query .= " where Mrienv = '".$v0."' and Mriemp = Empcod order by mrirem, Mritip ";
			break;
			case 2:
				$query .= " where Mrirem = '".$v0."' and Mriemp = Empcod order by mrirem, Mritip ";
			break;
		}
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		echo "<center><table border=1>";
		echo "<tr><td colspan=7 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>INFORME DE MOVIMIENTO DE RIPS</b></td></tr>";
		echo "<tr><td colspan=7 align=center><b>POR ENVIO / REMISION</b></td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc align=center><b>Envio</b></td>";
		echo "<td bgcolor=#cccccc align=center><b>Remision</b></td>";
		echo "<td bgcolor=#cccccc align=center><b>Tipo<br>Archivo</b></td>";
		echo "<td bgcolor=#cccccc align=center><b>Empresa</b></td>";
		echo "<td bgcolor=#cccccc align=center><b>Fecha</b></td>";
		echo "<td bgcolor=#cccccc align=center><b>Estado</b></td>";
		echo "<td bgcolor=#cccccc align=center><b>Archivo</b></td>";
		echo "</tr>";
		$t=array();
		$wra="";
		$k=-1;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($wra != $row[2])
			{
				$wra = $row[2];
				$k = $k + 1;
				$t[$k] = $wra;
			}
			echo "<tr>";
			echo "<td align=center>".$row[0]."</td>";
			echo "<td>".$row[1]."</td>";
			echo "<td align=center>".$row[2]."</td>";
			echo "<td>".$row[3]."-".$row[6]."</td>";
			echo "<td>".$row[4]."</td>";
			if($row[5] == "on")
				echo "<td bgcolor=#009900 align=center><b>".$row[5]."</b></td>";
			else
				echo "<td bgcolor=#FF0000 align=center><b>".$row[5]."</b></td>";
			$files=$row[2].$row[1].".txt";
			if($row[5] == "on")
				echo "<td><b><A href=".$ruta.$files.">".$files."</A></b></td>";
			else
				echo "<td><b>".$files."</b></td>";
			echo "</tr>";
		}
	 	echo "</table></center>";
	 	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
	}
}
?>
</body>
</html>
