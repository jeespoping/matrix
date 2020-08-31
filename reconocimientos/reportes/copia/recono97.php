<html>
<head>
<title>Reporte de como van las votaciones para el programa de reconocimientos</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.recono97.submit();   // Ojo para la funcion recono97 <> Recono97  (sencible a mayusculas)
	}
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Reporte de conrol de votaciones.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Octubre 31 DE 2007.                                                                                
//FECHA ULTIMA ACTUALIZACION  :28 de Septiembre de 2008.                                                                             
//DESCRIPCION			      :Empleados mas votados por Ccosto

$wactualiz="Ver. 2009-09-28";

session_start();
if(!isset($_SESSION['user']))
  echo "error";
else
{
	echo "<form name='recono97' action='recono97.php' method=post>";  

	// Este programa se ejecuta en matrix recibiendo un parametro o desde el browser asi:
	// http://localhost/reconocimientos/recono97.php?tabla=recono_000001

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
	
echo "<center><table border=1>";
echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b> PROGRAMA DE RECONOCIMIENTOS </b></font></tr>";
echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b> ESTADO DE LAS VOTACIONES </b></font></td></tr>";
echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$titulo."</b></font></td></tr>";



if (!isset($wcco) or $wcco=='' )
{
   // Lleno un ComboBox con los centros de costos de la tabla cocco de informix
   // y dependiendo de la seleccion con la Funcion enter() muestro otro Combo
   // con los empleados asignados a este centro de costos.
   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> C.Costos: <br></font></b>";
   
    $query = "SELECT ccocod,cconom "
	       ."   FROM cocco"
	       ."  WHERE ccoact = 'S' "
	       ."  ORDER BY cconom";
    $resultado = odbc_do($conexN,$query);            // Ejecuto el query 
	
	//Defino un ComboBox con los centros de costo del query anterior maneja AutoEnter
	echo "<select name='wcco' onchange='enter()'>";
	echo "<option>TODOS</option>";
		
    $Num_Filas = 0;
	while (odbc_fetch_row($resultado))
	  {
		$Num_Filas++;
		if(substr($wcco,0,strpos($wcco,"-")) == odbc_result($resultado,1))
	      echo "<option selected>".odbc_result($resultado,1)."-".odbc_result($resultado,2)."</option>";
	    else
	      echo "<option>".odbc_result($resultado,1)."-".odbc_result($resultado,2)."</option>";
      }   
    echo "</select></td>";
    odbc_close($conexN); 
       
   	echo "<tr><td align=center colspan=3 bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";          
   	echo "</tr>";
	echo "</center></table>";
}
else  ///////////              Cuando ya estan capturados todos los datos      ///////////////////////////
{
	echo "<center><table border=0>";
    echo "<tr><td align=center colspan=3 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>PROMOGRAMA DE RECONOCIMIENTOS</b></font></td></tr>";
	echo "<tr><td align=center colspan=3 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>Centro de Costos: <i>".$wcco."</i></td></tr>";

    $w=explode('-',$wcco); 
     
    if ($wcco == "TODOS")
    {
	  $query = "Select votcc2,votemp,count(*) AS total FROM ".$tabla
	          ." Group By votcc2,votemp"
	          ." Order By votcc2,total DESC";
    }          
    else
    {
	  $query = "Select votcc2,votemp,count(*) AS total FROM ".$tabla
	          ." Where votcc2 = '".$w[0]."'" 
	          ." Group By votcc2,votemp"
	          ." Order By votcc2,total DESC";

 	}  

	$resultado = mysql_query($query,$conex);
	$nroreg = mysql_num_rows($resultado);
	
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
 	       $query = "SELECT ccocod,cconom "
	               ."   FROM cocco "
	               ."  WHERE ccocod = '".$ccoant."'"
	               ."  ORDER BY cconom";
           $resultado2 = odbc_do($conexN,$query);            // Ejecuto el query 
	       $nombre=odbc_result($resultado2,2)."( ".odbc_result($resultado2,1)." ) ";
	       echo "<tr bgcolor=#FFFFFF><td>".$nombre."</td>";
         }
         else
          echo "<tr bgcolor=#FFFFFF><td>VOTOS EN BLANCO</td>";  
	       
        }
		echo "</tr>";
		
        if ($registro[1] <> "00000")
        { 	 
           	
	      $query = "SELECT perap1,perap2,perno1,perno2 FROM noper";
	      $query = $query." WHERE percod = '".$registro[1]."'";
	      $resultado2 = odbc_do($conexN,$query);  
	      // Concateno el nombre
	      $nombre="( ".$registro[1]." ) ".odbc_result($resultado2,1)." ".odbc_result($resultado2,2)." ".odbc_result($resultado2,3)." ".odbc_result($resultado2,4);
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
    $query = "Select votcod FROM ".$tabla
	        ." Group By votcod";
	$resultado = mysql_query($query,$conex);
	$nroreg = mysql_num_rows($resultado);
	
	//EMPLEADOS APTOS PARA VOTAR
	$query = "SELECT COUNT(*)"
	       	."   FROM noper,noofi"
	       	."  WHERE peretr = 'A' "
	       	."    AND perofi = oficod ";      
	$resultado = odbc_do($conexN,$query);       
	$Num_Filas = odbc_result($resultado,1);
	$Faltan = $Num_Filas - $nroreg;
	
	echo "<center>";
	echo "<br>Han votado ".$nroreg." empleados de ".$Num_Filas;
	echo "<br>Faltan por votar: ".$Faltan." empleados";
   
	//PORCENTAJE DE VOTACION
	$PorVot = ($nroreg / $Num_Filas ) * 100;
	echo "<br>Porcentaje de votacion: ".number_format($PorVot,2,'.',',')."%";
	echo "</center>"; 
	
	
	
	echo "<br><br>";
    echo "<center><table border=0>";
    echo "<tr><td align=center colspan=4 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>VOTACION POR CENTRO DE COSTO</b></font></td></tr>";
    echo "<tr>";
    echo "<td>CODIGO</td>";
    echo "<td>DESCRIPCION</td>";
    echo "<td>PERSONAS</td>";
    echo "<td>HAN VOTADO</td>";
    echo "<td>FALTAN</td>";
    echo "</th>";
    echo "<tr>";
    //PERSONAS APTAS VOTAR POR CCOSTO 
	//$query="Select ccovot,codemp,cconom from votset,cocco where ccovot=ccocod order by ccovot,codemp";  //Asi fue para el año 2007
	$query="Select percco,percod,cconom from noper,cocco where percco=ccocod and peretr = 'A' order by percco,percod";
    $resultado = odbc_do($conexN,$query); 
    $i=1;    //Para amanejar el color del fon
    $ccoant = "";    //para controlar el rompimiento por centro de costo
  	while (odbc_fetch_row($resultado))
	{
	   	
	   	//Rompimiento    
		iF ($ccoant != odbc_result($resultado,1))
		{
		 if ($ccoant <> "") //Menos la primera vez
		 {
			echo "<td>".$perxcco."</td>";
		    echo "<td>".$votosxcco."</td>"; 
		    $faltan = $perxcco - $votosxcco;
		    echo "<td>".$faltan."</td>"; 
		    echo "</tr>";  
		    $i++; 
	     }   
	 
		 // Actualizo las variables
	     $ccoant = odbc_result($resultado,1);
	     $votosxcco = 0;   //votos por centro de costo 
	     $perxcco = 0;    //personas por ccosto
	     
	     // color de fondo  
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    
	   	  
         echo "<tr bgcolor=".$wcf.">";
		 echo "<td>".odbc_result($resultado,1)."</td>";
		 echo "<td>".odbc_result($resultado,3)."</td>"; 
	    }
	    
	    $perxcco++; 
	    $query = "Select votcod FROM ".$tabla." Where votcod = '".odbc_result($resultado,2)."'";
	    $resultado2 = mysql_query($query,$conex);
	    $nroreg = mysql_num_rows($resultado2);
	    if ($nroreg > 0 )    // Ya voto 
	      $votosxcco++;		
	    
      }   
      //Si es fin imprimo totales del ultimo ccosto
		echo "<td>".$perxcco."</td>";
	    echo "<td>".$votosxcco."</td>"; 
	    $faltan = $perxcco - $votosxcco;
	    echo "<td>".$faltan."</td>"; 
	    echo "</tr>";  

      odbc_close($conexN); 
	  Mysql_close($conex); 
    
    //Para que refresque cada 5 minutos con el mismo centro de costos seleccionado y el parametro inicial tabla
    echo "<meta http-equiv='refresh' content='300;url=recono97.php?wcco=".$wcco."&tabla=".$tabla."'>";
}

} // De la sesion
?>
</BODY>
</html>
