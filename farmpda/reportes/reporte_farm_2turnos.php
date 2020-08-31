 <title>REPORTE REPOSICION V2.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000" BGCOLOR="#FFFFFF">
<?php
include_once("conex.php");

  /***************************************************
	Hace un reporte entre dos fechas  entre horarios especiales de turnos
	de enfermeria de 7 am a 7 pm y de 7 pm a 7 am.
	Se puede mirar un mismo turno.
	El material encontrado lo resta con el material devuelto,
	corrobora con la tabla de inconsistencias , si esta en esta no 
	toma en cuenta el registro al pasar la info de itdro a la la facturacion 
	de la clinica, corrobora con la tabla que indica las formulas y justificaciones
	para que aparesca de un color diferente.
	Y busca las unidades en el UNIX.
	LE FALTA: Entregar el reporte por grupo de articulos. */
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	//$key = substr($user,2,strlen($user));
	

	

	$conex_o = odbc_connect('inventarios','','');
	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	if(!isset($year1)  or !isset($cc))
	{
		if(isset($cc))
		{
			$ini1=strpos($cc,"-");
			$cc=substr($cc,0,$ini1);
		}
		//echo $cc."<br>";
		//Echo $pac;
		echo "<form action='reporte_farm_2turnos.php' method=post>";
		//echo "<center><table border=1 width=200><tr><td>kndljd";
		//echo "</table>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>MEDICAMENTOS Y MATERIAL</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CC: </font></td>";	
		echo "<td bgcolor=#cccccc><select name='cc' >";
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
		}	// fin del if $num>0
		echo "</select></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>DESDE: </font></td>";	
		echo "<td bgcolor=#cccccc  >";
		if(!isset($year1))
		{
			$year1=date('Y');
			$month1=date('m');
			$day1=date('d');
		}
		echo " <select name='year1' >";
		for($f=2004;$f<2051;$f++)
		{
			if($f == $year1)
				echo "<option selected>".$f."</option>";
			else
				echo "<option>".$f."</option>";
		}
		echo "</select> <select name='month1' >";
		for($f=1;$f<13;$f++)
		{
			if( $f == $month1)
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
			echo "<option>00:00:00</option>";
		echo "</select></td></tr>";
		
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>HASTA: </font></td>";	
		echo "<td bgcolor=#cccccc  >";
		if(!isset($year2))
		{
			$year2=date('Y');
			$month2=date('m');
			$day2=date('d');
		}
		echo " <select name='year2' >";
		for($f=2004;$f<2051;$f++)
		{
			if($f == $year2)
				echo "<option selected>".$f."</option>";
			else
				echo "<option>".$f."</option>";
		}
		echo "</select> <select name='month2' >";
		for($f=1;$f<13;$f++)
		{
			if( $f == $month2)
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
		$fecha1=$year1."-".$month1."-".$day1;
		$fecha2=$year2."-".$month2."-".$day2;
		//echo "fecha2".$fecha2."<br>";
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
			while($fecha3 < $fecha2 )
			{
				//echo "fecha3=".$fecha3."<br>";
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
			$desde="DESDE ".$hora1." DE ".$fecha1." HASTA LAS ".$hora2." DE ".$fecha2;
		}
		else // fecha1 == Fecha2
		{
			$hora="Fecha_data='".$fecha1."' and Hora_data between '".$hora1."' and '".$hora2."' ";
			$desde="DESDE ".$hora1." HASTA LAS ".$hora2." DE ".$fecha2;
		}
					
		/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table  border='0' align='center' width=600>";
		echo "<tr><td  align=center colspan='3'><font size=2 face='arial'><B>MATERIAL Y MEDICAMENTOS INGRESADOS</b></font>";
		echo "</tr><tr><td  align=center colspan='3'><font size=2  face='arial'><B>CENTRO DE COSTOS: ".$cc."</b></font>";
		echo "</tr><tr><td  align=center colspan='3'><font size=2  face='arial'><b>".$desde."</b>";//".substr($medico,$ini1+1,strlen($medico))."";
		echo "</tr><tr><td  align=center bgcolor='#03C4FC'><font size=2  face='arial'><b> REQUIERE FORMULA</b>";
		echo "<td  align=center bgcolor='#FED801'><font size=2  face='arial'><b>REQUIERE JUSTIFICACION</b>";
		echo "<td  align=center bgcolor='#45FF3C'><font size=2 face='arial'><b>REQUIERE AMBAS</b>";
		
		$list="";//donde quedara la descripcion del articulo
		$cant=0;//donde quedara la cantidad del articulo
		$querdev=""; // donde quedara almacenado el query de la devolucion
		$color="#ffffff";// color base de las celdas
		echo "</tablE><table border='0'  align='center' width=600>";
		$query = "select descripcion_art,justificacion,cantidad,Cod_articulo,reg_num from farmpda_000001 where Cc='".$cc."' and  ".$hora." order by Descripcion_art";
	//	echo $query;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		//echo "<br>err=".$err."  num=".$num."<br><br><br>";
		$j=0;
		$cod="";
		if($num>0)
		{
			for ($p=0;$p<$num;$p++)
			{
				
				$row = mysql_fetch_row($err);
				/*Buscamos en el Unix que no halla presentado inconsistencia*/
				$ini1=strpos($row[4],"-");
				$lin=substr($row[4],$ini1+1);
				$nums=substr($row[4],0,$ini1);
				$query_o="select * from itdroinc where droincnum='".$nums."' and droinclin='".$lin."' ";
				$err_o= odbc_do($conex_o,$query_o);
				if (!odbc_fetch_row($err_o))
				{
					if($cod=="")
					{
						$list=$row[0];
						$cant=$row[2];
						$just=$row[1];
						$cod=$row[3];
	//				echo "list=$list<br>Cant=$cant<br>just=$just<br>cod=$cod<br>";
						/*Buscamos las condiciones en farmpda_000002 por si requiere formula, justificacion o ambos*/
						$query1="select condiciones from farmpda_000002 where codigo_esp='".$cod."' or Codigo='".$cod."'";
						$err1 = mysql_query($query1,$conex);
						$num1 = mysql_num_rows($err1);
						if($num>0)
						{
							$row1 = mysql_fetch_row($err1);
							if($row1[0] == "00-NINGUNA")
								$color="#ffffff";
							else if($row1[0] == "01-JUSTIFICACION")
								$color="#FED801";
							else if($row1[0] == "02-FORMULA")
								$color="#03C4FC";
							else if($row1[0] == "03-JUSTIFICACION Y FORMULA")
								$color="#45FF3C";
							
						}
						else
							$color="#ffffff";
						/*Terminamos de buscar las condiciones*/
					}
					else if ($cod != "" and $cod == $row[3])
					{
						$cant=$cant+$row[2];
	//				echo "cant=$cant<br>";
					}
					else if ($cod != "" and $cod != $row[3] )
					{
						//		echo "<TR><td ALIGN ='CENTER'><font size=2 face='arial'><b>SD ".$cant."</b></td><td><font size=2 face='arial'>".$list."</td></tr>";
						/*Buscamos si hay devoluciones para ese medicamento en esas fechas */
						$query = "select descripcion_art,cantidad,reg_num from farmpda_000003 where Cc='".$cc."' and ".$hora." and Cod_articulo='".$cod."' order by Descripcion_art";
						//echo $query."<br>";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1>0)
						{
							$querdev=$querdev." and Cod_articulo <> '".$cod."' ";
							for ($p=0;$p<$num1;$p++)
							{
								$row1 = mysql_fetch_array($err1);
						//	echo "row[2]=".$row1[2]."<br>";
								$ini2=strpos($row1[2],"-");
								$line=substr($row1[2],$ini2+1);
								$numse=substr($row1[2],0,$ini2);
								$query_o1="select * from itdroinc where droincnum='".$numse."' and droinclin='".$line."' ";
								//echo $query_o1."<br>";
								$err_o1= odbc_do($conex_o,$query_o1);
								if (!odbc_fetch_row($err_o1))
								{
									$cant=$cant-$row1[1];
	//								echo "cant=-$cant<br>";
								}
							}
						}
						/*terminamos de buscar devoluciones*/
						
						/*Buscamos en el Unix las unidades*/
						$query_o="select artuni,artgru from ivart where artcod='".$cod."'";
						$err_o1= odbc_do($conex_o,$query_o);
						if (odbc_fetch_row($err_o1))
						{
							$uni=odbc_result($err_o1,1);
							$artgru=odbc_result($err_o1,2);
						}
						else
						{
						 	$uni="unidades no encontradas en el sistema";
						 	$artgru="grupo no encontrado";
					 	}
						/*Terminamos de buscar en el Unix las unidades*/
						
						/*Buscamos las condiciones en farmpda_000002 por si requiere formula, justificacion o ambos*/
						$query1="select condiciones from farmpda_000002 where codigo_esp='".$cod."' or Codigo='".$cod."'";
						$err1 = mysql_query($query1,$conex);
						$num1 = mysql_num_rows($err1);
						if($num>0)
						{
							$row1 = mysql_fetch_row($err1);
							if($row1[0] == "00-NINGUNA")
								$color="#ffffff";
							else if($row1[0] == "01-JUSTIFICACION")
								$color="#FED801";
							else if($row1[0] == "02-FORMULA")
								$color="#03C4FC";
							else if($row1[0] == "03-JUSTIFICACION Y FORMULA")
								$color="#45FF3C";
							
						}
						else
							$color="#ffffff";
						/*Terminamos de buscar las condiciones*/
						$j++;
						$arr[$j]['cod']=$cod;
						$arr[$j]['list']=$list;
						$arr[$j]['uni']=$uni;
						$arr[$j]['cant']=$cant;
						$arr[$j]['cod']=$cod;
						$arr[$j]['artgru']=$artgru;
						$arr[$j]['color']=$color;
						$arr[$j]['ok']="no";
	//			echo "<br><br>list[$j]=$list<br>Cant=$cant<br>just=$just<br>cod=$cod<br>";		
						
						$cant=$row[2];
						$list=$row[0];
						$cod=$row[3];
						$color="#ffffff";
				//echo "<br><br>list[$j]=$list<br>Cant=$cant<br>just=$just<br>cod=$cod<br>";
						
					}
				}
				
			}
			$j++;
						$arr[$j]['cod']=$cod;
						$arr[$j]['list']=$list;
						$arr[$j]['uni']=$uni;
						$arr[$j]['cant']=$cant;
						$arr[$j]['cod']=$cod;
						$arr[$j]['artgru']=$artgru;
						$arr[$j]['color']=$color;
						$arr[$j]['ok']="no";
		}
		/*Impresión del reporte*/
		$a=1;
		$p=1;
	//	echo "j=$j<br>";
	//	$j++;
		while($j>=$a)
		{
			for($i=1;$i<=$j;$i++)
			{
				if(!isset($gru) or($gru== "" and $arr[$i]['ok'] == "no" and $arr[$i]['artgru'] != "RUS"  and $arr[$i]['artgru'] != "FRA"))
				{
					
	//				echo "list[$i]=".$arr[$i]['list']."<br>";
					$gru=$arr[$i]['artgru'];
					/*Buscamos en el Unix el nombre del grupo*/
					$query_o="select grunom from ivgru where  grucod='".$gru."'";
					$err_o1= odbc_do($conex_o,$query_o);
					if (odbc_fetch_row($err_o1))
					{
						echo "<tr></tr>";
						echo "<TR><td colspan=4 align=center bgcolor='#dddddd'><font size=2 face='arial'><b>$gru - ".odbc_result($err_o1,1)."</b></td></tr>";
						ECHO "<TR><td><font size=2 face='arial'><b>COD.</b></td>";
						echo "<td><font size=2 face='arial'><B>DESCRIPCION</B></td>";
						echo "<td><font size=2 face='arial'><b>UNID.</b></td>";
						echo "<td><font size=2 face='arial'><b>CANT.</b></td></TR>";
					}
					
				}
				if($arr[$i]['ok'] == "no" and $gru == $arr[$i]['artgru'])
				{
	//				echo "list[$i]=".$arr[$i]['list']."<br>";
					$arr[$i]['ok']="ok";
					echo "<TR><td bgcolor='".$arr[$i]['color']."'><font size=2 face='arial'><b>".$arr[$i]['cod']."</b></td>";
					echo "<td bgcolor='".$arr[$i]['color']."'><font size=2 face='arial'>".$arr[$i]['list']."</td>";
					echo "<td bgcolor='".$arr[$i]['color']."'><font size=2 face='arial'>".$arr[$i]['uni']."</td>";
					echo "<td bgcolor='".$arr[$i]['color']."'><font size=2 face='arial'><b>".$arr[$i]['cant']."</b></td></TR>";
					$p++;
				}
			}
	//		echo "a=$a<br><br>";
			$a++;
			$gru="";
			
		}
		/*final de impresión*/
		/*IMPRESION DE ARTICULOS REGISTRADOS PARA DEVOLUCIÓN (POR FUENTE 12 )*
		$list="";
		$cant=0;
		
		$query = "select Descripcion_art,Cantidad,cod_articulo,Reg_num from farmpda_000003 where Cc='".$cc."' and ".$hora." ".$querdev." order by Descripcion_art";
		//echo $query;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			//echo "</tablE><table border=1 align='center' width=300>";
			echo "<tr><td align='center'colspan='4'><font size=2 color=#000066 face='arial'><b>DEVOLUCIONES</font><br>";
			echo "<TR><td><font size=2 face='arial'><b>COD.</b></td>";
			echo "<td><font size=2 face='arial'><B>DESCRIPCION</B></td>";
			echo "<td><font size=2 face='arial'><b>UNID.</b></td>";
			echo "<td><font size=2 face='arial'><b>CANT.</b></td></TR>";
			for ($j=0;$j<$num;$j++)
			{
				$row = mysql_fetch_array($err);
				/*Buscamos en el Unix que no halla presentado inconsistencia*
				$ini1=strpos($row[3],"-");
				$lin=substr($row[3],$ini1+1);
				$nums=substr($row[3],0,$ini1);
				$query_o="select * from itdroinc where droincnum='".$nums."' and droinclin='".$lin."' ";
				$err_o= odbc_do($conex_o,$query_o);
				//lineas= odbc_num_ROWS($err_o1);
				if (!odbc_fetch_row($err_o))
				{
						/*Terminamos de buscar en el Unix las unidades*
					if($list=="")
					{
						$list=$row[0];
						$cant=$row[1];
						$cod=$row[2];
					}
					else if ($list != "" and $list == $row[0])
						$cant=$cant+$row[1];
					else if ($list != "" and $list != $row[0] )
					{
						/*Buscamos en el Unix las unidades*
						$query_o="select artuni from ivart where artcod='".$cod."'";
						$err_o= odbc_do($conex_o,$query_o);
						if (odbc_fetch_row($err_o))
							$uni=odbc_result($err_o,1);
						else
						 	$uni="unidades no encontradas en el sistema";
						/*Terminamos de buscar en el Unix las unidades*
						ECHO "<TR><td><font size=2 face='arial'><b>".$cod."</b></td>";
						echo "<td><font size=2 face='arial'>".$list."</td>";
						echo "<td><font size=2 face='arial'>".$uni."</td>";
						echo "<td><font size=2 face='arial'><b>".$cant."</b></td></TR>";
						$cant=$row[1];
						$list=$row[0];
						$cod=$row[2];
					}
					echo "<TR><td><font size=2 face='arial'><b>".$cod."</b></td>";
					echo "<td><font size=2 face='arial'>".$list."</td>";
					echo "<td><font size=2 face='arial'>".$uni."</td>";
					echo "<td><font size=2 face='arial'><b>".$cant."</b></td></TR>";
				}
			}
		}*/
	}
}
?>
</body>
</html>