<?php
include_once("conex.php");
	
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

header('Content-type: text/html;charset=ISO-8859-1');

if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";
	echo "<title>MONITOR SERVICIO DE ALIMENTACION</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script type="text/javascript" src="../../../include/movhos/mensajeriaDietas.js"></script>';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/toJson.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.easyAccordion.js" type="text/javascript"></script>';
}

include_once("root/magenta.php");
include_once("root/comun.php");
include_once("movhos/movhos.inc.php");

$conex = obtenerConexionBD("matrix");



global $array_alertas;

$array_alertas = array();

$wactualiz="Marzo 16 de 2022";
	
$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');  
$fecha_inicial_minutas = consultarAliasPorAplicacion($conex, $wemp_pmla, "fecha_inicial_minutas");

//Actualiza la tabla de auditoria cuando visualizan una nueva alerta, 													
//para marcarla como ya leida y asi no volverla a mostrar en las alertas
if(isset($consultaAjax) && $consultaAjax=='actualizarLog')
{
	

	

	
	$leyo_id=explode("|",$id_alertas);
	foreach($leyo_id as $valor){
		if ($valor!='' && $valor!=' '){
			$q=" UPDATE ".$wbasedato."_000078 
				   SET Audfle = '".date("Y-m-d")."',
				       Audhle = '".date("H:i:s")."',
					   Audule = '".$usuario."' 
				 WHERE id 	  = '".$valor."'";
			$resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}
	}

	$chain = '(';
	foreach( $leyo_id as $valor ){
		$chain.=$valor.",";
	}
	$chain = substr_replace($chain,"",-2);
	$chain.= ")";
	
	$q=" SELECT A.audacc as tipo, Estcol as color "
	  ."   FROM ".$wbasedato."_000078 A, ".$wbasedato."_000129 "
	  ."  WHERE A.id IN ".$chain.""
	  ."    AND Estdes = A.audacc ";

	$res= mysql_query($q,$conex);
	
	$result=array();
	while($row = mysql_fetch_assoc($res)){
		array_push($result, $row);
	}
	echo json_encode($result);
	return;
}
  /***********************************************
   *      MONITOR SERVICIO DE ALIMENTACION       *         *
   ***********************************************/
    
//=========================================================================================================================================\\
//                  MONITOR SERVICIO DE ALIMENTACION 
//=========================================================================================================================================\\
//	DESCRIPCION:	Con este programa se monitorea todos los Pedidos, Modificacion y Cancelaciones de los servicios que de 
// 					alimentacion se requieran desde el servicio de hospitalizacion de la clinica.
//	AUTOR: 			Jerson Trujillo 
//=========================================================================================================================================\\
//                  								ACTUALIZACIONES                                                                                                                          \\
/*=========================================================================================================================================\\
//Abril 17 de 2020: 	(Edwin MG)
						Se muestra el diagnóstico en la función mostrar_detalle si el parametro mostrarDiagnósticoMonitorDietas en root_000051
						Esta en on.
//Octubre 26 de 201: 	(Jonatan Lopez)
						Se corrige la actualizacion hora de leido (Audhle) de la tabla movhos_000078 tenia el h:i:s y es H:i:s la H mayúscula
						guarda hora militar.
//Agosto 28 de 2017:	(Jonatan Lopez)
						Se agrega base64_decode cuando se muestra el detalle de los pacientes en el resumen de productos por patron.
//Abril 26 de 2017:		(Jonatan Lopez)
						Se agrega en la pantalla la hora de la ultima actualizacion del monitor ademas de un boton para recargar la pagina.
//Agosto 13 de 2014    	(Jonatan Lopez)
						Se corrige la impresion de los textos en las observaciones de enfermeria y observaciones de la nutricionista.
						Se agregan colores en los dias de estancia, dependiendo de un rango definido por la aplicacion "colores_dias_estancia", en la tabla 
						root_000051.
//Abril 10 de 2014: 	(Jonatan Lopez)
						Se controla que no aparezca la nutricionista, el patron asociado a DSN y la observacion DSN cuando el patron sea 
						diferente a DSN.
//Noviembre 26 de 2013: (Jonatan Lopez)
						Se reemplaza el uso del tag <blink> por una funcion jquery que cumple con la misma funcion, esto para que las alertas
						parpadeen el todos los navegadores.
//Mayo 05 de 2013:		(Frederick Aguirre)
						Se muestra el nombre del nutricionista bajo la dieta "movnut"
//Abril 22 de 2013: 	(Frederick Aguirre)
						Se cambia el orden en wdetalle, cantidad-producto, se reemplazan caracteres especiales por errores en el json
//Abril 12 de 2013: 	(Frederick Aguirre)
						Se crea el acordion para las cuatro tablas (Resumen patron-piso, patrones individuales, carta menu y componentes).
						Tabla de carta menu y componentes se cargan (llamado ajax) si activan el acordion correspondiente.
						Se concatena el campo movdsn cuando tiene el patron DSN.
//Marzo 27 de 2013: 	(Frederick Aguirre)
						Cuando se quieren ver los pacientes del patron-piso en RESUMEN POR CARTA MENU se crea una fila adicional,
						antes de mostraba una ventana modal.
						Se crea y muestra el memo: observacion DSN.
						La lista de productos se muestra en accordion por piso.					
//Febrero 28 de 2013:  	(Frederick Aguirre) 
						Se adapta el programa para los patrones pediatricos, es decir, si es un niño se le combina el patron que le fue 
						asignado con el pediatrico que le corresponda
//Febrero 04 de 2013:  	(Frederick Aguirre) 
						Se reestructura el codigo para permitir llamados ajax.
 						"Mostrar detalle", funcion llamada al presionar un piso, o piso patron, es llamada mediante ajax,
						evitando que imprima informacion que puede no se consulte.
						Se agregan dos nuevas tablas, una con los pedidos de "carta menu" y otra con los componentes a preparar.
//Junio 07 de 2012:	 	(Jerson Trujillo)
						Se adapto el programa para mostrar primeramente los patrones agrupados por centro de costos, y cuando el usuario
						de clik en alguno de ellos visualizar el detalle.                                                                                                                                           
						Se realizaron cambios en la logica del programa para permitir visualizar el detalle en una ventana modal jquery. 
						Se grego funcionalidad para el manejo del chat de mensajeria. 
						Se agrego funcionalidad para manejar el sistema de alertas.                					                                                                                                   
//=========================================================================================================================================*/
	               
