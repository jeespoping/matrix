<html>
<head>
<title>Detalle Protocolos</title>
<script type="text/javascript" src="tabbed.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body BGCOLOR=#DDDDDD>
<script type="text/javascript">
	function enter()
	{
	   document.forms.DetalleProtocolos.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     } 
</script>

<!-- Programa en PHP -->
<?php
include_once("conex.php");





include_once("root/magenta.php");
include_once("root/comun.php");

$wactualiz = "(Julio 15 de 2009)";



$wbasedato="hce";   //OJO **** OJO **** CAMBIAR

$wfecha=date("Y-m-d");   
$whora = (string)date("H:i:s");

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user)); 



//=================================================================================================================================
//***************************************** D E F I N I C I O N   D E   F U N C I O N E S *****************************************
//=================================================================================================================================
/*
function leading_zero( $aNumber, $intPart, $floatPart=NULL, $dec_point=NULL, $thousands_sep=NULL) 
  {        
   $formattedNumber = $aNumber;
   if (!is_null($floatPart)) 
     {    //without 3rd parameters the "float part" of the float shouldn't be touched
      $formattedNumber = number_format($formattedNumber, $floatPart, $dec_point, $thousands_sep);
     }
  if ($intPart > floor(log10($formattedNumber)))
    $formattedNumber = str_repeat("0",($intPart + -1 - floor(log10($formattedNumber)))).$formattedNumber;
  return $formattedNumber;
  }
*/
  
  
function validar_campos(&$wvalidacion, &$wobligatorio, &$wvariables, &$wcar_obl)
  {
   global $conex;	  
   global $wbasedato;
   
   global $wok;
   
   global $wdetpro,$wdetcon,$wdetorp,$wdettip,$wdetdes,$wdetarc,$wdetcav,$wdetvde,$wdetnpa,$wdetvim,$wdetume,$wdetcol,$wdethl7,$wdetjco,$wdetsiv,$wdetase;
   global $wdetved,$wdetimp,$wdetimc,$wdetvco,$wdetvcr,$wdetobl,$wdetdep,$wdetcde,$wdeturl,$wdetfor,$wdetcco,$wdetcac,$wdetnse,$wdetfac,$wdetest;
   
   
   $wnr=count($wobligatorio);
   
   if (isset($wdettip) and $wdettip!="")
      {
       $wok=true;
	   
	   for ($i=0;$i<$wnr;$i++)
	      {
		   if ($wobligatorio[$i]=="on")
		      {
			   if (!isset($$wvariables[$i]) or trim($$wvariables[$i])=="") 
			      {
				   $wok=false;
				   
				   echo '<script language="javascript">';
				   echo 'alert ("El campo : '.$wvariables[$i].' esta vacio")';
				   echo '</script>';
				  }
			  }
		   echo "<input type='HIDDEN' name='wvalidacion[".$i."]'  value='".$wvalidacion[$i]."'>";
		   echo "<input type='HIDDEN' name='wobligatorio[".$i."]' value='".$wobligatorio[$i]."'>";
		   echo "<input type='HIDDEN' name='wvariables[".$i."]'   value='".$wvariables[$i]."'>";
		   echo "<input type='HIDDEN' name='wcar_obl[".$i."]'     value='".$wcar_obl[$i]."'>";  
		  }
      }
     else
        {
	     echo '<script language="javascript">';
	     echo 'alert ("Debe seleccionar un tipo de dato, antes de Grabar")';
	     echo '</script>';
	    }     	  
  }

  
function evaluar_campos_boleanos()
  {
   global $wdetpro,$wdetcon,$wdetorp,$wdettip,$wdetdes,$wdetarc,$wdetcav,$wdetvde,$wdetnpa,$wdetvim,$wdetume,$wdetcol,$wdethl7,$wdetjco,$wdetsiv,$wdetase;
   global $wdetved,$wdetimp,$wdetimc,$wdetvco,$wdetvcr,$wdetobl,$wdetdep,$wdetcde,$wdeturl,$wdetfor,$wdetcco,$wdetcac,$wdetnse,$wdetfac,$wdetest;	  
	  
	  
   //Evaluo los campos boleanos - checkbox
   //Aca le modifico el valor a los campos BOLEANOS
   
   if (isset($wdethl7))
     $wdethl7="checked";
    else
       $wdethl7="unchecked"; 
   if (isset($wdetjco))
     $wdetjco="checked";
    else
       $wdetjco="unchecked"; 
   if (isset($wdetsiv))
     $wdetsiv="checked";
    else
       $wdetsiv="unchecked"; 
   if (isset($wdetase))
     $wdetase="checked";
    else
       $wdetase="unchecked"; 
   if (isset($wdetved))
     $wdetved="checked";
    else
       $wdetved="unchecked"; 
   if (isset($wdetimp))
     $wdetimp="checked";
    else
       $wdetimp="unchecked"; 
   if (isset($wdetimc))
     $wdetimc="checked";
    else
       $wdetimc="unchecked"; 
   if (isset($wdetvco))
     $wdetvco="checked";
    else
       $wdetvco="unchecked"; 
   if (isset($wdetvcr))
     $wdetvcr="checked";
    else
       $wdetvcr="unchecked"; 
   if (isset($wdetobl))
     $wdetobl="checked";
    else
       $wdetobl="unchecked";
   if (isset($wdetdep))
     $wdetdep="checked";
    else
       $wdetdep="unchecked";     
   if (isset($wdetest))
     $wdetest="checked";
    else
       $wdetest="unchecked"; 	  
  }	  
    
