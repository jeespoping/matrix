<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Tarjeta de dispositivo medico implantable
 * Fecha		:	2015-10-04
 * Por			:	Felipe Alvarez Sanchez
 * Descripcion	:
 * Condiciones  :
 *********************************************************************************************************

 Actualizaciones:


 **********************************************************************************************************/

$wactualiz = "2017-09-25";

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
	<title>Comprobante</title>
	<!--<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />-->
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/smartpaginator.css" rel="stylesheet" /> <!-- Autocomplete -->

	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

	<style>

		.tborder
		{
			/*border: solid black;*/
		}
		.visibilidad
		{
			display:none;
		}

		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}

		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 10pt;
		}


		.ui-autocomplete{
			max-width: 	250px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}

		// --> Estylo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		/*Firefox*/
		[tipo=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}




	</style>
	<script>


	</script>
<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//cargar_datapicker();
		$( "#accordionprincipal" ).accordion({
				collapsible: true,
				heightStyle: "content"
			});

	    // $('#input_valor').keypress(function (){
		  // this.value = (this.value + '').replace(/[^0-9]/g, '');
	    // });

		$('#input_valor').keydown(function(e) {
		  // Admite [0-9], BACKSPACE y TAB
		  if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 8 && e.keyCode != 9)
			  e.preventDefault();
		});



		$("[scrollAlto=si]").each(function(){
				if($(this).height() > 120)
					$(this).css({"height":"200px","overflow":"auto","background":"none repeat scroll 0 0"});
		});

		var ArrayValores  = eval('(' + $('#hidden_responsables').val() + ')');

		//--Consulto si tiene el atributo de cuantos y si lo tiene miro cual es su valor y dependiendo de eso establesco la propiedad del autocompletar
		//--minlength
		if( ($("#hidden_responsables").attr('cuantos')*1)> 1000 )
		{
			var minimoparabusqueda = 3;
		}
		else
		{
			var minimoparabusqueda = 0;
		}
		//--
		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+"-"+ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}



		$( "#input_identificacion" ).autocomplete({
			minLength: 	minimoparabusqueda,
			source: 	ArraySource,
			select: 	function( event, ui ){
				// $( "#input_identificacion" ).val(ui.item.label);
				$( "#input_identificacion" ).val(ui.item.value);
				$( "#input_identificacion" ).attr('valor', ui.item.value);
				$( "#input_identificacion" ).attr('nombre', ui.item.name);

				//---------
				//se llenan los campos o divs que tiene que ver con la busqueda del tercero al que se le hace el comprobante
				$("#div_nombre").html(ui.item.name);

				$.post("comprobante_factura.php",
				{
					consultaAjax:     '',
					wemp_pmla:        $('#wemp_pmla').val(),
					accion:           'trae_datos_tercero',
					wnit:			  ui.item.value
				},function(data) {
					$("#div_direccion").html(data.direccion);
					$("#div_telefono").html(data.telefono);
					$("#div_ciudad").html(data.ciudad);


				},"json");
				//------------------
				return false;
			}
		});


		var ArrayValores  = eval('(' + $('#hidden_cco').val() + ')');
		//--Consulto si tiene el atributo de cuantos y si lo tiene miro cual es su valor y dependiendo de eso establesco la propiedad del autocompletar
		//--minlength
		if( ($("#hidden_cco").attr('cuantos')*1)> 1000 )
		{
			var minimoparabusqueda = 3;
		}
		else
		{
			var minimoparabusqueda = 0;
		}


		//--

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+"-"+ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}



		$( "#buscador_centros_costos" ).autocomplete({
			minLength: 	minimoparabusqueda,
			source: 	ArraySource,
			select: 	function( event, ui ){
				 $( "#buscador_centros_costos" ).val(ui.item.label);
				//$( "#buscador_centros_costos" ).val(ui.item.value);
				$( "#buscador_centros_costos" ).attr('valor', ui.item.value);
				$( "#buscador_centros_costos" ).attr('nombre', ui.item.name);
				return false;
			}
		});

		function daysInMonth(month,year) {
			return new Date(year, month, 0).getDate();
		}


		var date = new Date();
		var currentMonth = date.getMonth();
		var currentMonthEnd = date.getMonth();
		var currentDate = 1;
		var currentYear = date.getFullYear();
		var currentDateEnd = daysInMonth(currentMonthEnd+1, currentYear);

		$("#wfechaproceso").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			defaultTime: 'now',
			dateFormat: 'yy-mm-dd',
			minDate: new Date(currentYear, currentMonth-1, currentDate),
			maxDate: new Date(currentYear, currentMonthEnd, currentDateEnd)
		}).attr("disabled","disabled");

		cargar_datapicker();

		// $(function(){
			// $("#input_porcentaje").keydown(function(event){
				// if(event.keyCode < 48 || event.keyCode > 57){
					// return false;
				// }
			// });
		// });

		$('#input_porcentaje').keydown(function(e) {
		  // Admite [0-9], BACKSPACE y TAB
		  if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 8 && e.keyCode != 9)
		  {
			  if(e.keyCode == 190 || e.keyCode == 110)
			  {

			  }
			  else
			  e.preventDefault();
		  }
		  else
		  {

		  }
		});

	});


	function adicionarcco()
	{
		//alert("entro");
		//validaciones
		$("#buscador_centros_costos").removeClass('campoObligatorio');
		$("#input_porcentaje").removeClass('campoObligatorio');
		var validaciones = true;
		if($("#buscador_centros_costos").val()=='')
		{
			jAlert("<span style='color:#4C99F5'>Debe seleccionar el centro de costos</span>", "Mensaje");
			validaciones = false;
		}
		if($("#input_porcentaje").val()==''  && validaciones == true)
		{
			jAlert("<span style='color:#4C99F5'>Debe llenar el campo porcentaje</span>", "Mensaje");
			validaciones = false;
		}


		if($("#buscador_centros_costos").val()!='' && $("#input_porcentaje").val()!='' )
		{
			//adicionarcco
			var valor_actual = $("#input_porcentaje").val();

			var valoracumulado = 0;
			$('.solo_numeros').each(function(){
				valoracumulado = (valoracumulado * 1) + ($(this).val() * 1);

			});


			valoracumulado = (valoracumulado *1) + (valor_actual * 1);
			// alert("valor actual: 1____"+valor_actual);
		    // alert("valoracumulado : "+valoracumulado);

			valoracumulado = Math.round(valoracumulado*100)/100;
			if(valoracumulado*1 > 100)
			{
				jAlert("<span style='color:#4C99F5'>el porcentaje acumulado no puede ser mas de 100</span>", "Mensaje");
				validaciones = false;

			}
		}

		var result = $("#buscador_centros_costos").val().split('-');


		if($("#td_cco"+result[0]).length!=0 && validaciones == true  )
		{
			jAlert("<span style='color:#4C99F5'>Ya existe este centro de costo</span>", "Mensaje");
			validaciones = false;
		}

		if(validaciones)
		{
			$("#tr_cco").show();
			if($("#table_cco").length ==0)
			{
				$("#td_cco").html("<table  id='table_cco'></table>");
			}

			$("#table_cco").append('<tr class="fila2 trcco" id="tr_cco'+result[0]+'" centrodecostos="'+$("#buscador_centros_costos").val()+'" codigocentrodecostos="'+result[0]+'" ><td width="103px">&nbsp;</td><td  width="190px" id="td_cco'+result[0]+'">'+$("#buscador_centros_costos").val()+'</td><td width="200px">&nbsp;</td><td><input type="text" id="input_'+result[0]+'" class="solo_numeros validar"  value="'+$("#input_porcentaje").val()+'"></td><td>&nbsp;<img width="15" height="15" src="../../images/medical/root/borrar.png" style="cursor:pointer;" onclick="borrarcco(\''+result[0]+'\')"></td></tr>');

		}

		$('.solo_numeros').keydown(function(e) {
		  // Admite [0-9], BACKSPACE y TAB
		  if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 8 && e.keyCode != 9)
			  e.preventDefault();
		});

		$('#input_'+result[0]).blur(function (){
		  var idauxiliar = $(this).attr('id');
		  var valoractual = $(this).val();
		  var valoracumulado = 0 ;
		  $('.solo_numeros').each(function(){
			  if($(this).attr('id') != idauxiliar)
			  {
				  valoracumulado = valoracumulado*1 + $(this).val()*1;

			  }
		  });

		  // alert("valor actual: 2___"+valoractual);
		  // alert("valoracumulado : "+valoracumulado);

		  valoracumulado = Math.round(valoracumulado*100)/100;
		  if((valoractual*1) + (valoracumulado*1) > 100  )
		  {
			  $(this).val(0);
			  $(this).addClass('campoObligatorio');
			  jAlert("<span style='color:#4C99F5'>el valor del porcentaje acumulado no puede ser mas de 100</span>", "Mensaje");
		  }


		});

		if(validaciones == false)
		{

			$("#input_porcentaje").addClass('campoObligatorio');
		}
		else
		{
			$("#buscador_centros_costos").val('');
			$("#input_porcentaje").val('');
		}


	}

	function verModificaciones(registro)
	{

		alert("");
		$("#div_modal2").html("");

		$.post("comprobante_factura.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'traerDiferencias',
			numero:			  registro

		},function(data) {
			$("#div_modal2").html(data);
		});


		$( "#div_modal2" ).dialog({

			height: 700,
			width:  900,
			modal: true,
			title: "Comprobante numero: ",
			buttons: {

					cerrar: function() { //cancel
						$( this ).dialog( "close" );
					}
				}

		});
	}

	function cerrar_mes(mes , ano)
	{


		$.post("comprobante_factura.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'cerrarMes',
			wmes:			  mes,
			wano:			  ano

		},function(data) {

			//alert(data);

		},"json");

	}

	function borrarcco(cco)
	{
		//alert(cco)
		$("#tr_cco"+cco).remove();

	}

	function cargar_datapicker()
	{
		// if($("#checkboxmesnumeracionpasada").is(':checked')) {
			// var cerradomes ='si';
		// }
		// else
		// {
			// var cerradomes ='no';
		// }

			var fecha = new Date();
			var ano = fecha.getFullYear();
			var month = fecha.getMonth()+1;
			var day = fecha.getDate();

			month = (month*1) -2 ;
			if(month ==0)
			{
				ano = (ano*1) -1;
				month = 12;
			}
			//alert(month);



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
			yearSuffix: '',
		};
		$.datepicker.setDefaults($.datepicker.regional['esp']);




	}


	function calcular_valores()
	{

		var valor = $("#input_valor").val().replace(/,/g, "");
		valor = valor.replace(".00","");
		wvalor= valor *1;
		valor = formatearnumero(valor*1);
		//alert(valor);
		$("#input_valor").val(valor*1);

		$.post("comprobante_factura.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'calcular_valores',
			wtope:			  $("#select_topes").val(),
			wtarifa:		  $("#select_tarifa").val(),
			wvalor:			  wvalor

		},function(data) {
			//alert(data.total);
			$("#input_valor").val(valor);
			$("#div_impuesto").html(data.impuesto);
			$("#div_impuesto_visible").html(formatearnumero(data.impuesto));
			$("#div_total").html(data.total);
			$("#div_total_auxiliar").html(formatearnumero(data.total));

		},"json");
	}

	function formatearnumero(numero)
	{
		var resultado;
		resultado = numero.toFixed(2).replace(/./g, function(c, i, a) {
		return i && c !== "." && !((a.length - i) % 3) ? ',' + c : c;
		});

		return resultado;

	}


	function grabarComprobante()
	{

	//	alert($("#div_consecutivo").attr("valor"));
		if($("#checkboxmesnumeracionpasada").is(':checked')) {
			var cerradomes ='si';
		}
		else
		{
			var cerradomes ='no';
		}
		//alert(cerradomes);

		//-Validaciones a la grabacion
		$("#buscador_centros_costos").removeClass('campoObligatorio');
		$("#input_porcentaje").removeClass('campoObligatorio');
		var validacion = true;
		$(".validar").each(function(){
			$(this).removeClass('campoObligatorio');
			if($(this).val() =='')
			{
				$(this).addClass('campoObligatorio');
				validacion = false;

			}
		});

		//alert($(".trcco").length);
		//alert($(".trcco").length);

		if($(".trcco").length == '0' && validacion==true )
		{
			$("#buscador_centros_costos").addClass('campoObligatorio');
		    $("#input_porcentaje").addClass('campoObligatorio');
			jAlert("<span style='color:#4C99F5'>Debe seleccionar un centro de costos</span>", "Mensaje");
			validacion = false;
		}
		else
		{
			var valoracumulado = 0;
			$('.solo_numeros').each(function(){
				valoracumulado = (valoracumulado * 1) + ($(this).val() * 1);

			});

			//alert(valoracumulado);
			valoracumulado = Math.round(valoracumulado*100)/100;
			if ((valoracumulado*1) == (100*1))
			{

			}
			else
			{
						jAlert("<span style='color:#4C99F5'>El porcentaje acumulado de los centros de costo debe ser 100</span>", "Mensaje");
						validacion = false;
						return;

			}

			//armo un string para grabar los centros de costo y sus porcentajes
			var cco ='';
			var codigocco ='';
			var cadena ='';
			var valor = 0;
			$(".trcco").each(function(){
				cco = $(this).attr('centrodecostos');
				codigocco = $(this).attr('codigocentrodecostos');

				valor = $("#input_"+codigocco).val() * 1;

				cadena = cadena+"!"+cco+":"+valor;
			});

			cadena = cadena.substring(1, cadena.length);
		}

		//validacion = true;
		if(!validacion)
		{

			jAlert("<span style='color:#4C99F5'>Hay campos obligatorios sin llenar</span>", "Mensaje");
		}
		else
		{

			if($('#checkremanente').is(":checked"))
			{
				var utilizar_remanente = 'si';
			}
			else
				var utilizar_remanente = 'no';


			var valor = $("#input_valor").val().replace(/,/g, "");
			valor = valor.replace(".00","");
			valor= valor *1;
			//alert(valor);
			$.post("comprobante_factura.php",
				{
					consultaAjax:     '',
					wemp_pmla:        $('#wemp_pmla').val(),
					accion:           'grabarComprobante',
					wtope:			  $("#select_topes").val(),
					wtarifa:		  $("#select_tarifa").val(),
					wvalor:			  valor,
					wfechaproceso:	  $("#wfechaproceso").val(),
					widentificacion:  $("#input_identificacion").attr('valor'),
					wnombre:		  $("#div_nombre").html(),
					wdireccion:		  $("#div_direccion").html(),
					wciudad:		  $("#div_ciudad").html(),
					wtelefono:		  $("#div_telefono").html(),
					wconcepto:		  $("#wconcepto").val(),
					wimpuesto:		  $("#div_impuesto").html(),
					wtotal:			  $("#div_total").html(),
					wobservaciones:	  $("#wobservaciones").val(),
					wutilizarremanentes: utilizar_remanente,
					wcadena			  : cadena,
					wcerradomes		  : cerradomes,
					modifica		  : $("#modifica").val(),
					wcomprobante	  : $("#div_consecutivo").attr("valor") ,
					wvalor2			  : valor


				},function(data) {
					//alert(data.respuesta);
					if($("#modifica").val()=='si')
					{
						jAlert("<span style='color:#4C99F5; font-size: 15px' align='center'>Modificacion Exitosa </span>", "Mensaje");
						buscar("si");
						limpiar();
					}
					else
					{
						jAlert("<span style='color:#4C99F5; font-size: 15px' align='center'>Grabacion Exitosa , se grabo el consecutivo "+data.html+" </span>", "Mensaje");
						buscar("si");
						limpiar();
					}
				},"json");
		}
	}

	function buscar(inicial)
	{

		if($("#buscador_por_mes").val() !='0' &&  $("#buscador_por_ano").val() =='0')
		{
			jAlert("<span style='color:#4C99F5'>Debe especificar año para la busqueda</span>", "Mensaje");
			return;
		}

		//if(inicial =='si')
		// se valida que el campo de busqueda no venga vacio
		if ($("#buscador").val() =='' && inicial =='no' && $("#buscador_por_mes").val() =='0' &&  $("#buscador_por_ano").val() =='0' )
		{
			jAlert("<span style='color:#4C99F5'>Debe especificar algun criterio para la busqueda</span>", "Mensaje");
			//$("#buscador").addClass('campoObligatorio');
		}
		else
		{
			$("#buscador").removeClass('campoObligatorio');
			var wbuscadoraux = $("#buscador").val();
			var wbuscador_por_mes_aux = $("#buscador_por_mes").val();
			var wbuscador_por_ano_aux = $("#buscador_por_ano").val();
			$.post("comprobante_factura.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'buscar',
				wbuscador:		  $("#buscador").val(),
				winicial:		  inicial,
				wbuscador_por_mes: $("#buscador_por_mes").val(),
				wbuscador_por_ano: $("#buscador_por_ano").val()

			},function(data) {
				//alert(data.select);
				$("#resultado_consulta").html(data.html);

				//-- Se mira si se maneja el scroll
				$("[scrollAlto=si]").each(function(){
				if($(this).height() > 120)
					$(this).css({"height":"200px","overflow":"auto","background":"none repeat scroll 0 0"});
				});

				$("#buscador").val(wbuscadoraux);
				$("#buscador_por_mes").val(wbuscador_por_mes_aux);
				$("#buscador_por_ano").val(wbuscador_por_ano_aux);
			},"json");
		}
	}

	function limpiar()
	{
		$(this).removeClass('campoObligatorio');
		$("#modifica").val("no");
		$("#botongrabar").val('Grabar');
		$("#buscador_centros_costos").removeClass('campoObligatorio');
		$("#input_porcentaje").removeClass('campoObligatorio');
		$(".validar").each(function(){

			$(this).val("");
		});
		$("#wobservaciones").val("");
		$(".limpiar").each(function(){
			$(this).html("");
		});

		$.post("comprobante_factura.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'consecutivo'

		},function(data) {
			$("#div_consecutivo").html(data['consecutivo']);
		},"json");

		$.post("comprobante_factura.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'fechayhora'

		},function(data) {
			$("#div_fechayhora").html(data.fecha);
			$("#wfechaproceso").val(data.fechadatepicker);
		},"json");

		$("#table_cco").remove();



	}

	function editar(consecutivo)
	{

		$("#div_modal").html("");
		$.post("comprobante_factura.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'traer_datos',
			numero:			  consecutivo

		},function(respuesta) {
			//alert (respuesta.comprobanteNumero);
			$("#modifica").val('si');
			$("#botongrabar").val('Modificar');

			$("#div_consecutivo").html(respuesta.comprobanteNumero);
			$("#div_consecutivo").attr("valor" ,respuesta.comprobanteNumero )
			$("#input_identificacion").val(respuesta.identificacion);
			 $("#input_identificacion").attr('valor' , respuesta.identificacion)
			$("#div_nombre").html(respuesta.nombre);
			$("#div_direccion").html(respuesta.direccion);
			$("#div_telefono").html(respuesta.telefono);
			$("#div_ciudad").html(respuesta.ciudad);
			$("#wconcepto").val(respuesta.concepto);
			$("#input_valor").val(respuesta.valor);
			$("#input_valor").attr('valor' , respuesta.valor2);
			$("#select_tarifa").val(respuesta.Tarifa);

			$("#select_topes").val(respuesta.Topes);




			$("#wobservaciones").val(respuesta.observaciones);

			//alert(respuesta.porcentajes);
			$("#tr_cco").show();
			$("#td_cco").html("<table  id='table_cco'></table>");
			$("#table_cco").append(respuesta.porcentajes);


			//$("#input_valor").val(valor);


			$("#div_impuesto_visible").html(respuesta.impuesto);
			$("#div_impuesto").html(respuesta.impuesto2);
			$("#div_total").html((respuesta.total2));
			$("#div_total_auxiliar").html(respuesta.total);

			/*
			$respuesta['comprobanteNumero'] =$row[3];
				$respuesta['fechaoperacion']    =$row[4];
				$respuesta['identificacion']	   =$row[5];
				$respuesta['telefono']		   =$row[9];
				$respuesta['nombre'] 		   =$row[6];
				$respuesta['direccion']=$row[7];
				$respuesta['ciudad']=$row[8];
				$respuesta['concepto']=$row[10];
				$respuesta['valor'] =number_format((double)$row[11],2,'.',',');
				$respuesta['impuesto'] =number_format((double)$row[14],2,'.',',');
				$respuesta['total']*/


		},'json');



	}

	function ver(consecutivo)
	{

		$("#div_modal").html("");
		$.post("comprobante_factura.php",
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'traer_consecutivo',
			numero:			  consecutivo

		},function(data) {
			$("#div_modal").html(data);
		});


		$( "#div_modal" ).dialog({

			height: 700,
			width:  900,
			modal: true,
			title: "Comprobante numero: "+consecutivo,
			buttons: {
					imprimir: function() { //ok
						imprimir();
					},
					cerrar: function() { //cancel
						$( this ).dialog( "close" );
					}
				}

		});

	}

	function imprimir(){

		/*var contenido = "<html><body onload='window.print();window.close();'>";
		contenido = contenido + $("#div_modal").html() + "</body></html>";

		var windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=1,height=1,resizable=yes,screenX=1,screenY=1,personalbar=no,scrollbars=no";
		var ventana = window.open( "", "",  windowAttr );
		ventana.document.write(contenido);
		ventana.document.close();*/

		var data = $("#div_modal").html();
        var mywindow = window.open('', 'my div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>my div</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10

        mywindow.print();
        mywindow.close();

        return true;

	}



</script>
</head>

<?php

}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");



