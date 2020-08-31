<html>
<head>
<title>Pasar formularios de HCE a archivo plano en columnas    JairS  19/07/2017 </title>
</head>

<script>

    function ira()
    {
	 document.genhce02.$wfec1.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

  <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) OJO: El calendario NO funciona en programas que se ubiquen en la raiz www -->
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

   // Conexion a Matrix 
    

    mysql_select_db("matrix") or die("No se selecciono la base de datos");    
 
echo "<form name='genhce02' action='genhce02.php' method=post>";

			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>GENERACION DE DATOS DE FORMULARIOS HCE EN ARCHIVO PLANO POR COLUMNAS<b></td></tr>";
			echo "<tr>";

   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>";

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
    $wfec2=substr($hoy,0,4)."-".substr($hoy,5,2)."-01";
  }
    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final  <br></font></b>";
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	   //]]></script>
	<?php
	
	 echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Formularios HCE: </font></b>"; 
	 $query="SELECT encpro,encdes FROM hce_000001 WHERE encpro >= '000051' ORDER BY encpro";
	 $resultado = mysql_query($query);            // Ejecuto el query 	 
	 echo "<select name='wtab' >"; 

	 $i = 1;
     While ($row = mysql_fetch_row($resultado)) 
	  {	  
  		if($wtab == $row[0]."-".$row[1])
	      echo "<option selected>".$row[0]."-".$row[1]."</option>";
	    else
	      echo "<option>".$row[0]."-".$row[1]."</option>"; 
	    $i++; 
      }   
      mysql_free_result($resultado);
     echo "</select>";
	 
     echo "<tr><td bgcolor=#cccccc align=center><input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
     echo "<input type='submit' value='Generar'></td></tr></table>";

 if ( $conf == "on" )      // Ya hay datos seleccionados
 {

       $dato = explode('-',$wtab);  // parto el string capturado en el <Select> y los dejo en un arreglo 
	   $codtabla = $dato[0];         // Codigo de la tabla
	   $nomtabla = "hce_".$dato[0];  // Armo el nombre de la tabla	   
	   
   	   //  ./ para que genere el archivo en un subdirectorio apartir de la ruta donde estan los fuentes
	   //     o asi para que lo genere en la ruta donde estan los fuentes
   	   //  $archivo = fopen("../genhce02.txt","w");
	   
   	   // Abro el archivo
   	   $archivo = fopen("genhce02.txt","w"); 
	   
	   //  Total campos de la tabla
	   $query="SELECT count(*) FROM hce_000002 WHERE detpro = '".$codtabla."' ";
	   $resultado = mysql_query($query);            // Ejecuto el query 
       $registro = mysql_fetch_row($resultado);     // leer siguiente
	   $TotCampos = $registro[0];
	   
	   // Detalle o titulos de los campos de la tabla
	   fwrite($archivo, "Nro de Historia|Nro Ingreso|" ); 
	   $query="SELECT detdes FROM hce_000002 WHERE DETPRO='".$codtabla."' ORDER BY DETCON";
	   $resultado = mysql_query($query);            // Ejecuto el query 
	   $n=1;
	   While ($n <= $TotCampos)
	   {
		$registro = mysql_fetch_row($resultado);     // Leo registro
		fwrite($archivo, $registro[0]."|" );   
		$n++;
	   }
	   fwrite($archivo, chr(13).chr(10) );   
   	   
	  // Proceso que pasa un formulario a Columnas en un archivo plano
	  //    '           0      1     2      3     
      $query="Select movhis,moving,movcon,movdat From ".$nomtabla
      ." Where Fecha_data Between '".$wfec1."' And '".$wfec2."'" 
	  ." and movtip != 'Grid' Order by movhis,moving";
	  
      $resultado = mysql_query($query);            // Ejecuto el query 
      $nroreg = mysql_num_rows($resultado);  
      
	  $registro = mysql_fetch_row($resultado);     // Leo 1er registro
	  $n=0;
	  
      While ($n <= $nroreg)
      {
	    $hisant=$registro[0];
        $ingant=$registro[1];
		$LineaDatos1 = $registro[0]."|".$registro[1]."|";
		$arr[]=array();
		
		$LineaDatos2="";
		
		// Ahora leo los registros hasta que cambie de historia o ingreso
        while   ( ($hisant==$registro[0]) and ($ingant==$registro[1]) and ($n <= $nroreg) )
        {
		   // Eliminar caracter especial y ocultos en el dato
		   $text = str_replace(chr(13).chr(10) , ' ', $registro[3]); 
		   $text = str_replace("\n", ' ', $text); 
		   // El dato lo llevo al arreglo en la posicion que diga movcon ( El consecutivo de campo )
		   $arr[ $registro[2] ] = $text;
	      	
	       $registro = mysql_fetch_row($resultado);   // leer siguiente
	       $n++;
		 }

		 // En un cliclo recorro el arreglo llevandolo a un string separado por | PIPE
		 for ($i = 1; $i <= $TotCampos; $i++)
		   $LineaDatos2=$LineaDatos2.$arr[$i]."|";
	   
		 // Imprimo el string con la historia-Ingreso mas string con los campos
         fwrite($archivo, $LineaDatos1.$LineaDatos2.chr(13).chr(10) );
		 unset($arr);
		 
       }

//  **************************************************************************************************************************************************
        fclose($archivo);
		echo "<li><A href='genhce02.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
       	echo "<br>";
       	echo "<li>Registros generados: ".$nroreg;

 }
echo "</form>";
?>
</body>
</html>