$wfecha=date("Y-m-d");  
$whora = (string)date("H:i:s");
  
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if($action=="mostrarDetallePiso"){
		mostrar_detalle( @$_REQUEST['nom_cco'], '', '', @$_REQUEST['arr_patrones'], @$_REQUEST['main_pacientes'], @$_REQUEST['filtrar'], @$_REQUEST['pk_wccocod'], @$_REQUEST['increment'], @$_REQUEST['nom_servicio'], @$_REQUEST['orden_habitaciones'], @$_REQUEST['wfec_i'], @$_REQUEST['cco_detalle']);
	}else if( $action == 'mostrarDetallePatronPiso'){
		mostrar_detalle(  @$_REQUEST['nom_cco'], @$_REQUEST['pk_nom_patron'], @$_REQUEST['valor2'], @$_REQUEST['arr_patrones'], @$_REQUEST['main_pacientes'], @$_REQUEST['filtrar'], @$_REQUEST['pk_wccocod'], @$_REQUEST['increment'], @$_REQUEST['nom_servicio'], @$_REQUEST['orden_habitaciones'], @$_REQUEST['wfec_i'], @$_REQUEST['cco_detalle']);
	}else if($action == 'consultarPacientesMenu' ){
		consultarPacientesMenu( $_REQUEST['fecha'], $_REQUEST['patron'], $_REQUEST['servicio'], $_REQUEST['cco'], $_REQUEST['minuta'] );
	}else if( $action == 'mostrarMonitor'){
		//De entrada solamente se carga la tabla de resumen por patrones y centros de costo, y la tabla de resumen patrones individuales
		
		$minutaElegida = consultarMinutaParaElDia( $_REQUEST['wfec_i'] );
		//echo '<div style="float:left; margin-left:10%;"><font size=3>Minuta '.$minutaElegida.'</font></div>';
		//echo "<br>";
		//Los div's para el acordion se imprimen dentro de la funcion
		imprimirTablaPedidos($_REQUEST['wcco'], $_REQUEST['wser'], $_REQUEST['wfec_i']);

		$cant = haySolicitudesCartaMenu($_REQUEST['wfec_i'], $_REQUEST['wser'],$_REQUEST['wcco']);
		if( $cant > 0 ){
			//Se imprimen los div's para mostrar el acordion, aunque al principio este vacio
			echo "<div class='desplegables' style='width:95%;'>";
			echo "<h3><b>* RESUMEN POR CARTA MENU *</b></h3>";
			echo "<div>";
			echo "<div id='tabla_resumen_carta_menu' class='contenedor_del_llamado' accion='mostrarTablaCartaMenu'>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
		}
		$canti = hayProductosParaMostrar($_REQUEST['wfec_i'], $_REQUEST['wser'],$_REQUEST['wcco'], $minutaElegida);
		if( $canti > 0 ){
			//Se imprimen los div's para mostrar el acordion, aunque al principio este vacio
			echo "<div class='desplegables' style='width:95%;'>";
			echo "<h3><b>* RESUMEN POR COMPONENTES *</b></h3>";
			echo "<div>";
			echo "<div id='tabla_resumen_componentes' class='contenedor_del_llamado' accion='mostrarTablaComponentes' style='width:1290;'>";
			echo "</div>";
			echo "</div>";
			echo "</div>";
		}
	}else if( $action == 'mostrarTablaCartaMenu'){
		imprimirTablaDePacientesConMenu($_REQUEST['wfec_i'], $_REQUEST['wser'],$_REQUEST['wcco']);
	}else if( $action == 'mostrarTablaComponentes'){
		imprimirTablaDeProductos($_REQUEST['wfec_i'], $_REQUEST['wser'],$_REQUEST['wcco']);
	}
	return;
}
	//=====================================================================================================================================================================     
	// F U N C I O N E S
	//=====================================================================================================================================================================
	  
	//Retorna el numero de solicitudes de carta menu para una fecha, un servicio y un centro de costos
	function haySolicitudesCartaMenu( $wfecha, $wservicio, $wcco ){
		global $wbasedato;
		global $conex;
		
		$ccodatos = explode("-",$wcco);
		$wcco=trim($ccodatos[0]);
		if( $wcco == '%' )
			$wcco = "";
		
		$and ="";
		if( $wcco != "")
			$and = "   AND Pamcco = '".$wcco."' ";
		
		$q = "SELECT count(*) "
		    ."  FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B "
			." WHERE B.Pamfec = '".$wfecha."' "
			."   AND Pamide = A.id "
			.$and
			."   AND Menser = '".$wservicio."' "
			."   AND Pamest = 'on' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $rownp=mysql_fetch_array($res);

        return $rownp[0];
	}
	
	//Retorna el numero de solicitudes de productos para una fecha, un servicio, un centro de costos y un codigo de minuta
	function hayProductosParaMostrar( $wfecha, $wservicio, $wcco, $minutaElegida){
		global $wbasedato;
		global $conex;
		
		$ccodatos = explode("-",$wcco);
		$wcco=trim($ccodatos[0]);
		if( $wcco == '%' )
			$wcco = "";
		$and ="";
		if( $wcco != "")
			$and = "	  AND Movcco='".$wcco."' ";
			
		//BUSCO LOS CENTROS DE COSTO QUE TIENEN PEDIDO DE DIETA
		$qcco = " SELECT count(*) "
				."	 FROM ".$wbasedato."_000077 A, ".$wbasedato."_000146 B "
				."	WHERE A.Fecha_data='".$wfecha."' "
				."	  AND Movser='".$wservicio."' "
				.$and		
				."	  AND Movest='on' "
				."    AND Mspmin = '".$minutaElegida."'"
				."    AND Msppat = Movdie "
				."    AND Movser = Mspser "
				."    AND Mspest = 'on' ";
		$res = mysql_query($qcco,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$rownp=mysql_fetch_array($res);
        return $rownp[0]; 
	}
	
	//Retorna el resulset con todas las solicitudes de dietas e informacion relevante
	function query_principal($wcco, $wser, $sCodigoSede = NULL){
		global $wfec_i;
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		$sFiltroSede='';
	
		if(isset($wemp_pmla) && !empty($wemp_pmla))
		{
			$estadosede=consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
		
			if($estadosede=='on')
			{
				$codigoSede = (is_null($sCodigoSede)) ? consultarsedeFiltro() : $sCodigoSede;
				$sFiltroSede = (isset($codigoSede) && ($codigoSede !='')) ? " AND Ccosed = '{$codigoSede}' " : "";
			}
		}
		
		$wcco=trim($wcco);
		if( $wcco == '%' )
			$wcco = "";
		$and ="";
		if( $wcco != "")
			$and = "	  AND movcco='".$wcco."' ";
		
		$q= " SELECT dieord, movcco, movser, movhis, moving, movdie, movpam, movcan, movhab, movpco, movest, movobs, movods, movint, movdsn, movnut, movpqu, movimp, diecod, diedes, Cconom, pacno1, pacno2,   "
			." pacap1, pacap2, pacced, pactid, pacnac, ubihan, ubiptr, ubihac, ubisac, ubisan, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0) as dias "
			." FROM ".$wbasedato."_000077, ".$wbasedato."_000041, ".$wbasedato."_000011, root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016  "
			." WHERE movfec = '".$wfec_i."'"
			//." AND movcco LIKE '%".trim($wcco)."%' "
			.$and
			." AND movser = '".$wser."' "
			." AND movpco = diecod "
			." AND movcco = Ccocod"
			." AND movhis = orihis"
			." AND moving = oriing"
			." AND oriori = '".$wemp_pmla."'"
			." AND oriced = pacced "
			." AND oritid = pactid"
			." AND movhis = ubihis "
			." AND moving = ubiing "
			." AND movhis  = inghis "
			." AND moving  = inging "
			." {$sFiltroSede} "
			." UNION "	//Este union es para mostrar los pacientes que tiene observaciones e intolerancias pero que no tienen dietas programadas 
			." SELECT '', movcco, movser, movhis, moving, movdie, movpam, movcan, movhab, movpco, movest, movobs, movods, movint, movdsn, movnut, '','','', '', '', '', '', "
			." '', '', '', '', '', '', '', '', '', '', '', '' "
			." FROM ".$wbasedato."_000077 "
			." WHERE movfec = '".$wfec_i."'"
			//." AND movcco LIKE '%".trim($wcco)."%' "
			.$and
			." AND movser = '".$wser."' "
			." AND movdie = '' "
			." ORDER BY dieord, movcco, movser "
			."";
        $resultado = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        return $resultado;
    }
	  
	//FUNCION DE ALERTAS
	function guardar_alertas($cco, $accion, $leido, $whis, &$main_pacientes, $id){
		global $array_alertas;
		
		if ($leido =='0000-00-00' || $leido==NULL)// si no se ha leido
		{
			if(!isset($array_alertas[$cco][$accion]))
				$array_alertas[$cco][$accion]=1; 	//este array me permite almacenar el numero de las alertas sin leer
			else
				$array_alertas[$cco][$accion]++;
			
			if (!isset($main_pacientes[$whis]['alertas']))
				$main_pacientes[$whis]['alertas']=$id; //este nuevo campo (alertas) del array principal ($main_pacientes), es para saber a cuales historias hacerles el blink cuando se abra el detalle
		}		
	}
  
  //Funcion que maneja el sistema de mensajeria (chat)
	function mensajeria($id, $wcco)
	{
		echo "<INPUT type='hidden' id='mensajeriaPrograma' value='cpa'>";
		 
		echo "<table style='width:80%;font-size:10pt' align='center'>";
		echo "<tr><td class='encabezadotabla' align='center' colspan='3'>Mensajer&iacute;a Dietas</td></tr>";
		echo "<tr>";
		//Area para escribir
		echo "<td style='width:45%;' rowspan='2'>";
		// echo "<textarea id='mensajeriaKardex' onKeyPress='return validarEntradaAlfabetica(event);' style='width:100%;height:80px'></textarea>";
		echo "<textarea id='mensajeriaKardex".$id."' style='width:100%;height:80px'></textarea>";
		echo "</td>";
		//Boton Enviar mensaje
		echo "<td align='center' style='width:10%'>";
		echo "<input type='button' onClick='enviandoMensaje(".$id.",".$wcco.")' value='Enviar' style='width:100px'>";
		echo "</td>";
		//Mensajes
		echo "<td style='width:45%' rowspan='2'>";
		echo "<div id='historicoMensajeria".$id."' style='overflow:auto;font-size:10pt;height:80px'>";
		echo "</div>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align='center'><b>Mensajes sin leer: </b><div id='sinLeer".$id."'></div></td>";
		echo "</tr>";
		echo "</table>";
	}
	
	function resumenXproductos($cco_detalle, $pk_wccocod, $filtrar, $pk_nom_patron )
	{
		if (isset($cco_detalle))
		{	
			echo "<td align=center>";//$cco_detalle[$wccocod][$patron_principal][$nombre_producto]
				foreach ($cco_detalle  as $clave_nom_cco => $array_patro_pro)
				{
					if($clave_nom_cco==$pk_wccocod)
					{
						$ya_pinto_tabla='no';
						$wclass4="fila1";
						foreach ($array_patro_pro as $nom_patro_pro => $array_productos)
						{
							$pintar_resumen='no';
							if ($filtrar=='no')
								$pintar_resumen='si';
							elseif($nom_patro_pro==$pk_nom_patron)
								$pintar_resumen='si';
							
							if ($pintar_resumen=='si')
							{
								if($ya_pinto_tabla=='no')
								{
									echo "<table >";
									echo "<tr align=center class='encabezadoTabla'><td colspan=2>Resumen x Productos</td></tr>";
									echo "<tr align=center class='encabezadoTabla'><td>Nombre</td><td>Cantidad</td></tr>";
								}
								$ya_pinto_tabla='si';
								foreach ($array_productos as $nom_producto => $cantidad_prod)
								{
									if ($wclass4=="fila1")
										$wclass4="fila2";
									else
										$wclass4="fila1";
									echo '<tr class="'.$wclass4.'" align=center><td>'.$nom_producto.'</td>';
									echo '<td>'.$cantidad_prod.'</td></tr>';
								}
							}
						}
						if($ya_pinto_tabla=='si')
						{
							echo "</table><br>";
						}	
					}
				}
			echo "</td>";
		}
	}
	
	function convenciones()
	{
		
		global $conex;
		global $wemp_pmla;
		
	
		echo "
		<table width=100% height=100% border=1 align=center class=fila1>        
			<caption class=fila2><b>CONVENCIONES</b></caption>
			<tr><td align=center class='Fila1'><font size=1><b>&nbsp PEDIDO</b></font></td><td align=center class='Fila2'><font size=1><b>&nbsp PEDIDO</b></font></td><td align=center bgcolor='E9C2A6'><font size=1><b>P O S</b></font></td><td align=center bgcolor='#FA5858'><font size=1><b>MEDIA <BR> PORCIÓN</b></font></td></tr>       
			<tr><td align=center bgcolor='FF7F00'><font size=1><b>SERVICIO INDIVIDUAL</b></font></td><td align=center bgcolor='3299CC'><font size=1><b>TRASLADADO</b></font></td><td align=center bgcolor='FFFF99' colspan=2><font size=1><b>&nbsp EN PROCESO DE ALTA</b></font></td></tr> 
			<tr><td align=center bgcolor='FFCC00'><font size=1><b>CANCELADO</b></font></td><td bgcolor='007FFF' align=center><font size=1><b>MODIFICADO</b></font></td><td bgcolor='70DB93' align=center colspan=2 width=40%><font size=1><b>&nbsp A D I C I O N</b></font></td></tr>
		</table>
		";
		
		//Se consulta la configuracion de los rangos de edades para mostrarle el fondo del color respectivo.
		$wcolores_estancia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'colores_dias_estancia');								
		$wrangos_col_estancia_aux = explode(";",$wcolores_estancia);
		
		echo "<table>";
		echo "<tr class=fila2>";
		echo "<td align=center colspan='".count($wrangos_col_estancia_aux)."'><b>DIAS DE ESTANCIA</b></td>";
		echo "</tr>";
		echo "<tr>";
		
		foreach($wrangos_col_estancia_aux as $key => $value){
			
			//Separa cada dato de la configuracion de rangos de edad.
			//$wdatos_rangos[0] = rango inicial
			//$wdatos_rangos[1] = rango final
			//$wdatos_rangos[2] = color asociado
			$wdatos_rangos = explode("-", $value);			
			$rango = "style='background: linear-gradient(70deg, ".$wdatos_rangos[2].", white);font-size:10pt;'";
			echo "<td $rango><br><b>".$wdatos_rangos[0]." - ".$wdatos_rangos[1]." días</b><br>&nbsp;</td>";
		
		}
		
		echo "</tr>";
		echo "</table>";
	}
	
  
    //funcion que pinta el detalle de los pacientes, en una ventana emergente en jquery
    function mostrar_detalle($nom_cco, $pk_nom_patron='', $valor2='', $arr_patrones, $main_pacientes, $filtrar, $pk_wccocod, $increment , $wser_pac, $orden_habitaciones, $wfec_i, $cco_detalle)
	{
		global $whora;
		global $wemp_pmla;
		global $conex;
		
		$arr_patrones = str_replace("\\", "", $arr_patrones);
		$arr_patrones = json_decode( $arr_patrones, true );
		
		$cco_detalle = str_replace("\\", "", $cco_detalle);
		$cco_detalle = json_decode( $cco_detalle, true );
		
		$main_pacientes = str_replace("\\", "", $main_pacientes);
		$main_pacientes = json_decode( $main_pacientes, true );
		
		$orden_habitaciones = str_replace("\\", "", $orden_habitaciones);
		$orden_habitaciones = json_decode( $orden_habitaciones, true );
		
		$wdatos_rol = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ValidarDsnNutricion');
		$winf_nutricion_dsn = explode("-", $wdatos_rol);
		
		$verDiagnostico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'mostrarDiagnósticoMonitorDietas');
		$verDiagnostico = strtolower( $verDiagnostico ) == 'on';
		
		$id_alertas = "";		
		//echo "<BR>LLEGAN LOS DATOS: --nom_cco ".$nom_cco." --pk_nom_patron ".$pk_nom_patron." --valor2 ".$valor2." --arr_patrones ".$arr_patrones." --main_pacientes ".$main_pacientes." --filtrar ".$filtrar." --pk_wccocod ".$pk_wccocod." --increment ".$increment." --wser_pac ".$wser_pac." --orden_habitaciones ".$orden_habitaciones." --wfec_i".$wfec_i." --cco_detalle".$cco_detalle;
				
		if (isset($cco_detalle))
				$colspan=12;
			else
				$colspan=13;
				
		echo "<table  width=100%>";
		echo "<tr class='fondoamarillo'>";
		echo "<td align=center colspan='".$colspan."' ><br><font size=3><b>CENTRO DE COSTOS:  </b>".$nom_cco."<br><b>SERVICIO:  </b>".$wser_pac."<br><b>FECHA:  </b>".$wfec_i."</font>";
		//chat
		echo "<table width=80% >";
		echo "<tr class='fondoamarillo'><td align=left colspan='".$colspan."'><font size=2 text color=#CC0000><b>Hora: ".$whora."</b></font></td></tr>";
		echo "</table>";
		$id=$increment.$pk_wccocod;
		mensajeria($id, $pk_wccocod);
		echo "<script>
			//arCco[ arCco.length ] = $id;
			arCco[ 0 ] = $id;
		</script>";  
		echo "<br><input type='HIDDEN' id='centro_costos".$id."' name='centro_costos' value='".trim($pk_wccocod)."' />";
		//fin chat
		if($filtrar=='si')  
			echo"<font size=2><b>PATRON </b>: ".$pk_nom_patron."-".$valor2."</font><br></td>";
		else
			echo "</td>";

		resumenXproductos($cco_detalle, $pk_wccocod, $filtrar, $pk_nom_patron );

		//=============================================================================================================
		// C U A D R O   D E   C O N V E N C I O N E S
		//=============================================================================================================
		echo "<td colspan=3>";
		convenciones();
		echo "</td>";
		//=============================================================================================================
		echo "</tr>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th>Habitacion</th>";
		echo "<th>Días</th>";
		echo "<th>Edad</th>";
		echo "<th>Historia</th>";
		echo "<th>Paciente</th>";
		echo "<th>Patrónes<br>Pedidos</th>";
		echo "<th>Patrón<br>Principal</th>";
		echo "<th>Cant.</th>";
		echo "<th>Detalle</th>";
		echo "<th>Estado</th>";
		echo "<th>Afinidad</th>";
		
		if( $verDiagnostico ) 
			echo "<th>Diagnostico</th>";
		
		echo "<th>Observaciones de <br>la estancia</th>";
		echo "<th>Observaciones de<br>DSN</th>";
		echo "<th>Traslados</th>";
		echo "<th>Intolerancias</th>";
		echo "<th>Hora<br>Solicitud</th>";
		echo "</tr>";
		$wclass2="fila1";
		
		//este primer foreach recorre el array de habitaciones ordenadas, lo utilizo solo con el fin de que los registros me salgan ordenados por habitacion
		//si el orden de las habitaciones no interesa, se puede quitar este foreach y no interfiere en el procedimiento 	
		foreach ($orden_habitaciones as $historia_orden => $clave_ord) 
		{
			foreach($arr_patrones as $main_nom_patr => $array_historias )
			{
				//si filtrar es igual a 'no', indica que quieren ver el detalle de todo el centro de costos 
				//osea que dieron click en el nombre del centro del costos, lo que implica que no se filtrara por patron
				$pintar='no';
				if($filtrar=='no')
					$pintar='si';
				else
				{
					if ($main_nom_patr==$pk_nom_patron)
						$pintar='si';
				}
			  
				if($pintar=='si')
				{
					foreach ($array_historias as $historia_pac => $valor)
					{
						if ($historia_pac==$historia_orden) // si esta historia corresponde a la historia del primer foreach; esto es para mostrar los registros ordenados por habitacion
						{
							if($filtrar=='no'){
								if(isset($paci_ya_pintados) && array_key_exists($historia_pac, $paci_ya_pintados))// esto es para no pintar pacientes repetidos
								continue;
							}
							$rango = "";
							$wdatos_rangos = "";
							$paci_ya_pintados[$historia_pac]='';
							if ($wclass2=="fila1")
								$wclass2="fila2";
							else
								$wclass2="fila1";
							//-----------------------------------------------------------------------------
							//Construir array para mostrar al final las observaciones y la intolerancias.
							//-----------------------------------------------------------------------------
							
							$habitacion	=	$main_pacientes[$historia_pac]['whab'];
							$color_habi	=	$main_pacientes[$historia_pac]['wcolor'];
							$observacio	=	$main_pacientes[$historia_pac]['observ_textarea'];
							$intoleranc	=	$main_pacientes[$historia_pac]['histor_intole'];
							
							$observa_intoleran[$habitacion]	=	$color_habi."|".$observacio."|".$intoleranc;
							//---------------------------
							//Fin array observaciones
							//---------------------------
							
							
							if($main_pacientes[$historia_pac]['rowdie']!=null && $main_pacientes[$historia_pac]['rowdie']!='') // si el patron esta en null no lo muestro 
							{
							
								$wpatronppal = $main_pacientes[$historia_pac]['rowdie'];
								
								//Si el patron asociado al paciente es DSN, imprimirá el patron asociado al DSN. Jonatan Lopez 10 Abril de 2014
								if( $main_pacientes[$historia_pac]['movdsn'] != "" and $wpatronppal == $winf_nutricion_dsn[1]){
									$main_pacientes[$historia_pac]['rowdie'].=" <br> (".$main_pacientes[$historia_pac]['movdsn'].")";
								}
								
								//Si el patron asociado al paciente es DSN, imprimirá la nutricionista. Jonatan Lopez 10 Abril de 2014
								if( $main_pacientes[$historia_pac]['movnut'] != "" and $wpatronppal == $winf_nutricion_dsn[1]){
								
									$main_pacientes[$historia_pac]['rowdie'].=" <br> <b>(".$main_pacientes[$historia_pac]['movnut'].")</b>";
								}								
								echo "<tr align=center class='".$wclass2."'>";
								echo "<td bgcolor=".$main_pacientes[$historia_pac]['wcolor'].">".$main_pacientes[$historia_pac]['whab']."</td>";          //**HABITACION
								
								//Se consulta la configuracion de los rangos de edades para mostrarle el fondo del color respectivo.
								$wcolores_estancia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'colores_dias_estancia');								
								$wrangos_col_estancia_aux = explode(";",$wcolores_estancia);
								
								foreach($wrangos_col_estancia_aux as $key => $value){
									
									//Separa cada dato de la configuracion de rangos de edad.
									//$wdatos_rangos[0] = rango inicial
									//$wdatos_rangos[1] = rango final
									//$wdatos_rangos[2] = color asociado
									$wdatos_rangos = explode("-", $value);
									if($main_pacientes[$historia_pac]['dias'] >= $wdatos_rangos[0] and $main_pacientes[$historia_pac]['dias'] <= $wdatos_rangos[1] ){
									
									$rango = "style='background: linear-gradient(70deg, ".$wdatos_rangos[2].", white);'";
									
									}
								
								}
								
								
								echo "<td $rango>".$main_pacientes[$historia_pac]['dias']."</td>"; 
								echo "<td>".($main_pacientes[$historia_pac]['wedad'])."</td>";                                                //**Edad
								echo "<td>".$main_pacientes[$historia_pac]['whis-wing']."</td>";                                                          //** Historia
								echo "<td align='left'>".$main_pacientes[$historia_pac]['wpac']."</td>";                                                               //** Paciente
								echo "<td>".$main_pacientes[$historia_pac]['rowdie']."</td>";                                                             //** Patrones
								echo "<td>".$main_pacientes[$historia_pac]['pat_prin']."</td>";                                                             //** Patron principal
								if ($main_pacientes[$historia_pac]['cant_ped'] == "0.5") 
									$color_can = "#FA5858";
								else
									$color_can = "";
								echo "<td bgcolor='".$color_can."'>".$main_pacientes[$historia_pac]['cant_ped']."</td>";                                                           //** cantidad pedida
								if( $main_pacientes[$historia_pac]['wcolor_det'] != "")
									echo "<td nowrap='nowrap' bgcolor=".$main_pacientes[$historia_pac]['wcolor_det']." align='left'>".$main_pacientes[$historia_pac]['wdetalle']."</td>";  //** DETALLE 
								else
									echo "<td nowrap='nowrap' align='left'>".$main_pacientes[$historia_pac]['wdetalle']."</td>";  //** DETALLE 
								if (isset($main_pacientes[$historia_pac]['alertas'])) //si tiene alertas le hago blink 
								{
									echo "<td bgcolor=".$main_pacientes[$historia_pac]['color_estado'].">";
									echo "<div class='blink'>";
									echo $main_pacientes[$historia_pac]['estado'];					//** Acciones
									if ($main_pacientes[$historia_pac]['estado']=='MODIFICO PEDIDO' || $main_pacientes[$historia_pac]['estado']=='MODIFICO ADICION')
									{
										echo '<br><b>Patrones Anteriores:</b><br>'.$main_pacientes[$historia_pac]['patr_anter'];		//patrones anteriores
									}
									echo "</div>";
									echo "</td>";  
									//$id_alertas=$id_alertas.$main_pacientes[$historia_pac]['alertas'].'|'; //variable que se enviara por ajax para actualizar el log de auditoria como alertas ya leias
									if( !empty( $main_pacientes[$historia_pac]['alertas'] )){
										$id_alertas=$id_alertas.$main_pacientes[$historia_pac]['alertas'].'|'; //variable que se enviara por ajax para actualizar el log de auditoria como alertas ya leias
									}
								}
								else
									echo "<td bgcolor=".$main_pacientes[$historia_pac]['color_estado'].">".$main_pacientes[$historia_pac]['estado']."</td>";  //** Acciones
									
								echo "<td align=center><font color=".$main_pacientes[$historia_pac]['color_afin']."><b>".$main_pacientes[$historia_pac]['wtpa']."<b></font></td>"; //**Afinidad
								//inicio consultar el diagnostico del paciente segun el kardex 
								$wing=$main_pacientes[$historia_pac]['wing'];
								$historia_consultar=$main_pacientes[$historia_pac]['whistoria'];
								$wdiag=traer_diagnostico($historia_consultar, $wing, $wfec_i);
							    if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
								{
									$dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
									$wayer = date('Y-m-d', $dia); //Formatea dia
									   
									$wdiag=traer_diagnostico($historia_consultar, $wing, $wayer);
								} 
								
								if( $verDiagnostico )  
									echo "<td><TEXTAREA rows=2 cols=30 READONLY>".$wdiag."</TEXTAREA></td>";       											 //** Diagnostico
								
								//fin diagnostico
								echo "<td><TEXTAREA rows=2 cols=30 READONLY>".base64_decode($main_pacientes[$historia_pac]['observ_textarea'])."</TEXTAREA></td>";        //** Observaciones
								
								//Si el patron asociado al paciente es DSN, imprimirá las observaciones. Jonatan Lopez 10 Abril de 2014
								if($wpatronppal == $winf_nutricion_dsn[1]){
								
								$main_pacientes[$historia_pac]['observ_dsn_textarea'] = base64_decode($main_pacientes[$historia_pac]['observ_dsn_textarea']);
								
								}else{
								
								$main_pacientes[$historia_pac]['observ_dsn_textarea'] = "";
								
								}
								
								echo "<td><TEXTAREA rows=2 cols=15 READONLY>".$main_pacientes[$historia_pac]['observ_dsn_textarea']."</TEXTAREA></td>";        //** Observaciones DSN
								if (isset($main_pacientes[$historia_pac]['alertas'])) //si tiene alertas le hago blink 
								{
									echo "<td><b class='blink'>".base64_decode($main_pacientes[$historia_pac]['wmensaje'])."</b></td>";                                      //**Traslados 
								}
								else
								{
									echo "<td><b>".base64_decode($main_pacientes[$historia_pac]['wmensaje'])."</b></td>";                                      //**Traslados 
								}
								echo "<td>".base64_decode($main_pacientes[$historia_pac]['histor_intole'])."</td>";                                                       //** Intolerancias
								echo "<td>".@$main_pacientes[$historia_pac]['hora_solicit']."</td>";                                                       //**Hora de Ultima Accion
								echo "</tr>";
							}
						}	
					  //if($filtrar=='si')
					  //break;
					}
				}
			}
		}	
		echo "</table><br>";
		
		//===========================================================    
        //     PINTAR INTOLERANCIAS Y OBSERVACIONES
        //===========================================================  
		$wclass4="fila1";
		echo "<br><br>";
		echo "<table width=40%>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th colspan='3'><FONT SIZE=3>Observaciones e Intolerancias<FONT></th>";
		echo "</tr>";
		echo "<tr class='encabezadoTabla'>";
		echo "<td align=center >Habitación</th>";
		echo "<td align=center >Observación</th>";
		echo "<td align=center >Intolerancia</th>";
		echo "</tr>";
		//$observa_intoleran[$habitacion]	=	$color_habi."|".$observacio."|".$intoleranc;
		foreach($observa_intoleran as $hab => $valores)	
		{	
			if ($wclass4=="fila1")
			$wclass4="fila2";
			else
				$wclass4="fila1";
			
			$valores = explode("|", $valores); // Explicacion: $valores[0] = Color de habitacion; $valores[1] = Observacion; $valores[2] = Intolerancia; 
			if ( ($valores[1] !=null && $valores[1] !=' ' && $valores[1] !='.') || ($valores[2] !=null && $valores[2] !=' ' && $valores[2] !='.') )
			{
				echo "<tr  class='".$wclass4."'>";
				echo "<td align='center' width=20% bgcolor=".$valores[0].">".$hab."</td>";          							//**HABITACION
				echo "<td align='center' width=30%><TEXTAREA rows=2 cols=28 READONLY>".base64_decode($valores[1])."</TEXTAREA></td>";       	//** Observaciones
				echo "<td align='center' width=30%>".base64_decode($valores[2])."</td>";                                                      	//** Intolerancias
				echo "</tr>";
			}
		}
		echo "</table>";
		unset ($observa_intoleran);
		//----------------------------------
		//FIN INTOLERANCIAS Y OBSERVACIONES
		//----------------------------------
		$pisoCompleto='';
		if($pk_nom_patron=='')
			$pisoCompleto="OK";		
		
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' $.unblockUI(); reactivar(\"".$id_alertas."\", \"".$pk_wccocod."\", \"".$pisoCompleto."\");' style='width:100'><br><br>";
	}
	
	// Esta funcion es para mostrar el detalle pero de los productos individuales.
	// La parte grafica es igual a la funcion mostrar_detalle pero su funcionalidad es muy diferente,
	// Es por eso que se decide hacer esta funcion aparte, para no aumentar la complejidad de la primera.
	function mostrar_detalle_productos($productos_pacientes,$clave_nombre, $main_pacientes, $wser_pac)
	{
		global $whora;
		global $wfec_i;
		
		echo "<table  width=100%>";
		echo "<tr class='fondoamarillo'>";
		echo "<td align=center colspan=13 ><br><font size=3><b>PRODUCTO:  </b>". strtoupper($clave_nombre)."<b><br>SERVICIO:  </b>".$wser_pac."</font></td>";
		//=============================================================================================================
		// C U A D R O   D E   C O N V E N C I O N E S
		//=============================================================================================================
		echo "<td colspan=3>";        
		convenciones();
		echo "</td>";
		//=============================================================================================================
		echo "</tr>";
		echo "<tr class='fondoamarillo'>";
		echo "<tr>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th>Habitacion</th>";
		echo "<th>Días</th>";
		echo "<th>Edad</th>";
		echo "<th>Historia</th>";
		echo "<th>Paciente</th>";
		echo "<th>Patrónes<br>Pedidos</th>";
		echo "<th>Patrón<br>Principal</th>";
		echo "<th>Cant.</th>";
		echo "<th>Detalle</th>";
		echo "<th>Estado</th>";
		echo "<th>Afinidad</th>";
		echo "<th>Diagnostico</th>";
		echo "<th>Observaciones de <br>la estancia</th>";
		echo "<th>Observaciones de <br>DSN</th>";
		echo "<th>Traslados</th>";
		echo "<th>Intolerancias</th>";
		echo "<th>Hora<br>Solicitud</th>";
		echo "</tr>";
		$wclass2="fila1";
		  
		foreach ($productos_pacientes as $key_producto => $array_productos) 
		{
			if ($key_producto==$clave_nombre)
			{
				foreach($array_productos as $historia_pac => $key)
				{
					if($wclass2=="fila2")
						$wclass2="fila1";
					else
						$wclass2="fila2";
					echo "<tr align=center class='".$wclass2."'>";
					echo "<td bgcolor=".$main_pacientes[$historia_pac]['wcolor'].">".$main_pacientes[$historia_pac]['whab']."</td>";          						//**Habitacion
					echo "<td>".$main_pacientes[$historia_pac]['dias']."</td>"; 																					//**Dias
					echo "<td>".html_entity_decode($main_pacientes[$historia_pac]['wedad'])."</td>";                                               						//**Edad
					echo "<td>".$main_pacientes[$historia_pac]['whis-wing']."</td>";                                                          						//**Historia
					echo "<td align='left'>".$main_pacientes[$historia_pac]['wpac']."</td>";                                                               						//**Paciente
					echo "<td>".$main_pacientes[$historia_pac]['rowdie']."</td>";                                                             						//**Patrones
					echo "<td>".$main_pacientes[$historia_pac]['pat_prin']."</td>";                                                             					//**Patrone principal
					echo "<td>".$main_pacientes[$historia_pac]['cant_ped']."</td>";                                                           						//**cantidad pedida
					if( $main_pacientes[$historia_pac]['wcolor_det'] != "")
						echo "<td nowrap='nowrap' bgcolor=".$main_pacientes[$historia_pac]['wcolor_det']." align='left'>".$main_pacientes[$historia_pac]['wdetalle']."</td>";  //** DETALLE 
					else
						echo "<td nowrap='nowrap' align='left'>".$main_pacientes[$historia_pac]['wdetalle']."</td>";  //** DETALLE 
					
					echo "<td bgcolor=".$main_pacientes[$historia_pac]['color_estado'].">".$main_pacientes[$historia_pac]['estado'];								//**Acciones
					if ($main_pacientes[$historia_pac]['estado']=='MODIFICO PEDIDO' || $main_pacientes[$historia_pac]['estado']=='MODIFICO ADICION')
					{
						echo '<br><b>Patrones Anteriores:</b><br>'.$main_pacientes[$historia_pac]['patr_anter'];												//**Patrones anteriores
					}
					echo "</td>";
					echo "<td align=center><font color=".$main_pacientes[$historia_pac]['color_afin']."><b>".$main_pacientes[$historia_pac]['wtpa']."<b></font></td>";//**Afinidad
					//inicio consultar el diagnostico del paciente segun el kardex 
						$wing=$main_pacientes[$historia_pac]['wing'];
						$historia_consultar=$main_pacientes[$historia_pac]['whistoria'];
						$wdiag=traer_diagnostico($historia_consultar, $wing, $wfec_i);
						   if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
							  {
							   $dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
							   $wayer = date('Y-m-d', $dia); //Formatea dia
							   
							   $wdiag=traer_diagnostico($historia_consultar, $wing, $wayer);
							  } 
					echo "<td><TEXTAREA rows=2 cols=30 READONLY>".$wdiag."</TEXTAREA></td>";       											 						//**Diagnostico
					//fin diagnostico
					echo "<td><TEXTAREA rows=2 cols=30 READONLY>".base64_decode($main_pacientes[$historia_pac]['observ_textarea'])."</TEXTAREA></td>"; 
					echo "<td><TEXTAREA rows=2 cols=15 READONLY>".base64_decode($main_pacientes[$historia_pac]['observ_dsn_textarea'])."</TEXTAREA></td>";        //** Observaciones DSN//**Observaciones
					echo "<td><b>".$main_pacientes[$historia_pac]['wmensaje']."</b></td>";                                      									//**Traslados 
					echo "<td>".$main_pacientes[$historia_pac]['histor_intole']."</td>";                                                       						//** Intolerancias
					echo "<td>".@$main_pacientes[$historia_pac]['hora_solicit']."</td>";                                                       						//**Hora de Ultima Accion
					
					echo "</tr>";
				}
			}		
		}	
		echo "</table><br>";
	}
  
	//FUNCION QUE ME RETORNA EL NOMBRE DE UN PATRON
	function nombre_patron($valor_patr)
    {
        global $wbasedato;
        global $conex;
        $query_nom_pat="SELECT Diedes, Dieord 
						  FROM ".$wbasedato."_000041 
						 WHERE Diecod='".$valor_patr."' 
						";
        $resnp = mysql_query($query_nom_pat, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_nom_pat." - ".mysql_error());
        $rownp=mysql_fetch_array($resnp);
        return $rownp; 
    }
   
	//Conocer si el paciente esta en proceso de alta o traslado
	function estado_del_paciente($whis,$wing,&$walta,&$wtraslado)
    {
		global $wbasedato;
		global $conex;
	  
		$walta="off";
		$wtraslado="off";
		
		$q= " SELECT ubialp, ubiptr
				FROM ".$wbasedato."_000018 
			   WHERE ubihis  = '".$whis."'
				 AND ubiing  = '".$wing."'
				 AND ubiald != 'on' ";
		$res = mysql_query($q, $conex) or die("ERROR EN QUERY");
		$wnum = mysql_fetch_array($res); 		
		if ($wnum[0] > 0)         //Si es mayor a cero es porque esta en proceso de alta
		{
			$row = mysql_fetch_array($res);
			if( $row[0] == 'on' )
				$walta= $row[0];
			if( $row[1] == 'on' )
				$wtraslado=$row[1];
        }
		/*		 
		//En proceso de alta
		$q= " SELECT COUNT(*) 
				FROM ".$wbasedato."_000018 
			   WHERE ubihis  = '".$whis."'
				 AND ubiing  = '".$wing."'
				 AND ubialp  = 'on' 
				 AND ubiald != 'on' ";
		$res = mysql_query($q, $conex) or die("ERROR EN QUERY");
		$wnum = mysql_fetch_array($res); 
      
		if ($wnum[0] > 0)         //Si es mayor a cero es porque esta en proceso de alta
		{
			$walta="on";
        }         
		//En proceso de traslado
		$q= " SELECT COUNT(*) 
				FROM ".$wbasedato."_000018 
			   WHERE ubihis  = '".$whis."'
			     AND ubiing  = '".$wing."'
				 AND ubiptr  = 'on' 
				 AND ubiald != 'on' 
			";
		$res = mysql_query($q, $conex) or die("ERROR EN QUERY");
		$wnum = mysql_fetch_array($res); 
      
		if ($wnum[0] > 0)         //Si es mayor a cero es porque esta en proceso de alta
		{
			$wtraslado="on";
		}     */   
	}
	
	//----------------------------------------
	//	Funciones para calcular edad
	//----------------------------------------
	function tiempo_transcurrido($fecha_nacimiento, $fecha_control)
	{
		$fecha_actual = $fecha_control;
	   
		if(!strlen($fecha_actual)){
			$fecha_actual = date('d/m/Y');
		}
		// separamos en partes las fechas 
		$array_nacimiento = explode ( "/", $fecha_nacimiento ); 
	    $array_actual = explode ( "/", $fecha_actual ); 

	    $anos =  $array_actual[2] - $array_nacimiento[2]; // calculamos años 
	    $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
	    $dias =  $array_actual[0] - $array_nacimiento[0]; // calculamos días 

	    //ajuste de posible negativo en $días 
	    if ($dias < 0){ 
		    --$meses;
		    //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual 
		    switch ($array_actual[1]) 
			{ 
				case 1: 
					$dias_mes_anterior=31;
					break; 
				case 2:     
					$dias_mes_anterior=31;
					break; 
				case 3:  
					if (bisiesto($array_actual[2])){ 
						$dias_mes_anterior=29;
						break; 
					} 
					else{ 
						$dias_mes_anterior=28;
						break; 
					} 
				case 4:
					$dias_mes_anterior=31;
					break; 
				case 5:
					$dias_mes_anterior=30;
					break; 
				case 6:
					$dias_mes_anterior=31;
					break; 
				case 7:
					$dias_mes_anterior=30;
					break; 
				case 8:
					$dias_mes_anterior=31;
					break; 
				case 9:
					$dias_mes_anterior=31;
					break; 
				case 10:
					$dias_mes_anterior=30;
					break; 
				case 11:
					$dias_mes_anterior=31;
					break; 
				case 12:
					$dias_mes_anterior=30;
				break; 
			}	 

		$dias=$dias + $dias_mes_anterior;

			if ($dias < 0){
				--$meses;
				if($dias == -1)
				{
					$dias = 30;
				}
				if($dias == -2)
				{
					$dias = 29;
				}
			}
		}
		//ajuste de posible negativo en $meses 
		if ($meses < 0){ 
			--$anos; 
			$meses=$meses + 12; 
		}
		$tiempo[0] = $anos;
		$tiempo[1] = $meses;
		$tiempo[2] = $dias;

		return $tiempo;
	}

	function bisiesto($anio_actual){
		$bisiesto=false; 
		//probamos si el mes de febrero del año actual tiene 29 días 
		if (checkdate(2,29,$anio_actual)){ 
			$bisiesto=true; 
		} 
		return $bisiesto; 
	}
	//----------------------------------------
	//	Fin calcular edad
	//----------------------------------------
	
	function partircadena($cadena, $numCaracteres)
	{
       $cadenatransformada = "";
       $veccadena = array();
       $contadorcadena=0;
       $veccadena = explode(" ",$cadena);
       for($i=0; $i < count($veccadena) ; $i++)
       {		
                $contadorcadena += strlen($veccadena[$i]);				
                if($contadorcadena >= $numCaracteres){
                       $contadorcadena = 0;
                       $cadenatransformada .= $veccadena[$i]."<br>";
                }
                else{
                       $cadenatransformada .= $veccadena[$i]." ";
                }       
       }
      $cadena = $cadenatransformada;
	}
	//================================================================ 
	//    FIN FUNCIONES
	//================================================================
	function imprimirTablaPedidos($wcco, $wser, $wfec_i){
		global $wemp_pmla;
		global $user;
		global $wtabcco;
		global $wbasedato;
		global $conex;
		global $id_alertas;
		global $array_alertas;
		global $selectsede;
		
		$caracteres = array( "á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","\\","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??","?£", "°", "\"");
		$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ", "", "N", "N", "U", "", "");
			
		
		//Formatear arCco para que no agregue mas pisos
		echo "<script>
				arCco = new Array();
			</script>";
		
		//=====================================================================================
		// Aca comienzo a consultar y procesar la informacion para luego mostrarla
		//=====================================================================================
		$wccoaux=explode("-",$wcco);
		$res= query_principal($wccoaux[0], $wser, $selectsede);
		$num = mysql_num_rows($res);
		$wccoant="";
		if ($num > 0)
		{
			for ($i=0;$i<$num;$i++) //for principal
			{
				$row = mysql_fetch_array($res);
				
				$row['movcan'] = (float) $row['movcan'];
				
				$hay_resultados='si';
			  
				$whab      = $row['movhab'];   				//Habitacion desde donde se relizo el pedido
				$whis      = $row['movhis'];				//Historia
				$his_hab	 = $row['movhis'].'-'.$row['movhab'].'-'.$row['movpco'];
				$wing      = $row['moving'];				//Ingreso
				$wptr      = $row['ubiptr'];   				//Indica si es un traslado
				$whab_tras = $row['ubihac'];   				//Habitacion de traslado, si hay traslado.
				$wccocod   = trim($row['movcco']);   		//Centro de Costo
				$wcconom   = $row['Cconom'];   				//Nombre del Centro de Costos
				$wtpo      = $row['ingtip'];  				//Indica si el tipo de paciente es POS o No
				$wdias_est = $row['dias'];  				//Dias de estancia      
				$wpac      = htmlspecialchars( htmlentities( $row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2'] ) ); //Nombre completo
				$wdpa 	 = $row['pacced'];     				//Cedula del paciente
				$wtid 	 = $row['pactid'];					// Tipo de identificacion
				$wnac 	 = $row['pacnac'];					//Fecha de nacimiento
				$wser_pac  = $row['movser'];				//Servicio
				$pos_quiru = $row['movpqu'];				//Indica si se pidio posqirurgico o no 
				$hab_anter = $row['ubihan'];				//Habitacion anterior
				$cco_actual= $row['ubisac'];				//CCO Actual
				$cco_anter = $row['ubisan'];				//CCO anterior
				$pendi_imp = $row['movimp'];				//Si esta pendiente de impresion
				$movdsn = $row['movdsn'];
				$movnut = $row['movnut'];
				$nom_cco[$wccocod]=$wcconom; //Arreglo para guardar los nombres de los CC
				
				
				if( $movnut != "" && $movnut != "NO APLICA" && $movnut != "." ){
					$movnut = consultarNombreUsuario( $movnut );				
				}
				
				$movdsn = str_replace( $caracteres, $caracteres2, $movdsn );
				$movnut = str_replace( $caracteres, $caracteres2, $movnut );
				//-----------------
				// Calculo la edad
				//-----------------
				$wedad = "";
				if( empty( $wnac ) == false){
					$row_fecha_nacimiento = explode('-', $wnac);
					$fecha_nacimiento = $row_fecha_nacimiento[2].'/'.$row_fecha_nacimiento[1].'/'.$row_fecha_nacimiento[0]; // pasar fecha de formato 2012-10-09 a formato 09/10/2012
					$wedad = tiempo_transcurrido($fecha_nacimiento, date('d/m/Y'));
					$mensaje_edad = "";
					if( $wedad[0] > 1 )
						$mensaje_edad.= $wedad[0]." A&ntilde;os ";
					else if( $wedad[0] == 1 )
						$mensaje_edad.= $wedad[0]." A&ntilde;o ";
						
					if( $wedad[1] > 1 )
						$mensaje_edad.= $wedad[1]." Meses";
					else if( $wedad[1] == 1 )
						$mensaje_edad.= $wedad[1]." Mes";
						
					$wedad = htmlspecialchars ( $mensaje_edad );
				}			  
				//-----------------------------------------------
				// Si esta en proceso de traslado colocar mensaje
				//-----------------------------------------------
				$wmensaje="";
				if ($wptr == "on"){// si es un traslado				
					$wmensaje="Paciente que va para la habitación : <b>".$whab_tras."</b>";
				}
				//--------------------------------
				// Consultar si el paciento es POS
				//--------------------------------
				$q = " SELECT COUNT(*) 
						 FROM ".$wbasedato."_000076 
						WHERE Sertpo LIKE '%".$wtpo."%'
						  AND Sercod = '".$wser_pac."'
						  AND Serest = 'on' ";
				$restpo = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$rowtpo=mysql_fetch_array($restpo);
				if ($rowtpo[0] > 0)
					$wcolor="E9C2A6";
				else
					$wcolor="";
				 
				//================================================================================================================
				//    ACA COMIENZO A GUARDAR LOS DATOS EN UN ARRAY PARA PINTARLOS AL FINAL 
				//        $main_pacientes[HISTORIA DEL PACIENTE][NOMBRE DEL CAMPO]= VALOR
				//================================================================================================================
			  
				$main_pacientes[$his_hab]['wcolor']=$wcolor;                     	//** Color
				$main_pacientes[$his_hab]['whab']=$whab;                         	//** Habitacion
				$main_pacientes[$his_hab]['wedad']=$wedad;                       	//** Edad
				$main_pacientes[$his_hab]['whistoria']=$whis;                    	//** Historia
				$main_pacientes[$his_hab]['whis-wing']=$whis.'-'.$wing;          	//** Historia e ingreso    
				$main_pacientes[$his_hab]['wing']=$wing;          			   		//** ingreso    
				$main_pacientes[$his_hab]['wpac']=$wpac;                         	//** Paciente
				$main_pacientes[$his_hab]['dias']=$wdias_est;                    	//** Dias de estancia
				$main_pacientes[$his_hab]['rowdie']=$row['movdie'];              	//** Patrones
				$main_pacientes[$his_hab]['patr_anter']=$row['movpam'];          	//** Patrones Anteriores, en caso de que se hayan realizado modficaciones
				$main_pacientes[$his_hab]['cant_ped']=$row['movcan'];            	//** cantidad pedida
				$main_pacientes[$his_hab]['pat_prin']=$row['movpco'];            	//** patron principal
				$main_pacientes[$his_hab]['movdsn']=$movdsn;            			//** patron principal
				$main_pacientes[$his_hab]['movnut']=$movnut;            			//** codigo nutricionista
				$orden_habitaciones[$his_hab]=$whab;								//** aca almaceno todas las habitaciones que se mostraran, para luego ordenarlas  
				$patron_principal=$row['movpco'];									//** patron prinicpal
				$cantidad_pat=$row['movcan'];										//** cantidad pedida 
				$cantidad_pat=$cantidad_pat*1;										//** Para que trabaje como un entero
				$patron_con_productos='';											//** Esta variable me indica si debo consular detalle de productos
				$patrones_pac=$row['movdie']; 										//** Esta variable la utilizo en las alertas para saber si el paciente tiene dietas
				//=================================================================================================================
			  
				/*if (strpos($row['movdie'],",")) // si el paciente tiene mas de un patron programado para el servicio actual
				{
					$wpatron=explode(",",$row['movdie']);
					foreach($wpatron as $valor_patr)	//recorro todos los patrones
					{
						//===============================================================================
						//  En este array guardo los patrones e historias pertenecientes a cada CC
						//  $main_patrones[Centro de costos][Patron][Historia]='este valor no interesa' 
						//================================================================================
							$main_patrones[$wccocod][$valor_patr][$his_hab]=1;     
						//================================================================================
						
						// Si dentro de la cadena de patrones existe alguno que sea de servicio individual le debo consultar el detalle.
						// Por ejemplo si es la combinacion 'L,SI' el patron principal es L pero igual en el monitor debo mostrar el detalle 
						// Porque existe un servicio individual en el pedido
						if (servicio_individual($valor_patr))
							$patron_con_productos=$valor_patr;
						
						
						if($pos_quiru=='on')
							$valor_patr_temp=$patron_principal;
						else
							$valor_patr_temp=$valor_patr;
							
						// 1= $rowdie[6]=='off', indica que el patron ha sido cancelado, y solo debo contabilizar los activos
						// 2= $valor_patr_temp==$patron_principal, de todos los patrones programados solo debo contabilizar el principal que es el mismo que el cobrado (77, Movpco)
						if($row['movest']!='off' && $valor_patr_temp==$patron_principal)            
						{
							$nom_patron=nombre_patron($valor_patr);         	//funcion que retorna el nombre del patron
							$todos_patrones[$valor_patr]=$nom_patron[0];       	//array para conocer todos los patrones existentes en el reporte
							$orden_patrones[$valor_patr]=$nom_patron[1];	   	// Array para almacenar el orden de los patrones en el sistema 
						   
							if(!isset($num_patrones[$wccocod][$valor_patr]))
							{
								$num_patrones[$wccocod][$valor_patr]=$cantidad_pat;       //array para almacenar la cantidad del patron
							}
							else
							{
								$num_patrones[$wccocod][$valor_patr]+=$cantidad_pat;
							}
						}
					}
				}*/
				
				$valor_patr=$row['movdie'];
				//===============================================================================
				//  En este array guardo los patrones e historias pertenecientes a cada CC
				//  $main_patrones[Centro de costos][Patron][Historia]='este valor no interesa' 
				//================================================================================
				$main_patrones[$wccocod][$valor_patr][$his_hab]=1;     
				//================================================================================
				unset($arr_patrones );
				if (strpos($valor_patr,","))
				{
					$arr_patrones = explode(",",$valor_patr);
					foreach($arr_patrones as $valor_pat)	//recorro todos los patrones
					{
						if($valor_pat !=''){
							if (servicio_individual($valor_pat))
								$patron_con_productos=$valor_pat;
						}
					}
				}
				else
				{
					$arr_patrones[0] = $valor_patr;
					if (servicio_individual($valor_patr))
						$patron_con_productos=$valor_patr;
				}
				
				// 1 = si el patron es null no lo debo contabilizar en la matriz principal y solo lo debo mostrar en la tabla de 'observaciones e intolerancias' 
				// 2= $rowdie[6]=='off', indica que el patron ha sido cancelado, y solo debo contabilizar los patrones activos
				if($valor_patr!='' && $valor_patr!=NULL && $row['movest']!='off')
				{
					$nom_patron = '';
					foreach($arr_patrones as $valor_pat)	//recorro todos los patrones
					{
						if($valor_pat !='')
						{
							$vector_nom_posi = nombre_patron($valor_pat);
							if(count($arr_patrones)>1)
								$pos_patron = 100; //Le pongo 100 para que los que tengan mas de un patron queden de ultimos
							else
								$pos_patron = $vector_nom_posi[1];
								
							if($nom_patron == '')
								$nom_patron = $vector_nom_posi[0];
							else
								$nom_patron.='-'.$vector_nom_posi[0];;
						}
					}					
					$todos_patrones[$valor_patr]=$nom_patron;                  // Array para conocer todos los patrones existentes en el reporte
					$orden_patrones[$valor_patr]=$pos_patron;				  // Array para almacenar el orden de los patrones en el sistema 
					
					if(!isset($num_patrones[$wccocod][$valor_patr])){
						$num_patrones[$wccocod][$valor_patr]=$cantidad_pat;
					}    
					else{
						$num_patrones[$wccocod][$valor_patr]+=$cantidad_pat;
					} 
				}
				
				//--------------------------------------------------------------
				// Consultar el Detalle: 
				// Solo si existe algun servicio individual dentro del pedido
				//--------------------------------------------------------------
				if (isset($patron_con_productos) && $patron_con_productos!=''){
					$q = " SELECT Prodes, Detcan "
					  ."   FROM ".$wbasedato."_000084, ".$wbasedato."_000082 "
					  ."  WHERE detfec = '".$wfec_i."'"
					  ."    AND dethis = '".$whis."'"
					  ."    AND deting = '".$wing."'"
					  ."    AND detser = '".$row['movser']."'"
					  ."    AND detpat = '".$patron_con_productos."'";
					  //."	AND detcco = '".$cco_actual."'"
					  
					if ($row['movest']=='on'){
						$q.="    AND detest = 'on' ";
					}
					else{
						$q.="   AND detest = 'off' 
								AND detcal = 'on'	";
					}
					
					$q.="    AND Procod = detpro";
					$rescbi = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$numcbi = mysql_num_rows($rescbi);
					$wdetalle="";
					$wcolor_detalle="";
					
					for ($k=1;$k<=$numcbi;$k++){
					
						$wcolor_detalle="FF7F00";  //Naranja
						$rowcbi=mysql_fetch_array($rescbi);
						$nombre_producto=$rowcbi[0];
						$cantidad=$rowcbi[1];
						
						$nombre_producto = str_replace( $caracteres, $caracteres2, $nombre_producto );
						
						partircadena($nombre_producto, 15);
						
						if (trim($wdetalle) != "")
							$wdetalle=$wdetalle."<br> ".$cantidad." ".$nombre_producto;
						else
							$wdetalle=$cantidad." ".$nombre_producto;
						
						//Este array es para agrupar pacientes por producto	
							$productos_pacientes[$nombre_producto][$his_hab]='';
							
						if ($row['movest']=='on'){ // Solo muestro los cuadro resumen si el patron no esta cancelado
							//Este array lo uso para mostrar el cuadro 'resumen x productos' que se pinta dentro del detalle o ventana emergente	
							if (!isset($cco_detalle[$wccocod][$patron_con_productos][$nombre_producto]))
								$cco_detalle[$wccocod][$patron_con_productos][$nombre_producto]=$cantidad;
							else
								$cco_detalle[$wccocod][$patron_con_productos][$nombre_producto]+=$cantidad;	
							
							//Este arreglo es para mostrar el cuadro, RESUMEN POR PRODUCTO DE PATRONES INDIVIDUALES
							if (!isset($resumenXproducto[$nombre_producto])){
								$resumenXproducto[$nombre_producto]=$cantidad;		
							}
							else{
								$resumenXproducto[$nombre_producto]+=$cantidad;						
							}
						}
					}
					$wdetalle = str_replace( "<br><br>", "<br>", $wdetalle );
					$wdetalle = str_replace( $caracteres, $caracteres2, $wdetalle );
					
					$main_pacientes[$his_hab]['wcolor_det']=$wcolor_detalle;  
					$main_pacientes[$his_hab]['wdetalle'] = $wdetalle;
					//$main_pacientes[$his_hab]['serv_indivi']=$patron_con_productos;            //** patron de servicio individual
				}
				else{
					$main_pacientes[$his_hab]['wcolor_det']='';  
					$main_pacientes[$his_hab]['wdetalle']='';
					//$main_pacientes[$his_hab]['serv_indivi']='';
				}
				//-------------------
				//	Fin detalle
				//-------------------
				//-------------------------------------------------
				//	Si esta pendiente de impresion, generar alerta  
				//-------------------------------------------------
				if($pendi_imp != 'on' && $row['movdie']!='' && $row['movest']=='on')
				{
					if (isset($array_alertas[$wccocod]['impresiones']))
						$array_alertas[$wccocod]['impresiones']++;
					else
						$array_alertas[$wccocod]['impresiones']=1;
				}
				//----------------------------------------------------------
				// Consultar el estado del patron y gestionar las alertas
				//----------------------------------------------------------				
				$q = " SELECT hora_data, Audacc, Audfle, MAX(id) as id "
					."  FROM ".$wbasedato."_000078 "
					." WHERE audhis = '".$whis."'"
					."   AND auding = '".$wing."'"
					."   AND audser = '".$wser_pac."'"
					."   AND fecha_data = '".$wfec_i."'"
					." GROUP BY id "
					." ORDER BY id DESC ";			
						
				$resaud = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$numaud = mysql_num_rows($resaud);
				if ($numaud > 0)
				{
					$rowaud = mysql_fetch_array($resaud);
					$wcolor="";				  
					//----------------------------------------------------------------------------------------------------------------------------
					// ! ! !  NOTA ¡ ¡ ¡ = Existen unos casos donde el ultimo movimiento registrado en la tabla de auditoria no es el 
					//					 correspondiente al registro del pedido, por eso utilizo estas excepciones, si el registro 
					//					 cumple con alguna de ellas entonces le consulto su estado real 
					//---------------------------------------------------------------------------------------------------------------------------------------
					// EXCEPCION 1: Si no esta en proceso de traslado && el servicio esta cancelado && el cc anterior es igual al cc de donde hicieron el pedido. 
					// Esto lo hago porque cuando se realiza un traslado el ultimo movimiento en la tabla de auditoria sera un 'pedido' y el pedido del
					// centro de costos antes del traslado debe salir con el estado 'cancelado por traslado' no como 'pedido'
					//---------------------------------------------------------------------------------------------------------------------------------------
					if ($wptr == "off" && $row['movest']=='off' && $cco_anter==$wccocod  )
					{
						$q2 = "SELECT Audfle, id, Audacc 
								 FROM ".$wbasedato."_000078 
								WHERE audhis = '".$whis."'
								  AND auding = '".$wing."'
								  AND audser = '".$wser_pac."'
								  AND fecha_data = '".$wfec_i."'
								  AND Audacc = 'CANCELADO POR TRASLADO' 
							";
						
						$resaud2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
						$rowaud2 = mysql_fetch_array($resaud2);
						$num_rowaud2 = mysql_num_rows($resaud2);
						if ($num_rowaud2>0)
						{
							$rowaud[1]= $rowaud2[2];	
							$rowaud[2]= $rowaud2[0];
							$rowaud[3]= $rowaud2[1];
						}	  
					}
				  
					//---------------------------------------------------------------------------------------------------------------------------------------
					// EXCEPCION 2: Si el pedido esta cancelado y el ultimo movimiento es una modificacion de intolerancia u observacion;
					// Esto lo hago porque cuando cancelan un pedido y luego insertan una observacion o intolerancia el ultimo registro en la tabla de auditoria 
					// es 'Modifico Observacion' o 'intolerancia', entonces el patron saldra con este estado y no se visualizara el 'Cancelado', 
					// que es lo que realmente se necesita visualizar.
					//---------------------------------------------------------------------------------------------------------------------------------------		  
					if ( $row['movest']=='off' and ($rowaud[1]=='MODIFICO OBSERVACION' or $rowaud[1]=='MODIFICO INTOLERANCIA' or $rowaud[1]=='MODIFICO OBSERVACION Y MODIFICO INTOLERANCIAS' or $rowaud[1]=='MODIFICO OBSERVACION DSN')) 
					{
						//Valido que exista un registro de 'cancelado' en la auditoria
						$q3 ="SELECT Audfle, id, Audacc 
								FROM ".$wbasedato."_000078 
							   WHERE audhis = '".$whis."'
								 AND auding = '".$wing."'
								 AND audser = '".$wser_pac."'
								 AND fecha_data = '".$wfec_i."'
								 AND Audacc = 'CANCELADO' ";
						
						$resaud3 = mysql_query($q3,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q3." - ".mysql_error());
						$rowaud3 = mysql_fetch_array($resaud3);
						$num_rowaud3 = mysql_num_rows($resaud3);
						if ($num_rowaud3>0)
						{
							$rowaud[1]	=	$rowaud3[2];	
							$rowaud[2]	=	$rowaud3[0];
							$rowaud[3]	=	$rowaud3[1];
						}
					}
				  
					//Consultar el color del estado 
					$q_color= "	SELECT  Estcol
								  FROM  ".$wbasedato."_000129
								 WHERE  Estdes = '".$rowaud[1]."'
								   AND	Estest = 'on'";
					$res_color = mysql_query($q_color,$conex) or die ("Error: ".mysql_errno()." - en el query:(consultar color) ".$q_color." - ".mysql_error());
					$row_color = mysql_fetch_array($res_color);
					$num_color = mysql_num_rows($res_color);
					if ($num_color>0){
						$wcolor=$row_color['Estcol'];
					}
				  
					$maneja_alerta = 'si';
					if ($rowaud[1]=='ALTA - SERVICIO SIN CANCELAR' || $rowaud[1]=='PROCESO DE ALTA' || $rowaud[1]=='MUERTE - SERVICIO SIN CANCELAR' || $rowaud[1]=='PEDIDO')
						$maneja_alerta='no';
				  
					// Si el estado de la dieta maneja alerta y el paciente tiene dieta programada.
					if ($maneja_alerta=='si' && $patrones_pac!='' && $patrones_pac!=NULL)
					{
						guardar_alertas($wccocod, $wcolor, $rowaud[2], $his_hab, $main_pacientes, $rowaud[3]);
						if($rowaud[1]=='PEDIDO POR TRASLADO') // Este estado maneja mensaje
						{
							$wmensaje="Habitación Origen : <b>".$hab_anter."</b>";
						}
					}					
					//Consultar si esta en proceso de alta o de traslado
					estado_del_paciente($whis,$wing,$walta,$wtraslado);
					//Indica que esta en proceso de alta
					if ($walta=="on" && $rowaud[1]!='CANCELADO POR TRASLADO')
						$wcolor="FFFF99";
						 
					//Indica que el paciente esta siendo trasladado
					if ($wtraslado=="on"){
						$wcolor="3299CC";
						if ($patrones_pac!='' && $patrones_pac!=NULL)
							guardar_alertas($wccocod, $wcolor, '0000-00-00', $his_hab, $main_pacientes, ' ');
					}
					//Guardo en el array principal el estado con su correspondiente color
					$main_pacientes[$his_hab]['color_estado']=$wcolor;  
					$main_pacientes[$his_hab]['estado']=$rowaud[1];							
				}
				else{
					$main_pacientes[$his_hab]['color_estado']="";  
					$main_pacientes[$his_hab]['estado']=" "; 
				}
				//Fin estado
				
				//=========================================================
				// Consultar si el paciente es AFIN o no, y de que tipo
				//=========================================================
				$wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
				if ($wafin){
					$main_pacientes[$his_hab]['color_afin']=$wcolorpac;
					$main_pacientes[$his_hab]['wtpa']=$wtpa;
				}
				else{
					$main_pacientes[$his_hab]['color_afin']="";
					$main_pacientes[$his_hab]['wtpa']="";
				}
				
				$row['movobs'] = base64_encode($row['movobs']);
				$main_pacientes[$his_hab]['observ_textarea']=$row['movobs'];
				
				$row['movods'] = base64_encode( $row['movods'] );
				$main_pacientes[$his_hab]['observ_dsn_textarea']=$row['movods'];
				
				$wmensaje = base64_encode( $wmensaje );
				$main_pacientes[$his_hab]['wmensaje']=$wmensaje;
				$main_pacientes[$his_hab]['hora_solicit']=@$rowaud[0]; 
			  
				//======================================================================
				// Intolerancia: Busco si hay alguna en cualquier ingreso del paciente
				//======================================================================	
				$q = " SELECT MAX(CONCAT(fecha_data,hora_data)), movint 
						 FROM ".$wbasedato."_000077 
						WHERE movhis = '".$whis."'
						  AND movint != '' 
						GROUP BY 2 
						ORDER BY 1 DESC 
					";
				$res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (Buscar intolerancia): ".$q." - ".mysql_error());
				$rowint=mysql_fetch_array($res_mov);
				
				$main_pacientes[$his_hab]['histor_intole']=base64_encode($rowint['movint']);
				
			}//Fin del for principal
			
			//                         CONSULTAR LOS PEDIDOS DE CARTA MENU PARA RESTARLE AL PATRON-PISO
			//*************************************
				//LOS PATRONES PEDIATRICOS SE IGNORAN, ES DECIR, SI UN NINO TIENE EN CARTA MENU UN PATRON   N,P9.12  :
				//QUE ES: NORMAL Y PEDIATRICO ENTRE 9 Y 12 MESES
				//EN EL "RESUMEN POR PATRONES Y CENTRO DE COSTOS" SE TIENE QUE RESTAR AL PATRON NORMAL
				//PUESTO QUE EL PATRON PEDIATRICO SE ASIGNA A TODOS LOS NIÑOS POR SISTEMA Y NO MANUALMENTE
				$q = " SELECT Diecod as codigo"
					."   FROM ".$wbasedato."_000041 "
					."  WHERE Dieped = 'on'";
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);

				$patrones_a_ignorar = array();
				if ($num > 0){
					$i=0;
					while($row = mysql_fetch_assoc($res)){
						array_push( $patrones_a_ignorar, $row['codigo'] );
					}
				}
			    //--------------------------------------------------------------------------------     
				$and ="";
				$ccodatos = explode("-",$wcco);
				$ccodatos=trim($ccodatos[0]);
				if( $ccodatos == '%' )
					$ccodatos = "";
				if( $ccodatos != "")
					$and = "   AND Pamcco = '".$ccodatos."' ";
				
				$q = "SELECT Pamcco as cco, Menpat as pat "
					."  FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B "
					." WHERE B.Pamfec = '".$wfec_i."' "
					."   AND Pamide = A.id "
					.$and
					."   AND Menser = '".$wser."' "
					."   AND Pamest = 'on' "
					." GROUP BY Pamhis, Paming, Pamcco, Menpat "
					." 	ORDER BY 1,2 ";
				
				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				$datos_carta_menu = array();
				if ($num > 0){
					 while($row = mysql_fetch_assoc($res)) {
						//	QUITAR LOS PATRONES IGNORADOS DEL PATRON QUE TRAJO DE CARTA MENU
						$row['pat'] = str_replace($patrones_a_ignorar, "", $row['pat']);
						$aux = substr($row['pat'], 0, 1); 
						if( $aux == "," ){ //Si el primer caracter es una coma
							$row['pat'] = substr($row['pat'], 1); 
						}
						$aux = substr($row['pat'], -1);  //Si el ultimo caracter es una coma
						if( $aux == "," ){
							$row['pat'] = substr($row['pat'], 0, -1); 
						}						
						$row['pat'] = explode(",", trim($row['pat']) );
						sort($row['pat']);
						$row['pat'] = implode(",", $row['pat']);

						if ( array_key_exists( $row['cco']."-".$row['pat'], $datos_carta_menu) == false ){
							$datos_carta_menu[ $row['cco']."-".$row['pat'] ] = 1;
						}else{
							$datos_carta_menu[ $row['cco']."-".$row['pat'] ] += 1;
						}
					 }
				}
			//************************************
			//================================================================================================================
			//      PINTAR MATRIZ PRINCIPAL
			//      $main_patrones[$wccocod]            [$valor_patr]   [$whis]     ='';
			//      $main_patrones[Centro de costos]    [Patron]        [Historia]  =''
			//================================================================================================================
		   
			if (isset($hay_resultados) && isset($todos_patrones)){ // si hay resultados para pintar
				@asort($orden_habitaciones);
				 
				//CONSULTAR EL NOMBRE DEL SERVICIO PARA MOSTRARLO EN EL DETALLE
				$q1 = " SELECT sernom 
						  FROM ".$wbasedato."_000076
						 WHERE sercod = '".$wser."' ";
				$resser1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
				$nom_servicio = mysql_fetch_array($resser1);
				//FIN COSULTAR				
				$fecha_hora = date('H:i:s');
				echo "<div class='desplegables' style='width:95%;'>";
				echo "<h3 align=center><div align=right><span>* RESUMEN POR PATRONES Y CENTROS DE COSTO *</span><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span>Última actualización: ".$fecha_hora."&nbsp;<img width='auto' width='15' height='15' border='0' onclick='recargar();' title='Recargar' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></span></div></h3>";
				echo "<div>";
				
				//El siguiente div se crea para que cuando se abre un acordion se busca dentro un elemento con la clase
				//"div_contenido_cargado" y si no existe hace un llamado ajax para traer el contenido
				echo "<div class='div_contenido_cargado'></div>";
				//========================
				//tabla principal
				//========================
				
				echo "<table width='96%'  id='tablaresumenpatronpiso' style='margin-bottom:30;' >"; 
				//========================
				$colspan=count($todos_patrones); 
				$colspan0 = '';
				($colspan >= 17) ? $colspan0 = 26 : $colspan0 = $colspan + 9;
				
				//echo "<tr class=encabezadoTabla>"; 
				//echo "<td colspan='".$colspan0."' style='Font-size:13pt;' align='center'>RESUMEN POR PATRONES Y CENTROS DE COSTO</td>";
				//echo "</tr>";
				echo "<tr class=encabezadoTabla>"; 
					echo "<td colspan='7' align='center' width='21%'><FONT SIZE=3><b>ALERTAS</b></FONT></td>";
					echo "<td rowspan='2' align='center' width='19%' ><FONT SIZE=3><b>CENTRO DE COSTOS</b></FONT></td>";
					$colspan=count($todos_patrones); 
					echo "<td colspan='".(($colspan >= 17) ? 17 : $colspan)."' align='center' width='55%' >
							<table width='100%'>
								<tr>
									<td id='atras' width='5%'  align='center' style='cursor:pointer;Font-size:12pt;' class='parrafo_text' onClick='mostrar_atras_adelante(\"atras\")'><</td>
									<td width='90%' align='center'  style='Font-size:12pt;' class='Encabezadotabla'>PATRON</td>
									<td id='adelante' width='5%' align='center'  style='cursor:pointer;Font-size:12pt;' class='parrafo_text' onClick='mostrar_atras_adelante(\"adelante\")'>></td>
								</tr>
							</table>
						</td>";
					echo "<td rowspan='2' align='center'  width='5%'><FONT SIZE=3><b>TOTAL<b></FONT></td>";
				echo "</tr>";
				$i=1;
				echo "<tr class=encabezadoTabla>";
				echo "<td align='center'><span id='wdie".$i++."' class='msg_tooltip' title='IMPRESIONES PENDIENTES' ><font size=2>Imp.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' class='msg_tooltip' title='MENSAJES<BR>SIN LEER' ><font size=2>Men.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' class='msg_tooltip' title='ADICIONES' ><font size=2>Adi.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' class='msg_tooltip' title='MODIFICACIONES' ><font size=2>Mod.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' class='msg_tooltip' title='CANCELACIONES' ><font size=2>Can.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' class='msg_tooltip' title='TRASLADOS' ><font size=2>Tra.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' class='msg_tooltip' title='POSTQUIRÚRGICOS' ><font size=2>Pqx.</font></span></td>";
			
				//ORDENAR EL ARRAY $todos_patrones SEGUN EL ORDEN DE UBICACION EN EL SISTEMA 
				@asort($orden_patrones);
				foreach ($orden_patrones as $clave_pat => $valor_pat){
					$orden_patrones[$clave_pat]=$todos_patrones[$clave_pat];
				}
				$todos_patrones=$orden_patrones;
				//FIN ORDENAR
				
				//PINTAR EL NOMBRE DE LOS PATRONES A MOSTRAR
				$primera_entrada = 'si';
				$cant_pat_pint = 0;
				$posicion = 0;
				foreach($todos_patrones as $nomb_patr => $valor ){
					if( (isset($minimo_visible) && $minimo_visible > $posicion) || $cant_pat_pint > 16){
						$display = 'none';
					}else
					{
						$display = '';
						$cant_pat_pint++;
					}					
					if (strpos($nomb_patr,","))
					{
						$vect_nomb_patr = explode(',', $nomb_patr);
						$nom_pintar = '';
						foreach($vect_nomb_patr as $valor_nom_patr)
						{
							if ($nom_pintar == '')
								$nom_pintar = $valor_nom_patr;
							else 
								$nom_pintar.= ',<br>'.$valor_nom_patr;
						}
					}
					else
						$nom_pintar = $nomb_patr;
						
					echo "<td class = 'td_patron' rel='".$nomb_patr."' pos='".$posicion."' align='center' style='display:".$display."'><span class='msg_tooltip' id='wdie".$i."' title='".$valor."' ><font size=3>".$nom_pintar."</font></span></td>";
					$i++;
					$posicion++;
				}
				//echo "<td name='adelante' id='adelante' style='width:8px;cursor:pointer;background-color: #FFFFCC;color: #000000;font-size: 5pt;font-weight: bold;' align='center' rowspan='".(count($main_patrones)+1)."' onClick='mostrar_atras_adelante(\"adelante\")'>>></td>";
				echo "</tr>";
				//FIN PINTAR NOMBRE
				
				$wccoant="";
				$wclass="fila1";
				$total_todos_cco=0;
				$increment=1;
							
				foreach($main_patrones as $pk_wccocod => $arr_patrones){ //recorre por centro de costos
				
					if ($wclass=="fila1")
						$wclass="fila2";
					else
						$wclass="fila1";
					
					echo "<tr class='".$wclass."'>";//pintar nombre del centro de costos
													
					$total_cco=0;       
					//recorrer el array = $num_patrones[$wccocod][$valor_patr], se recorre por CC 
					foreach($num_patrones as $pk_wccocod2 => $nom_patron)     
					{
						if($pk_wccocod == $pk_wccocod2)                 
						{
							//PINTAR ALERTAS
							if( isset( $array_alertas[$pk_wccocod] ) ){
								$adix="";
								if( isset($array_alertas[$pk_wccocod]['70DB93']))  $adix=$array_alertas[$pk_wccocod]['70DB93'];
								$modix="";
								if( isset($array_alertas[$pk_wccocod]['007FFF']))  $modix=$array_alertas[$pk_wccocod]['007FFF'];
								$cancex="";
								if( isset($array_alertas[$pk_wccocod]['FFCC00']))  $cancex=$array_alertas[$pk_wccocod]['FFCC00'];
								$trasl="";
								if( isset($array_alertas[$pk_wccocod]['3299CC']))  $trasl=$array_alertas[$pk_wccocod]['3299CC'];
								$posqx = "";
								if( isset($array_alertas[$pk_wccocod]['']))  $posqx=$array_alertas[$pk_wccocod][''];
								
								echo "<td width='3%' align=center onClick='window.open(\"../reportes/Rep_lista_dietas.php?wemp_pmla=".$wemp_pmla."&wcco=".$pk_wccocod."-".$nom_cco[$pk_wccocod]."&wser=".$wser."&activo=on&wfec_i=".date("Y-m-d")."&wfec_f=".date("Y-m-d")."&impresas=on&wtipo=\", \"\", \"\")' style='cursor: pointer'><b><div id='impresion".$pk_wccocod."' class='blink'> &nbsp;".$array_alertas[$pk_wccocod]['impresiones']."</div></b></td>";//Impresiones
								echo "<td width='3%' align=center><b><div class='blink' id='sinLeer2".$increment.$pk_wccocod."'></div></b></td>";//Mensajes sin leer
								echo "<td width='3%' align=center><b><div class='blink' id='adicion".$pk_wccocod."'>".$adix."</div></b></td>";//Adiciones
								echo "<td width='3%' align=center><b><div class='blink' id='modificacion".$pk_wccocod."'>".$modix."</div></b></td>";//Modificaciones
								echo "<td width='3%' align=center><b><div class='blink' id='cancelacion".$pk_wccocod."'>".$cancex."</div></b></td>";//Cancelaciones
								echo "<td width='3%' align=center><b><div class='blink' id='traslados".$pk_wccocod."'>".$trasl."</div></b></td>";//Traslados
								echo "<td width='3%' align=center><b><div class='blink' id='posq".$pk_wccocod."'>".$posqx."</div></b></td>";//Pos quirurjicos
							}else{
								echo "<td width='3%' align=center onClick='window.open(\"../reportes/Rep_lista_dietas.php?wemp_pmla=".$wemp_pmla."&wcco=".$pk_wccocod."-".$nom_cco[$pk_wccocod]."&wser=".$wser."&activo=on&wfec_i=".date("Y-m-d")."&wfec_f=".date("Y-m-d")."&impresas=on&wtipo=\", \"\", \"\")' style='cursor: pointer'><b><div id='impresion".$pk_wccocod."' class='blink'> &nbsp;</div></b></td>";//Impresiones
								echo "<td width='3%' align=center><b><div class='blink' id='sinLeer2".$increment.$pk_wccocod."'></div></b></td>";//Mensajes sin leer
								echo "<td width='3%' align=center><b><div class='blink' id='adicion".$pk_wccocod."'>&nbsp;</div></b></td>";//Adiciones
								echo "<td width='3%' align=center><b><div class='blink' id='modificacion".$pk_wccocod."'>&nbsp;</div></b></td>";//Modificaciones
								echo "<td width='3%' align=center><b><div class='blink' id='cancelacion".$pk_wccocod."'>&nbsp;</div></b></td>";//Cancelaciones
								echo "<td width='3%' align=center><b><div class='blink' id='traslados".$pk_wccocod."'>&nbsp;</div></b></td>";//Traslados
								echo "<td width='3%' align=center><b><div class='blink' id='posq".$pk_wccocod."'>&nbsp;</div></b></td>";//Pos quirurjicos
							}
							//FIN PINTAR ALERTAS
							
							//IMPRIMIR VARIABLES OCULTAS PARA REALIZAR SOLICITUDES AJAX
							$variables_ocultas ="<input type='hidden' class='nom_cco' value='".$nom_cco[$pk_wccocod]."' />";
							$variables_ocultas.="<input type='hidden' class='filtrar' value='no' />";
							$variables_ocultas.="<input type='hidden' class='pk_wccocod' value='".$pk_wccocod."' />";
							$variables_ocultas.="<input type='hidden' class='increment' value='".$increment."' />";
							$variables_ocultas.="<input type='hidden' class='wser_pac' value='".$wser_pac."' />";
							$variables_ocultas.="<input type='hidden' class='nom_servicio' value='".$nom_servicio[0]."' />";
							$variables_ocultas.="<input type='hidden' class='wfec_i' value='".$wfec_i."' />";
							if( isset( $cco_detalle) )
								$variables_ocultas.="<input type='hidden' class='cco_detalle' value='".json_encode($cco_detalle)."' />";
							$variables_ocultas.="<input type='hidden' class='arr_patrones' value='".json_encode($arr_patrones)."' />";
							$variables_ocultas.="<input type='hidden' class='main_pacientes' value='".json_encode($main_pacientes)."' />";
							$variables_ocultas.="<input type='hidden' class='orden_habitaciones' value='".json_encode($orden_habitaciones)."' />";
							
							
							$idxx=$increment.$pk_wccocod;
							echo "<script>
									arCco[ arCco.length ] = $idxx;
							</script>"; 
							
							//mostrar el detalle de todos los pacientes asociados al centro de costo
							echo "<td width='19%' nowrap id='".$pk_wccocod."' align='center' style='cursor:pointer;font-size: 8pt;' onClick='fnMostrarDetalle(\"".$pk_wccocod."\")'>";
						
							echo "<span class='cantidad_pedidos'><b>".$nom_cco[$pk_wccocod]."</b></span>";  
							echo $variables_ocultas;
							
							echo "<div align='center' class='div_detalle' id='detalle".$increment.$pk_wccocod."' style='display:none;cursor:default;background:none repeat scroll 0 0; "
								."position:relative;width:100 %;height:710px;overflow:auto;'><center><br>";
							$id_alertas='';
							
							echo "<input type='HIDDEN' id='centro_costos".$idxx."' name='centro_costos' value='".trim($pk_wccocod)."'>";
							echo "<div id='historicoMensajeria".$idxx."' style='overflow:auto;font-size:10pt;height:80px'>";
							
							//mostrar_detalle($nom_cco[$pk_wccocod], '', '', $arr_patrones, $main_pacientes, 'no', $pk_wccocod, $increment, &$id_alertas, $nom_servicio[0], $orden_habitaciones, $wfec_i, $cco_detalle);
							$increment++;
							echo "</center></div></div></td>";
							//fin mostrar todos
							
							//mostrar detallado por patron
							$cant_pat_pint = 0; 
							$posicion = 0;
							foreach($todos_patrones as $pk_nom_patron =>$valor2)    //se recorre por patrones
							{
								if( (isset($minimo_visible) && $minimo_visible > $posicion) || $cant_pat_pint > 16){
									$display = 'none';
								}else
								{
									$display = '';
									$cant_pat_pint++;
								}
									
								if(array_key_exists($pk_nom_patron, $nom_patron))
								{
									$pk_nom_sin_comas = str_replace(",", "-", $pk_nom_patron);
								
									$variables_ocultas ="<input type='hidden' class='nom_cco' value='".$nom_cco[$pk_wccocod]."' />";
									$variables_ocultas.="<input type='hidden' class='pk_nom_patron' value='".$pk_nom_patron."' />";
									$variables_ocultas.="<input type='hidden' class='valor2' value='".$valor2."' />";
									$arr = array(); $arr[ $pk_nom_patron ] = $arr_patrones[$pk_nom_patron];
									$main_pac_aux = array();
									$orden_habitaciones_aux = array();
									foreach( $arr[ $pk_nom_patron ] as $his_hab_pat=>$valorc ){
										//$main_pac_aux[$his_hab_pat] = array();
										$main_pac_aux[$his_hab_pat] = $main_pacientes[$his_hab_pat];
										$orden_habitaciones_aux[$his_hab_pat] = $orden_habitaciones[$his_hab_pat];
									}
									$variables_ocultas.="<input type='hidden' class='arr_patrones' value='".json_encode($arr)."' />";
									$variables_ocultas.="<input type='hidden' class='main_pacientes' value='".json_encode($main_pac_aux)."' />";
									$variables_ocultas.="<input type='hidden' class='filtrar' value='si' />";
									$variables_ocultas.="<input type='hidden' class='pk_wccocod' value='".$pk_wccocod."' />";
									$variables_ocultas.="<input type='hidden' class='increment' value='".$increment."' />";
									$variables_ocultas.="<input type='hidden' class='wser_pac' value='".$wser_pac."' />";	
									$variables_ocultas.="<input type='hidden' class='nom_servicio' value='".$nom_servicio[0]."' />";									
									$variables_ocultas.="<input type='hidden' class='orden_habitaciones' value='".json_encode($orden_habitaciones_aux)."' />";
									$variables_ocultas.="<input type='hidden' class='wfec_i' value='".$wfec_i."' />";
									if( isset( $cco_detalle) )
										$variables_ocultas.="<input type='hidden' class='cco_detalle' value='".json_encode($cco_detalle)."' />";
									
									//Centro de costos: $pk_wccocod    ----- Patron: $pk_nom_patron
									
									$pk_nom_patron_aux = explode(",", trim($pk_nom_patron) ); //Ordenar por si tiene patron combinado
									sort($pk_nom_patron_aux);
									$pk_nom_patron_aux = implode(",", $pk_nom_patron_aux);
									
									//PARA RESTARLE LA CANTIDAD DE SOLICITUDES CARTA MENU QUE SE HICIERON EN EL PISO-PATRON
									if( array_key_exists( $pk_wccocod."-".$pk_nom_patron_aux, $datos_carta_menu ) ){
										$nom_patron[$pk_nom_patron] = $nom_patron[$pk_nom_patron] - $datos_carta_menu[ $pk_wccocod."-".$pk_nom_patron_aux ];
									}
									echo "<td rel='".$pk_nom_patron."' id='".$pk_wccocod.$pk_nom_sin_comas."' align='center' style='display:".$display.";cursor:pointer;' onClick='fnMostrarDetallePatronPiso(\"".$pk_wccocod.$pk_nom_sin_comas."\")'>";
									echo "<span class='cantidad_pedidos'>".$nom_patron[$pk_nom_patron]."</span>";                       //cantidad del patron
									$total_cco=$total_cco+$nom_patron[$pk_nom_patron];
									
									echo $variables_ocultas;
									
									echo "<div class='div_detalle' id='detalle".$increment.$pk_wccocod."' align='center' style='display:none;cursor:default;background:none repeat scroll 0 0; "
										."position:relative;width:100 %;height:710px;overflow:auto;'><center><br>";
									$id_alertas='';
									//mostrar_detalle($nom_cco[$pk_wccocod], $pk_nom_patron, $valor2, $arr_patrones, $main_pacientes, 'si', $pk_wccocod, &$increment, &$id_alertas, $nom_servicio[0], $orden_habitaciones, $wfec_i, $cco_detalle);
									$increment++;
									echo "</center></div></td>";
									//acumular el total de los patrones
									if (!isset ($total_patrones[$pk_nom_patron]))
										$total_patrones[$pk_nom_patron]=$nom_patron[$pk_nom_patron];
									else
										$total_patrones[$pk_nom_patron]=$total_patrones[$pk_nom_patron]+$nom_patron[$pk_nom_patron];
									//fin acumular
								}
								else{
									echo "<td  rel='".$pk_nom_patron."' align='center' style='display:".$display.";'> - </td>";
								}
								$posicion++;
							}
		
							//total centro de costos
							echo "<td  align='center' id='total".$pk_wccocod."'><b>".$total_cco."</b></td>";
							$total_todos_cco=$total_cco+$total_todos_cco;
							break;
						}					
					}
					echo "</tr>";					
				}
				//PINTAR EL TOTAL DE LOS PATRONES
				echo "<tr>";
				echo "<td colspan='7'></td>";
				echo "<td class=encabezadoTabla align='center'>";
				echo "TOTAL PATRONES";
				echo "</td>";					
				$cant_pat_pint = 0;
				$posicion = 0;
				foreach($todos_patrones as $pk_nom_patron =>$valor2)    //rrecorrer el nombre de todos los patrones que aparecen en el reporte
				{
					if( (isset($minimo_visible) && $minimo_visible > $posicion) || $cant_pat_pint > 16){
						$display = 'none';
					}else{
						$display = '';
						$cant_pat_pint++;
					}						
					if(array_key_exists($pk_nom_patron, $total_patrones))
					{
						$pk_nom_sin_comas = str_replace(",", "-", $pk_nom_patron);
						echo "<td rel='".$pk_nom_patron."' style='display:".$display."' id='total".$pk_nom_sin_comas."' class=encabezadoTabla align='center'>";
						echo $total_patrones[$pk_nom_patron];
						echo "</td>";
					}
					else
						echo "<td rel='".$pk_nom_patron."' style='display:".$display."' align='center' > - </td>";
					
					$posicion++;
				}
				echo "<td class=encabezadoTabla align='center' id='totalescco'>";
				echo $total_todos_cco;
				echo "</td>";
				echo "</tr>";
				//FIN TOTAL PATRONES
				echo "</table>";//cierro tabla principal
				
				echo "</div>"; 
				echo "</div>";//desplegable	
		
				//===========================================================
				//     FIN  MATRIZ PRINCIPAL
				//===========================================================
				
				//===========================================================
				//     PINTAR RESUMEN X PRODUCTO DE PATRONES INDIVIDUALES
				//		$resumenXproducto[$nombre_producto]=$cantidad;	
				//===========================================================
				
				if (isset($resumenXproducto))
				{				
					echo "<div class='desplegables' style='width:95%;'>";
					echo "<h3><b>* RESUMEN POR PRODUCTO DE PATRONES INDIVIDUALES *</b></h3>";
					echo "<div>";
					
					//El siguiente div se crea para que cuando se abre un acordion se busca dentro un elemento con la clase
					//"div_contenido_cargado" y si no existe hace un llamado ajax para traer el contenido
					echo "<div class='div_contenido_cargado'></div>";
				
					$wclass4="fila1";
					echo "<table width= 40%>";
					//echo "<tr align=center class='encabezadoTabla'><td colspan=2><font SIZE=3>RESUMEN POR PRODUCTO DE PATRONES INDIVIDUALES</font></td></tr>";
					echo "<tr align=center class='encabezadoTabla'><td>Nombre</td><td>Cantidad</td></tr>";
					foreach ($resumenXproducto  as $clave_nombre => $valor_cantidad)
					{
						if ($wclass4=="fila1")
							$wclass4="fila2";
						else
							$wclass4="fila1";
						echo "<tr class='".$wclass4."'  style='cursor:pointer;' id='".$clave_nombre."' align=center onClick='fnMostrar(\"".$clave_nombre."\")'>";
							echo '<td align="left">'.$clave_nombre.'</td>';
							echo '<td>'.$valor_cantidad;	//style='display:none;cursor:default;background:none repeat scroll 0 0;position:absolute;height:100%;overflow:auto;'
								echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:710px;overflow:auto;'>";
								echo "<center>";
									mostrar_detalle_productos($productos_pacientes,$clave_nombre, $main_pacientes, $nom_servicio[0]);
								echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' $.unblockUI();' style='width:100'><br><br>";
								echo "</center></div>";
							echo'</td>';
						echo'</tr>';
					}
					echo "</table>";		
					
					echo "</div></div>";
				}
				//===========================================================
				//     FIN PINTAR RESUMEN X PRODUCTO
				//===========================================================			
			}   //FIN SI HAY RESULTADOS
			else
				echo "<BR>NO SE ENCONTRARON RESULTADOS"; 
			 
		}
		 else
			echo "NO SE ENCONTRARON SOLICITUDES PROGRAMADAS";
			
	 //echo "<meta id='refrescar' http-equiv='refresh' content='5;url=Monitor_dietas.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wcco=".$wcco."&wser=".$wser."'>";   		    
	}
	
	function imprimirTablaDePacientesConMenu($wfecha, $wservicio, $wcco){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		if( !isset($wfecha) || !isset($wservicio) || !isset($wcco) ){
			echo "FALTAN DATOS PACMENU";
			return;
		}
		
		echo "<input type='hidden' id='fechap' value='".$wfecha."'>";
		echo "<input type='hidden' id='serviciop' value='".$wservicio."'>";
		echo "<input type='hidden' id='ccop' value='".$wcco."'>";
		
		$ccodatos = explode("-",$wcco);
		$wcco=trim($ccodatos[0]);
		if( $wcco == '%' )
			$wcco = "";
		
		//----PARA LA PAGINACION
		$columnasVisibles = 8; //PAGINACION  Cuantas columnas (con los patrones) seran visibles
		$widthColPatrones = floor(75 / $columnasVisibles); //PAGINACION width de las columnas de patrones( 75% / numero de columnas), la primera(clasificaciones) tendra el 25% 
				
		echo "<input type='hidden' id ='pagina_visible1' value='1' />";		
		$and ="";
		if( $wcco != "")
			$and = "   AND Pamcco = '".$wcco."' ";
		
		$q = "SELECT Pamcco as cco, Menpat as pat "
		    ."  FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B "
			." WHERE B.Pamfec = '".$wfecha."' "
			."   AND Pamide = A.id "
			.$and
			."   AND Menser = '".$wservicio."' "
			."   AND Pamest = 'on' "
			." GROUP BY Pamhis, Paming, Pamcco, Menpat "
			." 	ORDER BY 1,2 ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		$datos = array();
		$patrones_menu = array();
		$ccos_menu = array();
        if ($num > 0){
			 while($row = mysql_fetch_assoc($res)) {			 
				if ( in_array( $row['pat'] , $patrones_menu) == false )
					array_push( $patrones_menu, $row['pat'] );
					
				if ( in_array( $row['cco'] , $ccos_menu) == false )
					array_push( $ccos_menu, $row['cco'] );
				
				if ( array_key_exists( $row['cco'], $datos) == false ){
					$datos[ $row['cco'] ] = array();
					$dato = array();
					$dato['pat'] = $row['pat'];
					$dato['cant'] = 1;
					array_push( $datos[ $row['cco'] ], $dato );
				}else{
					$encontrado = false;
					foreach($datos[ $row['cco'] ] as $pos=>&$datox){
						if( $datox['pat'] == $row['pat'] ){							
							$datox['cant'] = $datox['cant'] + 1;
							$encontrado = true;
						}
					}
					if($encontrado == false){					
						$dato = array();
						$dato['pat'] = $row['pat'];
						$dato['cant'] = 1;
						array_push( $datos[ $row['cco'] ], $dato );
					}
				}
			 }
		}else{
			echo "<br><br>NO SE ENCONTRARON SOLICITUDES DE CARTA MENU PROGRAMADAS";
			return;
		}		
		//echo "<pre>";
		//print_r($datos);
		//echo "</pre>";
		$patrones = consultarListaPatrones();		
		$colspan = 3;
		(count($patrones_menu) > 0) ? $colspan = (count($patrones_menu) + 2) : $colspan = 3;
		
		$max_paginas = ceil((sizeof($patrones_menu)) / $columnasVisibles ); //PAGINACION
		echo "<input type='hidden' id ='paginas1' value='".$max_paginas."' />";
		
		$colspan2 = 0;
		(count($patrones_menu) > $columnasVisibles) ? $colspan2 = $columnasVisibles : $colspan2 = count($patrones_menu);
		
		echo "<div class='div_contenido_cargado'></div>";
		echo "<table id='pacientesconmenu' width='96%'  style='margin-bottom:30;'>";
		//echo "<tr class='encabezadotabla'><td colspan='".$colspan."' style='Font-size:13pt;' align='center'>RESUMEN POR CARTA MENU</td></tr> ";
		echo "<tr class='encabezadotabla'>";
		echo "<td width='21%' align='center' rowspan=2>CENTRO DE COSTOS</td>";
		echo "<td width='55%' colspan='".($colspan2)."' align='center' width='55%' >
							<table width='100%'>
								<tr>
									<td id='atras' onclick='cambiarPagina(\"ant\", 1)' width='5%'  align='center' style='cursor:pointer;Font-size:12pt;' class='parrafo_text'><</td>
									<td width='90%' align='center'  style='Font-size:12pt;' class='Encabezadotabla'>PATRON</td>
									<td id='adelante' onclick='cambiarPagina(\"sig\", 1)' width='5%' align='center'  style='cursor:pointer;Font-size:12pt;' class='parrafo_text'>></td>
								</tr>
							</table>";
		echo "</td>";
        echo "<td rowspan='2' align='center'  width='5%'><FONT SIZE=3><b>TOTAL<b></FONT></td>";
		echo "</tr>";
		echo "<tr class='encabezadotabla'>";
		
		$contador_columnas = 1;
		$ocultar_columna ="";
		foreach( $patrones as $pos=>$patron){
			if ( in_array( $patron['codigo'] , $patrones_menu) == true ){
				if( $contador_columnas > $columnasVisibles ) //PAGINACION
					$ocultar_columna = " style='display:none;' "; //PAGINACION
					
				$numero_de_pagina = ceil( $contador_columnas / $columnasVisibles ); //PAGINACION
				
				echo "<td class='msg_tooltip_carta pag".$numero_de_pagina."' align='center' width='".$widthColPatrones."%' title='".$patron['nombre']."' ".$ocultar_columna.">".$patron['codigo']."</td>";
				$contador_columnas++;
			}
		}
		echo "</tr>";
		
		$totales = array();
		$suma_totales = 0;
		$wclass = 'fila2';
		$wccos = consultaCentrosCostos("ccohos");
		foreach ($wccos as $centroCostos){		
			if ( in_array( $centroCostos->codigo , $ccos_menu) == true ){
				$suma_fila = 0;
				( $wclass == 'fila2' ) ? $wclass = 'fila1' : $wclass = 'fila2';
				echo "<tr class='".$wclass."'>";
				echo "<td align='left'>".$centroCostos->nombre."</td>";
				
				$contador_columnas = 1;
				$ocultar_columna ="";
				$ocultar_columna1 ="";
						
				foreach( $patrones as $pos=>$patron){
					if ( in_array( $patron['codigo'] , $patrones_menu) == true ){
						$encontrado = false;
						
						if( array_key_exists( $patron['codigo'], $totales) == false )
							$totales[ $patron['codigo'] ] = 0;

						if( $contador_columnas > $columnasVisibles ){ //PAGINACION
							$ocultar_columna = " display:none; "; //PAGINACION	
							$ocultar_columna1 = " style='display:none;' "; //PAGINACION	
						}
									
						$numero_de_pagina = ceil( $contador_columnas / $columnasVisibles ); //PAGINACION
		
						foreach( $datos[ $centroCostos->codigo ] as $pos2=> $datoMenu ){
							if( $patron['codigo'] == $datoMenu['pat'] ){
								echo "<td align='center' class='enlace pag".$numero_de_pagina."' onclick='mostrarPacientesMenu( \"".($centroCostos->codigo)."\", \"".$datoMenu['pat']."\", this );' width='".$widthColPatrones."%' patron='".$datoMenu['pat']."' piso='".($centroCostos->codigo)."' style='cursor:pointer; ".$ocultar_columna."'>".$datoMenu['cant']."</td>";
								$totales[ $patron['codigo'] ] = $totales[ $patron['codigo'] ] + $datoMenu['cant'];
								$suma_fila = $suma_fila + $datoMenu['cant'];
								$encontrado = true;
								break;
							}
						}
						if( $encontrado == false )
							echo "<td align='center' class='pag".$numero_de_pagina."' width='".$widthColPatrones."%' ".$ocultar_columna1.">-</td>";
							
						$contador_columnas++;
					}					
				}
				echo "<td align='center'><b>".$suma_fila."</b></td>";//Total
				$suma_totales = $suma_totales+$suma_fila;
				echo "</tr>";
			}
		}
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>TOTALES</td>";
		foreach( $totales as $patroncod=>$cantidad){
			echo "<td align='center'>".$cantidad."</td>";
		}
		echo "<td align='center'>".$suma_totales."</td>";
		echo "</tr>";
		echo "</table>";
	}
	
	function existeMinutaPos($wcodminuta, $wcodservicio, $wpatron ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Msppat "
			."   FROM ".$wbasedato."_000146 "
			."  WHERE Mspmin = '".$wcodminuta."' "
			."    AND Mspser = '".$wcodservicio."' "
			."    AND Msppat = '".$wpatron."' "
			."    AND Msppos = 'on' "
			."    AND Mspest = 'on' ";
		$pos = strrpos($wpatron, ",");	
		//El menu es de patrones combinados
		$pats = array();
		$and = "";
		if ($pos != ''){
			$pats = explode(",", $wpatron );
			foreach( $pats as $pos=>$patron ){
				$and.= " AND  Msppat like '%".$patron."%' ";
			}
			$q = " SELECT * "
				."   FROM ".$wbasedato."_000146 "
				."  WHERE Mspmin = '".$wcodminuta."' "
				."    AND Mspser = '".$wcodservicio."' "
				.$and
				."    AND Msppos = 'on' "
				."    AND Mspest = 'on' ";
		}
			
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
        if ($num > 0 && ($pos == '')){ //Si no es combinacion retornar
			return true;
		}else if( $num > 0 ){//Para controlar que, buscando los productos de N,LC  no traiga los productos de N,LC,HS pues el query trae todos estos con el like por cada patron
			while($row = mysql_fetch_assoc($res)){
				$arreglo = explode(",", $row['Msppat'] );
				if( (count($arreglo) == count($pats))){
					return true;
				}
			}			
		}	
		return false;
	}
	
	function consultarMinutaParaElDia($wfecha){
	
		global $conex;
        global $wbasedato;
		global $fecha_inicial_minutas;
		//minutas activas
		$q = "	SELECT Mincod as cod"
			."    FROM ".$wbasedato."_000145 "
			."   WHERE Minest = 'on' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);	
		
		$minutas = array();
		if ($num > 0){
			while($row = mysql_fetch_assoc($res)){
				array_push($minutas, $row['cod']);
			}
		}
		//Cual es la minuta del dia de hoy?
		$diff = calcularDiferenciaFechas($fecha_inicial_minutas, $wfecha); //dias desde la fecha inicial de las minutas y wfecha
		@$posMinuta = ($diff % ( sizeof( $minutas ) ) );
		$minutaElegida = 1;
		if( $posMinuta >= 0 )
			$minutaElegida = $minutas[ $posMinuta ];
			
		 return $minutaElegida;
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
	
	function imprimirTablaDeProductos2($wfecha, $wservicio, $wcco){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		if( !isset($wfecha) || !isset($wservicio) || !isset($wcco) ){
			echo "FALTAN DATOS";
			return;
		}
		
		$ccodatos = explode("-",$wcco);
		$wcco=trim($ccodatos[0]);
		if( $wcco == '%' )
			$wcco = "";
		
		//Array con el codigo y el nombre de las clasificaciones, para buscar el nombre de la clasificacion teniendo el codigo
		$listaClasificiones = consultarListaClasificaciones();
		
			//----PARA LA PAGINACION
		$columnasVisibles = 8; //PAGINACION  Cuantas columnas (con los patrones) seran visibles
		$widthColPatrones = floor(75 / $columnasVisibles); //PAGINACION width de las columnas de patrones( 75% / numero de columnas), la primera(clasificaciones) tendra el 25% 
				
		echo "<input type='hidden' id ='pagina_visible2' value='1' />";
		
		$minutaElegida = consultarMinutaParaElDia($wfecha);
		echo "<input type='hidden' id ='minuta_del_dia' value='".$minutaElegida."' />";
			
		$and ="";
		if( $wcco != "")
			$and = "	  AND Movcco='".$wcco."' ";
			
		$q = " SELECT Movhis as historia, Moving as ingreso, Movdie as dieta, Movcan as cantidad, ROUND((DATEDIFF(  now(),  pacnac ))/365*12,0) as edad_meses "
			."	 FROM ".$wbasedato."_000077 A, root_000036, root_000037 "
			."	WHERE A.Fecha_data='".$wfecha."' "
			."	  AND Movser='".$wservicio."' "
			.$and		
			."	  AND Movest='on' "
			."    AND Movhis = Orihis "
			."    AND oriori  = '".$wemp_pmla."' "
			."    AND oriced  = pacced "
			."    AND oritid  = pactid " 
			."  ORDER BY Movdie ";		

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);	
		
		$productos_total = array();//Contiene los codigos de los productos a mostrar
		$patrones_menu = array();//Contiene los codigos de los patrones a mostrar
		
		//--------------------PRODUCTOS DE LA MINUTA------------------------
		$productosMinutaGlobal = array(); //Para evitar consultar lo mismo varias veces
		$existeMinutaPosGlobal = array(); //Para evitar consultar lo mismo varias veces
		$datos = array();
		if ($num > 0){
			while($row = mysql_fetch_assoc($res)){			
				if( empty( $row['dieta'] ) ){
					continue;
				}
				$pacienteEsPos = pacienteEsPOS( $row['historia'], $row['ingreso'], $wservicio );
				
				if( $row['edad_meses'] <= 120 ){
						$patt_pediatrico = consultarPatronPediatrico( $row['edad_meses'] );
						if( empty( $row['dieta'] ) == false && empty( $patt_pediatrico ) == false){
							$row['dieta'].=",".$patt_pediatrico;
						}
				}				
				$productos_minuta= "";
				$key = $minutaElegida."-".$wservicio."-".$row['dieta'];
				if( isset( $productosMinutaGlobal[ $key ] )){
					$productos_minuta = $productosMinutaGlobal[$key];
				}else{
					$productos_minuta = consultarProductosMinuta($minutaElegida, $wservicio, $row['dieta'] );
					$productosMinutaGlobal[$key] = $productos_minuta;
				}				
				$existeMinutaPos = "";
				if( isset( $existeMinutaPosGlobal[ $key ] )){
					$existeMinutaPos = $existeMinutaPosGlobal[ $key ];
				}else{
					$existeMinutaPos = existeMinutaPos( $minutaElegida, $wservicio, $row['dieta'] );
					$existeMinutaPosGlobal[ $key ] = $existeMinutaPos;
				}				
				foreach( $productos_minuta as $pos=> $producto ){
					$productoParaPos = $producto['pos'];
					unset( $producto['pos'] );

					//Verificar que no agregue combinados repetidos en desorden, ej: BHGR,HGL y HGL,BHGR
					if( strrpos($row['dieta'], ",") == '' ){					
						if ( in_array( $row['dieta'] , $patrones_menu) == false ){
							array_push( $patrones_menu, $row['dieta'] );							
						}
					}else{
						$existeInvertido = false;
						foreach ( $patrones_menu as $patmenu ){
							$pats1 = explode(",", trim($row['dieta'] ) );
							$pats2 = explode(",", trim($patmenu) );
							sort($pats2); sort($pats1);
							if( $pats1 == $pats2 ){
								$existeInvertido = true;
								break;
							}
						}
						if ( in_array( $row['dieta'] , $patrones_menu) == false && $existeInvertido==false){
							array_push( $patrones_menu, $row['dieta'] );							
						}						
					}					

					if ( array_key_exists( $producto['clasificacion'] , $productos_total) == false )
						$productos_total[ $producto['clasificacion'] ] = array();
					if ( in_array( $producto , $productos_total[ $producto['clasificacion'] ] ) == false )
						array_push( $productos_total[ $producto['clasificacion'] ], $producto );
						
					if ( array_key_exists( $producto['codigo'], $datos) == false ){
						$datos[ $producto['codigo'] ] = array();
						//SOLO SUMA SI,   (El paciente es pos Y MAYOR DE 10 AÑOS Y el producto es POS) o (El paciente es pos Y MENOR DE 10 AÑOS Y el producto no es POS) o ( el paciente no es POS y el producto no es POS) o si NO EXISTE una minuta POS para el patron y el servicio
						//Asi, si el paciente es POS y los productos NO SON POS, se suma SI NO EXISTE una minuta POS, porque se les da lo mismo a los pacientes POS y NO POS
						if( ($pacienteEsPos && $row['edad_meses'] > 120 && $productoParaPos=='on') || ($pacienteEsPos && $row['edad_meses'] <= 120 && $productoParaPos=='off') || ($pacienteEsPos == false && $productoParaPos=='off') || $existeMinutaPos==false){
							$dato = array();
							$dato['pat'] = $row['dieta'];
							$dato['cant'] = $row['cantidad'];
							array_push( $datos[ $producto['codigo'] ], $dato );
						}
					}else{
						$encontrado = false;
						foreach($datos[ $producto['codigo'] ] as $posx=>&$datox){
							if( $datox['pat'] == $row['dieta'] ){
								if( ($pacienteEsPos && $row['edad_meses'] > 120 && $productoParaPos=='on') || ($pacienteEsPos && $row['edad_meses'] <= 120 && $productoParaPos=='off') || ($pacienteEsPos == false && $productoParaPos=='off') || $existeMinutaPos==false){
									$datox['cant'] = $datox['cant'] + $row['cantidad'];
									$encontrado = true;
								}
							}
						}
						if($encontrado == false){	
							if( ($pacienteEsPos && $row['edad_meses'] > 120 && $productoParaPos=='on') || ($pacienteEsPos && $row['edad_meses'] <= 120 && $productoParaPos=='off') || ($pacienteEsPos == false && $productoParaPos=='off') || $existeMinutaPos==false){
								$dato = array();
								$dato['pat'] = $row['dieta'];
								$dato['cant'] = $row['cantidad'];
								array_push( $datos[ $producto['codigo'] ], $dato );
							}
						}
					}
				}
			}
		}else{
			//echo "<br><br>NO SE ENCONTRARON SOLICITUDES PROGRAMADAS";
			return;
		}		
		//Se buscar todas las clasificaciones que no tienen productos, dado que gracias a que la minuta trae
		//los productos para los tipo POS, y que en el piso no hay pacientes POS en algunos casos,
		//Se pueden crear clasificaciones con productos que no tienen cantidad
		foreach($productos_total as $clasi2 =>$prods2){
			$canti = 0;
			foreach( $prods2 as $pos2=>$prod2 ){
				$canti +=( count( $datos[ $prod2['codigo'] ] ) );
			}
			if( $canti == 0 )
				unset( $productos_total[$clasi2] );
		}		
		//echo "<br>Productos de la minuta: ".json_encode( $productos_total )."<br><br>";
		//echo "<br>Lo que hay en datos: ".json_encode( $datos )."<br><br>";
		
		//---------TRAER LAS CLASIFICACIONES PEDIDAS EN LA CARTA MENU, PARA RESTARLE 1 A CADA PRODUCTO CON LA MISMA CLASIFICACION DE LA MINUTA
		$and ="";
		if( $wcco != "")
			$and = "   AND Pamcco = '".$wcco."' ";
			
		$q = "SELECT Procla as clasificacion, Menpat as patron "
			."	FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B, ".$wbasedato."_000082 C "
			."   WHERE B.Pamfec='".$wfecha."' "
			."	 AND Menser='".$wservicio."' "
			."	 AND Pamide = A.id "
			."	 AND Pamest = 'on' "
			."	 AND Menpro = Procod "
			.$and
		."	GROUP BY menpat, procla, pamhis, paming "
		."	ORDER BY procla, pamhis, paming ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		
		if( $num > 0){
			while($row = mysql_fetch_assoc($res)){
				if( array_key_exists($row['clasificacion'], $productos_total) ){
					//Busco los productos de la minuta con la misma clasificacion
					foreach( $productos_total[ $row['clasificacion'] ] as $poscc=> $productoMin ){
						//Busco en $datos que es quien lleva la suma, esos productos con el patron $row['patron'], y le resto 1
						foreach( $datos[ $productoMin['codigo'] ] as $posfg=> &$productoCuenta){
							$patrones1 = explode(',', $productoCuenta['pat'] );
							$patrones2 = explode(',', $row['patron']);
							sort( $patrones1 );
							sort( $patrones2 );
							if( $patrones1  == $patrones2  ){
								$productoCuenta['cant'] = $productoCuenta['cant'] - 1;
								break;
							}
						}
					}
				}
			}
		}
		//---FIN FIN FIN------TRAER LAS CLASIFICACIONES PEDIDAS EN LA CARTA MENU, PARA RESTARLE 1 A CADA PRODUCTO CON LA MISMA CLASIFICACION DE LA MINUTA
		
		//-----------------------------------PRODUCTOS PEDIDOS POR MENU------------------
		$q = " SELECT Procla as clasificacion, Menpro as codigo, Prodes as nombre, Menpat as dieta, count(*) as cantidad"
			."	 FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B, ".$wbasedato."_000082 C "
			."	WHERE B.Pamfec='".$wfecha."' "
			."	  AND Menser='".$wservicio."' "
			."    AND Pamide = A.id"
			."    AND Pamest = 'on' "
			."    AND Menpro = Procod "
			.$and
			."	GROUP BY A.id "
			."  ORDER BY Menpat ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		
		if( $num > 0){
			while($row = mysql_fetch_assoc($res)){
				
				$producto = array();
				$producto['codigo'] = $row['codigo'];
				$producto['nombre'] = $row['nombre'];
				$producto['clasificacion'] = $row['clasificacion'];
			
				//Verificar que no agregue combinados repetidos en desorden, ej: BHGR,HGL y HGL,BHGR
				if( strrpos($row['dieta'], ",") == '' ){					
					if ( in_array( $row['dieta'] , $patrones_menu) == false ){
						array_push( $patrones_menu, $row['dieta'] );							
					}
				}else{
					$existeInvertido = false;
					foreach ( $patrones_menu as $patmenu ){
						$pats1 = explode(",", trim($row['dieta'] ) );
						$pats2 = explode(",", trim($patmenu) );
						sort($pats2); sort($pats1);
						if( $pats1 == $pats2 ){
							$existeInvertido = true;
							break;
						}
					}
					if ( in_array( $row['dieta'] , $patrones_menu) == false && $existeInvertido==false){
						array_push( $patrones_menu, $row['dieta'] );							
					}						
				}

				if ( array_key_exists( $producto['clasificacion'] , $productos_total) == false )
						$productos_total[ $producto['clasificacion'] ] = array();
				if ( in_array( $producto , $productos_total[ $producto['clasificacion'] ] ) == false )
					array_push( $productos_total[ $producto['clasificacion'] ], $producto );
					
				if ( array_key_exists( $producto['codigo'], $datos) == false ){
					$datos[ $producto['codigo'] ] = array();				
					$dato = array();
					$dato['pat'] = $row['dieta'];
					$dato['cant'] = $row['cantidad'];
					array_push( $datos[ $producto['codigo'] ], $dato );
				}else{
					$encontrado = false;
					foreach($datos[ $producto['codigo'] ] as $posx=>&$datox){
						if( $datox['pat'] == $row['dieta'] ){							
							$datox['cant'] = $datox['cant'] + $row['cantidad'];
							$encontrado = true;
						}
					}
					if($encontrado == false){					
						$dato = array();
						$dato['pat'] = $row['dieta'];
						$dato['cant'] = $row['cantidad'];
						array_push( $datos[ $producto['codigo'] ], $dato );
					}
				}
			}
		}
		/*echo "<pre>";
		print_r( $patrones_menu );
		echo "</pre>";*/		
		//echo "<br>Productos de la todo: ".json_encode( $productos_total )."<br><br>";
		//echo "<br>Lo que hay en datos: ".json_encode( $datos )."<br><br>";

		$patrones = consultarListaPatrones();
		
		$max_paginas = ceil((sizeof($patrones_menu)) / $columnasVisibles ); //PAGINACION
		echo "<input type='hidden' id ='paginas2' value='".$max_paginas."' />";
		
		$colspan = 3;
		(count($patrones_menu) > 0) ? $colspan = (count($patrones_menu) + 2) : $colspan = 3;
		
		$colspan2 = 0;
		(count($patrones_menu) > $columnasVisibles) ? $colspan2 = $columnasVisibles : $colspan2 = count($patrones_menu);
		
		echo "<table id='tablaproductos' width='96%'  style='margin-bottom:30;'>";
		echo "<tr class='encabezadotabla'><td colspan='".$colspan."' style='Font-size:13pt;' align='center'>RESUMEN POR COMPONENTES</td></tr> ";
		echo "<tr class='encabezadotabla'>";
		echo "<td width='21%' align='center' rowspan=2>PRODUCTO</td>";
		echo "<td width='55%' colspan='".$colspan2."' align='center' width='55%' >
							<table width='100%'>
								<tr>
									<td id='atras' onclick='cambiarPagina(\"ant\", 2)'  width='5%'  align='center' style='cursor:pointer;Font-size:12pt;' class='parrafo_text'><</td>
									<td width='90%' align='center'  style='Font-size:12pt;' class='Encabezadotabla'>PATRON</td>
									<td id='adelante' onclick='cambiarPagina(\"sig\", 2)'  width='5%' align='center'  style='cursor:pointer;Font-size:12pt;' class='parrafo_text'>></td>
								</tr>
							</table>";
		echo "</td>";
        echo "<td rowspan='2' align='center'  width='5%'><FONT SIZE=3><b>TOTAL<b></FONT></td>";
		echo "</tr>";
		echo "<tr class='encabezadotabla'>";
		$contador_columnas = 1;
		$ocultar_columna ="";
		foreach( $patrones as $pos=>$patron){		
			$existeInvertido = false;
			//COMPROBAR SI EL PATRON COMBINADO QUE TIENE LA MINUTA O EL MENU, ES IGUAL AL DEL PACIENTE, PERO EN DESORDEN. EJ:  BHGR,HGL  Y HGL,BHGR
			if( strrpos($patron['codigo'], ",") != '' ){
				foreach ( $patrones_menu as $patmenu ){
					$pats1 = explode(",", trim($patron['codigo']) );
					$pats2 = explode(",", trim($patmenu) );
					sort($pats2); sort($pats1);
					if( $pats1 == $pats2 ){
						$existeInvertido = true;
						break;
					}
				}
			}
			if ( in_array( $patron['codigo'] , $patrones_menu) == true || $existeInvertido==true ){
				if( $contador_columnas > $columnasVisibles ) //PAGINACION
					$ocultar_columna = " style='display:none;' "; //PAGINACION
					
				$numero_de_pagina = ceil( $contador_columnas / $columnasVisibles ); //PAGINACION
				
				echo "<td align='center' class='msg_tooltip pag".$numero_de_pagina."' width='".$widthColPatrones."%' title='".$patron['nombre']."' ".$ocultar_columna.">".$patron['codigo']."</td>";
				$contador_columnas++;
			}
		}
		echo "</tr>";
		
		$wclass = 'fila2';		
		//-------------------IMPRIMIR PRODUCTOS DE MINUTA Y CARTA MENU----------------
		foreach ($productos_total as $clasifi=>$productos){
			$nombre_cla = "";
			foreach($listaClasificiones as $posc=>$clacodnom){
				if( $clasifi == $clacodnom['codigo'] ){
					$nombre_cla = $clacodnom['nombre'];
					break;
				}
			}
			$wclass = 'fila1';
			echo "<tr><td class='".$wclass."' colspan='".(count($patrones_menu) + 2)."' style='font-size:11pt;' align='left'><b>     * ".$nombre_cla." *</b></td></tr> ";
			foreach( $productos as $producto ){

				$contador_columnas = 1;
				$ocultar_columna1 ="";
				$total_fila = 0;
				$wclass = 'fila2';
				$fila = "<tr class='".$wclass."'>";
				$fila.= "<td>".$producto['nombre']."</td>";
				$suma_fila = 0;
				foreach( $patrones as $pos=>$patron){
					$existeInvertido = false;
					//COMPROBAR SI EL PATRON COMBINADO QUE TIENE LA MINUTA O EL MENU, ES IGUAL AL DEL PACIENTE, PERO EN DESORDEN. EJ:  BHGR,HGL  Y HGL,BHGR
					if( strrpos($patron['codigo'], ",") != '' ){
						foreach ( $patrones_menu as $patmenu ){
							$pats1 = explode(",", trim($patron['codigo']) );
							$pats2 = explode(",", trim($patmenu) );
							sort($pats2); sort($pats1);
							if( $pats1 == $pats2 ){
								$existeInvertido = true;
								break;
							}
						}
					}			
					if ( in_array( $patron['codigo'] , $patrones_menu) == true || $existeInvertido==true){
						$encontrado = false;
						if( $contador_columnas > $columnasVisibles ){ //PAGINACION
							$ocultar_columna1 = " style='display:none;' "; //PAGINACION	
						}									
						$numero_de_pagina = ceil( $contador_columnas / $columnasVisibles ); //PAGINACION
						$canti = 0;						
						foreach( $datos[ $producto['codigo'] ] as $pos2=> $datoMenu ){
							$existeInvertido2 = false;
							//COMPROBAR SI EL PATRON COMBINADO QUE TIENE LA MINUTA O EL MENU, ES IGUAL AL DEL PACIENTE, PERO EN DESORDEN. EJ:  BHGR,HGL  Y HGL,BHGR
							if( strrpos($patron['codigo'], ",") != '' && strrpos($datoMenu['pat'], ",") != '' ){							
								$pats1 = explode(",", trim($patron['codigo']) );
								$pats2 = explode(",", trim($datoMenu['pat']) );
								sort($pats2); sort($pats1);
								if( $pats1 == $pats2 ){
									$existeInvertido2 = true;
								}								
							}
							if( $patron['codigo'] == $datoMenu['pat'] || $existeInvertido2 == true){
								$canti = $datoMenu['cant'];
								if($datoMenu['cant'] == 0) $canti = "-";								
								
								$fila.= "<td align='center' class='enlaceproducto pag".$numero_de_pagina."'  width='".$widthColPatrones."%' patron='".$datoMenu['pat']."' piso='".($producto['codigo'])."' ".$ocultar_columna1.">".$canti."</td>";
								$suma_fila = $suma_fila + $datoMenu['cant'];
								$encontrado = true;
							}
						}
						if( $encontrado == false ){							
							$fila.= "<td align='center' class='pag".$numero_de_pagina."' width='".$widthColPatrones."%' ".$ocultar_columna1.">-</td>";							
						}							
						$contador_columnas++;
					}
				}
				$fila.="<td align='center' ><b>".$suma_fila."</b></td>";//Total
				$fila.="</tr>";
				//SI no hay pedidos del producto, en ningun patron, para que se dibuja?
				//Condicion que surgio al restarle uno a los productos de la clasificacion, por cada paciente que pidio 
				//la misma clasificacion desde una carta menu
				if( $suma_fila > 0 )
					echo $fila;
			}			
		}
		echo "</table>";
	}
	
	function imprimirTablaDeProductos($wfecha, $wservicio, $wcco){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		global $wtabcco;
		
		$width_td = ' width=34 ';
		
		if( !isset($wfecha) || !isset($wservicio) || !isset($wcco) ){
			echo "FALTAN DATOS";
			return;
		}		
		$ccodatos = explode("-",$wcco);
		$wcco=trim($ccodatos[0]);
		if( $wcco == '%' )
			$wcco = "";
		
		//Array con el codigo y el nombre de las clasificaciones, para buscar el nombre de la clasificacion teniendo el codigo
		$listaClasificiones = consultarListaClasificaciones();
		
			//----PARA LA PAGINACION
		$columnasVisibles = 8; //PAGINACION  Cuantas columnas (con los patrones) seran visibles
		$widthColPatrones = floor(75 / $columnasVisibles); //PAGINACION width de las columnas de patrones( 75% / numero de columnas), la primera(clasificaciones) tendra el 25% 
				
		echo "<input type='hidden' id ='pagina_visible2' value='1' />";
		
		$minutaElegida = consultarMinutaParaElDia($wfecha);
		echo "<input type='hidden' id ='minuta_del_dia' value='".$minutaElegida."' />";
			
		//$wcco = '1182';
		$and ="";
		if( $wcco != "")
			$and = "	  AND Movcco='".$wcco."' ";
			
		//BUSCO LOS CENTROS DE COSTO QUE TIENEN PEDIDO DE DIETA
		$qcco = " SELECT Movcco as ccocod, Cconom as cconom "
				."	 FROM ".$wbasedato."_000077 A, ".$wbasedato."_000011 "
				."	WHERE A.Fecha_data='".$wfecha."' "
				."	  AND Movser='".$wservicio."' "
				.$and		
				."	  AND Movest='on' "
				."    AND Movcco = Ccocod "
				."  GROUP BY Movcco"
				."  ORDER BY Movcco ";				
		
		$rescco = mysql_query($qcco,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$arregloPisos = array();//Array con el codigo de los centros de costos		
		$patronesPisos = array();
		$datosPisos = array();		
		$productos_total = array();//Contiene los codigos de los productos a mostrar

		//--------------------PRODUCTOS DE LA MINUTA------------------------
		$productosMinutaGlobal = array(); //Para evitar consultar lo mismo varias veces
		$existeMinutaPosGlobal = array(); //Para evitar consultar lo mismo varias veces		
			
		while( $rowcco = mysql_fetch_array($rescco) ){
			$wcco = $rowcco['ccocod'];
			array_push( $arregloPisos , $rowcco );
				
			$patrones_menu = array();//Contiene los codigos de los patrones a mostrar
			$datos = array();
				
			$and ="";
			if( $wcco != "")
				$and = "	  AND Movcco='".$wcco."' ";
				
			$q = " SELECT Movhis as historia, Moving as ingreso, Movdie as dieta, Movcan as cantidad, ROUND((DATEDIFF(  now(),  pacnac ))/365*12,0) as edad_meses "
				."	 FROM ".$wbasedato."_000077 A, root_000036, root_000037 "
				."	WHERE A.Fecha_data='".$wfecha."' "
				."	  AND Movser='".$wservicio."' "
				.$and		
				."	  AND Movest='on' "
				."    AND Movhis = Orihis "
				."    AND oriori  = '".$wemp_pmla."' "
				."    AND oriced  = pacced "
				."    AND oritid  = pactid " 
				."  ORDER BY Movdie ";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);				
			
			if ($num > 0){
				while($row = mysql_fetch_assoc($res)){
				
					if( empty( $row['dieta'] ) ){
						continue;
					}
					$pacienteEsPos = pacienteEsPOS( $row['historia'], $row['ingreso'], $wservicio );
					
					if( $row['edad_meses'] <= 120 ){
							$patt_pediatrico = consultarPatronPediatrico( $row['edad_meses'] );
							if( empty( $row['dieta'] ) == false && empty( $patt_pediatrico ) == false){
								$row['dieta'].=",".$patt_pediatrico;
							}
					}					
					$productos_minuta= "";
					$key = $minutaElegida."-".$wservicio."-".$row['dieta'];
					if( isset( $productosMinutaGlobal[ $key ] )){
						$productos_minuta = $productosMinutaGlobal[$key];
					}else{
						$productos_minuta = consultarProductosMinuta($minutaElegida, $wservicio, $row['dieta'] );
						$productosMinutaGlobal[$key] = $productos_minuta;
					}					
					$existeMinutaPos = "";
					if( isset( $existeMinutaPosGlobal[ $key ] )){
						$existeMinutaPos = $existeMinutaPosGlobal[ $key ];
					}else{
						$existeMinutaPos = existeMinutaPos( $minutaElegida, $wservicio, $row['dieta'] );
						$existeMinutaPosGlobal[ $key ] = $existeMinutaPos;
					}					
					foreach( $productos_minuta as $pos=> $producto ){
						$productoParaPos = $producto['pos'];
						unset( $producto['pos'] );
						//Verificar que no agregue combinados repetidos en desorden, ej: BHGR,HGL y HGL,BHGR
						if( strrpos($row['dieta'], ",") == '' ){					
							if ( in_array( $row['dieta'] , $patrones_menu) == false ){
								array_push( $patrones_menu, $row['dieta'] );							
							}
						}else{
							$existeInvertido = false;
							foreach ( $patrones_menu as $patmenu ){
								$pats1 = explode(",", trim($row['dieta'] ) );
								$pats2 = explode(",", trim($patmenu) );
								sort($pats2); sort($pats1);
								if( $pats1 == $pats2 ){
									$existeInvertido = true;
									break;
								}
							}
							if ( in_array( $row['dieta'] , $patrones_menu) == false && $existeInvertido==false){
								array_push( $patrones_menu, $row['dieta'] );							
							}						
						}						

						if ( array_key_exists( $producto['clasificacion'] , $productos_total) == false )
							$productos_total[ $producto['clasificacion'] ] = array();
						if ( in_array( $producto , $productos_total[ $producto['clasificacion'] ] ) == false )
							array_push( $productos_total[ $producto['clasificacion'] ], $producto );
							
						if ( array_key_exists( $producto['codigo'], $datos) == false ){
							$datos[ $producto['codigo'] ] = array();
							//SOLO SUMA SI,   (El paciente es pos Y MAYOR DE 10 AÑOS Y el producto es POS) o (El paciente es pos Y MENOR DE 10 AÑOS Y el producto no es POS) o ( el paciente no es POS y el producto no es POS) o si NO EXISTE una minuta POS para el patron y el servicio
							//Asi, si el paciente es POS y los productos NO SON POS, se suma SI NO EXISTE una minuta POS, porque se les da lo mismo a los pacientes POS y NO POS
							if( ($pacienteEsPos && $row['edad_meses'] > 120 && $productoParaPos=='on') || ($pacienteEsPos && $row['edad_meses'] <= 120 && $productoParaPos=='off') || ($pacienteEsPos == false && $productoParaPos=='off') || $existeMinutaPos==false){
								$dato = array();
								$dato['pat'] = $row['dieta'];
								$dato['cant'] = $row['cantidad'];
								array_push( $datos[ $producto['codigo'] ], $dato );
							}
						}else{
							$encontrado = false;
							foreach($datos[ $producto['codigo'] ] as $posx=>&$datox){
								if( $datox['pat'] == $row['dieta'] ){
									if( ($pacienteEsPos && $row['edad_meses'] > 120 && $productoParaPos=='on') || ($pacienteEsPos && $row['edad_meses'] <= 120 && $productoParaPos=='off') || ($pacienteEsPos == false && $productoParaPos=='off') || $existeMinutaPos==false){
										$datox['cant'] = $datox['cant'] + $row['cantidad'];
										$encontrado = true;
									}
								}
							}
							if($encontrado == false){	
								if( ($pacienteEsPos && $row['edad_meses'] > 120 && $productoParaPos=='on') || ($pacienteEsPos && $row['edad_meses'] <= 120 && $productoParaPos=='off') || ($pacienteEsPos == false && $productoParaPos=='off') || $existeMinutaPos==false){
									$dato = array();
									$dato['pat'] = $row['dieta'];
									$dato['cant'] = $row['cantidad'];
									array_push( $datos[ $producto['codigo'] ], $dato );
								}
							}
						}
					}
				}
			}else{
				echo "NO HAY PARA EL PISO";
				//return;
			}
			
			//Se buscar todas las clasificaciones que no tienen productos, dado que gracias a que la minuta trae
			//los productos para los tipo POS, y que en el piso no hay pacientes POS en algunos casos,
			//Se pueden crear clasificaciones con productos que no tienen cantidad
			/*foreach($productos_total as $clasi2 =>$prods2){
				$canti = 0;
				foreach( $prods2 as $pos2=>$prod2 ){
					$canti +=( count( $datos[ $prod2['codigo'] ] ) );
				}
				if( $canti == 0 )
					unset( $productos_total[$clasi2] );
			}*/
			//echo "<br>Productos de la minuta: ".json_encode( $productos_total )."<br><br>";
			//echo "<br>Lo que hay en datos: ".json_encode( $datos )."<br><br>";
			
			//---------TRAER LAS CLASIFICACIONES PEDIDAS EN LA CARTA MENU, PARA RESTARLE 1 A CADA PRODUCTO CON LA MISMA CLASIFICACION DE LA MINUTA
			$and ="";
			if( $wcco != "")
				$and = "   AND Pamcco = '".$wcco."' ";
				
			$q = "SELECT Procla as clasificacion, Menpat as patron "
				."	FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B, ".$wbasedato."_000082 C "
				."   WHERE B.Pamfec='".$wfecha."' "
				."	 AND Menser='".$wservicio."' "
				."	 AND Pamide = A.id "
				."	 AND Pamest = 'on' "
				."	 AND Menpro = Procod "
				.$and
			."	GROUP BY menpat, procla, pamhis, paming "
			."	ORDER BY procla, pamhis, paming ";
			
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			if( $num > 0){
				while($row = mysql_fetch_assoc($res)){
					if( array_key_exists($row['clasificacion'], $productos_total) ){
						//Busco los productos de la minuta con la misma clasificacion
						foreach( $productos_total[ $row['clasificacion'] ] as $poscc=> $productoMin ){
							//Busco en $datos que es quien lleva la suma, esos productos con el patron $row['patron'], y le resto 1
							foreach( $datos[ $productoMin['codigo'] ] as $posfg=> &$productoCuenta){
								$patrones1 = explode(',', $productoCuenta['pat'] );
								$patrones2 = explode(',', $row['patron']);
								sort( $patrones1 );
								sort( $patrones2 );
								if( $patrones1  == $patrones2  ){
									$productoCuenta['cant'] = $productoCuenta['cant'] - 1;
									break;
								}
							}
						}
					}
				}
			}
			//---FIN FIN FIN------TRAER LAS CLASIFICACIONES PEDIDAS EN LA CARTA MENU, PARA RESTARLE 1 A CADA PRODUCTO CON LA MISMA CLASIFICACION DE LA MINUTA
			
			//-----------------------------------PRODUCTOS PEDIDOS POR MENU------------------
			$q = " SELECT Procla as clasificacion, Menpro as codigo, Prodes as nombre, Menpat as dieta, count(*) as cantidad"
				."	 FROM ".$wbasedato."_000147 A, ".$wbasedato."_000148 B, ".$wbasedato."_000082 C "
				."	WHERE B.Pamfec='".$wfecha."' "
				."	  AND Menser='".$wservicio."' "
				."    AND Pamide = A.id"
				."    AND Pamest = 'on' "
				."    AND Menpro = Procod "
				.$and
				."	GROUP BY A.id "
				."  ORDER BY Menpat ";
			
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			if( $num > 0){
				while($row = mysql_fetch_assoc($res)){
					
					$producto = array();
					$producto['codigo'] = $row['codigo'];
					$producto['nombre'] = $row['nombre'];
					$producto['clasificacion'] = $row['clasificacion'];
				
					//Verificar que no agregue combinados repetidos en desorden, ej: BHGR,HGL y HGL,BHGR
					if( strrpos($row['dieta'], ",") == '' ){					
						if ( in_array( $row['dieta'] , $patrones_menu) == false ){
							array_push( $patrones_menu, $row['dieta'] );							
						}
					}else{
						$existeInvertido = false;
						foreach ( $patrones_menu as $patmenu ){
							$pats1 = explode(",", trim($row['dieta'] ) );
							$pats2 = explode(",", trim($patmenu) );
							sort($pats2); sort($pats1);
							if( $pats1 == $pats2 ){
								$existeInvertido = true;
								break;
							}
						}
						if ( in_array( $row['dieta'] , $patrones_menu) == false && $existeInvertido==false){
							array_push( $patrones_menu, $row['dieta'] );							
						}						
					}

					if ( array_key_exists( $producto['clasificacion'] , $productos_total) == false )
							$productos_total[ $producto['clasificacion'] ] = array();
					if ( in_array( $producto , $productos_total[ $producto['clasificacion'] ] ) == false )
						array_push( $productos_total[ $producto['clasificacion'] ], $producto );
						
					if ( array_key_exists( $producto['codigo'], $datos) == false ){
						$datos[ $producto['codigo'] ] = array();					
						$dato = array();
						$dato['pat'] = $row['dieta'];
						$dato['cant'] = $row['cantidad'];
						array_push( $datos[ $producto['codigo'] ], $dato );
					}else{
						$encontrado = false;
						foreach($datos[ $producto['codigo'] ] as $posx=>&$datox){
							if( $datox['pat'] == $row['dieta'] ){							
								$datox['cant'] = $datox['cant'] + $row['cantidad'];
								$encontrado = true;
							}
						}
						if($encontrado == false){					
							$dato = array();
							$dato['pat'] = $row['dieta'];
							$dato['cant'] = $row['cantidad'];
							array_push( $datos[ $producto['codigo'] ], $dato );
						}
					}
				}
			}
			//--------------------------------------------------------------------------------------------------------------------
			/*echo "<br>Productos de la todo: ".json_encode( $productos_total )."<br><br>";
			echo "<br>Lo que hay en datos: ".json_encode( $datos )."<br><br>";
			echo "FIN DEL PISO:".$wcco."<br><br><br><br><br><br><br><br>";*/
			
			//GUARDO LOS PATRONES QUE VA A TENER EL PISO
			$patronesPisos[ $wcco ] = $patrones_menu;
			
			//GUARDO LOS DATOS QUE VA A TENER EL PISO
			$datosPisos[ $wcco ] = $datos;			
			
			$patrones_menu = array();
			$datos = array();			
		}			
			
		$patrones = consultarListaPatrones();
		
		//Se buscar todas las clasificaciones que no tienen productos, dado que la minuta trae
		//los productos para los tipo POS, y que en el piso no hay pacientes POS en algunos casos,
		//Se pueden crear clasificaciones con productos que no tienen cantidad
		
		//SE ELIMINAN LAS CLASIFICACIONES Y LOS PRODUCTOS CUYO TOTAL DE PEDIDOS SEA CERO
		foreach($productos_total as $clasi2 =>$prods2){
			$canti = 0;
			foreach( $prods2 as $pos2=>$prod2 ){
				$cantiProd = 0;
				foreach ( $arregloPisos as $poxx => $centroCostos ){
					$ccoCodigo = $centroCostos['ccocod'];
					if( isset( $datosPisos[ $ccoCodigo ][ $prod2['codigo'] ] )){
						$canti +=( count( $datosPisos[ $ccoCodigo ][ $prod2['codigo'] ] ) );
						foreach (  $datosPisos[ $ccoCodigo ][ $prod2['codigo'] ] as $poxce => $patCant ){
							$cantiProd+= $patCant['cant'];
						}
					}
				}
				if( $cantiProd == 0 ){
					unset($productos_total[$clasi2][$pos2]);
				}
			}
			if( $canti == 0 )
				unset( $productos_total[$clasi2] );
		}
		$contador_columnas = 1;
		$ocultar_columna ="";
		
		if( count($productos_total) == 0 ){
			echo "NO HAY PRODUCTOS PARA MOSTRAR";
			return;
		}

		echo "<div class='div_contenido_cargado'></div>";		
		//echo "<span><font size=3><b>RESUMEN POR COMPONENTES</b></font></span>";
		echo "<br>";
		echo "<div class='enlinea'>";
		echo "<div class='accordion'>";
		echo "<dl>";
		
		//Se imprime la primera tabla con la lista de clasificaciones y sus productos
		echo "<dt>PRODUCTOS</dt>";
		echo "<dd>";
		echo "<p>";
		echo "<table  style='display:inline-block' class='tabla_lista_productos'>";
		echo "<tr class='encabezadotabla'><td>&nbsp;</td></tr>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'><font size=3>Productos</font></td>";
		echo "</tr>";

		foreach ($productos_total as $clasifi=>$productos){
			/*  SE IMPRIME LA CLASIFICACION */
			$nombre_cla = "";
			foreach($listaClasificiones as $posc=>$clacodnom){
				if( $clasifi == $clacodnom['codigo'] ){
					$nombre_cla = $clacodnom['nombre'];
					break;
				}
			}
			$wclass = 'fila1';
			$nombre_claux= $nombre_cla;
			$nombre_cla = substr($nombre_cla, 0, 10);
			echo "<tr><td class='".$wclass." msg_tooltip_prod' title='".$nombre_claux."' style='font-size:11pt;' align='left' nowrap><b>     * ".$nombre_cla." *</b></td></tr> ";
			/*   FIN DE IMPRIMIR CLASIFICACION   */
			foreach( $productos as $producto ){
				/*  SE IMPRIME EL PRODUCTO */
				$contador_columnas = 1;
				$ocultar_columna1 ="";
				$total_fila = 0;
				$wclass = 'fila2';
				echo "<tr class='".$wclass." tr_producto'>";
				$prod_nombre = $producto['nombre'];
				$producto['nombre'] = substr($producto['nombre'], 0, 16);
				echo "<td class='msg_tooltip_prod' title='".$prod_nombre."'>".$producto['nombre']."</td>";
				echo "</tr>";
				$suma_fila = 0;
				/*  FIN SE IMPRIME EL PRODUCTO  */
			}
		}
		echo "</table>";
		
		echo "</p>";
		echo "</dd>";
		
		echo "</dl>";
		echo "</div>";
		echo "</div>";
		
		echo "<div class='enlinea'>";
		echo "<div class='accordion'>";
		echo "<dl>";		
		$totales_productos_piso = array();
		foreach ( $arregloPisos as $poxx => $centroCostos ){
	
				$ccoCodigo = $centroCostos['ccocod'];
				$ccoNombre = $centroCostos['cconom'];
				$codigo_html_tabla = "";
				$codigo_html_tabla.= "<table class='tabla_lista_productos' style='display:inline-block;margin-left:10px;'>";
				
				/*  SE IMPRIME LA FILA DE PATRONES  */
				$codigo_html_tabla_tr_patrones = "<tr class='fila1'>";
				
				$agregoPatron = false;
				$num_patrones_agregados = 0;
				foreach( $patrones as $pos=>$patron){
					$existeInvertido = false;
					//COMPROBAR SI EL PATRON COMBINADO QUE TIENE LA MINUTA O EL MENU, ES IGUAL AL DEL PACIENTE, PERO EN DESORDEN. EJ:  BHGR,HGL  Y HGL,BHGR
					if( strrpos($patron['codigo'], ",") != '' ){
						foreach ( $patronesPisos[ $ccoCodigo ] as $patmenu ){
							$pats1 = explode(",", trim($patron['codigo']) );
							$pats2 = explode(",", trim($patmenu) );
							sort($pats2); sort($pats1);
							if( $pats1 == $pats2 ){
								$existeInvertido = true;
								break;
							}
						}
					}										
					if ( in_array( $patron['codigo'] , $patronesPisos[ $ccoCodigo ]) == true || $existeInvertido==true ){
						if( $contador_columnas > $columnasVisibles ) //PAGINACION
							$ocultar_columna = " style='display:none;' "; //PAGINACION
						$agregoPatron = true;
						$num_patrones_agregados++;
						$numero_de_pagina = ceil( $contador_columnas / $columnasVisibles ); //PAGINACION
						
						$codigo_html_tabla_tr_patrones.= "<td $width_td align='center' class='msg_tooltip_prod pag".$numero_de_pagina."' title='".$patron['nombre']."'>".$patron['codigo']."</td>";						
						$contador_columnas++;
					}
				}
				if( $agregoPatron == false){
					//$codigo_html_tabla_tr_patrones.= "<td>NOIMP</td>";
					continue;
				}					
				$codigo_html_tabla_tr_patrones.= "</tr>";
				
				$codigo_html_tabla.= "<tr class='encabezadotabla'>";
				$codigo_html_tabla.= "<td align='center' class='msg_tooltip_prod' title='".$ccoNombre."' colspan=".$num_patrones_agregados.">".$ccoCodigo."</td>";
				$codigo_html_tabla.= "</tr>";
				$codigo_html_tabla.= $codigo_html_tabla_tr_patrones;		
				
				echo "<dt>$ccoNombre</dt>";
				echo "<dd>";
				echo "<p>";				
				echo $codigo_html_tabla;
				/*  FIN SE IMPRIME LA FILA DE PATRONES  */
				
				$wclass = 'fila2';					
				//-------------------IMPRIMIR PRODUCTOS DE MINUTA Y CARTA MENU----------------
				foreach ($productos_total as $clasifi=>$productos){						
						$suma_fila = 0;
						$wclass = 'fila1';
						echo "<tr><td colspan=".$num_patrones_agregados." class='".$wclass."' style='font-size:11pt;' >&nbsp;</td></tr> ";
						
						foreach( $productos as $producto ){						
								if( array_key_exists($producto['codigo'],  $totales_productos_piso) == false )
									$totales_productos_piso[ $producto['codigo'] ] = 0;
								
								/*  ASI COMO SE IMPRIME EL PATRON RECORREMOS EL ARREGLO DE PATRONES  */
								$fila = "";
								echo "<tr class='fila2 tr_producto'>";
								foreach( $patrones as $pos=>$patron){
									$existeInvertido = false;
									//COMPROBAR SI EL PATRON COMBINADO QUE TIENE LA MINUTA O EL MENU, ES IGUAL AL DEL PACIENTE, PERO EN DESORDEN. EJ:  BHGR,HGL  Y HGL,BHGR
									if( strrpos($patron['codigo'], ",") != '' ){
										foreach ( $patronesPisos[ $ccoCodigo ] as $patmenu ){
											$pats1 = explode(",", trim($patron['codigo']) );
											$pats2 = explode(",", trim($patmenu) );
											sort($pats2); sort($pats1);
											if( $pats1 == $pats2 ){
												$existeInvertido = true;
												break;
											}
										}
									}									
									/* SI EL PATRON DE LA LISTA, ESTA EN LOS IMPRIMIBLES  */
									if ( in_array( $patron['codigo'] , $patronesPisos[ $ccoCodigo ]) == true || $existeInvertido==true){
										$encontrado = false;
										if( $contador_columnas > $columnasVisibles ){ //PAGINACION
											$ocultar_columna1 = " "; //PAGINACION	
										}									
										$numero_de_pagina = ceil( $contador_columnas / $columnasVisibles ); //PAGINACION
										$canti = 0;			
										
										/*  BUSCAMOS EL $DATOS EN LA POSICION DEL PRODUCTO */
										if( isset( $datosPisos[ $ccoCodigo ][ $producto['codigo'] ] ) ){
												foreach( $datosPisos[ $ccoCodigo ][ $producto['codigo'] ] as $pos2=> $datoMenu ){
													$existeInvertido2 = false;
													//COMPROBAR SI EL PATRON COMBINADO QUE TIENE LA MINUTA O EL MENU, ES IGUAL AL DEL PACIENTE, PERO EN DESORDEN. EJ:  BHGR,HGL  Y HGL,BHGR
													if( strrpos($patron['codigo'], ",") != '' && strrpos($datoMenu['pat'], ",") != '' ){							
														$pats1 = explode(",", trim($patron['codigo']) );
														$pats2 = explode(",", trim($datoMenu['pat']) );
														sort($pats2); sort($pats1);
														if( $pats1 == $pats2 ){
															$existeInvertido2 = true;
														}								
													}
													/*  SI EL PRODUCTO SI EXISTE PARA EL PATRON ANTERIOR SE IMPRIME   */
													if( $patron['codigo'] == $datoMenu['pat'] || $existeInvertido2 == true){
														$canti = $datoMenu['cant'];
														if($datoMenu['cant'] == 0) $canti = "-";								
														
														$fila.= "<td $width_td align='center' class='enlaceproducto pag".$numero_de_pagina."'  patron='".$datoMenu['pat']."' piso='".($producto['codigo'])."' ".$ocultar_columna1.">".$canti."</td>";
														$totales_productos_piso[ $producto['codigo'] ]+= $datoMenu['cant'];
														$suma_fila = $suma_fila + $datoMenu['cant'];
														$encontrado = true;
													}
												}
										}										
										/*  EL PRODUCTO NO EXISTIA PARA EL PATRON ANTERIOR, SE IMPRIME -  */
										if( $encontrado == false ){							
											$fila.= "<td $width_td align='center' class='pag".$numero_de_pagina."' ".$ocultar_columna1.">-</td>";							
										}											
										$contador_columnas++;
									}
								}
								//-----------$fila.="<td align='center' ><b>".$suma_fila."</b></td>";//Total
								//-----------$fila.="</tffr>";
								//SI no hay pedidos del producto, en ningun patron, para que se dibuja?
								//Condicion que surgio al restarle uno a los productos de la clasificacion, por cada paciente que pidio 
								//la misma clasificacion desde una carta menu
								if( $suma_fila > 0 ){
									echo $fila;
								}else{
									for( $ii = 0; $ii< $num_patrones_agregados ; $ii++){
										echo "<td $width_td align='center' class='fila2'>-</td>";
									}
								}								
								echo "</tr>";
						}	
				}
				echo "</table>";
				
				echo "</p>";
				echo "</dd>";
				//Imprimiendo tabla con los totales			
		}
		echo "</dl>";
		echo "</div>";//contenedor_accordion
		echo "</div>";
		
		echo "<div class='enlinea'>";
		echo "<div class='accordion'>";
		echo "<dl>";
		echo "<dt>TOTALES</dt>";
		echo "<dd>";
		echo "<p>";
		
		echo "<table class='tabla_lista_productos' style='display:inline-block;'>";
		echo "<tr class='encabezadotabla'><td>&nbsp;</td></tr>";
		echo "<tr class='encabezadotabla'><td align='center'>TOTALES</td></tr>";
		
		foreach ($productos_total as $clasifi=>$productos){
				$wclass = 'fila1';
				echo "<tr><td class='".$wclass."' style='font-size:11pt;' >&nbsp;</td></tr> ";						
				foreach( $productos as $producto ){
					echo "<tr class='fila2 tr_producto'><td align='center'><b>".$totales_productos_piso[ $producto['codigo'] ]."</b></td></tr>";
				}
		}
		echo "</table>";
		echo "</p>";
		echo "</dd>";
	
		echo "</dl>";
		echo "</div>";
		echo "</div>";
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
	
	function consultarNombreUsuario( $wcodigo ){
	
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
	
		$q = " SELECT descripcion "
			."   FROM usuarios "
			."  WHERE codigo = '".$wcodigo."' ";
	
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
			$row = mysql_fetch_array($res);
			return $row[0];
		}
		
		return "";
	}	
	
	function consultarPacientesMenu( $wfecha, $wcodPatron, $wcodservicio, $wcco, $wcodigominuta ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$listaClasificiones = consultarListaClasificaciones();
		$clasificaciones = array();
		foreach ( $listaClasificiones as $clasifi ){
				$clasificaciones[ $clasifi['codigo'] ] = $clasifi['nombre'];
		}
		$listaClasificiones = null;
				
		//Consultar productos pedidos
		$q = " SELECT Pamhis as his, Paming as ing, Clacod as cl, Clades as cldes, A.Menpro as pr,  Prodes as prdes "
			."   FROM ".$wbasedato."_000147 A, ".$wbasedato."_000082, ".$wbasedato."_000083, ".$wbasedato."_000148 B "
			."  WHERE B.Pamfec = '".$wfecha."' "
			."    AND A.Menser = '".$wcodservicio."' "
			."    AND Pamide = A.id "
			."    ANd A.Menpro = Procod "
			."    AND Pamcco = '".$wcco."' "
			."    AND A.Menpat = '".$wcodPatron."'"
			."    AND Clacod = Procla "
			."    AND Pamest = 'on' "
			." 	ORDER BY Pamhis , Paming  ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$datos = array();
        if ($num > 0){
			 while($row = mysql_fetch_assoc($res)) {
				$clave = $row['his']."-".$row['ing'];
				if ( array_key_exists( $clave, $datos) == false )
					$datos[ $clave ] = array();
					
				if ( array_key_exists( $row['cl'], $datos[ $clave ]) == false )
					$datos[ $clave ][$row['cl']] = array();
					
					$dato = array();
					$dato['cl'] = $row['cl'];
					$dato['cldes'] = $row['cldes'];
					$dato['pr'] = $row['pr'];
					$dato['prdes'] = $row['prdes'];
					array_push( $datos[ $clave ][$row['cl']], $dato );
			 }
		}		
		//Consultar productos de la minuta
		$productos_minuta = consultarProductosMinuta($wcodigominuta, $wcodservicio, $wcodPatron );
		$productos_minuta_clasificacion = array();
		foreach( $productos_minuta as $posx=>$producto){
			if ( array_key_exists( $producto['clasificacion'], $productos_minuta_clasificacion) == false )
					$productos_minuta_clasificacion[$producto['clasificacion']] = array();
					
			array_push( $productos_minuta_clasificacion[$producto['clasificacion']], $producto );
		}
		$productos_minuta = null;
		
		$cadena_in = "('".$wcodPatron."')";
		if( strrpos($wcodPatron, ",") != '' ){
			$cadena_in = "(";
			$pats1 = explode(",", trim($wcodPatron) );
			foreach ( $pats1 as $po=>$pa ){
				$cadena_in.="'".$pa."',";
			}
			$cadena_in = substr($cadena_in, 0, -1);  //Quitarle la ultima coma
			$cadena_in.=")";
		}		
		$nombre_patron = "";
		$q = " SELECT Diedes as nombre "
			."   FROM ".$wbasedato."_000041 "
			."  WHERE Diecod IN ".$cadena_in." "; //IN porque puede ser un patron combinado
		$res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);
		if( $num > 0 ){
			while( $row = mysql_fetch_assoc($res) ){
				$nombre_patron.= $row['nombre']." - ";
			}
			$nombre_patron = substr($nombre_patron, 0, -2);  //Quitarle el guion
		}
		$nombre_piso = "";
		$q = " SELECT Cconom as nombre "
			."   FROM ".$wbasedato."_000011 "
			."  WHERE Ccocod = '".$wcco."' ";
		$res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);
		if( $num > 0 ){
			$row = mysql_fetch_assoc($res);
			$nombre_piso = $row['nombre'];
		}
		//Enlace para eliminar la fila auxiliar construida
		echo "<a href='#pacientesconmenu' onclick='quitarFila()'><img src='../../../include/root/cross.png' /></a>";
		
		echo "<table id='tabla_pacientes_menu' style='cursor:default;'>";
		echo "<tr class='encabezadotabla'><td colspan=3 align='center'>RESULTADOS <br> <b>".$nombre_patron."</b>  <br> <b>".$nombre_piso."</b> </td></tr>";
		echo "<tr class='encabezadotabla'><td align='center'>Historia</td><td align='center'>Ingreso</td><td align='center'>Paciente</td></tr>";
		$indice = 1;		
		foreach( $datos as $hising=>$clasifis ){
			
			$claseFila = "filaHistoria".$indice;
			
			$pacArray = explode( "-", $hising );
			$datosPaciente = buscarPaciente($pacArray[0]);
						
			echo "<tr class='fila1' onclick='mostrarProductosPaciente(this)' style='cursor:pointer;'>";
			echo "<td align='center'>".$pacArray[0]."</td>";
			echo "<td align='center'>".$pacArray[1]."</td>";
			echo "<td class='msg_tooltip2' title='".$datosPaciente['tipodoc']." ".$datosPaciente['doc']."'>".$datosPaciente['nombres']."</td>";
			echo "</tr>";
			
			echo "<tr  style='display:none; '>";
			echo "<td colspan=3>";
			echo "<table align='center'>";
			echo "<tr class='encabezadotabla'>";
			echo "<td align='center'><b>PRODUCTOS PARA EL PACIENTE</b></td>";
			echo "</tr>";			
				
			$pacienteEsPos = pacienteEsPOS( $pacArray[0], $pacArray[1], $wcodservicio );
			//IMPRIMIR PRODUCTOS DE LA CARTA MENU	
			foreach( $clasifis as $posx=>$productos ){
				echo "<tr class='fila1' ><td colspan=3><b>*".$productos[0]['cldes']."*</b></td></tr>";
				unset( $productos_minuta_clasificacion[ $posx ] ); //QUITAR LOS PRODUCTOS DE LA MINUTA CON LA MISMA CLASIFICACION
				foreach( $productos as $producto ){
					echo "<tr class='msg_tooltip2 fila2' title='Pedido en carta menu' ><td colspan=3 bgcolor='#DAFFE6'>".$producto['prdes']."</td></tr>";
				}
			}
			//IMPRIMIR LOS PRODUCTOS DE LA MINUTA
			foreach( $productos_minuta_clasificacion as $posy=>$productos ){
				
				$imprimir = "<tr class='fila1' ><td colspan=3><b>*".$clasificaciones[ $posy ]."*</b></td></tr>";
				$tiene = false;
				foreach( $productos as $producto ){
					if( ($pacienteEsPos && $producto['pos'] == 'on' && $datosPaciente['edad_meses'] > 120) || ($pacienteEsPos && $producto['pos'] == 'off' && $datosPaciente['edad_meses'] <= 120) ){
						$imprimir.= "<tr class='fila2 msg_tooltip2' title='De la minuta' ><td colspan=3>".$producto['nombre']."</td></tr>";
						$tiene = true;
					}else if( $pacienteEsPos == false && $producto['pos'] == 'off' ){
						$imprimir.= "<tr class='fila2 msg_tooltip2' title='De la minuta' ><td colspan=3>".$producto['nombre']."</td></tr>";
						$tiene = true;
					}
				}
				if( $tiene ){  //Para que no muestre el titulo de las clasificaciones que no mostro productos, ej: porque el paciente no es pos
					echo $imprimir;
				}
			}
			echo "</table>";
			echo "</td>";
			echo "</tr>";			
			$indice++;			
		}		
		echo "</table>";		
	}
	
	function buscarPaciente( $whis ){
		global $conex;
		global $wemp_pmla;
		
		$query_info = "
			SELECT  datos_paciente.Pactid ,pacientes_id.Oriced,"
				."  datos_paciente.Pacno1, datos_paciente.Pacno2,"
				."  datos_paciente.Pacap1, datos_paciente.Pacap2, ROUND((DATEDIFF(  now(),  pacnac ))/365*12,0) as edad_meses "
		  ."  FROM  root_000037 as pacientes_id, root_000036 as datos_paciente"
		  ." WHERE  pacientes_id.Orihis = '".$whis."'"
		  ."   AND  pacientes_id.Oriori =  '".$wemp_pmla."'"
		  ."   AND 	pacientes_id.Oriced = datos_paciente.Pacced"
		  ."   AND 	Oritid = Pactid";

		$res_info = mysql_query($query_info, $conex);
		$row_datos = mysql_fetch_array($res_info);
		$datos = array('nombres'=>( $row_datos['Pacno1']." ".$row_datos['Pacno2']." ".$row_datos['Pacap1']." ".$row_datos['Pacap2'] ),'doc'=>$row_datos['Oriced'],'tipodoc'=>$row_datos['Pactid'], 'historia'=>$whis, 'edad_meses'=>$row_datos['edad_meses']);
		return $datos;
	}
	
	//Retorna una lista con los patrones disponibles
	function consultarListaPatrones(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Diecod as codigo, Diedes as nombre"
			."   FROM ".$wbasedato."_000041 "
			."  WHERE Dieest = 'on' "
			."    AND Dieind != 'on' ";
		
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
			."	  AND Menest = 'on' "
			."   UNION "
			." SELECT DISTINCT Msppat as patron "
			."   FROM ".$wbasedato."_000146 "
			."  WHERE Msppat like '%,%' "
			."	  AND Mspest = 'on' ";			
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);	
		
		$result = array();		
		if ($num > 0){
			while($row = mysql_fetch_assoc($res)){
			
				$existeInvertido = false;
				//COMPROBAR SI EL PATRON COMBINADO QUE TIENE EL MENU, ES IGUAL AL DEL PACIENTE, PERO EN DESORDEN. EJ:  BHGR,HGL  Y HGL,BHGR
				if( strrpos($row['patron'], ",") != '' ){
					foreach ( $result as $patmenu ){
						$pats1 = explode(",", trim($row['patron']) );
						$pats2 = explode(",", trim($patmenu['codigo']) );
						sort($pats2); sort($pats1);
						if( $pats1 == $pats2 ){
							$existeInvertido = true;
							break;
						}
					}
				}
				if( $existeInvertido == false ){
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
		}		
		return $result;
	}
	
	//Funcion que retorna la diferencia en dias entre dos fechas
	function calcularDiferenciaFechas($fecha1, $fecha2){
		global $conex;
		if( !isset($fecha1) && !isset($fecha2) ){
			return;
		}
		$query = "SELECT DATEDIFF(  '".$fecha1."',  '".$fecha2."' ) as diferencia";
		
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$diferencia = "";
		if($num > 0){
			$row = mysql_fetch_assoc($res);
			$diferencia = abs($row['diferencia']);
		}
		return $diferencia;
	}
	
	//Retorna los productos que hacen parte de una minuta y un servicio
	function consultarProductosMinuta( $wcodminuta, $wcodservicio, $wpatron ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		$q = " SELECT Procla as clasificacion, A.Msppro as codigo, Prodes as nombre, Msppos as pos "
			."   FROM ".$wbasedato."_000146 A, ".$wbasedato."_000082, ".$wbasedato."_000083 "
			."  WHERE A.Mspmin = '".$wcodminuta."' "
			."    AND A.Mspser = '".$wcodservicio."' "
			."    AND A.Msppat = '".$wpatron."' "
			."    AND A.Msppro = Procod "
			."    AND Clacod = Procla "
			."    AND A.Mspest = 'on'";
			
		$pats = array();	
		$pos = strrpos($wpatron, ",");	
		//El menu es de patrones combinados
		if ($pos != ''){
			$pats = explode(",", $wpatron );
			$and = "";
			foreach( $pats as $pos=>$patron ){
				$and.= " AND  Msppat like '%".$patron."%' ";
			}
			if( $and != "" ){
				$q = " SELECT Msppat as pat,Procla as clasificacion, A.Msppro as codigo, Prodes as nombre, Msppos as pos "
					."   FROM ".$wbasedato."_000146 A, ".$wbasedato."_000082, ".$wbasedato."_000083 "
					."  WHERE A.Mspmin = '".$wcodminuta."' "
					."    AND A.Mspser = '".$wcodservicio."' "
					.$and
					."    AND A.Msppro = Procod "
					."    AND Clacod = Procla "
					."    AND A.Mspest = 'on'";
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
				$arreglo = explode(",", $row['pat'] );
				if( (count($arreglo) == count($pats))){
					unset($row['pat']);
					array_push($result, $row);
				}
			}	
		}		
		return( $result );
	}
	
	function vistaInicial($sCodigoSede = NULL){

		global $wemp_pmla;
		global $wactualiz;
		global $user;
		global $wtabcco;
		global $wbasedato;
		global $conex;
		global $wser;
		global $selectsede;

		$sFiltroSede='';
	
		if(isset($wemp_pmla) && !empty($wemp_pmla))
		{
			$estadosede=consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
		
			if($estadosede=='on')
			{
				$codigoSede = (is_null($sCodigoSede)) ? consultarsedeFiltro() : $sCodigoSede;
				$sFiltroSede = (isset($codigoSede) && ($codigoSede !='')) ? " AND Ccosed = '{$codigoSede}' " : "";
			}
		}
		
		echo "<form name='mondietas' action='' method=post>";
		
		encabezado("Monitor Servicio de Alimentación", $wactualiz, 'clinica', TRUE);

		$width_sel = " width: 95%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";		

		echo "<form name='mondietas' action='' method=post>";
		echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='hidden' name='sede' id='sede' value='".$selectsede."'>"; 
		echo "<INPUT type='hidden' id='mensajeriaPrograma' value='cpa'>";
		if (strpos($user,"-") > 0)
			$wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
	
		echo "<center>";
		echo "<table>";
		//Traigo los centros de costos
		$q = "SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom 
				FROM ".$wtabcco.", ".$wbasedato."_000011
			   WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod 
				 AND ccohos  = 'on' 
				 {$sFiltroSede}";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		echo "<tr>";
		echo "<td align=center class='encabezadotabla'><b>Servicio</b></td>";
		echo "<td align=center class='encabezadotabla'><b>Centro de costos</b></td>";			
		echo "<td align=center class='encabezadotabla'><b>Fecha </b></td>"; 			
		echo "</tr>";
		//=================================
		// SELECCIONAR EL SERVICIO
		//=================================
		//Consultar los servicios del maestro
		$q = " SELECT sernom, serhin, serhfi, sercod 
		FROM ".$wbasedato."_000076 
		WHERE serest = 'on' ";
		$resser = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numser = mysql_num_rows($resser);

		echo "<tr class='seccion1'>";

		echo "<td align=center class='fila1'>";
		echo "<SELECT name='wser' id='wser' style='".$width_sel." margin:5px;'>";					
		for ($i=1;$i<=$numser;$i++){
			$rowser = mysql_fetch_array($resser); 
			echo "<OPTION value=".$rowser[3].">".$rowser[0]."</OPTION>";
		} 
		echo "</SELECT>";
		echo "</td>";
		echo "<td align=center class='fila1'>";
		echo "<SELECT name='wcco' id='wcco' style='".$width_sel." margin:5px;'>";
		echo "<option>% - TODOS</option>";
		for ($i=1;$i<=$num;$i++){
			$row = mysql_fetch_array($res); 
			echo "<OPTION>".$row[0]." - ".$row[1]."</OPTION>";
		}
		echo "</SELECT>";
		echo "</td>";
		echo "<td align=center class='fila1'>";					
		//campoFechaDefecto("wfec_i", date("Y-m-d"));
		echo "<input type='text' id='wfec_i' name='wfec_i' value='".date("Y-m-d")."' />";					
		echo "</td>";
		echo "</tr>";
		//=================================
		// SELECCIONAR FECHAS A CONSULTAR
		//=================================

		echo "</table>";
		echo "<input type='button' id='btn_consultar' value='Consultar'>";	
		echo "<br><br>";
		echo "<div name='hidden_minimo' id='hidden_minimo'></div>";		
		echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
		echo "<input type='HIDDEN' id='usuario' name='usuario' value='".$wusuario."'>";
		//echo "<input type='HIDDEN' id='servicio' name='servicio' value='".$wser."'>";

		$estadosede=consultarAliasPorAplicacion($conexion, $wemp_pmla, "filtrarSede");
		$sFiltroSede="";
		$codigoSede = '';
		if($estadosede=='on')
		{	  
			$codigoSede = (isset($selectsede)) ? $selectsede : consultarsedeFiltro();
			$sFiltroSede = (isset($codigoSede) && ($codigoSede != '')) ? " AND Ccosed = '{$codigoSede}' " : "";
		}
	
		$sUrlCodigoSede = ($estadosede=='on') ? '&selectsede='.$codigoSede : '';
		
		echo "<div id='resultados_monitor' style='width:auto;'></div>";
		echo "<br><br>";
		echo "<table>";
			echo "<tr>";  
				echo "<td align=center colspan=7><A href='Monitor_dietas.php?wemp_pmla=".$wemp_pmla.$sUrlCodigoSede."'><b>Retornar</b></A></td>"; 
			echo "</tr>";
		echo "</table>";
		echo "</center>";
		//FIN FORMA
		echo "</form>";		
		echo "<center>";
		echo "<table>"; 
		echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
		echo "</table>";
		echo "</center>";
		
		echo "<center>";
		echo "<div style='display:none;' align='center' id='contenedorModal'><div id='result'></div><input type='button' value='Cerrar' onclick='cerrarModal();'/></div>";
		echo "</center>";
		//Mensaje de alertas
		echo "<div id='msjAlerta' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/root/Advertencia.png'/>";
		echo "<br><br><div id='textoAlerta'></div><br><br>";
		echo '</div>';
		echo "</form>";
	
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
	}
?>

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

<script type="text/javascript">
var intervalCiclo = null;

$(document).ready(function() {
	$("#btn_consultar").click(function(){
		consultarMonitor();
	});
	$("#wfec_i").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true,
	  maxDate:"+1D"
    });	
	
	setInterval(function() {
    
	 $('.blink').effect("pulsate", {}, 5000);
	
	}, 1000);
		
});

