<html>
<head>
  <title>VACACIONES</title>
</head>
<body>	
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type="text/javascript">
	
	function enter()
	{
	 document.forms.vacaciones.submit();
	}
	
	function enter3(){
		$.unblockUI();
		
		var clave = document.getElementById( "pwClaveCancelarAlta" ).value;
		document.getElementById( "inClaveCancelarAlta" ).value = clave;

		enter();
	}
	
	function enter2()
	{
		$.blockUI({ message: $('#divClaveCancelarAlta') });
	}
	
	function agregarJusti()
	{
		var obj = document.getElementById('mjust');
		a = obj.options[obj.selectedIndex].value;
		if(a=="--")
		{
			alert("Debe seleccionar una justificaci\xf3n");
		}else
			{
				$.unblockUI();
				b = document.createElement("input");
				b.type ="text";				
				b.name = "wjust";		
				b.id = "wjust";
				b.value = a;
				document.altas.appendChild(b);
				var f = b.name;
				document.forms.altas.submit();
				
			}
			document.forms.altas.submit();
	}
		
	
	function fnMostrar(){
	
			$.blockUI({ message: $("#menuJusti"), 
							css: { left: ( $(window).width() - 800 )/2 +'px', 
								    top: ( $(window).height() - $("#menuJusti").height() )/2 +'px',
								  width: '800px'
								 } 
					  });
	}
	
	function cerrarVentana()
	 {
      window.close();	
     }
</script>

<?php
include_once("conex.php");
  /***********************************************
   *      REPORTE Y SOLICUTUD DE VACACIONES      *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
@session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  
  
  include_once("root/comun.php");
  
  $conexunix = odbc_pconnect('informix','nomina','1201')
  					    or die("No se ralizo Conexion con el Unix");
						
  $conex = obtenerConexionBD("matrix");
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="2014-01-29";                                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	
	                                                           
//=========================================================================================================================================\\
//Programa       : Vacaciones.php
//Autor    		 : Juan Carlos Hernández M - Coordinador Desarrollo de Sistemas de Información
//Fecha creación : Enero 29 de 2014
//=========================================================================================================================================\\
//ACTUALIZACIONES
//=========================================================================================================================================\\
//Descripción : xxxxxxxx
//
//=========================================================================================================================================\\

  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
  $wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
  $wafinidad = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');
  $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');   

  encabezado("VACACIONES",$wactualiz, "clinica");

  //Mensaje de espera
  echo "<div id='msjEspere' style='display:none;'>";
  echo '<br>';
  echo "<img src='../../images/medical/ajax-loader5.gif'/>";
  echo "<br><br> Por favor espere un momento ... <br><br>";
  echo '</div>';

  echo "<script>";
  echo "$.blockUI({ message: $('#msjEspere') });";
  echo "</script>";
       
  //FORMA ================================================================
  echo "<form name='vacaciones' action='vacaciones.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name='wcencam' value='".$wcencam."'>";
  
  
  echo "<center><table>";
  
        
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

  $q = " SELECT Empdes, Empmsa "
      ."   FROM root_000050 "
      ."  WHERE Empcod = '".$wemp_pmla."'"
      ."    AND Empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
	
	
	
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
    $query = " SELECT pvadetcod, pvadetsec, pvadetfci, pvadetfcf, pvadetdia, pvadetcpc "
			."   FROM nopvadet "
			."  WHERE pvadetcod = '".$user."'"
			."  ORDER BY 2 ";
	$res = odbc_do($conexunix,$query);
			
    echo "<tr>"; 			
	echo "<td>Secuencia</td>";
	echo "<td>Fecha Inicial</td>";
	echo "<td>Fecha Final</td>";
	echo "<td>Días a Disfrutar</td>";
	echo "<td>Consecutivo</td>";
	while(odbc_fetch_row($res))
	 {
	  echo "<td>".odbc_result($res,2)."</td>";
	  echo "<td>".odbc_result($res,3)."</td>";
	  echo "<td>".odbc_result($res,4)."</td>";
	  echo "<td>".odbc_result($res,5)."</td>";
	  echo "<td>".odbc_result($res,6)."</td>";
	 }      
	echo "</tr>";  
	      
	   
		
		
    echo "</form>";
	  
    echo "<table>"; 
    echo "<tr class=boton><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register

  echo "<script>";
  echo "$.unblockUI();";
  echo "</script>";

include_once("free.php");

?>
