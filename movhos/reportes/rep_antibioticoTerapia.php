<?php
include_once("conex.php");
/**
* REPORTE INDICADOR DE OPORTUNIDAD DE ATENCION EN URGENCIAS                                                *
*/
// ==========================================================================================================================================|
// PROGRAMA				      :Reporte de Antibiotico-Terapia                                                								 |
// AUTOR				      :Ing. Camilo Zapaat                                                                      						 |
// FECHA CREACION			  :Noviembre 19 de 2012                                                                                          |
// FECHA ULTIMA ACTUALIZACION :Noviembre 19 de 2012                                                                                       	 |
// DESCRIPCION			      :Reporte que permite verificar el historial de aplicación de antibióticos, de un paciente						 |
//							   Tambien permite identificar los cambios de dosis y/o frecuencia durante la suministración del medicamento	 |
//                                                                                                                                           |
//                                                                                         													 |
// ==========================================================================================================================================|
include_once('conex.php');


if(isset($queryAjax)){//cuando existan consultas ajax;

function crearTablaTemporalArticulos($tmpArts)//está funcion va a crear las tablas temporales de articulos, se separa en una funcion para no repetir código
{
	global $conex;
	global $wbasedato;
	$qaux = "DROP TABLE IF EXISTS {$tmpArts}";
	$rsArts = mysql_query($qaux, $conex);

	$qtmpArts = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpArts}
				(INDEX idx(art))
				SELECT Artcod art, Artcom nom
				  FROM {$wbasedato}_000026
				 WHERE artgru like 'J00%'
				   AND artest='on'
				 UNION ALL
				SELECT Pdepro art, Artcom nom
				  FROM cenpro_000003, cenpro_000002
				 WHERE Pdeins like 'MA%'
				   AND pdeest = 'on'
				   AND artcod = Pdepro
				 GROUP BY Pdepro";
	$rsArts = mysql_query($qtmpArts, $conex) or die(mysql_error());
	return;
}

function calcularDiferenciaDias($fecha_inicio, $fecha_fin)//funcion que calcula la diferencia en Dias de dos fechas
{
	$inicio = strtotime($fecha_inicio." 00:00:00");
	$fin = strtotime($fecha_fin." 00:00:00");
	$resultado = $fin*1 - $inicio*1;
	$resultado = gmdate( "z", $resultado);
    return ($resultado);
}

if($queryAjax == 'buscarNombre')//busca el nombre y la habitación correspondientes al paciente cuando se busca por historia e ingreso
{
	$nombre = '';
	$habitacion = 'NA';
	$query = " SELECT Oriced, Oritid
			     FROM root_000037
				WHERE Orihis = '{$whis}'
				  AND Oriing = '{$wing}'";
	$rs = mysql_query($query, $conex);
	while($row = mysql_fetch_array($rs))
	{
		$query = " SELECT Pacno1, Pacno2, Pacap1, Pacap2
				     FROM root_000036
					WHERE Pacced = '{$row['Oriced']}'
					  AND Pactid = '{$row['Oritid']}'";
		$rs2 = mysql_query($query, $conex);
		while($row2 = mysql_fetch_array($rs2))
		{
			$nombre2='';
			$apellido2='';
			if($row2['Pacno2']!='')
				$nombre2 = " ".$row2['Pacno2'];

			if($row2['Pacap2']!='')
				$apellido2 = " ".$row2['Pacap2'];

			$nombre = $row2['Pacno1']."".$nombre2." ".$row2['Pacap1']."".$apellido2;
		}

		$query = "SELECT Ubihac
				    FROM {$wbasedato}_000018
				   WHERE Ubihis = '{$whis}'
					 AND Ubiing = '{$wing}'";
		$rs3 = mysql_query($query, $conex);
		while($row3 = mysql_fetch_array($rs3))
		{
			$habitacion = $row3['Ubihac'];
		}
	}
	$data = array('nombre'=>$nombre, 'habitacion'=>$habitacion);
	echo json_encode($data);
	return;
}

if($queryAjax == 'actFechaInicial')//acá se consultan las fechas por defecto al buscar una historia con ingreso(fecha de ingreso, y fecha de alta definitiva)
{
	$fechaIni='';
	$fechaFin='';
	$query = "SELECT Fecha_data, ubifad fechaAlta
			    FROM {$wbasedato}_000018
			   WHERE ubihis = '{$whis}'
			     AND ubiing = '{$wing}'";
	$rs = mysql_query($query,$conex);
	$row = mysql_fetch_array($rs);

	if($row[0]!='0000-00-00'&&($row[0]!=''))
		$fechaIni=$row[0];

	if($row[1]!='0000-00-00'&&($row[1]!=''))
		$fechaFin=$row[1];
		else
			$fechaFin=$hoy;

	$data = array('fechaIni'=>$fechaIni, 'fechaFin'=>$fechaFin);
	echo json_encode($data);
	return;
}