//Para que cuando se presione la flecha de arriba y abajo ponga el tr siguiente de color amarillo en la tabla de componentes
$(document).keydown(function(event) {	
	if (event.which == 38 || event.which == 40) {
		event.preventDefault();
	}else{
		return;
	}
	var tr_amarillo = $(this).find('.tr_producto.fondoAmarillo:first');
	if(tr_amarillo.length == 0 )
		return;
		
	if( event.which == 38 ){//Tecla flecha arriba
		if( tr_amarillo.prev().hasClass('tr_producto') ){
			tr_amarillo.prev().trigger("click");
		}else if( tr_amarillo.prev().prev().hasClass('tr_producto') ){
			tr_amarillo.prev().prev().trigger("click");
		}
	}		
	if(event.which == 40 ){//Techa flecha abajo
		if( tr_amarillo.next().hasClass('tr_producto') ){
			tr_amarillo.next().trigger("click");
		}else if( tr_amarillo.next().next().hasClass('tr_producto') ){
			tr_amarillo.next().next().trigger("click");
		}		
	}
});


function recargar(){

	 $.blockUI({ message:	'Espere...',
							css: 	{
										width: 	'auto',
										height: 'auto'
									}
					 });

	consultarMonitor();


}

function convertirAcordion(){
	//Convertir los cuatro cuadros en acordiones verticales
	$( ".desplegables" ).accordion({
		collapsible: true,
		active:false,
		heightStyle: "content",
		icons: null,
		beforeActivate: function( event, ui ) {			
			if( $(this).find('.div_contenido_cargado').length == 0 ){ //Hay que hacer un llamado ajax para cargar el contenido
				var div_contenedor = $(this).find(".contenedor_del_llamado");
				var accion = div_contenedor.attr('accion');
				var id_contenedor = div_contenedor.attr('id');
				consultarContenidoAcordion(accion, id_contenedor);
			}
		}
	});			
	//Desplegar el primer cuadro
	$( ".desplegables:first" ).accordion( "option", "active", 0 );
}

