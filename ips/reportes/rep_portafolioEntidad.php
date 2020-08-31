<?php
include_once("conex.php");
if( !isset($_SESSION['user']) && isset($peticionAjax) )
{
	if( $tipoPeticion == "json" ){
		$data = array( 'error'=>"error" );
		echo json_encode($data);
		return;
	}
    echo 'error';
    return;
}

if(isset($peticionAjax))
{
    if(isset($peticionAjax) && $peticionAjax == 'exportar_excel') // se debe diferenciar por los dos o por otro diferente a $accion puesto que desde talento.php ya esta seteado $accion
    {
		header("Content-type: application/ms-excel; name='excel'");
		header("Content-Disposition: attachment; filename=portafolio_".$empresa."_".date("Ymd").".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo $_POST['datos_a_enviar'];
        return;
    }
    $wfachos = $_SESSION['wfachos'];
}




$conexunix = odbc_connect('facturacion','informix','sco') or die("No se ralizo Conexion con Unix");


	function consultarUltimoPortafolio( $wcodigo )
	{

		global $conex;
		global $wfachos;
		$notasAnteriores = array();

		$query = " SELECT Pennum numeroId
					 FROM {$wfachos}_000004
					WHERE Pencod = '{$wcodigo}'
					  AND Penest = 'on'
					ORDER BY Pennum desc
					LIMIT 1";
		$rs    = mysql_query( $query, $conex );
		$row   = mysql_fetch_array( $rs );
		if( $row['numeroId']*1 >=1 ){
			$query2 = " SELECT Pdecco cco, Pdecod codigo, Pdenot nota, Pdeecc esCentroCostos
						 FROM {$wfachos}_000005
						WHERE Pdenum = '{$row['numeroId']}'
						  AND Pdeest = 'on'
						  AND pdenot != ''";
			$rs2 = mysql_query( $query2, $conex );
			while( $row2 = mysql_fetch_array( $rs2 ) ){
				if( $row2['esCentroCostos'] == "off" )
					$notasAnteriores[$row2['cco']][$row2['codigo']] = $row2['nota'];
					else
						$notasAnteriores['esCentroCostos'][$row2['cco']] = $row2['nota'];
			}
		}
		return( $notasAnteriores );
	}

	function verificarExistenciaMatrix() //FUNCION QUE VERIFICA SI UN PORTAFOLIO EXISTE EN MATRIX, DADOS UNA EMPRESA, UN AÑO Y UN MES.
	{
		global $wbasedato;
		global $conex;
		global $wfachos;
		global $wcodigo;
		global $wnit;
		global $tarifa;
		global $hoy;
		global $anio;
		global $mes;
		global $vigenciaInicio;
		global $vigenciaFinal;
		global $datosMatrix;
		global $numeroDocumento;
		global $ccosActivos;
		global $notasCentrosCostos;
		global $notasProductos;
		global $insertar;
		global $wbusMatrix;

		$encontrado = true ;
		$query = " SELECT Penfei fechaInicio, Penfef fechaFinal, Pennum numero
					 FROM {$wfachos}_000004
					WHERE Pencod = '{$wcodigo}'
					  AND Pennit = '{$wnit}'
					  AND Pentar = '{$tarifa}'
					  AND Penano = '{$anio}'
					  AND Penmes = '{$mes}'
					  AND Penest = 'on'";
		$rs = mysql_query( $query, $conex ) or die (mysql_error());
		$num = mysql_num_rows( $rs );
		if( $num <= 0 and $wbusMatrix != 'si')
		{
			$encontrado = false;
			$insertar = true;
			$query = " SELECT Penfei fechaInicio, Penfef fechaFinal, Pennum numero
						 FROM {$wfachos}_000004
						WHERE Pencod = '{$wcodigo}'
						  AND Pennit = '{$wnit}'
						  AND Pentar = '{$tarifa}'
						  AND Penfei <= '{$hoy}'
						  AND Penfef >= '{$hoy}'
						  AND Penest = 'on'
					   HAVING (MAX(Penano) AND MAX(Penmes))";
			$rs = mysql_query( $query, $conex ) or die (mysql_error());
			$num = mysql_num_rows( $rs );
		}

		if( $num>0 ) //EN CASO DE QUE EXISTA EL PORTAFOLIO CARGO EN ARREGLOS LOS PROCEDIMIENTOS, EXÁMENES Y CENTROS DE COSTOS QUE YA ESTÁN REGISTRADOS EN LA BASE DATOS.
		{
			$row = mysql_fetch_array( $rs );
			$vigenciaInicio = $row['fechaInicio'];
			$vigenciaFinal = $row['fechaFinal'];
			$numeroDocumento = $row['numero'];
			$queryAux = "SELECT Pdecod codigo, Pdecup cups, Pdetip tipo, Pdecco cco, Pdecon concepto
						   FROM {$wfachos}_000005
						  WHERE Pdenum = '{$row['numero']}'
						    AND Pdeecc = 'off'
							AND Pdeest = 'on'";
			$rsAux = mysql_query( $queryAux, $conex );
			while($rowAux = mysql_fetch_array($rsAux))
			{
				$datosMatrix["".trim($rowAux['codigo'])."-".trim($rowAux['cups'])."-".trim($rowAux['tipo'])."-".trim($rowAux['cco'])."-".$rowAux['concepto'].""] = '';
			}

			$queryAux = "SELECT Pdecco cco
						   FROM {$wfachos}_000005
						  WHERE Pdenum = '{$row['numero']}'
						    AND Pdeecc = 'on'
							AND Pdeest = 'on'";
			$rsAux = mysql_query( $queryAux, $conex );
			while($rowAux = mysql_fetch_array($rsAux))
			{
				$ccosActivos["{$rowAux['cco']}"] = '';
			}

			//notas
			$queryAux1 =" SELECT Pdecco centroCostos, Pdenot nota, Pdeecc escco, Pdecod codigo
							FROM {$wfachos}_000005
						   WHERE Pdenum = '{$row['numero']}'
							 AND Pdeest = 'on'
						   GROUP BY 1, 4, 2, 3";
			$rsAux1 = mysql_query( $queryAux1, $conex ) or die ( mysql_error() );
			while( $row = mysql_fetch_array( $rsAux1 ) )
			{
				if( $row['escco'] == 'on')
				{
					$notasCentrosCostos[$row['centroCostos']] = $row['nota'];
				}else
					{
						$notasProductos[trim($row['centroCostos'])."-".trim($row['codigo'])] = $row['nota'];
					}
			}
		}
		return( $encontrado );
	}

	function generarEncabezado( $numeroSgc, $nombreEntidad, $vigenciaInicio, $vigenciaFinal, $numeroTarifa, $tipo ) //$tipo INDICA SI EL REPORTE ESTÁ MOSTRANDO AMBULATORIO U HOSPITALARIOS.
	{
		global $conexunix;
		global $wtipoCcos;
		if( $wtipoCcos == "%")
			$filtro = "HOSPITALARIO Y AMBULATORIO";
			else{
				if( $wtipoCcos == "on")
					$filtro = "HOSPITALARIO";
					else
						$filtro = "AMBULATORIO";
			}
		$encabezado = "<tr align='center'>";
			$encabezado .= "<td colspan=2><img src='../../images/medical/root/clinica.jpg' width=120 heigth=76></td>";
			$encabezado .= "<td colspan=4><font size='4'><b>PORTAFOLIO DE {$nombreEntidad}</b></font><br>";
			$encabezado .= "NUMERO DE TARIFA {$numeroTarifa} <br>";
			$encabezado .= "TIPO DE PORTAFOLIO <b>{$filtro}</b> <br>";
			$encabezado .= "VIGENCIA TARIFA INICIO {$vigenciaInicio} <br> TERMINA {$vigenciaFinal}</td>";
			$encabezado .= "<td rowspan=2 colspan=2> FA-GC-01-02 <br>V.02</td>"; //
		$encabezado .= "<tr align='center'>";
			$encabezado .= "<td colspan=2>Gesti&oacute;n de la calidad</td><td colspan=4><b>Mercadeo</b></td>";
		$encabezado .= "</tr>";
		$encabezado .= "</tr>";
		$encabezado .= "<tr align='center' class='encabezadotabla'>";
			$encabezado .= "<td><b>CODIGO CLINICA</b></td>";
			$encabezado .= "<td><b>CODIGO CUPS</b></td>";
			$encabezado .= "<td><b>DESCRIPCION</b></td>";
			$encabezado .= "<td><b>CONCEPTO</b></td>";
			$encabezado .= "<td><b>NOTA</b></td>";
			$encabezado .= "<td><b>VALOR TARIFA ANTERIOR</b></td>";
			$encabezado .= "<td><b>FECHA ACTUALIZACION</b></td>";
			$encabezado .= "<td><b>VALOR TARIFA ACTUAL</b></td>";
		$encabezado .= "</tr>";
		return ($encabezado);
	}

	function buscarEnUnix() //FUNCION QUE BUSCA EL PORTAFOLIO EN MATRIX, SIEMPRE ES EL MAS RECIENTE PUESTO QUE NO SE ALMACENA HISTÓRICO
	{
		global $wcodigo;
		global $hayResultados;
		global $conexunix;
		global $tarifa;
		global $validarTipoCco;
		global $conexunix;
		global $wconcepto;
		global $wcentroCostos;
		global $wprocedimiento;
		global $wtipoCcos;
		global $wexamen;
		global $conceptos;
		global $centrosCostos;
		global $consolidado;
		global $todo;
		global $notasProductos;
		global $mostrarHabitaciones;
		$wconcepto = trim($wconcepto);
		$wprocedimiento = trim($wprocedimiento);

		$nombre = '';
		$condicionConcepto1 = '';
		$condicionConcepto2 = '';
		$condicionCentroCostos1 = '';
		$condicionCentroCostos2 = '';
		$condicionProcedimiento1 = '';
		$condicionExamen1 = '';

		if( trim($wconcepto) != '' )
		{
			$condicionConcepto1 = " AND exatarcon = '{$wconcepto}'";
			$condicionConcepto2 = " AND protarcon = '{$wconcepto}'";
		}
		if( trim($wcentroCostos) != '' )
		{
			$condicionCentroCostos1 = " AND exatarcco = '{$wcentroCostos}'";
			$condicionCentroCostos2 = " AND protarcco = '{$wcentroCostos}'";
		}
		if( trim($wprocedimiento) != '' )
		{
			$condicionProcedimiento1 = " AND protarpro = '{$wprocedimiento}'";
			(trim($wexamen == '')) ? $soloProcedimientos = true : $soloProcedimientos = false;
		}
		if( trim($wexamen) != '' )
		{
			$condicionExamen1 = " AND exatarexa = '{$wexamen}'";
			(trim($wprocedimiento == '')) ? $soloExamenes = true : $soloExamenes = false;
		}
		//query de examenes
		$queryAux1 =" SELECT exatarexa codigo, exatartar tarifa, exatarcon concepto, exatarvaa valorAnterior, exatarval valorActual, exatarcco centroCostos, exanom descripcion,
							 exaane cups, exatarfec fechaActualizacion, 'exa' as tipo
						FROM inexatar, inexa
						WHERE exatartar = '".trim($tarifa)."'
							  {$condicionExamen1}
							  {$condicionConcepto1}
							  {$condicionCentroCostos1}
						AND exacod = exatarexa
						AND exaact = 'S'
						AND exaane is not null
					  UNION ALL
					  SELECT exatarexa codigo, exatartar tarifa, exatarcon concepto, exatarvaa valorAnterior, exatarval valorActual, exatarcco centroCostos, exanom descripcion,
							 '' cups, exatarfec fechaActualizacion, 'exa' as tipo
						FROM inexatar, inexa
						WHERE exatartar = '".trim($tarifa)."'
							  {$condicionExamen1}
							  {$condicionConcepto1}
							  {$condicionCentroCostos1}
						AND exacod = exatarexa
						AND exaact = 'S'
						AND exaane is null";
		//query de procedimientos
		$queryAux2 =" SELECT protarpro codigo, protartar tarifa, protarcon concepto, protarvaa valorAnterior, protarval valorActual, protarcco centroCostos, pronom descripcion,
						     proane cups, protarfec fechaActualizacion, 'pro' as tipo
						FROM inprotar, inpro
					   WHERE protartar = '".trim($tarifa)."'
							{$condicionProcedimiento1}
							{$condicionConcepto2}
							{$condicionCentroCostos2}
						AND procod = protarpro
						AND proact = 'S'
						AND proane is not null
					  UNION ALL
					  SELECT protarpro codigo, protartar tarifa, protarcon concepto, protarvaa valorAnterior, protarval valorActual, protarcco centroCostos, pronom descripcion,
						     '' cups, protarfec fechaActualizacion, 'pro' as tipo
						FROM inprotar, inpro
					   WHERE protartar = '".trim($tarifa)."'
							{$condicionProcedimiento1}
							{$condicionConcepto2}
							{$condicionCentroCostos2}
						AND procod = protarpro
						AND proact = 'S'
						AND proane is null";

		 if((!$soloProcedimientos and !$soloExamenes) or ($soloProcedimientos and $soloExamenes))
			$query1 =  $queryAux1."  UNION ALL ".$queryAux2;
			else
				{
					if($soloProcedimientos)
						$query1 = $queryAux2;
					if($soloExamenes)
						$query1 = $queryAux1;
				}
		 //query de las camas
		 $query2 = "   SELECT Tipcod codigo, Tiptar tarifa,  Tipcon concepto, Tipvaa valorAnterior, Tipval valorActual, 'HABITACIONES' centroCostos,  Tipdes descripcion,
							 'No Aplica' cups, Tipfec fechaActualizacion, 'hab' as tipo
						FROM intip
					   WHERE Tipact = 'S'
					     AND Tiptar = '".trim($tarifa)."'";

		 if($wtipoCcos != 'hab')
		 {
			if($wtipoCcos == '%' and $wconcepto == '' and $wprocedimiento == '')
			{
				$query = $query1." UNION ALL ".$query2;
				$mostrarHabitaciones = true;
				$todo = true;
			}else
				$query = $query1;
		 }else
			{
				$query = $query2;
				$mostrarHabitaciones = true;
			}
		  $query = $query." GROUP BY 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
							ORDER BY 6, 1 ";

		  $queryEfectuado = $query;
		  $resTar = odbc_exec($conexunix,$query);


		  while($row = odbc_fetch_row($resTar))
		  {
				$hayResultados = true;
				$exaCodigo = odbc_result($resTar,'codigo');
				$exaCups = odbc_result($resTar,'cups');
				$tarifa = odbc_result($resTar,'tarifa');
				$valorAnterior = odbc_result($resTar,'valorAnterior');
				$valorActual = odbc_result($resTar,'valorActual');
				$centroCostos = trim(odbc_result($resTar,'centroCostos'));
				$concepto = trim(odbc_result($resTar,'concepto'));
				$descripcion = odbc_result($resTar,'descripcion');
				$fechaActualizacion = odbc_result($resTar,'fechaActualizacion');
				$tipo = odbc_result($resTar,'tipo');
				$nota = '';
				//construccion de arreglos
				$consolidado[$centroCostos][$exaCodigo]['tarifa'] = $tarifa;
				$consolidado[$centroCostos][$exaCodigo]['descripcion'] = $descripcion;
				$consolidado[$centroCostos][$exaCodigo]['cups'] = $exaCups;
				if(!isset($consolidado[$centroCostos][$exaCodigo]['cantidadConceptos']))
					$consolidado[$centroCostos][$exaCodigo]['cantidadConceptos'] = 1;
					else
						$consolidado[$centroCostos][$exaCodigo]['cantidadConceptos'] = $consolidado[$centroCostos][$exaCodigo]['cantidadConceptos'] + 1 ;
				$consolidado[$centroCostos][$exaCodigo]['conceptos'][$concepto]['valorAnterior'] = $valorAnterior;
				$consolidado[$centroCostos][$exaCodigo]['conceptos'][$concepto]['valorActual'] = $valorActual;
				$consolidado[$centroCostos][$exaCodigo]['conceptos'][$concepto]['fechaActualizacion'] = $fechaActualizacion;
				$consolidado[$centroCostos][$exaCodigo]['conceptos'][$concepto]['tipo'] = $tipo;

				if(array_key_exists( "".trim($centroCostos)."-".trim($exaCodigo)."", $notasProductos))
				 $nota = $notasProductos["".trim($centroCostos)."-".trim($exaCodigo).""];

				$consolidado[$centroCostos][$exaCodigo]['nota'] = $nota;

				$centrosCostos[$centroCostos]='';
				$conceptos[$concepto]='';
		  }
	}

	function buscarEnMatrix() //FUNCION QUE BUSCA UN PORTAFOLIO EN MATRIX, LA ÚLTIMA VERSIÓN GUARDADA EN EL AÑO Y MES DETERMINADOS PARA LA BUSQUEDA.
	{
		global $wcodigo;
		global $hayResultados;
		global $conex;
		global $wfachos;
		global $tarifa;
		global $validarTipoCco;
		global $wconcepto;
		global $wcentroCostos;
		global $wprocedimiento;
		global $wtipoCcos;
		global $wexamen;
		global $conceptos;
		global $notasCentrosCostos;
		global $notasProductos;
		global $centrosCostos;
		global $consolidado;
		global $todo;
		global $anio;
		global $mes;
		global $mostrarHabitaciones;
		global $usuario;
		$wconcepto = trim($wconcepto);
		$wprocedimiento = trim($wprocedimiento);

		$nombre = '';
		$condicionConcepto1 = '';
		$condicionConcepto2 = '';
		$condicionCentroCostos1 = '';
		$condicionCentroCostos2 = '';
		$condicionProcedimiento1 = '';
		$condicionExamen1 = '';

		if( trim($wconcepto) != '' )
		{
			$condicionConcepto1 = " AND Pdecon = '{$wconcepto}'";
		}
		if( trim($wcentroCostos) != '' )
		{
			$condicionCentroCostos1 = " AND Pdecco = '{$wcentroCostos}'";
		}
		if( trim($wprocedimiento) != '' )
		{
			$condicionProcedimiento1 = " AND Pdecod = '{$wprocedimiento}' AND Pdetip = 'pro' ";
			(trim($wexamen == '')) ? $soloProcedimientos = true : $soloProcedimientos = false;
		}
		if( trim($wexamen) != '' )
		{
			$condicionExamen1 = " AND Pdecod = '{$wexamen}' AND Pdetip = 'exa' ";
			(trim($wprocedimiento == '')) ? $soloExamenes = true : $soloExamenes = false;
		}
		if($wtipoCcos != 'hab')
		 {
			if($wtipoCcos == '%' and $wconcepto == '' and $wprocedimiento == '')
			{
				$mostrarHabitaciones = true;
				$todo = true;
			}
		 }else
			{
				$mostrarHabitaciones = true;
			}
		$queryAux1 =" SELECT Pdecod codigo, Pentar tarifa, Pdecon concepto, Pdevaa valorAnterior, Pdevac valorActual, Pdecco centroCostos, Pdedes descripcion,
							 Pdecup cups, Pdefac fechaActualizacion, Pdetip tipo, Pdenot nota
						FROM {$wfachos}_000004, {$wfachos}_000005
					   WHERE Pencod = '".trim($wcodigo)."'
						 AND Penano = '{$anio}'
						 AND Penmes = '{$mes}'
						 AND Pdenum = Pennum
						 AND Pdeest = 'on'
						 AND Penest = 'on'
						 AND Pdeecc = 'off'
						 {$condicionExamen1}
						 {$condicionProcedimiento1}
						 {$condicionConcepto1}
						 {$condicionCentroCostos1}
					   GROUP BY 6, 1, 3, 11
					   ORDER BY 6, 1 ";
		$rsAux1 = mysql_query( $queryAux1, $conex );
		while( $row = mysql_fetch_array( $rsAux1 ) )
		{
			$hayResultados = true;
			$codigo = $row['codigo'];
			$exaCups = $row['cups'];
			$tarifa = $row['tarifa'];
			$valorAnterior = $row['valorAnterior'];
			$valorActual = $row['valorActual'];
			$centroCostos = $row['centroCostos'];
			$concepto = $row['concepto'];
			$descripcion = $row['descripcion'];
			$fechaActualizacion = $row['fechaActualizacion'];
			$nota = $row['nota'];
			$tipo = $row['tipo'];
			//construccion de arreglos
			$consolidado[$centroCostos][$codigo]['tarifa'] = $tarifa;
			$consolidado[$centroCostos][$codigo]['descripcion'] = $descripcion;
			$consolidado[$centroCostos][$codigo]['cups'] = $exaCups;
			if(!isset($consolidado[$centroCostos][$codigo]['cantidadConceptos']))
				$consolidado[$centroCostos][$codigo]['cantidadConceptos'] = 1;
				else
					$consolidado[$centroCostos][$codigo]['cantidadConceptos'] = $consolidado[$centroCostos][$codigo]['cantidadConceptos'] + 1 ;
			$consolidado[$centroCostos][$codigo]['conceptos'][$concepto]['valorAnterior'] = $valorAnterior;
			$consolidado[$centroCostos][$codigo]['conceptos'][$concepto]['valorActual'] = $valorActual;
			$consolidado[$centroCostos][$codigo]['conceptos'][$concepto]['fechaActualizacion'] = $fechaActualizacion;
			$consolidado[$centroCostos][$codigo]['conceptos'][$concepto]['tipo'] = $tipo;
			$consolidado[$centroCostos][$codigo]['nota'] = $nota;
			$consolidado[$centroCostos][$codigo]['tipo'] = $tipo;

			$centrosCostos[$centroCostos]='';
			$conceptos[$concepto]='';
		}

		//notasCentrosCostos
		$queryAux1 =" SELECT Pdecco centroCostos, Pdenot nota
						FROM {$wfachos}_000004, {$wfachos}_000005
					   WHERE Pencod = '".trim($wcodigo)."'
						 AND Penano = '{$anio}'
						 AND Penmes = '{$mes}'
						 AND Pdenum = Pennum
						 AND Pdeest = 'on'
						 AND Penest = 'on'
						 AND Pdeecc = 'on'
						 {$condicionExamen1}
						 {$condicionProcedimiento1}
						 {$condicionConcepto1}
						 {$condicionCentroCostos1}";
		$rsAux1 = mysql_query( $queryAux1, $conex ) or die ( mysql_error() );
		while( $row = mysql_fetch_array( $rsAux1 ) )
		{
			$notasCentrosCostos[$row['centroCostos']] = $row['nota'];
		}

		return;
	}

	function generarPortafolio() //FUNCION QUE DECIDE A DONDE VA A BUSCAR EL PORTAFOLIO, QUE VA A PINTAR Y QUE VA A ACTUALIZAR.
	{
		global $wbasedato;
		global $conex;
		global $wfachos;
		global $wemp_pmla;
		global $wcodigo;
		global $wnit;
		global $hoy;
		global $hora;
		global $anio;
		global $mes;
		global $dia;
		global $hayResultados;
		global $tarifa;
		global $fechaInicio;
		global $validarTipoCco;
		global $wconcepto;
		global $wcentroCostos;
		global $tablaResultado;
		global $wtipoCcos;
		global $wexamen;
		global $centrosCostos;
		global $conceptos;
		global $consolidado;
		global $encontrado;
		global $mostrarHabitaciones;
		global $soloProcedimientos;
		global $soloExamenes;
		global $crearEnMatrix;
		global $todo;
		global $existeEnMatrix;
		global $conexunix;
		global $wbusMatrix;
		global $numeroDocumento;
		global $notasCentrosCostos;
		global $notasProductos;
		global $proExaMatrix;
		global $datosMatrix;
		global $ccosActivos;
		global $insertar;
		global $vigenciaInicio;
		global $vigenciaFinal;
		global $wpermisos;
		global $usuario, $caracteres, $caracteres2;
		$notasAnteriores = array();

		if($wbusMatrix == 'si')
		{
			if($existeEnMatrix)
			{
				buscarEnMatrix();
				$crearEnMatrix = false;
			}
			else{
					return;
				}
		}else
			{
				$notasAnteriores = consultarUltimoPortafolio( $wcodigo );
				buscarEnUnix();
				if( !$existeEnMatrix )
				{
					$crearEnMatrix = true;
				}else
					{
						if($todo)
						{
							$queryDelete = "DELETE
											  FROM {$wfachos}_000005
											 WHERE Pdenum = '{$numeroDocumento}'";
							$rsDelete = mysql_query( $queryDelete, $conex );
							$queryDelete = "DELETE
											  FROM {$wfachos}_000004
											 WHERE Pennum = '{$numeroDocumento}'";
							$rsDelete = mysql_query( $queryDelete, $conex );
							$crearEnMatrix = true;
						}else
							{
								$crearEnMatrix = false;
							}
					}
			}

		//SE BUSCAN LOS VALORES DE LOS CENTROS DE COSTOS Y LOS CONCEPTOS, TAMBIEN SE CREA EL ENCABEZADO DEL PORTAFOLIO EN MATRIX EN CASO DE SER NECESARIO.
		if($hayResultados)
		 {
			if( $crearEnMatrix AND trim( $wbusMatrix ) == 'no' AND $todo)//SE CREA EL ENCABEZADO SI ES NECESARIO DE LOS DOCUMENTOS
			{
				$query_aux =  "INSERT INTO {$wfachos}_000004 ( Medico, Fecha_data, Hora_data, Pencod, Pennit, Pentar, Penfei, Penfef, Penano, Penmes, Penest, Penfem, Penusu, Seguridad )
								  VALUES ( 'fachos', '".$hoy."' , '".$hora."' , '".trim($wcodigo)."', '".trim($wnit)."', '{$tarifa}','{$vigenciaInicio}','{$vigenciaFinal}','{$anio}','{$mes}','on','{$hoy}','{$usuario}','{$usuario}')";
				$rsaux = mysql_query( $query_aux, $conex ) or die ( mysql_error() );
				$idPortafolio = mysql_insert_id();
				$numeroDocumento = $idPortafolio;
			}

			foreach($centrosCostos as $keyCco=>$datos)//SE LLENA EL ARRAY DE LOS CENTROS DE COSTOS CON LOS DATOS APROPIADOS
			{
				$encontrado = false;
				$query = "SELECT Cconom nombre, Ccohos hospitalario
							FROM {$wbasedato}_000011
						   WHERE Ccocod = '{$keyCco}'
							 AND Ccoest = 'on'";
				$rs = mysql_query( $query, $conex ) or die ( mysql_error() );
				while( $row = mysql_fetch_array($rs) )
				{
					$encontrado = true;
					$centrosCostos[$keyCco]['nombre'] = $row['nombre'];
					$centrosCostos[$keyCco]['hospitalario'] = $row['hospitalario'];
				}
				if(!$encontrado)
				{
					$query = "SELECT Cconom nombre, Ccotip tipo
								FROM costosyp_000005
							   WHERE Ccocod = '{$keyCco}'";
					$rs = mysql_query( $query, $conex ) or die ( mysql_error() );
					while( $row = mysql_fetch_array($rs) )
					{
						$encontrado = true;
						$centrosCostos[$keyCco]['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
						( $row['tipo'] == "H") ? $row['hospitalario'] = "on" : $row['hospitalario'] = "off";
						$centrosCostos[$keyCco]['hospitalario'] = $row['hospitalario'];
					}
				}
			}

			if($mostrarHabitaciones and trim($wcentroCostos) == '')//SE LLENA EL ARRAY DE LAS HABITACIONES CON VALORES POR DEFECTO
			{
				$centrosCostos['HABITACIONES']['nombre'] = 'ESTANCIAS';
				$centrosCostos['HABITACIONES']['hospitalario'] = 'NA';
			}

			foreach($conceptos as $keyConcepto=>$datos) //SE LLENA EL ARRAY DE CONCEPTOS
			{
				$encontrado = false;
				$query = "SELECT Connom nombre
							FROM facon
						   WHERE Concod = '{$keyConcepto}'
							 AND Conact = 'S'";
				$rs = odbc_exec($conexunix,$query);
				while($row = odbc_fetch_row($rs))
				{
					$conceptos[$keyConcepto] = odbc_result($rs,'nombre');
					$encontrado = true;
				}
				if(!$encontrado)
				{
					unset($conceptos[$keyConcepto]);
				}
			 }
		 }

		//SE PINTA EL PORTAFOLIO EN PANTALLA, INDEPENDIENTEMENTE DE DONDE VIENE( MATRIX O UNIX )
		 $registros = '';
		 $seccion = 0;
		 foreach( $centrosCostos as $keyCco=>$datosCco)
		 {
			if( (!$validarTipoCco) or ($datosCco['hospitalario']== $wtipoCcos) or ($mostrarHabitaciones and $datosCco['hospitalario'] == 'NA'))
			{
				$notaCco = '';
				if(array_key_exists($keyCco, $notasCentrosCostos))
					$notaCco = utf8_encode($notasCentrosCostos[$keyCco]);
					if( trim( $notaCco )=="" ) $notaCco = $notasAnteriores['esCentroCostos'][$keyCco];
				if( $todo and $wbusMatrix == 'no' )//INSERCIONES EN MATRIX
				{
					$query_aux = "INSERT INTO {$wfachos}_000005 ( Medico, Fecha_data, Hora_data,  Pdenum,  Pdecod,  Pdecup,  Pdedes,  Pdecco,  Pdeecc,  Pdecon,  Pdenot, Pdetip,  Pdevaa,  Pdevac,  Pdefac,  Pdeest,  Seguridad)
														 VALUES ( 'fachos', '".$hoy."' , '".$hora."' , '{$numeroDocumento}' , '',  '', '', '{$keyCco}', 'on','','{$notaCco}','','','','', 'on', '{$usuario}' ) ";
					$rsaux = mysql_query( $query_aux, $conex ) or die ( mysql_error() );

				}
				//ESTE CORRESPONDE A LOS TR DE ENCABEZADO DE CENTRO DE COSTOS
				$leer = "";
				($wpermisos == '1') ? $leer = "" : $leer = "readonly='readonly'";
				$seccion++;
				$registros .= "<tr>";
					$registros .= "<td class='fila2'>&nbsp;&nbsp;</td>";
					$registros .= "<td class='fila2'>&nbsp;&nbsp;</td>";
					$registros .= "<td class='encabezadotabla' id='cco_{$keyCco}' style='cursor:pointer' onclick='mostrarHijos(this.id)' mostrando='false'><font size=4><b>{$seccion}. {$datosCco['nombre']} - {$keyCco}</b></font></td>";
					$registros .= "<td class='fila2' align='center' colspan=1>&nbsp;&nbsp;</td>";
					$registros .= "<td class='fila2' ><textarea  rows='3' id='txt_cco_{$keyCco}' onblur='actualizarNota( this, \"{$keyCco}\", \"{$keyProducto}\", \"{$datosProducto['tipo']}\", \"{$mes}\", \"{$anio}\", \"on\", \"{$numeroDocumento}\" )' {$leer}>{$notaCco}</textarea></td>";
					$registros .= "<td class='fila2' align='center'>&nbsp;&nbsp;</td>";
					$registros .= "<td class='fila2' align='center'>&nbsp;&nbsp;</td>";
					$registros .= "<td class='fila2' align='center'>&nbsp;&nbsp;</td>";
				$registros .= "</tr>";
				if( !isset( $consolidado[$keyCco] ) ) $consolidado[$keyCco] = array();
				foreach ( $consolidado[$keyCco] as $keyProducto=>$datosProducto )
				{
					//EN CASO DE QUE EL ARTICULO TENGA MAS DE UN CONCEPTO ASOCIADO SE PINTAN LOS DATOS EN ACORDEAON POR LO TANTO LA PRIMERA FILA VA SIN DATOS DE CONCEPTO
					if( $consolidado[$keyCco][$keyProducto]['cantidadConceptos']>1 )
					{
						$leer = "";
						($wpermisos == '1') ? $leer = "" : $leer = "readonly='readonly'";
						$notaPdto = utf8_decode($datosProducto['nota']);
						if( trim( $notaPdto ) == "" or !isset( $notaPdto ) ) $notaPdto = $notasAnteriores[$keyCco][trim($keyProducto)];
						$registros .= "<tr class='fila1 cco_{$keyCco}' id='tr_{$keyCco}-".trim($keyProducto)."' style='display:none'>";
							$registros .= "<td align='center' style='cursor:pointer'>".trim($keyProducto)."</td>";
							$registros .= "<td align='center' style='cursor:pointer'>{$datosProducto['cups']}</td>";
							$registros .= "<td id='producto_{$keyCco}-".trim($keyProducto)."' style='cursor:pointer' onclick='mostrarHijos(this.id)' mostrando='false'><font size=2><b>{$datosProducto['descripcion']}</b></font></td>";
							$registros .= "<td align='center' colspan=1>&nbsp;&nbsp;</td>";
							$registros .= "<td><textarea  rows='5' class='txt_hijo{$keyCco}' id='txt_proc_{$keyCco}-{$keyProducto}' onblur='actualizarNota( this, \"{$keyCco}\", \"{$keyProducto}\", \"{$datosProducto['tipo']}\", \"{$mes}\", \"{$anio}\", \"off\", \"{$numeroDocumento}\" )' {$leer}>{$notaPdto}</textarea></td>";
							$registros .= "<td align='center'>&nbsp;&nbsp;</td>";
							$registros .= "<td align='center'>&nbsp;&nbsp;</td>";
							$registros .= "<td align='center'>&nbsp;&nbsp;</td>";
						$registros .= "</tr>";
					}
					foreach( $consolidado[$keyCco][$keyProducto]['conceptos'] as $keyConcepto=>$datos )
					{
						if(isset($conceptos[$keyConcepto]))
						{
							$notaPdto = utf8_decode($datosProducto['nota']);
							if( trim( $notaPdto ) == "" or !isset( $notaPdto ) ) $notaPdto = $notasAnteriores[$keyCco][trim($keyProducto)];
							if( $consolidado[$keyCco][$keyProducto]['cantidadConceptos']==1 )
							{		$notaPdto = utf8_decode($datosProducto['nota']);
									if( trim( $notaPdto ) == "" or !isset( $notaPdto ) ) $notaPdto = $notasAnteriores[$keyCco][trim($keyProducto)];
									$leer = "";
									($wpermisos == '1') ? $leer = "" : $leer = "readonly='readonly'";
									//SI EL ARTICULO TIENE UN SOLO CONCEPTO ASOCIADO ENTONCES SE PINTA DIRECTAMENTE TODOS LOS DATOS EN UNA SOLA FILA
									$registros .= "<tr class='fila1 cco_{$keyCco}' id='tr_{$keyCco}-".trim($keyProducto)."' style='display:none'>";
										$registros .= "<td align='center' style='cursor:pointer'>".trim($keyProducto)."</td>";
										$registros .= "<td align='center' style='cursor:pointer'>{$datosProducto['cups']}</td>";
										$registros .= "<td id='producto_{$keyCco}-".trim($keyProducto)."' style='cursor:pointer'><font size=2><b>{$datosProducto['descripcion']}</b></font></td>";
										$registros .= "<td nowrap='nowrap' >{$conceptos[$keyConcepto]} ({$keyConcepto})</td>";
										$registros .= "<td><textarea  rows='5' class='txt_hijo{$keyCco}' id='txt_proc_{$keyCco}-{$keyProducto}' onblur='actualizarNota( this, \"{$keyCco}\", \"{$keyProducto}\", \"{$datosProducto['tipo']}\", \"{$mes}\", \"{$anio}\", \"off\", \"{$numeroDocumento}\" )' {$leer}>{$notaPdto}</textarea></td>";
										$registros .= "<td align='center'>".number_format($datos['valorAnterior'], 0, ',' , '.')."</td>";
										$registros .= "<td align='center'>{$datos['fechaActualizacion']}</td>";
										$registros .= "<td align='center'>".number_format($datos['valorActual'], 0, ',', '.')."</td>";
									$registros .= "</tr>";

									if( $todo and $wbusMatrix == 'no' )//INSERCIONES EN MATRIX
									{
											$query_aux = "INSERT INTO {$wfachos}_000005 ( Medico, Fecha_data, Hora_data,  Pdenum,  Pdecod,  Pdecup,  Pdedes,  Pdecco,  Pdeecc,  Pdecon,  Pdenot, Pdetip, Pdevaa,  Pdevac,  Pdefac,  Pdeest,  Seguridad)
																				 VALUES ( 'fachos', '{$hoy}' , '{$hora}' , '".trim($numeroDocumento)."' , '".trim($keyProducto)."',  '".trim($datosProducto['cups'])."', '".trim($datosProducto['descripcion'])."', '".trim($keyCco)."', 'off', '".trim($keyConcepto)."' ,'{$notaPdto}', '".trim($datos['tipo'])."', '".trim($datos['valorAnterior'])."', '".trim($datos['valorActual'])."' , '".trim($datos['fechaActualizacion'])."', 'on', '{$usuario}' ) ";
											$rsaux = mysql_query( $query_aux, $conex );
									}

							}else
								 {
									//SE DETALLAN LOS CONCEPTOS POR ARTICULO Y CENTRO DE COSTOS, ESTE DETALLE INCLUYE LOS VALORES ANTERIORES Y ACTUALES.
									$registros .= "<tr class='fila2 producto_{$keyCco}-".trim($keyProducto)."' style='display:none'>";
										$registros .= "<td align='center' >&nbsp;&nbsp;</td>";
										$registros .= "<td align='center' >&nbsp;&nbsp;</td>";
										$registros .= "<td align='center' >&nbsp;&nbsp;</td>";
										$registros .= "<td nowrap='nowrap'>{$conceptos[$keyConcepto]} ({$keyConcepto})</td>";
										$registros .= "<td align='center' colspan=1>&nbsp;&nbsp;</td>";
										$registros .= "<td align='center'>".number_format( $datos['valorAnterior'], 0, ',','.' )."</td>";
										$registros .= "<td align='center'>{$datos['fechaActualizacion']}</td>";
										$registros .= "<td align='center'>".number_format($datos['valorActual'], 0,',','.' )."</td>";
									$registros .= "</tr>";

									if( $todo and $wbusMatrix == 'no' )//INSERCIONES EN MATRIX
									{
											$query_aux = "INSERT INTO {$wfachos}_000005 ( Medico, Fecha_data, Hora_data,  Pdenum,  Pdecod,  Pdecup,  Pdedes,  Pdecco,  Pdeecc,  Pdecon,  Pdenot, Pdetip, Pdevaa,  Pdevac,  Pdefac,  Pdeest,  Seguridad)
																					 VALUES ( 'fachos', '{$hoy}' , '".$hora."' , '".trim($numeroDocumento)."' , '".trim($keyProducto)."',   '".trim($datosProducto['cups'])."', '".trim($datosProducto['descripcion'])."', '".trim($keyCco)."', 'off', '".trim($keyConcepto)."' ,'{$notaPdto}','".trim($datos['tipo'])."', '".trim($datos['valorAnterior'])."', '".trim($datos['valorActual'])."' , '".trim($datos['fechaActualizacion'])."', 'on', '{$usuario}' ) ";
											$rsaux = mysql_query( $query_aux, $conex );
									}

								}
						}
					}
				}
			}
		 }
		 return($registros);
	}

//peticiones Ajax
if($peticionAjax == 'consultarPortafolio') //FUNCION QUE MUESTRA EN PANTALLA EL PORTAFOLIO CONSULTADO, ADEMAS PERMITE LA INTERACCIÓN CON EL USUARIO QUIEN GUARDARÁ LAS NOTAS QUE PREFIERA
{
	$nombreEntidad = "({$wcodigo}) {$wnombre}";
	$vigenciaInicio = "0000-00-00";
	$vigenciaFinal  = "0000-00-00";
	$tarifa = $wtarifa;
	$numeroSgc = "101";
	$tipo = 'HOSPITALIZACION';
	$wnit = trim($wnit);
	$hoy = date("Y-m-d");
	$hora = date( "H:i:s" );
	$hoyAux = explode("-", $hoy);
	$anio = $hoyAux[0];
	$mes = $hoyAux[1];
	$dia = $hoyAux[2];
	$datosConsulta = '';
	$queryEfectuado = '';
	$crearEnMatrix =  false;
	$insertar = false;
	$todo = false;
	$encontrado = false;
	$mostrarHabitaciones = false;
	$soloProcedimientos = false;
	$soloExamenes = false;
	$hayResultados = false;
	$validarTipoCco =false;
	$centrosCostos = array();
	$conceptos = array();
	$consolidado = array();
	$notasCentrosCostos = array();
	$notasProductos = array();
	$datosMatrix = array();
	$ccosActivos = array();
	$numeroDocumento = '';
	$caracteres = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","°");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","");

	if( $wbusMatrix == 'si' ){
		$anio = $wanio;
		$mes = $wmes;
	}

	if( $wtipoCcos != '%' )
		$validarTipoCco = true;

	if( trim($wtarifa)=='' )
	{
		///PRIMERO CONSULTAMOS LA TARIFA ASOCIADA A LA EMPRESA
		$query  ="SELECT Emptar, Empnom
					FROM Inemp, Intar
				   WHERE Empnit = '".trim($wnit)."'
					 AND Empcod = '".trim($wcodigo)."'
					 AND Empact = 'S'
					 AND Tarcod = Emptar
					 AND Taract = 'S'";

		$resTar = odbc_exec( $conexunix,$query );
		while($row = odbc_fetch_row($resTar))
		{
			$tarifa = odbc_result($resTar,'Emptar');
			$nombre = odbc_result($resTar,'Empnom');
		}
	}
	$existeEnMatrix = verificarExistenciaMatrix();
	$tablaResultado = "<div align='left' style='text-align:left;'>
							<span style='color:#999999;font-size:14pt;'>Resultado de la consulta:</span>
							<div id='div_exportar' style='display:block;text-align:left;'>
								<form action='rep_portafolioEntidad.php?form=&peticionAjax=exportar_excel&empresa=".$nombreEntidad."' method='post' target='_blank' id='FormularioExportacion'>
									<span style='color:#999999;'>Exportar</span>  <img width='28' height='14' border='0' src='../../images/medical/root/export_to_excel.gif' class='botonExcel' style='cursor:pointer;' />
									<input type='hidden' id='datos_a_enviar' name='datos_a_enviar' />
								</form>
							</div>
					    </div>";
	if( $wpermisos=='1' and $todo )// todo no existe en este punto, por lo tanto no se está habilitando para nadie, en caso de ser requerido basta con quitar la segunda condición, dejando únicamente la $wpermiso
	{
		$tablaResultado .= "<div align='right'>";
		$tablaResultado .= "<table><tr><td class='fila2' style='cursor:pointer' onclick='mostrarDiv();'><font color=blue>ACTUALIZAR VIGENCIA</font></td></tr></table>";
		$tablaResultado .= "</div>";
	}
	$tablaResultado .= "<table id='tbl_portafolio' border=1>";
	$tablaResultado .= generarEncabezado($numeroSgc, $nombreEntidad, $vigenciaInicio, $vigenciaFinal, $tarifa, $tipo);
	$tablaResultado .= generarPortafolio();
	$tablaResultado .= "</table>";

	$tablaResultado .= "<input type='hidden' name='numeroDoc' id='numeroDoc' value='{$numeroDocumento}'>";
	$tablaResultado .= "<input type='hidden' name='mes' 	  id='mes' value='{$mes}'>";
	$tablaResultado .= "<input type='hidden' name='anio'      id='anio' value='{$anio}'>";
	$tablaResultado .= "<input type='hidden' name='empresa'   id='empresa' value='{$wcodigo}'>";

	if(!$hayResultados)
		$tablaResultado = '';
	$data  = array('table'=>utf8_encode($tablaResultado), 'datosConsulta'=>utf8_encode($datosConsulta), 'mostrarGuardar'=>$todo);
	echo json_encode($data);
	return;
}

if( $peticionAjax == 'consultarEmpresas' )//CONSULTA LAS EMPRESAS QUE TIENEN UNA TARIFA X
{
	global $conex;
	global $wfachos;
	$tablaEmpresas = '';
	$query = "	SELECT Epscod codigo, Epsnit nit, Epsnom nombre "
			."	  FROM 	{$wbasedato}_000049 "
			."	 WHERE Epstar = '{$wtarifa}'"
			."	 ORDER BY Epsnom";

	$rs = mysql_query( $query,$conex );
	if(mysql_num_rows($rs) > 0)
	{
		$tablaEmpresas .= "<table>";
		$tablaEmpresas .= "<tr class='encabezadotabla'>";
			$tablaEmpresas .= "<td align='center'>CODIGO</td>";
			$tablaEmpresas .= "<td align='center'>NIT</td>";
			$tablaEmpresas .= "<td align='center'>NOMBRE</td>";
			$tablaEmpresas .= "<td align='center'>&nbsp;&nbsp</td>";
		$tablaEmpresas .= "</tr>";

		$i = 0;
		while( $row = mysql_fetch_array($rs) )
		{
			$codigo = $row['codigo'];
			$nit = $row['nit'];
			$nombre = $row['nombre'];
			$i++;

			($i%2==0) ? $wclass = 'fila1' : $wclass = 'fila2';

			$tablaEmpresas .= "<tr class='{$wclass}'>";
				$tablaEmpresas .= "<td align='center'>{$codigo}</td>";
				$tablaEmpresas .= "<td align='center'>{$nit}</td>";
				$tablaEmpresas .= "<td align='left'>{$nombre}</td>";
				$tablaEmpresas .= "<td align='center' style='cursor:pointer' onclick='verPortafolioEmpresaTarifa(\"{$codigo}, {$nit}, {$nombre}\")'><font color='blue'> Ver portafolio </font></td>";
			$tablaEmpresas .= "</tr>";
		}
		$tablaEmpresas .= "</table>";
	}
	$data = array('table'=>utf8_encode($tablaEmpresas));
	echo json_encode($data);
	return;
}

if( $peticionAjax == 'guardarPortafolio' ) //GUARDA EL PORTAFOLIO EN MATRIX (FUERA DE USO E INCOMPLETA)
{
	//echo $wnotas;
	$arregloNotas = array();
	$wnotas = str_replace("\\","", $wnotas);
	$arregloNotas = json_decode($wnotas, true);
	echo "<pre>";
		print_r($arregloNotas);
	echo "</pre>";
	return;
}

if( $peticionAjax == 'actualizarNota' ) //ACTUALIZA UNA NOTA CUANDO EL EVENTO DE ONBLUR SE EJECUTA
{
	if($wesCco == 'off')
		$filtroCodigo = " AND Pdecod = '{$wpdto}'";
		else
			$filtroCodigo = '';

	$query = "UPDATE {$wfachos}_000005
				 SET Pdenot = '{$wnota}'
			   WHERE Pdenum = '{$wnum}'
			     {$filtroCodigo}
			     AND Pdecco = '{$wcco}'
			     AND Pdeecc = '{$wesCco}'";
	$rs = mysql_query( $query,$conex );
	return;
}

if( $peticionAjax == 'actualizarVigencia')
{
	$query = "UPDATE {$wfachos}_000004
				 SET Penfei = '{$wfeci}',
					 Penfef = '{$wfecf}'
			   WHERE Pennum = '{$wnum}'
			     AND Penest = 'on'";
	$rs = mysql_query( $query, $conex );
	return;
}

?>
<html>
	<head>
		<title>
			PORTAFOLIO POR EMPRESA
		</title>
		<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	</head>
	<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
	<link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<!--<script src="../../../include/root/json2.js" type="text/javascript"></script>
	<script src="../../../include/root/toJson.js" type="text/javascript"></script>-->
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
	<script>
		//variables javascript
		var wbd;
		var wemp_pmla;
		var input_empresa;
		var	input_concepto;
		var	input_centroCostos;
		var input_procedimiento
		var	input_tarifa;
		var input_examen;
		var input_permisos;
		var input_usuario;

		$(document).ready(function(){//CREA LOS AUTOCOMPLETES DE LOS CAMPOS DE BUSQUEDA E INICIALIZA LAS VARIABLES QUE SON REQUERIDAS DURANTE TODO LA EXPERIENCIA DE USUARIO.
			//autocompletar de las empresas
			entidades_nombres_array = new Array();
			var datosEmpresas = eval( $("#array_empresas").val() );
			for( i in datosEmpresas ){
				entidades_nombres_array.push( datosEmpresas[i] );
			}

			 $( "#input_datoBuscado" ).autocomplete({
					source: entidades_nombres_array, minLength : 3
			});

			//autocompletar de las tarifas
			tarifas_nombres_array = new Array();
			var datosTarifas = eval( $("#array_tarifas").val() );

			for( j in datosTarifas ){
				tarifas_nombres_array.push( datosTarifas[j] );
			}

			$( "#input_tarifaBuscada" ).autocomplete({
					source: tarifas_nombres_array, minLength : 1
			});

			//autocompletar de las centrosCostos
			centrosCostos_nombres_array = new Array();
			var datosCentrosCostos = eval( $("#array_centrosCostos").val() );

			for( k in datosCentrosCostos ){
				centrosCostos_nombres_array.push( datosCentrosCostos[k] );
			}

			$( "#input_centroCostos" ).autocomplete({
					source: centrosCostos_nombres_array, minLength : 1
			});

			//autocompletar de las conceptos
			conceptos_nombres_array = new Array();
			var datosConceptos = eval( $("#array_conceptos").val() );

			for( m in datosConceptos ){
				conceptos_nombres_array.push( datosConceptos[m] );
			}

			$( "#input_conceptos" ).autocomplete({
					source: conceptos_nombres_array, minLength : 1
			});


			//autocompletar de las procediemientos
			procedimientos_nombres_array = new Array();
			var datosProcedimientos = eval( $("#array_procedimientos").val() );


			for( l in datosProcedimientos ){
				procedimientos_nombres_array.push( datosProcedimientos[l] );
			}

			$( "#input_procedimiento" ).autocomplete({
					source: procedimientos_nombres_array, minLength : 3
			});

			//autocompletar de las examenes
			examenes_nombres_array = new Array();
			var datosExamenes = eval( $("#array_examenes").val() );


			for( n in datosExamenes ){
				examenes_nombres_array.push( datosExamenes[n] );
			}

			$( "#input_examen" ).autocomplete({
					source: examenes_nombres_array, minLength : 3
			});

			wbd = $("#wbasedato").val();
			wemp_pmla = $("#wemp_pmla").val();
			$("#rb_todo").click();
			input_empresa = jQuery($("#input_datoBuscado"));
			input_concepto = jQuery($("#input_conceptos"));
			input_centroCostos = jQuery($("#input_centroCostos"));
			input_tarifa = jQuery($("#input_tarifaBuscada"));
			input_procedimiento = jQuery($("#input_procedimiento"));
			input_examen = jQuery($("#input_examen"));
			input_usuario = jQuery($("#input_usuario"));

			input_permisos = jQuery($("#input_permisos"));
		});

		function seleccionarTipoBusqueda(rbutton) //SELECCIONA EL TIPO DE PORTAFOLIO QUE SE VA A BUSCAR( HOSPITALARIO, HAMBULATORIO, TODO, ETC. ) EVITANDO QUE DOS OPCIONES ESTÉN CHECKEADAS AL TIEMPO.
		{
			radio = jQuery(rbutton);
			if(radio.is(":checked"))
			{
				$("#td_tipoBusqueda input:checkbox").each(function(){

						opcion  = jQuery(this);
						name = opcion.attr('id');
						if((opcion.is(":checked"))&&(opcion.attr("id")!=radio.attr("id")))
						{
							$(this).removeAttr("checked");
						}
					}
				);
				$("#tipo_busqueda").val(radio.val());
			}else
				{
					$("#tipo_busqueda").val('');
					return;
				}
		}

		function buscar()//ESTA FUNCION VALIDA QUE LOS DATOS ESTÉN COMPLETOS PARA PODER HACER LAS CONSULTAS, SOLICITANDO AL USUARIO AQUELLOS CAMPOS QUE SON OBLIGATORIOS.
		{
			//acá se valida primero la compatibilidad de la consulta
			if( ( $.trim(input_concepto.val()) == '' ) && ( $.trim(input_centroCostos.val()) == '') && ( $.trim(input_procedimiento.val()) == '' ) && ( $.trim(input_tarifa.val()) == '' ) && ( $.trim(input_empresa.val()) == '')){
				alerta(" Por favor ingrese los parámetros de búsqueda ");
				return;
			}
			if( ( $.trim(input_concepto.val()) != '' ) || ( $.trim(input_centroCostos.val()) != '' ) || ( $.trim(input_procedimiento.val()) != '') )
			{
				if($.trim(input_tarifa.val()) == '' && $.trim(input_empresa.val()) == '')
				{
					alerta("Por favor, ingrese la empresa o la tarifa que desea buscar");
					return;
				}
			}
			if($.trim(input_tarifa.val()) != '' && $.trim(input_empresa.val()) == ''){
				buscarEmpresasPorTarifa();
			}else{
					buscarPortafolio();
			     }
		}

		function buscarEmpresasPorTarifa() //ESTA FUNCION BUSCA TODAS LAS EMPRESAS QUE TIENEN UNA TARIFA DETERMINADA.
		{
			tarifa = input_tarifa.val();
			tarifa = tarifa.split(",");
			tarifa = tarifa[0];
			$("#div_respuestas").html('');
			$.ajax({
					url:	"rep_portafolioEntidad.php",
					async: false,
					type: "POST",
					data: {
								peticionAjax: 'consultarEmpresas',
								wbasedato:	wbd,
								wtarifa:    tarifa,
								wemp_pmla: 	wemp_pmla,
								tipoPeticion: "json"
						  },
					success: function(data)
							 {
							 	if( data.error == "error" ){
									validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
									return;
								}
								if(data.table == '')
								{
									alerta("Actualmente no hay empresas con dicha tarifa");
									return;
								}
								$("#div_empresas").html(data.table);
							 },
					dataType: "json"
				}
			);

		}

		function buscarPortafolio()//FUNCION QUE HACE EL LLAMADO AJAX QUE ARMA EL PORTAFOLIO Y LUEGO LO PINTA.
		{
			if(empresa == '')
			{
				alerta("por favor ingrese los datos de la empresa que desea buscar");
				return;
			}
			var generarDesdeMatrix = 'no';
			if($("#chk_buscarEnMatrix").is(":checked"))
				generarDesdeMatrix = 'si';

			if( generarDesdeMatrix == 'no' &&  $.trim(input_empresa.val()) != '' ){
				if( !confirm( "Esta acción podria reemplazar el portafolio del Mes Actual, desea continuar?" ) ){
					return;
				}
			}

			var empresa = input_empresa.val();
			var concepto = input_concepto.val();
				concepto = concepto.split(",");
				concepto = $.trim( concepto[0] );
			var centroCostos = input_centroCostos.val();
				centroCostos = centroCostos.split(",");
				centroCostos = $.trim( centroCostos[0] );
			var tarifa = input_tarifa.val();
				tarifa = tarifa.split(",");
				tarifa = $.trim( tarifa[0] );
			var procedimiento = input_procedimiento.val();
				procedimiento = procedimiento.split(",");
				procedimiento = $.trim( procedimiento[0] );
			var examen = input_examen.val();
				examen = examen.split(",");
				examen = $.trim( examen[0] );

			var datos =  new Array();
				datos = empresa.split(",");
			var codigo = datos[0];
			var nit = datos[1];
			var nombre = datos[2];

			var prueba = eval($("#array_conceptos").val())


			var tipoBusqueda = $("#tipo_busqueda").val();
			$.ajax({
					url:	"rep_portafolioEntidad.php",
					type: "POST",
					beforeSend: $.blockUI({ async: false, message: $('#msjEspere') }),
					data: {
								peticionAjax: 'consultarPortafolio',
								wbasedato:	wbd,
								wnit: 		nit,
								wcodigo:	codigo,
								wnombre: 	nombre,
								wtipoCcos:  tipoBusqueda,
								wconcepto:  concepto,
								wcentroCostos: centroCostos,
								wtarifa: tarifa,
								wprocedimiento: procedimiento,
								wexamen: examen,
								wprueba: prueba,
								wbusMatrix: generarDesdeMatrix,
								wanio:	$("#slc_anio").val(),
								wmes:	$("#slc_mes").val(),
								wpermisos:	input_permisos.val(),
								usuario:	input_usuario.val(),
								wemp_pmla: 	wemp_pmla,
								tipoPeticion: "json"
						  },
					success: function(data)
							 {
							 	if( data.error == "error" ){
									alerta( " la sesion ha caducado, por favor reingrese al programa " );
									return;
								}
								if(data.table == '')
								{
									$.unblockUI();
									alerta("No existen convenios con los parámetros ingresados");
									$("#div_respuestas").html('');
									return;
								}
								$("#div_empresas").html('');
								$("#div_respuestas").html(data.table);
								$("#div_guardar").html(data.datosConsulta);
								configurarBotonExportar();
							    $.unblockUI();

							 },
					dataType: "json"
				}
			);
		}

		function agregarNotaHijos(padre)//AGREGA A TODOS LOS PROCEDIMIENTOS DE UN CENTRO DE COSTOS LA NOTA QUE SE AGREGARA A ESTE (FUERA DE USO)
		{
			padre = jQuery($(padre));
			nuevoTexto = padre.val();
			cco = padre.attr("id").split("_");
			cco = cco[1];
			$(".txt_hijo"+cco).each(function(){
					input = jQuery($(this));
					input.val(nuevoTexto);
				}
			);
		}

		/*function mostrarHijos(id)//OCULTA Y MUESTRA LOS HIJOS DE UN CENTRO DE COSTOS(PROCEDIMIENTOS) Y LOS HIJOS DE LOS PROCEDIMIETOS(CONCEPTOS)
		{
			aux =  id.split("_");
			padre = aux[0];	//pregunta si se dio click en un tr de cco o de producto
			identificador = aux[1];	// provee la combinación de códigos de cco y producto necesarios para encontrar los conceptos asociados a estos.
			tr_padre = jQuery($("#"+id));
			mostrando = tr_padre.attr("mostrando");
			nuevoId = '';
			tieneNietos = false;

			switch(padre)
			{
				case 'cco':
					  tieneNietos = true;
					  break;
				case 'producto':
					  tieneNietos = false;
					  break;
			}

			if(mostrando=='false')
			{
				tr_padre.attr("mostrando","true");
			}
			$("."+id).each(function(){
					tr = jQuery(this);
					if(mostrando=='false')
					{
						tr.show();
						if(tieneNietos)
							{
								nuevoId = tr.attr("id");
								nuevoId2 = nuevoId.split("_");
								nuevoId2 = "producto_"+nuevoId2[1];
								if($("#"+nuevoId2).attr("mostrando")=="false")
								{
									$("#"+nuevoId2).attr("mostrando", "true");
									$("."+nuevoId2).show();
								}
							}
					}else
						{
							if(tieneNietos)
							{
								nuevoId = tr.attr("id");
								nuevoId2 = nuevoId.split("_");
								nuevoId2Aux = nuevoId2[1].split("-");
								nuevoId2 = "producto_"+nuevoId2[1];
								if($("#"+nuevoId2).attr("mostrando")=="true")
								{
									$("#"+nuevoId2).attr("mostrando", "false");
									$("."+nuevoId2).hide();
								}
							}
							tr.hide();
							tr_padre.attr("mostrando","false");
						}
				}
			);
		}*/

		function mostrarHijos(id)
		{
			aux =  id.split("_");
			padre = aux[0];	//pregunta si se dio click en un tr de cco o de producto
			identificador = aux[1];	// provee la combinación de códigos de cco y producto necesarios para encontrar los conceptos asociados a estos.
			tr_padre = jQuery($("#"+id));
			mostrando = tr_padre.attr("mostrando");
			nuevoId = '';
			tieneNietos = false;

			switch(padre)
			{
				case 'cco':
					  tieneNietos = true;
					  break;
				case 'producto':
					  tieneNietos = false;
					  break;
			}

			if(mostrando=='false')
			{
				tr_padre.attr("mostrando","true");
			}
			$("."+id).each(function(){
					tr = jQuery(this);
					if(mostrando=='false')
					{
						tr.show();
						if(tieneNietos)
							{
								nuevoId = tr.attr("id");
								nuevoId2 = nuevoId.split("_");
								nuevoId2 = "producto_"+nuevoId2[1];
								if($("#"+nuevoId2).attr("mostrando")=="false")
								{
									$("#"+nuevoId2).attr("mostrando", "true");
									$("."+nuevoId2).show();
								}
							}
					}else
						{
							if(tieneNietos)
							{
								nuevoId = tr.attr("id");
								nuevoId2 = nuevoId.split("_");
								nuevoId2 = "producto_"+nuevoId2[1];
								if($("#"+nuevoId2).attr("mostrando")=="true")
								{
									$("#"+nuevoId2).attr("mostrando", "false");
									$("."+nuevoId2).hide();
								}
							}
							tr.hide();
							tr_padre.attr("mostrando","false");
						}
				}
			);
		}

		function activarInputTarifa() //BLOQUEA INPUTS CUANDO UNO QUE ESTÁ EN CONFLICTO ESTÁ SIENDO USADO (FUERA DE USO)
		{
			rb_tarifa = jQuery("#rb_buscarTarifa");
			input_tarifa = jQuery("#input_tarifaBuscada");
			if(rb_tarifa.is(":checked")){
				input_tarifa.attr("readonly", false);
				$("#input_datoBuscado").val("");
				$("#input_datoBuscado").attr("readonly", true);
			}else{
				input_tarifa.val('');
				$("#input_datoBuscado").attr("readonly", false);
				input_tarifa.attr("readonly", true)
			}
		}

		function verPortafolioEmpresaTarifa(empresa) //LLAMA A LA FUNCION BUSCAR PORTAFOLIO A PARTIR DE UNA EMPRESA ELEGIDA POR MEDIO DE UNA BUSQUEDA POR TARIFA.
		{
			input_empresa.val(empresa);
			buscarPortafolio();
		}

		function guardarActualizarPortafolio(accion) //GUARDA UN PORTAFOLIO EN MATRIX(FUERA DE USO)
		{
			if(accion == 'guardar')
			{

				notas = arregloNotas();
				//notas = JSON.stringify(notas);
				//alert(notas);

				consulta = $("#consulta_efectuada").val();

				$.ajax({
					url:	"rep_portafolioEntidad.php",
					async: false,
					type: "POST",
					data: {
								peticionAjax: 'guardarPortafolio',
								wbasedato:	wbd,
								wemp_pmla: 	wemp_pmla,
								wconsulta:  consulta,
								wnotas: notas,
								tipoPeticion: "json"
						  },
					success: function()
							 {
							 	if( data.error == "error" ){
									validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
									return;
								}
								alerta("hecho");
							 },
					dataType: "json"
				});

				return;
			}else
				{
					alerta(accion);
					return;
				}
		}

		function arregloNotas()//RECORRE TODAS LAS NOTAS Y LAS GUARDA EN UN ARREGLO QUE ES ENVIADO POR AJAX PARA SER GUARDADO(FUERA DE USO)
		{
			var arregloNotas = new Object();
			$("#tbl_portafolio input:text").each(function(){
					nota = jQuery(this);
					identificacion = $.trim(nota.attr("id"));
					datosAux = identificacion.split("_");
					tipo = $.trim(datosAux[1]);
					clave = $.trim(datosAux[2]);

					notaGuardar = new Object()
					notaGuardar.identificacion = clave;
					notaGuardar.valor = nota.val();
					if(tipo == 'cco')
					{
						notaGuardar.esCco = 'on';
					}else
						{
							notaGuardar.esCco = 'off';
						}

					//arregloNotas.push(notaGuardar);
					arregloNotas[clave] = notaGuardar;
				}
			);
			notas = $.toJSON(arregloNotas);
			return(notas);
		}

		function actualizarNota( input, centroCostos, producto, tipo, mes, anio, esCco, numeroDoc ) //FUNCTION QUE SE ACTIVA CUANDO HAY CAMBIOS EN UNA NOTA PARA ACTUALIZARLA EN MATRIX
		{
			nota = jQuery(input);
			valorNota = nota.val();
			$.ajax({
				url:	"rep_portafolioEntidad.php",
				async: 	true,
				type: 	"POST",
				data:	{
							peticionAjax: 'actualizarNota',
							wbasedato:	wbd,
							wemp_pmla: 	wemp_pmla,
							wcco: centroCostos,
							wpdto: producto,
							wtipo: tipo,
							wmes: mes,
							wanio: anio,
							wesCco: esCco,
							wnota: valorNota,
							wnum: numeroDoc,
							tipoPeticion: "json"
						},
				success: function( data )
						{
							if( data.error == "error" ){
								validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa" );
							}
						},
				dataType: "json"
			});
		}

		function actualizarVigencia() // FUNCION QUE ACTUALIZA LA VIGENCIA DE UN PORTAFOLIO.
		{
			var numeroDocumento = $("#numeroDoc").val();
			var fechaInicial = $("#wfecini").val();
			var fechaFinal = $("#wfecfin").val();
			var mes = $("#mes").val();
			var anio = $("#anio").val();
			var empresa = $("#empresa").val();
			$.ajax({
				url:	"rep_portafolioEntidad.php",
				async: 	true,
				type: 	"POST",
				data:	{
							peticionAjax: 'actualizarVigencia',
							wbasedato:	wbd,
							wemp_pmla: 	wemp_pmla,
							wmes: mes,
							wanio: anio,
							wcodigoEmpresa: empresa,
							wfeci: fechaInicial,
							wfecf: fechaFinal,
							wnum: numeroDocumento,
							tipoPeticion: "normal"
						},
				success: function( data )
						{
							if( data.error == "error" ){
								validarExistenciaParametros( "la sesion ha caducado, por favor reingrese al programa " );
								return;
							}
							$("#chk_buscarEnMatrix").attr("checked","checked");
							buscarPortafolio();
							$('#btnwfecini').click(function(){
								agregarZindex('res');
							 });
							$('#btnwfecfin').click(function(){
								agregarZindex('res');
							 });
							$.unblockUI();

							//$("#div_vigencia").hide();
						}
			});

		}

		function mostrarDiv() //FUNCION QUE MUESTRA EL MENÚ PARA LA ELECCIÓN DE FECHAS PARA LA VIGENCIA DE UN PORTAFOLIO.
		{
			div="div_vigencia";
			$('#btnwfecini').click(function(){
				agregarZindex('add');
			 });
			$('#btnwfecfin').click(function(){
				agregarZindex('add');
			 });
			$.blockUI({
							message: $("#"+div),
							css: { left: '15%',
									top: '15%',
								  width: '40%',
						    	 height: '30%'
								 }
					  });
		}

		function agregarZindex(accion)//MODIFICA LA PROPIEDAD CSS "x-index", DE LOS CALENDARIOS SUMANDO O RESTANDO DE SER NECESARIO
		{
			if(accion == 'add')
				$(".calendar").css("z-index", 1002);
			else
				$$(".calendar").css("z-index", 1000);
		}

		function configurarBotonExportar()//FUNCION QUE PERMITE QUE LA TABLA HTML GENERADA SE PUEDA EXPORTAR
		{
			/**
                Inicializa la funcionalidad para generar la exportación a excel.
            */
            $(".botonExcel").click(function(event) {
				var tabla = $("#tbl_portafolio").eq(0).clone();
				tablaAux = jQuery(tabla);
				//tablaAux.find( "input[type='text']:not(:visible)" ).each(function(){
				tablaAux.find( "textarea" ).each(function(){

					nota = $.trim($(this).val());
					padre = jQuery($(this).parent());
					if(nota == "")
						nota = "&nbsp;";
					padre.html(nota);
				});
                $("#datos_a_enviar").val( $("<div>").append( tabla ).html());
                $("#FormularioExportacion").submit();
            });
		}

		function habilitarBusqueda( buscarMatrix ){
			if( $(buscarMatrix ).attr("checked") == true ){
				$("#slc_anio").removeAttr( "disabled" );
				$("#slc_mes").removeAttr( "disabled" );
			}else{
				$("#slc_anio").attr( "disabled", true );
				$("#slc_mes").attr( "disabled", true );
			}
		}

		function alerta( txt ){
			$("#textoAlerta").text( txt );
			$.blockUI({ message: $('#msjAlerta') });
				setTimeout( function(){
								$.unblockUI();
							}, 1600 );
		}

		function validarExistenciaParametros( txt ){
			$("div [id!='div_sesion_muerta']").hide();
			$("#div_sesion_muerta").show();
		}
	</script>
	<body>
<?php
/**
* NOMBRE:  REPORTE DE CARTERA POR EDADES
*
* PROGRAMA: RepCarXEdad.php
* TIPO DE SCRIPT: PRINCIPAL
* //DESCRIPCION:Este reporte presenta el listado de exámenes y procedimientos que tiene convenio la clinica con una entidad, genera historial en matrix siempre y cuando
* 				se consulte completo el portafolio; este historial guarda versiones mensuales cada que se consulte
*
* HISTORIAL DE ACTAULIZACIONES:
* 2013-02-01 Camilo Zapata.	- se modificó el script para que consulte el nombre de los centros de costos de la tabla costosyp_000005
* 2013-10-30 Camilo Zapata.	- se modificó el script para que notifique al usuario cuando ya la sesión ha muerto, para que así no continué trabajando sin guardar porque las peticiones ajax no trabajan*/
include_once('root/comun.php');
//

session_start();
if(!isset($_SESSION['user']))
{
	die('error');
}

function inicializarArreglosEmpresas(){
	global $conex;
	global $wfachos;
	global $wbasedato;
	global $conexunix;
	global $empresas;
	global $tarifas;
	global $conceptos;
	global $centrosCostos;
	global $procedimientos;
	global $examenes;
	$caracteres = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'");
	$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","");
	//formación de arreglo de empresas
	$query = "	SELECT Epscod as codigo, Epsnit nit, Epsnom as nombre "
			."	  FROM 	{$wbasedato}_000049"
			."	 ORDER BY Epsnom";

	$rs = mysql_query( $query, $conex ) or die ( mysql_error() );
	$i = 0;
	while( $row = mysql_fetch_array($rs) )
	{
		$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
		array_push( $empresas, trim($row['codigo']).", ".trim($row['nit']).", ".trim($row['nombre']) );
	}
	//formación arreglo de tarifas
	$query = "SELECT Tarcod codigo, Tarnom nombre
						FROM intar
					   WHERE Taract = 'S'";
	$rs = odbc_exec($conexunix,$query);
	while($row2 = odbc_fetch_row($rs))
	{
		$nombre = odbc_result($rs,'nombre');
		$codigo = odbc_result($rs,'codigo');
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );
		array_push( $tarifas, trim($codigo).", ".trim($nombre)."");
	}
	//formación de arreglo de conceptos
	$query = "SELECT Connom nombre, Concod codigo
						FROM facon
					   WHERE Conact = 'S'";

	$rs = odbc_exec($conexunix,$query);
	while($row2 = odbc_fetch_row($rs))
	{
		$nombre = odbc_result($rs,'nombre');
		$codigo = odbc_result($rs,'codigo');
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );
		array_push( $conceptos, trim($codigo).", ".trim($nombre)."");
	}

	//formación de arreglo de centros de costo

	$query = "SELECT Cconom nombre, Ccocod codigo
				FROM {$wbasedato}_000011
			   WHERE Ccoest = 'on'";

	$rs = mysql_query( $query, $conex ) or die ( mysql_error() );
	while( $row = mysql_fetch_array( $rs ) )
	{
		$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
		array_push( $centrosCostos, trim($row['codigo']).", ".trim($row['nombre']) );
	}

	//formación de arreglo de procedimientos
	$query = "SELECT procod codigo, proane cups, pronom nombre
			    FROM inpro
			   WHERE proact = 'S'
			     AND proane is not null
               UNION ALL
				SELECT procod codigo, '' cups, pronom nombre
			    FROM inpro
			   WHERE proact = 'S'
			     AND proane is null
			   GROUP BY 1,2,3
			   ORDER BY 3";
	$rs = odbc_exec($conexunix,$query);
	while($row2 = odbc_fetch_row($rs))
	{
		$nombre = odbc_result($rs,'nombre');
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );
		$codigo = odbc_result($rs,'codigo');
		$cups = odbc_result($rs,'cups');
		array_push( $procedimientos, trim($codigo).", ".trim($cups).", ".utf8_encode(trim($nombre))."");
	}

	//formación de arreglo de examenes
	$query = "SELECT exacod codigo, exaane cups, exanom nombre
				FROM inexa
			   WHERE exaact = 'S'
			     AND exaane is not null
			   UNION ALL
			   SELECT exacod codigo, '' cups, exanom nombre
				FROM inexa
			   WHERE exaact = 'S'
			     AND exaane is null
			   GROUP BY 1,2,3
			   ORDER BY 3";
	$rs = odbc_exec($conexunix,$query);
	while($row2 = odbc_fetch_row($rs))
	{
		$nombre = odbc_result($rs,'nombre');
		$nombre = str_replace( $caracteres, $caracteres2, $nombre );
		$codigo = odbc_result($rs,'codigo');
		$cups = odbc_result($rs,'cups');
		array_push( $examenes, trim($codigo).", ".trim($cups).", ".utf8_encode(trim($nombre))."");
	}
}

