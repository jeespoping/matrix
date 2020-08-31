<html>
<head>
   <title>Reporte Grabado Vs Aplicado</title>
</head>

<BODY>

<script type="text/javascript">
function enter()
{
	document.forms.rep_facvsapl.submit();
}

function cerrarVentana()
{
	window.close()
}

function ejecutar(path)
{
	window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
}
</script>


<?php
include_once("conex.php");

/**
* REPORTE DE ARTICULOS GRABADOS VS APLICADOS X PACIENTE	                                                   *
*/
// ===========================================================================================================================================
// PROGRAMA				      :Reporte para saber los articulos grabados y aplicados por paciente.                                         |
// AUTOR				      :Juan C. Hernandez M.                                                                                          |
// FECHA CREACION			  :Febrero 5 DE 2005.                                                                                            |
// FECHA ULTIMA ACTUALIZACION :Noviembre 30 de 2007.                                                                                         |
// DESCRIPCION			      :Reporte para saber lo grabado a un paciente vs lo aplicada, devuelto o descartado y conocer el saldo        |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// root_000050       : Tabla de Empresas para escojer empresa y esta traer un campo para saber que centros de costos escojer.                |
// costosyp_000005   : Tabla de Centros de costos de Clinica las Americas, Laboratorio de Patologia, y Laboratorio Medico.                   |
// ==========================================================================================================================================|
// Modificaciones:																															 |
//							
// Noviembre 8 de 2011	(Edwin MG)	Se agrega caso no considerado al calcular devoluciones, hay ocasiones en que en central de mezclas cargan|
//									medicamentos sin necesidad de tener lote, normalmente MMQ, este queda sin lote en detalle de cargos		 |
//									(movhos_000003)																							 |
//
// Marzo 29 de 2012 (Viviana Rodas) Se cambia para las aplicaciones en la tabla 000015 ya no van al campo Fecha_data sino al campo Aplfec
// Abril 23 de 2012 (Viviana Rodas) Se agrega un link para que retorne a la pagina rep_facturado_vs_aplicado.php a la lista de insumos facturados
//                                  y aplicados por paciente
// Abril 24 de 2012 (Viviana Rodas) Se quita el link de retornar ya que solo funciona en google chrome
// Noviembre 7 de 2013: (Jonatan Lopez) Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 para que traiga los     |
//										datos de contingencia (tabla movhos_00143) con estado activo. Jonatan Lopez	
// Enero 30 de 2020	(Jessica Madrid)	- Se agrega enlace a la impresión del rótulo de las nutriciones parenterales solo para los artículos |
// 										  que sean de este tipo.																			 |
// ==========================================================================================================================================|

$wactualiz = "2020-01-30";

