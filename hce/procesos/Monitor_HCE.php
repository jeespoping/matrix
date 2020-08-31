<html>
<head>
  <title>MONITOR HCE</title>
</head>

<script type="text/javascript">
	function enter()
	{
	 document.forms.monhce.submit();
	}
	
	function cerrarVentana()
	 {
      window.close();		  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *              MONITOR DE LA HCE              *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
  include_once("root/magenta.php");
  include_once("root/comun.php");
  
  $conex = obtenerConexionBD("matrix");
  
                                                   // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Abril 7 de 2011)";                  // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                               // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION  Junio 30 de 2009                                                                                                            \\
//=========================================================================================================================================\\
//Este programa muestra la situación en linea de todo lo que ocurre con la HISTORIA CLINICA ELECTRONICA                                    \\
//                                                                                                                                         \\
//=========================================================================================================================================\\	                                                         
	                                                           
	                                                             
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//Enero 7 de 2011                                                                                                                          \\
//=========================================================================================================================================\\
//                     \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//Agosto 5 de 2010                                                                                                                         \\
//=========================================================================================================================================\\
//                     \\
//                     \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//Junio 17 de 2010                                                                                                                         \\
//=========================================================================================================================================\\
//                     \\
//=========================================================================================================================================\\
//Mayo 27 de 2010                                                                                                                          \\
//=========================================================================================================================================\\
//                     \\
//                                                                                                                                         \\
//=========================================================================================================================================\\

	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
  $q = " SELECT detapl, detval, empdes "
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
	         
	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
	         
	      if ($row[0] == "camilleros")
	         $wcencam=$row[1]; 

          if ($row[0] == "hce")
	         $whce=$row[1];   			 
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  //========================================================================================================================================================
  //*** F U N C I O N E S 
  //========================================================================================================================================================
  function estado_HCE($whis, $wing, &$wfirmada, &$wformulario, &$wusuhce)
    {
	  global $wbasedato;
	  global $conex;
	  global $wfecha;
	  global $wopcion;
	  global $whce;
	  
	  switch ($wopcion)   
        {
	     case ("1"):
	         {
              //========================================================================================================================================================================
			  //Formularios SIN Firmar
			  //========================================================================================================================================================================
			  //Aca muestra todas las historias que tienen formularios sin firmar
			  
			  //Traigo todos los formualrios que se firman
			  $q = " SELECT encpro, encdes "
			      ."   FROM ".$whce."_000001 "
				  ."  WHERE encfir = 'on' "
				  ."  GROUP BY 1 ";
			  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $wnum = mysql_num_rows($res1);
			  
			  
			  
			  //Por cada formulario que firma busco si tiene registros
			  for ($i=1;$i<=$wnum;$i++)
			     {
				  $row = mysql_fetch_array($res1);
				  
				  //On
				  $hora = (string)date("H:i:s");
				  echo "Empezo 1er Query Tiempo 1 : ".$hora."<br>";
				  
				  //Busco para formulario si la historia tiene registros, si si, entonces luego busco si esta firmado o no
				  $q = " SELECT movusu, COUNT(*) "
				      ."   FROM ".$whce."_".$row[0]
					  ."  WHERE movhis = '".$whis."'"
					  ."    AND moving = '".$wing."'"
					  ."  GROUP BY 1 ";
				  $resusu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $wnumusu = mysql_num_rows($res1);
				  
				  //On
				  $hora = (string)date("H:i:s");
				  echo "Termino 1er Query Tiempo 2 : ".$hora."<br>";
				  
				  //POr cada usuario que tenga el formulario para la historia e ingreso
				  for ($j=1;$j<=$wnumusu; $j++)
				     {
					  $rowusu = mysql_fetch_array($resusu);	  
				
					  if ($rowusu[1] > 0)    //Si entra es porque tiene registros para el formulario el usuario= row[0]
						 {
						  //On
				  $hora = (string)date("H:i:s");
				  echo "Empezo 2do Query Tiempo 1 : ".$hora."<br>";
						 
						  //Busco si esta firmado el formulario
						  $q = " SELECT COUNT(*) "
							  ."   FROM ".$whce."_".$row[0]
							  ."  WHERE movhis = '".$whis."'"
							  ."    AND moving = '".$wing."'"
							  ."    AND movusu = '".$rowusu[0]."'"     //Usuario
							  ."    AND movtip = 'Firma' ";
						  $resfir = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						  $rowfir = mysql_fetch_array($resfir);	

				  //On
				  $hora = (string)date("H:i:s");
				  echo "Termino 2do Query Tiempo 2 : ".$hora."<br>";
						 
						  $wfirmada[$whis."-".$wing]   = false;
						  $wformulario[$whis."-".$wing]= $row[1];    //Formulario
						  $wusuhce[$whis."-".$wing]   = $rowusu[0];
						  if ($rowfir[0] > 0)
							 $wfirmada[$whis."-".$wing]=true;
						 }
					 }
				 }
			  break;   
	         }
		}//Aca termina el switch	         
	}
  	
	
  //=====================================================================================================================================================================	
  //**************************** Aca termina la funcion estado_hce *******************
  
  
  
  
  //Dependiendo de la opcion enviada en los parametros del programa, definido en la tabla root_000021, se coloca el TITULO en la pantalla
  switch ($wopcion)
    {
	 case "0":
	    $wtitulo = "MONITOR DE LA *** HCE ***";
	    break;
	 case "1":
	    $wtitulo = "Formularios SIN Firmar ";
	    break;    
	 case "2":
	    $wtitulo = "Historias SIN Egresar";
	    break;
	 case "3":
	    $wtitulo = "Pacientes SIN Registros las últimas 24 Horas";
	    break;
	 case "4":
	    $wtitulo = "Pacientes SIN Medico asignado";
	    break;
	}    
  
  if ($wopcion=="0")
     encabezado($wtitulo, $wactualiz, 'clinica');
    else
       { 
	    //echo "<center><table>";
	    //echo "<tr><td align=center colspan=6 bgcolor=C3D9FF><font size=4><b>FORMULARIOS SIN FIRMAR<br></b></font></td></tr>";
	    //echo "</table></center>";
	    //echo "<br>";
		
		echo "<div id='fixeddiv' style='position:absolute;z-index:99;width:920px;height:5px;left:370px;right:300px;top:1px;font-size: 14pt;text-align: center;padding:5px;background:#FFFFCC;border:0px solid #FFD700'>";
        echo "<b>FORMULARIOS SIN FIRMAR</b>";
        echo "</div>";
		
		echo "<br><br>";
	  }
  
  if ($wopcion != "0")
     {   
	  //FORMA ================================================================
	  echo "<form name='monhce' action='Monitor_HCE.php' method=post>";
    
	  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	  echo "<input type='HIDDEN' name='whce' value='".$whce."'>";
	  
	  echo "<center><table>";
	  
	  if (strpos($user,"-") > 0)
	     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

	  $usuario = consultarUsuario($conex,$wusuario);   
	        
	  //===============================================================================================================================================
	  //ACA COMIENZA EL MAIN DEL PROGRAMA   
	  //===============================================================================================================================================
	  switch ($wopcion)
	    {
		 case "9":    //CTC's
		    {
			  //Aca trae todos los pacientes que esten hospitalizados en la clinica y luego busco como es el estado en cuanto a Kardex de cada uno
			  
			  $dia = time()-(1*24*60*60);   //Te resta un dia
	          $wayer = date('Y-m-d', $dia); //Formatea dia 
			  
			  break;
		 	}
		 default:
		    {
			 //$w30dias = time()-(30*24*60*60);   //Te resta un dia, (2*24*60*60) te resta dos y //asi...
			 $w30dias = time()-(15*24*60*60);   //Te resta un dia, (2*24*60*60) te resta dos y //asi...
			 $whace30dias = date('Y-m-d', $w30dias); //Formatea dia 
			 $wfecha=date('Y-m-d');
			
             //Aca trae todos los pacientes que esten en la clinica y luego busco como es el estado en cuanto a HCE de cada uno
			 $q = " SELECT cconom, ubihac, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pacced, pactid, ubiald, firpro, firusu, encdes, A.fecha_data "
			     ."   FROM ".$wbasedato."_000018, root_000036, root_000037, ".$wbasedato."_000011, ".$whce."_000036 A, ".$whce."_000001 " 
			     ."  WHERE firhis  = ubihis "
				 ."    AND firing  = ubiing "
				 ."    AND firfir != 'on' "    //Que no este firmado
				 ."    AND ubihis  = orihis "
				 ."    AND ubiing  = oriing "
				 ."    AND oriori  = '".$wemp_pmla."'"
				 ."    AND oriced  = pacced "
				 ."    AND oritid  = pactid "
				 ."    AND ubisac  = ccocod "
				 ."    AND firpro  = encpro "
			     ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15 "
				 ."  ORDER BY 1,2,3 ";
			 break;
		 	}	
	 	}	  
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	           	
	  if ($num > 0)
	     {
		  if ($wopcion == "9")
		     {
			  ///ctc($res);         // <==== /// * * * C T C ' s * * * \\\
		     }
		    else
		     {
			  echo "<tr><td align=left bgcolor=#fffffff colspan=3><font size=2 text color=#CC0000><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#fffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Registros: ".$num."</b></font></td></tr>";
			 
			  echo "<tr class='encabezadoTabla'>";
			  echo "<th>Servicio</th>";
			  echo "<th>Habitacion</th>";
			  echo "<th>Historía</th>";
			  echo "<th>Paciente</th>";
			  echo "<th>Fecha</th>";
			  echo "<th>Formulario</th>";
			  echo "<th>Profesional que grabó</th>";
			  echo "<th>Rol</th>";
			  echo "</tr>";
			 
			  //Por cada paciente
		      for($i=1;$i<=$num;$i++)
				 {
				  $row = mysql_fetch_array($res);  	  
					 
                  $wsac    = $row[0];	                                     //Servicio Actual				 
				  $whab    = $row[1];                                        //Habitación actual
				  $whis    = $row[2];   
				  $wing    = $row[3];  
				  $wpac    = $row[4]." ".$row[5]." ".$row[6]." ".$row[7];    //Nombre
				  $wdpa    = $row[8];                                        //Documento del Paciente
			      $wtid    = $row[9];                                        //Tipo de Documento o Identificacion
				  $wald    = $row[10];                                       //Indica si tiene alta definitiva
				  $wcodfor = $row[11];                                       //Codigo Formulario sin firmar           
				  $wusu    = $row[12];                                       //Usuario
				  $wnomfor = $row[13];                                       //Nombre Formulario sin firmar
				  $wfec    = $row[14];                                       //Fecha en que se grabo el formulario
			      
			      //estado_HCE($whis, $wing, &$wfirmada, &$wformulario, &$wusuhce);     
			      
				  if (isset($wclass) and $wclass=="fila1")
					  $wclass="fila2";
					 else
						$wclass="fila1";						 
					
				  //Traigo el nombre del USUARIO y su ROL en la HCE
				  $q = " SELECT descripcion, roldes "
					  ."   FROM usuarios, ".$whce."_000020, ".$whce."_000019 "
					  ."  WHERE codigo = '".$wusu."'"
					  ."    AND codigo = usucod "
					  ."    AND usurol = rolcod ";
				  $resusu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $numusu = mysql_num_rows($resusu);  

				  if ($numusu > 0)
					 {
					  $rowusu = mysql_fetch_array($resusu);
					 
					  $wusu = $rowusu[0];
					  $wrol = $rowusu[1];
					 }
					else
					   {
						//$wusu = $wusu;
						$wrol = "No esta registrado su ROL";
					   }						   
				  
				  echo "<tr class=".$wclass.">";
				  echo "<td align=center>".$wsac."</td>";
				  echo "<td align=center>".$whab."</td>";
				  echo "<td align=center>".$whis."-".$wing."</td>";
				  echo "<td align=left  >".$wpac."</td>";
				  echo "<td align=left  >".$wfec."</td>";
				  echo "<td align=left  >".$wnomfor."</td>";
				  echo "<td align=left  >".$wusu."</td>";
				  echo "<td align=left  >".$wrol."</td>";
				  echo "</tr>";
                 }
			 }
		 }
		else
		   echo "NO HAY HABITACIONES OCUPADAS"; 
	  echo "</table>";
     
	  echo "<meta http-equiv='refresh' content='90;url=Monitor_HCE.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wopcion=".$wopcion."'>";
	                 
	  echo "</form>";
	  
	  echo "<br>";
	  echo "<table>"; 
	  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	  echo "</table>";
     }
} // if de register

