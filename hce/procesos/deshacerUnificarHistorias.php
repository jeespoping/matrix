<?php
include_once("conex.php");
/**
 PROGRAMA                   : deshacerUnificarHistorias.php
 AUTOR                      : Frederick Aguirre.
 FECHA CREACION             : 11 de Octubre de 2012

 DESCRIPCION:
 El objetivo del programa es DESHACER LOS CAMBIOS producidos por la unificacion dos historias que se hicieron con el programa  unificarHistorias.php
 
 CAMBIOS:
 2018-09-05 - Jessica Madrid:	- En la función actualizarTablasNOERP() para unificar los formularios de HCE se consultaban todos los 
								  formularios configurados en hce_000001, se modifica el query para que solo actualice la historia e
								  ingreso de los formularios firmados (que tengan registro en hce_000036) para evitar updates innecesarios
								  que generan lentitud. Además se modifica el update para que utilice el consecutivo en las condiciones 
								  del where y así haga uso del índice conhising_idx para que el proceso de unificación de las tablas de 
								  HCE sea más rápido.
								- En la actualización de tablas de cencam se comentan los updates a la tabla cencam_000003 por los 
								  campos Observación y Habitación ya que son queries que generan lentitud y actualizando el campo 
								  Historia es suficiente y se agrega el update a todas las tablas de cencam. 
								- Se modifica la variable $wtcx para que sea multiempresa (consultando en root_000051) ya que estaba 
								  quemada y se actualizaba también en clisur y soe.
								- Se agrega en la funcion actualizarTablasNOERP() deshacer la unificación a las tablas del grupo ayucni.
								- Se modifica las validaciones para saber el prefijo de la tabla de registro de unificación de historias
								  ya que estaba tomando el prefijo mhoscs y debía ser clisur, por tal motivo no se consultaba el registro 
								  de unificación para clinica del sur.
								- En la función actualizarTablasNOERP() se agrega el update a las tablas de hce menores a hce_000051 ya que
								  no las estaba actualizando generando inconsistencias en el proceso de deshacer unificación.
								- En la función vistaInicial() se envía el logo correcto al pintar el encabezado ya que para cliame quedaba sin logo.
								- En la función desUnificarNroHistoriaERP() se corrige el query que obtiene el log de unificación ya que si se
								  estaba realizando el proceso para la empresa 01 no encontraba los registros y por tal motivo no deshacía los 
								  registros de las tablas de cliame.
								- Se agrega la validacion en la función actualizarTablasERP() para que solo actualice las tablas de 
								  farpmla para cliame, es decir, cuando wemp_pmla sea 01. 
								- Al mostrar las historias que han sido unificadas consultaba el detalle de cada una de las historias de
								  la lista, se realiza la modificación para que solo consulte el detalle de la historia sobre la que hacen 
								  clic para ver el detalle.
								- En la función desUnificarNroHistoriaNOERP() se agrega update a root_000037 para actualizar el último ingreso 
								  de las dos historias.
23 octubre 2012: 	Debido a que es posible unificar el ultimo ingreso de una historia con cualquiera de sus anteriores. (Unificar ultimo ingreso). Fue necesario
					crear un nuevo campo en la tabla de RHU y modificar el query para mostrar las historias que han sido unificadas y la unificacion del ultimo ingreso

**/


if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

