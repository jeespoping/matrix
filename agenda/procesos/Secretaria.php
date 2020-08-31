<head>
  <title>AGENDA DIRECTIVOS</title>
</head>
<BODY>
<script type="text/javascript">
	function enter()
	{
	 document.forms.secretaria.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     } 

	
</script>
<?php
include_once("conex.php");
  /***************************************************
	*	      SOLICITUD SERVICIO DE CAMILLERO        *
	*	           DESDE CUALQUIER UNIDAD            *
	*				CONEX, FREE => OK				 *
	**************************************************/
	
//==================================================================================================================================
//PROGRAMA                   : solicitud_camillero.php
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Marzo 17 de 2005
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Febrero 3 de 2010)";                      
//DESCRIPCION
//==================================================================================================================================
//Este un programa estilo demonio, porque se refresca automaticamente la pantalla con la instrucción <meta>, el objetivo de este es
//registrar todas las solicitudes de camillero que se hagan a la central de camilleros, para esto se debe digitar el motivo del
//servicio, una observación (opcional), la habitación (opcional) y el destino del servicio. Una vez registrado el servicio se despliega
//las solicitudes hechas y que no han sido atendidas, es decir, en las que el camillero no ha llegado, ademas muestra, cuando y que
//camillero se le asigno al servicio. Una vez sea atendido el servicio y este evento sea registrado en la central, la solitud sale }
//del listado de servicios solicitados por el piso o unidad.
//El refresh se ejecuta cada 30 segundos, y si pasadas dos horas, no ha sido atendido el servicio este sale de la lista de pendientes y
//queda como un servicio no atendido.
//En la lista de pendientes solo aparecen los servicios de dia actual.
//==================================================================================================================================


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//M A Y O  1 4  DE 2007:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica la agenda para que se pueda tener mas deuna agenda, esto se pueda logar al definir una tabla de tipos de agenda y a su vez  \\
//las actividades se deben de poder clasificar en cada uno de los tipos de agenda (agendas). Adicionalmente se puede definir`para cada    \\
//tipo de agenda un adicional a capturar, el cual se guardara en la tabla 000003 en el campo dato.                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

    

    include_once("root/comun.php");
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
          
    $wano=date("Y");      
    $wfecha=date("Y-m-d");   
	$hora = (string)date("H:i:s");
	
	//=========================================================================
	//Aca viene la Dirección, Gerencia, Unidad o Servicio que utiliza la agenda
	echo "<input type='HIDDEN' name=wgerencia value='".$wgerencia."'>";
	//=========================================================================
	
	
	$wordeno="off";
    $worden="";
	
    //ESTO PARA PODER HACER EL ORDEN DE LA INFORMACION EN EL QUERY
    if (isset($fec) or isset($act) or isset($imp) or isset($adi)) 
       {
	    //Ordenado por Fecha y no por Actividad ni por Importancia 
        if (isset($fec) and $fec!="")
           {
            $worden="ORDER BY 11 ".$fec.",6 desc ";  
            echo "<input type='HIDDEN' name='fec' value=".$fec.">";
           }
          else
             $fec="";   
           
        //Ordenado por Actividad y no por Fecha ni por Importancia 
        if (isset($act) and $act!="")
           { 
            $worden="ORDER BY 1 ".$act;  
            echo "<input type='HIDDEN' name='act' value=".$act.">";
           }
          else
             $act="";  
            
        //Ordenado por Importancia y no por Fecha ni por Actividad 
        if (isset($imp) and $imp!="")
           {
            $worden="ORDER BY 3 ".$imp; 
            echo "<input type='HIDDEN' name='imp' value='".$imp."'>";
           }
          else
             $imp=""; 
             
        //Ordenado por el Dato Adicional 
        if (isset($adi) and $adi!="")
           {
            $worden="ORDER BY 13 ".$adi; 
            echo "<input type='HIDDEN' name='adi' value='".$adi."'>";
           }
          else
             $adi="";     
                 
        $wordeno="on";   
       }
      else
        {
         $worden = "ORDER BY 11 desc, 6 desc "; 
         $fec="desc";
        } 
        
    if (!isset($worden) or $worden=="") 
       {
        $worden = "ORDER BY 11 desc, 6 desc "; 
        $fec="desc";
       } 

    echo "<form name='secretaria' action='Secretaria.php' method=post>"; 
	
    if (isset($wtip))
      {
	    //traigo el codigo correspondiente al tipo de Agenda
		$q =  " SELECT Codigo "
	         ."   FROM agenda_000005 "
	         ."  WHERE Descripcion = '".$wtip."'"       
	         ."    AND Estado      = 'on' "
	         ."  ORDER BY Codigo ";     
	    $res = mysql_query($q,$conex);
	    $num = mysql_num_rows($res);
	    if ($num > 0)
	      {
	       $row = mysql_fetch_array($res); 
	       $wtipcod=$row[0];
	      }
      }     
    
    //Entro aca si no se ha seleccionado el motivo o el destino de la solicitud
    if (!isset($wmotivo) or !isset($wdestino) or (strpos($wdestino,"-") == 0))
       {
        $wgerencia=str_replace("%"," ",$wgerencia);   
           
        encabezado("AGENDA DIRECTIVA - ".$wgerencia." - ".$wano." - SECRETARIA",$wactualiz, "clinica");
        
        //Refresh es el tiempo en que se demora en refrescar la pantalla
	    $wrefresh = "'600";  //DIEZ MINUTOS
	    $wrefres  = 600;
        
        
        echo "<br>";
	    echo "<br>";
	    
	    if (!isset($wtip)) $wtip="";
		
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //ACA TRAIGO LOS TIPOS DE ACTIVIDADES
        if (!isset($wtip))
           {
		    $q =  " SELECT Descripcion, Codigo "
		         ."   FROM agenda_000005 "
		         ."  WHERE Estado = 'on' "
		         ."  ORDER BY Codigo ";
		   }      
		  else
		     {
		 	  $q =  " SELECT Descripcion, Codigo "
		           ."   FROM agenda_000005 "
		           ."  WHERE Descripcion != '".$wtip."'"       //MAYO 14 2007
		           ."    AND Estado       = 'on' "
			       ."  ORDER BY Codigo ";     
	         }     
        $res = mysql_query($q,$conex);
	    $num = mysql_num_rows($res);
		
	    //TIPO DE LA ACTIVIDAD                                 //MAYO 14 2007
	    echo "<center><table border=6>";
		echo "<tr class=seccion1><td align=center>SELECCIONE <b>*** LA AGENDA *** </b>EN LA QUE A GRABAR Y/O CONSULTAR: </td>";
	    echo "<td align=center><select name='wtip' onchange='enter()' class=tipo3>";
	    
	    if (isset($wtip) and trim($wtip) != "")
           echo "<option>".$wtip."</option>";      
	           
		for ($i=1;$i<=$num;$i++)
		    {
			 $row = mysql_fetch_array($res); 
			 
			 if ($i==1 and (!isset($wtip) or trim($wtip)==""))
			    {
                 $wtip=$row[0];
                 $wtipcod=$row[1];
                }
                
	         echo "<option>".$row[0]."</option>";
            }
		echo "</select></td></tr>";
		echo "</table>";
		echo "<br>";
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
		
		echo "<center><table border=1 cellspacing=1>";
        
        if (isset($wtip))
           {
	        //ACA TRAIGO EL DATO ADICIONAL SEGUN EL TIPO DE AGENDA   
	        $q =  " SELECT Dato, Codigo "
	             ."   FROM agenda_000005 "
	             ."  WHERE Descripcion = '".$wtip."'";         //MAYO 14 2007
	        $res = mysql_query($q,$conex);
	        $row = mysql_fetch_array($res); 
	        
	        $wcodtip=$row[1];
	        $wdato=$row[0];   //Dato adicional que se pide o no, segun el tipo de Agenda
	           
	        echo "<tr class=encabezadoTabla>";
            echo "<th><b>Actividad</b></th>";
	        echo "<th><b>Asunto</b></th>";
	        echo "<th><b>Importancia</b></th>";
	        if (trim($wdato) != "" and strtoupper(trim($wdato)) != "NO APLICA")
	          {
	           switch ($wdato)
	             {
		          case "FECHA":
		            { echo "<th><b>Fecha</b></th>"; }
		            break;
		          case "OTRO":  
		           	{ echo "<th><b>Otro</b></th>"; } 
		           	break;
	             }  	
              }
            echo "</tr>";  
	        
	        //TRAIGO LAS ACTIVIDADES
			$q = "  SELECT Descripcion "
			    ."    FROM agenda_000001 "
			    ."   WHERE Tipo = '".$wcodtip."'"
			    ."   ORDER BY Descripcion ";
			$res = mysql_query($q,$conex) or die ($q." - ".mysql_errno()." - ".mysql_error()); 
			$num = mysql_num_rows($res);
			
			echo "<tr>";	     
			echo "<td bgcolor=#cccccc>";
			echo "<select name='wmotivo' class=tipo3>";
			echo "<option>&nbsp</option>";
				 for($i=0;$i<$num;$i++)
				   { 
					$row = mysql_fetch_array($res);   
				    echo "<option>".$row[0]."</option>";
				   }
			echo "</select></td>";
	        echo "<td><TEXTAREA NAME='wobservacion' ROWS='3' COLS='40'></TEXTAREA></td>"; 

	        //ACA TRAIGO LA CLASIFICACION DE LA IMPORTACIA DE LAS ACTIVIDADES
			$q =  " SELECT nombre "
				 ."   FROM agenda_000004 "
				 ."  ORDER BY 1 ";
				 	 
		    $res = mysql_query($q,$conex);
		    $num = mysql_num_rows($res);
		    
			echo "<td bgcolor=#cccccc>";
		    echo "<select name='wdestino' class=tipo3>";
		    echo "<option>&nbsp</option>";    
			for ($i=1;$i<=$num;$i++)
			    {
				 $row = mysql_fetch_array($res); 
		         echo "<option>".$row[0]."</option>";
	            }
			echo "</select></td>";
			
			if (trim($wdato) != "" and strtoupper(trim($wdato)) != "NO APLICA")
	          {
	           switch ($wdato)
	             {
		          case "FECHA":
			   		  {  
			   		    if (!isset($wdat_adi)) $wdat_adi=date("Y-m-d");
				   		  $cal="calendario('wdat_ini','1')";
				   		  
						echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='wdat_adi' size=10 maxlength=10  id='wdat_adi' readonly='readonly' value=".$wdat_adi." class=tipo3><button id='trigger1' onclick=".$cal.">...</button>";
						?>
						   <script type="text/javascript">//<![CDATA[
						      Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wdat_adi',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
						   //]]></script>
						<?php
			   		  }
			   		  break;
			      case "OTRO":  
		           	  { 
			           //echo "<td align=left bgcolor=#cccccc><INPUT TYPE='text' NAME='wdat_adi'></td></tr>"; 
			           echo "<td align=left bgcolor=#cccccc><TEXTAREA NAME='wdat_adi' rows=3 cols=30></TEXTAREA></td></tr>";
			          } 
		           	break;
	             } 
              }    			  	
		    
			echo "<center><tr><td align=center colspan=6 bgcolor=#cccccc><b><input type='submit' value='Enviar Raz&oacute;n'></b></td></tr></center>"; 
			echo "</table>";
			
			echo "<br><br>";
			echo "<center><table>";
   	        echo "<tr><td align=center colspan=15><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
   	        echo "</table>";
			
			$wgerencia=str_replace("%"," ",$wgerencia);
			
			/////////////////////////////////////////////////
			$q = "  SELECT Motivo, Observacion, Destino, Solicito, Camillero, Hora_data, Hora_respuesta, "
			    ."         Habitacion, Id, Observ_central, Fecha_data, Hora_Cumplimiento, Dato "
			    ."    FROM agenda_000003 "
			    ."   WHERE Origen            = '".$wgerencia."'"
			    ."     AND Hora_llegada      = '00:00:00' "
			    ."     AND Hora_Cumplimiento = '00:00:00' "
			    ."     AND Anulada           = 'No' "
			    //."     AND Motivo            = Descripcion "
			    ."     AND Tipo              = '".$wcodtip."'" 
			    
			    ."   UNION "
			    
			    ."  SELECT Motivo, Observacion, Destino, Solicito, Camillero, Hora_data, Hora_respuesta, "
			    ."         Habitacion, Id, Observ_central, Fecha_data, Hora_Cumplimiento, Dato "
			    ."    FROM agenda_000003 "
			    ."   WHERE Origen                = '".$wgerencia."'"
			    ."     AND Hora_llegada          = '00:00:00' "
			    ."     AND trim(Observ_central) != '' "
			    //."     AND Motivo                = Descripcion "
			    ."     AND Tipo                  = '".$wcodtip."'"
			    ."     AND Anulada               = 'No' ".$worden;
			$res = mysql_query($q,$conex) or die ($q." - ".mysql_errno()." - ".mysql_error()); 
		    $num = mysql_num_rows($res);
		    
		    echo "<br>";
		    
		    echo "<center><table border=6>";
		    echo "<tr class=seccion1><td align=center colspan=10><font size=4><b>AGENDA REGISTRADA DENTRO DE UN LAPSO DE SEIS MESES</b></font></td></tr>";
	        echo "<tr class=seccion1><td align=left colspan=10><font size=2><p><b>Hora: ".$hora." ====> Cantidad de Actividades: ".$num." ====> Esta pantalla se actualiza cada ".($wrefres/60)." minutos, contados desde la ultima vez que se presione enter o click en (OK)</p></b></font></td></tr>";
		    
		    
	        $wgerencia=str_replace(" ","%",$wgerencia);
	        
		    echo "<tr class=encabezadoTabla>";
		    echo "<th><a href='Secretaria.php?fec=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a>Fecha</font><a href='Secretaria.php?fec=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></a></th>";
		    echo "<th><b>Hora</b></th>";
		    echo "<th><a href='Secretaria.php?act=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a>Actividad</font><a href='Secretaria.php?act=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></a></th>";
		    echo "<th><b>Asunto</b></th>";
	        echo "<th><a href='Secretaria.php?imp=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a>Importancia</font><a href='Secretaria.php?imp=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></a></th>";
	        echo "<th><b>Observaci&oacute;n del directivo</b></th>";
	        if (trim($wdato) != "" and strtoupper(trim($wdato)) != "NO APLICA")
	          {
	           switch ($wdato)
	             {
		          case "FECHA":
		            { echo "<th><href='Secretaria.php?adi=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'><font size=4>Fecha Entrega.</font><href='Secretaria.php?adi=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></th>"; }
		            break;
		          case "OTRO":  
		            { echo "<th><href='Secretaria.php?adi=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'><font size=4>Otro</font><href='Secretaria.php?adi=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></th>"; }
		           	break;
	             }  	
              }
	        echo "<th><b>Cumplido<br>Secretaria</b></th>";
	        echo "<th><b>Cumplido<br>Directivo</b></th>";
	        echo "<th>Grabar</th>";
	        echo "</tr>";
	        
	        $wgerencia=str_replace("%"," ",$wgerencia);
	        
	        //Coloco una linea en blanco
            echo "<tr class=seccion1><td colspan=10>&nbsp</td></tr>";
		         
            for ($i=1;$i<=$num;$i++)
		        {
			     $row = mysql_fetch_array($res);      
			        
			     //========================================================================================
				 //========================================================================================
				 if (isset($wdato) and $wdato!="FECHA")
	             	{
		             //====================================================================================
				     //====================================================================================	
					 //Aca calculo la diferencia entre la fecha actual y el dia que se coloco el mensaje
				     $q = " SELECT DATEDIFF('".$wfecha."','".$row[10]."') "
				         ."   FROM agenda_000001 "
				         ."  WHERE id=1 ";  
				     $resdias = mysql_query($q,$conex) or die ($q." - ".mysql_errno()." - ".mysql_error()); 
				     $rowdias = mysql_fetch_array($resdias); 
			        
				     //Aca configuro la presentacion de los colores segun el tiempo de respuesta
				     if ($rowdias[0] > 15)                       //Mayor a 15 días
				        {
				         $wcolor = "C3D9FF";                     //Azul
				         $wcolorfor = "000000";                  //Negro
			            }
				     if ($rowdias[0] >= 1 and $rowdias[0] <= 15) //Entre 1 y 15 días
				        {
				         $wcolor = "FFFFCC";                     //Amarillo  
				         $wcolorfor = "000000";                  //Negro
			            } 
				     if ($rowdias[0] < 1)                        //$Menos de un día 
				        { 
					     $wcolor = "99FFCC";                     //Verde   
					     $wcolorfor = "000000";                  //Negro
			            } 
		             //====================================================================================   
			         //====================================================================================
		            }
		           else
		              if (isset($wdato) and $wdato=="FECHA")
		                 {
			              //Aca calculo la diferencia entre la fecha actual y la fecha de entrega del mensaje
					      //$q = " SELECT DATEDIFF('".$wfecha."','".$row[12]."') "
					      $q = " SELECT DATEDIFF('".$row[12]."','".$wfecha."') "
					          ."   FROM agenda_000001 "
					          ."  WHERE id=1 ";  
					      $resdias = mysql_query($q,$conex) or die ($q." - ".mysql_errno()." - ".mysql_error()); 
					      $rowdias = mysql_fetch_array($resdias);   
					      
					      if ($rowdias[0] < 1)     //Si los dias son menores a 1, es porque el compromiso ya se vencio
			                {  
						     $wcolor = "66CC66";					   //Verde
						     $wcolorfor = "000000";                    //Negro
				            }
				           else
				              { 
					           $wcolor = "FFFFCC";                     //Amarillo   
							   $wcolorfor = "000000";                  //Negro
					          } 
		                 }    
		             
		         $wfec=explode("-",$row[10]); //Aca separo la fecha para tomar el mes y el día
			     switch ($wfec[1])
			           {
				        case "01":
				           { $wmes="Ene-".$wfec[2]; }
				           break;
				        case "02":
				           { $wmes="Feb-".$wfec[2]; }
				           break; 
				        case "03":
				           { $wmes="Mar-".$wfec[2]; }
				           break;
				        case "04":
				           { $wmes="Abr-".$wfec[2]; }
				           break;
				        case "05":
				           { $wmes="May-".$wfec[2]; }
				           break; 
				        case "06":
				           { $wmes="Jun-".$wfec[2]; }
				           break;
				        case "07":
				           { $wmes="Jul-".$wfec[2]; }
				           break;
				        case "08":
				           { $wmes="Ago-".$wfec[2]; }
				           break; 
				        case "09":
				           { $wmes="Sep-".$wfec[2]; }
				           break;
				        case "10":
				           { $wmes="Oct-".$wfec[2]; }
				           break;
				        case "11":
				           { $wmes="Nov-".$wfec[2]; }
				           break; 
				        case "12":
				           { $wmes="Dic-".$wfec[2]; }
				           break;                        
			           }
			     
			     echo "<tr>";
			     echo "<td bgcolor=".$wcolor."><font size=3>".$wmes."</font></td>";                     // Fecha
			     echo "<td bgcolor=".$wcolor."><font size=3>".$row[5]."</font></td>";                   // Hora
			     echo "<td bgcolor=".$wcolor."><font size=3>".$row[0]."</font></td>";                   // Actividad
			     echo "<td bgcolor=".$wcolor."><font size=3><b>".$row[1]."</b></font></td>";            // Observacion
		         echo "<td bgcolor=".$wcolor."><font size=3><b>".$row[2]."</b></font></td>";            // Destino
		         //==================================================================
		         
		         if ($row[9] == "")
				    echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>";                      // Sin Observacion del directivo
				   else
				      if (isset($wobscc[$i]))
				         echo "<td align=left bgcolor=".$wcolor.">".$wobscc[$i]."</td>";                // Observacion del directivo   
				        else 
		                   echo "<td align=left bgcolor=".$wcolor.">".$row[9]."</td>";                  // Observacion del directivo
		         
		         //Dato Adicional          
		         if (trim($wdato) != "" and strtoupper(trim($wdato) != "NO APLICA"))
			         if (trim($row[12]) != "")
				        echo "<td align=left bgcolor=".$wcolor.">".$row[12]."</td>";                    // Dato Adicional
				       else
				          echo "<td align=left bgcolor=".$wcolor.">&nbsp</td>";                         // Dato Adicional 
		                            
		         //Cumplimiento de la secretaria          
		         //Muestro el checkbox de llegada si tiene datos en el campo de observacion
		         if (trim($row[9]) != "")
		            {
			         echo "<td align=center bgcolor=".$wcolor."><font size=3><INPUT TYPE=CHECKBOX NAME='wllegada[".$i."]'></font></td>";  
			         if (isset($wllegada[$i]))
			            {
			             $q = "   UPDATE agenda_000003 "
		                     ."      SET Hora_llegada  = '".$hora."',"
		                     ."          Solicito      = '".$wusuario."'"
		                     ."    WHERE Id   = ".$row[8]
		                     ."      AND Tipo = '".$wtipcod."'";
		                 $rescam = mysql_query($q,$conex) or die ($q." - ".mysql_errno()." - ".mysql_error()); 
		                 
		                 if (!isset($fec)) $fec=""; 
						 if (!isset($act)) $act="";
						 if (!isset($imp)) $imp="";
						 if (!isset($adi)) $adi="";
		                 
						 echo "<meta http-equiv='refresh' content='0;url=Secretaria.php?wgerencia=".$wgerencia."&wtip=".$wtip."&fec=".$fec."&act=".$act."&imp=".$imp."&adi=".$adi."'>";
	                    }
	                }
                   else
                      echo "<td align=center bgcolor=".$wcolor."><font size=3><INPUT TYPE=CHECKBOX NAME='wllegada[".$i."]' DISABLED></font></td>";       
		         
                 //Cumplimiento del Directivo          
		         //Muestro el checkbox de cumplimiento
		         
		         if (isset($wcumplimiento[$i]))
		            {
			         $q = "   UPDATE agenda_000003 "
			             ."      SET Hora_cumplimiento = '".$hora."'"
			             ."    WHERE Id                = ".$row[8]
			             ."      AND Hora_cumplimiento = '00:00:00' "
			             ."      AND Tipo              = '".$wtipcod."'";
			         $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
			         					         
			         if (!isset($fec)) $fec=""; 
			         if (!isset($act)) $act="";
			         if (!isset($imp)) $imp="";
			    
			         $wgerencia=str_replace(" ","%",$wgerencia);
			         
			         echo "<td align=center bgcolor=".$wcolor."><font size=3 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wcumplimiento[".$i."]' CHECKED></font></td>"; 
			         echo "<meta http-equiv='refresh' content='0;url=Secretaria.php?fec=".$fec."&amp;act=".$act."&amp;imp=".$imp."&amp;wtip=".$wtip."&amp;wgerencia=".$wgerencia."'>";
			         
			         $wgerencia=str_replace("%"," ",$wgerencia);
		            } 
		           else
		             if ($row[11] != "00:00:00")
		                echo "<td align=center bgcolor=".$wcolor."><font size=3 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wcumplimiento[".$i."]' CHECKED DISABLED></font></td>";
		               else   
		                  echo "<td align=center bgcolor=".$wcolor."><font size=3 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wcumplimiento[".$i."]'></font></td>";
	             
                 echo "<td align=center bgcolor=#cccccc><b><input type='submit' value='OK'></b></td>";
		         echo "</tr>";
	            }
	        echo "</center></table>";
	        
	        //ACA GRABO EL MENSAJE
		    if (isset($wmotivo) and isset($wdestino) and (strlen($wmotivo) > 1) and (strlen($wdestino) > 1) )
		      {
			    $wrefresh = "'0";
			    
			    if (!isset($wdat_adi))
			       {
			        $wdat_adi=" ";
		           } 
			    
			    $q = "     INSERT INTO agenda_000003 (Medico  ,   Fecha_data,    Hora_data,   Origen       ,   Motivo     , Habitacion,   Observacion     ,    Destino     ,   Solicito    ,   Ccosto       , Camillero, Hora_respuesta, Hora_llegada, Hora_Cumplimiento, Anulada, Observ_central,    Dato        ,    Tipo       ,      Seguridad   ) "
				    ."                        VALUES ('agenda','".$wfecha."','".$hora."'  ,'".$wgerencia."','".$wmotivo."', '0'       ,'".$wobservacion."', '".$wdestino."','".$wusuario."','".$wgerencia."', ''       , ''            , ''          , ''               , 'No'   , ''            , '".$wdat_adi."', '".$wtipcod."', 'C-".$wusuario."') ";
				$res2 = mysql_query($q,$conex) or die($q." - ".mysql_errno().":".mysql_error()); 
			     
			    $fec="desc"; 
				$act="";
				$imp="";  
				echo "<input type='HIDDEN' name='fec' value=".$fec.">";
				echo "<input type='HIDDEN' name='act' value=".$act.">";
				echo "<input type='HIDDEN' name='imp' value=".$imp.">"; 
			  }
	      } //Fin del then del if isset($wtip)
	   }
       
	   echo "<input type='HIDDEN' name='wgerencia' value=".$wgerencia.">"; 
	   
	   if (!isset($fec)) $fec=""; 
       if (!isset($act)) $act="";
       if (!isset($imp)) $imp="";    
	       
	   echo "<meta http-equiv='refresh' content=".$wrefresh.";url=Secretaria.php?wgerencia=".$wgerencia."&wtip=".$wtip."&fec=".$fec."&act=".$act."&imp=".$imp."'>";
	 
	 echo "<HR align=center></hr>";  //Linea horizontal
	 
	 if (isset($wdato) and $wdato !="FECHA")
	 	{ 
		 echo "<table border=6 align=right>";
		 echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
		 echo "<tr><td colspan=3 bgcolor="."99FFCC"."><font size=2 color='"."000000"."'>&nbsp Hoy</font></td></tr>";                       //Verde 
		 echo "<tr><td colspan=3 bgcolor="."FFFFCC"."><font size=2 color='"."000000"."'>&nbsp De 1 a 15 días</font></td></tr>";            //Amarillo
		 echo "<tr><td colspan=3 bgcolor="."C3D9FF"."><font size=2 color='"."000000"."'>&nbsp Mas de quince (15) días</font></td></tr>";   //Azul
		 echo "</table>";
	    } 
	   else
	     if (isset($wdato) and $wdato=="FECHA")
	       {
		    echo "<table border=6 align=right>";
		    echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
		    echo "<tr><td colspan=3 bgcolor="."FFFFCC"."><font size=2 color='"."000000"."'>&nbsp Pendiente</font></td></tr>";                 //Amarillo 
		    echo "<tr><td colspan=3 bgcolor="."66CC66"."><font size=2 color='"."000000"."'>&nbsp Tarea Vencida</font></td></tr>";            //Verde
		    echo "</table>";
	       }      
   	 
	 
	 echo "<input type='HIDDEN' name='fec' value=".$fec.">";
	 echo "<input type='HIDDEN' name='act' value=".$act.">";
	 echo "<input type='HIDDEN' name='imp' value=".$imp.">";
	 echo "<input type='HIDDEN' name='wgerencia' value='".$wgerencia."'>";
	 
	 echo "<table border=2 align=left>";
	 $wgerencia=str_replace(" ","%",$wgerencia);
	 echo "<tr><td align=left ><font size=4><A href='/matrix/registro.php?call=1&Form=000003-agenda-C-Registro de Mensajes&Frm=0&tipo=P&key=agenda&CP=Origen=".$wgerencia."' target='_blank'> Consultar Mensajes Anteriores</A></font></td></tr>";       
	 
	 echo "</table>";
		 
     echo "</form>";    
     
      echo "<br><br>";
   	  echo "<tr><td align=center colspan=15><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
}
include_once("free.php");
?>
