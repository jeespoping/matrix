<html>
<head>
<title>MATRIX Reporte De Existencias De Productos Importados</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
include_once("root/comun.php");
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{ 
	$wactualiz = "2009-01-09";
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	encabezado( "REPORTE DE EXISTENCIAS DE PRODUCTOS IMPORTADOS", $wactualiz, $institucion->baseDeDatos );
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Importados.php' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	//                  0       1       2       3       4       5           6          
	$query  = "SELECT Artcod, Artnom, Mtavan, Mtafec, Mtavac, sum(Karexi), max(Karvuc)  from farstore_000001,farstore_000007, farstore_000026 ";
	$query .= "  where Artcod = Karcod ";
	$query .= "   and MID(Artgru,1,LOCATE('-',Artgru)-1) in ('007','207') ";
	$query .= "   and MID(Mtatar,1,LOCATE('-',Mtatar)-1) = '01' ";
	$query .= "   and MID(Mtacco,1,LOCATE('-',Mtacco)-1) = '3050' ";
	$query .= "   and MID(Mtaart,1,LOCATE('-',Mtaart)-1) = Karcod ";
	$query .= "   group by Artcod, Artnom, Mtavan, Mtafec, Mtavac ";
	$query .= "   Order by  Artnom ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	echo "<table border=0>";
	echo "<tr><td colspan=7 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
	echo "<tr><td colspan=7 align=center><b>DIRECCION DE INFORMATICA</b></td></tr>";
	echo "<tr><td colspan=7 align=center><b>REPORTE DE EXISTENCIAS DE PRODUCTOS IMPORTADOS</b></td></tr>";
	echo "<tr><td colspan=7 align=center><b>A : ".date("Y-m-d")."</b></td></tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc><b>Codigo</b></td>";
	echo "<td bgcolor=#cccccc><b>Descripcion</b></td>";
	echo "<td align=right bgcolor=#cccccc><b>Existencias</b></td>";
	echo "<td align=right bgcolor=#cccccc><b>Valor<BR>Ultima<BR>Compra</b></td>";
	echo "<td align=right bgcolor=#cccccc><b>Fecha<BR>Ultima<BR>Compra</b></td>";
	echo "<td align=right bgcolor=#cccccc><b>Tarifa de <BR>Venta</b></td>";
	echo "<td align=right bgcolor=#cccccc><b>Margen</b></td>";
	echo "</tr>"; 
	for ($i=0;$i<$num;$i++)
	{
		if($i % 2 == 0)
			$color="#99CCFF";
		else
			$color="#FFFFFF";
		$row = mysql_fetch_array($err);
		$query  = "SELECT Karfuc  from farstore_000007 ";
		$query .= "  where Karcod = '".$row[0]."'";
		$query .= "    and Karcco = '1060' ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			$wfuc=$row1[0];
		}
		else
			$wfuc="0000-00-00";
		echo "<tr>";
		echo "<td bgcolor=".$color." >".$row[0]."</td>";
		echo "<td bgcolor=".$color." >".$row[1]."</td>";
		echo "<td bgcolor=".$color." align=right>".number_format($row[5],2,'.',',')."</td>";
		echo "<td bgcolor=".$color." align=right>".number_format($row[6],2,'.',',')."</td>";
		echo "<td bgcolor=".$color." >".$wfuc."</td>";
		if($row[3] <= date("Y-m-d"))
		{
			$margen=($row[6] / $row[4]) * 100;
			echo "<td bgcolor=".$color." align=right>".number_format($row[4],2,'.',',')."</td>";
		}
		else
		{
			$margen=($row[6] / $row[2]) * 100;
			echo "<td bgcolor=".$color." align=right>".number_format($row[2],2,'.',',')."</td>";
		}
		echo "<td bgcolor=".$color." align=right>".number_format($margen,2,'.',',')."%</td>";
		echo "</tr>"; 
	}
	echo "<tr><td colspan=7 bgcolor=#cccccc><b>NUMERO DE ARTICULOS : ".$num."</b></td></tr>";
	echo "</table>"; 
}
?>
</body>
</html>
