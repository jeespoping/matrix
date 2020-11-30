<html>
<head>
<title>MATRIX - [KARDEX DE ENFERMERIA]</title>

<!-- JQUERY para los tabs -->
<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.simpletree.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<!-- Fin JQUERY para los tabs -->

<!-- Include de codigo javascript propio de mensajeria Kardex -->
<script type="text/javascript" src="../../../include/movhos/mensajeriaKardex.js"></script>

<script type="text/javascript" src="../../../include/movhos/alertas.js?v=<?=md5_file('../../../include/movhos/alertas.js');?>"></script>

<script type="text/javascript">
	$(document).ready(function(){
		inicializarJquery();
	});
</script>

<!-- Include de codigo javascript propio del kardex -->
<script type="text/javascript" src="kardex.js?v=<?=md5_file('kardex.js');?>"></script>

<style>
.fondoAlertaConfirmar
{
     background-color: #8181F7;	
     color: #000000;
     font-size: 10pt;
}

.fondoAlertaEliminar
{
     background-color: #F5D0A9;
     color: #000000;
     font-size: 10pt;
}

.fondoContingencia
{
     background-color: #D7DF01;
     color: #000000;
     font-size: 10pt;
}
</style>

</head>

<body>

<?php
include_once("conex.php");
/*BS'D
 * CONSULTA Y GENERACION DE KARDEX
 * Autor: Mauricio Sánchez Castaño.
 * /************************************************
	ADVERTENCIA:
		NO USE funciones que activen el evento onBeforeUnload en lo operativo, ya que esto dispara la grabacion del kardex.  Ej:
		->javascript:void(0) en los links use mejor href='#null'
	*************************************************
	*
	* Modificaciones:
	* 	Julio 9 de 2018		(Edwin MG)		Se corrige el posicionamiento del div de alertas
	* 	Julio 3 de 2018		(Edwin MG)		Se comenta la información de la pestaña auditoría ya que esta se consulta por ajax cuando se de clic sobre la pestaña Auditoría
	* 	Diciembre 18 de 2016				Se agrega el llamado a la función consultarUltimoDiagnosticoHCE() de comun.php que devuelve 
	*										la lista de los diagnósticos actuales del paciente
	* 	Diciembre 05 de 2016				Se agrega programa de alergias y alertas, se comenta en la pestaña de Informacion General los antecedentes alergicos y el Retiro de alergias 
	*										anteriores y se consulta peso y talla en formularios hce si no existe en movhos_000053
	*	Junio 22 de 2015 (Jonatan)			Se implementa la funcion para que al cerrar con la x roja del navegador, el kardex no quede bloqueado y que ademas ponga los registros en las tablas 
											definitivas.
	* 	Marzo 28 de 2015	(Edwin MG)		Se reactiva el mensaje emergente que valida que otro usuario no pueda tener el kardex abierto ya que estaba inactivo por petición de Juan.
	* 	Mayo 15 de 2015	(Edwin MG)			No se permite abrir kardex cuando una orden está abierta para el personal de lactario
	* 	Marzo 12 de 2015	(Edwin MG)		No se permite grabar medicamentos nuevo para la ronda actual.
	* 	Febrero 09 de 2015	(Edwin MG)		Se mejora validación para usar el kardex de enfermería.
	*	Enero 14 de 2015 (Jonatan) -> Se agregan los examenes realizados que fueron registrados desde ordenes en urgencias.
	* 	Diciembre 05 de 2014	 (Edwin MG)		Se agrega dosis máxima según frecuencia, esto lo indica el campo Perdma de la tabla movhos_000043
	* 	Octubre 02 de 2014 (Jonatan) -> Se da formato al texto de la bitacora de gestion, para que sa leido mejor.
	*   Septiembre 15 de 2014 (Jonatan)  -> Se agrega a la informacion del paciente los medicamentos de uso habitual, traidos desde hce.
								 Si el paciente tiene ordenes pendientes desde urgencias seran mostradas en la pestaña de examenes, la enfermera podrá
								 marcar estas ordenes como ralizadas.
	*   Agosto 27 de 2014 (Jonatan Lopez) -> Se agrega control en las observaciones de los examanes para que no se repitan si el paciente tiene el mismo examen, ademas de 
											 ocultan los arcticulos y examanes anteriores y se muestran cuando el usuario selecciona el titulo correspondiente.
	*   Agosto 8 de 2014 (Jonatan Lopez) -> Se agrega en el llamado al archivo kardex.js la siguente linea:
											<script type="text/javascript" src="kardex.js?v=<?=md5_file('kardex.js');?>"></script>
											src="kardex.js?v=<?=md5_file('kardex.js');?> = quiere decir que cuando haya cambios en el script se generara
											un numero md5 nuevo, esto permite que el script siempre este actualizado y no sea necesario actualizar la cache
											para cargar el javascrip nuevo.
	*   Julio 31 de 2014 (Jonatan Lopez) ->	Se quitan las terapias, interconsultas y cirugias, seguiran siendo manejadas como examenes.
											Se organiza el orden de los examenes desde el ultimo hasta el mas antiguo.
											Se puede repertir el mismo examen para el mismo dia.
											Se marcan los examenes cancelados y finalizados del dia actual para que se muestren con fondo rosado en el kardex.
											No se muestran los examenes que han sido cancelados o finalizados del dia anterior, solo los del dia actual.
	*	Abril 2 de 2014			(Edwin MG) -> Si el kardex se abre desde HCE, al cerrarse regresa al menu de HCE.
	*	Diciembre 26 de 2013	(Edwin MG) -> Se realizan cambios para que pase adecuadamente los registros del temporal detalle del kardex(movhos_000060) al detalle del kardex(movhos_000054)
	*	Octubre 11 de 2013	(Mario Cadavid)-> Se cambió el textarea wtxtobs$cont1 por un div. Este campo es el que contiene las observaciones del
	*										  histórico de medicamentos. Esto se hizo porque las observaciones ya contienen etiquetas HTML y en el
	*										  textarea se veain estas etiquetas
	*	Agosto 28 de 2013	(Mario Cadavid)-> Se creo el campo oculto usuariodes donde se guardael nombre completo del usuario actualmente logueado
	*	Mayo 16 de 2013			(Edwub MG) -> Agrego id en campos correspondientes al arbol de pendientes para que el programa funcione en Mozzila firefox
	*	Octubre 18 de 2012		(Edwin MG) -> Si el programa es llamao desde un programa externo y es editable, al grabar se cierra la ventana emergente en lugar de ir a la selección de ccos.
	*										  Anteriormente se cerraba pero solo si el kardex era de consulta.
	*	Octubre 01 de 2012		(Edwin MG) -> Se agrega un tooltip que dice que los días de tratamiento serán transformado en dosis máxima para el campo días de tratamiento
	*	Junio 19 de 2012		(Edwin MG) -> Se crea opcion para cargar articulos del día anterior siempre y cuando esten activos en el sistema(no esten como suspendidos)
	*										  La enfermeras pueden modificar los articulos de lactario.
	*										  En esta ultima entrega incluye los cambios realizados durante el 13 de junio.
	*	Junio 13 de 2012		(Edwin MG) -> Se modifcan kardex.inc.php, kardex.js para permitir colocar por defecto dosis maxima si una condicion lo requiere, quita tooltip de articulo y permitir
	*										  grabar dextrometer sin restriccion
	*	Marzo 26 de 2012		(Edwin MG) -> Se impide abrir el kardex si se esta dispensando el kardex
	*	Enero 26 de 2012		(Edwin MG) -> Se despliega para los examenes la bitacora de procedimientos
	*										  Si el kardex es editable, los procedimientos se marcan como leidos
	*										  Cuando se elige el servicio, se muestra un campo adicional que muestra cuantos procedimientos hay sin leer
	*	Diciembre 26 de 2011	(Edwin MG) -> Solo se carga el dextrometer del día anterior si en el día actual no se ha creado, antes siempre se estaba cargando el dextrometer del
	*										  día anterior aunque para el día actual se eliminara
	*	Diciembre 5 de 2011		(Edwin MG) -> Se agrega poder consultar kardex por ingreso
	*	Noviembre 21 de 2011	(Edwin MG) -> Para mensajeria se quita la prohibicion de enviar caracteres especiales.
	*	Noviembre 4 de 2011		(Edwin MG) -> Si el kardex es de consulta, se puede ver todo.
	* 	Noviembre 1 de 2011 	(Edwin MG) -> Se corrige calculo de regleta cuando el paciente fue trasladado de cirugia o urgencia del dia anterior a piso antes de las 02:00:00 en kardex.inc.php
	*								  	   -> Se registra fecha de ultima ronda grabada del medicamento en kardex.inc.php
	*   Octubre 27 de 2011		(Edwin MG) -> Se evita reemplazo de medicamento desde el perfil si el medicamento nuevo no tiene la misma via que el original
	*									   -> Se agrega borrado de tabla temporal, se deja solo los dos ultimos dias
	*	Octubre 25 de 2011		(Edwin MG) -> Se agrega información de afinidad en la tabla de información demográfica del paciente
	*	Octubre 14 de 2011		(Edwin MG) -> Se modifica include del kardex
	*	Octubre 11 de 2011		(Edwin MG) -> Se agrega mensajeria entre el perfil y el kardex farmacoterapeutico
	*	Septiembre 14 de 2011	(Edwin MG) -> Si la paciente se encuentra en un centro de costos que maneje ciclos de produccion (movhos 11 campo ccocpx), el kardex cuando se entra la primera vez
	*										  en el día, crea el kardex con las mismas condiciones que el día anterior para confirmacion de kardex, confirmacion de preparacion de medicamentos
	*										  y aprobacion del kardex.
	*	Agosto 26 de 2011		(Edwin MG) -> Se agrega mensaje "DEBE CONFIRMAR KARDEX" siempre que se abre por primera vez
	*   Agosto 3 de 2011		(Edwin MG) -> Se agrega mensaje que indica que un medicamento se encuentra en proceso de produccion
	* 	Julio 7 de 2011			(Edwin MG) -> Se verifica que un usuario que va a usar el kardex en modo editable, si tenga la opcion correspondiente para usarla, de no ser así no
	* 										  se le permite usar el programa.
	* 	Junio 30 de 2011		(Edwin MG) -> Se realizan cambion en kardex.inc.php
	*   Mayo 23 de 2011			(Edwin MG) -> Se modifica Kardex.inc.php. Se hace auditoría en las transacciones del kardex entre la tabla temporal (000060) y definitiva (000054) y viceversa
	*   									  Las modificaciones estan con fecha del 20 de Mayo de 2011
	* 	Abril 29 de 2011		(Edwin MG) -> Modificaciones varias durante los dias 25,26,27.
	* 										  Correccion en duplicacion de medicamentos,
	* 										  Si un articulo es del stock, automaticament se marca la casilla no enviar, stock de articulos.
	* 										  Si un articulo es a Necesidad, el saldo del dispensación del día anterior es 0, igual el saldo del articulo si no se dispensó completamente el día anterior
	* 										  Correccion: Si un articulo es no duplicable y ya esta en el kardex, no se puede duplicar.
	* 										  Si el paciente viene de urgencias, el saldo del día anterior es 0
	* 	Abril 18 de 2011		(Edwin MG) -> Se hace cambios en el include del kardex. Corrección a las dosis maximas a aplicar.
	* 	Abril 13 de 2011		(Edwin MG) -> Se hace cambios en el include del kardex. Corrección al modificar un medicamento por dosis, frecuencia y fecha y hora de inicio.
	* 	Abril 5 de 2011			(Edwin MG) -> Se hacen cambios en el include del kardex.
	* 	Marzo 31 de 2011		(Edwin MG) -> Se realiza cambios en la funcion esPrimeraVez. ver Kardex.inc.php.
	*   Marzo 16 de 2011		(Edwin MG) -> Se valida hora para la fecha seleccionada (Kardex.js)
	* 	Marzo 15 de 2011		(Edwin MG) -> Se corrige reemplazo de articulo por genericos desde el perfil.
	*   Marzo 14 de 2011		(Edwin MG) -> Se valida duplicación de medicamento con respecto a la tabla 54
	*	Marzo 11 de 2011		(Edwin MG) -> Se impide cerrar el kardex con la x de la ventana
	*	Marzo 8 de 2011			(Edwin MG) -> Se agregar control para el Kardex: No se puede abrir el kardex por dos personas simultaneamente.
	*										  Si un articulo se suspende no parpadea si el articulo se puede confirmar (kardex.js)
	*	Marzo 7 de 2011			(Edwin MG) -> Si un medicamento de central de mezclas no existe en la definición de fracciones y es del mismo tipo que un generico, la fraccion
	*										  es creada en la tabla de definición de fracciones(movhos_000059) (karddx.inc.php).
	*										  Se corrige la seleccion de vias al agregar un medicamento (kardex.js)
	*										  Si un medicamento se puede confirmar y no esta confirmado, la fila correspondiente parpadea (kardex.js)
	*	Marzo 3 de 2011			(Edwin MG) -> Se realiza cambios en el include del kardex.
	*										  Se agrega mensaje del cco al que se ecuentra asociado el usuario cuando se abre el kardex cuando el paciente esta en proceso de traslado
	*	Marzo 2 de 2011			(Edwin MG) -> Se realiza cambio en los query de consulta de medicamentos, tanto por nombre como por codigo
	*	Marzo 1 de 2011			(Edwin MG) -> Se realizan cambio en el include del kardex
	*	Febrero 22 de 2011		(Edwin MG) -> Se corrige funcion obtenerVectorAplicacionMedicamentos
	*	Febrero 17 de 2011		(Edwin MG) -> Si esta en proceso de tralasado y fue entregao desde cirugia, solo pueden hacer el kardex del paciente
	*										  personal de cirguia o urgencias
	*	Febrero 15 de 2011		(Edwin MG) -> Si el paciente se encuentra en traslado no se puede realizar el kardex
	*	Enero 24 de 2011		(Edwin MG) -> Si un articulo es modificado, el calculo de cantidad a dispensar se hace a partir de la fecha y hora de inicio del medicamento.
	*										  Para esto se modifica kardex.inc.php
	*   Enero 14 de 2011		(Edwin MG) -> Se corrige cantidad a dispensar para articulos que comienzan al día siguiente y pacientes que son trasladados
	*   							          desde urgencias a piso el mismo día y no se le habían generado kardex en ese momento.  Para ello se modifica
	*   									  Kardex.inc.php
	*	Enero 12 de 2011		(Edwin MG) -> Se cambia reemplazo desde el perfil. Para ello se modifica los archivos Kardex.inc.php y kardex.js
	*	Enero 05 de 2011		(Edwin MG) -> Se corrige dosis máxima y reemplazo desde el perfil para artiulos genéricos, para ello se modifica kadex.inc.php y kardex.js,
	*	Diciembre 15 de 2010:	(Edwin MG) -> Se modifica para que un usuario de CM o SF pueda consulta el kardex
	*	Diciembre 9 de 2010:	(Edwin MG) -> Cambio en el include del Kardex. Si un paciente es trasladado de urgencias o cirugia y no se ha dispensado ningun articulo,
	*								del día anterior y esta en piso, las dosis a dispendar son calculados como si fuera primera vez
	*	Diciembre 7 de 2010:	(Edwin MG) -> Se agrega funcion para impedir que el personal de SF puedan abrir el kardex.
	*	Diciembre 6 de 2010:	(Edwin MG) -> Cambio en el include del Kardex. Si un paciente es trasladado de urgencias o cirugia del día anterior y esta en piso
	*							   las dosi a dispendar son caculados como si fuera primera vez
	*	2010-05-18:  (Msanchez) -> Cambio de control de CTC.
	*   2010-06-04:  (Msanchez) -> El kardex debe grabar discriminado por centro de costos
    *   2010-08-10:  (Msanchez) -> Grabacion sin chulo verde, saldo de dispensacion
	*	2010-09-24:  (Msanchez) -> Ultimo movimiento hospitalario resaltado
	*	2010-10-27:  (Msanchez) -> Suma de dosis desde urgencias y cirugia segun el ultimo traslado
 */
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

/****************************************************************************************
 * Noviembre 4 de 2011
 ****************************************************************************************/
