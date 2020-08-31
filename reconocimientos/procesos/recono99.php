<html>
<head>
<title>Proceso de gestión de reconocimientos</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script>
    function ira()
    {
	 document.recono99.wemp1.focus();
	 }
</script>

<script type="text/javascript">
	function enter()
	{
		document.forms.recono99.submit();   // Ojo para la funcion Recono99 <> recono99  (sencible a mayusculas)
	}
</script>

<script languaje='JavaScript'>
 	function vaciarCampos()
	{
	 document.forms.recono99.wemp1.value = '';
	 document.forms.recono99.wemp2.value = '';
     document.forms.recono99.wemp3.value = '';
     document.forms.recono99.wemp4.value = '';
     document.forms.recono99.wemp5.value = '';
     document.forms.recono99.wemp6.value = '';
     document.forms.recono99.wemp7.value = '';
     document.forms.recono99.wemp8.value = '';
     document.forms.recono99.wemp9.value = '';
     document.forms.recono99.wemp10.value = '';
     document.forms.recono99.wemp11.value = '';
     document.forms.recono99.wemp12.value = '';
     document.forms.recono99.wotr.value = '';     
    }
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Captura votacion programa de reconocimientos.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :SEPTIEMBRE 18 DE 2009.                                                                                
//FECHA ULTIMA ACTUALIZACION  :02 de Noviembre de 2010.                                                                             

//DESCRIPCION                 :Este programa actualiza la tabla 'recono_000002' en Mysql que tiene siete campos  (Mas los campos de matrix)
//                             votcod char(6) 		CODIGO EMPLEADO QUE VOTA
//                             votemp char(6) 		CODIGO EMPLEADO POR EL QUE VOTAN
//                             votcat char(1)       Categoria
//                             votcc1 char(4) 		CCOSTO EMPLEADO QUE VOTA
// 							   votcc2 char(4) 		CCOSTO EMPLEADO POR EL QUE VOTAN
//                             votfec timestamp  	FECHA-HORA DE VOTACION
//                             votdip char(20)      DIRECCION IP DONDE HICIERON LA VOTACION    



session_start();
if(!isset($_SESSION['user']))
  echo "error";
