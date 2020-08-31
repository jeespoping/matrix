<?php
include_once("conex.php"); 
session_start();
if(!isset($_SESSION['user']))
    die ("Para entrar correctamente a la aplicacion debe hacerlo por la pagina index.php");

    

mysql_select_db("matrix") or die("No se selecciono la base de datos");    

/* OJO: COPIAR LA CARPERA src DENTRO DE LA CARPETA include   */
include_once("src/jpgraph.php"); 
include_once("src/jpgraph_pie.php"); 
include_once("src/jpgraph_pie3d.php"); 

$query = "SELECT YEAR(CURDATE())-substr(Afi_fechanaci,7,4)  AS EDAD  FROM pafplsan_000002"; 
$resultado = mysql_query($query);
$nroreg = mysql_num_rows($resultado);
$i = 0;
While ($i < $nroreg)
{
 $registro = mysql_fetch_row($resultado); 
 switch ($registro[0]) 
 {
    case ($registro[0] <= 20): 
        $data[0] = $data[0] + 1;
        break;
    case ($registro[0]>20 and $registro[0]<=40): 
        $data[1] = $data[1] + 1;
        break;
    case ($registro[0]>41 and $registro[0]<=60): 
        $data[2] = $data[2] + 1;
        break;
    Case  ($registro[0] > 60): 
        $data[3] = $data[3] + 1;    
 }
 $i++;
}
 
$i = 0;
$wtot=0;
While ($i <= 3)
{

 switch ($i) 
 {
    case 0: 
        $nombres[0] = "MENORES DE 20 ".$data[0];
        break;
    case 1: 
        $nombres[1] = "ENTRE 21 Y 40 ".$data[1];
        break;
    case 2:
        $nombres[2] = "ENTRE 41 Y 60 ".$data[2];
        break;
    Case 3: 
        $nombres[3] = "MAYORES DE 60 ".$data[3];
 }
 $wtot=$wtot+$data[$i];
 $i++;
} 	
	
	
//$nombres=array("Ingresado","Asignado","En Proceso","Entregado","Rechazado"); 
//$data = array(15,3,20,40,5); 

$graph = new PieGraph(750,500,"auto"); 
$graph->img->SetAntiAliasing(); 
$graph->SetMarginColor('gray'); 
//$graph->SetShadow(); 

// Setup margin and titles 
$graph->title->Set("PROGRAMA PLUS SANITAS - USUARIOS POR EDADES - TOTAL USUARIOS ".$wtot ); 

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
?> 
