<head>
  <title>VENTAS AL PUBLICO </title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.ventas.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     } 
</script>

<script type="text/javascript">
	function enter1()
	{
	   document.forms.ventas.submit();
	   alert ("Pulse de nuevo la tecla ENTER");
	}
</script>

<script type="text/javascript">
	function alerta()
	{
	   alert ("!!!! ATENCION !!!! ***** ESTA GRABANDO UNA FACTURA MANUAL *****");
	}
</script>


<?php
include_once("conex.php");
  /***************************************************
   *     PROGRAMA PARA LA GRABACION DE LAS VENTAS    *
   *                  DE FARMASTORE                  *
   ***************************************************/
   
//==================================================================================================================================
//PROGRAMA                   : ventas.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Abril 28 de 2005
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Versión Febrero 16 de 2009)"; 
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Este programa se hace con el objetivo de registrar las ventas de la empresa FARMASTORE, en donde se pueda luego realizar una       \\
//     facturación individual o por empresa y además de tener en cuenta que luego de poder facturar se generen los RIPS, además este      \\
//     programa tiene en cuenta la actualización del Inventario en línea, grabando también el movimiento de consumo en el inventario,     \\
//     El programa en general, tiene en cuenta el tipo de cliente, el responsable de la cuenta, las tarifas de los articulos según la     \\
//     empresa y el centro de costo (sucursal). tambien se tiene en cuenta que si la venta es para un particular o el paciente de         \\
//     empresa tiene que pagar salga una ventana en donde se le pide registrar un recibo de caja por el valor pagado.                     \\
//========================================================================================================================================\\
//========================================================================================================================================\\


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//F E B R E R O   16   DE 2009:                                                                                                           \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se puedan registrar o identificar ventas realizadas por internet, se agrego en el drop down de tipo de \\
//venta la palabra internet.                                                                                                              \\                                                                                                             
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//E N E R O   14   DE 2009:                                                                                                               \\
//________________________________________________________________________________________________________________________________________\\
//Se adicionan los campos de diagnostico y rango al que pertenece un usuario, para los casos en los que la empresa exija esos datos.      \\
//esta modifcacion se hace inicialmente para el contrato de FARMASTORE CON COLSUBSIDIO-SUSALUD. Estos campos solo se piden al momento de  \\
//la venta si asi esta configurado en la empresa correspondiente (tabla 000024).                                                          \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//M A Y O   5   DE 2006:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Cuando se hacian ventas a empleados se verificaba el cupo en nomina con X empleado y luego al ser aprobado el cupo se modificaba el     \\
//empleado, lo que hacia que el prestamo quedase con un empleado y la factura con otro, esto se modifico, para que verifique si se        \\                                                                                                             
//modifico el empleado o se cambio el valor de la venta, si alguno de los casos ocurre, el programa exigira verificar de nuevo el cupo.   \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A B R I L  18 DE 2006:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que tenga en cuenta los descuentos por tipo de cliente (tabla 000042) en el cual se especifica que lineas  \\
//tiene descuento, es decir, dependiendo del tipo de cliente y la o las lineas de los productos que se esten comprando se calcula el      \\                                                                                                             
//descuento que este configurado en la tabla de tipos de clientes (000042). Para esto se creo el campo de lineas (clelin) en la tabla     \\
//"000042" y el campo (temlpa) en la tabla "000034" y se creo tambien la variable de trabajo '$wlinpac'.                                  \\
//Esta modificacion quedo registrado en el requerimiento # 18.                                                                            \\
//                                                                                                                                        \\     
//________________________________________________________________________________________________________________________________________\\
//F E B R E R O  6 DE 2006:                                                                                                               \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se puedan facturar servicios que no muevan inventarios, para esto se modifica la tabla de grupos (     \\  
//farstore_00004 ) a la cual se le adiciono el campo gruinv, que indica en forma boleana si los articulos que pertenezcan a este grupo    \\
//afectan o no el inventario.                                                                                                             \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  25 DE 2005:                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa porque el 24 de Octubre se realizo la misma venta por dos pantallas diferentes en la aguacatala y el programa   \\  
//dejo realizarlas, por lo que se coloca el control que si no existe cantidad disponible en alguno de los articulos a vender no se realice\\
//la venta. Este control se hizo en el programa Grabar_venta.php. Con esto se evita que se vuelvan a generar negativos.                   \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  3 DE 2005:                                                                                                               \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que tome la nueva estructura de numeracion de facturas, recibos y notas, en donde se coloco numeracion     \\
//inicial y final para cada centro de costo asi como prefijo para las facturas; también se crearon las tablas Maestro de Bonos y Relacion \\
//Bonos X Linea, en donde se podra definir de acuerdo a un bono que lineas tienen descuento indicacndo cuando comienza y termina la       \\
//promoción.                                                                                                                              \\
//Se crea el campo 'empres' en el maestro de Empresas, para indicar de una empresa, que empresa se hace responsable, por ejemplo para los \\
//empleados de Promotora, cada empleado se crea como una empresa y a su vez la empresa responsable sera la clinica, para los empleados de \\
//Patologia la empresa responsable debe ser Patologia Las Americas S.A.                                                                   \\
//Tambien se modifico para que se generara factura cuando el campo 'empfac' este en 'on' en el maestro de Empresas.                       \\
//Se crea el campo 'temche' en el Maestro Tipos de Empresa,Con este campo se modifica el programa para que cuando el tipo de empresa tenga\\ 
//en 'on' este campo haga la verificación de pago por nomina,esto se utilizahasta el momento solo para empleados de las empresas de       \\
//Promotora.                                                                                                                              \\
//Se modifico el diseño de impresión de las Factura mostrando todos los valores con IVA, el descuento tambien lo muestra con el IVA       \\
//incluido, luego se muestra el resumen de IVA separando los valores de compra con IVA de los que no tienen.                              \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//S E P T I E M B R E  26 DE 2005:                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica la presentacion de la pantalla de ventas, adicionanle las columnas de % descuento y descuento total por articulo, asi como  \\
//las columnas de valor venta con IVA y SIN IVA. Tambien se modifico el calculo de la base del iva para que solo tome el valor sin IVA de \\
//los articulos que tienen IVA menos el descuento dado al articulo, si lo tiene. Se adiciona la suma total de los descuentos.             \\
//Para los descuento se definio una sola variable                                                                                         \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//S E P T I E M B R E  21 DE 2005:                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se puedan realizar descuentos dependiendo de la forma de pago y la linea del producto, por ejemplo:    \\
//Se le haran descuentos a los clientes que paguen con Bonos XX y lleve productos de la linea de improtados tendran un 10% de descuento   \\
//en esos productos.                                                                                                                      \\
//Para lograr hacer esto se creo la tabla Relacion formas de pago-lineas farstore_000047, en la cual se debe especificar la forma de pago,\\
//la linea a la cual se le va a hacer el descuento y la sublinea (opcional o si no todas), esto configiracion tiene un rango de fechas    \\
//de vigencia asi como un horario de aplicacion.                                                                                          \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A G O S T O  10 DE 2005:                                                                                                                \\
//________________________________________________________________________________________________________________________________________\\
//Se crea la table de tipos de clientes especiales farstore_000042, en la cual se pueden especificar descuentos dentro de un rango de     \\
//fechas. Esta tabla esta ligada con la tabla de clientes farstore_000041, esta tabla se va grabando automaticamente a medida que se      \\
//realizan las ventas y a su vez toman la información de los clientes, todos los clientes que se registran desde la venta quedan con      \\
//el tipo de cliente GENERAL, para cambiar este tipo se tendra que ir directamente a la tabla de clientes.                                \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A G O S T O  3 DE 2005:                                                                                                                 \\
//________________________________________________________________________________________________________________________________________\\
//SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO                                           \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\         

session_start();

if (!isset($user))
	{
	 if(!isset($_SESSION['user']))
		session_register("user");
	}

if(!isset($_SESSION['user']))
	echo "error";
