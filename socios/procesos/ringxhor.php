<HTML>
<HEAD>
<TITLE>INGRESO DE PACIENTES AL SERVICIO DE URGENCIAS POR HORAS</TITLE>
</HEAD>
<BODY>
  <!-- Estas 5 lineas es para que funcione el Calendar al capturar fechas -->
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
 
;
 $conexN = odbc_connect('informix','','') or die("No se realizo Conexion con la BD en Informix");
 //Defino dos arreglos
 $totales = array(); 
 $totact = array(); 
 // Inicializo los arreglos en 0 por ser para llevar totales de acumulados
 for($i=1;$i<=24;$i++) 
 {
  $totales[$i]=0;
  $totact[$i]=0;
 }
 // Dos variables para totales generales
 $total=0;
 $totala=0; 
  
 //Forma
 echo "<form name='ringxhor' action='ringxhor.php' method=post>";  
 
 if (!isset($wfec1) or $wfec1=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>INGRESO DE PACIENTES AL SERVICIO DE URGENCIAS POR HORAS<br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec1=date("Y-m-d");
    
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en el ultimo dia del mes actual con formato aaaa-mm-dd
    $wfec2=date("Y-m-d");
  
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
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>INGRESO DE PACIENTES AL SERVICIO DE URGENCIAS POR HORAS</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: ringxhor.php Ver. 2012/07/17<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b><b>HORA</td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>CANTIDAD<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>% PART.<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>ACTIVOS<b></td>";
    echo "</tr>"; 

/*        
     $query="Select egrhoi,'E' AS estado ";
     $query=$query." From inmegr ";
     $query=$query."  Where egring between '".$wfec1."' and '".$wfec2."'"
     $query=$query."  And egrsin='04' ";
     $query=$query." UNION ";
     $query=$query." Select pachor,'A' AS estado";
     $query=$query."  From inpac ";
     $query=$query."  Where pacfec between '".$wfec1."' and '".$wfec2."'"; 
     $query=$query."  And pacser='04' ";
*/  

  	//  ./ para que lo genere en un subdirectorio apartir de la ruta donde estan los fuentes
   	$archivo = fopen($ruta."salida02.txt","w");    
     
     $query="Select pachis,pacnum,pachor,pacser,'A' tipo"
     ." From inpac"
     ." Where pacfec  between '".$wfec1."' and '".$wfec2."'"
     ." UNION"
     ." Select egrhis,egrnum,egrhoi,egrsin,'E' tipo"
     ." From inmegr"
     ." Where egring  between '".$wfec1."' and '".$wfec2."'";

     $resultado = odbc_do($conexN,$query);            // Ejecuto el query 
	 $n = 0;
     while (odbc_fetch_row($resultado))              // Lee registros
	 {  $westadox = "Inactivo";
       if (odbc_result($resultado,4) <> "04")       // No tiene ahora a Urgencias como servicio pero como este lo modifican busco si ingreso por Urgencias
       {   
	      $query="Select logusu,logfec,EXTEND(logfec,hour to hour) From iflog"
	      ." Where logva1='".odbc_result($resultado,1)."' And logva2='".odbc_result($resultado,2)."'"
	      ." And logtab='inpac' And logope='GRABAR' ";
	      $resultado2 = odbc_do($conexN,$query) or die("No se encontro registro en archivo de LOG");           
	      $registro2 = odbc_fetch_row($resultado2);       
	      $wusuario=substr(odbc_result($resultado2,1), 0, 3);  
	      if ($wusuario=="urg")                    // Si Lo grabo un usuario de urgencias acumulo
	      { 
	       $query="Select logusu,logfec From iflog"
	       ." Where logva1='".odbc_result($resultado,1)."' And logva2='".odbc_result($resultado,2)."'"
	       ." And logtab='inpac' And logope='BORRAR/ANULAR' ";
	       $resultado3 = odbc_exec($conexN,$query) or die("No se encontro registro en archivo de LOG");           
           if (odbc_num_rows($resultado3) == 0 )   // Si no fue anulado el ingreso acumulo		                          
	       {		      
		    $n++; 
	        $whora=odbc_result($resultado2,3);
	        $totales[$whora+1] = $totales[$whora+1]+1;
            if (odbc_result($resultado,5) == "A")   // Acumulo en los que estan Activos
            {
             $totact[$whora+1] = $totact[$whora+1]+1;
             $westadox = "Activo";
            }
            $LineaDatos = odbc_result($resultado,1).chr(9).odbc_result($resultado,2).chr(9).odbc_result($resultado,3).chr(9).odbc_result($resultado,4).chr(9).$westadox;
            fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
            
           }  
          }
       }
       else     // Tiene a Urgencias como servicio de ingreso   
       {
         $n++; 
         $i = odbc_result($resultado,3);
         $totales[$i+1] = $totales[$i+1]+1;
         if (odbc_result($resultado,5) == "A") 
         {
          $totact[$i+1] = $totact[$i+1]+1;
          $westadox = "Activo";
         }
         $LineaDatos = odbc_result($resultado,1).chr(9).odbc_result($resultado,2).chr(9).odbc_result($resultado,3).chr(9).odbc_result($resultado,4).chr(9).$westadox;
         fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
         
       }      
     }
      
      fclose($archivo);
      
     /* ACUMULO   */
      for($i=1;$i<=24;$i++) 
      {
        $total = $total + $totales[$i];
        $totala = $totala + $totact[$i];
      }  
             
      for($i=1;$i<=24;$i++) 
      { 
         // color de fondo  
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    	
	    
	   	 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>De ".($i-1)." a ".$i."</td>";
         echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$totales[$i]."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".round((($totales[$i]/$total)*100),2)."%</td>";
 		 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$totact[$i]."</td>";
		 echo "</tr>";           
      }
     echo "</table>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total General: ".$total."</font></b><br>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total Activos: ".$totala."</font></b><br>";
 }   

echo "</BODY>";
echo "</HTML>";	

?>