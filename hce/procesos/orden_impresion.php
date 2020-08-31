<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Orden de impresion
 * Fecha		:	2012-12-19
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	El objetivo del programa es poder darle un orden de impresion a los formularios de HCE
 *********************************************************************************************************
 Actualizaciones:

 2016-06-21     Arleyda Insignares Ceballos
                Se modifica Consulta para que no muestre los Nodos(Carpetas). Solo se pueden imprimir los
                Formularios

 **********************************************************************************************************/
 
 $wactualiz = "2016-06-21";
 
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

	echo "<title>Orden de Impresion</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo ' <link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
}


//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//



include_once("root/comun.php");
include_once("movhos/movhos.inc.php");



$conex = obtenerConexionBD("matrix");
$wbaseHce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if($action=="guardarCambios"){
		actualizarOrden( $_REQUEST['orden']  );
	}else if($action=="mostrarPaciente"){
		mostrarDetallePaciente( @$_REQUEST['historia'], @$_REQUEST['ingreso'], @$_REQUEST['servicio'], @$_REQUEST['fecha'], @$_REQUEST['paciente'], @$_REQUEST['doc_paciente'], @$_REQUEST['habitacion'], @$_REQUEST['nacimiento'], @$_REQUEST['medico']);
	}
	return;
}
//FIN*LLAMADOS*AJAX**************************************************************************************************************//

function actualizarOrden( $ordentexto ){
		global $conex;
		global $wbaseHce;
		global $wemp_pmla;
		
		$formularios = explode( ",", $ordentexto );
		
		$q="UPDATE  ".$wbaseHce."_000001  SET  Encoim = 9999";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		

		foreach ( $formularios as $pos => $formulario ){
			$orden = ($pos+1);
			$q="UPDATE  
				".$wbaseHce."_000001 SET	Encoim = ".$orden."
									WHERE   Encpro like '%0".$formulario."'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			
		}
		echo "OK";
}