if(isset($editable) && $editable == "off"){
	@$usuario = consultarUsuarioKardexConsulta($wuser);
}
/****************************************************************************************/

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

if(isset($editable) && $editable == "off"){
	//Encabezado
	encabezado("Kardex de Enfermer&iacute;a de consulta",$wactualiz,"clinica");
} else {
	//Encabezado
	encabezado("Kardex de Enfermer&iacute;a",$wactualiz,"clinica");
}

if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} else {

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	//Ruta de carga de imagenes de formulas
	$ruta="C:/wamp/www/MATRIX/planos/";

	//Historial de modificaciones
	$mostrarAuditoria = true;

	//Fecha grabacion
	$fechaGrabacion = date("Y-m-d");

	//Base de datos, se generaliza de generar kardex
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$wccograbacion = "";

	if($usuario->pestanasKardex == ""){
		mensajeEmergente("No tiene permisos para usar el kardex de enfermeria.  Comuniquese con el Area de Soporte.");
		funcionJavascript("cerrarVentana();");
		die("");
	}


	//Formulario
	echo "<form name='forma' action='generarKardex.php' method='post'>";

	echo "<input type='HIDDEN' NAME= 'wemp_pmla' id= 'wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='HIDDEN' NAME= 'wbasedato' id= 'wbasedato' value='".$wbasedato."'/>";
	echo "<input type='HIDDEN' NAME= 'usuario' id='usuario' value='".$wuser."'/>";
	echo "<input type='HIDDEN' NAME= 'centroCostosUsuario' id= 'centroCostosUsuario' value='".$usuario->centroCostos."'/>";
	
	$centroCostosGrabacionTemp = $usuario->centroCostosGrabacion;
	if(!$usuario->esUsuarioLactario){
		if($usuario->esUsuarioCM || $usuario->esUsuarioSF){
			$centroCostosGrabacionTemp = "*";
		}
	}
	echo "<input type='HIDDEN' NAME= 'centroCostosGrabacion' id= 'centroCostosGrabacion' value='".$centroCostosGrabacionTemp."'/>";
	echo "<input type='hidden' NAME= 'wfechagrabacion' id= 'wfechagrabacion' value='$fechaGrabacion'>";
	echo "<input type='hidden' NAME= 'whgrupos' id= 'whgrupos' value='$usuario->gruposMedicamentos'>";

	// Agosto 28 de 2013
	$datosUsuario = consultarUsuario($conex,$wuser);
	echo "<input type='HIDDEN' NAME= 'usuariodes' id='usuariodes' value='".$datosUsuario->descripcion."'/>";

	if($usuario->esUsuarioLactario){
		echo "<input type='hidden' NAME= 'whusuariolactario' id= 'whusuariolactario' value='on'>";
	} else {
		echo "<input type='hidden' NAME= 'whusuariolactario' id= 'whusuariolactario' value='off'>";
	}

	if(!isset($editable)){
		$editable="on";
	}
	echo "<input type='HIDDEN' NAME='editable' id='editable' value='".$editable."'/>";

	//Indicador de si es fecha actual
	if(isset($wfecha)){
		$esFechaActual = ($wfecha == $fechaGrabacion);
	}

	//Calcula la fecha del dia anterior.
	$fechaActualMilis 	= time();
	$ayerMilis			= time() - (24 * 60 * 60);
	$fechaAyer			= date("Y-m-d", $ayerMilis);

	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...";
	echo "</div>";

	/*****************************************************************************************************************************
	 * MAESTROS OCULTOS.  SIRVEN PARA ASIGNAR LAS FILAS DINAMICAS... SE CARGAN en la accion b
	 *****************************************************************************************************************************/
	if(isset($waccion)){
		if($waccion == 'b'){

			/******************************************************************************************
			 * Julio 7 de 2011
			 ******************************************************************************************/
			if( !(isset($editable) && $editable == "off") ){
				if( !verficacionKardexEditable() ){
					echo "<b>No tiene permiso de usar kardex de enfermería editable</b>";
					exit;
				}
			}
			/******************************************************************************************/

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
			foreach ($colPeriodicidades as $periodicidad){
				echo "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
				
				if( !empty( $periodicidad->dosisMax ) ){
					$jsobjPerDefecto .= ",$periodicidad->codigo : { dma: $periodicidad->dosisMax }";
				}
			}
			echo "</select>";
			
			//Creo el obejto javascript para el manejo de dosis maxima por defecto
			if( !empty($jsobjPerDefecto) ){
				echo "<script>var dmaPorFrecuencia = { ".substr( $jsobjPerDefecto, 1 )." }</script>";
			}

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
			$colEstadosExamen = consultarEstadosExamenesLaboratorio();
			foreach ($colEstadosExamen as $estadoExamen){
				echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
			}
			echo "</select>";

			//CONDICIONES DE SUMINISTRO DE MEDICAMENTOS SIN IMPORTAR EL PROTOCOLO
			echo "<select id='wmcondicionessuministro'>";
			$colCondicionesSuministro = consultarCondicionesSuministroMedicamentos("");
			echo "<option value=''>Seleccione</option>";
			foreach ($colCondicionesSuministro as $condicion){
				echo "<option value='$condicion->codigo'>$condicion->descripcion</option>";

				if( !empty( $condicion->valDefecto ) ){
					$jsobjConDefecto .= ",$condicion->codigo : { dma: $condicion->valDefecto }";
				}
			}
			echo "</select>";


			//Creo el obejto javascript para el manejo de dosis maxima por defecto
			if( !empty($jsobjConDefecto) ){
				echo "<script>var dmaPorCondicionesSuministro = { ".substr( $jsobjConDefecto, 1 )." }</script>";
			}

			//CONDICIONES DE SUMINISTRO DE MEDICAMENTOS SIN IMPORTAR EL PROTOCOLO
			echo "<select id='wmcondicionessuministro'>";
			$colCondicionesSuministroInsulinas = consultarCondicionesSuministroMedicamentos("I");
			echo "<option value=''>Seleccione</option>";
			foreach ($colCondicionesSuministro as $condicion){
				echo "<option value='$condicion->codigo'>$condicion->descripcion</option>";
			}
			echo "</select>";

			echo "</div>";
			/*****************************************************************************************************************************
			 * FIN MAESTROS OCULTOS
			 *****************************************************************************************************************************/
		}
	}

	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}

	if( !empty($waccion) ){
		$paciente = @consultarInfoPacienteKardex($whistoria,$wingreso);
		//Diciembre 7 de 2010
		if( servicioConKardex( $conex, $wbasedato, $paciente->servicioActual, $usuario->centroCostos ) ){
			//Diciembre 15 de 2010
			//si es modo consulta no debe sacar este mensaje
			if( isset($editable) && $editable != "off" || !isset($editable) ){
				mensajeEmergente("No puede ver este paciente");
				funcionJavascript("inicio(\"$paciente->servicioActual\");");
			}
		}
	}

//	var_dump($usuario);

	//FC para hacer las acciones
	switch ($waccion){
		case 'a':  //Si el kardex para la historia no existe, se crea uno, de lo contrario se consulta el anterior y se trae su información
			/* Parametros de ingreso
			 * -whistoria
			 * -wfecha
			 */
			if(isset($whistoria) && isset($wfecha)){
				$paciente = @consultarInfoPacienteKardex($whistoria,$wingreso);

				/************************************************************************************************************************
				 * Febrero 15 de 2011
				 *
				 * Modificacion: Febrero 17 de 2011
				 ************************************************************************************************************************/
//				if( $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && $usuario->centroCostos != $paciente->servicioAnterior ){
				if( $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && !($usuario->esCcoUrgencias || $usuario->esCcoCirugia) && esCcoIngreso( $conex, $wbasedato, $paciente->servicioAnterior ) ){
				//if( $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && !existeEncabezadoKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha) ){
					if( isset($editable) && $editable != "off" || !isset($editable) ){
						mensajeEmergente("El paciente esta en proceso de traslado.\\nDebe recibir el paciente para hacer el kardex.\\n\\nUSTED SE ENCUENTRA ASOCIADO A\\n$usuario->nombreCentroCostos($usuario->centroCostos)"); //Marzo 3 de 2011
						funcionJavascript("inicio(\"$paciente->servicioActual\");");
					}
				}
				elseif( false && $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && ($usuario->esCcoUrgencias || $usuario->esCcoCirugia) ){
					$paciente->servicioActual = $paciente->servicioAnterior;
				}
				/************************************************************************************************************************/

				//$usuario->centroCostos
				if(!$usuario->esUsuarioLactario){
					if($usuario->esUsuarioCM || $usuario->esUsuarioSF){
						$usuario->centroCostosGrabacion = "*";
					}
				}
//				echo "Centro costos grabacion modificado $usuario->centroCostosGrabacion";

				if(!empty($paciente->ingresoHistoriaClinica)){
					$kardexActual = consultarKardexPorFechaPaciente($wfecha,$paciente);

					//Si existe esta variable creo un campo hidden para poderla usar en
					//la funcion javascript confirmarGeneracion
					if( !empty( $_GET['et'] ) ){
						echo "<input type='hidden' name='hiet' id='hiet' value='$et'>";
					}

					echo "<input type='hidden' name='whistoria' id='whistoria' value='$paciente->historiaClinica'>";
					echo "<input type='hidden' name='wingreso' id='wingreso' value='$paciente->ingresoHistoriaClinica'>";
					echo "<input type='hidden' name='wfecha' id='wfecha' value='$wfecha'>";

					if(isset($whgrabado)){
						echo "<input type='hidden' name='whgrabado' id='whgrabado' value='$whgrabado'>";
					}
					echo "<input type='HIDDEN' name='elementosKardex' id='elementosKardex' value='0'/>";
					if($editable == 'on' ){
						/*********************************************************************************************************************
						 * SI NO ENCUENTRA ENCABEZADO EN LA FECHA ANTERIOR O NO ESTA MARCADO COMO GRABADO EL KARDEX DEL DIA ANTERIOR SE GRABA
						 *********************************************************************************************************************/
						$cargarDefinitivo = false;
						if(!existeEncabezadoKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer)){
							if(crearEncabezadoKardexAnterior($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer)){
								$cargarDefinitivo = true;
							}
						} else {
							if(!grabadoEncabezadoKardexFecha($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer)){
								if(marcarGrabacionKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $fechaAyer, "on")){
									$cargarDefinitivo = true;
								}
							}	//Si hay articulos en la temporal carga lo anterior al definitivo
							elseif( hayArticulosEnTemporal( $conex, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$fechaAyer) ){
								$cargarDefinitivo = true;
							}
						}

						$esKardexNuevo = true;
						if(!empty($kardexActual->historiaClinica)){
							$esKardexNuevo = false;
						}
						
						/****************************************************************************************************************
						 * Si el kardex lo esta mirando otra persona, el kardex no se puede abrir. Si la persona quien lo estaba creando
						 * es la misma que el usuario el kardex se puede abrir
						 *
						 * Marzo 8 de 2011
						 ****************************************************************************************************************/
						if( !$esKardexNuevo ){
							if( $usuario->esUsuarioLactario && ir_a_ordenes($wemp_pmla, $paciente->servicioActual) == 'on' ){
								if( $kardexActual->grabado == "off" || hayArticulosEnTemporal( $conex, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha) ){
									if( $kardexActual->usuarioQueModifica != $usuario->codigo ){
										mensajeEmergente("El kardex ya se ha creado pero se encuentra actualmente en uso. Por el usuario: ".$kardexActual->nombreUsuarioQueModifica);
										funcionJavascript("inicio(\"$paciente->servicioActual\");");
									}
								}
							}
						}
						/****************************************************************************************************************/
						

						/****************************************************************************************************************
						 * Si el kardex lo esta mirando otra persona, el kardex no se puede abrir. Si la persona quien lo estaba creando
						 * es la misma que el usuario el kardex se puede abrir
						 *
						 * Marzo 8 de 2011
						 ****************************************************************************************************************/
						if( !$esKardexNuevo ){
							if( $kardexActual->grabado == "off" || hayArticulosEnTemporal( $conex, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha) ){
								if( $kardexActual->usuarioQueModifica != $usuario->codigo ){
									mensajeEmergente("El kardex ya se ha creado pero se encuentra actualmente en uso. Por el usuario: ".$kardexActual->nombreUsuarioQueModifica);
									funcionJavascript("inicio(\"$paciente->servicioActual\");");
								}
							}
						}
						/****************************************************************************************************************/

						cargarEsquemaDextrometer($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer,$wfecha);
						if($cargarDefinitivo){
							cargarArticulosADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer,$esKardexNuevo, $wccograbacion);
							cargarExamenesADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
							cargarInfusionesADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
							cargarMedicoADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
							cargarDietasADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);

							/********************************************************************************************
							 * Marzo 17 de 2011
							 ********************************************************************************************/
							//Registro de auditoria
							$auditoria = new AuditoriaDTO();

							$auditoria->historia = $paciente->historiaClinica;
							$auditoria->ingreso = $paciente->ingresoHistoriaClinica;
							$auditoria->descripcion = "Kardex recuperado automáticamente";
							$auditoria->fechaKardex = date("Y-m-d");
							$auditoria->mensaje = obtenerMensaje( "MSJ_KARDEX_RECUPERADO" );
							$auditoria->seguridad = $usuario->codigo;

							registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
							/********************************************************************************************/

							//Si hay una carga de detalle, se va directamente a la generacion
							funcionJavascript("confirmarGeneracion();");
						}

						if(!$esKardexNuevo){
							funcionJavascript("confirmarGeneracion();");
						} else { 
							//No permite generar el kardex NUEVOS para dias anteriores
							if(!$esFechaActual){
								mensajeEmergente("No existe kardex para la fecha seleccionada.");
								funcionJavascript("inicio(\"$paciente->servicioActual\");");
							}

							echo '<span class="subtituloPagina2" align="center">';
							echo 'Información demogr&aacute;fica';
							echo "</span><br><br>";

							echo "<table align='center'>";

							echo "<tr>";

							echo "<td class='fila1'>Historia cl&iacute;nica</td>";
							echo "<td class='fila2'>";
							echo $paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica;
							echo "</td>";

							echo "<td class='fila1' align=center rowspan=2><b><font size=3>Paciente</font></b></td>";
							echo "<td class='fila2' align=center colspan=3 rowspan=2><b><font size=3>";
							echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
							echo "</font></b></td>";

							echo "</tr>";
							echo "<tr>";

							echo "<td class='fila1'>Fecha y hora de ingreso</td>";
							echo "<td class='fila2'>";
							echo "$paciente->fechaIngreso - $paciente->horaIngreso";
							echo "</td>";

							echo "</tr>";
							echo "<tr>";

							//Servicio actual y habitacion
							echo "<td class='fila1'>Habitaci&oacute;n</td>";
							echo "<td class='fila2'>";
							echo "$paciente->nombreServicioActual - $paciente->habitacionActual";
							echo "</td>";

							//Entidad responsable
							echo "<td class='fila1'>Entidad responsable</td>";
							echo "<td class='fila2'>";
							echo "$paciente->numeroIdentificacionResponsable - $paciente->nombreResponsable";
							echo "</td>";

							//Valor de la edad
							$vecAnioNacimiento = explode("-",$paciente->fechaNacimiento);
							echo "<td class='fila1'>Edad</td>";
							echo "<td class='fila2'>";
							echo $paciente->edadPaciente;
							echo "</td>";

							echo "</tr>";
							echo "<tr>";

							//Calculo de dias de hospitalizcion desde ingreso
							$diaActual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
							$fecha = explode("-",$paciente->fechaIngreso);
							$diaIngreso = mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);
							$diasHospitalizacion = ROUND(($diaActual - $diaIngreso)/(60*60*24));

							echo "<td class='fila1'>D&iacute;as de hospitalizaci&oacute;n</td>";
							echo "<td class='fila2'>";
							echo $diasHospitalizacion;
							echo "</td>";

							//Usuario
							echo "<td class='fila1'>Usuario y servicio que genera</td>";
							echo "<td class='fila2'>";
							echo "$usuario->codigo - $usuario->descripcion.  $usuario->nombreCentroCostos";
							echo "</td>";

							echo "<td class='fila1'>Fecha y hora de generaci&oacute;n</td>";
							echo "<td class='fila2'>";
							echo $fechaGrabacion." - ".date("H:i:s");
							echo "</td>";

							echo "</tr>";
							echo "<tr>";

							echo "<td class='fila1'>Movimiento hospitalario</td>";
							echo "<td class='fondoAmarillo' colspan=5>";
							echo $paciente->ultimoMvtoHospitalario;
							echo "</td>";

							echo "</tr>";

							echo "</table>";

							//Si el paciente se encuentra en alta definitiva no debe permitir modificaciones en el kardex
							if($paciente->altaDefinitiva == "on"){
								echo "<br/><span class='subtituloPagina2'>El paciente se encuentra en alta definitiva, no puede crearse kardex.</span><br/>";
								echo "<br/><center><input type='button' value='Regresar' id='regresar' onclick='javascript:inicio(\"$paciente->servicioActual\");'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'><br/></center>";
							} else {
								echo "<br/><center><input type='button' value='Regresar' id='regresar' onclick='javascript:inicio(\"$paciente->servicioActual\");'> | <input type='button' id='btnConfirmar' value='Confirmar generacion Kardex' onclick='javascript:confirmarGeneracion();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'><br/></center>";
							}
						}
					} else {
						//Modo consulta
//						echo "consulta";
						funcionJavascript("confirmarGeneracion();");
					}
				} else {
					mensajeEmergente("No se pudo consultar el ultimo ingreso del paciente.  Verifique que la historia clínica fue digitada correctamente");
					funcionJavascript("inicio(\"\");");
				}//Fin existe ingreso de historia e informacion de paciente
			} else {
				mensajeEmergente("Faltan parametros para realizar la consulta del kardex");
				funcionJavascript("inicio(\"\");");
			}
			break;
		case 'b': //Cuando ya hay un kardex creado se muestra la pantalla de modificación

			//Octubre 27 de 2011
			borrarTablaTemporal( $conex, $wbasedato );

			/*******************************************************
			 * EL KARDEX PUEDE SER EDITABLE (SOLO EL DE HOY) O DE SOLO LECTURA (CUALQUIER OTRA FECHA)
			 *******************************************************/
			//$usuario->centroCostos
			if(!$usuario->esUsuarioLactario){
				if($usuario->esUsuarioCM || $usuario->esUsuarioSF){
					$usuario->centroCostosGrabacion = "*";
				}
			}
