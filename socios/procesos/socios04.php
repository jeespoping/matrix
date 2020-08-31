<html>
<head>
<title>Informacion academica</title>
</head>

<script>
    function ira()
    {
	 document.socios04.wpro.focus();
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
		document.forms.socios04.submit();   // Ojo para la funcion socios04 <> Socios04  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.socios04.wesp.value = '';
     document.forms.socios04.wtip.value = '';
     document.forms.socios04.wuni.value = '';
     document.forms.socios04.wfec.value = 'aaaa-mm-dd';     
     document.forms.socios04.wact.value = '';
     document.forms.socios04.wdep.value = '';
     document.forms.socios04.wmun.value = '';     
  
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

$wactualiz="PROGRAMA: socios04.php Ver. 2011-08-22   JairS";

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

Function validar_datos($esp,$uni,$act,$fec) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
     
   if (empty($esp))
   {
      $todok = false;
      $msgerr=$msgerr." Debe Seleccionar una especialidad. ";   
   }
                  
   if (empty($uni))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe Seleccionar una Universidad. ";   
   }   

   if (empty($act))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe existir Acta de grado.";   
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



echo "<form name='socios04' action='socios04.php' method=post>";  
		
echo "<center><table border=1>";
echo "<tr><td align=center colspan=3 bgcolor=#99CCCC><font size=3 text color=#FFFFFF><b>Datos de las especialidades</b></font></tr>";

//No se porque el parametro $wcod llega con un espacio en blanco al principio entonces trim()
$wcod=trim($wcod); 

