<?php
include_once("conex.php"); header("Content-Type: text/html;charset=ISO-8859-1"); ?>

<html>
<head>

<title>Detalle Protocolos</title>

    <link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet' />
	<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet' />

	<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
	
<script type="text/javascript" src="tabbed.js"></script>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body BGCOLOR=#DDDDDD>
<script type="text/javascript">
    //debugger	 
	 
	
	//$(document).ready(function()
	//  {  
	//    var f = 0;
	//	while (f <= wcanfil)
	//	  {
	//	   c=0
	//	   while (c < wcancol)
    //         {		   
	//		  $('#'+f.toString()+"-"+c.toString()).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	//		  //$(wcanfil.toString()+"-"+wcancol.toString()).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	//	      c++;
	//		 }
    //       f++;			 
	//	  }
	//  })
	  
	
	
	function enter()
	{
	   document.forms.DetalleProtocolos.submit();
	}
	
	function leyenda()
	{
	   document.write("alerta");
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     } 
</script>

<!-- Programa en PHP -->
<?php





include_once("root/magenta.php");
include_once("root/comun.php");

$wactualiz = "(Noviembre 9 2012)";


//$wbasedato="hce";   //OJO **** OJO **** CAMBIAR
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');

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
  
function validarSiHayDatos($wdetpro, $wdetcon, $wtip)
  {
   global $conex;	  
   global $wbasedato;
   
   //Busco si el tipo de campo con el consecutivo y formulario ya tiene datos
   $q = " SELECT COUNT(*) "
       ."   FROM ".$wbasedato."_".$wdetpro
	   ."  WHERE movpro = '".$wdetpro."'"
	   ."    AND movcon = ".$wdetcon;
	//   ."    AND movtip = '".$wtip."'";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   $row= mysql_fetch_array($res);
   
   if ($row[0] > 0)
	  return "on";
     else
       return "off";	 
  }
  
function validar_campos()
  {
   global $conex;	  
   global $wbasedato;
   global $wvariables;
   global $wcar_obl;
   global $wvalidacion;
   global $wobligatorio;
   global $wnom_variables;
   
   global $wok;
   
   //On
   //var_dump($wnom_variables);
   
   
   //con esto defino global todas las variables que estan definidas en la tabla 000010
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global $$wvariables[$i];
      } 
   
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
		   echo "<input type='HIDDEN' name='wvalidacion[".$i."]'    value='".$wvalidacion[$i]."'>";
		   echo "<input type='HIDDEN' name='wobligatorio[".$i."]'   value='".$wobligatorio[$i]."'>";
		   echo "<input type='HIDDEN' name='wvariables[".$i."]'     value='".$wvariables[$i]."'>";
		   echo "<input type='HIDDEN' name='wcar_obl[".$i."]'       value='".$wcar_obl[$i]."'>";  
		   echo "<input type='HIDDEN' name='wnom_variables[".$i."]' value='".$wnom_variables[$i]."'>";  
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
   global $wvariables;
   global $wbasedato;
   global $conex;
	  	  	  
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global $$wvariables[$i];
	         
	   //Averiguo por cada campo si es BOOLEANO y si si, establezco checked o no.   
	   $q = " SELECT COUNT(*) "
	       ."   FROM det_formulario "
	       ."  WHERE medico      = '".$wbasedato."'"
	       ."    AND codigo      = '000002'"
	       ."    AND descripcion = '".substr($wvariables[$i],1,strlen($wvariables[$i]))."'"
	       ."    AND tipo        = '10' "
	       ."    AND activo      = 'A' ";
	   $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	   $row= mysql_fetch_array($res);
	   
	   if ($row[0] > 0)
	     {
		  if (isset($$wvariables[$i]) and trim($$wvariables[$i])=="on")
		     {
			  $$wvariables[$i]="checked";      //Con esto se chulea el campo en pantalla
		     }  
		    else
		        $$wvariables[$i]="uncheked"; 
		 }    
	  }    
  }	  
    
//=================================================================================================================================
function grabar()
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   global $wvariables;
   
   global $wok;
   
   //con esto defino global todas las variables que estan definidas en la tabla 000010
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global $$wvariables[$i];
      }
   
   global $wdetpro;
   
   
   if ($wok==true)
     {
	   for ($i=0;$i<count($wvariables);$i++)
      	  {   
		   //Averiguo por cada campo si es BOOLEANO y si si, establezco checked o no, grabo un 'on' o un 'off'.   
		   $q = " SELECT COUNT(*) "
		       ."   FROM det_formulario "
		       ."  WHERE medico      = '".$wbasedato."'"
		       ."    AND codigo      = '000002'"
		       ."    AND descripcion = '".substr($wvariables[$i],1,strlen($wvariables[$i]))."'"
		       ."    AND tipo        = '10' "
		       ."    AND activo      = 'A' ";
		   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   $row= mysql_fetch_array($res);
		   
		   if ($row[0] > 0)
		     {
			  if ($$wvariables[$i]==true)
			     {
			      $$wvariables[$i]="on";      //Con esto se chulea el campo en pantalla
			     }  
			    else
			        $$wvariables[$i]="off"; 
			 }
          }		        
	    
       //Busco que consecutivo sigue en el protocolo
	   $q = " SELECT MAX(detcon) "
	       ."   FROM ".$wbasedato."_000002 "
	       ."  WHERE detpro = '".$wdetpro."'";
	   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);
	   
	   if ($num > 0)
	      {
		   $row = mysql_fetch_array($res);
		   
		   if ($wdetcon > ($row[0]+1))
		      $wdetcon = $row[0]+1;
		  }    
		  
		switch (trim($wdettip))
		  {
			case "Seleccion":
			   $wdetarc = "000012";
		       $wdetcav1=explode("_",$wdetcav);
		       $wdetcav=$wdetcav1[0]; 
		       
		       break;
		       
		    case "Referencia":
		       $wdetarc1 = explode("_",$wdetarc);
			   $wdetarc  = $wdetarc1[0];
			   
			   $wdetcav1 = explode("_",$wdetcav);
		       $wdetcav  = $wdetcav1[0];
		       
		       break;
		     
		    default:
			   if (trim($wdetarc) != "" and trim($wdetarc) != "_")
			      {
				   $wdetarc1 = explode("_",$wdetarc);
				   $wdetarc  = $wdetarc1[0]."_".$wdetarc1[1];
			      }
				 else
					$wdetarc = "";
					
			   if ($wdetcav != "" and trim($wdetcav) != "_")
			      {	  
				   $wdetcav1 = explode("_",$wdetcav);
				   $wdetcav  = $wdetcav1[0];
				  }
				 else
					$wdetcav = ""; 
		  }		  
		  

	  //Inserto el registro en la tabla de Configuracion del protocolo   
	  $q= " INSERT INTO ".$wbasedato."_000002 (   Medico       ,   fecha_data,   hora_data,    detpro    ,    detcon    ,    detorp    ,    dettip    ,    detdes    ,    detarc    ,    detcav    ,    detvde    ,    detnpa    ,   detvim     ,   detume     ,   detcol     ,   dethl7     ,   detjco     ,    detsiv    ,    detase    ,    detved    ,    detimp    ,    detimc    ,    detvco    ,    detvcr    ,    detobl    ,    detdep    ,    detcde    ,    deturl    ,    detfor    ,    detcco    ,    detcac    ,    detnse    ,    detfac    ,    detest    ,    detcoa    ,    detprs    ,    detalm    ,    detanm    ,    detlrb    ,    detdde    ,    detcbu    ,    detnbu    ,    dettta    ,    detcua    ,    detccu    ,    detcro    ,    dettii    ,    detdpl    ,    detvmi    ,    detvma    , Seguridad         ) "
		 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wdetpro."','".$wdetcon."','".$wdetorp."','".$wdettip."','".$wdetdes."','".$wdetarc."','".$wdetcav."','".$wdetvde."','".$wdetnpa."','".$wdetvim."','".$wdetume."','".$wdetcol."','".$wdethl7."','".$wdetjco."','".$wdetsiv."','".$wdetase."','".$wdetved."','".$wdetimp."','".$wdetimc."','".$wdetvco."','".$wdetvcr."','".$wdetobl."','".$wdetdep."','".$wdetcde."','".$wdeturl."','".$wdetfor."','".$wdetcco."','".$wdetcac."','".$wdetnse."','".$wdetfac."','".$wdetest."','".$wdetcoa."','".$wdetprs."','".$wdetalm."','".$wdetanm."','".$wdetlrb."','".$wdetdde."','".$wdetcbu."','".$wdetnbu."','".$wdettta."','".$wdetcua."','".$wdetccu."','".$wdetcro."','".$wdettii."','".$wdetdpl."','".$wdetvmi."','".$wdetvma."', 'C-".$wbasedato."') ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 }    
  }	  
