<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	genArchivoIntercambioNuevaEps.php
 * Fecha		:	2015-08-13
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Programa para generar el archivo de facturas y/o el de notas creditos para la nueva eps de acuerdo a las estructuras solicitada.
 * Condiciones  :  
 *********************************************************************************************************
 
 Actualizaciones:

	2016-07-27:		Jessica Madrid  - se agregan comillas simples para corregir error en el query cuando existen varias empresas.
	2015-11-06:		Jessica Madrid  - se modifica el valor total en detalle de factura para que reste en vez de sumar con el fin de corregir error.
 **********************************************************************************************************/
 
$wactualiz = "2016-07-27";
 
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
	<title>Archivo Intercambio Nueva EPS</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<style>
		/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMA�O  */
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
		
		fieldset{
			border: 2px solid #e2e2e2;
		}

		legend{
			border: 2px solid #e2e2e2;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		
		.descargafile{
			font-size: 25px
		}
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
		
		//var enlace = '<a href="../../planos/9003304160-23423.txt" class="descargafile" target="_blank" download>Descargar archivo txt</a>';
		//$("#contenido").html(enlace);
		
		$("#fecha_inicio, #fecha_fin").datepicker({
		  showOn: "button",
		  buttonImage: "../../images/medical/root/calendar.gif",
		  buttonImageOnly: true,
		  maxDate:"+0D"
		});		
	});
	
	function generarReporte(){
		var wemp_pmla = $("#wemp_pmla").val();
		var empresa = $("#empresa").val();
		var fecha_inicio = $("#fecha_inicio").val();
		var fecha_fin = $("#fecha_fin").val();
		var wfuente = $("#wfuente").val();
		var wfactura = $("#wfac").val();
		var whis = $("#whis").val();
		var wing = $("#wing").val();
		var wdoc = $("#wdoc").val();
		var wfacturaonc = $("#facturaonc").val();
		
		$.blockUI({ message: $('#msjEspere') });

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('genArchivoIntercambioNuevaEps.php', { wemp_pmla: wemp_pmla, action: "generarReporte", wempresa: empresa, wfuente: wfuente, wfacturaonc: wfacturaonc, wfactura: wfactura, whis: whis, wing: wing, wdoc: wdoc, wfeci: fecha_inicio, wfecf: fecha_fin, consultaAjax: ''} ,
			function(data) {
				$.unblockUI();
				$("#contenido").html(data);
				$(".enlace_retornar").show();
			});
	}
	
	function seleccionarTodas( obj ){
		obj = jQuery(obj);
		if( obj.is(":checked") ){
			$(".elegirfactura").attr("checked",true);
			$(".elegirfactura").prop("checked",true);
		}else{
			$(".elegirfactura").attr("checked",false);
			$(".elegirfactura").prop("checked",false);
			$("#totalValor").html( 0 );
			$("#totalSaldo").html( 0 );
		
			return;
		}
		var valor=0;
		var saldo=0;
		var totalvalor = 0; 
		var totalsaldo = 0;
		$(".filafactura").each(function(){
			valor = parseFloat( ($(this).find(".valor").html()).replace(/,/gi, "") );
			saldo = parseFloat( ($(this).find(".saldo").html()).replace(/,/gi, "") );
			if( isNaN(valor) )	valor=0;
			if( isNaN(saldo) )	saldo=0;				
			totalvalor+= valor;
			totalsaldo+= saldo;
		});
		totalValor = formato_numero(totalvalor, 0, ".", ",");
		totalsaldo = formato_numero(totalsaldo, 0, ".", ",");
		$("#totalValor").html( totalvalor );
		$("#totalSaldo").html( totalsaldo );
	}
	
	function elegirFactura( obj ){
		obj = jQuery(obj);
		var checked = false;
		if( obj.is(":checked") )
			checked = true;
			
		obj= obj.parent().parent(); //referencia al tr
		var valor = parseFloat(  (obj.find(".valor").html()).replace(/,/gi, "") );
		var saldo = parseFloat(  (obj.find(".saldo").html()).replace(/,/gi, "") );
		if( isNaN(valor) )	valor=0;
		if( isNaN(saldo) )	saldo=0;
		
		var totalvalor =  parseFloat( ($("#totalValor").html()).replace(/,/gi, "") );
		var totalsaldo =  parseFloat( ($("#totalSaldo").html()).replace(/,/gi, "") );
		
		if( checked == true ){
			totalvalor+= valor;
			totalsaldo+= saldo;
		}else{
			totalvalor-= valor;
			totalsaldo-= saldo;
		}
		totalValor = formato_numero(totalvalor, 0, ".", ",");
		totalsaldo = formato_numero(totalsaldo, 0, ".", ",");
		$("#totalValor").html( totalvalor );
		$("#totalSaldo").html( totalsaldo );
		
	}
	
	function crearArchivo(){
		var facturas = new Array();
		$(".elegirfactura").each(function(){
			if( $(this).is(":checked") ){
				var fac = new Object();
				fac.fue = $(this).attr("fuente");
				fac.fac = $(this).attr("factura");
				facturas.push( fac );
			}
		});
		
		if( facturas.length > 0 ){
			var wemp_pmla = $("#wemp_pmla").val();	
			var empresaHidden = $("#empresaHidden").val();	
			var wfacturaonc = $("#wfacturaonc").val();	
			var wfeci = $("#wfeci").val();	
			var wfecf = $("#wfecf").val();	
			$.blockUI({ message: $('#msjEspere') });

			//Realiza el llamado ajax con los parametros de busqueda
			$.post('genArchivoIntercambioNuevaEps.php', { wempresa: empresaHidden,  wfeci: wfeci, wfecf: wfecf, wfacturaonc: wfacturaonc, wemp_pmla: wemp_pmla, action: "crearArchivo", wdatos: facturas, consultaAjax: ''} ,
			function(data) {
				$.unblockUI();
				
				var texto = nombreArchivo(data);
				if( texto != "" ){
					var enlace = '<a href="../../planos/'+texto+'" id="link_dd" name="link_dd" class="descargafile" value="111" target="_blank" download>Descargar archivo '+texto+'</a>';
					$("#contenido").html(enlace);
				}else{
					alert("Error al generar el archivo \n"+data);
				}
				
				$(".enlace_retornar").show();
				
			});
		}else{
			alert("Debe elegir al menos una factura.");
		}
	}
	
	function nombreArchivo( texto ){
		var patt = /\[\[(.*?)\]\]/g;
		var arr = patt.exec( texto );
		if( arr == null )
			return "";
		console.log( arr );
		return arr[1]; 
	}
	
	function consultarFactura(obj, fuente, factura){
		obj = jQuery(obj);
		obj = obj.parent().parent();
		var wemp_pmla = $("#wemp_pmla").val();	
		
		$.blockUI({ message: $('#msjEspere') });

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('genArchivoIntercambioNuevaEps.php', { wemp_pmla: wemp_pmla, action: "consultarFactura", wfuente: fuente, wfactura: factura, consultaAjax: ''} ,
		function(data) {
			$.unblockUI();
			obj.next(".detalle").find("td").html( data+"<br><center><input type='button' value='Ocultar' onclick='cerrarDetalle(this)' /></center><br>" );
			obj.next(".detalle").show();
			
			var ele = jQuery( obj.next(".detalle") );
			posicion = ele.offset();
			ejeY = posicion.top;
		
			ejeY = ejeY -30;
			//Para que vaya al tr donde estaba
			$('html, body').animate({
				scrollTop: ejeY+'px',
				scrollLeft: '0px'
			},0);
		});
	}
	
	function cerrarDetalle( obj ){
		obj = jQuery(obj);
		obj.parents(".detalle").hide();
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
	
	
	function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06
        numero=parseFloat(numero);
        if(isNaN(numero)){
            return "";
        }

        if(decimales!==undefined){
            // Redondeamos
            numero=numero.toFixed(decimales);
        }

        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace(".", separador_decimal!==undefined ? separador_decimal : ",");

        if(separador_miles){
            // A�adimos los separadores de miles
            var miles=new RegExp("(-?[0-9]+)([0-9]{3})");
            while(miles.test(numero)) {
                numero=numero.replace(miles, "$1" + separador_miles + "$2");
            }
        }

        return numero;
    }

</script>
</head>
   
