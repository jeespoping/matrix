<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Configuracion de minutas
 * Fecha		:	2013-01-21
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	El objetivo del programa es permitir crear y configurar los productos de las minutas,
					considerando que es posible crear productos desde el programa
 * Condiciones  :   A continuacion algunas de las condiciones importantes del programa.
					---Una minuta consta de un patron, un servicio y multiples productos
					---Puede configurarse un servicio-patron para POS, que indica que para los pacientes
					   POS, con dicho patron, se les asignan los productos pertinentes. Si el servicio-patron
					   no tiene configuracion POS, tanto a los pacientes POS y PRE se les envian los mismos
					   productos
 *********************************************************************************************************
 
 Actualizaciones:
			
 **********************************************************************************************************/
 
$wactualiz = "2013-01-29";
 
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
	echo "<title>Configuracion de minutas</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo ' <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/toJson.js" type="text/javascript"></script>';
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");
include_once("movhos/movhos.inc.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if($action=="consultarMinuta"){
		consultarDatosMinuta( $_REQUEST['minuta'], $_REQUEST['servicio'], $_REQUEST['pos']  );
	}else if ( $action == "nuevoProducto"){
		guardarProducto($_REQUEST['descripcion'], $_REQUEST['clasificacion'], @$_REQUEST['agregardetodosmodos']);
	}else if ( $action == "nuevaMinuta"){
		crearMinuta($_REQUEST['codigo_minuta'], $_REQUEST['descripcion_minuta']);
	}else if ( $action == "guardarCambiosMinuta"){
		guardarCambiosMinuta($_REQUEST['minuta'], $_REQUEST['servicio'], $_REQUEST['datos'], $_REQUEST['pos']);
	}else if ( $action == "consultarPatronesComb"){
		consultarPatronesCombinables( $_REQUEST['patron'] );
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************

	//DADO UNO O VARIOS PATRONES, LA FUNCION ME RETORNA UN SELECT CON LOS DEMAS PATRONES CON LOS QUE SE PUEDE COMBINAR
	function consultarPatronesCombinables( $wpatron ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$pos = strrpos($wpatron, ",");
		
		//buscar los combinables de un solo patron
		if ($pos == ''){
			$q = " SELECT Patsec as patrones "
				."   FROM ".$wbasedato."_000128 "
				."  WHERE Patpri = '".$wpatron."'"
				."    AND Patest = 'on' ";
		}else{
			$pats = explode(",", $wpatron );
			$and = "";
			foreach( $pats as $pos=>$patron ){
				$and.= " AND  Patsec like '%".$patron."%' ";
			}
			if($and != ""){
				$q = " SELECT Patpri as patrones "
					."   FROM ".$wbasedato."_000128 "
					."  WHERE Patest = 'on' "
					.$and;
			}
		}
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		
		$respuesta = "<option></option>";

		//Para no repetir patrones
		$nombre_patrones = array();
		
		if ($num > 0){
			while($row = mysql_fetch_assoc($res)){
			
				$patrones = explode(",", $row['patrones']);
				//Por cada patron combinable...
				foreach( $patrones as $pos=>$patron ){
					$encontrado = false;
					foreach($nombre_patrones as $pos2=>$npat){ //Si ya tenia el patron...
						if( $npat['codigo'] == $patron ){
							$encontrado = true;
							break;
						}
					}
					if( $encontrado == true )
						continue;
			
					//Como no fue encontrado ( no lo tenia ) lo busco
					$q2 = " SELECT Diedes as nombre"
						."   FROM ".$wbasedato."_000041 "
						."  WHERE Diecod = '".$patron."' "
						."    AND Dieest = 'on' ";
					$res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num2 = mysql_num_rows($res2);
					if( $num2 > 0 ){
						$row2 = mysql_fetch_assoc($res2);
						$opt = array();
						$opt['codigo'] = $patron;
						$opt['nombre'] = $row2['nombre'];
						array_push( $nombre_patrones, $opt ); //Agrego el patron, con la seguridad de que no lo he agregado antes
						$respuesta.="<option value='".$patron."'>".$opt['nombre']."</option>";
					}
				}
			}
		}
		echo $respuesta;
	}
	
	//Retorna una lista con las minutas disponibles
	function consultarListaMinutas(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT mincod as codigo_minuta, mindes as des_minuta"
			."   FROM ".$wbasedato."_000145 "
			."  WHERE minest = 'on' ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0){
			while($row = mysql_fetch_assoc($res))
				array_push($result, $row);
		}
		return $result;
	}
	
	//Retorna una lista con los productos disponibles
	function consultarListaProductos(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Procod as codigo, Prodes as nombre, Clacod as clasificacion, Clades as nombre_cla "
			."   FROM ".$wbasedato."_000082, ".$wbasedato."_000083 "
			."  WHERE Clacod = Procla "	
			."    AND Claest = 'on' "
			."    AND Proest = 'on' "
			." ORDER BY Clades";
					
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		
		$result = array();
				
		if ($num > 0){
			while($row = mysql_fetch_assoc($res)){
				if ( array_key_exists( $row['nombre_cla'], $result) == false ) 
					$result[ $row['nombre_cla'] ] = array();
					
				$producto = array();
				$producto['codigo'] = $row['codigo'];
				$producto['nombre'] = $row['nombre'];
				$producto['clasificacion'] = $row['clasificacion'];
				array_push( $result[ $row['nombre_cla'] ], $producto ); 
			}
		}
		return $result;
	}
	
	function consultarListaClasificaciones(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Clacod as codigo, Clades as nombre "
			."   FROM ".$wbasedato."_000083 "
			."  WHERE Claest = 'on' "
			."    AND Claser = '' "
			." ORDER BY Clades";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0){
			while($row = mysql_fetch_assoc($res))
				array_push($result, $row);
		}
		return $result;
	}
	//Retorna una lista con los patrones disponibles
	function consultarListaPatrones(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Diecod as codigo, Diedes as nombre"
			."   FROM ".$wbasedato."_000041 "
			."  WHERE Dieest = 'on' ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
		
        if ($num > 0){
			while($row = mysql_fetch_assoc($res))
				array_push($result, $row);
		}
		$combinados = consultarListaPatronesCombinados();
		foreach( $combinados as $pos=>$patron)
			array_push($result, $patron);
		
		return $result;
	}
	//Retorna una lista con las combinaciones de patrones creadas en la tabla de productos de la minuta
	function consultarListaPatronesCombinados(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT DISTINCT Msppat as patron "
			."   FROM ".$wbasedato."_000146 "
			."  WHERE Msppat like '%,%' "
			."	  AND Mspest = 'on' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);	
		
		$result = array();
		
		if ($num > 0){
			while($row = mysql_fetch_assoc($res)){
				$pats = explode(",", $row['patron'] );
				$nombres =" ";
				foreach( $pats as $pos=>$patron ){
					$qq = " SELECT Diecod as codigo, Diedes as nombre"
						."   FROM ".$wbasedato."_000041 "
						."  WHERE Diecod = '".$patron."' ";
					
					$resq = mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qq." - ".mysql_error());
					$numq = mysql_num_rows($resq);
					
					if ($numq > 0){
						$rowq = mysql_fetch_assoc($resq);
						$nombres.= $rowq['nombre']." - ";
					}
				}
				$nombres = substr_replace($nombres,"",-2);
				$val['codigo'] = $row['patron'];
				$val['nombre'] = $nombres;
				array_push($result, $val);
			}

		}		
		return $result;
	}
	
	//Retorna una lista con los servicios disponibles ( Desayuno, Almuerzo... )
	function consultarListaServicios(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Sercod as codigo, Sernom as nombre"
			."   FROM ".$wbasedato."_000076 "
			."  WHERE Serest = 'on' ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0){
			while($row = mysql_fetch_assoc($res))
				array_push($result, $row);
		}		
		return $result;
	}
	
	//Retorna los productos que hacen parte de una minuta y un servicio
	function consultarDatosMinuta( $wcodminuta, $wcodservicio, $wParaPos ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT A.Msppat as pa, A.Msppro as pr, Clacod as cl, Prodes as de "
			."   FROM ".$wbasedato."_000146 A, ".$wbasedato."_000082, ".$wbasedato."_000083 "
			."  WHERE A.Mspmin = '".$wcodminuta."' "
			."    AND A.Mspser = '".$wcodservicio."' "
			."    AND A.Msppro = Procod "
			."    AND Clacod = Procla "
			."    AND Msppos = '".$wParaPos."' "
			."    AND Claest = 'on' "
			."    AND Proest = 'on' ";
						
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0){
			while($row = mysql_fetch_assoc($res)){				
				array_push($result, $row);				
			}
		}		
		echo json_encode( $result );
	}
	
	function evaluar_existencia_producto( $wnombre ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$palabras = explode(" ", $wnombre);
		$lista = array();
		foreach( $palabras as $palabra ){
			$palabra = trim( $palabra );//quitar espaciones
			if( strlen( $palabra ) < 3 )
				continue;
			$cont = 0;//Controlar que este en el ciclo al maximo 10 veces
			while ( (substr($palabra, -1) == "s" || substr($palabra, -1) == "S") && $cont < 10){ //si termina en "s"
				$palabra = substr( $palabra, 0, -1);//quitar la s	
				$cont++;
			}
			if( strlen( $palabra ) >=8 ){ //si la palabra tiene mas de 8 digitos, dividirla en 2
				$pos = ceil( (strlen($palabra)) / 2 );
				$p1 = substr($palabra, 0, $pos); 
				$p2 = substr($palabra, $pos); 
				array_push( $lista, $p1 );
				array_push( $lista, $p2 );
			}else{
				array_push( $lista, $palabra );
			}
		}
		$cadena = "";
		$i=0;
		foreach($lista as $word){
			if($i==0)
				$cadena.=$word;
			else
				$cadena.="|".$word;
			$i++;
		}
		
		$q = "SELECT Prodes as nombre FROM ".$wbasedato."_000082 WHERE Prodes REGEXP '".$cadena."' ";
		$res = mysql_query($q,$conex); 
		$num = mysql_num_rows($res);
		$result = array();
		if( $num > 0 ){
			while($row = mysql_fetch_assoc($res)){				
				array_push($result, $row);				
			}
		}
		return $result;
	}
	
	//Guarda en la bd un producto creado desde la interfaz
	function guardarProducto( $wnombre, $wclasificacion, $wagregarDeTodosModos ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
				
		$user_session = explode('-',$_SESSION['user']);
		$user_session = $user_session[1];
		
		$codigo_producto = 1;
		
		//la variable agregardetodosmodos es false cuando hago la solicitud la primera vez
		//Si hay productos que coincidan, retorna la lista de ellos y no guarda el producto
		//Si luego de ver la lista, insisto en guardar el producto, agregardetodosmodos es igual a true,
		//Por lo que no verifica nuevamente si hay productos parecidos
		if( $wagregarDeTodosModos == 'false' ){
			$existentes = evaluar_existencia_producto( $wnombre );
			if( count($existentes) > 0 ){
				$mensaje = "OK";
				foreach( $existentes as $posf=>$producto ){
					$mensaje.=" \n    *".$producto['nombre'];
				}
				echo $mensaje;
				return;
			}
		}
		
		$q = "SELECT Procod FROM ".$wbasedato."_000082 WHERE id = (SELECT MAX(id) FROM ".$wbasedato."_000082)";
		$res = mysql_query($q,$conex); 
		$num = mysql_num_rows($res);
	
		if( $num > 0 ){
			$row = mysql_fetch_assoc($res);
			$codigo_producto = $row['Procod'];
			$codigo_producto++;
		}

		$insert = "INSERT INTO  ".$wbasedato."_000082 "
								."	(Medico, "
								."	Fecha_data, "
								."	Hora_data, "
								."	Prodes, "
								."	Procod, "
								."	Procla, "
								."	Proest, "
								."	Seguridad) "
						."    VALUES "
								."	('".$wbasedato."', "
								."	'".date("Y-m-d")."', "
								."	'".date("H:i:s")."', "
								."	'".$wnombre."', "
								."	'".$codigo_producto."', "
								."	'".$wclasificacion."', "
								."	'on', "
								."	'C-".$user_session."')";
							
		$res = mysql_query($insert,$conex); 
		
		$guardo = mysql_insert_id();
		if( $guardo ){
			echo "NULL";
			return;
		}

		echo $codigo_producto;
	}
	
	//Guarda en la bd una minuta creada desde la interfaz
	function crearMinuta( $wcodigo, $wdescripcion ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$user_session = explode('-',$_SESSION['user']);
		$user_session = $user_session[1];
		
		while ( strlen($wcodigo) < 4 ){
			$wcodigo = "0".$wcodigo;			
		}
		
		$insert = "INSERT INTO  ".$wbasedato."_000145 "
								."	(Medico, "
								."	Fecha_data, "
								."	Hora_data, "
								."	Mincod, "
								."	Mindes, "
								."	Minest, "
								."	Seguridad) "
						."    VALUES "
								."	('".$wbasedato."', "
								."	'".date("Y-m-d")."', "
								."	'".date("H:i:s")."', "
								."	'".$wcodigo."', "
								."	'".$wdescripcion."', "
								."	'on', "
								."	'C-".$user_session."')";
							
		$res = mysql_query($insert,$conex); 
		echo "OK";
	}

	//Guarda en la bd los productos para una minuta y un servicio
	function guardarCambiosMinuta( $wminuta, $wservicio, $wdatos, $wParaPos ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla; 
		
		$wdatos = str_replace("\\", "", $wdatos);
		$wdatos = json_decode( $wdatos, true );

		$user_session = explode('-',$_SESSION['user']);
		$user_session = $user_session[1];

		//Se eliminan todos los productos de la minuta para el servicio
		$q ="Delete FROM ".$wbasedato."_000146 WHERE Mspmin='".$wminuta."' AND Mspser = '".$wservicio."' AND Msppos = '".$wParaPos."'";
		$res = mysql_query($q,$conex); 
		
		//Por cada producto se inserta para la minuta y el servicio
		foreach ( $wdatos as $dato ){
		
			$insert = "INSERT INTO  ".$wbasedato."_000146 "
									."	(Medico, "
									."	Fecha_data, "
									."	Hora_data, "
									."	Mspmin, "
									."	Mspser, "
									."	Msppat, "
									."	Msppro, "
									."	Msppos, "
									."	Mspest, "
									."	Seguridad) "
							."    VALUES "
									."	('".$wbasedato."', "
									."	'".date("Y-m-d")."', "
									."	'".date("H:i:s")."', "
									."	'".$wminuta."', "
									."	'".$wservicio."', "
									."	'".$dato['pa']."', "
									."	'".$dato['pr']."', "
									."	'".$wParaPos."', "									
									."  'on', "
									."	'C-".$user_session."')";
			$res = mysql_query($insert,$conex); 
		}
		echo "OK";
	}

	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		
		$columnasVisibles = 5; //Cuantas columnas (con los patrones) seran visibles
		$widthColPatrones = floor(75 / $columnasVisibles); //width de las columnas de patrones( 75% / numero de columnas), la primera(clasificaciones) tendra el 25% 
		$patrones = consultarListaPatrones();
		$servicios = consultarListaServicios();
		$clasificaciones = consultarListaClasificaciones();		
		$productos = consultarListaProductos();
		
		//CREAR ARREGLO EN HTML PARA EL AUTOCOMPLETAR
		$caracteres = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'");
        $caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","");
		$prods_json = array();
		foreach( $productos as $clasificacion=>$eleme){
			foreach($eleme as $posz=>$product){
				$product['nombre'] = str_replace( $caracteres, $caracteres2, $product['nombre'] );
				$product['nombre'] = utf8_decode( $product['nombre'] );
				array_push( $prods_json, trim($product['codigo']." - ".$product['nombre']) );
			}
		}
		$pr_json = json_encode( $prods_json );
		echo "<input type='hidden' id='productos_json' value='".$pr_json."' />";
		//------FIN AUTOCOMPLETAR
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		echo "<input type='hidden' id='patrones_elegidos' value='' />";

		encabezado("CONFIGURACION DE MINUTAS", $wactualiz, "clinica");
		
		echo "<center>";
		echo '<span class="subtituloPagina2">Parámetros de consulta</span>';
		echo "</center>";
		echo '<div id="div_opciones" style="float:right; display:none;">
		  <div>
			<button id="boton_configuracion">Opciones</button>
			<button id="select">Seleccione</button>
		  </div>
		  <ul>
			<li><a href="javascript:mostrarFormularioCombinar();">Nuevo patron combinable</a></li>
			<li><a href="javascript:configurarPOS();"><font id="titulo_pos">Configurar POS</font></a></li>
		  </ul>
		</div>';
		echo '<br><br>';
		
		echo '<div style="width: 100%">';
		
		//------------TABLA DE PARAMETROS-------------
		echo '<table align="center">';
		echo '<tr>';
		echo '<td class="encabezadotabla">Minutas</td>';
		//LISTA DE MINUTAS
		echo '<td class="fila1">';
		echo '<div id="lista_minutas_div" align="center" style="width: 100%; margin-right:25px;">';
		$minutas = consultarListaMinutas();
		$i=1;
		echo "<select id='lista_minutas' align='center' style='width: 95%; margin:5px;'>";
		echo "<option value=''>&nbsp;</option>";
		foreach( $minutas as $pos=>$minuta){
			echo '<option value="'.$minuta['codigo_minuta'].'" >'.$minuta['codigo_minuta']."-".$minuta['des_minuta'].'</option>';
			$i++;
		}
		echo "</select>";
		echo '<button id="boton_nueva_minuta">Nueva</button>';
		echo "</div>";
		echo "</td>";
		echo "</tr>";
		//FIN LISTA MINUTAS
		echo "<tr>";
		echo '<td class="encabezadotabla">Servicio</td>';
		//LISTA DE SERVICIOS
		echo '<td class="fila1">';
		echo "<div align='center'>";
		echo "<select id='lista_servicios'   align='center' style='width: 95%; margin:5px;'>";
		echo "<option></option>";
		foreach($servicios as $pos =>$servicio)
			echo '<option value="'.$servicio['codigo'].'">'.$servicio['nombre'].'</option>';
		echo '</select>';
		echo '</div>';
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		//FIN LISTA SERVICIOS
		echo "<div id='titulo_pos_div' align='center' style='margin-top:10px; display:none;'><font size=4 color='#FF00FF'>RECUERDE QUE ESTA CONFIGURANDO LA MINUTA PARA <b>POS</b></font></div>";
		
		//------------FIN TABLA DE PARAMETROS-------------
		echo "<br><br><br>"; 

		echo '<div class="enlinea" style="width: 79%; display:none;" id="gran_contenedor">';
		
		//LISTA CON ELEMENTOS A DESECHAR
		echo "<div id='caneca_basura' class='enlinea' style='border:2px dashed orange; width: 25%; height: 60px;'>";
		echo "<p align='center'><b>Arrastre aqui para eliminar</b></p>";
		echo "</div>";
		
		echo "<div class='enlinea' style='width: 48%;'></div>";
		
		//TABLA DE PAGINACION
		echo "<div class='enlinea' style='width: 25%;'>";
		$max_paginas = ceil((sizeof($patrones)) / $columnasVisibles );
		echo "<input type='hidden' id ='paginas' value='".$max_paginas."' />";
		echo "<input type='hidden' id ='pagina_visible' value='1' />";
		echo '<table class="enlinea"  align="right" id="tabla_paginacion">';
		echo '<tr class="encabezadotabla">';
		echo '<td align="center" id="td_pagina" colspan="5">Página 1 de '.$max_paginas.'</td>';
		echo '</tr>';
		echo '<tr>';
		echo "<td onclick=\"cambiarPagina('ini');\" title='Primera' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' class='encabezadoTabla'>";
		echo '<font style="font-weight:bold">&nbsp;<<&nbsp;</font>';
		echo '</td>';
		echo "<td onclick=\"cambiarPagina('ant');\" title='Anterior' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' class='encabezadoTabla'>";
		echo '<font style="font-weight:bold">&nbsp;<&nbsp;</font>';
		echo '</td>';
		echo '<td>';
		echo "<select onchange=\"cambiarPagina('sel');\" id='paginaMostrada'>";
		$i=1;
		while ( $i <= $max_paginas ){
			echo '<option value="'.$i.'">'.$i.'</option>';
			$i++;
		}
		echo '</select>';
		echo '</td>';
		echo "<td onclick=\"cambiarPagina('sig');\" title='Siguiente' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' class='encabezadoTabla'>";
		echo '<font style="font-weight:bold">&nbsp;>&nbsp;</font>';
		echo '</td>';
		echo "<td onclick=\"cambiarPagina('fin');\" title='Ultima' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' class='encabezadoTabla'>";
		echo '<font style="font-weight:bold">&nbsp;>>&nbsp;</font>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		//----FIN PAGINACION----
		echo "</div>";
		
		
		//FORMULARIO DE UNA MINUTA
		echo "<div>";
		echo "<table border=0 align='center' width='100%' id='tabla_minuta'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td colspan=".( $columnasVisibles + 1 )." align='center'><font id='titulo_minuta' size=5 color='#FFFF33'>Minuta</font></td>";
		echo "</tr>";
		echo "<tr  class='encabezadotabla'>";
		echo "<td width='25%'>&nbsp;</td>";
		
		//FILA CON LOS PATRONES
		$contador_columnas = 1;
		$ocultar_columna = "";
		$numero_de_pagina = 1;
		foreach($patrones as $pos =>$patron){
			if( $contador_columnas > $columnasVisibles )
				$ocultar_columna = " style='display:none;' ";
				
				$numero_de_pagina = ceil( $contador_columnas / $columnasVisibles );
					
			echo "<td width='".$widthColPatrones."%' title='".$patron['nombre']."' class='msg_tooltip pag".$numero_de_pagina."' ".$ocultar_columna.">".$patron['codigo']."</td>";
			$contador_columnas++;
		}	
		echo "</tr>";
		//--------FIN FILA PATRONES------
		
		$wclass='fila2';

		$colspan = 0;
		( sizeof($patrones) > $columnasVisibles )? $colspan = $columnasVisibles : $colspan = sizeof($patrones);
		
		echo "<tr class='ui-state-hover' style='font-size:8pt;'>";
		echo "<td colspan=".( $colspan +1 )." ><font id='fila_servicio' size=4 color='#000000' style='margin-left:2%;'>Nombre servicio</font></td>";
		echo "</tr>";
		
		//SE IMPRIME UNA FILA POR CADA CLASIFICACION
		foreach( $clasificaciones as $pos2 => $clasificacion ){
			echo "<tr class='".$wclass." trclasificacion".$clasificacion['codigo']."' >";
			echo "<td><b>".$clasificacion['nombre']."</b></td>";
			$contador_columnas = 1;
			$ocultar_columna = "";
			for($i=0; $i<sizeof($patrones);$i++){	
				if( $contador_columnas > $columnasVisibles )
					$ocultar_columna = " style='display:none;' ";
				$cod_patron = $patrones[ $i ]['codigo'];
				
				$numero_de_pagina = ceil( $contador_columnas / $columnasVisibles );
				
				echo "<td class='tdclasificacion".$clasificacion['codigo']." pag".$numero_de_pagina."' ".$ocultar_columna." patron='".$cod_patron."'>";
				echo "<ul class='lista_ordenable ulclasificacion".$clasificacion['codigo']."' style='width: 100%;'></ul>";
				echo "</td>";
				$contador_columnas++;
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";
		
		echo "</div>";
		//------FIN FORMULARIO------
		
		
		//LISTA DE PRODUCTOS
		$class1 = "ui-state-barra";
		$class2= "ui-state-barra2";
		echo '<div class="enlinea" id="lista_productos" style="width: 19%; margin-left: 1%; display:none; height: 750px; overflow:auto;">';
		echo '<div align="center"><b class="encabezadotabla">Lista de Productos</b><br><button id="boton_nuevo_producto">Nuevo</button></div><br><br>';

		$i=0;

		foreach( $productos as $clasificacion=>$elementos){
			echo '<ul class="lista_items" style="width:100%">';
			echo "<li class='ui-state-hover'><b>".$clasificacion."</b></li>";
			foreach($elementos as $pos=>$producto){
				echo "<li class='".$class1." producto'  align='left' clasificacion='".$producto['clasificacion']."' codigo='".$producto['codigo']."' value='".$producto['codigo']."'> ".$producto['nombre']."</li>";
			}
			echo "</ul><br><br>";
		}
		echo "</div>";
		//-------FIN LISTA PRODUCTOS
		
		echo "</div>";//Gran contenedor
		
		echo '<br><br>';
		echo '<center>';
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		echo "<input type='button' id='boton_guardar_minuta' value='Guardar'  >";
		echo "<br><br>";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()'>";
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
		
		//Formulario crear Columna con patron combinable
		echo "<div id='formulario_crear_combinable' style='display:none; align='center'>";
		echo "<center>";
		echo "<table>";
		echo "<tr>";
		echo "<td colspan=2 class='encabezadotabla' align='center'>";
		echo '<font size="4" color="#ffffff" style="margin-left:2%;">Nuevo Patron combinable</font>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Patron</td>";
		echo "<td class='fila1'>";
		echo "<select id='lista_patrones' onChange='mostrarPatronesCombinables();'   align='center' style='margin:5px;'>";
		echo "<option></option>";
		foreach($patrones as $pos =>$patron)
			echo '<option value="'.$patron['codigo'].'">'.$patron['nombre'].'</option>';
		echo '</select>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Combinar con</td>";
		echo "<td class='fila1'>";
		echo "<select id='lista_patrones_adicional' onChange='mostrarCombinacion();'   align='center' style='margin:5px;'>";
		echo "<option></option>";
		echo '</select>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Resultado</td>";
		echo "<td class='fila1' align='center'>";
		echo "<div id='resultadocombi' style='font-size: 11pt;'></div>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "<input type='button' id='boton_aceptarcombi' value='Aceptar'  >";
		echo "<input type='button' id='boton_cancelarcombi' value='Cancelar'  >";
		echo "</center>";
		echo "</div>";
		
		//Formulario crear producto
		echo "<div id='formulario_crear_producto' style='display:none' align='center'>";
		echo '<font size="4" color="#000000" style="margin-left:2%;">Nuevo Producto</font>';
		echo "<br>";
		echo "<br>";
		echo "<table align='center' border=0>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Descripcion</td>";
		echo "<td class='fila1'>";
		echo "<input type='text' id='descripcion_np'    style='width: 95%;'>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla'>Clasificacion</td>";
		echo "<td class='fila1'>";
		echo "<select id='clasificacion_np'   align='center' style='width: 95%; margin:5px;'>";
		echo "<option></option>";
		foreach($clasificaciones as $pos =>$clasificacion)
			echo '<option value="'.$clasificacion['codigo'].'">'.$clasificacion['nombre'].'</option>';
		echo '</select>';
		echo "</td>";
		echo "</tr>";
		echo "<tr><td colspan=2 align='center'><input type='button' id='boton_np' value='Guardar'  >";
		echo "<input type='button' id='cancelar_np' value='Cancelar'  ></tr>";
		echo "</table>";
		echo "<br>";
		echo "<br>";
		echo "</div>";
		
		echo "<div id='formulario_crear_minuta' style='display:none' align='center'>";
		echo "<font size=4 color='#000000' style='margin-left:2%;'>Nueva Minuta</font>";
		echo "<br>";
		echo "<br>";
		echo "<label>Por favor agregue la descripcion:</label>";
		echo "<br>";
		echo "<input type=text id='nueva_min_des' />";
		echo "<br>";
		echo "<br>";
		echo "<br>";
		
		echo "<input type=button onclick='guardarNuevaMinuta()' value='Crear' />";
		echo "<input type=button onclick='$.unblockUI()' value='Cancelar' />";
		echo "</div>";
	}
?>

<style>
	.enlinea{
		display:inline-block;
		vertical-align: top;
		/*display: -moz-inline-stack;*/ /* FF2*/
		zoom: 1; /* IE7 (hasLayout)*/
		*display: inline; /* IE */
	}
	
	.ui-menu { position: absolute; width: 200px; }
	
	.ui-state-barra   {border: 1px solid #C3D9FF; background: #C3D9FF; color: #000;}
	.ui-state-barra   {
		background: #fff; /* Old browsers */
		background: -moz-linear-gradient(top,  #fff 0%, #C3D9FF 30%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#C3D9FF)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* IE10+ */
		background: linear-gradient(to bottom,  #fff 0%,#C3D9FF 30%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#C3D9FF',GradientType=0 ); /* IE6-8 */
		-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#C3D9FF')";
		zoom:1;
		cursor: pointer;
	}
	.ui-state-barra2   {border: 1px solid #fefcea; background: #000000; color: #000;}
	.ui-state-barra2   {
		background: #fff; /* Old browsers */
		background: -moz-linear-gradient(top,  #fff 0%, #E8EEF7 30%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#E8EEF7)); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* Opera 11.10+ */
		background: -ms-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* IE10+ */
		background: linear-gradient(to bottom,  #fff 0%,#E8EEF7 30%); /* W3C */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#E8EEF7',GradientType=0 ); /* IE6-8 */
		-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E8EEF7')";
		zoom:1;
		cursor: pointer;
	}
	
	.lista_ordenable, .lista_items {
		list-style-type: none; margin: 0; padding: 0; display:inline-block; vertical-align:top; 
		font-size: 9pt;
		font-weight: normal; 
	}
    .lista_ordenable li, .lista_items li { margin: 0 3px 3px 0; padding: 0.4em; padding-left: 1.5em; font-size: 1.2em; }
    .lista_ordenable li span, .lista_items li span { position: absolute; margin-left: -1.3em; }
	
	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>
<script>
	//Variable de estado que indica si se esta moviendo un producto
	var moviendo_global = false;
	var configurandoPOS = false; //Indica si los productos de la minuta son para pacientes tipo POS
	
//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
	
		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});
		
		$("#boton_np").click(function(){
			crearProducto();
		});
		
		//--------AUTOCOMPLETAR
		var prods_array = new Array();
		//Selecciona la variable con los productos y llena el arreglo
		var prods = $("#productos_json").val();
		datos = eval ( prods );		
		for( i in datos ){
			prods_array.push( datos[i] );
		}
		//Autocompletar para los productos
        $( "#descripcion_np" ).autocomplete({
            source: prods_array,
			minLength : 3
        });
		//-------FIN AUTOCOMPLETAR
		
		$("#boton_aceptarcombi").click(function(){
			crearColumnaPatronCombinado();
		});
		$("#cancelar_np, #boton_cancelarcombi").click(function(){
			$.unblockUI();
			$(".del").remove(); //elimina las opciones combinadas del select de patrones
			$("#lista_patrones option").show();
			$("#patrones_elegidos").val('');
			$("#resultadocombi").text('');
			$("#lista_patrones_adicional").html('<option value="" selected></option>');
			$("#clasificacion_np option").eq(0).attr('selected',true);
			$("#descripcion_np").val('');
		});
		$("#boton_guardar_minuta").hide().click(function(){
			guardarCambiosMinuta();
		});
		$("#lista_minutas").change(function() {
			consultarMinuta();
		});
		$("#lista_servicios").change(function() {
			consultarMinuta();
		});
		
		agregarHover(".producto");
		
		//Cada producto sera un draggable
		$( ".producto" ).draggable({
			revert: "invalid",
			opacity: 0.75 ,
			helper: "clone",
			cancel: "button",
			start: function( event, ui ) {
				moviendo_global = true;
				//Cuando se comienze a mover el producto, se deben mostrar la fila que tenga la clasificacion del producto
				var item = ui.helper;
				item.css('width','75px'); //le doy un tamano fijo al li
			},
			stop: function( event, ui ) {
					moviendo_global = false;
					$( ".lista_ordenable").css('border', "0");
			}
		}).disableSelection();
		
		//Cada producto conecta con una lista (ul) que tenga la clase ulclasificacion + (la clasificacion del producto)
		$(".producto").each( function(){
				var conectar = "ulclasificacion"+$(this).attr('clasificacion');
				$(this).draggable( "option", "connectToSortable", "."+conectar );
		});
		
		//Agrego la funcion sortable a las listas dentro de cada td
		$( ".lista_ordenable" ).sortable({
			//Cuando la lista reciba un producto, verifica que no haya sido agregado antes, y si ya fue agregado, lo esconde
			receive: function( event, ui ) {
				var item = ui.item;
				var class_ul = "ulclasificacion"+item.attr('clasificacion');
				$( "."+class_ul ).css('border', "0"); //Cuando recibe un producto, quita el borde que produce HOVER
				var i=0;
				$(this).find('li:visible').each(function(){
					if ( $(this).val() == item.val() && i==1){
						$(this).hide();
					}
					if ( $(this).val() == item.val() && i==0)
						i=1;
				});
			}
		}).disableSelection();

		//llevar estilo a la lista de minutas
		//$("#lista_minutas").buttonset();
		
		boton_configuracion();
		
		//llevar estilo al boton agregar minuta y asignar funcion click
		$("#boton_nueva_minuta").button({
            icons: {
                primary: "ui-icon-plusthick"
            }
        }).click(function(){
			crearMinuta();
		});

		//llevar estilo al boton agregar producto y asignar funcion clic
		$("#boton_nuevo_producto").button({
            icons: {
                primary: "ui-icon-plusthick"
            }
        }).click(function(){
			//mostrar formulario para crear producto
			$.blockUI({ message: $('#formulario_crear_producto') });
		});
		
		//Agrego la funcion droppable al div que sera la caneca
		$("#caneca_basura").droppable({
            activeClass: "ui-state-highlight",
            hoverClass: "ui-state-hover",
            drop: function( event, ui ) {
				var item = ui.helper;	//obtengo el item que llegue			
				item.remove(); //lo elimino
				animarColor( 'caneca_basura' ); //cambio el color del div
            }
        });
		$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
	});
	
	//Funcion que se llama cuando se presiona el boton "Combinar" que se encarga de consultar los patrones con los que se puede combinar el elegido
	//y crear las opciones en el input box con esos patrones
	function mostrarPatronesCombinables(){
		
		var codigoPatron = $("#patrones_elegidos").val();
		if( codigoPatron == '' )
			codigoPatron = $("#lista_patrones").val();
		
		if( codigoPatron == '')
			return;
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('configuracion_minutas.php', { wemp_pmla: wemp_pmla, action: "consultarPatronesComb", patron: codigoPatron, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$("#lista_patrones_adicional").html( data );
				 mostrarFormularioCombinar();
			});
	}
	
	function mostrarCombinacion(){
		var pat1 ="";
		if( $("#patrones_elegidos").val() != '')
			pat1 = $("#patrones_elegidos").val();
		else			
			pat1 = $("#lista_patrones").val();
		pat1 = pat1+","+$("#lista_patrones_adicional").val();
		var patron_adicional = $("#lista_patrones_adicional").val();
		$("#patrones_elegidos").val( pat1 );
		//Buscar el nombre del patron adicional en el select de patrones
		var pats = pat1.split(",");
		var i=0;
		var texto = "  ";
		for( i in pats){
			$("#lista_patrones option").each( function(){
				if( $(this).val() == pats[i] ){
					texto+= $(this).text()+" - ";
					return false;
				}
			});
		}
		texto= texto.substring( 0, texto.length -2);

		$("#lista_patrones option").hide();
			
		var opcion = "<option class='del' value='' selected></option><option class='del' value='"+pat1+"' >"+texto+"</option>";
		$("#lista_patrones").append( opcion );
		$("#lista_patrones_adicional").html('');
		$("#resultadocombi").text(pat1);
	}
	
	//AGREGA A LA TABLA DE LA MINUTA, UNA NUEVA COLUMNA CON EL PATRON COMBINADO, CON TODAS LAS CONFIGURACIONES NECESARIAS
	function crearColumnaPatronCombinado(){
			var patrones = $("#resultadocombi").text();
			var texto = $("#lista_patrones option:visible").eq(1).text();
			
			var patrones_array = patrones.split(",");
			
			var agregarColumna = true;
			//Si ya existe la combinacion no debe agregar la columna otra vez, retornar
			$("#tabla_minuta tr").eq(1).find("td").each(function(){
				if( ($(this).text()).indexOf(",") > 0 ){
					array_pats = ($(this).text()).split(",");
					if( array_pats.length == patrones_array.length ){
						if( array_pats.sort().toString() == patrones_array.sort().toString() ){
							agregarColumna = false;
							return false;
						}
					}
				}
			});
			
			if( agregarColumna == false){
				alerta("La combinacion ya existe");
				$(".del").remove();
				$("#lista_patrones option").show();
				$("#patrones_elegidos").val('');
				$("#resultadocombi").text('');
				$("#lista_patrones_adicional").html('<option value="" selected></option>');
				return;
			}
			
			var keyx=patrones.replace(",","");
			
			//IDENTIFICAR si la nueva columna hace parte de la ultima pagina, o se debe crear una nueva
			var columnas_visibles = $("#tabla_minuta tr:last .pag1").length;
			var ultima_pagina = $("#paginas").val();
			var class_ultima = "pag"+ultima_pagina;
			var columnas_ultima_pagina = $("#tabla_minuta tr:last ."+class_ultima).length;

			if( columnas_visibles == columnas_ultima_pagina ){
				//Modificar lo necesario, se creara una nueva pagina
				ultima_pagina++;
				$("#paginas").val(ultima_pagina)
				$("#paginaMostrada").append("<option value='"+ultima_pagina+"'>"+ultima_pagina+"</option>");
			}
			
			var width = $("#tabla_minuta .pag1").eq(1).css("width"); //Cual es el width de los td
			
			//Agregar patron a la fila de patrones
			var td = "<td class='msg_tooltip"+keyx+" pag"+ultima_pagina+"' title='"+texto+"' width='"+width+"'>"+patrones+"</td>";
			$("#tabla_minuta tr").eq(1).append(td);
			
			$(".msg_tooltip"+keyx).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			
			//agregar un td a cada fila para el nuevo patron
			$("#tabla_minuta tr").each(function(){
				if( $(this).index() > 2 ){
					var clase = $(this).find("td").eq(1).attr("class"); //traer las clases del td
					var clases_array = clase.split(" ");
					var clase_ul = $(this).find("td ul").attr("class"); //traer las clases del ul dentro del td
					var td_n = "<td class='"+clases_array[0]+" pag"+ultima_pagina+"' patron='"+patrones+"' >";
					td_n+="<ul class='"+clase_ul+"' style='width: 100%;'></ul>";
					td_n+="</td>";
					$(this).append(td_n);
				}
			});
			
			cambiarPagina('fin'); //mostrar la ultima columna para ver la columna con la combinacion
			
			//Ahora cada td agregado tiene un ul dentro, que debe ser sortable
			$( ".lista_ordenable" ).sortable({
				//Cuando la lista reciba un producto, verifica que no haya sido agregado antes, y si ya fue agregado, lo esconde
				receive: function( event, ui ) {
					var item = ui.item;
					var class_ul = "ulclasificacion"+item.attr('clasificacion');
					$( "."+class_ul ).css('border', "0"); //Cuando recibe un producto, quita el borde que produce HOVER
					var i=0;
					$(this).find('li:visible').each(function(){
						if ( $(this).val() == item.val() && i==1){
							$(this).hide();
						}
						if ( $(this).val() == item.val() && i==0)
							i=1;
					});
				}
			}).disableSelection();
			
			$.unblockUI();
			$(".del").remove();
			$("#lista_patrones option").show();
			$("#patrones_elegidos").val('');
			$("#resultadocombi").text('');
			$("#lista_patrones_adicional").html('<option value="" selected></option>');
	}
	
	function mostrarFormularioCombinar(){
		 $.blockUI({ message: $('#formulario_crear_combinable') });
	}
	
	//Al presionar el boton superior derecho (opciones)
	function boton_configuracion(){
		
		$( "#boton_configuracion" )
		  .button({
          icons: {
				primary: "ui-icon-gear"
			  },
			  text: false
        })
		  .next()
			.button({
			  text: false,
			  icons: {
				primary: "ui-icon-triangle-1-s"
			  }
			})
			.click(function() {
			  var menu = $( this ).parent().next().show().position({
				my: "left top",
				at: "left bottom",
				of: this
			  });
			  $( document ).one( "click", function() {
				menu.hide();
			  });
			  return false;
			})
			.parent()
			  .buttonset()
			  .next()
				.hide()
				.menu();
	}

	function guardarCambiosMinuta(){
		var datos = new Array();
		//Selecciona todos los productos VISIBLES, debido a que si agrego varias veces el mismo producto, el programa lo oculta 
		//porque no si lo elimina se produce un error en la lista sortable
		$("#tabla_minuta .producto").each( function(){
			dato = new Object();
			//dato.pr = $(this).val(); //codigo producto
			dato.pr = $(this).attr('codigo'); //codigo producto
			dato.pa = $(this).parents("td").attr("patron"); //patron
			datos.push( dato );
		});
		
		var datosJson = $.toJSON( datos ); //convertir el arreglo de objetos en una variable json
		var codigoMinuta = $("#lista_minutas").val();
		var codigoServicio = $("#lista_servicios").val();
		var paraPos = "off";
		if( configurandoPOS == true )
			paraPos='on';
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('configuracion_minutas.php', { wemp_pmla: wemp_pmla, action: "guardarCambiosMinuta", datos: datosJson, minuta: codigoMinuta, servicio: codigoServicio, pos: paraPos, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				if( data == "OK" ){
					alerta("Exito al guardar los cambios");
				}else{
					alerta("Error");
				}
			});
	}
	
	function animarColor( idEle ){
		//agrega con un efecto una clase, y segundo y medio despues la quita
		$("#"+idEle).addClass( "ui-state-hover", 1000, function(){
			 setTimeout(function() {
                $("#"+idEle).removeClass( "ui-state-hover" );
            }, 1500 );
		});
	}
	
	function crearMinuta(){
		 $.blockUI({ message: $('#formulario_crear_minuta') });
	}
	
	function guardarNuevaMinuta(){
		
		var minuta = $("#lista_minutas option:last").val();
		if( minuta == "" )
			minuta=1;
		else
			minuta++;
			
		var descripcion = $('#nueva_min_des').val();
		if( descripcion == "" ){
			alert('Por favor ingrese la descripcion para la minuta');
		}
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('configuracion_minutas.php', { wemp_pmla: wemp_pmla, action: "nuevaMinuta",  descripcion_minuta: descripcion, codigo_minuta: minuta, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				if( data == 'OK'){
					alerta('Minuta guardada con exito');
					agregarMinutaEnLista(minuta, descripcion);
					$('#nueva_min_des').text("");
				}else
					alerta("Error");
			});
		
	}
	
	//Agregar en la lista de minutas una nueva
	function agregarMinutaEnLista(minuta, descripcion){
		minuta = minuta+"";
		while ( minuta.length < 4 ){
			minuta = "0"+minuta;			
		}
		
		var html_option = "<option value='"+minuta+"'>"+minuta+"-"+descripcion+"</option>";
		$("#lista_minutas").append( html_option );
			
		//var html_minuta =  '<input type="radio" id="minuta'+minuta+'" name="minutas" value="'+minuta+'" /><label for="minuta'+minuta+'">'+minuta+'</label>';
		//var idMinutaAnt = minuta-1;
		/*$("#boton_nueva_minuta").before(html_minuta);
		$("#minuta"+minuta).button().click(function() {
			consultarMinuta();
		});*/
	}
	
	function crearProducto( agregarDeTodosModos ){
		var clasificacion = $("#clasificacion_np").val();
		var descripcion = $("#descripcion_np").val();
		
		if( agregarDeTodosModos == undefined )
			agregarDeTodosModos = false;
		
		descripcion = $.trim( descripcion );
		clasificacion = $.trim( clasificacion );
		
		//Si no tiene ningun caracter...
		if( (/\w/).test(descripcion) == false){
			alert("Debe ingresar la descripcion");
			return;
		}
		//Si no selecciono clasificacion...
		if( clasificacion == '' ){
			alert("Debe seleccionar la clasificacion");
			return;
		}
		
		//Si tiene un digito o mas, y es seguido de un guion (-), ha utilizado el autocompletar
		if( (/\d+ -/).test(descripcion) ){
			alert("Al parecer desea crear un producto que ya existe, por favor verifique la informacion ingresada");
			return;
		}
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		$.unblockUI();

		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('configuracion_minutas.php', { wemp_pmla: wemp_pmla, action: "nuevoProducto", descripcion: descripcion, agregardetodosmodos: agregarDeTodosModos, clasificacion: clasificacion, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				if( (/^\d+$/).test(data) ){ //Si la respuesta es un numero, codigo del producto
					alerta('Producto guardado con exito');
					agregarEnListaNuevoProducto(data, descripcion, clasificacion);
				}else if(data.substring(0, 2) == 'OK'){ //HAY PRODUCTOS PARECIDOS AL QUE PRETENDE INGRESAR
					productoQuizasRepetido( data );
				}else
					alerta("Error");
			});
	}
	
	function productoQuizasRepetido( datos ){
		var descripcion = $("#descripcion_np").val();
		var lista = datos.substring(3);
		var mensaje = "El sistema ha detectado que desea crear el producto * "+descripcion+" * y que tiene similitud a el(los) siguiente(s) ya existente(s):";
		mensaje+="  "+lista;
		mensaje+="\n                                    ¿DESEA AGREGAR DE TODOS MODOS?        ";
		var confi = confirm(mensaje);
		if(confi){
			crearProducto( true );
		}
	}
	
	//Agregar el producto guardado en matrix, a la lista de productos
	function agregarEnListaNuevoProducto(codigo, descripcion, clasificacion){
	
		var terminado = false;
		//Busca en la lista de productos si ya existe uno con la misma clasificacion
		//Si existe, lo agrega despues
		$("#lista_productos li").each(function(){
			if( $(this).attr('clasificacion') == clasificacion ){
				//Crea el li del producto creado y guardado en matrix
				var li = '<li class="ui-state-barra producto ui-draggable" align="left" clasificacion="'+clasificacion+'" codigo="'+codigo+'" value="'+codigo+'">'+descripcion+'</li>';

				$(this).after( li ); //lo agrega despues del encontrado con la misma clasificacion
				terminado = true;
				//Agrega la funcion de draggable para el li
				$(this).next().draggable({
					revert: "invalid",
					opacity: 0.75 ,
					helper: "clone",
					cancel: "button",
					start: function( event, ui ) {
						moviendo_global = true;
						//Cuando se comienze a mover el producto, se deben mostrar la fila que tenga la clasificacion del producto
						var item = ui.helper;
						item.css('width','75px'); //le doy un tamano fijo al li
					},
					stop: function( event, ui ) {
							moviendo_global = false;
							$( ".lista_ordenable").css('border', "0");
					}
				}).disableSelection();
				//Agrega la opcion de que el li se conecte a las listas con la misma clasificacion
				var conectar = "ulclasificacion"+clasificacion;
				$(this).next().draggable( "option", "connectToSortable", "."+conectar );
				agregarHover( $(this).next());
				return false;
			}
		});
		//NO encontro un producto para esa clasificacion, hay que crear en la lista de productos
		//Una lista ul que contenga un li con el nombre de la clasificacion y otro con el nuevo producto
		if( terminado == true)
			return;
		var texto_clasificacion = $("#clasificacion_np option:selected").text();
		//Creo la lista ul con los dos items li mencionados
		var ul_lista = '<ul class="lista_items" style="width:100%">';
		//ul_lista += '<li class="ui-state-hover"><span class="ui-icon ui-icon-arrowthick-1-s"></span>'+texto_clasificacion+'</li>';
		ul_lista += '<li class="ui-state-hover"><b>'+texto_clasificacion+'</b></li>';
		ul_lista += '<li class="ui-state-barra producto ui-draggable" align="left" clasificacion="'+clasificacion+'" codigo="'+codigo+'" value="'+codigo+'">'+descripcion+'</li>';
		ul_lista += '</ul>';
		
		$("#lista_productos").append( ul_lista ); //agrego la lista ul a la lista de productos

		//Al producto le doy la opcion draggable
		$(".lista_items:last .producto").draggable({
			revert: "invalid",
			opacity: 0.75 ,
			helper: "clone",
			cancel: "button",
			start: function( event, ui ) {
				moviendo_global = true;
				//Cuando se comienze a mover el producto, se deben mostrar la fila que tenga la clasificacion del producto
				var item = ui.helper;
				item.css('width','75px'); //le doy un tamano fijo al li
			},
			stop: function( event, ui ) {
					moviendo_global = false;
					$( ".lista_ordenable").css('border', "0");
			}	
		}).disableSelection();
		var conectar = "ulclasificacion"+clasificacion;
		var selector = ".lista_items:last .producto";
		//Agrega la opcion de que el li se conecte a las listas con la misma clasificacion
		$(selector).draggable( "option", "connectToSortable", "."+conectar );
		
		agregarHover(selector);
		
		//Resetear el formulario de agregar producto
		$("#clasificacion_np option").eq(0).attr('selected',true);
		$("#descripcion_np").val('');
	}
	
	function agregarHover(selector){
		var sele = selector;
		var ele = jQuery(sele);
		ele.hover(function(){
			//Cuando el mouse este sobre el producto
			if( moviendo_global == false ){
				var class_ul = "ulclasificacion"+$(this).attr('clasificacion');//obtengo las listas dentro de cada td
				$( "."+class_ul ).css('border', "2px dashed blue");//resalto las listas, indicando donde puedo agregar el producto
			}
		},function(){
			//Cuando el mouse salga sobre el producto
			if( moviendo_global ) return;
			var class_ul = "ulclasificacion"+$(this).attr('clasificacion');//obtengo las listas dentro de cada td
			$( "."+class_ul ).css('border', "0");//quito el resalto de las listas
		});
	}
	
	//Consultar los productos que pertenecen a la minuta, para el servicio
	function consultarMinuta(){
	
		var codigoMinuta = $("#lista_minutas").val();
		var codigoServicio = $("#lista_servicios").val();
		var textoServicio = $("#lista_servicios option:selected").text();
		if( codigoMinuta != undefined ){
			$("#lista_servicios").focus();
		}
		if( codigoMinuta == "" || codigoServicio == ""){
			$("#gran_contenedor, #lista_productos, #boton_guardar_minuta, #div_opciones").hide( 'drop', {}, 500 );
			return;
		}
		
		$("#gran_contenedor, #lista_productos, #boton_guardar_minuta, #div_opciones").show( 'drop', {}, 500 );
		
		//Eliminar los productos en la tabla
		$("#tabla_minuta .producto").remove();
		var paraPos = "off";
		if( configurandoPOS == true )
			paraPos='on';
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('configuracion_minutas.php', { wemp_pmla: wemp_pmla, action: "consultarMinuta", minuta: codigoMinuta, servicio: codigoServicio, pos: paraPos, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$("#titulo_minuta").text("Minuta "+codigoMinuta);
				$("#fila_servicio").html( "<b>"+textoServicio+"</b>");
				llenarFormularioMinuta( data );	
			}, 'json');	
	}
	
	//Al consultar los productos de la minuta-servicio desde el servidor, se llena la tabla minuta con dichos productos
	function llenarFormularioMinuta( datos ){
		//pa:patron   pr:producto   cl:clasificacion
		var i=0;
		if( datos.length == 0){
			//alerta("No hay un menu creado para el servicio de "+$("#lista_servicios option:selected").text()+" y la minuta elegida");
			return;
		}
		//Por cada producto...
		for( i in datos){
			var patron = datos[i].pa;
			var clasificacion = datos[i].cl;
			var cod_producto = datos[i].pr;
			var nom_producto = datos[i].de;
			
			idtr = "trclasificacion"+clasificacion; //Identifico el tr con la clasificacion del producto
			//Creo un li para el producto
			var li = '<li class="ui-state-barra producto ui-draggable" align="left" clasificacion="'+clasificacion+'" codigo="'+cod_producto+'" value="'+cod_producto+'">'+nom_producto+'</li>';
			$("."+idtr).show();//Muestro la fila
			//Busco en los td de la fila, cual es el td que tiene el patron del producto
			$("."+idtr+" td").each(function(){
				if( $(this).attr("patron") == patron ){
					$(this).find('ul').append( li ); //Agrego el producto a la lista ul dentro del td con el patron del producto
				}
			});
		}
	}
	
	 //funcion que oculta las columnas de tal manera que se realice la paginación.
	function cambiarPagina(accion){
		var paginas = $("#paginas").val();
		var paginador = jQuery("#paginaMostrada");
		var pagina = jQuery("#td_pagina");
		var factor = 1;
		//elaboración de limites
		switch(accion)
		{
			case 'ant':
				factor = (paginador.val()*1)-1;
				break;
			case 'sig':
				factor = (paginador.val()*1)+1;
				break;
			case 'fin':
				factor = paginas;
				break;
			case 'sel':
				factor = paginador.val();
				break;
		}
		if(factor<1 || factor>paginas){
			return;
		}
		pag_visible = $("#pagina_visible").val();
		$(".pag"+pag_visible).hide();
		paginador.val(factor);
		$("#pagina_visible").val( factor );
		$("#td_pagina").html("P&aacute;gina "+factor+ " de "+paginas);
		$(".pag"+factor).show();
	}
	
	function configurarPOS(){
		if( configurandoPOS ==  false ){
			var confi = confirm("Desea configurar los productos de la minuta, para pacientes tipo POS?");
			if(!confi){
				return;
			}
			$("#titulo_pos_div").show();
			configurandoPOS = true;
			//Eliminar los productos en la tabla
			$("#tabla_minuta .producto").remove();
			//Cambiar el texto de las opciones
			$("#titulo_pos").html('NO configurar POS');	
			consultarMinuta();
			
		}else{
			var confi = confirm("Desea dejar de configurar los productos de la minuta, para pacientes tipo POS?");
			if(!confi){
				return;
			}
			$("#titulo_pos_div").hide();
			configurandoPOS = false;
			//Eliminar los productos en la tabla
			$("#tabla_minuta .producto").remove();
			//Cambiar el texto de las opciones
			$("#titulo_pos").text('Configurar POS');
			consultarMinuta();
		}		
	}
	
	function alerta( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
							$.unblockUI();
						}, 1600 );
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