function consultarContenidoAcordion(accion, id_contenedor){
	var wemp_pmla = $("#wemp_pmla").val();
	var wcco = $("#wcco").val();
	var wser = $("#wser").val();
	var wfec_i = $("#wfec_i").val();	
	if( wcco == "" || wser == "" || wfec_i == "" ){
		return;
	}
	var dateReg = /(^\d{4})([-])(\d{2})([-])(\d{2})$/
	if( dateReg.test(wfec_i) == false){
		$("#wfec_i").val('');
		return;
	}
	if( $('#msjEspere').length == 0 ){
		construirDivEsperar();
	}
	//muestra el mensaje de cargando
	$.blockUI({ message: $('#msjEspere') });
	
	//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
	var rango_superior = 245;
	var rango_inferior = 11;
	var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
	//Realiza el llamado ajax con los parametros de busqueda
	$.post('Monitor_dietas.php', { wemp_pmla: wemp_pmla, action: accion, wcco: wcco, wser: wser, wfec_i: wfec_i, consultaAjax: aleatorio} ,
		function(data) {
			$.unblockUI();
			//$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });	
			$('#'+id_contenedor).html(data);
			if( /carta/.test(id_contenedor)){
				$(".msg_tooltip_carta").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			}else{
				$(".msg_tooltip_prod").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				//Convertir la tabla de componentes en acordion horizontal por piso
				$('.accordion').easyAccordion({
						autoStart: false
				});	
				agregarEventoResaltar();
			}
		}
	);	
}

