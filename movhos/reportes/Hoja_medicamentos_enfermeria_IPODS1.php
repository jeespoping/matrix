<head>
  <title>REPORTE ADMINISTRACION DE MEDICAMENTOS (HOJA DE MEDICAMENTOS)</title>
</head>
<BODY TEXT="#000000">
<script type="text/javascript">


/////////////////////////
document.onkeydown = mykeyhandler;
function mykeyhandler(event)
{
  //keyCode 116 = F5
  //keyCode 122 = F11
  //keyCode 8   = Backspace
  //keyCode 37  = LEFT ROW
  //keyCode 78  = N
  //keyCode 39  = RIGHT ROW
  //keyCode 67  = C
  //keyCode 86  = V
  //keyCode 85  = U
  //keyCode 45  = Insert
  
  //keyCode 18  =  alt
  //keyCode 19  = pause/break
  //keyCode 27  = escape
  //keyCode 32  = space bar
  //keyCode 33  = page up
  //keyCode 34  = page down
  //keyCode 35  = end
  //keyCode 40  = down row
  //keyCode 46  = delete
  //keyCode 91  = left window key
  //keyCode 92  = right window key
  //keyCode 93  = select key
  //keyCode 112 = f1
  //keyCode 113 = f2
  //keyCode 114 = f3
  //keyCode 115 = f4
  //keyCode 116 = f5
  //keyCode 117 = f6
  //keyCode 118 = f7
  //keyCode 119 = f8
  //keyCode 120 = f9
  //keyCode 121 = f10
  //keyCode 122 = F11
  //keyCode 123 = f12
  //keyCode 124 = num lock
  //keyCode 145 = scroll lock
  //keyCode 154 = print screen 
  //         44 = print screen
  
  
event = event || window.event;
if (navigator.appName == "Netscape")
	{
	 var tgt = event.target || event.srcElement;

	 if ((event.ctrlKey && event.which==37) || (event.ctrlKey && event.which==39) ||
		(event.ctrlKey && event.which==78) || (event.ctrlKey && event.which==67) ||
		(event.ctrlKey && event.which==86) || (event.ctrlKey && event.which==85) ||
		(event.ctrlKey && event.which==45) || (event.ctrlKey && event.which==45))
	   {
		event.cancelBubble = true;
		event.returnValue = false;
		alert("Funcion no permitida");
		return false;
	   }

	 if(event.which==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	   {
	    return false;
	   }

	 if (event.which == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	   {
	    return false;
	   }

	 //if ((event.which == 116) || (event.which == 122))
	 if (event.which == 122)
	   {
	    return false;
	   }
	}
else
   {
	var tgt = event.target || event.srcElement;
	if((event.altKey && event.keyCode==37) || (event.altKey && event.keyCode==39) ||
		(event.ctrlKey && event.keyCode==78)|| (event.ctrlKey && event.keyCode==67)||
		(event.ctrlKey && event.keyCode==86)|| (event.ctrlKey && event.keyCode==85)||
		(event.ctrlKey && event.keyCode==45)|| (event.shiftKey && event.keyCode==45))
	   {
		event.cancelBubble = true;
		event.returnValue = false;
		alert("Funcion no permitida");
		return false;
	   }

	if(event.keyCode==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	  {
		return false;
	  }

	if (event.keyCode == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	  {
	   return false;
	  }

	//if ((event.keyCode == 116) || (event.keyCode == 122))
	if (event.keyCode == 122)
	  {
	   if (navigator.appName == "Microsoft Internet Explorer")
	     {
	      window.event.keyCode=0;
	     }
	   return false;
	  }
   }
   
  if ((event.keyCode == 44) || (event.keyCode == 144))
	  {
	   //if (navigator.appName == "Microsoft Internet Explorer")
	   //  {
	      window.event.keyCode=0;
	   ///  }
	   return false;
	  } 
   
  if (event.constructor.DOM_VK_PRINTSCREEN==event.keyCode)
	 {
	  alert("Funci�n no permitida");		 
	  return false;
	 }  
}

function mouseDown(e)
{
var ctrlPressed=0;
var altPressed=0;
var shiftPressed=0;
if (parseInt(navigator.appVersion)>3)
{
if (navigator.appName=="Netscape")
{
var mString =(e.modifiers+32).toString(2).substring(3,6);
shiftPressed=(mString.charAt(0)=="1");
ctrlPressed =(mString.charAt(1)=="1");
altPressed =(mString.charAt(2)=="1");
self.status="modifiers="+e.modifiers+" ("+mString+")"
}
else
{
shiftPressed=event.shiftKey;
altPressed =event.altKey;
ctrlPressed =event.ctrlKey;
}
if (shiftPressed || altPressed || ctrlPressed)
alert ("Funci�n no permitida");
}
return true;
}

if (parseInt(navigator.appVersion)>3)
{
document.onmousedown = mouseDown;
if (navigator.appName=="Netscape")
document.captureEvents(Event.MOUSEDOWN);
}

var message="";

function clickIE()
{
if (document.all)
{
(message);
return false;
}
}

function clickNS(e)
{
if(document.layers||(document.getElementById&&!document.all))
{
if (e.which==2||e.which==3)
{
(message);return false;
}
}
}

if (document.layers)
{
document.captureEvents(Event.MOUSEDOWN);
document.onmousedown=clickNS;
}
else
{
document.onmouseup=clickNS;document.oncontextmenu=clickIE;
}

function imprimir()
  {
   window.print();
  }

function cerrarVentana()
 {
  window.close()		  
 }
 
function enter()
 {
  document.forms.hojamed.submit();
 } 
</script>
<?php
include_once("conex.php");
  /***************************************************
	*	          HOJA DE MEDICAMENTOS               *
	*	              POR HISOTRIA                   *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

 	

 	include_once("root/comun.php");
  					    
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wuser = substr($user,(strpos($user,"-")+1),strlen($user)); 
		  
	$wusuario=$wuser;
    
    $wactualiz="(Octubre 20 de 2011)";      
     
	//==========================================================================================================================
	//Octubre 20 de 2011 
	//==========================================================================================================================
	//Se acondiciona para que este reporte pueda ser visto desde la historia clinica, haciendo el enlace por medio de la cedula
	//y tipo de documento los cuales sirven para ir a buscar la historia y numero de ingreso
	//==========================================================================================================================
	//==========================================================================================================================
	//Septiembre 27 de 2011 
	//==========================================================================================================================
	//Se adiciona la validaci�n del usuario basado en la configuraci�n de HCE para el ROL de cada usuario, es decir, que si el
	//ROL que tiene asignado el usuario en la configuraci�n de la HCE no tiene el permiso para ver la Hoja de Medicamentos del 
	//paciente que esta intentando acceder no puede; este permiso esta dado por la empresa responsable del paciente, la cual 
    //debe estar incluida en las empresas que puede ver el usuario seg�n la relaci�n del Rol-Empresas de la tabla HCE_000019 y
    //HCE_000025.	
	//==========================================================================================================================
	//==========================================================================================================================
	//Agosto 22 de 2011 
	//==========================================================================================================================
	//Se muestra tanto lo aplicado como lo NO aplicado, basado en la tabla movhos_000113 y en la _000015, en donde se registran
	//los motivos de NO aplicaci�n y lo aplicado respectivamente, adem�s se coloca en symbolos estas dos acciones (chulo para lo
	//aplicado y una X para lo no aplicado con justificaci�n.
    //==========================================================================================================================
	//Mayo 31 de 2011 
	//==========================================================================================================================
	//Se modifco el 31 de Mayo para que tome el nombre del articulo de las tablas maestras movhos_000026 y cenpro_000002, porque
    //antes lo venia tomando del nombre que quedo en la tabla de movimiento ('apldes' de la tabla movhos_000015 y cuanod se 
	//cambiaba el nombre de alg�n articulo entonces este reporte salia desplazado en las columnas.
	//Pero igual en el movimiento sigue quedando la descripci�n que tenia el articulo al momento de la aplicaci�n.
	//==========================================================================================================================
	
	
	 
    //=======================================================================================================
	//FUNCIONES
	//=======================================================================================================
    
    function tomar_ronda($wron)
      {
	   $wronda=explode(":",$wron);
	   
	   if ((integer)$wronda[0] < 12)
	      {
		   if (isset($wronda[1]) and strpos($wronda[1],"PM") > 0)
		      {
			   return $wronda[0]+12;   
			  }
			 else 
			    return $wronda[0];     
		  }
		 else
		    if ((integer)$wronda[0]==12)           
		       {
			    if (strpos($wronda[1],"PM") > 0)
			       return $wronda[0];                //Devuelve 12. que equivale a 12:00 PM osea del medio dia
			      else
			         return $wronda[0]-12; 
		       }
		      else       
		         return $wronda[0];
	 }    
        
	  
	 
	function elegir_centro_de_costo()   
     {
	  global $conex;
	  global $wbasedato;
	  global $wtabcco;
	  global $wcco;  
	  
	  
	  global $whora_par_actual;
	  global $whora_par_anterior;
	  
	  
	  echo "<center><table>";
      echo "<tr class=encabezadoTabla><td align=center><font size=7>HOJA DE MEDICAMENTOS</font></td></tr>";
	  echo "</table>";
	  
	  echo "<br><br>";
	  
	  //Seleccionar CENTRO DE COSTOS
	  echo "<center><table>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND ".$wbasedato."_000011.ccohos = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
		
      
	  echo "<tr class=fila1><td align=center><font size=6>Seleccione la Unidad : </font></td></tr>";
	  echo "</table>";
	  echo "<br><br><br>";
	  echo "<center><table>";
	  echo "<tr><td align=center><select name='wcco' size='1' style=' font-size:20px; font-family:Verdana, Arial, Helvetica, sans-serif; height:40px' onchange='enter()'>";
	  echo "<option> </option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
     } 
     
    
    function esdelStock($wart, $wcco)
	    {
		 global $conex;
		 global $wbasedato;  
		    
		 //=======================================================================================================
		 //Busco si el articulo hace parte del stock     Febrero 8 de 2011
		 //=======================================================================================================
		 $q = " SELECT COUNT(*) "
		     ."   FROM ".$wbasedato."_000091 "
		     ."  WHERE Arscco = '".trim($wcco)."' "
		     ."    AND Arscod = '".$wart."'"
		     ."    AND Arsest = 'on' ";
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $row = mysql_fetch_array($res);
		 //=======================================================================================================   
		  
		 if ($row[0] == 0)
		    return false;
		   else
		      return true; 
		}    	  
     
     
    function convertir_a_fraccion($wart,$wcan_apl,&$wuni_fra,$wcco)
      {
	   global $conex;
	   global $wbasedato;
	        
	   $wdos_apl=$wcan_apl;    //Dosis
	   
	   $q = " SELECT deffra, deffru "
	       ."   FROM ".$wbasedato."_000059 "
	       ."  WHERE defcco in ('1050','1051')"
	       ."    AND defart = '".$wart."'"
	       ."    AND defest = 'on' ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
       $num = mysql_num_rows($res);
       if ($num > 0)
          {
           $row = mysql_fetch_array($res);     
	       //$wcan_fra = $row[0];   //Cantidad de fracciones
	       $wuni_fra = $row[1];   //Unidad de la fracci�n
	       
	       //Si es el medicamento es del stock, no se hace la conversion, porque multiplicaria la cantidad aplicada por la fraccion de la 000059
	       //if (!esdelStock($wart, $wcco))     //No es del Stock
	       //   {
	       //    $wdos_apl = $wcan_apl*$wcan_fra;
           //   }
           //  else
                $wdos_apl = $wcan_apl; 
	       
	       return $wdos_apl;
          }
         else
            return $wdos_apl; 
      }    
      
      
    function buscarSiEstaSuspendido($whis, $wing, $wart, $wfecha)
    {
	 global $user;
	 global $conex;
	 global $wbasedato;
	    
     $whorsus="";	   
	   
	 //Busco si esta suspendido  
	 $q = " SELECT COUNT(*)  "
	     ."   FROM ".$wbasedato."_000055 A "
	     ."  WHERE kauhis  = '".$whis."'"
	     ."    AND kauing  = '".$wing."'"
	     ."    AND kaufec  = '".$wfecha."'"
	     ."    AND kaudes  = '".$wart."'"
	     ."    AND kaumen  = 'Articulo suspendido' ";
	 $ressus = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $rowsus = mysql_fetch_array($ressus); 
	 
	 if ($rowsus[0] > 0)
        return true;
	   else
	      return false; //Indica que fue Suspendido hace mas de dos horas
	}       
      
	 
    function buscarJustificacionNoAplicado($wart, $whis, $wing, $wron, $wfec)
     {
      global $conex;
	  global $wbasedato;
	  
	  $q = " SELECT jusjus "
	      ."   FROM ".$wbasedato."_000113 "
		  ."  WHERE jushis = '".$whis."'"
		  ."    AND jusing = '".$wing."'"
		  ."    AND jusfec = '".$wfec."'"
		  ."    AND jusart = '".$wart."'"
		  ."    AND cast(MID(jusron,1,instr(jusron,':')-1) AS SIGNED) = '".$wron."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  
	  if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);
		  return $row[0];
	     }
		else
           return "";		
	 }	 
	  
	
	function buscarUltimaAplicacionDia($Mrondas, $i, $j)
	 {
	  $j++;
	  $wult="on";
	  for ($k=$j; $k<= 23; $k++)
	     {
		  if ($Mrondas[$i][$k] > 0)
		     $wult="off";
		 }
	  return $wult;
	 }
	 
	//Septiembre 27 de 2011 
	function validar_usuario($his, $ing)
	 {
	  global $conex;
	  global $wbasedato;
	  global $wusuario;
	  global $whce;
	  
	  $wfecha=date("Y-m-d");
	  
	  $q = " SELECT ingres "
	      ."   FROM ".$wbasedato."_000016 "
		  ."  WHERE inghis = '".$his."'"
		  ."    AND inging = '".$ing."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);	  
		
      $wempresa_del_paciente=$row[0];		
	  
	  $q = " SELECT COUNT(*) "
	      ."   FROM ".$whce."_000020, ".$whce."_000019, ".$whce."_000025 "
	      ."  WHERE usucod     = '".$wusuario."'"
		  ."    AND usurol     = rolcod "
		  ."    AND usufve    >= '".$wfecha."'"
		  ."    AND usuest     = 'on' "
		  ."    AND (((rolemp  = empcod "
		  ."    AND   INSTR(empemp,'".$wempresa_del_paciente."') > 0 ) "
		  ."     OR rolatr     = 'on') "
		  ."     OR rolemp    in ('%','*') ) "
		  ."    AND rolest     = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
	  $row = mysql_fetch_array($res);

      if ($row[0] > 0)
	     return true;
		else
		   {
		    //Si hace join en este query es porque el usuario es un empleado
		    $q = " SELECT COUNT(*) "
			    ."   FROM usuarios, ".$wbasedato."_000011 "
			    ."  WHERE codigo  = '".$wusuario."'"
			    ."    AND ccostos = ccocod ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
		    $row = mysql_fetch_array($res);

		    if ($row[0] > 0)
			   return true;
              else              
 			     return false;
		   }
	 }
	 
    //=======================================================================================================
    
      
    
    //=======================================================================================================
    //=======================================================================================================
    //CON ESTO TRAIGO LA EMPRESA Y TODAS CAMPOS NECESARIOS DE LA EMPRESA
    $q = " SELECT empdes "
        ."   FROM root_000050 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' ";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row = mysql_fetch_array($res); 
  
    $wnominst=$row[0];
  
    //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
    $q = " SELECT detapl, detval "
        ."   FROM root_000050, root_000051 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' "
        ."    AND empcod = detemp "; 
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res); 
  
    if ($num > 0 )
       {
	    for ($i=1;$i<=$num;$i++)
	       {   
	        $row = mysql_fetch_array($res);
	      
	        if ($row[0] == "cenmez")
	           $wcenmez=$row[1];
	         
	        if ($row[0] == "afinidad")
	           $wafinidad=$row[1];
	         
	        if ($row[0] == "movhos")
	           $wbasedato=$row[1];
			  
			if (strtoupper($row[0]) == "HCE")
	           $whce=$row[1];
	         
	        if ($row[0] == "tabcco")
	           $wtabcco=$row[1];
           }  
      }
     else
        echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";            
    //=======================================================================================================
    //=======================================================================================================            
    
    echo "<form NAME=hojamed action='Hoja_medicamentos_enfermeria_IPODS1.php' method=post>";

    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>"; 
    echo "<input type='HIDDEN' name='whce' value='".$whce."'>";	
    
	if (!isset($wcco))
       elegir_centro_de_costo();
	else 
       {
	    echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>"; 
	      
		//Si ya vienen definidos tanto la cedula como el tipo de documento busco con ellos la hria y el ingreso
		if (isset($wced) and isset($wtid))
		   {
		    $q = " SELECT orihis, oriing "
			    ."   FROM root_000036, root_000037 "
				."  WHERE pacced = '".$wced."'"
				."    AND pactid = '".$wtid."'"
				."    AND pacced = oriced "
				."    AND pactid = oritid "
				."    AND oriori = '".$wemp_pmla."'";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $wnr = mysql_num_rows($res);

            if ($wnr > 0)
               {
			    $row = mysql_fetch_row($res);
				
                $whis = $row[0];
				$wing = $row[1];
               }				
		    }
		
        if (!isset($whis) or !isset($wing))
	       {
		    if ($wcco=="*")
			   $wcco="%";
		   
		    $wcco1=explode("-",$wcco);
		       
		    encabezado("ADMINISTRACION DE MEDICAMENTOS (HOJA DE MEDICAMENTOS)", $wactualiz, 'clinica');   
		       
		    echo "<center><table></center>";
	          
	        $q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data, ubisac "
		        ."   FROM ".$wbasedato."_000018, root_000037, root_000036 "
		        ."  WHERE ubihis  = orihis "
		        ."    AND ubiing  = oriing "
		        ."    AND oriori  = '".$wemp_pmla."'"
		        ."    AND oriced  = pacced "
		        ."    AND ubiald != 'on' "              //Solo los que estan activos
		        ."    AND ubisac  LIKE '".trim($wcco1[0])."'"
		        ."  ORDER BY 1, 4, 5 ";    
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $wnr = mysql_num_rows($res);        
		            
		    echo "<tr class=encabezadoTabla>";
		    echo "<th>Habitacion</th>";
		    echo "<th>Historia</th>";
		    echo "<th>Ingreso</th>";
		    echo "<th colspan=2>Paciente</th>";
		    echo "</tr>";
		           	  
            $wclass = "fila2";					  
		    $whabant = "";
		    for ($i=1;$i<=$wnr;$i++)
			   {
				$row = mysql_fetch_row($res);     
			    $whab    = $row[0];
			    $whis    = $row[1];
			    $wing    = $row[2];
			    $wpac    = $row[5]." ".$row[6]." ".$row[3]." ".$row[4];
			            	            
			    if ($whabant != $whab)
			       {
				    if ($wclass == "fila1")
						$wclass = "fila2";
					  else
						 $wclass = "fila1";
				    echo "<tr class=".$wclass.">";
				    echo "<td align=center>".$whab."</td>";
				    echo "<td align=center>".$whis."</td>";
				    echo "<td align=center>".$wing."</td>";
				    echo "<td align=left  >".$wpac."</td>";
				            
				    echo "<td align=center><A href='Hoja_medicamentos_enfermeria_IPODS1.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wemp_pmla=".$wemp_pmla."'>Imprimir</A></td>";
				    echo "</tr>";
				           
				    $whabant = $whab;
			       }
			   } 
			 
			 echo "</table>";
			 echo "<center><table>";
			 echo "<tr class=seccion1>";
			 echo "<td><b>Ingrese la Historia :</b><INPUT TYPE='text' NAME='whis' SIZE=10></td>";
			 echo "<td><b>Nro de Ingreso :</b><INPUT TYPE='text' NAME='wing' SIZE=10></td>";
			 echo "</tr>";
			 echo "<tr> </tr>";
			 echo "<tr> </tr>";
			 echo "<tr class=boton1><td align=center colspan=6></b><input type='submit' value='ACEPTAR'></b></td></tr></center>";
			 echo "</table>";
			 
			 echo "<center><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS1.php?wemp_pmla=".$wemp_pmla."'> Retornar</A></font></center>";
	       }	       
		else 
		   {
		    if (validar_usuario($whis, $wing))     //Septiembre 27 de 2011 
			   {
			    /********************************* 
				* TODOS LOS PARAMETROS ESTAN SET *
				**********************************/
				$wcco1=explode("-",$wcco);    
				
				/*
				//=======================================================================================================
				//=======================================================================================================
				//Traer Indicador del Kardex Electronico
				$q = " SELECT ccokar "
					."   FROM ".$wbasedato."_000011 "
					."  WHERE ccocod = '".trim($wcco1[0])."'"
					."    AND ccoest = 'on' ";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row = mysql_fetch_array($res);
				$wkar_ele = $row[0];        //Indica que tiene kardex electronico
				//=======================================================================================================
				*/					  
					
				encabezado("REPORTE ADMINISTRACION DE MEDICAMENTOS A PACIENTES", $wactualiz, 'clinica');
				
				$q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data, ubisac, cconom "
					."   FROM ".$wbasedato."_000018, root_000037, root_000036, ".$wtabcco
					."  WHERE ubihis  = '".$whis."'"
					."    AND ubiing  = '".$wing."'"
					."    AND ubihis  = orihis "
					."    AND oriori  = '".$wemp_pmla."'"
					."    AND oriced  = pacced "
					."    AND ubisac  = ccocod "
					."  ORDER BY 1, 4, 5 ";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$wnr = mysql_num_rows($res); 
						
				$row     = mysql_fetch_row($res);     
				$whab    = $row[0];
				$whis    = $row[1];
				$wing    = $row[2];
				$wpac    = $row[5]." ".$row[6]." ".$row[3]." ".$row[4];
				$wser    = $row[9];
				$wnomser = $row[10];   
				
					  
				echo "<br>";
				echo "<input type='button' value='Imprimir' onClick='window.print()'>";	  
					  
				if (!isset($wced) and !isset($wtid))
                   {				
					echo "<center><table>"; 
					echo "<td><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS1.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'> Retornar</A></font></td>";
					echo "</table></center>";
                   }				  
						
				echo "<br>";
				echo "<center><table border=1></center>";		
						
				echo "<tr class=seccion1>";  
				echo "<td colspan=10><b>HISTORIA N� : </b>".$whis." - ".$wing."</td>";
				echo "<td colspan=10><b>SERVICIO : </b>".$wnomser."</td>";
				echo "<td colspan=9 ><b>CAMA : </b>".$whab."</td>"; 
				echo "</tr>";  
				
				echo "<tr class=seccion1>";
				echo "<td colspan=29><b>PACIENTE : </b>".$wpac."</td>";
				echo "</tr>";
			   
				$q = " CREATE TEMPORARY TABLE if not exists TEMPO as "
					." SELECT aplart art, aplfec fec, aplron ron, aplcan can, artcom com, aplufr ufr, apldos dos, artgen gen, 'on' apl, '' jus, ccokar kar, aplcco cco "
					."   FROM ".$wbasedato."_000015, ".$wbasedato."_000026, ".$wbasedato."_000029, ".$wbasedato."_000011 "
					."  WHERE aplhis                            = '".$whis."'"
					."    AND apling                            = '".$wing."'"
					."    AND aplest                            = 'on' "
					."    AND aplart                            = artcod "
					."    AND mid(artgru,1,instr(artgru,'-')-1) = gjugru "
					."    AND gjujus                            = 'on' "
					."    AND aplcco                            = ccocod "
					."  UNION ALL"
					." SELECT aplart art, aplfec fec, aplron ron, aplcan can, artcom com, aplufr ufr, apldos dos, artgen gen, 'on' apl, '' jus, ccokar kar, aplcco cco "
					."   FROM ".$wbasedato."_000015, ".$wcenmez."_000002, ".$wbasedato."_000011 "
					."  WHERE aplhis                            = '".$whis."'"
					."    AND apling                            = '".$wing."'"
					."    AND aplest                            = 'on' "
					."    AND aplart                            = artcod "
					."    AND aplart                            NOT IN (SELECT artcod FROM ".$wbasedato."_000026) "
					."    AND aplcco                            = ccocod "
					
					//Agosto 22 de 2011
					//Aca traigo todos los registros de las rondas que NO se aplicaron
					."  UNION ALL "
					." SELECT jusart art, jusfec fec, jusron ron, 0 can, artcom com, '' ufr, '' dos, artgen gen, 'off' apl, jusjus jus, '' kar, '' cco "
					."   FROM ".$wbasedato."_000113, ".$wbasedato."_000026, ".$wbasedato."_000029 "
					."  WHERE jushis                            = '".$whis."'"
					."    AND jusing                            = '".$wing."'"
					."    AND jusart                            = artcod "
					."    AND mid(artgru,1,instr(artgru,'-')-1) = gjugru "
					."    AND gjujus                            = 'on' "
					."  UNION ALL"
					." SELECT jusart art, jusfec fec, jusron ron, 0 can, artcom com, '' ufr, '' dos, artgen gen, 'off' apl, jusjus jus, '' kar, '' cco "
					."   FROM ".$wbasedato."_000113, ".$wcenmez."_000002 "
					."  WHERE jushis                            = '".$whis."'"
					."    AND jusing                            = '".$wing."'"
					."    AND jusart                            = artcod "
					."    AND jusart                            NOT IN (SELECT artcod FROM ".$wbasedato."_000026) "
					."  ORDER BY 2 desc, 1, 3 ";
				$res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());;
				
				$q = " SELECT art, fec, ron, SUM(can), com, ufr, dos, gen, apl, jus, kar, cco "
					."   FROM TEMPO "
					."  GROUP BY 1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 12 "
					."  ORDER BY 2 desc, 1, 3 ";
				$res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());;
				$wnr = mysql_num_rows($res3); 
				
				//Inicializo la MATRIZ a donde voy a llevar todo lo que le aplicaron al paciente en la estadia
				for ($j=0;$j<=$wnr;$j++)
				   {
					for ($l=0;$l<=23;$l++)     //24 horas que tiene el dia
					   {
						$Mrondas[$j][$l]=0;    //Aca almaceno las cantidades de cada articulo segun la ronda
					   } 
					$Afechas[$j]   =0;         //Aca llevo las fecha de aplicacion   
					$Aarticulos[$j]=0;         //Aca llevo el codigo del articulo de acuerdo a la hora de la ronda
					$Adesc[$j]     ="";        //Aca llevo la descripcion del articulo de acuerdo a la hora de la ronda
				   }		 
						
				$j=1;
				$i=1;
				$row = mysql_fetch_row($res3);
				
				while ($j <= $wnr)
				   {
					$wfec  = $row[1];
					$wart  = $row[0];
					$wdesc = $row[4];
					
					
					$Afechas[$i]    = $row[1];
					$Aarticulos[$i] = $row[0];
					$Adesc[$i]      = $row[4];    //Nombre Comercial
					$Agen           = $row[7];    //Nombre Generico
					$Akar           = $row[10];   //Es electronico
					$Acco           = $row[11];   //CCo donde se aplico
				  
				  	while ($wart==$row[0] and $wfec==$row[1] and $wdesc==$row[4])
						{
						 $wronda=(integer)tomar_ronda($row[2]);  
						 
						 /*  Nov 3 de 2011
						 if ((isset($wfrac) and $wfrac==0) or $Akar != "on")      //Si tiene Kardex Electronico (directo) en centro de costos _000011
							{
							 $Mrondas[$i][$wronda]=$Mrondas[$i][$wronda]+$row[3];
							 if ($Akar == "on")                                   //Si tiene Kardex Electronico (directo) en centro de costos _000011
								$Mrondas[$i]["fraccion"]="Sin Fracci�n";          
							   else
								  $Mrondas[$i]["fraccion"]=""; 
							}
						   else //tiene fraccion   */ 
							  $Mrondas[$i][$wronda]=$Mrondas[$i][$wronda]+$row[6];    //Cantidad (este dato es la dosis que se coloco en el kardex)
							  $Mrondas[$i]["fraccion".$wronda]=$row[5];               //Fraccion que hay en la 000015 basado en la dosis del kardex
							  $Mrondas[$i]["apl".$wronda]=$row[8];                    //Indica que fue aplicado o NO             Agosto 22 de 2011      
							  $wjus=explode("-",$row[9]);                             //                                         Agosto 22 de 2011
							  
						 if (isset($wjus[1]) and $wjus[1] != "")                 //                                         Agosto 22 de 2011
							$Mrondas[$i]["jus".$wronda]=$wjus[1];                //Grabo la justificaci�n pero sin c�digo   Agosto 22 de 2011
						   else                                                  //                                         Agosto 22 de 2011
							  $Mrondas[$i]["jus".$wronda]=$row[9];               //Justificacion                            Agosto 22 de 2011
							  
						 $row = mysql_fetch_row($res3);   
						 $j++;
						} 
					$i++;
				   }      
					
				$t=$i;
				
				echo "<tr class=encabezadoTabla>";    
				echo "<th>FECHA</th>";
				echo "<th>CODIGO</th>";
				echo "<th>MEDICAMENTO</th>";
				//echo "<th>Unidad</th>";
				$i=0;
				while ($i <= 23)
					 {
					  if ($i < 12)      
						 echo "<th>".$i." AM"."</th>"; 
						else 
						   echo "<th>".$i." PM"."</th>";
					  $i=$i+1;
					 }         
				echo "<th>TOTAL</th>";
				echo "</tr>";         
				
				$wfec="";
				$i=1;
				$k=1;
				
				$cont_serv=0;
				while ($i < $t)   //Recorro la matriz con cada articulo
					 {
					  $wuni=".";  
					  $wnomart=$Adesc[$i]; 
					  $wnomgen=$Agen[$i]; 
					  if ($Aarticulos[$i] != "999")
						 {  
						  //$q =  " SELECT artcom, artuni, artgen "
						  $q =  " SELECT artcom, deffru, artgen "
							   ."   FROM ".$wbasedato."_000026, ".$wbasedato."_000059 "
							   ."  WHERE artcod = '".$Aarticulos[$i]."'"
							   ."    AND artcod = defart ";
						  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						  $wfilas = mysql_num_rows($res);
						  if ($wfilas==0)                                 //Si no existe en movhos lo busco en central de mezclas
							 {
							  //$q =  " SELECT artcom, artuni, artgen "
							  $q =  " SELECT artcom, deffru, artgen "
								   ."   FROM ".$wcenmez."_000002, ".$wbasedato."_000059 "
								   ."  WHERE artcod = '".$Aarticulos[$i]."'"
								   ."    AND artcod = defart ";
							  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
							  $wfilas = mysql_num_rows($res);  //Nov 3 2011
							  
							  if ($wfilas==0)   //Nov 3 2011                              //Si no existe en movhos lo busco en central de mezclas
								 {
								  $q =  " SELECT artcom, artuni, artgen "
								       ."   FROM ".$wcenmez."_000002 "
									   ."  WHERE artcod = '".$Aarticulos[$i]."'";
								  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
							      $wfilas = mysql_num_rows($res);
								  
								  if ($wfilas==0)                                 //Si no existe en movhos lo busco en central de mezclas
									 {
									  $q =  " SELECT artcom, artuni, artgen "
										   ."   FROM ".$wbasedato."_000026 "
										   ."  WHERE artcod = '".$Aarticulos[$i]."'";
									  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
									 } //Nov 3 2011
								 }     //Nov 3 2011
							 }       
						  $row = mysql_fetch_array($res);
						  
						  $wnomart = $row[0];
						  $wuni    = $row[1];
						  $wgen    = $row[2];
						 } 
					  
					  //Traigo la cantidad de articulos(distintos) por cada FECHA
					  $q = " SELECT COUNT(DISTINCT(art)) "
						  ."   FROM TEMPO "
						  ."  WHERE fec  = '".$Afechas[$i]."'";
					  $res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					  $wfilas = mysql_fetch_array($res3);   
					  
					  if (is_integer(($i+$cont_serv)/2))
						 $wclass = "fila1";
						else  
						   $wclass = "fila2";  
					  
					  echo "<tr class=".$wclass.">";
					  if ($wfec != $Afechas[$i])
						 {
						  echo "<tr><td bgcolor=DDDDDD colspan=29> </td></tr>";
						  echo "<tr class=".$wclass.">";
						  echo "<td rowspan=".$wfilas[0]." align=center><font size=2><b>".$Afechas[$i]."</b></td>";
						  $wfec = $Afechas[$i];
						 }
						 
					  $wsuspendido=false;   
					  $wsuspendido=buscarSiEstaSuspendido($whis, $wing, $Aarticulos[$i], $Afechas[$i]);    //, &$whorsus);
						 
					  echo "<td>".$Aarticulos[$i]."</td>";                                  //Codigo Articulo
					  echo "<td>Com.: ".$wnomart."<br>Gen.: ".$wgen."</td>";                //Nombre Comercial y Generico
					  //echo "<td align=center>".$wuni."</td>";                               //Unidad de Medida
					  $j=0;
					  $wtotal=0;
					  while ($j <= 23)
						 {
						  if ($j >= 12)
							 if ($j == 12)
								$wmsg=$j." PM";
							   else
								  $wmsg=($j-12)." PM";
							else
							   $wmsg=$j." AM";
									 
						  //Agosto 22 de 2011                                             - page_cross.gif   msgbox04.ico
						  if ($Mrondas[$i][$j] == 0)            
							 {
							  if (isset($Mrondas[$i]["jus".$j]) and  $Mrondas[$i]["jus".$j] != '')	 
								 echo "<td align=center title='".$wmsg.", No se aplic�: ".$Mrondas[$i]["jus".$j]."'><img src=/matrix/images/medical/movhos/info.png></td>";
								else 
								   echo "<td align=center>.</td>"; 
							 } 
							else  
							   {
								$wultapl=buscarUltimaAplicacionDia($Mrondas, $i, $j);
								if ($wsuspendido and $wultapl=="on")
								   echo "<td align=center bgcolor='FEAAA4' title='".$wmsg.", Luego de esta aplicaci�n fue Suspendido'><img src=/matrix/images/medical/movhos/checkmrk.ico alt='".$wmsg.", Luego de esta aplicaci�n fue Suspendido'> ".$Mrondas[$i][$j]."<br>".$Mrondas[$i]["fraccion".$j]."<br>"."</td>";
								  else
									 echo "<td align=center title='".$wmsg."'><img src=/matrix/images/medical/movhos/checkmrk.ico> ".$Mrondas[$i][$j]."<br>".$Mrondas[$i]["fraccion".$j]."<br>"."</td>"; 
								$wtotal = $wtotal + $Mrondas[$i][$j];
								$wultfra= $Mrondas[$i]["fraccion".$j];   //Ultima Unidad de Medida
							   }
						  $j++;
						 }
					  //echo "<td align=right><b>".$wtotal." ".$Mrondas[$i]["fraccion".($j-1)]."</b></td>";
					  echo "<td align=right><b>".$wtotal." ".$wultfra."</b></td>";
					  echo "</tr>";
					  $i++;   
					 }
				echo "</table>";
				
				if (!isset($wced) and !isset($wtid))
                   {				
					echo "<center><table>"; 
					echo "<tr><td><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS1.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'> Retornar</A></font></td></tr>";
					echo "</table></center>";
                   }
				
				echo "<br>";
				echo "<input type='button' value='Imprimir' onClick='window.print()'>";		
			   }
			  else   //Septiembre 27 de 2011 
                 {
                  ?>	    
				   <script>
				     alert("No tiene PERMISO para acceder a esta H�storia");
				   </script>
				  <?php
				  
				  unset($whis);
			      unset($wing);
				  echo "<meta http-equiv='refresh' content='0;url=Hoja_medicamentos_enfermeria_IPODS1.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'>";
                 } 
		   }
		}
	   	
	   if (!isset($wced) and !isset($wtid))
          {
		   echo "<br>";
		   echo "<center><table>"; 
		   echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		   echo "</table></center>";
		  }
	   
}
include_once("free.php");
?>
