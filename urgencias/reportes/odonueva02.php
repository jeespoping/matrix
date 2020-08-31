<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
    die ("Para entrar correctamente a la aplicacion debe hacerlo por la pagina index.php");



mysql_select_db("matrix") or die("No se selecciono la base de datos");  
//Conexion a Informix Creada en el "DSN de Sistema"
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");  

/* OJO: COPIAR LA CARPERA src DENTRO DE LA CARPETA include   */
include_once("src/jpgraph.php"); 
// include_once("src/jpgraph_odo.php"); 
include_once("src/jpgraph_line.php"); 

$hoy = date("Y-m-d");
$wano = substr($hoy,0,4);
$wmes = substr($hoy,5,2);

$wtot=0;

$query = "Select fecini,fecfin "
." From sifec"
." Where fecanc='".$wano."'"
." And fecmes='".$wmes."'";

$resultado = odbc_do($conexN,$query);            // Ejecuto el query  
$registro = odbc_fetch_row($resultado);          // leo un registro
$wfecini=odbc_result($resultado,1);              // Tomo el valor del campo
$wfecfin=odbc_result($resultado,2); 

$query = "Select DAY(cardetfec) AS dia,SUM(cardettot) AS tot"
." From facardet"
." Where cardetano='".$wano."'"
." And cardetmes='".$wmes."'"
." And cardetfec between '".$wfecini."' AND '".$wfecfin."'"
." And cardetanu='0' And cardetfac='S'"
." And cardettar IN ('11','D2') "
." Group By 1"
." UNION ALL "
." Select DAY(cardetfec) AS dia,SUM(cardettot) AS tot"
." From aycardet"
." Where cardetano = '".$wano."'"
." And cardetmes = '".$wmes."'"
." And cardetfec between '".$wfecini."' AND '".$wfecfin."'"
." And cardetanu='0' And cardetfac='S' "
." And cardettar IN ('11','D2') "
." Group By 1"
." Into temp tmp";
$resultado = odbc_do($conexN,$query);            // Ejecuto el query  

$query = "Select dia,SUM(tot)"
." from tmp"
." group by dia"
." order by dia";
$resultado = odbc_do($conexN,$query);            // Ejecuto el query  
$nroreg= 0;
$wtot=0;
$i=0;
while (odbc_fetch_row($resultado))
{
 $data[$i] = odbc_result($resultado,2)/1000000;
 $wtot=$wtot+odbc_result($resultado,2);
 $i++;
}
odbc_close($conexN);

$graph = new Graph(550, 250, "auto");   
$graph->SetScale( "textlin");
$graph->img->SetMargin(40, 20, 20, 40);
$graph->title->Set("Distribucion x Dia Total: ".number_format($wtot,0)." Sin estancia");
$graph->xaxis->title->Set("Dia");
$graph->yaxis->title->Set("ingresos diarios en millones");

$lineplot = new LinePlot($data);
$lineplot->SetColor("blue");

$graph->Add($lineplot);
$graph->Stroke();

// envia el grafico a un archivo PNG o JPG
//$graph-> Stroke( "/apl/result2102.jpg");

odbc_close($conexN);
odbc_close_all();
?>
