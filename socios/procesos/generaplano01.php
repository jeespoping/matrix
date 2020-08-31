<html>
<head>
  <title>Utilidad generar archivo plano de Socios de P.M.L.A.</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php"); 	

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
	
   

   mysql_select_db("matrix") or die("No se selecciono la base de datos");    
									
	echo "<form action='generaplano01.php' method=post>";
	
	if(!isset($wpro) )
	{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>SISTEMA DE INFORMACION DE SOCIOS</td></tr>";
			echo "<tr><td align=center>GENERACION DE ARCHIVO PLANO GRAL</td></tr>";
			echo "<tr>";
			
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Profesion:</font></b><br>";   
   
   $query = "SELECT prfcod,prfdes FROM socios_000007 ORDER BY prfdes";   
   echo "<select name='wpro'>"; 
   echo "<option></option>";
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c1=explode('-',$wpro); 			  
  		if($c1[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
   echo "</select></tr></td>";
   
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Especialidad:</font></b><br>";   
   
   $query = "SELECT espcod,espdes FROM socios_000010 ORDER BY espdes";   
   echo "<select name='wesp'>"; 
   echo "<option></option>";
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c2=explode('-',$wesp); 			  
  		if($c2[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
   echo "</select></tr></td>";
   
	 		
     echo "<tr><td bgcolor=#cccccc align=center><input type='submit' value='IR'>";
     echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox name=conf CHECKED>Ignorar especialidades";
     echo"</td></tr></table>";		
	}
	else
	{    
		//Generare archivo plano en esta ruta
   		$ruta=  "../archivos";   
   		//  ./ para que lo genere en un subdirectorio apartir de la ruta donde estan los fuentes
   		 $archivo = fopen($ruta."/socios01.txt","w"); 
   		 if ( $conf == "on" )
   		 {

	   	$query="SELECT socsex,socap1,socap2,socnom,socdir,nombre,descripcion,prfdes,socced "
	   	      ." FROM socios_000001,socios_000003,socios_000007,root_000006,root_000002"
              ." WHERE socact='A' "
              ." AND socced=proced "
              ." AND propro=prfcod "
              ." AND socmun = root_000006.codigo"
              ." AND socdep = root_000002.codigo";

        } 
   		else 
   		{
         /*
         $query="SELECT socsex, socap1, socap2, socnom, socdir, nombre, descripcion, prfdes, espesp, espdes, socced"
         ." FROM socios_000001, socios_000003"
         ." LEFT OUTER JOIN socios_000004 ON ( proced = espced )" 
         ." LEFT OUTER JOIN socios_000010 ON ( espesp = espcod ) , socios_000007, root_000006, root_000002"
         ." WHERE socced = proced"
         ." AND propro = prfcod"
         ." AND socmun = root_000006.codigo"
         ." AND socdep = root_000002.codigo"
         ." AND socact = 'A' ";
         */
         
		 $query="SELECT socsex,socap1,socap2,socnom,socdir,"
			   ." nombre,descripcion,prfdes,espesp,espdes,socced "
               ." FROM socios_000001,socios_000003,socios_000007,root_000006, "
               ." root_000002,socios_000004,socios_000010 "
               ." WHERE socact='A' "
               ." AND socced=proced "
               ." AND propro=prfcod "
               ." AND socmun = root_000006.codigo"
               ." AND socdep = root_000002.codigo"
               ." AND socced=espced"
               ." AND espesp=espcod";
        }
              if ($wpro <> "")
              {
               $c1=explode('-',$wpro); 	 
               $query=$query." AND prfcod='".$c1[0]."'";
              } 
              
              if ( $conf <> "on" ) 
              {
               if ($wesp <> "")
               {
                $c2=explode('-',$wesp); 	 
                $query=$query." AND espcod='".$c2[0]."'";
               }  
              } 
              $query=$query." ORDER BY socap1,socap2";

          $resultado = mysql_query($query);
          $nroreg = mysql_num_rows($resultado);
          $i = 1;
          While ( $i <= $nroreg )
	      { 
           $registro = mysql_fetch_row($resultado); 
           if ($registro[0] == 'M')   // Sexo Masculino
            $tipo="Señor";
           else
            $tipo="Señora";
         
           if ( $conf == "on" ) 
              $LineaDatos = $tipo.chr(9).$registro[1].chr(9).$registro[2].chr(9).$registro[3].chr(9).$registro[4].chr(9).$registro[5].chr(9).$registro[6]
                          .chr(9).$registro[7].chr(9).$registro[10];
           else
              $LineaDatos = $tipo.chr(9).$registro[1].chr(9).$registro[2].chr(9).$registro[3].chr(9).$registro[4].chr(9).$registro[5].chr(9).$registro[6]
                          .chr(9).$registro[7].chr(9).$registro[9].chr(9).$registro[10];
                         
           fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
           $i++; 		
		  }

	    fclose($archivo);
       	Mysql_close($conex); 
	   	//Si en el href lo hago a la variable $ruta me mostrara todos los
	   	//archivos generados alli, pero si $ruta tiene el path completo
	   	//con el archivo generado lo bajaria directamente y no mostraria
	   	//otros archivos
	   	echo "<li><A href='".$ruta."/socios01.txt"."'>Presione Clic Derecho Para Bajar El Archivo</A>";
       	echo "<br>";
       	echo "<li>Registros generados: ".($i-1);   
   	   	echo"</table>";
	}
	//echo "<li><A HREF='socios00.php'>Regresar</A>";
	
?>
</body>
</html>
