<html>
<head>
<title>Generacion de Consultas por SQL</title>
</head>

<script type="text/javascript">

 	function vaciarCampos()	
	{
	 document.forms.generasql.wsql.value = '';
    }

	function numeros(e)
	{
     key = e.keyCode || e.which;
     tecla = String.fromCharCode(key).toLowerCase();
     letras = "0123456789";
     especiales = [8];      // El 8 es para que la tecla <backspace> tambien la deje digitar (Se pueden incluir otros ascii separados por coma)
 
     tecla_especial = false
     for(var i in especiales)
	 {
      if(key == especiales[i])
	   {
         tecla_especial = true;
         break;
       }
     }
 
      if(letras.indexOf(tecla)==-1 && !tecla_especial)
        return false;
	}
	
    function ira()
    {
	 document.generasql.wsql.focus();
	}

	function enter()
	{
	 document.forms.generasql.submit();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">
<?php
include_once("conex.php");
/*  
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
*/

  
 
  mysql_select_db("matrix") or die("No se selecciono la base de datos");    
  
   // Abro el tag FORM ojo debe llevar "name" para poder ejecutar las funciones ira() y enter()								
   echo "<form name='generasql' action='generasql.php' method=post>";
  
   echo "<center><table border=0>"; // Tabla para pintar la captura de datos
	 
	  echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
	  echo "<tr><td align=center>GENERACION DE ARCHIVOS PLANOS  JairS</td></tr>";
	  
      // Si No hay instruccion O si el checkbox no esta confirmado  	** LO COMENTE PARA QUE ME VUELVA A PINTAR LOS CAMPOS **
      // if ((!isset($wsql)) or (!$confirmar)) 
      // {
	 
     echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Tablas: </font></b>";   
     echo "<select name='wtab' onchange='enter()'>"; 
	 echo "<option></option>";     // Primera opcion en blanco
     // $result = mysql_list_tables("matrix");           // Ejecuto el query tambien podria ser:  $result = mysql_query("SHOW TABLES") y $query1="SHOW COLUMNS FROM
     $result = mysql_query("SHOW TABLES");           // Ejecuto el query tambien podria ser:  $result = mysql_query("SHOW TABLES") y $query1="SHOW COLUMNS FROM
	 
     $i = 1;
     While ($row = mysql_fetch_row($result)) 
      {	  
  		if($wtab == $row[0])
	      echo "<option selected>".$row[0]."</option>";
	    else
	      echo "<option>".$row[0]."</option>"; 
	    $i++; 
      }   
      mysql_free_result($result);
     echo "</select>";
    
     echo "<b><font text color=#003366 size=2 align=center>  Separador de campos: </font></b>"; 
     if (isset($wsep))
      echo "<INPUT TYPE='text' NAME='wsep' size=3 maxlength=1 style='text-align:center' VALUE='".$wsep."'></INPUT></td>"; 
     else
      echo "<INPUT TYPE='text' NAME='wsep' size=3 maxlength=1 style='text-align:center' VALUE='|'></INPUT></td>"; 
     
    if ( strlen($wtab) > 0 ) 
    {                                                          	  
      $result = mysql_query("SELECT * FROM ".$wtab."  LIMIT 0, ".$wnro);  // Este Limit es por si es una tabla grande no traiga registros pero asi capturo
	                                                                      // Los nombres de los campos de cada registro y ademas puedo tomar el nro de campos:
      $fields = mysql_num_fields($result);            //Nro de campos de cada registro en la tabla *
	  
	  if  ($wsql=='')   // Si el campo instruccion Sql esta vacio por comodidad coloca automaticamente un select asi 
	  {                                                                                                                      
        //$wsql="Select * From ".$wtab;                                                                               
        $wsql="Select ";                                                                                                     
        for ($i=0; $i < $fields-1; $i++)                                                                                        
          $wsql=$wsql.mysql_field_name($result, $i).",";	                                                                
        $wsql=$wsql.mysql_field_name($result, $i)." From ".$wtab;       
      }

		$queryTipoDato = "SELECT COLUMN_NAME,DATA_TYPE
							FROM information_schema.COLUMNS
   						   WHERE table_schema = 'matrix' 
							 AND TABLE_NAME='".$wtab."';";
							 
		$resTipoDato = mysql_query($queryTipoDato, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTipoDato . " - " . mysql_error());		   
		$numTipoDato = mysql_num_rows($resTipoDato);
		
		$arrayTipoDatos = array();
		if($numTipoDato>0)
		{
			while($rowCampos = mysql_fetch_array($resTipoDato))
			{
				$arrayTipoDatos[$rowCampos['COLUMN_NAME']] = $rowCampos['DATA_TYPE'];
			}
		}			
 	}
	 // Muestro los campos de la tabla seleccionada
      echo "<tr>";
      echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Campos</font></b><br>";   
      echo "<select name='wcam'>"; 
      for ($i=0; $i < $fields; $i++) // y para cada campo tomo....
      {
	   $name  = mysql_field_name($result, $i);      //Nombre del campo $i
       // $type  = mysql_field_type($result, $i);      //Tipo de dato del campo $i 
	   $type  = $arrayTipoDatos[$name];      //Tipo de dato del campo $i 
       $len   = mysql_field_len($result, $i);       //Longitud o tamaño del campo $i
       $flags = mysql_field_flags($result, $i);     //Indica si el campo $i es: not_null y si es primary_key o multiple_key  
 		if($wcam == $name)
	      echo "<option selected>".$name.";".$type.";".$len.";".$flags."</option>";
	    else
	      echo "<option>".$name."; ".strtoupper($type)."; ".$len."; ".$flags."</option>";
      }
      echo "</select></td>";
	  
    
     echo "<tr>"; 								
     echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>INSTRUCCION SQL</font></b><br>";
     
     if (isset($wsql))
      echo "<textarea name='wsql' cols=80 rows=15>".stripslashes($wsql)."</textarea></td>";
     else
      echo "<textarea name='wsql' cols=80 rows=15></textarea></td>";

	
    echo "<tr><td align=center colspan=6 bgcolor=#cccccc>";
	
	echo "<input type='checkbox' name=wgenerar >Generar Plano";
	
	echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	
	if (isset($wnro))
       echo " Nro de Reg a visualizar: <INPUT TYPE='text' NAME='wnro' size=10 maxlength=8 VALUE=".$wnro." onkeypress='return numeros(event);'> 0=Todos</INPUT></td>"; 
     else
       echo " Nro de Reg a visualizar: <INPUT TYPE='text' NAME='wnro' size=10 maxlength=8 VALUE=100 onkeypress='return numeros(event);'> 0=Todos</INPUT></td>";     
    
 
    echo "<tr><td align=center colspan=6 bgcolor=#cccccc>";
    echo "<input type='submit' value='Ejecutar'>";
    if(isset($confirmar))
	 echo "<input type='checkbox' name=confirmar checked>Confirmar";
	else
	 echo "<input type='checkbox' name=confirmar>Confirmar";  
    
	echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	
    echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";   
    echo "</td>";

/*****************************************************************************************/
    
    // Si ya hay instruccion y el checkbox esta confirmado
    if ( (strlen($wsql) > 0) and (isset($confirmar)) )
    {
		
	  $query=strtolower($wsql);            // Paso toda la instruccion a minuscula 
	  
      $pos1 = strpos($query, "delete");    // Busco palabras reservadas que no dejare ejecutar en un query
      $pos2 = strpos($query, "drop");      // Ademas instrucciones SQL que tengan estas palabras no generan
      $pos3 = strpos($query, "update");    // registros para visualizar o generar un archivo plano
      $pos4 = strpos($query, "truncate"); 
      $pos5 = strpos($query, "insert"); 
      $pos6 = strpos($query, "create");    //Create si voy a dejar hacer.... por el Else
	  
    if ( ($pos1 === false) and ($pos2 === false) and ($pos3 === false) and ($pos4 === false) and ($pos5 === false) and ($pos6 === false) )
      {	
        //Para generar o ver solo los que el usuario especifique	
	    if ($wnro > 0 )
          $query=$query." LIMIT 0, ".$wnro;	
	
   		// Ejecuto el Query
   		$query=stripslashes($query);                           //Como el string pasa por el navegador quito las barras del string con comillas escapadas
        $resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
		// Tomo el numero de registros seleccionados 
        $nroreg = mysql_num_rows($resultado);
		  
		if ( $nroreg > 0 )
		{
			
		  If ( isset($wgenerar) )  //Si selecciono generar archivo plano con el resultado Abro el archivo
		  {
	       // Ruta para el archivo plano 
	       $ruta=  "../archivos";   
   		   // Abro el archivo
   		   $archivo = fopen($ruta."/plano01.txt","w"); 
		  }
		  
          // Nro de campos que se muestran en el query 
          $campos = mysql_num_fields($resultado);            
          $i = 1;
		  
		  if ( $nroreg > $wnro )
		  {
		    if ($wnro == 0 )
			 $VerReg =  $nroreg;
		    else
             $VerReg =  $wnro;
		  }
		  else
			$VerReg =  $nroreg;
		
          While ( $i <= $VerReg )
	      { 
            $registro = mysql_fetch_row($resultado);    // Leo un registro
			
            If ( !isset($wgenerar) )    // Si selecciono generar archivo Plano NO muestro resultado en pantalla
			{
			
			 //Si es el primer registro definino la tabla para visualizar los datos y coloco los nombres de los campos como titulos	 
			 if ($i==1)   
			 {
			  echo "<center><table border=0>";  
              for ($j=0; $j<$campos; $j++)             
                echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>".mysql_field_name($resultado, $j)."<b></td>";	
			  echo "<tr>";	
			 }
			 
			 // color para la linea de datos  
             if (is_int ($i/2))    // Cuando la variable $i es par coloca este color
              $wcf="DDDDDD";   
             else
              $wcf="CCFFFF";  
		  
  	         // Muestro el registro
             for ($j=0;$j<$campos;$j++)   	      			
               echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$registro[$j]."</td>";
			 echo "<tr>";	
			 		 
			}
			
			 If ( isset($wgenerar) )  //Si selecciono generar archivo plano con el resultado
			 {
              $LineaDatos="";
           	  for ($j=0;$j<$campos;$j++)
           	  {
           	   //$type = mysql_field_type($resultado, $j);   // Tomo el tipo del campo
			   $type = mysqli_fetch_field_direct($resultado, $j);   // Tomo el tipo del campo
           	   $name = mysql_field_name($resultado, $j);   // Tomo el nombre del campo
           	   
           	   if (($type == "blob")  or ($type == "string"))               // Si es campo MEMO o STRING Reemplazo los (CR) Carriage return y los (LF) Line Feed salto de linea  
           	   {                                                            // osea que la combinacion CRLF se cambia por espacio y punto coma
	           	 $nuevostr = str_replace(chr(13), "", $registro[$j] );    
	           	 $nuevostr = str_replace(chr(10), ";", $nuevostr );
	           	 $LineaDatos=$LineaDatos.$nuevostr.$wsep;
	           }
               else
                 $LineaDatos=$LineaDatos.$registro[$j].$wsep; 
              }
			  fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
			 }   
         
           $i++;
		  }
		  echo "</table>";    // De la Tabla de visualizacion de datos		  
		   
    	  echo "<tr><td bgcolor=#dddddd  colspan=1 align=center><b>Registros generados: ".($i-1)."</b></td>";
		  
		  echo"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		  
		  if ( isset($wgenerar) )  //Si selecciono generar archivo plano con el resultado
		  {
		   fclose($archivo);
    	   echo "<tr><td bgcolor=#dddddd  colspan=1 align=center><b><A href=".$ruta."/plano01.txt"." TARGET='_blank'>Presione Clic Derecho Para Bajar El Archivo.</A></b></td>";
		  }
          
		}
		else
		{
		 echo "<tr><td align=center colspan=1 bgcolor=#F5DEB3>";
	     echo "No hay registros que cumplan con estas condiciones.....";
	     echo "</td>";
		}
      }
	  else
	  { 
	   if ( $pos6 !== False )	// OJO Como solo devuelve False y no true toca con negacion asi que
	    {                       // Si es un create no genera registros pero si dejo ejecutar para crear tablas por ejemplo
       	   
  	     // Ejecuto el Query
   	     $query=stripslashes($query);                           //Como el string pasa por el navegador quito las barras del string con comillas escapadas
         $resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
         if ($resultado)
         {
	      echo "<tr><td align=center colspan=1 bgcolor=#F5DEB3>";
	      echo "Se ejecuto la Instruccion SQL con exito.....";
	      echo "</td>";
	     }
	   }
	   else
	   {
	     echo "<tr><td align=center colspan=1 bgcolor=#F5DEB3>";
	     echo "Con esta utilidad NO se pueden ejcutar intrucciones SQL con DROP, DELETE, UPDATE, TRUNCATE, INSERT";
	     echo "</td>";
	   }	 
      }
    }
	echo "</table>";    // De la Tabla de visualizacion de datos "
    echo "</form>";
?>
</body>
</html>