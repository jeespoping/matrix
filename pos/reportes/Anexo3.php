<html>
<head>
  	<title>MATRIX Anexo 3 Facturacion de Empresa x Bloques</title>
</head>
<body  BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Anexo3' action='Anexo3.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfac))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>RELACION DE VENTAS POR FACTURA</td></tr>";
		echo "<tr><td  bgcolor=#cccccc>Factura Nro.</td><td bgcolor=#cccccc><INPUT TYPE='text' NAME='wfac'></td></tr>";
		$query =  " SELECT Procod, Pronom  FROM ".$empresa."_000052 where Proest='on'  ORDER BY Procod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=#cccccc>Programa : </td><td bgcolor=#cccccc><select name='wpro'>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err); 
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		}
		echo "</select></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center  colspan=10><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=10><font size=6 face='tahoma'><b>RELACION DE VENTAS POR FACTURA Ver. 1.00</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=10><font size=4 face='tahoma'><b>FACTURA NRo. : ".$wfac."</b></font></font></td></tr>";
		$query = "select Vennum, Venfec, Vennfa, Vennit, venvto, Venviv, Vencmo, Vmpmed, Vmppro ";
		$query .= " from ".$empresa."_000016,".$empresa."_000024,".$empresa."_000050 ";
		$query .= " where vennfa = '".$wfac."'";
		$query .= " and vencod = empcod  ";
		$query .= " and vennum = Vmpvta  ";
		if($wpro != "- NO APLICA")
			$query .= " and Vmppro = '".$wpro."'  ";
		$query .= " Order by Venfec, Vennum  ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$wtotg=0;
		$wven="";
		$wpac=0;
		$wt=0;
		echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Nro. Formula</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Fecha</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Factura</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Cedula<br>Paciente</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Nombre<br>Paciente</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Programa</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Valor<br>Venta</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Valor<br>Iva</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Valor<br>Cuota<br>Moderadora</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>Registro<br>Medico</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($wven != $row[0])
			{
				$wpac++;
				$wven=$row[0];
				$wt += $row[4];
			}
			$wmed=$row[7];
			$wpro=$row[8];
			$query = "select Clinom from ".$empresa."_000041  ";
			$query .= " where Clidoc = '".$row[3]."'";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wcli=$row1[0];
			}
			else
			{
				$wcli="9999";
			}
			$wtotg += $row[4];
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[1]."</font></td>";	
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[2]."</font></td>";
			echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$row[3]."</font></td>";	
			if($row[3] != "9999" and $wcli != "9999")	
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wcli."</font></td>";	
			else
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>No Especificado</font></td>";	
			if(substr($wpro,0,1) != "-")	
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wpro."</font></td>";
			else
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>No Especificado</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>".number_format((double)$row[4],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[5],2,'.',',')."</font></td>";	
			echo "<td bgcolor=".$color." align=right><font face='tahoma' size=2>$".number_format((double)$row[6],2,'.',',')."</font></td>";	
			if(substr($wmed,0,3) != "NO ")
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>".$wmed."</font></td></tr>";	
			else
				echo "<td bgcolor=".$color."><font face='tahoma' size=2>No Especificado</font></td>";
		}
		echo "<tr><td bgcolor=#999999 colspan=10><font face='tahoma' size=2><b>VALOR TOTAL : ".number_format((double)$wt,0,'.',',')."</b></font></td></tr>";	
		echo "<tr><td bgcolor=#999999 colspan=8><font face='tahoma' size=2><b>REGISTROS TOTALES : ".number_format((double)$num,0,'.',',')." PACIENTES ATENDIDOS : ".number_format((double)$wpac,0,'.',',')."</b></font></td><td bgcolor=#999999 align=right><font face='tahoma' size=2>$".number_format((double)$wtotg,2,'.',',')."</font></td><td bgcolor=#999999 align=right>&nbsp </td></tr>";	
		echo"</table>";
	}
}
?>
</body>
</html>