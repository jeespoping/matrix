
<html>
<head>
<title>Actualizacion de datos para asociados al Fondo de empleados</title>
</head>

<script>
    function ira()
    {
	 document.encuesta02a.wap1.focus();
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
		document.forms.encuesta02a.submit();   // Ojo para la funcion encuesta02a <> encuesta02a  (sencible a mayusculas)
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

$wactualiz="PROGRAMA: encuesta02a.php Ver. Mayo 28 de 2015   JairS";

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
	
Function validar_datos($ced,$ap1,$no1,$nac,$sex,$dir,$mun,$bar,$est,$tel,$cel,$coe,$edu,$acvx,$cu1,$pac,$eci,$tra,$cu4,$arex,$cu6,$cu7,$cu8,$cu9,$arrx,$cu11,$ne1,$cu12,$ta1,$cu13) 
//Function validar_datos($ced,$ap1,$no1,$nac,$sex,$dir,$mun,$bar,$est,$tel,$cel,$coe,$edu,$pac,$acvx,$cu1,$eci,$tra,$cu4,$arex,$cu6,$arrx,$cu11,$ne1,$cu12,$ta1,$cu13,$cu14) 
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
	   	 

	If (empty($tra))
    {
      $todok = false;
      $msgerr=$msgerr." Debe seleccionar el medio de transporte utilizado; ";   
    }
	if ( ( $tra ==  7 ) and  ( empty($cu4) ) )
    {
      $todok = false;
      $msgerr=$msgerr." Cual es el otro medio de transporte?; ";   
    }		
	
    	
	
	/*******************************************************************************************************/
    //Esta variable llega en un arreglo
    $wminsel = "";
	$werrvlr = "";
    for ($i = 1; $i <= 8; $i++)
	{
	  if ( $arex[$i] <> "" )
	    $wminsel = "1";
	
   	  if ( (int)$arex[$i] > 8 )	  
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
	if ( ( $arex[3] <> "" ) and ( empty($cu6) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar que genero de conciertos le gustarian; ";   	
	}  
	if ( ( $arex[4] <> "" ) and ( empty($cu7) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar que cursos le gustarian; ";   	
	}  
	if ( ( $arex[5] <> "" ) and ( empty($cu8) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar cual deporte le gustaria; ";   	
	}  
    if ( ( $arex[8] <> "" ) and ( empty($cu9) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar Cual es la otra Actividad de recreacion que le gustaria encontrar; ";   	
	}  
    
	$wrepetido = "";
    for ($i = 1; $i <= 8; $i++)	
	{
	 for ($j = $i+1; $j <= 8; $j++)
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
	/*******************************************************************************************************/
    //Esta variable llega en un arreglo
    $wminsel = "";
	$werrvlr = "";
    for ($i = 1; $i <= 10; $i++)
	{
	  if ( $arrx[$i] <> "" )
	    $wminsel = "1";
	
   	  if ( (int)$arrx[$i] > 10 )	  
	    $werrvlr = "1";
	 
	}	   
    if ($wminsel <> "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Minimo debe seleccionar una opcion en Destinos de Viaje que le gustaria; ";   
    }   
    if ($werrvlr == "1")
    { 
      $todok = false;
      $msgerr=$msgerr." Hay valores fuera de rango en Destinos de Viaje que le gustaria; ";   
    }   
    if ( ( $arrx[10] <> "" ) and ( empty($cu11) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar Cual es el otro Destino de Viaje que le gustaria; ";   	
	}  
    
	$wrepetido = "";
    for ($i = 1; $i <= 10; $i++)	
	{
	 for ($j = $i+1; $j <= 10; $j++)
	 {	 
       if ( ($arrx[$i] == $arrx[$j]) and ($arrx[$i] <> "") )
		 $wrepetido = "S";	
	 }
	}
	if ($wrepetido == "S")
	{	
	  $todok = false;
      $msgerr=$msgerr." Hay valores repetidos en Destinos de Viaje que le gustaria; ";   	
	}  
	/*******************************************************************************************************/
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
	  
	 if (empty($ne1))
    {
      $todok = false;
      $msgerr=$msgerr." Debe especificar si tiene algun negocio familiar; ";   
	}		
	if ( ( $ne1 == "1" ) and ( empty($cu12) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar cual es el negocio familiar; ";   	
	}  
	
	if (empty($ta1))
    {
      $todok = false;
      $msgerr=$msgerr." Debe especificar si tiene algun talento que desee compartir; ";   
	}		
	if ( ( $ta1 == "1" ) and ( empty($cu13) ) )
	{	
	  $todok = false;
      $msgerr=$msgerr." Debe especificar cual es el Talento que tiene; ";   	
	}  
	  
   //echo "<font size=2 text color=#CC0000>".$msgerr;   
   //return $todok;   
   
   return $msgerr;   
 }  

//*************************************
 


//$conex = mysql_connect('localhost','root','q6@nt6m') or die("No se realizo Conexion");       // PARA PRUEBAS LOCALES
mysql_select_db("matrix") or die("No se selecciono la base de datos");  

echo "<form name='encuesta02a' action='encuesta02a.php' method=post>";  

    echo "<center><table border=1>";

    echo "<td align=center bgcolor=#6699CC colspan=5><b>";
	echo "<font text color='blue' size=2>PERFIL DE ASOCIADOS FONDO DE EMPLEADOS LAS AMERICAS<br>";
    echo "<font text color=#003366 size=2><b>CON MIRAS A DESARROLLAR LOS PLANES DE BIENESTAR DEL FONDO DE EMPLEADOS Y SATISFACER<br> ";
	echo "<font text color=#003366 size=2><b>SUS NECESIDADES Y EXPECTATIVAS, LOS INVITAMOS A ACTUALIZAR LA SIGUIENTE ENCUESTA ";
	echo "</font></b><br></td>";
	
    $key = substr($user,2,strlen($user));
	
 
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
		        "enccoe","encedu","encpac","enccv1","enccv2","enccv3","enccv4","enccv5","enccv6","enccv7","enccu1","enceci","enctra","enccu4",
				"encre1","encre2","encre3","encre4","encre5","encre6","encre7","encre8","enccu6","enccu7","enccu8","enccu9","encrr1","encrr2","encrr3",
				"encrr4","encrr5","encrr6","encrr7","encrr8","encrr9","encrr10","enccu11","encne1","enccu12","encta1","enccu13","enccu14");
     
	  // Armo el SELECT		  
	  $query="SELECT ";	
      for ($i = 0; $i < count($campos)-1; $i++)
       $query=$query.$campos[$i].",";
      $query=$query." ".$campos[$i]." FROM fondos_000100 Where encced='".$wced."'";
	  
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
       echo "<br><b>Nro de Personas a cargo e Hijos: </b><INPUT TYPE='text' NAME='wpac' size=1 maxlength=1 VALUE='".$wpac."' OnBlur='enter()' onkeypress='return numeros(event);'></INPUT>";  	   
     else
	   echo "<br><b>Nro de Personas a cargo e Hijos: </b><INPUT TYPE='text' NAME='wpac' size=1 maxlength=1  OnBlur='enter()' onkeypress='return numeros(event);'></INPUT>";  	  
   
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

 /***********************************Captura TEXT con Variable Variable en PHP ó sea $$  ***************************************************************/	
	
     $a=array(1=>"Cine",2=>"Obras de Teatro",3=>"Conciertos",4=>"Cursos",5=>"Practicar Deportes",6=>"Caminatas",7=>"Vacaciones Recreativas",8=>"Otro"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Que actividades de recreacion le gustaria encontrar en el fondo<br>Enumere en orden de preferencia siendo 1 la que mas le gusta</font></b><br>";
	  
     echo "<font text color=#003366 size=1>";
	 for ($i = 1; $i <= count($a); $i++)
	 {
      $nam = 'wre'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
      if( isset($$nam) )
		echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2 value='".$$nam."' onkeypress='return numeros(event);' >&nbsp;&nbsp;";
      else
        echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2  onkeypress='return numeros(event);' >&nbsp;&nbsp;";		  
     }
	 echo "<br><br><b><font text color=#003366 size=2>Debe diligenciar los siguientes campos si marco alguna de estas actividades<br>Cursos - Conciertos - Deporte - Otro</font></b><br>";
	 if (isset($wcu6))
	   echo "<br>Conciertos Que Genero? <INPUT TYPE='text' NAME='wcu6' size=25 value='".$wcu6."' maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 
     else
	   echo "<br>Conciertos Que Genero? <INPUT TYPE='text' NAME='wcu6' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 
	
	if (isset($wcu7))
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;Que Cursos? <INPUT TYPE='text' NAME='wcu7' size=25 value='".$wcu7."' maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 
     else
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;Que Cursos? <INPUT TYPE='text' NAME='wcu7' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 
    
	if (isset($wcu8))
	   echo "<br>Cual Deporte? <INPUT TYPE='text' NAME='wcu8' size=25 value='".$wcu8."' maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 
     else
	   echo "<br>Cual Deporte? <INPUT TYPE='text' NAME='wcu8' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT>"; 
    
	if (isset($wcu9))
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;Cual Otra Actividad? <INPUT TYPE='text' NAME='wcu9' size=25 value='".$wcu9."' maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 
     else
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;Cual Otra Actividad? <INPUT TYPE='text' NAME='wcu9' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 
/***************************************************************************************************************************************************************/	

/*   LA SIGUIENTE SERIA LA MANERA MEDIANTE UN COMBO DE SELECCION MULTIPLE CAPTURAR VARIAS OPCIONES, COMO SE HACE EN LA PREGUNTA ANTERIOR, PERO COMO LUEGO QUERIAN
     APARTE DE PODER REALIZAR VARIAS SELECCIONES ENUMERAR EL ORDEN DE PREFERENCIA DE LAS MISMAS NO SE UTILIZO. DEJO EL CODIGO PARA FUTURA UTILIZACION
****************************************************************************************************************************************************************/
/*	 
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

     $a=array(1=>"Coveñas",2=>"Eje Cafetero",3=>"Pueblos de Antioquia",4=>"Cartagena",5=>"San Andres",6=>"Santander",7=>"Boyaca",8=>"Amazonas",9=>"Lllanos Orientales",10=>"Otro"); 
	 
 	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Que destino de viaje le gustaria que el fondo ofreciera en el año 2018<br>Enumere en orden de preferencia siendo 1 la que mas le gusta</font></b><br>";
	 
	 echo "<font text color=#003366 size=1>";
	 for ($i = 1; $i <= count($a); $i++)
	 {
      $nam = 'wrr'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
      if( isset($$nam))
		echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2 value='".$$nam."' onkeypress='return numeros(event);' >";
      else
        echo $a[$i].": <input type=text name=".$nam."  size=2 maxlength=2  onkeypress='return numeros(event);' >";		  
     }
	 
	 if ( isset($wcu11) )
       echo "<br>Cual? <INPUT TYPE='text' NAME='wcu11' size=25 maxlength=25 value='".$wcu11."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   
     else
	   echo "<br>Cual? <INPUT TYPE='text' NAME='wcu11' size=25 maxlength=25 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   	 
	 
/***************************************************************************************************/	
	 echo "<tr><td align=center colspan=6 bgcolor=#6699CC><font size=3 text color=#FFFFFF>OTROS</font></tr>";	 
		
	 $t="Tiene algun negocio familiar<br>";
	 echo "<td align=CENTER bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2><b>".$t;	  	 
	   
	 $a=array(1=>"Si",2=>"No"); 
	 // para que se sostenga la seleccion hecha despues de un submit entonces:
     echo "<font text color=#003366 size=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
	  if (isset($wne1))  
	  {
	    for ($i = 1; $i <= 2; $i++)
        {
		 if ( $wne1 ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wne1' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wne1' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= 2; $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wne1' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;";   
		} 	  
	  }	
    
     echo "<font text color=#003366 size=2>";
	 if ( isset($wcu12) )
	   echo "<br>Cual? <INPUT TYPE='text' NAME='wcu12' size=50 maxlength=50 value='".$wcu12."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   
     else
	   echo "<br>Cual? <INPUT TYPE='text' NAME='wcu12' size=50 maxlength=50 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>";
	 
/***************************************************************************************************/	 
	 $t="Tiene algun Talento que quisiera compartir?<br>";
	 echo "<td align=CENTER bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2><b>".$t;	  	 
	   
	 $a=array(1=>"Si",2=>"No"); 
	 // para que se sostenga la seleccion hecha despues de un submit entonces:
     echo "<font text color=#003366 size=3>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 
	  if (isset($wta1))  
	  {
	    for ($i = 1; $i <= 2; $i++)
        {
		 if ( $wta1 ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wta1' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wta1' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= 2; $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wta1' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;";   
		} 	  
	  }	
    
     echo "<font text color=#003366 size=2>";
	 if ( isset($wcu13) )
	   echo "<br>Cual? <INPUT TYPE='text' NAME='wcu13' size=50 maxlength=50 value='".$wcu13."' onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>"; 	   
     else
	   echo "<br>Cual? <INPUT TYPE='text' NAME='wcu13' size=50 maxlength=50 onKeyUp='this.value = this.value.toUpperCase();'></INPUT></td>";

   /*******************************************************************************************************************************************************************************************************/ 
	$t="Alguna sugerencia para las actividades de bienestar de 2018?<br>";
	 echo "<td align=CENTER bgcolor=#C0C0C0 colspan=2><font text color=#003366 size=2><b>".$t;	  	 
	   
	     
     echo "<font text color=#003366 size=2>";
	 if ( isset($wcu14) )
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<TEXTAREA cols='10' rows='5' NAME='wcu14' style='width:600px; height:50px' size=60 maxlength=80 value='".$wcu14."' onKeyUp='form.wmar.value=form.wmar.value.toUpperCase()'>".$wcu14."</TEXTAREA></td>"; 	   
     else
	   echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<TEXTAREA cols='10' rows='5' NAME='wcu14' style='width:600px; height:50px' size=60 maxlength=80 onKeyUp='form.wmar.value=form.wcu14.value.toUpperCase()'>".$wcu14."</TEXTAREA></td>";
   
   /*************************************************************************************************************************************************************************************************/
   
	echo "<tr><td align=center bgcolor=#C0C0C0 colspan=5><b><font text color=#003366 size=2>";	
	 
	 if ( (int) $wpac > 0 )
	 {     
	 ?>
    <BUTTON TYPE=BUTTON value="Personas a cargo" onclick="javascript:window.open('encuesta02b.php?wcod= <?phpPHP echo trim($wced); ?>&wnro=<?php echo $wpac; ?>&windicador=PrimeraVez','fondos',' Top=200, Left=150, width=670, height=300, resizable=YES, Scrollbars=YES')">Detallar Personas a cargo</BUTTON>
     <?php	      
	 }
  	
   	echo "<input type='submit' value='Enviar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<br><font text color=#003366 size=2>¡MUCHAS GRACIAS!!! La actualizacion de la información es muy importante para nuestra gestión</font></b><br>";
	//echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
	
	
	$query="SELECT * FROM fondos_000100 Where encced='".$wced."'";	   // Si ya lleno la encuesta muestro el LINK para ir a la pagina del fondo de empleados
	$resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
	if ($nroreg > 0) 
    {
	 echo "<br>";
     // Como el documento la pagina del fondo lo recibe encriptado lo paso por la funcion que encripta
	 $docenc = encrypt($wced);	 
		if( $wta1 <> "" and (strlen( $msgerr ) < 6 ) )
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

      For ($i = 1; $i <= 8; $i++)
	  {
	   $nam = 'wre'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrrex[$i] = $$nam; 
	  }	  

	  For ($i = 1; $i <= 10; $i++)
	  {
	   $nam = 'wrr'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrrrx[$i] = $$nam; 
	  }	  
	  
    
	  
	  validar_datos($wced,$wap1,$wno1,$wnac,$wsex,$wdir,$wmun,$wbar,$west,$wtel,$wcel,$wcoe,$wedu,$arrcvx,$wcu1,$wpac,$weci,$wtra,$wcu4,$arrrex,$wcu6,$wcu7,$wcu8,$wcu9,$arrrrx,$wcu11,$wne1,$wcu12,$wta1,$wcu13); 
	  
	  if (strlen( $msgerr ) > 5 )    //Si retorna de la funcion con mensaje entonces lo muestro
       print "<script>alert('$msgerr')</script>";
	  else
	  {
	   $anio = date("Y");    // Y=Devuelve 4 digitos (2015) y=devuelve 2 digitos(15)
	   $query = "SELECT * FROM fondos_000100 Where encced='".$wced."'";
	   $resultado = mysql_query($query);
       $nroreg = mysql_num_rows($resultado);
	   if ($nroreg == 0) 
	   {
	    $fecha = date("Y-m-d");
	    $hora = (string)date("H:i:s");

   	    $query = "INSERT INTO fondos_000100 (Medico,Fecha_data,Hora_data,encced,encap1,encap2,encno1,encno2,encnac,encsex,encdir,encmun,encbar,encest,enctel,enccel,"
		        ."enccoe,encedu,encpac,enccv1,enccv2,enccv3,enccv4,enccv5,enccv6,enccv7,enccu1,enceci,enctra,enccu4,"
				."encre1,encre2,encre3,encre4,encre5,encre6,encre7,encre8,enccu6,enccu7,enccu8,enccu9,encrr1,encrr2,encrr3,encrr4,encrr5,"
                ."encrr6,encrr7,encrr8,encrr9,encrr10,enccu11,encne1,enccu12,encta1,enccu13,enccu14,Seguridad,id) "
		        ." VALUES ('fondos','".$fecha."','".$hora."','".trim($wced)."','".trim($wap1)."','".trim($wap2)."','".trim($wno1)."','".trim($wno2)."','".$wnac."','".$wsex."',"
		        ."'".$wdir."','".$wmun."','".$wbar."','".$west."','".$wtel."','".$wcel."','".$wcoe."','".$wedu."','".$wpac."','".$wcv1."','".$wcv2."','".$wcv3."','".$wcv4."','".$wcv5."',"
				."'".$wcv6."','".$wcv7."','".$wcu1."','".$weci."','".$wtra."','".$wcu4."',"
				."'".$wre1."','".$wre2."','".$wre3."','".$wre4."','".$wre5."','".$wre6."','".$wre7."','".$wre8."','".$wcu6."','".$wcu7."','".$wcu8."','".$wcu9."',"
				."'".$wrr1."','".$wrr2."','".$wrr3."','".$wrr4."','".$wrr5."','".$wrr6."','".$wrr7."','".$wrr8."','".$wrr9."','".$wrr10."',"
				."'".$wcu11."','".$wne1."','".$wcu12."','".$wta1."','".$wcu13."','".$wcu14."','".$key."','')";

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
		        "enccoe","encedu","encpac","enccv1","enccv2","enccv3","enccv4","enccv5","enccv6","enccv7","enccu1","enceci","enctra","enccu4",
				"encre1","encre2","encre3","encre4","encre5","encre6","encre7","encre8","enccu6","enccu7","enccu8","enccu9","encrr1","encrr2","encrr3",
				"encrr4","encrr5","encrr6","encrr7","encrr8","encrr9","encrr10","enccu11","encne1","enccu12","encta1","enccu13","enccu14");
	        // Armo el UPDATE 		  
	        $query="UPDATE fondos_000100 SET ";	
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