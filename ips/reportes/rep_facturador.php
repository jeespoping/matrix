<html>
<head>
<title>Reporte Facturador por Ventas
</title>
</head>
<script type="text/javascript">
	function cerrarVentana()
	 {
      top.close();
     }

	function enviar_form()            //Funcion para enviar el formulario dependiendo del boton que se active
	{								 //ya sea anuladas o activas
	 document.forms.form1.submit();
	}

</script>
<body>
<?php
include_once("conex.php");
/*
* /REPORTE FACTURADOR POR VENTAS ACTIVAS Y/O ANULADAS                                             *
*/
// ===========================================================================================================================================
// PROGRAMA				      :Reporte por Facturador de Ventas activas o anuladas                                              			 |
// AUTOR				      :Ing. Luis Haroldo Zapata Arismendy                                                                        	 |
// FECHA CREACION			  :Diciembre 27 de 2011.                                                                                         |
// FECHA ULTIMA ACTUALIZACION :Enero 3 de 2012.                                                                                     	 |
// DESCRIPCION			      :Reporte para conocer la cantidad de facturas activas o anuladas realizadas por cada facturador														 |
//                             durante un rango de fechas                                                                                                             |
// TABLAS UTILIZADAS :                                                                                                                       |
// CLISUR_000018		 	  :Tabla que contiene la informacion de las facturas como la numeracion de las mismas, su valor
//							   y el facturador que la realizó.
// USUARIOS			  		  :tabla que contiene el codigo y el nombre del facturador relacionados con la tabla clisur_000018
//                                                                                         													 |
// ==========================================================================================================================================
$wactualiz = "Ver. 2012-01-03";

//================================================================================
include_once("root/comun.php");



if(!isset($_SESSION['user']))
	exit("error session no abierta");


