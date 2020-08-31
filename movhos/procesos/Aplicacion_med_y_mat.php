<head>
  <title>APLICACION MEDICAMENTOS Y MATERIAL MEDICO GRABADOS A LOS PACIENTES</title>
  
</head>
<body>
<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()		  
     } 
     
    function enter()
	 {
	   document.forms.apl_med_mat.submit();
     } 
      
</script>

<?php
include_once("conex.php");
  /*********************************************************
   *   APLICACION DE MEDICAMENTOS Y MATERIAL A PACIENTES   *
   *    EN LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
   *     				CONEX, FREE => OK	
   **********************************************************/


include_once("root/comun.php");


include_once("root/magenta.php");

$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla))
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$institucion = consultarInstitucionPorcodigo($conex, $wemp_pmla);
$wbasedato = $institucion->baseDeDatos;
$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );    


session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
session_register("wemp_pmla","wcenmez");	  

if(!isset($_SESSION['user']))
	echo "error";
		
else
{	

  $origen='AM';									//variable que maneja el origen para ingresar a Aplicacion_med_y_mat 
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(2012-06-15)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	                                             
  //=====================================================================================================================================
  // A C T U A L I Z A C I O N E S    
  //============================================================================================================================================
  // Junio 15 de 2012
  // Se agregan las funciones para listar los centros de costos y la que dibuja el select con los centros de costos que se envian por parametro
  // Viviana Rodas.
  //============================================================================================================================================  
  //============================================================================================================================================
  // Febrero 7 de 2012
  // Se agrega una consulta para traer las historias inactivas, independiente del centro de costos por donde se ingrese
  //============================================================================================================================================
  // Enero 31 de 2012 	
  // Se declara la variable $origen que es llamada en el proceso Consultar ronda para que ambos procesos puedan trabajar de manera independiente
  // Se agregan conexiones a las bases de datos para permitir que el proceso consultar ronda pueda acceder a las mismas.
  //=============================================================================================================================================
  // Noviembre 4 de 2010
  //=====================================================================================================================================
  // Se crea un campo nuevo en la tabla movhos_000011 'ccoipd', el cual indica que la aplicacion de medicamentos en ese CCo se hace en  
  // los IPOD's, sin importar que el CCo ya este realizando el Kardex Electronico. Este cambio se observa en los programas :
  // ** Aplicacion_med_y_mat_ingreso.php ** y en ** Consulta_ronda.php **
  //=====================================================================================================================================
  // Septiembre 21 de 2010
  //=====================================================================================================================================
  // Se reversa la modificacion anterior para que deje entrar a cualquier centro de costo, porque la validación se va a hacer es por
  // la lista de articulos, es decir, solo se mostraran los articulos de MQX para los centros de costo que hallan iniciado el kardex
  // electronico directamente y no podran hacer apliaciones, y para los demas CC. les mostrara todos los articulos y los dejara aplicar
  //=====================================================================================================================================
  // Septiembre 2 de 2010
  //=====================================================================================================================================
  // Se hace modificación para que a medida que la aplicación con IPOD's se valla implementando en los servicios, a su vez, no la puedan
  // hacer por este programa.
  //=====================================================================================================================================
   
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
      		
  echo "<form name='apl_med_mat' action='Aplicacion_med_y_mat.php?wemp_pmla=$wemp_pmla' method=post>";
  echo "<center><table>";
   
   
  if (strpos($user,"-") > 0)
     $wuser = substr($user,(strpos($user,"-")+1),strlen($user)); 
	
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
        }  
    }
    else
    echo "NO EXISTE NINGUNA APLICACION DEFINIDA PARA ESTA EMPRESA";
  
	$winstitucion=$row[2];
      
	encabezado("Aplicación de Insumos y Medicamentos",$wactualiz, "clinica");  
       
	if (!isset($wcco) or (trim($wcco)==""))
    {
	       
		if ($wuser=="03150")
	    {   
	      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	      //llamado a las funciones que listan los centros de costos y la que dibuja el select
	      
			$cco="Ccohos";
			$sub="off";
			//$cco=" ";
			$tod="";
			$ipod="off";
			$centrosCostos = consultaCentrosCostos($cco);
			echo "<table align='center' border=0 >";
			$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
			echo $dib;
			echo "</table>";
        }
        else
          {
	       ?>	    
			 <script>
			     function ira(){document.apl_med_mat.wcco.focus();}
			 </script>
		   <?php 
		   
           echo "<td bgcolor=00CCFF align=center><INPUT TYPE='password' NAME='wcco' SIZE=7></td>";       
          }
          	    
		   echo "<center><tr><td align=center colspan=1 bgcolor=#cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
		   echo "</table>";
    }    
   else   
    { 
	    echo "</form>";   
	     
	 // echo "<form name='apl_med_mat' action='Aplicacion_med_y_mat_ingreso.php' method=post>";   
	    echo "<form name='apl_med_mat' action='Aplicacion_med_y_mat.php' method=post>"; 
	  
	  // $pos = strpos($user,"-");
      // $wusuario = substr($user,$pos+1,strlen($user));
	  
		echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	     
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
	      
	  //=======================================================================================================
	  //=======================================================================================================
	  /*//Traer Indicador del Kardex Electronico
	  $q = " SELECT ccokar "
	      ."   FROM ".$wbasedato."_000011 "
	      ."  WHERE ccocod = '".$wcco."'"
	      ."    AND ccoest = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($res);
	  $wkarele = $row[0];*/
	  //=======================================================================================================
	  
	  /*
	  //if (trim($wcco) != "1183" or $wuser=="03678" or $wuser=="0110191" or $wuser=="00334" or $wuser=="01156" or $wuser=="0105885")     //Esta linea es temporal, porque este programa luego se quita cuando toda la clinica este aplicando por el pgma de los IPOD's
	  if ($wkarele != "on" or $wlactario=="on" or $wuser=="03678")
	    { */
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
		       alert ("EL CENTRO DE COSTO NO FUE INGRESADO POR CODIGO DE BARRA");     
		      </script>
		     <?php 
		    }       
	     
		  $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced "
		      ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000018, root_000037, root_000036 "
		      ."  WHERE habcco  = '".$wcco."'"
		      ."    AND habali != 'on' "             //Que no este para alistar
		      ."    AND habdis != 'on' "             //Que no este disponible, osea que este ocupada
			  ."    AND habhis  = ubihis "
		      ."    AND habing  = ubiing "
		      ."    AND habcod  = ubihac "		     //se coloca en comentario ya que pone lento el programa
		      ."    AND ubihis  = orihis "
		      ."    AND ubiing  = oriing "
		      ."    AND ubiald != 'on' "
		      ."    AND ubiptr != 'on' "
			  ."    AND ubisac  = habcco"
		      ."    AND ubisac  = '".$wcco."'"	     //se coloca en comentario ya que pone lento el programa
		      ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
		      ."    AND oriced  = pacced "
			  ."    AND oritid  = pactid "
		      ."  GROUP BY 1,2,3,4,5,6,7,8 "		 //se coloca en comentario ya que pone lento el programa
		      ."  ORDER BY Habord, Habcod ";    //se agrega el campo orden
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);  
		  	     
		  echo "<table>";
		  
		  echo "<tr class=titulo>";
		  echo "<td colspan=6 align=center><b>Servicio o Unidad: ".$wnomcco."</b></td>";
		  echo "</tr>";
		  
		  echo "<tr class=encabezadoTabla>";
		  echo "<th>Habitacion</th>";
		  echo "<th>Historia</th>";
		  echo "<th>Ingreso</th>";
		  echo "<th>Paciente</th>";
		  echo "<th>Afinidad</th>";
		  echo "<th></th>";
		  echo "</tr>";
			                       
		  $whabant = "";
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
					   $wtid = $row[7];                                      //Tipo documento paciente
					   $wdpa = $row[8];                                      //Documento del paciente
				        	            
					if ($whabant != $whab)
				    {
					   echo "<tr class=".$wclass.">";
				       echo "<td align=center><b>".$whab."</b></td>";
				       echo "<td align=center><b>".$whis."</b></td>";
				       echo "<td align=center><b>".$wing."</b></td>";
				       echo "<td align=left><b>".$wpac."</b></td>";
					  
				       
				       //======================================================================================================
				       //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
				       $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
				       if ($wafin)
					    {
						  echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
					    }
					    else
						echo "<td>&nbsp</td>";
					     //======================================================================================================
				        echo "<td align=center><A href='Aplicacion_med_y_mat_ingreso.php?&whis=".$whis."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wcenmez=".$wcenmez."&origen=".$origen."'><font size=3><b>Ver</b></A></font></td>";
				        echo "</tr>";
				           
				        $whabant = $whab;
				    }
		        }	     
			}
			else
			    echo "NO HAY HABITACIONES OCUPADAS";  
		    echo "</table>"; 
			   
		  //CENTRO DE COSTO
		  echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>"; 
			   
		  echo "<br>";
		  echo "<center><table>";
		  
		  /*
		  ?>	    
		  <script>
		     function ira(){document.apl_med_mat.whis.focus();}
		  </script>
		  <?php
		  */
		  
		  if(isset($whis1))
			$wvalor = $whis1;
		  else 
			$wvalor = "";
		
			
		  echo "<tr class=encabezadoTabla><td align=center><b>Ingrese la Historia :</b><INPUT TYPE='text' NAME='whis1' value='".$wvalor."'    SIZE=10></td></tr>";
		  echo "<input type='HIDDEN' name='origen' value='AM'>";
		  
		  echo "<tr><td align=center><INPUT type='submit' value='Generar' style='width:100'></td></tr>";
		  
		  if(!isset ($whis1))								//Variables para las historias inactivas
		  {
			$whis1="";
		  }
		  $wing1="";
		  $wpac1="";
		  $whab1="";
		  $wcco1="";
		  
		  if($whis1 != "" )
			{
			  //se agrega esta consulta para traer las historias inactivas 
			  //              0      1      2      3      4       5     6      7       8     9       10
			  $q= " SELECT  pacno1, pacno2,pacap1,pacap2,pactid,pacced,ubihis,ubiing, ubisac,ubihac,A.fecha_data "
				 ."   FROM  ".$wbasedato."_000018 A,root_000036 B,root_000037 C  "
				 ."  WHERE  A.ubihis = '".$whis1."'"
				 ."    AND  A.ubihis = C.orihis "
				 ."    AND  C.oriori = '".$wemp_pmla."' "
				 ."    AND  B.pacced = C.oriced "
				 ."    AND  B.pactid = C.oritid "
				 ."  ORDER BY A.ubihis ";
			  $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()."- en el query: ".$q." - ".mysql_error());
			  $num= mysql_num_rows($res);
			  
			  echo "<table>";
			  echo "<tr class=encabezadoTabla>";
			  echo "<th>Fecha Ingreso</th>";
			  echo "<th>Historia</th>";
			  echo "<th>Ingreso</th>";
			  echo "<th>Paciente</th>";
			  echo "<th>Ver</th>";
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
							
					   $wfecha= $row[10];									 //Fecha de ingreso del paciente
					   $wpac1 = $row[0]." ".$row[1]." ".$row[2]." ".$row[3]; //Nombre del paciente
					   //$wtid = $row[4];                                    //Tipo documento paciente
					  // $wdpa = $row[5];                                    //Documento del paciente
					   $wing1 = $row[7];  									 //Ingreso del paciente
					   $wcco1 = $row[8];									 //Centro de costos actual
					   $whab1=  $row[9];								     //Habitacion actual
					   
					   echo "<tr class=".$wclass.">";
					   echo "<td align=center><b>".$wfecha."</b></td>";
					   echo "<td align=center><b>".$whis1."</b></td>";
					   echo "<td align=center><b>".$wing1."</b></td>";
					   echo "<td align=center><b>".$wpac1."</b></td>";
					  
					   echo "<td align=center><A href='Consultar_ronda.php?whis=".$whis1."&wing=".$wing1."&wcenmez=".$wcenmez."&wcco=".$wcco."&wpac1=".$wpac1."&whab1=".$whab1."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&origen=".$origen."&wactivo=I'><font size=3><b>Ver</b></font></A></td>";
						
					}	     
				}
					   echo "</table>";
			}	  
		  echo "</table>";
		  echo "<br><br>";
		 
		  echo "<table>";
		  
		  //=================================================================================================================================================================
		  //=================================================================================================================================================================
		  //DESDE ACA HAGO UNA BUSQUEDA DE LAS CONDICIONES EN QUE SE ENCUENTRA EL SERVICIO
		  
		  //Busco si hay pacientes por recibir en el servicio  
		  $q = " SELECT count(*) "  
		      ."   FROM ".$wbasedato."_000018 "
		      ."  WHERE ubisac = '".$wcco."'"
		      ."    AND ubiptr = 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row = mysql_fetch_array($res);
		    if ($row[0] > 0)
		    {
			  echo "<tr class=encabezadoTabla>";
		      echo "<td align=left><b>Tiene (".$row[0].") Paciente(s) para Recibir.</b></td>";
		      echo "</tr>";    
		    }
		  
		  //Busco a cuantos pacientes se les puede dar alta definitiva  
		  $q = " SELECT count(*) "  
		      ."   FROM ".$wbasedato."_000018,".$wbasedato."_000022 "
		      ."  WHERE ubialp  = 'on' "
		      ."    AND ubisac  = '".$wcco."'"
		      ."    AND ubihis  = cuehis "
		      ."    AND ubiing  = cueing "
		      ."    AND ubiald != 'on' "
		      ."    AND cuegen  = 'on' "
		      ."    AND cuepag  = 'on' "
		      ."    AND cuecok  = 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row = mysql_fetch_array($res);
			if ($row[0] > 0)
			{
			  echo "<tr class=encabezadoTabla>";
			  echo "<td align=left><b>Tiene (".$row[0].") Paciente(s) en alta esperando el Alta Definitiva.</b></td>";
			  echo "</tr>";    
			}
		    
		  //Busco cuantos pacientes estan con factura pero no se han enviado a caja a pagar
		  $q = " SELECT count(*) "  
		      ."   FROM ".$wbasedato."_000018,".$wbasedato."_000022 "
		      ."  WHERE ubialp  = 'on' "
		      ."    AND ubisac  = '".$wcco."'"
		      ."    AND ubihis  = cuehis "
		      ."    AND ubiing  = cueing "
		      ."    AND ubiald != 'on' "
		      ."    AND cuegen != 'on' "
		      ."    AND cuepag != 'on' "
		      ."    AND cuecok  = 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row = mysql_fetch_array($res);
		  if ($row[0] > 0)
		    {
			  echo "<tr class=encabezadoTabla>";
		      echo "<td><b><blink>Tiene (".$row[0].") Paciente(s) en alta y esta(n) siendo facturado(s) en este momento.</b></td>";
		      echo "</tr>";    
		    }
		        
		  //Busco a cuantos pacientes estan pendientes de la devolucion o que el facturador verifique si puede facturar  
		  $q = " SELECT count(*) "  
		      ."   FROM ".$wbasedato."_000018 "
		      ."  WHERE ubialp  = 'on' "
		      ."    AND ubisac  = '".$wcco."'"
		      ."    AND ubihis not in ( SELECT cuehis "
		      ."                          FROM ".$wbasedato."_000022 "
		      ."                         WHERE cuehis = ubihis  "
		      ."                           AND cueing = ubiing )"                
		      ."    AND ubiald != 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row = mysql_fetch_array($res);
		  if ($row[0] > 0)
		    {
			  echo "<tr class=encabezadoTabla>";
		      echo "<td><b>Tiene (".$row[0].") Paciente(s) en alta, pendiente(s) de la Devolución o que el facturador verifique si puede facturar.</b></td>";
		      echo "</tr>";    
		    }     
		    
		  //Busco cuantos pacientes estan facturados pero pendientes del pago  
		  $q = " SELECT count(*) "  
		      ."   FROM ".$wbasedato."_000018,".$wbasedato."_000022 "
		      ."  WHERE ubialp  = 'on' "
		      ."    AND ubisac  = '".$wcco."'"
		      ."    AND ubihis  = cuehis "
		      ."    AND ubiing  = cueing "
		      ."    AND ubiald != 'on' "
		      ."    AND cuegen  = 'on' "
		      ."    AND cuepag != 'on' "
		      ."    AND cuecok  = 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row = mysql_fetch_array($res);
		   if ($row[0] > 0)
		    {
			  echo "<tr class=encabezadoTabla>";
		      echo "<td align=left><b>Tiene (".$row[0].") Paciente(s) en alta, pendiente(s) de pago en Caja.</b></td>";
		      echo "</tr>";
		    }
		     
		  //Busco si hay pacientes con Muerte pero no tiene Proceso de Alta  
		  $q = " SELECT count(*) "  
	          ."   FROM ".$wbasedato."_000018 "
		      ."  WHERE ubisac  = '".$wcco."'"
		      ."    AND ubimue  = 'on'   " 
		      ."    AND ubialp != 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row = mysql_fetch_array($res);
		  if ($row[0] > 0)
		    {
		     echo "<tr class=encabezadoTabla>";
		     echo "<td align=left><b>Tiene (".$row[0].") Paciente(s) con Muerte y Sin proceso de Alta.</b></td>";
		     echo "</tr>";    
		    }   
		  //=================================================================================================================================================================   
		  //=================================================================================================================================================================
		  echo "</table>"; 
		 
		  echo "<br>";
		  echo "<table>";
		  echo "<tr class=link>";
		  echo "<td align=center colspan=7><A href=Aplicacion_med_y_mat.php?wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."><b>Retornar</b></A>";
		  echo "</tr>";
		  
		  echo "</table>";
		  echo "<br>";
		  echo "<br>";
		   echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='javascript:cerrarVentana()'></td></tr>";
		/* }
		else  //Septiembre 2 de 2010
		   {
			echo "<br>";
		    echo "<table>";
		    echo "<tr class=link>";
		    echo "<td align=center colspan=7><A href=Aplicacion_med_y_mat.php?wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."><b>Retornar</b></A>";
		    echo "</tr>";
		    echo "</table>";
		    echo "<br>";   
			   
			?>	    
		      <script>
		       alert ("LA APLICACION LA DEBE HACER DESDE EL PROGRAMA DE LOS IPOD'S");     
		      </script>
		     <?php    
		   }	      //Hasta aca el cambio de Septiembre 2 de 2010 
		   
		   */
		   

	  echo "</form>";
     }     
     
} // if de register

echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";


unset($wano);
unset($wmes);
unset($wdia);
unset($wcco);

include_once("free.php");

?>