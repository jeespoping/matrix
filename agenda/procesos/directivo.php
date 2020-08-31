<head>
  <title>AGENDA DIRECTIVA</title>
</head>

<BODY>
<script type="text/javascript">
	function enter()
	{
	   document.forms.agenda.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     } 
	
</script>
<?php
include_once("conex.php");


   /**************************************************
	*	             AGENDA DIRECTIVA                *
	*				CONEX, FREE =>  OK				 *
	**************************************************/
//session_start();
//if(!isset($_SESSION['user']))
//	echo "error";
//else
{
	

    

    include_once("root/comun.php");
    
	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
       $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
         
    
    $wactualiz="(2010-02-03)";   
       
    
    
    function convencion()
      {
	   echo "<table border=6 align=right>";
	   echo "<caption bgcolor=#ffcc66><b>Convenciones</b></caption>";
	   echo "<tr><td colspan=3 bgcolor="."99FFCC"."><font size=2 color='"."000000"."'>&nbsp De Hoy</font></td></tr>";                    //Verde  
	   echo "<tr><td colspan=3 bgcolor="."FFFFCC"."><font size=2 color='"."000000"."'>&nbsp De 1 a 15 días</font></td></tr>";         //Amarillo
	   echo "<tr class=fila1><td colspan=3><font size=2>&nbsp De Mas de quince (15) días</font></td></tr>";   							  //Azul claro
	   echo "<tr><td colspan=3 bgcolor="."FF9966"."><font size=2 color='"."000000"."'>&nbsp atendido</font></td></tr>";       //Naranjado
	   echo "</table>";    
	  }    
    
    
	//========================================================================================================================================================
	//========================================================================================================================================================
	function mostrar()
	  {
		global $wgerencia;
		global $wcodtip;
		global $conex;
		global $wconex;
		global $wfecha;
		global $hora;
		global $worden;
		global $wtip;
		global $wid;
		  
		  
		//ACA TRAIGO EL DATO ADICIONAL SEGUN EL TIPO DE AGENDA   
        $q =  " SELECT Dato, Codigo "
             ."   FROM agenda_000005 "
             ."  WHERE Descripcion = '".$wtip."'";
        $res = mysql_query($q,$conex);
        $row = mysql_fetch_array($res); 
        
        $wdato=$row[0];   //Dato adicional que se pide o no, segun el tipo de Agenda   
		$wcodtip=$row[1];    
		   
		//===================================================================================================================================================
	    // QUERY PRINCIPAL 
	    //===================================================================================================================================================
	    // ACA TRAIGO TODAS LAS SOLICITUDES HECHAS QUE NO TENGAN MAS DE DOS MESES DE ESPERA
	    //===================================================================================================================================================
	    //Trae los mensajes de 4 meses hasta la hoy y que no tengan hora de llegada (Realizado por el directivo) sin importar la hora de cumplimiento (atendida
	    //o vista por el directivo parcialmente)
	    $q = "  SELECT Hora_data, Origen, Motivo, Observacion, Destino, Solicito, Camillero, Hora_llegada, "
	        ."         Hora_Cumplimiento, Id, Habitacion, Observ_central, Fecha_data, Dato "
		    ."    FROM agenda_000003 "
		    ."   WHERE Anulada                   = 'No' "                           
		    ."     AND agenda_000003.fecha_data >= str_to_date(ADDDATE('".$wfecha."',-360),'%Y-%m-%d')"    //Muestra los mensajes de 6 meses hasta hoy
		    ."     AND Origen                    = '".$wgerencia."'"
		    ."     AND Tipo                      = '".$wcodtip."' "
		    ."     AND Hora_llegada              = '00:00:00' ".$worden; 
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);
		
		
		echo "<br>";
   		echo "<tr><td align=center colspan=15><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
   		echo "<br>";
				
		echo "<br>";
		echo "<center><table border=1 cellspacing=0>";
		
		echo "<tr class=seccion1><td align=left colspan=13><b>Hora: ".$hora." ====> Cantidad de Actividades: ".$num." ====> Para actualizar la lista presione [F5] y luego [Reintentar] ó de [Click] sobre algún botón [OK]</b></td></tr>";
	    
	    $wgerencia=str_replace(" ","%",$wgerencia);
	    
	    echo "<tr class=encabezadoTabla>";
	    echo "<th colspan=2><a href='Directivo.php?fec=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a>Fecha<a href='Directivo.php?fec=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></a></th>";
	    echo "<th colspan=1>Hora</th>";
	    echo "<th colspan=2><a href='Directivo.php?act=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a>Actividad<a href='Directivo.php?act=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></a></th>";
	    echo "<th colspan=1>Asunto</th>";
	    echo "<th colspan=2><a href='Directivo.php?imp=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a>Importancia<a href='Directivo.php?imp=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></a></th>";
	    if (trim($wdato) != "" and strtoupper(trim($wdato)) != "NO APLICA")
          {
           switch ($wdato)
             {
	          case "FECHA":
	            { echo "<th><a href='Secretaria.php?adi=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'><font size=4 color=FFFF33></a>Fecha Aprox.<a href='Secretaria.php?adi=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></a></th>"; }
	            break;
	          case "OTRO":  
	            { echo "<th><a href='Secretaria.php?adi=asc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'><font size=4 color=FFFF33></a>Otro<a href='Secretaria.php?adi=desc&wgerencia=".$wgerencia."&wtip=".$wtip."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif'></a></th>"; }
	           	break;
             }  	
          }
	    echo "<th>Atendido</th>";
	    echo "<th>Realizado</th>";
	    echo "<th>Observación</th>";
	    echo "<th>Grabar</th>";
	    echo "</tr>";
	    
	    $wgerencia=str_replace("%"," ",$wgerencia);
	    
	    //Coloco una linea en blanco
	    echo "<tr class=seccion1><td colspan=13>&nbsp</td></tr>";
	    
	    for ($i=1;$i<=$num;$i++)
		   {
			$row = mysql_fetch_array($res); 
			
			 //========================================================================================
			 //========================================================================================
			 if (isset($wdato) and $wdato!="FECHA")
                {
				 //==================================================================================
				 //==================================================================================
				 //Aca calculo la diferencia entre la fecha actual y el dia que se coloco el mensaje
			     $q = "SELECT DATEDIFF('".$wfecha."','".$row[12]."') FROM agenda_000001 WHERE id=1 ";  
			     $resdias = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
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
		            
		         if ($row[8] != "00:00:00" or isset($wcumplimiento[$i]) )
		            $wcolor = "FF9966";                      //Naranjado
		            
		         //==================================================================================   
		         //==================================================================================
	            }
	           else
	              if (isset($wdato) and $wdato=="FECHA")
	                 {
		              //Aca calculo la diferencia entre la fecha actual y la fecha de entrega del mensaje
				      $q = " SELECT DATEDIFF('".$row[13]."','".$wfecha."') "
				          ."   FROM agenda_000001 "
				          ."  WHERE id=1 ";  
				      $resdias = mysql_query($q,$conex) or die ($q." - ".mysql_errno()." - ".mysql_error()); 
				      $rowdias = mysql_fetch_array($resdias);   
				      
				      if ($rowdias[0] <= 1)     //Si los dias son menores a 1, es porque el compromiso ya se vencio
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
	                        
	        $wfec=explode("-",$row[12]); //Aca separo la fecha para tomar el mes y el día
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
	           
	        echo "<INPUT TYPE='hidden' NAME='wid[".$i."]' VALUE='".$row[9]."'>";                      // Id Oculto
	           
			echo "<tr bgcolor=".$wcolor.">";
			echo "<td colspan=2><font size=2 color=".$wcolorfor.">".$wmes."</font></td>";     		 // Fecha de solicitud
		    echo "<td colspan=1><font size=2 color=".$wcolorfor.">".$row[0]."</font></td>";          // Hora de solicitud
		    echo "<td colspan=2><font size=2 color=".$wcolorfor."><b>".$row[2]."</b></font></td>";   // Actividad
	        echo "<td colspan=1><font size=2 color=".$wcolorfor."><b>".$row[3]."</b></font></td>";   // Asunto
	        echo "<td colspan=2><font size=2 color=".$wcolorfor."><b>".$row[4]."</b></font></td>";   // Importancia
	        
	        //Dato Adicional          
	        if (trim($wdato) != "" and strtoupper(trim($wdato) != "NO APLICA"))
		       if (trim($row[13]) != "")
			      echo "<td align=left bgcolor=".$wcolor.">".$row[13]."</td>";                       // Dato Adicional
			     else
			        echo "<td align=left bgcolor=".$wcolor.">&nbsp</td>";                            // Dato Adicional 
			        
			
			//======================================================================================================================================================
		    //Evaluo si el CUMPLIMIENTO - ATENDIDO ya habia sido dado, si no, evaluo si se dio dando click en checkbox, si no lo muestro desmarcado       
			if ($row[8] != "00:00:00")
			   {
		        echo "<td align=center bgcolor=".$wcolor."><font size=2 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wcumplimiento[".$i."]' CHECKED></font></td>";  
		        //$wid[$i]=$row[9];
		        //echo "<input type='HIDDEN' name=wid[".$i."] value='".$wid[$i]."'>";
	           }
		      else
		         echo "<td align=center bgcolor=".$wcolor."><font size=2 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wcumplimiento[".$i."]' ></font></td>";   
		         
		    
		    //=====================================================================================================================================================
		    //Evaluo si ya se REALIZO, si no, evaluo si se dio dando click en checkbox, si no lo muestro desmarcado
		    if ($row[7] != "00:00:00")
		       echo "<td align=center bgcolor=".$wcolor."><font size=2 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wrealizado[".$i."]' CHECKED></font></td>";
		      else
		         echo "<td align=center bgcolor=".$wcolor."><font size=2 color=".$wcolorfor."><INPUT TYPE=CHECKBOX NAME='wrealizado[".$i."]'></font></td>";  
		       
		    //=====================================================================================================================================================     
		    //Observacion Central 
		    ///if ($row[11] != "")   
		    ///   echo "<td align=left bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wobscc[".$i."]' VALUE='".$row[11]."'></td>";
		    ///  else
		    ///     {
		          //echo "<td align=left bgcolor=".$wcolor."><INPUT TYPE='text' NAME='wobscc[".$i."]'></td>";
		          echo "<td align=left bgcolor=".$wcolor."><TEXTAREA NAME='wobscc[".$i."]' rows=3 cols=30>".$row[11]."</TEXTAREA></td>";
	        ///     } 
		       
		    echo "<td align=center bgcolor=".$wcolor."></b><input type='submit' value='OK'></b></td>"; 
		    
	  	}
      }		  
    //========================================================================================================================================================
    
    
    
//==================================================================================================================================
//PROGRAMA                   : Directivo.php
//AUTOR                      : Juan Carlos Hernández M.
//$wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Mayo 9 de 2005
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Febrero 3 de 2010"; 
//DESCRIPCION
//=====================================================================================================================================\\
//Este programa muestra todos los mensajes u citas agendadas y otros items configurables, que se hagan desde el programa secretaria.php\\
//ambos programas forman la solucion de agenda o mensajeria del staff directivo.                                                       \\                                                                                                               \\
//=====================================================================================================================================\\


//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//                                                                                                                                         \\
//                                                                                                                                         \\
//_________________________________________________________________________________________________________________________________________\\
//MAYO 13 DE 2009:                                                                                                                         \\
//_________________________________________________________________________________________________________________________________________\\
//Se modifico radicalmente la operacion del programa, separando lo que es actualizacion de la presentacion, es decir, las acciones que se  \\
//realizan en pantalla, como dar atencion, realizada u observacion se evaluan primero y luego se hace un query para consultar los registros\\
//con las nuevas condiciones, luego de la actualizacion.                                                                                   \\
//                                                                                                                                         \\
//=========================================================================================================================================\\
    
    
    //=====================================================================================================================================\\
    //=====================================================================================================================================\\
    //COMIENZA EL PROGRAMA      
    echo "<form name=agenda action='directivo.php' method=post>";
    
    //=========================================================================
    //Aca viene la Dirección, Gerencia, Unidad o Servicio que utiliza la agenda
    echo "<input type='HIDDEN' name=wgerencia value='".$wgerencia."'>";
    //=========================================================================
    
    $wano=date("Y");
    $wfecha=date("Y-m-d"); 
    $hora = (string)date("H:i:s");
    
    echo "<HR align=center></hr>";
    
    
    $wordeno="off";
    
    //ESTO PARA PODER HACER EL ORDEN DE LA INFORMACION EN EL QUERY
    if (isset($fec) or isset($act) or isset($imp))
       {
	    //Ordenado por Fecha y no por Actividad ni por Importancia 
        if (isset($fec) and $fec!="")
           {
            $worden="ORDER BY 13 ".$fec.",1 desc";
            echo "<input type='HIDDEN' name= 'fec' value=".$fec.">";
           }
          else
             $fec=""; 
           
        //Ordenado por Actividad y no por Fecha ni por Importancia 
        if (isset($act) and $act!="")
           {
            $worden="ORDER BY 3 ".$act; 
            echo "<input type='HIDDEN' name= 'act' value=".$act.">";
           }
          else
             $act=""; 
          
        //Ordenado por Importancia y no por Fecha ni por Actividad 
        if (isset($imp) and $imp!="")
           {
            $worden="ORDER BY 5 ".$imp;  
            echo "<input type='HIDDEN' name= 'imp' value=".$imp.">"; 
           }
          else
             $imp="";   
        
                   
        $wordeno="on";   
       }
      else
         {
          $worden = "ORDER BY 13 desc, 1 desc "; 
          $fec="desc";
         } 
        
    
    if (!isset($worden) or $worden=="") 
       {
        $worden = "ORDER BY 13 desc, 1 desc "; 
        $fec="desc";
       } 
   
    $wgerencia=str_replace("%"," ",$wgerencia);   
       
    //===================================================================================================================================================
    //Refresh es el tiempo en que se demora en refrescar la pantalla
    //===================================================================================================================================================
    $wrefresh = "'600";  //DIEZ MINUTOS
    $wrefres  = 600;
    //===================================================================================================================================================
    
    encabezado("AGENDA DIRECTIVA - ".$wgerencia,$wactualiz, "clinica");
    
    
    if (!isset($wtip)) $wtip="";
    
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //ACA TRAIGO LOS TIPOS DE ACTIVIDADES
    if (!isset($wtip))
       {
	    $q =  " SELECT Descripcion "
		     ."   FROM agenda_000005 "
			 ."  WHERE Estado = 'on' "
			 ."  ORDER BY Codigo ";
       }      
	  else
	     {
	 	  $q =  " SELECT Descripcion "
	           ."   FROM agenda_000005 "
	           ."  WHERE Descripcion != '".$wtip."'"
	           ."    AND Estado = 'on' "
		       ."  ORDER BY Codigo ";     
         }     
    $res = mysql_query($q,$conex);
    $num = mysql_num_rows($res);
	
    
    convencion();
    
    echo "<br><br><br>";
    
    echo "<center><table border='6'>";
    //TIPO DE LA ACTIVIDAD
	echo "<tr class=seccion1><td align=center colspan=11>SELECCIONE <b>*** LA AGENDA ***</b> A CONSULTAR: </td>";
    echo "<td align=center colspan=11><select name='wtip' onchange='enter()'>";
    
    if (isset($wtip) and trim($wtip) != "")
       echo "<option>".$wtip."</option>";    
           
	for ($i=1;$i<=$num;$i++)
	    {
		 $row = mysql_fetch_array($res); 
		 
		 if ($i==1 and (!isset($wtip) or trim($wtip)==""))
		    $wtip=$row[0];
         echo "<option>".$row[0]."</option>";
        }
	echo "</select></td></tr>";
	echo "</table>";
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
	//ACA TRAIGO EL DATO ADICIONAL SEGUN EL TIPO DE AGENDA   
    $q =  " SELECT Dato, Codigo "
         ."   FROM agenda_000005 "
         ."  WHERE Descripcion = '".$wtip."'";
    $res = mysql_query($q,$conex);
    $row = mysql_fetch_array($res); 
    
    $wdato=$row[0];   //Dato adicional que se pide o no, segun el tipo de Agenda   
	$wcodtip=$row[1]; 
	
	if (isset($wtip))
	   {
		//============================================================================================================================================
		//Aca vuelvo a hacer el query para traer la informacion con las nuevas condiciones que se dieron en pantalla.
		//============================================================================================================================================
		$q = "  SELECT Hora_data, Origen, Motivo, Observacion, Destino, Solicito, Camillero, Hora_llegada, "
	        ."         Hora_Cumplimiento, Id, Habitacion, Observ_central, Fecha_data, Dato "
		    ."    FROM agenda_000003 "
		    ."   WHERE Anulada                   = 'No' "                           
		    ."     AND agenda_000003.fecha_data >= str_to_date(ADDDATE('".$wfecha."',-360),'%Y-%m-%d')"    //Muestra los mensajes de 4 meses hasta hoy
		    ."     AND Origen                    = '".$wgerencia."'"
		    ."     AND Tipo                      = '".$wcodtip."' "
		    ."     AND Hora_llegada              = '00:00:00' ".$worden; 
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);
        
		
		for ($i=1;$i<=$num;$i++)
		   {
		    $row = mysql_fetch_array($res);      
            //=======================================================================================================================================
		    //Evaluo si el CUMPLIMIENTO - ATENDIDO ya habia sido dado, si no, evaluo si se dio dando click en checkbox, si no lo muestro desmarcado
		    if (isset($wcumplimiento[$i]) and trim($wcumplimiento[$i])!="" and $wid[$i]==$row[9])  //Chulio en pantalla atendido
	            {
                 $q = "   UPDATE agenda_000003 "
		             ."      SET Hora_cumplimiento = '".$hora."'"
		             ."    WHERE Id   = ".$row[9]
		             ."      AND Tipo = '".$wcodtip."' ";
		         $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
		         
		         //echo "<meta http-equiv='refresh' content='0;url=directivo.php?wgerencia=".$wgerencia."&wtip=".$wtip."&fec=".$fec."&act=".$act."'>";
		        } 
	        
		              
		    //=======================================================================================================================================
		    //Evaluo si ya se REALIZO, si no, evaluo si se dio dando click en checkbox, si no lo muestro desmarcado
	        if (isset($wrealizado[$i]) and trim($wcumplimiento[$i]) and $wid[$i]==$row[9])
	            {
		         $q = "   UPDATE agenda_000003 "
		             ."      SET Hora_llegada = '".$hora."'"
		             ."    WHERE Id   = ".$row[9]
		             ."      AND Tipo = '".$wcodtip."' ";;
		         $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
		        } 
	        
		             
	        //===========================================================================================================================
	        //Observaciones del Directivo
	        
	        if (isset($wobscc[$i]) and $wid[$i]==$row[9])
	           {
		        if ($wobscc[$i] != "")
		           {
		            $q = "   UPDATE agenda_000003 "
			            ."      SET Observ_central = '".$wobscc[$i]."'"
			            ."    WHERE Id   = ".$row[9]
			            ."      AND Tipo = '".$wcodtip."' ";
			        $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());   
				   }
			   }
	        echo "</tr>";
	       } //FIN DEL FOR
       
	       mostrar();    //Aca hago la presentacion de la Agenda
	       
       } //Fin del if (isset(wtip))
   echo "</BIG>";
   echo "</center></table>";
   
   
   
   echo "<HR align=center></hr>";  //Linea horizontal
   
   if (isset($wdato) and $wdato !="FECHA")
	  {
	   convencion();
      } 
	 else
	   if (isset($wdato) and $wdato=="FECHA")
	     {
	      echo "<table border=6 align=right>";
		  echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
		  echo "<tr><td colspan=3 bgcolor="."FFFFCC"."><font size=2 color='"."000000"."'>&nbsp Pendiente</font></td></tr>";                //Amarillo 
		  echo "<tr><td colspan=3 bgcolor="."66CC66"."><font size=2 color='"."000000"."'>&nbsp Tarea Vencida</font></td></tr>";            //Verde
		  echo "</table>";
	     }
	       
   echo "</form>";
   
   if (!isset($fec)) $fec=""; 
   if (!isset($act)) $act="";
   if (!isset($imp)) $imp="";
   
   $wgerencia=str_replace(" ","%",$wgerencia);
   echo "<input type='HIDDEN' name=wgerencia value='".$wgerencia."'>";
   echo "<input type='HIDDEN' name=wtip value='".$wtip."'>";
      
   echo "<br><br>";
   echo "<tr><td align=center colspan=15><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
   
   $wgerencia=str_replace("%"," ",$wgerencia);
}
include_once("free.php");
?>