$conex = obtenerConexionBD("matrix");


$user_session = explode('-',$_SESSION['user']);
$user_session = (count($user_session) > 1)? $user_session[1] : $user_session[0];
//$user_session = ( strlen($user_session) > 5) ? substr($user_session,-5): $user_session;
$wbasedatoimpuesto 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'impuesto');


$wusuario = $user_session;

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if(isset($accion))
{
	switch($accion)
	{

		case "trae_datos_tercero":
		{

			$wbasedatoimpuesto 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'impuesto');
			$array_respuesta = array();
			$select_res = "SELECT Cedula_nit, Nombres, Direccion, Telefonos, Ciudad
							 FROM ".$wbasedatoimpuesto."_000001
							WHERE Cedula_nit ='".$wnit."'
							ORDER BY Nombres";

			$res = 	mysql_query($select_res,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				$array_respuesta['nombres'] = trim(utf8_encode($row['Nombres']));
				$array_respuesta['direccion'] = trim(utf8_encode($row['Direccion']));
				$array_respuesta['telefono'] = trim(utf8_encode($row['Telefonos']));
				$array_respuesta['ciudad'] = trim(utf8_encode($row['Ciudad']));
			}

			echo json_encode($array_respuesta);
			break;
		}

		case "traerDiferencias":
		{

			$wbasedatoimpuesto 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'impuesto');
			$sql = "SELECT * FROM ".$wbasedatoimpuesto."_000003   WHERE Regreg ='".$numero."'";

			$res = 	mysql_query($sql,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$sql." - ".mysql_error());

			$imprimir .="<table>";
			while($row = mysql_fetch_array($res))
			{

				$aux1= explode("---",$row['Regant']);
				$aux = explode("---",$row['Regact']);

				for($i=0; $i <= count($aux) ;$i++)
				{
					if ($aux1[$i] != $aux[$i])
						$auximprimir .= $aux[$i];

				}
				$imprimir .= "<tr><td>".$auximprimir."</td></tr>";
			//	$imprimir .= "Regant".$row['Regant']."Reactu".$row['Regact'];
			}
			$imprimir .="</table>";
			echo $imprimir;
			break;
		}

		case "calcular_valores":
		{
			$array_respuesta = array();
			//------Impuesto
			// esto estaba grabado en el campo tipo algoritmico del programa inicial, se busca reemplazar esto en el nuevo programa
			/*
			$ini_i=strpos($registro[10],"-");
				if($registro[8] > substr($registro[10],0,$ini_i))
					//$registro[11]=$registro[8]*($registro[9]/100)*0.50; Se cambia esta linea por peticion de jaime contador clinica Enero 17 2013
					$registro[11]=$registro[8]*($registro[9]/100)*0.15;
				else
					$registro[11]=0;
			*/
			if ( ( $wvalor > $wtope ) and ( trim( $wtarifa ) != "" and isset( $wtarifa ) ) )
			{
				$wimpuesto = $wvalor*($wtarifa/100)*0.15;
			}
			else
			{
				$wimpuesto = 0;
			}
			$array_respuesta['impuesto'] = $wimpuesto;
			//---------------------------------------

			//----Total
			//Total sera el valor total mas el impuesto
			$wvalortotal = $wvalor + $wimpuesto;
			$array_respuesta['total'] = $wvalortotal;

			echo json_encode($array_respuesta);
			break;

		}


		case "grabarComprobante":
		{


			$modeloconolgura='no';

			if ($modeloconolgura=='no')
			{

				if($modifica =='si')
				{
					$wfechaprocesoaux = explode("-",$wfechaproceso);

					$select ="SELECT *
							    FROM ".$wbasedatoimpuesto."_000012
							   WHERE Consecutivo = '".$wcomprobante."'" ;
					$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
					$insertantes='';
					while($row = mysql_fetch_array($res))
					{
						$insertantes .= " Identificacion = ".$row['Identificacion']."---";
						$insertantes .= " Nombre =  ".$row['Nombres']."---";
						$insertantes .= " Direccion =  ".$row['Direccion']."---";
						$insertantes .= " Ciudad =  ".$row['Ciudad']."---";
						$insertantes .= " Telefono =  ".$row['Telefono']."---";
						$insertantes .= " Concepto =  ".$row['Concepto']."---";
						$insertantes .= " Valor =  ".$row['Valor']."---";
						$insertantes .= " Tarifa =  ".$row['Tarifa']."---";
						$insertantes .= " Topes =  ".$row['Topes']."---";
						$insertantes .= " Impuesto =  ".$row['Impuesto']."---";
						$insertantes .= " Total =  ".$row['Total']."---";
						$insertantes .= " Observaciones =  ".$row['Observaciones']."---";
						$insertantes .= " Fecha_proceso =  ".$row['Fecha_proceso']."---";
						$insertantes .= " Seguridad =  ".$row['Seguridad']."---";
						$querycco = "SELECT * FROM ".$wbasedatoimpuesto."_000013 WHERE consecutivo='".$wcomprobante."-".$widentificacion."'";
						$rescco = 	mysql_query($querycco,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
						while($rowcco = mysql_fetch_array($rescco))
						{
							$insertantes .=" cco =".$rowcco['Cdec']." porcentaje = ".$rowcco['Porcentaje']."---";
						}


					}


					$update = "
					UPDATE ".$wbasedatoimpuesto."_000012
					   SET
						   Identificacion ='".$widentificacion."',
						   Nombres ='".$wnombre."',
						   Direccion = '".$wdireccion."',
						   Ciudad = '".$wciudad."',
						   Telefono = '".$wtelefono."',
						   Concepto = '".$wconcepto."',
						   Valor = '".$wvalor2."',
						   Tarifa = '".$wtarifa."',
						   Topes = '".$wtope."',
						   Impuesto = '".$wimpuesto."',
						   Total = '".$wtotal."',
						   Observaciones = '".$wobservaciones."',
						   Fecha_proceso = '".$wfechaprocesoaux[2]."-".$wfechaprocesoaux[1]."-".$wfechaprocesoaux[0]."'
					   WHERE Consecutivo = '".$wcomprobante."'" ;

						mysql_query($update,$conex) or die ("Error 4: ".mysql_errno()." - en el query 1: ".$update." - ".mysql_error());


						$wcadenaaux = $wcadena;
						$wcadena 	= explode("!",$wcadena);
						$vectorcco  = array();
						$auxiliarcco = '';
						for($t=0;$t<count($wcadena);$t++)
						{
							$auxiliarcco = explode(":", $wcadena[$t]);
							$vectorcco[$t]['cco']  	  = $auxiliarcco[0] ;
							$vectorcco[$t]['valor']   = $auxiliarcco[1] ;
							$numerodecentrosdecostos = $t;
						}
						//----------------------------


						$wfecha = date("Y-m-d");
						$whora = date("H:i:s");
						$delete ="DELETE FROM ".$wbasedatoimpuesto."_000013
									   WHERE Consecutivo ='".$wcomprobante."-".$widentificacion."'";
						mysql_query($delete,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$insert." - ".mysql_error());

						for($t=0;$t<=$numerodecentrosdecostos;$t++)
						{

							$insert =	"INSERT  ".$wbasedatoimpuesto."_000013
										( Medico 		,Fecha_data  	,	Hora_data 		,	 	Consecutivo 		     						,   	Cdec 					, 		Porcentaje 						,   Seguridad 	    )
							  VALUES	('impuesto' 	, '".$wfecha."' , 	'".$whora."'	,   '".$wcomprobante."-".$widentificacion."'  	, 	'".$vectorcco[$t]['cco']."' , '".$vectorcco[$t]['valor']."' 		, 'C-".$wusuario."'	)";


							mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$insert." - ".mysql_error());
							$array_respuesta['html'] = "";
						}


					$select ="SELECT *
							    FROM ".$wbasedatoimpuesto."_000012
							   WHERE Consecutivo = '".$wcomprobante."'" ;
					$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

					$insertdespues ='';
					while($row = mysql_fetch_array($res))
					{
						$insertdespues .= " Identificacion = ".$row['Identificacion']."---";
						$insertdespues .= " Nombre =  ".$row['Nombres']."---";
						$insertdespues .= " Direccion =  ".$row['Direccion']."---";
						$insertdespues .= " Ciudad =  ".$row['Ciudad']."---";
						$insertdespues .= " Telefono =  ".$row['Telefono']."---";
						$insertdespues .= " Concepto =  ".$row['Concepto']."---";
						$insertdespues .= " Valor =  ".$row['Valor']."---";
						$insertdespues .= " Tarifa =  ".$row['Tarifa']."---";
						$insertdespues .= " Topes =  ".$row['Topes']."---";
						$insertdespues .= " Impuesto =  ".$row['Impuesto']."---";
						$insertdespues .= " Total =  ".$row['Total']."---";
						$insertdespues .= " Observaciones =  ".$row['Observaciones']."---";
						$insertdespues .= " Fecha_proceso =  ".$row['Fecha_proceso']."---";
						$insertdespues .= " Seguridad =  ".$row['Seguridad']."---";

						$querycco = "SELECT * FROM ".$wbasedatoimpuesto."_000013 WHERE consecutivo='".$wcomprobante."-".$widentificacion."'";
						$rescco = 	mysql_query($querycco,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
						while($rowcco = mysql_fetch_array($rescco))
						{
							$insertdespues .=" cco =".$rowcco['Cdec']." porcentaje = ".$rowcco['Porcentaje']."---";
						}



					}

					$insert =	"INSERT  ".$wbasedatoimpuesto."_000003
										( Medico 		,Fecha_data  	,	Hora_data 		,	 Regant 		     	,   	Regact 					, 	 Seguridad , Regreg	    )
							   VALUES	('impuesto' 	, '".$wfecha."' , 	'".$whora."'	,   '".$insertantes."'  	, 	'".$insertdespues."' ,  'C-".$wusuario."' , '".$wcomprobante."'	)";


					mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$insert." - ".mysql_error());


					// echo json_encode($array_respuesta);


				}
				else
				{
						$select_consecutivo = "SELECT MAX(Consecutivo) as consecutivo
															 FROM ".$wbasedatoimpuesto."_000012 ";
						$res = 	mysql_query($select_consecutivo,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select_consecutivo." - ".mysql_error());
						$row = mysql_fetch_array($res);
						$wconsecutivo = $row['consecutivo']*1 ;

						$wconsecutivo_grabar = $wconsecutivo*1 + 1;
						$wfecha = date("Y-m-d");
						$whora = date("H:i:s");
						$wcadenaaux = $wcadena;
						$wcadena 	= explode("!",$wcadena);
						$vectorcco  = array();
						$auxiliarcco = '';
						for($t=0;$t<count($wcadena);$t++)
						{
							$auxiliarcco = explode(":", $wcadena[$t]);
							$vectorcco[$t]['cco']  	  = $auxiliarcco[0] ;
							$vectorcco[$t]['valor']   = $auxiliarcco[1] ;
							$numerodecentrosdecostos = $t;
						}
						//----------------------------


						$wfechaprocesoaux = explode("-",$wfechaproceso);

						$insert =	"INSERT  ".$wbasedatoimpuesto."_000012
												 (Medico ,	Fecha_data 	 ,  Hora_data 	, 	Consecutivo 					, Fecha_proceso			,     Identificacion		, 	Nombres			, Direccion 		, Ciudad 			, Telefono			,  Concepto			,	Valor			,		Tarifa		,		Topes			,	Impuesto			,	Total 			,	Observaciones 			,	Seguridad)
									  VALUES	 ('impuesto' , '".$wfecha."' , 	'".$whora."',   '".$wconsecutivo_grabar."'  	, '".$wfechaprocesoaux[2]."-".$wfechaprocesoaux[1]."-".$wfechaprocesoaux[0]."' 	,  '".$widentificacion."'  	, '".utf8_decode($wnombre)."' , '".utf8_decode($wdireccion)."'	, '".utf8_decode($wciudad)."'	, '".$wtelefono."'	,  '".utf8_decode($wconcepto)."'	,	'".$wvalor."'	,	'".$wtarifa."'	,	'".$wtope."'		,	'".$wimpuesto."' 	, 	'".$wtotal."'	,	'".utf8_decode($wobservaciones)."'	,	'C-".$wusuario."'	)";

						mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query 1: ".$insert." - ".mysql_error());

						$insertaud = $insert;
						for($t=0;$t<=$numerodecentrosdecostos;$t++)
						{
							$insert =	"INSERT  ".$wbasedatoimpuesto."_000013
													( Medico 		,Fecha_data  	,	Hora_data 		,	 	Consecutivo 		     						,   	Cdec 					, 		Porcentaje 						,   Seguridad 	    )
										  VALUES	('impuesto' 	, '".$wfecha."' , 	'".$whora."'	,   '".$wconsecutivo_grabar."-".$widentificacion."'  	, 	'".$vectorcco[$t]['cco']."' , '".$vectorcco[$t]['valor']."' 		, 'C-".$wusuario."'	)";

							$rr = $insert;
							mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$insert." - ".mysql_error());
						}


						$array_respuesta['html']=$wconsecutivo_grabar;
						//$array_respuesta['respuesta']= $select;
						//$array_respuesta['respuesta']="consecutivo a grabar:".$wconsecutivo_grabar."consecutivo maximo de ese periodo".$wconsecutivomax;
						//$array_respuesta['respuesta']="consecutivo a grabar:".$wconsecutivo_grabar."select".$select_consecutivo;
						//$array_respuesta['respuesta']="consecutivo a grabar:".$wconsecutivo_grabar."consecutivo maximo de ese periodo".$wconsecutivomax;
				}

				echo json_encode($array_respuesta);
				break;
				return;

			}

			//--Numero de comprobantes de holgura por mes----------
			$numerodeconsecutivos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'numeroComprobantes');
			$numerodeconsecutivos = $numerodeconsecutivos * 1;
			//------------------------------------------------------
			$esdelmesactual 	= true;
			$array_respuesta 	= array();
			$wfecha 	 		= date("Y-m-d");

			$wmesactual  		= date("m") * 1;
			$wmesactualaux		= date("m");
			$wanoactualaux		= date("Y");
			$wmesenviadoaux 	= explode("-",$wfechaproceso);
			$wmesenviado 		= $wmesenviadoaux[1]*1;

			$whora  = date("H:i:s");
			$wbasedatoimpuesto 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'impuesto');
			//-----------
			// Se mira si es el primer comprobante del mes y se registra en la tabla impuestos 2 donde se marcara en que comprobante termina el mes anterior
			// y en cual comienza el nuevo mes
			// si es el primero del mes actual




			if($wmesenviado == 1)
			{
			   $wmesenviado_auxiliar = 12;
			   $wanoenviado_auxiliar = ($wmesenviadoaux[0]*1) -1;
			}
			else
			{
			   $wmesenviado_auxiliar  = $wmesenviado - 1;
			   $wanoenviado_auxiliar  = $wmesenviadoaux[0]*1;
			}

			if($wcerradomes == 'si')
			{
				$wutilizarremanentes='no';
			}


			if($wutilizarremanentes == "no")
			{


				$select ="SELECT  COUNT(*) as cuantos  ,Impcmi , Impcma
							FROM ".$wbasedatoimpuesto."_000002
						   WHERE Impmes ='".($wmesenviadoaux[1]*1)."'
							 AND Impano ='".($wmesenviadoaux[0]*1)."'";

				$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
				$row =  mysql_fetch_array($res);
				$cuantos = $row['cuantos'];


				if($cuantos ==0)
				{


						$select_consecutivo = "SELECT MAX(Consecutivo) as consecutivo
												 FROM ".$wbasedatoimpuesto."_000012 ";
						$res = 	mysql_query($select_consecutivo,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select_consecutivo." - ".mysql_error());
						$row = mysql_fetch_array($res);
						$wconsecutivo = ($row['consecutivo']*1 )+ ($numerodeconsecutivos * 1);


						$insert =	"INSERT  ".$wbasedatoimpuesto."_000002
											 (Medico ,	Fecha_data 	 ,  Hora_data 	, 		Impmes 						, 		Impano				,     Impcmi														,	Seguridad , Impcer)
									 VALUES	 ('root' , '".$wfecha."' , 	'".$whora."',   '".(($wmesenviadoaux[1])*1)."'  	, '".$wmesenviadoaux[0]."' 	,  '".(($wconsecutivo*1) + 1 )."'  	, 	'C-".$wusuario."'	, 'on')";

						mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$insert." - ".mysql_error());

						$Select_existe = "SELECT COUNT(*) as cuantos  FROM ".$wbasedatoimpuesto."_000002
										   WHERE Impmes ='".$wmesenviado_auxiliar."'
											 AND Impano ='".$wanoenviado_auxiliar."'";
						$res = 	mysql_query($Select_existe,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$Select_existe." - ".mysql_error());
						$row = mysql_fetch_array($res);
						$cuantos = $row['cuantos'];



						if($cuantos ==0 )
						{

							$select_consecutivo = "SELECT MAX(Consecutivo) as consecutivo
														 FROM ".$wbasedatoimpuesto."_000012
														WHERE Fecha_proceso NOT LIKE '".$wmesactualaux."-".$wanoactualaux."%'";

							$res = 	mysql_query($select_consecutivo,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select_consecutivo." - ".mysql_error());
							$row = mysql_fetch_array($res);
							$wconsecutivominimo = $row['consecutivo'];



							$insert =	"INSERT  ".$wbasedatoimpuesto."_000002
											 (Medico ,	Fecha_data 	 ,  Hora_data 	, 		Impmes 						, 		Impano					,     Impcma													, Impcmi					,	Seguridad)
											VALUES	 ('root' , '".$wfecha."' , 	'".$whora."',   '".$wmesenviado_auxiliar."'  	, '".$wanoenviado_auxiliar."' 	,  '".(($wconsecutivo*1)  + 1)."'  	, 	".$wconsecutivominimo."	,'C-".$wusuario."'	)";

							mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$insert." - ".mysql_error());

						}
						else
						{


							$update = "UPDATE ".$wbasedatoimpuesto."_000002  SET Impcma ='".(($wconsecutivo*1))."'
										WHERE Impmes ='".$wmesenviado_auxiliar."'
										  AND Impano ='".$wanoenviado_auxiliar."'";

							mysql_query($update,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$update." - ".mysql_error());

						}

				}
				else
				{


					$wconsecutivo = $row['Impcmi']*1;
					$wconsecutivomax = $row['Impcma']*1;

					if($wconsecutivo == $wconsecutivomax  or  $wcerradomes == 'si')
					{
						$select_consecutivo = "SELECT MAX(Consecutivo) as consecutivo
												 FROM ".$wbasedatoimpuesto."_000012 ";
						$res = 	mysql_query($select_consecutivo,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select_consecutivo." - ".mysql_error());
						$row = mysql_fetch_array($res);
						$wconsecutivo = $row['consecutivo']*1 ;



					}
					else
					{

						$update = "UPDATE ".$wbasedatoimpuesto."_000002  SET Impcmi ='".(($wconsecutivo*1)  + 1)."'
									WHERE Impmes ='".($wmesenviadoaux[1]*1)."'
									  AND Impano ='".$wmesenviadoaux[0]."'";

						mysql_query($update,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$update." - ".mysql_error());
					}

				}
			}
			else
			{
					$Select_existe = "SELECT COUNT(*) as cuantos  ,Impcmi , Impcma  FROM ".$wbasedatoimpuesto."_000002
										   WHERE Impmes ='".$wmesenviado_auxiliar."'
											 AND Impano ='".$wanoenviado_auxiliar."'";
					$res = 	mysql_query($Select_existe,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$Select_existe." - ".mysql_error());
					$row = mysql_fetch_array($res);
					$cuantos = $row['cuantos'];

					$se = $Select_existe;

					if($cuantos ==0)
					{


					}
					else
					{
						$wconsecutivo = $row['Impcmi']*1;
						$wconsecutivomax = $row['Impcma']*1;

						if($wconsecutivo == $wconsecutivomax)
						{
							$select_consecutivo = "SELECT MAX(Consecutivo) as consecutivo
													 FROM ".$wbasedatoimpuesto."_000012 ";
							$res = 	mysql_query($select_consecutivo,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select_consecutivo." - ".mysql_error());
							$row = mysql_fetch_array($res);
							$wconsecutivo = $row['consecutivo']*1 ;
						}
						else
						{

							$update = "UPDATE ".$wbasedatoimpuesto."_000002  SET Impcmi ='".(($wconsecutivo*1)  + 1)."'
										WHERE Impmes ='".$wmesenviado_auxiliar."'
										  AND Impano ='".$wanoenviado_auxiliar."'";

							mysql_query($update,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$update." - ".mysql_error());
						}
					}


			}

			$wconsecutivo_grabar = $wconsecutivo*1 + 1;
			//--------
			$wcadenaaux = $wcadena;
			$wcadena 	= explode("!",$wcadena);
			$vectorcco  = array();
			$auxiliarcco = '';
			for($t=0;$t<count($wcadena);$t++)
			{
				$auxiliarcco = explode(":", $wcadena[$t]);
				$vectorcco[$t]['cco']  	  = $auxiliarcco[0] ;
				$vectorcco[$t]['valor']   = $auxiliarcco[1] ;
				$numerodecentrosdecostos = $t;
			}
			//----------------------------


			$wfechaprocesoaux = explode("-",$wfechaproceso);

			$insert =	"INSERT  ".$wbasedatoimpuesto."_000012
									 (Medico ,	Fecha_data 	 ,  Hora_data 	, 	Consecutivo 					, Fecha_proceso			,     Identificacion		, 	Nombres			, Direccion 		, Ciudad 			, Telefono			,  Concepto			,	Valor			,		Tarifa		,		Topes			,	Impuesto			,	Total 			,	Observaciones 			,	Seguridad)
						  VALUES	 ('impuesto' , '".$wfecha."' , 	'".$whora."',   '".$wconsecutivo_grabar."'  	, '".$wfechaprocesoaux[2]."-".$wfechaprocesoaux[1]."-".$wfechaprocesoaux[0]."' 	,  '".$widentificacion."'  	, '".utf8_decode($wnombre)."' , '".utf8_decode($wdireccion)."'	, '".utf8_decode($wciudad)."'	, '".$wtelefono."'	,  '".utf8_decode($wconcepto)."'	,	'".$wvalor."'	,	'".$wtarifa."'	,	'".$wtope."'		,	'".$wimpuesto."' 	, 	'".$wtotal."'	,	'".utf8_decode($wobservaciones)."'	,	'C-".$wusuario."'	)";

			mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query 1: ".$insert." - ".mysql_error());


			for($t=0;$t<=$numerodecentrosdecostos;$t++)
			{
				$insert =	"INSERT  ".$wbasedatoimpuesto."_000013
										( Medico 		,Fecha_data  	,	Hora_data 		,	 	Consecutivo 		     						,   	Cdec 					, 		Porcentaje 						,   Seguridad 	    )
							  VALUES	('impuesto' 	, '".$wfecha."' , 	'".$whora."'	,   '".$wconsecutivo_grabar."-".$widentificacion."'  	, 	'".$vectorcco[$t]['cco']."' , '".$vectorcco[$t]['valor']."' 		, 'C-".$wusuario."'	)";

				$rr = $insert;
				mysql_query($insert,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$insert." - ".mysql_error());
			}


			$array_respuesta['html']=$wconsecutivo_grabar;
			//$array_respuesta['respuesta']= $select;
			//$array_respuesta['respuesta']="consecutivo a grabar:".$wconsecutivo_grabar."consecutivo maximo de ese periodo".$wconsecutivomax;
			//$array_respuesta['respuesta']="consecutivo a grabar:".$wconsecutivo_grabar."select".$select_consecutivo;
			//$array_respuesta['respuesta']="consecutivo a grabar:".$wconsecutivo_grabar."consecutivo maximo de ese periodo".$wconsecutivomax;
			echo json_encode($array_respuesta);

			break;
		}
		case "cerrarMes" :
		{
		  $wbasedatoimpuesto 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'impuesto');
		  $sql = "UPDATE ".$wbasedatoimpuesto."_000002
					 SET Impcer ='off'
				   WHERE Impano = '".$wano."'
				     AND Impmes ='".$wmes."'";

		  mysql_query($sql,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$sql." - ".mysql_error());
		  //echo $sql;
		  break;
		}
		case "buscar":
		{

			$array_respuesta= array();
			$wbasedatoimpuesto 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'impuesto');

			if($winicial == 'no')
			{

				if($wbuscador =='' AND $wbuscador_por_mes !=0 AND $wbuscador_por_ano !=0)
				{
					$select_comprobantes .= "SELECT  Consecutivo,Fecha_proceso,Identificacion,Nombres,Direccion,Ciudad,Telefono,Concepto,Valor,Tarifa,Topes,Impuesto,Total,Observaciones
										       FROM ".$wbasedatoimpuesto."_000012  ";

					if(($wbuscador_por_mes*1) != 0)
					{
						$select_comprobantes .="WHERE Fecha_proceso LIKE '%".$wbuscador_por_mes."-".$wbuscador_por_ano."%'";
					}

				}
				else if($wbuscador !='' AND $wbuscador_por_mes !=0 AND $wbuscador_por_ano !=0)
				{
					$select_comprobantes .= "SELECT  Consecutivo,Fecha_proceso,Identificacion,Nombres,Direccion,Ciudad,Telefono,Concepto,Valor,Tarifa,Topes,Impuesto,Total,Observaciones
										  FROM ".$wbasedatoimpuesto."_000012
										 WHERE (consecutivo  LIKE '%".$wbuscador."%'
											OR Identificacion  LIKE '%".$wbuscador."%'
											OR Nombres LIKE '%".$wbuscador."%'
											OR Ciudad LIKE '%".$wbuscador."%'
											OR Concepto LIKE  '%".$wbuscador."%') ";

					if(($wbuscador_por_mes*1) != 0)
					{
						$select_comprobantes .=" AND Fecha_proceso LIKE '%".$wbuscador_por_mes."-".$wbuscador_por_ano."'";
					}

				}
				else if($wbuscador !='' AND $wbuscador_por_ano ==0 AND $wbuscador_por_mes ==0 )
				{
					$select_comprobantes .= "SELECT  Consecutivo,Fecha_proceso,Identificacion,Nombres,Direccion,Ciudad,Telefono,Concepto,Valor,Tarifa,Topes,Impuesto,Total,Observaciones
										  FROM ".$wbasedatoimpuesto."_000012
										 WHERE (consecutivo  LIKE '%".$wbuscador."%'
											OR Identificacion  LIKE '%".$wbuscador."%'
											OR Nombres LIKE '%".$wbuscador."%'
											OR Ciudad LIKE '%".$wbuscador."%'
											OR Concepto LIKE  '%".$wbuscador."%') ";

					if(($wbuscador_por_mes*1) != 0)
					{
						$select_comprobantes .=" AND Fecha_proceso LIKE '%".$wbuscador_por_ano."'";
					}

				}
				else if($wbuscador =='' AND $wbuscador_por_ano !=0 AND $wbuscador_por_mes ==0 )
				{
					$select_comprobantes .= "SELECT  Consecutivo,Fecha_proceso,Identificacion,Nombres,Direccion,Ciudad,Telefono,Concepto,Valor,Tarifa,Topes,Impuesto,Total,Observaciones
										       FROM ".$wbasedatoimpuesto."_000012  ";

					if(($wbuscador_por_mes*1) != 0)
					{
						$select_comprobantes .="WHERE Fecha_proceso LIKE '%".$wbuscador_por_ano."%'";
					}

				}






					$select_comprobantes .=	" ORDER BY Consecutivo DESC" ;
			}
			else
			{

				$select_comprobantes  = "  SELECT Consecutivo,Fecha_proceso,Identificacion,Nombres,Direccion,Ciudad,Telefono,Concepto,Valor,Tarifa,Topes,Impuesto,Total,Observaciones
											 FROM  ".$wbasedatoimpuesto."_000012
										 ORDER BY Consecutivo DESC
											LIMIT 100";

			}

			if($res = mysql_query($select_comprobantes,$conex))
			{
				$usuarioAdministrador	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'AdministradorComprobantes');
				$wauxiliarAdministrador	= explode(",",$usuarioAdministrador);
				$arrayAdministrador     = array();
				$es_visible = "style='display : none'";
				for($t=0;$t<count($wauxiliarAdministrador);$t++)
				{
					if($wauxiliarAdministrador[$t]==$wusuario)
						$es_visible = "";

				}
				$html .="<div scrollAlto='si' style='text-align:justify'>
									<table align='center'  width='50%'>
									    <tr><td class='encabezadoTabla'>Buscar:</td><td  class='encabezadoTabla' nowrap='nowrap'><input id='buscador' ></td>
										<td class='encabezadoTabla' nowrap='nowrap'>Mes: <Select id='buscador_por_mes'>
										<option value='0'>Seleccione</option>
										<option value='01'>Enero</option>
										<option value='02'>Febrero</option>
										<option value='03'>Marzo</option>
										<option value='04'>Abril</option>
										<option value='05'>Mayo</option>
										<option value='06'>Junio</option>
										<option value='07'>Julio</option>
										<option value='08'>Agosto</option>
										<option value='09'>Septiembre</option>
										<option value='10'>Octubre</option>
										<option value='11'>Noviembre</option>
										<option value='12'>Diciembre</option>
										</select></td>
										<td class='encabezadoTabla' nowrap='nowrap'>Ano: <Select id='buscador_por_ano'>
										<option value='0'>Seleccione</option>
										<option value='".((date('Y')))."'>".date('Y')."</option>
										<option value='".((date('Y')*1)-1)."'>".((date('Y')*1)-1)."</option>
										<option value='".((date('Y')*1)-2)."'>".((date('Y')*1)-2)."</option>
										</select></td>
										<td><input type='button' value='Buscar' onclick='buscar(\"no\")' style='cursor:pointer;padding:1px;font-family:verdana;font-weight:bold;font-size: 8pt;'></td>
										<td colspan='5'></td></tr>
										</table>
										<br>


									<table align='center' width='98%'>
											<tr class='encabezadoTabla' align='center'>
											<td width='90px' >Consecutivo</td>
											<td width='50px'>Fecha proceso</td>
											<td width='90px'>Identificaci&oacute;n</td>
											<td width='130px'>Nombre</td>
											<td width='130px'>Direcci&oacute;n</td>
											<td width='90px'>Ciudad</td>
											<td width='130px'>Telefono</td>
											<td>Concepto</td>
											<td width='10px' ".$esvisible.">Editar</td>
											<td width='10px' ".$esvisible.">Modificacion</td>
											<td width='10px'>Ver</td>

										</tr>";


				while($row = mysql_fetch_array($res))
				{

					if (is_int ($k/2))
					   {
						$wcf="fila1";  // color de fondo de la fila
					   }
					else
					   {
						$wcf="fila2"; // color de fondo de la fila
					   }

					$html .="<tr class='".$wcf."'>";
					$html .="<td>".$row['Consecutivo']."</td>";
					$html .="<td>".$row['Fecha_proceso']."</td>";
					$html .="<td>".$row['Identificacion']."</td>";
					$html .="<td>".utf8_encode($row['Nombres'])."</td>";
					$html .="<td>".$row['Direccion']."</td>";
					$html .="<td>".$row['Ciudad']."</td>";
					$html .="<td>".$row['Telefono']."</td>";
					$html .="<td>".substr($row['Concepto'],0,50)."</td>";


					$html .="<td style='cursor:pointer' ".$es_visible ." onclick='editar(".$row['Consecutivo'].")'><u>Editar</u></td>";

					$selectlog = "SELECT * FROM ".$wbasedatoimpuesto."_000003 WHERE Regreg='".$row['Consecutivo']."'";
					$modificable = 'no';
					if($reslog = mysql_query($selectlog,$conex))
					{
						while($rowlog = mysql_fetch_array($reslog))
						{
							$modificadopor .= $rowlog['Seguridad'] ;
							$fechapor 	   .= $rowlog['Fecha_data'] ;
							$modificable = 'si';
						}
					}
					$html .="<td style='cursor:pointer' ".$es_visible ." align='center'>".(($modificable =='no') ? " " : "SI")."</td>";
					$html .="<td  style='cursor:pointer' onclick='ver(".$row['Consecutivo'].")'><u>ver</u></td>";
					$html .="</tr>";
					$k++;

				}
			}
			$array_respuesta['select']=$select_comprobantes;
			$array_respuesta['html']=utf8_encode($html);
			echo json_encode($array_respuesta);
			break;
		}

		case "consecutivo":
		{
			//----
			//-Traigo el ultimo consecutivo
			$array_respuesta= array();
			$select_consecutivo = "SELECT MAX(Consecutivo) as consecutivo
									 FROM ".$wbasedatoimpuesto."_000012 ";
			$res = 	mysql_query($select_consecutivo,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select_consecutivo." - ".mysql_error());
			$row = mysql_fetch_array($res);
			$wconsecutivo = $row['consecutivo'];
			$wconsecutivo = ($wconsecutivo*1)+1;
			$array_respuesta['consecutivo'] = "<b>#".$wconsecutivo."</b>";
			echo json_encode($array_respuesta);
			break;
			//----------------------------

		}

		case "traer_consecutivo":
		{
			$key = substr($user,2,strlen($user));
			$qnombreUsuario = "SELECT Descripcion
								FROM usuarios
							   WHERE Codigo='".$key."'";


			$res = mysql_query($qnombreUsuario,$conex);
			if($rowUsuario = mysql_fetch_array($res))
				$nombreUsuario = $rowUsuario['Descripcion'];

			$query = "SELECT * FROM ".$wbasedatoimpuesto."_000012
					   WHERE consecutivo='".$numero."'";

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				echo "<table border=1 align='center'>";
				echo "<tr><td rowspan=4 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.png' ></td>";
				echo "<td colspan=2><font size=5>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
				echo "<tr><td colspan=2><font size=4 aling=center>NIT : 800.067.065-9</font><td></tr>";
				echo "<tr><td  colspan=2><font size=3>COMPROBANTE DE COMPRA DE BIENES O SERVICIOS</font></td></tr>";
				echo "<tr><td colspan=2 align=center><font size=1><b>PERSONAS NATURALES NO COMERCIANTES O INSCRITOS EN EL REGIMEN SIMPLIFICADO</b></font></td></tr>";
				echo "<tr><td><b>COMPROBANTE NRO. ".$row[3]."</b></td>";
				echo "<td align=right colspan=2>Fecha de Documento.".$row[4]."</td></tr>";
				echo "<tr><td>Identificaci&oacute;n: ".$row[5]."</td>";
				echo "<td  colspan=2 align=right>Telefono: ".$row[9]."</td></tr>";
				echo "<tr><td colspan=3>Apellidos y Nombres: ".$row[6]."</td></tr>";
				echo "<tr><td>Direcci&oacute;n: ".$row[7]."</td>";
				echo "<td  colspan=2 align=right>Ciudad: ".$row[8]."</td></tr>";
				echo "<tr><td colspan=3>Concepto: ".$row[10]."</td></tr>";
				echo "<tr><td>Valor  : </td>";
				echo "<td  colspan=2 align=right>".number_format((double)$row[11],2,'.',',')."</td></tr>";
				echo "<tr><td>Impuesto Asumido : </td>";
				echo "<td  colspan=2 align=right>".number_format((double)$row[14],2,'.',',')."</td></tr>";
				echo "<tr><td>Total : </td>";
				echo "<td  colspan=2 align=right>".number_format((double)$row[15],2,'.',',')."</td></tr>";
				echo "<tr><td><b>Centro de Costos</b></td><td align=center><b>Porcentaje</b></td><td align=right><b>Valor</b></td></tr>";

				$query = "select * from ".$wbasedatoimpuesto."_000013 where consecutivo='".$row[3]."-".$row[5]."' order by porcentaje";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$totpor=0;
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$parcial=(double)$row[15]*((double)$row1[5]/100);
					$totpor=$totpor+$row1[5];
					echo "<tr><td>".$row1[4]."</td><td align=center>".$row1[5]."</td><td align=right>".number_format($parcial,2,'.',',')."</td></tr>";
				}
				echo "<tr><td colspan=3>Observaciones: ".$row[16]."</td></tr>";
				$blanco=".";
				echo "<tr><td><font size=20>".$blanco."</font></td><td><font size=20>".$blanco."</font></td><td><font size=20>".$blanco."</font></td></tr>";
				echo "<tr><td><b>Firma Proveedor</b></td><td><b>Elaborado Por</b></td><td><b>Recibido Por</b></td></tr>";

				if(number_format($totpor,2,'.',',') == 100.00 )
				{
					$codigouser = explode('-',$row['Seguridad']);

					$qnombreUsuario = "SELECT Descripcion
								FROM usuarios
							   WHERE Codigo='".$codigouser[1]."'";


					$res = mysql_query($qnombreUsuario,$conex);
					if($rowUsuario = mysql_fetch_array($res))
						$nombreUsuario = $rowUsuario['Descripcion'];

					echo "<tr><td colspan=3><b>Elaborado por : ".$codigouser[1]."-".$nombreUsuario."</b></td></tr>";

				}
				else
				{
					$codigouser = explode('-',$row['Seguridad']);

					$qnombreUsuario = "SELECT Descripcion
								FROM usuarios
							   WHERE Codigo='".$codigouser[1]."'";


					$res = mysql_query($qnombreUsuario,$conex);
					if($rowUsuario = mysql_fetch_array($res))
						$nombreUsuario = $rowUsuario['Descripcion'];
					echo "<tr><td colspan=3 bgcolor=#ff0000><b>ERROR : LA DISTRIBUCION DE PORCENTAJES NO SUMA EL 100%</b></td></tr>";
					echo "<tr><td colspan=3><b>Elaborado por : ".$codigouser[1]."-".$nombreUsuario." </b></td></tr>";
				}
				echo "<tr><td colspan=3><b>Fecha del Registro</b> ".$row[1]."</td></tr>";

				echo "</table>";
			}


			break;
		}

		case "traer_datos" :
		{
			$key = substr($user,2,strlen($user));
			$respuesta = array();
			$qnombreUsuario = "SELECT Descripcion
								FROM usuarios
							   WHERE Codigo='".$key."'";
			$res = mysql_query($qnombreUsuario,$conex);
			if($rowUsuario = mysql_fetch_array($res))
				$nombreUsuario = $rowUsuario['Descripcion'];

			$query = "SELECT a.*, b.Nombres FROM ".$wbasedatoimpuesto."_000012 a, {$wbasedatoimpuesto}_000001 b
					   WHERE consecutivo='".$numero."'
					     AND b.Cedula_nit = a.identificacion";


			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{

				$row = mysql_fetch_array($err);
				$respuesta['comprobanteNumero'] =$row[3];
				$respuesta['fechaoperacion']    =$row[4];
				$respuesta['identificacion']	   =$row[5];
				$respuesta['telefono']		   =$row[9];
				$respuesta['nombre'] 		   = utf8_encode($row['Nombres']);
				$respuesta['direccion']=$row[7];
				$respuesta['ciudad']=$row[8];
				$respuesta['concepto']= utf8_encode($row[10]);
				$respuesta['valor'] =number_format((double)$row[11],2,'.',',');
				$respuesta['valor2'] =$row[11];
				$respuesta['Tarifa']=$row[12];
				$respuesta['Topes']=$row[13];

				$respuesta['impuesto'] =number_format((double)$row[14],2,'.',',');
				$respuesta['impuesto2'] =$row[14];
				$respuesta['total']=number_format((double)$row[15],2,'.',',');
				$respuesta['total2']=$row[15];
				$query = "select * from ".$wbasedatoimpuesto."_000013 where consecutivo='".$row[3]."-".$row[5]."' order by porcentaje";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$totpor=0;
				$porcentajes ='';
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err1);
					$parcial=(double)$row[15]*((double)$row1[5]/100);
					$totpor=$totpor+$row1[5];
					$auxiliar ='';
					$auxiliar =explode( "-",$row1[4]);
					$porcentajes .= "
					<tr class='fila2 trcco' id='tr_cco".$auxiliar[0]."' centrodecostos='".$row1[4]."' codigocentrodecostos='".$auxiliar[0]."' ><td width='103px'>&nbsp;</td><td  width='190px' id='td_cco".$auxiliar[0]."'>".$row1[4]."</td><td width='200px'>&nbsp;</td><td><input type='text' id='input_".$auxiliar[0]."' class='solo_numeros validar'  value='".$row1[5]."'></td><td>&nbsp;<img width='15' height='15' src='../../images/medical/root/borrar.png' style='cursor:pointer;' onclick='borrarcco(".$auxiliar[0].")'></td></tr>";

				}
				$respuesta['porcentajes'] = $porcentajes;
				$respuesta['observaciones']=utf8_encode($row[16]);


				// $blanco=".";
				// echo "<tr><td><font size=20>".$blanco."</font></td><td><font size=20>".$blanco."</font></td><td><font size=20>".$blanco."</font></td></tr>";
				// echo "<tr><td><b>Firma Proveedor</b></td><td><b>Elaborado Por</b></td><td><b>Recibido Por</b></td></tr>";

				// if(number_format($totpor,2,'.',',') == 100.00 )
				// {
					// echo "<tr><td colspan=3><b>Elaborado por : ".$key." ".$nombreUsuario."</b></td></tr>";
				// }
				// else
				// {
					// echo "<tr><td colspan=3 bgcolor=#ff0000><b>ERROR : LA DISTRIBUCION DE PORCENTAJES NO SUMA EL 100%</b></td></tr>";
					// echo "<tr><td colspan=3><b>Elaborado por : ".$key." ".$nombreUsuario." </b></td></tr>";
				// }
				// echo "<tr><td colspan=3><b>Fecha de elaboraci&oacute;n:</b> ".$row[1]."</td></tr>";

				// echo "</table>";
			}
			//echo "<pre>".print_r($respuesta, true)."</pre>";
			echo json_encode($respuesta);
			break;
		}

		case "fechayhora" :
		{
			$array_respuesta= array();
			$wfecha = date("Y-m-d");
			$whora  = date("H:i:s");

			$array_respuesta['fecha'] =  "&nbsp;&nbsp;".$wfecha."&nbsp;&nbsp;Hora: ".$whora;
			$array_respuesta['fechadatepicker'] =  $wfecha;
			echo json_encode($array_respuesta);
			break;

		}

	}
	return;

}



