<html>
<head>
<title>Movimiento de Acciones</title>
</head>

<script>
    function ira()
    {
	 document.socios06.wtit.focus();
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
		document.forms.socios06.submit();   // Ojo para la funcion socios06 <> Socios06  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.socios06.wtit.value = '';
     document.forms.socios06.wtra.value = '';
     document.forms.socios06.wfec.value = 'aaaa-mm-dd';
     document.forms.socios06.wacc.value = '';     
     document.forms.socios06.wtip.value = '';
     document.forms.socios06.wobs.value = '';
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
//PROGRAMA				      :Movimiento de Acciones.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Agosto 22 de 2011
//FECHA ULTIMA ACTUALIZACION  :Agosto 22 de 2011.                                                                             

$wactualiz="PROGRAMA: socios06.php Ver. 2011-08-22   JairS";

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

Function validar_datos($tit,$tra,$fec,$acc) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
     
   if (empty($tit))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir Nro de Titulo o documento. ";   
   }
                  
   if (empty($tra))
   {
      $todok = false; 
      $msgerr=$msgerr." Error en Tipo de Transaccion. ";   
   }   

   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( !checkdate(substr($fec,5,2), substr($fec,8,2), substr($fec,0,4)) )
   {
     $todok = false;     
     $msgerr=$msgerr." Fecha de la transaccion debe tener formato aaaa-mm-dd ";   
   }   
   
   if (empty($acc))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe existir Nro de Acciones a Transar.";   
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



echo "<form name='socios06' action='socios06.php' method=post>";  
		
echo "<center><table border=1>";
echo "<tr><td align=center colspan=3 bgcolor=#99CCCC><font size=3 text color=#FFFFFF><b>Movimiento de Acciones</b></font></tr>";

//No se porque el parametro $wcod llega con un espacio en blanco al principio entonces trim()
$wcod=trim($wcod); 

