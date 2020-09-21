<?php
include_once("conex.php");  header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<html>
<head>
<title>HCE - [ORDENES]</title>

<script>window.onerror=null</script>

<!-- JQUERY para los tabs -->
<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
<link type="text/css" href="../../../include/root/jquery.simpletree.css" rel="stylesheet" />
<link type='text/css' href='../../../include/root/matrix.css' rel='stylesheet'>		<!-- HCE -->

<link type='text/css' href='HCE.css' rel='stylesheet'>		<!-- HCE -->
<script type='text/javascript' src='HCE.js' ></script>		<!-- HCE -->

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>	<!-- Acordeon -->
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<!-- <script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script> -->	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type="text/javascript" src="../../../include/root/ui.datepicker.js"></script>

<!-- Fin JQUERY para los tabs -->

<!-- Include de codigo javascript propio de mensajeria Kardex -->
<script type="text/javascript" src="../../../include/movhos/mensajeriaKardex.js"></script>
<script type="text/javascript" src="./generarCTCprocedimientos.js"></script>
<script type="text/javascript" src="./generarCTCOrdenesIDC.js"></script>

<script type="text/javascript" src="../../../include/movhos/alertas.js?v=<?=md5_file('../../../include/movhos/alertas.js');?>"></script>

<script type="text/javascript">
	$(document).ready(function(){  inicializarJquery();  });
</script>

<!-- Include de codigo javascript propio de la orden -->
<script type="text/javascript" src="ordenesidc.js?v=<?=md5_file('ordenesidc.js');?>"></script>

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
.tituloFamilia                     
{
     background-color: #B0C4DE; 
	 font-family: verdana;
	 font-size: 10pt;
	 overflow: hidden;
	 text-transform: uppercase;
	 font-weight: bold;
	 height: 21px;
	 border-top-color: #2A5DB0;
	 border-top-width: 1px;
	 border-left-color: #2A5DB0;
	 border-left-width: 1px;
	 border-right-color: #2A5DB0;
	 border-bottom-color: #2A5DB0;
	 border-bottom-width: 1px;
	 margin: 2pt;
}
.esCompuesta                                    
{
     background-color: #6495ED;
     color: #000000;
     font-size: 10pt;
}

td{
	font-size: 10pt;
}
.opacar img {filter:alpha(opacity=50);-moz-opacity: 0.5;opacity: 0.5;}
.aclarar img {filter:alpha(opacity=100)3000;-moz-opacity: 1.0;opacity: 1.0;}

input.promptbox { border:1 solid #0000FF; background-color:white;width:90%; }

</style>

</head>

<body>
<?php
/*BS'D
 * CONSULTA Y GENERACION DE ORDENES PARA HCE
 * Autor: Mauricio Sánchez Castaño.
 * /************************************************
	ADVERTENCIA: 
		NO USE funciones que activen el evento onBeforeUnload en lo operativo, ya que esto dispara la grabacion del orden.  Ej:
		->javascript:void(0) en los links use mejor href='#null'
	*************************************************
	*
	* Modificaciones:
	*
	* 	Septiembre 21 de 2020	(Edwin MG) -> Si la variable $fechaAyer es vacia, se deja por defecto con valor 0000-00-00, esto para que las consultas con fecha no generen error.
	*										  Esto es debido al cambio de BD realizada el 19 de septiembre
	* 	Junio 09 de 2020		(Edwin MG) -> Se hacen cambios varios para poder visualizar las ordenes en modo de consulta y poder imprimir las ordenes de medicamentos o de estudios en este modo
	* 	Abril 16 de 2020		(Edwin MG) -> Al dar click sobre cerrar ventana o grabar las ordenes regresa a HCE
	* 	Octubre 04 de 2017		(Edwin MG) -> Se deshabilita el lenguage américas idc
	* 	Diciembre 05 de 2016				  Se agrega programa de alergias y alertas y se comenta en la pestaña de Informacion General los antecedentes alergicos
	*	Agosto 22 de 2014		(Edwin MG) -> Se crea función para que baje todos los articulos que se encuentren en la tabla temporal(movhos_000060) antes de la última fecha
	*										  en que tenga kardex y los pasa a la tabla definitiva.
	*	Mayo 5 de 2014			(Edwin MG) -> Se realizan cambios para que al momento de cerrar ordenes se cree el encabezado de las ordenes (mhosidc_000053)
	*	Abril 10 de 2014		(Edwin MG) -> Se realizan cambios varios para el manejo de tabla temporal para los procedimientos
	*	Marzo 12 de 2014		(Edwin MG) -> Se agrega campo oculto para las ordenes de procedimientos que indican el id del formulario firmado de HCE
	*	Mayo 24 de 2012			(Edwin MG) -> Se homologa Ordenes con Kardex
	*	Febrero 22 de 2011		(Edwin MG) -> Se corrige funcion obtenerVectorAplicacionMedicamentos
	*	Febrero 17 de 2011		(Edwin MG) -> Si esta en proceso de tralasado y fue entregao desde cirugia, solo pueden hacer el kardex del paciente
	*										  personal de cirguia o urgencias
	*	Febrero 15 de 2011		(Edwin MG) -> Si el paciente se encuentra en traslado no se puede realizar el kardex
	* 	2010-06-09:  (Msanchez):  Creado
	*
 */

if( !empty($hce) ){
	$wfecha = date( "Y-m-d" );
	$waccion = 'b';
}

$usuarioValidado = true;
$wactualiz = " Diciembre 05 de 2016";

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false; 	
} else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;	
}

/*****************************
 * INCLUDES
 ****************************/
include_once("./ordenesidc.inc.php");

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$winstitucion = $institucion->nombre;
encabezado("Ordenes médicas",$wactualiz,$institucion->baseDeDatos);
	
if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";
	
	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} else {
	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	//Historial de modificaciones
	$mostrarAuditoria = true;

	//Fecha grabacion
	$fechaGrabacion = date("Y-m-d");
	
	//Base de datos, se generaliza de generar orden
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

	//Consulta de la información del usuario
//	$usuario = consultarUsuarioKardex($wuser);
//	var_dump($usuario);



 /*************************************************************************
  * FUNCIONES
  *************************************************************************/
     
  function mostrar_resultado($wresultado)
     {
	  global $whce;
	  global $conex;
	  
	  $wmensaje=explode("@",$wresultado);       		 			 //Separo el encabezado del resto de la información, el '@' indica fin del encabezado
		 
	  //===========================================================================================================================
	  //* * * Encabezado * * *
	  //===========================================================================================================================
	  $wencabezado=explode("!",$wmensaje[0]);            			 //Separo las filas del encabezado, '!' indica fin de fila <tr>
	  
	  //echo "<center><table border=0>";		// Se comenta porque el fin de la tabla ya está definido donde se llama la función	// 2012-06-26
	  for ($k=0;$k < count($wencabezado); $k++)
	     {
		   $wnegrilla=false; 
		   if (($k+1)==count($wencabezado))                         //Si (k+1) == count, es porque es el nombre del Estudio
		      {
		       $wnegrilla=true;
		      } 
		     
		   $wlinea=explode(":",$wencabezado[$k]);
		   if (!isset($wlinea[1]))                                  //Si no hay ':' es porque es un Titulo
		      $wlinea=$wencabezado[$k];
		     else
		       $wlinea="<b>".$wlinea[0].":</b>&nbsp".$wlinea[1];
		      
		   echo "<tr>";
		   if ($wnegrilla)
		      echo "<td align=center colspan=3 class=tipoTA>".$wlinea."</td>";
		     else
		        echo "<td align=center colspan=3>".$wlinea."</td>"; 
		   echo "</tr>";
		 }
	  //===========================================================================================================================
	  
		 
	  //===========================================================================================================================
	  //* * * Detalle * * *
	  //===========================================================================================================================
	  echo "<tr class='encabezadoTabla'>";
	  echo "<th align=center>Descripción</th>";
	  echo "<th align=center>Valor Resultado</th>";
	  echo "<th align=center>Valor de Referencia</th>";
	  echo "</tr>";
	  
	  $wfilas=explode("!",$wmensaje[1]);        		 			 //La información diferente al encabezado, la separo en filas, como si fuera un registro
		 
	  for ($i=0;$i<count($wfilas);$i++)           
	     {
	      $wcolumnas=explode("$",$wfilas[$i]);     		 			 //Cada fila o registro lo separo en columnas o campos, '$' indica fin del campo
		  
	      if (isset($wclase) and $wclase == "fila1")
		    $wclase = "fila2";
		   else 
		      $wclase = "fila1";
	      
	      echo "<tr class='".$wclase."'>"; 
		  for ($j=0;$j<count($wcolumnas);$j++)
		     {
			  if (count($wcolumnas) == 1)                            //Si entra aca es porque es un SubTitulo
	             echo "<td align=left colspan=3 class='encabezadoTabla'><b>".$wcolumnas[$j]."</b></td>";          //Imprimo cada columna
	            else
	               echo "<td align=center colspan=3>".$wcolumnas[$j]."</td>";  //Imprimo cada columna 
		     }
	      echo "</tr>";	
	     }
	  // echo "</table></center>";	// Se comenta porque el fin de la tabla ya está definido donde se llama la función	// 2012-06-26
	  //===========================================================================================================================     
	 }    
  	 
     
  function traer_resultado($wtabla, $wcco, $whis, $wing, $wnor, $wite)
     {
	  global $whce;
	  global $conex;
	  
	  $q = " SELECT hl7rdo "
	      ."   FROM hceidc_".$wtabla
	      ."  WHERE hl7his = '".$whis."'"
	      ."    AND hl7ing = '".$wing."'"
	      ."    AND hl7des = '".trim($wcco)."'"
	      ."    AND hl7nor = ".$wnor
	      ."    AND hl7nit = '".$wnor."-".$wite."'"
	      ."    AND hl7edo = 'Realizado' "
	      ."    AND hl7est = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	
	  if ($wnum > 0)
	     {
		  $row = mysql_fetch_array($res);
		  
		  return $row[0];
	     }     
	    else
	       return "";    
     }       
     
     
	 
  //===========================================================================================================================================  
  //*******************************************************************************************************************************************
  
	/****************************************************************************************************************
	 * PARAMETROS QUE SE NECESITAN
	 *************************************************************************/
	$wempresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
	
	$nroDocumento = $wcedula;
	$tipoDocumento = $wtipodoc;

	$paciente = consultarInfoPacienteOrdenHCE($tipoDocumento,$nroDocumento);
	
	/************************************************************************************************************************
	 * Febrero 15 de 2011
	 * 
	 * Modificacion: Febrero 17 de 2011
	 ************************************************************************************************************************/
//	if( $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && $usuario->centroCostos != $paciente->servicioAnterior ){
	if( $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && !($usuario->esCcoUrgencias || $usuario->esCcoCirugia) && esCcoIngreso( $conex, $wbasedato, $paciente->servicioAnterior ) ){
	//if( $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && !existeEncabezadoKardex($paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha) ){
		if( isset($editable) && $editable != "off" || !isset($editable) ){
			mensajeEmergente("El paciente esta en proceso de traslado.\\nDebe recibir el paciente para hacer el kardex.\\n\\nUSTED SE ENCUENTRA ASOCIADO A\\n$usuario->nombreCentroCostos($usuario->centroCostos)"); //Marzo 3 de 2011
			funcionJavascript("window.parent.cerrarModal();");
			exit;
		}
	}
	elseif( false && $paciente->ultimoMvtoHospitalario == "En proceso de traslado" && ($usuario->esCcoUrgencias || $usuario->esCcoCirugia) ){
		$paciente->servicioActual = $paciente->servicioAnterior; 
	}
	/************************************************************************************************************************/
	
	//Parametros que siempre llegan
	//echo "Documento '$wcedula' '$wtipodoc'";
	
	if($usuario->pestanasKardex == ""){
		mensajeEmergente("No tiene permisos para usar ordenes de hce.  Comuniquese por favor con el Area de Soporte.");
		//		funcionJavascript("cerrarModal();");
		//		die("");
	}
	
	/*********************************************************************************************
	 * :::HCE:::Validacion de las empresas responsables
	 *********************************************************************************************/
	if($usuario->empresasAgrupadas != "*" && $usuario->empresasAgrupadas != ""){
		if(strpos($usuario->empresasAgrupadas,$paciente->numeroIdentificacionResponsable) === false){
			mensajeEmergente("La entidad responsable de este paciente no esta asociada a su rol.");
			die("");
		}
	}

	// 2012-06-27
	// Se adicionó accept-charset='utf-8' para que el formulario pueda codificar todos los caracteres correctamente
	// y no arroje algunas veces datos corrompidos que bloqueaban la grabación de ordenes
	// Formulario
	echo "<form name='forma' action='ordenesidc.php' method='post' accept-charset='utf-8'>";
	
	echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'/>";
	echo "<input type='hidden' name='wbasedato' id='wbasedato' value='".$wbasedato."'/>";
	echo "<input type='hidden' name='wbasedatohce' id='wbasedatohce' value='".$wbasedatohce."'/>";
	echo "<input type='hidden' name='wcenmez' id= 'wcenmez' value='".$wcenmez."'/>";
	echo "<input type='hidden' name='usuario' id='usuario' value='".$wuser."'/>";
	echo "<input type='hidden' name='centroCostosUsuario' value='".$usuario->centroCostos."'/>";
	
	$centroCostosGrabacionTemp = $usuario->centroCostosGrabacion;
	if(!$usuario->esUsuarioLactario){
		if($usuario->esUsuarioCM || $usuario->esUsuarioSF){
			$centroCostosGrabacionTemp = "*";
		}
	}
	echo "<input type='hidden' name='centroCostosGrabacion' value='".$centroCostosGrabacionTemp."'/>";
	echo "<input type='hidden' name='wfechagrabacion' id='wfechagrabacion' value='$fechaGrabacion'>";
	echo "<input type='hidden' name='whgrupos' value='$usuario->gruposMedicamentos'>";
	echo "<input type='hidden' name='wempresa' value='$wempresa'>";
	
	echo "<input type='hidden' name='wcedula' id='wcedula' value='$wcedula'>";
	echo "<input type='hidden' name='wtipodoc' id='wtipodoc' value='$wtipodoc'>";
	echo "<input type='hidden' name='whfirma' id='whfirma' value=''>";

	echo "<input type='hidden' name='wespecialidad' value='$usuario->codigoEspecialidad'>";
	
	if($usuario->esUsuarioLactario){
		echo "<input type='hidden' name='whusuariolactario' value='on'>";
	} else {
		echo "<input type='hidden' name='whusuariolactario' value='off'>";
	}

	if(!isset($editable)){
		$editable="on";
	}
	echo "<input type='HIDDEN' NAME='editable' value='".$editable."'/>";

	//Indicador de si es fecha actual
	if(isset($wfecha)){
		$esFechaActual = ($wfecha == $fechaGrabacion);
	}

	//Calcula la fecha del dia anterior.
	$fechaActualMilis 	= time();
	$ayerMilis			= time() - (24 * 60 * 60);
	$fechaAyer			= date("Y-m-d", $ayerMilis);
	
	
	
	
	/************************************************************************************************************************************
	 * Consulto la última fecha del Kardex
	 ************************************************************************************************************************************/
	$sql ="SELECT MAX(fecha_data) 
		     FROM ".$wbasedato."_000053 
			WHERE karhis='".$paciente->historiaClinica."' 
			  AND karing='".$paciente->ingresoHistoriaClinica."' 
			  AND fecha_data !='".date( "Y-m-d" )."'";
	$res = mysql_query( $sql, $conex );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$fechaAyer = $rows[0];
	}
	
	//2020-09-21. Si la fecha es vacia se deja por defecto la fecha 0000-00-00, para que no halla conflicto con la BD
	if( empty( $fechaAyer ) ){
		$fechaAyer = '0000-00-00';
	}
	/************************************************************************************************************************************/
			
	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>"; 
    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...";
	echo "</div>";

	//Seccion de combinacion de articulos de nutricion y liquidos si aplica
	echo "<div id='modalArticulos' style='display:none;'>";
	echo "<table width='100%'>";

	echo "<tr>";
	echo "<td colspan=4 align=center class='encabezadoTabla'>";
	echo "<b>Selecci&oacute;n de componentes</b>";
	echo "</td>";
	echo "</tr>";

	echo "<tr><td class='fila1'>Insumos que componen <span id='articuloComponentes'></span></td></tr>";

	echo "<tr><td class='fila2' style=' width: 100%;'>";
	echo "<div id='listaComponentes' style='overflow-y: scroll; width: 100%; height: 200px;'></div>";
	echo "</td></tr>";

	echo "<input type='hidden' id='wcomponentesarticulo' name='wcomponentesarticulo' value=''>";
	echo "<input type='hidden' id='wcomponentesarticulocod' name='wcomponentesarticulocod' value=''>";

	echo "<input type='hidden' name='indiceArticuloComponentes' id='indiceArticuloComponentes' value=''>";
	
	echo "<tr><td colspan=4 align=center>";
	echo "<input type='button' value='Terminar' onclick='javascript:cerrarModalArticulos();'>"; 
	echo "</td></tr>";
	echo "</table>";
	echo "</div>";
	
	
	if( isset($matrix) ){
		echo "<INPUT type='hidden' name='matrix' value='off'>";
	}
   /*****************************************************************************************************************************
     * MAESTROS OCULTOS.  SIRVEN PARA ASIGNAR LAS FILAS DINAMICAS... SE CARGAN en la accion b
     *****************************************************************************************************************************/
    if(isset($waccion) && $waccion == "b"){

    	echo "<div style='display:none'>";

    	//UNIDADES DE MEDIDA
    	echo "<select name='wmunidadesmedida' id='wmunidadesmedida' style='width:120' class='seleccion' style='display:block'>";
    	$colUnidades = consultarUnidadesMedida();
    	echo "<option value=''>Seleccione</option>";
    	foreach ($colUnidades as $unidad){
    		echo "<option value='".$unidad->codigo."'>$unidad->descripcion</option>";
    	}
    	echo "</select>";

    	//UNIDADES DE MEDIDA POSOLOGIA
    	echo "<select name='wmunidadesmedidaposologia' id='wmunidadesmedidaposologia' style='width:120' class='seleccion' style='display:block'>";
    	$colUnidadesPosologia = consultarUnidadesMedidaPosologia();
    	echo "<option value=''>Seleccione</option>";
    	foreach ($colUnidadesPosologia as $unidadpos){
    		echo "<option value='".$unidadpos->codigo."'>$unidadpos->descripcion</option>";
    	}
    	echo "</select>";

	
    	//PERIODICIDADES CLINICA
    	echo "<select id='wmperiodicidades'>";
    	echo "<option value=''>Seleccione</option>";
    	$colPeriodicidades = consultarPeriodicidades();
    	foreach ($colPeriodicidades as $periodicidad){
    		echo "<option class='cli' value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
    	}
    	// echo "</select>";
    	
    	// //PERIODICIDADES ONCOLOGICAS
    	// echo "<select id='wmperiodicidades'>";
    	// echo "<option value=''>Seleccione</option>";
    	$colPeriodicidadesOnc = consultarPeriodicidadesOnc();
    	foreach ($colPeriodicidadesOnc as $periodicidad){
    		echo "<option class='onc' value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
    	}
    	echo "</select>";

		/*
		//PERIODICIDADES ONCOLOGICAS
    	echo "<select id='wmperiodicidades'>";
    	echo "<option value=''>Seleccione</option>";
    	$colPeriodicidadesOnco = consultarPeriodicidadesOnco();
    	foreach ($colPeriodicidadesOnco as $periodicidad){
    		echo "<option value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
    	}
    	echo "</select>";
		*/

		
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
    	$examenesLaboratorio = consultarCentrosAyudasDiagnosticas();
    	$colServiciosExamenes = $examenesLaboratorio;

    	echo "<option value=''>Seleccione</option>";
    	foreach ($examenesLaboratorio as $examen){
    		echo "<option value='$examen->codigo|$examen->consecutivoOrden'>$examen->nombre - Consecutivo de orden: $examen->consecutivoOrden</option>";
    	}
    	echo "</select>";

    	//ESTADOS DEL EXAMEN
    	echo "<select id='wmestadosexamen'>";
    	$colEstadosExamen = consultarEstadosExamenes();
    	foreach ($colEstadosExamen as $estadoExamen){
    		echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
    	}
    	echo "</select>";

    	//ESTADOS DEL EXAMEN
    	echo "<select id='wmestadosexamen'>";
    	$colEstadosExamenRol = consultarEstadosExamenesRol();
    	foreach ($colEstadosExamenRol as $estadoExamen){
    		echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
    	}
    	echo "</select>";

    	//ESTADOS DEL EXAMEN DEL LABORATORIO
    	echo "<select id='wmestadosexamenlab'>";
    	$colEstadosExamenLab = consultarEstadosExamenesLaboratorio();
    	foreach ($colEstadosExamenLab as $estadoExamen){
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
    }
    /*****************************************************************************************************************************
     * FIN MAESTROS OCULTOS
     *****************************************************************************************************************************/

	//Estrategia de FC con parámetro waccion
	if(!isset($waccion)){
		$waccion = "";
	}
	
	//FC para hacer las acciones
	switch ($waccion){
		case 'b': //Cuando ya hay un kardex creado se muestra la pantalla de modificación			
			/*******************************************************
			 * EL KARDEX PUEDE SER EDITABLE (SOLO EL DE HOY) O DE SOLO LECTURA (CUALQUIER OTRA FECHA)
			 *******************************************************/
			$confirmaGeneracion = true;
			
			if(isset($paciente->historiaClinica) && isset($wfecha)){
//				$paciente = consultarInfoPacienteKardex($whistoria,"");  //Consulta de paciente por historia, sin ingerso

				//$usuario->centroCostos
				if(!$usuario->esUsuarioLactario){
					if($usuario->esUsuarioCM || $usuario->esUsuarioSF){
						$usuario->centroCostosGrabacion = "*";
					}
				}
//				echo "Centro costos grabacion modificado $usuario->centroCostosGrabacion"; 
				
				if(!empty($paciente->ingresoHistoriaClinica)){
					
					$kardexActual = consultarKardexPorFechaPaciente($wfecha,$paciente);
						
					if(isset($whgrabado)){
						echo "<input type='hidden' name='whgrabado' value='$whgrabado'>";	
					}
//					echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='0'/>";

					/****************************************************************************************
					 * Agosto 22 de 2014
					 *
					 * Busco todos los articulos en la temporal antes de la última fecha en que tuvo kardex
					 * y los bajo a la temporal
					 ****************************************************************************************/
					
					$sql = "SELECT 
								Kadfec
							FROM
								".$wbasedato."_000060 a
							WHERE
								Kadhis = '".$paciente->historiaClinica."'
								AND Kading = '".$paciente->ingresoHistoriaClinica."'
								AND Kadfec < '".$fechaAyer."'
							GROUP BY Kadfec
							";
					
					$resKarAnt = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					
					while( $rowsKarAnt = mysql_fetch_array($resKarAnt) ){
						
						cargarArticulosADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$rowsKarAnt[ 'Kadfec' ],false,'');
					}
					 
					/****************************************************************************************/
					
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
						}
					}

					$esKardexNuevo = true;
					if(!empty($kardexActual->historiaClinica)){
						$esKardexNuevo = false;
					}
					
					/************************************************************************
					 * Dejo siempre las ordenes de procedimiento anteriores sin imprimir anteriores sin imprimir
					 ************************************************************************/
					$sql = "UPDATE
								".$wbasedatohce."_000027 a, ".$wbasedatohce."_000028 b
							SET
								Detimp = 'off'
							WHERE
								Ordhis =  '".$paciente->historiaClinica."' 
								AND Ording = '".$paciente->ingresoHistoriaClinica."'
								AND Ordnro = Detnro
								AND Dettor = Ordtor
								AND Detest = 'on'
								AND Detimp = 'on'
								AND b.fecha_data <= '".date( "Y-m-d" )."'
							";
					
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					
					$sql = "UPDATE
								".$wbasedatohce."_000027 a, ".$wbasedato."_000159 b
							SET
								Detimp = 'off'
							WHERE
								Ordhis =  '".$paciente->historiaClinica."' 
								AND Ording = '".$paciente->ingresoHistoriaClinica."'
								AND Ordnro = Detnro
								AND Dettor = Ordtor
								AND Detest = 'on'
								AND Detimp = 'on'
								AND b.fecha_data <= '".date( "Y-m-d" )."'
							";
					
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					/************************************************************************/
					
					/************************************************************************
					 * Dejo siempre las ordenes de medicamentos anteriores sin imprimir
					 ************************************************************************/
					$sql = "UPDATE
								".$wbasedato."_000054 a
							SET
								Kadimp = 'off'
							WHERE
								Kadhis =  '".$paciente->historiaClinica."' 
								AND Kading = '".$paciente->ingresoHistoriaClinica."'
								AND Kadimp = 'on'
								AND Kadfec <= '".date( "Y-m-d" )."'
							";
					
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					
					$sql = "UPDATE
								".$wbasedato."_000060 a
							SET
								Kadimp = 'off'
							WHERE
								Kadhis =  '".$paciente->historiaClinica."' 
								AND Kading = '".$paciente->ingresoHistoriaClinica."'
								AND Kadimp = 'on'
								AND Kadfec <= '".date( "Y-m-d" )."'
							";
					
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					/************************************************************************/
					
					/****************************************************************************************************************
					 * Si el kardex lo esta mirando otra persona, el kardex no se puede abrir. Si la persona quien lo estaba creando
					 * es la misma que el usuario el kardex se puede abrir
					 *
					 * Marzo 8 de 2011
					 ****************************************************************************************************************/
					if( !$esKardexNuevo ){
						if( $kardexActual->grabado == "off" || hayArticulosEnTemporal( $conex, $wbasedato, $paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha) ){
							if( $kardexActual->usuarioQueModifica != $usuario->codigo ){
								// mensajeEmergente("Las Ordenes se encuentra actualmente en uso por el usuario: ".$kardexActual->nombreUsuarioQueModifica);
								// funcionJavascript("window.parent.cerrarModal();");
								// exit();
								// funcionJavascript("inicio(\"$paciente->servicioActual\");");
							}
						}
					}
					/****************************************************************************************************************/

					//Carga esquema de insulina si el usuario es de un centro de costos hospitalario
					cargarEsquemaDextrometer($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer,$wfecha);

					if($cargarDefinitivo){
						cargarArticulosADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer,@$esKardexNuevo,'');
						cargarExamenesADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer,@$firmaDigital);
						cargarInfusionesADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
						cargarMedicoADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
						cargarDietasADefinitivo($paciente->historiaClinica, $paciente->ingresoHistoriaClinica,$fechaAyer);
						
						// global $basedatos;
						// $basedatos = $wbasedato;
						// crearEncabezadoKardexCerrar( $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, '' );
						
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
						$confirmaGeneracion = true;
