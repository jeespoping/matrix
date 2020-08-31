<title>MEDICAMENTOS ESPECIALES V2.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

  /**************************************************************************
	*	REPORTE DE medicamentos y material grabado O DEVUELTO por la pda	* 
	*	APARECEN LOS MEDICAMENTOS QUE SE ENCUENTRAN	EN LA TABLA DE CODIGOS	*
	*	 FARMPDA_000002 Y QUE EN ESTA TABLA DICE QUE NECESITAN FORMULA, 	*
	*	JUSTIFICACION O AMBAS. ACERCA DE LAS DEVOLUCIONES LAS TRAE SEGUN LA	*
	*	FECHA DEL TURNO Y EL TURNO 											*
	*************************************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	$conex_o = odbc_connect('inventarios','','');
	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($year1)  or !isset($year2) )
	{
		echo "<form action='reporte_med_farm.php' method=post>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>MEDICAMENTOS Y MATERIAL</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CC: </font></td>";	
		echo "<td bgcolor=#cccccc><select name='cc'>";
		$query = "SELECT Ccocod FROM costosyp_000005 where Ccoclas='PR' order by Ccocod ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				if (($row[0]) == $cc)
					echo "<option selected>".$row[0]."</option>";
				else
					echo "<option>".$row[0]."</option>";
			}
			echo "<option>Todos</option>";
		}	// fin del if $num>0
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESDE: </font></td>";	
		echo "<td bgcolor=#cccccc  >";
		if(!isset($year1))
		{
			$year1=date('Y');
			$month1=date('m');
			$day1=date('d');
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
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>HORA: </font></td>";	
		echo "<td bgcolor=#cccccc><select name='hora1'>";
			echo "<option>07:00:00</option>";
			echo "<option>19:00:00</option>";
		echo "</select></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>HASTA: </font></td>";	
		echo "<td bgcolor=#cccccc >";
		if(!isset($year2))
		{
			$year2=date('Y');
			$month2=date('m');
			$day2=date('d');
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
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>HORA: </font></td>";	
		echo "<td bgcolor=#cccccc><select name='hora2'>";
			echo "<option>06:59:59</option>";
			echo "<option>18:59:59</option>";
		echo "</select></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else
	{
		$fecha2=$year2."-".$month2."-".$day2;
		$fecha1=$year1."-".$month1."-".$day1;
		$fechas="";
		If($fecha2 != $fecha1)
		{
			/* LLegar de una fecha a la otra avanzando dia a dia*/
			$day3=$day1;
			$month3=$month1;
			$year3=$year1;
			$day3++;
				//echo "checkdate=".!checkdate($month3,$day3,$year3)." day3=".$day3;
			if ($day3 =='29' and $month3 =='2')
				{
					if(!checkdate($month3,$day3,$year3))
					{
						$day3='01';
						$month3='03';
					}
					
				}
				//ver si el años es bisiesto
				
				if ($day3 == '31' and ($month3=='11' or $month3=='04' or $month3=='06' or $month3=='10'))
				{
					//echo "entro";
					$day3='01';
					$month3=(integer)$month3 +1;
					if($month3 < 10)
						$month3="0".$month3;
				}
				if ((integer)($day3) =='32')
				{
					$day3='01';
					$month3=(integer)$month3 +1;
					if($month3 < 10)
						$month3="0".$month3;
					else if ($month3 == '13')
					{
						$month3='01';
						$year3++;
					}
				}
				$fecha3=$year3."-".$month3."-".$day3;
				$i=0;
			while($fecha3 < $fecha2 )
			{
				//echo "fecha3=".$fecha3."<br>";
				$i++;
				$fecha4=$fecha3;
				$fechas=$fechas." or Fecha_data='".$fecha3."'";
				$day3++;
				//echo "checkdate=".!checkdate($month3,$day3,$year3)." day3=".$day3;
				if ($day3 =='29' and $month3 =='2')
				{
					if(!checkdate($month3,$day3,$year3))
					{
						$day3='01';
						$month3='03';
					}
					
				}
				//ver si el años es bisiesto
				
				if ($day3 == '31' and ($month3=='11' or $month3=='04' or $month3=='06' or $month3=='10'))
				{
					//echo "entro";
					$day3='01';
					$month3=(integer)$month3 +1;
					if($month3 < 10)
						$month3="0".$month3;
				}
				if ((integer)($day3) =='32')
				{
					$day3='01';
					$month3=(integer)$month3 +1;
					if($month3 < 10)
						$month3="0".$month3;
					else if ($month3 == '13')
					{
						$month3='01';
						$year3++;
					}
				}
				$fecha3=$year3."-".$month3."-".$day3;
				
			}
			$hora="((Fecha_data='".$fecha1."'  and Hora_data between '".$hora1."' and '24:00:00') or (Fecha_data='".$fecha2."'  and Hora_data between '00:00:00' and '".$hora2."') ".$fechas.") ";
			if($hora1 == "07:00:00")
				$horadev="((Fecha_turno='".$fecha1."'  and Turno ='01-DIA') ";
			ELSE
				$horadev="((Fecha_turno='".$fecha1."'  and Turno ='01-NOCHE') ";

			if($hora2 == "06:59:59" AND $i != 0) //coge el turno de la noche anterior a la fecha final
				$horadev=$horadev."or (Fecha_turno='".$fecha4."'  and Turno='01-NOCHE') ".$fechas.") ";
				
			else if( $hora2 == "18:59:59" and $i !=0) //coge el turno de dia de la fecha final
				$horadev=$horadev."or (Fecha_turno='".$fecha2."'  and Turno='01-DIA') ".$fechas.") ";
				
			else If($i==0)	// son dos dias consecutivos por lo que coge el turno dia y noche de la fecha inicial
				$horadev=$horadev."or (Fecha_turno='".$fecha1."'  and Turno ='01-NOCHE') ) ";
				
			$desde="DESDE ".$hora1." DE ".$fecha1." HASTA LAS ".$hora2." DE ".$fecha2;
		}
		else // fecha1 == Fecha2
		{
			$hora="Fecha_data='".$fecha1."' and Hora_data between '".$hora1."' and '".$hora2."' ";
			if($hora2 != "06:59:59" and $hora1 == "07:00:00")
			{
				$horadev="Fecha_turno=".$fecha1." and Turno='01-DIA'";
				$desde="DESDE ".$hora1." HASTA LAS ".$hora2." DE ".$fecha2;
			}
			ELSE
			{
				
				$horadev="Fecha_turno=".$fecha1." and Turno='01-DIA'";
				$hora="Fecha_data='".$fecha1."' and Hora_data between '07:00:00' and '18:59:59' ";
				$desde="HA ESCOGIDO MAL LAS FECHAS Y HORAS ASI QUE FUERON CAMBIADAS PARA:<BR>DESDE ".$hora1." HASTA LAS ".$hora2." DE ".$fecha2;
			}
		}
		
		if($cc=="Todos")
		{
			$cc="";
			$span=3;
			$name="";
		}
		else
		{
			$name="</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>CENTRO DE COSTOS: ".$cc."</b></font>";
			$cc="Cc='".$cc."' and ";
			$span=4;
		}
		/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table border=1 align='center' width=700><tr><td rowspan=4 align='center' colspan='0'>";
		echo "<img SRC='/MATRIX/images/medical/root/americas10.jpg'  size=150 width=128></td>";
		echo "<td align=center colspan='4' ><font size=3 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>MATERIAL Y MEDICAMENTOS INGRESADOS</b></font>";
		echo $name."</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$desde;//".substr($medico,$ini1+1,strlen($medico))."";
		echo "</tr></table>";

		if($cc != "")
		{
			$artcod="";
			echo "<table border=1 align='center' width=700>";
			echo "<TR><td width=75><font size=2 face='arial'><b>FECHA</td>";
			echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
			echo "<td ><font size=2 face='arial'><b>REG.</td>";
			echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
			echo "<td ><font size=2 face='arial'><b>PACIENTE</td>";
			echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
			echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
			echo "<td ><font size=2 face='arial'><b>CANT.</td>";
			/*Buscar en la tabla de ingreso de medicamentos*/
			$query = "select fecha_data,hora_data,reg_num,historia,paciente,cod_articulo,descripcion_art,cantidad,fuente,justificacion from farmpda_000001 where ".$cc."  ".$hora."  order by Cod_articulo,Fecha_data, historia";
			$err = mysql_query($query,$conex);
			//echo $query;
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_row($err);
					$doc="";$l=0;
					if ($row[5] != $artcod)
					{
						if($row[5] != "A10A10" and $row[5] != "A10AA9" and $row[5] != "E1AB11" and $row[5] != "E1AB07" and $row[5] != "E1AB05" and 
						   $row[5] != "E1AB03" and $row[5] != "E1AB02" and $row[5] != "E11105" and $row[5] != "E1AD02" and $row[5] != "E1AD03" )
						{
							$query1="select congrufac,artuni from ivart,ivcongru where artcod='".$row[5]."' and congrugru=artgru and   congrufac is not null";
							$err_o = odbc_do($conex_o,$query1);
							if (odbc_fetch_row($err_o))
							{
								$grupo=odbc_result($err_o,1);
								$artcod=$row[5];
								$artcod1=$artcod;
								$uni=odbc_result($err_o,2);
							}
							else
							{
								$grupo="0616";
								$artcod1=$row[5]."<br><b>ESTE ARTICULO NO TIENE GRUPO EN EL SISTEMA</b>";
								$uni="unidades no encontradas en el sistema";
							}
						}
						ELSE
							$grupo="salino, agua destilada, hartman o Humulin";
					}
					if ($grupo == "0616")
					{
						switch ($row[9])
						{
							case "00-NO NECESITA": $color="#ffffff";
							break;
							case "01-JUSTIFICACION": $color="#FED801";
							break;
							case "02-FORMULA": $color="#03C4FC";
							break;
							case "00-JUSTIFICACION Y FORMULA": $color="#45FF3C";
							break;
						}
						/*Buscamos en el Unix las unidades
						$query_o="select artuni from ivart where artcod='".$row[5]."'";
						$err_o= odbc_do($conex_o,$query_o);
						if (odbc_fetch_row($err_o))
							$uni=odbc_result($err_o,1);
						else
						 	$uni="unidades no encontradas en el sistema";
						/*Terminamos de buscar en el Unix las unidades*/
						echo "<TR><td bgcolor='".$color."'><font size=2 face='arial'>".$row[0]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".substr($row[1],0,5)."</td>"; 
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[2]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[3]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial'>".$row[4]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[5]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial'>".$row[6]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[7].$uni."</td>";
					}
				}
			}
			/*Buscar en la tabla de devolucion de medicamentos para los medicamentos devueltos para esas fechas y horas*/
			$artcod="";
			$query = "select fecha_data,hora_data,reg_num,historia,paciente,cod_articulo,descripcion_art,cantidad,turno,fecha_turno from farmpda_000003 where ".$cc."  ".$hora." order by Cod_articulo,historia";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				echo "<table border=1 align='center' width=700>";
				echo "<TR><td COLSPAN='9' align='center'><font size=3 face='arial'><center><b>DEVOLUCIONES REALIZADAS ENTRE LAS FECHAS Y LAS HORAS</td></tr>";
				echo "<TR><td width=75><font size=2 face='arial'><b>FECHA</td>";
				echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
				echo "<td ><font size=2 face='arial'><b>REG.</td>";
				echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
				echo "<td ><font size=2 face='arial'><b>PACIENTE</td>";
				echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
				echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
				echo "<td ><font size=2 face='arial'><b>CANT.</td>";
				echo "<td ><font size=2 face='arial'><b>TURNO</td>";
				
				for ($h=0;$h<$num;$h++)
				{
					$row = mysql_fetch_row($err);
					if ($row[5] != $artcod)
					{
						if($row[5] != "A10A10" and $row[5] != "A10AA9" and $row[5] != "E1AB11" and $row[5] != "E1AB07" and $row[5] != "E1AB05" and 
						   $row[5] != "E1AB03" and $row[5] != "E1AB02" and $row[5] != "E11105" and $row[5] != "E1AD02" and $row[5] != "E1AD03" )
						{
							/*Busco el grupo al que pertenece en el Unix*/
							$query1="select congrufac,artuni from ivart,ivcongru where artcod='".$row[5]."' and congrugru=artgru and   congrufac is not null";
							$err_o = odbc_do($conex_o,$query1);
							if (odbc_fetch_row($err_o))
							{
								$grupo=odbc_result($err_o,1);
								$artcod=$row[5];
								$artcod1=$artcod;
								$uni=odbc_result($err_o,2);
							}
							else
							{
								$grupo="0616";
								$artcod1=$row[5]."<br><b>ESTE ARTICULO NO TIENE GRUPO EN EL SISTEMA</b>";
								$uni="unidades no encontradas en el sistema";
							}
							/*Budsco si tiene alguna condicion especial el medicamento*/
							$query2="select Condiciones from farmpda_000002 where condiciones <> '00-NINGUNA' and (codigo_esp='".$row[5]."' or codigo='".$row[5]."')";
							$err2 = mysql_query($query2,$conex);
							//echo mysql_errno()."=".mysql_error();
							$num2 = mysql_num_rows($err2);
							if($num2>0)
							{
								$row2 = mysql_fetch_row($err2);
								switch ($row2[0])
								{
									case "01-JUSTIFICACION": $color="#FED801";
									break;
									case "02-FORMULA": $color="#03C4FC";
									break;
									case "00-JUSTIFICACION Y FORMULA": $color="#45FF3C";
									break;
								}
							}
							else
								$color="#ffffff";
						}
						ELSE
							$grupo="salino, agua destilada, hartman o Humulin";
							
						
					}
					if ($grupo == "0616")
					{
						/*Buscamos en el Unix las unidades
						$query_o="select artuni from ivart where artcod='".$row[5]."'";
						$err_o= odbc_do($conex_o,$query_o);
						if (odbc_fetch_row($err_o))
							$uni=odbc_result($err_o,1);
						else
						 	$uni="unidades no encontradas en el sistema";
						/*Terminamos de buscar en el Unix las unidades*/
						
						
						echo "<TR><td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[0]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".substr($row[1],0,5)."</td>"; 
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[2]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[3]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[4]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[5]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[6]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[7].$uni."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[9]." ".substr($row[8],3)."</td>";
					}
					
				}
			}
			
			/*Buscar en la tabla de devolucion de medicamentos*/
			$query = "select fecha_data,hora_data,reg_num,historia,paciente,cod_articulo,descripcion_art,cantidad,turno,fecha_turno from farmpda_000003 where ".$cc."  ".$horadev." order by Cod_articulo,historia";
			//echo "<br>".$query;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				echo "<table border=1 align='center' width=700>";
				echo "<TR><td COLSPAN='9' align='center'><font size=3 face='arial'><center><b>DEVOLUCIONES REALIZADAS PARA LOS TURNOS A LOS QUE LAS FECHAS Y LAS HORAS</td></tr>";
				echo "<TR><td width=75><font size=2 face='arial'><b>FECHA</td>";
				echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
				echo "<td ><font size=2 face='arial'><b>REG.</td>";
				echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
				echo "<td ><font size=2 face='arial'><b>PACIENTE</td>";
				echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
				echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
				echo "<td ><font size=2 face='arial'><b>CANT.</td>";
				echo "<td ><font size=2 face='arial'><b>TURNO</td>";
				/*Buscarlos en la tabla de codigos de procedimientos
				para ver si necesitan formula o justificacion*/
				
				for ($h=0;$h<$num;$h++)
				{
					$row = mysql_fetch_row($err);
					if ($row[5] != $artcod)
					{
						if($row[5] != "A10A10" and $row[5] != "A10AA9" and $row[5] != "E1AB11" and $row[5] != "E1AB07" and $row[5] != "E1AB05" and 
						   $row[5] != "E1AB03" and $row[5] != "E1AB02" and $row[5] != "E11105" and $row[5] != "E1AD02" and $row[5] != "E1AD03" )
						{
							/*Busco el grupo al que pertenece en el Unix*/
							$query1="select congrufac,artuni from ivart,ivcongru where artcod='".$row[5]."' and congrugru=artgru and   congrufac is not null";
							$err_o = odbc_do($conex_o,$query1);
							if (odbc_fetch_row($err_o))
							{
								$grupo=odbc_result($err_o,1);
								$artcod=$row[5];
								$artcod1=$artcod;
								$uni=odbc_result($err_o,2);
							}
							else
							{
								$grupo="0616";
								$artcod1=$row[5]."<br><b>ESTE ARTICULO NO TIENE GRUPO EN EL SISTEMA</b>";
								$uni="unidades no encontradas en el sistema";
							}
							/*Budsco si tiene alguna condicion especial el medicamento*/
							$query2="select Condiciones from farmpda_000002 where condiciones <> '00-NINGUNA' and (codigo_esp='".$row[5]."' or codigo='".$row[5]."')";
							$err2 = mysql_query($query2,$conex);
							$num2 = mysql_num_rows($err2);
							if($num2>0)
							{
								$row2 = mysql_fetch_row($err2);
								switch ($row2[0])
								{
									case "01-JUSTIFICACION": $color="#FED801";
									break;
									case "02-FORMULA": $color="#03C4FC";
									break;
									case "00-JUSTIFICACION Y FORMULA": $color="#45FF3C";
									break;
								}
							}
							else
								$color="#ffffff";
						}
						ELSE
							$grupo="salino, agua destilada, hartman o Humulin";
							
						
					}
					if ($grupo == "0616")
					{
						/*Buscamos en el Unix las unidades
						$query_o="select artuni from ivart where artcod='".$row[5]."'";
						$err_o= odbc_do($conex_o,$query_o);
						if (odbc_fetch_row($err_o))
							$uni=odbc_result($err_o,1);
						else
						 	$uni="unidades no encontradas en el sistema";
						/*Terminamos de buscar en el Unix las unidades*/
						
						
						echo "<TR><td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[0]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".substr($row[1],0,5)."</td>"; 
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[2]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[3]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[4]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[5]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[6]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[7].$uni."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[9]." ".substr($row[8],3)."</td>";
					}
					
				}
			}
				
		}
		else
		{
			echo "<table border=1 align='center' width=700>";
			echo "<TR><td ><font size=2 face='arial'><b>CC</td>";
			echo "<td width=75><font size=2 face='arial'><b>FECHA</td>";
			echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
			echo "<td ><font size=2 face='arial'><b>REG.</td>";
			echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
			echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
			echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
			echo "<td ><font size=2 face='arial'><b>CANT.</td>";
			$artcod="";
			$query = "select Cc,fecha_data,hora_data,reg_num,historia,cod_articulo,descripcion_art,cantidad,justificacion from farmpda_000001 where   ".$hora." order by cod_articulo,historia";
			//echo $query;
			$err = mysql_query($query,$conex);
			$doc="";$l=0;$est="";$nums="";
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_row($err);
					if ($row[5] != $artcod)
					{
						if($row[5] != "A10A10" and $row[5] != "A10AA9" and $row[5] != "E1AB11" and $row[5] != "E1AB07" and $row[5] != "E1AB05" and 
						   $row[5] != "E1AB03" and $row[5] != "E1AB02" and $row[5] != "E11105" and $row[5] != "E1AD02" and $row[5] != "E1AD03" )
						{
							$query1="select congrufac,artuni from ivart,ivcongru where artcod='".$row[5]."' and congrugru=artgru and   congrufac is not null";
							$err_o = odbc_do($conex_o,$query1);
							if (odbc_fetch_row($err_o))
							{
								$grupo=odbc_result($err_o,1);
								$artcod=$row[5];
								$artcod1=$artcod;
								$uni=odbc_result($err_o,2);
							}
							else
							{
								$grupo="0616";
								$artcod1=$row[5]."<br><b>ESTE ARTICULO NO TIENE GRUPO EN EL SISTEMA</b>";
								$uni="unidades no encontradas en el sistema";
							}
						}
						ELSE
							$grupo="salino, agua destilada, hartman o Humulin";
					}
					if ($grupo == "0616")
					{
						switch ($row[8])
						{
							case "00-NO NECESITA": $color="#ffffff";
							break;
							case "01-JUSTIFICACION": $color="#FED801";
							break;
							case "02-FORMULA": $color="#03C4FC";
							break;
							case "00-JUSTIFICACION Y FORMULA": $color="#45FF3C";
							break;
						}
						/*Buscamos en el Unix las unidades
						$query_o="select artuni from ivart where artcod='".$row[5]."'";
						$err_o= odbc_do($conex_o,$query_o);
						if (odbc_fetch_row($err_o))
							$uni=odbc_result($err_o,1);
						else
						 	$uni="unidades no encontradas en el sistema";
						/*Terminamos de buscar en el Unix las unidades*/
						echo "<TR><td width=75 bgcolor='".$color."'><font size=2 face='arial'><b>".$row[0]."</b></td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[1]."</td>"; 
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".substr($row[2],0,5)."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[3]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[4]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[5]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[6]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[7].$uni."</td>";
					}
				}
			}
			$query = "select Cc,fecha_data,hora_data,reg_num,historia,cod_articulo,descripcion_art,cantidad,turno,fecha_turno from farmpda_000003 where   ".$hora."  order by Cod_articulo, historia";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				echo "<table border=1 align='center' width=700>";
				echo "<TR><td  COLSPAN='9' align='center'><font size=3 face='arial'><center><b>DEVOLUCIONES REALIZADAS ENTRE LAS FECHAS Y LAS HORAS</td></tr>";
				echo "<TR><td ><font size=2 face='arial'><b>CC</td>";
				echo "<td width=75><font size=2 face='arial'><b>FECHA</td>";
				echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
				echo "<td ><font size=2 face='arial'><b>REG.</td>";
				echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
				echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
				echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
				echo "<td ><font size=2 face='arial'><b>CANT.</td>";
				echo "<td ><font size=2 face='arial'><b>TURNO</td>";
				for ($h=0;$h<$num;$h++)
				{
					$row = mysql_fetch_row($err);
					if ($row[5] != $artcod)
					{
						if($row[5] != "A10A10" and $row[5] != "A10AA9" and $row[5] != "E1AB11" and $row[5] != "E1AB07" and $row[5] != "E1AB05" and 
						   $row[5] != "E1AB03" and $row[5] != "E1AB02" and $row[5] != "E11105" and $row[5] != "E1AD02" and $row[5] != "E1AD03" )
						{
							/*Busco el grupo al que pertenece en el Unix*/
							$query1="select congrufac,artuni from ivart,ivcongru where artcod='".$row[5]."' and congrugru=artgru and   congrufac is not null";
							$err_o = odbc_do($conex_o,$query1);
							if (odbc_fetch_row($err_o))
							{
								$grupo=odbc_result($err_o,1);
								$artcod=$row[5];
								$artcod1=$artcod;
								$uni=odbc_result($err_o,2);
							}
							else
							{
								$grupo="0616";
								$artcod1=$row[5]."<br><b>ESTE ARTICULO NO TIENE GRUPO EN EL SISTEMA</b>";
								$uni="unidades no encontradas en el sistema";
							}
							/*Busco si tiene alguna condicion especial el medicamento*/
							$query2="select Condiciones from farmpda_000002 where condiciones <> '00-NINGUNA' and (codigo_uci='".$row[5]."' or codigo='".$row[5]."')";
							$err2 = mysql_query($query2,$conex);
							$num2 = mysql_num_rows($err2);
							if($num2>0)
							{
								$row2 = mysql_fetch_row($err2);
								switch ($row2[0])
								{
									case "01-JUSTIFICACION": $color="#FED801";
									break;
									case "02-FORMULA": $color="#03C4FC";
									break;
									case "00-JUSTIFICACION Y FORMULA": $color="#45FF3C";
									break;
								}
							}
							else
								$color="#ffffff";
						}
						ELSE
							$grupo="salino, agua destilada, hartman o Humulin";
					}
					if ($grupo == "0616")
					{
						echo "<TR><td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[0]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[1]."</td>"; 
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".substr($row[2],0,5)."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[3]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[4]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[5]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[6]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[7].$uni."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[9]." ".substr($row[8],3)."</td>";
					}
				}
			}
			$query = "select Cc,fecha_data,hora_data,reg_num,historia,cod_articulo,descripcion_art,cantidad,turno,fecha_turno from farmpda_000003 where   ".$horadev."  order by Cod_articulo, historia";
			//echo "<br>".$query;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				echo "<table border=1 align='center' width=700>";
				echo "<TR><td COLSPAN='9' align='center'><font size=3 face='arial'><center><b>DEVOLUCIONES REALIZADAS PARA LOS TURNOS A LOS QUE LAS FECHAS Y LAS HORAS</td></tr>";
				echo "<TR><td ><font size=2 face='arial'><b>CC</td>";
				echo "<td width=75><font size=2 face='arial'><b>FECHA</td>";
				echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
				echo "<td ><font size=2 face='arial'><b>REG.</td>";
				echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
				echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
				echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
				echo "<td ><font size=2 face='arial'><b>CANT.</td>";
				echo "<td ><font size=2 face='arial'><b>TURNO</td>";
				for ($h=0;$h<$num;$h++)
				{
					$row = mysql_fetch_row($err);
					if ($row[5] != $artcod)
					{
						if($row[5] != "A10A10" and $row[5] != "A10AA9" and $row[5] != "E1AB11" and $row[5] != "E1AB07" and $row[5] != "E1AB05" and 
						   $row[5] != "E1AB03" and $row[5] != "E1AB02" and $row[5] != "E11105" and $row[5] != "E1AD02" and $row[5] != "E1AD03" )
						{
							/*Busco el grupo al que pertenece en el Unix*/
							$query1="select congrufac,artuni from ivart,ivcongru where artcod='".$row[5]."' and congrugru=artgru and   congrufac is not null";
							$err_o = odbc_do($conex_o,$query1);
							if (odbc_fetch_row($err_o))
							{
								$grupo=odbc_result($err_o,1);
								$artcod=$row[5];
								$artcod1=$artcod;
								$uni=odbc_result($err_o,2);
							}
							else
							{
								$grupo="0616";
								$artcod1=$row[5]."<br><b>ESTE ARTICULO NO TIENE GRUPO EN EL SISTEMA</b>";
								$uni="unidades no encontradas en el sistema";
							}
							/*Busco si tiene alguna condicion especial el medicamento*/
							$query2="select Condiciones from farmpda_000002 where condiciones <> '00-NINGUNA' and (codigo_uci='".$row[5]."' or codigo='".$row[5]."')";
							$err2 = mysql_query($query2,$conex);
							$num2 = mysql_num_rows($err2);
							if($num2>0)
							{
								$row2 = mysql_fetch_row($err2);
								switch ($row2[0])
								{
									case "01-JUSTIFICACION": $color="#FED801";
									break;
									case "02-FORMULA": $color="#03C4FC";
									break;
									case "00-JUSTIFICACION Y FORMULA": $color="#45FF3C";
									break;
								}
							}
							else
								$color="#ffffff";
						}
						ELSE
							$grupo="salino, agua destilada, hartman o Humulin";
					}
					if ($grupo == "0616")
					{
						echo "<TR><td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[0]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[1]."</td>"; 
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".substr($row[2],0,5)."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[3]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[4]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[5]."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[6]."</td>";
						echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[7].$uni."</td>";
						echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[9]." ".substr($row[8],3)."</td>";
					}
				}
			}
		}
	}
}
?>
</body>
</html>