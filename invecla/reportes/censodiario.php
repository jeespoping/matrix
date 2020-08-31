
  <title>CENSO DIARIO DE PACIENTES</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");

/********************************************************
*     REPORTE DE CENSO DIARIO DE PACIENTES		        *
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de censo diario de pacientes
//AUTOR							:Juan David Londoño
//FECHA CREACION				:FEBRERO 2006
//FECHA ULTIMA ACTUALIZACION 	:29 de Marzo de 2007
//DESCRIPCION					:Este reporte muestra el ceso de los pacientes que se encuentran en la clinica, consulta en los
//								 formularios cominf-000032 y cominf-000033.
//ACTUALIZACIONES				:
//								 2007-03-29: Se quitaron los egresos por traslado cuando se escoge la opcion 9999-todos los servicios
//								 2007-04-02: Se quitaron los egresos por traslado para calcular la tasa de moratalidad cuando
//											 escoge la opcion 9999-todos los servicios.
//								 
//==================================================================================================================================

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(unidad, fecha inicial y fecha final)

	echo "<form action='' method=post>";

	if(!isset($unidad)  or !isset($fecha1) or !isset($fecha2))

	{
		
		
		echo "<center><table border=0 width=400>";
		echo "<tr><td align=center colspan=3><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=3>CENSO DIARIO</td></tr>";
		
		
		echo "<table border=(1) align=center>";
		
		echo "<tr><td>Unidad:</td><td><select name='unidad'>";
			$query = "SELECT Subcodigo,Descripcion FROM `det_selecciones` WHERE medico = 'cominf' AND codigo = '087'  order by Descripcion ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				echo "<option>9999-Todos los Servicios</option>";	
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				
				}
			}
		echo "</select></td></tr>";
		echo "<tr><td>Fecha Inicial:</td><td><input type='TEXT' name='fecha1' size=10 maxlength=10  >AAAA-MM-DD</td></tr>";
		echo "<tr><td>Fecha Final:</td><td><input type='TEXT' name='fecha2' size=10 maxlength=10 >AAAA-MM-DD</td></tr>";
		echo "<tr><td colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		echo "</table>";
	
	
	}else if($fecha1==''){
		echo"<br>";
		echo"<br>";
		echo "<font size='6'color=#FF3300><i>DEBE INGRESAR UNA FECHA INICIAL DE CORTE</MARQUEE></FONT>";
	}else if($fecha2==''){
		echo"<br>";
		echo"<br>";
		echo "<font size='6'color=#FF3300><i>DEBE INGRESAR UNA FECHA FINAL DE CORTE</MARQUEE></FONT>";
	}else if($fecha1>$fecha2){
		echo"<br>";
		echo"<br>";
		echo "<font size='6'color=#FF3300><i>LA FECHA INICIAL DEBE SER MENOR QUE LA FECHA FINAL</MARQUEE></FONT>";
	}


	
		else
		{ 
				
		if ($unidad=="9999-Todos los Servicios")
			{
				$variable="";
			}else
			{
				$variable="and Servicio='".$unidad."'";
			}
			
		
			$query = "SELECT * FROM cominf_000032  
					WHERE (Fecha_ing between '$fecha1' and '$fecha2') 
					and Procedencia='03-Urgencias' ".$variable."";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$numur = mysql_num_rows($err);	
			if($numur==0)
			{
				$numur=0;
			}
			//echo $query;
			$query =  "SELECT * FROM cominf_000032  
					WHERE (Fecha_ing between '$fecha1' and '$fecha2') 
					and Procedencia='01-Admisiones'  ".$variable."";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$numad = mysql_num_rows($err);	
			if($numad==0)
			{
				$numad=0;
			}
			
			$query =  "SELECT * FROM cominf_000032  
					WHERE (Fecha_ing between '$fecha1' and '$fecha2') 
					and Procedencia='02-Cirugia_Parto PMLA'  ".$variable."";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$numci = mysql_num_rows($err);	
			if($numci==0)
			{
				$numci=0;
			}
			
			$numtot = $numur+$numad+$numci;
			
			$query =  "SELECT * FROM cominf_000032  
					WHERE (Fecha_ing between '$fecha1' and '$fecha2') 
					and (Procedencia='12-Otro servicio' " .
							"or Procedencia like '%13-1020-Cuidados Intensivos%' " .
							"or Procedencia like '%14-1180-Cuidados Especiales%'" .
							"or Procedencia like '%15-1182-Hosp piso 3 PAP%'" .
							"or Procedencia like '%16-1183-Hosp piso 4%'" .
							"or Procedencia like '%17-1184-Hosp piso 5%'" .
							"or Procedencia like '%18-1185-Hosp piso 6%'" .
							"or Procedencia like '%19-1186-Hosp piso 7%'" .
							"or Procedencia like '%20-1187-UCE Primer piso%'" .
							"or Procedencia like '%21-1188-Hosp Oncologica%'" .
							"or Procedencia like '%22-1190-Neonatos%'" .
							"or Procedencia like '%23-1189-Hosp Transplante Medula Osea%') ".$variable."";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$numixt = mysql_num_rows($err);	
			if($numixt==0)
			{
				$numixt=0;
			}
			
			$query = "SELECT * FROM cominf_000033  WHERE (Fecha_egre_serv between '$fecha1' and '$fecha2') 
						 and (Tipo_egre_serv ='01-Alta de la Clinica' or Tipo_egre_serv ='05-Remision otra IPS') ".$variable."";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			
			$numeal = mysql_num_rows($err);	
			if($numeal==0)
			{
				$numeal=0;
			}
			
			if ($numeal>0)
		 	{
			 $totalt=0;
			 	for ($i=0;$i<$numeal;$i++)
			 			{
				 			$alnum = mysql_fetch_array($err);
				 			$totalt=$totalt+$alnum['Dias_estan_serv'];
			 			}
		 	}else{
		 		$totalt=0;
		 	}
			
			
			$query = "SELECT * FROM cominf_000033  WHERE (Fecha_egre_serv between '$fecha1' and '$fecha2') 
						 and (Tipo_egre_serv ='07-Muerte menor 24 hr' or Tipo_egre_serv ='07-Muerte menor 48 hr')".$variable."";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			
			$numemv = mysql_num_rows($err);	
			if($numemv==0)
			{
				$numemv=0;
			}
			
			if ($numemv>0)
		 	{
			 $totemv=0;
			 	for ($i=0;$i<$numemv;$i++)
			 			{
				 			$mvnum = mysql_fetch_array($err);
				 			$totemv=$totemv+$mvnum['Dias_estan_serv'];
			 			}
		 	}else{
		 		$totemv=0;
		 	}
			
			$query = "SELECT * FROM cominf_000033  WHERE (Fecha_egre_serv between '$fecha1' and '$fecha2') 
						 and (Tipo_egre_serv ='08-Muerte mayor 24 hr' or Tipo_egre_serv ='08-Muerte mayor 48 hr')".$variable."";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			
			$numemh = mysql_num_rows($err);	
			if($numemh==0)
			{
				$numemh=0;
			}
			
			if ($numemh>0)
		 	{
			 $totemh=0;
			 	for ($i=0;$i<$numemh;$i++)
			 			{
				 			$emhnum = mysql_fetch_array($err);
				 			$totemh=$totemh+$emhnum['Dias_estan_serv'];
			 			}
		 	}else{
		 		$totemh=0;
		 	}
			
			
			$query = "SELECT * FROM cominf_000033  WHERE (Fecha_egre_serv between '$fecha1' and '$fecha2') 
						 and Tipo_egre_serv ='06-Voluntaria o fuga' ".$variable."";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			
			$numfu = mysql_num_rows($err);	
			if($numfu==0)
			{
				$numfu=0;
			}
			
			if ($numfu>0)
		 	{
			 $totfu=0;
			 	for ($i=0;$i<$numfu;$i++)
			 			{
				 			$funum = mysql_fetch_array($err);
				 			$totfu=$totfu+$funum['Dias_estan_serv'];
			 			}
		 	}else{
		 		$totfu=0;
		 	}
			$numtote=$numeal+$numemv+$numemh+$numfu;
			
			$diest=$totalt+$totemv+$totemh+$totfu;
			
			
						 		
				$query = "SELECT * FROM cominf_000033  WHERE (Fecha_egre_serv between '$fecha1' and '$fecha2') 
							 and (Tipo_egre_serv ='04-Traslado otro serv Clin Las Amer' " .
							 		"or Tipo_egre_serv = '13-1183-Traslado Hosp piso 4'" .
							 		"or Tipo_egre_serv = '10-1020-Traslado Cuidados Intensivos'" .
							 		"or Tipo_egre_serv = '11-1180-Traslado Cuidados Especiales'" .
							 		"or Tipo_egre_serv = '12-1182-Traslado Hosp piso 3 PAP'" .
							 		"or Tipo_egre_serv = '14-1184-Traslado Hosp piso 5'" .
							 		"or Tipo_egre_serv = '15-1185-Traslado Hosp piso 6'" .
							 		"or Tipo_egre_serv = '16-1186-Traslado Hosp piso 7'" .
							 		"or Tipo_egre_serv = '17-1187-Traslado UCE Primer piso'" .
							 		"or Tipo_egre_serv = '18-1188-Traslado Hosp Oncologica'" .
							 		"or Tipo_egre_serv = '19-1190-Traslado Neonatos'" .
							 		"or Tipo_egre_serv = '20-1189-Hosp Transplante Medula Osea') ".$variable."";
				$err = mysql_query($query,$conex);
				//echo mysql_errno() ."=". mysql_error();
				//echo $query;
				$numtra = mysql_num_rows($err);	
			
				if(!isset ($numtra))
				{
					$numtra=0;
				}
			
			
		if ($numtra>0)
		 	{
			 $totras=0;
			 	for ($i=0;$i<$numtra;$i++)
			 			{
				 			$resulta= mysql_fetch_array($err);
				 			$totras=$totras+$resulta['Dias_estan_serv'];
			 			}
			 			
		 	}else{
		 		$totras=0;
		 	}
		 	
		 	if (($numtote+$numtra)==0){
		 		$promest=0;
		 		echo "";
		 	}else{
		 	$promest= ($diest+$totras)/($numtote+$numtra);  // Promedio de estancia
		 	}
		 	
		 	if (($numtote+$numtra)==0){
		 		$tasmor=0;
		 		echo "";
		 	}else{
		 		
		 	if ($unidad == "9999-Todos los Servicios")
			{
				$tasmor=(($numemv+$numemh)/($numtote))*100;   // Tasa de Mortalidad
			}else
				{
			 		$tasmor=(($numemv+$numemh)/($numtote+$numtra))*100;   // Tasa de Mortalidad
				}
		 	}
		 	
			/* IMPRESION DE LOS DATOS EN PANTALLA*/
		echo "<table border=0 align=center >";
		echo "<tr><td align=center rowspan=2 colspan=3><img SRC='/MATRIX/images/medical/root/clinica.jpg'></td>";
		echo "<tr><td align=center colspan=7><font size='6'color=#000066><i>CENSO DIARIO DE PACIENTES<br><font size='1'>Ver. 2007-04-02</font></td></tr>";
		echo "</table>";
		echo "<table border=1 align=center bgcolor=#DDDDDD>";
		echo "<tr><td align=center COLSPAN=2><font size='4'>Servicio:</td><td COLSPAN=3> ".$unidad."</td></tr>";
		echo "<tr><td align=center COLSPAN=2><font size='4'>Fechas:</td><td COLSPAN=3> <i>Desde</i> <b>".$fecha1."</b> <i>hasta</i><b> ".$fecha2."</b></td></tr>";
		echo "</table>";
		echo"<br>";
		echo "<table border=1 align=center bgcolor=#99CCFF>";
		echo "<tr><td align=center colspan=4><font size='2'>INGRESOS</td><td align=center rowspan=2><font size='2'>INGRESOS POR TRASLADOS</td>
			<td align=center colspan=5><font size='2'>EGRESOS</td><td align=center rowspan=2><font size='2'>EGRESOS POR TRASLADOS</td>
			<td align=center rowspan=2><font size='2'>DIAS ESTANCIA</td><td align=center rowspan=2><font size='2'>DIAS TRASLADOS</td></tr>";
		echo "<tr><td>Urgencias</td><td>Admisiones</td><td>Cirugia</td><td><b>Total</td><td>Alta</td><td>Muerte -48h</td>
			<td>Muerte +48h</td><td>Fuga</td><td><b>Total</td></tr>";
		echo "<tr><td align=center>".$numur."</td><td align=center>".$numad."</td><td align=center>".$numci."</td><td align=center>".$numtot."</td><td align=center>".$numixt."</td>
			<td align=center>".$numeal."</td><td align=center>".$numemv."</td><td align=center>".$numemh."</td><td align=center>".$numfu."</td><td align=center>".$numtote."</td><td align=center>".$numtra."</td>
			<td>".$diest."</td><td>".$totras."</td></tr>";
		echo "</table>";
		echo"<br>";
		echo"<br>";
		echo "<table border=0 align=center >";
		echo "<tr><td align=center colspan=7><font size='5'color=#000066><i>INDICADORES</td></tr>";
		echo "</table>";
		echo"<br>";
		echo "<table border=1 align=center bgcolor=#DDDDDD>";
		echo "<tr><td align=center COLSPAN=2><font size='4'>Promedio estancia:</td><td COLSPAN=3> ".number_format($promest,2,'',".")." dias</td></tr>";
		echo "</table>";
		echo"<br>";
		echo "<table border=1 align=center bgcolor=#DDDDDD>";
		echo "<tr><td align=center COLSPAN=2><font size='4'>Tasa de Mortalidad:</td><td COLSPAN=3> ".number_format($tasmor,2,'',".")."%</td></tr>";
		echo "</table>";
	}
	include_once("free.php");
}
?>