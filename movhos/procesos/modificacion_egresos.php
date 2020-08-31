<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Modificacion de Egresos
 * Fecha		:	2013-02-06
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Programa que permite cambiar el tipo de egreso de una historia-ingreso
 * Condiciones  :   -No se pueden eliminar datos, solo se CAMBIA el motivo del egreso.
					-Se actualizan las tablas 18, 33 y 38
 *********************************************************************************************************
 
 Actualizaciones:
				2013-02-07:  Se crea un registro en movhos39 (Auditoria) indicando que se ha hecho una modificacion de egresos

			
 **********************************************************************************************************/
 
$wactualiz = "2013-02-07";
 
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
	echo "<title>Modificacion de Egresos</title>";
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
$tiposEgresos = consultarTiposEgresosModificables();

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "mostrarListaEgresos"){
		mostrarListaEgresos( $_REQUEST['historia'], $_REQUEST['ingreso'] );
	}else if( $action == "cambiarMotivoEgreso"){
		cambiarMotivoEgreso( $_REQUEST['historia'], $_REQUEST['ingreso'], $_REQUEST['campo_id'], $_REQUEST['motivo'], $_REQUEST['cco'], $_REQUEST['fecha'], $_REQUEST['egresoantes']  );
	}else if( $action == "imprimirBoxIngresos" ){
		buscarIngresos( $_REQUEST['historia'] );
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************

	function buscarIngresos($whis){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$q = "SELECT Inging as ingreso "
		   ."   FROM ".$wbasedato."_000016 "
		   ."  WHERE Inghis='".$whis."'";
		   
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		if ($num > 0){
			echo "<option value=''>&nbsp;</option>";
			while($row = mysql_fetch_assoc($res)){
				echo "<option value='".$row['ingreso']."'>".$row['ingreso']."</option>";
			}
		}
	}
	
	function consultarInfoPaciente($whis){
	
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		
		 $q = " SELECT orihis as historia, oriing as ingreso, CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente, "
				."     pactid as tipodoc, pacced as numdoc,  ROUND((DATEDIFF(  now(),  pacnac ))/365,0) as edad"
			."   FROM root_000036, root_000037 "
			."  WHERE orihis  = '".$whis."'"
			."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
			."    AND oriced  = pacced "
			."    AND oritid  = pactid "
			."   LIMIT 1";
			
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		if ($num > 0){
			$row = mysql_fetch_assoc($res);
			return $row;
		}
	}
	
	function mostrarListaEgresos( $whis, $wing ){
	
        global $conex;
        global $wbasedato;
		global $wemp_pmla;
		global $tiposEgresos;		
		
		$chain = '(';
		foreach($tiposEgresos as $tipo){
			$chain.= "'".$tipo."',";
		}
		$chain = substr_replace($chain,"",-1);
		$chain.= ")";
		
        //Selecciono todos los pacientes del servicio seleccionado
        $q = " SELECT A.id, A.Fecha_data as fecha, A.Hora_data as hora, Servicio as cco, Tipo_egre_serv as tipo_egreso, Cconom as nombre_servicio  "
            ."   FROM ".$wbasedato."_000033 A, ".$wbasedato."_000011 "
            ."  WHERE Historia_clinica  = '".$whis."'"
            ."    AND Num_ingreso= '".$wing."' "
			."    AND Tipo_egre_serv IN ".$chain.""
			."    AND Servicio = Ccocod";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		
		$paciente = consultarInfoPaciente( $whis );
		if( !empty( $paciente ) ){
			echo "<table>";
			echo "<tr><td class=encabezadotabla>Documento</td><td class=fila2 align=center>".$paciente['tipodoc']." ".$paciente['numdoc']."</td></tr>";	
			echo "<tr><td class=encabezadotabla>Nombre</td><td class=fila2 align=center><b>".$paciente['paciente']."</b></td></tr>";
			echo "<tr><td class=encabezadotabla>Edad</td><td class=fila2 align=center>".$paciente['edad']." años</td></tr>";
			echo "<tr><td class=encabezadotabla>Historia</td><td class=fila2 align=center>".$paciente['historia']."</td></tr>";
			echo "<tr><td class=encabezadotabla>Ingreso</td><td class=fila2 align=center>".$paciente['ingreso']."</td></tr>";	
			echo "</table>";
			echo "<br>";
		}

		if ($num > 0){
			echo "<table id='tabla_egresos'>";
			echo "<tr class='encabezadotabla'>";
			echo "<td colspan=5 align='center'>";
			echo "EGRESOS DEL PACIENTE";
			echo "</td>";
			echo "</tr>";
			echo "<tr class='encabezadotabla'>";
			echo "<td align='center'>Fecha</td>";
			echo "<td align='center'>Hora</td>";
			echo "<td align='center'>Servicio</td>";
			echo "<td align='center'>Tipo Egreso</td>";
			echo "<td align='center'>Cambiar por</td>";
			echo "</tr>";
			$wclass = 'fila1';
			while($row = mysql_fetch_assoc($res)){
				( $wclass == 'fila2' )? $wclass='fila1' : $wclass = 'fila2';
				$row['hora'] = substr_replace($row['hora'],"",-3);
				echo "<tr class='".$wclass."'>";
				echo "<td>";
				echo $row['fecha'];
				echo "</td>";
				echo "<td>";
				echo $row['hora'];
				echo "</td>";
				echo "<td align='center'>";
				echo $row['cco']." - ".$row['nombre_servicio'];
				echo "</td>";
				echo "<td align='center'>";
				echo $row['tipo_egreso'];
				echo "</td>";				
				echo "<td>";
				echo "<select onchange=\"cambiarMotivoEgreso({$row['id']}, this, {$whis}, {$wing}, {$row['cco']}, '{$row['fecha']}', '{$row['tipo_egreso']}');\">";
				echo "<option value=''>&nbsp;</option>";
				foreach($tiposEgresos as $tipo){
					if( $tipo != $row['tipo_egreso'] ){
						echo "<option value='".$tipo."'>".$tipo."</option>";
					}
				}				
				echo "</select>";
				echo "</td>";	
				echo "</tr>";
			}
			echo "</table>";
		}
    }
	
	function consultarTiposEgresosModificables(){
		global $conex;
		global $wemp_pmla;
		
		$tiposEgresos = consultarAliasPorAplicacion($conex, $wemp_pmla, "motivos_egreso");
		
		$result = explode("-", $tiposEgresos);		
		
		return $result;
	}
	
	function cambiarMotivoEgreso( $whis, $wing, $campo_id, $motivo, $wcco, $wfecha, $wegresoAntes){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		global $tiposEgresos;
		
		$user_session = explode('-',$_SESSION['user']);
		$user_session = $user_session[1];
		
		//El motivo por el que se debe cambiar debe ser un tipo de egreso aceptado
		if( in_array($motivo, $tiposEgresos) == false ){
			echo "NO";
			return;
		}
		
		$q = " UPDATE ".$wbasedato."_000033 "
		    ."  SET Tipo_egre_serv = '".$motivo."'"
			."  WHERE id = '".$campo_id."'"
			."  LIMIT 1";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        //$num = mysql_affected_rows();
			
		$muerte = 'off'; 
		if(preg_match('/MUERTE/i',$motivo))
			$muerte = 'on';		
		
		$q = " UPDATE ".$wbasedato."_000018 "
		    ."  SET Ubimue = '".$muerte."'"
			."  WHERE ubihis = '".$whis."'"
			."    AND ubiing = '".$wing."'"
			." LIMIT 1 ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        //$num = mysql_affected_rows();
		
		$q = " SELECT * "
		    ."   FROM ".$wbasedato."_000038 "
			."  WHERE Fecha_data = '".$wfecha."'"
			."    AND Cieser = '".$wcco."'";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		$row = mysql_fetch_assoc($res);
		
		
		$muerteMayor = $row['Ciemmay'];
		$muerteMenor = $row['Ciemmen'];
		$egresosAlta = $row['Cieeal'];
		
		//Restamos uno al motivo de egreso que tenia el paciente
		if(preg_match('/MAYOR/i',$wegresoAntes)){ //Muerte mayor
			$muerteMayor--;
		}else if(preg_match('/MENOR/i',$wegresoAntes)){ // Muerte menor
			$muerteMenor--;			
		}else if(preg_match('/ALTA/i',$wegresoAntes)){
			$egresosAlta--;
		}
		
		//Sumamos uno al motivo de egreso nuevo del paciente
		if( $muerte == 'on' ){
			if(preg_match('/MAYOR/i',$motivo)){ //Muerte mayor
				$muerteMayor++;
			}else if(preg_match('/MENOR/i',$motivo)){ // Muerte menor
				$muerteMenor++;			
			}		
		}else{
			if(preg_match('/ALTA/i',$motivo)){
				$egresosAlta++;
			}
		}
		
		$q = " UPDATE ".$wbasedato."_000038 "
		    ."  SET Ciemmay = '".$muerteMayor."',"
			."  	Ciemmen = '".$muerteMenor."',"
			."  	Cieeal = '".$egresosAlta."'"
			."  WHERE Fecha_data = '".$wfecha."'"
			."    AND Cieser = '".$wcco."'"
			." LIMIT 1 ";
		
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        //$num = mysql_affected_rows();
		
		//Guardar en el log
		$insert = "INSERT INTO  ".$wbasedato."_000039 "
								."	(Medico, "
								."	Fecha_data, "
								."	Hora_data, "
								."	Aunnum, "
								."	Aunanu, "
								."	Aunmod, "
								."	Aunfmo, "
								."	Aunacc, "
								."	Seguridad) "
						."    VALUES "
								."	('".$wbasedato."', "
								."	'".date("Y-m-d")."', "
								."	'".date("H:i:s")."', "
								."	'".$whis."-".$wing."', "
								."	'off', "
								."	'off', "
								."	'".$wfecha."', "
								."	'".$wegresoAntes."-".$motivo."', "
								."	'C-".$user_session."')";							
		$res = mysql_query($insert,$conex); 		

		echo "OK";
	}
	
	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

		encabezado("Modificacion de Egresos", $wactualiz, "clinica");
		
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
		echo '<td class="encabezadotabla" width="80px" height="20px">Historia</td>';

		echo '<td class="fila1" width="auto">';
		echo "<input type='text' id='historia' />";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo '<td class="encabezadotabla" width="80px" height="20px">Ingreso</td>';

		echo '<td class="fila1">';
		echo "<select id='ingreso'  style='width:99%;'><option value=''></option></select>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		echo "<br><br>";
		
		echo "<center>";
		echo "<input type='button' id='btn_consultar' value='Consultar' />";
		echo "</center>";
		
		//------------FIN TABLA DE PARAMETROS-------------

		echo "<br><br><br>"; 
		
		//---DIV PRINCIPAL

		//LISTA DE PACIENTES
		echo '<div id="resultados_lista" align="center" style="width: 100%"></div>';
		echo "<br><br>";

		//------FIN FORMULARIO------
		echo "</div>";//Gran contenedor
		echo "<center>";
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "</center>";
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
	}