else
{
	
	echo "<form name='recono99' action='recono99.php?tabla=recono_000001' method=post>";  
	
	// Este programa se ejecuta en matrix recibiendo un parametro o desde el browser asi:
	// http://localhost/reconocimientos/recono99.php?tabla=recono_000001
	
	echo "<INPUT TYPE = 'hidden' NAME='tabla' VALUE='".$tabla."'></INPUT>"; 
	
	
//Seteo la variable de ambiente del usuario PARA PRUEBAS LOCALES
$user = "recono";  
	
	
	switch ($tabla)
	{          
	  case "recono_000001": 
		$titulo = "CLINICA LAS AMERICAS";		
	    break;
	  case "recono_000004":
		$titulo = "FARMASTOR";				
		break;
	  case "recono_000005":
		$titulo = "CLINICA DEL SUR";	
	    break;	  	      
	}//del switch 

	//

	
	
	
	mysql_select_db("matrix") or die("No se selecciono la base de datos");    

	// TOMO LA DIRECCION IP DE DONDE EJECUTAN EL PROGRAMA (Funciona si no pasa por un proxi)
	$ip=getenv("REMOTE_ADDR");	

			
	echo "<center><table border=1>";
	//echo "<tr><td colspan=1 align=center><IMG SRC='logo1.gif' ></td></tr>";
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font size=3 text color=#FFFFFF><b> DIRECCION DE INFORMATICA </b></font></tr>";
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font size=3 text color=#FFFFFF><b> ".$titulo."</b></font></tr>";
	$tit0 = "PROGRAMA DE RECONOCIMIENTOS 2010";
	echo "<tr><td align=center rowspan=1 colspan=1 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$tit0."</b></font></td></tr>";

	   
	if ( isset($user) and !isset($wemp1) )
	{
	 if (!isset($wotr) or $wotr=="") 
	 {	
	    // Lleno un combo con los empleados que NO han votado 
	    echo "<tr><td align=LEFT colspan=1 bgcolor=#006699><b><font text color=#FFFFFF size=3>Personas aptas para votar: <br></font></b>";    

	    $query = "SELECT reccod,recap1,recap2,recno1,recno2,recofi "
	        	."   FROM recono_000001"
	        	."  GROUP BY reccod,recap1,recap2,recno1,recno2,recofi" 
		        ."  ORDER BY recap1,recap2";	        
		        
	    $resultado = mysql_query($query,$conex);            // Ejecuto el query 	    
	    $nroreg = mysql_num_rows($resultado);
		
		echo "<select name='wotr'>";	
		echo "<option></option>";
		
	    $Num_Filas = 1;
		while ($Num_Filas <= $nroreg)
		{ 
	       $registro = mysql_fetch_row($resultado);  	
	       		
		   $query = "Select count(*) FROM recono_000002 Where votcod = '".$registro[0]."'";
		   $yavoto = mysql_query($query,$conex);
		   $nrovot = mysql_result($yavoto,0);
		   if ($nrovot < 1 )   //No encontro ==> muestro
		   {	  			
			//if(substr($wotr,0,strpos($wotr,"-")) == $registro[0])
	        // echo "<option selected>".$registro[0]."- ".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]."-".$registro[5]."</option>";
	        //else 
	         echo "<option>".$registro[0]."- ".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]."-".$registro[5]."</option>";
	       }   
           $Num_Filas++;
	    }   
	    echo "</select></td>";
	 }
	 else   //Si ya seleccionaron el usuario a votar 
	 { 	  
	  if (!isset($wemp1))
	  {
	   $user2 = substr($wotr,0,strpos($wotr,"-"));
	    
	   $query = "SELECT recno1,recno2,recap1,recap2,reccod,recccb,recuni From recono_000001";
	   $query = $query." WHERE reccod = '".$user2."'";
	   
       $resultado = mysql_query($query,$conex);            // Ejecuto el query 	   
       $registro = mysql_fetch_row($resultado);  	 
       $tit3 = $registro[0]." ".$registro[1]." ".$registro[2]." ".$registro[3];
       echo "<tr><td align=center rowspan=1 colspan=1 bgcolor=#003366><font size=3 text color=#FFFFFF><b>"."Sr(a). ".$tit3." usted puede votar por: "."</b></font></td></tr>";
		
	   $query ="SELECT rectp1 FROM recono_000001 WHERE  reccod='".$user2."'";
	   $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
	   $registro = mysql_fetch_row($resultado1);
	   $wtipo=$registro[0];
				 
		//Personas por las que puede votar en la categoria tipo 1.1	
		$query ="SELECT reccod, recap1, recap2, recno1, recno2, recuni, reccco"
               ." FROM recono_000001"
               ." WHERE reccca IN (SELECT recccb FROM recono_000001 WHERE reccod = '".$user2."')"
               ." AND reccod <> '".$user2."'" //Nunca se vota por uno mismo 
               ." AND rectp1 = 'B'"
               ." GROUP BY reccod, recap1, recap2, recno1, recno2, recuni, reccco"
               ." ORDER BY recuni, recap1,recap2";
         // echo $query;
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
		$nroreg2 = mysql_num_rows($resultado1);	
		if (($nroreg2>0) and ($wtipo<>"C"))		
		{
	     echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Imagen y Presentación<br></font></b>";          
         echo "<select name='wemp1'>"; 	      
         // Solo en el primer combo muestra el voto en blanco  
         echo "<option></option>";
         echo "<option>00000- VOTO EN BLANCO</option>";            
   		
         $i = 1;
         While ($i <= $nroreg2)	             
	     {               
          echo "<option></option>";
          $registro = mysql_fetch_row($resultado1);  	 
          echo "<option>".$registro[0]."- ".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]." ( ".$registro[5]." )</option>";         
          $i++;
         }
         echo "</select></td>";  
        }
        
 		//Personas por las que puede votar en la categoria tipo 1.2	
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
		$nroreg2 = mysql_num_rows($resultado1);		
        if (($nroreg2>0) and ($wtipo<>"C"))      
        {
         echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Cordialidad y orientación al servicio<br></font></b>";          
         echo "<select name='wemp2'>"; 	      
         // Solo en el primer combo muestra el voto en blanco  
         echo "<option></option>";
         echo "<option>00000- VOTO EN BLANCO</option>";            
         echo "<option></option>";
         $i = 1;
         While ( $i <= $nroreg2 ) 
	     {       
		  echo "<option></option>";   
		  $registro = mysql_fetch_row($resultado1);  	 
          echo "<option>".$registro[0]."- ".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]." ( ".$registro[5]." )</option>";
          $i++;
         }
         echo "</select></td>";    
        }  
        
        //Personas por las que puede votar en la categoria tipo 1.3	
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
		$nroreg2 = mysql_num_rows($resultado1);		
	    if (($nroreg2>0) and ($wtipo<>"C"))
	    {           
         echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Disciplina y Compromiso<br></font></b>";          
         echo "<select name='wemp3'>"; 	      
         // Solo en el primer combo muestra el voto en blanco  
         echo "<option></option>";
         echo "<option>00000- VOTO EN BLANCO</option>";            
         echo "<option></option>";
         $i = 1;
         While ($i <= $nroreg2)  	              
	     {       
		  echo "<option></option>";
		  $registro = mysql_fetch_row($resultado1);  	 
          echo "<option>".$registro[0]."- ".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]." ( ".$registro[5]." )</option>";
          $i++;
         }
         echo "</select></td>";    
        } 
        
	    //Personas por las que puede votar en la categoria tipo 2	
	    
		$query ="SELECT rectp2 FROM recono_000001 WHERE  reccod='".$user2."'";
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
		$registro = mysql_fetch_row($resultado1);
		$wtipo=$registro[0];
	    	    	    
	    $query ="SELECT reccod, recap1, recap2, recno1, recno2, recuni, reccco"
               ." FROM recono_000001"
               ." WHERE "
               ." reccod <> '".$user2."'" //Nunca se vota por uno mismo 
               ." AND rectp2 = 'B'"
               ." GROUP BY reccod, recap1, recap2, recno1, recno2, recuni, reccco"
               ." ORDER BY recap1,recap2";
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
	    $nroreg2 = mysql_num_rows($resultado1);		
	    if (($nroreg2>0) and ($wtipo<>"C"))
	    {
	     echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Líder de Cultura<br></font></b>";          
         echo "<select name='wemp4'>"; 	      
         // Solo en el primer combo muestra el voto en blanco  
         echo "<option></option>";
         echo "<option>00000- VOTO EN BLANCO</option>";            
         echo "<option></option>";
         $i = 1;
         While ($i <= $nroreg2) 	              
	     {       
		  echo "<option></option>";
		  $registro = mysql_fetch_row($resultado1);  	 
          echo "<option>".$registro[0]."- ".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]." ( ".$registro[5]." )</option>";          
          $i++;
         }
         echo "</select></td>";   
        }
        
       	//Personas por las que puede votar en la categoria tipo 3
       	
		$query ="SELECT rectp3 FROM recono_000001 WHERE  reccod='".$user2."'";
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
		$registro = mysql_fetch_row($resultado1);
		$wtipo=$registro[0];

       		
		$query ="SELECT reccod, recap1, recap2, recno1, recno2, recuni, reccco"
               ." FROM recono_000001"
               ." WHERE reccod <> '".$user2."'" //Nunca se vota por uno mismo 
               ." AND rectp3 = 'B'"
               ." GROUP BY reccod, recap1, recap2, recno1, recno2, recuni, reccco"
               ." ORDER BY recap1,recap2";
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
		$nroreg2 = mysql_num_rows($resultado1);		
		if (($nroreg2>0) and ($wtipo<>"C"))
		{
	     echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Coordinadores y Directores<br></font></b>";          
         echo "<select name='wemp5'>"; 	      
         // Solo en el primer combo muestra el voto en blanco  
         echo "<option></option>";
         echo "<option>00000- VOTO EN BLANCO</option>";            
         echo "<option></option>";
         $i = 1;
         While ($i <= $nroreg2) 	              
	     {      
		  echo "<option></option>"; 
		  $registro = mysql_fetch_row($resultado1);  	 
          echo "<option>".$registro[0]."- ".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]." ( ".$registro[5]." )</option>";
          $i++;
         }
         echo "</select></td>";        
        }
                  
		//Personas por las que puede votar en la categoria tipo 4	
		
		$query ="SELECT rectp4 FROM recono_000001 WHERE  reccod='".$user2."'";
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
		$registro = mysql_fetch_row($resultado1);
		$wtipo=$registro[0];
		
		$query ="SELECT reccod, recap1, recap2, recno1, recno2, recuni, reccco"
               ." FROM recono_000001"
               ." WHERE reccca IN (SELECT recccb FROM recono_000001 WHERE reccod = '".$user2."')"
               ." AND reccod <> '".$user2."'" //Nunca se vota por uno mismo 
               ." AND rectp4 = 'B'"
               ." GROUP BY reccod, recap1, recap2, recno1, recno2, recuni, reccco"
               ." ORDER BY recap1,recap2";
	    $resultado1 = mysql_query($query,$conex);            // Ejecuto el query 	         
		$nroreg2 = mysql_num_rows($resultado1);		
		
		if ( ($nroreg2>0) and ($wtipo<>"C"))
		{
	     echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Médico líder<br></font></b>";          
         echo "<select name='wemp6'>"; 	      
         // Solo en el primer combo muestra el voto en blanco  
         echo "<option></option>";
         echo "<option>00000- VOTO EN BLANCO</option>";            
         echo "<option></option>";
         $i = 1;
         While ($i <= $nroreg2) 	              
	     {       
		  echo "<option></option>";
		  $registro = mysql_fetch_row($resultado1);  	 
          echo "<option>".$registro[0]."- ".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]." ( ".$registro[5]." )</option>";
          $i++;
         }
         echo "</select></td>";  	                                                        	     
        } 

	      // Variables escondidas que enviaremos a travez del formulario
	     	       	       
	      if (isset($user2))
	       echo "<INPUT TYPE = 'hidden' NAME='user2' VALUE='".$user2."'></INPUT>"; 
	      else
	       echo "<INPUT TYPE = 'hidden' NAME='user2'></INPUT>";  
	       
	      if (isset($tipo))
	       echo "<INPUT TYPE = 'hidden' NAME='tipo' VALUE='".$tipo."'></INPUT>"; 
	      else
	       echo "<INPUT TYPE = 'hidden' NAME='tipo'></INPUT>";  	       
	           
	 
	   }// del if $wotr usuario a votar
     
	  } // del else del if de $wotr
	  	  	  
	    
	   	echo "<tr><td align=center colspan=1 bgcolor=#cccccc>";
	   	echo "<input type='submit' value='Votar'>";          
	   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='limpiar' id='BReset'>";
	   	echo "</td></tr>";
	  
	}// del if user
	   	
		

	if ( (isset($wemp1) and $wemp1<>'') or (isset($wemp2) and $wemp2<>'') or (isset($wemp3) and $wemp3<>'') or (isset($wemp4) and $wemp4<>'') or (isset($wemp5) and $wemp5<>'') or (isset($wemp6) and $wemp6<>''))
	{
	  ///////////              Cuando minimo tengo un dato capturado    //////////////////
	  	  
	  if (isset($wemp1))
	   $e1=explode('-',$wemp1);   
	  
	  if (isset($wemp2))
	   $e2=explode('-',$wemp2);   

	  if (isset($wemp3))
	   $e3=explode('-',$wemp3);   

	  if (isset($wemp4))
	   $e4=explode('-',$wemp4);   

	  if (isset($wemp5))
	   $e5=explode('-',$wemp5);   

	  if (isset($wemp6))
	   $e6=explode('-',$wemp6);   


	      $wper=6;
	     
	      $j = 0;
	      $i = 1;
	      While ( $i<=$wper)
	      {
		   // PUEDE PASAR QUE YO TENGA DERECHO A VOTAR POR MAXIMO 3 PERSONAS, ENTONCES ENTRO HOY Y VOTO POR 2 PERSONAS
		   // LUEGO VUELVO Y ENTRO Y VOTO POR OTRAS 2 O TRES MAS, ENTOCES CADA VEZ CUENTO EL NRO DE VOTOS REALIZADOS
		   
		   // NOTA: Esto ya no se da con el cambio que al entrar solo muestra las personas que faltan por votar
	   
	       $query = "Select count(*) FROM recono_000002 Where votcod = '".$user2."'";
	       
	 	   $resultado = mysql_query($query,$conex);
		   $nrovot = mysql_result($resultado,0);
		   
		   if ( $nrovot >= $wper )  
		   {
		    echo "<table border=1>";	 
		    echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
		    echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>USTED YA COMPLETO EL PROCESO DE VOTACION !!!!</MARQUEE></font>";				
		    echo "</td></tr></table><br><br>";
		    $i = $wper;  // Para salir
	       }  
	       else 
		   {
			
			 $empleado="";
			
	     	switch ($i)
	        {          
		    case 1: 
		     if (isset($wemp1) and is_numeric($e1[0]) )
		     { 
		      $e1=explode('-',$wemp1);   
		      $empleado = $e1[0];
	    	 } 
		     break;
		    case 2: 
		     if (isset($wemp2) and is_numeric($e2[0]) )
		     { 
		      $e2=explode('-',$wemp2);   
		      $empleado = $e2[0];
	    	 } 
		     break;
		    case 3: 
		     if (isset($wemp3) and is_numeric($e3[0]) )
		     {
	    	  $e3=explode('-',$wemp3);    
	    	  $empleado = $e3[0];
	         }
		     break;
		    case 4: 
		     if (isset($wemp4) and is_numeric($e4[0]) )
		     { 
		      $e4=explode('-',$wemp4);   
		      $empleado = $e4[0];
	    	 } 
		     break;
		    case 5: 
		     if (isset($wemp5) and is_numeric($e5[0]) )
		     {
		      $e5=explode('-',$wemp5);    
		      $empleado = $e5[0];
	    	 } 
		     break;
		    case 6: 
		     if (isset($wemp6) and is_numeric($e6[0]) )
		     { 
		      $e6=explode('-',$wemp6);   
		      $empleado = $e6[0];
	    	 } 
		     break;
		     
	  	    }//   Del switch
		   
	 	    if ( is_numeric($empleado) )
	  	    {
		  	 $query0 = "Select votemp FROM recono_000002 Where votcod = '".$user2."' And votcat = '".$i."' And votemp <> '00000' ";   
	 	     $resultado = mysql_query($query0,$conex);
	 	     $nroreg = mysql_num_rows($resultado);
	 	     if ($nroreg > 0 )    // Ya voto para esta categoria
	 	     { 
		      echo "<table border=1>";	 
		      echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
		      echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, USTED YA VOTO POR EL EMPLEADO CON CODIGO: ".mysql_result($resultado,0)."</MARQUEE></font>";				
		      echo "</td></tr></table><br><br>";
	         } 
	 	     else
	         {  
		        
		        // Busco si tiene voto en blanco 
		       	$query0 = "Select votemp FROM recono_000002 Where votcod = '".$user2."' And votcat = '".$i."' And votemp = '00000' ";    
	 	     	$resultado = mysql_query($query0,$conex);
	 	     	$nroreg = mysql_num_rows($resultado);
	 	     	if ($nroreg > 0 )    // Ya voto en blanco en esta categoria
	 	     	{
		      	 echo "<table border=1>";	 
		      	 echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
		      	 echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>USTED VOTO EN BLANCO!!!!</MARQUEE></font>";				
		      	 echo "</td></tr></table><br><br>";
		      	 $i = $wper;  // Para salir
	      		}
	      		else
	      		{      		
	 	         //Tomo el ccosto original del usuario que vota
	    	     $query = "SELECT reccco FROM ".$tabla." WHERE reccod = '".$user2."'";
       	 	     $resultado = mysql_query($query,$conex);
		         $ccosto = mysql_result($resultado,0);
  
	 	         //Tomo el ccosto del usuario por el que votaron
	    	     $query = "SELECT reccco FROM ".$tabla." WHERE reccod = '".$empleado."'";
	    	     $resultado = mysql_query($query,$conex);
	    	     $nroreg = mysql_num_rows($resultado);
	    	     if ($nroreg>0)
		           $wcco = mysql_result($resultado,0);
		         else
	               $wcco = "00000";      //Seleccionaron voto en blanco 
	                   	     
	    	     $FechaHora=date("Y-m-d H:i:s a");
	      		 $fecha = date("Y-m-d");
				 $hora = (string)date("H:i:s");
	      		 $query1 = "INSERT INTO recono_000002 (medico,fecha_data,hora_data, Votcod, Votemp, Votcat, Votcc1, Votcc2, Votfec, Votdip, Seguridad) VALUES ('recono','".$fecha."','".$hora."','".$user2."','".$empleado."','".$i."','".$ccosto."','".$wcco."','".$FechaHora."','".$ip."','C-recono')";
	  	         $resultado = mysql_query($query1,$conex) or die("ERROR GRABANDO VOTACION : ".mysql_errno().":".mysql_error());  //Adiciono
		         if ($resultado)
		          $j++;
		         else 
		         {
	 		      echo "<table border=1>";	 
		          echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
		          echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, AL GRABAR REGISTROS!!!!</MARQUEE></font>";				
		          echo "</td></tr></table><br><br>";
	  	         }  	        
	  	        
	  	        }// Del voto en blanco 
	  	        
	  	     }// Del ya voto por ese empleado
	  	      
	  	    }//Si no es numerico no lo procesa (por ejemplo cuando del combobox selecciona linea en blanco o lineas con notas de observacion)
	  	    
	  	   }// del if ya voto    
	  	   
	  	   $i++;  
	      }//del while i 
	      
	      if ($j == 1)
		     echo "<br><tr><td bgcolor=#33FFFF colspan=4 align=center>Se grabo ".$j." voto valido</td></tr>";
		  else
		   if ($j > 1)
		     echo "<br><tr><td bgcolor=#33FFFF colspan=4 align=center>Se grabaron ".$j." votos validos</td></tr>";   
		     	  	
	 	Mysql_close($conex); 
	 	
	}// del if de datos capturados
	echo "</center></table>"; 
	/*echo "<center>";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp";
	echo "<A HREF='salida.php'>Salir del programa</A>";   
	echo "</center>";*/
	echo "</form>";
	
} //De la session
?>
</BODY>
</html>