function vistaInicial(){
	global $conex;
	global $wbaseHce;
	
	global $wemp_pmla;
	global $wactualiz;

	echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";

	encabezado("ORDEN DE IMPRESION FORMULARIOS HCE", $wactualiz, "clinica");
	
	// 2016-06-21 Se modifica Consulta Para que no tenga en cuenta los Nodos 
	$query = " SELECT 	Encpro as codigo, Encdes as descripcion, Encoim as orden	"
			."	 FROM 	".$wbaseHce."_000001 A"
			."   INNER JOIN ".$wbaseHce."_000009 B"
			."   ON A.Encpro = SUBSTRING(B.Preurl,3,6)"
			."  WHERE   A.Encest = 'on' AND B.Prenod <> 'on'"
			."  GROUP BY Codigo "
			."  ORDER BY  Encoim";
				
	$res = mysql_query($query, $conex);
	$num = mysql_num_rows($res);
	
	echo "<br><br>";
	echo "<center>";
	
	$lista_ordenados = '<ul id="sortable1" class="droptrue">';
	$lista_sin_ordenar = '<ul id="sortable2" class="dropfalse">';
	
	$span = "<span class='ui-icon ui-icon-print'></span>";
	$class1 = "ui-state-barra";
	$class2= "ui-state-barra2";
	
	//Si es internet explorer
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/MSIE/i',$u_agent)){
		$span = "";
		//$class1 = "ui-state-active";
		//$class2 = "ui-state-hover";
	}
	
	if ($num > 0){
		while($row = mysql_fetch_assoc($res)) {
			if ( (int)$row['codigo'] > 50 ){
				if( (int)$row['orden'] != 9999 )
					$lista_ordenados .= "<li class='".$class1."'  align='left' value='".$row['codigo']."'>".$span." ".$row['orden']." - ".$row['descripcion']."</li>";
				else
					$lista_sin_ordenar .= "<li class='".$class2."' align='left' value='".$row['codigo']."'>".$row['descripcion']."</li>";
			}
		}
	}

	$lista_ordenados .= "</ul>";
	$lista_sin_ordenar .= "</ul>";
	
	echo "<input type='button' id='boton_guardar' class='botona' value='Guardar' />";
	echo "<br><br>";	
	$title = "-Arrastre a la columna izquierda los formularios que<br>desea que salgan en la impresion";
	$title.="<br>-En la misma columna arrastre los formularios para indicar el orden de impresion";
	echo "<table>";
	echo "<tr><td colspan=2 align='right' style='font-size: 8pt;'><a href='#' class='msg_tooltip' title='".$title."' id='enlace_ayuda'>Ayuda</a></td></tr>";
	echo "<tr><td class='encabezadotabla' align='center' colspan=2>FORMULARIOS DE HCE</td></tr>";
	echo "<tr><td class='encabezadotabla' align='center'><b>Orden de impresion</b></td><td class='encabezadotabla' align='center'><b>No se imprimen</b></td></tr>";
	echo "<tr><td align='center' style='vertical-align: top; width: 509px'>";
	echo $lista_ordenados;
	echo "</td>";
	echo "<td align='center' style='vertical-align: top; width: 509px'>";
	echo $lista_sin_ordenar;
	echo "</td></tr></table>";
	echo "<br><br>";

	
	echo "</center>";
	
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
	<style>
		.ui-state-barra   {border: 1px solid #C3D9FF; background: #C3D9FF; color: #000; }
		.ui-state-barra   {
			background: #fff; /* Old browsers */
			/* IE9 SVG, needs conditional override of 'filter' to 'none' */
			background: -moz-linear-gradient(top,  #fff 0%, #C3D9FF 30%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#C3D9FF)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* IE10+ */
			background: linear-gradient(to bottom,  #fff 0%,#C3D9FF 30%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#C3D9FF',GradientType=0 ); /* IE6-8 */
			-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#C3D9FF')";
			zoom:1;
		}
		.ui-state-barra2   {border: 1px solid #fefcea; background: #000000; color: #000; }
		.ui-state-barra2   {
			background: #fff; /* Old browsers */
			/* IE9 SVG, needs conditional override of 'filter' to 'none' */
			background: -moz-linear-gradient(top,  #fff 0%, #E8EEF7 30%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#E8EEF7)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* IE10+ */
			background: linear-gradient(to bottom,  #fff 0%,#E8EEF7 30%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#E8EEF7',GradientType=0 ); /* IE6-8 */
			-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E8EEF7')";
			zoom:1;
		}
		
		#sortable1, #sortable2, #sortable3 {
			list-style-type: none; margin: 0; padding: 0; display:inline-block; vertical-align:top; 
			margin-right: 10px; background: #eef; padding: 5px; width: 98%;
			font-size: 9pt;
			font-weight: normal;
		}
		#sortable1 li, #sortable2 li, #sortable3 li {
			margin: 5px; padding: 5px; font-size: 1.2em; width: auto; cursor: pointer; text-align: left;
		}
		#sortable1 li span{ /*position: absolute; margin-left: 30em;*/ float:right; }
		
		.botona{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:black;
			background:#C3D9FF;
			border:0px;
			width:180px;
			height:30px;
			margin-left: 1%;
		}
		
		.botona:hover{
			background:#638BD5;
		}
		
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
		#tooltip h3, #tooltip div{margin:0; width:auto}
	</style>
	
	<!--[if gte lt 9]>
	  <style type="text/css">
		.ui-state-frede {
		   filter: none;
		}
	  </style>
	<![endif]-->
   
    <script>
	
		var span_html = "<span class='ui-icon ui-icon-print'></span>";
		
		$(document).ready(function() {
		
			$("#boton_guardar").click( function(){
				guardarCambios();
			});
		
			$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			
			if ( $.browser.msie ) 
				span_html = "";
		
			$( "ul.droptrue" ).sortable({
				connectWith: "ul",
				cursor: "move",
				tolerance: "intersect",
				 dropOnEmpty: true,
				stop: function( event, ui ) {
					//lo cambie
					cambiarNumeros();
				},
				out: function( event, ui ) {
					//llego
					cambiarNumeros();
				}
			});
	 
			$( "ul.dropfalse" ).sortable({
				connectWith: "ul",
				dropOnEmpty: true,
				cursor: "move",
				out: function( event, ui ) {
					//llego
					quitarNumeros();
				}
			});
	 
			$( "#sortable1, #sortable2, #sortable3" ).disableSelection();
		});
		
		
		
		function guardarCambios(){
			var codigos_ordenados = new Array();
			$("#sortable1 li").each( function(){
				var codigo_formulario = $(this).val();
				codigos_ordenados.push( codigo_formulario );
			} );
			var cods = codigos_ordenados.toString();
			
			var wemp_pmla = $("#wemp_pmla").val();
			var rango_superior = 245;
			var rango_inferior = 11;
			var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

			$.blockUI({ message: $('#msjEspere') });
			$.get('orden_impresion.php', { wemp_pmla: wemp_pmla, action: "guardarCambios", orden: cods, consultaAjax: aleatorio } ,
				function(data) {
					$.unblockUI();
					if( data == "OK")
						alerta("Guardado con exito");
					else
						alerta("Ha ocurrido un error, por favor intente nuevamente");
				});
		}
		
		function quitarNumeros(){
			//Bloquea mientras termina de ordenar
			$( "#sortable1, #sortable2" ).sortable( "disable" );
			var i = 1;
			$("#sortable2 li").each( function(){ //Para cada li
				var texto = $(this).text();
				var pos = -1;
				//Quitar numeros solo si comienza con un numero
				if( /^[1-9]/.test( $.trim(texto) ) == true ) pos = texto.indexOf("-");
				if( pos > 0 )
					$(this).text( texto.substring(pos+1) );
			});
			//Desbloquea cuando termina de ordenar
			$( "#sortable1, #sortable2" ).sortable( "enable" );
			
		}
		
		function cambiarNumeros(){
			//Bloquea mientras termina de ordenar
			$( "#sortable1, #sortable2" ).sortable( "disable" );
			var i = 1;
			$("#sortable1 li").each( function(){ //Para cada li
				var texto = $(this).text();
				var pos = -1;
				//Solo si comienza con un numero
				if( /^[1-9]/.test( $.trim(texto) ) == true ) pos = texto.indexOf("-");
				if( pos > 0 )
					$(this).html( span_html + i + " - " + texto.substring(pos+1));
				else
					$(this).html( span_html + i + " - " + texto);
				i++;
			});
			//Desbloquea cuando termina de ordenar
			$( "#sortable1, #sortable2" ).sortable( "enable" );
		}
		
		function alerta( txt ){
			$("#textoAlerta").text( txt );
			$.blockUI({ message: $('#msjAlerta') });
				setTimeout( function(){
								$.unblockUI();
							}, 2000 );
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