else
{	   
  session_register("wpagook");	 
  session_register("wprestamo");
	      
  

  //

  //		      or die("No se ralizo Conexion");
  

 
  //$conexunix = odbc_pconnect('facturacion','infadm','1201')
  //					    or die("No se ralizo Conexion con el Unix");
  					    

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user)); 
  
  $wactualiz="(Versión Diciembre 21 de 2005)";                     // Aca se coloca la ultima fecha de actualizacion de este programa \\
	                                                               // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= \\
	                                                           
  $wfecha=date("Y-m-d");   
  $hora = (string)date("H:i:s");	              
    
  echo "<form name='ventas' action='Ventas.php' method=post>";
  
  echo "<input type='HIDDEN' name='wini' value='".$wini."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  if (isset($wmedi)) echo "<input type='HIDDEN' name='wmedi' value='".$wmedi."'>";
  if (isset($wprog)) echo "<input type='HIDDEN' name='wprog' value='".$wprog."'>";
  if (isset($wremi)) echo "<input type='HIDDEN' name='wremi' value='".$wremi."'>";
  
  
  //$wpuntos="N";
  
  if ($wini == "S")  //'S' Indica que se esta iniciando una venta
     {
      $wfecha_tempo=$wfecha;
      $whora_tempo=$hora;
      $wpagook=0;           //Para indicar si la venta se hace con descuento por Nomina o NO   0:No 1:Si
      $wprestamo=0;
      $wchequeo="off";
      //$whabilita_venta="ENABLED";
      $whabilita_venta="";
      
      $wpuntos="N";
      
      
      //include_once("/pos/cierre.php");    //Se hace el cierre en la primera venta del mes siguiente
      
      $wfecha_bor=date("Y-m-d");   
	      
      //=============================================================================
	  //BORRO LOS REGISTROS DE LA TABLA DE VENTAS TEMPORALES
	  //=============================================================================
	  $q = "  DELETE FROM ".$wbasedato."_000034 "
	      ."   WHERE temfec <= str_to_date(ADDDATE('".$wfecha_bor."',-2),'%Y-%m-%d')";
	  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
	  //=============================================================================
      
      
      //Esto lo hago para indicar que la venta anterior ya termino, entonces inicializo las siguientes variables
      if (isset($wterm_vta) and ($wterm_vta=="S"))
         {
	      unset($wcarpun);
		  unset($wtipcli);
		  unset($wempresa);
		  unset($wdocpac);
		  unset($wnompac);
		  unset($wte1pac);
		  unset($wdirpac);
		  unset($wmaipac);
		  unset($wcuotamod);
		  unset($wtipven);
		  unset($wtipfac);
		  unset($wmensajero);
		  unset($wdesemp);
		  unset($wdesart);
		  unset($wrecemp);
		  unset($wtotdes);
		  unset($wtotrec);
		  unset($wbondto);
		 } 
	 }
    else
      {
       echo "<input type='HIDDEN' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";   
	   echo "<input type='HIDDEN' name= 'whora_tempo' value='".$whora_tempo."'>"; 
	  } 
  
  echo "<input type='HIDDEN' name='wpagook' value='".$wpagook."'>";	
  echo "<input type='HIDDEN' name='whabilita_venta' value='".$whabilita_venta."'>";  
	  
  //ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
  $q =  " SELECT cjecco, cjecaj, cjetin, cjetem "
       ."   FROM ".$wbasedato."_000030 "
       ."  WHERE cjeusu = '".$wusuario."'"
       ."    AND cjeest = 'on' ";
       
  $res = mysql_query($q,$conex);
  $num = mysql_num_rows($res);
  if ($num > 0)
     {
      $row = mysql_fetch_array($res);
      
      $pos = strpos($row[0],"-");
      $wcco = substr($row[0],0,$pos);
      $wnomcco = substr($row[0],$pos+1,strlen($row[0])); 
      
      $pos = strpos($row[1],"-");
      $wcaja = substr($row[1],0,$pos);
      $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));
      
      $wtiping = $row[2];
      if (!isset($wtipcli)) $wtipcli = $row[3];
     }
    else
       echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR";
     
  $wcol=6;  //Numero de columnas que se tienen o se muestran en pantalla   
  
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  function bisiesto($year)
	{
     return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
	}


  function validar_fecha($dato)
	{
     $fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	 if(ereg($fecha,$dato,$occur))
	   {
	    if($occur[2] < 0 or $occur[2] > 12)
	      return false;
	    if(($occur[3] < 0   or  $occur[3] > 31) or 
	       ($occur[2] == 4  and $occur[3] > 30) or 
	       ($occur[2] == 6  and $occur[3] > 30) or 
		   ($occur[2] == 9  and $occur[3] > 30) or 
		   ($occur[2] == 11 and $occur[3] > 30) or 
		   ($occur[2] == 2  and $occur[3] > 29 and bisiesto($occur[1])) or 
		   ($occur[2] == 2  and $occur[3] > 28 and !bisiesto($occur[1])))
		    return false;
		 return true;
	   }
	  else
	     return false;
	}          
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  
  
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA VERIFICAR QUE TODOS LOS CAMPOS ESTEN DIGITADOS Y DILIGENCIADOS CORRECTAMENTE
  function verifica_datos()
      {
	   global $wmedi;
	   global $wcodmed;    
	   global $wnommed;
	   global $wprog;
	   global $wprograma;
	   global $wremi;
	   global $wcodrem;
	   global $wnomrem;
	   global $wdiag;
	   global $wran;
	   global $wdiagnostico;
	   global $wrango;
	   global $wtipdes;
	   global $wtde;
	   
	   global $wrips;
	   global $wtipusu;
	   global $wtipdto; 
	   global $wmuni;
	   global $wzona;
	   global $wsexo;
	   global $wpoliza;
	   global $wauto;
	   
	   global $wtipven;
	   global $wtipcli;
	   global $wempresa;
	   global $wte1pac;
	   global $wnompac;
	   global $wtipfac;
	   global $wdocpac;
	   global $wbondto;
	   
	   global $whabilita_venta;
	   
	   global $wvaldat;
	   global $wfecven;
	   global $wedad;
	      
	    
	   $whabilita_venta="ENABLED";
	    
	   if ($wmedi == "on")
	      {
		   if (!isset($wcodmed) or trim($wcodmed) == "" or trim($wcodmed) == "NO APLICA")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Codigo Médico"; }
	       if (!isset($wnommed) or trim($wnommed) == "" or trim($wnommed) == "NO APLICA")
	          { $whabilita_venta="DISABLED";  
	            $wvaldat="Nombre Médico"; }
	      }   
	    
	   if ($wprog == "on")
	      if (!isset($wprograma) or trim($wprograma) == "" or trim($wprograma) == " - NO APLICA")
	         { $whabilita_venta="DISABLED";
	           $wvaldat="prog"; }
	         
	   if ($wremi == "on")
	      {
	       if (!isset($wcodrem) or trim($wcodrem) == "" or trim($wcodrem) == "NO APLICA")
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Codigo Médico Remitente"; }
	       if (!isset($wnomrem) or trim($wnomrem) == "" or trim($wnomrem) == "NO APLICA")
	          { $whabilita_venta="DISABLED";     
	            $wvaldat="Nombre Médico Remitente"; }
	      }  
	      
	   if ($wdiag == "on")
	      if (!isset($wdiagnostico) or trim($wdiagnostico) == "" or trim($wdiagnostico) == " - NO APLICA")
	         { $whabilita_venta="DISABLED";
	           $wvaldat="Diagnostico"; }
	           
	   if ($wran == "on")
	      if (!isset($wrango) or trim($wrango) == "" or trim($wrango) == " - NO APLICA")
	         { $whabilita_venta="DISABLED";
	           $wvaldat="Rango"; } 
	           
	   if ($wtde == "on")
	      if (!isset($wtipdes) or trim($wtipdes) == "" or trim($wtipdes) == " - NO APLICA")
	         { $whabilita_venta="DISABLED";
	           $wvaldat="Tipo de Despacho"; }            
	       
	   if ($wrips == "on")
	      {
		   if (!isset($wte1pac) or trim($wte1pac) == "SIN DATO") 
		      { $whabilita_venta="DISABLED";
		        $wvaldat="Telefono"; }
		   if (!isset($wnompac) or trim($wnompac) == "CLIENTE PARTICULAR") 
		      { $whabilita_venta="DISABLED";   
		        $wvaldat="Nombre del Cliente"; }
		   if (!isset($wdocpac) or trim($wdocpac) == "9999") 
		      { $whabilita_venta="DISABLED";
		        $wvaldat="Documento del Cliente"; }
		   if (!isset($wtipusu) or trim($wtipusu) == "")      
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Tipo de Usuario"; }
	       if (!isset($wtipdto) or trim($wtipdto) == "")      
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Tipo de Documento"; }
	       if (!isset($wmuni) or trim($wmuni) == "")      
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Ciudad"; } 
	       if (!isset($wzona) or trim($wzona) == "")      
	          { $whabilita_venta="DISABLED"; 
	            $wvaldat="Zona"; }
	       if (!isset($wsexo) or trim($wsexo) == "")      
	          { $whabilita_venta="DISABLED"; 
	            $wvaldat="Sexo"; }
	       if (!isset($wpoliza) or trim($wpoliza) == "")      
	          { $whabilita_venta="DISABLED"; 
	            $wvaldat="Poliza"; }
	       if (!isset($wauto) or trim($wauto) == "")      
	          { $whabilita_venta="DISABLED"; 
	            $wvaldat="Autorización"; }
	       if (!isset($wfecven) or trim($wfecven) == "" or validar_fecha($wfecven) == false) 
	          { $whabilita_venta="DISABLED";
	            $wvaldat="Fecha de Vencimiento"; } 
	       if (!isset($wedad) or trim($wedad) == "" or (is_numeric($wedad) == false) or ($wedad <= 0))      
	          { $whabilita_venta="DISABLED";      
	            $wvaldat="Edad"; }
	      }
       
	   if (!isset($wtipcli) or trim($wtipcli) == "")         
	      { $whabilita_venta="DISABLED";     
	        $wvaldat="Tipo de Cliente"; }
	   if (!isset($wtipven) or trim($wtipven) == "")         
	      { $whabilita_venta="DISABLED";
	        $wvaldat="Tipo de Venta"; }
	   if (!isset($wempresa) or trim($wempresa) == "" or trim($wempresa) == "--")         
	      { $whabilita_venta="DISABLED";  
	        $wvaldat="Empresa"; }
	   if (!isset($wte1pac) or trim($wte1pac) == "") 
	      { $whabilita_venta="DISABLED";  
	        $wvaldat="Telefono"; }
	   if (!isset($wnompac) or trim($wnompac) == "") 
	      { $whabilita_venta="DISABLED"; 
	        $wvaldat="Nombre del Cliente"; }
	   if (!isset($wtipfac) or trim($wtipfac) == "") 
	      { $whabilita_venta="DISABLED";   
	        $wvaldat="Tipo de Factura"; }
	   if (!isset($wdocpac) or trim($wdocpac) == "") 
	      { $whabilita_venta="DISABLED";  
	        $wvaldat="Documento del Cliente"; }
	   if (!isset($wbondto) or trim($wbondto) == "") 
	      { $whabilita_venta="DISABLED";  
	        $wvaldat="Bono VIP"; }
	      
	       
	  }
  
  
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA MOSTRAR LAS OPCIONES DE RECIBOS DE DINERO - FORMAS DE PAGO
  function formasdepago($fk,$fconex,$fwcf,$fwcol,$fwclfg,$wfpa,$wdocane,$wobsrec,$wvalfpa,$wtotventot)
      {
	    global $wfecha;
        global $hora;	  
	      
	    global $fk;
	    global $wcf2;
	    global $wclfa;
	    global $wbondto;
	    global $wbasedato;
	    global $wtipcli;
	    global $wusuario;
	    global $wfecha_tempo;
	    global $whora_tempo;
	    global $wcco;
	    global $wcaja;
	    
	    global $whabilita_venta;
	    
	    global $wtotbase_dev_iva;
	    
	    
	    echo "<input type='HIDDEN' name='whabilita_venta' value='".$whabilita_venta."'>";
	    //echo $whabilita_venta."<br>";
	    
	     
	    for ($j=1;$j<=$fk;$j++)
	        {  
		      $q =  " SELECT fpacod, fpades "
			       ."   FROM ".$wbasedato."_000023 "
			       ."  WHERE fpaest = 'on' "
			       ."  ORDER BY fpacod ";     
				
			  $res = mysql_query($q,$fconex); // or die (mysql_errno()." - ".mysql_error());;
			  $num = mysql_num_rows($res);    // or die (mysql_errno()." - ".mysql_error());;
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //FORMA DE PAGO
			  echo "<td align=left bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Forma de pago: </font></b><select name='wfpa[".$j."]' onchange='enter()'>";
			  
			  if (isset($wfpa[$j]))
			     echo "<option selected>".$wfpa[$j]."</option>";
			  
			  $q =  " SELECT fpabde "
			       ."   FROM ".$wbasedato."_000023 " 
			       ."  WHERE fpacod = '".trim(substr($wfpa[$j],0,strpos($wfpa[$j],"-")))."'"
			       ."    AND fpaest = 'on' "
			       ."  ORDER BY fpacod ";     
			  $res_bde = mysql_query($q,$fconex);  
			  $row_bde = mysql_fetch_array($res_bde);  
			  $wbas_dev=$row_bde[0];
			  
			  for ($i=1;$i<=$num;$i++)
			     {
			      $row = mysql_fetch_array($res); 
			      echo "<option>".$row[0]." - ".$row[1]."</option>";
			     }
			  echo "</select></td>";
			  
			  
			  $wexistebon="N";
	///////&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&		  
	          if (isset($wfpa[$j]) and ($wbondto == "NO APLICA - NO APLICA"))     //Indica que si hay bono de descuento no busco formas de pago con bonos
	             {
		          if ($j >= 1) 
		             {  
			          $wwfpa=explode("-",$wfpa[$j]);  
					            
			          $q = " SELECT count(*) "
			              ."   FROM ".$wbasedato."_000057 "
			              ."  WHERE mid(codfpa,1,instr(codfpa,'-')-1) = '".trim($wwfpa[0])."'"
					      ."    AND tipemp                            = '".$wtipcli."'"
					      ."    AND fecha_ini                         <= '".$wfecha."'"
				          ."    AND fecha_fin                         >= '".$wfecha."'"
				          ."    AND hora_ini                          <= '".$hora."'"
				          ."    AND hora_fin                          >= '".$hora."'";   
				        
				      $resbonfpa = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
					  $numbonfpa = mysql_num_rows($resbonfpa);
					  $rowbonfpa = mysql_fetch_array($resbonfpa);
			          
					  if ($rowbonfpa[0] > 0) 
			             for ($h=1;$h<$j;$h++)
				             if ($wfpa[$h] == $wfpa[$j])
				                $wexistebon="S"; 
			         }        
				    else
			           $wexistebon="N";
		          
		         
			      if ($wexistebon == "N")  
		             { 
			          $wwfpa=explode("-",$wfpa[$j]);
			             
			          $q = " SELECT lineas "
			              ."   FROM ".$wbasedato."_000057 "
			              ."  WHERE mid(codfpa,1,instr(codfpa,'-')-1) = '".$wwfpa[0]."'"
						  ."    AND tipemp                            = '".$wtipcli."'"
						  ."    AND fecha_ini                         <= '".$wfecha."'"
				          ."    AND fecha_fin                         >= '".$wfecha."'"
				          ."    AND hora_ini                          <= '".$hora."'"
				          ."    AND hora_fin                          >= '".$hora."'";   
			          $resbonfpa = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
				      $numbonfpa = mysql_num_rows($resbonfpa);
				      
				      if ($numbonfpa > 0)
				         {
					      $rowbonfpa = mysql_fetch_array($resbonfpa);   
					      $wlineas=$rowbonfpa[0];   
					         
				          //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
		 			      //ACA TRAIGO TODO LO QUE HAY PENDIENTE DE FACTURAR EN ESTE CAJA   
					      $q = " SELECT sum(temtot), pordscto, valorbono, compramin "
					          ."   FROM ".$wbasedato."_000034, ".$wbasedato."_000001, ".$wbasedato."_000057 "
					          ."  WHERE temusu                            = '".$wusuario."'"
					          ."    AND temfec                            = '".$wfecha_tempo."'"
					          ."    AND temhor                            = '".$whora_tempo."'"
					          ."    AND temsuc                            = '".$wcco."'"
					          ."    AND temcaj                            = '".$wcaja."'"
					          ."    AND temdem                            = 0 "
					          ."    AND temdar                            = 0 "
					          ."    AND temdpa                            = 0 "
					          ."    AND artcod                            = temart "
					          ."    AND mid(codfpa,1,instr(codfpa,'-')-1) = '".$wwfpa[0]."'"
							  ."    AND tipemp                            = '".$wtipcli."'"
							  ."    AND mid(artgru,1,instr(artgru,'-')-1) in (".$wlineas.") "
							  ."    AND fecha_ini                         <= '".$wfecha."'"
					          ."    AND fecha_fin                         >= '".$wfecha."'"
					          ."    AND hora_ini                          <= '".$hora."'"
					          ."    AND hora_fin                          >= '".$hora."'"
					          ."  GROUP BY pordscto, valorbono, compramin ";
					          
					      $resbonfpa = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
					      $numbonfpa = mysql_num_rows($resbonfpa);
					      
					      if ($numbonfpa > 0)
					         {
						      $rowbonfpa = mysql_fetch_array($resbonfpa);
						      
						      $wcompracli = $rowbonfpa[0];
						      $wporcdscto = $rowbonfpa[1];
						      $wvalorbono = $rowbonfpa[2];
						      $wcompramin = $rowbonfpa[3];
						      if ($wcompracli >= $wcompramin)
						         if ($wporcdscto > 0)
						            $wvalfpa[$j] = $wcompracli*(1+($wporcdscto/100));
						           else
						              $wvalfpa[$j] = $wvalorbono; 
						        else
						           {
						            unset ($wfpa[$j]);
						            ?>	    
				    				  <script>
				    				    alert ("La compra NO alcanza el valor mínimo para aceptar esta forma de pago");      
						                function ira(){document.ventas.elements[document.ventas.elements.length-8].focus();}
				    				  </script>
				    				<?php
					               } 
						     }
						    else
						       {
						        unset ($wfpa[$j]);
						        ?>	    
				    			  <script>
				    			   alert ("En esta compra NO existen articulos habilitados para esta forma de pago");      
						           function ira(){document.ventas.elements[document.ventas.elements.length-8].focus();}
				    			  </script>
				    			<?php
					           }     
				         }
			         }  
			        else
			           {
				        //unset ($wfpa[$j]);
				        $wfpa[$j]="";
				        ?>	    
		    			  <script>
		    			   alert ("Ya se utilizó esta forma de pago");      
				           function ira(){document.ventas.elements[document.ventas.elements.length-8].focus();}
				           document.forms.ventas.submit();
		    			  </script>
		    			<?php
		    		   }
			     }     
	///////&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
			  
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //DOCUMENTO ANEXO
			  if (isset($wdocane[$j])) //Si ya fue digitado el documento anexo
			     echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Dcto Anexo: </font></b><INPUT TYPE='text' NAME='wdocane[".$j."]' VALUE='".$wdocane[$j]."'></td>";  //wdocane
			    else 
			       echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Dcto Anexo: </font></b><INPUT TYPE='text' NAME='wdocane[".$j."]' ></td>";                        //wdocane
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //OBSERVACIONES
			  if (isset($wobsrec[$j])) //Si ya fue digitado la observacion
			     echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Observ.: </font></b><INPUT TYPE='text' NAME='wobsrec[".$j."]' VALUE='".$wobsrec[$j]."'></td>";     //wobsrec
			    else 
			       echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Observ.: </font></b><INPUT TYPE='text' NAME='wobsrec[".$j."]' ></td>";                           //wobsrec     
			    
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //Con la siguiente instrucción en Javascript se ubica el cursor en el ultimo campo del valor de la forma de pago osea en: $wvalfpa[$j] : en el VALOR         
			  //$wvalfpa ==> Valor forma de pago
			  ?>	    
			    <script>
			      //function ira(){document.ventas.elements.length;}
			      //function ira(){document.ventas.elements[document.ventas.elements.length-1].focus();}
			      function ira(){document.ventas.elements[document.ventas.elements.length-4].focus();}
			    </script>
			  <?php
				
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //VALOR
			  if (isset($wvalfpa[$j]) & $wvalfpa > 0 ) //Si ya fue digitado el valor y es mayor a cero
			     {
				  $wpagado=0;   
			      for ($y=1;$y<=$j;$y++)
			          $wpagado=$wpagado+$wvalfpa[$y];
			      
			      $wvalfpa[$j]=str_replace(",","",$wvalfpa[$j]); //Esto se hace para quitarle el formato que trae el número
			      echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Valor: </font></b><INPUT TYPE='text' NAME='wvalfpa[".$j."]' VALUE='".number_format($wvalfpa[$j],2,'.',',')."' SIZE=15></td>";       //wvalfpa
			      if (($wtotventot-$wpagado) > 0 )
			         echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Saldo: </font></b>".number_format(($wtotventot-$wpagado),0,'.',',')."</td>";            //wtotventot-wtotfpa
			        else 
			           echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Saldo: </font></b>".number_format((0),0,'.',',')."</td>";                             //wtotventot-wtotfpa
			     } 
			    else
			       echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Valor: </font></b><INPUT TYPE='text' NAME='wvalfpa[".$j."]' SIZE=15></td>";  //wvalfpa     
			       
			  
			  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //BASE DE DEVOLUCION
			  if ($wbas_dev=="on" and isset($wvalfpa[$j]))
			     echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Base Dev. Iva: </b></font>".number_format(($wvalfpa[$j]*$wtotbase_dev_iva/$wtotventot),0,'.',',')."</td>";
			    else
			       echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$wclfa.">&nbsp</font></b></td>";
			  
			  echo "</tr>"; 
			}
	  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
  	  
	  
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
  //FUNCION PARA MOSTRAR LOS ARTICULOS SELECCIONADOS PARA LA VENTA  
  function mostrar($fwusuario,$fwfecha_tempo,$fwhora_tempo,$fwcco,$fwcaja,$fconex,$fwini,$fwdocpac,$fwnompac,$fwte1pac,$fwdirpac,$fwmaipac,$fwcol,$fwtipcli,$fwcuotamod,$fwempresa,$fwventa,$fwtipven,$fwmensajero,$fwdesemp,$fwrecemp,$fwdesart,$fwpdepac,$fwvdepac,$fwlinpac)
       {
	     global $wtotventot;  
	     global $wtotveniva;  
	     global $wcf; 
	     global $wcf2;
	     global $wclfa;
         global $wclfg;
         global $wtotdes;
         global $wtotrec;
         
         global $wbondto;
         global $wfecha;
         global $hora;
         global $wtipfac;
         global $wtipcli;
         global $wbasedato;
         global $wpagook;     //Indica que si puede pagar por Nomina 0:No 1:Si
         global $wchequeo;    //Indica si se verifica cupo en Nomina on:Si off:NO
         global $wmedico;
         global $wcodmed;
         global $wnommed;
         global $wprograma;
         global $wremitente;
         global $wcodrem;
         global $wnomrem;
         global $wcoddia;
         global $wnomdia;
         global $wrango;
         global $wtipdes;
         global $wnitemp;
         global $wpuntos;
         global $wval_pun;
         global $wcan_pun;
         global $wtipven;
         
         global $wprestamo;
         global $wemp;     //Codigo de la empresa responsable, cuando es empleado el carne
         
         global $wcarpun;     //Carne de puntos del cliente
         
         //VARIABLES RIPS
         global $wrips;
         global $wtipusu;
         global $wtipdto;
         global $wmuni;
         global $wzona;
         global $wsexo;
         global $wpoliza;
         global $wauto;
         global $wfecven;
         global $wedad;
         
         global $whabilita_venta;
         
         global $wtotbase_dev_iva;
         
         if ($wbondto == "NO APLICA - NO APLICA")
	        {   
             $q = " UPDATE ".$wbasedato."_000034"
                 ."    SET temdbo = 0 "
                 ."  WHERE temusu = '".$fwusuario."'"
      	         ."    AND temfec = '".$fwfecha_tempo."'"
	             ."    AND temhor = '".$fwhora_tempo."'"
	             ."    AND temsuc = '".$fwcco."'"
	             ."    AND temcaj = '".$fwcaja."'";
             $res_lin = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
            }
         
         //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
	     //ACA TRAIGO TODO LO QUE HAY PENDIENTE DE FACTURAR EN ESTE CAJA   
	     $q = " SELECT temart, temdes, tempre, temcan, temvun, tempiv, temiva, temtot, id, temdem, temrem, temdar, temdpa, tembpa, temdbo, temlpa "
	         ."   FROM ".$wbasedato."_000034 "
	         ."  WHERE temusu = '".$fwusuario."'"
	         ."    AND temfec = '".$fwfecha_tempo."'"
	         ."    AND temhor = '".$fwhora_tempo."'"
	         ."    AND temsuc = '".$fwcco."'"
	         ."    AND temcaj = '".$fwcaja."'"
	         ."  ORDER BY id ";
	     $res = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
	     $num = mysql_num_rows($res);
	    
	     if ($num > 0)
	        {
		     $wtotveniva=0;
	         $wtotventot=0;
	         $wtotdes=0;
	         $wtotrec=0;
	         $wtotbase_dev_iva=0;
	         $wtotartdessiniva=0;
	         
	         echo "<tr><td colspan=13>&nbsp</td></tr>";
	  
	         echo "<tr><td align=center colspan=13 bgcolor=".$wcf2."><font size=5 text color=".$wclfa."><b>DETALLE DE VENTA</b></font></td></tr>";
		     echo "<tr>";
		     echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Articulo</font></th>";
			 echo "<th bgcolor=".$wcf2." colspan=1><font text color=".$wclfa.">Descripción</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Presentación</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Cantidad</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">V/r Unit.Con IVA</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">V/r Unit.Sin IVA</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">% Descuento</th></font>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Descuento</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Total Venta Con</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">% Iva</th></font>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Valor Iva.</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Total</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">&nbsp</font></th>";
			 echo "</tr>";
			 
			 echo "<tr>"; 
			 echo "<th bgcolor=".$wcf2." colspan=7>&nbsp</tr>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Total Art.</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Dscto Sin IVA</font></th>";
			 echo "<th bgcolor=".$wcf2." colspan=4>&nbsp</tr>";
			 echo "</tr>";
			 
			 
			 if (isset($wbondto) and $wbondto != "NO APLICA - NO APLICA")
			    {
				 $wbondto1=explode("-",$wbondto);   
				  
			     //ACA BUSCO SI EL BONO TIENE DESCUENTO
			     $q = " SELECT linea, sublinea, descuento, recargo "
			         ."   FROM ".$wbasedato."_000047 "
			         ."  WHERE mid(bono,1,instr(bono,'-')-1) = '".trim($wbondto1[0])."'"
			         ."    AND fecha_ini <= '".$wfecha."'"
			         ."    AND fecha_fin >= '".$wfecha."'"
			         ."    AND hora_ini  <= '".$hora."'"
			         ."    AND hora_fin  >= '".$hora."'";
			     $res_desc = mysql_query($q,$fconex);
			     $num_desc = mysql_num_rows($res_desc);
			      
			     if ($num_desc > 0)
			        { 
			         $row_desc = mysql_fetch_array($res_desc); 
			         $wlin_bon=$row_desc[0];      //Linea
			         $wsub_bon=$row_desc[1];      //Sublinea
			         $wdes_bon=$row_desc[2];      //Descuento
			         $wrec_bon=$row_desc[3];      //Recargo
			        }
			       else
			          {
			           $wlin_bon="";     //Linea
			           $wsub_bon="";     //Sublinea
			           $wdes_bon=0;      //Descuento
			           $wrec_bon=0;      //Recargo 
		              } 
			     }
			     
			 
			 
			 //Con este for se recorren todos los articulos que hasta el momento se han registrado en la venta
			 for ($i=1;$i<=$num;$i++)
	            {   
	             $row = mysql_fetch_array($res);   
		       
	             //============================================================================     
	             //====== P R O G R A M A   P U N T O S =======================================         
	             //ACA VOY A BUSCAR SI ESTE TIPO DE CLIENTE ACUMULA PUNTOS O NO
				 $q =  " SELECT count(*), cpuvun, cpupun "
				      ."   FROM ".$wbasedato."_000062 "  
				      ."  WHERE cputem = '".$fwtipcli."'"
				      ."    AND cpufin <= '".$wfecha."'"
				      ."    AND cpuffi >= '".$wfecha."'"
				      ."  GROUP BY 2,3 ";
				 $res_pun = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
	             $row_pun = mysql_fetch_array($res_pun); 
	             
	             if ($row_pun[0] > 0)
	                {
	                 $wpuntos = "S";    //Indica que si acumula puntos
	                 $wval_pun=$row_pun[1];
	                 $wcan_pun=$row_pun[2];
	                 
	                 //Febrero 13 de 2009 - Si el tipo de venta es 'Internet' los puntos son dobles. Esto se coloco en funcionamiento desde el 16 de Feb de 2009
	                 if ($wtipven=="Internet")
	                    $wcan_pun=$wcan_pun*2;
                    }
                    
                 //Esto lo hago porque si habia seleccionado un bono de descto y luego lo quito entonces actualizo la tabla temporal tambien.
                 //Esto podria ocurrir cuando ya esta digitando la forma de pago.
                 if ($wbondto == "NO APLICA - NO APLICA")
	                {   
                     $q = "UPDATE ".$wbasedato."_000034"
                         ."   SET temdbo = 0 "
                         ." WHERE id     = ".$row[8];
                     $res_lin = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
                    }
	               
	             
	             ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	             //========================================================================================================================\\
	             //SI HAY DESCUENTO POR BONO BUSCO SI HAY ALGUN ARTICULO DE LA VENTA QUE PERTENEZCA A LA LINEA QUE TIENE DESCUENTO         \\
	             //========================================================================================================================\\
	             if (isset($wdes_bon) and $wdes_bon > 0)
	                {
		             if ($wsub_bon != "NO APLICA")
		                $wlinea_bon = substr($wlin_bon,0,strpos($wlin_bon,"-"))."-".substr($wsub_bon,0,strpos($wsub_bon,"-"));
		               else
		                  $wlinea_bon = substr($wlin_bon,0,strpos($wlin_bon,"-"))."%"; 
		             
		             $q = "SELECT descuento, recargo "
		                 ."  FROM ".$wbasedato."_000001, ".$wbasedato."_000047 "
		                 ." WHERE artcod                                          = '".$row[0]."'"                            //Articulo
		                 ."   AND ((mid(artgru,1,instr(artgru,'-')-1)               = mid(linea,1,instr(linea,'-')-1) "        //Linea
		                 ."   AND  mid(artgru,instr(artgru,'-')+1,length(artgru)) = mid(sublinea,1,instr(sublinea,'-')-1)) "  //Linea  
		                 ."    OR (mid(artgru,1,instr(artgru,'-')-1)               = mid(linea,1,instr(linea,'-')-1) "        //Linea 
		                 ."   AND  sublinea                                       = 'NO APLICA')) "
		                 ."   AND artest                                          = 'on' "
		                 ."   AND mid(bono,1,instr(bono,'-')-1)                   = '".trim($wbondto1[0])."'"
				         ."   AND fecha_ini                                      <= '".$wfecha."'"
				         ."   AND fecha_fin                                      >= '".$wfecha."'"
				         ."   AND hora_ini                                       <= '".$hora."'"
				         ."   AND hora_fin                                       >= '".$hora."'";
		             $res_lin = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
		             $num_lin = mysql_num_rows($res_lin); 
		             
		             $row_lin = mysql_fetch_array($res_lin);
		             
		             if ($row_lin[0] == 0 or $num_lin==0)
		                $wdcto_bon=0;
		               else
		                  {
		                   $wdcto_bon=$row_lin[0];    //Descuento
		                   $wrec_bon =$row_lin[1];    //Recargo
	                      } 
	                } 
	               else
	                  $wdcto_bon=0;
	             
	                  
	             //////////////////////////////////////////////////////////////////////////////////////////////////////
			     //ACA EVALUO SI LA CUENTA POSEE ALGUN DESCUENTO
			     //////////////////////////////////////////////////////////////////////////////////////////////////////
			     if ($row[9] > 0)                      //Si tiene descuento empresa
			        $wdesc_art=$row[9];
			       else
			         if ($row[11] > 0)                 //Si tiene descuento por articulo
			            $wdesc_art=$row[11];  
			           else
			              if ($row[12] > 0 or $fwlinpac != "" )     //Si tiene descuento por tipo de cliente por linea, El 'or' es porque si de pronto se digitaron antes los articulos
			                 {                                      //que los datos del cliente, para que recalcule el descuento. Porque no quedo el descto en la tabla temporal 000034 
				              $q = " SELECT clepde, clevde "
					              ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000042, ".$wbasedato."_000041 "
					              ."  WHERE artcod                            = '".$row[0]."'"
					              ."    AND mid(artgru,1,instr(artgru,'-')-1) in (".$fwlinpac.") "
							      ."    AND clefid                            <= '".$wfecha."'"
					              ."    AND cleffd                            >= '".$wfecha."'"
					              ."    AND clidoc                             = '".$fwdocpac."'"
					              ."    AND clitip                             = clecla "
					              ."    AND cleest                             = 'on' ";
					          $res_lpa = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
		                      $num_lpa = mysql_num_rows($res_lpa); 
		             
		                      
		                      $row_lpa = mysql_fetch_array($res_lpa);  
		                      if ($row_lpa[0] > 0)               //Si tiene porcentaje de descuento
		                         $wdesc_art=$row_lpa[0]/100;
		                        else 
		                           if ($row_lpa[1] > 0)          //Si tiene valor de descuento
		                              $wvald_art=$row_lpa[1];  
		                             else
		                                $wdesc_art=0;        
			                 }   
			                else
			                   if ($row[14] > 0)       //Si tiene descuento por bonos
			                      $wdesc_art=$row[14]; 
			                     else                  //Con lo siguiente averiguo si se selecciono el bono de descuento despues de los articulos.
			                        if ($wdcto_bon > 0)               //Esto lo hago porque si el vendedor coloco el bono de descuento despues de digitar los articulos, esto hace que el descuento se le aplique a todos
			                           {                              //por este motivo debe de actualizar la tabla temporal colocandole al articulo el porcentaje de descuento que le corresponde.,
			                            $wdesc_art=$wdcto_bon/100;      
			                            $q = "UPDATE ".$wbasedato."_000034"
			                                ."   SET temdbo = ".($wdcto_bon/100)
			                                ." WHERE id     = ".$row[8];
			                            $res_lin = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
		                               } 
			                          else
			                             $wdesc_art=0;
			                             
			              //$row[3] = Cantidad
			              //$row[4] = Valor Unitario con IVA
			              //$row[5] = Porcentaje de IVA
			              //$row[6] = Valor IVA
			              //$row[7] = Total articulo
			              
			     $wvaluni=round(($row[4]-($row[6]/$row[3])));                                     //Valor Unitario sin IVA
			     $wdesart=round($row[3]*round($wvaluni*$wdesc_art));                              //Descuento total por Articulo
			     if ($wdesc_art > 0)
			        $wivaart=round($row[3]*round(round($wvaluni*(1-$wdesc_art))*($row[5]/100)));     //Valor IVA total por articulo
			       else 
			          $wivaart=round(($row[4]*$row[3])-($wvaluni*$row[3])-(round(($row[4]*$wdesc_art)/(1+($row[5]/100)))*($row[5]/100)));       //Valor IVA total por articulo
			     $wtotart=((round(($row[3]*$wvaluni))-$wdesart)+$wivaart);                        //Valor Total articulo
			     
			     $wtotartdessiniva=$wtotartdessiniva+(($row[3]*$wvaluni)-$wdesart);               //Suma Columna Valor Articulo articulo con descto SIN IVA
			     
			     
			     echo "<tr>";
			     echo "<td align=center>".$row[0]."</td>";                                                  //Articulo
			     echo "<td align=LEFT>".$row[1]."</td>";                                                    //Descripcion
			     echo "<td align=center>".$row[2]."</td>";                                                  //Unidad
	             echo "<td align=center>".$row[3]."</td>";                                                  //Cantidad
	             echo "<td align=RIGHT>".number_format($row[4],0,'.',',')."</td>";                          //Valor unitario CON IVA
			     echo "<td align=RIGHT>".number_format($wvaluni,0,'.',',')."</td>";                         //Valor unitario SIN IVA
			     echo "<td align=RIGHT>".number_format(($wdesc_art*100),0,'.',',')."</td>";                 //% Descuento
			     echo "<td align=RIGHT>".number_format($wdesart,0,'.',',')."</td>";                         //Descuento total articulo
			     echo "<td align=RIGHT>".number_format((($row[3]*$wvaluni)-$wdesart),0,'.',',')."</td>";    //Total articulo CON DESCTO SIN IVA
			     echo "<td align=RIGHT>".number_format($row[5],0,'.',',')."</td>";                          //Porcentaje de iva
			     echo "<td align=RIGHT>".number_format($wivaart,0,'.',',')."</td>";                         //Valor iva total articulo
			     echo "<td align=RIGHT>".number_format($wtotart,0,'.',',')."</td>";                         //Total articulo
			     
			     if (!isset($fwventa) or $fwventa == "N" )  //Solo da la opcion de eliminar mientras no se haya grabado la venta definitiva
			        echo "<td align=center><font size=3><A href='Ventas.php?wid=".$row[8]."&amp;wborrar=S"."&amp;wini=".$fwini."&amp;wfecha_tempo=".$fwfecha_tempo."&amp;whora_tempo=".$fwhora_tempo."&amp;wdocpac=".$fwdocpac."&amp;wnompac=".$fwnompac."&amp;wte1pac=".$fwte1pac."&amp;wdirpac=".$fwdirpac."&amp;wmaipac=".$fwmaipac."&amp;wtipcli=".$fwtipcli."&amp;wcuotamod=".$fwcuotamod."&amp;wempresa=".$fwempresa."&amp;wtipven=".$fwtipven."&amp;wmensajero=".$fwmensajero."&amp;wdesemp=".$fwdesemp."&amp;wrecemp=".$fwrecemp."&amp;wbondto=".$wbondto."&amp;wbasedato=".$wbasedato."&amp;wtipfac=".$wtipfac."&amp;whabilita_venta=".$whabilita_venta."&amp;wmedico=".$wmedico."&amp;wcodmed=".$wcodmed."&amp;wnommed=".$wnommed."&amp;wprograma=".$wprograma."&amp;wremitente=".$wremitente."&amp;wcodrem=".$wcodrem."&amp;wnomrem=".$wnomrem."&amp;wrips=".$wrips."&amp;wtipusu=".$wtipusu."&amp;wtipdto=".$wtipdto."&amp;wmuni=".$wmuni."&amp;wzona=".$wzona."&amp;wsexo=".$wsexo."&amp;wpoliza=".$wpoliza."&amp;wauto=".$wauto."&amp;wfecven=".$wfecven."&amp;wedad=".$wedad."&amp;wcarpun=".$wcarpun."'> Eliminar</A></font></td>";
			     echo "<tr>";
			    
			     //$row[0]    = Codigo articulo
			     //$row[1]    = Descripcion articulo
			     //$row[2]    = Presentacion
			     //$row[3]    = Cantidad
			     //$row[4]    = Valor unitario
			     //$row[5]    = Porcentaje de IVA
			     //$row[6]    = Valor IVA
			     //$row[7]    = Valor total
			     //$row[8]    = Registro id
			     //$row[9]    = % Descuento empresa
			     //$row[10]   = % Recargo empresa
			     //$row[11]   = % Descuento articulo
			     //$row[12]   = % Descuento al usuario por tipo de cliente
			     //$row[13]   = Bono al usuario por tipo de cliente
			     //$wdcto_bon = Descuento por bonos traidos por el cliente (promocion)
			     //$wrec_bon  = Descuento por bonos traidos por el cliente (promocion)
			     
			     
			     $wtotdes=$wtotdes+$wdesart;
			     $wtotveniva=$wtotveniva+$wivaart;
			     $wtotventot=$wtotventot+$wtotart;
			     
			     if ($row[5] > 0)
			        $wtotbase_dev_iva=round(($wtotbase_dev_iva+(($row[3]*$wvaluni)-$wdesart)));   
			    }
			  echo "<tr>";
	          echo "<td align=RIGHT bgcolor=".$wcf2." colspan=5><font text color=".$wclfa."><b>TOTALES &nbsp &nbsp</b></font></td>"; 
	          echo "<td bgcolor=".$wcf2."><font text color=".$wclfa.">&nbsp</font></td>";
	          echo "<td bgcolor=".$wcf2."><font text color=".$wclfa.">&nbsp</font></td>";
	          echo "<td align=RIGHT bgcolor=".$wcf2."><font text color=".$wclfa.">".number_format($wtotdes,0,'.',',')."</font></td>";
	          echo "<td align=RIGHT bgcolor=".$wcf2."><font text color=".$wclfa.">".number_format($wtotartdessiniva,0,'.',',')."</font></td>";
	          echo "<td bgcolor=".$wcf2."><font text color=".$wclfa.">&nbsp</font></td>";
	          echo "<td align=RIGHT bgcolor=".$wcf2."><font text color=".$wclfa.">".number_format($wtotveniva,0,'.',',')."</font></td>";
	          echo "<td align=RIGHT bgcolor=".$wcf2."><font text color=".$wclfa.">".number_format($wtotventot,0,'.',',')."</font></td>";
	          echo "<td align=CENTER bgcolor=".$wcf2."><font text color=".$wclfa.">Base Devolución: <br>".number_format($wtotbase_dev_iva,0,'.',',')."</font></td>";
	          echo "</tr>";
	          
	          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	          //ACA VERIFICO SI EL TIPO DE CLIENTE DEBE SER CHEQUEADO
			  //POR AHORA SE VERIFICAN LOS QUE SEAN DE TIPO EMPLEADO 
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////  
			  $wcodigo=explode("-",$fwempresa);
			  $q =  " SELECT temche "
		           ."   FROM ".$wbasedato."_000029 "
		           ."  WHERE temcod = (mid('".$fwtipcli."',1,instr('".$fwtipcli."','-')-1)) " 
			       ."  ORDER BY temcod ";
			       
			  $res = mysql_query($q,$fconex); // or die (mysql_errno()." - ".mysql_error());;
		      $row = mysql_fetch_array($res); 
		      $wchequeo=$row[0];   
		      
		      if ($row[0] == 'on')  //Si hay que verificar el cupo por Nomina
			     {
				  $q = " SELECT empres "
				      ."   FROM ".$wbasedato."_000024 "
				      ."  WHERE empcod = '".trim($wcodigo[0])."'"
				      ."    AND empnit = '".trim($wnitemp)."'"
				      ."    AND emptem = '".$wtipcli."'";  
				      
				  $res = mysql_query($q,$fconex); // or die (mysql_errno()." - ".mysql_error());;
		          $row = mysql_fetch_array($res);
		          $wresp=$row[0];
		          
		          if ($whabilita_venta == "ENABLED")
		             {
			          if (!isset($fwventa) or $fwventa == "N" )  //Solo da la opcion de Grabar Venta mientras no se haya grabado la venta definitiva
			             {
				          echo "<td align=center bgcolor=#cccccc colspan=6><font size=5><A href='prestamos.php?codigo=".trim($wcodigo[0])."&amp;wemp=".trim($wresp)."&amp;monto=".$wtotventot."&amp;empresa=".$wbasedato."&amp;wpagook=".$wpagook."&amp;whabilita_venta=".$whabilita_venta."&amp;wcarpun=".$wcarpun."' target='_blank'> Verificar Cupo</A></font></td>";   
		                  echo "<td align=center bgcolor=#cccccc colspan=7><font size=5><A href='Ventas.php?wventa=S"."&amp;wini=".$fwini."&amp;wtipcli=".$fwtipcli."&amp;wfecha_tempo=".$fwfecha_tempo."&amp;whora_tempo=".$fwhora_tempo."&amp;wcuotamod=".$fwcuotamod."&amp;wempresa=".$fwempresa."&amp;wdocpac=".$fwdocpac."&amp;wnompac=".$fwnompac."&amp;wte1pac=".$fwte1pac."&amp;wdirpac=".$fwdirpac."&amp;wmaipac=".$fwmaipac."&amp;wtipven=".$fwtipven."&amp;wmensajero=".$fwmensajero."&amp;wdesemp=".$fwdesemp."&amp;wrecemp=".$fwrecemp."&amp;wbondto=".$wbondto."&amp;wtipfac=".$wtipfac."&amp;wbasedato=".$wbasedato."&amp;wpagook=".$wpagook."&amp;wmedico=".$wmedico."&amp;wcodmed=".$wcodmed."&amp;wnommed=".$wnommed."&amp;wprograma=".$wprograma."&amp;wremitente=".$wremitente."&amp;wcodrem=".$wcodrem."&amp;wnomrem=".$wnomrem."&amp;wrips=".$wrips."&amp;wtipusu=".$wtipusu."&amp;wtipdto=".$wtipdto."&amp;wmuni=".$wmuni."&amp;wzona=".$wzona."&amp;wsexo=".$wsexo."&amp;wpoliza=".$wpoliza."&amp;wauto=".$wauto."&amp;wfecven=".$wfecven."&amp;wedad=".$wedad."&amp;whabilita_venta=".$whabilita_venta."&amp;wcarpun=".$wcarpun."&amp;wprestamo=".$wprestamo."&amp;wtotventot=".$wtotventot."&amp;wcodigo=".$wcodigo."&amp;wemp=".$wemp."&wcoddia=".$wcoddia."&wnomdia=".$wnomdia."&wrango=".$wrango."&wtipdes=".$wtipdes."'> Grabar Venta</A></font></td>";
	                     } 
                     }
                     
                  //Si es un empleado y gana puntos tomo la cedula, el nombre y el codigo como los datos del cliente
                  if ($wpuntos == "S")
                     {
	                  $wte1pac=trim($wcodigo[0]);   
                      $wdocpac=trim($wcodigo[1]); 
                      $wnompac=trim($wcodigo[2]);
                      
                      echo "<input type='HIDDEN' name='wdocpac' value='".$wdocpac."'>";
                      echo "<input type='HIDDEN' name='wnompac' value='".$wnompac."'>";
	                  echo "<input type='HIDDEN' name='wte1pac' value='".$wte1pac."'>";
                     } 
                 }
                else
                   if ($whabilita_venta == "ENABLED")
		              {
	                   if (!isset($fwventa) or $fwventa == "N" )  //Solo da la opcion de Grabar Venta mientras no se haya grabado la venta definitiva
	                      echo "<td align=center bgcolor=#cccccc colspan=13><font size=5><A href='Ventas.php?wventa=S"."&wini=".$fwini."&wtipcli=".$fwtipcli."&wfecha_tempo=".$fwfecha_tempo."&whora_tempo=".$fwhora_tempo."&wcuotamod=".$fwcuotamod."&wempresa=".$fwempresa."&wdocpac=".$fwdocpac."&wnompac=".$fwnompac."&wte1pac=".$fwte1pac."&wdirpac=".$fwdirpac."&wmaipac=".$fwmaipac."&wtipven=".$fwtipven."&wmensajero=".$fwmensajero."&wdesemp=".$fwdesemp."&wrecemp=".$fwrecemp."&wbondto=".$wbondto."&wtipfac=".$wtipfac."&wbasedato=".$wbasedato."&wpagook=".$wpagook."&wmedico=".$wmedico."&wcodmed=".$wcodmed."&wnommed=".$wnommed."&wprograma=".$wprograma."&wremitente=".$wremitente."&wcodrem=".$wcodrem."&wnomrem=".$wnomrem."&wrips=".$wrips."&wtipusu=".$wtipusu."&wtipdto=".$wtipdto."&wmuni=".$wmuni."&wzona=".$wzona."&wsexo=".$wsexo."&wpoliza=".$wpoliza."&wauto=".$wauto."&wfecven=".$wfecven."&wedad=".$wedad."&whabilita_venta=".$whabilita_venta."&wcarpun=".$wcarpun."&wprestamo=".$wprestamo."&wtotventot=".$wtotventot."&wcodigo=".$wcodigo."&wemp=".$wemp."&wcoddia=".$wcoddia."&wnomdia=".$wnomdia."&wrango=".$wrango."&wtipdes=".$wtipdes."&wcuotamod=".$fwcuotamod."'> Grabar Venta</A></font></td>";
                      }    
	          echo "</tr>";
	          
	          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	    
	        }   
		  ///////////////////////////////////////////////////////////////////////  
	   }    
       
  //===========================================================================================================================================
  //INICIO DEL PROGRAMA   
  //===========================================================================================================================================
  
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
  
  echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";
  //=======================================================================================================================================
  //ACA COMIENZA EL ENCABEZADO DE LA VENTA
  echo "<center><table border>";
  echo "<tr><td align=center rowspan=2 colspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center colspan=6 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>VENTAS AL PUBLICO</b></font></td></tr>";
  echo "<tr>";
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CARNE O TARJETA DE PUNTOS
  //*************************************************************************************************************************************************************************************************************************************************************************************
  $whabilita_puntos="ENABLED";
  if (isset($wcarpun)) //Si ya fue digitado el documento del cliente
     {
	  if ($wcarpun!="000000")
         $whabilita_puntos="DISABLED";
        else
           $whabilita_puntos="ENABLED"; 
      echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Tarjeta Puntos: </font></b><INPUT TYPE='text' NAME='wcarpun' VALUE='".$wcarpun."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false' ".$whabilita_puntos."></td>";   //wcarpun
      echo "<input type='HIDDEN' name='wcarpun' value='".$wcarpun."'>";
     } 
    else
       {
	    echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Tarjeta Puntos: </font></b><INPUT TYPE='text' NAME='wcarpun' VALUE='000000' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";              //wcarpun
        $wpdepac=0;
        $wvdepac=0;
        $wlinpac="";   
       }    
  /*   
  if (isset($wcarpun)) //Si ya fue digitado el documento del cliente
     {
	  $whabilita_puntos="ENABLED";   
      if ($wcarpun != "000000" and $wcarpun != "")
         {
	      $q= "SELECT clidoc, clinom, clite1, clidir, climai, clitip, clipun "
	         ."  FROM ".$wbasedato."_000041 "
	         ." WHERE clipun = '".$wcarpun."'";
	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);   
	      if ($num1 > 0)
	         {
		      $row1 = mysql_fetch_array($res1);
	          //if (isset($wnompac) and $wnompac == "CLIENTE PARTICULAR") $wnompac=$row1[1];  //Si el Nombre esta setiado y es diferente al almacenado
	          //if (isset($wte1pac) and $wte1pac == "SIN DATO") $wte1pac=$row1[2];            //Si el Telefono esta setiado y es diferente al almacenado
	          //if (isset($wdirpac) and $wdirpac == "SIN DATO") $wdirpac=$row1[3];            //Si la Direccion esta setiada y es diferente a la almacenada
	          //if (isset($wmaipac) and $wmaipac == "SIN DATO") $wmaipac=$row1[4];            //Si la Direccion esta setiada y es diferente a la almacenada
	          
	          $wnompac=$row1[1];
	          $wte1pac=$row1[2];
	          $wdirpac=$row1[3];
	          $wmaipac=$row1[4];
	          
	          echo "<input type='HIDDEN' name='wnompac' value='".$wnompac."'>";
	          echo "<input type='HIDDEN' name='wte1pac' value='".$wte1pac."'>";
	          echo "<input type='HIDDEN' name='wdirpac' value='".$wdirpac."'>";
	          echo "<input type='HIDDEN' name='wmaipac' value='".$wmaipac."'>";
	          
	          $wclitip=$row1[5];
	          $whabilita_puntos="DISABLED";
	         }
	      echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Tarjeta Puntos: </font></b><INPUT TYPE='text' NAME='wcarpun' VALUE='".$wcarpun."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false' ".$whabilita_puntos."></td>";   //wcarpun   
	      if ($whabilita_puntos == "DISABLED")
	         echo "<input type='HIDDEN' name='wcarpun' value='".$wcarpun."'>";
         }
        else
           if ($whabilita_venta=="ENABLED")
              echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Tarjeta Puntos: </font></b><INPUT TYPE='text' NAME='wcarpun' VALUE='".$wcarpun."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false' DISABLED></td>";       //wcarpun     
             else
                echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Tarjeta Puntos: </font></b><INPUT TYPE='text' NAME='wcarpun' VALUE='".$wcarpun."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false' ENABLED></td>";       //wcarpun     
            
  
      //SIN IMPORTAR SI EL CLIENTE ES DIFERENTE A 9999 O IGUAL BUSCO EN EL TIPO DE CLIENTE SI HAY DESCUENTO     
      //ACA CONSULTO SI EL TIPO DE CLIENTE ESPECIAL TIENE DESCUENTO O BONO DE DESCUENTO PARA APLICARLO LUEGO EN LA VENTA
	  $q= "SELECT clepde, clevde, clelin "
	   	 ."  FROM ".$wbasedato."_000041, ".$wbasedato."_000042 "         //Tabla tipos de clientes
	     ." WHERE clipun  = '".$wcarpun."'"
	     ."   AND clitip  = clecla "
	     ."   AND clefid <= '".$wfecha."'"
	     ."   AND cleffd >= '".$wfecha."'"
	     ."   AND cleest  = 'on' "
	     ."   AND clelin  != 'NO APLICA' "
	     ."   AND clelin  != '' ";
	  $res1 = mysql_query($q,$conex);
	  $num1 = mysql_num_rows($res1); 
	  
	   if ($num1 > 0)
	    {
	     $row1 = mysql_fetch_array($res1);
	     $wpdepac=($row1[0]/100);   
	     $wvdepac=$row1[1];
	     $wlinpac=$row[2];
	    }
	   else
	      {
	       $wpdepac=0;
           $wvdepac=0; 
           $wlinpac="";     
          } 
     } 
    else 
       {
	    echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Tarjeta Puntos: </font></b><INPUT TYPE='text' NAME='wcarpun' VALUE='000000' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";              //wcarpun
        $wpdepac=0;
        $wvdepac=0;
        $wlinpac="";
       }    */  
  //*************************************************************************************************************************************************************************************************************************************************************************************
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA DE LA VENTA
  echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Fecha: </font></b>".$wfecha."</td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //SUCURSAL
  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Sucursal: </font></b>".$wnomcco."</td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CAJA
  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Caja: </font></b>".$wnomcaj."</td>";
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE RESPONSABLE
  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Tipo de Responsable: </font></b><select name='wtipcli' onchange='enter()'>"; 
  
  if (isset($wtipcli))
     {
	  $q =  " SELECT temcod, temdes "
           ."   FROM ".$wbasedato."_000029 "
           ."  WHERE temcod not in (mid('".$wtipcli."',1,instr('".$wtipcli."','-')-1)) " 
	       ."  ORDER BY temcod ";
	 }  
    else
       { 
        $q =  " SELECT temcod, temdes "
             ."   FROM ".$wbasedato."_000029 "
	         ."  ORDER BY temcod ";
	   }      
  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
  if (isset($wtipcli))
     echo "<option selected>".$wtipcli."</option>";    
  for ($i=1;$i<=$num;$i++)
     {
      $row = mysql_fetch_array($res); 
      echo "<option>".$row[0]."-".$row[1]."</option>";
     }
  echo "</select></td>";
  
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE VENTA
  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Tipo de Venta: </font></b><select name='wtipven' onchange='enter()'>";
  
  if (isset($wtipven))
    {
     if ($wtipven == "Directa")
        {
         echo "<option selected>".$wtipven."</option>";  
         echo "<option>Domicilio</option>";
         echo "<option>Internet</option>";
        }  
     if ($wtipven == "Domicilio")
        {
         echo "<option selected>".$wtipven."</option>";  
         echo "<option>Directa</option>";
         echo "<option>Internet</option>";
        }  
     if ($wtipven == "Internet")
        {
         echo "<option selected>".$wtipven."</option>";  
         echo "<option>Directa</option>";
         echo "<option>Domicilio</option>";
        }
    }      
   else  
      {
       echo "<option>Directa</option>";
       echo "<option>Domicilio</option>";
       echo "<option>Internet</option>";
      } 
  echo "</select></td></tr>";
  
  if (isset($wtipven) and ($wtipven <> "Directa"))
     {
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //MENSAJERO
	  if (isset($wmensajero))
	     {
		  $q =  " SELECT msjcod, msjnom "
		       ."   FROM ".$wbasedato."_000035 "
		       ."  WHERE msjcod <> '".$wmensajero."'"
		       ."    AND msjest = 'on'"
		       ."  ORDER BY msjcod ";
		 }
	    else
	       {
		    $q =  " SELECT msjcod, msjnom "
		         ."   FROM ".$wbasedato."_000035 "
		         ."  WHERE msjest = 'on'"
		         ."  ORDER BY msjcod ";
	       }
	   
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Mensajero: <br></font></b><select name='wmensajero'>";
	  
	  if (isset($wmensajero))
	     {
		  $q= "   SELECT count(*) FROM ".$wbasedato."_000035 "
	         ."    WHERE msjcod = (mid('".$wmensajero."',1,instr('".$wmensajero."','-')-1)) "  
	         ."      AND msjest = 'on'";
	         
	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);   
	      $row1 = mysql_fetch_array($res1);
	      if ($row1[0] > 0)
		     echo "<option selected>".$wmensajero."</option>";    
	     } 
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
	     }
	  echo "</select></td>";
     }
    else
       $wmensajero=""; 
	  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //RESPONSABLES
  if (isset($wtipcli))
     {
	  $q =  " SELECT empcod, empnit, empnom "
	       ."   FROM ".$wbasedato."_000024 "
	       ."  WHERE emptem = '".$wtipcli."'"
	       ."    AND empest = 'on' "
	       ."  ORDER BY empcod ";
     }
    else
       {
	    $q =  " SELECT empcod, empnit, empnom "
	         ."   FROM ".$wbasedato."_000024 "
	         ."  WHERE empcod != ' ' "
	         ."    AND empest = 'on' "
	         ."  ORDER BY empcod ";
       }
  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
  
  echo "<td align=left bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg."> Responsable: </font></b><select name='wempresa' onchange='enter()'>";
  
  if (isset($wempresa))     {
	  //Este query lo hago para saber si la empresa que esta en pantalla corresponde al tipo de cliente o empresa seleccionado en el campo anterior
	  //Si si corresponde la muestro, si no, solo muestra las seleccionadas en el query anterior   
      $q= "   SELECT count(*), empmed, emppro, emprem, emprip, empdgn, empran, emptde "
         ."     FROM ".$wbasedato."_000024 "
         ."    WHERE empcod = (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) "  
         ."      AND emptem = '".$wtipcli."'"
         ."      AND empest = 'on' "
         ."    GROUP BY 2,3,4,5 ";
      $res1 = mysql_query($q,$conex);
      $num1 = mysql_num_rows($res1);   
      $row1 = mysql_fetch_array($res1);
      
      if ($row1[0] > 0)
         {
	      echo "<option selected>".$wempresa."</option>";    
	      $wmedi=$row1[1];    //Si pide medico
	      $wprog=$row1[2];    //Si pide programa de afiliacion
	      $wremi=$row1[3];    //Si pide remitente
	      $wrips=$row1[4];    //Si genera RIPS
	      $wdiag=$row1[5];    //Si pide Diagnostico
	      $wran =$row1[6];    //Si pide rango del usuario
	      $wtde =$row1[7];    //Si pide tipo de despacho
	     }
     }
     
  for ($i=1;$i<=$num;$i++)
     {
      $row = mysql_fetch_array($res); 
      echo "<option>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
     }
  echo "</select></td>";
  
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //TELEFONO DEL CLIENTE
  if (isset($wte1pac)) //Si ya fue digitado el telefono del cliente
     echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Telefono: </font></b><INPUT TYPE='text' NAME='wte1pac' VALUE='".$wte1pac."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";
	else
      {
	   echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Telefono: </font></b><INPUT TYPE='text' NAME='wte1pac' VALUE='SIN DATO' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";
       $wpdepac=0;
       $wvdepac=0;
       $wlinpac="";   
      }   
	     
	  /*   
      if ($wte1pac != "SIN DATO" and $wte1pac != "")
         {
	      $q= " SELECT clidoc, clinom, clite1, clidir, climai, clitip, clipun "
	         ."   FROM ".$wbasedato."_000041 "
	         ."  WHERE clite1 = '".$wte1pac."'";
	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);   
	      if ($num1 > 0)
	         {
		      $row1 = mysql_fetch_array($res1);
	          
	          if (isset($wnompac) and $wnompac == "CLIENTE PARTICULAR") $wnompac=$row1[1];   //Si el Nombre esta setiado o es diferente al almacenado
		      if (isset($wdocpac) and $wdocpac == "9999") $wdocpac=$row1[0];                 //Si el Documento esta setiado o es diferente al almacenado
		      if (isset($wdirpac) and $wdirpac == "SIN DATO") $wdirpac=$row1[3];             //Si la Direccion esta setiada o es diferente a la almacenada
		      if (isset($wmaipac) and $wmaipac == "SIN DATO") $wmaipac=$row1[4];             //Si la Direccion esta setiada o es diferente a la almacenada
		      if (isset($wcarpun) and $wcarpun == "000000" and $row1[6] != "" and $row1[6] != "000000") 
		         {
			      unset ($wcarpun);   
		          $wcarpun=$row1[6];  //Tarjeta de puntos
		          echo "<input type='HIDDEN' name='wcarpun' value='".$wcarpun."'>";
		         } 
		          
		      echo "<input type='HIDDEN' name='wnompac' value='".$wnompac."'>";
	          echo "<input type='HIDDEN' name='wdocpac' value='".$wdocpac."'>";
	          echo "<input type='HIDDEN' name='wdirpac' value='".$wdirpac."'>";
	          echo "<input type='HIDDEN' name='wmaipac' value='".$wmaipac."'>";
	          
	          $wclitip=$row1[5];
	          
		      //ACA CONSULTO SI EL TIPO DE CLIENTE ESPECIAL TIENE DESCUENTO O BONO DE DESCUENTO PARA APLICARLO LUEGO EN LA VENTA
	          $q= " SELECT clepde, clevde, clelin "
	         	 ."   FROM ".$wbasedato."_000042 "          //Tabla tipos de clientes
	             ."  WHERE clecla  = '".$wclitip."'"
	             ."    AND clefid <= '".$wfecha."'"
	             ."    AND cleffd >= '".$wfecha."'"
	             ."    AND cleest  = 'on' "
	             ."   AND clelin  != 'NO APLICA' "
	             ."   AND clelin  != '' ";;
	          $res1 = mysql_query($q,$conex);
	          $num1 = mysql_num_rows($res1);   
	          if ($num1 > 0)
	             {
		          $row1 = mysql_fetch_array($res1);
		          $wpdepac=($row1[0]/100);   
		          $wvdepac=$row1[1];
		          $wlinpac=$row[2]; 
		         }
		     }
	        else
	           if ($wdocpac == "9999") $wdocpac=$wte1pac;
	      
	      echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg."> Telefono : <br></font></b><INPUT TYPE='text' NAME='wte1pac' SIZE=9 VALUE='".$wte1pac."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false' onchange='enter()' ></td>";             //wte1pac  
         }
        else 
           echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg."> Telefono : <br></font></b><INPUT TYPE='text' NAME='wte1pac' SIZE=9 VALUE='".$wte1pac."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false' onchange='enter()'></td>";            //wte1pac
           
     } 
    else 
       echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg."> Telefono : <br></font></b><INPUT TYPE='text' NAME='wte1pac' SIZE=9 VALUE='SIN DATO' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false' onchange='enter()'></td>";                    //wdirpac
  
       */
       
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //NOMBRE DEL CLIENTE  
  $wcolspan=1;  
  if (isset($wnompac)) //Si ya fue digitado el nombre del cliente    
     echo "<td align=left bgcolor=".$wcf." colspan=".$wcolspan."><b><font text color=".$wclfg."> Nombre: </font></b><INPUT TYPE='text' NAME='wnompac' SIZE=30 VALUE='".$wnompac."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false' ></td>";          //wnompac
    else 
       echo "<td align=left bgcolor=".$wcf." colspan=".$wcolspan."><b><font text color=".$wclfg."> Nombre: </font></b><INPUT TYPE='text' NAME='wnompac' SIZE=30 VALUE='CLIENTE PARTICULAR' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";  //wnompac
  
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE FACTURA
  echo "<td align=left bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg.">Tipo de Factura: </font></b><select name='wtipfac' onchange='enter()'>";
  
  if (isset($wtipfac))
     if ($wtipfac == "Automatica")
        {
         echo "<option selected>".$wtipfac."</option>";  
         echo "<option>Manual</option>";
        }  
       else
          {
	       $q = " SELECT ccopfm, ccoffm, ccofmi "
		        ."  FROM ".$wbasedato."_000003 "
	 		    ." WHERE ccocod='".$wcco."'";
	 		          
		   $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	       $row = mysql_fetch_array($err);
	       
	       $mfueffa   =$row[1];
	       $mnrofac   =$row[0]."-".($row[2]+1);     
	       
           echo "<option selected>".$wtipfac."</option>";  
           echo "<option>Automatica</option>";
          }  
    else  
       {
        echo "<option>Automatica</option>";
        echo "<option>Manual</option>";
        //$wtipfac="Automatica";
        //echo "<input type='HIDDEN' name='wtipfac' value='".$wtipfac."'>";
       } 
  //echo "</select></td>";   
  if (isset($wtipfac) and $wtipfac=="Manual")
     echo "</select><b><font size=4>*** Proxima Factura: ".$mnrofac."</font></b></td>";   
    else
       echo "</select></td>";
  
  
  //." -- Proxima Factura: ".$mnrofac
  
  //////////////////////////////////////////////////////////////////
  //DOCUMENTO DEL CLIENTE
  echo "<tr>";
  if (isset($wdocpac)) //Si ya fue digitado el documento del cliente
     {
      if ($wdocpac != "9999" and $wdocpac != "")
         {
	      $q= "SELECT clidoc, clinom, clite1, clidir, climai, clitip, clipun "
	         ."  FROM ".$wbasedato."_000041 "
	         ." WHERE clidoc = '".$wdocpac."'";
	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);   
	      if ($num1 > 0)
	         {
		      $row1 = mysql_fetch_array($res1);
		      
		      ////
		      $wnompac=$row1[1];
		      $wte1pac=$row1[2];
		      $wdirpac=$row1[3];
		      $wmaipac=$row1[4];
		      $wcarpun=$row1[6];
		      
		      echo "<input type='HIDDEN' name='wcarpun' value='".$wcarpun."'>";
		      
		      //=====================================================
              //Modifico los campos consultados en linea
              //=====================================================
              echo "<script language='Javascript'>";
              echo "document.ventas.wnompac.value='".$wnompac."';";
              echo "document.ventas.wte1pac.value='".$wte1pac."';";
              echo "document.ventas.wdirpac.value='".$wdirpac."';";
              echo "document.ventas.wmaipac.value='".$wmaipac."';";
              echo "document.ventas.wcarpun.value='".$wcarpun."';";
			  echo "</script>";
			  //=====================================================
		      
		      ////
		      
		      /*
	          if (isset($wnompac) and $wnompac == "CLIENTE PARTICULAR") $wnompac=$row1[1];  //Si el Nombre esta setiado y es diferente al almacenado
	          if (isset($wte1pac) and $wte1pac == "SIN DATO") $wte1pac=$row1[2];            //Si el Telefono esta setiado y es diferente al almacenado
	          if (isset($wdirpac) and $wdirpac == "SIN DATO") $wdirpac=$row1[3];            //Si la Direccion esta setiada y es diferente a la almacenada
	          if (isset($wmaipac) and $wmaipac == "SIN DATO") $wmaipac=$row1[4];            //Si la Direccion esta setiada y es diferente a la almacenada
	          if (isset($wcarpun) and $wcarpun == "000000" and $row1[6] != "" and $row1[6] != "000000")  //Tarjeta de puntos
	             {
			      unset ($wcarpun);   
		          $wcarpun=$row1[6];  //Tarjeta de puntos
		          echo "<input type='HIDDEN' name='wcarpun' value='".$wcarpun."'>";
		         }
	          
	          echo "<input type='HIDDEN' name='wnompac' value='".$wnompac."'>";
	          echo "<input type='HIDDEN' name='wte1pac' value='".$wte1pac."'>";
	          echo "<input type='HIDDEN' name='wdirpac' value='".$wdirpac."'>";
	          echo "<input type='HIDDEN' name='wmaipac' value='".$wmaipac."'>";
	          */
	          
	          $wclitip=$row1[5];
	         }
	     echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Documento: </font></b><INPUT TYPE='text' NAME='wdocpac' VALUE='".$wdocpac."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false' ></td>";   //wdocpac     
         }
        else
           echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Documento: </font></b><INPUT TYPE='text' NAME='wdocpac' VALUE='".$wdocpac."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false'></td>";   //wdocpac     
  
      //SIN IMPORTAR SI EL CLIENTE ES DIFERENTE A 9999 O IGUAL BUSCO EN EL TIPO DE CLIENTE SI HAY DESCUENTO     
      //ACA CONSULTO SI EL TIPO DE CLIENTE ESPECIAL TIENE DESCUENTO O BONO DE DESCUENTO PARA APLICARLO LUEGO EN LA VENTA
	  $q= "SELECT clepde, clevde, clelin "
	   	 ."  FROM ".$wbasedato."_000041, ".$wbasedato."_000042 "         //Tabla tipos de clientes
	     ." WHERE clidoc  = '".$wdocpac."'"
	     ."   AND clitip  = clecla "
	     ."   AND clefid <= '".$wfecha."'"
	     ."   AND cleffd >= '".$wfecha."'"
	     ."   AND cleest  = 'on' "
	     ."   AND clelin  != 'NO APLICA' "
	     ."   AND clelin  != '' ";
	  $res1 = mysql_query($q,$conex);
	  $num1 = mysql_num_rows($res1); 
	  
	  if ($num1 > 0)
	    {
		 $row1 = mysql_fetch_array($res1);
	     $wpdepac=($row1[0]/100);   
	     $wvdepac=$row1[1]; 
	     $wlinpac=$row1[2]; 
	    }
	   else
	      {
	       $wpdepac=0;
           $wvdepac=0;
           $wlinpac="";        
          } 
     } 
    else 
       {
        echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Documento: </font></b><INPUT TYPE='text' NAME='wdocpac' VALUE='9999' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";              //wdocpac
        $wpdepac=0;
        $wvdepac=0;
        $wlinpac="";
       } 
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //DIRECCION DEL CLIENTE     
  if (isset($wdirpac)) //Si ya fue digitado el nombre del cliente    
     echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Dirección: </font></b><INPUT TYPE='text' NAME='wdirpac' VALUE='".$wdirpac."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";       //wdirpac
    else 
       echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Dirección: </font></b><INPUT TYPE='text' NAME='wdirpac' VALUE='SIN DATO' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38) event.returnValue = false'></td>";         //wdirpac
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //E-MAIL DEL CLIENTE     
  if (isset($wmaipac)) //Si ya fue digitado el nombre del cliente    
     echo "<td bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> E-Mail: </font></b><INPUT TYPE='text' NAME='wmaipac' SIZE=40 VALUE='".$wmaipac."' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";  //wmaipac
    else 
       echo "<td bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> E-Mail: </font></b><INPUT TYPE='text' NAME='wmaipac' SIZE=40 VALUE='SIN DATO' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38)  event.returnValue = false'></td>";    //wmaipac     
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CUOTA MODERADORA     
  if (isset($wcuotamod)) //Si ya fue digitado el nombre del cliente    
     echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Franquicia o Cuota Moderadora: </font></b><INPUT TYPE='text' NAME='wcuotamod' VALUE='".$wcuotamod."'></td>";          //wnompac
    else 
       echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Franquicia o Cuota Moderadora: </font></b><INPUT TYPE='text' NAME='wcuotamod' VALUE='0'></td>";            //wnompac          
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //BONOS DE DESCUENTO
  if (!isset($wbondto))
	  $q =  " SELECT boncod, bondes "
	       ."   FROM ".$wbasedato."_000048 "
	       ."  ORDER BY boncod ";
	 else
	    {
		 $wbondto1=explode("-",$wbondto);   
	     $q =  " SELECT boncod, bondes "
	          ."   FROM ".$wbasedato."_000048 "
	          ."  WHERE boncod != ('".trim($wbondto1[0])."')"
	          ."  ORDER BY boncod ";     
        }     
        
  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
  
  echo "<td align=left bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg."> Bonos de Dcto: <br></font></b><select name='wbondto' onchange='enter()'>";
  if (isset($wbondto))
     echo "<option selected>".$wbondto."</option>";
  for ($i=1;$i<=$num;$i++)
     {
	  $row = mysql_fetch_array($res); 
	  echo "<option>".$row[0]." - ".$row[1]."</option>";
     }
  echo "</select></td>";
  echo "</tr>";
  
  //ACA DEFINO LAS COLUMNAS A MOSTRAR DEPENDIENDO DE LOS DATOS ADICIONALES QUE SE PIDAN EN PANTALLA
  if (isset($wmedi) or isset($wprog) or isset($wremi) or isset($wdiag) or isset($wran))
     {
      echo "<tr>"; 
      $wcf="6699CC";
      if ($wmedi=="on" and $wprog=="on" and $wremi=="on")
         {
	      $wcolmed1=1;
	      $wcolmed2=1;
	      $wcolprog=1;
	      $wcolrem1=1;   
	      $wcolrem2=2; 
         }
         
      if ($wmedi=="on" and $wprog=="on" and $wremi=="off")
         {
	      $wcolmed1=1;
	      $wcolmed2=2;
	      $wcolprog=3;
	     }
      
      if ($wmedi=="on" and $wprog=="off" and $wremi=="off")
         {
	      $wcolmed1=1;
	      $wcolmed2=5;
	      if (isset($wdiag) and $wdiag=="on")
	         $wcolmed2=1;
	     }  
	  if ($wmedi=="off" and $wprog=="on" and $wremi=="on")
         {
	      $wcolprog=3;
	      $wcolrem1=1;
	      $wcolrem2=2;
	     }   
	  if ($wmedi=="off" and $wprog=="off" and $wremi=="on")
	     {
          $wcolrem1=1;
          $wcolrem2=5;
         }
      if ($wmedi=="on" and $wprog=="off" and $wremi=="on")
	     {
		  $wcolmed1=1;
	      $wcolmed2=1;   
          $wcolrem1=1;
          $wcolrem2=3;
         }   
      if ($wmedi=="off" and $wprog=="on" and $wremi=="off")
         $wcolprog=6; 
         
      if ($wdiag=="on" and $wran=="on" and $wtde="on")
	     {
		  $wcoldia1=1;
	      $wcoldia2=1;   
          $wcolran=1;
          $wcoltde=1;
         }         
	 } 
    
  	 
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //MEDICO QUE FORMULA
  if (isset($wmedi) and $wmedi=="on")
     {
	  if ($wcolmed2 > 0)
         $wancho=40;
        else
           $wancho=20;    
	     
	  if (isset($wcodmed) and ($wcodmed != ""))
	     { 
		  $q =  " SELECT medcod, mednom "
		       ."   FROM ".$wbasedato."_000051 "
		       ."  WHERE medest = 'on' "
		       ."    AND medcod = '".$wcodmed."'"
		       ."  ORDER BY mednom ";
	     	        
	      $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
	      if ($num > 0)
	         { 
		      $row = mysql_fetch_array($res);   
		      $wcodmed = $row[0];
		      $wnommed = $row[1];   
		      $wmedico = $row[0]." - ".$row[1];
		      
		      /////
		      echo "<script language='Javascript'>";
              echo "document.ventas.wnommed.value='".$wnommed."';";
              echo "</script>";
		      /////
		      
		      
		      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed1."><b><font text color=".$wclfg."> Medico: </font></b><INPUT TYPE='text' NAME='wcodmed' VALUE='".$wcodmed."' ></td>";             //wcodmed  
		      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnommed' VALUE='".$wnommed."' size = '".$wancho."' onchange='enter()'></td>";   //wnommed  
	         }
	        else
	           {
		        echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed1."><b><font text color=".$wclfg."> Medico: </font></b><INPUT TYPE='text' NAME='wcodmed' ></td>";                                //wcodmed  
			    echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnommed' size = '".$wancho."' onchange='enter()'></td>";                      //wnommed     
	           }  
         }
        else
           {
	        if (isset($wnommed) and ($wnommed != ""))
			   {   
				$wnommed=str_replace(" ","%",$wnommed);  
			    $q =  " SELECT medcod, mednom "
			         ."   FROM ".$wbasedato."_000051 "
			         ."  WHERE medest = 'on' "
			         ."    AND mednom like '%".$wnommed."%'"
			         ."  ORDER BY mednom ";
			    $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
			    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
				
			    if ($num > 0)
			       {  
				    echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed1."><b><font text color=".$wclfg."> Medico: </font></b><INPUT TYPE='text' NAME='wcodmed' ></td>";    //wcodmed  
				    for ($i=1;$i<=$num;$i++)
				       {
					    $row = mysql_fetch_array($res); 
				        if ($num == 1) 
				           {
					        $wcodmed=$row[0];
				            ///echo "<input type='HIDDEN' name='wcodmed' value='".$wcodmed."'>";   
				            /////
				            echo "<script language='Javascript'>";
				            echo "document.ventas.wcodmed.value='".$wcodmed."';";
				            echo "</script>";
						    /////			               
						   }
			              else
			                 {
				              if ($num > 1)           //Si entra por aca es porque el medico tiene varios registros con el nombre muy similar
				                 $wcodmed=$row[0];    
			                 }          
				        $wnommed1[$i]=$row[1];  
				       }
				       
				    echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed1."><b><font text color=".$wclfg."> </font></b><select name='wnommed' onchange='enter()' >";
				    for ($i=1;$i<=$num;$i++)
				       {
				        echo "<option>".$wnommed1[$i]."</option>";
				        if ($num == 1)   
				           $wnommed=$wnommed1[$i]; 
				       }
				    echo "</select></td>"; 
			       } 
	              else
	                 {
			 	      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed1."><b><font text color=".$wclfg."> Medico: </font></b><INPUT TYPE='text' NAME='wcodmed' ></td>";    //wcodmed  
					  echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnommed' size='".$wancho."' onchange='enter()'></td>";          //wnommed     
				     }
		         }
		        else
		           {
			 	    echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed1."><b><font text color=".$wclfg."> Medico: </font></b><INPUT TYPE='text' NAME='wcodmed' ></td>";    //wcodmed  
					echo "<td align=left bgcolor=".$wcf." colspan=".$wcolmed2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnommed' size='".$wancho."' onchange='enter()'></td>";          //wnommed     
				   } 	         
           }
     } 
  
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //PROGRAMA AL QUE ESTA AFILIADO O INSCRITO EL USUARIO
  if (isset($wprog) and $wprog=="on")
     {
	  if (isset($wprograma))
	     $q =  " SELECT procod, pronom "
		      ."   FROM ".$wbasedato."_000052 "
		      ."  WHERE proest = 'on' "
		      ."    AND procod != '".$wprograma."'"
		      ."  ORDER BY procod ";
	    else
	       $q =  " SELECT procod, pronom "
			     ."   FROM ".$wbasedato."_000052 "
			     ."  WHERE proest = 'on' "
			     ."  ORDER BY procod ";
		       
		 	        
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
		  
	  echo "<td align=left bgcolor=".$wcf." colspan=".$wcolprog."><b><font text color=".$wclfg."> Programa: </font></b><select name='wprograma' onchange='enter()' >";
		  
	  if (isset($wprograma))
	      echo "<option selected>".$wprograma."</option>";    
	
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
	     }
	  echo "</select></td>";
     }
     
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //REMITENTE
  if (isset($wremi) and $wremi=="on")
     {
	  if (isset($wcodrem) and ($wcodrem != ""))
	     { 
		  $q =  " SELECT remreg, remnom "
		       ."   FROM ".$wbasedato."_000058 "
		       ."  WHERE remest = 'on' "
		       ."    AND remreg = '".$wcodrem."'"
		       ."  ORDER BY remnom ";
	     	        
	      $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
	      
	      if ($num > 0)
	         { 
		      $row = mysql_fetch_array($res);   
		      $wcodrem = $row[0];
		      $wnomrem = $row[1];
		      $wremitente = $row[0]." - ".$row[1];
		      
		      /////
	          echo "<script language='Javascript'>";
	          echo "document.ventas.wnomrem.value='".$wnomrem."';";
	          echo "</script>";
			  /////
		      
		      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem1."><b><font text color=".$wclfg."> Remitente: </font></b><INPUT TYPE='text' NAME='wcodrem' VALUE='".$wcodrem."'></td>";  //wcodmed  
		      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnomrem' VALUE='".$wnomrem."'></td>"; /// onchange='enter()'></td>";          //wnommed  
	         }
	        else
	           {
		        echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem1."><b><font text color=".$wclfg."> Remitente: </font></b><INPUT TYPE='text' NAME='wcodrem' ></td>";    //wcodmed  
			    echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnomrem' onchange='enter()'></td>";          //wnommed     
	           }  
         }
        else
           {
	        if (isset($wnomrem) and ($wnomrem != ""))
			   {   
			    $q =  " SELECT remreg, remnom "
			         ."   FROM ".$wbasedato."_000058 "
			         ."  WHERE remest = 'on' "
			         ."    AND remnom like '%".$wnomrem."%'"
			         ."  ORDER BY remnom ";
			    $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
			    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
				
			    if ($num > 0)
			       {  
				    echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem1."><b><font text color=".$wclfg."> Remitente: </font></b><INPUT TYPE='text' NAME='wcodrem' ></td>";    //wcodmed  
				    for ($i=1;$i<=$num;$i++)
				       {
					    $row = mysql_fetch_array($res); 
				        if ($num == 1) 
				           {
				            $wcodrem=$row[0];
				            //echo "<input type='HIDDEN' name='wcodrem' value='".$wcodrem."'>"; 
				            
				            /////
				            echo "<script language='Javascript'>";
				            echo "document.ventas.wcodrem.value='".$wcodrem."';";
				            echo "</script>";
						    /////
				              
			               } 
				        $wnomrem1[$i]=$row[1];  
				       }
				    echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem1."><b><font text color=".$wclfg."> </font></b><select name='wnomrem' onchange='enter()' >";
				    for ($i=1;$i<=$num;$i++)
				       {
				        echo "<option>".$wnomrem1[$i]."</option>";
				        if ($num == 1)   
				           $wnomrem=$wnomrem1[$i]; 
				       }
				    echo "</select></td>";
			       } 
	              else
	                 {
			 	      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem1."><b><font text color=".$wclfg."> Remitente: </font></b><INPUT TYPE='text' NAME='wcodrem' ></td>";    //wcodmed  
					  echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnomrem'></td>"; /// onchange='enter()'></td>";          //wnommed     
				     }
		       }
		      else
		         {
			      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem1."><b><font text color=".$wclfg."> Remitente: </font></b><INPUT TYPE='text' NAME='wcodrem' ></td>";    //wcodmed  
			      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnomrem'></td>"; /// onchange='enter()'></td>";          //wnommed     
				 } 	         
           }
     } 
  
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //DIAGNOSTICO
  if (isset($wdiag) and $wdiag=="on")
     {
	  if (isset($wcoddia) and ($wcoddia != ""))
	     { 
		  $q =  " SELECT codigo, descripcion "
		       ."   FROM root_000011 "
		       ."  WHERE codigo = '".$wcoddia."'"
		       ."  ORDER BY 2 ";
	     	        
	      $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());
	      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
	      
	      if ($num > 0)
	         { 
		      $row = mysql_fetch_array($res);   
		      $wcoddia = $row[0];
		      $wnomdia = $row[1];
		      $wdiagnostico = $row[0]." - ".$row[1];
		      
		      /////
	          echo "<script language='Javascript'>";
	          echo "document.ventas.wnomdia.value='".$wnomdia."';";
	          echo "</script>";
			  /////
		      
			  echo "<td align=left bgcolor=".$wcf." colspan=".$wcoldia1."><b><font text color=".$wclfg."> Diagnostico: </font></b><INPUT TYPE='text' NAME='wcoddia' VALUE='".$wcoddia."'></td>";    
		      echo "<td align=left bgcolor=".$wcf." colspan=".$wcoldia2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnomdia' VALUE='".$wnomdia."' onchange='enter()' size=60></td>";         
	         }
	        else
	           {
		        echo "<td align=left bgcolor=".$wcf." colspan=".$wcoldia1."><b><font text color=".$wclfg."> Diagnostico: </font></b><INPUT TYPE='text' NAME='wcoddia' ></td>";     
			    echo "<td align=left bgcolor=".$wcf." colspan=".$wcoldia2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnomdia' onchange='enter()'></td>";    
	           }  
         }
        else
           {
	        if (isset($wnomdia) and ($wnomdia != ""))
			   {   
			    $q =  " SELECT codigo, descripcion "
			         ."   FROM root_000011 "
			         ."  WHERE descripcion like '%".$wnomdia."%'"
			         ."  ORDER BY 2 ";
			    $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
			    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
				
			    if ($num > 0)
			       {  
				    echo "<td align=left bgcolor=".$wcf." colspan=".$wcoldia1."><b><font text color=".$wclfg."> Diagnostico: </font></b><INPUT TYPE='text' NAME='wcoddia' ></td>";      
				    for ($i=1;$i<=$num;$i++)
				       {
					    $row = mysql_fetch_array($res); 
				        if ($num == 1) 
				           {
				            $wcoddia=$row[0];
				            
				            /////
				            echo "<script language='Javascript'>";
				            echo "document.ventas.wcoddia.value='".$wcoddia."';";
				            echo "</script>";
						    /////  
			               } 
				        $wnomdia1[$i]=$row[1];  
				       }
				    echo "<td align=left bgcolor=".$wcf." colspan=".$wcoldia1."><b><font text color=".$wclfg."> </font></b><select name='wnomdia' onchange='enter()' >";
				    for ($i=1;$i<=$num;$i++)
				       {
				        echo "<option>".$wnomdia1[$i]."</option>";
				        if ($num == 1)   
				           $wnomdia=$wnomdia1[$i]; 
				       }
				    echo "</select></td>";
			       } 
	              else
	                 {
			 	      echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem1."><b><font text color=".$wclfg."> Diagnostico: </font></b><INPUT TYPE='text' NAME='wcoddia' ></td>";      
					  echo "<td align=left bgcolor=".$wcf." colspan=".$wcolrem2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnomdia' onchange='enter()'></td>";      
				     }
		         }
		        else
		           {
			        echo "<td align=left bgcolor=".$wcf." colspan=".$wcoldia1."><b><font text color=".$wclfg."> Diagnostico: </font></b><INPUT TYPE='text' NAME='wcoddia' ></td>";  
					echo "<td align=left bgcolor=".$wcf." colspan=".$wcoldia2."><b><font text color=".$wclfg."> </font></b><INPUT TYPE='text' NAME='wnomdia' onchange='enter()'></td>";
				   } 	         
           }
     }   
     
     
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //RANGO DEL USUARIO
  if (isset($wran) and $wran=="on")
     {
	  if (isset($wrango))
	     $q =  " SELECT rancod "
		      ."   FROM ".$wbasedato."_000091 "
		      ."  WHERE rancod != '".$wrango."'"
		      ."  ORDER BY 1 ";
	    else
	       $q =  " SELECT rancod "
			    ."   FROM ".$wbasedato."_000091 "
			    ."  ORDER BY 1 ";
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	
	  //creo una tabla dentro de un TD para ahorrar espacio con el campo que sigue
	  echo "<td align=left bgcolor=".$wcf." colspan=2>";
	  echo "<table>";
	  echo "<tr>";  
	  echo "<td align=left bgcolor=".$wcf." colspan=".$wcolran."><b><font text color=".$wclfg."> Rango: </font></b><select name='wrango' onchange='enter()' >";
		  
	  if (isset($wrango))
	      echo "<option selected>".$wrango."</option>";    
	
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
	     }
	  echo "</select></td>";
	  echo "</tr>";
     }   
     
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE DESPACHO
  if (isset($wtde) and $wtde=="on")
     {
	  if (isset($wtipdes) and trim($wtipdes)!="")
	     $q =  " SELECT descripcion "
		      ."   FROM det_selecciones "
		      ."  WHERE medico = '".$wbasedato."'"
		      ."    AND codigo = '014' "
		      ."    AND descripcion != '".$wtipdes."'"
		      ."  ORDER BY 1 desc ";
	    else
	       $q =  " SELECT descripcion "
			    ."   FROM det_selecciones "
			    ."  WHERE medico = '".$wbasedato."'"
		        ."    AND codigo = '014' "
			    ."  ORDER BY 1 desc ";
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	
	  echo "<tr>";	  
	  echo "<td align=left bgcolor=".$wcf." colspan=".$wcoltde."><b><font text color=".$wclfg."> Tipo Despacho: </font></b><select name='wtipdes' >";
		  
	  if (isset($wtipdes))
	      echo "<option selected>".$wtipdes."</option>";    
	
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]."</option>";
	     }
	  echo "</select></td>";
	  echo "</tr>";
	  echo "</table>";
	  echo "</td>";
	 }        
     
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   
  echo "</tr>";  
  
  ///====================================================================================================================================================
  ///===== A C A   E M P I E Z A N   L O S   R I P S ====================================================================================================
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //====== SI LA EMPRESA PIDE  **** R I P S ****
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (isset($wrips) and $wrips == "on")
     {
	  echo "<tr><td align=center colspan=6 bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>* * *   R I P S   * * *</b></font></td></tr>";
	     
	  echo "<tr>";   
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //TIPO DE USUARIO
	  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Tipo de Usuario: </font></b><select name='wtipusu' onchange='enter()'>";
	  if (isset($wtipusu))
	     {
		  $q =  " SELECT tuscod, tusdes "
	           ."   FROM root_000027 "
	           ."  WHERE tuscod != mid('".$wtipusu."',1,instr('".$wtipusu."','-')-1) "
	           ."    AND tusest = 'on' "
		       ."  ORDER BY 1 desc ";
		 }  
	    else
	       { 
	        $q =  " SELECT tuscod, tusdes "
	             ."   FROM root_000027 "
	             ."  WHERE tusest = 'on' "
		         ."  ORDER BY 1 desc ";
		   }      
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wtipusu))
	     echo "<option selected>".$wtipusu."</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";   
	  
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //TIPO DE IDENTIFICACION O DOCUMENTO
	  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Tipo de Dcto: </font></b><select name='wtipdto'>";
	  if (isset($wtipdto))
	     {
		  $q =  " SELECT codigo, descripcion "
	           ."   FROM root_000007 "
	           ."  WHERE codigo != mid('".$wtipdto."',1,instr('".$wtipdto."','-')-1) "
	           ."  ORDER BY 2, 1 ";
		 }  
	    else
	       { 
	        $q =  " SELECT codigo, descripcion "
	             ."   FROM root_000007 "
	             ."  ORDER BY 2, 1 ";
		   }      
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wtipdto))
	     echo "<option selected>".$wtipdto."</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";
	  
	  
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //MUNICIPIO
	  if (!isset($wmuni)) $wmuni="05001-MEDELLIN";
	  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Municipio: </font></b><select name='wmuni'>";
	  if (isset($wmuni))
	     {
		  $q =  " SELECT codigo, nombre "
	           ."   FROM root_000006 "
	           ."  WHERE medico = 'root' "
	           ."    AND codigo != mid('".$wmuni."',1,instr('".$wmuni."','-')-1) "
		       ."  ORDER BY 2, 1 ";
		 }  
	    else
	       { 
	        $q =  " SELECT codigo, nombre "
	           ."   FROM root_000006 "
	           ."  WHERE medico = 'root' "
	           ."  ORDER BY 2, 1 ";
		   }      
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wmuni))
	     echo "<option selected>".$wmuni."</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";
	  
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //ZONA
	  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Zona: </font></b><select name='wzona'>";
	  if (isset($wzona))
	     {
		  $q =  " SELECT zoncod, zondes "
	           ."   FROM root_000028 "
	           ."  WHERE zoncod != mid('".$wzona."',1,instr('".$wzona."','-')-1) "
	           ."    AND zonest = 'on' "
		       ."  ORDER BY 2 desc ";
		 }  
	    else
	       { 
	        $q =  " SELECT zoncod, zondes "
	             ."   FROM root_000028 "
	             ."  WHERE zonest = 'on' "
	             ."  ORDER BY 2 desc ";
		   }      
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wzona))
	     echo "<option selected>".$wzona."</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";
	  
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //SEXO
	  echo "<td align=left bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg.">Sexo: </font></b><select name='wsexo'>";
	  if (isset($wsexo))
	     {
		  $q =  " SELECT sexcod, sexdes "
	           ."   FROM root_000029 "
	           ."  WHERE sexcod != mid('".$wsexo."',1,instr('".$wsexo."','-')-1) "
	           ."    AND sexest = 'on' "
		       ."  ORDER BY 2, 1 ";
		 }  
	    else
	       { 
	        $q =  " SELECT sexcod, sexdes "
	             ."   FROM root_000029 "
	             ."  WHERE sexest = 'on' "
	             ."  ORDER BY 2, 1 ";
		   }      
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  if (isset($wsexo))
	     echo "<option selected>".$wsexo."</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]."-".$row[1]."</option>";
	     }
	  echo "</select></td>";
	  
	  echo "</tr>";     
	 
	  
	  echo "<tr>"; 
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //POLIZA  
	  if (isset($wpoliza)) //Si ya fue digitado el nombre del cliente    
	     echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Poliza: </font></b><INPUT TYPE='text' NAME='wpoliza' SIZE=30 VALUE=".$wpoliza."></td>";  //wpoliza
	    else 
	       echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Poliza: </font></b><INPUT TYPE='text' NAME='wpoliza' SIZE=30></td>";                   //wpoliza
	  
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //NRO DE AUTORIZACION  
	  if (isset($wauto)) //Si ya fue digitado el nombre del cliente    
	     echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Autorizacion: </font></b><INPUT TYPE='text' NAME='wauto' SIZE=30 VALUE=".$wauto."></td>";
	    else 
	       echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Autorizacion: </font></b><INPUT TYPE='text' NAME='wauto' SIZE=30></td>";
	  
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //FECHA VENCIMIENTO POLIZA  
 	  if (isset($wfecven)) //Si ya fue digitado el nombre del cliente    
 	     echo "<td align=left bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg."> Vence en: </font></b><INPUT TYPE='text' NAME='wfecven' SIZE=30 VALUE=".$wfecven."></td>";  //wpoliza
 	    else 
 	       echo "<td align=left bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg."> Vence en: </font></b><INPUT TYPE='text' NAME='wfecven' SIZE=30 VALUE='".$wfecha."'></td>";                   //wpoliza
	  
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //EDAD  
 	  if (isset($wedad)) //Si ya fue digitado el nombre del cliente    
 	     echo "<td align=left bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg."> Edad: </font></b><INPUT TYPE='text' NAME='wedad' SIZE=30 VALUE=".$wedad."></td>";
 	    else 
 	       echo "<td align=left bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg."> Edad: </font></b><INPUT TYPE='text' NAME='wedad' SIZE=30></td>";
	        
	  echo "</tr>";
     }
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //====== ACA TERMINA LA INFORMACION PARA  **** R I P S ****
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
         
  
  
  if (!isset($wventa) or ($wventa=="N"))
     echo "<tr><td colspan=10>&nbsp</td></tr>";  //Solo muestra esta linea antes de realizar la venta efectiva
	  
  if (isset($wclitip))
     if (($wclitip <> "GENERAL") and ($wclitip <> "NO APLICA"))
        echo "<tr bgcolor=#ffcc66><td align=center colspan=10>Cliente Especial: ".$wclitip."</td></tr>";  
        
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //ACA EVALUO CUANDO SE HACE LA VENTA
  if (!isset($wventa) or ($wventa=="N"))
     {
	  echo "<tr><td align=center colspan=10 bgcolor=".$wcf2."><font size=3 text color=#ffffff><b>* * *  BUSQUEDA DE ARTICULOS * * *</b></font></td></tr>";
	  echo "<tr>";
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //BUSQUEDA POR CODIGO O DESCRIPCION 
	  if ($wtiping=="C")   //Evaluo si el ingreso de articulos o busqueda se hace por codigo o descripcion
	     {
	      echo "<td bgcolor=".$wcf2."><b><font text color=".$wclfa."> Codigo      </font></b><input type='radio' name='wcons' VALUE=codart checked SIZE=2 ></td>";                //wcons
	      echo "<td bgcolor=".$wcf2."><b><font text color=".$wclfa."> Descripción </font></b><input type='radio' name='wcons' VALUE=desart SIZE=2 ></td>";                        //wcons 
	     }
	    else
	       {
	        echo "<td bgcolor=".$wcf2."><b><font text color=".$wclfa."> Codigo      </font></b><input type='radio' name='wcons' VALUE=codart SIZE=2 ></td>";                             //wcons
	        echo "<td bgcolor=".$wcf2."><b><font text color=".$wclfa."> Descripción </font></b><input type='radio' name='wcons' VALUE=desart checked SIZE=2 ></td>";                     //wcons 
	       }  
	  //Siempre que utilice esta opcion de javascript, se debe cargar la funcion ira() arriba en el BODY
	  ?>	    
	    <script>
	      function ira(){document.ventas.wdato.focus();}
	    </script>
	  <?php
	  //echo "<td bgcolor=#fffffff> <INPUT TYPE='text' NAME='wdato' onkeypress='if (event.keyCode >= 35 & event.keyCode <= 38 )  event.returnValue = false'></td>";                                                                  //wdato
	  
	  
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //====== A C A   E V A L U O   S I   S E   D I G I T A R O N   T O D O S   L O S   D A T O S   O B L I G A T O R I O S =======================
	  //=== No se habilita el campo donde se digita el codigo o la descripcion hasta que se digiten todos los datos obligatorios ===================
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  if ($wini=="N")
	     {
		  $whabilita_venta="ENABLED";
	      verifica_datos();
         } 
	  
	  if ($whabilita_venta == "DISABLED")
	     {
		  echo "<b>CAMPO QUE SE DEBE CORREGIR: (** ".$wvaldat." **)</b><br>";   
	      $wdato="";
	      echo "<input type='HIDDEN' name='wdato' value='".$wdato."'>";  //Esto lo hago porque no se esta enviando ningun dato en la linea de busquedad
	      ?>	    
	        <script>
	           alert ("FALTA ALGUN DATO POR INGRESAR O COLOCAR EL DATO CORRECTO"); 
	        </script>
	      <?php
         }   
	  //============================================================================================================================================
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
	     
	  echo "<td bgcolor=#fffffff> <INPUT TYPE='text' NAME='wdato' ".$whabilita_venta."></td>";                                                                  //wdato
	  
	  if (!isset($wdato) or ($wdato == ""))
	     echo "<td align=center bgcolor=#cccccc colspan=1><input type='submit' value='Consultar'></td>";                                   //submit 
	     
	  echo "</table>";   
	  echo "<center><table border>";   
		
	  if (isset($wempresa))
	     {	 
		  $wempresa1=explode("-",$wempresa);   
		  
		  $wemp=$wempresa1[0]; 
	      $wnitemp=$wempresa1[1];
	     }
	     
	  //ACA ELIMINO EL REGISTRO SELECCIONADO
	  if (isset($wborrar) and ($wborrar == 'S'))
	     {
	      $q="  DELETE FROM ".$wbasedato."_000034 "
	        ."   WHERE id = ".$wid;
	      $res = mysql_query($q,$conex);
	      $wborrar='N';
	     }   //fin del if $wborrar  
	     
	  //////////////////////////////////////////////////////////////////////////////////////////////   
	  //ACA TRAIGO LOS ARTICULOS QUE TENGAN TARIFA EN EL CONCEPTO DE VENTAS
	  if (isset($wcons) and !isset($wcan) and $wdato != "")
	     {
		  if ($wcons == "codart")
		     {
			  //==============================================================================================================   
			  //VERIFICO QUE EL CODIGO DIGITADO SEA EXTERNO O NO  ============================================================  
	          $q= "  SELECT axpart "
	             ."    FROM ".$wbasedato."_000009 "
	             ."   WHERE axpcpr = '".$wdato."'"
	             ."     AND axpest = 'on' ";
	          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		      $num = mysql_num_rows($res);
		      if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
		         {
			      $row = mysql_fetch_array($res);   
			      
			      $pos = strpos($row[0],"-");
	              $wdato = substr($row[0],0,$pos); 
			     }   
			  //==============================================================================================================   
			     
			  //==============================================================================================================
			  //AVERIGUO SI EL ARTICULO DIGITADO PERTENECE A UN GRUPO QUE MUEVA INVENTARIOS O NO
			  $q= "  SELECT gruinv, grumva "
	             ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000004 "
	             ."   WHERE artcod = '".$wdato."'"
	             ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
	             ."     AND artest = 'on' "
	             ."     AND gruest = 'on' ";
	          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		      $num = mysql_num_rows($res);
		      if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
		         {
			      $row = mysql_fetch_array($res);   
			      $WMUEINV = $row[0]; 
			      $WMODVAL = $row[1]; 
			     }
			    else 
			       {
			        $WMUEINV="";    
			        $WMODVAL="";
		           } 
			  //==============================================================================================================  
			  
			  
			  //==============================================================================================================  
			  //==============================================================================================================
			  if ($WMUEINV == 'on')
			     {
				  $q =  " SELECT artcod, artnom, mtavac, mtavan,  karexi, mtafec, artiva, artrec, artfre "
				       ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000007 "
				       ."  WHERE artcod                            = '".$wdato."'"
				       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
				       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
				       ."    AND empcod                            = '".$wemp."'"
				       ."    AND karcco                            = '".$wcco."'"
				       ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
				       ."    AND karcod                            = artcod "
				       ."    AND artest                            = 'on' "
				       ."    AND mtaest                            = 'on' "
				       ."    AND emptem                            = '".$wtipcli."'"
				       ."  ORDER BY artcod ";
			     }
			    else
			       {
				    if ($WMODVAL=="N")  //Si el valor es fijo osea por tarifa  
					    $q =  " SELECT artcod, artnom, mtavac, mtavan, 'serv', mtafec, artiva, artrec, artfre "
					         ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024 "
					         ."  WHERE artcod                            = '".$wdato."'"
					         ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
					         ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
					         ."    AND empcod                            = '".$wemp."'"
					         ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
					         ."    AND artest                            = 'on' "
					         ."    AND mtaest                            = 'on' "
					         ."    AND emptem                            = '".$wtipcli."'"
					         ."  ORDER BY artcod ";
					   else  //El codigo no tiene tarifa
					      $q =  " SELECT artcod, artnom, 0, 0, 'serv', 0, artiva "
					           ."   FROM ".$wbasedato."_000001 "
					           ."  WHERE artcod                            = '".$wdato."'"
					           ."    AND artest                            = 'on' "
					           ."  ORDER BY artcod ";     
			       }       
			 }
	      if ($wcons == "desart")
		     {
			  $q =  " SELECT artcod, artnom, mtavac, mtavan, karexi, mtafec, artiva, artrec, artfre "
			       ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000007, ".$wbasedato."_000004 "
			       ."  WHERE artnom                            like '%".$wdato."%'"
			       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
			       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
			       ."    AND empcod                            = '".trim($wemp)."'"
			       ."    AND karcco                            = '".$wcco."'"
			       ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
			       ."    AND karcod                            = artcod "
			       ."    AND artest                            = 'on' "
			       ."    AND mtaest                            = 'on' "
			       ."    AND emptem                            = '".$wtipcli."'"
			       ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
			       ."    AND gruinv                            = 'on' "
			       			       
				   ."  UNION "
				   
				   ." SELECT artcod, artnom, mtavac, mtavan, 'serv', mtafec, artiva, artrec, artfre "
			       ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000004 "
			       ."  WHERE artnom                            like '%".$wdato."%'"
			       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
			       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
			       ."    AND empcod                            = '".trim($wemp)."'"
			       ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
			       ."    AND artest                            = 'on' "
			       ."    AND mtaest                            = 'on' "
			       ."    AND emptem                            = '".$wtipcli."'"
			       ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
			       ."    AND gruinv                            = 'off' "
			       
			       //."  UNION "
			       
			       //." SELECT artcod, artnom, 0, 0, 'serv', 0, artiva, artrec, artfre "
				   //."   FROM ".$wbasedato."_000001 "
				   //."  WHERE artnom                            like '%".$wdato."%'"
				   //."    AND artest                            = 'on' "
				   ."  ORDER BY artnom ";
			 }   
		  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		  $num = mysql_num_rows($res);
	     
		  if ($num > 0) //El articulo existe y tiene tarifa, entra por el then
		     {
			  echo "</table>";   
			  echo "<table align=center border=1>";   
			  echo "<tr>";	    
			  echo "<td align=center><select name='warticulo'>";                                                //warticulo
			  for ($i=1;$i<=$num;$i++)
			     {
				  $row = mysql_fetch_array($res); 
				  
				  //=========================================================================================
				  //Esto lo hago para colocar todas las descripciones del mismo tamaño, osea de 60 caracteres
			      $j= 60-strlen($row[1]);
			      for ($k=1;$k<=$j;$k++)
			          $row[1]=$row[1].'&nbsp';
			          
			      //EL 3 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO         
			      $wporiva = 1+(round($row[6]/100));
			      if ($wfecha < $row[5])   //Aca evaluo si tomo el valor anterior o el actual
			         $wval = $row[3];      //*$wporiva;    //Valor anterior
			        else
			           $wval = $row[2];    //*$wporiva;  //Valor actual 
			      //=========================================================================================
			      
			      //=============================================================================================
			      //ACA EVALUO SI EL ARTICULO TIENE RECAMBIO Y SI TODAVIA ESTA VIGENTE EL RECAMBIO SEGUN LA FECHA
			      //=============================================================================================
			      if ($wfecha <= $row[8])      //Aca evaluo si la fecha de recambio esta vigente
			         $wrecambio = $row[7];     //Variable de recambio
			        else
			           $wrecambio = "off";     //Variable de recambio
			      
			      if ($wrecambio == "on")
			         echo "<b><option>".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,0,'.',',')." | ".$row[4]." | *** TIENE RECAMBIO ***</option></b>";
			        else
			           echo "<option>".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,0,'.',',')." | ".$row[4]."</option>"; 
			     }
			  echo "</select></td>";
			  
			  if (isset($WMODVAL) and $WMODVAL == "on") //No tiene tarifa
				 {
				  ?>	    
				    <script>
				      function ira(){document.ventas.wvalser.focus();}
				      function ira(){document.ventas.wvalser.select();}
				    </script>
				  <?php
				  echo "<td bgcolor=".$wcf."><BLINK>Valor <INPUT TYPE='text' NAME='wvalser' VALUE=1 onkeypress='if ((event.keyCode < 48 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></td>";    //wcan
			     }
			  
			  
		      ?>	    
		        <script>
		          function ira(){document.ventas.wcan.focus();}
		          function ira(){document.ventas.wcan.select();}  //Deja seleccionado el valor por defecto
		        </script>
		      <?php
		      
		      
		      //===================================================================================================================
		      //Enero 30 de 2009 ==================================================================================================
		      ///if ($wcons=="codart")
		         echo "<td bgcolor=".$wcf.">Cantidad <INPUT TYPE='text' NAME='wcan' VALUE=1 onkeypress='if ((event.keyCode < 48 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></td>";    //wcan
		      /*  else
		           { 
		            $wcan=0;
		            echo "<input type='HIDDEN' name='wcan' value='".$wcan."'>"; 
	               } */
	          //===================================================================================================================     
		      
		      
			  echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";                                         //submit 
		      echo "</tr>";
		      echo "</table>";
		      echo "<table align=center border=1>";
			  
		      $wventa="N";
		      $wdesemp=0;
		      $wrecemp=0;
		      $wdesart=0;
		      mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac, $wlinpac);
		     }
	        else  //Si el articulo no existe o no tiene tarifa para la empresa seleccionada
	           {
		        ///========================================================================================================
		        ///TARIFA DE COBRO POR GRUPO    
		        ///========================================================================================================
		        ///Si no encontro tarifa para el articulo busco si existe tarifa o % de utilidad para el grupo del articulo 
		        if ($wcons=="codart")
		           {
			        //VERIFICO QUE EL CODIGO DIGITADO SEA EXTERNO O NO  ============================================================  
			        $q= "  SELECT axpcpr "
			           ."    FROM ".$wbasedato."_000009 "
			           ."   WHERE axpcpr = '".$wdato."'"
			           ."     AND axpest = 'on' ";
			        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				    $num = mysql_num_rows($res);
				    if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
				       {
					    $row = mysql_fetch_array($res);   
			            $wdato=$row[0];
			            $whomolo="S";
		               }
		              else
		                 $whomolo="N";
			           
		            $q= "  SELECT artcod, artnom, (karpro+(karpro*(tgrpac/100))), (karpro+(karpro*(tgrpan/100))), karexi, tgrfec "
				       ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000007, ".$wbasedato."_000027, ".$wbasedato."_000024 "
				       ."   WHERE artcod                             = '".$wdato."'"
				       ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
				       ."     AND empcod                             = '".$wemp."'"
				       ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
				       ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
				       ."     AND artcod                             = karcod "
				       ."     AND karcco                             = '".$wcco."'"
				       ."     AND tgrest                             = 'on' "
				       ."     AND artest                             = 'on' "
				       ."   ORDER BY artnom "; 
			       }    
		        if ($wcons=="desart")
		           {
			        $q= "  SELECT artcod, artnom, (karpro+(karpro*(tgrpac/100))), (karpro+(karpro*(tgrpan/100))), karexi, tgrfec "
			           ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000007, ".$wbasedato."_000027, ".$wbasedato."_000024 "
			           ."   WHERE artnom                             like '".$wdato."'"
			           ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
			           ."     AND empcod                             = '".$wemp."'"
			           ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
			           ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
			           ."     AND artcod                             = karcod "
			           ."     AND karcco                             = '".$wcco."'"
			           ."     AND tgrest                             = 'on' "
			           ."     AND artest                             = 'on' "
			           ."     AND tgrfec                            <= '".$wfecha."'"
			           ."     AND (tgrpac                            > 0 "
			           ."      OR  tgrpan                            > 0) "
			           ."     AND emptem                             = '".$wtipcli."'"
			           ."  ORDER BY artnom "; 
		           }    
		        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);
			     
				if ($num > 0) //El articulo existe y tiene tarifa, entra por el then
				   {
					echo "</table>";   
					echo "<table align=center border=1>";
					echo "<tr>";  
				    echo "<td align=center><select name='warticulo'>";                         //warticulo
				    //echo "<option>&nbsp</option>";   
				    for ($i=1;$i<=$num;$i++)
				       {
				         $row = mysql_fetch_array($res); 
					     //=========================================================================================
						 //Esto lo hago para colocar todas las descripciones del mismo tamaño, osea de 60 caracteres
					     $j= 60-strlen($row[1]);
					     for ($k=1;$k<=$j;$k++)
					         $row[1]=$row[1].'&nbsp';
					         
					     if ($wfecha < $row[5])   //Aca evaluo si tomo el valor anterior o el actual
				            $wval = $row[3];      //Valor anterior
				           else
				              $wval = $row[2];    //Valor actual
					     //=========================================================================================
					     echo "<option>".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,2,'.',',')." | ".$row[4]."</option>";
					   }
					echo "</select></td>";
					
					?>	    
				      <script>
				        function ira(){document.ventas.wcan.focus();}
				        function ira(){document.ventas.wcan.select();}
				      </script>
				    <?php
					echo "<td bgcolor=".$wcf."><BLINK>Cantidad <INPUT TYPE='text' NAME='wcan' VALUE=1 onkeypress='if ((event.keyCode < 48 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></td>";    //wcan
					echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";                            //submit 
				    echo "</tr>";
				    //echo "</table>";
					  
				    $wventa="N";
				    $wdesemp=0;
		            $wrecemp=0;
		            $wdesart=0;
		            mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac, $wlinpac);
			       } 
			      else
			         { 
				      //===========================================================================================
				      //Aca hago la busqueda del motivo por el cual NO sale el articulo al momento de irlo a vender
				      //===========================================================================================
				      if ($wcons=="codart")
		                 {
			              $q =  " SELECT count(*) "
						       ."   FROM ".$wbasedato."_000001 "
						       ."  WHERE artcod = '".$wdato."'"
						       ."    AND artest = 'on' ";
						  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						  $num = mysql_num_rows($res);
					      $row = mysql_fetch_array($res); 
					      
					      echo "<table align=center border=1>";
					      echo "<tr>";
					      if ($row[0] == 0) 
					         if ($whomolo == "S")
						        echo "<td bgcolor=#99FFCC colspan=".($wcol-5).">El Articulo No existe o Esta inactivo en el Maestro de Articulos</TD>";     
						       else
						          echo "<td bgcolor=#99FFCC colspan=".($wcol-5).">El Articulo No ha sido homologado</TD>";      
					        else
						       {
							    $q =  " SELECT count(*) "
						             ."   FROM ".$wbasedato."_000026, ".$wbasedato."_000024 "
						             ."  WHERE mid(mtaart,1,instr(mtaart,'-')-1) = '".$wdato."'"
						             ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
						             ."    AND empcod                            = '".trim($wemp)."'"
						             ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".trim($wcco)."'"
						             ."    AND mtaest                            = 'on' "
						             ."    AND emptem                            = '".$wtipcli."'";
							    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							    $num = mysql_num_rows($res);
							    $row = mysql_fetch_array($res); 
							    
							    if ($row[0] == 0)   
						           echo "<td bgcolor=#99FFCC colspan=".($wcol-5).">El Articulo No tiene tarifa para la sucursal y responsable seleccionado</TD>";
						          else
						             {
						              $q =  " SELECT count(*) "
						                   ."   FROM ".$wbasedato."_000007, ".$wbasedato."_000024 "
							               ."  WHERE karcod = '".$wdato."'"
							               ."    AND karcco = '".trim($wcco)."'"
							               ."    AND karexi > 0 ";
							          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							          $num = mysql_num_rows($res);
							          $row = mysql_fetch_array($res); 
							          if ($row[0] == 0)   
						                 echo "<td bgcolor=#99FFCC colspan=".($wcol-5).">El Articulo No tiene existencias en esta sucursal</TD>";
						             } 
					           }   
					      }     
				      //===========================================================================================   
			          echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";                        //submit 
			          echo "</table>";
		              echo "<table align=center border=1>";
			          $wventa="N";
			          $wdesemp=0;
		              $wrecemp=0;
		              $wdesart=0;
		              mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac);
		             }
	           }     
		 }
	   else
	       //===========================================================================================================================
	       //===========================================================================================================================
	       //ACA ESTAN LOS DATOS SETIADOS   
	       //===========================================================================================================================
	       //===========================================================================================================================
		   {
			echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";   
			//echo "<input type='HIDDEN' name='wmueinv' value='".$WMUEINV."'>";
			   
			if (isset($warticulo))
			   {
				$pos = strpos($warticulo,"|");
		        $wart = substr($warticulo,0,$pos-1);   
		        
		        
		        if (isset($wprog) and $wprog=="on")
		           {
			        echo "<script>window.open('buscar_ventas_anteriores.php?wbasedato=".$wbasedato."&wdocpac=".$wdocpac."&wart=".$wart."&wfecha=".$wfecha."','','height=400,width=600, top=200 left=200,scrollbars=yes')</script>";
	               }
				   
				////////////////////////////////////////
				////////////////////////////////////////
				if (isset($wbondto) and $wbondto != "NO APLICA - NO APLICA")
				   {
				    $wbondto1=explode("-",$wbondto);   
					  
				    //ACA BUSCO SI EL BONO TIENE DESCUENTO
				    $q = " SELECT linea, sublinea, descuento, recargo "
				        ."   FROM ".$wbasedato."_000047 "
				        ."  WHERE mid(bono,1,instr(bono,'-')-1) = '".trim($wbondto1[0])."'"
				        ."    AND fecha_ini <= '".$wfecha."'"
				        ."    AND fecha_fin >= '".$wfecha."'"
				        ."    AND hora_ini  <= '".$hora."'"
				        ."    AND hora_fin  >= '".$hora."'";
				    $res_desc = mysql_query($q,$conex);
				    $num_desc = mysql_num_rows($res_desc);
				      
				    if ($num_desc > 0)
				       { 
				        $row_desc = mysql_fetch_array($res_desc); 
				        $wlin_bon=$row_desc[0];      //Linea
				        $wsub_bon=$row_desc[1];      //Sublinea
				        $wdes_bon=$row_desc[2];      //Descuento
				        $wrec_bon=$row_desc[3];      //Recargo
				       }
				      else
				         {
				          $wlin_bon="";              //Linea
				          $wsub_bon="";              //Sublinea
				          $wdes_bon=0;               //Descuento
				          $wrec_bon=0;               //Recargo 
			             } 
				   }   
				   
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	            //========================================================================================================================\\
	            //SI HAY DESCUENTO POR BONO BUSCO SI HAY ALGUN ARTICULO DE LA VENTA QUE PERTENEZCA A LA LINEA QUE TIENE DESCUENTO         \\
	            //========================================================================================================================\\
	             if (isset($wdes_bon) and $wdes_bon > 0)
	                {
		             if ($wsub_bon != "NO APLICA")
		                $wlinea_bon = substr($wlin_bon,0,strpos($wlin_bon,"-"))."-".substr($wsub_bon,0,strpos($wsub_bon,"-"));
		               else
		                  $wlinea_bon = substr($wlin_bon,0,strpos($wlin_bon,"-"))."%"; 
		                
		             $q = "SELECT count(*) "
		                 ."  FROM ".$wbasedato."_000001"
		                 ." WHERE artcod = '".$wart."'"       //Articulo
		                 ."   AND artgru like '".$wlinea_bon."'"   //Linea
		                 ."   AND artest = 'on' ";
		             $res_lin = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		             $num_lin = mysql_num_rows($res_lin); 
		              
		             $row_lin = mysql_fetch_array($res_lin);
		             
		             if ($row_lin[0] == 0)
		                $wdcto_bon=0;
		               else
		                  $wdcto_bon=$wdes_bon/100; 
	                } 
	               else
	                  $wdcto_bon=0;   
				   
				////////////////////////////////////////
				////////////////////////////////////////   
				   
				$wini="N";   
				echo "<input type='HIDDEN' name= 'wini' value='N'>";                                            //wini
		        echo "<input type='HIDDEN' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";                    //wfecha_tempo
		        echo "<input type='HIDDEN' name= 'whora_tempo' value='".$whora_tempo."'>";                      //whora_tempo
		        echo "<input type='HIDDEN' name= 'wpdepac' value='".$wpdepac."'>";                              //wpdepac
		        echo "<input type='HIDDEN' name= 'wvdepac' value='".$wvdepac."'>";                              //wvdepac
		        echo "<input type='HIDDEN' name= 'wlinpac' value='".$wlinpac."'>";                              //wlinpac
		           
		        //==============================================================================================================
	            //AVERIGUO SI EL ARTICULO DIGITADO PERTENECE A UN GRUPO QUE MUEVA INVENTARIOS O NO
			    $q= "  SELECT gruinv, grumva "
	               ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000004 "
	               ."   WHERE artcod = '".$wart."'"
	               ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
	               ."     AND artest = 'on' "
	               ."     AND gruest = 'on' ";
	            $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		        $num = mysql_num_rows($res);
		        if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
		           {
			        $row = mysql_fetch_array($res);   
			        $WMUEINV = $row[0]; 
			        $WMODVAL = $row[1];
			       }   
			    //==============================================================================================================
		        
			    if ($WMUEINV == 'on')
			       {
					$q =  " SELECT artcod, artnom, unides, mtavac, artiva, karexi, karpro, mtavan, mtafec, emppdt, empprt, mtapde "
					     ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000002, ".$wbasedato."_000007 "
					     ."  WHERE artcod                            = '".$wart."'"
					     ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
					     ."    AND artest                            = 'on' "
					     ."    AND mtaest                            = 'on' "
					     ."    AND unicod                            = mid(artuni,1,instr(artuni,'-')-1) "
					     ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
					     ."    AND karcco                            = '".$wcco."'"
				         ."    AND karcod                            = artcod "
				         ."    AND karexi                           >= ".$wcan
				         ."    AND empcod                            = '".$wemp."'"
				         ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
				         ."    AND emptem                            = '".$wtipcli."'";
			       }
			      else
			         {
				      if ($WMODVAL == "N")   
						  $q =  " SELECT artcod, artnom, unides, mtavac, artiva, 'serv', 0, mtavan, mtafec, emppdt, empprt, mtapde "
						       ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000024, ".$wbasedato."_000002, ".$wbasedato."_000004 "
						       ."  WHERE artcod                            = '".$wart."'"
						       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
						       ."    AND artest                            = 'on' "
						       ."    AND mtaest                            = 'on' "
						       ."    AND unicod                            = mid(artuni,1,instr(artuni,'-')-1) "
						       ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
						       ."    AND empcod                            = '".$wemp."'"
						       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
						       ."    AND emptem                            = '".$wtipcli."'"
						       ."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
					           ."    AND gruinv                            = 'off' ";
					    else
					       $q = " SELECT artcod, artnom, 0, 0, 'serv', 0, artiva "
						       ."   FROM ".$wbasedato."_000001 "
						       ."  WHERE artnom                            like '%".$wdato."%'"
						       ."    AND artest                            = 'on' "
						       ."  ORDER BY artnom ";      
					 }       
				
			    $res = mysql_query($q,$conex); //or die (mysql_errno()." - ".mysql_error());
			    $num = mysql_num_rows($res);   //or die (mysql_errno()." - ".mysql_error());
			    
			    if ($num > 0)
			       {
				    $row = mysql_fetch_array($res); 
			        //$wart    = $row[0];
			        $wdes    = $row[1];
			        $wuni    = $row[2];
			        $wvac    = $row[3];
			        $wporiva = $row[4];
			        $wcospro = $row[6];
			        if ($WMUEINV == 'on' or $WMODVAL == "N")
			           {
				        $wvan    = $row[7];
				        $wfeccam = $row[8];
				        $wdesemp = ($row[9]/100);
		                $wrecemp = ($row[10]/100);
		                $wdesart = ($row[11]/100);
	                   } 
	                
			        if ($wfecha < $wfeccam)   //Aca evaluo si tomo el valor anterior o el actual
			           $wval = $wvan;
			          else
			             $wval = $wvac;
			             
			        //Si el valor si digito entonces lo tomo como el valor a cobrar     
			        if (isset($wvalser) and ($wvalser > 0))     
			           $wval=$wvalser;
				           
			        //////////////////////////////////////////////////////////////////////////////////////////////////////////////      
			        //CALCULO DEL IVA ============================================================================================     
			        //EL 3 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO     
			        //$wvaliva = (integer)($wcan*$wval*($wporiva/100));
			        //$wvaltot = (integer)(($wcan*$wval)+($wcan*$wval*($wporiva/100)));
			        if ($wporiva > 0)
			     	   $wvaliva = round((($wcan*$wval)-(($wcan*$wval)/(1+($wporiva/100)))));
			     	  else
			     	     $wvaliva=0; 
			     	$wvaltot = round(($wcan*$wval));
			     	
			     	if ($wcan > 0)
			           {		    
				        //Si entra por aca es porque ya se valido y por ende puede grabar el articulo en la tabla TEMPORAL
	     	            $q= " INSERT INTO ".$wbasedato."_000034 (Medico          ,   Fecha_data ,   Hora_data,   temusu      ,   temfec           ,   temhor          ,   temsuc  ,   temcaj   ,   temtcl     ,   temres  ,   temdcl     ,   temncl     ,   temart ,    temdes  ,   tempre  ,  temcan ,  temvun ,  tempiv    ,  temiva    ,  temtot     , temcmo      ,  temcpr    ,  temdem    ,  temrem    ,  temdar    ,  temdpa    ,  tembpa    ,  temdbo      , temrbo      ,    temcpu      ,   temlpa     ,   Seguridad) "
		                   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wcco."','".$wcaja."','".$wtipcli."','".$wemp."','".$wdocpac."','".$wnompac."','".$wart."','".$wdes."','".$wuni."',".$wcan.",".$wval.",".$wporiva.",".$wvaliva.",".$wvaltot.",".$wcuotamod.",".$wcospro.",".$wdesemp.",".$wrecemp.",".$wdesart.",".$wpdepac.",".$wvdepac.",".$wdcto_bon.",0            , '".$wcarpun."','".$wlinpac."', 'C-".$wusuario."')";
		                $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	                   } 
		            
		            $wventa="N";
		            mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac, $wlinpac);
		           }
		           else  //Si el articulo no tiene la cantidad digitada con tarifa POR ARTICULO, busco la cantidad pero con tarifa por grupo
	                  {
		               $q= "  SELECT artcod, artnom, unides, (karpro+(karpro*(tgrpac/100))), artiva, karexi, karpro, (karpro+(karpro*(tgrpan/100))), tgrfec, emppdt, empprt "
				          ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000027, ".$wbasedato."_000024, ".$wbasedato."_000002, ".$wbasedato."_000007 "
				          ."   WHERE artcod                             = '".$wart."'"
				          ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
				          ."     AND empcod                             = '".$wemp."'"
				          ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
				          ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
				          ."     AND artcod                             = karcod "
				          ."     AND karcco                             = '".$wcco."'"
				          ."     AND tgrest                             = 'on' "
				          ."     AND artest                             = 'on' "
				          ."     AND karexi                            >= ".$wcan
				          ."     AND emptem                            = '".$wtipcli."'";    
					   	
				       $res = mysql_query($q,$conex); //or die (mysql_errno()." - ".mysql_error());
					   $num = mysql_num_rows($res);   //or die (mysql_errno()." - ".mysql_error());
					    
					   if ($num > 0)
					      {
						   $row = mysql_fetch_array($res); 
					       $wart    = $row[0];
					       $wdes    = $row[1];
					       $wuni    = $row[2];
					       $wvac    = $row[3];
					       $wporiva = $row[4];
					       $wcospro = $row[6];
					       $wvan    = $row[7];
					       $wfeccam = $row[8];
					       $wdesemp = ($row[9]/100);
			               $wrecemp = ($row[10]/100);
					        
					       if ($wfecha < $wfeccam)   //Aca evaluo si tomo el valor anterior o el actual
					          $wval = $wvan;
					         else
					            $wval = $wvac;
					       //////////////////////////////////////////////////////////////////////////////////////////////////////////////      
			               //CALCULO DEL IVA ============================================================================================    
					       //EL 3 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO             
					       //$wvaliva = $wcan*$wval*($wporiva/100);
					       //$wvaltot = (($wcan*$wval)+($wcan*$wval*($wporiva/100)));
					       if ($wporiva > 0)
					          $wvaliva = round((($wcan*$wval)-(($wcan*$wval)/(1+($wporiva/100)))));
					         else
					            $wvaliva=0; 
					       $wvaltot = round(($wcan*$wval));
					       
					       			    
						   //Si entra por aca es porque ya se valido y por ende puede grabar el articulo en la tabla TEMPORAL
			     	       $q= " INSERT INTO ".$wbasedato."_000034 (   Medico       ,   Fecha_data ,   Hora_data,   temusu      ,   temfec           ,   temhor          ,   temsuc  ,   temcaj   ,   temtcl     ,   temres  ,   temdcl     ,   temncl     ,   temart ,    temdes  ,   tempre  ,  temcan ,  temvun ,  tempiv    ,  temiva    ,  temtot     , temcmo      ,  temcpr    ,  temdem    ,  temrem    ,  temdar    ,  temdpa    ,  tembpa    ,  temlpa    ,  temdbo      , temrbo      ,    temcpu     ,   temlpa     ,  Seguridad) "
		                      ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wcco."','".$wcaja."','".$wtipcli."','".$wemp."','".$wdocpac."','".$wnompac."','".$wart."','".$wdes."','".$wuni."',".$wcan.",".$wval.",".$wporiva.",".$wvaliva.",".$wvaltot.",".$wcuotamod.",".$wcospro.",".$wdesemp.",".$wrecemp.",".$wdesart.",".$wpdepac.",".$wvdepac.",".$wlinpac.",".$wdcto_bon.",0            , '".$wcarpun."','".$wlinpac."', 'C-".$wusuario."')";
		                   $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				            
				           $wventa="N";
				           $wdesart=0;
				           mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac);
				          }   
		                 else 
		                    {
		                     ////===========================================================================================================================   
				             ////===========================================================================================================================
				             ////===========================================================================================================================
				             echo "<td bgcolor=#99FFCC colspan=".($wcol-6).">No se tiene disponible la cantidad solicitada o NO tiene asignada unidad de medida</TD>";  
				             echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";                        //submit 
				             $wventa="N";
				             $wdesemp=0;
		      				 $wrecemp=0;
		      				 $wdesart=0;
		      				 mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac);
			                } 
	                  } 
	           } // fin del if isset($warticulo)  
			  else
			     if ($wini == 'N') //Aca entra porque no digito nada pero ya ha digitado otro u otros articulos
			        {
				     $wventa="N";
				     $wdesemp=0;
		             $wrecemp=0; 
		             $wdesart=0;
		             if (!isset($wmensajero))
		                $wmensajero=" ";
		             mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac);
				    } 
		   }
	   echo "</tr>";   
	 } //Fin del then del if de $wventa = 'N' 	   
	else
       {
	    //=================================================================================================================   
	    //=================================================================================================================
	    //ACA SE GRABA LA VENTA !!!!!!!!!!!
	    //=================================================================================================================
	    //Primero verifico que no se halla cambiado el empleado o el valor
	    
	    if ($wtipfac == "Manual")
	       {
		    $q = " SELECT ccopfm, ccoffm, ccofmi "
		         ."  FROM ".$wbasedato."_000003 "
	 		     ." WHERE ccocod='".$wcco."'";
	 		          
		    $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	        $row = mysql_fetch_array($err);
	       
	        $wfueffa   =$row[1];
	        $wnrofac   =$row[0]."-".$row[2];   
		       
		    ?>	    
		    <script>  
		       //alert("Pasooo")
		       //function alerta(){document.write($wnrofac)}
		       //function alerta(){document.write("Otro Pasoooooo")}
		       alert ("!!!! ATENCION !!!! ***** ESTA GRABANDO UNA FACTURA MANUAL *****");
		    </script>
			<?php   
	       }	
	    
	    if (isset($wprestamo) and $wprestamo > 0)
	       {
		    $q = "SELECT pnocod, pnoval "
		        ."  FROM ".$wbasedato."_000046 "
		        ." WHERE pnocon = ".$wprestamo;
		    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		    $row = mysql_fetch_array($res);
		
		    if (isset($wemp) and isset($wtotventot))    
		        if (trim($row[0]) != trim($wemp) or trim($row[1]) != trim($wtotventot))
			       {
				    $WEXISTE_PRESTAMO="off";
			        $whabilita_venta=="DISABLED";
			        $wventa="N";
			        $wprestamo=0;
			        $wpagook=0; 
			        
			        $wempleado=explode("-",$wempresa);
			        
			        $wte1pac=$wempleado[0];
			        $wdocpac=$wempleado[1];
			        $wnompac=$wempleado[2];
			        $wcarpun="000000";
			        
			        echo "<input type='HIDDEN' name= 'wcarpun' value='".$wcarpun."'>";
			        echo "<input type='HIDDEN' name= 'wte1pac' value='".$wte1pac."'>";
			        echo "<input type='HIDDEN' name= 'wdocpac' value='".$wdocpac."'>";
			        echo "<input type='HIDDEN' name= 'wnompac' value='".$wnompac."'>";
			        
			        
			        ?>	    
				     <script>
				        alert ("!!!! ATENCION !!!! Se modifico el responsable del prestamo o su valor, favor repita el proceso de verificación de cupo");
				        document.forms.ventas.submit();
				     </script>
					<?php  
					
				   }    
	       } 
	    
	    
	    if ($wini == "N" and $whabilita_venta=="ENABLED" )
	       {
		    echo "</table>";   
	        echo "<center><table border>";    
		    
		    $wdesart=0;    
	        mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wte1pac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart,$wpdepac,$wvdepac,$wlinpac);
	      
	        
	        
	        $WSINCUOTA="N";                                     //Indica que el responsable es una empresa pero no se le cobra nada al paciente
            if ($wtipcli=="01-PARTICULAR") 
               include_once("pos/Grabar_venta.php");   
	          else  //Cuando entre por aca pregunto si la cuota moderadora es mayor a cero
	             {
		          if ($wcuotamod > 0 and $wtipcli <> "01-PARTICULAR")   
		             include_once("pos/Grabar_venta.php");    
	                else 
			           if ($wcuotamod == 0 and $wtipcli <> "01-PARTICULAR")   
			              {
				           if ($wchequeo=="on")
				              if ($wpagook==0)    //No tiene capacidad de pago por nomina
				                 {
					              $fk=0;
					              $wventa="N";  
				                  echo "<td bgcolor=#99FFCC colspan=13 align=center><font size=4><b>EMPLEADO NO HABILITADO PARA DEDUCCION POR NOMINA O EL NUMERO DE CUOTAS NO ES SUFICIENTE</b></font></TD>";     
				                  echo "<tr><td align=center colspan=13 bgcolor=#cccccc><input type='submit' value='OK'></td></tr>";                            //submit 
			                     }  
				                else
				                   {      
				                    $WSINCUOTA="S";                  //Si entra por aca Indica que el responsable es una empresa pero no se le cobra nada al paciente   
		                            include_once("pos/Grabar_venta.php"); 
		                            $fk=0; 
	                               }
	                         else       
		                        {      
			                     $WSINCUOTA="S";                     //Si entra por aca Indica que el responsable es una empresa pero no se le cobra nada al paciente   
	                             include_once("pos/Grabar_venta.php"); 
	                             $fk=0; 
                                }   
		                  } 
		         }
		    echo "<input type='HIDDEN' name= 'wventa' value='".$wventa."'>";      //Envio la venta como "S"
	        if (isset($fk)) echo "<input type='HIDDEN' name= 'fk' value='".$fk."'>";              //Contador de formas de pago que han digitado
           }
       }  
       
   echo "<br><br>";
   echo "<tr><td align=left colspan=13><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
       
   echo "</table>";
   
   $wdato="";	   
   $wcodart="";
   $wdesart="";
   unset($wcodart);
   unset($wdesart);  
   unset($wdato);  
   //echo "<meta http-equiv='refresh' content='0;url=Ventas.php?'>";
   echo "<br><br>";	   
   echo "<TR align=left><td align=left bgcolor=#cccccc><font size=3><A href='copia_factura.php?wcaja=".$wcaja."&amp;wbasedato=".$wbasedato."'> Imprimir Copia de Factura</A></font></TD></TR>";
}
?>