function empresaEmpleado($wemp_pmla, $conex, $wbasedato, $cod_use_emp)
{
    $use_emp = '+';

    $user_session = explode('-',$cod_use_emp);
    $user_session = (count($user_session) > 1) ? $user_session[1] : $user_session[0];

    $q = "  SELECT  Codigo, Empresa
            FROM    usuarios
            WHERE   codigo = '".$user_session."'
                    AND Activo = 'A'";
    $res = mysql_query($q,$conex);
    if(mysql_num_rows($res) > 0)
    {
        $row = mysql_fetch_array($res);
        $user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;

        $use_emp = $user_session.'-'.$row['Empresa']; // concatena los últimos 5 digitos del código del usuario con el código de la empresa a la que pertenece.
    }
    return $use_emp;
}

function verificarPermisos($conex, $wbasedato, $user_emp)
{
	/*$tienePermisos = false;
	$query = " SELECT Prfadm
			     FROM root_000082
				WHERE Prftem = '04'
				  AND Prftab = '27'
				  AND Prfuse = '{$user_emp}'
				  AND Prfest = 'on'";
	$rs = mysql_query( $query, $conex );
	while( $row = mysql_fetch_array( $rs ) )
	{
		( $row['Prfadm']=='on' ) ? $tienePermisos = true : $tienePermisos = false;
	}*/
	$tienePermisos = true;
	return( $tienePermisos );
}

