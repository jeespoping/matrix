<?php
include_once("conex.php"); 
header("Content-Type: text/html;charset=ISO-8859-1"); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
  <script src="../../../include/root/print.js" type="text/javascript"></script>
  <link type='text/css' href='HCE.css' rel='stylesheet'> 
  <title>Ordenes Médicas</title>

  <script type='text/javascript'>
	/******************************************************************
	 * Realiza una llamada ajax a una pagina
	 *
	 * met:		Medtodo Post o Get
	 * pag:		Página a la que se realizará la llamada
	 * param:	Parametros de la consulta
	 * as:		Asincronro? true para asincrono, false para sincrono
	 * fn:		Función de retorno del Ajax, no requerido si el ajax es sincrono
	 *
	 * Nota:
	 * - Si la llamada es GET las opciones deben ir con la pagina.
	 * - Si el ajax es sincrono la funcion retorna la respuesta ajax (responseText)
	 * - La funcion fn recibe un parametro, el cual es el objeto ajax
	 ******************************************************************/
	function consultasAjax( met, pag, param, as, fn ){

		this.metodo = met;
		this.parametros = param;
		this.pagina = pag;
		this.asc = as;
		this.fnchange = fn;

		try{
			this.ajax=nuevoAjax();

			this.ajax.open( this.metodo, this.pagina, this.asc );
			this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			this.ajax.send(this.parametros);

			if( this.asc ){
				var xajax = this.ajax;
	//			this.ajax.onreadystatechange = this.fnchange;
				this.ajax.onreadystatechange = function(){ fn( xajax ) };

				if ( !estaEnProceso(this.ajax) ) {
					this.ajax.send(null);
				}
			}
			else{
				return this.ajax.responseText;
			}
		}catch(e){	}
	}
	/************************************************************************/		
	// Llama al script de impresión de los CTC para que traiga la impresión del CTC
	// del artículo enviado
	function consultarCTCArticulo( his, ing, art, div )
	{
		var vwemp_pmla = document.getElementById( "wemp_pmla" );

		var parametros = "imprimir=on&historia="+his+"&art="+art+"&boton_imp=no";

		consultasAjax( "POST", "impresionCTCArticulosNoPosIDC.php?wemp_pmla="+vwemp_pmla.value,
						parametros,
						true,
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								document.getElementById( div ).innerHTML = '<div style="page-break-after: always;">'+ajax.responseText+'</div>';
							}
						}
					);
	}

	// Llama a este mismo script para hacer la impresión solo del medicamento correspondiente al CTC
	// para eso se envia el parámetro art
	function consultarArticulo( his, ing, art, div )
	{
		var vwemp_pmla = document.getElementById( "wemp_pmla" );

		var parametros = "whistoria="+his+"&wingreso="+ing+"&art="+art+"&boton_imp=no";

		consultasAjax( "POST", "ordenes_imp.php?wemp_pmla="+vwemp_pmla.value,
						parametros,
						true,
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								document.getElementById( div ).innerHTML = '<div style="page-break-after: always;">'+ajax.responseText+'</div>';
							}
						}
					);
	}


	// Llama a este mismo script para hacer la impresión solo del medicamento correspondiente al CTC
	// para eso se envia el parámetro pro
	function consultarProcedimiento( his, ing, pro, div )
	{
		var vwemp_pmla = document.getElementById( "wemp_pmla" );

		var parametros = "whistoria="+his+"&wingreso="+ing+"&pro="+pro+"&boton_imp=no";

		consultasAjax( "POST", "ordenes_imp.php?wemp_pmla="+vwemp_pmla.value,
						parametros,
						true,
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								document.getElementById( div ).innerHTML = '<div style="page-break-after: always;">'+ajax.responseText+'</div>';
							}
						}
					);
	}

	function cerrarVentana()
	 {
      top.close();		  
     }
	
	function enviarPdf(historia, ingreso, rutaArchivo, nombreArchivo, nombrePaciente, nombreEmpresa, wbasedato, usuario, tiposOrdenesGeneradas, nombreEntidad)
	{
		$("#btnEnviarPdf").prop( "disabled",true );
		$("#msjEspere").show();
		
		const asunto = "";
		const mensaje = "";
		
		$.ajax({
			url: "envioCorreoHCEOrdenes.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'enviarPdf',
				wemp_pmla		: $('#wemp_pmla').val(),
				historia		: historia,
				ingreso			: ingreso,
				email			: $('#emailEnviarCorreo').val(),
				rutaArchivo		: rutaArchivo,
				nombreArchivo	: nombreArchivo,
				asunto			: asunto,
				mensaje			: mensaje,
				prefijo			: 'OM',
				wbasedatoMovhos	: wbasedato,
				usuario			: usuario,
				envioPaciente	: $("#envioPaciente").val(),
				nombrePaciente	: nombrePaciente,
				nombreEntidad	: nombreEntidad,
				nombreEmpresa	: nombreEmpresa,
				tiposOrdenesGeneradas	: tiposOrdenesGeneradas,
				},
				async: false,
				success:function(respuesta) {
					$("#btnEnviarPdf").prop( "disabled",false );
					$("#msjEspere").hide();
					alert(respuesta)
				}
		});
	}
	
$(document).ready(function()
	{
		$(".printer").bind("click",function()
		{	
			$(".areaimprimir").printArea({			
				
				popClose: false,
				popTitle : 'Ordenes',
				popHt    : 500,
                popWd    : 1200,
                popX     : 200,
                popY     : 200,
				
				});
		});
		
		//$('.printer').click();
	
	})
  </script>

  <style type="text/css">
	
	table, td {
		font-family: Arial;
		font-size: 6.5pt;
	}
	.encabezado {
		font-size: 6.5pt;
		font-weight: bold;
	}
	.encabezadoExamen {
		text-align: right;
		font-size: 8pt;
	}
	.encabezadoEmpresa {
		text-align: left;
		font-size: 6.5pt;
	}
	.filaEncabezado {
		border-bottom: 1px solid rgb(51, 51, 51);
		border-left: 1px solid rgb(51, 51, 51);
	}
	.filaEncabezadoFin {
		border-bottom: 1px solid rgb(51, 51, 51);
		border-right: 1px solid rgb(51, 51, 51);
		border-left: 1px solid rgb(51, 51, 51);
	}
	.campoFirma {
		border-bottom: 1px solid rgb(51, 51, 51);
		width:208px;
		height:31px !important;
		height:27px;
	}
	.descripcion
	{
		font-size: 5.5pt;
		text-align:justify;
	}
 	.total
	{
		font-size: 6.5pt;
		height: 27px;
		text-align:right;
		text-valign:bottom;
	}

  </style>
</head>


<body>

<?php
    /******************************************************************
    * 	  			IMPRESIÓN DE ORDENES MÉDICAS					  *
    * ----------------------------------------------------------------*
    * Este script imprime la orden médica .							  *
    ******************************************************************/
	/*
	 * Autor: John M. Cadavid. G, Jonatan Lopez.
	 * Fecha creacion: 2013-03-01
	 * Modificado:
	 */
	/******************************************************************
	 *  Modificaciones:
	 * ================================================================================================================================================
	 *	Enero 21 de 2022: Marlon Osorio
							- Se parametrizo el centro de costos de Dispensacion Servicio Farmaceutico
	  ================================================================================================================================================
	 *	Mayo 4 de 2020: Jessica Madrid
							- Para el envío de las ordenes por correo se modifica en la función enviarPdf() el mensaje y asunto como vacíos ya
							  que el mensaje se construirá dinamicamente desde envioCorreoHCEOrdenes.php con los parámetros de root_000051 
							  mensajeCorreoEnvioOrdenesPaciente y mensajeCorreoEnvioOrdenesEntidad
	  ================================================================================================================================================
	 *	Abril 2 de 2020: Jessica Madrid
							- Se adiciona la opción de envío por correo del pdf con las ordenes médicas, se habilita la opción si se recibe el 
							  parámetro enviarCorreo en on y se recibe el email en el parámetro emailEnviarCorreo
	  ================================================================================================================================================
	 *	Enero 17 de 2020: Jessica Madrid
							- Se la consulta del responsable para garantizar que muestre el responsable del ingreso correspondiente y no del último.
							- Se modifica la descripción del logo para que se visualice correctamente.
	 ================================================================================================================================================
	 *	Noviembre 21 de 2019: Edwin MG
							- Se imprime el código de barras solo con el número de la orden sin el tipo de orden
	 ================================================================================================================================================
	 *	Noviembre 19 de 2019: Jessica Madrid
							- Se modifica el calculo de la edad: si el paciente tiene alta definitiva debe realizar el calculo de 
							  la edad con la fecha de egreso, de lo contrario realiza el calculo con la fecha actual.
							- Se corrige la impresión ya que al generar el pdf no se visualiza correctamente la historia y la tarifa.
	 ================================================================================================================================================
	 *	Octubre 30 de 2019: Edwin MG
							- Se imprime el código de barras de la historia y el número de orden
	 ================================================================================================================================================
	 *	Abril 4 de 2019: Edwin Molina
							- Se muestra el nombre del procedimiento según el cup (root_000012) en lugar del lenguage Américas
	 ================================================================================================================================================
	 *	Diciembre 18 de 2017: Jessica Madrid
							- Se comenta el contenido de la funcion consultarDxs() y agrega el llamado a la función consultarUltimoDiagnosticoHCE() 
							de comun.php que devuelve la lista de los diagnósticos actuales del paciente
	 ================================================================================================================================================
	 
	   *	Abril 21 de 2017: Jessica Madrid
							- Para la impresion de nutriciones se modifica la funcion consultarInsumosNPT para que muestre la cantidad de los insumos
							como se crea la NPT en central de mezclas (en ML)
	 ================================================================================================================================================
	 
	  * Marzo 21 de 2017: Jonatan Lopez
						- Se quita el filtro de kadreg donde se tenia en cuenta solo el registro del primer articulo, y se agrupa por kaido teniendo
						en cuenta los cambios realizados sobre el articulo, ademas para todos se imprime el kadfec y no la fecha_data de la movhos_000054,
						esto para que se muestren los articulos que han cambiado de frecuencia cuando hay cambio de dia.
	  ================================================================================================================================================
	  *	Agosto 4 de 2016: Jessica Madrid
							- Se modifica la impresion de nutriciones para que lea los insumos de movhos_000214 en vez de la observacion.
		
	 ================================================================================================================================================
	 *  2016-04-14:  Arleyda Insignares 
	 *   			 - Se agrega el campo numero de contrato a la impresion de ordenes, ubicado antes del campo diagnostico, campo ingnce de la tabla
	 *   			  cliame_000101
	 *   			  
	 ================================================================================================================================================
	 *  Agosto 12 de 2016: Jonatan Lopez
							- Se agrega el texto sin ubicacion asignada si el paciente no tiene ubicacion actual, ademas del nombre del centro de costos.
	  ================================================================================================================================================
	 *	Marzo 30 de 2016: Jessica Madrid
							- Se modifica las impresion de la orden desde el ctc para que muestre el medicamento reemplazado para la nueva eps.
		
	 ================================================================================================================================================
	 *	Marzo 11 de 2016: Edwin MG
	 *						- Se corrige query de consulta de procedimientos cuando se imprime todas las ordenes para que tenga en cuentas aquellos 
	 *						  examenes que están eliminados desde homologación de examenes, es decir, que solo se encuentran en la tabla hce_000017.
	 ================================================================================================================================================
	 *	Marzo 02 de 2016: Edwin MG
	 *						- Se corrige filtro indicado por la variable $textAnd para la consulta de procedimientos cuando se imprime todas 
	 *						  las ordenes dsede el programa impresion de ordenes a pacientes egresados
	 ================================================================================================================================================
	 *	Enero 12 de 2016: Jessica Madrid
							- Se modifican las observaciones  de las nutriciones parenterales para que se muestren todas las observaciones en vez de 
							la primera y se muestran puntos suspensivos solo si la observacion excede el limite establecido en el caracteresObserNutricionesOrdenes de root_000051.
							
	 ================================================================================================================================================
	 *	Enero 12 de 2016 Veronica Arismendy 
							- Se modifica el archivo para agregar validación si la impresión se solicitó desde rp_PacientesEgresadosActivosOrdenes.php
							también para agregar filtros dependiendo del tipo de orden que selecciono el usuario imprimir, con el fin de evitar que se le cargue
							todo en el pdf y por el contrario sólo le salgan el tipo de ordenes que el solicitó.	 
	 ================================================================================================================================================
	 * Noviembre 19 de 2015: Edwin MG
							- Se modifica la llave con la que se organizaba el arreglo de medicamentos, antes estaba kadido, ahora es el id del registro, 
							esto porque algunos kadido quedaban en cero y al encontrar el primer cero ya no agrega otro que tenga cero en el arreglo, 
							en cambio el id siempre es diferente.
	 * Septiembre 7 de 2015: Jonatan	
							- Se imprimen los articulos genericos que han sido reemplazados por otro, si en el campo Kadaan es un generico, se imprimira el articulo.
	 * Junio 26 de 2015:	Jonatan
							- Cuando se imprimen observaciones de un articulo tipo nutricion parenteral, solo se imprimen la cantidad de caracateres de la variable caracteresObserNutricionesOrdenes.
	 * Junio 16 de 2015: 	Jonatan
							- Se imprimen las ordenes de facturacion en un archio pdf, agrupando todos los medicamentos en un paquete y las ayudas diag. en otro, esto 
								para evitar el gasto de papel, la variable $origen debe ser igual a "on" y $wtodos_ordenes debe ser igual a "on" para funcione de esta manera,
								para todos los otros casos seguira imprimiendo en el html y el boton imprimir enviara la informacion a la impresora seleccionada.								
	 * Junio 5 de 2015:		Jonatan
							- Se quita el filtro de estado de la tabla movhos_000048 para que muestre siempre la informacion del medico, aunque este inactivo.
	 * Febrero 26 de 2015: Jonatan
							- Se agregan los dias de tratamiento en la orden si el articulo asociado al paciente tiene ctc, se utilizara ctcttn de la tabla movhos_000134.
	 * Febrero 24 de 2015: Jonatan
							- Se asigan en blanco el filtro de articulo reemplazado cuando es impreso desde CTC.
	 * Enero 9 de 2015: Jonatan
							- Se imprimiran solamente los que tengan la variable Kadaan = '' sin tener en cuente si esta suspendido, segun esto
								solo se imprimirán los que registre el medico y no los que reemplace farmacia.
	 * Noviembre 25 de 2014: Jonatan
							- Se comenta la variable $filtro_reemplazado que contiene kadaan = '' ya que se necesita imprimir los articulo asi hayan sido reeemplazados.
							- Se agrega el sexo y la ubicacion del paciente.
	 * Noviembre 21 de 2014: Jonatan
							- Si se estan imprimiendo las ordenes desde la pestaña de alta se mostrara la cantidad, en caso contrario no.
	 * Noviembre 20 de 2014: Jonatan
							- Se agregan columnas a los medicamentos y ayudas, ademas de la edad del paciente.
	 * Noviembre 19 de 2014: Jonatan
							- Se quita el filtro por estado para los examenes y procedimientos, mostrara los que estan marcados para imprimir.
	 * Octubre 27 de 2014: Jonatan 
							- Se agrega filtro que revisa si la ordenes es para imprimir.
	 * Enero 29 de 2014: 	- Para las ordenes que tienen formulario emergente, se corrige la información del paciente, ya que si se imprimía solo una orden de este tipo
	 *					   		no salía la información demográfica del paciente
	 *					 	- Ya no se imprime DIAGNÓSTICO QUE JUSTIFICA LA TRANSFUSIÓN por que este ya fue eliminado del formulario de TRANSFUSIONES de HCE
	*						 - Para la impresión de orden de TRANSFUSIÓN, se cambia observaciones por justificación
	 * Enero 21 de 2014: 	- Se quita resumen de historia clínica para orden de hospitalización
	 *					 	- Se corrige el motivo al imprimir una orden de hospitalización ya que estaba mostrando para el motivo las observaciones
	 ******************************************************************/

/****************************************************
 **************** VARIABLES GLOBALES ****************
 ****************************************************/
$altoimagen = 0;
$anchoimagen = 0;
$wprograma = "on"; //Para que la libreria de codigos de barra no imprima una imagen al incluirla

function consultarFechaEgreso($conex,$wbasedato,$historia,$ingreso)
{
	$query = "SELECT Ubifad 
				FROM ".$wbasedato."_000018 
			   WHERE Ubihis='".$historia."' 
			     AND Ubiing='".$ingreso."' 
				 AND Ubiald='on';";
				 
	$res = mysql_query ($query, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error() );
	$num = mysql_num_rows($res);
	
	$fechaEgreso = "";
	if($num>0)
	{
		$row = mysql_fetch_array($res);
		
		$fechaEgreso = $row['Ubifad'];
	}
	
	return $fechaEgreso;
}

