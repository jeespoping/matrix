<head>
  <title>ESTADISTICAS DE NUTRICION</title>
</head>
<body>
<script type="text/javascript">
	function enter()
	{
	 document.forms.estnutricion.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *          ESTADISTICAS DE NUTRICION          *
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
 	
  

  

  include_once("root/comun.php");
 
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));
     
  
 	                                            // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="2017-08-14";                      // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                            // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
                           
	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
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
	         
	      if ($row[0] == "invecla")
	         $winvecla=$row[1];    
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  encabezado("ESTADISTICAS NUTRICION",$wactualiz, "clinica");  
       
  
  //==================================================================================================================================================
  //********************************************  E M P I E Z A   F U N C I O N   E S T A D I S T I C A  *********************************************
  //==================================================================================================================================================
  //Con esta funciòn se generan todas las estadisticas por Servicio, Nutricionista, Empresa, Diagnostico e ingreso o soportes nuevos y viejos.  
  //Teniendo en cuenta la cantidad de pacientes y el tipo de nutriciòn.
  //Tambièn se generan las estadisticos para los mimos item especificados arriba pero teniendo en cuenta la cantidad de soportes o tipos de nutriciòn,
  //NO por paciente. Es decir existen 3 tipos de nutriciòn basicos: NPT, NE y VO, pero la combinaciòn entre ellos cuenta para cada uno de los soportes, 
  //osea: NPT+NE: cuenta 1 para NPT y 1 para NE. Es decir se atendio un paciente pero se prestaron 2 soportes.
  //Tambièn se generan las estadisticas de Soportes especiales y Soportes del Tracto GastroIntestinal.
  //Entonces la funciòn se divide en dos partes la 1ra que corresponda a las estadisticas basadas en la cantidad de pacientes y la 2da parte que se 
  //basa en la cantidad de soportes.
  //==================================================================================================================================================
  // 2017-08-14 Jonatan Lopez. Se agrega al listado de centros de costos a urgencias.
  //==================================================================================================================================================
  // 2017-02-10 Arleyda Insignares. Se agrega condición con el campo ccoemp en caso de que el Query utilice la tabla costosyp_000005.
  //================================================================================================================================================== 
  // Julio 4 de 2012 Viviana Rodas
  // Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos de un grupo seleccionado y dibujarSelect 
  // que dibuja el select con los centros de costos obtenidos de la primera funcion.
  // De igual forma en las consultas de cambio .substr($wcco,0,strpos($wcco,'-')-1). por .trim(substr($wcco,0,strpos($wcco,'-')-0)). porque le
  // estaba cortando el ultimo numero al centro de costo.  
  ///========================================================================================================================================\\
  //Modificacion: Abril 2 de 2012---> Camilo Zapata
  //========================================================================================================================================\\       
  //Se agrega la dieta básica COM(COMPLEMENTO) y todas las combinaciones pertinentes de esta con las demas dietas, 
  //en los estádisticos para todos los tipos de servicios, siguiendo la misma estructura ya establecida.
  //     NOTA: falta clarificar los conceptos de dieta especial y gastrointestinal, para completar los estadísticos (lineas 465 y 469)
  //========================================================================================================================================\\

