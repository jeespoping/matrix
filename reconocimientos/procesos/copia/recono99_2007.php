<html>
<head>
<title>Reporte de gestión de reconocimientos</title>
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
		document.forms.recono99.submit();   // Ojo para la funcion recono99 <> Recono99  (sencible a mayusculas)
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
    }
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Captura votacion programa de reconocimientos.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :OCTUBRE 20 DE 2007.                                                                                
//FECHA ULTIMA ACTUALIZACION  :24 de Octubre de 2007.                                                                             

//DESCRIPCION                 :Este programa actualiza la tabla 'votaci' en Mysql que tiene seis campos  (Mas los campos de matrix)
//                             votcod char(6) 		CODIGO EMPLEADO QUE VOTA
//                             votemp char(6) 		CODIGO EMPLEADO POR EL QUE VOTAN
//                             votcc1 char(4) 		CCOSTO EMPLEADO QUE VOTA
// 							   votcc2 char(4) 		CCOSTO EMPLEADO POR EL QUE VOTAN
//                             votfec timestamp  	FECHA-HORA DE VOTACION
//                             votdip char(20)      DIRECCION IP DONDE HICIERON LA VOTACION    

//                             Adicionalmente se creo una tabla en UNIX 'votset' con dos campos "codemp" codigo empleado que vota y "ccovot"
//                             con el centro de costos de personas por las que puede votar, Asi la funcion de esta tabla es especificarle
//                             a cada empleado porque ccostos puede votar. Caso Angela Ocampo puede votar por personas de Comunicaciones
//                             y de Informatica. Asi un persona aparece tantos veces como en centros de costo pueda votar. El archivo lo pasa
//                             talento humano en excel a partir de del query SETH.SQL y luego se toman solo las columnas codigo y centro de 
//                             costo de votacion. Para que el programa funcione sin cambios en farmastor y Clinica del sur se pasa el mismo
//                             archivo pero a todos los empleados les colocan centro de costos de votacion  de la gerencia (todos votan por
//                             por todos)

session_start();
if(!isset($_SESSION['user']))
  echo "error";
