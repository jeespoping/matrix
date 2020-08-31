<html>
<head>
  <title>MONITOR KARDEX</title>
</head>
<script type="text/javascript">
	function enter()
	{
	 document.forms.monkardex.submit();
	}
	
	function cerrarVentana()
	 {
      window.close();		  
     }
</script>	
<?php
  /***********************************************
   *             MONITOR DEL KARDEX              *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
session_start();

if (!isset($user))
	if(!session_is_registered("user"))
	  session_register("user");
	
if(!session_is_registered("user"))
	echo "error";
else
{	            
  include("root/magenta.php");
  include("root/comun.php");
  
  $conex = obtenerConexionBD("matrix");
  
                                                     // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Agosto 9 de 2011)";                  // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                               // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION  Junio 30 de 2009                                                                                                            \\
//=========================================================================================================================================\\
//Este programa muestra la situación en linea de todo lo que ocurre con el Kardex en la clinica.                                           \\
//muestra que Kardex no se han generado, que kardex han cambiado luego de haberse dispensado y que Kardex estan pedientes de dispensar.    \\
//                                                                                                                                         \\
//=========================================================================================================================================\\	                                                         
	                                                           
	                                                             
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//Enero 7 de 2011                                                                                                                          \\
//=========================================================================================================================================\\
//Se crea el Monitor de CTC's, para ver que pacientes se pasaron de lo autorizado o como va el consumo de lo autorizado                    \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//Agosto 5 de 2010                                                                                                                         \\
//=========================================================================================================================================\\
//Se adicionan los monitores 6 y 7. 6: Monitor de Antibioticos sin confirmar y 7: Perfiles sin aporbar para dispensacion                   \\
//Se modifico el monitor 2, antes era kardex sin dispensar, paso a ser Perfil aprobado SIN dispensar                                       \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//Junio 17 de 2010                                                                                                                         \\
//=========================================================================================================================================\\
//Se modifica la opcion 3 para solo tenga en cuenta los Kardex no generados con articulos de dispensación.                                 \\
//=========================================================================================================================================\\
//Mayo 27 de 2010                                                                                                                          \\
//=========================================================================================================================================\\
//Se adiciona una opcion para las historias que tengan articulos del lactario en el Kardex, se define como la opcion=5                     \\
//                                                                                                                                         \\
//=========================================================================================================================================\\

	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  //echo "<br>";				
  //echo "<br>";
      		
  
  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
  $q = " SELECT detapl, detval, empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' "
      ."    AND empcod = detemp "; 
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res); 
  
  if ($num > 0 )
     {
	  for ($i=1;$i<=$num;$i++)
	     {   
	      $row = mysql_fetch_array($res);
	      
	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];
	         
	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];
	         
	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];
	         
	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
	         
	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];   
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  

  /*     
  //=====================================================================================================================================================================     
  // F U N C I O N E S
  //=====================================================================================================================================================================
  function comparacion($vec1,$vec2)
    {
	 if($vec1[6] > $vec2[6])
	    return -1;
	   elseif ($vec1[6] < $vec2[6])
	          return 1;
	         else
	           return 0;
    }
  */
  
  //Se consigue la hora PAR anterior a la hora actual(si es hora impar) si no se deja la hora PAR actual
  function hora_par()
    {
	 global $whora_par_actual;
	 global $whora_par_anterior;
	 global $wfecha;
	 global $wfecha_actual;
	    
	 
	 $whora_Actual=date("H");
	 $whora_Act=($whora_Actual/2);
	 
	 $wfecha_actual=date("Y-m-d");
	 
	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
		 $whora_par_actual=$whora_Actual-1;
	     if ($whora_par_actual=="00" or $whora_par_actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
	    } 
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
		    else
		       $whora_par_actual=$whora_Actual; 
	     }
	     
	  if ($whora_Actual=="02" or $whora_Actual=="2")        //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	     $whora_par_anterior="24";
	    else
	      {
		   if (($whora_par_actual-2) == "00")               //Abril 12 de 2011
		      $whora_par_anterior="24";
		     else
	            $whora_par_anterior = $whora_par_actual-2;
		  }
	      
	  if (strlen($whora_par_anterior) == 1)
	     $whora_par_anterior="0".$whora_par_anterior;
	     
	  if (strlen($whora_par_actual) == 1)
	     $whora_par_actual="0".$whora_par_actual;  
    }
  
  
  function ctc($res)
    {
	 global $wbasedato;
	 global $conex;
	 global $wemp_pmla;
	 global $wfecha;
	 
	 $wnum = mysql_num_rows($res);
	 
	 $wfila = mysql_fetch_array($res);
	 
	 echo "<table>";
	 
	 $i=1;
	 while ($i<=$wnum)
	    {
		 $wauxcco = $wfila[0];
		 
		 echo "<tr class='tituloPagina'>";
		 echo "<td colspan=8>".$wfila[0]." : ".$wfila[1]."</td>";                  //Centro de Costo
		 echo "</tr>";
		 
		 while ($i <= $wnum and $wauxcco == $wfila[0])
		   {
			$wauxhis = $wfila[3]."-".$wfila[4];
			
			//==========================================================================================================
			//Traigo los datos demograficos del paciente
			//==========================================================================================================
            $q = " SELECT pacno1, pacno2, pacap1, pacap2, pacced, pactid "
                ."   FROM root_000036, root_000037 "
                ."  WHERE orihis = '".$wfila[3]."'"
		        ."    AND oriing = '".$wfila[4]."'"
		        ."    AND oriori = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
		        ."    AND oriced = pacced "; 
            $respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $rowpac = mysql_fetch_array($respac); 
               
		    $wpac = $rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3];    //Nombre
		    $wdpa = $rowpac[4];                                                 //Documento del Paciente
	        $wtid = $rowpac[5]; 
			//==========================================================================================================
			
			
	        //==========================================================================================================
	        //Busco si tiene el KARDEX actualizado a la fecha
	        //==========================================================================================================
	        $q = " SELECT COUNT(*) "
	            ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000011 B"
	            ."  WHERE karhis       = '".$wfila[3]."'"
	            ."    AND karing       = '".$wfila[4]."'"
	            ."    AND A.fecha_data = '".$wfecha."'"
	            ."    AND karcon       = 'on' "
	            ."    AND ((karcco     = ccocod "
	            ."    AND  ccolac     != 'on') "
	            ."     OR   karcco     = '*' ) ";
	        $reskar = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $rowkar = mysql_fetch_array($reskar); 
            
            if ($rowkar[0] > 0)
               $wkardexActualizado="Actualizado";
              else
                 $wkardexActualizado="Sin Actualizar";
            //==========================================================================================================
            
            
            echo "<tr class='encabezadoTabla'>";
			echo "<td align=center class='fondoAmarillo'>Habitación<br>".$wfila[2]."</td>";        //Habitación
		    echo "<td align=center>Historia<br>".$wfila[3]."-".$wfila[4]."</td>";                  //Historia - Ingreso
		    echo "<td align=center colspan=1>Paciente<br>".$wpac."</td>";                          //Nombre paciente
		    echo "<td align=center>Documento<br>".$wdpa."</td>";                                   //Dcto Identificacion
		    echo "<td align=center colspan=3>Responsable<br>".$wfila[7]."-".$wfila[8]."</td>";     //Responsable
		    echo "<td align=center class='fondoAmarillo'>Kardex<br>".$wkardexActualizado."</td>";  //Estado del Kardex
		    echo "</tr>";
			   
		    echo "<tr class='fila1'>";
		    echo "<td><b>Código</b></td>";
		    echo "<td><b>Descripción</b></td>";
		    //echo "<td align=center><b>Envia</b></td>";
		    echo "<td align=center><b>Unidad</b></td>";
		    echo "<td align=center><b>Fecha CTC</b></td>";
		    echo "<td align=center><b>Cantidad<br>Autorizada</b></td>";
		    echo "<td align=center><b>Cantidad<br>Consumida</b></td>";
		    echo "<td align=center><b>Cantidad<br>Restante</b></td>";
		    echo "<td align=center><b>Cantidad<br>Dispensada</b></td>";
		    echo "</tr>";
		    
			while ($i <= $wnum and $wauxcco == $wfila[0] and $wauxhis == $wfila[3]."-".$wfila[4])
			  {
			   if ($i%2==0)
			      $wclass="fila1";
			     else
			       $wclass="fila2";
			   $wcolorAlerta="";     
			       
			       
			       
			   //==========================================================================================================
	           //Traigo la Cantidad Dispensada Real
	           //==========================================================================================================
	           $q = " CREATE TEMPORARY TABLE if not exists TEMPO1 "
		           ." SELECT spauen AS cant "
		           ."   FROM ".$wbasedato."_000004 "
		           ."  WHERE spahis = '".$wfila[3]."'"
		           ."    AND spaing = '".$wfila[4]."'"
		           ."    AND spaart = '".$wfila[5]."'"
		           ."  UNION "
	               ." SELECT spluen AS cant "
	               ."   FROM ".$wbasedato."_000030 "
		           ."  WHERE splhis = '".$wfila[3]."'"
		           ."    AND spling = '".$wfila[4]."'"
		           ."    AND splart = '".$wfila[5]."'";
		       $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		        
		       $q = " SELECT SUM(cant) "
			       ."   FROM TEMPO1 ";
			   $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	           $rowdes = mysql_fetch_array($resdes); 
	            
	           if ($rowdes[0] > 0)
	              $wcantidadDispensada=$rowdes[0];
	             else 
	                $wcantidadDispensada=0;
	                
	           $q = " DELETE FROM TEMPO1 ";
	           $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	           //==========================================================================================================    
			               
	           
			   echo "<tr class=".$wclass.">";
		       echo "<td>".$wfila[5]."</td>";                                      //Codigo articulo
		       echo "<td>".$wfila[6]."</td>";                                      //Nombre articulo
		       
		       /*
		       //Evaluo si se envia o no
		       if ($wfila[9] == "on")
		          $wenvia="No Enviar";
		         else
		            $wenvia="Enviar"; 
		       echo "<td align=center>".$wenvia."</td>";                           //Envia 
		       */
		       
			   			   
			   //BUSCO SI LA HISTORIA CON EL ARTICULO TIENE CTC
			   $q = " SELECT fecha_data, ctccau, ctccus, ctcuca "                  //cau: Cantidad Autorizada, cus: Cantidad Usuada, uca: Unidad de medida
			       ."   FROM ".$wbasedato."_000095 "
			       ."  WHERE ctchis = '".$wfila[3]."'"                             //Historia
			       ."    AND ctcing = '".$wfila[4]."'"                             //Ingreso
			       ."    AND ctcart = '".$wfila[5]."'";                            //Código Articulo
			   $resctc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	           $wnumctc = mysql_num_rows($resctc);
	           
	           if ($wnumctc > 0)
	              {
		           $rowctc = mysql_fetch_array($resctc);
		           
		           echo "<td align=center>".$rowctc[3]."</td>";                    //Unidad de Medida   
		           echo "<td align=center>".$rowctc[0]."</td>";                    //Fecha de Autorizacion  
		           echo "<td align=center>".$rowctc[1]."</td>";                    //Cantidad Autorizada  
		           echo "<td align=center>".$rowctc[2]."</td>";                    //Cantidad Consumida 
		           echo "<td align=center>".($rowctc[1]-$rowctc[2])."</td>";       //Cantidad Restante
		           
		           if ($wcantidadDispensada > $rowctc[1])
			          $wcolorAlerta="fondoRojo";
			         else
			           $wcolorAlerta=""; 
		          }      
		         else
		           {
			        echo "<td align=center colspan=5><b>Sin CTC</b></td>";
			       }   
			   
			   echo "<td align=center class='".$wcolorAlerta."'>".$wcantidadDispensada."</td>";              //Cantidad Dispensada    
		           
		       $wfila = mysql_fetch_array($res);
		       $i++;  
		       echo "</tr>";  
			  }
		   }
		}    
	 
	 echo "</table>";
	 
	 // ccocod, cconom, ubihac, ubihis, ubiing, kadart, artnom, Ingres, Ingnre
	  
	    
    }
    
        
 
  function estado_del_Kardex($whis, $wing, &$westado, $wmuerte, &$wcolor, &$wactual)
    {
	  global $wbasedato;
	  global $conex;
	  global $wespera;
	  global $wfecha;
	  global $waltadm;
	  global $wopcion;
	  
	  $westado="off";  //Apago el estado, que indica segun la opcion si la historia si esta en el estado que indica la opcion.
	  $wactual="Sin Kardex Hoy";  //Indica si el Kardex esta actualizado a la fecha, off = actualizado al día anterior, on= Actualizado a la fecha
	  
	  
	  switch ($wopcion)   
        {
	     case ("1"):
	         {
              //========================================================================================================================================================================
			  //Dispensado modificado
			  //========================================================================================================================================================================
			  //Aca muestra todas las historias que ya fueron dispensadas, pero que tuvieron alguna modificación en sus articulos
			  //luego de la dispensación.
			  $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000054 DetKar, ".$wbasedato."_000055 AudKar, ".$wbasedato."_000053 A"
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadhdi       != '00:00:00' "         //Esto me indica que ya fue dispensado
		          ."   AND kadfec        = AudKar.fecha_data "  //Esto me valida que el Kardex sea del día en la tabla de Auditoria
		          ."   AND kadhdi        < AudKar.hora_data "   //Esto me indica que el Kardex tienen alguna modificación despues de dispensado
		          ."   AND kadhis        = kauhis "
		          ."   AND kading        = kauing "
		          ."   AND kadsus       != 'on' "
		          ."   AND kadhdi        > '00:00:00' "
		          ."   AND kadcdi-kaddis > 0 "
		          ."   AND kadori        = 'SF' " 
		          //."   AND kadcon        = 'on' " 
		          ."   AND kaumen     LIKE 'Articulo%' "        //Me indica si la modiifcación se hizo en la pestaña de medicamentos.
		          ."   AND Karcon        = 'on' "
		          ."   AND karhis        = Kadhis "
		          ."   AND karing        = Kading "
		          ."   AND Karcco        = Kadcco "
		          ."   AND A.Fecha_data  = kadfec "
		          ."   AND kadare        = 'on' ";              //Articulo aprobado para dispensar
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1); 
		      $wcan = mysql_fetch_array($res1);
		      
		      if ($wcan[0] > 0)         //Si hay registros es porque ya el facturador hizo el chequeo para facturar
		         {
			      $westado="on";        //Indica que la historia si esta Dispensada pero con el Kardex modificado, es decir en 'on'
			      $wactual="Actualizado";        //Indica que esta actualizado a la fecha
			     }
		      break;   
	         }     
	  		  
           
	      case ("2"):
	         {
			  //========================================================================================================================================================================
			  //Perfil Aprobado sin Dispensar parcial o totalmente
			  //========================================================================================================================================================================
			  //Aca muestra todas las historias que no se han dispensado nada
			  $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B" 
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus       != 'on' "
		          ."   AND kadori        = 'SF' "
		          ."   AND kadcdi-kaddis > 0 "
		          //."   AND kadcon        = 'on' "                 //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND Karcon        = 'on' "
		          ."   AND karhis        = Kadhis "
		          ."   AND karing        = Kading "
		          ."   AND Karcco        = Kadcco "
		          ."   AND B.Fecha_data  = kadfec "
		          ."   AND kadare        = 'on' ";
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      $wcan = mysql_fetch_array($res1);
		      
		      if ($wcan[0] > 0)         //Si hay registros es porque ya el facturador hizo el chequeo para facturar
		         {
			      $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
			      $wactual="Actualizado";        //Indica que esta actualizado a la fecha
			     } 
	          break;   
	         }

	         
		  case ("3"):
		     {
			  //========================================================================================================================================================================
			  //Pacientes sin Kardex
			  //========================================================================================================================================================================
			  //Aca muestra todas las historias que no tienen Kardex
			  $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054, ".$wbasedato."_000011 "
		          ." WHERE karhis            = '".$whis."'"
		          ."   AND karing            = '".$wing."'"
		          ."   AND EncKar.fecha_data = '".$wfecha."'"     //Esto me valida que el Kardex sea del día
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND Enckar.fecha_data = kadfec "
		          ."   AND ((kadcco          = ccocod "
		          ."   AND  ccolac          != 'on') "
		          ."    OR kadcco            = '*') "
		          ."   AND kadori            = 'SF' ";
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1); 
		      
		      $wcan=mysql_fetch_array($res1);
			  
		      if ($wcan[0] > 0)   //Indica que existe kardex pero puede que no este confirmado entonces verifico esa condicion
		         {
			      $q=  " SELECT COUNT(*) "
			          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054, ".$wbasedato."_000011 "
			          ." WHERE karhis            = '".$whis."'"
			          ."   AND karing            = '".$wing."'"
			          ."   AND EncKar.fecha_data = '".$wfecha."'"     //Esto me valida que el Kardex sea del día
			          ."   AND karhis            = kadhis "
			          ."   AND karing            = kading "
			          ."   AND Enckar.fecha_data = kadfec "
			          ."   AND ((kadcco          = ccocod "
			          ."   AND  ccolac          != 'on') "
			          ."    OR kadcco            = '*') "
			          ."   AND kadori            = 'SF' "
			          ."   AND karcon            = 'off' ";
			      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      $wnum = mysql_num_rows($res1);    
			      
			      $row=mysql_fetch_array($res1); 
			      
			      if ($row[0] > 0)   //indica que esta creado el kardex pero sin confirmar por el estado debe ser 'on' para que salga esta historia en la lista
			         $wcan[0]=0;
		         }    
		      
		      if ($wcan[0] == 0)     //Si hay registros es porque ya el facturador hizo el chequeo para facturar
		         {
			      $westado="on";     //Indica que la historia si esta sin generar el Kardex, es decir en 'off'  
			      $wactual="Actualizado";     //Indica que esta actualizado a la fecha
			     } 
			    else
			        $westado="off";
			        
			  break;   
	         }
	       
	       case ("4"):
		     {
			  //========================================================================================================================================================================
			  //Pacientes con Kardex y con Antibioticos Confirmados
			  //========================================================================================================================================================================
			  /*
			  $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054 DetKar " //, ".$wbasedato."_000055 AudKar"
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND kadcon            = 'on' "
		          ."   AND kadori            = 'CM' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec ";
		          //."   AND kadare            = 'on' ";
		      */    
		      $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054 DetKar "
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND kadcon            = 'on' "               //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori            = 'CM' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec "
		          ." UNION "
			      ."SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054 DetKar, ".$wbasedato."_000059 deffra"
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND kadcon            = 'on' "               //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori            = 'SF' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec "
		          ."   AND kadart            = defart "
		          ."   AND defcon            = 'on' ";
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      $wcan = mysql_fetch_array($res1);
		      
		      if ($wcan[0] > 0)         
		         {
			      $westado="on";
			      $wactual="Actualizado";        //Indica que esta actualizado a la fecha
			     } 
			    else
			       $westado="off";
	           break;   
	         }   
	         
	         
	       case ("5"):
		     {
			  //========================================================================================================================================================================
			  //Pacientes con Kardex con Articulos del Lactario
			  //========================================================================================================================================================================
			  $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000054, ".$wbasedato."_000053 A, ".$wbasedato."_000011, ".$wbasedato."_000026 "
		          ." WHERE karhis         = '".$whis."'"
		          ."   AND karing         = '".$wing."'"
		          ."   AND karhis         = kadhis "
		          ."   AND karing         = kading "
		          ."   AND kadfec         = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus        != 'on' "
		          ."   AND kadcdi-kaddis  > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND kadart         = artcod "
		          ."   AND INSTR(ccogka, mid(artgru,1,INSTR(artgru,'-')-1)) "
		          ."   AND ccolac         = 'on' "
		          ."   AND karcon         = 'on' "
		          ."   AND A.Fecha_data   = kadfec ";
		          //."   AND kadare         = 'on' "; 
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      $wcan = mysql_fetch_array($res1);
		      
		      if ($wcan[0] > 0)         
		         {
			      $westado="on";
			      $wactual="Actualizado";        //Indica que esta actualizado a la fecha
			     } 
			    else
			       $westado="off";
	           break;   
	         } 
	         
	       case ("8"):  //Con esto lo que hace es ejecutar el sigte case
	       case ("6"):
		     {
			  //========================================================================================================================================================================
			  //Pacientes con Antibioticos SIN Confirmar
			  //========================================================================================================================================================================   
			  $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054 DetKar "
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND kadcon           != 'on' "               //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori            = 'CM' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec "
		          ." UNION "
			      ."SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000053 EncKar, ".$wbasedato."_000054 DetKar, ".$wbasedato."_000059 deffra"
		          ." WHERE kadhis            = '".$whis."'"
		          ."   AND kading            = '".$wing."'"
		          ."   AND kadfec            = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus           != 'on' "
		          ."   AND kadcdi-kaddis     > 0    "               //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND kadcon           != 'on' "               //Indica que NO sea confirmado el ANTIBIOTICO
		          ."   AND kadori            = 'SF' "               //Solo de Central de Mezclas
		          ."   AND karhis            = kadhis "
		          ."   AND karing            = kading "
		          ."   AND karcco            = kadcco "
		          ."   AND karcon            = 'on' "
		          ."   AND EncKar.Fecha_data = kadfec "
		          ."   AND kadart            = defart "
		          ."   AND defcon            = 'on' ";
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      $wcan = mysql_fetch_array($res1);
		      
		      if ($wcan[0] > 0)         
		         {
			      $westado="on";
			      $wactual="Actualizado";        //Indica que esta actualizado a la fecha
			     } 
			    else
			       $westado="off";
	           break;   
	         }
	         
	       case ("7"):
	         {
			  //========================================================================================================================================================================
			  //Perfil con Articulo(s) Sin Aprobar
			  //========================================================================================================================================================================
			  //Aca muestra todas las historias que no se han dispensado nada
			  $q= " SELECT COUNT(*) "
		          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B" 
		          ." WHERE kadhis        = '".$whis."'"
		          ."   AND kading        = '".$wing."'"
		          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
		          ."   AND kadsus       != 'on' "
		          ."   AND kadori        = 'SF' "
		          ."   AND kadcdi-kaddis > 0 "
		          //."   AND kadcon        = 'on' "                 //Esto me indica que ya falta por dispensar parcial o totalmente
		          ."   AND Karcon        = 'on' "
		          ."   AND karhis        = Kadhis "
		          ."   AND karing        = Kading "
		          ."   AND Karcco        = Kadcco "
		          ."   AND B.Fecha_data  = kadfec "
		          ."   AND kadare       != 'on' ";
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
		      $wcan = mysql_fetch_array($res1);
		      
		      if ($wcan[0] > 0)         //Si hay registros es porque ya el facturador hizo el chequeo para facturar
		         {
			      $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
			      $wactual="Actualizado";        //Indica que esta actualizado a la fecha
			     } 
	          break;   
	         }   
	    }//Aca termina el switch	         
		         	      
      if ($wmuerte=="on")
         $westado="Falleció";     
	}
  	
	
  //=====================================================================================================================================================================	
  //**************************** Aca termina la funcion estado_del_kardex *******************
  
  
  
  
  //Dependiendo de la opcion enviada en los parametros del programa, definido en la tabla root_000021, se coloca el TITULO en la pantalla
  switch ($wopcion)
    {
	 case "0":
	    $wtitulo = "MONITOR DEL KARDEX Y PERFIL FARMACOTERAPEUTICO";
	    break;
	 case "1":
	    $wtitulo = "KARDEX ** DISPENSANDO Y LUEGO MODIFICADO ** ";
	    break;    
	 case "2":
	    $wtitulo = "PERFIL con Articulos ** APROBADOS SIN DISPENSAR ** PARCIAL O TOTALMENTE";
	    break;
	 case "3":
	    $wtitulo = "PACIENTES ** SIN GENERAR ** KARDEX";
	    break;
	 case "4":
	    $wtitulo = "KARDEX CON ANTIBIOTICOS CONFIRMADOS";
	    break;
	 case "5":
	    $wtitulo = "KARDEX CON ARTICULOS DEL LACTARIO";
	    break;
	 case "6":
	    $wtitulo = "KARDEX CON ANTIBIOTICOS ** SIN CONFIRMAR **";
	    break;
	 case "7":
	    $wtitulo = "PERFIL CON Articulos ** SIN APROBAR ** ";
	    break; 
	 case "8":
	    $wtitulo = "KARDEX CON ANTIBIOTICOS ** SIN CONFIRMAR **";   //Esta opcion es la misma que la 6, pero hubo que crearla para el monitor de Enfermeria
	    break;     
	 case "9":
	    $wtitulo = "KARDEX CON ARTICULOS ** NO POS ** "; 
	    break;                  
    }    
  
  if ($wopcion=="0")
     encabezado($wtitulo, $wactualiz, 'clinica');
    else
       { 
        echo "<center><table>";
		//echo "<p class='fila1' align=center><b><font size=3>".$wtitulo."</font></b></p>";
		echo "<tr class='fila1' align=center><td><b><font size=3>".$wtitulo."</font></b></td></tr>";
		echo "</table>";
      }
  
  if ($wopcion != "0")
     {   
	  //FORMA ================================================================
	  echo "<form name='monkardex' action='Monitor_Kardex.php' method=post>";
  
  
	  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	  echo "<input type='HIDDEN' name='wcencam' value='".$wcencam."'>";
	  
	  echo "<center><table>";
	  
	        
	  if (strpos($user,"-") > 0)
	     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

	  $usuario = consultarUsuario($conex,$wusuario);   
	  $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
	  
	  //traer Centro de costo desde root_000025
	  $q = " SELECT cc "
	      ."   FROM root_000025 "
	      ."  WHERE Empleado = '".$wusuario."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  if ($num > 0)
	     {
	      $row = mysql_fetch_array($res);   
	      $wccousu = $row[0];
	     }
	    else
	       {
		    echo "<script type='text/javascript'>"; 
	        echo "alert ('El usuario no existe en la tabla, root_000025 favor comunicarse con Soporte de Sistemas');";
	        echo "</script>";
	       }  
	  
	       
	  //===============================================================================================================================================
	  //ACA COMIENZA EL MAIN DEL PROGRAMA   
	  //===============================================================================================================================================
	  switch ($wopcion)
	    {
		 case "9":    //CTC's
		    {
			  //Aca trae todos los pacientes que esten hospitalizados en la clinica y luego busco como es el estado en cuanto a Kardex de cada uno
			  
			  $dia = time()-(1*24*60*60);   //Te resta un dia
	          $wayer = date('Y-m-d', $dia); //Formatea dia 
			  
			  $q = " CREATE TEMPORARY TABLE if not exists TEMPO "
			      ." SELECT ccocod, cconom, ubihac, ubihis, ubiing, kadart, artcom, Ingres, Ingnre "
			      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000016, ".$wbasedato."_000054, ".$wbasedato."_000026 "
			      ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
			      ."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
			      ."    AND ubisac  = ccocod "
			      ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
			      ."    AND ccourg != 'on' "             //Que no se de Urgencias
			      ."    AND ccocir != 'on' "             //Que no se de Cirugia
			      ."    AND ubihis  = habhis "
			      ."    AND ubiing  = habing "
			      ."    AND ubihis != '' "
			      ."    AND ubihis  = inghis "
			      ."    AND ubiing  = inging "
			      ."    AND ingtip != '02' "             // 02 = Particular
			      ."    AND ubihis  = kadhis "
			      ."    AND ubiing  = kading "
			      ."    AND kadfec  = '".$wfecha."'"     //HOY
			      ."    AND kadart  = artcod "
			      ."    AND artpos  = 'N' "
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
			      
	              ."  UNION "
	              
	              ." SELECT ccocod, cconom, ubihac, ubihis, ubiing, kadart, artcom, Ingres, Ingnre "
			      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000016, ".$wbasedato."_000054, ".$wbasedato."_000026 "
			      ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
			      ."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
			      ."    AND ubisac  = ccocod "
			      ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
			      ."    AND ccourg != 'on' "             //Que no se de Urgencias
			      ."    AND ccocir != 'on' "             //Que no se de Cirugia
			      ."    AND ubihis  = habhis "
			      ."    AND ubiing  = habing "
			      ."    AND ubihis != '' "
			      ."    AND ubihis  = inghis "
			      ."    AND ubiing  = inging "
			      ."    AND ingtip != '02' "             // 02 = Particular
			      ."    AND ubihis  = kadhis "
			      ."    AND ubiing  = kading "
			      ."    AND kadfec  = '".$wayer."'"      //AYER
			      ."    AND kadart  = artcod "
			      ."    AND artpos  = 'N' "
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9 "; 
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  
			  
			  $q = " SELECT ccocod, cconom, ubihac, ubihis, ubiing, kadart, artcom, Ingres, Ingnre "
			      ."   FROM TEMPO "
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
			      ."  ORDER BY 1,3,6 ";
			   
			  break;
		 	}
		 default:
		    {
			  hora_par();
			  /*
			  //Aca trae todos los pacientes que esten hospitalizados en la clinica y luego busco como es el estado en cuanto a Kardex de cada uno
			  $q = " SELECT ubihac, ubihis, ubiing, ".$wbasedato."_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr  "
			      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000017 "
			      ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
			      ."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
			      ."    AND ubisac  = ccocod "
			      ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
			      ."    AND ccourg != 'on' "             //Que no se de Urgencias
			      ."    AND ccocir != 'on' "             //Que no se de Cirugia
			      ."    AND ubihis  = habhis "
			      ."    AND ubiing  = habing "
			      ."    AND ubihis != '' "
			      ."    AND ubihis  = eyrhis "            //Estas cuatro linea que siguen son temporales
			      ."    AND ubiing  = eyring "             
			      ."    AND eyrsde  = ccocod "
			      ."    AND eyrtip  = 'Recibo' "
			      ."    AND eyrest  = 'on' "
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9,10 ";
			  */
			
			  //Aca trae todos los pacientes que esten hospitalizados en la clinica y luego busco como es el estado en cuanto a Kardex de cada uno
			  $q = " SELECT ubihac, ubihis, ubiing, ".$wbasedato."_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr  "
			      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000017 "
			      ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
			      ."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
			      ."    AND ubisac  = ccocod "
			      ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
			      ."    AND ccourg != 'on' "             //Que no se de Urgencias
			      ."    AND ccocir != 'on' "             //Que no se de Cirugia
			      ."    AND ubihis  = habhis "
			      ."    AND ubiing  = habing "
			      ."    AND ubihis != '' "
			      ."    AND ubihis  = eyrhis "            //Estas cuatro linea que siguen son temporales
			      ."    AND ubiing  = eyring "             
			      ."    AND eyrsde  = ccocod "
			      ."    AND eyrtip  = 'Recibo' "
			      ."    AND eyrest  = 'on' "
				  ."    AND ccocpx != 'on' "              //CICLOS de Producción 'OFF'
				  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10 "
				  ."  UNION ALL "
				  //Solo traigo los pacientes que esten en un servicio que sea con el MODELO DE CICLOS DE PRODUCCION Y DISPENSACION FRECUENTE
				  ." SELECT ubihac, ubihis, ubiing, ".$wbasedato."_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr  "
			      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000017, ".$wbasedato."_000054, "
				  ."        ".$wbasedato."_000043, ".$wbasedato."_000099 "
			      ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
			      ."    AND ubialp != 'on' "             //Que no este en Alta en Proceso
			      ."    AND ubisac  = ccocod "
			      ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
			      ."    AND ccourg != 'on' "             //Que no se de Urgencias
			      ."    AND ccocir != 'on' "             //Que no se de Cirugia
			      ."    AND ubihis  = habhis "
			      ."    AND ubiing  = habing "
			      ."    AND ubihis != '' "
			      ."    AND ubihis  = eyrhis "            //Estas cuatro linea que siguen son temporales
			      ."    AND ubiing  = eyring "             
			      ."    AND eyrsde  = ccocod "
			      ."    AND eyrtip  = 'Recibo' "
			      ."    AND eyrest  = 'on' "
				  ."    AND ccocpx  = 'on' "              //CICLOS de Producción 'ON'
				  ."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('$wfecha',' ','$whora_par_actual',':00:00')),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
				  ."    AND ubihis  = kadhis "
				  ."    AND ubiing  = kading "
				  ."    AND kadare  = 'on' "
				  ."    AND kadfec  = '".$wfecha."'"
				  ."    AND kadpro  = tarcod "
				  ."    AND tarpdx  = 'off' "            //Tipo de Articulo no se produce en ciclos osea, que son de dispensacion
				  ."    AND kadart  NOT IN ( SELECT artcod FROM cenpro_000002 WHERE artcod = kadart) "
				  ."    AND kadper       = percod "
				  ."  GROUP BY 1,2,3,4,5,6,7,8,9,10 ";
				  
				  //On
				  //echo $q."<br>";
			   break;
		 	}	
	 	}	  
	       
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);  
	           	
	  if ($num > 0)
	     {
		  if ($wopcion == "9")   //Entra si es el monitor de CTC's
		     {
			  ctc($res);         // <==== /// * * * C T C ' s * * * \\\
		     } 
		    else
		     {
		      $j=1;  //Indica cuantas historias tienen estado == "on" y son las unicas llevadas a la matriz   
			  for($i=1;$i<=$num;$i++)
				 {
				  $row = mysql_fetch_array($res);  	  
					  
				  $whab = $row[0];   //habitación actual
				  $whis = $row[1];   
				  $wing = $row[2];  
				  $wreg = $row[3];   //Id
			      $whap = $row[5];   //Hora Alta en Proceso
			      $wmue = $row[6];   //Indicador de Muerte
			      $whan = $row[7];   //Habitación Anterior
			      
			      
		               
			      //Traigo los datos demograficos del paciente
		          $q = " SELECT pacno1, pacno2, pacap1, pacap2, pacced, pactid "
		              ."   FROM root_000036, root_000037 "
		              ."  WHERE orihis = '".$whis."'"
				      ."    AND oriing = '".$wing."'"
				      ."    AND oriori = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
				      ."    AND oriced = pacced "; 
		          $respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		          $rowpac = mysql_fetch_array($respac); 
		               
				  $wpac = $rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3];    //Nombre
				  $wdpa = $rowpac[4];                                                 //Documento del Paciente
			      $wtid = $rowpac[5];                                                 //Tipo de Documento o Identificacion
			      
			      estado_del_kardex($whis, $wing, &$westado, $wmue, &$wcolor, &$wactual);     
			      
			      if ($wmue=="on")
			         {
				      $whab=$whan;
				     } 
			      
				     
				  //Llevo los registros con estado=="on" a una matrix, para luego imprimirla por el orden (worden)
			      if ($westado=="on")
			         {
				      $wmat_estado[$j][0]=$whab;
				      $wmat_estado[$j][1]=$whis;
				      $wmat_estado[$j][2]=$wing;
				      $wmat_estado[$j][3]=$wpac;
				      $wmat_estado[$j][4]=$westado;
				      $wmat_estado[$j][5]=$wcolor;
				      
				      $j++;
				     } 
			     }	
			     
			     // usort($wmat_estado,'comparacion'); Esto ya no hay que hacerlo

			     if ($wopcion != "0")
			     
			     echo "<tr><td align=left bgcolor=#fffffff colspan=3><font size=2 text color=#CC0000><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#fffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Registros: ".($j-1)."</b></font></td></tr>";
			     if ($j > 6)     //Si el numero de Hrias es mayor a 6 entonces lo muestro en dos grupos de columnas (Izq. y Derecha)
			        {
				     switch ($wopcion)
					    {
						 case "3":       //Pacientes SIN Kardex
						   {   
					         echo "<tr class='encabezadoTabla'>";
							 echo "<th>Habitacion</th>";
							 echo "<th>Historia</th>";
							 echo "<th>Paciente</th>";
							 echo "<th>Ver</th>";
							 echo "<th>&nbsp</th>";
							 echo "<th>Habitacion</th>";
							 echo "<th>Historia</th>";
							 echo "<th>Paciente</th>";
							 echo "<th>Ver</th>";
							 echo "</tr>";
							 
							 break;
					       }
					     default:
				            {   
							   echo "<tr class='encabezadoTabla'>";
							   echo "<th>Habitacion</th>";
							   echo "<th>Historia</th>";
							   echo "<th>Paciente</th>";
							   echo "<th>Ver</th>";
							   echo "<th>&nbsp</th>";
							   echo "<th>&nbsp</th>";
							   echo "<th>Habitacion</th>";
							   echo "<th>Historia</th>";
							   echo "<th>Paciente</th>";
							   echo "<th>Ver</th>";
							   echo "<th>&nbsp</th>";
							   echo "</tr>";
							   
							   break;
					        } 
				   		}
				    } 
			       else
			          {
				       switch ($wopcion)
					    {
						 default:       //Pacientes SIN Kardex y Resto
						   {   
						    echo "<tr class='encabezadoTabla'>";
						    echo "<th>Habitacion</th>";
						    echo "<th>Historia</th>";
						    echo "<th>Paciente</th>";
						    echo "<th>Ver</th>";
						    echo "<th>&nbsp</th>";
						    echo "</tr>";
						    
						    break;
					       } 
				        }
			          }  
				 
				 $wsw=0;     
			     for($i=1;$i<=$j-1;$i++)
			        {
				     if ($j > 6)  //Numero de lineas que se muestran en pantalla por defecto si es mayor lo muestro en dos grupos de columnas
		                {
			             if ($wsw==0)
			                {
			                 $wclass="fila1";
			                 $wsw=1;
		                    } 
			               else
			                  {
			                   $wclass="fila2";   
			                   $wsw=0;
		                      } 
			                  
			             echo "<tr class=".$wclass.">";
				         echo "<td align=center>".$wmat_estado[$i][0]."</td>";
				         echo "<td align=center>".$wmat_estado[$i][1]." - ".$wmat_estado[$i][2]."</td>";
				         echo "<td align=left  >".$wmat_estado[$i][3]."</td>";
				         
				         if ($wopcion=="3" or $wopcion=="8")      //Esta es la opcion de historias que no tienen kardex actualizado
				            echo "<td align=center><A href='generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Kardex </A></td>";
				           else
				              {
					           if ($wopcion != "2")   
					               echo "<td align=center><A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>"; 
					            //Cargar
					            if ($wopcion!="4" and $wopcion!="6" and $wopcion!="7")  // 4 = es la opcion de antibioticos, debe ejecutar es el programa de cargos de la central de mezclas
				                   echo "<td align=center><A href='cargos.php?emp=".$wemp_pmla."&bd=".$wbasedato."&tipTrans=C&wemp=".$wemp_pmla."&cco[cod]=".$wccousu."&historia=".$wmat_estado[$i][1]."&fecDispensacion=".$wfecha."' target=_blank> Cargar </A></td>";
				                  else
				                     {
				                      if ($wopcion != "7")
				                         echo "<td align=center ><A href='../../".$wcenmez."/procesos/cargos.php?wbasedato=lotes.php&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
				                        else
				                           echo "<td align=center>&nbsp</td>";
			                         }
			                  } 
				              
				              
			             echo "<td align=center>--------</td>";
				         $i++;
				         if ($i <= ($j-1))
				            {
					         echo "<td align=center>".$wmat_estado[$i][0]."</td>";
					         echo "<td align=center>".$wmat_estado[$i][1]." - ".$wmat_estado[$i][2]."</td>";
					         echo "<td align=left  >".$wmat_estado[$i][3]."</td>";  
					         if ($wopcion=="3" or $wopcion=="8")  //Esta es la opcion de historias que no tienen kardex actualizado
					            echo "<td align=center><A href='generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Kardex </A></td>";
					           else
					              {
					                if ($wopcion != "2")    
						               echo "<td align=center><A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>"; 
						            //Cargar
						            if ($wopcion!="4" and $wopcion!="6" and $wopcion!="7")  // 4 = es la opcion de antibioticos, debe ejecutar es el programa de cargos de la central de mezclas
					                   echo "<td align=center><A href='cargos.php?emp=".$wemp_pmla."&bd=".$wbasedato."&tipTrans=C&wemp=".$wemp_pmla."&cco[cod]=".$wccousu."&historia=".$wmat_estado[$i][1]."&fecDispensacion=".$wfecha."' target=_blank> Cargar </A></td>";
					                  else
					                    {
					                     if ($wopcion != "7")
					                        echo "<td align=center ><A href='../../".$wcenmez."/procesos/cargos.php?wbasedato=lotes.php&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
					                       else
					                          echo "<td align=center>&nbsp</td>";
				                        }
			                      } 
					        }    
				         echo "</tr>";   
		                }
		               else 
		                  {    
			               if (is_integer($i/2))
			                  $wclass="fila1";
			                 else
			                   $wclass="fila2";
			                    
				           echo "<tr class=".$wclass.">";
					       echo "<td align=center>".$wmat_estado[$i][0]."</td>";
					       echo "<td align=center>".$wmat_estado[$i][1]." - ".$wmat_estado[$i][2]."</td>";
					       echo "<td align=left  >".$wmat_estado[$i][3]."</td>";
					       if ($wopcion=="3" or $wopcion=="8")   //Esta es la opcion de historias que no tienen kardex actualizado
				              echo "<td align=center  ><A href='generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Kardex </A></td>";
				             else
				                {
				                  echo "<td align=center><A href='perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$wmat_estado[$i][1]."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>"; 
						          //Cargar
						          if ($wopcion!="4" and $wopcion!="6" and $wopcion!="7")  // 4 = es la opcion de antibioticos, debe ejecutar es el programa de cargos de la central de mezclas
					                 echo "<td align=center><A href='cargos.php?emp=".$wemp_pmla."&bd=".$wbasedato."&tipTrans=C&wemp=".$wemp_pmla."&cco[cod]=".$wccousu."&historia=".$wmat_estado[$i][1]."&fecDispensacion=".$wfecha."' target=_blank> Cargar </A></td>";
					                else
					                   if ($wopcion!="6" and $wopcion!="7")  //Con Antibioticos SIN Confirmar no debe salir Cargar
					                      echo "<td align=center><A href='../../".$wcenmez."/procesos/cargos.php?wbasedato=lotes.php&tipo=C&historia=".$wmat_estado[$i][1]."&cco=' target=_blank> Cargar </A></td>";
					                     else
					                        echo "<td align=center>&nbsp</td>"; 
				                }
					       echo "</tr>";
				          } 
		            }
	          }  // fin del else que no es CTC
		  }
		 else
		    echo "NO HAY HABITACIONES OCUPADAS"; 
	  echo "</table>";
     
	  if ($wopcion=="9")
	     echo "<meta http-equiv='refresh' content='300;url=Monitor_kardex.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wopcion=".$wopcion."'>";
	    else
	       echo "<meta http-equiv='refresh' content='120;url=Monitor_kardex.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wopcion=".$wopcion."'>";
	                 
	  echo "</form>";
	  
	  echo "<br>";
	  echo "<table>"; 
	  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	  echo "</table>";
     }
} // if de register



include("free.php");

?>