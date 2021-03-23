<html>
<head>
<script src="reporte-res1995/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<title>MATRIX - [REPORTE ADMISIONES RES 1995]</title>
</head>

<body>
<script type="text/javascript">
    function enter(){ document.forms.indhosp.submit(); }
    function cerrarVentana() { window.close(); }
</script>

<?php
include_once("conex.php");
include_once("root/comun.php");
include '../presap/models/Admisiones.php';
include '../presap/service/ServicioAdmisiones.php';
use matrix\admisiones\presap\models\Admisiones;
use matrix\admisiones\presap\service\ServicioAdmisiones;

$wactualiz = date('Y-m-d');

if (!isset($user))
if(!isset($_SESSION['user']))
session_register("user");

if(!isset($_SESSION['user']))
    terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
else{

	/*PARTE DEL REGISTRO Y LA APLICACIÓN*/
	$conex = obtenerConexionBD("matrix");
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$modeloAdmisiones = new Admisiones($conex,$wemp_pmla,$wtabcco);
	//$servicioAdmisiones = new ServicioAdmisiones($modeloAdmisiones);
	//$prueba = $servicioAdmisiones->calcularEdad();
	encabezado("Sistema de reporte admisiones", $wactualiz, "cliame");
	?>
	<div id="app">
	</div>
	<script type='module' src='reporte-res1995/main.js'>
	</script>
	<?php
	//FORMA ================================================================
	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
	if (strpos($user,"-") > 0)
		$wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

//===============================================================================================================================================
//ACA COMIENZA EL MAIN DEL PROGRAMA
//===============================================================================================================================================
		
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
	."   FROM ".$wtabcco.", ".$wbasedato."_000011"
	."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
	//$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	//$num = mysql_num_rows($res);
	
	$cco="Ccohos";
	$sub="off";
	$tod="Todos";
	//$cco=" ";
	$ipod="off";
}
echo "</table>";

echo "</table>";
liberarConexionBD($conex);
?>
</body>
</html>