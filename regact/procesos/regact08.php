<?php
include_once("conex.php");
$id = $_GET['id'];

include_once("regact/regact02.php");
if(!isset($_SESSION['user']))
{
    ?>
    <div align="center">
        <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
    </div>
    <?php
    return;
}
else
{
    $user_session = explode('-', $_SESSION['user']);
    $wuse = $user_session[1];
    

    include_once("root/comun.php");
    


    $conex = obtenerConexionBD("matrix");
}

$qry = mysql_query("SELECT * FROM regact_000002 WHERE id = '$id' ");
$dato = mysql_fetch_array($qry);
$documento = $dato['Nombre_archivo'];
?>

<html>
<head>
  	<title>MATRIX Visualizacion de Archivos PDF</title>
  	<!-- UTF-8 is the recommended encoding for your pages -->
</head>

<body onload="myLink = document.getElementById('vinculo'); myLink.click();">

<?php
	echo "<form><A id='vinculo' href='/matrix/images/medical/regact/".$documento."'></A></form>";
?>
</body>
</html>