//						funcionJavascript("confirmarGeneracion();");
					}
						
					if(!$esKardexNuevo){
						$confirmaGeneracion = true;
//						funcionJavascript("confirmarGeneracion();");
					} else {
						//No permite generar el kardex NUEVOS para dias anteriores
						if(!$esFechaActual){
							mensajeEmergente("No existe orden para la fecha seleccionada.");
							$confirmaGeneracion = false;
//							funcionJavascript("inicio();");
						}

						//Si el paciente se encuentra en alta definitiva no debe permitir modificaciones en el kardex
						if($paciente->altaDefinitiva == "on"){
//							echo "<br /><span class='subtituloPagina2'>El paciente se encuentra en alta definitiva, no puede crearse kardex.</span><br />";
//							echo "<br /><center><input type='button' value='Regresar' id='regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'><br /></center>";
						} else {
//							echo "<br /><center><input type='button' value='Regresar' id='regresar' onclick='javascript:inicio();'> | <input type='button' value='Confirmar generacion Kardex' onclick='javascript:confirmarGeneracion();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'><br /></center>";							
						}
					} 
				} else {
					$confirmaGeneracion = false;
					mensajeEmergente("No se pudo consultar el ultimo ingreso del paciente.  Verifique que la historia clínica fue digitada correctamente");
					funcionJavascript("inicio();");
				}//Fin existe ingreso de historia e informacion de paciente
			} else {
				$confirmaGeneracion = false;
				mensajeEmergente("Faltan parametros para realizar la consulta de la orden");
//				funcionJavascript("inicio();");
			}
			/***************************************************************************************************/
			
			if( $confirmaGeneracion ){
			$aplicaGraficaSuministro = true;
			$activarPestanas = true;			//**OJO TODO:  ESTO SE DEBE QUITAR
				
//			$paciente = consultarInfoPacienteKardex($whistoria,"");
			$kardexActual = consultarKardexPorFechaPaciente($wfecha, $paciente);

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
				$kardex->medidasGenerales = $kardexActual->medidasGenerales;
				
				$kardex->obsDietas = $kardexActual->obsDietas;
				$kardex->procedimientos = $kardexActual->procedimientos;
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

			//Si el paciente se encuentra en alta definitiva no debe permitir modificaciones en el kardex
			if($paciente->altaDefinitiva == "on" || (isset($editable) && $editable == "off")){
				$kardexActual->editable = false;
			}
			
			if($kardexActual->editable){
				funcionJavascript("window.onbeforeunload = salida;");
			}
			
			//Autorecuperacion de kardex anterior si no esta grabado on
			//Cuando realice una consulta del kardex debe apagarse la bandera de grabado
			marcarGrabacionKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, "off");
			
			//Campos ocultos
			echo "<input type='hidden' id='whistoria' name='whistoria' value='$paciente->historiaClinica'>";
			echo "<input type='hidden' id='wingreso' name='wingreso' value='$paciente->ingresoHistoriaClinica'>";
			echo "<input type='hidden' name='wfecha' value='$wfecha'>";
			// echo "<input type='hidden' name='weditable' id='weditable' value='$kardexActual->editable'>";
			echo "<input type='hidden' name='weditable' id='weditable' value='".($kardexActual->editable ? 'on': 'off' )."'>";
			echo "<input type='hidden' name='wservicio' value='$paciente->servicioActual'>";
			
			if($kardexActual->noAcumulaSaldoDispensacion){
				echo "<input type='hidden' name='wkardexnoacumula' value='on'>";
			} else {
				echo "<input type='hidden' name='wkardexnoacumula' value='off'>";
			}

			//Indicador si es primer kardex o no. Es verdadero si no hay kardex del dia o del dia anterior.
			if($kardexActual->esPrimerKardex){
				echo "<input type='hidden' id='wkardexnuevo' value='S'>";			
			} else {
				echo "<input type='hidden' id='wkardexnuevo' value='N'>";
			}
			
			// //Div flotante con las alergias y los diagnosticos
			// consultarAlergiasDiagnosticosAnteriores($paciente->historiaClinica,&$alergiasAnteriores,&$diagnosticosAnteriores);
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
			
			
			$estilosFrame = "";
			$posicionFrame = "position:absolute;top:68px;left:650px;z-index:99;";
			
			echo "<script>";
			echo "llamarIframeAlerta('".$paciente->historiaClinica."','".$paciente->ingresoHistoriaClinica."','".$wemp_pmla."','".$estilosFrame."',true,true,1,'".$posicionFrame."')";
			echo "</script>";
			
			echo "<br><br>";
				
			echo "<table border=0>";
			echo "<tr>";
			echo "<td class='subtituloPagina2' width=350>";
//			echo '<span class="subtituloPagina2" nowrap align="center">';
			if($kardexActual->editable){
				if($cargarDefinitivo){
					echo "Crear orden del d&iacute;a ".$wfecha;
				} else {
					echo "Editar orden del d&iacute;a ".$wfecha;
				}
			} else {
				echo "Consultar orden del d&iacute;a $wfecha";	
			}

			$accionesPestana = consultarAccionesPestana( "3" );
			
			$confirmaAutomaticamente = "";
			//Si un usuario puede leer y crear, el campo confirmado siempre al guardar quedará confirmado
			//Si puede crear pero no leer, siempre quedará desconfirmado
			//Ninguna de las anteriores siempre queda como estaba el kardex
			if( !empty( $accionesPestana["3.99"]->crear ) && $accionesPestana["3.99"]->leer === true && $accionesPestana["3.99"]->crear === true ){
				$confirmaAutomaticamente = "checked";
				echo "<INPUT type='hidden' id='hiNoParpadear' name='hiNoParpadear' value='on'>";
			}
			elseif( !empty( $accionesPestana["3.99"]->crear ) && $accionesPestana["3.99"]->crear === true && $accionesPestana["3.99"]->leer === false ){
				$confirmaAutomaticamente = "";
				echo "<INPUT type='hidden' id='hiNoParpadear' name='hiNoParpadear' value='off'>";
			}
			else{
				$confirmaAutomaticamente = ( $kardexActual->confirmado == 'on' ) ? "checked" : "";
				echo "<INPUT type='hidden' id='hiNoParpadear' name='hiNoParpadear' value='off'>";
			}
			
//			echo "</span>";
			echo "</td>";
			echo "<td width='75%' align='right'>";
 			if($kardexActual->editable){
 				if(!$usuario->firmaElectronicamente){
 					
 					if(true || $kardexActual->confirmado == "on"){
 						echo "<input type='checkbox' name='wcconf' id='wcconf' onClick='javascript:marcarKardexConfirmado();' $confirmaAutomaticamente style='display:none'>";
 					}
 					else{
 						echo "<input type='checkbox' name='wcconf' id='wcconf' onClick='javascript:marcarKardexConfirmado();'>";
 					}
					echo "<div id='btnGrabar1' onclick='javascript:grabarKardex();' style='cursor:pointer;'><img src='/matrix/images/medical/hce/ok.png'></div>";
 					echo "&nbsp;|&nbsp;";
 					echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='javascript:abrirModalHCE();'>Vistas Asociadas</A>";
 				} else {
 					echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='javascript:abrirModalHCE();'>Vistas Asociadas</A>";
 				}
 			} 

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
			echo "<td class='fila1'>Servicio actual</td>";
			echo "<td class='fila2'>";
			echo $paciente->nombreServicioActual;
			echo "</td>";
			
			if( !empty($tipo) ){
				echo "<td bgcolor='$color' align=center><b><font size=2 color='white'>";
				echo $tipo;
				echo "</font></b></td>";				
			}

			//Enfermera(o) que genera
			echo "<tr>";
			echo "<td class='fila1'>Usuario que actualiza (Codigo y nombre del Rol)</td>";
			echo "<td class='fila2'>";
			echo "$usuario->codigo - $usuario->descripcion. <br>$usuario->nombreCentroCostos ($usuario->codigoRolHCE-$usuario->nombreRolHCE)";
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

			//echo "<td class='fila1'>Ultimo mvto hospitalario</td>";
			
			if($paciente->altaDefinitiva == 'on'){
				//echo "<td class='articuloControl'>";
			} else {
				//echo "<td class='fondoAmarillo'>";
			}
			//echo $paciente->ultimoMvtoHospitalario;						
			//echo "</td>";
						
			//Calculo de dias de hospitalizcion desde ingreso
			$diaActual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$fecha = explode("-",$paciente->fechaIngreso);
			$diaIngreso = mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);

			$diasHospitalizacion = ROUND(($diaActual - $diaIngreso)/(60*60*24));

			// echo "<td class='fila1'>D&iacute;as de hospitalizaci&oacute;n</td>";
			// echo "<td class='fila2'>";
			// echo "".$diasHospitalizacion;
			// echo "</td>";
			
			// echo "<td colspan=2>&nbsp;</td>";
			
			// echo "</tr>";
			
			// echo "<tr>";		
			
			//Responsable
			echo "<td class='fila1'>Entidad responsable</td>";
			echo "<td class='fila2' colspan='3'>";
			echo "$paciente->numeroIdentificacionResponsable - $paciente->nombreResponsable";
			echo "</td>";
			
			//Fecha y hora de ingreso al servicio actual
			// echo "<td class='fila1'>Fecha de ingreso al servicio actual</td>";
			// echo "<td class='fila2'>";
			// echo "$paciente->fechaHoraIngresoServicio";						
			// echo "</td>";
			
			//echo "<td class='fila1'>";
			
			//Boton de vistas asociadas
//			echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='javascript:abrirModalHCE();'>Vistas Asociadas</A>";
			
			//echo "</td>";
			//echo "<td class='fila2'>&nbsp;";
//			echo "<a href='#null' onclick='return fixedMenu.show();'>Alergias</a>";						
			//echo "</td>";
			
			echo "</tr>";
			
			echo "<tr>";
			echo "<td height=30 colspan=6>&nbsp;</td>";
			echo "</tr>";
			
			echo "</table>";
			
			/************************************************
			 * Agrengando mensajeria
			 ************************************************/
			//Campo oculto que indica de que programa se abrio
			
			/*
			echo "<INPUT type='hidden' id='mesajeriaPrograma' value='Ordenes'>";
			 
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
			*/
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
				echo "<input type='button' value='Consultar' onclick='javascript:consultarMedicamento();'>&nbsp;|&nbsp;<input type='button' value='Agregar medicamento' onclick='javascript:agregarArticulo(\"detKardexAdd\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar' onclick='return fixedMenu2.hide();'>";
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
			$indicePestana = "1";
			$vecPestanaGrabacion = array();
			
			//Mensaje de espera
			echo "<div id='msjInicio' align=center>";
			echo "<img src='../../images/medical/ajax-loader5.gif'/>Cargando las pestañas, por favor espere...";
			echo "</div>";

			echo "<input type=hidden id=hpestanas value='$usuario->pestanasKardex'>";
			
			echo "<div id='tabs' class='ui-tabs' style='display:none'>";				//Inicio de lo que va a ir encerrado en las pestañas
			echo "<ul>";
			if($usuario->pestanasKardex == "*"){
				/******************************************************************************
				 * Las pestañas se fragmentan de la siguiente manera:
				 * 
				 * 1.  Codigo
				 * 2.  Nombre
				 * 3.  Puede grabar
				 ******************************************************************************/
				if($activarPestanas){
					$vecPestanas = explode(";",$usuario->pestanasHCE);
//					var_dump($vecPestanas);
					$tmp = "";					
					foreach($vecPestanas as $pestana){
						$vecPestanaElemento = explode("|",$pestana);
						$tmp .= "|".$pestana;
						if($vecPestanaElemento[0] != ''){
							echo "<li><a href='#fragment-$vecPestanaElemento[0]'><span>$vecPestanaElemento[1]</span></a></li>";
							$vecPestanaGrabacion[$vecPestanaElemento[0]] = ($vecPestanaElemento[2] == 'on');
						}
					}
				}
			}
			echo "</ul>";
			
			if(count($vecPestanaGrabacion) == 0){
				mensajeEmergente("tmp: ".$tmp.". No tiene permisos para usar ordenes de hce.  Comuniquese por favor con el Area de Soporte.");
				die("");
			}
	
			//PESTAÑA DE INFORMACION DEMOGRAFICA
			if(isset($vecPestanaGrabacion[$indicePestana])){
				/************************************************************************************************************************
				 * CONSULTA DE ACCIONES POR CADA PESTAÑA 
				 ************************************************************************************************************************/
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				echo "<div id='fragment-1'>";

				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";					
				
				echo "<table align='center'>";

				echo "<tr>";
					
				//Talla
				echo "<td class='fila1'>Talla (cm.)&nbsp;";
				//Consulta del nombre de la accion
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("1","txTalla",@$accionesPestana[$indicePestana.".1"],array("maxlength"=>"3","size"=>"5","class"=>"textoNormal","onKeyPress"=>"return validarEntradaEntera(event)"),$kardexActual->talla);
//					echo "<input type=text name=txTalla class='textoNormal' maxlength=4 size=5 value='$kardexActual->talla' idGlobal='{$tablaIDAcciones[$indicePestana."-"."txTalla"]}'/>";
				} else {
					echo "$kardexActual->talla";
				}
				echo "</td>";

				//Peso
				echo "<td class='fila1'>Peso (kg.)&nbsp;";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("1","txPeso",@$accionesPestana[$indicePestana.".2"],array("maxlength"=>"5","size"=>"5","class"=>"textoNormal"),$kardexActual->peso);
