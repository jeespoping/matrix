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
include_once("src/jpgraph_pie.php"); 
include_once("src/jpgraph_pie3d.php"); 

$hoy = date("Y-m-d");
$wano = substr($hoy,0,4);
$wmes = substr($hoy,5,2);

$query = "Select cardetcco,cconom,'H' tipo,SUM(cardettot)"
. " From FACARDET, OUTER cocco "
. " Where cardettar='PF' "
. " And cardetano = '".$wano."'"
. " And cardetmes = '".$wmes."'"
. " And cardetfec > '20120601'"       // Como esta fue la fecha de inicio la utilizo para acelerar notablemente el query
. " And cardetanu = '0' "
. " And cardetcco=ccocod"
. " GROUP BY 1,2,3"
//. " ORDER BY 4 DESC";

. " UNION ALL "

. " Select cardetcco,cconom,'A' tipo,SUM(cardettot)"
. " From AYCARDET, OUTER cocco "
. " Where cardettar='PF' "
. " And cardetano = '".$wano."'"
. " And cardetmes = '".$wmes."'"
. " And cardetfec > '20120601'"       // Como esta fue la fecha de inicio la utilizo para acelerar notablemente el query
. " And cardetanu = '0' "
. " And cardetcco=ccocod"
. " GROUP BY 1,2,3"
."  ORDER BY 4 DESC";

/* estos dos ultimos querys dan igual pero para caso como Imagenologia donde graban cargos no facturables en Aycardet variaria
   pues en aymov queda solo el valor facturado.
   
." Select movcco,cconom,'A' tipo,SUM(movval)"
." From aymov,cocco"
." Where movtar='PF' "
." And movano = '".$wano."'"
." And movmes = '".$wmes."'"
." And movfec > '20120601'"         // Como esta fue la fecha de inicio la utilizo para acelerar notablemente el query
." And movtip = 'E' "     // Cargos de pacientes Externos o Ambulatorios en las unidades de Ayudas Dx
." And movanu = '0' "
." And movcco=ccocod"
." GROUP BY 1,2,3"
." ORDER BY 4 DESC";
*/

$resultado = odbc_do($conexN,$query);            // Ejecuto el query  
$nroreg= 0;
$wtot=0;
$i=0;
while (odbc_fetch_row($resultado))
{
 if (substr(odbc_result($resultado,2),0,27)=="") 
   $nombres[$i] = substr("SIN CENTRO DE COSTO ASIGNADO",0,27)." ".odbc_result($resultado,3)." ".number_format(odbc_result($resultado,4),0);
 else
   $nombres[$i] = substr(odbc_result($resultado,2),0,27)." ".odbc_result($resultado,3)." ".number_format(odbc_result($resultado,4),0);
 
 $data[$i] = odbc_result($resultado,4);
 $wtot=$wtot+odbc_result($resultado,4);
 $i++;
}
odbc_close($conexN);

//$nombres=array("Ingresado","Asignado","En Proceso","Entregado","Rechazado"); 
//$data = array(15,3,20,40,5); 

$graph = new PieGraph(1080,500,"auto"); 
$graph->img->SetAntiAliasing(); 
$graph->SetMarginColor('green'); 
$graph->SetShadow(); 

// Setup margin and titles 
$graph->title->Set("PROGRAMA CARDIOVASCULAR - TOTAL CARGOS HOSPITALIZACION Y AYUDAS ".number_format($wtot,0));


$p1 = new PiePlot3D($data); 
$p1->SetSize(0.35); 
$p1->SetCenter(0.5); 

// Setup slice labels and move them into the plot 
$p1->value->SetFont(FF_FONT1,FS_BOLD); 
$p1->value->SetColor("black"); 
$p1->SetLabelPos(0.2); 


$p1->SetLegends($nombres); 

// Explode all slices 
$p1->ExplodeAll(); 

$graph->Add($p1); 
$graph->Stroke(); 

odbc_close($conexN);
odbc_close_all();
?> 
