<html>
<head>
<title>Reporte de ventas neto
</title>
</head>
<script type="text/javascript">
	function cerrarVentana()
	 {
      top.close();
     }

</script>

<body>
<?php
include_once("conex.php");
/*
//REPORTE DE VENTAS NETO
// ===========================================================================================================================================
// PROGRAMA				      :Reporte de ventas neto                                             			 			 |
// AUTOR				      :Ing. Luis Haroldo Zapata Arismendy                                                                        	 |
// FECHA CREACION			  :Enero-5-2012                                                                                       			 |
// FECHA ULTIMA ACTUALIZACION :Enero-17-2012                                                                                     			 |
// DESCRIPCION			      :Reporte que muestra las ventas de un determinado periodo menos los movimientos generados en el mismo
//							   como las notas credito y los descuentos																		 |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// SOE_000018		 	 	 :Tabla que contiene los campos de facturacion solicitados como fecha, fuente, numero de factura y valor
// SOE_000024             	 :Tabla que contiene el maestro de empresas y de alli traemos el nit y el nombre de la entidad
// SOE_000021			  	 :Tabla que contiene la informacion de las notas credito generadas en el mes y a que facturas.
// SOE_000020				 :Tabla que contiene
// SOE_000040			 	 :Tabla que posee el maestro de fuentes de cartera                                                                  													 |
// ==========================================================================================================================================
*/
$wactualiz = "2012-01-23";

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

Encabezado("REPORTE FACTURACION NETO", $wactualiz  ,"clinica");

if( !isset($mostrar) )
	{
		$mostrar = 'off';
	}