function consultarInsumosNPT( $conex, $wemp_pmla, $wbasedato, $historia, $ingreso,$codigo_medicamento)
{
	$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");	
	
	// $sql = "  SELECT Dnucod,Dnupre,Artcom 
				// FROM ".$wbasedato."_000214,".$wbasedato."_000215,".$wcenpro."_000002 
			   // WHERE Enuhis='".$historia."' 
			     // AND Enuing='".$ingreso."' 
				 // AND Enucnu='".$codigo_medicamento."' 
				 // AND Dnuhis=Enuhis 
				 // AND Dnuing=Enuing 
				 // AND Dnuart=Enuart 
				 // AND Dnuido=Enuido 
				 // AND Artcod=Dnucod 
				 // AND Artest='on'; ";
				 
	$sql = "  SELECT Dnucod,Dnupre,Artcom,Pdecan,Artuni 
				FROM ".$wbasedato."_000214,".$wbasedato."_000215,".$wcenpro."_000002,".$wcenpro."_000003 
			   WHERE Enuhis='".$historia."' 
			     AND Enuing='".$ingreso."' 
				 AND Enucnu='".$codigo_medicamento."' 
				 AND Dnuhis=Enuhis 
				 AND Dnuing=Enuing 
				 AND Dnuart=Enuart 
				 AND Dnuido=Enuido 
				 AND Artcod=Dnucod 
				 AND Artest='on'
				 AND Pdepro=Enucnu
				 AND Pdeins=Dnucod; ";
		 
	$res = mysql_query ($sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	$num = mysql_num_rows($res);
	
	$insumosNPT = array();
	if($num>0)
	{
		$cont = 0;
		while($row = mysql_fetch_array($res))
		{
			$insumosNPT[$cont]['codigo'] = $row['Dnucod'];
			// $insumosNPT[$cont]['prescripcion'] = $row['Dnupre'];
			$insumosNPT[$cont]['prescripcion'] = $row['Pdecan']." ".$row['Artuni'];
			$insumosNPT[$cont]['nombreComercial'] = $row['Artcom'];
			$cont++;
		}
	}
	
	return $insumosNPT;
}

function consultarEmpresaConEquivalencia( $conex, $wemp_pmla, $wbasedato, $historia, $ingreso)
{
	$esEmpConEquivalentes = false;
	$empresaEquivalentes = consultarAliasPorAplicacion( $conex, $wemp_pmla, "empresaConEquivalenciaMedEInsumos" );
	
	$empEquivalentes = explode(",",$empresaEquivalentes);
	
	$sql = "SELECT Ingres  
			  FROM ".$wbasedato."_000016
			 WHERE Inghis='".$historia."' 
			   AND Inging='".$ingreso."'; ";
		 
	$res = mysql_query ($sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		for($i=0;$i<count($empEquivalentes);$i++)
		{
			if($empEquivalentes[$i] == $rows['Ingres'])
			{
				$esEmpConEquivalentes = true;
				break;
			}
		}			
	}
	
	return $esEmpConEquivalentes;
}

function consultarMedicamentoEquivalenteCTC( $wbasedato, $codMedicamento )
{
	global $conex;
	global $wemp_pmla;
	global $ccoSF;
	
	$cenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	
	$reemplazo = array();
	
	$sql = "SELECT e.Artgen, Unides
			  FROM ".$cenmez."_000001 a, ".$cenmez."_000002 b, ".$cenmez."_000003 c, ".$cenmez."_000009 d, ".$wbasedato."_000026 e, ".$cenmez."_000002 f, ".$wbasedato."_000027 g, ".$wbasedato."_000059 h, ".$wbasedato."_000059 i
			 WHERE tipcdo =  'on'
				AND tipest =  'on'
				AND tipcod = b.arttip
				AND b.artcod =  '".$codMedicamento."'
				AND pdepro = b.artcod
				AND pdeest =  'on'
				AND pdeins = appcod
				AND apppre = e.artcod
				AND appest =  'on'
				AND e.artest =  'on'
				AND f.artcod = appcod
				AND artpos = 'N'
				AND e.artuni = Unicod
				AND h.defart = b.artcod
				AND i.defart = e.artcod
				AND h.deffru = i.deffru
			ORDER BY Appcod";
			
	$res = mysql_query ($sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num == 1 ){
		
		if( $rows = mysql_fetch_array($res) ){
			
			$reemplazo['Artgen'] = $rows['Artgen'];
			$reemplazo['Unides'] = $rows['Unides'];
		}
	}
	else{
		 // -- AND Areceq > '1'
		$sql = "SELECT Artgen, Unides
				  FROM ".$wbasedato."_000008, ".$wbasedato."_000026, ".$wbasedato."_000027
				 WHERE Arecco={$ccoSF} 
				   AND Areces='".$codMedicamento."'
				   AND Areaeq = Artcod
				   AND Artest = 'on'
				   AND Artpos = 'N'
				   AND Artuni = Unicod; ";
			 
		$res = mysql_query ($sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
		
		if( $rows = mysql_fetch_array($res) ){
			
			$reemplazo['Artgen'] = $rows['Artgen'];
			$reemplazo['Unides'] = $rows['Unides'];
			
		}
	}
	
	return $reemplazo;
}


function es_nutricion($cod_art){
	
	global $conex;
	global $wemp_pmla;
	
	$nutricion = 'off';
	
	$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");	
	
	$sql = "SELECT Artcod  
			  FROM ".$wcenpro."_000001, ".$wcenpro."_000002 
			 WHERE Tipcdo != 'on' 
			   AND Tipnco = 'off' 
			   AND Tippro = 'on' 
			   AND Tipest = 'on'
			   AND Tipcod = Arttip
			   AND Artcod = '".$cod_art."'";		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	$row = mysql_fetch_array($res);
	
	if($row['Artcod'] != ''){
		
		$nutricion = 'on';
		
	}
	
	
	return $nutricion;
	
}

function generarcodigodebarras($barcode,$width,$heigth,$quality,$format, $ruta, $nombre){
	
	global $wprograma;
	
	include_once('root/clsGenerarCodigoBarras.php');
	
	Barcode39($barcode,$width,$heigth,$quality,$format, $ruta, $nombre);
	
	
}


function borrarOrdenesImpresas(){
		
		$resultado = shell_exec( "find /var/www/matrix/hce/procesos/impresion_ordenes/* -mtime +0 -exec rm {} \; ");		
		
	}

function estilo(){
	
	$estilo = "<head><style type='text/css'>
	
	table, td {
		font-family: Arial;
		font-size: 6.5pt;
	}
	.encabezado {
		font-size: 6.5pt;
		font-weight: bold;
	}
	.encabezadoExamen {
		text-align: right;
		font-size: 8pt;
	}
	.encabezadoEmpresa {
		text-align: left;
		font-size: 6.5pt;
	}
	.filaEncabezado {
		border-bottom: 1px solid rgb(51, 51, 51);
		border-left: 1px solid rgb(51, 51, 51);
	}
	.filaEncabezadoFin {
		border-bottom: 1px solid rgb(51, 51, 51);
		border-right: 1px solid rgb(51, 51, 51);
		border-left: 1px solid rgb(51, 51, 51);
	}
	.campoFirma {
		border-bottom: 1px solid rgb(51, 51, 51);
		width:208px;
		height:31px !important;
		height:27px;
	}
	.descripcion
	{
		font-size: 5.5pt;
		text-align:justify;
	}
 	.total
	{
		font-size: 6.5pt;
		height: 27px;
		text-align:right;
		text-valign:bottom;
	}

  </style></head>";
	
	return $estilo;
}

//Se crea el PDF en la carpeta resultados
function imprimirPDF(){
	global $pdf;
	
	$dir = 'resultados';

	if(is_dir($dir)){ }
	else { mkdir($dir,0777); }
	
	$sufijo_orden = "prueba";
	$archivo_dir = $dir."/resultado_".$sufijo_orden.".pdf";
	if(file_exists($archivo_dir)){
		unlink($archivo_dir);
	}

	$pdf->Output($archivo_dir, 'F');
}

//Funcion que determina la posicion donde debe ir ubicada la tarjeta
function agregarTarjetaPDF( $contenido_pdf, $numero_tarjeta=1 ){
	global $pdf;
	
	$contenido_css = "<style>	
						table {
							font-family: helvetica;
							font-size: 8pt;	
						}
						.patronc{
							font-size: 13pt;	
						}
						.patroncp{
							font-size: 9pt;	
						}
						.patrondsn1{
							font-size: 5pt;	
						}
						.patrondsn2{
							font-size: 7pt;
						}
						.tipopos{
							font-size: 10pt;
						}
					</style>";		
	$contenido_pdf = $contenido_css." ".$contenido_pdf;
	$contenido_pdf = str_replace("'",'"',$contenido_pdf);
	$html = str_replace("\\", "", $contenido_pdf);
	
	$y = $pdf->getY();
	$x = 0;
			
	//Explicacion: Las medidas de una hoja tamaño carta son 21.59 x 27.94
	//Cada celda para la tarjeta mide 10x6.984
	//Si son dos tarjetas por fila se ocupa un width de 20, queda un espacio 1.59 que se divide entre 4 (0.3975) para dejar de "margen" izq y der para cada tarjeta. Asi se ocupa todo el width
	//Si son 4 tarjetas por columna se ocupa un height de 6.984x4=27.936, quedan 0.004 despreciables. Se ocupa todo el heigth
	if( $numero_tarjeta == 1 ){
		$x = 0.3975;
		$y = 0;
	}else if( $numero_tarjeta == 2 ){
		$x = 11.1925;
		$y = 0;
	}else if( $numero_tarjeta == 3 ){
		$x = 0.3975;
		$y = 6.985;
	}else if( $numero_tarjeta == 4 ){
		$x = 11.1925;
		$y = 6.985;
	}else if( $numero_tarjeta == 5 ){
		$x = 0.3975;
		$y = 13.97;
	}else if( $numero_tarjeta == 6 ){
		$x = 11.1925;
		$y = 13.97;
	}else if( $numero_tarjeta == 7 ){
		$x = 0.3975;
		$y = 20.955;
	}else if( $numero_tarjeta == 8 ){
		$x = 11.1925;
		$y = 20.955;
	}
	
	$pdf->StartTransform();	
	$pdf->Rect($x, $y, 10, 6.8, 'CNZ'); //Aunque la celda mide 10x6.984, solo se muestra el contenido hasta 6.8 por si la tarjeta desborda el tamaño de la celda
	$pdf->writeHTMLCell(10, 6.984, $x, $y, $html); //Se imprime una celda de 10x6.984
	$pdf->StopTransform();	
}

//Busca el la duracion del medicamento en el registro del ctc.
function traer_diastto_ctc($wbasedato,$idctc){
	
	
	global $conex;
	$dias = "";
	
	$sql = "SELECT Ctcttn
			  FROM ".$wbasedato."_000134
			 WHERE ctcido LIKE '".$idctc."'
			   AND Ctcttn != 'NaN'";		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	$row = mysql_fetch_assoc($res);
	
	if($num > 0){
	 $dias = $row['Ctcttn']." días";
	}
	
	return $dias;
}
	
	
function verificar($chain)
{
	for ($w=0;$w<=strlen($chain);$w++)
	{
		if((substr($chain,$w,1) > "9" or substr($chain,$w,1) < "0") and substr($chain,$w,1) != ".")
			return false;
	} 
	return true;
}
	
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
	{
		if(substr_count($chain, '-') == 1)
			return substr($chain,strpos($chain,"-")+1);
		else
			return $chain;
	}
}

function calcularspan($s)
{
	/*
	if(strlen($s) < 51)
		return 1;
	else
		return 8;
*/

	if(strlen($s) <= 25)
		return 1;
	if(strlen($s) >= 26 and strlen($s) <= 40)
		return 2;
	if(strlen($s) >= 41 and strlen($s) <= 55)
		return 3;
	if(strlen($s) >= 56 and strlen($s) <= 70)
		return 4;
	if(strlen($s) >= 71 and strlen($s) <= 85)
		return 5;
	if(strlen($s) >= 86 and strlen($s) <= 100)
		return 6;
	if(strlen($s) >= 101 and strlen($s) <= 115)
		return 7;
	if(strlen($s) > 115)
		return 8;

}

function buscarlabel(&$t,$ord,$n,$f)
{
	for ($w=0;$w<=$n;$w++)
	{
/*
		$w1=$n-$w-1;
		if($t[$w1][0] < $ord and $t[$w1][3] == 1 and $t[$w1][2] == $f)
			return -1;
*/
		//elseif($t[$w][0] < $ord and $t[$w][3] == 0 and $t[$w][2] == $f)
		if($t[$w][0] < $ord and $t[$w][3] == 0 and $t[$w][2] == $f)
		{
			$t[$w][3] = 1;
			return $w;
		}
		
		
	}
	return -1;
}	
	
function buscarstitulo(&$t,$ord,$n,$f)
{
	$pos=-1;
	for ($w=0;$w<=$n;$w++)
	{
		if($t[$w][2] == $f and $t[$w][0] > $ord)
			break;
		else
			if($t[$w][0] < $ord and $t[$w][3] == 0 and $t[$w][2] == $f)
			{
				$t[$w][3] = 1;
				$pos=$w;
			}
	}
	return $pos;
}
	
	
function buscartitulo(&$t,$ord,$n,$f)
{
	$pos=-1;
	for ($w=0;$w<=$n;$w++)
	{
		if($t[$w][2] == $f and $t[$w][0] > $ord)
			break;
		else
			if($t[$w][0] < $ord and $t[$w][3] == 0 and $t[$w][2] == $f)
			{
				$t[$w][3] = 1;
				$pos=$w;
			}
	}
	return $pos;
}	
	
function imprimir($conex,&$empresa,&$queryI,&$whis,&$wing,&$key,&$en,&$wintitulo,&$Hgraficas)
{

	global $wbasedato;
	
	if($queryI != "")
	{
		$queryI .= "  order by 15,4,5,8,3 ";
		$ks=-1;
		$titulos=array();
		//                                      0                           1                         2                          3
		$query  = "select ".$empresa."_000002.Detorp,".$empresa."_000002.Detdes,".$empresa."_000002.detpro,".$empresa."_000002.detase from ".$empresa."_000002 ";
		$query .= " where ".$empresa."_000002.detpro in (".$en.") ";
		$query .= "   and ".$empresa."_000002.dettip = 'Titulo' "; 
		$query .= "   and ".$empresa."_000002.detest='on' ";  
		$query .= "  order by 3,1 ";
		$err1 = mysql_query($query,$conex);
		$numt = mysql_num_rows($err1);
		if ($numt>0)
		{
			for ($j=0;$j<$numt;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if($row1[3] == "A" or $row1[3] == $wsex)
				{
					$ks=$ks+1;
					$titulos[$ks][0]=$row1[0];
					$titulos[$ks][1]=$row1[1];
					$titulos[$ks][2]=$row1[2];
					$titulos[$ks][3]=0;
				}
			}
		}
		$numt=$ks;
		$Stitulos=array();
		$ks=-1;
		//                                      0                           1                         2                          3
		$query  = "select ".$empresa."_000002.Detorp,".$empresa."_000002.Detdes,".$empresa."_000002.detpro,".$empresa."_000002.detase from ".$empresa."_000002 ";
		$query .= " where ".$empresa."_000002.detpro in (".$en.") ";
		$query .= "   and ".$empresa."_000002.dettip = 'Subtitulo' "; 
		$query .= "   and ".$empresa."_000002.Detimp='on' "; 
		$query .= "   and ".$empresa."_000002.detest='on' ";  
		$query .= "  order by 3,1 ";
		$err1 = mysql_query($query,$conex);
		$nums = mysql_num_rows($err1);
		if ($nums>0)
		{
			for ($j=0;$j<$nums;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if($row1[3] == "A" or $row1[3] == $wsex)
				{
					$ks=$ks+1;
					$Stitulos[$ks][0]=$row1[0];
					$Stitulos[$ks][1]=$row1[1];
					$Stitulos[$ks][2]=$row1[2];
					$Stitulos[$ks][3]=0;
				}
			}
		}
		$nums=$ks;
		$Label=array();
		$ks=-1;
		//                                      0                           1                         2                           3                         4
		$query  = "select ".$empresa."_000002.Detorp,".$empresa."_000002.Detdes,".$empresa."_000002.detpro,".$empresa."_000002.detfor,".$empresa."_000002.detase from ".$empresa."_000002 ";
		$query .= " where ".$empresa."_000002.detpro in (".$en.") ";
		$query .= "   and ".$empresa."_000002.dettip = 'Label' "; 
		$query .= "   and ".$empresa."_000002.Detimp='on' "; 
		$query .= "   and ".$empresa."_000002.detest='on' ";  
		$query .= "  order by 3,1 ";
		$err1 = mysql_query($query,$conex);
		$numl = mysql_num_rows($err1);
		if ($numl>0)
		{
			for ($j=0;$j<$numl;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if($row1[4] == "A" or $row1[4] == $wsex)
				{
					$ks=$ks+1;
					$Label[$ks][0]=$row1[0];
					if(strlen($row1[3]) > 1)
						$Label[$ks][1]=$row1[3];
					else
						$Label[$ks][1]=$row1[1];
					$Label[$ks][2]=$row1[2];
					$Label[$ks][3]=0;
				}
			}
		}
		$numl=$ks;
		$filanterior=0;
		$spana=0;
		$kcolor=0;
		$tcolor=1;
		$wfor="";
		$wforant="";
		$wfecant="";
		$whorant="";
		$numimg=0;
		$wsimagenes="";
		$FIRMADO=1;
		$TEXTOSF="";
		echo "<table border=1 cellpadding=5 id='mitablita' width='712' cellspacing=0 class=tipoTABLE>";
		$err1 = mysql_query($queryI,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if ($num1>0)
		{
			for ($j=0;$j<$num1;$j++)
			{
				$row1 = mysql_fetch_array($err1);
				if($wfor != $row1[7].$row1[3].$row1[4])
				{
					$datfirma="";
					if($wforant != "" and $FIRMADO == 1)
					{
						//                                                 0                                   1                   2                                  3                                 4                               5
						$queryJ  = " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,usuarios.Descripcion,".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat,".$empresa."_".$wforant.".movusu  from ".$empresa."_".$wforant.",".$empresa."_000020, usuarios ";
						$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
						$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
						$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
						$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
						$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
						$queryJ .= "   and ".$empresa."_".$wforant.".movcon = 1000 ";
						//$queryJ .= "   and ".$empresa."_".$wforant.".movdat = ".$empresa."_000020.Usucla ";
						$queryJ .= "   and ".$empresa."_".$wforant.".movusu = ".$empresa."_000020.Usucod ";
						$queryJ .= "   and ".$empresa."_000020.Usucod = usuarios.Codigo ";
						$queryJ .= "  UNION ALL ";
						$queryJ .= " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,usuarios.Descripcion,".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat,".$empresa."_".$wforant.".movusu  from ".$empresa."_".$wforant.",".$empresa."_000020, usuarios ";
						$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
						$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
						$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
						$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
						$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
						$queryJ .= "   and ".$empresa."_".$wforant.".movcon > 1000 ";
						$queryJ .= "   and ".$empresa."_".$wforant.".movusu = ".$empresa."_000020.Usucod ";
						$queryJ .= "   and ".$empresa."_000020.Usucod = usuarios.Codigo ";
						$queryJ .= "  order by 4 ";
						$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
						$num = mysql_num_rows($err);
						if($num > 0)
						{	
							$notas=array();
							$kn=-1;
							for ($h=0;$h<$num;$h++)
							{
								$row = mysql_fetch_array($err);
								if($row[3] == 1000)
								{
									$queryF  = "select Medtdo,Meddoc,Medreg,Firrol  from ".$wbasedato."_000048, ".$empresa."_000036 ";
									$queryF .= "   where Meduma = '".$row[5]."' ";
									$queryF .= " 	and Meduma = Firusu ";
									$queryF .= " 	and Firpro = '".$wforant."' ";
									$queryF .= " 	and Firhis = '".$whis."' "; 
									$queryF .= " 	and Firing = '".$wing."' ";
									$queryF .= " 	and ".$empresa."_000036.Fecha_data = '".$wfecant."' "; 
									$queryF .= " 	and ".$empresa."_000036.Hora_data = '".$whorant."' "; 
/*
									$queryF  = " select Medtdo,Meddoc,Medreg,Medesp  from movhos_000048 ";
									$queryF .= "  where Meduma ='".$row[5]."' ";
*/
									$errF = mysql_query($queryF,$conex) or die(mysql_errno().":".mysql_error());
									$numF = mysql_num_rows($errF);
									if($numF > 0)
									{
										$rowF = mysql_fetch_array($errF);
										if(strpos($rowF[3],"-") === false)
										{
											$queryE  = " select Espnom  from ".$wbasedato."_000044 ";
											$queryE .= "  where Espcod ='".$rowF[3]."' ";
											$errE = mysql_query($queryE,$conex) or die(mysql_errno().":".mysql_error());
											$numE = mysql_num_rows($errE);
											if($numE > 0)
											{
												$rowE = mysql_fetch_array($errE);
												$wespe=$rowE[0];
											}
											else
												$wespe="";
										}
										else
											$wespe=substr($rowF[3],strpos($rowF[3],"-")+1);
										$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$row[2]." Identificacion : ".$rowF[0]." ".$rowF[1]." Registro : ".$rowF[2]." Profesi&oacute;n o Especialidad : ".$wespe." Fecha : ".$row[0]." Hora : ".$row[1];
									}
									else
										$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$row[2]." Fecha : ".$row[0]." Hora : ".$row[1];
								}
								else
								{
									$kn++;
									$notas[$kn]=$row[4];
									
									$queryF  = " select Medtdo,Meddoc,Medreg,Medesp  from ".$wbasedato."_000048 ";
									$queryF .= "  where Meduma ='".$row[5]."' ";
									$errF = mysql_query($queryF,$conex) or die(mysql_errno().":".mysql_error());
									$numF = mysql_num_rows($errF);
									if($numF > 0)
									{
										$rowF = mysql_fetch_array($errF);
										if(strpos($rowF[3],"-") === false)
										{
											$queryE  = " select Espnom  from ".$wbasedato."_000044 ";
											$queryE .= "  where Espcod ='".$rowF[3]."' ";
											$errE = mysql_query($queryE,$conex) or die(mysql_errno().":".mysql_error());
											$numE = mysql_num_rows($errE);
											if($numE > 0)
											{
												$rowE = mysql_fetch_array($errE);
												$wespe=$rowE[0];
											}
											else
												$wespe="";
										}
										else
											$wespe=substr($rowF[3],strpos($rowF[3],"-")+1);
										$datfirma1="<br>NOTA REALIZADA POR : ".$row[2]." Identificacion : ".$rowF[0]." ".$rowF[1]." Registro : ".$rowF[2]." Profesi&oacute;n o Especialidad : ".$wespe;
									}
									else
										$datfirma1="<br>NOTA REALIZADA POR : ".$row[2];
									$notas[$kn] .= $datfirma1;
								}
							}
						}
					}
					$wfor=$row1[7].$row1[3].$row1[4];
					$wforant=$row1[7];
					$wfecant=$row1[3];
					$whorant=$row1[4];
					$FIRMADO=1;
					$TEXTOSFA=$TEXTOSF;
					$queryJ  = " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,usuarios.Descripcion,".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat from ".$empresa."_".$wforant.",".$empresa."_000020, usuarios ";
					$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
					$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
					$queryJ .= "   and ".$empresa."_".$wforant.".movcon = 1000 ";
					//$queryJ .= "   and ".$empresa."_".$wforant.".movdat = ".$empresa."_000020.Usucla ";
					$queryJ .= "   and ".$empresa."_".$wforant.".movusu = ".$empresa."_000020.Usucod ";
					$queryJ .= "   and ".$empresa."_000020.Usucod = usuarios.Codigo ";
					$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if($num == 0)
					{
						$FIRMADO=0;
						$queryJ  = " select usuarios.Descripcion from usuarios ";
						$queryJ .= " where usuarios.Codigo = '".$row1[12]."' ";
						$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
						$num = mysql_num_rows($err);
						if($num > 0)
						{
							$row = mysql_fetch_array($err);
							$TEXTOSF="Registro : ".$wforant." Diligenciado En : ".$wfecant." ".$whorant."<br>".$row1[8]."<br>DOCUMENTO NO SE IMPRIME. SIN FIRMAR POR : ".$row1[12]." ".$row[0];
						}
						else
						{
							$TEXTOSF="Registro : ".$wforant." Diligenciado En : ".$wfecant." ".$whorant."<br>".$row1[8]."<br>DOCUMENTO NO SE IMPRIME. SIN FIRMAR POR : USUARIO NO REGISTRADO. COMUNIQUELO A INFORMATICA !!!!";
						}
					}
					else
					{
						$TEXTOSF="";
					}
					for ($z=0;$z<=$numt;$z++)
						$titulos[$z][3]=0;
					for ($z=0;$z<=$nums;$z++)
						$Stitulos[$z][3]=0;
					for ($z=0;$z<=$numl;$z++)
						$Label[$z][3]=0;
				}
				
				$datos_paciente = consultarInfoPaciente($conex, $whis, $wing);
				$pacno1 = $datos_paciente['pacno1'];
				$pacno2 = $datos_paciente['pacno2'];
				$pacap1 = $datos_paciente['pacap1'];
				$pacap2 = $datos_paciente['pacap2'];
				$pacnac = $datos_paciente['pacnac'];
				$pacsex = $datos_paciente['pacsex'];
				$ingnre = $datos_paciente['ingnre'];
				
				$wpac = $pacno1." ".$pacno2." ".$pacap1." ".$pacap2;
				
				$q_ubi = "SELECT habcod, habhis, habcpa, habzon, habcco
							FROM ".$wbasedato."_000020
						   WHERE Habhis = '".$whis."'
							 AND Habing = '".$wing."'" ;
				$res_ubi = mysql_query($q_ubi, $conex);
				$row_ubi = mysql_fetch_array($res_ubi);
				
				//Imprime la ubicacion del paciente.
				if(trim($row_ubi['habcpa']) != ""){
				
					if(trim($row_ubi['habzon']) != 'NO APLICA' or trim($row_ubi['habzon']) != ''){			
					
						$zona = $row_ubi['habzon']."<br>";
					}
					
					$ubicacion = $zona.$row_ubi['habcpa'];
					
				}else{
				
					$ubicacion = $row_ubi['habcod'];
				}
				
				if(trim($ubicacion) == ''){
				
					$ubicacion = $cconombre;
				}
				
				
				if($filanterior > $row1[2])
				{
					if($spana < 8)
					{
						$spanf=8 - $spana;
						$WIDTH=$spanf*89;
						echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
					}
					if($datfirma != "")
					{
						$WIDTH=8*89;
						echo "<tr><td  colspan=8 id=tipoIL02H width='".$WIDTH."'>".$datfirma."</td></tr>";						
						if($row1[17] == "on")
							if(file_exists("../../images/medical/hce/Firmas/".$key.".png"))
								echo "<tr><td  colspan=8 id=tipoIL02H width='".$WIDTH."'><IMG SRC='../../images/medical/hce/Firmas/".$key.".png'></td></tr>";
							else
								echo "<tr><td  colspan=8 id=tipoIL02H width='".$WIDTH."'>FIRMA SIN DIGITALIZAR</td></tr>";
						for ($h=0;$h<count($notas);$h++)
							echo "<tr><td  colspan=8 id=tipoIL02I width='".$WIDTH."'>NOTA REALIZADA EN : ".$notas[$h]."</td></tr>";
					}
					else
					{
						$WIDTH=8*89;
						//echo "<tr><td  colspan=8 id=tipoIL02N width='".$WIDTH."'>SIN FIRMAR</td></tr>";
						echo "<tr><td  colspan=8 id=tipoIL02N width='".$WIDTH."'>".$TEXTOSFA."</td></tr>";
					}
					$WIDTH=8*89;
				
					echo "<tr><td id=tipoIL02Y colspan=8 width='".$WIDTH."'>&nbsp;</td></tr>";
					echo "<tr><td class=tipoTF colspan=8 width='".$WIDTH."'>";						
					echo "<table border=1 width='712' class=tipoTABLE1>";
					echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/lmatrix.jpg' id='logo'></td>";	
					echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
					echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".calcularEdad($conex,$wbasedato,$whis,$wing,$pacnac)."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".obtenerSexo($pacsex)."</td></tr>";
					echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$ubicacion."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$ingnre."</td></tr>";
					echo "</table>";					
					echo "</td></tr>";
					echo "<tr><td id=tipoIL02U colspan=8 width='".$WIDTH."'><b>*** ".strtoupper($row1[8])." ***</b></td></tr>";
					$spana = 0;
				}elseif($filanterior == 0)
					{
											
						$WIDTH=8*89;
						echo "<tr><td class=tipoTF colspan=8 width='".$WIDTH."'>";
						
						echo "<table border=1 width='712' class=tipoTABLE1>";
						echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/lmatrix.jpg' id='logo'></td>";	
						echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
						echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".calcularEdad($conex,$wbasedato,$whis,$wing,$pacnac)."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".obtenerSexo($pacsex)."</td></tr>";
						echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$ubicacion."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$ingnre."</td></tr>";
						echo "</table>";
						
						echo "</td></tr>";
						echo "<tr><td id=tipoIL02U colspan=8 width='".$WIDTH."'><b>*** ".strtoupper($row1[8])." ***</b></td></tr>";
					}
				if($FIRMADO == 1)
				{
					if(strlen($row1[1]) > 0)
					{
						$sit=buscartitulo($titulos,$row1[2],$numt,$row1[7]);
						//echo "Titulo ".$row1[2]." ".$row1[7]." ".$sit."<br>";
						$sitt=$sit;
						if($sit > -1)
						{
							if($spana < 8 and $spana > 0)
							{
								$spanf=8 - $spana;
								$WIDTH=$spanf*89;
								echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
							}
							$WIDTH=8*89;
							echo "<tr><td id=tipoIL02Z colspan=8 width='".$WIDTH."'><b><i>".htmlentities($titulos[$sit][1])."</i></b></td></tr>";
							if($kcolor % 2 == 0)
								$tcolor="1";
							else
								$tcolor="2";
							$kcolor++;
							$spana = 0;
						}
						$sit=buscarstitulo($Stitulos,$row1[2],$nums,$row1[7]);
						$sits=$sit;
						if($sit > -1 and $sit > $sitt)
						{
							if($spana < 8 and $spana > 0)
							{
								$spanf=8 - $spana;
								$WIDTH=$spanf*89;
								echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
							}
							$WIDTH=8*89;
							echo "<tr><td id=tipoIL02S colspan=8 width='".$WIDTH."'><b><i>".htmlentities($Stitulos[$sit][1])."</i></b></td></tr>";
							if($kcolor % 2 == 0)
								$tcolor="1";
							else
								$tcolor="2";
							$kcolor++;
							$spana = 0;
						}
						$sit=buscarlabel($Label,$row1[2],$numl,$row1[7]);
						while($sit > -1)
						{
							if($sit > $sitt and $sit > $sits)
							{
								if($spana < 8 and $spana > 0)
								{
									$spanf=8 - $spana;
									$WIDTH=$spanf*89;
									echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
								}
								$WIDTH=8*89;
								echo "<tr><td id=tipoIL02L colspan=8 width='".$WIDTH."'><b><i>".htmlentities($Label[$sit][1])."</i></b></td></tr>";
								if($kcolor % 2 == 0)
									$tcolor="1";
								else
									$tcolor="2";
								$kcolor++;
								$spana = 0;
							}
							$sit=buscarlabel($Label,$row1[2],$numl,$row1[7]);
						}
					}
					$filanterior=$row1[2];
					if($row1[5] == "Seleccion" and $row1[1] == "undefined")
						$row1[1] = "";
					if($row1[5] == "Seleccion" and $row1[1] != "" and $row1[15]=="M1")
					{
						$row1[1]=str_replace("</OPTION>"," ",$row1[1]);
						$row1[1]=str_replace("</option>"," ",$row1[1]);
						$imp=0;
						$def="";
						$ndiag=0;
						$wclase=0;
						for ($z=0;$z<strlen($row1[1]);$z++)
						{
							if(substr($row1[1],$z,1) == "<")
								$imp=1;
							if(substr($row1[1],$z,1) == ">")
							{
								$imp=0;
								$z++;
								if(substr($row1[1],$z,1) == "M")
								{
									$wclase=1;
									$ndiag++;
									$def .= "<BR>(".$ndiag.") ";
								}
								else
								{
									$wclase=0;
									$def .= "<BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
								}
							}
							if($imp == 0)
							{
								if($wclase == 1)
									$def .= "<b><u>".substr($row1[1],$z,1)."</b></u>";
								else
									$def .= substr($row1[1],$z,1);
							}
						}
						$def=str_replace("M-","",$def);
						$def=str_replace("O-","",$def);
						$def=str_replace(".","",$def);
						$row1[1]=$def;
					}
					if($row1[5] == "Grid")
					{
						$wsgrid="";
						$Gridseg=explode("*",$row1[16]);
						$Gridtit=explode("|",$Gridseg[0]);
						$wsgrid .= "<table align=center border=1 class=tipoTABLEGRID>";
						$wsgrid .= "<tr>";
						$wsgrid .= "<td id=tipoL06GRID>ITEM</td>";
						for ($g=0;$g<count($Gridtit);$g++)
						{
							$wsgrid .= "<td id=tipoL06GRID>".$Gridtit[$g]."</td>";
						}
						$wsgrid .= "</tr>";
						$Gdataseg=explode("*",$row1[1]);
						for ($g=1;$g<=$Gdataseg[0];$g++)
						{
							if($g % 2 == 0)
								$gridcolor="tipoL02GRID1";
							else
								$gridcolor="tipoL02GRID2";
							$Gdatadata=explode("|",$Gdataseg[$g]);
							$wsgrid .= "<tr>";
							$wsgrid .= "<td class=".$gridcolor.">".$g."</td>";
							for ($g1=0;$g1<count($Gdatadata);$g1++)
							{
								$wsgrid .= "<td class=".$gridcolor.">".$Gdatadata[$g1]."</td>";
							}
							$wsgrid .= "</tr>";
						}
						$wsgrid .= "</table><br>";
					}
					if($row1[5] == "Seleccion" and $row1[1] != "" and $row1[15]=="M")
					{
						$row1[1]=str_replace("</OPTION>"," ",$row1[1]);
						$row1[1]=str_replace("</option>"," ",$row1[1]);
						$imp=0;
						$def="";
						$ndiag=0;
						for ($z=0;$z<strlen($row1[1]);$z++)
						{
							if(substr($row1[1],$z,1) == "<")
								$imp=1;
							if(substr($row1[1],$z,1) == ">")
							{
								$imp=0;
								$z++;
								$ndiag++;
								$def .= "(".$ndiag.") ";
							}
							if($imp == 0)
							{
								$def .= substr($row1[1],$z,1);
							}
						}
						$def=str_replace("P-"," Presuntivo ",$def);
						$def=str_replace("C-"," Confirmado ",$def);
						$row1[1]=$def;
					}
					//if(strlen($row1[1]) > 0 and $row1[5] != "Label" and $row1[5] != "Link")
					if((strlen($row1[1]) > 0 and $row1[5] != "Link"))
					{
						$dospuntos=":";
						if($row1[11] == "off")
						{
							$dospuntos="";
							$row1[0]="";
						}
						if($row1[5] == "Memo")
						{
							$row1[1]=str_replace(".  ","<br>",$row1[1]);
						}
						if($row1[5] == "Booleano")
							if($row1[1] == "CHECKED")
								$row1[1] = "SI";
							else
								$row1[1] = "NO";
						if($row1[5] == "Tabla")
						{
							$row1[1]=str_replace("</OPTION>"," ",$row1[1]);
							$row1[1]=str_replace("</option>"," ",$row1[1]);
							$imp=0;
							$def="";
							$ndiag=0;
							for ($z=0;$z<strlen($row1[1]);$z++)
							{
								if(substr($row1[1],$z,1) == "<")
									$imp=1;
								if(substr($row1[1],$z,1) == ">")
								{
									$imp=0;
									$z++;
									$ndiag++;
									$def .= "(".$ndiag.") ";
								}
								if($imp == 0)
								{
									$def .= substr($row1[1],$z,1);
								}
							}
							$def=str_replace("P-P-"," Presuntivo ",$def);
							$def=str_replace("S-P-"," Presuntivo ",$def);
							$def=str_replace("P-C-"," Confirmado ",$def);
							$def=str_replace("S-C-"," Confirmado ",$def);
							$row1[1]=$def;
						}
						
						if($row1[5] == "Formula")
							if(verificar($row1[1]))
								$wsstring = "<b>".htmlentities($row1[0])."</b> ".$dospuntos." ".number_format((double)$row1[1],2,'.',',')." ";
							else
								$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
						elseif($row1[5] == "Seleccion" and $row1[15]=="M")
									$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
							elseif($row1[5] == "Seleccion" and $row1[15]=="M1")
									$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$row1[1]."</b> ";
								elseif($row1[5] == "Seleccion")
										$wsstring = "<b>".htmlentities($row1[0])."</b> ".$dospuntos." ".ver(htmlentities($row1[1]))." ";
									elseif($row1[5] == "Memo")
											$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$row1[1]."</b> ";
										elseif($row1[5] == "Grid")
												$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$wsgrid."</b> ";
											else
												$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
						$spanw=calcularspan($wsstring);
						if($row1[5] == "Seleccion" and $row1[1] != "" and $row1[15]=="M")
							$spanw=8;
						if($spanw == 1)
							if($row1[5] == "Formula")
								if(verificar($row1[1]))
									$wsstring = "<b>".htmlentities($row1[0])."</b> ".$dospuntos." ".number_format((double)$row1[1],2,'.',',')." ";
								else
									$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
							elseif($row1[5] == "Seleccion" and $row1[15]=="M")
									$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
								elseif($row1[5] == "Seleccion" and $row1[15]=="M1")
										$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$row1[1]."</b> ";
									elseif($row1[5] == "Seleccion")
											$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".ver(htmlentities($row1[1]))."</b> ";
										elseif($row1[5] == "Memo")
											$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$row1[1]."</b> ";
											elseif($row1[5] == "Grid")
												$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".$wsgrid."</b> ";
											else
												$wsstring = "".htmlentities($row1[0])." ".$dospuntos."<br><b>".htmlentities($row1[1])."</b> ";
						if($row1[5] == "Imagen")
						{
							$TEXTO=explode("^",$row1[1]);
							$TEXTOD="";
							for ($zx=0;$zx<count($TEXTO);$zx++)
							{
								if($TEXTO[$zx] != "")
								{
									$TEXTOV=explode("~",$TEXTO[$zx]);
									$TEXTOD .= "<BR>".$zx.":".$TEXTOV[5];
								}
							}
							//echo "<input type='HIDDEN' name= 'Hgraficas' id='Hgraficas' value='".$Hgraficas."'>";
							$wsstring = "IMAGEN: ".htmlentities($row1[0])." <br>INFORMACION IMAGEN:<BR>".$TEXTOD."";
							$spanw=8;
						}
						
						if($row1[10] != "")
						{
							$wsstring .= " ".$row1[10]; 
						}	
						if(($spana + $spanw) < 9)
						{
							if($spana == 0)
							{
								if($spanw == 1)
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02J".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
								else
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
							}
							else
							{
								if($spanw == 1)
								{
									$WIDTH=$spanw*89;
									echo "<td colspan=".$spanw." id=tipoIL02J".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
								else
								{
									$WIDTH=$spanw*89;
									//echo "<tr><td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
									echo "<td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
							}
							$spana += $spanw;
						}
						else
						{
							if($spana == 0)
							{
								if($spanw == 1)
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02J".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
								else
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
							}
							else
							{
								if($spana < 8)
								{
									$spanf=8 - $spana;
									$WIDTH=$spanf*89;
									echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
								}
								if($kcolor % 2 == 0)
									$tcolor="1";
								else
									$tcolor="2";
								$kcolor++;
								if($spanw == 1)
								{
									$WIDTH=$spanw*89;
									echo "</tr><tr><td colspan=".$spanw." id=tipoIL02J".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
								else
								{
									$WIDTH=$spanw*89;
									echo "<tr><td colspan=".$spanw." id=tipoIL02W".$tcolor." width='".$WIDTH."'><div class=tipoDIV>".$wsstring."</div></td>";
								}
							}	
							$spana = $spanw;
						}
					}
				}
				else
				{
					$filanterior=$row1[2];
				}
			}
			echo "<input type='HIDDEN' name= 'Hgraficas' id='Hgraficas' value='".$Hgraficas."'>";
			if($spana < 8)
			{
				$spanf=8 - $spana;
				$WIDTH=$spanf*89;
				echo "<td colspan=".$spanf." id=tipoIL02J".$tcolor." width='".$WIDTH."'>&nbsp;</td>";
			}
			$datfirma="";
			if($wforant != "" and $FIRMADO == 1)
			{
				$queryJ  = " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,usuarios.Descripcion,".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat,".$empresa."_".$wforant.".movusu from ".$empresa."_".$wforant.",".$empresa."_000020, usuarios ";
				$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
				$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
				$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
				$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
				$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
				$queryJ .= "   and ".$empresa."_".$wforant.".movcon = 1000 ";
				//$queryJ .= "   and ".$empresa."_".$wforant.".movdat = ".$empresa."_000020.Usucla ";
				$queryJ .= "   and ".$empresa."_".$wforant.".movusu = ".$empresa."_000020.Usucod ";
				$queryJ .= "   and ".$empresa."_000020.Usucod = usuarios.Codigo ";
				$queryJ .= "  UNION ALL ";
				$queryJ .= " select ".$empresa."_".$wforant.".fecha_data,".$empresa."_".$wforant.".Hora_data,usuarios.Descripcion,".$empresa."_".$wforant.".movcon,".$empresa."_".$wforant.".movdat,".$empresa."_".$wforant.".movusu from ".$empresa."_".$wforant.",".$empresa."_000020, usuarios ";
				$queryJ .= " where ".$empresa."_".$wforant.".movpro='".$wforant."' ";
				$queryJ .= "   and ".$empresa."_".$wforant.".movhis='".$whis."' ";
				$queryJ .= "   and ".$empresa."_".$wforant.".moving='".$wing."' ";
				$queryJ .= "   and ".$empresa."_".$wforant.".fecha_data = '".$wfecant."' "; 
				$queryJ .= "   and ".$empresa."_".$wforant.".hora_data = '".$whorant."' ";
				$queryJ .= "   and ".$empresa."_".$wforant.".movcon > 1000 ";
				$queryJ .= "   and ".$empresa."_".$wforant.".movusu = ".$empresa."_000020.Usucod ";
				$queryJ .= "   and ".$empresa."_000020.Usucod = usuarios.Codigo ";
				$queryJ .= "  order by 4 ";
				$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$notas=array();
					$kn=-1;
					for ($h=0;$h<$num;$h++)
					{
						$row = mysql_fetch_array($err);
						if($row[3] == 1000)
						{
							$queryF  = "select Medtdo,Meddoc,Medreg,Firrol  from ".$wbasedato."_000048, ".$empresa."_000036 ";
							$queryF .= "   where Meduma = '".$row[5]."' ";
							$queryF .= " 	and Meduma = Firusu ";
							$queryF .= " 	and Firpro = '".$wforant."' ";
							$queryF .= " 	and Firhis = '".$whis."' "; 
							$queryF .= " 	and Firing = '".$wing."' ";
							$queryF .= " 	and ".$empresa."_000036.Fecha_data = '".$wfecant."' "; 
							$queryF .= " 	and ".$empresa."_000036.Hora_data = '".$whorant."' "; 
/*
							$queryF  = " select Medtdo,Meddoc,Medreg,Medesp  from movhos_000048 ";
							$queryF .= "  where Meduma ='".$row[5]."' ";
*/
							$errF = mysql_query($queryF,$conex) or die(mysql_errno().":".mysql_error());
							$numF = mysql_num_rows($errF);
							if($numF > 0)
							{
								$rowF = mysql_fetch_array($errF);
								if(strpos($rowF[3],"-") === false)
								{
									$queryE  = " select Espnom  from ".$wbasedato."_000044 ";
									$queryE .= "  where Espcod ='".$rowF[3]."' ";
									$errE = mysql_query($queryE,$conex) or die(mysql_errno().":".mysql_error());
									$numE = mysql_num_rows($errE);
									if($numE > 0)
									{
										$rowE = mysql_fetch_array($errE);
										$wespe=$rowE[0];
									}
									else
										$wespe="";
								}
								else
									$wespe=substr($rowF[3],strpos($rowF[3],"-")+1);
								$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$row[2]." Identificacion : ".$rowF[0]." ".$rowF[1]." Registro : ".$rowF[2]." Profesi&oacute;n o Especialidad : ".$wespe." Fecha : ".$row[0]." Hora : ".$row[1];
							}
							else
								$datfirma="FIRMADO ELECTRONICAMENTE POR : ".$row[2]." Fecha : ".$row[0]." Hora : ".$row[1];
						}
						else
						{
							$kn++;
							$notas[$kn]=$row[4];
							$queryF  = "select Medtdo,Meddoc,Medreg,Firrol  from ".$wbasedato."_000048, ".$empresa."_000036 ";
							$queryF .= "   where Meduma = '".$row[5]."' ";
							$queryF .= " 	and Meduma = Firusu ";
							$queryF .= " 	and Firpro = '".$wforant."' ";
							$queryF .= " 	and Firhis = '".$whis."' "; 
							$queryF .= " 	and Firing = '".$wing."' ";
							$queryF .= " 	and ".$empresa."_000036.Fecha_data = '".$wfecant."' "; 
							$queryF .= " 	and ".$empresa."_000036.Hora_data = '".$whorant."' "; 
/*
							$queryF  = " select Medtdo,Meddoc,Medreg,Medesp  from movhos_000048 ";
							$queryF .= "  where Meduma ='".$row[5]."' ";
*/
							$errF = mysql_query($queryF,$conex) or die(mysql_errno().":".mysql_error());
							$numF = mysql_num_rows($errF);
							if($numF > 0)
							{
								$rowF = mysql_fetch_array($errF);
								if(strpos($rowF[3],"-") === false)
								{
									$queryE  = " select Espnom  from ".$wbasedato."_000044 ";
									$queryE .= "  where Espcod ='".$rowF[3]."' ";
									$errE = mysql_query($queryE,$conex) or die(mysql_errno().":".mysql_error());
									$numE = mysql_num_rows($errE);
									if($numE > 0)
									{
										$rowE = mysql_fetch_array($errE);
										$wespe=$rowE[0];
									}
									else
										$wespe="";
								}
								else
									$wespe=substr($rowF[3],strpos($rowF[3],"-")+1);
								$datfirma1="<br>NOTA REALIZADA POR : ".$row[2]." Identificacion : ".$rowF[0]." ".$rowF[1]." Registro : ".$rowF[2]." Profesi&oacute;n o Especialidad : ".$wespe;
							}
							else
								$datfirma1="<br>NOTA REALIZADA POR : ".$row[2];
							$notas[$kn] .= $datfirma1;
						}
					}
				}
			}
			if($datfirma != "")
			{
				$WIDTH=8*89;
				echo "<tr><td  colspan=8 id=tipoIL02H width='".$WIDTH."'>".$datfirma."</td></tr>";
				if($row1[17] == "on")
					if(file_exists("../../images/medical/hce/Firmas/".$key.".png"))
						echo "<tr><td  colspan=8 id=tipoIL02H width='".$WIDTH."'><IMG SRC='../../images/medical/hce/Firmas/".$key.".png'></td></tr>";
					else
						echo "<tr><td  colspan=8 id=tipoIL02H width='".$WIDTH."'>FIRMA SIN DIGITALIZAR</td></tr>";
				for ($h=0;$h<count($notas);$h++)
					echo "<tr><td  colspan=8 id=tipoIL02I width='".$WIDTH."'>NOTA REALIZADA EN : ".$notas[$h]."</td></tr>";
			}
			else
			{
				$TEXTOSFA=$TEXTOSF;
				$WIDTH=8*89;
				echo "<tr><td  colspan=8 id=tipoIL02N width='".$WIDTH."'>".$TEXTOSFA."</td></tr>";
				//echo "<tr><td  colspan=8 id=tipoIL02N width='".$WIDTH."'>SIN FIRMAR</td></tr>";
			}
		}
		
		$WIDTH=8*89;
		echo "<tr><td colspan=8 id=tipoIL02Q width='".$WIDTH."'><input type='button' value='FIN IMPRESION' class=tipoFinI onclick='salto1(\"".$wintitulo."\")'></td></tr>";
		echo "</table>";
		$wintitulo .= "&nbsp;&nbsp;".date("Y-m-d")."&nbsp;&nbsp;".(string)date("H:i:s");
		echo '<script language="Javascript">';
		echo '	   paginar(document.getElementById("mitablita"),document.forms[0],"'.$wintitulo.'");';
		echo '</script>';
	}
	else
	{
		echo "<center><table border=0 aling=center><tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' id='cabeza'></td><tr></table></center>";
		echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>NO SELECCIONO FORMULARIOS PARA IMPRESION !!!</MARQUEE></FONT>";
	}
}
	
	
//Funcion que trae la tarifa asociada al paciente.
function traer_tarifa($wemp_pmla,$whistoria, $wingreso)	{

	global $conex;
	
	$cliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame" );
	
	$sql = " SELECT ingtar
			   FROM ".$cliame."_000101
			  WHERE inghis = '".$whistoria."'
			    AND ingnin =  '".$wingreso."'";			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_array( $res );
	
	return $row['ingtar'];

}
	
function cups(){
	
	global $conex;
	
	$sql = "SELECT *
			  FROM root_000012";			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	$array_cups = array();
	
	while($row = mysql_fetch_assoc($res)){
						
				if(!array_key_exists($row['Codigo'], $array_cups)){
									
					$array_cups[$row['Codigo']] = $row;					
				}
			}
	
	return $array_cups;

}
	
function validar_si_eps($whistoria, $wingreso){
	
	global $conex;
	global $wemp_pmla;
	global $wbasedato;

	//Busco los tipos de empresa que son EPS
	$tiposEmpresa = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiposEmpresasEps" );
	
	//creo un IN para la consulta
	$list = explode( "-", $tiposEmpresa );
	
	$inEPS = '';
	
	foreach( $list as $key => $value ){
		$inEPS .= ",'$value'";
	}
	
	$inEPS = "IN( ".substr( $inEPS, 1 )." ) ";
			
	$sql = "SELECT 
				*
			FROM
				{$wbasedato}_000016 b
			WHERE
				inghis = '".$whistoria."'
				AND inging = '".$wingreso."'
				AND ingtip $inEPS
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	$esPacEPS = false;
	if( $num > 0 ){
		$esPacEPS = true;
	}
	
	return $esPacEPS;
	/****************************************************************************************/

}
	
/****************************************************
 ********************* FUNCIONES ********************
 ****************************************************/
function consultar_dato_apl($aplicacion){


    global $conex;
	global $wemp_pmla;

    $val = '4';

	$sql = "SELECT Detval
			  FROM root_000051
			 WHERE detemp = '$wemp_pmla'
			   AND detapl = '$aplicacion'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
	$num = mysql_num_rows($res);

	if($num > 0)
        {
        $rows = mysql_fetch_array($res);
		$val = $rows[ 'Detval' ];
        }

	return $val;
}
 
function consultarInfoPaciente($conex, $historia, $ingreso){
	
	global $wbasedato;
	global $wbasedatocliame;
	global $wemp_pmla;
	
	// $q = "SELECT 
			// C.inghis,pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre, pacnac, pacsex, ingnco
		// FROM 
			// root_000036, root_000037, ".$wbasedato."_000016 C, ".$wbasedatocliame."_000101 D
		// WHERE
			// oriced       = pacced
			// AND oritid   = pactid 
			// AND orihis   = '".$historia."'			
			// AND oriori   = '".$wemp_pmla."'
            // AND C.inghis = orihis 
			// AND C.inghis = D.inghis
			// AND C.inging = D.ingnin
			// AND inging   = oriing ";
	$q = "SELECT movhos_16.inghis, movhos_16.ingnre,root_36.pacno1, root_36.pacno2, root_36.pacap1, root_36.pacap2, root_36.pactid, root_36.pacced, root_36.pacnac, root_36.pacsex, cliame_101.ingnco 
			FROM ".$wbasedato."_000016 movhos_16, root_000037 root_37, root_000036 root_36, ".$wbasedatocliame."_000101 cliame_101
		   WHERE movhos_16.Inghis='".$historia."' 
			 AND movhos_16.Inging='".$ingreso."'
			 AND root_37.Orihis=movhos_16.Inghis
			 AND root_37.Oriori='".$wemp_pmla."'
			 AND root_36.Pacced=root_37.Oriced
			 AND root_36.Pactid=root_37.Oritid
			 AND cliame_101.Inghis=movhos_16.Inghis
			 AND cliame_101.Ingnin=movhos_16.Inging;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	$info = array();
	if ($num > 0)
	{		
		$info = mysql_fetch_assoc($res);		
	}
    
	return $info;
}
	
function consultarDxs( $conex, $wemp_pmla, $whce, $his, $ing ){

	return consultarUltimoDiagnosticoHCE( $conex, $wemp_pmla, $whce, $his, $ing );
	
	// $val = "";

	// $camposRoot = consultarAliasPorAplicacion( $conex, $wemp_pmla, "dxsHce" );

	// if( !empty( $camposRoot ) ){
		
		// $campos = explode( ",", $camposRoot );
		
		// for( $i = 0; $i < count( $campos ); $i++ ){
		
			// list( $tabla, $cmp ) = explode( "-", $campos[$i] );
			
			// if( $i > 0 ){
				// $sql .= " UNION ";
			// }
			
			// $sql .= "SELECT
						// *
					// FROM
						// {$whce}_{$tabla}
					// WHERE
						// movhis = '$his'
						// AND moving = '$ing'
						// AND movcon = '$cmp'
					// ";
		// }
		
		// $sql .= " ORDER BY fecha_data DESC, hora_data DESC";
		
		// $res = mysql_query( $sql , $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		// // for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		// $i = 0;
		// if( $rows = mysql_fetch_array( $res ) ){
			
			// if( trim( strip_tags( trim( $rows[ 'movdat' ] ) ) ) != '' ){
				// // echo "<br>".trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// if( $i == 0 ){
					// $val .= trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// }
				// else{
					// $val .= "\n".trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// }
			// }
		// }
	// }
	
	// return $val;
}

	// Función que retorna la edad con base en la fecha de nacimiento
	function obtenerSexo($sexo)
	{
		if($sexo=='F')
			return "Femenino";
		else
			return "Masculino";
	}

	// // Función que retorna la edad con base en la fecha de nacimiento
	// function calcularEdad($fechaNacimiento)
	// {
		// $ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
		// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		// $ann1=($aa - $ann)/360;
		// $meses=(($aa - $ann) % 360)/30;
		// if ($ann1<1){
			// $dias1=(($aa - $ann) % 360) % 30;
			// // $wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
			// $wedad=(string)(integer)$meses." mes(es) ";
		// } else {
			// $dias1=(($aa - $ann) % 360) % 30;
			// //$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
			// $wedad=(string)(integer)$ann1." a&ntilde;o(s) ";
		// }

		// return $wedad;
	// }

	// Función que retorna la edad con base en la fecha de nacimiento
	function calcularEdad($conex,$wbasedato,$historia,$ingreso,$fechaNacimiento)
	{
		// si tiene alta definitiva debe calcular la edad con la fecha de egreso, de lo contrario calcular la edad con la fecha actual
		$fechaEgreso = consultarFechaEgreso($conex,$wbasedato,$historia,$ingreso);
		
		$fechaCalculoEdad = date("Y-m-d");
		if($fechaEgreso!="")
		{
			$fechaCalculoEdad = $fechaEgreso;
		}
		
		$fecha_nac = new DateTime(date("Y-m-d",strtotime($fechaNacimiento)));
		$fechaActual =  new DateTime($fechaCalculoEdad);
		
		$edad = date_diff($fechaActual,$fecha_nac);
		// echo "<pre>".print_r($fecha_nac,true)."<pre>";
		// echo "<pre>".print_r($fechaActual,true)."<pre>";
		// echo "<pre>".print_r($edad,true)."<pre>";
		
		if ($edad->y<1)
		{
			$wedad = $edad->m." mes(es) ";	
		}
		else
		{
			$wedad = $edad->y." a&ntilde;o(s) ";
		}
		
		return $wedad;
	}

	function valorBoleanoHCE($valor)
	{
		$strValor = explode("-",$valor);
		return $strValor[1];
	}


	function dimensionesImagen($idemed)
	{
		global $altoimagen;
		global $anchoimagen;

		// Obtengo las propiedades de la imagen, ancho y alto
		// list($widthimg, $heightimg) = getimagesize('../../images/medical/hce/'.$idemed.'.png');
		// if($heightimg==0)
			// $heightimg=1;

		$altoimagen = '27';
		// $anchoimagen = (27 * $widthimg) / $heightimg;
		// if($anchoimagen<81)
			$anchoimagen = 81;
	}

	function pintarEncabezado($tituloOrden,$whistoria,$nroOrden, $wingreso, $cconombre)
	{
		global $dia;
		global $mes;
		global $anio;
		global $institucion;

		global $conex;
		global $wemp_pmla;
		global $wdiagnostico;
		global $wbasedatohce;	
		global $wbasedato;
		global $wbasedatocliame;
		global $wtodos_ordenes;		
		global $origen;
		global $tipo;
		
		$nroOrden = explode( "-", $nroOrden )[1];
		
		$fecha = date("Y-m-d");
		
		$datos_paciente = consultarInfoPaciente($conex, $whistoria, $wingreso);
		
		$ingnre = $datos_paciente['ingnre'];	
		$pacced = $datos_paciente['pacced'];
		$pactid = $datos_paciente['pactid'];
		$pacno1 = $datos_paciente['pacno1'];
		$pacno2 = $datos_paciente['pacno2'];
		$pacap1 = $datos_paciente['pacap1'];
		$pacap2 = $datos_paciente['pacap2'];
		$pacnac = $datos_paciente['pacnac'];
		$pacsex = $datos_paciente['pacsex'];
		$numcontrato = $datos_paciente['ingnco'];
		$wdiagnostico = consultarDxs( $conex, $wemp_pmla, $wbasedatohce, $whistoria, $wingreso );
		$tarifa = traer_tarifa($wemp_pmla,$whistoria, $wingreso);			
		$ruta = '/var/www/matrix/hce/procesos/impresion_ordenes';
		
		
		//Si se imprime desde el programa de impresion de ordenes se genera el codigo de barras en una carpeta, sino se carga
		//desde el script y se imprime directamente en la pagina.
		if($wtodos_ordenes == 'on'){
						
			$tipo = "tarifa";				
			$nombre = 'tar_'.$whistoria.$wingreso.$fecha;
			
			if($tarifa != ''){
				
				generarcodigodebarras($tarifa." ",170,40,100,"JPEG", $ruta, $nombre);
			
			}
			
			$nombre = 'historia_'.$whistoria.$wingreso.$fecha;
			generarcodigodebarras($whistoria,170,40,100,"JPEG", $ruta, $nombre);
			
			$img_codigo_barras_tar = "<img src='tar_".$whistoria.$wingreso.$fecha.".jpg'>";
			$img_codigo_barras_his = "<img src='historia_".$whistoria.$wingreso.$fecha.".jpg'>";
			// $logo = '<img src="../../../images/medical/root/'.$institucion->baseDeDatos.'.JPG" width="148" heigth="53" border="0" align="left">';			
			$logo = '<img src="../../../images/medical/root/HCE'.$wemp_pmla.'.jpg" width="148" heigth="53" border="0" align="left">';			
			
		}else{		
			
			if($tarifa != ''){
				
				$img_codigo_barras_tar = "<img src='../../../include/root/clsGenerarCodigoBarras.php?width=170&height=40&barcode=$tarifa&historia=$whistoria&ingreso=$wingreso&tarifa=$tarifa &tipo=tarifa&wtodos_ordenes=$wtodos_ordenes'>";
			
			}else{
				
				$img_codigo_barras_tar = "";
			}
			
			$img_codigo_barras_his = "<img src='../../../include/root/clsGenerarCodigoBarras.php?width=170&height=40&barcode=$whistoria&historia=$whistoria&ingreso=$wingreso&tipo=historia&wtodos_ordenes=$wtodos_ordenes'>";
			// $logo = '<img src="../../images/medical/root/'.$institucion->baseDeDatos.'.JPG" width="148" heigth="53" border="0" align="left">';			
			$logo = '<img src="../../images/medical/root/HCE'.$wemp_pmla.'.jpg" width="148" heigth="53" border="0" align="left">';			
			
		}
		
		$img_nro_orden = "";
		$textoNumero = " &nbsp; ";
		$textoOrden = " &nbsp; ";
		if(isset($nroOrden) && trim($nroOrden)!=""){			
			$img_nro_orden = "<img src='../../../include/root/clsGenerarCodigoBarras.php?width=170&height=40&barcode=$nroOrden'>";
			
			$textoNumero = $nroOrden;
			$textoOrden = "Orden ";
		}

		$htmlencabezado .= '
					<table style="border: 0px; width: 740px;vertical-align:middle;font-size:8pt;" cellpadding="2" cellspacing="2">
						<tbody>
							<tr class="encabezado">
								<td rowspan=2>
									'.$logo.'
								</td>
								<td class="encabezadoExamen" colspan=2>
									'.$textoOrden.'
								</td>
								<td colspan=2>
									'.$img_nro_orden.'
								</td>
								<td colspan=2>
									'.$textoNumero.'
								</td>
								<td class="encabezadoExamen" style=""  colspan=2>
									<div>'.$tituloOrden.'</div>
								</td>
							</tr>
							<tr>
								<td class="encabezadoExamen" colspan=2>
									<b>Historia '.$whistoria.'</b>
								</td>
								<td class="encabezadoExamen" style="align:left;" colspan=2>
									'.$img_codigo_barras_his.'
								</td>
								<td class="encabezadoExamen" colspan=2>
									<b>Tarifa '.$tarifa.'</b>
								</td>
								<td class="encabezadoExamen" colspan=2>
									'.$img_codigo_barras_tar.'
								</td>
							</tr>
							
							<tr class="encabezado">
								<td colspan=3>
									<div class="encabezadoEmpresa">PROMOTORA MEDICA LAS AMERICAS S.A. <br />Nit. 8000670659
								</td>
							</tr>
						</tbody>
					</table>';
		/********************************************************************************/
		
		// /********************************************************************************
		 // * Forma 1
		 // ********************************************************************************/
		// // Inicio tabla logo
		// $htmlencabezado .= '
				  // <table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
					// <tbody>
					  // <tr class="encabezado">
					  // <tr class="encabezado">
						// <td style="width: 15%;" rowspan=5>
							// '.$logo.'
						  // </td>
						// <td colspan=6><div class="encabezadoExamen" style="display:flex;justify-content:center;">'.$textoNumero.'</div>
					  // <tr class="encabezado">';

		// // Contenido tabla logo
		// $htmlencabezado .= '
						  // <td style="width: 42.5%;" nowrap colspan=3>
							// <div class="encabezadoEmpresa">PROMOTORA MEDICA LAS AMERICAS S.A. <br />Nit. 8000670659
						  // </td>
						  // <td style="width: 42.5%;"  colspan=3>
							// <div class="encabezadoExamen">'.$tituloOrden.'</div>
						  // </td>
						  
						// <tr>
							// <td style="width: 15%;" colspan=3>
								// <div class="encabezadoExamen" style="display:flex;justify-content:right;flex-wrap:wrap;overflow:hidden;height:30px;align-items:center;">
									// <b>Historia '.$whistoria.'</b>
									// <span style="text-align:right;justify-content: center;display: flex;">'.$img_codigo_barras_his.'</span>
								// </div>
							// </td>
							// <td style="width: 15%;" colspan=3>
								// <div class="encabezadoExamen" style="display:flex;justify-content:center;flex-wrap:wrap;overflow:hidden;height:30px;align-items:center;">
									// <b>Tarifa '.$tarifa.'</b>'.$img_codigo_barras_tar.'
								// </div>
							// </td>';

		// // Fin tabla logo
		// $htmlencabezado .= '
					  // </tr>
					// </tbody>
				  // </table>';
		// /********************************************************************************/
		
		
		
		
		
		
		
		
		// /********************************************************************************
		 // * Original
		 // ********************************************************************************/
		// // Inicio tabla logo
		// $htmlencabezado .= '
				  // <table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
					// <tbody>
					  // <tr class="encabezado">';

		// // Contenido tabla logo
		// $htmlencabezado .= '
						// <td style="width: 15%;">
							// '.$logo.'
						  // </td>
						  // <td style="width: 30%;" nowrap>
							// <div class="encabezadoEmpresa">PROMOTORA MEDICA LAS AMERICAS S.A. <br />Nit. 8000670659
						  // </td>
						  // <td style="width: 15%;">
							// <div class="encabezadoExamen">'.$textoNumero.'</div>
							// <div class="encabezadoExamen"><br>'.$img_codigo_barras_his.' <br> '.$img_codigo_barras_tar.'</div>
						  // </td>
						  // <td style="width: 40%;">
							// <div class="encabezadoExamen">'.$tituloOrden.'</div>
						  // </td>';

		// // Fin tabla logo
		// $htmlencabezado .= '
					  // </tr>
					// </tbody>
				  // </table>';
		// /********************************************************************************/
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  

		// División tabla principal
		$htmlencabezado .= '
			  </td>
			</tr>

			<tr>
			  <td>';

		// Inicio tabla encabezado
		$htmlencabezado .= '
				<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td>';


		// Inicio tablas datos paciente
		$htmlencabezado .= '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr  style="height:17px;background-color:#EEEDED;">
							  <td colspan="6">
								&nbsp; <b>Información del Usuario</b>
							  </td>
							  <td style="width:35px; text-align:center;">
								<b>Día</b>
							  </td>
							  <td style="width:40px; text-align:center;">
								<b>Mes</b>
							  </td>
							  <td style="width:40px; text-align:center;">
								<b>Año</b>
							  </td>
							</tr>
						  </tbody>
						</table>
			';
		
		$texto_fecha = "Fecha de solicitud";
		
		if($wtodos_ordenes == 'on'){
		
			$texto_fecha = "Fecha";
			
			$fechaEgreso = consultarFechaEgreso($conex,$wbasedato,$whistoria,$wingreso);
	
			$fechaCalculoEdad = date("Y-m-d");
			if($fechaEgreso!="")
			{
				$texto_fecha = "Fecha de egreso: ";
				list($anio, $mes, $dia) = explode("-",$fechaEgreso);
			}
		}
		
		$q_ubi = "SELECT habcod, habhis, habcpa, habzon
					FROM ".$wbasedato."_000020
				   WHERE Habhis = '".$whistoria."'
				     AND Habing = '".$wingreso."'" ;
		$res_ubi = mysql_query($q_ubi, $conex);
		$row_ubi = mysql_fetch_array($res_ubi);
		
		//Imprime la ubicacion del paciente.
		if(trim($row_ubi['habcpa']) != ""){
		
			if(trim($row_ubi['habzon']) != 'NO APLICA' or trim($row_ubi['habzon']) != ''){			
			
				$zona = $row_ubi['habzon']."<br>";
			}
			
			$ubicacion = $zona.$row_ubi['habcpa'];
			
		}else{
		
			$ubicacion = $row_ubi['habcod'];
		}
		
		if(trim($ubicacion) == ''){
		
			$q_ubi = "SELECT Cconom
						FROM ".$wbasedato."_000011, ".$wbasedato."_000018
				       WHERE ubihis = '".$whistoria."'
				         AND ubiing = '".$wingreso."'
						 AND ubisac = Ccocod " ;
			$res_ubi = mysql_query($q_ubi, $conex);
			$row_ubi = mysql_fetch_array($res_ubi);
			
			$ubicacion = $row_ubi['Cconom']."<br>Sin ubicación asignada.";
		}
		
		$htmlencabezado .= '
						<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
						  <tbody>
							<tr style="height:17px;">
							  <td style="width:5%;">
								<b>Entidad:</b>
							  </td>
							  <td style="width:35%;">
								&nbsp;'.$ingnre.'
							  </td>
							  <td style="width:7%;">
								&nbsp;<b>Origen:</b>
							  </td>
							  <td style="width:10%;">
								'.$ubicacion.'
							  </td>							 
							  <td style="width:17%; text-align:center;">
								<b>'.$texto_fecha.'</b>
							  </td>
							  <td style="width:4%; text-align:center;">
								<b>'.$dia.'</b>
							  </td>
							  <td style="width:4%; text-align:center;">
								<b>'.$mes.'</b>
							  </td>
							  <td style="width:5%; text-align:center;">
								<b>'.$anio.'</b>
							  </td>
							  ';

		$htmlencabezado .= '
							</tr>
						  </tbody>
						</table>
						';

		$htmlencabezado .= '
						<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
						  <tbody>
							<tr style="height:17px;">
							  <td style="text-align:left;">
								<b>Nro Id:</b>
							  </td>
							  <td style="text-align:left;">
								'.$pacced.'
							  </td>
							  <td style="text-align:right;">
								<b>Tipo doc:</b>
							  </td>
							  <td>
								'.$pactid.' &nbsp;&nbsp;&nbsp;&nbsp;
							  </td>
							  <td>
								<b>Edad:</b> '.calcularEdad($conex,$wbasedato,$whistoria,$wingreso,$pacnac).'
							  </td>
							  <td>
								<b>Sexo:</b> '.obtenerSexo($pacsex).'
							  </td>
							  <td>
								<b>Nombre:</b>							 
								'.$pacno1.' '.$pacno2.' '.$pacap1.' '.$pacap2.'
							  </td>
							  <td>
								<b>Historia:</b>
							  </td>
							  <td style="text-align:left;">
								'.$whistoria.' - '.$wingreso.'
							  </td>
							</tr>
						  </tbody>
						</table>
			';
		
		$htmlencabezado .= '
						<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr style="height:21px;">
							  <td style="width:25%; text-align:left;">
								&nbsp;<b>Nro Contrato: </b>'.$numcontrato.'
							  </td>							
							  <td style="width:75%; text-align:left;">
								&nbsp;<b>Diagnóstico: </b> '.$wdiagnostico.'
							  </td>
							</tr>
						  </tbody>
						</table>
			';
		// Fin tablas datos paciente


		// Fin tabla encabezado
		$htmlencabezado .= '
					  </td>
					</tr>
				  </tbody>
				</table>';
				
		return $htmlencabezado;
	}

	function pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom, $cod_med)
	{
		global $altoimagen;
		global $anchoimagen;
		global $wtodos_ordenes;
		global $origen;		

		// Inicio tabla profesional
		$htmlpiepagina .= '
				<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td>';

		$htmlpiepagina .= '
						<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr>
							  <td>
								<br /><b>PROFESIONAL</b>
							  </td>
							</tr>
						  </tbody>
						</table>
			';

		$htmlpiepagina .= '
						<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr>
							  <td><div class="campoFirma">';
		
		if(file_exists('../../images/medical/hce/Firmas/'.$cod_med.'.png'))
		{
			if($wtodos_ordenes != 'on'){
			
				$htmlpiepagina .= '				<img src="../../images/medical/hce/Firmas/'.$cod_med.'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';	
				
			}else{
				
				$htmlpiepagina .= '				<img src="../../../images/medical/hce/Firmas/'.$cod_med.'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';	
				
			}
		}	
		else
		{
			$htmlpiepagina .= '&nbsp;';
		}
		
		$htmlpiepagina .= '				  </div></td>
							</tr>
							<tr>
							  <td>
								&nbsp;Nombre del Médico: '.$medap1.' '.$medap2.', '.$medno1.' '.$medno2.' <br />
								&nbsp;Identificación: '.$medtdo.' '.$meddoc.' <br />
								&nbsp;Registro Médico: '.$medreg.' <br />
								&nbsp;Especialidad: '.$espnom.' <br />
							  </td>
							</tr>
						  </tbody>
						</table>
			';

		// Fin tabla profesional
		$htmlpiepagina .= '
					  </td>
					</tr>
				  </tbody>
				</table>';

		// Inicio tabla pie de página	
		$htmlpiepagina .= '
				<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td style="text-align:center;" calss="descripcion">
						Diagonal 75B N. 2A-80/140 (057) 4 3421010 (057) 4 3412946 Medellín, Colombia
					  </td>
					</tr>
				  </tbody>
				</table>';
		// Fin tabla pie de página
		
		$htmlpiepagina .= '
				<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td style="text-align:center;" calss="descripcion">
						<b>- Firmado electrónicamente -</b>
					  </td>
					</tr>
				  </tbody>
				</table>';
				
		return $htmlpiepagina;
		

	}
	
	function consultarNombrePaciente($conex, $wemp_pmla, $whistoria)
	{
		$query = "SELECT CONCAT_WS(' ',Pacno1,Pacno2,Pacap1,Pacap2) AS nombrePaciente
					FROM root_000037 
			  INNER JOIN root_000036
					  ON Pactid=Oritid
					 AND Pacced=Oriced
				   WHERE Orihis='".$whistoria."' 
					 AND Oriori='".$wemp_pmla."';";
					 
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$nombrePaciente = "";
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			$nombrePaciente = $row['nombrePaciente'];
		}
		
		return $nombrePaciente;
	}
	
	function consultarResponsablePaciente($conex, $wbasedatoMovhos, $historia, $ingreso)
	{
		$query = "SELECT Ingnre 
					FROM ".$wbasedatoMovhos."_000016 
			       WHERE Inghis='".$historia."' 
					 AND Inging='".$ingreso."';";
					 
		$res = mysqli_query($conex,$query) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$nombreEntidad = "";
		if($num>0)
		{
			$row = mysqli_fetch_array($res);
			$nombreEntidad = $row['Ingnre'];
		}
		
		return $nombreEntidad;
		
	}
	
	function consultarTipoOrdenesGeneradas($conex, $wbasedatohce, $arrOrden)
	{
		$tipos = str_replace(",","','",$arrOrden);
		
		$queryTipoOrden = "SELECT Descripcion 
							 FROM ".$wbasedatohce."_000015 
							WHERE Codigo IN ('".$tipos."');";
		
		$res = mysqli_query($conex,$queryTipoOrden) or die ("Error: " . mysqli_errno($conex) . " - en el query:  - " . mysqli_error($conex));
		$num = mysql_num_rows($res);
		
		$tiposOrdenesGeneradas = "";
		
		if(strpos($arrOrden,"medtos")!==false)
		{
			$tiposOrdenesGeneradas .= "- Ordenes de medicamentos<br>";
		}
		
		if($num>0)
		{
			while($row = mysqli_fetch_array($res))
			{
				$tiposOrdenesGeneradas .= "- Ordenes de ".strtolower($row['Descripcion'])."<br>";
			}
		}	
		
		return $tiposOrdenesGeneradas;
	}

/////////////////////////// FIN FUNCIONES /////////////////////////
///////////////////////////////////////////////////////////////////


session_start();


// Si el usuario no está registrado muestra el mensaje de error, modificacion hecha por Jaime Mejia
if(!isset($_SESSION['user']) && !isset($_GET['automatizacion_pdfs'])){
	echo "<br /> La sessión de usuario ha caducado. Vuelva a entrar a Matrix";
}
else	// Si el usuario está registrado inicia el programa
{
	include_once("root/comun.php");	
	$ccoSF=ccoUnificadoSF();

	$conex = obtenerConexionBD("matrix");
	$html .= estilo();
	borrarOrdenesImpresas(); //Funcion que borra todos los archivos en un lapso de tiempo.
	
	//Modificacion Jaime Mejia para no sacar la ventana
	if(!isset($_GET['automatizacion_pdfs'])){
		echo "<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";
	}
	if($boton_imp == '' and $ctcNoPos == '' and $wtodos_ordenes == ''){
	echo "<p align=center><input type='button' class='printer' value='Imprimir'></p>";
	}
	
	echo "<div class=areaimprimir>";	
	echo '<input type="hidden" name="wemp_pmla" id="wemp_pmla" value="'.$wemp_pmla.'">';
	echo '<input type="hidden" name="enviarCorreo" id="enviarCorreo" value="'.$enviarCorreo.'">';
	echo '<input type="hidden" name="emailEnviarCorreo" id="emailEnviarCorreo" value="'.$emailEnviarCorreo.'">';
	echo '<input type="hidden" name="envioPaciente" id="envioPaciente" value="'.$envioPaciente.'">';

	// Se obtiene el valor para $wbasedato
	$wbasedatohce             = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wbasedato                = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wbasedatocliame          = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
	$wcenpro                  = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$caracteresObservacionesN = consultarAliasPorAplicacion($conex, $wemp_pmla, "caracteresObserNutricionesOrdenes");

	$institucion = consultarInstitucionPorCodigo($conex,$wemp_pmla);

	// Obtengo los datos del usuario
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));
	
	$wdiagnostico = consultarDxs( $conex, $wemp_pmla, $wbasedatohce, $whistoria, $wingreso );
	

	// Aca se coloca la ultima fecha de actualización
	$wactualiz = " 2020-05-04";

	/***********************************************
	*********** P R I N C I P A L ******************
	************************************************/

	// Titulo de la página
	$titulo = "ORDENES MEDICAS";


	// Se asigna el valor por defecto para el filtro de impresión
	$filtroArtImprimir = " AND Kadimp = 'on' "; 	// " AND Kadimp = 'on' ";
	$filtroProImprimir = " AND Detimp = 'on' ";

	// Si se llama desde la pestaña de Otras ordenes, se establece que solo se va a imprimir
	// los procedimientos de la orden y no los medicamentos
	// Esto aplica solo para ordenes clinica por la pestaña de alta
	if(isset($alt) && $alt=="on")
	{
		$filtroPestana = " AND Detalt = 'on' ";
	}
	else
	{
		$filtroPestana = " AND Detalt != 'on' ";
	}


	$mostrarSoloArticulos = false;
	$mostrarSoloProcedimientos = false;
	$mostrarSoloCTC = false;
	$mostrarSoloCTCArt = false;
	$mostrarSoloCTCPro = false;

	// Si tipoimp viene con el valor impart se establece que solo se va a imprimir los artículos
	if(isset($tipoimp) && $tipoimp=="impexa")
		$mostrarSoloArticulos = true;

	// Si tipoimp viene con el valor imppro se establece que solo se va a imprimir los procedimientos
	if(isset($tipoimp) && $tipoimp=="imppro")
		$mostrarSoloProcedimientos = true;

	// Si tipoimp viene con el valor impctc se establece que solo se va a imprimir los formularios de CTC
	if(isset($tipoimp) && $tipoimp=="impctc")
		$mostrarSoloCTC = true;
		
	// Si tipoimp viene con el valor impctc se establece que solo se va a imprimir los formularios de CTC
	if(isset($tipoimp) && $tipoimp=="impctcart"){
		$mostrarSoloCTCArt = true;
	}
		
	// Si tipoimp viene con el valor impctc se establece que solo se va a imprimir los formularios de CTC
	if(isset($tipoimp) && $tipoimp=="impctcpro"){
		$mostrarSoloCTCPro = true;
	}

	// Se define si solo se va a imprimir un medicamento o procedimiento especifico
	// Esto pasa cuando se llama desde la impresión de CTC, solo se imprime
	// el medicamento o el procedimiento correspondiente al CTC solicitado
	$filtroProcedimiento = "";
	if(isset($pro) && $pro!="")
	{
		$filtroProcedimiento = "	  AND Detcod = '".$pro."' ";
		$mostrarSoloProcedimientos = true;
		$filtroProImprimir = " ";
	}
	$filtroArticulo = "";
	if(isset($art) && $art!="")
	{
		$filtroArticulo = "	  AND Kadart = '".$art."' ";
		$mostrarSoloArticulos = true;
		$filtroArtImprimir = " ";
	}


	// Datos cronológicos
	$anio = date("Y");
	$mes = date("m");
	$dia = date("d");

	if(isset($fec) && $fec!='')
		$fecha = $fec;
	else
		$fecha = date("Y-m-d");

	$observaciones = "";

	// Arreglo que me guardará la lista de medicamentos No Pos ordenados
	$medicamentosNoPos = array();

	// Arreglo que me guardará la lista de procedimientos No Pos ordenados
	$examenesNoPos = array();
	
	$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	
	$filtro_reemplazado = " AND Kadaan = '' ";
	$filtro_reemplazado_genericos = "	AND SUBSTRING_INDEX(Kadaan,',',1) IN (  
										SELECT artcod FROM	{$wbasedato}_000068,{$wcenmez}_000002,{$wcenmez}_000001
											  WHERE	artcod = arkcod
												AND arttip = tipcod
												AND tiptpr = arktip
												AND artest = 'on'
												AND arkest = 'on'
												AND tipest = 'on' ) "; //Solo muestra los que registra el medico, no los reemplazados del perfil, si esta variable tiene este valor, no se tiene en cuenta si esta suspendido.	
	$filtro_med_pendientes = "";
	$wult_fec_kardex = "";	
	$wagrupacion = "";
	
	//Si la consulta es por articulo, mostrara solo ese registro, esto es en el caso de la impresion de CTC.
	if($art != ''){
	
		if($impOrdCTC=="on")
		{
			$empresaEquivalente = consultarEmpresaConEquivalencia( $conex, $wemp_pmla, $wbasedato, $whistoria, $wingreso );

			// var_dump($empresaEquivalente);
			if($empresaEquivalente == true)
			{
				$reemplazarMedCTC = consultarMedicamentoEquivalenteCTC( $wbasedato, $art );	
				// var_dump($reemplazarMedCTC);
			}
		}
	
	$filtro_fecha = "";
	$filtro_suspendido = "";
	$filtro_firmado = "";
	$filtro_reg_nuevo = "";
	$filtro_reemplazado = "";
	//Busca la ultima fecha de generacion de los medicamentos y que hayan sido con ordenes.
	$q_max_fec = "SELECT ctcfkx
					FROM ".$wbasedato."_000134
				   WHERE ctchis = '".$whistoria."' 
					 AND ctcing = '".$wingreso."'
					 AND ctcart = '".$art."'
				ORDER BY ctcfkx DESC ";
	$res_max_fec = mysql_query($q_max_fec,$conex) or die (mysql_errno()." - ".mysql_error());
	$row_max_fec = mysql_fetch_array($res_max_fec);
	
	$wfecha_ctc_art = "	  AND Kadfec = '".$row_max_fec['ctcfkx']."' ";
	
	}else{
	
	$filtro_fecha = "	  AND Kadfec = '".$fecha."' ";
	//$filtro_suspendido = "	  AND Kadsus != 'on' "; //Se comenta esta variable ya que deben salir los suspendidos pero que solo hayan sido enviados por el medico, osea que el el campo Kadaan = ''.
	$filtro_firmado = "	  AND Kadusu != ''  ";	
	
	}
	
	//Esta variable viene del programa rep_PacientesEgresadosActivosOrdenes.php
	if($wtodos_ordenes == 'on'){
		
		$filtro_fecha = ""; //Busca todas las fechas	
		//$filtro_reg_nuevo = " AND Kadreg = ''"; //Busca todas las fechas	
		$filtro_med_pendientes = "";
		$filtroArtImprimir = ""; //Muestra todos los articulos asi no esten activos para imprimir.
		$wagrupacion = " GROUP BY Kadart, Kadcfr, Kadper, Kadvia, Kadcnd, Kadusu, Kadfin, Kadido "; //Como se estan buscando todas las fechas entonces debe agrupar por estos campos.
		
		}
	
	if(!isset($orden)){
			
			$orden = 'asc';
			$ordenes_pro_exa = 'ksort';
		}
		
		//Orden en que se mostrara la informacion para los medicamentos y examenes.
		switch($orden){
			
				case 'asc':	
							$ordenar = $orden;
							$ordenes_pro_exa = 'ksort';
				break;
				
				case 'desc': 
							$ordenar = $orden;
							$ordenes_pro_exa = 'krsort';
				break;
				
				default: 
							$ordenar = 'desc';
							$ordenes_pro_exa = 'ksort';
		}

	if( !( $mostrarSoloCTCPro || $mostrarSoloCTCArt ) )
	if($mostrarSoloArticulos || (!$mostrarSoloProcedimientos && !$mostrarSoloCTC))
	{
		/**************************************************************************
		********************* IMPRESIÓN MEDICAMENTOS EXTERNOS *********************
		***************************************************************************/
				
		if($alt == 'off'){			
					
				// Consulto los medicamentos del paciente
				$q = "SELECT * FROM ( 
					   SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
					."   FROM ".$wbasedato."_000060 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i  "
					." 	WHERE Kadhis = '".$whistoria."' "
					."	  AND Kading = '".$wingreso."'  "
					."	  AND Kadest = 'on'  "							
					."	  AND Kadart = Artcod "
					."	  AND Kadhis = Inghis "
					."	  AND Kading = Inging "
					."	  AND Inghis = Ubihis "
					."	  AND Inging = Ubiing "
					."	  AND Kadhis = Orihis  "
					."	  AND Karhis = Kadhis  "
					."	  AND Karing = Kading  "				
					."	  AND Karord = 'on'  "				
					."	  AND i.Fecha_data = Kadfec  "
					."	  AND Oriori = '".$wemp_pmla."'"
					.$filtroArticulo
					.$filtro_fecha
					.$filtroArtImprimir
					.$filtro_firmado
					.$filtro_suspendido
					.$filtro_reemplazado				
					.$filtro_med_pendientes
					.$wfecha_ctc_art
					.$filtro_reg_nuevo;
			
					$q .= " UNION ";

					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000054 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "									
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "									
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "					
						."	  AND i.Fecha_data = Kadfec  "
						."	  AND Oriori = '".$wemp_pmla."'"
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo;
					
					/////// Dosis Adaptadas no reemplazadas
					$q .= " UNION ";

					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, '' as Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000054 b, ".$wcenpro."_000002 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i, ".$wcenpro."_000001 j, ".$wbasedato."_000068 k  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "				
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "					
						."	  AND tiptpr = arktip  "
						."	  AND tipcod = arttip  "					
						."	  AND tipcdo != 'on'  "					
						."	  AND tippro='on'  "					
						."	  AND tipnco='on'  "					
						."	  AND tipina='off'  "					
						."	  AND tipiar='off'  "					
						."	  AND i.Fecha_data = Kadfec  "
						."	  AND kadart not in (SELECT Arkcod FROM ".$wbasedato."_000068 WHERE Arktip = Tiptpr) "
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo
						."	  AND Oriori = '".$wemp_pmla."'";
						
					$q .= " UNION ";
					
					//Dosis adaptadas en la 60
					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, '' as Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000060 b, ".$wcenpro."_000002 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i, ".$wcenpro."_000001 j, ".$wbasedato."_000068 k  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "				
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "					
						."	  AND tiptpr = arktip  "
						."	  AND tipcod = arttip  "					
						."	  AND tipcdo != 'on'  "					
						."	  AND tippro='on'  "					
						."	  AND tipnco='on'  "					
						."	  AND tipina='off'  "					
						."	  AND tipiar='off'  "					
						."	  AND i.Fecha_data = Kadfec  "
						."	  AND kadart not in (SELECT Arkcod FROM ".$wbasedato."_000068 WHERE Arktip = Tiptpr) "
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo
						."	  AND Oriori = '".$wemp_pmla."'";
						
						$q .= " UNION ";
						
					//Busca en la tabla 54 las nutriciones
					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, '' as Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000054 b, ".$wcenpro."_000002 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i, ".$wcenpro."_000001 j, ".$wbasedato."_000068 k  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "
						."	  AND tiptpr = arktip  "
						."	  AND tipcod = arttip  "
						."	  AND tipcdo != 'on'  "
						."	  AND tippro = 'on'  "					
						."	  AND tipina != ''  "					
						."	  AND tipiar != 'on'  "					
						."	  AND i.Fecha_data = Kadfec "
						."	  AND kadart not in (SELECT Arkcod FROM ".$wbasedato."_000068 WHERE Arktip = Tiptpr) "
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo
						."	  AND Oriori = '".$wemp_pmla."'";
						
						$q .= " UNION ";
					//Busca en la tabla 60 
					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, '' as Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000060 b, ".$wcenpro."_000002 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i, ".$wcenpro."_000001 j, ".$wbasedato."_000068 k  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "
						."	  AND tiptpr = arktip  "
						."	  AND tipcod = arttip  "
						."	  AND tipcdo != 'on'  "
						."	  AND tippro = 'on'  "					
						."	  AND tipina != ''  "					
						."	  AND tipiar != 'on'  "					
						."	  AND i.Fecha_data = Kadfec "
						."	  AND kadart not in (SELECT Arkcod FROM ".$wbasedato."_000068 WHERE Arktip = Tiptpr) "
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo
						."	  AND Oriori = '".$wemp_pmla."'";
					
					//Articulos Dosis Adaptada. y nutriciones que nacen en una generica
					$q .= " UNION ";

					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, '' as Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000054 b, ".$wcenpro."_000002 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i, ".$wcenpro."_000001 j, ".$wbasedato."_000068 k  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "				
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "					
						."	  AND tiptpr = arktip  "
						."	  AND tipcod = arttip  "					
						."	  AND tipcdo != 'on'  "					
						."	  AND tippro='on'  "					
						."	  AND tipnco='on'  "					
						."	  AND tipina='off'  "					
						."	  AND tipiar='off'  "					
						."	  AND i.Fecha_data = Kadfec  "
						."	  AND kadart not in (SELECT Arkcod FROM ".$wbasedato."_000068 WHERE Arktip = Tiptpr) "
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado_genericos					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo
						."	  AND Oriori = '".$wemp_pmla."'";
						
					$q .= " UNION ";
					
					//Dosis adaptadas en la 60
					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, '' as Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000060 b, ".$wcenpro."_000002 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i, ".$wcenpro."_000001 j, ".$wbasedato."_000068 k  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "				
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "					
						."	  AND tiptpr = arktip  "
						."	  AND tipcod = arttip  "					
						."	  AND tipcdo != 'on'  "					
						."	  AND tippro='on'  "					
						."	  AND tipnco='on'  "					
						."	  AND tipina='off'  "					
						."	  AND tipiar='off'  "					
						."	  AND i.Fecha_data = Kadfec  "
						."	  AND kadart not in (SELECT Arkcod FROM ".$wbasedato."_000068 WHERE Arktip = Tiptpr) "
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado_genericos					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo
						."	  AND Oriori = '".$wemp_pmla."'";
						
						$q .= " UNION ";
						
					//Busca en la tabla 54 las nutriciones
					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, '' as Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000054 b, ".$wcenpro."_000002 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i, ".$wcenpro."_000001 j, ".$wbasedato."_000068 k  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "
						."	  AND tiptpr = arktip  "
						."	  AND tipcod = arttip  "
						."	  AND tipcdo != 'on'  "
						."	  AND tippro = 'on'  "					
						."	  AND tipina != ''  "					
						."	  AND tipiar != 'on'  "					
						."	  AND i.Fecha_data = Kadfec "
						."	  AND kadart not in (SELECT Arkcod FROM ".$wbasedato."_000068 WHERE Arktip = Tiptpr) "
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado_genericos					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo
						."	  AND Oriori = '".$wemp_pmla."'";
						
						$q .= " UNION ";
					//Busca en la tabla 60 
					$q .= " SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, '' as Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadusu, Kadido, b.id "
						."   FROM ".$wbasedato."_000060 b, ".$wcenpro."_000002 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000037 g, ".$wbasedato."_000053 i, ".$wcenpro."_000001 j, ".$wbasedato."_000068 k  "
						." 	WHERE Kadhis = '".$whistoria."' "
						."	  AND Kading = '".$wingreso."'  "
						."	  AND Kadest = 'on'  "
						."	  AND Kadart = Artcod "
						."	  AND Kadhis = Inghis "
						."	  AND Kading = Inging "
						."	  AND Inghis = Ubihis "
						."	  AND Inging = Ubiing "
						."	  AND Kadhis = Orihis  "
						."	  AND Karhis = Kadhis  "
						."	  AND Karing = Kading  "
						."	  AND Karord = 'on'  "
						."	  AND tiptpr = arktip  "
						."	  AND tipcod = arttip  "
						."	  AND tipcdo != 'on'  "
						."	  AND tippro = 'on'  "					
						."	  AND tipina != ''  "					
						."	  AND tipiar != 'on'  "					
						."	  AND i.Fecha_data = Kadfec "
						."	  AND kadart not in (SELECT Arkcod FROM ".$wbasedato."_000068 WHERE Arktip = Tiptpr) "
						.$filtroArticulo
						.$filtro_fecha
						.$filtroArtImprimir
						.$filtro_firmado
						.$filtro_suspendido
						.$filtro_reemplazado_genericos					
						.$filtro_med_pendientes
						.$wfecha_ctc_art
						.$filtro_reg_nuevo
						."	  AND Oriori = '".$wemp_pmla."'						
						) as t "
						.$wagrupacion
						."	ORDER BY concat(Fecha,' ',Hora) $ordenar ";
				
				//Veronica Arismendy
				//Se valida si viene de rp_PacientesEgresadosActivosOrdenes y si solicito imprimir las ordenes de medicamentos
				if(isset($desdeImpOrden) && $desdeImpOrden === "on"){
					$arrOrdenes = explode(",",$arrOrden );
					if(in_array("medtos", $arrOrdenes)) {	
						$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						$num = mysql_num_rows($res);
						//modificacion Jaime Mejia
						if(isset($_GET['automatizacion_pdfs'])){
							$numeroMedtos = mysql_num_rows($res);							
						}
					}else{
						$num = 0;
					}
				}else{
					$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num = mysql_num_rows($res);
				}
			}else{
			
			//Consulta los articulos de ALTA Jonatan Lopez Aguirre. 07 Oct 2014			
			$q1 .= "SELECT b.Fecha_data Fecha, b.Hora_data Hora, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Kadfir, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, '' AS Ingdir, '' AS Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo, 'off' as Artonc, Kadfir, Kadusu, b.id, Kadido, b.id"
				."   FROM ".$wbasedato."_000168 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000036 g, root_000037 h  "
				." 	WHERE Kadhis = '".$whistoria."' "
				."	  AND Kading = '".$wingreso."'  "						
				.$filtroArtImprimir
				."	  AND Kadest = 'on'  "
				."	  AND Kadint != 'on'  "				
				.$filtroArticulo
				."	  AND Kadart = Artcod "
				."	  AND Kadhis = Inghis "
				."	  AND Kading = Inging "
				."	  AND Inghis = Ubihis "
				."	  AND Inging = Ubiing "
				."	  AND Kadhis = Orihis  "
				."	  AND Kading = Oriing  "
				."	  AND Kadusu != ''  "
				//."	  AND Kadimp = 'on'  "
				."	  AND Kadsus != 'on' "
				."	  AND Oriced = Pacced "
				."	  AND Oritid = Pactid  "
				."	  AND Oriori = '".$wemp_pmla."'"			
				."	ORDER BY concat(Fecha,' ',Hora) $ordenar";		
			
			$res = mysql_query($q1,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			}
		//echo $q;
		$array_medicamentos = array();	
		$array_medicos = array();	

		// Si se encontraron medicamentos ordenados
		if($num > 0){
			
			//Arreglo cuando se imprime desde el programa de impresion de ordenes.
			if($origen != ''){
				
				//Arreglo cuando se imprime desde el programa de impresion de ordenes
				while($row = mysql_fetch_assoc($res)){
					//Se modifica la llave con la que se organizaba el arreglo, antes estaba kadido, ahora es el id del registro, esto porque algunos kadido quedaban en cero y al encontrar el primer cero ya no agrega otro que tenga cero, en cambio el id siempre es diferente. 19 de nov.
					if(!array_key_exists($row['id'], $array_medicamentos)){
										
						$array_medicamentos[$row['id']] = $row;		
					
					}		
				
				}				
		
			}else{
			
				//Arreglo cuando se imprime desde las ordenes
				while($row = mysql_fetch_assoc($res)){
					
					if(!array_key_exists($row['Kadusu'], $array_medicamentos)){
										
						$array_medicamentos[$row['Kadusu']][] = $row;		
					
					}else{
					
						$array_medicamentos[$row['Kadusu']][] = $row;
						
					}			
				
				}
				
			}
		
		}
		else{
			if(isset($_GET['automatizacion_pdfs'])){
				if(strlen($arrOrden) <= 0 && $numeroMedtos == 0){
				$html .= "sin medicamentos";
				echo "sin medicamentos";
				}	
			}
		}
		// echo "<pre>";
		// print_r($array_medicamentos);
		// echo "</pre>";		
		
		if($origen == 'on'){
			
			$cantidad_med = consultar_dato_apl('CantidadMedicamentosImpOrdFact');
						
			$array_medicamentos = array_chunk($array_medicamentos,$cantidad_med);
		
		}else{
			
			$cantidad_med = consultar_dato_apl('CantidadMedicamentosImpOrd');
			
			$array_temp = $array_medicamentos; 
			foreach($array_temp as $key => $med){
						
				$array_medicamentos[$key] = array_chunk($array_temp[$key],$cantidad_med);			
					
			}
					
			
		}
		
		// echo "<pre>";
		// print_r($array_medicamentos);
		// echo "</pre>";
		
		$subEncabezado = "";		
					 
		//Si se imprime desde la pestaña de alta si se muestra la cantidad.
		if($alt == 'on'){	
		
			$columna_cantidad = '<td style="width:9%;"><b>&nbsp;Cantidad</b></td>';
			$dato_cantidad = '<td>&nbsp;'.$cantidad.'</td>';
		
		}
					 
		if(count($array_medicamentos) > 0 and $origen != 'on' ){
			
			foreach($array_medicamentos as $key_medico => $array_paginas){
				
				//Recorro los registros de los medicos que han firmado
				foreach($array_paginas as $key => $medicamentos){
										 
					if(count($medicamentos) > 1){
						
						// Se define el filtro para consultar el médico tratante
						if(isset($ide) && $ide!="")
							$filtroMedico = " Meddoc = '".$ide."' ";
						else
							$filtroMedico = " Meduma = '".$key_medico."' ";

						// Consulto los datos del médico
						$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
								."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
								." 	WHERE ".$filtroMedico." "
								."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
						$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
						$nummed = mysql_num_rows($resmed);
						$rowmed = mysql_fetch_array($resmed);

						// Se calculan las dimensiones para la imagen de la firma del médico
						dimensionesImagen($rowmed['Meddoc']);

						// Datos del médico tratante
						$medtdo = $rowmed['Medtdo'];
						$meddoc = $rowmed['Meddoc'];
						$medno1 = $rowmed['Medno1'];
						$medno2 = $rowmed['Medno2'];
						$medap1 = $rowmed['Medap1'];
						$medap2 = $rowmed['Medap2'];
						$medreg = $rowmed['Medreg'];
						$espnom = $rowmed['Espnom'];
						
						$html .="<div style='page-break-after: always;'>";
						
						$html .= pintarEncabezado('ORDEN MEDICAMENTOS'.$subEncabezado,$whistoria,'', $wingreso, $cconombre);
										
								// Inicio tablas lista detalle						
								$html .='	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
												  <tbody>
													<tr>
													  <td style="height:17px;background-color:#EEEDED;">
														&nbsp; <b>Servicios Solicitados</b>
													  </td>
													</tr>
													<tr>
													  <td  style="height:11px;">
														&nbsp;
													  </td>
													</tr>
												  </tbody>
												</table>';

								// Inicio tabla con lista de medicamentos
								$html .='	<table rules="all" border="1" style="width: 740px;">
												  <tbody>
													<tr style="height:17px;" align="center">
													  <td style="width:70%;">
														<b>&nbsp;Medicamento</b>
													  </td>
													  <td style="width:10%;">
														<b>&nbsp;Presentacion</b>
													  </td>
													  <td style="width:10%;">
														<b>&nbsp;Fecha y Hora</b>
													  </td>
													  '.$columna_cantidad.'
													  <td style="width:10%;">
														<b>&nbsp;Dosis</b>
													  </td>
													  <td style="width:9%;">
														<b>&nbsp;Via</b>
													  </td>
													  <td style="width:11%;">
														<b>&nbsp;Frecuencia</b>
													  </td>
													  <td style="width:10%;">
														<b>&nbsp;Duraci&oacute;n</b>
													  </td>
													  <td style="width:10%;">
														<b>&nbsp;Condici&oacute;n</b>
													  </td>
													</tr>';
									
						
						//Recorro los medicos que han firmado.
						foreach($medicamentos as $key1 => $row){
							
							$kadobs1 = "";
							$desCnd1 = "";
							$es_nutricion = "";
							// // Consulto los datos de la unidad
							$q = " SELECT  Unides "
								."   FROM ".$wbasedato."_000027 "
								." 	WHERE  Unicod = '".$row['Kaduma']."' ";
							$resuni = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							$numuni = mysql_num_rows($resuni);
							$rowuni = mysql_fetch_array($resuni);
							
							//Fraccion 
							$q_fr = " SELECT  Unides "
								."   FROM ".$wbasedato."_000027 "
								." 	WHERE  Unicod = '".$row['Kadufr']."' ";
							$resfr = mysql_query($q_fr,$conex) or die (mysql_errno()." - ".mysql_error());							
							$rowfr = mysql_fetch_array($resfr);
							$fraccion = $rowfr['Unides'];
							$wfraccion =  ucfirst(strtolower($fraccion));
							
							// // Consulto los datos de la presentacion
							$q = " SELECT  Ffanom "
								."   FROM ".$wbasedato."_000046 "
								." 	WHERE  Ffacod = '".$row['Kadffa']."' ";
							$resffa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							$numffa = mysql_num_rows($resffa);
							$rowffa = mysql_fetch_array($resffa);

							// // Consulto los datos de la presentacion
							$q = " SELECT  Viades "
								."   FROM ".$wbasedato."_000040 "
								." 	WHERE  Viacod = '".$row['Kadvia']."' ";
							$resvia = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							$numvia = mysql_num_rows($resvia);
							$rowvia = mysql_fetch_array($resvia);

							// Consulto los datos de la frecuencia de administracion
							$q = " SELECT  Percan, Peruni, Pertip, Perequ "
								."   FROM ".$wbasedato."_000043 "
								." 	WHERE  Percod = '".$row['Kadper']."' ";
							$resfre = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfre = mysql_num_rows($resfre);
							$rowfre = mysql_fetch_array($resfre);

							$frecuencia = 'Cada '.$rowfre['Percan'].' '.$rowfre['Peruni'];
						
							// Consulto el nombre de la familia del medicamento
							$qfam =  " SELECT Famnom, Relcon, Reluni "
									."   FROM ".$wbasedato."_000114 a, ".$wbasedato."_000115 b "
									." 	WHERE Relart = '".$row['Kadart']."' "
									."	  AND Relest = 'on' "
									."	  AND Relfam = Famcod  "
									."	  AND Famest = 'on' "
									."	ORDER BY b.id DESC";

							$resfam = mysql_query($qfam,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfam = mysql_num_rows($resfam);
							
							
							// Consulto el nombre de la codición
							$qCnd =  " SELECT Condes "
									."   FROM ".$wbasedato."_000042 a "
									." 	WHERE Concod = '".$row['Kadcnd']."' ";

							$rescnd = mysql_query($qCnd,$conex) or die (mysql_errno()." - ".mysql_error());
							$numcnd = mysql_num_rows($rescnd);
							if( $rowsCnd =  mysql_fetch_array($rescnd) ){
								$desCnd1 = $rowsCnd[ 'Condes' ];
							}

							$cantidad = "";
							if($row['Kaddia'] && trim($row['Kaddia'])!="" && $row['Kaddia']!='0'){
								$diasTto = $row['Kaddia'].' d&iacute;a(s)';
								$cantidad = ceil( $row['Kaddia']*24/$rowfre[ 'Perequ' ]*$row['Kadcfr']/$row[ 'Kadcma' ] );								
							}
							else{
								$diasTto = '';
							}
							
							if($row['Kaddma'] && trim($row['Kaddma'])!="" && $row['Kaddma']!='0'){
								$diasTto = ceil( $row['Kaddma']*$rowfre[ 'Perequ' ]/24 ).' d&iacute;a(s)';
								$cantidad = ceil( $row['Kaddma']*$row['Kadcfr']/$row[ 'Kadcma' ] );								
							}
							else{
								$diasTto = '';
							}
							
							if( !empty($row[ 'Kadcal' ]) && trim( $row[ 'Kadcal' ] )*1 > 0 ){
								$cantidad = $row[ 'Kadcal' ];
								$diasTto = ceil( $row[ 'Kadcal' ]/($row['Kadcfr']/$row[ 'Kadcma' ]*24/$rowfre[ 'Perequ' ]) ).' d&iacute;a(s';
							}
							
							//Si los dias de tratamiento estan vacios, busca si el articulo tiene ctc e imprime la duracion del medicamento, registrado por el medico.		
							if($diasTto == ""){
								if($row['Kadido'] != '0' and $row['Kadido'] != 'NaN'){
									$diasTto = traer_diastto_ctc($wbasedato, $row['Kadido']);
								}								
							}
							
							$medicamento = $row['Artgen'];
							$fecha_orden_med = $row['Kadfec'];
							$hora_orden_med = $row['Hora'];
							
							
							if($alt == 'on'){	
							
								$dato_cantidad = '<td>&nbsp;'.$cantidad.'</td>';
							
							}
							
							$codigo_medicamento = $row['Kadart'];

							
							$es_nutricion = es_nutricion($codigo_medicamento);
							
							if($es_nutricion == 'on'){
								
								// var_dump("hola1");
								
								$insumosNPT = consultarInsumosNPT($conex, $wemp_pmla, $wbasedato,$row['Kadhis'],$row['Kading'],$codigo_medicamento);
						
								$kadobs1="";
								if(count($insumosNPT)>0)
								{
									$observ="";
									
									for($t=0;$t<count($insumosNPT);$t++)
									{
										$observ .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
									}
									
									$canCaractObserv = strlen($observ);
									
									
									if($canCaractObserv >= $caracteresObservacionesN)
									{
										$kadobs1 = nl2br($observ)."...";
									}
									else
									{
										$kadobs1 = nl2br($observ);
									}
									
									if(trim($row['Kadobs'])!="" && strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) )!="")
									{
										$kadobs1 .= "<br><br><b>Observación:</b> ".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
									}
								}
								else
								{
									if(trim($row['Kadobs'])!="")
									{
										$observaciones=explode("<div",$row['Kadobs']);
							
										$kadobs1="";
										for($s=1;$s<count($observaciones);$s++)
										{
											$observacion="<div".$observaciones[$s];	
											
											$observ=substr(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ), 0 , $caracteresObservacionesN);
											$canCaractObserv = strlen($observ);
											
											// $kadobs1 .= "<br>".nl2br(substr(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
											
											if($canCaractObserv >= $caracteresObservacionesN)
											{
												$kadobs1 .= "<br>".nl2br($observ)."...";
											}
											else
											{
												$kadobs1 .= "<br>".nl2br($observ);
											}
											
											
										}
										// $kadobs1 = "<br>".nl2br(substr(strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
									
									}
								}
							}
							else
							{
								if(trim($row['Kadobs'])!="")
								{
									$kadobs1 = "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
								}
							}
							$cont++;
															
							
							$nombre_medicamento = "";
							
							if($es_nutricion == 'on'){
								$nombre_medicamento = '<td align="left">&nbsp;<b>'.$medicamento."</b>".$kadobs1.'</td>';
							}else{						
								$nombre_medicamento = '<td align="left">&nbsp;'.$medicamento.$kadobs1.'</td>';
							}
					
							$html .='	<tr align="center">
										'.$nombre_medicamento.'
										<td>&nbsp;'.$rowuni['Unides'].'</td>
										<td>&nbsp;'.$fecha_orden_med.'<br>'.$hora_orden_med.'</td>
										'.$dato_cantidad.'
										<td>&nbsp;'.$row['Kadcfr'].' '.$wfraccion.'</td>
										<td>&nbsp;'.$rowvia['Viades'].'</td>
										<td>&nbsp;'.$frecuencia.'</td>
										<td>&nbsp;'.$diasTto.'</td>
										<td>&nbsp;'.$desCnd1.'</td>
									</tr>';

								
														
							}
							
							
							$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom, $key_medico);
							
								$html .='</tbody>
									</table>';
							$html .="</div>";
														
							
							//Verifica si es paciente de EPS
							if($pacEps == 'on'){
							
								//Recorro lo articulos que son NoPos							
								foreach($medicamentos as $key1 => $row){
										
								if($row['Artpos'] == 'N'){
									
									$es_nutricion = "";
									// Se define el filtro para consultar el médico tratante
									if(isset($ide) && $ide!="")
										$filtroMedico = " Meddoc = '".$ide."' ";
									else
										$filtroMedico = " Meduma = '".$row['Kadusu']."' ";
									
									$kadobs2 = "";
									$desCnd2 = "";
									// Consulto los datos del médico
									$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
										."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
										." 	WHERE ".$filtroMedico." "
										."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
									$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
									$nummed = mysql_num_rows($resmed);
									$rowmed = mysql_fetch_array($resmed);

									// Se calculan las dimensiones para la imagen de la firma del médico
									dimensionesImagen($rowmed['Meddoc']);

									// Datos del médico tratante
									$medtdo = $rowmed['Medtdo'];
									$meddoc = $rowmed['Meddoc'];
									$medno1 = $rowmed['Medno1'];
									$medno2 = $rowmed['Medno2'];
									$medap1 = $rowmed['Medap1'];
									$medap2 = $rowmed['Medap2'];
									$medreg = $rowmed['Medreg'];
									$espnom = $rowmed['Espnom'];
									
									$html .="<div style='page-break-after: always;'>";									
									
									$html .= pintarEncabezado('ORDEN MEDICAMENTOS <br />Con CTC',$whistoria,'', $wingreso, $cconombre);
																		
											// Inicio tablas lista detalle						
											$html .='	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
															  <tbody>
																<tr>
																  <td style="height:17px;background-color:#EEEDED;">
																	&nbsp; <b>Servicios Solicitados</b>
																  </td>
																</tr>
																<tr>
																  <td  style="height:11px;">
																	&nbsp;
																  </td>
																</tr>
															  </tbody>
															</table>';

											// Inicio tabla con lista de medicamentos
											$html .='	<table rules="all" border="1" style="width: 740px;">
															  <tbody>
																<tr style="height:17px;" align="center">
																  <td style="width:70%;">
																	<b>&nbsp;Medicamento</b>
																  </td>
																  <td style="width:10%;">
																	<b>&nbsp;Presentacion</b>
																  </td>
																  <td style="width:10%;">
																	<b>&nbsp;Fecha y Hora</b>
																  </td>
																  '.$columna_cantidad.'
																  <td style="width:10%;">
																	<b>&nbsp;Dosis</b>
																  </td>
																  <td style="width:9%;">
																	<b>&nbsp;Via</b>
																  </td>
																  <td style="width:11%;">
																	<b>&nbsp;Frecuencia</b>
																  </td>
																  <td style="width:10%;">
																	<b>&nbsp;Duraci&oacute;n</b>
																  </td>
																   <td style="width:10%;">
																	<b>&nbsp;Condici&oacute;n</b>
																  </td>
																</tr>';					

										// // Consulto los datos de la unidad
										$q = " SELECT  Unides "
											."   FROM ".$wbasedato."_000027 "
											." 	WHERE  Unicod = '".$row['Kaduma']."' ";
										$resuni = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
										$numuni = mysql_num_rows($resuni);
										$rowuni = mysql_fetch_array($resuni);

										// // Consulto los datos de la presentacion
										$q = " SELECT  Ffanom "
											."   FROM ".$wbasedato."_000046 "
											." 	WHERE  Ffacod = '".$row['Kadffa']."' ";
										$resffa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
										$numffa = mysql_num_rows($resffa);
										$rowffa = mysql_fetch_array($resffa);
										
										//Fraccion 
										$q_fr = " SELECT  Unides "
											."   FROM ".$wbasedato."_000027 "
											." 	WHERE  Unicod = '".$row['Kadufr']."' ";
										$resfr = mysql_query($q_fr,$conex) or die (mysql_errno()." - ".mysql_error());							
										$rowfr = mysql_fetch_array($resfr);
										$fraccion = $rowfr['Unides'];
										$wfraccion =  ucfirst(strtolower($fraccion));
										
										// // Consulto los datos de la presentacion
										$q = " SELECT  Viades "
											."   FROM ".$wbasedato."_000040 "
											." 	WHERE  Viacod = '".$row['Kadvia']."' ";
										$resvia = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
										$numvia = mysql_num_rows($resvia);
										$rowvia = mysql_fetch_array($resvia);

										// Consulto los datos de la frecuencia de administracion
										$q = " SELECT  Percan, Peruni, Pertip, Perequ "
											."   FROM ".$wbasedato."_000043 "
											." 	WHERE  Percod = '".$row['Kadper']."' ";
										$resfre = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
										$numfre = mysql_num_rows($resfre);
										$rowfre = mysql_fetch_array($resfre);

										$frecuencia = 'Cada '.$rowfre['Percan'].' '.$rowfre['Peruni'];

										// Consulto el nombre de la familia del medicamento
										$qfam =  " SELECT Famnom, Relcon, Reluni "
												."   FROM ".$wbasedato."_000114 a, ".$wbasedato."_000115 b "
												." 	WHERE Relart = '".$row['Kadart']."' "
												."	  AND Relest = 'on' "
												."	  AND Relfam = Famcod  "
												."	  AND Famest = 'on' "
												."	ORDER BY b.id DESC";

										$resfam = mysql_query($qfam,$conex) or die (mysql_errno()." - ".mysql_error());
										$numfam = mysql_num_rows($resfam);
										
										
										// Consulto el nombre de la codición
										$qCnd =  " SELECT Condes "
												."   FROM ".$wbasedato."_000042 a "
												." 	WHERE Concod = '".$row['Kadcnd']."' ";

										$rescnd = mysql_query($qCnd,$conex) or die (mysql_errno()." - ".mysql_error());
										$numcnd = mysql_num_rows($rescnd);
										if( $rowsCnd =  mysql_fetch_array($rescnd) ){
											$desCnd2 = $rowsCnd[ 'Condes' ];
										}

										$cantidad = "";
										if($row['Kaddia'] && trim($row['Kaddia'])!="" && $row['Kaddia']!='0'){
											$diasTto = $row['Kaddia'].' d&iacute;a(s)';
											$cantidad = ceil( $row['Kaddia']*24/$rowfre[ 'Perequ' ]*$row['Kadcfr']/$row[ 'Kadcma' ] );
										}
										else{
											$diasTto = '';
										}
										
										if($row['Kaddma'] && trim($row['Kaddma'])!="" && $row['Kaddma']!='0'){
											$diasTto = ceil( $row['Kaddma']*$rowfre[ 'Perequ' ]/24 ).' d&iacute;a(s)';
											$cantidad = ceil( $row['Kaddma']*$row['Kadcfr']/$row[ 'Kadcma' ] );
										}
										else{
											$diasTto = '';
										}
										
										if( !empty($row[ 'Kadcal' ]) && trim( $row[ 'Kadcal' ] )*1 > 0 ){
											$cantidad = $row[ 'Kadcal' ];
											$diasTto = ceil( $row[ 'Kadcal' ]/($row['Kadcfr']/$row[ 'Kadcma' ]*24/$rowfre[ 'Perequ' ]) ).' d&iacute;a(s';
										}
										
										//Si los dias de tratamiento estan vacios, busca si el articulo tiene ctc e imprime la duracion del medicamento, registrado por el medico.	
										if($diasTto == ""){
											if($row['Kadido'] != '0' and $row['Kadido'] != 'NaN'){
												$diasTto = traer_diastto_ctc($wbasedato, $row['Kadido']);
											}
										}

										$medicamento = $row['Artgen'];
										$fecha_orden_med = $row['Kadfec'];
										$hora_orden_med = $row['Hora'];
										
										if($alt == 'on'){	
							
											$dato_cantidad = '<td>&nbsp;'.$cantidad.'</td>';
										
										}
										
										$codigo_medicamento = $row['Kadart'];

											
												
										$es_nutricion = es_nutricion($codigo_medicamento);
										
										if($es_nutricion == 'on'){
											
											// var_dump("hola2");
											
											$insumosNPT = consultarInsumosNPT($conex, $wemp_pmla, $wbasedato,$row['Kadhis'],$row['Kading'],$codigo_medicamento);
						
											// $kadobs2="";
											// for($t=0;$t<count($insumosNPT);$t++)
											// {
												// $kadobs2 .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
											// }
											
											$kadobs2="";
											if(count($insumosNPT)>0)
											{
												$observ="";
												for($t=0;$t<count($insumosNPT);$t++)
												{
													$observ .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
												}
												
												$canCaractObserv = strlen($observ);
												
												
												if($canCaractObserv >= $caracteresObservacionesN)
												{
													$kadobs2 = nl2br($observ)."...";
												}
												else
												{
													$kadobs2 = nl2br($observ);
												}
												
												if(trim($row['Kadobs'])!="" && strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) )!="")
												{
													$kadobs2 .= "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
												}
											}
											else
											{
												if(trim($row['Kadobs'])!="")
												{
													$observaciones=explode("<div",$row['Kadobs']);
							
													$kadobs2="";
													for($s=1;$s<count($observaciones);$s++)
													{
														// $observacion="<div".$observaciones[$s];
														// $kadobs2 .= "<br>".nl2br(substr(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
														
														// $observacion="<div".$observaciones[$s];	
											
														$observ=substr(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ), 0 , $caracteresObservacionesN);
														$canCaractObserv = strlen($observ);
														
														if($canCaractObserv >= $caracteresObservacionesN)
														{
															$kadobs2 .= "<br>".nl2br($observ)."...";
														}
														else
														{
															$kadobs2 .= "<br>".nl2br($observ);
														}
														
													}
													// $kadobs2 = "<br>".nl2br(substr(strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
												
												}
												
											}
										}
										else
										{
											if(trim($row['Kadobs'])!="")
											{
												$kadobs2 = "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
											}
										}
										$cont++;
												
										$nombre_medicamento = "";
										if($es_nutricion == 'on'){
											$nombre_medicamento = '<td align="left">&nbsp;<b>'.$medicamento."</b>".$kadobs2.'</td>';
										}else{						
											$nombre_medicamento = '<td align="left">&nbsp;'.$medicamento.$kadobs2.'</td>';
										}
										
										$html .='<tr align="center">
											  '.$nombre_medicamento.'
											  <td>&nbsp;'.$rowuni['Unides'].'</td>
											  <td>&nbsp;'.$fecha_orden_med.'<br>'.$hora_orden_med.'</td>
											  '.$dato_cantidad.'
											  <td>&nbsp;'.$row['Kadcfr'].' '.$wfraccion.'</td>
											  <td>&nbsp;'.$rowvia['Viades'].'</td>
											  <td>&nbsp;'.$frecuencia.'</td>
											  <td>&nbsp;'.$diasTto.'</td>
											  <td>&nbsp;'.$desCnd2.'</td>
											  </tr>';					

										
										
										// if(trim($desCnd)!="")
										// {
											// $html .='<tr><td colspan="7" height="17" style="vertical-align:top"> &nbsp; &nbsp; Condici&oacute;n: '.$desCnd.'</td></tr>';
											// $cont++;
										// }

										$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom,$row['Kadusu']);

											$html .='</tbody>
												</table>';
										
										$html .="</div>";										
									
									}
							
								}
							
							}
							
						}else{
						
						//Recorro los medicos que han firmado.
							foreach($medicamentos as $key1 => $row){
							$es_nutricion = "";	
								// Se define el filtro para consultar el médico tratante
							if(isset($ide) && $ide!="")
								$filtroMedico = " Meddoc = '".$ide."' ";
							else
								$filtroMedico = " Meduma = '".$row['Kadusu']."' ";
							
							$kadobs3 = "";
							$desCnd3 = "";
							
							// Consulto los datos del médico
							$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
								."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
								." 	WHERE ".$filtroMedico." "
								."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
							$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
							$nummed = mysql_num_rows($resmed);
							$rowmed = mysql_fetch_array($resmed);

							// Se calculan las dimensiones para la imagen de la firma del médico
							dimensionesImagen($rowmed['Meddoc']);

							// Datos del médico tratante
							$medtdo = $rowmed['Medtdo'];
							$meddoc = $rowmed['Meddoc'];
							$medno1 = $rowmed['Medno1'];
							$medno2 = $rowmed['Medno2'];
							$medap1 = $rowmed['Medap1'];
							$medap2 = $rowmed['Medap2'];
							$medreg = $rowmed['Medreg'];
							$espnom = $rowmed['Espnom'];
							
							$subEncabezado = "";
							
							$html .="<div style='page-break-after: always;'>";
							
							$html .= pintarEncabezado('ORDEN MEDICAMENTOS'.$subEncabezado,$whistoria,'', $wingreso, $cconombre);
																
									// Inicio tablas lista detalle						
									$html .='	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
													  <tbody>
														<tr>
														  <td style="height:17px;background-color:#EEEDED;">
															&nbsp; <b>Servicios Solicitados</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
													  </tbody>
													</table>';

									// Inicio tabla con lista de medicamentos
									$html .='	<table rules="all" border="1" style="width: 740px;">
													  <tbody>
														<tr style="height:17px;" align="center">
														  <td style="width:70%;">
															<b>&nbsp;Medicamento</b>
														  </td>
														  <td style="width:10%;">
															<b>&nbsp;Presentacion</b>
														  </td>
														  <td style="width:10%;">
															<b>&nbsp;Fecha y Hora</b>
														  </td>
														  '.$columna_cantidad.'
														  <td style="width:10%;">
															<b>&nbsp;Dosis</b>
														  </td>
														  <td style="width:9%;">
															<b>&nbsp;Via</b>
														  </td>
														  <td style="width:11%;">
															<b>&nbsp;Frecuencia</b>
														  </td>
														  <td style="width:10%;">
															<b>&nbsp;Duraci&oacute;n</b>
														  </td>
														   <td style="width:10%;">
															<b>&nbsp;Condici&oacute;n</b>
														  </td>
														</tr>';					

								
								
								// // Consulto los datos de la unidad
								$q = " SELECT  Unides "
									."   FROM ".$wbasedato."_000027 "
									." 	WHERE  Unicod = '".$row['Kaduma']."' ";
								$resuni = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$numuni = mysql_num_rows($resuni);
								$rowuni = mysql_fetch_array($resuni);
								
								
								
								//Fraccion 
								$q_fr = " SELECT  Unides "
									."   FROM ".$wbasedato."_000027 "
									." 	WHERE  Unicod = '".$row['Kadufr']."' ";
								$resfr = mysql_query($q_fr,$conex) or die (mysql_errno()." - ".mysql_error());							
								$rowfr = mysql_fetch_array($resfr);
								$fraccion = $rowfr['Unides'];
								$wfraccion =  ucfirst(strtolower($fraccion));
								
								// // Consulto los datos de la presentacion
								$q = " SELECT  Ffanom "
									."   FROM ".$wbasedato."_000046 "
									." 	WHERE  Ffacod = '".$row['Kadffa']."' ";
								$resffa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$numffa = mysql_num_rows($resffa);
								$rowffa = mysql_fetch_array($resffa);

								// // Consulto los datos de la presentacion
								$q = " SELECT  Viades "
									."   FROM ".$wbasedato."_000040 "
									." 	WHERE  Viacod = '".$row['Kadvia']."' ";
								$resvia = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$numvia = mysql_num_rows($resvia);
								$rowvia = mysql_fetch_array($resvia);

								// Consulto los datos de la frecuencia de administracion
								$q = " SELECT  Percan, Peruni, Pertip, Perequ "
									."   FROM ".$wbasedato."_000043 "
									." 	WHERE  Percod = '".$row['Kadper']."' ";
								$resfre = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
								$numfre = mysql_num_rows($resfre);
								$rowfre = mysql_fetch_array($resfre);

								$frecuencia = 'Cada '.$rowfre['Percan'].' '.$rowfre['Peruni'];


								// Consulto el nombre de la familia del medicamento
								$qfam =  " SELECT Famnom, Relcon, Reluni "
										."   FROM ".$wbasedato."_000114 a, ".$wbasedato."_000115 b "
										." 	WHERE Relart = '".$row['Kadart']."' "
										."	  AND Relest = 'on' "
										."	  AND Relfam = Famcod  "
										."	  AND Famest = 'on' "
										."	ORDER BY b.id DESC";

								$resfam = mysql_query($qfam,$conex) or die (mysql_errno()." - ".mysql_error());
								$numfam = mysql_num_rows($resfam);
								
								
								// Consulto el nombre de la codición
								$qCnd =  " SELECT Condes "
										."   FROM ".$wbasedato."_000042 a "
										." 	WHERE Concod = '".$row['Kadcnd']."' ";

								$rescnd = mysql_query($qCnd,$conex) or die (mysql_errno()." - ".mysql_error());
								$numcnd = mysql_num_rows($rescnd);
								if( $rowsCnd =  mysql_fetch_array($rescnd) ){
									$desCnd3 = $rowsCnd[ 'Condes' ];
								}

								$cantidad = "";
								if($row['Kaddia'] && trim($row['Kaddia'])!="" && $row['Kaddia']!='0'){
									$diasTto = $row['Kaddia'].' d&iacute;a(s)';
									$cantidad = ceil( $row['Kaddia']*24/$rowfre[ 'Perequ' ]*$row['Kadcfr']/$row[ 'Kadcma' ] );
								}
								else{
									$diasTto = '';
								}
								
								if($row['Kaddma'] && trim($row['Kaddma'])!="" && $row['Kaddma']!='0'){
									$diasTto = ceil( $row['Kaddma']*$rowfre[ 'Perequ' ]/24 ).' d&iacute;a(s)';
									$cantidad = ceil( $row['Kaddma']*$row['Kadcfr']/$row[ 'Kadcma' ] );
								}
								else{
									$diasTto = '';
								}
								
								if( !empty($row[ 'Kadcal' ]) && trim( $row[ 'Kadcal' ] )*1 > 0 ){
									$cantidad = $row[ 'Kadcal' ];
									$diasTto = ceil( $row[ 'Kadcal' ]/($row['Kadcfr']/$row[ 'Kadcma' ]*24/$rowfre[ 'Perequ' ]) ).' d&iacute;a(s)';
								}

								//Si los dias de tratamiento estan vacios, busca si el articulo tiene ctc e imprime la duracion del medicamento, registrado por el medico.	
								if($diasTto == ""){								
									if($row['Kadido'] != '0' and $row['Kadido'] != 'NaN'){
										$diasTto = traer_diastto_ctc($wbasedato, $row['Kadido']);
									}									
								}
								$unidad="";
								if(count($reemplazarMedCTC)>0)
								{
									$medicamento = $reemplazarMedCTC['Artgen'];
									$unidad = $reemplazarMedCTC['Unides'];
								}
								else
								{
									$medicamento = $row['Artgen'];
									$unidad = $rowuni['Unides'];
								}
								
								// $medicamento = $row['Artgen'];
								$hora_orden_med = $row['Hora'];
								$fecha_orden_med = $row['Kadfec'];
								
								if($alt == 'on'){	
							
									$dato_cantidad = '<td>&nbsp;'.$cantidad.'</td>';
								
								}		
								
								
								$codigo_medicamento = $row['Kadart'];

									
										
								$es_nutricion = es_nutricion($codigo_medicamento);
								
								if($es_nutricion == 'on'){
									
									// var_dump("hola3");
									
									$insumosNPT = consultarInsumosNPT($conex, $wemp_pmla, $wbasedato,$row['Kadhis'],$row['Kading'],$codigo_medicamento);
						
									// $kadobs3="";
									// for($t=0;$t<count($insumosNPT);$t++)
									// {
										// $kadobs3 .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
									// }
									
									$kadobs3="";
									if(count($insumosNPT)>0)
									{
										$observ="";
										for($t=0;$t<count($insumosNPT);$t++)
										{
											$observ .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
										}
										
										$canCaractObserv = strlen($observ);
										
										
										if($canCaractObserv >= $caracteresObservacionesN)
										{
											$kadobs3 = nl2br($observ)."...";
										}
										else
										{
											$kadobs3 = nl2br($observ);
										}
										
										if(trim($row['Kadobs'])!="" && strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) )!="")
										{
											$kadobs3 .= "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
										}
									}
									else
									{
										if(trim($row['Kadobs'])!="")
										{
											$observaciones=explode("<div",$row['Kadobs']);
						
											$kadobs3="";
											for($s=1;$s<count($observaciones);$s++)
											{
												// $observacion="<div".$observaciones[$s];
												// $kadobs3 .= "<br>".nl2br(substr(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
												
												$observ=substr(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ), 0 , $caracteresObservacionesN);
												$canCaractObserv = strlen($observ);
												
												if($canCaractObserv >= $caracteresObservacionesN)
												{
													$kadobs3 .= "<br>".nl2br($observ)."...";
												}
												else
												{
													$kadobs3 .= "<br>".nl2br($observ);
												}
											}
											// $kadobs3 = "<br>".nl2br(substr(strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
										
										}
										
									}
								}
								else
								{
									if(trim($row['Kadobs'])!="")
									{
										$kadobs3 = "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
									}
								}
								$cont++;
										
								$nombre_medicamento = "";
								if($es_nutricion == 'on'){
									$nombre_medicamento = '<td align="left">&nbsp;<b>'.$medicamento."</b>".$kadobs3.'</td>';
								}else{						
									$nombre_medicamento = '<td align="left">&nbsp;'.$medicamento.$kadobs3.'</td>';
								}
								
								$html .='	<tr align="center">
										  '.$nombre_medicamento.' 
										  <td>&nbsp;'.$unidad.'</td>
										  <td>&nbsp;'.$fecha_orden_med.'<br>'.$hora_orden_med.'</td>
										  '.$dato_cantidad.'
										  <td>&nbsp;'.$row['Kadcfr'].' '.$wfraccion.'</td>
										  <td>&nbsp;'.$rowvia['Viades'].'</td>
										  <td>&nbsp;'.$frecuencia.'</td>
										  <td>&nbsp;'.$diasTto.'</td>
										  <td>&nbsp;'.$desCnd3.'</td>
										</tr>';	

								// $html .='	<tr align="center">
										  // '.$nombre_medicamento.' 
										  // <td>&nbsp;'.$rowuni['Unides'].'</td>
										  // <td>&nbsp;'.$fecha_orden_med.'<br>'.$hora_orden_med.'</td>
										  // '.$dato_cantidad.'
										  // <td>&nbsp;'.$row['Kadcfr'].' '.$wfraccion.'</td>
										  // <td>&nbsp;'.$rowvia['Viades'].'</td>
										  // <td>&nbsp;'.$frecuencia.'</td>
										  // <td>&nbsp;'.$diasTto.'</td>
										  // <td>&nbsp;'.$desCnd3.'</td>
										// </tr>';					
								
								$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom,$row['Kadusu']);

									$html .='</tbody>
										</table>';
								
								$html .="</div>";
								
								
							
								}
								
								//Validar si el paciente es de EPS, si es asi imprime CTC.							
								if($pacEps == 'on'){
							
									//Recorro los medicos que han firmado y que son NoPos.
									foreach($medicamentos as $key_pos => $row){
										
										if($row['Artpos'] == 'N'){
											$es_nutricion = "";
											// Se define el filtro para consultar el médico tratante
											if(isset($ide) && $ide!="")
												$filtroMedico = " Meddoc = '".$ide."' ";
											else
												$filtroMedico = " Meduma = '".$row['Kadusu']."' ";
											
											$kadobs4 = "";
											$desCnd4 = "";
											
											// Consulto los datos del médico
											$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
												."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
												." 	WHERE ".$filtroMedico." "
												."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
											$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
											$nummed = mysql_num_rows($resmed);
											$rowmed = mysql_fetch_array($resmed);

											// Se calculan las dimensiones para la imagen de la firma del médico
											dimensionesImagen($rowmed['Meddoc']);

											// Datos del médico tratante
											$medtdo = $rowmed['Medtdo'];
											$meddoc = $rowmed['Meddoc'];
											$medno1 = $rowmed['Medno1'];
											$medno2 = $rowmed['Medno2'];
											$medap1 = $rowmed['Medap1'];
											$medap2 = $rowmed['Medap2'];
											$medreg = $rowmed['Medreg'];
											$espnom = $rowmed['Espnom'];
											
											$html .="<div style='page-break-after: always;'>";
											
											$html .= pintarEncabezado('ORDEN MEDICAMENTOS <br />Con CTC',$whistoria,'', $wingreso, $cconombre);
																				
													// Inicio tablas lista detalle						
													$html .='	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
																	  <tbody>
																		<tr>
																		  <td style="height:17px;background-color:#EEEDED;">
																			&nbsp; <b>Servicios Solicitados</b>
																		  </td>
																		</tr>
																		<tr>
																		  <td  style="height:11px;">
																			&nbsp;
																		  </td>
																		</tr>
																	  </tbody>
																	</table>';

													// Inicio tabla con lista de medicamentos
													$html .='	<table rules="all" border="1" style="width: 740px;">
																	  <tbody>
																		<tr style="height:17px;" align="center">
																		  <td style="width:70%;">
																			<b>&nbsp;Medicamento</b>
																		  </td>
																		  <td style="width:10%;">
																			<b>&nbsp;Presentacion</b>
																		  </td>
																		  <td style="width:10%;">
																			<b>&nbsp;Fecha y Hora</b>
																		  </td>
																		  <td style="width:9%;">
																			<b>&nbsp;Cantidad</b>
																		  </td>
																		  <td style="width:10%;">
																			<b>&nbsp;Dosis</b>
																		  </td>
																		  '.$columna_cantidad.'
																		  <td style="width:11%;">
																			<b>&nbsp;Frecuencia</b>
																		  </td>
																		  <td style="width:10%;">
																			<b>&nbsp;Duraci&oacute;n</b>
																		  </td>
																		   <td style="width:10%;">
																			<b>&nbsp;Condici&oacute;n</b>
																		  </td>
																		</tr>';					

												// // Consulto los datos de la unidad
												$q = " SELECT  Unides "
													."   FROM ".$wbasedato."_000027 "
													." 	WHERE  Unicod = '".$row['Kaduma']."' ";
												$resuni = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
												$numuni = mysql_num_rows($resuni);
												$rowuni = mysql_fetch_array($resuni);

												// // Consulto los datos de la presentacion
												$q = " SELECT  Ffanom "
													."   FROM ".$wbasedato."_000046 "
													." 	WHERE  Ffacod = '".$row['Kadffa']."' ";
												$resffa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
												$numffa = mysql_num_rows($resffa);
												$rowffa = mysql_fetch_array($resffa);
												
												//Fraccion 
												$q_fr = " SELECT  Unides "
													."   FROM ".$wbasedato."_000027 "
													." 	WHERE  Unicod = '".$row['Kadufr']."' ";
												$resfr = mysql_query($q_fr,$conex) or die (mysql_errno()." - ".mysql_error());							
												$rowfr = mysql_fetch_array($resfr);
												$fraccion = $rowfr['Unides'];
												$wfraccion =  ucfirst(strtolower($fraccion));
												
												// // Consulto los datos de la presentacion
												$q = " SELECT  Viades "
													."   FROM ".$wbasedato."_000040 "
													." 	WHERE  Viacod = '".$row['Kadvia']."' ";
												$resvia = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
												$numvia = mysql_num_rows($resvia);
												$rowvia = mysql_fetch_array($resvia);

												// Consulto los datos de la frecuencia de administracion
												$q = " SELECT  Percan, Peruni, Pertip, Perequ "
													."   FROM ".$wbasedato."_000043 "
													." 	WHERE  Percod = '".$row['Kadper']."' ";
												$resfre = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
												$numfre = mysql_num_rows($resfre);
												$rowfre = mysql_fetch_array($resfre);

												$frecuencia = 'Cada '.$rowfre['Percan'].' '.$rowfre['Peruni'];

												// Consulto el nombre de la familia del medicamento
												$qfam =  " SELECT Famnom, Relcon, Reluni "
														."   FROM ".$wbasedato."_000114 a, ".$wbasedato."_000115 b "
														." 	WHERE Relart = '".$row['Kadart']."' "
														."	  AND Relest = 'on' "
														."	  AND Relfam = Famcod  "
														."	  AND Famest = 'on' "
														."	ORDER BY b.id DESC";

												$resfam = mysql_query($qfam,$conex) or die (mysql_errno()." - ".mysql_error());
												$numfam = mysql_num_rows($resfam);
												
												
												// Consulto el nombre de la codición
												$qCnd =  " SELECT Condes "
														."   FROM ".$wbasedato."_000042 a "
														." 	WHERE Concod = '".$row['Kadcnd']."' ";

												$rescnd = mysql_query($qCnd,$conex) or die (mysql_errno()." - ".mysql_error());
												$numcnd = mysql_num_rows($rescnd);
												if( $rowsCnd =  mysql_fetch_array($rescnd) ){
													$desCnd4 = $rowsCnd[ 'Condes' ];
												}

												$cantidad = "";
												if($row['Kaddia'] && trim($row['Kaddia'])!="" && $row['Kaddia']!='0'){
													$diasTto = $row['Kaddia'].' d&iacute;a(s)';
													$cantidad = ceil( $row['Kaddia']*24/$rowfre[ 'Perequ' ]*$row['Kadcfr']/$row[ 'Kadcma' ] );
												}
												else{
													$diasTto = '';
												}
												
												if($row['Kaddma'] && trim($row['Kaddma'])!="" && $row['Kaddma']!='0'){
													$diasTto = ceil( $row['Kaddma']*$rowfre[ 'Perequ' ]/24 ).' d&iacute;a(s)';
													$cantidad = ceil( $row['Kaddma']*$row['Kadcfr']/$row[ 'Kadcma' ] );
												}
												else{
													$diasTto = '';
												}
												
												if( !empty($row[ 'Kadcal' ]) && trim( $row[ 'Kadcal' ] )*1 > 0 ){
													$cantidad = $row[ 'Kadcal' ];
													$diasTto = ceil( $row[ 'Kadcal' ]/($row['Kadcfr']/$row[ 'Kadcma' ]*24/$rowfre[ 'Perequ' ]) ).' d&iacute;a(s)';
												}

												//Si los dias de tratamiento estan vacios, busca si el articulo tiene ctc e imprime la duracion del medicamento, registrado por el medico.	
												if($diasTto == ""){
													if($row['Kadido'] != '0' and $row['Kadido'] != 'NaN'){
														$diasTto = traer_diastto_ctc($wbasedato, $row['Kadido']);
													}
												}
												
												$medicamento = $row['Artgen'];
												$fecha_orden_med = $row['Kadfec'];
												$hora_orden_med = $row['Hora'];
												
												if($alt == 'on'){	
							
													$dato_cantidad = '<td>&nbsp;'.$cantidad.'</td>';
												
												}
												
												$codigo_medicamento = $row['Kadart'];

													
														
												$es_nutricion = es_nutricion($codigo_medicamento);
												
												if($es_nutricion == 'on'){
													
													// var_dump("hola4");
													
													$insumosNPT = consultarInsumosNPT($conex, $wemp_pmla, $wbasedato,$row['Kadhis'],$row['Kading'],$codigo_medicamento);
						
													// $kadobs4="";
													// for($t=0;$t<count($insumosNPT);$t++)
													// {
														// $kadobs4 .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
													// }
													
													$kadobs4="";
													if(count($insumosNPT)>0)
													{
														$observ="";
														for($t=0;$t<count($insumosNPT);$t++)
														{
															$observ .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
														}
														
														$canCaractObserv = strlen($observ);
														
														
														if($canCaractObserv >= $caracteresObservacionesN)
														{
															$kadobs4 = nl2br($observ)."...";
														}
														else
														{
															$kadobs4 = nl2br($observ);
														}
														
														if(trim($row['Kadobs'])!="" && strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) )!="")
														{
															$kadobs4 .= "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
														}
													}
													else
													{
														if(trim($row['Kadobs'])!="")
														{
															$observaciones=explode("<div",$row['Kadobs']);
													
															$kadobs4="";
															for($s=1;$s<count($observaciones);$s++)
															{
																// $observacion="<div".$observaciones[$s];
																// $kadobs4 .= "<br>".nl2br(substr(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
																
																$observ=substr(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ), 0 , $caracteresObservacionesN);
																$canCaractObserv = strlen($observ);
																
																if($canCaractObserv >= $caracteresObservacionesN)
																{
																	$kadobs4 .= "<br>".nl2br($observ)."...";
																}
																else
																{
																	$kadobs4 .= "<br>".nl2br($observ);
																}
															}
															// $kadobs4 = "<br>".nl2br(substr(strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
														
														}
													}
												}
												else
												{
													if(trim($row['Kadobs'])!="")
													{
														$kadobs4 = "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
													}
												}
												$cont++;
													
												$nombre_medicamento = "";
												if($es_nutricion == 'on'){
													$nombre_medicamento = '<td align="left">&nbsp;<b>'.$medicamento."</b>".$kadobs4.'</td>';
												}else{						
													$nombre_medicamento = '<td align="left">&nbsp;'.$medicamento.$kadobs4.'</td>';
												}
												
												
												$html .='	<tr align="center">
																  '.$nombre_medicamento.'
																  <td>&nbsp;'.$rowuni['Unides'].'</td>
																  <td>&nbsp;'.$fecha_orden_med.'<br>'.$hora_orden_med.'</td>
																  '.$dato_cantidad.'
																  <td>&nbsp;'.$row['Kadcfr'].' '.$wfraccion.'</td>
																  <td>&nbsp;'.$rowvia['Viades'].'</td>
																  <td>&nbsp;'.$frecuencia.'</td>
																  <td>&nbsp;'.$diasTto.'</td>
																  <td>&nbsp;'.$desCnd4.'</td>
																</tr>';													

												$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom,$row['Kadusu']);

													$html .='</tbody>
														</table>';
												
												$html .="</div>";
										
											
											}
									
										}								
								}
							}							
					}
			 }		
		}
	}
	
	
	//***********************IMPRESION DE MEDICAMENTOS PARA FACTURACION *********************/
	// echo "<pre>";
	// print_r($array_medicamentos);
	// echo "</pre>";
	
	if(count($array_medicamentos) > 0 and $wtodos_ordenes == 'on' and $origen == 'on'){
				
				
				foreach($array_medicamentos as $key => $medicamentos){
				
				
				$html .="<div style='page-break-after: always;'>";
				
				$html .= pintarEncabezado('ORDEN MEDICAMENTOS'.$subEncabezado,$whistoria,'', $wingreso, $cconombre);
								
						// Inicio tablas lista detalle						
						$html .='	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
										  <tbody>
											<tr>
											  <td style="height:17px;background-color:#EEEDED;">
												&nbsp; <b>Servicios Solicitados</b>
											  </td>
											</tr>
											<tr>
											  <td  style="height:11px;">
												&nbsp;
											  </td>
											</tr>
										  </tbody>
										</table>';

						// Inicio tabla con lista de medicamentos
						$html .='	<table rules="all" border="1" style="width: 740px;">
										  <tbody>
											<tr style="height:17px;" align="center">
											  <td style="width:70%;">
												<b>&nbsp;Medicamento</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;Presentacion</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;Fecha y Hora</b>
											  </td>
											  '.$columna_cantidad.'
											  <td style="width:10%;">
												<b>&nbsp;Dosis</b>
											  </td>
											  <td style="width:9%;">
												<b>&nbsp;Via</b>
											  </td>
											  <td style="width:11%;">
												<b>&nbsp;Frecuencia</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;M&eacute;dico</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;Firma</b>
											  </td>
											</tr>';							
				
			
			foreach($medicamentos as $datos => $row){
					
				$kadobs1 = "";
				$desCnd1 = "";
				
				$key_medico = $row['Kadusu'];
				$es_nutricion = "";
				$filtroMedico = " Meduma = '".$key_medico."' ";

				// Consulto los datos del médico
				$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
						."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
						." 	WHERE ".$filtroMedico." "
						."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
				$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
				$nummed = mysql_num_rows($resmed);
				$rowmed = mysql_fetch_array($resmed);

				// Se calculan las dimensiones para la imagen de la firma del médico
				dimensionesImagen($rowmed['Meddoc']);

				// Datos del médico tratante
				$medtdo = $rowmed['Medtdo'];
				$meddoc = $rowmed['Meddoc'];
				$medno1 = $rowmed['Medno1'];
				$medno2 = $rowmed['Medno2'];
				$medap1 = $rowmed['Medap1'];
				$medap2 = $rowmed['Medap2'];
				$medreg = $rowmed['Medreg'];
				$espnom = $rowmed['Espnom'];
				
				$nombre_medico = $medno1." ".$medno2." ".$medap1." ".$medap2;
				
					
					// // Consulto los datos de la unidad
					$q = " SELECT  Unides "
						."   FROM ".$wbasedato."_000027 "
						." 	WHERE  Unicod = '".$row['Kaduma']."' ";
					$resuni = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$numuni = mysql_num_rows($resuni);
					$rowuni = mysql_fetch_array($resuni);
					
					//Fraccion 
					$q_fr = " SELECT  Unides "
						."   FROM ".$wbasedato."_000027 "
						." 	WHERE  Unicod = '".$row['Kadufr']."' ";
					$resfr = mysql_query($q_fr,$conex) or die (mysql_errno()." - ".mysql_error());							
					$rowfr = mysql_fetch_array($resfr);
					$fraccion = $rowfr['Unides'];
					$wfraccion =  ucfirst(strtolower($fraccion));
					
					// // Consulto los datos de la presentacion
					$q = " SELECT  Ffanom "
						."   FROM ".$wbasedato."_000046 "
						." 	WHERE  Ffacod = '".$row['Kadffa']."' ";
					$resffa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$numffa = mysql_num_rows($resffa);
					$rowffa = mysql_fetch_array($resffa);

					// // Consulto los datos de la presentacion
					$q = " SELECT  Viades "
						."   FROM ".$wbasedato."_000040 "
						." 	WHERE  Viacod = '".$row['Kadvia']."' ";
					$resvia = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$numvia = mysql_num_rows($resvia);
					$rowvia = mysql_fetch_array($resvia);

					// Consulto los datos de la frecuencia de administracion
					$q = " SELECT  Percan, Peruni, Pertip, Perequ "
						."   FROM ".$wbasedato."_000043 "
						." 	WHERE  Percod = '".$row['Kadper']."' ";
					$resfre = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$numfre = mysql_num_rows($resfre);
					$rowfre = mysql_fetch_array($resfre);

					$frecuencia = 'Cada '.$rowfre['Percan'].' '.$rowfre['Peruni'];
				
					// Consulto el nombre de la familia del medicamento
					$qfam =  " SELECT Famnom, Relcon, Reluni "
							."   FROM ".$wbasedato."_000114 a, ".$wbasedato."_000115 b "
							." 	WHERE Relart = '".$row['Kadart']."' "
							."	  AND Relest = 'on' "
							."	  AND Relfam = Famcod  "
							."	  AND Famest = 'on' "
							."	ORDER BY b.id DESC";

					$resfam = mysql_query($qfam,$conex) or die (mysql_errno()." - ".mysql_error());
					$numfam = mysql_num_rows($resfam);
										

					$cantidad = "";
					if($row['Kaddia'] && trim($row['Kaddia'])!="" && $row['Kaddia']!='0'){
						$diasTto = $row['Kaddia'].' d&iacute;a(s)';
						$cantidad = ceil( $row['Kaddia']*24/$rowfre[ 'Perequ' ]*$row['Kadcfr']/$row[ 'Kadcma' ] );								
					}
					else{
						$diasTto = '';
					}
					
					if($row['Kaddma'] && trim($row['Kaddma'])!="" && $row['Kaddma']!='0'){
						$diasTto = ceil( $row['Kaddma']*$rowfre[ 'Perequ' ]/24 ).' d&iacute;a(s)';
						$cantidad = ceil( $row['Kaddma']*$row['Kadcfr']/$row[ 'Kadcma' ] );								
					}
					else{
						$diasTto = '';
					}
					
					if( !empty($row[ 'Kadcal' ]) && trim( $row[ 'Kadcal' ] )*1 > 0 ){
						$cantidad = $row[ 'Kadcal' ];
						$diasTto = ceil( $row[ 'Kadcal' ]/($row['Kadcfr']/$row[ 'Kadcma' ]*24/$rowfre[ 'Perequ' ]) ).' d&iacute;a(s';
					}
					
					//Si los dias de tratamiento estan vacios, busca si el articulo tiene ctc e imprime la duracion del medicamento, registrado por el medico.		
					if($diasTto == ""){
						if($row['Kadido'] != '0' and $row['Kadido'] != 'NaN'){
							$diasTto = traer_diastto_ctc($wbasedato, $row['Kadido']);
						}								
					}
					
					$medicamento = $row['Artgen'];
					$fecha_orden_med = $row['Kadfec'];
					$hora_orden_med = $row['Hora'];
					
					
					if($alt == 'on'){	
					
						$dato_cantidad = '<td>&nbsp;'.$cantidad.'</td>';
					
					}
					
					$codigo_medicamento = $row['Kadart'];

						
					$es_nutricion = es_nutricion($codigo_medicamento);
					
					if($es_nutricion == 'on'){
						
						// var_dump("hola5");
						
						$insumosNPT = consultarInsumosNPT($conex, $wemp_pmla, $wbasedato,$row['Kadhis'],$row['Kading'],$codigo_medicamento);
						
						// $kadobs1="";
						// for($t=0;$t<count($insumosNPT);$t++)
						// {
							// $kadobs1 .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
						// }
						
						$kadobs1="";
						if(count($insumosNPT)>0)
						{
							for($t=0;$t<count($insumosNPT);$t++)
							{
								$kadobs1 .= "<br>".$insumosNPT[$t]['nombreComercial']." - ".$insumosNPT[$t]['prescripcion'];
							}
							
							if(trim($row['Kadobs'])!="" && strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) ) !="")
							{
								$kadobs1 .= "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
							}
						}
						else
						{
							if(trim($row['Kadobs'])!="")
							{
								$observaciones=explode("<div",$row['Kadobs']);
							
								for($s=1;$s<count($observaciones);$s++)
								{
									$observacion="<div".$observaciones[$s];
									$kadobs1 .= "<br>".nl2br(strip_tags( substr( $observacion, 0, strpos($observacion, "<span" ) ) ));
								}
							}
							
						}
						
						// // $kadobs1 = "<br>".nl2br(substr(strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) ), 0 , $caracteresObservacionesN))."...";
						// // $kadobs1 = "<br>".nl2br(strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) ));
					
					}
					else
					{
						// if(trim($row['Kadobs'])!="")
						if(trim($row['Kadobs'])!="")
						{
							$kadobs1 = "<br><br><b>Observación:</b>".strip_tags( substr( $row['Kadobs'], 0, strpos($row['Kadobs'], "<span" ) ) );
						}
					}
					$cont++;
						
											
					
					if(file_exists('../../images/medical/hce/Firmas/'.$key_medico.'.png'))
						{
							if(isset($wtodos_ordenes)){	
							$firma = '				<img src="../../../images/medical/hce/Firmas/'.$key_medico.'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';
							}else{
								
								$firma = '				<img src="../../images/medical/hce/Firmas/'.$key_medico.'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';	
							}
						}	
						else
						{
							$firma = '&nbsp;';
						}
					
					if($es_nutricion == 'on'){
						$nombre_medicamento = '<td align="left">&nbsp;<b>'.$medicamento."</b>".$kadobs1.'</td>';
					}else{						
						$nombre_medicamento = '<td align="left">&nbsp;'.$medicamento.$kadobs1.'</td>';
					}
					
					$html .='	<tr align="center">
								'.$nombre_medicamento.'
								<td>&nbsp;'.$rowuni['Unides'].'</td>
								<td>&nbsp;'.$fecha_orden_med.'<br>'.$hora_orden_med.'</td>
								'.$dato_cantidad.'
								<td>&nbsp;'.$row['Kadcfr'].' '.$wfraccion.'</td>
								<td>&nbsp;'.$rowvia['Viades'].'</td>
								<td>&nbsp;'.$frecuencia.'</td>
								<td>&nbsp;'.$nombre_medico.'</td>
								<td>'.$firma.'</td>
							</tr>';					
					
					
					
				}
														
						$html .='</tbody>
							</table>';
						$html .='<br><table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
								  <tbody>
									<tr>
									  <td style="text-align:center;" calss="descripcion">
										<b>- Firmado electrónicamente -</b>
									  </td>
									</tr>
								  </tbody>
								</table>';
					$html .="</div>";	
			}		
	
	}
	
	//----
	
	
	
		/******************* FIN IMPRESIÓN MEDICAMENTOS EXTERNOS *******************/

		// *****************************************************************
		// *********************** IMPRESIÓN EXAMENES ***********************
		// ******************************************************************/
		
		
	if( !( $mostrarSoloCTCPro || $mostrarSoloCTCArt ) )
	if($mostrarSoloProcedimientos || (!$mostrarSoloArticulos && !$mostrarSoloCTC))
	{
		
		$subEncabezado = "";
		
		//Se valida si la impresion del CTc es desde el programa de ctc de procedimientos no POS, si es asi entonces no tiene que tener en cuenta el filtro
		//de impresion, osea Detimp=on.
		if($pro != ''){
		
			$detimp = "";
		}else{
		
			$detimp = "AND Detimp = 'on'";
		}
		
		//La variable $wtodos_ordenes viene del programa impresion de ordenes a pacientes egresados, si esta en on imprimira todas las ordenes del paciente.
		if($wtodos_ordenes != 'on'){		
		
		$q = "  SELECT
					Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Tiprju,Tipfrm,Codcups as Codigo_cup, NoPos, {$wbasedatohce}_000028.Fecha_data as fecha_orden, {$wbasedatohce}_000028.Hora_data as hora_orden
				FROM 
					{$wbasedatohce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$wbasedatohce}_000028, {$wbasedatohce}_000047 c, {$wbasedatohce}_000015 d,".$wbasedato."_000045
				WHERE 
					Ordhis = '$whistoria' 
					AND Ording = '$wingreso'
					AND Ordest = 'on'
					AND Ordtor = Dettor
					AND Ordnro = Detnro
					AND Detest = 'on'	
					".$filtroPestana."
					$filtroProcedimiento
					AND c.Codigo = Detcod
					AND Tipoestudio = d.Codigo
					".$detimp."
					AND Detesi = Eexcod
					AND Eexpen = 'on'
				UNION
				SELECT
					Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Tiprju,Tipfrm,c.Codigo as Codigo_cup, NoPos, {$wbasedatohce}_000028.Fecha_data as fecha_orden, {$wbasedatohce}_000028.Hora_data as hora_orden
				FROM 
					{$wbasedatohce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$wbasedatohce}_000028, {$wbasedatohce}_000017 c,  {$wbasedatohce}_000015 d,".$wbasedato."_000045
				WHERE 
					Ordhis = '$whistoria' 
					AND Ording = '$wingreso'
					AND Ordest = 'on'
					AND Ordtor = Dettor
					AND Ordnro = Detnro
					AND Detest = 'on'	
					".$filtroPestana."
					$filtroProcedimiento
					AND c.Codigo = Detcod
					AND Tipoestudio = d.Codigo
					AND nuevo = 'on'
					".$detimp."
					AND Detesi = Eexcod
					AND Eexpen = 'on'
				ORDER BY 
					Ordtor,Ordnro";
		
		}
		else{
			
			//Veronica Arismendy
			//Se valida si seleccionaron para imprimir los tipos de ordenes
			$arrOrdenes = explode(",",$arrOrden );			
			if(isset($arrOrden) && count($arrOrdenes) >= 2 || (isset($arrOrden) && count($arrOrdenes) >= 1 && $arrOrdenes[0] != "medtos")){	
				$arrOrdenes = implode("','",$arrOrdenes);
				$textAnd = "AND d.Codigo IN('".$arrOrdenes."')";
			}else{
				$textAnd = "";
			}	
			
			$q = "  SELECT
						Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Tiprju,Tipfrm,Codcups as Codigo_cup, NoPos, {$wbasedatohce}_000028.Fecha_data as fecha_orden, {$wbasedatohce}_000028.Hora_data as hora_orden, {$wbasedatohce}_000028.id as id28
					FROM 
						{$wbasedatohce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$wbasedatohce}_000028, {$wbasedatohce}_000047 c, {$wbasedatohce}_000015 d
					WHERE 
						Ordhis = '$whistoria' 
						AND Ording = '$wingreso'
						AND Ordest = 'on'
						AND Ordtor = Dettor
						AND Ordnro = Detnro
						AND Detest = 'on'	
						".$filtroPestana."
						$filtroProcedimiento
						AND c.Codigo = Detcod
						AND Tipoestudio = d.Codigo
						" . $textAnd . "
					UNION
					SELECT
						Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Tiprju,Tipfrm,c.Codigo as Codigo_cup, NoPos, {$wbasedatohce}_000028.Fecha_data as fecha_orden, {$wbasedatohce}_000028.Hora_data as hora_orden, {$wbasedatohce}_000028.id as id28
					FROM 
						{$wbasedatohce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$wbasedatohce}_000028, {$wbasedatohce}_000017 c,  {$wbasedatohce}_000015 d
					WHERE 
						Ordhis = '$whistoria' 
						AND Ording = '$wingreso'
						AND Ordest = 'on'
						AND Ordtor = Dettor
						AND Ordnro = Detnro
						AND Detest = 'on'	
						".$filtroPestana."
						$filtroProcedimiento
						AND c.Codigo = Detcod
						AND Tipoestudio = d.Codigo
						AND nuevo = 'on'
						" . $textAnd . "
					UNION
					SELECT
						Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp,Tiprju,Tipfrm,c.Codigo as Codigo_cup, NoPos, {$wbasedatohce}_000028.Fecha_data as fecha_orden, {$wbasedatohce}_000028.Hora_data as hora_orden, {$wbasedatohce}_000028.id as id28
					FROM 
						{$wbasedatohce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$wbasedatohce}_000028, {$wbasedatohce}_000017 c,  {$wbasedatohce}_000015 d
					WHERE 
						Ordhis = '$whistoria' 
						AND Ording = '$wingreso'
						AND Ordest = 'on'
						AND Ordtor = Dettor
						AND Ordnro = Detnro
						AND Detest = 'on'	
						".$filtroPestana."
						$filtroProcedimiento
						AND c.Codigo = Detcod
						AND Tipoestudio = d.Codigo
						AND nuevo = 'off'
						" . $textAnd . "
						AND c.codigo NOT IN( SELECT x.codigo from {$wbasedatohce}_000047 x where x.codigo = c.codigo)
					ORDER BY 
						 CONCAT(fecha_orden, ' ', hora_orden) $ordenar ";

		}		
		

		//Veronica Arismendy
		//Se valida si viene desde rp_PacientesEgresadosActivosOrdenes.php para verificar que tipo de ordenes seleccionaron imprimir
		if(isset($desdeImpOrden) && $desdeImpOrden === "on"){
			if(count($arrOrdenes) >= 2 || (count($arrOrdenes) >= 1 && $arrOrdenes[0] != "medtos")){	
				$res_pye = mysql_query($q,$conex) or die (mysql_errno()." - $q ".mysql_error());	
				$num_pye = mysql_num_rows($res_pye);
			}else{
				$num_pye = 0;
			}
		}else{
			$res_pye = mysql_query($q,$conex) or die (mysql_errno()." - $q ".mysql_error());	
			$num_pye = mysql_num_rows($res_pye);
		}
			

		$array_ordenes = array();

		// Si se encontraron medicamentos ordenados
		if($num_pye > 0 and $origen == ''){
			
			while($row_pye = mysql_fetch_assoc($res_pye)){
				
				if(!array_key_exists($row_pye['Detnro'], $array_ordenes)){
									
					$array_ordenes[$row_pye['Detnro']] = array();
					
				}				
				
				if(!array_key_exists($row_pye['Detusu'], $array_ordenes[$row_pye['Detnro']])){
									
					$array_ordenes[$row_pye['Detnro']][$row_pye['Detusu']] = array();					
				}
				
				if(!array_key_exists($row_pye['Dettor'], $array_ordenes[$row_pye['Detnro']][$row_pye['Detusu']])){
									
					$array_ordenes[$row_pye['Detnro']][$row_pye['Detusu']][$row_pye['Dettor']] = array();				
				}
				
				$array_ordenes[$row_pye['Detnro']][$row_pye['Detusu']][$row_pye['Dettor']][] = $row_pye;
						
			
			}
		
		}
		
		
		//Arreglo para facturacion
		if($num_pye > 0 and $origen == 'on'){
			
			while($row_pye = mysql_fetch_assoc($res_pye)){
				
				if(!array_key_exists($row_pye['id28'], $array_ordenes)){
									
					$array_ordenes[$row_pye['id28']] = $row_pye;
					
				}							
			
			}
		
		}	
		
		
		// echo "<pre>";
		// print_r($array_ordenes);
		// echo "</pre>";
		
		$array_cups = cups();		
		$cantidad_ordenes = consultar_dato_apl('CantidadExamenesImpresion');
		
		if($origen == 'on'){
			
			$cantidad_ordenes = consultar_dato_apl('CantidadExamenesImpresionFact');
			$array_ordenes = array_chunk($array_ordenes,$cantidad_ordenes);
			
		
		}else{
			
			//Arreglo final que ordena por la cantidad de examen que necesitan que se aparezacan por orden.
			$array_temp_ord = $array_ordenes;
			
			foreach($array_temp_ord as $key_nro_ord => $array_nro_ord){
				foreach($array_nro_ord as $key_ord => $array_esp){				
					foreach($array_esp as $key_esp => $esp_ord){
								
						$array_ordenes[$key_nro_ord][$key_ord][$key_esp] = array_chunk($array_nro_ord[$key_ord][$key_esp],$cantidad_ordenes);
						
					}	
				}		
			}
			
			
		}				
		
		//ksort($array_ordenes); 
		
		// echo "<pre>";
		// print_r($array_ordenes);
		// echo "</pre>";
		
		if(count($array_ordenes) > 0 and $origen != 'on'){
			
			foreach($array_ordenes as $key_ini => $array_nro_ord){
			
				foreach($array_nro_ord as $key_medico_orden => $array_especialidades){
					$i = 0;
					//Recorro los registros que han sido firmados
					foreach($array_especialidades as $key_esp => $array_paginas_orden){
						
						foreach($array_paginas_orden as $key => $ordenes){
						
						if(count($ordenes) > 1){
							
							// Se define el filtro para consultar el médico tratante
							if(isset($ide) && $ide!="")
								$filtroMedico = " Meddoc = '".$ide."' ";
							else
								$filtroMedico = " Meduma = '".$key_medico_orden."' ";
							
							
							// Consulto los datos del médico
							$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
								."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
								." 	WHERE ".$filtroMedico." "
								."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
							$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
							$nummed = mysql_num_rows($resmed);
							$rowmed = mysql_fetch_array($resmed);

							// Se calculan las dimensiones para la imagen de la firma del médico
							dimensionesImagen($rowmed['Meddoc']);

							// Datos del médico tratante
							$medtdo = $rowmed['Medtdo'];
							$meddoc = $rowmed['Meddoc'];
							$medno1 = $rowmed['Medno1'];
							$medno2 = $rowmed['Medno2'];
							$medap1 = $rowmed['Medap1'];
							$medap2 = $rowmed['Medap2'];
							$medreg = $rowmed['Medreg'];
							$espnom = $rowmed['Espnom'];
							
							$html .= "<div style='page-break-after: always;'>";
							
							$html .= pintarEncabezado($ordenes[$i]['Cconom'].$subEncabezado,$whistoria,$ordenes[$i]['Dettor']."-".$ordenes[$i]['Detnro'], $wingreso, $cconombre);
											
									// Inicio tablas lista detalle						
									$html .= '	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
													  <tbody>
														<tr>
														  <td style="height:17px;background-color:#EEEDED;">
															&nbsp; <b>Servicios Solicitados</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
													  </tbody>
													</table>';

									// Inicio tabla con lista de medicamentos
									$html .= '	<table rules="all" border="1" style="width: 740px;">
													  <tbody>
														<tr align=center>
														  <td style="width:40%;">
															<b>Procedimiento o examen</b>
														  </td>
														  <td style="width:10%;">
															<b>CUPS</b>
														  </td>
														  <td style="width:10%;">
															<b>Fecha y hora en que se orden&oacute;</b>
														  </td>
														  <td style="width:10%;">
															<b>Fecha a realizar</b>
														  </td>
														  <td style="width:40%;">
															<b>Justificación</b>
														  </td>
														</tr>';
							
							//Recorro los medicos que han firmado.
							foreach($ordenes as $key1 => $row){
								
								$fecha_proc = $row['Detfec'];
								$fecha_ordenado = $row['fecha_orden'];
								$hora_proc = $row['hora_orden'];
								$cod_cup = "";
							
								if(array_key_exists($row['Codigo_cup'], $array_cups)){
										
										$cod_cup = $row['Codigo_cup'];
													
								}
								
								// Mayo 27 de 2019
								// Consulto los datos del médico
								// $nombreCup = " SELECT nombre "
											// ."   FROM root_000012 "
											// ." 	WHERE codigo = '".$cod_cup."' ";
								// $res_nombreCup = mysql_query($nombreCup,$conex) or die (mysql_errno()." - ".mysql_error());
								// if( $row_nombreCup = mysql_fetch_array($res_nombreCup) ){
									// $row['Descripcion'] = $row_nombreCup[ 'nombre' ];
								// }
								
								
								
								$html .= '		<tr align=center>
											  <td align=left>&nbsp;'.$row['Descripcion'].'</td>
											  <td>&nbsp;'.$cod_cup.'</td> 
											  <td>&nbsp;'.$fecha_ordenado.'<br>'.$hora_proc.'</td> 
											  <td>&nbsp;'.$fecha_proc.'</td> 
											  <td align=left>&nbsp;'.$row['Detjus'].'</td>
											</tr>
											';
											
								}
								
								$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom, $key_medico_orden);
								
									$html .= '</tbody>
										</table>';
								$html .= "</div>";
								
								//Valida si el paciente es de EPS, si es asi imprime los CTC.
								if($pacEps == 'on'){
								
									//Impresion de registros con CTC
									$j = 0;
									//Recorro los medicos que han firmado.
									foreach($ordenes as $key1 => $row){
									
									//No Pos
									if($row['NoPos']=='on'){
							
									// Se define el filtro para consultar el médico tratante
									if(isset($ide) && $ide!="")
										$filtroMedico = " Meddoc = '".$ide."' ";
									else
										$filtroMedico = " Meduma = '".$key_medico_orden."' ";

									// Consulto los datos del médico
									$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
										."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
										." 	WHERE ".$filtroMedico." "
										."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
									$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
									$nummed = mysql_num_rows($resmed);
									$rowmed = mysql_fetch_array($resmed);

									// Se calculan las dimensiones para la imagen de la firma del médico
									dimensionesImagen($rowmed['Meddoc']);

									// Datos del médico tratante
									$medtdo = $rowmed['Medtdo'];
									$meddoc = $rowmed['Meddoc'];
									$medno1 = $rowmed['Medno1'];
									$medno2 = $rowmed['Medno2'];
									$medap1 = $rowmed['Medap1'];
									$medap2 = $rowmed['Medap2'];
									$medreg = $rowmed['Medreg'];
									$espnom = $rowmed['Espnom'];
									
									$fecha_proc = $row['Detfec'];
									$fecha_ordenado = $row['fecha_orden'];
									$hora_proc = $row['hora_orden'];
									$cod_cup = "";
											
									$html .= "<div style='page-break-after: always;'>";
									
									$html .= pintarEncabezado($ordenes[$j]['Cconom'].'<br />Con CTC',$whistoria,$ordenes[$i]['Dettor']."-".$ordenes[$i]['Detnro'], $wingreso, $cconombre);
													
											// Inicio tablas lista detalle						
											$html .= '	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
															  <tbody>
																<tr>
																  <td style="height:17px;background-color:#EEEDED;">
																	&nbsp; <b>Servicios Solicitados</b>
																  </td>
																</tr>
																<tr>
																  <td  style="height:11px;">
																	&nbsp;
																  </td>
																</tr>
															  </tbody>
															</table>';

										// Inicio tabla con lista de medicamentos
										$html .= '	<table rules="all" border="1" style="width: 740px;">
														  <tbody>
															<tr align=center>
															  <td style="width:40%;">
																<b>Procedimiento o examen</b>
															  </td>
															  <td style="width:10%;">
																<b>CUPS</b>
															  </td>
															  <td style="width:10%;">
																<b>Fecha y hora en que se orden&oacute;</b>
															  </td>
															  <td style="width:10%;">
																<b>Fecha a realizar</b>
															  </td>
															  <td style="width:40%;">
																<b>Justificación</b>
															  </td>
															</tr>';
										
										if(array_key_exists($row['Codigo_cup'], $array_cups)){							
											$cod_cup = $row['Codigo_cup'];
										}
										
										// Mayo 27 de 2019
										// Consulto los datos del médico
										// $nombreCup = " SELECT nombre "
													// ."   FROM root_000012 "
													// ." 	WHERE codigo = '".$cod_cup."' ";
										// $res_nombreCup = mysql_query($nombreCup,$conex) or die (mysql_errno()." - ".mysql_error());
										// if( $row_nombreCup = mysql_fetch_array($res_nombreCup) ){
											// $row['Descripcion'] = $row_nombreCup[ 'nombre' ];
										// }
										
										$html .= '<tr align=center>
											   <td align=left>&nbsp;'.$row['Descripcion'].'</td>
											   <td>&nbsp;'.$cod_cup.'</td>
											   <td>&nbsp;'.$fecha_ordenado.' '.$hora_proc.'</td>
											   <td>&nbsp;'.$fecha_proc.'</td>											   
											   <td align=left>&nbsp;'.$row['Detjus'].'</td>
											  </tr>';										
										
										$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom,$key_medico_orden);
										
											$html .= '</tbody>
												</table>';
										$html .= "</div>";
								
									}
									$j++;
								} //Cierra NoPos
								
								}
							}else{
							
								//Recorro los medicos que han firmado.
								foreach($ordenes as $key1 => $row){
									
									// Se define el filtro para consultar el médico tratante
								if(isset($ide) && $ide!="")
									$filtroMedico = " Meddoc = '".$ide."' ";
								else
									$filtroMedico = " Meduma = '".$row['Detusu']."' ";
								
								$fecha_proc = $row['Detfec'];
								$fecha_ordenado = $row['fecha_orden'];
								$hora_proc = $row['hora_orden'];
								$cod_cup = "";
								
								// Consulto los datos del médico
								$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
									."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
									." 	WHERE ".$filtroMedico." "
									."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
								$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
								$nummed = mysql_num_rows($resmed);
								$rowmed = mysql_fetch_array($resmed);

								// Se calculan las dimensiones para la imagen de la firma del médico
								dimensionesImagen($rowmed['Meddoc']);

								// Datos del médico tratante
								$medtdo = $rowmed['Medtdo'];
								$meddoc = $rowmed['Meddoc'];
								$medno1 = $rowmed['Medno1'];
								$medno2 = $rowmed['Medno2'];
								$medap1 = $rowmed['Medap1'];
								$medap2 = $rowmed['Medap2'];
								$medreg = $rowmed['Medreg'];
								$espnom = $rowmed['Espnom'];
								
								$html .= "<div style='page-break-after: always;'>";
								
								$html .= pintarEncabezado($row['Cconom'].$subEncabezado,$whistoria,$row['Dettor']."-".$row['Detnro'], $wingreso, $cconombre);
																	
										// Inicio tablas lista detalle						
										// Inicio tablas lista detalle						
									$html .= '	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
													  <tbody>
														<tr>
														  <td style="height:17px;background-color:#EEEDED;">
															&nbsp; <b>Servicios Solicitados</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
													  </tbody>
													</table>';

									// Inicio tabla con lista de medicamentos
									$html .= '	<table rules="all" border="1" style="width: 740px;">
													  <tbody>
														<tr align=center>
														  <td style="width:40%;">
															<b>Procedimiento o examen</b>
														  </td>
														  <td style="width:10%;">
															<b>CUPS</b>
														  </td>
														  <td style="width:10%;">
															<b>Fecha y hora en que se orden&oacute;</b>
														  </td>
														  <td style="width:10%;">
															<b>Fecha a realizar</b>
														  </td>
														  <td style="width:40%;">
															<b>Justificación</b>
														  </td>
														</tr>';			

									

										if(array_key_exists($row['Codigo_cup'], $array_cups)){							
											$cod_cup = $row['Codigo_cup'];
										}
										
										// Mayo 27 de 2019
										// Consulto los datos del médico
										// $nombreCup = " SELECT nombre "
													// ."   FROM root_000012 "
													// ." 	WHERE codigo = '".$cod_cup."' ";
										// $res_nombreCup = mysql_query($nombreCup,$conex) or die (mysql_errno()." - ".mysql_error());
										// if( $row_nombreCup = mysql_fetch_array($res_nombreCup) ){
											// $row['Descripcion'] = $row_nombreCup[ 'nombre' ];
										// }
										
										$html .= '<tr align=center>
											   <td align=left>&nbsp;'.$row['Descripcion'].'</td>
											   <td>&nbsp;'.$cod_cup.'</td>
											   <td>&nbsp;'.$fecha_ordenado.' '.$hora_proc.'</td>
											   <td>&nbsp;'.$fecha_proc.'</td>
											   <td align=left>&nbsp;'.$row['Detjus'].'</td>
											  </tr>';		

									$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom,$row['Detusu']);

										$html .= '</tbody>
											</table>';
									
									$html .= "</div>";
								
								
								
								//Valida si el paciente es EPS, si es asi imprime los CTC
								if($pacEps == 'on'){
									//---------------------------------------------------- ******** ---------------
									//Procedimientos No Pos
									//Recorro las ordenes que han firmado y que son NoPos.
									foreach($ordenes as $key1 => $row){
											
											//No Pos
											if($row['NoPos']=='on'){
											
												// Se define el filtro para consultar el médico tratante
											if(isset($ide) && $ide!="")
												$filtroMedico = " Meddoc = '".$ide."' ";
											else
												$filtroMedico = " Meduma = '".$row['Detusu']."' ";

											$fecha_proc = $row['Detfec'];
											$fecha_ordenado = $row['fecha_orden'];
											$hora_proc = $row['hora_orden'];
											$cod_cup = "";
											
											// Consulto los datos del médico
											$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
												."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
												." 	WHERE ".$filtroMedico." "
												."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
											$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
											$nummed = mysql_num_rows($resmed);
											$rowmed = mysql_fetch_array($resmed);

											// Se calculan las dimensiones para la imagen de la firma del médico
											dimensionesImagen($rowmed['Meddoc']);

											// Datos del médico tratante
											$medtdo = $rowmed['Medtdo'];
											$meddoc = $rowmed['Meddoc'];
											$medno1 = $rowmed['Medno1'];
											$medno2 = $rowmed['Medno2'];
											$medap1 = $rowmed['Medap1'];
											$medap2 = $rowmed['Medap2'];
											$medreg = $rowmed['Medreg'];
											$espnom = $rowmed['Espnom'];
											
											$html .= "<div style='page-break-after: always;'>";
											
											$html .= pintarEncabezado($row['Cconom'].'<br />Con CTC',$whistoria,$row['Dettor']."-".$row['Detnro'], $wingreso, $cconombre);
																				
													// Inicio tablas lista detalle						
													// Inicio tablas lista detalle						
												$html .= '	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
																  <tbody>
																	<tr>
																	  <td style="height:17px;background-color:#EEEDED;">
																		&nbsp; <b>Servicios Solicitados</b>
																	  </td>
																	</tr>
																	<tr>
																	  <td  style="height:11px;">
																		&nbsp;
																	  </td>
																	</tr>
																  </tbody>
																</table>';

												// Inicio tabla con lista de ordenes
												$html .= '	<table rules="all" border="1" style="width: 740px;">
																  <tbody>
																	<tr>
																	  <td style="width:40%;">
																		<b>Procedimiento o examen</b>
																	  </td> 
																	  <td style="width:10%;">
																		<b>CUPS</b>
																	  </td> 
																	  <td style="width:10%;">
																		<b>Fecha y hora en que se orden&oacute;</b>
																	  </td>
																	  <td style="width:10%;">
																		<b>Fecha a realizar</b>
																	  </td>
																	  <td style="width:40%;">
																		<b>Justificación</b>
																	  </td>
																	</tr>';			

												

												if(array_key_exists($row['Codigo_cup'], $array_cups)){							
													$cod_cup = $row['Codigo_cup'];
												}
												
												// Mayo 27 de 2019
												// Consulto los datos del médico
												// $nombreCup = " SELECT nombre "
															// ."   FROM root_000012 "
															// ." 	WHERE codigo = '".$cod_cup."' ";
												// $res_nombreCup = mysql_query($nombreCup,$conex) or die (mysql_errno()." - ".mysql_error());
												// if( $row_nombreCup = mysql_fetch_array($res_nombreCup) ){
													// $row['Descripcion'] = $row_nombreCup[ 'nombre' ];
												// }
										
												$html .= '<tr align=center>
													   <td align=left>&nbsp;'.$row['Descripcion'].'</td>
													   <td>&nbsp;'.$cod_cup.'</td>
													   <td>&nbsp;'.$fecha_ordenado.' '.$hora_proc.'</td>
													   <td>&nbsp;'.$fecha_proc.'</td>
													   <td align=left>&nbsp;'.$row['Detjus'].'</td>
													  </tr>';	
												

												$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom,$row['Detusu']);

													$html .= '</tbody>
														</table>';
												
												$html .= "</div>";
											}
										}
									}
								}
								$i++;
							}
						}
					}
				}
			}			
		}
				
		//Impresion de procedimientos y examenes por facturacion
		if(count($array_ordenes) > 0 and $wtodos_ordenes == 'on' and $origen == 'on'){
			
			foreach($array_ordenes as $key => $ordenes){	
					
					$html .= "<div style='page-break-after: always;'>";
					
					$html .= pintarEncabezado("OTRAS ORDENES",$whistoria,"", $wingreso, "");
									
							// Inicio tablas lista detalle						
							$html .= '	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
											  <tbody>
												<tr>
												  <td style="height:17px;background-color:#EEEDED;">
													&nbsp; <b>Servicios Solicitados</b>
												  </td>
												</tr>
												<tr>
												  <td  style="height:11px;">
													&nbsp;
												  </td>
												</tr>
											  </tbody>
											</table>';

							// Inicio tabla con lista de medicamentos
							$html .= '	<table rules="all" border="1" style="width: 740px;">
											  <tbody>
												<tr align=center>
												  <td style="width:10%;">
													<b>Nro Orden</b>
												  </td>
												  <td style="width:10%;">
													<b>Fecha y hora en que se orden&oacute;</b>
												  </td>
												  <td style="width:10%;">
													<b>Fecha a realizar</b>
												  </td>
												  <td style="width:10%;">
													<b>Tipo Orden</b>
												  </td>												  
												  <td style="width:30%;">
													<b>Procedimiento o examen</b>
												  </td>
												  <td style="width:25%;">
													<b>Justificaci&oacute;n</b>
												  </td>
												  <td style="width:30%;">
													<b>M&eacute;dico</b>
												  </td> 
												  <td style="width:30%;">
													<b>Firma</b>
												  </td>
												</tr>';								
						
						//Recorro los medicos que han firmado.
						foreach($ordenes as $key1 => $row){
							
								// Se define el filtro para consultar el médico tratante
								$filtroMedico = " Meduma = '".$row['Detusu']."' ";
								$codigo_medico = $row['Detusu'];
								
								$fecha_proc = $row['fecha_orden'];
								$hora_proc = $row['hora_orden'];
								$fecha_realizo = $row['Detfec'];
								
								$cod_cup = "";
								$tipo_orden = $row['Dettor'];
								$nro_orden = $row['Detnro'];
								
								// Consulto los datos del médico
								$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
									."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
									." 	WHERE ".$filtroMedico." "
									."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
								$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
								$nummed = mysql_num_rows($resmed);
								$rowmed = mysql_fetch_array($resmed);

								// Se calculan las dimensiones para la imagen de la firma del médico
								dimensionesImagen($rowmed['Meddoc']);

								// Datos del médico tratante
								$medtdo = $rowmed['Medtdo'];
								$meddoc = $rowmed['Meddoc'];
								$medno1 = $rowmed['Medno1'];
								$medno2 = $rowmed['Medno2'];
								$medap1 = $rowmed['Medap1'];
								$medap2 = $rowmed['Medap2'];
								$medreg = $rowmed['Medreg'];
								$espnom = $rowmed['Espnom'];
									
								$nombre_medico_exa = $medno1." ".$medno2." ".$medap1." ".$medap2;
								
								if(file_exists('../../images/medical/hce/Firmas/'.$codigo_medico.'.png'))
								{
									if(isset($wtodos_ordenes)){	
									$firma = '				<img src="../../../images/medical/hce/Firmas/'.$codigo_medico.'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';
									}else{
										
										$firma = '				<img src="../../images/medical/hce/Firmas/'.$codigo_medico.'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';	
									}
								}	
								else
								{
									$firma = '&nbsp;';
								}
								
								
								if(array_key_exists($row['Codigo_cup'], $array_cups)){							
										$cod_cup = $row['Codigo_cup'];
									}
									
									// Mayo 27 de 2019
									// Consulto los datos del médico
									// $nombreCup = " SELECT nombre "
												// ."   FROM root_000012 "
												// ." 	WHERE codigo = '".$cod_cup."' ";
									// $res_nombreCup = mysql_query($nombreCup,$conex) or die (mysql_errno()." - ".mysql_error());
									// if( $row_nombreCup = mysql_fetch_array($res_nombreCup) ){
										// $row['Descripcion'] = $row_nombreCup[ 'nombre' ];
									// }
									
									$html .= '<tr align=center>
										   <td>&nbsp;'.$nro_orden.'</td>
										   <td align=center>&nbsp;'.$fecha_proc.' '.$hora_proc.'</td>
										   <td align=center>&nbsp;'.$fecha_realizo.'</td>
										   <td align=left>&nbsp;'.$row['Cconom'].'</td>										   
										   <td align=left>&nbsp;'.$row['Descripcion'].'</td>
										   <td align=left>&nbsp;'.$row['Detjus'].'</td>										   
										   <td align=center>&nbsp;'.$nombre_medico_exa.'</td>
										   <td align=left>&nbsp;'.$firma.'</td>
										  </tr>';							
						
							}
						
					$html .= '</tbody>
							</table>';
					$html .='<br><table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
									  <tbody>
										<tr>
										  <td style="text-align:center;" calss="descripcion">
											<b>- Firmado electrónicamente -</b>
										  </td>
										</tr>
									  </tbody>
									</table>';
					$html .= "</div>";
				
			}	
		}
	}	
	
	
