<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066" onload=ira()>
<center>
<?php
include_once("conex.php");
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
/************************************
*     REPORTE MENSAJERIA EXTERNA    *
*									*
************************************/
//=======================================================
//AUTOR			:Gabriel Agudelo Zapata
$wautor="Gabriel Agudelo Zapata";
//FECHA CREACION :Septiembre 2011
//FECHA ULTIMA ACTUALIZACION 	:
$wactualiz="(Versión 2011-09-26)";
/*DESCRIPCION	:Trae el reporte entre dos fechas de la Mensajeria 
                 Externa de Administraxcion de Documentos por centro de costos. */

echo "<center><table border>";
echo "<tr><td align=center colspan=2 bgcolor=#000066><font size=5 text color=#FFFFFF><b>MENSAJERIA EXTERNA POR CENTRO DE COSTOS</b></font></td></tr>";
echo "<tr>";
echo "</center>";
 session_start();
 if (!isset($_SESSION['user']))
    echo "error";
   else
	   { 
		$key = substr($user,2,strlen($user));
		

		

		$wfecha=date("Y-m-d");
		
		$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
		
		echo "<form action='estadistica_mensajeria_externa.php?wemp_pmla=".$wemp_pmla."' name=estadistica_mensajeria_externa method=post>";
		
		if (!isset($wfecini) or !isset($wfecfin) )
		   {
			echo "<table border=1 align=center>";   
			echo "<tr>";
			?>	    
		      <script>
		          function ira(){document.estadistica_mensajeria_externa.wfecini.focus();}
		          function ira(){document.estadistica_mensajeria_externa.wfecini.select();}
		      </script>
		      <?php
		    echo "<td align=center bgcolor=#cccccc><b>Fecha Inicial (AAAA-MM-DD): <br></font></b><INPUT TYPE='text' NAME='wfecini' VALUE='".$wfecha."'></td>";
		    echo "<td align=center bgcolor=#cccccc><b>Fecha Final (AAAA-MM-DD): <br></font></b><INPUT TYPE='text' NAME='wfecfin' VALUE='".$wfecha."'></td>";
		    echo "</tr>";
		    
	        echo "<tr>";
		    echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";
		    echo "</tr>";
		    
		    echo "</table>";
	      } 
		  else
		     {
                $query = "   SELECT Habitacion, count(Habitacion) "
               		   ."    FROM ".$wcencam."_000003 "
				       ."    WHERE Fecha_data between '".$wfecini."' AND '".$wfecfin."' "
				       ."    AND Central = 'MSGEXT' "
				       ."    AND Anulada = 'No' "
				       ."    GROUP BY Habitacion "
				       ."    ORDER BY Habitacion"; 
		       
	       		$err = mysql_query($query,$conex);
		   		$num = mysql_num_rows($err);
		   	
				echo "<table border=1 align=center>";
				echo "<tr><td colspan=3 align=center bgcolor=#DBDFF8><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></td></tr>";
				echo "<tr><td colspan=3 align=center bgcolor=#DBDFF8><b>DIRECCION DE INFORMATICA</b></td></tr>";
				echo "<tr>";
				echo "<td colspan=3 align=center bgcolor=#DBDFF8><b>Fecha Inicial: </b>".$wfecini." - <b>Fecha Final:</b> ".$wfecfin."</td>";
				echo "</tr>"; 	
		
				echo "<tr>";
				echo "<td align=center bgcolor=#DBDFF8><b>CENTRO DE COSTOS</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>CANTIDAD</b></td>";
				echo "</tr>";
			
				for ($i=0;$i<$num;$i++)
				   {
					if ($i==0){
							$s=$i;
						}
					$row = mysql_fetch_array($err);
					echo "<tr>";
					if (substr($row[0],0,11)=='<b>Historia'){
						echo "<td align=center>".substr($row[0],0,19)."</td>";
						echo "<td align=center>".$row[1]."</td>";
					}else{
						echo "<td align=center>".substr($row[0],0,15)."</td>";
					   	echo "<td align=center>".$row[1]."</td>";
					}
					
				    echo "</tr>";
				    $s = $s + $row[1];				    
				   }
			    echo "<td align=center bgcolor=#DBDFF8><b>TOTAL: </b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>".$s."</b></td>";
				echo "</table>"; 
		     }
	   }
?>
</body>
</html>