if($queryAjax == 'buscarPacientesCco') // funcion que lista a los pacientes que tienen antibioticoterapia programada en un centro de costos dado;
{
	$cco = explode("-",$wcco);
	$ccoNom = $cco[1];
	$cco = $cco[0];
	$filtroCco='';
	$tabla ='';
	$i=0;
	$id = date('Hms'); //para identificar las tablas temporales;
	$tmpArts = 'tmpArts'.$id; //temporal de antibioticos.
	$tmpHist = 'tmpHist'.$id; //temporal de historias con antibioticos.
	$tmpPacs = 'tmpPacs'.$id; //temporal de pacientes.
	$filtroAltaDefinitiva = '';
	if($addAd!='si')
	{
		$filtroAltaDefinitiva = "AND ubiald = 'off' ";
	}

	if($cco != 'todos')
	{
		$filtroCco = " AND a.Ubisac = '{$cco}'";
	}
	//creación de la tabla TEMPORAL de los ARTICULOS que son antibióticos.
	crearTablaTemporalArticulos($tmpArts);

	//creacion de la TEMPORAL de PACIENTES
	$qaux = "DROP TABLE IF EXISTS {$tmpPacs}";
	$rsArts = mysql_query($qaux, $conex);


    // ESTO SE COMENTÓ CON EL PROPÓSITO DE QUE SE MUESTRE SIEMPRE LOS PACIENTES QUE ESTÁN "ACOSTADOS" EN LAS CAMAS
	/*$qtmpPacs = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpPacs}
				 (INDEX idx(ubihis, ubiing))
				 SELECT a.Fecha_data fechaing, ubihis, ubiing, ubisac, Pacno1, Pacno2, Pacap1, Pacap2, ubihac, ubifad fechaEgr, habord, habcco
				   FROM {$wbasedato}_000018 a, root_000036, root_000037, {$wbasedato}_000020
				  WHERE a.Fecha_data BETWEEN '{$fecIni}' AND '{$fecFin}'
				    {$filtroCco}
					AND Orihis = Ubihis
					AND Oriori = '{$wemp_pmla}'
					AND Pactid = Oritid
					AND Pacced = Oriced
					AND ubihac = Habcod
					{$filtroAltaDefinitiva}
				  ORDER BY habcco, habord";*/
	$qtmpPacs = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpPacs}
				 (INDEX idx(ubihis, ubiing))
				 SELECT a.Fecha_data fechaing, ubihis, ubiing, ubisac, Pacno1, Pacno2, Pacap1, Pacap2, ubihac, ubifad fechaEgr, habord, habcco
				   FROM {$wbasedato}_000018 a, root_000036, root_000037, {$wbasedato}_000020
				  WHERE Orihis = Ubihis
					AND Oriori = '{$wemp_pmla}'
					AND Pactid = Oritid
					AND Pacced = Oriced
					{$filtroCco}
					AND ubihac = Habcod
					{$filtroAltaDefinitiva}
				  ORDER BY habcco, habord";
	$rstmpPacs = mysql_query($qtmpPacs, $conex);

	// ESTA MODIFICACIÓN ES CRÍTICA, CONSISTE EN MODIFICAR EL QUERY, CAMBIANDO LA BUSQUEDA POR FECHA A BUSCAR POR HISTORIA INGRESO MEDIANTE UN IN DE DICHOS CAMPOS CONCATENADOS,
	$arrayAux = array();
	$busqueda = "IN (";
	$queryAux = "SELECT ubihis, ubiing
				   FROM {$tmpPacs}
				  GROUP BY 1,2";
	$rsAux	  = mysql_query( $queryAux, $conex );

	if( mysql_num_rows( $rsAux ) == 0 ){
		$data = array('pacientes'=>"");
		echo json_encode($data);
		return;
	}

	while( $rowtmpPacs = mysql_fetch_array( $rsAux )){/* se construye un in() compuesto de historia-ingreso con el programadapósito de ejecutar la busqueda */

		array_push( $arrayAux, "'".$rowtmpPacs['ubihis']."".$rowtmpPacs['ubiing']."'");
	}
	$historias_ingresos = implode(",", $arrayAux );
	$busqueda .= $historias_ingresos;
	$busqueda .= ")";
	//
	//creación de la TEMPORAL  de HISTORIAS con antibióticos.
	$qaux = "DROP TABLE IF EXISTS {$tmpHist}";
	$rsArts = mysql_query($qaux, $conex);

	/*$qtmpHist = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpHist}
				 (INDEX idx(hist, ing))"
				 //SELECT Kadhis hist, Kading ing, Kadart art, kadido, Kadcma fracciones, Kadper frecuencia
			   ."SELECT Kadhis hist, Kading ing
				   FROM {$wbasedato}_000054, {$tmpArts}
				  WHERE Kadfec BETWEEN '{$fecIni}' AND '{$fecFin}'
				    AND Kadart = art
					AND Kadest = 'on'
				  GROUP BY Kadhis, Kading";*/

	// se buscan las historias-ingresos que tienen antibióticos programados
	 $qtmpHist = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tmpHist}
				 (INDEX idx(hist, ing))"
				 //SELECT Kadhis hist, Kading ing, Kadart art, kadido, Kadcma fracciones, Kadper frecuencia
			   ."SELECT Kadhis hist, Kading ing
				   FROM {$wbasedato}_000054, {$tmpArts}
				  WHERE concat( kadhis, kading ) {$busqueda}
				    AND Kadart = art
					AND Kadest = 'on'
				  GROUP BY Kadhis, Kading";
	$rstmpHist = mysql_query($qtmpHist, $conex) or die (mysql_error());
	//FIN CREACIÓN DE TABLAS TEMPORALES.

	//QUERY PRINCIPAL.
	$qppal = " SELECT b.*
				 FROM {$tmpPacs} b, {$tmpHist} a
				WHERE b.ubihis = hist
				  AND b.ubiing = ing";
	$rsppal = mysql_query($qppal, $conex) or die(mysql_error());
	$resultados =mysql_num_rows($rsppal);

	if($resultados>0)
	{
		$tabla .= "<center><table id='tblPacientes'>";
		$tabla .= "<tr><td class='encabezadotabla' colspan='6' align='center'>PACIENTES CON ANTIBIOTICOTERAPIA - CENTRO DE COSTOS: <span title='{$ccoNom}'>{$cco}</span></td></tr>";
		$tabla .= "<tr class='encabezadotabla'><td>HISTORIA</td><td>INGRESO</td><td>NOMBRE</td><td>DIAS DE ESTANCIA</td><td>HABITACION</td><td>&nbsp;</td></tr>";
	}
	while($row = mysql_fetch_array($rsppal))
	{
		$fechaEgreso = $row['fechaEgr'];
		if($row['fechaEgr']=='0000-00-00')
			$fechaEgreso = date('Y-m-d');
		$diasInstancias = calcularDiferenciaDias($row['fechaing'], $fechaEgreso)+1;
		$i++;
		$nombre2='';
		$apellido2='';
		if(is_int($i/2))
			$wclass = 'fila1';
			else
				$wclass = 'fila2';
		if($row['Pacno2']!='')
			$nombre2 = " ".$row['Pacno2'];

		if($row['Pacap2']!='')
			$apellido2 = " ".$row['Pacap2'];

		$nombre = $row['Pacno1']."".$nombre2." ".$row['Pacap1']."".$apellido2;
		$tabla .= "<tr  class='{$wclass}'>";
			$tabla .= "<td id='his{$i}'>{$row['ubihis']}</td>";
			$tabla .= "<td id='ing{$i}' align='center'>{$row['ubiing']}</td>";
			$tabla .= "<td id='nom{$i}'>{$nombre}</td>";
			$tabla .= "<td id='dias{$i}' align='center'>{$diasInstancias}</td>";
			$tabla .= "<td id='hac{$i}'>{$row['ubihac']}</td>";
			$tabla .= "<td id='verDet{$i}' style='cursor:pointer' onclick='verDetallePaciente(\"{$i}\");'><input type='hidden' id='fechaIngreso{$i}' value='{$row['fechaing']}'><input type='hidden' id='fechaEgreso{$i}' value='{$row['fechaEgr']}'><font id='font{$i}' color='blue'>Ver Detalle</font></td>";
		$tabla .= "</tr>";
	}
	if($resultados>0)
	{
		$tabla .= "</table></center>";
	}
	$data = array('pacientes'=>$tabla);
	echo json_encode($data);
	return;
}