//=================================================================================================================================
function insert_orden_pantalla()
  {
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wok;
   global $wcodpro;
   global $wdetpro;
   global $wdetorp;
   global $wnompro;
   global $wdetcon;
   global $wtipuso;
   global $wtipfor;
   global $wtipimp;
   global $walerta;
   global $westado;
   
   global $worpaux;	   //Aca esta el orden en pantalla que tenia cuando se consulto el registro  
	  
   //Si entra por aca es porque se esta es grabando, entonces averiguo si el orden a poner ya existe o es menor a alguno que 
   //ya exista, para hacer la reorganizacion
   $q = " SELECT COUNT(*) "
       ."   FROM ".$wbasedato."_000002 "
       ."  WHERE detpro = '".$wdetpro."'"
       ."    AND detorp = ".$wdetorp
       ."    AND detest = 'on' ";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);
   $row= mysql_fetch_array($res);
   
   if ($row[0]>0)   //Si existe el orden, aumento la secuencia
      {
	   $q = " UPDATE ".$wbasedato."_000002 "
	       ."    SET detorp = detorp + 1 "
	       ."  WHERE detpro = '".$wdetpro."'"
	       ."    AND detest = 'on' "
	       ."    AND detorp >=  ".$wdetorp;
	   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());      
	  }
  }	  


function cambiar_orden_pantalla()
  {
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wok;
   global $wcodpro;
   global $wdetpro;
   global $wdetorp;
   global $wdetcon;
   global $wtipuso;
   global $wtipfor;
   global $wtipimp;
   global $walerta;
   global $westado;
   
   global $worpaux;	   //Aca esta el orden en pantalla que tenia cuando se consulto el registro
	  
   
   if ($wdetorp != $worpaux)
     {
	  if ($wdetorp < $worpaux)
        {
	      //Busco si el Orden en Pantalla NUEVO existe en la tabla, porque si no, no hay que incrementar los otros campos  
	      $q = " SELECT COUNT(*) "
	          ."   FROM ".$wbasedato."_000002 "
	          ."  WHERE detpro = '".$wdetpro."'"
	          ."    AND detorp = ".$wdetorp       //Orden nuevo
	          ."    AND detest = 'on' ";
	      $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());     
	      $row = mysql_fetch_array($res);    
	   
	      if ($row[0] > 0)  //Indica que si existe el orden, si si, debe incrementarse
	         {
		      $q = " DELETE FROM ".$wbasedato."_000002 "
		          ."  WHERE detpro = '".$wdetpro."'"
		          ."    AND detorp = ".$worpaux;
		      $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		         
		        
			  $q = " UPDATE ".$wbasedato."_000002 "
			      ."    SET detorp = detorp + 1 "
			      ."  WHERE detpro = '".$wdetpro."'"
			      ."    AND detest = 'on' "
			      ."    AND detorp < ".$worpaux
			      ."    AND detorp >=  ".$wdetorp;
			  $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 } 
        }
       else
          {
	       if ($wdetorp > $worpaux and trim($worpaux) != "")
	          {  
		       $q = " UPDATE ".$wbasedato."_000002 "
			       ."    SET detorp  = detorp - 1 "
			       ."  WHERE detpro  = '".$wdetpro."'"
			       ."    AND detest  = 'on' "
			       ."    AND detorp > ".$worpaux
			       ."    AND detorp <=  ".$wdetorp;
			   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  }
	         else
	           insert_orden_pantalla();   //por aca entra solo si es nuevo
	      }         
     }
  } 

function modificar()
  {
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   global $wvariables;
   
   
   //con esto defino global todas las variables que estan definidas en la tabla 000010
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global $$wvariables[$i];
      }
   
   global $wok;
   	  
   if ($wok==true)
     {
	  cambiar_orden_pantalla();   
	     
	  //Primero se borra y luego se graba.
	  $q = " DELETE FROM ".$wbasedato."_000002 "
	      ."  WHERE detpro = '".$wdetpro."'"
	      ."    AND detcon = ".$wdetcon;
	  $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  
	  grabar();
	 }     
  } 
  
  
function evaluar_actividad_campos($wdettip)
  {
   global $conex;	  
   global $wbasedato;
   global $wvariables;
   global $wcar_obl;
   global $wvalidacion;
   global $wobligatorio;
   
   //con esto defino global todas las variables que estan definidas en la tabla 000010
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global $$wvariables[$i];
      }
   
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
     	    $wcar_obl[$i]="<font color=0000FF>(*)</font>";          //Este caracter lo debe mostrar en la pantalla en cada campo obligatorio
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
      evaluar_campos_boleanos();
     }
  }	  
  
