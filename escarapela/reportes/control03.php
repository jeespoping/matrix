<html>
<head>
<title>Reporte control de ingreso de empleados</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.control03.submit();   // Ojo para la funcion control03 <> Control03  (sencible a mayusculas)
	}
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Reporte de control de ingreso de empleados.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :JUNIO 26 DE 2007.                                                                                
//FECHA ULTIMA ACTUALIZACION  :11 de Diciembre de 2007.                                                                             
//DESCRIPCION			      :Este programa genera un reporte para los cordinadores de area por CCosto y empleado
//==========================================================================================================================================

$wactualiz="Ver. 2007-12-10";

//session_start();
//if(!isset($_SESSION['user']))
//  echo "error";
//else
//{
	
	
	
	
	mysql_select_db("matrix") or die("No se selecciono la base de datos matrix en Mysql");    


echo "<center><table border=1>";
echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b> REPORTE DE CONTROL DE INGRESO DE EMPLEADOS </b></font></tr>";
echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

echo "<form name='control03' action='control03.php' method=post>";  

if (!isset($wcco) or $wcco=='' or !isset($wemp) or $wemp=='' or !isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
   // Lleno un ComboBox con los centros de costos de la tabla cocco de informix
   // y dependiendo de la seleccion con la Funcion enter() muestro otro Combo
   // con los empleados asignados a este centro de costos.
   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> C.Costos: <br></font></b>";
   
   // $query = "SELECT ccocod,cconom "
   //      ."   FROM costosyp_000005"
   //      ."  ORDER BY cconom";
	       
    $query = "SELECT ccocod,ccodes "
	       ."   FROM clisur_000003"
	       ."  ORDER BY ccodes";	       
	       
    $resultado = mysql_query($query,$conex); 
	$nroreg = mysql_num_rows($resultado);  
	
	//Defino un ComboBox con los centros de costo del query anterior maneja AutoEnter
	echo "<select name='wcco' onchange='enter()'>";
	echo "<option></option>";                       //primera opcion en blanco 	
		
    $Num_Filas = 0;
	while ($Num_Filas <= $nroreg)
	  {
		if(substr($wcco,0,strpos($wcco,"-")) == mysql_result($resultado,$Num_Filas,0))
	      echo "<option selected>".mysql_result($resultado,$Num_Filas,0)."-".mysql_result($resultado,$Num_Filas,1)."</option>";
	    else
	      echo "<option>".mysql_result($resultado,$Num_Filas,0)."-".mysql_result($resultado,$Num_Filas,1)."</option>";
	      
	    $Num_Filas++;
      }   
    echo "</select></td>";
    
    
   if (isset($wcco))
   {
    //Con explode parto en una arreglo el campo 'wcco' capturado en el <option> cada guion '-'
    $w=explode('-',$wcco); 
    if ($wcco != "")    // Solo si se escoje un C.Costos pide el combo de empleados
    {
      // Lleno un combo con los empleados del C.costos seleccionado
      echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Empleados: <br></font></b>";
   
      $ccosto = substr($wcco,1,4);
   
      $query = "SELECT Codigo,Descripcion FROM usuarios";
	  $query = $query." WHERE Ccostos = '".$w[0]."'";
	  $query = $query." ORDER BY Descripcion";
      $resultado = mysql_query($query,$conex); 
	  $nroreg = mysql_num_rows($resultado);  
	
	  echo "<select name='wemp'>";
	  echo "<option>TODOS</option>";
      $Num_Filas = 0;
	  while ($Num_Filas <= $nroreg)
	  {
	    echo "<option value='".mysql_result($resultado,$Num_Filas,0)."-".mysql_result($resultado,$Num_Filas,1)."'>".mysql_result($resultado,$Num_Filas,0)."-".mysql_result($resultado,$Num_Filas,1)."</option>";
	    $Num_Filas++;
      }
      echo "</select></td>";
    }
   } 
   
   //Con explode genero un arreglo donde cada posicion es el campo wemp partido cada guion '-'
   if (isset($wemp))
    $e=explode('-',$wemp); 
    
    if (!isset($fec1))      // Si la variable no ha sido seteada
      $fec1 = date("Y-m-d")." 00:00:00";
    if (!isset($fec2))      // Si la variable no ha sido seteada
      $fec2 = date("Y-m-d")." 23:59:59";
      
    echo "<tr><td align=center colspan=3 bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fecha Inicial (aaaa-mm-dd hh:mm:ss):</font></b><INPUT TYPE='text' NAME='fec1' VALUE='".$fec1."'>
	<b><font text color='#003366' size='2'> <i>Fecha Final (aaaa-mm-dd hh:mm:ss):</font></b><INPUT TYPE='text' NAME='fec2' VALUE='".$fec2."'></td></tr>";

   	echo "<tr><td align=center colspan=3 bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";          
   	echo "</tr>";
	echo "</center></table>";
}
else  ///////////              Cuando ya estan capturados todos los datos      ///////////////////////////
{
	echo "<center><table border=0>";
    echo "<tr><td align=center colspan=3 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>PROMOTORA MEDICA LAS AMERICAS</b></font></td></tr>";
	echo "<tr><td align=center colspan=3 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>Centro de Costos: <i>".$wcco."</i></td></tr>";
    echo "<tr><td align=center colspan=3 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>Empleado: <i>".$wemp."</i></td></tr>";
	echo "<tr><td align=center colspan=3 bgcolor='#FFFFFF'><font size=2 color='#003366'><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";		

    $w=explode('-',$wcco); 
    $e=explode('-',$wemp);   
     
    if ($wemp == "TODOS")
    {
	  $query = "Select horcod,horfec,hortip,horcco FROM escara_000001"
	          ." Where horcco = '".$w[0]."'"
	          ."   and horfec between '".$fec1."' and '".$fec2."'" 
	          ." Order By horcod,horfec";

    }          
    else
    {
	  $query = "Select horcod,horfec,hortip,horcco FROM escara_000001"
	          ." Where horcco = '".$w[0]."'"
	          ."   And horcod = '".$e[0]."'"
	          ."   and horfec between '".$fec1."' and '".$fec2."'" 
	          ." Order By horcod,horfec";
 	} 
    $resultado = mysql_query($query,$conex); 
    $nroreg = mysql_num_rows($resultado);  
    $campos = mysql_num_fields($resultado);
    
	// Para dejar una linea en blanco en la tabla
	// echo "<tr><td alinn=center colspan=3 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";    
	
	// Subtitulos dentro de la tabla
	echo "<tr>";
	echo "<th align=center bgcolor=#006699><font text color=#FFFFFF><b>EMPLEADO</b></font></th>";
	echo "<th align=center bgcolor=#006699><font text color=#FFFFFF><b>FECHA HORA</b></font></th>";
	echo "<th align=center bgcolor=#006699><font text color=#FFFFFF><b>TIPO</b></font></th>";
	echo "</tr>";
	
	$i = 1;	  // Variable para intercalar el color de fondo en la tabla
	
	// PROCESO DE IMPRESION CON ROMPIMIENTO DE CONTROL POR CODIGO DE EMPLEADO
    $codant = "";
    $Num_Filas = 0;
    while ($Num_Filas < $nroreg)
//  while (odbc_fetch_row($resultado))
	{
		if ($codant != mysql_result($resultado,$Num_Filas,0))
		{
		 // Actualizo la variable anterior
	     $codant =mysql_result($resultado,$Num_Filas,0);
	     
		 // imprimo subtitulos
  		 $l = strlen($codant);
  	   //if ($l <= 6)  
  		 if ($l <= 8)        
         {
          echo "<tr>";  	
	      echo "<td>".$codant."</td>";
    	  $query = "SELECT Codigo,Descripcion FROM usuarios";
	      $query = $query." WHERE Codigo = '".$codant."'";
	      $resultado2 = mysql_query($query,$conex);   
	      //Tomo el nombre
	      $nombre=mysql_result($resultado2,0,1);
	      echo "<td>".$nombre."</td>";
	      echo "</tr>";  
     	 } 
     	 else
     	 {
          echo "<tr>";  	
	      echo "<td>".$codant."</td>";
    	  $query = "SELECT pexced,pexnom FROM escara_000003";
	      $query = $query." WHERE pexced = '".$codant."'";
	      $resultado2 = mysql_query($query,$conex);   
	      echo "<td>".mysql_result($resultado2,0,1)."</td>";
	      echo "</tr>";  	     	  	  
       	 }	  	  
        } 
        
	    // color de fondo  
	    if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	else
	   	  $wcf="CCFFFF";    	
	   	$i++; 	
       
	   	// TENGO UNA FECHA ALMACENADA EN MysqlL COMO timestamp (fecha y hora juntos) O SEA 20071210153633 
	   	// Y LA QUIERO MOSTRAR CON FORMATO 10/12/2007 03:36:33 PM
         $f =mysql_result($resultado,$Num_Filas,1);     
            
        // PRIMERO ES TOMAR ESA FECHA Y PASARLA A SU CORRESPONDIENTE VALOR EN ENTERO
        // $f1 = mktime((integer)substr($f,8,2),(integer)substr($f,10,2),(integer)substr($f,12,2),(integer)substr($f,4,2),(integer)substr($f,6,2),(integer)substr($f,0,4));
        // Y CON LA FUNCION date LA MUESTRO CON EL FORMATO DESEADO 
        // $f2 = date("d/m/Y h:m:s A",$f1);
        // echo "<tr bgcolor=".$wcf."><td align=center>"." "."</td><td align=center>".$f2."</td><td align=center>".mysql_result($resultado,$Num_Filas,2)."</tr>"; 
          
        // Nada en MATRIX funciona derecho 
         echo "<tr bgcolor=".$wcf."><td align=center>"." "."</td><td align=center>".$f."</td><td align=center>".mysql_result($resultado,$Num_Filas,2)."</tr>"; 

	    
	  $Num_Filas++;   
    } // Del While
    echo "</table>"; 
    mysql_close($conex);
    // Para que refresque cada 10 segundos con los mismos datos seleccionados
    echo "<meta http-equiv='refresh' content='10;url=control03.php?wcco=".$wcco."&wemp=".$wemp."&fec1=".$fec1."&fec2=".$fec2."'>";
}
//}
?>
</BODY>
</html>