if($queryAjax == 'detalleTerapia') //funcion que arma la tabla con el detalle de aplicacion y cambios del kardex de un paciente dado
{
	$table = "";
	//funciones propias de esta llamada ajax
	function paginacion()
	{
		global $encabezado;
		global $paginas;
		$encabezado .= "<center><table>";
			$encabezado .= "<tr class='encabezadotabla'><td colspan='5' id='td_pagina' align='center'>P&aacute;gina {$paginas} de {$paginas}</td></tr>";
			$encabezado .= "<tr>";
				$encabezado .= "<td class='encabezadoTabla' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' title='Primera' onclick='cambiarPagina(\"ini\");'> <font style='font-weight:bold'>&nbsp;<<&nbsp;</font></td>";
				$encabezado .= "<td class='encabezadoTabla' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' title='Anterior' onclick='cambiarPagina(\"ant\");'><font style='font-weight:bold'>&nbsp;<&nbsp;</font></td>";
				$encabezado .= "<td><select id='paginaMostrada' onchange='cambiarPagina(\"sel\");'>";
					for($i = 1; $i <= $paginas; $i++)
					{
						if($i==$paginas)
							$encabezado .= "<option value='{$i}' selected='selected'>{$i}</option>";
							else
								$encabezado .= "<option value='{$i}'>{$i}</option>";
					}
				$encabezado .= "</select></td>";
				$encabezado .= "<td class='encabezadoTabla' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' title='Siguiente' onclick='cambiarPagina(\"sig\");'><font style='font-weight:bold'>&nbsp;>&nbsp;</font></td>";
				$encabezado .= "<td class='encabezadoTabla' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' title='Ultima' onclick='cambiarPagina(\"fin\");'><font style='font-weight:bold'>&nbsp;>>&nbsp;</font></td>";
			$encabezado .= "</tr>";
		$encabezado .= "</table></center>";

		return;
	}

	function compararFechas($fecha1,$fecha2)//funcion que verifica si $fecha1 es mayor a $fecha2;
	{
		//datos de la fecha1
		$fecha1 = explode('-',$fecha1);
		$anho1 = $fecha1[0]*1;
		$mes1 = $fecha1[1]*1;
		$dia1 = $fecha1[2]*1;

		$fecha2 = explode('-',$fecha2);
		$anho2 = ($fecha2[0])*1;
		$mes2 = ($fecha2[1])*1;
		$dia2 = ($fecha2[2])*1;

		if($anho1 > $anho2) //si es mayor el año retorna verdadero
			return(true);
		if($anho1 == $anho2)
		{
			if($mes1 > $mes2)// si son el mismo año pero es mayor el mes1 returna verdadero
				return(true);
			if($mes1 == $mes2)
			{
				if($dia1 > $dia2)
					return(true);
				if($dia1 == $dia2)
					return(true);
					else
					 return(false);
			}else
				{
					return(false);
				}
		}else
			{
				return (false);
			}

	}

	function fechaInicio($his, $ing, $articulo)//elige la menor fecha asociada a una programación en kardex entre la 54 y la 15
	{
		$queryfei15 = "SELECT MIN(Aplfec)
					   FROM movhos_000015
					  WHERE Aplhis = '{$his}'
					    AND Apling = '{$ing}'
						AND Aplart = '{$articulo}'";
		$rsfeci15 = mysql_query($queryfei15);
		$rowfei15 = mysql_fetch_array($rsfeci15);
		$fecini15 = $rowfei15[0];

		$queryfei54 = "SELECT MIN(Aplfec)
					   FROM movhos_000045
					  WHERE Kadhis = '{$his}'
					    AND Kading = '{$ing}'
						AND Kadart = '{$articulo}'";
		$rsfeci54 = mysql_query($queryfei15);
		$rowfei54 = mysql_fetch_array($rsfeci54);
		$feciori54 = $rowfei54[0];
		if($fecini15[0]=='')
			return ($feciori54); //si no existe en la 54 cojo la fecha de la 54
			else{
				$esMayor = compararFechas($feciori54, $fecini15);
				if($esMayor)
					return($fecini15);
				else
					return($feciori54);
			}
		return($fecini15);
	}

	function corregirRonda($rondaOriginal) //esta función está hecha para que corriga la ronda de aplicacion registrada en la 15 cuando sea necesario
	{
		$rondaAuxiliar = explode(':',$rondaOriginal);
		$hora = $rondaAuxiliar[0]*1;
		if(is_int($hora/2))
		{
			$hora = $hora;
		}
		else
		{
			$hora = $hora -1;
		}

		if($hora<10)
		{
			$nuevaRonda = "0".$hora.":00 - AM";
		}else
			{
				if($hora>=12)
					$nuevaRonda = "".$hora.":00 - PM";
				else
					{
						$nuevaRonda = $hora.":00 - AM";
					}
			}
		return($nuevaRonda);
		//gmdate( "H:i:s", floor( $hora/2)*2*3600 );
	}

	function colorCambio($fechaActual, $keyFecha, $ido, $articulo)//entrega el color que se va a mostrar en pantalla en cada dia verificando los cambios.
	{
		global $cambios;
		$cambiosPropios = array();
		$cambiosPropios['cambio'] = false; //hay algun cambio?
		$cambiosPropios['dosis'] = array(); //['value']['descripcion'];
		$cambiosPropios['frecuencia'] = array(); //['value']['descripcion'];
		$datoBuscado = $fechaActual."_".$keyFecha;
		$cambiosPropios['dosis']['value'] = false;
		$cambiosPropios['frecuencia']['value'] = false;

		if(!isset($cambios[$articulo][$ido]))
			$cambios[$articulo][$ido] = array();
		if(array_key_exists($datoBuscado, @$cambios[$articulo][$ido]))
		{
			if(array_key_exists('frecuencia', $cambios[$articulo][$ido][$datoBuscado]))
			{
				$cambiosPropios['frecuencia']['value'] = true; //hay cambio en la frecuencia.
				$cambiosPropios['cambio'] = true;
				$cambioFr1=$cambios[$articulo][$ido][$datoBuscado]['frecuencia'];
				$cambioFr1 = explode("_",$cambioFr1);
				$cambiosPropios['frecuencia']['descripcion'] = "<br>DE: ".$frecuencias[$cambioFr1[0]]." A ".$frecuencias[$cambioFr1[1]];
				$cambiosPropios['frecuencia']['nuevaFrecuencia'] = $frecuencias[$cambioFr1[1]];
				//$frecuenciaMostrada = $frecuencias[$cambioFr1[1]];
			}
			if(array_key_exists('dosis', $cambios[$articulo][$ido][$datoBuscado]))
			{
				$cambiosPropios['dosis']['value'] = true; //hay cambio en la dosis.}
				$cambiosPropios['cambio'] = true;
				$cambioDo1=$cambios[$articulo][$ido][$datoBuscado]['dosis'];
				$cambioDo1 = explode("_",$cambioDo1);
				$cambiosPropios['dosis']['descripcion'] = "<br>DE: ".$cambioDo1[0]." A ".$cambioDo1[1];
			}
		}
		return($cambiosPropios);
	}

	function inicioArticulo($keyArticulo)//fecha de inicio de aplicacion del articulo... la primera aplicacion
	{
		global $kardex;
		foreach($kardex[$keyArticulo] as $keyIdo=>$datos)
		{
			$fecha = $datos['fechaInicio'];
			return($fecha);
		}
	}

	function detallarSuspencion($articulo, $historia, $ingreso, $ido, $fecha, $j)
	{
		global $frecuencias;
		global $vias;
		global $conex;
		global $wbasedato;
		$query = "SELECT Kadper frecuencia, Kadcfr dosis, Kaduma unidad, Kaddma dosisMaxima, Kadvia via
				    FROM {$wbasedato}_000054
				   WHERE kadhis = '{$historia}'
				     AND kading = '{$ingreso}'
					 AND kadart = '{$articulo}'
					 AND kadido = '{$ido}'
					 AND kadfec = '{$fecha}'";
		$rs = mysql_query($query, $conex);
		while($row = mysql_fetch_array($rs))
		{
			if(is_int($j))
				$cl = 'fila2';
				else
				$cl = 'fila1';
			if($row['dosisMaxima']=='')
				$row['dosisMaxima'] = 'No aplica';
			$fila = "<tr class={$cl}>";
				$fila .= "<td>{$row['dosis']}({$row['unidad']})</td>";
				$fila .= "<td>{$frecuencias[$row['frecuencia']]}</td>";
				$fila .= "<td>{$vias[$row['via']]}</td>";
				$fila .= "<td>{$row['dosisMaxima']}</td>";
			$fila .= "</tr>";
		}
		return($fila);
	}

	function buscarNombrePaciente($whis, $wing)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		$hoy = date('Y-m-d');
		$datos = array();
		$query ="SELECT a.Fecha_data fechaing, ubihis, ubiing, ubisac, Pacno1, Pacno2, Pacap1, Pacap2, ubihac, ubifad fechaEgr
				   FROM {$wbasedato}_000018 a, root_000036, root_000037
				  WHERE Orihis = '{$whis}'
					AND Oriori = '{$wemp_pmla}'
					AND Ubihis = Orihis
					AND Ubiing = '{$wing}'
					AND Pactid = Oritid
					AND Pacced = Oriced ";
		$rs = mysql_query( $query, $conex ) or die (mysql_error());

		while($row = mysql_fetch_array($rs))
		{
			$nombre2='';
			$apellido2='';
			if($row['Pacno2']!='')
				$nombre2 = " ".$row['Pacno2'];

			if($row['Pacap2']!='')
				$apellido2 = " ".$row['Pacap2'];

			$nombre = $row['Pacno1']."".$nombre2." ".$row['Pacap1']."".$apellido2;
			$datos['nombre'] = $nombre;
			$datos['habitacion'] = $row['ubihac'];
			$datos['fechaIngreso'] = $row['fechaing'];
			$fechafinal = $row['fechaEgr'];
			if($fechafinal == '0000-00-00')
				$fechafinal = $hoy;
		    $datos['fechaEgreso'] = $fechafinal;
			$datos['diasEstancia'] = (calcularDiferenciaDias($row['fechaing'],$fechafinal)+1);
			//echo "{$row['fechaing']} - {$fechafinal} - {$fechafinal} \n";
		}
		return($datos);
	}
	//final de funciones propias de esta llamada
	if( (!isset($wnom) or $wnom=='') or  ( $wfechaIngreso == "undefined" or !isset( $wfechaIngreso )) )
	{
		$datosPaciente = buscarNombrePaciente( $whis , $wing );
		$wnom = $datosPaciente['nombre'];
		$whab = $datosPaciente['habitacion'];
		$wdiasEstancia = $datosPaciente['diasEstancia'];
		if( $wfechaIngreso == "undefined" or $wfechaIngreso == "" or !isset( $wfechaIngreso ))
			$wfechaIngreso = $datosPaciente['fechaIngreso'];
		if( $wfechaEgreso == "undefined" or $wfechaEgreso == "" );
			$wfechaEgreso = $datosPaciente['fechaEgreso'];
		if($whab=='')
			$whab == 'NA';

	}

	//declaración de variables
	$id = date('Hms'); //para identificar las tablas temporales;
	$tmpArts = 'tmpArts'.$id; //temporal de antibioticos.
	$tmpPacs = 'tmpPacs'.$id; //temporal de pacientes.
	$fechaInicialInforme = $wfechaIngreso; //fecha de inicio del reporte
	$fechaFinalInforme = date('Y-m-d'); //fechaFinal de reporte basica(el dia de hoy)

	if($wfechaEgreso != '0000-00-00' && $wfechaEgreso!='' or $wfechaEgreso == "undefined") //fecha final del reporte en caso de que se haya dado alta anteriormente al paciente definitiva
		$fechaFinalInforme = $wfechaEgreso;

	$diasTotalesInforme = calcularDiferenciaDias($fechaInicialInforme, $fechaFinalInforme)+1; //rango de dias en que el paciente está o estuvo en la clinica.
	$articulos = array(); //codigo - nombre
	$kardex = array(); // programación en la 54 para el paciente
	$rondas = array(); // posibles valores de ronda.
	$consolidado = array(); //arreglo con el consolidado de movimientos en la 15.
	$frecuencias = array(); //arreglo con las frecuencias.
	$cambios = array();  //arreglo para almacenar los cambios entre los dias.
	$detalleAplicaciones = array(); //arreglo que almacenará el número de aplicaciones y las dosis de estas;
	$hayDatos = false; //variable para validar si se retorna la tabla con algun dato o no
	$paginas = 0;
	$todosDetalles = ''; //variable que contiene los divs ocultos con el detalle de las aplicaciones de cada medicamento
	$encabezado = '';
	$trRondas = '';
	$fechasOrdenadas = array(); //variable con la cual se mantendra el control de lo que sucede diariamente aplicaciones y ausencia de estas.
	$frecuenciasPorDia = array();
	$DosisPorDia = array(); //almacena las dosis máximas por dia de aplicacion de cada articulo
	$viasPorDia = array(); //almacena las vias de administracion por dia de aplicacion de cada articulo
	$suspendidosPorDia = array(); //variable que almacena por articulo e ido si se supendió en un dia dado.
	$idosPorArticulo = array(); //son todos los idos que pertenecen a un articulo.
	$fechasPorIdo = array();

	//inicializo el arreglo de rondas.
	$sufijo = 'AM';
	$prefijo='0';
	for($i = 0; $i < 24; $i=$i+2)
	{
		$j = $i;
		if($i >= 12)
			$sufijo = 'PM';
		if($j>=10)
			$prefijo = '';
			else
				$prefijo = '0';
		$rondas["{$prefijo}{$j}:00 - {$sufijo}"]='s';
	}
	//final de arreglo de rondas

	//creamos la tabla temporal con los articulos que son antibióticos
	crearTablaTemporalArticulos($tmpArts);

	//creacion del arreglo de articulos.
	$qarts = " SELECT *
			   FROM {$tmpArts} ";
	$rsArts = mysql_query($qarts, $conex);
	while($row = mysql_fetch_array($rsArts))
	{
		$articulos[$row['art']] = $row['nom'];
	}

	//creación del arreglo de frecuencias.
	$qvias = "SELECT Viacod, Viades
			    FROM {$wbasedato}_000040";
	$rsvias = mysql_query($qvias, $conex);
	while($rowVias = mysql_fetch_array($rsvias))
	{
		$vias[$rowVias['Viacod']]=$rowVias['Viades'];
	}


	//creación de arreglo de vias de suministración.
	$qfrec = "SELECT Percod, Percan, Peruni
			    FROM {$wbasedato}_000043";
	$rsfrec = mysql_query($qfrec, $conex);
	while($rowfrec = mysql_fetch_array($rsfrec))
	{
		$frecuencias[$rowfrec['Percod']]=$rowfrec['Percan']." ".$rowfrec['Peruni']."(s)";
	}

	//consultamos los datos de los antibióticos programados para el paciente.(todos los registros en la 54 asociados a antibióticos)
	$qhist = "SELECT Kadfec fecha, Kadhis his, Kading ing, Kadart art, Kadper fre, Kadfin fecin, Kadhin horin, Kadcfr dosis, Kadido ido, Kaduma unidad, Kadsus suspendido, Kaddma dosisMax, Kadvia via
			    FROM {$wbasedato}_000054
			   WHERE Kadhis = '{$whis}'
				 AND Kading = '{$wing}'
				 AND Kadest = 'on'
				 AND Kadart IN ( SELECT art
								   FROM {$tmpArts} )
				 ORDER BY fecha, art, ido, fecin, horin";
	$rshist = mysql_query($qhist, $conex) or die(mysql_error());

	//se almacena en un arreglo todos los datos correspondientes a la programación de los antibioticos en el kardex
	while($row = mysql_fetch_array($rshist))
	{
		$huboCambio =  false;
		if(!isset($kardex[$row['art']][$row['ido']]['registros']))//el primer registro que llega por articulo e ido.
		{
			$kardex[$row['art']][$row['ido']]['registros'] = 0;
			$kardex[$row['art']][$row['ido']]['dosisIni'] = $row['dosis'];
			$kardex[$row['art']][$row['ido']]['frecuenciaIni'] = $row['fre'];
			$kardex[$row['art']][$row['ido']]['dosisActual'] = $row['dosis'];
			$kardex[$row['art']][$row['ido']]['unidad'] = $row['unidad'];
			$kardex[$row['art']][$row['ido']]['frecuenciaActual'] = $row['fre'];
			$kardex[$row['art']][$row['ido']]['fechaActual'] = $row['fecha'];
		}else //acá se verifican los cambios entre un dia y otro;
			{
				if(!isset($cambios[$row['art']][$row['ido']]))
					$cambios[$row['art']][$row['ido']]=array();

				$kardex[$row['art']][$row['ido']]['registros']++;
				if($kardex[$row['art']][$row['ido']]['frecuenciaActual'] != $row['fre']) // se verifica si hay cambios en la frecuencia.
				{
					$cambios[$row['art']][$row['ido']][$kardex[$row['art']][$row['ido']]['fechaActual']."_".$row['fecha']]['frecuencia'] = $kardex[$row['art']][$row['ido']]['frecuenciaActual']."_".$row['fre'];
					$kardex[$row['art']][$row['ido']]['frecuenciaActual'] = $row['fre'];
					$huboCambio =  true;

				}
				if($kardex[$row['art']][$row['ido']]['dosisActual'] != $row['dosis']) // se verifica si hay cambios en la dosis.
				{
					$cambios[$row['art']][$row['ido']][$kardex[$row['art']][$row['ido']]['fechaActual']."_".$row['fecha']]['dosis'] = $kardex[$row['art']][$row['ido']]['dosisActual']."_".$row['dosis'];
					$kardex[$row['art']][$row['ido']]['dosisActual'] = $row['dosis'];
					$huboCambio =  true;
				}

				$kardex[$row['art']][$row['ido']]['fechaActual'] = $row['fecha'];
			}
			$frecuenciasPorDia[$row['art']][$row['fecha']][$row['ido']] = $row['fre'];
			$DosisPorDia[$row['art']][$row['fecha']][$row['ido']] = $row['dosisMax'];
			$viasPorDia[$row['art']][$row['fecha']][$row['ido']] = $row['via'];
			$suspendidosPorDia[$row['art']][$row['fecha']][$row['ido']] = $row['suspendido'];
			$idosPorArticulo[$row['art']][$row['fecha']][$row['ido']] = 's';
	}

	//acá armamos el arreglo consolidado con el detalle de las aplicaciones  de cada articulo programado en el kardex, para realizar la cuenta por el dia.
	foreach($kardex as $keyArt=>$datos)
	{
		foreach($datos as $keyIdo=>$detalle)
		{
			$fec = $detalle['fechaActual'];
			$qapl = "SELECT aplart, aplfec, aplido, aplron, apldos, aplufr
					   FROM {$wbasedato}_000015
					  WHERE Aplido = '{$keyIdo}'
					    AND Aplhis = '{$whis}'
					    AND Apling = '{$wing}'
						AND Aplart = '{$keyArt}'
						AND Aplest = 'on'
					  GROUP BY aplart, aplfec, aplido, aplron
					  ORDER BY aplfec, aplron";
			$rsapl = mysql_query($qapl, $conex);

			$apls = 0;
			while($rapl = mysql_fetch_array($rsapl))
			{
				$hayDatos = true;
				$apls++;
				$rapl['aplron'] = corregirRonda($rapl['aplron']);
				$consolidado[$keyArt][$rapl['aplfec']][$rapl['aplido']][$rapl['aplron']]['Ronda'] = $rapl['aplron'];
				$consolidado[$keyArt][$rapl['aplfec']][$rapl['aplido']][$rapl['aplron']]['FechaApl'] = $rapl['aplfec'];
				$consolidado[$keyArt][$rapl['aplfec']][$rapl['aplido']][$rapl['aplron']]['Dosis'] = $rapl['apldos'];
				$consolidado[$keyArt][$rapl['aplfec']][$rapl['aplido']][$rapl['aplron']]['Unidad'] = $rapl['aplufr'];
			}
		}
	}

	//aca se arma el tr para mostrar las rondas graficamente.
	$trRondas .= "<tr class='encabezadotabla'>";
	$trRondas .= "<td>FECHA APLICACION</td>";
	foreach($rondas as $keyRonda=>$datosRonda)//array que recorre las rondas y verifica la existencia
	{
		$nomRon = explode("-",$keyRonda);
		$nomRon = trim($nomRon[0]);
		$trRondas .= "<td align='center'>{$nomRon}</td>";
	}
	$trRondas .= "<td align='center'>IR A KARDEX</td></tr>";
	if($hayDatos)
	{
		//encabezado
		$encabezado  = "<div style='width:80%;' align='right'><table>";
		$encabezado .= "<tr class='encabezadotabla'><td colspan='4'>Notaci&oacute;n de colores</td></tr>";
		$encabezado .= "<tr class='fila1'><td>Condiciones Iniciales</td><td colspan=2 style='background-color:yellow' >&nbsp;&nbsp;&nbsp;</td></tr>";
		$encabezado .= "<tr class='fila2'><td>Suspendido</td><td colspan=2 class='suspendido'>&nbsp;&nbsp;&nbsp;</td></tr>";
		$encabezado .= "<tr class='fila1'><td>Cambio en Dosis</td><td style='background-color:#21EF35'>&nbsp;&nbsp;&nbsp;</td><td style='background-color:yellow'>&nbsp;&nbsp;&nbsp;</td></tr>";
		$encabezado .= "<tr class='fila2'><td>Cambio en Frecuencia</td><td style='background-color:#EFA021'>&nbsp;&nbsp;&nbsp;</td><td style='background-color:#EF7D21'>&nbsp;&nbsp;&nbsp;</td></tr>";
		$encabezado .= "<tr class='fila1'><td>Cambio en Dosis y Frecuencia</td><td style='background-color:#A09F9F'>&nbsp;&nbsp;&nbsp;</td><td style='background-color:#C2C2C2'>&nbsp;&nbsp;&nbsp;</td></tr>";
		$encabezado .= "</table></div>";
		$encabezado .= "<div style='clear:both;'></div>";
		$encabezado .= "<div><center><table id='tblDatosPaciente' width='80%'>";
		$encabezado .= "<tr class='encabezadotabla'><td align='center'><b>HABITACI&Oacute;N</b></td><td align='center'><b>HISTORIA</b></td><td align='center'><b>INGRESO</b></td><td align='center'><b>NOMBRE</b><td align='center'><b>FECHA INGRESO</b><td align='center'>DIAS DE ESTANCIA</td></td></td></tr>";
		$encabezado .= "<tr class='fila2'><td bgcolor='333399' align='center'><font size='5' color='00FF00'>{$whab}</font></td><td align='center'><b>{$whis}</b></td><td align='center'><b>{$wing}</b></td><td align='center'>{$wnom}</td><td align='center'>{$wfechaIngreso}</td><td align='center'>{$diasTotalesInforme}</td></tr>";
		$encabezado .= "</table></center></div>";
		if(is_int($diasTotalesInforme/30))
		{
			$paginas = $diasTotalesInforme/30;
		}else
			{
				$paginas = floor($diasTotalesInforme/30)+1;
			}
		$encabezado .= "<input type='hidden' id='paginas' value='{$paginas}'>";
		paginacion();

		$colspan = $diasTotalesInforme + 4;
		$colspan2 = $diasTotalesInforme + 1;
		$todosDetalles = '';
		$encabezado .= "<center><table id='tblDetalle' width='80%'>";
		$encabezado .= "<tr class='encabezadotabla'><td colspan='{$colspan}' nowrap='nowrap' align='center'>CONSOLIDADO DE ANTIBIOTICOTERAPIA POR DIAS</td>";//articulo
		$encabezado .= "<tr class='encabezadotabla'><td colspan=3 nowrap='nowrap' align='center'>CARACTER&Iacute;STICAS INICIALES DE PROGRAMACI&Oacute;N DEL MEDICAMENTO</td><td align='center' colspan='{$colspan2}'>DIAS DE ESTANCIA Vs DIAS DE TRATAMIENTO</td>";//articulo
		$encabezado .= "<tr class='encabezadotabla' id='guiaDias'>";
		$encabezado .= "<td>ANTIBIOTICO</td><td nowrap='nowrap'>FECHA DE INICIO</td><td nowrap='nowrap'>FECHA FINAL</td>";

		for($i = 1; $i<=$diasTotalesInforme; $i++)
		{
			$j = $i-1;
			$dia = strtotime ( "+{$j} day" , strtotime ( $wfechaIngreso ) ) ;
			$nuevafecha = date ( 'Y-m-d' , $dia );
			$encabezado .= "<td class='msg_tooltip' title='{$nuevafecha}' style='width:30px'>".(($i<10)? "&nbsp;&nbsp;".$i : $i)."</td>";
			$fechasOrdenadas[$nuevafecha]='s';
		}
		$encabezado .= "<td>TOTAL APLICACIONES</td>";
		$encabezado .= "</tr>";
		//final del encabezado;
		$k = 0;
		$table = $encabezado;
		foreach($consolidado as $keyArt=>$fechasDet)//SE RECORRE EL ARREGLO DE CONSOLIDADO POR ARTICULO
		{
			 $articulo = $keyArt;
			 $nombreArticulo = $articulos[$articulo];
			 $total = 0; 		// total de aplicaciones del medicamento
			 $d=0; 				//para llevar el control de la clase en las tablas de detalle;
			 $fechaInicioArt = fechaInicio($whis, $wing, $keyArt); //la fecha para la cual está programado el inicio de una pp
			 $fechas = ''; 		//almacena el codigo html que muestra las aplicaciones en el tiempo por dia.
			 $fechaActual = ''; //esta variable guardara la fecha anterior para verificar si hubo algun cambio en la programación del medicamento.
			 $fechaSuspencion = '';//esta variable permitira evaluar si el ultimo registro de kardex de un medicamento está suspendido;
			 $frecuenciaMostrada = '';
			 $cambiosConsolidado = array();
			 unset($nuevoComportamiento);
			 $finalizaciones = array(); //este arreglo va a almacenar las fechas en las que se termina la suministración de una programación en kardex de cada articulo.
			 $numeroDia = 0;

			 //detalle de las aplicaciones de este articulo
			 $divDetalle = '';
			 $detalle = '';
			 $k++;
			 if(is_int($k/2))
				$wclass = 'fila1';
				else
				$wclass = 'fila2';
			foreach($fechasOrdenadas as $keyFecha=>$detFecha)//FOR QUE RECORRE LAS FECHAS PARA VERIFICAR LAS APLICACIONES
			{
				if(array_key_exists($keyFecha,$fechasDet))
				{
					$hayAplicaciones = false;
					$aplicacionesDiarias = array();
					$hayCambio = false;
					$d++;
					if(is_int($d/2))
						$wclass1 = 'fila1';
						else
						$wclass1 = 'fila2';
					$divDetalle .= "<tr class='{$wclass1}' id='{$keyArt}_{$keyFecha}'>";
					$divDetalle .= "<td align='center'>{$keyFecha}</td>";
					$dosisDia = 0;
					foreach($fechasDet[$keyFecha] as $keyIdo=>$datos)
					{
						//se va guardando la fecha de tal manera que al terminar el ciclo quede la fecha final de administracion del articulo
						$finalizaciones[$keyIdo]['fechaFinal'] = $keyFecha;
						//variables para el detalle del dia que se aplico.
						$cambioFrecuencia = false;
						$cambioDosis = false;
						$title = '';
						$ultimaFecha = $keyFecha;
						$detalleAplicaciones[$keyArt][$keyFecha][$keyIdo] = array();
						foreach($rondas as $keyRonda=>$datosRonda)//array que recorre las rondas y verifica la existencia
						{
							if(!isset($aplicacionesDiarias[$keyArt][$keyFecha][$keyRonda]))
								$aplicacionesDiarias[$keyArt][$keyFecha][$keyRonda] = array();
							if(array_key_exists($keyRonda, $datos))
							{
								$hayAplicaciones = true;
								$dosis = $datos[$keyRonda]['Dosis'];
								$unidad = $datos[$keyRonda]['Unidad'];
								$dosisDia ++; //aplicaciones por dias.
								if(!isset($detalleAplicaciones[$keyArt][$keyFecha][$keyIdo][$datos[$keyRonda]['Dosis']]['numero']))
									$detalleAplicaciones[$keyArt][$keyFecha][$keyIdo][$datos[$keyRonda]['Dosis']]['numero'] = 0;
								$detalleAplicaciones[$keyArt][$keyFecha][$keyIdo][$datos[$keyRonda]['Dosis']]['numero']++;
								$detalleAplicaciones[$keyArt][$keyFecha][$keyIdo][$datos[$keyRonda]['Dosis']]['unidad']=$unidad;
								$aplicacionesDiarias[$keyArt][$keyFecha][$keyRonda]['Detalle'] .= "{$dosis} ({$unidad})";
							}else
								$aplicacionesDiarias[$keyArt][$keyFecha][$keyRonda]['Detalle'] .= "&nbsp;";
						}

						if($fechaActual=='')
							$fechaActual = $keyFecha;
						if(!$hayCambio)
						{
							$cambiosConsolidado = colorCambio($fechaActual,$keyFecha, $keyIdo, $keyArt);
							$hayCambio = $cambiosConsolidado['cambio'];
						}
						$fechaActual = $keyFecha;

					}//FOR QUE RECORRE LOS IDOS DEL ARTICULO APLICADO EN LA FECHA
					foreach($rondas as $keyRonda=>$datosRonda)//array que recorre las rondas y verifica la existencia
					{
						$divDetalle .= "<td>{$aplicacionesDiarias[$keyArt][$keyFecha][$keyRonda]['Detalle']}</td>";
					}
					$total += $dosisDia;
					$divDetalle .= "<td align='center'><A onclick='abrirVentana(\"../procesos/generarKardex.php?wemp_pmla={$wemp_pmla}&waccion=b&whistoria={$whis}&wingreso={$wing}&wfecha={$keyFecha}&editable=off&et=on\")'><font color='blue'>Ir</font></a></td>";
					$divDetalle .= "</tr>";

					//ACÁ SE VA A ARMAR EL DETALLE QUE SE MUESTRA INICIALMENTE EN PANTALLA, DEBIDO A QUE DIFERENTES IDOS SE PUEDEN APLICAR EL MISMO DIA.
					if($hayAplicaciones)
					{
						$numeroDia++;
						$hayAplicaciones=false;
					}
					$detalle = "<table>";
					$detalle .= "<tr class=encabezadotabla ><td colspan=5>{$keyFecha}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dia Tratamiento: {$numeroDia}</td></tr>";
					$detalle .= "<tr class=encabezadotabla ><td align=center>Dosis</td><td align=center>Aplis</td><td align=center>Frecu</td><td align=center>Via</td><td align=center>Dosis M&aacute;ximas</td></tr>";
					$m = 0;
					foreach($detalleAplicaciones[$keyArt][$keyFecha] as $ido_aux=>$idos)
					{
						$m++;
						if(is_int($m/2))
							$wcla = "fila1";
							else
							$wcla = "fila2";
						foreach($idos as $keyDosis=>$value)
						{
							$frecuenciaMostrada = $frecuencias[$frecuenciasPorDia[$keyArt][$keyFecha][$ido_aux]];
							$dosisMostrada = $DosisPorDia[$keyArt][$keyFecha][$ido_aux];
							$viaMostrada = $vias[$viasPorDia[$keyArt][$keyFecha][$ido_aux]];
							if($dosisMostrada=='')
								$dosisMostrada = "No Aplica";
							$detalle .= " <tr class={$wcla}><td align=center>{$keyDosis}({$value['unidad']})</td><td align=center>{$value['numero']}</td><td align=center>{$frecuenciaMostrada}</td><td align=center>{$viaMostrada}</td><td align=center>{$dosisMostrada}</td>";
						}
					}
					$detalle .= "</table>";
					//FIN DE ARMADO DE DETALLE PARA ESTA FECHA
					$color = "background-color:yellow; cursor:pointer;";
					$class = "class='msg_tooltip'";

					//SE DA COLOR DEPENDIENDO DEL TIPO DE CAMBIO QUE SE HA PRESENTADO ENTRE UN DIA Y OTRO.
					if($cambiosConsolidado['dosis']['value'])
					{
						if($verde == '#21EF35')
							$verde = 'yellow';
							else
							 $verde = '#21EF35';
						$colorN = "background-color:{$verde}; cursor:pointer;";
						$nuevoComportamiento = true;
					}
					if($cambiosConsolidado['frecuencia']['value'])
					{
						if($naranja == '#EFA021')
							$naranja = '#EF7D21';
							else
							 $naranja = '#EFA021';
						$colorN = "background-color:{$naranja}; cursor:pointer;";
						$nuevoComportamiento = true;
						//$frecuenciaMostrada = $cambiosConsolidado['frecuencia']['nuevaFrecuencia'];
					}
					if($cambiosConsolidado['dosis']['value'] && $cambiosConsolidado['frecuencia']['value'])
					{
						if($azul == '#C2C2C2') //segundo tipo de verde
							$azul = '#A09F9F';
							else
							 $azul = '#C2C2C2';
						$colorN = "background-color:{$azul}; cursor:pointer;";
						$nuevoComportamiento = true;
					}
					if(!isset($nuevoComportamiento))
					{
						$color = "background-color:yellow; cursor:pointer;";
					}else
						{
							$color = $colorN;
							if($nuevoComportamiento==true)
								$nuevoComportamiento = false;
						}

					$style = "style='{$color} '";
					$title = "title='{$detalle}'";
					$fechas .= "<td align='center' width='30' {$class} {$title} {$style} onclick='mostrarDetalleAntibioticoDia(\"{$keyArt}\", \"{$keyFecha}\");'>&nbsp;&nbsp;</td>";
					$fechaFinalizacion = $keyFecha;
				}else //SI NO HAY APLICACION PARA ESE DIA.
					{
						//se buscará si el medicamento tiene alguna suspencion.  class='suspendido'
						$classSuspendido = '';
						$controlSuspendido = calcularDiferenciaDias($keyFecha, $fechaInicioArt);
						$aux = '';
						$deta ='';
						if(array_key_exists($keyFecha, $suspendidosPorDia[$keyArt]))
						{
							$p = 0;
							$contar = true;
							foreach($idosPorArticulo[$keyArt][$keyFecha] as $idoAux2=>$value)
							{
								if($suspendidosPorDia[$keyArt][$keyFecha][$idoAux2]=='on')
								{
									if($contar)
									{
										$numeroDia++;
										$contar = false;
									}
									$fechaSuspencion = $keyFecha; //en caso de que si haya suspendidos
									$p++;
									$classSuspendido = "class='suspendido msg_tooltip' style='cursor:pointer;'";
									$deta .= detallarSuspencion($keyArt, $whis, $wing, $idoAux2, $keyFecha, $p);
								}
							}
							$encabezadoDeta = "<table>";
							$encabezadoDeta .= "<tr class=encabezadotabla ><td colspan=4>{$keyFecha}&nbsp;&nbsp;(SUSPENDIDO)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Dia Tratamiento: {$numeroDia}</td></tr>";
							$encabezadoDeta .= "<tr class=encabezadotabla ><td align=center>Dosis</td><td align=center>Frecu</td><td align=center>Via</td><td align=center>Dosis M&aacute;ximas</td></tr>";
							if($fechaSuspencion != '')
							{
								$deta = $encabezadoDeta."".$deta;
								$deta .= "</table>";
							}
						}
						$fechas .= "<td width='30' {$classSuspendido} title = '{$deta}'>&nbsp;&nbsp;&nbsp;</td>";
					}

			} //FIN DE FOR DE FECHAS.
			  if($fechaSuspencion != '' and compararFechas($fechaSuspencion,$fechaFinalizacion))
				$fechaFinalizacion = 'Suspendido';
			  $divDetalleEncabezado = "<div id='{$keyArt}' style='display:none; cursor:default; background:none repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'><center><table id='tbl_{$keyArt}'>"; //variable que arma la tabla detalle de cada medicamento
			  $divDetalleEncabezado .= "<tr class='encabezadotabla'><td colspan=8>ARTICULO: {$nombreArticulo}({$articulo})</td><td colspan=3>FECHA INICIO: {$fechaInicioArt}</td><td colspan=3>FECHA FINAL: {$fechaFinalizacion}</td></tr>";
			  $divDetalleEncabezado .= $trRondas;
			  $divDetalle = $divDetalleEncabezado."".$divDetalle;
			  //esta parte es el encabezado de cada registro en pantalla, lo armamos al final para poder obtener la última fecha de aplicacion.
			  $datosRegistro  = "<tr class='{$wclass}' id='tr_{$articulo}'><td colspan=1 style='cursor:pointer' title='ver Detalle' nowrap='nowrap' onclick='mostrarDetalleAntibiotico(\"{$keyArt}\");'>{$nombreArticulo}({$articulo})</td>";//articulo
			  $datosRegistro .= "<td colspan=1 style='cursor:pointer' title='ver Detalle' align='center' nowrap='nowrap' onclick='mostrarDetalleAntibiotico(\"{$keyArt}\");'>{$fechaInicioArt}</td>";//fecha de inicio
			  $datosRegistro .= "<td colspan=1 style='cursor:pointer' title='ver Detalle' nowrap='nowrap' onclick='mostrarDetalleAntibiotico(\"{$keyArt}\");'>{$fechaFinalizacion}</td>";
			  //$datosRegistro .= "<td style='cursor:pointer' title='ver Detalle' onclick='mostrarDetalleAntibiotico(\"{$keyArt}\");'>&nbsp</td>";

			 //cierre del detalle asociado a este articulo.
			 $divDetalle .=  "</table></center>";
			 $divDetalle .= "<center><table>";
			 $divDetalle .= "<tr><td>&nbsp;";
			 $divDetalle .= "</td></tr>";
			 $divDetalle .= "<tr><td>";
			 $divDetalle .= "<input type='button' value='Cerrar' onclick='ocultarDiv(\"{$keyArt}\");'>";
			 $divDetalle .= "</td></tr>";
			 $divDetalle .= "</table></center></div>";
			 $todosDetalles .= $divDetalle;

			 $fechas .= "<td align='center' style='cursor:pointer' title='ver Detalle' nowrap='nowrap' onclick='mostrarDetalleAntibiotico(\"{$keyArt}\");'>{$total}</td>";
			 $fechas .= "</tr>";
			 $table .= $datosRegistro."".$fechas;

		}//FIN DEL FOREACH DEL CONSOLIDADO.

		$table .= "</table></center>";
		$table .= "<br>";
		$table .= "<br>";
		$table .= "<div align='center'><font color='blue' style='cursor:pointer' onclick='retornar()'><b>RETORNAR</b></font></div>";
		$table .= $todosDetalles;
	}
	echo $table;
	/*$data = array( 'table'=>$table );
	echo json_encode($data);*/
	return;
}
}
?>
<html>
<head>
	<title>Reporte AntibioticoTerapia</title>
	<style>
		#tooltip{
			color: #2A5DB0;
			font-family: Arial,Helvetica,sans-serif;
			position:absolute;
			z-index:3000;
			border:1px solid #2A5DB0;
			background-color:#FFFFFF;
			padding:5px;
			opacity:1;}
		#tooltip div{margin:0; width:250px}

		#tds_dias{
			<!--cursor: pointer;-->
		}

	</style>

