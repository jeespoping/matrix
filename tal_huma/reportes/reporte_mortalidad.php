<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");

  
?>

<html>
<head>
<title>Porcentaje de Muerte</title>
 <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

	

<script type='text/javascript'>
	var chart;
   
			 
            $(document).ready(function () {	
			
					$('#tablaresultado2').LeerTablaAmericas({ 
						empezardesdefila: 3, 
						titulo : 'Muertes Violentas ' ,
						tituloy: 'cantidad',
						filaencabezado : [2,0],
						datosadicionales : 'todo'
					});	
					
					$('#tablaresultado1').LeerTablaAmericas({ 
						empezardesdefila: 3, 
						titulo : 'Muertes Violentas ' ,
						tituloy: 'cantidad',
						divgrafica: 'amchart2',
						filaencabezado : [2,1],
						invertirgrafica	 : 'si',
						datosadicionales : 'todo',
						//filadatos : 2,
						tipografico : "linea",
						columnadatos :1
		
					
					});	
					
					
		
				});	
	
			
		


</script>

<style type="text/css">
    .displ{
        display:block;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
	#tooltip
	{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>
</head>
<body >

<?php

$wactualiz = "(Diciembre 26 de 2012)";


$titulo = "Reporte de Causas de Muertes Violentas";
// Se muestra el encabezado del programa
include_once("root/comun.php");
encabezado($titulo,$wactualiz, "clinica"); 
echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';


echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<table id='tablaresultado2' align='center'>";
echo "<tr>";
echo "<td class='encabezadoTabla' colspan='4'>Porcentaje de Muertes Violentas</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='encabezadoTabla' rowspan='2'>Causa</td><td class='encabezadoTabla' colspan='3'>porcentaje</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='encabezadoTabla'>2010</td><td class='encabezadoTabla'>2011</td><td class='encabezadoTabla'>2012</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila2'>Homicidios</td><td class='fila2'>60,54%</td><td class='fila2'>50%</td><td class='fila2'>15%</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila1' >accidentes de tránsito</td><td class='fila1' >21%</td><td class='fila1' >15%</td><td class='fila1' >78%</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila2' >Otros</td><td class='fila2' >12%</td><td class='fila2' >20%</td><td class='fila2' >13%</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila1' >Suicidio</td><td class='fila1' >8%</td><td class='fila2' >15%</td><td class='fila2' >11%</td>";
echo "</tr>";
echo "</table>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";


echo "<table align='center' >";
echo "<tr align='center'>";
echo "<td><div id='amchart1' style='width:600px; height:400px;'></div></td>";
echo "</tr>";
echo "</table>";

echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<table id='tablaresultado1' align='center'>";
echo "<tr>";
echo "<td class='encabezadoTabla' colspan='4'>Porcentaje de Muertes Violentas</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='encabezadoTabla' ></td><td class='encabezadoTabla' colspan='3'>porcentaje</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='encabezadoTabla' >Causa</td>";
echo "<td class='encabezadoTabla'>2010</td><td class='encabezadoTabla'>2011</td><td class='encabezadoTabla'>2012</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila2'>Homicidios</td><td class='fila2'>60,54%</td><td class='fila2'>50%</td><td class='fila2'>15%</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila1' >accidentes de tránsito</td><td class='fila1' >21%</td><td class='fila1' >15%</td><td class='fila1' >78%</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila2' >Otros</td><td class='fila2' >12%</td><td class='fila2' >20%</td><td class='fila2' >13%</td>";
echo "</tr>";
echo "<tr>";
echo "<td class='fila1' >Suicidio</td><td class='fila1' >8%</td><td class='fila2' >15%</td><td class='fila2' >11%</td>";
echo "</tr>";
echo "</table>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<br>";

echo "<table align='center' >";
echo "<tr>";
echo "<td><div id='amchart2' style='width:600px; height:400px;'></div></td>";
echo "</tr>";
echo "<tr>";
echo "<td><div id='legenddiv' style='width:600px; height:400px;'></div></td>";
echo "</tr>";
echo "</table>";



?>
</body>
</html>
