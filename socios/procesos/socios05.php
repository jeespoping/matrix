<HTML>
<HEAD>
<TITLE>Reporte de Socios que cumplen años en un periodo... </TITLE>
</HEAD>
<BODY>

    
<?php
include_once("conex.php");
include_once("root/comun.php");
$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
$wactualiz = "2011/12/09";
encabezado( "Reporte de Socios que cumplen años en un periodo", $wactualiz, $institucion->baseDeDatos );
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
          



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

 //Forma
 echo "<form name='socios05' action='socios05.php?wemp_pmla=".$wemp_pmla."' method=post>"; 
 echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
 
 if (!isset($wmes) or $wmes=='')
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>GENERAR SOCIOS QUE CUMPLEN AÑOS EN UN PERIODO<br></font></b>";   
	echo "</tr>";

/*	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec1=date("Y-m-d");
    
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec2=date("Y-m-d");
    
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final  <br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' readonly='readonly' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
*/
  echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Mes a procesar<br></font></b>";   
  echo "<select name='wmes'>"; 
  echo "<option></option>";
  echo "<option>1- Enero</option>";
  echo "<option>2- Febreo</option>"; 
  echo "<option>3- Marzo</option>";
  echo "<option>4- Abril</option>";
  echo "<option>5- Mayo</option>";
  echo "<option>6- Junio</option>";
  echo "<option>7- Julio</option>";
  echo "<option>8- Agosto</option>";
  echo "<option>9- Septiembre</option>";
  echo "<option>10-Octubre</option>";
  echo "<option>11-Noviembre</option>";
  echo "<option>12-Diciembre</option>";
  echo "</select>";
	
	
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit osea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos
 {
	$mes=explode('-',$wmes); 
    echo "<center><table border=0>";
    //echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Reporte de Socios que cumplen años en un periodo</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Periodo: ".$wmes."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: socios05.php Ver. 2011/12/09<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cedula<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Nombre<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Fecha Nac/to<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Profesion<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Direccion<b></td>";
    echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Ciudad<b></td>";
    echo "</tr>"; 
    
    $query="SELECT socced,socap1,socap2,socnom,socnac,prfdes,socdir,nombre "
    ." FROM socios_000001 LEFT JOIN socios_000003 ON socced=proced LEFT JOIN socios_000007 ON propro=prfcod LEFT JOIN root_000006 ON socmun=root_000006.codigo  "
    ."  WHERE socact='A' AND MONTH(socnac)=".$mes[0]
    ."  ORDER BY DAY(socnac)";
 /*
    $query="SELECT socced,socap1,socap2,socnom,socnac,nombre,descripcion,prfdes"
          ." FROM socios_000001,socios_000003,socios_000007,root_000006,root_000002" 
          ." WHERE socced=proced AND propro=prfcod AND socmun=root_000006.codigo"
          ."   AND socdep=root_000002.codigo "
          ."   AND MONTH(socnac)=".$mes[0]
          ."   AND socact='A' "
          ."   ORDER BY DAY(socnac)";
 */ 
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);

		$i = 1;
		While ($i <= $nroreg)
		{		
	     // color de fondo  
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    	
	 
		 $registro = mysql_fetch_row($resultado);  			
		 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";
         $wnom=$registro[1]." ".$registro[2]." ".$registro[3];
		 echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$wnom."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[4]."</td>";
 		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[5]."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[6]."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[7]."</td>";
		 echo "</tr>";           
          $i++; 
	    }		
 
     echo "</table>";
     echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>Total Cumpleaños del periodo: ".($i-1)."</font></b><br>";
 }   
mysql_close(); 
echo "</BODY>";
echo "</HTML>";	

?>