</head>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<!-- FUNCIONES JAVASCRIPT-->
<script>
//variables globales;
var wbd;
var whoy;
var divRespuesta;
var divRes;
var hisActual = '';
var ingActual = '';
var nombre = '';
var habitacion = '';
//fin variables globales;
$(document).ready(function(){
	wbd = $("#wbd").val();
	whoy = $("#whoy").val();
	divRes = jQuery($("#divResultados"));
	if( $("#accesoDirecto").val() == "on" ){
		$("#divResultados").html('');
		verDetallePaciente('manual');
		setTimeout(function(){
			window.close();
		}, 600);
	}
});

function validar(e)
{
   var esIE=(document.all);
   var esNS=(document.layers);
   var tecla=(esIE) ? event.keyCode : e.which;
   if (tecla==13){
	setTimeout(function(){
	$(".calendar").hide();
	},20);
	return true;
   }
   else return false;
}

function buscarNombre(whis, wing)//funcion que hace la peticion ajax que busca el nombre y la habitacion cuando se hace la busqueda por historia e ingreso
{
	$.ajax({
			url:	"rep_antibioticoTerapia.php",
			async:	false,
			type: 	"POST",
			data: {
					queryAjax: 'buscarNombre',
					whis: whis,
					wing: wing,
					wbasedato: wbd
				  },
			success: function(data)
					{
						nombre = data.nombre;
						habitacion = data.habitacion;
					},
			dataType:	"json"
	});
	return(nombre);
}

