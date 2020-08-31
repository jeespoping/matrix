<html>
<head>
<title>Informacion academica</title>
</head>

<script>
    function ira()
    {
	 document.socios03.wpro.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

  <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    

    <script type="text/javascript">

	function enter()
	{
		document.forms.socios03.submit();   // Ojo para la funcion socios03 <> socios03  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.socios03.wpro.value = '';
     document.forms.socios03.wuni.value = '';
     document.forms.socios03.wreg.value = '';
     document.forms.socios03.wact.value = '';     
     document.forms.socios03.wfec.value = 'aaaa-mm-dd';
     document.forms.socios03.wdep.value = '';
     document.forms.socios03.wmun.value = '';     
     document.forms.socios03.whom.value = '';
  
    }
    
 	// Fn que solo deja digitar los nros del 0 al 9, el . y el enter
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
 
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Actualiza Informacion Academica.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Agosto 22 de 2011
//FECHA ULTIMA ACTUALIZACION  :Agosto 22 de 2011.                                                                             

$wactualiz="PROGRAMA: socios03.php Ver. 2011-08-22   JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

/**
 * Inserta de manera automatica un input con su respectivo botón para manejar fechas, con valor inicial
 * @param $nombreCampo
 * @return unknown_type
 */

Function validar_datos($pro,$uni,$reg,$fec) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
     
   if (empty($pro))
   {
      $todok = false;
      $msgerr=$msgerr." Debe Seleccionar una Profesion. ";   
   }
                  
   if (empty($uni))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe Seleccionar una Universidad. ";   
   }   

   if (empty($reg))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe existir Nro de registro.";   
   }   

   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( !checkdate(substr($fec,5,2), substr($fec,8,2), substr($fec,0,4)) )
   {
     $todok = false;     
     $msgerr=$msgerr." Fecha de grado debe ser aaaa-mm-dd. ";   
   }                      
     
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  




mysql_select_db("matrix") or die("No se selecciono la base de datos");    



echo "<form name='socios03' action='socios03.php' method=post>";  
		
echo "<center><table border=1>";
echo "<tr><td align=center colspan=3 bgcolor=#99CCCC><font size=3 text color=#FFFFFF><b>Datos de la profesion</b></font></tr>";

//OJO No se porque el parametro $wcod llega con un espacio en blanco al principio entonces trim()
$wcod=trim($wcod); 

