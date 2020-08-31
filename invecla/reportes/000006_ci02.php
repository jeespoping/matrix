 <title>REPORTE APACHE II</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

  /***************************************************
	*  REPORTE DEL APACHE ENTRE DOS FECHAS 	V.1.00 	 *
	*					CONEX, FREE =>	OK			 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($year1)  or !isset($year2))
	{
		echo "<form action='000006_ci02.php' method=post>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>APACHE II</td></tr>";
		echo "<tr></tr>";
		
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESDE: </font></td>";	
		echo "<td bgcolor=#cccccc  >";
		if(!isset($year1))
		{
			$year=date('Y');
			$month=date('m');
			$day=date('d');
		}
		echo " <select name='year1'>";
		for($f=2004;$f<2051;$f++)
		{
			if($f == $year1)
				echo "<option selected>".$f."</option>";
			else
				echo "<option>".$f."</option>";
		}
		echo "</select> <select name='month1'>";
		for($f=1;$f<13;$f++)
		{
			if($f == $month1)
				if($f < 10)
					echo "<option selected>0".$f."</option>";
				else
					echo "<option selected>".$f."</option>";
			else
				if($f < 10)
					echo "<option>0".$f."</option>";
				else
					echo "<option>".$f."</option>";
		}
		echo "</select> <select name='day1'>";
		for($f=1;$f<32;$f++)
		{
		if($f == $day1)
			if($f < 10)
				echo "<option selected>0".$f."</option>";
			else
				echo "<option selected>".$f."</option>";
		else
			if($f < 10)
				echo "<option>0".$f."</option>";
			else
					echo "<option>".$f."</option>";
		}
		echo "</select></td></tr>";

		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>HASTA: </font></td>";	
		echo "<td bgcolor=#cccccc >";
		if(!isset($year2))
		{
			$year=date('Y');
			$month=date('m');
			$day=date('d');
		}
		echo "<select name='year2'>";
		for($f=2004;$f<2051;$f++)
		{
			if($f == $year2)
				echo "<option selected>".$f."</option>";
			else
				echo "<option>".$f."</option>";
		}
		echo "</select> <select name='month2'>";
		for($f=1;$f<13;$f++)
		{
			if($f == $month2)
				if($f < 10)
					echo "<option selected>0".$f."</option>";
				else
					echo "<option selected>".$f."</option>";
			else
				if($f < 10)
					echo "<option>0".$f."</option>";
				else
					echo "<option>".$f."</option>";
		}
		echo "</select> <select name='day2'>";
		for($f=1;$f<32;$f++)
		{
		if($f == $day2)
			if($f < 10)
				echo "<option selected>0".$f."</option>";
			else
				echo "<option selected>".$f."</option>";
		else
			if($f < 10)
				echo "<option>0".$f."</option>";
			else
					echo "<option>".$f."</option>";
		}
		echo "</select></td></tr>";
		
		//echo "<tr><td bgcolor=#cccccc colspan=1 rowspan='3'><font color=#000066>CIRCUNSTANCIA: </font></td>";
		//echo "<td bgcolor=#cccccc colspan=1><input type='radio' name='circ' value='ini'>Ingreso<br>";
		//echo "<tr><td bgcolor=#cccccc colspan=1><input type='radio' name='circ' value='desp'>24 Horas<br>";
		//echo "<tr><td bgcolor=#cccccc colspan=1><input type='radio' name='circ' value='ambos'>Ambos";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
		
		
	}
	else
	{
		$fecha2=$year2."-".$month2."-".$day2;
		$fecha1=$year1."-".$month1."-".$day1;
		/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table border=1 align='center' width=450><tr><td rowspan=3 align='center' colspan='0'>";
		echo "<img SRC='/MATRIX/images/medical/root/americas10.jpg'  size=150 width=128></td>";
		echo "<td align=center colspan='4' ><font size=3 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>MEDICIÓN DEL APACHE II</b></font>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>DESDE ".$fecha1." HASTA ".$fecha2;//".substr($medico,$ini1+1,strlen($medico))."";
		echo "</tr></table>";
		//ECHO $circ;
		echo "<table border=1 width=450 align='center'>";
		/*if($circ=="ini" or $circ=="ambos")
		{
			//$query = "select Count(Glasgow),AVG(Glasgow),AVG(Apache),AVG(Rata_muerte),AVG(Rata_muerte_ajustada) from cominf_000006 ";//where ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."' or Fecha = '".$fecha1."') and Circunstancia_apache='01-INGRESO'";
			$query = "select Count(*),AVG(Glasgow),AVG(Apache),AVG(Rata_muerte),AVG(Rata_muerte_ajustada) from cominf_000006 where ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."' or Fecha = '".$fecha1."') and Circunstancia_apache='01-INGRESO'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				$row = mysql_fetch_row($err);
				echo "<td colspan=2><font size=2 face='arial'><b>PROMEDIO DE INFORMACION AL INGRESO</td>"; 
				echo "<tr><td><font size=2  face='arial' ><b>	Glasgow:</b></td><td align='center'> ".number_format($row[1],2,'.',',')."</td></tr>";
				echo "<tr><td ><font size=2 face='arial'><b>	Apache II: </b></td><td align='center'>".number_format($row[2],2,'.',',')."</td></tr>";
				echo "<tr><td ><font size=2  face='arial'><b>	Rata de muerte:</td><td align='center'></b> ".number_format($row[3],2,'.',',')." %</td></tr>";
				echo "<tr><td ><font size=2 face='arial'><b>	Rata de muerte ajustada:</b></td><td align='center'> ".number_format($row[4],2,'.',',')." %</td></tr>"; 
				echo "<tr><td ><font size=2  face='arial'><b>	Numero de Registros:</b></td><td align='center'> ".$row[0]."</td></tr>";
			}
		}
		if($circ=="desp" or $circ=="ambos")
		{
			//$query = "select Count(Glasgow),AVG(Glasgow),AVG(Apache),AVG(Rata_muerte),AVG(Rata_muerte_ajustada) from cominf_000006 ";//where ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."' or Fecha = '".$fecha1."') and Circunstancia_apache='01-INGRESO'";
			$query = "select Count(*),AVG(Glasgow),AVG(Apache),AVG(Rata_muerte),AVG(Rata_muerte_ajustada) from cominf_000006 where ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."' or Fecha = '".$fecha1."') and Circunstancia_apache='02-24 HORAS'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				$row = mysql_fetch_row($err);
				echo "<td colspan=2><font size=2 face='arial'><b>PROMEDIO DE INFORMACION A LAS 24 HORAS</td>"; 
				echo "<tr><td ><font size=2  face='arial'><b>	Glasgow:</b></td><td align='center'> ".number_format($row[1],2,'.',',')."</td></tr>";
				echo "<tr><td ><font size=2 face='arial'><b>	Apache II: </b></td><td align='center'>".number_format($row[2],2,'.',',')."</td></tr>";
				echo "<tr><td ><font size=2  face='arial'><b>	Rata de muerte:</td><td align='center'></b> ".number_format($row[3],2,'.',',')." %</td></tr>";
				echo "<tr><td ><font size=2 face='arial'><b>	Rata de muerte ajustada:</b></td><td align='center'> ".number_format($row[4],2,'.',',')." %</td></tr>"; 
				echo "<tr><td ><font size=2  face='arial'><b>	Numero de Registros:</b></td><td align='center'> ".$row[0]."</td></tr>";
			}
		}
		if($circ=="ambos")
		{*/
			//$query = "select Count(Glasgow),AVG(Glasgow),AVG(Apache),AVG(Rata_muerte),AVG(Rata_muerte_ajustada) from cominf_000006 ";//where ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."' or Fecha = '".$fecha1."') and Circunstancia_apache='01-INGRESO'";
			$query = "select Count(*),AVG(Glasgow),AVG(Apache),AVG(Rata_muerte),AVG(Rata_muerte_ajustada) from cominf_000006 where ((Fecha > '".$fecha1."' and Fecha < '".$fecha2."') or Fecha = '".$fecha2."' or Fecha = '".$fecha1."')";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				$row = mysql_fetch_row($err);
				echo "<td colspan=2><font size=2 face='arial'><b>PROMEDIO DE INFORMACION TOTAL</td>"; 
				echo "<tr><td ><font size=2  face='arial'><b>	Glasgow:</b></td><td align='center'> ".number_format($row[1],2,'.',',')."</td></tr>";
				echo "<tr><td ><font size=2 face='arial'><b>	Apache II: </b></td><td align='center'>".number_format($row[2],2,'.',',')."</td></tr>";
				echo "<tr><td ><font size=2  face='arial'><b>	Rata de muerte:</td><td align='center'></b> ".number_format($row[3],2,'.',',')." %</td></tr>";
				echo "<tr><td ><font size=2 face='arial'><b>	Rata de muerte ajustada:</b></td><td align='center'> ".number_format($row[4],2,'.',',')." %</td></tr>"; 
				echo "<tr><td ><font size=2  face='arial'><b>	Numero de Registros:</b></td><td align='center'> ".$row[0]."</td></tr>";
			}
		//}
		
	}
	include_once("free.php");
}
?>

