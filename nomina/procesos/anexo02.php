<html>
<head>
<title>Actualizacion de datos para retencion en la fuente ANEXO 2</title>
</head>

<script>
    function ira()
    {
	 document.anexo02.wdep.focus();
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
		document.forms.anexo02.submit();   // Ojo para la funcion anexo02 <> Anexo02  (sencible a mayusculas)
	}

	function vaciarCampos()
	{document.forms.anexo02.wdep.value = '';
     document.forms.anexo02.wnro6.value = '';
	 document.forms.anexo02.wnro7.value = '';
	 document.forms.anexo02.wnro8.value = '';
	 document.forms.anexo02.went.value = '';
	 document.forms.anexo02.wvlr.value = '';
    }
    
 	// Fn que solo deja digitar los nros del 0 al 9, el . 
	function numeros(e)
	{
    key = e.keyCode || e.which;
    tecla = String.fromCharCode(key).toLowerCase();
    letras = " 0123456789";
    especiales = [8,37,39,46];
 
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
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Actualizacion de datos para retencion en la fuente ANEXO 2                                                                 
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Marzo 24 de 2015
//FECHA ULTIMA ACTUALIZACION  :Marzo 24 de 2015

$wactualiz="PROGRAMA: anexo02.php Ver. Marzo 24 de 2015  JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

Function validar_datos($dep,$re1,$re2,$re3) 

{  global $todok;
   global $msgerr;
   
   $todok = true;
   $msgerr = "";
   
   if ( trim($dep) == "" )
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar el nro de dependientes o cero si no tiene.";   
   }
       
   if (empty($re1))
   {
      $todok = false; 
      $msgerr=$msgerr." La pregunta nro 2 debe tener respuesta.";
   }
   
    if (empty($re2))
   {
      $todok = false; 
      $msgerr=$msgerr." La pregunta nro 3 debe tener respuesta.";
   }

    if (empty($re3))
   {
      $todok = false; 
      $msgerr=$msgerr." La pregunta nro 4 debe tener respuesta.";
   }
   
   //echo "<font size=3 text color=#CC0000>".$msgerr;   
   //return $todok;   
   
   return $msgerr;   
}  



mysql_select_db("matrix") or die("No se selecciono la base de datos");  
$conexN = odbc_connect('nomina','','') or die("No se realizo Conexion con la BD suministros en Informix");

echo "<form name='anexo02' action='anexo02.php' method=post>";  

// Almaceno el Id del registro enviado
$wid=trim($wid); 

    echo "<center><table border=1>";

    echo "<td align=center bgcolor=#90C0CE colspan=1><b>";
	echo "<font text color=#003366 size=2>ANEXO 2<br>";
    echo "<font text color=red size=2>(ÚNICAMENTE PARA EMPLEADOS CON INGRESOS SUPERIORES A $3,000,000)<br>";
    echo "</font></b><br></td>";
	
	$key = substr($user,2,strlen($user));

