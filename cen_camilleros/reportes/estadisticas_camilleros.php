<head>
  <title>ESTADISTICAS DE CAMILLEROS</title>
  <script type="text/javascript">

function retornar(wemp_pmla,wfecha_i,wfecha_f,bandera,wcentral1)
	{
		location.href = "estadisticas_camilleros.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&bandera="+bandera+"&wcentral1="+wcentral1;
		
		
    }
	
function enviar()
	{	
	   
	  if (document.estadisticas_camilleros.wcentral.selectedIndex==" ")
	  {
       alert("Debe seleccionar una central.");
       document.estadisticas_camilleros.wcentral.focus(); 
      }
      else 	
		  document.estadisticas_camilleros.submit();
		  
	}	
</script>	
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
	
// ==========================================================================================================================================
// M O D I F I C A C I O N E S 	 
// ==========================================================================================================================================
// ==========================================================================================================================================
// ==========================================================================================================================================
// Agosto 21 de 2012 :   Camilo Zapata 
// ==========================================================================================================================================
// - Se cambiaron todos los querys que calculaban los tiempo ya que habian inconsistencias en las funciones, las cuales podrian presentar 
//	 errores cuando los datos pertenezcan a años diferentes. 
// - se le dio el formato (HH:mm:ss) a los datos presentados con el fin de que sean comprendidos por el usuario de manera mas sencilla
// ==========================================================================================================================================
// Junio 06 de 2012 :   Camilo Zapata 
// ==========================================================================================================================================
// - Se le dió el formato a los porcentajes para que aparezcan con "." para separar los decimales
// - En los cálculos de diferencia de tiempos entre la creación de la solicitud y tiempo de respuesta a la misma, para que tambien tenga en 
//	 cuenta las fechas y no solo las horas. 
// ==========================================================================================================================================
// Enero 02 de 2012 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// - Se calcula el total de servicios por central: en mas y menos de la meta
// - Se modifican todos los Querys para que el reporte sea dinamico (Multiempresa)
// - Se pinta un boton retornar al mostrar el reporte para que el usuario seleccione otros parametros
// ==========================================================================================================================================
// Enero 11 de 2012 :   Ing. Santiago Rivera Botero
// - Se insertan campos para que el usuario parametrice por rango en minutos y calcule el total de servicios 		 
// - Se adiciona la función CalculaServicioPorParametros el cual calcula los servicios por entre rango en minutos 		


function makeTimeFromSeconds( $total_seconds )
{
$horas              = floor ( $total_seconds / 3600 );
$minutes            = ( ( $total_seconds / 60 ) % 60 );
$seconds            = ( $total_seconds % 60 );
 
$time['horas']      = str_pad( $horas, 2, "0", STR_PAD_LEFT );
$time['minutes']    = str_pad( $minutes, 2, "0", STR_PAD_LEFT );
$time['seconds']    = str_pad( $seconds, 2, "0", STR_PAD_LEFT );
 
$time               = implode( ':', $time );
 
return $time;
}