<?php
	
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");
include_once("root/montoescrito.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
$nitsArchivoIntercambio = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nitsArchivoIntercambio');
$wmovhos = "";
$wtcx = "";
// $sep = "|";
$sep = "";
$saltoLineaArchivo = PHP_EOL; //Estaba "\n", probar cual interpreta el programa de nueva eps

//---------------------------------------------------------------------------------------------
// --> 	MAESTRO DE CONCEPTOS:
//		- Antigua facturacion 	--> 000004
//		- Nueva facturacion 	--> 000200
//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
//		de conceptos cambiara por la tabla 000200.
//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
//----------------------------------------------------------------------------------------------
$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "generarReporte"){
		generarReporte( $wfacturaonc, $wempresa, @$wfuente, @$wfactura, @$whis, @$wing, @$wdoc, $wfeci, $wfecf );
		
	}elseif( $action == "consultarFactura"){
		consultarFactura( $wfuente, $wfactura );
	}elseif( $action == "crearArchivo"){
		crearArchivo( $wdatos, $wempresa, $wfacturaonc, $wfeci, $wfecf );
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************
	
	function crearArchivo( $wdatos, $wempresa, $wfacturaonc, $wfeci, $wfecf  ){
		global $conex;
        global $wbasedato;
		global $wtcx;
		global $wmovhos;
		global $wemp_pmla;
		global $wusuario;
		global $sep;
		global $tablaConceptos;
		global $saltoLineaArchivo;
		
		//Anexo 1, Tipos de identificacion
		/*	11	Registro Civil	RC
			12	Tarjeta de Identidad	TI
			13	Cedula de Ciudadan�a	CC
			22	Cedula de extranjeria	CE
			31	NIT	NIT
			41	Pasaporte	PA
			42	Menor sin identificacion	MS
			43	Adulto sin identificacion	AS
			44	Numero Unico de Identificacion	NU*/
		$arr_tipos_identificacion = array('RC'=>11,
										  'TI'=>12,
										  'CC'=>13,
										  'CE'=>22,
										  'NIT'=>31,
										  'PA'=>41,
										  'MS'=>42,
										  'AS'=>43,
										  'NU'=>44
										  );
		//Anexo 3, Tipo IVA
		/*	0	0%
			1	10%
			2	12%
			3	14%
			4	15%
			5	16%
			*/
		$arr_tipo_iva = array(			'0'=>0,
										'10'=>1,
										'12'=>2,
										'14'=>3,
										'15'=>4,
										'16'=>5
										  );
										  
		//Anexo 4, Tipos de atencion
		/*	1		CONSULTAS AMBULATORIAS		
			2		SERVICIOS ODONTOLOGICOS AMBULATORIAS		
			3		EXAMENES DE LABORATORIO, IMAGENES Y OTRAS AYUDAS DIAGNOSTICAS AMBULATORIAS		
			4		PROCEDIMIENTOS TERAPEUTICOS AMBULATORIOS		
			5		MEDICAMENTOS DE USO AMBULATORIO		
			6		INSUMOS, OX. Y ARR. DE EQUIPOS DE USO AMBULATORIO		
			7		LENTES		
			8		ATENCION INICIAL DE URGENCIAS		
			9		SERVICIOS DE INTERNACION Y/O CIRUGIA (HOSPITALARIA O AMBULA)		
			*/
		$arr_tipo_atencion = array('0'=>0,
										  '10'=>1,
										  '12'=>2,
										  '14'=>3,
										  '15'=>4,
										  '16'=>5
										  );		
		$facturaHos = "";
		
		if( $wfacturaonc == "FAC" )
		{
					
			
			/***********************************/
			/* CREAR ENCABEZADO
			/***********************************/
			$tiporeg = "0"; //1
			// $numRegsEnviados = count($wdatos); //6
			// $numRegsEnviados = count($wDetalle); //6  -- Se llena m�s abajo
			$numFacsEnviados = count($wdatos); //6
			$fechaEnvio = date("d/m/Y"); //10
			$codigoSucursalIPS = "001"; //3
			$tipoDocIPS = "NI"; //2
			$nitIPS = ""; //15
			$digitoVerificadorIPS = ""; //1
			$nombreIPS = ""; //50
			$tipoDocumentoEPS = "NI"; //2
			$nitEPS = ""; //15
			$digitoVerificadorEPS = ""; //1
			$nombreEPS = ""; //50
			
			$q = " SELECT empdes as nombre, empnit as nit, emphos as hos
					 FROM root_000050 
					WHERE empcod = '".$wemp_pmla."'
			";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			$num = mysql_num_rows($res);	
			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				$facturaHos = $row['hos'];
				$aux = explode("-",$row['nit']);
				$nitIPS = $aux[0];
				if( isset($aux[1]) == true && $aux[1] != "" )
					$digitoVerificadorIPS = $aux[1];
				$nombreIPS = $row['nombre'];
			}
			if( $digitoVerificadorIPS == "" ){
				$digitoVerificadorIPS = calcularDV($nitIPS);
			}
			
				
			$consecutivoEmpresa = 0;
			
			$consecutivoEmpresa = consultarAliasPorAplicacion($conex, $wemp_pmla, "consecutivoFAC-".$wempresa);		
			// $myfile = fopen("../../planos/".$nitIPS."-".$consecutivoEmpresa.".txt", "w") or die("Unable to open file!");	
			$myfile = fopen("../../planos/".$nitIPS.$digitoVerificadorIPS."-".$consecutivoEmpresa.".txt", "w") or die("Unable to open file!");	
			$nitIPSsf = $nitIPS;
			$q = " SELECT empnom as nombre, empnit as nit, empdiv as digito
					 FROM ".$wbasedato."_000024
					WHERE empcod = '".$wempresa."'				
			";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			$num = mysql_num_rows($res);
			if ($num > 0){
				$row = mysql_fetch_array($res);
				$nitEPS = $row['nit'];
				$nombreEPS = $row['nombre'];
				$digitoVerificadorEPS = $row['digito'];
			}
			
			$tiporeg = formatearCadena( $tiporeg, "n", 1 ); //1
			// $numRegsEnviados = formatearCadena( $numRegsEnviados, "n", 6 ); //6 -- Se formatea m�s abajo
			$numFacsEnviados = formatearCadena( $numFacsEnviados, "n", 6 ); //6
			$fechaEnvio = formatearCadena( $fechaEnvio, "c", 10 ); //10
			$codigoSucursalIPS = formatearCadena( $codigoSucursalIPS, "n", 3 ); //3
			$tipoDocIPS = formatearCadena( $tipoDocIPS, "c", 2 ); //2
			$nitIPS = formatearCadena( $nitIPS, "n", 15 ); //15
			$digitoVerificadorIPS = formatearCadena( $digitoVerificadorIPS, "n", 1 ); //1
			$nombreIPS = formatearCadena( $nombreIPS, "c", 50 ); //50
			$tipoDocumentoEPS = formatearCadena( $tipoDocumentoEPS, "c", 2 ); //2
			$nitEPS = formatearCadena( $nitEPS, "n", 15 ); //15
			$digitoVerificadorEPS = formatearCadena( $digitoVerificadorEPS, "n", 1 ); //1
			$nombreEPS = formatearCadena( $nombreEPS, "c", 50 ); //50
			
			// // Se imprime m�s abajo para poder llenar $numRegsEnviados (cantidad de detalles)
			// //IMPRIMIR LINEA
			// $linea = $tiporeg.$sep.$numRegsEnviados.$sep.$numFacsEnviados.$sep.$fechaEnvio.$sep.$codigoSucursalIPS.$sep.$tipoDocIPS.$sep.$nitIPS.$sep.$digitoVerificadorIPS.$sep.$nombreIPS.$sep.$tipoDocumentoEPS.$sep.$nitEPS.$sep.$digitoVerificadorEPS.$sep.$nombreEPS;
			// fwrite($myfile, $linea.$saltoLineaArchivo);

			
			$arr_facturas = array();
			foreach($wdatos as $wdato ){
				array_push( $arr_facturas, "'".$wdato['fac']."'" );
			}
					
			$q="";		
			if ($facturaHos == "on")  //Facturacion Hospitalaria
			{
				$q = "   SELECT fenffa as fuente, fenfac as factura, fenfec as fecha, fenval as valor,	fensal as saldo, 
								fenesf as estadofac, fenhis as historia, fening as ingreso, fenest as estado, fennpa as nombrepac, 
								fendpa as docpac, fenviv as valoriva, fencop as copago, fencmo as cuotamoderadora, Pactdo as ti, 
								Pacdoc as doc, Pacno1 as no1, Pacno2 as no2, Pacap1 as ap1, Pacap2 as ap2,
								Ingfei as fei, Ingord as orden, rcfreg as regcargo, tcarconcod as concepto, grudes as grupodes, 
								tcarfec as fechacargo, tcarprocod as procedimiento, pronom as nomprocedimiento, tcartercod, tcarcan as cantidad, 
								tcarvun as valorunitario, tcarvto as valortotal, tcarfex, tcarfre, rcfval as valorfacturado, 
								tcarusu, proemppro as procedimientoempresa, proempnom as nomprocedimientoempresa, Egrfee as fechaegr, 0 as poriva, 
								diacod as diagnos, d.Hora_data as hora, tcardev as devolucion, Egrfia as fechainiciotratamiento
						   FROM ".$wbasedato."_000018 a LEFT JOIN ".$wbasedato."_000108 n ON(a.fenhis=n.egrhis AND a.fening=n.egring)  LEFT JOIN ".$wbasedato."_000109 xx ON(a.fenhis=xx.diahis AND a.fening=xx.diaing AND xx.diatip='P'), ".$wbasedato."_000100 b, ".$wbasedato."_000101 c,".$wbasedato."_000066 e, ".$tablaConceptos." f, ".$wbasedato."_000103 g, ".$wbasedato."_000106 d
						   LEFT JOIN ".$wbasedato."_000070 h ON (proempcod = d.tcarprocod AND proempemp = '".$wemp_pmla."' AND proempest = 'on')
						  WHERE fenfac IN (".implode(",",$arr_facturas).")
							AND Inghis = fenhis
							AND Ingnin = fening
							AND Pachis = Inghis
							AND rcfffa = fenffa
							AND rcffac = fenfac
							AND rcfreg = d.id 
							AND tcarconcod  = grucod 
							AND grutab != 'on' 
							AND tcarprocod = procod
							AND proest = 'on' 
							AND fencod = '".$wempresa."' 
							GROUP BY 1,2,3,4,5,7,8,24,26,27,30,31,32,35,36
						UNION
						 SELECT fenffa as fuente, fenfac as factura, fenfec as fecha, fenval as valor, fensal as saldo, 
								fenesf as estadofac, fenhis as historia, fening as ingreso, fenest as estado, fennpa as nombrepac, 
								fendpa as docpac, fenviv as valoriva, fencop as copago, fencmo as cuotamoderadora, Pactdo as ti, 
								Pacdoc as doc, Pacno1 as no1, Pacno2 as no2, Pacap1 as ap1, Pacap2 as ap2,
								Ingfei as fei, Ingord as orden, rcfreg as regcargo, tcarconcod as concepto, grudes as grupodes, 
								tcarfec as fechacargo, tcarprocod as procedimiento, artnom as nomprocedimiento, tcartercod, tcarcan as cantidad, 
								tcarvun as valorunitario, tcarvto as valortotal, tcarfex, tcarfre, rcfval as valorfacturado, 
								tcarusu, proemppro as procedimientoempresa, proempnom as nomprocedimientoempresa, Egrfee as fechaegr, artiva as poriva, 
								diacod as diagnos, d.Hora_data as hora, tcardev as devolucion, Egrfia as fechainiciotratamiento
						   FROM ".$wbasedato."_000018 a LEFT JOIN ".$wbasedato."_000108 n ON(a.fenhis=n.egrhis AND a.fening=n.egring) LEFT JOIN ".$wbasedato."_000109 xx ON(a.fenhis=xx.diahis AND a.fening=xx.diaing AND xx.diatip='P'), ".$wbasedato."_000100 b, ".$wbasedato."_000101 c, ".$wbasedato."_000106 d, ".$wbasedato."_000066 e, ".$tablaConceptos." f, ".$wbasedato."_000001 g
						   LEFT JOIN ".$wbasedato."_000070 h ON (proempcod = g.artcod AND proempemp = '".$wemp_pmla."' AND proempest = 'on')
						  WHERE fenfac IN (".implode(",",$arr_facturas).")
							AND Inghis = fenhis
							AND Ingnin = fening
							AND Pachis = Inghis
							AND rcfffa = fenffa
							AND rcffac = fenfac
							AND rcfreg = d.id 
							AND tcarconcod  = grucod 
							AND grutab != 'on' 
							AND tcarprocod = artcod 
							AND artest = 'on'
							AND fencod = '".$wempresa."' 
							GROUP BY 1,2,3,4,5,6,7,8,9,26,42,27,30,31,32,35";				
			}
			else             //Facturacion POS
			{
					$q = "   SELECT fenffa as fuente, fenfac as factura, fenfec as fecha, fenval as valor, 
								fensal as saldo, fenesf as estadofac, fenhis as historia, fening as ingreso, fenest as estado, 
								fennpa as nombrepac, fendpa as docpac, fenviv as valoriva, fencop as copago, fencmo as cuotamoderadora,
								Pactdo as ti, Pacdoc as doc, Pacno1 as no1, Pacno2 as no2, Pacap1 as ap1, Pacap2 as ap2,
								Ingfei as fei, Ingord as orden,
								rcfreg as regcargo, artgru as concepto, grudes as grupodes, d.fecha_data  as fechacargo, artcod as procedimiento, artnom as nomprocedimiento, '', vdecan as cantidad, vdevun as valorunitario, vdecan*vdevun as valortotal, 0, 0, rcfval as valorfacturado, d.seguridad ,
								proemppro as procedimientoempresa, proempnom as nomprocedimientoempresa, Egrfee as fechaegr, artiva as poriva, diacod as diagnos, d.Hora_data as hora
								, tcardev as devolucion, Egrfia as fechainiciotratamiento
						   FROM ".$wbasedato."_000018 a LEFT JOIN ".$wbasedato."_000108 n ON(a.fenhis=n.egrhis AND a.fening=n.egring) LEFT JOIN ".$wbasedato."_000109 xx ON(a.fenhis=xx.diahis AND a.fening=xx.diaing AND xx.diatip='P'), ".$wbasedato."_000100 b, ".$wbasedato."_000101 c, ".$wbasedato."_000017 d, ".$wbasedato."_000066 e, ".$wbasedato."_000004 f, ".$wbasedato."_000001 g
						   LEFT JOIN ".$wbasedato."_000070 h ON (proempcod = g.artcod AND proempemp = '".$wemp_pmla."' AND proempest = 'on')
						  WHERE fenfac IN (".implode(",",$arr_facturas).")
							AND Inghis = fenhis
							AND Ingnin = fening						
							AND Pachis = Inghis
							AND rcfffa = fenffa
							AND rcffac = fenfac
							AND rcfreg = d.id 
							AND vdeart = artcod 
							AND mid(artgru,1,instr(artgru,'-')-1) = grucod 
							AND grutab != 'on'
							AND fencod = '".$wempresa."' 
							GROUP BY 1,2,3,4,5,6,7,8,9,26,42,27,30,31,32,35";
			}
				
			//---------------------------------------------------------------------------
			
			$array_datos = array();			

			$resx = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			$num = mysql_num_rows($resx);	
			
			
			
			if ($num > 0)
			{
				while( $row = mysql_fetch_array($resx) ){
					if( array_key_exists( $row['fuente']."|".$row['factura'], $array_datos ) == false ){
						$array_datos[$row['fuente']."|".$row['factura']] = array();
						$array_datos[$row['fuente']."|".$row['factura']]['fuente'] = $row['fuente'];
						$array_datos[$row['fuente']."|".$row['factura']]['factura'] = $row['factura'];
						$array_datos[$row['fuente']."|".$row['factura']]['fecha'] = $row['fecha'];
						$array_datos[$row['fuente']."|".$row['factura']]['valor'] = $row['valor'];
						$array_datos[$row['fuente']."|".$row['factura']]['cuotamoderadora'] = $row['cuotamoderadora'];
						$array_datos[$row['fuente']."|".$row['factura']]['valoriva'] = $row['valoriva'];
						$array_datos[$row['fuente']."|".$row['factura']]['no1'] = $row['no1'];
						$array_datos[$row['fuente']."|".$row['factura']]['no2'] = $row['no2'];
						$array_datos[$row['fuente']."|".$row['factura']]['ap1'] = $row['ap1'];
						$array_datos[$row['fuente']."|".$row['factura']]['ap2'] = $row['ap2'];
						
						
						//La fecha de ingreso se asume como la fecha de la tabla 101, pero, si la fecha de inicio del
						//tratamiento existe se considera como la fecha de ingreso segun solicitud de clinica del sur						
						$array_datos[$row['fuente']."|".$row['factura']]['fei'] = $row['fei'];
						if( $row['fechainiciotratamiento'] != "" &&  $row['fechainiciotratamiento'] != "0000-00-00" )
							$array_datos[$row['fuente']."|".$row['factura']]['fei'] = $row['fechainiciotratamiento'];
							
							
						$array_datos[$row['fuente']."|".$row['factura']]['fechaegr'] = $row['fechaegr'];
						$array_datos[$row['fuente']."|".$row['factura']]['ti'] = $row['ti'];
						$array_datos[$row['fuente']."|".$row['factura']]['doc'] = $row['doc'];
						$array_datos[$row['fuente']."|".$row['factura']]['orden'] = $row['orden'];
						$array_datos[$row['fuente']."|".$row['factura']]['copago'] = $row['copago'];
						$array_datos[$row['fuente']."|".$row['factura']]['detalle'] = array();
					}
					/*if( array_key_exists( $row['concepto'], $array_datos[$row['fuente']."|".$row['factura']]['detalle'] ) == false ){
						$array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['concepto']] = array();
					}
					
					$filadetalle=array( 'concepto'=>$row['concepto'], 'grupodes'=>$row['grupodes'], 'procedimientoempresa'=>$row['procedimientoempresa'],
										'nomprocedimientoempresa'=>$row['nomprocedimientoempresa'], 'procedimiento'=>$row['procedimiento'], 'nomprocedimiento'=>$row['nomprocedimiento'],
										'valorfacturado'=>$row['valorfacturado'], 'cantidad'=>$row['cantidad'], 'valorunitario'=>$row['valorunitario'], 'diagnos'=>$row['diagnos'], 'poriva'=>$row['poriva'], 'valortotal'=>$row['valortotal'] );

					array_push( $array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['concepto']], $row );
					*/
					
					if( array_key_exists( $row['procedimiento'], $array_datos[$row['fuente']."|".$row['factura']]['detalle'] ) == false ){
						$array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['procedimiento']] = array();
						
						$filadetalle=array( 'concepto'=>$row['concepto'], 'grupodes'=>$row['grupodes'], 'procedimientoempresa'=>$row['procedimientoempresa'],
										'nomprocedimientoempresa'=>$row['nomprocedimientoempresa'], 'procedimiento'=>$row['procedimiento'], 'nomprocedimiento'=>$row['nomprocedimiento'],
										'valorfacturado'=>$row['valorfacturado'], 'cantidad'=>$row['cantidad'], 'valorunitario'=>$row['valorunitario'], 'diagnos'=>$row['diagnos'], 'poriva'=>$row['poriva'], 'valortotal'=>$row['valortotal'] );

						 $array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['procedimiento']] = $filadetalle;
					}else{
						if( $row['devolucion'] == "on" ){
							$array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['procedimiento']]['cantidad']-=$row['cantidad'];
							// $array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['procedimiento']]['valorfacturado']-=$row['valorfacturado'];
							$array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['procedimiento']]['valorfacturado']+=$row['valorfacturado'];
						}else{
							$array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['procedimiento']]['cantidad']+=$row['cantidad'];
							$array_datos[$row['fuente']."|".$row['factura']]['detalle'][$row['procedimiento']]['valorfacturado']+=$row['valorfacturado'];
						}						
					}
				}
			}
			
			$cantDetalles=0;
			if($num > 0)
			{
				foreach( $array_datos as $fuefackey => $wdato )
				{
					$cantDetalles+=count($wdato['detalle']);
				}
								
				/***********************************/
				/* CREAR ENCABEZADO - IMPRIMIR
				/***********************************/
				
				$numRegsEnviados = $cantDetalles; //6
				
				$numRegsEnviados = formatearCadena( $numRegsEnviados, "n", 6 ); //6
				
				
				//IMPRIMIR LINEA
				$linea = $tiporeg.$sep.$numRegsEnviados.$sep.$numFacsEnviados.$sep.$fechaEnvio.$sep.$codigoSucursalIPS.$sep.$tipoDocIPS.$sep.$nitIPS.$sep.$digitoVerificadorIPS.$sep.$nombreIPS.$sep.$tipoDocumentoEPS.$sep.$nitEPS.$sep.$digitoVerificadorEPS.$sep.$nombreEPS;
				fwrite($myfile, $linea.$saltoLineaArchivo);

			}
			
			if ($num > 0)
			{
				//Va entre dos corchetes para que el regex en javascript identifique el nombre del archivo y cree en el enlace correctamente
				echo "[[".$nitIPSsf.$digitoVerificadorIPS."-".$consecutivoEmpresa.".txt]]"; //Se agrega digito de verificaci�n
				// echo "[[".$nitIPSsf."-".$consecutivoEmpresa.".txt]]"; //Sin digito de verificaci�n
				aumentarConsecutivo("consecutivoFAC-".$wempresa);
				
				foreach( $array_datos as $fuefackey => $wdato )
				{
					//echo "<br><b>Factura: ".$wdato['factura']."</b>";
					$existe = false;
					foreach($wdatos as $wfac ){
						if( $wfac['fac'] == $wdato['factura'] && $wfac['fue'] == $wdato['fuente'] ){
							$existe = true;
						}
					}
					if( $existe == false ){
						//echo "NO EXISTE EN EL ARREGLO";
						continue;
					}
					
					$wcem = $wempresa;
						echo "---->".$wdato['cuotamoderadora']."----".$wdato['copago']."<----";
					/***********************************/
					/*CREAR CABECERA
					/***********************************/
					$tiporeg = "1"; //1
					$plan = "0"; //1
					$fechaFactura = date_format(date_create($wdato['fecha']), 'd/m/Y'); //10
					$letrasFactura = preg_replace("/[^A-Za-z]/", "", $wdato['factura']); //6
					$digitosFactura = preg_replace("/[^0-9]/", "", $wdato['factura']); //10
					// $valorBruto = $wdato['valor']-$wdato['cuotamoderadora']-$wdato['copago']; //12
					$valorBruto = $wdato['valor']+$wdato['cuotamoderadora']+$wdato['copago']; //12
					$valorNeto = $wdato['valor']; //12
					$valorNetoLetras = montoescrito($wdato['valor']); //80
					$periodosCarencia = "0"; //10
					$cuotaModeradora = $wdato['cuotamoderadora']; //10
					$copago = $wdato['copago']; //10
					$tipoCuenta = "SERV"; //10
					$valorIVA = $wdato['valoriva']; //10
					$conceptoRetFuente = "0"; //1	


					$tiporeg = formatearCadena( $tiporeg, "n", 1 ); //1
					$plan = formatearCadena( $plan, "n", 1 ); //1
					$fechaFactura = formatearCadena( $fechaFactura, "c", 10 ); //10
					$letrasFactura = formatearCadena( $letrasFactura, "c", 6 ); //6
					$digitosFactura = formatearCadena( $digitosFactura, "n", 10 ); //10
					$valorBruto = formatearCadena( $valorBruto, "n", 12 ); //12
					$valorNeto = formatearCadena( $valorNeto, "n", 12 ); //12
					$valorNetoLetras = formatearCadena( $valorNetoLetras, "c", 80 ); //80
					$periodosCarencia = formatearCadena( $periodosCarencia, "n", 10 ); //10
					$cuotaModeradora = formatearCadena( $cuotaModeradora, "n", 10 ); //10
					$copago = formatearCadena( $copago, "n", 10 ); //10
					$tipoCuenta = formatearCadena( $tipoCuenta, "c", 10 ); //10
					$valorIVA = formatearCadena( $valorIVA, "n", 10 ); //10
					$conceptoRetFuente = formatearCadena( $conceptoRetFuente, "n", 1 ); //1	
					
					
					//IMPRIMIR LINEA
					$linea = $tiporeg.$sep.$plan.$sep.$fechaFactura.$sep.$letrasFactura.$sep.$digitosFactura.$sep.$valorBruto.$sep.$valorNeto.$sep.$valorNetoLetras.$sep.$periodosCarencia.$sep.$cuotaModeradora.$sep.$copago.$sep.$tipoCuenta.$sep.$valorIVA.$sep.$conceptoRetFuente;
					fwrite($myfile, $linea.$saltoLineaArchivo);
					
							
					/***********************************/
					/*CREAR DETALLE
					/***********************************/
					$tiporeg = "2"; //1
					$numeroFactura = preg_replace("/[^0-9]/", "", $wdato['factura']); //10
					$numOrdenAutorizacion = ""; //15
					$tiAfiliado = ""; //2
					$docAfiliado = ""; //12
					$apellidosAfiliado = ""; //25
					$nombresAfiliado = ""; //15
					$fechaIngAfiliado = ""; //10
					$fechaEgrAfiliado = ""; //10
					$tipoAtencion = "1"; //2
					$codigoAtencion = ""; //12
					$descripcionServicio = ""; //100
					$cantidadServicio = ""; //3
					$aplicaIva = ""; //1
					$valorUnitarioServicio = ""; //12
					$valorTotalServicio = ""; //12
					$codigoDiagnostico = ""; //7
					$totalCuotaModeradora = $cuotaModeradora; //12
					$totalCopago = $copago; //12
					
					
					
			
					$apellidosAfiliado = $wdato['ap1'];
					if( $wdato['ap2'] != "" && $wdato['ap2'] != "." )
						$apellidosAfiliado.= " ".$wdato['ap2'];
						
					$nombresAfiliado = $wdato['no1'];
					if( $wdato['no2'] != "" && $wdato['no2'] != "." )
						$nombresAfiliado.= " ".$wdato['no2'];
						
					$fechaIngAfiliado =	date_format(date_create($wdato['fei']), 'd/m/Y');
					if( $wdato['fechaegr'] != "" )
					$fechaEgrAfiliado =	date_format(date_create($wdato['fechaegr']), 'd/m/Y');
					
					$tiAfiliado = $wdato['ti'];
					$docAfiliado =	$wdato['doc'];
					
					$numOrdenAutorizacion = $wdato['orden'];	
					// echo count($wdato['detalle'])."----";
					if (count($wdato['detalle']) > 0)
					{
						 $j=1;

						 $wgtotgracon=0;
						 $wgtotfaccon=0;

						 //===========================================================
						 $wtiene_paq="off";
						

						if ($wtiene_paq == "off")
						{
							foreach( $wdato['detalle'] as $keyProcedimiento => $wDetalle )
							{
							
								//foreach( $arrConceptos as $wDetalle ){
									
									 $wtotgracon=0;
									 $wtotfaccon=0;

									if ($wDetalle['procedimientoempresa'] != "" )
									{								
										$wcodpro = $wDetalle['procedimientoempresa'];
										$wnompro = $wDetalle['nomprocedimientoempresa'];
									}
									else
									{
										$wcodpro = $wDetalle['procedimiento'];
										$wnompro = $wDetalle['nomprocedimiento'];
									}
									
									$codigoAtencion = $wcodpro;
									$descripcionServicio = $wnompro;
									$cantidadServicio = $wDetalle['cantidad'];
									$valorUnitarioServicio = $wDetalle['valorunitario'];
									$valorTotalServicio = $wDetalle['valorfacturado'];
									$codigoDiagnostico = $wDetalle['diagnos'];
									
									
									$aplicaIva = $wDetalle['poriva'];
									if( $aplicaIva != "" && array_key_exists( $aplicaIva, $arr_tipo_iva ) == true )
										$aplicaIva = $arr_tipo_iva[ $aplicaIva ];
									else 
										echo "NO existe ".$aplicaIva." en el arreglo";
									
									//if( $tiAfiliado != "" && array_key_exists( $tiAfiliado, $arr_tipos_identificacion ) == true )
									//	$tiAfiliado = $arr_tipos_identificacion[ $tiAfiliado ];
									
									$tiporeg = formatearCadena( $tiporeg, "n", 1 ); //1
									$numeroFactura = formatearCadena( $numeroFactura, "n", 10 ); //10
									$numOrdenAutorizacion = formatearCadena( $numOrdenAutorizacion, "n", 15 ); //15
									$tiAfiliado = formatearCadena( $tiAfiliado, "c", 2 ); //2
									$docAfiliado = formatearCadena( $docAfiliado, "c", 12 ); //12
									$apellidosAfiliado = formatearCadena( $apellidosAfiliado, "c", 25 ); //25
									$nombresAfiliado = formatearCadena( $nombresAfiliado, "c", 15 ); //15
									$fechaIngAfiliado = formatearCadena( $fechaIngAfiliado, "c", 10 ); //10
									$fechaEgrAfiliado = formatearCadena( $fechaEgrAfiliado, "c", 10 ); //10
									$tipoAtencion = formatearCadena( $tipoAtencion, "n", 2 ); //2
									$codigoAtencion = formatearCadena( $codigoAtencion, "c", 12 ); //12
									$descripcionServicio = formatearCadena( $descripcionServicio, "c", 100 ); //100									
									// $cantidadServicio = formatearCadena( $cantidadServicio, "n", 3, true ); //3
									$cantidadServicio = formatearCadena( $cantidadServicio, "n", 6, true ); //6
									$aplicaIva = formatearCadena( $aplicaIva, "n", 1 ); //1
									$valorUnitarioServicio = formatearCadena( $valorUnitarioServicio, "n", 12 ); //12
									$valorTotalServicio = formatearCadena( $valorTotalServicio, "n", 12 ); //12
									$codigoDiagnostico = formatearCadena( $codigoDiagnostico, "c", 7 ); //7
									$totalCuotaModeradora = formatearCadena( $totalCuotaModeradora, "n", 12 ); //12
									$totalCopago = formatearCadena( $totalCopago, "n", 12 ); //12													
									
									$linea = $tiporeg.$sep.$numeroFactura.$sep.$numOrdenAutorizacion.$sep.$tiAfiliado.$sep.$docAfiliado.$sep.$apellidosAfiliado.$sep.$nombresAfiliado.$sep.$fechaIngAfiliado.$sep.$fechaEgrAfiliado.$sep.$tipoAtencion.$sep.$codigoAtencion.$sep.$descripcionServicio.$sep.$cantidadServicio.$sep.$aplicaIva.$sep.$valorUnitarioServicio.$sep.$valorTotalServicio.$sep.$codigoDiagnostico.$sep.$totalCuotaModeradora.$sep.$totalCopago.$sep;
									fwrite($myfile, $linea.$saltoLineaArchivo);

																		
									 //Solo la primera puede tener ese valor
									 $totalCuotaModeradora = ""; 
									 $totalCopago = "";
								//}
							}
						}
						else
						{  //Si es PAQUETE entra por aca
							/*  $wtotal=0;

							   //2007-11-27
							   // para traer los conceptos que no pertenecen al paquete pero que estan en la factura
							  $q = " SELECT distinct(Tcarconcod), Grudes, fdeter, fdepte, fdevco, grutip"
								  ."   FROM ".$wbasedato."_000066, ".$wbasedato."_000106, ".$tablaConceptos.", ".$wbasedato."_000065"
								  ."  WHERE rcfffa    = '".$row['fuente']."'"
								  ."    AND rcffac    = '".$row['factura']."'"
								  ."    AND fdefue = rcfffa"
								  ."    AND fdedoc = rcffac"
								  ."	AND grucod = fdecon"
								  ."    AND ".$wbasedato."_000106.id =Rcfreg"
								  ."    AND Tcarconcod = Grucod"
								  ."    and  Tcartfa !='PAQUETE'"
								  ."    and  Tcartfa !='ABONO'"
								  ."    and  Tcarfac ='S' "
								  ."    AND Tcarest = 'on'"
								  ."    AND Rcfest ='on'"
								  ."    AND Fdeest ='on'"
								  ."    AND Gruabo='off'"
								  ."    AND Rcfreg  not in (SELECT Movpaqreg from ".$wbasedato."_000115 where Movpaqhis=".$whis." and Movpaqing=".$wing." and  Movpaqcod='".$row_paq[0]."' and Movpaqest='on')";
							  $res_reg = mysql_query($q,$conex);
							  $num_reg = mysql_num_rows($res_reg);

							  // entra solo si tiene conceptos que no pertenecen a la factura
							if ($num_reg>0)
							{
								//DETALLE DE LOS CONCEPTOS
								for ($i=1;$i<=$num_reg;$i++)
								{
									$row_reg = mysql_fetch_array($res_reg);

									 $q1 = "  SELECT rcfreg, tcarconcod, grudes, tcarfec, tcarprocod, tcarpronom, tcartercod, tcarcan, tcarvun, tcarvto, tcarfex, tcarfre, rcfval  as valorfacturado, tcarusu  "
										 ."    FROM ".$wbasedato."_000106, ".$tablaConceptos.", ".$wbasedato."_000066"
										 ."   WHERE tcarhis     = '".$whis."'"
										 ."     AND tcaring     = '".$wing."'"
										 ."     AND Tcarconcod  = '".$row_reg[0]."'"
										 ."     AND Tcartfa !='PAQUETE'"
										 ."     AND tcarfac = 'S' "
										 ."     AND tcarfre != 0 " // el query estaba < 0, debido a que era una devolucion, tenia que tenerla en cuenta 2010-12-23 (CS-161779)
										 ."     AND tcarconcod  = grucod "
										 ."     AND Tcarest = 'on'"
										 ."     AND rcfreg      = ".$wbasedato."_000106.id "
										 ."     AND rcffac      = '".$row['factura']."'" // se anexo esto porque estaba trayento todos los conceptos del ingreso 2010-12-21
										 ."   GROUP BY 1"
										 ."   ORDER BY 2, 4 ";
									 $res1 = mysql_query($q1,$conex);
									 $num1 = mysql_num_rows($res1);

									 $wtotgracon=0;
									 $wtotfaccon=0;
									for ($K=1;$K<=$num1;$K++)
									{
											$row1 = mysql_fetch_array($res1);
											
										//Aca busco si el procedimiento y la empresa tienen algun registro en la tabla relacion procedimeintos-empresas
										 //e imprimo con el codigo de la empresa.
										 $q = " SELECT proemppro, proempnom "
											 ."   FROM ".$wbasedato."_000070 "
											 ."  WHERE proempcod = '".$row1[4]."'"
											 ."    AND proempemp = '".$wcem."'"
											 ."    AND proempest = 'on' ";
										 $res2 = mysql_query($q,$conex);
										 $num2 = mysql_num_rows($res2);

										if ($num2 > 0)
										{
											 $row2 = mysql_fetch_array($res2);
											 $wcodpro = $row2[0];
											 $wnompro = $row2[1];
										}
										else
										{
											   $wcodpro = $row1[4];
											  $wnompro = $row1[5];
										}

										$codigoAtencion = $wcodpro;
										$descripcionServicio = $wnompro;
										$cantidadServicio = $row1[7];
										$valorUnitarioServicio = $row1[8];
										$valorTotalServicio = $row1[9];

										if( $tiAfiliado != "" && array_key_exists( $tiAfiliado, $arr_tipos_identificacion ) == true )
											$tiAfiliado = $arr_tipos_identificacion[ $tiAfiliado ];
										
										$tiporeg = formatearCadena( $tiporeg, "n", 1 ); //1
										$numeroFactura = formatearCadena( $numeroFactura, "n", 10 ); //10
										$numOrdenAutorizacion = formatearCadena( $numOrdenAutorizacion, "n", 15 ); //15
										$tiAfiliado = formatearCadena( $tiAfiliado, "c", 2 ); //2
										$docAfiliado = formatearCadena( $docAfiliado, "c", 12 ); //12
										$apellidosAfiliado = formatearCadena( $apellidosAfiliado, "c", 25 ); //25
										$nombresAfiliado = formatearCadena( $nombresAfiliado, "c", 15 ); //15
										$fechaIngAfiliado = formatearCadena( $fechaIngAfiliado, "c", 10 ); //10
										$fechaEgrAfiliado = formatearCadena( $fechaEgrAfiliado, "c", 10 ); //10
										$tipoAtencion = formatearCadena( $tipoAtencion, "n", 2 ); //2
										$codigoAtencion = formatearCadena( $codigoAtencion, "c", 12 ); //12
										$descripcionServicio = formatearCadena( $descripcionServicio, "c", 100 ); //100
										$cantidadServicio = formatearCadena( $cantidadServicio, "n", 6 ); //6
										$aplicaIva = formatearCadena( $aplicaIva, "n", 1 ); //1
										$valorUnitarioServicio = formatearCadena( $valorUnitarioServicio, "n", 12 ); //12
										$valorTotalServicio = formatearCadena( $valorTotalServicio, "n", 12 ); //12
										$codigoDiagnostico = formatearCadena( $codigoDiagnostico, "c", 7 ); //7
										$totalCuotaModeradora = formatearCadena( $totalCuotaModeradora, "n", 12 ); //12
										$totalCopago = formatearCadena( $totalCopago, "n", 12 ); //12	
									
										$linea = $tiporeg.$sep.$numeroFactura.$sep.$numOrdenAutorizacion.$sep.$tiAfiliado.$sep.$docAfiliado.$sep.$apellidosAfiliado.$sep.$nombresAfiliado.$sep.$fechaIngAfiliado.$sep.$fechaEgrAfiliado.$sep.$tipoAtencion.$sep.$codigoAtencion.$sep.$descripcionServicio.$sep.$cantidadServicio.$sep.$aplicaIva.$sep.$valorUnitarioServicio.$sep.$valorTotalServicio.$sep.$codigoDiagnostico.$sep.$totalCuotaModeradora.$sep.$totalCopago.$sep;
										fwrite($myfile, $linea.$saltoLineaArchivo);
					
										 $wtotgracon=$wtotgracon+$row1[9];
										 $wtotfaccon=$wtotfaccon+$row1[12];

										 $wgtotgracon=$wgtotgracon+$row1[9];
										 $wgtotfaccon=$wgtotfaccon+$row1[12];

									}
								   $wcodcon=$row['valorfacturado'];
								   $wnomcon=$row1[2];
								}
							}
								 //entra para sumar los conceptos que no pertenecen a la factura al total
							if ($num_reg>0)
							{
									$wtotal=$wtotal+$wgtotfaccon;
							}*/
						}
					}			
				}
			}
		}else{
			//$myfile = fopen("../../planos/NC-9003304160-23423.txt", "w") or die("Unable to open file!");
						
			
			
			
			
			/***********************************/
			/* CREAR ENCABEZADO
			/***********************************/
			$tiporeg = "0"; //1
			$numRegsEnviados = count($wdatos); //6
			$numNCsEnviados = count($wdatos); //6
			$fechaEnvio = date("d/m/Y"); //10
			$codigoSucursalIPS = "001"; //3
			$tipoDocIPS = "NI"; //2
			$nitIPS = ""; //15
			$digitoVerificadorIPS = ""; //1
			$nombreIPS = ""; //50
			$tipoDocumentoEPS = "NI"; //2
			$nitEPS = ""; //15
			$digitoVerificadorEPS = ""; //1
			$nombreEPS = ""; //50
			
			$q = " SELECT empdes as nombre, empnit as nit, emphos as hos
					 FROM root_000050 
					WHERE empcod = '".$wemp_pmla."'
			";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			$num = mysql_num_rows($res);	

			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				$facturaHos = $row['hos'];
				$aux = explode("-",$row['nit']);
				$nitIPS = $aux[0];
				if( isset($aux[1]) == true && $aux[1] != "" )
					$digitoVerificadorIPS = $aux[1];
				$nombreIPS = $row['nombre'];
			}
			if( $digitoVerificadorIPS == "" )
				$digitoVerificadorIPS = calcularDV($nitIPS);
				
			$consecutivoEmpresa = consultarAliasPorAplicacion($conex, $wemp_pmla, "consecutivoNC-".$wempresa);		
			// $myfile = fopen("../../planos/NC-".$nitIPS."-".$consecutivoEmpresa.".txt", "w") or die("Unable to open file!");
			$myfile = fopen("../../planos/NC-".$nitIPS.$digitoVerificadorIPS."-".$consecutivoEmpresa.".txt", "w") or die("Unable to open file!");
			$nitIPSsf = $nitIPS;
			$q = " SELECT empnom as nombre, empnit as nit, empdiv as digito
					 FROM ".$wbasedato."_000024
					WHERE empcod = '".$wempresa."'				
			";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			$num = mysql_num_rows($res);
			if ($num > 0){
				$row = mysql_fetch_array($res);
				$nitEPS = $row['nit'];
				$nombreEPS = $row['nombre'];
				$digitoVerificadorEPS = $row['digito'];
			}
			
			$tiporeg = formatearCadena( $tiporeg, "n", 1 ); //1
			$numRegsEnviados = formatearCadena( $numRegsEnviados, "n", 6 ); //6
			$numNCsEnviados = formatearCadena( $numNCsEnviados, "n", 6 ); //6
			$fechaEnvio = formatearCadena( $fechaEnvio, "c", 10 ); //10
			$codigoSucursalIPS = formatearCadena( $codigoSucursalIPS, "n", 3 ); //3
			$tipoDocIPS = formatearCadena( $tipoDocIPS, "c", 2 ); //2
			$nitIPS = formatearCadena( $nitIPS, "n", 15 ); //15
			$digitoVerificadorIPS = formatearCadena( $digitoVerificadorIPS, "n", 1 ); //1
			$nombreIPS = formatearCadena( $nombreIPS, "c", 50 ); //50
			$tipoDocumentoEPS = formatearCadena( $tipoDocumentoEPS, "c", 2 ); //2
			$nitEPS = formatearCadena( $nitEPS, "n", 15 ); //15
			$digitoVerificadorEPS = formatearCadena( $digitoVerificadorEPS, "n", 1 ); //1
			$nombreEPS = formatearCadena( $nombreEPS, "c", 50 ); //50
			
			//IMPRIMIR LINEA
			$linea = $tiporeg.$sep.$numRegsEnviados.$sep.$numNCsEnviados.$sep.$fechaEnvio.$sep.$codigoSucursalIPS.$sep.$tipoDocIPS.$sep.$nitIPS.$sep.$digitoVerificadorIPS.$sep.$nombreIPS.$sep.$tipoDocumentoEPS.$sep.$nitEPS.$sep.$digitoVerificadorEPS.$sep.$nombreEPS;
			fwrite($myfile, $linea.$saltoLineaArchivo);

			
			$arr_notascredito = array();
			foreach($wdatos as $wdato ){
				array_push( $arr_notascredito, "'".$wdato['fac']."'" ); //La clave es "fac" aunque en realidad, es el numero del documento
			}			
			
			//Se ejecuta el query igual al que muestra los resultados en la pantalla, y de esos resultados solo se trabaja con los
			//seleccionados, que estan en el arreglo wdatos, esto se hace asi para lograr utilizar los indices de la tabla 20
			//dato que buscar varias notas credito con fuente-numero no usa los indices y la consulta se tarda.
			$q = " SELECT carfue as fuentecar, fenffa as fuentefac, fenfac as factura, Renfec as fecha, fencod as entidad, fennit as nit, empnom as nombreent,  
						  fenval as valor, fensal as saldo, fenesf as estadofac, fenhis as historia, fening as ingreso, fenest as estado, fennpa as nombrepac, fendpa as docpac,
						  Renfue as fuente, Rennum numero, Renvca valor_cancelado, Rdevco valor_concepto, Renobs as obs
                     FROM ".$wbasedato."_000020 as c20, ".$wbasedato."_000021 as c21, ".$wbasedato."_000018 as a, ".$wbasedato."_000024 c, ".$wbasedato."_000040 as c40
					WHERE Renfec BETWEEN '".$wfeci."' AND '".$wfecf."'
					  AND Rennum = Rdenum
					  AND Renfue = Rdefue
					  AND Rencco = Rdecco
					  AND fenffa = Rdeffa					  
					  AND fenfac = Rdefac					 
					  AND Carfue = Rdefue
					  AND fencod = empcod
				      AND fencod = '".$wempresa."'
					  AND Rdeest ='on'
					  AND Carncr='on' ";	
						
			$array_datos = array();			

			$resx = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			$num = mysql_num_rows($resx);	

			if ($num > 0)
			{
				while( $row = mysql_fetch_array($resx) ){
					if( array_key_exists( $row['fuente']."|".$row['numero'], $array_datos ) == false ){
						$array_datos[$row['fuente']."|".$row['numero']] = array();
						$array_datos[$row['fuente']."|".$row['numero']]['fuente'] = $row['fuente'];
						$array_datos[$row['fuente']."|".$row['numero']]['numero'] = $row['numero'];						
						$array_datos[$row['fuente']."|".$row['numero']]['detalle'] = array();
					}
					array_push( $array_datos[$row['fuente']."|".$row['numero']]['detalle'], $row );
				}
			}
			
			if ($num > 0)
			{
				//Va entre dos corchetes para que el regex en javascript identifique el nombre del archivo y cree en el enlace correctamente
				
				echo "[[NC-".$nitIPSsf.$digitoVerificadorIPS."-".$consecutivoEmpresa.".txt]]";
				// echo "[[NC-".$nitIPSsf."-".$consecutivoEmpresa.".txt]]";
				aumentarConsecutivo("consecutivoNC-".$wempresa);
				
				foreach( $array_datos as $fuefackey => $wdatog ){
					$existe = false;
					foreach($wdatos as $wfac ){
						if( $wfac['fac'] == $wdatog['numero'] && $wfac['fue'] == $wdatog['fuente'] ){//La clave es "fac" aunque en realidad, es el numero del documento
							$existe = true;
						}
					}
					if( $existe == false ){
						continue; //No seguir ejecutando el codigo porque la nota credito no fue seleccionada con el checkbox
					}
					
					/***********************************/
					/*CREAR DETALLE
					/***********************************/
					foreach( $wdatog['detalle'] as $posx=>$wdato ){
						$tiporeg = "1"; //1					
						$letrasNC = preg_replace("/[^A-Za-z]/", "", $wdato['numero']); //6
						$digitosNC = preg_replace("/[^0-9]/", "", $wdato['numero']); //15
						$fechaNC = date_format(date_create($wdato['fecha']), 'd/m/Y'); //10					
						$letrasFactura = preg_replace("/[^A-Za-z]/", "", $wdato['factura']); //6
						$digitosFactura = preg_replace("/[^0-9]/", "", $wdato['factura']); //10
						$valorNC = $wdato['valor_cancelado']; //12

						$tiporeg = formatearCadena( $tiporeg, "n", 1 ); //1
						$letrasNC = formatearCadena( $letrasNC, "c", 6 ); //6
						// $digitosNC = formatearCadena( $digitosNC, "c", 15 ); //15
						$digitosNC = formatearCadena( $digitosNC, "n", 15 ); //15
						$fechaNC = formatearCadena( $fechaNC, "c", 10 ); //10
						$letrasFactura = formatearCadena( $letrasFactura, "c", 6 ); //6
						$digitosFactura = formatearCadena( $digitosFactura, "n", 10 ); //10
						$valorNC = formatearCadena( $valorNC, "n", 12 ); //12
						
						//IMPRIMIR LINEA
						$linea = $tiporeg.$sep.$letrasNC.$sep.$digitosNC.$sep.$fechaNC.$sep.$letrasFactura.$sep.$digitosFactura.$sep.$valorNC;
						fwrite($myfile, $linea.$saltoLineaArchivo);
					}
				}
			}
					
		
		}

		fclose($myfile);
		
	}
	
	function aumentarConsecutivo( $clave ){
		global $wemp_pmla;
		global $conex;
		$q = "UPDATE root_000051 SET Detval = Detval + 1 WHERE Detapl = '".$clave."' AND Detemp='".$wemp_pmla."'";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
	}
	/*
	En el archivo, cada variable debe tener un tamanio fijo, si es numerico se llena con ceros a la izquierda hasta alcanzar el tamanio adecuado.
	Si es texto se llena con espacios en blanco a la derecha hasta alcanzar el tamanio adecuado.
	Si su longitud es mayor a la indicada por tamanio, se recorta.
	*/
	function formatearCadena( $cadena, $tipo, $tamanio){
		if( strlen( $cadena ) > $tamanio ){
			$cadena = substr($cadena, 0, $tamanio);
		}
		if( $tipo == "n" ){			
			$aux = number_format((float)$cadena,0,'',''); //Sin decimales ni separadores de miles		
			$cadena = str_pad($aux."", $tamanio, "0", STR_PAD_LEFT);			
		}else
			$cadena = str_pad($cadena, $tamanio, " ", STR_PAD_RIGHT);
		
		return $cadena;
	}
	
	function generarReporte( $wfacturaonc, $wempresa, $wfuente='', $wfactura='', $whis='', $wing='', $wdoc='', $wfeci, $wfecf ){
		global $conex;
        global $wbasedato;
		global $wtcx;
		global $wmovhos;
		global $wemp_pmla;
		global $wusuario;
		
		$num = 0;
		$totalValor=0;
		$totalSaldo=0;
					
					
		if( $wfacturaonc == "FAC" ){
			/*$condiciones = "";
			if( $wfuente != "" && $wfactura != "" )
				$condiciones = " fenffa = '".$wfuente."' AND fenfac = '".$wfactura."' ";
			else if( $wfactura != "" )
				$condiciones = " fenfac = '".$wfactura."' ";
			else if( $whis != "" && $wing != "" )
				$condiciones = " fenhis = '".$whis."' AND fening = '".$wing."' ";
			else if( $whis != "" )
				$condiciones = " fenhis = '".$whis."' ";
			else if( $wdoc != "" )
				$condiciones = " fendpa = '".$wdoc."' ";
			else if( $wfeci != "" && $wfecf != "" )
				$condiciones = " fenfec BETWEEN '".$wfeci."' AND '".$wfecf."' ";

			if( $condiciones == "" ){
				echo "<br>Los parametros ingresados no son suficientes para realizar la busqueda.";
				exit;
			}*/
				
			$q = " SELECT fenffa as fuente, fenfac as factura, fenfec as fecha, fencod as entidad, fennit as nit, empnom as nombreent,  
						  fenval as valor, fensal as saldo, fenesf as estadofac, fenhis as historia, fening as ingreso, fenest as estado, fennpa as nombrepac, fendpa as docpac, egrhis as egresado"
			."   FROM ".$wbasedato."_000018 a LEFT JOIN ".$wbasedato."_000108 b ON(a.fenhis=b.egrhis AND a.fening=b.egring), ".$wbasedato."_000024 c" //, ".$wbasedato."_000100 "
			."  WHERE fenfec BETWEEN '".$wfeci."' AND '".$wfecf."' "
			."    AND fencod = empcod "
			."    AND fencod = '".$wempresa."' "
			."    GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13 "
			."    ORDER BY fenfec desc,fenffa,fenfac";				//Se actualiza el reporte para que se muestre en orden de fecha,
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			$num = mysql_num_rows($res);
		
			if ($num > 0)
			{
				echo "<center>";
				echo "<br<br><input type='button' value='Generar Archivo' onclick='crearArchivo()' /><br><br>";
				echo "</center>";
				echo "<br>";
				echo "<center><table border='0'>";

				echo "<tr><td colspan=14 class='fila2'><b>Cantidad de Facturas encontradas: ".$num."</b></td></tr>";

				echo "<tr class='encabezadoTabla'>";
				echo "<th>SELECCIONAR</th>";
				echo "<th>ESTADO<br>PACIENTE</th>";
				echo "<th>FUENTE</th>";
				echo "<th>FACTURA</th>";
				echo "<th>FECHA</th>";
				echo "<th>HISTORIA</th>";
				echo "<th>DOCUMENTO</th>";
				echo "<th>PACIENTE</th>";
				echo "<th>RESPONSABLE</th>";
				echo "<th>VALOR</th>";
				echo "<th>SALDO</th>";
				echo "<th>ESTADO CARTERA</th>";
				echo "<th>ESTADO FACTURA</th>";
				echo "<th>&nbsp</th>";
				echo "</tr>";

				$wver="";
				
				$totalValor=0;
				$totalSaldo=0;

				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);

					if ($i%2==0)
					$wclass="fila1";
					else
					$wclass="fila2";
					
					$westadopac = "Egresado";
					if( $row['egresado'] == "" )
						$westadopac = "<div class='fondorojo'>SIN egresar</span>";

					$westcar = '';
					//Le coloco nombre a los estados de la factura en cartera
					switch ($row['estadofac'])
					{
					case "GE":
					{ $westcar="GENERADA"; }
					break;
					case "EV":
					{ $westcar="ENVIADA"; }
					break;
					case "RD":
					{ $westcar="RADICADA"; }
					break;
					case "DV":
					{ $westcar="DEVUELTA"; }
					break;
					case "GL":
					{ $westcar="GLOSADA"; }
					}

					//Le coloco nombre a los estados de la factura en el archivo
					switch ($row['estado'])
					{
					case "on":
					{ $westreg="ACTIVA*"; }
					BREAK;
					case "off":
					{ $westreg="ANULADA"; }
					BREAK;
					}

					echo "<tr class='filafactura ".$wclass."'>";
					//echo "<td class='".$wclass."' align=center><A href='Consultar_Factura.php?wffue=".$row['fuente']."&wffac=".$row[1]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&$wver=on&wfue=".convertir_url($wfue)."&wfac=".convertir_url($wfac)."&whis=".convertir_url($whis)."&wnom=".convertir_url($wnom)."&wdoc=".convertir_url($wdoc)."'> Ver</A></td>";
					echo "<td align='center'><input type='checkbox' onclick='elegirFactura(this)' class='elegirfactura' value='".$row['fuente']."||".$row['factura']."' fuente='".$row['fuente']."' factura='".$row['factura']."' checked/></td>";
					echo "<td align='center'>".$westadopac."</td>";
					echo "<td>".$row['fuente']."</td>";
					echo "<td>".$row['factura']."</td>";
					echo "<td>".$row['fecha']."</td>";
					echo "<td>".$row['historia']."-".$row['ingreso']."</td>";
					echo "<td>".$row['docpac']."</td>";
					echo "<td>".$row['nombrepac']."</td>";
					echo "<td>".$row['nit']." ".$row['nombreent']."</td>";
					echo "<td class='valor' align=right>".number_format($row['valor'],0,'.',',')."</td>";
					echo "<td class='saldo' align=right>".number_format($row['saldo'],0,'.',',')."</td>";
					echo "<td align=center><b>".$westcar."</b></td>";
					echo "<td align=center><b>".$westreg."</b></td>";
					//echo "<td align=center><A href='Consultar_Factura.php?wffue=".$row['fuente']."&wffac=".$row['factura']."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&$wver=on&wfue=".$wfuente."&wfac=".$wfactura."&whis=".$whis."&wdoc=".$wdoc."'> Ver</A></td>";
					echo "<td align=center><A onclick='consultarFactura(this, \"".$row['fuente']."\", \"".$row['factura']."\")' href='#'>Ver</A></td>";
					echo "</tr>";
					echo "<tr class='detalle' style='display:none;'><td colspan=14>&nbsp;</td></tr>";
					
					$totalValor+=$row['valor'];
					$totalSaldo+=$row['saldo'];
				}

			}else{
				echo "<br>No se encontraron datos con los parametros ingresados.";			
			}
		}else{
				 
			$q = " SELECT carfue as fuentecar, fenffa as fuentefac, fenfac as factura, Renfec as fecha, fencod as entidad, fennit as nit, empnom as nombreent,  
						  fenval as valor, fensal as saldo, fenesf as estadofac, fenhis as historia, fening as ingreso, fenest as estado, fennpa as nombrepac, fendpa as docpac, egrhis as egresado,
						  Renfue as fuente, Rennum numero, Renvca valor_cancelado, Rdevco valor_concepto, Renobs as obs
                     FROM ".$wbasedato."_000020 as c20, ".$wbasedato."_000021 as c21, ".$wbasedato."_000018 as a LEFT JOIN ".$wbasedato."_000108 as b ON(a.fenhis=b.egrhis AND a.fening=b.egring), ".$wbasedato."_000024 c, ".$wbasedato."_000040 as c40
					WHERE Renfec BETWEEN '".$wfeci."' AND '".$wfecf."'
					  AND Rennum = Rdenum
					  AND Renfue = Rdefue
					  AND Rencco = Rdecco
					  AND fenffa = Rdeffa					  
					  AND fenfac = Rdefac					 
					  AND Carfue = Rdefue
					  AND fencod = empcod
				      AND fencod = '".$wempresa."'
					  AND Rdeest ='on'
					  AND Carncr='on' ";		
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			if ($num > 0)
			{
				echo "<center>";
				echo "<br<br><input type='button' value='Generar Archivo' onclick='crearArchivo()' /><br><br>";
				echo "</center>";
				echo "<br>";
				echo "<center><table border='0'>";

				echo "<tr><td colspan=15 class='fila2'><b>Cantidad de Notas Cr�dito encontradas: ".$num."</b></td></tr>";

				echo "<tr class='encabezadoTabla'>";
				echo "<th>SELECCIONAR</th>";
				echo "<th>ESTADO</th>";
				echo "<th>FUENTE</th>";
				echo "<th>DOCUMENTO</th>";
				echo "<th>FACTURA</th>";
				echo "<th>FECHA</th>";
				echo "<th>HISTORIA</th>";
				echo "<th>DOCUMENTO</th>";
				echo "<th>PACIENTE</th>";
				echo "<th>RESPONSABLE</th>";
				echo "<th>VALOR<br>CANCELADO</th>";
				echo "<th>VALOR<BR>CONCEPTO</th>";
				echo "<th>ESTADO CARTERA</th>";
				echo "<th>ESTADO FACTURA</th>";
				echo "<th>OBSERVACION</th>";				
				echo "</tr>";

				$wver="";
				
				$totalValor=0;
				$totalSaldo=0;

				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);

					if ($i%2==0)
					$wclass="fila1";
					else
					$wclass="fila2";
					
					$westadopac = "Egresado";
					if( $row['egresado'] == "" )
						$westadopac = "<div class='fondorojo'>SIN egresar</span>";

					$westcar = '';
					//Le coloco nombre a los estados de la factura en cartera
					switch ($row['estadofac'])
					{
					case "GE":
					{ $westcar="GENERADA"; }
					break;
					case "EV":
					{ $westcar="ENVIADA"; }
					break;
					case "RD":
					{ $westcar="RADICADA"; }
					break;
					case "DV":
					{ $westcar="DEVUELTA"; }
					break;
					case "GL":
					{ $westcar="GLOSADA"; }
					}

					//Le coloco nombre a los estados de la factura en el archivo
					switch ($row['estado'])
					{
					case "on":
					{ $westreg="ACTIVA*"; }
					BREAK;
					case "off":
					{ $westreg="ANULADA"; }
					BREAK;
					}				
				
					//En el checkbox, se pone atributo fuente y factura, aunque el ultimo atributo en realidad es "numero", se deja asi para no cambiar funciones javascript
					echo "<tr class='filafactura ".$wclass."'>";					
					echo "<td align='center'><input type='checkbox' onclick='elegirFactura(this)' class='elegirfactura' value='".$row['fuente']."||".$row['numero']."' fuente='".$row['fuente']."' factura='".$row['numero']."' checked/></td>";
					echo "<td align='center'>".$westadopac."</td>";
					echo "<td>".$row['fuente']."</td>";
					echo "<td>".$row['numero']."</td>";
					echo "<td>".$row['fuentefac']."/".$row['factura']."</td>";
					echo "<td>".$row['fecha']."</td>";
					echo "<td>".$row['historia']."-".$row['ingreso']."</td>";
					echo "<td>".$row['docpac']."</td>";
					echo "<td>".$row['nombrepac']."</td>";
					echo "<td>".$row['nit']." ".$row['nombreent']."</td>";
					echo "<td class='valor' align=right>".number_format($row['valor_cancelado'],0,'.',',')."</td>";
					echo "<td class='saldo' align=right>".number_format($row['valor_concepto'],0,'.',',')."</td>";
					echo "<td align=center><b>".$westcar."</b></td>";
					echo "<td align=center><b>".$westreg."</b></td>";
					echo "<td align=center>".$row['obs']."</td>";
					echo "</tr>";
					echo "<tr class='detalle' style='display:none;'><td colspan=14>&nbsp;</td></tr>";
					
					$totalValor+=$row['valor'];
					$totalSaldo+=$row['saldo'];
				}
			}else{
				echo "<br>No se encontraron datos con los parametros ingresados.";			
			}
		}
		
		if( $num > 0 ){
			$cols = 9;
			if( $wfacturaonc == "FAC" )
				$cols=8;
			echo "<tr class='encabezadotabla'>";
			echo "<td align='center' nowrap>Elegir todas<br><input type='checkbox' onclick='seleccionarTodas(this)' checked/></td>";
			echo "<td colspan={$cols}>&nbsp;</td>";
			echo "<td id='totalValor'>".number_format($totalValor,0,'.',',')."</td>";
			echo "<td id='totalSaldo'>".number_format($totalSaldo,0,'.',',')."</td>";
			echo "<td colspan=3>&nbsp;</td>";
			echo "</tr>";
			
			echo "</table>";
			echo "<br>";
			echo "<center>";
			echo "<br<br><input type='button' value='Generar Archivo' onclick='crearArchivo()' /><br><br>";
			echo "</center>";
			echo "<input type='hidden' id='empresaHidden' value='".$wempresa."' />";
			echo "<input type='hidden' id='wfacturaonc' value='".$wfacturaonc."' />";
			echo "<input type='hidden' id='wfeci' value='".$wfeci."' />";
			echo "<input type='hidden' id='wfecf' value='".$wfecf."' />";
		}
	}
	
	function traerDatosFactura($wfue, $wfac){
		global $conex;
        global $wbasedato;
		global $wtcx;
		global $wmovhos;
		global $wemp_pmla;
		global $wusuario;
		global $nuevaFacturacion;
		
		$arr_datos = array();
		//---------------------------------------------------------------------------------------------
		// --> 	MAESTRO DE CONCEPTOS:
		//		- Antigua facturacion 	--> 000004
		//		- Nueva facturacion 	--> 000200
		//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
		//		de conceptos cambiara por la tabla 000200.
		//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
		//----------------------------------------------------------------------------------------------
		$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
		
		$q = "  SELECT fdecco as cco, ccodes as cconom, fdecon as concepto, grudes as grunom, fdeter as tercero, mednom as mednom, fdepte as portercero, fdevco as valorconcepto, fdevde as valordcto, fdeest as estadodetalle, medest as estado "
			."    FROM ".$wbasedato."_000065, ".$wbasedato."_000003, ".$tablaConceptos.", ".$wbasedato."_000051 "
			."   WHERE fdefue = '".$wfue."'"
			."     AND fdedoc = '".$wfac."'"
			."     AND fdecco = ccocod "
			."     AND fdecon = grucod "
			."     AND fdeter != '' "
			."     AND fdeter != ' ' "
			."     AND fdeter != 'NO APLICA' "
			."     AND fdeter != '0' "
			."     AND fdeter = meddoc "

			."   UNION "

			."  SELECT fdecco as cco, ccodes as cconom, fdecon as concepto, grudes as grunom, fdeter as tercero, '' as mednom, fdepte as portercero, fdevco as valorconcepto, fdevde as valordcto, fdeest as estadodetalle, '' as estado"
			."    FROM ".$wbasedato."_000065, ".$wbasedato."_000003, ".$tablaConceptos." "
			."   WHERE fdefue = '".$wfue."'"
			."     AND fdedoc = '".$wfac."'"
			."     AND fdecco = ccocod "
			."     AND fdecon = grucod "
			."     AND (fdeter = '' "
			."      OR  fdeter = ' ' "
			."      OR  fdeter = 'NO APLICA' "
			."      OR  fdeter = '0' ) ";
		$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num1 = mysql_num_rows($res1);
		
		if( $num1 > 0 ){
			while( $row = mysql_fetch_array($res1) ){
				array_push( $arr_datos, $row );
			}
		}
		
		return $arr_datos;
	}
	
	function consultarFactura( $wfue, $wfac ){
		global $conex;
        global $wbasedato;
		global $wtcx;
		global $wmovhos;
		global $wemp_pmla;
		global $wusuario;

		$doctores = array();
		$query = "SELECT meddoc
		FROM {$wbasedato}_000051";
		$rs    = mysql_query( $query, $conex );
		
		

		while( $rowmed = mysql_fetch_array( $rs ) ){
		( !isset( $doctores[$rowmed['meddoc']]) ) ? $doctores[$rowmed['meddoc']] = 1 : $doctores[$rowmed['meddoc']] ++;
		}
		$doctores['0'] = 1; //artificio para que funcione normal en uvglobal quienes tienen cargos al documento 0
		$doctores[''] = 1; //artificio para que funcione normal en uvglobal quienes tienen cargos al documento 0


		$res1 = traerDatosFactura($wfue, $wfac);
				
			echo "<br>";
			echo "<center><table border='0'>";
			echo "<tr class='encabezadoTabla'>";
			echo "<th colspan=2>CONCEPTO</th>";
			echo "<th colspan=2>CENTRO DE COSTO</th>";
			echo "<th colspan=3>TERCERO</th>";
			echo "<th colspan=1>VALOR BRUTO</th>";
			echo "<th colspan=1>DESCUENTO</th>";
			echo "<th colspan=1>VALOR NETO</th>";
			echo "</tr>";

			$wtotcon=0;
			$wtotdes=0;
			$j=1;
			foreach( $res1 as $row1 ){
				$j++;
				( $row1[4]=="" or ( $doctores[$row1[4]]*1 == 1 ) ) ? $mostrarDirecto = true : $mostrarDirecto = false;
				( $doctores[$row1[4]]*1 > 1 ) ? $filtroMedico = true : $filtroMedico = false;
				if( ( $mostrarDirecto ) or ( $filtroMedico and ($row1['estado'] == 'on') ) )
				{
					if ($j%2==0)
						$wclass="fila1";
					else
						$wclass="fila2";
					echo "<tr>";
					echo "<td class='".$wclass."'>".$row1[2]."</td>";	 //CONCEPTO
					echo "<td class='".$wclass."'>".$row1[3]."</td>";     //CONCEPTO
					if ($row1[0] == "") 									 //PROCEDIMIENTO
					{
						echo "<td class='".$wclass."'>&nbsp</td>";
						echo "<td class='".$wclass."'>&nbsp</td>";
					}
					else
					{
						echo "<td class='".$wclass."'>".$row1[0]."</td>";
						echo "<td class='".$wclass."'>".$row1[1]."</td>";
					}
					if ($row1[4] == "")  									 //TERCERO
					{
						echo "<td class='".$wclass."'>&nbsp</td>";
						echo "<td class='".$wclass."'>&nbsp</td>";
						echo "<td class='".$wclass."'>&nbsp</td>";
					}
					else
					{
						echo "<td class='".$wclass."'>".$row1[4]."</td>";
						echo "<td class='".$wclass."'>".$row1[5]."</td>";
						echo "<td class='".$wclass."'>".$row1[6]."%</td>";
					}
					echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[7],0,'.',',')."</td>";
					echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[8],0,'.',',')."</td>";
					echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[7]-$row1[8],0,'.',',')."</td>";

					$wtotcon=$wtotcon+$row1[7];
					$wtotdes=$wtotdes+$row1[8];
				}
			}
			echo "<tr class='encabezadoTabla'>";
			echo "<td align=RIGHT colspan=7><b>Totales</b></td>";
			echo "<td align=RIGHT colspan=1><b>".number_format($wtotcon,0,'.',',')."</b></td>";
			echo "<td align=RIGHT colspan=1><b>".number_format($wtotdes,0,'.',',')."</b></td>";
			echo "<td align=RIGHT colspan=1><b>".number_format($wtotcon-$wtotdes,0,'.',',')."</b></td>";
			echo "</tr>";

			if (isset($whosp) and strtoupper($whosp)=="ON")
			{
				echo "<input type='hidden' id='fuenteFactura' value='".$wfue."'>";
				echo "<input type='hidden' id='numeroFactura' value='".$wfac."'>";

				$puedeAnular = "off";
				$queryAnular = " SELECT Peranu
				FROM {$wbasedato}_000081
				WHERE Perusu = '{$wuser}'
				AND Perfue = '{$wfue}'";
				$rsAnular   = mysql_query( $queryAnular, $conex );
				while( $rowAnular = mysql_fetch_array( $rsAnular ) ){
					$puedeAnular = $rowAnular['Peranu'];
				}
				$causas=consultarCausas( $west, $wfue, $wfac );
				if( $puedeAnular == "on" ){
					echo "<tr>";
					( $west == "on" ) ? $anularFactura = "<font style='cursor:pointer;' color='blue' size='2' onclick='anular()'> Anular Factura </font>" : $anularFactura = "";
					echo "<td class='fila1' colspan='10' align=center><b>{$anularFactura}</b></td>";
					echo "</tr>";
				}

				// FILAS PARA LA ANULACI�N DE FACTURAS.
				if($west == "on" and $puedeAnular == "on" ){
					$visibilidad = "display:none;";
					$textHabilitado = "";
				}else{
					$visibilidad = "";
					$textHabilitado = "disabled";
				}
				if( $puedeAnular == "on" ){
					echo "<tr id='tr_causas' style='{$visibilidad}'>";
					echo "<td colspan='10'></br><div align='center'>";
					echo pintarCausas( $causas, $west  );
					echo "</div>";
					echo "</br>";
					echo "<div align='center'><table border=0 width='85%'><tr><td class=encabezadoTabla align=center><b>OBSERVACION:</b> </td></tr><tr><td class='fila2' align=center><b><textarea {$textHabilitado} name='wobs' id='wobs' cols='80' rows='3'>{$wobservacion}</textarea></td></tr></table>";
					($west !=  "on") ? $botonVisible = "style='display:none'" : $botonVisible = "";
					echo "<br><input type='button' value='Anular' {$botonVisible} onclick='anularFactura( \"/matrix/ips/procesos/Consultar_factura.php?wfactura=".$wfue."-".$wfac."&amp;wemp_pmla=".$wemp_pmla."&amp;wbasedato=".$wbasedato."&amp;wffue=".$wfue."&amp;tablaConceptos=".$tablaConceptos."&amp;wffac=".$wfac."\");'>";
					echo "</div></br>";
					echo "</td>";
					echo "</tr>";
				}
			}


			echo "<tr>";
			$wimpfac="<A href='/matrix/ips/reportes/r003-imp_factura.php?wfactura=".$wfue."-".$wfac."&amp;wemp_pmla=".$wemp_pmla."&amp;wbasedato=".$wbasedato."' TARGET='_blank'> ";
			echo "<td class='fila1' colspan=5 align=center><b>".$wimpfac." Imprimir Factura</b></td>";
			$wdetfac="<A href='/matrix/ips/reportes/imp_det_factura.php?wfue=".$wfue."&wfac=".$wfac."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'> ";
			echo "<td class='fila1' colspan=10 align=center><b>".$wdetfac." Detalle de Cargos</b></td>";
			echo "</tr>";
		
		echo "</table>";
    
		
	}
	
	function calcularDV($nit) {		
		if (! is_numeric($nit)) {
			echo "NO ES NUMERICO";
			return false;
		}
	 
		$arr = array(1 => 3, 4 => 17, 7 => 29, 10 => 43, 13 => 59, 2 => 7, 5 => 19, 
		8 => 37, 11 => 47, 14 => 67, 3 => 13, 6 => 23, 9 => 41, 12 => 53, 15 => 71);
		$x = 0;
		$y = 0;
		$z = strlen($nit);
		$dv = '';
		
		for ($i=0; $i<$z; $i++) {
			$y = substr($nit, $i, 1);
			$x += ($y*$arr[$z-$i]);
		}
		
		$y = $x%11;
		
		if ($y > 1) {
			$dv = 11-$y;
			return $dv;
		} else {
			$dv = $y;
			return $dv;
		}
		
	}


	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla, $nitsArchivoIntercambio;
		global $wactualiz, $wbasedato, $wmovhos, $conex;
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		
		$logo="clinica";
		if($wemp_pmla != "01" )
			$logo = "logo_".$wbasedato;
		
		encabezado("ARCHIVO INTERCAMBIO NUEVA EPS", $wactualiz, $logo);

		echo '<div style="width: 100%">';
		$anio = date("Y");
		$anio--;
		$width_sel = " width: 80%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
			
		$nitsArchivoIntercambio = explode(",",$nitsArchivoIntercambio);
		$nitsArchivoIntercambio = array_map('trim',$nitsArchivoIntercambio);
		
		$arr_empresas_archivo = array();
		$q = " SELECT empnom as nombre, empcod as cod
				 FROM ".$wbasedato."_000024
				WHERE empcod IN ('".implode("','",$nitsArchivoIntercambio)."')
				group by 1,2
		";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
		$num = mysql_num_rows($res);	
		if ($num > 0){
			while($row = mysql_fetch_array($res)){
				array_push($arr_empresas_archivo, $row );
			}			
		}
		
		echo "<center>";
		 echo "<div align='center'><fieldset id='' style='padding:15px;width:800px'>";
		//------------TABLA DE PARAMETROS-------------		
		echo "<legend class='fieldset'>Par&aacute;metros de busqueda</legend>";
		echo "<div>";
		echo "<table border='0' style='width:60%;'>";
		echo "<tr>";
		echo "<td align=left width='10%' class='encabezadotabla' colspan=1><b>Empresa:</b></td>
			  <td width='40%' class='fila2'>
			 <select id='empresa'>				
				";
		foreach( $arr_empresas_archivo as $empresa ){
			echo "<option value='".$empresa['cod']."'>".$empresa['cod']."  -  ".$empresa['nombre']."</option>";
		}
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=left width='10%' class='encabezadotabla' colspan=1><b>Facturas/Notas Credito:</b></td>
			  <td width='40%' class='fila2' align='center'>
			 <select id='facturaonc'>";		
			echo "<option value='FAC'>FACTURAS</option>";
			echo "<option value='NC'>NOTAS CREDITO</option>";
		echo "</select>";
		echo "</td>";
		echo "</tr>";	
		/*echo "<tr>";
		echo "<td align=left width='10%' class='encabezadotabla' colspan=1><b>Fuente:</b></td>
			  <td width='40%' class='fila2'><INPUT TYPE='text' NAME='wfuente' id='wfuente'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=left width='10%' class='encabezadotabla' colspan=1><b> Factura:</b></td>
			  <td width='40%' class='fila2'><INPUT TYPE='text' NAME='wfac' id='wfac'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=left width='10%' class='encabezadotabla' colspan=1><b> Historia:</b></td>
			  <td width='40%' class='fila2'><INPUT TYPE='text' NAME='whis' id='whis'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=left width='10%' class='encabezadotabla' colspan=1><b> Ingreso:</b></td>
			  <td width='40%' class='fila2'><INPUT TYPE='text' NAME='wing' id='wing'></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=left class='encabezadotabla'><b>Documento de Identificaci�n: </b></td>
			  <td colspan='2' class='fila2'> <INPUT TYPE='text' NAME='wdoc' id='wdoc' SIZE=34 ></td>";
		echo "</tr>";*/	
		echo "<tr>";
		echo "<td align=left class='encabezadotabla'><b>Fecha Inicio:</b></td>";
		echo "<td class='fila2' align='center'>";
		
		//echo "<input type='text' id='fecha_inicio' value='2012-08-30' disabled placeholder=' '>";
		echo "<input type='text' id='fecha_inicio' value='".date("Y-m-d")."' disabled placeholder=' '>";
		echo "</td>";
		echo "</tr>";		
		echo "<tr>";
		echo "<td align=left class='encabezadotabla'><b>Fecha fin:</b></td>";
		echo "<td class='fila2' align='center'>";
		echo "<input type='text' id='fecha_fin' value='".date("Y-m-d")."' disabled placeholder=' '>";		
		echo "</td>";
		echo "</tr>";

		echo "<tr class='fila2'>";
		echo "<td colspan=2 align='center'>
		<br>
				<input type='button' value='Consultar' onclick='generarReporte()' />
			  </td>";
		echo "</tr>";
		echo "</table>";
		
		//------------FIN TABLA DE PARAMETROS-------------

		echo "</div>";//Gran contenedor
		echo "</div>";//Gran contenedor
	
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