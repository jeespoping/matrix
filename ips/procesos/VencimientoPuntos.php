<head>
  <title>PROCESO VENCIMIENTO DE PUNTOS</title>
  <link href="/matrix/root/caro.css" rel="stylesheet" type="text/css" />
  
  <!-- UTF-8 is the recommended encoding for your pages -->
  <!--   <meta http-equiv="content-type" content="text/xml; charset=utf-8" />  -->
    <title>Zapatec DHTML Calendar</title>

  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    
  <!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

  <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo4{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo5{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo6{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo7{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	
    </style>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	 document.forms.vencimiento.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *       PROCESO VENCIMIENTO DE PUNTOS       *
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
 	
  

						or die("No se ralizo Conexion");
  

 
  
  
	                                                      // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Enero 5 de 2009)";                 // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                      // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
	                                                      
//==============================================================================================================================================\\
//==============================================================================================================================================\\
//DESCRIPCION                                                                                                                                   \\
//==============================================================================================================================================\\
//Este proceso determina la cantidad de puntos vencidos de cada cliente y actuliza la tabla  de saldos.                                         \\
//La formula a aplicar en este es la siguiente: (PuntosCausados-PuntosRedimidos-PuntosDevueltos-PuntosVencidos) > 0 ==> Saldo-(formula anterior)\\
//                                          formula==> (salcau-salred-saldev-salven) > 0 ==> Saldo-(formula)                                    \\
//Es decir si el valor resultante de la formula es mayor cero (0), entonces en esa cantidad se resta el saldo y registra esta cantidad en el    \\
//campo saldev, tambien se graba en la tabla de movimiento (_000059) de puntos un registro con el tipo=V que indica Puntos Vencidos.            \\
//El programa pide la fecha limite compra para dar baja o vencer los puntos y basado en esta fecha se realizan los calculos anteriores para     \\
//cliente que tenga saldo en la tabla _000060.
//==============================================================================================================================================\\
	                                                           
//==============================================================================================================================================\\
//==============================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                               \\
//==============================================================================================================================================\\
//                                                                                                                                              \\
//==============================================================================================================================================\\
	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  echo "<br>";				
  echo "<br>";
      		
  
  $q = " SELECT empdes, empbda "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  $wbasedato=$row[1];
  
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
  
  
  echo "<div id='header'>";
  echo "<div id='logo'>";
  echo "<h1><a href='vencimientopuntos.php'>PROCESO VENCIMIENTO DE PUNTOS</a></h1>";
  echo "<h2><b>".$winstitucion."</b>".$wactualiz."</h2>";
  echo "</div>";
  echo "</div></br>";     
       
  //FORMA ================================================================
  echo "<form name='vencimiento' action='vencimientopuntos.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  
  echo "<br><br><br>";
  echo "<center><table>";
  echo "<tr>";
  echo "<b>";
  echo "<td>=====================================================================================================================================<br>";
  echo "<b>Este proceso determina la cantidad de puntos vencidos de cada cliente y actuliza la tabla  de saldos. <br>";
  echo "La formula a aplicar en este es la siguiente: (PuntosCausados-PuntosRedimidos-PuntosDevueltos-PuntosVencidos) > 0 ==> Saldo-(formula anterior)<br>";
  echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp formula==> (salcau-salred-saldev-salven) > 0 ==> Saldo-(formula)<br>";
  echo "Es decir si el valor resultante de la formula es mayor cero (0), entonces en esa cantidad se resta el saldo y registra esta cantidad en el <br>";
  echo "campo salven, tambien se graba en la tabla de movimiento (_000059) de puntos un registro con el tipo=V que indica Puntos Vencidos.<br>";
  echo "El programa pide la fecha limite de compra para dar baja o vencer los puntos y basado en esta fecha se realizan los calculos anteriores para cada<br>";
  echo "cliente que tenga saldo en la tabla _000060. <br>";
  echo "========================================================================================================================<br></td>";
  echo "</b>";
  echo "</tr>";
  echo "</table>";
  
  
  echo "<br><br><br>";
  echo "<center><table>";
       
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
   if (!isset($wfec) or $wfec=="")
     {     
	  echo "<tr>";
      echo "<td bgcolor='#dddddd' align=center><b>Fecha Limite de Compra</b></td>";
      echo "</tr>";
      
	  if (!isset($wfec)) 
	     $wfec=date("Y-m-d");
   	  $cal="calendario('wfec','1')";
   	  echo "<tr>";
	  echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=".$wfec." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	  ?>
	    <script type="text/javascript">//<![CDATA[
	       Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	    //]]></script>
	  <?php
   		
	  echo "</tr>";    
	    
	  echo "<center><tr><td align=center colspan=4 bgcolor=cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else 
      { 
	   $q = " SELECT Saldto, Salcau, Salred, Saldev, Salsal, Salven  "
	       ."   FROM ".$wbasedato."_000060 "
	       ."  WHERE salsal > 0 "
	       ."    AND saldto != '' "
	       ."  ORDER BY 1 ";    
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $num = mysql_num_rows($res);   
		     
	   if ($num >= 0)
	     {
		  for ($i=1;$i<=$num;$i++)
	         {
		      $row = mysql_fetch_array($res);   
			    
		      //Traigo el total de puntos acumulados hasta la fecha limite de vencimiento  
			  $q = " SELECT SUM(puncan) "
			      ."   FROM ".$wbasedato."_000059 "
			      ."  WHERE pundto = '".trim($row[0])."'"
			      ."    AND puntip = 'C' "
			      ."    AND punfec <= '".$wfec."'";
			  $resacu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	          $wcaus = mysql_fetch_array($resacu);
	          
	          //**********************************************************************
	          //EXPLICAICON DE LA FORMULA ********************************************
	          //**********************************************************************
	          //formula = ((causado_hasta_la_fecha)-redimidos-devueltos-vencidos)
	          //formula = (($wcaus                )-$row[2]  -$row[3]  -$row[5] )
	          
	          //if formula > 0 ==> (saldo=saldo-formula) y (vecidos=vencidos+formula)
	          //**********************************************************************
	          
	          $wformula=($wcaus[0]-$row[2]-$row[3]-$row[5]);
	          
	          if ($wformula > 0 AND ($wformula<=$row[4]))  //Si formula es mayor a cero y la formula es mayor o igual a lo que esta en el campo de saldo de la tabla de saldos
	             {
		          $q = " UPDATE ".$wbasedato."_000060 "
		              ."    SET salven = salven + ".$wformula.","
		              ."        salsal = salsal - ".$wformula
		              ."  WHERE saldto = '".$row[0]."'";
		          $resupt = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		               
		          //i: Se utiliza para grabarla en el campo de 'punvta' porque no existe una venta y el indice unico esta incluido el campo de 'punvta'
		          //entonces con la hora se garantiza la diferencia de cada registro.
		          
		          $q= " INSERT INTO ".$wbasedato."_000059(   Medico       ,   Fecha_data,   Hora_data, Punind , Pundto      ,   Punfec    , Puntip,    Puncan      ,    Punvta, Seguridad        ) "
	         		 ."                            VALUES('".$wbasedato."','".$wfecha."','".$whora."', 'C'    ,'".$row[0]."','".$wfecha."', 'V'   , '".$wformula."', '".$i."' , 'C-".$wusuario."') ";
	              $resupt = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());     
		         }    
			 }
			 ?>	    
		       <script>
			      alert ("Termino de procesar el Vencimiento de puntos"); 
			   </script>
		     <?php   
	     }            
			     
		  
	   echo "</table>"; 
	  
	   echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	      
	   echo "<tr>";  
	   echo "<td align=center colspan=7><A href='VencimientoPuntos.php?wemp_pmla=".$wemp_pmla."'><b>Retornar</b></A></td>"; 
	   echo "</tr>";
     }
    echo "</form>";
	  
    echo "<br>";
    echo "<table>"; 
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register



include_once("free.php");

?>