//=================================================================================================================================
function grabar()
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wok;
   
   global $wdetpro,$wdetcon,$wdetorp,$wdettip,$wdetdes,$wdetarc,$wdetcav,$wdetvde,$wdetnpa,$wdetvim,$wdetume,$wdetcol,$wdethl7,$wdetjco,$wdetsiv,$wdetase;
   global $wdetved,$wdetimp,$wdetimc,$wdetvco,$wdetvcr,$wdetobl,$wdetdep,$wdetcde,$wdeturl,$wdetfor,$wdetcco,$wdetcac,$wdetnse,$wdetfac,$wdetest;
   
   
   if ($wok==true)
     {
	  //Aca le modifico el valor a los campos BOLEANOS
	  if ($wdethl7 == true)
	     $wdethl7="on";
	    else
	       $wdethl7="off";
	  if ($wdetjco == true)
	     $wdetjco="on";
	    else
	       $wdetjco="off";
	  if ($wdetsiv == true)
	     $wdetsiv="on";
	    else
	       $wdetsiv="off";
	  if ($wdetase == true)
	     $wdetase="on";
	    else
	       $wdetase="off";
	  if ($wdetved == true)
	     $wdetved="on";
	    else
	       $wdetved="off";
	  if ($wdetimp == true)
	     $wdetimp="on";
	    else
	       $wdetimp="off";
	  if ($wdetimc == true)
	     $wdetimc="on";
	    else
	       $wdetimc="off";
	  if ($wdetvco == true)
	     $wdetvco="on";
	    else
	       $wdetvco="off";
	  if ($wdetvcr == true)
	     $wdetvcr="on";
	    else
	       $wdetvcr="off";
	  if ($wdetobl == true)
	     $wdetobl="on";
	    else
	       $wdetobl="off";
	  if ($wdetdep == true)
	     $wdetdep="on";
	    else
	       $wdetdep="off";
	  if ($wdetest == true)
	     $wdetest="on";
	    else
	       $wdetest="off"; 
	       
	     
	  //Inserto el registro en la tabla de Configuracion del protocolo   
	  $q= " INSERT INTO ".$wbasedato."_000002 (   Medico       ,   fecha_data,   hora_data,    detpro    ,    detcon    ,    detorp    ,    dettip    ,    detdes    ,    detarc    ,    detcav    ,    detvde    ,    detnpa     ,   detvim     ,   detume     ,   detcol     ,   dethl7     ,   detjco    ,    detsiv    ,    detase    ,    detved    ,    detimp    ,    detimc    ,    detvco    ,    detvcr    ,    detobl    ,    detdep    ,    detcde    ,    deturl    ,    detfor    ,    detcco    ,    detcac    ,    detnse    ,    detfac    ,    detest    ,    Seguridad   ) "
		 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wdetpro."','".$wdetcon."','".$wdetorp."','".$wdettip."','".$wdetdes."','".$wdetarc."','".$wdetcav."','".$wdetvde."','".$wdetnpa."','".$wdetvim."','".$wdetume."','".$wdetcol."','".$wdethl7."','".$wdetjco."','".$wdetsiv."','".$wdetase."','".$wdetved."','".$wdetimp."','".$wdetimc."','".$wdetvco."','".$wdetvcr."','".$wdetobl."','".$wdetdep."','".$wdetcde."','".$wdeturl."','".$wdetfor."','".$wdetcco."','".$wdetcac."','".$wdetnse."','".$wdetfac."','".$wdetest."','".$wbasedato."') ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 }    
  }	  
  
//=================================================================================================================================

 
function modificar()
  {
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wok;
   global $wcodpro;
   global $wnompro;
   global $wtipuso;
   global $wtipfor;
   global $wtipimp;
   global $walerta;
   global $westado;	  
	  
   if ($wok==true)
     {
	  //Primero se borra y luego se graba.
	  $q = " DELETE FROM ".$wbasedato."_000002 "
	      ."  WHERE encpro = '".$wdetpro."'";
	  //$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  
	  grabar();
	 }     
  } 
  
