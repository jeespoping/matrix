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

$query = "SELECT socsex,count(*)  FROM socios_000001 WHERE socact='A' GROUP BY socsex "; 

$resultado = mysql_query($query);
$nroreg = mysql_num_rows($resultado);
$i = 0;
$wtot=0;
While ($i < $nroreg)
{
 $registro = mysql_fetch_row($resultado);  
 if ($registro[0]=="F")
   $ws="FEMENINO";
 else
   $ws="MASCULINO";
   
 $nombres[$i] = $ws." ".$registro[1];
 $data[$i] = $registro[1];
 $wtot=$wtot+$data[$i];
 $i++;
}

$graph = new PieGraph(750,500,"auto"); 
$graph->img->SetAntiAliasing(); 
$graph->SetMarginColor('gray'); 
//$graph->SetShadow(); 

// Setup margin and titles 
$graph->title->Set("SISTEMA DE INFORMACION DE SOCIOS - SOCIOS POR SEXO - TOTAL ".$wtot ); 

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
