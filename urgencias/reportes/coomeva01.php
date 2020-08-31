<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
    die ("Para entrar correctamente a la aplicacion debe hacerlo por la pagina index.php");
/*==================================================================================
// File:	    Odometro.PHP
// Description:	Genera grafico en Odometro para control de un tope de valor de cargos
// Created: 	2015-03-11
// Author:	    Jair Saldarriaga orozco
// Ver:		    1.0
/*==================================================================================
*/
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-01-13';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2020-01-13	Jessica Madrid Mejía:	- Se modifica el script para que reciba $wano y $wmes que se envía desde frames02.php y así 
// 										  se pueda generar la grafica de cualquier mes y no solo el actual.
//--------------------------------------------------------------------------------------------------------------------------------------------


mysql_select_db("matrix") or die("No se selecciono la base de datos");  
//Conexion a Informix Creada en el "DSN de Sistema"
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");  

/* OJO: COPIAR LA CARPERA src DENTRO DE LA CARPETA include   */
include_once("src/jpgraph.php"); 
include_once ('src/jpgraph_bar.php');
// include_once("src/jpgraph_odo.php"); 
// include_once("src/jpgraph_line.php"); 

//$hoy = date("2015-03-11");

// $hoy = date("Y-m-d");
// $wano = substr($hoy,0,4);
// $wmes = substr($hoy,5,2);

$wtot=0;

$query = "Select fecini,fecfin "
." From sifec"
." Where fecanc='".$wano."'"
." And fecmes='".$wmes."'";

$resultado = odbc_do($conexN,$query);            // Ejecuto el query  
$registro = odbc_fetch_row($resultado);          // leo un registro

$wfecini=odbc_result($resultado,1);              // Tomo el valor del campo
$wfecfin=odbc_result($resultado,2); 

$query = "Select SUM(cardettot) AS tot"
." From facardet"
." Where cardetano='".$wano."'"
." And cardetmes='".$wmes."'"
." And cardetfec between '".$wfecini."' AND '".$wfecfin."'"
." And cardetanu='0' And cardetfac='S'"
." And cardettar IN ('36','CU') ";


$resultado = odbc_do($conexN,$query);            // Ejecuto el query  
$registro = odbc_fetch_row($resultado);          // leo un registro
$wtot=odbc_result($resultado,1);                 // Tomo el valor del campo

/* Como al ya todo grabarse en facardet esta trayendo nulos toco: */
$query = "Select count(*) AS tot "
 ." From aycardet"
 ." Where cardetano = '".$wano."'"
 ." And cardetmes = '".$wmes."'"
 ." And cardetfec between '".$wfecini."' AND '".$wfecfin."'"
 ." And cardetanu='0' And cardetfac='S' "
 ." And cardettar IN ('36','CU') ";
 
$resultado = odbc_do($conexN,$query);            // Ejecuto el query  
$registro = odbc_fetch_row($resultado);          // leo un registro
$wcan = odbc_result($resultado,1);               // Tomo el valor del campo 
if ( $wcan > 0 )
{	
 $query = "Select SUM(cardettot) AS tot"
 ." From aycardet"
 ." Where cardetano = '".$wano."'"
 ." And cardetmes = '".$wmes."'"
 ." And cardetfec between '".$wfecini."' AND '".$wfecfin."'"
 ." And cardetanu='0' And cardetfac='S' "
 ." And cardettar IN ('36','CU') ";
 
 $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
 $registro = odbc_fetch_row($resultado);          // leo un registro
 $wtot=$wtot+odbc_result($resultado,1);           // Tomo el valor del campo y acumulo
}

$query = "select SUM(traval*(today-traing)) AS tot"
." from inmtra"
." where tradoc>0"
." and tratar IN ('36','CU')"
." and traliq='0'"
." and traegr is Null";

$resultado = odbc_do($conexN,$query);            // Ejecuto el query  
$registro = odbc_fetch_row($resultado);          // leo un registro
$wtot=$wtot+odbc_result($resultado,1);           // Tomo el valor del campo y acumulo

$query = "select SUM(traval*tradfa) AS tot"
." from inmtra"
." where tradoc>0"
." and tratar IN ('36','CU')"
." and traliq='0'"
." and traegr is not Null";

$resultado = odbc_do($conexN,$query);            // Ejecuto el query  
$registro = odbc_fetch_row($resultado);          // leo un registro
$wtot=$wtot+odbc_result($resultado,1);           // Tomo el valor del campo y acumulo

$wbase=600000000;                         //este valor deberia estar en un maestro de constantes

$wproporcion = ($wtot / $wbase)*100;   
if ($wproporcion>100)                       //Para que no marque error
  $wproporcionAju=100;
else  
  $wproporcionAju=$wproporcion;
  
$wbase2=500000000;
$wproporcion2 = ($wtot / $wbase2)*100;         
if ($wproporcion2>100)                       //Para que no marque error
  $wproporcion2Aju=100;