$wactualiz = "2018-09-05";

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";

	echo "<title>Deshacer unificar historias</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
}

	//********************FUNCIONES COMUNES****************************//
	

	
	include_once("root/comun.php");
	

	$conex = obtenerConexionBD("matrix");
	$wmovhos = consultarPrefijosBD($conex, $wemp_pmla, 'movhos');
	$whce = consultarPrefijosBD($conex, $wemp_pmla, "hce");
	$wclisur = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");
	$wcencam = consultarPrefijosBD($conex, $wemp_pmla, "camilleros");
	$wcenpro = consultarPrefijosBD($conex, $wemp_pmla, "cenmez");
	$wchequeo = consultarPrefijosBD($conex, $wemp_pmla, "Chequeo Ejecutivo");
	$wfachos = consultarPrefijosBD($conex, $wemp_pmla, "Facturacion hospitalaria");
	$wtcx = "";//consultarPrefijosBD($conex, $wemp_pmla, "Facturacion hospitalaria");
	$wtcx = consultarPrefijosBD($conex, $wemp_pmla, "tcx");
	$wmagenta = consultarPrefijosBD($conex, $wemp_pmla, "afinidad");
	$ayucni = consultarPrefijosBD($conex, $wemp_pmla, "ayudas_diag");
	
	$log_errores = "";
	$user_session = explode('-',$_SESSION['user']);
	$user_session = $user_session[1];
	
	//FIN**COMUNES*************************************************************//

	
	//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
	if( isset($_REQUEST['action'] )){
		$action = $_REQUEST['action'];

		if($action=="desunificar"){
			desUnificarNroHistoriaNOERP( $_REQUEST['movimiento'] );
			desUnificarNroHistoriaERP( $_REQUEST['movimiento'] );
			return;
		}
		else if($action=="pintarDetalleUnificacion"){
			$data = pintarDetalleUnificacion($idElemento,$basedatos,$tablaRHU,$nroMovimiento,$historiaOrigen,$historiaDestino);
			echo json_encode($data);
			return;
		}else{
			return;
		}
	}
	//FIN*LLAMADOS*AJAX**************************************************************************************************************//

	
	//**************************FUNCIONES DE PHP********************************************//
	
	function consultarConsecutivos($conex,$wemp_pmla,$whce,$tabla)
	{
		$queryConsecutivos = "SELECT Detcon 
								FROM ".$whce."_000002 
							   WHERE Detpro='".$tabla."';";
							   
		$resConsecutivos = mysql_query($queryConsecutivos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryConsecutivos . " - " . mysql_error());		   
		$numConsecutivos = mysql_num_rows($resConsecutivos);
		
		// $arrayConsecutivos = array();
		$consecutivos = "";
		if($numConsecutivos>0)
		{
			while($rowConsecutivos = mysql_fetch_array($resConsecutivos))
			{
				$consecutivos .= $rowConsecutivos['Detcon'].",";
			}
		}
		
		$consecutivos .= consultarAliasPorAplicacion($conex, $wemp_pmla, 'consecutivosHceFirmaYNotas');
		
		return $consecutivos;
	}
	
	function pintarDetalleUnificacion($idElemento,$basedatos,$tablaRHU,$nroMovimiento,$historiaOrigen,$historiaDestino)
	{
		global $conex;
		$texto_oculto="<td colspan='6' class='fondotd'>
							<table align='center' width=80%>
							
									<tr class='fondoGris'>
										<td colspan='4' align='center'><b>CAMBIOS</b></td>
									</tr>
									<tr class='fondoGris'>
										<th colspan='2'>Antes</th>
										<th colspan='2'>Despues</th>
									</tr>
									<tr class='fondoGris'>
										<th nowrap='nowrap' class='centrar'>Historia</th>
										<th nowrap='nowrap' class='centrar'>Ingreso</th>
										<th nowrap='nowrap' class='centrar'>Historia</th>
										<th class='centrar' nowrap='nowrap'>Ingreso</th>
									</tr>";
				
				
		
		
		$query2 = " SELECT  Rhuhia as hisa, Rhuina as inga, Rhuhid as hisd, Rhuind as ingd"
		."	 FROM 	".$basedatos."_".$tablaRHU
		."  WHERE 	Rhuest = 'on'"
		." 	  AND	Rhumov = ".$nroMovimiento;
		
		$res2 = mysql_query($query2, $conex);
		while($row2 = mysql_fetch_assoc($res2)) {
			$texto_oculto.="<tr class='fondoCrema'>";
			$texto_oculto.="<td align='center' nowrap='nowrap'>".$row2['hisa']."</td>";
			$texto_oculto.="<td align='center' nowrap='nowrap'>".$row2['inga']."</td>";
			$texto_oculto.="<td align='center' nowrap='nowrap'>".$row2['hisd']."</td>";
			$texto_oculto.="<td align='center' nowrap='nowrap'>".$row2['ingd']."</td>";
			$texto_oculto.="</tr>";
		}
		$texto_oculto.="</table> ";
		$texto_oculto.="	</td> ";
		$texto_oculto.="	<td class='fondotd' align='center'> ";
							
		if( $historiaOrigen != $historiaDestino ){
			$texto_oculto.="<input type='button' class='botona' id='consultar' value='Deshacer unificacion' onclick='javascript:desUnificar(".$nroMovimiento.")'></input> ";
		}
		$texto_oculto.="	</td> ";
		
		return $texto_oculto;
	}
	
	//Para consultar el prefijo de la bd ya que la aplicacion y el codigo de la tabla RHU para las distintas empresas
	function consultarPrefijosBD($conexion, $codigoInstitucion, $nombreAplicacion){
		$q = " SELECT 	Detval  
				 FROM 	root_000051
				WHERE   Detemp = '".$codigoInstitucion."'
				  AND 	Detapl = '".$nombreAplicacion."'";
		
		$res = mysql_query($q, $conexion) ; // or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		$alias = "";
		if ($num > 0){
			$rs = mysql_fetch_array($res);
			$alias = $rs['Detval'];
		} 
		return $alias;
	}
	
	//ERP, PARA CLINICA DEL SUR Y SOE,   FARPMLA
	function desUnificarNroHistoriaERP($wmovimiento ){
		//TODO LO QUE ES FACTURACION, ADMISIONES, CARTERA ETC

		global $wclisur;
		global $wmovhos;
		global $conex;
		global $wemp_pmla;
		
		if(empty( $wclisur ) ){
			return;
		}
		
		$basedatos = "";
		//Determino en que bd se guardara el registro
		// if ( empty ( $wmovhos ) )
		if ( $wemp_pmla!="01" )
			$basedatos = $wclisur;//clisur o soe
		else
			$basedatos = $wmovhos;
		
		//Consulto cual es el numero de la tabla de registros de historias unificadas segun la empresa
		$tablaRHU = consultarPrefijosBD($conex, $wemp_pmla, 'tablarhu');	
		
		// $query_ingresos = "SELECT 	Fecha_data as fecha, Hora_data as hora, Rhuhid as historia, Rhuind as ingreso, Rhuhia as historia_destino, Rhuina as ing_ajustado"
		// ."	 FROM 	".$wclisur."_".$tablaRHU." "
		// ."	 WHERE  Rhumov = ".$wmovimiento
		// ."  ORDER BY  Fecha_data, Hora_data, Rhuhia, Rhuina";
		
		$query_ingresos = "SELECT 	Fecha_data as fecha, Hora_data as hora, Rhuhid as historia, Rhuind as ingreso, Rhuhia as historia_destino, Rhuina as ing_ajustado"
		."	 FROM 	".$basedatos."_".$tablaRHU." "
		."	 WHERE  Rhumov = ".$wmovimiento
		."  ORDER BY  Fecha_data, Hora_data, Rhuhia, Rhuina";
							
		$res_ingresos = mysql_query($query_ingresos, $conex);
		$num = mysql_num_rows($res_ingresos);
		
		$wresultado_ingresos = array();
		if ($num > 0){
			while($datatmp = mysql_fetch_assoc($res_ingresos)) {
				array_push( $wresultado_ingresos, $datatmp );
			}
		}
		
		//Se modifica el log, en donde se guarda que historia-ingreso es reemplazada por cual historia-ingreso
		borrarRegistroDeHistoriasUnificadas( $wmovimiento );
		//Se actualizan las tablas necesarias
		$isFarpmla = false;//FALSE porque se actualizan tablas de SOE Y CLISUR, no las de FARPMLA
		actualizarTablasERP($wresultado_ingresos, $isFarpmla );	
		if( empty($log_errores) ){
			echo "No se presentaron errores";
		}else{
			echo "Errores presentados: <br><br> ".$log_errores;
		}
	}
	
	function actualizarTablasERP( $datos, $isFarpmla ){

		global $wclisur;
		global $wemp_pmla;
		global $conex;
		
		global $log_errores;
		//SE ACTUALIZARAN TABLAS DE FARMPLA?
		$wclisur = "";
		if( $isFarpmla ){
			if ( $wemp_pmla == '01' || $wemp_pmla == '09' )
			{
				$wclisur = consultarPrefijosBD($conex, '09', "farpmla");
			}
		}else{
			$wclisur = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");
		}
		//Se crea un arreglo que contiene las historia-ingreso que seran cambiadas
		$array_historia_ingreso = array();
		foreach ( $datos as $fila ){
			array_push( $array_historia_ingreso, $fila['historia']."-".$fila['ingreso']);
		}
		
		$pila = array();
		
		$indice = 0;
		foreach ( $datos as $fila ){
			
			//Las historia-ingreso son iguales? si lo son no es necesario actualizar
			if( $fila['historia_destino']."-".$fila['ing_ajustado']  ==  $fila['historia']."-".$fila['ingreso']){
				continue;
			}
			
			//CONDICION para apilar, la historia-ingreso destino ya existe desde mi posicion en adelante? SI ya existe apilo para actualizar despues
			if( contieneAdelante( $fila['historia_destino']."-".$fila['ing_ajustado'], $array_historia_ingreso, $indice)){
				array_push($pila, $fila);
				continue;
			}

			$indice++;

			/*****************ACTUALIZANDO CLISUR O SOE O FARPMLA*********************/
			
			//Buscar las tablas y el campo que contiene la palabra "his"
			$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wclisur."' "
								." ORDER BY		medico, codigo";
								
			$res_his = mysql_query($query_his,$conex) ; // or die( $log_errores.= "<br>ERP, tablas que tienen historia ".mysql_errno() );  
			
			while($row_his = mysql_fetch_assoc($res_his)) {
			
				//QUE EJECUTE EL CODIGO PARA LAS TABLAS 38, 39, 40 Y 41 SOLO SI ES LA EMPRESA 02   CLISUR
				if( $row_his['tabla'] == "000038" || $row_his['tabla'] == "000039" || $row_his['tabla'] == "000040" || $row_his['tabla'] == "000041" ){
					if ( $wemp_pmla != '02' ){
						continue;
					}
				}
				
				//QUE EJECUTE EL CODIGO PARA LAS TABLAS 28, 31, 32, 34 Y 41 SOLO SI ES LA EMPRESA 07   SOE
				if( $row_his['tabla'] == "000028" || $row_his['tabla'] == "000031" || $row_his['tabla'] == "000032" || $row_his['tabla'] == "000034" || $row_his['tabla'] == "000041" ){
					if ( $wemp_pmla != '07' ){
						continue;
					}
				}
			
				//Buscar el campo de ingreso correspondiente al formulario
				$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
							."	   FROM 	det_formulario "
							."	  WHERE 	activo = 'A'  "
							."	    AND 	descripcion like '%ing%'  "
							."	    AND		medico = '".$wclisur."'"
							."	    AND		codigo = '".$row_his['tabla']."'"
							." ORDER BY		medico, codigo";
				
				//La tabla 101 tiene el campo de ingreso con la frase "nin"
				if( $row_his['tabla'] == "000101" ){
					$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
							."	   FROM 	det_formulario "
							."	  WHERE 	activo = 'A'  "
							."	    AND 	descripcion like '%nin%'  "
							."	    AND		medico = '".$wclisur."'"
							."	    AND		codigo = '".$row_his['tabla']."'"
							." ORDER BY		medico, codigo";
				}
							
				$res_ing = mysql_query($query_ing,$conex) ; // or die( $log_errores.= "<br>ERP, buscando ingreso ".mysql_errno() ); 
				$num_ing = mysql_num_rows($res_ing);
				$and_ing = "";
				$upt_ing = "";
				if( $num_ing == 1 ){
					$row_ing = mysql_fetch_assoc($res_ing);
					$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
					$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
				}
				
				//Query para actualizar la tabla, contenga o no un campo ingreso
				$query_update = "UPDATE 	".$wclisur."_".$row_his['tabla']
					."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
					." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
					.$and_ing;
				$res_update = mysql_query($query_update,$conex) ; // or die( $log_errores.= "<br>ERP, actualizando ".$row_his['tabla']." Error No:".mysql_errno() ); 
			}
			
			/*****************FIN ACTUALIZANDO CLISUR O SOE O FARPMLA*********************/
			
		}
		//$pila = array_reverse($pila);
		if(count($pila)>0){
			actualizarTablasERP( $pila, $isFarpmla );
		}
		$wclisur = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");
	}
	
	//NO ERP, PARA CLINICA LAS AMERICAS
	function desUnificarNroHistoriaNOERP($wmovimiento ){
		//MOVHOS, HCE, MAGENTA, FACHOS, CENPRO ETC
		global $conex;
		global $wmovhos;
		global $wclisur;
		global $wemp_pmla;
		
		// if(empty( $wmovhos ) ){
			// return;
		// }
	
		if ( $wemp_pmla!="01" )
			$basedatos = $wclisur;//clisur o soe
		else
			$basedatos = $wmovhos;

	
		//Consulto cual es el numero de la tabla de registros de historias unificadas segun la empresa
		$tablaRHU = consultarPrefijosBD($conex, $wemp_pmla, 'tablarhu');	
		
		$query_ingresos = "SELECT 	Fecha_data as fecha, Hora_data as hora, Rhuhid as historia, Rhuind as ingreso, Rhuhia as historia_destino, Rhuina as ing_ajustado"
		."	 FROM 	".$basedatos."_".$tablaRHU." "
		."	 WHERE  Rhumov = ".$wmovimiento;
		//."  ORDER BY  Fecha_data, Hora_data, Rhuhia, Rhuina";
							
		$res_ingresos = mysql_query($query_ingresos, $conex);
		$num = mysql_num_rows($res_ingresos);
		
		$wresultado_ingresos = array();
		$arrayUltimoIngreso = array();
		if ($num > 0){
			while($datatmp = mysql_fetch_assoc($res_ingresos)) {
				array_push( $wresultado_ingresos, $datatmp );
				
				if($datatmp['ing_ajustado'] > $arrayUltimoIngreso[$datatmp['historia_destino']])
				{
					$arrayUltimoIngreso[$datatmp['historia_destino']] = $datatmp['ing_ajustado'];
				}
			}
		}
		
		if(count($arrayUltimoIngreso)>0)
		{
			foreach($arrayUltimoIngreso as $keyHistoria => $valueIngreso)
			{
				//ACTUALIZO EN LA TABLA ROOT 37
				$query = " UPDATE root_000037 
							  SET Oriing='".$valueIngreso."' 
							WHERE Orihis = '".$keyHistoria."' 
							  AND Oriori='".$wemp_pmla."'";
				$res = mysql_query($query, $conex);
			}
		}
		
		//Se crea el log, en donde se guarda que historia-ingreso es reemplazada por cual historia-ingreso
		borrarRegistroDeHistoriasUnificadas( $wmovimiento );
		
		//Se actualizan las tablas necesarias
		actualizarTablasNOERP($wresultado_ingresos );
		
		
		//Se actualizan tablas de ERP para FARPMLA, ya que funciona similar a SOE Y CLISUR, pero
		//enviando un parametro indicando que es para farpmla
		$isFarpmla = true;
		actualizarTablasERP($wresultado_ingresos, $isFarpmla );
		if( empty($log_errores) ){
			echo "No se presentaron errores";
		}else{
			echo "Errores presentados: <br><br> ".$log_errores;
		}
	}
	
	function actualizarTablasNOERP( $datos ){
	
		global $conex;
		global $wmovhos;
		global $whce;
		global $wmagenta;
		global $wchequeo;
		global $wtcx;
		global $wcencam;
		global $wcenpro;
		global $wfachos;
		global $ayucni;
		global $wemp_pmla;
		
		global $log_errores;
		
		//Se crea un arreglo que contiene las historia-ingreso que seran cambiadas
		$array_historia_ingreso = array();

		foreach ( $datos as $fila ){
			array_push( $array_historia_ingreso, $fila['historia']."-".$fila['ingreso']);
		}
		
		$pila = array();
		$indice = 0;
		foreach ( $datos as $fila ){
			//Si la historia-ingreso ORIGEN son el mismo historia-ingreso DESTINO, NO ACTUALIZE TABLAS
			if( $fila['historia_destino']."-".$fila['ing_ajustado']  ==  $fila['historia']."-".$fila['ingreso']){
				continue;
			}
			
			if( contieneAdelante( $fila['historia_destino']."-".$fila['ing_ajustado'], $array_historia_ingreso, $indice)){
				array_push($pila, $fila);
				continue;
			}

			$indice++;
			
			//Verifica que exista el prefijo HCE para la empresa
			if(! empty( $whce ) ){
				
					/*****************ACTUALIZANDO HCE DE LA 51 HACIA ATRAS*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$whce."' "
								."ORDER BY		medico, codigo";

					$res_his = mysql_query($query_his,$conex);

					while($row_his = mysql_fetch_assoc($res_his)) {
						if( (int)$row_his['tabla'] < 51 ){

							$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
										."	   FROM 	det_formulario "
										."	  WHERE 	activo = 'A'  "
										."	    AND 	descripcion like '%ing%'  "
										."	    AND		medico = '".$whce."'"
										."	    AND		codigo = '".$row_his['tabla']."'"
										." ORDER BY		medico, codigo";

							$res_ing = mysql_query($query_ing,$conex);
							$num_ing = mysql_num_rows($res_ing);
							$and_ing = "";
							$upt_ing = "";
							if( $num_ing == 1 ){
								$row_ing = mysql_fetch_assoc($res_ing);
								$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
								$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
							}
							$query_update = " UPDATE 	".$whce."_".$row_his['tabla']
											."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
											." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
											.$and_ing;
							$res_update = mysql_query($query_update,$conex) ;
						}
					}
					/*****************FIN ACTUALIZANDO HCE DE LA 51 HACIA ATRAS*********************/

					/*ACTUALIZANDO LAS TABLAS DE HCE DE LA 51 EN ADELANTE*/
					
					// //Busco los formularios de hce que se deben actualizar
					// $query_hce = "SELECT Encpro as ind_tab
								// FROM ".$whce."_000001 
							   // GROUP BY Encpro
							   // ORDER BY Encpro";
							   
					//Busco los formularios firmados de hce que se deben actualizar
					$query_hce = "SELECT Firpro as ind_tab 
									FROM ".$whce."_000036 
								   WHERE Firhis='".$fila['historia_destino']."' 
									 AND Firing='".$fila['ing_ajustado']."' 
								GROUP BY Firpro;";
					$res_hce = mysql_query($query_hce, $conex);// ; // or die( $log_errores.= "<br>NO ERP, buscando tablas HCE. Error No:".mysql_errno() ); 
					
					while($row = mysql_fetch_assoc($res_hce)) {
						
						// $query_update_hce = "UPDATE 	".$whce."_".$row['ind_tab']
									// ."		SET 	movhis = '".$fila['historia_destino']."' , moving = '".$fila['ing_ajustado']."'"
									// ."	  WHERE 	movhis = '".$fila['historia']."' "
									// ."		AND 	moving = '".$fila['ingreso']."'";
						
						$consecutivos = consultarConsecutivos($conex,$wemp_pmla,$whce,$row['ind_tab']);
						
						$query_update_hce = "UPDATE 	".$whce."_".$row['ind_tab']
									."		SET 	movhis = '".$fila['historia_destino']."' , moving = '".$fila['ing_ajustado']."'"
									."	  WHERE 	movcon IN (".$consecutivos.") "
									."		AND 	movhis = '".$fila['historia']."'"
									."		AND 	moving = '".$fila['ingreso']."';";
									
						$res_update_hce = mysql_query($query_update_hce, $conex) ; // or die( $log_errores.= "<br>NO ERP, actualizando tabla ".$row['ind_tab']." de HCE. Error No:".mysql_errno() );
					}
					/*FIN ACTUALIZANDO LAS TABLAS DE HCE DE LA 51 EN ADELANTE*/
			}
			
			/*ACTUALIZANDO LAS TABLAS DE MOVHOS*/
			//Busco las tablas que contengan un campo con la palabra "his"
			$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wmovhos."' "
							." 	  GROUP BY 		tabla"
							."	  ORDER BY		medico, codigo";
							
			$res_his = mysql_query($query_his,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando tablas con historia. Error No:".mysql_errno() );  
			
			while($row_his = mysql_fetch_assoc($res_his)) {
			
				//Busco el campo que contenta la palabra "ing" para un formulario correspondiente
				$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
							."	   FROM 	det_formulario "
							."	  WHERE 	activo = 'A'  "
							."	    AND 	descripcion like '%ing%'  "
							."	    AND		medico = '".$wmovhos."'"
							."	    AND		codigo = '".$row_his['tabla']."'"
							." ORDER BY		medico, codigo";
							
				$res_ing = mysql_query($query_ing,$conex) ; // or die(mysql_errno().":".mysql_error()); 
				$num_ing = mysql_num_rows($res_ing);
				$and_ing = "";
				$upt_ing = "";
				if( $num_ing == 1 ){
					$row_ing = mysql_fetch_assoc($res_ing);
					$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
					$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
				}elseif( $num_ing > 1 ){
					//Hay tablas que contienen multiples campos con la palabra "ing", lo restringimos a que termine en "ing" o contenga la palabra "ingreso"
					$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
							."	   FROM 	det_formulario "
							."	  WHERE 	activo = 'A'  "
							."	    AND 	( descripcion like '%ing'  "
							."       OR 	descripcion like '%ingreso%' ) "
							."	    AND		medico = '".$wmovhos."'"
							."	    AND		codigo = '".$row_his['tabla']."'"
							." ORDER BY		medico, codigo limit 1";
							
					$res_ing = mysql_query($query_ing,$conex) ; // or die(mysql_errno().":".mysql_error()); 
					$num_ing = mysql_num_rows($res_ing);
					
					if( $num_ing == 1 ){
						$row_ing = mysql_fetch_assoc($res_ing);
						$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
						$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
					}
				}
				//Query para actualizar el formulario contenga o no un ingreso
				$query_update = "UPDATE 	".$wmovhos."_".$row_his['tabla']
					."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
					." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
					.$and_ing;
				$res_update = mysql_query($query_update,$conex) ; // or die( $log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla'].". Error No:".mysql_errno() ); 
			}
			/*FIN ACTUALIZANDO LAS TABLAS DE MOVHOS*/

			//Verifica que exista el prefijo CENCAM
			if(! empty( $wcencam ) ){
					/*****************ACTUALIZANDO CENCAM*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wcencam."' "
								."ORDER BY		medico, codigo";

					$res_his = mysql_query($query_his,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando tablas con historia en cencam. Error No:".mysql_errno() );

					while($row_his = mysql_fetch_assoc($res_his)) {

						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$wcencam."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";

						$res_ing = mysql_query($query_ing,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando ingreso en cencam. Error No:".mysql_errno() );
						$num_ing = mysql_num_rows($res_ing);
						$and_ing = "";
						$upt_ing = "";
						if( $num_ing == 1 ){
							$row_ing = mysql_fetch_assoc($res_ing);
							$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
							$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
						}
						$query_update = " UPDATE 	".$wcencam."_".$row_his['tabla']
										."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
										." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
										.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die( $log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en cencam. Error No:".mysql_errno() );
					}
					
					// $query = "UPDATE 	".$wcencam."_000003"
							// ."	 SET 	Habitacion = Replace( Habitacion, '".$fila['historia']."', '".$fila['historia_destino']."')"
							// ." WHERE 	Habitacion  like '%".$fila['historia']."%'";
					// $res = mysql_query($query,$conex) ; // or die( $log_errores.= "<br>NO ERP, actualizando centro de camilleros. Error No:".mysql_errno() );  
					
					// $query = "UPDATE 	".$wcencam."_000003"
							// ."	 SET 	Observacion = Replace( Observacion, '".$fila['historia']."', '".$fila['historia_destino']."')"
							// ." WHERE 	Observacion  like '%".$fila['historia']."%'";
					// $res = mysql_query($query,$conex) ; // or die( $log_errores.= "<br>NO ERP, actualizando centro de camilleros. Error No:".mysql_errno() );  
					/*****************FIN ACTUALIZANDO CENCAM*********************/
			}
			
			//Verifica que exista el prefijo CENPRO
			if(! empty( $wcenpro ) ){
					/*****************ACTUALIZANDO CENPRO*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wcenpro."' "
								."ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando tablas con historia en cenpro. Error No:".mysql_errno() );  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
					
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$wcenpro."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando ingreso en cenpro. Error No:".mysql_errno() ); 
						$num_ing = mysql_num_rows($res_ing);
						$and_ing = "";
						$upt_ing = "";
						if( $num_ing == 1 ){
							$row_ing = mysql_fetch_assoc($res_ing);
							$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
							$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
						}
						$query_update = "UPDATE 	".$wcenpro."_".$row_his['tabla']
							."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
							." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
							.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die( $log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en cenpro. Error No:".mysql_errno() ); 
					}
					/*****************FIN ACTUALIZANDO CENPRO*********************/
			}
			
			//Verifica que exista el prefijo CHEQUEO
			if(! empty( $wchequeo ) ){
					/*****************ACTUALIZANDO CHEQUEO*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wchequeo."' "
								." ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) ; // or die($log_errores.= "<br>NO ERP, buscando tablas con historia en chequeo. Error No:".mysql_errno());  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
					
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$wchequeo."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) ; // or die($log_errores.= "<br>NO ERP, buscando ingreso en chequeo. Error No:".mysql_errno()); 
						$num_ing = mysql_num_rows($res_ing);
						$and_ing = "";
						$upt_ing = "";
						if( $num_ing == 1 ){
							$row_ing = mysql_fetch_assoc($res_ing);
							$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
							$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
						}
						$query_update = "UPDATE 	".$wchequeo."_".$row_his['tabla']
							."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
							." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
							.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en chequeo. Error No:".mysql_errno()); 
					}	
					/*****************FIN ACTUALIZANDO CHEQUEO*********************/
			}
			
			//Verifica que exista el prefijo FACHOS
			if(! empty( $wfachos ) ){
				/*****************ACTUALIZANDO FACHOS*********************/
				$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wfachos."' "
								." ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) ; // or die($log_errores.= "<br>NO ERP, buscando tablas con historia en fachos. Error No:".mysql_errno());  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
					
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$wfachos."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando ingreso en fachos. Error No:".mysql_errno()); 
						$num_ing = mysql_num_rows($res_ing);
						$and_ing = "";
						$upt_ing = "";
						if( $num_ing == 1 ){
							$row_ing = mysql_fetch_assoc($res_ing);
							$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
							$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
						}
						$query_update = "UPDATE 	".$wfachos."_".$row_his['tabla']
							."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
							." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
							.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en fachos. Error No:".mysql_errno()); 
					}	
				/*****************FIN ACTUALIZANDO FACHOS*********************/
			}
			
			//Verifica que exista el prefijo TCX
			if(! empty( $wtcx ) ){
					/*****************ACTUALIZANDO TCX*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wtcx."' "
								." ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) ; // or die($log_errores.= "<br>NO ERP, buscando tablas con historia en turnoscirugia. Error No:".mysql_errno());  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
						//PARA TCX EL CAMPO INGRESO CONTIENE LA PALABRA "nin" NO LA PALABRA "ing"
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%nin%'  "                   
									."	    AND		medico = '".$wtcx."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) ; // or die($log_errores.= "<br>NO ERP, buscando ingreso en turnoscirugia. Error No:".mysql_errno()); 
						$num_ing = mysql_num_rows($res_ing);
						$and_ing = "";
						$upt_ing = "";
						if( $num_ing == 1 ){
							$row_ing = mysql_fetch_assoc($res_ing);
							$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
							$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
						}
						$query_update = "UPDATE 	".$wtcx."_".$row_his['tabla']
							."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
							." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
							.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en turnoscirugia. Error No:".mysql_errno()); 
					}	
			}
			
			//Verifica que exista el prefijo MAGENTA
			if(! empty( $wmagenta ) ){
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wmagenta."' "
								." ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) ; // or die($log_errores.= "<br>NO ERP, buscando tabla con historia en magenta. Error No:".mysql_errno());  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "                   
									."	    AND		medico = '".$wmagenta."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) ; // or die($log_errores.= "<br>NO ERP, buscando ingreso en magenta. Error No:".mysql_errno()); 
						$num_ing = mysql_num_rows($res_ing);
						$and_ing = "";
						$upt_ing = "";
						if( $num_ing == 1 ){
							$row_ing = mysql_fetch_assoc($res_ing);
							$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
							$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
						}
						$query_update = "UPDATE 	".$wmagenta."_".$row_his['tabla']
							."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
							." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
							.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en magenta. Error No:".mysql_errno()); 
					}	
			}
			
			//Verifica que exista el prefijo AYUCNI
			if(! empty( $ayucni ) ){
					/*****************ACTUALIZANDO AYUCNI*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$ayucni."' "
								."ORDER BY		medico, codigo";

					$res_his = mysql_query($query_his,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando tablas con historia en cliame. Error No:".mysql_errno() );

					while($row_his = mysql_fetch_assoc($res_his)) {

						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$ayucni."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";

						
						$res_ing = mysql_query($query_ing,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando ingreso en cliame. Error No:".mysql_errno() );
						$num_ing = mysql_num_rows($res_ing);
						$and_ing = "";
						$upt_ing = "";
						if( $num_ing == 1 ){
							$row_ing = mysql_fetch_assoc($res_ing);
							$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
							$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
						}
						$query_update = " UPDATE 	".$ayucni."_".$row_his['tabla']
										."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
										." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
										.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die( $log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en cliame. Error No:".mysql_errno() );
					}
					/*****************FIN ACTUALIZANDO AYUCNI*********************/
			}
			
		}
		//$pila = array_reverse($pila);
		if(count($pila)>0){
			actualizarTablasNOERP( $pila );
		}
	}

	//FUNCION QUE CREA EL LOG, QUE GUARDA QUE HISOTORIA-INGRESO ES REEMPLAZADA POR CUAL HISTORIA-INGRESO
	function borrarRegistroDeHistoriasUnificadas( $movimiento ){
	
		global $conex;
		global $wemp_pmla;
		global $wmovhos;
		global $wclisur;
		global $user_session;
		$basedatos = "";
		//Determino en que bd se guardara el registro
		// if ( empty ( $wmovhos ) )
		if ( $wemp_pmla!="01" )
			$basedatos = $wclisur;//clisur o soe
		else
			$basedatos = $wmovhos;

		//Consulto cual es el numero de la tabla de registros de historias unificadas segun la empresa
		$tablaRHU = consultarPrefijosBD($conex, $wemp_pmla, 'tablarhu');		
		
		$query = " UPDATE ".$basedatos."_".$tablaRHU." set Rhuest='off' WHERE Rhumov = ".$movimiento;
		  
		$res = mysql_query($query, $conex);
	}
	
	function buscarPaciente( $whis ){
		global $conex;
		global $wemp_pmla;

		$query_info = "
			SELECT  datos_paciente.Pactid ,pacientes_id.Oriced,"
				."  datos_paciente.Pacno1, datos_paciente.Pacno2,"
				."  datos_paciente.Pacap1, datos_paciente.Pacap2"
		  ."  FROM  root_000037 as pacientes_id, root_000036 as datos_paciente"
		  ." WHERE  pacientes_id.Orihis = '".$whis."'"
		  ."   AND  pacientes_id.Oriori =  '".$wemp_pmla."'"
		  ."   AND 	pacientes_id.Oriced = datos_paciente.Pacced"
		  ."   AND 	Oritid = Pactid";

		  
		$res_info = mysql_query($query_info, $conex);
		$row_datos = mysql_fetch_array($res_info);
		$datos = array('primer_nombre'=>$row_datos['Pacno1'], 'segundo_nombre'=>$row_datos['Pacno2'], 'primer_apellido'=>$row_datos['Pacap1'], 
					   'segundo_apellido'=>$row_datos['Pacap2'],'doc'=>$row_datos['Oriced'],'tipodoc'=>$row_datos['Pactid'], 'historia'=>$whis );
		return $datos;
	}
	
	function contieneAdelante($clave, $arreglo, $posicion){
		
		$indice =0;
		foreach( $arreglo as $valor ){
			if( $clave == $valor && $indice > $posicion ){
				return true;
			}
			$indice++;
		}
		return false;
	}

	//FUNCION QUE SE LLAMA CUANDO LA PAGINA CARGA Y MUESTRA LOS PARAMETROS DE CONSULTA
	function vistaInicial(){
		
		global $wemp_pmla;
		global $wccosSU;
		global $wactualiz;
		global $wclisur;
		
		//Se imprimen variables ocultas
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		echo "<center>";
		$logo = "";
		// if (empty( $wclisur ))
			// $logo = "clinica";
		// else
			// $logo = "logo_".$wclisur;
		
		if ( $wemp_pmla=="01")
			$logo = "clinica";
		else
			$logo = "logo_".$wclisur;

		encabezado("DESHACER PROCESO DE UNIFICAR HISTORIAS",$wactualiz, $logo);
		
		if(!isset($_SESSION['user'])){
			echo "Error";
			return;
		}
		
		global $conex;
		global $wemp_pmla;
		global $wmovhos;
		global $wclisur;
		global $user_session;
		
		$basedatos = "";
		//Determino en que bd se guardara el registro
		// if ( empty ( $wmovhos ) )
		if ( $wemp_pmla!="01" )
			$basedatos = $wclisur;//clisur o soe
		else
			$basedatos = $wmovhos;

		//Consulto cual es el numero de la tabla de registros de historias unificadas segun la empresa
		$tablaRHU = consultarPrefijosBD($conex, $wemp_pmla, 'tablarhu');		
		
		//Busco todos los registros para las unificaciones de ingresos para dos historias
		$query = " SELECT  	Fecha_data as fecha, Hora_data as hora, Rhumov as movimiento, Rhuhia as historia_origen, "
				."			Rhuhid as historia_destino"
				."	 FROM 	".$basedatos."_".$tablaRHU
				."  WHERE 	Rhuest = 'on'"
				."	  AND 	Rhuhia != Rhuhid "
				."	  AND   Rhutip = '0' "
			   ."GROUP BY 	Rhumov, Rhuhia, Rhuhid "
			   ."ORDER BY 	Fecha_data, Hora_data ASC";
		
		$arreglo = array();
		$res = mysql_query($query, $conex);
		while($row = mysql_fetch_assoc($res)) {
			//Traer datos de los pacientes
			array_push($arreglo, $row);
		}
		
		//Busco todos los registros para las unificaciones de ingresos para una misma historia
		$query = " SELECT  	Fecha_data as fecha, Hora_data as hora, Rhumov as movimiento, Rhuhia as historia_origen, "
				."			Rhuhid as historia_destino"
				."	 FROM 	".$basedatos."_".$tablaRHU
				."  WHERE 	Rhuest = 'on'"
				."	  AND 	Rhuhia = Rhuhid "
				."	  AND   Rhutip = '1' "
			   ."GROUP BY 	Rhumov "
			   ."ORDER BY 	Fecha_data, Hora_data ASC";
		$res = mysql_query($query, $conex);
		while($row = mysql_fetch_assoc($res)) {
			//Traer datos de los pacientes
			array_push($arreglo, $row);
		}
		
		$respuesta = '<div>';
		$respuesta.= "<center>";
		$respuesta.= "<table align='center' id='tabla_resultados'>";
		$respuesta.= "<tr class='encabezadotabla'>";
		$respuesta.= "<td colspan='14' align='center'>Historias que han sido unificadas</td>";
		$respuesta.= "</tr>";
		
		$respuesta.= "<tr class='encabezadoTabla'>";
		$respuesta.= "<th rowspan='2' nowrap='nowrap'>Fecha <br>(aaaa-mm-dd)</th>";
		$respuesta.= "<th rowspan='2' nowrap='nowrap'>Hora <br>(hh:mm)</th>";
		$respuesta.= "<th rowspan='2'>Movimiento</th>";
		$respuesta.= "<th colspan='2'>Origen</th>";
		$respuesta.= "<th colspan='2'>Destino</th>";
		$respuesta.= "</tr>";
		
		$respuesta.= "<tr class='encabezadoTabla'>";		
		$respuesta.= "<th>Historia</th>";
		$respuesta.= "<th>Paciente</th>";
		$respuesta.= "<th>Historia</th>";
		$respuesta.= "<th>Paciente</th>";
		$respuesta.= "</tr>";
			
		$texto_oculto = "";
		$wclass="";
		$i=0;
		$historia_origen_actual = "";
		$movimiento_actual = 0;
		foreach( $arreglo as $fila){
			$i++;
			if ( ($i%2) == 0 )
				$wclass="fila1";
			else
				$wclass="fila2";
				
			$id = "oculto_".$i;	
			
			$paciente_origen = buscarPaciente( $fila['historia_origen'] );
			$paciente_destino = buscarPaciente( $fila['historia_destino'] );

			$respuesta.="<tr onclick="."\""."javascript:mostrarOculto('".$id."','".$basedatos."','".$tablaRHU."','".$fila['movimiento']."','".$fila['historia_origen']."','".$fila['historia_destino']."')"."\"".">";

			//FECHA
			$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$fila['fecha']."</td>";
			//HORA
			$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$fila['hora']."</td>";
			//MOVIMIENTO
			$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$fila['movimiento']."</td>";
			//HISTORIA ORIGEN
			$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$fila['historia_origen']."</td>";
			//PACIENTE ORIGEN
			$nombreC = $paciente_origen['primer_nombre']." ".$paciente_origen['segundo_nombre']." ".$paciente_origen['primer_apellido']." ".$paciente_origen['segundo_apellido'];
			$respuesta.="<td nowrap='nowrap' class='$wclass izquierda msg_tooltip' title='".$paciente_origen['tipodoc']."-".$paciente_origen['doc']."'>".$nombreC."</td>";
			//HISTORIA DESTINO
			$respuesta.="<td class='$wclass centrar' nowrap='nowrap'>".$fila['historia_destino']."</td>";
			//PACIENTE DESTINO
			$nombreC = $paciente_destino['primer_nombre']." ".$paciente_destino['segundo_nombre']." ".$paciente_destino['primer_apellido']." ".$paciente_destino['segundo_apellido'];
			$respuesta.="<td nowrap='nowrap' class='$wclass izquierda msg_tooltip' title='".$paciente_destino['tipodoc']." - ".$paciente_destino['doc']."'>".$nombreC."</td>";
			$respuesta.= "</tr>";
			$respuesta.= "<tr id='oculto_$i' class='oculto' style='display:none'>
						</tr>";
			/*
			$texto_oculto="<tr id='oculto_$i' class='oculto' style='display:none'>
								<td colspan='6' class='fondotd'>
									<table align='center' width=80%>
									
											<tr class='fondoGris'>
												<td colspan='4' align='center'><b>CAMBIOS</b></td>
											</tr>
											<tr class='fondoGris'>
												<th colspan='2'>Antes</th>
												<th colspan='2'>Despues</th>
											</tr>
											<tr class='fondoGris'>
												<th nowrap='nowrap' class='centrar'>Historia</th>
												<th nowrap='nowrap' class='centrar'>Ingreso</th>
												<th nowrap='nowrap' class='centrar'>Historia</th>
												<th class='centrar' nowrap='nowrap'>Ingreso</th>
											</tr>";
					
					
			
			
			$query2 = " SELECT  Rhuhia as hisa, Rhuina as inga, Rhuhid as hisd, Rhuind as ingd"
			."	 FROM 	".$basedatos."_".$tablaRHU
			."  WHERE 	Rhuest = 'on'"
			." 	  AND	Rhumov = ".$fila['movimiento'];
			
			$res2 = mysql_query($query2, $conex);
			while($row2 = mysql_fetch_assoc($res2)) {
				$texto_oculto.="<tr class='fondoCrema'>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".$row2['hisa']."</td>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".$row2['inga']."</td>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".$row2['hisd']."</td>";
				$texto_oculto.="<td align='center' nowrap='nowrap'>".$row2['ingd']."</td>";
				$texto_oculto.="</tr>";
			}
			$texto_oculto.="</table> ";
			$texto_oculto.="	</td> ";
			$texto_oculto.="	<td class='fondotd' align='center'> ";
								
			if( $fila['historia_origen'] != $fila['historia_destino'] ){
				$texto_oculto.="<input type='button' class='botona' id='consultar' value='Deshacer unificacion' onclick='javascript:desUnificar(".$fila['movimiento'].")'></input> ";
			}
			$texto_oculto.="	</td> ";
			$texto_oculto.="	</tr>";

			$respuesta.=$texto_oculto;*/
		}
		
		echo $respuesta;
		
		echo '</center>';
		
		echo '<center>';
		echo '<br><br>';
		echo '<table>';
		echo "<tr>";
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>';
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana'>";
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</center>';
		echo '<br><br><br><br>';
		
		echo '<div id="respuestaAjax" align="center"></div>';			
		
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
		
		
		
		
	}
	//FIN***********************************************************************************************************//