//==================================================================================================================================================

  function estadistica($wdesc, $wresq, $wnum)
   {   
	$wmatriz = Array();
	
    $row = mysql_fetch_array($wresq);
			   
    //====================================================================================================================
    //En este procedimiento lleno la matriz de cada uno de los TIPOS DE ESTADISTICA con sus diferentes tipos de nutricion.
    //====================================================================================================================
    $j=0;
    $i=1;
    while ($i <= $wnum)
      {   
	   $j++;       
       $wmatriz[$j][1]=$row[0];
       $wmatriz[$j][2]=$row[1];
       
       while ($wmatriz[$j][1]==$row[0] and $i <= $wnum)   //Rompimiento por tipo de estadistica e $i<=$num
          {
	       switch ($row[2])  
	         {
	          case "NPT":
	             {
		          $wmatriz[$j][3]=$row[3];
		         }    
	             break;
	          case "NE":
	             {
		          $wmatriz[$j][4]=$row[3];   
	             }    
	             break;
	          case "VO":
	             {
		          $wmatriz[$j][5]=$row[3];   
	             }    
	             break;
			  case "COM":
	             {
		          $wmatriz[$j][6]=$row[3];   
	             }    
	             break;
	          case "NPT+NE":
	             {
		          $wmatriz[$j][7]=$row[3];   
	             }    
	             break;
	          case "NPT+VO":
	             {
		          $wmatriz[$j][8]=$row[3];   
	             }    
	             break;
	          case "NE+VO":
	             {
		          $wmatriz[$j][9]=$row[3];   
	             }    
	             break;
	          case "NPT+NE+VO":
	             {
		          $wmatriz[$j][10]=$row[3];   
	             }    
	             break;              
			//nuevos tipos de dietas
			  case "NPT+COM":
	             {
		          $wmatriz[$j][11]=$row[3];   
	             }    
	             break;
			  case "NE+COM":
	             {
		          $wmatriz[$j][12]=$row[3];   
	             }    
	             break;
			  case "COM+VO":
	             {
		          $wmatriz[$j][13]=$row[3];   
	             }    
	             break;
			  case "NPT+NE+VO+COM":
	             {
		          $wmatriz[$j][14]=$row[3];   
	             }    
	             break;
			  case "NPT+NE+COM":
	             {
		          $wmatriz[$j][15]=$row[3];   
	             }    
	             break;
			  case "NPT+VO+COM":
	             {
		          $wmatriz[$j][16]=$row[3];   
	             }    
	             break;
			  case "NE+VO+COM":
	             {
		          $wmatriz[$j][17]=$row[3];   
	             }    
	             break;
	         } //terminan nuevos tipos de dietas
	             
	       $row = mysql_fetch_array($wresq);
	       $i++;            
          }
          
        $wmatriz[$j][18]=0;      
        $wmatriz[$j][19]=0;
      }
     //==========================================================================================================  
    
  
	 //==========================================================================================================   
	 //Desde aca hago la impresion del cuadro estadistico   
	 //==========================================================================================================    
	 
	 //==========================================================================================================
	 //*** 1ra P A R T E ***
	 //E S T A D I S T I C A S   P O R   P A C I E N T E 
	 //==========================================================================================================   
	 echo "<center><table cellspacing=1>";
	   
	 echo "<tr class=encabezadoTabla >";
	 echo "<td align=center colspan=2 rowspan=2><b>".$wdesc."</b></td>";
	 echo "<td align=center colspan=16><b>Tipo de Nutricion</b></td>";
	 echo "<tr class=encabezadoTabla>";
	 echo "<td align=center><b>PARENTERAL</b></td>";
	 echo "<td align=center><b>&nbspENTERAL&nbsp</b></td>";
	 echo "<td align=center><b>&nbspVIA ORAL&nbsp</b></td>";
	 echo "<td align=center><b>&nbspCOMPLEMENTO&nbsp</b></td>";
	 echo "<td align=center><b>PARENTERAL<br>+ ENTERAL</b></td>";
	 echo "<td align=center><b>PARENTERAL<br>+VIA ORAL</b></td>";
	 echo "<td align=center><b>ENTERAL<br>+VIA ORAL</b></tdh>";
	 echo "<td align=center><b>PARENTERAL<br>+ ENTERAL+ <br>VIA ORAL</b></td>";
	 //nuevos tipos de dieta
	 echo "<td align=center><b>PARENTERAL<br>+ COMPLEMENTO</b></td>";
	 echo "<td align=center><b>ENTERAL+ <br>COMPLEMENTO</b></td>";
	 echo "<td align=center><b>COMPLEMENTO+ <br>VIA ORAL</b></td>";
	 echo "<td align=center><b>PARENTERAL<br>+ ENTERAL+ <br>VIA ORAL+ <br>COMPLEMENTO</b></td>";
	 echo "<td align=center><b>PARENTERAL<br>+ ENTERAL+ <br>COMPLEMENTO</b></td>";
	 echo "<td align=center><b>PARENTERAL<br>+ <br>VIA ORAL+ <br>COMPLEMENTO</b></td>";
	 echo "<td align=center><b>ENTERAL+ <br>VIA ORAL+ <br>COMPLEMENTO</b></td>";
	//hasta acá
	 echo "<td align=center><b>Total</b></td>";
	 echo "</tr>";

	 $wtotnpt=0;
	 $wtotne=0;
	 $wtotvo=0;
	 $wtotcom=0; //agregada
	 $wtotnptne=0;
	 $wtotnptvo=0;
	 $wtotnevo=0;
	 $wtotnptnevo=0;
	 //variables de trabajo para nuevos tipos de dieta
	 $wtotnptcom=0;
	 $wtotnecom=0;
	 $wtotcomvo=0;
	 $wtotnptnevocom=0;
	 $wtotnptnecom=0;
	 $wtotnptvocom=0;
	 $wtotnevocom=0;
	 //hasta acá
	 $wtottot=0;
	 $wtotfila=0;

	 if (isset($j))
	  {
	   for ($i=1;$i<=$j;$i++)
	      {
		   if (is_integer($i/2))
	          $wclass="fila1";
	         else
	            $wclass="fila2";   
		   
	       $wtotcco=0;     
	       echo "<tr class=".$wclass.">";
	       
	       for ($k=1;$k<=17;$k++)
		      {
			   if (isset($wmatriz[$i][$k]))
			      if ($k>2) 
			         {
			          echo "<td align=center>".$wmatriz[$i][$k]."</td>";
			          $wtotcco=$wtotcco+$wmatriz[$i][$k];                //Total por fila
			         } 
			        else
			           echo "<td align=left>".$wmatriz[$i][$k]."</td>"; 
			     else
			        echo "<td>&nbsp</td>"; 
			   
			   //Acumulo el total por tipo de nutricion
			   switch ($k)  
			       {
				    case 3:
				      { if (isset($wmatriz[$i][$k])) $wtotnpt=$wtotnpt     		 + $wmatriz[$i][$k]; }
				      break;   
				    case 4:
				      { if (isset($wmatriz[$i][$k])) $wtotne=$wtotne       		 + $wmatriz[$i][$k]; }
				      break;
				    case 5:
				      { if (isset($wmatriz[$i][$k])) $wtotvo=$wtotvo       		 + $wmatriz[$i][$k]; }
				      break;
					case 6:
				      { if (isset($wmatriz[$i][$k])) $wtotcom=$wtotcom       	 + $wmatriz[$i][$k]; }
				      break;
				    case 7:
				      { if (isset($wmatriz[$i][$k])) $wtotnptne=$wtotnptne		 + $wmatriz[$i][$k]; }
				      break;
				    case 8:
				      { if (isset($wmatriz[$i][$k])) $wtotnptvo=$wtotnptvo 		 + $wmatriz[$i][$k]; }
				      break;
				    case 9:
				      { if (isset($wmatriz[$i][$k])) $wtotnevo=$wtotnevo   		 + $wmatriz[$i][$k]; }
				      break;
				    case 10:
				      { if (isset($wmatriz[$i][$k])) $wtotnptnevo=$wtotnptnevo   + $wmatriz[$i][$k]; }
				      break;
					  //nuevos
				    case 11:
				      { if (isset($wmatriz[$i][$k])) $wtotnptcom=$wtotnptcom     		 + $wmatriz[$i][$k]; }
				      break;
				    case 12:
				      { if (isset($wmatriz[$i][$k])) $wtotnecom=$wtotnecom      		 + $wmatriz[$i][$k]; }
				      break	;
				    case 13:
				      { if (isset($wmatriz[$i][$k])) $wtotcomvo=$wtotcomvo       		 + $wmatriz[$i][$k]; }
				      break;
				    case 14:
				      { if (isset($wmatriz[$i][$k])) $wtotnptnevocom=$wtotnptnevocom     + $wmatriz[$i][$k]; }
				      break;
				    case 15:
				      { if (isset($wmatriz[$i][$k])) $wtotnptnecom=$wtotnptnecom         + $wmatriz[$i][$k]; }
				      break;					  
					case 16:
				      { if (isset($wmatriz[$i][$k])) $wtotnptvocom=$wtotnptvocom 		 + $wmatriz[$i][$k]; }
				      break;
				    case 17:
				      { if (isset($wmatriz[$i][$k])) $wtotnevocom=$wtotnevocom    		 + $wmatriz[$i][$k]; }
				      break;
				   }       
			  }
		   echo "<td align=right>".$wtotcco."</td>"; 
		   $wtottot = $wtottot+$wtotcco;      
	      } 
	    
	    echo "<tr class=encabezadoTabla>";
		echo "<td colspan=2 align=left><b>Totales .... </b></td>";
		echo "<td align=center><b>".$wtotnpt."</b></td>";
		echo "<td align=center><b>".$wtotne."</b></td>";
		echo "<td align=center><b>".$wtotvo."</b></td>";
		echo "<td align=center><b>".$wtotcom."</b></td>"; 
		echo "<td align=center><b>".$wtotnptne."</b></td>";
		echo "<td align=center><b>".$wtotnptvo."</b></td>";
		echo "<td align=center><b>".$wtotnevo."</b></td>";
		echo "<td align=center><b>".$wtotnptnevo."</b></td>";
		//valores para nuevos tipos de dieta
		echo "<td align=center><b>".$wtotnptcom."</b></td>";
		echo "<td align=center><b>".$wtotnecom."</b></td>";
		echo "<td align=center><b>".$wtotcomvo."</b></td>";
		echo "<td align=center><b>".$wtotnptnevocom."</b></td>";
		echo "<td align=center><b>".$wtotnptnecom."</b></td>";
		echo "<td align=center><b>".$wtotnptvocom."</b></td>";
		echo "<td align=center><b>".$wtotnevocom."</b></td>";
		//hasta acá
		echo "<td align=center><b>".$wtottot."</b></td>";
		echo "</tr>";
		 
		echo "</table>";
		echo "<br><br>";  
	    //==============================================================================================================
	    //==============================================================================================================   
	      
		
		
	    //==============================================================================================================
		//*** 2da P A R T E ***
		//E S T A D I S T I C A S   P O R   S O P O R T E 
		//==============================================================================================================
	    //Si se esta en el punto de las estadisticas por NUTRICIONISTAS se adiciona el cuadro por ** TIPO DE SOPORTE **
	    //==============================================================================================================
	    							    //** O J O ** Con solo quitar o comentar esta linea salen todas las estadisticas   
	    ////if ($wdesc == "NUTRICIONISTA")  //            tambien por soporte, no solo para "Nutricionista". La lìnea del IF  
	       {
		     ///////////////////
		     
		     echo "<br><br>";
		     echo "<center><table>";
		     echo "<tr class=seccion1>";
		     echo "<td align=center colspan=10><font size=4><b>SOPORTES POR *** ".$wdesc." ***</b></font></td>";
		     echo "</tr>";
		     echo "</table>";
		     
		     
		     echo "<center><table cellspacing=1>";
	   
			 echo "<tr class=encabezadoTabla >";
			 echo "<td align=center colspan=2 rowspan=2><b>".$wdesc."</b></td>";
			 echo "<td align=center colspan=16><b>Tipo de Nutricion</b></td>";
			 echo "<tr class=encabezadoTabla>";
			 echo "<td align=center><b>PARENTERAL</b></td>";
			 echo "<td align=center><b>&nbspENTERAL&nbsp</b></td>";
			 echo "<td align=center><b>&nbspVIA ORAL&nbsp</b></td>";
			 echo "<td align=center><b>&nbspCOMPLEMENTO&nbsp</b></td>";
			 echo "<td align=center><b>Total</b></td>";
			 echo "<td align=center><b>&nbspESPECIAL&nbsp</b></td>";
			 echo "<td align=center><b>&nbspTRACTO<br>GASTROINTESTINAL&nbsp</b></td>";
			 //echo "<td align=center><b>Total</b></td>";
			 echo "</tr>";

			 $wtotnpt=0;
			 $wtotne=0;
			 $wtotvo=0;
			 $wtotcom=0;
			 $wtotes=0;
			 $wtottg=0;
			 $wtottot=0;
			 $wtotfila=0;
			 $wtottotesp=0;

			 if (isset($j))
			  {
			   for ($i=1;$i<=$j;$i++)
			      {
				   if (is_integer($i/2))
			          $wclass="fila1";
			         else
			            $wclass="fila2";   
				   
			       $wtotcco=0; 
			       $wtotesp=0;    
			       echo "<tr class=".$wclass.">";
			       
			       //Primero resumo las cantidades en las celdas de Parenteral, Enteral y Via Oral por cada fila
			       for ($k=1;$k<=17;$k++)
			          {
				       if (!isset($wmatriz[$i][$k]))  //Si no tiene valor el arreglo en esa posicion la pongo en cero
				          $wmatriz[$i][$k]=0;
				          //agregado.. acá se ubican todas las dietas que incluyen una básica específica
				       if ($k>2)
				         {
					      if ($k==7 or $k==8 or $k==10 or  $k==11 or  $k==14 or $k==15 or $k==16)                                     //Parenteral NPT
						    {  $wmatriz[$i][3]=$wmatriz[$i][3] + $wmatriz[$i][$k];}
						  if ($k==7 or $k==9 or $k==10 or $k==12 or $k==14 or $k==15 or $k==17)                                     //Enteral NE
						     {  $wmatriz[$i][4]=$wmatriz[$i][4] + $wmatriz[$i][$k];}
						  if ($k==8 or $k==9 or $k==10 or $k==13 or $k==14 or $k==16 or $k==17)                                     //Via Oral
						     {  $wmatriz[$i][5]=$wmatriz[$i][5] + $wmatriz[$i][$k]; }
						  if ($k==11 or $k==12 or $k==13 or $k==14 or $k==15 or $k==16 or $k==17)                                     //Complementaria
						     {  $wmatriz[$i][6]=$wmatriz[$i][6] + $wmatriz[$i][$k]; }
						  if ($k==3 or $k==4 or $k==6 or $k==7 or $k==8 or $k==9 or $k==10 or $k==11 or $k==12 or$k==13 or $k==14 or $k==15 or $k==16 or $k==17 )        //Soporte Especial
						     {  $wmatriz[$i][18]=$wmatriz[$i][18] + $wmatriz[$i][$k]; }
						  if ($k==4 or $k==5 or $k==6 or $k==7 or $k==8 or $k==9)          //Tracto GastroIntestinal
						     {  $wmatriz[$i][19]=$wmatriz[$i][19] + $wmatriz[$i][$k]; }      
						 }    
			          }    
			       
			       for ($k=1;$k<=6;$k++)
				      {
					   if (isset($wmatriz[$i][$k]))
					      {
						   if ($k > 2)
						      {   
					       	   echo "<td align=center>".$wmatriz[$i][$k]."</td>";
					           $wtotcco=$wtotcco+$wmatriz[$i][$k];                //Total por fila
				              }
				             else
					            echo "<td>".$wmatriz[$i][$k]."</td>";  
					      } 
					     else
					       echo "<td>&nbsp</td>";
					             
					   
					   //Acumulo el total por tipo de nutricion
					   switch ($k)  
					       {
						    case ($k==3):
						      { if (isset($wmatriz[$i][$k])) $wtotnpt=$wtotnpt + $wmatriz[$i][$k]; }
						      break;   
						    case ($k==4):
						      { if (isset($wmatriz[$i][$k])) $wtotne=$wtotne   + $wmatriz[$i][$k]; }
						      break;
						    case ($k==5):
						      { if (isset($wmatriz[$i][$k])) $wtotvo=$wtotvo   + $wmatriz[$i][$k]; }
						      break;
							case ($k==6):
						      { if (isset($wmatriz[$i][$k])) $wtotcom=$wtotcom   + $wmatriz[$i][$k]; }
						      break;
						   }       
					  }
				   echo "<td align=right bgcolor=dddddd>".$wtotcco."</td>"; 
				   $wtottot = $wtottot+$wtotcco; 
				   
				   
				   for ($k=18;$k<=19;$k++)
				      {
					   if (isset($wmatriz[$i][$k]))
					      {
						   echo "<td align=center>".$wmatriz[$i][$k]."</td>";
					       $wtotesp=$wtotesp+$wmatriz[$i][$k];                //Total por fila
					      } 
					     else
					       echo "<td align=right>&nbsp</td>";
					             
					   
					   //Acumulo el total por tipo de nutricion
					   switch ($k)  
					       {
						    case ($k==18):
						      { if (isset($wmatriz[$i][$k])) $wtotes=$wtotes   + $wmatriz[$i][$k]; }
						      break;
						    case ($k==19):
						      { if (isset($wmatriz[$i][$k])) $wtottg=$wtottg   + $wmatriz[$i][$k]; }
						      break;   
						   }       
					  }
				   //echo "<td align=right bgcolor=dddddd>".$wtotesp."</td>"; 
				   //$wtottotesp = $wtottotesp+$wtotesp;
				  }
		      }
		     //////////////////// 
		     echo "<tr class=encabezadoTabla>";
			 echo "<td colspan=2 align=left><b>Totales .... </b></td>";
			 echo "<td align=center><b>".$wtotnpt."</b></td>";
			 echo "<td align=center><b>".$wtotne."</b></td>";
			 echo "<td align=center><b>".$wtotvo."</b></td>";
			 echo "<td align=center><b>".$wtotcom."</b></td>";
			 echo "<td align=center><b>".$wtottot."</b></td>";
			 echo "<td align=center><b>".$wtotes."</b></td>";
			 echo "<td align=center><b>".$wtottg."</b></td>";
			 //echo "<td align=right><b>".$wtottotesp."</b></td>";
			 echo "</tr>";
			 
			 echo "</table>";
			 echo "<br><br>";
	       }
	     //=============================================================================================================
	     //=============================================================================================================  
	  }    
	}
	
	
  //===============================================================================================================================================
  //********************************************  T E R M I N A   F U N C I O N   E S T A D I S T I C A  ******************************************
  //===============================================================================================================================================
  
    
  //FORMA ================================================================
  echo "<form name='altas' action='estadisticas_nutricion.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
   if (!isset($wfec_i) or trim($wfec_i) == "" or !isset($wfec_f) or trim($wfec_f) == "" or !isset($wcco) or trim($wcco) == "" )
     {     
	  
	     echo "<br>";
		 echo "<br>";
		  
	  //**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		$cco="Ccohos,Ccourg";
		$sub="off";
		$tod="Todos";
		$ipod="off";
		//$cco=" ";
		$centrosCostos = consultaCentrosCostos($cco);
					
		echo "<table align='center' border=0>";		
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
		echo $dib;
		echo "</table>";
	  
      echo "<br>";
	  
	  echo "<center><table cellspacing=1>";
	  echo "<tr class=seccion1>";
      echo "<td align=center><b>Fecha Inicial</b><br>";
      campoFecha("wfec_i");
      echo "</td>";
  	  echo "<td align=center><b>Fecha Final</b><br>";
      campoFecha("wfec_f");
      echo "</td>";
      echo "</tr>";
      echo "</table>";
	  
	  echo "<br>";
	  
	  echo "<center><table cellspacing=1>";	    
	  echo "<tr><td align=center bgcolor=cccccc colspan=2></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else 
      { 
	  
	   //================================================================================================================
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   //////////////////////////////////////////////// P O R   S E R V I C I O /////////////////////////////////////////
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   //================================================================================================================
	      
	   echo "<center><table cellspacing=0>";
	   echo "<tr class=encabezadoTabla>";
	   echo "<td align=center><b>Fecha Inicial</b></td>";
	   echo "<td>&nbsp&nbsp&nbsp</td>";
	   echo "<td align=center><b>Fecha Final</b></td>";
	   echo "</tr><tr class=seccion1>";
	   echo "<td align=center>".$wfec_i."</td>";
	   echo "<td>&nbsp&nbsp&nbsp</td>";
	   echo "<td align=center>".$wfec_f."</td>";
	   echo "</tr>";
	   echo "</table>";
	   
	   //========================================================================================================   
	   //Consulta Nutricion por Servicio  
	   if ($wtabcco == 'costosyp_000005'){

		   	$q = " SELECT habcco, ".$wtabcco.".cconom, nuttip, COUNT(*) "
		       ."   FROM ".$wbasedato."_000056, ".$wtabcco.", ".$wbasedato."_000011, ".$wbasedato."_000020 "
		       ."  WHERE nutfec BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
		       ."    AND nuthab = habcod "
		       ."    AND habcco LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
		       ."    AND habcco = ".$wbasedato."_000011.ccocod "
		       ."    AND nutest = 'on' "
		       ."    AND ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
		       ."    AND ".$wtabcco.".Ccoemp = '".$wemp_pmla."' "
		       ."  GROUP BY 1, 2, 3 "
		       ."  ORDER BY 1, 2, 3 ";

	   }
	   else{

			$q = " SELECT habcco, ".$wtabcco.".cconom, nuttip, COUNT(*) "
		       ."   FROM ".$wbasedato."_000056, ".$wtabcco.", ".$wbasedato."_000011, ".$wbasedato."_000020 "
		       ."  WHERE nutfec BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
		       ."    AND nuthab = habcod "
		       ."    AND habcco LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
		       ."    AND habcco = ".$wbasedato."_000011.ccocod "
		       ."    AND nutest = 'on' "
		       ."    AND ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
		       ."  GROUP BY 1, 2, 3 "
		       ."  ORDER BY 1, 2, 3 ";


	   }    

	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);

	   if ($num > 0)
	      {
		   echo "<br><br>";
		   echo "<center><table>";
		   echo "<tr class=seccion1>";
		   echo "<td align=center colspan=10><font size=4><b>PACIENTES POR *** SERVICIO ***</b></font></td>";
		   echo "</tr>";
		   echo "</table>";   
		      
		   estadistica("SERVICIO", $res, $num); 
		  }
		 else
		    {
		     echo "<br><br>";
		     echo "<center><table>";
		     echo "<tr class=seccion1>";
		     echo "<td align=center colspan=10><font size=4><b>NO EXISTEN REGISTROS PARA ESTE RANGO DE FECHAS</b></font></td>";
		     echo "</tr>";
		     echo "</table>";   
		    } 
	    
	   //================================================================================================================
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   ////////////////////////////////////////// P O R   N U T R I C I O N I S T A /////////////////////////////////////
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   //================================================================================================================
	   
	   //========================================================================================================   
	   //Consulta Nutricion por Servicio  
	   
	   if ($wtabcco == 'costosyp_000005'){
		   
		    $q = " SELECT nutnut, descripcion, nuttip, COUNT(*) "
		       ."   FROM ".$wbasedato."_000056, ".$wtabcco.", ".$wbasedato."_000011, ".$wbasedato."_000020, usuarios "
		       ."  WHERE nutfec              BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
		       ."    AND nuthab              = habcod "
		       ."    AND habcco              LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
		       ."    AND habcco              = ".$wbasedato."_000011.ccocod "
		       ."    AND nutest              = 'on' "
		       ."    AND ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
		       ."    AND nutnut              = codigo "
		       ."    AND ".$wtabcco.".Ccoemp = '".$wemp_pmla."' "
		       ."  GROUP BY 1, 2, 3 "
		       ."  ORDER BY 1, 2, 3 ";
	   }
	   else{

		   	$q = " SELECT nutnut, descripcion, nuttip, COUNT(*) "
			       ."   FROM ".$wbasedato."_000056, ".$wtabcco.", ".$wbasedato."_000011, ".$wbasedato."_000020, usuarios "
			       ."  WHERE nutfec              BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
			       ."    AND nuthab              = habcod "
			       ."    AND habcco              LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
			       ."    AND habcco              = ".$wbasedato."_000011.ccocod "
			       ."    AND nutest              = 'on' "
			       ."    AND ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
			       ."    AND nutnut              = codigo "
			       ."  GROUP BY 1, 2, 3 "
			       ."  ORDER BY 1, 2, 3 ";

	   }

	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   echo "<center><table>";
		   echo "<tr class=seccion1>";
		   echo "<td align=center colspan=10><font size=4><b>PACIENTES POR *** NUTRICIONISTA *** </b></font></td>";
		   echo "</tr>";
		   echo "</table>";
	      
		   estadistica("NUTRICIONISTA", $res, $num);
		  }
	    
	   //================================================================================================================
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   ///////////////////////////////////////////// P O R   E M P R E S A //////////////////////////////////////////////
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   //================================================================================================================
	   
	   //================================================================================================================   
	   //Consulta Nutricion por Empresa  
	   $q = " SELECT '9999', 'Particulares', nuttip, COUNT(*) "
	       ."   FROM ".$wbasedato."_000056, ".$wbasedato."_000020, ".$wbasedato."_000016, root_000037 "
	       ."  WHERE nutfec BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND nuthab = habcod "
	       ."    AND habcco LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND nutest = 'on' "
	       ."    AND nuthis = inghis "
	       ."    AND nuting = inging "
	       ."    AND ingres = oriced "
	       ."    AND nuthis = orihis "
	       ."    AND oriori = '".$wemp_pmla."'"
	       ."  GROUP BY 1, 2, 3 "
	       ."  UNION ALL "
	       ." SELECT ingres, ingnre, nuttip, COUNT(*) "
	       ."   FROM ".$wbasedato."_000056, ".$wbasedato."_000020, ".$wbasedato."_000016 "
	       ."  WHERE nutfec              BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND nuthab              = habcod "
	       ."    AND habcco              LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND nutest              = 'on' "
	       ."    AND nuthis              = inghis "
	       ."    AND nuting              = inging "
	       ."    AND ingres NOT IN (SELECT oriced "
	       ."                         FROM root_000037 "
	       ."                        WHERE oriori = '".$wemp_pmla."' ) "
	       ."  GROUP BY 1, 2, 3 "    
	       ."  ORDER BY 1, 2, 3 ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   if ($num > 0)
	      {
		   echo "<center><table>";
		   echo "<tr class=seccion1>";
		   echo "<td align=center colspan=10><font size=4><b>PACIENTES POR *** EMPRESA ***</b></font></td>";
		   echo "</tr>";
		   echo "</table>";
	      
		   estadistica("EMPRESA", $res, $num);   
		  }
	    
	   //================================================================================================================
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   ////////////////////////////////////////////I N G R E S O S   N U E V O S/////////////////////////////////////////
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   //================================================================================================================
	   $wmatriz = Array();
	    
	   	   
	   //Inicializo la Matriz
	   for ($i=1;$i<=2;$i++)
	      for($j=1;$j<=18;$j++)
	         $wmatriz[$i][$j]=0;
	    
	   //========================================================================================================   
	   //Consulta Nutricion Ingresos nuevos o no  
	   $q = " SELECT nuthis, nuting, nuttip, nutfec "
	       ."   FROM ".$wbasedato."_000056, ".$wbasedato."_000020 "
	       ."  WHERE nutfec              BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND nuthab              = habcod "
	       ."    AND habcco              LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND nutest              = 'on' "
	       ."  ORDER BY 1, 2 ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	    
	   if ($num > 0)
	     {
		  echo "<center><table>";
		  echo "<tr class=seccion1>";
		  echo "<td align=center colspan=10><font size=4><b>PACIENTES *** INGRESOS NUEVOS ***</b></font></td>";
		  echo "</tr>";
		  echo "</table>";   
		     
	      for ($i=1;$i<=$num;$i++)
		      {
			   $row = mysql_fetch_array($res);
			   
			   //Aca busco si hay una consulta anterior a la fecha inicial dada en el mismo ingreso de la historia
			   //si existe algun registro quiere decir que la consulta o registro que trae del query de arriba no
			   //es de primera vez.
			   $q = " SELECT COUNT(*) "
			       ."   FROM ".$wbasedato."_000056 "
			       ."  WHERE nuthis = '".$row[0]."'"
			       ."    AND nuting = '".$row[1]."'"
			       ."    AND nutfec < '".$row[3]."'";
			   $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			   $rowcon = mysql_fetch_array($rescon);
			   
			   if ($rowcon[0] == 0)
			      {
			       //Por aca sumo las consultas de ** 1ra vez ** con su respectivo tipo de nutricion
				   switch ($row[2])  
			         {
			          case "NPT":
			             { $wmatriz[1][1]++; }    
			             break;
			          case "NE":
			             { $wmatriz[1][2]++; }    
			             break;
			          case "VO":
			             { $wmatriz[1][3]++; }    
			             break;
			          case "COM":
			             { $wmatriz[1][4]++; }  
			             break;
			          case "NPT+NE":
			             { $wmatriz[1][5]++; }    
			             break;
			          case "NPT+VO":
			             { $wmatriz[1][6]++; }    
			             break;
			          case "NE+VO":
			             { $wmatriz[1][7]++; }    
			             break;
			          case "NPT+NE+VO":
			             { $wmatriz[1][8]++; }    
			             break;
						 //nuevos tipos de dieta
					  case "NPT+COM":
						 { $wmatriz[1][9]++; }    
						 break;
					  case "NE+COM":
						 { $wmatriz[1][10]++; }    
						 break;
					  case "COM+VO":
						 { $wmatriz[1][11]++; }    
						 break;
					  case "NPT+NE+VO+COM":
						 { $wmatriz[1][12]++; }    
						 break;
					  case "NPT+NE+COM":
						 { $wmatriz[1][13]++; }    
						 break;
					  case "NPT+VO+COM":
						 { $wmatriz[1][14]++; }    
						 break;
					  case "NE+VO+COM":
						 { $wmatriz[1][15]++; }    
						 break;
					 } //hasta acá
		          }  
			     else
			       { 
				    //Por aca sumo las consultas de ** seguimiento ** con su respectivo tipo de nutricion   
			        switch ($row[2])  
			         {
			          case "NPT":
			             { $wmatriz[2][1]++; }    
			             break;
			          case "NE":
			             { $wmatriz[2][2]++; }    
			             break;
			          case "VO":
			             { $wmatriz[2][3]++; }    
			             break;
					  case "COM":
			             { $wmatriz[2][4]++; }    
			             break;
			          case "NPT+NE":
			             { $wmatriz[2][5]++; }    
			             break;
			          case "NPT+VO":
			             { $wmatriz[2][6]++; }    
			             break;
			          case "NE+VO":
			             { $wmatriz[2][7]++; }    
			             break;
			          case "NPT+NE+VO":
			             { $wmatriz[2][8]++; }    
			             break;  
				 //nuevos tipos de dieta
					  case "NPT+COM":
						 { $wmatriz[2][9]++; }    
						 break;
					  case "NE+COM":
						 { $wmatriz[2][10]++; }    
						 break;
					  case "COM+VO":
						 { $wmatriz[2][11]++; }    
						 break;
					  case "NPT+NE+VO+COM":
						 { $wmatriz[2][12]++; }    
						 break;
					  case "NPT+NE+COM":
						 { $wmatriz[2][13]++; }    
						 break;
					  case "NPT+VO+COM":
						 { $wmatriz[2][14]++; }    
						 break;
					  case "NE+VO+COM":
						 { $wmatriz[2][15]++; }    
						 break;
					 } //hasta acá
			        
		           }  
			  }
		  
		    //==========================================================================================================   
		    //Desde aca hago la impresion del cuadro estadistico por *** INGRESOS NUEVOS ***
		    //==========================================================================================================       
		    echo "<center><table cellspacing=1>";
	   		echo "<tr class=encabezadoTabla>";
		    echo "<td align=center colspan=2 rowspan=2><b>Tipo de Consulta</b></td>";
		    echo "<td align=center colspan=16><b>Tipo de Nutricion</b></td>";
		    echo "</tr>";
		    echo "<tr class=encabezadoTabla>";
		    echo "<td align=center><b>PARENTERAL</b></td>";
		    echo "<td align=center><b>&nbspENTERAL&nbsp</b></td>";
		    echo "<td align=center><b>&nbspVIA ORAL&nbsp</b></td>";
			echo "<td align=center><b>&nbspCOMPLEMENTO&nbsp</b></td>";
		    echo "<td align=center><b>PARENTERAL<br>+ ENTERAL</b></td>";
		    echo "<td align=center><b>PARENTERAL<br>+VIA ORAL</b></td>";
		    echo "<td align=center><b>ENTERAL<br>+VIA ORAL</b></td>";
		    echo "<td align=center><b>PARENTERAL<br>+ ENTERAL+ <br>VIA ORAL</b></td>";
			//nuevos tipos de dieta
			echo "<td align=center><b>PARENTERAL<br>+ COMPLEMENTO</b></td>";
			echo "<td align=center><b>ENTERAL+ <br>COMPLEMENTO</b></td>";
			echo "<td align=center><b>COMPLEMENTO+ <br>VIA ORAL</b></td>";
			echo "<td align=center><b>PARENTERAL<br>+ ENTERAL+ <br>VIA ORAL+ <br>COMPLEMENTO</b></td>";
			echo "<td align=center><b>PARENTERAL<br>+ ENTERAL+ <br>COMPLEMENTO</b></td>";
			echo "<td align=center><b>PARENTERAL<br>+ <br>VIA ORAL+ <br>COMPLEMENTO</b></td>";
			echo "<td align=center><b>ENTERAL+ <br>VIA ORAL+ <br>COMPLEMENTO</b></td>";
			//hasta acá
		    echo "<td align=center><b>Total</b></td>";
		    echo "</tr>";

		    $wtotnpt=0;
		    $wtotne=0;
		    $wtotvo=0;
			$wtotcom=0;
		    $wtotnptne=0;
		    $wtotnptvo=0;
		    $wtotnevo=0;
		    $wtotnptnevo=0;
			//variables de trabajo de nuevos tipos de dieta
			$wtotnptcom=0;
			$wtotnecom=0;
			$wtotcomvo=0;
			$wtotnptnevocom=0;
			$wtotnptnecom=0;
			$wtotnptvocom=0;
			$wtotnevocom=0;
			//hasta acá
		    $wtottot=0;
		   
		    for ($i=1;$i<=2;$i++)
		      {
			   if (is_integer($i/2))
	              $wclass="fila1";
	             else
	                $wclass="fila2";   
			   
	           $wtotcco=0;     
	           echo "<tr class=".$wclass.">";
	           
	           if ($i==1) 
		         echo "<td align=left colspan=2><b>Consulta de 1ra vez</b></td>";
		       if ($i==2) 
		         echo "<td align=left colspan=2><b>Consulta de seguimiento</b></td>";
	           
	           for ($k=1;$k<=15;$k++)
			      {
				   if (isset($wmatriz[$i][$k]))
				      {
				       echo "<td align=right><b>".$wmatriz[$i][$k]."</b></td>";
				       $wtotcco=$wtotcco+$wmatriz[$i][$k];
			          }
				     else
				        echo "<td align=right><b>&nbsp</b></td>"; 
				   
				   //Acumulo el total por tipo de nutricion
				   switch ($k)  
				       {
					    case 1:
					      { if (isset($wmatriz[$i][$k])) $wtotnpt=$wtotnpt     		 + $wmatriz[$i][$k]; }
					      break;   
					    case 2:
					      { if (isset($wmatriz[$i][$k])) $wtotne=$wtotne       		 + $wmatriz[$i][$k]; }
					      break;
					    case 3:
					      { if (isset($wmatriz[$i][$k])) $wtotvo=$wtotvo       		 + $wmatriz[$i][$k]; }
					      break;
						case 4:
					      { if (isset($wmatriz[$i][$k])) $wtotcom=$wtotcom       	 + $wmatriz[$i][$k]; }//agregado
					      break;
					    case 5:
					      { if (isset($wmatriz[$i][$k])) $wtotnptne=$wtotnptne 		 + $wmatriz[$i][$k]; }
					      break;
					    case 6:
					      { if (isset($wmatriz[$i][$k])) $wtotnptvo=$wtotnptvo 		 + $wmatriz[$i][$k]; }
					      break;
					    case 7:
					      { if (isset($wmatriz[$i][$k])) $wtotnevo=$wtotnevo   		 + $wmatriz[$i][$k]; }
					      break;
					    case 8:
					      { if (isset($wmatriz[$i][$k])) $wtotnptnevo=$wtotnptnevo   + $wmatriz[$i][$k]; }
					      break;
						//nuevos tipos de dieta
						case 9:
					      { if (isset($wmatriz[$i][$k])) $wtotnptcom=$wtotnptcom   + $wmatriz[$i][$k]; }
					      break;
						case 10:
					      { if (isset($wmatriz[$i][$k])) $wtotnecom=$wtotnecom   + $wmatriz[$i][$k]; }
						  break;
						case 11:
					      { if (isset($wmatriz[$i][$k])) $wtotcomvo=$wtotcomvo   + $wmatriz[$i][$k]; }
					      break;
						case 12:
					      { if (isset($wmatriz[$i][$k])) $wtotnptnevocom=$wtotnptnevocom   + $wmatriz[$i][$k]; }
					      break;
						case 13:
					      { if (isset($wmatriz[$i][$k])) $wtotnptnecom=$wtotnptnecom   + $wmatriz[$i][$k]; }
					      break;
						case 14:
					      { if (isset($wmatriz[$i][$k])) $wtotnptvocom=$wtotnptvocom   + $wmatriz[$i][$k]; }
					      break;
						case 15:
					      { if (isset($wmatriz[$i][$k])) $wtotnevocom=$wtotnevocom   + $wmatriz[$i][$k]; }
					      break;
						//hasta acá
					   }       
				  }
			   echo "<td align=right><b>".$wtotcco."</b></td>"; 
			   echo "</tr>";
		 	   $wtottot = $wtottot+$wtotcco;      
		      }    	        
		    //==========================================================================================================	      
		    echo "<tr class=encabezadoTabla>";
		    echo "<td colspan=2 align=left><b>Totales .... </b></td>";
		    echo "<td align=right><b>".$wtotnpt."</b></td>";
		    echo "<td align=right><b>".$wtotne."</b></td>";
		    echo "<td align=right><b>".$wtotvo."</b></td>";
			echo "<td align=right><b>".$wtotcom."</b></td>";//agregado
		    echo "<td align=right><b>".$wtotnptne."</b></td>";
		    echo "<td align=right><b>".$wtotnptvo."</b></td>";
		    echo "<td align=right><b>".$wtotnevo."</b></td>";
		    echo "<td align=right><b>".$wtotnptnevo."</b></td>";
			//nuevos tipos de dieta
			echo "<td align=right><b>".$wtotnptcom."</b></td>";
			echo "<td align=right><b>".$wtotnecom."</b></td>";
			echo "<td align=right><b>".$wtotcomvo."</b></td>";
			echo "<td align=right><b>".$wtotnptnevocom."</b></td>";
			echo "<td align=right><b>".$wtotnecom."</b></td>";
			echo "<td align=right><b>".$wtotnptvocom."</b></td>";
			echo "<td align=right><b>".$wtotnevocom."</b></td>";
			//hasta acá
			echo "<td align=right><b>".$wtottot."</b></td>";
		    echo "</tr>";
		    echo "</table>";
		    echo "<br><br>";
	      } 
	       
	   //================================================================================================================
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   //////////////////////////////////////////// P O R   D I A G N O S T I C O ///////////////////////////////////////
	   //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   //================================================================================================================
	   
	   
	   //==========================================================================================================================================   
	   //Consulta Nutricion por Diagnostico 
	   $q = " SELECT mid(Diagnostico,1,instr(Diagnostico,'-')-1), mid(Diagnostico,instr(Diagnostico,'-')+1,length(Diagnostico)), nuttip, COUNT(*) "
	       ."   FROM ".$wbasedato."_000056, ".$wbasedato."_000020, ".$winvecla."_000031 "
	       ."  WHERE nutfec  BETWEEN '".$wfec_i."' AND '".$wfec_f."'"
	       ."    AND nuthab  = habcod "
	       ."    AND habcco  LIKE '".trim(substr($wcco,0,strpos($wcco,'-')-0))."'"
	       ."    AND nutest  = 'on' "
	       ."    AND nuthis  = historia_clinica "
	       ."    AND nuting  = num_ingreso "
	       ."    AND Dx_ppal = 'on' "
	       ."  GROUP BY 1, 2, 3 "
	       ."  ORDER BY 1, 2, 3 ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   if ($num > 0)
	      {
		   echo "<center><table>";
		   echo "<tr class=seccion1>";
		   echo "<td align=center><font size=4><b>PACIENTES POR *** DIAGNOSTICO ***</b></font></td>";
		   echo "<tr class=seccion1>";
		   echo "<td align=center colspan=10><font size=1>(Puede existir diferencia con la totalidad de las otras estadísticas debido a que NO todas las historias tienen aun Diagnóstico)</font></td>";
		   echo "</tr>";
		   echo "</table>";   
		      
		   estadistica("DIAGNOSTICO", $res, $num);   
		  }
    }
    
	echo "</form>";
	  
	echo "<center><table>"; 
    echo "</table>";
	
    echo "<br>";
    echo "<center><table>"; 
    echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register

include_once("free.php");

?>