//					echo "<input type=text name=txPeso class='textoNormal' maxlength=5 size=5 value='$kardexActual->peso' idGlobal='{$tablaIDAcciones[$indicePestana."-"."txPeso"]}'/>";
				} else {
					echo "Peso (kg.)&nbsp;$kardexActual->peso";
				}
				echo "</td>";

				echo "<td class='fila1'>&nbsp;</td>";
				echo "</tr>";

				//Diagnostico actual
				echo "<tr>";
				echo "<td align=center class='fila1'>";
				echo "Diagn&oacute;stico actual<br />";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2",'txDiag',@$accionesPestana[$indicePestana.".3"],array("cols"=>"40","rows"=>"8"),"$kardexActual->diagnostico");
//					echo "<textarea name='txDiag' cols=40 rows=8 idGlobal='{$tablaIDAcciones[$indicePestana."-"."txDiag"]}'>$kardexActual->diagnostico</textarea>";
				} else {
					echo "<textarea name='txDiag' cols=40 rows=8 readonly>$kardexActual->diagnostico</textarea>";
				}
				echo "<br />&nbsp;</td>";

				//Antecedentes alergicos
				echo "<td align=center class='fila1'>";
				echo "Antecedentes al&eacute;rgicos y alertas<br />";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2","txAlergias",@$accionesPestana[$indicePestana.".4"],array("cols"=>"40","rows"=>"8"),"$kardexActual->antecedentesAlergicos");
//					echo "<textarea name='txAlergias' cols=40 rows=8 idGlobal='{$tablaIDAcciones[$indicePestana."-"."txAlergias"]}'>$kardexActual->antecedentesAlergicos</textarea>";
				}else{
					echo "<textarea name='txAlergias' cols=40 rows=8 readonly>$kardexActual->antecedentesAlergicos</textarea>";
				}
				echo "<br />&nbsp;</td>";
					
				//Antecedentes personales
				echo "<td align=center class='fila1'>";
				echo "Antecedentes personales<br />";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2",'txAntecedentesPersonales',@$accionesPestana[$indicePestana.".5"],array("cols"=>"40","rows"=>"8"),"$kardexActual->antecedentesPersonales");
//					echo "<textarea name='txAntecedentesPersonales' cols=40 rows=8 idGlobal='{$tablaIDAcciones[$indicePestana."-"."txAntecedentesPersonales"]}'>$kardexActual->antecedentesPersonales</textarea>";
				} else {
					echo "<textarea name='txAntecedentesPersonales' cols=40 rows=8 readonly>$kardexActual->antecedentesPersonales</textarea>";
				}
				echo "<br />&nbsp;</td>";

				echo "</tr>";
				echo "</td>";
				echo "</tr>";
				echo "</table>";

				echo "<div align='center'>";
					
				//Muestra las alergias de los dias para eliminar
				$colAlergias = consultarAlergias($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);
					
				if(count($colAlergias) > 0){
					echo "<table>";
					echo "<thead>";
					echo "<tr class=encabezadoTabla>";
					echo "<td colspan=3 align=center>Retiro de alergias anteriores</td>";
					echo "</tr>";
						
					echo "<tr class=encabezadoTabla align=center>";

					echo "<td>Fecha de registro</td>";
					echo "<td>Descripcion</td>";
					echo "<td>Accion</td>";

					echo "</tr>";
					echo "</thead>";

					$clase="fila1";

					echo "<tbody id='detAlergias'>";
						
					foreach ($colAlergias as $alergia){
						if($clase=="fila1"){
							$clase = "fila2";
						} else {
							$clase = "fila1";
						}

						echo "<tr class=$clase id='trAle$alergia->descripcion'>";

						echo "<td>$alergia->descripcion</td>";
						echo "<td>$alergia->observacion</td>";
						echo "<td align='center'>";
						crearCampo("4","",@$accionesPestana[$indicePestana.".6"],array("onClick"=>"javascript:quitarAlergia('$alergia->descripcion');"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' alt='Quitar alergia'/>");
//						echo "<a href='#null' onclick='javascript:quitarAlergia("."\"$alergia->descripcion"."\");'><img src='../../images/medical/root/borrar.png' alt='Quitar alergia'></a>";
						echo "</td>";

						echo "</tr>";
					}
					echo "</tbody>";
					echo "</table>";
				}
				echo "</div>";

				echo "</div>";
			}
			
			$indicePestana = "2";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-2'>";
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";					
				
				$cont1 = 0;
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<table align='center'>";
//					echo "<tr>";
//
//					echo "<td class='fila1'>C&oacute;digo</td>";
//					echo "<td class='fila2'>";
//					echo "<INPUT TYPE='text' NAME='wcodcom' SIZE=10 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarComponente()\");'>";
//					echo "</td>";
//
//					echo "<td rowspan=2 colspan=2 class='fila2'>";
//					echo "<img id='imgCodCom' style='display:none' src='../../images/medical/ajax-loader5.gif'>";
//					echo "<div id='cntComponente' style='overflow-y: scroll; width: 430px; height: 160px;'>";
//					echo "</div>";
//					echo "</td>";
//
//					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 align=center  class='encabezadoTabla'>";
					echo "<b>Consulta</b>";
					echo "</td>";
					echo "</tr>";
						
					echo "<tr>";
					echo "<td class='fila1'>Componente</td>";
					echo "<td class='fila2'>";
					crearCampo("1","wnomcom",@$accionesPestana[$indicePestana.".1"],array("size"=>"60","class"=>"textoNormal","onBlur"=>"this.value=''"),"");
//					echo "<INPUT TYPE='text' NAME='wnomcom' id='wnomcom' SIZE=60 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarComponente()\");' onBlur='this.value=\"\"'>&nbsp;|&nbsp;";
					echo "&nbsp;|&nbsp;";
					crearCampo("3","",@$accionesPestana[$indicePestana.".2"],array("size"=>"60","onClick"=>"javascript:agregarInfusion();"),"Ordenar...");
//					echo "<input type='button' value='Ordenar...' onClick='javascript:agregarInfusion();' >";
					echo "</td>";
					echo "</tr>";
						
						
//					echo "<tr>";
//					echo "<td colspan=4 align=center class='fila2'>";
//					echo "Nombre Genérico<input type='radio' id='wtipocom' name='wtipocom' value='G'>&nbsp;Nombre Comercial<input type='radio' id='wtipocom' name='wtipocom' value='C' checked>";
//					echo " | ";
//					echo "Unidad de medida&nbsp;";
//					echo "<select id='wunidadcom' name='wunidadcom' class='seleccionNormal'>";
//						
//					$colUnidades = consultarUnidadesMedida();
//						
//					echo "<option value='%'>Cualquier unidad de medida</option>";
//					foreach ($colUnidades as $unidad){
//						echo "<option value='".$unidad->codigo."'>$unidad->codigo - $unidad->descripcion</option>";
//					}
//
//					echo "</select>";
//						
//					echo "</td>";
//					echo "</tr>";
						
//					echo "<tr><td colspan=4 align=center>";
//					echo "<input type='button' value='Consultar' onclick='javascript:consultarComponente();' >&nbsp;|&nbsp;";
//					echo "<input type='button' value='Ordenar programa' onclick='javascript:agregarInfusion();' >";
//					echo "</td></tr>";
					echo "</table>";
					$cont1++;
				}

				echo '<br><span class="subtituloPagina2" align="center">';
				echo "Programas actuales";
				echo "</span>";
				echo "<br>";
				echo "<br>";
					
				echo "<table align='center' border=0 id='tbDetInfusiones'>";

				echo "<tr align='center'>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<td class='encabezadoTabla'>";
					echo "Acciones";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.7' id='wacc$indicePestana.7' value='".accionesATexto(@$accionesPestana[$indicePestana.".7"])."'>";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.3' id='wacc$indicePestana.3' value='".accionesATexto(@$accionesPestana[$indicePestana.".3"])."'>";
					echo "</td>";
					echo "<td class='encabezadoTabla'>";
					echo "Fecha de solicitud";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.4' id='wacc$indicePestana.4' value='".accionesATexto(@$accionesPestana[$indicePestana.".4"])."'>";
					echo "</td>";
					echo "<td class='encabezadoTabla'>";
					echo "Componentes";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.5' id='wacc$indicePestana.5' value='".accionesATexto(@$accionesPestana[$indicePestana.".5"])."'>";
					echo "</td>";
					echo "<td class='encabezadoTabla'>";
					echo "Observaciones";
					echo "<INPUT TYPE='hidden' name='wacc$indicePestana.6' id='wacc$indicePestana.6' value='".accionesATexto(@$accionesPestana[$indicePestana.".6"])."'>";
					echo "</td>";
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
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana] && $kardexActual->esAnterior && $esFechaActual){
					//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
					$colTemporal = consultarInfusionesTemporalKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					if(count($colTemporal) == 0){
						cargarInfusionesAnteriorATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$fechaGrabacion);
					}
				}

				//1. Consulta de estructura temporal.
				$componentesInfusion = consultarInfusionesTemporalKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				$cuentaInfusiones = count($componentesInfusion);
				$contInfusiones = 0;

				if($cuentaInfusiones == 0){
					$componentesInfusion = consultarInfusionesDefinitivoKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					$cuentaInfusiones = count($componentesInfusion);

					if($cuentaInfusiones > 0 && $esFechaActual){
						//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
						cargarInfusionesATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$wfecha);
					}
				}

				$mayorIdInfusiones = $cuentaInfusiones;
				$cont1 = 0;
				foreach ($componentesInfusion as $infusion){
					if($cont1 % 2 == 0){
						echo "<tr id='trIn$infusion->codigo' class='fila1'>";
					} else {
						echo "<tr id='trIn$infusion->codigo' class='fila2'>";
					}

					if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						echo "<td align='center'>";
//						echo "<a href='#null' onclick='javascript:grabarInfusion($infusion->codigo);'><img src='../../images/medical/root/grabar.png'/></a>";
						crearCampo("4","",@$accionesPestana[$indicePestana.".3"],array("onClick"=>"javascript:quitarInfusion($infusion->codigo);"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' />");
//						echo "<a href='#null' onclick='javascript:quitarInfusion($infusion->codigo);'><img src='../../images/medical/root/borrar.png'/></a>";
						
						echo "<INPUT TYPE='hidden' name='wmodificado$indicePestana$infusion->codigo' id='wmodificado$indicePestana$infusion->codigo' value='N'>";
						echo "<INPUT TYPE='hidden' name='windiceliq$contInfusiones' id='windiceliq$contInfusiones' value='$infusion->codigo'>";
						
						echo "</td>";
					}
						
					//Fecha de solicitado examen
					echo "<td align=center>";
					
					echo "<INPUT TYPE='text' name='wfliq$infusion->codigo' id='wfliq$infusion->codigo' SIZE=10 readonly class='campo2' value='$infusion->fecha' onChange='javascript:marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>";
					if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						crearCampo("3","btnFechaLiq$infusion->codigo",@$accionesPestana[$indicePestana.".4"],array("size"=>"60","onClick"=>"javascript:calendario4($infusion->codigo);"),"*");
//						echo "<input type='button' id='btnFechaLiq$infusion->codigo' onclick='javascript:calendario4($infusion->codigo);' height=20 value='*'>";
					}
					echo "</td>";

					//Componentes de la infusion en forma de textarea
					echo "<td>";
					crearCampo("2","wtxtcomponentes$infusion->codigo",@$accionesPestana[$indicePestana.".5"],array("cols"=>"65","rows"=>"5","readonly"=>"readonly","onChange"=>"javascript:marcarCambio('$indicePestana','$infusion->codigo');"),str_replace(';',"\r\n",$infusion->descripcion));
//					echo "<textarea id=wtxtcomponentes$infusion->codigo cols=65 rows=5 readonly onChange='javascript:marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>".str_replace(';',"\r\n",$infusion->descripcion)."</textarea>";
					echo "</td>";

					//Componentes de la infusion en forma de textarea
					echo "<td>";
					crearCampo("2","wobscomponentes$infusion->codigo",@$accionesPestana[$indicePestana.".6"],array("cols"=>"65","rows"=>"5","onChange"=>"javascript:marcarCambio('$indicePestana','$infusion->codigo');"),str_replace(';',"\r\n",$infusion->observacion));
//					echo "<textarea id=wobscomponentes$infusion->codigo cols=65 rows=5 onChange='javascript:marcarCambio(\"$indicePestana\",\"$infusion->codigo\");'>".str_replace(';',"\r\n",$infusion->observacion)."</textarea></td>";
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

				$indicePestana = "3";
				if(isset($vecPestanaGrabacion[$indicePestana])){
					echo "<div id='fragment-3'>";
	
					$accionesPestana = consultarAccionesPestana($indicePestana);
	
					//Indicador para javascript de puede grabar la pestaña
					$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
					echo "<input type=hidden id='pestana' value='$indicePestana'>";
	
					$elementosActuales = 0;
					$colDetalle = array();

					if( $kardexActual->editable ){
						
						$listaProtocolos = generarListaProtocolos('wprotocolo',$usuario->codigo,$paciente->servicioActual,'Medicamentos');
						
						vista_generarConvencion($listaProtocolos);		//Genera la muestra de convenciones de los articulos
					}
	
					if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
						
						$esEditable = $kardexActual->editable;
						
						if( $esEditable ){
							$eventosQuitarTooltip = " onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'";	//Creo los eventos que quitan el tooltip si el kardex es editable
						}

						echo "<div align=center>";
							
						echo "<table>";
						// echo "<tr>";
						// echo "<td class=fila1 width=220px>Buscar medicamento</td>";
						// echo "<td class=fila2>";
						// //echo "<INPUT TYPE='text' NAME='wnombremedicamento' id='wnombremedicamento' SIZE=100 class='textoNormal' onBlur='this.value=\"\"'>";
						// crearCampo("1","wnombremedicamento",@$accionesPestana[$indicePestana.".1"],array("size"=>"100","class"=>"textoNormal","onBlur"=>"this.value=''"),"");
						// //echo "<input type='button' value='Movimiento de articulos' onclick='javascript:abrirMovimientoArticulos(\"N\");'>";
						// echo "</td>";
						// echo "</table>";
						// echo "</div>";
						
						//Nuevo buscador de medicamentos
						echo "<tr>";
						echo "<td class=fondoAmarillo>";
						
						
						echo "<table id='nuevoBuscador'>";
						echo "<tr class='encabezadotabla' align='center'>";
						echo "<td width='100'>Grabar</td>";
						echo "<td width='250'>Medicamento(*)</td>";
						echo "<td width='50'>Manejo<br>Interno</td>";
						echo "<td>Presentaci&oacute;n(*)</td>";
						echo "<td>Unidad de medida(*)</td>";
						echo "<td width='100' cTit>Dosis/D&iacute;a</td>";
						echo "<td width='100'>Cantidad(*)</td>";
						echo "<td width='100' cTit>Vía</td>";
						echo "<td width='100'>Posolog&iacute;a</td>";
						echo "<td width='100'>Unidad Posolog&iacute;a</td>";
						echo "<td width='100' cTit>Frecuencia</td>";
						echo "<td width='100' style='display:none'>Fecha y hora incio(*)</td>";
						echo "<td width='100'>Días tto.</td>";
						echo "<td width='100'>Condición</td>";
						echo "<td width='100'>Observaciones</td>";
						//echo "<td width='100' style='display:none;'>Manejo<br />Interno</td>";
						echo "<td width='100'>Grabar</td>";
						echo "</tr>";
						
						echo "<tr class='fondoAmarillo' align='center'>";
						
						// Boton para el submit
						echo "<td><input type='button' name='btnGrabar4' value='OK' onClick='eleccionMedicamento()' /></td>";

						// Nombre
						echo "<td>";
						//echo "<INPUT TYPE='text' NAME='wnombremedicamento' id='wnombremedicamento' SIZE=100 class='textoNormal' onBlur='this.value=\"\"'>";
						// Llama a la función autocompletarParaBusqueMedicamentosPorFamilia en ordenesidc.js
						crearCampo("1","wnombrefamilia",@$accionesPestana[$indicePestana.".1"],array( "size"=>"50","class"=>"textoNormal"),"");

						//echo "<input type='button' value='Movimiento de articulos' onclick='javascript:abrirMovimientoArticulos(\"N\");'>";
						echo "</td>";
						
						// Manejo interno
						echo "<td>";
						crearCampo("5","wckInterno",@$accionesPestana[$indicePestana.".1"],array("class"=>"","onBlur"=>"","onClick"=>"cambiarTituloNuevoBuscador();"),"");
						echo "</td>";
						
						// Presentación
						echo "<td>";
						crearCampo("6","wpresentacion",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"","onChange"=>"filtrarMedicamentosPorCampo('presentacion')"),"");
						echo "</td>";

						// Unidad de medida
						echo "<td>";
						crearCampo("6","wunidad",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","onBlur"=>"","onChange"=>"filtrarMedicamentosPorCampo('unidad')"),"");
						echo "</td>";
						
						// Dosis
						echo "<td>";
						//crearCampo("1","wdosisfamilia",@$accionesPestana[$indicePestana.".1"],array("size"=>"3","class"=>"textoNormal","onChange"=>"eleccionMedicamento(this.value)","onKeyPress"=>"eleccionPreviaMedicamento(this, event)"),"");
						crearCampo("1","wdosisfamilia",@$accionesPestana[$indicePestana.".1"],array("size"=>"3","class"=>"textoNormal"),"");
						echo "</td>";

						//Dosis máximas
						echo "<td $eventosQuitarTooltip>";
						crearCampo("1","wdosismaxima",@$accionesPestana[$indicePestana."1"],array("size"=>"3","maxlength"=>"6","class"=>"textoNormal","onKeyPress"=>"return validarEntradaEntera(event);","onKeyUp"=>"inhabilitarDiasTratamiento( this,'', '');"),"");
						echo "</td>";

						//Via administracion
						/* 2012-11-02
						// Se comenta porque ya se va a generar dinamicamente segun la familia seleccionada
						// Ya no se muestran todas las vias sino las asociadas a la familia
						echo "<td $eventosQuitarTooltip>";
						$opcionesSeleccion = "<option value='' selected>Seleccione</option>";
						foreach ($colVias as $via){
							$opcionesSeleccion .= "<option value='".$via->codigo."'>$via->descripcion</option>";
						}
						crearCampo("6","wadministracion",@$accionesPestana[$indicePestana."1"],array("class"=>"seleccion","onBlur"=>""),"$opcionesSeleccion");
						*/
						
						echo "<td $eventosQuitarTooltip>";
						crearCampo("6","wadministracion",@$accionesPestana[$indicePestana."1"],array("class"=>"seleccion","onBlur"=>""),"");
						echo "</td>";

						
						echo "<td $eventosQuitarTooltip>";
						crearCampo("1","wposologia",@$accionesPestana[$indicePestana."1"],array("size"=>"3","maxlength"=>"10","class"=>"textoNormal","onKeyPress"=>"return validarEntradaDecimal(event);"),"");
						echo "</td>";

						echo "<td $eventosQuitarTooltip>";
						$opcUnidadesPosologia = "<option value='' selected>Seleccione</option>";
						foreach ($colUnidadesPosologia as $unidadpos){
							$opcUnidadesPosologia .= "<option value='".$unidadpos->codigo."'>$unidadpos->descripcion</option>";
						}
						crearCampo("6","wunidadposologia",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccion","style"=>"width:100px"),$opcUnidadesPosologia);
						echo "</td>";

						
						//Frecuencia
						$equivalenciaPeriodicidad = 0;
						echo "<td $eventosQuitarTooltip>";
						$opcionesSeleccion = "<option value='' selected>Seleccione</option>";
						
						foreach ($colPeriodicidades as $periodicidad){
							$opcionesSeleccionCli .= "<option class='cli' value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
						}
						
						foreach ($colPeriodicidadesOnc as $periodicidad){
							$opcionesSeleccionOnc .= "<option class='onc' value='".$periodicidad->codigo."'>$periodicidad->descripcion</option>";
						}
						$opcionesSeleccion .= $opcionesSeleccionCli.$opcionesSeleccionOnc;
						
						// Se adiciona la opción de horario especial
						//$opcionesSeleccion .= "<option value='H.E.'>H.E.</option>";
						echo "<div id='frecuencias'>";
						crearCampo("6","wfrecuencia",@$accionesPestana[$indicePestana."1"],array("class"=>"seleccion","style"=>"width:120px","onBlur"=>"","onChange"=>"eleccionFrecuencia(this.value)"),"$opcionesSeleccion");
						echo "</div>";

						echo "<div id='frecuenciasCli' style='display:none'>";
						echo $opcionesSeleccionCli;
						echo "</div>";

						echo "<div id='frecuenciasOnc' style='display:none'>";
						echo $opcionesSeleccionOnc;
						echo "</div>";
						
						echo "<input type='hidden' id='wequdosis$articulo->tipoProtocolo$contArticulos' value='$equivalenciaPeriodicidad'>";
						echo "</td>";
						
						$colPeriodicidades = array_merge($colPeriodicidades,$colPeriodicidadesOnc);

						//Fecha y hora inicio
						// Encuentro la hora de inicio par siguiente
						$horParActInicial = floor(date("H")/2) * 2;
						$horIniAdmInicial = "$horParActInicial:00:00";
						$fecIniAdmInicial = strtotime(date("Y-m-d $horIniAdmInicial")) + (60*60*2);

						$fecIniAdmInicial = date("Y-m-d \a \l\a\s:H:i", $fecIniAdmInicial);

						echo "<td style='display:none' $eventosQuitarTooltip>";

						echo "<INPUT TYPE='hidden' NAME='whfinicioN999' id='whfinicioN999' SIZE=22 readonly class='campo2' value='$fecIniAdmInicial'>";

						echo "<INPUT TYPE='text' NAME='wfinicioaplicacion' id='wfinicioaplicacion' SIZE=25 readonly class='campo2' value='$fecIniAdmInicial'>";
						crearCampo("3","btnFechaN999",@$accionesPestana[$indicePestana."N999"],array("onClick"=>"javascript:calendario5(999,'N');"),"*");
						//				echo "<input type='button' id='btnFecha$articulo->tipoProtocolo$contArticulos' onclick='javascript:calendario($contArticulos,\"$articulo->tipoProtocolo\");' value='*'>";
						echo "</td>";

						//Dias tratamiento, debe mostrarse en un alt la fecha de terminación y los dias restantes
						$diasFaltantes = 0;
						$fechaFinal = "-";

						echo "<td $eventosQuitarTooltip>";
						crearCampo("1","wdiastratamiento",@$accionesPestana[$indicePestana."1"],array("size"=>"3","maxlength"=>"3","class"=>"textoNormal","onKeyPress"=>"return validarEntradaEntera(event);","onKeyUp"=>"inhabilitarDosisMaxima( this,'', '' );"),"");
						echo "</td>";

						//Condicion de suministro
						echo "<td onMouseOver='quitarTooltip( this )' onMouseOut='reestablecerTooltip( this );'>";
						$opcionesSeleccion = "<option value='' selected>Seleccione</option>";
						foreach ($colCondicionesSuministro as $condicion){
							$opcionesSeleccion .= "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
						}
						crearCampo("6","wcondicionsum",@$accionesPestana[$indicePestana."1"],array("class"=>"seleccion","style"=>"width:120px","onBlur"=>""),"$opcionesSeleccion");
						echo "</td>";
						
						//Observaciones
						echo "<td $eventosQuitarTooltip>";
						crearCampo("2","wtxtobservasiones",@$accionesPestana[$indicePestana."1"],array("cols"=>"40","rows"=>"2","onKeyPress"=>"return validarEntradaAlfabetica(event);"),"");
						echo "</td>";
						
						//Manejo Interno
						//echo "<td $eventosQuitarTooltip>";
						//crearCampo("5","wchkint",@$accionesPestana[$indicePestana."1"],array(),"");
						//echo "<input type=hidden id='wchkint' name='wchkint' value=''>";
						//echo "</td>";
						
						// Boton para el submit
						echo "<td><input type='button' name='btnGrabar' value='OK' onClick='eleccionMedicamento()' /></td>";
						echo "</tr>";
						echo "</table>";
						
						echo "<div id='regletaGrabacion' align='center' style='display:none'>";
						echo "<table>";
						echo "<tr class='encabezadoTabla'>";
						//echo "<td class=fila2 colspan='2'>";

						echo "<td> &nbsp; <b>Ronda</b> &nbsp; </td>";
						echo "<td>08</td>";
						echo "<td>10</td>";
						echo "<td>12</td>";
						echo "<td>14</td>";
						echo "<td>16</td>";
						echo "<td>18</td>";
						echo "<td>20</td>";
						echo "<td>22</td>";
						echo "<td>24</td>";
						echo "<td>02</td>";
						echo "<td>04</td>";
						echo "<td>06</td>";

						echo "</tr>";
						echo "<tr class='fila1'>";
						echo "<td> &nbsp; <b>Dosis</b> &nbsp; </td>";
						
						/*********************************************************
						 * 	GRAFICA DE LOS HORARIOS DE SUMINISTRO DE MEDICAMENTOS.
						 *
						 *
						 * 1.  Si la fecha de inicio es menor o igual a la de consulta del kardex se muestra la info
						 * 2.  Si se encuentra suspendido no se muestra la grafica
						 *********************************************************/
						//Grafica de los horarios ... 24 horas del dia.  Debe convertirse a horas cada periodicidad
						foreach ($colPeriodicidades as $periodicidad){
							if($periodicidad->codigo == $articulo->periodicidad){
								$horasPeriodicidad = intval($periodicidad->equivalencia);
								break;
							}
						}

						$arrAplicacion = obtenerVectorAplicacionMedicamentos(date("Y-m-d"),date("Y-m-d", $fecIniAdmInicial),date("H:i", $fecIniAdmInicial),$horasPeriodicidad);
						
						$horaArranque = 8;
						echo "<input type='hidden' name='horaArranque' id='horaArranque' value='".$horaArranque."'>";
						
						$aplicaGraficaSuministro = true;

						$cont1 = 1;
						$cont2 = $horaArranque;   //Desplazamiento desde la hora inicial
						$caracterMarca = "*";
						$claseGrafica = "";

						$claseGrafica = "fondoVerde";

						while($cont1 <= 24){
							if(isset($arrAplicacion[$cont2]) && $arrAplicacion[$cont2] == $caracterMarca && $aplicaGraficaSuministro){
								echo "<td align='center' onMouseOver='mostrarTooltip(this, $cont2)'>";
								echo "<input name='dosisRonda$cont2' id='dosisRonda$cont2' value='' size='2'>";
								echo "</td>";
							} else {
								echo "<td onMouseOver='mostrarTooltip(this, $cont2)'>&nbsp;</td>";
							}

							if($cont2 == 24){
								$cont2 = 0;
							}

							$cont1++;
							$cont2++;

							if($cont2 % 2 != 0){
								$cont2++;
							}
							if($cont1 % 2 != 0){
								$cont1++;
							}

							if($cont2 == $horaArranque){
								break;
							}
						}
						echo "</td>";
						echo "</tr>";
						echo "</table>";
						echo "</div>";
						
						echo "</td>";
						echo "</tr>";
						echo "</table>";
						echo "<br /><br />";
						echo "</div>";
					}
						

					echo "<input type='hidden' name='codigoCtc' id='codigoCtc' value='' />";
					echo "<input type='hidden' name='tipoProtocoloAuxCtc' id='tipoProtocoloAuxCtc' value='' />";
					echo "<input type='hidden' name='idxCtc' id='idxCtc' value='' />";
					echo "<div style='display:none'><input type='text' name='tratamientoCtc' id='tratamientoCtc' onFocus='abrirCtcArticulos(this);' value='' /></div>";

					//crearCampo("3","",@$accionesPestana[$indicePestana.".1"],array("onClick"=>"javascript:abrirMovimientoArticulos('N');"),"Ordenar...");
//						echo "<input type='button' value='Ordenar...' onclick='javascript:abrirMovimientoArticulos(\"N\");'>";
						
//				}
	
					//Realiza los movimientos propios en las tablas temporal y definitiva del detalle de articulos del kardex
					realizarMovimientosArticulos($kardexActual, $paciente, $esFechaActual, $wfecha, $fechaGrabacion, "%", $elementosActuales, $colDetalle);

					global $basedatos;
					$basedatos = $wbasedato;
					crearEncabezadoKardexCerrar( $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, '' );
					
					//				var_dump($accionesPestana);
					//Despliega la vista de la tabla de articulos para el protocolo normal
					vista_desplegarListaArticulos($colDetalle,$elementosActuales,"N",$kardexActual->editable,$colUnidades,$colPeriodicidades,$colVias,$colCondicionesSuministro,$accionesPestana,$indicePestana);
					
						
					echo "<br>";
					echo "<br>";
					
					echo "<br /><div align='center'><input id='btnImpArt' type=button value='Imprimir' onclick='grabarKardex(\"imp\")'> &nbsp; &nbsp; <input id='btnImpArtCTC' type=button value='Imprimir CTC' onclick='grabarKardex(\"impctc\")'></div>";
					

					//Detalle de medicamentos anteriores
					$colDetalleAnteriorKardex = consultarDetalleMedicamentosAnterioresKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha, "%");
					$cantidadElementosAnteriores = count($colDetalleAnteriorKardex);
	
					if($cantidadElementosAnteriores > 0){
						vista_desplegarListaArticulosHistorial($colDetalleAnteriorKardex,"%",$colUnidades,$colPeriodicidades,$colCondicionesSuministro,$colFormasFarmaceuticas,$colVias,$kardexActual->editable);
					} else {
						echo '<br><span class="subtituloPagina2" align="center">';
						echo "No hay medicamentos anteriores";
						echo "</span>";
						echo "<div id='medAnt'>";
						echo "</div>";
					}
					echo "</div>";
				}
			}
			
			/**
			 * Se pasa los datos de ordenes del día actual a la tabla temporal
			 */
			
			
			cargarProcedimientosDetalleATemporal( $conex, $wbasedatohce, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $wfecha );
			
			$indicePestana = "4";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-4'>";

				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					
					$optionsSelTipoServicio = "<option value='%' selected>Todos</option>";
					
					$tiposDeAyudaDxs = tiposAyudasDiagnosticas( $conex, $wempresa, $usuario->codigoEspecialidad );
					
					$tiposDeAyudaDxs = explode( "|", $tiposDeAyudaDxs );
					
					foreach( $tiposDeAyudaDxs as $key => $value ){
						 list( $codigo, $descripcion ) = explode( "-", $value );
						 $optionsSelTipoServicio .= "<option value='$codigo'>$descripcion</option>";
					}
					
					$listaProtocolos = generarListaProtocolos('wprotocolo_ayd',$usuario->codigo,$paciente->servicioActual,'Procedimientos');
					echo "<div align='center'>";
					echo "<div class='fondoAmarillo' style='border: 1px solid #333333; width:100% !important; width:77%; height:70px;'>";
					echo "<table align='center' border='0'>";
					echo "<tr class='fondoAmarillo'>";
					
					/* 2013-08-09*/
					echo "<td width='35%' align='left' style='border-right:1px solid #333;'>";
						echo "<table align='left' height='100%'>";
						echo "<tr>";
						echo "<td colspan=2 align=center class='encabezadoTabla'>";
						echo "<b>Protocolos</b>";
						echo "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td align='left' valign='bottom' style='font-size:10pt'>";
						echo $listaProtocolos;
						echo "</td>";
						echo "<td>";
						echo " &nbsp; <input type='button' name='btnImport' value='Importar protocolo' onclick='eleccionMedicamento(1)'>";
						echo "</td>";
						echo "</tr>";
						echo "</table>";
					echo "</td>";
					

					//echo "<td width='10%' style='border-left:2px solid #333;'>&nbsp;</td>";

					echo "<td>";
						echo "<table align='right' height='100%'>";
						echo "<tr>";
						echo "<td align=center class='encabezadoTabla'>";
						echo "<b>Tipo de Orden</b>";
						echo "</td>";
						echo "</tr>";
						echo "<tr height='41'>";
						echo "<td> &nbsp; ";
						crearCampo("6","wselTipoServicio",@$accionesPestana[$indicePestana.".1"],array("class"=>"textoNormal", "onChange"=>"autocompletarParaConsultaDiagnosticas();"),$optionsSelTipoServicio);
						echo " &nbsp; </td>";
						echo "</tr>";
						echo "</table>";
					echo "</td>";

					echo "<td>";
						echo "<table align='center' height='100%'>";
						echo "<tr>";
						echo "<td colspan=3 align=center class='encabezadoTabla'>";
						echo "<b>Consulta</b>";
						echo "</td>";
						echo "</tr>";
						echo "<tr height='41'>";
						echo "<td nowrap> Ayuda o procedimiento &nbsp; </td>";
						echo "<td> ";
						crearCampo("1","wnomproc",@$accionesPestana[$indicePestana.".1"],array("size"=>"60","class"=>"textoNormal"),"");
						// echo "<td> &nbsp; <img id='imgAddExam' src='../../images/medical/hce/add_blue.png' width='14' height='14' border='0' onclick='javascript:agregarNuevoExamen();'> &nbsp; </td>";
	//					echo "<INPUT TYPE='text' NAME='wnomproc' id='wnomproc' SIZE=60 class='textoNormal' onBlur='this.value=\"\"'>";
						//echo "&nbsp;|&nbsp;";
						//echo "<input type='button' value='Ordenar...' onclick='javascript:movimientoExamenes();'>";
						echo " </td>";
						echo "</tr>";
						echo "</table>";
					echo "</td>";
					
					echo "</tr>";

					echo "</table>";
					echo "</div>";
					echo "</div>";
					