?>
 <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php


					encabezado("Comprobantes", $wactualiz, "clinica");

					echo '
					<style type="text/css">
						fieldset{
							border: 2px solid #e0e0e0;
						}
						legend{
							border: 2px solid #e0e0e0;
							border-top: 0px;
							font-family: Verdana;
							background-color: #e6e6e6;
							font-size: 10pt;
						}
						.fila1{
							background-color: #C3D9FF;
							color: #000000;
							font-size: 8pt;
							padding:1px;
							font-family: verdana;
						}
						.fila2{
							background-color: #E8EEF7;
							color: #000000;
							font-size: 8pt;
							padding:1px;
							font-family: verdana;
						}
						.encabezadoTabla{
							background-color: #2a5db0;
							color: #ffffff;
							font-size: 8pt;
							padding:1px;
							font-family: verdana;
							fond-weight: bold;
						}
						.listaProPendiente{
							border: 1px solid red;
						}
						.ui-autocomplete{
							max-width: 	290px;
							max-height: 150px;
							overflow-y: auto;
							overflow-x: hidden;
							font-size: 	7pt;
						}
					</style>';

					$user_session = explode('-',$_SESSION['user']);
					$user_session = (count($user_session) > 1)? $user_session[1] : $user_session[0];
					//$user_session = (strlen($user_session) > 5) ? substr($user_session,-5): $user_session;
					$wusuario = $user_session;
					$wbasedatoimpuesto 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'impuesto');
					// oculto de wempmla
					$html.="<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
					//----------------------------------------

					$wfecha = date("Y-m-d");
					$whora = date("H:i:s");

					//---
					//Construyo vector con responsables y lo pongo en campo oculto
					//---
					$arr = array();
					$select_res = "SELECT Cedula_nit, Nombres, Direccion, Telefonos, Ciudad
									 FROM ".$wbasedatoimpuesto."_000001
									ORDER BY Nombres";

					$res = 	mysql_query($select_res,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$select_res." - ".mysql_error());
					while($row = mysql_fetch_array($res))
					{
						$arr[utf8_encode(trim($row['Cedula_nit']))] = trim(utf8_encode($row['Nombres']));
					}
					$arr['*']	= 'TODOS';
					$arr['']	= 'NO APLICA';
					$cuantos = count($arr);
					$html.= '<input cuantos="'.$cuantos.'" type="hidden" id="hidden_responsables" value=\''.json_encode($arr).'\' >';
					//---------------------------------------------------------

					$arr2 = array();
					/*$select = "SELECT subcodigo, codigo ,descripcion
								 FROM det_selecciones
								WHERE medico ='".$wbasedatoimpuesto."'
								  AND codigo ='00000005'
								  AND activo = 'A'";

					$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
					while($row = mysql_fetch_array($res))
					{
						$arr2[utf8_encode(trim($row['subcodigo']))] = trim(utf8_encode($row['descripcion']));
					}*/


					$select = "SELECT Ccocod,Cconom
								 FROM costosyp_000005
								WHERE Ccoest = 'on'";

					$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());
					while($row = mysql_fetch_array($res))
					{
						$arr2[utf8_encode(trim($row['Ccocod']))] = trim(utf8_encode($row['Cconom']));
					}

					$arr2['*']	= 'TODOS';
					$arr2['']	= 'NO APLICA';

					$cuantos = count($arr2);
					$html.= '<input cuantos="'.$cuantos.'" type="hidden" id="hidden_cco" value=\''.json_encode($arr2).'\' >';
					//----------------------------------------------------------


					//----
					//-Traigo el ultimo consecutivo
					$select_consecutivo = "SELECT MAX(Consecutivo) as consecutivo
											 FROM ".$wbasedatoimpuesto."_000012 ";
					$res = 	mysql_query($select_consecutivo,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select_consecutivo." - ".mysql_error());
					$row = mysql_fetch_array($res);
					$wconsecutivo = $row['consecutivo'];
					$wconsecutivo = ($wconsecutivo*1)+1;
					//----------------------------
					/*
					//-----------------
					//-construyo el select de tarifas desde la tabla selecciones
					//--------
					$select = "SELECT subcodigo, codigo ,descripcion
								 FROM det_selecciones
								WHERE medico ='".$wbasedatoimpuesto."'
								  AND codigo ='00000004'
								  AND activo = 'A'";

					$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

					$select_tarifa .="<select id='select_tarifa' class='validar' onchange='calcular_valores()'>";
					$select_tarifa .="<option value=''>Seleccione...</option>";
					while($row = mysql_fetch_array($res))
					{
						$select_tarifa .="<option value='".$row['subcodigo']."-".$row['descripcion']."'>".$row['subcodigo']."-".$row['descripcion']."</option>";
					}
					$select_tarifa .="</select>";
					//------------------------
					*/



					//-----------------
					//-construyo el select de tarifas desde la tabla selecciones
					//--------
					$select = "SELECT Impval, Impdes
								 FROM ".$wbasedatoimpuesto."_000004
								WHERE Impest='on'";

					$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

					$select_tarifa .="<select id='select_tarifa' class='validar' onchange='calcular_valores()'>";
					$select_tarifa .="<option value=''>Seleccione...</option>";
					while($row = mysql_fetch_array($res))
					{
						$select_tarifa .="<option value='".$row['Impval']."-".$row['Impdes']."'>".$row['Impval']."-".$row['Impdes']."</option>";
					}
					$select_tarifa .="</select>";


					//--------
					//-construyo el select de Topes desde la tabla selecciones
					//--------
					$select = "SELECT Topval, Topdes
								 FROM ".$wbasedatoimpuesto."_000005
								WHERE Topest='on'";

					$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

					$select_tope .="<select id='select_topes'  style='width:198px' class='validar' onchange='calcular_valores()'>";
					$select_tope .="<option value=''>Seleccione...</option>";
					while($row = mysql_fetch_array($res))
					{
						$select_tope .="<option value='".$row['Topval']."-".$row['Topdes']."'>".$row['Topval']."-".$row['Topdes']."</option>";
					}
					$select_tope .="</select>";
					//------------------------

					//-----------------
					//-construyo el select de centro de costos desde la tabla selecciones
					//--------
					/*$select = "SELECT subcodigo, codigo ,descripcion
								 FROM det_selecciones
								WHERE medico ='".$wbasedatoimpuesto."'
								  AND codigo ='00000005'
								  AND activo = 'A'";

					$res = 	mysql_query($select,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$select." - ".mysql_error());

					$select_cco .="<select id='select_cco'  style='width:198px'  >";
					$select_cco .="<option value=''>Seleccione...</option>";
					while($row = mysql_fetch_array($res))
					{
						$select_cco .="<option value='".$row['subcodigo']."-".$row['descripcion']."'>".$row['subcodigo']."-".$row['descripcion']."</option>";
					}
					$select_cco .="</select>";*/
					$select_cco .="<input type='text' id='buscador_centros_costos' size='33' >";
					//------------------------

					$modeloconolgura='no';

					if ($modeloconolgura=='no')
					{
						$es_visible = "style='display : none'";
						$es_visible2 = "style='display : none'";

					}
					else
					{
						$es_visible2 ="";
						$arrayMeses[1]='Enero';
						$arrayMeses[2]='Febrero';
						$arrayMeses[3]='Marzo';
						$arrayMeses[4]='Abril';
						$arrayMeses[5]='Mayo';
						$arrayMeses[6]='Junio';
						$arrayMeses[7]='Julio';
						$arrayMeses[8]='Agosto';
						$arrayMeses[9]='Septiembre';
						$arrayMeses[10]='Octubre';
						$arrayMeses[11]='Noviembre';
						$arrayMeses[12]='Diciembre';

						$mes = date('m');
						$ano = date('Y');

						$mes = $mes - 1 ;

						$select = "SELECT Impcer
									 FROM ".$wbasedatoimpuesto."_000002
									WHERE Impmes ='".$mes."'
									  AND Impano = '".$ano."'";
						// echo $select;

						$periodoCerrado ='on';
						$res = mysql_query($select,$conex);
						if($row = mysql_fetch_array($res))
						{
							$periodoCerrado = $row['Impcer'];
						}
						$condicion ='';
						if($periodoCerrado=='off')
						{
						   $condicion ="disabled checked";
						   $condicionaux = "disabled";
						   $leyendadelmes='Periodo ya cerrado ';
						}
						else
						{
						   $condicion ="";
						   $condicionaux = "";
						   $leyendadelmes ='Cerrar numeraci&oacute;n' ;
						}
						if ($mes == 0)
						{
							$mes = 12;
							$ano = ($ano*1) -1;
						}

						//--Usuario que puede cerrar los comprobantes del mes
						$usuarioAdministrador	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'AdministradorComprobantes');

						$wauxiliarAdministrador	= explode(",",$usuarioAdministrador);
						$arrayAdministrador     = array();
						$es_visible = "style='display : none'";
						for($t=0;$t<count($wauxiliarAdministrador);$t++)
						{
							if($wauxiliarAdministrador[$t]==$wusuario)
								$es_visible = "";

						}

					}
					$html .= "<input type='hidden' id='modifica' value='no'><div width='95%' id='accordionprincipal'>
								<h3>Comprobante de Facturas</h3>
									<div>
										<fieldset id='fieldsetnuevo'>
										  <legend align='left'>Nuevo comprabante</legend>
										  <div style='padding: 3px;'>

										  <table ".$es_visible." ><tr><td align='left'>".$leyendadelmes." ".$arrayMeses[$mes]." ".$ano." <input type='checkbox' ".$condicion." id='checkboxmesnumeracionpasada' onclick='cerrar_mes(".$mes." , ".$ano.")' ></td></tr></table>

										  <table ".$es_visible2." align='right'><tr><td class='fila1' style='border: 2px solid rgb(226, 183, 9); background-color: rgb(255, 255, 255);'><b>Utilizar numeraci&oacute;n del mes pasado</b></td>
										  <td class='fila2' align='center'><input ".$condicionaux." type='checkbox'  id='checkremanente' ></td></tr></table></td>
										  </tr>
										  </table><br><br>

										  <table align='center'>
										  <tr>
										  <td colspan='4' class='encabezadoTabla' align='center'>Datos del tercero</td>
										  </tr>
										  <tr>
											  <td class='fila1'>Fecha del Registro:</td><td class='fila2' ><div id='div_fechayhora'>&nbsp;&nbsp;".$wfecha."&nbsp;&nbsp;Hora: ".$whora."</div></td>
											  <td class='fila1'>Consecutivo:</td><td class='fila2' align='center'><div id='div_consecutivo' class='limpiar' valor='".$wconsecutivo."'><b>#".$wconsecutivo."</b></div></td>
										  </tr>
										  <tr>
											  <td class='fila1'>Identificaci&oacute;n:</td>
											  <td class='fila2' >
											  <input id='input_identificacion'  class='validar' size='33' >

											  </td>
											  <td class='fila1'>Fecha del documento:</td><td class='fila2' ><input type='text' id='wfechaproceso' class='validar' value='".$wfecha."'></td>
										  </tr>
										  <tr>

											  <td class='fila1'>Nombre:</td><td class='fila2'><div id='div_nombre' class='limpiar' ></div></td>
											  <td class='fila1'>Direcci&oacute;n:</td><td class='fila2'><div id='div_direccion' class='limpiar'></div></td>


										  </tr>
										  <tr>
												<td class='fila1' >Ciudad:</td><td class='fila2'><div id='div_ciudad' class='limpiar'></div></td>
												<td class='fila1' >Telefono:</td><td class='fila2'><div id='div_telefono' class='limpiar'></div></td>
										  </tr>
										  <tr>
												<td colspan='4' class='encabezadoTabla' align='center'>Comprobante</td>
										  </tr>
										  <tr>
												<td class='fila1'>Concepto:</td><td class='fila2' colspan='3'><textarea rows='2' cols='90' id='wconcepto' class='validar'></textarea></td>
										  </tr>
										  <tr>
												<td class='fila1'>Valor Total:</td><td class='fila2'><input id='input_valor' valor='' onblur='calcular_valores()' class='validar'></td>
												<td class='fila1'>Tarifa:</td><td class='fila2'>".$select_tarifa."</td>

										  </tr>
										  <tr>
												<td class='fila1'>Topes:</td><td class='fila2'>".$select_tope."</td>
												<td class='fila1'>Impuesto:</td><td class='fila2'><div id='div_impuesto' class='limpiar' style='display:none'></div><div id='div_impuesto_visible' class='limpiar'></div></td>

										  </tr>
										   <tr>
											<td class='fila1'>Total:</td><td class='fila2' colspan='3'><div id='div_total_auxiliar' class='limpiar'></div><div id='div_total' style='display : none'  class='limpiar'></div></td>
										  </tr>
										  <tr>
											  <td class='fila1'>Observaciones:</td><td class='fila2' colspan='3'><textarea rows='4' cols='90' id='wobservaciones'></textarea></td>
										  </tr>
										  <tr>
												<td colspan='4' class='encabezadoTabla' align='center'>Cuentas por pagar</td>
										  </tr>
										  <tr>
											<td class='fila1'>Centro de costo:</td><td class='fila2'>".$select_cco."</td><td class='fila1' >Porcentaje:</td><td class='fila2'><input type='text' id='input_porcentaje'>&nbsp;&nbsp;<img width='15' height='15' src='../../images/medical/root/adicionar2.png' style='cursor:pointer;' title='Click para agregar centro de costo y porcentaje' onclick='adicionarcco()'></td>
										  </tr>
										  <tr id='tr_cco' style='display:none'>
											<td class='fila2' id='td_cco'  colspan='4'><td>
										  <tr>
										  <tr>
												<td colspan='4' align='center'>
												<input type='button' id='botongrabar' value='Grabar' onclick='grabarComprobante()' style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' >
												<input type='button' value='Limpiar' onclick='limpiar()' style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' >
												</td>
										  </tr>
										  </table>
										  </div>
									    </fieldset>
										<br>
										<fieldset id='fieldsetconsultar'>
										  <legend align='left'>Consulta</legend>
											<div style='padding: 3px;' id='resultado_consulta'>
											";

					$arrayTerceros        = array();
					$queryTerceros        = "SELECT Cedula_nit, nombres
					                           FROM {$wbasedatoimpuesto}_000001";
					$rsTerceros           = mysql_query($queryTerceros, $conex);
					while( $rowTerceros = mysql_fetch_assoc($rsTerceros) ){
						$arrayTerceros[$rowTerceros['Cedula_nit']] = $rowTerceros['nombres'];
					}

					$select_comprobantes  = "  SELECT Consecutivo,Fecha_proceso,Identificacion,Nombres,Direccion,Ciudad,Telefono,Concepto,Valor,Tarifa,Topes,Impuesto,Total,Observaciones ,Fecha_data
											     FROM  ".$wbasedatoimpuesto."_000012 a
											 ORDER BY Consecutivo DESC
											    LIMIT 100";


					if($res = mysql_query($select_comprobantes,$conex))
					{
						$usuarioAdministrador	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'AdministradorComprobantes');
						$wauxiliarAdministrador	= explode(",",$usuarioAdministrador);
						$arrayAdministrador     = array();
						$es_visible = "style='display : none'";
						for($t=0;$t<count($wauxiliarAdministrador);$t++)
						{
							if($wauxiliarAdministrador[$t]==$wusuario)
								$es_visible = "";

						}

						$html .="<div scrollAlto='si' style='text-align:justify'>
									<table align='center'  width='50%'>
									    <tr><td class='encabezadoTabla'>Buscar:</td><td  class='encabezadoTabla' nowrap='nowrap'><input id='buscador' ></td>
										<td class='encabezadoTabla' nowrap='nowrap'>Mes: <Select id='buscador_por_mes'>
										<option value='0'>Seleccione</option>
										<option value='01'>Enero</option>
										<option value='02'>Febrero</option>
										<option value='03'>Marzo</option>
										<option value='04'>Abril</option>
										<option value='05'>Mayo</option>
										<option value='06'>Junio</option>
										<option value='07'>Julio</option>
										<option value='08'>Agosto</option>
										<option value='09'>Septiembre</option>
										<option value='10'>Octubre</option>
										<option value='11'>Noviembre</option>
										<option value='12'>Diciembre</option>
										</select></td>
										<td class='encabezadoTabla' nowrap='nowrap'>Ano: <Select id='buscador_por_ano'>
										<option value='0'>Seleccione</option>
										<option value='".((date('Y')))."'>".date('Y')."</option>
										<option value='".((date('Y')*1)-1)."'>".((date('Y')*1)-1)."</option>
										<option value='".((date('Y')*1)-2)."'>".((date('Y')*1)-2)."</option>
										</select></td>
										<td><input type='button' value='Buscar' onclick='buscar(\"no\")' style='cursor:pointer;padding:1px;font-family:verdana;font-weight:bold;font-size: 8pt;'></td>
										<td colspan='5'></td></tr>
										</table>
										<br>
										<table align='center'  width='98%'>
										<tr class='encabezadoTabla' align='center'>
											<td width='90px'>Consecutivo</td>
											<td width='50px'>Fecha Documento</td>
											<td width='50px'>Fecha Registro</td>
											<td width='90px'>Identificacion</td>
											<td width='130px'>Nombre</td>
											<td width='130px' >Direcci&oacute;n</td>
											<td width='90px' >Ciudad</td>
											<td width='130px' >Telefono</td>
											<td>Concepto</td>
											<td width='10px' ".$es_visible .">Modificacion</td>
											<td width='10px' ".$es_visible .">Editar</td>
											<td width='10px'>Ver</td>
										</tr>";
						while($row = mysql_fetch_array($res))
						{

							if (is_int ($k/2))
							   {
								$wcf="fila1";  // color de fondo de la fila
							   }
							else
							   {
								$wcf="fila2"; // color de fondo de la fila
							   }

							$html .="<tr class='".$wcf."'>";
							$html .="<td>".$row['Consecutivo']."</td>";
							$html .="<td>".$row['Fecha_proceso']."</td>";
							$html .="<td>".$row['Fecha_data']."</td>";
							$html .="<td>".$row['Identificacion']."</td>";
							$html .="<td>".$arrayTerceros[$row['Identificacion']]."</td>";
							$html .="<td>".$row['Direccion']."</td>";
							$html .="<td>".$row['Ciudad']."</td>";
							$html .="<td>".$row['Telefono']."</td>";
							$html .="<td>".substr($row['Concepto'],0,50)."</td>";

							$selectlog = "SELECT * FROM ".$wbasedatoimpuesto."_000003 WHERE Regreg='".$row['Consecutivo']."'";
							$modificable = 'no';
							if($reslog = mysql_query($selectlog,$conex))
							{
								while($rowlog = mysql_fetch_array($reslog))
								{
									$modificadopor .= $rowlog['Seguridad'] ;
									$fechapor 	   .= $rowlog['Fecha_data'] ;
									$modificable = 'si';
								}
							}

							$html .="<td  ".$es_visible ." style='cursor:pointer' align='center' >".(($modificable =='no') ? " " : "<input onclick='verModificaciones(".$row['Consecutivo'].")' type='button'  value='SI'>")."</td>";
							$html .="<td  ".$es_visible ." style='cursor:pointer' onclick='editar(".$row['Consecutivo'].")'><u>Editar</u> </td>";
							$html .="<td style='cursor:pointer' onclick='ver(".$row['Consecutivo'].")'><u>ver</u></td>";
							$html .="</tr>";
							$k++;
						}

						$html .="</table></div>";

					}

					$html .= "		   </div>
									   </fieldset>
					</div></div>
					<br><center><div><input  type='button' value='Cerrar' onclick='window.close();'></div></center>";
					$html .="<div id='div_modal' style='display:none'>";
					$html .="</div>";
					$html .="<div id='div_modal2' style='display:none'>";
					$html .="</div>";
					echo $html;

			?>
    </body>
</html>