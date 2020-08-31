<HTML>
<HEAD>
<TITLE>PACIENTES ATENDIDOS POR MEDICO EN EL SERVICIO</TITLE>
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
 
 // Dos variables para totales generales
 $total=0;
 $totala=0;

 //Forma
 echo "<form name='pacatendxmed' action='pacatendxmed.php' method=post>";  
 
 if (!isset($wfec1) or $wfec1=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
	echo "<input type='HIDDEN' name=wcco value='".$wcco."'>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>PACIENTES ATENDIDOS POR MEDICO EN EL SERVICIO DE URGENCIAS<br></font></b>";   
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
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>PACIENTES POR MEDICO EN URGENCIAS</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: pacatendxmed.php Ver. 2014/11/17<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b><b>CODIGO</td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>NOMBRE<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>PACIENTES<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>ACTIVOS<b></td>";
    echo "</tr>"; 

    $query="Select mtrmed,Descripcion,count(*) From hce_000022  LEFT JOIN usuarios "
        . " ON mtrmed=Codigo"
        . " WHERE mtrcci= '".$wcco."'"
        . " And Fecha_data between '".$wfec1."' and '".$wfec2."'"
        . " GROUP BY mtrmed,Descripcion ORDER BY 3 DESC";

    $resultado = mysql_query($query,$conex) or die("ERROR EN QUERY1");             // Ejecuto el query 
    $nroreg = mysql_num_rows($resultado);
		
	$n = 0;
    while ($n < $nroreg)       		
	 {         
	     $registro = mysql_fetch_row($resultado);	 // Lee registro	
	     $n++;
	     $total=$total+$registro[2];
		 
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    	
	    
	   	 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";
         echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
		 
		 $query="Select count(*) activos From hce_000022,movhos_000018"
         . " WHERE mtrcci = '".$wcco."'   "
         . " And Mtrmed='".$registro[0]."'"
         . " And hce_000022.Fecha_data between '".$wfec1."' and '".$wfec2."'"
         . " And Mtrhis = ubihis "
         . " And Mtring = ubiing "
         . " And ubiald = 'off' ";	 
         $resultado2 = mysql_query($query,$conex) or die("ERROR EN QUERY2");             // Ejecuto el query 
		 $wactivos=mysql_fetch_row($resultado2);                                        // Lee Registro
         $totala=$totala+$wactivos[0];
         echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$wactivos[0]."</td>";
		 echo "</tr>";  
		          
      }
     echo "</table>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total Pacientes: ".$total."</font></b><br>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total Pacientes Activos: ".$totala."</font></b><br>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total Medicos: ".$n."</font></b><br>";

 }   

echo "</BODY>";
echo "</HTML>";	

?>