//					echo "<div align='center'><input type='button' onClick='javascript:movimientoExamenes();' value='Ordenar...'></div>";
					echo "<br />";
				}
				
				//Examenes de historia clinica
				$datosAdicionales = Array();
				$colExamenesHistoria = consultarOrdenesHCE($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$datosAdicionales,$usuario->codigoEspecialidad);
				$cuentaExamenesHistoria = count($colExamenesHistoria);
				$contExamenes = 0;
				$contProtocolosExamenes = 0;
				
				$contOrdenes = 0;
				
				$imprimirUrl = false;
				$url = true;
				
				$verAnt = "";
				
				foreach( $datosAdicionales as $keyCco => $valueCco ){
					
					foreach( $valueCco as $keyNroOrden => $valueNroOrden ){
						
						if( @$valueNroOrden['Anteriores'] > 0 ){
							$imprimirUrl = true;
							
							// Se agrega wcco=$paciente->servicioActual requerido como parámetro en el script	// 2012-06-26
							$url = $valueNroOrden['Programa']."?wemp_pmla=$wemp_pmla&wcco=$paciente->servicioActual&whis=$paciente->historiaClinica&wing=$paciente->ingresoHistoriaClinica";
						}
					}
				}
				
				// 2012-07-03
				// Se comenta porque ya se van amostrar las ordenes realizadas en formato de pestañas
				/*
				if( $imprimirUrl ){
					$verAnt = "<font size='5'><a onClick=\"abrirVentanaVerAnteriores( '$url' );\" style='cursor:hand' onMouseOver='this.style.color=\"blue\";' onMouseOut='this.style.color=\"black\";'>Ver ordenes realizadas</a></font>";
				}
				*/
				
				echo '<div class="subtituloPagina2" style="overflow:visible;">';
				
				// 2012-07-03
				// Se comenta porque ya se van a mostrar las ordenes realizadas en formato de pestañas
				/*
				echo "<table align=center width='100%'>";
				echo "<tr>";
				echo "<td colspan='3'>";
				*/
				echo "<div id='tabs2' class='ui-tabs'>";				//Inicio de lo que va a ir encerrado en las pestañas
				echo "<ul>";
				echo "<li><a href='#fragment-pendientes'><span>Ordenes del día</span></a></li>";
				echo "<li><a href='#fragment-realizadas'><span>ordenes anteriores</span></a></li>";
				echo "</ul>";
				
				// 2012-07-03
				// Se comenta porque ya se van a mostrar las ordenes realizadas en formato de pestañas
				/*
				echo "</td>"; 
				
				echo "<td style='width:30%' align=right>$verAnt</td>";
				
				echo "</tr>";
				echo "</table>";
				
				echo "<br>";
				*/


				// 2012-07-03
				/********************************************************
				 ********************************************************
				 ** Inicio contenedor de pestaña de ordenes pendientes **
				 ********************************************************
				 ********************************************************/

				 echo "<div id='fragment-pendientes'>";

				/***************************
				 * Movimiento de examenes
				 ***************************/
				if($kardexActual->editable){
					echo "<div id='movExamenes' style='position:absolute;display:none;z-index:200;width:450px;height:360px;left:21px;top:10px;padding:5px;background:#FFFFFF;border:2px solid #2266AA'>";
					echo "<table>";

					echo "<tr>";
					echo "<td colspan=4 align=center class='encabezadoTabla'>";
					echo "<b>Buscador de ayudas diagnosticas</b>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";

					echo "<td class='fila1' nowrap>Unidad que realiza</td>";
					echo "<td class='fila2'>";
						
					echo "<select id='wservexamen' name='wservexamen' class='seleccionNormal' onChange='javascript:consultarServicioExamen();'>";

					echo "<option value=''>Seleccione</option>";
					foreach ($colServiciosExamenes as $servicio){
						echo "<option value='".$servicio->codigo."|$servicio->consecutivoOrden'>$servicio->codigo - $servicio->nombre</option>";
					}

					echo "</select>";
					echo "</td>";

					echo "</tr>";

					echo "<tr>";
					echo "<td class='fila1'>Descripcion</td>";
					echo "<td class='fila2'>";
					echo "<INPUT TYPE='text' NAME='wnomayu' id='wnomayu' SIZE=20 class='textoNormal' onkeypress='return teclaEnter(event,"."\"consultarAyudasDiagnosticas();\");'>";
					echo "</td>";
					echo "</tr>";
					
					echo "<tr>";
					echo "<td colspan=4 align=center class='fila2'>";
					echo "<b>&nbsp;</b>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 align=center class='fila1'>";
					echo "<b>Consecutivo de orden para el servicio: &nbsp;<span id='wconsserv'></span></b>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 align=center class='fila2'>";
					echo "<b>&nbsp;</b>";
					echo "</td>";
					echo "</tr>";
					
					echo "<tr><td colspan=4 align=center>";
					echo "<input type='button' value='Consultar' onclick='javascript:consultarAyudasDiagnosticas();'>&nbsp;|&nbsp;<input type='button' value='Cerrar' onclick='return movExamenes.hide();'>";
					echo "</td></tr>";

					echo "<tr>";
					echo "<td colspan=4 class='fila2'>";
					echo "<img id='imgCodMed' style='display:none' src='../../images/medical/ajax-loader5.gif'>";
					echo "<div id='cntExamenes' style='overflow-y: scroll; width: 100%; height: 160px;'>";
					echo "</div>";
					echo "</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td colspan=4 class='fila2'>";
					echo "<span><b>NOTA: </b>Realice su búsqueda específica, este buscador retornará hasta cien resultados</span>";
					echo "</td>";
					echo "</tr>";

					echo "</table>";
					echo "</div>";
				}

				/*Accion de actualizacion de observaciones
				 * Accion de cancelación de orden
				 */
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.2' id='wacc$indicePestana.2' value='".accionesATexto(@$accionesPestana[$indicePestana.".2"])."'>";//Grabar observaciones				
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.3' id='wacc$indicePestana.3' value='".accionesATexto(@$accionesPestana[$indicePestana.".3"])."'>";//Cancelar orden
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.4' id='wacc$indicePestana.4' value='".accionesATexto(@$accionesPestana[$indicePestana.".4"])."'>";//Campo observaciones orden

				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.5' id='wacc$indicePestana.5' value='".accionesATexto(@$accionesPestana[$indicePestana.".5"])."'>";//Eliminar ayuda o procedimiento
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.6' id='wacc$indicePestana.6' value='".accionesATexto(@$accionesPestana[$indicePestana.".6"])."'>";//Justificacion
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.7' id='wacc$indicePestana.7' value='".accionesATexto(@$accionesPestana[$indicePestana.".7"])."'>";//Resultado				
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.8' id='wacc$indicePestana.8' value='".accionesATexto(@$accionesPestana[$indicePestana.".8"])."'>";//Fecha realizacion
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.9' id='wacc$indicePestana.9' value='".accionesATexto(@$accionesPestana[$indicePestana.".9"])."'>";//Estado de ayuda o procedimiento
				
				echo "<INPUT TYPE='hidden' name='wacc$indicePestana.10' id='wacc$indicePestana.10' value='".accionesATexto(@$accionesPestana[$indicePestana.".10"])."'>";//Grabacion automatica
				
				/*Agrupacion por centros de costos y ordenes
				 * Si no existen ordenes pendientes se ubica el contenedor solo
				 */				
				if($cuentaExamenesHistoria == 0){
					echo "<div id='cntOtrosExamenes'>";   //Contenedor de agrupacion de examenes por centros de costos	
				} else {
					echo "<div id='cntOtrosExamenes'>";
				}			
						
				
				$whis = $paciente->historiaClinica;
				$wing = $paciente->ingresoHistoriaClinica;
				
				///////////////////////////////////////////////////////////////////
				// 2012-07-10
				// Encabezado de búsqueda rápida de ordenes
				echo "<center><table id='examPendientes'>";   
				// echo "<tr>";
				// echo "<td colspan='4' class='fila2'><b>Filtro para la b&uacute;squeda de procedimienos</b></td>";
				// echo "<td colspan='3'>&nbsp;</td>";
				// echo "</tr>";
				echo "<tr>";

				  $dias = 31;
				  $fechaord = date("Y-m-d");
				  if(!isset($wfecini2))
					$wfecini2 = date("Y-m-d", strtotime("$fechaord -$dias day"));
				  if(!isset($wfecfin2))
					$wfecfin2 = $fechaord;
				  
				  if(!isset($wtiposerv2))
					$wtiposerv2 = "";
					
				  if(!isset($wprocedimiento2))
					$wprocedimiento2 = "";
					
				  $westadodet = 'Realizado';

				// Columna de fecha
				echo "<td colspan='3' class='textoNormal' style='display:none' nowrap>";
				echo "Desde ";
				campoFechaDefecto('wfecini2',$wfecini2);
				echo " hasta ";
				campoFechaDefecto('wfecfin2',$wfecfin2);
				echo "</td>";
				
				$numspan = '1';
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana])
					$numspan = '3';

				echo "<td colspan='".$numspan."' class='textoNormal' nowrap><b>Filtro para la b&uacute;squeda</b></td>";

				// Columna de tipos de servicio
				echo "<td width='80' valign='middle'>";
				echo "<select id='wtiposerv2' name='wtiposerv2' class='textoNormal' onchange='consultarOrdenesPendientes(\"".$wemp_pmla."\",\"".$wempresa."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\")'>";
				$examenesLaboratorio = consultarCentrosAyudasDiagnosticas($whis,$wing);
				$colServiciosExamenes = $examenesLaboratorio;

				echo "<option value=''>Seleccione</option>";
				foreach ($examenesLaboratorio as $examen){
					if($wtiposerv2!=$examen->codigo)
						echo "<option value='$examen->nombre'>$examen->nombre</option>";
					else
						echo "<option value='$examen->nombre' selected>$examen->nombre</option>";
				}
				echo "</select>";
				echo "</td>";

				// Columna de procedimientos
				echo "<td valign='middle'><input class='textoNormal' type='text' name='wprocedimiento2' id='wprocedimiento2' value='".$wprocedimiento2."' onblur='consultarOrdenesPendientes(\"".$wemp_pmla."\",\"".$wempresa."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\")' size='41'></td>";

				// Columna de justificacion
				echo "<td><input class='textoNormal' type='text' name='wjustificacion2' id='wjustificacion2' value='".$wjustificacion2."' onblur='consultarOrdenesPendientes(\"".$wemp_pmla."\",\"".$wempresa."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\")' size='41'></td>";

				// Columna de estados
				echo "<td width='80' valign='middle' style='display:none'>";
				echo "<select id='westadodet2' name='westadodet2' class='textoNormal' onchange='consultarOrdenesPendientes(\"".$wemp_pmla."\",\"".$wempresa."\",\"".$wbasedato."\",\"".$whis."\",\"".$wing."\")'>";

				//$colEstadosExamen = consultarEstadosExamenes();
				foreach ($colEstadosExamen as $estadoExamen){
					if($westadodet2!=$estadoExamen->codigo)
						echo "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
					else
						echo "<option value='$estadoExamen->codigo' selected>$estadoExamen->descripcion</option>";
				}
				echo "</select>";
				echo "</td>";
				echo "</tr>";
				echo "<tr class='encabezadoTabla'>";
				echo "<td><b>Orden</b></td>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<td align='center'><b>Imprimir</b></td>";
					echo "<td align='center'><b>Eliminar</b></td>";
				}
				else{
					echo "<td align='center'><b>Imprimir</b></td>";
				}
				echo "<td style='display:none'><b>Fecha</b></td>";
				echo "<td><b>Tipo de servicio</b></td>";
				echo "<td><b>Procedimiento</b></td>";
				echo "<td><b>Justificación</b></td>";
				echo "<td style='display:none'><b>Estado</b></td>";
				echo "</tr>";

				echo "<tbody id='encabezadoExamenes'>";
				echo "</tbody>";

				//echo "</table>";
				///////////////////////////////////////////////////////////////////

				$centroCostosExamenes = "";
				$consecutivoOrdenExamen = "";

				$caso = "";
				$pasoPorAqui = false;
				$huboOrdenes = false;
				foreach ($colExamenesHistoria as $examen){
//					if($centroCostosExamenes != $examen->tipoDeOrden && $consecutivoOrdenExamen != $examen->numeroDeOrden){
					if( $centroCostosExamenes != $examen->tipoDeOrden ){
						$caso = "1";
					}
					if($centroCostosExamenes == $examen->tipoDeOrden && $consecutivoOrdenExamen == $examen->numeroDeOrden){
						$caso = "4";
					}
					if($centroCostosExamenes == $examen->tipoDeOrden && $consecutivoOrdenExamen != $examen->numeroDeOrden){
						$caso = "3";
					}
					
					/************************************************************************************************************
					 * Queda así para el switch:
					 * - Opcion 1: Crea el encabezado del Servicio
					 * - Opcion 2: Crea la orden
					 * - Cualquier otra opción no hace nada
					 ************************************************************************************************************/
					//Inicio switch
					switch ($caso){
						
						case '1':
							
							$contOrdenes = 0;
							
							if($contExamenes != 0){
								
								if( $huboOrdenes ){
									//Cierre de orden
									echo "</tr>";
									//echo "</div>";
								}
								
								echo "</tbody>";
							}
							$pasoPorAqui = false;
							$huboOrdenes = false;
							
							echo "<div id='".$examen->tipoDeOrden."' style='display:block'>";

							//Crear tabla
							if( true ){
								
								echo "<tbody id='detExamenes".$examen->tipoDeOrden."'>";

								$pasoPorAqui = false;
							}
							
							
						case '2':
						case '3':
							$fechaHoy = date("Y-m-d");
							//$datosAdicionales
							if( strtoupper( $examen->estadoExamen ) == 'P' || strtoupper( $examen->estadoExamen ) == 'PENDIENTE' || strtoupper( $examen->estadoExamen ) == 'PENDIENTERESULTADO' || $examen->fechaARealizar == $fechaHoy ){
								
								//Cierre de tabla
								if( $caso != 1 ){
									
									if( $pasoPorAqui ){
										echo "</tbody>";
										//echo "</table>";
									}

									//Cierre de orden
									if($huboOrdenes){
										
										echo "</tr>";
									}
								}
								
								$huboOrdenes = true;
								//Crear orden

								if($contExamenes % 2 == 0){
									echo "<tr align=center id='trEx$contExamenes' class=fila1>";
								} else {
									echo "<tr align=center id='trEx$contExamenes' class=fila2>";
								}
								
								if($contOrdenes % 2 == 0){
									echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila1>";
								}
								else{
									echo "<td id=del$examen->tipoDeOrden$examen->numeroDeOrden rowspan='{$datosAdicionales[$examen->tipoDeOrden][$examen->numeroDeOrden]['Pendientes']}' class=fila2>";
								}
								$contOrdenes++;
								
								if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
									
									$puedeEliminar = true;									
									if( $usuario->permisosPestanas[$indicePestana]['modifica'] ){
										if( $usuario->codigo != $examen->creadorOrden ){
											$puedeEliminar = false;
										}
									}
									
									if( $puedeEliminar ){
										crearCampo("4","",@$accionesPestana[$indicePestana.".3"],array("onClick"=>"javascript:cancelarOrden('$examen->tipoDeOrden','$examen->numeroDeOrden',this);"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' />");
									}
								}
								
								echo "<a href='#null' onclick=javascript:intercalarElemento(\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\");>";
								echo "<b>&nbsp;&nbsp;&nbsp;<u>Orden Nro. ".$examen->numeroDeOrden."</u></b></a>";
								echo "<div id=\"".$examen->tipoDeOrden."".$examen->numeroDeOrden."\" class='fila2'>";
								
								echo "<div style='display:none'>";
								echo "<br>Observaciones actuales: <br>";
								echo "<textarea rows='4' cols='80' readonly>$examen->observacionesOrden</textarea>";
								
								/*echo "<br><br>Agregar observaciones: <br>";
								crearCampo("2","wtxtobsexamen$examen->tipoDeOrden$examen->numeroDeOrden",@$accionesPestana[$indicePestana.".4"],array("cols"=>"80","rows"=>"4","onKeyPress"=>"return validarEntradaAlfabetica(event);"),"");
								*/
								echo "</div>";
								
								echo "</div>";
								echo "</td>";
								
								$consecutivoOrdenExamen = $examen->numeroDeOrden;
							}

						break;
						
						case '4': break;

					}//Fin switch
					
					//Crear tabla
					if( false && ($caso == 1 || $caso == 3) && (strtoupper( $examen->estadoExamen ) == 'P' || strtoupper( $examen->estadoExamen ) == 'PENDIENTE' || strtoupper( $examen->estadoExamen ) == 'PENDIENTERESULTADO') ){
						
						//echo "<table align='center'>";
						echo "<tr align='center'>";
	
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							echo "<td class='encabezadoTabla'>";
							echo "Acciones";
							echo "</td>";
						}
						
						echo "<tbody id='detExamenes".$examen->tipoDeOrden."'>";
						
						$pasoPorAqui = true;
					}
					
					
					if( strtoupper( $examen->estadoExamen ) == 'P' || strtoupper( $examen->estadoExamen ) == 'PENDIENTE' || strtoupper( $examen->estadoExamen ) == 'PENDIENTERESULTADO' ){
						
						if( !($caso == 1 || $caso == 3) ){
							
							//Examen
							if($contExamenes % 2 == 0){
								echo "<tr id='trEx$contExamenes' class='fila1' align='center'>";
							} else {
								echo "<tr id='trEx$contExamenes' class='fila2' align='center'>";
							}
						}
	
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							echo "<td>";
							
							$puedeEliminar = true;									
							if( $usuario->permisosPestanas[$indicePestana]['modifica'] ){
								if( $usuario->codigo != $examen->creadorItem ){
									$puedeEliminar = false;
								}
							}
							
							if($examen->imprimirExamen!='on')
							{
								$claseImagen = 'opacar';
								$claseAlterna = 'aclarar';
								$activoImpresion = '';
							}
							else
							{
								$claseImagen = 'aclarar';
								$claseAlterna = 'opacar';
								$activoImpresion = ' checked';
							}
							
							// echo "<div id='imgImprimir".$contExamenes."' class='".$claseImagen."' style='display:inline' title='Click para imprimir este procedimiento'><img width='21' height='21' onClick='javascript:cambiarClase(\"imgImprimir".$contExamenes."\",\"opacar\",\"aclarar\"); marcarImpresionExamen(\"\",\"".$examen->tipoDeOrden."\",\"".$examen->numeroDeOrden."\",\"".$examen->codigoExamen."\",\"".$examen->fechaARealizar."\",\"".$contExamenes."\");' src='../../images/medical/hce/icono_imprimir.png' border='0'/> &nbsp;&nbsp; </div>";
							
							echo "<div id='imgImprimir".$contExamenes."' style='display:inline'><img width='18' height='18' src='../../images/medical/hce/icono_imprimir.png' border='0'/><br /><input type='checkbox' id='imprimir_examen".$contExamenes."' name='imprimir_examen".$contExamenes."' onClick='javascript:marcarImpresionExamen(this,\"".$examen->tipoDeOrden."\",\"".$examen->numeroDeOrden."\",\"".$examen->codigoExamen."\",\"".$examen->fechaARealizar."\",\"".$contExamenes."\",\"".$examen->nroItem."\" );' ".$activoImpresion." /></div>";
							
							/*************************************************************************************************************************************
							 * Agrego campos que indican si el examen es pos y si tiene ctc
							 *************************************************************************************************************************************/
			
							$proConCTC = false;
							if( !$examen->esPos ){
								$proConCTC = tieneCTCProcedimientos( $conex, $wbasedato, $whis, $wing, $row['Ordtor'], $row['ordnro'], $row['detite'] , $row['detcod'] );
							}
							echo "<input type='hidden' id='wprotienectc".$contExamenes."' value='".( $proConCTC ? 'off':'on' )."'>";
							echo "<input type='hidden' id='wproespos".$contExamenes."' value='".( $examen->esPos ? 'on':'off' )."'>";
							/************************************************************************************************************************************/
							
							echo "</td>";
							
							echo "<td>";
							if( $puedeEliminar ){
								crearCampo("4","",@$accionesPestana[$indicePestana.".5"],array("onClick"=>"javascript:quitarExamen('$contExamenes','','off');"),"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17' />");
							}
							
							
							echo "</td>";
						}
						else{
							echo "<td>";
							echo "<div id='imgImprimir".$contExamenes."' style='display:inline'><img width='18' height='18' src='../../images/medical/hce/icono_imprimir.png' border='0'/><br /><input type='checkbox' id='imprimir_examen".$contExamenes."' name='imprimir_examen".$contExamenes."' onClick='javascript:marcarImpresionExamen(this,\"".$examen->tipoDeOrden."\",\"".$examen->numeroDeOrden."\",\"".$examen->codigoExamen."\",\"".$examen->fechaARealizar."\",\"".$contExamenes."\",\"".$examen->nroItem."\" );' ".$activoImpresion." /></div>";
							echo "</td>";
						}
	
						//Fecha de solicitado examen
						echo "<td style='display:none;'>";
						if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
							echo "<INPUT TYPE='text' id='wfsol$contExamenes' NAME='wfsol$contExamenes' SIZE=10 readonly class='campo2' value='$examen->fechaARealizar' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>";
							crearCampo("3","btnFechaSol$contExamenes",@$accionesPestana[$indicePestana.".8"],array("height"=>"20","onClick"=>"javascript:calendario3($contExamenes);"),"*");
	//						echo "<INPUT TYPE='button' id='btnFechaSol$contExamenes' onClick='javascript:calendario3($contExamenes);' height=20 value='*'>";
						} else {
							echo "$examen->fechaARealizar&nbsp;";
						}
						echo "</td>";

						// Columna de tipo de servicio
						echo "<td>".$examen->nombreCentroCostos."</td>";	
						
						//Columna de datos
						echo "<td>";
						//echo "$examen->codigoExamen - $examen->nombreExamen";
						echo " $examen->nombreExamen ";
						
						if(isset($examen->protocoloPreparacion) && !empty($examen->protocoloPreparacion)){
							$contenido = str_replace("\r\n","<br>",$examen->protocoloPreparacion);
							
							echo "<span id='$indicePestana-$contProtocolosExamenes' title=' - $contenido'>";
							echo "<img src='../../images/medical/root/info.png' border='0' />";
							echo "</span>";
							$contProtocolosExamenes++;
						}
						
						//Ocultos
						if(true || $wfecha == $examen->fecha){
							echo "<input type=hidden name='wmodificado$indicePestana$contExamenes' id='wmodificado$indicePestana$contExamenes' value='S'>";	
						} else {
							echo "<input type=hidden name='wmodificado$indicePestana$contExamenes' id='wmodificado$indicePestana$contExamenes' value='N'>";
						}					
						echo "<input type=hidden id='wnmexamen$contExamenes' value='$examen->nombreExamen'>";
						echo "<input type=hidden id='hexcco$contExamenes' value='$examen->tipoDeOrden'>";
						echo "<input type=hidden id='hexcod$contExamenes' value='$examen->codigoExamen'>";
						echo "<input type=hidden id='hexcons$contExamenes' value='$examen->numeroDeOrden'>";
						echo "<input type=hidden id='hexnroitem$contExamenes' value='$examen->nroItem'>";
						echo "<input type=hidden id='hiFormHce$contExamenes' value='$examen->firmHCE'>";
						
						echo "</td>";
	
						//Justificacion
						echo "<td><div style='float:left;'>";
						crearCampo("2","wtxtjustexamen$contExamenes",@$accionesPestana[$indicePestana.".6"],array("cols"=>"40","rows"=>"2","onChange"=>"javascript:marcarCambio('$indicePestana','$contExamenes');"),"$examen->justificacion");
						echo "</div><div style='float:right;font-size:10px'>Traer resumen <input type='checkbox' name='chkJust' id='chkJust' style='width:16px;line-height:16px' onClick='traeJustificacionHCE(this,\"wtxtjustexamen".$contExamenes."\");'></div>";
						echo "</td>";
	//					echo "<td><textarea id='wtxtjustexamen$contExamenes' rows='2' cols='40' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>$examen->justificacion</textarea></td>";
						
						//Resultado
						/*echo "<td style='display:none'>";
						crearCampo("2","wtxtobsexamen$contExamenes",@$accionesPestana[$indicePestana.".7"],array("cols"=>"40","rows"=>"2","readonly"=>"readonly","style"=>"display:none"),"$examen->resultadoExamen");*/
						echo "</td>";
	//					echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='40' readonly>$examen->resultadoExamen</textarea></td>";
							
						//Estado del examen
						if($examen->tipoDeOrden == $codigoAyudaHospitalaria){
							echo "<td style='display:none'>";
							$opcionesSeleccion = "";
							/*
							foreach ($colEstadosExamenLab as $estadoExamen){
								if($estadoExamen->codigo == $examen->estadoExamen){
									$opcionesSeleccion .= "<option value='".$estadoExamen->codigo."' selected>$estadoExamen->descripcion</option>";
								} else {
									$opcionesSeleccion .= "<option value='".$estadoExamen->codigo."'>$estadoExamen->descripcion</option>";
								}
							}
							*/
							
							foreach ($colEstadosExamenRol as $estadoExamen){
								if($examen->estadoExamen!=$estadoExamen->codigo)
									$opcionesSeleccion .= "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
								else
									$opcionesSeleccion .= "<option value='$estadoExamen->codigo' selected>$estadoExamen->descripcion</option>";
							}
							
							crearCampo("6","westadoexamen$contExamenes",@$accionesPestana[$indicePestana.".9"],array("class"=>"campo2","onChange"=>"javascript:marcarCambio('$indicePestana','$contExamenes');"),"$opcionesSeleccion");
//							echo "<select name='westadoexamen$contExamenes' id='westadoexamen$contExamenes' class='campo2' onChange='javascript:marcarCambio(\"$indicePestana\",\"$contExamenes\");'>";
//
//							foreach ($colEstadosExamenLab as $estadoExamen){
//								if($estadoExamen->codigo == $examen->estadoExamen){
//									echo "<option value='".$estadoExamen->codigo."' selected>$estadoExamen->descripcion</option>";
//								}else{
//									echo "<option value='".$estadoExamen->codigo."'>$estadoExamen->descripcion</option>";
//								}
//							}
//							echo "</select>";
							echo "</td>";						
						} else {
							echo "<td class='fondoAmarillo' style='display:none'>";
							// 2012-07-13
							// Si el rol puede modificar estado del examen
							if(puedeCambiarEstado()) {

								$estadoActual = $examen->estadoExamen;
								$descripcionEstadoActual = consultarDescripcionEstado($estadoActual);

								$opcionesSeleccion = "";
								
								/*
								$opcionesSeleccion .= "<option value='".$estadoActual."' selected>$descripcionEstadoActual</option>";
								foreach ($colEstadosExamen as $estadoExamen){
									if($estadoExamen->codigo != $estadoActual){
										$opcionesSeleccion .= "<option value='".$estadoExamen->codigo."'>$estadoExamen->descripcion</option>";
									}
								}
								*/
								
								foreach ($colEstadosExamenRol as $estadoExamen){
									if($estadoExamen->codigo!=$estadoActual)
										$opcionesSeleccion .= "<option value='$estadoExamen->codigo'>$estadoExamen->descripcion</option>";
									else
										$opcionesSeleccion .= "<option value='$estadoExamen->codigo' selected>$estadoExamen->descripcion</option>";
								}
								
								crearCampo("6","westadoexamen$contExamenes",@$accionesPestana[$indicePestana.".9"],array("class"=>"campo2","onChange"=>"javascript:marcarCambio('$indicePestana','$contExamenes');"),"$opcionesSeleccion");
							} else {
								echo "<input type='hidden' name='westadoexamen$contExamenes' id='westadoexamen$contExamenes' value='$examen->estadoExamen'>";
								echo $examen->estadoExamen;
							}
							echo "</td>";
						}
						echo "</tr>";
						
						$contExamenes++;
					}
					
					//Fin filas
