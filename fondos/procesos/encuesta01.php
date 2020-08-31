<html>
<head>
<title>Actualizacion de datos para asociados al Fondo de empleados</title>
</head>

<script>
    function ira()
    {
	 document.encuesta01.wap1.focus();
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
		document.forms.encuesta01.submit();   // Ojo para la funcion encuesta01 <> Encuesta01  (sencible a mayusculas)
	}

	function vaciarCampos()
	
	{document.forms.encuesta01.wced.value = '';
	 document.forms.encuesta01.wap1.value = '';
     document.forms.encuesta01.wap2.value = '';
     document.forms.encuesta01.wno1.value = '';
     document.forms.encuesta01.wno2.value = '';
	 document.forms.encuesta01.wnac.value = '';
	 document.forms.encuesta01.wsex.value = '';
	 document.forms.encuesta01.wdir.value = '';
	 document.forms.encuesta01.wmun.value = '';
	 document.forms.encuesta01.wbar.value = '';
	 document.forms.encuesta01.west.value = '';
	 document.forms.encuesta01.wtel.value = '';
	 document.forms.encuesta01.wcel.value = '';
	 document.forms.encuesta01.wcoe.value = '';
	 document.forms.encuesta01.wedu.value = '';
	 
	 document.forms.encuesta01.wcv1.value = '';
	 document.forms.encuesta01.wcv2.value = '';
	 document.forms.encuesta01.wcv3.value = '';
	 document.forms.encuesta01.wcv4.value = '';
	 document.forms.encuesta01.wcv5.value = '';
	 document.forms.encuesta01.wcv6.value = '';
	 document.forms.encuesta01.wcv7.value = '';
	 document.forms.encuesta01.wcu1.value = '';
	 
	 document.forms.encuesta01.wpac.value = '';
	 
	 document.forms.encuesta01.weci.value = '';
	 document.forms.encuesta01.wtvi.value = '';
	 document.forms.encuesta01.wpvi.value = '';
	 document.forms.encuesta01.wevi.value = '';
	 document.forms.encuesta01.wcu2.value = '';
	 
	 document.forms.encuesta01.wser.value = '';
	 document.forms.encuesta01.wcu3.value = '';
	 
	 document.forms.encuesta01.wtra.value = '';
	 document.forms.encuesta01.wcu4.value = '';
	 
	 document.forms.encuesta01.wact.value = '';
	 document.forms.encuesta01.wcu5.value = '';
	 
	 document.forms.encuesta01.wre1.value = '';
	 document.forms.encuesta01.wre2.value = '';
	 document.forms.encuesta01.wre3.value = '';
	 document.forms.encuesta01.wre4.value = '';
	 document.forms.encuesta01.wre5.value = '';
	 document.forms.encuesta01.wre6.value = '';
	 document.forms.encuesta01.wre7.value = '';
	 document.forms.encuesta01.wre8.value = '';
	 document.forms.encuesta01.wre9.value = '';
	 document.forms.encuesta01.wre10.value = '';
	 document.forms.encuesta01.wcu6.value = '';
	 
	 document.forms.encuesta01.wcom.value = '';
	 document.forms.encuesta01.wco1.value = '';
	 document.forms.encuesta01.wco2.value = '';
	 document.forms.encuesta01.wco3.value = '';
	 
	 document.forms.encuesta01.wcr1.value = '';
	 document.forms.encuesta01.wcr2.value = '';
	 document.forms.encuesta01.wcr3.value = '';
	 document.forms.encuesta01.wcr4.value = '';
	 document.forms.encuesta01.wcr5.value = '';
	 document.forms.encuesta01.wcr6.value = '';
	 document.forms.encuesta01.wcr7.value = '';
	 document.forms.encuesta01.wcu7.value = '';
	 
	 document.forms.encuesta01.wfe1.value = '';
	 document.forms.encuesta01.wfe2.value = '';
	 document.forms.encuesta01.wfe3.value = '';
	 document.forms.encuesta01.wcu8.value = '';
	 
	 document.forms.encuesta01.wfon.value = '';
	 document.forms.encuesta01.wcu8.value = '';
	 
	 document.forms.encuesta01.wde1.value = '';
	 document.forms.encuesta01.wde2.value = '';
	 document.forms.encuesta01.wde3.value = '';
	 document.forms.encuesta01.wde4.value = '';
	 document.forms.encuesta01.wde5.value = '';
	 document.forms.encuesta01.wde6.value = '';
	 document.forms.encuesta01.wde7.value = '';
	 document.forms.encuesta01.wcu10.value = '';
	 
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
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Actualizacion de datos para el fondo de empleados                                                                 
//AUTOR				          :Jair Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Mayo 28 de 2015
//FECHA ULTIMA ACTUALIZACION  :Mayo 28 de 2015.                                                                             

$wactualiz="PROGRAMA: encuesta01.php Ver. Mayo 28 de 2015   JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
	

// Función de encriptación
// Permite enviar la cédula del visitante a la pagina del fondo ecriptada en la URL de la página
	function encrypt($string)
	{
	   return base64_encode($string);
	}
	
Function validar_datos($ced,$ap1,$no1,$nac,$sex,$dir,$mun,$bar,$est,$tel,$cel,$coe,$edu,$acvx,$cu1,$pac,$eci,$tvi,$pvi,$evi,$cu2,$ser,$cu3,$tra,$cu4,$act,$cu5,$arex,$cu6,$com,$acox,$acrx,$cu7,$afex,$cu8,$fon,$cu9,$adex,$cu10) 

 {  
   global $todok;
   global $msgerr;
   
   $todok = true;
   $msgerr = "";
   
   if (empty($ced))
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar el nro de de cedula o documento de identidad; ";   
   }
   if (empty($ap1))
   {
      $todok = false;
      $msgerr=$msgerr." Debe digitar minimo su primer apellido; ";  
   }
   if (empty($no1))
   {
      $todok = false;
      $msgerr=$msgerr." Debe digitar minimo su primer nombre; ";   
   }
   
   // Chequeo la fecha con checkdate(mm,dd,aaaa) pero como la fecha viene yyyy/mm/dd entonces 
   if ( !checkdate(substr($nac,5,2), substr($nac,8,2), substr($nac,0,4)) )
   {   
	 $todok = false;
     $msgerr=$msgerr." Debe especificar una fecha de nacimiento valida; ";  
   }	 
   else
   { 
     $segundos= strtotime('now') - strtotime($nac);
     $dias=intval($segundos/60/60/24);
	 $wanios = (int) $dias / 365;

	 if ( $wanios < 18 ) 
     {		 
	   $todok = false;
       $msgerr=$msgerr." Error en fecha de nacimiento no puede ser menor de edad (".(int) $wanios." Años); ";  
     }   
     else
	 {	 
      if ( $wanios > 90 )	
	  {	  
	   $todok = false;
       $msgerr=$msgerr." Error en fecha de nacimiento, Edad mayor a 90 Años; ";  
	  }
     }	  
   }
   
   if (empty($sex))
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar su genero; ";   
   }
   if (empty($dir))
   {
      $todok = false;
      $msgerr=$msgerr." El campo Direccion no puede ser nulo; ";   
   } 
   if (empty($mun))
   {
      $todok = false;
      $msgerr=$msgerr." El municipio debe ser especificado; ";   
   }
   if (empty($bar))
   {
      $todok = false;
      $msgerr=$msgerr." Debe seleccionar el barrio; ";   
   }
   if (empty($est))
   {
      $todok = false;
      $msgerr=$msgerr." El estrato debe ser especificado; ";   
   }
   if (empty($tel))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir un telefono fijo de contacto o nro de extension de la oficina; ";   
   }
   if (empty($cel))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir un telefono celular de contacto o el de un familiar; ";   
   }   
   
   if (!empty($coe))
   {	   
     if (strlen($coe)<5)
	 {
	  $todok = false;	
	  $msgerr=$msgerr." Direccion de correo electronico Invalido; ";   
	 } 
     else
     {
	   //Validar cuentas de correo electronicos con expresiones regulares
       if(!preg_match('/^[(a-z0-9\_\-\.)]+@[(a-z0-9\_\-\.)]+\.[(a-z)]{2,4}$/i',$coe))
	   {	
	    $todok = false;	
        $msgerr=$msgerr." Direccion de correo electronico Invalido; ";   
       }
	 }    
   }
   
   if (empty($edu))
   {
      $todok = false;
      $msgerr=$msgerr." Debe especificar el grado de educacion; ";   
   }   
    /*****************************************************************************************************************/
    //Esta variable llega en un arreglo
    $wminsel = "";
    for ($i = 1; $i <= 7; $i++)
	{
	    if ( $acvx[$i] == "on" )
		  $wminsel = "1";
	}	   
    if ($wminsel <> "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Minimo debe seleccionar una opcion en  Con Quien Vive?; ";   
    }   
    if ( ( $acvx[7] == "on" ) and ( empty($cu1) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar con Cuales otras personas vive?; ";   	
	}  
    /**********************************************************************************************************************/
	
	if ( $pac == "" )    // No me funciono el empty toco asi sera por el tamño 1 del campo?
    {
      $todok = false;
      $msgerr=$msgerr." Debe especificar el Nro de personas a cargo cero si no tiene; ";   
    }   
	
	if (empty($eci))
    {
      $todok = false;
      $msgerr=$msgerr." El estado Civil no se ha especificado; ";   
    }
	if (empty($tvi))
    {
      $todok = false;
      $msgerr=$msgerr." Debe seleccionar el Tipo de vivienda; ";   
    }
	if (empty($pvi))
    {
      $todok = false;
      $msgerr=$msgerr." La posesion sobre la vivienda no se ha especificado; ";   
    }

	if (empty($evi))
    {
      $todok = false;
      $msgerr=$msgerr." No ha seleccionado el estado de la vivienda; ";   
    }
	if ( ( $evi ==  3 ) and  ( empty($cu2) ) )
    {
      $todok = false;
      $msgerr=$msgerr." Debe especificar Cual? es el otro estado de la vivienda; ";   
    }	    	 

	if (empty($ser))
    {
      $todok = false;
      $msgerr=$msgerr." No ha seleccionado el medio de pago de los servicios publicos; ";   
    }
	if ( ( $ser ==  3 ) and  ( empty($cu3) ) )
    {
      $todok = false;
      $msgerr=$msgerr." Cual es el otro medio de pago de los servicios publicos?; ";   
    }	    
	
	if (empty($tra))
    {
      $todok = false;
      $msgerr=$msgerr." Debe seleccionar el medio de transporte utilizado; ";   
    }
	if ( ( $tra ==  7 ) and  ( empty($cu4) ) )
    {
      $todok = false;
      $msgerr=$msgerr." Cual es el otro medio de transporte?; ";   
    }		
	
    if (empty($act))
    {
      $todok = false;
      $msgerr=$msgerr." Debe especificar La actividad que mas le gusta; ";   
    }
	if ( ( $act ==  7 ) and  ( empty($cu5) ) )
    {
      $todok = false;
      $msgerr=$msgerr." Especifique cual otra actividad es la que mas le gusta?; ";   
    }		
	
	/*******************************************************************************************************/
    //Esta variable llega en un arreglo
    $wminsel = "";
	$werrvlr = "";
    for ($i = 1; $i <= 10; $i++)
	{
	  if ( $arex[$i] <> "" )
	    $wminsel = "1";
	
   	  if ( (int)$arex[$i] > 10 )	  
	    $werrvlr = "1";
	 
	}	   
    if ($wminsel <> "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Minimo debe seleccionar una opcion en que Actividades de Recreacion le gustaria; ";   
    }   
    if ($werrvlr == "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Hay valores fuera de rango en que Actividades de Recreacion le gustaria; ";   
    }   
    if ( ( $arex[10] <> "" ) and ( empty($cu6) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar Cual es la otra Actividad de recreacion que le gustaria encontar; ";   	
	}  
    
	$wrepetido = "";
    for ($i = 1; $i <= 10; $i++)	
	{
	 for ($j = $i+1; $j <= 10; $j++)
	 {	 
       if ( ($arex[$i] == $arex[$j]) and ($arex[$i] <> "") )
		 $wrepetido = "S";	
	 }
	}
	if ($wrepetido == "S")
	{	
	  $todok = false;
      $msgerr=$msgerr." Hay valores repetidos en la otra Actividad de recreacion que le gustaria encontar; ";   	
	}  
	/*******************************************************************************************************/
	
	if (empty($com))
    {
      $todok = false;
      $msgerr=$msgerr." Debe especificar si posee computador; ";   
	}
	else
    {
	  $wminsel = "";
      for ($i = 1; $i <= 3; $i++)
	  {
	    if ( $acox[$i] == "on" )
		 $wminsel = "1";  
	  }
      if ( ($com==1)  and ($wminsel <> "1") )
      {  
        $todok = false;
        $msgerr=$msgerr." Minimo debe seleccionar que computador tienes; ";   
      }   
	  else
	  {
		  if ( ($wminsel == "1") and ($com==2) )
		  {
		    $todok = false;
            $msgerr=$msgerr." Humm!! Parece que si tienes computador; ";   
		  }
	  }
	}
	
    /*******************************************************************************************************/
    //Esta variable llega en un arreglo
    $wminsel = "";
	$werrvlr = "";
    for ($i = 1; $i <= 7; $i++)
	{
	  if ( $acrx[$i] <> "" )
	    $wminsel = "1";
	
   	  if ( (int)$acrx[$i] > 7 )	  
	    $werrvlr = "1";
	 
	}	   
    if ($wminsel <> "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Minimo debe calificar que otras lines de credito le gustaria; ";   
    }   
    if ($werrvlr == "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Hay valores fuera de rango en que Otras Lineas de Credito le Gustaria; ";   
    }   
    if ( ( $acrx[7] <> "" ) and ( empty($cu7) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar Cual es la otra linea de credito que le gustaria; ";   	
	}  
    
	$wrepetido = "";
    for ($i = 1; $i <= 7; $i++)	
	{
	 for ($j = $i+1; $j <= 7; $j++)
	 {	 
       if ( ($acrx[$i] == $acrx[$j]) and ($acrx[$i] <> "") )
		 $wrepetido = "S";	
	 }
	}
	if ($wrepetido == "S")
	{	
	  $todok = false;
      $msgerr=$msgerr." Hay valores repetidos al Enumerar Otras Lineas de Credito; ";   	
	}  
	/*******************************************************************************************************/
    //Esta variable llega en un arreglo
    $wminsel = "";
    for ($i = 1; $i <= 3; $i++)
	{
	    if ( $afex[$i] == "on" )
		  $wminsel = "1";
	}	   
    if ($wminsel <> "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Minimo debe seleccionar una opcion en que otras ferias le gustaria; ";   
    }   
    if ( ( $afex[3] == "on" ) and ( empty($cu8) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar Cuales otras ferias le gustaria; ";   	
	}  
    /**********************************************************************************************************/

	if (empty($fon))
    {
      $todok = false;
      $msgerr=$msgerr." No ha seleccionado si esta de acuerdo con la creacion del fondo de solidaridad; ";   
    }
	if ( ( $fon ==  2 ) and ( empty($cu9) ) )
    {
      $todok = false;
      $msgerr=$msgerr." Debe especificar Porque no esta de acuerdo con la creacion del fondo de solidaridad; ";   
    }	    	 
	

    /*******************************************************************************************************/
    //Esta variable llega en un arreglo
    $wminsel = "";
	$werrvlr = "";
    for ($i = 1; $i <= 7; $i++)
	{
	  if ( $adex[$i] <> "" )
	    $wminsel = "1";
	
   	  if ( (int)$adex[$i] > 7 )	  
	    $werrvlr = "1";
	 
	}	   
    if ($wminsel <> "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Minimo debe enumerar que destinacion sugiere para el fondo de solidaridad; ";   
    }   
    if ($werrvlr == "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Hay valores fuera de rango en que destinacion sugiere para el fondo de solidaridad; ";   
    }   
    if ( ( $adex[7] <> "" ) and ( empty($cu10) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar Cual es la otra destinacion que sugiere para el fondo de solidaridad; ";   	
	}  
    
	$wrepetido = "";
    for ($i = 1; $i <= 7; $i++)	
	{
	 for ($j = $i+1; $j <= 7; $j++)
	 {	 
       if ( ($adex[$i] == $adex[$j]) and ($adex[$i] <> "") )
		 $wrepetido = "S";	
	 }
	}
	if ($wrepetido == "S")
	{	
	  $todok = false;
      $msgerr=$msgerr." Hay valores repetidos al Enumerar que destinacion sugiere para el fondo de solidaridad; ";   	
	}
	
	  if ( (int)$pac > 0 )	   // Tiene personas a cargo   
	  {
	   $query = "SELECT * FROM fondos_000098 Where pacced=".$ced;
	   $resultado = mysql_query($query);
       $nroreg = mysql_num_rows($resultado);
	   if ((int)$pac <> (int) $nroreg)       // Y no las ha especificado
	   {
	     $todok = false;
         $msgerr=$msgerr." Tiene ".$pac." personas a cargo y no las ha especificado. ";   	
	   }   
	  }
	  
	  
   //echo "<font size=2 text color=#CC0000>".$msgerr;   
   //return $todok;   
   
   return $msgerr;   
 }  

//*************************************
 


//$conex = mysql_connect('localhost','root','q6@nt6m') or die("No se realizo Conexion");       // PARA PRUEBAS LOCALES
mysql_select_db("matrix") or die("No se selecciono la base de datos");  

echo "<form name='encuesta01' action='encuesta01.php' method=post>";  

    echo "<center><table border=1>";

    echo "<td align=center bgcolor=#6699CC colspan=5><b>";
	echo "<font text color='blue' size=2>PERFIL DE ASOCIADOS FONDO DE EMPLEADOS LAS AMERICAS<br>";
    echo "<font text color=#003366 size=2><b>CON MIRAS A DESARROLLAR LOS PLANES DE BIENESTAR DEL FONDO DE EMPLEADOS Y SATISFACER<br> ";
	echo "<font text color=#003366 size=2><b>SUS NECESIDADES Y EXPECTATIVAS, LOS INVITAMOS A ACTUALIZAR LA SIGUIENTE ENCUESTA ";
	echo "</font></b><br></td>";

	
    $key = substr($user,2,strlen($user));
	
//  $key = "07012";    /* Se quita cuando publique */

/*
	 if (strlen($key)==5)
	  $codigo=$key;
	 else
	  $codigo=substr($key,2,5);
*/
  
	if ($windicador == "PrimeraVez")     //Como este programa es llamado por el programa encuesta00.php la primera vez que se ejecute toma el parametro
		$wced=$wcedula;                  //Tando la variable $cedula como $windicador como no las guardo se colocaran en blanco despeus de ejecutado este script

		
	//*************************Para que lo haga solo una vez al Primera vez*******************************************
    // Si hay cedula digitada y no hay primer apellido ni primer nombre busco y muestro si ya tiene registro *
	//********************************************************************************************************
    if ((isset($wced)) and (strlen($wap1)==0) and (strlen($wno1)==0) )
	{        
	  //Con esa Cedula busco si ya lleno diligencio la encuesta	  

      // Lo siguiente lo puedo hacer asi por la forma que tengo de nombrar los campos en la forma y los nombres en la tabla 
	  // Ejemplo la CEDULA en la forma wced en la tabla encced 
	  
      // Campos a un arreglo
      $campos= array("encced","encap1","encap2","encno1","encno2","encnac","encsex","encdir","encmun","encbar","encest","enctel","enccel",
	        "enccoe","encedu","encpac","enccv1","enccv2","enccv3","enccv4","enccv5","enccv6","enccv7","enccu1","enceci","enctvi","encpvi","encevi","enccu2","encser",
			"enccu3","enctra","enccu4","encact","enccu5","encre1","encre2","encre3","encre4","encre5","encre6","encre7","encre8","encre9","encre10","enccu6","enccom",
            "encco1","encco2","encco3","enccr1","enccr2","enccr3","enccr4","enccr5","enccr6","enccr7","enccu7","encfe1","encfe2","encfe3","enccu8","encfon","enccu9",
            "encde1","encde2","encde3","encde4","encde5","encde6","encde7","enccu10");
     
	  // Armo el SELECT		  
	  $query="SELECT ";	
      for ($i = 0; $i < count($campos)-1; $i++)
       $query=$query.$campos[$i].",";
      $query=$query." ".$campos[$i]." FROM fondos_000099 Where encced='".$wced."'";
	  
  	  // Ejecuto el Query
	  $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
	  if ($nroreg > 0) 
	  {  
	    $registro = mysql_fetch_row($resultado);  	
        for ($i = 0; $i < count($campos); $i++)
   	    {
		  if (strlen($campos[$i]) == 6 )
		   $nomcam="w".substr($campos[$i],3,3);             /* ==> TOMO LOS 3 ULTIMOS CARACTERES DEL NOMBRE DEL CAMPO   */  
	      else
		   $nomcam="w".substr($campos[$i],3,4);             /* ==> TOMO LOS 4 ULTIMOS CARACTERES DEL NOMBRE DEL CAMPO   */    
		  
		  $$nomcam=$registro[$i]; 
	    }	            
	  }
	  
	}
	//********************************************************************************************************
	//********************************************************************************************************
	
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Dto de Identidad<br>";
    if (isset($wced))
      echo "<INPUT TYPE='text' NAME='wced' size=15 maxlength=15 VALUE='".$wced."' READONLY onkeypress='return numeros(event);' OnBlur='enter()'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wced' size=15 maxlength=15 READONLY onkeypress='return numeros(event);' OnBlur='enter()'></INPUT>"; 
  
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>1er Apellido<br>";	  
    if (isset($wap1))
      echo "<INPUT TYPE='text' NAME='wap1' size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$wap1."'></INPUT>"; 
    else 
      echo "<INPUT TYPE='text' NAME='wap1' size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();' VALUE=''></INPUT>";	  
	  
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>2do Apellido<br>";	  	
    if (isset($wap2))
      echo "<INPUT TYPE='text' NAME='wap2' size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$wap2."'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wap2' size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();' VALUE=''></INPUT>"; 
	  
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>1er Nombre<br>";	  	
    if (isset($wno1))
      echo "<INPUT TYPE='text' NAME='wno1' size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$wno1."'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wno1' size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();' VALUE=''></INPUT>"; 
	  
	echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>2do nombre<br>";	  
    if (isset($wno2))
      echo "<INPUT TYPE='text' NAME='wno2' size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$wno2."'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wno2' size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();' VALUE=''></INPUT>"; 
	
	echo "</table>";
	
	
	echo "<center><table border=1>";
	echo "<tr><td align=center colspan=4 bgcolor=#6699CC><font size=3 text color=#FFFFFF><b>DATOS GENERALES DEL SOCIO</b></font></tr>";
	echo "<td align=center bgcolor=#C0C0C0 colspan=4><b>";
	
	// Si no esta seteada o esta en blanco la inicializo con fecha actual
    if (!isset($wnac) or $wnac=="")   
      $wnac=date("Y-m-d");
    	
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>F.Nac/to (AAAA-MM-DD)<br>";	  
   	$cal="calendario('wnac','1')";
	echo "<input type='TEXT' name='wnac' size=10 maxlength=10  id='wnac'  value=".$wnac." class=tipo3><button id='trigger2' onclick=".$cal.">...</button>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wnac',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
 		   
	// RADIO BOTTOM 
	 $a=array(1=>"F",2=>"M"); 
     echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Genero<br>";	  	
   	 	
	  if (isset($wsex))  
	  {
	    for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wsex ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wsex' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wsex' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= count($a); $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wsex' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		} 	  
	  }	

	echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Direccion<br>";	  
    if (isset($wdir))
      echo "<INPUT TYPE='text' NAME='wdir' size=60 maxlength=60 onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$wdir."')' ></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wdir' size=60 maxlength=60 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 

	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Municipio<br>";	  
     $query = "SELECT nombre,codigo FROM root_000006 Order By nombre";  
     $resultado = mysql_query($query);          // Ejecuto el query   
	 $nroreg = mysql_num_rows($resultado);
	 echo "<select name='wmun' OnBlur='enter()'>";  
	 echo "<option></option>";                   	
   
	 $Num_Filas = 0;
	 while ( $Num_Filas < $nroreg )
	  {
		$registro = mysql_fetch_row($resultado);
		$c2=explode('-',$wmun);
  		if(substr($wmun,0,strpos($wmun,"-")) == $registro[0])
	      echo "<option selected>".$registro[0]."-".$registro[1]."</option>";
	    else
	      echo "<option>".$registro[0]."-".$registro[1]."</option>";
	    $Num_Filas++;		  
      }   
     echo "</select></td>";	        	 
 
	 echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Barrio<br>";	  
     $query = "SELECT bardes,barcod FROM root_000034,root_000006"
              ." Where barmun=codigo And codigo='".$c2[1]."'  Order By bardes";
			  
     $resultado = mysql_query($query);          // Ejecuto el query   
	 $nroreg = mysql_num_rows($resultado);
	 if ( $nroreg > 0 )
	 {
	  echo "<select name='wbar'>";  
	  echo "<option></option>";                   	
   
	  $Num_Filas = 0;
	  while ( $Num_Filas < $nroreg )
	  {
		$registro = mysql_fetch_row($resultado);
  		if(substr($wbar,0,strpos($wbar,"-")) == $registro[0])
	      echo "<option selected>".$registro[0]."- ".$registro[1]."</option>";
	    else
	      echo "<option>".$registro[0]."- ".$registro[1]."</option>";
	    $Num_Filas++;		  
      }   
      echo "</select></td>";	
	 } 
	 else
	 {
	  echo "<select name='wbar'>";  
	  echo "<option>SIN DATO-00999</option>";  
      echo "</select></td>";		  
     }
	 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Estrato<br>";	  
     echo "<select name='west'>";  
	 echo "<option></option>";                   	
	 $Num_Filas = 1;
	 while ( $Num_Filas <= 6 )
	  {
  		if ($west == $Num_Filas)
	      echo "<option selected>".$Num_Filas."</option>";
	    else
	      echo "<option>".$Num_Filas."</option>";
	    $Num_Filas++;		  
      }   
     echo "</select></td>";	 	 

	echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Telefono Res.<br>";
    if (isset($wced))
      echo "<INPUT TYPE='text' NAME='wtel' size=15 maxlength=15 VALUE='".$wtel."' onkeypress='return numeros(event);'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wtel' size=15 maxlength=15 onkeypress='return numeros(event);'></INPUT>";  
	  
	echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Celular Nro<br>";
    if (isset($wced))
      echo "<INPUT TYPE='text' NAME='wcel' size=15 maxlength=15 VALUE='".$wcel."' onkeypress='return numeros(event);'></INPUT>"; 
    else
      echo "<INPUT TYPE='text' NAME='wcel' size=15 maxlength=15 onkeypress='return numeros(event);'></INPUT>";  
	  
	echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Correo Electronico:</font></b><br>";
    if (isset($wcoe))
     echo "<INPUT TYPE='text' NAME='wcoe' size=40 maxlength=50 VALUE='".$wcoe."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcoe' size=40 maxlength=50 ></INPUT></td>"; 
	 
/***************************************************************************************************/	 
	 $a=array(1=>"Primaria",2=>"Secundaria",3=>"Técnico",4=>"Tecnólogo",5=>"Universitarios",6=>"Especialización",7=>"Maestría"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Grado de Educacion:</font></b><br>";
	 echo "<select name='wedu'>";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wedu)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wedu == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
	 }	
	 else          //no seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
     }
	 echo "</select></td>";  
	 
 /***********************************Captura CHECKBOX Variable Variable en PHP ó sea $$  ***************************************************************/	
 
     $a=array(1=>"Conyuge",2=>"Hijos",3=>"Padres",4=>"Abuelos",5=>"Amigos",6=>"Solo",7=>"Otros"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Con Quien Vive:</font></b><br>";
	 
	 echo "<font text color=#003366 size=1>";
	 
	 for ($i = 1; $i <= count($a); $i++)
	 {
	   $nam = 'wcv'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   if( isset($$nam) )  
	   {
	    if ( $$nam == "on" )    
	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' CHECKED>".$a[$i]."</INPUT>";
		else 
   	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' >".$a[$i]."</INPUT>";
	   }	
	   else
	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' >".$a[$i]."</INPUT>";
     }
     
     if (isset($wcu1))
       echo "<br>Cuales? <INPUT TYPE='text' NAME='wcu1' size=25 maxlength=50 VALUE='".$wcu1."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT>";  
     else
	   echo "<br>Cuales? <INPUT TYPE='text' NAME='wcu1' size=25 maxlength=50 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>";  	 
   
	 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   
	 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
     echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";   	 
	   
	 if (isset($wpac))
       echo "<b>Nro de Personas a cargo e Hijos: </b><INPUT TYPE='text' NAME='wpac' size=1 maxlength=1 VALUE='".$wpac."' OnBlur='enter()' onkeypress='return numeros(event);'></INPUT>";  	   
     else
	   echo "<b>Nro de Personas a cargo e Hijos: </b><INPUT TYPE='text' NAME='wpac' size=1 maxlength=1  OnBlur='enter()' onkeypress='return numeros(event);'></INPUT>";  	  
   
	 echo "</td>";
	
/***************************************************************************************************/	
	 $a=array(1=>"Casado",2=>"Soltero",3=>"Union Libre",4=>"Viudo",5=>"Separado"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Estado Civil:</font></b><br>";
	 echo "<select name='weci'>";	
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($weci)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $weci == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
	 }	
	 else          //no seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
     }
	 echo "</select></td>";  
/***************************************************************************************************/	

	 echo "<tr><td align=center colspan=6 bgcolor=#6699CC><font size=3 text color=#FFFFFF><b>VIVIENDA</b></font></tr>";
	 
	 $a=array(1=>"Casa",2=>"Apartamento",3=>"Finca",4=>"Pieza",5=>"Pension"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Tipo de Vivienda:</font></b><br>";
	 echo "<select name='wtvi'>";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wtvi)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wtvi == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
		
      echo "</select></td>";  	 
	 } 
	 else          //No seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
		 
      echo "</select></td>";  
	 } 
	 
