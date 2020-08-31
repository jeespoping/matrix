<html>
<head>
<title>MATRIX - HCE-Historia Clinica Electronica</title>
</head>

<?php
include_once("conex.php");
//session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	echo "<frameset rows=100,* frameborder=0 framespacing=0>";                                                                                               //Encabezado antes de las pestañas
	    echo "<frame src='configuracion.php?accion=T&ok=0&wemp_pmla=".$wemp_pmla."' name='titulos' marginwidth=0 scrolling='yes' marginheiht=0>";
	    echo "<frameset cols=15%,85%  frameborder=0 framespacing=2>";                                                                                        //20% para el arbol, 80% para las pestañas
			echo "<frameset rows=15%,85% frameborder=0 framespacing=0 marginwidth=0>";                                                                       //15% para el Usuario, 85% para las pestañas al lado derecho
				echo "<frame src='configuracion.php?accion=U&ok=0&wemp_pmla=".$wemp_pmla."' name='usuario' marginwidth=0 marginheiht=0 scrolling='no'>";        //Usuario
				echo "<frame src='configuracion.php?accion=F&ok=0&wemp_pmla=".$wemp_pmla."' name='formularios' marginwidth=0 marginheiht=0 scrolling='yes'>";    //Formulario
			echo "</frameset>";
			echo "<frameset rows=100% frameborder=0 framespacing=2>";
				echo "<frame src='configuracion.php?accion=M&ok=0&wemp_pmla=".$wemp_pmla."' name='principal' marginwidth=0 marginheiht=0 scrolling='yes'>";
			echo "</frameset>";
		echo "</frameset>";
	echo "</frameset>";
}
?>	
</html>


