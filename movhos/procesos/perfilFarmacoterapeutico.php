<html>

<head>
<title>MATRIX - [PERFIL FARMACOTERAPÉUTICO]</title>

<!-- JQUERY para los tabs -->
<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script>
<!-- Fin JQUERY para los tabs -->

<!-- Include de codigo javascript propio de mensajeria Kardex -->
<script type="text/javascript" src="../../../include/movhos/mensajeriaKardex.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>

<!-- Include de codigo javascript propio del kardex -->
<script type="text/javascript" src="kardex.js?v=<?=md5_file('kardex.js');?>"></script>

<script type="text/javascript">

function mostrarImprimirSticker(){
	 $.blockUI({ 
		message: $('#dvImprimirStickers'),
		css:{
			cursor: 'pointer',
			width:  '30%'
		},
	 });
}

//Esta funcion permite a impresion de un solo sticker, segun la historia digitada.
function sticker_historia(wbasedato, wemp_pmla, whis)
{
    var whis			 = $("#whistoria").val();
	var wccoo 			 = $("#servicioPaciente").val();
	var wipimpresora 	 = $("#wipimpresora").val();
	var whora_par_actual = document.getElementById("whora_par_actual").value;

	if(whis == '')
	{
		alert('Debe ingresar una historia.');
		return;
	}
	
    $.post("../reportes/stickers_Dispensacion.php",
		{
			consultaAjax:   	'stick_historia',
			wemp_pmla:      	wemp_pmla,
			wbasedato:          wbasedato,
			whis:       		whis,
			whora_par_actual:   whora_par_actual,
			wccoo: 				wccoo,
			wipimpresora  :     wipimpresora,
		}
		,function(data_json) {
			if (data_json.error == 1)
			{
				alert(data_json.mensaje);
			}
			else
			{
			  alert(data_json.mensaje);
			}
		},
		"json"
    );
}


	$(document).ready(function(){ 
		inicializarJqueryPerfil();  
	});

function ocultar_mostrar(){

	$("#med_consumo_hab").toggle("1000");
	
}
/************************************************************************************
 * Actualiza los mensjaes sin leer cuando se actualiza la mensajeria
 ************************************************************************************/
function alActualizarMensajeria(){
	
	var mensajes_sin_leer = $("#mensajes_sinleer").val();
	$("#sinLeer").html(mensajes_sin_leer);
	$("#sinLeer").attr('contador',mensajes_sin_leer);	
}

/**********************************************************************
 * Octubre 11 de 2011
 **********************************************************************/
function enviandoMensaje(){

	if( document.getElementById( 'mensajeriaKardex' ).value != '' ){
		enviarMensaje( document.getElementById( 'mensajeriaKardex' ), document.getElementById( 'mesajeriaPrograma' ).value,document.forms.forma.whistoria.value,document.forms.forma.wingreso.value,document.getElementById( "usuario" ).value, document.forms.forma.wbasedato.value );
	}
}

/**********************************************************************
 * Octubre 11 de 2011
 **********************************************************************/
function marcarLeido( campo, id ){
	
	//Funcion que marca el mensaje como leido.
	marcandoLeido( document.forms.forma.wbasedato.value, id, document.getElementById( "usuario" ).value );
	
	//Se remueve la clase blink para que no parpadee ya que se marca como leido.
	$('#fila_'+id).find('span').each(function(){
		
		$(this).removeClass('blink');

    });
	
	$('#tdfila_'+id).attr('onclick','');
	
	var contador_mensajes = $("#sinLeer").attr('contador');
	var count_final = contador_mensajes - 1;
	$("#sinLeer").attr('contador',count_final);
	
	$("#sinLeer").html(count_final);
}

function inicioPerfil(){
	document.location.href='perfilFarmacoterapeutico.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&wsservicio='+document.forms.forma.servicioPaciente.value+'&editable=<?=$editable ?>';
}

/*****************************************************************************************************************************
 * Punto de entrada de Perfil
 ******************************************************************************************************************************/
function consultarPerfil(){

	var historia = document.forms.forma.whistoria.value;
	var esFechaValida = esFechaMenorIgualAActual(document.forms.forma.wfecha.value);
	
	//Digitó historia
	if(!historia || historia == ''){
		alert("Debe especificar una historia clínica");
		return;
	}

	if(esFechaValida){
		if(whgrabado && whgrabado.value != ''){
			document.location.href = 'perfilFarmacoterapeutico.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia+'&wfecha='+document.forms.forma.wfecha.value+'&editable='+document.forms.forma.editable.value;
		} else {
			document.location.href = 'perfilFarmacoterapeutico.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+historia+'&wfecha='+document.forms.forma.wfecha.value+'&editable='+document.forms.forma.editable.value;
		}
	} else {
		alert("La fecha ingresada debe ser igual o anterior a la fecha actual");
	}
}

function irAPerfil(historia){
	document.forms.forma.wthistoria.value = historia;
	consultarPerfil();	
}

function consultarPerfil(){
	var ingreso = "";

	if(document.forms.forma.wtingreso && document.forms.forma.wtingreso.value != ''){
		ingreso = document.forms.forma.wtingreso.value;
	}

	if(document.forms.forma.whistoria.value == ''){
		alert("Debe ingresar una historia clinica");
		return;
	}

	document.location.href = 'perfilFarmacoterapeutico.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=a&whistoria='+document.forms.forma.whistoria.value+'&wfecha='+document.forms.forma.wfecha.value
				+'&editable='+document.forms.forma.editable.value+'&wingreso='+ingreso;
}

function limpiarCampo(idx){
	var elemento = document.getElementById("wnmmed"+idx);

	if(elemento){
		elemento.value = '';
	}
}

/*****************************************************************************************************************************
 *Consulta las historias y habitaciones de acuerdo a un servicio
 ******************************************************************************************************************************/
function consultarHabitaciones()
{
	var contenedor = document.getElementById('cntHabitacion');
	var parametros = ""; 
				
	parametros = "wemp_pmla=01&consultaAjaxKardex=29&basedatos="+document.forms.forma.wbasedato.value+"&servicio=" + document.getElementById('wsservicio').value; 
	
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax1=nuevoAjax();
		
		ajax1.open("POST", "../../../include/movhos/kardex.inc.php",true);
		ajax1.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax1.send(parametros);
		
		ajax1.onreadystatechange=function() 
		{ 
			if (ajax1.readyState==4 && ajax1.status==200)
			{
				contenedor.innerHTML=ajax1.responseText;
			}
			
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax1) ) {
			ajax1.send(null);
		}
	}catch(e){	}
}

/*****************************************************************************************************************************
 * Intercala la lista de articulos en el historial
 ******************************************************************************************************************************/
function intercalarMedicamentoAnteriorPerfil(idElemento){
	$("#med"+idElemento).toggle("normal");
}

consultarAuditoria = true;
//Inicio de jquery
$(document).ready(function(){
	
	$( "img[title]" ).tooltip({showURL:false});
	
    $("#fixeddiv").draggable();

    $("#tabs").tabs({ fx: {opacity: 'toggle' }});

    $("#tabs").tabs('select', 0);
    enfocarInicio();
	
	//Inicializando mensajeria
	try{
		mensajeriaActualizarSinLeer = alActualizarMensajeria;
		consultarHistoricoTextoProcesado( document.forms.forma.wbasedato.value, document.forms.forma.wemp_pmla.value, document.forms.forma.whistoria.value, document.forms.forma.wingreso.value, document.getElementById( 'mesajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria' ) );	//Octubre 11 de 2011
		
		mensajeriaTiempoRecarga = consultasAjax( "POST", "../../../include/movhos/mensajeriaKardex.php", "consultaAjax=4&wemp="+document.forms.forma.wemp_pmla.value, false );
		mensajeriaTiempoRecarga = mensajeriaTiempoRecarga*60000;	//El tiempo que se consulta esta en minutos
		
		if( !mensajeriaTiempoRecarga || mensajeriaTiempoRecarga == 0 || isNaN( mensajeriaTiempoRecarga ) )
			mensajeriaTiempoRecarga = 10*60000;
		
		setInterval( "mensajeriaActualizar()", mensajeriaTiempoRecarga );
	}
	catch(e){}
	
	//activando blink
	setInterval( "parpadear()", 500 );
	
	$( "[href=#fragment-3]" ).click(function(){
	
		if( consultarAuditoria ){
			
			$( "#fragment-3" ).block({ message: "<b style='font-size:20pt;'>Por favor espere...<br>Cargando datos...</b>" });
			
			try{
				
				$.post("../../hce/procesos/ordenes.inc.php",
					{
						consultaAjax		: '',
						consultaAjaxKardex	: 'consultarAuditoria',
						wbasedato			: $("[name=wbasedato]").val(),
						wemp_pmla			: $("[name=wemp_pmla]").val(),
						whistoria			: $("#whistoria").val(),
						wingreso			: $("#wingreso").val(),
						mostrarRol			: true,
						// fechaKardex			: $('#wfechagrabacion').val(),
					}, 
					function(data){
						
						try{
							var enc = "<span class='subtituloPagina2' align='center'>Registros de auditoria</span><br><br>";
							
							$( "#fragment-3" ).html( enc+data.html );
							
							consultarAuditoria = false;
							
							$( "#fragment-3" ).unblock();
						}
						catch(e){
							$( "#fragment-3" ).unblock();
						}
					},
					"json"
				);
			}
			catch(e){
				$( "#fragment-3" ).unblock()
			}
		}
		
	});
 });
</script>

<style>
	.fondoNoPOSCM
	{
		background-color: #F5BCA9;
		color: #000000;
		font-size: 10pt;
	}
	.textoObservaciones
	{
		font-size: 10pt;
	}
	.fondoAlertaConfirmar {
		background-color: #8181F7;
		color: #000000;
		font-size: 10pt;
	}
	.articuloCm1 {
		background-color: #2AAAFF;
		color: #000000;
		font-size: 10pt;
	}
	
	.articuloAntibiotico {
		background-color: #CFE66F;
		color: #000000;
		font-size: 10pt;
	}
</style>

</head>

