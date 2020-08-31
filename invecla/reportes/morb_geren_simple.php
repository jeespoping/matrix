<html>
<head>
<title>Rep. Morbilidad General</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<?php
include_once("conex.php");
/********************************************************
*           Autor: Ana Maria Betancur					*
*			Fecha de Creación:2005-05-25				*
* El programa muestra los diagnosticos que se presentan *
* 	con mayor frecuencia en la institucón. Pide por 	*
* 	pantalla dos fechas limites y un numero, este 		*
*   numero es el numero de diagnosticos que se deben	* 
*  mostrar, en orden descendente segun la frecuencia. 	*

El reporte muestra los diagnosticos con su frecuencia, numero de pacientes y porcentaje sobre el volumen del paciente para el periodo, muestra la sumatoria para los diganosticos y su porcentaje sobre el total de pacientes para el periodo.
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
		/*Pedir los paámetros por pantalla*/
		if (!isset($fecha1))
		$fecha1="";
		if (!isset($fecha2))
		$fecha2="";
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>CLÍNICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>CONSOLIDADO FINAL DE MORBILIDAD</b></td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha inicial</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha1' size=10 maxlength=10 value='".$fecha1."'> AAAA-MM-DD</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha final</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='fecha2' size=10 maxlength=10 value='".$fecha2."'>AAAA-MM-DD</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Numero de diagnosticos:</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='numMax' size=2 maxlength=2 value='".$fecha2."'></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		/*Hacer el reporte*/
		echo "<center><table border=1 width='450'>";
		echo "<tr><td  align=center><font face='arial'><b>CLÍNICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td  align=center><font face='arial'><b>ÁREA INVECLA <br><font face='arial' size='2'>Sistema de Información Clínico Epidemiologico - S.I.C.E.</b></td></tr>";
		echo "<tr><td align=center><font face='arial' size='2'><b>FRECUENCIA MORBILIDAD ATENDIDA EN LA INSTITUCIÓN</TD></TR>"; 
		echo "<tr><td align=center><font face='arial' size='2'><b>$numMax DIAGNOSTICOS MAS FRECUENTES</TD></TR>"; 
		Echo "<tr><td align=center><font face='arial' size='2'><b>PERIODO $fecha1 / $fecha2</b></td></tr>";

		
		$query = "select Count(*) from cominf_000017 where Fecha_egr between '$fecha1' and '$fecha2'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		$total=$row[0];
		if ($total > 0) {
			$query = "select Count(*),Dx_ppal from cominf_000017 where (fecha_ing between '$fecha1' and '$fecha2') or (Fecha_egr between '$fecha1' and '$fecha2') group by Dx_ppal order by 1 DESC";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0){
				echo "</table><br/><br/><center><table border=1 width='600'>";
				echo "<tr><td bgcolor='#000066'><font  face='arial' color='#ffffff'><b>DIAGNOSTICO</td>";
				echo "<td bgcolor='#000066'><font  face='arial' color='#ffffff'><b># <font SIZE='2'>PACIENTES</td>";
				echo "<td bgcolor='#000066'><font  face='arial' color='#ffffff'><b>% <font SIZE='2'>SOBRE TOTAL<BR> PACIENTES PARA EL PERIODO</td></tr>";
				$i=0;
				$totalParcial=0;
				while (($i<$num) and ($i<$numMax)){
					$row = mysql_fetch_array($err);
					$i++;
					echo "<tr><td><font color='#0055A8' face='arial' size='2'><B><I>".$row[1]."</td>";
					echo "<td align='center'><font color='#0055A8' face='arial'>".$row[0]."</td>";
					echo "<td align='center'><font color='#0055A8' face='arial'>".number_format((($row[0]/$total)*100),2,"","")."</td></tr>";
					$totalParcial+=$row[0];
				}
			}
			/*Informacion de el total de la sumatoria de los anteriores*/
			echo "<tr><td bgcolor='#A4E1E8'><font face='arial' size='2'><B>TOTAL DE LOS $numMax PRIMEROS DIAGNOSTICOS</td>";
			echo "<td bgcolor='#A4E1E8' align='center'><font face='arial'><b>".$totalParcial."</td>";
			echo "<td bgcolor='#A4E1E8' align='center'><font face='arial'><b>".number_format((($totalParcial/$total)*100),2,"","")."</td></tr>";
			
			/*Informacion sobre el total*/
			echo "<tr><td bgcolor='#FFDBA8'><font face='arial' size='2'><B>TOTAL DE DIAGNOSTICOS</td>";
			echo "<td bgcolor='#FFDBA8' align='center'><font face='arial'><b>".$total."</td>";
			echo "<td bgcolor='#FFDBA8' align='center'><font face='arial'><b>100%</td></tr>";
		}else {
			Echo "<tr><td align=center><font face='arial' size='2'><b>NO HAY DIAGNOSTICOS PARA ESE PERIODO</b></td></tr>";
			echo "</table>";
		}
	}
}
include_once("free.php");
?>

</body>
</html>
		
		
		
		
		
		