// FUNCIÓN QUE CALCULA LOS SERVICIOS EN RANGO DE MINUTOS POR PARAMETROS
function CalculaServicioPorParametros($pain,$pafin,$wtotser){
	
	global $wcencam;
	global $wcentral;
	global $wfecha_i;
	global $wfecha_f;
	global $conex;
	
    if($pain<$pafin){	
	  
	 $q= "  SELECT count(*) "
			."    FROM ".$wcencam."_000003 A ,".$wcencam."_000006 B	 "
			."   WHERE A.Anulada = 'No' " 
			."	   AND TIMEDIFF(A.fecha_llegada, A.fecha_data) = 0"
			."     AND TIMEDIFF(A.hora_llegada,A.hora_data) BETWEEN '00:";
			
		 if($pain < 10)
			{
			 $q.= "0".$pain.":00' and ";
			} 
			else
				{
				 $q.= $pain.":00' and ";
				}
		 $q.= "'00:";		
		 if($pafin < 10)
			{
			 $q.= "0".$pafin.":00'";
			} 
			else
				{
				 $q.= $pafin.":00'";
				}		
				
	 $q.= " AND A.Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		 ." AND A.Hora_respuesta != '00:00:00' "
		 ." AND A.Hora_llegada   != '00:00:00' "
	     ." AND A.Central = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
		 ." AND A.Central = B.Codcen";
	
	
	}else
	    {
	     $q= "  SELECT count(*) "
			."    FROM ".$wcencam."_000003 A ,".$wcencam."_000006 B	 "
			."   WHERE A.Anulada = 'No' " 
			."	   AND TIMEDIFF(A.fecha_llegada, A.fecha_data) = 0"
			."     AND TIMEDIFF(A.hora_llegada,A.hora_data) BETWEEN '00:";
		 if($pain < 10)
			{
			 $q.= "0".$pafin.":00' and ";
			} 
			else
				{
				 $q.= $pafin.":00' and ";
				}
		 $q.= "'00:";		
		 if($pafin < 10)
			{
			 $q.= "0".$pain.":00'";
			} 
			else
				{
				 $q.= $pain.":00'";
				}		
				
	     $q.= " AND A.Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		     ." AND A.Hora_respuesta != '00:00:00' "
		     ." AND A.Hora_llegada   != '00:00:00' "
	         ." AND A.Central = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
		     ." AND A.Central = B.Codcen";
	
	
	
	    }
		
    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		 

	if ($num > 0)
		{
		 $row = mysql_fetch_array($res);
		 echo "<tr class=fila2>";
		 echo "<td colspan=2><font size=3>Total de servicios respondidos entre (".$pain.") y (".$pafin.")   minutos: </font></td>";
		 echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
		 echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
		 echo "<td>&nbsp</td>";
		 echo "</tr>";
		}
		  
	echo "<tr></tr>";		




}	

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	
  
    include_once("root/magenta.php");
    include_once("root/comun.php");
  
    $conex = obtenerConexionBD("matrix");
	
    

  	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
    $wactualiz="(2012-08-21)";                              // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  					    
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

	$q = " SELECT detapl, detval "
        ."   FROM root_000050, root_000051 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' "
        ."    AND empcod = detemp ";
    
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res); 
    
	if($num > 0 )
        {
	     for($i=1;$i<=$num;$i++)
	        {   
	         $row = mysql_fetch_array($res);
	      
	         if($row[0] == "cenmez")
	            $wcenmez=$row[1];
	         
	         if($row[0] == "afinidad")
	            $wafinidad=$row[1];
	         
	         if($row[0] == "movhos")
	            $wbasedato=$row[1];
			  
			 if(strtoupper($row[0]) == "HCE")
	            $whce=$row[1];
	         
	         if($row[0] == "tabcco")
	            $wtabcco=$row[1];
				
			 if($row[0] == "camilleros")
	            $wcencam=$row[1];	
            }  
        }
        else
		    { 
             echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";  
	        }		
    
	
    //====================================================================================================================================
    //COMIENZA LA FORMA      
    echo "<form action='estadisticas_camilleros.php' name='estadisticas_camilleros' method=post>";
    
    
	
	
    $hora = (string)date("H:i:s");
    
    encabezado("ESTADISTICAS CENTRALES DE SERVICIOS ",$wactualiz, "clinica");
    
    echo "<center><table border=2>";
    echo "<tr class=encabezadoTabla><td align=center colspan=5><b>Los tiempos son tomados desde el momento de la solicitud hasta la llegada al servicio</b></td></tr>";
    
      
    if (!isset($wfecha_i) or !isset($wfecha_f) or !isset($wcentral) or isset($bandera))
       {
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //FECHA INICIAL Y FINAL
		
		if(!isset($wfecha_i ) && !isset($wfecha_i ))
		{
			$wfecha_i = date("Y-m-d");
			$wfecha_f = date("Y-m-d");
		}
		
		echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
        echo "<tr>";
        echo "<td align=right  class=fila1><b>Fecha Inicial: </b></td>";
        echo "<td align=center class=fila2>";
        campofechaDefecto("wfecha_i",$wfecha_i);
        echo "</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td align=right  class=fila1><b>Fecha final: </b></td>";
        echo "<td align=center class=fila2>";
        campofechaDefecto("wfecha_f",$wfecha_f);
        echo "</td>";
        echo "</tr>";
	    
	    $q =  " SELECT codcen, nomcen"
			 ."   FROM ".$wcencam."_000006 ";
		$res = mysql_query($q,$conex);
	    $num = mysql_num_rows($res);
	    
	    echo "<tr class=encabezadoTabla><td align=center colspan=2>INGRESE LOS PARAMETROS POR RANGO EN MINUTOS A CONSULTAR (OPCIONALES): </td></tr>";
		echo "<tr class=fila1><td align=center><b>Parametro Inicial</b></td><td align=center><b>Parametro Final</b></td></tr>";
		echo "<tr class=fila1><td align=center> <input type='text' name='pain1' id='pain1' /></td><td align=center><input type='text' name='pafin1' id='pafin1' /></td></tr>";
		echo "<tr class=fila1><td align=center> <input type='text' name='pain2' id='pain2' /></td><td align=center><input type='text' name='pafin2' id='pafin2' /></td></tr>";
		echo "<tr class=fila1><td align=center> <input type='text' name='pain3' id='pain3' /></td><td align=center><input type='text' name='pafin3' id='pafin3' /></td></tr>";
		echo "<tr class=fila1><td align=center> <input type='text' name='pain4' id='pain4' /></td><td align=center><input type='text' name='pafin4' id='pafin4' /></td></tr>";
		
		echo "<tr class=encabezadoTabla><td align=center colspan=2>SELECCIONE LA CENTRAL A CONSULTAR: </td></tr>";
	    echo "<tr class=fila1>";
		echo "<td align=center colspan=2>";
	    echo "<select name='wcentral'>";
	    echo "<option value=' '>&nbsp</option>";    
		for ($i=1;$i<=$num;$i++)
		    {
			 $row = mysql_fetch_array($res); 
	         if(isset($wcentral1) && $row[0]==$wcentral1)
			 echo "<option selected= selected>".$row[0]." - ".$row[1]."</option>";
			  else
			     echo "<option>".$row[0]." - ".$row[1]."</option>";
            }
		echo "</select></td></tr>";
		
		echo "<tr>";
        echo "<td align=center bgcolor=#cccccc colspan=5><input type='button' value='Consultar' onclick= 'enviar()'></td>";   
        echo "</tr>";
       }
      else
         {
	        
			$wcent=explode("-",$wcentral);
			if(isset($wcentral))
	          $wcentral1 = $wcent[0];
	        	
	        echo "<tr class=fila1>";
	        echo "<td align=center colspan=5><font size=4><b>Central: </b>".$wcent[1]."</font></td>";  
	        echo "</tr>"; 
	        echo "<tr class=fila1>";
	        echo "<td align=center colspan=5><b>Período de Consulta: </b>".$wfecha_i."<b> al </b>".$wfecha_f."</td>";  
	        echo "</tr>";
	        
	        echo "<tr class=fila1>";
	        echo "<td align=center colspan=5>&nbsp</td>";  
	        echo "</tr>";
	         
	        //Traigo datos Generales de la CENTRAL
	        $q = " SELECT centre, cenvig, cenhpr, cenmet "
	            ."   FROM ".$wcencam."_000006 "
	            ."  WHERE codcen = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)";
	        $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
			
			if ($num > 0)
			   {
				$row = mysql_fetch_array($res);
				
				$wtiempo_referesh      = $row[0];   
				$wvigencia_solicitud   = $row[1];   //En horas
				$whoras_dia_promedio   = $row[2];   //En horas
				$wmeta_tiempo_promedio = $row[3];   //En minutos
			   }	   
	        
			   
			//===================================================================================================================================================
		    // QUERY: CANTIDAD DE SERVICIOS EN ** EL PERIODO **
		    //===================================================================================================================================================
		    $q=   "  SELECT count(*) "
		         ."    FROM ".$wcencam."_000003 "
		         ."   WHERE Anulada         = 'No' " 
		         ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND Hora_respuesta != '00:00:00' "
		         ."     AND Hora_llegada   != '00:00:00' "
		         ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)";
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
		    if ($num > 0)
			   {
				$row = mysql_fetch_array($res); 
				$wtotser=$row[0];
				echo "<tr class=fila2>";
				echo "<td colspan=2><font size=3>Total servicios en el período (No incluye los Anulados, ni los No atendidos): "."</font></td>";
				echo "<td colspan=2 align=center>".number_format($row[0],0,'.',',')."</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			   } 
		    
			if($wtotser != 0)
			   {
			
				//===================================================================================================================================================
				// QUERY: PROMEDIO DE LLEGADA A LOS SERVICIOS 
				//===================================================================================================================================================
				$q =  "SELECT SUM((UNIX_TIMESTAMP(CONCAT(fecha_llegada, ' ',hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(fecha_data,' ', hora_data))))"
					 ."   FROM ".$wcencam."_000003 "
					 ."  WHERE Anulada         = 'No' " 
					 ."    AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."    AND Hora_respuesta != '00:00:00' "
					 ."    AND Hora_llegada   != '00:00:00' "
					 ."    AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)";
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				 if($num > 0)
					{
					 $row = mysql_fetch_array($res);
					 $wprom = $row[0];
					 $wproserseg=($wprom/$wtotser);
					$wproser= makeTimeFromSeconds($wproserseg);					
					 echo "<tr class=fila2>";
					 echo "<td colspan=2><font size=3>Promedio de llegada al servicio que solicito (en minutos): </font></td>";
					 echo "<td colspan=2 align=center><b>".$wproser."</b></td>";
					 echo "<td>&nbsp</td>";
					 echo "</tr>";
			  
					}         
					
					
				
				//===================================================================================================================================================
				// TIEMPO PROMEDIO ESPERADO
				//===================================================================================================================================================   
				echo "<tr class=fila2>";
				echo "<td colspan=2><font size=3>Meta del tiempo promedio por servicio (en minutos): </font></td>";
				echo "<td colspan=2 align=center><b>".$wmeta_tiempo_promedio."</b></td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";   
				   
				//===================================================================================================================================================
				// QUERY: SERVICIO QUE MAS TARDO 
				//===================================================================================================================================================
					$q= "SELECT Id, MAX((UNIX_TIMESTAMP(CONCAT(fecha_llegada, ' ',hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(fecha_data,' ', hora_data))))" 
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada = 'No' " 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Hora_llegada   != '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
					 ."   GROUP BY id "
					 ."   ORDER BY 2 desc ";
					 
					 
				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
				
				if ($num > 0)
				   {
					$row = mysql_fetch_array($res);
					
					echo "<tr class=fila2>";
					echo "<td colspan=2><font size=3>Solicitud que tardo MAS en ser atendida (en minutos): </font></td>";
					//$wsegundos=60*(($row[1]-(integer)$row[1])/100);
					$wsegundos=makeTimeFromSeconds($row[1]);
					echo "<td colspan=2 align=center>".$wsegundos."</td>";
					echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&wid=".$row[0]."&wcentral=".$wcentral."' TARGET='_blank'> Detallar</A></font></td>";
					echo "</tr>";
				   }   
				   
				
				//===================================================================================================================================================
				// QUERY: SERVICIO QUE MENOS TARDO 
				//===================================================================================================================================================
				   $q= "SELECT Id, MIN((UNIX_TIMESTAMP(CONCAT(fecha_llegada, ' ',hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(fecha_data,' ', hora_data))))" 
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada         = 'No' " 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Hora_llegada   != '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
					 ."   GROUP BY id "
					 ."   ORDER BY 2 ";
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				
				
				
				if ($num > 0)
				   {
					$row = mysql_fetch_array($res);
					
					echo "<tr class=fila2>";
					echo "<td colspan=2><font size=3>Solicitud que tardo MENOS en ser atendida (en minutos): </font></td>";
					//$wsegundos=60*(($row[1]-(integer)$row[1])/100);
					$wsegundos=makeTimeFromSeconds($row[1]);
					echo "<td colspan=2 align=center>".$wsegundos."</td>";
					echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&wid=".$row[0]."&wcentral=".$wcentral."' TARGET='_blank'> Detallar</A></font></td>";
					echo "</tr>";
				   }   
					  
				//===================================================================================================================================================
				// QUERY: CANTIDAD DE SERVICIOS ASIGNADOS PERO NO ATENDIDOS ** EN EL PERIODO **
				//===================================================================================================================================================
				$q=   "  SELECT count(*) "
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada         = 'No' " 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Hora_llegada    = '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)";
				
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				
				if ($num > 0)
				   {
					 
						
					 $row = mysql_fetch_array($res); 
					 echo "<tr class=fila2>";
					 echo "<td colspan=2><font size=3>Total servicios asignados pero no atendidos (sin hora de llegada): "."</font></td>";
					 echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
					 echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.',',')." %</td>";
					 echo "<td>&nbsp</td>";
					 echo "</tr>";
							
								
				   }       
					  
				   
				//===================================================================================================================================================
				// QUERY: CANTIDAD DE SERVICIOS EN ** MENOS O IGUAL AL PROMEDIO **
				//===================================================================================================================================================
				$q=   "  SELECT count(*) "
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada         = 'No' " 
					 ."     AND (((UNIX_TIMESTAMP(CONCAT(fecha_llegada, ' ',hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(fecha_data,' ', hora_data)))))<=(SECOND('".$wproser."') + minute('".$wproser."')*60+hour('".$wproser."')*60*24)" 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Hora_llegada   != '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)";
					 
					 
				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
				
				if ($num > 0)
				   {
							   
					 $row = mysql_fetch_array($res); 
					 echo "<tr class=fila2>";
					 echo "<td colspan=2><font size=3>Total servicios respondidos en menos de (".$wproser.") minutos: </font></td>";
					 echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'',',')."</td>";
					 echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.',',')." %</td>";
					 echo "<td>&nbsp</td>";
					 echo "</tr>";
						 
				   } 

				//===================================================================================================================================================
				// QUERY: CANTIDAD DE SERVICIOS EN ** MAS DEL PROMEDIO **
				//===================================================================================================================================================
				$q=   "  SELECT count(*) "
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada = 'No' " 
					 ."     AND (((UNIX_TIMESTAMP(CONCAT(fecha_llegada, ' ',hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(fecha_data,' ', hora_data)))))>(SECOND('".$wproser."') + minute('".$wproser."')*60+hour('".$wproser."')*60*24)" 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Hora_llegada   != '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)";
				$res = mysql_query($q,$conex)  or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());				
				
				if ($num > 0)
				   {
					 
					 $row = mysql_fetch_array($res); 
					 echo "<tr class=fila2>";
					 echo "<td colspan=2><font size=3>Total servicios respondidos en mas de (".$wproser.") minutos: </font></td>";
					 echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
					 echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
					 echo "<td>&nbsp</td>";
					 echo "</tr>";
						 
				   } 	 
				   
				//===================================================================================================================================================
				// QUERY: CANTIDAD DE SERVICIOS ** ANULADOS ** EN EL PERIODO
				//===================================================================================================================================================
				$q=   "  SELECT count(*) "
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada         = 'Si' " 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)";
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				
				if ($num > 0)
				   {
					 
					 $row = mysql_fetch_array($res); 
					 echo "<tr class=fila2>";
					 echo "<td colspan=2><font size=3>Total servicios Anulados en el período: </font></td>";
					 echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
					 echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
					 echo "<td>&nbsp</td>";
					 echo "</tr>";
						
				   } 	
				
				//===================================================================================================================================================
				// QUERY: CANTIDAD DE SERVICIOS ** NO ATENDIDOS ** EN EL PERIODO
				//===================================================================================================================================================
				$q=   "  SELECT count(*) "
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada        = 'No' " 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta = '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)";
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				
				if ($num > 0)
				   {
						
					 $row = mysql_fetch_array($res); 
					 echo "<tr class=fila2>";
					 echo "<td colspan=2><font size=3>Total servicios No atendidos en el período (sin camillero): </font></td>";
					 echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
					 echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
					 echo "<td>&nbsp</td>";
					 echo "</tr>";
				   }    
					  
				echo "<tr></tr>";
				//===================================================================================================================================================
				// QUERY: TOTAL DE SERVICIOS RESPONDIDOS  EN MAS DE LA META 
				//===================================================================================================================================================	
				$q=   "  SELECT count(*) "
					 ."    FROM ".$wcencam."_000003 A ,".$wcencam."_000006 B	 "
					 ."   WHERE A.Anulada = 'No' " 
					 ."     AND ((UNIX_TIMESTAMP(CONCAT(A.fecha_llegada, ' ',A.hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(A.fecha_data,' ', A.hora_data))))>(SECOND(B.cenmet) + minute(B.cenmet)*60+hour(B.cenmet)*60*24)"
					 ."     AND A.Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND A.Hora_respuesta != '00:00:00' "
					 ."     AND A.Hora_llegada   != '00:00:00' "
					 ."     AND A.Central = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
					 ."    	AND A.Central = B.Codcen";
		
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				
				if ($num > 0)
				   {
					$row = mysql_fetch_array($res);
					echo "<tr class=fila2>";
					echo "<td colspan=2><font size=3>Total de servicios respondidos en mas de la meta (".$wmeta_tiempo_promedio.") minutos: </font></td>";
					echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
					echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
					echo "<td>&nbsp</td>";
					echo "</tr>";
				   }
					  
				echo "<tr></tr>";			
					 
				//===================================================================================================================================================
				// QUERY: TOTAL DE SERVICIOS RESPONDIDOS  EN MENOS DE LA META 
				//===================================================================================================================================================
				$q=   "  SELECT count(*) "
					 ."    FROM ".$wcencam."_000003 A ,".$wcencam."_000006 B	 "
					 ."   WHERE A.Anulada = 'No' " 
					 ."     AND (((UNIX_TIMESTAMP(CONCAT(fecha_llegada, ' ',A.hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(A.fecha_data,' ', A.hora_data))))) <= (SECOND(B.cenmet) + minute(B.cenmet)*60+hour(B.cenmet)*60*24) "
					 ."     AND A.Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND A.Hora_respuesta != '00:00:00' "
					 ."     AND A.Hora_llegada   != '00:00:00' "
					 ."     AND A.Central = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
					 ."    	AND A.Central = B.Codcen";
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				
				
				
				if ($num > 0)
				   {
					$row = mysql_fetch_array($res);
					echo "<tr class=fila2>";
					echo "<td colspan=2><font size=3>Total de servicios respondidos en menos de la meta (".$wmeta_tiempo_promedio.") minutos: </font></td>";
					echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
					echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
					echo "<td>&nbsp</td>";
					echo "</tr>";
				   }
				
				
				echo "<tr></tr>";

                //===================================================================================================================================================
				// QUERY: TOTAL DE SERVICIOS RESPONDIDOS POR PARAMETROS EN RANGO DE MINUTOS
				//===================================================================================================================================================	
				if(!empty($pain1) && !empty($pafin1))
				    {
					 CalculaServicioPorParametros($pain1,$pafin1,$wtotser);
				     	
				    }
					
				if(!empty($pain2) && !empty($pafin2))
				    {
					 CalculaServicioPorParametros($pain2,$pafin2,$wtotser);
				     	
				    }
				if(!empty($pain3) && !empty($pafin3))
				    {
					 CalculaServicioPorParametros($pain3,$pafin3,$wtotser);
				     	
				    }
				if(!empty($pain4) && !empty($pafin4))
				    {
					 CalculaServicioPorParametros($pain4,$pafin4,$wtotser);
				     	
				    }		
					 
				//===================================================================================================================================================
				// QUERY: CANTIDAD DE SERVICIOS ** POR CAMILLERO ** EN EL PERIODO
				//===================================================================================================================================================
				
				  $q= "SELECT Camillero, COUNT(*),  SUM((UNIX_TIMESTAMP(CONCAT(fecha_llegada, ' ',hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(fecha_data,' ', hora_data))))"
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada         = 'No' " 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Hora_llegada   != '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
					 ."   GROUP BY 1 "
					 ."   ORDER BY 2 desc ";
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
				
				
				
				if ($num > 0)
				   {
					 echo "<tr class=encabezadoTabla>";
					 echo "<td colspan=5 align=center><b><font size=4>Servicios por camillero: </font></b></td>";
					 echo "</tr>";
					 echo "<tr class=encabezadoTabla>";
					 echo "<th>Empleado</th>";
					 echo "<th>Cantidad</th>";
					 echo "<th>% de participación</th>";
					 echo "<th>Tiempo Promedio<br>(HH:mm:ss)</th>";
					 echo "<th>&nbsp</th>";
					 echo "</tr>"; 
					 $wtotal=0;
					
					 for ($i=1;$i<=$num;$i++)
					   {
						 if (is_integer($i/2))
							$wcolor = "fila1";
						   else  
							 $wcolor = "fila2";
					   
						 $row = mysql_fetch_array($res); 
						 echo "<tr class=".$wcolor.">";
						 echo "<td colspan=1>".$row[0]."</td>";
						 echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'.',',')."</td>";
						 echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,'.','')." %</td>";
						 $minutos=makeTimeFromSeconds($row[2]/$row[1]);
						 echo "<td colspan=1 align=center>".$minutos."</td>";
						 echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&wcamillero=".$row[0]."&wcentral=".$wcentral."' TARGET='_blank'> Detallar</A></font></td>"; 
						 $wtotal=$wtotal+$row[1];	   
						}
					 echo "<tr class=encabezadoTabla>";
					 echo "<td><font size=3>Total servicios: </font></td>";
					 echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,'.',',')."</td>"; 
					 echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,'.',',')." %</td>";
					 echo "<td colspan=2 align=RIGHT>&nbsp</td>";
					 echo "</tr>";
						 
				   }
				
				echo "<tr></tr>";   
				//===================================================================================================================================================
				// QUERY: CANTIDAD DE SOLICITUDES ** POR SERVICIO ** EN EL PERIODO
				//===================================================================================================================================================
				  $q= "SELECT Origen, COUNT(*),  SUM((UNIX_TIMESTAMP(CONCAT(fecha_llegada, ' ',hora_llegada)))-(UNIX_TIMESTAMP(CONCAT(fecha_data,' ', hora_data))))"
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada         = 'No' " 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Hora_llegada   != '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
					 ."   GROUP BY 1 "
					 ."   ORDER BY 2 desc ";
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				
				
				if ($num > 0)
				   {
					echo "<tr class=encabezadoTabla>";
					echo "<td colspan=5 align=center><b><font size=4>Solicitudes por Servicio o Unidad: </font></b></td>";
					echo "</tr>";
					echo "<tr class=encabezadoTabla>";
					echo "<th>Servicio</th>";
					echo "<th>Cantidad</th>";
					echo "<th>% de participación</th>";
					echo "<th>Tiempo Promedio<br>(HH:mm:ss)</th>";
					echo "<th>&nbsp</th>";
					echo "</tr>";
					$wtotal=0;
					for ($i=1;$i<=$num;$i++)
					   {
						if (is_integer($i/2))
						   $wcolor = "fila1";
						  else  
							$wcolor = "fila2";   
						   
						$row = mysql_fetch_array($res); 
						echo "<tr class=".$wcolor.">";
						echo "<td colspan=1>".$row[0]."</td>";
						echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'.',',')."</td>";
						echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,'.','')." %</td>";
						$minutos=makeTimeFromSeconds($row[2]/$row[1]);
						echo "<td colspan=1 align=center>".$minutos."</td>";         //Tiempo promedio por solicitud, por servicio
						echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&wser=".$row[0]."&word=S"."&wcentral=".$wcentral."' TARGET='_blank'> Detallar</A></font></td>";
						$wtotal=$wtotal+$row[1];   
					   }
					echo "<tr class=encabezadoTabla>";
					echo "<td colspan=1><font size=3>Total servicios: </font></td>";
					echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,'',',')."</td>";
					echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,'.','')." %</td>"; 
					echo "<td colspan=2>&nbsp</td>";
					echo "</tr>";   
				   } 
				   
				echo "<tr></tr>";   
				//===================================================================================================================================================
				// QUERY: CANTIDAD DE SERVICIOS ** POR MOTIVO ** EN EL PERIODO
				//===================================================================================================================================================
				$q=   "  SELECT Motivo, COUNT(*) "
					 ."    FROM ".$wcencam."_000003 "
					 ."   WHERE Anulada         = 'No' " 
					 ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND Hora_respuesta != '00:00:00' "
					 ."     AND Hora_llegada   != '00:00:00' "
					 ."     AND Central         = mid('".$wcentral."',1,instr('".$wcentral."','-')-1)"
					 ."   GROUP BY 1 "
					 ."   ORDER BY 2 desc ";
				$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
				
				if ($num > 0)
				   {
					echo "<tr class=encabezadoTabla>";
					echo "<td colspan=5 align=center><b><font size=4>SOLICITUDES POR MOTIVO</font></b></td>";
					echo "</tr>";
					echo "<tr class=encabezadoTabla>";
					echo "<th colspan=2>Motivo</th>";
					echo "<th>Cantidad</th>";
					echo "<th>% de participación</th>";
					echo "<td>&nbsp</td>";
					echo "</tr>";
					$wtotal=0;
					for ($i=1;$i<=$num;$i++)
					   {
						if (is_integer($i/2))
						   $wcolor = "fila1";
						  else  
							$wcolor = "fila2";   
						   
						$row = mysql_fetch_array($res); 
						echo "<tr class=".$wcolor.">";
						echo "<td colspan=2>".$row[0]."</td>";
						echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'.',',')."</td>";
						echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,'.','')." %</td>";
						echo "<td align=center><font size=3><A href='consulta_de_servicios.php?wemp_pmla=".$wemp_pmla."&wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&wmotivo=".$row[0]."&word=S"."&wcentral=".$wcentral."&wser='' ' TARGET='_blank'> Detallar</A></font></td>";
						$wtotal=$wtotal+$row[1];   
					   }
					echo "<tr class=encabezadoTabla>";
					echo "<td colspan=2><font size=4>Total servicios: </font></td>";
					echo "<td colspan=1 align=RIGHT>".number_format($wtotal,0,'.',',')."</td>"; 
					echo "<td colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,'.','')." %</td>";
					echo "<td>&nbsp</td>";
					echo "</tr>";
							
				   }
              }
			  else{
                    echo "<td colspan=2><font size=3>No se encontraron servicios</font></td>";
				  }				
		    $bandera = "1";
		    echo "</center></table>";
            echo "<br/>";
            echo "<center>";
            echo "<input type='button' name='btn_retornar2' value='retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\",\"".$wcentral1."\")'/>";
            echo "</center>";
            echo "</form>";
		 
		 }   	      	     
   	   
   
   
}
include_once("free.php");
?>