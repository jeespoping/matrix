<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
$wactualiz='2021-11-19';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//    2021-11-19  Daniel CB.  -Se realiza modificación de paramatro 01 quemado.
//					
// 						
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	

	include_once("root/comun.php");
	

	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$wmovhos 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wfecha			= date("Y-m-d");   
    $whora 			= date("H:i:s");

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//---------------------------------------------------------
	// --> Obtener maestro de empresas
	//---------------------------------------------------------
	function maestroEmpresas()
	{
		global $conex;
		global $wbasedato;
		
		$arrEmp = array();
		
		$sqlEmp = "
		SELECT Empcod, Empnom
		  FROM ".$wbasedato."_000024
		";
		$resEmp = mysql_query($sqlEmp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEmp):</b><br>".$sqlEmp."<br>".mysql_error());
		while($rowEmp = mysql_fetch_array($resEmp))
			$arrEmp[$rowEmp['Empcod']] = strtoupper(utf8_encode($rowEmp['Empnom']));
		
		return $arrEmp;
	}
	//---------------------------------------------------------
	// --> Obtener maestro de conceptos
	//---------------------------------------------------------
	function maestroConceptos()
	{
		global $conex;
		global $wbasedato;
		
		$arrCon = array();
		
		$sqlCon = "
		SELECT Congen, Connom 
		  FROM ".$wbasedato."_000197
		";
		$resCon = mysql_query($sqlCon, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCon):</b><br>".$sqlCon."<br>".mysql_error());
		while($rowCon = mysql_fetch_array($resCon))
			$arrCon[$rowCon['Congen']] = $rowCon['Connom'];
		
		return $arrCon;
	}
	
	//---------------------------------------------------------
	// --> Obtener maestro de procedimientos
	//---------------------------------------------------------
	function maestroProcedimientos()
	{
		global $conex;
		global $wbasedato;
		
		$arrayProcedimientos = array();
		
		$sqlPro = "
		SELECT Procod, Pronom
		  FROM ".$wbasedato."_000103
		";
		$resPro = mysql_query($sqlPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPro):</b><br>".$sqlPro."<br>".mysql_error());
		while($rowPro = mysql_fetch_array($resPro))
			$arrayProcedimientos[$rowPro['Procod']] = utf8_encode(trim($rowPro['Pronom']));
		
		return $arrayProcedimientos;
	}
	
	//---------------------------------------------------------
	// --> Obtener maestro de articulos
	//---------------------------------------------------------
	function maestroArticulos()
	{
		global $conex;
		global $wbasedato;
		global $wmovhos;
		
		$arrayArticulo = array();
		$sqlArt = "
		SELECT Artcod, Artgen
		  FROM ".$wmovhos."_000026
		";
		$resArt = mysql_query($sqlArt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlArt):</b><br>".mysql_error());
		while($rowArt = mysql_fetch_array($resArt))
			$arrayArticulo[trim($rowArt['Artcod'])] = utf8_encode(trim($rowArt['Artgen']));
		
		return $arrayArticulo;
	}
	
	//---------------------------------------------------------
	// --> Obtener maestro de cco
	//---------------------------------------------------------
	function maestroCco()
	{
		global $conex;
		global $wbasedato;
		global $wmovhos;
		
		$arrayCco = array();
		$sqlCco   = "
		SELECT Ccocod, Cconom
		  FROM ".$wmovhos."_000011
		 WHERE Ccoest = 'on'
		";
		$resCco = mysql_query($sqlCco, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCco):</b><br>".mysql_error());
		while($rowCco = mysql_fetch_array($resCco))
		{
			$rowCco['Ccocod'] = ($rowCco['Ccocod'] == '*') ? 'TODOS' : $rowCco['Ccocod'];
			
			$arrayCco[trim($rowCco['Ccocod'])] = strtoupper(utf8_encode(trim($rowCco['Cconom'])));
		}
		return $arrayCco;
	}
	
	//---------------------------------------------------------
	// --> Obtener maestro de causas
	//---------------------------------------------------------
	function maestroCausas()
	{
		global $conex;
		global $wbasedato;
		
		$arrayCausas = array("" => "PENDIENTES DE CAUSA");
		$sqlCausas 	 = "
		SELECT Caucod, Caunom
		  FROM ".$wbasedato."_000276
		 WHERE Cauest = 'on'
		";
		$resCausas = mysql_query($sqlCausas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCausas):</b><br>".mysql_error());
		while($rowCausas = mysql_fetch_array($resCausas))
			$arrayCausas[trim($rowCausas['Caucod'])] = strtoupper(utf8_encode(trim($rowCausas['Caunom'])));
		
		return $arrayCausas;
	}
	//---------------------------------------------------------
	// --> Obtener maestro de responsables (Cco+Medicos)
	//---------------------------------------------------------
	function maestroResponsables()
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wmovhos;
		
		$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
		$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
		
		$arrayRespon 	= array("" => "SIN CAUSANTE");
		$sqlRespon 		= "
		SELECT Meddoc as Codigo, CONCAT(Medno1, ' ', Medno2, ' ', Medap1, ' ', Medap2) as Nombre
		  FROM ".$wmovhos."_000048
		 WHERE Medest = 'on'
		 GROUP BY Meddoc
		 UNION
		SELECT Ccocod as Codigo, Cconom as Nombre
		  FROM ".$wmovhos."_000011
		 WHERE Ccoest = 'on' 
		";
		$resRespon = mysql_query($sqlRespon, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRespon):</b><br>".mysql_error());
		while($rowRespon = mysql_fetch_array($resRespon))
		{
			$arrayRespon[$rowRespon['Codigo']] = str_replace($caracter_ma, $caracter_ok, utf8_encode(trim($rowRespon['Nombre'])));
		}
		
		return $arrayRespon;
	}
	//---------------------------------------------------------
	// --> Obtener maestro de terceros
	//---------------------------------------------------------
	function maestroTerceros()
	{
		global $conex;
		global $wbasedato;
		
		$arrayTerceros = array();
		$sqlTerceros 	 = "
		SELECT Tercod, Ternom
		  FROM ".$wbasedato."_000196
		 WHERE Terest = 'on'
		";
		$resTerceros = mysql_query($sqlTerceros, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTerceros):</b><br>".mysql_error());
		while($rowTerceros = mysql_fetch_array($resTerceros))
			$arrayTerceros[trim($rowTerceros['Tercod'])] = strtoupper(utf8_encode(trim($rowTerceros['Ternom'])));
		
		return $arrayTerceros;
	}
	//---------------------------------------------------------
	// --> Generar el reporte principal
	//---------------------------------------------------------
	function generarReporte($fechaInicial, $fechaFinal, $campoAgrupar, $maestroPincipal, $titulo, $estadoGlo, $divPintar, $campoOrdenar)
	{
		global $conex;
		global $wbasedato;
		
		$arrayMain 			= array();
		$arrayFactDobles 	= array();
		$arrayEmpresas		= maestroEmpresas();
		$arrayConceptos		= maestroConceptos();
		$arrayProcedimientos= maestroProcedimientos();
		$arrayArticulos		= maestroArticulos();
		$arrayCausas		= maestroCausas();
		$arrayCco			= maestroCco();
		$maestroTerceros	= maestroTerceros();
		$maestroResponsables= maestroResponsables();
		
		$nomMaestroPincipal	= $maestroPincipal;
		$maestroPincipal 	= $$maestroPincipal;
		
		$totalCantGlo 	= 0;
		$totalCant 		= 0;
		$totalValor 	= 0;
		$totalValAcep 	= 0;
		
		// --> Consultar glosas
		$sqlGlo = "
		SELECT *, A.id AS NroGlo
		  FROM ".$wbasedato."_000273 AS A INNER JOIN ".$wbasedato."_000274 AS B ON(A.id = B.Gdeidg)
		 WHERE A.Fecha_data BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
		   AND Gloest = 'on'
		   AND Gloesg LIKE '".$estadoGlo."'
		 ORDER BY A.id DESC
		";
		$resGlo = mysql_query($sqlGlo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGlo):</b><br>".$sqlGlo."<br>".mysql_error());
		while($rowGlo = mysql_fetch_array($resGlo))
		{
			$rowGlo['Gdecco'] = ($rowGlo['Gdecco'] == '*') ? 'TODOS' : $rowGlo['Gdecco'];
			
			$valCampoAgrupar = $rowGlo[$campoAgrupar];
			
			// --> 	Ya que una factura se puede registrar varias veces, se hace este control para tener en cuenta una factura solamente 
			//		una vez y se tomará el ulitmo registro ingresado, osea el mas actual.			
			if(!isset($arrayFactDobles[$rowGlo['Glonfa']]))
				$arrayFactDobles[$rowGlo['Glonfa']] = $rowGlo['NroGlo'];
			elseif($arrayFactDobles[$rowGlo['Glonfa']] != $rowGlo['NroGlo'])
				continue;				
			
			if(!isset($arrayMain[$valCampoAgrupar]))
			{
				$arrayMain[$valCampoAgrupar]['CantGlo'] = 0;	
				$arrayMain[$valCampoAgrupar]['Cant']	 = 0;	
				$arrayMain[$valCampoAgrupar]['Valor']	 = 0;
				$arrayMain[$valCampoAgrupar]['ValAcep'] = 0;	
			}
			
			$totalCantGlo 	+= 1;
			$totalCant 		+= $rowGlo['Gdecgl'];
			$totalValor 	+= $rowGlo['Gdevgl'];
			$rowGlo['Gdevac']= (int)$rowGlo['Gdevac'];
			$totalValAcep 	+= $rowGlo['Gdevac'];
			
			$arrayMain[$valCampoAgrupar]['CantGlo'] += 1;
			$arrayMain[$valCampoAgrupar]['Cant']	 += $rowGlo['Gdecgl']; 	
			$arrayMain[$valCampoAgrupar]['Valor']	 += $rowGlo['Gdevgl'];
			$arrayMain[$valCampoAgrupar]['ValAcep'] += $rowGlo['Gdevac'];
			
			if(!isset($arrayMain[$valCampoAgrupar][$rowGlo['Gdecon']]))
			{
				$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['CantGlo']	= 0;	
				$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['Cant']		= 0;	
				$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['Valor']	 	= 0;
				$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['ValAcep'] 	= 0;	
			}
			
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['CantGlo']	+= 1;
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['Cant']		+= $rowGlo['Gdecgl']; 	
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['Valor']		+= $rowGlo['Gdevgl']; 	
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['ValAcep'] 	+= $rowGlo['Gdevac']; 
			
			if(!isset($arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['Cant']))
			{
				$arrayMain[$valCampoAgrupar][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['CantGlo']		= 0;
				$arrayMain[$valCampoAgrupar][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['Cant'] 		= 0;
				$arrayMain[$valCampoAgrupar][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['Valor'] 		= 0;
				$arrayMain[$valCampoAgrupar][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['ValAcep'] 	= 0;
			}
			
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['CantGlo']	+= 1;
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['Cant']		+= $rowGlo['Gdecgl']; 	
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['Valor']		+= $rowGlo['Gdevgl']; 	
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['ValAcep']	+= $rowGlo['Gdevac']; 	
			$arrayMain[$valCampoAgrupar]['DetCon'][$rowGlo['Gdecon']]['DetPro'][$rowGlo['Gdecod']]['Deta'][]     = $rowGlo; 	
		}
		
		$html = "
		<table width='100%' id='por".$campoAgrupar."' style='cursor:default'>
			<tr class='encabezadoTabla' align='center'>
				<td colspan='3'>".$titulo."</td>
				<td>Cant.Glosas</td>
				<td>Cantidad Conceptos</td>
				<td>Valor Glosado</td>
				<td>Val.Aceptado</td>
			</tr>
			<tr align='center'>
				<td colspan='3'></td>
				<td align='right'>
					<img width='16' height='14' style='cursor:pointer;".(($campoOrdenar=='CantGlo') ? "border-bottom: 1px solid #999999;" : "")."' onClick='llamarGenerarReporte(\"".$campoAgrupar."\", \"".$nomMaestroPincipal."\", \"".$divPintar."\", \"".$titulo."\", \"CantGlo\")' tooltip='si' title='Ordenar descendentemente' src='../../images/medical/sgc/ordenar.png'>
					<img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"principal\", 1, 2, \"Cantidad de glosas/".$titulo."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'>
				</td>
				<td align='right'>
					<img width='16' height='14' style='cursor:pointer;".(($campoOrdenar=='Cant') ? "border-bottom: 1px solid #999999;" : "")."' onClick='llamarGenerarReporte(\"".$campoAgrupar."\", \"".$nomMaestroPincipal."\", \"".$divPintar."\", \"".$titulo."\", \"Cant\")' tooltip='si' title='Ordenar descendentemente' src='../../images/medical/sgc/ordenar.png'>
					<img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"principal\", 1, 3, \"Cantidad/".$titulo."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'>
				</td>
				<td align='right'>
					<img width='16' height='14' style='cursor:pointer;".(($campoOrdenar=='Valor') ? "border-bottom: 1px solid #999999;" : "")."' onClick='llamarGenerarReporte(\"".$campoAgrupar."\", \"".$nomMaestroPincipal."\", \"".$divPintar."\", \"".$titulo."\", \"Valor\")' tooltip='si' title='Ordenar descendentemente' src='../../images/medical/sgc/ordenar.png'>
					<img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"principal\", 1, 4, \"Valor/".$titulo."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'>
				</td>
				<td align='right'>
					<img width='16' height='14' style='cursor:pointer;".(($campoOrdenar=='ValAcep') ? "border-bottom: 1px solid #999999;" : "")."' onClick='llamarGenerarReporte(\"".$campoAgrupar."\", \"".$nomMaestroPincipal."\", \"".$divPintar."\", \"".$titulo."\", \"ValAcep\")' tooltip='si' title='Ordenar descendentemente' src='../../images/medical/sgc/ordenar.png'>
					<img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"principal\", 1, 5, \"Valor Aceptado/".$titulo."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'>
				</td>
			</tr>
		";
		// --> Ordenar array
		$orden1 = array();
		foreach($arrayMain as $codEnt => $infoEnt)
			$orden1[$codEnt] = $infoEnt[$campoOrdenar];
		
		arsort($orden1);
			
		// --> Detalle por el campo de agrupamiento (campoAgrupar)
		foreach($orden1 as $codEnt => $x)
		{
			$infoEnt = $arrayMain[$codEnt];
			
			$html.= "
			<tr class='fondoAmarillo' clase='principal' style='border: 1px solid #999999;'>
				<td colspan='3' style='padding:0px;'>
					&nbsp;
					<img style='cursor:pointer' desplegar='' width='15px' height='15px' src='../../images/medical/sgc/mas.png' onclick='mostrarOcultar(\"".$codEnt."\", this, \"por".$campoAgrupar."\")'>&nbsp;&nbsp;
					".$codEnt."-".$maestroPincipal[$codEnt]."
				</td>
				<td align='right'>".number_format($infoEnt['CantGlo'], 0, '.', ',')."</td>
				<td align='right'>".number_format($infoEnt['Cant'], 0, '.', ',')."</td>
				<td align='right'>$ ".number_format($infoEnt['Valor'], 0, '.', ',')."</td>
				<td align='right'>$ ".number_format($infoEnt['ValAcep'], 0, '.', ',')."</td>
			</tr>
			<tr clase='".$codEnt."' style='display:none'>
				<td colspan='3'></td>
				<td align='right'><img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"".$codEnt."\", 2, 3, \"Cantidad de glosas/".$maestroPincipal[$codEnt]."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'></td>
				<td align='right'><img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"".$codEnt."\", 2, 4, \"Cantidad/".$maestroPincipal[$codEnt]."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'></td>
				<td align='right'><img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"".$codEnt."\", 2, 5, \"Valor/".$maestroPincipal[$codEnt]."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'></td>
				<td align='right'><img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"".$codEnt."\", 2, 6, \"Valor Aceptado/".$maestroPincipal[$codEnt]."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'></td>
			</tr>";	
			
			// --> Ordenar array
			$orden2 = array();
			foreach($infoEnt['DetCon'] as $concepto => $infoCon)
				$orden2[$concepto] = $infoCon[$campoOrdenar];
			
			arsort($orden2);
			$colorF = 'fila1';
			
			// --> Detalle por concepto
			foreach($orden2 as $concepto => $y)
			{
				$infoCon = $infoEnt['DetCon'][$concepto];
				//$colorF = ($colorF == 'fila2') ? 'fila1' : 'fila1';
				
				$html.= "
				<tr clase='".$codEnt."' style='display:none'>
					<td width='4%'></td>
					<td class='".$colorF."' style='padding:0px;' colspan='2'>
						&nbsp;
						<img style='cursor:pointer' desplegar='' width='15px' height='15px' src='../../images/medical/sgc/mas.png' onclick='mostrarOcultar(\"".$codEnt."-".$concepto."\", this, \"por".$campoAgrupar."\")'>&nbsp;&nbsp;
						".$concepto."-".$arrayConceptos[$concepto]."
					</td>
					<td class='".$colorF."' align='right'>".number_format($infoCon['CantGlo'], 0, '.', ',')."</td>
					<td class='".$colorF."' align='right'>".number_format($infoCon['Cant'], 0, '.', ',')."</td>
					<td class='".$colorF."' align='right'>$ ".number_format($infoCon['Valor'], 0, '.', ',')."</td>
					<td class='".$colorF."' align='right'>$ ".number_format($infoCon['ValAcep'], 0, '.', ',')."</td>
				</tr>
				<tr clase='".$codEnt."-".$concepto."' style='display:none'>
					<td colspan='3'></td>
					<td align='right'><img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"".$codEnt."-".$concepto."\", 3, 4, \"Cantidad de glosas/".$maestroPincipal[$codEnt]."/".$arrayConceptos[$concepto]."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'></td>
					<td align='right'><img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"".$codEnt."-".$concepto."\", 3, 5, \"Cantidad/".$maestroPincipal[$codEnt]."/".$arrayConceptos[$concepto]."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'></td>
					<td align='right'><img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"".$codEnt."-".$concepto."\", 3, 6, \"Valor/".$maestroPincipal[$codEnt]."/".$arrayConceptos[$concepto]."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'></td>
					<td align='right'><img width='16' height='14' style='cursor:pointer;' onClick='pintarGrafica(\"por".$campoAgrupar."\", \"".$codEnt."-".$concepto."\", 3, 7, \"Valor Aceptado/".$maestroPincipal[$codEnt]."/".$arrayConceptos[$concepto]."\")' tooltip='si' title='Graficar' src='../../images/medical/root/chart.png'></td>
				</tr>
				";
				
				// --> Ordenar array
				$orden3 = array();
				foreach($infoCon['DetPro'] as $procedimiento => $infoPro)
					$orden3[$procedimiento] = $infoPro[$campoOrdenar];
				
				arsort($orden3);
			
				// --> Detalle por procedimientos
				foreach($orden3 as $procedimiento => $z)
				{
					$infoPro = $infoCon['DetPro'][$procedimiento];
					//$colorF = ($colorF == 'fila2') ? 'fila1' : 'fila2';
					
					$nomPro = (array_key_exists($procedimiento, $arrayProcedimientos)) ? $arrayProcedimientos[$procedimiento] : $arrayArticulos[$procedimiento];
					
					$html.= "
					<tr clase='".$codEnt."-".$concepto."' style='display:none'>
						<td width='4%'></td>
						<td width='4%'></td>
						<td class='fila2' style='padding:0px;'>
							&nbsp;
							<img style='cursor:pointer' desplegar='' width='15px' height='15px' src='../../images/medical/sgc/mas.png' onclick='mostrarOcultar(\"".$codEnt."-".$concepto."-".$procedimiento."\", this, \"por".$campoAgrupar."\")'>&nbsp;&nbsp;
							".$procedimiento."-".$nomPro."
						</td>
						<td class='fila2' align='right'>".number_format($infoPro['CantGlo'], 0, '.', ',')."</td>
						<td class='fila2' align='right'>".number_format($infoPro['Cant'], 0, '.', ',')."</td>
						<td class='fila2' align='right'>$ ".number_format($infoPro['Valor'], 0, '.', ',')."</td>
						<td class='fila2' align='right'>$ ".number_format($infoPro['ValAcep'], 0, '.', ',')."</td>
					</tr>
					<tr clase='".$codEnt."-".$concepto."-".$procedimiento."' style='display:none'>
						<td colspan='2'></td>
						<td colspan='5' align='center'>
							<table width='90%'>
								<tr class='filaAzul' style='font-weight:' align='center'><td>Factura</td><td>Cantidad</td><td>Valor</td><td>Val.Aceptado</td><td>Causa</td><td>Resposable</td><td>Objeciones Auditoria</td><td>Abrir</td></tr>";
					
							// --> Detalle por factura
							foreach($infoPro['Deta'] as $idx => $infoFac)
							{
								$html.= "
								<tr>										
									<td class='fondoBlanco'>".$infoFac['Glonfa']."</td>
									<td class='fondoBlanco' align='right'>".number_format($infoFac['Gdecgl'], 0, '.', ',')."</td>
									<td class='fondoBlanco' align='right'>$ ".number_format($infoFac['Gdevgl'], 0, '.', ',')."</td>
									<td class='fondoBlanco' align='right'>$ ".@number_format($infoFac['Gdevac'], 0, '.', ',')."</td>
									<td class='fondoBlanco'>&nbsp;&nbsp;".$infoFac['Gdecau']."-".$arrayCausas[$infoFac['Gdecau']]."</td>
									<td class='fondoBlanco'>".$infoFac['Gderes']."</td>
									<td class='fondoBlanco' style='font-size:7pt'>".$infoFac['Gdeobj']."</td>
									<td class='fondoBlanco' align='center'>
										<img style='cursor:pointer' width='16px' height='16px' src='../../images/medical/sgc/verHce.png' onclick='abrirProgramaGlosas(\"".$infoFac['NroGlo']."\")'>
									</td>
								</tr>";
							}
					$html.= "
							</table>
						</td>
					</tr>
					";
				}
				
				// --> Linea en blanco
				$html.= "
					<tr clase='".$codEnt."-".$concepto."' style='display:none'>
						<td colspan='5'></td>
					</tr>";
			}
		}
		
		$html.= "
			<tr style='font-weight:bold;color:#000000;font-size:8pt;padding:1px;font-family:verdana;' align='right'>
				<td colspan='3'>Totales:</td>
				<td>".number_format($totalCantGlo, 0, '.', ',')."</td>
				<td>".number_format($totalCant, 0, '.', ',')."</td>
				<td>$ ".number_format($totalValor, 0, '.', ',')."</td>
				<td>$ ".number_format($totalValAcep, 0, '.', ',')."</td>
			</tr>
		</table>
		";
		
		return $html;
	}


//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		case 'generarReporte':
		{
			$respuesta 			= array('Msj' => FALSE, 'Html' => '');
			$html 				= generarReporte($fechaInicial, $fechaFinal, $campoAgrupar, $maestroPincipal, $titulo, $estadoGlo, $divPintar, $campoOrdenar);			
			$respuesta['Html']  = $html;
			
			echo json_encode($respuesta);
			return;
			break;
		}
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{
	?>
	<html>
	<head>
	  <title>Reporte de glosas y objeciones</title>
	</head>	
		<meta charset="UTF-8">
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
		<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>
		
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	$(function(){
		// --> Activar tabs jaquery
		$( "#tabsOpc" ).tabs({
			heightStyle: "content"
		});
		// --> Parametrización del datapicker
		cargar_elementos_datapicker();
		// --> Activar datapicker
		$("#fechaInicial").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				
			}
		});
		$("#fechaFinal").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				
			}
		});
		
		consultar();
	});
	//--------------------------------------------------------
	//	--> Activar datapicker
	//---------------------------------------------------------
	function cargar_elementos_datapicker()
	{
		$.datepicker.regional['esp'] = {
			closeText: 'Cerrar',
			prevText: 'Antes',
			nextText: 'Despues',
			monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
			'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
			'Jul','Ago','Sep','Oct','Nov','Dic'],
			dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
			dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
			dayNamesMin: ['D','L','M','M','J','V','S'],
			weekHeader: 'Sem.',
			dateFormat: 'yy-mm-dd',
			yearSuffix: ''
		};
		$.datepicker.setDefaults($.datepicker.regional['esp']);
	}
	//--------------------------------------------------------
	//	--> 
	//---------------------------------------------------------
	function selectOtras()
	{		
		if($("#selectPorOtras").val() != "")
		{
			llamarGenerarReporte($("#selectPorOtras").val(), $("#selectPorOtras option:selected").attr("maestro"), "listaPorOtras", $("#selectPorOtras option:selected").text(), "Valor");
			$("#hrefPorOtras").trigger("click");
		}
	}
	//--------------------------------------------------------
	//	--> 
	//---------------------------------------------------------
	function consultar()
	{
		$("#tabsOpc").find("li[class*=ui-state-active]").children().trigger("click");
		if($("#tabsOpc").find("li[class*=ui-state-active]").children().attr("href") == "#tabPorOtras")
			selectOtras();
	}
	//-----------------------------------------------------------------------
	// Funcion que hace el llamado al graficador en la opcion ver detalle
	//-----------------------------------------------------------------------
	function pintarGrafica(tabla, claseFila, numColumnaNom, numColumnaVal, subtitulo)
	{
		html = "";
		$("#"+tabla+" tr[clase="+claseFila+"]").each(function(){
			nombre = $(this).find("td:eq("+(numColumnaNom-1)+")").text();
			nombre = nombre.split("-");
			nombre = nombre[(nombre.length-1)];
			
			valor = $(this).find("td:eq("+(numColumnaVal-1)+")").text();
			valor = valor.split(".").join("");
			valor = valor.split(",").join("");
			valor = valor.split("$").join("");
			
			if(valor != "" && nombre != "")
				html += "<tr><td>"+nombre+"</td><td>"+valor+"</td></tr>";
		});
		
		//console.log(html);
		
		$("#prueba").html(html);
		
		$('#contGra').show();
		$('#prueba').LeerTablaAmericas(
		{
			empezardesdefila: 	0,
			titulo 			: 	subtitulo,
			datosadicionales: 	'nada'	,
			divgrafica 		:	'amchart2',
			dimension 		:	'3d'
		});
	}	
	//--------------------------------------------------------
	//	--> 
	//---------------------------------------------------------
	function llamarGenerarReporte(campoAgrupar, maestroPincipal, divPintar, titulo, campoOrdenar)
	{
		$.blockUI({
			message: "<div style='background-color: #111111;color:#ffffff;font-size: 12pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Consultando...</div>",
			css:{"border": "1pt solid #7F7F7F"}
		});
		
		$('#contGra').hide();
		$.post("reporteGlosasObjeciones.php",
		{
			consultaAjax:   		'',
			accion:         		'generarReporte',
			wemp_pmla:        		$('#wemp_pmla').val(),
			fechaInicial:			$("#fechaInicial").val(),
			fechaFinal:				$("#fechaFinal").val(),
			estadoGlo:				$("#selectEstado").val(),
			campoAgrupar:			campoAgrupar,
			maestroPincipal:		maestroPincipal,
			titulo:					titulo,
			divPintar:				divPintar,
			campoOrdenar:			campoOrdenar
			
		}, function(respuesta){
			$("#"+divPintar).html(respuesta.Html);
			$.unblockUI();
		
			// $("#porEntidad td").hover(function(){$(this).css("color","red");}, function(){$(this).css("color", "#000000");});
		}, 'json');
	}
	//--------------------------------------------------------
	//	--> Desplegar u ocultar una seccion
	//---------------------------------------------------------
	function mostrarOcultar(atributo, img, tabla)
	{
		$("#"+tabla+" [clase="+atributo+"]").toggle();
		img = $(img);
		if(img.attr("src") == "../../images/medical/sgc/mas.png")
		{
			img.attr("src",  "../../images/medical/sgc/menos.png");
		}
		else
		{
			img.attr("src",  "../../images/medical/sgc/mas.png");
			$("#"+tabla+" [clase^="+atributo+"-]").hide();
			$("#"+tabla+" [clase="+atributo+"]").find("img[desplegar]").attr("src",  "../../images/medical/sgc/mas.png");
		}
	}
	//---------------------------------------------------------
	//	--> 
	//---------------------------------------------------------
	function abrirProgramaGlosas(idGlosa)
	{
		ruta = "/matrix/ips/procesos/registroDeGlosas.php?wemp_pmla="+$wemp_pmla+"&idGlosaMostrar="+idGlosa+"";
		window.open(ruta,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
	}
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		.fila1
		{
			background-color: 	#C3D9FF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fila2
		{
			background-color: 	#E8EEF7;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fondoAmarillo {
			background-color: 	#ffffcc;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fondoBlanco {
			background-color: 	#FFFFFF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.filaAzul {
			background-color: 	#62BBE8;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		fieldset{
			border: 1px solid #000000;
			border-radius: 5px;
		}
		legend{
			border: 1px solid #000000;
			border-top: 0px;
			font-family: Verdana;
			background-color: #000000;
			color: #ffffff;
			font-size: 11pt;
		}
		#tooltip{font-family: verdana;font-weight:normal;color: #ffffff;font-size: 7pt;position:absolute;z-index:3000;border:1px solid #000000;background-color:#000000;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY>
	<?php
	// -->	ENCABEZADO
	encabezado("Reporte de glosas y objeciones", $wactualiz, 'clinica');
	
	echo "
	<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>
	
	<div id='tabsOpc' style='margin:4px'>
		<ul>
			<li id='tab1'><a href='#tabPorEntidad' 	onclick='llamarGenerarReporte(\"Gloent\", \"arrayEmpresas\", \"listaPorEntidad\", \"Entidad\", \"Valor\")'>Por entidad</a></li>
			<li id='tab2'><a href='#tabPorCco' 		onclick='llamarGenerarReporte(\"Gdecco\", \"arrayCco\", \"listaPorCco\", \"Centro de costos\", \"Valor\")'>Por centro de costos</a></li>
			<li id='tab3'><a href='#tabPorCausa'	onclick='llamarGenerarReporte(\"Gdecau\", \"arrayCausas\", \"listaPorCausa\", \"Causa\", \"Valor\")'>Por causa</a></li>
			<li id='tab4'><a href='#tabPorOtras' id='hrefPorOtras'>Por otras:</a></li>
			<li align='right'>
				<table width='100%' style='padding:6px;font-family: verdana;font-size: 10pt;color: #4C4C4C'>
					<tr>
						<td style='font-weight:normal;' align='center'>
							<select id='selectPorOtras' onchange='selectOtras()' style='font-family: verdana;font-weight:normal;font-size: 10pt;border-radius: 4px;border:1px solid #AFAFAF;width:90px'>
								<option></option>
								<option value='Gdeter' maestro='maestroTerceros'>Tercero</option>
								<option value='Gderes' maestro='maestroResponsables'>Causante</option>
							</select>
							<b>|</b>
							<span style='font-family: verdana;font-weight:normal;font-size: 10pt;'>
								Periodo de consulta:&nbsp;</b>
								<input id='fechaInicial' type='text' value='".date("Y-m-01")."' style='border-radius: 4px;border:1px solid #AFAFAF;width:90px'>
								<input id='fechaFinal' type='text' value='".date("Y-m-d")."' style='border-radius: 4px;border:1px solid #AFAFAF;width:90px'>
								<b>|</b>
								Estado:
								<select id='selectEstado' style='font-family: verdana;font-weight:normal;font-size: 10pt;border-radius: 4px;border:1px solid #AFAFAF;width:90px'>
									<option value='%'>Todos</option>
									<option value='GL'>Glosada</option>
									<option value='RA'>Auditada</option>
									<option value='GR'>Respondida</option>
									<option value='AP'>Generada(Arc Plano)</option>
									<option value='AN'>Anulada</option>
								</select>
								&nbsp;
								<img style='cursor:pointer' width='16px' height='16px' src='../../images/medical/sgc/lupa.png' onclick='consultar()' title='Consultar'>
							</span>
						</td>
					</tr>
				</table>
			</li>
		</ul>
		<div id='tabPorEntidad' align='center'>
			<br>
			<div id='listaPorEntidad' align='center'></div>
		</div>
		<div id='tabPorCco'>
			<br>
			<div id='listaPorCco'></div>		
		</div>
		<div id='tabPorCausa'>
			<br>	
			<div id='listaPorCausa'></div>	
		</div>
		<div id='tabPorOtras'>
			<br>
			<div id='listaPorOtras'></div>	
		</div>
	</div>
	<br>
	<table width='100%'>
	<tr>
		<td align='center'>
			<div id='contGra' style='display:none;'>
				<fieldset align='center' style='padding:15px;width:650px'>
					<legend class='fieldset'>Gr&aacute;fico Glosas y Objeciones</legend>
					<table width='100%'>
						<tr><td align='right'><img onClick='$(\"#contGra\").hide()' title='Cerrar' style='cursor:pointer' src='../../images/medical/eliminar1.png'></td></tr>
					</table>
					<div id='amchart2' style='font-size:5pt;width:750px; height:450px;'></div>
				</fieldset>
				<table id='prueba' style='display:none'>
				</table>
			</div>
		</td>
	</tr>
	</table>
	<br>
	<div align='center'>
		<button style='font-size:9pt' onclick='window.close()'>Cerrar Ventana</button>
	<div>
	<br>
	";
	
	?>
	</BODY>

<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