<body>
<?php
include_once("conex.php");
/*BS'D
 * PERFIL FARMACOTERAPEUTICO
 * Autor: Mauricio Sánchez Castaño.
 *
 * Modificaciones:
 * Agosto 1 de 2019	 		(Edwin MG) 	->	Los articulos de stock no se muestran
 * Junio 25 de 2019	 		(Edwin MG) 	->	Las funciones file_get_contents se cambian por llamados ajax
 * Abril 25 de 2019	 		(Edwin MG) 	->	Se muestra talla y peso del paciente
 * Julio 3 de 2018	 		(Jessica)  	->	La información de la pestaña OTROS, la cual contiene la auditoría de kardex y/o ordenes médicas se consulta por ajax al dar click
 *											sobre la pestaña.
 * Diciembre 18 de 2017 	(Jessica)  	->	Se adiciona el ingreso como parámetro de la función consultarAlergiasDiagnosticosAnteriores()     
 *											y se muestra el diagnóstico que devuelve esta función en vez de el diagnostico que se guarda 
 *											en movhos_000053 y quedaba en $kardexActual->diagnostico
 * Octubre 23 de 2017 		(Edwin MG)	->	Se corrigen cambios varios para que funcione el perfilFarmacoterapeutico no editable
 * Septiembre 12 de 2017 	(Jessica)	->	En kardex.inc.php se valida si el articulo es de central de mezclas y si el tipo permite reemplazarlo o no en el perfil.
 * Abril 17 de 2017 		(Edwin MG)	->	En la funcion crearKardexAutomaticamente, al crear el encabezado del kardex se agrega el campo Karmeg
 * Enero 25 de 2017 		(Edwin MG)	->	Los articulos de LEV e IC, si están marcados como ENVIAR se cambia a NO ENVIAR. Esto se hace por que fueron creados en urgencias.
 * Enero 10 de 2017 		(Edwin MG)	->	No se permite reemplazo para NPT desde el perfil, el reemplazo se debe hacer desde los progrmas de CM.
 * Diciembre 14 de 2016 	(Edwin MG)	->	En la funcion crearKardexAutomaticamente, se agrega también que pueda generar los articulos de control a imprimir
 * Diciembre 05 de 2016 	(Jessica)  	->	Se modifican las alertas para que traiga las ingresadas en movhos_000220
 * Abril 19 de 2016 		(Edwin MG)  ->	Se resaltan los articulos antibioticos
 * Noviembre 20 de 2015 	(Edwin MG)  ->	Se muestran las NE como las DA
 * Julio 06 de 2015 		(Edwin MG)  ->	En la llamda de la función cargarArticulosADefinitivo se agrega parametro faltante
 * Junio 01 de 2015			(Edwin MG)	->  Se corrige creación del kardex, ya que provocaba que algunos articulos se perdieran.
 * Diciembre 01 de 2014		(Edwin MG)	->  Se permite que los grupos de Control sean más de uno
 * Septiembre 19 de 2014 (Jonatan Lopez) -> Se agrega a la informacion del paciente los medicamentos de uso habitual, traidos desde hce, se ocultan los med. consumo habitual 
											para que el usuario decisa si lo ve.
 * Marzo 05 de 2014 (Jonatan Lopez) 	->  Si un medicamento esta marcado como no enviar, permite agregar observaciones.
 * Noviembre 12 de 2013		(Edwin MG)	->	Se muestran los articulos suspendidos y no enviados. Adicionalmente se cambia el color de los articulos de control a amarillo
 *											para que tenga correspondencia con el color del kardex de enfermería
 * Septiembre 16 de 2013	(Edwin MG)	->	Se genera kardex automáticamente si no se ha creado en el día actual.
 * Agosto 28 de 2013	(Mario Cadavid)	->  Se creo el campo wtxtobsadd que permite separar las observaciones ya realizadas de las observaciones nuevas
 *											Todo lo que se adicione en este campo se guardará como observación del usuario actual, sin que éste pueda
 *											modificar las observaciones ya hechas por otros usuarios, las cuales siguen en el campo wtxtobs que es oculto
 *											y se muestran en un div que permite dar formato a la fuente de este histórico de observaciones
 *											Se creo el campo oculto usuariodes donde se guarda el nombre completo del usuario actualmente logueado
 * Agosto 15 de 2013	(Mario Cadavid)	->  Se adicionó una alerta cuando el artículo está a punto de agotarse por CTC
 *											además la fila del articulo se sonbreó con un color que identifica este estado.
 *											También se agregó un tooltip en el nombre del artículo con información adicional sobre éste
 * Diciembre 20 de 2012	(Edwin MG)	->	Se corrige mensaje si no se ha generado kardex para el día actual.
 * Junio 12 de 2012		(Edwin MG)	->	Para los medicamentos de CM, se detectan si son POS o NO POS. Un medicamento de CM es NO POS, si tiene por lo menos un insumo NO POS.
 * Junio 06 de 2012		(Edwin MG)	->	Si un medicamentos se encuentra como no aprobado, parpadea hasta que se apruebe el medicamento.
 * Junio 05 de 2012		(Edwin MG)	->	Si un medicamentos se encuentra como no aprobado, parpadea hasta que se apruebe el medicamento.
 * Mayo 30 de 2012		(Edwin MG)	->	Una vez que se abre el perfil, se actualiza la fecha y hora de la ultima
 * Febrero 22 de 2012	(Edwin MG)	->	Se corrige las alertas que hay para el perfil, solo se mostraba las alergias actuales y no todas la anteriores
 * Noviembre 21 de 2011	(Edwin MG)	->	Para mensajeria del perfil, se quita la prohibicion de enviar caracteres especiales.
 * Noviembre 1 de 2011	(Edwin MG)	->	Se valida que no se pueda reemplzar medicamentos desde el perfil si el medicamento nuevo no tiene la misma via de admnistracion 
										que el medicamento anterior, tanto desde el kardex.inc.php como desde javascript (kardex.js)
 * Octubre 27 de 2011	(Edwin MG)	->	Al mostrar los medicamentos del dia, el campo forma farmaceutica ya no queda vacio
 * Octubre 25 de 2011	(Edwin MG)	->	Se agrega información de afinidad en la tabla de información demografica del paciente
 * Octubre 24 de 2011	(Edwin MG)	->	Se agrega seleccion de ccos igual que el kardex
 *									->	Se evita aprobar y dar prioridad cuando el perfil esta en consulta, es decir que la fecha actual es diferente a fecha seleccionada
 * Octubre 7 de 2011	(Edwin MG)	->	Modificación automática al dar click sobre el boton de prioridad.
 * Mayo 30 de 2011		(Edwin MG)	->  Si el paciente esta de alta dejar ver el perfil
 * Abril 15 de 2011		(Edwin MG)	->	Req 2129 Dejar escribir cuatro digitos en la opcion Aut. CTC 
 * 										Se muestra los medicos tratantes.
 * Marzo 28 de 2011		(Edwin MG)	->	Se mostraba la hora 24 cuando un medicamento comenzaba a la media noche (00:00:00)
 * Marzo 23 de 2011		(Edwin MG)	->	Se verifica que el paciente tenga en cabezado del kardex sin importar el centro de costos de grabación del usuario. Se agrega funcion 
 * 									  	en Kardex.inc.php
 * 									->	Si el paciente tiene alta definitica deja ver el perfil.
 * 									->	Si una persona tiene el kardex abierto, no se puede ver el perfil.
 * Marzo 18 de 2011		(Edwin MG)	->	Se corrige la validción para el encabezado de centro de costos del usuario.
 * 14.May.10 -(Msanchez):  Se corrige error de grabacion de priorizacion para ultimo articulo del perfil
 * 18.May.10 -(Msanchez):  Cambio para registrar las cantidades autorizadas de ctc
 * 2010-06-04:  (Msanchez) -> El kardex debe grabar discriminado por centro de costos
 * 2010-09-24:  (Msanchez) -> Ultimo movimiento hospitalario resaltado
 */
 
/**********************************************************************************************************************************
 * Modal para imprimir el sticker del paciente por ronda
 **********************************************************************************************************************************/
function pintarImpresora( $conex, $wemp_pmla, $wbasedato ){
	
	 $query	= " SELECT ccocod
				   FROM {$wbasedato}_000011
				  WHERE `Ccofac` = 'on'
					AND `Ccotra` = 'on'
					AND `Ccoima` = 'on'";
	$rs = mysql_query( $query, $conex );
	$row = mysql_fetch_assoc($rs);
	$ccoCentral = $row['ccocod'];

	$q = " SELECT Impcod, Impnom, Impnip
			   FROM root_000053
			  WHERE Impcco = '$ccoCentral'
				AND Impest='on'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	if( !isset($wipimpresora) )
		$wipimpresora = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ImpresoraStickersDispen');
	
	$selected = ( !isset($wipimpresora) ) ?  "" : " selected ";
	
	echo "<div style='display:none;width:350px;height:150px;' id='dvImprimirStickers'>";
	//echo "<center class=encabezadoTabla><b style='font-size:12pt;'>IMPRIMIR STICKER</b></center>";	
	echo "<table align=center style='margin:10px'>";
	echo "<tr>";
    echo "<td colspan='3' class=encabezadoTabla align=center><b style='font-size:12pt;'>IMPRIMIR STICKER</b>";
    echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' align=center><font size=4>Ronda</font></td>";
	echo "<td class='fila1' align=center><font size=4>IMPRESORA</font></td>";
	echo "<td class='fila1' align=center><font size=4>Accion</font></td>";
	echo "</tr>";
	echo "<tr>";
	
	//Ronda
	echo "<td>";
	$horaParActual = floor( date("H")/2 )*2;	//Esto obtiene la hora par actual
	echo "<select name='whora_par_actual' id='whora_par_actual'  size='1' style='width:100%;font-family:Verdana, Arial, Helvetica, sans-serif; '>";
	for( $i = 0; $i < 24; $i += 2 ){
		$selected = ( $horaParActual == $i )? "selected" : "";
		echo "<option $selected value=".$i.">".$i."</option>";
	}
	echo "</select>";
	echo "</td>";
	
	
	//Impresora
	echo "<td align=center>";
	
	echo "<select name='wipimpresora' id='wipimpresora' size='1' style=' font-family:Verdana, Arial, Helvetica, sans-serif;'>";
	echo "<option {$selected} value='{$wipimpresora}'>Defecto: $wipimpresora</option>";
	for( $i=1; $i<=$num; $i++ )
	{
		$row1 = mysql_fetch_array($res);
		( $wipimpresora == $row1['Impnip'] ) ? $selected = "" : $selected = " selected ";
		$row1['Impcod'] . '-' . $row1['Impnom'] . '-' . $row1['Impnip'];
		echo "<option value='{$row1['Impnip']}'>".$row1[0]." - ".$row1[1]." - ".$row1['Impnip']."</option>";
	}
	echo "</select>";
	
	echo "</td>";
	
	//Accion
	echo "<td align=center>";
	echo "<input value='Imprimir' onclick='sticker_historia(&quot;movhos&quot;, &quot;01&quot;)' type='button'>";
	echo "</td>";
	
	echo "<tr>";
	echo "<td colspan='3' align=center>";
	echo "<input type=button onclick='$.unblockUI();' value='Cerrar' style='width:100px;'>";
	echo "</td>";
	echo "</tr>";
	
	echo "</tr>";	//Fin de filas
	
	echo "</table>";
	echo "</div>";
}

/**************************************************************************************************************
 * Crea un kardex automaticamente si ha pasado la media noche, esto valido solo si el kardex no se ha generado en 
 * el día y no esta abierto.  Esto solo es permitido hasta la HORA CORTE KARDEX (root_000051)
 *
 * Agosto 8 de 2011
 **************************************************************************************************************/
