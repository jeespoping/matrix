<HTML>
<HEAD>
<TITLE>Captura datos a graficar</TITLE>
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
    die ("Para entrar correctamente a la aplicacion debe hacerlo por la pagina index.php");

function UltimoDia($anho,$mes)
{ 
   if (((fmod($anho,4)==0) and (fmod($anho,100)!=0)) or (fmod($anho,400)==0)) { 
       $dias_febrero = 29; 
   } else { 
       $dias_febrero = 28; 
   } 
   switch($mes)  
   { 
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

echo "<form name='datosagraficarsura' action='datosagraficarsura.php' method=post>";  

  //Cuerpo de la pagina
  echo "<table align='center' border=0>";
  echo "<tr>";
  echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=4>Generacion de graficos programa cardiovascular en un periodo<br></font></b>";   
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

    echo "</tr>";	
    
    // $wid y $PrimeraVez variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
   	if (isset($wind))
	  echo "<INPUT TYPE = 'hidden' NAME='wind' VALUE='".$wind."'></INPUT>"; 
	else
	  echo "<INPUT TYPE = 'hidden' NAME='wind'></INPUT>"; 
	
   	if (isset($PrimeraVez))
	  echo "<INPUT TYPE = 'hidden' NAME='PrimeraVez' VALUE='".$PrimeraVez."'></INPUT>"; 
	else
	  echo "<INPUT TYPE = 'hidden' NAME='PrimeraVez'></INPUT>"; 
    
      
      echo "<tr><td align=center colspan=4 bgcolor=#C0C0C0>";
   	  echo "<input type='submit' value='Enviar'>";

	if (isset($wfec1) And isset($wfec2))
	{ 
      if (isset($PrimeraVez))  //La primera vez no muestre esta linea
        echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><A HREF='graficos_pafsura.php?wind=".$wind."&wfec1=".$wfec1."&wfec2=".$wfec2."' >Graficar <IMG SRC='Icon_05.ico' ALT='Genera Grafico de los datos'></A></td>";	     
      else
        $PrimeraVez="N";
    }
echo "</table>";
echo "</Form>";
?>
</BODY>
</HTML>
