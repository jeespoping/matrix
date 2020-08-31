<HTML>
<HEAD>
<TITLE>Consolidado general del programa Inst Mujer SOM</TITLE>
</HEAD>
<BODY>

 <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    
  
<?php
include_once("conex.php");
/*
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
*/
function UltimoDia($anho,$mes)
{ 
   if (((fmod($anho,4)==0) and (fmod($anho,100)!=0)) or (fmod($anho,400)==0)) { 
       $dias_febrero = 29; 
   } else { 
       $dias_febrero = 28; 
   }
    
   switch($mes) { 
       case "01": return 31; break; 
       case "02": return $dias_febrero; break; 
       case "03": return 31; break; 
       case "04": return 30; break; 
       case "05": return 31; break; 
       case "06": return 30; break; 
       case "07": return 31; break; 
       case "08": return 31; break; 
       case "09": return 30; break; 
       case "10": return 31; break; 
       case "11": return 30; break; 
       case "12": return 31; break; 
   } 
} 



mysql_select_db("matrix") or die("No se selecciono la base de datos");    
//Conexion a Informix Creada en el "DSN de Sistema"
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");  

//Forma
echo "<form name='paf03som' action='paf03som.php' method=post>";  
 
 if (!isset($wfec1) or !isset($wfec2))
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Consolidado general del programa Inst Mujer SOM<br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo en el primer dia del mes actual con formato aaaa-mm-dd
  {
    $hoy = date("Y-mm-dd");
    $wfec1=substr($hoy,0,4)."-".substr($hoy,5,2)."-01";
  } 
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en el ultimo dia del mes actual con formato aaaa-mm-dd
  {
    $hoy = date("Y-mm-dd");
    $wfec2=substr($hoy,0,4)."-".substr($hoy,5,2)."-".UltimoDia( substr($hoy,0,4),(substr($hoy,5,2) ) );
  }  
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final  <br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' readonly='readonly' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit osea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos
 {
	echo "<center><table border=0>";
	
    //Generare archivo plano en esta ruta
   	$ruta=  "../archivos";   
   	// ./ para que lo genere en un subdirectorio apartir de la ruta donde estan los fuentes
   	// $archivo = fopen($ruta."/pafsura04.txt","w"); 
	$archivo = fopen("paf03som.txt","w"); 
	
	$query="SELECT id,paffec,pafced,pafape,pafnom,pafexa FROM  pafsom_000001"
	      ." Where paffec between '".$wfec1."' AND '".$wfec2."'"
		  ." Order By id";	

    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    $i = 1;
    While ( $i <= $nroreg )
    { 
      $registro = mysql_fetch_row($resultado);
      $query = "SELECT nombre FROM root_000012 Where codigo = '".$registro[5]."'"; 
      $resultado2 = mysql_query($query);
      $registro2 = mysql_fetch_row($resultado2);
	 
	  /*COMO AL DAR LA CITA LA CODIFICACION DEL EXAMEN NO ES CUPS, ENTONCES LA BUSCO HOMOLOGOGANDO POR NOMBRE
		EC0001	ECOCARDIOGRAFIA Y ECOCARDIOGRAMA
		EL0001	ECO ESTRES CON EJERCICIO
		EC0009	ECO CONVENCIONAL HOSP
		HO0001	HOLTER
		MA0001	MONITOREO
		PE0001	PRUEBA
		EK0001	ELECTROCARDIOGRAMA
      */
	  
	  $query="";
      $pos = strpos($registro2[0], "ECOCARDIOGRA");	 
      if ($pos !== false)  // Se encontro el substring
	  { // Busco las citas con codigo que empiece por "EC"
        $query ="SELECT cod_equ,cod_exa,fecha,Hi,Hf,Edad,fecsol from citasom_000009 where Activo='A' And cedula='".$registro[2]."'"
              ." And Fecha >= '".$registro[1]."' "; 
      }
	  else
	  {	  
		$pos = strpos($registro2[0], "ECO ");	 
        if ($pos !== false)  // Se encontro el substring
		{ // Busco las citas con codigo que empiece por  "EL" o "EC"
		  $query ="SELECT cod_equ,cod_exa,fecha,Hi,Hf,Edad,fecsol from citasom_000009 where Activo='A' And cedula='".$registro[2]."'"
                 ." And Fecha >= '".$registro[1]."'  ";  	
	    } 		 
		else
		{	
      	 $pos = strpos($registro2[0], "HOLTER");	 
         if ($pos !== false)  // Se encontro el substring
		 { // Busco las citas con codigo que empiece por  "HO"
		   $query ="SELECT cod_equ,cod_exa,fecha,Hi,Hf,Edad,fecsol from citasom_000009 where Activo='A' And cedula='".$registro[2]."'"
                  ." And Fecha >= '".$registro[1]."'  "; 
		 }		 
		 else
		 {	 
       	  $pos = strpos($registro2[0], "MONITOREO");	 
          if ($pos !== false)  // Se encontro el substring
		  { // Busco las citas con codigo que empiece por  "MA"
      		   $query ="SELECT cod_equ,cod_exa,fecha,Hi,Hf,Edad,fecsol from citasom_000009 where Activo='A' And cedula='".$registro[2]."'"
                      ." And Fecha >= '".$registro[1]."'  ";
		  }
		  else
		  {  
       	   $pos = strpos($registro2[0], "PRUEBA");	 
           if ($pos !== false)  // Se encontro el substring
		   { // Busco las citas con codigo que empiece por  "PE"
      		    $query ="SELECT cod_equ,cod_exa,fecha,Hi,Hf,Edad,fecsol from citasom_000009 where Activo='A' And cedula='".$registro[2]."'"
                       ." And Fecha >= '".$registro[1]."'  ";  
		   }
		   else
		   {	   
        	$pos = strpos($registro2[0], "ELECTROCARDIO");	 
            if ($pos !== false)  // Se encontro el substring
			{ // Busco las citas con codigo que empiece por  "EK"
     		  $query ="SELECT cod_equ,cod_exa,fecha,Hi,Hf,Edad,fecsol from citasom_000009 where Activo='A' And cedula='".$registro[2]."'"
                     ." And Fecha >= '".$registro[1]."'  ";  
			}
		   }	
		  }	   
	     }
		}
	   }

	   if ($query !== "")
	   {
	    $resultado2 = mysql_query($query);
	    $nr = mysql_num_rows($resultado2);
	    if ($nr > 0)   // Encontro Cita
	    {
   
        $registro2 = mysql_fetch_row($resultado2); 
		//id,paffec,pafced,pafape,pafnom,pafexa
        $LineaDatos = $registro[0].chr(9).$registro[1].chr(9).$registro[2].chr(9).$registro[3].chr(9).$registro[4].chr(9).$registro[5].chr(9)
		//cod_equ,cod_exa,fecha,Hi,Hf,Edad,fecsol
                     .$registro2[0].chr(9).$registro2[1].chr(9).$registro2[2].chr(9).$registro2[3].chr(9).$registro2[4].chr(9).$registro2[5].chr(9)."ENCONTRO***";
        }
	    else
         $LineaDatos = $LineaDatos = $registro[0].chr(9).$registro[1].chr(9).$registro[2].chr(9).$registro[3].chr(9).$registro[4].chr(9).$registro[5].chr(9);
	   	
	    fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
	   }	
       $i++; 		
    }
 
	fclose($archivo);
    Mysql_close($conex); 
	//Si en el href lo hago a la variable $ruta me mostrara todos los
	//archivos generados alli, pero si $ruta tiene el path completo
	//con el archivo generado lo bajaria directamente y no mostraria
	//otros archivos
	//echo "<li><A href='".$ruta."/socios03.txt"."'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
	echo "<li><A href='paf03som.txt' >Presione Clic Derecho Para Bajar El Archivo ...</A>";
    echo "<br>";
    echo "<li>Registros generados: ".($i-1);   

	  
    echo "</table>";
 }   
//mysql_close(); 
echo "</BODY>";
echo "</HTML>";	

//odbc_close($conexN);
//odbc_close_all();

?>
