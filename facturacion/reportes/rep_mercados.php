<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Reporte de mercados
 * Fecha		:	2015-05-29
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Reporte para consultar los insumos programados para una cirugia que entre las fechas elegidas  
					aun no han sido liquidados.
 * Condiciones  :  
 *********************************************************************************************************
 
 Actualizaciones:

			
 **********************************************************************************************************/
 
$wactualiz = "2015-05-29";
 
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

if( isset($consultaAjax) == false ){
	
?>
	<html>
	<head>
	<title>Reporte de Mercados</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
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

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		
		$(".enlace_retornar").hide();
		$(".enlace_retornar").click(function() {
			restablecer_pagina();
		});
		
		$("#fecha_inicio, #fecha_fin").datepicker({
		  showOn: "button",
		  buttonImage: "../../images/medical/root/calendar.gif",
		  buttonImageOnly: true,
		  maxDate:"+0D"
		});		
	});
	
	function generarReporte(){
		var wemp_pmla = $("#wemp_pmla").val();
		var wcco = $("#wcco").val();
		var fecha_inicio = $("#fecha_inicio").val();
		var fecha_fin = $("#fecha_fin").val();
		
		$.blockUI({ message: $('#msjEspere') });

		//Realiza el llamado ajax con los parametros de busqueda
		$.get('rep_mercados.php', { wemp_pmla: wemp_pmla, action: "generarReporte", wcco: wcco, fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, consultaAjax: ''} ,
			function(data) {
				$.unblockUI();
				$("#contenido").html(data);
				$(".enlace_retornar").show();
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$( ".desplegables" ).accordion({
					collapsible: true,
					active:0,
					heightStyle: "content",
					icons: null
				});
			});
	}
	
	function mostrarDetalle(obj){
		obj = jQuery(obj);
		obj.next(".detalle").toggle();
	}
	
	//Funcion que se activa cuando se presiona el enlace "retornar"
	function restablecer_pagina(){
		$("#contenido").html("");
		$("#wcco").val("");
		$(".enlace_retornar").hide();
	}

</script>
</head>
   