function buscarCcos()//hace petición ajax que busca los centros de costos hospitalarios.
{
	$.post(
		"rep_antibioticoTerapia.php",
		{
			queryAjax:		'generarCco',
			wbasedato:		wbd
		},
		function(data)
		{
			if(data.select!='')
				{
					alert(data.select);
				}
		},
		"json"
	);
}

//NO SE ESTÁ USANDO!!!!!!!!!!!!!!!!!
function pedirDatos(id, chk) //funcion que modifica la UI para que el usuario ingrese los datos que desee para parametrizar la busqueda.
{
	check = jQuery("#"+chk);
	$("#tblMenuPpal input:checkbox").each(function(){
		if(($(this).is(":checked"))&&($(this).attr("id")!=check.attr("id")))
		{
			$(this).removeAttr("checked");
			divaux = ($(this).attr("value"));
			$("#div"+divaux).hide();
		}
	})
	if(check.is(":checked")){
			$("#tipoBusqueda").val(id);
			$("#div"+id).show('slow');
			$("#menuFechas").show('slow');
			$("#divBuscar").show('slow');
		}
		else{
			$("#div"+id).hide();
			$("#menuFechas").hide();
			$("#tipoBusqueda").val('');
			$("#whis").val('');
			$("#wing").val('');
			$("#fechaini").val(whoy);
			$("#fechafin").val(whoy);
			$("#divBuscar").hide();
			$("#divResultados").html('');
			$("#divBuscar").hide();
		}
}
// pedirDatos NO SE ESTÁ USANDO!!!!!!!!!!!