//					$consecutivoOrdenExamen = $examen->numeroDeOrden;
					$centroCostosExamenes = $examen->tipoDeOrden; 
//					$contExamenes++;
					
				} //FIN FOREACH $colExamenesHistoria as $examen

				$auxContExamenes = $contExamenes;
				
				if($cuentaExamenesHistoria != 0){
					
					if( $pasoPorAqui ){
						//Cierre de tabla
						echo "</tbody>";
						//echo "</table>";
					}

					
					if( $huboOrdenes ){
						//Cierre de orden
						echo "</span>";
						echo "</div>";
					}			

					//Cierro centro de costos
					//echo "</span>";
					//echo "</table>";
					echo "</div>";
					echo "</div>";
				}
				else
				{
					echo "</div>";
				}
				
				echo "</table>";
				
				echo "<br>";
				echo "<input type='HIDDEN' name='cuentaExamenes' id='cuentaExamenes' value='$contExamenes'/>";
			
				/* 1. Consulta de estructura temporal.
				 * 1.1. Si hay registros, carga en pantalla
				 * 1.2. No hay registros
				 * 1.2.1. Consulta de estructura definitiva
				 * 1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
				 * 1.2.3. No hay registros, carga pantalla (sin registros), graba movimientos en temporal
				 */

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana] && $kardexActual->esAnterior && $esFechaActual){
					//Para evitar doble carga de lo definitivo a lo temporal, consulto que lo temporal en la fecha actual no tenga datos en lo temporal
					$colTemporal = consultarExamenesLaboratorioTemporalKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					if(count($colTemporal) == 0){
						cargarExamenesAnteriorATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$fechaGrabacion);
					}
				}
				//1. Consulta de estructura temporal
				$examenesLaboratorio = consultarExamenesLaboratorioTemporalKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				$cuentaExamenes = count($examenesLaboratorio);

				if($cuentaExamenes == 0){
					$examenesLaboratorio = consultarExamenesLaboratorioDefinitivoKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
					$cuentaExamenes = count($examenesLaboratorio);

					if($cuentaExamenes > 0 && $esFechaActual){
						//1.2.2. Si hay registros, carga en estructura temporal y carga en pantalla
						cargarExamenesATemporal($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha,$wfecha);
					}
					
				}
				$contExamenes = 0;

				$fechaTmp = "";
				
