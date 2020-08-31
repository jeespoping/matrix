<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Felipe Alvarez
//FECHA DE CREACION:
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
//--------------------------------------------------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	

	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");

	include_once("ips/funciones_facturacionERP.php");

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");
	//.include_once("ips/validacionTopesyParalelosERP.php");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-------------------------------------------------------------------------------------------------------
	//	Funcion que obtiene la informacion relacionada al grabador (Usuario) asi como sus dierentes permisos
	//-------------------------------------------------------------------------------------------------------
	function cargar_datos_caja()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wuse;

		$data = array();
		$q =  " SELECT Cjecco, Cjecaj, Cjetin, cjetem, cjeadm, cjebod, Descripcion, Cjecrc, Cjectc, Cjeprc, Cjesfc, Cjesre
				  FROM ".$wbasedato."_000030, usuarios
				 WHERE Cjeusu = '".$wuse."'
				   AND Cjeest = 'on'
				   AND Cjeusu = Codigo";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

		if ($row = mysql_fetch_array($res))
		{
			$pos 									= strpos($row['Cjecco'],"-");
			$data['wcco']    						= substr($row['Cjecco'],0,$pos);
			$data['wnomcco'] 						= substr($row['Cjecco'],$pos+1,strlen($row['Cjecco']));
			$data['wbod'] 	 						= $row['cjebod'];
			$pos 									= strpos($row['Cjecaj'],"-");
			$data['wcaja']   						= substr($row['Cjecaj'],0,$pos);
			$data['wnomcaj'] 						= substr($row['Cjecaj'],$pos+1,strlen($row['Cjecaj']));
			$data['wcajadm'] 						= $row['cjeadm'];
			$data['wtiping'] 						= $row['Cjetin'];
			$data['nomCajero'] 						= $row['Descripcion'];
			$data['cambiarResponsable'] 			= $row['Cjecrc'];
			$data['cambiarTarifa'] 					= $row['Cjectc'];
			$data['permiteRegrabar'] 				= $row['Cjeprc'];
			$data['permiteSeleccionarFacturable'] 	= $row['Cjesfc'];
			$data['permiteSeleccionarRecExc'] 		= $row['Cjesre'];
			$data['wtipcli'] 						= $row['cjetem'];
		}

		return $data;
	}


	function traer_mercados($whistoria,$wingreso,$wemp_pmla)
	{
		global $conex;
		$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

		$q = "SELECT Mpapro ,Pronom
				FROM  ".$wbasedato."_000207 , ".$wbasedato."_000103
				WHERE  Mpahis = '".$whistoria."'
				  AND  Mpaing = '".$wingreso."'
				  AND  Mpapro = Procod
				GROUP BY Mpapro";

		$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		$i=0;
		$data = array();
		while($row = mysql_fetch_array($res))
		{
			$i++;
			$data[$i]['codigo'] = $row['Mpapro'];
			$data[$i]['nombre'] = $row['Pronom'];
		}

		$q = "SELECT Mpapro, Paqnom
				FROM ".$wbasedato."_000207 , ".$wbasedato."_000113
				WHERE  Mpahis = '".$whistoria."'
				  AND  Mpaing = '".$wingreso."'
				  AND  Mpapro = Paqcod
				GROUP BY Mpapro";

		$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());



		while($row = mysql_fetch_array($res))
		{
			$i++;
			$data[$i]['codigo'] = $row['Mpapro'];
			$data[$i]['nombre'] = $row['Paqnom'];
		}


		return $data;


	}









//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================