function crearKardexAutomaticamente( $conex, $wbd, $his, $ing, $fecha ){

	global $wbasedato;
	global $usuario;
	global $wemp_pmla;
	
	$wbasedato = $wbd;
	
	$pac[ 'his' ] = $his;
	$pac[ 'ing' ] = $ing;

	//Obtengo la fehca del día anterior
	$ayer = date( "Y-m-d", strtotime( $fecha." 00:00:00" ) - 24*3600 );
	
	//Creo un array con los diferentes cco de costos que se deben crear
	$sql = "SELECT Karcco 
			FROM
				{$wbd}_000053
			WHERE
				karhis = '{$pac['his']}'
				AND karing = '{$pac['ing']}'
				AND fecha_data = '$ayer'
			";
	
	$resCcos = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	$numCcos = mysql_num_rows( $resCcos );

	if( $numCcos > 0 ){	//Indica que hubo kardex el día anterior y por tanto puede hacerse el kardex automatico
	
		//Consulto si se ha generado kardex el día de hoy, es decir si tiene encabezado
		$sql = "SELECT * 
				FROM
					{$wbd}_000053
				WHERE
					karhis = '{$pac['his']}'
					AND karing = '{$pac['ing']}'
					AND fecha_data = '$fecha'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		//Si no existe kardex
		if( $numrows < $numCcos ){	//Si la cantidad de kardex generados hoy es menor a los del día anterior quiere decir que faltan kardex por generar
		
			//verifico que no halla articulos en la temporal
			$sql = "SELECT * 
					FROM
						{$wbd}_000060
					WHERE
						kadhis = '{$pac['his']}'
						AND kading = '{$pac['ing']}'
						AND kadfec = '$fecha'
					";
					
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numrows = mysql_num_rows( $res );
			
			if( $numrows == 0 ){	//No se ha generado kardex ni esta abierto
			
				$sql = "SELECT * 
						FROM
							{$wbd}_000053
						WHERE
							karhis = '{$pac['his']}'
							AND karing = '{$pac['ing']}'
							AND fecha_data = '$ayer'
							AND kargra != 'on'
						";
					
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$numrows = mysql_num_rows( $res );
			
				if( $numrows == 0 ){
				
					//Verifico que no se halla pasado la hora de corte kardex
					$corteKardex = true; //consultarHoraCorteKardex( $conex );
				
					if( $corteKardex ){
					
						//if( true || time() < strtotime( "$fecha $corteKardex" ) ){
						if( true ){
						
							//Si la hora actual es menor a la hora corte del kardex
							
							//Creo kardex nuevo para el día actual
							$auxUsuario = $usuario;							//Se activa está línea. Junio 01 de 2015
							// $usuario = consultarUsuarioKardex($auxUsuario);
							// $usuario->esUsuarioLactario = false;
							$usuario->gruposMedicamentos = false;			//Se activa está línea. Junio 01 de 2015
							
							/*********************************************************************************************************************
							 * Junio 12 de 2012
							 *********************************************************************************************************************/
							for( ;$rowsCcos = mysql_fetch_array($resCcos); ){
							
								//Busco si el cco es de lactario
								$sql = "SELECT
											ccolac
										FROM
											{$wbd}_000011
										WHERE
											ccocod = '".trim( $rowsCcos[ 'Karcco' ] )."'
											AND ccolac = 'on'
										";
								
								$resLac = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".msyql_error() );
								$numLac = mysql_num_rows( $resLac );
								
								$ccos[ trim( $rowsCcos[ 'Karcco' ] ) ] = false;
								
								if( $numLac > 0 ){								
									$ccos[ trim( $rowsCcos[ 'Karcco' ] ) ] = true;
								}
							}
							/*********************************************************************************************************************/
							
							foreach( $ccos as $keyCcos => $valueCcos ){
								
								//Consulto si se ha generado kardex el día de hoy, es decir si tiene encabezado
								$sql = "SELECT * 
										FROM
											{$wbd}_000053
										WHERE
											karhis = '{$pac['his']}'
											AND karing = '{$pac['ing']}'
											AND fecha_data = '$fecha'
											AND karcco = '$keyCcos'
										";
										
								$resKardexHoy = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
								$numKardexHoy = mysql_num_rows( $resKardexHoy );
							
								if( $numKardexHoy == 0 ){	//Esto se hace si no se ha generado kardex
								
									$usuario->centroCostosGrabacion = $keyCcos;
									$usuario->esUsuarioLactario = $valueCcos;	//El valor del array dice si el cco es de lactario o no
									
									$paciente = consultarInfoPacienteKardex( $pac['his'], '' );
									$kardexAc = consultarKardexPorFechaPaciente( $fecha, $paciente );
									
									//Dejo los articulos del día anterior en la tabla definitiva por si se quedaron en la temporal
									cargarArticulosADefinitivo( $pac['his'], $pac['ing'], $ayer, false, $keyCcos );
									cargarExamenesADefinitivo($pac['his'], $pac['ing'], $ayer);
									cargarInfusionesADefinitivo($pac['his'], $pac['ing'], $ayer);
									cargarMedicoADefinitivo($pac['his'], $pac['ing'],$ayer);
									cargarDietasADefinitivo($pac['his'], $pac['ing'],$ayer);
									
									//Cargos los datos del día anterior al actual
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "N", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "Q", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "A", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "U", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									
									cargarArticulosADefinitivo( $pac['his'], $pac['ing'], $fecha, false, $keyCcos );
									
									/************************************************************************************************
									 * Agosto 27 de 2011
									 ************************************************************************************************/
									if( $keyCcos == '*' ){	//Solo lo hace enfermería
										cargarExamenesAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarInfusionesAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarMedicosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarDietasAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										
										
										cargarExamenesADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarInfusionesADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarMedicoADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarDietasADefinitivo( $pac['his'], $pac['ing'], $fecha );
									}
									/************************************************************************************************/
									
									//Creo encabezado del kardex tal cual esta el día anterior
									$sql = "INSERT INTO
												{$wbd}_000053(Medico,Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karpal,Kardie,Karmez,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karfir,Karmeg,Karsuc,Karaut,Karord,Seguridad)
											SELECT
															  Medico,'".$fecha."','".date( "H:i:s" )."',Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karpal,Kardie,Karmez,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,karare,Karcco,Karusu,Karfir,Karmeg,Karsuc,'on',Karord,Seguridad
												FROM
													{$wbd}_000053
												WHERE
													Karhis = '{$pac['his']}'
													AND karing = '{$pac['ing']}'
													AND fecha_data = '$ayer'
													AND karcco = '$keyCcos'
											";
									
									$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
									
									if( mysql_affected_rows() > 0 ){
									
										//Dejo todos los registros del kardex como estaban antes
										$sql = "SELECT
													*
												FROM
													{$wbd}_000054
												WHERE
													kadhis = '{$pac['his']}'
													AND kading = '{$pac['ing']}'
													AND kadfec = '$fecha'
													AND kadcco = '$keyCcos'
												";
										
										$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
										$numrows = mysql_num_rows( $res );
										
										if( $numrows > 0 ){
										
											for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
											
												$sqlAnt = "SELECT
																*
															FROM
																{$wbd}_000054
															WHERE
																id = '{$rows['Kadreg']}'
															";
										
												$resAnt = mysql_query( $sqlAnt, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
												
												if( $rowsAnt = mysql_fetch_array($resAnt) ){
											
													$sqlAct = "UPDATE
																	{$wbd}_000054
																SET
																	kadare = '{$rowsAnt['Kadare']}',
																	kadcon = '{$rowsAnt['Kadcon']}'
																WHERE
																	id = '{$rows['id']}'
																";
											
													$resAct = mysql_query( $sqlAct, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );							
												}
											}
										}
									}
									
								}
							}
							
							$medicamentosControlAuto = consultarAliasPorAplicacion( $conex, $wemp_pmla, "MedicamentosControlAuto" );
				
							if( $medicamentosControlAuto == 'on' ){
								
								// $opciones = array(
								  // 'http'=>array(
									// 'method'=>"GET",
									// 'header'=>"Accept-language: en\r\n",
									// 'content'=>"user=".$user,
								  // )
								// );
								// $contexto = stream_context_create($opciones);
								// $url = 'http://'.$_SERVER['HTTP_HOST'];
 								// $varGet = file_get_contents( $url."/matrix/movhos/procesos/impresionMedicamentosControl.php?wemp_pmla=".$wemp_pmla."&historia=".$pac['his']."&ingreso=".$pac['ing']."&fechaKardex=".$fecha."&consultaAjax=10", false, $contexto );
								$url .= "/matrix/movhos/procesos/impresionMedicamentosControl.php?wemp_pmla=".$wemp_pmla."&historia=".$pac['his']."&ingreso=".$pac['ing']."&fechaKardex=".$fecha."&consultaAjax=10";
								?>
								<script>
									try{
										$.post("<?= $url ?>",function(data){})
									}
									catch(e){}
								</script>
								<?php
							}
							
							$usuario = $auxUsuario;
						}
						else{
							return false;
						}
					}
					else{
						return false;
					}
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
}

function esGrupoAntibiotico( $conex, $wbd, $wemp_pmla, $grp ){
	
	$val = false;
	
	$grpAntibioticos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "gruposMedicamentosAntibioticos" );
	
	$grpAntibioticos = explode( ",", $grpAntibioticos );
	
	$val = in_array( $grp, $grpAntibioticos );
	
	return $val;
}
 
$usuarioValidado = true;
$wactualiz = "Diciembre 18 de 2017";

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

/*****************************
 * INCLUDES
 ****************************/
include_once("movhos/kardex.inc.php");

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
encabezado("Perfil Farmacoterapéutico",$wactualiz,"clinica");

if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}else{
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	//Forma
	echo "<form name='forma' action='perfilFarmacoterapeutico.php' method='post'>";

	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' id='usuario' value='".$wuser."'/>";
	echo "<input type='HIDDEN' NAME= 'centroCostosUsuario' value='".$usuario->centroCostos."'/>";
	echo "<input type='HIDDEN' NAME= 'centroCostosGrabacion' value='".$usuario->centroCostosGrabacion."'/>";
	
	$wipimpresora = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ImpresoraStickersDispen');
	echo "<input type='HIDDEN' id='wipimpresora' name='wipimpresora' value='".$wipimpresora."'/>";

	$datosUsuario = consultarUsuario($conex,$wuser);
	echo "<input type='HIDDEN' NAME= 'usuariodes' id='usuariodes' value='".$datosUsuario->descripcion."'/>";
	
	if(!isset($editable) || empty($editable) ){
		$editable="on";
	}
	echo "<input type='HIDDEN' NAME='editable' value='".$editable."'/>";

	//Verificar que la fecha de consulta sea la actual de lo contrario todo el perfil sera de consulta
	$fechaActual = date("Y-m-d");
	$esFechaActual = false;

	if(isset($wfecha) && $wfecha == $fechaActual){
		$esFechaActual = true;
	}

	//Campos ocultos
	if(isset($fechaactual)){
		echo "<input type='HIDDEN' NAME= 'wfechaactual' value='".$fechaactual."'/>";
	}

	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...";
	echo "</div>";

	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}

	//FC para hacer las acciones
	switch ($waccion){
		case 'a':		// Consulta del detalle de medicamentos del kardex para modificación
			/*
			 * -whistoria
			 * -wfecha
			 */
			if(isset($whistoria) && isset($wfecha)){
				echo "<input type='hidden' name='wfechagrabacion' value='$wfecha'>";

				if(!isset($wingreso) || empty($wingreso)){
					$wingreso = '';
				}
				$paciente = consultarInfoPacienteKardex($whistoria,$wingreso);
				
				echo "<input type='hidden' id='servicioPaciente' name='servicioPaciente' value='$paciente->servicioActual'>";				

				if(!empty($paciente->ingresoHistoriaClinica)){
					$fechaPorIngreso = "";
					$kardexActual = new kardexDTO();

					//Si el consecutivo de ingreso fue digitado por el usuario, se consulta la ultima fecha del kardex generado para ese ingreso
					if( isset($wingreso) && $wingreso != '' ){
						
						$fechaPorIngreso = ultimaFechaKardexHistoriaIngreso($whistoria,$wingreso);

						if($fechaPorIngreso != ""){
							$wfecha = $fechaPorIngreso;
						}
					}
					
					/******************************************************************************************
					 * Septiembre 16 de 2013
					 ******************************************************************************************/
					 
					$detEditable = false;

					if($esFechaActual){
						$detEditable = true;
					}

					//Si es un auxiliar de enfermeria o por cualquier otro motivo el flag editable en la url inactivará la edición
					if(isset($editable) && $editable == "off" || $wfecha == $fechaPorIngreso){
						$detEditable = false;
					} 
					
					if( $detEditable ){
						crearKardexAutomaticamente( $conex, $wbasedato, $whistoria, $paciente->ingresoHistoriaClinica, $wfecha );
					}
					/******************************************************************************************/
					
					/***************************************************************************************************/
					/* Marzo 23 de 2011 Se desactivan estas las tres lineas siguientes  							   */
					/***************************************************************************************************/
					$usuario->centroCostosGrabacion = "*";
					if($paciente->servicioActual == "1183" && ($usuario->centroCostos == "1050" || $usuario->centroCostos == "1051")){
					}
					/***************************************************************************************************/
					
					$kardexActual = consultarKardexPorFechaPaciente($wfecha, $paciente);
					
//					$kardexActual->nombreUsuarioQueModifica;
					
					//Se pregunta por el centro de costos de enfermería, para no restringir ver el perfil al usuario
//					$usuario->centroCostosGrabacion = "*";	//Marzo 18 de 2011
//					$kardexActualPiso = consultarKardexPorFechaPaciente($wfecha, $paciente);
//
					$esKardexNuevo = true;
//
					if( !empty($kardexActual->historiaClinica) || !empty($kardexActualPiso->historiaClinica) ){
						$esKardexNuevo = false;
					}
//
//					/********************************************************************************************************
//					 * Marzo 23 de 2011
//					 * 
//					 * Corrección de validación de los encabezados del kardex.
//					 ********************************************************************************************************/
//					$encabezados = consultarEncabezadosPacientesPorFecha( $conex, $wbasedato, $wfecha, $paciente );
//					
//					$estaKardexConfirmado = true;
//					$sinCco = false;	//Indica que ya que la variable $kardexActual ya fue inicializada con un encabezado del kardex
//					$kadexGrabado = true;
//					
//					foreach( $encabezados as $keyEncabezado => $valueEncabezado ){
//						
//						if( !empty($valueEncabezado->historiaClinica) ){
//							$esKardexNuevo = false;
//						}
//						
//						if( $valueEncabezado->confirmado != "on" ){
//							$estaKardexConfirmado = false;
//						}
//						
//						
//						if( $valueEncabezado->grabado == "off" ){
//							$kadexGrabado = false;
//							$usuarioQueModifica = $valueEncabezado->nombreUsuarioQueModifica;
//						}
//						
//						if( $valueEncabezado->centroCostos == "*" && !$sinCco ){
//							
//							$kardexActual = $valueEncabezado;
//							$sinCco = true;
//						}
//					}
//					
//					if( !$sinCco ){
//						$kardexActual = @$encabezados[0];
//					}
//					
//					if( $paciente->ultimoMvtoHospitalario == "Alta definitiva" ){
//						$estaKardexConfirmado = true;
//					}
//					/********************************************************************************************************/
//					
//					$usuario->centroCostosGrabacion = "*";

					echo "<input type='hidden' id='whistoria' name='whistoria' value='$paciente->historiaClinica'>";
					echo "<input type='hidden' id='wingreso' name='wingreso' value='$paciente->ingresoHistoriaClinica'>";
					echo "<input type='hidden' id='wfecha' name='wfecha' value='$wfecha'>";

					echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='0'/>";
					
					/**************************************************************************
					 * Mayo 30 de 2011
					 **************************************************************************/
					if( $kardexActual->confirmado != "on" && $paciente->altaDefinitiva == 'on' ){
						$kardexActual->confirmado = "on";
					}
					/**************************************************************************/

					$ordenConfirmado = false;
					if( ir_a_ordenes( $wemp_pmla, trim( $paciente->servicioActual ) ) == 'on' ){
						$ordenConfirmado = true;
					}
					
					// /**************************************************************************************************
					 // * Enero 25 de 2017
					 // * Se busca los articulos que son LQ o IC que esten como enviar y se dejan como no enviar
					 // * Esto se hace por que son articulos que fueron creados en urgencias y ahora están en piso
					 // * y dichos articulos son del stock.
					 // **************************************************************************************************/
					// if( !$paciente->enUrgencias ){
						
						// $opciones = array(
						  // 'http'=>array(
							// 'method'=>"GET",
							// 'header'=>"Accept-language: en\r\n",
							// 'content'=>"user=".$user,
						  // )
						// );
						// $contexto = stream_context_create($opciones);
						// $url = 'http://'.$_SERVER['HTTP_HOST'];
						// $url .= "/matrix/hce/procesos/ordenes.inc.php?wemp_pmla=".$wemp_pmla."&his=".$paciente->historiaClinica."&ing=".$paciente->ingresoHistoriaClinica."&consultaAjaxKardex=82";
						// $varGet = file_get_contents( $url, false, $contexto );
					// }
					// /**************************************************************************************************/
					
					//Permite la consulta del detalle del kardex de mendicamentos
					if(!$esKardexNuevo ){
						if( $ordenConfirmado || $kardexActual->confirmado == "on" ){
//						if( $kadexGrabado && $estaKardexConfirmado ){
							
							/*************************
							 * MAESTROS OCULTOS
							 *************************/
							//UNIDADES DE MEDIDA
							echo "<div style='display:none'>";
							echo "<select name='wmunidadesmedida' id='wmunidadesmedida' style='width:120' class='seleccion' style='display:block'>";
							$colUnidades = consultarUnidadesMedida();
							echo "<option value=''>Seleccione</option>";
							foreach ($colUnidades as $unidad){
								echo "<option value='".$unidad->codigo."'>$unidad->descripcion</option>";
							}
							echo "</select>";

							//PERIODICIDADES
							echo "<select id='wmperiodicidades'>";
							echo "<option value=''>Seleccione</option>";
							$colPeriodicidades = consultarPeriodicidades();
							//Otro
							echo "<option value='OT'>Otro</option>";
							foreach ($colPeriodicidades as $periodicidad){
								echo "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
							}
							echo "</select>";

							//FORMAS FARMACEUTICAS
							echo "<select id='wmfftica'>";
							echo "<option value=''>Seleccione</option>";
							$colFormasFarmaceuticas = consultarFormasFarmaceuticas();
							foreach ($colFormasFarmaceuticas as $formasFarmaceutica){
								echo "<option value='".$formasFarmaceutica->codigo."'>$formasFarmaceutica->descripcion</option>";
							}
							echo "</select>";

							//VIAS ADMINISTRACION
							echo "<select id='wmviaadmon'>";
							echo "<option value=''>Seleccione</option>";
							$colVias = consultarViasAdministracion();
							foreach ($colVias as $via){
								echo "<option value='".$via->codigo."'>$via->descripcion</option>";
							}
							echo "</select>";

							//EXAMENES DE LABORATORIO
							echo "<select id='wmexamenlab'>";
							$examenesLaboratorio = consultarExamenesLaboratorio();

							echo "<option value=''>Seleccione</option>";
							foreach ($examenesLaboratorio as $examen){
								echo "<option value='$examen->codigo'>$examen->descripcion</option>";
							}
							echo "</select>";

							//ESTADOS DEL EXAMEN DEL LABORATORIO
							echo "<select id='wmestadosexamenlab'>";
							//Maestro de estados posibles del examen de laboratorio
							$colEstadosExamen = consultarEstadosExamenesLaboratorio();

							foreach ($colEstadosExamen as $estadoExamen){
								echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
							}
							echo "</select>";

							//CONDICIONES DE SUMINISTRO DE MEDICAMENTOS
							echo "<select id='wmcondicionessuministro'>";
							$colCondicionesSuministro = consultarCondicionesSuministroMedicamentos('');
							echo "<option value=''>Seleccione</option>";
							foreach ($colCondicionesSuministro as $condicion){
								echo "<option value='$condicion->codigo'>$condicion->descripcion</option>";
							}
							echo "</select>";
							echo "</div>"; //Fin maestros ocultos

							echo '<span class="subtituloPagina2" align="center">';
							echo 'Información demográfica';
							echo "</span><br><br>";

							echo "<table align='center' width='90%'>";

							echo "<tr>";

							//Historia clinica
							echo "<td class='fila1'>Historia cl&iacute;nica</td>";
							echo "<td class='fila2' colspan='3'>";
							echo $paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica;
							echo "</td>";

							//Nombres
							echo "<td class='fila1'>Nombres y apellidos</td>";
							echo "<td class='fila2'>";
							echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
							echo "</td>";

							//Ingreso
							echo "<td class='fila1'>Fecha y hora de ingreso</td>";
							echo "<td class='fila2'>";
							echo "$paciente->fechaIngreso - $paciente->horaIngreso";
							echo "</td>";

							echo "</tr>";

							echo "<tr>";

							//Servicio actual y habitacion
							$ccoActual = consultarCentroCosto($conex,$paciente->servicioActual, $wbasedato);
							echo "<td class='fila1'>Habitaci&oacute;n</td>";
							echo "<td class='fila2' colspan='3'>";
							echo "$ccoActual->nombre - $paciente->habitacionActual";
							echo "</td>";
							
							//Usuario
							echo "<td class='fila1'>Entidad responsable</td>";
							echo "<td class='fila2'>";
							echo "$paciente->numeroIdentificacionResponsable - $paciente->nombreResponsable";
							echo "</td>";

							//Calculo de dias de hospitalizcion desde ingreso
							$diaActual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
							$fecha = explode("-",$paciente->fechaIngreso);
							$diaIngreso = mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);

							$diasHospitalizacion = ROUND(($diaActual - $diaIngreso)/(60*60*24));

							echo "<td class='fila1'>D&iacute;as de hospitalizaci&oacute;n</td>";
							echo "<td class='fila2'>";
							echo $diasHospitalizacion;
							echo "</td>";

							//Enfermera(o) que genera
							$servicio = "";
							switch ($usuario->centroCostos){
								case $centroCostosServicioFarmaceutico:
									$servicio = $descripcionServicioFarmaceutico;
									break;
								case $centroCostosCentralMezclas:
									$servicio = $descripcionCentralMezclas;
									break;
								default:
									$servicio = $descripcionOtroServicio;
									break;
							}

							echo "<tr>";
							echo "<td class='fila1'>Enfermera(o) que actualiza</td>";
							echo "<td class='fila2' colspan='3'>";
							echo "$usuario->codigo - $usuario->descripcion. $servicio $usuario->centroCostos";
							echo "</td>";

							echo "<td class='fila1'>Fecha y hora de generación</td>";
							echo "<td class='fila2'>";
							echo $kardexActual->fechaCreacion." - ".date("H:i:s");
							echo "</td>";

							//Valor de la edad
							$vecAnioNacimiento = explode("-",$paciente->fechaNacimiento);
							echo "<td class='fila1'>Edad</td>";
							echo "<td class='fila2'>";
							echo $paciente->edadPaciente;
							echo "</td>";
							echo "</tr>";

							echo "<tr>";

							$tipo = $color = '';
							clienteMagenta( $paciente->documentoIdentidad, $paciente->tipoDocumentoIdentidad, $tipo, $color );
							
							echo "<td class='fila1'>Movimiento hospitalario</td>";
							
							if( !empty($tipo) ){
								echo "<td class='fondoAmarillo' colspan='2'>";
								echo $paciente->ultimoMvtoHospitalario;
								echo "</td>";
							}
							else{
								echo "<td class='fondoAmarillo' colspan='3'>";
								echo $paciente->ultimoMvtoHospitalario;
								echo "</td>";
							}
							
							/********************************************************
							 * Octubre 25 de 2011
							 ********************************************************/
							//Afinidad
							if( !empty($tipo) ){
								echo "<td align='center' class='fila2' style='font-size:10pt;background-color:$color;width:7%;color:white'><b>";
								echo $tipo;
								echo "</b></td>";
							}							
							/********************************************************/
							

							//Formula medica
							echo "<td class='fila1'>Ultima fórmula médica</td>";
							echo "<td class='fila2'>";

							if($kardexActual->rutaOrdenMedica != ''){
								echo "<a href='$kardexActual->rutaOrdenMedica' target='_blank'>Ver archivo</a><br/>";
							} else {
								echo "Sin archivo";
							}

							echo "</td>";

							//Es ctc?
							echo "<td class='fila1'>Es usuario CTC</td>";

							echo "<td class='fila2'>";
							if($usuario->esUsuarioCTC){
								echo "Si";
							} else {
								echo "No";
							}
							echo "</td>";

							echo "</tr>";
							
							echo "</table>";
							
							/********************************************************
							 * Abril 25 de 2019
							 * Talla y peso 
							 *******************************************************/
							// exit( $wemp_pmla."-".$conex."-".$wbasedato."-".$whce );
							$talla 	= consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "tallaHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
							
							$esAdulto = consultarSiAdulto($paciente->fechaNacimiento);
					
							// Validar edad del paciente, si es menor a 15 años, 2 meses y 15 dias consecutivo 135 o si es mayor 134
							$parametroPeso = "";
							if($esAdulto)
							{
								$parametroPeso = "pesoHCE";
							}
							else
							{
								$parametroPeso = "pesoHCEpediatrico";
							}
							
							$peso 	= consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, $parametroPeso, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
							/*******************************************************/
							
							echo "<table align='center'>";

							echo "<tr><td>&nbsp;</td>";
							echo "</tr>";
							
							echo "<tr>";
							echo "<td colspan=8 class='fila2'>";
							echo "<table>";
							echo "<tr>";
							echo "<td class='fila1' style='width:100;'><b>Talla (m.)</b></td>";
							echo "<td class='fila2' style='width:100;'>".$talla."</td>";
							echo "<td class='fila1' style='width:100;'><b>Peso (Kg.)</b></td>";
							echo "<td class='fila2' style='width:100;'>".$peso."</td>";
							echo "</tr>";
							echo "</table>";
							echo "</td>";
							echo "</tr>";
							

							echo "<tr>";

							//Alergias y diagnosticos anteriores
							$alergiasAnteriores = "";
							$diagnosticosAnteriores = "";

							consultarAlergiasDiagnosticosAnteriores($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$alergiasAnteriores,$diagnosticosAnteriores);
							
							// // Consultar alertas en movhos_000220
							// $alergiasAnteriores = consultarAlergiaAlertas($paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
							
							
							
							/***************************************************************************************************
							 * Abril 15 de 2011
							 ***************************************************************************************************/
							$colMedicos = consultarMedicosTratantesDefinitivoKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha);
							
//							echo "<tr>";
							
							echo "<td class='fila1'>Medico(s) tratante(s)</td>";
							echo "<td class='fila2'>";
							foreach ($colMedicos as $medico) {
								if(!empty($medico->id)){
									echo $medico->nombre1." ".$medico->nombre2." ".$medico->apellido1." ".$medico->apellido2."<br>";
								}
							}
//							echo "<textarea name='txAlerg' cols=30 rows=4 readonly>$kardexActual->antecedentesPersonales</textarea>";
							echo "</td>";
							/****************************************************************************************/

							echo "<td class='fila1'>Diagn&oacute;sticos</td>";
							echo "<td class='fila2' align=center>";
							// echo "<textarea name='txDiag' cols=30 rows=4 readonly>$kardexActual->diagnostico</textarea>";
							echo "<textarea name='txDiag' cols=30 rows=4 readonly>".$diagnosticosAnteriores."</textarea>";
							echo "</td>";

							echo "<td class='fila1'>Alergias</td>";
							echo "<td class='fila2' align=center>";
							echo "<textarea name='txAlerg' cols=30 rows=4 readonly>$alergiasAnteriores</textarea>";
							echo "</td>";

							echo "<td class='fila1'>Antecedentes personales</td>";
							echo "<td class='fila2' align=center>";
							echo "<textarea name='txAlerg' cols=30 rows=4 readonly>$kardexActual->antecedentesPersonales</textarea>";
							echo "</td>";						
							echo "</tr>";
							
							//Medicamentos de consumo habitual
							echo "<tr>";
							echo "<td align=center class='fila1' colspan=8>";
							echo "<br><p onclick='ocultar_mostrar();' style='cursor:pointer;'><b>Medicamentos de consumo habitual (Ver)</b></br>";
							echo "<div id='med_consumo_hab' style='display:none;'>";
							$datosMedConsuHab = consultarMedConsuHabitual( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "MedicamentosConsumoHabitual", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
							
							$array_datosMedConsuHab = explode("*",$datosMedConsuHab); //Creo un arreglo con el valor de los med. de consu. habitual.
							unset($array_datosMedConsuHab[0]); //Se elimina la posicion 0 porque no contien valores para mostrar.
							
							$wtabla_medConsumoHabitual .= "<table >";
							$wtabla_medConsumoHabitual .= "<tr class=encabezadotabla><td>Medicamento</td><td>Dosis</td><td>Vía</td><td>Frecuencia</td><td>Indicación</td><td>Horario</td><td>Decisión</td><td>Observaciones</td><tr>";
							
							$array_final_mca = array();
							$datos_medConsumoHab = array();
							
							foreach($array_datosMedConsuHab as $key => $value){
								
								$datos_medConsumoHab = explode("|",$value);
								
								foreach($datos_medConsumoHab as $key1 => $value1){
															
									if( !array_key_exists( $key, $array_final_mca ) ){
										
										if($datos_medConsumoHab[2] == 'Seleccione'){
											$datos_medConsumoHab[2] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}
										
										if($datos_medConsumoHab[3] == 'Seleccione'){
											$datos_medConsumoHab[3] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}
										
										if($datos_medConsumoHab[4] == 'Seleccione'){
											$datos_medConsumoHab[4] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}
										
										if($datos_medConsumoHab[6] == 'Seleccione'){
											$datos_medConsumoHab[6] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}
										
										$array_via = explode("-", $datos_medConsumoHab[2]);
										$array_frecuencia = explode("-", $datos_medConsumoHab[3]);
										$array_decision = explode("-", $datos_medConsumoHab[6]);
										
										$array_final_mca[$key] = array('medicamento'=>$datos_medConsumoHab[0],'dosis'=>$datos_medConsumoHab[1], 'via'=>$datos_medConsumoHab[2], 'frecuencia'=>$datos_medConsumoHab[3], 'indicaciones'=>$datos_medConsumoHab[4],'horario'=>$datos_medConsumoHab[5],'decision'=>$datos_medConsumoHab[6], 'observaciones'=>$datos_medConsumoHab[7]); 
										$wtabla_medConsumoHabitual .= "<tr class=fila2><td>$datos_medConsumoHab[0]</td><td>$datos_medConsumoHab[1]</td><td>$array_via[1]</td><td>$array_frecuencia[1]</td><td>$datos_medConsumoHab[4]</td><td>$datos_medConsumoHab[5]</td><td>$array_decision[1]</td><td>$datos_medConsumoHab[7]</td></tr>"; 
															
									}					
								
								}				
								
							}				
							
							$wtabla_medConsumoHabitual .= "</table>";							
							echo "<div>".$wtabla_medConsumoHabitual."</div>";
							echo "<br/>&nbsp;</td>";
							echo "</tr>";

							echo "</table>";
							echo "</div>";
							echo "<br>";
							
							/************************************************
							 * Agrengando mensajeria
							 ************************************************/
							//Campo oculto que indica de que programa se abrio
							
							echo "<INPUT type='hidden' id='mesajeriaPrograma' value='Perfil'>";
							//echo "<INPUT type='hidden' id='usuario' name='usuario' value='$usuario->codigo'>";
							 
							echo "<table style='width:80%;font-size:10pt' align='center'>";
			
							echo "<tr><td class='encabezadotabla' align='center' colspan='3'>Mensajer&iacute;a Kardex</td></tr>";
							
							echo "<tr>";
							
							//Area para escribir
							echo "<td style='width:45%;' rowspan='2'>";
							// echo "<textarea id='mensajeriaKardex' onKeyPress='return validarEntradaAlfabetica(event);' style='width:100%;height:80px'></textarea>";
							echo "<textarea id='mensajeriaKardex' style='width:100%;height:80px'></textarea>";	//Noviembre 21 de 2011
							echo "</td>";
							
							//Boton Enviar mensaje
							echo "<td align='center' style='width:10%'>";
							echo "<input type='button' onClick=\"javascript:enviandoMensaje()\" value='Enviar' style='width:100px'>";
							echo "</td>";
							
							//Mensajes
							echo "<td style='width:45%' rowspan='2'>";
							echo "<div id='historicoMensajeria' style='overflow:auto;font-size:10pt;height:80px'>";
							echo "</div>";
							echo "</td>";
							
							echo "</tr>";
							
							echo "<tr>";
							echo "<td align='center'><b>Mensajes sin leer: </b><div id='sinLeer'></div></td>";
							echo "</tr>";
							
							echo "</table>";
							/****************************************************************/
							
							
							
							

							//Buscador de medicamentos para sustituir
							echo "<div id='fixeddiv' style='position:absolute;display:none;z-index:110;width:450px;height:370px;left:200px;top:10px;padding:5px;background:#FFFFFF;border:2px solid #2266AA'>";
							echo "<table>";

							echo "<tr>";
							echo "<td colspan=4 align=center class='encabezadoTabla'>";
							echo "<b>Buscador de medicamentos</b>";
							echo "</td>";
							echo "</tr>";

							echo "<tr>";

							echo "<td class='fila1'>C&oacute;digo</td>";
							echo "<td class='fila2'>";
							echo "<INPUT TYPE='text' NAME='wcodmed' SIZE=20  class='textoNormal'>";
							echo "</td>";

							echo "</tr>";

							echo "<tr>";
							echo "<td class='fila1'>Nombre</td>";
							echo "<td class='fila2'>";
							echo "<INPUT TYPE='text' NAME='wnommed' SIZE=20 class='textoNormal'>";
							echo "</td>";
							echo "</tr>";

							echo "<tr>";
							echo "<td colspan=4 align=center class='encabezadoTabla'>";
							echo "<b>Parametros de consulta</b>";
							echo "</td>";
							echo "</tr>";

							echo "<tr>";
							echo "<td colspan=4 align=center class='fila2'>";
							echo "N.Genérico<input type='radio' id='wtipoart' name='wtipoart' value='G'>&nbsp;N.Comercial<input type='radio' id='wtipoart' name='wtipoart' value='C' checked>";
							echo " | ";

							echo "<select id='wunidadmed' name='wunidadmed' class='seleccionNormal'>";

							$colUnidades = consultarUnidadesMedida();

							echo "<option value='%'>Cualquier unidad de medida</option>";
							foreach ($colUnidades as $unidad){
								echo "<option value='".$unidad->codigo."'>$unidad->codigo - $unidad->descripcion</option>";
							}

							echo "</select>";
							echo "</td>";
							echo "</tr>";

							echo "<tr><td colspan=4 align=center>";
							echo "<input type='button' value='Consultar' onclick='javascript:consultarMedicamentoPerfil();'>&nbsp;|&nbsp;<input type='button' value='Cerrar' onclick='return fixedMenu.hide();'>";
							echo "</td></tr>";

							echo "<tr>";
							echo "<td colspan=4 class='fila2'>";
							echo "<img id='imgCodMed' style='display:none' src='../../images/medical/ajax-loader5.gif'>";
							echo "<div id='cntMedicamento' style='overflow-y: scroll; width: 430px; height: 160px;'>";
							echo "</div>";
							echo "</td>";
							echo "</tr>";

							echo "<tr>";
							echo "<td colspan=4 class='fila2'>";
							echo "<span>NOTA: Realice su búsqueda específica, este buscador únicamente retornará los primeros diez resultados</span>";
							echo "</td>";
							echo "</tr>";

							echo "<tr>";
							echo "<td colspan=4 class='fila2'>";
							echo "<a href='../../registro.php?call=1&Form=000059-movhos-C-Definicion de fracciones&Frm=0&tipo=P&key=movhos' target='_blank' class='vinculo'>Ir a definición fracciones</a>";
							echo "</td>";
							echo "</tr>";

							echo "</table>";
							echo "</div>";

							echo "<br>";

							echo "</div>";

							$detalleEditable = false;

							//$colDetalleKardex = consultarDetalleDefinitivoKardex($conex,$paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);
							$colDetalleKardex = consultarDetalleDefinitivoPerfil($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);
							$elementosActuales = count($colDetalleKardex);

							if($esFechaActual){
								$detalleEditable = true;
							}

							//Si es un auxiliar de enfermeria o por cualquier otro motivo el flag editable en la url inactivará la edición
							if(isset($editable) && $editable == "off" || $wfecha == $fechaPorIngreso){
								$detalleEditable = false;
							}
							
//							if( $paciente->altaDefinitiva == 'on' ){
//								$detalleEditable = false;
//							}

							$colDetalleAnteriorKardex = consultarDetallePerfilKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);
							$elementosAnteriores = count($colDetalleAnteriorKardex);
							$cont1 = 0;

							echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='0'/>";


							echo "<div align=right>";
							//Check de aprobación por parte del regente
							/*
							if($elementosActuales > 0 && $detalleEditable){
								echo "<font size=3><strong>Aprobado (S/N)</strong></font>";
								if($kardexActual->aprobado){
									echo "<input type='checkbox' name='wchkapr' id='wchkapr' checked>&nbsp;";
								} else {
									echo "<input type='checkbox' name='wchkapr' id='wchkapr'>&nbsp;";
								}
								echo "<input type='button' value='Grabar estado aprobaci&oacute;n' onclick='javascript:grabarAprobacionRegente11(1);'>&nbsp;|&nbsp;";
							}
							*/
							echo "<input type='button' value='Regresar' onclick='javascript:inicioPerfil();'>&nbsp;|&nbsp;";
							echo "<input type=button value=' X ' onclick='javascript:cerrarVentana();'>";
							echo "</div>";

							/*****************
							 * INICIO DE LA DIVISIÓN POR PESTAÑAS.
							 *****************/
							echo "<div id='tabs' class='ui-tabs'>";				//Inicio de lo que va a ir encerrado en las pestañas

							echo "<ul>";
							echo "<li><a href='#fragment-1'><span>Medicamentos del d&iacute;a</span></a></li>";
							echo "<li><a href='#fragment-2'><span>Medicamentos anteriores</span></a></li>";
							echo "<li><a href='#fragment-3'><span>Otros</span></a></li>";
							echo "</ul>";

							echo "<div id='fragment-1'>";

							//Convencion de colores del kardex
							echo "<div align=right style='height:80px'>";
							echo "<div align='left' style='float:left;height:100%'>"; 
							echo "<image src='../../images/medical/hce/icono_imprimir.png' style='position:relative;top:50%;left:50%' onclick='mostrarImprimirSticker();' title='Imprimir stickers'>";
							echo "</div>";
							echo "<div align=right  style='float:rigth'>";
							echo "<table>";
							echo "<tr align=center>";
//							echo "<td class='fila1' width=80>Normal</td>";
							echo "<td class='fila2' width=80>Normal</td>";
							echo "<td class='articuloAntibiotico' width=80>Antibi&oacute;tico</td>";
							echo "<td class='articuloCm1' width=80>Central mezclas o crear DA o NE</td>";
							echo "<td class='articuloNuevoPerfil' width=80>Nuevo</td>";
							echo "<td class='suspendido' width=80>Suspendido</td>";
							echo "<td class='fondoAmarillo' width=80>Control</td>";
							echo "<td class='fondoVioleta' width=80>No POS</td>";
							echo "<td class='fondoAmarilloOscuro' width=80>Revisar<br />CTC</td>";
							echo "<td class='fondoGris' width=80>No enviar</td>";
							echo "</tr>";
							echo "</table>";
							echo "</div>";
							echo "</div>";

							if($elementosActuales > 0){
							
								/******************************************************************************************
								 * Mayo 30 de 2012
								 *
								 * Antes de actualizar consulto cual fue la ultima fecha y hora en que fue abierto el perfil
								 ******************************************************************************************/
								// $fhPerfilVisto = consultarUltimaVistaPerfil( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
								 
								// if( $esFechaActual ){
									// actualizarFechaHoraPerfilVisto( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha );
								// }
								/******************************************************************************************/
							
								//Muestra el detalle de medicamentos del kardex consultado
//								echo '<span class="subtituloPagina2" align="center">';
//								echo "Detalle medicamentos para la fecha $wfecha";
//								echo "</span><br>";

								echo "<div>";

								echo "<br>";

								echo "<table align='center' border=0 id='tbDetalle'>";

								echo "<thead>";
								echo "<tr align='center' class='encabezadoTabla'>";

								//Encabezado detalle kardex
								if($detalleEditable){
									echo "<td>Act.</td>";
								}
								echo "<td colspan='2'>Observaciones</td>";
								echo "<td>Articulo</td>";
								echo "<td>Dosis</td>";
								echo "<td>Unidad de<br>presentaci&oacute;n</td>";
								echo "<td>Frecuencia</td>";
								echo "<td>Enviar</td>";
								echo "<td>Cant. pendiente dia anterior</td>";
								echo "<td>Cantidad dia actual</td>";
								echo "<td>TOTAL A GRABAR</td>";
								echo "<td>Saldo del<br>paciente</td>";
								echo "<td>Condicion</td>";
								echo "<td>Forma farmaceutica</td>";
								echo "<td>Fecha y hora inicio</td>";
								echo "<td>Conf.</td>";
								echo "<td>Pri.</td>";
								echo "<td>";
								echo "<br>Apr.<br>reg.<br>";
								
								if($detalleEditable){	//Octubre 24 de 2011
									echo "<input type='checkbox' onClick='javascript:marcarAprobacionArticulos(this.checked);'>";
								}
								
								echo "</td>";
								echo "<td>Via</td>";
								echo "<td>Reemplazar articulo por</td>";
								echo "<td>Dias trat.</td>";
								echo "<td>Dosis max.</td>";
								echo "<td>Aut. CTC</td>";
								echo "<td>Usado/ Disp. CTC</td>";

								//Encabezado detalle kardex
								if($detalleEditable){
									echo "<td>Act.</td>";
								}
								echo "</tr>";
								echo "</thead>";

								//Detalle
								$contArticulos = 1;

								echo "<tbody id='detKardex'>";
								
								foreach ($colDetalleKardex as $articulo){
								
									//Si está cómo no dispensable y cómo no enviar no lo muestro
									if( $articulo->estadoAdministracion == 'on' && $articulo->dispensable == 'off' ){
										continue;
									}
									
									//Si es del stock no se muestra
									if( esStock( $conex, $wbasedato, $articulo->consultarCodigoArticulo(), $paciente->servicioActual ) ){
										continue;
									}
								
									/********************************************************************************
									 * Mayo 30 de 2012
									 ********************************************************************************/
									// if($fhPerfilVisto ){
									if( $articulo->aprobado != 'on' ){
									
										//Consulto ultimo registro modificado
										// $ultimaModificacion = consultarUltimaModificacionPorMedicamento( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fhPerfilVisto[0], $fhPerfilVisto[1], $articulo->consultarCodigoArticulo(), $articulo->idOriginal );
										$ultimaModificacion = consultarUltimaModificacionPorMedicamento( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, "0000-00-00", "00:00:00", $articulo->consultarCodigoArticulo(), $articulo->idOriginal );
										
										if( $ultimaModificacion ){
										
											$crearBlinkInicial = "<blink>";
											$crearBlinkFinal = "</blink>";
										}
										else{
											$crearBlinkInicial = "";
											$crearBlinkFinal = "";
										}
									}
									else{
										$crearBlinkInicial = "";
										$crearBlinkFinal = "";
									}
									/********************************************************************************/

									
									if($contArticulos % 2 == 0){
										$clase = "fila2";
									} else {
										$clase = "fila2";
									}

									if($articulo->fechaCreacion == $fechaActual){
										$clase = "articuloNuevoPerfil";
									}

									if($articulo->origen == 'CM'){
										$clase = "articuloCm1";
									}

									$grupo = explode("-",$articulo->grupo);
									
									$gruposDeControl =  consultarAliasPorAplicacion( $conex, $wemp_pmla, "gruposControl" );
									$esDeControl = ( array_search( $grupo[0], explode( ",", $gruposDeControl ) ) !== false ) ? true : false ;
									
									// if($grupo[0] == $grupoControl){
									if( $esDeControl ){
										$clase = "fondoAmarillo";
									}

									//Si es no POS tiene mayor precedencia
									if(!$articulo->esPos){
										$clase = "fondoVioleta";
									}
									
									//Si el medicamento es No enviar - Estado de administración
									if( $articulo->estadoAdministracion == 'on' ){
										$clase = "fondoGris";
									}
									
									//Si el medicamento está suspendido
									if($articulo->suspendido == 'on'){
										$clase = "suspendido";
									}
									
									//Si está suspendido
									$auxEditable = $detalleEditable;
									if( strtolower( $articulo->suspendido ) == 'on' ){
										$detalleEditable = false;
									}
									
									//Si esta marcado como no enviar, puede agregar observaciones, la variable $detalleEditable cambiara de valor a false, 
									//dependiendo de las restricciones que tiene el programa, en las siguientes lineas. (Jonatan Lopez / 05 Marzo 2014)
									if($detalleEditable && $articulo->estadoAdministracion == 'on'){
										$detalleEditable = true;
									}


									////////////////////////////////////////////////////////////////////
									// Agosto 15 de 2013
									//Obtengo la información del tooltip
									$informacion = "<strong>$articulo->codigoArticulo</strong><br>";
									
									$informacion .= "<br><strong>Nombre Gen&eacute;rico: </strong>$articulo->nombreGenerico<br>";

									$unidadManejo = "";
									foreach ($colUnidades as $unidad){
										if($unidad->codigo == $articulo->unidadDosis){
											$unidadManejo = $unidad->descripcion;
										}
									}
									$informacion .= "<br>".$articulo->cantidadDosis." ".$unidadManejo." POR ".descripcionMaestroPorCodigo($colUnidades,$articulo->unidadManejo)."</strong><br><br>";

									//Ctc
									if(isset($articulo->cantidadAutorizadaCtc) && !empty($articulo->cantidadAutorizadaCtc)){

										$miniClase = "";
										$diferenciaCtc = intval($articulo->cantidadAutorizadaCtc)-intval($articulo->cantidadUtilizadaCtc);
										
										// Definición de alerta si el artículo está a punto de agotarse por CTC
										$porcentajeUsoCtc = ($articulo->cantidadUtilizadaCtc/$articulo->cantidadAutorizadaCtc)*100;
										if($porcentajeUsoCtc >= $topePorcentualCtc){
											$miniClase = "fondoAmarilloOscuro";
											$clase = "fondoAmarilloOscuro";
											mensajeEmergente("El articulo ".trim($articulo->codigoArticulo)." está a punto de agotarse o se agotó por CTC.  Utilización: ".intval($porcentajeUsoCtc)."%");
										}
										else
										{
											$miniClase = "fondoVerde";				
										}
									
										$informacion .= "<br><strong>ESTADO DE CTC</strong><br>";
										$informacion .= "Autorizado: $articulo->cantidadAutorizadaCtc<br>";
										$informacion .= "Utilizado	: $articulo->cantidadUtilizadaCtc<br>";
										$informacion .= "<span class=$miniClase><strong>DISPONIBLE</strong>: $diferenciaCtc</span><br>";
										$informacion .= "<br>";
									}

									if($articulo->esDispensable == 'on'){
										$informacion .= "-Es dispensable<br>";
									} else {
										$informacion .= "-No es dispensable<br>";
									}

									if($articulo->esDuplicable == 'on'){
										$informacion .= "-Es duplicable<br>";
									} else {
										$informacion .= "-No es duplicable<br>";
									}

									if($articulo->diasTotalesTto > 0){
										$informacion .= "-D&iacute;as acumulados: ".$articulo->diasTotalesTto."<br>";
									} else {
										$informacion .= "-D&iacute;as acumulados: 0<br>";
									}

									if($articulo->dosisTotalesTto > 0){
										$informacion .= "-Dosis acumuladas: ".$articulo->dosisTotalesTto."<br>";
									} else {
										$informacion .= "-Dosis acumuladas: 0<br>";
									}
									////////////////////////////////////////////////////////////////////

									
									// if($articulo->suspendido == 'on'){
										// echo "<tr id='tr$contArticulos' title='$informacion' class='suspendido' align='center'>";
									// }else{
										// echo "<tr id='tr$contArticulos' title='$informacion' class=$clase align='center'>";
									// }
									
									echo "<tr id='tr$contArticulos' title='$informacion' class=$clase align='center'>";

									$eventosQuitarTooltip = " onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'";	//Creo los eventos que quitan el tooltip si el kardex es editable

									
									//Acciones
									if($detalleEditable){
										echo "<td align=center $eventosQuitarTooltip>";
										echo "<a href='javascript:grabarArticuloPerfil($contArticulos,\"on\");'><img src='../../images/medical/root/grabar.png' alt='Modificar articulo' ></a>&nbsp;";
										echo "</td>";
									}
									elseif( $editable == 'on' && ( strtolower( $articulo->suspendido ) == 'on' || $articulo->estadoAdministracion == 'on' ) ){
										echo "<td></td>";
									}
									
									//Observaciones
									echo "<td $eventosQuitarTooltip align='center'>";
									if($detalleEditable){
										echo "<textarea class='textoObservaciones' id='wtxtobsadd$contArticulos' style='width:180px;height:73px;' onblur='javascript:grabarArticuloPerfil($contArticulos,\"off\");'>";
										echo "</textarea>";
									} else {
										echo "<textarea class='textoObservaciones' id='wtxtobsadd$contArticulos' style='width:180px;height:73px;' readonly>";
										echo "</textarea>";
									}
									echo "</td>";
									
									//Agosto 28 de 2013
									//Observaciones historico
									echo "<td $eventosQuitarTooltip align='center'>";
										echo '<div style="overflow:auto;width:270px;height:70px;background:#fff;border:1px #999 solid;font-size:9pt;text-align:left;">'.$articulo->observaciones.'</div>';
										echo "<input type='hidden' id='wtxtobs$contArticulos' value='".$articulo->observaciones."'>";
									echo "</td>";
									
									//Si esta marcado como no enviar, puede agregar observaciones. (Jonatan Lopez / 05 Marzo 2014)
									if($articulo->estadoAdministracion == 'on'){
										$detalleEditable = true;
									}									
									

									//Articulo
									if( $articulo->dosisAdaptada || $articulo->noEnteral ){
										echo "<td class='articuloCm1'>";
										echo "$crearBlinkInicial<div id='wnmmedact$contArticulos'>$articulo->codigoArticulo</div>$crearBlinkFinal";
										echo "</td>";
									}
									else{
										$codigoGrupo = "";
										list( $codigoGrupo ) =explode( "-", $articulo->grupo );
										
										$esGrupoAntibiotico = esGrupoAntibiotico( $conex, $wbasedato, $wemp_pmla ,$codigoGrupo  );
										
										$classGrupoAntibiotico = "";
										if( $esGrupoAntibiotico ){
											$classGrupoAntibiotico = "class='articuloAntibiotico'";
										}
										
										echo "<td $classGrupoAntibiotico>";
										echo "$crearBlinkInicial<div id='wnmmedact$contArticulos'>$articulo->codigoArticulo</div>$crearBlinkFinal";
										echo "</td>";
									}

									//En caso de que exista un cambio de articulo se debe grabar junto con el articulo, la forma farmaceutica, el origen y la unidad de dosis
									echo "<input type='hidden' name='wfftica$contArticulos' id='wfftica$contArticulos'>";
									echo "<input type='hidden' name='wudosis$contArticulos' id='wudosis$contArticulos'>";

									//Dosis y unidad de medida
									echo "<td $eventosQuitarTooltip>";
									echo "$articulo->cantidadDosis ";

									// Arriba ya se asignó este valor no es necesario volver a hacer el ciclo
									// foreach ($colUnidades as $unidad){
										// if($unidad->codigo == $articulo->unidadDosis){
											// echo "<div id='wudosisact$contArticulos'>$unidad->descripcion</div>";
										// }
									// }

									echo "<div id='wudosisact$contArticulos'>".$unidadManejo."</div>";
									
									echo "</td>";
									
									//Unidad de presentación
									echo "<td>";
									echo $articulo->unidadManejo;
									echo "</td>";

									//Periodicidad
									$unidadFrecuencia = 0;
									echo "<td $eventosQuitarTooltip>";

									foreach ($colPeriodicidades as $periodicidad){
										if($periodicidad->codigo == $articulo->periodicidad){
											echo $periodicidad->descripcion;
											$unidadFrecuencia = $periodicidad->equivalencia;
											break;
										}
									}
									echo "</td>";
									
									//No enviar - Estado de administración
									echo "<td $eventosQuitarTooltip>";
									echo ($articulo->estadoAdministracion != 'on')? "S&iacute;" : "No";
									echo "</td>";

									//Cantidad pendiente de despacho dia anterior
									echo "<td $eventosQuitarTooltip>";
									echo "$articulo->saldoDispensacion";
									echo "</td>";
									
									//Cantidad pendiente de despacho dia anterior
									echo "<td $eventosQuitarTooltip>";
									echo intval($articulo->cantidadGrabar-$articulo->saldoDispensacion);
									echo "</td>";
									
									//Cantidad a grabar.
									echo "<td $eventosQuitarTooltip>";
									echo "$articulo->cantidadGrabar";
									echo "</td>";
									
									//Saldo
									echo "<td>";
									echo $articulo->saldo;
									echo "</td>";

									//Condicion
									echo "<td $eventosQuitarTooltip>";
									foreach ($colCondicionesSuministro as $condicion){
										if($condicion->codigo == $articulo->condicionSuministro){
											echo $condicion->descripcion;
										}
									}
									echo "</td>";


									//Forma farmaceutica
									echo "<td $eventosQuitarTooltip>";
									
									/********************************************************
									 * Octubre 27 de 2011
									 ********************************************************/									
									echo "<div id='wffticaact$contArticulos'>";
									foreach ($colFormasFarmaceuticas as $formasFarmaceutica){
										if($formasFarmaceutica->codigo == $articulo->formaFarmaceutica){
											echo $formasFarmaceutica->descripcion;
										}
									}
									echo "</div>";
									/********************************************************/
									
									echo "</td>";

									//Fecha y hora inicio
									echo "<td $eventosQuitarTooltip>";
									echo "<INPUT TYPE='hidden' NAME='whfinicio$contArticulos' id='whfinicio$contArticulos' SIZE=22 readonly class='campo2' value='$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion'>";
									if(false && $articulo->horaInicioAdministracion == "00:00:00"){	//Se desactiva esta condición Marzo 28 de 2011
										echo "$articulo->fechaInicioAdministracion a las:24:00:00";
									} else {
										echo "$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion";
									}
									echo "</td>";

									//Confirmacion
									echo "<td $eventosQuitarTooltip>";
									if($articulo->estaConfirmado == 'on'){
										echo "Si";
									}else{
										echo "No";
									}
									echo "</td>";

									//Prioridad
									echo "<td $eventosQuitarTooltip>";
									
									//Si esta marcado como no enviar, puede agregar observaciones, en este caso la variable $detalleEditable = false, 
									//ya que para este tipo de medicamentos se debe mantener inactivas algunas acciones. (Jonatan Lopez / 05 Marzo 2014)
									if($articulo->estadoAdministracion == 'on'){
										$detalleEditable = false;
									}

									if( $detalleEditable ){ 	//Octubre 24 de 2011
										if($articulo->tienePrioridad == 'on'){
											echo "<input type='checkbox' name='wchkpri$contArticulos' id='wchkpri$contArticulos' alt='Prioridad para cargos' onClick='marcarPrioridad( this )' checked>";	//Octubre 7 de 2011
										} else {
											echo "<input type='checkbox' name='wchkpri$contArticulos' id='wchkpri$contArticulos' alt='Prioridad para cargos' onClick='marcarPrioridad( this )'>";	//Octubre 7 de 2011
										}
									}
									else{ 	//Octubre 24 de 2011
										if($articulo->tienePrioridad == 'on'){
											echo "<input type='checkbox' name='wchkpri$contArticulos' id='wchkpri$contArticulos' checked disabled>";
										}
										else{
											echo "<input type='checkbox' name='wchkpri$contArticulos' id='wchkpri$contArticulos' disabled>";
										}
									}
									echo "</td>";

									//Aprobacion regente
									echo "<td $eventosQuitarTooltip>";
									if( $detalleEditable ){		//Octubre 24 de 2011
										if($articulo->origen == $codigoCentralMezclas){
											if($usuario->esUsuarioCM){
												if($articulo->aprobado == 'on'){
													echo "<input type='checkbox' name='wchkare$contArticulos' id='wchkare$contArticulos' checked onClick='javascript:marcarAprobacionArticulo(\"$contArticulos\");'>";
												} else {
													echo "<input type='checkbox' name='wchkare$contArticulos' id='wchkare$contArticulos' onClick='javascript:marcarAprobacionArticulo(\"$contArticulos\");'>";
												}
											} else {
												if($articulo->aprobado == 'on'){
													echo "<input type='checkbox' name='wchkare$contArticulos' id='wchkare$contArticulos' checked disabled>";
												} else {
													echo "<input type='checkbox' name='wchkare$contArticulos' id='wchkare$contArticulos' disabled>";
												}
											}
										} else {
											if($articulo->aprobado == 'on'){
												echo "<input type='checkbox' name='wchkare$contArticulos' id='wchkare$contArticulos' checked onClick='javascript:marcarAprobacionArticulo(\"$contArticulos\");'>";
											} else {
												echo "<input type='checkbox' name='wchkare$contArticulos' id='wchkare$contArticulos' onClick='javascript:marcarAprobacionArticulo(\"$contArticulos\");'>";
											}
										}
									}
									else{	//Octubre 24 de 2011
										if($articulo->aprobado == 'on'){
											echo "<input type='checkbox' name='wchkare$contArticulos' id='wchkare$contArticulos' checked disabled>";
										}
										else{
											echo "<input type='checkbox' name='wchkare$contArticulos' id='wchkare$contArticulos' disabled>";
										}
									}
									echo "</td>";
									
									//Via administracion
									echo "<td $eventosQuitarTooltip>";
									echo "<select name='wviadmon$contArticulos' id='wviadmon$contArticulos' class='seleccion' style='width:100px' ".( !$detalleEditable ? 'disabled': '' ).">";

									foreach ($colVias as $via){
										if($via->codigo == $articulo->via){
											echo "<option value='".$via->codigo."' selected>$via->descripcion</option>";
										}else{
											echo "<option value='".$via->codigo."'>$via->descripcion</option>";
										}
									}
									echo "</select>";
									echo "</td>";

									//Articulo de reemplazo
									echo "<td $eventosQuitarTooltip nowrap>";
									if($articulo->puedeReemplazar){
										if($detalleEditable){
											echo "<INPUT TYPE='radio' NAME='wsalamandra' id='wsalamandra' value='$contArticulos' onclick='javascript:seleccionReemplazo($contArticulos);'>";
										} else {
											echo "<INPUT TYPE='radio' disabled>";
										}
										echo "<input type='text' id='wnmmed$contArticulos' readonly class='textoNormal' size=27>";
										echo "<a href='javascript:limpiarCampo($contArticulos);'>X</a>";
									} else {
										// if( $articulo->esNutricionParenteral )
											// echo "Reemplazo solo desde CM";
										// else
											// echo "Saldo pendiente";
										
										
										if( $articulo->saldo > 0 )
										{
											echo "Saldo pendiente";
										}
										else
										{
											echo "Reemplazo solo desde CM";
										}
									}
									echo "</td>";
									
									//Si esta marcado como no enviar, puede agregar observaciones, aqui se vuelve a reasignar la variable $detalleEditable a true, ya que se necesitan los valores de dias de tto y dosis maxima impresas y ocultas. (Jonatan Lopez / 05 Marzo 2014)
									if($articulo->estadoAdministracion == 'on'){
										$detalleEditable = true;
									}
									
									//Dias tratamiento
									echo "<td $eventosQuitarTooltip align='center'>";
									if($detalleEditable){
										//echo "<INPUT TYPE='text' NAME='wdiastto$contArticulos' id='wdiastto$contArticulos' SIZE=3 maxlength=3 onkeypress='return validarEntradaEntera(event);' class='campo2' value='$articulo->diasTratamiento' readonly>";
										echo "<INPUT TYPE='hidden' NAME='wdiastto$contArticulos' id='wdiastto$contArticulos' value='$articulo->diasTratamiento'>";
										echo "$articulo->diasTratamiento";
									} else {
										echo "$articulo->diasTratamiento";
									}
									echo "</td>";

									//Dosis máximas
									echo "<td $eventosQuitarTooltip align='center'>";
									if($detalleEditable){
										//echo "<INPUT TYPE='text' NAME='wdosmax$contArticulos' id='wdosmax$contArticulos' SIZE=3 maxlength=3 onkeypress='return validarEntradaEntera(event);' class='campo2' value='$articulo->dosisMaxima' readonly>";
										echo "<INPUT TYPE='hidden' NAME='wdosmax$contArticulos' id='wdosmax$contArticulos' value='$articulo->dosisMaxima'>";
										echo "$articulo->dosisMaxima";
									} else {
										echo "$articulo->dosisMaxima";
									}
									echo "</td>";

									/*
									 $detalle->cantidadAutorizadaCtc 	= $info2['Ctccau'];
									 $detalle->cantidadUtilizadaCtc 		= $info2['Ctccus'];
									 $detalle->unidadesCantidadesCtc 	= $info2['Ctcuca'];
									 */
									
									$disponibleCtc = "";
									$usadoCtc = "";
									if(isset($articulo->cantidadAutorizadaCtc) && !empty($articulo->cantidadAutorizadaCtc)){
										$disponibleCtc = intval($articulo->cantidadAutorizadaCtc)-intval($articulo->cantidadUtilizadaCtc);

										if(isset($articulo->cantidadUtilizadaCtc) && !empty($articulo->cantidadUtilizadaCtc)){
											$usadoCtc = $articulo->cantidadUtilizadaCtc;
										} else {
											$usadoCtc = $articulo->cantidadGrabar;
										}
										$usadoCtc = $articulo->cantidadUtilizadaCtc;
									}
									
									//Si esta marcado como no enviar, puede agregar observaciones, en este caso la variable $detalleEditable = false, 
									//ya que para este tipo de medicamentos se debe mantener inactivas algunas acciones. (Jonatan Lopez / 05 Marzo 2014)
									if($articulo->estadoAdministracion == 'on'){
										$detalleEditable = false;
									}									
									
									//Cantidad autorizada en el CTC
									echo "<td $eventosQuitarTooltip align='center'>";
									if($usuario->esUsuarioCTC){
										if($detalleEditable){
											echo "<INPUT TYPE='text' NAME='wautctc$contArticulos' id='wautctc$contArticulos' SIZE=4 maxlength=4 onkeypress='return validarEntradaEntera(event);' class='campo2' value='$articulo->cantidadAutorizadaCtc'>";	//Abril 15 de 2011
										} else {
											echo "$articulo->cantidadAutorizadaCtc";
										}
									} else {
										echo !empty($articulo->cantidadAutorizadaCtc)? $articulo->cantidadAutorizadaCtc : "0";
									}
									echo "</td>";

									//Cantidad usada en el CTC
									echo "<td $eventosQuitarTooltip align='center'>";
									if($usuario->esUsuarioCTC){
										if($detalleEditable){
											echo "<INPUT TYPE='text' NAME='wusadoctc$contArticulos' id='wusadoctc$contArticulos' SIZE=3 maxlength=3 class='campo2' value='$usadoCtc' readonly>";
											echo "&nbsp;/&nbsp;";
											echo "<INPUT TYPE='text' NAME='wdispctc$contArticulos' id='wdispctc$contArticulos' SIZE=3 maxlength=3 class='campo2' value='$disponibleCtc' readonly>";
										} else {
											echo "$articulo->cantidadUtilizadaCtc/$disponibleCtc";
										}
									} else {
										echo !empty($articulo->cantidadUtilizadaCtc)? $articulo->cantidadUtilizadaCtc : "0";
									}
									echo "</td>";
																		
									
									//Acciones
									if($detalleEditable){
										echo "<td align=center $eventosQuitarTooltip>";
										echo "<a href='javascript:grabarArticuloPerfil($contArticulos,\"on\");'><img src='../../images/medical/root/grabar.png' alt='Modificar articulo' ></a>&nbsp;";
										echo "</td>";
									}
									elseif( strtolower( $articulo->suspendido ) == 'on' || $articulo->estadoAdministracion == 'on' ){
										echo "<td></td>";
									}
									
									echo "<INPUT TYPE='hidden' NAME='wido$contArticulos' id='wido$contArticulos' value='$articulo->idOriginal'>";

									echo "</tr>";
									$contArticulos++;
									
									//Si esta marcado como no enviar, puede agregar observaciones, en este caso la variable $detalleEditable = false, 
									//ya que para este tipo de medicamentos se debe mantener inactivas algunas acciones. (Jonatan Lopez / 05 Marzo 2014)
									if($articulo->estadoAdministracion == 'on'){
										$detalleEditable = false;
									}	
									
																		
									$detalleEditable = $auxEditable;
								}
								echo "</tbody>";

								echo "</table>";

								echo "<br>";
								echo "</div>";
							} else {
							
								/******************************************************************************************
								 * Diciembre 20 de 2012
								 *
								 * Corrijo mensaje, este mensaje puede salir si el kardex del día no se ha creado
								 ******************************************************************************************/
								if( $esFechaActual && $kardexActual->fechaCreacion == $fechaActual ){
									mensajeEmergente("Verifique lo siguiente: \\n-El kardex debe estar grabado.  \\n-El kardex debe tener artículos.  \\n-No tiene articulos a mostrar.");
								}
								else{
									mensajeEmergente("No se ha generado kardex en esta fecha: \\n-Puede generar el kardex desde la PDA.");
									
								}
								/******************************************************************************************/
							}
							echo "</div>";
							echo "<div id='fragment-2'>";

							//Convencion de colores del kardex
							echo "<div align=right>";
							echo "<table>";
							echo "<tr align=center>";
//							echo "<td class='fila1' width=80>Normal</td>";
							echo "<td class='fila2' width=80>Normal</td>";
							echo "<td class='articuloCm1' width=80>Central mezclas</td>";
							echo "<td class='articuloNuevoPerfil' width=80>Nuevo</td>";
//							echo "<td class='suspendido' width=80>Suspendido</td>";
							echo "<td class='fondoAmarillo' width=80>Control</td>";
							echo "<td class='fondoVioleta' width=80>No POS</td>";
							echo "</tr>";
							echo "</table>";
							echo "</div>";

							//Detalle anterior
							if($elementosAnteriores > 0){
//								Muestra el detalle de medicamentos del kardex consultado
								echo '<span class="subtituloPagina2" align="center">';
								echo "Fechas de perfiles anteriores";
								echo "</span><br><br>";

								$cont1=0;
								$fechaTmp = "";
								$clase = "";
								$contArticulos = 1;

								foreach ($colDetalleAnteriorKardex as $articulo){

									if($fechaTmp != $articulo->fecha){
										//Seccion nueva del acordeon
										if($cont1 > 0){
											echo "</table>";
											echo "</p>";
											echo "</div>";
										}

										echo "<a href='#1' onclick=javascript:intercalarMedicamentoAnteriorPerfil('$articulo->fecha');>$articulo->fecha</a></br>";
										echo "<div id='med$articulo->fecha' style='display:none'>";

										echo "<p>";
										echo "<table align='center' border=0>";

										echo "<tr align='center' class='encabezadoTabla'>";

										//Encabezado detalle kardex
//										echo "<td>Fecha del Kardex</td>";
										echo "<td>Articulo</td>";
										echo "<td>Dosis</td>";
										echo "<td>Frecuencia</td>";
										echo "<td>Condicion</td>";
										echo "<td>Forma farmaceutica</td>";
										echo "<td>Fecha y hora inicio</td>";
										echo "<td>Via</td>";
										echo "<td>Conf.</td>";
										echo "<td>Dias tto</td>";
										echo "<td>Apr.<br>reg.</td>";
										echo "<td>Observaciones</td>";

										echo "</tr>";
									}

									$grupo = explode("-",$articulo->grupo);

									if($clase == "fila1"){
										$clase = "fila2";
									} else {
										$clase = "fila1";
									}
									
									if($articulo->origen == 'CM'){
										$clase = "articuloCm1";
									}

									if( $esDeControl ){
										$clase = "fondoAmarillo";
									}

									//Si es no POS tiene mayor precedencia
									if( !$articulo->esPos ){
										$clase = "fondoVioleta";
									}

									echo "<tr id='tr$contArticulos' class='$clase'>";

									//Fecha del kardex
									$fechaTmp = $articulo->fecha;

									//Fecha del kardex
//									echo "<td>$articulo->fecha</td>";

									//Articulo
									echo "<td>$articulo->codigoArticulo</td>";

									//Dosis y unidad de medida
									echo "<td>";
									echo $articulo->cantidadDosis." ";

									foreach ($colUnidades as $unidad){
										if($unidad->codigo == $articulo->unidadDosis){
											echo $unidad->descripcion;
										}
									}
									echo " (S)";
									echo "</td>";

									//Periodicidad
									echo "<td>";
									foreach ($colPeriodicidades as $periodicidad){
										if($periodicidad->codigo == $articulo->periodicidad){
											echo $periodicidad->descripcion;
										}
									}
									echo "</td>";

									//Condicion
									echo "<td>";
									foreach ($colCondicionesSuministro as $condicion){
										if($condicion->codigo == $articulo->condicionSuministro){
											echo $condicion->descripcion;
										}
									}
									echo "</td>";

									//Forma farmaceutica
									echo "<td>";

									foreach ($colFormasFarmaceuticas as $formasFarmaceutica){
										if($formasFarmaceutica->codigo == $articulo->formaFarmaceutica){
											echo $formasFarmaceutica->descripcion;
										}
									}

									echo "</td>";

									//Fecha y hora inicio
									echo "<td>";
									echo "$articulo->fechaInicioAdministracion a las:$articulo->horaInicioAdministracion";
									echo "</td>";

									//Via administracion
									echo "<td>";
									foreach ($colVias as $via){
										if($via->codigo == $articulo->via){
											echo $via->descripcion;
										}
									}
									echo "</td>";

									//Confirmacion
									echo "<td>";
									if($articulo->estaConfirmado == 'on'){
										echo "si";
									}else{
										echo "no";
									}
									echo "</td>";

									//Dias tratamiento
									echo "<td>";
									echo $articulo->diasTratamiento;
									echo "</td>";
									
									echo "<td align='center'>";
									echo $articulo->aprobado;
									echo "</td>";

									//Observaciones
									echo "<td>";
									// echo "<textarea id='wtxtobs$contArticulos' rows=2 cols=10 readonly>";
									echo $articulo->observaciones;
									// echo "</textarea>";
									echo "</td>";

									echo "</tr>";
									$cont1++;

								}
								echo "</table>";
								echo "<br>";
								echo "</div>";
							}
							echo "</div>";
							echo "<div id='fragment-3'>";

							// echo '<span class="subtituloPagina2" align="center">';
							// echo "Registros de auditoria";
							// echo "</span><br><br>";

							// echo "<table align='center' border=0>";

							// echo "<tr align='center' class='encabezadoTabla'>";
							// echo "<td>Usuario</td>";
							// echo "<td>Fecha y hora</td>";
							// echo "<td>Mensaje</td>";
							// echo "<td>Referencia</td>";
							// echo "</tr>";

							// $historialCambios = consultarHistorialCambiosKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);

							// $cont1 = 0;
							// foreach($historialCambios as $historia){
								// if($cont1 % 2 == 0){
									// echo "<tr class='fila1'>";
								// } else {
									// echo "<tr class='fila2'>";
								// }

								// echo "<td>";
								// echo $historia->usuario;
								// if( $historia->codigoRolHce  != '' ){
									// echo "<br>".$historia->codigoRolHce." - ".$historia->descripcionRolHCE;
								// }
								// echo "</td>";
								// echo "<td>$historia->fecha - $historia->hora</td>";
								// echo "<td>$historia->mensaje</td>";
								// echo "<td>$historia->descripcion</td>";

								// echo "</tr>";

								// $cont1++;
							// }

							// echo "</table>";
							// //Muestra los archivos cargados en el dia
							// $colArchivos = consultarArchivosDia($conex,$paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);

							// if(count($colArchivos) > 0){
								// echo '<span class="subtituloPagina2" align="center">';
								// echo "Ordenes medicas";
								// echo "</span><br><br>";

								// echo "<table align=center>";
								// echo "<tr class=encabezadoTabla>";

								// echo "<td colspan=2 align=center>Archivos cargados hoy</td>";

								// echo "</tr>";

								// echo "<tr class=encabezadoTabla align=center>";

								// echo "<td>Hora de carga</td>";
								// echo "<td>Ruta</td>";

								// echo "</tr>";

								// $clase="fila1";

								// foreach ($colArchivos as $cambio){
									// if($clase=="fila1"){
										// $clase = "fila2";
									// }else {
										// $clase = "fila1";
									// }

									// echo "<tr class=$clase>";

									// echo "<td>$cambio->hora</td>";
									// echo "<td>$cambio->descripcion</td>";

									// echo "</tr>";
								// }
								// echo "</table>";
							// }

							// echo "</div>";
							echo "<br/><center><input type='button' value='Regresar' onclick='javascript:inicioPerfil();' > | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();' ></center><br/>";
							
							pintarImpresora( $conex, $wemp_pmla, $wbasedato );
							
						} else {
							if( $kardexActual->grabado == "off" ){	//!$kadexGrabado
								mensajeEmergente("El kardex ya se ha creado pero se encuentra actualmente en uso. Por el usuario: ".$kardexActual->nombreUsuarioQueModifica );
							} else {
								mensajeEmergente("El kardex ya se ha creado pero aun no ha sido confirmado.");
							}
							funcionJavascript("inicioPerfil();");
						}
					}else{
						mensajeEmergente("No se ha generado kardex en esta fecha");
						funcionJavascript("inicioPerfil();");
					}
				} else {
					mensajeEmergente("No se pudo consultar el ingreso del paciente.  Verifique que la historia clinica fue digitada correctamente");
					funcionJavascript("inicioPerfil();");
				}//Fin existe ingreso de historia e informacion de paciente
			} else {
				mensajeEmergente("Faltan parametros para realizar la consulta");
				funcionJavascript("inicioPerfil();");
			}

			echo "</div>";
			break;
		default:		//Pantalla inicial de la consulta del perfil
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";
			
			//Servicio
			$centrosCostosHospitalarios = centrosCostosHospitalariosOcupados();
			echo "<tr>";
			echo "<td class='fila1'>Servicio</td>";
			echo "<td class='fila2'>"; 
			echo "<select id='wsservicio' NAME='wsservicio' onchange='javascript:consultarHabitaciones();' class='textoNormal'>";
			echo "<option value=''>Seleccione</option>";
			foreach ($centrosCostosHospitalarios as $centroCostosHospitalario){
				if(isset($wsservicio) && !empty($wsservicio) && $wsservicio == $centroCostosHospitalario->codigo){
					echo "<option value=".$centroCostosHospitalario->codigo." selected>".$centroCostosHospitalario->codigo."-".$centroCostosHospitalario->nombre."</option>";
				} else {
					echo "<option value=".$centroCostosHospitalario->codigo.">".$centroCostosHospitalario->codigo."-".$centroCostosHospitalario->nombre."</option>";
				}
			}
			echo "</select>";
			echo"</td>";
			echo "</tr>";
			
			//Habitaciones
			echo "<tr>";
			echo "<td colspan='2' id='cntHabitacion' align='center'>";
			
			if(isset($wsservicio) && !empty($wsservicio)){
				echo @consultarHabitacionPacienteServicioPerfil($wbasedato,$wsservicio);
			}
			
			echo "</td>";
			
			echo "</tr>";

			//Ingreso de fecha de consulta
			$cal="calendario()";
			echo '<span class="subtituloPagina2">';
			echo 'Ingreso de parámetros de consulta';
			echo "</span>";
			echo "<br>";
			echo "<br>";

			//Por Historia clinica
			echo "<tr><td class='fila1' width=170>Historia clínica</td>";
			echo "<td class='fila2' align='center' width=170>";
			echo "<INPUT TYPE='text' id='wthistoria' NAME='whistoria' SIZE=10 onkeypress='return teclaEnterEntero(event,"."\"consultarPerfil()\");' class='textoNormal'>";
			echo "</td>";
			echo "</tr>";

			//Ingreso (para pacientes inactivos)
			echo "<tr><td class='fila1' width=170>Ingreso</td>";
			echo "<td class='fila2' align='center' width=170>";
			echo "<INPUT TYPE='text' id='wtingreso' NAME='wtingreso' SIZE=5 onkeypress='return teclaEnterEntero(event,"."\"consultarPerfil()\");' class='textoNormal'>";
			echo "</td>";
			echo "</tr>";

			//Por fecha generacion kardex
			echo "<tr>";
			echo "<td class='fila1'>Fecha</td>";
			echo "<td class='fila2' align='center'>";
			echo "<INPUT TYPE='text' NAME='wfecha' id='wfecha' value=".date("Y-m-d")." SIZE=11 readonly class='textoNormal'>";
			echo "&nbsp;<button id='btnFecha' onclick=".$cal.">...</button>";
			echo "</td></tr>";

			echo "<tr><td align=center colspan=4><br><input type=button value='Consultar' onclick='javascript:consultarPerfil();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();' ></td></tr>";
			echo "</table>";

			//Se configura el calendario cuando ingresa la primera vez
			funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecha',button:'btnFecha',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
			break;
	}
}
$url = "";
if( !empty( $paciente ) && !empty($paciente->historiaClinica) && !empty($paciente->ingresoHistoriaClinica) )
	$url .= "../../hce/procesos/ordenes.inc.php?wemp_pmla=".$wemp_pmla."&his=".$paciente->historiaClinica."&ing=".$paciente->ingresoHistoriaClinica."&consultaAjaxKardex=82";
