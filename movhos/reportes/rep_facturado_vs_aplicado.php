<html>
<head>
   <title>Reporte Grabado Vs Aplicado</title>
</head>

<script type="text/javascript">
	function enter()
	  {
		document.forms.facvsapl.submit();
	  }

	function cerrarVentana()
	  {
		window.close();
	  }

</script>

<BODY>

<?php
include_once("conex.php");

/********************************************************************************************************************************************
 * REPORTE DE ARTICULOS GRABADOS VS APLICADOS X PACIENTE	                                                   								*
 *******************************************************************************************************************************************/
// ==========================================================================================================================================|
// PROGRAMA				      :Reporte para saber los articulos aplicados por paciente.                                                      |
// AUTOR				      :Ing. Juan Carlos Hernandez.                                         			                                 |
// FECHA CREACION			  :Octubre 3 DE 2007.                                                                                            |
// FECHA ULTIMA ACTUALIZACION :03 de Octubre de 2007.                                                                                        |
// DESCRIPCION			      :Este reporte sirve para ver por centro de costos-habitacio y paciente que saldo tiene pendiente de aplicar.   |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// root_000050       : Tabla de Empresas para escojer empresa y esta traer un campo para saber que centros de costos escojer.                |
// costosyp_000005   : Tabla de Centros de costos de Clinica las Americas, Laboratorio de Patologia, y Laboratorio Medico.                   |
// usuarios          : Tabla de Usuarios con su codigo y descripcion.                                                                        |
// ===========================================================================================================================================
// Modificaciones:
// Mayo      20 de 2014: (Camilo Zapata) Se agregaron union con consultas sobre la tabla de respaldo de la de detalles de cargos(000003) porq
//										 la estaban consultando y no salian los registros anteriores a la fecha de realización del respaldo
// Diciembre 11 de 2013: (Jonatan Lopez) Se agrega el parametro SQL ALL a la consulta de los detalles de aplicado, devuelto y descarte, ademas
//										se diferencia con un alias cada una de las consultas para saber de donde sale la informacion y asi tener
//									  	una respuesta mas correcta.																		|
// Noviembre  5 de 2013: (Jonatan Lopez)Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 para que traiga los     |
//										datos de contingencia (tabla movhos_00143) con estado activo. Jonatan Lopez											 | 																			 |
// Septiembre 7 de 2012	(Camilo Zapata)	Se agrega una sentencia drop de la tabla temporal cuando exista, y se corrigió en la linea	226 el   |
//										script para que haga la consulta solo cuando No hay valor del ingreso, es decir, cuando se consulta  |
//										el ingreso activo.																					 |														 |
// Agosto 1 de 2012		(Edwin MG)	Se corrige query para cuando el paciente esta muerto, ya que no se mostraba informacion para dicho caso	 |
// Noviembre 8 de 2011	(Edwin MG)	Se agrega caso no considerado al calcular devoluciones, hay ocasiones en que en central de mezclas cargan|
//									medicamentos sin necesidad de tener lote, normalmente MMQ, este queda sin lote en detalle de cargos		 |
//									(movhos_000003)																							 |
//===========================================================================================================================================|

