<head>
  <title>ESTADISTICAS AGENDA DIRECTIVOS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");

//echo "<meta http-equiv='refresh' content='10;url=central_de_AGENDA.php?'>";

  /***************************************************
	*	        ESTADISTICAS DE AGENDA           *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

	

	

						
    

    
	//$conexunix = odbc_pconnect('facturacion','facadm','1201')
  	//				    or die("No se ralizo Conexion con el Unix");
  					    
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
    
          
    //====================================================================================================================================
    //COMIENZA LA FORMA      
    echo "<form action='estadisticas_agenda.php' method=post>";
    
    $wfecha=date("Y-m-d"); 
    $hora = (string)date("H:i:s");
    
    //=========================================================================
    //Aca viene la Dirección, Gerencia, Unidad o Servicio que utiliza la agenda
    echo "<input type='HIDDEN' name='wgerencia' value='".$wgerencia."'>";
    //=========================================================================
    
    
    echo "<center><table border=2>";
    echo "<tr><td align=center bgcolor=#fffffff colspan=5><font size=5 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
    echo "<tr><td align=center bgcolor=#fffffff colspan=5><font size=4 text color=#CC0000><b>ESTADISTICAS AGENDA</b></font></td></tr>";
    echo "<tr><td align=center colspan=13><font size=4 text color=#fffffff><b>".$wgerencia."</b></font></td></tr>";
    //echo "<tr><td align=center bgcolor=#fffffff colspan=5><font size=4 text color=#CC0000><b>Los tiempos son tomados de el momento de solicitud hasta la llegada al servicio</b></font></td></tr>";
    
    $wcolor="dddddd";
    $wcolorfor="666666";
    
    if (!isset($wfecha_i) or !isset($wfecha_f))
       {
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //FECHA INICIAL Y FINAL
        echo "<tr>";
        echo "<td align=left bgcolor=".$wcolor."><b>Fecha Inicial: </b><INPUT TYPE='text' NAME='wfecha_i' VALUE=".$wfecha."></td>";  
        echo "<td align=left bgcolor=".$wcolor."><b>Fecha Final: </b><INPUT TYPE='text' NAME='wfecha_f' VALUE=".$wfecha."></td>";  
	    echo "</tr>";
	    
	    echo "<tr>";
        echo "<td align=center bgcolor=#cccccc colspan=5><input type='submit' value='Consultar'></td>";   
        echo "</tr>";
       }
      else
         {
	        echo "<tr>";
	        echo "<td align=center bgcolor=fffffff colspan=5><b>Período de Consulta: </b>".$wfecha_i."<b> al </b>".$wfecha_f."</td>";  
	        echo "</tr>";
	        
	        echo "<tr>";
	        echo "<td align=center bgcolor=fffffff colspan=5>&nbsp</td>";  
	        echo "</tr>";
	         
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS EN ** EL PERIODO **
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Origen = '".$wgerencia."'";
		         //."     AND Hora_respuesta != '00:00:00' "
		         //."     AND Hora_llegada != '00:00:00' ";
		    
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				$wtotser=$row[0];
				echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Total actividades en el período (No incluye los Anulados): "."</font></td>";
				echo "<td colspan=2 align=center>".number_format($row[0],0,'',',')."</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   } 
		    
			/*   
			//===================================================================================================================================================
		    // QUERY: PROMEDIO DE LLEGADA A LOS SERVICIOS 
		    //===================================================================================================================================================
		    $q=   "  SELECT SUM(HOUR(TIMEDIFF(hora_llegada,hora_data))),"
		         ."         SUM(MINUTE(TIMEDIFF(hora_llegada,hora_data))),"
		         ."         SUM(SECOND(TIMEDIFF(hora_llegada,hora_data))) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
			
			if ($num > 0)
			   {
			    $row = mysql_fetch_array($res);
			    $wprom = $row[0]*60;
			    $wprom = $wprom+($row[1]);
			    $wprom = $wprom+($row[2]/60);
			    
			    if ($wtotser==0) $wtotser=1;
			    
			    //$wprom ya esta en minutos, ahora lo divido por el numero de servicios
			    $wproserseg=($wprom/$wtotser)-(integer)($wprom/$wtotser);
			    $wproserseg=60*($wproserseg/100);
			    $wproser=number_format((((integer)($wprom/$wtotser))+$wproserseg),2,'','');
			    
			    echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Promedio de llegada al servicio que solicito (en minutos): </font></td>";
				echo "<td colspan=2 align=center>".$wproser."</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
		       }
			
			
		    //===================================================================================================================================================
		    // QUERY: SERVICIO QUE MAS TARDO 
		    //===================================================================================================================================================
		    $q=   "  SELECT Id, MAX((HOUR(TIMEDIFF(hora_llegada,hora_data))*60)   +"
		         ."                  MINUTE(TIMEDIFF(hora_llegada,hora_data))     +"
		         ."                (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY id "
		         ."   ORDER BY 2 desc ";
		    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
			
			if ($num > 0)
			   {
			    $row = mysql_fetch_array($res);
			    
			    echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Solicitud que tardo MAS en ser atendida (en minutos): </font></td>";
				$wsegundos=60*(($row[1]-(integer)$row[1])/100);
				echo "<td colspan=2 align=center>".number_format(((integer)$row[1]+$wsegundos),2,'','')."</td>";
				echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wfecha_i=".$wfecha_i."&amp;wfecha_f=".$wfecha_f."&amp;wid=".$row[0]."' TARGET='_blank'> Detallar</A></font></td>";
				echo "</tr>";
		       }   
		       
		    
		    //===================================================================================================================================================
		    // QUERY: SERVICIO QUE MENOS TARDO 
		    //===================================================================================================================================================
		    $q=   "  SELECT id, MIN((HOUR(TIMEDIFF(hora_llegada,hora_data))*60)  +"
		         ."                  MINUTE(TIMEDIFF(hora_llegada,hora_data))    +"
		         ."                 (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY id "
		         ."   ORDER BY 2 ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
			
			if ($num > 0)
			   {
			    $row = mysql_fetch_array($res);
			    
			    echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Solicitud que tardo MENOS en ser atendida (en minutos): </font></td>";
				$wsegundos=60*(($row[1]-(integer)$row[1])/100);
				echo "<td colspan=2 align=center>".number_format(((integer)$row[1]+$wsegundos),2,'','')."</td>";
				echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wfecha_i=".$wfecha_i."&amp;wfecha_f=".$wfecha_f."&amp;wid=".$row[0]."' TARGET='_blank'> Detallar</A></font></td>";
				echo "</tr>";
		       }   
		          
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ASIGNADOS PERO NO ATENDIDOS ** EN EL PERIODO **
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada = '00:00:00' ";
		    
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Total servicios asignados pero no atendidos (sin hora de llegada): "."</font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   }       
		          
			   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS EN ** MENOS O IGUAL AL PROMEDIO **
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND ((HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
		         ."          (MINUTE(TIMEDIFF(hora_llegada,hora_data)))  + "
		         ."          (SECOND(TIMEDIFF(hora_llegada,hora_data))/60)) <= ".$wproser
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' ";
		         
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Total servicios respondidos en menos de (".$wproser.") minutos: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   } 

		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS EN ** MAS DEL PROMEDIO **
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND ((HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
		         ."          (MINUTE(TIMEDIFF(hora_llegada,hora_data)))  + "
		         ."          (SECOND(TIMEDIFF(hora_llegada,hora_data))/60)) > ".$wproser
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' ";
		    $res = mysql_query($q,$conex)  or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Total servicios respondidos en mas de (".$wproser.") minutos: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   } 	 
			*/
			   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ** ANULADOS ** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'Si' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Origen = '".$wgerencia."'";
		    //     ."     AND Hora_respuesta != '00:00:00' ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Total actividades Anuladas en el período: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   } 	
			
			/*   
			//===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ** NO ATENDIDOS ** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta = '00:00:00' ";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=2><font size=4>Total servicios No atendidos en el período (sin camillero): </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   }
			      
			echo "<tr></tr>";  
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS ** POR CAMILLERO ** EN EL PERIODO
		    //===================================================================================================================================================
		    
		    $q=   "  SELECT Camillero, COUNT(*), SUM((HOUR(TIMEDIFF(hora_llegada,hora_data))*60)     + "
		         ."                                  (MINUTE(TIMEDIFF(hora_llegada,hora_data)))      + "
		         ."                                  ((SECOND(TIMEDIFF(hora_llegada,hora_data)))/60))  "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY 1 "
		         ."   ORDER BY 2 desc ";
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
		    
			if ($num > 0)
			   {
				echo "<tr bgcolor=ffcc66>";
				echo "<td colspan=5 align=center><b><font size=5>Servicios por camillero: </font></b></td>";
				echo "</tr>";
				echo "<tr>";
			    echo "<th bgcolor=#ffcc66>Empleado</th>";
			    echo "<th bgcolor=#ffcc66>Cantidad</th>";
			    echo "<th bgcolor=#ffcc66>% de participación</th>";
				echo "<th bgcolor=#ffcc66>Tiempo Promedio</th>";
				echo "<th bgcolor=#ffcc66>&nbsp</th>";
				echo "</tr>"; 
				$wtotal=0;  
				for ($i=1;$i<=$num;$i++)
				   {
				    $row = mysql_fetch_array($res); 
					echo "<tr bgcolor=".$wcolor.">";
					echo "<td colspan=1>".$row[0]."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'',',')."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,'','')." %</td>";
				    $wsegundos=60*((($row[2]/$row[1])-(integer)($row[2]/$row[1]))/100);
				    echo "<td colspan=1 align=RIGHT>".number_format(((integer)($row[2]/$row[1])+$wsegundos),2,'','')."</td>";
				    echo "<td>&nbsp</td>";
				    $wtotal=$wtotal+$row[1];	   
			       }
			    echo "<tr bgcolor=ffcc66>";
			    echo "<td><font size=4>Total servicios: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,'',',')."</td>"; 
				echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,'','')." %</td>";
				echo "<td colspan=2 align=RIGHT>&nbsp</td>"; 
				echo "</tr>";  
			   }
			
			
			echo "<tr></tr>";   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE SOLICITUDES ** POR SERVICIO ** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT Origen, COUNT(*), SUM((HOUR(TIMEDIFF(hora_llegada,hora_data))*60) + "
		         ."                               (MINUTE(TIMEDIFF(hora_llegada,hora_data)))  + "
		         ."                               (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY 1 "
		         ."   ORDER BY 2 desc ";
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				echo "<tr bgcolor=ffcc66>";
				echo "<td colspan=5 align=center><b><font size=5>Solicitudes por Servicio o Unidad: </font></b></td>";
				echo "</tr>";
				echo "<tr>";
			    echo "<th bgcolor=#ffcc66>Servicio</th>";
			    echo "<th bgcolor=#ffcc66>Cantidad</th>";
			    echo "<th bgcolor=#ffcc66>% de participación</th>";
			    echo "<th bgcolor=#ffcc66>Tiempo Promedio</th>";
			    echo "<th bgcolor=#ffcc66>&nbsp</th>";
				echo "</tr>";
				$wtotal=0;
				for ($i=1;$i<=$num;$i++)
				   {
					$row = mysql_fetch_array($res); 
					echo "<tr bgcolor=".$wcolor.">";
					echo "<td colspan=1>".$row[0]."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'',',')."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,'','')." %</td>";
				    $wsegundos=60*((($row[2]/$row[1])-(integer)($row[2]/$row[1]))/100);
				    echo "<td colspan=1 align=RIGHT>".number_format(((integer)($row[2]/$row[1])+$wsegundos),2,'','')."</td>";         //Tiempo promedio por solicitud, por servicio
				    echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wfecha_i=".$wfecha_i."&amp;wfecha_f=".$wfecha_f."&amp;wser=".$row[0]."&amp;word=S"."' TARGET='_blank'> Detallar</A></font></td>";
				    $wtotal=$wtotal+$row[1];   
				   }
				echo "<tr bgcolor=ffcc66>";
			    echo "<td colspan=1><font size=4>Total servicios: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,'',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,'','')." %</td>"; 
				echo "<td colspan=2>&nbsp</td>";
				echo "</tr>";   
			   } 
			*/
			   
			echo "<tr></tr>";   
		    //===================================================================================================================================================
		    // QUERY: CANTIDAD DE ACTIVIDADES ** POR ACTIVIDAD ** EN EL PERIODO
		    //===================================================================================================================================================
		    $q=   "  SELECT Motivo, COUNT(*) "
		         ."    FROM agenda_000003 "
		         ."   WHERE Anulada = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Origen  = '".$wgerencia."'"
		         //."     AND Hora_respuesta != '00:00:00' "
		         //."     AND Hora_llegada != '00:00:00' "
		         ."   GROUP BY 1 "
		         ."   ORDER BY 2 desc ";
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				echo "<tr bgcolor=ffcc66>";
				echo "<td colspan=5 align=center><b><font size=5>AGENDA POR ACTIVIDADES</font></b></td>";
				echo "</tr>";
				echo "<tr>";
			    echo "<th bgcolor=#ffcc66 colspan=2>Actividad</th>";
			    echo "<th bgcolor=#ffcc66>Cantidad</th>";
			    echo "<th bgcolor=#ffcc66>% de participación</th>";
			    echo "<td bgcolor=#ffcc66>&nbsp</td>";
				echo "</tr>";
				$wtotal=0;
				for ($i=1;$i<=$num;$i++)
				   {
					$row = mysql_fetch_array($res); 
					echo "<tr bgcolor=".$wcolor.">";
					echo "<td colspan=2>".$row[0]."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'',',')."</td>";
				    echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,'','')." %</td>";
				    //echo "<td>&nbsp</td>";
				    echo "<td align=center><font size=3><A href='consulta_agenda.php?wfecha_i=".$wfecha_i."&amp;wfecha_f=".$wfecha_f."&amp;wmotivo=".$row[0]."&amp;word=S"."&amp;wgerencia=".$wgerencia."' TARGET='_blank'> Detallar</A></font></td>";
				    $wtotal=$wtotal+$row[1];   
				   }
				echo "<tr bgcolor=ffcc66>";
			    echo "<td colspan=2><font size=4>Total servicios: </font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,'',',')."</td>"; 
				echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,'','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";   
			   } 
		 }   	      	     
	   
   echo "</center></table>";
   
   //=========================================================================
   //Aca viene la Dirección, Gerencia, Unidad o Servicio que utiliza la agenda
   echo "<input type='HIDDEN' name='wgerencia' value='".$wgerencia."'>";
   //=========================================================================
    
   echo "</form>";
   
}
include_once("free.php");
?>
