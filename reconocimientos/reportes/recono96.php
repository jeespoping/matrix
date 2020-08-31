<html>
<head>
<title>Reporte de como van las votaciones para el programa de reconocimientos</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.recono96.submit();   // Ojo para la funcion recono96 <> Recono96  (sencible a mayusculas)
	}
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Reporte de control de ingreso de empleados.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Octubre 31 DE 2007.                                                                                
//FECHA ULTIMA ACTUALIZACION  :4 de Noviembre de 2010.                                                                             
//DESCRIPCION			      :Empleados mas votados por Ccosto

$wactualiz="Ver. 2010-11-09";

session_start();
if(!isset($_SESSION['user']))  echo "error";
else
{
	echo "<form name='recono96' action='recono96.php' method=post>";  

	// Este programa se ejecuta en matrix recibiendo un parametro o desde el browser asi:
	// http://localhost/reconocimientos/recono96.php?tabla=recono_000001

	echo "<INPUT TYPE = 'hidden' NAME='tabla' VALUE='".$tabla."'></INPUT>"; 
	
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
	
echo "<center><table border=1>";
echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b>PROGRAMA DE RECONOCIMIENTOS </b></font></tr>";
echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b>ESTADO DE LAS VOTACIONES</b></font></td></tr>";
echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$titulo."</b></font></td></tr>";



if (!isset($wcco) or $wcco=='' )
{
   // Lleno un ComboBox con las planchas y dependiendo de la seleccion con la Funcion enter() muestro otro Combo
   // con los empleados asignados a esta.
   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Tipos de Categorias: <br></font></b>";
   
    $query = "Select catcod,catdes from recono_000003"
	        ."  ORDER BY catcod";
	$resultado = mysql_query($query,$conex);            // Ejecuto el query 	    
	$nroreg = mysql_num_rows($resultado);
	
	//Defino un ComboBox con las Tipos de categorias
	echo "<select name='wcco' onchange='enter()'>";
	echo "<option>TODOS</option>";
		
    $Num_Filas = 1;
	while ($Num_Filas <= $nroreg)
	  {
		$registro = mysql_fetch_row($resultado);  	
		$Num_Filas++;
		if(substr($wcco,0,strpos($wcco,"-")) == $registro[0])
	      echo "<option selected>".$registro[0]."-".$registro[1]."</option>";
	    else
	      echo "<option>".$registro[0]."-".$registro[1]."</option>";
      }   
    echo "</select></td>";
    Mysql_close($conex); 
	 	
       
   	echo "<tr><td align=center colspan=3 bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";          
   	echo "</tr>";
	echo "</center></table>";
}
else  ///////////              Cuando ya estan capturados todos los datos      ///////////////////////////
{
	echo "<center><table border=0>";
    echo "<tr><td align=center colspan=3 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>PROMOGRAMA DE RECONOCIMIENTOS</b></font></td></tr>";
	echo "<tr><td align=center colspan=3 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>Categoria: <i>".$wcco."</i></td></tr>";

    $w=explode('-',$wcco); 
     
    if ($wcco == "TODOS")
    {
	  $query = "Select votcat,votemp,count(*) AS total FROM recono_000002"
	          ." Group By votcat,votemp"
	          ." Order By votcat,total DESC,votemp";
    }          
    else
    {
	  $query = "Select votcat,votemp,count(*) AS total FROM recono_000002"
	          ." Where votcat = '".$w[0]."'" 
	          ." Group By votcat,votemp"
	          ." Order By votcat,total DESC,votemp";

 	}  
	$resultado = mysql_query($query,$conex);
	$nroreg = mysql_num_rows($resultado);
	
	
	/*      PRIMER REPORTE    */	
	
	// Para dejar una linea en blanco en la tabla
	// echo "<tr><td alinn=center colspan=3 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";    
	
	// Subtitulos dentro de la tabla
	echo "<tr>";
	echo "<th align=center bgcolor=#006699><font text color=#FFFFFF><b>EMPLEADO</b></font></th>";
	echo "<th align=center bgcolor=#006699><font text color=#FFFFFF><b>VOTACION</b></font></th>";
	echo "</tr>";
	
	$i = 1;	  // Variable para intercalar el color de fondo en la tabla
	
	// PROCESO DE IMPRESION CON ROMPIMIENTO DE CONTROL POR CENTRO DE COSTO
    $ccoant = "";
    while ($registro = mysql_fetch_row($resultado))
	{
        // color de fondo  
	    if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	else
	   	  $wcf="CCFFFF";    

				 
		IF ($ccoant != $registro[0])
		{
		 // Actualizo las variables
	     $ccoant = $registro[0];
	      
		 // imprimo subtitulos
		 if ($ccoant <> "0000" ) 
		 {
 	       $query = "SELECT catcod,catdes "
	               ."   FROM recono_000003 "
	               ."  WHERE catcod = '".$ccoant."'";         
           $resultado2 = mysql_query($query,$conex);          // Ejecuto el query 
	       $nombre=mysql_result($resultado2,0,1)."( ".mysql_result($resultado2,0,0)." ) ";
	       echo "<tr bgcolor=#666699><td><font text color=#FFFFFF>".$nombre."</font></td>";	      	       
         }
         else
          echo "<tr bgcolor=#FFFFFF><td>VOTOS EN BLANCO</td>";        
        }
		echo "</tr>";
		
        if ($registro[1] <> "00000")
        { 	 
           	
	      $query = "SELECT recap1,recap2,recno1,recno2 FROM recono_000001";
	      $query = $query." WHERE reccod = '".$registro[1]."'";
	      $resultado2 = mysql_query($query,$conex);          // Ejecuto el query 
	      // Concateno el nombre
	      $nombre="( ".$registro[1]." ) ".mysql_result($resultado2,0,0)." ".mysql_result($resultado2,0,1)." ".mysql_result($resultado2,0,2)." ".mysql_result($resultado2,0,3);
	      echo "<tr bgcolor=".$wcf."><td>".$nombre."</td>";
	      echo "<td>".$registro[2]."</td>";
	      echo "</tr>";  
        }
        else
        {
   	      echo "<tr bgcolor=".$wcf."><td>VOTO EN BLANCO</td>";
	      echo "<td>".$registro[2]."</td>";
	      echo "</tr>";  
        }  
 
	      
	   	$i++; 		
	    
    } // Del While
    
    echo "</table>"; 

    //EMPLEADOS QUE HAN VOTADO
    $query = "Select votcod FROM recono_000002"
	        ." Group By votcod";
	$resultado = mysql_query($query,$conex);
	$nroreg = mysql_num_rows($resultado);
	
	$query = "Select count(*) FROM recono_000002";
	$resultado = mysql_query($query,$conex);
	$nrovot = mysql_result($resultado,0);
		
	echo "<center>";
	echo "<br>Han votado ".$nroreg." empleados ";
	echo "<br>Total de Votos: ".$nrovot;	
	echo "</center>";

		
/*      SEGUNDO REPORTE    */	    
		echo "<br><br>";
    	echo "<center><table border=0>";
    	echo "<tr><td align=center colspan=4 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>EMPLEADOS QUE FALTAN POR VOTAR POR CCOSTO</b></font></td></tr>";
    	echo "<tr>";
    	echo "<td>CCOSTO</td>";    	
    	echo "<td>CODIGO</td>";
    	echo "<td>NOMBRE</td>";
    	echo "<td>CARGO</td>";    	
    	echo "<tr>";
 	    
	    // Lleno una tabla con los empleados que NO han votado 
	        
	    $query = "SELECT reccod,recap1,recap2,recno1,recno2,recofi,recuni "
	        	."   FROM recono_000001"
		       	."  GROUP BY reccod,recap1,recap2,recno1,recno2,recofi,recuni "
		        ."  ORDER BY recuni,recap1";
		        
	    $resultado = mysql_query($query,$conex);          // Ejecuto el query 
		$nroreg = mysql_num_rows($resultado);
		
		$Num_Filas=1;
	    $tot_filas = 0;
		while ($tot_filas <= $nroreg)
		{ 
		   $registro=mysql_fetch_row($resultado);	   
		   
		   $query = "Select count(*) FROM recono_000002 Where votcod = '".$registro[0]."'";

	 	   $yavoto = mysql_query($query,$conex);
		   $nrovot = mysql_result($yavoto,0);		   
		   if ( $nrovot < 1 )   //No encontro ==> muestro
		   {	  
			  // color de fondo  
	         if (is_int ($Num_Filas/2))  // Cuando la variable $Num_Filas es par coloca este color
	          $wcf="DDDDDD";  
	   	     else
	   	      $wcf="CCFFFF";    
	   	     echo "<tr bgcolor=".$wcf.">";
	   	     
			 echo "<td>".$registro[6]."</td>";   
			 echo "<td>".$registro[0]."</td>";
		     echo "<td>".$registro[1]." ".$registro[2]." ".$registro[3]." ".$registro[4]."</td>"; 		     
		     echo "<td>".$registro[5]."</td>"; 
		     
		     echo "</tr>";  
   			 $Num_Filas++;
	       }
	       $tot_filas++;   
	    }   
	    echo "</table>";
	    echo "<center>";
	    echo "<br>Faltan por votar ".$Num_Filas." empleados ";
        echo "</center>";
         
	Mysql_close($conex); 
    
    //Para que refresque cada 5 minutos con el mismo centro de costos seleccionado y el parametro inicial tabla
    echo "<meta http-equiv='refresh' content='300;url=recono96.php?wcco=".$wcco."&tabla=".$tabla."'>";
}

} // De la sesion
?>
</BODY>
</html>