function evaluar_actividad_campos($wdettip, &$wvalidacion, &$wobligatorio, &$wvariables, &$wcar_obl)
  {
   global $conex;	  
   global $wbasedato;
   
   
   global $wdetpro,$wdetcon,$wdetorp,$wdettip,$wdetdes,$wdetarc,$wdetcav,$wdetvde,$wdetnpa,$wdetvim,$wdetume,$wdetcol,$wdethl7,$wdetjco,$wdetsiv,$wdetase;
   global $wdetved,$wdetimp,$wdetimc,$wdetvco,$wdetvcr,$wdetobl,$wdetdep,$wdetcde,$wdeturl,$wdetfor,$wdetcco,$wdetcac,$wdetnse,$wdetfac,$wdetest;
   
   
   $q = " SELECT tipdat, tippan, tipobl, tipvar "
       ."   FROM ".$wbasedato."_000010 "
       ."  WHERE tipdat = '".$wdettip."'";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   $num = mysql_num_rows($res);
   
   if ($num > 0 )
     {
	  $row= mysql_fetch_array($res);
	  
	  $wvalores=explode("-",$row[1]);     //Aca se separa el campo de cada uno de los estados de los campos en pantalla (Enabled, Disabled, Readonly)
	  
	  
	  for ($i=0;$i<count($wvalores);$i++)
	    {
     	 switch ($wvalores[$i])
		  {
		   case "E":   
		     $wvalidacion[$i]="Enabled";  
		     break;
		   case "D":
		     $wvalidacion[$i]="Disabled";
		     break;
		   case "R":
		     $wvalidacion[$i]="Readonly";
		     break; 
		     
		   echo "<input type='HIDDEN' name='wvalidacion[".$i."]' value='".$wvalidacion[$i]."'>";   
		  }
		}
		
	  $wvalores=explode("-",$row[2]);	  //Aca se separa el indicador de obligatoriedad para cada uno de los campos en pantalla
	  for ($i=0;$i<count($wvalores);$i++)
	    {
     	 $wobligatorio[$i]=$wvalores[$i];
     	 echo "<input type='HIDDEN' name='wobligatorio[".$i."]' value='".$wobligatorio[$i]."'>";
     	 
     	 if ($wobligatorio[$i]=="on")
     	    $wcar_obl[$i]="(*)";          //Este caracter lo dbe mostrar en la pantalla en cada campo obligatorio
     	   else
     	      $wcar_obl[$i]=""; 
     	 echo "<input type='HIDDEN' name='wcar_obl[".$i."]' value='".$wcar_obl[$i]."'>";     
     	} 
		
	  $wvalores=explode("-",$row[3]);	  //Aca se separan los nombres de las variables que tiene cada campo de la pantalla en este programa.
	  for ($i=0;$i<count($wvalores);$i++)
	    {
     	 $wvariables[$i]=$wvalores[$i];
     	 echo "<input type='HIDDEN' name='wvariables[".$i."]' value='".$wvariables[$i]."'>";
		}
	 }   
  }	  

  
function consultar(&$wvalidacion, &$wobligatorio, &$wvariables)
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
      
   global $wdetpro,$wdetcon,$wdetorp,$wdettip,$wdetdes,$wdetarc,$wdetcav,$wdetvde,$wdetnpa,$wdetvim,$wdetume,$wdetcol,$wdethl7,$wdetjco,$wdetsiv,$wdetase;
   global $wdetved,$wdetimp,$wdetimc,$wdetvco,$wdetvcr,$wdetobl,$wdetdep,$wdetcde,$wdeturl,$wdetfor,$wdetcco,$wdetcac,$wdetnse,$wdetfac,$wdetest;
        
   if (isset($wdetpro))
      {
	   $q = " SELECT detpro,detcon,detorp,dettip,detdes,detarc,detcav,detvde,detnpa,detvim,detume,detcol,dethl7,detjco,detsiv,detase, "
	       ."        detved,detimp,detimc,detvco,detvcr,detobl,detdep,detcde,deturl,detfor,detcco,detcac,detnse,detfac,detest "
	       ."   FROM ".$wbasedato."_000002 "
	       ."  WHERE detpro = '".$wdetpro."'"
	       ."  ORDER BY 1 ";
	  }
	  
	/* else
	    {
		 $q = " SELECT tipdat, tippan, tipobl, tipvar "
	         ."   FROM ".$wbasedato."_000010 ";
	     $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num = mysql_num_rows($res);
         
         if ($num > 0)
            {
	         $row = mysql_fetch_array($res);   
	     
	         $wvar=explode("-",$row[3]);
	         
	         for ($i=0;$i<count($wvar);$i++)
	            {
		         $$wvar[$i]       ="";
		         $wvalidacion[$i] ="Enabled";
		         $wobligatorio[$i]="On";
		         $wvariables[$i]  =$row[3];
		         
		         $row = mysql_fetch_array($res);
                }
	        }    
	    }         
    */
	//    var_dump($wobligatorio);
	    
   if (isset($wdettip)) 
      evaluar_actividad_campos($wdettip,$wvalidacion, $wobligatorio, $wvariables, $wcar_obl);
  } 
 
  
   