//				$colExamenesAnteriores = consultarExamenesLaboratorioAnteriorKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
//				$cuentaExamenesAnteriores = count($colExamenesAnteriores);
				
				//Ordenes HCE
				$colExamenesAnteriores = consultarExamenesAnteriorHCE($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				$cuentaExamenesAnteriores = count($colExamenesAnteriores);
					
				if(false && $cuentaExamenesAnteriores > 0){

					//Detalle examenes anteriores
					echo '<span class="subtituloPagina2" align="center">';
					echo "Detalle examenes en ordenes realizadas";
					echo "</span><br><br>";

					$contExamenes = 0;
					foreach ($colExamenesAnteriores as $examen){
						if($clase == "fila1"){
							$clase = "fila2";
						} else {
							$clase = "fila1";
						}
						if($fechaTmp != $examen->fecha){

							//Seccion nueva del acordeon
							if($contExamenes > 0){
								echo "</table>";
								echo "</p>";
								echo "</div>";
							}
							echo "<a href='#null' onclick=javascript:intercalarExamenAnterior('$examen->fecha');>$examen->fecha</a><br />";
							echo "<div id='ex$examen->fecha' style='display:none'>";

							echo "<p>";
							echo "<table align='center' border=0>";

							echo "<tr class='encabezadoTabla' align='center'>";

							echo "<td>Unidad</td>";
							echo "<td>Numero de orden</td>";
							echo "<td>Examen solicitado</td>";
							echo "<td>Justificacion</td>";
							echo "<td>Resultado</td>";
							echo "<td>Realizar en la fecha</td>";
							echo "<td>Estado</td>";

							echo "</tr>";
						}
							
						echo "<tr id='trEx$contExamenes' class='$clase' align='center'>";
							
						//Fecha del kardex
						$fechaTmp = $examen->fecha;

						echo "<td>$examen->nombreCentroCostos</td>";
						echo "<td>$examen->numeroDeOrden</td>";
						echo "<td>$examen->codigoExamen - $examen->nombreExamen</td>";
						echo "<td><textarea id='wtxtjustexamen$contExamenes' rows='2' cols='30' readonly>$examen->justificacion</textarea></td>";
						//echo "<td><textarea id='wtxtobsexamen$contExamenes' rows='2' cols='30' readonly>$examen->resultadoExamen</textarea></td>";

						//Fecha de solicitado examen
						echo "<td>$examen->fechaARealizar</td>";

						//Estado del examen
						echo "<td>";
						if($examen->tipoDeOrden == $codigoAyudaHospitalaria){
							foreach ($colEstadosExamenLab as $estadoExamen){
								if($estadoExamen->codigo == $examen->estadoExamen){
									echo $estadoExamen->descripcion;
									break;
								}
							}
						} else {
							echo $examen->estadoExamen;
						}
						echo "</td>";
							
						echo "</tr>";

						$contExamenes++;
					}
					echo "</table>";
				}else {
					//Detalle examenes anteriores
					echo '<span class="subtituloPagina2" align="center" style=display:none>';
					echo "No hay examenes anteriores";
					echo "</span><br><br>";
					echo "<div id='exaAnt'>";
				}
				//Fin detalle examenes anteriores
				echo "</div>";
				
				/*
				if( $imprimirUrl ){
					$verAnt = "<center><font size='5'><a onClick='abrirVentanaVerAnteriores(\"$url\");' style='cursor:hand' onMouseOver='this.style.color=\"blue\";' onMouseOut='this.style.color=\"black\";'><u><b>Ver ordenes realizadas</b></u></a></font></center>";
					
					// 2012-07-03
					// Se comenta porque ya se van amostrar las ordenes realizadas en formato de pestañas
					//echo "$verAnt";
				}
				*/
				
				// 2012-07-03
				echo "</div>";
				
				echo "</div>";
				
				/********************************************************
				 **  Fin contenedor de pestaña de ordenes pendientes   **
				 ********************************************************/


				
				// 2012-07-03
				/********************************************************
				 ********************************************************
				 ** Inicio contenedor de pestaña de ordenes realizadas **
				 ********************************************************
				 ********************************************************/
				echo "<div id='fragment-realizadas'>";

				  echo "<div name='detOrdenesRealizadas' id='detOrdenesRealizadas'>";
				  $whis = $paciente->historiaClinica;
				  $wing = $paciente->ingresoHistoriaClinica;
				  $wcco = $paciente->servicioActual;
				  

				  //$dias = 40000;
				  $fechaord = date("Y-m-d");
				  if(!isset($wfecini))
					$wfecini = gmdate("Y-m-d", 0 );
				  if(!isset($wfecfin))
					$wfecfin = date("Y-m-d", strtotime("$fechaord - 1 day"));
					//$wfecfin = $fechaord;
				  
				  if(!isset($wtiposerv))
					$wtiposerv = "";
					
				  if(!isset($wprocedimiento))
					$wprocedimiento = "";
					
				  $westadodet = 'Realizado';

				  // Muestra la lista de ordenes según los parámetros enviados
				  mostrarDetalleOrdenes( $wemp_pmla,$wempresa,$wbasedato,$whis,$wing,$wfecini,$wfecfin,$wtiposerv,$wprocedimiento,$westadodet );				  
				  
				  echo "</div>";

				// 2012-07-03
				echo "</div>";
				/********************************************************
				 **  Fin contenedor de pestaña de ordenes realizadas   **
				 ********************************************************/

				
				//if( $huboOrdenes )
					echo "</div>";
				
				echo "</div>";

				echo "<br /><div align='center'><input id='btnImpPro' type=button value='Imprimir' onclick='grabarKardex(\"impexa\")'> &nbsp; &nbsp; <input id='btnImpProCTC' type=button value='Imprimir CTC' onclick='grabarKardex(\"impctc\")'></div>";

				echo "</div>";

				
				if (isset($mayorIdInfusiones))
				   echo "<input type='HIDDEN' name='cuentaInfusiones' id='cuentaInfusiones' value='".$mayorIdInfusiones."'/>";
			}
			
			//echo "</div>";

			$indicePestana = "5";
			// 2012-07-09
			// Se comenta porque se elimina la pestaña Pendientes
			/*
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-5'>";

				
				//Acciones complementarias
				echo '<span class="subtituloPagina2" align="center">';
				echo "Acciones complementarias y pendientes";
				echo "</span><br><br>";
				
				echo "<table align='center'>";

				echo "<tr>";

				//Acciones predefinidas del arbol para pendientes
				echo "<td rowspan=5 valign=top>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<div class=textoNormal align=left>";
					$colClases = consultarClases("1");

					echo "'".@$accionesPestana[$indicePestana."R$clase->codigo"]."'";
					echo "<ul class='simpleTree' id='arbol1'>";
					echo "<li class='root' id='1'><span>Acciones predefinidas</span>";
					echo "<ul>";
					foreach ($colClases as $clase){
						if($clase->codigo != ''){
							$ramaArbol = "<li id='R$clase->codigo'><span>$clase->descripcion</span>";
							$ramaArbol .= "<ul class='ajax'>";
							$ramaArbol .= "<li id='ajax$clase->codigo'>{url:../../../include/movhos/kardex.inc.php?tree_id=1&consultaAjaxKardex=24&nivelA=$clase->codigo&basedatos=$wbasedato}</li>";
							$ramaArbol .= "</ul>";
							$ramaArbol .= "</li>";
							
							crearCampo("7","ajax$clase->codigo",@$accionesPestana[$indicePestana.".R$clase->codigo"],array(),"$ramaArbol");
							
//							echo "<li id='R$clase->codigo'><span>$clase->descripcion</span>";
//							echo "<ul class='ajax'>";
//							echo "<li id='ajax$clase->codigo'>{url:../../../include/movhos/kardex.inc.php?tree_id=1&consultaAjaxKardex=24&nivelA=$clase->codigo&basedatos=$wbasedato}</li>";
//							echo "</ul>";
//							echo "</li>";
						}
					}
					echo "</ul>";
					echo "</li>";
					echo "</ul>";

					echo "</div>";
				}
				echo "</td>";
				//Cuidados enfermería
				echo "<td width='550' class='fila1' colspan=3 align=center>Cuidados de enfermer&iacute;a";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2",'txCuidados',@$accionesPestana[$indicePestana.".R02"],array("cols"=>"100","rows"=>"8","onFocus"=>"javascript:expandirRama(this,'R02');"),"$kardexActual->cuidadosEnfermeria");
//					echo "<textarea name=txCuidados id=txCuidados rows=8 cols=100 onFocus='javascript:expandirRama(this,\"R02\");'>$kardexActual->cuidadosEnfermeria</textarea>";
				} else {
					echo "<textarea name=txCuidados rows=8 cols=100 readonly>";
					echo $kardexActual->cuidadosEnfermeria;
					echo "</textarea>";
				}
				echo "</td>";

				//Sondas y cateteres
				/*
				echo "<td width='210' class='fila1'>Sondas, cateteres y drenes";

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txtSondas id=txtSondas rows=8 cols=30 onFocus='javascript:expandirRama(this,\"R01\");'>";
					echo $kardexActual->sondasCateteres;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtSondas rows=8 cols=30 readonly>";
					echo $kardexActual->sondasCateteres;
					echo "</textarea>";
				}
				echo "</td>";
				*/
				
				/*
				//Aislamientos
				echo "<td width='20' class='fila1'>Aislamientos";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txAislamientos id=txAislamientos rows=8 cols=30 onFocus='javascript:expandirRama(this,\"R03\");'>";
					echo $kardexActual->aislamientos;
					echo "</textarea>";
				} else {
					echo "<textarea name=txAislamientos rows=8 cols=30 readonly>";
					echo $kardexActual->aislamientos;
					echo "</textarea>";
				}
				echo "</td>";
				*/
				
				/*
				echo "</tr>";

				echo "<tr>";
				*/

				//Curaciones
				/*
				echo "<td width='20' class='fila1'><label for='wcodmed'>Curaciones";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txtCuraciones id=txtCuraciones rows=8 cols=30 onFocus='javascript:expandirRama(this,\"R04\");'>";
					echo $kardexActual->curaciones;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtCuraciones rows=8 cols=30 readonly>";
					echo $kardexActual->curaciones;
					echo "</textarea>";
				}
				echo "</td>";
				*/
/*
				//Terapia respiratoria, fisica y rehabilitacion cardiaca
				echo "<td width='180' class='fila1'>";
				echo "<label for='wcodmed'>Terapias";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txTerapia id=txTerapia rows=8 cols=30 onFocus='javascript:expandirRama(this,\"R05\");'>";
					echo $kardexActual->terapiaRespiratoria;
					echo "</textarea>";
				} else {
					echo "<textarea name=txTerapia rows=8 cols=30 readonly>";
					echo $kardexActual->terapiaRespiratoria;
					echo "</textarea>";
				}
				echo "</td>";
				
				echo "</tr>";
				
				//Interconsulta
				echo "<td width='180' class='fila1'>Interconsulta";

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txtInterconsulta id=txtInterconsulta rows=8 cols=30 onFocus='javascript:expandirRama(this,\"R06\");'>";
					echo $kardexActual->interconsulta;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtInterconsulta rows=8 cols=30 readonly>";
					echo $kardexActual->interconsulta;
					echo "</textarea>";
				}
				echo "</td>";
					
				//Cirugias pendientes
				echo "<td width='180' class='fila1'>Cirug&iacute;as pendientes";

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<textarea name=txtCirugiasPendientes rows=8 cols=30>";
					echo $kardexActual->cirugiasPendientes;
					echo "</textarea>";
				} else {
					echo "<textarea name=txtCirugiasPendientes rows=8 cols=30 readonly>";
					echo $kardexActual->cirugiasPendientes;
					echo "</textarea>";
				}
				echo "</td>";
*/
				/*
				echo "</tr>";

				//Observaciones generales
				echo "<td width='550' class='fila1' colspan=3 align=center>Observaciones generales";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2",'txObservaciones',@$accionesPestana[$indicePestana.".2"],array("cols"=>"100","rows"=>"8"),"$kardexActual->observaciones");
//					echo "<textarea name=txObservaciones rows=8 cols=100>$kardexActual->observaciones</textarea>";
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
			*/
			
			$indicePestana = "6";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-6'>";
				
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";

				//Consulta de los valores actuales para el dextrometer
				$esquemaInsulina = consultarEsquemaInsulina($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				
				if(!isset($esquemaInsulina->codigo)){
					$esquemaInsulina = consultarEsquemaInsulina($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$fechaAyer);
				}
				//Cuerpo de la pagina
				echo "<table align='center' border=0>";

				//Insulina
				$colInsulinas = consultarArticulosEspeciales("",$articuloInsulina);
				echo "<tr><td class='fila1' width=170>Insulina</td>";
				echo "<td class='fila2' align='center'>";

				$opcionesSeleccion = "<option value=''>Seleccione</option>";
				foreach ($colInsulinas as $insulina){
					if($esquemaInsulina->codigo == $insulina->codigo){
						$opcionesSeleccion .= "<option value='".$insulina->codigo."' selected>$insulina->codigo - $insulina->nombre</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$insulina->codigo."'>$insulina->codigo - $insulina->nombre</option>";
					}
				}
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("6","wdexins",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccionNormal"),"$opcionesSeleccion");
//					echo "<select id='wdexins' class='seleccionNormal'>";
				} else {
					crearCampo("6","wdexins",@$accionesPestana[$indicePestana.".1"],array("class"=>"seleccionNormal","disabled"=>""),"$opcionesSeleccion");
//					echo "<select id='wdexins' class='seleccionNormal' disabled>";
				}
				
//				echo "<option value=''>Seleccione</option>";
//				foreach ($colInsulinas as $insulina){
//					if($esquemaInsulina->codigo == $insulina->codigo){
//						echo "<option value='".$insulina->codigo."' selected>$insulina->codigo - $insulina->nombre</option>";
//					}else{
//						echo "<option value='".$insulina->codigo."'>$insulina->codigo - $insulina->nombre</option>";
//					}
//				}
				echo "</select>";
				echo "</td>";
				echo "</tr>";
				
				//Frecuencia del procedimiento
				echo "<tr><td class='fila1' width=270>Frecuencia o condicion</td>";
				echo "<td class='fila2' align='center'>";

				$opcionesSeleccion = "<option value=''>Seleccione</option>";
				foreach ($colCondicionesSuministroInsulinas as $condicion){
					if($esquemaInsulina->frecuencia == $condicion->codigo){
						$opcionesSeleccion .= "<option value='".$condicion->codigo."' selected>$condicion->descripcion</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
					}
				}

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("6","wdexfrecuencia",@$accionesPestana[$indicePestana.".2"],array("class"=>"seleccionNormal"),"$opcionesSeleccion");
//					echo "<select id='wdexfrecuencia' class='seleccionNormal'>";
				} else {
					crearCampo("6","wdexfrecuencia",@$accionesPestana[$indicePestana.".2"],array("class"=>"seleccionNormal","disabled"=>""),"$opcionesSeleccion");
//					echo "<select id='wdexfrecuencia' class='seleccionNormal' disabled>";
				}