//			echo "Centro costos grabacion modificado $usuario->centroCostosGrabacion";

			$aplicaGraficaSuministro = true;
			$activarPestanas = false;			//**OJO TODO:  ESTO SE DEBE QUITAR

			$paciente = consultarInfoPacienteKardex($whistoria,$wingreso);

			/********************************************************************************
			 * Agosto 8 de 2011
			 * Modificaciones. Agosto 26 de 2011
			 ********************************************************************************/
			if( !isset( $editable ) || $editable == 'on' ){

				if( ( esAutomatico( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha )
					|| !existeEncabezadoKardex( $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha ) )
					&& $wfecha == date( "Y-m-d" )
				){

					echo "<INPUT type='hidden' id='mostrarConfirmarKardex' name='mostrarConfirmarKardex' value='on'>";

					echo "<div id='msjConfirmarKardex' style='display:none' class='fondoAmarillo'>";
					echo "<br><h1><center><b id='txConfKar'>¡¡¡ ATENCION !!!</b></center></h1>";
					echo "<h1><center><b id='txConfKar'>PRIMERA VEZ QUE INGRESA HOY AL KARDEX<BR>DEBE CONFIRMAR EL KARDEX AL SALIR</b></center></h1><br>";
					echo "<center><INPUT type='button' onClick='$.unblockUI();' value='Aceptar'></center><br>";
					echo "</div>";

					quitandoKardexAutomatico( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha );
				}
				else{
					echo "<INPUT type='hidden' id='mostrarConfirmarKardex' name='mostrarConfirmarKardex' value='off'>";
				}
			}
			/********************************************************************************/


			$kardexActual = consultarKardexPorFechaPaciente($wfecha, $paciente);
			$auxConfirmado = $kardexActual->confirmado;

			/**********************************************************************
			 * Si esta siendo dispensado el kardex no se deja abrir
			 **********************************************************************/
			if( $kardexActual->usuarioPDA != 'NO APLICA' && !empty( $kardexActual->usuarioPDA ) ){

				$sql = "SELECT CONCAT(Codigo,' - ',Descripcion) Usuario FROM usuarios WHERE Codigo = '$kardexActual->usuarioPDA'";
				$resUsuPDA = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());

				if( $rowsUsuPDA = mysql_fetch_array( $resUsuPDA ) ){

					mensajeEmergente( "El perfil esta siendo dispensado por ".$rowsUsuPDA[ 'Usuario' ] );
					funcionJavascript( "inicio(".$paciente->servicioActual.");" );
					exit;
				}
			}
			/**********************************************************************/

			if($kardexActual->grabado == 'off' && !$kardexActual->esAnterior){
//				mensajeEmergente("El kardex está siendo modificado en este momento.");
//				funcionJavascript("inicio();");
			}

			//INDICADOR DE DESCUENTO DE DISPENSACION::
			$noAcumulaSaldoDispensacion = $kardexActual->noAcumulaSaldoDispensacion;
			$descontarDispensaciones = $kardexActual->descontarDispensaciones;

			//Kardex anterior
			if((empty($kardexActual->historiaClinica) || $kardexActual->esAnterior)){
				$kardex = new kardexDTO();

				$kardex->historia = $paciente->historiaClinica;
				$kardex->ingreso = $paciente->ingresoHistoriaClinica;
				$kardex->fechaCreacion = date("Y-m-d");
				$kardex->horaCreacion = date("H:i:s");
				$kardex->estado = "on";
				$kardex->usuario = $wuser;
				$kardex->esAnterior = $kardexActual->esAnterior;
				$kardex->editable = true;

				//Trae los datos del encabezado del dia anterior
				$kardex->talla = $kardexActual->talla;
				$kardex->peso = $kardexActual->peso;
				$kardex->diagnostico = $kardexActual->diagnostico;
				$kardex->antecedentesAlergicos = $kardexActual->antecedentesAlergicos;
				$kardex->cuidadosEnfermeria = $kardexActual->cuidadosEnfermeria;
				$kardex->observaciones = $kardexActual->observaciones;
				$kardex->curaciones = $kardexActual->curaciones;
				$kardex->terapiaRespiratoria = $kardexActual->terapiaRespiratoria;
				$kardex->sondasCateteres = $kardexActual->sondasCateteres;
				$kardex->interconsulta = $kardexActual->interconsulta;
				$kardex->consentimientos = $kardexActual->consentimientos;
				$kardex->preparacionAlta = $kardexActual->preparacionAlta;

				$kardex->obsDietas = $kardexActual->obsDietas;
				$kardex->mezclas = $kardexActual->mezclas;
				$kardex->dextrometer = $kardexActual->dextrometer;
				$kardex->cirugiasPendientes = $kardexActual->cirugiasPendientes;
				$kardex->terapiaFisica = $kardexActual->terapiaFisica;
				$kardex->rehabilitacionCardiaca = $kardexActual->rehabilitacionCardiaca;
				$kardex->antecedentesPersonales = $kardexActual->antecedentesPersonales;
				$kardex->aislamientos = $kardexActual->aislamientos;
				$kardex->grabado = "off";

				$kardex->centroCostos = $usuario->centroCostosGrabacion;
				$kardex->usuarioQueModifica = $usuario->codigo;

				$kardex->rutaOrdenMedica = $kardexActual->rutaOrdenMedica;
				$kardex->horaDescuentoDispensaciones = $kardexActual->horaDescuentoDispensaciones;

				if(empty($kardexActual->historiaClinica)){
					$kardex->esPrimerKardex = true;
				} else {
					$kardex->esPrimerKardex = false;
				}
				$kardexActual = $kardex;

				$kardexActual->descontarDispensaciones = $descontarDispensaciones;
				$kardexActual->noAcumulaSaldoDispensacion = $noAcumulaSaldoDispensacion;

				$wfecha = date("Y-m-d");
			}

			/********************************************************************************
			 * Septiembre 14 de 2011
			 ********************************************************************************/
			$tieneCpx = tieneCpxPorHistoria( $conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );

			if( $kardexActual->editable && $tieneCpx ){
				$kardexActual->confirmado = $auxConfirmado;
			}
			/********************************************************************************/

			//Si el paciente se encuentra en alta definitiva no debe permitir modificaciones en el kardex
			if( $paciente->altaDefinitiva == "on" || (isset($editable) && $editable == "off" ) ){
				$kardexActual->editable = false;
			}

			if($kardexActual->editable){
				funcionJavascript("window.onbeforeunload = salida;");

				//Autorecuperacion de kardex anterior si no esta grabado on
				//Cuando realice una consulta del kardex debe apagarse la bandera de grabado
				marcarGrabacionKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, "off");
			}


			/************************************************************************************************************************
			 * Enero 26 de 2012
			 ************************************************************************************************************************/
			//Si el kardex es editable, la gestion de procedimientos realizados por la secretaria (Bitacora de procedimientos) son marcados como off
			if( $kardexActual->editable ){
				marcandoLeidoBitacoraProcedimientos( $conex, $wbasedato, $usuario->codigo, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica );
			}
			/************************************************************************************************************************/

			//Campos ocultos
			echo "<input type='hidden' name='whistoria' id='whistoria' value='$paciente->historiaClinica'>";
			echo "<input type='hidden' name='wingreso' id='wingreso' value='$paciente->ingresoHistoriaClinica'>";
			echo "<input type='hidden' name='wfecha' id='wfecha' value='$wfecha'>";
			echo "<input type='hidden' name='weditable' id='weditable' value='$kardexActual->editable'>";
			echo "<input type='hidden' name='wservicio' id='wservicio' value='$paciente->servicioActual'>";
			echo "<input type='hidden' id='enUrgencias' name='enUrgencias' value='".(( $paciente->enUrgencias ) ? "on" : "off")."'/>";	//Indica si el paciente tiene EPS

			if($kardexActual->noAcumulaSaldoDispensacion){
				echo "<input type='hidden' name='wkardexnoacumula' id='wkardexnoacumula' value='on'>";
			} else {
				echo "<input type='hidden' name='wkardexnoacumula' id='wkardexnoacumula' value='off'>";
			}

			//Indicador si es primer kardex o no. Es verdadero si no hay kardex del dia o del dia anterior.
			if($kardexActual->esPrimerKardex){
				echo "<input type='hidden' id='wkardexnuevo' value='S'>";
			} else {
				echo "<input type='hidden' id='wkardexnuevo' value='N'>";
			}

			// //Div flotante con las alergias y los diagnosticos
			// consultarAlergiasDiagnosticosAnteriores($whistoria,&$alergiasAnteriores,&$diagnosticosAnteriores);
			// echo "<div id='fixeddiv' style='position:absolute;z-index:99;width:228px;height:93px;right:10px;top:10px;padding:5px;background:#FFFFCC;border:2px solid #FFD700'>";
			// echo "<b>Alergias y alertas</b>&nbsp;|&nbsp;<a href='#null' onclick='return fixedMenu.hide();'>Cerrar</a>";
			// echo "<table>";
			// echo "<tr>";
			// echo "<td>";
			// echo "<textarea id='txFlAlergias' cols=25 rows=3 readonly>$alergiasAnteriores</textarea>";
			// echo "</td>";
			// echo "</tr>";
			// echo "</table>";
			// echo "</div>";
			// //Fin div flotante
			
			$estilosFrame = "position:absolute;top:68px;left:520px;z-index:99;width:228px;height:93px;padding:5px;background:#FFFFCC;border:2px solid #FFD700;";
			$posicionFrame = "position:absolute;top:68px;left:650px;z-index:99;";	//Julio 9 de 2018
			
			echo "<script>";
			echo "llamarIframeAlerta('".$paciente->historiaClinica."','".$paciente->ingresoHistoriaClinica."','".$wemp_pmla."','".$estilosFrame."',true,true,1,'".$posicionFrame."')";
			echo "</script>";

			echo "<table border=0 align=center>";
			echo "<tr>";
			echo "<td width='57%'>";
			echo '<span class="subtituloPagina2" align="center">';
			if($kardexActual->editable){
				echo "Editar kardex del d&iacute;a ".$wfecha;
			} else {
				echo "Consultar kardex del d&iacute;a $wfecha";
			}
			echo "</span>";
			echo "</td>";
			echo "<td width='60%' align='right'>";
			if($kardexActual->editable){
				if($kardexActual->confirmado == "on"){
					echo "<span class='textoMedio'>Confirmar kardex e iniciar dispensación</span></label><input type='checkbox' name='wcconf' id='wcconf' onClick='javascript:marcarKardexConfirmado();' checked>&nbsp;|&nbsp;";
				} else {
					echo "<span class='textoMedio'>Confirmar kardex e iniciar dispensación</span></label><input type='checkbox' name='wcconf' id='wcconf' onClick='javascript:marcarKardexConfirmado();'>&nbsp;|&nbsp;";
				}
				echo "<input type='button' value='Grabar kardex' onclick='javascript:grabarKardex();'>";
			}