//=====================================================================================================

if($wtodos_ordenes != 'on' and $origen != ''){
//Impresion de formularios hce relacionados.
$query = "SELECT Fecha_data from ".$wbasedato."_000016 
		   WHERE Inghis='".$whistoria."'
			 AND Inging='".$wingreso."'";
$err1 = mysql_query($query,$conex);
$num1 = mysql_num_rows($err1);
	
if($num1 > 0){
	
	$row1 = mysql_fetch_array($err1);
	$wfechai=$row1['Fecha_data'];
	
	}
	else
	{
		$wfechai=date("Y-m-d");
	}

$wfechaf=date("Y-m-d");

$array_consultas = array();
$array_formularios = array();

//Impresion de formularios hce relacionados.
	if(count($array_ordenes) > 0){
			
			foreach($array_ordenes as $key_ini => $array_nro_ord){
			
				foreach($array_nro_ord as $key_medico_orden => $array_especialidades){
					$i = 0;
					//Recorro los registros que han sido firmados
					foreach($array_especialidades as $key_esp => $array_paginas_orden){
						
						foreach($array_paginas_orden as $key => $ordenes){
						
						//Recorro los medicos que han firmado.
							foreach($ordenes as $key1 => $row){
																
								$formulario = $row['Tipfrm'];	
								
								if(trim($formulario) != ""){
								
							    $queryI = " SELECT ".$wbasedatohce."_000002.Detdes,".$wbasedatohce."_".$formulario.".movdat,".$wbasedatohce."_000002.Detorp,".$wbasedatohce."_".$formulario.".fecha_data,".$wbasedatohce."_".$formulario.".Hora_data,".$wbasedatohce."_000002.Dettip,".$wbasedatohce."_000002.Detcon,".$wbasedatohce."_000002.Detpro,".$wbasedatohce."_000001.Encdes,".$wbasedatohce."_000002.Detarc,".$wbasedatohce."_000002.Detume,".$wbasedatohce."_000002.Detimp,".$wbasedatohce."_".$formulario.".movusu,".$wbasedatohce."_000001.Encsca,".$wbasedatohce."_000001.Encoim,".$wbasedatohce."_000002.Dettta,".$wbasedatohce."_000002.Detfor,".$wbasedatohce."_000001.Encfir 
											  FROM ".$wbasedatohce."_".$formulario.",".$wbasedatohce."_000002,".$wbasedatohce."_000001
											 WHERE ".$wbasedatohce."_".$formulario.".movpro='".$formulario."'
											   AND ".$wbasedatohce."_".$formulario.".movhis='".$whistoria."'
											   AND ".$wbasedatohce."_".$formulario.".moving='".$wingreso."'
											   AND ".$wbasedatohce."_".$formulario.".fecha_data between '".$wfechai."' AND '".$wfechaf."'
											   AND ".$wbasedatohce."_".$formulario.".movpro=".$wbasedatohce."_000002.detpro
											   AND ".$wbasedatohce."_".$formulario.".movcon = ".$wbasedatohce."_000002.detcon
											   AND ".$wbasedatohce."_000002.detest='on'
											   AND ".$wbasedatohce."_000002.Dettip != 'Titulo' 
											   AND ".$wbasedatohce."_000002.Detpro = ".$wbasedatohce."_000001.Encpro";							
							
								echo "<div style='page-break-after: always;'>";								
								imprimir($conex,$wbasedatohce,$queryI,$whistoria,$wingreso,$key_medico_orden,$formulario,$$wintitulo,$Hgraficas);
								echo "</div>";
								}
							}
						}
					}
				}
			}
		}
}		
	// echo "<pre>";
	// echo "<div>";
	// print_r($array_formularios);
	// echo "</pre>";
	// echo "</div>";
	
	
		
