<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066" onload=ira()>
<!-- Estas 5 lineas es para que funcione el Calendar al capturar fechas -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    
    
<center>
<?php
include_once("conex.php");
/************************************
*    REPORTE CONTRATOS PROXIMOS A   * 
*    VENCER ENTRE LA FECHA ACTUAL Y *
*	 LOS PROXIMOS 3 MESES		    *
************************************/
//=======================================================
//AUTOR			:Gabriel Agudelo Zapata
$wautor="Gabriel Agudelo Zapata";
//FECHA CREACION :Julio 23 2012
//FECHA ULTIMA ACTUALIZACION 	:
$wactualiz="(Versión 2012-07-23)";
/*DESCRIPCION	:Trae el reporte de los contratos proximos a vencer entre la fecha actual y los proximos 3 meses */
                 

 session_start();
 if (!isset($_SESSION['user']))
    echo "error";
   else
	   { 
		$key = substr($user,2,strlen($user));
		

		

		$wfecha1=date("Y-m-d");
		$wfecha2=date("Y-m-d", strtotime("$fecha_inicio + 3 month"));
		echo "<form action='rep_contratos.php' name=rep_contratos method=post>";
		
			{
                $query = "   SELECT Concon, Connum, Contip, Conpa1, Conpa2, Connit, Conobj, Convac, Confin, Conffi, Confpr, Conres, Conpro, Conter   "
               		   ."    FROM talento_000002 "
				       ."    WHERE Confpr between '".$wfecha1."' AND '".$wfecha2."' ";
				        
		        $err = mysql_query($query,$conex);
		   		$num = mysql_num_rows($err);
		   	
				echo "<table border=1 align=center>";
				echo "<tr><td colspan=14 align=center bgcolor=#DBDFF8><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
				echo "<tr><td colspan=14 align=center bgcolor=#DBDFF8><b>DIRECCION DE INFORMATICA</b></td></tr>";
				echo "<tr>";
				echo "<td colspan=15 align=center bgcolor=#DBDFF8><b>Contratos proximos a vencer entre las Fechas </b>".$wfecha1." <b> y </b> ".$wfecha2."</td>";
				echo "</tr>"; 	
		
				echo "<tr>";
				echo "<td align=center bgcolor=#DBDFF8><b>#</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>CONTRATO</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>TIPO CONTRATO</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>PARTE 1</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>PARTE 2</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>NIT</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>OBJETO</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>VALOR</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>FECHA INICIAL</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>FECHA FINAL</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>FECHA PREAVISO</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>RESPONSABLE</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>PRORROGA</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>FIN</b></td>";
				echo "</tr>";
			
				for ($i=0;$i<$num;$i++)
				   {
					if ($i==0){
							$s=$i;
						}
					$row = mysql_fetch_array($err);
					echo "<tr>";
						echo "<td align=center>".$row[0]."</td>";
						echo "<td align=center>".$row[1]."</td>";
						echo "<td align=center>".$row[2]."</td>";
						echo "<td align=center>".$row[3]."</td>";
						echo "<td align=center>".$row[4]."</td>";
						echo "<td align=center>".$row[5]."</td>";
						echo "<td align=center>".$row[6]."</td>";
						echo "<td align=center>".$row[7]."</td>";
						echo "<td align=center>".$row[8]."</td>";
						echo "<td align=center>".$row[9]."</td>";
						echo "<td align=center>".$row[10]."</td>";
						echo "<td align=center>".$row[11]."</td>";
						echo "<td align=center>".$row[12]."</td>";
						echo "<td align=center>".$row[13]."</td>";
					echo "</tr>";
				    $s = $s + 1;				    
				   }
			    echo "<td colspan=12 align=center bgcolor=#DBDFF8><b>TOTAL CONTRATOS PROXIMOS A VENCER: </b></td>";
				echo "<td colspan=2 align=center bgcolor=#DBDFF8><b>".$s."</b></td>";
				echo "</table>"; 
		     }
	   }
	
?>
</body>
</html>
