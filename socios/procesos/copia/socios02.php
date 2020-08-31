<html>
<head>
<title>Actualizacion del grupo familiar</title>
</head>

<script>
    function ira()
    {
	 document.socios02.wced.focus();
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
		document.forms.socios02.submit();   // Ojo para la funcion socios02 <> socios02  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.socios02.wced.value = '';
     document.forms.socios02.wap1.value = '';
     document.forms.socios02.wap2.value = '';
     document.forms.socios02.wnom.value = '';     
     document.forms.socios02.wnac.value = 'aaaa-mm-dd';
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
//PROGRAMA				      :Actualiza Grupo Familiar de Socios PMLA.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Agosto 22 de 2011
//FECHA ULTIMA ACTUALIZACION  :Agosto 22 de 2011.                                                                             

$wactualiz="PROGRAMA: socios02.php Ver. 2011-08-22   JairS";


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

Function validar_datos($ced,$ap1,$nom,$nac) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
     
   if (empty($ced))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir nro de documento. ";   
   }
                  
   if (empty($ap1))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe existir 1er Apellido. ";   
   }   

   if (empty($nom))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe existir Nombre del familiar.";   
   }   

   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( !checkdate(substr($nac,5,2), substr($nac,8,2), substr($nac,0,4)) )
   {
     $todok = false;     
     $msgerr=$msgerr." Fecha de nacimiento debe ser aaaa-mm-dd. ";   
   }                      
     
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  

Function pasardatos() 
{
 	echo "DDDDD:".$wdatos;	    
    return;	
}


mysql_select_db("matrix") or die("No se selecciono la base de datos");    

echo "<form name='socios02' action='socios02.php' method=post>";  
		
echo "<center><table border=1>";
echo "<tr><td align=center colspan=3 bgcolor=#99CCCC><font size=3 text color=#FFFFFF><b>Grupo Familiar del Socio</b></font></tr>";

//No se porque el parametro $wcod llega con un espacio en blanco al principio entonces trim()
$wcod=trim($wcod); 

