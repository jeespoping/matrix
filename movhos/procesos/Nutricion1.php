<head>
  <title>REGISTRO ATENCION NUTRICIONISTA</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	 document.forms.nutricion.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *       REGISTRO ATENCION NUTRICIONISTA       *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
	
  

  

  include_once("root/magenta.php");
  include_once("root/comun.php");
  
	                                                      // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Junio 15 de 2010)";                        // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                      // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
	                                                      
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION                                                                                                                              \\
//=========================================================================================================================================\\
//Este programa se crea por solicitud de las nutricionistas de la clinica las americas, en donde ellas pedian poder registrar las consultas\\                                                                                                                             \\
//realizadas por ellas a los pacientes y basado en estos registros poder generar las estadisticas correspondientes a lservicio de nutricion\\
//lo que hasta se hace en su totalidad manualmente, el funcionamiento del programa es de la sgte manera:                                   \\
//Se selecciona un servicio hospitalario de la clinica y la fecha en la que se va a realizar el registro por defecto sale selecionada la   \\
//fecha actual, se da clic en el boton <ENTRAR>, se despliega la lista de pacientes de el servicio seleccionado con las columnas de las 3  \\
//formas de nutricion la cual se puede seleccionar con solo dar clic, asi mismo si se registro a alguien por equivocacion se puede cancelar\\
//dando click en el circulo de la columna cancelar en la linea del paciente correspondiente.                                               \\
//=========================================================================================================================================\\
	                                                           
