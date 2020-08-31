<?php
include_once("conex.php");                                                    
session_start();
if (!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

/* Este script se ejecuta con parametro ejemplo asi:
/* http://localhost/archivos/ameniif01.php?wemp=1
/*
/* donde wemp=1 es usuario de PMLA
         wemp=2 es usuario de LABORATORIO
		 wemp=3 es usuario de CLINICA DEL SUR
		 wemp=4 es usuario de PATOLOGIA
		 wemp=5 es usuario de FARMASTORE
		 wemp=6 es usuario del INSTITUTO
*/
?>    
<html>
<head>
<title>HOMOLOGACION DE CUENTAS A NIIF</title>
	
  <link rel="stylesheet" href="jquery-ui.css">
  <script src="jquery-2.1.4.js"></script>
  <script src="jquery-ui.js"></script>
   
</head>

<script>
    function ira()
    {
	 document.ameniif01.wcac.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

<script type="text/javascript">

	function enter()
	{
		document.forms.ameniif01.submit();   // Ojo para la funcion ameniif01 <> ameniif01  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.ameniif01.wcac.value = '';
	 document.forms.ameniif01.wdac.value = '';
     document.forms.ameniif01.wcni.value = '';
     document.forms.ameniif01.wdni.value = '';
    }
    
 	// Fn que solo deje digitar los nros del 0 al 9
	function numeros(e)
	{
     key = e.keyCode || e.which;
     tecla = String.fromCharCode(key).toLowerCase();
     letras = "0123456789";
     especiales = [8];      // El 8 es para que la tecla <backspace> tambien la deje digitar (Se pueden incluir otros ascii separados por coma)
 
     tecla_especial = false
     for(var i in especiales)
	 {
      if(key == especiales[i])
	   {
         tecla_especial = true;
         break;
       }
     }
 
      if(letras.indexOf(tecla)==-1 && !tecla_especial)
        return false;
	}
	
 </script>

<?php

//==========================================================================================================================================
//PROGRAMA				      :ameniif01  permite actualizar el maestro de homologacion de ctas                                                            
//AUTOR				          :Jair Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Enero 22 de 2016
                                                                       
$wactualiz="PROGRAMA: ameniif01.php Ver. Enero 22 de 2016  JairS";

Function validar_datos($cac,$dac,$cni,$dni) 

 {  
   global $todok;
   global $msgerr;
   
   $todok = true;
   $msgerr = "";
   
   if (empty($cac))
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar la cuenta actual; ";   
   }
   if (empty($dac))
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar la descripcion de la cuenta actual; ";  
   }
   if (empty($cni))
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar la cuenta NIIF; ";   
   }
   
   if (empty($dni))
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar la descripcion de la cuenta NIIF; ";   
   }  	  
   //echo "<font size=2 text color=#CC0000>".$msgerr;   
   //return $todok;   
   
   return $msgerr;   
 }  

//*************************************
  

   
   
  switch($wemp) 
  {
   case 1:     //es usuario de PMLA
    $conex = odbc_connect(connif,'informix','sco') or die("No se realizo Conexion con la BD connif en Informix");  	
	break;
   case 2:     //es usuario de LABORATORIO
    $conex = odbc_connect(conlabn,'informix','sco') or die("No se realizo Conexion con la BD conlabn en Informix");  	
	break;
   case 3:     //es usuario de CLINICA DEL SUR
    $conex = odbc_connect(consurn,'informix','sco') or die("No se realizo Conexion con la BD consurn en Informix");  	
	break;
   case 4:     //es usuario de PATOLOGIA
    $conex = odbc_connect(conpatn,'informix','sco') or die("No se realizo Conexion con la BD conpatn en Informix");  	
	break;
   case 5:     //es usuario de FARMASTORE
    $conex = odbc_connect(conston,'informix','sco') or die("No se realizo Conexion con la BD conston en Informix");  	
	break;
   case 6:     //es usuario del INSTITUTO
    $conex = odbc_connect(coninst,'informix','sco') or die("No se realizo Conexion con la BD coninst en Informix");  	
	break;	
  }