if ($windicador == "PrimeraVez") 
{
		
   $query = "SELECT * FROM socios_000006 Where traced = '".$wcod."'";
        
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wtit=$registro[4];
    $wtra=$registro[5];
    $wfec=$registro[6];
    $wacc=$registro[7];
    $wtip=$registro[8];
    $wsoc=$registro[9];
    $wobs=$registro[10];
  } 
 
} 
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=3>Nro del titulo o Documento:</font></b><br>";
    if (isset($wtit))
     echo "<INPUT TYPE='text' NAME='wtit' size=30 maxlength=15 VALUE='".$wtit."' onKeyUp='form.wtit.value=form.wtit.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wtit' size=30 maxlength=15 onKeyUp='form.wtit.value=form.wtit.value.toUpperCase()'></INPUT></td>"; 
     
    echo "<td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Tipo de Transaccion<br></font></b>"; 
    echo "<select name='wtra'>";
    if (isset($wtra))
    {
	 $c1=explode('-',$wtra);     // Del combo tomo el codigo   
	 if($c1[0] == "01" ) 
	  echo "<option SELECTED>01- Donacion</option>";                
	 else
	  echo "<option>01- Donacion</option>";                         
	    
	 if($c1[0] == "02" ) 
	  echo "<option SELECTED>02- Suscripcion</option>";            
	 else                                                     
      echo "<option>02- Suscripcion</option>";                     

     if($c1[0] == "03" )                                       
      echo "<option SELECTED>03- Venta</option>"; 
     else                                                      
      echo "<option>03- Venta</option>";    

     if($c1[0] == "04" )      
      echo "<option SELECTED>04- Compra</option>";        
     else  
      echo "<option>04- Compra</option>";  
      
     if($c1[0] == "05" )                                       
      echo "<option SELECTED>05- Adjudicacion</option>"; 
     else                                                      
      echo "<option>05- Adjudicacion</option>";    

     if($c1[0] == "06" )      
      echo "<option SELECTED>06- Embargo</option>";        
     else  
      echo "<option>06- Embargo</option>";  

    }  
    else
	{		
	 echo "<option>01- Donacion</option>";      
	 echo "<option>02- Suscripcion</option>";   
     echo "<option>03- Venta</option>";  
	 echo "<option>04- Compra</option>"; 
	 echo "<option>05- Adjudicacion</option>";     
	 echo "<option>06- Embargo</option>";     
	}	
	echo "</select></td>";  	
	
  if (!isset($wfec))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec=date("Y-m-d");
    
 	$fecha = date("Y-m-d");echo "<td bgcolor=#C0C0C0 align=center colspan=1><font text color=#003366 size=3>Fecha de la transaccion:<br>";
   	$cal="calendario('wfec','1')";
	echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=".$wfec." class=tipo3><button id='trigger4' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Nro de acciones</font></b><br>";
    if (isset($wacc))
     echo "<INPUT TYPE='text' NAME='wacc' size=10 maxlength=4 VALUE='".$wacc."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wacc' size=10 maxlength=4 onkeypress='teclado()'></INPUT></td>";  

    
 	echo "<td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Tipo de Accion<br></font></b>"; 
    echo "<select name='wtip'>";
    if (isset($wtip))
    {
	 $c1=explode('-',$wtip);     // Del combo tomo el codigo
	 if($c1[0] == "01" ) 
	  echo "<option SELECTED>01- Ordinaria</option>";          
	 else
	  echo "<option>01- Ordinaria</option>";    
	    
	 if($c1[0] == "02" ) 
	  echo "<option SELECTED>02- Priviligiada</option>";            
	 else      
      echo "<option>02- Privilegiada</option>";  

    }  
    else
	{		
	 echo "<option>01- Ordinaria</option>";
	 echo "<option>02- Privilegiada</option>";
	}	
	 echo "</select></td>";  	
	
   echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Socio:</font></b><br>";   
   $query = "SELECT socced,socap1,socap2,socnom FROM socios_000001 ORDER BY socap1,socap2";   
   echo "<select name='wsoc'>"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB); 
		$c3=explode('-',$wsoc); 	 			  
  		if($c3[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";
    	 	
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=3><b><font text color=#003366 size=2>Observaciones</font></b><br>";
    if (isset($wobs))
     echo "<textarea name='wobs' cols=50 rows=3>".$wobs."</textarea></td>";
    else
     echo "<textarea name='wobs' cols=50 rows=3></textarea></td>";
           
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
	
if ( isset($wtit) and $wtit<>'' and isset($wtra) and $wtra<>'')   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
   
  // invoco la funcion que valida los campos 
  validar_datos($wtit,$wtra,$wfec,$wacc); 
  

  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {	     
    if (isset($wtid)) 
     $c1=explode('-',$wtra);     // Del combo tomo el codigo
    if (isset($wtip)) 
     $c2=explode('-',$wtip);     // Del combo tomo el codigo
    if (isset($wsoc)) 
     $c3=explode('-',$wsoc);     // Del combo tomo el codigo

   
     $query = "SELECT * FROM socios_000006 Where traced = '".$wcod."' And tratit = '".$wtit."'";

     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )                   //Encontro
     {
	 	 
       $query = "Update socios_000006 SET traced='".$wcod."',tratit='".$wtit."',tratra='".$c1[0]."',trafec='".$wfec."'"
               .", tranro=".$wacc.",tratip='".$c2[0]."',trasoc='".$c3[0]."',traobs='".$wobs."' Where traced='".$wcod."'"
               ." And tratit='".$wtit."'";
    
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
      $query = "INSERT INTO socios_000006 VALUES ('socios','".$fecha."','".$hora."','".$wcod."','".$wtit."','".$c1[0]."','".$wfec."',".$wacc
              .",'".$c2[0]."','".$c3[0]."','".$wobs."','C-socios','')";  
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

if ($windicador == "PrimeraVez") 	
{
   //Si no controlo con esta variable siempre que le den editar en el programa que lo llama este entra modificando   
   $windicador = "SegundaVez";
   echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
}	    
?>
</BODY>
</html>