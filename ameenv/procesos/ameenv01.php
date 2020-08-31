<html>
<head>
<title>Actualizacion Control de Entrega de cuentas de cobro o cartas de envio</title>
</head>

<script>
    function ira()
    {
	 document.ameenv01.wnro.focus();
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
		document.forms.ameenv01.submit();   // Ojo para la funcion ameenv01 <> ameenv01  (sencible a mayusculas)
	}

	function vaciarCampos()
	{document.forms.ameenv01.wnro.value = '';
	 document.forms.ameenv01.went.value = '';
     document.forms.ameenv01.wdes.value = '';
     document.forms.ameenv01.wfec.value = '';
     document.forms.ameenv01.wrec.value = '';
	 document.forms.ameenv01.wcon.value = '';
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
//PROGRAMA				      :Actualizacion Control de Entrega de cuentas de cobro o cartas de envio                                                                  
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Enero 21 de 2013
//FECHA ULTIMA ACTUALIZACION  :Febrero 13 de 2013,  Abril 2 de 2016 se adiciono campo Acta de conciliacion                                                                           
//FECHA ULTIMA ACTUALIZACION  :Mayo 28 de 2019	Edwin MG. Se corrige inicialización de variable al editar registro

$wactualiz="PROGRAMA: ameenv01.php Ver. 2013-01-21   JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

Function validar_datos($nro,$ent,$des,$fec,$rec,$gui,$con) 

{  global $todok;
   
   $todok = true;
   $msgerr = "";
   
   if (empty($nro))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir nro de envio o cuenta de cobro. ";  
   }
 
   
   if (empty($ent))
   {
      $todok = false;
      $msgerr=$msgerr." Debe seleccionar usuario que entrega. ";   
   }
   
   if (empty($des))
   {
      $todok = false;
      $msgerr=$msgerr." Debe seleccionar el destino de la cuenta de cobro. ";   
   }
   
    
    // Como permite 30 dias mas a la fecha actual entonces
    $wfecok=date('Y-m-d h:i:s', strtotime("+30 day"));
    if ($fec > $wfecok) 
    {
     $todok = false;     
     $msgerr=$msgerr." Fecha de radicacion no debe ser posterior a ".$wfecok;   
    }
    else
    {  
     if ($fec=="0000-00-00")
     {
      $todok = false;     
      $msgerr=$msgerr." Debe existir una fecha de sello valida. ";   
     }
    } 
     
   if (empty($rec))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe seleccionar usuario que recibe. ";
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

echo "<form name='ameenv01' action='ameenv01.php' method=post>";  

// Almaceno el Id del registro enviado
$wid=trim($wid); 

echo "<center><table border=1>";

if ($wproceso == "Modificar" )
 echo "<td align=center colspan=2 bgcolor=#99CCCC><font size=3 text color=#FF0000><b>Actualizacion Carta de Envio Nro: ".$wid."</b></font></tr>";
else
 echo "<td align=center colspan=2 bgcolor=#99CCCC><font size=3 text color=#FF0000><b>Actualizacion entrega de cartas de envio</b></font></tr>";

if ($windicador == "PrimeraVez") 
{
 if ( ($wproceso == "Modificar" )  or ($wproceso == "Borrar" ) )	
  {
   $query = "SELECT envahnro,envahent,envahdes,envahfec,envahrec,envahgui,envahcon FROM ameenvah Where envahnro=".$wid;
   $resultadoB = odbc_exec($conexN,$query);            // Ejecuto el query
   
   if (odbc_fetch_row($resultadoB))                    // Encontro 
   {
    $wnro=odbc_result($resultadoB,1);
    $went=odbc_result($resultadoB,2);
    $wdes=odbc_result($resultadoB,3);
    $wfec=odbc_result($resultadoB,4);
    $wrec=odbc_result($resultadoB,5);
	$wgui=odbc_result($resultadoB,6);
	$wcon=odbc_result($resultadoB,7);
    //$query="Select Count(*) TOTFAC from ahdocact where docactenv=".$wid;
    $query="Select envencreg,envencnit,empnom From caenvenc,inemp"
          ." Where envencfue='80' And envencdoc=".$wid." And envencnit=empcod";
    $resultadoC = odbc_do($conexN,$query);    // Ejecuto el query  
    if (odbc_fetch_row($resultadoC))         // Encontro ==> Modifico 
     echo "<font size=3 text color=#0066FF>Nro de Facturas: ".odbc_result($resultadoC,1)." Empresa ".odbc_result($resultadoC,3);  
     
   }
  } 
}
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Nro de envio</font></b><br>";
    if ($wnro>0)
    {
	   echo "<INPUT TYPE='text' NAME='wnro' size=30 maxlength=20 VALUE='".$wnro."' onkeypress='teclado()' ></INPUT></td>"; 
       if ( ($windicador <> "PrimeraVez") )
       { 
	     if ($wproceso=="Nuevo")
	     {
		   $query = "SELECT Count(*) NROENV FROM ameenvah Where envahnro=".$wnro; 
	       $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
           if (odbc_result($resultadoC,1) > 0)  
           {
	        //echo "<font size=3 text color=#CC0000>Este envio ya fue relacionado...";
	        $mensaje = "Error!!! este envio ya fue relacionado...";
	        print "<script>alert('$mensaje')</script>";
	        $todok = false; 
           } 
	       else
	       { $query="Select Count(*) TOTFAC from ahdocact where docactenv=".$wnro;
	         $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
             if (odbc_result($resultadoC,1) > 0)  
             {          
               $query="Select envencreg,envencnit,empnom From caenvenc,inemp"
                     ." Where envencfue='80' And envencdoc=".$wnro." And envencnit=empcod";
               $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
               if (odbc_fetch_row($resultadoC))         // Encontro ==> Modifico 
                 echo "<font size=3 text color=#0066FF>Nro de Facturas: ".odbc_result($resultadoC,1)." Empresa ".odbc_result($resultadoC,3); 
             }  
             else
             {
               //echo "<font size=3 text color=#CC0000>Error!!! nro de envio no existe...";
               $mensaje = "Error!!! nro de envio no existe...";
               print "<script>alert('$mensaje')</script>";
               $todok = false; 
             } 

           }  
               
         } 
       } 
     } 
      else
        echo "<INPUT TYPE='text' NAME='wnro' size=30 maxlength=20 onkeypress='teclado()' OnBlur='enter()'></INPUT></td>"; 
    
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Usuario que entrega:</font></b><br>";   
    $query = "SELECT usuahcod, usuahnom FROM ameusuah WHERE usuahtip = 'E' And usuahare='ADMDOC'  ORDER BY usuahcod";   
    echo "<select name='went'>"; 
    echo "<option></option>"; 
    
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    $i = 1;
    while (odbc_fetch_row($resultadoB))               // leo registro 
	  {
		$c1=explode('-',$went); 				  
  		if($c1[0] == odbc_result($resultadoB,1))
 	      echo "<option selected>".odbc_result($resultadoB,1)."- ".odbc_result($resultadoB,2)."</option>";
	    else
	      echo "<option>".odbc_result($resultadoB,1)."- ".odbc_result($resultadoB,2)."</option>"; 
	    $i++; 
      }   
     echo "</select></td>";
     
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Destino:</font></b><br>";   
    $query = "SELECT arccod, arcnom FROM aharc WHERE arcact = 'A' ORDER BY arcnom";   
    
    echo "<select name='wdes' OnBlur='enter()' >"; 
    echo "<option></option>"; 
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    $i = 1;
    while (odbc_fetch_row($resultadoB))               // leo registro 
    {   // COMO arccod NO ES DEL MISMO TIPO Y LONGITUD DEL STRING QUE QUEDA EN $c2[0] no lo comparo con ==
        // SI NO QUE UTILIZO LA Fn substr_count QUE BUSCA EL STRING $c2[0] EN EL STGRING arccod SI DA 1 ...
	    $c2=explode('-',$wdes); 	
		$s = substr_count(odbc_result($resultadoB,1), $c2[0]); 
  		if ( $s==1 )
 	     echo "<option selected>".odbc_result($resultadoB,1)."- ".(odbc_result($resultadoB,2))."</option>";
	    else
	     echo "<option>".odbc_result($resultadoB,1)."- ".(odbc_result($resultadoB,2))."</option>"; 
	    
	    $i++; 
    }   
    echo "</select></td>";
    
    
  if (!isset($wfec) or $wfec=="")   // Si no esta seteada entonces la inicializo
    $wfec="0000-00-00";
  
    echo "<td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha del Sello<br></font></b>";   
   	$cal="calendario('wfec','1')";
	echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=".$wfec." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Usuario que recibe:</font></b><br>";   
    $query = "SELECT usuahcod, usuahnom FROM ameusuah WHERE usuahtip = 'R' And usuahare='".$c2[0]."' ORDER BY usuahcod";   
    
    echo "<select name='wrec'>"; 
    echo "<option></option>"; 
    
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    $i = 1;
    while (odbc_fetch_row($resultadoB))               // Leo registro 
	  {
		$c3=explode('-',$wrec); 				  
  		if($c3[0] == odbc_result($resultadoB,1))
 	      echo "<option selected>".odbc_result($resultadoB,1)."- ".odbc_result($resultadoB,2)."</option>";
	    else
	      echo "<option>".odbc_result($resultadoB,1)."- ".odbc_result($resultadoB,2)."</option>"; 
	    $i++; 
      }   
     echo "</select></td>";
     
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Firma digital</font></b><br>";
    if (isset($wfir))
      echo "<INPUT TYPE='password' NAME='wfir' size=10 maxlength=6 VALUE='".$wfir."')' ></INPUT></td>"; 
    else
      echo "<INPUT TYPE='password' NAME='wfir' size=10 maxlength=6 ></INPUT></td>"; 
 
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Nro de Guia o Radicacion:</font></b><br>";   
    if (isset($wgui))
      echo "<INPUT TYPE='text' NAME='wgui' size=30 maxlength=25 VALUE='".$wgui."')' ></INPUT></td>"; 
    else
      echo "<INPUT TYPE='text' NAME='wgui' size=30 maxlength=25 ></INPUT></td>"; 
    
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Datos De Conciliacion ( Nros de Acta y fechas ) :</font></b><br>";   
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

   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	
   	echo "<tr><td align=center colspan=6 bgcolor=#C0C0C0>";
   	echo "</td></tr>";	


if ( $conf == "on" and isset($wnro) and $wnro<>'' and isset($went) and $went<>'' and isset($wdes) and $wdes<>'' and $windicador <> "PrimeraVez" )   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////

  // invoco la funcion que valida los campos  
  if ( ($wproceso == "Grabar" ) or ($wproceso == "Modificar" ) )
  {
    validar_datos($wnro,$went,$wdes,$wfec,$wrec,$wgui,$wcon); 
	
    // Validacion adicional para la firma digital
	// $todok = false; //Lo dejo en false para que siempre valide la firma
    if (!empty($wfir))
    {
     if (!empty($wrec)) 
     {
		$c3=explode('-',$wrec);   
		$query = "SELECT Password FROM usuarios Where Codigo IN ('$c3[0]','01$c3[0]')";
		$resultadoC = mysql_query($query);     // Ejecuto el query 
		$nroreg = mysql_num_rows($resultadoC);
		if( $row = mysql_fetch_array($resultadoC))         // Encontro 
		{
			// Busca la firma digitada en la firma leida de la base de datos
			//$s = substr_count(mysql_result($resultadoC,0), $wfir); 
			//if ( $s==0 )  // No lo encontro
			 if( $row['Password'] == $wfir)
			 {
				$todok = true; 
			 }
			 else{
				echo "<font size=3 text color=#CC0000>La Firma digital no es valida."; 
			 }
		}
		
		// if (mysql_fetch_row($resultadoC))         // Encontro 
		// {
		// // Busca la firma digitada en la firma leida de la base de datos
		// //$s = substr_count(mysql_result($resultadoC,0), $wfir); 
		// //if ( $s==0 )  // No lo encontro
		 // if (mysql_result($resultadoC,0)!=$wfir)
		 // {
		 	// $todok = false; 
		 	// echo "<font size=3 text color=#CC0000>La Firma digital no es valida."; 
		 // }
		// }
	  }
    }

	// Otra validacion adicional agregada el 23/07/2014
	$query = "SELECT envahfre,envahrec,envahfra FROM ameenvah Where envahnro=".$wnro." And envahfre<>'' ";
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    if (odbc_fetch_row($resultadoB))         // Encontro
	{
      $todok = false;
      echo "<font size=3 text color=#CC0000>Este nro de envio fue recibido el ".odbc_result($resultadoB,1)." por (".odbc_result($resultadoB,2).") y radicado por cartera el ".odbc_result($resultadoB,3); 
    }
	
	// Otra validacion adicional agregada el 01/03/2016
	$query = "SELECT envencfec FROM caenvenc Where envencfue='80' And envencdoc=".$wnro;
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    if (odbc_fetch_row($resultadoB))         // Encontro
	{
	   if ($wfec < odbc_result($resultadoB,1)) 
	   {
         $todok = false;
         echo "<font size=3 text color=#CC0000>Fecha de sello no puede ser menor a la fecha de la carta de cobro: ".odbc_result($resultadoB,1);
	   }		 
    }	
	
  }
  
  if ($todok) 
  { 
    if (isset($went)) 
     $c1=explode('-',$went);     // De los combos tomo los codigos
    if (isset($wdes)) 
     $c2=explode('-',$wdes);     
    if (isset($wrec)) 
     $c3=explode('-',$wrec);     
          
     $query = "SELECT * FROM ameenvah Where envahnro=".$wnro;
     
     $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
     if (odbc_fetch_row($resultadoB))         // Encontro ==> Modifico 
     {
	   if (!empty($wfir))   // Si Tiene firma actualiza todos los datos hasta el estado  
        $query = "Update ameenvah SET envahent='".$c1[0]."',envahdes='".$c2[0]."',envahfec='".$wfec."',envahrec='".$c3[0]."',"
                ."envahrad='N',envahgui='".$wgui."',envahcon='".$wcon."' Where envahnro=".$wnro;
       else                  // De lo contrario no actualiza el estado
        $query = "Update ameenvah SET envahent='".$c1[0]."',envahdes='".$c2[0]."',envahfec='".$wfec."',envahrec='".$c3[0]."',envahgui='".$wgui."',envahcon='".$wcon."'"
                ." Where envahnro=".$wnro;
                                                               
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
	 
      $query="Select envencreg,envencnit,empnom From caenvenc,inemp"
            ." Where envencfue='80' And envencdoc=".$wnro." And envencnit=empcod";
      $resultadoC = odbc_do($conexN,$query);   // Ejecuto el query  
      if (odbc_fetch_row($resultadoC))         // Encontro 
      {
        $fecha = date("Y-m-d");
	    $hora = (string)date("H:i:s");	
	    if (!empty($wfir))   // Si Tiene firma adiciona con estado N y fecha de recibido
	      $query = "INSERT INTO ameenvah (envahnro,envahent,envahdes,envahfec,envahrec,envahrad,envahusr,envahgui,envahcon,envahfad,envahfre)"
                  ." VALUES (".$wnro.",'".$c1[0]."','".$c2[0]."','".$wfec."','".$c3[0]."','N','".$user."','".$wgui."','".$wcon."','".$fecha." ".$hora."','".$fecha." ".$hora."')";  
	    else
	      $query = "INSERT INTO ameenvah (envahnro,envahent,envahdes,envahfec,envahrec,envahrad,envahusr,envahgui,envahcon,envahfad)"
                ." VALUES (".$wnro.",'".$c1[0]."','".$c2[0]."','".$wfec."','".$c3[0]."','P','".$user."','".$wgui."','".$wcon."','".$fecha." ".$hora."')";  
          
        $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
        if ($resultado)
          echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Adicionado</td></tr>";
	    else
	    {
	     echo "<table border=1>";	 
	     echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	     echo "<font size=3 text color=#FF0000><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL ADICIONAR DATOS, POSIBLEMENTE YA ESTA REGISTRADO ESTE NRO DE ENVIO!!!! </MARQUEE></font>";				
	     echo "</td></tr></table><br><br>";
        }
	  } 
     } 
   }  
   else
    {
      if ($windicador <> "PrimeraVez") 	     
	  {	    
		 if  ($wproceso == "Borrar" )
         {
		  $query = "DELETE FROM ameenvah WHERE envahnro=".$wnro;	
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