function iniciar(&$wvalidacion, &$wobligatorio, &$wvariables, &$wcar_obl)
  {
   global $conex;
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   
   global $wdetpro,$wdetcon,$wdetorp,$wdettip,$wdetdes,$wdetarc,$wdetcav,$wdetvde,$wdetnpa,$wdetvim,$wdetume,$wdetcol,$wdethl7,$wdetjco,$wdetsiv,$wdetase;
   global $wdetved,$wdetimp,$wdetimc,$wdetvco,$wdetvcr,$wdetobl,$wdetdep,$wdetcde,$wdeturl,$wdetfor,$wdetcco,$wdetcac,$wdetnse,$wdetfac,$wdetest;
   
   //Busco que consecutivo sigue en el protocolo
   $q = " SELECT MAX(detcon) "
       ."   FROM ".$wbasedato."_000002 "
       ."  WHERE detpro = '".$wdetpro."'"
       ."    AND detest = 'on' ";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);
   
   if ($num > 0)
      {
	   $row = mysql_fetch_array($res);
	   
	   $wdetcon = $row[0]+1;
	  }        
   
   //Aca busco las variables con sus correspondientes estados en pantalla
   $q = " SELECT tipdat, tippan, tipobl, tipvar "
       ."   FROM ".$wbasedato."_000010 ";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);
         
   if ($num > 0)
     {
      $row = mysql_fetch_array($res);   
 
      $wvar=explode("-",$row[3]);
     
      for ($i=0;$i<count($wvar);$i++)
        {
	     //$$wvar[$i]       ="";    //Esto inicializa todas las variables
         $wvalidacion[$i] ="Enabled";
         $wobligatorio[$i]="On";
         $wvariables[$i]  =$row[3];
         $wcar_obl[$i]    ="";
         
         $row = mysql_fetch_array($res);
        }
     }
  }  
//=================================================================================================================================
//***************************** T E R M I N A   L A   D E F I N I C I O N   D E   F U N C I O N E S *******************************
//=================================================================================================================================



echo "<form name='DetalleProtocolos' method='post' action=''>";
//encabezado("Encabezado del Protocolo",$wactualiz, "clinica");
echo "<div align='center'><font color='#000099' size='5'><strong>&nbsp</strong></font><br>";
echo "<CENTER>";


//***************************************************************************************************************
//*********   A C A   C O M I E N Z A   E L   B L O Q U E   P R I C I P A L   D E L   P R O G R A M A   *********
//***************************************************************************************************************

