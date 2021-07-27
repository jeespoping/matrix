<head>
	<title>ESTADISTICAS DE CAMILLEROS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");

//echo "<meta http-equiv='refresh' content='10;url=central_de_camilleros.php?'>";

  /***************************************************
	*	        ESTADISTICAS DE CAMILLEROS           *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	

	include_once("root/comun.php");
	$wemp_pmla=$_REQUEST['wemp_pmla'];						
    mysql_select_db("matrix") or die("No se ralizo Conexion");
    
	//$conexunix = odbc_pconnect('facturacion','facadm','1201')
  	//				    or die("No se ralizo Conexion con el Unix");
  					    
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
    
          
    //====================================================================================================================================
    //COMIENZA LA FORMA      
    echo "<form action='estadisticas_fechayhora.php?wemp_pmla=".$wemp_pmla."' method=post>";
    
    $wfecha=date("Y-m-d"); 
    $hora = (string)date("H:i:s");
	$wactualiz = "2012-06-06";
	
	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
    //encabezado("ESTADISTICAS SERVICIO DE CAMILLEROS POR RANGO DE HORAS EN EL PERIODO",$wactualiz, "clinica");
    echo "<center><table>";
    //echo "<tr><td align=center class='fila1' colspan=5><b>CLINICA LAS AMERICAS</b></td></tr>";
    echo "<tr><td align=center class='fila1' colspan=5><font size=6><b>ESTADISTICAS SERVICIO DE CAMILLEROS POR RANGO DE HORAS<br>EN EL PERIODO</b></font></td></tr>";
	echo "<tr class=fila2><td align='right'><span class='version'>versi&oacute;n: (".$wactualiz.")</span></td></tr>";
	echo "<tr><td> </td></tr>";
	echo "<tr><td> </td></tr>";
	echo "</table></center>";
    
    
	echo "<center><table border=2>";
	echo "<tr><td align=center class='encabezadotabla' colspan=5><b>Los tiempos son tomados de el momento de solicitud hasta la llegada al servicio</b></font></td></tr>";
    if (!isset($wfecha_i) or !isset($wfecha_f))
       {
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //FECHA INICIAL Y FINAL
        echo "<tr class=fila1>";
        echo "<td align=left><b>Fecha Inicial: </b><INPUT TYPE='text' NAME='wfecha_i' VALUE=".$wfecha."></td>";  
        echo "<td align=left><b>Fecha Final: </b><INPUT TYPE='text' NAME='wfecha_f' VALUE=".$wfecha."></td>";  
	    echo "</tr>";
	    
	    echo "<tr class=fila2>";
        echo "<td align=left><b>Hora Inicial (HH:MM:SS): </b><INPUT TYPE='text' NAME='whora_i' VALUE='".$hora."'></td>";  
        echo "<td align=left><b>Hora Final (HH:MM:SS): </b><INPUT TYPE='text' NAME='whora_f' VALUE='".$hora."'></td>";  
	    echo "</tr>";
		echo "</table></center>";
		
		echo "<center><table>";
		echo"<tr><td> </td></tr>";
		echo"<tr><td> </td></tr>";
		echo"<tr><td> </td></tr>";
	    echo "<tr>";
        echo "<td align=center><input type='submit' value='Consultar'></td>";   
        echo "</tr>";
		echo "</table></center>";
		
       }
      else
         {
	        echo "<tr>";
	        echo "<td align=center class=fila1 colspan=5><b>Período de Consulta: </b>".$wfecha_i."<b> al </b>".$wfecha_f."</td>";  
	        echo "</tr>";
	        
	        echo "<tr>";
	        echo "<td align=center class=fila2 colspan=5><b>Rango de horas Consultado: </b>".$whora_i."<b> al </b>".$whora_f."</td>";  
	        echo "</tr>";

			echo "</table></center>";
			
			echo "<center><table>";
			echo"<tr><td> </td></tr>";
			echo"<tr><td> </td></tr>";
			echo"<tr><td> </td></tr>";
			echo "</table></center>";
			
			echo "<center><table border=2>";
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS EN ** EL PERIODO **
		    //===================================================================================================================================================
		    $q =  "  SELECT count(*) "
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				$wtotser=$row[0];
				echo "<tr class=fila2>";
				echo "<td colspan=2><font size=4>Total servicios en el período (No incluye los Anulados, ni los No atendidos): "."</font></td>";
				echo "<td colspan=2 align=center>".number_format($row[0],0,'','')."</td>";
				echo "<td>&nbsp;</td>";
				echo "</tr>";
			   } 
		    
			//===================================================================================================================================================
		    // QUERY: PROMEDIO DE LLEGADA A LOS SERVICIOS 
		    //===================================================================================================================================================
		    $q=   " SELECT SUM(HOUR(TIMEDIFF(hora_llegada,hora_data))),"
				 ." 	   SUM(MINUTE(TIMEDIFF(hora_llegada,hora_data))),"
				 ." 	   SUM(SECOND(TIMEDIFF(hora_llegada,hora_data))),"
				 ." 	   SUM(DAY(TIMEDIFF(fecha_llegada,fecha_data))),"
				 ." 	   SUM(MONTH(TIMEDIFF(fecha_llegada,fecha_data))),"
				 ."		   SUM(YEAR(TIMEDIFF(fecha_llegada,fecha_data)))"
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			//echo $q;
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
			
			if ($num > 0)
			   {
			    $row = mysql_fetch_array($res);
				$wprom = $row[3]*24*60;
				$wprom = $wprom + ($row[4]*30*24*60);
				$wprom = $wprom + ($row[5]*12*30*24*60);
			    $wprom = $wprom + $row[0]*60;
			    $wprom = $wprom + ($row[1]);
			    $wprom = $wprom + ($row[2]/60);
			    
			    //$wprom ya esta en minutos, ahora lo divido por el numero de servicios
			    $wproserseg=($wprom/$wtotser)-(integer)($wprom/$wtotser);
			    $wproserseg=60*($wproserseg/100);
			    $wproser=number_format((((integer)($wprom/$wtotser))+$wproserseg),2,',','');
			    
			    echo "<tr class=fila1>";
				echo "<td colspan=2><font size=4>Promedio de llegada al servicio que solicito (en minutos): </font></td>";
				echo "<td colspan=2 align=center>".$wproser."</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
		       }
			
		    //===================================================================================================================================================
		    // QUERY: SERVICIO QUE MAS TARDO 
		    //===================================================================================================================================================
		    $q=   "  SELECT Id, MAX((YEAR(TIMEDIFF(fecha_llegada,fecha_data))*12*30*24*60) +"
					 ."               (MONTH(TIMEDIFF(fecha_llegada,fecha_data))*30*24*60) +"
					 ."				  (DAY(TIMEDIFF(fecha_llegada,fecha_data))*24*60) +"
					 ."				  (HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
					 ."				   MINUTE(TIMEDIFF(hora_llegada,hora_data)) +"
					 ."					(SECOND(TIMEDIFF(hora_llegada,hora_data)))/60)"
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada   != '00:00:00' "
		         ."   GROUP BY id "
		         ."   ORDER BY 2 desc ";
		    $res = mysql_query($q,$conex) or die (mysql_errno()." 1111- ".mysql_error());
			$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
			
			if ($num > 0)
			   {
			    $row = mysql_fetch_array($res);
			    
			    echo "<tr class=fila2>";
				echo "<td colspan=2><font size=4>Solicitud que tardo MAS en ser atendida (en minutos): </font></td>";
				$wsegundos=60*(($row[1]-(integer)$row[1])/100);
				echo "<td colspan=2 align=center>".number_format(((integer)$row[1]+$wsegundos),2,'','')."</td>";
				echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&whora_i=".$whora_i."&whora_f=".$whora_f."&wid=".$row[0]."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'> Detallar</A></font></td>";
				echo "</tr>";
		       }   
		       
		    
		    //===================================================================================================================================================
		    // QUERY: SERVICIO QUE MENOS TARDO 
		    //===================================================================================================================================================
		    $q=   "  SELECT id, MIN((YEAR(TIMEDIFF(fecha_llegada,fecha_data))*12*30*24*60) +"
					 ."               (MONTH(TIMEDIFF(fecha_llegada,fecha_data))*30*24*60) +"
					 ."				  (DAY(TIMEDIFF(fecha_llegada,fecha_data))*24*60) +"
					 ."				  (HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
					 ."				   MINUTE(TIMEDIFF(hora_llegada,hora_data)) +"
					 ." 			  (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60)"
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY id "
		         ."   ORDER BY 2 ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
			
			if ($num > 0)
			   {
			    $row = mysql_fetch_array($res);
			    
			    echo "<tr class=fila1>";
				echo "<td colspan=2><font size=4>Solicitud que tardo MENOS en ser atendida (en minutos): </font></td>";
				$wsegundos=60*(($row[1]-(integer)$row[1])/100);
				echo "<td colspan=2 align=center>".number_format(((integer)$row[1]+$wsegundos),2,'','')."</td>";
				echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&whora_i=".$whora_i."&whora_f=".$whora_f."&amp;wid=".$row[0]."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'> Detallar</A></font></td>";
				echo "</tr>";
		       }   
		          
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ASIGNADOS PERO NO ATENDIDOS ** EN EL PERIODO **
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada = '00:00:00' ";
		    
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr class=fila2>";
				echo "<td colspan=2><font size=4>Total servicios asignados pero no atendidos (sin hora de llegada): "."</font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'',' ')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,',','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   }       
		          
			   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS EN ** MENOS O IGUAL AL PROMEDIO **
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ." 	  AND ((YEAR(TIMEDIFF(fecha_llegada,fecha_data))*12*30*24*60) +"
				 ."           (MONTH(TIMEDIFF(fecha_llegada,fecha_data))*30*24*60) +"
				 ."			  (DAY(TIMEDIFF(fecha_llegada,fecha_data))*24*60) +"
				 ."			  (HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
				 ."			   MINUTE(TIMEDIFF(hora_llegada,hora_data)) +"
				 ." 		  (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60) <= '".$wproser."'"
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' ";
			$res = mysql_query($q,$conex) or die (mysql_errno()." 333- ".mysql_error());
			$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr class=fila1>";
				echo "<td colspan=2><font size=4>Total servicios respondidos en menos de (".$wproser.") minutos: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'','')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,',','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   } 

		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS EN ** MAS DEL PROMEDIO **
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ." 	  AND ((YEAR(TIMEDIFF(fecha_llegada,fecha_data))*12*30*24*60) +"
				 ."           (MONTH(TIMEDIFF(fecha_llegada,fecha_data))*30*24*60) +"
				 ."			  (DAY(TIMEDIFF(fecha_llegada,fecha_data))*24*60) +"
				 ."			  (HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
				 ."			   MINUTE(TIMEDIFF(hora_llegada,hora_data)) +"
				 ." 		  (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60) > '".$wproser."'"
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' ";
		    $res = mysql_query($q,$conex)  or die (mysql_errno()."222- ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr class=fila1>";
				echo "<td colspan=2><font size=4>Total servicios respondidos en mas de (".$wproser.") minutos: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'','')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,',','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   } 	 
			   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ** ANULADOS ** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'Si' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr class=fila2>";
				echo "<td colspan=2><font size=4>Total servicios Anulados en el período: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'','')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,',','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   } 	
			
			//===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ** NO ATENDIDOS ** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta = '00:00:00' ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr class=fila1>";
				echo "<td colspan=2><font size=4>Total servicios No atendidos en el período (sin camillero): </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,',','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   }
			      
			echo "<tr></tr>";  
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ** POR CAMILLERO ** EN EL PERIODO
		    //===================================================================================================================================================
		    
		    $q=   "  SELECT Camillero, COUNT(*), SUM((YEAR(TIMEDIFF(fecha_llegada,fecha_data))*12*30*24*60) +"
				 ."               					(MONTH(TIMEDIFF(fecha_llegada,fecha_data))*30*24*60) +"
				 ."				  					(DAY(TIMEDIFF(fecha_llegada,fecha_data))*24*60) +"
				 ."				  					(HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
				 ."				  					 MINUTE(TIMEDIFF(hora_llegada,hora_data)) +"
				 ." 			  					(SECOND(TIMEDIFF(hora_llegada,hora_data)))/60)"
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY 1 "
		         ."   ORDER BY 2 desc ";
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
		    
			if ($num > 0)
			   {
				echo "<tr class='encabezadotabla'>";
				echo "<td colspan=5 align=center><b><font size=5>Servicios por camillero: </font></b></td>";
				echo "</tr>";
				echo "<tr class='encabezadotabla'>";
			    echo "<th>Empleado</th>";
			    echo "<th>Cantidad</th>";
			    echo "<th>% de participación</th>";
				echo "<th>Tiempo Promedio<br>(Mins)</th>";
				echo "<th>&nbsp</th>";
				echo "</tr>"; 
				$wtotal=0;  
				for ($i=1;$i<=$num;$i++)
				   {
					if(is_integer($i/2))
						$wclass="class=fila1";
						else
						$wclass="class=fila2";
				    $row = mysql_fetch_array($res); 
					echo "<tr ".$wclass.">";
					echo "<td colspan=1>".$row[0]."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'','')."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,',','')." %</td>";
				    $wsegundos=60*((($row[2]/$row[1])-(integer)($row[2]/$row[1]))/100);
				    echo "<td colspan=1 align=RIGHT>".number_format(((integer)($row[2]/$row[1])+$wsegundos),2,',','')."</td>";
					echo "<td>&nbsp;</td>";
				    $wtotal=$wtotal+$row[1];	   
			       }
			    echo "<tr class=fila1>";
			    echo "<td><font size=4>Total servicios: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,'','')."</td>"; 
				echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,',','')." %</td>";
				echo "<td colspan=2 align=RIGHT>&nbsp</td>";
//echo "<td>&nbsp;</td>";
				echo "</tr>";  
			   }
			
			echo "<tr></tr>";   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SOLICITUDES ** POR SERVICIO ** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT Origen, COUNT(*), SUM((YEAR(TIMEDIFF(fecha_llegada,fecha_data))*12*30*24*60) +"
					 ."               					(MONTH(TIMEDIFF(fecha_llegada,fecha_data))*30*24*60) +"
					 ."				  					(DAY(TIMEDIFF(fecha_llegada,fecha_data))*24*60) +"
					 ."				  					(HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
					 ."				  					 MINUTE(TIMEDIFF(hora_llegada,hora_data)) +"
					 ." 			  					(SECOND(TIMEDIFF(hora_llegada,hora_data)))/60)"
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY 1 "
		         ."   ORDER BY 2 desc ";
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				echo "<tr class='encabezadotabla'>";
				echo "<td colspan=5 align=center><b><font size=5>Solicitudes por Servicio o Unidad: </font></b></td>";
				echo "</tr>";
				echo "<tr class='encabezadotabla'>";
			    echo "<th>Servicio</th>";
			    echo "<th>Cantidad</th>";
			    echo "<th>% de participación</th>";
			    echo "<th>Tiempo Promedio<br>(Mins)</th>";
			    echo "<th>&nbsp</th>";
				echo "</tr>";
				$wtotal=0;
				for ($i=1;$i<=$num;$i++)
				   {
					if(is_integer($i/2))
						$wclass="class=fila1";
						else
						$wclass="class=fila2";
					$row = mysql_fetch_array($res); 
					echo "<tr ".$wclass.">";
					echo "<td colspan=1>".$row[0]."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'','')."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,',','')." %</td>";
				    $wsegundos=60*((($row[2]/$row[1])-(integer)($row[2]/$row[1]))/100);
				    echo "<td colspan=1 align=RIGHT>".number_format(((integer)($row[2]/$row[1])+$wsegundos),2,',','')."</td>";         //Tiempo promedio por solicitud, por servicio
				    echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&whora_i=".$whora_i."&whora_f=".$whora_f."&wser=".$row[0]."&word=S"."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'> Detallar</A></font></td>";
				    $wtotal=$wtotal+$row[1];   
				   }
				echo "<tr class=fila1>";
			    echo "<td colspan=1><font size=4>Total servicios: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,'','')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,',','')." %</td>"; 
				echo "<td colspan=2>&nbsp</td>";
				echo "</tr>";   
			   } 
			   
			echo "<tr></tr>";   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ** POR MOTIVO ** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT Motivo, COUNT(*) "
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_data  BETWEEN '".$whora_i."'  AND '".$whora_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY 1 "
		         ."   ORDER BY 2 desc ";
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				echo "<tr class='encabezadotabla'>";
				echo "<td colspan=5 align=center><b><font size=5>SOLICITUDES POR MOTIVO</font></b></td>";
				echo "</tr>";
				echo "<tr class='encabezadotabla'>";
			    echo "<th colspan=2>Motivo</th>";
			    echo "<th>Cantidad</th>";
			    echo "<th>% de participación</th>";
			    echo "<td>&nbsp</td>";
				echo "</tr>";
				$wtotal=0;
				for ($i=1;$i<=$num;$i++)
				   {
					if(is_integer($i/2))
						$wclass="class=fila1";
						else
						$wclass="class=fila2";
					$row = mysql_fetch_array($res); 
					echo "<tr ".$wclass.">";
					echo "<td colspan=2>".$row[0]."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,',','')."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,',','')." %</td>";
				    //echo "<td>&nbsp</td>";
				    echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&whora_i=".$whora_i."&whora_f=".$whora_f."&wmotivo=".$row[0]."&word=S"."&wser=''' TARGET='_blank'> Detallar</A></font></td>";
				    $wtotal=$wtotal+$row[1];   
				   }
				echo "<tr class='encabezadotabla'>";
			    echo "<td colspan=2><font size=4>Total servicios: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,',','')."</td>"; 
				echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,',','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";   
			   } 
		 }   	      	     
	   
   echo "</center></table>";
   
   echo "</form>";
   
}
include_once("free.php");
?>