if ($windicador == "PrimeraVez") 
{
   $query = "SELECT * FROM socios_000002 Where famced = '".$wcod."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wced=$registro[4];
    $wtid=$registro[5];
    $wtip=$registro[6];
    $wap1=$registro[7];
    $wap2=$registro[8];
    $wnom=$registro[9];
    $wnac=$registro[10];

  } 
 
} 
else
{
// ***** RESPECTO AL STANDARD SE AGREGA ESTE else   ***	
  if ( $windicador == "OtraVez" )
  {
   $query = "SELECT * FROM socios_000002 Where famced = '".$wcod."' AND famdoc='".$wdoc."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wced=$registro[4];
    $wtid=$registro[5];
    $wtip=$registro[6];
    $wap1=$registro[7];
    $wap2=$registro[8];
    $wnom=$registro[9];
    $wnac=$registro[10];
    
    
   }
  }
}   	
	
	

    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=3>Nro de Documento:</font></b><br>";
    if (isset($wced))
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 VALUE='".$wced."' onKeyUp='form.wced.value=form.wced.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 onKeyUp='form.wced.value=form.wced.value.toUpperCase()'></INPUT></td>"; 
    echo "<td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Tipo de Documento<br></font></b>"; 
    
    echo "<select name='wtid'>";
    if (isset($wtid))
    {
	 $c1=explode('-',$wtid);     // Del combo tomo el codigo   
	 if($c1[0] == "01" ) 
	  echo "<option SELECTED>01- Nit</option>";                
	 else
	  echo "<option>01- Nit</option>";                         
	    
	 if($c1[0] == "02" ) 
	  echo "<option SELECTED>02- Cedula</option>";            
	 else                                                     
      echo "<option>02- Cedula</option>";                     

     if($c1[0] == "03" )                                       
      echo "<option SELECTED>03- Cedula Extrangeria</option>"; 
     else                                                      
      echo "<option>03- Cedula Extrangeria</option>";    

     if($c1[0] == "04" )      
      echo "<option SELECTED>04- Tarjeta de Identidad</option>";        
     else  
      echo "<option>04- Tarjeta de Identidad</option>";  
    }  
    else
	{		
	 echo "<option>01- Nit</option>";
	 echo "<option>02- Cedula</option>";
	 echo "<option>03- Cedula Extrangeria</option>";
	 echo "<option>04- Tarjeta de Identidad</option>";
	}	
	 echo "</select></td>";  	
	 
	echo "<td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Parentesco<br></font></b>"; 
    echo "<select name='wtip'>";
    if (isset($wtip))
    {
	 $c1=explode('-',$wtip);     // Del combo tomo el codigo
	 if($c1[0] == "01" ) 
	  echo "<option SELECTED>01- Padre</option>";          
	 else
	  echo "<option>01- Padre</option>";    
	    
	 if($c1[0] == "02" ) 
	  echo "<option SELECTED>02- Madre</option>";            
	 else      
      echo "<option>02- Madre</option>";  

     if($c1[0] == "03" )      
      echo "<option SELECTED>03- Conyuge</option>"; 
     else   
      echo "<option>03- Conyuge</option>";    

     if($c1[0] == "04" )      
      echo "<option SELECTED>04- Hijo</option>";        
     else  
      echo "<option>04- Hijo</option>";  
     if($c1[0] == "05" )      
      echo "<option SELECTED>05- Hija</option>";        
     else  
      echo "<option>05- Hija</option>";  
    }  
    else
	{		
	 echo "<option>01- Padre</option>";
	 echo "<option>02- Madre</option>";
	 echo "<option>03- Conyuge</option>";
	 echo "<option>04- Hijo</option>";
	 echo "<option>05- Hija</option>";
	}	
	 echo "</select></td>";  	
		
 
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>1er Apellido:</font></b><br>";
    if (isset($wap1))
     echo "<INPUT TYPE='text' NAME='wap1' size=30 maxlength=20 VALUE='".$wap1."' onKeyUp='form.wap1.value=form.wap1.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wap1' size=30 maxlength=20 onKeyUp='form.wap1.value=form.wap1.value.toUpperCase()'></INPUT></td>"; 

    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>2do Apellido:</font></b><br>";
    if (isset($wap2))
     echo "<INPUT TYPE='text' NAME='wap2' size=30 maxlength=20 VALUE='".$wap2."' onKeyUp='form.wap2.value=form.wap2.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wap2' size=30 maxlength=20 onKeyUp='form.wap2.value=form.wap2.value.toUpperCase()'></INPUT></td>"; 
     
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Nombres:</font></b><br>";
    if (isset($wnom))
     echo "<INPUT TYPE='text' NAME='wnom' size=30 maxlength=25 VALUE='".$wnom."' onKeyUp='form.wnom.value=form.wnom.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wnom' size=30 maxlength=25 onKeyUp='form.wnom.value=form.wnom.value.toUpperCase()'></INPUT></td>"; 

    
  if (!isset($wnac))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wnac=date("Y-m-d");
    
    echo "<tr><td bgcolor=#C0C0C0 align=center colspan=1><font text color=#003366 size=3>Fecha de Nacimiento:<br>";
   	$cal="calendario('wnac','1')";
	echo "<input type='TEXT' name='wnac' size=10 maxlength=10  id='wnac' readonly='readonly' value=".$wnac." class=tipo3><button id='trigger4' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wnac',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

    
  echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Grupo Familiar:</font></b><br>";   


   $query = "SELECT * FROM socios_000002 where famced='".$wcod."' ORDER BY famtip";   
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   // ***** PARA QUE FUNCIONE ESTE <select> se hacen los cambios que se documentan aqui  *** 
   echo "<select name='winf' SIZE='3' onchange='window.location.href=this.options[this.selectedIndex].value' >";
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  		
		
		switch ($registroB[6]) 
		{
         case "01":
          $c1="Padre";
          break;
        case "02":
          $c1="Madre";
          break;
        case "03":
          $c1="Conyuge";
          break;
        case "04":
          $c1="Hijo";  
        case "05":
          $c1="Hija";  
       }
       echo "<option VALUE='socios02.php?wcod=".$wcod."&wdoc=".$registroB[4]."&windicador=OtraVez&wproceso=Modificar'>".$registroB[4]."- ".$registroB[7]." ".$registroB[8]." ".$registroB[9]." -".$registroB[6]."- ".$c1."</option>";
  	
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
if ( isset($wced) and $wced<>'' and isset($wap1) and $wap1<>'' and $windicador == "SegundaVez")   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
   
  // invoco la funcion que valida los campos 
  validar_datos($wced,$wap1,$wnom,$wnac); 
  

  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {	     
    if (isset($wtid)) 
     $c1=explode('-',$wtid);     // Del combo tomo el codigo
    if (isset($wtip)) 
     $c2=explode('-',$wtip);     // Del combo tomo el codigo

   
     $query = "SELECT * FROM socios_000002 Where famced = '".$wcod."' And famdoc = '".$wced."'";

     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )                   //Encontro
     {
	 	 
       $query = "Update socios_000002 SET famtid='".$c1[0]."',famtip='".$c2[0]."',famap1='".$wap1."',famap2='".$wap2."'"
               .",famnom='".$wnom."',famnac='".$wnac."' Where famced='".$wcod."' And famdoc='".$wced."'";
              
       $resultado = mysql_query($query,$conex);  
	   if ($resultado)
	   {
	    echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Modificado</td></tr>";
       // echo "<script language='javascript'>alert('Registro Modificado');</script>"; 
        //echo "<script>alert('Registro Modificado');</script>";
       }  
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
      $query = "INSERT INTO socios_000002 VALUES ('socios','".$fecha."','".$hora."','".$wcod."','".$wced."','".$c1[0]."','".$c2[0]."','".$wap1
              ."','".$wap2."','".$wnom."','".$wnac."','C-socios','')";  
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