<?php
	
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "generarReporte"){
		generarReporte( $wcco, $fecha_inicio, $fecha_fin );
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************

	
	function generarReporte( $wcco, $fecha_inicio, $fecha_fin ){
		global $conex;
        global $wbasedato;
		global $wtcx;
		global $wmovhos;
		global $wemp_pmla;
		global $wusuario;
		
		$tablaTmp = "tmpMerc".$wusuario;	
		 
		$qaux = "DROP TABLE IF EXISTS {$tablaTmp}";
		$res = mysql_query($qaux, $conex);			
		
		$and = "";
		if( $wcco != "" )
			$and = " AND Ccocod = '".trim($wcco)."' ";
			
		$q = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tablaTmp}
					(INDEX idxtmp(Turtur))
					SELECT Turtur, Turhis, Turnin, Turdoc, Turnom, Ccocod, Cconom
					  FROM {$wtcx}_000011, {$wtcx}_000012, {$wmovhos}_000011
					 WHERE Turfec BETWEEN '".$fecha_inicio."' AND '".$fecha_fin."'					  
					   AND Turqui = Quicod
					   AND Ccocod = Quicco
					   ".$and."
					   AND Turest = 'on'
					";
	
		$rs = mysql_query($q, $conex) or die(mysql_error());	
		

		$q= " SELECT Turhis as his, Turnin as ing, Turtur as tur, Turdoc as doc, Turnom as nompac, Ccocod as cco, Cconom as cconom, 
					 Artcod as art, Artcom as artnom, Artgen as artnomg, ( Mpacan - Mpadev ) as can
				FROM ".$tablaTmp." FORCE INDEX ( idxtmp ), ".$wbasedato."_000207, ".$wmovhos."_000026 A
			   WHERE Turtur = Mpatur
				 AND Mpacom = Artcod
				 AND Mpalux != 'on'
			ORDER BY Ccocod, Artcod
				 ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$arr_datos = array();
		
		if ($num > 0){
			while( $row = mysql_fetch_assoc($res) ){
			
				if( $row['can'] == 0 )
					continue;			
				
				if( array_key_exists( $row['cco'], $arr_datos ) == false ){
					$arr_datos[ $row['cco'] ] = array();
					$arr_datos[ $row['cco'] ]['nombrecco'] = $row['cconom'];
					$arr_datos[ $row['cco'] ]['articulos'] = array();
				}
				if( array_key_exists( $row['art'], $arr_datos[ $row['cco'] ]['articulos'] ) == false ){
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ] = array();
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['artnom'] = $row['artnom'];
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['artnomg'] = $row['artnomg'];
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['cantidad'] = 0;
					$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['pacientes'] = array();
				}
				$arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['cantidad']+= $row['can'];
				
				array_push( $arr_datos[ $row['cco'] ]['articulos'][ $row['art'] ]['pacientes'], $row );
			}
		}
		
		$html_abrirTablaDetalle = "<tr class='detalle fondoAmarillo' style='display:none;'>"
									."<td colspan=6 align='center'>"
									."<div class='div_detalle' align='center' style='margin: 10 0 10 0;'>"
									."<table class='tabla_detalle' align='center'>"
									."<tr class='encabezadotabla'>
											<td colspan=5 align='center'><font size=3><b>Pacientes</b></font></td>
									  </tr>"
									."<tr class='encabezadotabla'>											
											<td align='center'>Historia</td>
											<td align='center'>Ingreso</td>			
											<td align='center'>Nombre</td>
											<td align='center'>Turno</td>
											<td align='center'>Cantidad</td>											
									  </tr>";
		
		
		
		echo "<div style='width: 1000px;'>";
	
		foreach($arr_datos as $dato ){
			$dato['nombrecco'] = strtoupper( $dato['nombrecco'] );
			
			echo "<div class='desplegables' style='width:100%;'>";
			echo "<h3><b>* ".$dato['nombrecco']." *</b></h3>";
			echo "<div>";
			echo "<table class='entidades' align='center' width='800px'>";
			echo "<tr class='encabezadotabla'>
					<td colspan=6 align='center'><font size=4><b>ARTICULOS SIN LIQUIDAR DEL PISO ".$dato['nombrecco']."</b></font></td>
				 </tr>";
			echo "<tr class='encabezadotabla'>
					<td align='center'>Codigo</td>
					<td align='center'>Nombre Comercial</td>
					<td align='center'>Nombre Genérico</td>
					<td align='center'>Cantidad</td>					
				</tr>";
			$class='fila1';
			$classdet='fila1';
			
			$historia = "";
			$ingreso = "";
			$fecha_data = "";
			
			//Ordenar por cantidad			
			uasort($dato['articulos'], function ($a, $b) { return $b['cantidad'] - $a['cantidad']; });
			
			foreach( $dato['articulos'] as $codArt=>$fila ){

				($class == "fila2" )? $class = "fila1" : $class = "fila2";
				
				echo "<tr class='".$class."' onclick='mostrarDetalle(this)' style='cursor:pointer;'>
						<td align='center'>".$codArt."</td>
						<td align='left'>".$fila['artnom']."</td>
						<td align='left'>".$fila['artnomg']."</td>						
						<td align='right'>".number_format((double)$fila['cantidad'],0,'.',',')."</td>						
					 </tr>";

				//Detalle de los pacientes
				echo $html_abrirTablaDetalle;
				
				//Ordenar por cantidad
				uasort($fila['pacientes'], function ($a, $b) { return $b['can'] - $a['can']; });
				
				foreach($fila['pacientes'] as $filap){
					($classdet == "fila2" )? $classdet = "fila1" : $classdet = "fila2";		
					echo "<tr class='".$classdet."'>";						
						echo "<td align='center'>".$filap['his']."</td>";
						echo "<td align='center'>".$filap['ing']."</td>";
						echo "<td align='left'>".$filap['nompac']."</td>";
						echo "<td align='center'>".$filap['tur']."</td>";
						echo "<td align='right'>".number_format((double)$filap['can'],0,'.',',')."</td>";
					echo "</tr>";
				}
				echo "</table>"; // cerrar la tabla_detalle
				echo "</div>"; // cerrar el div_detalle
				echo "</td>";
				echo "</tr>"; //cerrar el tr class='detalle'
			}			
			echo "</table>";
			echo "</div>";
			echo "</div>";
		}
		
		if( count( $arr_datos ) == 0 ){
			echo "<font size=4><b>SIN RESULTADOS</b></font>";
		}
		echo "<br><br>";
		
		/*echo "<center>";		
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "</center>";*/
		
		echo "</div>";
	}
	
	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz, $wbasedato, $wmovhos, $conex;
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		encabezado("REPORTE DE MERCADOS", $wactualiz, "clinica");

		echo '<div style="width: 100%">';
		$anio = date("Y");
		$anio--;
		$width_sel = " width: 80%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
			
		$q = " SELECT Ccocod as cod, Cconom as nom "
			."   FROM ".$wmovhos."_000011 "
			."  WHERE Ccocir = 'on'
			ORDER BY Ccoord";
		$res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);
				
		//------------TABLA DE PARAMETROS-------------		
		echo '<table align="center">';
		/*echo "<tr class='encabezadotabla'>";
		echo "<td colspan=2 align='center'><font size=4>FILTROS</font></td>";
		echo "</tr>";*/
		echo "<tr>";
		echo "<td colspan=2 align='center' class='encabezadotabla'>Centro de Costos</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td colspan=2 class='fila2' align='center'>";
		
		echo "<select id='wcco' style='".$width_sel." margin:5px;'>";
		echo "<option value=''>TODOS</option>";
		if( $num > 0 ){		
			while( $row = mysql_fetch_array($res) ){
				echo "<option value=".$row['cod'].">".$row['nom']."</option>";
			}
		}
		echo "</select>";
		
		echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td colspan=2 align='center' class='encabezadotabla'>Fecha</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td class='fila2' align='center'>";
		echo "<input type='text' id='fecha_inicio' value='".date("Y-m-d")."' disabled placeholder=' '>";
		echo "</td>";
		echo "<td class='fila2' align='center'>";
		echo "<input type='text' id='fecha_fin' value='".date("Y-m-d")."' disabled placeholder=' '>";		
		echo "</td>";
		echo "</tr>";

		echo "<tr class='fila2'>";
		echo "<td colspan=2 align='center'>
				<input type='button' value='Generar' onclick='generarReporte()' />
			  </td>";
		echo "</tr>";
		echo "</table>";
		//------------FIN TABLA DE PARAMETROS-------------

		echo "</div>";//Gran contenedor
		echo '<center>';		
		//mostrarTablaEntidades("2012");
		echo "<br><br>";
		echo "<a class='enlace_retornar' href='#' >RETORNAR</a>";

		
		
		echo "<br><br>"; 
		echo "<br><br>";
		
		echo "<div id='contenido' style='display:;'></div>";
		
		//Mensaje de espera		
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		
		echo "<br><br>";
		
		echo "<center>";		
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "</center>";
	}
	
?>
 <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>