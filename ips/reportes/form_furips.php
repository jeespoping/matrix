<?php
include_once("conex.php"); header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>FURIPS</title>
</head>

<body>

<?php
  /******************************************************************
   * 	  FORMULARIO ÚNICO DE RECLAMACIÓN IPS - FURIPS				*
   * -------------------------------------------------------------- *
   * Formulario de selección del responsable e ingreso de historia	*
   * clínica para generación del formato FURIPS (formato_furips.php)*
   * que es el Formulario Único de Reclamación de los Prestadores 	*
   * de Servicios de Salud por Servicios Prestados a Víctimas  de 	*
   * de Eventos Catastróficos y Accidentes de Tránsito 				*
   * (Personas Jurídicas - FURIPS) 									*
   ******************************************************************/
	/*
	 * Autor: John M. Cadavid. G.
	 * Fecha creacion: 2011-10-04
	 * Modificado: 
	 * Aca se ponen los comentarios de las modificaciones del programa
	 * 2018-07-17 	Edwin MG	- Se consultan los formatos furips solamente por número factura y sin importar la fuente de la factura
	 */


  // Consulta los datos de las aplicaciones
  function datos_empresa($wemp_pmla)
    {  
	  global $user;   
	  global $conex;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	     
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
		      
		      if ($row[0] == "movhos")
		         $wbasedato=$row[1];

			  if ($row[0] == "tabcco")
		         $wtabcco=$row[1];

			 }  
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	  
	  $winstitucion=$row[2];
	      
    }
   
	 
session_start();

//carga de jquery al momento de iniciar sesion por la automatizacion
if (isset($_GET['automatizacion_pdfs'])){
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo ' <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
}
// Inicia la sessión del usuario
if (!isset($user) && !isset($_GET['automatizacion_pdfs']))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
// Si el usuario no está registrado muestra el mensaje de error
if(!isset($_SESSION['user']) && !isset($_GET['automatizacion_pdfs']))
	echo "error";
else	// Si el usuario está registrado inicia el programa
{	            
 	
  include_once("root/comun.php");

  $conex = obtenerConexionBD("matrix");

  //$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
  $wbasedato_farm = consultarAliasPorAplicacion($conex, $wemp_pmla, "farpmla");

  datos_empresa($wemp_pmla);

  // conexionOdbc($conex, $wbasedato, &$conexUnix, 'facturacion');
  
  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 
  // Aca se coloca la ultima fecha de actualización
  $wactualiz = "12 junio de 2017";

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtener titulo de la página con base en el concepto
  $titulo = "FURIPS";

  $wfecha = date("Y-m-d");
	
  if (!isset($bandera))
  {  			
	$wfecha_ini=$wfecha;
	$wfecha_fin=$wfecha;
  }

  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

	// Formulario que pide la historia clínica y la empresa relacionada con el reporte
	$query = "SELECT ccocod, cconom  "
				."  FROM ".$wbasedato_farm."_000049, costosyp_000005 "
				." WHERE cfgcco = ccocod "
				." 	 AND ccoest = 'on' "
				." ORDER BY cconom ";
	$result = mysql_query($query,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
	$num1 = mysql_num_rows($result);
	//Se modifica para que reciba la automatizacion jaime mejia
	if(isset($_GET['automatizacion_pdfs'])){
		echo "<form name='ingreso' action='formato_furips_x_fac.php?automatizacion_pdfs=' target='_blank' method='POST'>";
	}
	else{
		echo "<form name='ingreso' action='formato_furips_x_fac.php' target='_blank' method='POST'>";
	}
	//fin modificacion para que reciba la automatizacion jaime mejia														 

	echo "<form name='ingreso' action='formato_furips_x_fac.php' target='_blank' method='POST'>";
	echo "<table border=0 align=center width=310>";
	echo "<tr><td colspan=2 align=left height=31 class=fila2> &nbsp;&nbsp; <b>Ingrese los datos a consultar</b> &nbsp;&nbsp;</td>";
	echo "</tr>";
	echo "<tr height=37><td align=center class=fila2>Número de factura:&nbsp</td>";
	if(isset($_GET['automatizacion_pdfs'])){
		$NAME = $_GET['numerofactura'];
				
		echo "<td align=center class=fila2><input type='text' id ='datoautomatizado' name='wnumero' value=''></td></tr>";
		echo '<script type="text/javascript">' . 
		'document.getElementById("datoautomatizado").value ='. $NAME . ';' .
		'</script>';		
	}
	else{
		echo "<td align=center class=fila2><input type='text' name='wnumero' value=''></td></tr>";
	}
	echo "<tr style='display:none'>";
	echo "<td align=center class=fila2 colspan=2><input type='radio' name='wgrupo' value='0' checked> Factura";
	echo "&nbsp;<input type='radio' name='wgrupo' value='1'> Identificación<br></td></tr>";
	echo "<tr><td align=center colspan=2 height=31><input type='submit' name='aceptar' value='Aceptar' > &nbsp;&nbsp;&nbsp;&nbsp; <input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
	echo "</table>";
	echo "<input type='hidden' name='bandera' value='1'>";
	echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "</form>";

}
?>

</body>
<script>

//************cuando la pagina este lista...**********//
//modificado por jaime mejia
	//Funcion para hacer click en "generar solicitud" en el metodo automatico para rescatar el soporte
	function readyFn() {
		var clickGenerarSolicitud = '<?php echo (isset($_GET['automatizacion_pdfs'])); ?>';
		if(clickGenerarSolicitud == 1){
			$("input[name=aceptar]").click();
		}
	}
	
	$( document ).ready( readyFn );
</script>		 
</html>