echo "<form action='rep_facturacionneto.php?wemp_pmla=$wemp_pmla' method='post'>";
if( $mostrar == 'off' )
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
	//Seleccionamos la Entidad o empresa responsable

	echo "<br><br><table align= center class = encabezadotabla>";
	echo "<tr align='center'>";
	echo "<td>Responsable</td>";
	echo "</tr>";
	echo "<tr align='center'>";
	echo "<td>";
	echo "<select name= 'wemp'>";

	$sql= " SELECT  empnit,empnom "				//De la tabla del maestro de empresas traemos el nit y el nombre
		 ."   FROM ".$wbasedato."_000024 "		//donde el codigo de la empresa sea igual al codigo de la empresa responsable
		 ."  WHERE empcod= empres "
		 ."  ORDER BY 1 ";

	$res=mysql_query($sql);

	$num= mysql_num_rows($res);
	$wemp='% - Todas las empresas';
	if ($num >0)
		{
			if ($wemp!='%-Todas las empresas')
				echo "<option>%-Todas las empresas</option>";
			for($i=1;$i<=$num;$i++)
			{
				$row=mysql_fetch_array($res);
				echo "<option> ".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";

		//Agregamos los botones de ver y cerrar
		echo "<br><table align='center'>";
		echo "<tr>";
		echo "<td align='center' width='150'><INPUT type='submit' value='Ver' style='width:100' name='btVer'></INPUT></td>";
		echo "<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></INPUT></td>";
		echo "</tr>";
		echo "</table>";

		echo "<INPUT type='hidden' name='mostrar' value='on'>";
		echo"</form>";
}
	else
	{
		//informacion ingresada por el usuario
		echo "<br><table align='center'>";
		echo "<tr align='left'>";
		echo "<td width='150' class='fila1'>Fecha inicial</td>";
		echo "<td width='150' class='fila2'>$fechaini</td>";
		echo "</tr>";
		echo "<tr class='fila1' align='left'>";
		echo "<td class='fila1'>Fecha final</td>";
		echo "<td class='fila2'>$fechafin</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='fila1'>Empresa:</td>";
		echo "<td class='fila2'>$wemp</td>";
		echo "</tr>";
		echo "</table><br><br>";

		$wemp_str= explode("-",$wemp);
		$wemp=$wemp_str[0];

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


		//Realizamos dos consultas: una para que me traiga las facturas del mes y otra con las notas credito generadas en el mes.
		//Realizamos una union entre las dos consultas para traer la informacion tanto de facturas como de notas.
		$q= "(SELECT empcod,fennit as empnit,empnom,fenffa,fenfac,fenfec,'' as renfue,'' as rennum,fenfec as renfec ,fendpa,fennpa,(fenval+fenabo+fencmo+fencop+fendes) as valorfact,fensal, "
			."		 '0' as fenvnc,fendes,(fenval+fenabo+fencmo+fencop-fenvnc) as neto "		//Este query me trae las facturas del mes
			."  FROM ".$wbasedato."_000018 A,".$wbasedato."_000024 D "
			." WHERE fenfec BETWEEN '$fechaini' and '$fechafin' "
			."   AND A.fennit like '".$wemp."' "
			."   AND A.fencod= D.empcod "
			."   AND D.empcod=D.empres "
			."   AND fenest = 'on' )"

			."UNION "
		    ."(SELECT empcod,empnit,empnom,fenffa,fenfac,fenfec,renfue,rennum,renfec,fendpa,fennpa,(fenval+fenabo+fencmo+fencop+fendes)*0 as valorfact,fensal,"
			."		  B.rdevco as fenvnc,0 as fendes,B.rdevco*(-1) as neto "
			."   FROM ".$wbasedato."_000018 A,".$wbasedato."_000021 B,".$wbasedato."_000040 C,".$wbasedato."_000024 D,".$wbasedato."_000020 E "					//Este me trae las notas credito generadas en el mes
			."  WHERE E.renfec between '$fechaini' and '$fechafin'  "				//este me trae las notas generadas en el mes
			."    AND A.fennit LIKE '".$wemp."' "
			."    AND A.fencod=D.empcod "
			."    AND D.empcod=D.empres "
			."    AND C.carncr='on' "
			."    AND B.rdeest='on' "
			."    AND B.rdefue=C.carfue "
			."    AND E.renfue=B.rdefue "
			."    AND E.rennum=B.rdenum "
			."    AND A.fenffa=B.rdeffa "
			."    AND A.fenfac=B.rdefac  )"
			."ORDER BY empnit,fenfec, fenfac "	;


		$res= mysql_query($q,$conex) or die ("ERROR EN QUERY $q - ".mysql_error());
		$num= mysql_num_rows($res);

		$row= mysql_fetch_array($res);
		$wgrantotalfac=0;
		$wgrantotalnc=0;
		$wgrantotaldesc=0;
		$wgrantotalneto=0;

		for($i=0;$i<$num; )
		{
			$wtotalfac=0;
			$wtotalnc=0;
			$wtotaldesc=0;
			$wtotalneto=0;

			$wempnit=$row['empnit'];
			$wempnom=$row['empnom'];

			//Encabezados de la tabla

			echo "<table style='width: 100%;' align=center >";
			echo "<tr><td align=left colspan=12 class='titulo'>" .$wempnit." - " .$wempnom. "</td></tr>";
			echo "<br>";
			echo "<tr class='encabezadoTabla'>";
			echo "<td align=center>&nbsp;Fuente<br>Factura&nbsp;</td>";
			echo "<td align=center>&nbsp;Nro<br>Factura&nbsp;</td>";
			echo "<td align=center>&nbsp;Fecha <br>Factura&nbsp</td>";
			echo "<td align=center>&nbsp;Fuente <br>Nc&nbsp</td>";
			echo "<td align=center>&nbsp;Nro <br>Nc&nbsp</td>";
			echo "<td align=center>&nbsp;Fecha <br>Nc&nbsp</td>";
			echo "<td align=center>&nbsp;Identificacion <br> Paciente&nbsp;</td>";
			echo "<td align=center>&nbsp;Nombre paciente&nbsp;</td>";
			echo "<td align=center>&nbsp;Vlr Factura&nbsp;</td>";
			echo "<td align=center>&nbsp;Vlr <br> Nota Credito&nbsp;</td>";
			echo "<td align=center>&nbsp;Descuento&nbsp;</td>";
			echo "<td align=center>&nbsp;Total Fac<br>Neto&nbsp;</td>";
			echo "</tr>";

			$wnotascreditos = array();
			$fa="";
			$rowspan=0;
			$j=1;
			$z=1;

			$wsumanotacredito = 0;

			while($wempnit==$row['empnit']) 			 // mientras sea el mismo nit, que me traiga la informacion de los pacientes
			{										   	// que posee ese nit

				$wrenfue=$row['renfue'];
				$wrennum=$row['rennum'];
				$wrenfec=$row['renfec'];
				$wfenvnc=$row['fenvnc'];
				$wfendes=$row['fendes'];

				if (is_int ($z/2))
				{
					$wclass="fila1"; 					 	// color de fondo= sentencia para colocar color a los datos
				}
				else
				{
					$wclass="fila2";
				}


				if( strtoupper( $fa )==strtoupper( $row['fenfac']) )         //si la variable es igual a la factura
				{															// que verifique si los campos de la nota credito poseen informacion

					if($wrenfue!= "")
					{
						$wnotascreditos[ count($wnotascreditos) ] = array( 0 => $wrenfue, 1 => $wrennum, 2 => $wrenfec, 3 => $wfenvnc );     //mientras se trate de la misma factura

						$wsumanotacredito = $wsumanotacredito + $wfenvnc;																	//si tiene varias notas que las guarde en el array
					}																													//y que me sume los valores de las notas
					if($wvalorfact==0)
					{
						$wvalorfact = $row['valorfact'];				//si la primera fila es la nota que me traiga el valor de la factura
					}													//que esta en la segunda fila

					$j++;   //para que me siga recorriendo por filas
				}
					else
					{

						$wfenffa=$row['fenffa'];				//Declaro las variables que voy a mostrar en el informe
						$wfenfac=$row['fenfac'];
						$wfenfec=$row['fenfec'];
						$wfendpa=$row['fendpa'];
						$wfennpa=$row['fennpa'];
						$wvalorfact=$row['valorfact'];

						if( $wrenfue != "" )    				//sino tiene varias notas sino una la guardo en el array y la sumatoria seria igual al valor del campo nota
						{
							$wnotascreditos[0] = array( 0 => $wrenfue, 1 => $wrennum, 2 => $wrenfec, 3 => $wfenvnc );
							$wsumanotacredito = $wfenvnc;
						}
					 }

				$fa=$wfenfac;

				$wtotalfac=$wtotalfac+$row['valorfact'];				//Estas son las variables para totalizar por Entidad
				$wtotalnc=$wtotalnc+$row['fenvnc'];
				$wtotaldesc=$wtotaldesc+$row['fendes'];
				$wtotalneto=$wtotalfac-$wtotalnc-$wtotaldesc;

				$row= mysql_fetch_array($res);
				$i++;

				if(strtoupper( $fa )!=strtoupper( $row['fenfac']) )    //En esta parte pinto las filas y agrupo las notas credito por factura
				{

					if( $i > 0 )
					{

						$rowspan = count( $wnotascreditos );

						$wtienenotascredito = true;

						if( $rowspan == 0 )
						{
							$rowspan = 1;
							$wtienenotascredito = false;
						}

						for( $k = 0; $k < $rowspan; $k++)
						{

							if( !isset($wnotascreditos[$k][3]) or trim($wnotascreditos[$k][3]) == "" ){
								$wnotascreditos[$k] = array();
								$wnotascreditos[$k][3] = 0;
							}

							if( $k == 0 )
							{
								echo "<tr class=".$wclass.">";
								echo "<td rowspan='$rowspan' align=center> $wfenffa </td>";				//Muestro la informacion de la factura
								echo "<td rowspan='$rowspan' align=center> $wfenfac </td>";
								echo "<td rowspan='$rowspan' align=center> $wfenfec </td>";

								if( $wtienenotascredito == true )						//si hay nota credito imprimo los datos de fuente,numero y fecha
								{

									echo "<td align=center>".$wnotascreditos[$k][0]." </td>";
									echo "<td align=center>".$wnotascreditos[$k][1]."</td>";
									echo "<td align=center>".$wnotascreditos[$k][2]."</td>";

								}
								else													//si no hay nota credito que no muestre nada
								{
									echo "<td align=center></td>";
									echo "<td align=center></td>";
									echo "<td align=center></td>";

								}

								echo "<td rowspan='$rowspan' align=right>  $wfendpa</td>";
								echo "<td rowspan='$rowspan'  align=left>   $wfennpa </td>";
								echo "<td rowspan='$rowspan'  align=right>".number_format($wvalorfact). "</td>";

								if( $wtienenotascredito == true )
								{
									echo "<td align=right>".number_format($wnotascreditos[$k][3])."</td>";
								}
								else
								{
									echo "<td align=center></td>";
								}

								echo "<td rowspan='$rowspan' align=right>".number_format($wfendes)." </td>";
								echo "<td rowspan='$rowspan' align=right>".number_format($wvalorfact-$wsumanotacredito-$wfendes)." </td>";

								echo "</tr>";
								$z++;

							}
							else
							{
								echo "<tr class=".$wclass.">";
								echo "<td align=center>".$wnotascreditos[$k][0]." </td>";
								echo "<td align=center>".$wnotascreditos[$k][1]."</td>";
								echo "<td align=center>".$wnotascreditos[$k][2]."</td>";
								echo "<td align=right>".number_format($wnotascreditos[$k][3])."</td>";
								echo "</tr>";
							}
						}

						$wsumanotacredito = 0;
						$wnotascreditos = array();
					}
				}
			}

			echo "<tr class='encabezadoTabla'>";
			echo"<td align=center colspan=8>Total</td>";
			echo "<td align=right><font size=2><strong>" .number_format($wtotalfac)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wtotalnc)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wtotaldesc)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wtotalneto)." </strong></font></td>";

			$wgrantotalfac+=$wtotalfac;
			$wgrantotalnc=$wgrantotalnc+$wtotalnc;
			$wgrantotaldesc=$wgrantotaldesc+$wtotaldesc;
			$wgrantotalneto=$wgrantotalneto+$wtotalneto;
		}

		if($wgrantotalfac<>0)
		{

			echo "<tr class='encabezadoTabla'>";
			echo"<td align=center colspan=8>Total Empresas</td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotalfac)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotalnc)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotaldesc)." </strong></font></td>";
			echo "<td align=right><font size=2><strong>" .number_format($wgrantotalneto)." </strong></font></td>";
			echo "</table>";
		}
		else
		{
			echo "<center><b>No se encontraron resultados</b></center>";
		}

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