/***************************************************************************************************/	

	 $a=array(1=>"Propia Sin Hipoteca",2=>"Propia Con Hipoteca",3=>"Arrendada",4=>"Familiar",5=>"Prestada"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Posesión de la Vivienda:</font></b><br>";
	 echo "<select name='wpvi'>";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wpvi)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wpvi == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
		
      echo "</select></td>";  	 
	 } 
	 else          //No seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
		 
      echo "</select></td>";  
	 } 

/***************************************************************************************************/		 
	 $a=array(1=>"Obra Gris",2=>"Terminada",3=>"Otro"); 
	 // para que se sostenga la seleccion hecha despues de un submit entonces:
      echo "<td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2><b>Estado de la vivienda</b><br>";	  	
	  if (isset($wevi))  
	  {
	    for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wevi ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wevi' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wevi' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= count($a); $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wevi' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		} 	  
	  }	
	  
	  if (isset($wcu2))
	    echo "<br>Cual? <INPUT TYPE='text' NAME='wcu2' size=25 maxlength=25 value='".$wcu2."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 
	  else
		echo "<br>Cual? <INPUT TYPE='text' NAME='wcu2' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 
/***************************************************************************************************/	  
 	 $a=array(1=>"Prepago",2=>"Factura mensual",3=>"Otro"); 
	 echo "<b><td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2><b>Medio de pago de servicios publicos:</b></font></b><br>";
	 echo "<select name='wser'>";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wser)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wser == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
	 
      echo "</select>";  	 	
	 } 
	 else          //No seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
	
      echo "</select>";    
	 } 
	 
	  if (isset($wcu3))
		echo "<br><INPUT TYPE='text' NAME='wcu3' size=25 maxlength=25 value='".$wcu3."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	
	  else
        echo "<br><INPUT TYPE='text' NAME='wcu3' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	 

