<head>
  <title>CENTRAL DE CAMILLEROS</title>
</head>
<body >
<BODY TEXT="#000000">
<script type="text/javascript">
	function enter()
	{
	   document.forms.central.submit();
	}
	
	function cerrarVentana()
	 {
      top.close()		  
     } 
	
</script>
<?php
include_once("conex.php");



  /***************************************************
	*	           CENTRAL DE CAMILLEROS             *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']) and !isset($user))
	echo "error usuario no esta registrado";
else
{
	
  
    include_once("root/magenta.php");
    include_once("root/comun.php");
  
    $conex = obtenerConexionBD("matrix");
	
    

  	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
    $wactualiz="(Octubre 6 de 2011)";                            // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
  					    
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
         
          
    function actualizar_operador($wcentral, $wcodope, $whorope)
      {
	   global $wcentral;
	   global $wcodope;
	   global $whorope;
	   global $hora;
	   global $wusuario;
	   global $conex;
	   
	   
	   //Traigo el operario que tiene actualmente y la hora hasta la que se queda en turno
	   $q = " SELECT cenope, cenhop "
	       ."   FROM cencam_000006 "
	       ."  WHERE codcen = '".$wcentral."'"
	       ."    AND cenest = 'on' ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);
	   
	   $wope_reg=$row[0];
	   $whor_reg=$row[1];
	   
	   $whora=explode(":",$hora);
	   
	   
	   //Busco si el usuario es un camillero de la misma central para colocarlo como operario de la central
	   //esto beneficia mucho la central de Servicio Farmaceutico
	   $q = " SELECT COUNT(*) "
	       ."   FROM cencam_000002 "
	       ."  WHERE codced  = '".$wusuario."'"
	       ."    AND central = '".$wcentral."'";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);
	   
	   
	   if ($row[0] > 0)  //Si entra es porque si es un Camillero de la misma Central
	      {
	       //============================================================================================================================
		   //Según la hora actual coloco cual es el turno del operario y lo coloco en el registro de la central en la tabla cencam_000006
		   //============================================================================================================================
		   if (strval($whora[0]) >= 7 and strval($whora[0]) < 19)
		      $whor_gra="19:00:00";
		     else 
		        $whor_gra="07:00:00";
		   //============================================================================================================================
		   
		   
		   if ($whor_reg=="00:00:00")               //Si entra por aca  es porque es primera vez que se asigna el Operario a esta central
		      {
			   $q = " UPDATE cencam_000006 "
			       ."    SET cenope = '".$wusuario."', "
			       ."        cenhop = '".$whor_gra."' "
			       ."  WHERE codcen = '".$wcentral."' "
			       ."    AND cenest = 'on' ";
			   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
			  }
			 else
			    {     
				 if ($hora > $whor_reg)            //Si la hora actual es mayor a la hora final del operario entro, asi sea por un segundo
				    {
					 if ($wusuario != $wope_reg)   //Si el usuario que entro es diferente al que esta como operario, entonces entro a cambiarlo
					    {   
						 $q = " UPDATE cencam_000006 "
						     ."    SET cenope  = '".$wusuario."', "
						     ."        cenhop  = '".$whor_gra."'  "
						     ."  WHERE codcen  = '".$wcentral."'  "
						     ."    AND cenest  = 'on' ";
						 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				        }
				    }    
				}
	      }           
	  }        
          
          
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ACA SE COLOCA EL HORARIO DE ATENCION EN CENTRAL DE CAMILLEROS POR MATRIX
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$whora_atencion="LAS 24 HORAS DE LUNES A DOMINGO";
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	$wfecha=date("Y-m-d"); 
	$hora = (string)date("H:i:s");
	
	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));
  
	
	$q = " SELECT nomcen, centre, cenvig, cenest, cenope, cenhop "
	    ."   FROM cencam_000006 "
	    ."  WHERE codcen = '".$wcentral."'";
	$res = mysql_query($q,$conex);    
	$num = mysql_num_rows($res);
	if ($num > 0)
	  {
	   $row = mysql_fetch_array($res);
	   $wnomcen               = $row[0];
	   $wtiempo_refresh       = $row[1];	//Tiempo en SEGUNDOS que tarda la pantalla en referescarse o actualizarse
	   $wvigencia_solicitudes = $row[2];    //Tiempo en HORAS que puede verse una solicitud que no se halla terminado
	   $westado_central       = $row[3];    //Estado de la central 'on': activa, 'off':inactiva
	   $wcodope               = $row[3];    //Codigo del operador de la central
	   $whorope               = $row[3];    //Hora hasta la que esta el operario
	   
	   encabezado("CENTRAL ".$wnomcen,$wactualiz, "clinica");
	   
	   actualizar_operador($wcentral, $wcodope, $whorope);
	   
	  }		        
	 else
	    echo "<tr><td align=center bgcolor=#fffffff colspan=13><font size=3 text color=#CC0000><b>FALTA DEFINIR EN LA TABLA cencam_000006 EL CODIGO DE LA CENTRAL</b></font></td></tr>";
	    
	    
	    
	if ($westado_central=="on")    
	  { 
	    //====================================================================================================================================
	    //COMIENZA LA FORMA      
	    echo "<form name=central action='central_de_camilleros.php' method=post>";
	    
	    echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='HIDDEN' NAME= 'wcentral' value='".$wcentral."'>";
	    
	    echo "<HR align=center></hr>";
	    
		//===================================================================================================================================================
	    // QUERY PRINCIPAL 
	    //===================================================================================================================================================
	    // ACA TRAIGO TODAS LAS SOLICITUDES HECHAS QUE NO TENGAN MAS DE DOS HORAS DE ESPERA
	    //===================================================================================================================================================
	    $q = "  SELECT A.Hora_data, Origen, Motivo, Observacion, Destino, Solicito, Camillero, "
	        ."         Hora_llegada, Hora_Cumplimiento, A.Id, Habitacion, Observ_central, A.fecha_data "
		    ."    FROM cencam_000003 A, cencam_000001 B"
		    ."   WHERE Anulada           = 'No' "
		    ."     AND TIMESTAMPDIFF(HOUR,CONCAT(A.Fecha_data,' ',A.Hora_data),CONCAT('".$wfecha."',' ','".$hora."')) <= ".$wvigencia_solicitudes 
		    ."     AND Hora_cumplimiento = '00:00:00' "
		    ."     AND Motivo            = Descripcion "
		    ."     AND A.central         = '".$wcentral."'"
		    ."   ORDER BY A.Fecha_data desc, A.Hora_data desc ";
		$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
	    
		echo "<center><table border=0>";
	    echo "<tr><td align=left bgcolor=#fffffff colspan=3><font size=2 text color=#CC0000><b>Hora: ".$hora."</b></font></td><td align=left bgcolor=#fffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Solicitudes: ".$num."</b></font></td></tr>";
	    
	    echo "<tr class='encabezadoTabla'>";
	    echo "<th><font size=1>Gra</font></th>";
		echo "<th><font size=1>Fecha</font></th>";
	    echo "<th><font size=1>Hora</font></th>";
	    echo "<th><font size=1>Origen</font></th>";
	    echo "<th><font size=1>Motivo</font></th>";
	    echo "<th><font size=1>Habit</font></th>";
	    echo "<th><font size=1>Observacion</font></th>";
	    echo "<th><font size=1>Destino</font></th>";
	    echo "<th><font size=1>Solicitado por</font></th>";
	    echo "<th><font size=1>Camillero/Ubicación</font></th>";
	    echo "<th><font size=1>Lle</font></th>";
	    echo "<th><font size=1>Cum</font></th>";
	    echo "<th><font size=1>Anu</font></th>";
	    echo "<th><font size=1>Observ.Central</font></th>";
	    echo "<th><font size=1>Gra</font></th>";
	    echo "</tr>";
	    
	    for ($i=$num;$i>=1;$i--)
		   {
			$row = mysql_fetch_array($res); 
			
		    $a1=$row[0];
		    $a2=date("H:i:s");
		    $a3=((integer)substr($a2,0,2)-(integer)substr($a1,0,2))*60 + ((integer)substr($a2,3,2)-(integer)substr($a1,3,2)) + ((integer)substr($a2,6,2)-(integer)substr($a1,6,2))/60;
		    
		    //Aca configuro la presentacion de los colores segun el tiempo de respuesta
		    if ($a3 > 5)                 //$a3 > 5
		       {
		        $wcolor = "CCCCFF";      //Lila
		        $wcolorfor = "000000";   //Negro
	           }
		    if ($a3 > 2.5 and $a3 <= 5)  //$a3 > 2.5 and $a3 <= 5
		       {
		        $wcolor = "FFFF66";      //Amarillo  
		        $wcolorfor = "000000";   //Negro
	           } 
		    if ($a3 <= 2.5)              //$a3 <= 2.5 
		       { 
			    $wcolor = "99FFCC";      //Verde   
			    $wcolorfor = "000000";   //Negro
	           } 
	                        
			echo "<tr bgcolor=".$wcolor.">";
			echo "<td align=center bgcolor=#cccccc></b><input type='submit' value='OK'></b></td>";
			echo "<td><font size=1 color=".$wcolorfor.">".$row[12]."</font></td>";                  // Fecha de solicitud
		    echo "<td><font size=1 color=".$wcolorfor.">".$row[0]."</font></td>";                   // Hora de solicitud
		    echo "<td><font size=1 color=".$wcolorfor.">".$row[1]."</font></td>";                   // Origen
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[2]."</font></td>";                   // Motivo
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[10]."</font></td>";                  // Habitacion
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[3]."</font></td>";                   // Observacion
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[4]."</font></td>";                   // Destino
	        
	        //============================================================================================================================
	        //TRAIGO EL NOMBRE DE QUIEN SOLICITO EL SERVICIO
	        $q = "  SELECT Descripcion "
	            ."    FROM Usuarios "
	            ."   WHERE Codigo = '".$row[5]."'"
	            ."     AND Activo = 'A' ";
		    $res2 = mysql_query($q,$conex);
		    $row1 = mysql_fetch_array($res2);
		    
		    if ($row1[0] == "")
		       $wnomusu = $row[5];                                                                 // Sin Nombre de Solicitado por
		      else 
		         $wnomusu = $row1[0];                                                              // Solicitado por
		    echo "<td bgcolor=".$wcolor."><font size=1 color=".$wcolorfor.">".$wnomusu."</font></td>";                  // Nombre de quien solicito el servicio
		    //============================================================================================================================
		    
		    //============================================================================================================================
		    //TRAIGO LOS CAMILLEROS 
		    $wpaso="N";
		    if (strpos($row[6],"-") == 0 )
		      {
			   if (isset($wcamillero[$i]) and strpos($wcamillero[$i],"-") >= 1 )
			      {
				    //$wcodigo = substr($row[6],0,(strpos($row[6],"-")-1));    
		            $wcodigo = substr($wcamillero[$i],0,(strpos($wcamillero[$i],"-")-1));       		     
			        
				    $q = " SELECT Codigo, Nombre, 1 AS Tip "  //El tipo lo utilizo para poder ordenar el query por nombre, trayendo primero el registro que selecciono el usuario
				        ."    FROM cencam_000002 "
				        ."   WHERE Codigo = '".$wcodigo."'"
				        ."     AND Unidad != 'INACTIVO' "
				        ."     AND central = '".$wcentral."'"
				           
		                ."   UNION "
				            
				        ."  SELECT Codigo, Nombre, 2 AS Tip "
				        ."    FROM cencam_000002 "
				        ."   WHERE Codigo != '".$wcodigo."'"
				        ."     AND Unidad != 'INACTIVO' "
				        ."     AND central = '".$wcentral."'"
				        ."   ORDER BY Tip, Nombre " ;
				    $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				    $numcam = mysql_num_rows($rescam) or die (mysql_errno()." - ".mysql_error());
				
			 		echo "<td bgcolor=".$wcolor.">";
			    	echo "<SELECT name='wcamillero[".$i."]' onchange='enter()'>";
			    	for($j=0;$j<$numcam;$j++)
					   { 
						$rowcam = mysql_fetch_array($rescam);   
						echo "<option>".$rowcam[0]." - ".$rowcam[1]."</option>";
					   }
				    echo "</SELECT></td>";
				    $wpaso="S";
			      }    
				 else     
			       $q = "  SELECT Codigo, Nombre "
		               ."    FROM cencam_000002 " 
		               ."   WHERE Unidad != 'INACTIVO' " 
		               ."     AND central = '".$wcentral."'"
		               ."   ORDER BY Nombre ";
		      }
		     else
		       {
			    if (strpos($row[6],"-") >= 1)
			       {
				    if (isset($wcamillero) and $row[6] != $wcamillero[$i])
				       {
					    echo "<meta http-equiv='refresh' content='0;url=central_de_camilleros.php?wemp_pmla=".$wemp_pmla."&wcentral=".$wcentral."'>";
				       }         
				      
				    $wcodigo = substr($row[6],0,(strpos($row[6],"-")-1));     
				             		     
					$q = "  SELECT Codigo, Nombre, 1 AS Tip "
					    ."    FROM cencam_000002 "
					    ."   WHERE Codigo = '".$wcodigo."'"
					    ."     AND Unidad != 'INACTIVO' "
					    ."     AND central = '".$wcentral."'"
					          
					    ."   UNION "
					            
					    ."  SELECT Codigo, Nombre, 2 AS Tip "
					    ."    FROM cencam_000002 "
					    ."   WHERE Codigo != '".$wcodigo."'"
					    ."     AND Unidad != 'INACTIVO' "
					    ."     AND central = '".$wcentral."'"
					    ."   ORDER BY Tip, Nombre ";
				   }
		        }
		    if ($wpaso == "N")
		       {    
			    $rescam = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
			    $numcam = mysql_num_rows($rescam); // or die (mysql_errno()." - ".mysql_error());
			
			    echo "<td bgcolor=".$wcolor.">";
		    	echo "<SELECT name='wcamillero[".$i."]' onchange='enter()'>";
		    	
		    	if (strpos($row[6],"-") == 0 or $numcam==0)
		    	   {
			    	echo "<option>&nbsp</option>";
	    	       } 
		    	   
				for($j=0;$j<$numcam;$j++)
				   { 
					$rowcam = mysql_fetch_array($rescam);  
					echo "<option>".$rowcam[0]." - ".$rowcam[1]."</option>";
				   }
			    echo "</SELECT></td>";
	           }
		    //Aca actualizo el camillero =================================================================================================
		    if (isset($wcamillero[$i]) and strpos($wcamillero[$i],"-") >= 1 )
		       {
			    if ($wcamillero[$i] != $row[6] and !isset($wllegada[$i]))  
			       {
				    $q= "  UPDATE cencam_000003 "
				       ."     SET Camillero       = '".$wcamillero[$i]."', "
				       ."         Hora_respuesta  = '".$hora."', "
				       ."         Central         = '".$wcentral."', "
				       ."         Usu_central     = '".$wusuario."' "
				       ."   WHERE Id = ".$row[9]
				       ."     AND (Central        = '' "
				       ."      OR  Central        = '".$wcentral."') "
				       ."     AND (Hora_respuesta = '00:00:00' "
				       ."      OR  Usu_central    = '".$wusuario."') ";
				    $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			       } 
			   }
	        //============================================================================================================================
		    
		    //============================================================================================================================
		    //Evaluo si la LLEGADA ya habia sido dada, si no, evaluo si se dio dando click en checkbox, si no la muestro desmarcada
		    if ($row[7] != "00:00:00")
		       //Tiene hora de llegada pero no tiene asignado camillero, entonces coloco la hora de llegada en ceros
		       if (strpos($row[6],"-") == 0 )
		          {
			       $q = "   UPDATE cencam_000003 "
			           ."      SET Hora_llegada = '00:00:00' "       //Llevo nulo
			           ."    WHERE Id = ".$row[9];
			       $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			       echo "<td align=center bgcolor=".$wcolor."><font size=1 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wllegada[".$i."]'></font></td>"; 
		          }
		         else
		            echo "<td align=center bgcolor=".$wcolor."><font size=1 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wllegada[".$i."]' CHECKED></font></td>"; 
	          else
		         if (isset($wllegada[$i]) and strpos($wcamillero[$i],"-") >= 1)
		            {
			         $q = "   UPDATE cencam_000003 "
			             ."      SET Hora_llegada   = '".$hora."'"
			             ."    WHERE Id = ".$row[9];
			         $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
			         
			         echo "<td align=center bgcolor=".$wcolor."><font size=1 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wllegada[".$i."]' CHECKED></font></td>"; 
		            } 
		           else //Si esta desmarcada la casilla, borro la hora que tenia grabada en este campo
		              {   
			           $q = "   UPDATE cencam_000003 "
			             ."      SET Hora_llegada   = '00:00:00' "       //Llevo nulo
			             ."    WHERE Id = ".$row[9];
			           $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			           echo "<td align=center bgcolor=".$wcolor."><font size=1 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wllegada[".$i."]'></font></td>"; 
		              }
	        
			//============================================================================================================================
		    //Evaluo si el CUMPLIMIENTO ya habia sido dado, si no, evaluo si se dio dando click en checkbox, si no lo muestro desmarcado
		    if ($row[8] != "00:00:00")
		       echo "<td align=center bgcolor=".$wcolor."><font size=1 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wcumplimiento[".$i."]' CHECKED></font></td>"; 
		      else
		         if (isset($wcumplimiento[$i]) and isset($wllegada[$i]) and (strpos($row[6],"-") >= 1))
		            {
			         $q = "   UPDATE cencam_000003 "
			             ."      SET Hora_cumplimiento = '".$hora."'"
			             ."    WHERE Id = ".$row[9];
			         $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
			         
			         echo "<td align=center bgcolor=".$wcolor."><font size=1 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wcumplimiento[".$i."]' CHECKED></font></td>"; 
			         echo "<meta http-equiv='refresh' content='0;url=central_de_camilleros.php?wemp_pmla=".$wemp_pmla."&wcentral=".$wcentral."'>";
		            } 
		           else   //Si esta desmarcada la casilla, borro la hora que tenia grabada en este campo
		              {
			           $q = "   UPDATE cencam_000003 "
			             ."      SET Hora_cumplimiento = '00:00:00'"    //Llevo un nulo
			             ."    WHERE Id = ".$row[9];
			           $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());    
			           echo "<td align=center bgcolor=".$wcolor."><font size=1 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wcumplimiento[".$i."]'></font></td>";
		              }
			       
		    //============================================================================================================================
		    //Evaluo si ha sido ANULADA la solicitud
		    if (isset($wanulada[$i]) and $row[7] == "00:00:00" and $row[8] == "00:00:00") 
		       {
			    $q = "   UPDATE cencam_000003 "
			        ."      SET Anulada = 'Si'"
			        ."    WHERE Id = ".$row[9];
			    $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
			    echo "<td align=center bgcolor=".$wcolor."><font size=1><INPUT TYPE=CHECKBOX NAME='wanulada[".$i."]' CHECKED></font></td>"; 
			    echo "<meta http-equiv='refresh' content='0;url=central_de_camilleros.php?wemp_pmla=".$wemp_pmla."&wcentral=".$wcentral."'>";
		       } 
		      else   
	             echo "<td align=center bgcolor=".$wcolor."><font size=1><INPUT TYPE=CHECKBOX NAME='wanulada[".$i."]'></font></td>"; 
	        
	        //===========================================================================================================================
	        //Observacion de la central de camilleros
	        if (isset($wobscc[$i]))
	           {
			    ///echo "<td align=left bgcolor=".$wcolor."><TEXTAREA NAME='wobscc[".$i."]' ROWS='3' COLS='20'>".$wobscc[".$i."]."</TEXTAREA></td>";
			   
			    echo "<td align=left bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wobscc[".$i."]' VALUE='".$wobscc[$i]."'></td>";  // Observacion de la central camilleros
		        if ($wobscc[$i] != "")
		           {
			        if ($wobscc[$i] != $row[11]) 
				      {  
				       $q = "   UPDATE cencam_000003 "
				           ."      SET Observ_central = '".$wobscc[$i]."'"
				           ."    WHERE Id = ".$row[9];
				       $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());   
				       
				       //echo "<meta http-equiv='refresh' content='0;url=central_de_camilleros.php?'>";   
				      } 
			       }   
			   }
	          else
			    {
	             echo "<td align=left bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wobscc[".$i."]' VALUE='".$row[11]."'></td>";  // Observacion de la central camilleros  
				 ///echo "<td align=left bgcolor=".$wcolor."><TEXTAREA NAME='wobscc[".$i."]' ROWS='3' COLS='20'>".$row[11]."</TEXTAREA></td>";
				}
	             
	        echo "<td align=center bgcolor=#cccccc></b><input type='submit' value='OK'></b></td>";
	        echo "</tr>";
	       } //FIN DEL FOR
	       
	   echo "<tr></tr>";
	   echo "<tr></tr>";
	   echo "<tr></tr>";
	   echo "<tr><td align=center colspan=13><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";    
	       
	   echo "</BIG>";
	   echo "</center></table>";
	   //echo "<br>";
	   //echo "<font size=2 text color=#CC0000><b>Cantidad de Solicitudes: ".$num."</b></font>";
	   
	   echo "<HR align=center></hr>";  //Linea horizontal
	   
	   echo "<table border=1 align=right>";
	   echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
	   echo "<tr><td colspan=3 bgcolor="."CCCCFF"."><font size=2 color='"."000000"."'>&nbsp Mas de cinco (5) minutos</font></td></tr>";      //Lila
	   echo "<tr><td colspan=3 bgcolor="."FFFF66"."><font size=2 color='"."000000"."'>&nbsp De 2.5 a 5 minutos</font></td></tr>";            //Amarillo
	   echo "<tr><td colspan=3 bgcolor="."99FFCC"."><font size=2 color='"."000000"."'>&nbsp Menos de 2.5 minutos</font></td></tr>";          //Verde  
	   
	   echo "</table>";
	   
	   echo "</form>";
	   
	   //echo "<meta http-equiv='refresh' content='40;url=central_de_camilleros.php?wemp_pmla=".$wemp_pmla."&wcentral=".$wcentral."'>";
	   echo "<meta http-equiv='refresh' content='".$wtiempo_refresh.";url=central_de_camilleros.php?wemp_pmla=".$wemp_pmla."&wcentral=".$wcentral."'>";
	  }
     else
       {
	    echo "<br><br>";   
        echo "<center><table>";
        echo "<tr><td align=center bgcolor=#fffffff><font size=5 text color=#CC0000><b>LA CENTRAL ESTA INACTIVA</b></font></td></tr>";
        echo "</table>";
       } 
}

echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='HIDDEN' NAME= 'wcentral' value='".$wcentral."'>";
	
include_once("free.php");
?>