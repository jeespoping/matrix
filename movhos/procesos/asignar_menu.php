<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	asignacion de menu
 * Fecha		:	2013-02-26
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	El objetivo del programa es permitir asignar uno de los menu que existen en el sistema 
					a los pacientes que se encuentren hospitalizados.
 * Condiciones  :   A continuacion algunas de las condiciones importantes del programa.
					---Los Pacientes tipo POS no aparecen en la lista, ya que no se les puede asignar
					   carta menu
					---Solo aparecen los patrones que tienen configurado un menu
					---Cuando el paciente es un niño( menos de 120 meses ) el programa de dietas.php le asigna
					   por ejemplo el patron NORMAL (N), este programa le combina el patron pediatrico que le corresponde,
					   queda por ejemplo: N,P6.8 (Normal, Pediatrico 6 a 8 meses). NO SE VERIFICA QUE EXISTA UNA CARTA MENU
					   CON ESA COMBINACION, pues no se le puede mandar una carta menu NORMAL a un niño. 
 *********************************************************************************************************
 
 Actualizaciones:
  2013-05-22 (Frederick Aguirre S.) 
			Se cambia la forma en que se visualiza el mensaje cuando los productos que seleccione son para el dia siguiente.
			Se corrigen fallas en la edicion de los productos solicitados.
  2013-02-26 (Frederick Aguirre S.) 
			Se agrega la combinacion de patron pediatrico cuando el paciente tiene menos de 120 meses
  2013-01-10 (Frederick Aguirre S.) 
			Se cambia la forma en que se asigna el menu a los pacientes, mediante modificacion de interfaz y funciones
			
 **********************************************************************************************************/
 
