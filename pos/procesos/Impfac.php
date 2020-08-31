<html>
<head>
  	<title>MATRIX Impresion de Facturas y Anexos</title>
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
	echo "<form name='Impfac' action='Impfac.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($wfini) or !isset($wffin))
	{
		$wcolor="#cccccc";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>IMPRESION DE FACTURAS Y ANEXOS</td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Fecha Inicial (AAAA-MM-DD) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wfini' value = '".date("Y-m-d")."'></td></tr>";
		echo "<tr><td  bgcolor=".$wcolor.">Fecha Final (AAAA-MM-DD) : </td><td bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wffin' value = '".date("Y-m-d")."'></td></tr>";
		$query =  " SELECT empres, empnom FROM ".$empresa."_000024  where Empfac= 'off' ORDER BY empcod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td bgcolor=".$wcolor.">Empresa : </td><td bgcolor=".$wcolor."><select name='wemp'>";
		for ($i=0;$i<$num;$i++)
		{
		      $row = mysql_fetch_array($err); 
		      echo "<option>".$row[0]."-".$row[1]."</option>";
		 }
		 echo "</select></td></tr>";
		 $query =  " SELECT ccocod, ccodes  FROM ".$empresa."_000003  ORDER BY ccocod ";
		 $err = mysql_query($query,$conex);
		 $num = mysql_num_rows($err);
		 echo "<tr><td bgcolor=".$wcolor.">Centro de Costo : </td><td bgcolor=".$wcolor."><select name='wcco'>";
		 for ($i=0;$i<$num;$i++)
		 {
			$row = mysql_fetch_array($err); 
			echo "<option>".$row[0]."-".$row[1]."</option>";
		 }
		 echo "</select></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<table border=0 align=center>";
		echo "<tr><td align=center  colspan=5><IMG SRC='/matrix/images/medical/Pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=center bgcolor=#999999 colspan=5><font size=6 face='tahoma'><b>IMPRESION DE FACTURAS Y ANEXOS Ver. 2007-11-23</font></b></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=5><font size=4 face='tahoma'><b>PERIODO : ".$wfini." - ".$wffin."</b></font></font></td></tr>";
		echo "<tr><td align=center bgcolor=#cccccc colspan=5><font size=4 face='tahoma'><b>EMPRESA : ".$wemp."</b></font></font></td></tr>";
		$query = "select Fenfac,Vencco from farstore_000018,farstore_000016  ";
		$query .= " where Fenfec between '".$wfini."' and '".$wffin."'  ";
		$query .= " and Fencod = '".substr($wemp,0,strpos($wemp,"-"))."'  ";
		$query .= " and Fenfac = Vennfa ";
		$query .= " and Vencco = '".substr($wcco,0,strpos($wcco,"-"))."'  "; // CAMBIO 2007-11-23
		$query .= " and Fenest = 'on' ";
		$query .= " Group by Fenfac,Vencco ";
		$query .= " order by Fenfac ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>FACTURA NRO</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>IMPRIMIR<BR>FACTURA</b></font></td><td align=center bgcolor=#dddddd><font face='tahoma' size=2><b>IMPRIMIR<BR>ANEXO 1</b></font></td><td align=center bgcolor=#dddddd ><font face='tahoma' size=2><b>IMPRIMIR<BR>ANEXO 2</b></font></td><td align=center bgcolor=#dddddd ><font face='tahoma' size=2><b>IMPRIMIR<BR>ANEXO 3</b></font></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($i % 2 == 0)
				$color="#9999FF";
			else
				$color="#ffffff";
			echo "<tr><td bgcolor=".$color."><font face='tahoma' size=2>".$row[0]."</font></td><td bgcolor=".$color."><font face='tahoma' size=2><A HREF='Imp_facemp.php?wbasedato=farstore&wnrofac=".$row[0]."&wcco=".$row[1]."' target = '_blank'>Factura</font></td><td bgcolor=".$color." align=right><font face='tahoma' size=2><A HREF='/matrix/pos/reportes/anexo1.php?empresa=farstore' target = '_blank'>Anexo 1</font></td><td bgcolor=".$color." align=right><font face='tahoma' size=2><A HREF='/matrix/pos/reportes/anexo2.php?empresa=farstore' target = '_blank'>Anexo 2</font></td><td bgcolor=".$color." align=right><font face='tahoma' size=2><A HREF='/matrix/pos/reportes/anexo3.php?empresa=farstore' target = '_blank'>Anexo 3</font></td></tr>";	
		}
		echo"</table>";
	}
}
?>
</body>
</html>