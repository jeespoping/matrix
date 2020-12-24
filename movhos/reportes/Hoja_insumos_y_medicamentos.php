<head>
  <title>REPORTE ADMINISTRACION DE INSUMOS Y MEDICAMENTOS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<script type="text/javascript">
function mostrar(x,y)
{
	document.forms.display.x;

}

function cerrarVentana()
	 {
      window.close()		  
     }
</script>
<?php
include_once("conex.php");
   /**************************************************
	*	  REPORTE DE ADMINISTRACION DE MEDICAMENTOS  *
	*	  Y MATERIAL MEDICO QX POR SERVICIO O UNIDAD *
	*				CONEX, FREE => OK				 *
	*************************************************
	*Modificaciones
	*Marzo 14 del 2019 Arleyda I.C. Migración realizada */
	
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

 	

 	include_once("root/comun.php");
  					    
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wuser = substr($user,(strpos($user,"-")+1),strlen($user)); 
    
                
    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";      
          
    
    //=======================================================================================================
    //=======================================================================================================
    //CON ESTO TRAIGO LA EMPRESA Y TODAS CAMPOS NECESARIOS DE LA EMPRESA
    $q = " SELECT empdes "
        ."   FROM root_000050 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' ";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row = mysql_fetch_array($res); 
  
    $wnominst=$row[0];
  
    //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
    $q = " SELECT detapl, detval "
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
           }  
      }
     else
        echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";            
    //=======================================================================================================
    //=======================================================================================================            
    
    $wactualiz="(Abril 28 de 2009)";
    
    
    echo "<form action='Hoja_insumos_y_medicamentos.php' method=post>";
	
	echo "<INPUT type='hidden' name='servicioDomiciliario' value='".$servicioDomiciliario."'>";

    if (!isset($whis) or !isset($wing))
       {
	    encabezado("REPORTE ADMINISTRACION DE INSUMOS Y MEDICAMENTOS (HOJA DE MEDICAMENTOS E INSUMOS)", $wactualiz, 'clinica');
		
		$filtroSD = " != 'on' ";
		if( isset( $servicioDomiciliario ) && $servicioDomiciliario == 'on' ){
			$filtroSD = " = 'on' ";
		}
	       
	    echo "<center><table>";
          
        $q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data "
	        ."   FROM ".$wbasedato."_000018, root_000037, root_000036, ".$wbasedato."_000011 "
	        ."  WHERE ubihis  = orihis "
	        ."    AND ubiing  = oriing "
	        ."    AND oriori  = '".$wemp_pmla."'"
	        ."    AND oriced  = pacced "
	        ."    AND ubiald != 'on' "              //Solo los que estan activos
	        ."    AND ubisac  = ccocod "
	        ."	  AND ccodom ".$filtroSD
	        ."  ORDER BY 1, 4, 5 ";
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $wnr = mysql_num_rows($res);        
	            
	           
	    echo "<tr class=encabezadoTabla>";
	    echo "<th>Habitacion</th>";
	    echo "<th>Historia</th>";
	    echo "<th>Ingreso</th>";
	    echo "<th colspan=2>Paciente</th>";
	    echo "</tr>";
	           	   
	    $whabant = "";
	    for ($i=1;$i<=$wnr;$i++)
		   {
			if ($i % 2 == 0)
			   $wclass = "fila1";
			  else
			     $wclass = "fila2";
			        
			$row = mysql_fetch_row($res);     
			
		    $whab = $row[0];
		    $whis = $row[1];
		    $wing = $row[2];
		    $wpac = $row[5]." ".$row[6]." ".$row[3]." ".$row[4];
		    		            	            
		    if ($whabant != $whab)
		       {
			    echo "<tr class=".$wclass.">";
			    echo "<td align=center>".$whab."</td>";
			    echo "<td align=center>".$whis."</td>";
			    echo "<td align=center>".$wing."</td>";
			    echo "<td align=left  >".$wpac."</td>";
			            
			    echo "<td align=center><A href='Hoja_insumos_y_medicamentos.php?whis=".$whis."&wing=".$wing."&wemp_pmla=".$wemp_pmla.( ( isset( $servicioDomiciliario ) && $servicioDomiciliario == 'on' ) ? '&servicioDomiciliario=on' : '' )."'>Imprimir</A></td>";
			    echo "</tr>";
			           
			    $whabant = $whab;
		       }
		   } 
		 echo "</table>";      
		 
		 echo "<center><table>";
		 echo "<tr class=seccion1>";
		 echo "<td><b>Ingrese la Historia :</b><INPUT TYPE='text' NAME='whis' SIZE=10></td>";
		 echo "<td><b>Nro de Ingreso :</b><INPUT TYPE='text' NAME='wing' SIZE=10></td>";
		 echo "</tr>";
		 echo "<tr>&nbsp</tr>";
		 echo "<tr>&nbsp</tr>";
		 echo "<tr class=boton1><td align=center colspan=6></b><input type='submit' value='ACEPTAR'></b></td></tr></center>";
		 echo "</table>";
       }	       
	else 
	   /******************************** 
	   * TODOS LOS PARAMETROS ESTAN SET *
	   ********************************/
	   {                                
	    
        encabezado("REPORTE ADMINISTRACION DE MEDICAMENTOS E INSUMOS A PACIENTES", $wactualiz, 'clinica');
        
        echo "<center><table>";
        
        $q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data, ubisac, cconom "
	        ."   FROM ".$wbasedato."_000018, root_000037, root_000036, ".$wtabcco
	        ."  WHERE ubihis  = '".$whis."'"
	        ."    AND ubiing  = '".$wing."'"
	        ."    AND ubihis  = orihis "
	        ."    AND ubiing  = oriing "
	        ."    AND oriori  = '".$wemp_pmla."'"
	        ."    AND oriced  = pacced "
	        //."    AND ubiald != 'on' "              //Solo los que estan activos
	        ."    AND ubisac  = ccocod "
	        ."  ORDER BY 1, 4, 5 ";
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $wnr = mysql_num_rows($res);   
	    
	    $row = mysql_fetch_row($res);     
        $whab    = $row[0];
        $whis    = $row[1];
        $wing    = $row[2];
        $wpac    = $row[5]." ".$row[6]." ".$row[3]." ".$row[4];
        $wser    = $row[9];
        $wnomser = $row[10];
	    
        echo "<tr class=seccion1>";  
		echo "<td colspan=10><b>HISTORIA : </b>".$whis." - ".$wing."</td>";
        echo "<td colspan=10><b>SERVICIO : </b>".$wnomser."</td>";
        echo "<td colspan=9 ><b>CAMA : </b>".$whab."</td>"; 
        echo "</tr>";  
        
        echo "<tr class=seccion1>";
        echo "<td colspan=29><b>PACIENTE : </b>".$wpac."</td>";
		echo "</tr>";
       
        
        $q = " SELECT aplart, ".$wbasedato."_000015.fecha_data, mid(aplron,1,instr(aplron,':')-1), sum(aplcan) AS aplcan, apldes "
            ."   FROM ".$wbasedato."_000015 "
		    ."  WHERE aplhis = '".$whis."'"
		    ."    AND apling = '".$wing."'"
		    ."    AND aplest = 'on' "
		    ."  GROUP BY 1, 2, 3, 5 "
		    ." HAVING sum(aplcan) <> 0 "
		    ."  ORDER BY 2, 1, 3 ";
		$res3 = mysql_query($q,$conex);
		$wnr = mysql_num_rows($res3); 
		     
		
        //Inicializo la MATRIZ a donde voy a llevar todo lo que le aplicaron al paciente en la estadia
	    for ($j=0;$j<=$wnr;$j++)
	       {
	        for ($l=0;$l<=24;$l++)     //24 horas que tiene el dia
	           {
	            $Mrondas[$j][$l]=0;    //Aca almaceno las cantidades de cada articulo segun la ronda
		       } 
		    $Afechas[$j]=0;            //Aca llevo las fecha de aplicacion   
		    $Aarticulos[$j]=0;         //Aca llevo el codigo del articulo de acuerdo a la hora de la ronda
		    $Adesc[$j]="";             //Aca llevo el codigo del articulo de acuerdo a la hora de la ronda
		   }		 
		
		   	    
	    $j=1;
	    $i=1;
	    $row = mysql_fetch_row($res3);
	    
	    while ($j <= $wnr)
	       {
		    $wfec  = $row[1];
		    $wart  = $row[0];
		    $wdesc = $row[4];
		    
		    $Afechas[$i]    = $row[1];
		    $Aarticulos[$i] = $row[0];
		    $Adesc[$i]      = $row[4];
		  
		    while ($wart==$row[0] and $wfec==$row[1] and $wdesc==$row[4])
		        {
			     $wronda= (integer) $row[2];   
			       
			     if ($row[3]!=0 and trim($row[3])!="")  //Si la cantidad es diferente a cero
			        {    
		         	 $Mrondas[$i][$wronda]=$row[3];
		         	} 
		         $row = mysql_fetch_row($res3);   
		         $j++;                            //Subindice para controlar el puntero del resultado del query
	         	} 
	        $i++;
	       }      
		    
	    $t=$i;
	    
	    echo "<tr class=encabezadoTabla>";    
        echo "<th>FECHA</th>";
        echo "<th>CODIGO</th>";
        echo "<th>INSUMO</th>";
        echo "<th>Unidad</th>";
        $i=0;
        while ($i <= 23)
             {
	          if ($i <= 12)      
	             echo "<th>".$i." AM"."</th>"; 
	            else              
	               echo "<th>".($i-12)." PM"."</th>";
	          $i=$i+1;
             }         
        echo "<th>TOTAL</th>";
	    echo "</tr>";         
	    
	    $wfec="";
	    $i=1;
	    $k=1;
	    
	    $cont_serv=0;
	    while ($i < $t)   
	         {
		      $wuni=".";  
		      $wnomart=$Adesc[$i]; 
		      if ($Aarticulos[$i] != "999")
		         {   
			      $q =  " SELECT artcom, artuni "
			           ."   FROM ".$wbasedato."_000026 "
			           ."  WHERE artcod = '".$Aarticulos[$i]."'";
			      $res = mysql_query($q,$conex);
			      $wfilas = mysql_num_rows($res);
			      if ($wfilas==0)  //Si no existe en movhos lo busco en central de mezclas
			         {
				      $q =  " SELECT artcom, artuni "
			               ."   FROM ".$wcenmez."_000002 "
			               ."  WHERE artcod = '".$Aarticulos[$i]."'";
			          $res = mysql_query($q,$conex);   
				     }       
                  $row = mysql_fetch_row($res);
                  
			      $wnomart = $row[0];
			      $wuni    = $row[1];
                 } 
			  
              $q = " SELECT count(*) "
                  ."   FROM ".$wbasedato."_000015 "
		          ."  WHERE aplhis     = '".$whis."'"
		          ."    AND apling     = '".$wing."'"
		          ."    AND fecha_data = '".$Afechas[$i]."'"
		          ."    AND aplest = 'on'"
		          ."  GROUP BY apldes ";        //Se agrupa por decripcion porque si se hace por codigo, falla el reporte para los medicamentos traidos por los pacientes porque estos tienen el mismo codigo pero diferente descripcion
		          //." HAVING sum(aplcan) <> 0 ";
		      $res3 = mysql_query($q,$conex);
              $wfilas = mysql_num_rows($res3);   
              
              if (is_integer(($i+$cont_serv)/2))
	             $wclass = "fila1";
	            else  
	               $wclass = "fila2";
              
              
              echo "<tr class=".$wclass.">";
		      if ($wfec != $Afechas[$i])
		         {
		          echo "<td rowspan=".$wfilas." align=center><font size=2>".$Afechas[$i]."</font></td>";
		          $wfec = $Afechas[$i];
	             }
	          echo "<td>".$Aarticulos[$i]."</td>";
		      echo "<td>".$wnomart."</td>";
		      echo "<td>".$wuni."</td>";
		      $j=0;
		      $wtotal=0;
		      while ($j <= 23)
		         {
			      if ($Mrondas[$i][$j] == 0)
			         echo "<td align=center>&nbsp</td>";
			        else 
			           {
				        if ($j > 12)
				           $wmsg=($j-12)." PM";
				          else
				             $wmsg=$j." AM";    
			            echo "<td align=center bgcolor=#dddddd><img src=/matrix/images/medical/movhos/plus.png alt='".$wmsg."'>&nbsp".$Mrondas[$i][$j]."</td>";
			            $wtotal = $wtotal + $Mrondas[$i][$j];
		               }
			      $j++;
		         }
		      echo "<td align=center bgcolor=#dddddd>".$wtotal."</td>";
		      echo "</tr>";
	          $i++;   
	         }
	    echo "</table>";
	    echo "<br>";
	   }
	   
	   echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	   
	   echo "<font size=3><A href='Hoja_insumos_y_medicamentos.php?wemp_pmla=".$wemp_pmla.( ( isset( $servicioDomiciliario ) && $servicioDomiciliario == 'on' ) ? '&servicioDomiciliario=on' : '' )."'> Retornar</A></font>";
	   
	   echo "<br><br>";
	   echo "<center><table></center>"; 
	   echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	   echo "</table>";
}
include_once("free.php");
?>
