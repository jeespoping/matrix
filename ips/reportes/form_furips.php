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
   * 	  FORMULARIO �NICO DE RECLAMACI�N IPS - FURIPS				*
   * -------------------------------------------------------------- *
   * Formulario de selecci�n del responsable e ingreso de historia	*
   * cl�nica para generaci�n del formato FURIPS (formato_furips.php)*
   * que es el Formulario �nico de Reclamaci�n de los Prestadores 	*
   * de Servicios de Salud por Servicios Prestados a V�ctimas  de 	*
   * de Eventos Catastr�ficos y Accidentes de Tr�nsito 				*
   * (Personas Jur�dicas - FURIPS) 									*
   ******************************************************************/
	/*
	 * Autor: John M. Cadavid. G.
	 * Fecha creacion: 2011-10-04
	 * Modificado: 
	 * Aca se ponen los comentarios de las modificaciones del programa
	 * 2018-07-17 	Edwin MG	- Se consultan los formatos furips solamente por n�mero factura y sin importar la fuente de la factura
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

// Inicia la sessi�n del usuario
if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
// Si el usuario no est� registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
	echo "error";
else	// Si el usuario est� registrado inicia el programa
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
 
  // Aca se coloca la ultima fecha de actualizaci�n
  $wactualiz = "12 junio de 2017";

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtener titulo de la p�gina con base en el concepto
  $titulo = "FURIPS";

  $wfecha = date("Y-m-d");
	
  if (!isset($bandera))
  {  			
	$wfecha_ini=$wfecha;
	$wfecha_fin=$wfecha;
  }

  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

	// Formulario que pide la historia cl�nica y la empresa relacionada con el reporte
	$query = "SELECT ccocod, cconom  "
				."  FROM ".$wbasedato_farm."_000049, costosyp_000005 "
				." WHERE cfgcco = ccocod "
				." 	 AND ccoest = 'on' "
				." ORDER BY cconom ";
	$result = mysql_query($query,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
	$num1 = mysql_num_rows($result);

	echo "<form name='ingreso' action='formato_furips_x_fac.php' target='_blank' method='POST'>";
	echo "<table border=0 align=center width=310>";
	echo "<tr><td colspan=2 align=left height=31 class=fila2> &nbsp;&nbsp; <b>Ingrese los datos a consultar</b> &nbsp;&nbsp;</td>";
	echo "</tr>";
	echo "<tr height=37><td align=center class=fila2>N�mero de factura:&nbsp</td>";
	echo "<td align=center class=fila2><input type='text' name='wnumero' value=''></td></tr>";
	echo "<tr style='display:none'>";
	echo "<td align=center class=fila2 colspan=2><input type='radio' name='wgrupo' value='0' checked> Factura";
	echo "&nbsp;<input type='radio' name='wgrupo' value='1'> Identificaci�n<br></td></tr>";
	echo "<tr><td align=center colspan=2 height=31><input type='submit' name='aceptar' value='Aceptar' > &nbsp;&nbsp;&nbsp;&nbsp; <input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
	echo "</table>";
	echo "<input type='hidden' name='bandera' value='1'>";
	echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "</form>";

}
?>

</body>
</html>