/***************************************************************************************************/	
	 
    echo "<tr><td align=center colspan=6 bgcolor=#6699CC><font size=3 text color=#FFFFFF><b>BIENESTAR Y RECREACION</b></font></tr>";	  

	 $a=array(1=>"Carro",2=>"Moto",3=>"Taxi",4=>"Bus",5=>"Bicicleta",6=>"Sistema Metro",7=>"Otro"); 
	 echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Medio de transporte que utiliza con<br>frecuencia para llegar al trabajo:</font></b><br>";
	 echo "<select name='wtra'>";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wtra)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wtra == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
      echo "</select>";  	 
	 } 
	 else          //No seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
	
      echo "</select>";   		  
	 } 
	 
 	if (isset($wcu4))
      echo "<br><INPUT TYPE='text' NAME='wcu4' size=25 maxlength=25 value='".$wcu4."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	
    else 
	  echo "<br><INPUT TYPE='text' NAME='wcu4' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   

	 
/***************************************************************************************************/	
	 $a=array(1=>"Bingo",2=>"Ferias",3=>"Boleteria",4=>"Dia de Sol",5=>"Regalo Navideño",6=>"Celebracion cumpleaños",7=>"Otro"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Cual de estas actividades que realiza el fondo es la que mas le gusta:</font></b><br>";
	 echo "<select name='wact'>";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wact)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wact == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
	 
      echo "</select><br>";  	 
	 } 
	 else          //No seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
	
      echo "</select><br>";   	
	 } 
	 
	  if (isset($wcu5))
        echo "<INPUT TYPE='text' NAME='wcu5' size=20 maxlength=25 value='".$wcu5."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 
	  else
        echo "<INPUT TYPE='text' NAME='wcu5' size=20 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	  
	 
 /***********************************Captura TEXT con Variable Variable en PHP ó sea $$  ***************************************************************/	
	
     $a=array(1=>"Concierto",2=>"Teatro",3=>"Museos",4=>"Cursos",5=>"Deportes",6=>"Caminatas",7=>"Celebracion dia clasico",8=>"Vacaciones Recreativas",9=>"Excursiones",10=>"Otro"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Que actividades de recreacion le gustaria encontrar en el fondo<br>Enumere en orden de preferencia siendo 1 la que mas le gusta</font></b><br>";
	  
     echo "<font text color=#003366 size=1>";
	 for ($i = 1; $i <= count($a); $i++)
	 {
      $nam = 'wre'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
      if( isset($$nam) )
		echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2 value='".$$nam."' onkeypress='return numeros(event);' >&nbsp;&nbsp;";
      else
        echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2  onkeypress='return numeros(event);' >&nbsp;&nbsp;";		  
     }
	 
	 if (isset($wcu6))
	   echo "<br>Cual? <INPUT TYPE='text' NAME='wcu6' size=25 value='".$wcu6."' maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 
     else
	   echo "<br>Cual? <INPUT TYPE='text' NAME='wcu6' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 
   
/***************************************************************************************************************************************************************/	

/*   LA SIGUIENTE SERIA LA MANERA MEDIANTE UN COMBO DE SELECCION MULTIPLE CAPTURAR VARIAS OPCIONES, COMO SE HACE EN LA PREGUNTA ANTERIOR, PERO COMO LUEGO QUERIAN
     APARTE DE PODER REALIZAR VARIAS SELECCIONES ENUMERAR EL ORDEN DE PREFERENCIA DE LAS MISMAS NO SE UTILIZO. DEJO EL CODIGO PARA FUTURA UTILIZACION
****************************************************************************************************************************************************************/
/*
     $a=array(1=>"Concierto",2=>"Teatro",3=>"Museos",4=>"Cursos",5=>"Deportes",6=>"Caminatas",7=>"Celebracion dia clasico",8=>"Vacaciones Recreativas",9=>"Excursiones",10=>"Otro"); 
	 
	 echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2>Que actividades de recreacion le gustaria encontrar en el fondo?<br>";	  	 
	 
	 echo "<select name='wact[]' size=4 MULTIPLE >"; // Se muestra un combo de  ** MULTIPLE ** seleccion, las opciones seleccionadas con <ctrl> quedan almacenadas como un arreglo  
	                                                 // de 0 al nro de selecciones OJO con  $nrosel =count($wact); Tendria el nro de items u opciones seleccionadas dentro del 
	  if (isset($wact))        //Ya esta seteada     // combo multiple
	  {
	   for ($i = 0; $i <= count($a); $i++)
	   {
	     if ( in_array($i,$wact) )                   // Como puede haber selecciones ya, al volver a llenar el combo para cada elemento con esta funcion pregunto si esta en
	      echo "<option selected value='".$i."'>".$a[$i]."</option>";      // el arreglo de selecciones que se crea automaticamente por ser MULTIPLE si lo esta lo marco 
	     else                                                              // como seleccionado
	      echo "<option value='".$i."'>".$a[$i]."</option>";                  
	   } 
	   echo "</select><br>"; 
	   echo "<INPUT TYPE='text' NAME='wcu6' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();' VALUE='".$wcu6."')' ></INPUT></td>";  
	  }
       else                   //La primera vez
	  {
	   for ($i = 0; $i <= count($a); $i++)
	     echo "<option value='".$i."'>".$a[$i]."</option>";    
       echo "</select><br>"; 		 
	   echo "<INPUT TYPE='text' NAME='wcu6' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>";  
	  }  
	  
*/
	
/***********************************************************************************************************************/	
   	  $a=array(1=>"Si",2=>"No");  
      echo "<td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2><b>Tiene computador</b><br>";	  	
	  if (isset($wcom))  
	  {
	    for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wcom ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wcom' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wcom' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= count($a); $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wcom' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		} 	  
	  }	
	  
	$a=array(1=>"De escritorio",2=>"Portatil",3=>"Tablet"); 
	echo "<br><font text color=#003366 size=2>Tipo: </font></b> ";
	 
	 for ($i = 1; $i <= count($a); $i++)
	 {
	   $nam = 'wco'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   if( isset($$nam) )  
	   {
	    if ( $$nam == "on" )    
	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' CHECKED>".$a[$i]."</INPUT>";
		else 
   	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' >".$a[$i]."</INPUT>";
	   }	
	   else
	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' >".$a[$i]."</INPUT>";
     } 
	 
/***************************************************************************************************/	
     echo "<tr><td align=center colspan=6 bgcolor=#6699CC><font size=3 text color=#FFFFFF><b>LINEAS DE SERVICIO Y CREDITO</b></font></tr>";	  
 
	 $a=array(1=>"Turismo",2=>"Salud",3=>"Educacion",4=>"Vehiculo",5=>"Deportes",6=>"Credito Rotativo",7=>"Otro"); 
	 
 	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Que otras lineas de credito le gustaria<br>Enumere en orden de preferencia siendo 1 la que mas le gusta</font></b><br>";
	 
	 echo "<font text color=#003366 size=1>";
	 for ($i = 1; $i <= count($a); $i++)
	 {
      $nam = 'wcr'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
      if( isset($$nam))
		echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2 value='".$$nam."' onkeypress='return numeros(event);' >&nbsp;&nbsp;";
      else
        echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2  onkeypress='return numeros(event);' >&nbsp;&nbsp;";		  
     }
	 
	 if ( isset($wcu7) )
       echo "<br>Cual? <INPUT TYPE='text' NAME='wcu7' size=25 maxlength=25 value='".$wcu7."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   
     else
	   echo "<br>Cual? <INPUT TYPE='text' NAME='wcu7' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   	 
	 
/***************************************************************************************************/	
	
	 $a=array(1=>"Escolar",2=>"Navideña",3=>"Otra"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Que otras Ferias le gustaria:</font></b><br>";
	 
	 echo "<font text color=#003366 size=1>";
	 for ($i = 1; $i <= count($a); $i++)
	 {
	   $nam = 'wfe'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   if( isset($$nam) )  
	   {
	    if ( $$nam == "on" )    
	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' CHECKED>".$a[$i]."</INPUT>";
		else 
   	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' >".$a[$i]."</INPUT>";
	   }	
	   else
	     echo "<INPUT TYPE = 'checkbox' NAME = '".$nam."' >".$a[$i]."</INPUT>";
     } 
	  
     if (isset($wcu8))	  
        echo "<br>Cual? <INPUT TYPE='text' NAME='wcu8' size=25 maxlength=25 value='".$wcu8."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   
     else
	    echo "<br>Cual? <INPUT TYPE='text' NAME='wcu8' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   

/***************************************************************************************************/
	
	 $t="Los Fondos de Solidaridad se constituyen con cuotas quincenales que aportan todos los asociados. En los Fondos de Empleados por lo "
	    ."regular se manejan Fondos de Solidaridad, los cuales se utilizan para darle auxilios a los Asociados, especialmente en situaciones "
		."dificiles. En la Asamblea es concertada el monto de la cuota y la destinacion es reglamentada por la Junta Directiva.<br>"
		."Esta Usted de acuerdo con la creacion de un fondo de solidaridad:<br>";
	 echo "<td align=LEFT bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2>".$t;	  	 
	   
	 $a=array(1=>"Si",2=>"No"); 
	 // para que se sostenga la seleccion hecha despues de un submit entonces:
     echo "<font text color=#003366 size=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
	  if (isset($wfon))  
	  {
	    for ($i = 1; $i <= 2; $i++)
        {
		 if ( $wfon ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wfon' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wfon' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= 2; $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wfon' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;";   
		} 	  
	  }	
    
     echo "<font text color=#003366 size=2>";
	 if ( isset($wcu9) )
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Porque No? <INPUT TYPE='text' NAME='wcu9' size=50 maxlength=50 value='".$wcu9."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   
     else
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Porque No? <INPUT TYPE='text' NAME='wcu9' size=50 maxlength=50 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>";
	 
/***************************************************************************************************/	 
	 $a=array(1=>"Incapacidades",2=>"Auxilio Funerario",3=>"Auxilio Lentes",4=>"Auxilio Copagos/Moderadoras",5=>"Becas Educacion",6=>"Calamidad",7=>"Otro");  
 	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Que destinacion sugiere para el fondo de solidaridad<br>Enumere en orden de preferencia siendo 1 la que mas le gusta</font></b><br>";
	 
	 echo "<font text color=#003366 size=1>";
	 for ($i = 1; $i <= count($a); $i++)
	 {
      $nam = 'wde'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
      if( isset($$nam))
		echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2 value='".$$nam."' onkeypress='return numeros(event);' >&nbsp;&nbsp;";
      else
        echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2  onkeypress='return numeros(event);' >&nbsp;&nbsp;";		  
     }
	 
	 if ( isset($wcu10))
      echo "<br>Cual? <INPUT TYPE='text' NAME='wcu10' size=25 maxlength=50 value='".$wcu10."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	
     else
      echo "<br>Cual? <INPUT TYPE='text' NAME='wcu10' size=25 maxlength=50 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	 		 
 
	 echo "<tr><td align=center bgcolor=#C0C0C0 colspan=5><b><font text color=#003366 size=2>";	
	 
	 if ( (int) $wpac > 0 )
	 {     
	 ?>
    <BUTTON TYPE=BUTTON value="Personas a cargo" onclick="javascript:window.open('encuesta01b.php?wcod= <?phpPHP echo trim($wced); ?>&wnro=<?php echo $wpac; ?>&windicador=PrimeraVez','fondos',' Top=200, Left=150, width=670, height=300, resizable=YES, Scrollbars=YES')">Detallar Personas a cargo</BUTTON>
     <?php	      
	 }
  	
   	echo "<input type='submit' value='Enviar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<br><font text color=#003366 size=2>¡MUCHAS GRACIAS!!! La actualizacion de la información es muy importante para nuestra gestión</font></b><br>";
	//echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
	
	
	$query="SELECT * FROM fondos_000099 Where encced='".$wced."'";	   // Si ya lleno la encuesta muestro el LINK para ir a la pagina del fondo de empleados
	$resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
	if ($nroreg > 0) 
    {
	 echo "<br>";
     // Como el documento la pagina del fondo lo recibe encriptado lo paso por la funcion que encripta
	 $docenc = encrypt($wced);	 
	 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='p' href='http://fondo.lasamericas.com.co/fempleados/index.php?wuser=".$docenc."'><font face='serif' color='blue' size=4>::Ir a la Pagina del fondo de empleados::</a></font>";
	}
   	echo "</td></tr>";	
	
                                 /***********************************************************************************/							
	if ( $conf == "on" )   
    {
		
	  // VARIABLES como "Con Quien Vive" se capturan en 7 campos checkbox nombradas asi wcv1,wcv2,wcv3.. o "Que actividades de recreacion le gustaria encontar .."	
	  // que se capturan con 10 campos Text. nombradas asi wre1,wre2,wre3 entonces para validarlas las enviare en un arreglo
	  
	  For ($i = 1; $i <= 7; $i++)
	  {
	   $nam = 'wcv'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrcvx[$i] = $$nam; 
	  }

      For ($i = 1; $i <= 10; $i++)
	  {
	   $nam = 'wre'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrrex[$i] = $$nam; 
	  }	  

      For ($i = 1; $i <= 3; $i++)
	  {
	   $nam = 'wco'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrcox[$i] = $$nam; 
	  }	  
	  
      For ($i = 1; $i <= 7; $i++)
	  {
	   $nam = 'wcr'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrcrx[$i] = $$nam; 
	  }	  
    
	  For ($i = 1; $i <= 3; $i++)
	  {
	   $nam = 'wfe'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrfex[$i] = $$nam; 
	  }		  
	   
	  For ($i = 1; $i <= 7; $i++)
	  {
	   $nam = 'wde'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrdex[$i] = $$nam; 
	  }	
	  
	  validar_datos($wced,$wap1,$wno1,$wnac,$wsex,$wdir,$wmun,$wbar,$west,$wtel,$wcel,$wcoe,$wedu,$arrcvx,$wcu1,$wpac,$weci,$wtvi,$wpvi,$wevi,$wcu2,$wser,$wcu3,$wtra,$wcu4,$wact,$wcu5,$arrrex,$wcu6,$wcom,$arrcox,$arrcrx,$wcu7,$arrfex,$wcu8,$wfon,$wcu9,$arrdex,$wcu10); 
	  
	  if (strlen( $msgerr ) > 5 )    //Si retorna de la funcion con mensaje entonces lo muestro
       print "<script>alert('$msgerr')</script>";
	  else
	  {
	   $anio = date("Y");    // Y=Devuelve 4 digitos (2015) y=devuelve 2 digitos(15)
	   $query = "SELECT * FROM fondos_000099 Where encced='".$wced."'";
	   $resultado = mysql_query($query);
       $nroreg = mysql_num_rows($resultado);
	   if ($nroreg == 0) 
	   {
	    $fecha = date("Y-m-d");
	    $hora = (string)date("H:i:s");

   	    $query = "INSERT INTO fondos_000099 (Medico,Fecha_data,Hora_data,encced,encap1,encap2,encno1,encno2,encnac,encsex,encdir,encmun,encbar,encest,enctel,enccel,"
		        ."enccoe,encedu,encpac,enccv1,enccv2,enccv3,enccv4,enccv5,enccv6,enccv7,enccu1,enceci,enctvi,encpvi,encevi,enccu2,encser,enccu3,enctra,enccu4,encact,enccu5,"
				."encre1,encre2,encre3,encre4,encre5,encre6,encre7,encre8,encre9,encre10,enccu6,enccom,encco1,encco2,encco3,enccr1,enccr2,enccr3,enccr4,enccr5,"
                ."enccr6,enccr7,enccu7,encfe1,encfe2,encfe3,enccu8,encfon,enccu9,encde1,encde2,encde3,encde4,encde5,encde6,encde7,enccu10,Seguridad,id) "
		        ." VALUES ('fondos','".$fecha."','".$hora."','".trim($wced)."','".trim($wap1)."','".trim($wap2)."','".trim($wno1)."','".trim($wno2)."','".$wnac."','".$wsex."',"
		        ."'".$wdir."','".$wmun."','".$wbar."','".$west."','".$wtel."','".$wcel."','".$wcoe."','".$wedu."','".$wpac."','".$wcv1."','".$wcv2."','".$wcv3."','".$wcv4."','".$wcv5."',"
				."'".$wcv6."','".$wcv7."','".$wcu1."','".$weci."','".$wtvi."','".$wpvi."','".$wevi."','".$wcu2."','".$wser."','".$wcu3."','".$wtra."','".$wcu4."','".$wact."',"
				."'".$wcu5."','".$wre1."','".$wre2."','".$wre3."','".$wre4."','".$wre5."','".$wre6."','".$wre7."','".$wre8."','".$wre9."','".$wre10."','".$wcu6."','".$wcom."',"
				."'".$wco1."','".$wco2."','".$wco3."','".$wcr1."','".$wcr2."','".$wcr3."','".$wcr4."','".$wcr5."','".$wcr6."','".$wcr7."','".$wcu7."','".$wfe1."','".$wfe2."',"
				."'".$wfe3."','".$wcu8."','".$wfon."','".$wcu9."','".$wde1."','".$wde2."','".$wde3."','".$wde4."','".$wde5."','".$wde6."','".$wde7."','".$wcu10."','".$key."','')";

				$resultado = mysql_query($query,$conex);  
			
	     if ($resultado)
		  print "<script>alert('Su informacion ha sido enviada correctamente!!!!')</script>";
		 else
		  print "<script>alert('Atencion!!! Se produjo un error al enviar la informacion')</script>";
		  
		 print "<script>enter()</script>";    // Para que me haga un submit y refresque
		 
	   }	
	   else
		   
            // Como lo explique el UPDATE tambien lo puedo hacer asi por la forma que tengo de nombrar los campos en la forma y los nombres en la tabla 
	        // Ejemplo la CEDULA en la forma wced en la tabla encced y ademas porque todos los campos en la tabla son tipo texto

		    // Dado que todos los campos son tipo texto y al forma de 
            $campos= array("encced","encap1","encap2","encno1","encno2","encnac","encsex","encdir","encmun","encbar","encest","enctel","enccel",
	        "enccoe","encedu","encpac","enccv1","enccv2","enccv3","enccv4","enccv5","enccv6","enccv7","enccu1","enceci","enctvi","encpvi","encevi","enccu2","encser",
			"enccu3","enctra","enccu4","encact","enccu5","encre1","encre2","encre3","encre4","encre5","encre6","encre7","encre8","encre9","encre10","enccu6","enccom",
            "encco1","encco2","encco3","enccr1","enccr2","enccr3","enccr4","enccr5","enccr6","enccr7","enccu7","encfe1","encfe2","encfe3","enccu8","encfon","enccu9",
            "encde1","encde2","encde3","encde4","encde5","encde6","encde7","enccu10");
	        // Armo el UPDATE 		  
	        $query="UPDATE fondos_000099 SET ";	
            for ($i = 1; $i < count($campos); $i++)    // Arranco en $i=1 porque no hare update a la cedula que es el campo 0
			{ 
		     if (strlen($campos[$i]) == 6 )
		      $nomcam="w".substr($campos[$i],3,3);             /* ==> TOMO LOS 3 ULTIMOS CARACTERES DEL NOMBRE DEL CAMPO   */  
	         else
		      $nomcam="w".substr($campos[$i],3,4);             /* ==> TOMO LOS 4 ULTIMOS CARACTERES DEL NOMBRE DEL CAMPO   */    
			
			//Si es el ultimo campo no termina con ,
			 if ($i == count($campos)-1)
			   $query=$query.$campos[$i]."='".$$nomcam."'";		  		  
		     else
			   $query=$query.$campos[$i]."='".$$nomcam."',";
			}
			$query=$query." Where encced='".$wced."'";
	        
			$resultado = mysql_query($query,$conex);  
	   	    if ($resultado)
		      print "<script>alert('Atencion!!! La encuesta fue modificada. ')</script>";   
		    else
		      print "<script>alert('Atencion!!! Se produjo un error al modificar la informacion')</script>";
		  
	  } 
	}
	
	echo "</table>";
echo "</Form>";	  
//odbc_close($conexN);
//odbc_close_all();
  
?>
</BODY>
</html>