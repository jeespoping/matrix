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

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

 

 mysql_select_db("matrix") or die("No se selecciono la base de datos");    
 
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
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: ringxhor.php Ver. 2014/11/19<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b><b>HORA</td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>CANTIDAD<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>% PART.<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>ACTIVOS<b></td>";
    echo "</tr>";    

   	//  ./ para que lo genere en un subdirectorio apartir de la ruta donde estan los fuentes
   	$archivo = fopen($ruta."Ejecuciones.txt","a+");    
    //$LineaDatos = $registro[0].chr(9).$registro[1].chr(9).$registro[2].chr(9).$registro[3].chr(9).$westadox;
	$fecha = date("Y-m-d H:i:s");
	$LineaDatos = $fecha.chr(9).$user.chr(9).$wfec1.chr(9).$wfec2;
    fwrite($archivo, $LineaDatos.chr(13).chr(10) );       
	fclose($archivo);
    	 
	 $query="Select inghis,ingnin,inghin,ingsei,ubiald"
     ." From cliame_000101,movhos_000018 "
     ." Where ingfei  between '".$wfec1."' and '".$wfec2."'"
     ." And ingsei = '1130' "
     ." And inghis = ubihis "
     ." And ingnin = ubiing ";
		
    $resultado = mysql_query($query,$conex) or die("ERROR EN QUERY");             // Ejecuto el query 
    $nroreg = mysql_num_rows($resultado);

	$n = 0;
     while ($n < $nroreg)                          
	 {
         $registro = mysql_fetch_row($resultado);	 // Lee registro	  
	     $n++; 
		    $whora=$registro[2];
	        $totales[$whora+1] = $totales[$whora+1]+1;      // [$whora+1]   porque el arreglo tiene indice 0
            if ($registro[4] == "off") 
            {
             $totact[$whora+1] = $totact[$whora+1]+1;
             $westadox = "Activo";
            }
            else
             $westadox = "Inactivo";	
     }
     
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
         echo "<td><A HREF='medqaten.php?i=".($i-1)."&wfec1=".$wfec1."&wfec2=".$wfec2."' TARGET='_blank'>Detallar</A></td>";	

		 echo "</tr>";           
      }
     echo "</table>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total General: ".$total."</font></b><br>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total Activos: ".$totala."</font></b><br>";
 }   

echo "</BODY>";
echo "</HTML>";	

?>