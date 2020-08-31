<head>
  <title>CONSULTA AGENDA</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");


  /***************************************************
	*	      CONSULTA DE ACTIVIDADES AGENDA         *
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
    
      
    //=========================================================================
    //Aca viene la Dirección, Gerencia, Unidad o Servicio que utiliza la agenda
    echo "<input type='HIDDEN' name='wgerencia' value='".$wgerencia."'>";
    //=========================================================================          
    
    $wser=$wgerencia;
          
    
    //====================================================================================================================================
    //COMIENZA LA FORMA      
    echo "<form action='consulta_agenda.php' method=post>";
    
    $wfecha=date("Y-m-d"); 
    $hora = (string)date("H:i:s");
    
    $wcolor="dddddd";
    $wcolorfor="666666";
    
    
    //On
    //echo $wfecha_i."<br>";
    //echo $wfecha_f."<br>";
    //echo $wser."<br>";
    
    //if ((!isset($wfecha_i) or !isset($wfecha_f) or !isset($wser)) and !isset($wid))
    if ((!isset($wfecha_i) or !isset($wfecha_f) or !isset($wser)) and !isset($wid))
        {
	    echo "<center><table border=2>";
        echo "<tr><td align=center bgcolor=#fffffff colspan=2><font size=5 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
        echo "<tr><td align=center bgcolor=#fffffff colspan=2><font size=4 text color=#CC0000><b>CONSULTA DE ACTVIDADES</b></font></td></tr>";   
	       
	    ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //FECHA INICIAL Y FINAL
        echo "<tr>";
        echo "<td align=left bgcolor=".$wcolor."><b>Fecha Inicial: </b><INPUT TYPE='text' NAME='wfecha_i' VALUE=".$wfecha."></td>";  
        echo "<td align=left bgcolor=".$wcolor."><b>Fecha Final: </b><INPUT TYPE='text' NAME='wfecha_f' VALUE=".$wfecha."></td>";  
        echo "<tr>";
        
        
        if (!isset($wser) and $wser!="")
           {
	        $q =  " SELECT nombre "
				 ."   FROM agenda_000004 "
				 ."  ORDER BY 1 ";
				 	 
		    $res = mysql_query($q,$conex);
		    $num = mysql_num_rows($res);
	        echo "<tr><td align=center bgcolor=#ffcc66 colspan=2>SELECCIONE EL SERVICIO A CONSULTAR: </td></tr>";
		    echo "<tr><td align=center colspan=2><select name='wser'>";
		    echo "<option>* - Todos</option>";    
			for ($i=1;$i<=$num;$i++)
			    {
				 $row = mysql_fetch_array($res); 
		         echo "<option>".$row[0]."</option>";
	            }
			echo "</select></td></tr>";
           }		
	    
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
		    if (isset($wid))
		       $q=   "  SELECT * "
		         ."    FROM agenda_000003 "
		         ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND id         = ".$wid;
		      else   
		         {
			      if (isset($wmotivo))
			         $q=   "  SELECT * "
			              ."    FROM agenda_000003 "
			              ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
			              ."     AND Motivo     like '".$wmotivo."'"
			              ."     AND Origen     like '".$wser."'";
			              //."     AND Hora_respuesta != '00:00:00' "
		                  //."     AND Hora_llegada != '00:00:00' ";
			        else
			           $q=   "  SELECT * "
			              ."    FROM agenda_000003 "
			              ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
			              ."     AND Origen     like '".$wser."'";
			              //."     AND Hora_respuesta != '00:00:00' "
		                  //."     AND Hora_llegada != '00:00:00' ";      
		         }      
			         
			           
			    
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    $col = mysql_num_fields($res); 
	        
		    echo "<center><table border=2>";
            echo "<tr><td align=center bgcolor=#fffffff colspan=".$col."><font size=5 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
            echo "<tr><td align=center bgcolor=#fffffff colspan=".$col."><font size=4 text color=#CC0000><b>CONSULTA DE ACTIVIDADES</b></font></td></tr>"; 
		     
	        echo "<tr>";
	        echo "<td align=center bgcolor=fffffff colspan=".$col."><b>Período de Consulta: </b>".$wfecha_i."<b> al </b>".$wfecha_f."</td>";  
	        echo "</tr>";
	       
	        if (!isset($wid) and isset($wser) and ($wser != ""))
	           { 
		        echo "<tr>";
		        echo "<td align=center bgcolor=fffffff colspan=".$col."><b>Servicio: </b>".$wser."</td>";  
		        echo "</tr>";
	           } 
	        echo "<tr>";
	        echo "<td align=center bgcolor=fffffff colspan=".$col.">&nbsp</td>";  
	        echo "</tr>";
	         
	        if (!isset($wid))
		        if ($wser == '* - Todos')
		           $wser='%';
	        
		    //===================================================================================================================================================
		    // QUERY: 
		    //===================================================================================================================================================
		    if (isset($wid))   //Si esta setiado el id busco por este, si no por el servicio u origen
		       $q=   "  SELECT * "
		         ."    FROM agenda_000003 "
		         ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		         ."     AND id         = ".$wid  
		         //."     AND Hora_respuesta != '00:00:00' "
		         //."     AND Hora_llegada != '00:00:00' "
		         ."   ORDER BY fecha_data, hora_data ";
		      else 
		         if (!isset($wmotivo))
			         if (isset($word) and $word == "S")
			            $q=   "  SELECT *, (HOUR(TIMEDIFF(hora_llegada,hora_data))*60        + "
					         ."                  MINUTE(TIMEDIFF(hora_llegada,hora_data))    + "
					         ."                (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60)  "
					         ."    FROM agenda_000003 "
					         ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					         ."     AND Origen     like '".$wser."'"  
					         //."     AND Hora_respuesta != '00:00:00' "
		                     //."     AND Hora_llegada != '00:00:00' "
					         ."   ORDER BY 19 desc ";
					    else     
			               $q=   "  SELECT *  "
					            ."    FROM agenda_000003 "
					            ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					            ."     AND Origen     like '".$wser."'" 
					            //."     AND Hora_respuesta != '00:00:00' "
		                        //."     AND Hora_llegada != '00:00:00' " 
					            ."   ORDER BY fecha_data, hora_data ";
					else
					   if (isset($word) and $word == "S")
				          $q=   "  SELECT *, (HOUR(TIMEDIFF(hora_llegada,hora_data))*60        + "
						       ."                  MINUTE(TIMEDIFF(hora_llegada,hora_data))    + "
						       ."                (SECOND(TIMEDIFF(hora_llegada,hora_data)))/60)  "
						       ."    FROM agenda_000003 "
						       ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
						       ."     AND Motivo     like '".$wmotivo."'" 
						       ."     AND Origen     like '".$wser."'"
						       //."     AND Hora_respuesta != '00:00:00' "
		                       //."     AND Hora_llegada != '00:00:00' "
						       ."   ORDER BY 19 desc ";
						 else     
				            $q= "  SELECT *  "
						       ."    FROM agenda_000003 "
						       ."   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
						       ."     AND Motivo     like '".$wmotivo."'" 
						       ."     AND Origen     like '".$wser."'"
						       //."     AND Hora_respuesta != '00:00:00' "
		                       //."     AND Hora_llegada != '00:00:00' " 
						       ."   ORDER BY fecha_data, hora_data ";            
			          
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    $col = mysql_num_fields($res);
		    
		    if ($num > 0)
			   {
				//ACA IMPRIMO LOS NOMBRES DE LOS CAMPOS COMO TITULOS   
				for ($j=1;$j<=$col-3;$j++)
					        echo "<th bgcolor=#ffcc66><font size=1>".mysql_field_name($res,$j)."</font></th>";     //Imprime los titulos con los nombre de los campos
					           
				for ($i=1;$i<=$num;$i++)
				    {
					 $row = mysql_fetch_array($res); 
					 
					 if (is_integer($i/2))
				         $wcolor = "99ccff";
				        else  
				           $wcolor = "dddddd"; 
					 
					 echo "<tr>";   
					 for ($j=1;$j<=$col-3;$j++)
					     {
						  if ($row[$j] == "")
						     echo "<td bgcolor=".$wcolor."><font size=1>&nbsp</font></td>"; 
						    else    
					           echo "<td bgcolor=".$wcolor."><font size=1>".$row[$j]."</font></td>";	     
					     }
					 echo "</tr>";    
				    }         
				echo "<tr bgcolor=".$wcolor.">";
				echo "<td colspan=4><font size=3>Total actividades en el período : "."</font></td>";
				echo "<td colspan=".($col-6).">".$num."</td>";
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
