<html>
<head>
  <title>Utilidad generar archivo plano de Socios de P.M.L.A. con Direcciones y Correo electronico</title>
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
									
	echo "<form action='generaplano02.php' method=post>";

	 if(!isset($wtodos) )
	 {
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>SISTEMA DE INFORMACION DE SOCIOS</td></tr>";
			echo "<tr><td align=center>GENERACION DE ARCHIVO PLANO GRAL</td></tr>";
			echo "<tr>";
			
            echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><font text color=#FF0000 size=3>Generar solo los que firmaron el acuerdo? (S/N) : </font>"; 
            echo "<INPUT TYPE='text' NAME='wtodos' style='text-align:center' size=5 maxlength=1 VALUE='N' onKeyUp='form.wreg.value=form.wreg.value.toUpperCase()'></INPUT></td>";

            echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><font text color=#FF0000 size=3>Generar reporte con acciones? (S/N) : </font>"; 
            echo "<INPUT TYPE='text' NAME='waccion' style='text-align:center' size=5 maxlength=1 VALUE='N' onKeyUp='form.wreg.value=form.wreg.value.toUpperCase()'></INPUT></td>";
             
            echo "<tr><td bgcolor=#cccccc align=center><input type='submit' value='IR'></td></tr></table>";	  
     }
     else
     {
                
		//Generare archivo plano en esta ruta
   		$ruta=  "../archivos";   
   		//  ./ para que lo genere en un subdirectorio apartir de la ruta donde estan los fuentes
   		 $archivo = fopen($ruta."/socios02.txt","w"); 
 
         $query="SELECT socced, socap1, socap2, socnom, socsex, socnac, socdir, nombre, descripcion, soctof, soccoa, socact, socfir, socpri, socacc, soctac,"
                ." socvot,socdel"
                ." FROM socios_000001, root_000006, root_000002 WHERE socmun = root_000006.codigo AND socdep = root_000002.codigo";
                
                if ($wtodos=="S")
                 $query=$query." AND socfir='S'";

                 $query=$query." AND socact='A'";
                
                 $query=$query." ORDER BY socap1,socap2";

          $resultado = mysql_query($query);
          
          // Genero la primera fila con los titulos de los campos
          $LineaDatos = "Cedula".chr(9)."1er Apellido".chr(9)."2do Apellido".chr(9)."Nombres".chr(9)."Sexo".chr(9)."F.Nac/to".chr(9)."Direccion".chr(9)
                        ."Ciudad".chr(9)."Dep/to".chr(9)."Telefono".chr(9)."EMail".chr(9)."Estado".chr(9)."Firmo Acuerdo".chr(9)."Especialidad".chr(9)
                        ."VotoxPoder".chr(9)."VotoxDelegacion";
                        
          if ($waccion=="S") 
            $LineaDatos=$LineaDatos.chr(9)."Acc Privi".chr(9)."Acc Ordinarias".chr(9)."Total Acciones";
              
          fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
          
          // Genero lo datos 
          $i = 1;
          While ( $registro = mysql_fetch_row($resultado) )
	      { 
		      
		   // Voy a mostrar la Primera especialidad grabada
           $query="Select Espdes"
                 ." From socios_000004,socios_000010"
                 ." Where espced='".$registro[0]."'"
                 ." And espesp=espcod"
                 ." Order By socios_000004.id";
                 $resultadoB = mysql_query($query);
                 $nroreg = mysql_num_rows($resultadoB);
                 if ($nroreg > 0)
                  { $registroB = mysql_fetch_row($resultadoB);  
                    $wespecialidad=$registroB[0];
                  }
                 else
                    $wespecialidad=" ";
		      
           $LineaDatos = $registro[0].chr(9).$registro[1].chr(9).$registro[2].chr(9).$registro[3].chr(9).$registro[4].chr(9).$registro[5].chr(9).$registro[6].chr(9)
                        .$registro[7].chr(9).$registro[8].chr(9).$registro[9].chr(9).$registro[10].chr(9).$registro[11].chr(9).$registro[12].chr(9).$wespecialidad.chr(9)
                        .$registro[16].chr(9).$registro[17];
	                              
           if ($waccion=="S") 
             $LineaDatos=$LineaDatos.chr(9).$registro[13].chr(9).$registro[14].chr(9).$registro[15];
                        
           fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
           $i++; 		
		  }
 
	    fclose($archivo);
       	Mysql_close($conex); 
	   	//Si en el href lo hago a la variable $ruta me mostrara todos los
	   	//archivos generados alli, pero si $ruta tiene el path completo
	   	//con el archivo generado lo bajaria directamente y no mostraria
	   	//otros archivos
	   	echo "<li><A href='".$ruta."/socios02.txt"."'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
       	echo "<br>";
       	echo "<li>Registros generados: ".($i-1);   

   	   	//echo "<li><A HREF='socios00.php'>Regresar</A>";
    }
?>
</body>
</html>