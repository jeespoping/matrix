<head>
  <title>REGISTRO DE DIETAS</title>
  
  <!--JQUERY-->
	<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

	<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script>
	<script type="text/javascript" src="../../../include/root/ui.core.js"></script>
	<script type="text/javascript" src="../../../include/root/ui.tabs.js"></script>
	<script type="text/javascript" src="../../../include/root/ui.draggable.js"></script>
	<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>

	<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
  <!--Fin JQUERY-->
</head>

<script type="text/javascript">

    function terminoGrabar(){
	$.unblockUI();
	}

	//Mirar que esto probablemente sobre
	function grabar(){
		$.blockUI({ message: $('#msjEspere') });	
		setTimeout(terminoGrabar,5000);
	}
      
	function enter()
	{
	 document.forms.dietas.submit();
	}
	
	function cerrarModal()
	 {
	  $.unblockUI();
     }

     
    function respuestaUnblock(idElemento, arreglo)
    {
//	    debugger;
		var temporal="";
		var temporal2="";
		var mensaje="";
		var valores = "";  //Acumulo los values de los checkboxes

		var acumulador = "";
		
		var setter = new Array();
		var cont1 = 0;
				
		var elemento = document.getElementById("sel"+idElemento);		
		elemento.value = "";	
		
		while(document.getElementById("chk"+idElemento+cont1.toString())){
			temporal2 = document.getElementById("chk"+idElemento+cont1.toString()).checked;
			temporal = arreglo[cont1];
			
			mensaje += temporal2 ? " on" : " off";

			document.getElementById("chk"+idElemento+cont1.toString()).checked = temporal;

			acumulador += temporal ? "on," : "off,";
			
			if(temporal)
			   valores+=document.getElementById("chk"+idElemento+cont1.toString()).value+",";
			   
			mensaje += temporal ? " on" : " off";
			mensaje += "<br>";
			
			cont1++;
		}	
		
		arregloTemporal = arreglo;
		document.getElementById(idElemento).value = acumulador.substring(0,acumulador.length-1);	
		elemento.value = valores;
		
		mensaje+=elemento.value+"<br>";		
		
		//On $.growlUI('Riposta de la monda', mensaje);
	} 
     
     
	//IdElmento es el id del div, indice de cada check
	function activarModal(idElemento,indice)
	  {
//		debugger;
		
		var cont1 = 0;
		var temporal2 = "", mensaje = "";
		var valores = new Array();
		var elemento = document.getElementById("sel"+indice);
		elemento.value="";
		
		$.blockUI({ 
				message: $('#'+idElemento), 
				css: { height:'800px', width:'1200px', overflow:'scroll', top:'5%', left:'2%'}
				});

		var acumulador = document.getElementById(indice).value;
		var valores = acumulador.split(",");
		var resultado = "";
		var x = "chk"+idElemento+cont1.toString();				
		
		//alert (x);
		
		while(document.getElementById("chk"+indice+cont1.toString()))
		{
			temporal2 = document.getElementById("chk"+indice+cont1.toString()).checked;
			temporal = valores[cont1] == "on" ? true : false;
				
			mensaje += temporal2 ? " on" : " off";
		
			//document.getElementById("chk"+indice+cont1.toString()).checked = temporal2; 
			
			if(valores.length > 1) {
				document.getElementById("chk"+indice+cont1.toString()).checked = temporal;  
			} else {
				document.getElementById("chk"+indice+cont1.toString()).checked = temporal2; 
			}
					
		  
			if(temporal2)
			   resultado+=document.getElementById("chk"+indice+cont1.toString()).value+",";
			   
			mensaje += temporal ? " on" : " off";	
			mensaje += "<br>";
				
			cont1++;
		}		
		//alert("activar modal: " + document.getElementById("sel"+indice).value);
		//elemento.value.substring(0,elemento.value.length-1);
		elemento.value = resultado;
	  }
	
	
	function cerrarVentana()
	 {
      window.close()		  
     }

         
	function chequeoTodos(id)
	   {
		var cont1  = 1;
		var estado = document.getElementById("chk"+id).checked;

		while(document.getElementById("patron"+cont1.toString()+"-"+id))
		{	
			document.getElementById("patron"+cont1.toString()+"-"+id).checked = estado;
			cont1++;
		}
	}
	
	
	function combina(f, c, cod_dieta, adi_ser)
	   {
		var cont1    = 1;
		var unico    = false;
		var elemento = "";
		var hayotro  = false;
		
		<!--//Recorro todas las columnas en pantalla para saber si hay alguno que sea 'Unica Seleccionable' -->
		while(document.getElementById("wcom"+cont1.toString())) 
		  {
		   elemento = document.getElementById("wcom"+cont1.toString());
		   
		   <!--//Si es 'Unica Seleccionable' y esta chuleada coloco un swicht que me indica esto (unico=true) y toma la columna en que esta (columna=cont1) -->
		   if (elemento.value=="off" && document.getElementById("patron"+f.toString()+"-"+cont1.toString()).checked==true)
		      {
			   unico   = true;
			   columna = cont1;
			   break;
			  }    	  
		   cont1++;  
	      }
	      
	    <!--// Una vez encontrado el patron unico y que esta chuliado, recorro de nuevo todas las columnas de la fila donde dieron el click -->
	    <!--   y si es diferente a la columna de unico no la dejo marcar, porque ya esta marcada la columna unica (osea el patron unico). -->
	    if (unico)
	       {
		    if (adi_ser != "on")  //Osea que no es horario de Adicion
		       {    
			    cont1 = 1;
			    while(document.getElementById("wcom"+cont1.toString()))
			       {
				    <!-- // Busco si hay alguna columna chuliada y que sea diferente de la columna unica -->
				    <!--    esto puede ocurrir porque se marco 1ro una columna no unica y luego si se esta tratando de marcar una unica-->   
				    if ((document.getElementById("patron"+f.toString()+"-"+cont1.toString()).checked) && (cont1 != columna))  
				       hayotro=true;
				    cont1++;
			       }
		       }    
		    
		    if (hayotro)
		       {
	            document.getElementById("patron"+f.toString()+"-"+c.toString()).checked=false;
	            alert ("El Patrón < "+document.getElementById("wcelda"+f.toString()+"-"+columna.toString()).tooltipText + " > No puede combinarse con otro(s)");
               }
              else
                 {	              
	              //alert ("Cod dieta: "+cod_dieta+f.toString()+" Otro codigo: "+f.toString()+cod_dieta);
	              
	              //var x=document.getElementById(cod_dieta+f.toString()).innerHTML;
	              
	              activarModal(cod_dieta+f.toString(),f.toString()+cod_dieta);
                 }     
           }
           
           //alert("adicion: "+adi_ser); 
      }
      
    function evaluarEnvio(fila, patron)
       {
//	    debugger;
		var mensaje="";
		var idElemento=fila.toString()+patron;
		var cont1 = 0;
		var valores = "";
		var arreglo = document.getElementById(idElemento);
		var setter = new Array();
		arreglo.value = "";
		
		var elemento = document.getElementById("sel"+idElemento);
		elemento.value="";
		
		while(document.getElementById("chk"+idElemento+cont1.toString()))         //**** Corresponde a los checkbox de la ventana modal
		  {
			mensaje += idElemento + cont1.toString();
			mensaje += document.getElementById("chk"+idElemento+cont1.toString()).checked ? " on\n\r" : " off\n\r";

			arreglo.value += document.getElementById("chk"+idElemento+cont1.toString()).checked ? "on," : "off,";

			setter[cont1] = document.getElementById("chk"+idElemento+cont1.toString()).checked;
			
			if(setter[cont1])
			   valores+=document.getElementById("chk"+idElemento+cont1.toString()).value+",";
			
			cont1++;
		  }

		arreglo.value = arreglo.value.substring(0,arreglo.value.length-1);
		elemento.value = valores.substring(0,valores.length-1);
		
		//On alert(arreglo.value);
		//On alert(elemento.value);

		$.unblockUI({onUnblock: function(){ 
				if(arreglo != 'undefined'){
					respuestaUnblock(idElemento,setter);
				}
				}});
       }     	
	
	
	$(document).ready(function()
	 {
		var cont1 = 1;
	    while(document.getElementById("wdie"+cont1))
	      {
	    	 $('#wdie'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	    	 $('#wcol'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	    	 cont1++;
          }; 
          
        var cont1 = 1;
	    while(cont1 <= parseInt(document.getElementById('num_pac').value))
	      {
		     var cont2 = 1; 
		     while(cont2 <= parseInt(document.getElementById('num_die').value)) 
		        {
			     if (document.getElementById('wcelda'+cont1.toString()+"-"+cont2.toString()))  
			        $('#wcelda'+cont1.toString()+"-"+cont2.toString()).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	    	     cont2++;
	    	    } 
    	     cont1++;   
          };   
	  });
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *              REGISTRO DE DIETAS             *
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
  
	                                              // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Febrero 10 de 2010)";              // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                              // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
	                                                      
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION                                                                                                                              \\
//=========================================================================================================================================\\
//En este programa se registran las diferentes dietas por servicio y pacientes de la clinica.                                              \\
//=========================================================================================================================================\\
	                                                           
//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//                                                                                                                                        \\
//========================================================================================================================================\\
	               
  $wfecha=date("Y-m-d");   
  $whora =(string)date("H:i:s");	                                                           
	                                                                                                       
  
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
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  
  encabezado("REGISTRO DE DIETAS",$wactualiz, "clinica");   
      
  
          
  //==================================================================================================================	 
  //==================================================================================================================	 
  //***********************************************  F U N C I O N E S  **********************************************
  //==================================================================================================================	 
  //==================================================================================================================	
  function accion_a_grabar($whis, $wing, $whab, $wser, $wpatron, &$westado, $wobservacion, $wintolerancias)
     {
	  global $wbasedato;
	  global $conex;
	  global $wfec;
	  global $whora;
	  
	  
	  $westado="on";
	  
	  //Primero valido que al paciente no le hallan dado ALTA DEFINITIVA, porque puede ser que la enfermera graba el 
	  //registro de Dietas despues de dado el paciente de ALTA y todavia lo tenga en pantalla, y eso lo que haria es
	  //reactivar el pedido de ese paciente que ya se fue.
	  $q = " SELECT COUNT(*) "
	      ."   FROM ".$wbasedato."_000018 "
	      ."  WHERE ubihis  = '".$whis."'"
	      ."    AND ubiing  = '".$wing."'"
	      ."    AND ubiald != 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      if ($row[0] > 0)                              //SI esta activo
         {
          //Busco si la historia ya tiene registrado el servicio en la auditoria
		  $q = " SELECT COUNT(*) "
		      ."   FROM ".$wbasedato."_000078 "
		      ."  WHERE audhis     = '".$whis."'"
		      ."    AND auding     = '".$wing."'"
		      ."    AND audser     = '".$wser."'"
		      ."    AND fecha_data = '".$wfec."'";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $row = mysql_fetch_array($res); 
	      if ($row[0] > 0)                          //Ya tiene servicio
	         {
		      if ($wpatron!="")                     //Si hay un patron?
		         {
			      //Busco si tiene el mismo patron activo, si si, quiere decir que NO lo modifico   
			      $q = " SELECT COUNT(*) "
			          ."   FROM ".$wbasedato."_000077 "
			          ."  WHERE movhis = '".$whis."'"
			          ."    AND moving = '".$wing."'"
			          ."    AND movser = '".$wser."'"
			          ."    AND movdie = '".$wpatron."'"
			          ."    AND movest = 'on' ";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		          $row = mysql_fetch_array($res); 
		          if ($row[0] > 0)                     //Ya tiene el servicio y el patron
		             {
		              //Busco si se modifico la OBSERVACION
		              $q = " SELECT COUNT(*) "
				          ."   FROM ".$wbasedato."_000077 "
				          ."  WHERE movhis = '".$whis."'"
				          ."    AND moving = '".$wing."'"
				          ."    AND movser = '".$wser."'"
				          ."    AND movobs = '".trim($wobservacion)."'"
				          ."    AND movest = 'on' ";
				      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			          $row = mysql_fetch_array($res); 
			          if ($row[0] > 0)                 //Ya tiene el servicio, el patron y la observacion, osea no se modifico nada
		                 $waccion="off";               //No se hace ninguna accion
		                else
		                   $waccion = "MODIFICO OBSERVACION";
		              
		              //Busco si se modificaron las INTOLERANCIAS
		              $q = " SELECT COUNT(*) "
				          ."   FROM ".$wbasedato."_000077 "
				          ."  WHERE movhis = '".$whis."'"
				          ."    AND moving = '".$wing."'"
				          ."    AND movser = '".$wser."'"
				          ."    AND movint = '".trim($wintolerancias)."'"
				          ."    AND movest = 'on' ";
				      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			          $row = mysql_fetch_array($res); 
			          if ($row[0] == 0)                 //Ya tiene el servicio, el patron y la observacion, osea no se modifico nada
		                 {
			              if ($waccion != "off")
			                 $waccion = $waccion." Y "."MODIFICO INTOLERANCIAS";
			                else
			                   $waccion = "MODIFICO INTOLERANCIAS";
		                 }         
	                 }
		            else                               //Hay servicio pero no hay Patron
		              {
			           //Busco si esta dentro del rango modificacion del pedido
			           $q = " SELECT COUNT(*) "
				           ."   FROM ".$wbasedato."_000076 "
				           ."  WHERE sernom = '".$wser."'"
				           ."    AND serhin <= '".$whora."'"
				           ."    AND serhmo >= '".$whora."'"
				           ."    AND seradi  = 'on' ";
			           $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				       $row = mysql_fetch_array($res); 
				       if ($row[0] > 0) 
				          {
				           $waccion = "MODIFICO PEDIDO";     //Quiere decir que se esta modificando
				          }
				         else                                //Esta fuera del rango de modificacion osea que es una adicion
				           {
					        //Busco si esta dentro del rango Adicion   
					        $q = " SELECT COUNT(*) "
					            ."   FROM ".$wbasedato."_000076 "
					            ."  WHERE sernom = '".$wser."'"
					            ."    AND serhin <= '".$whora."'"
					            ."    AND serhad >= '".$whora."'"
					            ."    AND seradi  = 'on' ";
				            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					        $row = mysql_fetch_array($res); 
					        if ($row[0] > 0)   
					           {
				                $waccion = "ADICION"; 
			                   }
			                  else
			                     {
				                  $waccion="off";
			                      echo "<script language='Javascript'>";
					              echo "alert ('EL SERVICIO DE LA HISTORIA: ".$whistoria." NO PUEDE SER ** ADICIONADO o MODIFICADO ** PORQUE ESTA POR FUERA DEL HORARIO');"; 
				                  echo "</script>";   
			                     }      
			               } 
		              } 
	             }
	            else                                   //Si entra aca es porque tenia un patron y ya se le quito, por lo tanto se cancelo.
	              {
		           //Busco si se esta dentro del rango de cancelacion   
		           $q = " SELECT COUNT(*) "
			           ."   FROM ".$wbasedato."_000076 "
			           ."  WHERE sernom = '".$wser."'"
			           ."    AND serhin <= '".$whora."'"
			           ."    AND serhca >= '".$whora."'"
			           ."    AND seradi  = 'on' ";
		           $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			       $row = mysql_fetch_array($res); 
			       if ($row[0] > 0)
			          {
			           $waccion="CANCELADO";
	                   $westado="off";
	                  }
	                 else
	                   {
		                $waccion="off";
	                    echo "<script language='Javascript'>";
			            echo "alert ('EL SERVICIO DE LA HISTORIA: ".$whistoria." NO PUEDE SER  ** CANCELADO ** PORQUE ESTA POR FUERA DEL HORARIO');"; 
		                echo "</script>";
	                   }    
	              } 
	         }
	       else  //No tenia el servicio 
	          {
	           if ($wpatron!="")  //Si hay un patron
		         {
			      //Busco si esta dentro del rango de PEDIDO   
			      $q = " SELECT COUNT(*) "
			          ."   FROM ".$wbasedato."_000076 "
			          ."  WHERE sernom = '".$wser."'"
			          ."    AND serhin <= '".$whora."'"
			          ."    AND serhfi >= '".$whora."'"
			          ."    AND seradi  = 'on' ";
		          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      $row = mysql_fetch_array($res); 
			      if ($row[0] > 0)   
			         {
		              $waccion = "PEDIDO"; 
	                 }
	                else
	                   {
		                //Busco si esta dentro del rango de ADICION  
				        $q = " SELECT COUNT(*) "
				            ."   FROM ".$wbasedato."_000076 "
				            ."  WHERE sernom = '".$wser."'"
				            ."    AND serhin <= '".$whora."'"
				            ."    AND serhad >= '".$whora."'"
				            ."    AND seradi  = 'on' ";
			            $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				        $row = mysql_fetch_array($res); 
				        if ($row[0] > 0)   
				          {
			               $waccion = "ADICION"; 
		                  }
		                 else
		                    {
			                 $waccion="off";
		                     echo "<script language='Javascript'>";
				             echo "alert ('EL SERVICIO DE LA HISTORIA: ".$whistoria." NO PUEDE SER  ** ADICIONAR ** PORQUE ESTA POR FUERA DEL HORARIO');"; 
			                 echo "</script>";
		                    }       
		               }        
			     } 
	          	else
	          	  $waccion="off";                      //No se a seleccionado ningun patron y tampoco tenia patron para el servicio.
	          }	
          }
         else 
            $waccion="off";               //No se hace ninguna accion      
	   return $waccion; 
     }
	 
	 
  
  function validaciones($whistoria, $wingreso, $whabitacion, $wservicio, $wtransaccion)
     {
	  global $wbasedato;
	  global $conex;
	  global $wcco;
	  global $wnomcco;
	  global $wfec;
	  global $wemp_pmla;
	  global $wser; 
	  global $wfecha;
	  
	  global $whabilitado;
	  
	  global $wpedido;      //Indica si esta habilitada la hora de pedido
	  global $wmodifica;    //Indica si esta habilitada la hora de modificacion
	  global $wcancela;     //Indica si esta habilitada la hora de cancelacion
	  global $wadicion;     //Indica si esta habilitada la hora de adicion
	  
	  
	  
	  $whora =(string)date("H:i:s");
	  
	  
	  //Valido que la fecha se igual a la actual, para poder habilitar el boton de adicion y cancelacion
	  if ($wfec == $wfecha)
	     {
		  switch ($wtransaccion)
		     {  
			  case "Consulta":  
			      //========================================================================================================\\
			      //** H O R A R I O ***************************************************************************************\\
			      //Aca se hacen cuatro validaciones basadas todas en la hora actual, se valida si se habilitan los cajones \\
			      //de las dietas o no, y sabiendo porque se habilitan, si es por, PEDIDO, MODIFICACION, ADICION O          \\
			      //CANCELACION, para que al momento de grabar se sepa cual es la accion o transaccion que se esta haciendo.\\
			      //********************************************************************************************************\\
			      //Verifico que se pueda actualiazar o no de acuerdo al horario del servicio, si alguna de las horas       \\
			      //limite no se a cumplido entonces dejo el cajon habilitado, pero al GRABAR se debe identificar que accion\\
			      //se esta haciendo PEDIDO, MODIFICACION, ADICION o CANCELACION.                                           \\
			      //========================================================================================================\\
			      //Que este dentro de la HORA INICIAL y FINAL de PEDIDO
			      $q = " SELECT COUNT(*) "
			          ."   FROM ".$wbasedato."_000076 "
			          ."  WHERE sernom = '".$wservicio."'"
			          ."    AND serhin <= '".$whora."'"
			          ."    AND (serhfi >= '".$whora."'" 
			          ."     OR  serhmo >= '".$whora."'"
			          ."     OR  serhca >= '".$whora."'"
			          ."     OR  serhad >= '".$whora."')"
			          ."    AND seradi  = 'on' ";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      $row = mysql_fetch_array($res); 
			      if ($row[0] > 0)
			         {
				      $whabilitado="Enabled";
				     }
			        else
			          {
				       $whabilitado="Disabled";
					  } 
			    
		          ///return $whabilitado;
		          break;
		           
			  case "Grabar":
			      //Valido si todavia esta dentro del rango de tiempo para grabar el servicio
			      //volviendo a llamar esta funcion con el parametro de 'Consulta'
			      validaciones($whistoria, $wingreso, $whabitacion, $wservicio, "Consulta");
			      if ($whabilitado != "Enabled" )
			         {
				      echo "<script language='Javascript'>";
			          echo "alert ('NO SE ACTUALIZO EL SERVICIO DE LA HISTORIA: ".$whistoria." PORQUE ESTA POR FUERA DEL HORARIO');"; 
		              echo "</script>";
		              $whabilitado="Disabled";
		             }  
		     }
         }
        else    //Si la fecha seleccionada es anterior el cajon de adicion siempre esta deshabilitado
	       $whabilitado="Disabled";  
	       
	  return $whabilitado;            
	 }    
  //==================================================================================================================
  //==================================================================================================================
	
  
  //==================================================================================================================
  //================================================================================================================== 
  function determinar_adicion($wser)
     {
	  global $wbasedato;
	  global $conex;
	  global $whora;
	  global $wadi_ser;
  
	  //Busco que la hora este dentro del rango de inicio del servicio y la hora maxima de modificacion, si esta por fuera
	  //de este rango es porque puede estar dentro del rango de adicion.
	  $q = " SELECT COUNT(*) "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE serhin <= '".$whora."'"
	      ."    AND serhca >= '".$whora."'"
	      ."    AND sernom  = '".$wser."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res); 
      if ($row[0] > 0)   
         $wadi_ser="off";
        else
           { 
            //Busco si esta dentro del rango Adicion   
			$q = " SELECT COUNT(*) "
			    ."   FROM ".$wbasedato."_000076 "
			    ."  WHERE sernom  = '".$wser."'"
			    ."    AND serhin <= '".$whora."'"
			    ."    AND serhad >= '".$whora."'"
			    ."    AND seradi  = 'on' ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row = mysql_fetch_array($res); 
			if ($row[0] > 0)
			   {
				$wadi_ser="on";   
			   }
			  else
			     $wadi_ser="off"; 
	       }
	   return $wadi_ser;  
	 }
  //==================================================================================================================
  //==================================================================================================================
  
  
  //==================================================================================================================
  //================================================================================================================== 
  function buscar_servicio_anterior()
     {
	  global $wbasedato;
	  global $conex;
	  global $whora;
	  global $wser_ant;
	  
	  global $wser;
	  
	  
	  //Traigo la Hora Inicial del servicio actual, porque con ese dato busco que coincida con la hora final del servicio anterior
	  $q = " SELECT mid(serhin,1,2) "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE sernom = '".$wser."'"
	      ."    AND serest = 'on' ";  
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);    
	  if ($num > 0) 
	     {
		  $row = mysql_fetch_array($res);
		  
		  //Busco el servicio anterior a partir de la hora inicial del servicio actual
		  $q = " SELECT sernom "
		      ."   FROM ".$wbasedato."_000076 "
		      ."  WHERE MID(serhfi,1,2) = '".$row[0]."'"
		      ."    AND serest = 'on' ";    
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);    
		  if ($num > 0) 
		     {  
			  $row = mysql_fetch_array($res);
			  $wser_ant=$row[0];  
			 }
			else
			   {
				//Busco el servicio anterior a partir de la hora inicial del servicio actual
			    $q = " SELECT sernom "
			        ."   FROM ".$wbasedato."_000076 "
			        ."  WHERE MID(serhin,1,2) = '00'"
			        ."    AND serest = 'on' "; 
			    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			    $num = mysql_num_rows($res);
			    if ($num > 0)
			       {
				    $row = mysql_fetch_array($res);
			  		$wser_ant=$row[0]; 
			  	   }
			      else     
			         $wser_ant="";
		       }
         }
	 }
  //==================================================================================================================
  //==================================================================================================================
  
  
  //==================================================================================================================
  //================================================================================================================== 
  function buscar_servicio_anterior_por_historia($whis, $wing)
     {
	  global $wbasedato;
	  global $conex;
	  global $whora;
	  global $wser_ant;
	  
	  global $wser;
	  
	  
	  //Busco el servicio anterior a partir de la hora inicial del servicio actual
	  $q = " SELECT movser, MAX(movfec), MAX(hora_data) "
	      ."   FROM ".$wbasedato."_000077 "
	      ."  WHERE movhis = '".$whis."'"
	      ."    AND moving = '".$wing."'"
	      ."    AND movest = 'on' "
	      ."  GROUP BY 1 "
	      ."  ORDER BY 2 desc, 3 desc ";    
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);    
	  if ($num > 0) 
	     {  
		  $row = mysql_fetch_array($res);
		  $wser_ant=$row[0];
		 }
		else
		   $wser_ant="";     
     }
  //==================================================================================================================
  //==================================================================================================================
  
  
  
  //==================================================================================================================
  //================================================================================================================== 
  function buscar_si_hay_servicio_anterior($wser) 
     {
	  global $wbasedato;
	  global $conex;
	  global $wfecha;
	  global $wser;
	  global $wser_ant;
	  
	  global $whabilitado;
	  
	  
	  validaciones('', '', '', $wser, "Consulta");
	  
	  if ($whabilitado)
	     {
		  //Primero verifico que no halla movimiento en el dia con el Servicio Actual, porque si lo hay no tengo que buscar
		  //si hay movimeinto del servicio anterior.
		  $q = " SELECT COUNT(*) "
		      ."   FROM ".$wbasedato."_000077 "
		      ."  WHERE movfec = '".$wfecha."'"
		      ."    AND movser = '".$wser."'"
		      ."    AND movest = 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $row = mysql_fetch_array($res); 
		  
		  if ($row[0] == 0)
		     {
			  buscar_servicio_anterior($wser);    //Busco cual es el servicio anterior
			     
			  
			  //Busco si hay algun registro con el servicio anterior activo.
		      $q = " SELECT MAX(movfec) "
		          ."   FROM ".$wbasedato."_000077 "
		          //."  WHERE movfec = '".$wfecha."'"
		          ."  WHERE movser = '".$wser_ant."'"
		          ."    AND movest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row = mysql_fetch_array($res); 
			  $num = mysql_num_rows($res);  
			  if ($num > 0) 
			     {    
				  return true;    
				 }
				else
				   return false;  
	         }
	        else
	           return false; 		      
         }
        else
           return false; 
	 }
  //==================================================================================================================
  //==================================================================================================================
  
  /*
  function composicion_patron($wpatron, $fila, $whis, $wing, $wser)
     {
	  global $wbasedato;
	  global $conex; 
	  global $wfecha;  
	     
	  $q = " SELECT clades, claord, prodes, provan, profec, provac "
	      ."   FROM ".$wbasedato."_000082, ".$wbasedato."_000083 "
	      ."  WHERE propat = '".$wpatron."'"
	      ."    AND procla = clades "
	      ."    AND proest = 'on' "
	      ."    AND claest = 'on' "
	      ."  ORDER BY 2, 3 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  
	  if ($num > 0)
	     {
		  $wvar="";   
		  //echo "<table class='ventana-modal-ventana'>";
		  //Cantidad de opciones por Patron
		  for ($i=0;$i<=$num-1;$i++)
		     {
			  $row = mysql_fetch_array($res);
			  
			  //Busco si esta opcion esta grabada para el paciente en la tabla 000084
			  $q = " SELECT COUNT(*) "
			      ."   FROM ".$wbasedato."_000084 "
			      ."  WHERE detfec = '".$wfecha."'"
			      ."    AND dethis = '".$whis."'"
			      ."    AND deting = '".$wing."'"
			      ."    AND detser = '".$wser."'"
			      ."    AND detpat = '".$wpatron."'"
			      ."    AND detpro = '".$row[2]."'"
			      ."    AND detest = 'on' ";
			 $respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());       
			 $rowpro = mysql_fetch_array($respro);
			
			 if ($rowpro[0] > 0)
			    {
				 $wchk = "CHECKED";
			     
			     $wvar=$wvar.$row[2].",";
		        } 
			   else
			      $wchk = "UNCHECKED";  
		  
		      echo "<tr>";
		      //echo "<input type='checkbox' id='chk".$fila.$wpatron.$i."' name='".$row[2]."_".$fila.$i."' ".$wchk." value='".$row[2]."_".$fila."'>".$row[2]."<input type=text id='".$wpatron.$fila.$i."_can"."'  value='1' size=3><br>";   
		      echo "<td><input type='checkbox' id='chk".$fila.$wpatron.$i."' name='".$row[2]."_".$fila.$i."' ".$wchk." value='".$row[2]."_".$fila."'>".$row[2]."</td><td><input type=text id='".$wpatron.$fila.$i."_can"."'  value='1' size=3></td>";   
			  echo "</tr>";
			 }
		 }    
	 } 
  */ 
	 
  function composicion_patron($wpatron, $fila, $whis, $wing, $wser)
     {
	  global $wbasedato;
	  global $conex; 
	  global $wfecha;  
	     
	  $q = " SELECT clades, claord, prodes, provan, profec, provac "
	      ."   FROM ".$wbasedato."_000082, ".$wbasedato."_000083 "
	      ."  WHERE propat = '".$wpatron."'"
	      ."    AND procla = clades "
	      ."    AND proest = 'on' "
	      ."    AND claest = 'on' "
	      ."  ORDER BY 2, 3 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  
	  if ($num > 0)
	     {
		  $wvar="";   
		  
		  $row = mysql_fetch_array($res);
		  $word = $row[1];
		  //mysql_data_seek($res,0);        //Devuelvo el puntero
		  
		  echo "<tr>";
		  //Cantidad de opciones por Patron
		  for ($i=0; $i < $num; $i++)
		     {
			  $word = $row[1];
			     
			  echo "<td valign='top' bgcolor='38B0DE'>";
			  echo "<table valign='top'>";
			  echo "<tr><td colspan=3 align=center><font size=3><b>".$row[0]."</b></font></td></tr>";  
			  while ($i < $num and $word == $row[1])   
			       {
					//Busco si esta opcion esta grabada para el paciente en la tabla 000084
					$q = " SELECT COUNT(*) "
					    ."   FROM ".$wbasedato."_000084 "
					    ."  WHERE detfec = '".$wfecha."'"
					    ."    AND dethis = '".$whis."'"
					    ."    AND deting = '".$wing."'"
					    ."    AND detser = '".$wser."'"
					    ."    AND detpat = '".$wpatron."'"
					    ."    AND detpro = '".$row[2]."'"
					    ."    AND detest = 'on' ";
					$respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());       
					$rowpro = mysql_fetch_array($respro);
					
					if ($rowpro[0] > 0)
					   {
					    $wchk = "CHECKED";
					     
					    $wvar=$wvar.$row[2].",";
				       } 
					  else
					     $wchk = "UNCHECKED";  
				  
					echo "<tr>";
				    echo "<td><input type='checkbox' id='chk".$fila.$wpatron.$i."' name='".$row[2]."_".$fila.$i."' ".$wchk." value='".$row[2]."_".$fila."'>".$row[2]."</td>"; //"<td><input type=text id='".$wpatron.$fila.$i."_can"."'  value='1' size=3></td>";   
					echo "</tr>";
					
					$row = mysql_fetch_array($res);
					
					$i++;
			       }
			      $i=$i-1;
			      //mysql_data_seek($res,($i-1));        //Devuelvo el puntero
			      echo "</table>";
			      echo "</td>"; 
		 	 }
		   echo "</tr>";	 
		 }    
	 }	    
  
  
  function definir_div($wpatron, $fila, $whis, $wing, $wser)
     {
	  $wid_hidden=$fila.$wpatron;
	  $wid_sel="sel".$fila.$wpatron;
	  
	  echo "<input type='hidden' name='".$wid_hidden."' id='".$wid_hidden."'/>";
	  echo "<input type='hidden' name='".$wid_sel."'    id='".$wid_sel."'/>";
	  
	  
	  //display: none; 
	  echo "<div id='".$wpatron.$fila."' style='display: none; cursor: default' width=300 height=50>";
	  echo "<table valign='top'>";
	  echo "<span class='encabezadoTabla'>Patron : ".$wpatron." - ".$wid_hidden."</span>";
	  
	  echo "<img src='../../../include/root/cerrar.gif' title='Cerrar' onclick='javascript:cerrarModal();' style='cursor:hand'>";
	  echo "<br><br>";
	  
	  composicion_patron($wpatron, $fila, $whis, $wing, $wser);
	  
	  echo "<input type='button' onClick='javascript:evaluarEnvio(\"".$fila."\"".","."\"".$wpatron."\");' value='Grabar'>";
	  
	  echo "</table>";
	  echo "</div>";
	  
	 }
       
  
  //==================================================================================================================
  //==================================================================================================================
  function mostrar()
     {
	  global $wbasedato;
	  global $conex;
	  global $wcco;
	  global $wnomcco;
	  global $wfec;
	  global $wfecha;
	  global $wemp_pmla;
	  global $wser;
	  
	  global $wcelda;
	  global $wdietas;
	  
	  global $num_die;
	  global $num_pac;
	  
	  global $whis;
	  global $wing;
	  global $whab;          //Habitacion
	  global $wedad;          //Edad
	  global $wtem;          //Tipo de empresa
	  
	  global $wpedido;
	  global $wmodifica;
	  global $wadicion;
	  global $wcancela;
	  
	  global $whabilitado;
	  
	  global $wser_ant;
	          
	  global $wadi_ser;
	  
	  
	  //=======================================================================================================================
      //OJO ESTE PROCEDIMIENTO ES CLAVE PARA EL FUNCIONAMIENTO DEL PROGRAMA
      //=======================================================================================================================
      //Los patrones NO son conbinables solo cuando es en horario de Solicitud normal (Osea entre Hora Inicial de Pedido y 
      //Hora Máxima de Modificación, pero pasado este tiempo cual pedido se toma como una adición y las adiciones si pueden
      //estar cmbinadas con otros patrones.
      //Por eso se hace el siguiente procedimiento de determinar el horario en que se esta haciendo la transaccion para definir
      //si los patrones son combinables o no, porque solo se combinan cuando es adición.
      $wadi_ser=determinar_adicion($wser);
      
      //$wadi_ser='on' Indica que se esta en horario de adiciones para el servicio seleccionado, por lo tanto los patrones se 
      //puede combinar, entonces siempre coloco en el campo $row_die[4]=='on' el cual viene de la tabla 000076 
	  
	     
	  //Busco caracteristicas del servicio
	  $q = " SELECT seresq, seradi, sertpo, sercap "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE sernom = '".$wser."'"
	      ."    AND serest = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);    
	  if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);   
		     
		  $wesq = $row[0];   //Esquema asociado al Servicio
		  $wtpo = $row[2];   //Tipos de empresa POS
		  $wemp = $row[3];   //Cantidad de empleados (para enviar meriendas)
		 }    
	  	
	  //Busco si ya existe el servicio seleccionado en el dia, si ya existe no doy la posibilidad de traer el servicio anterior
	  //si no existe, traigo la configuracion igual al anterior servicio, para esto coloco $wserant='on'
	  if (buscar_si_hay_servicio_anterior($wser))
	     $wserant="on";   
	    else 
		   $wserant="off";  
		 
		   
	   //BUSCO si el CCO es de Urgencias para cambiar el query	   
	   $q = " SELECT COUNT(*) "
           ."   FROM ".$wbasedato."_000011"
           ."  WHERE ccourg = 'on' "
           ."    AND ccocod = '".trim($wcco)."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
         
      if ($row[0] > 0) //Es de Urgencias
		 {   
		  //Aca traigo los pacientes que estan en Urgencias en el momento y adiciono los que han tenido algun servicio de dieta 
		  //y todavia estan en Urgencias y que son de dias anteriores.   
	      $q = " SELECT 'Urg', ubihis, ubiing, trim(pacno1), trim(pacno2), pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, "
	          ."        ubialp, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0) "
		      ."   FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016 "
		      ."  WHERE ubihis                           = orihis "
		      ."    AND ubiing                           = oriing "
		      ."    AND oriori                           = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
		      ."    AND oriced                           = pacced "
		      //."    AND ubiptr                          != 'on' "             //Solo los pacientes que esten siendo trasladados
		      ."    AND ubiald                          != 'on' "			  //Que no este en Alta Definitiva
		      ."    AND ubisac                           = '".trim($wcco)."'" //Servicio Actual
		      ."    AND ".$wbasedato."_000018.fecha_data = '".$wfecha."' "
		      ."    AND ubihis = inghis "
		      ."    AND ubiing = inging "
		      ."  UNION "
		      /*//Pacientes que estan en Urgencias
		      ." SELECT 'Urg', ubihis, ubiing, trim(pacno1), trim(pacno2), pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, "
		      ."        ubialp, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0) "
		      ."   FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016 "
		      ."  WHERE ubihis                           = orihis "
		      ."    AND ubiing                           = oriing "
		      ."    AND oriori                           = '".$wemp_pmla."'"        //Empresa Origen de la historia, 
		      ."    AND oriced                           = pacced "
		      ."    AND ubiptr                           = 'on' "                   //Solo los pacientes que no esten siendo trasladados
		      ."    AND ubiald                          != 'on' "			        //Que no este en Alta Definitiva
		      ."    AND ubisan                           = '".trim($wcco)."'"       //Servicio Anterior
		      ."    AND ".$wbasedato."_000018.fecha_data = '".$wfecha."' "
		      ."    AND ubihis = inghis "
		      ."    AND ubiing = inging " 
		      ."  UNION "*/
		      //Pacientes que esten en Urgencias desde dias anteriores y hallan tenido servicio de alimentacion
		      ." SELECT 'Urg', ubihis, ubiing, trim(pacno1), trim(pacno2), pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, "
		      ."        ubialp, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0) "
		      ."   FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000077, ".$wbasedato."_000016 "
		      ."  WHERE ubihis                           = orihis "
		      ."    AND ubiing                           = oriing "
		      ."    AND oriori                           = '".$wemp_pmla."'"       //Empresa Origen de la historia, 
		      ."    AND oriced                           = pacced "
		      ."    AND ubiald                          != 'on' "			       //Que no este en Alta Definitiva
		      ."    AND ubisac                           = '".trim($wcco)."'"      //Servicio Actual
		      ."    AND ubihis                           = movhis "
		      ."    AND ubiing                           = moving "
		      ."    AND movfec                          >= str_to_date(ADDDATE('".$wfecha."',-1),'%Y-%m-%d') "  //Pacientes que hallan tenido servicio por lo menos el dia anterior
		      ."    AND ".$wbasedato."_000018.fecha_data < '".$wfecha."' "
		      ."    AND ubihis = inghis "
		      ."    AND ubiing = inging " 
		      ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14 "
		      ."  ORDER BY 4,5 ";
		 }
	    else
	       {
		    //Si algo cambia en este query tambien se debe cambiar en el mismo query de mas abajo   
		  	$q = " SELECT habcod, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ubiptr, ubimue, pacnac, "
		  	    ."        ubialp, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0) "
			    ."   FROM root_000036, root_000037, ".$wbasedato."_000020, ".$wbasedato."_000018, ".$wbasedato."_000016 "
			    ."  WHERE ubihis  = orihis "
			    ."    AND ubiing  = oriing "
			    ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
			    ."    AND oriced  = pacced "
			    ."    AND ubiald != 'on' "			   //Que no este en Alta Definitiva
			    ."    AND ubihis  = habhis "
			    ."    AND ubiing  = habing "
			    ."    AND habcco  LIKE '".trim($wcco)."'"
			    ."    AND ubihis = inghis "
			    ."    AND ubiing = inging "
			    ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14 "
			    ."  ORDER BY 1 ";   
		   }          
		   
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num_pac = mysql_num_rows($res);
	  
	  if ($num_pac > 0)
	     {
		  echo "<input type='HIDDEN' id='num_pac' name='num_pac' value='".$num_pac."'>";  
		  
		  //==============================================================================================================
		  // ENCABEZADO DONDE VAN LAS OBSERVACIONES DE ENFERMERIA Y LA CPA
		  //==============================================================================================================
		  //Traigo las observaciones y numero de empleados del ENCABEZADO DEL SERVICIO
	      $q = " SELECT encobe, encobc, enccap "
	          ."   FROM ".$wbasedato."_000085 "
	          ."  WHERE encfec = '".$wfec."'"
	          ."    AND enccco = '".$wcco."'"
	          ."    AND encser = '".$wser."'";
	      $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $rowenc = mysql_fetch_array($resenc);
	       
	      if (trim($rowenc[0]) != "" or trim($rowenc[1]) or trim($rowenc[2]))
	         {
	          $wobserv_enfer = $rowenc[0];
	          $wobserv_cpa   = $rowenc[1];
	          $wcap          = $rowenc[2];
	         }
	        else
	           {
	            $wobserv_enfer="";
	            $wobserv_cpa="";
	            $wcap=""; 
	           }       
	       
	      echo "<tr class='fila2'>";
	      echo "<td colspan=12 align=center class=encabezadoTabla>Observaciones Enfermería</td>";
	      if ($wemp == "on")  //Indica si en el servicio que se esta registrando se coloca la cantidad de empleados que hay para la Merienda o No.
	         echo "<td colspan=4 align=center class=encabezadoTabla>Cantidad de<br>Meriendas</td>";
	        else
	           echo "<td colspan=4 align=center class=encabezadoTabla>&nbsp</td>"; 
	      echo "<td colspan=8  align=center class=encabezadoTabla>Observaciones CPA</td>";
	      echo "</tr>";     
	      
	      echo "<tr align=center>";
	      echo "<td colspan=12><TEXTAREA name='wobserv_enfer' rows=4 cols=60>".$wobserv_enfer."</TEXTAREA>";
	      if ($wemp == "on") //Indica si en el servicio que se esta registrando se coloca la cantidad de empleados que hay para la Merienda o No.
	         echo "<td colspan=4><input type=text name=wcap value='".$wcap."'</td>";
	        else
	           echo "<td colspan=4>&nbsp</td>"; 
	      echo "<td colspan=8><TEXTAREA name='wobserv_cpa'   rows=4 cols=60 readonly>".$wobserv_cpa."</TEXTAREA>";
	      echo "</tr>";
		  //==============================================================================================================
		  
		     
		  //Traigo las DIETAS que existen para colocar la barra de titulo
		  $q = " SELECT diecod, diedes, diecom, dieord, diecbi "
		      ."   FROM ".$wbasedato."_000041, ".$wbasedato."_000076 "
		      ."  WHERE dieest = 'on' "
		      ."    AND dieord > 0 "
		      ."    AND dieesq = seresq "
		      ."    AND sernom = '".$wser."'"
		      ."  ORDER BY 4 ";
		  $res_die = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $num_die = mysql_num_rows($res_die);   
	      
	      echo "<input type='HIDDEN' id='num_die' name='num_die' value='".$num_die."'>";
	      
	      if ($num_die > 0 )
	         {
		      //========================================================================================================   
		      //Aca coloco los cajones multiselecion X columna   
		      //========================================================================================================
		      validaciones('', '', '', $wser, "Consulta");
		      echo "<tr class=encabezadoTabla>";
		      echo "<td colspan=5>&nbsp</td>";
		      for ($i=1;$i<=$num_die;$i++)        //Linea de cajones para que cuando se de clic se marquen o desmarquen todos los cajones de la columna
		         {
			      $row_die = mysql_fetch_array($res_die);
			      echo "<td><SPAN id='wcol".$i."' title='".$row_die[1]."'><INPUT TYPE='checkbox' id=chk".$i." NAME='wcol[".$i."]' ".$whabilitado." onClick='javascript:chequeoTodos("."\"".$i."\"".");'></SPAN></td>";
			     }
	          echo "<td colspan=3>&nbsp</td>"; 
	          echo "</tr>";
	          //=======================================================================================================
	          
	          
	          //========================================================================================================   
		      //Aca los titulos de cada columna, basados en el maestro de Dietas o Patrones con JQUERY.
		      //========================================================================================================
	          mysql_data_seek($res_die,0);         //Devuelvo el puntero al primer registro      
		      echo "<tr class=encabezadoTabla>";
		      echo "<td>Hab</td>";
		      echo "<td>Días</td>";
		      echo "<td>Edad</td>";
		      echo "<td>Historia</td>";
		      echo "<td>Paciente</td>";
		      $j=0;
		      for ($i=1;$i<=$num_die;$i++)
		         {
			      $row_die = mysql_fetch_array($res_die);
			         
		          echo "<td><span id='wdie".$i."' title='".$row_die[1]."'>".strtoupper($row_die[0])."</span></td>";
		          $wdietas[$i]=$row_die[1];
		          
		          if ($row_die[4]=="on")
		             {
			          $wdie_unicas[$j]=$row_die[1];
			          $j++;
		             }
		          
		          echo "<input type='HIDDEN' id='wcom$i' value='".$row_die[4]."'>";
	             } 
	         }      
	      echo "<td>Observación</td>";
		  echo "<td>Patologia / Intolerancias</td>";
		  echo "<td>Afinidad</td>";
		  echo "</tr>"; 
		     
		     
		  for($i=1;$i<=$num_pac;$i++)                             //For por habitacion o paciente
			 {
			  $row = mysql_fetch_array($res);  	  
				  
			  if (is_integer($i/2))
                 $wclass="fila1";
                else
                   $wclass="fila2";
			  
			  $whab[$i] = $row[0];                                       //Habitacion
			  $whis[$i] = $row[1];                                       //Historia
			  $wing[$i] = $row[2];                                       //Ingreso
			  $wpac[$i] = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];   //Paciente
			  $wdpa[$i] = $row[7];                                       //Documento de lpaciente, sirve para buscar en Magenta
		      $wtid[$i] = $row[8];                                       //Tipo de Identificacion, para Magenta
		      $wptr[$i] = $row[9];                                       //proceso de traslado
		      $wmue[$i] = $row[10];                                      //Muerte
		      $wnac[$i] = $row[11];                                      //Fecha nacimiento
		      $walp[$i] = $row[12];                                      //Alta en proceso
		      $wtem[$i] = $row[13];                                      //Tipo de Empresa
		      $west[$i] = $row[14];                                      //Dias de Estancia
		      
		      
		      if ($walp[$i]=="on")     //Si esta en proceso de alta Resalto la fila
		         $wclass="fondoAmarillo";
		      
		         
		      //Calculo la edad
		      $wfnac=(integer)substr($wnac[$i],0,4)*365 +(integer)substr($wnac[$i],5,2)*30 + (integer)substr($wnac[$i],8,2);
			  $wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
			  $wedad[$i]=(($wfhoy - $wfnac)/365);
			  
			  if ($wedad[$i] < 1)
		         $wedad[$i] = "<b>".number_format(($wedad[$i]*12),0,'.',',')." Meses</b>";
		        else
		           $wedad[$i]=number_format($wedad[$i],0,'.',',')." Años"; 
		           
		      echo "<tr class='".$wclass."'>";
		      echo "<td>".$whab[$i]."</td>";
		      echo "<td>".$west[$i]."</td>";
		      echo "<td>".$wedad[$i]."</td>";
		      echo "<td>".$whis[$i]."-".$wing[$i]."</td>";
		      echo "<td>".$wpac[$i]."</td>";
		      
		      echo "<input type='HIDDEN' name='whis[".$i."]' value='".$whis[$i]."'>";
		      echo "<input type='HIDDEN' name='wing[".$i."]' value='".$wing[$i]."'>";
		      echo "<input type='HIDDEN' name='whab[".$i."]' value='".$whab[$i]."'>";
		      echo "<input type='HIDDEN' name='wpac[".$i."]' value='".$wpac[$i]."'>";
		      echo "<input type='HIDDEN' name='wedad[".$i."]' value='".$wedad[$i]."'>";
		      echo "<input type='HIDDEN' name='wtem[".$i."]' value='".$wtem[$i]."'>";
		      //echo "<input type='HIDDEN' name='west[".$i."]' value='".$west[$i]."'>";
		      
		      validaciones($whis[$i], $wing[$i], $whab[$i], $wser, "Consulta");
		      
		      ///===============================================
		      
		      $wcolor="yellow";
		      //if ($wptr[$i]=="on" or $wmue[$i]=="on")    //Si la historia esta en proceso de traslado o el paciente murio 'deshabilito' los cajones
		      if ($wmue[$i]=="on")    //Si la historia esta en proceso de traslado o el paciente murio 'deshabilito' los cajones
		         {
		          $whabilitado="Disabled";
		          $wcolor="blue";
		         }
		         
		         
		      if ($wPTR[$i]=="on")    //Si la historia esta en proceso de traslado 
		         {
		          $whabilitado="Disabled";
		          $wcolor="3299CC";
		         }   
		      
		      //ATENCION !!!! Si la variable $wserant=='on' es porque se debe traer la configuracion del servicio anterior al actual
		      //entonces cambio el query pero con la variable del servicio anterior.
		      if ($wserant=="off")  //Indica que NO se puede traer el SERVICIO ANTERIOR
		         {   
			      //Como el PATRON de alimentacion puede ser mas de uno para un mismo paciente, y este queda grabado en el mismo campo 'movdie'
			      //entonces traigo este campo y le hago un explode para recorrerlo buscando el patron que estoy buscando segun el result de $row_die
			      $q = " SELECT movdie, id " 
			          ."   FROM ".$wbasedato."_000077 "
			          ."  WHERE movfec = '".$wfec."'"
			          ."    AND movhis = '".$whis[$i]."'"
			          ."    AND moving = '".$wing[$i]."'"
			          ///."    AND movhab = '".$whab[$i]."'"
			          ."    AND movser = '".$wser."'"           //Servicio Actual
			          ."    AND movest = 'on' ";
			       $wcolor="yellow";
			       
			       $wseraux=$wser;  
			     }
		        else    //Si entra por aca es porque debe traer el SERVICIO ANTERIOR
		          {
			       buscar_servicio_anterior_por_historia($whis[$i], $wing[$i]);   
			          
			       //Como el PATRON de alimentacion puede ser mas de uno para un mismo paciente, y este queda grabado en el mismo campo 'movdie'
			       //entonces traigo este campo y le hago un explode para recorrerlo buscando el patron que estoy buscando segun el result de $row_die
			       $q = " SELECT movdie, id " 
			           ."   FROM ".$wbasedato."_000077 "
			           //."  WHERE movfec = '".$wfec."'"
			           ."  WHERE movhis = '".$whis[$i]."'"
			           ."    AND moving = '".$wing[$i]."'"
			           ."    AND movhab = '".$whab[$i]."'"
			           ."    AND movser = '".$wser_ant."'"      //Servicio Anterior
			           ."    AND movest = 'on' ";
			       $wcolor="cccccc";
			       
			       $wseraux=$wser_ant;   
			      }     
		      $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $num_mov = mysql_num_rows($res_mov);
		      ///===============================================
		      
		      
		      if ($num_mov > 0)  //La historia si tiene registrado el servicio actual o anterior
		         {
      		      $row_mov = mysql_fetch_array($res_mov);
      		  
      		      $wpatrones=explode(",",$row_mov[0]);
      		      
      		      //Vuelvo a recorrer el result set de las dietas y por cada uno busco si el paciente la tiene seleccionada   
	      		  mysql_data_seek($res_die,0);                       //coloco el puntero en el ** 1er registro ** del result set de dietas
      		        
	      		  $wj=1;  //Para saber en que patron quedo cuando salga del for
      		      for ($k=0;$k < count($wpatrones);$k++)
      		         {
	      		      for ($j=$wj;$j<=$num_die;$j++)                   //For por cada dieta o patron dentro del paciente
				         { 
					      $row_die = mysql_fetch_array($res_die); 
					      
					      if ($row_die[4]=="off")
					         {
						      echo "<td style='display:none'>";   
						      definir_div($row_die[0], $i, $whis[$i], $wing[$i], $wseraux);
						      echo "</td>";
					         }    
					        
					      if ($wpatrones[$k]==$row_die[0]) 
		      		         {
			      		      echo "<td bgcolor=".$wcolor." align=center><SPAN id='wcelda".$i."-".$j."' title='".$wdietas[$j]."'><INPUT TYPE='checkbox' id=patron$i-$j NAME='wcelda[".$i."][".$j."]' CHECKED ".$whabilitado." onClick='javascript:combina("."\"".$i."\"".","."\"".$j."\"".","."\"".$wpatrones[$k]."\"".","."\"".$wadi_ser."\"".");'></SPAN></td>";
				      		  $wj=$j+1;
				      		  
				      		  $j=$num_die;  //Con esto salgo del 1er for, pero sigo con el otro Patron si es que tiene mas el paciente
				      		 }
				            else
				              {
					           echo "<td align=center><SPAN id='wcelda".$i."-".$j."' title='".$wdietas[$j]."'><INPUT TYPE='checkbox' id=patron$i-$j NAME='wcelda[".$i."][".$j."]' UNCHECKED ".$whabilitado." onClick='javascript:combina("."\"".$i."\"".","."\"".$j."\"".","."\"".$row_die[0]."\"".","."\"".$wadi_ser."\"".");'></SPAN></td>";
					          }
				         }    
	      		     }
	      		     
	      		  //Luego de buscar los patrones que tiene el paciente en el 'resultset', pregunto si ya llego al final del 'resultset'
	      		  //si no, quiere decir que faltan por pintar las columnas correspondientes a esos patrones, entonces las pinto.
	      		  //$wj= Es el registro hasta donde llego la busqueda anterior.
	      		  
	      		  if ($wj<=$num_die)
	      		     {
		      		  for ($k=$wj;$k<=$num_die;$k++)                   
		      		     { 
					      $row_die = mysql_fetch_array($res_die);
					      
					      if ($row_die[4]=="off")
					         {
						      echo "<td style='display:none'>";   
						      definir_div($row_die[0], $i, $whis[$i], $wing[$i], $wseraux);    
						      echo "</td>";
					         } 
					      echo "<td align=center><SPAN id='wcelda".$i."-".$k."' title='".$wdietas[$k]."'><INPUT TYPE='checkbox' id=patron$i-$k NAME='wcelda[".$i."][".$k."]' UNCHECKED ".$whabilitado." onClick='javascript:combina("."\"".$i."\"".","."\"".$k."\"".","."\"".$row_die[0]."\"".","."\"".$wadi_ser."\"".");'></SPAN></td>";
					     }    
	      		     }
	      		        
  		         }
	            else
	              {
		           //Vuelvo a recorrer el result set de las dietas y por cada uno busco si el paciente la tiene seleccionada   
      		       mysql_data_seek($res_die,0);                   //coloco el puntero en el 1er registro del result set de dietas
			       for ($j=1;$j<=$num_die;$j++)                   //For por cada dieta o patron dentro del paciente
			         {   
				      $row_die = mysql_fetch_array($res_die);    
				      
				       if ($row_die[4]=="off")   //Que solo sea patron Unico, osea que no se combine con ningun otro
					      {
						   echo "<td style='display:none'>";   
				           definir_div($row_die[0], $i, $whis[$i], $wing[$i], $wseraux);
				           echo "</td>";
			              } 
				         
		              echo "<td align=center><SPAN id='wcelda".$i."-".$j."' title='".$wdietas[$j]."'><INPUT TYPE='checkbox' id=patron$i-$j NAME='wcelda[".$i."][".$j."]' UNCHECKED ".$whabilitado." onClick='javascript:combina("."\"".$i."\"".","."\"".$j."\"".","."\"".$row_die[0]."\"".","."\"".$wadi_ser."\"".");'></SPAN></td>";
		             } 
	              }      
		      
	          //OBSERVACIONES DEL PACIENTE    
	          //Busco si hay alguna observacion
		      $q =  " SELECT MAX(CONCAT(fecha_data,hora_data)),movobs "
		           ."   FROM ".$wbasedato."_000077 "
		           ."  WHERE movhis  = '".$whis[$i]."'"
		           ."    AND moving  = '".$wing[$i]."'"
		           //."    AND movhab  = '".$whab[$i]."'"
		           //."    AND movser  = '".$wser."'"
		           //."    AND movfec  = '".$wfec."'"
		           ."    AND movest  = 'on' "
		           ."    AND movobs != '' "
		           ."  GROUP BY 2 ";
		      $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      		  $num_mov = mysql_num_rows($res_mov);
      		  if ($num_mov > 0) 
      		    {
	      		 $row_mov = mysql_fetch_array($res_mov);    
      		     $wobs[$i]=trim($row_mov[1]); 
      		    }
      		   else 
      		      $wobs[$i]='';
		      		  
	      	  echo "<td align=center><textarea NAME=wobs[".$i."] ".$whabilitado.">".trim($wobs[$i])."</textarea></td>";
	      	  
	      	  
	      	  //INTOLERANCIAS
	      	  //Busco si hay alguna observacion
			  $q =  " SELECT MAX(CONCAT(fecha_data,hora_data)), movint "
		           ."   FROM ".$wbasedato."_000077 "
		           ."  WHERE movhis  = '".$whis[$i]."'"
		           //."    AND moving  = '".$wing[$i]."'"
		           ."    AND movest  = 'on' "
		           ."    AND movint != '' "
		           ."  GROUP BY 2 ";
		      $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      		  $num_mov = mysql_num_rows($res_mov);
      		  if ($num_mov > 0) 
      		    {
	      		 $row_mov = mysql_fetch_array($res_mov);    
      		     $wint[$i]=trim($row_mov[1]); 
      		    }
      		   else 
      		      $wint[$i]='';
	      	  
      		  echo "<td align=center><textarea NAME=wint[".$i."] ".$whabilitado.">".trim($wint[$i])."</textarea></td>";
	      		    
	      	  
	      	  //======================================================================================================
		      //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
		      $wafin=clienteMagenta($wdpa,$wtid,&$wtpa,&$wcolorpac);
		      if ($wafin)
			     echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			    else
			      echo "<td>&nbsp</td>";
			  //====================================================================================================== 
			  echo "</tr>";
			 }
		     
		     //Si se dio click sobre el de multiseleccion de alguna columna, lo considero aca luego de mostrar los registros como venian 
		     //antes de presionar el click sobre la columna
		     if (isset($wj) and trim($wj) != "")
                 {
	              for($i=1;$i<=$num_pac;$i++)
	                 {
		             echo "<script language='Javascript'>";
		             echo "if (document.dietas.wcelda[".$i."] && document.dietas.wcelda[".$i."][".$wj."]) ";
		             echo "   document.dietas.wcelda[".$i."][".$wj."].value='checked';";
		             echo "</script>";
		             }       
                 }	         
	     }
     } 
     
     
     
  //Traigo los costos de cada Patron
  function traer_costo_del_patron($wpatron, $wtipemp, $wedad_pac, &$res_cos, $wser, &$wcob, &$wsec, &$wcbi)
     {
	  global $wbasedato;
	  global $conex;
	  
	     
	  ///global $num_cos;
	     
	  //Averiguo si el patron es Unico
	  $q = " SELECT diecbi "
	      ."   FROM ".$wbasedato."_000041 "
	      ."  WHERE diecod = '".$wpatron."'"
	      ."    AND dieest = 'on' ";
	  $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num_cos = mysql_num_rows($res_cos); 
	  if ($num_cos > 0)
	    {
	     $row=mysql_fetch_array($res_cos); 
	     $wcbi=$row[0];
        }
	     
	  //Busco el costo para el tipo de empresa del paciente, Si no existe con este tipo de empresa lo
	  //busco con '*' y retorno el numero de filas encontadas
	  $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi "
	      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041 "
	      ."  WHERE cospat  = '".$wpatron."'"
	      ."    AND costem  = '".$wtipemp."'" 
	      ."    AND cosedi <= ".($wedad_pac*12)
	      ."    AND cosedf >  ".($wedad_pac*12)
	      ."    AND cosest  = 'on' "
	      ."    AND cosser  = '".$wser."'"
	      ."    AND cospat  = diecod ";
	  $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num_cos = mysql_num_rows($res_cos); 
	  if ($num_cos == 0)
	     {
		  $q = " SELECT cosact, cosfec, cosant, diecob, diesec, diecbi "
		      ."   FROM ".$wbasedato."_000079, ".$wbasedato."_000041 "
		      ."  WHERE cospat  = '".$wpatron."'"
		      ."    AND costem  = '*' " 
		      ."    AND cosedi <= ".($wedad_pac*12)
		      ."    AND cosedf >  ".($wedad_pac*12)
		      ."    AND cosest  = 'on' "
		      ."    AND cosser  = '".$wser."'"
		      ."    AND cospat  = diecod ";
		  $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num_cos = mysql_num_rows($res_cos);   
		 }
		 
	   return $num_cos;	 	 
     }      
       
     
  function grabar_encabezado($wser, $wcap, $wobs_enf)
     {
	  global $wbasedato;
	  global $conex;
	  global $wfecha;
	  global $whora;
	  global $wusuario;
	  global $wcco;
	  
	  if (trim($wcap) != "" or trim($wobs_enf) != "")
	     {
		  $q = " SELECT COUNT(*) "
		      ."   FROM ".$wbasedato."_000085 "
		      ."  WHERE encfec = '".$wfecha."'"
		      ."    AND enccco = '".$wcco."'"
		      ."    AND encser = '".$wser."'";
		  $res_enc = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		  $row_enc=mysql_fetch_array($res_enc);
		  
		  if ($row_enc[0] > 0)
		     {
			  $q = " UPDATE ".$wbasedato."_000085 "
			      ."    SET encobe = '".$wobs_enf."', "
			      ."        enccap = '".$wcap."'"
			      ."  WHERE encfec = '".$wfecha."'"
			      ."    AND enccco = '".$wcco."'"
			      ."    AND encser = '".$wser."'";
			  $resenc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
			 }    
		    else   
		       {
			    //Grabo la dieta de cada historia y por cada servicio
		        $q = " INSERT INTO ".$wbasedato."_000085 (   Medico       ,   Fecha_data,   Hora_data,   encfec    ,   enccco  ,   encser  ,   enccap  ,   encusu      ,  encobe       , encobc, Seguridad        ) "
			        ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wfecha."','".$wcco."','".$wser."','".$wcap."','".$wusuario."','".$wobs_enf."', ''    , 'C-".$wusuario."') ";
			    $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		       } 
		     
	     }    
	 }
     
         
     
  function grabar_detalle_patron_unico($i, $wpatron, $whis, $wing, $whab, $wser, $wpatron, $wopcion, $wvalopc)
     {
	  global $wbasedato;
	  global $conex;
	  global $wfecha;
	  global $whora;
	  global $wfec;
	  global $wusuario;
	  
	  //Grabo la dieta de cada historia y por cada servicio
      $q = " INSERT INTO ".$wbasedato."_000084 (   Medico       ,   Fecha_data,   Hora_data,   detfec  ,   dethis  ,   deting  ,   detser  ,   detpat     ,   detpro     ,  detcos    , detest, Seguridad        ) "
	      ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis."','".$wing."','".$wser."','".$wpatron."','".$wopcion."',".$wvalopc.", 'on'  , 'C-".$wusuario."') ";
	  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());   
	 }
	 
	 
     
  function costear_composicion_patron_unico($i, $wpatron, $wcontenido, $whis, $wing, $whab, $wser, &$wencontro_costo)
     {
	  global $wbasedato;
	  global $conex;
	  global $wfecha;
	  
	  
	  $wcontenido=substr($wcontenido,0,strlen($wcontenido)-1);
	  $wopciones=explode(",",$wcontenido);
	  
	  $wvalpat=0;
	  
	  //Por cada opcion que compone el Patrón busco el costo y lo grabo el detalle en la tabla 000084,
	  //a su vez acumulo el valor total del costo del patrón para grabarlo en la tabla 000077
	  for ($j=0; $j<count($wopciones);$j++)
	     {
		     
		  $wopcion=explode("_",$wopciones[$j]);
		     
		  $wvalopc=0;   
		     
	      $q = " SELECT provac, profec, provan "
		      ."   FROM ".$wbasedato."_000082 "
		      ."  WHERE prodes = '".$wopcion[0]."'"
		      ."    AND propat = '".$wpatron."'"
		      ."    AND proest = 'on' ";
		  $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num_cos = mysql_num_rows($res_cos);
		  
		  if ($num_cos > 0)
		     {
			  $row_cos=mysql_fetch_array($res_cos);
			  
			  if ($wvalopc == 0)
		         {
			      if ($wfecha >= $row_cos[1])
			         $wvalopc=$row_cos[0];            //Asigno el valor actual
			        else
			           $wvalopc=$row_cos[2];          //Asigno el valor anterior a la fecha de cambio 
		         }
		        else
		           {
			        //Con esto tambien obtengo el mayor valor de los patrones, cuando la historia tiene mas de un patron   
			        if ($wfecha >= $row_cos[1])       //Fecha actual es mayor a fecha de cambio
			           {
			            if ($wvalopc < $row[0])       //wvalpat es Menor a valor actual, asigno entonces el valor actual a $wvalpat
				           $wvalopc=$row_cos[0];      //Asigno el valor actual
			           }    
				      else
				         if ($wvalopc < $row_cos[2])  //Indica que fecha de cambio es mayor a la actual, pregunto si $wvalpat es manor a valor anterior
				            $wvalopc=$row_cos[2];     //Asigno el valor anterior a la fecha de cambio    
			       }
			       
			   //Busco si la historia para este patron ya tiene grabado o no la opcion, para no volver a grabar
			   $q = " SELECT COUNT(*) "
				     ."   FROM ".$wbasedato."_000084 "
				     ."  WHERE detfec = '".$wfecha."'"
				     ."    AND dethis = '".$whis."'"
				     ."    AND deting = '".$wing."'"
				     ."    AND detser = '".$wser."'"
				     ."    AND detpat = '".$wpatron."'"
				     ."    AND detpro = '".$wopcion[0]."'"
				     ."    AND detest = 'on' ";
			   $res_opc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		       $wexiste_opc = mysql_fetch_array($res_opc);
			   
		       if ($wexiste_opc[0] == 0)
			      grabar_detalle_patron_unico($i, $wpatron, $whis, $wing, $whab, $wser, $wpatron, $wopcion[0], $wvalopc);
			       
			   $wvalpat = $wvalpat + $wvalopc; 
			   
			   $wencontro_costo="on";
		     }
		    else
		       {
			    echo "<script language='Javascript'>";
	            echo "alert ('El producto: ** ".$wopciones[$i]." ** No tiene costo en la tabla 000082, para el Patrón: ** ".$wpatron." ** en la historia: ** ".$whis." **');"; 
	            echo "</script>";
			   }
	     }
	  return $wvalpat;                
	 }       
  //==================================================================================================================
  //==================================================================================================================      
  //==================================================================================================================
  //==================================================================================================================
  
  
  
  //==================================================================================================================	 
  //==================================================================================================================	 
  //************************************* P R O G R A M A    P R I N C I P A L ***************************************
  //==================================================================================================================	 
  //==================================================================================================================	
  
  global $whabilitado;
  global $wusuario;
  global $wadi_ser;
  
  //On
  //echo "sel1si: ".$sel1SI."<br>";
  //echo "sel2si: ".$sel2SI."<br>";
  //echo "sel3si: ".$sel3SI."<br>";
  //echo "sel4si: ".$sel4SI."<br>";
  //echo "sel5si: ".$sel5SI."<br>";
  
  //FORMA ================================================================
  echo "<form name='dietas' action='dietas.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  
  
  echo "<center><table>";
  
        
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

     
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
  if (!isset($wcco) or trim($wcco) == "" or !isset($wser) or $wser=="" or !isset($wfec) or $wfec=="")
     {     
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND ccohos  = 'on' "
          //."    AND ccoing != 'on' "
          ."    AND ccocir != 'on' ";
          ///."    AND ccourg != 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
			 	 
	    
	  echo "<tr><td align=right class=fila1><b>SELECCIONE LA UNIDAD EN LA QUE SE ENCUENTRA: </b></td>";
	  echo "<td align=center class=fila2><select name='wcco'>";
	  echo "<option>&nbsp</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
          
      
      //===============================================================================================================================
      //traigo los registros del Maestro de Servicios
      //===============================================================================================================================
      $q = " SELECT sernom "
	      ."   FROM ".$wbasedato."_000076 "
	      ."  WHERE serest = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
      
      
      echo "<tr><td class=fila1 align=right><b>SELECCIONE EL SERVICIO DE ALIMENTACION QUE VA A SOLICITAR: </b></td>";
	  echo "<td align=center class=fila2><select name='wser'>";
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]."</option>";
	     }
      echo "</select></td></tr>";
      //===============================================================================================================================
      
      echo "<tr>";
      echo "<td align=right  class=fila1><b>Fecha a Registrar: </b></td>";
      echo "<td align=center class=fila2>";
      campofecha("wfec");
      echo "</td>";
      echo "</tr>";
        
	  echo "<center><tr><td align=center colspan=4 bgcolor=cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else  //Esta setiado CCO, SER y Fecha
      { 
	   if (strpos($wcco,"-") > 0)
	      { 
	       $wccosto=explode("-",$wcco);
	       $wcco=$wccosto[0];
          }
         else
           {
            if (strpos($wcco,".") > 0)  
	   	       { 
		        $wccosto=explode(".",$wcco);
		        $wcco=$wccosto[1];
	           }
	       }	      
	       
	   $q = " SELECT cconom "
	       ."   FROM ".$wtabcco
	       ."  WHERE ccocod = '".$wcco."'";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);
	   $wnomcco=$row[0];   
	      
	   
	   if (trim($wnomcco)!="")  //Si hay Cco valido
	     {
		  //====================================================================================================================
		  //*************** G R A B A R ***************
		  //====================================================================================================================
		  if (isset($wgrabar) and $wgrabar=="on")
		     {
			  //Recorro todas las HISTORIAS o PACIENTES
			  for ($i=1;$i<=$num_pac;$i++)
		         {
			      //Creo esta variable para indicar cuando ya no es necesario seguir buscando el costo. Esto se da cuando un patron ha sido
				  //parametrizado como 'diesec=on', esto indica que es el que se cobra, por esto debo indicar que no siga buscando costo
				  //porque el paciente puede tener otros patrones.
				  $wbusca_costo    = "on";     //Por defecto debe buscar el costo.  
				  $wencontro_costo = "on";     //Indica si se le hallo el costo al patron o No, por derfecto indica que SI (on). 
				  $waccion         = "off";    //indica que transaccion se va a hacer, si esta en 'off' no debe hacer ninguna transacción. 
			         
			         
			      //Traigo las DIETAS o PATRONES  para el servicio 
			      $q = " SELECT diecod, dieord "
				      ."   FROM ".$wbasedato."_000041, ".$wbasedato."_000076 "
				      ."  WHERE dieest = 'on' "
				      ."    AND dieord > 0 "
				      ."    AND dieesq = seresq "
				      ."    AND sernom = '".$wser."'"
				      ."  ORDER BY 2 ";   
			      $res_die = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
			      $num_die = mysql_num_rows($res_die); 
			      
			      if ($num_die > 0)
			         {
				      $wpatron="";
			          $wvalpat=0;   //Valor del patron, se asigna el mayor valor cuando tiene mas de un patron   
				         
			          $wel_patron_se_combina="on";  //quiere decir que por defecto todos se combinan
			          
			          if (!isset($wcap))
			             $wcap=0;
			          
			          grabar_encabezado($wser, $wcap, $wobserv_enfer); 
			          
			          //Recorro cada una de las DIETAS o PATRONES     
			          for ($j=1;$j<=$num_die;$j++)
			             {
				          $row = mysql_fetch_array($res_die);  //Dietas
				          
				          if (isset($wcelda[$i][$j]))          //Solo si la celda esta chuliada
				             {
					          if ($wpatron=="")
						         $wpatron=$row[0];
						        else  
						           $wpatron=$wpatron.",".$row[0];
						      
						      //                              Patron   Tipo Emp   Edad        Query      Servicio  Cobro Obligado  Solo se cobra este  Se puede combinar
						      $num_cos=traer_costo_del_patron($row[0], $wtem[$i], $wedad[$i], &$res_cos, $wser   , &$wcob        , &$wsec            , &$wcbi);
				              
				              if ($num_cos > 0)
				                 { 
					              $row_cos = mysql_fetch_array($res_cos);    
					                 
					              $wcob=$row_cos[3];                      //Indica costo obligatorio y/o adicional cuando se combina con otros		 
							      $wsec=$row_cos[4];                      //Indica que se cobro este.
								  $wcbi=$row_cos[5];                      //Indica si es combinable o no con otros patrones en horario de Pedido norma.
					              
					              //Pregunto si debe buscar el costo
					              if ($wbusca_costo=="on")
					                 {
							          //===================================================================================================================================     
								      //Busco el valor del patron
								      //El paciente puede tener mas de un patron, pero solo se le asigna el valor del mayor valor de los contenidos en el patron
								      //===================================================================================================================================
								      if ($wvalpat == 0)
								         {
									      if ($wfecha >= $row_cos[1])
									         $wvalpat=$row_cos[0];            //Asigno el valor actual
									        else
									           $wvalpat=$row_cos[2];          //Asigno el valor anterior a la fecha de cambio 
								         }
								        else
								           {
									        if ($wcob == "on")   
									           {
										        //Con esto tambien obtengo el mayor valor de los patrones, cuando la historia tiene mas de un patron   
										        if ($wfecha >= $row_cos[1])       //Fecha actual es mayor a fecha de cambio
										           {
											        $wvalpat=$wvalpat + $row_cos[0];      //Asigno el valor actual
										           }    
											      else
											         $wvalpat=$wvalpat + $row_cos[2];     //Asigno el valor anterior a la fecha de cambio
										       }
										      else
										         {
											      //Con esto tambien obtengo el mayor valor de los patrones, cuando la historia tiene mas de un patron   
										          if ($wfecha >= $row_cos[1])       //Fecha actual es mayor a fecha de cambio
										             {
											          if ($wvalpat < $row_cos[0])       //wvalpat es Menor a valor actual, asigno entonces el valor actual a $wvalpat
											             $wvalpat=$row_cos[0];      //Asigno el valor actual
										             }    
											        else
											           if ($wvalpat < $row_cos[2])  //Indica que fecha de cambio es mayor a la actual, pregunto si $wvalpat es manor a valor anterior
											              $wvalpat=$row_cos[2];     //Asigno el valor anterior a la fecha de cambio   
										         }             
									       }
									 }
							      
							      if ($wsec == "on")                      //Se cobra solo este patron, asi otros tengan un mayor o menor valor
								     $wbusca_costo="off";                 //No debe seguir buscando el costo, si es que el paciente tiene mas patrones
								     
								  if ($wcbi != "on")
								     {
								      $wel_patron_se_combina="off";       //No se puede combinar
								      $wpat_cbi = $row[0];                //Guardo el codigo del patron para luego compararlo con el $wpatron, si son <> no se grabar 
								     } 
								  //===================================================================================================================================            
							     }
							    else
							       {
								    if ($wcbi != "off")  //Indica que si es combinable con otro patron pero que no encontro costo 
								       {
									    echo "<script language='Javascript'>";
					                    echo "alert ('El Patrón: ** ".$row[0]." ** No tiene costo en la tabla 000079, para el tipo de empresa: ** ".$wtem[$i]." ** en la historia: ** ".$whis[$i]." **');"; 
				                        echo "</script>";
				                        			   
				                        $wencontro_costo="off";
			                           }
			                          else  //Indica que es Unico 
			                             {
			                              $wel_patron_se_combina="off";       //No se puede combinar                      
			                              $wpat_cbi = $row[0];
		                                 }
		                           }    
			                 }
						 }
						 
				      if (($wel_patron_se_combina=="off" and trim($wpat_cbi) != trim($wpatron)) and $wadi_ser=="off")
					     {
						  echo "<script language='Javascript'>";
	                      echo "alert ('El Patrón: ** ".$row[0]." ** No se puede combinar con ningún otro');"; 
                          echo "</script>";   
						     
					      break;
				         }
				        else
				           if ($wel_patron_se_combina=="off")  //Si entra por aca es porque se eligio un patron unico y debe de estar detallado su contenido
				              {
					           //$wvar="sel".$i.$wpatron;
					           $wvar="sel".$i.$wpat_cbi;
					           
					           //==========================================================================
					           //** Costear ** cada una de las opciones del PATRON UNICO y el total
					           //==========================================================================
					           
					           if (isset($$wvar) and trim($$wvar != ""))
					              {
						           $wvalpat = $wvalpat+costear_composicion_patron_unico($i, $wpat_cbi, $$wvar, $whis[$i], $wing[$i], $whab[$i], $wser, &$wencontro_costo);
					              } 
					           $wecontro_costo="on";
				              } 
						 
				  	  //Busco si la historia ya tenia el servicio grabado y de acuerdo a la dieta encontrada o no determino si la accion
				      //es PEDIDO, MODIFICACION, ADICION o CANCELACION
				      if ($wencontro_costo=="on")
				         {
					      $waccion = accion_a_grabar($whis[$i], $wing[$i], $whab[$i], $wser, $wpatron, &$westado, trim($wobs[$i]), trim($wint[$i]));
					     }
					            
			          
			          //Si la accion == off quiere decir que no se hizo ningun cambio sobre el patron por lo tanto no grabo nada
			          if ($waccion != "off" and trim($waccion) != "")
			             {
				          //Grabo la Auditoria
						  $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis      ,   auding      ,   audser  ,   audacc     ,   audusu      ,     Seguridad   ) "
						      ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis[$i]."','".$wing[$i]."','".$wser."','".$waccion."','".$wusuario."','C-".$wusuario."') ";
						  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						  //=======================================================================================================================================================   
						  
						  //Borro lo que tenia registrado la historia en el servicio, habitacion y fecha
						  $q = " DELETE FROM ".$wbasedato."_000077 "
						      ."  WHERE movfec = '".$wfecha."'"
						      ."    AND movhis = '".$whis[$i]."'"
						      ."    AND moving = '".$wing[$i]."'"
						      ."    AND movhab = '".$whab[$i]."'"
						      ."    AND movser = '".$wser."'";
						  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());    
						      
						  
						  if ($waccion=="CANCELADO")  //Grabo el registro pero con estado 'off'
						     $westado="off";
						         
						  //Grabo la dieta de cada historia y por cada servicio
					      $q = " INSERT INTO ".$wbasedato."_000077 (   Medico       ,   Fecha_data,   Hora_data,   movfec  ,   movhis      ,   moving      ,   movhab      ,   movser  ,   movdie     , movind,   movobs            ,   movest     , movobc,  movval    ,   movint            , Seguridad       ) "
						      ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wfec."','".$whis[$i]."','".$wing[$i]."','".$whab[$i]."','".$wser."','".$wpatron."', 'N'   ,'".trim($wobs[$i])."','".$westado."', ''    ,".$wvalpat.",'".trim($wint[$i])."', 'C-".$wusuario."') ";
						   $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						  //=======================================================================================================================================================   
					     }
					 }
		         }       
			 }  
           
	         
	       echo "<tr class=titulo>";
		   echo "<td colspan=23 align=center><b>Servicio o Unidad: ".$wnomcco."</b></td>";
		   echo "</tr>";
		  
		   echo "<tr class=titulo>";
		   echo "<td colspan=23 align=center><b>Fecha de Registro: ".$wfec."</b></td>";
		   echo "</tr>";  
             
           //traigo los registros del Maestro de Servicios
	       $q = " SELECT sernom "
		       ."   FROM ".$wbasedato."_000076 "
		       ."  WHERE serest = 'on' ";
		   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	       $num = mysql_num_rows($res);
	      
	       echo "<tr class=seccion1><td colspan=3>&nbsp</td></tr>";
	       echo "<tr class=seccion1><td align=center><b>SELECCIONE EL SERVICIO DE ALIMENTACION QUE VA A SOLICITAR: </b></td>";
		   echo "<td align=center><SELECT name='wser' onchange='enter()'>";
		   if (isset($wser))
		      echo "<OPTION SELECTED>".$wser."</OPTION>";    
		   for ($i=1;$i<=$num;$i++)
		      {
		       $row = mysql_fetch_array($res); 
		       echo "<OPTION>".$row[0]."</OPTION>";
		      }
	       echo "</SELECT></td>";
	       
	       validaciones('', '', '', $wser, 'Consulta');
	       echo "<td align=center bgcolor=#cccccc><input type='checkbox' name=wgrabar ".$whabilitado."><input type='submit' value='Grabar'></td>";      
	       echo "</tr>";
	       
		   echo "<tr class=seccion1><td colspan=3>&nbsp</td></tr>";   
		   echo "<tr>&nbsp</tr>";
		   
		   echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		   
		   echo "</table>";
		   
		   if (isset($wser))
		      {
			   $q = " SELECT serhin, serhfi, serhmo, serhad, serhca, serhdi, serexp "
			       ."   FROM ".$wbasedato."_000076 "
			       ."  WHERE serest = 'on' "
			       ."    AND sernom = '".$wser."'";
		       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	           $num = mysql_num_rows($res);
	           
	           if ($num>0)
	              {
		           $row = mysql_fetch_array($res);
		           
		           echo "<table>";
		           echo "<tr class=fila1>";
		           
		           echo "<td><b>Hora Inicio Pedido: </b></td><td>".$row[0]."</td>";
		           echo "<td><b>Hora Final Pedido: </b></td><td>".$row[1]."</td>";
		           echo "<td><b>Hora Máxima Modificación: </b></td><td>".$row[2]."</td>";
		           
		           echo "<td rowspan=2>";
			       //=============================================================================================================
			       //C O N V E N C I O N E S
			       //=============================================================================================================
			       echo "<table border=1 align=center class=fila1>";        
			   	   echo "<caption class=fila2><b>Convenciones</b></caption>";
			       echo "<tr class=fila1><td align=center bgcolor=cccccc><font size=2>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp ESQUEMA ANTERIOR (Sin Grabar) &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</font></td></tr>";         
			       echo "<tr class=fila2><td align=center bgcolor=yellow><font size=2>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp ESQUEMA ACTUAL (Grabado) &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</font></td></tr>";
			       echo "<tr class=fondoAmarillo><td align=center><font size=2>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp EN PROCESO DE ALTA &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</font></td></tr>";
			       echo "<tr class=fondoAmarillo><td align=center bgcolor='3299CC'><font size=2><B>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp PENDIENTE DE RECIBIR &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</B></font></td></tr>";
			       echo "</table>";
		           echo "</td>";
		           echo "</tr>";
		            
		           echo "<tr class=fila2>";
		           echo "<td><b>Hora Máxima Adición: </b></td><td>".$row[3]."</td>";
		           echo "<td><b>Hora Máxima Distribución: </b></td><td>".$row[5]."</td>";
		           echo "<td><b>Hora Máxima Cancelación: </b></td><td>".$row[4]."</td>";
		           echo "</tr>"; 
		           	           
		           echo "<tr class=fila1>";
			       echo "<td colspan=7>".$row[6]."</td>";   //Texto de la Explicacion
			       
			       echo "</table>";
	              }       
		      }    
		   
		   //On   
		   //======================================   
		   /*
		   //Mensaje que aparece cuando se graba
			echo "<div id='msjEspere' style='display:none;'>"; 
		    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento..."; 
			echo "</div>";
	   
		   echo "<table>";
		   echo "<tr>";
		   echo "<td align=center bgcolor=#cccccc><input type='button' name=wprueba onClick='javascript:grabar();'></td>";
		   echo "</tr>";
		   echo "</table>";
		   //======================================
		   */
		      
		   echo "<table>";   
		   mostrar(); 
		   echo "</table>";
		   
		   echo "<table>";
		   echo "<tr>";
		   echo "<td align=center bgcolor=#cccccc><input type='checkbox' name=wgrabar ".$whabilitado."><input type='submit' value='Grabar'></td>";
		   echo "</tr>";
		   echo "</table>";
		   
		   echo "<meta http-equiv='refresh' content='600;url=dietas.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wcco=".$wcco."&wser=".$wser."&wfec=".$wfec."'>";   		    
	     } //if $wnomcco
	    else
	      {   
           ?>	    
	         <script>
		       alert ("EL CENTRO DE COSTO NO FUE INGRESADO POR CODIGO DE BARRAS");     
		     </script>
		   <?php 
		  } 
	  echo "</table>"; 
		  
	  unset($wccohos);   //La destruyo para que el vuelva a entrar al if inicial, donde esta el if de los 'or'
	   
	  echo "<br>";
            
      $wini="N";
        
      echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
      echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
      
      echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
      echo "<input type='HIDDEN' name='wfec' value='".$wfec."'>";
      
      echo "<tr>";  
      echo "<td align=center colspan=7><A href='dietas.php?wtabcco=".$wtabcco."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."'><b>Retornar</b></A></td>"; 
      echo "</tr>";
     }  //if cco, ser y fecha
 }
echo "</form>";
  
echo "<br>";
echo "<table>"; 
echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
echo "</table>";
    
 // if de register



include_once("free.php");

?>