function consultar()
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wvariables;
   global $wobligatorio;
   global $wvalidacion;
   global $wcar_obl;
   
   global $wcampos_select;
   
   global $wdetpro;
   global $wbotgra;  
   global $wbotmod;
   
   //global $wtip;     //Tipo de dato que se consulto
   global $worpaux;  //Orden en pantalla que se consulto
   
      
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global $$wvariables[$i];
	  }
      
   if (isset($wdetpro))
      {
	   //Traigo todos los campos de la tabla 000002 que estan en el arreglo "$wcampos_select"
	   $q = " SELECT ".$wcampos_select
	       ."   FROM ".$wbasedato."_000002 "
	       ."  WHERE detpro = '".$wdetpro."'"
	       ."    AND detcon = ".$wdetcon
	       ."  ORDER BY 1 ";
	   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
       $num = mysql_num_rows($res);    
	   
       if ($num > 0)
         {
	      $row = mysql_fetch_array($res);
	      $wcol= mysql_num_fields($res);    //Total de campos de trae la tupla
	      
	      $wtip=$row[3];                    //Tipo de dato que se consulto
	      $q = " SELECT tipvar "
	          ."   FROM ".$wbasedato."_000010 "
	          ."  WHERE tipdat = '".$wtip."'";
	      $resvar = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $numvar = mysql_num_rows($resvar);
          if ($numvar > 0)
             {
	          $rowvar = mysql_fetch_array($resvar);   
	          $wcampos=explode("-",$rowvar[0]);    //Variables
	          
	          for ($j=0;$j<$wcol;$j++)
	   	         {
		   	      $$wcampos[$j]=$row[$j];    
		   	      echo "<INPUT TYPE='hidden' NAME='".$$wcampos[$j]."' VALUE = '".$row[$j]."'>";
		   	     }
	         }    
   	      $worpaux=$row['detorp'];  //Aca llevo el consecutivo con el se realizo la consulta, por si se modifica saber si se inserta o se borra.  
	   	  echo "<INPUT TYPE='hidden' NAME=worpaux VALUE = '".$worpaux."'>";
		  echo "<INPUT TYPE='hidden' NAME=wtip VALUE = '".$wtip."'>";
	   	  
	   	  $wbotmod="ENABLED";  
   		  $wbotgra="DISABLED";
   	     } 
   	    else
   	       {
	   	    iniciar();   
	   	    $wbotmod="DISABLED";  
   		    $wbotgra="ENABLED";   
   	       }      
   	   echo "<input type='HIDDEN' name=wformulario  value='".$wdetpro."'>"; 
   	  }
   	    
   if (isset($wdettip)) 
      {
       evaluar_actividad_campos($wdettip);
      } 
  } 
  
   
function iniciar()
  {
   global $conex;
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wformulario;
   global $wvariables;
   global $wnom_variables;
   global $wobligatorio;
   global $wvalidacion;
   global $wcar_obl;
   
   global $wbotmod;
   global $wbotgra;
   
   
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global $$wvariables[$i];
	   
	   if ($i == 0)   //Para el campo "detpro"
	      {
		   $$wvariables[$i]=$wformulario; 
		  }
	     else
	        {
	         $$wvariables[$i]="";  
	        }
	  }
	  
   //Busco que consecutivo sigue en el protocolo
   $q = " SELECT MAX(detcon), MAX(detorp) "
       ."   FROM ".$wbasedato."_000002 "
       ."  WHERE detpro = '".$wdetpro."'";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);
   
   if ($num > 0)
      {
	   $row = mysql_fetch_array($res);
	   
	   for ($i=0;$i<count($wvariables);$i++)
	      {
		   if ($wvariables[$i] == "wdetcon")
		      {
			   $$wvariables[$i]=$row[0]+1;
			  } 
		   if ($wvariables[$i] == "wdetorp")
		      {
			   $$wvariables[$i]=$row[1]+1;
			  }
		  } 
	  }
   $wbotmod="DISABLED";  
   $wbotgra="ENABLED"; 
  }
  
  
function mostrar_grilla()
  {
   global $conex;
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wvariables;
   global $wnom_variables;
   
   global $wcampos_select;
   global $num_campos;
   
   global $wcancol;
   global $wcanfil;
   
   for ($i=0;$i<count($wvariables);$i++)
      {
	   global $$wvariables[$i];
	  }
	   
   //Traigo todos los campos del formulario wdetpro
   $q = " SELECT ".$wcampos_select
       ."   FROM ".$wbasedato."_000002 "
       ."  WHERE detpro = '".$wdetpro."'"
       ."  ORDER BY detorp ";
   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);
   
   $num_campos=$num;
   
   if ($num > 0)
      {
	   echo "<center><table>";   
	   
	   echo "<tr class=encabezadoTabla>";
	   
	   //Aca imprimo los titulos o nombres de las columnas.
	   for ($i=0;$i<count($wvariables);$i++)
	      {
		   echo "<th>".$wnom_variables[$i]."</th>";
		  }
	   	   
	   $wcancol = count($wnom_variables);   //Cantidad de campos que tiene la tabla 000002 para la configuracion de c/formulario
	   $wcanfil = $num;                     //Cantidad de campos (metadata) que tiene configurado el protocolo o formulario
		  
	   //Inicializo las variables en Javascript
	   echo "<script language='Javascript'>";
	 	  echo "wcancol=".$wcancol.";";
		  echo "wcanfil=".$wcanfil.";";
	   echo "</script>"; 
		  
	   for ($i=1;$i<=$num;$i++)
	      {
		   if (is_integer($i/2))
	         $wclass="fila1";
	        else
	          $wclass="fila2";
	   
	       $row  = mysql_fetch_array($res);       
	       $wcol = mysql_num_fields($res);
	   
	       echo "<tr class=".$wclass.">";    
	       for ($j=0;$j<($wcol);$j++)
	          {
			  
			                            //onclick='javascript:cerrarModal();
			  //'javascript:evaluarEnvio(\"".$fila."\"".","."\"".$wpatron."\");'
		      //echo "<td onmouseover='javascript:cargarTooltip(\"".$wnom_variables[$j]."-".$i."\");' title='".$wnom_variables[$j]."' id='".$wnom_variables[$j]."-".$i."'>".$row[$j]."</td>";
			  //echo "<td onmouseover='javascript:cargarTooltip(\"".$j."-".$i."\");' title='".$wnom_variables[$j]."' id='".$j."-".$i."'>".$row[$j]."</td>";
			  echo "<td title='".$wnom_variables[$j]."' id='".$i."-".$j."'>".$row[$j]."</td>";
			  } 
	       echo "</tr>";      
	      }
	   echo "</table>";       
	  }        
  }  
  
//=================================================================================================================================
//***************************** T E R M I N A   L A   D E F I N I C I O N   D E   F U N C I O N E S *******************************
//=================================================================================================================================



echo "<form name='DetalleProtocolos' method='post' action=''>";
//encabezado("Encabezado del Protocolo",$wactualiz, "clinica");
echo "<div align='center'>&nbsp<br>";
echo "<CENTER>";


global $grabar;
global $modificar;
global $iniciar;

global $wbotmod;   //Sirve para indicar cuando se prende el boton GRABAR o cuando se apaga
global $wbotgra;   //Sirve para indicar cuando se prende el boton MODIFICAR o cuando se apaga

global $wtip;      //Tipo de dato, que se debe llenar al consultar un registro


