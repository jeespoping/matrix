<html>
	<head>		
	</head>
	<body>
		<!--<label>
			<div align="right"> Desarrollado Por: Juan Esteban Lopez A.</div>	
			&nbsp;					
		</label>-->
	
	   <SCRIPT LANGUAGE="JavaScript1.2">
		   <!--
		   function onLoad() {
		   	loadMenus();
		   }
		   //-->
		
	   function enter()
	   {
	   	document.formFacAnuladas.submit();
	   }
		</script>
		
		<?php
include_once("conex.php");
			
			/***********************************************************************************************************************************
			***    																															 ***				    ***									   		    Reporte de Facturas Anuladas por Usuario									     ***	
			***																																 ***
			************************************************************************************************************************************/			
			//==================================================================================================================================
			//PROGRAMA						:Reporte de facturas anuladas por Usuario en un determinado tiempo 
			//AUTOR							:Juan Esteban Lopez Aguirre
			//FECHA CREACION				:marzo de 2008
			//FECHA ULTIMA ACTUALIZACION 	:
			//DESCRIPCION					:En el reporte muestra las facturas anuladas asociadas a un periodo de tiempo con 
			//								 
			//MODIFICACIONES:
			//2012-08-17   Camilo Zapata:  Se le dieron los estilos estandar de los demas programas de matrix, se adicionó la columna valor de 
			//								la factura, y el total anulado(suma de los valores de las facturas)
			//===================================================================================================================================
	
		include_once("root/comun.php");

		if(!isset($wemp_pmla)){
			terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
		}

		$conex = obtenerConexionBD("matrix");
		$wactualiz='2012-08-17';
		$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

		$wbasedato = $institucion->baseDeDatos;
		$wentidad = $institucion->nombre;
		encabezado("Reporte de Facturas Anuladas por Usuario",$wactualiz,$wbasedato);

//			$feInicial = date("Y-m-d");
//			$feFinal = date("Y-m-d");
			$hora = (string)date("H:i:s");