if ($windicador == "PrimeraVez") 
{
		
   $query = "SELECT * FROM socios_000004 Where espced='".$wcod."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wesp=$registro[4];
    $wuni=$registro[5];
    $wtip=$registro[6];
    $wfec=$registro[7];
    $wact=$registro[8];
    $wdep=$registro[9];
    $wmun=$registro[10];
  } 
} 
else
{
// ***** RESPECTO AL STANDARD SE AGREGA ESTE else   ***	
  if ( $windicador == "OtraVez" )
  {
   $query = "SELECT * FROM socios_000004 Where espced='".$wcod."' AND espesp='".$wdoc."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wesp=$registro[4];
    $wuni=$registro[5];
    $wtip=$registro[6];
    $wfec=$registro[7];
    $wact=$registro[8];
    $wdep=$registro[9];
    $wmun=$registro[10];
    
    
   }
  }
} 



   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Especialidad:</font></b><br>";   
   
   $query = "SELECT espcod,espdes FROM socios_000010 ORDER BY espdes";   
   echo "<select name='wesp'>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c1=explode('-',$wesp); 			  
  		if($c1[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".substr($registroB[1],0,25)."</option>";
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
	      echo "<option selected>".$registroB[0]."- ".substr($registroB[1],0,25)."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";

   echo "<td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Tipo<br></font></b>"; 
    echo "<select name='wtip'>";
    if (isset($wtip))
    {
	 $c3=explode('-',$wtip);     // Del combo tomo el codigo
	 if($c3[0] == "01" ) 
	  echo "<option SELECTED>01- Especialidad</option>";          
	 else
	  echo "<option>01- Especialidad</option>";    
	    
	 if($c3[0] == "02" ) 
	  echo "<option SELECTED>02- Subespecialidad</option>";            
	 else      
      echo "<option>02- Subespecialidad</option>";  
      
	 if($c3[0] == "03" ) 
	  echo "<option SELECTED>03- Posgrado</option>";          
	 else
	  echo "<option>03- Posgrado</option>";    
	    
	 if($c3[0] == "04" ) 
	  echo "<option SELECTED>04- Maestria</option>";            
	 else      
      echo "<option>04- Maestria</option>";        
      
     if($c3[0] == "05" ) 
	  echo "<option SELECTED>05- Doctorado</option>";            
	 else      
      echo "<option>05- Doctorado</option>";        

     if($c3[0] == "06" ) 
	  echo "<option SELECTED>06- Diplomado</option>";            
	 else      
      echo "<option>06- Diplomado</option>";        

     if($c3[0] == "07" ) 
	  echo "<option SELECTED>07- Curso</option>";            
	 else      
      echo "<option>07- Curso</option>";        
     
    }  
    else
	{		
	 echo "<option>01- Especialidad</option>";    
	 echo "<option>02- Subespecialidad</option>";
	 echo "<option>03- Posgrado</option>";  
	 echo "<option>04- Maestria</option>";        
	 echo "<option>05- Doctorado</option>";  
	 echo "<option>06- Diplomado</option>";        
	 echo "<option>07- Curso</option>";        
	    
	}	
	echo "</select></td>";  	
	
	if (!isset($wfec))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec=date("Y-m-d");
    
    echo "<tr><td bgcolor=#C0C0C0 align=center colspan=1><font text color=#003366 size=3>Fecha de Grado:<br>";
   	$cal="calendario('wfec','1')";
	echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=".$wfec." class=tipo3><button id='trigger4' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=3>Acta de grado:</font></b><br>";
    if (isset($wact))
     echo "<INPUT TYPE='text' NAME='wact' size=30 maxlength=15 VALUE='".$wact."' onKeyUp='form.wact.value=form.wact.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wact' size=30 maxlength=15 onKeyUp='form.wact.value=form.wact.value.toUpperCase()'></INPUT></td>"; 
     
   echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Departamento:</font></b><br>";   
   
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
    
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Municipio:</font></b><br>";   
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
    
     
   
    echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Especialidades especificadas:</font></b><br>";   
   $query = "SELECT espesp,espdes,unides,espfec FROM socios_000004,socios_000010,socios_000008 where espced='".$wcod."' AND espesp=espcod AND espuni=unicod ORDER BY espesp"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
// ***** PARA QUE FUNCIONE ESTE <select> se hacen los cambios que se documentan aqui  ***
   echo "<select name='winf' SIZE='3' onchange='window.location.href=this.options[this.selectedIndex].value' >"; 
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  		
      	//echo "<option>".$registroB[0]."- ".$registroB[1]."- ".$registroB[2]."</option>"; 
        echo "<option VALUE='socios04.php?wcod=".$wcod."&wdoc=".$registroB[0]."&windicador=OtraVez&wproceso=Modificar'>".$registroB[0]."- ".substr($registroB[1],0,25)."- ".substr($registroB[2],0,25)."</option>";

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
if ( isset($wesp) and $wesp<>'' and isset($wuni) and $wuni<>'' and isset($wtip) and $wtip<>'' and $windicador == "SegundaVez")    
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
   
  // invoco la funcion que valida los campos 
  validar_datos($wesp,$wuni,$wact,$wfec); 
  

  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {	     
    if (isset($wesp)) 
     $c1=explode('-',$wesp);     // Del combo tomo el codigo
    if (isset($wuni)) 
     $c2=explode('-',$wuni);     // Del combo tomo el codigo
    if (isset($wtip)) 
     $c3=explode('-',$wtip);     // Del combo tomo el codigo
    if (isset($wdep)) 
     $c4=explode('-',$wdep);     // Del combo tomo el codigo
    if (isset($wmun)) 
     $c5=explode('-',$wmun);     // Del combo tomo el codigo
     
   
     $query = "SELECT * FROM socios_000004 Where espced = '".$wcod."' And espesp = '".$c1[0]."'";
     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )                   //Encontro
     {
	    $query = "Update socios_000004 SET espuni='".$c2[0]."',esptip='".$c3[0]."',espfec='".$wfec."'"
       .",espact='".$wact."',espdep='".$c4[0]."',espmun='".$c5[0]."' Where espced='".$wcod."'"
       ." And espesp='".$c1[0]."'";
               
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
      $query = "INSERT INTO socios_000004 VALUES ('socios','".$fecha."','".$hora."','".$wcod."','".$c1[0]."','".$c2[0]."','".$c3[0]."','".$wfec
              ."','".$wact."','".$c4[0]."','".$c5[0]."','C-socios','')";  
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