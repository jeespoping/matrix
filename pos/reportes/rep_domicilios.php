<html>
<head>
  	<title>Reporte de Domicilios</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">


<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Reporte de domicilios
//AUTOR							:Juan David Londoño
//FECHA CREACION				:2007-05-25
//FECHA ULTIMA ACTUALIZACION 	:2007-05-25
$wactualiz="2007-05-25";
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
// xxxx				 
//==================================================================================================================================
// xxxx
//==================================================================================================================================



session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	
	

	


	echo "<form name=seg_dispositivos action='' method=post>";
	//$wbasedato='cominf';
	// ENCABEZADO
	if (!isset ($fecha2) or $fecha2=='')
	
	{
	   	
	    echo "<br><br><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/pos/logo_farstore.png' WIDTH=340 HEIGHT=100></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>REPORTE DE DOMICILIOS</font></td></tr>";
	    echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='fecha1'></td>";
        echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='fecha2'></td>";
        echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";

	}
	////////////////////////////////////////////////////////apartir de aca comienza la impresion
	else
	{
	      $query= " SELECT Venfec, Vennum, Vennfa, Venvto, Venmsj
	      		 	  FROM ".$wbasedato."_000016
		         	 WHERE Ventve = 'Domicilio'
			       	   AND Venfec between '".$fecha1."' and '".$fecha2."'
			      ORDER BY 1 ";
	    $err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($err);
		//echo mysql_errno() ."=". mysql_error();
//echo $num;
		  echo "<center><table border=0>";// este es el encabezado del resultado
		  echo "<tr><td align=center colspan=7><font size=5><img src='/matrix/images/medical/pos/logo_farstore.png' WIDTH=340 HEIGHT=100></font></td></tr>";
		  echo "<tr><td><br></td></tr>";
		  echo "<tr><td align=center colspan=7><font size=5>REPORTE DE DOMICILIOS</font></td></tr>";
		  echo "<tr><td align=center colspan=7>Desde: <b>".$fecha1."</b> hasta <b>".$fecha2."</b></td></tr>";
		  echo "<tr><td>&nbsp</td></tr>";
		  echo "<tr><td>&nbsp</td></tr></table>";
		  
		  
  	  	 echo "<table border=1>";
		 echo "<tr bgcolor=#dddddd><td align=center><font size=2><b>FECHA DE LA VENTA</b></td><td align=center><font size=2><b>Nº VENTA</b></td><td align=center><font size=2><b>Nº FACTURA</b></td>";
		 echo "<td align=center><font size=2><b>VALOR VENTA</b></font></td><td align=center><font size=2><b>MENSAJERO</b></font></td></tr>";
		 
		 $total=0;
		 for ($i=1;$i<=$num;$i++) 
	     {
	  		$row = mysql_fetch_array($err);
		  	if (is_int ($i/2))
		       $wcf="DDDDDD";
		    else
		       $wcf="FFFFFF";
		  	echo "<tr bgcolor=".$wcf." border=1><td align=left>".$row[0]."</td><td align=left>".$row[1]."</td><td align=left>".$row[2]."</td><td align=right>".number_format($row[3],0,'.',',')."</td><td align=left>".$row[4]."</td></tr>";
		  	$total=$total+$row[3];
	     }
	     echo "<tr bgcolor=#dddddd><td align=center colspan=2><font size=2><b>TOTAL VENTAS: ".$num."</b></td><td align=center><font size=2><b>&nbsp</b></td><td align=right><font size=2><b>".number_format($total,0,'.',',')."</b></td><td align=center><font size=2><b>&nbsp</b></td></tr>";
		 
 	}
}
?>
</body>
</html>