$wactualiz           = "2013-02-01";
$wbasedato           = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$_SESSION['wfachos'] = consultarAliasPorAplicacion($conex, $wemp_pmla, "fachos");

$hoy = date("Y-m-d");
$anio = explode("-", $hoy);
$anio = $anio[0];
$empresas = array();
$conceptos = array();
$centrosCostos = array();
$tarifas = array();
$procedimientos = array();
$examenes = array();
//inicializo y formateo el usuario para verificar los permisos.
$user_session = explode('-',$_SESSION['user']);
$user_session = $user_session[1];
$user_session_wemp = empresaEmpleado($wemp_pmla, $conex, $wbasedato, $user_session);
$permisos = verificarPermisos( $conex, $wemp_pmla, $user_session_wemp );

inicializarArreglosEmpresas();
$empresas = json_encode($empresas);
$tarifas = json_encode($tarifas);
$centrosCostos = json_encode($centrosCostos);
$conceptos = json_encode($conceptos);
$procedimientos = json_encode($procedimientos);
$examenes = json_encode($examenes);


echo "<input type='hidden' id='array_procedimientos' value='{$procedimientos}'>";
echo "<input type='hidden' id='array_examenes' value='{$examenes}'>";
echo "<input type='hidden' id='array_empresas' value='{$empresas}'>";
echo "<input type='hidden' id='array_tarifas' value='{$tarifas}'>";
echo "<input type='hidden' id='array_centrosCostos' value='{$centrosCostos}'>";
echo "<input type='hidden' id='array_conceptos' value='{$conceptos}'>";
echo "<input type='hidden' id='wbasedato' value='{$wbasedato}'>";
echo "<input type='hidden' id='wemp_pmla' value='{$wemp_pmla}'>";
echo "<input type='hidden' id='input_permisos' value='{$permisos}'>";
echo "<input type='hidden' id='input_usuario' value='{$user_session}'>";
echo "<input type='hidden' id='wtarifa' value=''>";
echo "<input type='hidden' id='tipo_busqueda' value=''>";
echo "<br>";
echo "<br>";

