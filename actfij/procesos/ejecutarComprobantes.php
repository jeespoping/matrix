<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	comprobantes
 * Fecha		:	2014-12-30
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	
 * Condiciones  :   
 *********************************************************************************************************
 
	
 **********************************************************************************************************/ 
$wactualiz = "2014-12-30";
 
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

//La siguiente condicion se hace porque si existe el parametro accion quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['accion'] )){
	echo "<html>";
	echo "<head>";
	echo "<title>Ejecucion Comprobantes PPyE</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
	echo '<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" />';
	echo "<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>";
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



include "../../gesapl/procesos/gestor_aplicaciones_config.php";
include_once("../../gesapl/procesos/gesapl_funciones.php");

$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "activosFijos");
$codProcesoReglasDepreciacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codProcesoAplicaReglasDepreciacion');
$codProcesoReglasTraslado = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codProcesoAplicaReglasTraslado');
include_once("actfij/funciones_activosFijos.php");
$periodo = traerPeriodoActual();
$anoActual = $periodo['ano'];
$mesActual = $periodo['mes'];

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['accion'] )){
	$accion = $_REQUEST['accion'];
	if( $accion == "ejecutarComprobantes"){
		if( $resumido == 'on' ){
			ejecutarComprobante("R", $_REQUEST['nombreComprobante'], $_REQUEST['tipo'],$_REQUEST['subtipo']);
		}else{
			ejecutarComprobante("D", $_REQUEST['nombreComprobante'], $_REQUEST['tipo'],$_REQUEST['subtipo']);
			//ejecutarComprobante($_REQUEST['tipo'],$_REQUEST['subtipo']);
		}
	}else if( $accion == 'consultarSubtipos' ){
		$arr_datos = consultarSubtipos( $tipo );
		echo json_encode($arr_datos);		
	}else if( $accion == "guardarComprobantes" ){
		guardarComprobante($fechacomprobante, $nombreComprobante);
	}		
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************

	
	function ejecutarComprobante( $resumenDetalle, $nombreComprobante, $wtipo, $wsubtipo ){
		global $conex;
		global $wbasedato;
		global $wemp_pmla;		
		global $ano;
		global $mes;
		global $codProcesoReglasDepreciacion;
		global $codProcesoReglasTraslado;
		global $user;
		global $anoActual, $mesActual;
		
		
		$wbasedatoFac 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
		
		$hayError = false;
		
		$pos = strpos($user,"-");
		$wusuario = substr($user,$pos+1,strlen($user));
		
		if( $ano == '' && $mes == '' ){
			$ano= date("Y");
			$mes= date("m");
		}	

		$componente = "";
		$arr_comprobantes = array();		
		$arr_sumas_por_tipo_cuentas = array();
		$arr_sumas_por_tipo_cuentas_detalle = array();		
		$arr_numeros_cuenta_valores = array();

		/*		
		
		CONSULTAR INFORMACION NECESARIA PARA LOS CALCULOS
		
		*/
		$aplicanComponentes = "off";
		$sql = "SELECT Mcocod as codigo, Mcocom as componentes
					   FROM ".$wbasedato."_000037
					  WHERE Mcocod = '".$nombreComprobante."'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_array($res)){
			$aplicanComponentes = $row['componentes'];
		}
		
		
		
		echo "<center>";
		
		echo "<div id='div_mensajes'></div>";
		
		// --> Consultar los estados que no son depreciables
		$estNoDepre = array();
		$sqlEstDep = "SELECT Estcod
						FROM ".$wbasedato."_000004
					   WHERE Estdep != 'on'
						 AND Estest = 'on'
		";
		$resEstDep = mysql_query($sqlEstDep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstDep):</b><br>".mysql_error());
		while($rowEstados = mysql_fetch_array($resEstDep))
			$estNoDepre[] = $rowEstados['Estcod'];		// --> Consultar los estados que no son depreciables
		
		
		$arrCamposPpye = array();
		$sql = "  SELECT Percod as tabla, Percam as campo, Perspd as sensibleporcentaje
					FROM ".$wbasedato."_000019
				   WHERE Perest = 'on'
		";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_array($res))
			$arrCamposPpye[] = $row;
		
		/*Traer los tipos de cuentas (debito, credito)*/
		$tipoCuentas = array();
		$sql = "SELECT Tdccod as codigo, Tdcdes as nombre
					   FROM ".$wbasedato."_000033
					  WHERE Tdcest = 'on'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
			foreach($row as $indice => &$valor)
				$valor = htmlentities($valor);
			$tipoCuentas[$row['codigo']] = $row['nombre'];
			$arr_sumas_por_tipo_cuentas[$row['codigo']] = 0;
			$arr_sumas_por_tipo_cuentas_detalle[$row['codigo']] = 0;
		}
		
		
		/*Arreglo que asocie los titulos de cuenta con sus respectivos numeros de cuenta*/
		$arr_titulos_cuentas = array();		
		$sqlLista = "SELECT Tcccod as codigo, Tccnom as nombre, Cuenum as numero_cuenta
					   FROM ".$wbasedato."_000027, ".$wbasedato."_000028
					  WHERE Tcccod=Cuetcc
						AND Tccano = '".$ano."'
						AND Tccmes = '".$mes."'
						AND Tccano = Cueano
						AND Tccmes = Cuemes
						AND Cueest = 'on'
						AND Tccest = 'on'
				   ORDER BY Tcccod, Cuenum";
		
		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlLista):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($resLista))
		{
			if( array_key_exists( $row['codigo'], $arr_titulos_cuentas ) == false ){
				$arr_titulos_cuentas[ $row['codigo'] ] = array('nombre'=>$row['nombre'],'cuentas'=>array());
			}
			
			$cuenta = array();
			$cuenta['codigo'] = trim($row['numero_cuenta']);
			foreach( $tipoCuentas as $codtc => $nomtc ){
				$cuenta[ $codtc ] = array(); //Se crea un arreglo con los debitos y creditos que tuvo la cuenta
			}
			$arr_titulos_cuentas[ $row['codigo'] ]['datos'] = false;
			if( in_array( $cuenta, $arr_titulos_cuentas[ $row['codigo'] ]['cuentas'] ) == false )
				array_push( $arr_titulos_cuentas[ $row['codigo'] ]['cuentas'], $cuenta );
		}
		//echo "HOLA:".json_encode($arr_titulos_cuentas);
		//--------------------------------------------------------------------//
	
		/*Traer los grupos de cco*/
		$gruposCco = array();
		$sql = "SELECT Grccod as codigo, Ccocod as cco
					   FROM ".$wbasedato."_000026, ".$wbasedatoFac."_000003
					  WHERE Grccod=Ccogru";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
			if( array_key_exists($row['codigo'], $gruposCco ) == false )
				$gruposCco[$row['codigo']] = array();
			array_push( $gruposCco[$row['codigo']], $row['cco'] );
		}
		
		/*Traer las cuentas formuladas*/
		$cuentasFormuladas = array();
		$sql = "SELECT Tcccod as codigo, Cuegra as grupoactivos, Cuegrc as grupocco, Cuenum as numcuenta, Cueval as valorcuenta, b.id
				  FROM ".$wbasedato."_000027 a LEFT JOIN ".$wbasedato."_000028 b ON (Tcccod=Cuetcc AND Cueest='on' AND Cueano = '".$ano."' AND Cuemes = '".$mes."')		
				 WHERE Tccano = '".$ano."'
				   AND Tccmes = '".$mes."'
			  ORDER BY codigo, grupoactivos, grupocco DESC "; //Desc para que primero aparezcan los grupos de cco definidos diferentes a *
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
			if( array_key_exists($row['codigo'], $cuentasFormuladas ) == false )
				$cuentasFormuladas[$row['codigo']] = array();
			array_push( $cuentasFormuladas[$row['codigo']], $row );
		}
		
		/*Traer los tipos (TRANSACCION Y PROCESO)*/
		$arr_tipos = array();
		$sql = "SELECT Tipcod as codigo, Tipdes as nombre
					   FROM ".$wbasedato."_000029
					  WHERE Tipest = 'on' ";
		if( $wtipo != "" )
			$sql.=" AND Tipcod='".$wtipo."'";

		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_array($res))
		{
			foreach($row as $indice => &$valor)
				$valor = htmlentities($valor);
			$arr_tipos[$row['codigo']] = $row['nombre'];
		}
		
		
		
		
		
		/*
		
		LLENAR EL ARREGLO CON LA CONFIGURACION DEL COMPROBANTE
		
		*/
		$arr_tiposubtipos = array();
		$arr_tiposubtiposcausas = array();
		/*POR CADA TIPO*/
		foreach( $arr_tipos as $codTipo => $nomTipo )
		{
			/*Consultar los subtipos para cada tipo*/
			$sql = "SELECT Subcod as codigo, Subdes as nombre, Subcau as causa, Subtbc as tabla, Subcmc campo
					   FROM ".$wbasedato."_000030
					  WHERE Subest = 'on' 
					    AND Subtip = '".$codTipo."'";
			if( $wsubtipo != "" )
				$sql.=" AND Subcod='".$wsubtipo."'";
			
			$resSub = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
			while($rowSub = mysql_fetch_array($resSub))
			{
				$arr_causas = array();
				$arr_causas['*'] = '*';
				
				$arr_tiposubtipos[ $codTipo."-".$rowSub['codigo'] ] = $rowSub['nombre'];
				
				/*Consultar las posibles causas*/
				if( $rowSub['causa'] == 'on' ){
					$campos = explode(",",$rowSub['campo']);
					$prefijo = substr($campos[0], 0, 3);
					
					if( count( $campos ) == 2 ){
						$sqlc = "SELECT ".$campos[0]." as codigo, ".$campos[1]." as nombre 
								   FROM ".$wbasedato."_".$rowSub['tabla']."
								  WHERE ".$prefijo."est = 'on'
								   ";
						$resc = mysql_query($sqlc, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
						if( $resc ){
							$numc = mysql_num_rows($resc);
							if( $numc > 0 ){
								while( $rowc = mysql_fetch_assoc($resc) ){
									foreach($rowc as $indice => &$valor)
										$valor = htmlentities($valor);
									
									$arr_causas[ $rowc['codigo'] ] = $rowc['nombre'];
								}
							}
						}
					}
				}
				foreach( $arr_causas as $cod_causa => $nom_causa ){
				
					$arr_tiposubtiposcausas[ $codTipo."-".$rowSub['codigo']."-".$cod_causa ] = $nom_causa;

				
					//Traer la configuracion del comprobante
					$sqlLista = "SELECT Comtdc as codigo_tdc, Comtip as codigo_tipo, a.id,
										Comsub as codigo_subtipo, Comcau as codigo_causa, Comreg as registro, Comtcc as codigo_titulocuenta, Comfor as formula,
										Tipdes as nombre_tipo,
										Tdcdes as nombre_tdb,
										Tccnom as nombre_cuenta, Tccfij as fija, Tcctab as tabla, Tcccam as campo
								   FROM ".$wbasedato."_000032 a, ".$wbasedato."_000027, ".$wbasedato."_000033, ".$wbasedato."_000029 
								  WHERE Comano = '".$ano."'
									AND Commes = '".$mes."'
									AND Tcccod=Comtcc 
									AND Tccano = '".$ano."' 
									AND Tccmes = '".$mes."' 
									AND Tccest='on'
									AND Comtdc = Tdccod
									AND Comtip = Tipcod
									AND Comnco = '".$nombreComprobante."'
									AND Comtip = '".$codTipo."'
									AND Comsub = '".$rowSub['codigo']."'
									AND Comcau = '".$cod_causa."'
									AND Comest = 'on'
									AND Tccest = 'on'
							   ORDER BY Comtip, Comsub, Comcau, Comreg, Comtdc
					";
					
					$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlLista):</b><br>".mysql_error());
					while($row = mysql_fetch_assoc($resLista))
					{
						if( array_key_exists( $row['codigo_tipo'], $arr_comprobantes ) == false ){
							$arr_comprobantes[ $row['codigo_tipo'] ] = array();
						}
						if( array_key_exists( $row['codigo_subtipo'], $arr_comprobantes[ $row['codigo_tipo'] ] ) == false ){
							$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ] = array();
						}
						if( array_key_exists( $row['codigo_causa'], $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ] ) == false ){
							$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ] = array();
						}
						if( array_key_exists( $row['registro'], $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ] ) == false ){
							$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'] ] = array();
						}
						if( array_key_exists( $row['codigo_tdc'], $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'] ] ) == false ){
							$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'] ][ $row['codigo_tdc'] ] = array();
						}
						
						$tipodbaux = "DB";
						//CLON EN TRASLADO, se concatena .1 al consecutivo del registro, y se invierten los debito y credito
						if( $row['codigo_tipo'] == '01' && $row['codigo_subtipo'] == '02' ) {						
							if( $row['codigo_tdc'] == "DB" ) $tipodbaux = "CR";
							if( array_key_exists( $row['registro'].".1", $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ] ) == false ){
								$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'].".1" ] = array();
							}
							if( array_key_exists( $tipodbaux, $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'].".1" ] ) == false ){
								$arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'].".1" ][ $tipodbaux ] = array();
							}
						}
						
						$cuenta = array();
						$cuenta['activos'] = array();
						$cuenta['codigo_titulocuenta'] = $row['codigo_titulocuenta'];
						$cuenta['nombre'] = htmlentities($row['nombre_cuenta']);
						$cuenta['tabla'] = $row['tabla'];
						$cuenta['campo'] = $row['campo'];
						$cuenta['fija'] = $row['fija'];
						$cuenta['formula'] = $row['formula'];					
						$cuenta['id'] = $row['id'];

						array_push( $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'] ][ $row['codigo_tdc'] ], $cuenta );
						
						if( $row['codigo_tipo'] == '01' && $row['codigo_subtipo'] == '02' ) {
							$cuentaAux = $cuenta;
							$cuentaAux['copia'] = true;
							array_push( $arr_comprobantes[ $row['codigo_tipo'] ][ $row['codigo_subtipo'] ][ $row['codigo_causa'] ][ $row['registro'].".1" ][ $tipodbaux ], $cuentaAux );
						}
					}
				}
			}
		}
		
		
		/*
		
		FIN LLENAR EL ARREGLO CON LA CONFIGURACION DEL COMPROBANTE
		
		*/
		
		/*
		
		FIN CONSULTAR INFORMACION NECESARIA PARA LOS CALCULOS
		
		*/
		
		
		
		
		
		
		//--------------------*********************************************----------------------------------------------*
	
		
		
		
		
		
		$html_detalle = "";
		
		$html_detalle.= "	<table style='border: 2px solid #e0e0e0;' class='tabla_comprobantes'>";
		
		$arr_tipo_subtipo_condatos = array();
		
		
		//Se muestra el comprobante y se calcula la validez del mismo, calculando las cuentas fijas en  caso de que existan
		foreach( $arr_comprobantes as $keyTipo => &$datosTipo ){
		
		$html_detalle.= "<tr class='encabezadoTabla' align='center'>
							<td colspan=".(count($tipoCuentas)+1)." style='font-size:30px'>
								".$arr_tipos[$keyTipo]."
							</td>
						</tr>";
						
						

			foreach( $datosTipo as $keySubtipo => &$datosSubtipo ){
				$html_detalle.= "<tr class='fondoGris' align='center'>
									<td colspan=".(count($tipoCuentas)+1)." style='font-size:20px'>
										<b>".$arr_tiposubtipos[$keyTipo."-".$keySubtipo]."</b>
									</td>
								</tr>
								
					<tr class='encabezadoTabla' align='center'>
												
												<td>
													Registro
												</td>";
					foreach( $tipoCuentas as $cod=>$val){
						$html_detalle.= "		<td>";
						$html_detalle.= 		$val;
						$html_detalle.= "		</td>";
					}
					$html_detalle.= "		</tr>";
				
				

				foreach( $datosSubtipo as $keyCausa => &$datosCausa ){

					$arr_lista_activos = array();
					
					/*ELEGIR LA LISTA DE ACTIVOS, SI ES 01(TRANSACCION) SON LOS ACTIVOS QUE HAN TENIDO MOVIMIENTO EN LA 16
					SI ES 02(PROCESO) SON TODOS LOS ACTIVOS*/
					if( $keyTipo == "01" ){ //TRANSACCION
						//echo " ,Traer lista transaccion";
						$subtipQ = "";
						if( $keySubtipo == '01' )
							$subtipQ = "Ingreso";
						else if($keySubtipo == '02')
							$subtipQ = "Traslado";
						else if($keySubtipo == '03')
							$subtipQ = "Retiro";
							
						$qcompon = "";
						if( $aplicanComponentes == "off" )
							$qcompon = " AND Movcom = '*' ";
							
						$sql = " SELECT Movreg as activo, Movcom as componente, id
								   FROM ".$wbasedato."_000016
								  WHERE Movest = 'on'
									AND Movano = '".$ano."'
									AND Movmes = '".$mes."'
									AND Movtip = '".$subtipQ."'
									AND Movmre = '".$keyCausa."'
									".$qcompon."
								";
						
						$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
						while($row = mysql_fetch_array($res))
						{
							array_push( $arr_lista_activos, $row );							
						}
					}else if( $keyTipo == "02" ){

						$qcompon = "";
						if( $aplicanComponentes == "off" )
							$qcompon = " AND Apmcom = '*' ";
							
						$sql = " SELECT Apmreg as activo, Apmcom as componente
								   FROM ".$wbasedato."_000038
								  WHERE Apmano = '".$ano."'
									AND Apmmes = '".$mes."'
									AND Apmsub = '".$keySubtipo."'
									".$qcompon."
									AND Apmest = 'on'";
						
						$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
						while($row = mysql_fetch_array($res))
						{
							array_push( $arr_lista_activos, $row );
						}
					}

						/*Cuando es una transaccion, existe una copia por Registro (consecutivo) del comprobante,
					La copia es para los centros de costos destino con las cuentas debito/credito invertidas.
					Esta variable indica si ya se ejecuto el comprobante con los ccos del comprobante copia*/
					$ccosDelClon = false;
					
					if( count( $arr_lista_activos ) > 0 ){
						$arr_tipo_subtipo_condatos[$keyTipo."-".$keySubtipo] = true;
					}

					foreach( $arr_lista_activos as $wactivo ){
						$wregistro = $wactivo['activo'];
					
						$componente = $wactivo['componente'];
						if( $componente == 'NO APLICA' || $componente == '*' )
							$componente = "";

						$msj_componente = "";
						if( $componente != "" )
							$msj_componente = " ,componente:".$componente;
						
						
						/*Traer el grupo de activos, del activo*/
						$grupoDelActivo = "";
						$nombreDelActivo = "";
						$sql = "";
						
						$sql = " SELECT Actgru as grupo, Actnom as nombre
								   FROM ".$wbasedato."_000001
								  WHERE Actano = '".$ano."'
									AND Actmes = '".$mes."'
									AND Actreg = '".$wregistro."'";

						$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
						if($row = mysql_fetch_assoc($res)){
							$grupoDelActivo = $row['grupo'];
							$nombreDelActivo = $row['nombre'];
						}else{
							echo "<div class='msjoculto fondoAmarillo'>El activo: ".$wregistro." no existe en la ficha</div>";
							$hayError = true;
						}
						
						if( $componente != '' ){
							$sql = " SELECT Ainnco as nombre
										   FROM ".$wbasedato."_000003
										  WHERE Ainano = '".$ano."'
											AND Ainmes = '".$mes."'
										    AND Ainreg = '".$wregistro."'
											AND Aincom = '".$componente."'
											AND Ainest = 'on'";
							$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
						
							if($row = mysql_fetch_assoc($res)){
								$msj_componente.= " ".$row['nombre'];
							}
						}
						
						$arr_cuenta_valor = array();
						$html_detalle.= "<tr class='fondoAmarillo' align='center'>
											<td colspan=".(count($tipoCuentas)+1).">
												<b>Activo: ".$wregistro."-".$nombreDelActivo." ".$msj_componente.", Causa: ".$arr_tiposubtiposcausas[$keyTipo."-".$keySubtipo."-".$keyCausa]."</b>
											</td>
										</tr>";
					
						/*------------------------CONSULTAR INFORMACION DEL ACTIVO ESPECIFICO------------------------*/
						/*Consultar los centros de costos del activo*/
						$ccosActivo = array();
						$ccosDesActivo = array();
							/*------------------------CONSULTAR INFORMACION DEL ACTIVO ESPECIFICO------------------------*/

						if( $keyTipo."-".$keySubtipo == $codProcesoReglasTraslado ){
						
							$arr_aux = array();
							$arr_auxdes = array();
							/*Consultar los centros de costos ORIGEN y DESTINO del traslado*/
							$q="  SELECT Mcccco as cod, Mccpor as porcentaje, Mccest as estados, 
										 Mccccd as coddes, Mccesd as estadosdes, Mccpod as porcentajedes
									FROM ".$wbasedato."_000036
								   WHERE Mccidm = '".$wactivo['id']."'
									";

							$resq = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX($q):</b><br>".mysql_error());
							$numq = mysql_num_rows($resq);
							if( $numq > 0 ){
								while( $rowq = mysql_fetch_assoc($resq) ){
									//Normalizar los centros de costos, puesto que un mismo centro de costos pudo haberse fraccionado
									if( array_key_exists($rowq['cod'], $arr_aux ) == false ){
										$arr_aux[$rowq['cod']]['porc'] = $rowq['porcentaje'];
										$arr_aux[$rowq['cod']]['estados'] = explode(",", $rowq['estados']);
									}else{
										$arr_aux[$rowq['cod']]['porc']+= $rowq['porcentaje']*1;
										$arr_aux[$rowq['cod']]['estados'] = array_merge( $arr_aux[$rowq['cod']]['estados'], $rowq['estados'] );
									}
									//Normalizar los centros de costos, puesto que un mismo centro de costos pudo haberse fraccionado
									if( array_key_exists($rowq['coddes'], $arr_auxdes ) == false ){
										$arr_auxdes[$rowq['coddes']]['porc'] = $rowq['porcentajedes'];
										$arr_auxdes[$rowq['coddes']]['estados'] = explode(",", $rowq['estadosdes']);
									}else{
										$arr_auxdes[$rowq['coddes']]['porc']+= $rowq['porcentajedes']*1;
										$arr_auxdes[$rowq['coddes']]['estados'] = array_merge( $arr_auxdes[$rowq['coddes']]['estados'],explode(",", $rowq['estadosdes']) );
									}
								}
								
								foreach( $arr_aux as $keyc => $valores ){
									$var_aux = array('cod'=>$keyc, 'porcentaje'=>$valores['porc'], 'estados'=>$valores['estados']);
									array_push( $ccosActivo, $var_aux );
								}
								foreach( $arr_auxdes as $keycdes => $valoresdes ){
									$var_aux = array('cod'=>$keycdes, 'porcentaje'=>$valoresdes['porc'], 'estados'=>$valoresdes['estados']);
									array_push( $ccosDesActivo, $var_aux );
								}
							}
							if( count($ccosDesActivo) == 0 ){
								echo "<div class='msjoculto fondoAmarillo'>El activo ".$wregistro." no tiene centros de costos destino definidos en el traslado</div>";
								$hayError = true;
							}
						}else{
							/*Consultar los centros de costos ACTUALES del activo*/
							
							$q="  SELECT ccacco as cod, ccapor as porcentaje, ccaest as estados
										FROM ".$wbasedato."_000017
									   WHERE Ccareg = '".$wregistro."'
										 AND Ccaano = '".$ano."'
										 AND Ccames = '".$mes."'";
							$resq = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX($q):</b><br>".mysql_error());
							$numq = mysql_num_rows($resq);
							if( $numq > 0 ){
								while( $rowq = mysql_fetch_assoc($resq) ){
									$rowq['estados'] = explode(",", $rowq['estados']);
									array_push( $ccosActivo, $rowq );
								}
							}
						}
						
						if( count($ccosActivo) == 0 ){
							echo "<div class='msjoculto fondoAmarillo'>El activo ".$wregistro." no tiene centros de costos definidos</div>";
							$hayError = true;
						}												
						
						/*Definir en base a los centros de costos del activo, los grupos de centro de costos que corresponden al activo y su porcentaje*/
						$grupos_ccos_porcentaje_activo = array(); //clave: grupo de cco, valor: el porcentaje que le corresponde de acuerdo a la cantidad de centros de costos del activo que pertenezcan al grupo de cco
						foreach( $gruposCco as $codgru=>$ccosgru ){
							foreach( $ccosActivo as &$ccoActivo ){
								if( in_array($ccoActivo['cod'], $ccosgru ) == true ){
									if(array_key_exists($codgru, $grupos_ccos_porcentaje_activo ) == false ){
										$grupos_ccos_porcentaje_activo[$codgru] = 0;
									}
									$grupos_ccos_porcentaje_activo[$codgru]+= ($ccoActivo['porcentaje'])*1;
									$ccoActivo['definidoengrupodecco'] = true;
									$ccoActivo['grupodecco'] = $codgru;
								}
							}
						}
					//	echo "<b>ccosActivo:</b> ".json_encode($ccosActivo)."<br><b>grupos_ccos_porcentaje_activo:</b> ";
					//	echo json_encode($grupos_ccos_porcentaje_activo);
						//Centro de costos que no tenga un grupo de cco definido es un error
						foreach( $ccosActivo as $ccoActivox ){
							if( isset($ccoActivox['definidoengrupodecco']) == false ){										
								echo "<div class='msjoculto fondoAmarillo'>El centro de costos: ".$ccoActivox['cod'].", definido para el activo: ".$wregistro."-".$nombreDelActivo.", con porcentaje: ".$ccoActivox['porcentaje']."%.
										No tiene definido un grupo de centros de costos</div>";
								$hayError = true;								
							}
						}					
						
						/*
						
						INICIO
						PARA LOS CAMPOS QUE AFECTA PORCENTAJE DURANTE EL PROCESO DE DEPRECIACION
						
						*/						
						$arr_depreciacion_ccosActivo = array();
						$arr_depreciacion_grupos_ccos_porcentaje_activo = array();
						
						if( $keyTipo."-".$keySubtipo == $codProcesoReglasDepreciacion ){						
							/***********************************************/						
							foreach( $ccosActivo as $datosCcoActivo ){							
								$arrEstados = $datosCcoActivo['estados'];
								$tieneEstadosQueNoDeprecian = false;
								foreach($arrEstados as $codEstAct)
								{
									// --> 	Si el estado es no depreciable, el % asignado al cco ya no se deprecia.
									if(in_array($codEstAct, $estNoDepre) ){
										$tieneEstadosQueNoDeprecian = true;
									}
								}
								if( $tieneEstadosQueNoDeprecian == false ){
									array_push( $arr_depreciacion_ccosActivo, $datosCcoActivo );
								}
							}
							if( count( $arr_depreciacion_ccosActivo ) == 0 ){
								echo "<div class='msjoculto fondoAmarillo'>NO TIENE CCOS PARA DEPRECIAR</div>";
								$hayError = true;
							}							
							/***********************************************/
						
							/*Definir en base a los centros de costos del activo, los grupos de centro de costos que corresponden al activo y su porcentaje*/
							foreach( $gruposCco as $codgru=>$ccosgru ){
								foreach( $arr_depreciacion_ccosActivo as &$ccoActivoaux ){
									if( in_array($ccoActivoaux['cod'], $ccosgru ) == true ){
										if(array_key_exists($codgru, $arr_depreciacion_grupos_ccos_porcentaje_activo ) == false ){
											$arr_depreciacion_grupos_ccos_porcentaje_activo[$codgru] = 0;
										}
										$arr_depreciacion_grupos_ccos_porcentaje_activo[$codgru]+= ($ccoActivoaux['porcentaje'])*1;
										$ccoActivoaux['definidoengrupodecco'] = true;
										$ccoActivoaux['grupodecco'] = $codgru;
									}
								}
							}
							
							//Centro de costos que no tenga un grupo de cco definido es un error
							foreach( $arr_depreciacion_ccosActivo as $ccoActivoxy ){
								if( isset($ccoActivoxy['definidoengrupodecco']) == false ){
									echo "<div class='msjoculto fondoAmarillo'>El centro de costos: ".$ccoActivoxy['cod'].", definido para el activo: ".$wregistro.", con porcentaje: ".$ccoActivoxy['porcentaje']."%.
											No tiene definido un grupo de centros de costos</div>";
									$hayError = true;
									
								}
							}
						}
						/*						
						FIN
						PARA LOS CAMPOS QUE AFECTA PORCENTAJE DURANTE EL PROCESO DE DEPRECIACION						
						*/
						
						/*SI EXISTEN CENTROS DE COSTOS DESTINO*/						
						if( count( $ccosDesActivo ) > 0 ){
						
							/*Definir en base a los centros de costos del activo, los grupos de centro de costos que corresponden al activo y su porcentaje*/
							$grupos_ccos_porcentaje_activodes = array();
							foreach( $gruposCco as $codgru=>$ccosgru ){
								foreach( $ccosDesActivo as &$ccoActivodes ){
									if( in_array($ccoActivodes['cod'], $ccosgru ) == true ){
										if(array_key_exists($codgru, $grupos_ccos_porcentaje_activodes ) == false ){
											$grupos_ccos_porcentaje_activodes[$codgru] = 0;
										}
										$grupos_ccos_porcentaje_activodes[$codgru]+= ($ccoActivodes['porcentaje'])*1;
										$ccoActivodes['definidoengrupodecco'] = true;
										$ccoActivodes['grupodecco'] = $codgru;
									}
								}
							}
							//Centro de costos que no tenga un grupo de cco definido es un error
							foreach( $ccosDesActivo as $ccoActivoxy ){
								if( isset($ccoActivoxy['definidoengrupodecco']) == false ){								 
									echo "<div class='msjoculto fondoAmarillo'>El centro de costos destino: ".$ccoActivoxy['cod'].", definido para el activo: ".$wregistro.", con porcentaje: ".$ccoActivoxy['porcentaje']."%.
											No tiene definido un grupo de centros de costos</div>";
									$hayError = true;									
								}
							}	
						}				
						
					
						/*------------------------FIN CONSULTAR INFORMACION DEL ACTIVO ESPECIFICO------------------------*/
						
						/**/
						foreach( $datosCausa as $keyRegistroyx => &$datosRegistroyx ){
							foreach( $tipoCuentas as $cod=>$val){
								if( isset( $datosRegistroyx[$cod]) ){
									foreach( $datosRegistroyx[$cod] as &$cuentasyx ){
										unset($cuentasyx['valor']);
										unset($cuentasyx['detalle']);
									}
								}
							}
						}
						$arr_cuenta_valor = array();
						//CARGAR TODOS LOS VALORES DE CADA CUENTA PARA QUE EN CASO DE QUE EXISTA UN TITULO CUENTA CON FORMULA, LAS CUENTAS QUE USE YA TENGAN VALOR
						foreach( $datosCausa as $keyRegistroy => &$datosRegistroy ){
							foreach( $tipoCuentas as $cod=>$val){
								if( isset( $datosRegistroy[$cod]) ){
									foreach( $datosRegistroy[$cod] as &$cuentasy ){
										//Calculo el valor que se llevara a la cuenta si tiene tabla y campo
										//echo "<br>==>Codigo titulo cuenta: ".$cuentasy['codigo_titulocuenta'];
										if( $cuentasy['tabla'] != "" && $cuentasy['campo'] != "" && $cuentasy['tabla'] != "*" && $cuentasy['campo'] != "*" ){
											$prefijoCampos = substr($cuentasy['campo'], 0, 3);
											$q="  SELECT ".$cuentasy['campo']."
													FROM ".$wbasedato."_".$cuentasy['tabla']."
												   WHERE ".$prefijoCampos."ano = '".$ano."'
												     AND ".$prefijoCampos."mes = '".$mes."'
													 AND ".$prefijoCampos."reg = '".$wregistro."'
											";
											if( $componente != "" ){
												$q.=" AND ".$prefijoCampos."com = '".$componente."'";
											}
										
											$resq = mysql_query($q, $conex);
											if( !$resq ){
												if( mysql_errno() == 1054 ){
													echo "<div class='msjoculto fondoAmarillo'>El campo definido para la cuenta (".$cuentasy['tabla']."-".$cuentasy['campo'].") del activo ".$wregistro.", componente: ".$componente.", se refiere a una tabla que no esta configurada para comprobantes.</div>";
													$hayError = true;
												}else{												
													echo "<div class='msjoculto fondoAmarillo'>ERROR AL CONSULTAR EL VALOR DE LA CUENTA (".$cuentasy['tabla']."-".$cuentasy['campo'].") del activo ".$wregistro.", componente: ".$componente."</div>";
													$hayError = true;
												}
											}else{
												$numq = mysql_num_rows($resq);
												if( $numq > 0 ){
													$rowq = mysql_fetch_array($resq);
													//Asumir los null como ceros
													if( $rowq[0] == "" ) $rowq[0] = 0;
													
													$cuentasy['valor'] = $rowq[0];
													$arr_cuenta_valor[ $cuentasy['codigo_titulocuenta'] ] = $cuentasy['valor'];
													//echo "<br>--->Lleva a arr_cuenta_valor[{$cuentasy['codigo_titulocuenta']}] = {$cuentasy['valor']}<br>";
												}else{
													echo "<div class='msjoculto fondoAmarillo'>ERROR</b> no consulto valor {$q}</div>";
													$hayError = true;
												}
											}
										}else{
											//echo "<br><b>NO ENTRO</b>";
										}
									}
								}
							}						
						}

						foreach( $datosCausa as $keyRegistro => &$datosRegistro ){
							$html_detalle.= "<tr class='tr_comprobante fila2' align='center'>";
							$html_detalle.= "<td>".$keyRegistro."</td>";
							foreach( $tipoCuentas as $cod=>$val){
								if( isset( $datosRegistro[$cod]) ){
									$html_detalle.= "<td>
									<ul class='lista_cuentas'>";									
									
									foreach( $datosRegistro[$cod] as &$cuentas ){
										if( $cuentas['formula'] != "" && isset($cuentas['valor']) == false ){
										//echo "<br>Calcular cuenta fija para {$keyTipo}-{$keySubtipo}-{$keyRegistro}<br>";
										//echo "<b>Cuentas</b>: ".json_encode($datosRegistro)."<br><br>";
										//echo "<b>arr_cuentas</b>: ".json_encode($arr_cuenta_valor)."<br><br>";
											$cuentas['valor'] = calcularFormulaCuentaFija($cuentas['formula'], $arr_cuenta_valor);
											if( $cuentas['valor'] == -9999 ){
												echo "<div class='msjoculto fondoAmarillo'>ERROR AL CALCULAR LA FORMULA DE LA CUENTA: ".$cuentas['nombre']."</div>";
												$hayError = true;
												$cuentas['valor']=0;
											}
										}
										
										//Dos variables que son clave para la definicion de los numeros de cuenta y ccos
										$grupos_ccos_porcentaje_activo_aux = $grupos_ccos_porcentaje_activo;
										$arr_ccos_del_activo_a_usar = array();
										$arr_ccos_del_activo_a_usar = $ccosActivo;
										
										if( count($grupos_ccos_porcentaje_activo) == 0 )
											continue;
										
										//Si se ejecuta la depreciacion, hay que redefinir las dos variables anteriores de acuerdo a la configuracion del proceso
										if( $keyTipo."-".$keySubtipo == $codProcesoReglasDepreciacion ){
											//EN EL PROCESO DE DEPRECIACION HAY CAMPOS QUE VAN A USAR EL PORCENTAJE DE LOS CENTROS DE COSTOS QUE DEPRECIAN
											foreach( $arrCamposPpye as $campoTabla ){
												if( $cuentas['tabla'] == $campoTabla['tabla'] && strtoupper($cuentas['campo']) == strtoupper($campoTabla['campo']) ){												
													if( $campoTabla['sensibleporcentaje'] == 'on' ){														
														$grupos_ccos_porcentaje_activo_aux = $arr_depreciacion_grupos_ccos_porcentaje_activo;														
														$arr_ccos_del_activo_a_usar = $arr_depreciacion_ccosActivo;														
													}
													break;
												}
											}
											
											//calcular el porcentaje que hay guardado en el total del campo.
											//En la ficha existe un porcentaje inferior, de acuerdo a la suma de los porcentajes de los cco que deprecian
											//Segun ello, se calcula el 100%
											$sumaPorcentaje = 0;
											foreach( $grupos_ccos_porcentaje_activo_aux as $ccocodx => $valporcen ){
												$sumaPorcentaje+= $valporcen*1;
											}
											if( $sumaPorcentaje != 100 ){										
												$cuentas['valorTotal'] = ($cuentas['valor']*100) / $sumaPorcentaje;												
											}
										}
										
										
										$usarCcoDestino = false;
										if( isset($cuentas['copia']) && $cuentas['copia'] == true ){ /*Si copia=true, es la copia para las transacciones traslado, se usan los ccos destino*/
											$usarCcoDestino = true;
										}
																				
										//Si se ejecuta la transaccion "traslado", hay que redefinir las dos variables anteriores de acuerdo a la configuracion del proceso
										if( $usarCcoDestino == true ){										
											$grupos_ccos_porcentaje_activo_aux = $grupos_ccos_porcentaje_activodes;
											$arr_ccos_del_activo_a_usar = $ccosDesActivo;
										}

										foreach( $arr_ccos_del_activo_a_usar as &$datoccoaux ){
											unset( $datoccoaux['listo']);
										}
									
										
										//echo "<br>-----------------------------------------<br><br>titulocuenta: ".$cuentas['nombre'];
										//echo "<br><br>arr_ccos_del_activo_a_usar: ".json_encode($arr_ccos_del_activo_a_usar	);
										
										
										
										/*
										se utilizan:
										-Los titulos de cuentas del comprobante
										-El grupo de activos
										-El grupo de centros de costos y cada porcentaje
										
										Con esta informacion se consultan las cuentas formuladas que apliquen para el [grupo de activos]
										y para cada [grupo de centros de costos] se distribuye el valor dependiendo del [porcentaje] que aplique.
										*/
										if( array_key_exists($cuentas['codigo_titulocuenta'], $cuentasFormuladas) ){
											$datosCuentas = $cuentasFormuladas[$cuentas['codigo_titulocuenta']];
											//echo "<br><br>DATOS CUENTAS: ".json_encode($datosCuentas);
													
											foreach( $arr_ccos_del_activo_a_usar as &$dato_cco ){
												if(  isset($dato_cco['listo']) == false ){
													$datoCuenta = array(	'numcuenta'=>"",
																			'grupocco'=>$dato_cco['grupodecco'],
																			'id'=>'');
												
													foreach( $datosCuentas as $posx => $datoccc ){
														if( $datoccc['grupoactivos'] == $grupoDelActivo && $datoccc['grupocco'] == $dato_cco['grupodecco'] ){
															$datoCuenta['numcuenta'] = $datoccc['numcuenta'];
															$datoCuenta['id'] = $datoccc['id'];
														}else if( $datoccc['grupoactivos'] == $grupoDelActivo &&  $datoccc['grupocco'] == '*'  ){
															$datoCuenta['numcuenta'] = $datoccc['numcuenta'];
															$datoCuenta['id'] = $datoccc['id'];
														}else if( $datoccc['grupoactivos'] == '*' && $datoccc['grupocco'] == '*' && $cuentas['formula'] != "" ){ //Para la cuenta fija
															$datoCuenta['numcuenta'] = $datoccc['numcuenta'];
															$datoCuenta['id'] = $datoccc['id'];
														}
													}

													$dato_cco['listo'] = true;
													//coinciden el titulo de cuenta, el grupo de activos y el grupo de cco
													$valorcco = $cuentas['valor']*( ($dato_cco['porcentaje']*1) / 100 ); 
													
													//Si esta definido valor total, el valor que se uso no corresponde al 100% del valor depreciado,
													if( isset( $cuentas['valorTotal'] ) ){
														$valorcco = $cuentas['valorTotal']*( ($dato_cco['porcentaje']*1) / 100 );
													}													
													
													$arr_dato = array('numero'=>$datoCuenta['numcuenta'],
																	  'id_cuenta'=>$datoCuenta['id'],
																	  'cco'=>$dato_cco['cod'],
																	  'valor_cco'=>$valorcco);
													
													if( isset( $arr_numeros_cuenta_valores[ $cuentas['codigo_titulocuenta']."-".$datoCuenta['numcuenta'] ] ) == false ) //cada key del arreglo es el numero de cuenta
														 $arr_numeros_cuenta_valores[ $cuentas['codigo_titulocuenta']."-".$datoCuenta['numcuenta'] ] = array();
													if( isset( $arr_numeros_cuenta_valores[ $cuentas['codigo_titulocuenta']."-".$datoCuenta['numcuenta'] ][$cod] ) == false ) //tambien tiene definido un arreglo con los debito y credito
														 $arr_numeros_cuenta_valores[ $cuentas['codigo_titulocuenta']."-".$datoCuenta['numcuenta'] ][$cod] = array();
													
													array_push( $arr_numeros_cuenta_valores[ $cuentas['codigo_titulocuenta']."-".$datoCuenta['numcuenta'] ][$cod], $valorcco );
													
													if( isset($cuentas['detalle']) == false )
														$cuentas['detalle'] = array();
													
													array_push( $cuentas['detalle'], $arr_dato );
													$arr_sumas_por_tipo_cuentas_detalle[$cod]+= $valorcco;
													
													$grupos_ccos_porcentaje_activo_aux[ $datoCuenta['grupocco'] ] = $grupos_ccos_porcentaje_activo_aux[ $datoCuenta['grupocco'] ] - ($dato_cco['porcentaje']*1);
													if( $grupos_ccos_porcentaje_activo_aux[ $datoCuenta['grupocco'] ] == 0 ){
														unset( $grupos_ccos_porcentaje_activo_aux[ $datoCuenta['grupocco'] ] );
													}
												}
											}
											
											//echo "<br><b>QUEDO SOBRANDO?:</b> ".json_encode($grupos_ccos_porcentaje_activo_aux);
											
											if( isset($cuentas['valor']) == false ){
												$cuentas['valor'] = -1;
												echo "<div class='msjoculto fondoAmarillo'>Error en la configuracion del comprobante, no existe valor para el titulo de cuenta: {$cuentas['codigo_titulocuenta']} ".$cuentas['nombre'].", grupo: ".$grupoDelActivo.", Activo: ".$wregistro." ".$nombreDelActivo." ".$msj_componente.", Causa: ".$arr_tiposubtiposcausas[$keyTipo."-".$keySubtipo."-".$keyCausa]."</div>";
												$hayError = true;
											}
											
											//$arr_sumas_por_tipo_cuentas[$cod]+= $cuentas['valor'];
											//$arr_sumas_por_tipo_cuentas_detalle[$cod]+= $cuentas['valor'];
											$msj = "";
											if( isset( $cuentas['msj'] ) ) $msj= " ".$cuentas['msj'];
											$html_detalle.= "<li class='item_cuenta' cuenta='".$cuentas['codigo_titulocuenta']."'>".$cuentas['nombre']." - Total: $".formato_numero($cuentas['valor']).$msj."<br>";
											//mostrar los numeros de cuenta definidos y el valor respecto al porcentaje
											if( isset($cuentas['detalle']) ){
												foreach( $cuentas['detalle'] as $xod=>$detaa ){
													$numeroMostrar = $detaa['numero'];
													if( $detaa['numero'] == "" ){
														$numeroMostrar = "<font color='red'><b>Sin número(s) de cuenta</b></font>";
														$hayError = true;
													}
													$html_detalle.= "<br>#".$numeroMostrar." = $".formato_numero($detaa['valor_cco'])."  -->cco:".$detaa['cco'];
												}
											}else{
												$html_detalle.= "<br><font color='red'><b>Sin número(s) de cuenta</b></font>";
												$hayError = true;
											}
											$html_detalle.= "<br><br></li>";
											
											$arr_activo_datos_comprobante = array();
											$arr_activo_datos_comprobante['activo'] = $wregistro;
											$arr_activo_datos_comprobante['componente'] = $componente;
											$arr_activo_datos_comprobante['valor'] = $cuentas['valor'];
											if( isset($cuentas['detalle']) ) $arr_activo_datos_comprobante['detalle'] = $cuentas['detalle'];
											array_push( $cuentas['activos'], $arr_activo_datos_comprobante );										
										}
									}
									$html_detalle.= "	</ul>
									</td>";									
								}else{
									$html_detalle.= "<td>&nbsp;</td>";	
								}
							}
							$html_detalle.= "</tr>";
							
							//total del activo
							$html_detalle.= "<tr class='fila1' align='center'>
												<td>&nbsp;</td>
													";
							foreach( $tipoCuentas as $codtx=>$valt){									
								$html_detalle.= "<td align='center'>".formato_numero($arr_sumas_por_tipo_cuentas_detalle[$codtx])."</td>";
							}
							
							foreach( $tipoCuentas as $codtx=>$valt){								
								$arr_sumas_por_tipo_cuentas[$codtx]+= $arr_sumas_por_tipo_cuentas_detalle[$codtx];
								$arr_sumas_por_tipo_cuentas_detalle[$codtx] = 0;
							}
													
							$html_detalle.= "</tr>";
						}
					}
					//echo "<br>";
				}
			}
		}
		
		/*TOTAL PARA EL COMPROBANTE*/
		$html_detalle.= "<tr><td class='encabezadoTabla'  style='font-size:20px;'>TOTAL</td>";
		
		
		
		$colorError = "class='encabezadoTabla'";
		$valAnt = -1;
		foreach( $arr_sumas_por_tipo_cuentas as $codtc => $sum ){
			if( $valAnt == -1 ) $valAnt = $sum;
			else if( $valAnt != $sum ){
				$colorError = "bgcolor='CC3300'";
				$hayError = true; //Si la suma no es igual en ambos, no se puede guardar
			}
		}
		
		$valores = array_values($arr_sumas_por_tipo_cuentas);
		//Se muestran los totales Debitos y Creditos
		foreach( $tipoCuentas as $codt=>$valt){
			if( $arr_sumas_por_tipo_cuentas[$codt] != $valores[0] )
				$valid = false;
				
			$html_detalle.= "<td  style='font-size:20px;' ".$colorError." align='center'>".formato_numero($arr_sumas_por_tipo_cuentas[$codt])."</td>";
		}
		$html_detalle.= "</tr>";
		$html_detalle.= "</table>";
		/*FIN TOTAL PARA EL COMPROBANTE*/

		if( count( $arr_comprobantes ) == 0 ){
			echo "<div class='msjoculto fondoAmarillo'>El comprobante no esta configurado correctamente</div>";
			$hayError = true;
			$valid=false;
		}
		echo "<input type='hidden' id='json_comprobante' value='".json_encode($arr_comprobantes)."' />";
		
		//$valid=true;

		/*foreach( $arr_comprobantes as $keyTipo => $datosTipox ){
			foreach( $datosTipox as $keySubtipo => $datosSubtipox ){
				if( isset( $arr_tipo_subtipo_condatos[$keyTipo."-".$keySubtipo] ) == true ){ //El tipo-subtipo tiene activos, se verifica si hay detalle
					foreach( $datosSubtipox as $keyCausa => $datosCausax ){
						foreach( $datosCausax as $keyRegistro => $datosRegistrox ){
							foreach( $tipoCuentas as $cod=>$val){
								if( isset( $datosRegistrox[$cod]) ){
									foreach( $datosRegistrox[$cod] as $cuentass ){
										if(isset($cuentass['detalle']) == false && $valid == true){
											echo "<div class='msjoculto fondoAmarillo'>El comprobante no esta configurado correctamente</div>";
											$hayError = true;
											$valid=false;
										}
									}
								}
							}
						}
					}
				}
			}
		}*/
		/*FIN TOTAL PARA LOS NUMEROS DE CUENTA DEFINIDOS EN EL COMPROBANTE*/
		
		if( $resumenDetalle == "D" ){
			echo $html_detalle;
		}else if( $resumenDetalle == "R" ){
			//Mezclar el arreglo con los numeros de cuenta como key, con el arreglo de resumen	
			//echo "A: ".json_encode($arr_titulos_cuentas['017']);			
			//echo "<br>B: ".json_encode($arr_numeros_cuenta_valores);			
			foreach( $arr_titulos_cuentas as $cod_titulocuenta => &$datosTituloCuentax ){
				$arr_aux = array(); //tendra las cuentas que tuvieron movimiento
				foreach( $datosTituloCuentax['cuentas'] as &$datosCuentaxx ){
					if( array_key_exists( $cod_titulocuenta."-".$datosCuentaxx['codigo'], $arr_numeros_cuenta_valores ) == true ){
						//La cuenta tuvo movimientos
						foreach( $tipoCuentas as $codtC => $nomtC ){
							if( isset($arr_numeros_cuenta_valores[$cod_titulocuenta."-".$datosCuentaxx['codigo']][$codtC]) ){							
								foreach( $arr_numeros_cuenta_valores[$cod_titulocuenta."-".$datosCuentaxx['codigo']][$codtC] as $valorMov )
									array_push( $datosCuentaxx[$codtC], $valorMov );
							}
						}
						$datosTituloCuentax['datos'] = true;
						array_push( $arr_aux, $datosCuentaxx );
					}
				}
				$datosTituloCuentax['cuentas'] = $arr_aux;
			}
			//echo "<br>C: ".json_encode($arr_titulos_cuentas['017']);	
			//IMPRIMIR EL RESUMEN
			
			echo "<table>";
			echo "<tr class='encabezadoTabla'>";
			echo "<td align='center'>Titulo de Cuenta</td>";
			echo "<td align='center'>Número de Cuenta</td>";
			foreach( $tipoCuentas as $codtc => $nomtc )
				echo "<td align='center'>".$nomtc."</td>";
			
			echo "</tr>";
			
			$sumas_por_cuenta = array();
			$sumas_total = array();
			foreach( $tipoCuentas as $codtc => $nomtc )
				$sumas_total[$codtc] = 0;
			
			$tr_abierto = false;
			foreach( $arr_titulos_cuentas as $cod_titulocuenta => $datosTituloCuenta ){
				if( $datosTituloCuenta['datos']  == false ){ //ninguna de las cuentas tuvo movimientos
					continue;
				}
				if(  $tr_abierto == false ){
					echo "<tr class='tr_tc fila1' align='center'>"; 
					$tr_abierto = true;
				}
				$rowspanTcc = count( $datosTituloCuenta['cuentas'] )*3;
				echo "<td rowspan=".$rowspanTcc.">".$cod_titulocuenta."-".$datosTituloCuenta['nombre']."</td>";
				foreach( $datosTituloCuenta['cuentas'] as $datosCuenta ){
					
					foreach( $tipoCuentas as $codtc => $nomtc )
						$sumas_por_cuenta[$codtc] = 0;
				
					if(  $tr_abierto == false ){
						echo "<tr class='tr_numc fila1' align='center'>"; 
						$tr_abierto = true;
					}
					//Definir el rowspan para la cuenta, los debitos + los creditos
					$rowspanCuenta = 0;
					foreach( $tipoCuentas as $codtcx => $nomtcx ){
						$rowspanCuenta+= count($datosCuenta[$codtcx]);
					}
					
					echo "<td rowspan=2>".$datosCuenta['codigo']."</td>"; //numero de cuenta
					
					$i=0;
					foreach( $tipoCuentas as $codtcx => $nomtcx ){
						if(  $tr_abierto == false ){
							echo "<tr class='tr_numc fila1' align='center'>";
							$tr_abierto = true;
						}
						if($i==1)
							echo "<td class='1'>&nbsp;</td>";
						echo "<td class='a'>";
						foreach( $datosCuenta[$codtcx] as $valor ){
							echo formato_numero($valor)."<br>";
							$sumas_por_cuenta[$codtcx]+= $valor;
						}
						echo "</td>";
						if($i==0)
							echo "<td class='2'>&nbsp;</td>";
						$i++;
						echo "</tr>";
						$tr_abierto = false;					
					}
					
					//imprimir el total de la cuenta
					echo "<tr class='encabezadoTabla'>";
					
					echo "<td align='center'>Total cuenta</td>";
					foreach( $sumas_por_cuenta as $codTc => $sumTc ){
						echo "<td align='left'>".formato_numero($sumTc)."</td>";
						$sumas_total[$codTc]+= $sumTc;
					}
					echo "</tr>";
				}
			}
			
			$colorError = "";
			$valAnt = -1;
			foreach( $sumas_total as $codtc => $sum ){
				if( $valAnt == -1 ) $valAnt = $sum;
				else if( $valAnt != $sum ){
					$colorError = "bgcolor='CC3300'";
					$hayError = true;
				}
			}
			
			echo "<tr class='encabezadoTabla'>";
			echo "<td colspan=2 align='center'>TOTAL COMPROBANTE</td>";
			foreach( $tipoCuentas as $codtc => $nomtc )
				echo "<td ".$colorError." align='center'>".formato_numero($sumas_total[$codtc])."</td>";
			echo "</tr>";
			echo "</table>";
		}
		
		if( $hayError == true ){
			echo "<br><font size=4 color='red'>CONFIGURACIÓN DE COMPROBANTE INVÁLIDO</font><br>";
		}else{			
			echo "<br><font size=4 color='blue'>CONFIGURACIÓN DE COMPROBANTE VÁLIDO</font><br>";
		}
		
		$permiteGuardar = false;
		/*Si el periodo no esta cerrado no se puede guardar*/
		$select = "SELECT  	Cieano
					 FROM  	".$wbasedato."_000035
					WHERE  	Cieano = '".$ano."'
					  AND  	Ciemes = '".$mes."'
					  AND   Cieest = 'on'";

		if( $res = mysql_query($select,$conex) ){
			$num = mysql_num_rows($res);
			if ($num > 0){
				$permiteGuardar = true;
			}
		}
	
	

		if( $permiteGuardar == false ){
			echo "<div class='msjoculto fondoRojo'>No se puede guardar, el periodo ".$ano."-".$mes." no esta cerrado</div>";						
		}else if( $hayError == true ){
			echo "<div class='msjoculto fondoRojo'>No se puede guardar, hay errores en la ejecucion.</div>";
		}else{
			
			$q= " DELETE FROM ".$wbasedato."_000039 WHERE Ecousu='".$wusuario."' AND Econom='".$nombreComprobante."'";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 
			/*GUARDAR COMPROBANTE EN LA TABLA TEMPORAL*/
			foreach( $arr_comprobantes as $keyTipo => $datosTipox ){
				foreach( $datosTipox as $keySubtipo => $datosSubtipox ){
					foreach( $datosSubtipox as $keyCausa => $datosCausax ){
						foreach( $datosCausax as $keyRegistro => $datosRegistrox ){
							foreach( $tipoCuentas as $cod=>$val){
								if( isset( $datosRegistrox[$cod]) ){
									foreach( $datosRegistrox[$cod] as $cuentass ){
										if( isset( $cuentass['activos'] ) ){
											foreach( $cuentass['activos'] as $wactivos ){
												if(isset($wactivos['detalle'])){
													foreach( $wactivos['detalle'] as $xod=>$detaa ){
			  
														$q= " INSERT INTO ".$wbasedato."_000039 (   Medico       ,   fecha_data			,   hora_data		,   		 econom    			,    ecoano    	,    ecomes    	,    ecotip		,    ecosub    		,    ecocau    	,    ecoreg    		,    ecotdc   	,    ecofec    		,   	 ecocue    		,   	 ecocco    		,  		 ecoval     		,   ecousu     		,   ecoest	,	 Seguridad          ) "
															 ."                          VALUES ('".$wbasedato."','".date('Y-m-d')."'	,'".date('H:i:s')."','".$nombreComprobante."'		,'".$ano."'		,'".$mes."'		,'".$keyTipo."'	,'".$keySubtipo."'	,'".$keyCausa."','".$keyRegistro."'	,'".$cod."'		,'".date('Y-m-d')."','".$detaa['numero']."'	,'".$detaa['cco']."'	,'".$detaa['valor_cco']."'	,'".$wusuario."'	,	'on'	, 	'C-".$wusuario."'	) ";
														$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				 
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
			
			echo "<input type='button' id='btn_guardar_comprobante' value='GUARDAR COMPROBANTE' onclick='guardarComprobante( false )' />";
		}
		
		echo "</center><br><br><br>";

		/*if( $cod == "DB" ){
			$sql = " UPDATE ".$wbasedato."_000028 
						SET cueval = cueval + ".$cuentas['valor']." 
					  WHERE cuecod='".$cuentas['codigo']."'";
		}else if( $cod == "CR" ){
			$sql = " UPDATE ".$wbasedato."_000028 
						SET cueval = cueval - ".$cuentas['valor']." 
					  WHERE cuecod='".$cuentas['codigo']."'";
		}
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());*/
			
	}
	
	function guardarComprobante($wfecha, $nombreComprobante){
		global $conex;
		global $wbasedato;
		global $user;
		global $wemp_pmla;
		global $anoActual, $mesActual;
		global $ano, $mes;
		
		if( $anoActual != $ano || $mesActual != $mes ){
			$arr_res = array('OK'=>'NO', 'msj'=> utf8_encode("El periodo ".$ano."-".$mes." no es el periodo activo."), 'link'=>'');
			echo json_encode($arr_res);
			exit;
		}
		
		$pos = strpos($user,"-");
		$wusuario = substr($user,$pos+1,strlen($user));
	
		$wexisteODBC = false;


		/*CONSULTAR LA RUTA DONDE SE GUARDARA EL COMPROBANTE EN UNIX*/
		$rutaOdbcUnix = "";
		$sql = "SELECT Mcorut as rutaunix
					   FROM ".$wbasedato."_000037
					  WHERE Mcocod = '".$nombreComprobante."'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
			$rutaOdbcUnix = $row['rutaunix'];
			if( $row['rutaunix'] != "" ){
				$wexisteODBC = true;
			}
		}

		/*Traer los tipos de cuentas (debito, credito)*/
		$tipoCuentasNUnix = array();
		$sql = "SELECT Tdccod as codigo, Tdcnux as codigoUnix
					   FROM ".$wbasedato."_000033
					  WHERE Tdcest = 'on'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
			$tipoCuentasNUnix[$row['codigo']] = $row['codigoUnix'];			
		}
		
		/*TIPO-SUBTIPO Y EL NOMBRE QUE VIAJA EN LA DESCRIPCION DEL COMPROBANTE EN UNIX*/
		$arr_tiposubtipo_nombrecunix = array();
		$arr_tiposubtipo_fuentes = array();
		$arr_tiposubtipo_documentos = array();
		$sql = "SELECT Subtip as tipo, Subcod as subtipo, Subnux as comprobantenombreunix, Subfue as fuente, Subdoc as documento
				  FROM ".$wbasedato."_000030
				 WHERE Subest = 'on' ";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
			$arr_tiposubtipo_nombrecunix[$row['tipo']."-".$row['subtipo']] = $row['comprobantenombreunix'];
			$arr_tiposubtipo_fuentes[$row['tipo']."-".$row['subtipo']] = $row['fuente'];
			$arr_tiposubtipo_documentos[$row['tipo']."-".$row['subtipo']] = $row['documento'];
		}
			
		$arr_comprobante = array();
		/*CONSULTAR EL COMPROBANTE EJECUTADO POR EL USUARIO*/
		$q = "SELECT econom as nomcomprobante, ecoano as ano, ecomes as mes, ecotip as tipo, ecosub as subtipo, ecocau as causa, ecoreg as registro, ecotdc as debitocredito, ecofec as fecha, ecocue as cuenta, ecocco as cco, ecoval as valor
			    FROM ".$wbasedato."_000039
			   WHERE ecousu = '".$wusuario."'
			     AND ecoest = 'on'
			ORDER BY econom, ecotip, ecotip, ecosub, ecocau, ecoreg, ecotdc";
			
		$res = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($rowc = mysql_fetch_assoc($res))
		{			
			array_push( $arr_comprobante, $rowc );
		}
		
		$q= "DELETE FROM ".$wbasedato."_000040 WHERE Ecousu='".$wusuario."' AND Econom='".$nombreComprobante."'";												
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			
			
		/*GUARDAR EN MATRIX*/
		/*$q = "INSERT INTO ".$wbasedato."_000040 (   Medico       ,   fecha_data			,   hora_data		, econom , ecoano , ecomes ,	 ecofec 	, 		ecofue 	, 		ecodoc 		, ecotip , ecosub , ecocau , ecoreg , ecotdc , ecocue , ecoval , 		ecousu 		, ecoest 	, Seguridad 	) 
										SELECT 	 '".$wbasedato."','".date('Y-m-d')."'	,'".date('H:i:s')."', econom , ecoano , ecomes , '".$wfecha."'	, '".$wfuente."', '".$wdocumento."'	, ecotip , ecosub , ecocau , ecoreg , ecotdc , ecocue , ecoval , '".$wusuario."'	, 'on'		, 'C-".$wusuario."'
			    FROM ".$wbasedato."_000039
			   WHERE ecousu = '".$wusuario."'
			     AND econom='".$nombreComprobante."'
			     AND ecoest = 'on'
			ORDER BY econom, ecotip, ecotip, ecosub, ecocau, ecoreg, ecotdc";*/
		$q = "INSERT INTO ".$wbasedato."_000040 (   Medico       ,   fecha_data			,   hora_data		, econom , ecoano , ecomes ,	 ecofec 	,  ecotip , ecosub , ecocau , ecoreg , ecotdc , ecocue ,  ecocco , ecoval  , 		ecousu 		, ecoest 	, Seguridad 	) 
										SELECT 	 '".$wbasedato."','".date('Y-m-d')."'	,'".date('H:i:s')."', econom , ecoano , ecomes , '".$wfecha."'	, ecotip , ecosub , ecocau , ecoreg , ecotdc , ecocue , ecocco ,	 ecoval, '".$wusuario."'	, 'on'		, 'C-".$wusuario."'
			    FROM ".$wbasedato."_000039
			   WHERE ecousu = '".$wusuario."'
			     AND econom='".$nombreComprobante."'
			     AND ecoest = 'on'
			ORDER BY econom, ecotip, ecotip, ecosub, ecocau, ecoreg, ecotdc";
			
		$res = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX($q):</b><br>".mysql_error());		
		
		$tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );
		$tieneConexionUnix = "on";
		$conexUnixPac;
		if( $tieneConexionUnix == 'on' ){
			//conexionOdbc($conex, 'movhos', &$conexUnixPac, 'facturacion');
			conexionOdbc($conex, 'movhos', &$conexUnixPac, $rutaOdbcUnix);
		}
		
		if( !$conexUnixPac ){
			$arr_res = array('OK'=>'NO', 'msj'=> utf8_encode("El ODBC no es una conexión válida"), 'link'=>'');
				echo json_encode($arr_res);
				exit;
		}
		
		/*SE GUARDA EN UNIX?*/
		if( $wexisteODBC == true && $tieneConexionUnix == "on"){
			$sal = "";
			$bas = "";
			$query="SELECT 		ciesal, ciebas
					  FROM 		sicie
					 WHERE 		ciecia='".$wemp_pmla."'
					   AND 		cieapl='CONTAB'
					   AND 		cieanc = '".$ano."'
					   AND 		ciemes = '".$mes."'";

			$err_o = odbc_do($conexUnixPac,$query) or die( "Error al consultar en SERVINTE: ".odbc_error()." $query - ".odbc_errormsg() );
			while (odbc_fetch_row($err_o)){
				$sal = odbc_result($err_o, 'ciesal');
				$bas = odbc_result($err_o, 'ciebas');				
			}
			if( $sal != "N" || $bas != "N" || $sal == "" || $bas == "" ){
				$arr_res = array('OK'=>'NO', 'msj'=> utf8_encode("El periodo {$ano}-{$mes} no está habilitado en UNIX,\nNo se puede guardar el comprobante."), 'link'=>'');
				echo json_encode($arr_res);
				exit;
			}
			
			foreach( $arr_tiposubtipo_fuentes as $keytiposubtipo => $wfuentex ){
				$wdocumentox = $arr_tiposubtipo_documentos[$keytiposubtipo];
				/*BORRAR ENCABEZADO EN UNIX*/
				$slqDelete = "DELETE FROM comovenc WHERE movencano='".$ano."' AND movencmes='".$mes."' AND movencfue='".$wfuentex."' AND movencdoc='".$wdocumentox."' AND movencusu='actfij'";
				$err_o = odbc_do( $conexUnixPac, $slqDelete );			
			}
			
			$arr_fuente_documento_creado_en_unix = array();
			foreach( $arr_comprobante as $indxy => $valorxy ){
				$wano = $valorxy['ano'];
				$wmes = $valorxy['mes'];
				$wfuente = $arr_tiposubtipo_fuentes[$valorxy['tipo']."-".$valorxy['subtipo']];
				$wdocumento = $arr_tiposubtipo_documentos[$valorxy['tipo']."-".$valorxy['subtipo']];
				
				if( in_array( $wfuente."-".$wdocumento, $arr_fuente_documento_creado_en_unix ) == false ){				
					/*GRABAR ENCABEZADO EN UNIX*/
					$sql = "INSERT INTO comovenc (		movencano	,	movencmes  ,	movencfue		,		movencdoc		,	movencusu	,	movencanu	) 
										   VALUES (		'".$wano."'	,	'".$mes."' ,	'".$wfuente."'	,	'".$wdocumento."'	,	'actfij'	,	'0'			)";
					$err_o = odbc_do($conexUnixPac,$sql);
					array_push( $arr_fuente_documento_creado_en_unix, $wfuente."-".$wdocumento );
				}
				
			}
			$wane = "";
			$wbas = 0.00;
			$wfac = 0;
			$wuni = 0.00;
			$wcam = 0.00;
			$wbaj = "N";
			$wanu = 0;
			$wcon = "";
			
			$wite=0;
			
			foreach( $arr_tiposubtipo_fuentes as $keytiposubtipo => $wfuentex ){
				$wdocumentox = $arr_tiposubtipo_documentos[$keytiposubtipo];
				/*BORRAR DETALLE EN UNIX*/
				$slqDelete = "DELETE FROM comov WHERE movfue='".$wfuentex."' AND movdoc='".$wdocumentox."' AND movano='".$ano."' AND movmes='".$mes."'";
				$err_o = odbc_do( $conexUnixPac, $slqDelete );
			}
			
				
			foreach( $arr_comprobante as $indx => $valor ){
				$wite++;
				$wano = $valor['ano'];
				$wmes = $valor['mes'];
				$wcuenta = $valor['cuenta'];
				$wcco = $valor['cco'];
				$wnit = "";
				$wvalor = $valor['valor'];
				
				$wfuente = $arr_tiposubtipo_fuentes[$valor['tipo']."-".$valor['subtipo']];
				$wdocumento = $arr_tiposubtipo_documentos[$valor['tipo']."-".$valor['subtipo']];
				
				$wdes = $arr_tiposubtipo_nombrecunix[$valor['tipo']."-".$valor['subtipo']];
				$wind = $tipoCuentasNUnix[$valor['debitocredito']];
		
				$sql = "INSERT INTO comov (		movfue			,		movdoc			,	movane		,	movano		,	movmes		,	movite		,		movfec		,		movcue		,		movcco	,		movnit	,		movdes	,		movind	,		movval		,		movcon	,		movbas	,	movfac		,	movuni		,		movcam	,		movbaj	,		movanu		) 
								   VALUES (		'".$wfuente."'	,	'".$wdocumento."'	,	'".$wane."'	,	'".$wano."'	,	'".$wmes."'	,	'".$wite."'	,	'".$wfecha."'	,	'".$wcuenta."'	,	'".$wcco."'	,	'".$wnit."'	,	'".$wdes."'	,	'".$wind."'	,	'".$wvalor."'	,	'".$wcon."',	'".$wbas."'	,	'".$wfac."'	,	'".$wuni."'	,	'".$wcam."',	'".$wbaj."'	,	'".$wanu."'		)";
				$err_o = odbc_do($conexUnixPac,$sql);
			}
			$arr_res = array('OK'=>'OK', 'msj'=> 'Exito al guardar comprobante', 'link'=>'');
			echo json_encode($arr_res);
			
			if( $tieneConexionUnix == 'on' ){
				@odbc_close( $conexUnixPac );
			}
		
		}else if( $wexisteODBC == true && $tieneConexionUnix != "on"){
			$arr_res = array('OK'=>'NO', 'msj'=> utf8_encode('Tiene definido ODBC para UNIX, pero no está habilitada su conexión.\nNo se pudo guardar comprobante.'), 'link'=>'');
			echo json_encode($arr_res);
		}else{ //Generar archivo plano
			
			$arr_res = array('OK'=>'OK', 'msj'=> utf8_encode('Exito al guardar comprobante'), 'link'=>'../reportes/archivo.txt');
			echo json_encode($arr_res);
		}
	}
	
	function calcularFormulaCuentaFija( $wformula, $warrCuentas ){
		$wformula = json_decode( $wformula, true );
		$stringEjecutar="";
		$resultado=-9999;
		foreach($wformula as $datosFormula){
			if( strtoupper($datosFormula['tipo']) == "CUENTA" ){
				$stringEjecutar.= $warrCuentas[ $datosFormula['valor'] ];
			}else if(strtoupper($datosFormula['tipo']) == "OPERADOR"){
				$stringEjecutar.= $datosFormula['valor'];
			}
		}
		eval("\$resultado = $stringEjecutar;");
		return $resultado;
	}
	
	function consultarSubtipos( $wtipo ){
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $tipoCuentasGBL;
		
		/*Traer los tipos*/
		$arr_subtipos = array();
		$sql = "SELECT Subcod as codigo, Subdes as nombre
					   FROM ".$wbasedato."_000030
					  WHERE Subtip = '".$wtipo."'
					    AND Subest = 'on'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_assoc($res))
		{
			foreach($row as $indice => &$valor)
				$valor = htmlentities($valor);
			array_push($arr_subtipos, $row );
		}
	
		return ( $arr_subtipos );
	}
	
	function panelGuardarComprogante(){
		echo "<div id='panelGuardarComprobante' style='display:none;'>
				<fieldset align='center' style='padding:15px;margin:15px'>
					<legend class='fieldset' id='legendRetiro'>Guardar</legend>
					<table>
						<!--<tr>
							<td class='fila1'>Código Fuente</td>
							<td class='fila2'>
								<input type='text' id='codigofuente' tipo='obligatorio' placeholder=' '>
							</td>
							
						</tr>-->
						<tr>
							<td class='fila1'>Fecha</td>
							<td class='fila2'>
								<input type='text' id='fechacomprobante' value='".date("Y-m-d")."' tipo='obligatorio' disabled placeholder=' '>
							</td>							
						</tr>	
						<!--<tr>
							<td class='fila1'>Número de <br>documento</td>
							<td class='fila2'>
								<input type='text' id='numerodocumento' tipo='obligatorio' placeholder=' '>
							</td>							
						</tr>-->				
					</table>
					<center>
						<input type='button' value='Guardar' onclick='guardarComprobante(true)' />&nbsp;&nbsp;&nbsp;
						<input type='button' value='Cancelar' onclick='cerrarPanelGuardarComprobante(true)' />
					</center>
				</fieldset>
				<div id='resultados'></div>
			</div>
				";
	}
	
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		global $conex;
		global $wgruposccos;
		global $wbasedato;
		global $anoActual, $mesActual;
		
	
		$width_sel = " width: 95%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
					
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		echo "<input type='hidden' id='hiddenPeriodoSeleccionado' 	value='".$anoActual."-".$mesActual."'>";
		echo "<center>";
	
		echo '<br><br>';

		echo '<div style="width: 100%">';
		
		$arr_tipos = array();
		$sql = "SELECT Tipcod as codigo, Tipdes as nombre
					   FROM ".$wbasedato."_000029
					  WHERE Tipest = 'on'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_array($res))
		{
			foreach($row as $indice => &$valor)
				$valor = htmlentities($valor);
			$arr_tipos[$row['codigo']] = $row['nombre'];
		}
		
		/*Traer el maestro nombre de comprobantes*/
		$arr_nombreComprobante = array();
		$sql = "SELECT Mcocod as codigo, Mcodes as nombre
					   FROM ".$wbasedato."_000037
					  WHERE Mcoest = 'on'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		while($row = mysql_fetch_array($res))
		{
			foreach($row as $indice => &$valor)
				$valor = htmlentities($valor);
			$arr_nombreComprobante[$row['codigo']] = $row['nombre'];
		}

	echo "	<fieldset align='center' style='padding:15px;margin:15px; width: 35%'>
					<legend class='fieldset' id='legendCuenta'>Parámetros de consulta</legend>";
		//---DIV PRINCIPAL
		echo '<table align="center">';
		
		echo "<tr><td colspan=2 class='encabezadoTabla' align='center'>Filtros</td></tr>";
		echo "<tr>";
		echo '<td class="fila1">Comprobante</td>';
		echo '<td class="fila2">';
		echo "<div align='center'>";
		echo "<select id='nombreComprobante' align='center' style='".$width_sel." margin:5px;' onchange='desactivarGuardar()'>";
		foreach ($arr_nombreComprobante as $codigog=>$nombreg)
			echo "<option value='".$codigog."'>".$nombreg."</option>";
		echo '</select>';
		echo '</div>';
		echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo '<td class="fila1">Tipo</td>';		
		echo '<td class="fila2">';
		echo "<div align='center'>";
		echo "<select id='tipo'  onchange='consultarSubtipos()' align='center' style='".$width_sel." margin:5px;'>";
		echo "<option value=''>TODOS</option>";
		foreach ($arr_tipos as $codigog=>$nombreg)
			echo "<option value='".$codigog."'>".$nombreg."</option>";
		echo '</select>';
		echo '</div>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td class="fila1">Subtipo</td>';
		echo '<td class="fila2">';
		echo "<div align='center'>";
		echo "<select id='subtipo'  align='center' style='".$width_sel." margin:5px;'>";
		echo "<option value=''>TODOS</option>";
		
		echo '</select>';
		echo '</div>';
		echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td align='center' class='fila1'>Periodo</td>";
		echo "<td class='fila2'>
				<input type='text' size='5' id='anio' value='".$anoActual."' disabled/>
				<input type='text' size='3' id='mes' value='".$mesActual."' disabled/><br>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img id='calendar' width='22' height='22' src='../../images/medical/sgc/Calendar.png' onclick='calendarioSeleccionarPeriodo(this);' style='cursor:pointer;'>
			</td>";
									
		echo "</tr>";
		echo "<tr>";
		//echo '<td class="fila1">Resumido</td>';		
		echo '<td class="fila1" colspan=2>';
		echo "<div align='center'>";
		echo "<br>Resumido <input type='radio' name='resumido' value='on' checked/> &nbsp;&nbsp;&nbsp;";	
		echo "Detallado <input type='radio' name='resumido' value='off' /><br><br>";	
		
		echo "</div>";
		echo "</td>";
		echo "</tr>";		
		echo "</table>
		</fieldset>";
		
		echo "<input type=button value='Ejecutar' onClick='javascript:ejecutarComprobantes()' />";
		echo "<br><br>";

		echo '<div id="resultados_lista" align="center"></div>';
		echo "<br><br>";

		//------FIN FORMULARIO------
		echo "</div>";//Gran contenedor		
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		
		//echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "<br><br>"; 
		echo "<br><br>";
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		//Mensaje de alertas
		echo "<div id='msjAlerta' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/root/Advertencia.png'/>";
		echo "<br><br><div id='textoAlerta'></div><br><br>";
		echo '</div>';
		echo '</center>';
		
		echo ' <div id="divCalendarioFlotante" style="display:none;z-index:10000;position: absolute;">
			<div id="calendarioFlotante" style="border:solid 1px #4C8EAF;border-radius: 4px;padding:2px;background-color: ;">
				<table>
				<tr>
					<td colspan="3" align="center" style="font-size:11pt;text-align:center;"><b>Año</b>:
						<select id="año_sel" name="año_sel" style="width:67px;border: 1px solid #4C8EAF;background-color:lightyellow;font-size:9pt;">';
						$año_inicio = 2006;
						$año_actual = date('Y');
						for($x=$año_inicio; $x <= $año_actual+1; $x++)
							echo "<option ".(($año_actual==$x)? 'SELECTED':'').">".$x."</option>";
		echo '			</select>
						<img style="cursor:pointer;width:12px;height:12px;" onClick="$(\'#divCalendarioFlotante\').hide(500);" src="../../images/medical/eliminar1.png" title="Cerrar calendario" >
					</td>
				</tr>
				<tr>
					<td id="ene" class="cuadroMes" ref="01" onClick="seleccionarPeriodoDesdeCalendario(\'01\');">Ene</td>
					<td id="feb" class="cuadroMes" ref="02" onClick="seleccionarPeriodoDesdeCalendario(\'02\');">Feb</td>
					<td id="mar" class="cuadroMes" ref="03" onClick="seleccionarPeriodoDesdeCalendario(\'03\');">Mar</td>
				</tr>
				<tr>
					<td id="abr" class="cuadroMes" ref="04" onClick="seleccionarPeriodoDesdeCalendario(\'04\');">Abr</td>
					<td id="may" class="cuadroMes" ref="05" onClick="seleccionarPeriodoDesdeCalendario(\'05\');">May</td>
					<td id="jun" class="cuadroMes" ref="06" onClick="seleccionarPeriodoDesdeCalendario(\'06\');">Jun</td>
				</tr>
				<tr>
					<td id="jul" class="cuadroMes" ref="07" onClick="seleccionarPeriodoDesdeCalendario(\'07\');">Jul</td>
					<td id="ago" class="cuadroMes" ref="08" onClick="seleccionarPeriodoDesdeCalendario(\'08\');">Ago</td>
					<td id="sep" class="cuadroMes" ref="09" onClick="seleccionarPeriodoDesdeCalendario(\'09\');">Sep</td>
				</tr>
				<tr>
					<td id="oct" class="cuadroMes" ref="10" onClick="seleccionarPeriodoDesdeCalendario(\'10\');">Oct</td>
					<td id="nov" class="cuadroMes" ref="11" onClick="seleccionarPeriodoDesdeCalendario(\'11\');">Nov</td>
					<td id="dic" class="cuadroMes" ref="12" onClick="seleccionarPeriodoDesdeCalendario(\'12\');">Dic</td>
				</tr>
				</table>
			</div>
		</div>';
		panelGuardarComprogante();
		
	}
?>
<style>	
	/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
	.ui-datepicker {font-size:12px;}
	/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
	.ui-datepicker-cover {
		display: none; /*sorry for IE5*/
		display/**/: block; /*sorry for IE5*/
		position: absolute; /*must have*/
		z-index: -1; /*must have*/
		filter: mask(); /*must have*/
		top: -4px; /*must have*/
		left: -4px; /*must have*/
		width: 200px; /*must have*/
		height: 200px; /*must have*/
	}
	
	fieldset{
		border: 2px solid #e0e0e0;
	}
	legend{
		border: 2px solid #e0e0e0;
		border-top: 0px;
		font-family: Verdana;
		background-color: #e6e6e6;
		font-size: 11pt;
	}
	
	.cuadroMes2{
		background: #2A5DB0; border: 2px solid #D3D3D3;color: #FFFFFF;font-weight: normal;outline: medium none;margin: 1px; padding: 2px;text-align: center;
	}
	.cuadroMes{
		cursor:pointer;background: #E3F1FA; border: 1px solid #62BBE8;color: #000000;font-weight: normal;outline: medium none;margin: 1px; padding: 2px;text-align: center;
	}
	.cuadroMesSeleccionado{
		cursor:pointer;background: #62BBE8; border: 1px solid #2694E8;color: #FFFFFF;font-weight: bold;outline: medium none;margin: 1px; padding: 2px;text-align: center;
	}
	
	.encabezadoTabla, .encabezadotabla {
		background-color: #2a5db0;
		color: #ffffff;
		font-size: 10pt;
		font-weight: bold;
	}
	
	.msjoculto{
		-moz-border-radius: 0.4em;
		-webkit-border-radius: 0.4em;
		border-radius: 0.4em;
		display:none;
		border: 1px solid #2A5DB0;
		padding: 5px;
		width: 450px;
		font-weight: bold;
		font-size: 11pt;
		margin: 10px;
	}

	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>

<script>
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
	
</script>
<script>
	//Variable de estado que indica si se esta moviendo un producto
	var moviendo_global = false;
	var datos_paciente = new Object();
	var mostrandoMenu = false;
	
	var url_add_params = addUrlCamposCompartidosTalento();
	
//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});
		
		$("#fechacomprobante").datepicker({
		  showOn: "button",
		  buttonImage: "../../images/medical/root/calendar.gif",
		  buttonImageOnly: true,
		  maxDate:"+0D"
		});
		
	});
	
	function consultarSubtipos(){	
		obj = $("#tipo");
		var tipo = obj.val();
		
		if( tipo == "" ){
			$("#subtipo").html("<option value=''>&nbsp;</option>");
			return;
		}
		
		$("#subtipo").attr( "tipo", tipo );
		var wemp_pmla = $("#wemp_pmla").val();
		var grupo = $("#lista_grupos").val();
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params, { wemp_pmla: wemp_pmla, accion: "consultarSubtipos", tipo: tipo, consultaAjax: "0"} ,
			function(data) {
				var subtipos = $.parseJSON(data);
				var cadena_opciones = "<option value=''>TODOS</option>";
				
				$.each( subtipos, function( key, value ) {
					cadena_opciones+="<option value='"+value.codigo+"'>"+value.nombre+"</option>";
				});
				$("#subtipo").html( cadena_opciones );
			});
	}
	
	function ejecutarComprobantes(){
	
		$.blockUI({ message: $('#msjEspere') });
		var nombreComprobante = $("#nombreComprobante").val();
		var tipo = $("#tipo").val();
		var subtipo = $("#subtipo").val();
		var anio = $("#anio").val();
		var mes = $("#mes").val();
		var resumido = $("[name=resumido]:checked").val();
		
		var wemp_pmla = $("#wemp_pmla").val();
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params, { wemp_pmla: wemp_pmla, accion: "ejecutarComprobantes", nombreComprobante: nombreComprobante, tipo: tipo, subtipo: subtipo, ano: anio, mes: mes, resumido: resumido, consultaAjax: "0"} ,
			function(data) {
				$.unblockUI();
				$('#resultados_lista').html(data);
				$("#resultados_lista").show();
				//$("#btn_consultarfechas").hide();
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$("#div_mensajes").show();
				$(".msjoculto").show();
				/*$(".msjoculto").each(function(){
					var texto = $(this).text();
					$("#div_mensajes").append("<br>"+texto);
				});*/
				
			});
	}
	
	//----------------------------------------------------
	//	--> Calendario flotante para seleccionar periodo
	//----------------------------------------------------
	function calendarioSeleccionarPeriodo(ele)
	{
		var mes_seleccionado = (($('#hiddenPeriodoSeleccionado').val() == '') ? $('#hiddenFechaActual').val() : $('#hiddenPeriodoSeleccionado').val() );
		mes_seleccionado = mes_seleccionado.split('-');
		mes_seleccionado = mes_seleccionado[1];

		$('#calendarioFlotante').find('td[ref]').each(function(index){
			$(this).addClass("cuadroMes").removeClass("cuadroMesSeleccionado");

			if(mes_seleccionado == $(this).attr('ref'))
				$(this).addClass("cuadroMesSeleccionado");
		});

		var posicion = $(ele).offset();
		console.log("left: "+posicion.left + " , top: "+posicion.top+" ,margintop: "+$(ele).css("marginTop")+" cale"+$('#divCalendarioFlotante') );

		$('#divCalendarioFlotante').css({'left':posicion.left,'top':posicion.top+24}).show(400);
	}
	
	function seleccionarPeriodoDesdeCalendario(mes)
	{
		var fecha_seleccionada = $('#año_sel').val()+'-'+mes;
		$('#hiddenPeriodoSeleccionado').val(fecha_seleccionada);
		$('#divCalendarioFlotante').hide(400);

		$("#anio").val( $('#año_sel').val() );
		$("#mes").val( mes );
	}
	
	function guardarComprobante( datosRecolectados ){
		if( datosRecolectados == false ){
			$("#panelGuardarComprobante").dialog({
				show:{
					effect: "blind",
					duration: 100
				},
				hide:{
					effect: "blind",
					duration: 100
				},
				width:  580,
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Guardar Comprobante"				
			});
		}else{
			$.blockUI({ message: $('#msjEspere') });
			var codigofuente = $("#codigofuente").val();
			var fechacomprobante = $("#fechacomprobante").val();
			var numerodocumento = $("#numerodocumento").val();
			var nombreComprobante = $("#nombreComprobante").val();			
			var wemp_pmla = $("#wemp_pmla").val();
			var anio = $("#anio").val();
			var mes = $("#mes").val();
		
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params, { wemp_pmla: wemp_pmla, accion: "guardarComprobantes",fechacomprobante: fechacomprobante,  ano: anio, mes: mes, nombreComprobante:nombreComprobante, consultaAjax: "0"} ,
			function(data) {
				$.unblockUI();
				if( isJSON(data) == false ){
					alert("RESPUESTA NO ESPERADA\n"+data);
					return;
				}			
				data = $.parseJSON(data);
				if( data.OK == "OK" ){
					alert( data.msj );
					if( data.link != "" ){
						alert("Archivo plano generado");
						$("#resultados").html("<a href='"+data.link+"' download>DESCARGAR ARCHIVO</a>");
					}
				}else{
					alert("Ocurrió un error al guardar comprobantes\n"+data.msj);
					$('#resultados').html(data);
					$("#resultados").show();
				}				
			});
		}
	}
	
	function cerrarPanelGuardarComprobante(){
		$("#panelGuardarComprobante").dialog('close');
	}
	
	function desactivarGuardar(){
		$("#btn_guardar_comprobante").hide();
	}
	
	function isJSON(data) {
		var isJson = false
		try {
			// this works with JSON string AND JSON object, not sure about others
		   var json = $.parseJSON(data);
		   isJson = typeof json === 'object' ;
		} catch (ex) {
			//console.error('data is not JSON');
		}
		return isJson;
	}

</script>
</head>
    <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>