function mostrarPacientesMenu( cco, patron, obj ){
		var fecha = $("#fechap").val();
		var servicio = $("#serviciop").val();
		var minuta = $("#minuta_del_dia").val();
		
		if( minuta == undefined || minuta == '' )
			minuta = 1;
		
		obj = jQuery(obj);//Se convierte en obj jquery
		var cantidad_tds = obj.siblings().length; //cuantos td hay en la fila (pues obj es un td, y siblings son los hmnos)
		cantidad_tds++;
		$(".fila_auxiliar").remove();//elimina todas los tr con la clase fila_auxiliar
		
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
		if( $('#msjEspere').length == 0 ){
			construirDivEsperar();
		}
	    $.blockUI({ message: $('#msjEspere') });
		$.get('Monitor_dietas.php', { wemp_pmla: wemp_pmla, action: "consultarPacientesMenu", fecha: fecha, minuta: minuta, patron: patron, servicio: servicio, cco: cco, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				var code_html = "<tr class='fila2 fila_auxiliar'><td align='center' colspan="+cantidad_tds+">"+data+"</td></tr>";
				obj.parent().after( code_html );//el .parent() me dirige al tr, el .after crea el code_html luego del tr
				//$("#result").html(data);
				//$.blockUI({ css: { top: '50'}, message: $('#contenedorModal') });
				$(".msg_tooltip2").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			});	
	}
	
