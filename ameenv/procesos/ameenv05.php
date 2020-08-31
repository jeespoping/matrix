<html>
<head>
<title>Actualizacion Control de Entrega de respuestas a glosas</title>
</head>

<script>
    function ira()
    {
	 document.ameenv05.wnro.focus();
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
		document.forms.ameenv05.submit();   // Ojo para la funcion ameenv05 <> ameenv05  (sencible a mayusculas)
	}

	function vaciarCampos()
	{document.forms.ameenv05.wnro.value = '';
	 document.forms.ameenv05.wfac.value = '';
     document.forms.ameenv05.wsal.value = '';
     document.forms.ameenv05.wvli.value = '';
	 document.forms.ameenv05.wvlr.value = '';
	 document.forms.ameenv05.west.value = '';
	 document.forms.ameenv05.wgui.value = '';
	 document.forms.ameenv01.wcon.value = '';
     document.forms.ameenv05.wfes.value = '0000-00-00';
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
//PROGRAMA				      :Respuesta A Glosas por Cartera                                                                  
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Abril 23 de 2014
//FECHA ULTIMA ACTUALIZACION  :Julio 27 de 2015, Abril 2 de 2016 se adiciono campo Acta de conciliacion                                                                                      

$wactualiz="PROGRAMA: ameenv05.php Ver. 2014-04-2014   JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

Function validar_datos($nro,$fec,$gui,$con) 

{  global $todok;
   
   $todok = true;
   $msgerr = "";
   
   if (empty($nro))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir nro de respuesta. ";  
   }
     IF ($fec=="0000-00-00")
     {
      $todok = false;     
      $msgerr=$msgerr." Debe existir una fecha de sello valida. ";   
     }
	 ELSE
	 {
      // Como permite 30 dias mas o menos a la fecha actual entonces
      // Se modifican las lineas 89 y 92: se cambia '-30 day'  por '-120 day' y '+30 day' por '+120 day' para ampliar los rangos
	  $fechoy1 = date('Y-m-d h:i:s');
      $hoy30e = strtotime ( '-130 day' , strtotime ( $fechoy1 ) ) ;
      $hoy30e = date ( 'Y-m-d h:i:s' , $hoy30e );

      $hoy30m = strtotime ( '+30 day' , strtotime ( $fechoy1 ) ) ;
      $hoy30m = date ( 'Y-m-d h:i:s', $hoy30m );
		
      if ( ($fec < $hoy30e )  or ( $fec > $hoy30m)  ) 
      {
       $todok = false;     
       $msgerr=$msgerr." Fecha de sello debe estar en 30 dias mas o menos";   
      }
     }
	 
     if (empty($gui))
     {
      $todok = false;
      $msgerr=$msgerr." Debe digitar el nro de guia o radicacion. ";   
     }

	 if (empty($con))
     {
      $todok = false;
      $msgerr=$msgerr." Debe digitar datos de conciliacion Como Nro de Acta y fecha. ";   
     }
	
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  



mysql_select_db("matrix") or die("No se selecciono la base de datos");  
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");

echo "<form name='ameenv05' action='ameenv05.php?wproceso=Grabar' method=post>";  

// Almaceno el Id del registro enviado
$wid=trim($wid); 

echo "<center><table border=1>";

if ($wproceso == "Modificar" )
 echo "<td align=center colspan=3 bgcolor=#99CCCC><font size=3 text color=#FF0000><b>Actualizacion Respuesta a Glosa Nro: ".$wid."</b></font></tr>";
else
 echo "<td align=center colspan=3 bgcolor=#99CCCC><font size=3 text color=#FF0000><b>Actualizacion Respuesta a Glosas X Cartera</b></font></tr>";

if ($windicador == "PrimeraVez") 
{
 if ( ($wproceso == "Modificar" ) or ($wproceso == "Borrar" ) )
  {
   $query = "SELECT nrores,factu,saldo,vlrini,fecsello,vlrrecla,estado,fecing,guia,acta FROM ameglres Where nrores=".$wid;
   $resultadoB = odbc_exec($conexN,$query);            // Ejecuto el query
   
   if (odbc_fetch_row($resultadoB))                    // Encontro 
   {
    $wnro=odbc_result($resultadoB,1);
    $wfac=odbc_result($resultadoB,2);
    $wsal=odbc_result($resultadoB,3);
    $wvli=odbc_result($resultadoB,4);
    $wfes=odbc_result($resultadoB,5);
	$wvlr=odbc_result($resultadoB,6);
	$west=odbc_result($resultadoB,7);
	$wfec=odbc_result($resultadoB,8);
	$wgui=odbc_result($resultadoB,9);
	$wcon=odbc_result($resultadoB,10);
	
    //$query="Select Count(*) TOTFAC from ahdocact where docactenv=".$wid;
    $query="Select gloresfac From caglores"
          ." Where gloresfue='87' And gloresdoc=".$wid." Group by 1";
    $resultadoC = odbc_do($conexN,$query);    // Ejecuto el query  
    if (odbc_fetch_row($resultadoC))          // Encontro ==> Modifico 
      $wfac=odbc_result($resultadoC,1);      
   }
  } 
}
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Nro de respuesta</font></b><br>";
    if ($wnro>0)
		
    {  
	
    echo "<INPUT TYPE='text' NAME='wnro' size=30 maxlength=20 VALUE='".$wnro."' onkeypress='teclado()' OnChange='enter()' ></INPUT></td>"; 
     
	 //if ( ($windicador <> "PrimeraVez") )
     //{ 
    
	     if ($wproceso=="Grabar")
	     { 
		   $query = "SELECT Count(*) NRORES FROM ameglres Where nrores=".$wnro; 		   

	       $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
           if (odbc_result($resultadoC,1) > 0)  
           {
	        //echo "<font size=3 text color=#CC0000>Este envio ya fue relacionado...";
	        $mensaje = "Error!!! esta respuesta ya fue relacionada...";
	        print "<script>alert('$mensaje')</script>";
	        $todok = false; 
           } 
	       else
		   {
		     $query="Select gloresfac From caglores Where gloresfue='87' And gloresdoc=".$wnro." Group by 1";  
	         $resultadoC = odbc_do($conexN,$query);        // Ejecuto el query  
             if (odbc_result($resultadoC,1) > 0)  
			 {
                 $wfac=odbc_result($resultadoC,1); 

				 $query="Select sallinval From casallin Where sallinfue IN ('20','21','22') And sallindoc=".$wfac;
                 $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
                 if (odbc_result($resultadoC,1) > 0)
				  $wsal=odbc_result($resultadoC,1);
				 else
				  $wsal=0;
				  
				  
				  $query="Select SUM(gloresvre) From caglores Where gloresfca IN ('20','21','22')"
				   . " And gloresfac=".$wfac." And gloresfue='87' And gloresdoc=".$wnro;
                  $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
                  $wvlr=odbc_result($resultadoC,1);
				  
				  $query="Select count(*) From ameinggl Where factu=".$wfac." And estado IN ('EG','GV')";
                  $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
                  if  ( odbc_result($resultadoC,1)==0 )
    			  {
				   $mensaje = "Error!!! La no ha sido ingresada en cartera.";
                   print "<script>alert('$mensaje')</script>";
                   $todok = false;
                  }		
                  else
				  {
				   $query="Select MAX(fecha) From ameinggl Where factu=".$wfac." And estado IN ('EG','GV')";
                   $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
                   $maxfec=odbc_result($resultadoC,1);
				  				  
				   $query="Select SUM(vlrglosa) From ameinggl Where factu=".$wfac." And estado IN ('EG','GV') And fecha='".$maxfec."'";
                   $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
                   $wvli=odbc_result($resultadoC,1);
				  }
				   
				   $west='RC';
				  	 
			 }	 
             else
             {
               $mensaje = "Error!!! La respuesta no tiene factura asociada...";
               print "<script>alert('$mensaje')</script>";
               $todok = false; 
             } 
			 
            }
           }  
              
        // } 
       } 
      
      else
        echo "<INPUT TYPE='text' NAME='wnro' size=30 maxlength=20 onkeypress='teclado()' OnBlur='enter()'></INPUT></td>"; 
		
    
      echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2 >Factura:</font></b><br>";   
      if (isset($wfac))
        echo "<INPUT TYPE='text' NAME='wfac' size=30 maxlength=20 color=#FF0000 readonly='readonly' VALUE='".$wfac."')' ></INPUT></td>"; 
      else
        echo "<INPUT TYPE='text' NAME='wfac' size=30 maxlength=20 readonly='readonly'></INPUT></td>";      
 
      echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Saldo:</font></b><br>";   
      if (isset($wsal))
        echo "<INPUT TYPE='text' NAME='wsal' size=30 maxlength=20 readonly='readonly' VALUE='".$wsal."')' ></INPUT></td>"; 
      else
        echo "<INPUT TYPE='text' NAME='wsal' size=30 maxlength=20 readonly='readonly'></INPUT></td>";      
 
     
      echo "<tr><td align=center bgcolor=#C0C0C0 colspan=3><b><font text color=#003366 size=2>Valor Ingreso Glosa:</font></b><br>";   
      if (isset($wsal))
        echo "<INPUT TYPE='text' NAME='wvli' size=30 maxlength=20 readonly='readonly' VALUE='".$wvli."')' ></INPUT></td>"; 
      else
        echo "<INPUT TYPE='text' NAME='wvli' size=30 maxlength=20 readonly='readonly'></INPUT></td></tr>";        

     
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Valor Reclamado</font></b><br>";
    if (isset($wvlr))
      echo "<INPUT TYPE='text' NAME='wvlr' size=30 maxlength=6 readonly='readonly' VALUE='".$wvlr."')' ></INPUT></td>"; 
    else
      echo "<INPUT TYPE='text' NAME='wvlr' size=30 maxlength=6 readonly='readonly'></INPUT></td>"; 
 
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Estado:</font></b><br>";   
    if (isset($west))
      echo "<INPUT TYPE='text' NAME='west' size=10 maxlength=20 readonly='readonly' VALUE='".$west."')' ></INPUT></td>"; 
    else
      echo "<INPUT TYPE='text' NAME='west' size=10 maxlength=20 readonly='readonly'></INPUT></td></tr>"; 
	 
	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Nro de Guia o Radicacion:</font></b><br>";   
    if (isset($wgui))
      echo "<INPUT TYPE='text' NAME='wgui' size=30 maxlength=20 VALUE='".$wgui."')' ></INPUT></td>"; 
    else
      echo "<INPUT TYPE='text' NAME='wgui' size=30 maxlength=20 ></INPUT></td>"; 
	  
	if (!isset($wfes) or $wfes=="")   // Si no esta seteada entonces la inicializo
      $wfes="0000-00-00";
 
    echo "<td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Sello<br></font></b>";   
   	$cal="calendario('wfes','1')";
	echo "<input type='TEXT' name='wfes' size=10 maxlength=10  id='wfes'  value=".$wfes." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfes',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

	echo "<tr><td align=center bgcolor=#C0C0C0 colspan=3><b><font text color=#003366 size=2>Datos De Conciliacion ( Nros de Acta y fechas ) :</font></b><br>";   
    if (isset($wcon))
      echo "<INPUT TYPE='text' NAME='wcon' size=80 maxlength=80 VALUE='".$wcon."')' ></INPUT></td>"; 
    else
      echo "<INPUT TYPE='text' NAME='wcon' size=80 maxlength=80 ></INPUT></td>"; 
	
     // $wproceso y wnit son variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";   

	   if (isset($wid))
	     echo "<INPUT TYPE = 'hidden' NAME='wid' VALUE='".$wid."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wid'></INPUT>"; 
	                    
	   if (isset($windicador))
	     echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='windicador'></INPUT>";    
     
   	echo "<tr><td align=center colspan=6 bgcolor=#C0C0C0>";
   	echo "<input type='submit' value='".$wproceso."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	
   	echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

   	//echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	
   	echo "<tr><td align=center colspan=6 bgcolor=#C0C0C0>";
   	echo "</td></tr>";	


if ( $conf == "on" and isset($wnro) and $wnro<>'' and $wvli <>'' and $wfes<>'0000-00-00' and $windicador <> "PrimeraVez" )   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
  
// invoco la funcion que valida los campos   
if ( ($wproceso == "Grabar" ) or ($wproceso == "Modificar" ) )
  validar_datos($wnro,$wfes,$wgui,$wcon); 
else
  $todok = false;   

	// Otra validacion adicional agregada el 01/03/2016
	$query = "SELECT gloresfec FROM caglores Where gloresfue='87' And gloresdoc=".$wnro;
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    if (odbc_fetch_row($resultadoB))         // Encontro
	{
	   if ($wfes < odbc_result($resultadoB,1)) 
	   {
         $todok = false;
         echo "<font size=3 text color=#CC0000>Fecha de sello no puede ser menor a la fecha de la Respuesta a glosa: ".odbc_result($resultadoB,1);
	   }		 
    }	
  
  if  ($todok) 
  { 
           
     $query = "SELECT * FROM ameglres Where nrores=".$wnro." And factu=".$wfac;
     
     $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
     if (odbc_fetch_row($resultadoB))         // Encontro ==> Modifico 
     {  //Solo modifica el estado
       $query = "Update ameglres SET fecsello='".$wfes."',guia='".$wgui."',acta='".$wcon."' Where nrores=".$wnro." And factu=".$wfac;                                                        
       $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
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
	    echo "<font size=3 text color=#FF0000><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL MODIFICAR DATOS!!!!</MARQUEE></font>";				
	    echo "</td></tr></table><br><br>";
	   }
	  
     } 
     else
     {
	 
        $fecha = date("Y-m-d");
	    $hora = (string)date("H:i:s");	
	    
	    $query = "INSERT INTO ameglres (nrores,factu,saldo,vlrini,fecsello,vlrrecla,estado,fecing,usuent,destino,usurec,radicado,guia,acta)"
                ." VALUES (".$wnro.",".$wfac.",".$wsal.",".$wvli.",'".$wfes."',".$wvlr.",'".$west."','".$fecha."','.','.','.','P','".$wgui."','".$wcon."')";        
				
        $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
        if ($resultado)
          echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Adicionado</td></tr>";
	    else
	    {
	     echo "<table border=1>";	 
	     echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	     echo "<font size=3 text color=#FF0000><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL ADICIONAR DATOS, POSIBLEMENTE YA ESTA REGISTRADO ESTA RESPUESTA!!!! </MARQUEE></font>";				
	     echo "</td></tr></table><br><br>";
        }
	  
     } 
   }  
   else
   {
      if ($windicador <> "PrimeraVez") 	     
	  {
	    
		if  ($wproceso == "Borrar" )
        {
	      $query = "DELETE FROM ameglres WHERE nrores=".$wnro." And factu=".$wfac;
	
          $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
          if ($resultado)
           echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Borrado</td></tr>";
        }
        else
        {		
         echo "<table border=1>";	 
         echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
         echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, EN LOS DATOS DIGITADOS!!!!</MARQUEE></font>";				
         echo "</td></tr></table><br><br>";
        }
      }
    }	  
   echo "</center></table>";  
  
}

if ($windicador == "PrimeraVez" ) 	
{
   //Si no controlo con esta variable siempre que le den editar en el programa que lo llama este entra modificando   
   $windicador = "SegundaVez";
   echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
}
echo "</Form>";	    
odbc_close($conexN);
odbc_close_all();   
?>
</BODY>
</html>