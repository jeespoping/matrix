<html>
<head>
<title>Rep. Entidad</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
/********************************************************
 *           Autor: Ana Maria Betancur					*
 *			Fecha de Creación:2005-04-19				*
 *	Reporte de las inconsistencia por entidad, en orden *
 *	descendente por valor de la facturas auditadas, 	*
 * 			con valor total de facturas auditadas, de 	*
 *		inconsistencias y valor total de las notas 		*
 ********************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='' method=post>";
	if(!isset($fecha1))
	{
		$fecha1="";
		if (!isset($fecha2))
		$fecha2="";
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2><b>REPORTE POR ENTIDAD RESPONSABLE<b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha1' size=10 maxlength=10 value='".$fecha1."'></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha2' size=10 maxlength=10 value='".$fecha2."'></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		/*Aqui empieza la impresión del reporte*/
		echo "<center><table border=1 width='700'>";
		echo "<tr><td colspan=3 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>REPORTE POR ENTIDAD RESPONSABLE</b></td></tr>";	
		echo "<tr><td colspan=3 align=center><b>Desde $fecha1 Hasta $fecha2</b></td></tr>";
		echo "</table><br/><center><table border=1 width='700'>";
			

		/*INCONSISTENCIA POR PERSONA RESPONSABLE EN LA FACTURA*/
		echo "</table><br/><br/><center><table border=1 width='700'>";
		$query = "select COUNT(calidad_000001.Responsable),calidad_000001.Responsable,SUM(calidad_000003.Valor_nota)
		 from calidad_000003,calidad_000001 where calidad_000001.fecha between '".$fecha1."' and  '".$fecha2."' and calidad_000003.factura=calidad_000001.factura group by calidad_000001.Responsable ORDER BY 1 DESC,3 DESC ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td colspan='3' align=center bgcolor=#225486><font color='#ffffff'><b>EN LA FACTURA</b></td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<tr><td colspan='3' align='center' bgcolor='#57C8D5'><b>".$row[1]."</b></td></tr>";
			
			$query1 = "select COUNT(calidad_000001.Responsable),calidad_000001.Responsable,SUM(calidad_000001.Valor) from calidad_000001 where calidad_000001.fecha between '".$fecha1."' and  '".$fecha2."' and calidad_000001.Responsable='$row[1]' group by calidad_000001.Responsable" ;
			
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1>0)
			{
				$row1 = mysql_fetch_array($err1);
				echo "<tr>";
				echo "<td align='center' bgcolor=#FFECD2><b>".$row1[0]."</b></td>";
				echo "<td bgcolor=#FFECD2><b>Total Facturas Auditadas </b></td>";
				echo "<td bgcolor=#FFECD2><b> $".number_format($row1[2],0,'',',')." </b></td>";
				echo "</tr>";
			}
			
			echo "<tr><td align='center' bgcolor=#FFECD2><b>".$row[0]."</b></td>";
			echo "<td bgcolor=#FFECD2><b>Inconsistencias Totales</b></td>";
			echo "<td bgcolor=#FFECD2><b> $".number_format($row[2],0,'',',')." </b></td>";
			echo "<tr>";
			echo "<td bgcolor=#A4E1E8 align='center'><b>Cantidad</b></td>";
			echo "<td bgcolor=#A4E1E8><b>Tipo Nota </b></td>";
			echo "<td bgcolor=#A4E1E8><b>Valor Total Nota </b></td>";
			echo "</tr>";
			$query = "select COUNT(calidad_000003.Tipo_nota), calidad_000003.Tipo_nota,Sum(calidad_000003.Valor_nota) from calidad_000003, calidad_000001 where  calidad_000001.fecha between '".$fecha1."' and  '".$fecha2."' and calidad_000001.Responsable='".$row[1]."' and calidad_000003.factura=calidad_000001.factura group by calidad_000003.Tipo_nota ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				echo "<tr>";
				echo "<td align='center'><font color='#0055A8'><i>".$row1[0]."</i></td>";
				echo "<td><font color='#0055A8'><i>".ucwords(strtolower(substr($row1[1],3)))."</i></td>";
				echo "<td><font color='#0055A8'><i>".number_format($row1[2],0,'',',')."</i></td>";
				echo "</tr>";
			}			
			echo "</tr>";
		}		
	}
}
include_once("free.php");
?>
</body>
</html>