// Para Pruebas	
// $key = "07012";

	 if (strlen($key)==5)
	  $codigo=$key;
	 else
	  $codigo=substr($key,2,5);
	  
	  //Tomo los datos de NOPER
	  $query="Select perced,perno1,perno2,perap1,perap2 "
           ." From noper Where percod = '".$codigo."'";

	  $resultado = odbc_do($conexN,$query);                    // Ejecuto el query  
      if (odbc_fetch_row($resultado))                          // Encontro 
	  {
        $wnom=TRIM(odbc_result($resultado,2))." ".TRIM(odbc_result($resultado,3))." ".TRIM(odbc_result($resultado,4))." ".TRIM(odbc_result($resultado,5));
	    $wced=odbc_result($resultado,1);
	  }	

	  //Con esa Cedula busco si ya lleno diligencio el Anexo 2
	  $anio = date("Y");                                        // Y=Devuelve 4 digitos (2015) y=devuelve 2 digitos(15)
	  $query = "SELECT anxano,anxced,anxciu,anxfec,anxnom,anxre1,anxre2,anxdep,anxsal,anxviv,anxafc,anxent,anxvlr"
	         ." FROM nomina_000019 Where anxano='".$anio."' And anxced=".$wced;	
		
	  $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
	  if (($nroreg > 0)  and ($windicador == "PrimeraVez"))                               // Ya lleno el anexo 2
	  {
       $registro = mysql_fetch_row($resultado);  
	   $wciu=$registro[2];
	   $wfec=$registro[3];
	   $wnom=$registro[4];
	   $wced=$registro[1];
	   $wdep=$registro[7];
	   $wnro6=$registro[8];
	   $wnro7=$registro[9];
	   $wnro8=$registro[10];
	   $went=$registro[11];
	   $wvlr=$registro[12];
	   
	   if ($registro[11] == "1" or $registro[11] == "2" )  //Si esta pregunta tiene respuesta es porque ya lleno el anexo2 
	     echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='../reportes/boletincolilla.php?wemp_pmla=01'><font face='serif' color='blue'>::Ir a la colilla de pago::</a></font>";
	    
		//echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='http://mx.lasamericas.com.co/matrix/nomina/reportes/000001_rep3.php?wemp_pmla=01'><font face='serif' color='blue'>::Ir a la colilla de pago::</a></font>";
	  }

	
	echo "<tr><td align=center bgcolor=#90C0CE colspan=1><b><font text color=#003366 size=3>Ciudad:</font></b>";
    if (isset($wciu))
      echo "<INPUT TYPE='text' NAME='wciu' size=30 maxlength=30 readonly='readonly' onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$wciu."' ></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wciu' size=30 maxlength=30 readonly='readonly' onKeyUp='this.value = this.value.toUpperCase();' VALUE='MEDELLIN'></INPUT>"; 

   if (!isset($wfec) or $wfec=="")   // Si no esta seteada entonces la inicializo
    $wfec=date("Y-m-d");
  
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha: ";   
   	$cal="calendario('wfec','1')";
	echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=".$wfec." class=tipo3><button id='trigger2' disabled onclick=".$cal.">...</button>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	  
	echo "<br><br>Yo, "; 
    if (isset($wnom))
      echo "<INPUT TYPE='text' NAME='wnom' size=60 maxlength=60 disabled onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$wnom."' )' ></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wnom' size=60 maxlength=60 disabled onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 
    	
    echo "<br><br>Identificado con cédula de ciudadanía No. ";
    if (isset($wced))
      echo "<INPUT TYPE='text' NAME='wced' size=15 maxlength=15 disabled VALUE='".$wced."' onkeypress='return numeros(event);'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wced' size=15 maxlength=15 disabled onkeypress='return numeros(event);'></INPUT>"; 

	echo "<tr><td align=LEFT bgcolor=#90C0CE colspan=1><font text color=#003366 size=3>";
	echo "Declaro bajo la gravedad de juramento que de  acuerdo con lo estipulado en el Artículo 329 del Estatuto Tributario";
	echo "<br> los Artículos 1º. Y 3° del Decreto 1070 de 2013, y Artículo 6 decreto 3032 del 2013 lo siguiente: ";
	
	echo "<br><br>1.	DEPENDIENTES."; 
    echo "<br>Número de personas a mi cargo ";
    if (isset($wdep))
      echo "<INPUT TYPE='text' NAME='wdep' align=center size=3 maxlength=2 VALUE='".$wdep."' onkeypress='return numeros(event);'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wdep' align=center size=3 maxlength=2 onkeypress='return numeros(event);'></INPUT>"; 
	echo " (ver definición de dependientes en <a class='p' href='leame01.pdf' TARGET='_blank'><font face='serif' color=#003366>Informacion Adicional)</a></font></b>";
	echo "<br> <b>Adjuntar certificados.</b>";
	echo "<br>";
	
	echo "<br>";
	echo "2. ANEXA CERTIFICADO POR SALUD PREPAGADA-POLIZA DE  SALUD.";
	echo "<br>";
	
	// para que se sostenga la seleccion hecha despues de un submit entonces:
   
	  if ( isset($wnro6) and ($wnro6 != '') )  
	  {
	    if ( $wnro6 == "1")  
        {
	     echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 1 CHECKED>Si</INPUT>";
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
		 echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 2></INPUT>No";        
	    } 
		else 
        { 
   	     echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 1>Si</INPUT>";
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";  
		 echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 2 CHECKED>No</INPUT>";        
        }
	  }	
	  else
	  {
	   echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 1>Si</INPUT>";
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
	   echo "<INPUT TYPE = 'Radio' NAME = 'wnro6' VALUE = 2></INPUT>No";        
	  }
	  
	echo "<br><b>Adjuntar la certificación del pago realizado durante el ".(date("Y") - 1).", emitido por la respectiva entidad.</b><br>";
	
	echo "<br>";
	echo "3. CERTIFICADO POR PAGOS DE INTERESES SOBRE PRÉSTAMOS DE  VIVIENDA.";
	echo "<br>";
	
	  if (isset($wnro7) and ($wnro7 != ''))  
	  {
	    if ( $wnro7 == "1")  
        {
	     echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 1 CHECKED>Si</INPUT>";
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
		 echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 2></INPUT>No";        
	    } 
		else 
        {
   	     echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 1>Si</INPUT>";
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
		 echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 2 CHECKED>No</INPUT>";        
        }
	  }	
	 else	
	 {
	   echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 1>Si</INPUT>";
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
	   echo "<INPUT TYPE = 'Radio' NAME = 'wnro7' VALUE = 2>No</INPUT>";    
	 }
	echo "<br><b>Adjuntar la certificación del pago realizado durante el ".(date("Y") - 1).", emitido por la respectiva entidad bancaria.</b>";
	 
	echo "<br>";
    echo "<br>4. ANEXO CERTIFICADO POR AHORROS EN CUENTAS  AFC";	
	echo "<br>";
	
	
	  if ( isset($wnro8) and ($wnro8 != '') )  
	  {
	    if ( $wnro8 == "1")   
        {
	     echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 1 CHECKED>Si</INPUT>";
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
		 echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 2></INPUT>No";        
	    } 
		else 
        {
   	     echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 1>Si</INPUT>";
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
		 echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 2>No</INPUT>";
		}
	  }
	  else
	  {
	    echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 1>Si</INPUT>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
		echo "<INPUT TYPE = 'Radio' NAME = 'wnro8' VALUE = 2>No</INPUT>"; 
	  }
	
	echo "<br><b>Adjuntar la certificación del pago realizado durante el ".(date("Y") - 1).", emitido por la respectiva entidad bancaria.</b>";
	
	
	echo "<br>";
    echo "<br>5.  APORTES VOLUNTARIOS A FONDOS DE PENSIONES.";
	
    echo "<br>Entidad ";
    if (isset($went))
      echo "<INPUT TYPE='text' NAME='went' size=60 maxlength=50 onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$went."'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='went' size=60 maxlength=50 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 
	echo "  valor $ ";
    if (isset($wvlr))
      echo "<INPUT TYPE='text' NAME='wvlr' size=20 maxlength=20 onkeypress='return numeros(event);' VALUE='".$wvlr."'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wvlr' size=20 maxlength=20 onkeypress='return numeros(event);'></INPUT>"; 
	
	echo "<br>";	
	
	echo "<tr><td align=center colspan=6 bgcolor=#90C0CE>";
   	echo "<input type='submit' value='Enviar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	//echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
    //echo "<input type='button' value='Regresar' onClick='history.go(-1);'>";
   	
   	echo "</td></tr>";	
	 
	if ( $conf == "on" )   
    {
	  validar_datos($wdep,$wnro6,$wnro7,$wnro8);
	  
	  if (strlen( $msgerr ) > 5 )    //Si retorna de la funcion con mensaje entonces lo muestro
       print "<script>alert('$msgerr')</script>";
	  else
	  {
	   $anio = date("Y");	
	   $query = "SELECT * FROM nomina_000019 Where anxano='".$anio."' And anxced=".$wced;
	   $resultado = mysql_query($query);
       $nroreg = mysql_num_rows($resultado);
	   if ($nroreg > 0) 
	   {
	    $fecha = date("Y-m-d");
	    $hora = (string)date("H:i:s");

   	    $query = "UPDATE nomina_000019 SET anxdep=".$wdep.",anxsal='".$wnro6."',anxviv='".$wnro7."',anxafc='".$wnro8."',anxent='".$went."',anxvlr=".$wvlr;
		$query = $query." WHERE anxano='".$anio."' And anxced=".$wced;
	
		$resultado = mysql_query($query,$conex);  
	     if ($resultado)
		 {	 
		  print "<script>alert('Su informacion ha sido enviada correctamente!!!!')</script>";
		  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='../reportes/boletincolilla.php?wemp_pmla=01'><font face='serif' color='blue'>::Ir a la colilla de pago::</a></font>";
    	  //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='http://mx.lasamericas.com.co/matrix/nomina/reportes/000001_rep3.php?wemp_pmla=01'><font face='serif' color='blue'>::Ir a la colilla de pago::</a></font>";
		 }
		 else
		  print "<script>alert('Atencion!!! Se produjo un error al enviar la informacion')</script>";
	   }	
	   else
	     print "<script>alert('Atencion!!! Primero debe diligenciar el Anexo 1')</script>";   
	  } 
	}
	echo "</table>";
echo "</Form>";	    
odbc_close($conexN);
odbc_close_all();
?>
</BODY>
</htm