if(isset($accion))
{

	switch($accion)
	{


		case 'cargar_datos_caja':
		{
			$data = cargar_datos_caja();
			echo json_encode($data);
			break;
			return;
		}

		case 'cargar_mercados':
		{

			$data = traer_mercados ($whistoria,$wingreso,$wemp_pmla);
			echo json_encode($data);
			break;
			return;
		}

		case 'cargar_datos':
		{
			$data = cargar_datos($whistoria, $wing, $wcargos_sin_facturar, $welemento);
			echo json_encode($data);
			break;
			return;
		}

		case 'horaFechaDelServidor':
		{
			$data['Hora']  = date("H:i");
			$data['Fecha'] = date("Y-m-d");
			echo json_encode($data);
			break;
		}

		case 'pintarcupsautorizados':
		{
			$dataCups = pintarCupsAutorizados($whistoria, $wing,$wcedula);
			if($dataCups['hayCups'])
			echo $dataCups['html'];
			break;
		}





	}

}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	?>
	<html>
	<head>
	  <title>Liquidación Pensión</title>
	</head>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<script src="../../../include/ips/funcionInsumosqxERP.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		<script src="../../../include/root/toJson.js" type="text/javascript"></script>
		<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	var url_add_params = addUrlCamposCompartidosTalento();
	var ArrayValores ;
	var ArrayValoresTerceros;
	var Arrayporcentajesterceros;

	$(document).ready(function() {


		// --> Crear variable compartidas para todo el gestor
		crear_variables_compartidas();
		cargar_datos_caja();
		cargar_datos('whistoria');


		$("#DatosPaciente").css( "display" , "block");

		$("#detalle_liquidacion_general").css( "display" , "block");

		// --> Cargar tooltips
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

		//obtener_array_permisos();


		// --> se carga el datepicker wfeccar
		cargar_elementos_datapicker();
		$("#wfeccar").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			maxDate:"+0D"
		});

		if($("#whistoria_tal").val()!='' && $("#whistoria").val()!='')
		{
			crear_div('grabarpaciente','divmercado','no', '',$("#responsable_original_tal").val(),$("#tarifa_original_tal").val(),'','','',$("#whistoria_tal").val(),$("#wing_tal").val(),tipoEmpresa,'off','');

		}
		cargar_procedimiento($('#whistoria').val() , $('#wing').val() );
		crear_autocomplete('hidden_paquetes', 'SI', 'busc_paquete', 'crear_div_procedimiento');
		//crear_autocomplete('hidden_', 'SI', 'busc_paquete', 'crear_div_procedimiento');

		// --> Actualizar la fecha y la hora desde el servidor
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'horaFechaDelServidor'
		}, function (data){
			$('#wfeccar').val(data.Fecha);
		}, 'json');

		// --> Cargar datos basicos del paciente

		$("#tableppal").show();

		pintarCupsAutorizados();



		$( "#accordionDatosPaciente" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});


		$("#accordionContenido").accordion("destroy");
		$("#accordionContenido" ).accordion({
			collapsible: true,
			heightStyle: "content",
			active: 0
		});





	});


	function pintarCupsAutorizados()
	{


		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'pintarcupsautorizados',
			whistoria:		   $('#whistoria').val(),
			wingreso:		   $('#wing_tal').val(),
			wcedula:			''

		},function(data){
			//alert(data);
			$("#procedimiento_autorizado").html(data);
			$("#busc_procedimiento").hide();

		});

	}



	function crear_div(operacion,div,modal, procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,ant_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa)
	{

		$.ajax(
		{
			url: "<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			context: document.body,
			type: "POST",
			data:
			{
				consultaAjax:      		'',
				wemp_pmla:         		$('#wemp_pmla').val(),
				accion:            		'cargar_mercados',
				whistoria:		   		$('#whistoria').val(),
				wingreso:				$('#wing_tal').val()
			},

				async: false,
				success:function(data) {

					var data = eval('(' + data + ')');

					var index = 0;
					var c_procedimiento;
					for (var datos in data)
					{
						index++;
						c_procedimiento = data[index].codigo;
						n_procedimiento = data[index].nombre;


						if( $("#div_"+c_procedimiento).length == 0)
						{
							$("#"+div).append("<br><div   id='acordion_"+c_procedimiento+"' class='divacordion1'   ><h3>"+n_procedimiento+"</h3><div id='div_"+c_procedimiento+"'  procedimiento='"+c_procedimiento+"'></div></div>");
							ventana_insumo(operacion,"div_"+c_procedimiento+"",modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,'','','');
						}


						/*$("#acordion_"+c_procedimiento ).accordion({
							collapsible: true,
							heightStyle: "content",
							active: 0
						});*/



					}

						 /*$("#accordionContenido").accordion("destroy");
						$("#accordionContenido" ).accordion({
									collapsible: true,
									heightStyle: "content",
									active: 0
								});*/





				}

		});
		// setTimeout(function(){
		 // $("#accordionContenido").accordion("destroy");
				// $("#accordionContenido" ).accordion({
							// collapsible: true,
							// heightStyle: "content",
							// active: 0
						// });

		// }, 100);


		// $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		// {
			// consultaAjax:      		'',
			// wemp_pmla:         		$('#wemp_pmla').val(),
			// accion:            		'cargar_mercados',
			// whistoria:		   		$('#whistoria').val(),
			// wingreso:				$('#wing_tal').val()

		// },function(data){

			// var data = eval('(' + data + ')');

			// var index = 0;
			// var c_procedimiento;
			// for (var datos in data)
			// {
				// index++;
				// c_procedimiento = data[index].codigo;
				// n_procedimiento = data[index].nombre;


				// if( $("#div_"+c_procedimiento).length == 0)
				// {
					// $("#"+div).append("<br><div id='acordion_"+c_procedimiento+"' ><h3>"+n_procedimiento+"</h3><fieldset align='center' ><legend align='left'></legend><div id='div_"+c_procedimiento+"'  procedimiento='"+c_procedimiento+"'></div></fieldset></div>");
					// ventana_insumo(operacion,"div_"+c_procedimiento+"",modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa);

						 // $("#acordion_"+c_procedimiento ).accordion("destroy");


							// $("#acordion_"+c_procedimiento ).accordion({
								// collapsible: true,
								// heightStyle: "content",
								// active: 1
							// });


				// }



			// }





		// });





	}

	function crear_autocomplete(HiddenArray, TipoHidden, CampoCargar, AccionSelect, CampoProcedimiento)
	{
		if(TipoHidden == 'SI')
			var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
		else
			var ArrayValores  = eval('(' + HiddenArray + ')');

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+'-'+ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}

		CampoCargar = CampoCargar.split('|');
		$.each( CampoCargar, function(key, value){
			$( "#"+value ).autocomplete({
				minLength: 	0,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+value ).val(ui.item.name);
					$( "#"+value ).attr('valor', ui.item.value);
					$( "#"+value ).attr('nombre', ui.item.name);
					crear_div_procedimiento('grabarpaciente','divmercado','no', ui.item.value,$("#responsable_original_tal").val(),$("#tarifa_original_tal").val(),'','',ui.item.name,$("#whistoria_tal").val(),$("#wing_tal").val(),tipoEmpresa,'off','');
				}
			});
		;
		});
	}




	function cargar_elementos_datapicker()
	{
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
	}



	function evaluar_procedimiento ()
	{
		 var value = $( "input:checked" ).val();



		 if(value =='paquete')
		 {
			$("#etiquetatitulo").html("Agregar " +value+" :");
			$("#busc_procedimiento").hide();
			$("#busc_paquete").show();
			$("#procedimiento_autorizado").hide();

		 }
		 if(value =='procedimiento')
		 {

			$("#etiquetatitulo").html(" <input id='checkeado' onchange='todos_los_procedimientos()' type='checkbox' checked > Procedimientos Autorizados:");
			$("#busc_procedimiento").hide();
			$("#busc_paquete").hide();
			$("#procedimiento_autorizado").show();

		 }


	}

	function todos_los_procedimientos()
	{
			if($("#checkeado").val() =='on')
			{
				$("#etiquetatitulo").html(" <input onchange='todos_los_procedimientos()' type='checkbox'  > Agregar Procedimientos:");
				$("#busc_procedimiento").show();
				$("#busc_paquete").hide();
				$("#procedimiento_autorizado").hide();
			}
			else
			{

				$("#etiquetatitulo").html(" <input id='checkeado' onchange='todos_los_procedimientos()' type='checkbox' checked > Procedimientos Autorizados:");
				$("#busc_procedimiento").hide();
				$("#busc_paquete").hide();
				$("#procedimiento_autorizado").show();
			}


	}



	function limpiarPantalla()
	{
		$("#whistoria").val('');
		$("#wing").val('');
		$("input[type='radio'][defecto='si']").attr("checked", true);
		$("#informacion_inicial").find("[limpiar=si]").html("");

	}



	function CambiarFoco(e, Elemento)
	{
		var tecla = (document.all) ? e.keyCode : e.which;
		if(tecla == 13)
		{
			$('#'+Elemento).focus();
		}
	}



	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajes").html("<BLINK><img width='15' height='15' src='../../images/medical/root/info.png' /></BLINK>&nbsp;"+mensaje);
		$("#div_mensajes").css({"width":"300","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes").hide();
		$("#div_mensajes").show(500);
	}













	//------------------------------------------------------------------------------------------------------
	//	Funcion que carga un autocomplete para seleccionar un responsable
	//------------------------------------------------------------------------------------------------------
	function crear_variables_compartidas()
	{
		// --> Historia
		if($("#div_campos_compartidos").find("#whistoria_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="whistoria_tal" type="hidden" value="" name="whistoria">');
		// --> Ingreso
		if($("#div_campos_compartidos").find("#wing_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wing_tal" type="hidden" value="" name="wing">');
		// --> Nombre 1
		if($("#div_campos_compartidos").find("#wno1_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wno1_tal" type="hidden" value="" name="wno1">');
		// --> Nombre 2
		if($("#div_campos_compartidos").find("#wno2_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wno2_tal" type="hidden" value="" name="wno2">');
		// --> Apellido 1
		if($("#div_campos_compartidos").find("#wap1_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wap1_tal" type="hidden" value="" name="wap1">');
		// --> Apellido 2
		if($("#div_campos_compartidos").find("#wap2_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wap2_tal" type="hidden" value="" name="wap2">');
		// --> Documento
		if($("#div_campos_compartidos").find("#wdoc_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wdoc_tal" type="hidden" value="" name="wdoc">');
		// --> Nombre de empresa
		if($("#div_campos_compartidos").find("#wnomemp_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wnomemp_tal" type="hidden" value="" name="wnomemp">');
		// --> Fecha de ingreso
		if($("#div_campos_compartidos").find("#wfecing_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wfecing_tal" type="hidden" value="" name="wfecing">');
		// --> Servicio de ingreso
		if($("#div_campos_compartidos").find("#wser_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wser_tal" type="hidden" value="" name="wser">');
		// -->
		if($("#div_campos_compartidos").find("#wpactam_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wpactam_tal" type="hidden" value="" name="wpactam">');
		// --> Nombre del servicio de ingreso
		if($("#div_campos_compartidos").find("#nomservicio_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="nomservicio_tal" type="hidden" value="" name="nomservicio">');
		// --> Nombre Responsable
		if($("#div_campos_compartidos").find("#div_responsable_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="div_responsable_tal" type="hidden" value="" name="div_responsable">');
		// --> Codigo Responsable
		if($("#div_campos_compartidos").find("#responsable_original_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="responsable_original_tal" type="hidden" value="" name="responsable_original">');
		// --> Nombre Tarifa
		if($("#div_campos_compartidos").find("#div_tarifa_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="div_tarifa_tal" type="hidden" value="" name="div_tarifa">');
		// --> Codigo Tarifa
		if($("#div_campos_compartidos").find("#tarifa_original_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="tarifa_original_tal" type="hidden" value="" name="tarifa_original">');
		// -->
		if($("#div_campos_compartidos").find("#div_documento_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="div_documento_tal" type="hidden" value="" name="div_documento">');
		// --> cco del facturador
		if($("#div_campos_compartidos").find("#wcco_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wcco_tal" type="hidden" value="" name="wcco">');
		// --> Nombre del cco del facturador
		if($("#div_campos_compartidos").find("#div_servicio_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="div_servicio_tal" type="hidden" value="" name="div_servicio">');
		// --> Tipo de paciente
		if($("#div_campos_compartidos").find("#wtip_paciente_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wtip_paciente_tal" type="hidden" value="" name="wtip_paciente">');
		// --> Div para pintar cuadro de datos basicos del paciente
		if($("#div_campos_compartidos").find("#div_datos_basicos_tal").length == 0)
			$("#div_campos_compartidos").append('<div id="div_datos_basicos_tal" style="display:none"></div>');
		// --> Usuario administrador
		if($("#div_campos_compartidos").find("#wcajadm_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wcajadm_tal" type="hidden" value="" name="wcajadm">');
		// --> tipo de ingreso
		if($("#div_campos_compartidos").find("#wtipo_ingreso_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wtipo_ingreso_tal" type="hidden" value="" name="wtipo_ingreso">');
		// --> Hubicacion del paciente
		if($("#div_campos_compartidos").find("#ccoActualPac_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="ccoActualPac_tal" type="hidden" value="" name="ccoActualPac">');
		// --> Nombre Hubicacion del paciente
		if($("#div_campos_compartidos").find("#nomCcoActualPac_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="nomCcoActualPac_tal" type="hidden" value="" name="nomCcoActualPac">');
		// --> Nombre del tipo de ingreso
		if($("#div_campos_compartidos").find("#wtipo_ingreso_nom_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wtipo_ingreso_nom_tal" type="hidden" value="" name="wtipo_ingreso_nom">');
		// --> Tipo de empresa
		if($("#div_campos_compartidos").find("#tipoEmpresa_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="tipoEmpresa_tal" type="hidden" value="" name="tipoEmpresa">');
		// --> Nit de empresa
		if($("#div_campos_compartidos").find("#nitEmpresa_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="nitEmpresa_tal" type="hidden" value="" name="nitEmpresa">');
		// --> Si el usuario maneja bodega
		if($("#div_campos_compartidos").find("#wbod_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="wbod_tal" type="hidden" value="" name="wbod">');
		// --> Nombre del usuario
		if($("#div_campos_compartidos").find("#nomCajero_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="nomCajero_tal" type="hidden" value="" name="nomCajero">');
		// --> Si el usuario puede cambiar el responsable del cargo
		if($("#div_campos_compartidos").find("#permiteCambiarResponsable_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteCambiarResponsable_tal" type="hidden" value="" name="permiteCambiarResponsable_tal">');
		// --> Si el usuario puede cambiar de tarifa del cargo
		if($("#div_campos_compartidos").find("#permiteCambiarTarifa_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteCambiarTarifa_tal" type="hidden" value="" name="permiteCambiarTarifa_tal">');
		// --> Si el usuario puede regrabar cargos
		if($("#div_campos_compartidos").find("#permiteRegrabar_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteRegrabar_tal" type="hidden" value="" name="permiteRegrabar_tal">');
		// --> Si el usuario puede seleccionar si el cargo es facturable o no
		if($("#div_campos_compartidos").find("#permiteSeleccionarFacturable_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteSeleccionarFacturable_tal" type="hidden" value="" name="permiteSeleccionarFacturable_tal">');
		// --> Si el usuario puede seleccionar si el cargo es reconocido o excedente
		if($("#div_campos_compartidos").find("#permiteSeleccionarRecExc_tal").length == 0)
			$("#div_campos_compartidos").append('<input id="permiteSeleccionarRecExc_tal" type="hidden" value="" name="permiteSeleccionarRecExc_tal">');
	}



	function obtener_array_permisos()
	{

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:      '',
					wemp_pmla:         $('#wemp_pmla').val(),
					accion:            'obtener_array_permisos'

				},function (data) {
					 ArrayValores  = eval('(' + data + ')');
					$('#permisos').val(ArrayValores);
				});

	}

	function cargar_datos_caja()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'cargar_datos_caja'

		},function(data){
			nomcco = data.wnomcco ;
			$("#div_servicio_tal").val(nomcco);
			$("#div_servicio").html(data.wcco+'-'+nomcco);
			$("#wcajadm").val(data.wcajadm);
			$("#wcajadm_tal").val(data.wcajadm);

			$("#wcco").val(data.wcco);
			$("#wcco_tal").val(data.wcco);
			$("#wnomcco").val(data.wnomcco);
			$("#wbod").val(data.wbod);
			$("#wbod_tal").val(data.wbod);
			$("#wcaja").val(data.wcaja);
			$("#nomCajero").val(data.nomCajero);
			$("#nomCajero_tal").val(data.nomCajero);
			$("#permiteCambiarResponsable").val(data.cambiarResponsable);
			$("#permiteCambiarResponsable_tal").val(data.cambiarResponsable);
			$("#permiteCambiarTarifa").val(data.cambiarTarifa);
			$("#permiteCambiarTarifa_tal").val(data.cambiarTarifa);
			$("#permiteRegrabar").val(data.permiteRegrabar);
			$("#permiteRegrabar_tal").val(data.permiteRegrabar);
			$("#permiteSeleccionarFacturable").val(data.permiteSeleccionarFacturable);
			$("#permiteSeleccionarFacturable_tal").val(data.permiteSeleccionarFacturable);
			$("#permiteSeleccionarRecExc").val(data.permiteSeleccionarRecExc);
			$("#permiteSeleccionarRecExc_tal").val(data.permiteSeleccionarRecExc);
			if(data.permiteSeleccionarRecExc == 'on')
				$("[name=wrecexc_1]").removeAttr('disabled');
			if(data.permiteSeleccionarFacturable == 'on')
				$("[name=wfacturable_1]").removeAttr('disabled');
			if(data.cambiarResponsable == 'on')
				$("#ImgCambioRes").show();
			if(data.cambiarTarifa == 'on')
				$("#ImgCambioTar").show();

		},
		'json');
	}

	//----------------------------
	//	Nombre: cargar_datos
	//	Descripcion: funcion que carga los datos basicos informativos dados una historia y un ingreso
	//	Entradas: elemento - elemento desde donde se hace el llamado a la funcion
	//	Salidas:
	//----------------------------
	function cargar_datos(elemento)
	{
		var id = elemento;//variable que almacena el id del elemento de donde se hizo el llamado a la funcion cargar_datos

		// si la historia es vacia  se  inician los datos y no se continua la ejecucion de la funcion
		if($("#whistoria_tal").val()=='' && $("#whistoria").val()=='')
		{
			limpiarPantalla();
			return;
		}
		else
		{
			if($("#whistoria").val() == '')
				$("#whistoria").val($("#whistoria_tal").val());
		}

		// --> se hace una llamada ajax cargar_datos
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'cargar_datos',
			whistoria:		   		$('#whistoria').val(),
			wing:					$('#wing').val(),
			wcargos_sin_facturar:	$("#cargos_sin_facturar").val(),
			welemento:				id

		},function(data){

			//data.prueba indica si la historia existe
			if(data.prueba =='no')
			{
				alert('La historia no existe');
				$('#whistoria').val('');
				$('#wing').val('');
			}
			else
			{
				// data.error indica si hay un error  en el llamado de la funcion
				if(data.error ==1)
				{
					alert(data.mensaje);
					$('#whistoria').val('');
					$('#wing').val('');
					limpiarPantalla();
				}
				else
				{
					// --> datos traidos desde la funcion
					$("#whistoria_tal").val($('#whistoria').val());

					$("#wing").val(data.wwing);
					$("#wing_tal").val(data.wwing);

					$("#wno1").val(data.wno1);
					$("#wno1_tal").val(data.wno1);

					$("#wno2").val(data.wno2);
					$("#wno2_tal").val(data.wno2);

					$("#wap1").val(data.wap1);
					$("#wap1_tal").val(data.wap1);

					$("#wap2").val(data.wap2);
					$("#wap2_tal").val(data.wap2);

					$("#wdoc").val(data.wdoc);
					$("#wdoc_tal").val(data.wdoc);

					$("#wnomemp").val(data.wnomemp);
					$("#wnomemp_tal").val(data.wnomemp);

					$("#wfecing").html(data.wfecing);
					$("#wfecing_tal").val(data.wfecing);

					$("#wser").val(data.wser);
					$("#wser_tal").val(data.wser);

					// --> Ubicacion actual del paciente
					$("#divCcoActualPac").html(data.ccoActualPac+"-"+data.nomCcoActualPac);
					$("#ccoActualPac").val(data.ccoActualPac);
					$("#nomCcoActualPac").val(data.nomCcoActualPac);
					$("#ccoActualPac_tal").val(data.ccoActualPac);
					$("#nomCcoActualPac_tal").val(data.nomCcoActualPac);

					$("#wpactam").val(data.wpactam);
					$("#wpactam_tal").val(data.wpactam);

					$("#nomservicio").html(data.wnombreservicio);
					$("#nomservicio_tal").html(data.wnombreservicio);

					$("#div_tipo_servicio").html(data.wnombreservicio);

					$("#div_responsable").html(data.responsable);
					$("#div_responsable_tal").val(data.responsable);

					$("#responsable_original").val(data.wcodemp);
					$("#responsable_original_tal").val(data.wcodemp);

					$("#td_responsable").html(data.responsable);

					$("#hidden_responsable").val(data.wcodemp);

					$("#div_tarifa").html(data.tarifa);
					$("#div_tarifa_tal").val(data.tarifa);

					$("#tarifa_original").val(data.wtar);
					$("#tarifa_original_tal").val(data.wtar);

					$("#td_tarifa").html(data.tarifa);
					$("#hidden_tarifa").val(data.wtar);
					$("#div_paciente").html(data.paciente);

					// --> Pintar los otros responsables del paciente
					$("#tableResponsables").html('');
					$("#tableResponsables").append(data.otrosResponsables).show();

					$("#div_documento").html(data.wdoc);
					$("#div_documento_tal").val(data.wdoc);

					$("#div_servicio").html($("#wcco").val()+'-'+nomcco);
					$("#div_servicio_tal").val(nomcco);

					$("#wtip_paciente").val(data.wtip_paciente);
					$("#wtip_paciente_tal").val(data.wtip_paciente);

					$("#wtipo_ingreso").val(data.tipo_ingreso);
					$("#wtipo_ingreso_tal").val(data.tipo_ingreso);
					$("#wtipo_ingreso_nom_tal").val(data.nombre_tipo_ingreso);

					$("#div_tipo_ingreso").html(data.nombre_tipo_ingreso);

					// --> Tipo de empresa
					$("#tipoEmpresa").val(data.tipoEmpresa);
					$("#tipoEmpresa_tal").val(data.tipoEmpresa);

					// --> Nit de empresa
					$("#nitEmpresa").val(data.nitEmpresa);
					$("#nitEmpresa_tal").val(data.nitEmpresa);

					// --> Pintar el detalle de la cuenta simple
					// $("#cargos_sin_facturar").val(data.cargos_sin_facturar);
					// $("#tabla_informativos_basicos").css("display" , "block");

					// --> Pintar el detalle de la cuenta simple resumido
					//PintarDetalleCuentaResumido($('#whistoria').val(), data.wwing);

					// --> verificar si se pueden grabar cargos, por congelacion de cuenta.
					//validarEstadoDeCuentaCongelada(false)

				}
			}
		},
		'json');
	}

	function cargar_procedimiento()
	{

		var ArrayValores = eval('(' + $("#hidden_procedimiento").val() + ')');
		var procedimientos 	= new Array();
		var index		  	= -1;
		for (var cod_pro in ArrayValores)
		{
			index++;
			procedimientos[index] = {};
			procedimientos[index].value  = cod_pro+'-'+ArrayValores[cod_pro];
			procedimientos[index].label  = cod_pro+'-'+ArrayValores[cod_pro];
			procedimientos[index].nombre = cod_pro+'-'+ArrayValores[cod_pro];
			procedimientos[index].valor  = cod_pro;
		}
		$( "#busc_procedimiento" ).autocomplete({
			minLength: 	3,
			source: 	procedimientos,
			select: 	function( event, ui ){
				$("#busc_procedimiento").val(ui.item.nombre);
				$("#busc_procedimiento").attr('valor', ui.item.valor);
				$("#busc_procedimiento").attr("nombre", ui.item.nombre);
				crear_div_procedimiento('grabarpaciente','divmercado','no', ui.item.valor,$("#responsable_original_tal").val(),$("#tarifa_original_tal").val(),'','',ui.item.nombre,$("#whistoria_tal").val(),$("#wing_tal").val(),tipoEmpresa,'off','');
				$("#busc_procedimiento").val('');
				$("#busc_procedimiento").attr('valor', '');
				$("#busc_procedimiento").attr("nombre", '');
				return false;
			}
		});

	}


	//------------------------------------------------------------------------
	//Funcion que oculta los detalles inactivos y que solo muestra los activos
	//-------------------------------------------------------------------------
	function ver_detalle(clave)
	{
		//$(".detalle").hide();
		$("#detalle_"+clave).toggle();
	}

	function crear_div_procedimiento(operacion,div,modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa)
	{

		var value = $( "input:checked" ).val();
		if ( $("#whistoria").val() =='')
		{
			alert("Debe digitar primero una historia");
			return;
		}


			if( $("#div_"+c_procedimiento).length == 0)
			{

				$("#"+div).append("<br><div  id='acordion_"+c_procedimiento+"'  ><h3>"+n_procedimiento+"</h3><div id='div_"+c_procedimiento+"'   procedimiento='"+c_procedimiento+"'></div></div>");

				ventana_insumo(operacion,"div_"+c_procedimiento+"",modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,'','','');



				// $( "#acordion_"+c_procedimiento ).accordion("destroy");


				// $( "#acordion_"+c_procedimiento ).accordion({
					// collapsible: true,
					// heightStyle: "content",
					// active: 1
				// });

				/*$("#accordionContenido").accordion("destroy");
				$("#accordionContenido" ).accordion({
							collapsible: true,
							heightStyle: "content",
							active: 0
						});*/




			}
			else
			{
				alert("el mercado para este procedimiento ya esta agregado");
			}


	}


	function procedimiento_autorizado_seleccionado()
	{

		crear_div_procedimiento('grabarpaciente','divmercado','no', $("#cupsAutorizados").val(),$("#responsable_original_tal").val(),$("#tarifa_original_tal").val(),'','',$("#cupsAutorizados option:selected").text(),$("#whistoria_tal").val(),$("#wing_tal").val(),tipoEmpresa,'off','');
	}
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>

<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.ui-autocomplete{
			max-width:         230px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size:         9pt;
        }
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:2px;opacity:1;}
		#tooltip h7, #tooltip div{margin:0; width:auto}
		.Titulo_azul{
			color:#3399ff;
			font-weight: bold;
			font-family: verdana;
			font-size: 10pt;
		}
		.BordeGris{
			border: 1px solid #999999;
		}
		.BordeNaranja{
			border: 1px solid orange;
		}
		.campoRequerido{
			border: 1px outset #3399ff ;
			background-color:lightyellow;
			color:gray;
		}
		.pad{
                       padding: 3px;
            }


	.diviespecial{
		height: 800px;

	}

	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->

<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<input type="hidden" name="hidden_procedimiento" id="hidden_procedimiento" value='<?=json_encode(obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato))?>'>
	<input type='hidden' name='hidden_paquetes' id='hidden_paquetes' value='<?=json_encode(Obtener_array_paquetes($conex, $wemp_pmla, $wbasedato))?>'>
	<BODY>
	<?php
	// -->	ENCABEZADO
	//encabezado("", $wactualiz, 'clinica');

	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	// --> Cargar hidden de Paquetes, para el autocomplete.
	//echo "<input type='text' id='hidden_paquetes' value='".json_encode(Obtener_array_paquetes())."'>";
	// --> Entidades
	//-->Datos ocultos propios del programa de cargos
	echo "<input type='hidden' id='wno1' name='wno1' >";
	echo "<input type='hidden' id='wno2' name='wno2' >";
	echo "<input type='hidden' id='wap1' name='wap1' >";
	echo "<input type='hidden' id='wap2' name='wap2' >";
	echo "<input type='hidden' id='wdoc' name='wdoc' >";
	echo "<input type='hidden' id='wser' name='wser' >";
	echo "<input type='hidden' id='warctar' name='warctar' >";
	echo "<input type='hidden' id='wconmva' name='wconmva' >";
	echo "<input type='hidden' id='wtip_paciente' name='wtip_paciente' >";
	echo "<input type='hidden' id='wtipo_ingreso' name='wtipo_ingreso' >";
	echo "<input type='hidden' id='wcaja' name='wcaja' >";
	echo "<input type='hidden' id='nomCajero' name='nomCajero' >";
	echo "<input type='hidden' id='wcajadm' name='wcajadm' >";
	echo "<input type='hidden' id='permiteCambiarResponsable' 		name='permiteCambiarResponsable' >";
	echo "<input type='hidden' id='permiteCambiarTarifa' 			name='permiteCambiarTarifa' >";
	echo "<input type='hidden' id='permiteRegrabar' 				name='permiteRegrabar' >";
	echo "<input type='hidden' id='permiteSeleccionarFacturable' 	name='permiteSeleccionarFacturable' >";
	echo "<input type='hidden' id='permiteSeleccionarRecExc' 		name='permiteSeleccionarRecExc' >";
	echo "<input type='hidden' id='wnomcco' name='wnomcco' >";
	echo "<input type='hidden' id='wcco' name='wcco' >";
	echo "<input type='hidden' id='cargos_sin_facturar' name='cargos_sin_facturar' >";
	echo "<input type='hidden' id='wdevol' name='wdevol' >";
	echo "<input type='hidden' id='wcodpaq' name='wcodpaq' value='' >";
	echo "<input type='hidden' id='wconmvto' name='wconmvto' value='' >";
	echo "<input type='hidden' id='wbod' name='wbod' value='off' >";
	echo "<input type='hidden' id='wexidev' name='wexidev' value='' >";
	echo "<input type='hidden' id='num_paquete' name='num_paquete' value='1' >";
	echo "<input type='hidden' id='cuentaCongelada' name='cuentaCongelada' value='' >";
	echo "<input type='hidden' id='ccoActualPac' name='ccoActualPac' value='' >";
	echo "<input type='hidden' id='nomCcoActualPac' name='ccoActualPac' value='' >";

	echo "
	<input type='hidden' id='wnomemp' name='wnomemp'>
	<input type='hidden' id='hidden_responsable'>
	<input type='hidden' id='responsable_original'>
	<input type='hidden' id='hidden_tarifa'>
	<input type='hidden' id='tarifa_original'>
	<input type='hidden' id='tipoEmpresa'>
	<input type='hidden' id='nitEmpresa'>
	";



	echo"
	<div align='center'>
		<div width='95%' id='accordionDatosPaciente'>
			<h3>DATOS DEL PACIENTE</h3>
			<div class='pad' align='center' id='DatosPaciente'>
	<table width='98%' align='center'>";
	// --> informacion inicial
	echo'
		<tr>
			<td align="center" width="90%">
				<div id="informacion_inicial" width="80%">
					<table width="90%" style="border: 1px solid #999999;">
						<tr>
							<td align=center colspan="7" class="encabezadoTabla"><b>D A T O S &nbsp&nbspD E L &nbsp&nbspP A C I E N T E</b></td>
						</tr>
						<tr class="fila1" style="font-weight: bold;">
							<td align="left" width="11%">
								<b>Historia:</b>
							</td>
							<td align="left" width="15%">
								<b>Ingreso Nro:</b>
							</td>
							<td align="left" colspan="2">
								<b>Paciente:</b>
							</td>
							<td align="left">
								<b>Documento:</b>
							</td>
							<td align="left">
								<b>Fecha Ingreso:</b>
							</td>
							<td align="left">
								<b>Fecha del cargo:</b>
							</td>
						</tr>
						<tr class="fila2">
							<td align="left">
								<input type="text" id="whistoria" size="15"  value="" onchange="cargar_datos(\'whistoria\')" onkeypress="CambiarFoco(event, \'busc_concepto_1\');">
							</td>
							<td align="left">
								<input type="text" id="wing" value="" size="3" onchange="cargar_datos(\'wing\')" >
							</td>
							<td align="left" colspan="2" id="div_paciente" limpiar="si">
							</td>
							<td align="left" id="div_documento" limpiar="si">
							</td>
							<td align="left" id="wfecing" limpiar="si">
							</td>
							<td align="left" >
								<input type="text" id="wfeccar" name="wfeccar" value="" size="10">
							</td>
						</tr>
						<tr class="fila1" style="font-weight: bold;">
							<td align="left">
								<b>Servicio de Ing:</b>
							</td>
							<td align="left" width="12%">
								<b>Tipo de Ingreso:</b>
							</td>
							<td align="left">
								<b>Ubicación:</b>
							</td>
							<td align="left">
								<b>Servicio de facturación:</b>
							</td>
							<td align="center" colspan="3">
								<b>Responsables:</b>
							</td>
						</tr>
						<tr class="fila2">
							<td align="left" id="div_tipo_servicio" limpiar="si">
							</td>
							<td align="left" id="div_tipo_ingreso" limpiar="si">
							</td>
							<td align="left" id="divCcoActualPac" limpiar="si">
							</td>
							<td align="left" id="div_servicio">
							</td>
							<td align="left" colspan="3" style="font-size:8pt;" >
								<table width="100%" id="tableResponsables" style="background-color: #ffffff;display:none" limpiar="si">
								</table>
								<div id="div_responsable" 	style="display:none"></div>
								<div id="div_tarifa"		style="display:none"></div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>';
	// --> Fin informacion inicial
	echo"
		<tr><td><br><br></td></tr>
				</table>
			</div>
		</div>
		<div align='left'  width='100%' id='accordionContenido'>
			<h3>MERCADO DE CIRUGIA</h3>

				<div>
				<table width='100%' align='left' >
					<tr align='left'>
						<td align='left'>
							<b>Procedimiento</b><input type ='radio'  name='procedimiento_paquete' value='procedimiento' checked onchange='evaluar_procedimiento()'>
						</td>
						<td>
							<b>Paquete</b><input type ='radio'  name='procedimiento_paquete' value='paquete' onchange='evaluar_procedimiento()'>
						</td>
					</tr>
					<tr align='left'>
						<td id='etiquetatitulo' align='left'>
							<input   id='checkeado' onchange='todos_los_procedimientos()' type='checkbox' checked >Procedimientos Autorizados :
							</td><td>
							<input type='text' id='busc_procedimiento' size='60' style='display: block'>
							<div id='procedimiento_autorizado'></div>
							<input type='text' id='busc_paquete' style='display: none'></td>

					</tr>
				</table>

				<br><br><br>
				<div  align='center' id='divmercado' >

				</div>

				</div>

		</div>
	</div>";
	?>
	</BODY>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
}

}//Fin de session
?>