//			$cal = "Calendario('$feInicial','')";
			$i = 1;//Controla la interaccion con el numero de gregistro en while
			$k = 1; // Controla el color de las interlineas mostradas en el reporte
			
			// Estilo del Reporte
			//
			
			echo "<form action='repFacAnulada.php' method='post' name='formFacAnuladas'>";
			
			echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
			
			session_start();
			if (!isset($_SESSION['user']))
			{
				echo "ERROR";
			}
			else
			{
//				if(!isset($btnConsultar))
				if( (!isset( $feFinal ) || !isset( $feInicial ) ) || ($feFinal < $feInicial) )
				{				
					echo"<table border='0' align='center'>";
					echo "<tr class='fila1'>";
					echo "<td align='center'>Fecha Inicial: ";
					campoFecha("feInicial");
					echo "</td>";
		           
		            echo "<td align='center'>Fecha final:";
		            campoFecha("feFinal");
		            echo "</td>";
					echo "</td>";
					echo "</tr>";

					echo "<tr class='fila2' align='center'><td colspan='2'><input type='submit' name='btnConsultar' value='Consultar' onclick='enter()'>&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:window.close();'>					
					  </td></tr>";				
					echo"</table>";
				}				
				else
				{
					echo"&nbsp;";
					echo"&nbsp;";
					
					echo"<table border='0' align='center' width='100%'>";
					echo"<tr class='fila1'>";
					echo"<td align='center' colspan='2'><b><a name='arriba'>Reporte generado para el rango de fechas:</a></b></td>";
					echo"</tr>";
					echo"<tr class='fila2'>";
					echo"<td align='right'><b>Fecha Inicial :</b>".$feInicial."</td>";
					echo"<td align='left'><b>&nbsp;&nbsp;Fecha Final :</b>".$feFinal."</td>";
					echo"</tr>";
					echo"</table>";
		
					echo"&nbsp;";
					echo"<a href='repFacAnulada.php?wemp_pmla=".$wemp_pmla."'><center>VOLVER</center></a>";
					echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
					
					echo"&nbsp;";
					
				 $qRes="select cs107.Fecha_data, fenfec, fenffa, fenfac, cs107.Audusu,CONCAT_WS(' ',cs100.pacno1, cs100.pacno2, cs100.pacap1,cs100.pacap2), cs101.ingent,cs18.Seguridad, cs18.fenval"
						." from ".$wbasedato."_000107 cs107, ".$wbasedato."_000101 cs101, ".$wbasedato."_000100 cs100, ".$wbasedato."_000018 cs18"
						." where cs18.fenfec between '".$feInicial."' and '".$feFinal."'"
						." AND cs18.fenest ='off'"
						." AND cs18.fenffa = mid(cs107.Audreg,1,2)"
						." AND cs18.fenhis = cs107.Audhis"
						." AND cs18.fening = cs107.Auding"
						." AND cs18.fenfac = mid(cs107.Audreg,4,11)"
						." AND cs107.Audacc='Anulo Factura'"
						." AND cs101.inghis = cs107.Audhis"
						." AND cs101.ingnin = cs107.Auding"
						." AND cs101.inghis = cs100.pachis" 
						." GROUP BY 1,2,3,4,5,6,7";
					
					$res = mysql_query($qRes,$conex);
					$num = mysql_num_rows($res);
					$row = mysql_fetch_array($res);
					
					if ($num == 0)
					{
						echo"<table align='center' border='0' bordercolor='#000080' width='500' style='border:solid'>";
						echo"<tr><td align='center'colspan='2'><b><font size='3' color='#000080'>La consulta entre las fechas ".$feInicial." y ".$feFinal." no Contienen Ningun Documento	Asociado</font><b></td>";							
						echo"</tr>";
						echo"</table>";
												
					} 
					else
					{
						echo"<table align='center' border='0'>";
						echo"<tr class='encabezadotabla'>";
						echo"<td align='center'><b>Fecha Modificacion</b></td>";
						echo"<td align='center'><b>Fecha Facturacion</b></td>";	
						echo"<td align='center'><b>Fuente</b></td>";
						echo"<td align='center'><b>N&#176; Factura</b></td>";
						echo"<td align='center'><b>Usuario Modificacion</b></td>";
						echo"<td align='center'><b>Nombre Paciente</b></td>";						
						echo"<td align='center'><b>Entidad Responable</b></td>";
						echo"<td align='center'><b>Valor Factura</b></td>";						
						echo"<td align='center'><b>Usuario Creacion</b></td>";	
						echo"</tr>";
						$saldoTotal=0;

						while (	$i <= $num )
						{
							if (is_int($k/2))
							{
								$color='fila1';
								$k = $k + 1;	
							}
							else
							{
								$color='fila2';
								$k = $k + 1;
									
							}// Fin if (is_int($k/2))
							echo "<tr class='".$color."'>";
							echo "<td align=center><font size=2 >".$row[0]."</font></td>";
							echo "<td align=center><font size=2 >".$row[1]."</font></td>";
							echo "<td align=center><font size=2 >".$row[2]."</font></td>";
							echo "<td align=center><font size=2 >".$row[3]."</font></td>";
							echo "<td align=center><font size=2 >".$row[4]."</font></td>";
							echo "<td align=center><font size=2 >".$row[5]."</font></td>";
							echo "<td align=center><font size=2 >".$row[6]."</font></td>";
							echo "<td align=center><font size=2 >".number_format($row[8],0,'.',',')."</font></td>";
							echo "<td align=center><font size=2 >".$row[7]."</font></td>";
							echo "</tr>";
						
							$i = $i + 1;
							$saldoTotal+=$row[8];
							$row = mysql_fetch_array($res);
							
						}//Fin while (	$i <= $num )
						echo "<tr class='encabezadotabla'>";
							echo "<td colspan=7>TOTAL ANULADO</td>";
							echo "<td>".number_format($saldoTotal,0,'.',',')."</td>";
							echo "<td>&nbsp;</td>";
						echo "</tr>";
						
						echo"</table>";
						echo"&nbsp;";
						echo"<center><A href='#Arriba'>ARRIBA</A></center>";
						
						
					}//Fin if ($num == 0)
				}
				
			}//Fin else de if (!session_is_register("user"))
		liberarConexionBD($conex);	
		?>
	</body>
</html>