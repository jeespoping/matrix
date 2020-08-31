<html>
<head>
<title>Reporte de como van las votaciones para el programa de reconocimientos</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.recono95.submit();   // Ojo para la funcion recono95 <> recono95  (sencible a mayusculas)
	}
</script>
<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Reporte 2 de control de votaciones.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Septiembre 30 DE 2009.                                                                                
//FECHA ULTIMA ACTUALIZACION  :30 de Septiembre de 2009.                                                                             
//DESCRIPCION			      :Quienes han votado y quienes faltan por votar

$wactualiz="Ver. 2009-09-28";

session_start();
if(!isset($_SESSION['user']))
  echo "error";
else
{
    
    
	echo "<form name='recono95' action='recono95.php' method=post>";  

	// Este programa se ejecuta en matrix recibiendo un parametro o desde el browser asi:
	// http://localhost/reconocimientos/recono95.php?tabla=recono_000001

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
	$query="Select percco,percod,cconom from noper,cocco where percco=ccocod and peretr = 'A' order by cconom";
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
		    echo "<td><a href='recono95b.php?ccosto=".$ccoant."&tabla=".$tabla."'>Detallar</a></td>";   //Adiciono una columna
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

      //odbc_close($conexN); 
	  Mysql_close($conex); 
    
    //Para que refresque cada 5 minutos con el mismo centro de costos seleccionado y el parametro inicial tabla
    echo "<meta http-equiv='refresh' content='300;url=recono95.php?wcco=".$wcco."&tabla=".$tabla."'>";
}
	
	odbc_close($conexN);
	odbc_close_all();
} // De la sesion
?>
</BODY>
</html>
