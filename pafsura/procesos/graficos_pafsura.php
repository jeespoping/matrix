<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
    die ("Para entrar correctamente a la aplicacion debe hacerlo por la pagina index.php");



mysql_select_db("matrix") or die("No se selecciono la base de datos"); 

//Conexion a Informix Creada en el "DSN del Sistema"
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");  

/* OJO: COPIAR LA CARPERA src DENTRO DE LA CARPETA include   

 => Este modulo para que funcionara con la version del php en Matrix se comento la linea:
    JpGraphError::RaiseL(25128); en el archivo gd_image.inc.php pues
    marcaba error:
    The function imageantialias() is not available in your PHP installation. Use the GD version that comes with PHP and not the standalone version.
   
 => Si al ejecutar este modulo saca el siguiente mensaje:
   
    JpGraph Error: HTTP headers have already been sent.
    Caused by output from file graficos.php at line xx. 
    Explanation:
    HTTP headers have already been sent back to the browser indicating the data as text before the library got a chance to send it's image HTTP header
    to this browser. This makes it impossible for the library to send back image data to the browser (since that would be interpretated as text by the 
    browser and show up as junk text).
    Most likely you have some text in your script before the call to Graph::Stroke(). If this texts gets sent back to the browser the browser will 
    assume that all data is plain text. Look for any text, even spaces and newlines, that might have been sent back to the browser. 

    For example it is a common mistake to leave a blank line before the opening "<?php".
 
    CAUSA:
    Tiene un "echo" y no se permite o efectivamente hay una linea en blanco antes de <?php.
    
 => Un mensaje como este: Illegal pie Plot. Sum of all is zero for Pie plot 
    Significa que hay un problema con los datos, puede ser que se este llevando valores no numericos
   
    JairS
*/
//$wind=4;
//$wfec1="2012-07-01";
//$wfec2="2012-07-31";

include_once("src/jpgraph.php"); 
include_once("src/jpgraph_pie.php"); 
include_once("src/jpgraph_pie3d.php");    

      switch ($wind) 
      {
       case "1": 
        // tablas en Matrix
        $matrix="S";
        $query = "SELECT pafdia AS codigo,descripcion AS nombre,count(*) AS valor FROM paf_000001,root_000011"
        ." WHERE pafdia=codigo AND paffec BETWEEN '".$wfec1."' AND '".$wfec2."' AND pafest='A' GROUP BY pafdia,descripcion ORDER BY count(*) DESC"; 
        $titulo= "PROGRAMA CARDIOVASCULAR - ORDENES POR DIAGNOSTICO - TOTAL ORDENES ";
        $matrix="S";
        break;
       case "2": 
        $matrix="S";
        $query = "SELECT pafexa AS codigo,nombre AS nombre,count(*) AS valor FROM paf_000001,root_000012"
        ." WHERE pafexa=codigo AND paffec BETWEEN '".$wfec1."' AND '".$wfec2."' AND pafest='A' GROUP BY pafexa,nombre ORDER BY count(*) DESC";
        $titulo= "PROGRAMA CARDIOVASCULAR - ORDENES POR EXAMEN - TOTAL ORDENES ";
        $matrix="S";
        break;
       Case  "3":
        $matrix="S";
        $query = "SELECT pafcco AS codigo,cconom AS nombre,count(*) AS valor FROM paf_000001,costosyp_000005"
        ." WHERE pafcco=ccocod AND paffec BETWEEN '".$wfec1."' AND '".$wfec2."' AND pafest='A' GROUP BY pafcco,cconom ORDER BY count(*) DESC";
        $titulo= "PROGRAMA CARDIOVASCULAR - ORDENES POR SERVICIO - TOTAL ORDENES ";
        break;
        Case  "4": 
        // tablas en UNIX 
        $matrix="N";
        $query = "Select cardetcco AS codigo,cconom AS nombre,'H' AS tipo,SUM(cardettot*conmul) AS valor"
        . " From FACARDET, OUTER cocco,FACON"
        . " Where cardettar='PS' "
       // . " And cardetano = '".substr($wfec1,0,4)."'"
       // . " And cardetmes BETWEEN '".substr($wfec1,5,2)."' AND '".substr($wfec2,5,2)."'"
        . " And cardetfec BETWEEN '".$wfec1."' AND '".$wfec2."'"
        . " And cardetcon NOT IN ('2101','2105')"
        . " And cardetanu = '0' "
        . " And cardetcco=ccocod"
        . " And cardetcon=concod"
        . " GROUP BY 1,2,3"
        . " UNION "
        . " Select cardetcco AS codigo,cconom AS nombre,'A' as tipo,SUM(cardettot*conmul) AS valor"
        . " From AYCARDET, OUTER cocco,FACON"
        . " Where cardettar='PS' "
       // . " And cardetano = '".substr($wfec1,0,4)."'"
       // . " And cardetmes BETWEEN '".substr($wfec1,5,2)."' AND '".substr($wfec2,5,2)."'"
        . " And cardetfec BETWEEN '".$wfec1."' AND '".$wfec2."'"
        . " And cardetcon NOT IN ('2101','2105')"
        . " And cardetanu = '0' "
        . " And cardetcco=ccocod"
        . " And cardetcon=concod"
        . " GROUP BY 1,2,3"
        ."  ORDER BY 4 DESC";
        $titulo= "PROGRAMA CARDIOVASCULAR - TOTAL CARGOS HOSPITALIZACION Y AYUDAS ";
        break;        
      }
 
// Cuando el query  es sobre tablas en MYSQL
   if ($matrix=="S") 
     {
       // El programa funciona siempre y cuando reciba un query que genere tres columnas Codigo, Nombre y Cantidad
       $resultado = mysql_query($query);
       $nroreg = mysql_num_rows($resultado);
       $i = 0;
       $wtot=0;
       While ($i < $nroreg)
       {
         $registro = mysql_fetch_row($resultado);  
         $nombres[$i] = $registro[0]." ".substr($registro[1],0,30)." ".$registro[2];
         $data[$i] = $registro[2];
         $wtot=$wtot+$data[$i];
         $i++;
       }
     }
     else
     {    
// Cuando el query  es sobre tablas de INFORMIX       
      $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
       $nroreg= 0;
       $wtot=0;
       $i=0;
       while (odbc_fetch_row($resultado))
       {  if (substr(odbc_result($resultado,2),0,27)=="")  // pasa en el caso del OUTER cargos sin Ccosto
           $nombres[$i] = substr("SIN CENTRO DE COSTO ASIGNADO",0,27)." ".odbc_result($resultado,3)." ".number_format(odbc_result($resultado,4),0);
          else
           $nombres[$i] = substr(odbc_result($resultado,2),0,27)." ".odbc_result($resultado,3)." ".number_format(odbc_result($resultado,4),0);
         $data[$i] = odbc_result($resultado,4);
         $wtot=$wtot+odbc_result($resultado,4);
         $i++;
       }
       odbc_close($conexN);
       
     }
   //$nombres=array("Ingresado","Asignado","En Proceso","Entregado","Rechazado"); 
   //$data = array(15,3,20,40,5); 

   // $graph = new PieGraph(1080,500,"auto"); 
   $graph = new PieGraph(1200,650,"auto"); 
   $graph->img->SetAntiAliasing(); 
   $graph->SetMarginColor('gray'); 
   //$graph->SetShadow(); 
   // Setup margin and titles 
   $graph->title->Set($titulo." ".number_format($wtot,0) ); 

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

</BODY>
</HTML>