echo "<form name='ameniif01' action='ameniif01.php' method=post>";  

    echo "<center><table border=1>";

	echo "<td align=center bgcolor=#6699CC colspan=6><b><font text color='#000000' size=2>HOMOLOGACION DE CUENTAS A NIIF<br></td>";
    echo "<li><A HREF='ameniif01.php?wemp=".$wemp."&windicador=PrimeraVez&wproceso=Nuevo'><font color=#000000'>Adicionar registro</A></td>"; 


   //*************************Para que lo haga solo una vez la Primera vez*******************************************
   if  ($windicador == "PrimeraVez") 
   { 

    if ($wproceso=="Nuevo") 
	{        
     $wcac = "";
	 $wdac = "";
     $wcni = "";
     $wdni = "";
    }
    else  
    {  
      if ($wproceso=="Borrar")
	  {   
		 if ($wcuenta==$wcac)    //Obligo que el registro a borrar este editado en pantalla
		 {	 
	       $query  = "DELETE FROM ameniif01 "
		            ." Where cueact='".$wcuenta."'";			   
           $resultadoB = @odbc_exec( $conex, $query);
		   $wcac = "";
	       $wdac = "";
           $wcni = "";
           $wdni = "";   
		 }
		 else
		   print "<script>alert('Atencion!!! para borrar primero tiene que editar el registro')</script>";	  
      } 
	  else   // Si 'Modificar' o Editar entro mostrando los campos del registro seleccionado 
	  {
 	     $query  = "SELECT cueact,nomact,cuenif,nomnif "
                  ." From ameniif01 "
		          ." Where cueact='".$wcac."'";			   
         $resultadoB = @odbc_exec( $conex, $query);
         if ( @odbc_fetch_into($resultadoB, $registroB)) 
		 {	 
           $wcac=trim($registroB[0]);
	       $wdac=trim($registroB[1]);
	       $wcni=trim($registroB[2]);
	       $wdni=trim($registroB[3]);
	     }
      }
     }
   }      
   
//****CAPTURA DE DATOS ********************************************************************************/	
    
     echo "<tr><td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Cuenta Contable Actual:</font></b><br>";
     if (isset($wcac))
       echo "<INPUT TYPE='text' NAME='wcac' size=30 maxlength=9 VALUE='".$wcac."' onkeypress='return numeros(event);'></INPUT></td>"; 
     else
       echo "<INPUT TYPE='text' NAME='wcac' size=30 maxlength=9 onkeypress='return numeros(event);'></INPUT></td>";     
             
    	
     echo "<td align=center bgcolor=#C0C0C0 colspan=3><b><font text color=#003366 size=2>Descripcion Cuenta Actual:</font></b><br>";
    if (isset($wdac))
     echo "<TEXTAREA NAME='wdac' COLS='80' ROWS='2'>".$wdac."</TEXTAREA></td>"; 
    else
     echo "<TEXTAREA NAME='wdac' COLS='80' ROWS='2'></TEXTAREA></td>"; 
 
     echo "<tr><td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Cuenta Contable NIIF:</font></b><br>";
     if (isset($wcni))
       echo "<INPUT TYPE='text' NAME='wcni' size=30 maxlength=9 VALUE='".$wcni."' onkeypress='return numeros(event);'></INPUT></td>"; 
     else
       echo "<INPUT TYPE='text' NAME='wcni' size=30 maxlength=9 onkeypress='return numeros(event);'></INPUT></td>";     
             
    	
    echo "<td align=center bgcolor=#C0C0C0 colspan=3><b><font text color=#003366 size=2>Descripcion Cuenta NIIF:</font></b><br>";
    if (isset($wdni))
     echo "<TEXTAREA NAME='wdni' COLS='80' ROWS='2'>".$wdni."</TEXTAREA></td>"; 
    else
     echo "<TEXTAREA NAME='wdni' COLS='80' ROWS='2'></TEXTAREA></td>"; 
  
//**************************************************************************************************/	 
     // Aqui las Variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>";                   
	   echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	   
	   echo "<INPUT TYPE = 'hidden' NAME='wemp' VALUE='".$wemp."'></INPUT>"; 
//**************************************************************************************************/	 

   echo "<tr><td align=center bgcolor=#6699CC colspan=6>";
   echo "<input type='submit' value='Enviar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   echo "<td>";	
