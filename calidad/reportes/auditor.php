<html>
<head>
<title>Rep. Auditor</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<?php
include_once("conex.php");
/********************************************************
 *           Autor: Ana Maria Betancur					*
 *			Fecha de Creación:2005-04-19				*
 *	Reporte que permite ver la cantidad de facturas 	*
 *	y prefacturas revisadas por unidad y auditor		*
 *	incluyendo los montos de las prefacturas o facturas,* 
 *		de las inconsistencias y los tipos de notas		*
 *********************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='' method=post>";
	if(!isset($fecha1))
	{
		if (!isset($fecha1))
		$fecha1="";
		if (!isset($fecha2))
		$fecha2="";
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>INCONSISTENCIAS EN LA FACTURACIÓN</b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>REPORTE POR PERSONA Y UNIDAD QUE AUDITA</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha1' size=10 maxlength=10 value='".$fecha1."'></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha2' size=10 maxlength=10 value='".$fecha2."'></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<center><table border=1 width='700'>";
		echo "<tr><td colspan=3 align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>INCONSISTENCIAS EN LA FACTURACIÓN</b></td></tr>";
		echo "<tr><td colspan=3 align=center><b>REPORTE POR PERSONA Y UNIDAD QUE AUDITA</b></td></tr>";	
		echo "<tr><td colspan=3 align=center><b>Desde $fecha1 Hasta $fecha2</b></td></tr>";
		echo "</table><br/><br/><center><table border=1 width='700'>";
		echo "<tr><td colspan=3 align=center bgcolor=#225486><font color='#ffffff'><b>EN LA PREFACTURA</b></td></tr><tr></tr>";
		
		$query = "select COUNT(calidad_000005.Auditor_a),calidad_000005.Unidad_audito,calidad_000005.Auditor_a,SUM(calidad_000005.Valor_nota),SUM(calidad_000004.Valor) from calidad_000005,calidad_000004 where calidad_000004.fecha_data between '".$fecha1."' and  '".$fecha2."' and calidad_000005.Historia=calidad_000004.Historia and calidad_000005.Ingreso=calidad_000004.Ingreso group by calidad_000005.Auditor_a ORDER BY 2,1 DESC,3 DESC";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		
		$sum_inc=0;
		$prom=0;
		$unidad="";
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if ($unidad != ucwords(strtolower(substr($row[1],3))))
			{
				if($unidad != "")
				{
					echo "<tr>";
					echo "<td align='center'  bgcolor=#FFDBA8><b>$numNot</b></td>";
					echo "<td  bgcolor=#FFDBA8><b>Valor Total Prefacturas Auditadas por $unidad $".number_format($valAudTot,0,'',',')." </b></td>";
					echo "<td  bgcolor=#FFDBA8><b>".number_format($valTot,0,'',',')."</b></td>";
					echo "</tr><tr></tr>";
				}
				$numNot=$row[0];
				$valTot=$row[3];
				$valAudTot=$row[4];
				$string=ucwords(strtolower(substr($row[1],3)));
					include_once("tipo_titulo_guion.php");
				echo "<tr><td colspan=3 align=center bgcolor=#57C8D5><b>".$string."</b></td></tr>";
				echo "<tr>";
				echo "<td bgcolor=#A4E1E8><b>Cant. Inc.</b></td>";
				echo "<td bgcolor=#A4E1E8><b>Auditor a</b></td>";
				echo "<td bgcolor=#A4E1E8><b>Valor total Inc.</b></td>";
				echo "</tr>";
				$unidad=ucwords(strtolower(substr($row[1],3)));
			}
			else 
			{
				$numNot += $row[0];
				$valTot += $row[3];
				$valAudTot += $row[4];
			}	
			echo "<tr>";
			echo "<td align='center'><b>".$row[0]."</b></td>";
			$string=ucwords(strtolower($row[2]));
					include_once("tipo_titulo_guion.php");
			//echo "<td><b>".ucwords(strtolower($row[2]))."</b></td>";
			echo "<td><b>".$string." / Valor Total Auditado $".number_format($row[4],0,'',',')."</b></td>";
			echo "<td><b>".number_format($row[3],0,'',',')."</b></td>";
			//echo "<td><b>".number_format($row[4],0,'',',')."</b></td>";
			
			$query = "select COUNT(calidad_000005.Tipo_nota), calidad_000005.Tipo_nota,Sum(calidad_000005.Valor_nota) from calidad_000005, calidad_000004 where  calidad_000004.fecha_data between '".$fecha1."' and  '".$fecha2."'and calidad_000005.Historia=calidad_000004.Historia and calidad_000005.Ingreso=calidad_000004.Ingreso and calidad_000005.Auditor_a='$row[2]' and calidad_000005.Unidad_audito='$row[1]' group by calidad_000005.Tipo_nota ";
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
			$prom=$prom+$num1;
			$sum_inc=$sum_inc+$row[2];
			echo "</tr>";
		}
		echo "<tr>";
					echo "<td align='center'  bgcolor=#FFDBA8><b>$numNot</b></td>";
		echo "<td  bgcolor=#FFDBA8><b>Valor Total Prefacturas Auditadas por $unidad $".number_format($valAudTot,0,'',',')."</b></td>";
					echo "<td  bgcolor=#FFDBA8><b>".number_format($valTot,0,'',',')."</b></td>";
					echo "</tr><tr></tr>";

		
		/*FIN DE PREFACTURA*/

		/*INCONSISTENCIA POR PERSONA RESPONSABLE EN LA FACTURA*/
		echo "</table><br/><br/><center><table border=1 width='700'>";
		$query = "select COUNT(calidad_000003.Auditor_a),calidad_000003.Unidad_audito,calidad_000003.Auditor_a,SUM(calidad_000003.Valor_nota),SUM(calidad_000001.Valor) from calidad_000003,calidad_000001 where calidad_000001.fecha between '".$fecha1."' and  '".$fecha2."' and calidad_000003.factura=calidad_000001.factura group by calidad_000003.Auditor_a ORDER BY 2,1 DESC,3 DESC ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		echo "<tr><td colspan=3 align=center bgcolor='#225486'><font color='#ffffff'><b>EN LA FACTURA</b></td></tr>";
		$unidad="";
		$sum_inc=0;
		$prom=0;
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if ($unidad != ucwords(strtolower(substr($row[1],3))))
			{
				if($unidad != "")
				{
					echo "<tr>";
					echo "<td align='center'  bgcolor=#FFDBA8><b>$numNot</b></td>";
					echo "<td   bgcolor=#FFDBA8><b>Valor Facturas Totales Auditadas Por $unidad $".number_format($valAudTot,0,'',',')."</b></td>";
					echo "<td   bgcolor=#FFDBA8><b>".number_format($valTot,0,'',',')."</b></td>";
					echo "</tr>";
				}
				$numNot=$row[0];
				$valTot=$row[3];
				$string=ucwords(strtolower(substr($row[1],3)));
				$valAudTot=$row[4];
					include_once("tipo_titulo_guion.php");
				echo "<tr><td colspan=3 align=center bgcolor='#57C8D5'><b>".$string."</b></td></tr>";
				echo "<tr>";
				echo "<td bgcolor='#A4E1E8'><b>Cant. Inc.</b></td>";
				echo "<td bgcolor='#A4E1E8'><b>Auditor a</b></td>";
				echo "<td bgcolor='#A4E1E8'><b>Valor total Inc.</b></td>";
				echo "</tr>";
				$unidad=ucwords(strtolower(substr($row[1],3)));
			}	
			else 
			{
				$numNot += $row[0];
				$valTot += $row[3];
				$valAudTot += $row[4];
			}
			echo "<tr>";
			echo "<td align='center'><b>".$row[0]."</b></td>";
			$string=ucwords(strtolower($row[2]));
					include_once("tipo_titulo_guion.php");
			//echo "<td><b>".ucwords(strtolower($row[2]))."</b></td>";
			echo "<td><b>".$string."/ Valor Total Auditado $".number_format($row[4],0,'',',')."</b></td>";
			echo "<td><b>".number_format($row[3],0,'',',')."</b></td>";
			
			$query = "select COUNT(calidad_000003.Tipo_nota), calidad_000003.Tipo_nota,Sum(calidad_000003.Valor_nota),SUM(calidad_000001.Valor) from calidad_000003, calidad_000001 where  calidad_000001.fecha between '".$fecha1."' and  '".$fecha2."'and calidad_000003.factura=calidad_000001.factura and calidad_000003.Unidad_audito='$row[1]' and calidad_000003.Auditor_a='$row[2]'  group by calidad_000003.Tipo_nota ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				echo "<tr>";
				echo "<td align='center'><font color='#0055A8'><i>".$row1[0]."</i></td>";
				echo "<td><font color='#0055A8'><i>".ucwords(strtolower(substr($row1[1],3)))." </i></td>";
				echo "<td><font color='#0055A8'><i>".number_format($row1[2],0,'',',')."</i></td>";
				echo "</tr>";
			}
			$prom=$prom+$num1;
			$sum_inc=$sum_inc+$row[2];
			echo "</tr>";
		}
		echo "<tr>";
					echo "<td align='center'   bgcolor=#FFDBA8><b>$numNot</b></td>";
					echo "<td   bgcolor=#FFDBA8><b>Valor  Total Facturas Auditadas por $unidad $".number_format($valAudTot,0,'',',')."</b></td>";
					echo "<td   bgcolor=#FFDBA8><b>".number_format($valTot,0,'',',')."</b></td>";
					echo "</tr>";
		echo "</table><BR>";
			
		
		

		
	}
}
include_once("free.php");
?>
</body>
</html>
		
		
		
		
		
		