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

$query = "Select prfdes,count(*) From socios_000001,socios_000003,socios_000007"
       . " Where socact='A' And socced=proced AND propro=prfcod Group by 1 order by 2 desc"; 

$resultado = mysql_query($query);
//$nroreg = mysql_num_rows($resultado);
$i = 0;
$wtot=0;
While ($registro = mysql_fetch_array($resultado))
{
 $nombres[$i] = substr($registro[0],0,25)." ".$registro[1];
 $data[$i] = $registro[1];
 $wtot=$wtot+$data[$i];
 $i++;
}

$graph = new PieGraph(750,500,"auto"); 
$graph->img->SetAntiAliasing(); 
$graph->SetMarginColor('gray'); 
//$graph->SetShadow(); 

// Setup margin and titles 
$graph->title->Set("SISTEMA DE INFORMACION DE SOCIOS - SOCIOS POR PROFESION - TOTAL ".$wtot ); 

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