function actualizarFechaInicial()//funcion que hace la petición ajax para actualizar las fechas(ingreso y alta) que se van a mostrar por defecto cuando se busca por paciente
{
	historia = $("#whis").val();
	ingreso = $("#wing").val();
	if(historia=='' || ingreso=='')
	{
		return;
	}
	if(historia==hisActual && ingreso==ingActual)
	{
		return;
	}
	hisActual=historia;
	ingActual=ingreso;
	$.post(
		"rep_antibioticoTerapia.php",
		{
			queryAjax:  'actFechaInicial',
			wbasedato:	wbd,
			whis:	historia,
			wing:	ingreso,
			hoy:	whoy
		},
		function(data){
			if(data.fechaIni==''){
				alert('La datos ingresados no arrojaron resultados');
				return;
			}else
				{
					$("#fechaini").val(data.fechaIni);
					if(data.fechaFin!='')
						$("#fechafin").val(data.fechaFin);
				}
		},
		"json"
	);
}

function buscar()//verifica el tipo de busqueda y ejecuta la función correspondiente cuando se le dá click en buscar
{
	//accion = $("#tipoBusqueda").val();
	historia = $("#whis").val();
	ingreso = $("#wing").val();
	if(historia != '' && ingreso != '')
		accion = 'BusPaciente';
		else
			accion = 'BusCco';

	$("#divResultados").html('');
	if(accion=='BusPaciente')
	{
		verDetallePaciente('manual');
	}
	if(accion=='BusCco')
	{
		buscarPacientesCco();
	}
	return;

}