//=====================================================================================================
//Consultar indicaciones al egreso
$qind = " SELECT Karegr, Karusu
			FROM ".$wbasedato."_000053
		   WHERE Karhis = '".$whistoria."'
			 AND Karing = '".$wingreso."'
		   ORDER BY Fecha_data DESC ";
$resind = mysql_query($qind, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qind . " - " . mysql_error());
$rowind = mysql_fetch_array($resind);				

if($rowind['Karegr'] != ''){
	
	$html .= "<div style='page-break-after: always;'>";
						
	$html .= pintarEncabezado('INDICACIONES AL EGRESO',$whistoria,'', $wingreso, $cconombre);
									
	// Inicio tablas lista detalle						
	$html .= '	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
					  <tbody>
						<tr>
						  <td style="height:17px;background-color:#EEEDED;">
							&nbsp; <b>Indicaciones</b>
						  </td>
						</tr>							
					  </tbody>
					</table>';

	// Inicio tabla con lista de medicamentos
	$html .= '	<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">';
	$html .= '	<tr>
			  <td>&nbsp;<br><br>'.$rowind['Karegr'].'<br><br></td>
			</tr>';					
	
	// Se define el filtro para consultar el médico tratante
	if(isset($ide) && $ide!="")
		$filtroMedico = " Meddoc = '".$ide."' ";
	else
		$filtroMedico = " Meduma = '".$rowind['Karusu']."' ";
	
	// Consulto los datos del médico
	$q_med = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
		."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
		." 	WHERE ".$filtroMedico." "
		."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
	$resmed = mysql_query($q_med,$conex) or die (mysql_errno()." - ".mysql_error());
	$nummed = mysql_num_rows($resmed);
	$rowmed = mysql_fetch_array($resmed);
	
	// Datos del médico tratante
	$medtdo = $rowmed['Medtdo'];
	$meddoc = $rowmed['Meddoc'];
	$medno1 = $rowmed['Medno1'];
	$medno2 = $rowmed['Medno2'];
	$medap1 = $rowmed['Medap1'];
	$medap2 = $rowmed['Medap2'];
	$medreg = $rowmed['Medreg'];
	$espnom = $rowmed['Espnom'];
	
	$html .= pintarPiePagina($medtdo,$meddoc,$medno1,$medno2,$medap1,$medap2,$medreg,$espnom, $rowind['Karusu']);

		$html .= '</tbody>
			</table>';

	$html .= "</div>";

}

