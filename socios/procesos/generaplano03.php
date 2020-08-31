<html>
<head>
  <title>Utilidad generar archivo plano de Socios Activos con Profesion, especialidades, Direcciones, ciudad y Correo electronico</title>
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
									
	echo "<form action='generaplano03.php' method=post>";

		//Generare archivo plano en esta ruta
   		$ruta=  "../archivos";   
   		//  ./ para que lo genere en un subdirectorio apartir de la ruta donde estan los fuentes
   		 $archivo = fopen($ruta."/socios03.txt","w"); 
   		 
        $query="SELECT socced, concat( socap1,' ', socap2,' ',socnom) AS nombre, propro, prfdes, espesp, espdes,"
        ." socnac, socdir, nombre, descripcion, soctof, soccoa, soceci"
        ." FROM socios_000001"
        ." LEFT OUTER JOIN socios_000003 ON ( socced = proced ) "  // Tabla profesiones x socio
        ." LEFT OUTER JOIN socios_000007 ON ( propro = prfcod ) "  // Maestro de profesiones
        ." LEFT OUTER JOIN socios_000004 ON ( socced = espced ) "  // Tabla de especialidades x socios
        ." LEFT OUTER JOIN socios_000010 ON ( espesp = espcod ) "  // Maestro de especialidades
        ." LEFT OUTER JOIN root_000002 ON "                   // Maestro de departamentos
        ." (socdep = root_000002.codigo)"
        ." LEFT OUTER JOIN root_000006 ON "                   // Maestro de Municipios
        ." (socmun = root_000006.codigo)"
        ." WHERE socact = 'A' "
        ." ORDER BY socced, propro, espesp";

          $resultado = mysql_query($query);
          $nroreg = mysql_num_rows($resultado);
          $i = 1;
          While ( $i <= $nroreg )
	      { 
           $registro = mysql_fetch_row($resultado); 
           $LineaDatos = $registro[0].chr(9).$registro[1].chr(9).$registro[2].chr(9).$registro[3].chr(9).$registro[4].chr(9).$registro[5].chr(9).$registro[6].chr(9)
                        .$registro[7].chr(9).$registro[8].chr(9).$registro[9].chr(9).$registro[10].chr(9).$registro[11].chr(9).$registro[12];
           fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
           $i++; 		
		  }
 
	    fclose($archivo);
       	Mysql_close($conex); 
	   	//Si en el href lo hago a la variable $ruta me mostrara todos los
	   	//archivos generados alli, pero si $ruta tiene el path completo
	   	//con el archivo generado lo bajaria directamente y no mostraria
	   	//otros archivos
	   	echo "<li><A href='".$ruta."/socios03.txt"."'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
       	echo "<br>";
       	echo "<li>Registros generados: ".($i-1);   

   	   	//echo "<li><A HREF='socios00.php'>Regresar</A>";

?>
</body>
</html>