encabezado("CONSULTA DE PORTAFOLIO POR EMPRESA",$wactualiz, "clinica");

echo "<div id='div_menuppal' align='center'>";
	echo "<table>";
		echo "<tr class='encabezadotabla'>";
			echo "<td colspan=4>PAR&Aacute;METROS DE BUSQUEDA</td>";
		echo "</tr>";
		echo "<tr class='fila1'>";
			echo "<td colspan=2>TIPO DE PORTAFOLIO</td>";
			echo "<td id='td_tipoBusqueda'><input type='checkbox' id='rb_hospitalario' name='rb_hospitalario' value='on' onclick='seleccionarTipoBusqueda(this)'> Hospitalario<br>
    				  <input type='checkbox' id='rb_ambulatorio' name='rb_ambulatorio' value='off' onclick='seleccionarTipoBusqueda(this)'> Ambulatorio </td>
				  <td id='td_tipoBusqueda'>  <input type='checkbox' id='rb_habitaciones' name='rb_habitaciones' value='hab' onclick='seleccionarTipoBusqueda(this)'> Habitaciones<br>
    				  <input type='checkbox' id='rb_todo' name='rb_todo' value='%' onclick='seleccionarTipoBusqueda(this)'> Todo</td>";
			echo "</td>";
		echo "</tr>";
		echo "<tr class='fila2'>";
			echo "<td colspan=2>  TARIFA </td>";
			echo "<td colspan=2><input type='text' id='input_tarifaBuscada' size='50'></td>";
		echo "</tr>";
		echo "<tr class='fila1'>";
			echo "<td colspan=2> EMPRESA</td>";
			echo "<td colspan=2><input type='text' id='input_datoBuscado' size='50'></td>";
		echo "</tr>";
		echo "<tr class='fila2'>";
			echo "<td> PROCEDIMIENTO</td>";
			echo "<td><input type='text' id='input_procedimiento' size='50'></td>";
			echo "<td> EXAMEN</td>";
			echo "<td><input type='text' id='input_examen' size='50'></td>";
		echo "</tr>";
		echo "<tr class='fila1'>";
			echo "<td> CENTRO DE COSTOS</td>";
			echo "<td><input type='text' id='input_centroCostos' size='30'></td>";
			echo "<td> CONCEPTO </td>";
			echo "<td><input type='text' id='input_conceptos' size='30'></td>";
		echo "</tr>";
	echo "</table>";
	echo "<br>";
	echo "<br>";

	echo "<table>";
		echo "<tr class='encabezadotabla'>";
			echo "<td><input type='checkbox' id='chk_buscarEnMatrix' name='chk_buscarEnMatrix' value='busc_matrix' onclick='habilitarBusqueda( this )'></td><td colspan=4>BUSCAR PORTAFOLIO HISTORICO EN MATRIX</td>";
		echo "</tr>";
		echo "<tr class='fila1'>";
		echo "<td>&nbsp;</td>";
			echo "<td>AÑO:  ";
				echo "<select disabled id='slc_anio'>";
					for($i = $anio; $i >= 2012; $i--)
					{
						echo "<option value={$i}>{$i}</option>";
					}
				echo "</select>";
			echo "</td>";
			echo "<td> MES: ";
				echo "<select disabled id='slc_mes'>";
					for($i = 1; $i <= 12; $i++)
					{
						echo "<option value={$i}>{$i}</option>";
					}
				echo "</select>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";

	echo "<br>";
	echo "<br>";
	echo "<div align='center'>";
		echo "<input type='button' id='btn_buscar' value='BUSCAR' onclick='buscar()'>";
	echo "</div>";
