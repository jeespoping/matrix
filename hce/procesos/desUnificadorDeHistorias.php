<?php
include_once("conex.php");
/**
 PROGRAMA                   : desUnificadorDeHistorias.php
 AUTOR                      : Frederick Aguirre.
 FECHA CREACION             : 11 de Octubre de 2012

 DESCRIPCION:
 El objetivo del programa es DESHACER LOS CAMBIOS producidos por la unificacion dos historias que se hicieron con el programa  unificadorDeHistorias.php
 
 CAMBIOS:

**/


if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

$wactualiz = "2012-10-11";

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";

	echo "<title>desunificador de historias</title>";
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
	$wmagenta = consultarPrefijosBD($conex, $wemp_pmla, "afinidad");
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
		}else{
			return;
		}
	}
	//FIN*LLAMADOS*AJAX**************************************************************************************************************//

	
	//**************************FUNCIONES DE PHP********************************************//
	
	//Para consultar el prefijo de la bd ya que la aplicacion y el codigo de la tabla RHU para las distintas empresas
	function consultarPrefijosBD($conexion, $codigoInstitucion, $nombreAplicacion){
		$q = " SELECT 	Detval  
				 FROM 	root_000051
				WHERE   Detemp = '".$codigoInstitucion."'
				  AND 	Detapl = '".$nombreAplicacion."'";
		
		$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
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
		global $conex;
		global $wemp_pmla;
		
		if(empty( $wclisur ) ){
			return;
		}
		
		echo "<br><b>Se actualiza facturacion<b><br>";
				
		//Consulto cual es el numero de la tabla de registros de historias unificadas segun la empresa
		$tablaRHU = consultarPrefijosBD($conex, $wemp_pmla, 'tablarhu');	
		
		$query_ingresos = "SELECT 	Fecha_data as fecha, Hora_data as hora, Rhuhid as historia, Rhuind as ingreso, Rhuhia as historia_destino, Rhuina as ing_ajustado"
		."	 FROM 	".$wclisur."_".$tablaRHU." "
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
		
		//Se crea el log, en donde se guarda que historia-ingreso es reemplazada por cual historia-ingreso
		borrarRegistroDeHistoriasUnificadas( $wmovimiento );
		//Se actualizan las tablas necesarias y se manda el segundo parametro false porque no estoy enviando una pila
		$isFarpmla = false;//FALSE porque se actualizan tablas de SOE Y CLISUR, no las de FARPMLA
		//actualizarTablasERP($wresultado_ingresos, false, $isFarpmla );	
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
		if( $isFarpmla ){
			//print echo "<br> <b>Actualizando tablas ERP para farpmla</b><br>";
			$wclisur = consultarPrefijosBD($conex, '09', "farpmla");
		}else{
			$wclisur = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");
		}
		//Se crea un arreglo que contiene las historia-ingreso que seran cambiadas
		$array_historia_ingreso = array();
		foreach ( $datos as $fila ){
			array_push( $array_historia_ingreso, $fila['historia']."-".$fila['ingreso']);
		}
		
		$pila = array();
		//print echo "<br><br><br>";
		$indice = 0;
		foreach ( $datos as $fila ){
			//print echo "actualizare: <b>".$fila['historia']."-".$fila['ingreso']." </b> por: <b>".$fila['historia_destino']."-".$fila['ing_ajustado']."</b><br>";
			
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
			//continue;			
			
			/*****************ACTUALIZANDO CLISUR O SOE O FARPMLA*********************/
			
			//Buscar las tablas y el campo que contiene la palabra "his"
			$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wclisur."' "
								." ORDER BY		medico, codigo";
								
			$res_his = mysql_query($query_his,$conex) or die( $log_errores.= "<br>ERP, tablas que tienen historia ".mysql_errno() );  
			
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
							
				$res_ing = mysql_query($query_ing,$conex) or die( $log_errores.= "<br>ERP, buscando ingreso ".mysql_errno() ); 
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
				//NOACTUALIZO$res_update = mysql_query($query_update,$conex) or die( $log_errores.= "<br>ERP, actualizando ".$row_his['tabla']." Error No:".mysql_errno() ); 
				//print echo "<br> QUERY ".$query_update." </br>";
			}
					
			/*$query = "UPDATE 	".$wclisur."_000018"
					."	 SET 	Fenhis = '".$fila['historia_destino']."', Fening = '".$fila['ing_ajustado']."'"
					." WHERE 	Fenhis  = '".$fila['historia']."'"
					."   AND 	Fening = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wclisur."_000021"
					."	 SET 	Rdehis = '".$fila['historia_destino']."', Rdeing = '".$fila['ing_ajustado']."'"
					." WHERE 	Rdehis  = '".$fila['historia']."'"
					."   AND 	Rdeing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wclisur."_000100"
					."	 SET 	Pachis = '".$fila['historia_destino']."'"
					." WHERE 	Pachis  = '".$fila['historia']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			//CAMPO NIN
			$query = "UPDATE 	".$wclisur."_000101"
					."	 SET 	Inghis = '".$fila['historia_destino']."', Ingnin = '".$fila['ing_ajustado']."'"
					." WHERE 	Inghis  = '".$fila['historia']."'"
					."   AND 	Ingnin = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000106"
					."	 SET 	Tcarhis = '".$fila['historia_destino']."', Tcaring = '".$fila['ing_ajustado']."'"
					." WHERE 	Tcarhis  = '".$fila['historia']."'"
					."   AND 	Tcaring = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000107"
					."	 SET 	Audhis = '".$fila['historia_destino']."', Auding = '".$fila['ing_ajustado']."'"
					." WHERE 	Audhis  = '".$fila['historia']."'"
					."   AND 	Auding = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000108"
					."	 SET 	Egrhis = '".$fila['historia_destino']."', Egring = '".$fila['ing_ajustado']."'"
					." WHERE 	Egrhis  = '".$fila['historia']."'"
					."   AND 	Egring = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000109"
					."	 SET 	Diahis = '".$fila['historia_destino']."', Diaing = '".$fila['ing_ajustado']."'"
					." WHERE 	Diahis  = '".$fila['historia']."'"
					."   AND 	Diaing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000110"
					."	 SET 	Prohis = '".$fila['historia_destino']."', Proing = '".$fila['ing_ajustado']."'"
					." WHERE 	Prohis  = '".$fila['historia']."'"
					."   AND 	Proing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000111"
					."	 SET 	Esphis = '".$fila['historia_destino']."', Esping = '".$fila['ing_ajustado']."'"
					." WHERE 	Esphis  = '".$fila['historia']."'"
					."   AND 	Esping = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000112"
					."	 SET 	Serhis = '".$fila['historia_destino']."', Sering = '".$fila['ing_ajustado']."'"
					." WHERE 	Serhis  = '".$fila['historia']."'"
					."   AND 	Sering = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000115"
					."	 SET 	Movpaqhis = '".$fila['historia_destino']."', Movpaqing = '".$fila['ing_ajustado']."'"
					." WHERE 	Movpaqhis  = '".$fila['historia']."'"
					."   AND 	Movpaqing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			$query = "UPDATE 	".$wclisur."_000123"
					."	 SET 	Amshis = '".$fila['historia_destino']."', Amsing = '".$fila['ing_ajustado']."'"
					." WHERE 	Amshis  = '".$fila['historia']."'"
					."   AND 	Amsing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			
			
			$query = "UPDATE 	".$wclisur."_000142"
					."	 SET 	Poshis = '".$fila['historia_destino']."', Posing = '".$fila['ing_ajustado']."'"
					." WHERE 	Poshis  = '".$fila['historia']."'"
					."   AND 	Posing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
			*/
			//if ( $wemp_pmla == '02' ){
				//******************SOLO DE CLISUR************//
				/*			
				$query = "UPDATE 	".$wclisur."_000138"
						."	 SET 	Maihis = '".$fila['historia_destino']."', Maiing = '".$fila['ing_ajustado']."'"
						." WHERE 	Maihis  = '".$fila['historia']."'"
						."   AND 	Maiing = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
				
				$query = "UPDATE 	".$wclisur."_000139"
						."	 SET 	Hclhis = '".$fila['historia_destino']."', Hcling = '".$fila['ing_ajustado']."'"
						." WHERE 	Hclhis  = '".$fila['historia']."'"
						."   AND 	Hcling = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
				
				$query = "UPDATE 	".$wclisur."_000140"
						."	 SET 	Inthis = '".$fila['historia_destino']."', Inting = '".$fila['ing_ajustado']."'"
						." WHERE 	Inthis  = '".$fila['historia']."'"
						."   AND 	Inting = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
				
				$query = "UPDATE 	".$wclisur."_000141"
					."	 SET 	Esphis = '".$fila['historia_destino']."', Esping = '".$fila['ing_ajustado']."'"
					." WHERE 	Esphis  = '".$fila['historia']."'"
					."   AND 	Esping = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
				*/
				//******************FIN SOLO DE CLISUR************//				
			//}
			
			//if( $wemp_pmla == '07' ){
				//******************SOLO DE SOE************//
				/*
				$query = "UPDATE 	".$wclisur."_000128"
						."	 SET 	Hiscli = '".$fila['historia_destino']."'"
						." WHERE 	Hiscli  = '".$fila['historia']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
				
				$query = "UPDATE 	".$wclisur."_000131"
						."	 SET 	Ptohis = '".$fila['historia_destino']."', Ptoing = '".$fila['ing_ajustado']."'"
						." WHERE 	Ptohis  = '".$fila['historia']."'"
						."   AND 	Ptoing = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
				
				$query = "UPDATE 	".$wclisur."_000132"
						."	 SET 	Mdxhis = '".$fila['historia_destino']."', Mdxing = '".$fila['ing_ajustado']."'"
						." WHERE 	Mdxhis  = '".$fila['historia']."'"
						."   AND 	Mdxing = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
				
				$query = "UPDATE 	".$wclisur."_000134"
						."	 SET 	Historia = '".$fila['historia_destino']."', Ingreso = '".$fila['ing_ajustado']."'"
						." WHERE 	Historia  = '".$fila['historia']."'"
						."   AND 	Ingreso = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
					
				$query = "UPDATE 	".$wclisur."_000141"
				."	 SET 	Esphis = '".$fila['historia_destino']."', Esping = '".$fila['ing_ajustado']."'"
				." WHERE 	Esphis  = '".$fila['historia']."'"
				."   AND 	Esping = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				*/				
				//******************FIN SOLO DE SOE************//
			//}
			
			/*****************FIN ACTUALIZANDO CLISUR O SOE O FARPMLA*********************/
			
		}
		
		//Se le hace reverse a la pila para que el ultimo en apilarse sea el primero en salir
		//$pila = array_reverse($pila);
		if(count($pila)>0){
			//print echo "<br>REPETIR PROCESO  ERP ". count($pila) ."<br>";
			actualizarTablasERP( $pila, $isFarpmla );
		}
		$wclisur = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");
	}
	
	//NO ERP, PARA CLINICA LAS AMERICAS
	function desUnificarNroHistoriaNOERP($wmovimiento ){
		//MOVHOS, HCE, MAGENTA, FACHOS, CENPRO ETC
		global $conex;
		global $wmovhos;
		global $wemp_pmla;
		
		if(empty( $wmovhos ) ){
			return;
		}
		//echo "<br><b>Se actualiza movimiento hospitalario</b><br>";
		
		
	
		//Consulto cual es el numero de la tabla de registros de historias unificadas segun la empresa
		$tablaRHU = consultarPrefijosBD($conex, $wemp_pmla, 'tablarhu');	
		
		$query_ingresos = "SELECT 	Fecha_data as fecha, Hora_data as hora, Rhuhid as historia, Rhuind as ingreso, Rhuhia as historia_destino, Rhuina as ing_ajustado"
		."	 FROM 	".$wmovhos."_".$tablaRHU." "
		."	 WHERE  Rhumov = ".$wmovimiento;
		//."  ORDER BY  Fecha_data, Hora_data, Rhuhia, Rhuina";
							
		$res_ingresos = mysql_query($query_ingresos, $conex);
		$num = mysql_num_rows($res_ingresos);
		
		$wresultado_ingresos = array();
		if ($num > 0){
			while($datatmp = mysql_fetch_assoc($res_ingresos)) {
				array_push( $wresultado_ingresos, $datatmp );
			}
		}

		//Se crea el log, en donde se guarda que historia-ingreso es reemplazada por cual historia-ingreso
		borrarRegistroDeHistoriasUnificadas( $wmovimiento );
		
		//Se actualizan las tablas necesarias y se manda el segundo parametro false porque no estoy enviando una pila
		//actualizarTablasNOERP($wresultado_ingresos, false );
		actualizarTablasNOERP($wresultado_ingresos );
		
		
		//Se actualizan tablas de ERP para FARPMLA, ya que funciona similar a SOE Y CLISUR, pero
		//enviando un tercer parametro indicando que es para farpmla
		$isFarpmla = true;
		//actualizarTablasERP($wresultado_ingresos, false, $isFarpmla );
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
		
		global $log_errores;
		
		//Se crea un arreglo que contiene las historia-ingreso que seran cambiadas
		$array_historia_ingreso = array();

		foreach ( $datos as $fila ){
			array_push( $array_historia_ingreso, $fila['historia']."-".$fila['ingreso']);
		}
		
		$pila = array();
		//print echo "<br><br><br><br>";
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

			//continue;
			//Verifica que exista el prefijo HCE para la empresa
			if(! empty( $whce ) ){
				//print echo "<br><b>Se actualiza Historia clinica electronica</b><br>";
					/*ACTUALIZANDO LAS TABLAS DE HCE DE LA 51 EN ADELANTE*/
					
					//Busco los formularios de hce que se deben actualizar
					$query_hce = "SELECT Encpro as ind_tab
								FROM ".$whce."_000001 
							   GROUP BY Encpro
							   ORDER BY Encpro";
					$res_hce = mysql_query($query_hce, $conex) or die( $log_errores.= "<br>NO ERP, buscando tablas HCE. Error No:".mysql_errno() ); 
					
					while($row = mysql_fetch_assoc($res_hce)) {
						$query_update_hce = "UPDATE 	".$whce."_".$row['ind_tab']
									."		SET 	movhis = '".$fila['historia_destino']."' , moving = '".$fila['ing_ajustado']."'"
									."	  WHERE 	movhis = '".$fila['historia']."' "
									."		AND 	moving = '".$fila['ingreso']."'";
									
						//$res_update_hce = mysql_query($query_update_hce, $conex) or die( $log_errores.= "<br>NO ERP, actualizando tabla ".$row['ind_tab']." de HCE. Error No:".mysql_errno() );
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
							
			$res_his = mysql_query($query_his,$conex) or die( $log_errores.= "<br>NO ERP, buscando tablas con historia. Error No:".mysql_errno() );  
			
			while($row_his = mysql_fetch_assoc($res_his)) {
			
				//Busco el campo que contenta la palabra "ing" para un formulario correspondiente
				$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
							."	   FROM 	det_formulario "
							."	  WHERE 	activo = 'A'  "
							."	    AND 	descripcion like '%ing%'  "
							."	    AND		medico = '".$wmovhos."'"
							."	    AND		codigo = '".$row_his['tabla']."'"
							." ORDER BY		medico, codigo";
							
				$res_ing = mysql_query($query_ing,$conex) or die(mysql_errno().":".mysql_error()); 
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
							
					$res_ing = mysql_query($query_ing,$conex) or die(mysql_errno().":".mysql_error()); 
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
				//NOACTUALIZO$res_update = mysql_query($query_update,$conex) or die( $log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla'].". Error No:".mysql_errno() ); 
				//print echo "<br> QUERY ".$query_update." </br>";
			}
			/*		
			$query = "UPDATE 	".$wmovhos."_000002"
					." 	 SET 	Fenhis = '".$fila['historia_destino']."' , Fening = '".$fila['ing_ajustado']."'"
					." WHERE 	Fenhis = '".$fila['historia']."'"
					." 	 AND	Fening = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000004 "
					."	 SET 	Spahis = '".$fila['historia_destino']."', Spaing = '".$fila['ing_ajustado']."'"
					." WHERE 	Spahis = '".$fila['historia']."'"
					."	 AND 	Spaing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000005 "
					."	 SET 	Errhis = '".$fila['historia_destino']."'"
					." WHERE 	Errhis = '".$fila['historia']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000006 "
					."	 SET 	Historia = '".$fila['historia_destino']."', Ingreso = '".$fila['ing_ajustado']."'"
					." WHERE 	Historia = '".$fila['historia']."'"
					."	 AND 	Ingreso = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000015"
					."	 SET 	Aplhis = '".$fila['historia_destino']."', Apling = '".$fila['ing_ajustado']."'"
					." WHERE 	Aplhis = '".$fila['historia']."'"
					."   AND  	Apling = '".$fila[' ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000016"
					."	 SET 	Inghis = '".$fila['historia_destino']."', Inging = '".$fila['ing_ajustado']."'"
					." WHERE 	Inghis = '".$fila['historia']."'"
					."   AND 	Inging = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000017"
					."	 SET 	Eyrhis = '".$fila['historia_destino']."', Eyring = '".$fila['ing_ajustado']."'"
					." WHERE 	Eyrhis = '".$fila['historia']."'"
					."	 AND	Eyring = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000018"
					."	 SET 	Ubihis = '".$fila['historia_destino']."', Ubiing = '".$fila['ing_ajustado']."'"
					." WHERE 	Ubihis = '".$fila['historia']."'"
					."   AND	Ubiing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000020"
					."	 SET 	Habhis = '".$fila['historia_destino']."', Habing='".$fila['ing_ajustado']."'"
					." WHERE 	Habhis = '".$fila['historia']."'"
					."   AND	Habing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000021"
					."	 SET 	Bithis = '".$fila['historia_destino']."', Biting='".$fila['ing_ajustado']."'"
					." WHERE 	Bithis = '".$fila['historia']."'"
					."   AND	Biting = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000022"
					."	 SET 	Cuehis = '".$fila['historia_destino']."', Cueing='".$fila['ing_ajustado']."'"
					." WHERE 	Cuehis = '".$fila['historia']."'"
					."   AND	Cueing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000030"
					."	 SET 	Splhis = '".$fila['historia_destino']."', Spling = '".$fila['ing_ajustado']."'"
					." WHERE 	Splhis = '".$fila['historia']."'"
					."   AND	Spling = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000032"
					."	 SET 	Historia_clinica  = '".$fila['historia_destino']."', Num_ingreso = '".$fila['ing_ajustado']."'"
					." WHERE 	Historia_clinica  = '".$fila['historia']."'"
					." 	 AND	Num_ingreso = '".$fila['ingreso']."'";   
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000033"
					."	 SET 	Historia_clinica = '".$fila['historia_destino']."', Num_ingreso = '".$fila['ing_ajustado']."'"
					." WHERE 	Historia_clinica  = '".$fila['historia']."'"
					."   AND 	Num_ingreso = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000035"
					."	 SET 	Denhis = '".$fila['historia_destino']."', Dening = '".$fila['ing_ajustado']."'"
					." WHERE 	Denhis  = '".$fila['historia']."'"
					."   AND 	Dening = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000047"
					."	 SET 	Methis = '".$fila['historia_destino']."', Meting = '".$fila['ing_ajustado']."'"
					." WHERE 	Methis  = '".$fila['historia']."'"
					."   AND 	Meting = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000050"
					."	 SET 	Ekahis = '".$fila['historia_destino']."', Ekaing = '".$fila['ing_ajustado']."'"
					." WHERE 	Ekahis  = '".$fila['historia']."'"
					."   AND 	Ekaing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000051"
					."	 SET 	Inkhis = '".$fila['historia_destino']."', Inking = '".$fila['ing_ajustado']."'"
					." WHERE 	Inkhis  = '".$fila['historia']."'"
					."   AND 	Inking = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000052"
					."	 SET 	Dikhis = '".$fila['historia_destino']."', Diking = '".$fila['ing_ajustado']."'"
					." WHERE 	Dikhis  = '".$fila['historia']."'"
					."   AND 	Diking = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000053"
					."	 SET 	Karhis = '".$fila['historia_destino']."', Karing = '".$fila['ing_ajustado']."'"
					." WHERE 	Karhis  = '".$fila['historia']."'"
					."   AND 	Karing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000054"
					."	 SET 	Kadhis = '".$fila['historia_destino']."', Kading = '".$fila['ing_ajustado']."'"
					." WHERE 	Kadhis  = '".$fila['historia']."'"
					."   AND 	Kading = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000055"
					."	 SET 	Kauhis = '".$fila['historia_destino']."', Kauing = '".$fila['ing_ajustado']."'"
					." WHERE 	Kauhis  = '".$fila['historia']."'"
					."   AND 	Kauing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000056"
					."	 SET 	Nuthis = '".$fila['historia_destino']."', Nuting = '".$fila['ing_ajustado']."'"
					." WHERE 	Nuthis  = '".$fila['historia']."'"
					."   AND 	Nuting = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000057"
					."	 SET 	Historia_Clinica = '".$fila['historia_destino']."', Ingreso_historia_clinica = '".$fila['ing_ajustado']."'"
					." WHERE 	Historia_Clinica  = '".$fila['historia']."'"
					."   AND 	Ingreso_historia_clinica = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			*/
			
			
			/*	
			$query = "UPDATE 	".$wmovhos."_000060"
					."	 SET 	Kadhis = '".$fila['historia_destino']."', Kading = '".$fila['ing_ajustado']."'"
					." WHERE 	Kadhis  = '".$fila['historia']."'"
					."   AND 	Kading = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000061"
					."	 SET 	Ekahis = '".$fila['historia_destino']."', Ekaing = '".$fila['ing_ajustado']."'"
					." WHERE 	Ekahis  = '".$fila['historia']."'"
					."   AND 	Ekaing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000062"
					."	 SET 	Inkhis = '".$fila['historia_destino']."', Inking = '".$fila['ing_ajustado']."'"
					." WHERE 	Inkhis  = '".$fila['historia']."'"
					."   AND 	Inking = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000063"
					."	 SET 	Methis = '".$fila['historia_destino']."', Meting = '".$fila['ing_ajustado']."'"
					." WHERE 	Methis  = '".$fila['historia']."'"
					."   AND 	Meting = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000064"
					."	 SET 	Dikhis = '".$fila['historia_destino']."', Diking = '".$fila['ing_ajustado']."'"
					." WHERE 	Dikhis  = '".$fila['historia']."'"
					."   AND 	Diking = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			*/
			
			/*
			$query = "UPDATE 	".$wmovhos."_000067"
					."	 SET 	Habhis = '".$fila['historia_destino']."', Habing = '".$fila['ing_ajustado']."'"
					." WHERE 	Habhis  = '".$fila['historia']."'"
					."   AND 	Habing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000070"
					."	 SET 	Infhis = '".$fila['historia_destino']."', Infing = '".$fila['ing_ajustado']."'"
					." WHERE 	Infhis  = '".$fila['historia']."'"
					."   AND 	Infing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000071"
					."	 SET 	Indhis = '".$fila['historia_destino']."', Inding = '".$fila['ing_ajustado']."'"
					." WHERE 	Indhis  = '".$fila['historia']."'"
					."   AND 	Inding = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000077"
					."	 SET 	Movhis = '".$fila['historia_destino']."', Moving = '".$fila['ing_ajustado']."'"
					." WHERE 	Movhis  = '".$fila['historia']."'"
					."   AND 	Moving = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000078"
					."	 SET 	Audhis = '".$fila['historia_destino']."', Auding = '".$fila['ing_ajustado']."'"
					." WHERE 	Audhis  = '".$fila['historia']."'"
					."   AND 	Auding = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000084"
					."	 SET 	Dethis = '".$fila['historia_destino']."', Deting = '".$fila['ing_ajustado']."'"
					." WHERE 	Dethis  = '".$fila['historia']."'"
					."   AND 	Deting = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000093"
					."	 SET 	Rechis = '".$fila['historia_destino']."', Recing = '".$fila['ing_ajustado']."'"
					." WHERE 	Rechis  = '".$fila['historia']."'"
					."   AND 	Recing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000095"
					."	 SET 	Ctchis = '".$fila['historia_destino']."', Ctcing = '".$fila['ing_ajustado']."'"
					." WHERE 	Ctchis  = '".$fila['historia']."'"
					."   AND 	Ctcing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000096"
					."	 SET 	Etuhis = '".$fila['historia_destino']."', Etuing = '".$fila['ing_ajustado']."'"
					." WHERE 	Etuhis  = '".$fila['historia']."'"
					."   AND 	Etuing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000113"
					."	 SET 	Jushis = '".$fila['historia_destino']."', Jusing = '".$fila['ing_ajustado']."'"
					." WHERE 	Jushis  = '".$fila['historia']."'"
					."   AND 	Jusing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000117"
					."	 SET 	Menhis = '".$fila['historia_destino']."', Mening = '".$fila['ing_ajustado']."'"
					." WHERE 	Menhis  = '".$fila['historia']."'"
					."   AND 	Mening = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000119"
					."	 SET 	Glnhis = '".$fila['historia_destino']."', Glning = '".$fila['ing_ajustado']."'"
					." WHERE 	Glnhis  = '".$fila['historia']."'"
					."   AND 	Glning = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000120"
					."	 SET 	Monhis = '".$fila['historia_destino']."', Moning = '".$fila['ing_ajustado']."'"
					." WHERE 	Monhis  = '".$fila['historia']."'"
					."   AND 	Moning = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000121"
					."	 SET 	Dmohis = '".$fila['historia_destino']."', Dmoing = '".$fila['ing_ajustado']."'"
					." WHERE 	Dmohis  = '".$fila['historia']."'"
					."   AND 	Dmoing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000122"
					."	 SET 	Stihis = '".$fila['historia_destino']."', Stiing = '".$fila['ing_ajustado']."'"
					." WHERE 	Stihis  = '".$fila['historia']."'"
					."   AND 	Stiing = '".$fila['ingreso']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			
			$query = "UPDATE 	".$wmovhos."_000123"
					."	 SET 	Conhis = '".$fila['historia_destino']."'"
					." WHERE 	Conhis  = '".$fila['historia']."'";
			$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
			*/
			/*FIN ACTUALIZANDO LAS TABLAS DE MOVHOS*/

			//Verifica que exista el prefijo CENCAM
			if(! empty( $wcencam ) ){
				//print echo "<br><b>Se actualiza central de camilleros</b><br>";
					/*****************ACTUALIZANDO CENCAM*********************/
					
					$query = "UPDATE 	".$wcencam."_000003"
							."	 SET 	Habitacion = Replace( Habitacion, '".$fila['historia']."', '".$fila['historia_destino']."')"
							." WHERE 	Habitacion  like '%".$fila['historia']."%'";
					//$res = mysql_query($query,$conex) or die( $log_errores.= "<br>NO ERP, actualizando centro de camilleros. Error No:".mysql_errno() );  
					
					$query = "UPDATE 	".$wcencam."_000003"
							."	 SET 	Observacion = Replace( Observacion, '".$fila['historia']."', '".$fila['historia_destino']."')"
							." WHERE 	Observacion  like '%".$fila['historia']."%'";
					//$res = mysql_query($query,$conex) or die( $log_errores.= "<br>NO ERP, actualizando centro de camilleros. Error No:".mysql_errno() );  
					/*****************FIN ACTUALIZANDO CENCAM*********************/
			}
			
			//Verifica que exista el prefijo CENPRO
			if(! empty( $wcenpro ) ){
				//print echo "<br><b>Se actualiza central de mezclas</b><br>";
					/*****************ACTUALIZANDO CENPRO*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wcenpro."' "
								."ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) or die( $log_errores.= "<br>NO ERP, buscando tablas con historia en cenpro. Error No:".mysql_errno() );  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
					
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$wcenpro."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) or die( $log_errores.= "<br>NO ERP, buscando ingreso en cenpro. Error No:".mysql_errno() ); 
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
						//NOACTUALIZO$res_update = mysql_query($query_update,$conex) or die( $log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en cenpro. Error No:".mysql_errno() ); 
						//print echo "<br> QUERY ".$query_update." </br>";
					}
					/*$query = "UPDATE 	".$wcenpro."_000002"
							."	 SET 	Arthis = '".$fila['historia_destino']."'"
							." WHERE 	Arthis  = '".$fila['historia']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
					
					$query = "UPDATE 	".$wcenpro."_000010"
							."	 SET 	Ajphis = '".$fila['historia_destino']."', Ajping = '".$fila['ing_ajustado']."'"
							." WHERE 	Ajphis  = '".$fila['historia']."'"
							."   AND 	Ajping = '".$fila['ingreso']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());  
					
					$query = "UPDATE 	".$wcenpro."_000020"
							."	 SET 	Pachis = '".$fila['historia_destino']."'"
							." WHERE 	Pachis  = '".$fila['historia']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); */
					/*****************FIN ACTUALIZANDO CENPRO*********************/
			}
			
			//Verifica que exista el prefijo CHEQUEO
			if(! empty( $wchequeo ) ){
				//print echo "<br><b>Se actualizan chequeos ejecutivos</b><br>";
					/*****************ACTUALIZANDO CHEQUEO*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wchequeo."' "
								." ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) or die($log_errores.= "<br>NO ERP, buscando tablas con historia en chequeo. Error No:".mysql_errno());  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
					
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$wchequeo."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) or die($log_errores.= "<br>NO ERP, buscando ingreso en chequeo. Error No:".mysql_errno()); 
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
						//NOACTUALIZO$res_update = mysql_query($query_update,$conex) or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en chequeo. Error No:".mysql_errno()); 
						//print echo "<br> QUERY ".$query_update." </br>";
					}	
					/*$query = "UPDATE 	".$wchequeo."_000002"
							."	 SET 	Imphis = '".$fila['historia_destino']."', Imping = '".$fila['ing_ajustado']."'"
							." WHERE 	Imphis  = '".$fila['historia']."'"
							."   AND 	Imping = '".$fila['ingreso']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); */
					/*****************FIN ACTUALIZANDO CHEQUEO*********************/
			}
			
			//Verifica que exista el prefijo FACHOS
			if(! empty( $wfachos ) ){
				//print echo "<br><b>Se actualiza facturacion hospitalaria (?)</b><br>";
				/*****************ACTUALIZANDO FACHOS*********************/
				$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wfachos."' "
								." ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) or die($log_errores.= "<br>NO ERP, buscando tablas con historia en fachos. Error No:".mysql_errno());  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
					
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$wfachos."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) or die( $log_errores.= "<br>NO ERP, buscando ingreso en fachos. Error No:".mysql_errno()); 
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
						//NOACTUALIZO$res_update = mysql_query($query_update,$conex) or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en fachos. Error No:".mysql_errno()); 
						//print echo "<br> QUERY ".$query_update." </br>";
					}	
				/*$query = "UPDATE 	".$wfachos."_000002"
						."	 SET 	Imphis = '".$fila['historia_destino']."', Imping = '".$fila['ing_ajustado']."'"
						." WHERE 	Imphis  = '".$fila['historia']."'"
						."   AND 	Imping = '".$fila['ingreso']."'";
				$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); */
				/*****************FIN ACTUALIZANDO FACHOS*********************/
			}
			
			//Verifica que exista el prefijo TCX
			if(! empty( $wtcx ) ){
				//print echo "<br><b>Se actualiza turnos de cirugia</b><br>";
					/*****************ACTUALIZANDO TCX*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wtcx."' "
								." ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) or die($log_errores.= "<br>NO ERP, buscando tablas con historia en turnoscirugia. Error No:".mysql_errno());  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
						//PARA TCX EL CAMPO INGRESO CONTIENE LA PALABRA "nin" NO LA PALABRA "ing"
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%nin%'  "                   
									."	    AND		medico = '".$wtcx."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) or die($log_errores.= "<br>NO ERP, buscando ingreso en turnoscirugia. Error No:".mysql_errno()); 
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
						//NOACTUALIZO$res_update = mysql_query($query_update,$conex) or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en turnoscirugia. Error No:".mysql_errno()); 
						//print echo "<br> QUERY ".$query_update." </br>";
					}	
					/*$query = "UPDATE 	".$wtcx."_000007"
							."	 SET 	Mcahis = '".$fila['historia_destino']."', Mcanin = '".$fila['ing_ajustado']."'"
							." WHERE 	Mcahis  = '".$fila['historia']."'"
							."   AND 	Mcanin = '".$fila['ingreso']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
					
						$query = "UPDATE 	".$wtcx."_000011"
							."	 SET 	Turhis = '".$fila['historia_destino']."', Turnin = '".$fila['ing_ajustado']."'"
							." WHERE 	Turhis  = '".$fila['historia']."'"
							."   AND 	Turnin = '".$fila['ingreso']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());*/
					/*****************FIN ACTUALIZANDO TCX*********************/
			}
			
			//Verifica que exista el prefijo MAGENTA
			if(! empty( $wmagenta ) ){
				//print echo "<br><b>Se actualiza magenta</b><br>";
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wmagenta."' "
								." ORDER BY		medico, codigo";
							
					$res_his = mysql_query($query_his,$conex) or die($log_errores.= "<br>NO ERP, buscando tabla con historia en magenta. Error No:".mysql_errno());  
					
					while($row_his = mysql_fetch_assoc($res_his)) {
						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "                   
									."	    AND		medico = '".$wmagenta."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";
									
						$res_ing = mysql_query($query_ing,$conex) or die($log_errores.= "<br>NO ERP, buscando ingreso en magenta. Error No:".mysql_errno()); 
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
						//NOACTUALIZO$res_update = mysql_query($query_update,$conex) or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en magenta. Error No:".mysql_errno()); 
						//print echo "<br> QUERY ".$query_update." </br>";
					}	
					/*****************ACTUALIZANDO MAGENTA*********************/
					/*$query = "UPDATE 	".$wmagenta."_000001"
							."	 SET 	Acthis = '".$fila['historia_destino']."', Acting = '".$fila['ing_ajustado']."'"
							." WHERE 	Acthis  = '".$fila['historia']."'"
							."   AND 	Acting = '".$fila['ingreso']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()); 
					
					$query = "UPDATE 	".$wmagenta."_000014"
							."	 SET 	Rephis = '".$fila['historia_destino']."', Reping = '".$fila['ing_ajustado']."'"
							." WHERE 	Rephis  = '".$fila['historia']."'"
							."   AND 	Reping = '".$fila['ingreso']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					
					$query = "UPDATE 	".$wmagenta."_000017"
							."	 SET 	Ccohis = '".$fila['historia_destino']."'"
							." WHERE 	Ccohis  = '".$fila['historia']."'";
					$res = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());*/
					/*****************FIN ACTUALIZANDO MAGENTA*********************/
			}
			
		}
		//$pila = array_reverse($pila);
		if(count($pila)>0){
			//print echo "<br>REPETIR PROCESO (NOERP)  ". count($pila) ."<br>";
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
		if ( empty ( $wmovhos ) )
			$basedatos = $wclisur;//clisur o soe
		else
			$basedatos = $wmovhos;
		//print echo '<BR>BORRANDO REGISTROS DEL MOVIMIENTO <BR>'.$movimiento;
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
		if (empty( $wclisur ))
			$logo = "clinica";
		else
			$logo = "logo_".$wclisur;
		
		encabezado("DESUNIFICADOR DE HISTORIAS",$wactualiz, $logo);
		
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
		if ( empty ( $wmovhos ) )
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

			$respuesta.="<tr onclick="."\""."javascript:mostrarOculto('".$id."',this)"."\"".">";

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

			$respuesta.=$texto_oculto;
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
				
				.botona:hover{
					background:#638BD5;
				
				}
						   
			    a img{ 
					border:0; 
				}
				
				.fondotd{
					background:#F1F4FA;
				
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
			
			function mostrarOculto(idElemento, elemento){
				//ESCONDE O MUESTRA LA FILA OCULTA
				if( $("#"+idElemento).is(":hidden")){
					$("#"+idElemento).fadeIn('fast');
				}else{
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
				$.get('desUnificadorDeHistorias.php', { wemp_pmla: wemp_pmla, action: "desunificar", movimiento: movimiento, consultaAjax: aleatorio } ,
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