session_start();
if (!isset($_SESSION['user']))
{
	echo "error";
}
else
{

	

    

    include_once("root/comun.php");

    $wactualiz = "2014-05-20";

    encabezado("CONSULTA DE INSUMOS GRABADOS Y APLICADOS X PACIENTE",$wactualiz, "clinica");

	if( !isset($pacact) )
		$pacact = "";
	if ((!isset($whab) or $whab == '') and (!isset($whis) or $whis == '') and (!isset($wced) or $wced == '') )
	{
		echo "<form name='facvsapl' action='' method=post>";
		// ///////////////////////////////////////////////////////////////////////////////////// seleccion para saber la Base de Datos
		$query=" SELECT Empcod,Empdes,Emptcc "
		."   FROM root_000050"
		."  WHERE Empest = 'on'"
		."    AND Empcod = '".$wemp."'"
		."  ORDER BY Empcod,Empdes,Emptcc";
		$err = mysql_query($query, $conex);
		$num = mysql_num_rows($err);

		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($err);

			$tablacc = $row[2];
			$codemp = $row[0];
		}


		if( $pacact == "a" ){
			$varina = "No. Identificación";
			$varcampo="<INPUT TYPE='text' NAME='wced' SIZE=10>";
		}
		else{
			$varina = "Habitacion";
			$varcampo="<INPUT TYPE='text' NAME='whab' SIZE=10>";
		}
		echo "<br><br><table name='inicio' align=center>
	    		<tr class=encabezadotabla>
	    			<td>$varina
	    			<td>Historia
	    		<tr class=fila1>
	    			<td align= center>$varcampo
	    			<td><INPUT TYPE='text' NAME='whis' SIZE=10>
	    	</table>";

		echo '<table align=center>';

        ?>
	      <script>
	      function ira(){document.facvsapl.whab.focus();}
	      </script>
	    <?php

	    if( $pacact != "a" )
        	echo "<tr><td><input type='radio' name='pacact' checked=checked onClick='javascript:enter()'>Activo</td>";
        else
        	echo "<tr><td><input type='radio' name='pacact' onClick='javascript:enter()'>Activo</td>";

        if( $pacact == "a" )
        	echo "<td><input type='radio' value='a' checked=checked name='pacact' onClick='javascript:enter()'>Inactivo</td>";
        else
        	echo "<td><input type='radio' value='a' name='pacact' onClick='javascript:enter()'>Inactivo</td>";
        echo "</table>";

	    echo "<br>";

	    echo "<center><table align=center>";
	    echo "<tr><td><input type='submit' value='generar'> | <input type='button' value='Cerrar' onClick='cerrarVentana()'></td></tr>";
	    echo "</table>";
//	    echo $wced;
	}
	else // Cuando ya estan todos los datos escogidos
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION

		$q = " SELECT Detapl,Detval"
		    ."   FROM root_000051"
		    ."  WHERE Detemp = '".$wemp."'";
		$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($err);

		$empre1 = "";
		$empre2 = "";

		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($err);

			if ($row[0] == 'cenmez')
			{
				$wcenmez = $row[1];
			}
			else
			{
				if ($row[0] == 'movhos')
				{
					$wbasedato = $row[1];
				}
			}
		}

		//Si es inactivo
		if($pacact == "a" && (empty($whis) == false or empty($wced) == false )){
			$pacact = 'on';

			if( !isset($whis) || empty($whis) == true )
				$whis = "%";

			if( !isset($wced) || empty($wced) == true )
				$wced = "%";
			else
				$wced = "$wced%";

			//se consultan los ingresos inactivos.
			$q = " SELECT orihis, oriced, ubiing, pacno1, pacno2, pacap1, pacap2, ubifad "
		         ."   FROM root_000037, root_000036, ".$wbasedato."_000018 "
		         ."  WHERE oriori = '".$wemp."'"
		         ."    AND orihis like '".$whis."'"
		         ."    AND oriced = pacced "
		         ."    AND ubihis = orihis "
		         ."    AND oriced like '$wced' "
		         ."    AND ubiald = 'on'"
		         ."  ORDER BY ubiing";
		      //echo "------------".$q;
		    $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($err);

			echo "<table align=center>";
			echo "<tr class=encabezadotabla><td>Ingreso
				 </td><td>No. Identificación
				 </td><td>Nombres y Apellidos
				 </td><td>Historia
				 </td><td>Enlace
				 </td></tr>";

			if( $num > 0 ){
				for( $i = 0; $i < $num; $i++ ){
					$rows = mysql_fetch_array($err);
					echo "<tr class=fila".(($i%2)+1)."><td>{$rows['ubiing']}</td>
					<td>{$rows['oriced']}
					</td><td>{$rows['pacno1']} {$rows['pacno2']} {$rows['pacap1']} {$rows['pacap2']}
					</td><td>{$rows['orihis']}
					</td><td align=center><a href='rep_facturado_vs_aplicado.php?wemp=$wemp&whis={$rows['orihis']}&whab=&wing={$rows['ubiing']}'>Ver</a>
					</td></tr>";
				}

				echo "<form action='rep_facturado_vs_aplicado.php?wemp=".$wemp."' method=post>
				      <table align=center><tr><td><br><tr><td><td align=center colspan=9><input type=submit value='Retornar'>
				      </table>
				      </form>";
			}
			else{
				echo "<p align=center>No se produjo ningun resultado</p>";
				echo "<form action='rep_facturado_vs_aplicado.php?wemp=".$wemp."' method=post>
				      <table align=center><tr><td align=center colspan=9><input type=submit value='Retornar'>
				      </table>
				      </form>";
			}
		}
		else{


		if ((!isset($whab) or $whab=="") and isset($whis))         //No se digito la habitacion
		{
			if(!isset($wing) or $wing=='')
			{
				$q = " SELECT habcod, habhis, habing "
					."   FROM ".$wbasedato."_000020 "
					."  WHERE habhis = '".$whis."'"
					."    AND habest = 'on' "
					." UNION "	//Agrego este union por si el paciente esta muerto. Julio 31 de 2012
					." SELECT ubihan as habcod, ubihis as habhis, ubiing as habing "
					."   FROM ".$wbasedato."_000018 "
					."  WHERE ubimue  = 'on' "
					."    AND ubiald != 'on' "
					."    AND ubihis  = '".$whis."' "
					;
					//echo "----query: ".$q;
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$wnum = mysql_num_rows($res);
				if ($wnum > 0)
				{
					$row = mysql_fetch_array($res);

					$whab=$row[0];
					$whis=$row[1];
					$wing=$row[2];
				}
			}
		}

		if (isset($whab) and (!isset($whis) or $whis==""))         //No se digito la habitacion
		{
			$q = " SELECT habcod, habhis, habing "
			."   FROM ".$wbasedato."_000020 "
			."  WHERE habcod = '".$whab."'"
			."    AND habest = 'on' ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$wnum = mysql_num_rows($res);
			if ($wnum > 0)
			{
				$row = mysql_fetch_array($res);

				$whab=$row[0];
				$whis=$row[1];
				$wing=$row[2];
			}
		}

		if (isset($whab) and isset($whis))         //No se digito la habitacion
		{
			$q = " SELECT habcod, habhis, habing "
			    ."   FROM ".$wbasedato."_000020 "
			    ."  WHERE habcod = '".$whab."'"
			    ."    AND habhis = '".$whis."'"
			    ."    AND habest = 'on' ";
			//echo "<br>----".$q;
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$wnum = mysql_num_rows($res);
			if ($wnum > 0)
			{
				$row = mysql_fetch_array($res);

				$whab=$row[0];
				$whis=$row[1];
				$wing=$row[2];
			}
		}
		$query = "SELECT Detval
		 			FROM root_000051
		 		   WHERE Detemp = '{$wemp}'
		 		     AND Detapl = 'sufijoTablaRespaldoMovhos00003'";
		 $rsfijo = mysql_query( $query, $conex ) or die( mysql_error());
		 $rowSuf = mysql_fetch_array( $rsfijo );
		 $sufijoRespaldo = $rowSuf[0];
		//se consultan los cargos
		if (isset($whis) and isset($whab) and isset($wing))
		{
			$qaux = "DROP TABLE IF EXISTS tempo2";
			$rs = mysql_query($qaux, $conex);
			$q = " CREATE TEMPORARY TABLE if not exists tempo2 as "
				." SELECT fdeart, sum(fdecan) as fdecan "
				."   FROM ".$wbasedato."_000002,".$wbasedato."_000003, ".$wbasedato."_000011  "
				."  WHERE Fenhis = '".$whis."'"
				."    AND Fening = '".$wing."'"
				."    AND fennum = fdenum "
				."    AND fenest = 'on' "
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND ccoima = 'off' "
				."  GROUP BY 1 "
				."  UNION ALL "
				." SELECT fdeari as fdeart, count(distinct(fdenum)) as fdecan "
				."   FROM ".$wbasedato."_000002,".$wbasedato."_000003,  ".$wbasedato."_000011 "
				."  WHERE Fenhis = '".$whis."'"
				."    AND Fening = '".$wing."'"
				."    AND fennum = fdenum "
				."    AND fenest = 'on' "
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND ccoima = 'on' "
				."    AND fdelot <> ''  "
				."  GROUP BY 1  "
				."  UNION ALL "
				." SELECT fdeart, count(distinct(fdenum)) as fdecan "
				."   FROM ".$wbasedato."_000002,".$wbasedato."_000003,  ".$wbasedato."_000011 "
				."  WHERE Fenhis = '".$whis."'"
				."    AND Fening = '".$wing."'"
				."    AND fennum = fdenum "
				."    AND fenest = 'on' "
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND ccoima = 'on' "
				."    AND fdelot = ''  "
				."  GROUP BY 1  "

				/**             UNION CON TABLA DE RESPALDO                          **/
				// ." UNION "
				// ." SELECT fdeart, sum(fdecan) as fdecan "
				// ."   FROM ".$wbasedato."_000002,".$wbasedato."_000003{$sufijoRespaldo}, ".$wbasedato."_000011  "
				// ."  WHERE Fenhis = '".$whis."'"
				// ."    AND Fening = '".$wing."'"
				// ."    AND fennum = fdenum "
				// ."    AND fenest = 'on' "
				// ."    AND fentip in ('CA','CP','AP') "
				// ."    AND fencco = ccocod "
				// ."    AND ccoima = 'off' "
				// ."  GROUP BY 1 "
				// ."  UNION ALL "
				// ." SELECT fdeari as fdeart, count(distinct(fdenum)) as fdecan "
				// ."   FROM ".$wbasedato."_000002,".$wbasedato."_000003{$sufijoRespaldo},  ".$wbasedato."_000011 "
				// ."  WHERE Fenhis = '".$whis."'"
				// ."    AND Fening = '".$wing."'"
				// ."    AND fennum = fdenum "
				// ."    AND fenest = 'on' "
				// ."    AND fentip in ('CA','CP','AP') "
				// ."    AND fencco = ccocod "
				// ."    AND ccoima = 'on' "
				// ."    AND fdelot <> ''  "
				// ."  GROUP BY 1  "
				// ."  UNION ALL "
				// ." SELECT fdeart, count(distinct(fdenum)) as fdecan "
				// ."   FROM ".$wbasedato."_000002,".$wbasedato."_000003{$sufijoRespaldo},  ".$wbasedato."_000011 "
				// ."  WHERE Fenhis = '".$whis."'"
				// ."    AND Fening = '".$wing."'"
				// ."    AND fennum = fdenum "
				// ."    AND fenest = 'on' "
				// ."    AND fentip in ('CA','CP','AP') "
				// ."    AND fencco = ccocod "
				// ."    AND ccoima = 'on' "
				// ."    AND fdelot = ''  "
				// ."  GROUP BY 1  "
				."  UNION " //Se agrega este union para que tenga en cuanta la tabla backup de la medicamentos.
				." SELECT fdeart, sum(fdecan) as fdecan "
				."   FROM ".$wbasedato."_000002,".$wbasedato."_000143, ".$wbasedato."_000011  "
				."  WHERE Fenhis = '".$whis."'"
				."    AND Fening = '".$wing."'"
				."    AND fennum = fdenum "
				."    AND fenest = 'on' "
				."    AND fdeest = 'on' " //Estado en la tabla de contingencia.
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND ccoima = 'off' "
				."  GROUP BY 1 "
				."  UNION ALL "
				." SELECT fdeari as fdeart, count(distinct(fdenum)) as fdecan "
				."   FROM ".$wbasedato."_000002,".$wbasedato."_000143,  ".$wbasedato."_000011 "
				."  WHERE Fenhis = '".$whis."'"
				."    AND Fening = '".$wing."'"
				."    AND fennum = fdenum "
				."    AND fenest = 'on' "
				."    AND fdeest = 'on' " //Estado en la tabla de contingencia.
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND ccoima = 'on' "
				."    AND fdelot <> ''  "
				."  GROUP BY 1  "
				."  UNION ALL "
				." SELECT fdeart, count(distinct(fdenum)) as fdecan "
				."   FROM ".$wbasedato."_000002,".$wbasedato."_000143,  ".$wbasedato."_000011 "
				."  WHERE Fenhis = '".$whis."'"
				."    AND Fening = '".$wing."'"
				."    AND fennum = fdenum "
				."    AND fenest = 'on' "
				."    AND fdeest = 'on' " //Estado en la tabla de contingencia.
				."    AND fentip in ('CA','CP','AP') "
				."    AND fencco = ccocod "
				."    AND ccoima = 'on' "
				."    AND fdelot = ''  "
				."  GROUP BY 1  ";
				//echo "<br>---".$q;
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


			$q = " SELECT fdeart, sum(fdecan) "
				."   FROM tempo2 "
				."  Group by 1 ";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$wnum = mysql_num_rows($res);

			echo '<table align=center>';


			//TRAIGO EL NOMBRE DEL PACIENTE
			//==============================================================================
			$q = " SELECT pacno1, pacno2, pacap1, pacap2, orihis, oriing "
				."   FROM root_000036, root_000037 "
				."  WHERE oriori = '".$wemp."'"
				."    AND orihis = '".$whis."'"
//				."    AND oriing = '".$wing."'"
				."    AND oriced = pacced ";

			$respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$rowpac = mysql_fetch_array($respac);

			$wpac = $rowpac[0]." ".$rowpac[1]." ".$rowpac[2]." ".$rowpac[3];

			echo "<br><br><tr class=seccion1>";
			echo "<th align=center colspan=1><b>Habitacion: </b>".$whab."</font></th>";
			echo "<th align=center colspan=2><b>Historia: </b>".$whis." - ".$wing."</font></th>";
			echo "<th align=center colspan=2><b>Paciente: </b>".$wpac."</font></th>";
			echo "</tr>";
			echo "</table>";


			echo "<center><table>";

			echo "<tr class=encabezadoTabla>";
			echo "<th align=center colspan=2><b>ARTICULO</b></th>";
			echo "<th align=center colspan=1><b>CANTIDAD <BR> GRABADA</b></th>";
			echo "<th align=center colspan=1><b>CANTIDAD <BR> APLICADA +<BR> DEVUELTA +<BR> DESCARTADA</b></th>";
			echo "<th align=center colspan=2><b>SALDO</b></th>";
			echo "</tr>";

			for ($i=1;$i<=$wnum;$i++)
			{
				if ($i % 2 == 0)
				   $wclass = "fila1";
				  else
				    $wclass = "fila2";

				$row = mysql_fetch_array($res);


				//ACA TRAIGO EL NOMBRE DEL ARTICULO, BUSCO 1RO EN MOVHOS Y LUEGO EN CENMEZ
				//==============================================================================
				$q = " SELECT artcom "
					."   FROM ".$wbasedato."_000026 "
					."  WHERE artcod = '".$row[0]."'";
				$resart = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$wnumart = mysql_num_rows($resart);
				if ($wnumart > 0)
				{
					$rowart = mysql_fetch_array($resart);
					$wnomart = $rowart[0];
				}
				else
				{
					$q = " SELECT artcom "
						."   FROM ".$wcenmez."_000002 "
						."  WHERE artcod = '".$row[0]."'";
					$resart = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$wnumart = mysql_num_rows($resart);
					if ($wnumart > 0)
					{
						$rowart = mysql_fetch_array($resart);
						$wnomart=$rowart[0];
					}
				}
				//================================================================================================================

				echo "<tr class=".$wclass.">";
				echo "<td align=center>".$row[0]."</td>";                                        //Codigo
				echo "<td align=left  >".$wnomart."</td>";                                       //Descripcion

				$wtotfac=$row[1];
				//===============================================================================================================

				echo "<td align=right>".number_format($wtotfac,2,'.',',')."</td>";              //Cantidad facturada

				//===============================================================================================================
				//Aca traigo la cantidad aplicada, devuelta y descartada
				//===============================================================================================================

				$q = " CREATE TEMPORARY TABLE if not exists tempo1 as "          //APLICACIONES
					." SELECT SUM(aplcan) AS can, '15' as 'tabla_origen' "
					."   FROM ".$wbasedato."_000015 "
					."  WHERE aplhis = '".$whis."'"
					."    AND apling = '".$wing."'"
					."    AND aplart = '".$row[0]."'"
					."    AND aplest = 'on' "
					."  UNION ALL "
					." SELECT SUM(descan) AS can, '35 y 31' as 'tabla_origen'"                               //DESCARTES
					."   FROM ".$wbasedato."_000035, ".$wbasedato."_000031 "
					."  WHERE denhis = '".$whis."'"
					."    AND dening = '".$wing."'"
					."    AND dencon = descon "
					."    AND desart = '".$row[0]."'"
					."  UNION ALL "
					." SELECT sum(fdecan), '000003' as 'tabla_origen'  "                                      //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
					."   FROM ".$wbasedato."_000002,".$wbasedato."_000003, ".$wbasedato."_000011  "
					."  WHERE fenhis = ".$whis
					."    AND fening = ".$wing
					."    AND fennum = fdenum "
					."    AND fenest = 'on' "
					."    AND fdeart = '".$row[0]."'"
					."    AND fentip in ('DA','DP') "
					."    AND fencco = ccocod "
					."    AND ccoima = 'off' "
					//."  GROUP BY fdenum "
					."  UNION ALL "
					." SELECT COUNT(DISTINCT(fdenum)), '000003' as 'tabla_origen' "                           //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
					."   FROM ".$wbasedato."_000002,".$wbasedato."_000003, ".$wbasedato."_000011  "
					."  WHERE fenhis = ".$whis
					."    AND fening = ".$wing
					."    AND fennum = fdenum "
					."    AND fenest = 'on' "
					."    AND fdeari = '".$row[0]."'"
					."    AND fentip in ('DA','DP') "
					."    AND fencco = ccocod "
					."    AND ccoima = 'on' "
					/******************************************************************
					 * Noviembre 8 de 2011
					 ******************************************************************/
					."  UNION ALL "
					." SELECT COUNT(DISTINCT(fdenum)), '000003' as 'tabla_origen' "                           //DEVOLUCIONES DE CENTRAL DE MEZCLAS Y DE ENFERMERIA SIN LOTE
					."   FROM ".$wbasedato."_000002,".$wbasedato."_000003, ".$wbasedato."_000011, ".$wcenmez."_000002  "
					."  WHERE fenhis = ".$whis
					."    AND fening = ".$wing
					."    AND fennum = fdenum "
					."    AND fenest = 'on' "
					."    AND fdeart = '".$row[0]."'"
					."    AND fdeari = artcod "
					."    AND fentip in ('DA','DP') "
					."    AND fencco = ccocod "
					."    AND ccoima = 'on' "
					."    AND fdelot = '' "

					/******************************************************************
					* CONSULTA EN LA TABLA DE RESPALDO
					*******************************************************************/
					// ."  UNION ALL "
					// ." SELECT sum(fdecan), '000003' as 'tabla_origen'  "                                      //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
					// ."   FROM ".$wbasedato."_000002,".$wbasedato."_000003{$sufijoRespaldo}, ".$wbasedato."_000011  "
					// ."  WHERE fenhis = ".$whis
					// ."    AND fening = ".$wing
					// ."    AND fennum = fdenum "
					// ."    AND fenest = 'on' "
					// ."    AND fdeart = '".$row[0]."'"
					// ."    AND fentip in ('DA','DP') "
					// ."    AND fencco = ccocod "
					// ."    AND ccoima = 'off' "
					// //."  GROUP BY fdenum "
					// ."  UNION ALL "
					// ." SELECT COUNT(DISTINCT(fdenum)), '000003' as 'tabla_origen' "                           //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
					// ."   FROM ".$wbasedato."_000002,".$wbasedato."_000003{$sufijoRespaldo}, ".$wbasedato."_000011  "
					// ."  WHERE fenhis = ".$whis
					// ."    AND fening = ".$wing
					// ."    AND fennum = fdenum "
					// ."    AND fenest = 'on' "
					// ."    AND fdeari = '".$row[0]."'"
					// ."    AND fentip in ('DA','DP') "
					// ."    AND fencco = ccocod "
					// ."    AND ccoima = 'on' "
					// /******************************************************************
					 // * Noviembre 8 de 2011 con la tabla de respaldo
					 // ******************************************************************/
					// ."  UNION ALL "
					// ." SELECT COUNT(DISTINCT(fdenum)), '000003' as 'tabla_origen' "                           //DEVOLUCIONES DE CENTRAL DE MEZCLAS Y DE ENFERMERIA SIN LOTE
					// ."   FROM ".$wbasedato."_000002,".$wbasedato."_000003{$sufijoRespaldo}, ".$wbasedato."_000011, ".$wcenmez."_000002  "
					// ."  WHERE fenhis = ".$whis
					// ."    AND fening = ".$wing
					// ."    AND fennum = fdenum "
					// ."    AND fenest = 'on' "
					// ."    AND fdeart = '".$row[0]."'"
					// ."    AND fdeari = artcod "
					// ."    AND fentip in ('DA','DP') "
					// ."    AND fencco = ccocod "
					// ."    AND ccoima = 'on' "
					// ."    AND fdelot = '' "
					/*********************************************************************************************************************/
					/* Noviembre 05 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
					/*********************************************************************************************************************/
					."  UNION ALL"
					." SELECT sum(fdecan), '000143' as 'tabla_origen' "                                      //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
					."   FROM ".$wbasedato."_000002,".$wbasedato."_000143, ".$wbasedato."_000011  "
					."  WHERE fenhis = ".$whis
					."    AND fening = ".$wing
					."    AND fennum = fdenum "
					."    AND fenest = 'on' "
					."    AND fdeart = '".$row[0]."'"
					."    AND fentip in ('DA','DP') "
					."    AND fencco = ccocod "
					."    AND ccoima = 'off' "
					."    AND fdeest = 'on'"
					."  UNION ALL "
					." SELECT COUNT(DISTINCT(fdenum)), '000143' as 'tabla_origen' "                           //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
					."   FROM ".$wbasedato."_000002,".$wbasedato."_000143, ".$wbasedato."_000011  "
					."  WHERE fenhis = ".$whis
					."    AND fening = ".$wing
					."    AND fennum = fdenum "
					."    AND fenest = 'on' "
					."    AND fdeari = '".$row[0]."'"
					."    AND fentip in ('DA','DP') "
					."    AND fencco = ccocod "
					."    AND ccoima = 'on' "
					."    AND fdeest = 'on'"
					."  UNION ALL "
					." SELECT COUNT(DISTINCT(fdenum)), '000143' as 'tabla_origen' "                           //DEVOLUCIONES DE CENTRAL DE MEZCLAS Y DE ENFERMERIA SIN LOTE
					."   FROM ".$wbasedato."_000002,".$wbasedato."_000143, ".$wbasedato."_000011, ".$wcenmez."_000002  "
					."  WHERE fenhis = ".$whis
					."    AND fening = ".$wing
					."    AND fennum = fdenum "
					."    AND fenest = 'on' "
					."    AND fdeart = '".$row[0]."'"
					."    AND fdeari = artcod "
					."    AND fentip in ('DA','DP') "
					."    AND fencco = ccocod "
					."    AND ccoima = 'on' "
					."    AND fdelot = '' "
					."    AND fdeest = 'on'";
				//echo "-----segundo query: <br> ".$q;
				$resapl = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				$q = " SELECT SUM(can) "
					."   FROM tempo1 "
					."  WHERE can != '' ";
				$resapl = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$wnumapl = mysql_num_rows($resapl);

				if ($wnumapl > 0)
				{
					$rowapl = mysql_fetch_array($resapl);

//					if ($rowapl[0] > 0)
					   echo "<td align=right>".number_format($rowapl[0],2,'.',',')."</td>";        //Cantidad Aplicada
//					  else
//					     echo "<td align=right>&nbsp</td>";                                        //Cantidad Aplicada
				}
				else
				echo "<td align=right>&nbsp</td>";                                            						//Cantidad Aplicada
				//===============================================================================================================

				echo "<td align=right>".number_format(($wtotfac-$rowapl[0]),2,'.',',')."</td>";      //Saldo
				echo "<td align=center><b><A href='rep_facturado_vs_aplicado_detalle.php?wemp=".$wemp."&whis=".$whis."&wing=".$wing."&whab=".$whab."&wart=".$row[0]."&wdet=on&wpac=".$wpac."' target='_blank'> Detallar</A></b></font></td>";

				$q = " DROP TABLE tempo1 ";
				$resapl = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			} // fin del for
		}
		else                                      //No se digito la historia
		{
			echo "<font size=4><b>La Habitación o La Historía digitada no estan en el sistema</b></font><br><br>";
		}



		echo "<tr>";
		echo "<th colspan=5>&nbsp</th>";
		echo "</tr>";
		echo "<tr><td align=center colspan=8><A href='rep_facturado_vs_aplicado.php?wemp=".$wemp."'> Retornar</A></font></td></tr>";

	} // cierre del else donde empieza la impresión

	echo "</table>"; // cierra la tabla o cuadricula de la impresión

//	echo "<br>";
	echo "<form action='rep_facturado_vs_aplicado.php?wemp=".$wemp."'><table align=center>";
	echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	echo "</table></form>";
	}
}
?>