echo "</div>";

echo "<br>";
echo "<br>";
echo "<div id='div_respuestas' align='center'>";
echo "</div>";
echo "<br>";
echo "<div id='div_empresas' align='center'>";
echo "</div>";
echo "<br>";
echo "<div id='div_guardar' align='center'>";
echo "</div>";
echo "<br>";
//echo "<div id='div_vigencia' style='display:none' align='center'>";
echo "<div id='div_vigencia' align='center' style='display:none; cursor:default; background:none; repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'>";
	echo "<table>";
		echo "<tr class='encabezadotabla'><td colspan=4>POR FAVOR INGRESE LAS FECHAS QUE CUBRE EL CONVENIO---</td></tr>";
		echo "<tr id='tr_guardar' class='fila1'><td>Fecha Inicio</td><td>"; campoFechaDefecto("wfecini",$hoy); echo "</td><td>Fecha Final</td><td>"; campoFechaDefecto("wfecfin",$hoy); echo "</td></tr>";
		echo "<tr><td colspan=4>&nbsp;</td></tr>";
		echo "<tr><td colspan=4 align=center><input type='button' id='btn_cambiarVigencia' name='btn_cambiarVigencia' value='GUARDAR' onclick='actualizarVigencia();'></td></tr>";
		echo "<tr><td colspan=4>&nbsp;</td></tr>";
	echo "</table>";
echo "</div>";
echo "<div id='msjEspere' name='msjEspere' style='display:none; z-index=1005;'>";
echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />";
echo "</div>";
echo "<br>";
echo "<div id='div_cerrar' align='center'>";
echo  "<table>";
	echo "<tr><td><input type='button' id='btn_cerrar' name='btn_generar' value='CERRAR' onclick='window.close();'></td></tr>";
echo "</table>";
echo "</div>";
echo "<div id='msjAlerta' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/root/Advertencia.png'/>";
	echo "<br><br><div id='textoAlerta'></div><br><br>";
echo '</div>';
echo "<br /><br /><br /><br />
        <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center; display:none;' id='div_sesion_muerta'>
            [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
        </div>";
odbc_close($conexunix);
odbc_close_all();
?>
	</body>
</html>