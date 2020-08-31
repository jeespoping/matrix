<?php
include_once("conex.php");
/**
 PROGRAMA                   : unificarDeHistorias.php
 AUTOR                      : Frederick Aguirre.
 FECHA CREACION             : 04 de Octubre de 2012

 DESCRIPCION:
 El objetivo del programa es unificar dos historias, reemplazando todos los registros de la historia origen, en los de la historia destino.
 Consiste en combinar los ingresos de la historia duplicada con los de la original (destino).
 Consiste en actualizar toda la información de los movimientos registrados para ambas historias con sus respectivos ingresos en todas las tablas donde pueda existir.
 El aplicativo está destinado hasta el momento para MOVIMIENTO HOSPITALARIO (incluyendo farpmla), SOE Y CLISUR

 CAMBIOS:
 2018-09-05 - Jessica Madrid:	- Se agrega la validación de empresa (wemp_pmla) en la función actualizarTablasERP() para evitar que
								  vuelva a actualizar todas las tablas de cliame que habían sido actualizadas previamente en la
								  función actualizarTablasNOERP() y en ésta función al consultar el campo de ingreso para la tabla
								  cliame_000101 se le agrega la validación ya que no cumple con la condición ing sino nin.
								- En la función actualizarTablasNOERP() para unificar los formularios de HCE se consultaban todos los
								  formularios configurados en hce_000001, se modifica el query para que solo actualice la historia e
								  ingreso de los formularios firmados (que tengan registro en hce_000036) para evitar updates innecesarios
								  que generan lentitud. Además se modifica el update para que utilice el consecutivo en las condiciones
								  del where y así haga uso del índice conhising_idx para que el proceso de unificación de las tablas de
								  HCE sea más rápido.
								- En la actualización de tablas de cencam se comentan los updates a la tabla cencam_000003 por los
								  campos Observación y Habitación ya que son queries que generan lentitud y actualizando el campo
								  Historia es suficiente.
								- Se modifica la variable $wtcx para que sea multiempresa (consultando en root_000051) ya que estaba
								  quemada y se actualizaba también en clisur y soe.
								- Se agrega en la funcion actualizarTablasNOERP() la unificación a las tablas del grupo ayucni.
								- En la función crearRegistroDeHistoriasUnificadas() se modifica la validación para saber el prefijo
								  de la tabla donde se guarda el registro de unificación ya que estaba tomando el prefijo mhoscs y
								  debía ser clisur, por tal motivo no se estaba creando el registro de unificación para clínica  del sur.
								- Se agregan comillas a los queries con  filtros por historia e ingreso que no las tenían y generaban
								  lentitud.
								- Se agrega la validación en la función actualizarTablasERP() para que solo actualice las tablas de
								  farpmla para cliame, es decir, cuando wemp_pmla sea 01.
								- En la función vistaInicial() se envía el logo correcto al pintar el encabezado ya que para cliame
								  quedaba sin logo.
 2017-05-09: * Camilo Zapata: se adiciona la opción y el funcionamiento necesario para la gestión de las solicitudes del cambio de documento desde admisiones.de manera que agilice los movimientos si se invoca desde el programa
               reporte de pacientes egresados y activos.
 2015-06-24: En el cambio de documento se cambian las condiciones del where para el update, se tiene en cuenta la historia y no el documento
			 Si al consultar una historia no existe en matrix, se consulta en UNIX y si existe, se guarda en las tablas de matrix.
			 Si al cambiar un documento, este ya existe en las tablas de matrix, se verifica si la historia ligada a este ha sido
			 unificada alguna vez, si es asi, se cambia ese documento concatenando un 00- para que se permita realizar el cambio del doc
 2015-03-24: Se ajusta el programa para SOE, buscando los ingresos en la tabla 100 y unificando en la tabla xxxx en especifico, los documentos
 2014-10-21: Solo se consulta en UNIX si el parámetro tieneConexionUnix así lo indica
 2014-02-03: Se cambia el programa de simulación, se adapta para ser más general, dado que había casos donde no permitía unificar.
 2013-09-24: Se ofrece escoger cuales ingresos de la historia origen son los que se desean unificar, mediante un checkbox se eligen los ingresos.
 2013-07-02: Se cambia la funcion que muestra la simulación cuando la historia origen no tiene ingresos porque ya fue unificada
			 Se evita unificar los ingresos que vengan de UNIX
 05 Abril 2013: Se cambian los llamados GET a POST
 28 Febrero 2013: Cuando las fechas de los ingresos de UNIX y MATRIX no coinciden, no permite realizar la unificacion
				  El ingreso 1 de un paciente, debe ser igual en matrix y unix.
 19 Diciembre 2012: Se acondiciona el programa para cencam a causa de los nuevos campos creados
 11 Diciembre 2012: Se consultan los ingresos de UNIX que no estan en MATRIX y se hace la simulacion y unificacion contando con ellos,
					dado que la unificacion en UNIX	y MATRIX debe quedar igual.
 23 octubre 2012: Se modifica la interfaz. Se ofrece la posibilidad de unificar el ultimo ingreso de una historia con cualquiera de sus anteriores. (Unificar ultimo ingreso)
 18 octubre 2012: Se realiza un código que ejecute las actualizaciones a las tablas correspondientes sin necesidad de "quemar" cada tabla.
 17 octubre 2012: Mostrar los ingresos y la fecha de las historias consultadas y se agregó la opcion de simular una unificación ( cómo queda la historia-ingreso )

**/
$wactualiz = "2018-09-05";

/*		----------------------CASO DE ESTUDIO-------------------------
A continuacion se muestra un caso para unificar la historia 185703(origen) con la 171601 (destino)
Luego de buscar los ingresos de ambas historias y ordenarlos por Fecha, Hora, Historia e ingreso tenemos un arreglo asi:
[ entre corchetes [] se muestra la cantidad de registros con esa historia-ingreso para una tabla abc ]
1)  Actualizar: 185703-5[2 registros] por: 171601-5
2)  Actualizar: 171601-5[4 registros] por: 171601-6
3)  Actualizar: 171601-6[3 registros] por: 171601-7
4)  Actualizar: 185703-6[1 registro]  por: 171601-8
5)  Actualizar: 185703-7 por: 171601-9
6)  Actualizar: 185703-8 por: 171601-10
7)  Actualizar: 171601-7 por: 171601-11

Supongamos que para una tabla abc se actualizan todos los registros de 1).
Los 2 registros que habian con 185703-5 se convierten a 171601-5, como resultado hay 2 registros con 171601-5.
PERO DE ESA HISTORIA-INGRESO YA EXISTIAN 4, como consecuencia, si actualizo los registros de 2)
los 4 QUE YA EXISTIAN y los 2 QUE YO CREE se convertiran en 171601-6 causando PERDIDA de informacion, porque sólo se deben cambiar por 171601-6, 4 registros.
Este caso sucede varias veces para el ejemplo.

COMO SOLUCION se hizo lo siguiente:
Cuando se va a actualizar se pregunta si la historia-origen ya existe en la primera columna (historias-ingresos) ORIGEN. [DESDE ESA POSICION EN ADELANTE]
SI ya existe SE APILA y se continua con el registro siguiente.
Al final se repite el proceso CON LOS DATOS DE LA PILA, es decir, el ULTIMO en ser APILADO es el PRIMERO en ser DESAPILADO para su actualizacion.
De esta manera se logra que no se PIERDA información.

PARA EL EJEMPLO:

actualizare: 185703-5 por: 171601-5
El destino ya existe, apilo

actualizare: 171601-5 por: 171601-6
El destino ya existe, apilo

actualizare: 171601-6 por: 171601-7
El destino ya existe, apilo

actualizare: 185703-6 por: 171601-8
actualizare: 185703-7 por: 171601-9
actualizare: 185703-8 por: 171601-10
actualizare: 171601-7 por: 171601-11

REPETIR PROCESO PORQUE HAY 3 EN LA PILA
RECORDAR QUE LA PILA ES LIFO( ultimo en entrar, primero en salir)

actualizare: 171601-6 por: 171601-7
actualizare: 171601-5 por: 171601-6 ( 171601-6 EXISTE EN LA COLUMNA ORIGEN, pero ya ha sido cambiado por 171601-7)
actualizare: 185703-5 por: 171601-5 ( 171601-5 EXISTE EN LA COLUMNA ORIGEN, pero ya ha sido cambiado por 171601-6)
*/


