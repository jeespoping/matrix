<title>INGRESO MATERIAL UNIX V2.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

   /***************************************************
	*	REPORTE DE MEDICAMENTOS Y MATERIAL GRABADO POR PDA
	*	BUSCA LOS MEDICAMENTOS ENTRE DOS FECHAS Y DOS HORAS
	*	POR CENTRO DE OCSTOS O PARA TODOS LOS CENTROS DE COSTOS
	*	EN LAS TABLAS FARMPDA_000001 (INGRESO) Y FARMPDA_000002
	*	(DEVOLUCIONES) Y BUSCA EL NUMERO DE DOCUMENTO 
	*	CORRESPONDIENTE A CADA REGISTRO ( EN LA TABLA ITDRODOC)
	*	ASI COMO SI HUBO ALGUNA INCONSISTENCIA AL INGRESARLO
	*	 A FACTURACION (TABLA ITDROINC)
	**************************************************/
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
		echo "<form action='reporte_con_todo.php' method=post>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>MEDICAMENTOS Y MATERIAL</td></tr>";
		echo "<tr></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>CC: </font></td>";	
		echo "<td bgcolor=#cccccc><select name='cc'>";
		$query ="SELECT Ccocod FROM costosyp_000005 where Ccoclas='PR' order by Ccocod ";
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
		/*APARTIR DE AQUI EMPIEZA LA CREACION DEL REPORTE*/
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
			while($fecha3 < $fecha2 )
			{
				/*ir aumentando de a un dia para crear en el query las fechas entre las dos fechas limites*/
				$fechas=$fechas." or Fecha_data='".$fecha3."'";
				$day3++;
				if ($day3 =='29' and $month3 =='2')
				{
					
					//ver si el años es bisiesto
					if(!checkdate($month3,$day3,$year3))
					{
						$day3='01';
						$month3='03';
					}
					
				}
				
				
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
		
		if($cc=="Todos")
		{
			$cc="";
			$span=3;
			$name="";
			//$w=750;
		}
		else
		{
			$name="</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>CENTRO DE COSTOS: ".$cc."</b></font>";
			$cc="Cc='".$cc."' and ";
			$span=4;
			//$w=700;
		}
		/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table border=1 align='center' width=750><tr><td rowspan=4 align='center' colspan='0'>";
		echo "<img SRC='/MATRIX/images/medical/root/americas10.jpg'  size=150 width=128></td>";
		echo "<td align=center colspan='4' ><font size=3 color='#000080' face='arial'><B>CLÍNICA LAS AMÉRICAS</b></font></td>";
		echo "</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>MATERIAL Y MEDICAMENTOS INGRESADOS</b></font>";
		echo $name."</tr><tr><td  align=center colspan='4'><font size=2 color='#000080' face='arial'><B>".$desde;
		echo "</tr></table>";

		if($cc != "")
		{
			echo "<table border=1 align='center' width=750>";
			echo "<TR><td width='75'><font size=2 face='arial'><b>FECHA</td>";
			echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
			echo "<td width='40'><font size=2 face='arial'><b>REG.</td>";
			echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
			echo "<td ><font size=2 face='arial'><b>PACIENTE</td>";
			echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
			echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
			echo "<td ><font size=2 face='arial'><b>CANT.</td>";
			echo "<td width='75'><font size=2 face='arial'><b>DOC.</td></TR>";
			$query = "select fecha_data,hora_data,reg_num,historia,paciente,cod_articulo,descripcion_art,cantidad,fuente from farmpda_000001 where ".$cc." ".$hora."  order by Historia,Reg_num";
			$err = mysql_query($query,$conex);
			$doc="";$l=0;$est="";$nums="";
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_row($err);
					$ini1=strpos($row[2],"-");
					$lin=substr($row[2],$ini1+1);
					/*Si el numero del registro es diferente al que habia almacenado*/
					if(substr($row[2],0,$ini1) != $nums)
					{
						$nums=substr($row[2],0,$ini1);
						$doc="";$l=0;
						/*Buscar el numero del documento en el que quedaron almacenados los articulos*/
						$query1="select Drodocdoc from itdrodoc where Drodocnum=".$nums." and Drodocfue=".$row[8]." ";
						$err_o = odbc_do($conex_o,$query1);
						if (odbc_fetch_row($err_o))
						{
							$doc=odbc_result($err_o,1);
							$est=$doc;//por el momento
							$color="#ffffff";//por el momento
						}
						else
						{
							$est="NO INGRESADO A FACT.";
							$color="#00AADD";
						}
						/*Buscar si hubo algun error o alguna inconsistencia al ingresar los articulos a la facturacion**/
						$query2="select Droinclin,Droincdes from itdroinc where Droincnum=".$nums." order by Droinclin";
						$err_o1 = odbc_do($conex_o,$query2);
						$i=0;
						while (odbc_fetch_row($err_o1))
						{
								//Encontro inconsistencias
								$line[$i]=odbc_result($err_o1,1);
								$des[$i]=odbc_result($err_o1,2);
								$i++;
						}
						/*Si existe una inconsistencia para ese numero de linea y ese registro*/
						if($color != "#00AADD")
						{
							$est=$doc;
							$color="#ffffff";
						}
						for($u=0;$u<$i;$u++)
						{
							if($line[$u] == $lin)
							{
								
								$est=$est." ".$des[$u];
								$color="#ffff00";
							}
						}
						/*esta bien*/
					}
					else
					/*El numero del registro es igual al que habian almacenado*/
					{
						if($color != "#00AADD")
						{
							$est=$doc;
							$color="#ffffff";
						}
						for($u=0;$u<$i;$u++)
						{
							if($line[$u] == $lin)
							{
								$est=$est."<br>".$des[$u];
								$color="#ffff00";
							}
						}
					}
					/*Buscamos en el Unix las unidades*/
						$query_o="select artuni from ivart where artcod='".$row[5]."'";
						$err_o= odbc_do($conex_o,$query_o);
						if (odbc_fetch_row($err_o))
							$uni=odbc_result($err_o,1);
						else
						 	$uni="unidades no encontradas en el sistema";
					echo "<TR><td bgcolor='".$color."' width='75'><font size=2 face='arial'>".$row[0]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".substr($row[1],0,5)."</td>"; 
					echo "<td bgcolor='".$color."' width='40'><font size=2 face='arial'>".$row[2]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[3]."</td>";
					echo "<td bgcolor='".$color."'><font size=1 face='arial'>".$row[4]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[5]."</td>";
					echo "<td bgcolor='".$color."'><font size=1 face='arial'>".$row[6]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[7].$uni."</td>";
					echo "<td bgcolor='".$color."' width='75'><font size=2 face='arial'>".$est."</td>";
				}
			}
			
			/*IMPRESION DE ARTICULOS REGISTRADOS PARA DEVOLUCIÓN (POR FUENTE 12 )*/
			$list="";
			$cant=0;
			
			$query = "select fecha_data,hora_data,reg_num,historia,paciente,cod_articulo,descripcion_art,cantidad,fuente from farmpda_000003 where ".$cc." ".$hora."  order by Historia,Reg_num";
			//echo $query;
			$err = mysql_query($query,$conex);
			$doc="";$l=0;$est="";$nums="";
			$num = mysql_num_rows($err);
			if($num>0)
			{
				echo "<tr><td align='center'colspan='9'><font size=2 color=#000066 face='arial'><b>DEVOLUCIONES</font><br>";
				echo "<TR><td width=75><font size=2 face='arial'><b>FECHA</td>";
				echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
				echo "<td width=40 ><font size=2 face='arial'><b>REG.</td>";
				echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
				echo "<td ><font size=2 face='arial'><b>PACIENTE</td>";
				echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
				echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
				echo "<td ><font size=2 face='arial'><b>CANT.</td>";
				echo "<td width=75 ><font size=2 face='arial'><b>DOC.</td></TR>";
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_row($err);
					$ini1=strpos($row[2],"-");
					$lin=substr($row[2],$ini1+1);
					if(substr($row[2],0,$ini1) != $nums)
					{
						
							$nums=substr($row[2],0,$ini1);
							$doc="";$l=0;
							/*Buscar el numero del documento en el que quedaron almacenados los articulos*/
							$query1="select Drodocdoc from itdrodoc where Drodocnum=".$nums." and Drodocfue=".$row[8]." ";
							$err_o = odbc_do($conex_o,$query1);
							if (odbc_fetch_row($err_o))
							{
								//echo"encontro el numero";
								$doc=odbc_result($err_o,1);
								$est=$doc;//por el momento
								$color="#ffffff";//por el momento
							}
							else
							{
								$est="NO INGRESADO A FACT.";
								$color="#00AADD";
							}
							/*Buscar si hubo algun error o alguna inconsistencia al ingresar los articulos a la facturacion**/
							$query2="select Droinclin,Droincdes from itdroinc where Droincnum=".$nums." order by Droinclin";
							$err_o1 = odbc_do($conex_o,$query2);
							$i=0;
							while (odbc_fetch_row($err_o1))
							{
								//Encontro inconsistencias
								$line[$i]=odbc_result($err_o1,1);
								$des[$i]=odbc_result($err_o1,2);
								$i++;
							}
							/*Si existe una inconsistencia para ese numero de linea y ese registro*/
							if($color != "#00AADD")
							{
								$est=$doc;
								$color="#ffffff";
							}
							for($u=0;$u<$i;$u++)
							{
							/**Buscar si la linea coincide con una de las encotradas para el registro*/
								if($line[$u] == $lin)
								{
									
									$est=$est." ".$des[$u];
									$color="#ffff00";
								}
							}
							/*esta bien*/
					}
					else
					/*El numero del registro es igual al que habian almacenado*/
					{
			//			echo "entro al else";
						if($color != "#00AADD")
						{
							$est=$doc;
							$color="#ffffff";
						}
						for($u=0;$u<$i;$u++)
						{
						/**Buscar si la linea coincide con una de las encotradas para el registro*/
							if($line[$u] == $lin)
							{
								$est=$est."<br>".$des[$u];
								$color="#ffff00";
							}
						}
					}
					/*Buscamos en el Unix las unidades*/
						$query_o="select artuni from ivart where artcod='".$row[5]."'";
						$err_o= odbc_do($conex_o,$query_o);
						if (odbc_fetch_row($err_o))
							$uni=odbc_result($err_o,1);
						else
						 	$uni="unidades no encontradas en el sistema";
					echo "<TR><td width=75 bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[0]."</td>";
					echo "<td width=40 bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".substr($row[1],0,5)."</td>"; 
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[2]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[3]."</td>";
					echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[4]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[5]."</td>";
					echo "<td bgcolor='".$color."'><font size=1 face='arial' color='#ff0000'>".$row[6]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[7].$uni."</td>";
					echo "<td width=75 bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$est."</td>";
				}
			}
			
		}
		else
		{
			echo "<table border=1 align='center' width=750>";
			echo "<TR><td width=30><font size=2 face='arial'><b>CC</td>";
			echo "<td width=75><font size=2 face='arial'><b>FECHA</td>";
			echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
			echo "<td width=45><font size=2 face='arial'><b>REG.</td>";
			echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
			echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
			echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
			echo "<td ><font size=2 face='arial'><b>CANT.</td>";
			echo "<td width=75><font size=2 face='arial'><b>DOC.</td></TR>";
			$query = "select Cc,fecha_data,hora_data,reg_num,historia,cod_articulo,descripcion_art,cantidad,reposicion,fuente from farmpda_000001 where ".$cc." ".$hora." order by Reg_num";
			$doc="";$l=0;$est="";$nums="";
			$err = mysql_query($query,$conex);
			$doc="";$l=0;$est="";$nums="";
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_row($err);
					$ini1=strpos($row[3],"-");
					$lin=substr($row[3],$ini1+1);
					/*Si el numero del registro es diferente al que habia almacenado*/
					if(substr($row[3],0,$ini1) != $nums)
					{
						$nums=substr($row[3],0,$ini1);
						$doc="";$l=0;
						/*Buscar el numero del documento en el que quedaron almacenados los articulos*/
						$query1="select Drodocdoc from itdrodoc where Drodocnum=".$nums." and Drodocfue=".$row[9]." ";
						$err_o = odbc_do($conex_o,$query1);
						if (odbc_fetch_row($err_o))
						{
							//echo"encontro el numero";
							$doc=odbc_result($err_o,1);
							$est=$doc;//por el momento
							$color="#ffffff";//por el momento
						}
						else
						{
							$est="NO INGRESADO A FACT.";
							$color="#00AADD";
						}
						/*Buscar si hubo algun error o alguna inconsistencia al ingresar los articulos a la facturacion**/
						$query2="select Droinclin,Droincdes from itdroinc where Droincnum=".$nums." order by Droinclin";
						$err_o1 = odbc_do($conex_o,$query2);
						$i=0;
						while (odbc_fetch_row($err_o1))
						{
								$line[$i]=odbc_result($err_o1,1);
								$des[$i]=odbc_result($err_o1,2);
								$i++;
								
						}
						/*Si existe una inconsistencia para ese numero de linea y ese registro*/
						if($color != "#00AADD")
						{
							$est=$doc;
							$color="#ffffff";
						}
						for($u=0;$u<$i;$u++)
						{
						/**Buscar si la linea coincide con una de las encotradas para el registro*/
							if($line[$u] == $lin)
							{
								$est=$est."<br>".$des[$u];
								$color="#ffff00";
							}
						}
						/*esta bien*/
					}
					else
					/*El numero del registro es igual al que habian almacenado*/
					{
						if($color != "#00AADD")
						{
							$est=$doc;
							$color="#ffffff";
						}
						for($u=0;$u<$i;$u++)
						{
						/**Buscar si la lina coincide con una de las encotradas para el registro*/
							if($line[$u] == $lin)
							{
								$est=$est."<br>".$des[$u];
								$color="#ffff00";
							}
						}
					}
					/*Buscamos en el Unix las unidades*/
					$query_o="select artuni from ivart where artcod='".$row[5]."'";
					$err_o= odbc_do($conex_o,$query_o);
					if (odbc_fetch_row($err_o))
						$uni=odbc_result($err_o,1);
					else
					 	$uni="unidades no encontradas en el sistema";
					echo "<TR><td width=30 bgcolor='".$color."'><font size=2 face='arial'><b>".$row[0]."</b></td>";
					echo "<td width=75 bgcolor='".$color."'><font size=2 face='arial'>".$row[1]."</td>"; 
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".substr($row[2],0,5)."</td>";
					echo "<td width=45 bgcolor='".$color."'><font size=2 face='arial'>".$row[3]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[4]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[5]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[6]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial'>".$row[7].$uni."</td>";
					echo "<td bgcolor='".$color."' width=75><font size=2 face='arial'>".$est."</td>";
				}
			}
			
			
			/*IMPRESION DE ARTICULOS REGISTRADOS PARA DEVOLUCIÓN (POR FUENTE 12 )*/
			$list="";
			$cant=0;
			
			$query = "select Cc,fecha_data,hora_data,reg_num,historia,cod_articulo,descripcion_art,cantidad,fuente from farmpda_000003 where ".$cc." ".$hora."  order by Historia,Reg_num";
			//echo $query;
			$err = mysql_query($query,$conex);
			$doc="";$l=0;$est="";$nums="";
			$num = mysql_num_rows($err);
			if($num>0)
			{
				echo "</tablE><table border=1 align='center' width=750>";
				echo "<tr><td align='center'colspan='9'><font size=2 color=#000066 face='arial'><b>DEVOLUCIONES</font><br>";
				echo "<TR><td width=30><font size=2 face='arial'><b>CC</td>";
				echo "<td width=75><font size=2 face='arial'><b>FECHA</td>";
				echo "<td ><font size=2 face='arial'><b>HORA</td>"; 
				echo "<td width=45><font size=2 face='arial'><b>REG.</td>";
				echo "<td ><font size=2 face='arial'><b>HISTORIA</td>";
				echo "<td ><font size=2 face='arial'><b>CODIGO</td>";
				echo "<td ><font size=2 face='arial'><b>DESCRIPCION</td>";
				echo "<td ><font size=2 face='arial'><b>CANT.</td>";
				echo "<td width=75><font size=2 face='arial'><b>DOC.</td></TR>";
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_row($err);
					$ini1=strpos($row[3],"-");
					$lin=substr($row[3],$ini1+1);
					/*Si el numero del registro es diferente al que habia almacenado*/
					if(substr($row[3],0,$ini1) != $nums)
					{
						$nums=substr($row[3],0,$ini1);
						$doc="";$l=0;
						/*Buscar el numero del documento en el que quedaron almacenados los articulos*/
						$query1="select Drodocdoc from itdrodoc where Drodocnum=".$nums." and Drodocfue=".$row[8]." ";
						$err_o = odbc_do($conex_o,$query1);
						if (odbc_fetch_row($err_o))
						{
							//echo"encontro el numero";
							$doc=odbc_result($err_o,1);
							$est=$doc;//por el momento
							$color="#ffffff";//por el momento
						}
						else
						{
							$est="NO INGRESADO A FACT.";
							$color="#00AADD";
						}
						/*Buscar si hubo algun error o alguna inconsistencia al ingresar los articulos a la facturacion**/
						$query2="select Droinclin,Droincdes from itdroinc where Droincnum=".$nums." order by Droinclin";
						$err_o1 = odbc_do($conex_o,$query2);
						$i=0;
						while (odbc_fetch_row($err_o1))
						{
								$line[$i]=odbc_result($err_o1,1);
								$des[$i]=odbc_result($err_o1,2);
								$i++;
								
						}
						/*Si existe una inconsistencia para ese numero de linea y ese registro*/
						if($color != "#00AADD")
						{
							$est=$doc;
							$color="#ffffff";
						}
						/**Buscar si la lina coincide con una de las encotradas para el registro*/
						for($u=0;$u<$i;$u++)
						{
							if($line[$u] == $lin)
							{
								$est=$est."<br>".$des[$u];
								$color="#ffff00";
							}
						}
						/*esta bien*/
					}
					else
					/*El numero del registro es igual al que habian almacenado*/
					{
						if($color != "#00AADD")
						{
							$est=$doc;
							$color="#ffffff";
						}
						for($u=0;$u<$i;$u++)
						{
							/**Buscar si la lina coincide con una de las encotradas para el registro*/
							if($line[$u] == $lin)
							{
								$est=$est."<br>".$des[$u];
								$color="#ffff00";
							}
						}
					}
					/*Buscamos en el Unix las unidades*/
					$query_o="select artuni from ivart where artcod='".$row[5]."'";
					$err_o= odbc_do($conex_o,$query_o);
					if (odbc_fetch_row($err_o))
						$uni=odbc_result($err_o,1);
					else
					 	$uni="unidades no encontradas en el sistema";
					echo "<TR><td width=30 bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'><b>".$row[0]."</b></td>";
					echo "<td width=75 bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[1]."</td>"; 
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".substr($row[2],0,5)."</td>";
					echo "<td width=45 bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[3]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[4]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[5]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[6]."</td>";
					echo "<td bgcolor='".$color."'><font size=2 face='arial' color='#ff0000'>".$row[7].$uni."</td>";
					echo "<td bgcolor='".$color."' width=75><font size=2 face='arial' color='#ff0000'>".$est."</td>";
				}
			}
		}
	}
}
?>
</body>
</html>