//			echo "&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'>";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br>";

			//El encabezado es comun a todas las secciones del kardex
			echo "<table align='center'>";

			echo "<tr>";

			echo "<td class='fila1'>Historia cl&iacute;nica</td>";
			echo "<td class='fila2'>";
			echo $paciente->historiaClinica."-".$paciente->ingresoHistoriaClinica;
			echo "</td>";

			$tipo = $color = '';
			clienteMagenta( $paciente->documentoIdentidad, $paciente->tipoDocumentoIdentidad, $tipo, $color );

			if( empty($tipo) ){
				echo "<td class='fila1' align=center rowspan=2><b><font size=3>Paciente</font></b></td>";
				echo "<td class='fila2' align=center colspan=3 rowspan=2><b><font size=3>";
				echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
				echo "</font></b></td>";
			}
			else{
				echo "<td class='fila1' align=center rowspan=2><b><font size=2>Paciente</font></b></td>";
				echo "<td class='fila2' align=center colspan=2 rowspan=2><b><font size=3>";
				echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
				echo "</font></b></td>";
				echo "<td class='fila1' align=center><b><font style='font-size:10pt'>";
				echo "Afinidad:";
				echo "</font></b></td>";
			}

			echo "</tr>";

			echo "<tr>";

			//Servicio actual y habitacion

			echo "<td class='fila1'>Servicio y Habitaci&oacute;n actual</td>";
			echo "<td class='fila2'>";
			echo "$paciente->nombreServicioActual - $paciente->habitacionActual";
			echo "</td>";

			if( !empty($tipo) ){
				echo "<td bgcolor='$color' align=center><b><font size=2 color='white'>";
				echo $tipo;
				echo "</font></b></td>";
			}

			//Enfermera(o) que genera
			echo "<tr>";
			echo "<td class='fila1'>Enfermera(o) que actualiza</td>";
			echo "<td class='fila2'>";
			echo "$usuario->codigo - $usuario->descripcion. <br>$usuario->nombreCentroCostos";
			echo "</td>";

			echo "<td class='fila1'>Fecha y hora de generaci&oacute;n</td>";
			echo "<td class='fila2'>";
			echo "".$kardexActual->fechaCreacion." - ".$kardexActual->horaCreacion;
			echo "</td>";

			echo "<td class='fila1'>Fecha y hora de ingreso a la instituci&oacute;n</td>";
			echo "<td class='fila2'>";
			echo "$paciente->fechaIngreso - $paciente->horaIngreso";
			echo "</td>";

			echo "</tr>";

			echo "<tr>";

			//Valor de la edad
			$vecAnioNacimiento = explode("-",$paciente->fechaNacimiento);
			echo "<td class='fila1'>Edad</td>";
			echo "<td class='fila2'>";
			echo $paciente->edadPaciente;
			echo "</td>";

			echo "<td class='fila1'>&Uacute;ltimo mvto hospitalario</td>";

			if($paciente->altaDefinitiva == 'on'){
				echo "<td class='articuloControl'>";
			} else {
				echo "<td class='fondoAmarillo'>";
			}
			echo $paciente->ultimoMvtoHospitalario;
			echo "</td>";

			//Calculo de dias de hospitalizcion desde ingreso
			$diaActual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$fecha = explode("-",$paciente->fechaIngreso);
			$diaIngreso = mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);

			$diasHospitalizacion = ROUND(($diaActual - $diaIngreso)/(60*60*24));

			echo "<td class='fila1'>D&iacute;as de hospitalizaci&oacute;n</td>";
			echo "<td class='fila2'>";
			echo "".$diasHospitalizacion;
			echo "</td>";

			echo "<td colspan=2>&nbsp;</td>";

			echo "</tr>";

			echo "<tr>";

			//Responsable
			echo "<td class='fila1'>Entidad responsable</td>";
			echo "<td class='fila2'>";
			echo "$paciente->numeroIdentificacionResponsable - $paciente->nombreResponsable";
			echo "</td>";

			//Fecha y hora de ingreso al servicio actual
			echo "<td class='fila1'>Fecha de ingreso al servicio actual</td>";
			echo "<td class='fila2'>";
			echo "$paciente->fechaHoraIngresoServicio";
			echo "</td>";

			$estilosFrame = "position:absolute;top:68px;left:520px;z-index:99;width:228px;height:93px;padding:5px;background:#FFFFCC;border:2px solid #FFD700;";
			$posicionFrame = "position:absolute;top:68px;left:650px;z-index:99;";
			
			echo "<td class='fila1'>Otros</td>";
			echo "<td class='fila2'>";
			// echo "<a href='#null' onclick='return fixedMenu.show();'>Alergias</a>";
			echo "<a href='#null' onclick='llamarIframeAlerta(\"".$paciente->historiaClinica."\",\"".$paciente->ingresoHistoriaClinica."\",\"".$wemp_pmla."\",\"".$estilosFrame."\",true,true,1,\"".$posicionFrame."\");'>Alergias</a>";
			echo "</td>";
			
			echo "</tr>";

			echo "<tr>";
			echo "<td height=30 colspan=6>&nbsp;</td>";
			echo "</tr>";

			echo "</table>";

			/**********************************************************************
			 * Noviembre 4 de 2011
			 **********************************************************************/
			if( !$kardexActual->editable ){
				$usuario->centroCostosGrabacion = "*";
			}
			/**********************************************************************/

			/************************************************
			 * Agrengando mensajeria
			 ************************************************/
			//Campo oculto que indica de que programa se abrio

			echo "<INPUT type='hidden' id='mesajeriaPrograma' value='Kardex'>";

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

			/***************************
			 * Movimiento de articulos
			 ***************************/
			if($kardexActual->editable){
				echo "<div id='fixeddiv2' style='position:absolute;display:none;z-index:200;width:450px;height:425px;left:200px;top:10px;padding:5px;background:#FFFFFF;border:2px solid #2266AA'>";
				echo "<table>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='encabezadoTabla'>";
				echo "<b>Buscador de medicamentos</b>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";

				echo "<td class='fila1'>C&oacute;digo</td>";
				echo "<td class='fila2'>";
				echo "<INPUT TYPE='text' NAME='wcodmed' id='wbcodmed' SIZE=20  class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarMedicamento();\");'>";
				echo "</td>";

				echo "</tr>";

				echo "<tr>";
				echo "<td class='fila1'>Nombre</td>";
				echo "<td class='fila2'>";
				echo "<INPUT TYPE='text' NAME='wnommed' id='wbnommed' SIZE=20 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarMedicamento();\");'>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='fila1'>";
				echo "<b>Parametros de consulta</b>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='fila2'>";
				echo "N.Genérico<input type='radio' id='wtipoart' name='wtipoart' value='G'>&nbsp;N.Comercial<input type='radio' id='wtipoart' name='wtipoart' value='C' checked>";
				echo " | ";

				echo "<select id='wunidadmed' name='wunidadmed' class='seleccionNormal'>";

				echo "<option value='%'>Cualquier unidad de medida</option>";
				foreach ($colUnidades as $unidad){
					echo "<option value='".$unidad->codigo."'>$unidad->codigo - $unidad->descripcion</option>";
				}

				echo "</select>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='fila1'>";
				echo "<b>Protocolo</b>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 align=center class='fila2'>";
				echo "Normal<input type='radio' id='wtipoprot' name='wtipoprot' value='$protocoloNormal' checked onClick='javascript:limpiarBuscador();'>&nbsp;|&nbsp;";
				echo "Analgesia<input type='radio' id='wtipoprot' name='wtipoprot' value='$protocoloAnalgesia' onClick='javascript:limpiarBuscador();'>&nbsp;|&nbsp;";
				echo "Nutrici&oacute;n<input type='radio' id='wtipoprot' name='wtipoprot' value='$protocoloNutricion' onClick='javascript:limpiarBuscador();'>&nbsp;|&nbsp;";
				echo "Quimioterapia<input type='radio' id='wtipoprot' name='wtipoprot' value='$protocoloQuimioterapia' onClick='javascript:limpiarBuscador();'>";

				echo "</td>";
				echo "</tr>";

				echo "<tr><td colspan=4 align=center>";
				echo "<input type='button' value='Consultar' onclick='javascript:consultarMedicamento();'>&nbsp;|&nbsp;<input type='button' value='Nuevo artículo' onclick='javascript:agregarArticulo();'>&nbsp;|&nbsp;<input type='button' value='Cerrar' onclick='return fixedMenu2.hide();'>";
				echo "</td></tr>";

				echo "<tr>";
				echo "<td colspan=4 class='fila2'>";
				echo "<img id='imgCodMed' style='display:none' src='../../images/medical/ajax-loader5.gif'>";
				echo "<div id='cntMedicamento' style='overflow-y: scroll; width: 100%; height: 160px;'>";
				echo "</div>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td colspan=4 class='fila2'>";
				echo "<span><b>NOTA: </b>Realice su búsqueda específica, este buscador retornará hasta cien resultados</span>";
				echo "</td>";
				echo "</tr>";

				echo "<tr>";
				echo "<td class='fila1'>";
				echo "Grupos de medicamentos";
				echo "</td>";
				echo "<td class='fila2' width='350px'>";
				echo $usuario->gruposMedicamentos;
				echo "</td>";
				echo "</tr>";

				echo "</table>";
				echo "</div>";

			}

			/*****************
			 * INICIO DE LA DIVISIÓN POR PESTAÑAS.
			 *****************/
			//Mensaje de espera
			echo "<div id='msjInicio' align=center>";
			echo "<img src='../../images/medical/ajax-loader5.gif'/>Cargando las pestañas, por favor espere...";
			echo "</div>";

			echo "<input type=hidden id=hpestanas value='$usuario->pestanasKardex'>";

			echo "<div id='tabs' class='ui-tabs' style='display:none'>";				//Inicio de lo que va a ir encerrado en las pestañas
			echo "<ul>";

			if($usuario->pestanasKardex == "*"){
				echo "<li><a href='#fragment-1'><span>Informaci&oacute;n demogr&aacute;fica</span></a></li>";
				echo "<li><a href='#fragment-2'><span>L&iacute;quidos endovenosos</span></a></li>";
				echo "<li><a href='#fragment-3'><span>Medicamentos</span></a></li>";
				echo "<li><a href='#fragment-4'><span>Ex&aacute;menes</span></a></li>";
				echo "<li><a href='#fragment-5'><span>Pendientes</span></a></li>";
				echo "<li><a href='#fragment-6'><span>Dextrometer</span></a></li>";
				echo "<li><a href='#fragment-7'><span>Auditor&iacute;a</span></a></li>";
				echo "<li><a href='#fragment-8'><span>Mezclas</span></a></li>";
				echo "<li><a href='#fragment-9'><span>Quimioterapia</span></a></li>";

				if($activarPestanas){
					$pestanas = consultarPestanas();

					foreach($pestanas as $pestana){
						echo "<li><a href='#frag-$pestana->codigoPestana'><span>$pestana->nombrePestana</span></a></li>";
					}
				}
			} else {
				if(strpos($usuario->pestanasKardex,"1") !== false){
					echo "<li><a href='#fragment-1'><span>Informaci&oacute;n demogr&aacute;fica</span></a></li>";
				}
				if(strpos($usuario->pestanasKardex,"2") !== false){
					echo "<li><a href='#fragment-2'><span>L&iacute;quidos endovenosos</span></a></li>";
				}
				if(strpos($usuario->pestanasKardex,"3") !== false){
					echo "<li><a href='#fragment-3'><span>Medicamentos</span></a></li>";
				}
				if(strpos($usuario->pestanasKardex,"4") !== false){
					echo "<li><a href='#fragment-4'><span>Ex&aacute;menes</span></a></li>";
				}
				if(strpos($usuario->pestanasKardex,"5") !== false){
					echo "<li><a href='#fragment-5'><span>Pendientes</span></a></li>";
				}
				if(strpos($usuario->pestanasKardex,"6") !== false){
					echo "<li><a href='#fragment-6'><span>Dextrometer</span></a></li>";
				}
				if(strpos($usuario->pestanasKardex,"7") !== false){
					echo "<li><a href='#fragment-7'><span>Auditor&iacute;a</span></a></li>";
				}
				if(strpos($usuario->pestanasKardex,"8") !== false){
					echo "<li><a href='#fragment-8'><span>Mezclas</span></a></li>";
				}
				if(strpos($usuario->pestanasKardex,"9") !== false){
					echo "<li><a href='#fragment-9'><span>Quimioterapia</span></a></li>";
				}
			}
			echo "</ul>";

			if(strpos($usuario->pestanasKardex,"1") !== false || $usuario->pestanasKardex == "*"){
				echo "<div id='fragment-1'>";

				echo "<table align='center'>";

				echo "<tr>";

				$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
				
				if($kardexActual->talla == "")
				{
					// consecutivo 136
					$kardexActual->talla = consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, "tallaHCE", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
				}
				
				
				if($kardexActual->peso == "")
				{
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
					
					$kardexActual->peso = consultarCamposHCE( $wemp_pmla, $conex, $wbasedato, $wbasedatohce, $parametroPeso, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
				}
				
				
				
				
				//Talla
				echo "<td class='fila1'>Talla&nbsp;";
				if($kardexActual->editable){
					echo "<input type=text name=txTalla class='textoNormal' maxlength=4 size=5 value='$kardexActual->talla'/>";
				} else {
					echo "$kardexActual->talla";
				}
				echo "</td>";

				//Peso
				if($kardexActual->editable){
					echo "<td class='fila1'>Peso (kg.)&nbsp;<input type=text name=txPeso class='textoNormal' maxlength=5 size=5 value='$kardexActual->peso'/></td>";
				} else {
					echo "<td class='fila1'>Peso (kg.)&nbsp;$kardexActual->peso</td>";
				}

				// echo "<td class='fila1'>&nbsp;</td>";
				echo "</tr>";

				$diagnosticos = consultarUltimoDiagnosticoHCE( $conex, $wemp_pmla, "", $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
				
				//Diagnostico actual
				echo "<tr>";
				echo "<td align=center class='fila1'>";
				echo "Diagnostico actual</br>";
				if($kardexActual->editable){
					// echo "<textarea name='txDiag' cols=60 rows=8>$kardexActual->diagnostico</textarea>";
					echo "<textarea name='txDiag' cols=60 rows=8>".$diagnosticos."</textarea>";
				}else{
					// echo "<textarea name='txDiag' cols=60 rows=8 readonly>$kardexActual->diagnostico</textarea>";
					echo "<textarea name='txDiag' cols=60 rows=8 readonly>".$diagnosticos."</textarea>";
				}
				echo "<br/>&nbsp;</td>";

				// //Antecedentes alergicos
				// echo "<td align=center class='fila1'>";
				// echo "Antecedentes al&eacute;rgicos y alertas</br>";
				// if($kardexActual->editable){
					// echo "<textarea name='txAlergias' cols=40 rows=8>$kardexActual->antecedentesAlergicos</textarea>";
				// }else{
					// echo "<textarea name='txAlergias' cols=40 rows=8 readonly>$kardexActual->antecedentesAlergicos</textarea>";
				// }
				// echo "<br/>&nbsp;</td>";

				//Antecedentes personales
				echo "<td align=center class='fila1'>";
				echo "Antecedentes personales</br>";
				if($kardexActual->editable){
					echo "<textarea name='txAntecedentesPersonales' cols=60 rows=8>$kardexActual->antecedentesPersonales</textarea>";
				}else{
					echo "<textarea name='txAntecedentesPersonales' cols=60 rows=8 readonly>$kardexActual->antecedentesPersonales</textarea>";
				}
				echo "<br/>&nbsp;</td>";

				echo "</tr>";
				echo "</td>";
				
				//Medicamentos de consumo habitual
				// echo "<td align=center class='fila1' colspan=3>";
				echo "<td align=center class='fila1' colspan=2>";
				echo "<br><b>Medicamentos de consumo habitual</b></br>";
				
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
				
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					
					echo "<div>$wtabla_medConsumoHabitual</div>";

				} else {
					echo "<div>$wtabla_medConsumoHabitual</div>";
				}
				echo "<br/>&nbsp;</td>";
				
				echo "</tr>";
				echo "</table>";

				// echo "<div align='center'>";

				// //Muestra las alergias de los dias para eliminar
				// $colAlergias = consultarAlergias($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);

				// if(count($colAlergias) > 0){
					// echo "<table>";
					// echo "<thead>";
					// echo "<tr class=encabezadoTabla>";
					// echo "<td colspan=3 align=center>Retiro de alergias anteriores</td>";
					// echo "</tr>";

					// echo "<tr class=encabezadoTabla align=center>";

					// echo "<td>Fecha de registro</td>";
					// echo "<td>Descripcion</td>";
					// if($kardexActual->editable){
						// echo "<td>Accion</td>";
					// }

					// echo "</tr>";
					// echo "</thead>";

					// $clase="fila1";

					// echo "<tbody id='detAlergias'>";

					// foreach ($colAlergias as $alergia){
						// if($clase=="fila1"){
							// $clase = "fila2";
						// } else {
							// $clase = "fila1";
						// }

						// echo "<tr class=$clase id='trAle$alergia->descripcion'>";

						// echo "<td>$alergia->descripcion</td>";
						// echo "<td>$alergia->observacion</td>";
						// if($kardexActual->editable){
							// echo "<td align='center'><a href='#null' onclick='javascript:quitarAlergia("."\"$alergia->descripcion"."\");'><img src='../../images/medical/root/borrar.png' alt='Quitar alergia'></a></td>";
						// }

						// echo "</tr>";
					// }
					// echo "</tbody>";
					// echo "</table>";
				// }
				// echo "</div>";

				echo "</div>";
			}

			$indicePestana = "2";
			if(strpos($usuario->pestanasKardex,"2") !== false  || $usuario->pestanasKardex == "*"){
				echo "<div id='fragment-2'>";
				$cont1 = 0;
				if($kardexActual->editable){
					echo "<table align='center'>";
					echo "<tr>";

					echo "<td class='fila1'>C&oacute;digo</td>";
					echo "<td class='fila2'>";
					echo "<INPUT TYPE='text' NAME='wcodcom' SIZE=10 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarComponente()\");'>";
					echo "</td>";

					echo "<td rowspan=2 colspan=2 class='fila2'>";
					echo "<img id='imgCodCom' style='display:none' src='../../images/medical/ajax-loader5.gif'>";
					echo "<div id='cntComponente' style='overflow-y: scroll; width: 430px; height: 160px;'>";
					echo "</div>";
					echo "</td>";

					echo "</tr>";

					echo "<tr>";
					echo "<td class='fila1'>Nombre</td>";
					echo "<td class='fila2'>";
					echo "<INPUT TYPE='text' NAME='wnomcom' SIZE=50 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarComponente()\");'>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 align=center  class='encabezadoTabla'>";
					echo "<b>Parametros de consulta</b>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 align=center class='fila2'>";
					echo "Nombre Genérico<input type='radio' id='wtipocom' name='wtipocom' value='G'>&nbsp;Nombre Comercial<input type='radio' id='wtipocom' name='wtipocom' value='C' checked>";
					echo " | ";
					echo "Unidad de medida&nbsp;";
					echo "<select id='wunidadcom' name='wunidadcom' class='seleccionNormal'>";

					$colUnidades = consultarUnidadesMedida();

					echo "<option value='%'>Cualquier unidad de medida</option>";
					foreach ($colUnidades as $unidad){
						echo "<option value='".$unidad->codigo."'>$unidad->codigo - $unidad->descripcion</option>";
					}

					echo "</select>";

					echo "</td>";
					echo "</tr>";

					echo "<tr><td colspan=4 align=center>";
					echo "<input type='button' value='Consultar' onclick='javascript:consultarComponente();' > | <input type='button' value='Nuevo' onclick='javascript:agregarInfusion();' >";
					echo "</td></tr>";
					echo "</table>";
					$cont1++;
				}

				echo "<table align='center' border=0 id='tbDetInfusiones'>";

				echo "<tr align='center'>";
				if($kardexActual->editable){
					echo "<td class='encabezadoTabla'>Acciones</td>";
					echo "<td class='encabezadoTabla'>Fecha de solicitud</td>";
					echo "<td class='encabezadoTabla'>Componentes</td>";
					echo "<td class='encabezadoTabla'>Observaciones</td>";
				}else{
					echo "<td class='encabezadoTabla'>Fecha de solicitud</td>";
					echo "<td class='encabezadoTabla'>Componentes</td>";
					echo "<td class='encabezadoTabla'>Observaciones</td>";
				}
				echo "</tr>";

				echo "<tbody id='detInfusiones'>";

				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */
				if($kardexActual->editable){
					if($kardexActual->editable && $kardexActual->esAnterior && $esFechaActual){
						//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
						$colTemporal = consultarInfusionesTemporalKardex($whistoria,$wingreso,$wfecha);
						if(count($colTemporal) == 0){
							cargarInfusionesAnteriorATemporal($whistoria,$wingreso,$wfecha,$fechaGrabacion);
						}
					}

					//1. Consulta de estructura temporal.
					$componentesInfusion = consultarInfusionesTemporalKardex($whistoria,$wingreso,$wfecha);
					$cuentaInfusiones = count($componentesInfusion);
					$contInfusiones = 0;

					if($cuentaInfusiones == 0){
						$componentesInfusion = consultarInfusionesDefinitivoKardex($whistoria,$wingreso,$wfecha);
						$cuentaInfusiones = count($componentesInfusion);

						if($cuentaInfusiones > 0 && $esFechaActual){
							//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
							cargarInfusionesATemporal($whistoria,$wingreso,$wfecha,$wfecha);
						}
					}
				} else {
					$componentesInfusion = consultarInfusionesDefinitivoKardex($whistoria,$wingreso,$wfecha);
					$cuentaInfusiones = count($componentesInfusion);
					$contInfusiones = 0;
				}

				$mayorIdInfusiones = $cuentaInfusiones;
				$cont1 = 0;
				foreach ($componentesInfusion as $infusion){
					if($cont1 % 2 == 0){
						echo "<tr id='trIn$infusion->codigo' class='fila1'>";
					} else {
						echo "<tr id='trIn$infusion->codigo' class='fila2'>";
					}

					if($kardexActual->editable){
						echo "<td align='center'>";
//						echo "<a href='#null' onclick='javascript:grabarInfusion($infusion->codigo);'><img src='../../images/medical/root/grabar.png'/></a>";
						echo "<a href='#null' onclick='javascript:quitarInfusion($infusion->codigo);'><img src='../../images/medical/root/borrar.png'/></a>";

						echo "<INPUT TYPE='hidden' name='wmodificado$indicePestana$infusion->codigo' id='wmodificado$indicePestana$infusion->codigo' value='N'>";
						echo "<INPUT TYPE='hidden' name='windiceliq$contInfusiones' id='windiceliq$contInfusiones' value='$infusion->codigo'>";

						echo "</td>";
					}

					//Fecha de solicitado examen
					echo "<td align=center>";
					echo "<INPUT TYPE='text' name='wfliq$infusion->codigo' id='wfliq$infusion->codigo' SIZE=10 readonly class='campo2' value='$infusion->fecha' onChange='javascript:marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>";
					if($kardexActual->editable){
						echo "<input type='button' id='btnFechaLiq$infusion->codigo' onclick='javascript:calendario4($infusion->codigo);' height=20 value='*'>";
					}
					echo "</td>";

					//Componentes de la infusion en forma de textarea
					echo "<td><textarea id=wtxtcomponentes$infusion->codigo cols=65 rows=5 readonly onChange='javascript:marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>".str_replace(';',"\r\n",$infusion->descripcion)."</textarea></td>";

					//Componentes de la infusion en forma de textarea
					echo "<td><textarea id=wobscomponentes$infusion->codigo cols=65 rows=5 onChange='javascript:marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>".htmlentities(str_replace(';',"\r\n",$infusion->observacion))."</textarea></td>";
					echo "</tr>";

					if(intval($mayorIdInfusiones) < intval($infusion->codigo)){
						$mayorIdInfusiones = intval($infusion->codigo+1);
					}

					$contInfusiones++;
				}

				echo "</tbody>";
				echo "</table>";
				echo "</div>";
			}

			if(strpos($usuario->pestanasKardex,"3") !== false || $usuario->pestanasKardex == "*"){
				echo "<div id='fragment-3'>";

				$elementosActuales = 0;
				$colDetalle = array();

				if($kardexActual->editable){
					echo "<div align=center><input type='button' value='Movimiento de articulos' onclick='javascript:abrirMovimientoArticulos(\"N\");'></div>";
				}

				vista_generarConvencion();		//Genera la muestra de convenciones de los articulos

				//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
				realizarMovimientosArticulos($kardexActual, $paciente, $esFechaActual, $wfecha, $fechaGrabacion, $protocoloNormal, $elementosActuales, $colDetalle);

				//Despliega la vista de la tabla de articulos para el protocolo normal
				vista_desplegarListaArticulos($colDetalle,$elementosActuales,$protocoloNormal,$kardexActual->editable,$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro, manejaCPX( $conex, $wbasedato, $paciente->servicioActual ) );

				/**************************************************************************************************************
				 * Junio 19 de 2012
				 *
				 * Si no hay articulos para mostrar y hay medicamentos el día anterior que se estan activos, doy la opcion
				 * de traer medicamentos del día anterior
				 **************************************************************************************************************/
				if( $kardexActual->editable ){

					if( empty( $colDetalle ) ){

						$conMedicamentos = tieneMedicamentosActivos( $conex, $wbasedato, $wcenmez, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $usuario->centroCostosGrabacion, date( "Y-m-d", strtotime( "$fechaGrabacion 00:00:00" ) - 24*3600 ) );

						if( $conMedicamentos ){
							echo "<table align='center' id='tbCargarMedicamentosAnteriores'>";
							echo "<tr><td>";
							echo "<br><INPUT type='button' onClick='cargarMedicamentosAnteriores($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,\"".date( "Y-m-d", strtotime( "$fechaGrabacion 00:00:00" ) - 24*3600 )."\",\"$usuario->centroCostosGrabacion\");' value='Medicamentos del d&iacute;a anterior'>";
							echo "<tr><td>";
							echo "</table>";
						}
					}
				}
				/**************************************************************************************************************/

				//Detalle de medicamentos anteriores
				$colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, $protocoloNormal);
				$cantidadElementosAnteriores = count($colDetalleAnteriorKardex);

				if($cantidadElementosAnteriores > 0){
					vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,$protocoloNormal,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias);
				} else {
					echo '<br><span class="subtituloPagina2" align="center">';
					echo "No hay medicamentos anteriores";
					echo "</span>";
					echo "<div id='medAnt'>";
					echo "</div>";
				}
				echo "</div>";
			}
			$indicePestana = "4";
			if(strpos($usuario->pestanasKardex,"4") !== false || $usuario->pestanasKardex == "*"){
				//Examenes
				echo "<div id='fragment-4'>";
				
				$ir_a_ordenes = ir_a_ordenes($wemp_pmla, $paciente->servicioActual);
				
				if($ir_a_ordenes != 'on'){
							
				//Imprime las ordenes del paciente en caso de tenerlas.
				traer_ordenes($wemp_pmla, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica);
				echo "<br>";
				traer_ordenes_realizadas($wemp_pmla, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica);
				
				}
				
				echo "<br>";

				if($kardexActual->editable){
					echo "<div align='center'><input type='button' onClick='agregarExamen();' value='Agregar examen' ></div>";
					echo "<br/>";
				}
				
				echo "<table align='center'>";

				echo "<tr align='center'>";
				if($kardexActual->editable){
					echo "<td class='encabezadoTabla'>Acciones</td>";
					echo "<td class='encabezadoTabla'>Unidad que realiza</td>";
					echo "<td class='encabezadoTabla'>Observaciones</td>";
					echo "<td class='encabezadoTabla'>Bitacora de gestiones</td>";
					echo "<td class='encabezadoTabla'>Realizar en la fecha</td>";
					echo "<td class='encabezadoTabla'>Estado</td>";
				}else{
					echo "<td class='encabezadoTabla'>Unidad que realiza</td>";
					echo "<td class='encabezadoTabla'>Observaciones</td>";
					echo "<td class='encabezadoTabla'>Bitacora de gestiones</td>";
					echo "<td class='encabezadoTabla'>Fecha de solicitado</td>";
					echo "<td class='encabezadoTabla'>Estado</td>";
				}
				echo "</tr>";

				echo "<tbody id='detExamenes'>";

				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */
				if($kardexActual->editable){
					if($kardexActual->editable && $kardexActual->esAnterior && $esFechaActual){
						//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
						$colTemporal = consultarExamenesLaboratorioTemporalKardex($whistoria,$wingreso,$wfecha);
						if(count($colTemporal) == 0){
							cargarExamenesAnteriorATemporal($whistoria,$wingreso,$wfecha,$fechaGrabacion);
						}
					}
					//1. Consulta de estructura temporal
					$examenesLaboratorio = consultarExamenesLaboratorioTemporalKardex($whistoria,$wingreso,$wfecha);
					$cuentaExamenes = count($examenesLaboratorio);

					if($cuentaExamenes == 0){
						$examenesLaboratorio = consultarExamenesLaboratorioDefinitivoKardex($whistoria,$wingreso,$wfecha);
						$cuentaExamenes = count($examenesLaboratorio);

						if($cuentaExamenes > 0 && $esFechaActual){
							//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
							cargarExamenesATemporal($whistoria,$wingreso,$wfecha,$wfecha);
						}
					}
				} else {
					$examenesLaboratorio = consultarExamenesLaboratorioDefinitivoKardex($whistoria,$wingreso,$wfecha);
					$cuentaExamenes = count($examenesLaboratorio);
				}

				echo "<input type='HIDDEN' name='cuentaExamenes' id='cuentaExamenes' value='$cuentaExamenes'/>";
								
				$contExamenes = 0;

				foreach ($examenesLaboratorio as $examen){
						
					  if (is_integer($contExamenes/2))
							$wclass="fila1";
						else
						    $wclass="fila2";
					
					if($examen->clase_tr == 'marcar_rosado'){
					$wclass = 'suspendido';
					}	
					
					echo "<tr id='trEx$contExamenes' class='$wclass' align='center'>";
					
					if($kardexActual->editable){
						echo "<td>";
//						echo "<a href='#null' onclick='javascript:grabarExamen($contExamenes);'><img src='../../images/medical/root/grabar.png'/></a>";
						echo "<a href='#null' onclick='javascript:quitarExamen($contExamenes, \"$examen->id_registro_original\");'><img src='../../images/medical/root/borrar.png'/></a>";
						echo "</td>";
					}

					echo "<td><input type=hidden id='wid_$contExamenes' value='$examen->id_registro_original'><input type=hidden id='wexamenlab$contExamenes' value='$examen->codigoExamen'>$examen->codigoExamen - $examen->descripcionExamen</td>";			

					if($kardexActual->editable){
						//Ocultos
						echo "<input type=hidden name='wmodificado$indicePestana$contExamenes' id='wmodificado$indicePestana$contExamenes' value='N'>";

						echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='60' onkeypress='return validarEntradaAlfabetica(event);' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>$examen->observaciones</textarea></td>";


						/************************************************************************************************************************
						 * Enero 26 de 2012
						 * Pinto la bitacora de procedimientos
						 ************************************************************************************************************************/
						// echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='60' onkeypress='return validarEntradaAlfabetica(event);' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>$examen->observaciones</textarea></td>";
						if( !empty($examen->bitacoraGestion) && count( $examen->bitacoraGestion ) > 0 ){

							echo "<td>";
							echo "<div style='overflow:auto; height:80px;'>";
							// var_dump($examen->bitacoraGestion);
							echo "<table style='font-size:8pt'>";
							foreach( $examen->bitacoraGestion as $keyBitacora => $valueBitacora ){
								
								echo "<tr><td style='font-size:10pt'><b>";
								echo str_replace( "\n", "<br>", htmlentities( $valueBitacora['bitacora'] ) );
								echo "</b></td></tr>";
								echo "<tr><td style='font-size:7pt'>";
								echo $valueBitacora['fecha']." por ".$valueBitacora['usuario']." - ".$valueBitacora['nombre']."<br>";
								echo "</td></tr>";

								
							}
							echo "</table>";
							echo "</div>";
							echo "</td>";
						}
						else{
							echo "<td></td>";
						}
						/************************************************************************************************************************/

						//Fecha de solicitado examen
						echo "<td>";
						echo "<INPUT TYPE='text' NAME='wfsol$contExamenes' id='wfsol$contExamenes' SIZE=10 readonly class='campo2' value='$examen->fechaDeSolicitado' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>";
						echo "<input type='button' id='btnFechaSol$contExamenes' onclick='javascript:calendario3($contExamenes);' height=20 value='*'>";
						echo "</td>";

						echo "<td>";
						echo "<select name='westadoexamen$contExamenes' id='westadoexamen$contExamenes' class='campo2' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>";

						foreach ($colEstadosExamen as $estadoExamen){
							if($estadoExamen->codigo == $examen->estado){
								echo "<option value='".$estadoExamen->codigo."' selected>$estadoExamen->descripcion</option>";
							} else {
								echo "<option value='".$estadoExamen->codigo."'>$estadoExamen->descripcion</option>";
							}
						}
						echo "</select>";

						echo "</td>";
					}else{
						echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='60' readonly>$examen->observaciones</textarea></td>";


						/************************************************************************************************************************
						 * Enero 26 de 2012
						 * Pinto la bitacora de procedimientos
						 ************************************************************************************************************************/
						// echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='60' onkeypress='return validarEntradaAlfabetica(event);' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>$examen->observaciones</textarea></td>";
						if( !empty($examen->bitacoraGestion) && count( $examen->bitacoraGestion ) > 0 ){

							echo "<td>";
							echo "<div style='overflow:auto; height:80px;'>";
							// var_dump($examen->bitacoraGestion);
							echo "<table style='font-size:8pt'>";
							foreach( $examen->bitacoraGestion as $keyBitacora => $valueBitacora ){
								echo "<tr><td style='font-size:6pt'><b>";
								echo $valueBitacora['fecha']." por ".$valueBitacora['usuario']." - ".$valueBitacora['nombre'].": <br>";
								echo "</b></td></tr>";

								echo "<tr><td style='font-size:10pt'>";
								echo str_replace( "\n", "<br>", htmlentities( $valueBitacora['bitacora'] ) );
								echo "</td></tr>";
							}
							echo "</table>";
							echo "</div>";
							echo "</td>";
						}
						else{
							echo "<td></td>";
						}
						/************************************************************************************************************************/

						//Fecha de solicitado examen
						echo "<td>";
						echo $examen->fechaDeSolicitado;
						echo "</td>";

						//Estado del examen
						echo "<td>";
						foreach ($colEstadosExamen as $estadoExamen){
							if($estadoExamen->codigo == $examen->estado){
								echo $estadoExamen->descripcion;
							}
						}
						echo "</td>";
					}

					echo "</tr>";

					$contExamenes++;
				}

				echo "</tbody>";

				echo "</table>";

				$fechaTmp = "";
				$clase = "";

				$colExamenesAnteriores = consultarExamenesLaboratorioAnteriorKardex($whistoria,$wingreso,$wfecha);				
				$cuentaExamenesAnteriores = count($colExamenesAnteriores);
				
				if($cuentaExamenesAnteriores > 0){

					//Detalle examenes anteriores
					echo "<br><br>";
					echo '<span class="subtituloPagina2" align="center"  style="cursor:pointer;" onclick="ver_examenes_anteriores();">';
					echo "Detalle examenes en kardex anteriores (Ver)";
					echo "</span><br><br>";

					$contExamenes = 0;
					
					echo "<div id='lista_examenes_anteriores' style='display:none;'>";
					foreach ($colExamenesAnteriores as $examen){
						if($fechaTmp != $examen->fecha){
							if($clase == "fila1"){
								$clase = "fila2";
							} else {
								$clase = "fila1";
							}

							//Seccion nueva del acordeon
							if($contExamenes > 0){
								echo "</table>";
								echo "</p>";
								echo "</div>";
							}
							echo "<a href='#null' onclick=javascript:intercalarExamenAnterior('$examen->fecha');>$examen->fecha</a></br>";
							echo "<div id='ex$examen->fecha' style='display:none'>";

							echo "<p>";
							echo "<table align='center' border=0>";

							echo "<tr class='encabezadoTabla' align='center'>";

							echo "<td>Unidad que realiza</td>";
							echo "<td>Observaciones</td>";
							echo "<td>Bitacora de gestiones</td>";
							echo "<td>Realizar en la fecha</td>";
							echo "<td>Estado</td>";

							echo "</tr>";
						}

						echo "<tr class='$clase' align='center'>";

						//Fecha del kardex
						$fechaTmp = $examen->fecha;

						echo "<td>$examen->codigoExamen - $examen->descripcionExamen</td>";
						echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='60' readonly>$examen->observaciones</textarea></td>";

						/************************************************************************************************************************
						 * Enero 26 de 2012
						 * Pinto la bitacora de procedimientos
						 ************************************************************************************************************************/
						// echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='60' onkeypress='return validarEntradaAlfabetica(event);' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>$examen->observaciones</textarea></td>";
						if( !empty($examen->bitacoraGestion) && count( $examen->bitacoraGestion ) > 0 ){

							echo "<td style='font-size:8pt'>";
							echo "<div style='overflow:auto; height:80px;'>";
							// var_dump($examen->bitacoraGestion);
							echo "<table>";
							foreach( $examen->bitacoraGestion as $keyBitacora => $valueBitacora ){
								echo "<tr><td style='font-size:6pt'><b>";
								echo $valueBitacora['fecha']." por ".$valueBitacora['usuario']." - ".$valueBitacora['nombre'].": <br>";
								echo "</b></td></tr>";

								echo "<tr><td style='font-size:10pt'>";
								echo str_replace( "\n", "<br>", htmlentities( $valueBitacora['bitacora'] ) );
								echo "</td></tr>";
							}
							echo "</table>";
							echo "</div>";
							echo "</td>";
						}
						else{
							echo "<td></td>";
						}
						/************************************************************************************************************************/

						//Fecha de solicitado examen
						echo "<td>$examen->fechaDeSolicitado</td>";

						//Estado del examen
						echo "<td>";
						foreach ($colEstadosExamen as $estadoExamen){
							if($estadoExamen->codigo == $examen->estado){
								echo $estadoExamen->descripcion;
								break;
							}
						}
						echo "</td>";

						echo "</tr>";

						$contExamenes++;
					}
					echo "</table>";
					//Cierra el div que oculta los examenes anteriores.
					echo "</div>";
				}else {
					//Detalle examenes anteriores
					echo '<span class="subtituloPagina2" align="center">';
					echo "No hay examenes anteriores";
					echo "</span><br><br>";
					echo "<div id='exaAnt'>";
				}
				//Fin detalle examenes anteriores
				echo "</div>";

				echo "</div>";

				echo "<input type='HIDDEN' name='cuentaInfusiones' id='cuentaInfusiones' value='".$mayorIdInfusiones."'/>";
			}

			if(strpos($usuario->pestanasKardex,"5") !== false || $usuario->pestanasKardex == "*"){
				//Otros datos
				echo "<div id='fragment-5'>";

				echo "<table align='center'>";
				echo "<tr>";

				if($kardexActual->editable){
					echo "<td class='fila1'>M&eacute;dicos</td>";
					echo "<td align=center class='fila2'>";

					//Seleccion de especialidad
					echo "<select name='wselesp' id='wselesp' class='seleccionNormal' onchange='javascript:consultarMedicosEspecialidad();'>";
					$especialidades = consultarEspecialidades();
					echo "<option value=''>Seleccionar especialidad...</option>";
					foreach ($especialidades as $especialidad){
						echo "<option value='$especialidad->codigo'>".$especialidad->descripcion."</option>";
					}
					echo "</select>";

					echo "<br/><br/>";

					//Seleccion de medico
					echo "<span id='cntSelMedicos'>";
					echo "<select name='wselmed' id='wselmed' class='seleccionNormal'>";
					$medicos = consultarMedicos();
					echo "<option value=''>Seleccionar medico...</option>";
					foreach ($medicos as $medico){
						echo "<option value='".$medico->tipoDocumento."-".$medico->numeroDocumento."-".$medico->apellido1." ".$medico->apellido2.", ".$medico->nombre1." ".$medico->nombre2."-".$medico->codigoEspecialidad."'>".$medico->apellido1." ".$medico->apellido2.", ".$medico->nombre1." ".$medico->nombre2."</option>";
					}
					echo "</select>";
					echo "</span>";

					echo "<br>";
					echo "Interconsultante";
					echo "<input type=checkbox id=wchkmedint>";
					echo "</td>";
					echo "<td class='fila1'><input type='button' onclick='javascript:adicionarMedico();' value='Agregar >>' >";
					echo "</td>";
				}
				echo "<td class='fila2'><b>M&eacute;dicos tratantes actuales</b>";

				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */
				if($kardexActual->editable){
					if($kardexActual->editable && $kardexActual->esAnterior && $esFechaActual){
						//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
						$colTemporal = consultarMedicosTratantesTemporalKardex($whistoria,$wingreso,$wfecha);
						if(count($colTemporal) == 0){
							cargarMedicosAnteriorATemporal($whistoria,$wingreso,$wfecha,$fechaGrabacion);
						}
					}
					$cantMedicos = 0;
					$colMedicos = consultarMedicosTratantesTemporalKardex($whistoria,$wingreso,$wfecha);
					$cantMedicos = count($colMedicos);

					if($cantMedicos == 0){
						//1.2.1. Consulta de estructura definitiva
						$colMedicos = consultarMedicosTratantesDefinitivoKardex($whistoria,$wingreso,$wfecha);
						$cantMedicos = count($colMedicos);

						if($cantMedicos > 0  && $esFechaActual){
							//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
							cargarMedicosATemporal($whistoria,$wingreso,$wfecha,$wfecha);
						}
					}
				} else {
					$colMedicos = consultarMedicosTratantesDefinitivoKardex($whistoria,$wingreso,$wfecha);
					$cantMedicos = count($colMedicos);
				}
				//Listado de medicos
				echo "<table>";
				echo "<tr>";
				echo "<td>";
				if($kardexActual->editable){
					echo "<div id='cntMedicos'>";		//Contenedor de listado de medicos
					foreach($colMedicos as $medico){

						if(!empty($medico->id)){
							if($medico->interconsultante == 'on'){
								echo "<span id='Med$medico->tipoDocumento$medico->numeroDocumento$medico->codigoEspecialidad' class='vinculo'><a href='#null' onclick=javascript:quitarMedico('$medico->tipoDocumento-$medico->numeroDocumento-$medico->codigoEspecialidad');>$medico->nombre1 $medico->nombre2 $medico->apellido1 $medico->apellido2 (Interconsulta)</a><br/></span>";
							} else {
								echo "<span id='Med$medico->tipoDocumento$medico->numeroDocumento$medico->codigoEspecialidad' class='vinculo'><a href='#null' onclick=javascript:quitarMedico('$medico->tipoDocumento-$medico->numeroDocumento-$medico->codigoEspecialidad');>$medico->nombre1 $medico->nombre2 $medico->apellido1 $medico->apellido2</a><br/></span>";
							}
						}
					}
					echo "</div>";
				} else {
					foreach ($colMedicos as $medico) {
						if(!empty($medico->id)){
							echo $medico->nombre1." ".$medico->nombre2." ".$medico->apellido1." ".$medico->apellido2."<br>";
						}
					}
				}
				echo "</td>";
				echo "</tr>";
				echo "</table>";
				echo "</tr>";
				echo "<tr>";

				if($kardexActual->editable){
					echo "<td class='fila1'>Dieta</td>";
					echo "<td align=center class='fila2'><select name='wseldieta' id='wseldieta' class='seleccionNormal'>";
					$dietas = consultarDietas();

					echo "<option value=''>Seleccionar...</option>";
					foreach ($dietas as $dieta){
						echo "<option value='".$dieta->codigo."'>".$dieta->descripcion."</option>";
					}
					echo "</select>";

					echo "<td class='fila1'><input type='button' onclick='javascript:adicionarDieta();' value='Agregar >>' >";
					echo "</td>";
				}
				echo "<td class='fila2' align='center'><b>Dietas del paciente y observaciones nutricionales</b>";

				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */
				if($kardexActual->editable){
					if($kardexActual->editable && $kardexActual->esAnterior && $esFechaActual){
						//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
						$colTemporal = consultarDietasTemporalPaciente($whistoria,$wingreso,$wfecha);
						if(count($colTemporal) == 0){
							cargarDietasAnteriorATemporal($whistoria,$wingreso,$wfecha,$fechaGrabacion);
						}
					}

					//1. Consulta de estructura temporal.
					$colDietas = consultarDietasTemporalPaciente($whistoria,$wingreso,$wfecha);
					$cantDietas = count($colDietas);

					if($cantDietas == 0){
						//1.2.1. Consulta de estructura definitiva
						$colDietas = consultarDietasDefinitivoPaciente($whistoria,$wingreso,$wfecha);
						$cantDietas = count($colDietas);

						if($cantDietas > 0 && $esFechaActual){
							//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
							cargarDietasATemporal($whistoria,$wingreso,$wfecha,$wfecha);
						}
					}
				} else {
					$colDietas = consultarDietasDefinitivoPaciente($whistoria,$wingreso,$wfecha);
					$cantDietas = count($colDietas);
				}

				echo "<input type='hidden' name='colDietas' id='colDietas'>";

				//Listado de medicos
				echo "<table>";
				echo "<tr>";
				echo "<td>";
				echo "<div id='cntDietas'>"; //Contenedor de listado de dietas
				foreach ($colDietas as $dieta){
					if($kardexActual->editable){
						if(!empty($dieta->codigoDieta)){
							echo "<span id='Die$dieta->codigoDieta' class='vinculo'><a href='#null' onclick=javascript:quitarDieta('$dieta->codigoDieta');>$dieta->descripcionDieta</a><br/></span>";
						}
					} else {
						if(!empty($dieta->codigoDieta)){
							echo $dieta->descripcionDieta."<br>";
						}
					}
				}
				echo "</div>";
				echo "</td>";
				//Observaciones de las dietas
				echo "<td class=fila>";
				echo "<textarea name=txtObsDietas rows=5 cols=40>";
				echo $kardexActual->obsDietas;
				echo "</textarea>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";

				echo "</tr>";

				echo "</table>";
				echo "</td>";

				echo "</tr>";
				echo "</table>";

				echo "<br/>";
				echo "<br/>";

				//Los campos abiertos que se dejan de usar se sustituirán por hiddens
				echo "<input type=hidden name=txtPrepalta value=''>";
				echo "<input type=hidden name=txtMezclas value=''>";
				echo "<input type=hidden name=txTerapiaFisica value=''>";
				echo "<input type=hidden name=txRehabilitacionCardiaca value=''>";
				//echo "<input type=hidden name=txtConsentimientos value=''>";

				//Acciones complementarias
				echo '<span class="subtituloPagina2" align="center">';
				echo "Acciones complementarias y pendientes";
				echo "</span><br><br>";				
								
				//En esta parte del codigo se eliminan terapias, interconsultas y cirugias, ya que seran manejadas como examenes 
				//aparte en la pestaña de examenes. (Jonatan Lopez 29-07-2014)
				echo "<table>";					  
					echo "<tr>";
						//Arbol de acciones predeterminadas
						echo "<td colspan='1' rowspan='6' valign=top>";
						if($kardexActual->editable){
							echo "<div class=textoNormal align=left>";
							$colClases = consultarClases();

							echo "<ul class='simpleTree'>";
							echo "<li class='root' id='1'><span>Acciones predefinidas</span>";
							echo "<ul>";
							foreach ($colClases as $clase){
								if($clase->codigo != ''){
									echo "<li id='R$clase->codigo'><span>$clase->descripcion</span>";
									echo "<ul class='ajax'>";
									echo "<li id='ajax$clase->codigo'>{url:../../../include/movhos/kardex.inc.php?wemp_pmla=$wemp_pmla&tree_id=1&consultaAjaxKardex=24&nivelA=$clase->codigo&basedatos=$wbasedato}</li>";
									echo "</ul>";
									echo "</li>";
								}
							}
							echo "</ul>";
							echo "</li>";
							echo "</ul>";
							echo "</div>";
						}						
					   echo "</td>";
					   
					   //Cuidados de enfermeria
					   echo "<td colspan='1' rowspan='6' class='fila1'>Cuidados de enfermer&iacute;a";
						if($kardexActual->editable){
							echo "<textarea name=txCuidados id=txCuidados rows=60 cols=33 onFocus='javascript:expandirRama(this,\"R02\");'>";	//Mayo 16 de 2013
							echo $kardexActual->cuidadosEnfermeria;
							echo "</textarea>";
						} else {
							echo "<textarea name=txCuidados rows=60 cols=33 readonly>";	//Mayo 16 de 2013
							echo $kardexActual->cuidadosEnfermeria;
							echo "</textarea>";
						}
						echo "</td>";	
						
					   //Sondas y cateteres
						echo "<td colspan='1' rowspan='1' class='fila1'>Sondas, cateteres y drenes";
						if($kardexActual->editable){
							echo "<textarea name=txtSondas id=txtSondas rows=8 cols=100 onFocus='javascript:expandirRama(this,\"R01\");'>";
							echo $kardexActual->sondasCateteres;
							echo "</textarea>";
						} else {
							echo "<textarea name=txtSondas rows=8 cols=100 readonly>";
							echo $kardexActual->sondasCateteres;
							echo "</textarea>";
						}
						echo "</td>";
						
					echo "</tr>";						
					//Aislamientos
					echo "<tr>";						
						echo "<td colspan='1' rowspan='1' class='fila1'>Aislamientos";
						if($kardexActual->editable){
							echo "<textarea name=txAislamientos id=txAislamientos rows=8 cols=100 onFocus='javascript:expandirRama(this,\"R03\");'>";
							echo $kardexActual->aislamientos;
							echo "</textarea>";
						} else {
							echo "<textarea name=txAislamientos rows=8 cols=100 readonly>";
							echo $kardexActual->aislamientos;
							echo "</textarea>";
						}
						echo "</td>";
					echo "</tr>";
					//Curaciones
					echo "<tr>";						
						echo "<td class='fila1'><label for='wcodmed'>Curaciones";
						if($kardexActual->editable){
							echo "<textarea name=txtCuraciones id=txtCuraciones rows=8 cols=100 onFocus='javascript:expandirRama(this,\"R04\");'>";	//Mayo 16 de 2013
							echo $kardexActual->curaciones;
							echo "</textarea>";
						} else {
							echo "<textarea name=txtCuraciones id=txtCuraciones rows=8 cols=100 readonly>";	//Mayo 16 de 2013
							echo $kardexActual->curaciones;
							echo "</textarea>";
						}
						echo "</td>";
					echo "</tr>";
					
					//Decisiones
					if($kardexActual->consentimientos != ""){
					echo "<tr>";						
					   echo "<td colspan='1' rowspan='1' align=left class='fila1'>Decisiones";
						if($kardexActual->editable){
							echo "<textarea name=txtConsentimientos rows=8 cols=100 readonly>";
							echo $kardexActual->consentimientos;
							echo "</textarea>";
						} else {
							echo "<textarea name=txtConsentimientos rows=8 cols=100 readonly>";
							echo $kardexActual->consentimientos;
							echo "</textarea>";
						}
						echo "</td>";
					echo "</tr>";
					}
					
					//Medidas generales
					if($kardexActual->medidasGenerales != ""){
					echo "<tr>";						
					   echo "<td colspan='1' rowspan='1' align=left class='fila1'>Medidas generales";
						if($kardexActual->editable){
							echo "<textarea name=txMedidasGenerales rows=8 cols=100 readonly>";
							echo $kardexActual->medidasGenerales;
							echo "</textarea>";
						} else {
							echo "<textarea name=txMedidasGenerales rows=8 cols=100 readonly>";
							echo $kardexActual->medidasGenerales;
							echo "</textarea>";
						}
						echo "</td>";
					echo "</tr>";
					}
					
					//Observaciones generales
					echo "<tr>";						
					   echo "<td colspan='1' rowspan='1' align=center class='fila1'>Observaciones generales";
						if($kardexActual->editable){
							echo "<textarea name=txObservaciones rows=8 cols=100>";
							echo $kardexActual->observaciones;
							echo "</textarea>";
						} else {
							echo "<textarea name=txObservaciones rows=8 cols=100 readonly>";
							echo $kardexActual->observaciones;
							echo "</textarea>";
						}
						echo "</td>";
					echo "</tr>";
				echo "</table>";			
				echo "</div>";
			}

			//Pestaña Número 6
			if(strpos($usuario->pestanasKardex,"6") !== false || $usuario->pestanasKardex == "*"){
				echo "<div id='fragment-6'>";

				//Consulta de los valores actuales para el dextrometer
				$esquemaInsulina = consultarEsquemaInsulina($whistoria,$wingreso,$wfecha);
				$tieneEsquemaInsulinaAyer = false;
				if(!isset($esquemaInsulina->codigo)){
					$esquemaInsulina = consultarEsquemaInsulina($whistoria,$wingreso,$fechaAyer);
					$tieneEsquemaInsulinaAyer = true;
				}
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";

				//Insulina
				$colInsulinas = consultarArticulosEspeciales("",$articuloInsulina);
				echo "<tr><td class='fila1' width=170>Insulina</td>";
				echo "<td class='fila2' align='center'>";

				if($kardexActual->editable){
					echo "<select id='wdexins' class='seleccionNormal'>";
				} else {
					echo "<select id='wdexins' class='seleccionNormal' disabled>";
				}


				echo "<option value=''>Seleccione</option>";
				foreach ($colInsulinas as $insulina){
					if($esquemaInsulina->codigo == $insulina->codigo){
						echo "<option value='".$insulina->codigo."' selected>$insulina->codigo - $insulina->nombre</option>";
					}else{
						echo "<option value='".$insulina->codigo."'>$insulina->codigo - $insulina->nombre</option>";
					}
				}
				echo "</select>";
				echo "</td>";
				echo "</tr>";

				//Frecuencia del procedimiento
				echo "<tr><td class='fila1' width=270>Frecuencia de dextrometer</td>";
				echo "<td class='fila2' align='center'>";
				if($kardexActual->editable){
					echo "<select id='wdexfrecuencia' class='seleccionNormal' onChange='javascript:consultarEsquemaInsulina();'>";
				} else {
					echo "<select id='wdexfrecuencia' class='seleccionNormal' disabled>";
				}
				echo "<option value=''>Seleccione</option>";
				foreach ($colCondicionesSuministroInsulinas as $condicion){
					if($esquemaInsulina->frecuencia == $condicion->codigo){
						echo "<option value='".$condicion->codigo."' selected>$condicion->descripcion</option>";
					}else{
						echo "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
					}
				}
				echo "</select>";

				echo "</td>";
				echo "</tr>";

				//Esquema dextrometer de los predefinidos
				echo "<tr><td class='fila1' width=270>Esquema de insulina predefinido</td>";
				echo "<td class='fila2' align='center'>";

				echo "<input type=hidden name=whdexesquemaant id=whdexesquemaant value='btnQuitarEsquema'>";

				$colEsquemasInsulina = consultarMaestroEsquemasInsulina();
				if($kardexActual->editable){
					echo "<select id='wdexesquema' class='seleccionNormal' onChange='javascript:consultarEsquemaInsulina();'>";
				}else{
					echo "<select id='wdexesquema' class='seleccionNormal' onChange='javascript:consultarEsquemaInsulina();' disabled>";
				}
				echo "<option value=''>Seleccione</option>";
				foreach ($colEsquemasInsulina as $esquema){
					if($esquemaInsulina->codEsquema == $esquema->codigo ){
						echo "<option value='".$esquema->codigo."' selected>Esquema $esquema->codigo</option>";
					} else {
						echo "<option value='".$esquema->codigo."'>Esquema $esquema->codigo</option>";
					}
				}
				echo "</select>";

				echo "</td>";
				echo "</tr>";

				//Observaciones dextrometer
				echo "<tr><td class='fila1' colspan=2 align=center>Observaciones esquema dextrometer</td>";
				echo "<tr>";
				echo "<td colspan=2 class=fila2 align=center>";
				if($kardexActual->editable){
					echo "<textarea name=txtDextrometer rows=5 cols=40>";
					echo $kardexActual->dextrometer;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtDextrometer rows=5 cols=40 readonly>";
					echo $kardexActual->dextrometer;
					echo "</textarea>";
				}
				echo "</td>";
				echo "</tr>";

				if($kardexActual->editable){
					echo "<tr><td align=center colspan=4>";
					if($esquemaInsulina->codigo != '' || trim( $esquemaInsulina->frecuencia ) != '' || trim( $esquemaInsulina->codEsquema ) != '' ){
						echo "<br>";
						//echo "<input type=button id='btnEsquema' value='Seleccionar esquema' onclick='javascript:grabarEsquemaDextrometer();'>&nbsp;|&nbsp;";
						echo "<input type=button id='btnQuitarEsquema' value='Quitar esquema' onclick='javascript:quitarEsquemaDextrometer();'>";
					} else {
						echo "<br>";
						//echo "<input type=button id='btnEsquema' value='Seleccionar esquema' onclick='javascript:grabarEsquemaDextrometer();' disabled>&nbsp;|&nbsp;";
						echo "<input type=button id='btnQuitarEsquema' value='Quitar esquema' onclick='javascript:quitarEsquemaDextrometer();' disabled>";
					}
					echo "</td></tr>";
				}
				echo "</table>";

				//Consulta del esquema actual si existe
				$intervalosEsquema = consultarIntervalosDextrometer($whistoria,$wingreso,$wfecha);

				if( $tieneEsquemaInsulinaAyer && count($intervalosEsquema) == 0){
					$intervalosEsquema = consultarIntervalosDextrometer($whistoria,$wingreso,$fechaAyer);
				}

				if(count($intervalosEsquema) > 0){
					echo "<br>";
					echo "<center>";
					echo '<span class="subtituloPagina2">';
					echo 'Esquema actual dextrometer';
					echo "</span>";
					echo "</center>";
					echo "<br>";

					echo "<div id='cntEsquemaActual'>";
					echo "<table align=center>";
					echo "<tr class=encabezadoTabla align=center><td>M&iacute;nimo</td><td>M&aacute;ximo</td><td>Dosis</td><td>V&iacute;a</td><td>Observaciones</td></tr>";

					$clase = "fila2";
					foreach ($intervalosEsquema as $intervalo){
						if($intervalo->minimo != ''){
							if($clase=="fila1"){
								$clase = "fila2";
							} else {
								$clase = "fila1";
							}

							echo "<tr class=$clase align=center>";
							echo "<td>$intervalo->minimo</td>";
							echo "<td>$intervalo->maximo</td>";
							echo "<td>";
							echo "$intervalo->dosis ";

							foreach ($colUnidades as $unidad){
								if($unidad->codigo == $intervalo->unidadDosis){
									echo ucfirst(strtolower($unidad->descripcion));
								}
							}
							echo "</td>";

							echo "<td>";
							foreach ($colVias as $via){
								if($via->codigo == $intervalo->via){
									echo $via->descripcion;
								}
							}
							echo "</td>";
							echo "<td align='left'>$intervalo->observaciones</td>";
						}
					}
					echo "</table>";
					echo "</div>";
				}

				//Con ajax consulto la tabla de intervalos
				echo "<br>";
				echo "<div id=cntEsquema align=center>";
				echo "</div>";

				echo "</div>";
			}

			//Pestaña Numero 7
			if(strpos($usuario->pestanasKardex,"7") !== false || $usuario->pestanasKardex == "*"){
				echo "<div id='fragment-7'>";
				// if($mostrarAuditoria){
					// echo "<input type='hidden' name='wauditoria' id='wauditoria' value='1'>";

					// echo "<table align='center' border=0>";

					// echo "<tr align='center' class='encabezadoTabla'>";
					// echo "<td>Usuario</td>";
					// echo "<td>Fecha y hora</td>";
					// echo "<td>Mensaje</td>";
					// echo "<td>Referencia</td>";
					// echo "</tr>";

					// $historialCambios = consultarHistorialCambiosKardex( $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha );

					// $cont1 = 0;
					// foreach($historialCambios as $historia){
						// if($cont1 % 2 == 0){
							// echo "<tr class='fila1'>";
						// } else {
							// echo "<tr class='fila2'>";
						// }

						// echo "<td>$historia->usuario</td>";
						// echo "<td>$historia->fecha - $historia->hora</td>";
						// echo "<td>$historia->mensaje</td>";
						// echo "<td>$historia->descripcion</td>";

						// echo "</tr>";

						// $cont1++;
					// }

					// echo "</table>";
				// }
				echo "</div>";
			}

			if(strpos($usuario->pestanasKardex,"8") !== false || $usuario->pestanasKardex == "*"){
				echo "<div id='fragment-8'>";

				$elementosActuales = 0;
				$colDetalle = array();

				if($kardexActual->editable){
					echo "<div align=center><input type='button' value='Movimiento de articulos' onclick='javascript:abrirMovimientoArticulos(\"A,U\");'></div>";
				}

				vista_generarConvencion();		//Genera la muestra de convenciones de los articulos

				//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
				realizarMovimientosArticulos($kardexActual, $paciente, $esFechaActual, $wfecha, $fechaGrabacion, $protocoloAnalgesia, $elementosActuales, $colDetalle);

				//Subtitulo
				echo '<span class="subtituloPagina2">';
				echo 'Analgesia';
				echo "</span>";
				echo "<br>";
				echo "<br>";

				//Despliega la vista de la tabla de articulos para el protocolo normal
				vista_desplegarListaArticulos($colDetalle,$elementosActuales,$protocoloAnalgesia,$kardexActual->editable,$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro);

				//Detalle de medicamentos anteriores
				$colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$protocoloAnalgesia);
				$cantidadElementosAnteriores = count($colDetalleAnteriorKardex);

				if($cantidadElementosAnteriores > 0){
					vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,$protocoloAnalgesia,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias);
				} else {
					echo '<br><span class="subtituloPagina2" align="center">';
					echo "No hay analgesias anteriores";
					echo "</span>";
					echo "<div id='medAnt'>";
					echo "</div>";
				}

				echo "<br>";
				echo "<br>";

				//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
				realizarMovimientosArticulos($kardexActual, $paciente, $esFechaActual, $wfecha, $fechaGrabacion, $protocoloNutricion, $elementosActuales, $colDetalle);

				//Subtitulo
				echo '<span class="subtituloPagina2">';
				echo 'Nutriciones';
				echo "</span>";
				echo "<br>";
				echo "<br>";

				//Despliega la vista de la tabla de articulos para el protocolo normal
				vista_desplegarListaArticulos($colDetalle,$elementosActuales,$protocoloNutricion,$kardexActual->editable,$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro);

				//Detalle de medicamentos anteriores
				$colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, $protocoloNutricion);
				$cantidadElementosAnteriores = count($colDetalleAnteriorKardex);

				if($cantidadElementosAnteriores > 0){
					vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,$protocoloNutricion,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias);
				} else {
					echo '<br><span class="subtituloPagina2" align="center">';
					echo "No hay nutriciones anteriores";
					echo "</span>";
					echo "<div id='medAnt'>";
					echo "</div>";
				}
				echo "</div>";
			}
			if( strpos($usuario->pestanasKardex,"9") !== false || $usuario->pestanasKardex == "*" ){
				echo "<div id='fragment-9'>";

				$elementosActuales = 0;
				$colDetalle = array();

				if($kardexActual->editable){
					echo "<div align=center><input type='button' value='Movimiento de articulos' onclick='javascript:abrirMovimientoArticulos(\"Q\");'></div>";
				}

				vista_generarConvencion();		//Genera la muestra de convenciones de los articulos

				//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
				realizarMovimientosArticulos($kardexActual, $paciente, $esFechaActual, $wfecha, $fechaGrabacion, $protocoloQuimioterapia, $elementosActuales, $colDetalle);

				//Despliega la vista de la tabla de articulos para el protocolo normal
				vista_desplegarListaArticulos($colDetalle,$elementosActuales,$protocoloQuimioterapia,$kardexActual->editable,$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro);

				//Detalle de medicamentos anteriores
				$colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, $protocoloQuimioterapia);
				$cantidadElementosAnteriores = count($colDetalleAnteriorKardex);

				if($cantidadElementosAnteriores > 0){
					vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,$protocoloQuimioterapia,$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias);
				} else {
					echo '<br><span class="subtituloPagina2" align="center">';
					echo "No hay medicamentos de quimioterapia anteriores";
					echo "</span>";
					echo "<div id='medAnt'>";
					echo "</div>";
				}
				echo "</div>";
			}

			if($activarPestanas){
				foreach($pestanas as $pestana){
					echo "<div id='frag-$pestana->codigoPestana'>";
					echo $pestana->codigoPestana;
					echo "</div>";
				}
			}

			echo "<center>";
			if($kardexActual->editable){

				if($kardexActual->confirmado == "on"){
					echo "<span class='textoMedio'>Confirmar kardex e iniciar dispensación</span></label><input type='checkbox' name='wconfdisp' id='wconfdisp' checked>&nbsp;|&nbsp;";
				} else {
					echo "<span class='textoMedio'>Confirmar kardex e iniciar dispensación</span></label><input type='checkbox' name='wconfdisp' id='wconfdisp'>&nbsp;|&nbsp;";
				}

				echo "<div style='display:none'><input type='checkbox' name='wconfdisp' id='wconfdisp' checked></div>";
				echo "<input type='button' value='Grabar kardex' onclick='javascript:grabarKardex();' ><br/>";
				// | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();' >
//				echo "<br/><span class=fila2>IMPORTANTE:: Recuerde si hace una consulta o cambio en el kardex del dia y no hace click sobre 'Grabar kardex', no se traerán los cambios realizados para el dia siguiente</span><br/>";

				/****************************************************************************************************************
				 * Octubre 18 de 2012
				 ****************************************************************************************************************/
				if( !empty($et) && $et == 'on' ){
					echo "<input type='hidden' name='et' id='et' value='$et'>";
				}
				/****************************************************************************************************************/

			} else {
				if( !empty($et) && $et == 'on' ){
					echo "<br/><input type='button' value='Regresar' onclick='javascript:cerrarVentana();'><br/>";
				}
				else if( !empty($et) && $et == 'hce' ){
					echo "<br/><input type='button' value='Regresar' onclick='window.parent.cerrarModal();'><br/>";
				}
				else{
					echo "<br/><input type='button' value='Regresar' onclick='javascript:inicio(\"$paciente->servicioActual\");'><br/>";
				}
			}
			echo "</center>";
			echo "</div>";

			echo "</br>";
			echo "</br>";

			break;
		case 'c':		//Actualización del kardex
			//$usuario->centroCostos
			if(!$usuario->esUsuarioLactario){
				if($usuario->esUsuarioCM || $usuario->esUsuarioSF){
					$usuario->centroCostosGrabacion = "*";
				}
			}