//				echo "<option value=''>Seleccione</option>";
//				foreach ($colCondicionesSuministroInsulinas as $condicion){
//					if($esquemaInsulina->frecuencia == $condicion->codigo){
//						echo "<option value='".$condicion->codigo."' selected>$condicion->descripcion</option>";
//					}else{
//						echo "<option value='".$condicion->codigo."'>$condicion->descripcion</option>";
//					}
//				}
//				echo "</select>";
			
				echo "</td>";
				echo "</tr>";
					
				//Esquema dextrometer de los predefinidos
				echo "<tr><td class='fila1' width=270>Esquema dextrometer predefinido</td>";
				echo "<td class='fila2' align='center'>";
				
				echo "<input type=hidden name=whdexesquemaant id=whdexesquemaant value='$esquemaInsulina->codEsquema'>";
				
				$opcionesSeleccion = "<option value=''>Ninguno</option>";
				foreach (consultarMaestroEsquemasInsulina() as $esquema){
					if($esquemaInsulina->codEsquema == $esquema->codigo){
						$opcionesSeleccion .= "<option value='".$esquema->codigo."' selected>Esquema $esquema->codigo</option>";
					} else {
						$opcionesSeleccion .= "<option value='".$esquema->codigo."'>Esquema $esquema->codigo</option>";
					}
				}

				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("6","wdexesquema",@$accionesPestana[$indicePestana.".3"],array("class"=>"seleccionNormal","onChange"=>"javascript: consultarEsquemaInsulina();"),"$opcionesSeleccion");
//					echo "<select id='wdexesquema' class='seleccionNormal' onChange='javascript:consultarEsquemaInsulina();'>";
				}else{
					crearCampo("6","wdexesquema",@$accionesPestana[$indicePestana.".3"],array("class"=>"seleccionNormal","disabled"=>"","onChange"=>"javascript:consultarEsquemaInsulina();"),"$opcionesSeleccion");
//					echo "<select id='wdexesquema' class='seleccionNormal' onChange='javascript:consultarEsquemaInsulina();' disabled>";
				}
//				echo "<option value=''>Seleccione</option>";
//				foreach ($colEsquemasInsulina as $esquema){
//					if($esquemaInsulina->codEsquema == $esquema->codigo){
//						echo "<option value='".$esquema->codigo."' selected>Esquema $esquema->codigo</option>";
//					} else {
//						echo "<option value='".$esquema->codigo."'>Esquema $esquema->codigo</option>";
//					}
//				}
//				echo "</select>";
			
				echo "</td>";
				echo "</tr>";
					
				//Observaciones dextrometer
				echo "<tr><td class='fila1' colspan=2 align=center>Observaciones esquema dextrometer</td>";
				echo "<tr>";
				echo "<td colspan=2 class=fila2 align=center>";
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					crearCampo("2",'txtDextrometer',@$accionesPestana[$indicePestana.".4"],array("cols"=>"40","rows"=>"5"),"$kardexActual->dextrometer");
//					echo "<textarea name=txtDextrometer rows=5 cols=40>$kardexActual->dextrometer</textarea>";
				} else {
					echo "<textarea name=txtDextrometer rows=5 cols=40 readonly>$kardexActual->dextrometer</textarea>";
				}
				echo "</td>";
				echo "</tr>";
				
				if($kardexActual->editable && $vecPestanaGrabacion[$indicePestana]){
					echo "<tr><td align=center colspan=4>";
					if($esquemaInsulina->codigo != '' || trim( $esquemaInsulina->frecuencia ) != '' || trim( $esquemaInsulina->codEsquema ) != '' ){
						echo "<br>";
//						echo "<input type=button id='btnEsquema' value='Seleccionar esquema' onclick='javascript:grabarEsquemaDextrometer();'>&nbsp;|&nbsp;";
						crearCampo("3","btnQuitarEsquema",@$accionesPestana[$indicePestana.".5"],array("onClick"=>"javascript:quitarEsquemaDextrometer();"),"Quitar esquema");
//						echo "<input type=button id='btnQuitarEsquema' value='Quitar esquema' onclick='javascript:quitarEsquemaDextrometer();'>";
					} else {
						echo "<br>";
//						echo "<input type=button id='btnEsquema' value='Seleccionar esquema' onclick='javascript:grabarEsquemaDextrometer();' disabled>&nbsp;|&nbsp;";
						crearCampo("3","btnQuitarEsquema",@$accionesPestana[$indicePestana.".5"],array("onClick"=>"javascript:quitarEsquemaDextrometer();", "disabled"=>""),"Quitar esquema");
//						echo "<input type=button id='btnQuitarEsquema' value='Quitar esquema' onclick='javascript:quitarEsquemaDextrometer();' disabled>";
					}
					echo "</td></tr>";
				}
				echo "</table>";

				//Consulta del esquema actual si existe
				$intervalosEsquema = consultarIntervalosDextrometer($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
				
				if(count($intervalosEsquema) == 0){
					$intervalosEsquema = consultarIntervalosDextrometer($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$fechaAyer);
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
			
			$indicePestana = "7";
			if(isset($vecPestanaGrabacion[$indicePestana])){
				echo "<div id='fragment-7'>";
				
				$accionesPestana = consultarAccionesPestana($indicePestana);
				
				//Indicador para javascript de puede grabar la pestaña
				$estado = $vecPestanaGrabacion[$indicePestana] == true ? "on" : "off";
				echo "<input type=hidden id='pestana' value='$indicePestana'>";
				
				if($mostrarAuditoria){
					echo "<input type='hidden' name='wauditoria' value='1'>";

					echo "<table align='center' border=0>";

					echo "<tr align='center' class='encabezadoTabla'>";
					echo "<td>Usuario</td>";
					echo "<td>Fecha y hora</td>";
					echo "<td>Mensaje</td>";
					echo "<td>Referencia</td>";
					echo "</tr>";

					$historialCambios = consultarHistorialCambiosKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);

					$cont1 = 0;
					foreach($historialCambios as $historia){
						if($cont1 % 2 == 0){
							echo "<tr class='fila1'>";
						} else {
							echo "<tr class='fila2'>";
						}

						echo "<td>$historia->usuario</td>";
						echo "<td>$historia->fecha - $historia->hora</td>";
						echo "<td>$historia->mensaje</td>";
						echo "<td>$historia->descripcion</td>";
							
						echo "</tr>";
							
						$cont1++;
					}
						
					echo "</table>";
				}
				echo "</div>";
			}
			
			
			// if($activarPestanas){
				// foreach($pestanas as $pestana){
					// echo "<div id='frag-$pestana->codigoPestana'>";
					// echo $pestana->codigoPestana;
					// echo "</div>";
				// }
			// }
		
			echo "<center>";
			if($kardexActual->editable)
			{
							
				echo "</form>";
					
				// //Archivo de imagen formula medica
				// echo "<table>";
				
				// echo "<tr>";
				// echo "<td>";
				// echo "<span class='subtituloPagina2'>Imagen orden medica</span>";
				// echo "</td>";
				// echo "</tr>";
				
				// echo "<tr>";
				// echo "<td align=center colspan=2>";
				
				// echo "<form id='file_upload_form' name='carga' method='post' enctype='multipart/form-data' action='cargar_archivo_kardex.php?wemp_pmla=$wemp_pmla&ruta=$ruta&historia=$paciente->historiaClinica&ingreso=$paciente->ingresoHistoriaClinica&fecha=$wfecha&usuario=$usuario->codigo&wbasedato=$wbasedato'>
				// <input name='file' id='file' size='80' type='file'/>&nbsp;&nbsp;&nbsp;<input type='button' name='action' onclick='javascript:cargarArchivo();' value='Cargar...'/>
				// ";
				// echo "<div class='contenedorIframe'>";
				// echo "<iframe id='upload_target' name='upload_target' src='' style='width:100%;height:5em;border:0px dotted #FFFFFF;'allowTransparency='true'>";
				// echo "</iframe>";
				// echo "</div>
				// </div>				
				// </form>";
				
				// if($kardexActual->rutaOrdenMedica != ''){
					// echo "<a id='lkRuta' href='$kardexActual->rutaOrdenMedica' target='_blank' class='vinculo'>Ultimo archivo cargado</a><br />";
					// echo "<input type=hidden id='ordenActual' name='ordenActual' value='$kardexActual->rutaOrdenMedica'><br />";
				// }
				
				// echo "</td>";
				// echo "</tr>";
				
				// //Muestra los archivos cargados en el dia
				// $colArchivos = consultarArchivosDia($paciente->historiaClinica,$paciente->ingresoHistoriaClinica, $wfecha);
				
				// if(count($colArchivos) > 0){
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
				// }
				
				// echo "</table>";
				// echo "<br>";
				
				 echo "<hr>";
				
				if($kardexActual->editable){
					echo "<div align=center>";
					echo "<table>";
					if($usuario->firmaElectronicamente){
						echo "<tr>";
						echo "<td height=30 class='fila1'> &nbsp; &nbsp; Firma digital &nbsp; </td>";
						echo "<td height=30 class='fila2'><input type='password' name='pswFirma' size=40 maxlength=80 id='pswFirma' value='' class=tipo3 onKeyUp='javascript:validarFirmaDigitalHCE();'></td>";
						echo "<td id='tdEstadoFirma' height=30 width='150' class='fondoRojo'>Sin firma digital</td>";
						echo "<td height=30 class='fila2' colspan=3>";

						echo "<div style='display:none'><input type='checkbox' name='wconfdisp' id='wconfdisp' onClick='javascript:marcarKardexConfirmado();' $confirmaAutomaticamente disabled='disabled'>&nbsp;|&nbsp;</div>";

						echo "<div style='display:none' id='btnGrabar1' onclick='javascript:grabarKardex();' style='cursor:pointer;'><img src='/matrix/images/medical/hce/ok.png'></div>";
						echo "<div id='btnGrabarAux' style='cursor:pointer;'><img src='/matrix/images/medical/hce/ok.png'></div>";
						//echo "<button id='btnGrabar1' value='Guardar y salir' onclick='javascript:grabarKardex();' disabled><img src='/matrix/images/medical/hce/ok.png'></button>";

						echo "&nbsp;</td>";
						echo "</tr>";
					} else {
//						echo "<span class='textoMedio'>Confirmar cambios e iniciar dispensaci&oacute;n</span>";
						echo "<tr>";
						echo "<td height=30>";						
						if(true || $kardexActual->confirmado == "on"){
							echo "<input type='checkbox' name='wconfdisp' id='wconfdisp' onClick=\"javascript: document.getElementById('wcconf').checked = this.checked; document.getElementById('wcconf').onclick();\" $confirmaAutomaticamente style='display:none'>";
						}
						else{
							echo "<input type='checkbox' name='wconfdisp' id='wconfdisp' onClick=\"javascript: document.getElementById('wcconf').checked = this.checked; document.getElementById('wcconf').onclick();\">";
						}
//						echo "&nbsp;|&nbsp;";
						//echo "<input type='button' id='btnGrabar1' value='Guardar y salir' onclick='javascript:grabarKardex();'>";
						echo "<div id='btnGrabar1' onclick='javascript:grabarKardex();' style='cursor:pointer;'><img src='/matrix/images/medical/hce/ok.png'></div>";
//						echo "&nbsp;|&nbsp;";
//						echo "<input type='button' value='Regresar' id='regresar' onclick='javascript:inicio();'>";
						echo "&nbsp;</td>";
						echo "</tr>";
					} 
					echo "</table>";
					echo "</div>";
				}
				//else{
				//echo "<br /><input type='button' value='Regresar' onclick='javascript:history.back();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();' ><br />";
				echo "<div id='btnCerrarVentana'><br /><input type=button value='Cerrar ventana' onclick='salir_sin_grabar(); window.parent.cerrarModal();'><br /></div>";
				// echo "<div id='btnCerrarVentana'><br /><input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'><br /></div>";
				//}
// 				if($kardexActual->editable && $usuario->firmaElectronicamente){
// 					echo "<div align=center>";
// 					echo "<table>";
// 					echo "<tr>";
// 					echo "<td height=30 class='fila1'>Firma digital</td>";
// 					echo "<td height=30 class='fila2'><input type='password' name='pswFirma' size=40 maxlength=80 id='pswFirma' value='' class=tipo3 onBlur='javascript:validarFirmaDigitalHCE();'></td>";
// 					echo "<td id='tdEstadoFirma' height=30 class='fondoRojo'>Sin firma digital</td>";
// 					echo "<td height=30 class='fila2' colspan=3>&nbsp;</td>";
// 					echo "</tr>";
// 					echo "</table>";
// 					echo "</div>";
// 				}
// 				if($usuario->firmaElectronicamente){
// 					echo "<span class='textoMedio'>Confirmar orden </span></label><input type='checkbox' name='wconfdisp' id='wconfdisp' onClick='javascript:marcarKardexConfirmado();' disabled>&nbsp;|&nbsp;";
// 					echo "<input type='button' id='btnGrabar2' value='Grabar orden' onclick='javascript:grabarKardex();' disabled>&nbsp;|&nbsp;";
// 				} else {
// 					echo "<span class='textoMedio'>Confirmar cambios </span></label><input type='checkbox' name='wconfdisp' id='wconfdisp' onClick='javascript:marcarKardexConfirmado();'>&nbsp;|&nbsp;";
// 				}
				
//				echo "<span class='textoMedio'>Confirmar orden</span></label><input type='checkbox' name='wconfdisp' id='wconfdisp'>&nbsp;|&nbsp;<input type='button' id='btnGrabar2' value='Grabar orden' onclick='javascript:grabarKardex();' disabled>&nbsp;|&nbsp;";
//				echo "&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarModalHCE();' ><br />";
			} 
			else 
			{
//				echo "<br /><input type='button' value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar ventana' onclick='javascript:cerrarModalHCE();' ><br />";

				// if( isset($matrix) )
				// {
					// echo "<br /><input type='button' value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar ventana' onclick='window.parent.cerrarModal();' ><br />";
				// }
				// else
				// {
					// echo "<br /><input type='button' value='Regresar' onclick='javascript:inicio();'> | <input type=button value='Cerrar ventana' onclick='window.parent.cerrarModal();' ><br />";
				// }
				if( isset($programa) )
					echo "<br /><input type=button value='Cerrar ventana' onclick='cerrarVentana();' ><br />";
				else
					echo "<br /><input type=button value='Cerrar ventana' onclick='window.parent.cerrarModal();' ><br />";
			}
// 			echo "<input type='button' value='Regresar' id='regresar' onclick='javascript:inicio();'>";
			echo "</center>";
			echo "</div>";
			
			echo "<br />";
			echo "<br />";
			
		break;
		case 'c':		//Actualización del kardex
			$mensaje = "";
			$indicePestana = 1;	
			/**
			 * CONSULTA DEL MODO DE GRABACION DE CADA PESTAÑA.
			 */
			$vecPestanas = explode(";",$usuario->pestanasHCE);

			foreach($vecPestanas as $pestana){
				$vecPestanaElemento = explode("|",$pestana);

				if($vecPestanaElemento[0] != ''){
					$vecPestanaGrabacion[$vecPestanaElemento[0]] = ($vecPestanaElemento[2] == 'on');
				}
			}

			// 2012-06-27
			/************************************************
			 * Agrengando mensajeria
			 ************************************************/
			//Campo oculto que indica de que programa se abrio
			
			echo "<INPUT type='hidden' id='mesajeriaPrograma' value='Ordenes'>";
			 
			$kardexGrabar = new kardexDTO();

			//Captura de parametros. Encabezado del kardex
			$kardexGrabar->historia = $paciente->historiaClinica;
			$kardexGrabar->ingreso = $paciente->ingresoHistoriaClinica;
			$kardexGrabar->fechaCreacion = $wfecha;
			$kardexGrabar->horaCreacion = date("H:i:s");
			$kardexGrabar->fechaGrabacion = $wfechagrabacion;
			$kardexGrabar->usuario = $wuser;
			$kardexGrabar->confirmado = $confirmado;
			$kardexGrabar->esPrimerKardex = $primerKardex;
			$kardexGrabar->rutaOrdenMedica = $rutaOrdenMedica;
			$kardexGrabar->centroCostos = $usuario->centroCostosGrabacion;			
			$kardexGrabar->usuarioQueModifica = $usuario->codigo;
			$kardexGrabar->firmaDigital = $firmaDigital;
			$kardexGrabar->noAcumulaSaldoDispensacion = $wkardexnoacumula;

			if(isset($vecPestanaGrabacion[$indicePestana]) && $vecPestanaGrabacion[$indicePestana]){
				$kardexGrabar->talla = $txTalla;
				$kardexGrabar->peso = $txPeso;
				$kardexGrabar->diagnostico = str_replace("|",chr(13),$txDiag);
				$kardexGrabar->antecedentesAlergicos = str_replace("|",chr(13),$txAlergias);
				$kardexGrabar->antecedentesPersonales = str_replace("|",chr(13),$txAntecedentesPersonales);
			}

			$indicePestana = 5;
			if(isset($vecPestanaGrabacion[$indicePestana]) && $vecPestanaGrabacion[$indicePestana]){
				$kardexGrabar->observaciones = str_replace("|",chr(13),$txObservaciones);
				$kardexGrabar->cuidadosEnfermeria = str_replace("|",chr(13),$txCuidados);
			}
			
			$indicePestana = 6;
			if(isset($vecPestanaGrabacion[$indicePestana]) && $vecPestanaGrabacion[$indicePestana]){
				$kardexGrabar->dextrometer = str_replace("|",chr(13),$txtDextrometer);
			}
			
			$indicePestana = 10;
			if(isset($vecPestanaGrabacion[$indicePestana]) && $vecPestanaGrabacion[$indicePestana]){
				$kardexGrabar->consentimientos = str_replace("|",chr(13),$txtConsentimientos);
				$kardexGrabar->medidasGenerales = str_replace("|",chr(13),$txMedidas);
				$kardexGrabar->procedimientos = str_replace("|",chr(13),$txProcedimientos);
				$kardexGrabar->terapiaRespiratoria = str_replace("|",chr(13),$txTerapia);
				$kardexGrabar->curaciones = str_replace("|",chr(13),@$txtCuraciones);	//Esta sin uso en ordenes
				$kardexGrabar->sondasCateteres = str_replace("|",chr(13),@$txtSondas);	//Esta sin uso en ordenes
				$kardexGrabar->interconsulta = str_replace("|",chr(13),$txtInterconsulta);
				$kardexGrabar->obsDietas = str_replace("|",chr(13),$txtObsDietas);
				$kardexGrabar->cirugiasPendientes = str_replace("|",chr(13),$txtCirugiasPendientes);
				$kardexGrabar->terapiaFisica = str_replace("|",chr(13),@$txTerapiaFisica);
				$kardexGrabar->rehabilitacionCardiaca = str_replace("|",chr(13),@$txRehabilitacionCardiaca);
				$kardexGrabar->aislamientos = str_replace("|",chr(13),$txAislamientos);
			}
			
			$kardexGrabar->indicaciones = $windicaciones;
			
			echo "<input type='hidden' name='whistoria' value='$kardexGrabar->historia'>";
			echo "<input type='hidden' name='wingreso' value='$kardexGrabar->ingreso'>";
			echo "<input type='hidden' name='wfecha' value='$wfecha'>";
			echo "<input type='HIDDEN' name='elementosKardex' id=elementosKardex value='0'/>";

			if(!existeEncabezadoKardex($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha)){
				crearKardex($kardexGrabar);	
				$mensaje = "El kardex ha sido creado con éxito";
			} else {
				//Actualiza SOLO encabezado
				actualizarKardex($kardexGrabar,$vecPestanaGrabacion);
				$mensaje = "El kardex ha sido actualizado con éxito";
			}
			
			$esPrimerKardex = false;
			if($primerKardex == "S"){
				$esPrimerKardex = true;	
			}
			
			//Carga de temporal a definitivo de los componentes del kardex, para todos, debe borrarse lo anterior
			cargarInfusionesADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion);
			cargarArticulosADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$esPrimerKardex,$firmaDigital);
			cargarExamenesADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$firmaDigital);
			cargarDietasADefinitivo($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$firmaDigital);
			actualizarUsuarioDextrometer( $conex, $wbasedato,$paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$usuario->codigo,$firmaDigital);
			
			eliminarDatoTemporalProcedimiento( $wbasedato,$wbasedatohce, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica );
			
			/**
			 * HCE
			 */
			//Grabación de alertas via ordenes
			if(isset($kardexGrabar->antecedentesAlergicos) && $kardexGrabar->antecedentesAlergicos != ''){
				grabarAlertaHCE($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfechagrabacion,$kardexGrabar->antecedentesAlergicos,$kardexGrabar->usuario);
			}
			 
			//Si la orden está firmada, incremento los consecutivos de los centros de costos
			if(isset($kardexGrabar->firmaDigital) && !empty($kardexGrabar->firmaDigital)){
//				incrementarConsecutivoCentroCostos($paciente->historiaClinica,$paciente->ingresoHistoriaClinica,$wfecha);
			}
				
			echo "</form>";
				
			//Si el kardex se ha grabado este hidden se cargará en las paginas
			echo "<input type='hidden' name='whgrabado' value='on'>";
				
			echo '<span class="subtituloPagina2" align="center">';
			echo 'La orden médica se ha grabado correctamente... Regresando a la consulta';
			echo "</span><br><br>";