if (!isset($wdettip))
 {   
  $wvalidacion = array();  //Este arreglo sirve para guardar el estado que debe tener cada campo de la pantalla segun el tipo de dato
                         //estos datos son obtenidos de la tabla 000010
  $wobligatorio= array();  //Aca se almacena la obligatoriedad o no de cada campo de la pantalla segun el tipo de dato
  $wvariables  = array();  //SE alamcenan los nombres de los campos en pantalla para validarlos en este programa
  $wcar_obl    = array();
  
  global $wvariables;
  global $wobligatorio;
  global $wvalidacion;
  global $wcar_obl;
  global $wnom_variables;
  global $wcampos_select;
  
  
  $wok=true;
  
  //Traigo todos los nombres de variables, almacenados en la tabla 000010, el MAX es para que solo traiga un registro, porque todos los registros deben de tener
  //las mismas variables
  $q = " SELECT tipvar, MAX(id) "
      ."   FROM ".$wbasedato."_000010 "
      ."  GROUP BY 1 ";
  $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
        
  if ($num > 0)
    {
     $row = mysql_fetch_array($res);   
 
     $wvar=explode("-",$row[0]);
     
     $wcampos_select="";
     
     
     for ($i=0;$i<count($wvar);$i++)
       {
	    if ($i>1) $$wvar[$i] = "";    //Esto inicializa todas las variables, excepto codigo y el consecutivo
	    
	    $wvalidacion[$i]     = "Enabled";
        $wobligatorio[$i]    = "On";
        $wvariables[$i]      = $wvar[$i];
        $wcar_obl[$i]        = "";
        $wnom_variables[$i]  = "";
         
        global $$wvar[$i];
        
        if ($i == 0) 
           $$wvar[$i]=$wformulario;  //Aca debe venir el campo wdetpro 
        
              
        if ($wcampos_select=="")
	       $wcampos_select = substr($wvariables[$i],1,strlen($wvariables[$i]));                       //Aca llevo los campos que se seleccionan segun estan en la tabla 000010
	      else 
	   	     $wcampos_select= $wcampos_select.",".substr($wvariables[$i],1,strlen($wvariables[$i]));
	     
        $row = mysql_fetch_array($res);
       }
       
       //Estos son los nombres de los campos en Pantalla  
	   $wnom_variables[0]="Código";
	   $wnom_variables[1]="Consecutivo";
	   $wnom_variables[2]="Orden Pantalla";
	   $wnom_variables[3]="Tipo Dato";
	   $wnom_variables[4]="Unidad Medida";
	   $wnom_variables[5]="Descripción";
	   $wnom_variables[6]="Columna Sangria";
	   $wnom_variables[7]="HL7";
	   $wnom_variables[8]="Joint Comission";
	   $wnom_variables[9]="Nombre Pantalla";
	   $wnom_variables[10]="Sivigila";
	   $wnom_variables[11]="Aplica al Sexo";
	   $wnom_variables[12]="Valida Edad";
	   $wnom_variables[13]="Tabla";
	   $wnom_variables[14]="Campo Tabla";
	   $wnom_variables[15]="Vr Defecto";
	   $wnom_variables[16]="Presentación Continua";
	   $wnom_variables[17]="Validación Complementaria";
	   $wnom_variables[18]="V.Comp.Restrictiva";
	   $wnom_variables[19]="Vr Imprime";
	   $wnom_variables[20]="Obligatorio";
	   $wnom_variables[21]="Depende";
	   $wnom_variables[22]="Campo Depende";
	   $wnom_variables[23]="URL";
	   $wnom_variables[24]="Campos Conjunto";
	   $wnom_variables[25]="Caracter Conjunto";
	   $wnom_variables[26]="Nivel Seguridad";
	   $wnom_variables[27]="Formula";
	   $wnom_variables[28]="Fecha Activación";
	   $wnom_variables[29]="Estado";
	   $wnom_variables[30]="Columnas que Ocupa";
	   $wnom_variables[31]="Dato del Campo que Depende";
	   $wnom_variables[32]="Radio Buton Seleccion";
	   $wnom_variables[33]="Alto Campo Memo";
	   $wnom_variables[34]="Ancho Campo Memo y Texto";
	   $wnom_variables[35]="Campos de Busqueda Tabla";
	   $wnom_variables[36]="Tipo de Tabla (S/M)";
	   $wnom_variables[37]="Listas en el radio Botón";
	   $wnom_variables[38]="Se Imprime";
	   $wnom_variables[39]="Nombres Campos de Busqueda";
	   $wnom_variables[40]="Cualificable 'Tabla' ";
	   $wnom_variables[41]="Caracter Cualificador";
	   $wnom_variables[42]="Cronologico";
	   $wnom_variables[43]="'Tip' Informativo";
	   $wnom_variables[44]="Tabla Desplegable";
	   $wnom_variables[45]="Valor Mínimo";
	   $wnom_variables[46]="Valor Máximo";
    }       
 }
 