//Se evalua el boton presionado
if (isset($Grabar) or isset($Modificar) or isset($Consultar) or isset($Iniciar))
   {
	 if (isset($Grabar))
	   {
		validar_campos($wvalidacion, $wobligatorio, $wvariables, $wcar_obl);
		grabar();
		
		if ($wok==true)
		   {
			?>	    
		      <script> alert ("El Registro fue Grabado"); </script>
		    <?php
		    iniciar($wvalidacion, $wobligatorio, $wvariables, $wcar_obl);
	       } 
	     
	    $wdetorp="";
	    $wdettip="";
	    $wdetdes="";
	    $wdetarc="";
	    $wdetcav="";
	    $wdetvde="";
	    $wdetnpa="";
	    $wdetvim="";
	    $wdetume="";
	    $wdetcol="";
	    $wdetcde="";
	    $wdeturl="";
	    $wdetfor="";
	    $wdetcco="";
	    $wdetcac="";
	    $wdetnse="";
	    $wdetfac="";
      
	    unset($wdethl7);
	    unset($wdetjco);
	    unset($wdetsiv);
	    unset($wdetase);
	    unset($wdetved);
	    unset($wdetimp);
	    unset($wdetimc);
	    unset($wdetvco);
	    unset($wdetvcr);
	    unset($wdetobl);
	    unset($wdetdep);
	    unset($wdetest);
	    
	    
	    evaluar_campos_boleanos();  
       }	
          
	 if (isset($Modificar))
	   {
		validar_campos();
		if ($wok==true) 
		   { modificar(); 
		    ?>	    
		      <script> alert ("El Registro fue Modificado"); </script>
		    <?php
	       } 
       }
       
     if (isset($Consultar))
	   { consultar($wvalidacion, $wobligatorio, $wvariables); }    
     
       
     if (isset($Iniciar))
	   { iniciar($wvalidacion, $wobligatorio, $wvariables, $wcar_obl); }  
   } //fin del if (Grabar or Modificar or Consultar or Borrar)
  else
     { 
	  if (!isset($wdettip))
	     {   
		  $wvalidacion = array();  //Este arreglo sirve para guardar el estado que debe tener cada campo de la pantalla segun el tipo de dato
	                             //estos datos son obtenidos de la tabla 000010
		  $wobligatorio= array();  //Aca se almacena la obligatoriedad o no de cada campo de la pantalla segun el tipo de dato
	      $wvariables  = array();  //SE alamcenan los nombres de los campos en pantalla para validarlos en este programa
	      $wcar_obl    = array();
	      
	      $wok=true;
	      
	      $wdetpro="000051";
		  $wdetcon="";
		  $wdetorp="";
		  $wdettip="";
		  $wdetdes="";
		  $wdetarc="";
		  $wdetcav="";
		  $wdetvde="";
		  $wdetnpa="";
		  $wdetvim="";
		  $wdetume="";
		  $wdetcol="";
		  $wdethl7="";
		  $wdetjco="";
		  $wdetsiv="";
		  $wdetase="";
		  $wdetved="";
		  $wdetimp="";
		  $wdetimc="";
		  $wdetvco="";
		  $wdetvcr="";
		  $wdetobl="";
		  $wdetdep="";
		  $wdetcde="";
		  $wdeturl="";
		  $wdetfor="";
		  $wdetcco="";
		  $wdetcac="";
		  $wdetnse="";
		  $wdetfac="";
		  $wdetest="";
	      
		  
		  //consultar($wvalidacion, $wobligatorio, $wvariables); 
		  //iniciar($wdetpro, $wvalidacion, $wobligatorio, $wvariables);
		  iniciar($wvalidacion, $wobligatorio, $wvariables, $wcar_obl);
		 }
        else
          {
	       evaluar_actividad_campos($wdettip,$wvalidacion, $wobligatorio, $wvariables, $wcar_obl); 
	      } 
	 }
       
  
       	   

//====================================================================================================================
//DESDE ACA SE COMIENZA EL AREA DE LA PRESENTACION
//====================================================================================================================
      
//echo "<table width='98%' height='322' border='0'>";
echo "<table>";


//evaluar_campos_boleanos();


$k=0;

//====== 1ra Linea ======
echo "<tr class=encabezadoTabla>"; 
echo "<td bgcolor='#C3D9FF'><font color='#000066'><strong>".$wcar_obl[$k]."C&oacute;digo Protocolo</strong></font></td>";
echo "<td bgcolor='#E8EEF7'><input type='text' name='wvfopro' value='".$wdetpro."' ".$wvalidacion[$k]."></td>";     //$wdetpro
$k++;

//CAMPO DIGITADO DEL PROTOCOLO
if (isset($wdetpro))
  {
	$q = " SELECT detcon, detdes "
	    ."   FROM ".$wbasedato."_000002 "
	    ."  WHERE detest = 'on' "
	    ."  ORDER BY 1 ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	echo "<td bgcolor='#C3D9FF'>";   
	echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo Digitado</strong></font></td>";
	echo "<td bgcolor='#E8EEF7'><SELECT name='wvfocdi' ".$wvalidacion[$k].">";                                     //$wdetcav
	if (isset($wvfocdi) and ($wvfocdi!=""))   
	   {
	    $wvfocdi = explode("-",$wvfocdi);
	       
	    $q1 = " SELECT detpro, detdes "
	         ."   FROM ".$wbasedato."_000002 "
	         ."  WHERE detpro = '".$wvfocdi[0]."'"
	         ."    AND detest = 'on' "
	         ."  ORDER BY 1 ";
	    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num1 = mysql_num_rows($res1);
	    if ($num1>0)
	      {
	       $row1= mysql_fetch_array($res1);
	       echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]."</option>";
          } 
	   }
  
	for ($j=1;$j<=$num;$j++)
	   { 
		$row = mysql_fetch_array($res);   
		echo "<OPTION>".$row[0]."-".$row[1]."</option>";
	   }
   echo "</SELECT></td>";
  }
 else
    {
	 echo "<td bgcolor='#C3D9FF'>";   
	 echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo Digitado</strong></td>";
	 echo "<td bgcolor='#C3D9FF'>&nbsp</td>";
	}  
