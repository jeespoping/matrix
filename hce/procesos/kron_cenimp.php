<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	kron_cenimp
 * Fecha		:	2013-07-18
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Se encarga de eliminar los pdf que fueron impresos previamente
 * Condiciones  :
 *********************************************************************************************************

 Actualizaciones:

 **********************************************************************************************************/

$wactualiz = "2013-07-18";

//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenimp");

	//**************************************FUNCIONES PARA GENERAR PDF*************************************************

	//******************************************************************************************************************
	function borrarSolicitudesImpresas(){

		$resultado = shell_exec( "find /var/www/matrix/hce/reportes/cenimp/* -mtime +1 -exec rm {} \; ");
		echo $resultado;
		$resultado = shell_exec( "find /var/www/matrix/fachos/procesos/facturas/* -mtime +1 -exec rm {} \; ");
		echo $resultado;
		$resultado = shell_exec( "find /var/www/matrix/ips/procesos/soportes/* -mtime +1 -exec rm {} \; ");
		echo $resultado;
		
		// --> Borrar facturas electronicas
		shell_exec( "find /var/www/matrix/ips/e-facturas/11/*.pdf -mtime +1 -exec rm {} \; ");
		shell_exec( "find /var/www/matrix/ips/e-facturas/01/*.pdf -mtime +1 -exec rm {} \; ");
		shell_exec( "find /var/www/matrix/ips/e-facturas/*.pdf -mtime +1 -exec rm {} \; ");
		// shell_exec( "find /var/www/matrix/ips/e-facturas/11/*.xml -mtime +3 -exec rm {} \; ");
		// shell_exec( "find /var/www/matrix/ips/e-facturas/01/*.xml -mtime +3 -exec rm {} \; ");
		// shell_exec( "find /var/www/matrix/ips/e-facturas/*.xml -mtime +3 -exec rm {} \; ");

	}

	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;

		encabezado("KRON CENTRO DE IMPRESIONES", $wactualiz, "clinica");

		echo "<center>";
		echo '<br><br>';

		echo '<div style="width: 100%">';

		echo "<br><br><br>";

		borrarSolicitudesImpresas();

		echo "</div>";

		echo "<br><br>";

		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:enter()' />";
		echo "<br><br>";
		echo "<br><br><br>";
		echo "</center>";
	}

?>

<html>
	<head>
	<title>Kron Centro impresion</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	</head>
	<body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
	</body>
</html>