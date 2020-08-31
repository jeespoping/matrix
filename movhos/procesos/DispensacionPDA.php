<html>
<head>
  <title>DISPENSACION PDA'S</title>
</head>
<script type="text/javascript">
	function enter()
	{
	 document.forms.dispensacionPDA.submit();
	}
	
	function cerrarVentana()
	 {
      window.close();			  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *              DISPENSACION PDA               *
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
  
  $conex = obtenerConexionBD("matrix");
  
  $wactualiz="(Octubre 1 de 2009)";                      // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                    // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION  Junio 30 de 2009                                                                                                            \\
//=========================================================================================================================================\\
//Con este programa se observa una lista de trabajo de todas las historias que tengan el Kardex Electronico confirmado para dispensacion,  \\
//además se pueden ir directamente a la dispensación y luego de terminar volver a la lista.                                                \\
//                                                                                                                                         \\
//=========================================================================================================================================\\	                                                         
	                                                           
	                                                             
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//                                                                                                                                         \\
//                                                                                                                                         \\
//=========================================================================================================================================\\
	               
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
 
  function estado_del_Kardex($whis, $wing, &$westado, $wmuerte, &$wcolor)
    {
	  global $wbasedato;
	  global $conex;
	  global $wespera;
	  global $wfecha;
	  global $waltadm;
	  
	  
	  $westado="off";  //Apago el estado, que indica segun la opcion si la historia si esta en el estado que indica la opcion.
	  //========================================================================================================================================================================
	  //Kardex sin Dispensar parcial o totalmente
	  //========================================================================================================================================================================
	  //Aca muestra todas las historias que no se han dispensado nada
	  $q= " SELECT COUNT(*) "
          ."  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B"
          ." WHERE kadhis        = '".$whis."'"
          ."   AND kading        = '".$wing."'"
          ."   AND kadfec        = '".$wfecha."'"       //Esto me valida que el Kardex sea del día
          ."   AND kadsus       != 'on' "
          ."   AND kadori        = 'SF' "
          ."   AND kadcdi-kaddis > 0 "                  //Esto me indica que ya falta por dispensar parcial o totalmente
          ."   AND kadhis        = karhis "
          ."   AND kading        = karing "
          ."   AND A.kadfec      = B.fecha_data "
          ."   AND B.karcon      = 'on' ";
      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $wnum = mysql_num_rows($res1);
      $wcan = mysql_fetch_array($res1);
      
      if ($wcan[0] > 0)         //Si hay registros es porque ya el facturador hizo el chequeo para facturar
         {
	      $westado="on";        //Indica que la historia si esta sin Dispensar el Kardex pacial o totalmente, es decir en 'on'
	     } 
	     
	  if ($wmuerte=="on")
         $westado="Falleció";     
	}
  	
	
  //=====================================================================================================================================================================	
  //**************************** Aca termina la funcion estado_del_kardex *******************
  
  
  
  
  //$wtitulo = "KARDEX ** SIN ** DISPENSAR PARCIAL O TOTALMENTE";
       
  //encabezado($wtitulo, $wactualiz, 'clinica');
  
       
  //FORMA ================================================================
  echo "<form name='dispensacionPDA' action='DispensacionPDA.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  
  echo "<center><table>";
  
  if (!isset($wusuario) or !isset($wcco))
	 {
		echo "<table>";
		echo "<tr>";
		echo "<td class='titulo' align=center>CÓDIGO NOMINA: </font>";
		echo "<input type='text' cols='10' name='wusuario'></td>";
		echo "</tr>";	
		echo "<tr>";
		echo "<td class='titulo' align=center>CENTRO DE COSTOS: </font>";
		echo "<input type='text' cols='10' name='wcco'></td>";
		echo "</tr>";
		echo "<script language='JAVASCRIPT' type='text/javascript'>";
		echo "document.dispensacionPDA.wusuario.focus();";
		echo "</script>";
		echo"<tr><td align=center class='fila1'><input type='submit' value='ACEPTAR'></td></tr>";
		echo "</table>";
		
		echo "<br>";
	    echo "<table>"; 
	    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	    echo "</table>";
	 }
   else
     {
	  //Valido que usuario exista en la tabla root_000025
	  $q = " SELECT COUNT(*) "
	      ."   FROM root_000025 "
	      ."  WHERE Empleado = '".$wusuario."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num1 = mysql_fetch_array($res);
	  
	  //Valido que el Centro de costo exista
	  $q = " SELECT COUNT(*) "
	      ."   FROM ".$wbasedato."_000011 "
	      ."  WHERE ccocod = '".$wcco."'"
	      ."    AND ccoest = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num2 = mysql_fetch_array($res);
	  
	  if ($num1[0] > 0 and $num2[0] > 0)
	     {
	      //$row = mysql_fetch_array($res);   
	      $wccousu = $wcco;
	      
	      //===============================================================================================================================================
		  //ACA COMIENZA EL MAIN DEL PROGRAMA   
		  //===============================================================================================================================================
		  //Aca trae todos los pacientes que esten hospitalizados en la clinica y luego busco como es el estado en cuanto a Kardex de cada uno
		  $q = " SELECT ubihac, ubihis, ubiing, ".$wbasedato."_000018.id, ubihot, ubihap, ubimue, ubihan, ubialp, ubiptr "
		      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000011, ".$wbasedato."_000020, ".$wbasedato."_000017 "
		      ."  WHERE ubiald != 'on' "			 //Que no este en Alta Definitiva
		      ."    AND ubialp != 'on' "            //Que no este en Alta en Proceso
		      ."    AND ubisac  = ccocod "
		      ."    AND ccohos  = 'on' "             //Que el CC sea hospitalario
		      ."    AND ccourg != 'on' "             //Que no se de Urgencias
		      ."    AND ccocir != 'on' "             //Que no se de Cirugia
		      ."    AND ubihis  = habhis "
		      ."    AND ubiing  = habing "
		      ."    AND ubihis != '' "
		      ."    AND ubihis = eyrhis "             //Estas cuatro linea que siguen son temporales
		      ."    AND ubiing = eyring "             
		      ."    AND eyrsde = ccocod "
		      ."    AND eyrtip = 'Recibo' "
		      ."    AND eyrest = 'on' "
		      ."  GROUP BY 1,2,3,4,5,6 ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);
			
		  if ($num > 0)
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
			      
			      estado_del_kardex($whis,$wing,&$westado,$wmue, &$wcolor);     
			      
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

			     echo "<tr><td align=CENTER bgcolor=#fffffff colspan=4><font size=2 text color=#CC0000><b>DISPENSACION</b></font></td></tr>";
			     echo "<tr><td align=left bgcolor=#fffffff colspan=2><font size=2 text color=#CC0000><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#fffffff colspan=2><font size=2 text color=#CC0000><b>Cantidad: ".($j-1)."</b></font></td></tr>";
			    
			     echo "<tr class='encabezadoTabla'>";
				 echo "<th>Habitacion</th>";
				 echo "<th>Historia</th>";
				 echo "<th>Paciente</th>";
				 echo "<th>Ver</th>";
				 echo "</tr>";
				 
				 $wsw=0;     
			     for($i=1;$i<=$j-1;$i++)
			        {
			         if (is_integer($i/2))
			            $wclass="fila1";
			           else
			             $wclass="fila2";
			               
			         echo "<tr class=".$wclass.">";
				     echo "<td align=center>".$wmat_estado[$i][0]."</td>";
				     echo "<td align=center>".$wmat_estado[$i][1]." - ".$wmat_estado[$i][2]."</td>";
				     echo "<td align=left  >".$wmat_estado[$i][3]."</td>";
				     echo "<td align=left  ><A href='cargos.php?emp=".$wemp_pmla."&bd=".$wbasedato."&tipTrans=C&wemp=".$wemp_pmla."&usuario=".$wusuario."&cco[cod]=".$wccousu."&historia=".$wmat_estado[$i][1]."&fecDispensacion=".$wfecha."' target=_blank> Cargar </A></td>";
				     echo "</tr>";
				    }
			  }
			 else
			    echo "NO HAY HABITACIONES OCUPADAS"; 
		  echo "</table>"; 
		  
		  echo "<meta http-equiv='refresh' content='20;url=dispensacionPDA.php?wemp_pmla=".$wemp_pmla."&wusuario=".$wusuario."&wcco=".$wcco."'>";
		                 
		  echo "</form>";
		  
		  echo "<br>";
		  echo "<table>"; 
		  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		  echo "</table>";
	     }
	    else
	       {
		    echo "<script type='text/javascript'>"; 
	        echo "alert ('El usuario no existe en la tabla ** root_000025 ** o el Centro de Costo no es valido, favor comunicarse con Soporte de Sistemas');";
	        echo "</script>";
	        
	        ///unset($wusuario);
	        ///unset($user);
	        
	        echo "<meta http-equiv='refresh' content='0;url=dispensacionPDA.php?wemp_pmla=".$wemp_pmla."'>";
	       }  
	 } 
} // if de register



include_once("free.php");

?>