switch ($wparametro)
  {
    case "campos":
	   {
  	    //***************************************************************************************************************
		//*********   A C A   C O M I E N Z A   E L   B L O Q U E   P R I C I P A L   D E L   P R O G R A M A   *********
		//***************************************************************************************************************

		//Se evalua el boton presionado
		if (isset($Grabar) or isset($Modificar) or isset($Consultar) or isset($Iniciar))
		   {
			 if (isset($Grabar))
			   {
				validar_campos();
				insert_orden_pantalla();
				grabar();
				
				if ($wok==true)
				   {
					?>	    
				      <script> alert ("El Registro fue Grabado"); </script>
				    <?php
				    iniciar();
			       } 
			    
			    evaluar_campos_boleanos();  
			    
			    //*************************************************
			    //Actualizo la grilla
			    //*************************************************
			    echo "<script language=javascript>";
				   echo "top.principal.grilla.location.reload()";
				echo "</script>";
				//*************************************************
		       }	
		          
			 if (isset($Modificar))
			   {
			    $whay="off"; 

//On
//echo "wtip : ".$wtip."<br>";
//echo "wdettip : ".$wdettip."<br>";

				
			    if ($wtip != $wdettip)                                     //Abril 26 de 2011
				   {
				    $whay=validarSiHayDatos($wdetpro, $wdetcon, $wtip);    //Abril 26 de 2011
				   }
				
				if ($whay=="off")                                          //Abril 26 de 2011
				   { 
					validar_campos();
					if ($wok==true) 
					   { 
						modificar();
						//*************************************************
						//Actualizo la grilla
						//*************************************************
						echo "<script language=javascript>";
							echo "top.principal.grilla.location.reload()";
						echo "</script>";
						//*************************************************
					   }
				   }
				  else                                                 //Abril 26 de 2011
					{
					 ?>
					   <script> alert ("Existen historías con este tipo de dato, NO se puede modificar, inactivelo y cree uno nuevo"); </script>
					 <?php
					} 
				iniciar();				   
		       }
		       
		     if (isset($Consultar))
			   { 
				 consultar();
			   }    
		       
		     if (isset($Iniciar))
			   { iniciar();
			   }  
		   } //fin del if (Grabar or Modificar or Consultar or Borrar)
		  else
		     { 
			  if (isset($wdettip) and trim($wdettip) != "")
			     evaluar_actividad_campos($wdettip);
			    else
			       iniciar(); 
			 }
		       
		//Envio los arreglos en el http	 
		for ($i=0;$i<count($wvariables);$i++)
		    {
			 echo "<input type='HIDDEN' name='wvariables[".$i."]' value='".$wvariables[$i]."'>"; 
			 echo "<input type='HIDDEN' name='wvalidacion[".$i."]' value='".$wvalidacion[$i]."'>";  
	     	 echo "<input type='HIDDEN' name='wobligatorio[".$i."]' value='".$wobligatorio[$i]."'>";
	     	 echo "<input type='HIDDEN' name='wcar_obl[".$i."]' value='".$wcar_obl[$i]."'>"; 
	     	 echo "<input type='HIDDEN' name='wnom_variables[".$i."]' value='".$wnom_variables[$i]."'>";    
	     	}  
		       	   
	     	
	    //====================================================================================================================
	    //====================================================================================================================
		//====================================================================================================================
		//DESDE ACA SE COMIENZA EL AREA DE LA PRESENTACION
		//====================================================================================================================
		//====================================================================================================================
		//====================================================================================================================
		      
		//echo "<table>";

		echo "<table width='98%' height='422' border='0'>";

		$k=0;

		//====== 1ra Linea ======
		echo "<tr class=fila1>"; 
		//echo "<td><b>".$wcar_obl[$k]."C&oacute;digo<br>Protocolo</b></td>";
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input type='text' name='".$wvariables[$k]."' value='".$$wvariables[$k]."' ".$wvalidacion[$k]." size=10></td>";     
		$k++;

		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input type='text' name='".$wvariables[$k]."' value='".$$wvariables[$k]."' ".$wvalidacion[$k]." size=4></td>";     
		$k++;

		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input type='text' name='".$wvariables[$k]."' value='".$$wvariables[$k]."' ".$wvalidacion[$k]." size=4></td>";     
		$k++;

		//TIPO DE DATO
		$q = " SELECT tipdat "
		    ."   FROM ".$wbasedato."_000010 "
		    ."  ORDER BY 1 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><SELECT name='wdettip' ".$wvalidacion[$k]." onchange='enter()'>";                       
		if (isset($wdettip))   
		   {
		    $q = " SELECT tipdat "
		        ."   FROM ".$wbasedato."_000010 "
		        ."  WHERE tipdat = '".$wdettip."'"
		        ."  ORDER BY 1 ";
		    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $row1= mysql_fetch_array($res1);
		            
		    echo "<OPTION SELECTED>".$row1[0]."</option>";   
		   }
		for ($j=1;$j<=$num;$j++)
		   { 
			$row = mysql_fetch_array($res);   
			echo "<OPTION>".$row[0]."</OPTION>";
		   }
		echo "</SELECT></td>";
		$k++;
		
		
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td> <input name='wdetume' type='text' value='".$wdetume."' ".$wvalidacion[$k]." size='5'></td>";  //$wunimed
		$k++;
		echo "</tr>";


		//====== 2da Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td colspan=3><input type='text' name='wdetdes' value='".$wdetdes."' ".$wvalidacion[$k]." size=40></td>";     //$wdetdes
		$k++;

		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdetcol' type='text' value='".$wdetcol."' ".$wvalidacion[$k]." size='2'></td>";  //$wdetcol
		$k++;
		echo "<td><b><font color=3232CD>".$wcar_obl[$k]."</font>".$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdethl7' type='checkbox' ".$wdethl7." ".$wvalidacion[$k]."></td>";               //HL7
		$k++;
		 
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdetjco' type='checkbox' ".$wdetjco." ".$wvalidacion[$k]."></td>";              //Join Commission
		$k++;
		echo "</tr>";
		
		
		//====== 3ra Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td colspan=3> <input name='wdetnpa' type='text' value='".$wdetnpa."' ".$wvalidacion[$k]." size='40'></td>"; //$wnompan
		$k++;
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdetsiv' type='checkbox' ".$wdetsiv." ".$wvalidacion[$k]."></td>";         //Sivigila
		$k++;
		//////
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><SELECT name='wdetase' ".$wvalidacion[$k]." onchange='enter()'>";                       
		if (isset($wdetase) and $wdetase!="")   
		   {
			switch ($wdetase)
			  {
			   case "F":   
		          {
			       echo "<option SELECTED>F</option>";   
		           echo "<option>M</option>";
		           echo "<option>A</option>";
	              } 
		          break; 
		       case "M":   
		          {
			       echo "<option SELECTED>M</option>";   
		           echo "<option>F</option>";
		           echo "<option>A</option>";
	              } 
		          break;
		       case "A":   
		          {
			       echo "<option SELECTED>A</option>";   
		           echo "<option>F</option>";
		           echo "<option>M</option>";
	              } 
		          break;
	          }         
		   }
		  else 
		     {
			  echo "<option>F</option>";   
		      echo "<option>M</option>";
		      echo "<option>A</option>";
		     } 
		echo "</SELECT></td>";
		//////
		$k++;
		echo "<td><b>".$wcar_obl[$k].$wnom_variables[$k]."</b></td>";
		echo "<td><input name='wdetved' type='checkbox' ".$wdetved." ".$wvalidacion[$k]."></td>";         //Valida Edad
		$k++;
		echo "</tr>";
		
		
		//====== 4ta Linea ======
		echo "<tr class=fila1>";
		//==================================================================================================================
		//ARCHIVO DE VALIDACION
		echo "<td><b>".$wcar_obl[$k]."Archivo de<br>Validación</b></td>";
		switch ($wdettip)
		   {
			case "Seleccion":
			   {
				$wdetarc="000012";  //On Ojo si esto cambia por $wbasedato_000012 ==>HCE_000012     
				echo "<td colspan=5><input type='text' name='wdetarc' value='".$wdetarc."' ".$wvalidacion[$k]."></td>";   
			   }   
			   break;
			case "Referencia":
			   {
				$q = " SELECT encpro, encdes "
				    ."   FROM ".$wbasedato."_000001 "
			        ."  WHERE encest = 'on' "
			        ."  ORDER BY 1 ";
			    $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);   
				   
				echo "<td colspan=5><SELECT name='wdetarc' onchange='enter()' ".$wvalidacion[$k].">";                      //$wdetarc
					
				if (isset($wdetarc))   
				   {
					$wdetarc1 = explode("_",$wdetarc);
				       
				    $q = " SELECT encpro, encdes "
				        ."   FROM ".$wbasedato."_000001 "
				        ."  WHERE encpro = '".$wdetarc1[0]."'"
				        ."    AND encest = 'on' "
				        ."  ORDER BY 1 ";
				    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    $row1 = mysql_fetch_array($res1);
				            
				    echo "<OPTION SELECTED>".$row1[0]."_".$row1[1]."</option>";   
				   }
				   
				for ($j=1;$j<=$num;$j++)
				   { 
					$row = mysql_fetch_array($res);   
					echo "<OPTION>".$row[0]."_".$row[1]."_".$row[2]."</option>";
				   }
				echo "</SELECT></td>";  
			   }   
			   break; 
			case "Tabla":
		       {
			    $q = " SELECT medico, codigo, nombre "
			        ."   FROM formulario "
			        ."  WHERE activo = 'A' "
			        ."  ORDER BY 1 ";
				$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
			    echo "<td colspan=5><SELECT name='wdetarc' onchange='enter()' ".$wvalidacion[$k].">";                      //$wdetarc
				
				if (isset($wdetarc))   
				   {
					echo "<OPTION SELECTED>".$wdetarc."</option>";
				   }
				for ($j=1;$j<=$num;$j++)
				   { 
					$row = mysql_fetch_array($res);   
					echo "<OPTION>".$row[0]."_".$row[1]."_".$row[2]."</option>";
				   }
				echo "</SELECT></td>";
	           }
	           break;
	        default:
	           {
	            unset($wdetarc);
	            unset($wdetcav);
	            echo "<td colspan=5>&nbsp</td>";
               }
               break; 
		   }		
		$k++;
		
		//CAMPO A VALIDAR DEL ARCHIVO DE VALIDACION
		switch ($wdettip)
		   {
			case "Seleccion":
			   {
				$q = " SELECT msetab, msedes "
				    ."   FROM ".$wbasedato."_000014 "
				    ."  WHERE mseest = 'on' "
				    ."  ORDER BY 1 ";
				$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				
				echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
				echo "<td colspan=3><SELECT name='wdetcav' ".$wvalidacion[$k]." onchange='enter()'>";                                     //$wdetcav
				if (isset($wdetcav) and ($wdetcav!=""))   
				   {
				    $wdetcav1 = explode("_",$wdetcav);
				       
				    $q1 = " SELECT msetab, msedes "
				         ."   FROM ".$wbasedato."_000014 "
				         ."  WHERE msetab = '".$wdetcav1[0]."'"
				         ."  ORDER BY 1 ";
				    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    $num1 = mysql_num_rows($res1);
				    if ($num1>0)
				      {
				       $row1= mysql_fetch_array($res1);
				       echo "<OPTION SELECTED>".$row1[0]."_".$row1[1]."</option>";
			          } 
				   }
			  
				for ($j=1;$j<=$num;$j++)
				   { 
					$row = mysql_fetch_array($res);   
					echo "<OPTION>".$row[0]."_".$row[1]."</option>";
				   }
			    echo "</SELECT></td>";
			   }
			   break;
			case "Referencia":
			   {
				if (isset($wdetarc1[0]) and trim($wdetarc1[0])!="")
			      {
					$q = " SELECT detcon, detdes "
					    ."   FROM ".$wbasedato."_000002 "
					    ."  WHERE detpro = '".$wdetarc1[0]."'"
					    ."    AND detest = 'on' "
					    ."  ORDER BY 1 ";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res);
					
					echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
					echo "<td colspan=3><SELECT name='wdetcav' ".$wvalidacion[$k].">";                                     //$wdetcav
					if (isset($wdetcav) and ($wdetcav!=""))   
					   {
					    $wdetcav1 = explode("_",$wdetcav);
					       
					    $q1 = " SELECT detcon, detdes "
					         ."   FROM ".$wbasedato."_000002 "
					         ."  WHERE detpro = '".$wdetarc1[0]."'"
					         ."    AND detcon = '".$wdetcav1[0]."'"
					         ."  ORDER BY 1 ";
					    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					    $num1 = mysql_num_rows($res1);
					    if ($num1>0)
					      {
					       $row1= mysql_fetch_array($res1);
					       echo "<OPTION SELECTED>".$row1[0]."_".$row1[1]."</option>";
				          } 
					   }
				  
					for ($j=1;$j<=$num;$j++)
					   { 
						$row = mysql_fetch_array($res);   
						echo "<OPTION>".$row[0]."_".$row[1]."</option>";
					   }
				   echo "</SELECT></td>";
				  }
				 else
				    {
					 echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
					 echo "<td colspan=3>&nbsp</td>";
					}  
			   }
			   break;   
			case "Tabla":
			      {
			       if (isset($wdetarc))
				      {
					    $wdetarc1 = explode("_",$wdetarc);  
					      
						$q = " SELECT campo, descripcion "
						    ."   FROM det_formulario "
						    ."  WHERE medico = '".trim($wdetarc1[0])."'"
						    ."    AND codigo = '".trim($wdetarc1[1])."'"
						    ."    AND activo = 'A' "
						    ."  ORDER BY 1 ";
						$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$num = mysql_num_rows($res);
						
						echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
						echo "<td colspan=3><SELECT name='wdetcav' ".$wvalidacion[$k].">";                                     //$wdetcav
						if (isset($wdetcav) and (trim($wdetcav)!=""))   
						   {
						    $wdetcav1 = explode("_",$wdetcav);
						       
						    $q1 = " SELECT campo, descripcion "
						         ."   FROM det_formulario "
						         ."  WHERE medico = '".trim($wdetarc1[0])."'"
						         ."    AND codigo = '".trim($wdetarc1[1])."'"
						         ."    AND campo  = '".trim($wdetcav1[0])."'"
						         ."    AND activo = 'A' "
						         ."  ORDER BY 1 ";
						    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
						    $num1 = mysql_num_rows($res1);
						    if ($num1>0)
						      {
						       $row1= mysql_fetch_array($res1);
						       echo "<OPTION SELECTED>".$row1[0]."_".$row1[1]."</option>";
					          } 
						   }
					  
						for ($j=1;$j<=$num;$j++)
						   { 
							$row = mysql_fetch_array($res);   
							echo "<OPTION>".$row[0]."_".$row[1]."</option>";
						   }
					   echo "</SELECT></td>";
					  }
					 else
					    {
						 echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
						 echo "<td colspan=3>&nbsp</td>";
						}  
			      }
			      break;	
			default:
	           {
	            echo "<td><b>".$wcar_obl[$k]."Campo a<br>Validar</b></td>";
	            echo "<td colspan=3>&nbsp</td>";
               }
               break;      
		   }	
		$k++;
		
		//====== 5ta Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."Valor por<br>Defecto</b></td>";
		echo "<td colspan=3><input name='wdetvde' type='text' value='".$wdetvde."' ".$wvalidacion[$k]." size='40'></td>"; //$wvaldef
		$k++;
		
		echo "<td><b>".$wcar_obl[$k]."Presentación<br>Continua</b></td>";
		echo "<td><input name='wdetimc' type='checkbox' ".$wdetimc." ".$wvalidacion[$k]."></td>";         //Imprime y muestra el dato luego de la descripción
		$k++;

		echo "<td><b>".$wcar_obl[$k]."Validación<br>Complementaria</b></td>";
		echo "<td><input name='wdetvco' type='checkbox' ".$wdetvco." ".$wvalidacion[$k]."></td>";         //Validacion Complementaria
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Validación<br>Complementaria<br>Restrictiva</b></td>";
		echo "<td><input name='wdetvcr' type='checkbox' ".$wdetvcr." ".$wvalidacion[$k]."></td>";         //Val. Compl. Restrictiva
		$k++;
		echo "</tr>";
		//==================================================================================================================
		
		
		//====== 6ta Linea ======
		echo "<tr class=fila1>"; 
		echo "<td><b>".$wcar_obl[$k]."Valor que<br>Imprime</b></td>";
		echo "<td colspan=3><input name='wdetvim' type='text' value='".$wdetvim."' ".$wvalidacion[$k]." size='40'></td>"; //$wvalimp
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Obligatorio</b></td>";
		echo "<td> <input name='wdetobl' type='checkbox' ".$wdetobl." ".$wvalidacion[$k]."></td>";       //Obligatorio
		$k++;

		echo "<td><b>".$wcar_obl[$k]."Depende de</b></td>";
		echo "<td><input name='wdetdep' type='checkbox' ".$wdetdep." ".$wvalidacion[$k]."></td>";       //Depende
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Número del<br>Campo del<br>que Depende</b></td>";
		echo "<td><input name='wdetcde' type='text' value='".$wdetcde."' ".$wvalidacion[$k]." onblur='enter()' size='2'></td>";  //# Campo que Depende
		$k++;
		echo "</tr>";
		   
        
		//====== 7ma Linea ======
		echo "<tr class=fila1>"; 
		echo "<td><b>".$wcar_obl[$k]."URL (Dirección)</b></td>";
		echo "<td colspan=3><input name='wdeturl' type='text' value='".$wdeturl."' ".$wvalidacion[$k]." size='40'></td>"; //URL
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Campos del<br>Conjunto</b></td>";
		echo "<td><input name='wdetcco' type='text' value='".$wdetcco."' ".$wvalidacion[$k]." size='10'></td>"; //Campos Conjunto
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Caracter del<br>Conjunto</b></td>";
		echo "<td><input name='wdetcac' type='text' value='".$wdetcac."' ".$wvalidacion[$k]." size='2'></td>";  //Caracter Conjunto
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Nivel de<br>Seguridad</b></td>";
		echo "<td><input name='wdetnse' type='text' value='".$wdetnse."' ".$wvalidacion[$k]." size='2'></td>";  //Nivel de Seguridad
		$k++;
        echo "</tr>";
		
		
		//====== 8va Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."Formula</b></td>";
		echo "<td colspan=3> <input name='wdetfor' type='text' value='".$wdetfor."' ".$wvalidacion[$k]." size='40'></td>"; 
		$k++;
        if ($wdetfac=="")
		   $wdetfac=$wfecha;
		echo "<td><b>".$wcar_obl[$k]."Fecha de<br>Activación</b></td>";
		echo "<td><input name='wdetfac' type='text' value='".$wdetfac."' ".$wvalidacion[$k]." size='10'></td>";
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Estado</b></td>";
		echo "<td><input name='wdetest' type='checkbox' ".$wdetest." ".$wvalidacion[$k]."></td>";
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Columnas<br>que ocupa</b></td>";
		echo "<td><input name='wdetcoa' type='text' value='".$wdetcoa."' ".$wvalidacion[$k]." size='2'></td>";
		$k++;
		echo "</tr>";

		
		//====== 9na Linea ======
		echo "<tr class=fila1>";
		//Dato del Campo que depende
		////////////////////////////
		if (isset($wdetcde) and trim($wdetcde)!="")
		   { 
		    $q = " SELECT dettip, detarc, detcav "
		        ."   FROM ".$wbasedato."_000002 "
		        ."  WHERE detpro = '".$wdetpro."'"
		        ."    AND detcon = ".$wdetcde
		        ."    AND detest = 'on' ";
		    $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($res);
			
			$wtip_dat=$row[0];
			$warc_val=$row[1];
			$wcam_val=$row[2];
		   }
		
		if (isset($wtip_dat))
		   {        
		    ////////////////////////
			switch ($wtip_dat)
			   {
				case "Seleccion":
				   {
					//traigo todas opciones de la tabla de seleccion   
					$q = " SELECT selcda, selnda "
					    ."   FROM ".$wbasedato."_000012 "
					    ."  WHERE seltab = '".$wcam_val."'"    //Este campo equivale a la tabla de seleccion, que todas quedan en la 000012
					    ."    AND selest = 'on' "
					    ."  ORDER BY 1 ";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num = mysql_num_rows($res);
					
					echo "<td><b>".$wcar_obl[$k]."Dato del Campo<br>que depende</b></td>";
					echo "<td colspan=3><SELECT name='wdetdde' ".$wvalidacion[$k]." onchange='enter()'>";                                    
					if (isset($wdetdde) and (trim($wdetdde)!=""))   
					   {
					    $wdetdde1 = explode("_",$wdetdde);
					       
					    $q1 = " SELECT selcda, selnda "
					         ."   FROM ".$wbasedato."_000012 "
					         ."  WHERE seltab = '".$wcam_val."'"
					         ."    AND selcda = '".trim($wdetdde1[0])."'"
					         ."  ORDER BY 1 ";
					    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					    $num1 = mysql_num_rows($res1);
					    if ($num1>0)
					      {
					       $row1= mysql_fetch_array($res1);
					       echo "<OPTION SELECTED>".$row1[0]."_".$row1[1]."</option>";
				          } 
					   }
				  
					for ($j=1;$j<=$num;$j++)
					   { 
						$row = mysql_fetch_array($res);   
						echo "<OPTION>".$row[0]."_".$row[1]."</option>";
					   }
				    echo "</SELECT></td>";
				   }
				   break;
				case "Tabla":
				      {
				       if (isset($warc_val))
					      {
						    $warc_val1 = explode("_",$warc_val);  
						    //$wcam_val1 = explode("-",$wcam_val);
						    //$wdetdde1  = explode("-",$wdetdde);
						      
						    //Traigo el nombre del campo del cual se deben desplegar los datos
						    $q = " SELECT descripcion "
						        ."   FROM det_formulario "
						        ."  WHERE medico = '".trim($warc_val1[0])."'"
							    ."    AND codigo = '".trim($warc_val1[1])."'"
							    ."    AND campo  = '".trim($wcam_val)."'"
							    ."    AND activo = 'A' ";
							$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$num = mysql_num_rows($res);
							if ($num>0)
							   {
								$row= mysql_fetch_array($res);  
								 
								$q = " SELECT ".$row[0]                                         //Campo del cual salen los datos
								    ."   FROM ".trim($warc_val1[0])."_".trim($warc_val1[1])     //Conforman el archivo del cual salen los datos
								    ."  ORDER BY 1 ";
								$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
								$num = mysql_num_rows($res);
								
								echo "<td><b>".$wcar_obl[$k]."Dato del Campo<br>que depende</b></td>";
								echo "<td colspan=3><SELECT name='wdetdde' ".$wvalidacion[$k]." onchange='enter()'>";                                    
								if (isset($wdetdde) and (trim($wdetdde)!=""))    
								   {
								    $q1 = " SELECT ".$row[0]
								         ."   FROM ".trim($warc_val1[0])."_".trim($warc_val1[1])
								         ."  WHERE ".$row[0]." = '".$wdetdde."'"
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
						       } 
						  }
					  }
				      break;	
				default:
		           {
		            echo "<td><b>".$wcar_obl[$k]."Dato del Campo<br>que depende</b></td>";
					//On echo "<td> <input name='wdetdde' type='text' value='".$wdetdde."' ".$wvalidacion[$k]."><img src=/matrix/images/medical/TCX/tic.png alt='PAsooooo'></td>";           
					echo "<td colspan=3><input name='wdetdde' type='text' value='".$wdetdde."' ".$wvalidacion[$k]."><img src=/matrix/images/medical/TCX/tic.png alt='PAsooooo'></td>";           
				   }
	               break;      
			   }
			//////////////////////////////////////////   
           }  //fin del if isset($wtip_dat)
          else
            {
	         echo "<td><b>".$wcar_obl[$k]."Dato del Campo<br>que depende</b></td>";
			 echo "<td colspan=3><input name='wdetdde' type='text' value='".$wdetdde."' ".$wvalidacion[$k]." size='40'></td>";   
            }     
		$k++;
        echo "<td><b>".$wcar_obl[$k]."Radio Botón<br>Seleccion</b></td>";
		echo "<td> <input name='wdetprs' type='checkbox' ".$wdetprs." ".$wvalidacion[$k]."></td>";        
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Alto Memo</b></td>";
		echo "<td><input name='wdetalm' type='text' value='".$wdetalm."' ".$wvalidacion[$k]." size='2'></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Ancho Memo</b></td>";
		echo "<td><input name='wdetanm' type='text' value='".$wdetanm."' ".$wvalidacion[$k]." size='2'></td>";            
		$k++;
		echo "</tr>";
		
		
		//====== 10ma Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."Campos de<br>Busquedad(,)</b></td>";
		echo "<td colspan=3> <input name='wdetcbu' type='text' value='".$wdetcbu."' ".$wvalidacion[$k]." size='40'></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Tipo<br>Tabla (S/M)</b></td>";
		echo "<td><input name='wdettta' type='text' value='".$wdettta."' ".$wvalidacion[$k]." size=1></td>";            
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Cantidad Listas<br>Radio Botón</b></td>";
		echo "<td><input name='wdetlrb' type='text' value='".$wdetlrb."' ".$wvalidacion[$k]." size=1></td>";            
		$k++;
		echo "<td><b>Se imprime</b></td>";
		echo "<td><input name='wdetimp' type='checkbox' ".$wdetimp." ".$wvalidacion[$k]."></td>";               
		$k++;
		echo "</tr>";
		
		//====== 11va Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."Nombres<br>Campos de<br>Busqueda(,)</b></td>";
		echo "<td colspan=3> <input name='wdetnbu' type='text' value='".$wdetnbu."' ".$wvalidacion[$k]." size='40'></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Cualificable</b></td>";
		echo "<td><input name='wdetcua' type='checkbox' ".$wdetcua." ".$wvalidacion[$k]." size=1></td>"; 
		
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Caracteres<br>Cualificables</b></td>";
		echo "<td><input name='wdetccu' type='text' value='".$wdetccu."' ".$wvalidacion[$k]." size=6></td>";            
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Cronologico</b></td>";
		echo "<td><input name='wdetcro' type='checkbox' ".$wdetcro." ".$wvalidacion[$k]."></td>";               
		$k++;
		echo "</tr>";
		
		//====== 12va Linea ======
		echo "<tr class=fila1>";
		echo "<td><b>".$wcar_obl[$k]."'Tip'<br>Informativo</b></td>";
		echo "<td colspan=3> <input name='wdettii' type='text' value='".$wdettii."' ".$wvalidacion[$k]." size='40'></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Tabla<br>Desplegable</b></td>";
		echo "<td><input name='wdetdpl' type='checkbox' ".$wdetdpl." ".$wvalidacion[$k]."></td>"; 
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Valor Mínimo</b></td>";
		echo "<td><input name='wdetvmi' type='text' value='".$wdetvmi."' ".$wvalidacion[$k]." size=5></td>";            
		$k++;
		echo "<td><b>".$wcar_obl[$k]."Valor Máximo</b></td>";
		echo "<td><input name='wdetvma' type='text' value='".$wdetvma."' ".$wvalidacion[$k]." size=5></td>";            
		$k++;
		echo "</tr>";
		echo "</table>";
		
		echo "<br>";
		echo "<table>";
		echo "<div align='center'>";   
		echo "<p>";
		echo "<input type='submit' name='Iniciar'   value='Iniciar'>";
		echo "&nbsp&nbsp;|&nbsp"; 
		echo "<input type='submit' name='Grabar'    value='Grabar' ".$wbotgra.">";
		echo "&nbsp;|&nbsp"; 
		echo "<input type='submit' name='Modificar' value='Modificar' ".$wbotmod.">";
		echo "&nbsp;|&nbsp"; 
		echo "<input type='submit' name='Consultar' value='Consultar'>";
		echo "&nbsp;&nbsp;|&nbsp;&nbsp";
		echo "<input type='submit' name='Salir'     value='Salir' onclick='cerrarVentana()'>";
		echo "</p>";
		echo "</div>";
		echo "</table>";
		
	   }
	   break;   
   	case "grilla":
   	   {
	   	global $wvariables;
	   	global $wformulario;
	   	
	   	iniciar();
		
		echo "<input type='HIDDEN' name=wdetpro  value='".$wdetpro."'>";   
	   	if (isset($wformulario)) mostrar_grilla();   
       }	 
       break;
   }
 
 for ($i=0;$i<count($wvariables);$i++)
    {
     echo "<INPUT TYPE='hidden' NAME=wvariables[".$i."] VALUE = '".$wvariables[$i]."'>";
    }  
   
 if (isset($worpaux)) 
    echo "<INPUT TYPE='hidden' NAME=worpaux VALUE = '".$worpaux."'>"; 
 
 if (isset($wtip)) 
    echo "<INPUT TYPE='hidden' NAME=wtip VALUE = '".$wtip."'>"; 
    
 echo "<INPUT TYPE='hidden' NAME=wbotmod VALUE = '".$wbotmod."'>";
 echo "<INPUT TYPE='hidden' NAME=wbotgra VALUE = '".$wbotgra."'>";
 echo "<INPUT TYPE='hidden' NAME=wcampos_select VALUE = '".$wcampos_select."'>";
?>
</form>
</body>
</html>