?>
<script>
	
//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});

		$("#btn_consultar").click(function(){
			consultarListaEgresos();
		});
		
		$("#historia").focusout(function(){
			imprimirBoxIngresos();
		});
		
		//Solo valores numericos
		$("#historia").keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});		
	});
	
	//Funcion que se activa cuando se presiona el enlace "retornar"
	function restablecer_pagina(){
		$("#resultados_lista").hide( 'drop', {}, 500 ); //esconder lista
		$("#enlace_retornar").hide(); //esconder enlace retornar
	}
	
	function imprimirBoxIngresos(){
		var wemp_pmla = $("#wemp_pmla").val();
		var historia = $("#historia").val();
		
		$("#resultados_lista").hide( 'drop', {}, 500 );
		if( historia == "" ){			
			return;
		}
		
		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		$("#enlace_retornar").fadeIn('slow');
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		//Realiza el llamado ajax con los parametros de busqueda
		$.get('modificacion_egresos.php', { wemp_pmla: wemp_pmla, action: "imprimirBoxIngresos", historia: historia, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$("#ingreso").html(data);
			});
	}
	
	function consultarListaEgresos(){
	
		var wemp_pmla = $("#wemp_pmla").val();
		var historia = $("#historia").val();
		var ingreso = $("#ingreso").val();
		
		if( historia == "" || ingreso == "" ){
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
		//Realiza el llamado ajax con los parametros de busqueda
		$.get('modificacion_egresos.php', { wemp_pmla: wemp_pmla, action: "mostrarListaEgresos", historia: historia, ingreso: ingreso, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$('#resultados_lista').html(data);
				$("#resultados_lista").show( 'drop', {}, 500 );
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			});
	}	
	
	function cambiarMotivoEgreso(campo_id, objetoselect, historia, ingreso, cco, fecha, egresoantes){
	
		var wemp_pmla = $("#wemp_pmla").val(); 
		objetoselect = jQuery( objetoselect );
		var motivo = objetoselect.val();
		if( motivo == '' )
			return;
		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		$("#enlace_retornar").fadeIn('slow');
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		//Realiza el llamado ajax con los parametros de busqueda
		$.post('modificacion_egresos.php', { wemp_pmla: wemp_pmla, action: "cambiarMotivoEgreso", egresoantes: egresoantes, fecha: fecha, cco: cco, historia: historia, ingreso: ingreso, campo_id: campo_id, motivo: motivo, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				if( data == 'OK' ){
					alerta('Cambio realizado con exito');
					setTimeout(function(){
						$("#btn_consultar").trigger('click');
					},1700)
					
				}else{
					alerta('Ha ocurrido un error, intente nuevamente');
				}
			});
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