$wactualiz = "2013-05-22";
 
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
	echo "<title>Asignacion de menu</title>";
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
	if( $action == "mostrarListaPacientes"){
		mostrarPacientesPiso( $_REQUEST['piso'], $_REQUEST['servicio'] );
	}else if( $action == "guardarMenuPaciente"){
		guardarMenuPaciente( $_REQUEST['historia'], $_REQUEST['ingreso'], $_REQUEST['cco'], $_REQUEST['ide'], $_REQUEST['patron'], $_REQUEST['servicio'], $_REQUEST['estado'] );
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************
	
	function mostrarPacientesPiso( $wcco, $wservicios ){
	
        global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$todosPatrones = consultarPatrones();
		
        //Selecciono todos los pacientes del servicio seleccionado
        $q = " SELECT habcod as habitacion, habhis as historia, habing as ingreso, CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente, "
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento,  ROUND((DATEDIFF(  now(),  pacnac ))/365*12,0) as edad_meses"
            ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000018, root_000036, root_000037 "
            ."  WHERE habcco  = '".$wcco."'"
            ."    AND habali != 'on' "            //Que no este para alistar
            ."    AND habdis != 'on' "            //Que no este disponible
            ."    AND habcod  = ubihac "
            ."    AND ubihis  = orihis "
            ."    AND ubiing  = oriing "
            ."    AND ubiald != 'on' "
            ."    AND ubiptr != 'on' "
            ."    AND ubisac  = '".$wcco."'"
            ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
            ."    AND oriced  = pacced "
            ."    AND oritid  = pactid "
            ."    AND habhis  = ubihis "
            ."    AND habing  = ubiing "
            ."  GROUP BY habcod, habhis, habing "
            ."  ORDER BY Habord, Habcod ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		//echo $q;
		
		$servicios_array = explode('-', $wservicios);
		
		foreach( $servicios_array as $wservicio ){
			$patrones_piso = array();
			$pacientes = array();
			$whabant = "";
			$nombreServicio = consultarNombreServicio( $wservicio );
			
			$hora_limite = consultarHoraLimiteServicio($wservicio);
			$hora_sistema = date('H:i:s');
			$visible = "none;";
			if( strtotime($hora_sistema) > strtotime($hora_limite) ){
				$visible = "block;";
			}
			echo "<div class='wrong' id='wrongservicio".$wservicio."' style='display: ".$visible."'>";
					echo "Los productos que solicite para el servicio ".$nombreServicio.",<br>serán programados para el día de mañana";
			echo "</div>";
			
			echo "<font size=5 color='#2A5DB0'><b>".$nombreServicio."</b></font>";
			
			if ($num > 0){
				mysql_data_seek($res,0);    
				while($row = mysql_fetch_assoc($res)) {
					$dato = array();
					$dato['habitacion'] = $row['habitacion'];
					$dato['historia'] = $row['historia'];
					$dato['ingreso'] = $row['ingreso'];
					$dato['paciente'] = $row['paciente'];
					$dato['nacimiento'] = $row['nacimiento'];
					$dato['tipo_identificacion'] = $row['tipo_identificacion'];
					$dato['numero_documento'] = $row['numero_documento'];
					
					//Si el paciente es POS y es mayor de diez años, no se le ofrece la carta menu
					if( pacienteEsPOS( $dato['historia'], $dato['ingreso'], $wservicio ) && $row['edad_meses'] > 120){
						continue;
					}
					$patt = consultarPatron( $dato['historia'], $dato['ingreso'], $wservicio, $wcco);
					
					//Se trata de un niño, hay que combinarle al patron que trae, el patron pediatrico que le corresponde
					//Se le concatena el patron pediatrico y NO SE COMPRUEBA SI EXISTE O NO UNA COMBINACION, dado que
					//a un niño no se le manda un menu de un adulto, o existe la combinacion pediatrica o no aparece en la lista
					if( $row['edad_meses'] <= 120 ){
						$patt_pediatrico = consultarPatronPediatrico( $row['edad_meses'] );
						if( empty( $patt ) == false && empty( $patt_pediatrico ) == false){
							$patt['codigo'].=",".$patt_pediatrico;
						}
					}
					
					//Consultar el nombre del patron
					if( empty( $patt ) == false){
						$varios_pat = explode( ",", $patt['codigo']);
						
						foreach ( $todosPatrones as $patr ){
							foreach ($varios_pat as $patelegido){
								if( trim($patelegido) == $patr['codigo'] ){
									if( isset( $patt['nombre'] ))
										$patt['nombre'] = $patt['nombre']."-".$patr['nombre'];
									else
										 $patt['nombre'] = $patr['nombre'];
								}
							}
						}
						if( isset( $patt['nombre'] ) == false){
							$patt['nombre'] = "";
						}
					}
					if( empty( $patt ) ){
						$patt['codigo'] = '';
						$patt['nombre'] = '';
					}
					$dato['patron'] = $patt['codigo'];
					
					if( in_array($dato['patron'], $patrones_piso ) == false )
						array_push( $patrones_piso, $patt );
						
					if ( array_key_exists( $dato['patron'], $pacientes) == false ) 
						$pacientes[ $dato['patron'] ] = array();

					array_push($pacientes[ $dato['patron'] ], $dato);
				}
			}else{
				echo "<br>No hay pacientes en el piso";
				return;
			}
			
			//Cuales patrones tienen menu
			$patrones_con_menu = consultarPatronesConMenu($wservicio);		
			
			//Los patrones que trajo del piso y que tengan un menu definido, son los que se dibujan
			$patrones_a_dibujar = array();			
			foreach ( $patrones_piso as $pos=>$patron ){			
				$existeInvertido = false;
				//COMPROBAR SI EL PATRON COMBINADO QUE TIENE EL MENU, ES IGUAL AL DEL PACIENTE, PERO EN DESORDEN. EJ:  BHGR,HGL  Y HGL,BHGR
				if( strrpos($patron['codigo'], ",") != '' ){
					foreach ( $patrones_con_menu as $patmenu ){
						$pats1 = explode(",", trim($patron['codigo']) );
						$pats2 = explode(",", trim($patmenu) );
						sort($pats2); sort($pats1);
						if( $pats1 == $pats2 ){
							$existeInvertido = true;
							break;
						}
					}
				}			
				if( (in_array( $patron['codigo'], $patrones_con_menu ) || $existeInvertido==true) && in_array( $patron, $patrones_a_dibujar ) == false){
					array_push( $patrones_a_dibujar, $patron );
				}
			}
			
			if( count( $patrones_a_dibujar ) == 0 ){
				echo "<br>No existe ningun menu creado para los patrones que hay en el piso";
				return;
			}

			//----------Calculando tamano del div
			$numero = 0; //Almacenara la mayor cantidad de pacientes en una dieta
			foreach($pacientes as $posz=>$listap){
				if( $numero < count( $listap ) )
					$numero = count( $listap );
			}
			$width = 45*$numero; //Cada checkbox necesita al menos un width de 35px
			//el td con las clasificaciones, necesita al menos 150
			//el tamano minimo es 600
			if( $width <= 450 )
				$width = "600px";
			else if($width > 450 && $width <= 950 ){
				$width = ($width+150)."px";
			}else if( $width > 950 )
				$width = "1100px; overflow-x:scroll;";
			//---------FIN Calculando tamano del div	
			
			echo "<div class='desplegables' style='width:".$width.";'>";
			foreach ( $patrones_a_dibujar as $pos=>$patron ){
				echo "<h3><b>* ".$patron['nombre']." *</b></h3>";
				echo "<div>";
				//Consultar los productos del menu
				$menu = consultarDatosMenu( $patron['codigo'], $wservicio );
				echo "<table id='wtabla".$wservicio."'>";
				echo "<tr class='encabezadotabla'>";
				echo "<td width='25%' align='center'>COMPONENTES</td>";
				echo "<td width='75%' align='center' colspan='".count($pacientes[ $patron['codigo'] ])."'>HABITACIONES</td>";
				echo "</tr>";
				echo "<tr class='encabezadotabla'>";
				echo "<td>&nbsp;</td>";
				foreach($pacientes[ $patron['codigo'] ] as $pox=>$paciente){
					//Construir el title
					$title = "<table>";
					$title.= "<tr><td class=encabezadotabla>Nombre</td><td class=fila2 align=center><b>".$paciente['paciente']."</b></td></tr>";
					$title.= "<tr><td class=encabezadotabla>Historia</td><td class=fila2 align=center>".$paciente['historia']."</td></tr>";
					$title.= "<tr><td class=encabezadotabla>Ingreso</td><td class=fila2 align=center>".$paciente['ingreso']."</td></tr>";					
					$title.= "</table>";
					echo "<td class='msg_tooltip' title='".$title."' align='center'>".$paciente['habitacion']."</td>";
				}
				echo "</tr>";
				
				$class = "fila1";
				
				foreach( $menu as $clasi=>$productos ){
					echo "<tr class='encabezadotabla'>";
					echo "<td>".$clasi."</td>";
					echo "<td colspan='".count($pacientes[ $patron['codigo'] ])."'>&nbsp;</td>";
					echo "</tr>";
					foreach($productos as $posx=>$producto){
						( $class == 'fila2' )? $class='fila1' : $class='fila2';
						echo "<tr>";
						echo "<td class='".$class."' align='left'><b>".$producto['de']."</b></td>";
						foreach($pacientes[ $patron['codigo'] ] as $pox=>&$paciente){
							//Consultar todos los productos pedidos de este menu para el paciente, solo si no se ha consultado antes
							if( isset( $paciente['pedidos'] ) == false){
								$paciente['pedidos'] = consultarDatosMenuPaciente( $paciente['patron'], $wservicio, $paciente['historia'], $paciente['ingreso'], $wcco );
							}
							$checked = "";
							//SI el checked del producto a dibujar para la historia ya lo solicito antes, ponerlo en checked
							if( in_array( $producto['ide'], $paciente['pedidos']) ){
								$checked = " checked=checked ";
							}								
							echo "<td class='".$class."' align='center'><input type='checkbox' ser='".$wservicio."' his='".$paciente['historia']."' ing='".$paciente['ingreso']."' pat='".$paciente['patron']."' ide='".$producto['ide']."' onclick='guardarProductoPaciente(this)' ".$checked." ></input></td>";
						}
						echo "</tr>";
					}
				}			
				echo "</table>";
				echo "</div>";
			}
			echo "</div><br><br>";	 //desplegables
		}
    }
	
	function consultarPatronPediatrico( $wedad_meses ){
		global $conex;
        global $wbasedato;
		
		$q = " SELECT Diecod as codigo"
			."   FROM ".$wbasedato."_000041 "
			."  WHERE Dieedi <= ".$wedad_meses
			."    AND Dieedf >= ".$wedad_meses
			."    AND Dieped = 'on'";
	
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = '';
        if ($num > 0){
			$row = mysql_fetch_assoc($res);
			$result = $row['codigo'];
		}
		return $result;
	
	}
	
	function consultarNombreServicio( $codServicio ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Sernom as nombre"
			."   FROM ".$wbasedato."_000076 "
			."  WHERE Sercod = '".$codServicio."'";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = '';
        if ($num > 0){
			$row = mysql_fetch_assoc($res);
			$result = $row['nombre'];
		}
		return $result;
	}
	
	//Retorna los productos que hacen parte de un patron y un servicio
	function consultarDatosMenu( $wcodPatron, $wcodservicio ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT A.id as ide, A.Menpat as pa, A.Menpro as pr, Clacod as cl, Clades as cldes, Prodes as de "
			."   FROM ".$wbasedato."_000147 A, ".$wbasedato."_000082, ".$wbasedato."_000083 "
			."  WHERE A.Menser = '".$wcodservicio."' "
			."    ANd A.Menpro = Procod "
			."    AND A.Menpat = '".$wcodPatron."'"
			."    AND Clacod = Procla "
			."    AND Claest = 'on' "
			."    AND Menest = 'on' "
			."    AND Proest = 'on' ";
						
			
		$pos = strrpos($wcodPatron, ",");	
		$pats = array();	
		//El menu es de patrones combinados
		if ($pos != ''){
			$pats = explode(",", $wcodPatron );
			$and = "";
			foreach( $pats as $pos=>$patron ){
				$and.= " AND  Menpat like '%".$patron."%' ";
			}
			if( $and != "" ){
				$q = " SELECT A.id as ide, A.Menpat as pa, A.Menpro as pr, Clacod as cl, Clades as cldes, Prodes as de "
					."   FROM ".$wbasedato."_000147 A, ".$wbasedato."_000082, ".$wbasedato."_000083  "
					."  WHERE A.Menser = '".$wcodservicio."' "
					."    ANd A.Menpro = Procod "
					."    AND Clacod = Procla "					
					. $and
					."    AND Claest = 'on' "
					."    AND Menest = 'on' "
					."    AND Proest = 'on' ";
			}
		}

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
         if ($num > 0 && ($pos == '')){
			while($row = mysql_fetch_assoc($res)){
				if ( array_key_exists( $row['cldes'], $result) == false ) 
					$result[ $row['cldes'] ] = array();
					
				array_push( $result[ $row['cldes'] ], $row ); 				
			}
		}else if( $num > 0 ){ //Para controlar que, buscando los productos de N,LC  no traiga los productos de N,LC,HS pues el query trae todos estos con el like por cada patron
			while($row = mysql_fetch_assoc($res)){
				$arreglo = explode(",", $row['pa'] );
				if( (count($arreglo) == count($pats))){
					if ( array_key_exists( $row['cldes'], $result) == false ) 
						$result[ $row['cldes'] ] = array();
						
					array_push( $result[ $row['cldes'] ], $row ); 	
				}
			}
		}
		return $result;
	}
	
	function consultarPatronesConMenu($wservicio){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$patrones = array();
		
		//CONSULTAR LA SOLICITUD DE DIETA PARA EL DIA DE HOY
		$q = " SELECT DISTINCT Menpat as patron "
			."   FROM ".$wbasedato."_000147 "
			."  WHERE Menser = '".$wservicio."' "
			."    AND Menest = 'on'";
			
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		
		if( $num > 0 ){
			while( $row = mysql_fetch_assoc($res) )
				array_push( $patrones, $row['patron'] );
		}
		
		return $patrones;
	}
		
	function consultarPatrones(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Diecod as codigo, Diedes as nombre "
				."   FROM ".$wbasedato."_000041 "
				."  WHERE Dieest = 'on' ";
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$result = array();
		if( $num > 0 ){
			while($row = mysql_fetch_assoc($res)){
				array_push( $result, $row);
			}
		}
		return $result;
				
	}
	
	//CONSULTA EL PATRON ASIGNADO CON EL PROGRAMA DE DIETAS.PHP PARA UNA HISTORIA, INGRESO EN UN PISO Y PARA UN SERVICIO
	function consultarPatron( $whis, $wing, $wservicio, $wcco ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
	
		$patron = array();
		
		//CONSULTAR LA SOLICITUD DE DIETA PARA EL DIA DE HOY
		$q = " SELECT Movdie as codigo "
			."   FROM ".$wbasedato."_000077 "
			."  WHERE Movhis = '".$whis."'"
			."	  AND Moving = '".$wing."'"
			."    AND Movcco = '".$wcco."' "
			."    AND Movser = '".$wservicio."'"
			."    AND Movind = 'N' "
			."    AND Movest = 'on' "
			." 	  AND Fecha_data = '".date('Y-m-d')."' ";
			
			
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		if ($num == 0 ){

			$q = " SELECT Movdie as codigo "
			."   FROM ".$wbasedato."_000077 "
			."  WHERE Movhis = '".$whis."'"
			."	  AND Moving = '".$wing."'"
			."    AND Movser = '".$wservicio."'"
			."    AND Movind = 'N' "
			."    AND Movest = 'on' "
			." 	  ORDER BY Fecha_data"
			."    LIMIT 1";
			
			$res = mysql_query($q, $conex);
			$num = mysql_num_rows($res);
		}
		if( $num > 0 ){
			$row = mysql_fetch_assoc($res);
			$patron = $row;
		}
		
		return $patron;
	}
	
	//PARA VERIFICAR SI UN PACIENTE ES POS
	function pacienteEsPOS( $whis, $wing, $wservicio='01' ){
	
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
	
		$q = " SELECT * "
			."   FROM ".$wbasedato."_000076, ".$wbasedato."_000016 "
			."  WHERE Inghis = '".$whis."' "
			."	  AND Inging = '".$wing."' "
			."    AND Ingtip != '' "
			."    AND Sertpo LIKE CONCAT('%', Ingtip , '%') "
			."    AND Sercod = '".$wservicio."'"
			."    AND Serest = 'on' ";
			
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 )
			return true;
		
		return false;
	}
		
	//Retorna una lista con los servicios disponibles ( Desayuno, Almuerzo... )
	function consultarListaServicios(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT CONCAT(Sercod,'-',Seraso) as codigo, Sernom as nombre"
			."   FROM ".$wbasedato."_000076 "
			."  WHERE Serest = 'on' "
			."    AND Seraso != '' "
			."    AND Seraso != '.' ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0){
			while($row = mysql_fetch_assoc($res))
				array_push($result, $row);
		}
		return $result;
	}
	
	//Retorna los productos que hacen parte de un patron y un servicio, diferenciando los elegidos para una historia-ingreso en un piso
	function consultarDatosMenuPaciente( $wcodPatron, $wcodservicio, $whis, $wing, $wcco ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		//El cco no se tiene en cuenta, debido a que un paciente solo puede tener un servicio al dia, sin importar el piso donde este (?)
	
		$condiciones = "";
		
		$hora_limite = consultarHoraLimiteServicio($wcodservicio);
		$hora_sistema = date('H:i:s');
		
		if( strtotime($hora_sistema) < strtotime($hora_limite) ){
			$condiciones = "     AND B.Pamfec = '".date("Y-m-d")."' ";
		}else{
			$condiciones =   "	AND B.Fecha_data = '".date("Y-m-d")."' "
							."  AND B.Hora_data > '".$hora_limite."'";
		}		
		
		$q = " SELECT A.id as ide "
			."   FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B, ".$wbasedato."_000082, ".$wbasedato."_000083 "
			."  WHERE A.Menser = '".$wcodservicio."' "
			."    ANd A.Menpro = Procod "
			."    AND A.Menpat = '".$wcodPatron."'"
			."    AND Pamhis = '".$whis."' "
			."    AND Paming = '".$wing."' "
			.$condiciones
			."    AND Clacod = Procla "
			."    AND Pamide = A.id"
			."    AND Menest = 'on' "
			."    AND Claest = 'on' "
			."    AND Pamest = 'on' "
			."    AND Proest = 'on' ";
			
		$pos = strrpos($wcodPatron, ",");	
		$pats = array();
		//El menu es de patrones combinados
		if ($pos != ''){
			$pats = explode(",", $wcodPatron );
			$and = "";
			foreach( $pats as $pos=>$patron ){
				$and.= " AND  Menpat like '%".$patron."%' ";
			}
			
			if( $and != "" ){
				$q = " SELECT A.id as ide, A.Menpat as pa "
					."   FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B, ".$wbasedato."_000082, ".$wbasedato."_000083 "
					."  WHERE A.Menser = '".$wcodservicio."' "
					."    ANd A.Menpro = Procod "
					.$and
					."    AND Pamhis = '".$whis."' "
					."    AND Paming = '".$wing."' "
					.$condiciones
					."    AND Clacod = Procla "
					."    AND Pamide = A.id"
					."    AND Menest = 'on' "
					."    AND Claest = 'on' "
					."    AND Pamest = 'on' "
					."    AND Proest = 'on' ";
			}
		}
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$result = array();
        if ($num > 0 && ($pos == '')){
			while($row = mysql_fetch_assoc($res)){
				array_push($result, $row['ide']);
			}
		}else if( $num>0){
			//Para controlar que, buscando los productos de N,LC  no traiga los productos de N,LC,HS pues el query trae todos estos con el like por cada patron
			while($row = mysql_fetch_assoc($res)){
				$arreglo = explode(",", $row['pa'] );
				if( (count($arreglo) == count($pats))){
					unset( $row['pa'] );
					array_push($result, $row['ide']);
				}
			}	
		}
		return $result;
	}
	
	//PENDIENTE, CUANDO CANCELE UN PRODUCTO PARA MAÑANA PROGRAMADO HOY, ME CANCELO EL PRODUCTO QUE TAMBIEN PEDI PARA HOY PROGRAMADO HOY Y YA FUE SERVIDO
	

	//Guarda en la bd los productos elegidos del menu (patron-servicio) para un paciente en un piso
	function guardarMenuPaciente( $whis, $wing, $wcco, $wide, $wpatron, $wservicio, $westado ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla; 
		
		$continuar = true;
		$actualizarEstado = false;
		$mensaje = "";

		$user_session = explode('-',$_SESSION['user']);
		$user_session = $user_session[1];
		
		$hora_limite = consultarHoraLimiteServicio($wservicio);
		$hora_sistema = date('H:i:s');
		
		$query = " ";
		
		//SI LA HORA ACTUAL ES MENOR QUE LA HORA LIMITE Y LA FECHA DE VIGENCIA ES HOY...
		if( strtotime($hora_sistema) < strtotime($hora_limite) ){
			$query = " SELECT Pamest as estado "
					 ." FROM ".$wbasedato."_000148 "							
					." WHERE Pamfec = '".date("Y-m-d")."' "
					."   AND Pamhis = '".$whis."'"
					."   AND Paming = '".$wing."'"			
					."   AND Pamide = ".$wide." ";
		}else{ //SI LA HORA ACTUAL ES MAYOR QUE LA HORA LIMITE Y LA FECHA DEL PEDIDO ES MAYOR QUE LA HORA LIMITE Y EL PEDIDO SE HIZO HOY...
			$query = " SELECT Pamest as estado "
					 ." FROM ".$wbasedato."_000148 "							
					." WHERE Fecha_data = '".date("Y-m-d")."' "
					."   AND Hora_data > '".$hora_limite."'"
					."   AND Pamhis = '".$whis."'"
					."   AND Paming = '".$wing."'"			
					."   AND Pamide = ".$wide." ";
		}
		
		$res = mysql_query($query,$conex); 
		$num = mysql_num_rows($res);
		
		if ($num > 0){
			$row = mysql_fetch_assoc($res);
			if( $row['estado'] == $westado )
				$continuar = false;
			else
				$actualizarEstado = true;
		}
		
		if( $continuar == false ){
			$ret['estado'] = "OK";
			$ret = json_encode( $ret );
			echo $ret;
			return;
		}
		$wfecha_vigencia = date('Y-m-d');
		
		if( strtotime($hora_sistema) > strtotime($hora_limite) ){
			$wfecha_vigencia = date("Y-m-d",strtotime("+1 day"));
			if( $westado == "on"){
				//$mensaje = "La solicitud se registra para el dia de MANANA \n Porque el servicio ya esta cerrado";
				$mensaje = "El servicio ya esta cerrado \n El producto sera solicitado para manana \n El programa eliminara los pedidos que ya se estan preparando";
			}else if( $actualizarEstado == false ){
				$mensaje = "La solicitud no se puede cancelar, porque el servicio ya esta cerrado \n El programa eliminara los pedidos que ya se estan preparando";
				$ret['mensaje2'] = $mensaje;
				$ret['estado'] = "OK";
				$ret = json_encode( $ret );
				echo $ret;
				return;
			}
		}

		if( $actualizarEstado == false ){
			$insert = "INSERT INTO  ".$wbasedato."_000148 "
									."	(Medico, "
									."	Fecha_data, "
									."	Hora_data, "
									."	Pamhis, "
									."	Paming, "
									."	Pamcco, "
									."	Pamide, "
									."	Pamfec, "
									."	Seguridad) "
							."    VALUES "
									."	('".$wbasedato."', "
									."	'".date("Y-m-d")."', "
									."	'".date("H:i:s")."', "
									."	'".$whis."', "
									."	'".$wing."', "
									."	'".$wcco."', "									
									."	'".$wide."', "
									."	'".$wfecha_vigencia."', "
									."	'C-".$user_session."')";

			$res = mysql_query($insert,$conex);
		}else{
		
			//Actualizar:
			//-Si la hora es permitida (programado para hoy o programado ayer para hoy), actualizar donde Pamfec sea hoy
			//-Si la hora se paso, (programado para mañana) actualizar donde fecha_data sea hoy y hora_data sea mayor a la hora limite
			$query_update = " UPDATE 	".$wbasedato."_000148 "
							."	 SET 	Pamest = '".$westado."'"
							." WHERE 	Pamhis =  '".$whis."'"
							."   AND    Paming =  '".$wing."'"
							."   AND    Pamide = ".$wide." ";
							
			if( strtotime($hora_sistema) > strtotime($hora_limite) ){
				$query_update.= " AND Fecha_data = '".date("Y-m-d")."' ";
				$query_update.= " AND Hora_data > '".$hora_limite."' ";
			}else{
				$query_update.= " AND Pamfec = '".date("Y-m-d")."' ";
			}
			$res = mysql_query($query_update,$conex); 
		}
		
		//cancelarPedidoDieta($whis, $wing, $wcco, $wpatron, $wservicio);
		if( $mensaje != "" )
			$ret['mensaje'] = $mensaje;
		$ret['estado'] = "OK";
		$ret = json_encode( $ret );
		echo $ret;
	}
	
	function consultarHoraLimiteServicio( $wservicio ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla; 
		
		$query = "SELECT Serhfi as hora "
				 ." FROM ".$wbasedato."_000076 "							
				." WHERE Sercod = '".$wservicio."' ";
							
		$res = mysql_query($query,$conex); 
		$num = mysql_num_rows($res);
		
		$hora = "";
		if ($num > 0){
			$row = mysql_fetch_assoc($res);
			$hora = $row['hora'];
		}
		return $hora;
	}
	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		
		$servicios = consultarListaServicios();
		$wccos = consultaCentrosCostos("ccohos");
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		encabezado("ASIGNACION DE MENU", $wactualiz, "clinica");
		
		echo "<center>";
		echo '<span class="subtituloPagina2">Parámetros de consulta</span>';
		echo "</center>";
		echo '<br><br>';

		echo '<div style="width: 100%">';
		
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
		echo '<td class="encabezadotabla">Piso</td>';
		//LISTA DE PISOS
		echo '<td class="fila1">';
		echo "<div align='center'>";
		echo "<select id='lista_pisos'   align='center' style='".$width_sel." margin:5px;'>";
		echo "<option></option>";
		foreach ($wccos as $centroCostos)
			echo "<option value='".$centroCostos->codigo."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
		echo '</select>';
		echo '</div>';
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		//FIN LISTA SERVICIOS
		
		//------------FIN TABLA DE PARAMETROS-------------

		echo "<br><br><br>"; 
		
		//---DIV PRINCIPAL

		//LISTA DE PACIENTES
		echo '<div id="resultados_lista" align="center"></div>';
		echo "<br><br>";

		//------FIN FORMULARIO------
		echo "</div>";//Gran contenedor
		echo '<center>';
		
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
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
	}