else  
  $wproporcion2Aju=$wproporcion2;



// Ejemplo grafico de barras: https://jpgraph.net/download/manuals/chunkhtml/example_src/barscalecallbackex1.html


// Callback function for Y-scale to get 1000 separator on labels
function separator1000($aVal) {
    return number_format($aVal);
}
 
function separator1000_usd($aVal) {
    return '$'.number_format($aVal);
}


$titulo = "Total Periodo: ".number_format($wtot,0)." (".number_format($wproporcion,0)."%) "." ( TOPE: ".number_format($wbase,0).")";

// datos a graficar: total cargos y tope
$data=array($wtot,$wbase);
 
// Crea el grafico y configura los parametros basicos
$graph = new Graph(500,300,'auto');
$graph->img->SetMargin(150,30,30,40);
$graph->SetScale('textint');
$graph->SetShadow();
$graph->SetFrame(false);

// Agrega 20% adicional a la escala del eje y
$graph->yaxis->scale->SetGrace(20);

// Labels del eje y
// $graph->yaxis->SetLabelFormatCallback('separator1000'); // sin signo de pesos
$graph->yaxis->SetLabelFormatCallback('separator1000_usd'); // se agrega signo de pesos
 
// Labels del eje x
$graph->xaxis->SetTickLabels(array('CARGOS','TOPE'));

// Titulo del grafico
$graph->title->Set($titulo);

// Crea el grafico de barras
$bplot = new BarPlot($data);
$bplot->SetWidth(0.5);
$bplot->SetShadow();

$graph->Add($bplot);
 
// Muestra la grafica
$graph->Stroke();













// //ODOMETRO  
  
// $wtit = "Total Periodo: ".number_format($wtot,0)." (".number_format($wproporcion,0)."%) "." ( TOPE: ".number_format($wbase,0).")";
// // Crea un nuevo odometro grafico (width=250 Ancho, height=150 alto en pixels)
// $graph = new OdoGraph(550,250);

// $graph->title->Set($wtit);

// // Adiciona una sombra al marco de la grafica
// $graph->SetShadow();

// // Ahora creamos un odometro.
// $odo = new Odometer();

// // La escala por defecto sera de 0 to 100
// $odo->scale->Set(0,100);
// $odo->scale->SetTicks(5,2);            // cada 10 (coloca un valor en el Odometro)

// //$odo->scale->SetTickColor("brown");     // color del valor dentro del odometro
// //$odo->scale->SetTickLength(0.05);       // Tamaño de la señal al valor
// $odo->scale->SetTickWeight(1);            // Ancho de la señal

// //$odo->scale->SetLabelPos(0.75);
// //$odo->scale->label->SetFont(FF_FONT1, FS_BOLD);
// //$odo->scale->label->SetColor("brown");

// // Por defecto siempre muestra el valor del porcentaje dentro del odometro
// // pero si quiero mostrar el pocentaje y un titulo seria asi:

// // $odo->scale->SetLabelFormat("%d Millones");


// // defino colores para los indicadores desde verde hasta rojo por rangos para el odometro
// $odo->AddIndication(0,20,"green:0.7");
// $odo->AddIndication(20,40,"green:0.9");
// $odo->AddIndication(60,80,"yellow");
// $odo->AddIndication(80,90,"orange");
// $odo->AddIndication(90,100,"red");

// // Podemos disminuimos el area de colores en un 20%  de su radio asi:
// //$odo->SetCenterAreaWidth(0.20); 

// // Tambien Podemos colocar un pie de pagina pero solo se ven definiendo odometros
// // con tamaños iguales o menores a  new OdoGraph(250,150)
// //$odo->label->Set("Limite: 2500,000,000");
// //$odo->label->SetFont(FF_FONT2,FS_BOLD);

// // Indica la parte o el punto sobre 100 donde coloca la aguja en el odometro
// $odo->needle->Set($wproporcionAju);
// //---------------------------------------------------------------------
// // Adiciona una sombre para la aguja
// //---------------------------------------------------------------------
// $odo->needle->SetShadow();
// //---------------------------------------------------------------------
// // Adiciono una segunda Aguja
// $odo->needle2->Set($wproporcion2Aju);
// $odo->needle2->SetLength(0.7);           //Largo de la aguja
// $odo->needle2->SetFillColor("navy");
// $odo->needle2->SetShadow();
// $odo->needle2->Show();                   //Muestra la segunda aguja

// // Adiciona el odometro a la grafica
// //---------------------------------------------------------------------
// $graph->Add($odo);
// //---------------------------------------------------------------------
// // Finalmente mostramos la grafica en el browser
// $graph->Stroke();

// envia la imagen o el grafico a un archivo PNG o JPG
//$graph-> Stroke( "/temp/result2002.jpg");
odbc_close($conexN);
odbc_close_all();
?>

