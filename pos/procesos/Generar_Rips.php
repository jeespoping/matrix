<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  

  

  echo "<form action='Generar_Rips.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  $usuario = substr($user,strpos("-",$user)+2,strlen($user));
  
  $query = "select codigo,prioridad,grupo from usuarios where codigo='".$usuario."' and activo = 'A'";
  $err = mysql_query($query,$conex);
  $num = mysql_num_rows($err);
  $row = mysql_fetch_array($err);
  $prioridad=$row[1];
  $usuario=$row[0];
  $grupo=$row[2];
  
    
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  echo "<center><table border=2>";
  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>GENERACION DE RIPS</b></font></td></tr>";
  
  if (!isset($wfac_ini) or !isset($wfac_fin) or !isset($wcco) or !isset($wemp)) 
   {
	echo "<tr>";  
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Factura Inicial (XX-####): </font></b><INPUT TYPE='text' NAME='wfac_ini' SIZE=10></td>";
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Factura Final (XX-####): </font></b><INPUT TYPE='text' NAME='wfac_fin' SIZE=10></td>";
    echo "</tr>";
    
    //CENTRO DE COSTO
    $q =  " SELECT ccocod, ccodes "
		 ."   FROM ".$wbasedato."_000003 "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	 
	  
	echo "<tr><td align=center bgcolor=".$wcf." colspan=1>SELECCIONE LA SUCURSAL: ";
	echo "<select name='wcco'>";
	//echo "<option>&nbsp</option>";    
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td>";
    
    
	
    //SELECCIONAR EMPRESA
    $q =  " SELECT empcod, empnit, empnom "
		 ."   FROM ".$wbasedato."_000024 "
		 ."  WHERE empest = 'on' "
		 ."    AND emprip = 'on' "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	    
	echo "<td align=center bgcolor=".$wcf." colspan=1>SELECCIONE LA EMPRESA: ";
	echo "<select name='wemp'>";
	//echo "<option>&nbsp</option>";    
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."-".$row[2]."</option>";
       }
	echo "</select></td></tr>";
	
	
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	
    echo "<tr>";
    echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
    
   }
  else 
     {
	  echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	  $wccoe = explode("-",$wcco); 
	  
	  echo "<tr>";  
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Factura Inicial (XX-####): </font></b>".$wfac_ini."</td>";
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Factura Final (XX-####): </font></b>".$wfac_fin."</td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center colspan=1><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
      echo "<td bgcolor=".$wcf." align=center colspan=1><b><font text color=".$wclfg.">ENTIDAD: </font></b>".$wemp."</td>";
      echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>GENERACION DE RIPS</b></font></td></tr>";
      echo "</tr>";  
	  
      
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //================================= A C A   C O M I E N Z A   E L   A R C H I V O   ( A F ) =====================================//
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $q = "  SELECT cfgcpr, cfgnom, cfgtdo, cfgnit, cfgrip "
          ."    FROM ".$wbasedato."_000049 "
          ."   WHERE cfgcco = '".$wccoe[0]."'";
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
      $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
      
      if ($num > 0)
         {
          $row = mysql_fetch_array($err); 
          
          $conse=$row[4]+1;  //Consecutivo RIPS por C.C.
          
          //Actualizo 
          $q = "  UPDATE ".$wbasedato."_000049 "
              ."     SET cfgrip = cfgrip + 1 "
              ."   WHERE cfgcco = '".$wccoe[0]."'";
      	  $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
          
          $wcfgcpr=substr($row[0],0,10);
          $wcfgnom=substr($row[1],0,60);
          $wcfgtdo=substr($row[2],0,2);
          $wcfgnit=substr($row[3],0,20);
          
          
          chdir ("../..");
          $ruta="./planos/pos/".$usuario."/";
          
          //echo getcwd()."3 <br>";
		  $dh=opendir($ruta);
		  if(readdir($dh) == false)
		    { 
	         mkdir($ruta,0777);
		    }
		    
		  for ($j=1; $j <= ($conse); $j++)                    // Conse-1: Consecutivo anterior generado
			 {
			  if (file_exists($ruta."AF".$j.".txt"))
			     unlink ($ruta."AF".$j.".txt");               // Borro todos los archivos AF anteriores
		     }
		     
		  $file=fopen ($ruta."AF".$conse.".txt","w+");  
		 }
       
      $wemp1=explode("-",$wemp);    
         
      
      $q = "  SELECT min(venfec) "
          ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000016 "
          ."   WHERE fenfac BETWEEN '".$wfac_ini."' AND '".$wfac_fin."'"
          ."     AND fenfac = vennfa "
          ."     AND fenest = 'on' "
          ."     AND fencod = '".$wemp1[0]."'";
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
      $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
      
      if ($num > 0)
         {
          $row = mysql_fetch_array($err);
          
          $wfecmin=$row[0];
          $q = "  SELECT max(venfec) "
              ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000016 "
              ."   WHERE fenfac BETWEEN '".$wfac_ini."' AND '".$wfac_fin."'"
              ."     AND fenfac = vennfa "
              ."     AND fenest = 'on' "
              ."     AND fencod = '".$wemp1[0]."'";
          $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
          $row = mysql_fetch_array($err);
          $wfecmax=$row[0]; 
         }
      
      $q = "  SELECT fenfac, fenfec, empcmi, empnom, fencmo, fenval "
          ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000024 "   
          ."   WHERE fenfac BETWEEN '".$wfac_ini."' AND '".$wfac_fin."'"
          ."     AND fenest = 'on' "
          ."     AND fencod = empcod "
          ."     AND empcod = '".$wemp1[0]."'";
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
	  $wnumAF=$num;
	  
	  for ($i=1;$i<=$num;$i++)
	      {
		   $row = mysql_fetch_array($err);
		    
		   fwrite ($file,$wcfgcpr.",");               //Codigo prestador de servicios
		   fwrite ($file,$wcfgnom.",");               //Nombre prestador de servicios 
		   fwrite ($file,$wcfgtdo.",");               //Tipo de identificación del prestador
		   fwrite ($file,$wcfgnit.",");               //Nit o identificacion
		   fwrite ($file,substr($row[0],0,20).",");   //Factura
		   fwrite ($file,substr($row[1],0,20).",");   //Fecha factura
		   $wfecfac=$row[1];
		   fwrite ($file,$wfecmin.",");               //Fecha inicial de la facturación 
		   fwrite ($file,$wfecmax.",");               //Fecha máxima de la facturación
		   $wempcmi=$row[2];
		   fwrite ($file,$row[2].",");                //Código entidad administración
		   fwrite ($file,$row[3].",");                //Nombre entidad administradora
		   fwrite ($file,"".",");                     //Número de contrato
		   fwrite ($file,"".",");                     //Plan de beneficios 
		   
		   $q = "  SELECT rippol "
               ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000016, ".$wbasedato."_000063 "
               ."   WHERE fenfac = '".$row[0]."'"
               ."     AND fenfac = vennfa "
               ."     AND fenest = 'on' "
               ."     AND fencod = '".$wemp1[0]."'"
               ."     AND vennum = ripvta ";
           $err1 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
           $num1 = mysql_num_rows($err1) or die (mysql_errno()." - ".mysql_error());
           
           if ($num1 > 0)
              {
               $row1 = mysql_fetch_array($err1);
               $wpoliza=$row1[0];
              } 
             else 
                $wpoliza=""; 
           
           fwrite ($file,$wpoliza.",");               //Poliza    
           fwrite ($file,$row[4].",");                //Cuota moderadora
		   fwrite ($file,'0'.",");                    //Comisión
		   fwrite ($file,'0'.",");                    //Descuentos
		   fwrite ($file,$row[5]);                    //Valor neto a pagar 
		   
		   fwrite ($file,chr(13).chr(10));            //Imprimo el call return por cada linea
		  } 
	  fclose ($file);
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //================================== A C A   T E R M I N A   E L   A R C H I V O   ( A F ) ======================================//
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      
      
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //============================== A C A   ** C O M I E N Z A **   E L   A R C H I V O   ( U S ) ==================================//
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
      if ($num > 0)
         {
          $row = mysql_fetch_array($err); 
          
          //chdir ("../..");
          $ruta="./planos/pos/".$usuario."/";
          
          //echo getcwd()."3 <br>";
		  $dh=opendir($ruta);
		  if(readdir($dh) == false)
		    { 
	         mkdir($ruta,0777);
		    }
		  for ($j=1; $j <= ($conse); $j++)                  // Conse-1: Consecutivo anterior generado
			 {
			  if (file_exists($ruta."US".$j.".txt"))
			     unlink ($ruta."US".$j.".txt");               // Borro todos los archivos US anteriores
		     }
		     
		  $file=fopen ($ruta."US".$conse.".txt","w+");  
		 }
       
      $wemp1=explode("-",$wemp);    
         
      $q = "  SELECT riptid, vennit, riptus, clinom, ripeda, ripsex, ripmun, ripzon "
          ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000041, ".$wbasedato."_000063, ".$wbasedato."_000016 "  
          ."   WHERE fenfac BETWEEN '".$wfac_ini."' AND '".$wfac_fin."'"
          ."     AND fenest = 'on' "
          ."     AND fencod = '".$wemp1[0]."'"
          ."     AND fenfac = vennfa "
          ."     AND vennum = ripvta "
          ."     AND vennit = clidoc "
          ."   GROUP BY 1,2,3,4,5,6,7,8 ";
          
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
	  $wnumUS=$num;
	  
	  for ($i=1;$i<=$num;$i++)
	      {
		   $row = mysql_fetch_array($err);
		   
		   fwrite ($file,substr($row[0],0,2).",");       //Tipo de identificación
		   fwrite ($file,substr($row[1],0,20).",");      //Documento del usuario 
		   fwrite ($file,substr($wempcmi,0,6).",");      //Código entidad admministradora
		   fwrite ($file,substr($row[2],0,1).",");       //Tipo de usuario
		   
		   
		   //Aca separo los datos del nombre del cliente
		   $posicion = strpos($row[3], " ");
		   if ($posicion > 1) 
		      $wnom1=substr($row[3],0,$posicion);
		     else
		        $wnom1=$row[3]; 
		   $row[3]=substr($row[3],$posicion+1,strlen($row[3]));
		   if (strlen($row[3]) > 1)
		      {
			   $posicion = strpos($row[3], " ");
			   $wnom2=substr($row[3],0,$posicion);
			   $row[3]=substr($row[3],$posicion+1,strlen($row[3]));
			   
			   if ($posicion==0)
			      $wnom2=$row[3];
		      } 
			 else
			    $wnom2=""; 
		   
		   if (strlen($row[3]) > 1)
		      {
		       $posicion = strpos($row[3], " ");
		       $wape1=substr($row[3],0,$posicion);
		       $row[3]=substr($row[3],$posicion+1,strlen($row[3]));
		       
		       if ($posicion==0)
			      $wape1=$row[3];
	          }
	         else
	            $wape1="";  
		   
	       if (strlen($row[3]) > 1)
		      {     
			   $posicion = strpos($row[3], " ");
			   $wape2=substr($row[3],0,$posicion);
			   if ($posicion==0)
			      $wape2=$row[3];
			  }
		     else
		        $wape2="";  
		   fwrite ($file,$wape1.",");                    //1er apellido usuario
		   fwrite ($file,$wape2.",");                    //2do apellido usuario
		   fwrite ($file,$wnom1.",");                    //1er nombre usuario
		   fwrite ($file,$wnom2.",");                    //2do nombre usuario
		   fwrite ($file,$row[4].",");                   //Edad 
		   fwrite ($file,"1".",");                       //Unidad de medida de la edad
		   fwrite ($file,substr($row[5],0,1).",");       //Sexo
		   fwrite ($file,substr($row[6],0,2).",");       //Departamento
		   fwrite ($file,substr($row[6],2,3).",");       //Municipio
		   fwrite ($file,substr($row[7],0,1));           //Zona
		   
		   fwrite ($file,chr(13).chr(10));   //Imprimo el call return por cada linea
		  } 
	  fclose ($file);
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //================================= A C A   T E R M I N A   E L   A R C H I V O   ( U S ) =======================================//
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      
      
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //============================== A C A   ** C O M I E N Z A **   E L   A R C H I V O   ( A M ) ==================================//
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      
      if ($num > 0)
         {
          $row = mysql_fetch_array($err); 
          
          //chdir ("../..");
          $ruta="./planos/pos/".$usuario."/";
          
          //echo getcwd()."3 <br>";
		  $dh=opendir($ruta);
		  if(readdir($dh) == false)
		    { 
	         mkdir($ruta,0777);
		    }
		  for ($j=1; $j <= ($conse); $j++)                  // Conse-1: Consecutivo anterior generado
			 {
			  if (file_exists($ruta."AM".$j.".txt"))
			     unlink ($ruta."AM".$j.".txt");               // Borro todos los archivos AF anteriores
		     }
		     
		  $file=fopen ($ruta."AM".$conse.".txt","w+");  
		 }
       
      $wemp1=explode("-",$wemp);    
         
      $q = "  SELECT fenfac, riptid, vennit, ripaut, artcna, artpos, artgen, artffa, artcon, artuni, vdecan, vdevun, vdecan*vdevun "
          ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000063, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000001 " 
          ."   WHERE fenfac BETWEEN '".$wfac_ini."' AND '".$wfac_fin."'"
          ."     AND fenest = 'on' "
          ."     AND fencod = '".$wemp1[0]."'"
          ."     AND fenfac = vennfa "
          ."     AND vennum = ripvta "
          ."     AND vennum = vdenum "
          ."     AND vdeart = artcod "
          ."   GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13 ";
          
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
	  $wnumAM=$num;
	  
	  for ($i=1;$i<=$num;$i++)
	      {
		   $row = mysql_fetch_array($err);
		    
		   fwrite ($file,$row[0].",");                //Numero factura
		   fwrite ($file,$wcfgcpr.",");               //Codigo prestador de servicios
		   fwrite ($file,substr($row[1],0,2).",");    //Tipo de identificacion del usuario
		   fwrite ($file,substr($row[2],0,29).",");   //Documento del usuario
		   fwrite ($file,substr($row[3],0,15).",");   //Numero de autorizacion
		   fwrite ($file,substr($row[4],0,20).",");   //Codigo nacional del medicamento
		   if ($row[5]=="on") $wpos="1";
		   if ($row[5]=="off") $wpos="2";
		   fwrite ($file,$wpos.",");                  //Identificador de pos o NO pos
		   fwrite ($file,substr($row[6],0,30).",");   //Nombre generico
		   fwrite ($file,substr($row[7],0,20).",");   //Forma farmaceutica
		   fwrite ($file,substr($row[8],0,20).",");   //Concentracion del medicamento
		   fwrite ($file,substr($row[9],0,20).",");   //Unidad de medida
		   fwrite ($file,substr($row[10],0,5).",");   //Cantidad vendida
		   fwrite ($file,substr($row[11],0,15).",");  //Valor unitario
		   fwrite ($file,substr($row[12],0,15));  //Valor total
		   
		   fwrite ($file,chr(13).chr(10));   //Imprimo el call return por cada linea
		  } 
	  fclose ($file);
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //================================== A C A   T E R M I N A   E L   A R C H I V O   ( A M ) ======================================//
      ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      
      
      
      /*====================================================================================================================*/
	  /*================================  A C A   C O M I E N Z A   E L   A R C H I V O   C T  =============================*/
	  /*====================================================================================================================*/
	  //$q = "    SELECT Codigo_dls ";                                                  // Codigo del prestador de servicios
	  //$q = $q."   FROM root_000017 ";
	  //$q = $q."  WHERE Documento = '".$wmedico."'";      // Cedula del medico
										
	  //$err = mysql_query($q,$conex);
	  //$num = mysql_num_rows($err);
	  //$canct = 0;
											
	  if ($wnumAF > 0)
		{
			/*Desde aca capturo los nombres de los campos*/   		    
			$fields=mysql_num_fields($err);
     					
			//Si no existe la carpeta del usuario dentro del grupo la creo		
			$ruta="./planos/pos/".$usuario."/";
			$dh=opendir($ruta);
			if(readdir($dh) == false)
			  { 
		       mkdir($ruta,0777);
			  }
			  
			for ($j=1; $j <= ($conse); $j++)                                   // Conse-1: Consecutivo anterior generado
			   {
			    if (file_exists($ruta."CT".$j.".txt"))
			       unlink ($ruta."CT".$j.".txt");                                // Borro todos los archivos US anteriores
		       }
			$file=fopen ($ruta."CT".$conse.".txt","w+");
			
			  				  
			fwrite ($file,$wcfgcpr.",");                                         // Codigo del prestador de servicios
			fwrite ($file,$wfecfac.",");                                         // Fecha de remision
			fwrite ($file,"AF".$conse.",");                                      // Nombre del archivo
			fwrite ($file,$wnumAF);                                              // Total registros
			fwrite ($file,chr(13).chr(10));
					   
			if ($wnumUS > 0)
			   { 
				fwrite ($file,$wcfgcpr.",");                                     // Codigo del prestador de servicios
				fwrite ($file,$wfecfac.",");                                     // Fecha de remision
				fwrite ($file,"US".$conse.",");                                  // Nombre del archivo
				fwrite ($file,$wnumUS);                                          // Total registros
				fwrite ($file,chr(13).chr(10));
		       }		     
				     
			if ($wnumAM > 0)
			   { 
				fwrite ($file,$wcfgcpr.",");                                      // Codigo del prestador de servicios
				fwrite ($file,$wfecfac.",");                                      // Fecha de remision
				fwrite ($file,"AM".$conse.",");                                   // Nombre del archivo
				fwrite ($file,$wnumAM);                                           // Total registros
				fwrite ($file,chr(13).chr(10));
			   } 
			fclose ($file);
        }
        
      $ruta="/matrix/planos/pos/".$usuario."/";
      echo "<tr><td colspan=4 align=center><font size=5><b>ARCHIVOS GENERADOS CON EL CONSECUTIVO : ".$conse."</b></font></td></tr><br>";
   	  echo "<tr><td colspan=4 align=center><font size=5><b><A href=".$ruta.">Haga Click Para Bajar los Archivos</A></b></font></td></tr>";
   		  
      echo "</table>"; 
	 } 
}
?>
</body>
</html>