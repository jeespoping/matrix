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
	 * Fecha creacion: 2011-08-31
	 * Modificado: 
	 * 2012-01-24 - Se cambió la carpeta de consulta de planos, de ../../planos/FuRips/farpmla/ a ../../ips/FuRips/farpmla/
	 * ya que en la carpeta planos se borra información constantemente - Mario Cadavid
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

// Inicia la sessión del usuario
if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
// Si el usuario no está registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
	echo "error";
else	// Si el usuario está registrado inicia el programa
{	            
 	
  include_once("root/comun.php");

  $conex = obtenerConexionBD("matrix");

  //$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
  $wbasedato_farm = consultarAliasPorAplicacion($conex, $wemp_pmla, "farpmla");

  datos_empresa($wemp_pmla);

  conexionOdbc($conex, $wbasedato, &$conexUnix, 'facturacion');
  
  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 
  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " Ene. 24 de 2012";

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtener titulo de la página con base en el concepto
  $titulo = "ARCHIVOS PLANOS FURIPS";

  $wfecha = date("Y-m-d");
	
  if (!isset($bandera))
  {  			
	$wfecha_ini=$wfecha;
	$wfecha_fin=$wfecha;
  }

  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

  if(!isset($bandera) || $bandera!='1')
  {
	// Formulario que pide las fechas aconsultar
	echo "<form name='ingreso' action='rep_planos_furips.php' method='POST'>";
	echo "<table border=0 align=center width=310>";
	echo "<tr>";
	echo "<td colspan=2 align=center height=31 class=fila1> &nbsp;&nbsp; <b>Ingrese los datos a consultar</b> &nbsp;&nbsp;</td>";
	echo "</tr>";
	echo "<tr><td align=center class=fila2>Fecha inicial:&nbsp;</td>";
	echo "<td align=center class=fila2>Fecha final:&nbsp;";
	echo "</td></tr>";
	echo "<tr><td align=center class=fila2>";
	campoFechaDefecto("wfecha_ini", $wfecha_ini);
	echo "</td>";
	echo "<td align=left class=fila2>";
	campoFechaDefecto("wfecha_fin", $wfecha_fin);
	echo "</td></tr>";
	echo "<tr><td align=center colspan=2>&nbsp;</td></tr>";
	echo "<tr><td align=center colspan=2><input type='submit' name='aceptar' value='Aceptar' ></td></tr>";
	echo "<tr><td align=center colspan=2>&nbsp;</td></tr>";
	echo "<tr><td align=center colspan=2><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr>";
	echo "</table>";
	echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' name='bandera' value='1'>";
	echo "</form>";
  }
  else
  {

  	echo "<table border=0 align=center>";
	echo "<tr>";
	echo "<td align=left height=31 class=encabezadoTabla> &nbsp;&nbsp; <b>Archivos planos generados para FURIPS</b> &nbsp;&nbsp;</td>";
	echo "</tr>";
	echo "<tr><td height=10>&nbsp;</td></tr>";
    $path = "../../ips/FuRips/farpmla/";

    // Abrir la carpeta
    $dir_handle = @opendir($path) or die("Unable to open $path");
	$i = 0;

    // Leer los archivos
    while ($file = readdir($dir_handle)) 
	{
		$str_name = explode("-",$file);
		$name1 = $str_name[0];
		$year_name = substr($name1, -4);
		$mont_name = substr($name1, -6, 2);
		$day_name = substr($name1, -8, 2);
		
		$date_name = $year_name."-".$mont_name."-".$day_name;
		
		if($wfecha_ini <= $date_name && $wfecha_fin >= $date_name)
		{
			if($file == "." || $file == ".." || $file == "index.php" || $file == "consecutivo.txt")
				continue;

			if (is_int ($i/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila

 		    echo "<tr><td height=21 class=$wcf>";
			echo "&nbsp;<a href=\"../../ips/FuRips/farpmla/$file\" target=\"_blank\">$file</a> &nbsp; &nbsp; &nbsp; &nbsp;";
			echo "</td></tr>";
			$i++;
		}
    }
	
	if($i==0)
		echo "<tr><td align='center'> No se encontraron registros para las fechas seleccionadas </td></tr>";
	
	// Botones Retornar, Cerrar Ventana y Grabar
	echo "<tr><td align='left'>&nbsp;</td></tr>";
	echo "<tr><td align='center'>";
	echo "<input type=button value='Retornar' onclick='history.back();'> &nbsp; &nbsp; &nbsp; &nbsp; <input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td><td align='right' colspan='3'>";
	echo "</td></tr>";

	echo "</table>";
    // Cerrar
    closedir($dir_handle);
  }

}
?>

</body>
</html>
