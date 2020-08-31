<head>
  <title>APLICACION DE MEDICAMENTOS Y MATERIAL A LOS PACIENTES</title>
</head>
<body onload=ira()>

<?php
include_once("conex.php");
/*****************************************************************************************************************************************************************
 * Modificaciones:
 * Mayo 9 de 2012		(Edwin MG)	Las rondas a seleccionar siempre son pares
 * Febrero 9 de 2012 (Luis Zapata)  Se agrega la variable $origen en el enlace de consultar ronda para que en este programa y en Aplicacion_med_y_mat
 *									se puedan consultar las historias activas o inactivas de los pacientes.
 * Noviembre 29 de 2011	(Edwin MG)	Al hacer insert en la tabla de aplicaciones (movhos_000015), la dosis y la unidad de dosis es igual al ultimo
 *									registro del kardex, en caso de no encontrarse ningun registro se toma la dosis de la tabla de definicion de fracciones
 *									(movhos_000059)
 *										
 *****************************************************************************************************************************************************************/

  /*********************************************************
   *   APLICACION DE MEDICAMENTOS Y MATERIAL A PACIENTES   *
   *    EN LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
   *     				CONEX, FREE => OK				   *
   *********************************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
		
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

  include_once("root/comun.php");
  

  
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 global $origen; 		
 $origen='AM';
		                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(2012-02-09)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
                                                   
  echo "<br>";				
  echo "<br>";
  
  //*******************************************************************************************************************************************
  //===========================================================================================================================================
  
  function consultarFraccionPorArticulo( $articulo ){
  
	global $conex;
	global $wbasedato;
	global $wcenmez;
	
	$val = Array();
	
	$sql = "SELECT 
				*
			FROM
				{$wbasedato}_000059
			WHERE
				defart = '$articulo'
				AND defest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$rows = mysql_fetch_array( $res );
		$val['unidad'] = $rows['Deffru'];
		$val['fraccion'] = $rows['Deffra'];
	}
	
	//Busco la unidad minima del articulo, indicadas por el maestro de medicamentos
	if( true ){
	
		//Busco el medicamento en SF
		$sql = "SELECT 
					Artuni
				FROM
					{$wbasedato}_000026
				WHERE
					artcod = '$articulo'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
			$rows = mysql_fetch_array( $res );
			// $val['unidad'] = $rows['Artuni'];
			// $val['fraccion'] = 1;
		}
		else{
			//Busco el medicamento en CM
			$sql = "SELECT 
						Artuni
					FROM
						{$wcenmez}_000002
					WHERE
						artcod = '$articulo'
					";
			
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );
			
			if( $num > 0 ){
				$rows = mysql_fetch_array( $res );
				// $val['unidad'] = $rows['Artuni'];
				// $val['fraccion'] = 1;
			}
		}
	}
	
	//Hago la conversion
	if( !empty( $val['unidad'] ) ){
		
		if( strtoupper( trim( $val['unidad'] ) ) == strtoupper( trim( $rows['Artuni'] ) ) ){	//Si la unidades de fraccion de articulos son iguales que el maestro de articulos
			$val['unidad'] = $rows['Artuni'];
			$val['fraccion'] = 1;
		}
	}
	else{	//Si no existe en definicion de fraccion
		$val['unidad'] = $rows['Artuni'];
		$val['fraccion'] = 1;
	}
	
	return $val;
  }
  
  function traer_DosisyFraccion($whis, $wing, $wart, $wfec, &$wdosis, &$wfraccion)
    {
	 global $wbasedato;
	 global $wcenmez;
	 global $conex;
	 
	 $q = "  SELECT Kadcfr, Kadufr "
	     ."    FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B "
	     ."   WHERE kadhis  = '".$whis."'"
	     ."     AND kading  = '".$wing."'"
	     ."     AND kadfec  = '".$wfec."'"
         ."     AND kadart  = '".$wart."'"		 
	     ."     AND kadest  = 'on' "
	     ."     AND kadhis  = karhis "
	     ."     AND kading  = karing "
	     ."     AND karcon  = 'on' "
	     ."     AND karcco  = kadcco "
	     ."     AND B.fecha_data = kadfec "
		 ."ORDER BY kadfec	DESC";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $num = mysql_num_rows($res);
	 
	 if ($num == 0)
	    {
			return;
			// $datos = consultarFraccionPorArticulo( $wart );
			
			// if( !empty($datos) )
			  // {
				// $wfraccion = $datos['unidad'];
				// $wdosis    = $datos['fraccion'];
			  // }
			// else
			  // {
				// $wfraccion = "";
				// $wdosis    = "";
			  // }						  
		}
       else
   	     {
		   $row = mysql_fetch_array($res);
		   $wdosis    = $row[0];
		   $wfraccion = $row[1];
		 }	
	}
  
  // function traer_DosisyFraccion($whis, $wing, $wart, $wfec, &$wdosis, &$wfraccion)
    // {
	 // global $wbasedato;
	 // global $wcenmez;
	 // global $conex;
	 
	 
	 // $q = "  SELECT Kadcfr, Kadufr "
	     // ."    FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B "
	     // ."   WHERE kadhis  = '".$whis."'"
	     // ."     AND kading  = '".$wing."'"
	     // ."     AND kadfec  = '".$wfec."'"
         // ."     AND kadart  = '".$wart."'"		 
	     // ."     AND kadest  = 'on' "
	     // ."     AND kadhis  = karhis "
	     // ."     AND kading  = karing "
	     // ."     AND karcon  = 'on' "
	     // ."     AND karcco  = kadcco "
	     // ."     AND B.fecha_data = kadfec "
		 // ."ORDER BY kadfec	DESC";
	 // $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 // $num = mysql_num_rows($res);
	 
	 // if ($num == 0)
	    // {
		  // $dia   = time()-(1*24*60*60);   //Resta un dia
          // $wayer = date('Y-m-d', $dia);   //Formatea dia 
		  // $wfec  = $wayer;
		
	     // $q = " SELECT Kadcfr, Kadufr "
			 // ."   FROM ".$wbasedato."_000054 A, ".$wbasedato."_000053 B "
			 // ."  WHERE kadhis  = '".$whis."'"
			 // ."    AND kading  = '".$wing."'"
			 // ."    AND kadfec  = '".$wfec."'"
			 // ."    AND kadart  = '".$wart."'"		 
			 // ."    AND kadest  = 'on' "
			 // ."    AND kadhis  = karhis "
			 // ."    AND kading  = karing "
			 // ."    AND karcon  = 'on' "
			 // ."    AND karcco  = kadcco "
			 // ."    AND B.fecha_data = kadfec ";
		 // $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     // $num = mysql_num_rows($res);
		 
		 // if ($num == 0)
		    // {
				// $datos = consultarFraccionPorArticulo( $wart );
				
				// if( !empty($datos) ){
					// $wfraccion = $datos['unidad'];
					// $wdosis    = $datos['fraccion'];
				// }
				// else{
					// $wfraccion = "";
					// $wdosis    = "";
				// }
			// }
		   // else
              // {
               // $row = mysql_fetch_array($res);
			   // $wdosis    = $row[0];
			   // $wfraccion = $row[1];
              // }			  
		// }
       // else
   	      // {
		   // $row = mysql_fetch_array($res);
		   // $wdosis    = $row[0];
		   // $wfraccion = $row[1];
		  // }	
	// }
  
  
  function buscar_cco($wccosto)
    {
	 global $wbasedato;
	 global $conex;
	 
	 //Noviembre 4 de 2010
	 //Busco si en el cco se hace la aplicacion con los IPOD's
	 $q = " SELECT ccoipd "
	     ."   FROM ".$wbasedato."_000011 "
	     ."  WHERE ccocod = '".trim($wccosto)."'"
	     ."    AND ccoest = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res); 
	 
	 if ($row[0]=="on")
	    return true;
	   else 
	      return false;      
	}
  
  function buscar_enfermera($wcod_enf)
    {
	 global $wbasedato;
	 global $conex; 
	 global $wok_enfer;
	 global $wnom_enfer;
	 
	 
	 $q = " SELECT descripcion, count(*) "
	     ."   FROM usuarios "
	     ."  WHERE codigo = '".$wcod_enf."'"
	     ."    AND activo ='A' "
	     ."  GROUP BY 1 ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res); 
	 
	 if ($row[1] > 0)
	    {
	     $wok_enfer="on";
	     $wnom_enfer=$row[0];
        } 
	   else
	      $wok_enfer="off"; 
	      
	 return $wok_enfer;     
    }	 
  //===========================================================================================================================================  
  //*******************************************************************************************************************************************
  
  echo "<form name='ingreso' action='Aplicacion_med_y_mat_ingreso.php' method=post>";
  
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>"; 
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wcenmez' value='".$wcenmez."'>";
  echo "<input type='HIDDEN' name='origen' value='".$origen."'>";		//////////se agrega la variable $origen como oculta (feb-9-2012)
 
  if (isset($wenfer))
     {
	  $wenf=explode("-",$wenfer);   
      buscar_enfermera($wenf[0]);
      
      //CODIGO ENFERMERA
	  echo "<input type='HIDDEN' name= 'wenfer' value='".$wenf[0]."'>";
      //NOMBRE ENFERMERA
      echo "<input type='HIDDEN' name= 'wnom_enfer' value='".$wnom_enfer."'>";
     } 
  
  encabezado("APLICACION DE MEDICAMENTOS E INSUMOS A PACIENTES",$wactualiz, "clinica");
  
  echo "<center><table>";
  
  if (isset($whis))
     {
	  $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2 "
	      ."   FROM ".$wbasedato."_000020, root_000036, root_000037 "
	      ."  WHERE habcco  = '".$wcco."'"
	      ."    AND habali != 'on' "             //Que no este para alistar
	      ."    AND habdis != 'on' "             //Que no este disponible, osea que este ocupada
	      ."    AND habhis  = orihis "
	      ."    AND habing  = oriing "
	      ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
	      ."    AND oriced  = pacced "
	      ."    AND habhis  = '".$whis."'"
	      ."  GROUP BY 1,2,3,4,5,6 "
	      ."  ORDER BY 1 "; 
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
     }
       
  if ($num>0)
     {    
	  $row = mysql_fetch_array($res); 
              
      $whab = $row[0];
      $whis = $row[1];
      $wing = $row[2];
      $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
     
  
      //Verifico si la historia esta en proceso de traslado
      $q = " SELECT count(*) "
          ."   FROM ".$wbasedato."_000018 "
          ."  WHERE ubihis = '".$whis."'"
          ."    AND ubiing = '".$wing."'"
          ."    AND ubiptr = 'on' ";
      $resald = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $rowald = mysql_fetch_array($resald);    
      
      if ($rowald[0] == 0)  //Si es mayor a cero indica que esta pendiente de recibo en el servicio, entonces no debe dejar grabar
         {
		  echo "<tr class=fila1>";
		  echo "<td colspan=2><b>Historia:</b> ".$whis."</td>";
		  echo "<td><b>Paciente:</b> ".$wpac."</td>";
		  echo "<td colspan=3 align=center><b>Habitación:</b> ".$whab."</td>";
		  echo "</tr>";
		  
		  
		  //HISTORIA
		  echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>"; 
		  //INGRESO
		  echo "<input type='HIDDEN' name= 'wing' value='".$wing."'>";
		  //CENTRO DE COSTO
		  echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
		  //PACIENTE
		  echo "<input type='HIDDEN' name= 'wpac' value='".$wpac."'>";
		  //HABITACION
		  echo "<input type='HIDDEN' name= 'whab' value='".$whab."'>";
		  
		  
		  if (isset($warticulo1))
		     $warticulo=$warticulo1;
		     
		  if(!isset($warticulo) or !isset($wronda) or !isset($wenfer))
		    {
		     ?>	    
		      <script>
		       function ira(){document.ingreso.wronda.focus();}
		      </script>
		     <?php

		     $hora1 = (string)gmdate( "H", floor(date("H")/2)*2*3600 );	//Consulto la ronda actual
		     $hora  = (string)gmdate( "H", floor(date("H")/2)*2*3600 ); //Consulto la ronda actual
		     
		     //=======================================================================================================
			 //=======================================================================================================
			 //Traer Cco del Usuario
			 $q = " SELECT Ccostos "
			     ."   FROM usuarios "
			     ."  WHERE codigo = '".$wusuario."'"
			     ."    AND Activo = 'A' ";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $row = mysql_fetch_array($res);
			 $wccousu = $row[0];
			 //=======================================================================================================
	  
	         
	         //=======================================================================================================
	         // SI ES DEL LACTARIO
			 //=======================================================================================================
			 //Traer Indicador del Kardex Electronico
			 $q = " SELECT ccolac "
			     ."   FROM ".$wbasedato."_000011 "
			     ."  WHERE ccocod = '".$wccousu."'"
			     ."    AND ccoest = 'on' ";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $row = mysql_fetch_array($res);
			 $wlactario = $row[0];
			 //=======================================================================================================
		     
		     //Busco si este centro de costo solo puede anular el material medico QX
	         $wsolomaterial=buscar_cco($wcco);
		     
	         if ($wsolomaterial and $wlactario!="on" and $wusuario!="03678" and $wusuario!="00662" and $wusuario!="03799" and $wusuario!="03150" and $wusuario!="11700" and $wusuario!="0104014" and $wusuario!="0110533")    //Diferente del lactario 
	            {
		         echo "<tr></tr>";   
		         echo "<tr class=fondoAmarillo>";
		         echo "<td colspan=10 align=center><b>SOLO PARA CONSULTAR O ANULAR APLICACION DE DISPOSITIVOS MEDICOS<br>!!!LA APLICACION DE MEDICAMENTOS SE DEBE HACER EN EL IPOD¡¡¡</b></td>";
		         echo "</tr>";
		         echo "<tr></tr>";
	            }
	           else
	            {      
	             //HORA DE APLICACION
			     echo "<tr class=fila1>";
			     echo "<td><b>Hora Aplicación:</b><select name='wronda'>";
			     if (!isset($wronda))
			        {
				     for($f=0;$f<=22;$f+=2)
				       {
					   
					    if($f == $hora1)
					      { 
					       if ($f >= 12)
					          $hora = trim($f)." - PM";          //$hora = ($f-12)." - PM";
				             else
					            $hora = gmdate( "H", trim($f)*3600 )." - AM";
					       echo "<option selected>".$hora."</option>";
				          } 
				         else
				            {
				             if ($f >= 12)
				                 $hora = trim($f)." - PM";       //$hora = ($f-12)." - PM";
				                else
				                   $hora = gmdate( "H", trim($f)*3600 )." - AM";     //$hora = $f." - AM"; 
				             echo "<option>".$hora."</option>";
				            }
			           }      
			       }
			      else
			        {
				     if (strpos($wronda,":") > 0)
				        {
					     $wronda1=explode("-",$wronda);
					     $wminut1=explode(":",$wronda1[0]);
					     $wminu=$wminut1[1];
					     echo "<option selected>".$wminut1[0]." - ".$wronda1[1]."</option>";   
					    }       
				       else
				          {
						   $wronda1=explode("-",$wronda);
						   //echo "<option selected>".$wronda1[0]." - ".$wminut1[1]."</option>";     
						   echo "<option selected>".$wronda."</option>";     
					      }
					  
			         for($f=0;$f<=22;$f+=2)
				       {
						
					    if($f == $hora1)
					      { 
					       if ($f >= 12)
					          $hora = trim($f)." - PM";          //$hora = ($f-12)." - PM";
				             else
					            $hora = gmdate( "H", trim($f)*3600 )." - AM";
					       echo "<option>".$hora."</option>";
				          } 
				         else
				            {
				             if ($f >= 12)
				                 $hora = trim($f)." - PM";       //$hora = ($f-12)." - PM";
				                else
				                   $hora = gmdate( "H", trim($f)*3600 )." - AM";     //$hora = $f." - AM"; 
				             echo "<option>".$hora."</option>";
				            }
			           }
		            }    
				 echo "</select></td>";
				 
				 //MINUTOS DE APLICACION
			     echo "<td><select name='wminu'>";
			     if (!isset($wminu))
			        {
				     for($f=0;$f<=0;$f+=5)
				       {
					    if (strlen($f) == 1)
					       { $f="0".trim($f); }
					    echo "<option>".trim($f)."</option>";
				       } 
			        }
			       else
			          {
				       echo "<option selected>".$wminu."</option>";   
					   for($f=0;$f<=0;$f+=5)
					     {
					      if (strlen($f) == 1)
					        { $f="0".trim($f); }
					      echo "<option>".trim($f)."</option>";
					     }
			          }    
			     echo "</select></td>";
			     
				 ?>	    
			       <script>
			        function ira(){document.ingreso.wenfer.focus();}
			       </script>
			     <?php
				 
			     if (!isset($wok_enfer) or $wok_enfer=="off")
			        echo "<td colspan=4 align=left><b>Codigo de Auxiliar o Enfermera que aplica :</b><INPUT TYPE='text' NAME='wenfer' SIZE=10></td>";
			       else
			          echo "<td colspan=4 align=left><b>Auxiliar o Enfermera que aplica :</b><INPUT TYPE='text' NAME='wenfer' value='".$wenfer."-".$wnom_enfer."' SIZE=50></td>"; 
			     echo "</tr>";
			     
			     if (isset($wok_enfer) and ($wok_enfer=="on"))
			        {
				     ?>	    
				       <script>
				        function ira(){document.ingreso.warticulo.focus();}
				       </script>
				     <?php
			     
			     
				     echo "<tr class=fila1>";
					 //INGRESO DE ARTICULOS
					 echo "<td colspan=6 align=center><b>Medicamento e Insumo :</b><INPUT TYPE='text' NAME='warticulo' SIZE=10></td>";
					 echo "</tr>";
					 
					 //CODIGO ENFERMERA
			  		 echo "<input type='HIDDEN' name= 'wenfer' value='".$wenfer."'>";
			         //NOMBRE ENFERMERA
			         echo "<input type='HIDDEN' name= 'wnom_enfer' value='".$wnom_enfer."'>";
				    } 
				 
				 if (!isset($wronda))
				    $wronda="";
				}
			    
			 echo "<tr class=link>";																							///se agrega la variable $origen en este enlace de consultar ronda (Actualización Feb-9-2012)
			 echo "<td align=center colspan=6><A href='Consultar_ronda.php?whis=".$whis."&wing=".$wing."&wcenmez=".$wcenmez."&wcco=".$wcco."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&origen=".$origen."'><b><font size=3>Consultar una Hora de Aplicación</font></b></A></td>";
			 echo "</tr>";
			 
			 
		     echo "<tr class=titulo>";
		     echo "<td align=center bgcolor=#cccccc colspan=6><input type='submit' value='ENTER'>&nbsp;&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td>";
		     echo "</tr>";
		     
		     
		     echo "<tr class=link>";
		     echo "<td align=center colspan=7><A href='Aplicacion_med_y_mat.php?wcco=".$wcco."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&hora=".$hora."'><b>Retornar</b></A></td>";
		     echo "</tr>";
		     
		     echo "</form>";
		    } 
		   else 
		      { //Ya esta digitado el articulo y seleccionada la ronda
		       if (isset($wborrar) and $wborrar == "S" and isset($wusuario) and strlen($wusuario) > 0)
			      {
				   //Busco si la aplicacion se hizo por aprovechamiento o no   
				   $q = " SELECT aplapv "
				       ."   FROM ".$wbasedato."_000015 "
				       ."  WHERE aplron                           = '".$wronda."'"
			           ."    AND aplhis                           = '".$whis."'"
			           ."    AND apling                           = '".$wing."'"
			           ."    AND ".$wbasedato."_000015.Fecha_data = '".$wfecha."'"
			           ."    AND aplart                           = '".$warticulo."'"
			           ."    AND aplest                           = 'on' "
			           ."    AND id                               = ".$wid
			           ."    AND aplapr                          != 'on' ";
			       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
				   $row = mysql_fetch_array($res);
				   $wapro=$row[0];      
				   
				      
				   //Borra o anulo la aplicacion del articulo al paciente siempre y cuando no este aprobado (por el auditor) 
		  	       $q = " UPDATE ".$wbasedato."_000015 "
		  	           ."    SET aplest                           = 'off', "
		  	           ."        aplusu                           = '".$wusuario."'"
		               ."  WHERE aplron                           = '".$wronda."'"
			           ."    AND aplhis                           = '".$whis."'"
			           ."    AND apling                           = '".$wing."'"
			           ."    AND ".$wbasedato."_000015.Fecha_data = '".$wfecha."'"
			           ."    AND aplart                           = '".$warticulo."'"
			           ."    AND aplest                           = 'on' "
			           ."    AND id                               = ".$wid
			           ."    AND aplapr                          != 'on' ";
				           
			       $res = mysql_query($q,$conex);
			       
			       //Aca traigo el saldo del articulo para el paciente
				   $q = " SELECT min(ccopap), spacco "  //Tomo la prioridad MAXIMA del centro costo que tenga saldo del articulo
				       ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
				       ."  WHERE spahis = ".$whis
				       ."    AND spaing = ".$wing
				       ."    AND spaart = '".strtoupper($warticulo)."'"
				       ."    AND spacco = ccocod "
				       ."    AND spausa > 0 "					  //Solo los que tengan el articulo aplicado
				       ."  GROUP BY 2 ";
				   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
				   $row = mysql_fetch_array($res);
				   
				   $wccoapl=$row[1];
			       
				   if ($wapro != "on")
				      {
				       $q= " UPDATE ".$wbasedato."_000004 "
			              ."    SET spausa = spausa - ".$wcantidad           //Salidas Unix
			              ."  WHERE spahis = '".$whis."'"
			              ."    AND spaing = '".$wing."'"
			              ."    AND spaart = '".$warticulo."'"
			              ."    AND spacco = '".$wccoapl."'";
			           $res = mysql_query($q,$conex);
		              }
		             else
		                {
				         $q= " UPDATE ".$wbasedato."_000004 "
			                ."    SET spausa = spausa - ".$wcantidad.","    //Salidas Unix
			                ."        spaasa = spaasa - ".$wcantidad        //Salidas por Aprovechamiento
			                ."  WHERE spahis = '".$whis."'"
			                ."    AND spaing = '".$wing."'"
			                ."    AND spaart = '".$warticulo."'"
			                ."    AND spacco = '".$wccoapl."'";
			             $res = mysql_query($q,$conex);
		                } 
		          }
		         else
		            {
			         if (!isset($wusuario) and strlen($wusuario) == 0)
			            {
			             ?>	    
				           <script>
				              alert ("ESTA DESCONECTADO DE MATRIX, CIERRE ESTA VENTANA LUEGO PRESIONE LA TECLA [F5] Y VUELVA A INTENTAR LA ANULACION");
				           </script>
				         <?php
			            } 
		            }   
			   
			   if (!isset($wborrar) or $wborrar == "N")   
			      {  
				   if (strpos($wronda,":")==0)   
				      {     
				       $wron=explode("-",$wronda);
				       $wronda=trim($wron[0]).":".trim($wminu)." - ".trim($wron[1]);   
			          } 
				      
				   //HORA RONDA o APLICACION
			       echo "<input type='HIDDEN' name= 'wronda' value='".$wronda."'>";  
			       //MINUTOS RONDA o APLICACION
			       echo "<input type='HIDDEN' name= 'wminu' value='".$wminu."'>";
			       //ARTICULO
			       echo "<input type='HIDDEN' name= 'warticulo' value='".$warticulo."'>"; 
			       //PACIENTE
			       echo "<input type='HIDDEN' name= 'wpac' value='".$wpac."'>";
			       //HABITACION
			       echo "<input type='HIDDEN' name= 'whab' value='".$whab."'>";
			       
			       
			       //=======================================================================================================================
			       //=======================================================================================================================
			       //En este modulo de instrucciones busco si el codigo digitado pertene a un proveedor o si pertenece a la CENTRAL DE 
			       //MEZCLAS y a su vez si este se encuentra vencido en fecha u hora o no esta vencido.
			       //=======================================================================================================================
			       $wartlot=explode("-",$warticulo);
			       
			       include_once("root/barcod.php");
			       
			       $wartlot[0]=BARCOD($wartlot[0]);
			       
			       //Aca busco si el codigo es de un proveedor y traigo el codigo propio
			       $q= " SELECT artcod "
			          ."   FROM ".$wbasedato."_000009 "
			          ."  WHERE artcba = '".$wartlot[0]."'";
			       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			       $num = mysql_num_rows($res);  
			       if ($num > 0)
			          {
				       $row = mysql_fetch_array($res);
			           $warticulo=$row[0];
			           
			           $wvence='off';
			          }
			         else
			            {
				         //Busco si el producto viene de la CENTRAL DE MEZCLAS, si, si verifico que no este vencido 
				         if (isset($wartlot[1]) and $wartlot[1] != "")
				            {
					         $q = " SELECT count(*) "
					             ."   FROM ".$wcenmez."_000004 "
					             ."  WHERE plocod  = '".$wartlot[1]."'"
					             ."    AND plopro  = '".$wartlot[0]."'"
					             ."    AND plofve >= '".$wfecha."'";
					         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			                 $num = mysql_num_rows($res); 
			                 $row = mysql_fetch_array($res);
			                 
			                 //Si el producto no se ha vencido por fecha verifico que No este vencido por la hora
			                 if ($row[0] > 0)      
					            {
						         $q = " SELECT count(*) "
						             ."   FROM ".$wcenmez."_000004 "
						             ."  WHERE plocod  = '".$wartlot[1]."'"
						             ."    AND plopro  = '".$wartlot[0]."'"
						             ."    AND plohve >= '".$whora."'";
						         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				                 $num = mysql_num_rows($res); 
				                 $row = mysql_fetch_array($res); 
				                 
				                 if ($row[0] <=0)  //Si es igual a cero es porque el producto esta vencido por la hora
				                    $wvence='on';
				                   else
				                      $wvence='off'; 
					            }
					           else
					              $wvence='on';     
				            } 
				           else
				              $wvence='off';        
				        }
				   //==========================================================================================================================
				   //==========================================================================================================================     
			        
				   
			       if (isset($warticulo) and !isset($wdesc) and $wvence=="off")
				      {
					   //Aca traigo el saldo del articulo para el paciente
					   $q = " SELECT sum(spauen-spausa), max(ccopap), spacco "  //Tomo la prioridad MAXIMA del centro costo que tenga saldo del articulo
					       ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
					       ."  WHERE spahis          = '".$whis."'"
					       ."    AND spaing          = '".$wing."'"
					       ."    AND spaart          = '".strtoupper($warticulo)."'"
					       ."    AND spacco          = ccocod "
					       ."    AND round((spauen-spausa),3) > 0 "                      //Con esto traigo solo los registros que tengan saldo. 20081028 tavo , se coloca la palabra round para que haga redondeo del saldo en forma exacta
					       ."  GROUP BY 3 "
					       ."  ORDER BY 2 desc ";
					   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
					   $num = mysql_num_rows($res);  
					   if ($num > 0)
					      {
						   $row = mysql_fetch_array($res);   
					       $wsal   =$row[0];
					       $wccoapl=$row[2];   //Aca defino de que centro de costos se va aplicar el insumo
				          } 
					     else
					        {
					         $wsal=0; 
					         $wccoapl=$wcco;   //Aca defino de que centro de costos se va aplicar el insumo
				            } 
					   	  
					   if ($wsal > 0)  //Si hay saldo se puede hacer la aplicacion
					      {
					       //Por aca indica que si se puede aplicar el medicamento
			               $q = " SELECT artcom, unides "
			                   ."   FROM ".$wbasedato."_000026, ".$wbasedato."_000027 "
					           ."  WHERE artcod = '".strtoupper($warticulo)."'"
					           ."    AND artuni = unicod ";
					       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			               $num = mysql_num_rows($res);
			                
					       if ($num > 0)
					          {
					           $row = mysql_fetch_array($res);   
			               	   $wdesc = $row[0];
					           $wunid = $row[1];
				              }
				             else
				                {
					             //Por aca busco si el articulo es de la central de produccion
				                 $q = " SELECT artcom, unides "
				                     ."   FROM ".$wcenmez."_000002, ".$wbasedato."_000027 "
						             ."  WHERE artcod = '".strtoupper($warticulo)."'"
						             ."    AND artuni = unicod ";
						         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				                 $num = mysql_num_rows($res);   
					             
				                 if ($num > 0)
				                    {
					                 $row = mysql_fetch_array($res);   
			               	   		 $wdesc = $row[0];
					           		 $wunid = $row[1];   
					                }       
				                }      
					         
					       ?>	    
			                 <script>
			                  function ira(){document.ingreso.wcantidad.focus();}
			                  function ira(){document.ingreso.wcantidad.select();}
			                 </script>
			               <?php
					       
			               echo "<tr class=encabezadoTabla><td align=center colspan=5><b>Hora Aplicación : </b>".$wronda."</td></tr>";
			               
			               echo "<tr class=fila1>";
					       echo "<td>Articulo : ".strtoupper($warticulo)."</td>";
					       echo "<td>Descripción : ".$wdesc."</td>";
					       echo "<td>Presentación : ".$wunid."</td>";
					       echo "<td>Cantidad : <INPUT TYPE='text' NAME='wcantidad' SIZE = 6 VALUE=1 onkeypress='if ((event.keyCode < 46 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></td>";
					       echo "</tr>";
					              
					       //DESCRIPCION
			               echo "<input type='HIDDEN' name= 'wdesc' value='".$wdesc."'>";
			               //PACIENTE
			               echo "<input type='HIDDEN' name= 'wpac' value='".$wpac."'>";
			               //HABITACION
			               echo "<input type='HIDDEN' name= 'whab' value='".$whab."'>";
			               //SALDO
			               echo "<input type='HIDDEN' name= 'wsal' value='".$wsal."'>";
			               //CENTRO DE COSTO PARA LAS SALIDAS O APLICACION
			               echo "<input type='HIDDEN' name= 'wccoapl' value='".$wccoapl."'>";
			              }
			             else
				            {
					         //===================================================================================================================================================================================
					         //ACA SE INGRESAN LOS ARTICULOS QUE TRAEN LOS PACIENTES CON EL CODIGO <999> Y SE DIGITA LA DESCRIPCION Y LA CANTIDAD DE C/U  DE ELLOS
					         //===================================================================================================================================================================================
					         if ($warticulo == "999")  //Si digito 999 es porque es un articulo que trajo el paciente.
					            {
						         ?>	    
			                      <script>
			                         function ira(){document.ingreso.wdesc.focus();}
			                      </script>
			                     <?php   
					             echo "<td bgcolor=#00CCFF colspan=3><b>Descripción del articulo (externo) :</b><INPUT TYPE='text' NAME='wdesc' SIZE=60></td>";
					             echo "<td bgcolor=#00CCFF>Cantidad : <INPUT TYPE='text' NAME='wcantidad' SIZE = 6 VALUE=1 onkeypress='if ((event.keyCode < 46 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></td>";
					             echo "<tr><td align=center bgcolor=00CCFF colspan=5><input type='submit' value='OK'></td></tr>";
					             echo "<tr class=link><A href='Aplicacion_med_y_mat_ingreso.php?wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."'> Retornar</A></tr>";
				                } 
				               else 
				                  {
					               //Por aca indica que no se puede aplicar el medicamento porque no existe el articulo en la historia del paciente
						           echo "</tr><tr class=encabezadoTabla><td colspan=5><font size=4><b><CENTER>!!! El articulo no existe en la historia del paciente o No tiene Saldo pendiente para Aplicar. Articulo : ".$warticulo." ¡¡¡</b></font></td></tr>";
						           
						           
						           if (isset($row[2]) and isset($row[3]))
						              {
						               echo "<tr class=link>";
						               echo "<td align=center colspan=3><font size=3><b><A href='Aplicacion_med_y_mat_ingreso.php?whis=".$whis."&wronda=".$wronda."&wing=".$wing."&wborrar=S"."&warticulo=".$row[0]."&wccoapl=".$wccoapl."&wcantidad=".$row[2]."&wpac=".$wpac."&whab=".$whab."&wcco=".$wcco."&wid=".$row[3]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&wfecha=".$wfecha."'>Retornar</A></b></font></td>";
						               echo "</tr>";
					                  }
					                 else
					                    {
						                 $wid="";   
					                     echo "<tr class=link>";
						                 echo "<td align=center colspan=3><b><A href='Aplicacion_med_y_mat_ingreso.php?whis=".$whis."&wronda=".$wronda."&wing=".$wing."&wborrar=S"."&warticulo=".$row[0]."&wccoapl=".$wccoapl."&wcantidad=0&wpac=".$wpac."&whab=".$whab."&wcco=".$wcco."&wid=".$wid."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&wfecha=".$wfecha."'>Retornar</A></b></td>";
						                 echo "</tr>";  
					                    } 
					              } 
				            } 
			          } //Fin del then de isset($warticulo) and !isset($wdesc)      
					 else
					    {
						 if ($wvence=="off")   //Si entra es porque el artciulo digitado no esta vencido
			                {    
			                 if ($warticulo == "999")
						        $wdif=0;
						       else 
						          {
						           $wdif=$wsal-$wcantidad;
					              } 
				             
					         //Aca valido si el articulo puede ser grabado o no
						     if ($wdif >= 0)       //Si la cantidad que queda es mayor o igual a cero puede grabarse el articulo en MATRIX
						        {
							      
							     $warticulo=strtoupper($warticulo);
							     $wdesc=strtoupper($wdesc);
							     
							     if ($wdesc != "")
							        {     
								     //================================================================================================================
								     //Con este procedimiento verifico como debo grabar la aplicación, si por aprovechamiento o parte aprovechamiento y
								     //parte normal o si todo normal.   
								     //================================================================================================================
								     //Traigo todos los valores de la tabla de saldos   
								     $q = " SELECT spauen, spausa, spaaen, spaasa "
								         ."   FROM ".$wbasedato."_000004 "
								         ."  WHERE spahis = '".$whis."'"
								         ."    AND spaing = '".$wing."'"
								         ."    AND spaart = '".$warticulo."'"
								         ."    AND spacco = '".$wccoapl."'";
								     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());     
								     $row = mysql_fetch_array($res);
								     
								     $wsaldosimple=$row[0]-$row[1];
								     $wsaldoaprove=$row[2]-$row[3];
								     $wcanaprov=0;
								     
								     if ($wsaldoaprove>0)                           //Si Tiene saldo de aprovechamiento
								        {
									     $waprovecha="on";   
								         if ($wsaldoaprove>=$wcantidad)            //Si el saldo que tiene alcanza para aplicar
								            {
								             $wcanaprov=$wcantidad;
								             $wapro_parcial="off";
							                } 
								           else
								             {
									          $wapro_parcial="on";   
									          $wcanaprov=$wcantidad-$wsaldoaprove; //Coloco la cantidad a grabar por aprovechamiento
								             } 
								        }
							           else                                        //No tiene saldo de aprovechamiento grabo normal
							              {
							               $waprovecha="off";     
							               $wapro_parcial="off";
							              } 
						             //================================================================================================================    
								     //================================================================================================================
								           
								     if (!isset($waprovecha) or $waprovecha=='off' and isset($wusuario) and strlen($wusuario)>0)   
								        {   //SI ** NO ** ES APROVECHAMIENTO
									     //Aca actualizo el saldo del articulo por paciente 
									     $q= " UPDATE ".$wbasedato."_000004 "
								            ."    SET spausa = spausa + ".$wcantidad           //Salidas Unix
								            ."  WHERE spahis = '".$whis."'"
								            ."    AND spaing = '".$wing."'"
								            ."    AND spaart = '".$warticulo."'"
								            ."    AND spacco = '".$wccoapl."'";
							            }
							           else
							              { //SI ES APROVECHAMIENTO
							                if (isset($wusuario) and strlen($wusuario)>0)
							                   {
											    //Aca actualizo el saldo del articulo por paciente 
											    $q= " UPDATE ".$wbasedato."_000004 "
										           ."    SET spausa = spausa + ".$wcantidad.","     //Salidas Unix
										           ."        spaasa = spaasa + ".$wcanaprov         //Salidas Aprovechamientos
										           ."  WHERE spahis = '".$whis."'"
										           ."    AND spaing = '".$wing."'"
										           ."    AND spaart = '".$warticulo."'"
										           ."    AND spacco = '".$wccoapl."'";
										          
										        if ($wapro_parcial=="on")
										           {
												    traer_DosisyFraccion($whis, $wing, $warticulo, $wfecha, $wdosis, $wfraccion); 
													
													/************************************************************************************************
													 * Noviembre 29 de 2011
													 ************************************************************************************************/
													if( empty( $wdosis ) ){	//Indica que no se encontro en el kardex
														$datos = consultarFraccionPorArticulo( $warticulo );
														
														$wdosis = $datos['fraccion']*$wcanaprov;	//fraccion
														$wfraccion = $datos['unidad'];				//unidad de fraccion
													}
													/************************************************************************************************/
												   
											        $q1= " INSERT INTO ".$wbasedato."_000015 (   Medico       ,   Fecha_data ,   Hora_data ,   aplron    ,   aplhis  ,  apling   ,   aplcco  ,   aplart                   ,   apldes   ,  aplcan      ,   aplusu      , aplapr , aplest,   aplapv        ,   aplfec    , aplapl       , aplnen,    aplufr       ,   apldos    , Seguridad        ) "
									                   ."                             VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$wronda."','".$whis."','".$wing."','".$wcco."','".strtoupper($warticulo)."','".$wdesc."',".$wcanaprov.",'".$wusuario."', 'off'  , 'on'  ,'".$waprovecha."','".$wfecha."','".$wenf[0]."', 'off' , '".$wfraccion."','".$wdosis."', 'C-".$wusuario."') ";
									                $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
									               
									                $wapro_parcial="off";
									               }
									              else
									                 $wcanaprov=0;
								               }
								              else
								                 {
									              ?>	    
										           <script>
										              alert ("ESTA DESCONECTADO DE MATRIX, CIERRE ESTA VENTANA LUEGO PRESIONE LA TECLA [F5] Y VUELVA A INTENTAR ");
										           </script>
										          <?php
									             }             
								          }     
							         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
								        
								     //Si entra por aca es porque ya se valido y por ende puede grabar la aplicacion del articulo
					     		     //Aca grabo el nombre o descripción del articulo 
					     		     if ($wcantidad > 0 and $wapro_parcial=="off" and isset($wusuario) and strlen($wusuario)>0)
					     		        {                        
                                         traer_DosisyFraccion($whis, $wing, $warticulo, $wfecha, $wdosis, $wfraccion);
										 
										 /************************************************************************************************
										  * Noviembre 29 de 2011
										  ************************************************************************************************/
										if( empty( $wdosis ) ){	//Indica que no se encontro en el kardex
											$datos = consultarFraccionPorArticulo( $warticulo );
											
											$wdosis = $datos['fraccion']*($wcantidad-$wcanaprov);	//fraccion
											$wfraccion = $datos['unidad'];				//unidad de fraccion
										}
										/************************************************************************************************/
										 
						     		     $q= " INSERT INTO ".$wbasedato."_000015 (   Medico       ,   Fecha_data ,   Hora_data ,   aplron    ,   aplhis  ,  apling   ,   aplcco  ,   aplart                   ,   apldes   ,  aplcan                   ,   aplusu      , aplapr , aplest ,   aplapv        ,   aplfec    ,   aplapl     , aplnen,    aplufr       ,   apldos    , Seguridad        ) "
							                ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$wronda."','".$whis."','".$wing."','".$wcco."','".strtoupper($warticulo)."','".$wdesc."',".($wcantidad-$wcanaprov).",'".$wusuario."', 'off'  , 'on'   ,'".$waprovecha."','".$wfecha."','".$wenf[0]."', 'off' , '".$wfraccion."','".$wdosis."', 'C-".$wusuario."') ";
							             $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							            }
							           else
							             if (!isset($wusuario) or strlen($wusuario)==0)
							               {
								            ?>	    
									          <script>
									             alert ("ESTA DESCONECTADO DE MATRIX, CIERRE ESTA VENTANA LUEGO PRESIONE LA TECLA [F5] Y VUELVA A INTENTAR ");
									          </script>
									        <?php   
								           }    
					                }
					               else
				                      echo "</tr><tr class=fila1><td colspan=5><font size=4><b><CENTER>!!! Debe digitar una descripción para el articulo : ".$warticulo.". NO GRABADO¡¡¡</b></font></td></tr>";    
				                      
				                 $q = " SELECT aplart, apldes, aplcan, id "
					                 ."   FROM ".$wbasedato."_000015 "
					                 ."  WHERE aplhis     = '".$whis."'"
					                 ."    AND apling     = '".$wing."'"
					                 ."    AND Fecha_data = '".$wfecha."'"
					                 ."    AND aplron     = '".$wronda."'"
					                 ."    AND aplest     = 'on' ";
					             $res = mysql_query($q,$conex);  
					             $wnr = mysql_num_rows($res);   
				                      
					             echo "<tr class=fila1><td align=center colspan=5><b>Hora Aplicacion : ".$wronda."</b></td></tr>";
						         
					             echo "<tr class=encabezadoTabla>";
				                 echo "<th>Articulo</th>";
				                 echo "<th>Descripción</th>";
				                 echo "<th>Presentación</th>";
				                 echo "<th>Cantidad</th>";
				                 echo "<th>&nbsp</th>";
				                 echo "</tr>";
				                              
						         $i=1;
						         while ($i <= $wnr)
					              {
						           $row = mysql_fetch_row($res);
						       
						           if ($row[0] != "999")   //Articulo de farmacia
						              {
							           $q = " SELECT unides "
				     			           ."   FROM ".$wbasedato."_000026, ".$wbasedato."_000027 "
					     		           ."  WHERE artcod = '".$row[0]."'"
						     	           ."    AND artuni = unicod ";
							           $res1 = mysql_query($q,$conex);
						               $wpres = mysql_fetch_row($res1);
						              } 
						       
						           if ($wpres[0]=="")
						              {
							           $q = " SELECT unides "
				     			           ."   FROM ".$wcenmez."_000002, ".$wbasedato."_000027 "
					     		           ."  WHERE artcod = '".$row[0]."'"
						     	           ."    AND artuni = unicod ";
							           $res1 = mysql_query($q,$conex);
						               $wpres = mysql_fetch_row($res1);   
							          }     
						           
						           if (is_integer($i/2))
					                  $wclass="fila1";
					                 else
					                    $wclass="fila2";
					                    
					               echo "<tr class=".$wclass.">";
					               echo "<td>".$row[0]."</td>";
					               echo "<td>".$row[1]."</td>";
					               echo "<td>".$wpres[0]."</td>";
					               echo "<td align=center>".$row[2]."</td>";
					               echo "<td align=center class=link><b><A href='Aplicacion_med_y_mat_ingreso.php?whis=".$whis."&wronda=".$wronda."&wing=".$wing."&wborrar=S"."&warticulo=".$row[0]."&wccoapl=".$wccoapl."&wcantidad=".$row[2]."&wpac=".$wpac."&whab=".$whab."&wcco=".$wcco."&wid=".$row[3]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&wfecha=".$wfecha."&wronda=".$wronda."&wenfer=".$wenfer."'>Anular</A></b></td>";
					               echo "</tr>";
					               
					               $i=$i+1;
			                      }
				                }
				               else
				                  {
				                   echo "</tr><tr><td bgcolor=#66CC99 colspan=4><font size=4><b><CENTER>!!! No puede Grabar el articulo porque la cantidad colocada superaria la grabada al paciente. Articulo : ".$warticulo." ¡¡¡</b></font></td></tr>"; 
				                  }
				             unset ($warticulo);
				             unset ($wdesc);
				             unset ($wcantidad);
				             
				             ?>	    
				              <script>
				                function ira(){document.ingreso.warticulo1.focus();}
				              </script>
				             <?php
				             
				             //INGRESO DE ARTICULOS
					         echo "<tr class=encabezadoTabla><td align=center colspan=240>Articulo : <INPUT TYPE='text' NAME='warticulo1' SIZE = 10></td></tr>";
					         
					         echo "</table><br>";
				             echo "<font size=3><A href='Aplicacion_med_y_mat_ingreso.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."'> Retornar</A class=link></font>";
			               } // Fin del if ($wvence=='off')
			            } //////////////////////////
		       	  } //Fin del then de if (isset($wborrar) and $wborrar == "S")
		       	 else
		       	    {
			       	 //Si entra por aca es porque se acabo de borrar una aplicacion   
			       	 echo "<tr class=encabezadoTabla>";
		             echo "<th>Articulo</th>";
		             echo "<th>Descripción</th>";
		             echo "<th>Presentación</th>";
		             echo "<th>Cantidad</th>";
		             echo "<th>&nbsp</th>";
		             echo "</tr>";    
		             
		             $q = " SELECT aplart, apldes, aplcan, id "
		                 ."   FROM ".$wbasedato."_000015 "
		                 ."  WHERE aplhis     = '".$whis."'"
		                 ."    AND apling     = '".$wing."'"
		                 ."    AND Fecha_data = '".$wfecha."'"
		                 ."    AND aplron     = '".$wronda."'"
		                 ."    AND aplest     = 'on' ";
		             $res = mysql_query($q,$conex);  
		             $wnr = mysql_num_rows($res);  
		             
		             $i=1;
			         while ($i <= $wnr)
			              {
				           $row = mysql_fetch_row($res);
				       
				           if ($row[0] != "999")   //Articulo traido por el paciente
				              {
					           $q = " SELECT unides "
		     			           ."   FROM ".$wbasedato."_000026, ".$wbasedato."_000027 "
			     		           ."  WHERE artcod = '".$row[0]."'"
				     	           ."    AND artuni = unicod ";
					           $res1 = mysql_query($q,$conex);
				               $wpres = mysql_fetch_row($res1);
				              } 
				       
			               if (is_integer($i/2))
			                  $wclass="fila1";
			                 else
			                    $wclass="fila2";
			                    
			               echo "<tr class=".$wclass.">";
			               echo "<td>".$row[0]."</td>";
			               echo "<td>".$row[1]."</td>";
			               echo "<td>".$wpres[0]."</td>";
			               echo "<td align=center>".$row[2]."</td>";                                                                  //$warticulo) or !isset($wronda) or !isset($wenfer)
			               echo "<td align=center class=link><b><A href='Aplicacion_med_y_mat_ingreso.php?whis=".$whis."&wronda=".$wronda."&wing=".$wing."&wborrar=S"."&warticulo=".$row[0]."&wccoapl=".$wccoapl."&wcantidad=".$row[2]."&wpac=".$wpac."&whab=".$whab."&wcco=".$wcco."&wid=".$row[3]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&wfecha=".$wfecha."&wronda=".$wronda."&wenfer=".$wenfer."'>Anular</A></b></td>";
			               echo "</tr>";
			               
			               $i=$i+1;
		                  }
		              $wborrar="N";  
		              
		              unset ($warticulo);
		              unset ($wdesc);
		              unset ($wcantidad);
		             
		              echo "<input type='HIDDEN' name='wronda' value='".$wronda."'>";
		               
		              ?>	    
		               <script>
		                  function ira(){document.ingreso.warticulo1.focus();}
		               </script>
		              <?php
		              
		             
		              //INGRESO DE ARTICULOS
			          echo "<tr class=encabezadoTabla><td align=center colspan=240>Articulo : <INPUT TYPE='text' NAME='warticulo1' SIZE=10></td></tr>";
			         
			          echo "</table><br>";
		              echo "<A href='Aplicacion_med_y_mat_ingreso.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."' class=link> Retornar</A>";    
			       	}     
		      } // else de todos los campos setiados
	  	  }
	  	 else
	        {
		     ?>	    
	           <script>
	              alert ("LA HISTORIA DIGITADA ESTA PENDIENTE DE RECIBO EN ESTE SERVICIO ");
	           </script>
	         <?php
	         
	         echo "</table>";
	         echo "<br><br><br>";
	         echo "<table align=center>";
	         echo "<tr>";
	         echo "<A href='Aplicacion_med_y_mat.php?wcco=".$wcco."&amp;wbasedato=".$wbasedato."&amp;wemp_pmla=".$wemp_pmla."&amp;wcenmez=".$wcenmez."'> Retornar</A class=link>";    
	         echo "</tr>";
	         echo "</table>";
	        }    
      
      } // Fin del then si la historia existe
     else
        {
	     ?>	    
           <script>
              alert ("LA HISTORIA DIGITADA NO EXISTE EN ESTE SERVICIO O ESTA PENDIENTE DE RECIBIR");
           </script>
         <?php
         
         echo "</table>";
         echo "<br><br><br>";
         echo "<table align=center>";
         echo "<tr>";
         echo "<A href='Aplicacion_med_y_mat.php?wcco=".$wcco."&amp;wbasedato=".$wbasedato."&amp;wemp_pmla=".$wemp_pmla."&amp;wcenmez=".$wcenmez."'> Retornar</A class=link>";    
         echo "</tr>";
         echo "</table>";
        }              
      
} // if de register

echo "<br>";
?>