$k++;

//CAMPO ASOCIADO AL CAMPO DIGITADO DEL PROTOCOLO
if (isset($wdetpro))
  {
	$q = " SELECT detpro, detdes "
	    ."   FROM ".$wbasedato."_000002 "
	    ."  WHERE detest = 'on' "
	    ."  ORDER BY 1 ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	echo "<td bgcolor='#C3D9FF'>";   
	echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo Asociado</strong></font></td>";
	echo "<td bgcolor='#E8EEF7'><SELECT name='wvfocas' ".$wvalidacion[$k].">";                                     //$wdetcav
	if (isset($wvfocas) and ($wvfocas!=""))   
	   {
	    $wvfocas = explode("-",$wvfocas);
	       
	    $q1 = " SELECT detpro, detdes "
	         ."   FROM ".$wbasedato."_000002 "
	         ."  WHERE detpro = '".$wvfocas[0]."'"
	         ."    AND detest = 'on' "
	         ."  ORDER BY 1 ";
	    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num1 = mysql_num_rows($res1);
	    if ($num1>0)
	      {
	       $row1= mysql_fetch_array($res1);
	       echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]."</option>";
          } 
	   }
  
	for ($j=1;$j<=$num;$j++)
	   { 
		$row = mysql_fetch_array($res);   
		echo "<OPTION>".$row[0]."-".$row[1]."</option>";
	   }
   echo "</SELECT></td>";
  }
 else
    {
	 echo "<td bgcolor='#C3D9FF'>";   
	 echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo Asociado</strong></td>";
	 echo "<td bgcolor='#C3D9FF'>&nbsp</td>";
	}  
echo "</tr>";
$k++;



//====== 2da Linea ======
echo "<tr class=encabezadoTabla>";
//TABLA DE VALIDACION
$q = " SELECT medico, codigo, nombre "
    ."   FROM formulario "
    ."  WHERE activo = 'A' "
    ."    AND tipo   = 'C' "
    ."  ORDER BY 1 ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
$num = mysql_num_rows($res);

echo "<td bgcolor='#C3D9FF'>";   
echo "<font color='#000066'><strong>".$wcar_obl[$k]."Tabla de Validación</strong></font></td>";
echo "<td bgcolor='#E8EEF7'><SELECT name='wvfotva' onchange='enter()' ".$wvalidacion[$k].">";                      //$wdetarc
$k++;

if (isset($wvfotva))   
   {
    $wvfotva1 = explode("-",$wvfotva);
       
    $q = " SELECT medico, codigo, nombre "
        ."   FROM formulario "
        ."  WHERE medico = '".$wvfotva1[0]."'"
        ."    AND codigo = '".$wvfotva1[1]."'"
        ."    AND nombre = '".$wvfotva1[2]."'"
        ."  ORDER BY 1 ";
    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row1 = mysql_fetch_array($res1);
            
    echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";   
   }
for ($j=1;$j<=$num;$j++)
   { 
	$row = mysql_fetch_array($res);   
	echo "<OPTION>".$row[0]."-".$row[1]."-".$row[2]."</option>";
   }
echo "</SELECT></td>";



//CAMPO A VALIDAR DE LA TABLA DE VALIDACION
if (isset($wvfotva1[1]))
  {
	$q = " SELECT campo, descripcion "
	    ."   FROM det_formulario "
	    ."  WHERE medico = '".$wvfotva1[0]."'"
	    ."    AND codigo = '".$wvfotva1[1]."'"
	    ."    AND activo = 'A' "
	    ."  ORDER BY 1 ";
	    
	    
	    
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	echo "<td bgcolor='#C3D9FF'>";   
	echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo a Validar de la Tabla de Validación</strong></font></td>";
	echo "<td bgcolor='#E8EEF7'><SELECT name='wdetcav' ".$wvalidacion[$k].">";                                     //$wdetcav
	if (isset($wdetcav) and ($wdetcav!=""))   
	   {
	    $wdetcav1 = explode("-",$wdetcav);
	       
	    $q1 = " SELECT campo, descripcion "
	         ."   FROM det_formulario "
	         ."  WHERE medico = '".$wvfotva1[0]."'"
	         ."    AND codigo = '".$wvfotva1[1]."'"
	         ."    AND campo  = '".$wdetcav1[0]."'"
	         ."    AND activo = 'A' "
	         ."  ORDER BY 1 ";
	    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num1 = mysql_num_rows($res1);
	    if ($num1>0)
	      {
	       $row1= mysql_fetch_array($res1);
	       echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]."</option>";
          } 
	   }
  
	for ($j=1;$j<=$num;$j++)
	   { 
		$row = mysql_fetch_array($res);   
		echo "<OPTION>".$row[0]."-".$row[1]."</option>";
	   }
   echo "</SELECT></td>";
  }
 else
    {
	 echo "<td bgcolor='#C3D9FF'>";   
	 echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo a Validar de la Tabla de Validación</strong></td>";
	 echo "<td bgcolor='#C3D9FF'>&nbsp</td>";
	}  