?> 

		<style type="text/css">
				.botona
				{
					font-size:13px;
					font-family:Verdana,Helvetica;
					font-weight:bold;
					color:white;
					background:#638cb5;
					border:0px;
					width:180px;
					height:30px;
					margin-left: 1%;
				}
				
				input{
					outline: none;
					-moz-border-radius: 5px;
					-webkit-border-radius: 5px;
				    border-radius: 4px;

				}
				
				.botona:hover{
					background:#638BD5;
				
				}
						   
			    a img{ 
					border:0; 
				}
				
				.fondotd{
					background:#F1FDFF;
				
				}
				
				#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
			    #tooltip h3, #tooltip div{margin:0; width:auto}
				
			    .centrar{
					text-align:center;
					nowrap: nowrap;
			    }
				
				.fondoGris{
					font-weight:normal;
					text-align:center;
				}
				.fondoCrema{
					font-weight:bold;
					font-size: 9pt;
				}
				
				.oculto th{
					font-weight:normal;
				}
			   
			    .izquierda{
					text-align:left;
					nowrap: nowrap;
			    }
			   
			    .fila1{
					font-size: 9pt;
				}
				
				.fila2{
					font-size: 9pt;
				}
		</style>

		<script>
		
			//************cuando la pagina este lista...**********//
			$(document).ready(function() {
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$("#cerrar_ventana").click(function() {
					cerrarVentana();
				});
			});
				
			
			//***********funciones de javascript*****************//
			
			function mostrarOculto(idElemento,basedatos,tablaRHU,nroMovimiento,historiaOrigen,historiaDestino){
				//ESCONDE O MUESTRA LA FILA OCULTA
				if( $("#"+idElemento).is(":hidden"))
				{
					$.post("deshacerUnificarHistorias.php",
					{
						consultaAjax 	: '',
						action			: 'pintarDetalleUnificacion',
						idElemento		: idElemento,
						basedatos		: basedatos,
						tablaRHU		: tablaRHU,
						nroMovimiento	: nroMovimiento,
						historiaOrigen	: historiaOrigen,
						historiaDestino	: historiaDestino
					}
					, function(data) {
						$("#"+idElemento).html(data);
						$("#"+idElemento).fadeIn('fast');
						
					},'json');
					
				}
				else
				{
					$("#"+idElemento).fadeOut('fast');
				}
			}
			
			function desUnificar( movimiento ){
				var confirmar = confirm("Desea deshacer los cambios hechos al unificar las historias del movimiento "+movimiento);
				if(! confirmar)		
					return;
				
				var wemp_pmla = $("#wemp_pmla").val();
				//muestra el mensaje de cargando
				$.blockUI({ message: $('#msjEspere') });
				
				//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
						
				//Realiza el llamado ajax con los parametros de busqueda
				$.get('deshacerUnificarHistorias.php', { wemp_pmla: wemp_pmla, action: "desunificar", movimiento: movimiento, consultaAjax: aleatorio } ,
					function(data) {
						//oculta el mensaje de cargando
						$.unblockUI();
						$("#respuestaAjax").html( data );
						alerta("Proceso de deshacer unificacion finalizado");
						setTimeout( function(){
									window.location.reload();		
								}, 3000 );
									
					});			
			}
			
			function alerta( txt ){
				$("#textoAlerta").text( txt );
				$.blockUI({ message: $('#msjAlerta') });
					setTimeout( function(){
									$.unblockUI();
								}, 3000 );
			}
			
		</script>

    </head>
    <body>
		<!-- EN ADELANTE ES LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>
