<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Configuracion de menu
 * Fecha		:	2013-01-09
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	El objetivo del programa es permitir crear y configurar los productos del menu,
					considerando que es posible crear productos desde el programa
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
	echo "<title>Configuracion de menu</title>";
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
	if($action=="consultarMenu"){
		$resultado = consultarDatosMenu( $_REQUEST['patron'], $_REQUEST['servicio']  );
		echo json_encode( $resultado );
	}else if ( $action == "nuevoProducto"){
		guardarProducto($_REQUEST['descripcion'], $_REQUEST['clasificacion'], @$_REQUEST['agregardetodosmodos']);
	}else if ( $action == "guardarCambiosMenu"){
		guardarCambiosMenu($_REQUEST['patron'], $_REQUEST['servicio'], $_REQUEST['datos']);
	}else if ( $action == "consultarPatronesComb"){
		consultarPatronesCombinables( $_REQUEST['patron'] );
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************
	
	function consultarPatronesCombinables( $wpatron ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$pos = strrpos($wpatron, ",");
		
		//Si quiero buscar los combinables de un solo patron
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
	
	function consultarListaPatronesCombinados(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT DISTINCT Menpat as patron "
			."   FROM ".$wbasedato."_000147 "
			."  WHERE Menpat like '%,%' "
			."	  AND Menest = 'on' ";
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
	
	//Retorna los productos que hacen parte de un patron y un servicio
	function consultarDatosMenu( $wcodPatron, $wcodservicio, $wSinEst = false ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$condicion_and = "";
		if( $wSinEst == false)
			$condicion_and = "    AND Menest = 'on' ";
		
		$q = " SELECT A.Menpat as pa, A.Menpro as pr, Clacod as cl, Prodes as de "
			."   FROM ".$wbasedato."_000147 A, ".$wbasedato."_000082, ".$wbasedato."_000083 "
			."  WHERE A.Menser = '".$wcodservicio."' "
			."    ANd A.Menpro = Procod "
			."    AND A.Menpat = '".$wcodPatron."'"
			."    AND Clacod = Procla "
			."    AND Claest = 'on' "
			.$condicion_and
			."    AND Proest = 'on' ";
						
		$pats = array();	
		$pos = strrpos($wcodPatron, ",");	
		//El menu es de patrones combinados
		if ($pos != ''){
			$pats = explode(",", $wcodPatron );
			$and = "";
			foreach( $pats as $pos=>$patron ){
				$and.= " AND  Menpat like '%".$patron."%' ";
			}
			if( $and != "" ){
				$q = " SELECT A.Menpat as pa, A.Menpro as pr, Clacod as cl, Prodes as de "
					."   FROM ".$wbasedato."_000147 A, ".$wbasedato."_000082, ".$wbasedato."_000083 "
					."  WHERE A.Menser = '".$wcodservicio."' "
					."    ANd A.Menpro = Procod "
					."    AND Clacod = Procla "					
					. $and
					."    AND Claest = 'on' "
					.$condicion_and
					."    AND Proest = 'on' ";
			}
		}
			
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0 && ($pos == '')){
			while($row = mysql_fetch_assoc($res)){
				array_push($result, $row);
			}
		}else if( $num > 0 ){ 
			//Para controlar que, buscando los productos de N,LC  no traiga los productos de N,LC,HS pues el query trae todos estos con el like por cada patron
			while($row = mysql_fetch_assoc($res)){
				$arreglo = explode(",", $row['pa'] );
				if( (count($arreglo) == count($pats))){
					array_push($result, $row);
				}
			}	
		}
		return $result;
		
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
		if( is_null( $guardo) ){
			echo "NULL";
			return;
		}

		echo $codigo_producto;
	}

	//Guarda en la bd los productos para un patron y un servicio
	function guardarCambiosMenu( $wpatron, $wservicio, $wdatos ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla; 
		
		$wdatos = str_replace("\\", "", $wdatos);
		$wdatos = json_decode( $wdatos, true );

		//$pos = strrpos($wpatron, ",");
		
				
		$user_session = explode('-',$_SESSION['user']);
		$user_session = $user_session[1];
		
		//BUSCAR TODOS LOS PRODUCTOS, LOS QUE NO ESTEN EN LOS QUE TRAJO PONERLOS OFF
		
		$productos_antes = consultarDatosMenu( $wpatron, $wservicio, true );
		
		$query_update = " UPDATE 	".$wbasedato."_000147 "
						."	 SET 	Menest = 'off'"
						." WHERE 	Menpat =  '".$wpatron."'"
						."   AND    Menser =  '".$wservicio."'";
		$res = mysql_query($query_update,$conex); 
		
		//Por cada producto se inserta para el patron y el servicio
		foreach ( $wdatos as $dato ){
		
			$existeEnMenu = false;
			
			foreach ($productos_antes as $pos=>$producto){
				if( $producto['pr'] == $dato['pr'] && $producto['pa'] == $wpatron ){//El producto estaba
					$existeEnMenu = true; //true para que actualize
					break;
				}
			}
		
			if( $existeEnMenu == false){
				$insert = "INSERT INTO  ".$wbasedato."_000147 "
										."	(Medico, "
										."	Fecha_data, "
										."	Hora_data, "
										."	Menser, "
										."	Menpat, "
										."	Menpro, "
										."	Seguridad) "
								."    VALUES "
										."	('".$wbasedato."', "
										."	'".date("Y-m-d")."', "
										."	'".date("H:i:s")."', "
										."	'".$wservicio."', "
										."	'".$wpatron."', "									
										."	'".$dato['pr']."', "
										."	'C-".$user_session."')";
									
				$res = mysql_query($insert,$conex); 
			}else{
				$query_update = " UPDATE 	".$wbasedato."_000147 "
								."	 SET 	Menest = 'on'"
								." WHERE 	Menpro  = '".$dato['pr']."'"
								."   AND    Menpat =  '".$wpatron."'"
								."   AND    Menser =  '".$wservicio."'";
				$res = mysql_query($query_update,$conex); 
			}
		}
		echo "OK";
	}

	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		
		$patrones = consultarListaPatrones();
		$servicios = consultarListaServicios();
		$productos = consultarListaProductos();
		$clasificaciones = consultarListaClasificaciones();
		
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

		encabezado("CONFIGURACION DE MENU", $wactualiz, "clinica");
		
		echo "<center>";
		echo '<span class="subtituloPagina2">Parámetros de consulta</span>';
		echo "</center>";
		echo '<br><br>';

		//echo '<div style="border:2px solid yellow; width: 100%">';
		//echo '<div style="width: 100%">';
		
		$width_sel = " width: 95%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
		//------------TABLA DE PARAMETROS-------------
		echo '<table align="center">';
		echo "<tr>";
		echo '<td class="encabezadotabla" width="80px">Servicio</td>';
		//LISTA DE SERVICIOS
		echo '<td class="fila1" width="auto">';
		echo "<div align='center'>";
		echo "<select id='lista_servicios'   align='center' style='".$width_sel." margin:5px;'>";
		echo "<option></option>";
		foreach($servicios as $pos =>$servicio)
			echo '<option value="'.$servicio['codigo'].'">'.$servicio['nombre'].'</option>';
		echo '</select>';
		echo '</div>';
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td class="encabezadotabla">Patron</td>';
		//LISTA DE PATRONES
		echo '<td class="fila1">';
		echo "<div align='center'>";
		echo "<select id='lista_patrones'   align='center' style='".$width_sel." margin:5px;'>";
		echo "<option></option>";
		foreach($patrones as $pos =>$patron)
			echo '<option value="'.$patron['codigo'].'">'.$patron['nombre'].'</option>';
		echo '</select>';
		echo "<button id='boton_combinar_patron'>Combinar</button>";
		echo '</div>';

		echo "</td>";
		echo "</tr>";
		//FIN LISTA SERVICIOS
		
		//PATRON ADICIONAL
		echo "<tr id='fila_patron_adicional'>";
		echo '<td class="encabezadotabla">Combinar</td>';
		echo '<td class="fila1">';
		echo "<div align='center'>";
		echo "<select id='lista_patrones_adicional'   align='center' style='".$width_sel." margin:5px;'>";
		echo "<option></option>";
		//foreach($patrones as $pos =>$patron)
		//	echo '<option value="'.$patron['codigo'].'">'.$patron['nombre'].'</option>';
		echo '</select>';
		echo '</div>';
		echo "</td>";
		echo "</tr>";
		//FIN PATRON ADICIONAL
		echo "</table>";
		
		
		//------------FIN TABLA DE PARAMETROS-------------

		echo "<br><br><br>"; 
		
		//echo '<div class="enlinea" style="border:2px dashed blue; width: 65%" id="gran_contenedor">';
		echo '<div class="enlinea" style="width: 50%; display:none; margin-left:10%" id="gran_contenedor">';
		
		//LISTA CON ELEMENTOS A DESECHAR
		echo "<div id='caneca_basura' class='enlinea' style='border:2px dashed orange; width: 25%; height: 60px;'>";
		echo "<p align='center'><b>Arrastre aqui para eliminar</b></p>";
		echo "</div>";
	
		//FORMULARIO DEL MENU
		echo "<div>";
		echo "<table border=0 align='center' width='100%' id='tabla_menu'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'><font id='titulo_patron' size=5 color='#FFFF33'>Menu</font></td>";
		echo "</tr>";
		echo "<tr  class='encabezadotabla'>";
		echo "<td>&nbsp;</td>";
		echo "</tr>";

		$wclass='fila2';
		
		echo "<tr class='ui-state-hover' style='font-size:8pt;'>";
		echo "<td align='center'><font id='fila_servicio' size=4 color='#000000' >Desayuno</font></td>";
		echo "</tr>";
		
		//SE IMPRIME UNA FILA POR CADA CLASIFICACION
		foreach( $clasificaciones as $pos2 => $clasificacion ){
			echo "<tr class='".$wclass." trclasificacion".$clasificacion['codigo']."' >";
			echo "<td><b>".$clasificacion['nombre']."</b></td>";
			echo "</tr>";
			echo "<tr class='".$wclass."'>";
			echo "<td class='tdclasificacion".$clasificacion['codigo']."' >";
			echo "<ul class='lista_ordenable ulclasificacion".$clasificacion['codigo']."' style='width: 100%;'></ul>";
			echo "</td>";
			echo "</tr>";
		}
		
		echo "</table>";
		echo "</div>";
		
		echo "</div>";
		//------FIN FORMULARIO------

		//LISTA DE PRODUCTOS
		$class1 = "ui-state-barra";
		$class2= "ui-state-barra2";
		$heigh_div = 730;
		if(preg_match('/MSIE/i',$u_agent))
			$heigh_div = $heigh_div*1.4;
		//echo '<div class="enlinea" id="lista_productos" style="border:2px dashed blue; width: 33%; margin-left: 1%;">';
		echo '<div class="enlinea" id="lista_productos" style="width: 35%; margin-top:5%; margin-left: 1%; display:none; height: '.$heigh_div.'px; overflow:auto;">';
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
		
		//echo "</div>";//Gran contenedor
		
		echo '<br><br>';
		echo '<center>';
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		echo "<input type='button' id='boton_guardar_menu' value='Guardar'  >";
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
	.lista_ordenable li{
		/*width: 80px;*/ /*tambien hay que reducir el td*/
	}
	
	
	.botona{
		font-size:13px;
		font-family:Verdana,Helvetica;
		font-weight:bold;
		color:black;
		background:#C3D9FF;
		border:0px;
		width:180px;
		height:30px;
	}
	
	.botona:hover{
		background:#638BD5;
	}
	

	
	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>
<script>
	//Variable de estado que indica si se esta moviendo un producto
	var moviendo_global = false;
	
	
//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
		
		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});
		
		$("#fila_patron_adicional").hide();
		//Cuando cambie el select de patrones adicionales, lleva el codigo de los patrones a la variable oculta "patrones_elegidos"
		$("#lista_patrones_adicional").change(function(){
			var titulo_patron = "";
			var pat1 ="";
			if( $("#patrones_elegidos").val() != '')
				pat1 = $("#patrones_elegidos").val();
			else			
				pat1 = $("#lista_patrones").val();
			pat1 = pat1+","+$(this).val();
			var patron_adicional = $(this).val();
			$("#patrones_elegidos").val( pat1 );
			//Buscar el nombre del patron adicional en el select de patrones
			$("#lista_patrones option").each( function(){
				if( $(this).val() == patron_adicional ){
					$("#titulo_patron").append(" - "+ $(this).text() );
					return false;
				}
			});
			titulo_patron = $("#titulo_patron").text();		
			titulo_patron = titulo_patron.replace("Menu ","");
			option = '<option value="'+pat1+'" selected>'+titulo_patron+'</option>';
			$("#lista_patrones").append( option );
			$("#fila_patron_adicional").hide();
			$("#lista_patrones_adicional").html("<option value=''></option>");
			//Eliminar los productos en la tabla
			$("#tabla_menu .producto").remove();
			consultarMenu();
		});
		
		$("#boton_np").click(function(){
			crearProducto();
		});
		$("#cancelar_np").click(function(){
			$("#clasificacion_np option").eq(0).attr('selected',true);
			$("#descripcion_np").val('');
			$.unblockUI();
		});
		$("#boton_guardar_menu").hide().click(function(){
			guardarCambiosMenu();
		});		
		$("#lista_patrones").change(function() {
			$("#fila_patron_adicional").hide();
			$("#patrones_elegidos").val('');
			consultarMenu();
		});		
		$("#lista_servicios").change(function() {
			consultarMenu();
		});
		
		$("#boton_combinar_patron").button().hide().click(function(){
			mostrarPatronesCombinables();
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
				//console.log("RECEIVE");
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
				$(this).find('li').css("width","96%");
			},
			start: function( event, ui ) {
				//console.log("SALE!");
				var item = ui.item;
				item.css('width','75px'); //le doy un tamano fijo al li
			}
		}).disableSelection();
		
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
				$("ul").css("border","0");
				animarColor( 'caneca_basura' ); //cambio el color del div
            }
        });
		
		//AUTOCOMPLETAR
		var prods_array = new Array();
		//Selecciona la variable con las entidades y llena el arreglo para mostrarse en el input de "Entidad"
		var prods = $("#productos_json").val();
		datos = eval ( prods );		
		for( i in datos ){
			prods_array.push( datos[i] );
		}
		//Autocompletar para las entidades responsables, cuando seleccione uno llama a buscarcodigoentidad
        $( "#descripcion_np" ).autocomplete({
            source: prods_array,
			minLength : 3
        });
		// FIN AUTOCOMPLETAR
		
		
		
		$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
	});
	
	//Funcion que se llama cuando se presiona el boton "Combinar" que se encarga de consultar los patrones con los que se puede combinar el elegido
	//y crear las opciones en el input box con los esos patrones
	function mostrarPatronesCombinables(){
	
		$("#fila_patron_adicional").show();
		
		var codigoPatron = $("#patrones_elegidos").val();
		if( codigoPatron == '' )
			codigoPatron = $("#lista_patrones").val();
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('configuracion_menu.php', { wemp_pmla: wemp_pmla, action: "consultarPatronesComb", patron: codigoPatron, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$("#lista_patrones_adicional").html( data );
			});
	}

	function guardarCambiosMenu(){
		var datos = new Array();
		//Selecciona todos los productos VISIBLES, debido a que si agrego varias veces el mismo producto, el programa lo oculta 
		//porque no si lo elimina se produce un error en la lista sortable
		$("#tabla_menu .producto:visible").each( function(){
			dato = new Object();
			//dato.pr = $(this).val(); //codigo producto
			dato.pr = $(this).attr('codigo'); //codigo producto
			dato.pa = $(this).parents("td").attr("patron"); //patron
			datos.push( dato );
		});
		var datosJson = $.toJSON( datos ); //convertir el arreglo de objetos en una variable json
		var codigoPatron = $("#lista_patrones").val();
		var codigoServicio = $("#lista_servicios").val();
		
		if( $("#patrones_elegidos").val() != "")
			codigoPatron = $("#patrones_elegidos").val();
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('configuracion_menu.php', { wemp_pmla: wemp_pmla, action: "guardarCambiosMenu", datos: datosJson, patron: codigoPatron, servicio: codigoServicio, consultaAjax: aleatorio} ,
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
		
		$("#clasificacion_np").val("");
		$("#descripcion_np").val("");
		
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
			var class_tr = "trclasificacion"+$(this).attr('clasificacion');//obtengo la fila
			//$( "."+class_tr ).show();
			var class_ul = "ulclasificacion"+$(this).attr('clasificacion');//obtengo las listas dentro de cada td
			$( "."+class_ul ).css('border', "2px dashed blue");//resalto las listas, indicando donde puedo agregar el producto
		},function(){
			//Cuando el mouse salga sobre el producto
			if(moviendo_global)
				return;
			var class_tr = "trclasificacion"+$(this).attr('clasificacion');//obtengo la fila
			//$( "."+class_tr ).show();
			var class_ul = "ulclasificacion"+$(this).attr('clasificacion');//obtengo las listas dentro de cada td
			$( "."+class_ul ).css('border', "0");//quito el resalto de las listas
		});
	}
	
	//Consultar los productos que pertenecen al patron, para el servicio
	function consultarMenu(){
		var codigoPatron = $("#lista_patrones").val();
		var codigoServicio = $("#lista_servicios").val();
		var textoServicio = $("#lista_servicios option:selected").text();
		var textoPatron = $("#lista_patrones option:selected").text();
		/*if(  codigoServicio != "" ){
			$("#lista_patrones").focus();
		}*/
		if( codigoPatron == "" || codigoServicio == "" ){
			$("#gran_contenedor, #lista_productos, #boton_guardar_menu").hide( 'drop', {}, 500 );
			$("#patrones_elegidos").val('');
			return;
		}
		
		if( $("#patrones_elegidos").val() != ''){
			codigoPatron = $("#patrones_elegidos").val();
		}
		//Eliminar los productos en la tabla
		$("#tabla_menu .producto").remove();
		
		$("#boton_combinar_patron").show();
		
		$("#gran_contenedor, #lista_productos, #boton_guardar_menu").show( 'drop', {}, 500 );
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('configuracion_menu.php', { wemp_pmla: wemp_pmla, action: "consultarMenu", patron: codigoPatron, servicio: codigoServicio, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$("#titulo_patron").text("Menu "+textoPatron);
				$("#fila_servicio").html( "<b>"+textoServicio+"</b>");
				llenarFormularioMenu( data );	
			}, 'json');	
	}
	
	//Al consultar los productos de la patron-servicio desde el servidor, se llena la tabla minuta con dichos productos
	function llenarFormularioMenu( datos ){
		//pa:patron   pr:producto   cl:clasificacion
		var i=0;
		if( datos.length == 0){
			//alerta("No hay un menu creado para el servicio ");
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
			
			idtd = "tdclasificacion"+clasificacion; //Identifico el tr con la clasificacion del producto
			//Busco cual es el td que tiene el patron del producto
			$("."+idtd).find('ul').append( li ); //Agrego el producto a la lista ul dentro del td con el patron del producto
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