function cerrarModal(){
	$.unblockUI();
}
	
 //funcion que oculta las columnas de tal manera que se realice la paginación.
function cambiarPagina(accion, tabla){
	var paginas = $("#paginas"+tabla).val();
	var paginador = jQuery("#pagina_visible"+tabla);
	var factor = 1;
	
	tabla_id = "";
	if( tabla == 1 )
		tabla_id = "#pacientesconmenu";
	else if( tabla == 2)
		tabla_id = "#tablaproductos";
	//elaboración de limites
	switch(accion)
	{
		case 'ant':
			factor = (paginador.val()*1)-1;
			break;
		case 'sig':
			factor = (paginador.val()*1)+1;
			break;
	}
	if(factor<1 || factor>paginas){
		return;
	}
	pag_visible = paginador.val();
	$(tabla_id+" .pag"+pag_visible).hide();
	paginador.val( factor );
	$(tabla_id+" .pag"+factor).show();
}


function consultarMonitor(){
	
	clearTimeout(reload);
	reload = setTimeout("consultarMonitor()",300000);	
	var wemp_pmla = $("#wemp_pmla").val();
	var wcco = $("#wcco").val();
	var wser = $("#wser").val();
	var wfec_i = $("#wfec_i").val();
	var selectsede = $("#selectsede").val();
	
	if( wcco == "" || wser == "" || wfec_i == "" ){
		//$("#resultados_lista").hide( 'drop', {}, 500 );
		return;
	}
	var dateReg = /(^\d{4})([-])(\d{2})([-])(\d{2})$/
	if( dateReg.test(wfec_i) == false){
		$("#wfec_i").val('');
		return;
	}
	if( $('#msjEspere').length == 0 ){
		construirDivEsperar();
	}
	//muestra el mensaje de cargando
	$.blockUI({ message: $('#msjEspere') });
	
	//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
	var rango_superior = 245;
	var rango_inferior = 11;
	var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
	//Realiza el llamado ajax con los parametros de busqueda
	$.post('Monitor_dietas.php', { wemp_pmla: wemp_pmla, action: "mostrarMonitor", wcco: wcco, wser: wser, wfec_i: wfec_i, selectsede, consultaAjax: aleatorio} ,
		function(data) {
			$.unblockUI();
			$('#resultados_monitor').html(data);
			$("#resultados_monitor").show( 'drop', {}, 500 );
			$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });	
			inicializarJquery();
			convertirAcordion();			
		});
	
}