function buscarPacientesCco()//esta funcion es un puente para hacer blockui no tiene ninguna responsabilidad en el dominio del problema
{
	$.blockUI({ message: $('#msjEspere') });
	setTimeout(function(){ buscarPacientesCco2() }, 500);
}

function buscarPacientesCco2()//funcion que hace la petición ajax que lista los pacientes con antibioticos programados para el centro de costos y periodo de tiempo indicado
{
	centroCostos = $("#selCco").val();
	fecIni = $("#fechaini").val();
	fecFin = $("#fechafin").val();
	wemp = $("#wemp_pmla").val();
	agregar = 'no'; //agrega los pacientes en alta definitiva.
	adPacAd = jQuery($("#altDef"));
	if(adPacAd.is(":checked"))
	{
		agregar = 'si';
	}

	$.post(
		"rep_antibioticoTerapia.php",
		{
			queryAjax:  'buscarPacientesCco',
			wbasedato:	wbd,
			wcco:	centroCostos,
			addAd:	agregar,
			fecIni: fecIni,
			fecFin:	fecFin,
			wemp_pmla: wemp
		},
		function(data)
		{
			if(data.pacientes!='')
			{
				$("#divResultados").html(data.pacientes);
				//$("#divResultados").show('slow');
				$("#divResultados").show();
			}else
				{
					alert('No se encotraron pacientes con antibioticoterpia programada');
				}
			$.unblockUI();
		},
		"json"
	);
}

function verDetallePaciente(i)//funcion que hace el llamado ajax que busca el detalle de la antibiotico terapia dado un paciente de la lista
{
	if(i=='manual')
	{
		whis    = $("#whis").val();
		wing    = $("#wing").val();
		wfecing =  $("#fechaini").val();
		wfecegr =  $("#fechafin").val();
		wemp    = $("#wemp_pmla").val();
		buscarNombre(whis, wing);
		wnom    = nombre;
		whab    = habitacion;
		wdias   = '';
	}else
		{
			whis    = $("#his"+i).html();
			wing    = $("#ing"+i).html();
			wnom    = $("#nom"+i).html();
			wdias   = $("#dias"+i).html();
			whab    = $("#hac"+i).html();
			wemp    = $("#wemp_pmla").val();
			wfecing = $("#fechaIngreso"+i).val();
			wfecegr = $("#fechaEgreso"+i).val();
			$("#font"+i).attr('color','');
		}
	$.ajax({
		    url: "rep_antibioticoTerapia.php",
		  async: true,
         before: $.blockUI({ message: $('#msjEspere') }),
		   data:{
					queryAjax: 'detalleTerapia',
						 whis: whis,
						 wing: wing,
						 whab: whab,
						 wnom: wnom,
					wemp_pmla: wemp,
				wfechaIngreso: wfecing,
				 wfechaEgreso: wfecegr,
					wbasedato: wbd,
				wdiasEstancia: wdias
				},
		success:function(data)
				{
					if(data =='')
					{
						$.unblockUI();
						$("#whis").val('');
						$("#wing").val('');
						alert('no se han realizado aplicaciones a esta paciente');
						return;
					}
					$("#divPruebas").html(data);
					$("#divPruebas").show();
					$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
					setTimeout(function(){ cambiarPagina("sel") },50);
					$.unblockUI();
					divRes.hide();
					$("#divMenuPpal").hide();
				}
	});
}

