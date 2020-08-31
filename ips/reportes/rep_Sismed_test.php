<?php
include_once("conex.php");
/*     * *******************************************************
     *                REPORTE SISMED   					       *
     * ******************************************************* */
//==================================================================================================================================
//PROGRAMA                   : rep_Sismed_test.php
//AUTOR                      : Camilo Zapata.
//FECHA CREACION             : Octubre 9 de 2012

//DESCRIPCION
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//     Programa que permite descargar el archivo .txt que contiene la informaci�n requerida para el SISMED
//     Este programa solo requiere que se le proveea el rango de meses y el tipo(ventas o compras) de documento que se desea generar, este
// 	   documento responde a las exigencias hechas por la COMISI�N NACIONAL DE PRECIOS DE MEDICAMENTOS Y DISPOSITIVOS M�DICOS en la Circular 2
//     del 30 Diciembre de 2011
//===========================================================================================================================================//

//ACTUALIZACIONES
//=========================================================================================================================================\\
//2020-02-14 Camilo Zapata: Se realizaron las modificacions necesarias para realizar el reporte con la nueva estructura definida en la circular # 6 de 2018
//2019-10-25 camilo zapata: Debido a cambios en la configuraci�n de los datos en unix( codigo cum ), estaba llegando incompleto en t�rminos del tama�o,
//							por lo tanto un codigo cum de unix no coincidia con los codigos disponibles en matrix(cliame_000244),
//							generando omisi�n de los coodigos a la hora de reportarse, por lo tanto se crea una funci�n(completarCodigo()) que completa el tama�o del codigo
//							con ceros a la izquierda en orden de lograr coincidiencias y cumplir con el estandar requerido en el documento generado
//2019-10-23 camilo zapata: Se modifica el programa para que valide si el articulo con movimiento tiene un codigo cum valido, en caso de que
//                          alguno no cumpla con dicha condici�n se notifica al usuario en pantalla el/los c�digos a corregir.
//2016-01-08 Juan C Hdez: Se modifica el query que hace relaci�n de los (000106) con las facturas (000066) para que solo traiga los registros
//                        activos de la 000066. Estaba tomando los activos e inactivos.
//2015-03-20 camilo Zapata: se modifico el script para que los articulos lo consulte en la respectiva base datos( clisur, farpmla, etc) y no en root_000064
//2013-01-22 camilo Zapata: se modifico el script para que genere el reporte con los cums sin ceros a la izquierda de tal manera que se mejore
//2013-01-22 camilo Zapata: se modifico el script para que genere el reporte con los cums sin ceros a la izquierda de tal manera que se mejore
//							la consolidaci�n y se disminuyan los errores de registros repetidos causados por esto: 000198765-02 y 00198765-02
//							estos dos valores generarian dos registros independientes lo cual no deberia permitirse; generando el registro con el
//							cums asociado: 198765-02
//2013-01-15 camilo Zapata: se modifico el script para que omita las devoluciones tanto en ventas como en compras
//2012-10-19 camilo Zapata: se modific� el script para que consulte en root_000051 los conceptos correspondientes a los distintos movimientos
//							(ventas, compras, devoluciones en ventas, devoluciones en compras), para que realice los calculos de manera mas precisa
//							- en las funciones de consolidar ventas y compras se hizo que no se muestren los valores que quedaron negativos.
//2013-01-04 camilo Zapata: se agreg� al script la posibilidad de consultar en la tabla de cargos para las ventas de clisur.
//DESCRIPCION                                                                                                          						\\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
 if(isset($ajaxdes))
 {
	//http://www.solingest.com/blog/descarga-de-archivos-en-php
	header ("Content-Disposition: attachment; filename=".$wdesc." ");
	header ("Content-Type: application/octet-stream");
	header ("Content-Length: ".filesize($wdesc));
	readfile($wdesc);
	unlink($wdesc);
	//header("Location: http://localhost/matrix/ips/reportes/rep_Sismed_test.php?wemp_pmla='{$wemp_pmla}'");

 }else
 {


	//**************************************************************************FUNCIONES QUE CONTRUYEN EL DOCUMENTO SISMED*******************************************************************************************//
	 function crear_archivo($filename,$content,$cont)//funcion que crea el archivo.
	 {
		   if($cont==1)
		   {
			 if (file_exists($filename))
			 {
				unlink($filename);
			 }
			 $modo1 = 'w';
			 $modo2 = 'a';
		   }
		   else
		   {
			 $modo1 = 'w+';
			 $modo2 = 'a';
		   }

		   if (!file_exists($filename))
				   $reffichero = fopen($filename, $modo1);

		   // Let's make sure the file exists and is writable first.
		   if (is_writable($filename))
		   {

				   // In our example we're opening $filename in append mode.
				   // The file pointer is at the bottom of the file hence
				   // that's where $content will go when we fwrite() it.
				   if (!$handle = fopen($filename, $modo2))
				   {
							//echo "Cannot open file ($filename)";
							exit;
				   }

				   // Write $content to our opened file.
				   if (fwrite($handle, $content) === FALSE)
				   {
						   //echo "Cannot write to file ($filename)";
						   exit;
				   }

				   //echo "Success, wrote ($content) to file ($filename)";

				   fclose($handle);

		   }
		   else
		   {
				   //echo "The file $filename is not writable";
		   }
	}

	function imprimirDatos($datos, $tipo, $ano, $mesI, $mesF, $origen, $archivoContenedor=""){//funcion que agrega cada registro al archivo.

		//calculo de la fecha de corte "ultimo dia del periodo"
		global $nit,
		       $cont;
		/*echo $mesI."\n";
		echo $mesF."\n";*/
		$ultimoDia = mktime(0,0,0,(($mesF*1)+1),0,$ano);
		$fechaCorte = date("Ymd",$ultimoDia);

		//buscar fecha de corte
		if( $archivoContenedor == "" ){
			$nombre_archivo = "MED".$tipo."_".$ano."_".$mesI."_".$mesF.".txt"; //Sm(SisMed)(c o v compras o ventas) a�o y mes.
			switch($tipo)
			{
				case 'v':
					$fuenteTipo = "113MVEN"; //fuente y tipo correspondiente a las ventas.
					break;
				case 'c':
					$fuenteTipo = "114MCOM"; //fuente y tipo correspondiente a las compras.
					break;
				case 'con':
					$fuenteTipo = "100MPRE";
					break;
			}
			//MED{fuentey tipo}.{fecha de corte correspondiente al �ltimo dia del mes final para el reporte}{NI: tipo de indentificaci�n nit}{numero de la identificaci�n}

			$nombre_archivo = "MED".$fuenteTipo."".$fechaCorte."NI000{$nit}.txt";
			$cont = 0;
		}else{
			$nombre_archivo = $archivoContenedor;
		}

		$regs = sizeof($datos);
		for($i=0; $i<($regs); $i++)//empieza en -1 para que la primer vez que entre cree el archivo.
		{

			if(  $i==$regs-1 and $origen != "consolidado"  )
				$contenido = $datos[$i];
			else
				$contenido = $datos[$i]."
";

			if($contenido != '')
			{
				$cont++;
				crear_archivo($nombre_archivo,$contenido,$cont); // lo crea en el mismo directorio.
			}
		}
		return( $nombre_archivo );
	}

	 function limpia_espacios($cadena)
	 {
		$cadena = str_replace(' ', '', $cadena);
		return $cadena;
	 }

	 //busca caracteres especiales y los elimina de la cadena
	 function eliminarCaracteresEspeciales($cadena)
	 {
		$caracteres = array("|",".","'\'","%","&","/","(",")","?","�","�","!","#",",");
		$cadena = str_replace($caracteres,'',$cadena);
		return($cadena);
	 }
/************************************************************************************************************************************************************************/
/***************************************************************INICIO FUNCIONES PARA LAS COMPRAS************************************************************************/

	 function generarInformeCompras($concepto, $consultarUnix, $consultarCargos, $origenReporte ) //Esta funcion consulta las compras en matrix y en caso de ser necesario invoca la funci�n que realiza lo mismo en servinte
	 {
		global $wbasedatos;
		global $conex;
		global $wemp_pmla;
		global $wmesI;
		global $wmesF;
		global $wano;
		global $error;
		global $mensaje;
		global $codigoHabilitacion;
		global $cums;
		global $ipServidor;

		$registros = array();
		$registrosParciales = array();

		$ano = $wano;
		$mesIni = $wmesI;
		$mesFin = $wmesF;
		$cantidadCums = 0;
		$totalRegistros=0;
		$totalCompras=0;
		$cumAct=" ";
		//-----------------------------------------------------------------------------------------------------------------------------------------------

		//ac� se consultan los conceptos de las compras
		$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
					 AND Detapl = 'conComMatrixSis'";
		$rs = mysql_query($query,$conex) or die (mysql_error());
		$row = mysql_fetch_array($rs);
		$conVent =  explode(",",$row['Detval']);
		foreach($conVent as $i=>$concepto)
		{
			if($compras=='')
				$compras.="'{$concepto}'";
				else
				$compras.=",'{$concepto}'";
		}
		//consultamos los conceptos para las devoluciones
		//consultamos los conceptos para las devoluciones.
	/*	$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
					 AND Detapl = 'conDevComMatrixSis'";
		$rs = mysql_query($query,$conex) or die (mysql_error());
		$row = mysql_fetch_array($rs);
		$conDev =  explode(",",$row['Detval']);
		foreach($conDev as $i=>$concepto)
		{
			if($devoluciones=='')
				$devoluciones.="'{$concepto}'";
				else
				$devoluciones.=",'{$concepto}'";
		}*/
		//en este punto se contruye la consulta que contiene la informaci�n solicitada por el SISMED."REGISTRO TIPO 2"
		//creamos la tabla temporal para las tablas de encabezado y detalle de inventario filtrada por a�o, fecha, concepto y estado.
		$tmcompras = "tmpComp1011";
		$qaux = "DROP TABLE IF EXISTS $tmcompras";
		$resdr = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
		$qtemp ="CREATE TEMPORARY TABLE IF NOT EXISTS $tmcompras "
						."(INDEX idx(Mdeart))"
				."SELECT Mdeart, Menmes, Mdecan, Mdevto, Menfac, Menano, 1 fac"
				."  FROM ".$wbasedatos."_000010, ".$wbasedatos."_000011"
				." WHERE Menano ='".$ano."'"
				."	 AND (Menmes BETWEEN '".$mesIni."' AND '".$mesFin."' )"
				."	 AND Mencon  IN (".$compras.")"
				."	 AND Mdecon  IN (".$compras.")"
				."	 AND Mdecon = Mencon"
				."	 AND Mdedoc = Mendoc"
				."	 AND Mdeest ='on'";
				/*." UNION ALL "
				."SELECT Mdeart, Menmes, Mdecan, Mdevto, Menfac, Menano, -1 fac"
				."  FROM ".$wbasedatos."_000010, ".$wbasedatos."_000011"
				." WHERE Menano ='".$ano."'"
				."	 AND (Menmes BETWEEN '".$mesIni."' AND '".$mesFin."' )"
				."	 AND Mencon  IN (".$devoluciones.")"
				."	 AND Mdecon  IN (".$devoluciones.")"
				."	 AND Mdecon = Mencon"
				."	 AND Mdedoc = Mendoc"
				."	 AND Mdeest ='on'";*/
		$rstemp = mysql_query($qtemp,$conex) or die (mysql_errno().":".mysql_error());

		//consulto los datos los art�culos por mes
		$query = "SELECT Artcna, Artcod art, Menmes mes, (SUM(Mdecan*fac)*Cumequ) can, (SUM(Mdevto*fac)) total, MIN((Mdevto/Mdecan)/Cumequ) minVal, MAX((Mdevto/Mdecan)/Cumequ) maxVal, Cumcod cum"
				."  FROM ".$tmcompras.", ".$wbasedatos."_000001, {$wbasedatos}_000244"
				." WHERE Cumcod = Artcna"
				."	 AND Cumint = Artcod"
				."	 AND Mdeart = Artcod"
				."	 AND cumemp = {$wemp_pmla}"
				."   AND Cumcod != ''
				 UNION ALL "
				."SELECT Cumint as 'Artcna', Artcod art, Menmes mes, (SUM(Mdecan*fac)*Cumequ) can, (SUM(Mdevto*fac)) total, MIN((Mdevto/Mdecan)/Cumequ) minVal, MAX((Mdevto/Mdecan)/Cumequ) maxVal, Cumcod cum"
				."  FROM ".$tmcompras.", ".$wbasedatos."_000001, {$wbasedatos}_000244"
				." WHERE Cumint = Artcod"
				."	 AND Mdeart = Artcod"
				."	 AND cumemp = {$wemp_pmla}"
				."   AND Cumcod = ''"
				." GROUP BY 1, 2, 3";
		$rs = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($rs);

		//temporal para buscar las facturas con m�ximos y m�nimos.
		$tmMinMax = "tmpMinMax";
		$qaux = "DROP TABLE IF EXISTS $tmMinMax";
		$resdr = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
		$qtemp = "CREATE TEMPORARY TABLE IF NOT EXISTS $tmMinMax"
						."(INDEX idx(Artcna,Menmes))"
				."SELECT Artcna, Menmes, Menfac, Mdevto, Mdecan, Menano"
				."  FROM ".$tmcompras.", ".$wbasedatos."_000001 "
				." WHERE Mdeart = Artcod";
		$rs2 = mysql_query($qtemp, $conex) or die (mysql_errno().":".mysql_error());
		//en esta parte vamos y consultamos las facturas que evidencian los precios m�nimos y m�ximos de compra
		while($reg = mysql_fetch_array($rs))
		{
			$cambioMin = false;
			$cambioMax = false;
			$totalRegistros++;
			$totalCompras+=$reg[3];
			/*if($cumAct!=trim($reg[0]))
			{
				$cumAct = trim($reg[0]);
				$cantidadCums++;
			}*/
			$cumAux = trim($reg['cum']);
			$cumAux = trim($reg['cum']);
			$cumAux = explode("-",$cumAux);
			$cumAux[0] = ( is_numeric( $cumAux[0] ) ) ? $cumAux[0] * 1 : $cumAux[0];
			$cumAux = implode("-",$cumAux);
			$registrosParciales[$reg['mes']][$cumAux]['mes'] = $reg['mes'];
			$registrosParciales[$reg['mes']][$cumAux]['canal'] = 'INS';
			$registrosParciales[$reg['mes']][$cumAux]['cum'] = $cumAux;
			$registrosParciales[$reg['mes']][$cumAux]['factor'] = $reg['fac'];
			$registrosParciales[$reg['mes']][$cumAux]['comercial'] = 'SI';

			if(!isset($registrosParciales[$reg['mes']][$cumAux]['unidades'])or($registrosParciales[$reg['mes']][$cumAux]['unidades']==''))
			{
				$cantidadCums++;
				$registrosParciales[$reg['mes']][$cumAux]['total'] = $reg['total'];
				$registrosParciales[$reg['mes']][$cumAux]['unidades'] = $reg['can'];
			}else
				{
					$registrosParciales[$reg['mes']][$cumAux]['total'] += $reg['total'];
					$registrosParciales[$reg['mes']][$cumAux]['unidades'] += $reg['can'];
				}

			if(!isset($registrosParciales[$reg['mes']][$cumAux]['minCompra'])or($registrosParciales[$reg['mes']][$cumAux]['minCompra']==''))
			{
				$registrosParciales[$reg['mes']][$cumAux]['minCompra'] = $reg['minVal'];
				$cambioMin = true;
			}else
				{
					$minActual = $registrosParciales[$reg['mes']][$cumAux]['minCompra'];
					$minNuevo =  $reg['minVal'];
					if($minNuevo<$minActual)
					{
						$registrosParciales[$reg['mes']][$cumAux]['minCompra'] = $minNuevo;
						$cambioMin = true;
					}
				}

			if(!isset($registrosParciales[$reg['mes']][$cumAux]['maxCompra'])or($registrosParciales[$reg['mes']][$cumAux]['maxCompra']==''))
			{
				$registrosParciales[$reg['mes']][$cumAux]['maxCompra'] = $reg['maxVal'];
				$cambioMax = true;
			}else
				{
					$maxActual = $registrosParciales[$reg['mes']][$cumAux]['maxCompra'];
					$maxNuevo =  $reg['maxVal'];
					if($maxNuevo>$maxActual)
					{
						$registrosParciales[$reg['mes']][$cumAux]['maxCompra'] = $maxNuevo;
						$cambioMax = true;
					}
				}

			//BUSQUEDA DE FACTURAS
			if($cambioMin)
			{
				$query ="SELECT Menfac, (Mdevto/Mdecan)"
						." FROM ".$tmMinMax." "
						."WHERE Artcna = '".$reg['cum']."'"
						."	AND Menano = '".$ano."'"
						."	AND Menmes ='".$reg['mes']."' "
						."ORDER BY 2 ASC"
						." LIMIT 1";
				$rs2 = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
				$reg2 = mysql_fetch_row($rs2);
				$registrosParciales[$reg['mes']][$cumAux]['facMin'] = $reg2[0];
			}
			//el siguiente if pregunta si los valores m�ximos y m�nimos son iguales para evitar hacer una consulta innecesaria
			if(($registrosParciales[$reg['mes']][$cumAux]['maxCompra']!=$registrosParciales[$reg['mes']][$cumAux]['minCompra'])or($cambioMax))
			{
				$query ="SELECT Menfac, (Mdevto/Mdecan) "
						." FROM ".$tmMinMax." "
						."WHERE Artcna = '".$reg['cum']."' "
						."	AND Menano ='".$ano."' "
						."	AND Menmes ='".$reg['mes']."' "
						."ORDER BY 2 DESC"
						." LIMIT 1";

				$rs3 = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
				$reg3 = mysql_fetch_row($rs3);

				$registrosParciales[$reg['mes']][$cumAux]['facMax'] = $reg3[0];
			}else
				{
					$registrosParciales[$reg['mes']][$cumAux]['facMax'] = $reg2[0];
				}
		}
		if($consultarUnix=='on')
			$comprasServinte = comprasServinte( $ano, $mesIni, $mesFin);

		if( !$conex ){
			$conex = mysqli_connect($ipServidor,'root','q6@nt6m', 'matrix') or die("No se realizo Conexion");
		}

		if( $error != 0 ){
			return;
		}
		/*echo "<pre>";
			print_r($comprasServinte);
		echo "</pre>";*/
		$registros = consolidarCompras($comprasServinte, $registrosParciales, $ano, $wmesI, $wmesF, $origenReporte );
		if( $origenReporte != "consolidado" )
			$resultado = imprimirDatos( $registros, "c",$ano,$mesIni, $mesFin, $origenReporte );
		else
			$resultado = $registros;

		return( $resultado );

	 }

	 function comprasServinte($ano, $wmesI, $wmesF) //esta funcion consulta las compras en unix y las retorna a la funcion generarInformeCompras para que consolide
	 {
		global $conex;
		global $wemp_pmla;
		global $wbasedatos;
		global $error;
		global $mensaje;
		global $cums;
		global $articulosSinCum;

		$articulosProbar    = array();
		$articulosProbarCum = array();
		$conexunix = odbc_pconnect('inventarios','informix','sco') or die("No se ralizo Conexion con Unix");
		$id=date('s');
		$comprasUnix=array();
		//ac� se consultan las conceptos de las compras
		$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
					 AND Detapl = 'conComUnixSis'";
		$rs = mysql_query($query,$conex) or die (mysql_error());
		$row = mysql_fetch_array($rs);
		$conCom =  explode(",",$row['Detval']);
		foreach($conCom as $i=>$concepto)
		{
			if($compras=='')
				$compras.="'{$concepto}'";
				else
				$compras.=",'{$concepto}'";
		}
		//consultamos los conceptos para las devoluciones.
		/*$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
					 AND Detapl = 'conDevComUnixSis'";
		$rs = mysql_query($query,$conex) or die (mysql_error());
		$row = mysql_fetch_array($rs);
		$conDev =  explode(",",$row['Detval']);
		foreach($conDev as $i=>$concepto)
		{
			if($devoluciones=='')
				$devoluciones.="'{$concepto}'";
				else
				$devoluciones.=",'{$concepto}'";
		}*/
		//se crea el array de los cums para buscar los factores correspondientes a cada articulo posteriormente.
		if(($wmesI*1)<10)
			 $wmesI = "'0"."{$wmesI}'";
			else
				$wmesI = "'{$wmesI}'";
		if(($wmesF*1)<10)
			 $wmesF = "'0"."{$wmesF}'";
			 else
				$wmesF = "'{$wmesF}'";

			/*$qfac="SELECT  movano ano,movmes mes ,artcod art,artcum cum,movdetcan can, movdetpre pre,
						(movdettot) total, movfue fue, movdoc doc, movdni, 1 fac, movcon
				   FROM ivmov,ivmovdet,ivart
				  WHERE movfue='06'
					AND movcon IN ({$compras})
					AND movano = '{$ano}'
					AND movmes between {$wmesI} and {$wmesF}
					AND movanu='0'
					AND movfue=movdetfue
					AND movdoc=movdetdoc
					AND movdetart=artcod
				  UNION ALL
				 SELECT movano ano,movmes mes ,artcod art,artcum cum,movdetcan can, movdetpre pre,
							(movdettot) total, movfue fue, movdoc doc, movdni, -1 fac, movcon
				   FROM ivmov,ivmovdet,ivart
				  WHERE movfue='07'
					AND movcon IN ({$devoluciones})
					AND movano = '{$ano}'
					AND movmes between {$wmesI} and {$wmesF}
					AND movanu='0'
					AND movfue=movdetfue
					AND movdoc=movdetdoc
					AND movdetart=artcod
				   INTO temp tmpcompra{$id}";*/
			$qfac="SELECT  movano ano,movmes mes ,artcod art,artcum cum,movdetcan can, movdetpre pre,
						(movdettot) total, movfue fue, movdoc doc, movdni, 1 fac, movcon
				   FROM ivmov,ivmovdet,ivart
				  WHERE movfue='06'
					AND movcon IN ({$compras})
					AND movano = '{$ano}'
					AND movmes between {$wmesI} and {$wmesF}
					AND movanu='0'
					AND movfue=movdetfue
					AND movdoc=movdetdoc
					AND movdetart=artcod
					AND artcod in ( 'J01FA5' )




					INTO temp tmpcompra{$id}";
			//echo "<pre>".print_r( $qfac, true)."</pre>" ;
			$resFac = odbc_exec($conexunix,$qfac);
			$condicion = ( count( $articulosSinCum ) > 0 ) ? " AND art IN ( ".implode(",", $articulosSinCum )." )" : "";
			$qfac = "SELECT *
					   FROM tmpcompra{$id}
					  WHERE cum IS NOT NULL
						AND cum <>' '
						AND cum <>'.'
					 UNION
					 SELECT ano,mes , art, art cum, can,  pre,
						 total,  fue,  doc, movdni, fac, movcon
					   FROM tmpcompra{$id}
					  WHERE ( cum is null or cum = '' or cum = '.' )
					   {$condicion}
					   INTO temp tempcompra2{$id}";
			$resFac = odbc_exec($conexunix,$qfac);

			$qfac = "SELECT ano, mes, art, cum, SUM(can*fac) can, SUM(total*fac) total, MAX(pre) premax, MIN(pre) premin
					   FROM tempcompra2{$id}
					  GROUP BY 1,2,3,4
					   INTO temp tempcompra3{$id}";
			$resFac = odbc_exec($conexunix,$qfac);

			$qfac = "SELECT a.ano, a.mes, a.art, a.cum, a.can, a.total, a.premax, premin, MIN(b.movdni) facmin
					   FROM	tempcompra3{$id} a, tmpcompra{$id} b
					  WHERE b.ano = a.ano
						AND b.mes = a.mes
						AND b.art = a.art
						AND b.cum = a.cum
						AND b.pre = a.premin
					  GROUP BY 1,2,3,4,5,6,7,8
						INTO temp tempcompra4{$id}";
			$resFac = odbc_exec($conexunix,$qfac);

			$qfac = "SELECT  a.ano, a.mes, a.art, a.cum, a.can, a.total, a.premax, premin, facmin, MIN(b.movdni) facmax
					   FROM tempcompra4{$id} a, tmpcompra{$id} b
					  WHERE b.ano = a.ano
						AND b.mes = a.mes
						AND b.art = a.art
						AND b.cum = a.cum
						AND b.pre = a.premax
						GROUP BY 1,2,3,4,5,6,7,8,9";
			$resFac = odbc_exec($conexunix,$qfac);

			while($row=odbc_fetch_row($resFac))
			{

				$mes = odbc_result($resFac,'mes');
				$cum = trim(odbc_result($resFac,'cum'));
				$cum = explode("-",$cum);
				//$cum[0] = ( trim($cum[0]) == "" or trim($cum[0]) == "NO APLICA" or trim($cum[0]) == "N/A" ) ? 0 : $cum[0];
				if( Is_Numeric(  $cum[0] ) ){//2019-10-23
					$cum[0] = $cum[0] * 1;
				}else {
					if( !in_array( "'".$cum[0]."'", $articulosSinCum )   ){
						$error   = 1;
						$mensaje .= "<br><span  class='subtituloPagina2'>El art�culo: ".odbc_result($resFac,'art')." NO tiene codigo cum asociado en servinte en Compras</span>";
						continue;
					}
				}
				//$cum1 = completarCodigo($cum[0])."-".completarSufijo($cum[1]);
				if( in_array( "'".$articulo."'", $articulosSinCum ) ){
					$cum1 = $cum[0];
					array_push( $articulosProbarCum, $cum1 );
				}else{
					$cum1 = $cum[0]."-".completarSufijo($cum[1]);
				}
				$cum = $cum1;
				$articulo = odbc_result($resFac,'art');
				$canArticulos = odbc_result($resFac,'can');
				$valorTotal = odbc_result($resFac,'total');
				$valMin = odbc_result($resFac,'premin');
				$valMax = odbc_result($resFac,'premax');
				$facMin = odbc_result($resFac,'facmin');
				$facMax = odbc_result($resFac,'facmax');
				if( in_array( $articulo, $articulosProbar ) ){

					array_push( $articulosProbarCum, $cum1 );
				}
				if(array_key_exists($cum1, $cums))
				{
					if(array_key_exists($articulo, $cums[$cum1]))
					{

						if(($cums[$cum1][$articulo]['factor']=="") or !isset($cums[$cum1][$articulo]['factor']))
							$cums[$cum1][$articulo]['factor']=1;
						$mes = $mes*1;
						$comprasUnix[$mes][$cum]['mes'] = $mes;
						$comprasUnix[$mes][$cum]['canal'] = 'INS';
						$comprasUnix[$mes][$cum]['cum'] = $cum;
						$comprasUnix[$mes][$cum]['fac'] = $cums[trim($cum1)]['factor'];
						$comprasUnix[$mes][$cum]['comercial'] = 'SI';

						//SE VERIFICA SI EXISTEN O NO DATOS PARA EL CUM EN EL MES DADO(OTROS ARTICULOS CON EL MISMO CUM)
						if(!isset($comprasUnix[$mes][$cum]['unidades']) or ($comprasUnix[$mes][$cum]['unidades']=='') )//si est� vacio se asigna
						{
							$comprasUnix[$mes][$cum]['unidades'] = ($canArticulos*($cums[$cum1][$articulo]['factor'])*1);
							$comprasUnix[$mes][$cum]['total'] = $valorTotal*1;
						}else
							{//si no est� vacio se acumula.
								$comprasUnix[$mes][$cum]['total'] += $valorTotal*1;
								$comprasUnix[$mes][$cum]['unidades'] += ($canArticulos*($cums[$cum1][$articulo]['factor'])*1);
							}

						//MANEJO DE M�NIMOS Y M�XIMOS,
						if(!isset($comprasUnix[$mes][$cum]['minCompra']) or ($comprasUnix[$mes][$cum]['minCompra']=='') )
						{
							$comprasUnix[$mes][$cum]['minCompra'] = ($valMin/$cums[$cum1][$articulo]['factor'])*1;
							$comprasUnix[$mes][$cum]['facMin'] = $facMin;
						}else
							{
								$minActual = $comprasUnix[$mes][$cum]['minCompra']*1;
								$minNuevo = ($valMin/$cums[$cum1][$articulo]['factor'])*1;
								if($minNuevo<$minActual)
								{
									$comprasUnix[$mes][$cum]['minCompra'] = $minNuevo;
									$comprasUnix[$mes][$cum]['facMin'] = $facMin;
								}
							}
						if(!isset($comprasUnix[$mes][$cum]['maxCompra']) or ($comprasUnix[$mes][$cum]['maxCompra']=='') )
						{
							$comprasUnix[$mes][$cum]['maxCompra'] = ($valMax/$cums[$cum1][$articulo]['factor'])*1;
							$comprasUnix[$mes][$cum]['facMax'] = $facMax;
						}else
							{
								$maxActual = $comprasUnix[$mes][$cum]['maxCompra']*1;
								$maxNuevo = ($valMax/$cums[$cum1][$articulo]['factor'])*1;
								if($maxNuevo>$maxActual)
								{
									$comprasUnix[$mes][$cum]['maxCompra'] = $maxNuevo;
									$comprasUnix[$mes][$cum]['facMax'] = $facMax;
								}
							}
					}
				}
			}
		odbc_close($conexunix);
		odbc_close_all();
		/*foreach( $articulosProbarCum as $key=>$codigoCum ){
			echo "<br> compras -> buscandoooooo. $codigoCum ";
			echo "<pre>".print_r( $comprasUnix[1][$codigoCum], true )."</pre>";
		}*/

		return($comprasUnix);
	 }

	 function completarCodigo( $codigoRecibido ){

	 	$tam   = strlen( $codigoRecibido );
	 	$ceros = "";
	 	for( $i = 0 ; $i < 9 - $tam ; $i++ ){
	 		$ceros .= "0";
	 	}
	 	return( $ceros.$codigoRecibido );
	 }

	 function completarSufijo( $sufijoRecibido ){

	 	$tam   = strlen( $sufijoRecibido );
	 	$ceros = "";
	 	for( $i = 0 ; $i < 2 - $tam ; $i++ ){
	 		$ceros .= "0";
	 	}
	 	return( $ceros.$sufijoRecibido );
	 }

	 function consolidarCompras($comprasServinte, $comprasMatrix, $ano, $wmesI, $wmesF, $origenReporte )//funcion que consolida los informes de compras de matrix y unix en el archivo
	 {
		global $nit;
		global $dver;
		global $cumsConsolidado;
		global $contConsolidado;
		global $codigoHabilitacion;
		$cumsAux        = array();
		$cantidadCums   = 0;
		$totalRegistros = ( $origenReporte != "consolidado" ) ? 0 : -1;
		$datosCum       = ( $origenReporte == "consolidado" ) ? datosCumXarticulo() : array();
		$comprasTotales = 0;
		$registros      = array(); // quedar� en la forma ya implementada para no cambiar el resto del c�digo
		$parcial        = array(); //arreglo que se llenara por las claves
		$i=0;
		//AC� SE GUARDAN EN EL PARCIAL LAS VENTAS TRAIDAS DE MATRIX.
		foreach($comprasMatrix as $keyMes=>$comprasCums)
		{
			foreach($comprasCums as $keyCum=>$datosMatrix)
				{
					$parcial[$keyMes][$keyCum]=$datosMatrix;
				}
		}

		//AC� SE GUARDAN EN EL PARCIAL LAS COMPRAS TRAIDAS DE UNIX.
		if(count($comprasServinte)>0)
		{
			foreach($comprasServinte as $keyMes=>$CumsMes)
			{
				foreach($CumsMes as $keyCum=>$compras)
				{
					if(!isset($parcial[$keyMes]))
						$parcial[$keyMes]=array();
					if(!array_key_exists($keyCum, $parcial[$keyMes])) //si el cum traido de unix no est� para el mes desde matrix simplemente se agrega
					{
						$parcial[$keyMes][$keyCum]=$compras;
					}else
						{//en caso de que se encuentre, hay que sumar los totales y comparar los precios minimos y m�ximos para verificar por

							$parcial[$keyMes][$keyCum]['total'] += ($compras['total'])*1;
							$parcial[$keyMes][$keyCum]['unidades'] += ($compras['unidades'])*1;
							$minActual = ($parcial[$keyMes][$keyCum]['minCompra'])*1;
							$maxActual = $parcial[$keyMes][$keyCum]['maxCompra'];

							if(($compras['minCompra']*1)<($minActual*1))
							{
								$parcial[$keyMes][$keyCum]['minCompra']=$compras['minCompra'];
								$parcial[$keyMes][$keyCum]['facMin']=$compras['facMin'];
							}

							if($compras['maxCompra']>$maxActual)
							{
								$parcial[$keyMes][$keyCum]['maxCompra']=$compras['maxCompra'];
								$parcial[$keyMes][$keyCum]['facMax']=$compras['facMax'];
							}

						}
				}
			}
		}

		//AC� RECORREMOS EL ARREGLO ARMANDO A "REGISTROS"
		$cumAux = array();
		foreach($parcial as $keyMes=>$comprasCums)
		{
			foreach($comprasCums as $keyCum=>$datos)
			{
				if($datos['total']>0 and $datos['unidades']>0)
				{
					if( $origenReporte != "consolidado"){
						if(!in_array(limpia_espacios($keyCum), $cumsAux))
							{
								array_push($cumsAux, limpia_espacios($keyCum));
							}
					}else{
						if(!in_array(limpia_espacios($keyCum), $cumsConsolidado)){
							array_push($cumsConsolidado, limpia_espacios($keyCum));
						}
					}
					//ac� se va a verificar que las cantidades sean positivas en caso de que esto no se d�, no se muestra en el reporte.

					$totalRegistros++;
					$contConsolidado++;
					$comprasTotales+=number_format($datos['total'],2,".","");
					/*$registros[$totalRegistros]="2, ".$totalRegistros.", Mes: ".$datos['mes'].", Cum:".$datos['cum'].", Min costo/u: ".number_format($datos['minCompra'],2,".","").", Max costo/u: ".number_format($datos['maxCompra'],2,".","").", Valor t: ".number_format($datos['total'],2,".","").", Canti: ".number_format($datos['unidades'],5,".","").", fac min: ".$datos['facMin'].", fac max: ".$datos['facMax']."
";*/
					if( $origenReporte != "consolidado" ){
						$registros[$totalRegistros]="2,".$totalRegistros.",".$datos['mes'].",".limpia_espacios($datos['cum']).",".number_format($datos['minCompra'],2,".","").",".number_format($datos['maxCompra'],2,".","").",".number_format($datos['total'],2,".","").",".number_format($datos['unidades'],5,".","").",".trim( eliminarCaracteresEspeciales($datos['facMin'])).",".trim(eliminarCaracteresEspeciales($datos['facMax']))."";
					}else{
						echo "<br>edb-> entendiendo cum: ".$datos['cum'];
						var_dump( $datosCum );
						$cum           = $datosCum[$datos['cum']];
						$cum['numExpediente'] = explode("-",$datos['cum']);
						$cum['codPresentacionCom'] = $cum['numExpediente'][1];
						$cum['numExpediente'] = ltrim( $cum['numExpediente'][0], "0");
						$cum['Ium1'] = ( $cum['Ium1'] == "" ) ? 0 : $cum['Ium1'];
						$cum['Ium2'] = ( $cum['Ium2'] == "" ) ? 0 : $cum['Ium2'];
						$cum['Ium3'] = ( $cum['Ium3'] == "" ) ? 0 : $cum['Ium3'];
						$datos['facMin'] = explode( "-",$datos['facMin'] );
						$datos['facMin'] = ( count($datos['facMin']) == 1 ) ? $datos['facMin'][0] : $datos['facMin'][count($datos['facMin'])-1];
						$datos['facMax'] = explode( "-",$datos['facMax'] );
						$datos['facMax'] = ( count($datos['facMax']) == 1 ) ? $datos['facMax'][0] : $datos['facMax'][count($datos['facMax'])-1];
						if( !$cum['reportaCum'] ){
							$cum['numExpediente'] = "";
							$cum['codPresentacionCom'] = "";
						}

						$registros[$totalRegistros]="2|".$contConsolidado."|{$codigoHabilitacion}|".$datos['mes']."|2|CM|05|".$cum['Ium1']."|".$cum['Ium2']."|".$cum['Ium3']."|".$cum['numExpediente']."|".$cum['codPresentacionCom']."|C|".number_format($datos['minCompra'],2,".","")."|".number_format($datos['maxCompra'],2,".","")."|".number_format($datos['total'],2,".","")."|".$datos['unidades']."|".trim( eliminarCaracteresEspeciales($datos['facMin']))."|".trim(eliminarCaracteresEspeciales($datos['facMax']));
					}
				}
			}
		}
		$cantidadCums=count($cumsAux);
		if( $origenReporte != "consolidado" )
			$registros[0]="1,2,NI,{$nit},{$dver},,".$ano.",".$wmesI.",".$wmesF.",".$totalRegistros.",".number_format($comprasTotales,2,".","").",0";
		return($registros);
	 }

/************************************************************************************************************************************************************************/
/***************************************************************FIN FUNCIONES PARA LAS COMPRAS***************************************************************************/

/*****************************************************************INICIO FUNCIONES PARA LAS VENTAS*********************************************************************/
/*********************************************************************************************************************************************************************/

	 //funcion que genera las ventas de matrix e invoca en caso der ser necesario la funcion que consolida las ventas en unix
	 function generarInformeVentas($concepto, $consultarUnix, $consultarCargos, $origenReporte )
	 {
		global $wbasedatos;
		global $conex;
		global $wemp_pmla;
		global $wmesI;
		global $wmesF;
		global $wano;
		global $error;
		global $mensaje;
		global $codigoHabilitacion;
		global $cums;
		global $ipServidor;

		$registros = array();
		$registrosParciales = array();
		//declaraci�n de variables que contendran los datos para el registro de contr�l, este registro se construir� cuando se tenga toda la informaci�n
		$ano = $wano;
		$mesIni = $wmesI;
		$mesFin = $wmesF;
		$cantidadCums = 0;
		$totalRegistros=0;
		$ventasTotales=0;
		$cumAct=" ";
		//-----------------------------------------------------------------------------------------------------------------------------------------------
		$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
					 AND Detapl = 'conVenMatrixSis'";
		$rs = mysql_query($query,$conex) or die (mysql_error());
		$row = mysql_fetch_array($rs);
		$conVent =  explode(",",$row['Detval']);
		foreach($conVent as $i=>$concepto)
		{
			if($ventas=='')
				$ventas.="'{$concepto}'";
				else
				$ventas.=",'{$concepto}'";
		}
		//consultamos los conceptos para las devoluciones.
		/*$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
					 AND Detapl = 'conDevVenMatrixSis'";
		$rs = mysql_query($query,$conex) or die (mysql_error());
		$row = mysql_fetch_array($rs);
		$conDev =  explode(",",$row['Detval']);
		foreach($conDev as $i=>$concepto)
		{
			if($devoluciones=='')
				$devoluciones.="'{$concepto}'";
				else
				$devoluciones.=",'{$concepto}'";
		}	  */
		//en este punto se contruye la consulta que contiene la informaci�n solicitada por el SISMED."REGISTRO TIPO 2"
			//primero se crea una tabla temporal, para mejorar el rendimiento del script, esta tabla incluye los filtros y joins entre las tablas 16 y 17
		$tmventas = "tmpVen1617".date('s');
		$qaux = "DROP TABLE IF EXISTS $tmventas";
		$resdr = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
		if($consultarCargos != 'on')
		{
			$qtemp ="CREATE TEMPORARY TABLE IF NOT  EXISTS $tmventas "
							."(INDEX idx(Vdeart))	"
					."SELECT Vdeart, Venmes, Vdecan, Vdevun, Venffa, Vennfa, Venano, 1 fac"
					."  FROM ".$wbasedatos."_000016, ".$wbasedatos."_000017"
					." WHERE Vdenum = Vennum"
					."	 AND Venano ='".$ano."'"
					."	 AND (Venmes BETWEEN '".$mesIni."' AND '".$mesFin."' )"
					."	 AND Vencon in (".$ventas.")"
					."	 AND Venest ='on'";
					/*." UNION ALL "
					."SELECT Vdeart, Venmes, Vdecan, Vdevun, Venffa, Vennfa, Venano, -1 fac"
					."  FROM ".$wbasedatos."_000016, ".$wbasedatos."_000017"
					." WHERE Vdenum = Vennum"
					."	 AND Venano ='".$ano."'"
					."	 AND (Venmes BETWEEN '".$mesIni."' AND '".$mesFin."' )"
					."	 AND Vencon in (".$devoluciones.")"
					."	 AND Venest ='on'";*/
		}else
			{
				if(($mesIni*1)<10)
					 $mesIni = "0"."{$mesIni}";
					else
						$mesIni = "{$mesIni}";

				$fechaInicial = $ano."-".$mesIni."-01";
				$ultimoDia = mktime(0,0,0,(($mesFin*1)+1),0,$ano);
				$fechaFinal = date("Y-m-d",$ultimoDia);

				$qtemp = "CREATE TEMPORARY TABLE IF NOT  EXISTS $tmventas "
								."(INDEX idx(Vdeart))	"
						."SELECT Tcarprocod Vdeart,  SUBSTRING( tcarfec,6,2 ) Venmes, Tcarcan Vdecan, ABS(Tcarvun) Vdevun, Rcfffa Venffa, Rcffac Vennfa, SUBSTRING( tcarfec,1,4 ) Venano, 1 fac
							FROM  clisur_000106 a, clisur_000066 b
						   WHERE Tcarfec BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							 AND tcarest = 'on'
							 AND tcarcmo in (".$ventas.")
							 AND Rcfreg = a.id
							 AND Rcfest = 'on' ";         //Modificaci�n 2016-01-08
						/*." UNION ALL "
						."SELECT Tcarprocod Vdeart,  SUBSTRING( tcarfec,6,2 ) Venmes, Tcarcan Vdecan, ABS(Tcarvun) Vdevun, Rcfffa Venffa, Rcffac Vennfa, SUBSTRING( tcarfec,1,4 ) Venano, -1 fac
							FROM  clisur_000106 c, clisur_000066 d
						   WHERE Tcarfec BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
							 AND tcarest = 'on'
							 AND tcarcmo in (".$devoluciones.")
							 AND Rcfreg = c.id";*/
			}

		$rstemp = mysql_query($qtemp,$conex) or die (mysql_errno().":".mysql_error());
		//se hace el join con la tabla 64.
		$query = "SELECT  Artcna cum, Artcod art, Venmes mes, (SUM(Vdecan*fac)*Cumequ) unidades, (SUM(Vdecan*Vdevun*fac)) total, MIN(Vdevun/Cumequ) minVal, MAX(Vdevun/Cumequ) maxVal, Cumcod, Cumequ, Venano"
				."  FROM ".$tmventas.", ".$wbasedatos."_000001, {$wbasedatos}_000244" //."  FROM ".$wbasedatos."_000016, ".$wbasedatos."_000017, ".$wbasedatos."_000001, root_000064"
				." WHERE Cumcod = Artcna"
				."	 AND Cumint = Artcod"
				."	 AND Vdeart = Artcod"
				."	 AND cumemp = {$wemp_pmla}"
				."   AND Cumcod != ''"
				/*." UNION ALL "
				."SELECT Artcod cum, Artcod art, Venmes mes, (SUM(Vdecan*fac)*Cumequ) unidades, (SUM(Vdecan*Vdevun*fac)) total, MIN(Vdevun/Cumequ) minVal, MAX(Vdevun/Cumequ) maxVal, Cumcod, Cumequ, Venano"
				."  FROM ".$tmventas.", ".$wbasedatos."_000001, {$wbasedatos}_000244" //."  FROM ".$wbasedatos."_000016, ".$wbasedatos."_000017, ".$wbasedatos."_000001, root_000064"
				." WHERE Cumint = Artcod"
				."	 AND Vdeart = Artcod"
				."	 AND cumemp = {$wemp_pmla}"
				."   AND Cumcod = ''"*/
				." GROUP BY 1, 2, 3";
		$rs = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
		$num = mysql_num_rows($rs);


		//cremos una tabla temporal para buscar las facturas, que evidencien el m�ximo y el m�nimo.
		$tmMinMax = "tmpMinMax";
		$qaux = "DROP TABLE IF EXISTS $tmMinMax";
		$resdr = mysql_query($qaux,$conex) or die (mysql_errno().":".mysql_error());
		$qtemp = "CREATE TEMPORARY TABLE IF NOT EXISTS $tmMinMax"
						."(INDEX idx(Artcna, Venmes))"
				." SELECT Artcna, Venffa, Vennfa, Vdevun, Venmes, Venano "
				."   FROM ".$tmventas.", ".$wbasedatos."_000001"
				."  WHERE Vdeart = Artcod";
		$req2 = mysql_query($qtemp, $conex) or die (mysql_errno().":".mysql_error());

		while($reg = mysql_fetch_array($rs))
		{
			$cambioMin = false;
			$cambioMax = false;
			//variables para el registro de control.
			$totalRegistros++;
			$ventasTotales+= $reg[3];
			/*if($cumAct!=trim($reg[0]))
			{
				$cumAct = trim($reg[0]);
				$cantidadCums++;
			}*/
			//ESTAS VARIABLES NO CAMBIAN
			$cumAux = trim($reg['cum']);
			$cumAux = explode("-",$cumAux);
			$cumAux[0] = ( is_numeric( $cumAux[0] ) ) ? $cumAux[0] * 1 : $cumAux[0];
			$cumAux = implode("-",$cumAux);
			/*echo $cumAux;
			echo "\n";*/
			$registrosParciales[$reg['mes']][$cumAux]['mes'] = $reg['mes'];
			$registrosParciales[$reg['mes']][$cumAux]['canal'] = 'INS';
			$registrosParciales[$reg['mes']][$cumAux]['cum'] = $cumAux;
			//$registrosParciales[$reg['mes']][$reg['cum']]['factor'] = $reg[7];
			$registrosParciales[$reg['mes']][$cumAux]['comercial'] = 'SI';
			//AC� SE VERIFICA SI HAY O NO DATOS ACTUALES PARA EL CUM EN EL MES DADO
			if(!isset($registrosParciales[$reg['mes']][$cumAux]['total']) or ($registrosParciales[$reg['mes']][$cumAux]['total']==''))
			{
				$registrosParciales[$reg['mes']][$cumAux]['total'] = 0;
				$cantidadCums++;
			}
				$registrosParciales[$reg['mes']][$cumAux]['total'] += $reg['total'];

			if(!isset($registrosParciales[$reg['mes']][$cumAux]['unidades']) or ($registrosParciales[$reg['mes']][$cumAux]['unidades']==''))
				$registrosParciales[$reg['mes']][$cumAux]['unidades'] = 0;
				$registrosParciales[$reg['mes']][$cumAux]['unidades'] += $reg['unidades'];

			if(!isset($registrosParciales[$reg['mes']][$cumAux]['minVenta'])or($registrosParciales[$reg['mes']][$cumAux]['minVenta']==''))
			{
				$registrosParciales[$reg['mes']][$cumAux]['minVenta'] = $reg['minVal'];
				$cambioMin=true;
			}else
				{
					$minActual = $registrosParciales[$reg['mes']][$cumAux]['minVenta'];
					$minNuevo = $reg['minVal'];
					if($minNuevo<$minActual)
					{
						$registrosParciales[$reg['mes']][$cumAux]['minVenta'] = $reg['minVal'];
						$cambioMin=true;
					}
				}

			if(!isset($registrosParciales[$reg['mes']][$cumAux]['maxVenta'])or($registrosParciales[$reg['mes']][$cumAux]['maxVenta']==''))
			{
				$registrosParciales[$reg['mes']][$cumAux]['maxVenta'] = $reg['maxVal'];
				$cambioMax = true;
			}else
				{
					$maxActual = $registrosParciales[$reg['mes']][$cumAux]['maxVenta'];
					$maxNuevo = $reg['maxVal'];
					if($maxNuevo>$maxActual)
					{
						$registrosParciales[$reg['mes']][$cumAux]['maxVenta'] = $reg['maxVal'];
						$cambioMax = true;
					}
				}
			if($cambioMin)
			{
				$query ="SELECT Venffa, Vennfa, Vdevun"
						." FROM ".$tmMinMax." "
						."WHERE Artcna = '".$reg['cum']."' "
						."	AND Venmes ='".$reg['mes']."' "
						."	AND Venano ='".$ano."' "
						."ORDER BY 3 ASC "
						."LIMIT 1";
				$rs2 = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
				$reg2 = mysql_fetch_row($rs2);
				$registrosParciales[$reg['mes']][$cumAux]['facMin'] = $reg2[0]."-".$reg2[1];
			}
			//en esta parte vamos y consultamos las facturas que evidencian los precios m�nimos y m�ximos de compra
			if(($registrosParciales[$reg['mes']][$cumAux]['minVenta']!=$registrosParciales[$reg['mes']][$cumAux]['maxVenta']) or ($cambioMax))
			{
				$query ="SELECT Venffa, Vennfa, Vdevun"
						." FROM ".$tmMinMax." "
						."WHERE Artcna = '".$reg['cum']."' "
						."	AND Venmes ='".$reg['mes']."' "
						."	AND Venano ='".$ano."' "
						."ORDER BY 3 DESC "
						."LIMIT 1 ";
				$rs3 = mysql_query($query, $conex) or die (mysql_errno().":---".mysql_error());
				$reg3 = mysql_fetch_row($rs3);

				$registrosParciales[$reg['mes']][$cumAux]['facMax'] = $reg3[0]."-".$reg3[1];

			}else
				{
					$registrosParciales[$reg['mes']][$cumAux]['facMax'] = $registrosParciales[$reg['mes']][$cumAux]['facMin'];
				}
		}
		//consultamos las ventas en unix;

		if($consultarUnix=='on')
			$ventasServinte = ventasServinte( $ano, $wmesI, $wmesF);
			/*echo "<pre>";
				print_r( $registrosParciales );
			echo "</pre>";*/
		if( !$conex ){
			$conex = mysqli_connect($ipServidor,'root','q6@nt6m', 'matrix') or die("No se realizo Conexion");
		}

		$registros = consolidarVentas($ventasServinte, $registrosParciales, $ano, $wmesI, $wmesF, $origenReporte );
		/*$registros = consolidarVentas(&$registrosParciales, &$ano, &$wmesI, &$wmesF);*/
		if( $origenReporte != "consolidado" )
			$resultado = imprimirDatos($registros, "v",$ano, $wmesI, $wmesF, $origenReporte);
		else
			$resultado = $registros;
		return( $resultado );
	 }

	 //funci�n que consulta las ventas en servinte.
	 function ventasServinte($ano, $wmesI, $wmesF )
	 {
		global $conex;
		global $wemp_pmla;
		global $wbasedatos;
		global $error;
		global $mensaje;
		global $cums;
		global $articulosSinCum;

		$conexunix = odbc_pconnect('inventarios','informix','sco') or die("No se ralizo Conexion con Unix");
		$articulosProbar = array();
		$articulosProbarCum = array();
		$id=date('s');
		$ventasUnix=array();
		$articulosAprobar2 = array( 'J01FA5' );
		//ac� se consultan los conceptos de las ventas  para unix
		$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
					 AND Detapl = 'conVenUnixSis'";
		$rs = mysql_query($query,$conex) or die (mysql_error());
		$row = mysql_fetch_array($rs);
		$conVent =  explode(",",$row['Detval']);
		foreach($conVent as $i=>$concepto)
		{
			if($ventas=='')
				$ventas.="'{$concepto}'";
				else
				$ventas.=",'{$concepto}'";
		}
		//consultamos los conceptos para las devoluciones
		/*$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
					 AND Detapl = 'conDevVenUnixSis'";
		$rs = mysql_query($query,$conex) or die (mysql_error());
		$row = mysql_fetch_array($rs);
		$conDev =  explode(",",$row['Detval']);
		foreach($conDev as $i=>$concepto)
		{
			if($devoluciones=='')
				$devoluciones.="'{$concepto}'";
				else
				$devoluciones.=",'{$concepto}'";
		}*/
		//se consultan los cums y los factores
		$wmesI = "'".formatearMes($wmesI)."'";
		$wmesF = "'".formatearMes($wmesF)."'";

		$qfac = "SELECT drofue, drodoc, drodetite ite,drodetart, drodetcan, drodetpre,
						(drodetcan*drodetpre) tot, fuetmo,droano ano,dromes mes, artcum cum, fuecob
				   FROM ivdro, ivdrodet, sifue, ivart
				  WHERE droano = '{$ano}'
					AND dromes between {$wmesI} and {$wmesF}
					AND droanu = '0'
					AND drofue = drodetfue
					AND drodoc = drodetdoc
					AND drofue = fuecod
					AND drodetart = artcod
					AND drodetfac = 'S'
					AND artcod in ( 'J01FA5' )



				   INTO temp tmp{$id} ";
		//echo "<pre>".print_r( $qfac, true)."</pre>" ;

		$resFac = odbc_exec($conexunix,$qfac);

		/*carajo otra ves
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmp{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmp{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/
		$condicion = ( count( $articulosSinCum ) > 0 ) ? " AND drodetart IN ( ".implode(",", $articulosSinCum )." )" : "";
		$qfac = "SELECT drofue,drodoc,ite,drodetart art,drodetcan can,drodetpre pre,
						tot,fuetmo,cum,ano,mes, 1 fac
				   FROM tmp{$id}
				  WHERE fuetmo='2'
					AND fuecob IN ({$ventas})
					AND cum is not null
					AND cum <> ' '
					AND cum <> '.'
				  UNION
				 SELECT drofue,drodoc,ite,drodetart art,drodetcan can,drodetpre pre,
						tot,fuetmo,drodetart cum,ano,mes, 1 fac
				   FROM tmp{$id}
				  WHERE fuetmo='2'
					AND fuecob IN ({$ventas})
					AND ( cum is null or cum = '' or cum = '.' )
					{$condicion}
					INTO temp tmp1{$id} ";
				/*UNION ALL
				 SELECT drofue,drodoc,ite,drodetart art,drodetcan can,drodetpre pre,
						tot,fuetmo,cum,ano,mes, -1 fac
				   FROM tmp{$id}
				  WHERE fuetmo='1'
					AND fuecob IN ({$devoluciones})
					AND cum is not null
					AND cum <> ' '
					AND cum <> '.'
				   INTO temp tmp1{$id} " */
		$resFac = odbc_exec($conexunix,$qfac);
		/*carajo otra ves
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmp1{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmp1{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/

		$qfac = "SELECT ano,mes, cum, art, SUM(can*fac) can,SUM(tot*fac) total,MIN(pre) premin,
						MAX(pre) premax
				   FROM tmp1{$id}
				  GROUP by 1,2,3,4
				  ORDER by 1,2,3,4
				   INTO temp tmp2{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		/*carajo otra ves
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmp2{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmp2{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/

		$qfac = "SELECT tmp2{$id}.ano,tmp2{$id}.mes,tmp2{$id}.cum,tmp2{$id}.art,tmp2{$id}.can,tmp2{$id}.total,tmp2{$id}.premin,
						tmp1{$id}.drofue fuec,tmp1{$id}.drodoc docc,tmp1{$id}.ite itec,tmp2{$id}.premax
				   FROM tmp2{$id},tmp1{$id}
				  WHERE tmp2{$id}.ano=tmp1{$id}.ano
					AND tmp2{$id}.mes=tmp1{$id}.mes
					AND tmp2{$id}.cum=tmp1{$id}.cum
					AND tmp2{$id}.art=tmp1{$id}.art
					AND tmp2{$id}.premin=tmp1{$id}.pre
				   INTO temp tmpcardet1{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		/*carajo otra ves
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmpcardet1{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmpcardet1{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/


		$qfac = "SELECT tmpcardet1{$id}.ano,tmpcardet1{$id}.mes,tmpcardet1{$id}.cum,tmpcardet1{$id}.art,
						tmpcardet1{$id}.can,tmpcardet1{$id}.total,tmpcardet1{$id}.premin,tmpcardet1{$id}.fuec,
						tmpcardet1{$id}.docc,min(tmpcardet1{$id}.itec) itec,tmpcardet1{$id}.premax
				   FROM tmpcardet1{$id}
				  GROUP by 1,2,3,4,5,6,7,8,9,11
				   INTO temp tmpcardet2{$id}";
		$resFac = odbc_exec($conexunix,$qfac);

		$qfac = "SELECT ano,mes,cum,art,can,total,premin,fuec,docc,premax,carfacfue,carfacdoc
				   FROM tmpcardet2{$id},facardet,facarfac
				  WHERE fuec=cardetfue
					AND docc=cardetdoc
					AND itec=cardetite
					AND cardetreg=carfacreg
				  UNION
                  SELECT ano,mes,cum,art,can,total,premin,fuec,docc,premax,carfacfue,carfacdoc
                   FROM tmpcardet2{$id},Aycardet,aycarfac
                  WHERE fuec=cardetfue
                    AND docc=cardetdoc
                    AND itec=cardetite
                    AND cardetreg=carfacreg
				   INTO temp tmpcardet3{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		/*carajo otra ves
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmpcardet3{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmpcardet3{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/


		$qfac = "SELECT ano,mes,cum,art,can,total,premin,premax,carfacfue fuef,
						MIN(carfacdoc) docf
				   FROM tmpcardet3{$id}
				  GROUP BY 1,2,3,4,5,6,7,8,9
				   INTO temp tmp4{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		/*carajo otra ves
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmp4{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmp4{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/


		$qfac = "SELECT tmp4{$id}.ano,tmp4{$id}.mes,tmp4{$id}.cum,tmp4{$id}.art,tmp4{$id}.can,tmp4{$id}.total,tmp4{$id}.premin,
						tmp4{$id}.fuef,tmp4{$id}.docf,
						tmp1{$id}.drofue fuec,tmp1{$id}.drodoc docc,tmp1{$id}.ite itec,tmp4{$id}.premax
				   FROM tmp4{$id},tmp1{$id}
				  WHERE tmp4{$id}.ano=tmp1{$id}.ano
					AND tmp4{$id}.mes=tmp1{$id}.mes
					AND tmp4{$id}.cum=tmp1{$id}.cum
					AND tmp4{$id}.art=tmp1{$id}.art
					AND tmp4{$id}.premax=tmp1{$id}.pre
				   INTO temp tmpcardet5{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		/*carajo otra ves
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmpcardet5{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmpcardet5{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/


		$qfac = "SELECT tmpcardet5{$id}.ano,tmpcardet5{$id}.mes,tmpcardet5{$id}.cum,tmpcardet5{$id}.art,
						tmpcardet5{$id}.can,tmpcardet5{$id}.total,tmpcardet5{$id}.premin,
						tmpcardet5{$id}.fuef,tmpcardet5{$id}.docf,tmpcardet5{$id}.premax,
						tmpcardet5{$id}.fuec,tmpcardet5{$id}.docc,min(tmpcardet5{$id}.itec) itec
				   FROM tmpcardet5{$id}
				  GROUP by 1,2,3,4,5,6,7,8,9,10,11,12
				   INTO temp tmpcardet6{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		/*carajo otra ves
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmpcardet6{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmpcardet6{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/


		$qfac = "SELECT ano,mes,cum,art,can,total,premin,fuef,docf,premax,fuec,docc,
						carfacfue,carfacdoc
				   FROM tmpcardet6{$id},facardet,facarfac
				  WHERE fuec=cardetfue
					AND docc=cardetdoc
					AND itec=cardetite
					AND cardetreg=carfacreg
			 	  UNION
                  SELECT ano,mes,cum,art,can,total,premin,fuef,docf,premax,fuec,docc,
						carfacfue,carfacdoc
                   FROM tmpcardet6{$id},Aycardet,aycarfac
                  WHERE fuec=cardetfue
                    AND docc=cardetdoc
                    AND itec=cardetite
                    AND cardetreg=carfacreg
				   INTO temp tmpcardet7{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		/*o9carajo otra ves*/
		$qfac = " SELECT COUNT(*) cantidad
		            FROM tmpcardet7{$id}";
		$resFac = odbc_exec($conexunix,$qfac);
		$row=odbc_fetch_row($resFac);
		echo "<br>->edb cantidad en tmpcardet7{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/
		/*carajo otra ves
		$qfac = " SELECT *
		            FROM tmpcardet2{$id}";
		$arregloAuxiliar = array();
		$resFac = odbc_exec($conexunix,$qfac);
		while( $row=odbc_fetch_row($resFac) ){
			$aux1 = array('cum'=> odbc_result($resFac,'cum'),
							'ano'=> odbc_result($resFac,'ano'),
							'mes'=> odbc_result($resFac,'mes'),
							'art'=> odbc_result($resFac,'art'),
							'can'=> odbc_result($resFac,'can'),
							'total'=> odbc_result($resFac,'total'),
							'premin'=> odbc_result($resFac,'premin'),
							'fuec'=> odbc_result($resFac,'fuec'),
							'docc'=> odbc_result($resFac,'docc'),
							'itec'=> odbc_result($resFac,'itec'),
							'premax'=> odbc_result($resFac,'premax')
						 );
		    array_push( $arregloAuxiliar, $aux1 );

		}
		echo "<br> edb-> <pre>".print_r( $arregloAuxiliar, true )."</pre>";
		//echo "<br>->edb cantidad en tmpcardet2{$id}: ".odbc_result($resFac,'cantidad');
		/* cierra carajo otra ves*/

		$qfac = "SELECT ano,mes,cum,art,can,total,premin,fuef,docf,premax,carfacfue fuef2,
						MIN(carfacdoc) docf2
				  FROM tmpcardet7{$id}
				 GROUP BY 1,2,3,4,5,6,7,8,9,10,11";
		$resFac = odbc_exec($conexunix,$qfac);

		while($row=odbc_fetch_row($resFac))
		{

			$mes = odbc_result($resFac,'mes');
			$cum = trim(odbc_result($resFac,'cum'));
			$cum = explode("-",$cum);
			$articulo = odbc_result($resFac,'art');
			//$cum[0] = ( trim($cum[0]) == "" or trim($cum[0]) == "NO APLICA" or trim($cum[0]) == "N/A" ) ? 0 : $cum[0];
			if( Is_Numeric(  $cum[0] ) ){//2019-10-23
				$cum[0] = $cum[0] * 1;
			}else {
				if( !in_array( "'".$cum[0]."'", $articulosSinCum )   ){
					$error    = 1;
					$mensaje .= " <br><span  class='subtituloPagina2'>El art�culo: ".odbc_result($resFac,'art')." NO tiene codigo cum asociado en servinte en ventas</span>";
					continue;
				}
			}
			if( in_array( $articulo, $articulosAprobar2) ){
				array_push( $articulosProbarCum, $cum1 );
			}
			//$cum1 = completarCodigo($cum[0])."-".completarSufijo($cum[1]);
			if( in_array( "'".$articulo."'", $articulosSinCum ) ){
				$cum1 = $cum[0];
				array_push( $articulosProbarCum, $cum1 );
			}else{
				$cum1 = $cum[0]."-".completarSufijo($cum[1]);
			}
			$cum = $cum1;
			$canArticulos = odbc_result($resFac,'can');
			$valorTotal = odbc_result($resFac,'total');
			$valMin = odbc_result($resFac,'premin');
			$valMax = odbc_result($resFac,'premax');
			$fueMin = odbc_result($resFac,'fuef');
			$facMin = odbc_result($resFac,'docf');
			$fueMax = odbc_result($resFac,'fuef2');
			$facMax = odbc_result($resFac,'docf2');
			echo "<br> caraio, $cum1";
			echo "<pre>".print_r( $cums, true )."</pre>";
			if(array_key_exists($cum1, $cums))
			{


				if(($cums[$cum1][$articulo]['factor']=="") or !isset($cums[$cum1][$articulo]['factor']))
					$cums[$cum1][$articulo]['factor']=1;
				$mes = $mes*1;
				$ventasUnix[$mes][$cum]['fac'] = $cums[trim($cum1)]['factor'];
				$ventasUnix[$mes][$cum]['mes'] = $mes;
				$ventasUnix[$mes][$cum]['canal'] = 'INS';
				$ventasUnix[$mes][$cum]['cum'] = $cum;
				$ventasUnix[$mes][$cum]['comercial'] = 'SI';

				//SI ES LA PRIMERA VEZ QUE ENTRA PARA ESE CUM.
				if(!isset($ventasUnix[$mes][$cum]['total']) or ($ventasUnix[$mes][$cum]['total']==''))
				{
					$ventasUnix[$mes][$cum]['total']=0;
					$ventasUnix[$mes][$cum]['unidades']=0;
				}
				$anterior = $ventasUnix[$mes][$cum]['total']*1;
				$ventasUnix[$mes][$cum]['total'] += $valorTotal*1;
				$nuevo = $ventasUnix[$mes][$cum]['total']*1;
				$canant = $ventasUnix[$mes][$cum]['unidades']*1;
				$ventasUnix[$mes][$cum]['unidades'] += ($canArticulos*$cums[$cum1][$articulo]['factor'])*1;
				$cannue = $ventasUnix[$mes][$cum]['unidades'];

				//SI ES LA PRIMER QUE ENTRA A ESTE CUM Y NO HAY VALORES M�NIMOS
				if(!isset($ventasUnix[$mes][$cum]['minVenta']) or ($ventasUnix[$mes][$cum1]['minVenta']==''))
				{
					$ventasUnix[$mes][$cum]['minVenta'] = ($valMin/$cums[$cum1][$articulo]['factor'])*1;
					$ventasUnix[$mes][$cum]['facMin'] = $fueMin."-".$facMin;
				}else//SI YA HAY ALGO EN LOS PRECIOS M�NIMOS SE DEBE COMPARAR
					{
						//-----echo "<br>entr�: ".$cum."-codigo-".$articulo." antes: ".$anterior."-".$canant." despues:".$nuevo."-".$cannue;
						$minActual = $ventasUnix[$mes][$cum]['minVenta'];
						$valMin = ($valMin/$cums[$cum1][$articulo]['factor'])*1;
						if($valMin<$minActual)
						{
							$ventasUnix[$mes][$cum]['minVenta'] = $valmin;
							$ventasUnix[$mes][$cum]['facMin'] = $fueMin."-".$facMin;

						}
					}

				//SI ES LA PRIMER QUE ENTRA A ESTE CUM Y NO HAY VALORES M�XIMOS
				if(!isset($ventasUnix[$mes][$cum]['maxVenta']) or ($ventasUnix[$mes][$cum]['maxVenta']==''))
				{
					$ventasUnix[$mes][$cum]['maxVenta'] = ($valMax/$cums[$cum1][$articulo]['factor'])*1;
					$ventasUnix[$mes][$cum]['facMax'] = $fueMax."-".$facMax;
				}else//SI YA HAY ALGO EN LOS PRECIOS M�XIMOS SE DEBE COMPARAR
					{
						$maxActual = $ventasUnix[$mes][$cum]['maxVenta'];
						$valmax = ($valMin/$cums[$cum1][$articulo]['factor'])*1;
						if($valMax>$maxActual)
						{
							$ventasUnix[$mes][$cum]['maxVenta'] = $valMax;
							$ventasUnix[$mes][$cum]['facMax'] = $fueMax."-".$facMax;

						}
					}
			}
		}

		odbc_close($conexunix);
		odbc_close_all();
		foreach( $articulosProbarCum as $key=>$codigoCum ){
			echo "<br> ventas -> buscandoooooo. $codigoCum ";
			echo "<pre>".print_r( $ventasUnix[4][$codigoCum], true )."</pre>";
			echo "<pre>".print_r( $ventasUnix[5][$codigoCum], true )."</pre>";
			echo "<pre>".print_r( $ventasUnix[6][$codigoCum], true )."</pre>";
		}

		return($ventasUnix);
	 }

	 //funcion que consolida(suma las ventas de matrix y unix) el informe de ventas;
	 function consolidarVentas(&$ventasServinte, &$ventasMatrix, $ano, $mesIni, $mesFin, $origenReporte)
	 {
		global $nit;
		global $dver;
		global $cumsConsolidado;
		global $contConsolidado;
		global $codigoHabilitacion;
		$cumsAux        = array(); //este va registrando los diferentes cums que se van presentando
		$cantidadCums   = 0;
		$totalRegistros = ( $origenReporte != "consolidado" ) ? 0 : -1;
		$datosCum       = ( $origenReporte == "consolidado" ) ? datosCumXarticulo() : array();
		$ventasTotales  = 0;
		$registros      = array(); // quedar� en la forma ya implementada para no cambiar el resto del c�digo
		$parcial        = array(); //arreglo que se llenara por las claves

		//AC� SE GUARDAN EN EL PARCIAL LAS VENTAS TRAIDAS DE MATRIX.
		foreach($ventasMatrix as $keyMes=>$ventasCums)
		{
			foreach($ventasCums as $keyCum=>$datosMatrix)
				{
					$parcial[$keyMes][$keyCum]=$datosMatrix;
				}
		}

		//AC� SE GUARDAN EN EL PARCIAL LAS VENTAS TRAIDAS DE UNIX.
		if(count($ventasServinte)>0)
		{
			foreach($ventasServinte as $keyMes=>$CumsMes)
			{
				foreach($CumsMes as $keyCum=>$ventas)
				{
					if(!isset($parcial[$keyMes]))
						$parcial[$keyMes]=array();
					if(!array_key_exists($keyCum, $parcial[$keyMes])) //si el cum traido de unix no est� para el mes desde matrix simplemente se agrega
					{
						$parcial[$keyMes][$keyCum]=$ventas;
					}else
						{//en caso de que se encuentre, hay que sumar los totales y comparar los precios minimos y m�ximos para verificar por

							$parcial[$keyMes][$keyCum]['total'] += $ventas['total'];
							$parcial[$keyMes][$keyCum]['unidades'] += $ventas['unidades'];
							$minActual = $parcial[$keyMes][$keyCum]['minVenta'];
							$maxActual = $parcial[$keyMes][$keyCum]['maxVenta'];

							if($ventas['minVenta']<$minActual)
							{
								$parcial[$keyMes][$keyCum]['minVenta']=$ventas['minVenta'];
								$parcial[$keyMes][$keyCum]['facMin']=$ventas['facMin'];
							}

							if($ventas['maxVenta']>$maxActual)
							{
								$parcial[$keyMes][$keyCum]['maxVenta']=$ventas['maxVenta'];
								$parcial[$keyMes][$keyCum]['facMax']=$ventas['facMax'];
							}

						}
				}
			}
		}

		//AC� RECORREMOS EL ARREGLO ARMANDO A "REGISTROS"
		foreach($parcial as $keyMes=>$ventasCums)
		{
			foreach($ventasCums as $keyCum=>$datos)
			{
				if($datos['total']>0)
				{
					if( $origenReporte != "consolidado"){
						if(!in_array(trim($keyCum), $cumsAux)){
							array_push($cumsAux, $keyCum);
						}
					}else{
						if(!in_array(limpia_espacios($keyCum), $cumsConsolidado)){
							array_push($cumsConsolidado, limpia_espacios($keyCum));
						}
					}
					$totalRegistros++;
					$contConsolidado++;
					$ventasTotales+=number_format($datos['total'],2,".","");
					/*$registros[$totalRegistros]="2| ".$totalRegistros."|  Mes: ".$datos['mes']." | canal: INS | Cum:".$datos['cum']." | SI |  Min venta/u: ".number_format($datos['minVenta'],2,".","")." | Max venta/u: ".number_format($datos['maxVenta'],2,".","")." | Valor t: ".number_format($datos['total'],2,".","")."| Unidades: ".number_format($datos['unidades'],5,".","")." | fac min: ".$datos['facMin']." | fac max: ".$datos['facMax']."
";*/
					if( $origenReporte != "consolidado" ){
						$registros[$totalRegistros]="2,".$totalRegistros.",".$datos['mes'].",INS,".limpia_espacios($datos['cum']).",SI,".number_format($datos['minVenta'],2,".","").",".number_format($datos['maxVenta'],2,".","").",".number_format($datos['total'],2,".","").",".number_format($datos['unidades'],5,".","").",".trim( eliminarCaracteresEspeciales($datos['facMin']) ).",".trim( eliminarCaracteresEspeciales($datos['facMax']) )."";
					}else{
						echo "<br>edb-> entendiendo cum: ".$datos['cum'];
						var_dump( $datosCum );
						$cum           = $datosCum[$datos['cum']];
						$cum['numExpediente'] = explode("-",$datos['cum']);
						$cum['codPresentacionCom'] = $cum['numExpediente'][1];
						$cum['numExpediente'] = ltrim( $cum['numExpediente'][0], "0");
						$cum['Ium1'] = ( $cum['Ium1'] == "" ) ? 0 : $cum['Ium1'];
						$cum['Ium2'] = ( $cum['Ium2'] == "" ) ? 0 : $cum['Ium2'];
						$cum['Ium3'] = ( $cum['Ium3'] == "" ) ? 0 : $cum['Ium3'];
						$datos['facMin'] = explode( "-",$datos['facMin'] );
						$datos['facMin'] = ( count($datos['facMin']) == 1 ) ? $datos['facMin'][0] : $datos['facMin'][count($datos['facMin'])-1];
						$datos['facMax'] = explode( "-",$datos['facMax'] );
						$datos['facMax'] = ( count($datos['facMax']) == 1 ) ? $datos['facMax'][0] : $datos['facMax'][count($datos['facMax'])-1];
						if( !$cum['reportaCum'] ){
							$cum['numExpediente'] = "";
							$cum['codPresentacionCom'] = "";
						}
						$registros[$totalRegistros]="2|".$contConsolidado."|{$codigoHabilitacion}|".$datos['mes']."|2|VN|05|".$cum['Ium1']."|".$cum['Ium2']."|".$cum['Ium3']."|".$cum['numExpediente']."|".$cum['codPresentacionCom']."|C|".number_format($datos['minVenta'],2,".","")."|".number_format($datos['maxVenta'],2,".","")."|".number_format($datos['total'],2,".","")."|".$datos['unidades']."|".trim( eliminarCaracteresEspeciales($datos['facMin']) )."|".trim( eliminarCaracteresEspeciales($datos['facMax']) )."";
					}
				}
			}
		}
		$cantidadCums=count($cumsAux);
		if( $origenReporte != "consolidado" )
			$registros[0]="1,1,NI,{$nit},{$dver},".$cantidadCums.",,,,".$ano.",".$mesIni.",".$mesFin.",".$totalRegistros.",".$ventasTotales."";
		return($registros);
	 }


/*****************************************************************FINAL FUNCIONES PARA LAS VENTAS*********************************************************************/
/*********************************************************************************************************************************************************************/

/************************************************************************************************************************************************************************/
/*********************************************************INICIO FUNCIONES REPORTE CONSOLIDADO (NUEVA NORMATIVIDAD)******************************************************/
	function generarNuevoInformeSismed(){
		global $wbasedatos;
		global $conex;
		global $wemp_pmla;
		global $wmesI;
		global $wmesF;
		global $wano;
		global $error;
		global $mensaje;
		global $cont;
		global $consultarUnix;
		global $nit;
		global $dver;
		global $cumsConsolidado;
		global $contConsolidado;
		global $codigoHabilitacion;
		global $consultarCargos;
		global $cums;
		$registroEncabezado = array();
		$codigoHabilitacion = consultarCodigoHabilitacion( $wemp_pmla, "codigo_habilitacion" );

		$concepto     = buscarConcepto("V");
		$registrosVen = generarInformeVentas($concepto, $consultarUnix, $consultarCargos, "consolidado");
		$cantidadVen  = count( $registrosVen );
		$concepto     = buscarConcepto("C");
		$registrosCom = generarInformeCompras($concepto, $consultarUnix, $consultarCargos, "consolidado" );
		$cantidadCom  = count( $registrosCom );
		$fechaInicial = $wano."-".formatearMes($wmesI)."-01";
		$ultimoDia    = mktime(0,0,0,(($wmesF*1)+1),0,$wano);
		$fechaFinal   = date("Y-m-d",$ultimoDia);
		$totalArticulosDiferentes = count($cumsConsolidado);

		$registroEncabezado[0] = "1|NI|{$nit}|".$fechaInicial."|".$fechaFinal."|".($cantidadVen+$cantidadCom)."|".$totalArticulosDiferentes."";

		$nombreArchivo = imprimirDatos($registroEncabezado, "con",$wano, $wmesI, $wmesF, "consolidado");
		$nombreArchivo = imprimirDatos($registrosVen, "con",$wano, $wmesI, $wmesF, "consolidado", $nombreArchivo);
		$nombreArchivo = imprimirDatos($registrosCom, "con",$wano, $wmesI, $wmesF, "consolidado", $nombreArchivo);
		return( $nombreArchivo );

	}

	function crearArrayCums(){
		global $conex;
		global $wbasedatos;
		global $cums;
		global $articulosSinCum;
		$q64 = "SELECT Cumcod, Cumint, Cumequ FROM {$wbasedatos}_000244 where Cumint in ( 'J01FA5' )";
		$rs64 = mysql_query($q64, $conex);
		while($row=mysql_fetch_array($rs64))
		{
			if( trim($row['Cumcod']) == "" ){
				$indice = $row['Cumint'];
				array_push( $articulosSinCum, "'".$indice."'" );
			}else{
				$indice = trim($row['Cumcod']);
			}
			$cums[$indice][$row['Cumint']]['factor']=$row['Cumequ'];

		}
		var_dump( $cums );
	}

	function formatearMes( $wmesI ){
		if(($wmesI*1)<10)
			 $wmesI = "0"."{$wmesI}";
			else
				$wmesI = "{$wmesI}";
		return( $wmesI );
	}

	function buscarConcepto( $tipo ){
		global $conex,
		       $wbasedatos;
		$concepto = "";

		switch ( $tipo ) {
			case 'V':
				$query = " SELECT Concod"
						 ."  FROM {$wbasedatos}_000008"
						 ." WHERE Conmve='on'";
				$rs = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
				$reg = mysql_fetch_row($rs);
				$concepto = $reg[0];
				break;
			case 'C';
				$query = "SELECT Concod
							FROM {$wbasedatos}_000008
						   WHERE Conind='1'
							 AND Conaca='on'
							 AND Conaco='on'
							 AND Condan='on'
							 AND Conauc='on'
							 AND Congec='off'";
				$rs = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
				$reg = mysql_fetch_row($rs);
				$concepto = $reg[0];
				break;
		}
		return( $concepto );
	}

	function consultarCodigoHabilitacion( $wemp_pmla, $variable ){
		global $conex;
		global $wemp_pmla;
		$query = "SELECT Detval
					FROM root_000051
				   WHERE Detemp = '{$wemp_pmla}'
				     AND Detapl = '{$variable}'";
		$rs    = mysql_query( $query, $conex );
		$row   = mysql_fetch_assoc( $rs );
		return( $row['Detval'] );
	}

	function datosCumXarticulo(){
		global $conex;
		global $wemp_pmla;
		global $wbasedatos;
		global $origenReporte;

		$array_cums = array();

		$query = "SELECT Cumcod, Cumint, Cumiu1, Cumiu2, Cumiu3, Cumcpc, Cumufa, Cumnex
		            FROM {$wbasedatos}_000244
		           WHERE cumint  in ( 'J01FA5' )";
		$rs    = mysql_query( $query, $conex );
		while( $row = mysql_fetch_assoc( $rs ) ){
			$indice = ( trim( $row['Cumcod'] ) == "" ) ?  $row["Cumint"] : $row["Cumcod"];
			//$indice = $row["Cumcod"];
			$array_cums[$indice]                       = array();
			$array_cums[$indice]['codint']             = $row['Cumint'];
			$array_cums[$indice]['codCum']             = $row['Cumcod'];
			$array_cums[$indice]['Ium1']               = $row['Cumiu1'];
			$array_cums[$indice]['Ium2']               = $row['Cumiu2'];
			$array_cums[$indice]['Ium3']               = $row['Cumiu3'];
			$array_cums[$indice]['codPresentacionCom'] = $row['Cumcpc'];
			$array_cums[$indice]['unidadFacturar']     = $row['Cumufa'];
			$array_cums[$indice]['numExpediente']      = $row['Cumnex'];
			$array_cums[$indice]['numExpediente']      = $row['Cumnex'];
			$array_cums[$indice]['reportaCum']         = ( trim( $row['Cumcod'] ) == "" ) ? false : true ;

		}
		return( $array_cums );
	}

//********************************************************************FIN FUNCIONES***********************************************************************************/
	 if(isset($cajax))//si se hizo una petici�n ajax.
	 {


		  if(isset($wdoc))
		  {
			$qunix = "SELECT Detval
						FROM root_000051
					   WHERE Detemp={$wemp_pmla}
						 AND Detapl='SISMED_UNIX'";
			$rsUnix        = mysql_query($qunix, $conex);
			$regUnix       = mysql_fetch_array($rsUnix);
			$consultarUnix = $regUnix[0];

			$qcargos = "SELECT Detval
						FROM root_000051
					   WHERE Detemp={$wemp_pmla}
						 AND Detapl='conCargosSismed'";
			$rscargos        = mysql_query($qcargos, $conex);
			$regCargos       = mysql_fetch_array($rscargos);
			$consultarCargos = $regCargos[0];

			$qnit = "SELECT Empnit
					   FROM root_000050
					  WHERE empcod = '{$wemp_pmla}'";
			$rsnit              = mysql_query($qnit, $conex);
			$regnit             = mysql_fetch_array($rsnit);
			$nit1               = explode("-",$regnit[0]);
			$nit                = $nit1[0];
			$dver               = $nit1[1];
			$error              = 0;
			$mensaje            = "";
			$cont               = 0;
			$cumsConsolidado    = array();
			$contConsolidado    = 0;
			$codigoHabilitacion = consultarCodigoHabilitacion( $wemp_pmla, "codigo_habilitacion" );
			$cums               = array();
			$articulosSinCum    = array();
			$ipServidor         = explode(" ",mysqli_get_host_info($conex));
			$ipServidor         = $ipServidor[0];
			crearArrayCums();

			switch ($wdoc)
			{
				case 'ventas':
					$concepto = buscarConcepto("V");
					$nombreArchivo = generarInformeVentas($concepto, $consultarUnix, $consultarCargos, "estandar");
					$respuesta = ( $error == 0 ) ? $nombreArchivo : $mensaje;
					echo $error."|".$respuesta;
					break;
				case 'compras':
					$concepto = buscarConcepto("C");
					$nombreArchivo = generarInformeCompras($concepto, $consultarUnix, $consultarCargos, "estandar");

					$respuesta = ( $error == 0 ) ? $nombreArchivo : $mensaje;
					echo $error."|".$respuesta;
					break;
				default:
					$nombreArchivo = generarNuevoInformeSismed();
					$respuesta = ( $error == 0 ) ? $nombreArchivo : $mensaje;
					echo $error."|".$respuesta;
					break;
			}
		  }
	 }
	 else{//men� principal
	 include_once("root/comun.php");
	 ?>
	 <html>
	 <head>
	 </head>
	 <script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
	 <script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
	 <script type="text/javascript">
		function quitarEnlace(ele)
		{
			ele.parentNode.removeChild(ele);
		}

		function descargar()//funcion que hace la petici�n ajax y facilita la descarga del documento.
		{
			var nombreArchivo;
			var archivo;
			var link;
			var wano = document.getElementById("wano").value
			var wfecI= document.getElementById("wmesI").value;
			var wfecF= document.getElementById("wmesF").value;
			if(wfecI>wfecF)
			{
				alert("La fecha inicial debe ser inferior a la fecha final");
			}else
			 {
				var tipodoc= document.getElementById("wdoc").value;
				var wemp_pmla= document.getElementById("wemp_pmla").value;
				var wbd= document.getElementById("wbasedatos").value;
				var parametros = "cajax=rep_Sismed&wdoc="+tipodoc+"&wmesI="+wfecI+"&wemp_pmla="+wemp_pmla+"&wmesF="+wfecF+"&wbasedatos="+wbd+"&wano="+wano;

				try
				{
					try
					{
						$.blockUI({ message: $('#msjEspere') });
					} catch(e){ }
					var ajax = nuevoAjax();
					ajax.open("POST", "rep_Sismed_test.php",true);
					ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					ajax.send(parametros);

					ajax.onreadystatechange=function()
					{
						if (ajax.readyState==4)
						{
							respuesta = ajax.responseText;
							respuesta = respuesta.split("|");
							error     = respuesta[0];
							if( error*1 == 0 ){
								nombreArchivo = trim(respuesta[1]);
								var href="rep_Sismed_test.php?ajaxdes=Sismed&wdesc="+nombreArchivo+"&wemp_pmla="+wemp_pmla;
								var link = "<a href='"+href+"' name='warchi' id='warchi' TARGET='_new' onClick='quitarEnlace(this);' >DESCARGAR ARCHIVO SISMED</a>";
								document.getElementById("desarc").innerHTML=link;
							}else{
								document.getElementById("desarc").innerHTML=respuesta[1];
							}
						}try{
								$.unblockUI();
							} catch(e){ }
					}
				}catch(e){	}
			}
		}
		 </script>
		 <body>
	<?php

		//**************************************************************FUNCIONES QUE SE EJECUTAN PARA ARMAR EL MEN� PRINCIPAL***************************************************************************************//
		 function menudocumentos()
		 {
			echo "tipo documento: <select id='wdoc' name='wdoc'>";
				  echo "<option value='consolidado' checked>Consolidado 2020</option>";
				  echo "<option value='ventas'>Ventas</option>";
				  echo "<option value='compras'>Compras</option>";
		    echo "</select>";
		 }
		 function rangoFechas()
		 {
				$a�o=date("Y");
				echo "<td class='fila2' algin=center width=250>";
				echo "A�O: <select id='wano' name='wano'>";
				for($i=$a�o; $i>1999; $i--)
				{
				  echo "<option value='".$i."'>".$i."</option>";
				}
				echo "</select>";

				echo "<td class='fila2' algin=center width=250>";
				echo "MES INICIAL: <select id='wmesI' name='wmesI'>";
				for($i=1; $i<13; $i++)
				{
				  echo "<option value='".$i."'>".$i."</option>";
				}
				echo "</select>";
				echo "</td>";

				echo "<td class='fila2' algin=center width=250>";
				echo "MES FINAL: <select id='wmesF' name='wmesF'>";
				for($i=1; $i<13; $i++)
				{
				  echo "<option value='".$i."'>".$i."</option>";
				}
				echo "</select>";
				echo "</td>";
			}

		 function menuInicial()
		 {
		 global $wemp_pmla;
		 global $wbasedatos;
			if( !isset($wmesI) )
					$wmesI = " ";//date("Y-m-01");
				if( !isset($wmesF) )
					$wmesF = " ";//date("Y-m-t");
			 echo "<center><table width=500>";
					echo "<tr>";
						echo "<td class='encabezadotabla' colspan=3 align=center>ELIJA EL RANGO DE FECHAS QUE DESEA GENERAR</td>";
					echo "</tr>";
					echo "<tr>";
						rangoFechas();
					echo "</tr>";
					echo "<tr class='fila1'>";
						echo "<td colspan=3 align='center'>";
							echo "<table width=750>";
							echo "<tr align='center'><td class='fila1' align='center'>".menudocumentos()."</td></tr>";
							echo "</table>";
						echo"</td>";
					echo "</tr>";
					echo "<tr align=center>";
						echo "<td colspan=3><input align='center' type='button' value='ACEPTAR' onclick='descargar()'></td>";
					echo "</tr>";
				echo "</table></center>";
				echo"<input type=hidden name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
				echo"<input type=hidden name='wbasedatos' id='wbasedatos' value='".$wbasedatos."'>";
				echo"<div id=desarc align='center'></div>";
		 }

		 //***********************************************************************************FIN FUNCIONES*********************************************************************************************************//
		 session_start();
		 if(!isset($_SESSION['user']))
		  echo "error";
		 else
		 {
			 if(!isset($warchi))
			 {


				$wactualiz = "2019-10-23";
				encabezado("GENERACI�N DE NUEVO INFORME SISMED",$wactualiz, "clinica");
				$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
				$wbasedatos = $institucion->baseDeDatos;

				 echo "<form name='rep_Sismed' action='rep_Sismed_test.php?wemp_pmla=".$wemp_pmla."' method=post>";
				if(!isset($wmesI) and !isset($wmesF))
				{
					menuInicial();
				}
				echo "</form>";
				echo "<br>";
				echo "<center><table>";
				echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
				echo "</table></center>";
				echo "<div id='msjEspere' name='msjEspere' style='display:none;'>";
				echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />";
				echo "</div>";
			 }
		 }
		 ?>
		 </body>
		</html>
	<?php
	}
}
?>