//Cuando se da click para que resalte el mismo tr en todas las tablas en el acordion de "Resumen por componentes"
function agregarEventoResaltar(){
	//Las tablas tiene tr de productos, al darle click al tr debe resaltar el mismo tr en todas las tablas
	$(".tr_producto").click(function(){
		$(".tr_producto.fondoAmarillo td").removeClass("fondoAmarillo");//Se le quita el fondo amarillo al tr que lo tenia
		$(".tr_producto.fondoAmarillo").removeClass("fondoAmarillo");//Se le quita el fondo amarillo al tr que lo tenia
		var index_tr = $(this).index();
		$(".tabla_lista_productos").each(function(){
			$(this).find('tr').eq( index_tr ).addClass("fondoAmarillo");
			$(this).find('tr').eq( index_tr ).find('td').addClass("fondoAmarillo");
		});
	});		
}

    arCco = new Array();
	arCcoPat = new Array();	
    
    window.onload=function(){	
		reload = setTimeout("consultarMonitor()",300000);		
	};
	
	function isset(variable_name){
		try {
			 if (typeof(eval(variable_name)) != 'undefined')
			 if (eval(variable_name) != null)
			 return true;
		 } catch(e) { }
		return false;
    }	
    
    function reactivar(id_alertas, piso, pisoCompleto)
	{
		clearTimeout(reload);
		reload = setTimeout("consultarMonitor()",300000);
		if(id_alertas !='')
		{
			var usuario=document.getElementById("usuario").value;
			var wemp_pmla=document.getElementById("wemp_pmla").value;
			
			//actulizar la tabla de auditoria
			var parametros = "consultaAjax=actualizarLog&wemp_pmla="+wemp_pmla+"&usuario="+usuario+"&id_alertas="+id_alertas+""; 
			var resp = consultasAjax( "POST", "Monitor_dietas.php", parametros, false );
			restarAlertas( resp, piso, pisoCompleto );
		}		
	}
	
	function restarAlertas( datos, piso, pisoCompleto ){
		var datosJson = eval( datos );
		
		//Si pisoCompleto=='OK' entonces se eliminan todas las alertas del piso
		if( datosJson.length > 0 && pisoCompleto=='OK'){
			$("#traslados"+piso).text("");
			$("#cancelacion"+piso).text("");
			$("#modificacion"+piso).text("");
			$("#adicion"+piso).text("");
			$("#posq"+piso).text("");
			return;
		}		
		//adicion=70DB93  , modifi=007FFF   , cancel=FFCC00   , trasl=3299CC, postq=''		
		for( i in datosJson){
			if( datosJson[i].color == '70DB93' ){
				var adi = $("#adicion"+piso).text();
				((adi-1) <= 0) ? adi='' : adi=adi-1;
				$("#adicion"+piso).text(adi);
			}else if( datosJson[i].color == '007FFF' ){
				var mod = $("#modificacion"+piso).text();
				((mod-1) <= 0) ? mod='' : mod=mod-1;
				$("#modificacion"+piso).text(mod);
			}else if( datosJson[i].color == 'FFCC00' ){
				var cancel = $("#cancelacion"+piso).text();
				((cancel-1) <= 0) ? cancel='' : cancel=cancel-1;
				$("#cancelacion"+piso).text(cancel);
			}else if( datosJson[i].color == '3299CC' ){
				var trasl = $("#traslados"+piso).text();
				((trasl-1) <= 0) ? trasl='' : trasl=trasl-1;
				$("#traslados"+piso).text(trasl);
			}else{
				var postq = $("#posq"+piso).text();
				((postq-1) <= 0) ? postq='' : postq=postq-1;
				$("#posq"+piso).text(postq);
			}
		}		
	}	
    
	//setInterval("enter()",5000);
	function cerrarVentana(){
        window.close();		  
    }
	    
    function fnMostrar(td){
        var objetotd = document.getElementById(td);
        clearTimeout(reload);   //detener el refresh
		if( $("div", objetotd ).eq(0) )
        {
			$.blockUI({ message: $("div", objetotd ).eq(0), 
							css: {  left: '2%', 
									top:'10%',
								    width: '96%',
									height: 'auto',
									position:'absolute'									
								 } 
					  });
		}
	}
	
	function fnMostrarDetallePatronPiso(td){
	
		//var objetotd = document.getElementById(td);
        clearTimeout(reload);   //detener el refresh
		if( $("#"+td).text() == 0 || $("#"+td).text() == '-' )
			return;
		
		//Consultar las variables ocultas que hay dentro del td para hacer la consulta
		var nom_cco = $("#"+td+" input[class=nom_cco]").val();
		var pk_nom_patron = $("#"+td+" input[class=pk_nom_patron]").val();
		var valor2 = $("#"+td+" input[class=valor2]").val();
		var filtrar = $("#"+td+" input[class=filtrar]").val();
		var pk_wccocod = $("#"+td+" input[class=pk_wccocod]").val();
		var increment = $("#"+td+" input[class=increment]").val();
		var wser_pac = $("#"+td+" input[class=wser_pac]").val();
		var nom_servicio = $("#"+td+" input[class=nom_servicio]").val();
		var wfec_i = $("#"+td+" input[class=wfec_i]").val();
		var cco_detalle = $("#"+td+" input[class=cco_detalle]").val();
		var arr_patrones = $("#"+td+" input[class=arr_patrones]").val();
		var main_pacientes = $("#"+td+" input[class=main_pacientes]").val();
		var orden_habitaciones = $("#"+td+" input[class=orden_habitaciones]").val();
		
		if( cco_detalle == undefined )
			cco_detalle = "";
			
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
		if( $('#msjEspere').length == 0 ){
			construirDivEsperar();
		}
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Monitor_dietas.php', { wemp_pmla: wemp_pmla, action: "mostrarDetallePatronPiso", 
															nom_cco: nom_cco, 
															pk_nom_patron: pk_nom_patron,
															filtrar: filtrar,
															valor2: valor2,
															pk_wccocod: pk_wccocod,
															increment: increment,
															nom_servicio: nom_servicio,
															wser_pac: wser_pac,
															wfec_i: wfec_i,
															cco_detalle: cco_detalle,
															arr_patrones: arr_patrones,
															main_pacientes: main_pacientes,
															orden_habitaciones: orden_habitaciones,															
															consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				
				$("#"+td+" div").eq(0).html(data);
				
				
			
				$.blockUI({ message: $("#"+td+" div").eq(0), 
						css: {  left: '2%', 
								top:'10%',
								width: '96%',
								height: 'auto',
								position:'absolute'									
							 } 
				  });	
				//  inicializarJquery(); 
				//PARA LA MENSAJERIA DIETAS
				var clave = increment+""+pk_wccocod;
				consultarHistoricoTextoProcesado( document.getElementById( "wbasedato" ).value, document.getElementById( "wemp_pmla" ).value, document.getElementById( 'centro_costos'+clave ).value, document.getElementById( 'wser' ).value, document.getElementById( 'mensajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria'+clave ), clave );	//Octubre 11 de 2011
			});
	}
	
	function fnMostrarDetalle(td){
        var objetotd = document.getElementById(td);
        clearTimeout(reload);   //detener el refresh
		
		//Consultar las variables ocultas que hay dentro del td para hacer la consulta
		var nom_cco = $("#"+td+" input[class=nom_cco]").val();
		var filtrar = $("#"+td+" input[class=filtrar]").val();
		var pk_wccocod = $("#"+td+" input[class=pk_wccocod]").val();
		var increment = $("#"+td+" input[class=increment]").val();
		var wser_pac = $("#"+td+" input[class=wser_pac]").val();
		var nom_servicio = $("#"+td+" input[class=nom_servicio]").val();
		var wfec_i = $("#"+td+" input[class=wfec_i]").val();
		var cco_detalle = $("#"+td+" input[class=cco_detalle]").val();
		var arr_patrones = $("#"+td+" input[class=arr_patrones]").val();
		var main_pacientes = $("#"+td+" input[class=main_pacientes]").val();
		var orden_habitaciones = $("#"+td+" input[class=orden_habitaciones]").val();
		
		if( cco_detalle == undefined )
			cco_detalle = "";
			
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
		if( $('#msjEspere').length == 0 ){
			construirDivEsperar();
		}
	    $.blockUI({ message: $('#msjEspere') });
		$.post('Monitor_dietas.php', { wemp_pmla: wemp_pmla, action: "mostrarDetallePiso", 
															nom_cco: nom_cco, 
															filtrar: filtrar,
															pk_wccocod: pk_wccocod,
															increment: increment,
															wser_pac: wser_pac,
															nom_servicio: nom_servicio,
															wfec_i: wfec_i,
															cco_detalle: cco_detalle,
															arr_patrones: arr_patrones,
															main_pacientes: main_pacientes,
															orden_habitaciones: orden_habitaciones,															
															consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$("#"+td+"").find(".div_detalle").html(data);
			
				contenedor = $("#"+td).find(".div_detalle");
			
				$.blockUI({ message: contenedor, 
						css: {  left: '2%', 
								top:'10%',
								width: '96%',
								height: 'auto',
								position:'absolute'									
							 } 
				  });
				  
				 //inicializarJquery();  
				 //PARA LA MENSAJERIA DIETAS
				var clave = increment+""+pk_wccocod;
				if( inArray( arCco, clave ) == false ){ //Si no tiene la clave...
					arCco.push( clave );
				}
				consultarHistoricoTextoProcesado( document.getElementById( "wbasedato" ).value, document.getElementById( "wemp_pmla" ).value, document.getElementById( 'centro_costos'+clave ).value, document.getElementById( 'wser' ).value, document.getElementById( 'mensajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria'+clave ), clave );	//Octubre 11 de 2011
				if( intervalCiclo != null ){ clearInterval(intervalCiclo) };
				intervalCiclo = setInterval( "ciclo( document.getElementById( 'wbasedato' ).value, document.getElementById( 'wemp_pmla' ).value, arCco, document.getElementById( 'wser' ).value, document.getElementById( 'mensajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria'+arCco[0] ), arCco );", mensajeriaTiempoRecarga );
				//intervalCiclo = setInterval( "mensajeriaActualizar()", mensajeriaTiempoRecarga );
				  
			});	
	}
	
	function mostrarProductosPaciente( tr ){
		fila = jQuery( tr );
		fila.next().toggle();
	}

	
	function fnMostrar2(td)
    {
        var objetotd = document.getElementById(td);
        clearTimeout(reload);   //detener el refresh
		if( $("div", objetotd ).eq(0) )
        {
			$.blockUI({ message: $("div", objetotd ).eq(0), 
							css: { left: ( $(window).width() - 600 )/2 +'px', 
								    top: ( $(window).height() - $("div", td ).height() )/2 +'px',
								  width: '600px'
								 }
					  });
			
		}
	}
   
    //Mensajeria
    
    /************************************************************************************
	 * Muestra un mensaje debajo de un elemento
	 ************************************************************************************/
	function mostrar( campo, id ){
		return;
		try{
			clearInterval( interval );
		}
		catch(e){}

		var divTitle = document.getElementById( id );
		
		//divTitle.innerHTML = campo.title;

		divTitle.style.display = '';
		divTitle.style.position = 'absolute';
		divTitle.style.top = parseInt( findPosY(campo) ) + parseInt( campo.offsetHeight ) + 10;
		divTitle.style.left = findPosX( campo );
		divTitle.style.background = "#FFFFDF";
		divTitle.style.borderStyle = "solid";
		divTitle.style.borderwidth = "1px";
	}

	/************************************************************************************
	 * Actualiza los mensjaes sin leer cuando se actualiza la mensajeria
	 ************************************************************************************/
	function alActualizarMensajeria(Cco){
			if( mensajeriaSinLeer > 0 ){
				$("#sinLeer2"+Cco).text( mensajeriaSinLeer ).show();				
			}
			else{
				var campo2 = document.getElementById( "sinLeer2"+Cco );	
				campo2.innerHTML = '';
			}				
			var campo = document.getElementById( "sinLeer"+Cco );
			campo.innerHTML = mensajeriaSinLeer;
			
		
	}

	/**********************************************************************
	 * 
	 **********************************************************************/
    
     function enviandoMensaje(id, centro_costos)
     {
        var textarea = document.getElementById('detalle'+id).getElementsByTagName('textarea')[0];     
        
        if( document.getElementsByTagName('textarea')[0] != '' )
            {
            enviarMensaje2( textarea.value, document.getElementById( 'mensajeriaPrograma' ).value,document.getElementById( 'centro_costos'+id).value, document.getElementById( 'wser' ).value, document.getElementById( "usuario" ).value, document.getElementById( "wbasedato" ).value );
            }
        textarea.value='';
     }
	 
	function marcarLeido( campo, id, cco ){
			
		//campo es una tabla que tiene toda la informacion que se muestra
		//Con dos fila
		//La primera fila tiene dos celdas y la segunda 1
		
		marcandoLeido( document.getElementById( "wbasedato" ).value, id, document.getElementById( "usuario" ).value );
		
		$('#mensajesdietas tr[id^=fila_'+id+']').find(".blink").each(function(){
	
		$(this).stop(true);
		$(this).removeClass('blink');

		});
	}

	/************************************************************************************************
	 *
	 * campo
	 ************************************************************************************************/
	
	
	function marcarPrioridad( campo ){

		//celda en la que se encuentra el boton de guardar
		var celda = 0;
		
		//Campo es el checkbox de prioridad
		fila = campo.parentNode.parentNode;	//Busco la fila en la que se encuentra el checkbox
		
		eval( fila.cells[ celda ].firstChild.href );	//Click en boton guardar
	}
	
	
	function mostrarMensajeConfirmarKardex(){
		return;	//Septiembre 19 de 2011, Se deshabilita mostrar el mensaje para, esto por que viene tal cual el dia anterior
		var msjConfirmarKardex = document.getElementById( 'mostrarConfirmarKardex' );
		
		if(  msjConfirmarKardex && msjConfirmarKardex.value == 'on' ){
			//$.( '#txConfKar' ).blink();
			$.blockUI({ message: $('#msjConfirmarKardex') });		
		}
	}	
		
  
	/*****************************************************************************************************************************
    * Inicializa jquery
    ******************************************************************************************************************************/
    function inicializarJquery()
    {
       

        mostrarMensajeConfirmarKardex();	
		mensajeriaTiempoRecarga = consultasAjax( "POST", "../../../include/movhos/mensajeriaDietas.php", "consultaAjax=4&wemp="+document.getElementById( "wemp_pmla" ).value, false );	
		if( mensajeriaTiempoRecarga == '' || mensajeriaTiempoRecarga == undefined ){ mensajeriaTiempoRecarga = 4; }
        mensajeriaTiempoRecarga = mensajeriaTiempoRecarga*60000;	//El tiempo que se consulta esta en minutos
		
		
		
		mensajeriaActualizarSinLeer = alActualizarMensajeria; 
        for( i=0; i < arCco.length; i++ )
        {	
			/*console.log(i);
			console.log( document.getElementById( "wbasedato" ).value)
			console.log( document.getElementById( "wemp_pmla" ).value)
			console.log( document.getElementById( 'centro_costos'+arCco[i] ).value)
			console.log( document.getElementById( 'wser' ).value)
			console.log( document.getElementById( 'mensajeriaPrograma' ).value)
			console.log( document.getElementById( 'historicoMensajeria'+arCco[i] ) )*/
            consultarHistoricoTextoProcesado( document.getElementById( "wbasedato" ).value, document.getElementById( "wemp_pmla" ).value, document.getElementById( 'centro_costos'+arCco[i] ).value, document.getElementById( 'wser' ).value, document.getElementById( 'mensajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria'+arCco[i] ), arCco[i] );	//Octubre 11 de 2011
        }
        //intervalCiclo = setInterval( "ciclo( document.getElementById( 'wbasedato' ).value, document.getElementById( 'wemp_pmla' ).value, arCco, document.getElementById( 'wser' ).value, document.getElementById( 'mensajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria'+arCco[i] ), arCco );", mensajeriaTiempoRecarga );
        //setInterval( "mensajeriaActualizar()", mensajeriaTiempoRecarga );
    }
	
	//Funcion que es llamada cuando se presiona la imagen de la fila adicional al consultar los pacientes con el patron-piso en el cuadro
	//de RESUMEN POR CARTA MENU
	function quitarFila(){
		$(".fila_auxiliar").remove(); //Se supone que solo hay una fila_aulixiar	
	}
	
	function inArray(a, obj) {
		for (var i = 0; i < a.length; i++) {
			if (a[i] === obj) {
				return true;
			}
		}
		return false;
	}
	//--------------------------------------------
	//	Avanzar o retroceder una columna
	//--------------------------------------------
	function mostrar_atras_adelante(mover)
	{
		var columna_patron = '';
		var posicion;
		var nombre;
		array_visibles = new Array();
		array_todos = new Array();
		
		$('.td_patron').each(
			function(index) 
			{
				posicion = $(this).attr("pos");
				posicion = posicion*1;
				nombre = $(this).attr("rel");
				if($(this).is(':visible'))
				{
					array_visibles[nombre] = posicion;
				}
				array_todos[posicion] = nombre;
			}
		);
		
		var maximo_visible;
		var minimo_visible;
		var nom_max;
		var nom_min;
		var temporal;
		var pat_pintar = '';
		var pat_ocultar = '';
		var primera_vez = 'si';
		
		for (var x in array_visibles)
		{
			temporal = array_visibles[x];
			if(temporal < minimo_visible || primera_vez == 'si')
			{
				minimo_visible=temporal;
				nom_min = x;
			}
			if(temporal > maximo_visible || primera_vez == 'si')
			{
				maximo_visible = temporal;
				nom_max = x;
			}
			primera_vez = 'no';
		}
		
		if(mover == 'adelante')
		{
			pat_pintar = array_todos[((maximo_visible+1)*1)];			
			pat_ocultar = array_todos[minimo_visible];			
			
			if(pat_pintar != undefined)
			{
				pat_pintar=pat_pintar.replace(/\,/gi,"\\,");
				pat_ocultar=pat_ocultar.replace(/\,/gi,"\\,");
				$('td[rel='+pat_pintar+']').show();
				$('td[rel='+pat_ocultar+']').hide();
				maximo_visible = maximo_visible+1;
				minimo_visible = minimo_visible+1;
				$('#hidden_minimo').html('<input type="hidden" id="minimo_visible" name="minimo_visible" value="'+minimo_visible+'">');
				
			}
		}
		else
		{
			if(mover == 'atras')
			{
				pat_pintar = array_todos[((minimo_visible-1)*1)];				
				pat_ocultar = array_todos[maximo_visible];				
				
				if(pat_pintar != undefined)
				{
					pat_pintar=pat_pintar.replace(/\,/gi,"\\,");
					pat_ocultar=pat_ocultar.replace(/\,/gi,"\\,");
					$('td[rel='+pat_pintar+']').show();
					$('td[rel='+pat_ocultar+']').hide();
					maximo_visible = maximo_visible-1;
					minimo_visible = minimo_visible-1;
					$('#hidden_minimo').html('<input type="hidden" id="minimo_visible" name="minimo_visible" value="'+minimo_visible+'">');
				}
			}
		}
		
		if(minimo_visible > 0)
		{
			$('#atras').html('<blink></blink>');
		}
		else
		{
			$('#atras').html('<');
		}
		
		var longitud = array_todos.length;
		if(maximo_visible < ((longitud*1)-1))
		{
			$('#adelante').html('<blink></blink>');
		}
		else
		{
			$('#adelante').html('>');
		}
	}
	
	//En ocaciones elimina el div sin razon aparente
	function construirDivEsperar(){
		var html_codigo = "<div id='msjEspere' style='display:none;'>";
		html_codigo+= '<br>';
		html_codigo+= "<img src='../../images/medical/ajax-loader5.gif'/>";
		html_codigo+= "<br><br> Por favor espere un momento ... <br><br>";
		html_codigo+= '</div>';
		$("#wemp_pmla").after(html_codigo);	
	}
	//-------------------------------
	//	Al cargar la pagina
	//-------------------------------
    /*$(document).ready(function(){
			
			mostrar_atras_adelante('');
		}
	);*/

	$(document).on('change','#selectsede',function(){
		window.location.href = "Monitor_dietas.php?wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val();
	});
	