function cambiarPagina(accion) //funcion que oculta las columnas de tal manera que se realice la paginación.
{
	paginas = $("#paginas").val();
	tamPagina = 30;
	factor = 0;
	paginador = jQuery("#paginaMostrada");
	pagina = jQuery("#td_pagina");
	//elaboración de limites
	switch(accion)
	{
		case 'ini':
			factor = 1;
			break;
		case 'ant':
			factor = (paginador.val()*1)-1;
			if(factor<1)
			{
				//alert('No hay paginas Anteriores');
				return;
			}
			break;
		case 'sig':
			factor = (paginador.val()*1)+1;
			if(factor>paginas)
			{
				//alert('No hay paginas Posteriores');
				return;
			}
			break;
		case 'fin':
			factor = paginas;
			break;
		case 'sel':
			factor = paginador.val();
			break;
	}
	paginador.val(factor);
	pagina.html("P&aacute;gina "+factor+ " de "+paginas);
	inicio = (tamPagina*factor)-tamPagina;
	fin = inicio + tamPagina;
	fila = 0;
	$("#tblDetalle tr").each(function(){
			tr = jQuery($(this));
			fila++;
			if(fila>2)
			{
				idTr = tr.attr('id');
				dia = 0;
				$("#"+idTr+" td").each(function(){
						columna = jQuery($(this));
						dia++;
						if(dia>3 && dia<=3+inicio)//recordar que tengo que mover tanto el inicio como el fin 6 posiciones
						{
							columna.hide();
						}
						if(dia>3+inicio && dia<=3+fin)
						{
							columna.show();
						}
						if(dia>3+fin)
						{
							columna.hide();
						}
					}
				)
			}
		}
	)
}

function retornar() //regresa al listado de pacientes encontrados por cco, despues de estar viendo un detalle de terapia.
{
	$("#divMenuPpal").show();
	divRes.show();
	$("#divPruebas").hide();
	historia = $("#whis").val('');
	ingreso = $("#wing").val('');
}

function mostrarDetalleAntibiotico(divId)//muestra el detalle de las aplicaciones de un antibiótico durante la estancia de un paciente en la clinica.
{
	//alert(divId);
	$.blockUI({ message: $("#"+divId),
					css: { left: '15%',
							top: '15%',
						  width: '45%',
						  height: '50%'
						 }
			  });

}

function mostrarDetalleAntibioticoDia(divId, fecha)// muestra el detalle de las aplicaciones de un antibiotica dado un dia
{
	//ocultar os tr que no interesan.
	filaFecha = 0;
	$("#tbl_"+divId+" tr").each(function(){
		tr = jQuery($(this));
		filaFecha++;
		if((filaFecha>2)&&(tr.attr('id')!=divId+'_'+fecha))
		{
			tr.hide()
		}else{
				tr.show();
			 }
	});

	mostrarDetalleAntibiotico(divId);
}

function ocultarDiv(divId)//oculta los detalles.
{
	$.unblockUI();
	setTimeout(function(){
							$("#tbl_"+divId+" tr").each(function(){
								tr = jQuery($(this));
								tr.show();
							});}, 500
			  );
}

function abrirVentana(dir)
{
	window.open(dir,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
}
</script>
<?php
include_once('root/comun.php');




$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
//FUNCIONES DEL MENÚ PRINCIPAL;
function generarSelectCco()
{
	global $wbasedato;
	$ccos = consultaCentrosCostos('ccohos');
	 $select = "<select id='selCco'>";
	     $select .=  "<option value='todos'>Todos</option>";
		foreach($ccos as $cco=>$dat)
		{
			$select .=  "<option value='{$dat->codigo}-{$dat->nombre}'>{$dat->codigo}-{$dat->nombre}</option>";
		}
	$select .=  "</select>";
	return($select);
}
function generarMenuPpal()
{
	global $fechaHoy;
	$fechaini=$fechaHoy;
	$fechafin=$fechaHoy;
	$select=generarSelectCco();
	echo "<div id='divMenuPpal'>";
		echo "</tr>";
	echo "</table></center>";
	echo "<br>";
	echo "<br>";
	echo "<div id='divBusCco'>";
	echo "<center><table id='tblBusCco' width='60%'>";
		echo "<tr class='encabezadotabla'><td align='center' colspan=2>SELECCIONE EL CENTRO DE COSTOS</td></tr>";
		echo "<tr class='fila2'><td>SELECCIONE CENTRO DE COSTOS</td><td>".$select."</td></tr>";
		//echo "<tr class='fila1'><td>INCLUIR PACIENTES DADOS DE ALTA</td><td><input type='checkbox' value='incluirAd' id='altDef'></td></tr>";
	echo "</table></center>";
	echo "</div>";

	/*echo "<div id='menuFechas'>";
	echo "<center><table id='tablaFechas' width='60%'>";
		echo "<tr id='fechas' align='center'>";
		echo "<td class='fila2' align='left'>FECHA DE INGRESO ENTRE:</td>";
		echo "<td id='filafec' class='fila2' algin=center colspan=3><b>Fecha inicial: ";
		echo campoFechaDefecto( "fechaini", $fechaini );
		echo " Fecha final: </b>";
		echo campoFechaDefecto( "fechafin", $fechafin );
		echo "</td>";
		echo "</tr>";
	echo "</table></center>";
	echo "</div>";*/

	echo "<div id='divBusPaciente'>";
	echo "<center><table id='tblBusPac' width='60%'>";
		echo "<tr class='encabezadotabla'><td colspan='4' align='center'>INGRESE LOS DATOS DEL PACIENTE</td></tr>";
		//echo "<tr class='fila1'><td align='left' colspan=4>C&Eacute;DULA: <input type='text' id='txtCedula'></td></tr>";
		echo "<tr class='fila1'><td>HISTORIA</td><td><input type='text' id='whis'></td><td>INGRESO</td><td><input type='text' id='wing' onkeypress='if (validar(event)) actualizarFechaInicial();' onBlur='actualizarFechaInicial();'></td></tr>";
	echo "</table></center>";
	echo "</div>";

	echo "<br>";
	echo "<div id='divBuscar' align='center'>";
		echo "<input type='button' value='BUSCAR' onclick='buscar()'>";
	echo "</div>";
	echo "</div>";
}

//MENÚ PPAL
if(!isset($_SESSION['user']))
	exit("error");
$wactualiz="2012-11-19";
$fechaHoy=date("Y-m-d");
echo "<form align='center'>";
encabezado("Reporte de Antibioticoterapia ", $wactualiz, "clinica");
echo "<input type='hidden' id='wbd' value='{$wbasedato}'>";
echo "<input type='hidden' id='tipoBusqueda' value=''>";
echo "<input type='hidden' id='whoy' value='{$fechaHoy}'>";
echo "<input type='hidden' id='wemp_pmla' value='{$wemp_pmla}'>";
if( ( isset($whis) and trim($whis) ) AND ( isset( $wing ) and trim($wing) ) ){
	echo "<input type='hidden' id='whis' value='{$whis}'>";
	echo "<input type='hidden' id='wing' value='{$wing}'>";
	echo "<input type='hidden' id='accesoDirecto' value='on'>";
}else{
	echo "<input type='hidden' id='accesoDirecto' value='off'>";
	//menu de busqueda
	generarMenuPpal();
}
//EN ESTE DIV SE VAN A MOSTRAR LOS RESULTADOS DE LAS DIFERENTES BUSQUEDAS
echo "<div id='divResultados' style='display:none'>";
echo "</div>";
echo "<br>";
echo "<br>";
echo "<br>";
echo "<div id='divPruebas' align='center' style='display:none'>";
echo "</div>";
//
echo "<center><table>";
	echo "<tr><td>&nbsp;</td></tr>";
	echo "<tr id='btnRetornar' style='display:none'><td align='center'><a href='rep_antibioticoTerapia.php?wemp_pmla=".$wemp_pmla."'>Retornar</a></td></tr>";
	echo "<tr><td>&nbsp;</td></tr>";
	echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='javascript:window.close();'></td></tr>";
echo "</table></center>";
echo "<div id='msjEspere' name='msjEspere' style='display:none;'>";
echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />";
echo "</div>";
echo "</form>";
?>
</html>