?>
<script type="text/javascript">
if(document.getElementById("fixeddiv")) { fixedMenuId = "fixeddiv"; var fixedMenu = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId) : document.all ? document.all[fixedMenuId] : document.layers[fixedMenuId]}; fixedMenu.computeShifts = function() { fixedMenu.shiftX = fixedMenu.hasInner ? pageXOffset : fixedMenu.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu.shiftX += fixedMenu.targetLeft > 0 ? fixedMenu.targetLeft : (fixedMenu.hasElement ? document.documentElement.clientWidth : fixedMenu.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu.targetRight - fixedMenu.menu.offsetWidth; fixedMenu.shiftY = fixedMenu.hasInner ? pageYOffset : fixedMenu.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu.shiftY += fixedMenu.targetTop > 0 ? fixedMenu.targetTop : (fixedMenu.hasElement ? document.documentElement.clientHeight : fixedMenu.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu.targetBottom - fixedMenu.menu.offsetHeight }; fixedMenu.moveMenu = function() { fixedMenu.computeShifts(); if(fixedMenu.currentX != fixedMenu.shiftX || fixedMenu.currentY != fixedMenu.shiftY) { fixedMenu.currentX = fixedMenu.shiftX; fixedMenu.currentY = fixedMenu.shiftY; if(document.layers) { fixedMenu.menu.left = fixedMenu.currentX; fixedMenu.menu.top = fixedMenu.currentY }else { fixedMenu.menu.style.left = fixedMenu.currentX + "px"; fixedMenu.menu.style.top = fixedMenu.currentY + "px" } }fixedMenu.menu.style.right = ""; fixedMenu.menu.style.bottom = "" }; fixedMenu.floatMenu = function() { fixedMenu.moveMenu(); setTimeout("fixedMenu.floatMenu()", 20) }; fixedMenu.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu.init = function() { if(fixedMenu.supportsFixed())fixedMenu.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu.menu : fixedMenu.menu.style; fixedMenu.targetLeft = parseInt(a.left); fixedMenu.targetTop = parseInt(a.top); fixedMenu.targetRight = parseInt(a.right); fixedMenu.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu.addEvent(window, "onscroll", fixedMenu.moveMenu); fixedMenu.floatMenu() } }; fixedMenu.addEvent(window, "onload", fixedMenu.init); fixedMenu.hide = function() { fixedMenu.menu.style.display = "none"; return false }; fixedMenu.show = function() { fixedMenu.menu.style.display = "block"; return false } }

try{
	// /**************************************************************************************************
	// * Enero 25 de 2017
	// * Se busca los articulos que son LQ o IC que esten como enviar y se dejan como no enviar
	// * Esto se hace por que son articulos que fueron creados en urgencias y ahora están en piso
	// * y dichos articulos son del stock.
	// **************************************************************************************************/
	// Si el paciente no es de urgencias
	if( "<?= $url ?>" != "" && !<?=$paciente->enUrgencias ? "true" : "false" ?> ){
		$.post("<?= $url ?>",function(data){})
	}
}
catch(e){
	console.log("No se llama ")
}
</script>

</body>
</html>