//***********************************************************************************/		
  				
	if ( $conf == "on" )   
    { 
	  validar_datos($wcac,$wdac,$wcni,$wdni);                    
	                    
	  if (strlen( $msgerr ) > 5 )    //Si retorna de la funcion con mensaje entonces lo muestro
       print "<script>alert('$msgerr')</script>";
	  else
	  {                       		  
       $query = "SELECT cueact,nomact,cuenif,nomnif "
               ." From ameniif01 "
		       ." Where cueact='".$wcac."'";
		       
	   $rs = @odbc_exec( $conex, $query );
       if  ( (!@odbc_fetch_into($rs, $dato))  ) // como esta con ! ==> No Encontro
	   { 		
  	    $query = "INSERT INTO ameniif01 (cueact,nomact,cuenif,nomnif) "
		        ." VALUES ('".$wcac."','".$wdac."','".$wcni."','".$wdni."')";
				
        $resultado = odbc_do($conex,$query);       // Ejecuto el query  
        if ($resultado)
		  print "<script>alert('La informacion se ha grabado correctamente.')</script>";
		else
		  print "<script>alert('Atencion!!! Se produjo un error al grabar la informacion')</script>";		  
//		 print "<script>enter()</script>";    // Para que me haga un submit y refresque

	   }	
	   else
	   {   	   		 
	       $query = "UPDATE ameniif01 SET nomact='".$wdac."',cuenif='".$wcni."',nomnif='".$wdni."'"
		       ." Where cueact='".$wcac."'";
			   
			$resultado = odbc_do($conex,$query);            // Ejecuto el query  
	   	    if ($resultado)
		      print "<script>alert('La modificacion ha realizado correctamente. ')</script>";   
		    else
		      print "<script>alert('Atencion!!! Se produjo un error al modificar la informacion')</script>";	  
	   } 
	 }
 
   }

if ($windicador == "PrimeraVez" ) 	
{  
   //Ya no es Primera Vez   
   $windicador = "SegundaVez";
   echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
}
	

echo "</table>";


/*********************************************************************************************************/
/**********                       MUESTRO LO QUE  SE VA GRABANDO                               ***********/
/*********************************************************************************************************/
$query  = "SELECT cueact,nomact,cuenif,nomnif "
          ." From ameniif01 "
		  ." Order By cueact";

$rs = @odbc_exec( $conex, $query );
if ( !$rs ) 
   exit( "Aun No hay registros para esta tabla de homologacion..." );
else 
{ 
  echo "<br>";
  echo "<center><table border=0>";

  echo "<tr>";
  echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>NRO REG<b></td>";
  echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>CTA ACTUAL<b></td>";
  echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>DESCRIPCION<b></td>";
  echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>CTA NIIF<b></td>";
  echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>DESCRIPCION<b></td>";
  echo "</tr>"; 
  $i=1;
  While (@odbc_fetch_into($rs, $dato))
  {		
   // color de fondo  
   if (is_int ($i/2))  // Cuando la variable $i es para coloca este color
    $wcf="DDDDDD";  
   else
    $wcf="CCFFFF";  
  	
   echo "<tr>"; 	
   echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$i."</td>";   
   echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$dato[0]."</td>";
   echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$dato[1]."</td>";
   echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$dato[2]."</td>";
   echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$dato[3]."</td>";
   // LLAMADOS CON PARAMETROS
   echo "<td colspan=1 align=center color=#FFFFFF bgcolor=".$wcf.">";
   echo "<A HREF='ameniif01.php?wemp=".$wemp."&wcac=".trim($dato[0])."&windicador=PrimeraVez&wproceso=Modificar'>Editar</A></td>";     
   echo "<td colspan=1 align=center color=#FFFFFF bgcolor=".$wcf.">";
   echo "<A HREF='ameniif01.php?wemp=".$wemp."&wcac=".$wcac."&wcuenta=".trim($dato[0])."&windicador=PrimeraVez&wproceso=Borrar'>Borrar</A></td>";     
 
 
   $i++; 

  } 
}
echo "</Table>";	  
/*********************************************************************************************************/
/*********************************************************************************************************/
//echo "<META  HTTP-EQUIV='Window-target' CONTENT='_top'>";
echo "</Form>";	  
odbc_close($conex);
odbc_close_all();  
?>
</BODY>
</html>