<html>
<head>
<title>Reporte de como van las votaciones para el programa de reconocimientos</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.recono95b.submit();   // Ojo para la funcion recono95b <> recono95b  (sencible a mayusculas)
	}
</script>
<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Reporte 2 de control de votaciones.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Septiembre 30 DE 2009.                                                                                
//FECHA ULTIMA ACTUALIZACION  :30 de Septiembre de 2009.                                                                             
//DESCRIPCION			      :quienes faltan por votar

$wactualiz="Ver. 2009-09-30";


session_start();
if(!isset($_SESSION['user']))
  echo "error";
else
{
    
    
	echo "<form name='recono95b' action='recono95b.php' method=post>";  

	// Este programa se ejecuta en matrix recibiendo un parametro o desde el browser asi:
	// http://localhost/reconocimientos/recono95b.php?tabla=recono_000001

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
   echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b> PERSONAS QUE FALTAN POR VOTAR </b></font></td></tr>";
   echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$titulo."</b></font></td></tr>";


	
	echo "<br><br>";
    echo "<center><table border=0>";
    echo "<tr><td align=center colspan=4 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>".$wactualiz."</b></font></td></tr>";
    echo "<tr>";
    echo "<td>CODIGO</td>";
    echo "<td>NOMBRE</td>";
    echo "</th>";
    echo "<tr>";
    //PERSONAS APTAS PARA VOTAR POR CCOSTO 
	//$query="Select ccovot,codemp,cconom from votset,cocco where ccovot=ccocod order by ccovot,codemp";  //Asi fue para el año 2007
	$query="Select percco,percod,perap1,perap2,perno1,perno2 from noper where peretr = 'A' and percco = '".$ccosto."' order by perap1";
    $resultado = odbc_do($conexN,$query); 
    $i=1;    //Para amanejar el color del fondo
    
  	while (odbc_fetch_row($resultado))
	{   	
		$query = "Select votcod FROM ".$tabla." Where votcod = '".odbc_result($resultado,2)."'";

	    $resultado2 = mysql_query($query,$conex);
	    $nroreg = mysql_num_rows($resultado2);
	    if ($nroreg == 0 )    // NO HA Votado 
        {   
	        // color de fondo  
	        if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	         $wcf="DDDDDD";  
	   	    else
	   	     $wcf="CCFFFF";    
	        echo "<tr bgcolor=".$wcf.">";
			echo "<td>".odbc_result($resultado,2)."</td>";
		    echo "<td>".odbc_result($resultado,3)." ".odbc_result($resultado,4)." ".odbc_result($resultado,5)." ".odbc_result($resultado,6)."</td>"; 
		    $i++;
        }   	        	 
    }   

      odbc_close($conexN); 
	  Mysql_close($conex); 
    

} // De la sesion
?>
</BODY>
</html>