/*  -------------PROCESO CON LOS INGRESOS DE UNIX------------

 #####Se buscan los ingresos de la historia origen
			Si tiene ingresos quiere decir que en UNIX   NO HAN HECHO LA UNIFICACION
			y se muestran en un color distinto esos ingresos que no estan en matrix
			y TAMBIEN se buscan los ingresos de la historia destino

			Si no tiene ingresos indica que en UNIX YA SE HIZO LA UNIFICACION
			y que los ingresos con lo que se deben hacer los calculos son los de la historia destino DE UNIX
			EN DONDE SE VERA REFLEJADA LA UNIFICACION DE UNIX,  que debe quedar IGUAL a la unificacion en MATRIX

			2014-02-03:
			Dado a que en UNIX y ahora en MATRIX, se permiten traslado parciales, es decir, unificar ciertos ingresos (no todos)
			la metodología anterior ha sido cambiada.
*/
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";

	echo "<title>Unificar historias</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
	echo '<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
}

	//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//



	include_once("root/comun.php");



	$conex = obtenerConexionBD("matrix");
	$wmovhos = consultarPrefijosBD($conex, $wemp_pmla, 'movhos');
	$whce = consultarPrefijosBD($conex, $wemp_pmla, "hce");
	$wclisur = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");
	$wcencam = consultarPrefijosBD($conex, $wemp_pmla, "camilleros");
	$wcenpro = consultarPrefijosBD($conex, $wemp_pmla, "cenmez");
	$wchequeo = consultarPrefijosBD($conex, $wemp_pmla, "Chequeo Ejecutivo");
	$wfachos = consultarPrefijosBD($conex, $wemp_pmla, "Facturacion hospitalaria");
	// $wtcx = "tcx";//consultarPrefijosBD($conex, $wemp_pmla, "Facturacion hospitalaria");
	$wtcx = consultarPrefijosBD($conex, $wemp_pmla, "tcx");
	$wmagenta = consultarPrefijosBD($conex, $wemp_pmla, "afinidad");
	$wcliame = consultarPrefijosBD($conex, $wemp_pmla, "cliame");
	$ayucni = consultarPrefijosBD($conex, $wemp_pmla, "ayudas_diag");

	$tieneConexionUnix = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'conexionUnix' );

	$conexUnixPac; // conexion a unix para informacion de pacientes

	$log_errores="";
	$user_session = explode('-',$_SESSION['user']);
	$user_session = $user_session[1];

	//Variable que indica si la historia origen tiene ingresos en UNIX
	//Si es true, se consultan los ingresos de la historia destino
	//Si es false, no se consultan porque trae como resultado los ingresos de la unificacion en UNIX
	$whistoriaOrigenConIngUnix = false;

	//FIN***************************************************************//

	//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
	if( isset($_REQUEST['action'] )){
		$action = $_REQUEST['action'];
		if( $tieneConexionUnix == 'on' ){
			conexionOdbc($conex, 'movhos', $conexUnixPac, 'facturacion');
		}

		if ( @$_REQUEST['origenTieneUnix'] == 'true' ){
			$whistoriaOrigenConIngUnix = true;
		}

		if($action=="consultar"){
			ejecutarBusquedaHistoria( $_REQUEST['historia_ori'], @$_REQUEST['historia_des'] );
		}elseif($action=="unificar"){
			unificarNroHistoriaNOERP( $_REQUEST['historia_ori'], $_REQUEST['historia_des'], @$_REQUEST['ing_simulacion'] );
			unificarNroHistoriaERP( $_REQUEST['historia_ori'], $_REQUEST['historia_des'], @$_REQUEST['ing_simulacion']);
			actualizarCedulaen130(true, $_REQUEST['historia_ori'], @$_REQUEST['historia_des']);
		}elseif($action=="simular"){
			simularUnificacion( $_REQUEST['historia_ori'], $_REQUEST['historia_des'], @$_REQUEST['ingresos_origen'], @$_REQUEST['trasladoParcial'], @$_REQUEST['trasladoCompleto'], @$_REQUEST['listoEnUnix'] );
		}elseif($action=="unificarUltimoIngreso"){
			unificarUltimoIngreso( $_REQUEST['historia'], $_REQUEST['ingreso_origen'], $_REQUEST['ingreso_destino'] );
		}elseif( $action=="cambiarDocumento"){
			cambiarDocumento($_REQUEST['historia'], $_REQUEST['tipodoc'], $_REQUEST['documento'], $_REQUEST['tipodoca'], $_REQUEST['documentoa']);
		}

		if( $tieneConexionUnix == 'on' ){
			@odbc_close( $conexUnixPac );
		}
		return;
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

	function cambiarDocumento($whis, $wtipodoc, $wdocumento,$wtipoAntes, $wdocAntes){
		global $conexUnixPac, $wemp_pmla, $conex;
		global $tieneConexionUnix;
		global $wmovhos;
		global $wclisur;

		$resultados = array('res' => '', 'msg' => '');

		$query="SELECT 		pactid, pacced
				  FROM 		inpaci
				 WHERE 		pachis='".trim($whis)."'";

		$wtipodocunix = "";
		$wdocumentounix = "";

		if( $tieneConexionUnix == "on" ){
			$err_o = odbc_do($conexUnixPac,$query) or die( odbc_error()." $query - ".odbc_errormsg() );
			while (odbc_fetch_row($err_o)){
				$wtipodocunix = odbc_result($err_o, 'pactid');
				$wdocumentounix = odbc_result($err_o, 'pacced');
			}
		}

		$resultados['res'] = "NO";
		if( ($wtipodocunix == '' || $wdocumentounix == '') && $tieneConexionUnix == "on" ){
			$resultados['msg'] = "No hay datos del paciente en Servinte";
		}
		else if( trim($wtipodocunix) != $wtipodoc && $tieneConexionUnix == "on"){
			$resultados['msg'] = "El tipo de documento es diferente en Servinte ".$wtipodocunix;
		}
		else if( trim($wdocumentounix) != $wdocumento && $tieneConexionUnix == "on"){
			$resultados['msg'] = "El numero de documento es diferente en Servinte ".$wdocumentounix;
		}else{
			$cliame = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");

			$existe = false;

			/*$q = " SELECT 'root 36' as tabla
					 FROM root_000036
					WHERE Pactid='".$wtipodoc."'
					  AND Pacced = '".$wdocumento."'
				   UNION
				   SELECT 'root 37' as tabla
					 FROM root_000037
					WHERE Oritid='".$wtipodoc."'
					  AND Oriced = '".$wdocumento."'
					  AND Oriori = '".$wemp_pmla."'
				   UNION
				   SELECT '".$cliame." 100' as tabla
					 FROM ".$cliame."_000100
					WHERE Pactdo='".$wtipodoc."'
					  AND Pacdoc = '".$wdocumento."'";*/
			$q = " SELECT 'root 36,37' as tabla, Orihis as his
					 FROM root_000037,root_000036
					WHERE Oritid='".$wtipodoc."'
					  AND Oriced = '".$wdocumento."'
					  AND Pactid = Oritid
					  AND Pacced = Oriced
					  AND Oriori = '".$wemp_pmla."'
				   UNION
				   SELECT '".$cliame." 100' as tabla, Pachis as his
					 FROM ".$cliame."_000100
					WHERE Pactdo='".$wtipodoc."'
					  AND Pacdoc = '".$wdocumento."'";

			$res = mysql_query($q, $conex);
			$num = mysql_num_rows($res);

			if ($num > 0){
				/*2015-06-23
				Si la historia asociada al documento destino esta en el historico de las unificaciones,
				quiere decir que se esta tratando de dejar a la historia destino con el documento de la historia origen
				y se debe permitir el cambio de documento, para ello, hay que borrar el registro en matrix de la historia origen*/

				$basedatos = "";
				//Determino en que bd se guardara el registro
				if ( empty ( $wmovhos ) )
					$basedatos = $wclisur;//clisur o soe
				else
					$basedatos = $wmovhos;

				$tablaRHU = consultarPrefijosBD($conex, $wemp_pmla, 'tablarhu');

				$arr_datos = array();

				$historias_buscadas = array();
				while($row = mysql_fetch_array($res)){

					if( in_array($row[1], $historias_buscadas) == true )
						continue;

					//Busco si dicha historia esta en el historico de unificaciones
					$q1 = " SELECT id
								 FROM ".$basedatos."_".$tablaRHU."
								WHERE Rhuhia='".$row[1]."'
							LIMIT 1";
					$res1 = mysql_query($q1, $conex);
					$num1 = mysql_num_rows($res1);

					if ($num1 > 0){
						//Como la historia si existe en el historico de unificaciones, se eliminan los registros
						$query =  "UPDATE root_000036 SET Pacced = '00-".$wdocumento."' WHERE Pactid = '".$wtipodoc."' AND Pacced = '".$wdocumento."'";
						$err1 = mysql_query($query,$conex);
						$query =  "UPDATE root_000037 SET Oriced = '00-".$wdocumento."' WHERE Orihis='".$row[1]."' AND Oriori='".$wemp_pmla."'";
						$err1 = mysql_query($query,$conex);
						$query =  "UPDATE ".$cliame."_000100 SET Pacdoc = '00-".$wdocumento."' WHERE Pachis='".$row[1]."'";
						$err1 = mysql_query($query,$conex);
					}else{
						array_push($arr_datos, $row );
					}
					array_push($historias_buscadas, $row[1] );
				}


				if( count( $arr_datos ) > 0 ){
					$existe = true;
					$resultados['msg'] = "Error. El documento por el que desea cambiar ya existe en la(s) tabla(s): \n";
					foreach($arr_datos as $rowx){
						$resultados['msg'].= $rowx[0]." con la historia: ".$rowx[1]." que no ha sido unificada.";
					}
					$resultados['msg'] = rtrim( $resultados['msg'], ", " );
				}
			}

			if( $existe == false ){
				/*2015-06-18*/
				/*$query =  "UPDATE root_000036 SET Pactid = '".$wtipodoc."', Pacced = '".$wdocumento."' WHERE Pactid='".$wtipoAntes."' AND Pacced = '".$wdocAntes."'";
				$err1 = mysql_query($query,$conex);
				$query =  "UPDATE root_000037 SET Oritid = '".$wtipodoc."', Oriced = '".$wdocumento."' WHERE Oritid='".$wtipoAntes."' AND Oriced = '".$wdocAntes."' AND Oriori='".$wemp_pmla."'";
				$err1 = mysql_query($query,$conex);
				$query =  "UPDATE ".$cliame."_000100 SET Pactdo = '".$wtipodoc."', Pacdoc = '".$wdocumento."' WHERE Pactdo='".$wtipoAntes."' AND Pacdoc = '".$wdocAntes."'";
				$err1 = mysql_query($query,$conex);
				$resultados['res'] = "OK";*/


				$query =  "UPDATE root_000036 SET Pactid = '".$wtipodoc."', Pacced = '".$wdocumento."' WHERE Pactid='".$wtipoAntes."' AND Pacced = '".$wdocAntes."'";
				$err1 = mysql_query($query,$conex);
				$query =  "UPDATE root_000037 SET Oritid = '".$wtipodoc."', Oriced = '".$wdocumento."' WHERE Orihis='".$whis."' AND Oriori='".$wemp_pmla."'";
				$err1 = mysql_query($query,$conex);
				$query =  "UPDATE ".$cliame."_000100 SET Pactdo = '".$wtipodoc."', Pacdoc = '".$wdocumento."' WHERE Pachis='".$whis."'";
				$err1 = mysql_query($query,$conex);
				$resultados['res'] = "OK";

				actualizarCedulaen130(false, $wdocAntes, $wdocumento);
				/*$query =  "UPDATE encumage_000049 SET Encced = '".$wdocumento."' WHERE Encced = '".$wdocAntes."'";
				$err1 = mysql_query($query,$conex);*/
				/*$query =  "UPDATE adpatol SET Rpaced = '".$wdocumento."' WHERE Rpaced = '".$wdocAntes."'";
				$err1 = mysql_query($query,$conex);*/

				if( $resultados['res'] == "OK" ){
					$query = " UPDATE {$cliame}_000288
								  SET Scdest = 'ok'
								WHERE Scdtda = '{$wtipoAntes}'
								  AND Scddoa = '{$wdocAntes}'
								  AND Scdest = 'on' ";
					$rs    = mysql_query( $query, $conex );
				}
			}
		}
		echo json_encode( $resultados );
	}

	//Para consultar el prefijo de la bd ya que la aplicacion y el codigo de la tabla RHU para las distintas empresas
	function consultarPrefijosBD($conexion, $codigoInstitucion, $nombreAplicacion){
		$q = " SELECT 	Detval
				 FROM 	root_000051
				WHERE   Detemp = '".$codigoInstitucion."'
				  AND 	Detapl = '".$nombreAplicacion."'";

		$res = mysql_query($q, $conexion);
		$num = mysql_num_rows($res);

		$alias = "";
		if ($num > 0){
			$rs = mysql_fetch_array($res);
			$alias = $rs['Detval'];
		}
		return $alias;
	}

	function unificarUltimoIngreso($whis, $wing_ori, $wing_des){

		global $wmovhos;
		global $wclisur;
		global $wemp_pmla;
		global $conex;

		if( ! empty( $wmovhos ) ){
			//BUSCANDO SI EXISTE DESTINO EN 16
			$query = " SELECT * FROM ".$wmovhos."_000016 WHERE Inghis = '".$whis."' AND Inging='".$wing_des."'";
			$res = mysql_query($query, $conex);
			$num = mysql_num_rows($res);
			if($num > 0 ){ //BORRAMOS EL REGISTRO DEL INGRESO ORIGEN
				$query = " DELETE FROM ".$wmovhos."_000016 WHERE Inghis = '".$whis."' AND Inging='".$wing_ori."'";
				$res = mysql_query($query, $conex);
			}
			//BUSCANDO SI EXISTE DESTINO EN 18
			$query = " SELECT * FROM ".$wmovhos."_000018 WHERE Ubihis = '".$whis."' AND Ubiing='".$wing_des."'";
			$res = mysql_query($query, $conex);
			$num = mysql_num_rows($res);
			if($num > 0 ){ //BORRAMOS EL REGISTRO DEL INGRESO ORIGEN
				$query = " DELETE FROM ".$wmovhos."_000018 WHERE Ubihis = '".$whis."' AND Ubiing='".$wing_ori."'";
				$res = mysql_query($query, $conex);
			}
		}else{
			$query = " SELECT * FROM ".$wclisur."_000101 WHERE inghis = '".$whis."' AND ingnin='".$wing_des."'";
			$res = mysql_query($query, $conex);
			$num = mysql_num_rows($res);
			if($num > 0 ){ //BORRAMOS EL REGISTRO DEL INGRESO ORIGEN
				$query = " DELETE FROM ".$wclisur."_000101 WHERE inghis = '".$whis."' AND ingnin='".$wing_ori."'";
				$res = mysql_query($query, $conex);
			}
		}



		//ACTUALIZO EN LA TABLA ROOT 37
		$query = " UPDATE root_000037 set Oriing='".($wing_ori-1)."' WHERE Orihis = '".$whis."' AND Oriori='".$wemp_pmla."'";
		$res = mysql_query($query, $conex);

		$datatmp['historia'] = $whis;
		$datatmp['historia_destino'] = $whis;
		$datatmp['ingreso'] = $wing_ori;
		$datatmp['ing_ajustado'] = $wing_des;

		$wresultado_ingresos = array();
		array_push( $wresultado_ingresos, $datatmp );



		if(! empty( $wmovhos ) ){
			actualizarTablasNOERP($wresultado_ingresos );
			$isFarpmla = true;
			actualizarTablasERP($wresultado_ingresos, $isFarpmla );
		}
		if(! empty( $wclisur ) ){
			$isFarpmla = false;//FALSE porque se actualizan tablas de SOE Y CLISUR, no las de FARPMLA
			actualizarTablasERP($wresultado_ingresos, $isFarpmla );
		}

		$unificacion_tipo_ingreso = "1";
		crearRegistroDeHistoriasUnificadas( $wresultado_ingresos, $unificacion_tipo_ingreso );

		if( empty($log_errores) ){
			echo "Unificacion realizada con exito";
		}else{
			echo "Errores presentados: <br><br> ".$log_errores;
		}
	}
	//ERP, PARA CLINICA DEL SUR Y SOE,   FARPMLA
	function unificarNroHistoriaERP($whis_origen, $whis_destino, $wingresosSimulacion ){
		//TODO LO QUE ES FACTURACION, ADMISIONES, CARTERA ETC

		global $wclisur;
		global $conex;
		global $log_errores;
		global $whistoriaOrigenConIngUnix;

		if(empty( $wclisur ) ){
			return;
		}

		$wingresosSimulacion = str_replace("\\", "", $wingresosSimulacion);
		$wingresosSimulacion = json_decode( $wingresosSimulacion, true );
		$wresultado_ingresos = array();
		$dato="";
		$dato=array();
		$ingreso = 0;
		foreach ($wingresosSimulacion as $val=>$ing){
			if($ingreso==0){
				$ingreso = $ing['ingreso'];
			}
			$dato['historia'] = $ing['historia'];
			$dato['ingreso'] = $ing['ingreso'];
			$dato['historia_destino'] = $whis_destino;
			//$dato['ing_ajustado'] = $ingreso;
			( isset($ing['ing_ajustado']) )? $dato['ing_ajustado'] = $ing['ing_ajustado'] : $dato['ing_ajustado'] = $ingreso;

			if( isset( $ing['unix'] ) == false ){  //2013-07-02
				array_push( $wresultado_ingresos, $dato );
			}
			$ingreso++;
		}
		//Se actualizan las tablas necesarias
		$isFarpmla = false;//FALSE porque se actualizan tablas de SOE Y CLISUR, no las de FARPMLA
		actualizarTablasERP($wresultado_ingresos, $isFarpmla );

		//Se crea el log, en donde se guarda que historia-ingreso es reemplazada por cual historia-ingreso
		crearRegistroDeHistoriasUnificadas( $wresultado_ingresos );

		if( empty($log_errores) ){
			echo "Unificacion finalizada";
		}else{
			echo "Errores presentados: <br><br> ".$log_errores;
		}

	}

	//El primer parametro ($whistorias) indica si los dos siguientes son historias o los documentos
	function actualizarCedulaen130($whistorias, $whis_origen, $whis_destino=''){
		global $wclisur;
		global $wemp_pmla;
		global $conex;

		$documentoOrigen = "";
		$documentoDestino = "";

		if( $whis_origen == $whis_destino )
			return;

		$unificar130 = 'off';

		$query = " SELECT Detval as valor
					 FROM root_000051
				    WHERE Detapl='unificaren130'
					  AND Detemp='".$wemp_pmla."'";
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		if($num > 0 ){
			$row = mysql_fetch_array( $res );
			$unificar130 = $row['valor'];
		}

		if( $unificar130 != 'on' )
			return;

		if( $whistorias == true ){
			$query = " SELECT Orihis as his, Oriced as doc
						 FROM root_000037
						WHERE Orihis IN ( '".$whis_origen."', '".$whis_destino."' )
						  AND Oriori='".$wemp_pmla."'";
			$res = mysql_query($query, $conex);
			$num = mysql_num_rows($res);
			if($num > 0 ){
				while( $row = mysql_fetch_array( $res ) ){
					if( $whis_origen == $row['his'] )
						$documentoOrigen = $row['doc'];

					if( $whis_destino == $row['his'] )
						$documentoDestino = $row['doc'];
				}
			}
		}else{
			$documentoOrigen= $whis_origen;
			$documentoDestino= $whis_destino;
		}

		if( $documentoOrigen != '' && $documentoDestino != "" && $documentoOrigen != $documentoDestino ){
			$q= " UPDATE ".$wclisur."_000130 SET Identificacion='".$documentoDestino."'
				   WHERE Identificacion= '".$documentoOrigen."'";
			$res = mysql_query($q,$conex);
		}
	}

	function actualizarTablasERP( $datos, $isFarpmla ){

		global $wclisur;
		global $wemp_pmla;
		global $conex;

		global $log_errores;




		$wclisur = "";
		//SE ACTUALIZARAN TABLAS DE FARMPLA?
		if( $isFarpmla ){
			if ( $wemp_pmla == '01' || $wemp_pmla == '09' )
			{
				$wclisur = consultarPrefijosBD($conex, '09', "farpmla");
			}
		}else{
			// si el wemp_pmla es 01 las tablas de cliame ya se actualizaron en actualizarTablasNOERP
			if ( $wemp_pmla != '01' )
			{
				$wclisur = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");
			}
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

			if(! empty( $wclisur ) )
			{
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
					$query_update = " UPDATE 	".$wclisur."_".$row_his['tabla']
									."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
									." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
									.$and_ing;
					$res_update = mysql_query($query_update,$conex) ; // or die( $log_errores.= "<br>ERP, actualizando ".$row_his['tabla']." Error No:".mysql_errno() );
				}
				/*****************FIN ACTUALIZANDO CLISUR O SOE O FARPMLA*********************/

			}

		}

		//Se le hace reverse a la pila para que el ultimo en apilarse sea el primero en salir
		$pila = array_reverse($pila);
		if(count($pila)>0){
			actualizarTablasERP( $pila, $isFarpmla );
		}
		$wclisur = consultarPrefijosBD($conex, $wemp_pmla, "facturacion");
	}

	//NO ERP, PARA CLINICA LAS AMERICAS
	function unificarNroHistoriaNOERP($whis_origen, $whis_destino, $wingresosSimulacion ){
		//MOVHOS, HCE, MAGENTA, FACHOS, CENPRO ETC
		global $conex;
		global $wmovhos;
		global $whistoriaOrigenConIngUnix;
		global $wemp_pmla;

		global $log_errores;

		if(empty( $wmovhos ) ){
			return;
		}

		$wingresosSimulacion = str_replace("\\", "", $wingresosSimulacion);
		$wingresosSimulacion = json_decode( $wingresosSimulacion, true );
		$wresultado_ingresos = array();
		$dato="";
		$dato=array();
		$ingreso = 0;
		foreach ($wingresosSimulacion as $val=>$ing){
			if($ingreso==0){
				$ingreso = $ing['ingreso'];
			}
			$dato['historia'] = $ing['historia'];
			$dato['ingreso'] = $ing['ingreso'];
			$dato['historia_destino'] = $whis_destino;
			( isset($ing['ing_ajustado']) )? $dato['ing_ajustado'] = $ing['ing_ajustado'] : $dato['ing_ajustado'] = $ingreso;

			if( isset( $ing['unix'] ) == false ){ //2013-07-02
				array_push( $wresultado_ingresos, $dato );
			}

			$ingreso++;
		}

		//ACTUALIZO EN LA TABLA ROOT 37
		$query = " UPDATE root_000037 set Oriing='".($ingreso-1)."' WHERE Orihis = '".$whis_destino."' AND Oriori='".$wemp_pmla."'";
		$res = mysql_query($query, $conex);

		//Se actualizan las tablas necesarias
		actualizarTablasNOERP($wresultado_ingresos );
		///return;//XXX
		//Se actualizan tablas de ERP para FARPMLA, ya que funciona similar a SOE Y CLISUR, pero
		//enviando un parametro indicando que es para farpmla
		$isFarpmla = true;
		actualizarTablasERP($wresultado_ingresos, $isFarpmla );

		//Se crea el log, en donde se guarda que historia-ingreso es reemplazada por cual historia-ingreso
		crearRegistroDeHistoriasUnificadas( $wresultado_ingresos );
		if( empty($log_errores) ){
			echo "Unificacion realizada con exito";
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
		global $wcliame;
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



			//XXX
			//echo "<br>CONVERTIR: ".$fila['historia']."-".$fila['ingreso']."     A     ".$fila['historia_destino']."-".$fila['ing_ajustado'];

			//Si la historia-ingreso ORIGEN son el mismo historia-ingreso DESTINO, NO ACTUALIZE TABLAS
			if( $fila['historia_destino']."-".$fila['ing_ajustado']  ==  $fila['historia']."-".$fila['ingreso']){
				continue;
			}
			//continue;//XXX

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
					// $query_hce = "SELECT 	Encpro as ind_tab
								    // FROM 	".$whce."_000001
							    // GROUP BY 	Encpro
							    // ORDER BY 	Encpro";

					//Busco los formularios firmados de hce que se deben actualizar
					$query_hce = "SELECT Firpro as ind_tab
									FROM ".$whce."_000036
								   WHERE Firhis='".$fila['historia_destino']."'
									 AND Firing='".$fila['ing_ajustado']."'
								GROUP BY Firpro;";
					$res_hce = mysql_query($query_hce, $conex) ;//; // or die( $log_errores.= "<br>NO ERP, buscando tablas HCE. Error No:".mysql_errno() );

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

						$res_update_hce = mysql_query($query_update_hce, $conex) ; // ; // or die( $log_errores.= "<br>NO ERP, actualizando tabla ".$row['ind_tab']." de HCE. Error No:".mysql_errno() );
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
				$query_update = " UPDATE 	".$wmovhos."_".$row_his['tabla']
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
						$query_update = " UPDATE 	".$wcenpro."_".$row_his['tabla']
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
						$query_update = " UPDATE 	".$wchequeo."_".$row_his['tabla']
										."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
										." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
										.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en chequeo. Error No:".mysql_errno());
					}
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
						$query_update = " UPDATE 	".$wtcx."_".$row_his['tabla']
									  ."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
										." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
										.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en turnoscirugia. Error No:".mysql_errno());
					}

					/*****************FIN ACTUALIZANDO TCX*********************/
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
						$query_update = " UPDATE 	".$wmagenta."_".$row_his['tabla']
										."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
										." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
										.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die($log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en magenta. Error No:".mysql_errno());
					}
			}

			//Verifica que exista el prefijo CLIAME
			if(! empty( $wcliame ) ){
					/*****************ACTUALIZANDO CLIAME*********************/
					$query_his = "	SELECT 		medico, codigo as tabla, descripcion as campo  "
								."	  FROM 		det_formulario "
								."	 WHERE 		activo = 'A'  "
								."	   AND 		descripcion like '%his%'  "
								."	   AND		medico = '".$wcliame."' "
								."ORDER BY		medico, codigo";

					$res_his = mysql_query($query_his,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando tablas con historia en cliame. Error No:".mysql_errno() );

					while($row_his = mysql_fetch_assoc($res_his)) {

						$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
									."	   FROM 	det_formulario "
									."	  WHERE 	activo = 'A'  "
									."	    AND 	descripcion like '%ing%'  "
									."	    AND		medico = '".$wcliame."'"
									."	    AND		codigo = '".$row_his['tabla']."'"
									." ORDER BY		medico, codigo";

						//La tabla 101 tiene el campo de ingreso con la frase "nin"
						if( $row_his['tabla'] == "000101" ){
							$query_ing = "	 SELECT 	medico, codigo as tabla, descripcion as campo  "
										."	   FROM 	det_formulario "
										."	  WHERE 	activo = 'A'  "
										."	    AND 	descripcion like '%nin%'  "
										."	    AND		medico = '".$wcliame."'"
										."	    AND		codigo = '".$row_his['tabla']."'"
										." ORDER BY		medico, codigo";
						}

						$res_ing = mysql_query($query_ing,$conex) ; // or die( $log_errores.= "<br>NO ERP, buscando ingreso en cliame. Error No:".mysql_errno() );
						$num_ing = mysql_num_rows($res_ing);
						$and_ing = "";
						$upt_ing = "";
						if( $num_ing == 1 ){
							$row_ing = mysql_fetch_assoc($res_ing);
							$and_ing = " AND ".$row_ing['campo']." = '".$fila['ingreso']."'";
							$upt_ing = " , ".$row_ing['campo']." = '".$fila['ing_ajustado']."'";
						}
						$query_update = " UPDATE 	".$wcliame."_".$row_his['tabla']
										."	 SET 	".$row_his['campo']." = '".$fila['historia_destino']."'".$upt_ing
										." WHERE 	".$row_his['campo']."  = '".$fila['historia']."'"
										.$and_ing;
						$res_update = mysql_query($query_update,$conex) ; // or die( $log_errores.= "<br>NO ERP, actualizando formulario ".$row_his['tabla']." en cliame. Error No:".mysql_errno() );
					}
					/*****************FIN ACTUALIZANDO CLIAME*********************/
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
		$pila = array_reverse($pila);
		if(count($pila)>0){
			actualizarTablasNOERP( $pila );
		}
	}

	//ES LLAMADA PARA BUSCAR LA INFORMACION PERSONAL DE UNA O AMBAS HISTORIAS
	function ejecutarBusquedaHistoria( $whis1='', $whis2='' ){

		global $whistoriaOrigenConIngUnix;

		$pacientes = array();
		$validarTrasladoParcial = false;
		$validarTrasladoCompleto = false;
		$unificacionListaEnUnix = false;

		if( $whis1 != '' ){
			$pac1 = buscarPaciente( $whis1 );

			if( $pac1['doc'] == ""  ){
				//Funcion que trata de consultar en UNIX (si $tieneConexionUnix == 'on') la informacion del paciente
				//y la inserta en las tablas de matrix
				$hayDatUn = traerPacienteDeUnix( $whis1 );
				if( $hayDatUn == true )
					$pac1 = buscarPaciente( $whis1 );
			}

			if( $pac1['doc'] != "" ){
				//Busco ingresos de UNIX para la historia origen
				$ingresos_unix = buscarIngresosPacienteUNIX( $whis1 );

				if( !empty( $ingresos_unix ) ){ //Si hay ingresos de UNIX...
					$whistoriaOrigenConIngUnix = true;
					$lastUnix = end($ingresos_unix);
					$lastMatrix = end($pac1['ingresos']);
					if( $lastUnix['ingreso'] < $lastMatrix['ingreso'] ){
						//El ultimo ingreso de matrix es mayor al ultimo ingreso en UNIX, en UNIX se hizo una unificacion parcial
						$validarTrasladoParcial = true;
						$unificacionListaEnUnix = true;
					}else if($lastUnix['ingreso'] == $lastMatrix['ingreso']){
						//En UNIX no se ha hecho la unificacion
					}else{
						//El ultimo ingreso de UNIX es mayor al ultimo de matrix, esto no es posible

					}
				}else{
					//No hay ingresos en UNIX, ya se hizo la unificacion completa
					$validarTrasladoCompleto = true;
					$unificacionListaEnUnix = true;
				}

				if ( !empty($ingresos_unix) && $validarTrasladoParcial == false ){
					//Juntar los ingresos si no hay errores y no se trata de un traslado parcial
					$pac1['ingresos'] = juntarIngresosMatrixUnix( $pac1['ingresos'] , $ingresos_unix );
				}
			}
			array_push( $pacientes, $pac1 );
		}

		if( $whis2 != '' ){
			$pac2 = buscarPaciente( $whis2 );

			if( $pac2['doc'] == ""  ){
				//Funcion que trata de consultar en UNIX (si $tieneConexionUnix == 'on') la informacion del paciente
				//y la inserta en las tablas de matrix
				$hayDatUn = traerPacienteDeUnix( $whis2 );
				if( $hayDatUn == true )
					$pac2 = buscarPaciente( $whis2 );
			}


			if(  $pac2['doc'] != "" ){
					if( $whistoriaOrigenConIngUnix == true || $validarTrasladoParcial == true){
						//Busco ingresos de UNIX para la historia destino
						$ingresos_unix2 = buscarIngresosPacienteUNIX( $whis2 );
						//if( $validarTrasladoParcial == false )
							$pac2['ingresos'] = juntarIngresosMatrixUnix( $pac2['ingresos'] , $ingresos_unix2 );
					}
			}
			array_push( $pacientes, $pac2 );
		}

		$jsonRes = array("pacientes" => $pacientes,
						 "parcial" => $validarTrasladoParcial,
						 "completo" => $validarTrasladoCompleto,
						 "listounix" => $unificacionListaEnUnix );

		//Envia como respuesta un texto en formato JSON
		$json_response = json_encode( $jsonRes );

		echo $json_response;
	}

	function traerPacienteDeUnix($whis){
		global $conexUnixPac, $wemp_pmla, $wcliame, $conex;
		global $tieneConexionUnix;
		$wbasedato = $wcliame;

		if( $wcliame == "" )
			return;
		if( $tieneConexionUnix != "on" )
			return;

		$hayDatos = false;
		//					1		2		3		4		5		6		7		8		9		10		11		12		13		14		15		16		17		18		19
		$sql = "    SELECT pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacing, pacnum
					  FROM inpaci
					 WHERE pachis='".trim($whis)."'
					   AND pactra IS NULL
					   AND pacap2 IS NULL
					 UNION
					SELECT pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacing, pacnum
					  FROM inpaci
					 WHERE pachis='".trim($whis)."'
					   AND pactra IS NULL
					   AND pacap2 IS NOT NULL
					 UNION
					SELECT pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacing, pacnum
					  FROM inpaci
					 WHERE pachis='".trim($whis)."'
					   AND pactra IS NOT NULL
					   AND pacap2 IS NULL
					 UNION
					SELECT pachis, pacced, pactid, pacnui, pacap1, pacap2, pacnom, pacsex, pacnac, paclug, pacest, pacdir, pactel, paczon, pacmun, pactra, pacniv, pacing, pacnum
					  FROM inpaci
					 WHERE pachis='".trim($whis)."'
					   AND pactra IS NOT NULL
					   AND pacap2 IS NOT NULL
					";

			$err_o = odbc_do( $conexUnixPac, $sql );

			if( $err_o ){
				while( odbc_fetch_row($err_o) )
				{
					if( trim( odbc_result($err_o,2) ) == '' )
						continue;

					$paisNac = '';

					//Consulto el codigo del pais de nacimiento
					$sqlPais = "SELECT codigoPais
								  FROM root_000002
								 WHERE Codigo = '".trim( substr( odbc_result($err_o,10), 0, 2 ) )."'
							  ORDER BY Descripcion
							";

					$resPais = mysql_query( $sqlPais, $conex ) or die( mysql_errno()." - Error en el query $sqlPais - ".mysql_error() );

					if( $rows = mysql_fetch_array( $resPais ) ){
						$paisNac = $rows[ 'codigoPais' ];
					}

					$paisRes = '';

					//Consulto el codigo del pais de residencia
					$sqlPais = "SELECT codigoPais
								  FROM root_000002
								 WHERE Codigo = '".trim( substr( trim( odbc_result($err_o,15) ), 0, 2 ) )."'
							  ORDER BY Descripcion
							";

					$resPais = mysql_query( $sqlPais, $conex ) or die( mysql_errno()." - Error en el query $$sqlPais - ".mysql_error() );

					if( $rows = mysql_fetch_array( $resPais ) ){
						$paisRes = $rows[ 'codigoPais' ];
					}

					$estCivil = '';

					//Consulto estado civil
					$sqlEstadoCivil = "SELECT Selcod
										 FROM ".$wbasedato."_000105
										WHERE Seltip = '25'
										  AND selmat = '".trim( odbc_result($err_o,11) )."'
										";

					$resPais = mysql_query( $sqlEstadoCivil, $conex ) or die( mysql_errno()." - Error en el query $sqlEstadoCivil - ".mysql_error() );

					if( $rows = mysql_fetch_array( $resPais ) ){
						$estCivil = $rows[ 'Selcod' ];
					}

					//Avriguo el primer y segundo nombre
					$nombreUnix = trim( odbc_result($err_o,7) );
					$posSegundoNombre = strpos( $nombreUnix, ' ' );
					if( $posSegundoNombre > 0 ){
						$pacNom1 = substr(  $nombreUnix , 0, $posSegundoNombre ) ;
						$pacNom2 = trim( substr( $nombreUnix, $posSegundoNombre ) ) ;
					}
					else{
						$pacNom1 =  $nombreUnix ;
						$pacNom2 = '';
					}

					//Consulto tipo de residencia
					$tipoResidencia = 'E';	//Extranjera
					if( $paisRes == '169' ){
						$tipoResidencia = 'N';	//Nacional
					}

					//Traer responsable
					$qunix = "SELECT egrcer,egring
								FROM inmegr
							   WHERE egrhis='".$whis."'
								 AND egrnum='".trim( odbc_result($err_o,19) )."'
								 AND egrcer IS NOT NULL
								 AND egring IS NOT NULL
								";

					$err_res= odbc_do($conexUnixPac,$qunix);
					$num_o = odbc_num_fields($err_res);

					$fecha_ingreso = "";
					$responsable = "";
					while( odbc_fetch_row($err_res) )
					{
						if( trim( odbc_result($err_res,1) ) == '' )
							continue;
						$fecha_ingreso = str_replace("/","-",odbc_result($err_res,2));
						$responsable=  odbc_result($err_res,1);
					}


					//Inserto los datos demograficos del paciente
					$sql = "INSERT INTO ".$wbasedato."_000100(    Medico   , 	Fecha_data, 					Hora_data,               Pachis               ,             Pactdo                 ,                      Pacdoc        , Pactat	,            Pacap1               	 ,               Pacap2     		  ,    Pacno1     ,     Pacno2    ,                 Pacfna               ,               Pacsex               ,    Pacest      ,                          Pacdir                       ,                     Pactel                            ,               Paciu                 , Pacbar,                   Pacdep                            ,   Pacpan  ,                Paczon              ,              Pacmuh                ,                         Pacdeh                       ,  Pacpah   ,                       Pacemp                          ,          Pactrh     ,   Seguridad    )
													   VALUES( '$wbasedato',  '".date("Y-m-d")."' ,  '".date("H:i:s")."' , '".trim( odbc_result($err_o,1) )."', '".trim( odbc_result($err_o,3) )."', '".trim( odbc_result($err_o,2) )."',  ''  		, '".trim( odbc_result($err_o,5) )."', '".trim( odbc_result($err_o,6) )."', '".$pacNom1."', '".$pacNom2."',   '".trim( odbc_result($err_o,9) )."', '".trim( odbc_result($err_o,8) )."', '".$estCivil."', '".trim( odbc_result($err_o,12) )."', '".trim( odbc_result($err_o,13) )."', '".trim( odbc_result($err_o,10) )."',   ''  , '".trim( substr( odbc_result($err_o,10), 0, 2 ) )."', '$paisNac','".trim( odbc_result($err_o,14) )."', '".trim( odbc_result($err_o,15) )."', '".substr( trim( odbc_result($err_o,15) ), 0, 2 )."', '$paisRes', '".trim( odbc_result($err_o,16))."','".$tipoResidencia."', 'C-$wbasedato' );";

					//$resDem = mysql_query( $sql, $conex );

					//if( mysql_affected_rows() > 0 ){

						//Inserto datos de ingreso
						$sql = "INSERT INTO ".$wbasedato."_000101(    Medico   , 			Fecha_data, 			Hora_data,               Inghis               ,                 Ingnin              ,               Ingfei                ,               Ingtpa                ,               Ingcem                ,   Seguridad    )
														   VALUES( '$wbasedato',  '".date("Y-m-d")."' ,  '".date("H:i:s")."' , '".trim( odbc_result($err_o,1) )."', '".trim( odbc_result($err_o,19) )."', '".trim( odbc_result($err_o,18) )."', 				'E'					, '".trim( $responsable )."'			, 'C-$wbasedato' );";

						//$resIng = mysql_query( $sql, $conex );


						$sql = "INSERT INTO ".$wbasedato."_000205 (    Medico, 		Fecha_data,				 Hora_data,			 Reshis, 			Resing,		 Resnit, 		Resord, 	Resfir,				Resest, 	Restpa, 	Seguridad )
										VALUES (					'$wbasedato',  '".date("Y-m-d")."' ,  '".date("H:i:s")."', '".trim( odbc_result($err_o,1) )."',	'".trim( odbc_result($err_o,19) )."',	'".$responsable."',  '1',	'".$fecha_ingreso."',		'on',		'E', 	'C-cliame'  )";
						//$res = mysql_query( $sql, $conex );

						$qq = "INSERT INTO root_000036 (medico,		fecha_data,		hora_data		, 				Pacced				,				 Pactid				, 		Pacno1	, 		Pacno2	, 				Pacap1				, 				Pacap2				 , 					Pacnac				, 				Pacsex				, seguridad)
											VALUES ('root','".date("Y-m-d")."','".date("H:i:s")."'	,'".trim( odbc_result($err_o,2) )."','".trim( odbc_result($err_o,3) )."','".$pacNom1."'	,'".$pacNom2."'	,'".trim( odbc_result($err_o,5) )."', '".trim( odbc_result($err_o,6) )."',	'".trim( odbc_result($err_o,9) )."' , '".trim( odbc_result($err_o,8) )."','C-root')";
						//mysql_query($qq,$conex);
						echo $qq;
						$qq = "INSERT INTO root_000037 (medico,		fecha_data		,		hora_data	, 					Oriced			, 				Oritid				, 	Orihis	, 				Oriing				 , 		Oriori		, seguridad)
												VALUES ('root','".date("Y-m-d")."'	,'".date("H:i:s")."','".trim( odbc_result($err_o,2) )."','".trim( odbc_result($err_o,3) )."','".$whis."','".trim( odbc_result($err_o,19) )."','".$wemp_pmla."'	,'C-root')";
						//mysql_query($qq,$conex);
						echo "<br><br>".$qq;
					//}
					$hayDatos = true;

				}
			}

			return $hayDatos;
	}

	function ingresosInconsistentesUnixMatrix($ingresosMatrix, $ingresoUnix){

		foreach( $ingresoUnix as $unix ){
			foreach ( $ingresosMatrix as $matrix ){
					if ( $unix['ingreso'] == $matrix['ingreso'] ){
						if($unix['fecha'] == $matrix['fecha'] ){
							continue;
						}else{
							return true;
						}
					}
			}
		}
		return false;
	}

	function simularUnificacion($whis_origen, $whis_destino, $wingresos_origen, $trasladoParcial, $trasladoCompleto, $listoEnUnix  ){
		global $whistoriaOrigenConIngUnix,$tieneConexionUnix;

		$ingresosP = array();

		$ingresosP1 = buscarIngresosPaciente($whis_origen);
		$ingresosP2 = buscarIngresosPaciente($whis_destino);

		$juntarIngresos = false;
		$ingresosP1ori = $ingresosP1;

		//wingresos_origen tiene los ingresos de la historia origen que se desean unificar
		$ingresosP1aux = array();
		$wingresos_origen = explode(",", $wingresos_origen );

		//La unificacion no se ha realizado en UNIX,
		//Entonces hago la simulacion con los ingresos de matrix y unix (interseccion) de las historias origen y destino.
		if( $listoEnUnix == "false" ){
				//Busco ingresos de UNIX para la historia origen y destino
				$ingresos_unix = buscarIngresosPacienteUNIX( $whis_origen );
				$sonIncosistentes = ingresosInconsistentesUnixMatrix($ingresosP1, $ingresos_unix);
				if($sonIncosistentes==true){
					echo "<input type='hidden' class='inconsistencia' value='No se puede unificar, porque la fecha de los ingresos de la historia origen son distintos en unix y matrix' />";
				}
				$ingresosP1 = juntarIngresosMatrixUnix( $ingresosP1  , $ingresos_unix );
				$ingresosP1ori = $ingresosP1;
				$ingresosP1aux = array();
				foreach( $ingresosP1 as $pos=>$ingP1 ){
					if( in_array($ingP1['ingreso'], $wingresos_origen) ){
						array_push( $ingresosP1aux, $ingP1 );
					}
				}
				$ingresosP1 = $ingresosP1aux;
				$ingresos_unix = buscarIngresosPacienteUNIX($whis_destino);
				$sonIncosistentes = ingresosInconsistentesUnixMatrix($ingresosP2, $ingresos_unix);
				if($sonIncosistentes==true){
					echo "<input type='hidden' class='inconsistencia' value='No se puede unificar, porque la fecha de los ingresos de la historia destino son distintos en unix y matrix' />";
				}
				$ingresosP2 = juntarIngresosMatrixUnix( $ingresosP2  , $ingresos_unix );

			//agregando ambos arreglos a uno solo
			foreach ( $ingresosP1 as $val )
				array_push($ingresosP, $val);

			foreach ( $ingresosP2 as $val )
				array_push($ingresosP, $val);



			foreach($ingresosP as $key => $row){
				$volume[$key]  = $row['fecha'];
				$volume2[$key]  = $row['hora'];
			}
			array_multisort($volume, SORT_ASC, $volume2, SORT_ASC, $ingresosP);
			$empezarconing = 0;
			foreach( $ingresosP as &$ingx ){
				if( $empezarconing == 0) $empezarconing = $ingx['ingreso'];
				$ingx['ing_ajustado'] = $empezarconing;
				$empezarconing++;
			}
			//echo json_encode($ingresosP);
		}else{
			//SIN JUNTAR LOS INGRESOS DE UNIX DE LA HISTORIA ORIGEN
			foreach( $ingresosP1 as $pos=>$ingP1 ){
				if( in_array($ingP1['ingreso'], $wingresos_origen) ){
					array_push( $ingresosP1aux, $ingP1 );
				}
			}
			$ingresosP1 = $ingresosP1aux;

			//Si la historia origen NO TIENE INGRESOS EN UNIX, ya se hizo la unificacion ( en unix )
			//Entonces los ingresos de unix de la historia destino son el resultado de la unificacion
			//Con la suma de los ingresos de matrix, comparo con los ingresos de unix de la historia destino, solo por fecha y hora
			//La interseccion de esa comparacion son los ingresos que no tengo en matrix y los que tengo en cuenta para calcular la simulacion
			$ingresos_matrix = array();
			foreach ( $ingresosP1 as $val )
				array_push($ingresos_matrix, $val);

			foreach ( $ingresosP2 as $val )
				array_push($ingresos_matrix, $val);

			if( $tieneConexionUnix == "on" ){
				$ingresos_unix = buscarIngresosPacienteUNIX($whis_destino);

				//Comentado el 2013-07-02
				//$ingresosP = juntarIngresosMatrixUnixCasoDos( $ingresos_matrix, $ingresos_unix, $ingresosP1[0]['ingreso'], $ingresosP2[0]['ingreso'], $whis_origen, $whis_destino);
				$ingresosP = juntarIngresosMatrixUnixCasoDosCambio( $ingresos_matrix, $ingresos_unix );
				//echo "caso dos ".json_encode($ingresosP);
			}else{
				//Unificar los ingresos que se tienen en MATRIX
				$ingresosP = juntarIngresosSoloMatrix($ingresos_matrix);
			}
		}

		$texto_html = "<input type='hidden' id='ingresos_simulacion' value='".json_encode($ingresosP)."' />";
		$texto_html.= "<center><div>";
		$texto_html.= "<b>RESULTADO</b><br>";

		$texto_html.="<table align='center'>
						<tr>
							<td  class='encabezadotabla'></td>
							<td  colspan='2' class='encabezadotabla'>Documento</td>
							<td  colspan='2' class='encabezadotabla'>Nombre</td>

						</tr>
						<tr>
							<td  class='encabezadotabla'>Paciente</td>
							<td colspan='2' nowrap='nowrap' class='fila2' id='documento2_n' style='text-align:center'></td>
							<td colspan='2' nowrap='nowrap' class='fila2' id='primer_nombre2_n' style='text-align:center'></td>
						</tr>
						<tr>
							<td  class='encabezadotabla'>Historia</td>
							<td colspan='4' class='fila2' id='historia2_n' style='text-align:center'></td>
						</tr>
						<tr>
							<td colspan='5' >&nbsp;</td>
						</tr>
						<tr>
							<td colspan='5' align='center' class='fila1'><b>CAMBIOS</b></td>
						</tr>
						<tr class='encabezadotabla'>
							<th colspan='2'>Antes</th>
							<th colspan='2'>Despues</th>
							<th class='centrar' rowspan=2 nowrap='nowrap'>Fecha</th>
						</tr>
						<tr class='encabezadotabla'>
							<th nowrap='nowrap' class='centrar'>Historia</th>
							<th nowrap='nowrap' class='centrar'>Ingreso</th>
							<th nowrap='nowrap' class='centrar'>Historia</th>
							<th class='centrar' nowrap='nowrap'>Ingreso</th>
						</tr>";
		$color ="";
		$color2 ="";
		$his1 = "";
		foreach( $ingresosP as $ing ){

				if( $his1 == "" ){
					$his1 = $ing['historia'];
				}
				if( $his1 == $ing['historia'] ){
					$color = "fila1";
				}else{
					$color = "fila2";
				}
				$title = "";
				$color2 = "";
				if( isset( $ing['unix'] ) ){
					$color2 = " class= 'msg_tooltip' ";
					$title = "Ingreso desde Unix";
				}
				if( !isset($ing['ing_ajustado']) ){
					echo "<input type='hidden' class='inconsistencia' value='No se puede unificar, porque la fecha de los ingresos son distintos en unix y matrix' />";
				}

				$texto_html.="<tr  class='".$color."'>";
				$texto_html.="<td align='center' nowrap='nowrap'>".$ing['historia']."</td>";
				$texto_html.="<td align='center' title='".$title."' ".$color2." nowrap='nowrap'>".$ing['ingreso']."</td>";
				$texto_html.="<td align='center' title='".$title."' ".$color2." nowrap='nowrap'>".$whis_destino."</td>";
				$texto_html.="<td align='center' title='".$title."' ".$color2." nowrap='nowrap'>".$ing['ing_ajustado']."</td>";
				$texto_html.="<td align='center' title='".$title."' ".$color2." nowrap='nowrap'>".$ing['fecha']." ".$ing['hora']."</td>";
				$texto_html.="</tr>";
		}
		$texto_html.="</table>";
		$texto_html.= "</div></center>";

		echo $texto_html;
	}
	//FUNCION QUE CREA EL LOG, QUE GUARDA QUE HISOTORIA-INGRESO ES REEMPLAZADA POR CUAL HISTORIA-INGRESO
	function crearRegistroDeHistoriasUnificadas( $datos, $tipo_unificacion='0' ){

		global $conex;
		global $wemp_pmla;
		global $wmovhos;
		global $wclisur;
		global $user_session;

		global $log_errores;

		$basedatos = "";
		//Determino en que bd se guardara el registro
		// if ( empty ( $wmovhos ) )
		if ( $wemp_pmla!="01" )
			$basedatos = $wclisur;//clisur o soe
		else
			$basedatos = $wmovhos;

		//Consulto cual es el numero de la tabla de registros de historias unificadas segun la empresa
		$tablaRHU = consultarPrefijosBD($conex, $wemp_pmla, 'tablarhu');

		//Elijo el maximo "movimiento" de la tabla
		$query = " SELECT  MAX( Rhumov ) AS max FROM ".$basedatos."_".$tablaRHU;

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);

		$max_movimiento = 1;
		if($num>0)
		{
			$max_movimiento = mysql_fetch_array($res);
			$max_movimiento = (int) $max_movimiento['max'] + 1;
		}


		foreach ( $datos as $fila ){

			$insert = "INSERT INTO  ".$basedatos."_".$tablaRHU
								."	(Medico, "
								."	Fecha_data, "
								."	Hora_data, "
								."	Rhumov, "
								."	Rhuhia, "
								."	Rhuina, "
								."	Rhuhid, "
								."	Rhuind, "
								."	Rhuest, "
								."	Rhutip, "
								."	Seguridad) "
						."    VALUES "
								."	('".$basedatos."', "
								."	'".date("Y-m-d")."', "
								."	'".date("H:i:s")."', "
								."	'".$max_movimiento."', "
								."	'".$fila['historia']."', "
								."	'".$fila['ingreso']."',  "
								."	'".$fila['historia_destino']."',  "
								."	'".$fila['ing_ajustado']."', "
								."	'on', "
								."	'".$tipo_unificacion."', "
								."	'C-".$user_session."')";

			$res = mysql_query($insert,$conex) ; // or die($log_errores.= "<br>Creando registro de historias unificadas. Error No:".mysql_errno());
		}
	}

	//Retorna true si la $clave existe en el $arreglo a partir de una $posicion
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

	function buscarIngresosPacienteUNIX( $whis ){
		global $conexUnixPac;
		global $whistoriaOrigenConIngUnix, $tieneConexionUnix;
		$resultados = array();

		$query="SELECT 		egrnum, egring, egrhoi
				  FROM 		inmegr
				 WHERE 		egrhis='".trim($whis)."'";
		if( $tieneConexionUnix == "on" ){
			$err_o = odbc_do($conexUnixPac,$query) or die( odbc_error()." $query - ".odbc_errormsg() );
			while (odbc_fetch_row($err_o))
			{
				$dato['fecha'] = odbc_result($err_o, 'egring');
				$dato['hora'] = odbc_result($err_o, 'egrhoi');
				$dato['hora'] = str_replace(".", ":", $dato['hora']);
				$dato['hora'] = date('H:i', strtotime( $dato['hora'] ));
				$dato['ingreso'] = odbc_result($err_o, 'egrnum');
				$dato['historia'] = $whis;
				$dato['unix'] = '1';
				array_push( $resultados, $dato );
			}
		}
		return $resultados;
	}

	function buscarIngresosPaciente( $whis, $whis2='' ){
		global $conex;
		global $wmovhos;
		global $wclisur;

		$query_ingresos = "";

		if( $whis2=='' ){
			$where = "	 WHERE  inghis='".$whis."'";
		}else{
			$where = "	 WHERE  ( inghis='".$whis."' OR inghis='".$whis2."' ) ";
		}

		if( ! empty( $wmovhos ) ){
			$query_ingresos = "SELECT 	Fecha_data as fecha, Hora_data as hora, Inghis as historia, Inging ingreso 	"
							."	 FROM 	".$wmovhos."_000016 "
							.$where
							."  ORDER BY  fecha, hora, historia, ingreso";
		}else{
			$query_ingresos = "SELECT 	ingfei as fecha, inghin as hora, Inghis as historia, Ingnin as ingreso	"
							."	 FROM 	".$wclisur."_000101 "
							.$where
							."  ORDER BY  fecha, hora, historia, ingreso";
		}

		if( !empty( $wmovhos ) && !empty( $wclisur ) ){
			$query_ingresos = "
							SELECT * FROM (
							   SELECT 	ingfei as fecha, inghin as hora, Inghis as historia, Ingnin as ingreso	"
							."	 FROM 	".$wclisur."_000101 "
							.$where.
							" UNION ".
							"  SELECT 	Fecha_data as fecha, Hora_data as hora, Inghis as historia, Inging ingreso 	"
							."	 FROM 	".$wmovhos."_000016 "
							.$where
							."  ) as bbb
							GROUP BY fecha, historia, ingreso
							ORDER BY  fecha, hora, historia, ingreso";

		}

		$res_ingresos = mysql_query($query_ingresos, $conex);
		$num = mysql_num_rows($res_ingresos);

		$wresultado_ingresos = array();
		if ($num > 0){
			while($datatmp = mysql_fetch_assoc($res_ingresos)) {
				$datatmp['hora'] = substr_replace($datatmp['hora'],"",-3);
				array_push($wresultado_ingresos, $datatmp);
			}
		}
		return $wresultado_ingresos;
	}

	function juntarIngresosMatrixUnix( $ing_matrix, $ing_unix ){
		$resultados_interseccion = array();

			//Se agregan todos los ingresos de matrix
			foreach( $ing_matrix as $matrix ){
				array_push ($resultados_interseccion, $matrix);
			}

			//Agregando los ingresos de UNIX que no esten en matrix, comparando por fecha e ingreso
			foreach( $ing_unix as $unix ){
				$encontro = false;
				foreach ( $ing_matrix as $matrix ){
						//if ( $unix['fecha'] == $matrix['fecha'] && $unix['hora'] == $matrix['hora'] && $unix['ingreso'] == $matrix['ingreso'] )//2013-02-27
						if ( $unix['fecha'] == $matrix['fecha'] &&  $unix['ingreso'] == $matrix['ingreso'] )
							$encontro = true;
				}
				if( $encontro == false ){
					array_push ($resultados_interseccion, $unix);
				}
			}
			//Ordenando los ingresos por fecha y hora
			foreach ($resultados_interseccion as $key => $row) {
				$volume[$key]  = $row['fecha'];
				$volume2[$key]  = $row['hora'];
			}
			array_multisort($volume, SORT_ASC, $volume2, SORT_ASC, $resultados_interseccion);

		return $resultados_interseccion;
	}

	//Creada el 2013-07-02
	function juntarIngresosMatrixUnixCasoDosCambio( $ing_matrix, $ing_unix ){
			$resultados_interseccion = array();

			//2014-08-28
			$arr_ingresos_ajustados = array();
			//Se agregan todos los ingresos de matrix
			//Los ingresos de UNIX, cuya fecha sea igual al ingreso de MATRIX, el "ingreso ajustado" queda con el  numero de ingreso de MATRIX
			foreach( $ing_matrix as &$matrix1 ){
				foreach( $ing_unix as $unix1 ){
					if ( $unix1['fecha'] == $matrix1['fecha']  ){
						//2014-08-28 el ingreso ajustado ya existe? si: entonces avanzar mas en el arreglo hasta que coincida esa misma fecha
						//Porque en los casos en que un paciente ingresa varias veces al dia, se produce un error
						if( in_array( $unix1['ingreso'], $arr_ingresos_ajustados ) == false ){
							$matrix1['ing_ajustado'] = $unix1['ingreso'];
							array_push( $arr_ingresos_ajustados, $unix1['ingreso'] );
							break;
						}
					}
				}
				array_push ($resultados_interseccion, $matrix1);
			}

			//Las fechas de los ingresos de MATRIX deben estar en los ingresos de UNIX
			//Si existe una fecha de MATRIX	que no sea igual a un ingreso de UNIX, hay un error y no se debe dejar unificar
			$inconsistencia = false;
			$fecha_no_unix = "";
			foreach ($ing_matrix as $row) {
				$cantidad_de_existencia = 0;
				$fecha_no_unix = $row['fecha'];
				foreach ($ing_unix as $row2) {
					if( $row['fecha'] == $row2['fecha'] )
						$cantidad_de_existencia++;
				}
				if( $cantidad_de_existencia == 0 ){
					$inconsistencia = true;
					break;
				}
			}

			if( $inconsistencia == true ){
				echo "<input type='hidden' class='inconsistencia' value='No se puede unificar, porque la fechas de los ingresos son distintos en unix y matrix ".$fecha_no_unix."' />";
			}

			//Agregando los ingresos de UNIX que no esten en matrix, comparando por fecha
			foreach( $ing_unix as $unix ){
				$encontro = false;
				foreach ( $ing_matrix as $matrix ){
					if ( $unix['fecha'] == $matrix['fecha']  ){
						$encontro = true;
					}
				}
				if( $encontro == false ){
					$unix['ing_ajustado'] = $unix['ingreso'];
					array_push ($resultados_interseccion, $unix);
				}
			}

			if( $inconsistencia == false && ( count( $resultados_interseccion ) != count( $ing_unix ) ) ){
				echo "<input type='hidden' class='inconsistencia' value='No se puede unificar, el resultado de la unificacion no concuerda con UNIX' />";
			}

			//Ordenando los ingresos por fecha y hora
			foreach ($resultados_interseccion as $key => $row) {
				$volume[$key]  = $row['fecha'];
				$volume2[$key]  = $row['hora'];
			}
			array_multisort($volume, SORT_ASC, $volume2, SORT_ASC, $resultados_interseccion);

		return $resultados_interseccion;
	}

	function buscarPaciente( $whis ){
		global $conex;
		global $wemp_pmla;
		global $wclisur;

		$datos = array('primer_nombre'=>null, 'segundo_nombre'=>null, 'primer_apellido'=>null,
					   'segundo_apellido'=>null,'doc'=>null,'tipodoc'=>null, 'historia'=>$whis, 'ingresos'=>array());


		$query_info = "
			SELECT  datos_paciente.Pactid ,pacientes_id.Oriced,"
				."  datos_paciente.Pacno1, datos_paciente.Pacno2,"
				."  datos_paciente.Pacap1, datos_paciente.Pacap2"
		  ."  FROM  root_000037 as pacientes_id, root_000036 as datos_paciente"
		  ." WHERE  pacientes_id.Orihis = '".$whis."'"
		  ."   AND  pacientes_id.Oriori =  '".$wemp_pmla."'"
		  ."   AND 	pacientes_id.Oriced = datos_paciente.Pacced"
		  ."   AND 	Oritid = Pactid";

		$ingresos = buscarIngresosPaciente( $whis );

		$res_info = mysql_query($query_info, $conex);
		$num = mysql_num_rows($res_info);
		if( $num == 0 ){
			if( !empty( $wclisur ) ){
				//Buscarlo en la 100 y en caso de existir, grabarlo en root 36 y 37
				$q = " SELECT  Pactdo as tdo , Pacdoc as ced, Pacno1 as no1, Pacno2 as no2, Pacap1 as ap1, Pacap2 as ap2,
							   Pacfna as fna, Pacsex as sex, Ingnin as ing
						 FROM  ".$wclisur."_000100, ".$wclisur."_000101
						WHERE  Pachis = '".$whis."'
						  AND  Pachis = Inghis
					 ORDER BY  Ingnin*1 DESC
					  LIMIT 1";
				$res = mysql_query($q, $conex);
				$num1 = mysql_num_rows($res);
				if( $num1 > 0 ){
					$row = mysql_fetch_array($res);
					$qq = "INSERT INTO root_000036 (medico,fecha_data,hora_data, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, seguridad)
											VALUES ('root','".date("Y-m-d")."','".date("H:i:s")."','".$row['ced']."','".$row['tdo']."','".$row['no1']."','".$row['no2']."','".$row['ap1']."','".$row['ap2']."','".$row['fna']."','".$row['sex']."','C-root')";
					mysql_query($qq,$conex);
					$qq = "INSERT INTO root_000037 (medico,fecha_data,hora_data, Oriced, Oritid, Orihis, Oriing, Oriori, seguridad)
											VALUES ('root','".date("Y-m-d")."','".date("H:i:s")."','".$row['ced']."','".$row['tdo']."','".$whis."','".$row['ing']."','".$wemp_pmla."','C-root')";
					mysql_query($qq,$conex);

					$datos = array('primer_nombre'=>$row['no1'], 'segundo_nombre'=>$row['no2'], 'primer_apellido'=>$row['ap1'],
					   'segundo_apellido'=>$row['ap2'],'doc'=>$row['ced'],'tipodoc'=>$row['tdo'], 'historia'=>$whis, 'ingresos'=>$ingresos);

				}
			}
		}else{
			$row_datos = mysql_fetch_array($res_info);
			$datos = array('primer_nombre'=>$row_datos['Pacno1'], 'segundo_nombre'=>$row_datos['Pacno2'], 'primer_apellido'=>$row_datos['Pacap1'],
					   'segundo_apellido'=>$row_datos['Pacap2'],'doc'=>$row_datos['Oriced'],'tipodoc'=>$row_datos['Pactid'], 'historia'=>$whis, 'ingresos'=>$ingresos);
		}

		return $datos;
	}

	//creada el 2014-10-21
	function juntarIngresosSoloMatrix( $ing_matrix ){
		$ingresosP = $ing_matrix;
		$menorIngreso = 15000;
		//Se busca el numero de ingreso menor
		foreach( $ing_matrix as $matrix ){
			if( intval($matrix['ingreso']) < $menorIngreso ){
				$menorIngreso = $matrix['ingreso'];
			}
		}

		foreach($ingresosP as $key => $row){
			$volume[$key]  = $row['fecha'];
			$volume2[$key]  = $row['hora'];
		}
		array_multisort($volume, SORT_ASC, $volume2, SORT_ASC, $ingresosP);

		foreach( $ingresosP as &$ingx ){
			$ingx['ing_ajustado'] = $menorIngreso;
			$menorIngreso++;
		}
		return $ingresosP;
	}

	//FUNCION QUE SE LLAMA CUANDO LA PAGINA CARGA Y MUESTRA LOS PARAMETROS DE CONSULTA
	function vistaInicial(){

		global $wemp_pmla;
		global $wccosSU;
		global $wactualiz;
		global $wclisur;
		global $cambioDocumento;
		global $historia1;
		global $nuevoTd;
		global $nuevoDocumento;
		global $wcliame;
		//Se imprimen variables ocultas
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		echo "<input type='hidden' id ='cambioDocumento' value='".$cambioDocumento."'/>";
		echo "<input type='hidden' id ='nuevoTd' value='".$nuevoTd."'/>";
		echo "<input type='hidden' id ='nuevoDocumento' value='".$nuevoDocumento."'/>";

		echo "<center>";
		//Determino el logo del programa
		$logo = "";
		// if (empty( $wclisur ))
		if ( $wcliame != "")
			$logo = "clinica";
		else
			$logo = "logo_".$wclisur;

		encabezado("UNIFICAR HISTORIAS",$wactualiz, $logo);

		if(!isset($_SESSION['user'])){
			echo "Error";
			return;
		}
		echo '<span class="subtituloPagina2">Digite las historias que desea unificar</span>';
		echo '<br><br><br>';
		echo "<div id='historias'>";

		echo "<table align='center' border=0>";
		echo "<tbody>";
		echo "<tr>";
		echo "<td style='width: 260px;' class='fila1'>Historia origen</td>";
		echo "<td class='fila2' align='center'>";
		echo "<input type='text' id='historia1' class='solonumeros' value='{$historia1}'></input>";
		echo "</td>";
		echo "<td style='width: 15px;'></td>";
		echo "<td style='width: 260px;' class='fila1'>Historia destino</td>";
		echo "<td class='fila2' align='center'>";
		echo "<input type='text' id='historia2' class='solonumeros'></input>";
		echo "</td>";
		echo "</tr>";
		echo "<tr><td colspan='2'> <br> </td> <td style='width: 15px;'> <br> </td>  <td colspan='2'> <br> </td> </tr>";

		echo "<tr>";

		echo "<td colspan='2'>";
		echo "	<div id='formato_informacion_paciente1' class='bloque_informacion' style='float:left; width:100%'>
					<table width=100% style='float:left'>
						<tr>
							<td  colspan=3 class='titulo_grande'>ORIGEN</td>
						</tr>
						<tr>
							<td  class='encabezadotabla'></td>
							<td  class='encabezadotabla'>Documento<button onclick='cambiarDocumento(\"documento1\",\"historia1\")' class='editbuton'></button></td>
							<td   class='encabezadotabla'>Nombre</td>
						</tr>
						<tr>
							<td  class='encabezadotabla'>Paciente</td>
							<td class='fila2' id='documento1' ></td>
							<td  class='fila2' id='primer_nombre1'></td>
						</tr>
						<tr>
							<td  class='encabezadotabla'>Historia</td>
							<td  colspan=2 style='text-align:center' class='fila2' id='tit_historia1' ></td>
						</tr>
					</table>
				</div>";
		echo "</td>";
		echo "<td style='width: 15px;'></td> ";
		echo "<td colspan='2'>";
		echo "	<div id='formato_informacion_paciente2' class='bloque_informacion' style='float:left; width:100%'>
					<table width=100% style='float:left' >
						<tr>
							<td colspan=3 class='titulo_grande'>DESTINO</td>
						</tr>
						<tr>
							<td  class='encabezadotabla'></td>
							<td  class='encabezadotabla'>Documento<button onclick='cambiarDocumento(\"documento2\",\"historia2\")' class='editbuton'></button></td>
							<td  class='encabezadotabla'>Nombre</td>
						</tr>
						<tr>
							<td  class='encabezadotabla'>Paciente</td>
							<td  class='fila2' id='documento2' ></td>
							<td  class='fila2' id='primer_nombre2'></td>
						</tr>
						<tr>
							<td  class='encabezadotabla'>Historia</td>
							<td  colspan=2 style='text-align:center' class='fila2' id='tit_historia2' ></td>
						</tr>
					</table>
				</div>";
		echo "</td>";
		echo "</tr>";


		echo "<tr>";
		echo "<td colspan='2'><table class='ocultar1' style='margin-top:0' width=100% id='ingresos_fechas1'></table></td>";
		echo "<td style='width: 15px;'></td> ";
		echo "<td colspan='2' ><table class='ocultar2' style='margin-top:0' width=100% id='ingresos_fechas2'></table></td>";
		echo "</tr>";

		echo "</tbody>";
		echo "</table>";

		echo "</center>";

		echo "<br>";
		echo "<br>";
		echo "<center>";
		echo '<input type="button" class="botona" id="consultar" value="Consultar"></input>';
		echo '<input type="button" class="botona" id="simular" value="Simular" style="display:none;"></input>';
		echo '</div>';
		echo '<br><br>';
		echo '<div id="resultados"></div>';
		echo '</center>';

		echo '<center>';
		echo '<br><br>';
		echo '<table>';
		echo '<tr>';
		echo "<td align='center'>";
		echo '<div id="respuestaAjax" style="margin-bottom:30"></div>';
		echo '<input type="button" class="botona" id="unificar" value="Unificar" style="display:none;"></input>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		echo '<tr>';
		echo "<td align='center'>";
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		echo '<tr>';
		echo "<td align='center'>";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana'>";
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</center>';
		echo '<br><br>';

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

		$selectdocs = "<select type='text' id='tipdoc'>";
		$tiposDocumento = consultarTiposDocumento();
		$selectdocs.= "<option value='' selected>--</option>";
		foreach ($tiposDocumento as $tipo){
			$selectdocs .= "<option value='$tipo->codigo'>$tipo->descripcion</option>";
		}
		$selectdocs .= "</select>";

		//Formulario para cambiar documento
		echo "<div id='form_cambiar_documento' align='center' style='display:none;'>
				<table>
				<tr class='encabezadotabla'><td colspan=2 align='center'><font size=4>Cambiar Documento</font></td></tr>
				<tr class='encabezadotabla'><td colspan=2 align='center'><div id='documento_antes'>&nbsp;</div></td></tr>
				<tr><td class='fila1'>Tipo</td><td class='fila2'>".$selectdocs."</td></tr>
				<tr><td class='fila1'>Documento</td><td class='fila2'><input type='text' id='documento_nuevo' value='' class='solonumerosyletras'/><br></td></tr>
				<tr class='fila2'><td colspan=2 align='center'><input type='button' value='Cambiar' onclick='cambiarDocPac()' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='Cancelar' onclick='$.unblockUI();'  /></td></tr>
				</table>
				<input type='hidden' id='tipoantes' />
				<input type='hidden' id='docantes' />
				<input type='hidden' id='iddoccambiando' />
				<input type='hidden' id='historiacambiando' />
			</div>";
	}
	//FIN***********************************************************************************************************//
?>

		<style type="text/css">
				.msg_tooltip,.msg_tooltip2{
						 background-color: #F3B7B7;
						 color: #000000;
						 font-size: 10pt;
				}
				.enlinea{
					display: -moz-inline-stack; /* FF2*/
					display: inline-block;
					vertical-align: top; /* BASELINE CORRECCIÓN*/
					zoom: 1; /* IE7 (hasLayout)*/
					*display: inline; /* IE */
				}
				.titulo_grande{
					font-size:16px;
					font-weight:bold;
					text-align: center;
					width: 100%;
				}
				.botona{
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
				input:disabled {
					background: #fff;
					color: black;
				}
			    a img{
					border:0;
				}
				input{
					outline: none;
					-moz-border-radius: 5px;
					-webkit-border-radius: 5px;
				    border-radius: 4px;

				}
				input:focus{
					outline: none;
					-moz-border-radius: 5px;
					-webkit-border-radius: 5px;
				    border-radius: 5px;

				    box-shadow: 0 0 32px #C3D9FF;
					-webkit-box-shadow: 0 0 32px #C3D9FF;
					-moz-box-shadow: 0 0 32px #C3D9FF;
				}
				#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
				#tooltip h3, #tooltip div{margin:0; width:auto}
		</style>

		<script>
			var trasladoParcial = false;
			var trasladoCompleto = false;
			var listoEnUnix = false;
			//************cuando la pagina este lista...**********//
			$(document).ready(function() {
				//Cuando cargue completamente la pagina

				//agregar eventos a campos de la pagina
				$("#consultar").click(function() {
					realizarConsulta();
				});
				$("#cerrar_ventana").click(function() {
					cerrarVentana();
				});
				$("#enlace_retornar").click(function() {
					restablecer_pagina();
				});
				$("#unificar").click(function() {
					realizarUnificacion();
				});
				$("#simular").click(function() {
					simularUnificacion();
				});

				//esconde los elementos
				$("#enlace_retornar").hide();
				$("#formato_informacion_paciente1").hide();
				$("#formato_informacion_paciente2").hide();
				$('.ocultar1').hide();
				$('.ocultar2').hide();

				$("#historia1").change(function() {
					ocultar_info_paciente(1);
				});
				$("#historia2").change(function() {
					ocultar_info_paciente(2);
				});

				$(".editbuton").button({
				  icons: { primary: "ui-icon-pencil" },
				  text: false
				});

				//Los objetos con la clase solonumeros solo permiten digitar numeros
				$(".solonumeros").keyup(function(){
					if ($(this).val() !="")
						$(this).val($(this).val().replace(/[^0-9]/g, ""));
				});

				$(".solonumerosyletras").keyup(function(){
					if ($(this).val() !="")
						$(this).val($(this).val().replace(/[^a-zA-Z0-9]/g, ""));
				});

				if( $("#cambioDocumento").val() == "on" ){

					$("#consultar").click();

				}

			});

			//*******variables globales de javascript**********//
			var historiaMostrada1 = "";
			var historiaMostrada2 = "";
			var consultando1 = false;
			var consultando2 = false;
			var ingresosDeUnix = false;

			//Ocultar toda la informacion correspondiente al paciente origen(1) o destino(2)
			function ocultar_info_paciente ( indi ){
				$("#formato_informacion_paciente"+indi).slideUp();
				$('#documento'+indi).val( "" );
				$('#primer_nombre'+indi).val( "" );
				$('.ocultar'+indi).hide( );
				$("#simular").hide();
				$("#respuestaAjax").html('');
				$("#unificar").hide();
				if( indi == 1)
					historiaMostrada1 = "";
				else if( indi == 2)
					historiaMostrada2 = "";
			}

			//Muestra la vista inicial
			function restablecer_pagina(){
				$("#enlace_retornar").hide();
				$("#formato_informacion_paciente1").slideUp('slow');
				$("#formato_informacion_paciente2").slideUp('slow');
				$("#consultar").slideDown('slow');
				historiaMostrada1 = "";
				historiaMostrada2 = "";
				consultando1 = false;
			    consultando2 = false;
				ingresosDeUnix = false;
				trasladoParcial = false;
				trasladoCompleto = false;
				listoEnUnix = false;
				$("#historia1").val("");
				$("#historia2").val("");
				$('#documento1').val( "" );
				$('#primer_nombre1').val( "" );
				$('#segundo_nombre1').val( "" );
				$('#primer_apellido1').val( "" );
				$('#segundo_apellido1').val( "" );
				$('#documento2').val( "");
				$('#primer_nombre2').val( "");
				$('#segundo_nombre2').val( "");
				$('#primer_apellido2').val( "" );
				$('.ocultar1').slideUp();
				$('.ocultar2').slideUp();
				$('#segundo_apellido2').val( "" );
				$("#respuestaAjax").html('');
				$("#unificar").slideUp();
			}

			function realizarConsulta(){

				var historia1 = $("#historia1").val();
				var historia2 = $("#historia2").val();
				var wemp_pmla = $("#wemp_pmla").val();
				$("#respuestaAjax").html('');
				historia1 = $.trim( historia1 );
				historia2 = $.trim( historia2 );
				consultando1 = false;
				consultando2 = false;

				if( historia1 == "" && historia2 == ""){
					alerta("Ingrese las historias que desea unificar");
					return;
				}
				if( historia1 != "" ){
					if( $.isNumeric( historia1 ) == false ){
						alerta("El codigo de las historias debe ser numerico");
						return;
					}
				}
				if( historia2 != "" ){
					if( $.isNumeric( historia2 ) == false ){
						alerta("El codigo de las historias debe ser numerico");
						return;
					}
				}
				/*if( historia1 == historia2 ){
					alerta("Las historias no pueden iguales!");
					return;
				}*/
				if ( historiaMostrada1 == historia1 && historiaMostrada2 == historia2){
					return;
				}

				consultando1 = true;
				consultando2 = true;

				/*if ( historiaMostrada1 == historia1 ){
					historia1 = "";
					consultando1 = false;
				}
				if ( historiaMostrada2 == historia2 ){
					historia2 = "";
					consultando2 = false;
				}*/

				//TODO CORRECTO

				//muestra el mensaje de cargando
				$.blockUI({ message: $('#msjEspere') });

				$("#enlace_retornar").fadeIn('slow');

				//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

				//Realiza el llamado ajax con los parametros de busqueda
				$.post('unificarHistorias.php', { wemp_pmla: wemp_pmla, action: "consultar", historia_des: historia2, historia_ori: historia1, origenTieneUnix: ingresosDeUnix, consultaAjax: aleatorio } ,
					function(data) {
						//oculta el mensaje de cargando
						$.unblockUI();
						if( isJsonString( data )){ //respuesta esperada
							mostrarDatos( jQuery.parseJSON(data) );
						}else{
							alerta("Error \n"+data);
						}
					});
			}

			function simularUnificacion(){

				if( historiaMostrada2 == historiaMostrada1 ){
					confirma = confirm("Desea simular el reemplazo del ultimo ingreso por uno anterior?");
					if( confirma )
						construirSelectIngresos();
					return;
				}
				var ingresos_origen = new Array();
				$(".checkorigen:checked").each( function(){
					ingresos_origen.push( $(this).val() );
				});
				if( ingresos_origen.length < 1 ){
					alert("Debe elegir al menos un ingreso de la historia origen");
					return;
				}
				ingresos_origen = ingresos_origen.toString();
				var wemp_pmla = $("#wemp_pmla").val();
				$.blockUI({ message: $('#msjEspere') });
				$("#consultar").slideUp('slow');
				$.post('unificarHistorias.php', { wemp_pmla: wemp_pmla, action: "simular", historia_des: historiaMostrada2, historia_ori: historiaMostrada1,
												ingresos_origen: ingresos_origen, trasladoParcial: trasladoParcial,
												trasladoCompleto: trasladoCompleto, listoEnUnix : listoEnUnix, origenTieneUnix: ingresosDeUnix, consultaAjax: '' } ,
					function(data) {
						$.unblockUI();
						$("#respuestaAjax").html(data);
						$("#primer_nombre2_n").text( $('#primer_nombre2').text() );
						$("#documento2_n").text( $('#documento2').text() );
						$("#historia2_n").html( "<b>"+historiaMostrada2+"</b>");

						$("#simular").slideUp('slow');
						$("#unificar").show();
						$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });

						if( $(".inconsistencia").length > 0 ){
							$("#unificar").hide();
							alert( $(".inconsistencia").eq(0).val()  );
						}
					});
			}

			function construirSelectIngresos(){
				htmltext = $("#ingresos_fechas1").html();
				htmltext = "<table class='enlinea' id='unificar_ingresos'>"+htmltext+"</table>";
				$("#respuestaAjax").html( htmltext );

				ultimo_tr = $("#unificar_ingresos tr:last").html();
				$("#unificar_ingresos tr:last").remove();
				$("#unificar_ingresos .ing").each(function(){
					$(this).html( "<input type=radio name=rrr>"+$(this).html() );
				});

				htmltext1 = "<table class='enlinea' style='margin-right: 20px;'><tr class='encabezadotabla'><td>Ingreso</td><td>Fecha</td><td>Hora</td></tr><tr class='fila1'>"+ultimo_tr+"</tr></table>";
				htmltext = "<div id='div_unificar_ingresos'>"+htmltext1+$("#respuestaAjax").html()+"</div>";
				$("#respuestaAjax").html( htmltext );

				$("#div_unificar_ingresos").before("<span class='subtituloPagina1'>Seleccione el ingreso por el que desea unificar</span>");
				$("#simular").slideUp('slow');
				$("#unificar").slideUp('slow');
				$("#consultar").slideUp('slow');

				$("#unificar_ingresos input:radio").click(function(){
					simularUnificarUltimoIngreso( $(this).parent().text() );
				});
			}

			function simularUnificarUltimoIngreso( destino ){
				ultimo_ingreso = $("#ingresos_fechas1 tr:last").find('td:first').text();
				confirmar = confirm('Recuerde que esta unificacion no es posible restaurarla \n Desea unificar el ingreso '+ultimo_ingreso+' por el ingreso '+destino+' ?');
				if(!confirmar)
					return;
				var wemp_pmla = $("#wemp_pmla").val();
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
				$.blockUI({ message: $('#msjEspere') });
				$("#consultar").slideUp('slow');
				$.post('unificarHistorias.php', { wemp_pmla: wemp_pmla, action: "unificarUltimoIngreso", historia: historiaMostrada1, ingreso_origen: ultimo_ingreso, ingreso_destino: destino, consultaAjax: aleatorio } ,
					function(data) {
						alerta("Proceso de unificacion finalizado");
						restablecer_pagina();
						$("#respuestaAjax").html(data);
					});
			}

			function realizarUnificacion(){

				var confirmar = confirm("¿ Realmente desea unificar las historias "+historiaMostrada1+" y "+historiaMostrada2+" ?");

				if( !confirmar )
					return;
				var wemp_pmla = $("#wemp_pmla").val();
				var rango_superior = 245;
				var rango_inferior = 11;
				var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
				var ingresos_simulacion = $("#ingresos_simulacion").val();
				$.blockUI({ message: $('#msjEspere') });
				$("#consultar").slideUp('slow');
				$.post('unificarHistorias.php', { wemp_pmla: wemp_pmla, action: "unificar", historia_des: historiaMostrada2, historia_ori: historiaMostrada1, ing_simulacion: ingresos_simulacion, consultaAjax: aleatorio } ,
					function(data) {
						alerta("Proceso de unificacion finalizado");
						restablecer_pagina();
						$("#respuestaAjax").html(data);
					});
			}

			function mostrarDatos( jsonDatos ){
				jsonPaciente = jsonDatos.pacientes;

				var historia1 = $("#historia1").val();
				var historia2 = $("#historia2").val();

				var ingresos_unix_matrix_diferentes = false;
				trasladoParcial = jsonDatos.parcial;
				trasladoCompleto = jsonDatos.completo;
				listoEnUnix = jsonDatos.listounix;

				for(var i=0; i < jsonPaciente.length; i++){
					if( jsonPaciente[i].tipodoc != null ){
						if( jsonPaciente[i].historia == historia1 && consultando1==true && i==0 ){
							$('.ocultar1').show();
							ingresosDeUnix = false;
							historiaMostrada1 = historia1;
							$("#formato_informacion_paciente1").slideDown();
							$('#documento1').text( jsonPaciente[i].tipodoc  + " " + jsonPaciente[i].doc );
							$('#primer_nombre1').text( jsonPaciente[i].primer_nombre+" "+jsonPaciente[i].segundo_nombre+" "+jsonPaciente[i].primer_apellido+" "+jsonPaciente[i].segundo_apellido );
							var tit_h1 = jsonPaciente[i].historia;
							$("#tit_historia1").html(tit_h1);
							var textohtml = "<tr class='encabezadotabla'><td>Sel</td><td>Ingreso</td><td>Fecha</td><td>Hora</td></tr>";
							var color = "";
							for(var j=0;j<jsonPaciente[i].ingresos.length;j++){
								if( j%2 == 0)
									color="fila1";
								else
									color="fila2";

								title = "";
								if( jsonPaciente[i].ingresos[j].unix != undefined ){
									color= 'msg_tooltip2';
									title = "Ingreso desde Unix";
									ingresosDeUnix = true;
								}

								textohtml+= "<tr align=center title='"+title+"' class='"+color+"'>";
								textohtml+= "<td><input type='checkbox' class='checkorigen' value='"+jsonPaciente[i].ingresos[j].ingreso+"' checked /></td>";
								textohtml+= "<td class='ing'>";
								textohtml+= jsonPaciente[i].ingresos[j].ingreso;
								textohtml+= "</td>";
								textohtml+= "<td>";
								textohtml+= jsonPaciente[i].ingresos[j].fecha;
								textohtml+= "</td><td>";
								textohtml+= jsonPaciente[i].ingresos[j].hora;
								textohtml+= "</td> </tr>";

								var cantidad=0;
								for(var k=0;k<jsonPaciente[i].ingresos.length;k++){
									if( jsonPaciente[i].ingresos[j].ingreso == jsonPaciente[i].ingresos[k].ingreso ){
										cantidad++;
									}
								}
								if( cantidad > 1 && ingresos_unix_matrix_diferentes==false){
									ingresos_unix_matrix_diferentes = true;
									alert('El ingreso '+jsonPaciente[i].ingresos[j].ingreso+" de la historia "+historia1+" \n Tiene fecha distinta en Unix y Matrix");
									$("#simular").hide();
								}
							}
							$("#ingresos_fechas1").html( textohtml );

						}else if(consultando2==true){
							$('.ocultar2').show();
							historiaMostrada2 = historia2;
							$("#formato_informacion_paciente2").slideDown();
							$('#documento2').text(  jsonPaciente[i].tipodoc + " " + jsonPaciente[i].doc );
							$('#primer_nombre2').text( jsonPaciente[i].primer_nombre+" "+jsonPaciente[i].segundo_nombre+" "+jsonPaciente[i].primer_apellido+" "+jsonPaciente[i].segundo_apellido );
							var tit_h2 = jsonPaciente[i].historia;
							$("#tit_historia2").html(tit_h2);
							var textohtml = "<tr class='encabezadotabla'><td>Ingreso</td><td>Fecha</td><td>Hora</td></tr>";
							var color = "";
							for(var j=0;j<jsonPaciente[i].ingresos.length;j++){
								if( j%2 == 0){
									color="fila1";
								}else{
									color="fila2";
								}

								title = "";
								if( jsonPaciente[i].ingresos[j].unix != undefined ){
									color= 'msg_tooltip2';
									title = "Ingreso desde Unix";
									//ingresosDeUnix = true;
								}

								textohtml+= "<tr align=center title='"+title+"' class='"+color+"'>";
								textohtml+= "<td>";
								textohtml+= jsonPaciente[i].ingresos[j].ingreso;
								textohtml+= "</td>";
								textohtml+= "<td>";
								textohtml+= jsonPaciente[i].ingresos[j].fecha;
								textohtml+= "</td> <td>";
								textohtml+= jsonPaciente[i].ingresos[j].hora;
								textohtml+= "</td> </tr>";

								var cantidad=0;
								for(var k=0;k<jsonPaciente[i].ingresos.length;k++){
									if( jsonPaciente[i].ingresos[j].ingreso == jsonPaciente[i].ingresos[k].ingreso ){
										cantidad++;
									}
								}
								if( cantidad > 1 && ingresos_unix_matrix_diferentes==false && trasladoParcial == false){
									ingresos_unix_matrix_diferentes = true;
									alert('El ingreso '+jsonPaciente[i].ingresos[j].ingreso+" de la historia "+historia2+" \n Tiene fecha distinta en Unix y Matrix");
									$("#simular").hide();
								}
							}
							$("#ingresos_fechas2").html( textohtml );
						}
					}else{
						var indi;
						if(consultando1 == true && consultando2 == true ){
							indi = i+1;
						}else if( consultando1 == true ){
							indi = 1;
						}else{
							indi = 2;
						}
						ocultar_info_paciente( indi );
						if( indi == 1 )
							historiaMostrada1 = "";
						else
							historiaMostrada2 = "";
						alerta("No se encontraron datos de la historia "+jsonPaciente[i].historia);
					}
				}
				/*if( jsonPaciente.length == 1){
					if( jsonPaciente[0].historia == historiaMostrada1 &&  jsonPaciente[0].historia == historiaMostrada2){
						ocultar_info_paciente(1);
						ocultar_info_paciente(2);
					}
				}*/
				if( historiaMostrada1 != "" && historiaMostrada2 != "" && ingresos_unix_matrix_diferentes==false){
					$("#simular").slideDown('slow');
				}

				$(".msg_tooltip2").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			}

			function alerta( txt ){
				$("#textoAlerta").text( txt );
				$.blockUI({ message: $('#msjAlerta') });
					setTimeout( function(){
									$.unblockUI();
								}, 3000 );
			}

			function isJsonString( str ) {
				try {
					jQuery.parseJSON(str);
				} catch (e) {
					return false;
				}
				return true;
			}

			function cambiarDocumento( id_campo, id_historia ){
				var documento = $("#"+id_campo).text();
				var historia = $("#"+id_historia).val();
				if( documento == "" ) return;

				documento = documento.split(" ");
				if( documento.length != 2 )
					return;

				var tipo_doc = documento[0];
				documento = documento[1];
				$("#documento_antes").text( tipo_doc+" "+documento );
				$("#documento_nuevo").val( documento );
				$("#tipdoc").val( tipo_doc );
				$("#tipoantes").val( tipo_doc );
				$("#docantes").val( documento );
				$("#iddoccambiando").val( id_campo );
				$("#historiacambiando").val( historia );
				if( $("#cambioDocumento").val() == "on" ){
					$("#tipdoc  option[value='"+$("#nuevoTd").val()+"']").attr("selected", "selected");
					$("#documento_nuevo").val( $("#nuevoDocumento").val() );
				}
				$.blockUI({ message: $('#form_cambiar_documento') });
			}

			function cambiarDocPac(){
				var tipoAntes = $("#tipoantes").val();
				var docAntes = $("#docantes").val();
				var tipoDespues = $("#tipdoc").val();
				var docDespues = $("#documento_nuevo").val();
				var historia = $("#historiacambiando").val();
				if( tipoDespues == '' || docDespues == '' || docDespues.length < 3 )
					return;
				if( tipoDespues == tipoAntes && docDespues == docAntes )
					return;

				var wemp_pmla = $("#wemp_pmla").val();
				$.blockUI({ message: $('#msjEspere') });
				$.post('unificarHistorias.php', { wemp_pmla: wemp_pmla, action: "cambiarDocumento", historia: historia, tipodoc: tipoDespues, documento: docDespues, tipodoca: tipoAntes, documentoa: docAntes, consultaAjax: '' } ,
					function(data) {
						$.unblockUI();
						if( isJsonString( data ) ){
							data = eval ("("+data+")");
							if( data.res == "OK" ){
								alert("Documento cambiado con éxito");
								var idcampo = $("#iddoccambiando").val();
								$("#"+idcampo).text(tipoDespues+" "+docDespues);
							}else{
								alert( data.msg );
							}
						}else{
							alert("Error en la solicitud "+data);
						}
					});

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