if ($windicador == "PrimeraVez") 
{
		
   $query = "SELECT * FROM socios_000003 Where proced='".$wcod."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wpro=$registro[4];
    $wuni=$registro[5];
    $wreg=$registro[6];
    $wact=$registro[7];
    $wfec=$registro[8];
    $wdep=$registro[9];
    $wmun=$registro[10];
    $whom=$registro[11];

  } 
 
} 
else
{// ***** RESPECTO AL STANDARD SE AGREGA ESTE else   ***	
  if ( $windicador == "OtraVez" )
  {
   $query = "SELECT * FROM socios_000003 Where proced='".$wcod."' AND propro='".$wdoc."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wpro=$registro[4];
    $wuni=$registro[5];
    $wreg=$registro[6];
    $wact=$registro[7];
    $wfec=$registro[8];
    $wdep=$registro[9];
    $wmun=$registro[10];
    $whom=$registro[11];

   }
  }
}

   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Profesion:</font></b><br>";   
   
   $query = "SELECT prfcod,prfdes FROM socios_000007 ORDER BY prfdes";   
   echo "<select name='wpro'>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c1=explode('-',$wpro); 			  
  		if($c1[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";
    
   echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Universidad:</font></b><br>";   
   
   $query = "SELECT unicod,unides FROM socios_000008 ORDER BY unides";   
   echo "<select name='wuni'>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c2=explode('-',$wuni); 				  
  		if($c2[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";

    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=3>Nro de Registro:</font></b><br>";
    if (isset($wreg))
     echo "<INPUT TYPE='text' NAME='wreg' size=30 maxlength=15 VALUE='".$wreg."' onKeyUp='form.wreg.value=form.wreg.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wreg' size=30 maxlength=15 onKeyUp='form.wreg.value=form.wreg.value.toUpperCase()'></INPUT></td>"; 
     
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=3>Acta de Grado:</font></b><br>";
    if (isset($wact))
     echo "<INPUT TYPE='text' NAME='wact' size=40 maxlength=15 VALUE='".$wact."' onKeyUp='form.wact.value=form.wact.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wact' size=40 maxlength=15 onKeyUp='form.wact.value=form.wact.value.toUpperCase()'></INPUT></td>"; 

  if (!isset($wfec))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec=date("Y-m-d");
    
    echo "<td bgcolor=#C0C0C0 align=center colspan=2><font text color=#003366 size=3>Fecha de Grado:<br>";
   	$cal="calendario('wfec','1')";
	echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=".$wfec." class=tipo3><button id='trigger4' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Departamento:</font></b><br>";   
   
   $query = "SELECT codigo,descripcion FROM root_000002 ORDER BY descripcion";   
   echo "<select name='wdep'>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB); 
		$c3=explode('-',$wdep); 	 			  
  		if($c3[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";
    
   echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Municipio:</font></b><br>";   
   $query = "SELECT codigo,nombre FROM root_000006 ORDER BY nombre ";   
   echo "<select name='wmun' >"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);
		$c4=explode('-',$wmun); 	  			  
  		if($c4[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";
    
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=3>Homologacion:</font></b><br>";
    if (isset($whom))
     echo "<INPUT TYPE='text' NAME='whom' size=30 maxlength=15 VALUE='".$whom."' onKeyUp='form.whom.value=form.whom.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='whom' size=30 maxlength=15 onKeyUp='form.whom.value=form.whom.value.toUpperCase()'></INPUT></td>"; 

    
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=3><b><font text color=#003366 size=2>Profesiones:</font></b><br>";   
   $query = "SELECT propro,prfdes,unides,profec FROM socios_000003,socios_000007,socios_000008 where proced='".$wcod."' AND propro=prfcod AND prouni=unicod ORDER BY propro"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   echo "<select name='winf' SIZE='3' onchange='window.location.href=this.options[this.selectedIndex].value' >"; 
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  		
      	//echo "<option>".$registroB[0]."- ".$registroB[1]."- ".$registroB[2]."</option>"; 
    	echo "<option VALUE='socios03.php?wcod=".$wcod."&wdoc=".$registroB[0]."&windicador=OtraVez&wproceso=Modificar'>".$registroB[0]."- ".$registroB[1]."- ".$registroB[2]."</option>";

	    $i++; 
      }   
    echo "</select></td>";    
 
    // $wproceso y wnit son variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";   

	   if (isset($wcod))
	     echo "<INPUT TYPE = 'hidden' NAME='wcod' VALUE='".$wcod."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wcod'></INPUT>"; 
	                    
	   if (isset($windicador))
	     echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='windicador'></INPUT>";    
     
   	echo "<tr><td align=center colspan=6 bgcolor=#C0C0C0>";
   	echo "<input type='submit' value='Grabar'>";          
   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	echo "<tr><td align=center colspan=6 bgcolor=#C0C0C0>";
   	echo "</td></tr>";	
	
// ***** RESPECTO AL STANDARD CAMBIA  <> "PrimeraVez" POR  == "SegundaVez"   ***   	   	
if ( isset($wpro) and $wpro<>'' and isset($wuni) and $wuni<>'' and $windicador == "SegundaVez")   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
   
  // invoco la funcion que valida los campos 
  validar_datos($wpro,$wuni,$wreg,$wfec); 
  

  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {	     
    if (isset($wpro)) 
     $c1=explode('-',$wpro);     // Del combo tomo el codigo
    if (isset($wuni)) 
     $c2=explode('-',$wuni);     // Del combo tomo el codigo
    if (isset($wdep)) 
     $c3=explode('-',$wdep);     // Del combo tomo el codigo
    if (isset($wmun)) 
     $c4=explode('-',$wmun);     // Del combo tomo el codigo
   
     $query = "SELECT * FROM socios_000003 Where proced = '".$wcod."' And propro = '".$c1[0]."'";
     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )                   //Encontro
     {
	    $query = "Update socios_000003 SET prouni='".$c2[0]."',proreg='".$wreg."',proact='".$wact."'"
       .",profec='".$wfec."',prodep='".$c3[0]."',promun='".$c4[0]."',prohom='".$whom."' Where proced='".$wcod."'"
       ." And propro='".$c1[0]."'";
               
       $resultado = mysql_query($query,$conex);  
	   if ($resultado)
	    echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Modificado</td></tr>";
	   else
	   {
	    echo "<table border=1>";	 
	    echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	    echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL MODIFICAR DATOS!!!!</MARQUEE></font>";				
	    echo "</td></tr></table><br><br>";
	   }        
	   
     } 
     else
     {
	 
      $fecha = date("Y-m-d");
	  $hora = (string)date("H:i:s");		      
      $query = "INSERT INTO socios_000003 VALUES ('socios','".$fecha."','".$hora."','".$wcod."','".$c1[0]."','".$c2[0]."','".$wreg."','".$wact
              ."','".$wfec."','".$c3[0]."','".$c4[0]."','".$whom."','C-socios','')";  
	  $resultado = mysql_query($query,$conex);  
	  if ($resultado)
	   echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Adicionado</td></tr>";
	  else
	  {
	   echo "<table border=1>";	 
	   echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	   echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL ADICIONAR DATOS!!!!</MARQUEE></font>";				
	   echo "</td></tr></table><br><br>";
	  }  	   
     }
     
    } 
   }
   else
   {
	if ($windicador <> "PrimeraVez") 	     
	{
     echo "<table border=1>";	 
     echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
     echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, EN LOS DATOS DIGITADOS!!!!</MARQUEE></font>";				
     echo "</td></tr></table><br><br>";
    }
         
   } 
   echo "</center></table>";  
  
}

// ***** RESPECTO AL STANDARD CAMBIA  == "PrimeraVez" POR  <> "SegundaVez"   ***
if ($windicador <> "SegundaVez" )       	
{
   //Si no controlo con esta variable siempre que le den editar en el programa que lo llama este entra modificando   
   $windicador = "SegundaVez";
   echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
}	    
?>
</BODY>
</html>