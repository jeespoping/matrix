<head>
  <title>CONSULTA DE SERVICIOS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");

//echo "<meta http-equiv='refresh' content='10;url=central_de_camilleros.php?'>";

  /***************************************************
	*	          CONSULTA DE SERVICIOS              *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	/*
	

	

	

						or die("No se ralizo Conexion");
    

    */
    
	//$conexunix = odbc_pconnect('facturacion','facadm','1201')
  	//				    or die("No se ralizo Conexion con el Unix");
  					    
  			
  	
  
    include_once("root/magenta.php");
    include_once("root/comun.php");
  
    $conex = obtenerConexionBD("matrix");
	
    

  	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
    $wactualiz="(Octubre 06 de 2015)";                              // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
    /*
 		ACTUALIZACIONES:

	*	Octubre 06 de 2015
    	Eimer Castro     		:	Se modifica la tabla para mostrar el nombre del usuario junto con el código en el campo Usuario_recibe.

    *	Marzo 23 de 2010
    	Creación del programa
	**/
  			    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
    
          
    //====================================================================================================================================
    //COMIENZA LA FORMA      
    echo "<form action='consulta_de_servicios.php' method=post>";
    
    $wfecha=date("Y-m-d"); 
    $hora = (string)date("H:i:s");
    
    $wcolor="dddddd";
    $wcolorfor="666666";
    
    encabezado("CONSULTA DE SERVICIOS ",$wactualiz, "clinica");
    
    if ((!isset($wfecha_i) or !isset($wfecha_f) or (!isset($wser) and !isset($wcamillero))) and !isset($wid) and !isset($wcentral))
       {
	    echo "<center><table border=2>";
        echo "<tr>";
        echo "<td align=right  class=fila1><b>Fecha Inicial: </b></td>";
        echo "<td align=center class=fila2>";
        campofechaDefecto("wfecha_i",$wfecha);
        echo "</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td align=right  class=fila1><b>Fecha final: </b></td>";
        echo "<td align=center class=fila2>";
        campofechaDefecto("wfecha_f",$wfecha);
        echo "</td>";
        echo "</tr>";
        
        $q =  " SELECT nombre "
			 ."   FROM cencam_000004 "
			 ."  ORDER BY 1 ";
			 	 
	    $res = mysql_query($q,$conex);
	    $num = mysql_num_rows($res);
        echo "<tr class=encabezadoTabla><td align=center colspan=2>SELECCIONE EL SERVICIO A CONSULTAR: </td></tr>";
	    echo "<tr><td align=center colspan=2><select name='wser'>";
	    echo "<option>* - Todos</option>";    
		for ($i=1;$i<=$num;$i++)
		    {
			 $row = mysql_fetch_array($res); 
	         echo "<option>".$row[0]."</option>";
            }
		echo "</select></td></tr>";
		
		$q =  " SELECT codcen, nomcen"
			 ."   FROM cencam_000006 ";
		$res = mysql_query($q,$conex);
	    $num = mysql_num_rows($res);
	    
	    echo "<tr class=encabezadoTabla><td align=center colspan=2>SELECCIONE LA CENTRAL A CONSULTAR: </td></tr>";
	    echo "<tr>";
		echo "<td align=center bgcolor=#cccccc colspan=2>";
	    echo "<select name='wcentral'>";
	    echo "<option>&nbsp</option>";    
		for ($i=1;$i<=$num;$i++)
		    {
			 $row = mysql_fetch_array($res); 
	         echo "<option>".$row[0]." - ".$row[1]."</option>";
            }
		echo "</select></td></tr>";
	    
	    echo "<tr>";
        echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='Consultar'></td>";   
        echo "</tr>";
        echo "</table>";
       }
      else
         {
	     //===================================================================================================================================================
	     // QUERY: 
	     //===================================================================================================================================================
	     if (isset($whora_i))
	        $wrangohora=" AND Hora_data BETWEEN '".$whora_i."' AND '".$whora_f."' ";
	       else
	          $wrangohora=" "; 
	    
	      if (isset($wid))
	         {
		      $q=  "  SELECT * "
		          ."    FROM cencam_000003 "
		          ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		          ."     AND id         = ".$wid
		          ."     AND Central    like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
		          .$wrangohora;
	         }    
		    else   
		       {
			    if (isset($wmotivo))
			       {
			        $q=  "  SELECT * "
			            ."    FROM cencam_000003 "
			            ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
			            ."     AND Motivo         like '".$wmotivo."'"
			            ."     AND Hora_respuesta != '00:00:00' "
		                ."     AND Hora_llegada   != '00:00:00' "
		                ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
		                .$wrangohora;
	               }     
			      else
			        {
			         if (isset($wcamillero))
			            {
				         $q="  SELECT Motivo, COUNT(*) "
			               ."    FROM cencam_000003 "
				           ."   WHERE Anulada = 'No' " 
				           ."     AND Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				           ."     AND Hora_respuesta != '00:00:00' "
				           ."     AND Hora_llegada   != '00:00:00' "
				           ."     AND Camillero       = '".$wcamillero."'"
				           ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
				           ."   GROUP BY 1 "
				           ."   ORDER BY 2 desc ";
				        }
			           else
			              {     
				           $q= "  SELECT * "
				              ."    FROM cencam_000003 "
				              ."   WHERE Fecha_data     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				              ."     AND Origen         like '".$wser."'"
				              ."     AND Hora_respuesta != '00:00:00' "
			                  ."     AND Hora_llegada   != '00:00:00' "
			                  ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
			                  .$wrangohora;     
		                  }     
	                }      
		         } 
		    $res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
			
		    $col = mysql_num_fields($res); 
	        
		    echo "<center><table border=2>";
            echo "<tr class=fila1>";
            echo "<td align=center colspan=".$col."><b>CENTRAL : ".$wcentral."</b></td>";
            echo "</tr>";
		     
	        echo "<tr class=fila1>";
	        echo "<td align=center colspan=".$col."><b>Período de Consulta: </b>".$wfecha_i."<b> al </b>".$wfecha_f."</td>";  
	        echo "</tr>";
	       
	        if (!isset($wid) and isset($wser) and ($wser != ""))
	           { 
		        echo "<tr class=fila1>";
		        echo "<td align=center colspan=".$col."><b>Servicio: ".$wser."</b></td>";  
		        echo "</tr>";
	           } 
	           
	        if (isset($wcamillero))
	           { 
		        echo "<tr class=fila1>";
		        echo "<td align=center colspan=".$col."><b>Camillero: ".$wcamillero."</b></td>";  
		        echo "</tr>";
	           }    
	        echo "<tr class=fila1>";
	        echo "<td align=center colspan=".$col.">&nbsp</td>";  
	        echo "</tr>";
	         
	        if (!isset($wid))
		        if (isset($wser) and $wser == '* - Todos')
		           $wser='%';
	        
		    //===================================================================================================================================================
		    // QUERY: 
		    //===================================================================================================================================================
		    if (isset($wid))   //Si esta setiado el id busco por este, si no por el servicio u origen
		       $q=   "  SELECT * "
			        ."    FROM cencam_000003 "
			        ."   WHERE Fecha_data     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
			        ."     AND id              = ".$wid  
			        ."     AND Hora_respuesta != '00:00:00' "
			        ."     AND Hora_llegada   != '00:00:00' "
			        ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
			        .$wrangohora
			        ."   ORDER BY fecha_data, hora_data ";
		      else 
		         if (!isset($wmotivo))
		            {
			         if (isset($word) and $word == "S")
			           { 
			            $q=   "  SELECT *, (HOUR(TIMEDIFF(hora_llegada,hora_data))*60        + "
					         ."                  MINUTE(TIMEDIFF(hora_llegada,hora_data))    + "
					         ."                (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60)  "
					         ."    FROM cencam_000003 "
					         ."   WHERE Fecha_data     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					         ."     AND Origen         like '".$wser."'"  
					         ."     AND Hora_respuesta != '00:00:00' "
		                     ."     AND Hora_llegada   != '00:00:00' "
		                     ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
		                     .$wrangohora
					         ."   ORDER BY 19 desc ";
				       }     
					  else
					    { 
					     if (isset($wcamillero))
				            {
					         $q="  SELECT Motivo, COUNT(*) AS cantidad "
					           ."    FROM cencam_000003 "
					           ."   WHERE Anulada        = 'No' " 
					           ."     AND Fecha_data     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					           ."     AND Hora_respuesta != '00:00:00' "
					           ."     AND Hora_llegada   != '00:00:00' "
					           ."     AND Camillero       = '".$wcamillero."'"
					           ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
					           ."   GROUP BY 1 "
					           ."   ORDER BY 2 desc ";
					        }
				           else
				              {      
				               $q= "  SELECT *  "
						          ."    FROM cencam_000003 "
						          ."   WHERE Fecha_data     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
						          ."     AND Origen         like '".$wser."'" 
						          ."     AND Hora_respuesta != '00:00:00' "
			                      ."     AND Hora_llegada   != '00:00:00' "
			                      ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
			                      .$wrangohora
						          ."   ORDER BY fecha_data, hora_data ";
					         }
				        }
			        }             
					else
					   if (isset($word) and $word == "S")
				          $q=   "  SELECT *, (HOUR(TIMEDIFF(hora_llegada,hora_data))*60        + "
						       ."                  MINUTE(TIMEDIFF(hora_llegada,hora_data))    + "
						       ."                (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60)  "
						       ."    FROM cencam_000003 "
						       ."   WHERE Fecha_data     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
						       ."     AND Motivo         like '".$wmotivo."'" 
						       ."     AND Hora_respuesta != '00:00:00' "
		                       ."     AND Hora_llegada   != '00:00:00' "
		                       ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
		                       .$wrangohora
						       ."   ORDER BY 19 desc ";
						 else     
				            $q= "  SELECT *  "
						       ."    FROM cencam_000003 "
						       ."   WHERE Fecha_data     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
						       ."     AND Motivo         like '".$wmotivo."'" 
						       ."     AND Hora_respuesta != '00:00:00' "
		                       ."     AND Hora_llegada   != '00:00:00' "
		                       ."     AND Central        like mid('".$wcentral."',1,instr('".$wcentral."','-')-2)"
		                       .$wrangohora 
						       ."   ORDER BY fecha_data, hora_data ";            
			          
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    $col = mysql_num_fields($res);

		    $wposicion_campo = 0;
		    
		    if ($num > 0)
			   {
				echo "<tr class=encabezadotabla>";   
				//ACA IMPRIMO LOS NOMBRES DE LOS CAMPOS COMO TITULOS 
				if ($col > 3)
				   {  
				    for ($j=1;$j<=$col-3;$j++)
				    {
				    	$wnombre_campo = mysql_field_name($res,$j);
					   echo "<th>".$wnombre_campo."</th>";     //Imprime los titulos con los nombres de los campos
			       		if($wnombre_campo == "Usuario_recibe"){
			       			$wposicion_campo = $j;
			       		}
			       	}	
			       }
			      else
			         {
				      for ($j=0;$j<=$col-1;$j++)
					   echo "<th>".mysql_field_name($res,$j)."</th>";     //Imprime los titulos con los nombres de los campos   
				     }
				echo "</tr>";     
					 
				$wtot=0;             
				for ($i=1;$i<=$num;$i++)
				    {
					 $row = mysql_fetch_array($res); 
					 
					 if (is_integer($i/2))
				         $wcolor = "fila1";
				        else  
				           $wcolor = "fila2"; 
					 
					 echo "<tr class=".$wcolor.">";   
					 if ($col > 3)
					    {
						 for ($j=1;$j<=$col-3;$j++)
						     {
							  if ($row[$j] == "") {
							     echo "<td align=center>&nbsp</font></td>"; 
							    }
							    else {
							    	$wnombre_usuario = "";
							    	$wcodigo_usuario = $row[$j];
							    	if($j == $wposicion_campo && $row[$j] != "") {
							    		$consulta_usuario = "SELECT Descripcion 
							    								FROM usuarios 
							    								WHERE Codigo = {$wcodigo_usuario}";
							    		 $resultado = mysql_query($consulta_usuario, $conex);
							    		 $row_usuario = mysql_fetch_array($resultado);
							    		 $wnombre_usuario = " - " . $row_usuario['Descripcion'];

							    	}
						           echo "<td align=center>".$row[$j]. $wnombre_usuario."</font></td>";
						           }
						     }
					    }     
					   else
					      {
						   for ($j=0;$j<=$col-1;$j++)
						     {
							  if ($row[$j] == "")
							     echo "<td align=center>&nbsp</font></td>"; 
							    else    
						           if ($j==0)
						              echo "<td align=left>".$row[$j]."</font></td>";	     
						             else
						               {
						                echo "<td align=center>".$row[$j]."</font></td>"; 
						                $wtot=$wtot+$row[$j];
						               } 
						     }
						  } 
					 echo "</tr>";    
				    }
				    if (isset($wtot) and $wtot>0) $num=$wtot;
				             
				echo "<tr class=encabezadoTabla>";
				if ($col > 3) 
				   echo "<td colspan=4 align=left><b>Total servicios en el período : "."</b></td>";
				  else
				     echo "<td colspan=1 align=left><b>Total servicios en el período : "."</b></td>"; 
				echo "<td colspan=".($col-6)." align=center><b>".$num."</b></td>";
				echo "</tr>";
			   } 
		 }   	      	     
	   
   echo "</center></table>";
   echo "<br>";
   echo "<br>";
   echo "<br>";
   echo '<center>';
	echo "<input type='button' align='left' onclick='javascript:window.close();' value='CERRAR' name='btn_retornar2'>";
   echo '</center>';
   
   echo "</form>";
   
}
include_once("free.php");
?>