$html .= "</div>";


if($origen != ''){	

  $wfecha = date("Y-m-d");
  $user = $_SESSION['user'];
  $wnombrePDF = $user.$whistoria.$wingreso.$wfecha;
  
 
  //CREAR UN ARCHIVO .HTML CON EL CONTENIDO CREADO
  $dir = 'impresion_ordenes';
  if(is_dir($dir)){ }
  else { mkdir($dir,0777); }
  $archivo_dir = $dir."/".$wnombrePDF.".html";
   echo "<div style='display:none;'>".$archivo_dir."</div>";
  if(file_exists($archivo_dir)){
	unlink($archivo_dir);
  }
  $f = fopen( $archivo_dir, "w+" );
  fwrite( $f, $html);
  fclose( $f );

  $respuesta = shell_exec( "./generarPdf_ordenes.sh ".$wnombrePDF );

  $htmlFactura = "<br><br>"
				  ."<object type='application/pdf' data='".$dir."/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='900' height='700'>"
					."<param name='src' value='".$dir."/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
					."<p style='text-align:center; width: 60%;'>"
					  ."Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />"
					  ."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
						."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
					  ."</a>"
					."</p>"
				  ."</object>";
  
  echo "<div align='center'>";
  echo "<br>";
  echo $htmlFactura;  
  echo "</div>";

	
}else{

	if($wtodos_ordenes == 'on'){
	  
	  $wfecha = date("Y-m-d");
	  $user = $_SESSION['user'];
	  $wnombrePDF = $user.$whistoria.$wingreso.$wfecha;
	  
	 
	  //CREAR UN ARCHIVO .HTML CON EL CONTENIDO CREADO
	  $dir = 'impresion_ordenes';
	  if(is_dir($dir)){ }
	  else { mkdir($dir,0777); }
	  $archivo_dir = $dir."/".$wnombrePDF.".html";
	   echo "<div style='display:none;'>".$archivo_dir."</div>";
	  if(file_exists($archivo_dir)){
		unlink($archivo_dir);
	  }
	  $f = fopen( $archivo_dir, "w+" );
	  fwrite( $f, $html);
	  fclose( $f );

	  $respuesta = shell_exec( "./generarPdf_ordenes.sh ".$wnombrePDF );
	
	if(isset($_GET['automatizacion_pdfs'])){
		//prueba para capturar PDF en carpeta de soportes JAIME MEJIA
		$sinMedicamentos = 'sin medicamentos';

		$posicion_sin_medicamento = strpos($html,$sinMedicamentos);
		if($posicion_sin_medicamento === false){
			$dir = 'impresion_ordenes';
			$soporte = $_GET['soporte'];
			$wnombrePDF2 = $whistoria . '-' . $wingreso . '-' . $soporte;
			if(is_dir($dir)){ }
			else { mkdir($dir,0777); }
			$archivo_dir = $dir."/".$wnombrePDF2.".html";
			 echo "<div style='display:none;'>".$archivo_dir."</div>";
			if(file_exists($archivo_dir)){
			  unlink($archivo_dir);
			}
			$f = fopen( $archivo_dir, "w+" );
			fwrite( $f, $html);
			fclose( $f );
	  
			$respuesta = shell_exec( "./generarPdf_ordenes.sh ".$wnombrePDF2 );
		}
		else{
			$respuesta = shell_exec( "./generarPdf_ordenes.sh ".$wnombrePDF );
		}
	}
	$botonEnviarPdf = "";
	if($enviarCorreo=="on")
	{
		$nombrePaciente = consultarNombrePaciente($conex, $wemp_pmla, $whistoria);
		$nombreEmpresa = consultarAliasPorAplicacion($conex, $wemp_pmla, "nombreEmpresa");
		$archivoPdf = $dir."/".$wnombrePDF.".pdf";
		$user_session = explode('-',$_SESSION['user']);
		$usuario = $user_session[1];
		$nombreEntidad = consultarResponsablePaciente($conex, $wbasedato, $whistoria, $wingreso);
		$tiposOrdenesGeneradas = consultarTipoOrdenesGeneradas($conex, $wbasedatohce, $arrOrden);
		
		$botonEnviarPdf = "<input type='button' id='btnEnviarPdf' onclick='enviarPdf(\"".$whistoria."\",\"".$wingreso."\",\"".$dir."\",\"".$wnombrePDF.".pdf"."\",\"".$nombrePaciente."\",\"".$nombreEmpresa."\",\"".$wbasedato."\",\"".$usuario."\",\"".$tiposOrdenesGeneradas."\",\"".$nombreEntidad."\");' value='Enviar PDF'>
							<div id='msjEspere' align='center' style='display:none;'>
								<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...<br><br>
							</div>";
	}

	if(!isset($_GET['automatizacion_pdfs'])){
	  $htmlFactura = $botonEnviarPdf
					  ."<br><br>"
					  ."<object type='application/pdf' data='".$dir."/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='900' height='700'>"
						."<param name='src' value='".$dir."/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
						."<p style='text-align:center; width: 60%;'>"
						  ."Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />"
						  ."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
							."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
						  ."</a>"
						."</p>"
					  ."</object>";
	  
	  echo "<div align='center'>";
	  echo "<br>";
	  echo $htmlFactura;  
	  echo "</div>";	
	}
	  
	}else{
	  
	  echo $html;
		
	}
	
}



if($boton_imp == '' and $ctcNoPos == '' and $wtodos_ordenes == '' and !isset($_GET['automatizacion_pdfs'])){
	echo "<p align=center><input type='button' class='printer' value='Imprimir'></p><p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";
}
echo '<script> if(document.getElementById("ordenAnexa")) { document.getElementById("ordenAnexa").innerHTML = document.getElementById("anx'.$ordenAnexa.'").innerHTML; } </script>';


echo '</body>
</html>';
}
?>