session_start();
if (!isset($_SESSION['user']))
{
	echo "error";
}
else
{

	

    

    include_once("root/comun.php");

  

	encabezado("CONSULTA DE INSUMOS GRABADOS Y APLICADOS X PACIENTE",$wactualiz, "clinica");

	echo "<form name='facvsapldet' action='rep_facturado_vs_aplicado_detalle.php' method=post>";

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

	//=============================================================================================================
	function esNPT($conex, $wcenmez, $wcodart, &$insitucion)
	{
		$query = "SELECT Artins 
					FROM ".$wcenmez."_000002 
				   WHERE Artcod='".$wcodart."' 
				     AND Arttip='02';";
					 
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$esNPT = false;
		if($num>0)
		{
			$esNPT = true;
			$row = mysql_fetch_array($res);
			$insitucion = $row['Artins'];
		}			
		
		return $esNPT;
	}
	
	function NPTorigenOrdenes($conex, $wbasedato, $wcodart)
	{
		$query = "SELECT Enucnu 
					FROM ".$wbasedato."_000214 
				   WHERE Enucnu='".$wcodart."' 
				     AND Enuord='on';";
					 
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$origenOrdenes = false;
		if($num>0)
		{
			$origenOrdenes = true;
		}			
		
		return $origenOrdenes;
	}
	
	function recorrido_historia($fhis, $fing)
	{
		global $wbasedato;
		global $conex;


		$q = " SELECT fecha_data, hora_data, eyrsor, eyrsde, eyrhor, eyrhde, eyrtip "
			."   FROM ".$wbasedato."_000017 "
			."  WHERE eyrhis = '".$fhis."'"
			."    AND eyring = '".$fing."'"
			."    AND eyrest = 'on' "
			."  ORDER BY 1, 2, 3 ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$wnum = mysql_num_rows($res);

		if ($wnum > 0)
		{
			echo "<br>";
			echo "<center><table>";

			echo "<tr class=seccion1>";
			echo "<th colspan=9><font size=4><b>MOVIMIENTOS HOSPITALARIOS QUE HA TENIDO EL PACIENTE</b></font></th>";
			echo "</tr>";

			echo "<tr class=encabezadoTabla>";
			echo "<th colspan=4>Servicio Origen</th>";
			echo "<th colspan=4>Servicio Destino</th>";
			echo "<th colspan=1 rowspan=2>Evento</th>";

			echo "<tr class=encabezadoTabla>";
			echo "<th>C.Costo</th>";
			echo "<th>Habitacion</th>";
			echo "<th>Fecha</th>";
			echo "<th>Hora</th>";
			echo "<th>C.Costo</th>";
			echo "<th>Habitacion</th>";
			echo "<th>Fecha</th>";
			echo "<th>Hora</th>";
			echo "</tr>";

			for ($i=1;$i<=$wnum;$i++)
			{
				if ($i % 2 == 0)
				   $wclass = "fila1";
				  else
				     $wclass = "fila2";

				echo "<tr class=".$wclass.">";
				$row = mysql_fetch_array($res);

				echo "<td>".$row[2]."</td>";
				echo "<td>".$row[4]."</td>";
				echo "<td>".$row[0]."</td>";
				echo "<td>".$row[1]."</td>";
				echo "<td>".$row[3]."</td>";
				echo "<td>".$row[5]."</td>";
				echo "<td>".$row[0]."</td>";
				echo "<td>".$row[1]."</td>";
				echo "<td>".$row[6]."</td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<br>";
		}
	}
	//=============================================================================================================


	//=============================================================================================================
	//Aca traigo los registros GRABADOS del articulo si es de dispensacion
	$q = " SELECT fencco, fenfec, fdeart, artcom, SUM(fdecan) "
		."   FROM ".$wbasedato."_000002,".$wbasedato."_000003, ".$wbasedato."_000011, ".$wbasedato."_000026 " //, ".$wbasedato."_000004
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fdeart = '".$wart."'"
		."    AND fenest = 'on' "
		."    AND fentip in ('CA','CP','AP') "
		."    AND fencco = ccocod "
		."    AND ccoima = 'off' "
		."    AND fdeart = artcod "
		."  GROUP BY 1, 2, 3, 4 "
		/*********************************************************************************************************************/
		/* Noviembre 05 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/
		."  UNION " 
		." SELECT fencco, fenfec, fdeart, artcom, SUM(fdecan) "
		."   FROM ".$wbasedato."_000002,".$wbasedato."_000143, ".$wbasedato."_000011, ".$wbasedato."_000026 " //, ".$wbasedato."_000004
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fdeart = '".$wart."'"
		."    AND fenest = 'on' "
		."    AND fentip in ('CA','CP','AP') "
		."    AND fencco = ccocod "
		."    AND ccoima = 'off' "
		."    AND fdeart = artcod "
		."    AND fdeest = 'on'"
		."  GROUP BY 1, 2, 3, 4 ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$wnumfac = mysql_num_rows($res);

	if ($wnumfac > 0)
	{
		for ($i=1;$i<=$wnumfac;$i++)
		{
			$row = mysql_fetch_array($res);
			$warr[$i][1]=$row[0];           //Cco que facturo
			$warr[$i][2]=$row[1];           //Fecha facturacion
			$warr[$i][3]=$row[4];           //Cantidad

			$wcodart=$row[2];               //Codigo Articulo
			$wnomart=$row[3];               //Nombre Articulo
		}
	}
	else 
	{
		$i=1;
	}

	//Aca traigo los registros GRABADOS del articulo si es de Central de Mezclas
	$q = " SELECT fencco, fenfec, fdeari, artcom, COUNT(DISTINCT(fdenum)) "
		."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011, ".$wcenmez."_000002 " //, ".$wbasedato."_000004
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fdeari = '".$wart."'"
		."    AND fenest = 'on' "
		."    AND fentip in ('CA','CP','AP') "
		."    AND ccoima = 'on' "
		."    AND fdeari = artcod "
		."    AND fencco = ccocod "
		."    AND fdelot  <> '' "
		."  GROUP BY 1, 2, 3, 4 "
		/*********************************************************************************************************************/
		/* Noviembre 05 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/
		."  UNION "
		." SELECT fencco, fenfec, fdeari, artcom, COUNT(DISTINCT(fdenum)) "
		."   FROM ".$wbasedato."_000002, ".$wbasedato."_000143, ".$wbasedato."_000011, ".$wcenmez."_000002 " //, ".$wbasedato."_000004
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fdeari = '".$wart."'"
		."    AND fenest = 'on' "
		."    AND fentip in ('CA','CP','AP') "
		."    AND ccoima = 'on' "
		."    AND fdeari = artcod "
		."    AND fencco = ccocod "
		."    AND fdelot  <> '' "
		."    AND fdeest = 'on'"
		."  GROUP BY 1, 2, 3, 4 ";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$wnumfac2 = mysql_num_rows($res);

	if ($wnumfac2 > 0)
	{
		for ($j=$i;$j<=($wnumfac2+$wnumfac);$j++)
		{
			$row = mysql_fetch_array($res);
			$warr[$j][1]=$row[0];           //Cco que facturo
			$warr[$j][2]=$row[1];           //Fecha facturacion
			$warr[$j][3]=$row[4];           //Cantidad

			$wcodart=$row[2];               //Codigo Articulo
			$wnomart=$row[3];               //Nombre Articulo
		}
	}
	else
	{
		$j=$i;
	}
	$wnumfac=$wnumfac+$wnumfac2;

	//Aca traigo los registros GRABADOS del articulo si es de Central de Mezclas
	$q = " SELECT fencco, fenfec, fdeart, artcom, COUNT(DISTINCT(fdenum)) "
		."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011, ".$wcenmez."_000002 " //, ".$wbasedato."_000004
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fdeart = '".$wart."'"
		."    AND fenest = 'on' "
		."    AND fentip in ('CA','CP','AP') "
		."    AND ccoima = 'on' "
		."    AND fdeari = artcod "
		."    AND fencco = ccocod "
		."    AND fdelot              ='' "
		."  GROUP BY 1, 2, 3, 4 "
		/*********************************************************************************************************************/
		/* Noviembre 05 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/
		."  UNION"
		." SELECT fencco, fenfec, fdeart, artcom, COUNT(DISTINCT(fdenum)) "
		."   FROM ".$wbasedato."_000002, ".$wbasedato."_000143, ".$wbasedato."_000011, ".$wcenmez."_000002 " //, ".$wbasedato."_000004
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fdeart = '".$wart."'"
		."    AND fenest = 'on' "
		."    AND fentip in ('CA','CP','AP') "
		."    AND ccoima = 'on' "
		."    AND fdeari = artcod "
		."    AND fencco = ccocod "
		."    AND fdelot ='' "
		."    AND fdeest = 'on'"
		."  GROUP BY 1, 2, 3, 4 ";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$wnumfac3 = mysql_num_rows($res);

	if ($wnumfac3 > 0)
	{
		for ($k=$j;$k<=($wnumfac3+$wnumfac);$k++)
		{
			$row = mysql_fetch_array($res);
			$warr[$k][1]=$row[0];           //Cco que facturo
			$warr[$k][2]=$row[1];           //Fecha facturacion
			$warr[$k][3]=$row[4];           //Cantidad

			$wcodart=$row[2];               //Codigo Articulo
			$wnomart=$row[3];               //Nombre Articulo
		}
	}
	$wnumfac=$wnumfac+$wnumfac3;

	
	//=============================================================================================================
	//Aca traigo los registros APLICADOS del articulo
	$q = " SELECT aplcco, Aplfec, SUM(aplcan) "
		."   FROM ".$wbasedato."_000015 "
		."  WHERE aplhis = '".$whis."'"
		."    AND apling = '".$wing."'"
		."    AND aplart = '".$wart."'"
		."    AND aplest = 'on' "
		."  GROUP BY 1, 2 "
		."  ORDER BY 1, 2, 3 ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$wnum = mysql_num_rows($res);

	if ($wnum > 0)
	{
		for ($i=1;$i<=$wnum;$i++)
		{
			$row = mysql_fetch_array($res);

			$warr[$i][4]=$row[0];           //Cco que aplico
			$warr[$i][5]=$row[1];           //Fecha de Aplicacion
			$warr[$i][6]=$row[2];           //Cantidad aplicada
		}
	}
	//=============================================================================================================


	//Si la cantidad de registros grabados es mayor a los aplicados tomo como tope de registros a imprimir la variable
	//$numfac, si no tomo la variable $wnum
	if ($wnumfac>=$wnum)
	   $wnumarr=$wnumfac;
	  else
	    $wnumarr=$wnum;

	//=============================================================================================================
	//Aca traigo los registros DEVUELTOS del articulo
	$q = " SELECT fencco, fenfec, SUM(fdecan), '000003' as 'tabla_origen' "                                //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
		."   FROM ".$wbasedato."_000002,".$wbasedato."_000003, ".$wbasedato."_000011 "
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fenest = 'on' "
		."    AND fdeart = '".$wart."'"
		."    AND fentip in ('DA','DP') "
		."    AND fencco = ccocod "
		."    AND ccoima 			   = 'off' "
		."  GROUP BY 1, 2 "
		."  UNION "
		." SELECT fencco, fenfec, COUNT(DISTINCT(fdenum)), '000003' as 'tabla_origen' "                   //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
		."   FROM ".$wbasedato."_000002,".$wbasedato."_000003, ".$wbasedato."_000011 "
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fenest = 'on' "
		."    AND fdeari = '".$wart."'"
		."    AND fentip in ('DA','DP') "
		."    AND fencco = ccocod "
		."    AND ccoima = 'on' "
		."  GROUP BY 1, 2 "
		/*********************************************************************************************************************/
		/* Noviembre 05 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/
		."  UNION"
		." SELECT fencco, fenfec, SUM(fdecan), '000003' as 'tabla_origen' "                                //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
		."   FROM ".$wbasedato."_000002,".$wbasedato."_000143, ".$wbasedato."_000011 "
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fenest = 'on' "
		."    AND fdeart = '".$wart."'"
		."    AND fentip in ('DA','DP') "
		."    AND fencco = ccocod "
		."    AND ccoima = 'off' "
		."    AND fdeest = 'on'"
		."  GROUP BY 1, 2 "
		."  UNION "
		." SELECT fencco, fenfec, COUNT(DISTINCT(fdenum)), '000003' as 'tabla_origen' "                   //DEVOLUCIONES DE SERV. FARMACEUTICO Y DE ENFERMERIA
		."   FROM ".$wbasedato."_000002,".$wbasedato."_000143, ".$wbasedato."_000011 "
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fenest = 'on' "
		."    AND fdeari = '".$wart."'"
		."    AND fentip in ('DA','DP') "
		."    AND fencco = ccocod "
		."    AND ccoima = 'on' "
		."    AND fdeest = 'on'"
		."  GROUP BY 1, 2"
		
		/******************************************************************
		 * Noviembre 8 de 2011
		 ******************************************************************/
		."  UNION "
		." SELECT fencco, fenfec, COUNT(DISTINCT(fdenum)), '000003' as 'tabla_origen' "                   //DEVOLUCIONES DE CENTRAL DE MEZCLAS Y DE ENFERMERIA SIN LOTES
		."   FROM ".$wbasedato."_000002,".$wbasedato."_000003, ".$wbasedato."_000011, ".$wcenmez."_000002 "
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fenest = 'on' "
		."    AND fdeart = '".$wart."'"
		."    AND fdeari = artcod "
		."    AND fentip in ('DA','DP') "
		."    AND fencco = ccocod "
		."    AND ccoima = 'on' "
		."    AND fdelot = '' "		
		."  GROUP BY 1, 2 "
		/*********************************************************************************************************************/
		/* Noviembre 07 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/
		."  UNION "
		." SELECT fencco, fenfec, COUNT(DISTINCT(fdenum)), '000003' as 'tabla_origen' "                   //DEVOLUCIONES DE CENTRAL DE MEZCLAS Y DE ENFERMERIA SIN LOTES
		."   FROM ".$wbasedato."_000002,".$wbasedato."_000143, ".$wbasedato."_000011, ".$wcenmez."_000002 "
		."  WHERE fenhis = ".$whis
		."    AND fening = ".$wing
		."    AND fennum = fdenum "
		."    AND fenest = 'on' "
		."    AND fdeart = '".$wart."'"
		."    AND fdeari = artcod "
		."    AND fentip in ('DA','DP') "
		."    AND fencco = ccocod "
		."    AND ccoima = 'on' "
		."    AND fdelot = '' "
		."    AND fdeest = 'on'"
		."  GROUP BY 1, 2 "
		."  ORDER BY 1, 2, 3 ";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$wnum = mysql_num_rows($res);

	if ($wnum > 0)
	{
		for ($i=1;$i<=$wnum;$i++)
		{
			$row = mysql_fetch_array($res);

			$warr[$i][7]=$row[0];           //Cco que Devuelve
			$warr[$i][8]=$row[1];           //Fecha de Devolucion
			$warr[$i][9]=$row[2];           //Cantidad Devuelta
		}
	}
	//=============================================================================================================


	//Si la cantidad tope de registros anterior ($wnumarr) es mayor a los devueltos tomo como tope de registros a imprimir de la variable
	//$wnumarr, si no tomo la variable $wnum de las devoluciones
	if ($wnumarr<$wnum)
	$wnumarr=$wnum;

	//=============================================================================================================
	//Aca traigo los registros DESCARTADOS del articulo
	$q = " SELECT descco, ".$wbasedato."_000035.Fecha_data, descan "
		."   FROM ".$wbasedato."_000035, ".$wbasedato."_000031 "
		."  WHERE denhis = '".$whis."'"
		."    AND dening = '".$wing."'"
		."    AND dencon = descon "
		."    AND desart = '".$wart."'"
		."  ORDER BY 1, 2, 3 ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$wnum = mysql_num_rows($res);

	if ($wnum > 0)
	{
		for ($i=1;$i<=$wnum;$i++)
		{
			$row = mysql_fetch_array($res);

			$warr[$i][10]=$row[0];           //Cco que descarto
			$warr[$i][11]=$row[1];           //Fecha de Descarte
			$warr[$i][12]=$row[2];           //Cantidad Descartada
		}
	}
	//=============================================================================================================

	//Si la cantidad tope de registros anterior ($wnumarr) es menor a los descartados tomo como tope de registros a imprimir
	//$wnum de la variable
	if ($wnumarr<$wnum)
	$wnumarr=$wnum;

	
	echo '<table align=center>';
	echo "<tr class=seccion1>";
	echo "<th align=center colspan=1><font size=3>Habitacion: </font><b><font size=4>".$whab."</b></font></th>";
	echo "<th align=center colspan=2><font size=3>Historia: </font><b><font size=4>".$whis." - ".$wing."</b></font></th>";
	echo "<th align=center colspan=2><font size=3>Paciente: </font><b><font size=4>".$wpac."</b></font></th>";
	echo "</tr>";
	echo "</table>";

	recorrido_historia($whis,$wing);   //Voy describir todo el recorrido del paciente desde el momento del ingreso

	$wcenmez = consultarAliasPorAplicacion($conex, $wemp, 'cenmez');
	$iconoNPT = "";
	$insitucion = "";
	// Consultar si es una nutrición parenteral
	if(esNPT($conex,$wcenmez,$wcodart,$insitucion))
	{
		// si el origen es ordenes debe abrir el programa rotuloNPT.php sino rotulo2.php
		if(NPTorigenOrdenes($conex,$wbasedato,$wcodart))
		{
			$pathRotulo = "/matrix/cenpro/procesos/rotuloNPT.php?wemp_pmla=".$wemp."&historia=".$whis."&codigo=".$wcodart."&insti=".$insitucion;
		}
		else
		{
			$pathRotulo = "/matrix/cenpro/procesos/rotulo2.php?historia=".$whis."&codigo=".$wcodart."&insti=".$insitucion."&consulta=on";
		}
		
		// $iconoNPT = "<span style='cursor:pointer; position:absolute; right: 5px;bottom:0px; top:0.3px; font-size:8pt; display:flex; align-items:center;' title='Ver r&oacute;tulos NPT' onclick='ejecutar(\"/matrix/cenpro/procesos/rotuloNPT.php?wemp_pmla=".$wemp."&historia=".$whis."&codigo=".$wcodart."&insti=".$insitucion."\")'>Ver r&oacute;tulos nutrici&oacute;n&nbsp;<img src='../../images/medical/sgc/Printer.png' style='width:28px;'><span>";
		$iconoNPT = "<span style='cursor:pointer; position:absolute; right: 5px;bottom:0px; top:0.3px; font-size:8pt; display:flex; align-items:center;' title='Ver r&oacute;tulos NPT' onclick='ejecutar(\"".$pathRotulo."\")'>Ver r&oacute;tulos nutrici&oacute;n&nbsp;<img src='../../images/medical/sgc/Printer.png' style='width:28px;'><span>";
	}
	
	echo "<table align=center>";
	echo "<tr class=seccion1 style='vertical-align:middle;position:relative; '>";
	echo "<th align=center colspan=13><b><font size=4>INSUMO : ".$wcodart." : ".$wnomart."</font></b>".$iconoNPT."</th>";
	echo "</tr>";

	echo "<tr class=encabezadoTabla>";
	echo "<th align=center colspan=3><b>GRABADO</b></th>";
	echo "<th align=center colspan=3><b>APLICADO</b></th>";
	echo "<th align=center colspan=3><b>DEVOLUCION</b></th>";
	echo "<th align=center colspan=3><b>DESCARTE</b></th>";
	echo "<th align=center colspan=1 rowspan=2><b>SALDO</b></th>";
	echo "</tr>";

	echo "<tr class=encabezadoTabla>";
	echo "<th align=center colspan=1><b>C.COSTO</b></th>";
	echo "<th align=center colspan=1><b>FECHA</b></th>";
	echo "<th align=center colspan=1><b>CANTIDAD</b></th>";
	echo "<th align=center colspan=1><b>C.COSTO</b></th>";
	echo "<th align=center colspan=1><b>FECHA</b></th>";
	echo "<th align=center colspan=1><b>CANTIDAD</b></th>";
	echo "<th align=center colspan=1><b>C.COSTO</b></th>";
	echo "<th align=center colspan=1><b>FECHA</b></th>";
	echo "<th align=center colspan=1><b>CANTIDAD</b></th>";
	echo "<th align=center colspan=1><b>C.COSTO</b></th>";
	echo "<th align=center colspan=1><b>FECHA</b></th>";
	echo "<th align=center colspan=1><b>CANTIDAD</b></th>";
	echo "</tr>";

	$wtotfac=0;
	$wtotapl=0;
	$wtotdev=0;
	$wtotdes=0;
	for ($i=1;$i<=$wnumarr;$i++)
	{
		if ($i % 2 == 0)
		   $wclass = "fila1";
		  else
		    $wclass = "fila2";

		echo "<tr class=".$wclass.">";
		if (isset($warr[$i][1]) and $warr[$i][1] != "")
		echo "<td align=center>".$warr[$i][1]."</td>";                                  //Cco que facturo
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][2]) and $warr[$i][2] != "")
		echo "<td align=center>".$warr[$i][2]."</td>";                                  //Fecha de facturacion
		else
		echo "<td align=center>&nbsp</td>";
		if (isset($warr[$i][3]) and $warr[$i][3] != "")
		{
			echo "<td align=right>".number_format($warr[$i][3],2,'.',',')."</td>";        //Cantidad facturada
			$wtotfac=$wtotfac+$warr[$i][3];
		}
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][4]) and $warr[$i][4] != "")
		echo "<td align=center>".$warr[$i][4]."</td>";                                  //Cco que Aplico
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][5]) and $warr[$i][5] != "")
		echo "<td align=center>".$warr[$i][5]."</td>";                                  //Fecha de aplicacion
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][6]) and $warr[$i][6] != "")
		{
			echo "<td align=right>".number_format($warr[$i][6],2,'.',',')."</td>";        //Cantidad aplicada
			$wtotapl=$wtotapl+$warr[$i][6];
		}
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][7]) and $warr[$i][7] != "")
		echo "<td align=center>".$warr[$i][7]."</td>";                                  //Cco que Devolvio
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][8]) and $warr[$i][8] != "")
		echo "<td align=center>".$warr[$i][8]."</td>";                                  //Fecha Devolucion
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][9]) and $warr[$i][9] != "")
		{
			echo "<td align=right>".number_format($warr[$i][9],2,'.',',')."</td>";        //Cantidad Devuelta
			$wtotdev=$wtotdev+$warr[$i][9];
		}
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][10]) and $warr[$i][10] != "")
		echo "<td align=center>".$warr[$i][10]."</td>";                                  //Cco que Descarto
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][11]) and $warr[$i][11] != "")
		echo "<td align=center>".$warr[$i][11]."</td>";                                 //Fecha Descarte
		else
		echo "<td align=center>&nbsp</td>";

		if (isset($warr[$i][12]) and $warr[$i][12] != "")
		{
			echo "<td align=right>".number_format($warr[$i][12],2,'.',',')."</td>";        //Cantidad Descartada
			$wtotdes=$wtotdes+$warr[$i][12];
		}
		else
		echo "<td align=center>&nbsp</td>";

		echo "<td align=center>&nbsp</td>";

	} // fin del for

	echo "<tr class=encabezadoTabla>";
	echo "<th align=center colspan=2>Totales : </th>";
	echo "<th align=right>".number_format(($wtotfac),2,'.',',')."</th>";
	echo "<th align=right colspan=2>&nbsp</th>";
	echo "<th align=right>".number_format(($wtotapl),2,'.',',')."</th>";
	echo "<th align=right colspan=2>&nbsp</th>";
	echo "<th align=right>".number_format(($wtotdev),2,'.',',')."</th>";
	echo "<th align=right colspan=2>&nbsp</th>";
	echo "<th align=right>".number_format(($wtotdes),2,'.',',')."</th>";
	echo "<th align=right>".number_format(($wtotfac-$wtotapl-$wtotdev-$wtotdes),2,'.',',')."</th>";
	echo "<tr>";

	//echo "<tr><td align=center colspan=13><br><A href='rep_facturado_vs_aplicado.php?wemp=".$wemp."&whis=".$whis."&wing=".$wing."&whab=".$whab."'> Retornar</A></font></td></tr>";
	//echo "<tr><td align=center colspan=13><br><A href='rep_facturado_vs_aplicado.php?wemp=".$wemp."&whis=".$whis."'> Retornar</A></font></td></tr>";
	echo "<tr><td align=center colspan=13><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";


	echo "</table>";
	echo "</form>";
}
?>