include_once("free.php");
?>

<!-- Este procedimiento hace que se coloque un DIV fijo en los browser que no soportan FIXED en el style-->
<script>
if(document.getElementById("fixeddiv")) { fixedMenuId = "fixeddiv"; var fixedMenu = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId) : document.all ? document.all[fixedMenuId] : document.layers[fixedMenuId]}; fixedMenu.computeShifts = function() { fixedMenu.shiftX = fixedMenu.hasInner ? pageXOffset : fixedMenu.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu.shiftX += fixedMenu.targetLeft > 0 ? fixedMenu.targetLeft : (fixedMenu.hasElement ? document.documentElement.clientWidth : fixedMenu.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu.targetRight - fixedMenu.menu.offsetWidth; fixedMenu.shiftY = fixedMenu.hasInner ? pageYOffset : fixedMenu.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu.shiftY += fixedMenu.targetTop > 0 ? fixedMenu.targetTop : (fixedMenu.hasElement ? document.documentElement.clientHeight : fixedMenu.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu.targetBottom - fixedMenu.menu.offsetHeight }; fixedMenu.moveMenu = function() { fixedMenu.computeShifts(); if(fixedMenu.currentX != fixedMenu.shiftX || fixedMenu.currentY != fixedMenu.shiftY) { fixedMenu.currentX = fixedMenu.shiftX; fixedMenu.currentY = fixedMenu.shiftY; if(document.layers) { fixedMenu.menu.left = fixedMenu.currentX; fixedMenu.menu.top = fixedMenu.currentY }else { fixedMenu.menu.style.left = fixedMenu.currentX + "px"; fixedMenu.menu.style.top = fixedMenu.currentY + "px" } }fixedMenu.menu.style.right = ""; fixedMenu.menu.style.bottom = "" }; fixedMenu.floatMenu = function() { fixedMenu.moveMenu(); setTimeout("fixedMenu.floatMenu()", 20) }; fixedMenu.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu.init = function() { if(fixedMenu.supportsFixed())fixedMenu.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu.menu : fixedMenu.menu.style; fixedMenu.targetLeft = parseInt(a.left); fixedMenu.targetTop = parseInt(a.top); fixedMenu.targetRight = parseInt(a.right); fixedMenu.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu.addEvent(window, "onscroll", fixedMenu.moveMenu); fixedMenu.floatMenu() } }; fixedMenu.addEvent(window, "onload", fixedMenu.init); fixedMenu.hide = function() { fixedMenu.menu.style.display = "none"; return false }; fixedMenu.show = function() { fixedMenu.menu.style.display = "block"; return false } }if(document.getElementById("fixeddiv2")) { fixedMenuId2 = "fixeddiv2"; var fixedMenu2 = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId2) : document.all ? document.all[fixedMenuId2] : document.layers[fixedMenuId2]}; fixedMenu2.computeShifts = function() { fixedMenu2.shiftX = fixedMenu2.hasInner ? pageXOffset : fixedMenu2.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu2.shiftX += fixedMenu2.targetLeft > 0 ? fixedMenu2.targetLeft : (fixedMenu2.hasElement ? document.documentElement.clientWidth : fixedMenu2.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu2.targetRight - fixedMenu2.menu.offsetWidth; fixedMenu2.shiftY = fixedMenu2.hasInner ? pageYOffset : fixedMenu2.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu2.shiftY += fixedMenu2.targetTop > 0 ? fixedMenu2.targetTop : (fixedMenu2.hasElement ? document.documentElement.clientHeight : fixedMenu2.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu2.targetBottom - fixedMenu2.menu.offsetHeight }; fixedMenu2.moveMenu = function() { fixedMenu2.computeShifts(); if(fixedMenu2.currentX != fixedMenu2.shiftX || fixedMenu2.currentY != fixedMenu2.shiftY) { fixedMenu2.currentX = fixedMenu2.shiftX; fixedMenu2.currentY = fixedMenu2.shiftY; if(document.layers) { fixedMenu2.menu.left = fixedMenu2.currentX; fixedMenu2.menu.top = fixedMenu2.currentY }else { fixedMenu2.menu.style.left = fixedMenu2.currentX + "px"; fixedMenu2.menu.style.top = fixedMenu2.currentY + "px" } }fixedMenu2.menu.style.right = ""; fixedMenu2.menu.style.bottom = "" }; fixedMenu2.floatMenu = function() { fixedMenu2.moveMenu(); setTimeout("fixedMenu2.floatMenu()", 20) }; fixedMenu2.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu2.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu2.init = function() { if(fixedMenu2.supportsFixed())fixedMenu2.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu2.menu : fixedMenu2.menu.style; fixedMenu2.targetLeft = parseInt(a.left); fixedMenu2.targetTop = parseInt(a.top); fixedMenu2.targetRight = parseInt(a.right); fixedMenu2.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu2.addEvent(window, "onscroll", fixedMenu2.moveMenu); fixedMenu2.floatMenu() } }; fixedMenu2.addEvent(window, "onload", fixedMenu2.init); fixedMenu2.hide = function() { if(fixedMenu2.menu.style.display != "none")fixedMenu2.menu.style.display = "none"; return false }; fixedMenu2.show = function(a) { document.getElementById("wtipoprot"); var b = 0; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)document.forms.forma.wtipoprot[b].disabled = true; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)if(a.indexOf(document.forms.forma.wtipoprot[b].value) != -1) { document.forms.forma.wtipoprot[b].checked = true; document.forms.forma.wtipoprot[b].disabled = false }fixedMenu2.menu.style.display = "block"; return false } };
</script>