//			echo "Centro costos grabacion modificado $usuario->centroCostosGrabacion";
			
			$mensaje = "";

			$kardexGrabar = new kardexDTO();

			//Captura de parametros. Encabezado del kardex
			$kardexGrabar->historia = $whistoria;
			$kardexGrabar->ingreso = $ingreso;
			$kardexGrabar->fechaCreacion = $wfecha;
			$kardexGrabar->horaCreacion = date("H:i:s");
			$kardexGrabar->fechaGrabacion = $wfechagrabacion;
			$kardexGrabar->usuario = $wuser;
			$kardexGrabar->confirmado = $confirmado;
			$kardexGrabar->esPrimerKardex = $primerKardex;
			$kardexGrabar->rutaOrdenMedica = $rutaOrdenMedica;
			$kardexGrabar->centroCostos = $usuario->centroCostosGrabacion;
			$kardexGrabar->usuarioQueModifica = $usuario->codigo;
			$kardexGrabar->noAcumulaSaldoDispensacion = $wkardexnoacumula;

			if(strpos($usuario->pestanasKardex,"1") !== false || $usuario->pestanasKardex == "*"){
				$kardexGrabar->talla = $txTalla;
				$kardexGrabar->peso = $txPeso;
				$kardexGrabar->diagnostico = str_replace("|",chr(13),$txDiag);
				$kardexGrabar->antecedentesAlergicos = str_replace("|",chr(13),$txAlergias);
				$kardexGrabar->antecedentesPersonales = str_replace("|",chr(13),$txAntecedentesPersonales);
			}

			//Truco (los pipes |) los reemplazo a saltos de linea
			if(strpos($usuario->pestanasKardex,"5") !== false || $usuario->pestanasKardex == "*"){
				$kardexGrabar->observaciones = str_replace("|",chr(13),$txObservaciones);
				$kardexGrabar->cuidadosEnfermeria = str_replace("|",chr(13),$txCuidados);
				$kardexGrabar->terapiaRespiratoria = str_replace("|",chr(13),$txTerapia);
				$kardexGrabar->curaciones = str_replace("|",chr(13),$txtCuraciones);
				$kardexGrabar->sondasCateteres = str_replace("|",chr(13),$txtSondas);
				$kardexGrabar->preparacionAlta = str_replace("|",chr(13),$txtPrepalta);
				$kardexGrabar->interconsulta = str_replace("|",chr(13),$txtInterconsulta);
				$kardexGrabar->consentimientos = str_replace("|",chr(13),$txtConsentimientos);
				$kardexGrabar->obsDietas = str_replace("|",chr(13),$txtObsDietas);
				$kardexGrabar->mezclas = str_replace("|",chr(13),$txtMezclas);
				$kardexGrabar->dextrometer = str_replace("|",chr(13),$txtDextrometer);
				$kardexGrabar->cirugiasPendientes = str_replace("|",chr(13),$txtCirugiasPendientes);
				$kardexGrabar->terapiaFisica = str_replace("|",chr(13),$txTerapiaFisica);
				$kardexGrabar->rehabilitacionCardiaca = str_replace("|",chr(13),$txRehabilitacionCardiaca);
				$kardexGrabar->aislamientos = str_replace("|",chr(13),$txAislamientos);
			}
			
			$wtieneOrdenes = tieneOrdenes( $conex, $wbasedato, $whistoria, $wingreso, $wfecha, $wsservicio );
			$wpactieneOrdenes = $wtieneOrdenes ? "on" : "off";
			
			$kardexGrabar->ordenes = $wpactieneOrdenes;
			
			echo "<input type='hidden' name='whistoria' id='whistoria' value='$kardexGrabar->historia'>";
			echo "<input type='hidden' name='wingreso' id='wingreso' value='$kardexGrabar->ingreso'>";
			echo "<input type='hidden' name='wfecha' id='wfecha' value='$wfecha'>";
			echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='0'/>";

			if(!existeEncabezadoKardex($whistoria,$ingreso,$wfecha)){
				crearKardex($kardexGrabar);
				$mensaje = "El kardex ha sido creado con éxito";
			} else {
				//Actualiza SOLO encabezado
				actualizarKardex($kardexGrabar,$usuario->pestanasKardex);
				$mensaje = "El kardex ha sido actualizado con éxito";
			}

			$esPrimerKardex = false;
			if($primerKardex == "S"){
				$esPrimerKardex = true;
			}

			//Carga de temporal a definitivo de los componentes del kardex, para todos, debe borrarse lo anterior

			//Articulos
			cargarArticulosADefinitivo($whistoria,$ingreso,$wfechagrabacion,$esPrimerKardex, $wccograbacion);