else
{
	
	echo "<form name='recono99' action='recono99.php' method=post>";  
	
	// Este programa se ejecuta en matrix recibiendo un parametro o desde el browser asi:
	// http://localhost/reconocimientos/recono99.php?tabla=recono_000001
	
	echo "<INPUT TYPE = 'hidden' NAME='tabla' VALUE='".$tabla."'></INPUT>"; 
	
	
	switch ($tabla)
	{          
	  case "recono_000001": 
		$conexN = odbc_connect('nomina','','')
				or die("No se realizo Conexion con la BD nomina PROMOTORA en Informix");
		$titulo = "CLINICA LAS AMERICAS";		
	    break;
	  case "recono_000002":
	    $conexN = odbc_connect('nomsto','','')
				or die("No se realizo Conexion con la BD nomina FARMASTOR en Informix");
		$titulo = "FARMASTOR";				
		break;
	  case "recono_000003":
      	$conexN = odbc_connect('nomsur','','')
				or die("No se realizo Conexion con la BD nomina CLINICA DEL SUR en Informix");
		$titulo = "CLINICA DEL SUR";	
	    break;	  	      
	}//del switch 

	//

	

	or die("No se ralizo Conexion con MySql ");
	
	mysql_select_db("matrix") or die("No se selecciono la base de datos");    

	// TOMO LA DIRECCION IP DE DONDE EJECUTAN EL PROGRAMA (Funciona si no pasa por un proxi)
	$ip=getenv("REMOTE_ADDR");	

	
			
	echo "<center><table border=1>";
	//echo "<tr><td colspan=1 align=center><IMG SRC='logo1.gif' ></td></tr>";
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font size=3 text color=#FFFFFF><b> VOTACIONES ".$titulo."</b></font></tr>";
	$tit0 = "RECONOCIMIENTOS POR SU ALTO NIVEL DE COMPETENCIAS Y COMPROMISOS";
	echo "<tr><td align=center rowspan=1 colspan=1 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$tit0."</b></font></td></tr>";
	$tit1 = "CON LA CUTURA EMPRESARIAL REFLEJADA EN EL SETH";
	echo "<tr><td align=center rowspan=1 colspan=1 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$tit1."</b></font></td></tr>";


	//Seteo la variable de ambiente del usuario PARA PRUEBAS LOCALES
	//$user = "07012";  

	   
	if ( isset($user) and !isset($wemp1) )
	{
	 if (!isset($wotr)) 
	 {	
	    // Lleno un combo con los empleados que NO han votado 
	    echo "<tr><td align=CENTER colspan=1 bgcolor=#006699><b><font text color=#FFFFFF size=3> PERSONAS APTAS PARA VOTAR <br></font></b>";    
	    $query = "SELECT percod,perap1,perap2,perno1,perno2,ofinom "
	        	."   FROM noper,noofi"
		       	."  WHERE peretr = 'A' "
		       	."    AND perofi = oficod "
		        ."  ORDER BY perap1";
	    $resultado = odbc_do($conexN,$query);            // Ejecuto el query 
		
		echo "<select name='wotr'>";	
		
	    $Num_Filas = 0;
		while (odbc_fetch_row($resultado))
		{ 
	       
		   $query = "Select count(*) FROM ".$tabla." Where votcod = '".odbc_result($resultado,1)."'";
	 	   $yavoto = mysql_query($query,$conex);
		   $nrovot = mysql_result($yavoto,0);
		   if ( $nrovot < 1 )   //No encontro ==> muestro
		   {	  
			$Num_Filas++;
			if(substr($wotr,0,strpos($wotr,"-")) == odbc_result($resultado,1))
	         echo "<option selected>".odbc_result($resultado,1)."- ".odbc_result($resultado,2)." ".odbc_result($resultado,3)." ".odbc_result($resultado,4)." ".odbc_result($resultado,5)."-".odbc_result($resultado,6)."</option>";
	        else 
	         echo "<option>".odbc_result($resultado,1)."- ".odbc_result($resultado,2)." ".odbc_result($resultado,3)." ".odbc_result($resultado,4)." ".odbc_result($resultado,5)."-".odbc_result($resultado,6)."</option>";
	       }   

	    }   
	    echo "</select></td>";
	 }
	 else   //Si ya seleccionaron el usuario a votar 
	 { 	  
	  if (!isset($wemp1) )
	  {
	   $user2 = substr($wotr,0,strpos($wotr,"-"));
	    
	   $query = "SELECT perno1,perno2,perap1,perap2,persex From noper";
	   $query = $query." WHERE percod = '".$user2."'";
	   $query = $query."   AND peretr = 'A' ";
	   $resultado = odbc_do($conexN,$query);         // Ejecuto el query 
	   if (odbc_result($resultado,5) == "M" )
	   { 
	    $tit3 = odbc_result($resultado,1)." ".odbc_result($resultado,2)." ".odbc_result($resultado,3)." ".odbc_result($resultado,4);
	    echo "<tr><td align=center rowspan=1 colspan=1 bgcolor=#003366><font size=3 text color=#FFFFFF><b>"."Sr. ".$tit3." usted puede votar por: "."</b></font></td></tr>";
	   }
	   else
	   { 
	    $tit3 = odbc_result($resultado,1)." ".odbc_result($resultado,2)." ".odbc_result($resultado,3)." ".odbc_result($resultado,4);
	    echo "<tr><td align=center rowspan=1 colspan=1 bgcolor=#003366><font size=3 text color=#FFFFFF><b>"."Sra. ".$tit3." usted puede votar por: "."</b></font></td></tr>";
	   } 
		
		
		//Tomo EL o LOS ccosto de votacion permitidos al usuario que entro
	    $query = "SELECT ccovot "
		       ."   FROM votset"
		       ."  WHERE codemp = '".$user2."'";
	    $resultado1 = odbc_do($conexN,$query);            

	    //Total de empleados en los centro de costos permitidos al codigo que entro 
	     $wnro = 0;
	     $wcon = 0;
		 while (odbc_fetch_row($resultado1))
		  {
	  	    $query = "SELECT count(*) "
		       		."  FROM votset"
		       		." WHERE ccovot = '".odbc_result($resultado1,1)."'" ; 
	  		
		    $resultado = odbc_do($conexN,$query);            
	    	$wnro = $wnro + odbc_result($resultado,1);     
	    	$wcon++;
	    	
		  }
	       
	    // DEPENDIENDO DEL NRO DE EMPLEADOS EN ESOS CCOSTOS PERMITIRA SELECCIONAR UN MAXIMO DE NOMINADOS
	    // 1-10 personas = 1 Nominados.
		// 11-30 personas = 2 Nominados.
		// 31- 50 personas = 3 Nominados.
		// 51 - 100 Personas = 5 Nominados. 
		// Mas de 100 Personas = 7 Nominados. 

		if (($wnro > 0) and ($wnro <= 10))
	  	 $wper = 1;
	    else
	     if (($wnro > 10) and ($wnro <= 30))
	      $wper = 2;
	     else
	      if (($wnro > 30) and ($wnro <= 50))
	       $wper = 3;
	      else
	       if (($wnro > 50) and ($wnro <= 100))
	        $wper = 5;
	       else
	        if ($wnro > 100)     
	         $wper = 7;
	        else
	         $wper = 0;   //Se da si suben un codigo de empleado que no existe a la tabla votset
	     
	     $i= 1;
	     While ( $i<=$wper)
	     {
	     // Abro tantos combos como maximo nominados me permita seleccionar 
	      echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Empleado: <br></font></b>";          
	      
	      //Tomo EL o LOS ccosto de votacion permitidos al usuario que entro
	      $query = "SELECT ccovot "
		       ."   FROM votset"
		       ."  WHERE codemp = '".$user2."'";
	      $resultado1 = odbc_do($conexN,$query);    
	      
	         
	      echo "<select name='wemp".$i."'>";       //Creo una variable tantos nominados se puedan wemp1,wemp2,wemp3....
	      
	      if ($i==1) 
	      {
		    // Solo en el primer combo muestra el voto en blanco  
	        echo "<option></option>";
	        echo "<option>00000- VOTO EN BLANCO</option>";
	      }  
	            
	      while (odbc_fetch_row($resultado1))    
	      {          
	        $query = "SELECT codemp,perap1,perap2,perno1,perno2,ofinom FROM noper,votset,noofi";
		    $query = $query." WHERE codemp <> '".$user2."'";
		    $query = $query."   AND ccovot = '".odbc_result($resultado1,1)."'";
		    $query = $query."   AND peretr = 'A' ";
		    $query = $query."   AND codemp = percod";
		    $query = $query."   AND perofi = oficod";
	        $query = $query." ORDER BY 2";
		    $resultado = odbc_do($conexN,$query);         // Ejecuto el query 
		  
		    echo "<option></option>";
	        $Num_Filas = 0;
		    while (odbc_fetch_row($resultado))
		    {
		  	 $Num_Filas++;
		  	
	   	     switch ($i)
	         {          
		     case 1: 
	    	  $wcodigo = substr($wemp1,0,strpos($wemp1,"-")); 
		      break;
	         case 2:
	    	  $wcodigo = substr($wemp2,0,strpos($wemp2,"-"));
		      break;
	         case 3:
	          $wcodigo = substr($wemp3,0,strpos($wemp3,"-")); 
		      break;
	         case 4:
	       	  $wcodigo = substr($wemp4,0,strpos($wemp4,"-"));
		      break;
	         case 5:
	    	  $wcodigo = substr($wemp2,0,strpos($wemp5,"-"));
		      break;
	         case 6:
	          $wcodigo = substr($wemp3,0,strpos($wemp6,"-")); 
		      break;
	         case 7:
	       	  $wcodigo = substr($wemp4,0,strpos($wemp7,"-"));
		      break;
	  	      
	         }//del switch 
	         if ($wcodigo == odbc_result($resultado,1))
	          echo "<option selected>".odbc_result($resultado,1)."- ".odbc_result($resultado,2)." ".odbc_result($resultado,3)." ".odbc_result($resultado,4)." ".odbc_result($resultado,5)." ( ".odbc_result($resultado,6)." )</option>";
		     else
		      echo "<option>".odbc_result($resultado,1)."- ".odbc_result($resultado,2)." ".odbc_result($resultado,3)." ".odbc_result($resultado,4)." ".odbc_result($resultado,5)." ( ".odbc_result($resultado,6)." )</option>";
	          	
	        }//del while odbc resultado
	        
	     
	      }// del while obdc resultado1
	      echo "</select></td>";
	          
	      $i++;
	      
	     }//del while i

	      // 'wnro','wper' y $user2 son variables escondidas que enviaremos a travez del formulario
	      if (isset($wnro))
	       echo "<INPUT TYPE = 'hidden' NAME='wnro' VALUE='".$wnro."'></INPUT>"; 
	      else
	       echo "<INPUT TYPE = 'hidden' NAME='wnro'></INPUT>";  
	     
	      if (isset($wper))
	       echo "<INPUT TYPE = 'hidden' NAME='wper' VALUE='".$wper."'></INPUT>"; 
	      else
	       echo "<INPUT TYPE = 'hidden' NAME='wper'></INPUT>";  
	       
	      if (isset($user2))
	       echo "<INPUT TYPE = 'hidden' NAME='user2' VALUE='".$user2."'></INPUT>"; 
	      else
	       echo "<INPUT TYPE = 'hidden' NAME='user2'></INPUT>";  
	       
	       

	     
	   }   
	 
	 }// del if $wotr usuario a votar
	  
	   	echo "<tr><td align=center colspan=1 bgcolor=#cccccc>";
	   	echo "<input type='submit' value='Votar'>";          
	   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='limpiar' id='BReset'>";
	   	echo "</td></tr>";
	  
	}// del if user   	
		

	if (isset($wemp1) and $wemp1<>'')      
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

	  if (isset($wemp7))
	   $e7=explode('-',$wemp7);   
	     
	      $j = 0;
	      
	      $i = 1;
	      While ( $i<=$wper)
	      {
		   // PUEDE PASAR QUE YO TENGA DERECHO A VOTAR POR MAXIMO 3 PERSONAS, ENTONCES ENTRO HOY Y VOTO POR 2 PERSONAS
		   // LUEGO VUELVO Y ENTRO Y VOTO POR OTRAS 2 O TRES MAS, ENTOCES CADA VEZ CUENTO EL NRO DE VOTOS REALIZADOS
		   
		   // NOTA: Esto ya no se da con el cambio que al entrar solo muestra las personas que faltan por votar
	   
	       $query = "Select count(*) FROM ".$tabla." Where votcod = '".$user2."'";
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
		    case 7: 
		     if (isset($wemp7) and is_numeric($e7[0]) )
		     {
			  $e7=explode('-',$wemp7);      
			  $empleado = $e7[0];
	    	 } 
		     break;  
	  	    }//   Del switch
		   
	 	    if ( is_numeric($empleado) )
	  	    {
		  	 $query0 = "Select votemp FROM ".$tabla." Where votcod = '".$user2."' And votemp = '".$empleado."' And votemp <> '00000' ";   
	 	     $resultado = mysql_query($query0,$conex);
	 	     $nroreg = mysql_num_rows($resultado);
	 	     if ($nroreg > 0 )    // Ya voto por ese empleado
	 	     { 
		      echo "<table border=1>";	 
		      echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
		      echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, USTED YA VOTO POR EL EMPLEADO CON CODIGO: ".mysql_result($resultado,0)."</MARQUEE></font>";				
		      echo "</td></tr></table><br><br>";
	         } 
	 	     else
	         {  
		        
		        // Busco si tiene voto en blanco 
		       	$query0 = "Select votemp FROM ".$tabla." Where votcod = '".$user2."' And votemp = '00000' ";   
	 	     	$resultado = mysql_query($query0,$conex);
	 	     	$nroreg = mysql_num_rows($resultado);
	 	     	if ($nroreg > 0 )    // Ya voto en blanco
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
	    	     $query = "SELECT percco "
		       			."   FROM noper"
		       			."  WHERE percod = '".$user2."'";
	    	     $resultado = odbc_do($conexN,$query);            
	    	     $ccosto = odbc_result($resultado,1);   
	  
	 	         //Tomo el ccosto del usuario por el que votaron
	    	     $query = "SELECT percco "
		       			."   FROM noper"
		       			."  WHERE percod = '".$empleado."'";
	    	     $resultado = odbc_do($conexN,$query);            
	    	     $wcco = odbc_result($resultado,1);    	   
	    	     if ($wcco == "")
	               $wcco = "00000";
	                   	     
	    	     $FechaHora=date("Y-m-d H:i:s a");
	      		 $fecha = date("Y-m-d");
				 $hora = (string)date("H:i:s");
	      		 $query1 = "INSERT INTO ".$tabla." (medico,fecha_data,hora_data, Votcod, Votemp, Votcc1, Votcc2, Votfec, Votdip, Seguridad) VALUES ('recono','".$fecha."','".$hora."','".$user2."','".$empleado."','".$ccosto."','".$wcco."','".$FechaHora."','".$ip."','C-recono')";
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
		     
	  	odbc_close($conexN); 
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
