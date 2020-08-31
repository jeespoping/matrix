<HTML>
<HEAD>
<title>MATRIX - [REPORTE CITAS DE PACIENTES POR MEDICO]</title>
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

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

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

//Forma
echo "<form name='rep_citasim' action='rep_citasim.php' method=post>";  

  
 if (!isset($wfec1) or !isset($wfec2))
 {
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Reporte de Citas Instituto de la Mujer<br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec1 = date("Y-m-d");
   
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en la misma Inicial
    $wfec2=$wfec1;
  
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final  <br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' readonly='readonly' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
/*****/	
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Medico:</font></b><br>";   
   $query = "SELECT Codigo,Nombre FROM citasim_000008 where Activo = 'A' Order By Codigo"; 	         
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);

   echo "<select name='wmed'>"; 
   echo "<option>%%%-Todos</option>"; 
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c4=explode('-',$wmed); 				  
  	  if( trim($c4[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>"; 
	  else
	    echo "<option>".$registroB[0]."- ".$registroB[1]." </option>"; 
	  $i++; 
   }   
   echo "</select></td>";   

/****/	
		
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit o sea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos
 {
	echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Reporte de Citas Medicos</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: rep_citasim.php Ver. 2019-09-16<br>AUTOR: Gabriel Agudelo</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
    echo "<tr>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=3><b>FECHA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=3><b>NOMBRE PACIENTE</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=3><b>ENTIDAD</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=3><b>MEDICO</b></td>";
    echo "</tr>"; 
	
    // TOMO EL CODIGO DEL MEDICO	
	if (isset($wmed))  
     $c3=explode('-',$wmed);

    $query = " SELECT c1.Fecha,c1.Nom_pac,c2.Descripcion,c8.Nombre"
              ."   FROM citasim_000001 c1,citasim_000002 c2,citasim_000008 c8  "
              ."  WHERE c1.Fecha between '".$wfec1."' and '".$wfec2."'"
              ."    AND c1.Cod_med LIKE '".$c3[0]."'"
              ."    AND c1.Activo = 'A' and c1.Asistida = 'on' "
              ."    AND c1.Nit_resp = c2.Nit"
			  ."    AND c1.Cod_med  = c8.Codigo";  

    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    $k = 1;
	//echo "query:".$query;
    While ($k <= $nroreg)
    {   
        if (is_int ($k/2))
		  {
		   $wcf="DDDDDD";  // color de fondo
		  }
		 else
		  {
		   $wcf="CCFFFF"; // color de fondo
		  }
		 $row1 = mysql_fetch_row($resultado);  	 // Leo registro
		 echo "<Tr bgcolor=".$wcf.">";
         echo "<td align=center><font size=2>".$row1[0]."</font></td>";
         echo "<td align=center><font size=2>".$row1[1]."</font></td>";
         echo "<td align=center><font size=2>".$row1[2]."</font></td>";
		  echo "<td align=center><font size=2>".$row1[3]."</font></td>";
    	 echo "</tr>";
		 $k = $k + 1;
		
	} 
	echo "</table>";
	
 }  
echo "</Form>"; 
echo "</BODY>";
echo "</HTML>";	

?>
