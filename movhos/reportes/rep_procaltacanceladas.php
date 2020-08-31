<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Reporte de procesos de alta cancelados
 * Fecha		:	2015-06-10
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Reporte para consultar los pacientes que han tenido cancelaciones en el alta en proceso.
 * Condiciones  :  
		
 **********************************************************************************************************/
 
$wactualiz = "2015-06-10";
 
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
	<title>Reporte de Proceso de Alta</title>
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
		$.get('rep_procaltacanceladas.php', { wemp_pmla: wemp_pmla, action: "generarReporte", wcco: wcco, fecha_inicio: fecha_inicio, fecha_fin: fecha_fin, consultaAjax: ''} ,
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
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
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
		
		$tablaTmp = "tmp_procaltcan".$wusuario;	
		 
		$qaux = "DROP TABLE IF EXISTS {$tablaTmp}";
		$res = mysql_query($qaux, $conex);			
		
		$and = "";
		if( $wcco != "" )
			$and = " AND Ccocod = '".trim($wcco)."' ";
			
		$q = "	SELECT orihis as his, oriing as ing, oldUbisac as cco, oldUbihac as hab, oldUbialp as alp, newUbialp as alpn,
					   oldUbifap as fecha_ap, oldUbihap as hora_ap, newUbifap as fecha_apn, newUbihap as hora_apn, modificado,
					   Cconom as cconom, CONCAT( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) as nombrepac
				  FROM log{$wbasedato}_000018, root_000036, root_000037, {$wbasedato}_000011
				 WHERE modificado BETWEEN '".$fecha_inicio." 00:00:00' AND '".$fecha_fin." 23:59:59'
				   AND newUbihis = orihis
				   AND newUbiing = oriing
				   AND oriori = '".$wemp_pmla."'
				   AND oriced = pacced
				   AND oritid = pactid
				   ".$and."
				   AND Ccocod = oldUbisac
				   AND oldUbialp != newUbialp
				   AND Ccohos = 'on'
			   ORDER BY cco, modificado, his, ing
					";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$arr_datos = array();
		
		if ($num > 0){
			while( $row = mysql_fetch_assoc($res) ){
				if( array_key_exists( $row['cco'], $arr_datos ) == false ){
					$arr_datos[ $row['cco'] ] = array();
					$arr_datos[ $row['cco'] ]['nombrecco'] =  $row['cconom'];
					$arr_datos[ $row['cco'] ]['datos'] = array();
				}
				
				$clave = $row['his']."-".$row['ing'];
				if( array_key_exists( $clave, $arr_datos[ $row['cco'] ]['datos'] ) == false ){
					$arr_datos[ $row['cco'] ]['datos'][ $clave ] = array();
					$arr_datos[ $row['cco'] ]['datos'][ $clave ]['nombrepac'] = $row['nombrepac'];
					$arr_datos[ $row['cco'] ]['datos'][ $clave ]['hab'] = $row['hab'];
					$arr_datos[ $row['cco'] ]['datos'][ $clave ]['cambios'] = array();
				}
				array_push($arr_datos[ $row['cco'] ]['datos'][ $clave ]['cambios'], $row );
			}
		}
		
		$html_abrirTablaDetalle = "<tr class='detalle fondoAmarillo' style='display:none;'>"
									."<td colspan=4 align='center'>"
									."<div class='div_detalle' align='center' style='margin: 10 0 10 0;'>"
									."<table class='tabla_detalle' align='center'>"
									."<tr class='encabezadotabla'>
											<td colspan=4 align='center'><font size=3><b>Cambios en el alta en proceso</b></font></td>
									  </tr>"
									."<tr class='encabezadotabla'>											
											<td align='center'>Fecha del cambio</td>
											<td align='center'>Alta en proceso</td>
											<td align='center'>Fecha alta en proceso</td>
											<td align='center'>Hora alta en proceso</td>
									  </tr>";
		
		
	
		echo "<div style='width: 1000px;'>";
	
		foreach($arr_datos as $codCco => $dato ){
			$dato['nombrecco'] = strtoupper( $dato['nombrecco'] );
			
			if(  ccoValido($dato) == false )
				continue;
			
			echo "<div class='desplegables' style='width:100%;'>";
			echo "<h3><b>* ".$dato['nombrecco']." *</b></h3>";
			echo "<div>";
			echo "<table class='entidades' align='center' width='800px'>";
			echo "<tr class='encabezadotabla'>
					<td colspan=6 align='center'><font size=4><b>PACIENTES CON CAMBIOS EN EL PISO ".$dato['nombrecco']."</b></font></td>
				 </tr>";
			echo "<tr class='encabezadotabla'>
					<td align='center'>Historia</td>
					<td align='center'>Ingreso</td>
					<td align='center'>Nombre Paciente</td>
					<td align='center'>Habitación</td>					
				</tr>";
			$class='fila1';
			$classdet='fila1';
			
			foreach( $dato['datos'] as $hisIng=>$fila ){
				if( count($fila['cambios']) == 1 )
					continue;
					
				$his = "";
				$ing = "";
				$aux = explode("-",$hisIng);
				$his = $aux[0];
				$ing = $aux[1];
				($class == "fila2" )? $class = "fila1" : $class = "fila2";
				
				echo "<tr class='".$class."' onclick='mostrarDetalle(this)' style='cursor:pointer;'>
						<td align='center'>".$his."</td>
						<td align='center'>".$ing."</td>
						<td align='left'>".$fila['nombrepac']."</td>
						<td align='center'>".$fila['hab']."</td>										
					 </tr>";

				//Detalle de los pacientes
				echo $html_abrirTablaDetalle;
				$primera_fecha = "";
				$ultima_fecha = "";
				
				$i=0;
				foreach($fila['cambios'] as $filap){
					($classdet == "fila2" )? $classdet = "fila1" : $classdet = "fila2";
					
					$tipo = "";
					$fec = "";
					$hor = "";
					if( $filap['alp'] == "on" && $filap['alpn'] == "off" ){
						$tipo = "CANCELADA";
						$fec = $filap['fecha_ap'];
						$hor = $filap['hora_ap'];
					}else if( $filap['alp'] == "off" && $filap['alpn'] == "on" ){
						$tipo = "INICIADA";
						$fec = $filap['fecha_apn'];
						$hor = $filap['hora_apn'];
					}
					if( $i == 0 ) $primera_fecha = $fec." ".$hor;
					$ultima_fecha = $fec." ".$hor;
					
					echo "<tr class='".$classdet."'>";
						echo "<td align='center'>".$filap['modificado']."</td>";
						echo "<td align='center'>".$tipo."</td>";
						echo "<td align='center'>".$fec."</td>";
						echo "<td align='center'>".$hor."</td>";
					echo "</tr>";
					$i++;
				}
					
				$datetime1 = date_create($primera_fecha);
				$datetime2 = date_create($ultima_fecha);

				$interval = date_diff($datetime1, $datetime2);
				//echo $interval->format('%a días %h horas %i Minute');
				$result = explode("|", $interval->format('%a|%h:%i:%s') );
				$msj =  date("H:i:s", strtotime($result[1]));
				if( $result[0] > 0 )
					$msj = $result[0]." días, ".date("H:i:s", strtotime($result[1]));

				echo "<tr class='fondorojo'>";
					echo "<td align='center'>Total</td>";
					echo "<td colspan=3 align='center'>".$msj." (HH:mm:ss)</td>";				
				echo "</tr>";


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
	
	function ccoValido($arreglo){
		foreach($arreglo['datos'] as $hisIng=>$filax ){
			if( count($filax['cambios']) > 1 )
				return true;			
		}
		return false;
	}
	
	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz, $wbasedato, $wmovhos, $conex;
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		encabezado("REPORTE DE ALTAS EN PROCESO CANCELADAS", $wactualiz, "clinica");

		echo '<div style="width: 100%">';
		$anio = date("Y");
		$anio--;
		$width_sel = " width: 80%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
			
		$q = " SELECT Ccocod as cod, Cconom as nom "
			."   FROM ".$wmovhos."_000011 "
			."  WHERE Ccohos = 'on'
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