//			if($usuario->centroCostosHospitalario){
				//Examenes
				cargarExamenesADefinitivo($whistoria,$ingreso,$wfechagrabacion);

				//Infusiones
				cargarInfusionesADefinitivo($whistoria,$ingreso,$wfechagrabacion);

				//Medicos
				cargarMedicoADefinitivo($whistoria,$ingreso,$wfechagrabacion);

				//Dietas
				cargarDietasADefinitivo($whistoria,$ingreso,$wfechagrabacion);
//			}



			echo "</form>";

			//Si el kardex se ha grabado este hidden se cargará en las paginas
			echo "<input type='hidden' name='whgrabado' value='on'>";



			/****************************************************************************************************************
			 * Octubre 18 de 2012
			 ****************************************************************************************************************/

			if( !empty($et) && $et == 'on' ){

				echo "<input type='hidden' name='et' id='et' value='$et'>";
				funcionJavascript("inicio(\"$wsservicio&et=on\");");
			}
			else{

				//ECHO ".......quitar esta linea"; return;
				// if($kardexGrabar->confirmado == "on"){
					funcionJavascript("inicio(\"$wsservicio\");");
				// } else {
					// funcionJavascript("consultarKardex();");
				// }
			}
			/****************************************************************************************************************/




			break;
		default:  //Muestra la pantalla inicial

			if( !empty($et) && $et == 'on' ){
				?>
					<script>
						window.close();
					</script>
				<?php
			}

			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Ingrese los parámetros de consulta';
			echo "</span>";
			echo "<br>";
			echo "<br>";

			//Servicio
			$centrosCostosHospitalarios = centrosCostosHospitalariosOcupados();
			echo "<tr><td class='fila1' width=170>Servicio</td>";
			echo "<td class='fila2' align='center' width=170>";
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
			echo "</td>";
			echo "</tr>";

			//Habitacion - paciente
			echo "<tr>";
			echo "<td align='center' colspan=2>";
			echo "<div id='cntHabitacion'>";			
			
			if(isset($wsservicio) && !empty($wsservicio)){
				echo consultarHabitacionPacienteServicio($wbasedato,$wsservicio);
			}

			echo "</div>";
			echo "</tr>";

			//Por Historia clinica
			echo "<tr><td class='fila1' width=170>Historia clínica</td>";
			echo "<td class='fila2' align='center'>";
			echo "<INPUT TYPE='text' id='wthistoria' NAME='whistoria' value='' SIZE=10 onKeyPress='return teclaEnterEntero(event,"."\"consultarKardex();\");' class='textoNormal'>";
			echo "</td>";
			echo "</tr>";

			//Por ingreso
			if(isset($editable) && $editable == "off"){
				echo "<tr><td class='fila1' width=170>Ingreso</td>";
				echo "<td class='fila2' align='center'>";
				echo "<INPUT TYPE='text' id='wingreso' NAME='wingreso' value='' SIZE=10 onKeyPress='return teclaEnterEntero(event,"."\"consultarKardex();\");' class='textoNormal'>";
				echo "</td>";
				echo "</tr>";
			}
			
			//Por fecha generacion kardex
			echo "<tr>";
			echo "<td class='fila1'>Fecha</td>";
			echo "<td class='fila2' align='center'>";
			campoFecha("wfecha");
			echo "</td></tr>";

			//Si la fecha del servidor difiere de la del equipo donde se esta digitando el kardex
			$fechaActual = date("Y-m-d");
			$horaActual = date("H:i:s");

			funcionJavascript("validarFechayHoraLocal('".$fechaActual."','".$horaActual."');");

			echo "<tr><td align=center colspan=4><br>";
			if(isset($editable) && $editable == "off"){
				echo "<input type=button value='Consultar' onclick='javascript:consultarKardex();'>&nbsp;|&nbsp;";
			} else {
				echo "<input type=button value='Consultar o generar' onclick='javascript:consultarKardex();'>&nbsp;|&nbsp;";
			}
			echo "<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";
			echo "</table>";
			
			//Consultar los servicios que tengan las ordenes activas.	
			$sql_cco = "SELECT Ccocod
						  FROM ".$wbasedato."_000011
						 WHERE Ccoior = 'on'";			   
			$res_cco = mysql_query( $sql_cco, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
			
			echo "<table align=center>";
			
			if( !$usuario->esUsuarioLactario ){
				while($row_cco = mysql_fetch_array($res_cco)){
					
					echo "<tr>";
					echo "<td>";
					echo "<input type=hidden id='ccocod_".$row_cco['Ccocod']."' value='".$row_cco['Ccocod']."'>";
					echo "</td>";
					echo "<tr>";
					
				}
			}
			
			echo "</table>";
			
			break;
	}
	liberarConexionBD($conex);

	// echo "<div id='dvTitle' style='display:none;position:absolute'>Se calcula a partir de<br>la fecha y hora de inicio</div>";
	echo "<div id='dvTitle' style='display:none;position:absolute'>Los días de tratamiento serán<br>convertidos a dosis máxima.<br>Se calcula a partir de<br>la fecha y hora de inicio</div>";
	echo "<div id='dvTitleDMA' style='display:none;position:absolute'>Se calcula a partir de<br>la fecha y hora de inicio</div>";
	echo "<frame id='frTitle' style='display:none;position:absolute'></frame>";
}
?>
<script type="text/javascript">
var elementosDetalle = 0, elementosAnalgesia = 0, elementosNutricion = 0, elementosQuimioterapia = 0, cuentaExamenes = 0, cuentaInfusiones = 0; if(document.forms.forma.elementosKardex)elementosDetalle = document.forms.forma.elementosKardex.value; if(document.forms.forma.elementosAnalgesia)elementosAnalgesia = document.forms.forma.elementosAnalgesia.value; if(document.forms.forma.elementosNutricion)elementosNutricion = document.forms.forma.elementosNutricion.value; if(document.forms.forma.elementosQuimioterapia)elementosQuimioterapia = document.forms.forma.elementosQuimioterapia.value; if(document.forms.forma.cuentaExamenes)cuentaExamenes = document.forms.forma.cuentaExamenes.value; if(document.forms.forma.cuentaInfusiones)cuentaInfusiones = document.forms.forma.cuentaInfusiones.value; if(document.getElementById("fixeddiv")) { fixedMenuId = "fixeddiv"; var fixedMenu = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId) : document.all ? document.all[fixedMenuId] : document.layers[fixedMenuId]}; fixedMenu.computeShifts = function() { fixedMenu.shiftX = fixedMenu.hasInner ? pageXOffset : fixedMenu.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu.shiftX += fixedMenu.targetLeft > 0 ? fixedMenu.targetLeft : (fixedMenu.hasElement ? document.documentElement.clientWidth : fixedMenu.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu.targetRight - fixedMenu.menu.offsetWidth; fixedMenu.shiftY = fixedMenu.hasInner ? pageYOffset : fixedMenu.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu.shiftY += fixedMenu.targetTop > 0 ? fixedMenu.targetTop : (fixedMenu.hasElement ? document.documentElement.clientHeight : fixedMenu.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu.targetBottom - fixedMenu.menu.offsetHeight }; fixedMenu.moveMenu = function() { fixedMenu.computeShifts(); if(fixedMenu.currentX != fixedMenu.shiftX || fixedMenu.currentY != fixedMenu.shiftY) { fixedMenu.currentX = fixedMenu.shiftX; fixedMenu.currentY = fixedMenu.shiftY; if(document.layers) { fixedMenu.menu.left = fixedMenu.currentX; fixedMenu.menu.top = fixedMenu.currentY }else { fixedMenu.menu.style.left = fixedMenu.currentX + "px"; fixedMenu.menu.style.top = fixedMenu.currentY + "px" } }fixedMenu.menu.style.right = ""; fixedMenu.menu.style.bottom = "" }; fixedMenu.floatMenu = function() { fixedMenu.moveMenu(); setTimeout("fixedMenu.floatMenu()", 20) }; fixedMenu.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu.init = function() { if(fixedMenu.supportsFixed())fixedMenu.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu.menu : fixedMenu.menu.style; fixedMenu.targetLeft = parseInt(a.left); fixedMenu.targetTop = parseInt(a.top); fixedMenu.targetRight = parseInt(a.right); fixedMenu.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu.addEvent(window, "onscroll", fixedMenu.moveMenu); fixedMenu.floatMenu() } }; fixedMenu.addEvent(window, "onload", fixedMenu.init); fixedMenu.hide = function() { fixedMenu.menu.style.display = "none"; return false }; fixedMenu.show = function() { fixedMenu.menu.style.display = "block"; return false } }if(document.getElementById("fixeddiv2")) { fixedMenuId2 = "fixeddiv2"; var fixedMenu2 = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId2) : document.all ? document.all[fixedMenuId2] : document.layers[fixedMenuId2]}; fixedMenu2.computeShifts = function() { fixedMenu2.shiftX = fixedMenu2.hasInner ? pageXOffset : fixedMenu2.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu2.shiftX += fixedMenu2.targetLeft > 0 ? fixedMenu2.targetLeft : (fixedMenu2.hasElement ? document.documentElement.clientWidth : fixedMenu2.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu2.targetRight - fixedMenu2.menu.offsetWidth; fixedMenu2.shiftY = fixedMenu2.hasInner ? pageYOffset : fixedMenu2.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu2.shiftY += fixedMenu2.targetTop > 0 ? fixedMenu2.targetTop : (fixedMenu2.hasElement ? document.documentElement.clientHeight : fixedMenu2.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu2.targetBottom - fixedMenu2.menu.offsetHeight }; fixedMenu2.moveMenu = function() { fixedMenu2.computeShifts(); if(fixedMenu2.currentX != fixedMenu2.shiftX || fixedMenu2.currentY != fixedMenu2.shiftY) { fixedMenu2.currentX = fixedMenu2.shiftX; fixedMenu2.currentY = fixedMenu2.shiftY; if(document.layers) { fixedMenu2.menu.left = fixedMenu2.currentX; fixedMenu2.menu.top = fixedMenu2.currentY }else { fixedMenu2.menu.style.left = fixedMenu2.currentX + "px"; fixedMenu2.menu.style.top = fixedMenu2.currentY + "px" } }fixedMenu2.menu.style.right = ""; fixedMenu2.menu.style.bottom = "" }; fixedMenu2.floatMenu = function() { fixedMenu2.moveMenu(); setTimeout("fixedMenu2.floatMenu()", 20) }; fixedMenu2.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu2.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu2.init = function() { if(fixedMenu2.supportsFixed())fixedMenu2.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu2.menu : fixedMenu2.menu.style; fixedMenu2.targetLeft = parseInt(a.left); fixedMenu2.targetTop = parseInt(a.top); fixedMenu2.targetRight = parseInt(a.right); fixedMenu2.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu2.addEvent(window, "onscroll", fixedMenu2.moveMenu); fixedMenu2.floatMenu() } }; fixedMenu2.addEvent(window, "onload", fixedMenu2.init); fixedMenu2.hide = function() { if(fixedMenu2.menu.style.display != "none")fixedMenu2.menu.style.display = "none"; return false }; fixedMenu2.show = function(a) { document.getElementById("wtipoprot"); var b = 0; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)document.forms.forma.wtipoprot[b].disabled = true; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)if(a.indexOf(document.forms.forma.wtipoprot[b].value) != -1) { document.forms.forma.wtipoprot[b].checked = true; document.forms.forma.wtipoprot[b].disabled = false }fixedMenu2.menu.style.display = "block"; return false } };
</script>
</body>
</html>