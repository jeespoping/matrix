<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Reporte farmaco-vigilancia
 * Fecha		:	2015-05-25
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Reporte para consultar los pacientes HOSPITALIZADOS a quienes se les ha ORDENADO alguno
					de los medicamentos del maestro movhos_000172 durante el DIA ACTUAL(o del dia de ayer)
 * Condiciones  :  
 *********************************************************************************************************
 
 Actualizaciones:
 
 Mayo 3 de 2017 (Jonatan Lopez): Se agrega al filtro el responsable y en los articulos el nombre generico.
 --------------------------	
 Abril 27 de 2017 (Jonatan Lopez): Se agrega el responsable en el resultado de la consulta, ademas se cambia el seleccionado de articulos por un buscador de
									articulos y un calendario de consulta.
			
 **********************************************************************************************************/
 
$wactualiz = "2017-05-03";
 
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
	<title>Reporte medicamentos por paciente</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<style>
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
		#tooltip h3, #tooltip div{margin:0; width:auto}
	</style>
<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		
		$(".enlace_retornar").hide();
		$(".enlace_retornar").click(function() {
			restablecer_pagina();
		});
	});
	
	$(document).ready(function(){
		
		var wmed = $("#wmed").val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		$( "#wmed" ).autocomplete({
		  source: "rep_farmacovigilancia.php?wmed="+wmed+"&wemp_pmla="+wemp_pmla+"&consultaAjax=''&action=consultar_medicamento",
		  minLength: 1
		});
		
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
			
	
	    $("#wfecha").datepicker({
			

	   });
	 
	});
	
	function generarReporte(){
		
		var wemp_pmla = $("#wemp_pmla").val();
		var wcco = $("#wcco").val();
		var wmed = $("#wmed").val();
		var wfecha = $("#wfecha").val();
		var responsable = $("#responsable").val();
				
		$.blockUI({ message: $('#msjEspere') });

		//Realiza el llamado ajax con los parametros de busqueda
		$.get('rep_farmacovigilancia.php', 
					{ 
						wemp_pmla: wemp_pmla, 
						action: "generarReporte", 
						wcco: wcco, 
						wmed: wmed, 
						consultaAjax: '',
						wfecha:	wfecha,
						responsable : responsable
					} ,
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
include_once("movhos/movhos.inc.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wcenprobasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	
	switch($action){
		
		case 'generarReporte':
		
			generarReporte( $wcco, $wmed, $wfecha, $responsable );
		
		break;
		
		case 'modificarArticulo':
		
			cambiarEstado( $_REQUEST['registro'], $_REQUEST['estado'] );
		
		break;
		
		case 'consultar_medicamento':
		
			consultar_medicamento($wemp_pmla, $wmed, $term);
			
		break;
	}
	

	return;
}
//FIN*LLAMADOS*AJAX******************************************************************
	
	function consultar_medicamento($wemp_pmla, $wmed, $term){
		
		global $conex;
        global $wbasedato;
		global $wcenprobasedato;
		global $wemp_pmla;
		global $wusuario;
		
		$consulta = "SELECT * FROM 
					(SELECT Artcod, Artcom, Artgen
		               FROM ".$wbasedato."_000026 
					  WHERE (Artcom LIKE '%$term%' OR Artcod LIKE '%$term%' OR Artgen LIKE '%$term%')
					    AND Artesm = 'on'
					    AND Artest = 'on'
					  UNION
					  SELECT Artcod, Artcom, Artgen
						FROM  ".$wcenprobasedato."_000002 B
					   WHERE (Artcom LIKE '%$term%' OR Artcod LIKE '%$term%' OR Artgen LIKE '%$term%')
					     AND Artest = 'on') as t
					   GROUP BY Artcod
						 LIMIT 100";
		$res = mysql_query($consulta,$conex);
        $num = mysql_num_rows($res);
		
		$respuesta = array();
		
		while($fila = mysql_fetch_assoc($res)){
			$respuesta[$fila['Artcod']] = $fila['Artcod']." - ".trim($fila['Artcom'])." - ".trim($fila['Artgen']);		
		}
		
		echo json_encode($respuesta);
		
		
	}
	
	function consultarArticulos($cod_art){

		global $conex;
        global $wbasedato;
		global $wcenprobasedato;
		global $wemp_pmla;
		global $wusuario;

		$caracteres =  array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
		$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

		$arr_medicamento = array();

		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

		$q_medicamento= "SELECT Artcod as codart, Artcom as artnom, Artgen as artnomg
						   FROM ".$wbasedato."_000026
						  WHERE Artest = 'on'
						    AND Artcod = '".$cod_art."'
							UNION
					       SELECT Artcod as codart, Artcom as artnom, Artgen as artnomg
							 FROM  ".$wcenprobasedato."_000002 B
							WHERE Artcod = '".$cod_art."'
							  AND Artest = 'on'";
		$r_medicamento = mysql_query($q_medicamento,$conex) or die("Error en el query: ".$q_medicamento."<br>Tipo Error:".mysql_error());

		while($row_medicamento = mysql_fetch_assoc($r_medicamento))
		{
			$arr_medicamento[trim($row_medicamento['codart'])] = $row_medicamento;
		}


		//print_r($arr_medicamento);
		return $arr_medicamento;
	}
	
	function generarReporte( $wcco, $wmed, $wfecha, $responsable ){
		
		
		global $conex;
        global $wbasedato;
		global $wcenprobasedato;
		global $wemp_pmla;
		global $wusuario;
		
		$wmed = explode("-", $wmed);
		$wmed = trim($wmed[0]);
		
		//Consultar los articulos
		// $q = "   SELECT A.Artcod as codart, A.Artcom as artnom, A.Artgen as artnomg
				   // FROM ".$wbasedato."_000172 , ".$wbasedato."_000026 A
				  // WHERE Afvcod = A.Artcod
					// AND Afvest = 'on'
				  // UNION
				 // SELECT B.Artcod as codart, B.Artcom as artnom, B.Artgen as artnomg
				   // FROM ".$wbasedato."_000172 , ".$wcenprobasedato."_000002 B
				  // WHERE Afvcod = B.Artcod
					// AND Afvest = 'on'";
		// $res = mysql_query($q,$conex);
        // $num = mysql_num_rows($res);
	
		// $arr_articulos = array();
		// if ($num > 0){
			// while( $row = mysql_fetch_assoc($res) ){
				// $arr_articulos[$row['codart']] = $row;
			// }
		// }
			
		$tablaTmp = "tmpFV".$wusuario;	
		 
		$qaux = "DROP TABLE IF EXISTS {$tablaTmp}";
		$res = mysql_query($qaux, $conex);

		
		if( $wcco != "" )
			$and_cco = " AND Ubisac = '".trim($wcco)."' ";
		
		if($responsable != ""){
			
			$and_resp = "AND Ingres = '".$responsable."'";
		}
		
		$q = "CREATE TEMPORARY TABLE IF NOT EXISTS {$tablaTmp}
					(INDEX idxtmp(Ubihis,Ubiing))
					SELECT Ubihis, Ubiing, Ubisac, Ubihac, Ccocod, Cconom, pacno1, pacno2, pacap1, pacap2, pacced, pactid, Ingnre
					  FROM {$wbasedato}_000011, {$wbasedato}_000018, root_000036, root_000037, {$wbasedato}_000016
					 WHERE Ubiald != 'on' 
					   ".$and_cco."
					   ".$and_resp."
					   AND Ubihis = orihis
					   AND Ubiing = oriing 
					   AND Ubihis = Inghis
					   AND Ubiing = Inging
					   AND oriori = '".$wemp_pmla."'
					   AND oriced = pacced
					   AND oritid = pactid
					   AND Ubisac = ccocod
					  
					   AND Ccohos = 'on'
					   AND Ccoest = 'on'";
		$rs = mysql_query($q, $conex) or die(mysql_error());
		
		//Mostrar los articulos aplicados para el paciente para el dia actual
		//Si no tiene, buscar para el dia anterior
		//Si tiene en ambos dias, solamente mostrar los del ultimo dia
		
		// $and = "";
		// if( $wmed != "" )
			// $and = " AND Afvcod = '".$wmed."'";

		 $q = " SELECT Ubihis as his, Ubiing as ing, CONCAT( pacno1, ' ', pacno2, ' ', pacap1, ' ', pacap2 ) as nombre, 
					 pacced as doc, pactid as tipodoc, Ubisac as cco, Ubihac as hab, Cconom as cconom, Kadart as art, Kadfec as fecha_kardex, Ingnre as responsable
				FROM ".$tablaTmp." FORCE INDEX ( idxtmp ), ".$wbasedato."_000053 A, ".$wbasedato."_000054 B
			   WHERE Karhis = Ubihis
				 AND Karing = Ubiing
				 AND B.Kadfec = '".$wfecha."'
				 AND Kadhis = Karhis
				 AND Kading = Karing
				 AND Kadart = '".$wmed."'
				 AND A.fecha_data = B.Kadfec				
				 AND Kadsus != 'on'
		    GROUP BY B.Kadfec, Ubihis, Ubiing
			ORDER BY Ubihis, Ubiing, B.Kadfec desc";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$arr_datos = array();
		if ($num > 0){
			while( $row = mysql_fetch_assoc($res) ){
				if( array_key_exists( $row['cco'], $arr_datos ) == false ){
					$arr_datos[ $row['cco'] ] = array();
					$arr_datos[ $row['cco'] ]['nombrecco'] = $row['cconom'];
					$arr_datos[ $row['cco'] ]['datos'] = array();
				}
				
				$datoart =  consultarArticulos($row['art']);
			
				//Buscar el nombre comercial y generico en el arreglo de articulos
				$row['artnom'] = $datoart[$row['art']]['artnom']; //$arr_articulos[$row['art']]['artnom'];
				$row['artnomg'] = $datoart[$row['art']]['artnomg']; //$arr_articulos[$row['art']]['artnomg'];
				
				
				
				array_push( $arr_datos[ $row['cco'] ]['datos'], $row );
			}
		}
		
		$html_abrirTablaDetalle = "<tr class='detalle fondoAmarillo' style='display:none;'>"
									."<td colspan=6 align='center'>"
									."<div class='div_detalle' align='center' style='margin: 10 0 10 0;'>"
									."<table class='tabla_detalle' align='center'>"
									."<tr class='encabezadotabla'>
											<td colspan=3 align='center'><font size=3><b>Medicamentos ordenados</b></font></td>
									  </tr>"
									."<tr class='encabezadotabla'>											
											<td align='center'>Codigo</td>
											<td align='center'>Nombre comercial</td>			
											<td align='center'>Nombre generico</td>			
									  </tr>";
		
		echo "<div style='width: 1000px;'>";
	
		foreach($arr_datos as $dato ){
			$dato['nombrecco'] = strtoupper( $dato['nombrecco'] );
			
			echo "<div class='desplegables' style='width:100%;'>";
			echo "<h3><b>* ".$dato['nombrecco']." *</b></h3>";
			echo "<div>";
			echo "<table class='entidades' align='center' width='800px'>";
			echo "<tr class='encabezadotabla'>
					<td colspan=7 align='center'><font size=4><b>PACIENTES DEL PISO ".$dato['nombrecco']."</b></font></td>
				 </tr>";
			echo "<tr class='encabezadotabla'>
					<td align='center'>Ordenes de</td>
					<td align='center'>Historia</td>
					<td align='center'>Ingreso</td>
					<td align='center'>Nombre</td>
					<td align='center'>Responsable</td>
					<td align='center'>Habitaci&oacute;n</td>
					<td align='center'>Ver</td>
				</tr>";
			$class='fila1';
			$classdet='fila1';
			
			$historia = "";
			$ingreso = "";
			$fecha_data = "";
			
			foreach( $dato['datos'] as $fila ){						
				if($fecha_data."-".$historia."-".$ingreso == $fila['fecha_kardex']."-".$fila['his']."-".$fila['ing']){
					//Imprimir fila oculta con el detalle de medicamento aplicado
					echo "<tr class='".$classdet."'>";						
						echo "<td>".$fila['art']."</td>";
						echo "<td>".$fila['artnom']."</td>";
						echo "<td>".$fila['artnomg']."</td>";
					echo "</tr>";
					
				}else if($historia."-".$ingreso == $fila['his']."-".$fila['ing'] && $fecha_data != $fila['fecha_kardex']){
					//No hacer nada, es la misma historia ingreso, pero con medicamentos del dia anterior.
				}else if( $fecha_data."-".$historia."-".$ingreso !=  $fila['fecha_kardex']."-".$fila['his']."-".$fila['ing'] ){
					($class == "fila2" )? $class = "fila1" : $class = "fila2";
					//Mostrar el nombre del paciente y algunos datos, y crear tr oculto con el articulo aplicado
					
					if( $historia != "" ){ //Cerrar el tr oculto y todo el detalle
						echo "</table>"; // cerrar la tabla_detalle
						echo "</div>"; // cerrar el div_detalle
						echo "</td>";
						echo "</tr>"; //cerrar el tr class='detalle'
					}
					
					echo "<tr class='".$class."' onclick='mostrarDetalle(this)' style='cursor:pointer;'>";
					echo "	<td align='center'>".$fila['fecha_kardex']."</td>
							<td align='center'>".$fila['his']."</td>
							<td align='center'>".$fila['ing']."</td>
							<td align='center'>".$fila['nombre']."</td>
							<td align='center'>".$fila['responsable']."</td>
							<td align='center'>".$fila['hab']."</td>
							<td align='center'>";
					echo "		<a href='../procesos/perfilFarmacoterapeutico.php?wemp_pmla=01&waccion=a&whistoria=".$fila['his']."&wfecha=".$fila['fecha_kardex']."' target='_blank'> Ir al Perfil </a>";
					echo "	</td>";
					echo "</tr>";
					
					$historia = $fila['his'];
					$ingreso = $fila['ing'];
					$fecha_data = $fila['fecha_kardex'];
					
					//Se abre tr oculto con el detalle de los medicamentos aplicados
					echo $html_abrirTablaDetalle;
					($classdet == "fila2" )? $classdet = "fila1" : $classdet = "fila2";		
					echo "<tr class='".$classdet."'>";						
						echo "<td>".$fila['art']."</td>";
						echo "<td>".$fila['artnom']."</td>";
						echo "<td>".$fila['artnomg']."</td>";
					echo "</tr>";
					
				}
			}
			
			if( $historia != "" ){ //Cerrar el tr oculto y todo el detalle
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
		
		echo "<center>";		
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "</center>";
		
		echo "</div>";
	}
	
	function cambiarEstado($wreg, $west){
		global $conex;
        global $wbasedato;
		
		$q = " UPDATE ".$wbasedato."_000001 "
			  ."  SET Conest = '".$west."' "
			."  WHERE id = '".$wreg."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		echo "OK";
	}	
	
	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz, $wbasedato, $wcenprobasedato, $conex;
		
		//global $fecha;
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		//echo "<input type='hidden' id ='fecha' value='".$fecha."'/>";

		encabezado("REPORTE MEDICAMENTOS POR PACIENTE", $wactualiz, "clinica");		

		echo '<div style="width: 100%">';
		$anio = date("Y");
		$anio--;
		$width_sel = " width: 80%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
			
		$q = " SELECT Ccocod as cod, Cconom as nom "
			."   FROM ".$wbasedato."_000011 "
			."  WHERE Ccohos = 'on'
			 ORDER BY Ccocod ";
		$res = mysql_query($q,$conex);
        $num = mysql_num_rows($res);
				
		/*$q = " SELECT Artcod as cod, Artcom as nom "
			."   FROM ".$wbasedato."_000026, ".$wbasedato."_000172 "
			."  WHERE Afvcod = Artcod
				  AND Afvest = 'on'
			 ORDER BY Afvcod";*/
		// $q = " 	 SELECT A.Artcod as cod, A.Artcom as nom, A.Artgen as nomg
				   // FROM ".$wbasedato."_000172 , ".$wbasedato."_000026 A
				  // WHERE Afvcod = A.Artcod
					// AND Afvest = 'on'
				  // UNION
				 // SELECT B.Artcod as cod, B.Artcom as nom, B.Artgen as nomg
				   // FROM ".$wbasedato."_000172 , ".$wcenprobasedato."_000002 B
				  // WHERE Afvcod = B.Artcod
					// AND Afvest = 'on'
			   // ORDER BY nom";
				 
		// $res2 = mysql_query($q,$conex);
        // $num2 = mysql_num_rows($res2);
		//------------TABLA DE PARAMETROS-------------		
		echo '<table align="center">';
		echo "<tr class='encabezadotabla'>";
		echo "<td colspan=2 align='center'><font size=4>FILTROS</font></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align='center' class='encabezadotabla'>Centro de Costos</td>";
		echo "<td class='fila2' align='center'>";
		
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
		
		$q_res = " SELECT * FROM (SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.ingres AS nit_responsable, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, habord"
                   ."   FROM ".$wbasedato."_000020,".$wbasedato."_000018
                   LEFT JOIN ".$wbasedato."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
                   LEFT JOIN ".$wbasedato."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
                   ."  WHERE habali != 'on' "            //Que no este para alistar
                   ."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
                   ."    AND habcod  = ubihac "
                   ."    AND ubisac  LIKE '%".$wcco1[0]."%' "
                   ."    AND ubihis  = orihis "
                   ."    AND ubiing  = oriing "
                   ."    AND ubiald != 'on' "
                   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
                   ."    AND oriced  = pacced "
                   ."    AND oritid  = pactid "
                   ."    AND habhis  = ubihis "
                   ."    AND habing  = ubiing "
				   ." UNION"
                    //Este union agrega los pacientes que tienen muerte en on.
                   ." SELECT ubihan, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, t16.ingres AS nit_responsable, t16.Ingnre AS ent_responsable, t17.Eyrsor AS cco_origen, ubiptr, ubialp, 0 habord"
                   ."   FROM ".$wbasedato."_000018
                   LEFT JOIN ".$wbasedato."_000016 AS t16 ON ( ubihis = t16.Inghis AND  ubiing = t16.Inging)
                   LEFT JOIN ".$wbasedato."_000017 AS t17 ON ( ubihis = t17.Eyrhis AND  ubiing = t17.Eyring), root_000036, root_000037 "
                   ."  WHERE ubihis  = orihis "
                   ."    AND ubiing  = oriing "
				   ."    AND ubisac  LIKE '%".$wcco1[0]."%' "
                   ."    AND ubimue  = 'on' "
                   ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
                   ."    AND oriced  = pacced "
                   ."    AND oritid  = pactid "
                   ."    AND Ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
                   ."    AND Ubiald != 'on' "             //Que no este en Alta Definitiva
                   ."  ) as t "
				   ."  GROUP BY nit_responsable"
				   ."  ORDER BY ent_responsable";

			$res_resp = mysql_query($q_res, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$num_resp = mysql_num_rows($res_resp);
		
		
		echo "<tr>";
		echo "<td align='center' class='encabezadotabla'>Responsable</td>";
		echo "<td class='fila2' align='center'>";
		
		echo "<select id='responsable' style='".$width_sel." margin:5px;'>";
		echo "<option value=''>TODOS</option>";
		if( $num_resp > 0 ){		
			while( $row_resp = mysql_fetch_array($res_resp) ){
				echo "<option value='".$row_resp['nit_responsable']."'>".$row_resp['ent_responsable']."</option>";
			}
		}
		echo "</select>";
		
		echo "</td>";
		echo "</tr>";
		
		
		echo "<tr>";
		echo "<td align='center' class='encabezadotabla'>Medicamento</td>";
		echo "<td class='fila2' align='center'>";
		echo "<input type='text' id='wmed' name='wmed' value='' size='43'>";
		// echo "<select id='wmed' style='".$width_sel." margin:5px;'>";
		// echo "<option value=''>TODOS</option>";
		// if( $num2 > 0 ){		
			// while( $row2 = mysql_fetch_array($res2) ){
				// echo "<option value=".$row2['cod'].">".$row2['cod']."  -  ".$row2['nom']."</option>";
			// }
		// }
		// echo "</select>";
		
		echo "</td>";	
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadotabla' align='center'>Fecha:</td>";
		echo "<td class='fila2' align='center'><input type='text' id='wfecha' value='".date('Y-m-d')."' size='43' readonly></td>";
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
	}
	
?>
 <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>