//			echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:cerrarModalHCE();'></div>";
//			echo "Quitar linea........."; return;


			//if( isset($editable) && $editable != 'on' ){
				funcionJavascript("window.parent.cerrarModal();");
				// funcionJavascript("cerrarVentana();");			
			// }
			// else{
				// funcionJavascript( 'activarModalIframe("","nombreIframe","../../movhos/procesos/impresionMedicamentosControl.php?wemp_pmla='.$wemp_pmla.'&historia='.$paciente->historiaClinica.'&ingreso='.$paciente->ingresoHistoriaClinica.'&fechaKardex='.$wfechagrabacion.'&consultaAjax=10","-1","0" )' );
				
				// funcionJavascript("window.parent.cerrarModal();");

				// // 2012-10-31
				// // Llama el programa para validación de grabación de CTC y medicamentos de control
				// //funcionJavascript( 'activarModalIframe("","nombreIframe","generarCTCparaHCE.php?wemp_pmla='.$wemp_pmla.'&historia='.$paciente->historiaClinica.'&ingreso='.$paciente->ingresoHistoriaClinica.'&fechaKardex='.$wfechagrabacion.'","-1","0")' );
			
			// }
			
//			if($kardexGrabar->confirmado == "on"){
//				funcionJavascript("inicio();");
//			} else {
//				funcionJavascript("consultarKardex();");	
//			}
			break;
		default:
			echo "<table border=0>";
			echo "<tr>";
			echo "<td class='subtituloPagina2' width=350>";
			echo "Consulta o generación de orden médica";
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

			echo "<td class='fila1' align=center rowspan=2><b><font size=3>Paciente</font></b></td>";
			echo "<td class='fila2' align=center colspan=3 rowspan=2><b><font size=3>";
			echo $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$paciente->apellido2;
			echo "</font></b></td>";

			echo "</tr>";

			echo "<tr>";

			//Servicio actual y habitacion
			echo "<td class='fila1'>Servicio actual</td>";
			echo "<td class='fila2'>";
			echo $paciente->nombreServicioActual;
			echo "</td>";

			//Enfermera(o) que genera
			echo "<tr>";
			echo "<td class='fila1'>Usuario que actualiza (Codigo y nombre del Rol)</td>";
			echo "<td class='fila2'>";
			echo "$usuario->codigo - $usuario->descripcion. <br>$usuario->nombreCentroCostos ($usuario->codigoRolHCE-$usuario->nombreRolHCE)";
			echo "</td>";
			
			echo "<td class='fila1'>Fecha y hora de generaci&oacute;n</td>";
			echo "<td class='fila2'>";
			if(isset($kardexActual)){
				echo "".$kardexActual->fechaCreacion." - ".$kardexActual->horaCreacion;
			} else {
				echo "<br>";
			}
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
			echo round(date("Y")-$vecAnioNacimiento[0]);
			echo "</td>";

			//echo "<td class='fila1'>Ultimo mvto hospitalario</td>";
			
			if($paciente->altaDefinitiva == 'on'){
				//echo "<td class='articuloControl'>";
			} else {
				//echo "<td class='fondoAmarillo'>";
			}
			//echo $paciente->ultimoMvtoHospitalario;						
			//echo "</td>";
						
			//Calculo de dias de hospitalizcion desde ingreso
			$diaActual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			$fecha = explode("-",$paciente->fechaIngreso);
			$diaIngreso = mktime(0,0,0,$fecha[1],$fecha[2],$fecha[0]);

			$diasHospitalizacion = ROUND(($diaActual - $diaIngreso)/(60*60*24));

			//echo "<td class='fila1'>D&iacute;as de hospitalizaci&oacute;n</td>";
			//echo "<td class='fila2'>";
			//echo "".$diasHospitalizacion;
			//echo "</td>";
			
			//echo "<td colspan=2>&nbsp;</td>";
			
			//echo "</tr>";
			
			//echo "<tr>";		
			
			//Responsable
			echo "<td class='fila1'>Entidad responsable</td>";
			echo "<td class='fila2' colspan='3'>";
			echo "$paciente->numeroIdentificacionResponsable - $paciente->nombreResponsable";			
			echo "</td>";
			
			//Fecha y hora de ingreso al servicio actual
			//echo "<td class='fila1'>Fecha de ingreso al servicio actual</td>";
			//echo "<td class='fila2'>";
			//echo "$paciente->fechaHoraIngresoServicio";						
			//echo "</td>";
			
			//echo "<td class='fila1'>";
			
			//Boton de vistas asociadas
//			echo "<a href='#' id='btnModal001' name='btnModal001' class=tipo3V onClick='javascript:abrirModalHCE();'>Vistas Asociadas</A>";
			
			//echo "</td>";
			//echo "<td class='fila2'>&nbsp;";
//			echo "<a href='#null' onclick='return fixedMenu.show();'>Alergias</a>";						
			//echo "</td>";
			
			echo "</tr>";
				
			echo "<tr>";
			echo "<td height=30 colspan=6>&nbsp;</td>";
			echo "</tr>";
			
			echo "</table>";
			
			//Cuerpo de la pagina
			echo "<table align='center' border=0>";

			//Ingreso de fecha de consulta
			echo '<span class="subtituloPagina2">';
			echo 'Fecha de consulta de orden';
			echo "</span>";
			echo "<br>";
			echo "<br>"; 

			//Por fecha generacion kardex
			echo "<tr>";
			echo "<td class='fila2' align='center' width=250>";
			campoFecha("wfecha");
			echo "</td></tr>";

			//Si la fecha del servidor difiere de la del equipo donde se esta digitando el kardex
			$fechaActual = date("Y-m-d");
			$horaActual = date("H:i:s");
			
			funcionJavascript("validarFechayHoraLocal('".$fechaActual."','".$horaActual."');");
			
			echo "<tr><td align=center colspan=4><br><input id='btnConsultar' type=button value='Consultar o generar' onclick='javascript:consultarKardex();'></td>";
//			echo "<td align=center colspan=><br><input id='' type=button value='Cerrar ordenes' onclick='javascript:cerrarModal();'></td>";
			echo "</tr>";
			echo "</table>";
			break;			
	}
	liberarConexionBD($conex);
}
?>
<script type="text/javascript">
var elementosDetalle = 0, elementosAnalgesia = 0, elementosNutricion = 0, elementosQuimioterapia = 0, cuentaExamenes = 0, cuentaInfusiones = 0; if(document.forms.forma.elementosKardex) elementosDetalle = document.forms.forma.elementosKardex.value; if(document.forms.forma.elementosAnalgesia) elementosAnalgesia = document.forms.forma.elementosAnalgesia.value; if(document.forms.forma.elementosNutricion)elementosNutricion = document.forms.forma.elementosNutricion.value; if(document.forms.forma.elementosQuimioterapia)elementosQuimioterapia = document.forms.forma.elementosQuimioterapia.value; if(document.forms.forma.cuentaExamenes)cuentaExamenes = document.forms.forma.cuentaExamenes.value; if(document.forms.forma.cuentaInfusiones)cuentaInfusiones = document.forms.forma.cuentaInfusiones.value; if(document.getElementById("fixeddiv")) { fixedMenuId = "fixeddiv"; var fixedMenu = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId) : document.all ? document.all[fixedMenuId] : document.layers[fixedMenuId]}; fixedMenu.computeShifts = function() { fixedMenu.shiftX = fixedMenu.hasInner ? pageXOffset : fixedMenu.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu.shiftX += fixedMenu.targetLeft > 0 ? fixedMenu.targetLeft : (fixedMenu.hasElement ? document.documentElement.clientWidth : fixedMenu.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu.targetRight - fixedMenu.menu.offsetWidth; fixedMenu.shiftY = fixedMenu.hasInner ? pageYOffset : fixedMenu.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu.shiftY += fixedMenu.targetTop > 0 ? fixedMenu.targetTop : (fixedMenu.hasElement ? document.documentElement.clientHeight : fixedMenu.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu.targetBottom - fixedMenu.menu.offsetHeight }; fixedMenu.moveMenu = function() { fixedMenu.computeShifts(); if(fixedMenu.currentX != fixedMenu.shiftX || fixedMenu.currentY != fixedMenu.shiftY) { fixedMenu.currentX = fixedMenu.shiftX; fixedMenu.currentY = fixedMenu.shiftY; if(document.layers) { fixedMenu.menu.left = fixedMenu.currentX; fixedMenu.menu.top = fixedMenu.currentY }else { fixedMenu.menu.style.left = fixedMenu.currentX + "px"; fixedMenu.menu.style.top = fixedMenu.currentY + "px" } }fixedMenu.menu.style.right = ""; fixedMenu.menu.style.bottom = "" }; fixedMenu.floatMenu = function() { fixedMenu.moveMenu(); setTimeout("fixedMenu.floatMenu()", 20) }; fixedMenu.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu.init = function() { if(fixedMenu.supportsFixed())fixedMenu.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu.menu : fixedMenu.menu.style; fixedMenu.targetLeft = parseInt(a.left); fixedMenu.targetTop = parseInt(a.top); fixedMenu.targetRight = parseInt(a.right); fixedMenu.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu.addEvent(window, "onscroll", fixedMenu.moveMenu); fixedMenu.floatMenu() } }; fixedMenu.addEvent(window, "onload", fixedMenu.init); fixedMenu.hide = function() { fixedMenu.menu.style.display = "none"; return false }; fixedMenu.show = function() { fixedMenu.menu.style.display = "block"; return false } }if(document.getElementById("fixeddiv2")) { fixedMenuId2 = "fixeddiv2"; var fixedMenu2 = {hasInner:typeof window.innerWidth == "number", hasElement:document.documentElement != null && document.documentElement.clientWidth, menu:document.getElementById ? document.getElementById(fixedMenuId2) : document.all ? document.all[fixedMenuId2] : document.layers[fixedMenuId2]}; fixedMenu2.computeShifts = function() { fixedMenu2.shiftX = fixedMenu2.hasInner ? pageXOffset : fixedMenu2.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu2.shiftX += fixedMenu2.targetLeft > 0 ? fixedMenu2.targetLeft : (fixedMenu2.hasElement ? document.documentElement.clientWidth : fixedMenu2.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu2.targetRight - fixedMenu2.menu.offsetWidth; fixedMenu2.shiftY = fixedMenu2.hasInner ? pageYOffset : fixedMenu2.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu2.shiftY += fixedMenu2.targetTop > 0 ? fixedMenu2.targetTop : (fixedMenu2.hasElement ? document.documentElement.clientHeight : fixedMenu2.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu2.targetBottom - fixedMenu2.menu.offsetHeight }; fixedMenu2.moveMenu = function() { fixedMenu2.computeShifts(); if(fixedMenu2.currentX != fixedMenu2.shiftX || fixedMenu2.currentY != fixedMenu2.shiftY) { fixedMenu2.currentX = fixedMenu2.shiftX; fixedMenu2.currentY = fixedMenu2.shiftY; if(document.layers) { fixedMenu2.menu.left = fixedMenu2.currentX; fixedMenu2.menu.top = fixedMenu2.currentY }else { fixedMenu2.menu.style.left = fixedMenu2.currentX + "px"; fixedMenu2.menu.style.top = fixedMenu2.currentY + "px" } }fixedMenu2.menu.style.right = ""; fixedMenu2.menu.style.bottom = "" }; fixedMenu2.floatMenu = function() { fixedMenu2.moveMenu(); setTimeout("fixedMenu2.floatMenu()", 20) }; fixedMenu2.addEvent = function(a, b, f) { if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { a[b + "_num"] = 0; if(typeof a[b] == "function") { a[b + 0] = a[b]; a[b + "_num"]++ }a[b] = function(c) { var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g } }for(var e = 0;e < a[b + "_num"];e++)if(a[b + e] == f)return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu2.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu2.init = function() { if(fixedMenu2.supportsFixed())fixedMenu2.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu2.menu : fixedMenu2.menu.style; fixedMenu2.targetLeft = parseInt(a.left); fixedMenu2.targetTop = parseInt(a.top); fixedMenu2.targetRight = parseInt(a.right); fixedMenu2.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu2.addEvent(window, "onscroll", fixedMenu2.moveMenu); fixedMenu2.floatMenu() } }; fixedMenu2.addEvent(window, "onload", fixedMenu2.init); fixedMenu2.hide = function() { if(fixedMenu2.menu.style.display != "none")fixedMenu2.menu.style.display = "none"; return false }; fixedMenu2.show = function(a) { document.getElementById("wtipoprot"); var b = 0; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)document.forms.forma.wtipoprot[b].disabled = true; for(b = 0;b < document.forms.forma.wtipoprot.length;b++)if(a.indexOf(document.forms.forma.wtipoprot[b].value) != -1) { document.forms.forma.wtipoprot[b].checked = true; document.forms.forma.wtipoprot[b].disabled = false }fixedMenu2.menu.style.display = "block"; return false } };

if(document.getElementById("movExamenes")){ 

	fixedMenuId2 = 'movExamenes'; 

	var movExamenes = { hasInner: typeof(window.innerWidth) == 'number', hasElement: document.documentElement != null && document.documentElement.clientWidth, menu: document.getElementById ? document.getElementById(fixedMenuId2) : document.all ? document.all[fixedMenuId2] : document.layers[fixedMenuId2] };

	movExamenes.computeShifts = function() { movExamenes.shiftX = movExamenes.hasInner ? pageXOffset : movExamenes.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; if (movExamenes.targetLeft > 0) movExamenes.shiftX += movExamenes.targetLeft; else { movExamenes.shiftX += (movExamenes.hasElement ? document.documentElement.clientWidth : movExamenes.hasInner ? window.innerWidth - 20	: document.body.clientWidth) - movExamenes.targetRight- movExamenes.menu.offsetWidth; } movExamenes.shiftY = movExamenes.hasInner ? pageYOffset : movExamenes.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; if (movExamenes.targetTop > 0) movExamenes.shiftY += movExamenes.targetTop; else {	 movExamenes.shiftY += (movExamenes.hasElement ? document.documentElement.clientHeight : movExamenes.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - movExamenes.targetBottom - movExamenes.menu.offsetHeight; } };

    movExamenes.moveMenu = function(){	 movExamenes.computeShifts(); if (movExamenes.currentX != movExamenes.shiftX || movExamenes.currentY != movExamenes.shiftY) { movExamenes.currentX = movExamenes.shiftX; movExamenes.currentY = movExamenes.shiftY; if (document.layers) { movExamenes.menu.left = movExamenes.currentX; movExamenes.menu.top = movExamenes.currentY;} else { movExamenes.menu.style.left = movExamenes.currentX + 'px'; movExamenes.menu.style.top = movExamenes.currentY + 'px'; }} movExamenes.menu.style.right = ''; movExamenes.menu.style.bottom = ''; };

    movExamenes.floatMenu = function() { movExamenes.moveMenu(); setTimeout('movExamenes.floatMenu()', 20); };

 // addEvent designed by Aaron Moore
 	movExamenes.addEvent = function(element, listener, handler){ if(typeof element[listener] != 'function' || typeof element[listener + '_num'] == 'undefined') { element[listener + '_num'] = 0;if (typeof element[listener] == 'function')
		 {
			 element[listener + 0] = element[listener];
			 element[listener + '_num']++;
		 }
		 element[listener] = function(e)
		 {
			 var r = true;
			 e = (e) ? e : window.event;
			 for(var i = 0; i < element[listener + '_num']; i++)
				 if(element[listener + i](e) === false)
					 r = false;
			 return r;
		 }
	 }

	 //if handler is not already stored, assign it
	 for(var i = 0; i < element[listener + '_num']; i++)
		 if(element[listener + i] == handler)
			 return;
	 element[listener + element[listener + '_num']] = handler;
	 element[listener + '_num']++;
 };

 movExamenes.supportsFixed = function()
 {
	 var testDiv = document.createElement("div");
	 testDiv.id = "testingPositionFixed";
	 testDiv.style.position = "fixed";
	 testDiv.style.top = "0px";
	 testDiv.style.right = "0px";
	 document.body.appendChild(testDiv);
	 var offset = 1;
	 if (typeof testDiv.offsetTop == "number"
		 && testDiv.offsetTop != null 
		 && testDiv.offsetTop != "undefined")
	 {
		 offset = parseInt(testDiv.offsetTop);
	 }
	 if (offset == 0)
	 {
		 return true;
	 }

	 return false;
 };

 movExamenes.init = function()
 {
	 if (movExamenes.supportsFixed())
		 movExamenes.menu.style.position = "fixed";
	 else
	 {
		 var ob = document.layers ? movExamenes.menu : movExamenes.menu.style;

		 movExamenes.targetLeft = parseInt(ob.left);
		 movExamenes.targetTop = parseInt(ob.top);
		 movExamenes.targetRight = parseInt(ob.right);
		 movExamenes.targetBottom = parseInt(ob.bottom);

		 if (document.layers)
		 {
			 menu.left = 0;
			 menu.top = 0;
		 }
		 movExamenes.addEvent(window, 'onscroll', movExamenes.moveMenu);
		 movExamenes.floatMenu();
	 }
 };

 movExamenes.addEvent(window, 'onload', movExamenes.init);

 movExamenes.hide = function()
 {
	 if(movExamenes.menu.style.display != 'none'){
		 movExamenes.menu.style.display='none';
	 }
	 return false;
 }

 movExamenes.show = function(tipos)
 {
//	 debugger;
	 movExamenes.menu.style.display='block';
	 return false;
 }
}
</script>
</body>
</html>