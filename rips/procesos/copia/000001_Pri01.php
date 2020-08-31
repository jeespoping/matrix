<head>
  <title>GENERACION DE RIPS</title>
</head>
<body BACKGROUND="/inetpub/wwwroot/matrix/images/medical/root/fondo de bebida de limón.gif">
<?php
include_once("conex.php");
  /***************************************************
	*	          GENERAR LOS RIPS                   *
	*	  PARA LA TORRE MEDICA LAS AMERICAS	V.1.04	 *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		            
	$key = substr($user,2,strlen($user));
	

	

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	
	
	                                                              // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	$wactualiz="(Actualizado a Julio 23 de 2004)";                 // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                              // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	
	
	$usuario = substr($user,strpos("-",$user)+2,strlen($user));
		
	$query = "select codigo,prioridad,grupo from usuarios where codigo='".$usuario."' and activo = 'A'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	$prioridad=$row[1];
	$usuario=$row[0];
	$grupo=strtolower($row[2]);
		
	
	//echo "Consultorio : ".$consul."...Medico : ".$medico.".....Empresa : ".$emp;
	
	if(!isset($medico)  or !isset($emp)  or !isset($consul) )
	  {
		echo "<br>";				
		echo "<br>";
		echo "<br>";
		echo "<br>";
		echo "<br>";
		echo "<br>";
		
		echo "<form action='000001_Pri01.php' method=post>";
		echo "<center><table border=2 width=400>";
		echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=6 text color=#CC0000><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td align=center colspan=3 bgcolor=#fffffff><font size=6 text color=#CC0000><b>TORRE MEDICA LAS AMERICAS </b></font></td></tr>";
		echo "<tr><td align=center colspan=3 bgcolor=#fffffff><font size=6 text color=#CC0000><b>GENERACION DE RIPS</b></font></td></tr>";
		echo "<tr><td align=center colspan=3 bgcolor=#fffffff><font size=3 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";
		echo "<tr></tr>";
		
		
		/*=============================================================================*/
		/*===== A C A   S E   S E L E C C I O N A   E L   C O N S U L T O R I O =======*/
		/*=============================================================================*/
		/*=============================================================================*/
		if (!isset($consul))
		 {
		   echo "<tr><td bgcolor=#cccccc colspan=1><font size=6><b>Seleccione el CONSULTORIO: </b></font></td>";	
	 		    	   
		  
   	       /* Si el medico no ha sido escogido Buscar a los medico registrados para 
	 	   construir el drop down*/
		   echo "<td bgcolor=#cccccc colspan=2><SELECT name='consul'>";
		   $query = "        SELECT rips_000005.Consultorio ";
		   $query = $query."   FROM root_000017, rips_000005 ";
		   $query = $query."  WHERE rips_000005.usuario = '".$usuario."'";
		   $query = $query."    AND rips_000005.consultorio = root_000017.consultorio ";
		   $query = $query."  GROUP BY Consultorio";
		   $err = mysql_query($query,$conex);
		   $num = mysql_num_rows($err);
					
		   //echo "Query : ".$query;
		   
	       if($num>0)
		     {
			  for ($j=0;$j<$num;$j++)
			     {	
				  $row = mysql_fetch_array($err);
				  $consul=$row[0];
				  				  										
				  echo "<option selected>".$row[0]."</option>";
				  
				 }
			 }  	// fin del if $num>0
		     echo"</select></td></tr><tr><td align=center bgcolor=#cccccc></td>";
		     //echo"<td align=center bgcolor=#cccccc>Todo <input type='checkbox' name='cert1'></td>";
		     echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
		     exit;   //Este exit hace que no muestre el resto campos $medico y $emp, pero luego de seleccionar el consultorio si los pide
	      }  
		
		
	    /*===========================================================================================*/
		/*===== A C A   E S T A   Y A   S E L E C C I O N A D O   E L   C O N S U L T O R I O =======*/
		/*===========================================================================================*/
		/*===========================================================================================*/
		echo "<tr><td bgcolor=#cccccc colspan=1><b>CONSULTORIO: </b></td>";	
	 	
   	    /* Si el medico no ha sido escogido Buscar a los medico registrados para 
	 	   construir el drop down*/
	 	echo "<td bgcolor=#cccccc colspan=2><SELECT name='consul'>";
		$query = "SELECT Consultorio FROM root_000017 WHERE Consultorio = '".$consul."' GROUP BY Consultorio";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
				   		   			
	    if ($num>0)
		  {
		   for ($j=0;$j<$num;$j++)
		     {	
		      $row = mysql_fetch_array($err);
			  $consul=$row[0];
											
			  echo "<option selected>".$row[0]."</option>";
			 }
		  }  // fin del if $num>0  
	         		
		
		/*====================================================================*/
		/*====== A C A   S E   S E L E C C I O N A   E L   M E D I C O =======*/
		/*====================================================================*/
		/*====================================================================*/
		echo "<tr><td bgcolor=#cccccc colspan=1><b>MEDICO: </b></td>";	
			
		/* Si el medico no ha sido escogido Buscar a los medico registrados para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><SELECT name='medico'>";
		$query = "SELECT Consultorio, Documento, Nombre FROM root_000017 WHERE Consultorio='".$consul."' ORDER BY Consultorio";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
				
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				$medico=$row[0]."-".$row[1]."-".$row[2];
															
				echo "<option selected>".$row[0]."-".$row[1]."-".$row[2]."</option>";
			}
		}	// fin del if $num>0
			
			
		/*===================================================================*/
		/*===== A C A   S E   S E L E C C I O N A   L A    E M P R E S A ====*/
		/*===================================================================*/
		/*===================================================================*/
		echo "<tr><td bgcolor=#cccccc colspan=1><b>EMPRESA: </b></td>";	
		
		/* Si la empresa no ha sido escogida Buscar a las empresas registradas para 
		construir el drop down*/
		echo "<td bgcolor=#cccccc colspan=2><SELECT name='emp'>";
		$query = "SELECT Codigo, Nit, Nombre FROM rips_000002 WHERE seguridad like '%".$usuario."%' ORDER BY Codigo";
					
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
					
		if($num>0)
		{
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				$emp=$row[0]."-".$row[1]."-".$row[2];
											
				echo "<option selected>".$row[0]."-".$row[1]."-".$row[2]."</option>";
								
			}
		}	// fin del if $num>0
		echo "</TABLE></BR>";
				
		/*====================================================================================================================*/
		/*======================================   F E C H A   I N I C I A L   ===============================================*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
		$Y=date("Y");	//año actual
		$M=date("m");	//mes actual
		$D=date("d");	//dia actual
		$i=1;

		$anoi=$Y; // primera inicializacion para las variables de fecha
		$mesi=$M;
		$diai=$D;

   	    if(isset($anoi) and ($anoi != $Y or $mesi != $M or $diai !=$D))// y el drop down se modifico
          echo "";
          
        echo "<center><table border=2 width=400>";  
        echo "<tr><td bgcolor=#cccccc ><b>FECHA INICIAL DE LOS RIPS:  </b>";
		// inicialización de la ayuda (drop down de fecha)
		echo "<select name='anoi'>";
		for($f=1900;$f<2051;$f++)
		   {
		    if($f == $anoi)
		      echo "<option selected>".$f."</option>";
		     else
		        echo "<option>".$f."</option>";
		   }
		echo "</select><select name='mesi'>";
		for($f=1;$f<13;$f++)
		     {
			  if($f == $mesi)
			    if($f < 10)
			      echo "<option selected>0".$f."</option>";
				 else
				    echo "<option selected>".$f."</option>";
			   else
			     if($f < 10)
			       echo "<option>0".$f."</option>";
			      else
			  	     echo "<option>".$f."</option>";
			 }
		  echo "</select><select name='diai'>";
		  for($f=1;$f<32;$f++)
		    {
		     if($f == $diai)
		        if($f < 10)
		           echo "<option selected>0".$f."</option>";
			      else
			         echo "<option selected>".$f."</option>";
			  else
			 	if($f < 10)
			 	   echo "<option>0".$f."</option>";
			 	  else
			 	     echo "<option>".$f."</option>";
			}
		  ///echo "</select></td></tr>";
		  echo "</select></td>";
		  ///echo "</TABLE></BR>";
		/*====================================================================================================================*/
		
		

		/*====================================================================================================================*/
		/*======================================     F E C H A   F I N A L     ===============================================*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
		$Y=date("Y");	//año actual
		$M=date("m");	//mes actual
		$D=date("d");	//dia actual
		$i=1;

		$anof=$Y; // primera inicializacion para las variables de fecha
		$mesf=$M;
		$diaf=$D;

   	    if(isset($anof) and ($anof != $Y or $mesf != $M or $diaf !=$D))// y el drop down se modifico
          echo "";
          
        ///echo "<center><table border=2 width=400>";  
        ///echo "<tr><td bgcolor=#cccccc ><b>FECHA FINAL DE LOS RIPS:  </b>";
        echo "<td bgcolor=#cccccc ><b>FECHA FINAL DE LOS RIPS:  </b>";
		// inicialización de la ayuda (drop down de fecha)
		echo "<select name='anof'>";
		for($f=1900;$f<2051;$f++)
		   {
		    if($f == $anof)
		      echo "<option selected>".$f."</option>";
		     else
		        echo "<option>".$f."</option>";
		   }
		echo "</select><select name='mesf'>";
		for($f=1;$f<13;$f++)
		     {
			  if($f == $mesf)
			    if($f < 10)
			      echo "<option selected>0".$f."</option>";
				 else
				    echo "<option selected>".$f."</option>";
			   else
			     if($f < 10)
			       echo "<option>0".$f."</option>";
			      else
				     echo "<option>".$f."</option>";
			 }
		  echo "</select><select name='diaf'>";
		  for($f=1;$f<32;$f++)
		    {
		     if($f == $diaf)
		        if($f < 10)
		           echo "<option selected>0".$f."</option>";
			      else
			         echo "<option selected>".$f."</option>";
			  else
				if($f < 10)
				   echo "<option>0".$f."</option>";
				  else
				     echo "<option>".$f."</option>";
			}
		  echo "</select></td></tr>";
		  ///echo "</TABLE></BR>";
		  
		/*====================================================================================================================*/
				
		
		/*====================================================================================================================*/
		/*==============================     F A C T U R A   I N I C I A L   Y   F I N A L     ===============================*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
		echo "<tr><td bgcolor=#cccccc ><b>Factura Inicial</b><INPUT TYPE='text' NAME=Fact_ini></td>";
		echo "<td bgcolor=#cccccc ><b>Factura Final</b><INPUT TYPE='text' NAME=Fact_fin></td></tr>";
		
		echo "</TABLE></BR>";
		
		/*====================================================================================================================*/
		
		
		echo"</select></td></tr><tr><td align=center bgcolor=#cccccc></td>";
		//echo"<td align=center bgcolor=#cccccc>Todo <input type='checkbox' name='cert'></td>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
 	}
	else 
	 /********************************** 
	  * TODOS LOS PARAMETROS ESTAN SET *
	  **********************************/
	{
		/*====================================================================================================================*/
		/*====================================================================================================================*/
		/*====================  A C A   C O M I E N Z A   L A   G E N E R A C I O N   D E   L O S   R I P S  =================*/
		/*====================  A C A   C O M I E N Z A   L A   G E N E R A C I O N   D E   L O S   R I P S  =================*/
		/*====================================================================================================================*/
		/*====================================================================================================================*/
		
		$wfechai=$anoi."/".$mesi."/".$diai;
		$wfechaf=$anof."/".$mesf."/".$diaf;
		$pos1=strpos($medico,"-");
		$wconsul = substr($medico,0,$pos1);
		$pos2=strpos($medico,"-",$pos1+1);
		$wmedico = substr($medico,$pos1+1,$pos2-$pos1-1);
		$wnommed = substr($medico,$pos2+1,strlen($medico));
											
		// =================================================================================================================================
		// Consecutivo para los rips  ======================================================================================================
		// =================================================================================================================================
		$q = "    SELECT Consecutivo ";                   
		$q = $q."   FROM root_000017 ";
		$q = $q."  WHERE Documento = '".$wmedico."'";
														
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		$canct = 0;
											
		if($num>0)
		  {
		   $row = mysql_fetch_array($err);               //Tomo el consecutivo para los archivos de los rips
		   $conse = $row[0];
		   
		   //ECHO "CONSECUTIVO : ".$conse;
		  }
		 else
		   {
			echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "<center><font size=5 text color=#CC0000><b>!! EL MEDICO SELECCIONADO NO TIENE UNA CONFIGURACION DEFINIDA ¡¡</b></font></center><br>"; 	
		    exit; 
	       }
	    // ================================================================================================================================
	    // ================================================================================================================================
		
	    
		/*====================================================================================================================*/
		/*================================  A C A   C O M I E N Z A   E L   A R C H I V O   U S  =============================*/
		/*====================================================================================================================*/
		$q = "    SELECT mid(mid(rips_000001.Tipo_de_identificacion,1,locate('-',rips_000001.Tipo_de_identificacion)-1),1,2) AS tipid, ";  // Tipo de identificacion
		$q = $q."        mid(mid(rips_000001.Documento,1,locate('-',rips_000001.Documento)-1),1,10) AS docu, ";     // Documento de lusuario
		$q = $q."        mid(rips_000002.Codigo_ms,1,6), ";                                                         // Codigo entidad administradora
		$q = $q."        '5' AS tipusu, ";                                                                // Tipo de usuario 5 = otro
		$q = $q."        mid(rips_000004.Apellido1,1,30), ";                                              // Primer Apellido
		$q = $q."        mid(rips_000004.Apellido2,1,30), ";                                              // Segundo Apellido
		$q = $q."        mid(rips_000004.Nombre1,1,20), ";                                                // Primer Nombre
		$q = $q."        mid(rips_000004.Nombre2,1,20), ";                                                // Segundo Nombre
		$q = $q."        round(((to_days(current_date)-to_days(rips_000004.Fecha_nacimiento))/365),0) AS edad, ";  // Edad
		$q = $q."        '1' AS unimed, ";                                                                // Unidad de medida de la edad 1:Años
		$q = $q."        mid(mid(rips_000004.Sexo,1,locate('-',rips_000004.Sexo)-1),1,1) AS sexo, ";               // Sexo
		$q = $q."        '05' AS dep, ";                                                                  // Departamento de residencia
		$q = $q."        '001' AS ciu, ";                                                                 // Ciudad de residencia
		$q = $q."        'U' AS zona ";                                                                   // Zona de residencia U:rbana
		$q = $q."   FROM rips_000001, rips_000002, rips_000004 ";
		$q = $q."  WHERE rips_000001.Empresa='".$emp."'";
		$q = $q."    AND rips_000001.Profesional='".$medico."'";
		$q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		$q = $q."    AND '".$wfechaf."'";
		$q = $q."    AND rips_000001.Nro_Factura between '".$Fact_ini."'";
		$q = $q."    AND '".$Fact_fin."'";
		$q = $q."    AND mid(rips_000001.Empresa,1,locate('-',rips_000001.Empresa)-1) = rips_000002.Codigo ";
		$q = $q."    AND mid(rips_000001.Documento,1,locate('-',rips_000001.Documento)-1) = rips_000004.Documento ";
		$q = $q."    AND rips_000002.Seguridad like '%".$usuario."%'"; 
		$q = $q."    AND mid(rips_000001.Seguridad,locate('-',rips_000001.Seguridad)+1,length(rips_000001.Seguridad)) like '%".$usuario."%'";
		$q = $q."  GROUP BY tipid, docu, COdigo_ms, tipusu, apellido1, apellido2, Nombre1, Nombre2, edad, unimed, sexo, dep, ciu, zona ";
			
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		$canus = $num;
			
		
	 if ($canus > 0)   //Si hay usuarios entro a generar los rips 
		{							
		if($num>0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);   //Numero de campos
									
			//$ruta="./matrix/planos/".$grupo."/".$usuario."/";
			chdir ("../..");
			$ruta="./planos/".$grupo."/".$usuario."/";
			$dh=opendir($ruta);
			if(readdir($dh) == false)
			  { 
			//echo $ruta;	  
			   
		       mkdir($ruta,0777);
			  }
								
			//Hago este for porque en la anterior vez que se generaron Rips se pudo haber generado algun archivo que en esta vez no se genero
			//por lo que el archivo anterior se debe borrar con cualquier consecutivo aneterior.
			for ($j=1; $j <= ($conse-1); $j++)                  // Conse-1: Consecutivo anterior generado
			   {
			    if (file_exists($ruta."US".$j.".txt"))
			       unlink ($ruta."US".$j.".txt");               // Borro todos los archivos US anteriores
		       }
			$file=fopen ($ruta."US".$conse.".txt","w+");
		    					
			echo "<center><table border=1>";
			echo "<tr><td align=center colspan=70 bgcolor=#FFFFCC><font size=4 text color=#330000><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		    echo "<tr><td align=center colspan=70 bgcolor=#FFFFCC><font size=4 text color=#330000><b>TORRE MEDICA LAS AMERICAS </b></font></td></tr>";
			echo "<tr><td align=center colspan=70 bgcolor=#FFFFCC><font color=#330000 size=4><b>APLICACION DE RIPS</b></font></td></tr>";
			echo "<tr><td align=center colspan=70 bgcolor=#FFFFCC><font color=#330000 size=4><b>GENERACION DE MOVIMIENTO RIPS</b></font></td></tr>";
			echo "<tr><td align=center colspan=70 bgcolor=#FFFFCC><font color=#330000 size=2><b>".$wactualiz."</b></font></td></tr>";
			
			echo "<tr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</tr>"; 
			
			//===================================================================================================================//	 
			echo "<tr><td align=center colspan=70 bgcolor=#330099><font color=#FFFFFF size=3><br><b>ARCHIVO US</b></br></font></td></tr>";       
			//===================================================================================================================//	        
			for ($j=1; $j <= $num; $j++)
			 {
			  $row = mysql_fetch_array($err);
			  
			  echo "<tr>";  
			  for ($i=0; $i < ($fields-1); $i++)
			     {
				  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>"; //4
				       
				  fwrite ($file,$row[$i].",");
			     }
			  
			  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>"; //4      
			  fwrite ($file,$row[$i].chr(13).chr(10));   //Imprimo el call return por cada linea
			  //if ($j < $num)    
			  //   fwrite ($file,chr(13).chr(10));   
              echo "</tr>";
             }
             fclose ($file);
             
             //echo "</table>";   
        } 
        
        
        /*====================================================================================================================*/
		/*================================  A C A   C O M I E N Z A   E L   A R C H I V O   A C  =============================*/
		/*====================================================================================================================*/
		$q = "    SELECT mid(rips_000001.Nro_factura,1,20), ";                                            // Numero factura
		$q = $q."        mid(root_000017.Codigo_dls,1,20), ";                                             // Codigo del prestador de servicios
		$q = $q."        mid(mid(rips_000001.Tipo_de_identificacion,1,locate('-',rips_000001.Tipo_de_identificacion)-1),1,2) AS tipid, "; //Tipo Identificacion                                                               // Codigo entidad administradora
		$q = $q."        mid(rips_000004.Documento,1,20), ";                                              // Documento del usuario
		$q = $q."        date_format(rips_000001.Fecha_servicio,'%d/%m/%Y'), ";                              // Fecha del servicio
		$q = $q."        mid(rips_000001.Nro_autorizacion,1,15), ";                                       // Numero de autorizacion
		$q = $q."        mid(mid(rips_000001.Codigo_atencion,1,locate('-',rips_000001.Codigo_atencion)-1),1,8) AS codate, "; // Codigo Atencion                                                       // Segundo Nombre
		$q = $q."        '10' AS finalidad, ";                                                            // Finalidad de la consulta
		$q = $q."        '13' AS causaext, ";                                                             // Causa externa
		$q = $q."        mid(mid(rips_000001.Diagnostico,1,locate('-',rips_000001.Diagnostico)-1),1,4) AS diapal, "; // Diagnostico Ppal
		$q = $q."        ' ' AS diag1, ";                                                                  // Diagnostico 1
		$q = $q."        ' ' AS diag2, ";                                                                  // Diagnostico 2
		$q = $q."        ' ' AS diag3, ";                                                                  // Diagnostico 3
		$q = $q."        '1' AS Tipdia, ";                                                                // Tipo de diagnostico Ppal
		$q = $q."        rips_000001.Valor_atencion, ";                                                   // Valor atencion
		$q = $q."        0 AS cuomod, ";                                                                  // Cuota moderadora
		$q = $q."        rips_000001.Valor_atencion ";                                                    // Valor Neto Atencion
		$q = $q."   FROM rips_000001, root_000017, rips_000004, rips_000002";
		$q = $q."  WHERE rips_000001.Empresa='".$emp."'";
		$q = $q."    AND rips_000001.Profesional='".$medico."'";
		$q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		$q = $q."    AND '".$wfechaf."'";
		$q = $q."    AND rips_000001.Nro_Factura between '".$Fact_ini."'";
		$q = $q."    AND '".$Fact_fin."'";
		$q = $q."    AND mid(rips_000001.Empresa,1,locate('-',rips_000001.Empresa)-1) = rips_000002.Codigo ";
		$q = $q."    AND mid(rips_000001.Documento,1,locate('-',rips_000001.Documento)-1) = rips_000004.Documento ";
		$q = $q."    AND mid(rips_000001.Tipo_de_atencion,1,locate('-',rips_000001.Tipo_de_atencion)-1) = '01' ";         //Consultas
		$q = $q."    AND rips_000002.Seguridad like '%".$usuario."%'"; 
		$q = $q."    AND root_000017.Documento = '".$wmedico."'";
		$q = $q."    AND mid(rips_000001.Seguridad,locate('-',rips_000001.Seguridad)+1,length(rips_000001.Seguridad)) like '%".$usuario."%'";
		//echo $q;				
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		$canac = $num;
				
		if($num>0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
     					
			//Si no existe la carpeta del usuario dentro del grupo la creo		
			$ruta="./planos/".$grupo."/".$usuario."/";
			$dh=opendir($ruta);
			if(readdir($dh) == false)
			  { 
		       mkdir($ruta,0777);
			  }
			  
			for ($j=1; $j <= ($conse-1); $j++)                  // Conse-1: Consecutivo anterior generado
			   {
			    if (file_exists($ruta."AC".$j.".txt"))
			       unlink ($ruta."AC".$j.".txt");               // Borro todos los archivos AC anteriores
		       }
			$file=fopen ($ruta."AC".$conse.".txt","w+");
						
			echo "<tr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</tr>"; 
			
			//===================================================================================================================//	 
			echo "<tr><td align=center colspan=70 bgcolor=#330099><font color=#FFFFFF size=3><br><b>ARCHIVO AC</b></br></font></td></tr>";       
			//===================================================================================================================//	        
			for ($j=1; $j <= $num; $j++)
			 {
			  $row = mysql_fetch_array($err);
			  
			  echo "<tr>";  
			  for ($i=0; $i < ($fields-1); $i++)
			     {
				  if ($i == 4)    //Este campo campo corresponde a la fecha del servicio, se reeemplaza el guion por el slash
				     $row[$i] = str_replace("-","/",$row[$i]); 
				  
				  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else         
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>"; 
				  fwrite ($file,$row[$i].",");
			     }
			   if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				 else         
				     echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";    
				     
			  fwrite ($file,$row[$i].chr(13).chr(10));   //Imprimo el call return por cada linea
			  //if ($j < $num)    
			  //   fwrite ($file,chr(13).chr(10));   
              echo "</tr>";
             }
             //echo "</table>";   
             fclose ($file);
             
        }
        
        /*====================================================================================================================*/
		/*================================  A C A   C O M I E N Z A   E L   A R C H I V O   A P  =============================*/
		/*====================================================================================================================*/
		$q = "    SELECT mid(rips_000001.Nro_factura,1,20), ";                                            // Numero factura
		$q = $q."        mid(root_000017.Codigo_dls,1,10), ";                                             // Codigo del prestador de servicios
		$q = $q."        mid(mid(rips_000001.Tipo_de_identificacion,1,locate('-',rips_000001.Tipo_de_identificacion)-1),1,2) AS tipid, "; //Tipo Identificacion                                                               // Codigo entidad administradora
		$q = $q."        mid(rips_000004.Documento,1,20), ";                                              // Documento del usuario
		$q = $q."        date_format(rips_000001.Fecha_servicio,'%d/%m/%Y'), ";                              // Fecha del servicio
		$q = $q."        mid(rips_000001.Nro_autorizacion,1,15), ";                                       // Numero de autorizacion
		$q = $q."        mid(mid(rips_000001.Codigo_atencion,1,locate('-',rips_000001.Codigo_atencion)-1),1,8) AS codate, "; // Codigo Atencion                                                       // Segundo Nombre
		$q = $q."        '1' AS ambiento, ";                                                              // Ambito de realizacion
		$q = $q."        '1' AS finalidad, ";                                                             // Finalidad del procedimiento
		$q = $q."        '1' AS perate, ";                                                                // Personal que atiende
		$q = $q."        mid(mid(rips_000001.Diagnostico,1,locate('-',rips_000001.Diagnostico)-1),1,4) AS diapal, ";  // Diagnostico Principal
		$q = $q."        '' AS diarel, ";                                                                 // Diagnostico Relacionado
		$q = $q."        '' AS diacom, ";                                                                 // Diagnostico Complicacion
		$q = $q."        '1' AS forma, ";                                                                 // Forma realizacion acto qx
		$q = $q."        rips_000001.Valor_atencion ";                                                    // Valor atencion
		$q = $q."   FROM rips_000001, root_000017, rips_000004, rips_000002";
		$q = $q."  WHERE rips_000001.Empresa='".$emp."'";
		$q = $q."    AND rips_000001.Profesional='".$medico."'";
		$q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		$q = $q."    AND '".$wfechaf."'";
		$q = $q."    AND rips_000001.Nro_Factura between '".$Fact_ini."'";
		$q = $q."    AND '".$Fact_fin."'";
		$q = $q."    AND mid(rips_000001.Empresa,1,locate('-',rips_000001.Empresa)-1) = rips_000002.Codigo ";
		$q = $q."    AND mid(rips_000001.Documento,1,locate('-',rips_000001.Documento)-1) = rips_000004.Documento ";
		$q = $q."    AND mid(rips_000001.Tipo_de_atencion,1,locate('-',rips_000001.Tipo_de_atencion)-1) = '02' ";         //Procedimientos
		$q = $q."    AND rips_000002.Seguridad like '%".$usuario."%'";  
		$q = $q."    AND root_000017.documento = '".$wmedico."'";
		$q = $q."    AND mid(rips_000001.Seguridad,locate('-',rips_000001.Seguridad)+1,length(rips_000001.Seguridad)) like '%".$usuario."%'";
						
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		$canap = $num;
											
		if($num>0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
			
			//Si no existe la carpeta del usuario dentro del grupo la creo		
			$ruta="./planos/".strtolower($grupo)."/".$usuario."/";
			$dh=opendir($ruta);
			if(readdir($dh) == false)
			  { 
		       mkdir($ruta,0777);
			  }
						
			for ($j=1; $j <= ($conse-1); $j++)                  // Conse-1: Consecutivo anterior generado
			   {
			    if (file_exists($ruta."AP".$j.".txt"))
			       unlink ($ruta."AP".$j.".txt");               // Borro todos los archivos AP anteriores
		       }
			$file=fopen ($ruta."AP".$conse.".txt","w+");
     							
			echo "<tr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</tr>"; 
			
			//===================================================================================================================//	 
			echo "<tr><td align=center colspan=70 bgcolor=#330099><font color=#FFFFFF size=3><br><b>ARCHIVO AP</b></br></font></td></tr>";       
			//===================================================================================================================//	        
			for ($j=1; $j <= $num; $j++)
			 {
			  $row = mysql_fetch_array($err);
			  
			  /*echo $fields."<br>";
			  echo $row[14]."<br>";*/
			  
			  echo "<tr>";  
			  for ($i=0; $i < ($fields-1); $i++)
			     {
				  if ($i == 4)    //Este campo campo corresponde a la fecha del servicio, se reeemplaza el guion por el slash
				     $row[$i] = str_replace("-","/",$row[$i]); 
				      
				  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";  // 4
				      
				  //echo "i: ".$i." Valor: ".$row[$i]."br";  
				   
				 
				  fwrite ($file,$row[$i].",");
			     }
			  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";  // 4
				       
			  fwrite ($file,$row[$i].chr(13).chr(10));   //Imprimo el call return por cada linea
			  //if ($j < $num)    
			  //   fwrite ($file,chr(13).chr(10));   
              echo "</tr>";
             }
             //echo "</table>";  
             fclose ($file);
             
        }
        
        /*====================================================================================================================*/
		/*================================  A C A   C O M I E N Z A   E L   A R C H I V O   A T  =============================*/
		/*====================================================================================================================*/
		$q = "    SELECT mid(rips_000001.Nro_factura,1,20), ";                                            // Numero factura
		$q = $q."        mid(root_000017.Codigo_dls,1,10), ";                                             // Codigo del prestador de servicios
		$q = $q."        mid(mid(rips_000001.Tipo_de_identificacion,1,locate('-',rips_000001.Tipo_de_identificacion)-1),1,2) AS tipid, "; //Tipo Identificacion                                               
		$q = $q."        mid(rips_000004.Documento,1,20), ";                                              // Documento del usuario
		$q = $q."        mid(rips_000001.Nro_autorizacion,1,15), ";                                       // Numero de autorizacion  
		$q = $q."        '4' AS tipser, ";                                                                // Tipo de servicio 4: Honorarios
		$q = $q."        mid(mid(rips_000001.Codigo_atencion,1,locate('-',rips_000001.Codigo_atencion)-1),1,20) AS codate, "; // Codigo Atencion                                         
		$q = $q."        mid(mid(rips_000001.Codigo_atencion,locate('-',rips_000001.Codigo_atencion)+1,length(rips_000001.Codigo_atencion)),1,60) AS codate, "; // Nombre Atencion                                         
		$q = $q."        '1' AS cantidad, ";                                                              // Cantidad del servicio
		$q = $q."        rips_000001.Valor_atencion, ";                                                   // Valor unitario del servicio
		$q = $q."        rips_000001.Valor_atencion AS valtot ";                                          // Valor total del servicio
		$q = $q."   FROM rips_000001, root_000017, rips_000004, rips_000002";
		$q = $q."  WHERE rips_000001.Empresa='".$emp."'";
		$q = $q."    AND rips_000001.Profesional='".$medico."'";
		$q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		$q = $q."    AND '".$wfechaf."'";
		$q = $q."    AND rips_000001.Nro_Factura between '".$Fact_ini."'";
		$q = $q."    AND '".$Fact_fin."'";
		$q = $q."    AND mid(rips_000001.Empresa,1,locate('-',rips_000001.Empresa)-1) = rips_000002.Codigo ";
		$q = $q."    AND mid(rips_000001.Documento,1,locate('-',rips_000001.Documento)-1) = rips_000004.Documento ";
		$q = $q."    AND mid(rips_000001.Tipo_de_atencion,1,locate('-',rips_000001.Tipo_de_atencion)-1) = '07' ";     //Honorarios
		$q = $q."    AND rips_000002.Seguridad like '%".$usuario."%'";  
		$q = $q."    AND root_000017.documento = '".$wmedico."'";
		$q = $q."    AND mid(rips_000001.Seguridad,locate('-',rips_000001.Seguridad)+1,length(rips_000001.Seguridad)) like '%".$usuario."%'";
							
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		$canat = $num;
			
		if($num>0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
			
			//Si no existe la carpeta del usuario dentro del grupo la creo		
			$ruta="./planos/".$grupo."/".$usuario."/";
			$dh=opendir($ruta);
			if(readdir($dh) == false)
			  { 
		       mkdir($ruta,0777);
			  }
			  
			for ($j=1; $j <= ($conse-1); $j++)                  // Conse-1: Consecutivo anterior generado
			   {
			    if (file_exists($ruta."AT".$j.".txt"))
			       unlink ($ruta."AT".$j.".txt");               // Borro todos los archivos US anteriores
		       }
			$file=fopen ($ruta."AT".$conse.".txt","w+");  // Creo el nuevo archivo
			
			echo "<tr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</tr>"; 
			
			//===================================================================================================================//	 
			echo "<tr><td align=center colspan=70 bgcolor=#330099><font color=#FFFFFF size=3><br><b>ARCHIVO AT</b></br></font></td></tr>";       
			//===================================================================================================================//	        
			for ($j=1; $j <= $num; $j++)
			 {
			  $row = mysql_fetch_array($err);
			  
			  echo "<tr>";  
			  for ($i=0; $i < ($fields-1); $i++)
			     {
				  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";  // 4
				  fwrite ($file,$row[$i].",");
			     }
			     
			  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";  // 4   
			  fwrite ($file,$row[$i].chr(13).chr(10));   //Imprimo el call return por cada linea
			  //if ($j < $num)    
			  //   fwrite ($file,chr(13).chr(10));   
              echo "</tr>";
             }
             //echo "</table>";   
             fclose ($file);
        }
               
        /*====================================================================================================================*/
		/*================================  A C A   C O M I E N Z A   E L   A R C H I V O   A D  =============================*/
		/*====================================================================================================================*/
		$q = "    SELECT mid(rips_000001.Nro_factura,1,20), ";                                       // Numero factura
		$q = $q."        mid(root_000017.Codigo_dls,1,10), ";                                        // Codigo del prestador de servicios
		$q = $q."        mid(mid(rips_000001.Tipo_de_atencion,1,locate('-',rips_000001.Tipo_de_atencion)-1),1,2) AS tipate, "; //Concepto de cobro                                                               // Codigo entidad administradora
		$q = $q."        count(*) AS cant, ";                                                        // Cantidad por concepto
		$q = $q."        sum(rips_000001.valor_atencion)/count(*) AS valuni, ";                      // Valor unitario
		$q = $q."        sum(rips_000001.valor_atencion) AS valtot ";                                // Valor total
		$q = $q."   FROM rips_000001, root_000017 ";
		$q = $q."  WHERE rips_000001.Empresa='".$emp."'";
		$q = $q."    AND rips_000001.Profesional='".$medico."'";
		$q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		$q = $q."    AND '".$wfechaf."'";
		$q = $q."    AND rips_000001.Nro_Factura between '".$Fact_ini."'";
		$q = $q."    AND '".$Fact_fin."'";
		//$q = $q."    AND rips_000002.Seguridad like '%".$usuario."%'";  
		$q = $q."    AND root_000017.documento = '".$wmedico."'";
		$q = $q."    AND mid(rips_000001.Seguridad,locate('-',rips_000001.Seguridad)+1,length(rips_000001.Seguridad)) like '%".$usuario."%'";
		$q = $q."  GROUP BY rips_000001.Nro_factura, root_000017.Codigo_dls, tipate ";
							
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		
		$canad = $num;
								
		if($num>0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
			
			//Si no existe la carpeta del usuario dentro del grupo la creo		
			$ruta="./planos/".$grupo."/".$usuario."/";
			$dh=opendir($ruta);
			if(readdir($dh) == false)
			  { 
		       mkdir($ruta,0777);
			  }
			for ($j=1; $j <= ($conse-1); $j++)                  // Conse-1: Consecutivo anterior generado
			   {
			    if (file_exists($ruta."AD".$j.".txt"))
			       unlink ($ruta."AD".$j.".txt");               // Borro todos los archivos AD anteriores
		       }
			$file=fopen ($ruta."AD".$conse.".txt","w+");
     				  
			echo "<tr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</tr>"; 
						
			//===================================================================================================================//	 
			echo "<tr><td align=center colspan=70 bgcolor=#330099><font color=#FFFFFF size=3><br><b>ARCHIVO AD</b></br></font></td></tr>";       
			//===================================================================================================================//	        
			for ($j=1; $j <= $num; $j++)
			 {
			  $row = mysql_fetch_array($err);
			  
			  echo "<tr>";  
			  for ($i=0; $i < ($fields-1); $i++)
			     {
				  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";  // 4
				  fwrite ($file,$row[$i].",");
			     }
			     
			     if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";  // 4
			  fwrite ($file,$row[$i].chr(13).chr(10));   //Imprimo el call return por cada linea
			  //if ($j < $num)    
			  //   fwrite ($file,chr(13).chr(10));   
              echo "</tr>";
             }
             //echo "</table>";  
             fclose ($file);
             
        }
        
        $wanof=substr($wfechaf,0,4);
        $wmesf=substr($wfechaf,5,2);
        $wdiaf=substr($wfechaf,8,2);
        $wfecf=$wdiaf."/".$wmesf."/".$wanof;
        
        $wanoi=substr($wfechai,0,4);
        $wmesi=substr($wfechai,5,2);
        $wdiai=substr($wfechai,8,2);
        $wfeci=$wdiai."/".$wmesi."/".$wanoi;
        
        
        /*====================================================================================================================*/
		/*================================  A C A   C O M I E N Z A   E L   A R C H I V O   A F  =============================*/
		/*====================================================================================================================*/
		$q = "    SELECT mid(root_000017.Codigo_dls,1,10), ";                                        // Codigo del prestador de servicios
		$q = $q."        mid(root_000017.Nombre,1,60), ";                                            // Nombre o razon social del prest. de servicios
		$q = $q."        mid(root_000017.Tipo_documento,1,2), ";                                     // Tipo documento del prestador de servicios
		$q = $q."        mid(root_000017.Documento,1,20), ";                                         // Documento del prestador de servicios
		$q = $q."        mid(rips_000001.Nro_factura,1,20), ";                                       // Numero factura
		$q = $q."        '".$wfecf."', ";                                                          // Fecha expedicion factura
		$q = $q."        '".$wfeci."', ";                                                          // Fecha inicial
		$q = $q."        '".$wfecf."', ";                                                          // Fecha final
		$q = $q."        mid(rips_000002.Codigo_ms,1,6), ";                                          // Codigo entidad administradora
		$q = $q."        mid(rips_000002.Nombre,1,30), ";                                            // Nombre entidad administradora
		$q = $q."        ' ' as contrato, ";  														 // Numero de contrato
		$q = $q."        ' ' as plan, ";  														     // Plan de beneficios
    	$q = $q."        ' ' as poliza, ";  														     // Numero de poliza
    	$q = $q."        '0' as copago, ";  														 // Copago
    	$q = $q."        '0' as comision, ";  														 // Comision
    	$q = $q."        '0' as descuentos, ";  													 // Descuentos
    	$q = $q."        sum(Valor_atencion) AS valtot ";  									    	 // Valor total a pagar factura
		$q = $q."   FROM rips_000001, root_000017, rips_000002 ";
		$q = $q."  WHERE rips_000001.Empresa='".$emp."'";
		$q = $q."    AND rips_000001.Profesional='".$medico."'";
		$q = $q."    AND rips_000001.Fecha_servicio between '".$wfechai."'";
		$q = $q."    AND '".$wfechaf."'";
		$q = $q."    AND rips_000001.Nro_Factura between '".$Fact_ini."'";
		$q = $q."    AND '".$Fact_fin."'";
		$q = $q."    AND mid(rips_000001.Empresa,1,locate('-',rips_000001.Empresa)-1) = rips_000002.Codigo ";
		$q = $q."    AND rips_000002.Seguridad like '%".$usuario."%'";  
		$q = $q."    AND root_000017.documento = '".$wmedico."'";
		$q = $q."    AND mid(rips_000001.Seguridad,locate('-',rips_000001.Seguridad)+1,length(rips_000001.Seguridad)) like '%".$usuario."%'";
		$q = $q."  GROUP BY root_000017.Codigo_dls, root_000017.Nombre, root_000017.Tipo_documento, root_000017.Documento, rips_000001.Nro_factura, ";
		$q = $q."           rips_000002.Codigo_ms, rips_000002.Nombre, contrato, plan, plan, copago, comision, descuentos ";
							
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		
		$canaf = $num;
								
		if($num>0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
			
			//Si no existe la carpeta del usuario dentro del grupo la creo		
			$ruta="./planos/".$grupo."/".$usuario."/";
			$dh=opendir($ruta);
			if(readdir($dh) == false)
			  { 
		       mkdir($ruta,0777);
			  }
			  
			for ($j=1; $j <= ($conse-1); $j++)                  // Conse-1: Consecutivo anterior generado
			   {
			    if (file_exists($ruta."AF".$j.".txt"))
			       unlink ($ruta."AF".$j.".txt");               // Borro todos los archivos AF anteriores
		       }
			$file=fopen ($ruta."AF".$conse.".txt","w+");
     				  
			echo "<tr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</tr>"; 
						
			//===================================================================================================================//	 
			echo "<tr><td align=center colspan=70 bgcolor=#330099><font color=#FFFFFF size=3><br><b>ARCHIVO AF</b></br></font></td></tr>";       
			//===================================================================================================================//	        
			for ($j=1; $j <= $num; $j++)
			 {
			  $row = mysql_fetch_array($err);
			  
			  echo "<tr>";  
			  for ($i=0; $i < ($fields-1); $i++)
			     {
				  if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";   // 4
				  fwrite ($file,$row[$i].",");
			     }
			   if ($row[$i] == "" or $row[$i] == "NO APLICA")
				     echo "<td colspan=4 bgcolor=#CC3300>".$row[$i]."Error</td>";  
				    else   
				       echo "<td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";   // 4
				         
			  fwrite ($file,$row[$i].chr(13).chr(10));   //Imprimo el call return por cada linea
			  //if ($j < $num)    
			  //   fwrite ($file,chr(13).chr(10));   
              echo "</tr>";
             }
             //echo "</table>";  
             fclose ($file);
             
        }
        
        
        /*====================================================================================================================*/
		/*================================  A C A   C O M I E N Z A   E L   A R C H I V O   C T  =============================*/
		/*====================================================================================================================*/
		$q = "    SELECT Codigo_dls ";                                                  // Codigo del prestador de servicios
		$q = $q."   FROM root_000017 ";
		$q = $q."  WHERE Documento = '".$wmedico."'";      // Cedula del medico
										
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		$canct = 0;
											
		if($num>0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
     					
			//Si no existe la carpeta del usuario dentro del grupo la creo		
			$ruta="./planos/".$grupo."/".$usuario."/";
			$dh=opendir($ruta);
			if(readdir($dh) == false)
			  { 
		       mkdir($ruta,0777);
			  }
			  
			for ($j=1; $j <= ($conse-1); $j++)                  // Conse-1: Consecutivo anterior generado
			   {
			    if (file_exists($ruta."CT".$j.".txt"))
			       unlink ($ruta."CT".$j.".txt");               // Borro todos los archivos US anteriores
		       }
			$file=fopen ($ruta."CT".$conse.".txt","w+");
			
			echo "<tr>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</tr>"; 
						
			//===================================================================================================================//	 
			echo "<tr><td align=center colspan=70 bgcolor=#330099><font color=#FFFFFF size=3><br><b>ARCHIVO CT</b></br></font></td></tr>";       
			//===================================================================================================================//	        
			for ($j=1; $j <= $num; $j++)
			 {
			  $row = mysql_fetch_array($err);
			  
			  $sw = "0";  //Para saber si es la primera linea que se crea en el archivo CT
			  echo "<tr>";  
			  for ($i=0; $i <= ($fields-1); $i++)
			     {
				  				  
				  if ($canaf > 0)
				     { 
					   if ($sw == "1")
					      fwrite ($file,chr(13).chr(10)); 
					      
					   echo "<tr><td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";          // Codigo del prestador de servicios
				       fwrite ($file,$row[$i].",");                                         // Codigo del prestador de servicios
				       echo "<td colspan=4 bgcolor=#00FFFF>".$wfecf."</td>";                // Fecha de remision 
				       fwrite ($file,$wfecf.",");                                           // Fecha de remision
				       fwrite ($file,"AF".$conse.",");                                                // Nombre del archivo
				       //fwrite ($file,$canaf.",".chr(13).chr(10));                         // Total registros
				       fwrite ($file,$canaf);                                           // Total registros
					   
					   echo "<td colspan=4 bgcolor=#00FFFF>AF</td>";                        // Codigo del archivo AF
				       echo "<td colspan=4 bgcolor=#00FFFF>".$canaf."</td></tr>";           // Total registros AF
				       $canct = $canct + 1;
				       $sw="1";
				     }
				  	
			      if ($canad > 0)
				     { 
					   if ($sw == "1")
					      fwrite ($file,chr(13).chr(10));  
					   echo "<tr><td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";          // Codigo del prestador de servicios
				       fwrite ($file,$row[$i].",");                                         // Codigo del prestador de servicios
				       echo "<td colspan=4 bgcolor=#00FFFF>".$wfecf."</td>";                // Fecha de remision 
				       fwrite ($file,$wfecf.",");                                           // Fecha de remision
				       fwrite ($file,"AD".$conse.",");                                      // Nombre del archivo
				       //fwrite ($file,$canad.",".chr(13).chr(10));                         // Total registros
				       fwrite ($file,$canad);                                               // Total registros
					     
					   echo "<td colspan=4 bgcolor=#00FFFF>AD</td>";                        // Codigo del archivo AD
				       echo "<td colspan=4 bgcolor=#00FFFF>".$canad."</td></tr>";           // Total registros AD
				       $canct = $canct + 1;
				       $sw="1";
			         }
			         
			      if ($canus > 0)
				     { 
					   if ($sw == "1")
					      fwrite ($file,chr(13).chr(10));  
					   echo "<tr><td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";          // Codigo del prestador de servicios
				       fwrite ($file,$row[$i].",");                                         // Codigo del prestador de servicios
				       echo "<td colspan=4 bgcolor=#00FFFF>".$wfecf."</td>";              // Fecha de remision 
				       fwrite ($file,$wfecf.",");                                         // Fecha de remision
				       fwrite ($file,"US".$conse.",");                                      // Nombre del archivo
				       //fwrite ($file,$canus.",".chr(13).chr(10));                           // Total registros
				       fwrite ($file,$canus);                                           // Total registros
					     
					   echo "<td colspan=4 bgcolor=#00FFFF>US</td>";                        // Codigo del archivo US
				       echo "<td colspan=4 bgcolor=#00FFFF>".$canus."</td></tr>";           // Total registros US
				       $canct = $canct + 1;
				       $sw="1";
			         } 
				  
			      if ($canac > 0)
				     { 
					   if ($sw == "1")
					      fwrite ($file,chr(13).chr(10));  
					   echo "<tr><td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";          // Codigo del prestador de servicios
				       fwrite ($file,$row[$i].",");                                         // Codigo del prestador de servicios
				       echo "<td colspan=4 bgcolor=#00FFFF>".$wfecf."</td>";                // Fecha de remision 
				       fwrite ($file,$wfecf.",");                                           // Fecha de remision
				       fwrite ($file,"AC".$conse.",");                                      // Nombre del archivo
				       //fwrite ($file,$canac.",".chr(13).chr(10));                         // Total registros
				       fwrite ($file,$canac);                                               // Total registros
					     
					   echo "<td colspan=4 bgcolor=#00FFFF>AC</td>";                        // Codigo del archivo AC
				       echo "<td colspan=4 bgcolor=#00FFFF>".$canac."</td></tr>";           // Total registros AC
				       $canct = $canct + 1;
				       $sw="1";
			         } 
				  
			      if ($canap > 0)
				     { 
					   if ($sw == "1")
					      fwrite ($file,chr(13).chr(10));  
					   echo "<tr><td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";          // Codigo del prestador de servicios
				       fwrite ($file,$row[$i].",");                                         // Codigo del prestador de servicios
				       echo "<td colspan=4 bgcolor=#00FFFF>".$wfecf."</td>";              // Fecha de remision 
				       fwrite ($file,$wfecf.",");                                         // Fecha de remision
				       fwrite ($file,"AP".$conse.",");                                      // Nombre del archivo
				       //fwrite ($file,$canap.",".chr(13).chr(10));                           // Total registros
				       fwrite ($file,$canap);                                           // Total registros
					     
					   echo "<td colspan=4 bgcolor=#00FFFF>AP</td>";                        // Codigo del archivo AP
		     		   echo "<td colspan=4 bgcolor=#00FFFF>".$canap."</td></tr>";           // Total registros AP
		     		   $canct = $canct + 1;
		     		   $sw="1";
			         } 
				  
			      if ($canat > 0)
				     { 
					   if ($sw == "1")
					      fwrite ($file,chr(13).chr(10));  
					   echo "<tr><td colspan=4 bgcolor=#00FFFF>".$row[$i]."</td>";          // Codigo del prestador de servicios
				       fwrite ($file,$row[$i].",");                                         // Codigo del prestador de servicios
				       echo "<td colspan=4 bgcolor=#00FFFF>".$wfecf."</td>";              // Fecha de remision 
				       fwrite ($file,$wfecf.",");                                         // Fecha de remision
				       fwrite ($file,"AT".$conse.",");                                      // Nombre del archivo
				       //fwrite ($file,$canat.",".chr(13).chr(10));                           // Total registros
				       fwrite ($file,$canat);                                           // Total registros
					     
					   echo "<td colspan=4 bgcolor=#00FFFF>AT</td>";                        // Codigo del archivo AT
	     			   echo "<td colspan=4 bgcolor=#00FFFF>".$canat."</td></tr>";           // Total registros AT
	     			   $canct = $canct + 1;
	     			   $sw="1";
			         } 
				 }
		      //fwrite ($file,chr(13).chr(10));
              echo "</tr>";
             }
             echo "</table>";   
             fclose ($file);
                
             			     
			 //=====================================================================================================================    
			 //==== INCREMENTO EL CONSECUTIVO DE LOS ARCHIVOS RIPS =================================================================
			 //=====================================================================================================================
			 $q = "    UPDATE root_000017 ";                   // Consecutivo para los rips
		     $q = $q."    SET consecutivo = consecutivo + 1 ";
		     $q = $q."  WHERE Documento = '".$wmedico."'";
		     $err2 = mysql_query($q,$conex);
             
        }
        
        $ruta="/matrix/planos/".$grupo."/".$usuario."/";
        echo "<tr><td colspan=4 align=center><font size=5><b>ARCHIVOS GENERADOS CON EL CONSECUTIVO : ".$conse."</b></font></td></tr><br>";
   		echo "<tr><td colspan=4 align=center><font size=5><b><A href=".$ruta.">Haga Click Para Bajar los Archivos</A></b></font></td></tr>";
   	} 	// then del If de $canus	
    else //else del if de $canus
       {
	    echo "<br>";
		echo "<br>";
		echo "<br>";
		echo "<br>"; 
        echo "<center><font size=5 text color=#CC0000><b>!! NO HAY REGISTROS EN EL RANGO DE FECHAS PARA EL MEDICO Y EMPRESA SELECCIONADOS ¡¡</b></font></center><br>"; 	
       }
   	
   		
    }  // else del if de si esta setiado el $medico y la $emp
} // if de register
include_once("free.php");
?>