//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//Junio 15 de 2010                                                                                                                        \\
//========================================================================================================================================\\
//Se modifica para que al momento de grabar si no esta setiado el usuario o es nulo no Grabey evitar asi que queden registros sin usuario \\                                                                                                                                 \\
//========================================================================================================================================\\
	               
  $wfecha=date("Y-m-d");   
  $whora =(string)date("H:i:s");	                                                           
	                                                                                                       
  
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
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  
  encabezado("REGISTRO ATENCION NUTRICIONISTAS",$wactualiz, "clinica");   
       
  //FORMA ================================================================
  echo "<form name='nutricion' action='nutricion1.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  
  
  echo "<center><table>";
  
        
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
   if (!isset($wcco) or trim($wcco) == "" or !isset($wfec) or $wfec=="")
     {     
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
			 	 
	    
	  echo "<tr class=titulo><td align=center ><b>SELECCIONE EL SERVICIO EN EL QUE SE ENCUENTRA: </b></td></tr>";
	  echo "<tr><td align=center><select name='wcco'>";
	  echo "<option>&nbsp</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
          
      echo "<tr class=fila1>";
      echo "<td align=center><b>Fecha a Registrar</b><br>";
      campofecha("wfec");
      echo "</td>";
      echo "</tr>";
        
	  echo "<center><tr><td align=center colspan=4 bgcolor=cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else 
      { 
	   if (strpos($wcco,"-") > 0)
	      { 
	       $wccosto=explode("-",$wcco);
	       $wcco=$wccosto[0];
          }
         else
           {
            if (strpos($wcco,".") > 0)  
	   	       { 
		        $wccosto=explode(".",$wcco);
		        $wcco=$wccosto[1];
	           }
	       }	      
	       
	   $q = " SELECT cconom "
	       ."   FROM ".$wtabcco
	       ."  WHERE ccocod = '".$wcco."'";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);
	   $wnomcco=$row[0];   
	      
	   if (trim($wnomcco)=="")
	      {
           ?>	    
	         <script>
		       alert ("EL CENTRO DE COSTO NO FUE INGRESADO POR CODIGO DE BARRAS");     
		     </script>
		   <?php 
		  }        
          
	      
	   if (!isset($wccohos))
		 {	  
		  //Traigo el INDICADOR de si el centro de costo es hospitalario o No   
		  $q = " SELECT ccohos, ccoapl "
		      ."   FROM ".$wbasedato."_000011 "
		      ."  WHERE ccocod = '".$wcco."'";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res); 
		  if ($num > 0)
		     { 
		      $row = mysql_fetch_array($res);
		      if ($row[0]=="on")
		         {
		          $wccohos="on";
		          $wccoapl=$row[1];
	             } 
		        else
		           {
		            $wccohos="off";
		            $wccoapl=$row[1];
	               } 
		     }
		    else
		      {
		       $wccohos="off";        
		       $wccoapl="off";
	          } 
	          
	          //On
	         // echo "usuario: ".$wusuario."<br>";
		  
	      //Junio 15 de 2010    
	      if (isset($wusuario) and ($wusuario != ""))   //Verifico que el usuario si este setiado, para que no grabe sin el usuario.   
	         {
	          $q = " SELECT habcod, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, ".$wbasedato."_000056.id, nuthab, nutnut, nuttip, pactid, pacced "
			      ."   FROM root_000036, root_000037, ".$wbasedato."_000020, ".$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000056 "
			      ."     ON ".$wbasedato."_000018.ubihis = ".$wbasedato."_000056.nuthis "
			      ."    AND ".$wbasedato."_000018.ubiing = ".$wbasedato."_000056.nuting "
			      ."    AND nutest = 'on' "   
			      ."    AND nutfec  = '".$wfec."'"
			      ."  WHERE ubihis  = orihis "
			      ."    AND ubiing  = oriing "
			      ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
			      ."    AND oriced  = pacced "
			      ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
			      ."    AND ubiald != 'on' "			 //Que no este en Alta Definitiva
			      ."    AND ubihis  = habhis "
			      ."    AND ubiing  = habing "
			      ."    AND habcco  LIKE '".trim($wcco)."'"
			      ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
			      ."  ORDER BY 1 ";    
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);   
			     
			  if ($num >= 1)
			     {
				  for ($i=1;$i<=$num;$i++)
			         {
				      $row = mysql_fetch_array($res);   
				      
				      if ($row[7]=="" and isset($wtip[$i])) //Es primera atencion del dia y selecciono un tipo de Nutricion
				         {
					      //Grabo el registro del nutricionista con el tipo de nutricion
				          $q = " INSERT INTO ".$wbasedato."_000056 (   Medico       ,   Fecha_data,   Hora_data,   nutfec  ,   nuthis    ,   nuting    ,   nuthab    ,   nutnut      ,   nuttip      , nutest, Seguridad        ) "
						      ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$row[1]."','".$row[2]."','".$row[0]."','".$wusuario."','".$wtip[$i]."', 'on'  , 'C-".$wusuario."') ";
						  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						  //=======================================================================================================================================================   
			             }
			            else  //Si no es primera vez entro por aca, casi siempre debe ser por algun cambio
			               {
				            if (isset($wtip[$i]) and ($row[9]==$wusuario))
				               {
					            $q = " DELETE FROM ".$wbasedato."_000056 "
					                ."  WHERE nuthis = '".$row[1]."'"
					                ."    AND nuting = '".$row[2]."'"
					                ."    AND nuthab = '".$row[0]."'"
					                ."    AND nutnut = '".$wusuario."'"
					                ."    AND nutfec = '".$wfec."'";
					            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
					            
					            
					            //Grabo el registro del nutricionista con el tipo de nutricion
						        $q = " INSERT INTO ".$wbasedato."_000056 (   Medico       ,   Fecha_data,   Hora_data,   nutfec  ,   nuthis    ,   nuting    ,   nuthab    ,   nutnut      ,   nuttip      , nutest, Seguridad        ) "
							            ."                        VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$row[1]."','".$row[2]."','".$row[0]."','".$wusuario."','".$wtip[$i]."', 'on'  , 'C-".$wusuario."') ";
							    $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						       }
						       
						    if (isset($wcanc[$i]) and $wcanc[$i]=="on")
						       {
							    $q = " DELETE FROM ".$wbasedato."_000056 "
					                ."  WHERE nuthis = '".$row[1]."'"
					                ."    AND nuting = '".$row[2]."'"
					                ."    AND nuthab = '".$row[0]."'"
					                ."    AND nutnut = '".$wusuario."'"
					                ."    AND nutfec = '".$wfec."'";
					            $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());   
							   }       
				           }    
			          //==========================================================================================================================================================    
				      //==========================================================================================================================================================      
			         }
	             }
	         }
	        else   //Junio 15 de 2010
	           {
		           //On
		           echo "PAsooo<br>";
		           
		        echo "<script language='Javascript'>";
		          echo "alert(' NO SE REALIZA LA ACTUALIZACIÓN PORQUE EL SISTEMA NO DETECTO ACTIVIDAD DESDE HACE 5 MINUTOS Y TERMINÓ LA CONEXIÓN. DEBE REINGRESAR AL SISTEMA o Presionar Ctrl+F5 en la Pantalla Anterior');";  
		        echo "</script>";
		       }    
	                         
			     
		  $q = " SELECT habcod, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, ".$wbasedato."_000056.id, nuthab, nutnut, nuttip, pactid, pacced "
		      ."   FROM root_000036, root_000037, ".$wbasedato."_000020, ".$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000056 "
		      ."     ON ".$wbasedato."_000018.ubihis = ".$wbasedato."_000056.nuthis "
		      ."    AND ".$wbasedato."_000018.ubiing = ".$wbasedato."_000056.nuting "
		      ."    AND nutest  = 'on' "
		      ."    AND nutfec  = '".$wfec."'"    
		      ."  WHERE ubihis  = orihis "
		      ."    AND ubiing  = oriing "
		      ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
		      ."    AND oriced  = pacced "
		      ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
		      ."    AND ubiald != 'on' "			 //Que no este en Alta Definitiva
		      ."    AND ubihis  = habhis "
		      ."    AND ubiing  = habing "
		      ."    AND habcco  LIKE '".trim($wcco)."'"
		      ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
		      ."  ORDER BY 1 "; 
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);
				 
		  echo "<tr>&nbsp</tr>";
		  
		  echo "<tr class=seccion1>";
		  echo "<td colspan=13 align=center><b>Servicio o Unidad: ".$wnomcco."</b></td>";
		  echo "</tr>";
		  
		  echo "<tr class=seccion1>";
		  echo "<td colspan=13 align=center><b>Fecha de Registro: ".$wfec."</b></td>";
		  echo "</tr>";
		  
		  echo "<tr class=encabezadoTabla>";
		  //echo "<th>Id</th>";
		  echo "<th>Habitacion</th>";
		  echo "<th>Historia</th>";
		  echo "<th>Paciente</th>";
		  echo "<th>&nbspN P T&nbsp</th>";
		  echo "<th>&nbsp&nbspN E&nbsp&nbsp</th>";
		  echo "<th>&nbsp&nbspV O&nbsp&nbsp</th>";
		  echo "<th>NPT+NE</th>";
		  echo "<th>NPT+VO</th>";
		  echo "<th>NE+VO</th>";
		  echo "<th>NPT+NE+VO</th>";
		  echo "<th>Afinidad</th>";
		  echo "<th><b>Cancelar</b></th>";
		  echo "</tr>";
		  
		  if ($num > 0)
		     {
			  for($i=1;$i<=$num;$i++)
				 {
				  $row = mysql_fetch_array($res);  	  
					  
				  if (is_integer($i/2))
	                 $wclass="fila1";
	                else
	                   $wclass="fila2";
				  
				  $whab = $row[0];
				  $whis = $row[1];
				  $wing = $row[2];
				  $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
				  $wdpa = $row[12];     
			      $wtid = $row[11];
			      $wreg = $row[7];
			            	            
			      echo "<tr class='".$wclass."'>";
			      echo "<INPUT TYPE='hidden' NAME=wid[".$i."] VALUE='".$wreg."'>";   //Registro hidden
			      
			      $q = " SELECT count(*) "
			          ."   FROM ".$wbasedato."_000020 "
			          ."  WHERE habhis = '".$whis."'"
			          ."    AND habing = '".$wing."'"
			          ."    AND habcod = '".$whab."'";
			      $reshab = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
			      $rowhab = mysql_fetch_array($reshab); 
			      
			      if ($rowhab[0] > 0) 
			         echo "<td align=center><font size=3><b>".$whab."</b></font></td>";
			        else
			           echo "<td align=center><font size=3>&nbsp</font></td>"; 
			           
			      echo "<td align=center><font size=3><b>".$whis." - ".$wing."</b></font></td>";
			      echo "<td align=left  ><font size=3><b>".$wpac."</b></font></td>";
			      
			      if ($row[10] == "NPT")
			         echo "<td align=center bgcolor=0000FF><INPUT TYPE='radio' NAME=wtip[".$i."] value='NPT' onclick='enter()' CHECKED></td>";
			        else
			           echo "<td align=center><INPUT TYPE='radio' NAME=wtip[".$i."] value='NPT' onclick='enter()'></td>";
			      if ($row[10] == "NE")
			         echo "<td align=center bgcolor=0000FF><INPUT TYPE='radio' NAME=wtip[".$i."] value='NE' onclick='enter()' CHECKED></td>";
			        else
			           echo "<td align=center><INPUT TYPE='radio' NAME=wtip[".$i."] value='NE' onclick='enter()'></td>"; 
			      if ($row[10] == "VO")
			         echo "<td align=center bgcolor=0000FF><INPUT TYPE='radio' NAME=wtip[".$i."] value='VO' onclick='enter()' CHECKED></td>";
			        else
			           echo "<td align=center><INPUT TYPE='radio' NAME=wtip[".$i."] value='VO' onclick='enter()'></td>"; 
			      if ($row[10] == "NPT+NE")
			         echo "<td align=center bgcolor=0000FF><INPUT TYPE='radio' NAME=wtip[".$i."] value='NPT+NE' onclick='enter()' CHECKED></td>";
			        else
			           echo "<td align=center><INPUT TYPE='radio' NAME=wtip[".$i."] value='NPT+NE' onclick='enter()'></td>";
			      if ($row[10] == "NPT+VO")
			         echo "<td align=center bgcolor=0000FF><INPUT TYPE='radio' NAME=wtip[".$i."] value='NPT+VO' onclick='enter()' CHECKED></td>";
			        else
			           echo "<td align=center><INPUT TYPE='radio' NAME=wtip[".$i."] value='NPT+VO' onclick='enter()'></td>"; 
			      if ($row[10] == "NE+VO")
			         echo "<td align=center bgcolor=0000FF><INPUT TYPE='radio' NAME=wtip[".$i."] value='NE+VO' onclick='enter()' CHECKED></td>";
			        else
			           echo "<td align=center><INPUT TYPE='radio' NAME=wtip[".$i."] value='NE+VO' onclick='enter()'></td>";
			      if ($row[10] == "NPT+NE+VO")
			         echo "<td align=center bgcolor=0000FF><INPUT TYPE='radio' NAME=wtip[".$i."] value='NPT+NE+VO' onclick='enter()' CHECKED></td>";
			        else
			           echo "<td align=center><INPUT TYPE='radio' NAME=wtip[".$i."] value='NPT+NE+VO' onclick='enter()'></td>";         
			      
			      //======================================================================================================
			      //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
			      $wafin=clienteMagenta($wdpa,$wtid,&$wtpa,&$wcolorpac);
			      if ($wafin)
				     echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
				    else
				      echo "<td>&nbsp</td>";
				  //====================================================================================================== 
				  echo "<td align=center><INPUT TYPE='radio' NAME=wcanc[".$i."] onclick='enter()'></td>";    
			      echo "</tr>";
			     }	     
			  }
			 else
			    echo "NO HAY HABITACIONES OCUPADAS"; 
		  echo "</table>"; 
		  
		  unset($wccohos);   //La destruyo para que el vuelva a entrar al if inicial, donde esta el if de los 'or'
		   
		  echo "<br>";
	            
	      $wini="N";
	        
	      //On 
	      //echo "<meta http-equiv='refresh' content='60;url=nutricion.php?wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wini=".$wini."&wcco=".$wcco."'>";
		                 
		  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	      echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	      echo "<input type='HIDDEN' name='wini' value='".$wini."'>";
	      
	      echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
	      echo "<input type='HIDDEN' name='wfec' value='".$wfec."'>";
	      
	      echo "<tr>";  
	      echo "<td align=center colspan=7><A href='nutricion1.php?wtabcco=".$wtabcco."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."'><b>Retornar</b></A></td>"; 
	      echo "</tr>";
         }
     }
    echo "</form>";
	  
    echo "<br>";
    echo "<table>"; 
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register



include_once("free.php");

?>