$k++;	   

 
//TIPO DE VALIDACION
$q = " SELECT tvades "
    ."   FROM ".$wbasedato."_000011 "
    ."  WHERE tvaest = 'on' "
    ."  ORDER BY 1 ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
$num = mysql_num_rows($res);

echo "<td bgcolor='#C3D9FF'>";   
echo "<font color='#000066'><strong>".$wcar_obl[$k]."Tipo de Validación</strong></font></td>";
echo "<td bgcolor='#E8EEF7'><SELECT name='wvfotiv' ".$wvalidacion[$k].">";                                     //$wdetcav
if (isset($wvfotiv) and ($wvfotiv!=""))   
   {
    $q1 = " SELECT tvades "
         ."   FROM ".$wbasedato."_000011 "
         ."  WHERE tvades = '".$wvfotiv."'"
         ."    AND tvaest = 'on' "
         ."  ORDER BY 1 ";
    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num1 = mysql_num_rows($res1);
    if ($num1>0)
      {
       $row1= mysql_fetch_array($res1);
       echo "<OPTION SELECTED>".$row1[0]."</option>";
      } 
   }

for ($j=1;$j<=$num;$j++)
   { 
	$row = mysql_fetch_array($res);   
	echo "<OPTION>".$row[0]."</option>";
   }
echo "</SELECT></td>";
$k++;
echo "</tr>";


//====== 3ra Linea ======
echo "<tr class=encabezadoTabla>";
//TABLA ORIGEN ASOCIADA
$q = " SELECT medico, codigo, nombre "
    ."   FROM formulario "
    ."  WHERE activo = 'A' "
    ."    AND tipo   = 'C' "
    ."  ORDER BY 1 ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
$num = mysql_num_rows($res);

echo "<td bgcolor='#C3D9FF'>";   
echo "<font color='#000066'><strong>".$wcar_obl[$k]."Tabla Origén Asociada</strong></font></td>";
echo "<td bgcolor='#E8EEF7'><SELECT name='wvfotoa' onchange='enter()' ".$wvalidacion[$k].">";                      //$wdetarc
$k++;

if (isset($wvfotoa))   
   {
    $wvfotoa1 = explode("-",$wvfotoa);
       
    $q = " SELECT medico, codigo, nombre "
        ."   FROM formulario "
        ."  WHERE medico = '".$wvfotoa1[0]."'"
        ."    AND codigo = '".$wvfotoa1[1]."'"
        ."    AND nombre = '".$wvfotoa1[2]."'"
        ."  ORDER BY 1 ";
    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row1 = mysql_fetch_array($res1);
            
    echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";   
   }
for ($j=1;$j<=$num;$j++)
   { 
	$row = mysql_fetch_array($res);   
	echo "<OPTION>".$row[0]."-".$row[1]."-".$row[2]."</option>";
   }
echo "</SELECT></td>";



//CAMPO INICIAL TABLA ASOCIADA
if (isset($wvfotoa1[1]))
  {
	$q = " SELECT campo, descripcion "
	    ."   FROM det_formulario "
	    ."  WHERE medico = '".$wvfotoa1[0]."'"
	    ."    AND codigo = '".$wvfotoa1[1]."'"
	    ."    AND activo = 'A' "
	    ."  ORDER BY 1 ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	echo "<td bgcolor='#C3D9FF'>";   
	echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo inicial Tabla Asociada</strong></font></td>";
	echo "<td bgcolor='#E8EEF7'><SELECT name='wvfocit' ".$wvalidacion[$k].">";                                     //$wdetcav
	if (isset($wvfocit) and ($wvfocit!=""))   
	   {
	    $wvfocit1 = explode("-",$wvfocit);
	       
	    $q1 = " SELECT campo, descripcion "
	         ."   FROM det_formulario "
	         ."  WHERE medico = '".$wvfotoa1[0]."'"
	         ."    AND codigo = '".$wvfotoa1[1]."'"
	         ."    AND campo  = '".$wvfocit1[0]."'"
	         ."    AND activo = 'A' "
	         ."  ORDER BY 1 ";
	    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num1 = mysql_num_rows($res1);
	    if ($num1>0)
	      {
	       $row1= mysql_fetch_array($res1);
	       echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]."</option>";
          } 
	   }
  
	for ($j=1;$j<=$num;$j++)
	   { 
		$row = mysql_fetch_array($res);   
		echo "<OPTION>".$row[0]."-".$row[1]."</option>";
	   }
   echo "</SELECT></td>";
  }
 else
    {
	 echo "<td bgcolor='#C3D9FF'>";   
	 echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo inicial Tabla Asociada</strong></td>";
	 echo "<td bgcolor='#C3D9FF'>&nbsp</td>";
	}  
