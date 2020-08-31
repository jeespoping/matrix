<script type="text/javascript">
	function cerrarVentana()
	{
	 window.close()
	}
	
	function enter()
	{
		document.forms.ordlab.submit();
	}
</script>
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
 	echo "error";
else
{

//  $user = "1-uvla01";   //Temporal!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!	


	

	
	
	mysql_select_db("matrix") or die("No se selecciono la base de datos");  

    //Busco el ccosto del usuario
	$query = "Select Cjecco,Cjeadm From uvglobal_000030 Where Cjeusu = '".substr($user,2,80)."'";
	$resultado = mysql_query($query);
	$registro = mysql_fetch_row($resultado);  
	$sede = $registro[0];
	$admin= $registro[1];
	
	echo "<FORM name=ordlab action='uvglobal00pru.php' method=post>";
	
echo "<HTML>";

echo "<HEAD>";
echo "<TITLE>BIENVENIDA</TITLE>";
echo "</HEAD>";
echo "<BODY>";
echo "<center><table border=1>";
echo "<tr><td rowspan=1 align=center ><IMG SRC='/matrix/images/medical/pos/logo_uvglobal.png' ></td>";				
echo "</table>";
echo "<br>";
echo "<center><table border=0>";
echo "<tr><td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=4> <i>UNIDAD VISUAL GLOBAL S.A.</font></b><br>";
echo "<tr><td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=4> <i>".$sede."</font></b><br>";
echo "<tr><td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>uvglobal00pru.php Ver. 2008/01/22</font></b><br></tr>";

if ( $admin=='on')
{

 if (!isset($cco) or $cco=='')
 {
 ///////////////////////////////////////////////////////////////////////////////////////// seleccion para el centro de costos o sede
  echo "<tr><td align=CENTER bgcolor=#DDDDDD><b><font text color=#003366><B>Sede:</B></br></font></b><select name='cco' id='searchinput' onchange='enter()'>";  
    
  $query1="SELECT Ccocod, Ccodes "
         ."  FROM uvglobal_000003 "
         ." ORDER BY Ccocod,ccodes";
  
  $err1 = mysql_query($query1,$conex);
  $num1 = mysql_num_rows($err1);
  $Ccostos=explode('-',$cco);
   
  echo "<option>&nbsp</option>";
  for ($i=1;$i<=$num1;$i++)
   {
	$row1 = mysql_fetch_array($err1);
	echo "<option>".$row1[0]."-".$row1[1]."</option>";
   }
  echo "</select></td></tr>";
  echo "</table>";
 }
 else
 {	
 echo "<table>";
 echo "<br>";
 echo "<tr>";
 echo "<td><A HREF='uvglobal01vj.php?wproceso=Nuevo'>Crear Nueva Orden</A></td>";
 echo "<td colspan=8>&nbsp</td>";
 echo "<td><A HREF='uvglobal00pru.php'>Retornar</A></td>";
 echo "</tr>";
 echo "</table>";
 echo "<br>";
 echo "<center><table border=0>";

 echo "<tr><td align=center colspan=8 bgcolor=#DDDDDD><font text color=#003366><b>Ordenes Sede : <i>".$cco."</i></b></font></b></font></td></tr>";

 echo "<tr>";
 echo "<td align=center bgcolor=#DDDDDD><b>Orden Nro<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Fecha<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Cedula<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Nombre<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Fuente<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Factura<b></td>";
 echo "<td align=center bgcolor=#DDDDDD></td>";
 echo "<td align=center bgcolor=#DDDDDD></td>";
 echo "</tr>"; 
   

     //SOLO MOSTRAMOS LAS ORDENES QUE NO SE HAN ENTREGADO Y DEL CENTRO DE COSTOS O SEDE DEL USUARIO
       $query = "SELECT "
       ."ordnro,orddoc,ordran,ordtus,orddsi,orddes,orddci,orddej,orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,"
       ."ordled,ordlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,ordpin,ordde1,ordbra,ordde2,ordter,ordde3,"
       ."ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordffa,ordfac,ordfec,ordfre,ordfen,ordvel,ordvem,ordcco,CLINOM"    
       ." FROM uvglobal_000133,uvglobal_000041 WHERE ordfen = '0000-00-00' And ordcco = '".$cco."' And orddoc = clidoc " 
       ." ORDER by ordnro DESC";
       $resultado = mysql_query($query);
       if ($resultado)
       {
		 $nroreg = mysql_num_rows($resultado);    
		 $numcam = mysql_num_fields($resultado);
	    
		$i = 1;
		While ($i <= $nroreg)
		{		
	     // color de fondo  
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    	
	   	    
		 $registro = mysql_fetch_row($resultado);  			
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";    //Nro de Orden
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[40]."</td>";   //Fecha
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";    //Cedula
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[46]."</td>";   //Nombre
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[38]."</td>";   //Fuente
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[39]."</td>";   //Factura
		 
      
	     echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">";
	     // LLAMADO SIN PARAMETROS
	     // echo "<A HREF='uvglobal01.php'>Nuevo</A></td>";
	     
	     // LLAMADO CON PARAMETROS
	     // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."&wfec=".$registro[1]."&wdoc=".$registro[2]."'>Editar</A></td>";
	     
	     /* SIN EMBARGO COMO EN ESTE CASO SON 45 CAMPOS QUE TENGO QUE ENVIAR COMO PARAMETROS, ENTONCES SI TENGO
	        LA PRECAUCION DE DAR NOMBRES DE LOS CAMPOS EN LA TABLA ASI:       ordnro,orddoc,ordran,... Y SI EN LA FORMA
	        UTILIZO COMO NOMBRE DE VARIABLES PARA MANIPULAR ESTOS  CAMPOS:      wnro,  wdoc,  wran,...
	        ARMO MEDIANTE UN STRING UN href TOMANDO LOS ULTIMOS TRES CARACTERES DE LOS NOMBRES DE LOS CAMPOS
	     */
	        $l="<A HREF='uvglobal01vj.php?w".substr(mysql_field_name($resultado,0),3)."=".$registro[0];
	        for ($j=1;$j<=$numcam-1;$j++)
	          $l = $l."&w".substr(mysql_field_name($resultado,$j),3)."=".$registro[$j];
	          
	        $l = $l."&wproceso=Modificar'>Editar</A></td>";  //Adiciono un columna adicional para indicar que voy A "Modificar"	 
	        
	        $l = $l."<td align=center color=#FFFFFF bgcolor=".$wcf.">";     // Otra que llame el programa que imprime  
	        $l = $l."<A HREF='uvglobal02.php?wnro=".$registro[0]."'>Imprimir</A></td>";
	        
	        echo $l;                                                              
            echo "</tr>";
          // OTRA FORMA SERIA ENVIAR SOLO LOS CAMPOS CLAVES EN ESTE CASO ordnro Y EL PROGRAMA LLAMADO EMPIEZO HACIENDO
          // UN SELECT PARA LLENAR LAS VARIABLES 
 	      // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."'>Editar</A></td>";
 	              
          $i++; 
	    }		
       }
    }
 }    
 else
 {
 echo "<table>";
 echo "<br>";
 echo "<tr>";
 echo "<td><A HREF='uvglobal01vj.php?wproceso=Nuevo'>Crear Nueva Orden</A></td>";
 echo "<td colspan=8>&nbsp</td>";
 echo "<td><A HREF='uvglobal00pru.php'>Retornar</A></td>";
 echo "</tr>";
 echo "</table>";
 echo "<br>";
 echo "<center><table border=0>";

 echo "<tr><td align=center colspan=8 bgcolor=#DDDDDD><font text color=#003366><b>Ordenes Sede : <i>".$sede."</i></b></font></b></font></td></tr>";

 echo "<tr>";
 echo "<td align=center bgcolor=#DDDDDD><b>Orden Nro<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Fecha<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Cedula<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Nombre<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Fuente<b></td>";
 echo "<td align=center bgcolor=#DDDDDD><b>Factura<b></td>";
 echo "<td align=center bgcolor=#DDDDDD></td>";
 echo "<td align=center bgcolor=#DDDDDD></td>";
 echo "</tr>"; 
   

     //SOLO MOSTRAMOS LAS ORDENES QUE NO SE HAN ENTREGADO Y DEL CENTRO DE COSTOS O SEDE DEL USUARIO
       $query = "SELECT "
       ."ordnro,orddoc,ordran,ordtus,orddsi,orddes,orddci,orddej,orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,"
       ."ordled,ordlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,ordpin,ordde1,ordbra,ordde2,ordter,ordde3,"
       ."ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordffa,ordfac,ordfec,ordfre,ordfen,ordvel,ordvem,ordcco,CLINOM"    
       ." FROM uvglobal_000133,uvglobal_000041 WHERE ordfen = '0000-00-00' And ordcco = '".$sede."' And orddoc = clidoc " 
       ." ORDER by ordnro DESC";
       $resultado = mysql_query($query);
       if ($resultado)
       {
		 $nroreg = mysql_num_rows($resultado);    
		 $numcam = mysql_num_fields($resultado);
	    
		$i = 1;
		While ($i <= $nroreg)
		{		
	     // color de fondo  
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    	
	   	    
		 $registro = mysql_fetch_row($resultado);  			
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";    //Nro de Orden
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[40]."</td>";   //Fecha
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";    //Cedula
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[46]."</td>";   //Nombre
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[38]."</td>";   //Fuente
		 echo "<td align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[39]."</td>";   //Factura
		 
      
	     echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">";
	     // LLAMADO SIN PARAMETROS
	     // echo "<A HREF='uvglobal01.php'>Nuevo</A></td>";
	     
	     // LLAMADO CON PARAMETROS
	     // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."&wfec=".$registro[1]."&wdoc=".$registro[2]."'>Editar</A></td>";
	     
	     /* SIN EMBARGO COMO EN ESTE CASO SON 45 CAMPOS QUE TENGO QUE ENVIAR COMO PARAMETROS, ENTONCES SI TENGO
	        LA PRECAUCION DE DAR NOMBRES DE LOS CAMPOS EN LA TABLA ASI:       ordnro,orddoc,ordran,... Y SI EN LA FORMA
	        UTILIZO COMO NOMBRE DE VARIABLES PARA MANIPULAR ESTOS  CAMPOS:      wnro,  wdoc,  wran,...
	        ARMO MEDIANTE UN STRING UN href TOMANDO LOS ULTIMOS TRES CARACTERES DE LOS NOMBRES DE LOS CAMPOS
	     */
	     
	     
	        $l="<A HREF='uvglobal01vj.php?w".substr(mysql_field_name($resultado,0),3)."=".$registro[0];
	        for ($j=1;$j<=$numcam-1;$j++)
	          $l = $l."&w".substr(mysql_field_name($resultado,$j),3)."=".$registro[$j];
	          
	        $l = $l."&wproceso=Modificar'>Editar</A></td>";  //Adiciono un columna adicional para indicar que voy A "Modificar"
	        
	        echo $l;	 
	        
	        $l = "";
	        $l = $l."<td align=center color=#FFFFFF bgcolor=".$wcf.">";     // Otra que llame el programa que imprime  
	        $l = $l."<A HREF='uvglobal02.php?wnro=".$registro[0]."'>Imprimir</A></td>";
	        
	        echo $l;                                                              
            echo "</tr>";
          // OTRA FORMA SERIA ENVIAR SOLO LOS CAMPOS CLAVES EN ESTE CASO ordnro Y EL PROGRAMA LLAMADO EMPIEZO HACIENDO
          // UN SELECT PARA LLENAR LAS VARIABLES 
 	      // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."'>Editar</A></td>";
 	              
          $i++; 
	    }		
       }
    } 
 	
 
echo "<tr>";    
echo "<td><A HREF='uvglobal00pru.php'>Retornar</A></td>";
echo "</tr>";
echo "<tr><td align=center colspan=8><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    
echo "</table>";
echo "</BODY>";
echo "</HTML>";
echo "</form>";	


}    // De la sesion
?>


