<HTML>
<HEAD>
<TITLE>Consolidado de Ordenes Programa cardiovascular SURA en un periodo</TITLE>
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
echo "<form name='paf07sura' action='paf07sura.php' method=post>";  
 
 if (!isset($wfec1) or !isset($wfec2))
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Consolidado general del programa cardiovascular SURA en un periodo<br></font></b>";   
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
	
	
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Unidad que presta el servicio ( En blanco para todas ):</font></b><br>"; 
   
   // Para que no me cambien en las unidades este campo, Como no me funciono el 'Disabled' en los campos SELECT porque en el submid me lo borra entonces
   // lo hago segun el cco del usuario en la tabla 99 de cco y prioridad
   $query = "SELECT ccocod,cconom FROM costosyp_000005 WHERE ccoclas = 'PR' AND ccoest = 'on' ORDER BY cconom";   
   echo "<select name='wcco' >"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c3=explode('-',$wcco); 				  
  		if($c3[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";     
	
	
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit o sea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos
 {
	echo "<center><table border=0>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Consolidado de Ordenes Programa Cardiovascular SURA</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: paf07sura.php Ver. 2015/09/09<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Codigo<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Examen<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Codigo<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Servicio<b></td>";	
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Ordenes<b></td>";
	echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Sin Programar<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Programadas<b></td>";
	
    echo "</tr>"; 

	if ( strlen($wcco) < 2 ) 
	  $wccosto="%";
	else
	{  	
      $c1=explode('-',$wcco);   //Tomo el codigo del string del combo  		
	  $wccosto=$c1[0];
    }
	  
	$query="Select pafexa,nombre,pafcco,cconom,count(*)"
          ." From pafsura_000001,root_000012,costosyp_000005"
          ." Where pafexa=codigo"
          ." And pafcco=ccocod"
		  ." And pafcco LIKE '".$wccosto."'" 
		  ." And paffre BETWEEN '".$wfec1."' AND '".$wfec2."'"
		  ." And paffre <= CURDATE()"
          ." And pafest='A'"
          ." Group by pafexa,nombre,pafcco,cconom"
          ." Order by 5 DESC";

    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
	$wsuma1=0;
	$wsuma2=0;
	$wsuma3=0;
    $i = 1;
    While ($i <= $nroreg)
    {
	    $registro = mysql_fetch_row($resultado);  	
        echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>".$registro[0]."</td>";
        echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".$registro[1]."</td>";
        echo "<td colspan=2 align=center bgcolor='CCFFFF'><font text color=#003366 size=3>".$registro[2]."</td>";
        echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".$registro[3]."</td>";
        echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".$registro[4]."</td>";
	   
	    $query="Select count(*) From pafsura_000001"
          ." Where paffre BETWEEN '".$wfec1."' AND '".$wfec2."'"
		  ." And paffre <= CURDATE()"
          ." And pafest='A'"
		  ." And pafexa='".$registro[0]."'"     // Cuantas ordenes de este examen 
		  ." And pafcco='".$registro[2]."'"     // En este Centro de Costo o Servicio 
		  ." And paffci='0000-00-00' ";         // Estan Sin cita 
         
        $resultadoB = mysql_query($query);
        $nroregB = mysql_num_rows($resultadoB);
        if ($nroregB > 0)      //  Encontro 
		{
         $registroB = mysql_fetch_row($resultadoB);  
         $wsinprog =$registroB[0];
		} 
        else
		 $wsinprog=0;
	 
        echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".$wsinprog."</td>";
	    echo "<td colspan=2 align=LEFT   bgcolor='CCFFFF'><font text color=#003366 size=3>".($registro[4] - $wsinprog)."</td>";
		
        echo "</tr>";
		$wsuma1=$wsuma1+$registro[4]; 
		$wsuma2=$wsuma2+$wsinprog; 
		$wsuma3=$wsuma3+($registro[4] - $wsinprog); 
        $i=$i+1;
    }	  
     echo "<td colspan=8 align=center bgcolor='#CC99CC'><font text color=#003366 size=3>TOTAL: </td>";
     echo "<td colspan=2 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=3>".$wsuma1."</td>";
	 echo "<td colspan=2 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=3>".$wsuma2."</td>";
	 echo "<td colspan=2 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=3>".$wsuma3."</td>";
	 
     echo "</tr>";

    echo "</table>";
 }   
mysql_close(); 
echo "</BODY>";
echo "</HTML>";	

?>