$k++;	   

 
//CAMPO FINAL TABLA ASOCIADA
if (isset($wvfotoa1[1]))
  {
	$q = " SELECT campo, descripcion "
	    ."   FROM det_formulario "
	    ."  WHERE medico = '".$wvfotoa1[0]."'"
	    ."    AND codigo = '".$wvfotoa1[1]."'"
	    ."    AND activo = 'A' "
	    ."  ORDER BY 1 ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	echo "<td bgcolor='#C3D9FF'>";   
	echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo Final Tabla Asociada</strong></font></td>";
	echo "<td bgcolor='#E8EEF7'><SELECT name='wvfocft' ".$wvalidacion[$k].">";                                     //$wdetcav
	if (isset($wvfocit) and ($wvfocit!=""))   
	   {
	    $wvfocft1 = explode("-",$wvfocft);
	       
	    $q1 = " SELECT campo, descripcion "
	         ."   FROM det_formulario "
	         ."  WHERE medico = '".$wvfotoa1[0]."'"
	         ."    AND codigo = '".$wvfotoa1[1]."'"
	         ."    AND campo  = '".$wvfocft1[0]."'"
	         ."    AND activo = 'A' "
	         ."  ORDER BY 1 ";
	    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num1 = mysql_num_rows($res1);
	    if ($num1>0)
	      {
	       $row1= mysql_fetch_array($res1);
	       echo "<OPTION SELECTED>".$row1[0]." - ".$row1[1]."</option>";
          } 
	   }
  
	for ($j=1;$j<=$num;$j++)
	   { 
		$row = mysql_fetch_array($res);   
		echo "<OPTION>".$row[0]."-".$row[1]."</option>";
	   }
   echo "</SELECT></td>";
  }
 else
    {
	 echo "<td bgcolor='#C3D9FF'>";   
	 echo "<font color='#000066'><strong>".$wcar_obl[$k]."Campo Final Tabla Asociada</strong></td>";
	 echo "<td bgcolor='#C3D9FF'>&nbsp</td>";
	}  
$k++;



//====== 4ta Linea ======
echo "<tr class=encabezadoTabla>"; 
echo "<td bgcolor='#C3D9FF'>";   
	echo "<font color='#000066'><strong>".$wcar_obl[$k]."Criterio</strong></font></td>";
	echo "<td bgcolor='#E8EEF7'><SELECT name='wvfocri' ".$wvalidacion[$k].">";                                     //$wdetcav
	if (isset($wvfocri) and (trim($wvfocri)!=""))   
	   {
	    echo "<OPTION SELECTED>".$wvfocri."</option>";
       }
	 // else
	 //
	 //    {
		  echo "<OPTION>&nbsp</OPTION>";
		  echo "<OPTION>:  - Rango</OPTION>";
		  echo "<OPTION>=  - Igual</OPTION>";
		  echo "<OPTION>>  - Mayor que</OPTION>";
		  echo "<OPTION>>= - Mayor o Igual</OPTION>";
		  echo "<OPTION><  - Menor que</OPTION>";
		  echo "<OPTION><= - Menor o Igual</OPTION>";
	 // }     
		 
echo "<td bgcolor='#C3D9FF'><font color='#000066'><strong>".$wcar_obl[$k]."Estado</strong></font></td>";
//echo "<td bgcolor='#E8EEF7'> <input name='wdetest' type='checkbox' value='".$wdetest."' ".$wvalidacion[$k]."></td>";       //Estado
echo "<td bgcolor='#E8EEF7'> <input name='wdetest' type='checkbox' ".$wvalidacion[$k]."></td>";       //Estado
$k++;
echo "</tr>";


echo "</table>";
echo "</CENTER>";
echo "<div align='center'>";   
echo "<p>";
echo "<input type='submit' name='Iniciar'   value='Iniciar'>";
echo "&nbsp&nbsp;|&nbsp"; 
echo "<input type='submit' name='Grabar'    value='Grabar'>";
echo "&nbsp;|&nbsp"; 
echo "<input type='submit' name='Modificar' value='Modificar'>";
echo "&nbsp;|&nbsp"; 
echo "<input type='submit' name='Consultar' value='Consultar'>";
echo "</p>";
echo "</div>";
?>
</form>
</body>
</html>
