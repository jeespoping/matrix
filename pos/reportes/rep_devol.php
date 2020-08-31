<html>
<head>
<title>Reporte de devoluciones</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<?php
include_once("conex.php");

/********************************************************
*     REPORTE DE DEVOLUCIONES							*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de devoluciones	 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:NOVIEMBRE 2006
//FECHA ULTIMA ACTUALIZACION 	:23 de Noviembre de 2006
//DESCRIPCION					:
//								 
//==================================================================================================================================
$wactualiz="Ver. 2006-11-23";


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	//$empresa='farstore';
	
	

	


	echo "<form action='' method=post>";
	
	
if(!isset($fecha2))
	{
		if (!isset($fecha1))
		$fecha1="";
		if (!isset($fecha2))
		$fecha2="";
		
		echo  "<center><table border=1>";
		echo "<tr><td  colspan=2 align=center><img SRC='/MATRIX/images/medical/pos/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td colspan=2 align=center><b>REPORTE DE DEVOLUCIONES</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha1' size=10 maxlength=10 value='".$fecha1."'> AAAA-MM-DD</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha2' size=10 maxlength=10 value='".$fecha2."'>AAAA-MM-DD</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	
	}else
		{
		$query =  " SELECT Rfpfue, Rfpnum, Rfpdan, Rdefac
                FROM ".$empresa."_000022, ".$empresa."_000021
              	WHERE ".$empresa."_000022.Fecha_data between '".$fecha1."' AND '".$fecha2."'
              	AND Rfpfpa='BO'
              	AND Rdefue=Rfpfue
              	AND Rdenum=Rfpnum
              	AND Rdecco=Rfpcco
              	ORDER BY Rfpnum";
		 $err = mysql_query($query,$conex);
		 $num = mysql_num_rows($err);
		 
		 	echo  "<center><table border=1 bgcolor=666666 >";
			echo "<tr  ><td  colspan=4 align=center><img SRC='/MATRIX/images/medical/pos/logo_".$empresa.".png'></td></tr>";
			echo "<tr><td colspan=4 align=center><font color=FFFFFF><I><b>-REPORTE DE DEVOLUCIONES-</b></td></tr>";
		 	echo "<tr><td align=center><font color=FFFFFF><I><b>-FUENTE DEL RECIBO-</b></td><td align=center><font color=FFFFFF><I><b>-NUMERO DEL RECIBO-</b></td><td align=center><font color=FFFFFF><I><b>-NUMERO DEL BONO-</b></td><td align=center><font color=FFFFFF><I><b>-FACTURA-</b></td></tr>";
			 for ($i=1;$i<=$num;$i++)
	        {
	        	
	        	if (is_int ($i/2))
                    $wcf="DDDDDD";
                    else
                    $wcf="CCFFFF";
                    
			 $row = mysql_fetch_array($err);
			 echo "<tr bgcolor=".$wcf."><td align=center>".$row[0]."&nbsp</td><td align=center>".$row[1]."&nbsp</td><td align=center>".$row[2]."&nbsp</td><td align=center>".$row[3]."&nbsp</td></tr>";
			}
		}
}

?>	