?>
<style>
	.lista_ordenable {
		list-style-type: none; margin: 0; padding: 0; display:inline-block; vertical-align:top; 
		font-size: 9pt;
		font-weight: normal; 
	}
    .lista_ordenable li { margin: 0 3px 3px 0; padding: 0.4em; padding-left: 1.5em; font-size: 1.2em; }
    .lista_ordenable li span { position: absolute; margin-left: -1.3em; }
	
	div.wrong
	{
		background-color: white;
		color: #e34848;
		text-align: center; 	
		font-weight: bold;
		font-size: medium;
		width: 550px;
	}

	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>
<script>
	//Variable de estado que indica si se esta moviendo un producto
	var moviendo_global = false;
	var datos_paciente = new Object();
	var mostrandoMenu = false;
	
//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});
		
		$("#lista_servicios, #lista_pisos").change(function() {
			realizarConsultaLista();
		});
		
		$("#boton_guardar_menu").hide().click(function(){
			guardarMenuPaciente();
		});
		
	});
	
	//Funcion que se activa cuando se presiona el enlace "retornar"
	function restablecer_pagina(){
		//Si esta visible la tabla de menu...
		$("#lista_pisos option").eq(0).attr('selected',true); //llevar la opcion 0 a la lista de pisos
		$("#resultados_lista").hide( 'drop', {}, 500 ); //esconder lista de pacientes
		$("#enlace_retornar").hide(); //esconder enlace retornar
	}
	
	//funcion que luego de elegir el centro de costos, me trae los pacientes que se encuentran en el
	function realizarConsultaLista(){
	
		var wemp_pmla = $("#wemp_pmla").val();
		var piso = $("#lista_pisos").val();
		var codigoServicio = $("#lista_servicios").val();
		
		if( codigoServicio != "" ){
			$("#lista_pisos").focus();
		}
		if( codigoServicio == "" || piso == "" ){
			$("#resultados_lista").hide( 'drop', {}, 500 );
			return;
		}
		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		$("#enlace_retornar").fadeIn('slow');

		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		$("#piso_elegido").val(piso);	
		//Realiza el llamado ajax con los parametros de busqueda
		$.get('asignar_menu.php', { wemp_pmla: wemp_pmla, action: "mostrarListaPacientes", piso: piso, servicio: codigoServicio, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$('#resultados_lista').html(data);
				$("#resultados_lista").show( 'drop', {}, 500 );
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$( ".desplegables" ).accordion({
					collapsible: true,
					active:false,
					heightStyle: "content",
					icons: null
				});
			});
	}	
	
	function guardarProductoPaciente( elemento ){
		var estado = 'on';
		ele = jQuery(elemento);
		if( ele.is(':checked') == false){
			estado = 'off';
		}
		var historia = ele.attr('his');
		var ingreso = ele.attr('ing');
		var codigoPatron = ele.attr('pat');
		var codIde = ele.attr('ide');		
		//var codigoServicio = $("#lista_servicios").val();
		var codigoServicio = ele.attr('ser');
		var piso = $("#lista_pisos").val();
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('asignar_menu.php', { wemp_pmla: wemp_pmla, action: "guardarMenuPaciente", servicio: codigoServicio, patron: codigoPatron, historia: historia, ingreso: ingreso, estado: estado, ide: codIde, cco: piso, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				if( isJson(data) ){
					json = eval('(' + data + ')');
					if( json.mensaje != undefined ){ //Cuando llega un mensaje, es porque la solicitud sera para el dia de mañana
						var wrong = $("#wrongservicio"+codigoServicio);
						if( wrong.is(':visible') ){
							programarWrongBlink( codigoServicio );
						}else{
							wrong.show('drop', {}, 500);
							programarWrongBlink( codigoServicio );
							alert( json.mensaje );
							//location.reload();
							//Quitar todos los checked que corresponden a pedidos pasados
							$("#wtabla"+codigoServicio).find(':checkbox').attr('checked',false);
							var valor = true;
							if( estado == 'off' ) valor = false;
							ele.attr('checked', valor );
						}
					}else if( json.mensaje2 != undefined ){ //Cuando llega un mensaje2, es porque la solicitud no se puede cancelar
						alert( json.mensaje2 );
						wrong.show('drop', {}, 500);
						//location.reload();	
						//Quitar todos los checked que corresponden a pedidos pasados
						$("#wtabla"+codigoServicio).find(':checkbox').attr('checked',false);
						var valor = true;
						if( estado == 'off' ) valor = false;
						ele.attr('checked', valor );
					}
				}else{
					alerta("Error");
				}
			});
	}
	
	function programarWrongBlink( codigoServicio ){
		var wrong = $("#wrongservicio"+codigoServicio);
		wrong.css("background-color","#FFFFCC");
		wrong.effect("pulsate", {}, 5000, function(){
			wrong.css("background-color","#ffffff");
		});
	}
	
	function isJson(value) {
		try {
			eval('(' + value + ')');
			return true;
		} catch (ex) {
			return false;
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