$conex = obtenerConexionBD("matrix");
	if(!isset($wemp_pmla))
	{
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

$institucion = consultarInstitucionPorcodigo($conex, $wemp_pmla);
$wbasedato = strtolower($institucion->baseDeDatos);

Encabezado("REPORTE FACTURADOR POR VENTAS", $wactualiz  ,"clinica");

	if( !isset($mostrar) )
	{
		$mostrar = 'off';
	}

	echo "<form name='form1' action='rep_facturador.php?wemp_pmla=$wemp_pmla' method='post'>";

	if( $mostrar == 'off' )				//si no hay rango de fechas entonces  pedirlos al usuario
	{

		if( !isset( $fechafin ) )
		{
			$fechafin = date("Y-m-d");
		}

		if( !isset( $fechaini ) )
		{
			$fechaini = date("Y-m-01");
		}

		//El usuario selecciona el rango de fechas
		echo "</SELECT>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "<br><br><table align='center'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center' style='width:200'>Fecha inicial</td>";
		echo "<td align='center' style='width:200'>Fecha final</td>";
		echo "</tr><tr class='fila1'>";
		echo "<td align='center'>";
		campoFechaDefecto( "fechaini", $fechaini );
		echo "</td>";
		echo "<td align='center'>";
		campoFechaDefecto( "fechafin", "$fechafin" );
		echo "</td>";
		echo "</tr>";
		echo "</table>";

		// Botones de seleccion de tipo de reporte, tambien sirven como submit para enviar los datos de consulta
		echo "<table align='center'>";
		echo  "<tr>";
		echo "<tr><td colspan=4 class=fila2 align=center> ";
		echo "<input type='radio' name='estado' value='on' onclick='enviar_form()' checked> &nbsp;Facturas Activas ";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type='radio' name='estado' value='off'  onclick='enviar_form()' > &nbsp;Facturas Anuladas ";
		echo "</td></tr>";

		//Botones ver y cerrar el formulario
		echo "<br><table align='center'>";
		echo "<br>";
		echo  "<tr>";
		echo  "<td colspan=2 width='208' class=fila3  align='center'><INPUT type='submit' value='Ver' style='width:100' name='btVer'></INPUT></td>";
		echo  "<td colspan=2 width='208' class=fila3  align='center'><INPUT type='button' value='Cerrar Ventana' style='width:100' onClick='cerrarVentana();'></INPUT></td>";
		echo  "</tr>";
		echo  "</table>";

		echo "<INPUT type='hidden' name='mostrar' value='on'>";

		echo "</form>";

	}
	 else
		{	//informacion ingresada por el usuario
			echo "<br><table align='center'>";
			echo "<tr align='left'>";
			echo "<td width='150' class='fila1'>Fecha inicial</td>";
			echo "<td width='150' class='fila2'>$fechaini</td>";
			echo "</tr>";
			echo "<tr class='fila1' align='left'>";
			echo "<td class='fila1'>Fecha final</td>";
			echo "<td class='fila2'>$fechafin</td>";
			echo "</tr>";
			echo "</table><br><br>";

			//Encabezados de la tabla
		 	echo "<table align=center >";
			echo "<tr class='encabezadoTabla'>";
			echo "<td align=center>&nbsp;Codigo<br>Facturador&nbsp;</td>";
			echo "<td align=center>&nbsp;Nombre<br>Facturador&nbsp;</td>";
			echo "<td align=center>&nbsp;Cantidad <br>Facturas</td>";
			echo "<td align=center>&nbsp;Total <br> Facturado&nbsp;</td>";
			echo "<td align=center>&nbsp;Nota Credito&nbsp;</td>";
			echo "<td align=center>&nbsp;Descuento&nbsp;</td>";
			echo "<td align=center>&nbsp;Total Fac<br>Neto&nbsp;</td>";
			echo "<td align=center>&nbsp;Estado&nbsp;</td>";
			echo "</tr>";

			//Esta consulta me permite obtener la cantidad y el valor total de facturas agrupadas por cada facturador
			//y si han presentado notas credito o descuentos
			//                    0      1               2                                3         4           5
			$q = "  SELECT   count(*), seguridad,SUM(fenval+fenabo+fencmo+fencop+fendes),fenest,sum(fenvnc),sum(fendes),"
				."  SUM(fenval+fenabo+fencmo+fencop+fendes-fenvnc-fendes)"
				."  FROM     ".$wbasedato."_000018"
				."  WHERE    Fenfec between '".$fechaini."' and '".$fechafin."'"
				."  AND      Fenest = '".$estado."'"
				."  GROUP BY seguridad";

			$result = mysql_query($q, $conex) or die("ERROR EN QUERY $q - ".mysql_error() );
			$num = mysql_num_rows( $result );
			$cantfact=0;
			$totalfacturado = 0;
			$totalNotacredito=0;
			$totaldescuento=0;
			$totalfacneto=0;

			for ($i=1;$i<=$num;$i++)
			{

			 if (is_int ($i/2))
			  {
			   $wclass="fila1"; 					 // color de fondo= sentencia para colocar color a los datos
			  }
			 else
			  {
			   $wclass="fila2"; // color de fondo
			  }

				$row = mysql_fetch_array($result);
				$cod = explode('-',$row[1]);

				$wfenest=$row['fenest'];

				$query = " SELECT descripcion "						//Esta consulta me trae el nombre del facturador relacionado
					."   	FROM  usuarios "						//con la tabla de ventas y el campo seguridad = campo codigo.
					."      WHERE codigo= '".$cod[1]."'";
				$res = 	  mysql_query($query,$conex);
				$rowpro = mysql_fetch_array($res);

				//Muestro los datos seleccionados por facturador
				echo "<Tr class=".$wclass.">";
				echo "<td align=center><font size=2>$cod[1]</font></td>";
				echo "<td align=left><font size=2>".strtoupper($rowpro[0])."</font></td>";
				echo "<td align=right><font size=2><strong>".number_format($row[0],0,'.',',')."</strong></font></td>";
				echo "<td align=right><font size=2><strong>".number_format($row[2],0,'.',',')."</strong></font></td>";
				echo "<td align=right><font size=2><strong>".number_format($row[4],0,'.',',')."</strong></font></td>";
				echo "<td align=right><font size=2><strong>".number_format($row[5],0,'.',',')."</strong></font></td>";
				echo "<td align=right><font size=2><strong>".number_format($row[6],0,'.',',')."</strong></font></td>";

				if($row['fenest']=='on')
					$wfenest='Activas';							//Es para que muestre en el campo estado la palabra Activas en vez de on
				else
					$wfenest='Anuladas';
				echo "<td align=center><font size=2>$wfenest</font></td>";

				$cantfact=$cantfact+$row[0];
				$totalfacturado = $totalfacturado+$row[2];
				$totalNotacredito=$totalNotacredito+$row[4];
				$totaldescuento=$totaldescuento+$row[5];
				$totalfacneto=$totalfacturado-$totalNotacredito-$totaldescuento;

			}

				echo "<tr class='encabezadoTabla'>";//este class es para colocarle color al total de los datos
				echo"<td align=center colspan=2>Total</td>";
				echo "<td align=right><font size=2><strong>" .number_format($cantfact)." </strong></font></td>";
				echo "<td align=right><font size=2><strong>" .number_format($totalfacturado)." </strong></font></td>";
				echo "<td align=right><font size=2><strong>" .number_format($totalNotacredito)." </strong></font></td>";
				echo "<td align=right><font size=2><strong>" .number_format($totaldescuento)." </strong></font></td>";
				echo "<td align=right><font size=2><strong>" .number_format($totalfacneto)." </strong></font></td>";
				echo "<td></td>";

				echo "</tr>";
				echo "</table>";


				//Boton para retornar a los campos de fechas o para cerrar el formulario
				echo "<br>";
				echo "<table align='center'>";
				echo "<tr>";
				echo "<td align=center width='150'>";
				echo "<INPUT type='submit' value='Retornar' style='width:100'>";
				echo "</td>";
				echo "<td align= center width='150'>";
				echo "<INPUT type='button' value='Cerrar Ventana' onClick='javascript:cerrarVentana();' style='width:100'>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";

				echo "<INPUT type='hidden' name='mostrar' value='off'>";
				echo "<INPUT type='hidden' name='fechaini' value='$fechaini'>";
				echo "<INPUT type='hidden' name='fechafin' value='$fechafin'>";
		}
 ?>
 </body>
 </html>