</script>
<style type="text/css">
	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
	.parrafo_text{
			background-color: #666666;
			color: #FFFFFF;
			font-family: verdana;
			font-size: 10pt;
			font-weight: bold;
	}
	.tabla_productos td{
			width: 35px;
	}
	img { 
		border:0;
	}
	
	.rango1 {
    background: linear-gradient(70deg, green, white);	
	}
	
	.rango2 {
     background: linear-gradient(70deg, gray, white);
	}
	
	.rango3 {
     background: linear-gradient(70deg, orange, white);
	}
	
</style>	
<style>
.easy-accordion h2{margin:0px 0 20px 0;padding:0;font-size:1.6em;}
.easy-accordion{display:block;position:relative;overflow:hidden;padding:0;margin:0}
.easy-accordion dt,.easy-accordion dd{margin:0;padding:0}
.easy-accordion dt,.easy-accordion dd{position:absolute}
.easy-accordion dt{margin-bottom:0;margin-left:0;z-index:5;/* Safari */ -webkit-transform: rotate(-90deg); /* Firefox */ -moz-transform: rotate(-90deg);-moz-transform-origin: 20px 0px; /* Internet Explorer */ filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);cursor:pointer;}
.easy-accordion dd{z-index:1;opacity:0;overflow:hidden}
.easy-accordion dd.active{opacity:1;}
.easy-accordion dd.no-more-active{z-index:2;opacity:1}
.easy-accordion dd.active{z-index:3}
.easy-accordion dd.plus{z-index:4}
.easy-accordion .slide-number{position:absolute;bottom:0;left:10px;font-weight:normal;font-size:1.1em;/* Safari */ -webkit-transform: rotate(90deg); /* Firefox */ -moz-transform: rotate(90deg); /* Internet Explorer */ filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=1);}

/*PARA MOSTRAR ELEMENTOS CON ALINEACION HORIZONTAL*/
.enlinea{
		display:inline-block;
		vertical-align: top;
		/*display: -moz-inline-stack;*/ /* FF2*/
		zoom: 1; /* IE7 (hasLayout)*/
		*display: inline; /* IE */
	}
	
.accordion dl{height:350px}
.accordion dt{border:1px solid #E8EEF7;height:36px;line-height:34px;text-align:right;padding:0 15px 0 0;font-size:0.9em;font-weight:bold;font-family: Tahoma, Geneva, sans-serif;text-transform:uppercase;letter-spacing:1px;background:#C3D9FF;color:#26526c}
.accordion dt.active{cursor:pointer;color:#fff;background:#2A5DB0;}
.accordion dt.hover{background:#E8EEF7; color:#68889b;}
.accordion dt.active.hover{color:#000}
.accordion dd{padding:3px 25px 3px 25px;background:#fff;border:1px solid #2A5DB0;border-left:0;margin-right:3px}
.accordion .slide-number{color:#68889b;left:10px;font-weight:bold}
.accordion .active .slide-number{color:#000;}
.accordion a{color:#68889b}
.accordion .more{padding-top:10px;display:block}


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

p{margin:0;}
</style>
</head>
    <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial($selectsede);
			?>
    </body>
</html>