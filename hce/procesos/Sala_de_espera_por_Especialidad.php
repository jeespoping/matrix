<?php
include_once("conex.php");
session_start();
if (!isset($consultaAjax))
{
?>
<html>
<head>
  <title>SALA DE ESPERA URGENCIAS</title>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
  <script type="text/javascript">

	$(function(){
		// --> Tooltip en la lista de turnos
		$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		pedirConsultorio();

		// --> Si existe un paciente en consulta, inhabilito los botones de llamado
		if($("#hayPacienteEnConsulta").val())
		{
			$(".botonLlamarPaciente").hide();
		}
		else
		{
			// --> Si existe un turno que ya haya sido llamado por este usuario, inhabilito los demas
			if($("#turnoLlamadoPorEsteUsuario").val() != '')
			{
				var idTurno = $("#turnoLlamadoPorEsteUsuario").val();
				$(".botonLlamarPaciente").hide();
				$(".botonColgarPaciente").hide();
				$("#imgLlamar"+idTurno).hide();
				$("#imgLlamar"+idTurno).next().show();
				$("#imgLlamar"+idTurno).next().next().show();
				$("[botonIrConsulta="+idTurno+"]").show();
				$("#trTurno_"+idTurno).attr("classAnterior", $("#trTurno_"+idTurno).attr("class"));
				$("#trTurno_"+idTurno).attr("class", "llamadoPac");
			}
		}
		
		$(".msg").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		
		// --> Blink
		setInterval(function(){
			$("span[blink]").each(function(){
				$(this).css('visibility' , $(this).css('visibility') === 'hidden' ? '' : 'hidden');
			});
		}, 500);
	});
	
	function cancelarTrasladoPaciente(turno)
	{
		$.post("Sala_de_espera_por_Especialidad.php",
		{
			consultaAjax:   		'cancelarTrasladoPaciente',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno
		}, function(respuesta){
			
			if(respuesta.error == 1){
				
			}else{
			
			$(".botonLlamarPaciente").show();
			$(".botonColgarPaciente").hide();
			$("[botonIrConsulta="+turno+"]").hide();
			$("#trTurno_"+turno).attr("class", $("#trTurno_"+turno).attr("classAnterior"));
			
			}
		});
	}

	//-------------------------------------------------------------
	// --> Pedir el consultorio en el cual va a trabajar el medico
	//-------------------------------------------------------------
	function pedirConsultorio()
	{
		if($("#puestoTrabajo").val() == '')
		{
			$("#ventanaPedirConsultorio").dialog({
				modal	: true,
				width	: 350,
				title	: "<div align='center'>Bienvenido.</div>",
				close: function( event, ui ) {
					pedirConsultorio();
				}
			});
		}
		else
			$( "#ventanaPedirConsultorio" ).dialog( "close" );
	}
	//-------------------------------------------------------------
	// --> Actualiza el usuario asociado a un puesto de trabajo
	//-------------------------------------------------------------
	function cambiarPuestoTrabajo(Elemento)
	{
		$.post("Sala_de_espera_por_Especialidad.php",
		{
			consultaAjax:   		'cambiarPuestoTrabajo',
			wemp_pmla:        		$('#wemp_pmla').val(),
			puestoTrabajo:			$(Elemento).val()
		}, function(respuesta){

			if(respuesta.Error)
			{
				if(confirm("Este consultorio ya esta ocupado por "+respuesta.Usuario+". \n ¿Desea liberarlo?"))
				{
					// --> Liberar un puesto de trabajo
					$.post("Sala_de_espera_por_Especialidad.php",
					{
						consultaAjax:   		'liberarPuestoTrabajo',
						wemp_pmla:        		$('#wemp_pmla').val(),
						puestoTrabajo:			$(Elemento).val()
					}, function(respuesta){
						$("#puestoTrabajo").val($(Elemento).val());
						$("#puestoTrabajo2").val($(Elemento).val());
						$("#puestoTrabajo").attr("consultorioActUsu", $(Elemento).val());
						pedirConsultorio();
					});
				}
				else
				{
					$("#puestoTrabajo").val($("#puestoTrabajo").attr("consultorioActUsu"));
					$("#puestoTrabajo2").val($("#puestoTrabajo").attr("consultorioActUsu"));
					pedirConsultorio();
				}
			}
			else
			{
				$("#puestoTrabajo").val($(Elemento).val());
				$("#puestoTrabajo2").val($(Elemento).val());
				$("#puestoTrabajo").attr("consultorioActUsu", $(Elemento).val());
				pedirConsultorio();

			}
		}, 'json');
	}
	//-----------------------------------------------------------------------
	// --> Funcion que genera el llamado del paciente para que sea atendido
	//-----------------------------------------------------------------------
	function llamarPacienteAtencion(historia, ingreso, turno)
	{
		var continuar = true;
		// --> Validar si ya se tiene un paciente en consulta
		$("[botonIrConsulta]").each(function(){
			if($(this).attr('checked') == 'checked')
			{
				alert("No se puede realizar el llamado, ya que existe una consulta en proceso con:\n"+$(this).attr("nomPac")+".");
				continuar = false;
			}
		});

		if(continuar)
		{
			$.post("Sala_de_espera_por_Especialidad.php",
			{
				consultaAjax:   		'llamarPacienteAtencion',
				wemp_pmla:        		$('#wemp_pmla').val(),
				turno:					turno,
				historia:				historia,
				ingreso:				ingreso,
				consultorio:			$("#puestoTrabajo").val()
			}, function(respuesta){
				if(respuesta.Error)
				{
					alert(respuesta.Mensaje);
				}
				else
				{
					$(".botonLlamarPaciente").hide();
					$(".botonColgarPaciente").hide();
					$("#imgLlamar"+turno).hide();
					$("#imgLlamar"+turno).next().show();
					$("#imgLlamar"+turno).next().next().show();
					$("[botonIrConsulta="+turno+"]").show();
					$("#trTurno_"+turno).attr("classAnterior", $("#trTurno_"+turno).attr("class"));
					$("#trTurno_"+turno).attr("class", "llamadoPac");
				}
			}, 'json');
		}
	}
	//-----------------------------------------------------------------------
	// --> Funcion que cancela el llamado del paciente a la consulta
	//-----------------------------------------------------------------------
	function cancelarLlamarPacienteConsulta(turno)
	{
		$.post("Sala_de_espera_por_Especialidad.php",
		{
			consultaAjax:   		'cancelarLlamarPacienteConsulta',
			wemp_pmla:        		$('#wemp_pmla').val(),
			turno:					turno
		}, function(respuesta){

			$(".botonLlamarPaciente").show();
			$(".botonColgarPaciente").hide();
			$("[botonIrConsulta="+turno+"]").hide();
			$("#trTurno_"+turno).attr("class", $("#trTurno_"+turno).attr("classAnterior"));
		});
	}

	function filtrarCubiculo(whis, wing, i, atendidos, cubiculoActual, irhce){

		var prefijo_atendidos = "";

		if(irhce == 'on'){

		prefijo_atendidos = "_atendidos";

		}

		$.ajax({
			url: "Sala_de_espera_por_Especialidad.php",
			type: "POST",
			data:{
				wemp_pmla		: $("#wemp_pmla").val(),
				wsala			: $("#sala"+prefijo_atendidos+i).val(),
				consultaAjax 	: 'filtrarCubiculo',
				posicion		: i,
				whis 			: whis,
				wing			: wing,
				atendidos		: atendidos,
				cubiculoActual	: cubiculoActual
			},
			dataType: "json",
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{

					$("#dato_cubiculos"+prefijo_atendidos+i).html(data_json.html);

				}
			}

		});

	}

	function reasginarCubiculo(his, ing, i, irhce, cubiculoActual)
	  {
	    var wok=true;

		if(irhce == 'on'){

		var atendidos = "_atendidos";
		}

		var cod_cubiculo = $("#cubiculo"+atendidos+i).val();

	    if (wok==true)
		   {
			var parametros = "consultaAjax=reasignar_cubiculo&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&wbasedato="+document.forms.sala.wbasedato.value+"&whis="+his+"&wing="+ing+"&wusuario="+document.forms.sala.wusuario.value+"&wcubiculoActual="+cubiculoActual+"&wcubiculoNuevo="+cod_cubiculo;

			try
			  {
				var ajax = nuevoAjax();

				ajax.open("POST", "Sala_de_espera_por_Especialidad.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				if (ajax.readyState==4 && ajax.status==200)
					{
					 if(ajax.responseText!="ok"){

						if(ajax.responseText == "ya_asignado"){

							$("#cubiculo_aux"+i).val(cubiculoActual);
							alert("El cubiculo se encuentra ocupado, favor asignar otro que se encuentre disponible.");

							}
						}

						if(ajax.responseText == "ya_asignado_historia"){

							$("#cubiculo_aux"+i).val(cubiculoActual);
							alert("El paciente ya tiene cubiculo asignado.");

							}


						enter();
					}
			  }catch(e){    }

		   }
	  }


	function colocarCubiculo(his, ing, i, irhce, fecha_term_cons, hora_term_consulta)
	  {
	    var wok=true;
		var cod_cubiculo = document.getElementById("cubiculo"+i).value;
		var wok=document.getElementById("wirhce"+i.toString()).checked;

	    if (wok==true)
		   {
			var parametros = "consultaAjax=cubiculo&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&wbasedato="+document.forms.sala.wbasedato.value+"&whis="+his+"&wing="+ing+"&wusuario="+document.forms.sala.wusuario.value+"&wconducta="+document.getElementById("conducta"+i).value+"&wfecha_term_consul="+fecha_term_cons+"&whora_term_consulta="+hora_term_consulta+"&wcubiculo="+cod_cubiculo;

			try
			  {
				var ajax = nuevoAjax();

				ajax.open("POST", "Sala_de_espera_por_Especialidad.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				if (ajax.readyState==4 && ajax.status==200)
					{
					 if(ajax.responseText!="ok"){

						if(ajax.responseText == "ya_ubicado"){

							alert("El paciente ya se encuentra en un cubiculo, será mostrado en la lista de pacientes atendidos y activos.");
							enter();
							return;
							}

						if(ajax.responseText == "ya_asignado"){

							$("#cubiculo"+i).val();
							alert("El cubiculo se encuentra asignado, favor asignar otro diferente a este.");

							}

						}

					}
			  }catch(e){    }

			enter();

		   }else{

			    document.getElementById("cubiculo"+i.toString()).value='';
				alert ("Debe ingresar a la HCE antes de seleciconar un cubiculo.");
				return false;

		   }
	  }


    function validarConsulta(i)
	   {
	    var cont1 = 1;

		while(document.getElementById("wirhce"+cont1.toString()))
		  {
		    if ((document.getElementById("wirhce"+cont1.toString()).checked) && (cont1 != i))
			   {
			    document.getElementById("wirhce"+i.toString()).checked=false;
				alert ("No es posible tener dos consultas al mismo tiempo");
				return false;
			   }
			cont1++;
		  }
        return true;
	   }


	function validarConducta(i, irhce)
	   {

	    var cont1 = 1;

		while(document.getElementById("wirhce"+cont1.toString()))
		  {
              //console.log(document.getElementById("wirhce"+cont1.toString()).checked);
		    if (irhce != 'on')
			   {
				if ((document.getElementById("wirhce"+cont1.toString()).checked==false) && (cont1 == i) && (document.getElementById("conducta"+i.toString()).value)!='' && (document.getElementById("conducta"+i.toString()).value)!=' ')
				   {
					document.getElementById("conducta"+i.toString()).value='';
					alert ("Debe ingresar a la HCE antes de tomar una conducta");
					return false;
				   }
			   }
			cont1++;
		  }

        if (irhce != 'on')
            {
            var triage=document.getElementById("triage"+i.toString()).value;

            if (triage == '')
                {

                document.getElementById("conducta"+i.toString()).value='';
                alert ("Debe seleccionar un nivel de triage");

                        return false;
                }
                else
                    {
                    return true;
                    }
            }
			else
			{
			return true;
			}
	   }

    function activarConsulta(his, ing, doc, tid, i, irhce, especialidad, wemp_pmla, especi_usuario, fecha_consulta, hora_consulta, medico_asociado)
	  {

			if (irhce == 'on')
			{
				wok = true;
			}
			else
			{
			wok=validarConsulta(i);
			}

			if (wok==true)
			   {
				var parametros = "consultaAjax=activarcur&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&wusuario="+document.forms.sala.wusuario.value+"&irhce="+irhce+"&wespecialidad="+especialidad+"&wemp_pmla="+wemp_pmla+"&wesp_usuario="+especi_usuario+"&fecha_consulta="+fecha_consulta+"&hora_consulta="+hora_consulta+"&wmedico_asociado="+medico_asociado;
			try{
				//$.blockUI({ message: $('#msjEspere') });
				var ajax = nuevoAjax();

				ajax.open("POST", "Sala_de_espera_por_Especialidad.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);

				//ajax.onreadystatechange=function()
				//{
					if (ajax.readyState==4 && ajax.status==200)
					{
						//if(ajax.responseText!="ok")
						//	alert(ajax.responseText);
					}
				//}
				//if ( !estaEnProceso(ajax) ) {
				//    ajax.send(null);
				//}
				}catch(e){    }

				//LLamado a la historia HCE

				//location.href="HCE_iFrames.php?empresa="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&accion=M&ok=0&wcedula="+doc+"&wtipodoc="+tid;
				url="HCE_iFrames.php?empresa="+document.forms.sala.whce.value+"&whis="+his+"&wing="+ing+"&accion=F&ok=0&wcedula="+doc+"&wtipodoc="+tid+"&wdbmhos="+document.forms.sala.wbasedato.value+"&origen="+document.forms.sala.wemp_pmla.value;
				//open(url,'','top=50,left=100,width=960,height=940') ;
				//open(url,'',resizable='yes') ;
				window.open(url,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
				}
				if (irhce=="on")
		           {
		            document.getElementById('wirhce_atenact'+i).checked=false;
			       }
	  }


	function colocarConducta(his, ing, i, irhce, fecha_term_cons, hora_term_consulta, proc_traslado, cond_actual, obj, alta_proceso, id_fila)
	  {

		var prefijo_atendidos = "";

		//Esta variable (irhce) es on cuando el paciente ya esta atendido, osea, se encuentra en la lista inferior.
		if(irhce == 'on'){

			var prefijo_atendidos = "_atendidos";

		}

	    var wok=validarConducta(i, irhce);
		var datos_conducta = document.getElementById("conducta"+prefijo_atendidos+i).value;
		var dato_cubiculo = datos_conducta.split("-");
		var cubiculo = dato_cubiculo[2];

		//Validar proceso de traslado.
		if(proc_traslado == 'on' && dato_cubiculo == ''){

			$(obj).val($(obj).attr("conducta_atendidos"+i));
			alert("No puede asignarle esta conducta ya que el paciente esta en proceso de traslado.");
			return;

		}

	    if (wok==true)
		   {
			
			//--
			$.ajax({
			url: "Sala_de_espera_por_Especialidad.php",
			type: "POST",
			data:{
					consultaAjax 		: 'conducta',
					wemp_pmla 			: $("#wemp_pmla").val(),
					whce 				: $("#whce").val(),
					wbasedato 			: $("#wbasedato").val(),
					whis				: his,
					wing				: ing,
					wusuario			: $("#wusuario").val(),
					wconducta			: $("#conducta"+prefijo_atendidos+i).val(),
					wfecha_term_consul	: fecha_term_cons,
					whora_term_consulta	: hora_term_consulta,
					wtipo_cubiculo		: cubiculo,
					irhce				: irhce,
					proc_alta			: alta_proceso
			},
			dataType: "json",
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{
					
					$("#tr"+prefijo_atendidos+i).removeClass("fila1");
					$("#tr"+prefijo_atendidos+i).removeClass("fila2");
					$("#tr"+prefijo_atendidos+i).addClass(data_json.cambiar_tr);
						

					}
				}

			});
		

			if (irhce != 'on' && cubiculo != 'on')
				{
				document.getElementById('wirhce'+i).checked=false;
				}

			if(cubiculo != 'on'){
					enter();
				}else{

				$("#sala"+i).removeAttr('disabled');
				$("#sala_aux"+i).removeAttr('disabled');

				//Si tiene sala asignada por defecto, activara el seleccionador de cubiculo.
				if($("#sala"+i).val() != ""){
				$("#cubiculo"+i).removeAttr('disabled');
				}


				}

			//Si el paciente estaba en proceso de alta, cambiara el estilo del tr ya que le cambio de conducta.
			if(alta_proceso == 'on'){

				$("#"+id_fila).removeClass("fondoAmarillo");
				$("#"+id_fila).addClass("fila1");

			}
		   }
	  }


     function colocarTriage(his, ing, i, irhce, fecha, hora, fecha_term_consul, hora_term_consul)
	  {
	    var wok=document.getElementById("wirhce"+i.toString()).checked;
       // console.log(document.getElementById("wirhce"+i.toString()).checked);
	    if (wok == true)
		   {
			var parametros = "consultaAjax=niveltriage&wemp_pmla="+document.forms.sala.wemp_pmla.value+"&whce="+document.forms.sala.whce.value+"&wbasedato="+document.forms.sala.wbasedato.value+"&whis="+his+"&wing="+ing+"&wusuario="+document.forms.sala.wusuario.value+"&wniveltriage="+document.getElementById("triage"+i).value+"&wfecha="+fecha+"&whora="+hora+"&fecha_term_consul="+fecha_term_consul+"&hora_term_consul="+hora_term_consul;

			try
			  {
				var ajax = nuevoAjax();

				ajax.open("POST", "Sala_de_espera_por_Especialidad.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				if (ajax.readyState==4 && ajax.status==200)
					{
					 //if(ajax.responseText!="ok")
					 //	alert(ajax.responseText);
					}
			  }catch(e){    }


		   }
           else
               {
                    document.getElementById("triage"+i.toString()).value='';
					alert ("Debe ingresar a la HCE antes de seleciconar un nivel de triage");
					return false;
               }
  }


	function enter()
	  {
	   document.forms.sala.submit();
	  }
	function enter1()
	  {
	   document.forms.sala.submit();
	  }

	function cerrarVentana()
	  {
       window.close();
      }

	window.onunload=function(){
    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
    }

</script>

<style type="text/css">
	#tooltip{font-family: verdana;font-weight:normal;color: #ffffff;font-size: 7pt;position:absolute;z-index:3000;border:1px solid #000000;background-color:#000000;padding:3px;opacity:1;border-radius: 4px;}
	#tooltip div{margin:0; width:auto;}
	.llamadoPac{
		background-color: #FCC9C9;
	}
</style>

</head>
<body>
<?php
} //Cierra la validacion cuando se hace una consultaAjax

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");

if(!isset($_SESSION['user']))
	echo "error";
else
{

  

  include_once("root/magenta.php");
  include_once("root/comun.php");

  $conex = obtenerConexionBD("matrix");
  $wfecha=date("Y-m-d");
  $whora =(string)date("H:i:s");

  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

/*********************************************************
*               SALA DE ESPERA URGENCIAS                 *
*     				CONEX, FREE => OK				     *
*********************************************************/
//==================================================================================================================================
//PROGRAMA                   : Sala_de_espera_por_Especialidad.php
//AUTOR                      : Juan Carlos Hernández M.
//$wautor="Juan C. Hernandez M. ";
//FECHA CREACION             : Febrero 15 de 2011
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Diciembre 19 de 2021)";
 /* 
//DESCRIPCION
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//     Programa usado para la atencion de los pacientes en Urgencias.                                                                       \\
//     ** Funcionamiento General:                                                                                                           \\
//     En esta pantalla se muestran todos los pacientes que se hallan ingresado a Matrix por el programa de asignacion de medico que        \\
//     tienen los facturadores o auxiliares de admisiones de Urgencias.                                                                     \\
//     Asi: El facturador debe crear el ingreso en el Unix y luego debe ingresarlo a matrix con el programa de asignacion de medico o       \\
//     'agenda_urgencias.php' en el cual se le asigna el medico al paciente antes de ingresar al consultorio, por lo cual el médico solo    \\
//     podrá ver los pacientes que le asignaron a él en esta programa y los que se cencuentren en observacion o procedimientos y que sea    \\
//     factible que él lo pueda ver. Desde este programa también podrá acceder a la HCE y luego de esto debe asignar una conducta a seguir \\
//     con el paciente.                                                                                                                     \\
//         * Tablas: hce_000022      : Medico tratante, consulta Urgencias, conducta a seguir                                               \\
//                   hce_000035      : Maestro de conductas indica si se da de alta o se indica muerte                                      \\
//                   movhos_000018   : Ubicación del paciente y se indica el alta definitiva.
//==========================================================================================================================================\\
 
	Se edita el option vacio, por solicitud de calidad (lady) ya que al seleccionar el vacio el paciente vuelve a quedar 
	en la lista de pacientes en espera y se reinician los tiempos lo que implicaba que los indicadores de oportunidad
	se ampliaran. jerson trujillo 2019-12-16
 //==========================================================================================================================================\\
 * 2021-12-19 Sebastián Nevado
	Se hace modificacion para llevar las funciones esCirugia, y consultarCcoUrgencias a comun.
 
 //==========================================================================================================================================\\
 * 2020-04-01 Juan C. Hdez
	Se hace modificacion para poder ver en la colomna genero medico, los pacientes COVID19.
 
 //==========================================================================================================================================\\
 * 2019-03-05  Arleyda I.C.  -Migración realizada
//==========================================================================================================================================\\
 * 2018-03-12 Jonatan
	Se agrega filtro de atencion autorizada en la funcion mostrarPacientesPropios con el campo Mtraut, el cual controlara que el paciente si pueda
	ser visto por el medico siempre y cuando el personal de admisiones haya marcado la casilla en el programa agenda_urgencias_por_especialidad.php
	columna  Atención Autorizada.
//==========================================================================================================================================\\
 * 2017-09-21 Jonatan
	Se crean dos arreglos uno que contiene las ubicaciones fisicas y otra las virtuales, luego se valida por zona y si estan ocupadas todas 
	las fisicas muestra el listado de las virtuales.
//==========================================================================================================================================\\
 * 2017-07-04 Jonatan
	Se agrega la columna tiempo de atencion, la cual muestra el tiempo que lleva el paciente desde la terminacion de la consulta hasta la fecha
	y hora actual.
//==========================================================================================================================================\\
/* 2017-01-17: Jonatan Lopez
	Se agrega el filtro Areest en las consultas que tengan la tabla movhos_000169.
*/
//==========================================================================================================================================\\
/*  2016-07-25: Jonatan Lopez
	Se modifica el programa para que al seleccionar una conducta y esta sea de traslado, el paciente sea marcado en proceso de traslado y pueda ser
	movido a cirugia, el medico puede cambiar de conducta al paciente y esto inactivara este movimiento.
//==========================================================================================================================================\\
/*	2015-08-24: Jerson Trujillo.
	Modificaciones para el sistema del turnero.
	-	Para poder ingresar un medico a la sala de espera primero debe seleccionar un consultorio en el cual va a trabajar.
		Si el consultorio seleccionado se encuentra ocupado por otro usuario, se da la opcion de liberar el consultorio.
	-	En la lista de pacientes en espera se agregaron las columnas de clasificacion y turno.
	- 	En la columna turno, debajo del numero de turno se agrego una boton (Telefono), para realizar el llamado del paciente
		al consultorio, este llamado lo que hace es generar una alerta a el monitor ubicado en la sala de espera, para que el
		paciente visualice el llamado y se dirija al consultorio.
	-	Solo se puede ir a la historia del paciente si este ya fue llamado.
	- 	Al terminar la consulta (Dandole un triage y conducta al paciente), se habilita nuevamente la opcion de llamar otro paciente.
*/
//==========================================================================================================================================\\
//Agosto 19 de 2015 Jonatan
//Los pacientes que salen en la lista de arriba dependeran de la especialidad marcada como de urgencias en la tabla movhos_000044 (Espurg), esto
//permite que el listado no dependa de una sola especialidad.
//==========================================================================================================================================\\
//Mayo 4 de 2015(Jonatan - Jerson)
//Jonatan: Se elimina la variable $wmed_triage ya que podria interferir en el cambio de especialidad de un paciente cuando el medico inicia la consulta.
//Jerson: Se guarda el cubilculo asociado,la fecha y hora de asignacion de ese cubiculo en la tabla hce_000022.
//==========================================================================================================================================\\
//Marzo 30 de 2015 (Jonatan)
//Se muestran los pacientes que estan en proceso de traslado en la lista de pacientes en espera.
//==========================================================================================================================================\\
//Marzo 26 de 2015 (Jonatan)
//Se muestran los pacientes que tienen conducta de alta en los pacientes atendidos y activos, si asignana una conducta diferente a alta se quita el
//alta en proceso y la fechas y hora de alta en proceso se reinician a cero.
//==========================================================================================================================================\\
//Marzo 24 de 2015 (Jonatan)
//Por solicitud de Juan y Carmenza no se liberara el cubiculo por parte del medico cuando le de al paciente conducta de alta o muerte.
//==========================================================================================================================================\\
//Marzo 19 de 2015 (Jonatan)
//Se agrega consulta a la lista de pacientes en espera para que se muestren los que estan sin medico pero con alguna conducta asignada, y asi puedan
//ser atendidos.
//==========================================================================================================================================\\
//Marzo 12 de 2015 (Jonatan)
//Se validan todos los foreach con is_array para que no genere errores en el apache.
//==========================================================================================================================================\\
//Febrero 19 de 2015 (Jonatan)
//Se valida el arreglo de los cubiculos para que no muestre error en el apache, el arreglo debe contener datos para luego ser leido (linea 2898).
//Ademas se valida si el paciente esta en proceso de traslado no le permita asociar conducta vacia.
//==========================================================================================================================================\\
//Diciembre 29 de 2014 (Jonatan)
//Se libera el cubiculo ocupado por el paciente cuando el medico selecciona alta o muerte.
//==========================================================================================================================================\\
//Diciembre 18 de 2014 (Jonatan)
//Se corrige la reasignacion de conducta cuando el paciente esta atendido, ya que tenia el mismo id de los seleccionadores de los pacientes no
//atendidos y al seleccionarlo tomaba valores vacios.
//==========================================================================================================================================\\
//Diciembre 8 de 2014 (Jonatan)
//Se corrige la reasignacion de cubiculos para los pacientes atendidos, el seleccionados de cubiculos tendra diferente nombre en estos pacientes
//ya que pueden haber la misma cantidad en la parte de los no atendidos y causar confucion en el javascript.
//==========================================================================================================================================\\
//Diciembre 5 de 2014 (Jonatan)
//Se ordenan los pacientes atendidos y activos por ubicacion.
//==========================================================================================================================================\\
//Diciembre 3 de 2014 (Jonatan)
//Se agrega lista de seleccion con las salas para que puedan filtrar los pacientes atendidos y activos.
//==========================================================================================================================================\\
//Diciembre 2 de 2014 (Jonatan)
//Se libera el paciente de cualquier otro lugar donde se encuentre cuando lo cambie de zona.
//==========================================================================================================================================\\
//Diciembre 1 de 2014 (Jonatan)
//Se libera al paciente de cualquier otro cubiculo que este ocupando cuando se hace reasignacion de ubicacion.
//==========================================================================================================================================\\
//Noviembre 28 de 2014 (Jonatan)
//Se valida que el paciente tenga sala asignada por defecto desde la asignacion de especialidad, si es asi el seleccionador de cubiculos se
//habilita.
//==========================================================================================================================================\\
//Noviembre 24 de 2014 (Jonatan)
//Se hacen las modificicaciones necesarias para que la sala de espera maneje salas y esas salas o zonas esten asociadas a cubiculos o camillas,
//Se agrega numero de pacientes en el titulo de cada listado.
//==========================================================================================================================================\\
//Octubre 23 de 2014 (Jonatan)
//Se agrega fondo verde a los pacientes que tienen ordenes electronicas realizadas.
//==========================================================================================================================================\\
//Septiembre 30 de 2013 (Jonatan)
//Se agrega este union para que los medicos generales puedan ver los pacientes atendidos y activos con especialidad ortopedia,
//sin necesidad de que tenga conducta de ortopedia.
//==========================================================================================================================================\\
//Julio 22 de 2013 (Jonatan)
//Si el paciente no tiene medico asociado en la lista de pacientes atendidos y activos, se le asignará cuando el medico ingrese a la historia
//clínica, si ya tiene quedará con el mismo medico.
//==========================================================================================================================================\\
//Julio 19 de 2013 (Jonatan)
//Se corrige la asignacion de triage cuando el paciente tiene especialidad emergencia.
//==========================================================================================================================================\\
//Julio 17 de 2013 (Jonatan)
//Se modifica el programa para que cuando un paciente haya sido atendido y le den de alta, al volver a activarlo las fechas no cambien en el
//en que el medico lo tome de nuevo, osea no cambia la fecha inicial de consulta (mtrfco, mtrhco) y la fecha de terminacion de la consulta
//(mtrftc, mtrhtc), tambien la fecha y hora de triage, esto para evitar un desfase en los indicadores.
//==========================================================================================================================================\\
//Julio 9 de 2013 (Mario Cadavid)
//En la función ponerConducta se agrega el método trim en la asignación de la variable $wconducta, esto para prevenir que la conducta asignada
//en la tabla hce_000022 no quede con ningún espacio adicional y posteriormente no haga JOIN en los query de pacientes
//==========================================================================================================================================
//Abril 11 de 2013 (Jonatan)                                                     \\
//Se organiza la consulta para que los pacientes de triage se muestren a los medicos de triage y que no dependa de la especialidad del medico.
//==========================================================================================================================================\\
//Marzo 12 de 2013 (Jonatan)
//Se modifica la consulta de pacientes atendidos y activos para que el filtro sea desde la especialidad del medico y que el paciente tenga
//fecha y hora de terminacion actualzada, esto quiere decir que el paciente ya debe aparecer en la lista de pacientes atendidos  en la parte
//inferior.
//==========================================================================================================================================\\
//Febrero 27 de 2013 (Jonatan)
//Se modifica la funcion ponerConducta para que al momento en que un medico general asigne conducta Interconsulta Ortopedia, se cambie la
//especialidad asociada al paciente.
//==========================================================================================================================================
//Febrero 22 de 2013 (Jonatan)
//Se modifica la funcion ponerConsulta para que al momento en que un medico general inicie consulta con un paciente de triage, se cambie la
//especialidad asociada al paciente, en otros casos la especialidad seguira igual.
//==========================================================================================================================================
//Enero 31 de 2013 (Jonatan)
//Se agrega validaciones para que los pacientes de emergencia sean vistos por los medicos de urgencias y triage, y al momento de ingresar
//a la hce el paciente sea asociado a ese medico, ademas en el listado inferior se agrega un union para que muestre los pacientes que tienen
//especialidad emergencia asociada.
//Noviembre 27 de 2012 (Mario Cadavid)
//Se comenta la condicion 'AND ubiing  = oriing' para que en la lista de pacientes comunes se muestren todos los ingresos que tengan
//conducta por definir asi no sea el ingreso actualmente activo.
//==========================================================================================================================================\\
//Noviembre 27 de 2012 (Jonatan)
//Se agrega un UNION (sql) a la consulta que se encuentra en la funcion mostrarPacientesComunes para que los medicos generales puedan
//ver los pacientes de asignados a ortopedia en la parte de pacientes atendidos y activos, ademas se diferencia la consulta para los medicos
//generales y con especialidad, se quita el filtro de ubiprt en la misma funcion para que se muestren todos los pacientes y se agrega el estilo
//correspondiente al estado del paciente.
//=========================================================================================================================================
//Agosto 8 de 2012 (Jonatan)
//Se agrega el campo Esptri en la tabla 44 de movhos para que muestre a los enfermeros de triage los pacientes de triage de la tabla hce_000022.
//==========================================================================================================================================\\
//==========================================================================================================================================\\
//Mayo 14 de 2012 (Jonatan)
//Se modifica el script para que permite listar los pacientes relacionados con triage a los medicos o enfermeros que no son de triage, se
//agrega el seleccionador de nivel de traigge, ademas se imprime el nivel y tipo de convenio que tiene el paciente para los medicos o enfermeros
//que no son de triagge.
//==========================================================================================================================================\\
//Mayo 2 de 2012 (Jonatan)
//Se agrega una actualizacion a la tabla movhos_000022 en caso de que el medico de triage seleccione muerte o alta, ademas se modifica la
//consulta de conductas para que muestre las conductas que tengan el campo Conesp vacio, esto quiere decir que no tienen especialidad
//relacionada, al no no tener especialidad relacionada significa que el medico no es de triage.
//==========================================================================================================================================\\
//Abril 19 de 2012 (Jonatan)                                                                                                                       \\
//==========================================================================================================================================\\
//Se modifica el codigo para que permita trabajar con medicos o enfermeros de triage, para esto se ha validado que el usuario que utilice
//esta aplicacion sea de triage, para que el usuario sea de triage el campo Medtri(triage) en la tabla movhos_000048 debe estar en on,
//si el usuario tiene activo ese campo las consultas de pacientes en el panel de sala de espera solo mostraran los que han sido asignados
//a triage, ademas al usuario de triage se le mostrará cual de los pacientes tiene triage con convenio o sin convenio y asi asignarlo a
//medicina general, ortopedia, pediatria, muerte o alta.
//==========================================================================================================================================\\
//Mayo 20 de 2011 (Jonatan)                                                                                                                          \\
//==========================================================================================================================================\\
//Se crean 3 campos en la tabla hce_000035 que indican si la conducta es de Urgencias, Pediatria u Ortopedia esto para que un medico general\\
//pueda trasladar un paciente a las especialidades de Pediatria u Ortopedia, cuando esto se hace los medicos generales podran seguir viendo \\
//el paciente en la parte de abajo de este programa, pero los pediatras u ortopedistas solo podran ver los pacientes que tengan asignado un \\
//medico de su especialidad o que tengan una conducta correspodiente a su especialidad.                                                     \\
//Los médicos generales no pueden ver los pacientes que tengan asignado un medico pediatra u ortopedista, pero si tienen una conducta de    \\
//estas especialidades si.                                                                                                                  \\
//==========================================================================================================================================\\
//Mayo 13 de 2011                                                                                                                           \\
//==========================================================================================================================================\\
//Se controla que al momento de colocar una conducta que implique un Alta se coloque en proceso de alta en la tabla movhos_000018,  y a la  \\
//vez si la conducta no implica un Alta coloque el indicador ubialp='off'                                                                   \\
//==========================================================================================================================================\\
//Enero 24 de 2011                                                                                                                          \\
//==========================================================================================================================================\\
//Se adiciona el campo de alertas que se registra en el Kardex                                                                              \\
//==========================================================================================================================================\\


//=========================================================================================================================================================================================
//=========================================================================================================================================================================================

//Funcion que calcula el tiempo en horas y minutos con un tiempo definido en segundos.
function tiempo_transcurrido($tiempo_en_segundos){
	
	$horas = floor($tiempo_en_segundos / 3600);
	$minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
	$segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);
	
	if($horas > 0){
		return $horas . ' hrs <br>' . $minutos . " min ";
	}else{
		return $minutos . " min ";
	}
	
}

//Funciones Traslado de pacientes

function modificarIngresoPaciente($conex, $wbasedato, $historia, $ingreso, $servicio)
{
	$q = "	SELECT Ubisac, Ubihac, Ubisan, Ubihan
			  FROM ".$wbasedato."_000018
			 WHERE Ubihis = '".$historia."'
			   AND Ubiing = '".$ingreso."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	$ubisac = $row['Ubisac'];
	$ubihac = $row['Ubihac'];
	$ubiptr = 'off';

	if($row['Ubisan']!='' && $row['Ubisan']!='NO APLICA' && $row['Ubisan']!='.')
	{
		$ubisac = $row['Ubisan'];
		$ubihac = $row['Ubihan'];
	}

	$q = "UPDATE ".$wbasedato."_000018
			 SET Ubiptr = '".$ubiptr."', Ubisac = '".$ubisac."', Ubihac = '".$ubihac."', Ubisan = '', Ubihan = ''
		   WHERE Ubihis = '".$historia."'
			 AND Ubiing = '".$ingreso."';";
	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}


function deshabilitarUltimoMovimientoHospitalario($conex, $wbasedato, $whistoria, $wingreso){
	$q = "UPDATE
			".$wbasedato."_000017
		SET
			Eyrest = 'off'
		WHERE
			Eyrhis = '".$whistoria."'
			AND Eyrtip = 'Entrega'
			AND Eyring = '".$wingreso."';";

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}

//Esta funcion reemplaza la funcion consultarPacienteUnix, ya que este programa no debe depender de la conexion con este sistema. (25 Noviembre 2013 Jonatan)
//Funcion que consulta todos los datos de un paciente, verificando que el alta definitiva sea off, osea que este activo en la clinica.
function consultarPacienteMatrix($pacienteConsulta){
	
	global $wbasedato;
	global $wemp_pmla;
	global $conex;
	
	$paciente = new pacienteDTO();
	
	$ingreso = consultarUltimoIngresoHistoria($conex, $pacienteConsulta->historiaClinica, $wemp_pmla);
	
	$q = "SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, pacnac, pacsex, Ubisac, Ubihac, Ubisan, Ubihan, 
				 d.fecha_data as fechaIngreso, d.Hora_data as horaIngreso, Ingres, Ingnre, Ingtip
		    FROM root_000036 as a, root_000037 as b, ".$wbasedato."_000018 as c, ".$wbasedato."_000016 as d
		   WHERE oriced = pacced 
			 AND oritid = pactid
			 AND Ubihis = Orihis
			 AND Ubiing = Oriing
			 AND Ubihis = Inghis
			 AND Ubiing = Inging
			 AND Ubiald = 'off'
			 AND orihis = '".$pacienteConsulta->historiaClinica."'
			 AND oriing = '".$ingreso."'
			 AND oriori = '".$wemp_pmla."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if ($num > 0)
	{
		$info = mysql_fetch_array($res);

		$paciente->historiaClinica = $pacienteConsulta->historiaClinica;
		$paciente->ingresoHistoriaClinica = $ingreso;
		$paciente->nombre1 = $info['pacno1'];
		$paciente->nombre2 = $info['pacno2'];
		$paciente->apellido1 = $info['pacap1'];
		$paciente->apellido2 = $info['pacap2'];
		$paciente->documentoIdentidad = $info['pacced'];
		$paciente->tipoDocumentoIdentidad = $info['pactid'];
		$paciente->fechaNacimiento = $info['pacnac'];
		$paciente->genero = $info['pacsex'];
		$paciente->fechaIngreso = $info['fechaIngreso'];
		$paciente->horaIngreso = $info['horaIngreso'];
		$paciente->habitacionActual = $info['Ubihac'];
		$paciente->numeroIdentificacionResponsable = $info['Ingres'];
		$paciente->nombreResponsable = $info['Ingnre'];		
		$paciente->servicioActual = $info['Ubisac'];
		$paciente->tipoResponsable = $info['Ingtip'];
		
	}
			
	return $paciente;
}

//Funcion que cancela la solicitud de cama si cancelan la admision de un paciente
function cancelar_solicitud_cama($wemp_pmla, $whistoria)
{

	global $conex;
	global $wbasedato;

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

    $q2 =        "  SELECT max(id) as id "
                ."    FROM ".$wcencam."_000003 A"
                ."   WHERE Hora_llegada      = '00:00:00' "
                ."     AND Hora_Cumplimiento = '00:00:00' "
                ."     AND Anulada           = 'No' "
                ."     AND Historia          != ''"
                ."     AND Historia          = '".$whistoria."'"
                ."     AND Central           = '".$wcentral_camas."'";
    $res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q2 . "-" . mysql_error());
    $row = mysql_fetch_array($res2);
    $wid = $row['id'];

    $q3 =        "  SELECT Hab_asignada "
                ."    FROM ".$wcencam."_000003"
                ."   WHERE id      = '".$wid."' ";
    $res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q3 . "-" . mysql_error());
    $row_hab = mysql_fetch_array($res3);
    $whab_pro = $row_hab['Hab_asignada'];

	//La habitacion se pone en proceso de ocupacion
    $q_hab =  " UPDATE ".$wbasedato."_000020 "
        . "    SET Habpro = 'off'"
        . "  WHERE Habcod = '".$whab_pro."'";
    $err = mysql_query($q_hab, $conex) or die (mysql_errno() . $q_hab . " - " . mysql_error());


    $q = "UPDATE ".$wcencam."_000003
		     SET Anulada = 'Si'
		   WHERE id     = '".$wid."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());

}


 // Funcion que permite extraer la edad del paciente en años, meses y dias.
  function calcularAnioMesesDiasTranscurridos($fecha_inicio, $fecha_fin = '')
    {
        $datos = array('anios'=>0,'meses'=>0,'dias'=>0);

        if($fecha_inicio != '' && $fecha_inicio != '0000-00-00')
        {
            $fecha_de_nacimiento = $fecha_inicio;

            $fecha_actual = date ("Y-m-d");
            if($fecha_fin != '' && $fecha_fin != '0000-00-00')
            {
                $fecha_actual = $fecha_fin;
            }
            // echo "<br>Fecha final: $fecha_actual";
            // echo "<br>Fecha inicio: $fecha_de_nacimiento";

            // separamos en partes las fechas
            $array_nacimiento = explode ( "-", $fecha_de_nacimiento );
            $array_actual = explode ( "-", $fecha_actual );

            $anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años
            $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
            $dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días

            //ajuste de posible negativo en $días
            if ($dias < 0)
            {
                --$meses;

                //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
                switch ($array_actual[1]) {
                    case 1:     $dias_mes_anterior=31; break;
                    case 2:     $dias_mes_anterior=31; break;
                    case 3:
                            if (checkdate(2,29,$array_actual[0]))
                            {
                                $dias_mes_anterior=29; break;
                            } else {
                                $dias_mes_anterior=28; break;
                            }
                    case 4:     $dias_mes_anterior=31; break;
                    case 5:     $dias_mes_anterior=30; break;
                    case 6:     $dias_mes_anterior=31; break;
                    case 7:     $dias_mes_anterior=30; break;
                    case 8:     $dias_mes_anterior=31; break;
                    case 9:     $dias_mes_anterior=31; break;
                    case 10:     $dias_mes_anterior=30; break;
                    case 11:     $dias_mes_anterior=31; break;
                    case 12:     $dias_mes_anterior=30; break;
                }
                $dias=$dias + $dias_mes_anterior;
            }

            //ajuste de posible negativo en $meses
            if ($meses < 0)
            {
                --$anos;
                $meses=$meses + 12;
            }
            //echo "<br>Tu edad es: $anos años con $meses meses y $dias días";
            $datos['anios'] = $anos;

        }

        return $datos;
    }


   function traerresponsable($whis, $tipo_consulta)
    {

         global $conex;
		 global $wemp_pmla;
		 
         $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');;
         

         switch ($tipo_consulta) {
            
              case 'historia':

							$q = " SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
								."   FROM root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016"
								."  WHERE Inghis = '".$whis."'"
								."    AND ubihis  = Inghis"
								."    AND ubiing  = Inging"
								."    AND ubihis  = orihis "
								."    AND ubiing  = oriing "
								."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
								."    AND oriced  = pacced "
								."    AND oritid  = pactid "
								."  GROUP BY 1, 2, 3, 4, 5, 6, 7 "
								."  ORDER BY Inghis, Inging ";
							$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
							$row = mysql_fetch_array($res);

                 break;

             default:
                 break;
         }


        $wresponsable = $row['ingnre'];

        return $wresponsable;
    }


function registrarAsignacion($wemp_pmla, $wid)
{
    global $conex; 
    global $wusuario;	
	
    $wfecha = date("Y-m-d");
    $whora =(string)date("H:i:s");
   
	
	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

    $q =     "  SELECT Acaids "
            ."    FROM ".$wcencam."_000010"
            ."   WHERE Acaids   = '".$wid."'"
            ."     AND Acaest   = 'on'";
    $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $row = mysql_fetch_array($res);

    //Si el id ya tiene registro en on en la tabla 10 de cencam, no hara registro de datos.
    if ($row['Acaids'] == '')
        {
        $q =  " INSERT INTO ".$wcencam."_000010(   Medico   ,   Fecha_data,   Hora_data,    Acaids,  Acaest, Acarea, Seguridad     ) "
                                . "    VALUES('".$wcencam."','".$wfecha."','".$whora."','".$wid."',   'on' ,  'off', 'C-" . $wusuario . "')";
        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
        }


}


function solicitarCamillero($centroCosto, $wemp_pmla, $whistoria){
        
		global $conex;	
       
        $idRegistroInsertado = 0;
                
        //Con el ccentro de conto de origen consulta en cencam 4 el nombre
        $tablaCencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
		$wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
            
        $sqlRoot = "SELECT r.Ccaorg, r.Ccamot, r.Ccades, r.Ccaobs, cn.central
					  FROM root_000107 r
                INNER JOIN ".$tablaCencam."_000001 cn ON cn.Descripcion = r.Ccamot
                WHERE r.Ccaorg = '".$centroCosto."'";    
        $result = mysql_query($sqlRoot, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlRoot."):</b><br>".mysql_error());
        $row = mysql_fetch_assoc($result);        
    
        if(isset($row["Ccaorg"]) && $row["Ccaorg"] != ""){   
            
            if($tablaCencam != ""){
                
                $sqlCencam = "SELECT Nombre
                                FROM ".$tablaCencam."_000004
                               WHERE Cco LIKE '".$centroCosto."%'
                               LIMIT 1";                 
                $resultCen= mysql_query($sqlCencam, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlCencam."):</b><br>".mysql_error());
                $rowCen = mysql_fetch_assoc($resultCen);        
                
                if(isset($rowCen["Nombre"]) && $rowCen["Nombre"] != ""){
                    
					//---- Datos del paciente
					
							//Busco si lo digitado es la historia y con ese dato traigo el nombre del paciente
							 //si no busco si es la cedula y con el dato busco por cedula
							 $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex "
								."   FROM root_000036, root_000037 "
								."  WHERE orihis = '".$whistoria."'"       //Como Historia
								."    AND oriori = '".$wemp_pmla."'"
								."    AND oriced = pacced "
								."    AND oritid = pactid ";
							$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowhab = mysql_fetch_array($reshab);
							$numhab = mysql_num_rows($reshab);
							$whis = $whab; // En este caso se guardara la historia para el paciente en al tabla 3 de cencam.
							$wedad = calcularAnioMesesDiasTranscurridos($rowhab[4], $fecha_fin = '');
							$wresponsable = traerresponsable($whistoria,'historia');

						  switch ($rowhab[5]) {
							case 'M':
									$wgenero = "Masculino";
							break;

							case 'F':
									$wgenero = "Femenino";
							break;


							default:
								break;
							}
							if ($numhab > 0){
							   $whab="<b>Historia: ".$whistoria."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3]."<br>Edad:".$wedad['anios']."<br>Genero:".$wgenero."<br>Responsable:".$wresponsable;
							}else{
								$whab="<b>".$whistoria."</b><br>El dato No existe en la Base de Datos";
							}
					
					///------
					
                    //Variables necesarias para solicitar un camillero
                    $origen = $rowCen["Nombre"];
                    $motivo = $row["Ccamot"];
                    $destino = $row["Ccades"];
                    $solicito = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
                    $ccosto = $row["Ccades"];
                    $observacion = $row["Ccaobs"];
                    $fecha = date("Y-m-d");
                    $hora = date("H:i:s");
                    $central = $row["central"];
                    
                    $sqlSolcitudCamillero = "INSERT INTO ".$tablaCencam."_000003
                                                (Medico, Fecha_data, Hora_data, Origen, Motivo, Habitacion, Historia, Observacion, Destino, Solicito, Ccosto, Anulada, Central, Seguridad)
                                            VALUES 
                                                ('".$tablaCencam."', '".$fecha."', '".$hora."', '".$origen."', '".$motivo."','".$whab."', '".$whistoria."', '".$observacion."',
                                                '".$destino."', '".$solicito."', '".$ccosto."', 'No', '".$central."', 'C-cencam')";                 
                    $respSolicitud = mysql_query($sqlSolcitudCamillero, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlSolcitudCamillero."):</b><br>".mysql_error());
                    $wid = mysql_insert_id();
					
                    registrarAsignacion($wemp_pmla, $wid);
					
				}
			}
		}
}

// Función que permite consultar el código actual de el centro de costos de Urgencias
function consultarCcoUrgencias(){
	
	global $wbasedato;
	global $conex;

	$q = "SELECT Ccocod
		    FROM ".$wbasedato."_000011
		   WHERE Ccourg = 'on'
		     AND Ccoest = 'on'; ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = "";

	if($filas > 0){
		
		$fila = mysql_fetch_row($res);
		$cco = $fila[0];
	}
	
	return $cco;
}

// Función que permite consultar el código actual de el centro de costos de Cirugia
function consultarCcoCirugia(){
	
	global $wbasedato;
	global $conex;

	$q = "SELECT Ccocod
		    FROM ".$wbasedato."_000011
		   WHERE Ccocir = 'on'
		     AND Ccoest = 'on'; ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = "";

	if($filas > 0){
		
		$fila = mysql_fetch_row($res);
		$cco = $fila[0];
	}
	
	return $cco;
}

//-- fin trasaldo de pacientes

function filtrarCubiculo($wemp_pmla, $wsala, $posicion, $whis, $wing, $atendidos, $cubiculoActual){

	global $conex;
	global $wbasedato;
	global $whce;

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'');
	$control = "";
	//Asigno la sala al paciente para que al recargar deje seleccionada esa opcion en el dropdown y despues podra seleccionar el cubiculo donde desea ubicar al paciente.
	$q = 	 " UPDATE ".$whce."_000022 "
			."    SET mtrsal = '".$wsala."' "
			."  WHERE mtrhis = '".$whis."'"
			."	  AND mtring = '".$wing."'";
	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$funcion_filtrar = "colocarCubiculo($whis, $wing, $posicion)";

	//Se libera el paciente de cualquier ubicacion en la que se encuentre.
	$q = " UPDATE ".$wbasedato."_000020 "
		."    SET habhis = '', "
		."        habing = '',"
		."        habdis = 'on'"
		."  WHERE habhis = '".$whis."'"
		."    AND habing = '".$wing."'";
	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Si la accion es desde los pacientes atendidos y activos el onchange que le llegara al listado de cubiculos sera reasginarCubiculo con sus parametros.
	if($atendidos == 'on'){

	$funcion_filtrar = " reasginarCubiculo($whis, $wing, $posicion, \"on\", \"$cubiculoActual\")";
	$control = "_atendidos";
	}

	//Busco los cubiculos asociados a la sala y luego devuelvo el html con el dropdown para pintarlo.
	$q_cub =   " SELECT Habcod, Habcpa, Habzon, Habhis, Habing  "
			 . "   FROM ".$wbasedato."_000020 "
			 . "  WHERE habcub = 'on'"
			 . "	AND habest = 'on' "
			 . "	AND habdis = 'on' "
			 . "	AND habvir != 'on' "
			 . "	AND habzon = '".$wsala."' "
			." ORDER BY habord, habcpa ";
	$res_cub = mysql_query($q_cub, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub." - ".mysql_error());
	$num_cub = mysql_num_rows($res_cub);
	
	if($num_cub == 0){
		
		//Si ya estan ocupadas todas las fisicas muestra las virtuales.		
		$q_cub =   " SELECT Habcod, Habcpa, Habzon, Habhis, Habing  "
				 . "   FROM ".$wbasedato."_000020 "
				 . "  WHERE habcub = 'on'"
				 . "	AND habest = 'on' "
				 . "	AND habdis = 'on' "
				 . "	AND habvir = 'on' "
				 . "	AND habzon = '".$wsala."' "
				." ORDER BY habord, habcpa ";
		$res_cub = mysql_query($q_cub, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub." - ".mysql_error());
		
		
	}
	
	$datamensaje['html'] .= "<select id='cubiculo$control$posicion' name='wcubiculo$posicion' onchange='$funcion_filtrar'>";
	$datamensaje['html'] .= "<option value=''>&nbsp</option>";

	 while($row_cub = mysql_fetch_array($res_cub)){

		$datamensaje['html'] .= "<option value='".$row_cub['Habcod']."'>".$row_cub['Habcpa']."</option>";

		}

	$datamensaje['html'] .= "</select>";

	echo json_encode($datamensaje);

}


//Funcion que libera el cubiculo actual del paciente y le asigna otro.
function reasignarCubiculo($whce, $wbasedato, $whis, $wing, $wusuario, $wcubiculoActual, $wcubiculoNuevo)
   {

    global $conex;
	global $wbasedato;
    global $wemp_pmla;

	$wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");

	$num_hab_his = 0;

	//Evaluo el estado actual del cubiculo.
	$q_hab = " SELECT habdis
                 FROM ".$wbasedato."_000020
                WHERE habcod = '$wcubiculoNuevo'";
    $res_hab = mysql_query($q_hab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_hab . " - " . mysql_error());
    $row_hab = mysql_fetch_array($res_hab);

	//Si el paciente no tiene cubiculo y le van a asignar uno debe evaluar si ya tiene uno asignado y asi no se asignen dos cubiculos a la misma historia.
	if($wcubiculoActual == ""){

	$q_hab_his = " SELECT habhis
                     FROM ".$wbasedato."_000020
                    WHERE habhis = '$whis'";
    $res_hab_his = mysql_query($q_hab_his, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_hab_his . " - " . mysql_error());
    $num_hab_his = mysql_num_rows($res_hab_his);

	}

	//Si la historia no tiene cubiculo asignado permite la asignacion.
	if($num_hab_his == 0){

		if($row_hab['habdis'] == "on"){

		//Se libera al paciente de cualquier otro cubiculo que este ocupando.
		$q = " UPDATE ".$wbasedato."_000020 "
			."    SET habhis = '', "
			."        habing = '', "
			."        habdis = 'on' "
			."  WHERE habcod != '".$wcubiculoNuevo."'"
			."    AND habhis = '".$whis."'"
			."    AND habing = '".$wing."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		//Se actualiza el cubiculo para el paciente.
		$q = " UPDATE ".$wbasedato."_000020 "
			."    SET habhis = '".$whis."', "
			."        habing = '".$wing."',"
			."        habdis = 'off' "
			."  WHERE habcod = '".$wcubiculoNuevo."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		//Se libera el cubiculo en el que estaba el paciente.
		$q = " UPDATE ".$wbasedato."_000020 "
			."    SET habhis = '', "
			."        habing = '', "
			."        habdis = 'on' "
			."  WHERE habcod = '".$wcubiculoActual."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		//Terminar la consulta y marcar el paciente con cubiculo asignado.
		$q = " UPDATE ".$whce."_000022 "
			."    SET mtrcua = 'on',
					  Mtrccu = '".$wcubiculoNuevo."' "
			."  WHERE mtrhis = '".$whis."'"
			."	  AND mtring = '".$wing."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		// --> Registrar fecha y hora de asignación, solo si aun no se han registrado.
		// --> Jerson trujillo, 2015-04-21
		$sqlUpdFecHor = "
			UPDATE ".$whce."_000022
			   SET Mtrfac = '".date("Y-m-d")."',
				   Mtrhac = '".date("H:i:s")."'
			 WHERE Mtrhis = '".$whis."'
			   AND Mtring = '".$wing."'
			   AND Mtrfac = '0000-00-00'
			   AND Mtrhac = '00:00:00'
		";
		mysql_query($sqlUpdFecHor, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdFecHor):</b><br>".mysql_error());

		echo "ok";

		}else{

			echo "ya_asignado";

		}

	}else{

		echo "ya_asignado_historia";

	}



   }

 //Funcion de asignacion de cubiculos.
 function ponerCubiculo($whce, $wbasedato, $whis, $wing, $wusuario, $wconducta, $wfecha_term_consul, $whora_term_consulta, $wcubiculo)
   {

    global $conex;
	global $wbasedato;
    global $wemp_pmla;

	$wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");

	//Evaluo el estado actual del cubiculo.
    $q_hab = " SELECT habdis
                 FROM ".$wbasedato."_000020
                WHERE habcod = '$wcubiculo'";
    $res_hab = mysql_query($q_hab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_hab . " - " . mysql_error());
    $row_hab = mysql_fetch_array($res_hab);

	$q_hab_his = " SELECT habhis
                     FROM ".$wbasedato."_000020
                    WHERE habhis = '$whis'";
    $res_hab_his = mysql_query($q_hab_his, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_hab_his . " - " . mysql_error());
    $num_hab_his = mysql_num_rows($res_hab_his);

	//Si la historia no tiene cubiculo asignado permite la asignacion.
	if($num_hab_his == 0){

		//Si el cubiculo se encuentra disponible.
		if($row_hab['habdis'] == "on"){

				//Registrar el paciente en la tabla 20 de habitaciones.
				$q = " UPDATE ".$wbasedato."_000020 "
					."    SET habhis = '".$whis."', "
					."        habing = '".$wing."',"
					."        habdis = 'off' "
					."  WHERE habcod = '".$wcubiculo."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				//Terminar la consulta y marcar el paciente con cubiculo asignado.

				// --> Se modifica el update para que tambien guarde el codigo del cubiculo, fecha y hora de asignacion.
				// --> Jerson trujillo, 2015-04-21

				$q = " UPDATE ".$whce."_000022 "
					."    SET mtrcua = 'on',
							  Mtrccu = '".$wcubiculo."',
							  Mtrfac = '".date("Y-m-d")."',
							  Mtrhac = '".date("H:i:s")."',
							  mtrcur = 'off' "
					."  WHERE mtrhis = '".$whis."'"
					."	  AND mtring = '".$wing."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				
				//Consulta la conducta del paciente
				$q_con_pac = " SELECT contra
								 FROM ".$whce."_000022, ".$whce."_000035
								WHERE mtrcon = concod
								  AND mtrhis = '".$whis."'
								  AND mtring = '".$wing."'";
				$res_con_pac = mysql_query($q_con_pac, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_con_pac . " - " . mysql_error());
				$row_con_pac = mysql_fetch_array($res_con_pac);
				
				//Verifica si el paciente es tiene conducta de traslado.	
				if($row_con_pac['contra'] == 'on'){
					
					$codCcoCirugia = consultarCcoCirugia();
					$codCcoUrgencias = consultarCcoUrgencias();
					
					//====================================
					// Aca grabo el movimiento -- INGRESO -- del *** CENSO DIARIO ***					
					// Si el paciente a estado antes en el servicio para el mismo ingreso, traigo cuantas veces para sumarle una
					$q_32 = " SELECT COUNT(*) "
						."   FROM ".$wbasedato."_000032 "
						."  WHERE Historia_clinica = '".$whis."'"
						."    AND Num_ingreso      = '".$wing."'"
						."    AND Servicio         = '".$codCcoCirugia."'";
					$err_32 = mysql_query($q_32, $conex) or die (mysql_errno().$q_32." - ".mysql_error());
					$row_32 = mysql_fetch_array($err_32);

					$wingser = $row_32[0] + 1; //Sumo un ingreso a lo que traigo el query					
					
					$q = " UPDATE " . $wbasedato . "_000001 "
					   . "    SET connum=connum + 1 "
					   . "  WHERE contip='entyrec' ";
					$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

					$q = "SELECT connum "
						. "  FROM " . $wbasedato . "_000001 "
						. " WHERE contip='entyrec' ";
					$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
					$row = mysql_fetch_array($err);
					$wconsec = $row[0];
					
					// Aca grabo el encabezado de la entrega o recibo, validar que esto se haga solo si es de cirugia.
					$q = " INSERT INTO " . $wbasedato . "_000017 (   Medico       ,   Fecha_data,   Hora_data,   Eyrnum     ,   Eyrhis  ,   Eyring  ,   Eyrsor  ,   Eyrsde         ,  Eyrtip   , Eyrest, Seguridad     ) "
						."                                VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $whora . "','" . $wconsec . "','" . $whis . "','" . $wing . "','".$codCcoUrgencias."','".$codCcoCirugia."','Entrega', 'on', 'C-" . $wusuario . "')";
					$err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());
					
					$q = " INSERT INTO ".$wbasedato."_000032 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica ,   Num_ingreso,   Servicio       ,   Num_ing_Serv,   Fecha_ing ,   Hora_ing ,   Procedencia    , Seguridad     ) "
												."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."'         ,'".$wing."'   ,'".$codCcoCirugia . "','" . $wingser . "' ,'" . $wfecha . "','" . $whora . "','" . $codCcoUrgencias . "', 'C-" . $wusuario . "')";
					$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
					
					//====================================
					
					//El paciente queda en proceso de traslado.
					$q = " UPDATE ".$wbasedato."_000018 "
						."    SET ubiptr = 'on'"							  
						."  WHERE ubihis = '".$whis."'"
						."	  AND ubiing = '".$wing."'";
					$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					
					solicitarCamillero($codCcoUrgencias, $wemp_pmla, $whis);
					
				}
				
				return "ok";

			}else{

				return "ya_asignado";

			}
	}else{

		$q = " UPDATE ".$whce."_000022 "
			."    SET mtrcur = 'off' "
			."  WHERE mtrhis = '".$whis."'"
			."	  AND mtring = '".$wing."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		return "ya_ubicado";

	}

   }

//Verifica si el paciente ha tenido registros por el programa de ordenes.
function buscar_paciente_ordenes($conex, $wbasedato, $historia, $ingreso){

	global $wbasedato;
	global $conex;

	$wfecha_actual = date('Y-m-d');
	$dia_anterior = date("Y-m-d", strtotime("$wfecha_actual -1 day"));

	//Consulta el kardex del dia actual, si hay kardex utilizo la fecha actual, sino, la de la fecha un dia antes.
	$q = "SELECT karord
		   	FROM ".$wbasedato."_000053
		   WHERE Karest = 'on'
		     AND Karhis = '".$historia."'
		     AND Karing = '".$ingreso."'
		     AND Fecha_data = '".$wfecha_actual."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num == 0){

	//Consulta el kardex del dia actual, si hay kardex utilizo la fecha actual, sino, la de la fecha un dia antes.
	$q = "SELECT karord
		   	FROM ".$wbasedato."_000053
		   WHERE Karest = 'on'
		     AND Karhis = '".$historia."'
		     AND Karing = '".$ingreso."'
		     AND Fecha_data = '".$dia_anterior."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());


	}

	$wpaciente_ordenes = mysql_fetch_array($res);

	return $wpaciente_ordenes['karord'];

}

//Verifica si el centro de costos debe ir a ordenes.
function ir_a_ordenes($wemp_pmla, $wcco){


	global $wbasedato;
	global $conex;

	$q = "  SELECT Ccoior
			  FROM ".$wbasedato."_000011
			 WHERE Ccocod = '".$wcco."'" ;
	$res = mysql_query($q, $conex);
	$row = mysql_fetch_array($res);

	return $row['Ccoior'];

}

  //Verifica si el paciente esta en proceso de traslado
  function validaEstado($wbasedato,$whistoria, $wingreso)
	{

        global $conex;


		$qptr = "	SELECT Ubiptr
					  FROM ".$wbasedato."_000018
					 WHERE Ubihis = '".$whistoria."'
                       AND Ubiing = '".$wingreso."'
					   AND Ubiald != 'on'";
		$resptr = mysql_query($qptr, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qptr . " - " . mysql_error());
		$rowptr = mysql_fetch_array($resptr);

		return $rowptr['Ubiptr'];

	}


  function calculartiempoEstancia($whis,$wing, $wfec)
    {
	 global $conex;
	 global $wbasedato;

	 $q = " SELECT TIMEDIFF($wfec,fecha_data) "
         ."   FROM ".$wbasedato."_000018 "
         ."  WHERE ubihis  = '".$whis."' "
         ."    AND ubiing  = '".$wing."' "
         ."    AND ubiald != 'on' ";
     $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	 $row = mysql_fetch_array($res);

	 return $row[0];
	}


function ponerConsulta($whce, $whis, $wing, $wusuario, $irhce, $wespecialidad, $wemp_pmla, $wesp_usuario, $wfecha_consulta, $whora_consulta, $wmedico_asociado)
   {

	global $conex;
    global $wbasedato;

	$wtriage_espec = consultarAliasPorAplicacion($conex, $wemp_pmla, "TriageAutomatico");
    $wdato_esp_tri = explode("-", $wtriage_espec);
	$wespecial_auto =  $wdato_esp_tri[0]; //Especialidad emergencia

	$wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");
	$consulta_urg = "on";

    $q_cub =   " SELECT Habcod  "
			 . "   FROM ".$wbasedato."_000020 "
			 . "  WHERE habcub = 'on'"
			 . "	AND habest = 'on' "
			 . "	AND habhis = '".$whis."'"
			 . "	AND habing = '".$wing."'";
	$res_cub = mysql_query($q_cub, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub." - ".mysql_error());
	$num_cub = mysql_num_rows($res_cub);

	//Si el paciente tiene cubiculo asignado e ingresar a la hce del paciente desde la lista de pacientes en espera, entonces debe terminar la consulta para el paciente.
	if($num_cub > 0){

		$consulta_urg = "off";
	}
	
	//Si se inicia la consulta para el paciente ingresa a esta validacion.
	if ($irhce != "on")
	{
		// --> 	Consultar el turno asociado al paciente. Jerson Trujillo. 2015-11-12
		$sqlTurno = "
		SELECT Mtrtur
		  FROM ".$whce."_000022
		 WHERE Mtrhis  = '".$whis."'
		   AND Mtring  = '".$wing."'
		";
		$resTurno = mysql_query($sqlTurno, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTurno):</b><br>".mysql_error());
		if($rowTurno = mysql_fetch_array($resTurno))
		{
			// --> 	Apagar la alerta de llamado a consulta, generada en el monitor del turnero.
			//		Jerson trujillo, 2015-07-24
			$sqlApagAlerta = "
			UPDATE ".$wbasedato."_000178
			   SET Atullc = 'off'
			 WHERE Atutur = '".$rowTurno['Mtrtur']."'
			";
			mysql_query($sqlApagAlerta, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlApagAlerta):</b><br>".mysql_error());
		}

		// --> 	Obtener sí el usuario que está haciendo la consulta es de triaje.
		//		2015-11-11	Jerson Trujillo.
		$wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$sqlUsuTri = "
		SELECT Medtri
		  FROM ".$wbasedatoMovhos."_000048
		 WHERE Meduma = '".$wusuario."'
		   AND Medtri = 'on'
		";
		$resUsuTri = mysql_query($sqlUsuTri, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUsuTri):</b><br>".mysql_error());
		if(mysql_fetch_array($resUsuTri))
			$usuarioDeTriaje = true;
		else
			$usuarioDeTriaje = false;

	   	if ($wespecialidad == $wespecial_auto) //Esta validacion se refiere a un paciente que tiene especialidad emergencia asociada
	   	{
			//Aqui valida si el paciente ya tuvo atencion por un medico, en caso de ser asi solo actualizara el medico.
			if($wfecha_consulta == '0000-00-00' and $whora_consulta == '00:00:00')
			{
				if($usuarioDeTriaje)
				{
					// --> 	Como el usuario es de triaje, actualizo la fecha y hora de inicio del triaje.
					//		Jerson Trujillo. 2015-11-12.
					$q = "UPDATE ".$whce."_000022 "
						."	 SET mtrcur  = '".$consulta_urg."', "                         //Indica que esta en consulta
						."       Mtrfit  = '".$wfecha."', "                //Fecha en que comienza la consulta
						."       Mtrhit  = '".$whora."', "
						."       mtrmed  = '".$wusuario."'"                //Medico que esta atendiendo
						." WHERE mtrhis  = '".$whis."' "
						."	 AND mtring  = '".$wing."' ";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
				else
				{
					$q = "UPDATE ".$whce."_000022 "
						."	 SET mtrcur  = '".$consulta_urg."', "                         //Indica que esta en consulta
						."       mtrfco  = '".$wfecha."', "                //Fecha en que comienza la consulta
						."       mtrhco  = '".$whora."', "
						."       mtrmed  = '".$wusuario."'"                //Medico que esta atendiendo
						." WHERE mtrhis  = '".$whis."' "
						."	 AND mtring  = '".$wing."' ";
					$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}
			}
			else
				{
				$q = "UPDATE ".$whce."_000022 "
					."	 SET mtrcur  = '".$consulta_urg."',"                         //Indica que esta en consulta
					."       mtrmed  = '".$wusuario."'"                //Medico que esta atendiendo
					." WHERE mtrhis  = '".$whis."' "
					."	 AND mtring  = '".$wing."' ";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				}


	   	}
	   	else // Para otras especialidades.
	   	{
            //22 feb 2013 Jonatan
            //Se hace esta validacion cuando un medico general toma un paciente de triage, en este caso se debe cambiar la especialidad asociada al paciente.
            //============================

            if ($wesp_usuario != $wespecialidad)
				{
					//Aqui valida si el paciente ya tuvo atencion por un medico, en caso de ser asi solo actualizara el medico.
					if($wfecha_consulta == '0000-00-00' and $whora_consulta == '00:00:00')
					{
						if($usuarioDeTriaje)
						{
							// --> 	Como el usuario es de triaje, actualizo la fecha y hora de inicio del triaje.
							//		Jerson Trujillo. 2015-11-12.
							 $q = "UPDATE ".$whce."_000022 "
								."	 SET mtrcur  = '".$consulta_urg."', "                         //Indica que esta en consulta
								."       Mtrfit  = '".$wfecha."', "                //Fecha en que comienza la consulta
								."       Mtrhit  = '".$whora."', "
								."       mtrmed  = '".$wusuario."',"                //Medico que esta atendiendo
								."       mtreme  = '".$wesp_usuario."',"            //Si un medico general toma un paciente de triage se cambia la especilidad asociada.
								."       mtretr  = '".$wesp_usuario."'"            //Si un medico general toma un paciente de triage se cambia la especilidad asociada.
								." WHERE mtrhis  = '".$whis."' "
								."	 AND mtring  = '".$wing."' ";
							$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
						else
						{
							$q = "UPDATE ".$whce."_000022 "
								."	 SET mtrcur  = '".$consulta_urg."', "                         //Indica que esta en consulta
								."       mtrfco  = '".$wfecha."', "                //Fecha en que comienza la consulta
								."       mtrhco  = '".$whora."', "
								."       mtrmed  = '".$wusuario."',"                //Medico que esta atendiendo
								."       mtreme  = '".$wesp_usuario."',"            //Si un medico general toma un paciente de triage se cambia la especilidad asociada.
								."       mtretr  = '".$wesp_usuario."'"            //Si un medico general toma un paciente de triage se cambia la especilidad asociada.
								." WHERE mtrhis  = '".$whis."' "
								."	 AND mtring  = '".$wing."' ";
							$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}
					else
					{
						$q = "UPDATE ".$whce."_000022 "
							."	 SET mtrcur  = '".$consulta_urg."', "                         //Indica que esta en consulta
							."       mtrmed  = '".$wusuario."',"                //Medico que esta atendiendo
							."       mtreme  = '".$wesp_usuario."',"            //Si un medico general toma un paciente de triage se cambia la especilidad asociada.
							."       mtretr  = '".$wesp_usuario."'"            //Si un medico general toma un paciente de triage se cambia la especilidad asociada.
							." WHERE mtrhis  = '".$whis."' "
							."	 AND mtring  = '".$wing."' ";
						$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}

				}
				//================================
				else
				{
					//Aqui valida si el paciente ya tuvo atencion por un medico, en caso de ser asi solo actualizara el medico.
					if($wfecha_consulta == '0000-00-00' and $whora_consulta == '00:00:00')
					{
						if($usuarioDeTriaje)
						{
							// --> 	Como el usuario es de triaje, actualizo la fecha y hora de inicio del triaje.
							//		Jerson Trujillo. 2015-11-12.
							$q = "UPDATE ".$whce."_000022 "
								."	 SET mtrcur  = '".$consulta_urg."', "                         //Indica que esta en consulta
								."       Mtrfit  = '".$wfecha."', "                //Fecha en que comienza la consulta
								."       Mtrhit  = '".$whora."', "
								."       mtrmed  = '".$wusuario."'"                //Medico que esta atendiendo
								." WHERE mtrhis  = '".$whis."' "
								."	 AND mtring  = '".$wing."' ";
							$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
						else
						{
							$q = "UPDATE ".$whce."_000022 "
								."	 SET mtrcur  = '".$consulta_urg."', "                         //Indica que esta en consulta
								."       mtrfco  = '".$wfecha."', "                //Fecha en que comienza la consulta
								."       mtrhco  = '".$whora."', "
								."       mtrmed  = '".$wusuario."'"                //Medico que esta atendiendo
								." WHERE mtrhis  = '".$whis."' "
								."	 AND mtring  = '".$wing."' ";
							$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
						}
					}
					else
					{

							$q = "UPDATE ".$whce."_000022 "
								."	 SET mtrcur  = '".$consulta_urg."', "                         //Indica que esta en consulta
								."       mtrmed  = '".$wusuario."'"                //Medico que esta atendiendo
								." WHERE mtrhis  = '".$whis."' "
								."	 AND mtring  = '".$wing."' ";
							$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}
				}
		}

		if($res)
			return "ok";
		else
			return "No se pudo realizar la asignación. \n Error: ".$res;
	   }
	   else
		{
		if($wmedico_asociado == '')
			{
				$q = "UPDATE ".$whce."_000022 "
					."	 SET mtrmed  = '".$wusuario."'" //Medico que esta atendiendo
					." WHERE mtrhis  = '".$whis."' "
					."	 AND mtring  = '".$wing."' ";
				$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}

   }

function ponerTipotriage($wcodigo)
   {

	global $conex;
	global $wbasedato;

	$q =       " SELECT Espnom"
			 . "   FROM ".$wbasedato."_000044 "
			 . "  WHERE Espcod = '".$wcodigo."' ";
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	return $row[0];

   }

 //Nivel de triage (nombre)
  function niveltriage($wtri)
   {

	global $conex;
	global $whce;

	$q =       " SELECT Trinom"
			 . "   FROM ".$whce."_000040 "
			 . "  WHERE Tricod = '".$wtri."' "
             ."     AND triest = 'on'";
    $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	return $row[0];

   }



//Funcion para guardar el nivel de triage // Jonatan //06 Junio 2012
function ponerNiveltriage($whce, $wbasedato, $whis, $wing, $wusuario, $wniveltriage, $wfecha, $whora, $wfecha_term_consul, $whora_term_consul)
   {

	global $conex;
	global $wbasedato;

    $wfecha=date("Y-m-d");
    $wniveltri = explode("-",$wniveltriage);
	$wniveltriage1 = $wniveltri[0];

	if($wfecha_term_consul == '0000-00-00' and $whora_term_consul == '00:00:00')
		{

		$q = " UPDATE ".$whce."_000022 "
			."    SET Mtrtri     = '".$wniveltriage1."', Mtrftr = '".$wfecha."', Mtrhtr = '".$whora."'"
			."  WHERE Mtrhis     = '".$whis."'"
			."    AND Mtring     = '".$wing."'";
		$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		}
	else
		{
		$q = " UPDATE ".$whce."_000022 "
			."    SET Mtrtri     = '".$wniveltriage1."'"
			."  WHERE Mtrhis     = '".$whis."'"
			."    AND Mtring     = '".$wing."'";
		$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}



   }


function ponerConducta($whce, $wbasedato, $whis, $wing, $wusuario, $wconducta, $wfecha_term_consul, $whora_term_consulta, $wtipo_cubiculo, $irhce, $proc_alta)
   {

    global $conex;
	global $wbasedato;
    global $wemp_pmla;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'cambiar_tr'=>'');
	$datamensaje['cambiar_tr'] = "fila1";
	$wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");

	$wesp1 = explode("-",$wconducta);
	$wespecialidad = trim($wesp1[1]);

	$wcond1 = explode("-",$wconducta);
	$wconducta = trim($wcond1[0]);

    $wtriage_espec = consultarAliasPorAplicacion($conex, $wemp_pmla, "TriageAutomatico");
    $wdato_esp_tri = explode("-", $wtriage_espec);
	$wespecial_auto =  $wdato_esp_tri[0]; //Especialidad emergencia

    $validaptr = validaEstado($wbasedato,$whis, $wing); //Enero 30 de 2013 Jonatan Lopez

	$westado_cur = 'off'; //Estado de la consulta de urgencias por defecto.

	//Si la conducta es tipo cubiculo y no activa hce dejara mtrcur en off, sino la activa.
	if($wtipo_cubiculo == 'on'){

		if($irhce == 'on'){

			$westado_cur = 'off';

		}else{

			$westado_cur = 'on';

		}

	}
	
	//Consulta la conducta actual del paciente
	$q_cond = " SELECT mtrcon
				  FROM ".$whce."_000022 
				 WHERE mtrhis = '".$whis."'
				   AND mtring = '".$wing."'";
	$res_cond = mysql_query($q_cond, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cond . " - " . mysql_error());
	$row_cond = mysql_fetch_array($res_cond);
	$conducta_actual = $row_cond['mtrcon'];	

	//Evaluo si la conducta colocada es de Alta o Muerte, luego evaluo si el paciente esta siendo trasladado
	$q1 = " SELECT conalt, conmue
			  FROM ".$whce."_000035
			 WHERE contri = 'on'
			   AND concod = '".$wconducta."'";
	$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
	$row = mysql_fetch_array($res1);

	if (($row[0] == 'on' or $row[1] == 'on') and $validaptr == 'on' ) //30 Enero Se valida si la conducta es de alta o muerte y si el paciente esta en proceso de traslado, si es asi no permite continuar.
	{

		$datamensaje['mensaje'] = "El paciente con la historia ".$whis." - ".$wing." se encuentra en proceso de traslado hacia uno de los pisos, no es posible asignarle esta conducta.";
		$datamensaje['error'] = 1;
	
	}
	else{

	//Si la conducta es nula, ELSE solo termino la consulta y asigno la conducta nula osea borro lo que habia
	//por el THEN coloco la conducta y la hora de terminacion de la consulta
	if ($wconducta != "")
	    {
	    //Valido que el medico sea de triage // 19 Abril 2012
		$q =   " SELECT Medtri"
			 . "   FROM ".$wbasedato."_000048 "
			 . "  WHERE Medurg = 'on' "
			 . "	AND Meduma = '".$wusuario."'"
			 . "	AND Medtri = 'on'"
			 . "	AND Medest = 'on'";
		$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array($res);
		$wnum = mysql_num_rows($res);

		if ($row[0] != 'on')
				{
					//Esta validacion se da para los pacientes que ya fueron atendidos e iniciaron de nuevo la consulta. //16 julio Jonatan
					if($wfecha_term_consul == '0000-00-00' and $whora_term_consulta == '00:00:00')
						{
						$q = " UPDATE ".$whce."_000022 "
							."    SET mtrcur = '".$westado_cur."', "                        //Termina la consulta
							."        mtrcon = '".$wconducta."', "             //Asume una conducta, lo que indica que ya termino la consulta
							."        mtrftc = '".$wfecha."', "                //Fecha en que Termina la consulta
							."        mtrhtc = '".$whora."' "				   //Hora en que Termina la consulta
							."  WHERE mtrhis = '".$whis."' "
							."    AND mtring = '".$wing."' ";
						}
					else
						{
							$q = " UPDATE ".$whce."_000022 "
							."    SET mtrcur = '".$westado_cur."', "                        //Termina la consulta
							."        mtrcon = '".$wconducta."'"             //Asume una conducta, lo que indica que ya termino la consulta
							."  WHERE mtrhis = '".$whis."' "
							."    AND mtring = '".$wing."' ";
						}
				}
			else
				{

				//Evaluo si la conducta colocada es de Alta o Muerte para hacer el egreso por cualquiera de estas dos conductas
				$q1 = " SELECT conalt, conmue
						 FROM ".$whce."_000035
						WHERE contri = 'on'
						  AND concod = '".$wconducta."'";
				$res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
				$row = mysql_fetch_array($res1);

				if ($row[0] != 'on' and $row[1] != 'on')
					{

					  $q = " UPDATE ".$whce."_000022 "
						  ."    SET mtrcur = 'off', "                        //No hay consulta
						  ."        mtrcon = '', "                           //No hay conducta
						  ."        mtrfco = '0000-00-00', "
						  ."        mtrhco = '00:00:00', "
						  ."        mtrftc = '0000-00-00', "
						  ."        mtrhtc = '00:00:00', "
						  ."        mtrmed = '', "			                 //No hay medico tte
						  ."		mtreme = '".$wespecialidad."',"
						  ."		mtretr = '".$wespecialidad."'"
						  ."  WHERE mtrhis = '".$whis."' "
						  ."    AND mtring = '".$wing."' ";
                      $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
					}
				else
					{
					//Esta validacion se da para los pacientes que ya fueron atendidos e iniciaron de nuevo la consulta. //16 julio Jonatan
					if($wfecha_term_consul == '0000-00-00' and $whora_term_consulta == '00:00:00')
						{
						$q = " UPDATE ".$whce."_000022 "
							."    SET mtrcur = 'off', "                        //Termina la consulta
							."        mtrcon = '".$wconducta."', "             //Asume una conducta, lo que indica que ya termino la consulta
							."        mtrftc = '".$wfecha."', "                //Fecha en que Termina la consulta
							."        mtrhtc = '".$whora."' "				   //Hora en que Termina la consulta
							."  WHERE mtrhis = '".$whis."' "
							."    AND mtring = '".$wing."' ";
						}
					else
						{
						$q = " UPDATE ".$whce."_000022 "
							."    SET mtrcur = 'off', "                        //Termina la consulta
							."        mtrcon = '".$wconducta."'"             //Asume una conducta, lo que indica que ya termino la consulta
							."  WHERE mtrhis = '".$whis."' "
							."    AND mtring = '".$wing."' ";
						}
					}
				}

		}

     else
        {


         $q_esp = "SELECT Mtreme
                     FROM ".$whce."_000022 "
				."  WHERE mtrhis = '".$whis."' "
				."    AND mtring = '".$wing."' ";
         $res_esp = mysql_query($q_esp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_esp . " - " . mysql_error());
         $row_esp = mysql_fetch_array($res_esp);
         $wespecialidad_paciente = $row_esp['Mtreme'];

         //30 Enero de 2013 Esta validacion se refiere a un paciente que tiene especialidad emergencia asociada
         if ($wespecialidad_paciente == $wespecial_auto)
         {
             $q = " UPDATE ".$whce."_000022 "
			  ."    SET mtrcur = 'on', "                        //Hay consulta
			  ."        mtrcon = '', "                           //No hay conducta
              ."        mtrmed = '', "			                 //No hay medico tte
              ."        mtrcua = '', "
              ."        mtrsal = '' "
		      ."  WHERE mtrhis = '".$whis."' "
			  ."    AND mtring = '".$wing."' ";
         }
         else
         {
			//Esta validacion se da para los pacientes que ya fueron atendidos e iniciaron de nuevo la consulta. //16 julio Jonatan
			if($wfecha_term_consul == '0000-00-00' and $whora_term_consulta == '00:00:00')
				{
				  $q = " UPDATE ".$whce."_000022 "
					  ."    SET mtrcur = 'off', "                        //No hay consulta
					  ."        mtrcon = '', "                           //No hay conducta
					  ."        mtrfco = '0000-00-00', "
					  ."        mtrhco = '00:00:00', "
					  ."        mtrftc = '0000-00-00', "
					  ."        mtrhtc = '00:00:00', "
					  ."        mtrmed = '', "			                 //No hay medico tte
					  ."        mtrcua = '', "
					  ."        mtrsal = '' "
					  ."  WHERE mtrhis = '".$whis."' "
					  ."    AND mtring = '".$wing."' ";
				}
			else
				{
					$q = " UPDATE ".$whce."_000022 "
					  ."    SET mtrcur = 'off', "                        //No hay consulta
					  ."        mtrcon = '', "                           //No hay conducta
					  ."        mtrmed = '', "			                 //No hay medico tte
					  ."        mtrcua = '', "
					  ."        mtrsal = '' "
					  ."  WHERE mtrhis = '".$whis."' "
					  ."    AND mtring = '".$wing."' ";
				}

		 }

		 //Actualiza la tabla 20 de habitaciones y la desocupa. Jonatan 4 Nov 2014.
		 $q_cub = " UPDATE ".$wbasedato."_000020 "
						  ."    SET habhis = '', "
						  ."        habing = '', "
						  ."        habdis = 'on' "
						  ."  WHERE habhis = '".$whis."' "
						  ."    AND habing = '".$wing."' ";
		 $res_cub = mysql_query($q_cub, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cub . " - " . mysql_error());

		}
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Evaluo si la conducta es de Ortopedia para cambiar la especialidad
	$q = " SELECT Concod
	         FROM ".$whce."_000035
			WHERE Concod = '$wconducta'
			  AND Conort = 'on' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$wnum = mysql_num_rows($res);

	if ($wnum > 0)
	  {

		$q = " SELECT Espcod
				 FROM ".$wbasedato."_000044
				WHERE Espnom LIKE '%ortopedia%' ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$q = " UPDATE ".$whce."_000022 "
			  ."    SET Mtreme = '".$row['Espcod']."', "
			  ." 		Mtretr = '".$row['Espcod']."' "
		      ."  WHERE mtrhis = '".$whis."' "
			  ."    AND mtring = '".$wing."' ";
			  //."    AND mtrcur = 'on' ";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	  }

      //Evaluo si la conducta es de Pediatria para cambiar la especialidad
	$q = " SELECT Concod
	         FROM ".$whce."_000035
			WHERE Concod = '$wconducta'
			  AND Conped = 'on' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$wnum = mysql_num_rows($res);

	if ($wnum > 0)
	  {

		$q = " SELECT Espcod
				 FROM ".$wbasedato."_000044
				WHERE Espnom LIKE '%pediatria%' ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$q = " UPDATE ".$whce."_000022 "
			  ."    SET Mtreme = '".$row['Espcod']."', "
			  ." 		Mtretr = '".$row['Espcod']."' "
		      ."  WHERE mtrhis = '".$whis."' "
			  ."    AND mtring = '".$wing."' ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	  }

    //Evaluo si la conducta colocada es de Alta o Muerte para hacer el egreso por cualquiera de estas dos conductas
	$q = " SELECT conalt, conmue
	         FROM ".$whce."_000035
			WHERE concod = '$wconducta' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$wnum = mysql_num_rows($res);

	if ($wnum > 0)
	  {
	   $row = mysql_fetch_array($res);
	   $walt=$row[0];
	   $wmue=$row[1];

	   if ($walt=="on" or $wmue=="on")
	      {
		   $wmot="Alta";
		   if ($wmue=="on")
		      { $wmot="Muerte";}

		   //=============  Mayo 13 de 2011	===================================================================================
		   //Coloco en proceso de Alta la historia por cualquiera de las dos conductas, para que luego el facturador de
           //el Alta Definitiva
		   //Julio 16 de 2013 Jonatan: Aqui se actualiza la ultima fecha en que un medico dio de alta a un paciente,
		   //la fecha y hora real se encuentran en hce_000022, en algunos casos es diferente ya que el paciente pudo ser atendido mas de una vez.
		   $q = " UPDATE ".$wbasedato."_000018 "
		       ."    SET ubialp = 'on', "
			   ."        ubifap = '".$wfecha."', "
			   ."        ubihap = '".$whora."' "
			   ."  WHERE ubihis = '".$whis."' "
			   ."    AND ubiing = '".$wing."' ";
		   $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		   //Se comenta el 19 de Marzo de 2015 por solicitud de Juan ya que el paciente aun puede estar en esa ubicacion y llegar otro paciente.
		   //Actualiza la tabla 20 de habitaciones y la desocupa. Jonatan 29 Dic 2014.
			// $q_cub = " UPDATE ".$wbasedato."_000020 "
						  // ."    SET habhis = '', "
						  // ."        habing = '', "
						  // ."        habdis = 'on' "
						  // ."  WHERE habhis = '".$whis."' "
						  // ."    AND habing = '".$wing."' ";
			// $res_cub = mysql_query($q_cub, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cub . " - " . mysql_error());

		   //OJO ========================================================
		   //Se quita para que todas las altas las hagan los facturadores
		   //   altaDefinitiva($whis, $wing, $wmot, $wmue);
		  }
		 else
            {
			 //=============  Mayo 13 de 2011	===================================================================================
			 //Si la conducta es diferente a Alta o Muerte, me aseguro de colocar el 'ubialp' en 'off'
             $q = " UPDATE ".$wbasedato."_000018 "
		         ."    SET ubialp = 'off', "
				 ."        ubifap = '0000-00-00', "
			     ."        ubihap = '00:00:00'  "
			     ."  WHERE ubihis = '".$whis."' "
			     ."    AND ubiing = '".$wing."' ";
		     $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
            }
	  }
		
	//Cancelar el proceso de traslado si el paciente tiene una conducta que sea de traslado	
	
	//Consultar si la conducta asignada es de traslado
	$q_cond_nueva = " SELECT contra
				        FROM ".$whce."_000035
			           WHERE concod = '$wconducta' ";
	$res_cond_nueva = mysql_query($q_cond_nueva, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cond_nueva . " - " . mysql_error());
	$row_cond_nueva = mysql_fetch_array($res_cond_nueva);
	$cond_nueva_traslado = $row_cond_nueva['contra'];	
	
	//Consultar si la conducta que tenia el paciente es de traslado.
	$q_cond_actual = " SELECT contra
				        FROM ".$whce."_000035
			           WHERE concod = '$conducta_actual' ";
	$res_cond_actual = mysql_query($q_cond_actual, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cond_actual . " - " . mysql_error());
	$row_cond_actual = mysql_fetch_array($res_cond_actual);
	$cond_actual_traslado = $row_cond_actual['contra'];
	
	//Si $wirhce == 'on' osea que ya fue atendido y la conducta actual del paciente es de traslado, se inactivará el traslado.
	if($irhce == 'on'){
		
		//Si la conducta anterior es de traslado, y la conducta nuevo no es de traslado inactiva el registro del movimiento hospitalario tabla 17, 
		//inactiva la solicitud de trasado en la tabla 18, cancela la solicitud de cama.
		if($cond_actual_traslado == 'on'){	
			
			//Conducta nuevo no es de traslado.
			if( $cond_nueva_traslado != 'on'){
				
				$fechaLog = date("Y-m-d");
				$horaLog = date("H:i:s");

				//Creacion de un archivo plano para tomar una imagen de la informacion de las camas en ese momento
				$nombreArchivo = "admisionPreentrega.txt";

				//Apuntador en modo de adicion si no existe el archivo se intenta crear...
				$archivo = fopen($nombreArchivo, "a");
				if(!$archivo){
					$archivo = fopen($nombreArchivo, "w");
				}
			
				$usuario = $_SESSION['user'];
				
				@$contenidoLog = "****Sala de Espera..$fechaLog - $horaLog. Para historia: $whis. Usuario:$usuario \r\n";
			
								
				$contenidoLog = $contenidoLog."---->Accion: Cancelando admision... \r\n";
				$contenidoLog = $contenidoLog."PARAMETROS:::whistoria: '$whis'  \r\n";
				
				/* La cancelación consiste en llevar a cabo lo siguiente:		  	
				 * 1. Cancela la solicitud de cama.
				 * 2. Modifica el registro del movimiento hospitalario tabla 17.
				 * 3. Modifica el ingreso en la tabla 18.
				 */		
				
				//Funcion que cancela la solicitud de la cama.
				//Paso 1.
				cancelar_solicitud_cama($wemp_pmla, $whis);
				$paciente = new pacienteDTO();
				$paciente = consultarInfoPacientePorHistoria($conex,$whis);
				$datospacienteMatrix = consultarPacienteMatrix($paciente);

				//Datos del paciente			
				$ubicacionPaciente = new ingresoPacientesDTO();
				$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);

				$contenidoLog = $contenidoLog."Paciente: $ubicacionPaciente->historiaClinica-$ubicacionPaciente->ingresoHistoriaClinica:: Ubisan: $ubicacionPaciente->servicioAnterior Ubisac: $ubicacionPaciente->servicioActual Ubihan: $ubicacionPaciente->habitacionAnterior Ubihac: $ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado  \r\n";
				
				//Paso 2.
				//Modifica el registro del movimiento hospitalario tabla 17.
				deshabilitarUltimoMovimientoHospitalario($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
				$contenidoLog = $contenidoLog."Inactivando movimiento hospitalario \r\n";

				//Paso 3.
				//Modifica el ingreso en la tabla 18.
				modificarIngresoPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $ubicacionPaciente->servicioActual);
				$contenidoLog = $contenidoLog."Modificando ubicacion del paciente \r\n";
				$contenidoLog = $contenidoLog."La cancelación de la admisión del paciente $paciente->nombre1 $paciente->nombre2 $paciente->apellido1 $paciente->apellido2 con historia clínica $paciente->historiaClinica-$paciente->ingresoHistoriaClinica<br/>se realizó con éxito. \r\n";

				$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $datospacienteMatrix->historiaClinica, $datospacienteMatrix->ingresoHistoriaClinica);

				$contenidoLog = $contenidoLog."Ubicacion final del paciente cancelación: ";
				if(isset($ubicacionPaciente->servicioAnterior)){
					$contenidoLog = $contenidoLog."Ubisan: $ubicacionPaciente->servicioAnterior";
				}

				if(isset($ubicacionPaciente->servicioActual)){
					$contenidoLog = $contenidoLog.". Ubisac: $ubicacionPaciente->servicioActual";
				}

				if(isset($ubicacionPaciente->habitacionAnterior)){
					$contenidoLog = $contenidoLog.". Ubihan:$ubicacionPaciente->habitacionAnterior";
				}

				if(isset($ubicacionPaciente->habitacionActual)){
					$contenidoLog = $contenidoLog.". Ubihac:$ubicacionPaciente->habitacionActual";
				}

				if(isset($ubicacionPaciente->enProcesoTraslado)){
					$contenidoLog = $contenidoLog.". Ubiptr: $ubicacionPaciente->enProcesoTraslado";
				}
				$contenidoLog = $contenidoLog."\r\n";
				
				
				if($archivo){
					// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
					if (is_writable($nombreArchivo)) {
						// Escribir $contenido a nuestro arcivo abierto.
						fwrite($archivo, $contenidoLog);
						fclose($archivo);
					}
				}
				
				$datamensaje['cambiar_tr'] = "fila1";
				
			}else{
				
				$datamensaje['cambiar_tr'] = "colorAzul4";
				
			}
				
		}else{
		
		//Si la nueva conducta es de traslado, realizara los procesos para el traslado del paciente, registrar movimiento en la 17, registrar el egreso, poner el paciente
		//en proceso de traslado.
		if($cond_nueva_traslado == 'on'){
			
			$codCcoCirugia = consultarCcoCirugia();
			$codCcoUrgencias = consultarCcoUrgencias();
			
			//====================================
			// Aca grabo el movimiento -- INGRESO -- del *** CENSO DIARIO ***					
			// Si el paciente a estado antes en el servicio para el mismo ingreso, traigo cuantas veces para sumarle una
			$q_32 = " SELECT COUNT(*) "
				."   FROM ".$wbasedato."_000032 "
				."  WHERE Historia_clinica = '".$whis."'"
				."    AND Num_ingreso      = '".$wing."'"
				."    AND Servicio         = '".$codCcoCirugia."'";
			$err_32 = mysql_query($q_32, $conex) or die (mysql_errno().$q_32." - ".mysql_error());
			$row_32 = mysql_fetch_array($err_32);

			$wingser = $row_32[0] + 1; //Sumo un ingreso a lo que traigo el query					
			
			$q = " UPDATE " . $wbasedato . "_000001 "
			   . "    SET connum=connum + 1 "
			   . "  WHERE contip='entyrec' ";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

			$q = "SELECT connum "
				. "  FROM " . $wbasedato . "_000001 "
				. " WHERE contip='entyrec' ";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			$row = mysql_fetch_array($err);
			$wconsec = $row[0];
			
			// Aca grabo el encabezado de la entrega o recibo, validar que esto se haga solo si es de cirugia.
			$q = " INSERT INTO " . $wbasedato . "_000017 (   Medico       ,   Fecha_data,   Hora_data,   Eyrnum     ,   Eyrhis  ,   Eyring  ,   Eyrsor  ,   Eyrsde         ,  Eyrtip   , Eyrest, Seguridad     ) "
				."                                VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $whora . "','" . $wconsec . "','" . $whis . "','" . $wing . "','".$codCcoUrgencias."','".$codCcoCirugia."','Entrega', 'on', 'C-" . $wusuario . "')";
			$err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());
			
			$q = " INSERT INTO ".$wbasedato."_000032 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica ,   Num_ingreso,   Servicio       ,   Num_ing_Serv,   Fecha_ing ,   Hora_ing ,   Procedencia    , Seguridad     ) "
										."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."'         ,'".$wing."'   ,'".$codCcoCirugia . "','" . $wingser . "' ,'" . $wfecha . "','" . $whora . "','" . $codCcoUrgencias . "', 'C-" . $wusuario . "')";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			
			//====================================
			
			//El paciente queda en proceso de traslado.
			$q = " UPDATE ".$wbasedato."_000018 "
				."    SET ubiptr = 'on'"							  
				."  WHERE ubihis = '".$whis."'"
				."	  AND ubiing = '".$wing."'";
			$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			
			solicitarCamillero($codCcoUrgencias, $wemp_pmla, $whis);
			
			$datamensaje['cambiar_tr'] = "colorAzul4";
			
			}
		}
	}
  
	$datamensaje['mensaje'] = "ok";
	
	 echo json_encode($datamensaje);
	
  }

}


function altaDefinitiva($whis, $wing, $wmot, $wmue)
     {
	  global $conex;
	  global $wbasedato;
	  global $wcco;
	  global $wusuario;

	  $wfecha=date("Y-m-d");
      $whora = (string)date("H:i:s");

	  //Actualizo la historia como Alta Definitiva
	  $q = " UPDATE ".$wbasedato."_000018 "
		  ."    SET ubiald  = 'on', "
		  ."        ubimue  = 'on', "
		  ."        ubifad  = '".$wfecha."',"
		  ."        ubihad  = '".$whora."', "
		  ."        ubiuad  = '".$wusuario."' "
		  ."  WHERE ubihis  = '".$whis."'"
		  ."    AND ubiing  = '".$wing."'"
		  ."    AND ubiald != 'on' "
		  ."    AND ubiptr != 'on' ";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

      $wnuming=1;
      $wdiastan=calculartiempoEstancia($whis, $wing, $wfecha);
	  if ($wdiastan=="")
         $wdiastan=0;

      $wmotivo="ALTA";
	  if ($wmot == "Muerte")
	     {
		  if ($wdiastan>=2)
			 $wmotivo="MUERTE MAYOR A 48 HORAS";
			else
			  $wmotivo="MUERTE MENOR A 48 HORAS";
		  cancelar_pedido_alimentacion($whis, $wing, 'Muerte');
		  $wmotivo="Muerte";
         }
		else
           cancelar_pedido_alimentacion($whis, $wing, 'Cancelar');

	  //Grabo el registro de egreso del paciente del servicio
	  $q = " INSERT INTO ".$wbasedato."_000033 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,    Tipo_Egre_Serv,  Dias_estan_Serv, Seguridad        ) "
	      ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."'        ,'".$wing."'   ,'".$wcco."' ,".$wnuming."  ,'".$wfecha."'      ,'".$whora."'     , '".$wmotivo."'   ,".$wdiastan."    , 'C-".$wusuario."')";
	  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
}



  //=====================================================================================================================================================================
  //Esta funcion tambien se utiliza en el programa de 'hoteleria.php'   Febrero 10 2010
function cancelar_pedido_alimentacion($whis,$wing,$wtrans)
    {
	 global $wbasedato;
	 global $conex;
	 global $wfecha;
	 global $whora;
	 global $wusuario;


	 switch ($wtrans)
	   {
	    case "Cancelar":         //Se presiono alta definitiva
		    {
		 	 //Busco cual es el ultimo Servicio que tiene registrado el paciente en la fecha y hora
			 //junto con la accion realizada sobre este sin importar si esta activa o no.
			 //si tiene alguno valido que pueda ser cancelado
			 $q = " SELECT MAX(A.fecha_data), movser, audacc, movest "
		         ."   FROM ".$wbasedato."_000077 A, ".$wbasedato."_000078 B"
		         ."  WHERE movfec      >= '".$wfecha."'"
		         ."    AND movhis       = '".$whis."'"
		         ."    AND moving       = '".$wing."'"
		         //."    AND movest       = 'on' "
		         ."    AND A.fecha_data = B.fecha_data "
		         ."    AND A.hora_data  = B.hora_data "
		         ."    AND movhis       = audhis "
		         ."    AND moving       = auding "
		         ."    AND movser       = audser "
		         ."  GROUP BY 2, 3, 4 ";
		     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		     $wnum = mysql_num_rows($res);

		     if ($wnum > 0)
		        {
			     $row = mysql_fetch_array($res);

			     for ($i=1; $i<=$wnum;$i++)                //Marzo 1 de 2010
			        {
				     $row = mysql_fetch_array($res);
					     $wser=$row[1];
					     $west=$row[3];

					     if ($west == "on")
					        {
						     if ($row[2] != "ADICION")  //Osea que puede ser Pedido o Modificacion
						        {
								 //Busco que el SERVICIO se pueda cancelar en el momento
								 $q = " SELECT COUNT(*) "
								     ."   FROM ".$wbasedato."_000076 "
								     ."  WHERE serhca >= '".$whora."'"
								     ."    AND serest = 'on' "
								     ."    AND sernom = '".$wser."'";
							    }
							   else
							      {
								   //Busco que el SERVICIO se pueda cancelar en el momento si es una ADICION
								   $q = " SELECT COUNT(*) "
								       ."   FROM ".$wbasedato."_000076 "
								       ."  WHERE serhad >= '".$whora."'"
								       ."    AND serest = 'on' "
								       ."    AND sernom = '".$wser."'";
								  }
							 $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
						     $row = mysql_fetch_array($res);

						     if ($row[0] > 0)   //Si entra es porque SI se puede CANCELAR
						        {
							     $q = " SELECT COUNT(*) "
							         ."   FROM ".$wbasedato."_000077 "
							         ."  WHERE movfec = '".$wfecha."'"
							         ."    AND movhis = '".$whis."'"
							         ."    AND moving = '".$wing."'"
							         ."    AND movser = '".$wser."'"
							         ."    AND movest = 'on' ";
							     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
							     $row = mysql_fetch_array($res);

							     if ($row[0] > 0)
							        {
								     //Cancelo el PEDIDO de alimentacion
								     $q = " UPDATE ".$wbasedato."_000077 "
								         ."    SET movest = 'off' "
								         ."  WHERE movfec = '".$wfecha."'"
								         ."    AND movhis = '".$whis."'"
								         ."    AND moving = '".$wing."'"
								         ."    AND movser = '".$wser."'"
								         ."    AND movest = 'on' ";
								     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

								     //Inserto en la auditoria la cancelacion por el alta definitiva
									 $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis  ,   auding  ,   audser  , audacc              ,   audusu      ,     Seguridad   ) "
									     ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."', 'CANCELACION X ALTA','".$wusuario."','C-".$wusuario."') ";
									 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
									}
							    }
							   else
						         {
							      //Inserto en la auditoria la cancelacion por el alta definitiva
								  $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis  ,   auding  ,   audser  , audacc                        ,   audusu      ,     Seguridad   ) "
								      ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$wser."', 'ALTA - SERVICIO SIN CANCELAR','".$wusuario."','C-".$wusuario."') ";
								  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

						          echo "<script language='Javascript'>";
								  echo "alert ('¡¡¡ ATENCION !!! EL SERVICIO ".$wser.", NO SE PUDO CANCELAR POR ESTAR FUERA DEL HORARIO');";
								  echo "</script>";
							     }
						    }
					    }
		    	}
		    }
		    break;

		case "Muerte":         //Se presiono Muerte
		    {
		 	 //Busco que servicio se puede cancelar en el momento
			 $q = " SELECT sernom "
			     ."   FROM ".$wbasedato."_000076 "
			     ."  WHERE serhca <= '".$whora."'"
			     ."    AND serhad >= '".$whora."'"
			     ."    AND serest = 'on' ";
			 $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		     $wnum = mysql_num_rows($res);

		     //Busco el servicio correspondiente a la hora actual, si lo encuentra es porque se puede cancelar alguno
		     if ($wnum > 0)
		        {
			     for ($i= 1; $i<=$wnum;$i++)
			        {
				     $row = mysql_fetch_array($res);

				     $q = " SELECT COUNT(*) "
				         ."   FROM ".$wbasedato."_000077 "
				         ."  WHERE movfec = '".$wfecha."'"
				         ."    AND movhis = '".$whis."'"
				         ."    AND moving = '".$wing."'"
				         ."    AND movser = '".$row[0]."'"
				         ."    AND movest = 'on' ";
				     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
				     $wnum = mysql_num_rows($res);

				     if ($wnum > 0)
				        {
					     //Cancelo el PEDIDO de alimentacion
					     $q = " UPDATE ".$wbasedato."_000077 "
					         ."    SET movest = 'off' "
					         ."  WHERE movfec = '".$wfecha."'"
					         ."    AND movhis = '".$whis."'"
					         ."    AND moving = '".$wing."'"
					         ."    AND movser = '".$row[0]."'"
					         ."    AND movest = 'on' ";
					     $res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

					     //Inserto en la auditoria la cancelacion por el alta definitiva
					     $q = " INSERT INTO ".$wbasedato."_000078 (   Medico       ,   Fecha_data,   Hora_data,   audhis  ,   auding  ,   audser    , audacc              ,   audusu      ,     Seguridad   ) "
						     ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."','".$wing."','".$row[0]."', 'CANCELACION X MUERTE','".$wusuario."','C-".$wusuario."') ";
						 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
				        }
				    }
			    }
			  else
		         {
		          echo "<script language='Javascript'>";
				  echo "alert ('¡¡¡ ATENCION !!! TENIA PEDIDO DE ALIMENTACION, NO SE PUDO CANCELAR');";
				  echo "</script>";
			     }
		    }
		    break;
	  }	//Fin del swicht
    }

function convenciones($fecing, $hora)
  {
    $wfecha=date("Y-m-d");

    $a1=$hora;
	$a2=date("H:i:s");
	$a3=((integer)substr($a2,0,2)-(integer)substr($a1,0,2))*60 + ((integer)substr($a2,3,2)-(integer)substr($a1,3,2)) + ((integer)substr($a2,6,2)-(integer)substr($a1,6,2))/60;

	$wcolor="";

	//Aca configuro la presentacion de los colores segun el tiempo de respuesta
	if ($a3 > 30 or $wfecha != $fecing)                   //Mas de 35 Minutos
	   {
		$wcolor = "FC514E";        //Rojo
	   }
	if ($a3 > 16.1 and $a3 <= 30 and $wfecha == $fecing)  //de 20 Minutos a 35
	   {
		$wcolor = "FFFF3F";        //Amarillo
	   }
	if ($a3 <= 16 and $wfecha == $fecing)                 //20 Minutos
	   {
		$wcolor = "99FFCC";        //Verde
	   }

    return $wcolor;
  }

function mostrarPacientesPropios($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, &$i)
 {

  global $conex;

  //Aca trae los pacientes que estan en Urgenciasen el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
  //y que no esten ni en proceso ni en alta
  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed "
	  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D"
	  ."  WHERE ubihis  = orihis "
	  ."    AND ubiing  = oriing "
	  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
	  ."    AND oriced  = pacced "
	  ."    AND oritid  = pactid "
	  ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
	  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
	  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
	  ."    AND ubihis  = mtrhis "
	  ."    AND ubiing  = mtring "
	  ."    AND mtrmed  = '".$wusuario."'"
	  ."    AND mtrcon IN ('', 'NO APLICA') "
	  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
	  ."  ORDER BY 7, 12 ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);


  echo "<table>";
  echo "<tr class=encabezadoTabla>";
  //echo "<th>Semaforo</th>";
  echo "<th>Fecha de Ingreso</th>";
  echo "<th>Hora de Ingreso</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  echo "<th>Ir a Historía</th>";
  echo "<th>Conducta a Seguir</th>";
  echo "<th>Afinidad</th>";
  echo "</tr>";

  if ($num > 0)
	 {
	  for($i=1;$i<=$num;$i++)
		 {
		  $row = mysql_fetch_array($res);

		  if (is_integer($i/2))
			 $wclass="fila1";
			else
			   $wclass="fila2";

		  $whis = $row[0];
		  $wing = $row[1];
		  $wpac = $row[2]." ".$row[3]." ".$row[4]." ".$row[5];
		  $wfin = $row[6];     //Fecha de Ingreso
		  $wtid = $row[7];
		  $wdpa = $row[8];
		  $wcur = $row[9];     //Indicador de si esta en Consulta
		  $wcon = $row[10];    //Conducta
		  $whin = $row[11];    //Hora de Ingreso
		  $wmedico_asociado = $row[12];    //Hora de Ingreso

		  //$wcolor=convenciones($wfin, $whin);

		  echo "<tr class=".$wclass.">";
		  //echo "<td align=center bgcolor=".$wcolor.">&nbsp</td>";
		  echo "<td align=center>   ".$wfin."</td>";
		  echo "<td align=center>   ".$whin."</td>";
		  echo "<td align=center><b>".$whis." - ".$wing."</b></td>";
		  echo "<td align=left  ><b>".$wpac."</b></td>";

		  $irhce="off";  //Permite ingresar a la hce sin dar clic sobro el radio button de ir a hce
		  if ($wcur == "on")
			 echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing, \"$wdpa\", \"$wtid\", $i, \"\",  \"\", \"\", \"\")' checked></td>";
			else
			   echo "<td align=center><input type='radio' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing, \"$wdpa\", \"$wtid\", $i, \"$irhce\",  \"\", \"$wemp_pmla\", \"\" )'>";

		  echo "<td align=center><select id='conducta$i' name='wconducta$i' onchange='colocarConducta($whis, $wing, $i, \"$irhce\")'>";

		  if (isset($wcon))                              //Si selecciono una opcion del dropdown
			 {
			  $q = " SELECT condes "
				 . "   FROM ".$whce."_000035 "
				 . "  WHERE concod = '".$wcon."'";
			  $res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row2 = mysql_fetch_array($res2);

			  echo "<option selected value='$wcon'>".$row2[0]."</option>";
			 }

		  echo "<option value=''>&nbsp</option>";

		  //============================================================================================================
		  //Aca coloco todas las conductas
		  //============================================================================================================
		  $q = " SELECT concod, condes "
			 . "   FROM ".$whce."_000035 "
			 . "  WHERE conest = 'on' "
			 . "  ORDER BY conord ";
		  $res1 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num1 = mysql_num_rows($res1);
		  for ($j=1;$j<=$num1;$j++)
			 {
			  $row1 = mysql_fetch_array($res1);
			  echo "<option value=$row1[0]>".$row1[1]."</option>";
			 }
		  echo "</select></td>";
		  //============================================================================================================

		  //======================================================================================================
		  //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
		  $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
		  if ($wafin)
			 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			else
			  echo "<td>&nbsp</td>";
		  //======================================================================================================
		  echo "</tr>";
		 }
	  }
	 else
		echo "NO HAY PACIENTES PENDIENTES DE ATENCION";
   echo "</table>";
   }

function mostrarPacientesPorEspecialidad($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, &$i)
 {


  global $conex;
  global $wfecha;
  global $whora;

  $wgen = "on";
  $wped = "off";
  $wort = "off";
  $wesp = "";
  $turnoConLlamadoEnVentanilla 	= '';
  $hayPacienteEnConsulta 		= false;

  //Consulto las conductas tipo cubiculo.
  $q_con = " SELECT Concod "
		  ."   FROM ".$whce."_000035 "
		  ."  WHERE concub = 'on'"
		  ."    AND conest = 'on'";
  $res_con = mysql_query($q_con,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

  $array_cond_cub = array();

  while($row_con = mysql_fetch_assoc($res_con)){
		if(!array_key_exists($row_con['Concod'], $array_cond_cub)){
			array_push( $array_cond_cub, "'".$row_con['Concod']."'" );
		}
  }
  //Arreglo que contiene todas la conductas tipo cubiculo.
  $wconductas_cubiculo = implode(",",$array_cond_cub);


  //Traigo los indicadores si el medico es de urgencias y ademas es Pediatra u Ortopedista, si no, es porque es general
  $q = " SELECT medurg, medped, medort, medesp "
      ."   FROM ".$wbasedato."_000048 "
	  ."  WHERE meduma = '".$wusuario."'"
	  ."    AND medest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

	// --> Obtener maestro de salas de espera: Jerson trujillo 2015-07-21
	$arraySalasEspera = array();
	$sqlObtenerSalas = "
	SELECT Salcod, Salnom
	  FROM ".$wbasedato."_000182
	 WHERE Salest = 'on'
	 ORDER BY Salpri
	";
	$resObtenerSalas = mysql_query($sqlObtenerSalas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerSalas):</b><br>".mysql_error());
	while($rowObtenerSalas = mysql_fetch_array($resObtenerSalas))
		$arraySalasEspera[$rowObtenerSalas['Salcod']] = $rowObtenerSalas['Salnom'];
	
	// --> Obtener maestro de salas de espera: Jerson trujillo 2016-06-21
	$arrayPrioridades 	= array();
	$sqlPrioridades 	= "
	SELECT Pricod, Prinom
	  FROM ".$wbasedato."_000206
	 WHERE Priest  = 'on'
	";
	$resPrioridades = mysql_query($sqlPrioridades, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPrioridades):</b><br>".mysql_error());	
	while($rowPrioridades = mysql_fetch_array($resPrioridades))
		$arrayPrioridades[$rowPrioridades['Pricod']] = $rowPrioridades['Prinom'];

  if ($num > 0)
     {
      $row = mysql_fetch_array($res);

	  $wurg = $row[0];
	  $wped = $row[1];
	  $wort = $row[2];
	  $wesp = explode("-",$row[3]);

	  if ($wped == "on" or $wort == "on")   //Indica que es Especialista entonces obligo a que entre por Ped u Ort
	     $wgen = "off";
     }

    $q1 =     " SELECT Medtri, medesp"
            . "   FROM ".$wbasedato."_000048 "
            . "  WHERE Medurg = 'on' "
            . "	   AND Meduma = '".$wusuario."'"
            . "	   AND Medtri = 'on'"
            . "	   AND Medest = 'on'";
	$res1 = mysql_query($q1, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num1 = mysql_num_rows($res1);
    $row_triage = mysql_fetch_array($res1);

  if ($num1 > 0)
	{
     $wesp = explode("-",$row_triage['medesp']);

	 //Aca trae los pacientes que estan en Urgencias en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
	 //y que no esten en proceso ni en alta y que sean de de triage
	 $q =  " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtreme, mtrtri, mtrftc, mtrhtc, mtrsal, ubiptr, Mtrtur, Mtrgme  "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$wbasedato."_000048 F " //
		  ."  WHERE ubihis  = orihis "
		  ."    AND ubiing  = oriing "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		 // ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  ."    AND mtreme  = medesp "
		  ."    AND medtri  = 'on' "
		  ."    AND medurg  = 'on' "
          ."    AND mtrcon  = '' "
          ."    AND mtraut  = 'on' "
		  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
		  ."  ORDER BY 15,7,12";
	}
	else
		{
		 if ($wurg == "on" and $wgen == "on")
		    {
			  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtreme, mtrtri, mtrfco, mtrhco, mtrftc, mtrhtc, mtrsal, ubiptr, Mtrtur, Mtrgme"
				  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$wbasedato."_000044 G " //, ".$whce."_000035 E"
				  ."  WHERE ubihis  = orihis "
				  ."    AND ubiing  = oriing "
				  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
				  ."    AND oriced  = pacced "
				  ."    AND oritid  = pactid "
				 // ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
				  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
				  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
				  ."    AND ubihis  = mtrhis "
				  ."    AND ubiing  = mtring "
				 // ."    AND mtreme  = '".trim($wesp[0])."'"  //Ya no se consulta por la especialidad del medico sino por las especialidades sean de urgencias. (Espurg='on') 12 Agosto 2015 Jonatan.
				  ."	AND Espcod  = mtreme "
				  ."	AND Espurg  = 'on' "
				  ."    AND mtrcon  in ('',$wconductas_cubiculo) "
				  ."    AND mtrcua  = '' "
				  ."    AND mtraut  = 'on' ";
				   //Pacientes de otras especialidades que hayan sido tomados por el medico.
				  $q.= " UNION "
				  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtreme, mtrtri, mtrfco, mtrhco, mtrftc, mtrhtc, mtrsal, ubiptr, Mtrtur, Mtrgme "
				  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$wbasedato."_000048 F " //, ".$whce."_000035 E"
				  ."  WHERE ubihis  = orihis "
				  ."    AND ubiing  = oriing "
				  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
				  ."    AND oriced  = pacced "
				  ."    AND oritid  = pactid "
				  //."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
				  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
				  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
				  ."    AND ubihis  = mtrhis "
				  ."    AND ubiing  = mtring "
				  ."    AND mtreme  = medesp "
				  ."    AND medurg  = 'on' "
				  ."    AND mtrmed  = '".$wusuario."'"
				  ."    AND mtrcon  in ('',$wconductas_cubiculo) "
				  ."    AND mtrcua  = '' "
				  ."    AND mtraut  = 'on' ";

				 if($wgen=='on') //Si el medico es general se mostraran los pacientes de triage
				 {

				  $q.="UNION"
				  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtreme, mtrtri, mtrfco, mtrhco, mtrftc, mtrhtc, mtrsal, ubiptr, Mtrtur, Mtrgme "
				  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$wbasedato."_000048 F " //, ".$whce."_000035 E"
				  ."  WHERE ubihis  = orihis "
				  ."    AND ubiing  = oriing "
				  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
				  ."    AND oriced  = pacced "
				  ."    AND oritid  = pactid "
				 // ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
				  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
				  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
				  ."    AND ubihis  = mtrhis "
				  ."    AND ubiing  = mtring "
				  ."    AND mtreme  = medesp "
				  ."    AND medtri  = 'on' "
				  ."    AND medurg  = 'on' "
				  ."    AND mtrcon  = '' "
				  ."    AND mtraut  = 'on' "
				  ."  UNION "

				  //Paciente sin medico asignado pero con conducta diferente de alta o muerte.
				  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtreme, mtrtri, mtrfco, mtrhco, mtrftc, mtrhtc, mtrsal, ubiptr, Mtrtur, Mtrgme "
				  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E "
				  ."  WHERE ubihis  = orihis "
				  ."    AND ubiing  = oriing "
				  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
				  ."    AND oriced  = pacced "
				  ."    AND oritid  = pactid "
				  //."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
				  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
				  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
				  ."    AND ubihis  = mtrhis "
				  ."    AND ubiing  = mtring "
				  ."    AND mtrcon  = concod "
				  ."    AND mtrmed  = '' "
				  ."    AND mtrcon  != '' "
				  ."    AND conalt  != 'on' "
				  ."    AND conmue  != 'on' "
				  ."    AND mtraut  = 'on' ";

				 }
			}
			else
			   {
			    $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtreme, mtrtri, mtrfco, mtrhco, mtrftc, mtrhtc, mtrsal, ubiptr, Mtrtur, Mtrgme "
				    ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$wbasedato."_000048 F " //, ".$whce."_000035 E"
				    ."  WHERE ubihis  = orihis "
				    ."    AND ubiing  = oriing "
				    ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
				    ."    AND oriced  = pacced "
				    ."    AND oritid  = pactid "
				   // ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
				    ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
				    ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
				    ."    AND ubihis  = mtrhis "
				    ."    AND ubiing  = mtring "
				    ."    AND mtreme  = '".trim($wesp[0])."'"
				    ."    AND mtreme  = medesp "
				    ."    AND meduma  = '".$wusuario."'"
				    ."    AND mtrcon  in ('',$wconductas_cubiculo) "
				    ."    AND mtrcua  = ''"
					."    AND mtraut  = 'on' "
					 //Pacientes de otras especialidades que hayan sido tomados por el medico.
					." UNION "
					." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtreme, mtrtri, mtrfco, mtrhco, mtrftc, mtrhtc, mtrsal, ubiptr, Mtrtur, Mtrgme "
					."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$wbasedato."_000048 F " //, ".$whce."_000035 E"
					."  WHERE ubihis  = orihis "
					."    AND ubiing  = oriing "
					."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
					."    AND oriced  = pacced "
					."    AND oritid  = pactid "
					//."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
					."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
					."    AND ubisac  = '".$wcco."'"       //Servicio Actual
					."    AND ubihis  = mtrhis "
					."    AND ubiing  = mtring "
					."    AND mtreme  = medesp "
					."    AND medurg  = 'on' "
					."    AND mtrmed  = '".$wusuario."'"
					."    AND mtrcon  in ('',$wconductas_cubiculo) "
					."    AND mtrcua  = '' "
					."    AND mtraut  = 'on' ";
			   }
			$q.="  GROUP BY 1,2,3,4,5,6,7,8,9 "
			   ."  ORDER BY 15, 7, 12";
		}
  //echo $q;
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

	echo "
	<style type='text/css'>
		.fila1
		{
			background-color: 	#C3D9FF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fila2
		{
			background-color: 	#E8EEF7;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
	</style>
	";
  echo "<input type='HIDDEN' id='usuariotriage' name='usuariotriage' value=1>";
  echo "<br>";
  echo "<table>";

  echo "<tr class='tituloPagina'>";
  echo "<td align=center bgcolor=C3D9FF colspan=2><font size=2>$num PACIENTES</font></td>";
  echo "<td align=center bgcolor=C3D9FF colspan=17>PACIENTES EN ESPERA</td>";
  echo "</tr>";
  echo "<tr class=encabezadoTabla>";
  //echo "<th>Semaforo</th>";
  
  echo "<th>Turno</th>";
  echo "<th>Nivel Triage</th>";
  echo "<th>Prioridad</th>";
  echo "<th>Tiempo espera consulta<br><span style='font-family: verdana;font-weight:normal;font-size: 7pt;'>H:m:s</span></th>";
  echo "<th>Genero Médico</th>";
  echo "<th>Clasific.</th>";
  echo "<th>Sala de espera</th>";
  echo "<th width = 120px>TRIAGE</th>";
  // echo "<th>Fecha de Ingreso</th>";
  // echo "<th>Hora de Ingreso</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  echo "<th>Ir a Historía</th>";
  echo "<th>Nivel Triage</th>";
  echo "<th>Conducta a Seguir</th>";
  echo "<th>Sala</th>";
  echo "<th>Cubiculo</th>";
  echo "<th>Afinidad</th>";
  echo "<th>Medico Tratante</th>";
  echo "</tr>";

  if ($num > 0)
	 {

	 //Array de salas
    $q_sala =  	   "  SELECT Arecod, Aredes  "
				 . "    FROM ".$wbasedato."_000020, ".$wbasedato."_000169 "
				 ."	   WHERE habcco = '".$wcco."'"
				 ."		 AND habhis = '' "
				 ."		 AND habzon = Arecod "
				 ."		 AND Areest = 'on'"
				 ." GROUP BY habzon, habcco ";
	$res_sala = mysql_query($q_sala, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_sala." - ".mysql_error());

	$array_salas = array();

	while( $row_salas = mysql_fetch_assoc($res_sala)) {

		if(!array_key_exists($row_salas['Arecod'], $array_salas )){

			$array_salas[$row_salas['Arecod']] = $row_salas;

		}

	}

	//Array de cubiculos
	$q_cub =   " SELECT Habcod, Habcpa, Habzon, Habvir  "
			 . "   FROM ".$wbasedato."_000020 "
			 . "  WHERE habcub = 'on'"
			 . "	AND habdis = 'on'"
			 . "	AND habhis = '' "
			 . "	AND habest = 'on' "
			." ORDER BY habord, habcpa ";
	$res_cub = mysql_query($q_cub, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub." - ".mysql_error());

	$array_cubiculos_fisicos = array();
	$array_cubiculos_virtuales = array();

	while( $row_cubiculos = mysql_fetch_assoc($res_cub)) {
		
		//Se crean dos arreglos uno que contiene las ubicaciones fisicas y otra las virtuales, luego se valida por zona y si estan ocupadas todas las fisicas muestra el listado de las virtuales. Jonatan 21 sept 2017
		if($row_cubiculos['Habvir'] != 'on'){
			$array_cubiculos_fisicos[$row_cubiculos['Habzon']][$row_cubiculos['Habcod']] = $row_cubiculos;
		}elseif($row_cubiculos['Habvir'] == 'on'){			
			$array_cubiculos_virtuales[$row_cubiculos['Habzon']][$row_cubiculos['Habcod']] = $row_cubiculos;
		}

	}
	
	// echo "<div align=left>";
	// echo "<pre>";
	// print_r($array_cubiculos_fisicos);
	// echo "</pre>";
	// echo "</div>";
	
	// echo "<div align=left>";
	// echo "<pre>";
	// print_r($array_cubiculos_virtuales);
	// echo "</pre>";
	// echo "</div>";
	//Se recorren los pacientes que tiene especialidad asignada.

	 for($i=1;$i<=$num;$i++)
		 {
		  $row = mysql_fetch_array($res);
		  if (is_integer($i/2))
			 $wclass="fila1";
			else
			 $wclass="fila2";

		  $wptr = $row['ubiptr'];    //Procesos de traslado

		  if ($wptr=="on")    //Si la historia esta en proceso de traslado
                {
                $wclass="colorAzul4";
                }

		  $disabled_cub = "";
		  $disabled_sala = "";

		  $whis = $row[0];
		  $wing = $row[1];
		  $wpac = $row[2]." ".$row[3]." ".$row[4]." ".$row[5];
		  $wfin = $row[6];     //Fecha de Ingreso
		  $wtid = $row[7];
		  $wdpa = $row[8];
		  $wcur = $row[9];     //Indicador de si esta en Consulta
		  $wcon = $row[10];    //Conducta
		  $whin = $row[11];    //Hora de Ingreso
		  $wmed = $row[12];    //Medico que esta atendiendo al paciente
		  $wespecialidad = $row[13];    //Especialidad del medico asignado al paciente
		  //$wmed = $row[12]." ".$row[13]." ".$row[14]." ".$row[15];    //Medico
          $wtri = $row[14];  //Nivel de triage
		  $wfco = $row[15];  //Fecha consulta
		  $whco = $row[16];  //Hora de consulta
		  $wftc = $row['mtrftc'];  //Fecha de terminacion de la consulta
		  $whtc = $row['mtrhtc'];  //Hora de terminacion de la consulta
		  $wsala_asignada = $row['mtrsal'];  //Sala asignada

			// --> Turno del paciente, Jerson trujillo 2015-07-23
			$TurnoPaciente 		= $row['Mtrtur'];
			$clasificAtenPac	= '';
			$codPrioridad		= '';
			$usuarioTriage		= '';
			$salaEspera			= '';
			$fechaAsignaTriage	= '';
			
			// --> Consultar si el turno ya tiene llamado a consulta.
			$wbasedatoCliame 	= consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
			$sqlInfoTur 		= "
			SELECT Atullc, Atuulc, Atusea, Atupri, Atufat, Atuutr, Atuurt, Clanom
			  FROM ".$wbasedato."_000178 AS A LEFT JOIN ".$wbasedatoCliame."_000246 AS B ON A.Atucla = B.Clacod
			 WHERE Atutur = '".$TurnoPaciente."'
			";
			$resInfoTur = mysql_query($sqlInfoTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoTur):</b><br>".mysql_error());
			if($rowInfoTur = mysql_fetch_array($resInfoTur))
			{
				$codPrioridad		= $rowInfoTur['Atupri'];
				$salaEspera 		= $rowInfoTur['Atusea'];
				$clasificAtenPac	= $rowInfoTur['Clanom'];
				$fechaAsignaTriage	= $rowInfoTur['Atufat'];
				$usuarioTriage		= trim((($rowInfoTur['Atuurt'] != "") ? $rowInfoTur['Atuurt'] : $rowInfoTur['Atuutr']));	
				
				// --> Si tiene llamado a consulta y por el mismo usuario actual
				if($rowInfoTur['Atullc'] == 'on' && $rowInfoTur['Atuulc'] == $wusuario)
					$turnoConLlamadoEnVentanilla = $TurnoPaciente;
			}

		  $wcolor=convenciones($wfin, $whin);

		  $wpaciente_con_ordenes = buscar_paciente_ordenes($conex, $wbasedato, $whis, $wing);
		  $ir_a_ordenes = ir_a_ordenes($wemp_pmla, $wcco);
		  $fondo_verde = "";

		  //Si el centro de costos tiene activo ordenes electronicas y el paciente tambien pondra el fondo verde en la accion de ir a la historia. Jonatan 23 Octubre 2014
		  if($ir_a_ordenes == 'on'){

			if($wpaciente_con_ordenes == 'on'){

				$fondo_verde = "style='background-color:#3CB648;'";

				}

			}

           //Evalua si el medico es de triage
          $q1 =   " SELECT Medtri"
                . "   FROM ".$wbasedato."_000048 "
                . "  WHERE Medurg = 'on' "
                . "	   AND Meduma = '".$wusuario."'"
                . "	   AND Medtri = 'on'"
                . "	   AND Medest = 'on'";
          $res1 = mysql_query($q1, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $num1 = mysql_num_rows($res1);

		 echo "<tr class=".$wclass." id='trTurno_".$TurnoPaciente."'>";
		 //echo "<td bgcolor='".$wcolor."'>&nbsp</td>";

			// --> Clasificación y Turno del paciente, Jerson Trujillo.
			echo "
				<td align='center'>";
				if($TurnoPaciente != '')
			echo "
					<b>".substr($TurnoPaciente, 4)."</b><br>
					<img id='imgLlamar".$TurnoPaciente."' 		style='cursor:pointer;".(($tieneLlamado) ? "display:none" : "")."' class='botonLlamarPaciente' width='20' heigth='20' tooltip='si' title='&nbsp;&nbsp;Llamar a consulta' src='../../images/medical/root/Call2.png'	onclick='llamarPacienteAtencion(\"".$whis."\",\"".$wing."\",\"".$TurnoPaciente."\")'>
					<img style='cursor:pointer;display:none' 	class='botonColgarPaciente' width='20' heigth='20' tooltip='si' title='&nbsp;&nbsp;Cancelar llamado'  	src='../../images/medical/root/call3.png'	onclick='cancelarLlamarPacienteConsulta(\"".$TurnoPaciente."\")'>
					<img style='display:none' 					class='botonColgarPaciente' src='../../images/medical/ajax-loader1.gif'>
				</td>";
			$wniveltriage = niveltriage($wtri);

			//Si el paciente tiene asociado triage, se imprime su nivel, si esta vacio, imprime que tipo de convenio tiene (con o sin convenio)
			echo "<td align=center><b>".$wniveltriage."</b></td>";
			// --> Prioridad 2016-06-21
			echo "<td align='center'style='font-size:8pt'>".$arrayPrioridades[$codPrioridad]."</td>";
			
			// --> Tiempo de espera para la consulta
			if($fechaAsignaTriage != '')
			{
				$tiempoMax		= "";
				$tiempoEspera 	= strtotime(date("Y-m-d H:i:s"))-strtotime($fechaAsignaTriage);
				$tiempoCero 	= strtotime('1970-01-01 00:00:00')-18000;
				// --> Calcular si se supera el tiempo de espera
				switch($wtri)
				{
					// --> Atencion inmediata
					case '01':
					{
						break;
					}
					// --> Maximo 30 minutos
					case '02':
					{
						$tiempoMax = $tiempoCero+1800;							
						break;
					}
					// --> Maximo 1 hora
					case '03':
					{
						$tiempoMax = $tiempoCero+3600;
						break;
					}
					// --> Maximo 2 horas
					case '04':
					{
						$tiempoMax = $tiempoCero+7200;
						break;
					}
					// --> Maximo 4 horas
					case '05':
					{
						$tiempoMax = $tiempoCero+14400;
						break;
					}					
				}
				
				// --> Pintar con alerta
				if($tiempoEspera > $tiempoMax || $wtri == '01')
					echo "<td align='center'style='font-size:8pt'><span blink style='color:red;font-weight:bold'>".gmdate("H:i:s", (strtotime(date("Y-m-d H:i:s"))-strtotime($fechaAsignaTriage)))."</td>";
				else
					echo "<td align='center'style='font-size:8pt'>".gmdate("H:i:s", (strtotime(date("Y-m-d H:i:s"))-strtotime($fechaAsignaTriage)))."</td>";
			}
			else
				echo "<td align='center'style='font-size:8pt'></td>";
			// --> Genero medico
			//$generoMedico = (($row['Mtrgme'] == 'F') ? 'Femenino' : (($row['Mtrgme'] == 'M') ? 'Masculino' : ''));
			$generoMedico = (($row['Mtrgme'] == 'F') ? 'Femenino' : (($row['Mtrgme'] == 'M') ? 'Masculino' : ($row['Mtrgme'] == 'C') ? 'COVID19' : ''));
			if($generoMedico != '')
				echo "
				<td align='center' tooltip='si' style='cursor:help' title='<span style=\"font-weight:normal;font-size:9pt\" align=\"center\">Genero médico que el paciente<br>desea que lo atienda.</span>' style='font-size:8pt'>&nbsp;".$generoMedico."&nbsp;</td>
				";
			else
				echo "<td></td>";
			
			echo "<td align='center' style='font-size:8pt'>&nbsp;".$clasificAtenPac."&nbsp;</td>";

			// --> Sala de espera
			echo "<td align='center'style='font-size:8pt'>".$arraySalasEspera[$salaEspera]."</td>";

						
 		  //$wtipotriage = ponerTipotriage($wespecialidad);
		 // echo "<td align=center>   ".$wtipotriage."</td>";
			
			// --> Obtener nombre de enfermera que hizo triage
			$sqlEnfTriage	= "
			SELECT Descripcion
			  FROM usuarios
			 WHERE Codigo = '".$usuarioTriage."'
			";
			$resEnfTriage = mysql_query($sqlEnfTriage, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEnfTriage):</b><br>".mysql_error());
			if($rowEnfTriage = mysql_fetch_array($resEnfTriage))
			{
				echo "<td align='center' style='font-size:8pt'>&nbsp;".$rowEnfTriage['Descripcion']."&nbsp;</td>";
			}
			else
			{
				echo "<td align='center' style='font-size:8pt'>&nbsp;</td>";
			}		 
		 
		  // echo "<td align=center>   ".$wfin."</td>";
		  // echo "<td align=center>   ".$whin."</td>";
		  echo "<td align=center><b>".$whis." - ".$wing."</b></td>";
		  echo "<td align=left  ><b>".$wpac."</b></td>";

		  $irhce="off";  //Permite ingresar a la hce sin dar clic sobro el radio button de ir a hce
			if (trim($wmed) == "" or $wmed == $wusuario)
			{
				if ($wcur == "on" and $wmed !="")
				{
					echo "<td align=center $fondo_verde><input type='radio' nomPac='".$wpac."' style='cursor:pointer;' botonIrConsulta='".$TurnoPaciente."' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing, \"$wdpa\", \"$wtid\", $i)' checked></td>";
					$hayPacienteEnConsulta = true;
				}
				else
				   echo "<td align=center $fondo_verde><input type='radio' nomPac='".$wpac."' style='cursor:pointer;".(($TurnoPaciente != '') ? "display:none" : "")."' botonIrConsulta='".$TurnoPaciente."' name='wirhce$i' id='wirhce$i' onclick='activarConsulta($whis, $wing, \"$wdpa\", \"$wtid\", $i, \"$irhce\", \"$wespecialidad\", \"$wemp_pmla\", \"$wesp[0]\", \"$wftc\", \"$whtc\")'>";
			}
			else
				echo "<td align=center $fondo_verde><input type='radio' nomPac='".$wpac."' style='cursor:pointer;display:none' botonIrConsulta='".$TurnoPaciente."' name='wirhce$i' id='wirhce$i' disabled></td>";


            //-----------------------
            //Nivel de triage
            echo "<td align=center><select id='triage$i' name='triage$i' onchange='colocarTriage($whis, $wing, $i, \"$irhce\" , \"$wfecha\", \"$whora\", \"$wftc\", \"$whtc\")'>";
            echo "<option value=''></option>";
			  if (isset($wtri) and $wtri !='')                              //Si selecciono una opcion del dropdown
				 {
				  $q3 =     " SELECT Mtrtri, Trinom "
                          ."   FROM ".$whce."_000040, ".$whce."_000022 "
                          ."  WHERE Mtrtri = Tricod"
                          ."    AND Mtrhis = '".$whis."'"
                          ."    AND Mtrhis = '".$whis."'"
                          ."    AND Tricod = '".$wtri."'";
				  $res3 = mysql_query($q3, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row3 = mysql_fetch_array($res3);
				  echo "<option selected value=$row3[0]>".$row3[1]."</option>";
				 }

            $q3 =      " SELECT Tricod, Trinom "
                    . "   FROM ".$whce."_000040 ";
            $res3 = mysql_query($q3, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num3 = mysql_num_rows($res3);

			 for ($j=1;$j<=$num3;$j++)
				 {
				   $row3 = mysql_fetch_array($res3);
				  echo "<option value=$row3[0]-$row3[2]>".$row3[1]."</option>";
				 }
			  echo "</select></td>";


		  if (trim($wmed) == "" or $wmed == $wusuario)
		     {
			  echo "<td align=center><select id='conducta$i' name='wconducta$i' onchange='colocarConducta($whis, $wing, $i, \"$irhce\", \"$wftc\", \"$whtc\")'>";

			  if (isset($wcon))                              //Si selecciono una opcion del dropdown
				 {
				  $q = " SELECT condes "
					 . "   FROM ".$whce."_000035 "
					 . "  WHERE concod = '".$wcon."'";
				  $res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row2 = mysql_fetch_array($res2);

				  echo "<option selected value='$wcon'>".$row2[0]."</option>";
				 }

			  echo "<option value=''>&nbsp</option>";

			  //============================================================================================================
			  //Aca coloco todas las conductas
			  //============================================================================================================
			   // Valido primero si el medico que esta atendiendo es de triage // Abril 17 de 2012
			  $q1 = " SELECT Medtri"
				 . "   FROM ".$wbasedato."_000048 "
				 . "  WHERE Medurg = 'on' "
				 . "	AND Meduma = '".$wusuario."'"
				 . "	AND Medtri = 'on'"
				 . "	AND Medest = 'on'";
			  $res1 = mysql_query($q1, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num1 = mysql_num_rows($res1);

			if ($num1 > 0)
				{
				$q =  " SELECT concod, condes, conesp, concub "
					. "   FROM ".$whce."_000035 "
					. "  WHERE conest = 'on'"
					. "	  AND contri = 'on'"
					."   ORDER BY conord ";

				}
			else
				{
				  $q =  " SELECT concod, condes, conesp, concub "
					 . "   FROM ".$whce."_000035 "
					 . "  WHERE conest = 'on' "
					 . "    AND contri != 'on'"
					 . "     OR conmue = 'on'"
					 . "     OR conalt = 'on'"
				     ."ORDER BY conord ";

				}

			$res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num2 = mysql_num_rows($res2);

			 for ($j=1;$j<=$num2;$j++)
				 {
				  $row1 = mysql_fetch_array($res2);
				  echo "<option value='$row1[0]-$row1[2]-$row1[3]'>".$row1[1]."</option>";
				 }
			  echo "</select></td>";
		     }
			else
			 {
				$disabled_sala = "disabled";
				$disabled_cub = "disabled";

				echo "<td align=center><b>En Consulta</b></td>";
				echo "<input type='HIDDEN' id='conducta$i' name='wconducta$i' value='$wcon'>";
                echo "<input type='HIDDEN' id='conducta$i' name='wtriage$i' value='$wcon'>";
			 }
		  //============================================================================================================

		//Si el paciente tiene conducta, se deja activo el seleccionador de cubiculos, solo se mostraran en la lista los pacientes que tengan conducta asociada a cubiculo.
		if($wcon == ""){

			$disabled_sala = "disabled";
			$disabled_cub = "disabled";

		}

		//Salas
		echo "<td>";
			echo "<select id='sala$i' name='sala$i' $disabled_sala onchange='filtrarCubiculo($whis, $wing, \"$i\", \"off\", \"\", \"$irhce\")'>";
			echo "<option value=''>&nbsp</option>";

			 if(is_array($array_salas)){
				foreach($array_salas as $key => $row_sala){
					$sala_seleccionada = "";
					if($wsala_asignada == $row_sala['Arecod']){
						$sala_seleccionada = "selected";
					}
					echo "<option value='".$row_sala['Arecod']."' $sala_seleccionada>".$row_sala['Aredes']."</option>";
				}
			 }
			echo "</select>";
		echo "</td>";

		if($wsala_asignada == ""){

			$disabled_cub = "disabled";

		}
		//Cubiculos
		echo "<td id='dato_cubiculos$i'>";
			 echo "<select id='cubiculo$i' name='wconducta$i' $disabled_cub onchange='colocarCubiculo($whis, $wing, $i, \"$irhce\", \"$wftc\", \"$whtc\")'>";

			echo "<option value=''>&nbsp</option>";

			//Si la sala esta asignada al paciente entonces tomo ese dato y solo muestro en el select los que tengan esa sala asignada de acuerdo al key del array.
			if($wsala_asignada){

					$array_cubiculos_aux = $array_cubiculos_fisicos[$wsala_asignada];
					
					if(count($array_cubiculos_aux) == 0){
						
						$array_cubiculos_aux = @$array_cubiculos_virtuales[$wsala_asignada];
					}
					
					 if(is_array($array_cubiculos_aux)){

							 foreach ($array_cubiculos_aux as $key_cub => $row_cub){

							 echo "<option value='".$row_cub['Habcod']."'>".$row_cub['Habcpa']."</option>";

							}
						 }
				}else{

					 if(is_array($array_cubiculos)){
					 foreach ($array_cubiculos as $key_cub => $datos_cub){
							foreach($datos_cub as $key_aux => $row_cub){

							 echo "<option value='".$row_cub['Habcod']."'>".$row_cub['Habcpa']."</option>";

							 }
						}
					 }
			}
			echo "</select>";
		echo "</td>";

		  //======================================================================================================
		  //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
		  $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
		  if ($wafin)
			 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			else
			  echo "<td>&nbsp</td>";
		  //======================================================================================================

		  $wmednom="";
		  //Traigo el medico que esta tratando al paciente, para que lo muestre y ademas sirve para indicar en el drop down
		  //de conducta para que no deje modifcar o poner conducta cuando este en consulta
		  if ($wmed != "")
		     {
			  $q = " SELECT medno1, medno2, medap1, medap2 "
				 . "   FROM ".$wbasedato."_000048 "
				 . "  WHERE meduma = '".$wmed."'";
			  $res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num1 = mysql_num_rows($res2);
			  if ($num1 > 0)
				 {
				 $row1 = mysql_fetch_array($res2);
				 $wmednom = $row1[0]." ".$row1[1]." ".$row1[2]." ".$row1[3];
				 }
			 }
		  echo "<td style='font-size:8pt' align=center>".$wmednom."</td>";
		  echo "</tr>";
		 }
	  }
	echo "</table>";

	echo "<input type='hidden' id='turnoLlamadoPorEsteUsuario' 	value='".$turnoConLlamadoEnVentanilla."'>";
	echo "<input type='hidden' id='hayPacienteEnConsulta' 		value='".$hayPacienteEnConsulta."'>";

 }

 //Trae el nombre del medico asociado a un paciente.
 function nombremedico($wmed)
 {

     global $conex;

     //Traigo los indicadores si el medico es de urgencias y ademas es Pediatra u Ortopedista, si no, es porque es general
    $q = " SELECT Descripcion "
        ."   FROM usuarios "
        ."  WHERE codigo = '".$wmed."'"
        ."    AND Activo = 'A' ";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row = mysql_fetch_array($res);

    $wnombre = $row['Descripcion'];

    return $wnombre;

 }

function mostrarPacientesComunes($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, &$k, $zona)
 {

  global $conex;
  global $zona;

  $wgen = "on";
  $wped = "off";
  $wort = "off";
  $wesp[0] = "%";
  $wesp[1] = "";
  $wtriage_espec = consultarAliasPorAplicacion($conex, $wemp_pmla, "TriageAutomatico");
  $wdato_esp_tri = explode("-", $wtriage_espec);
  $wespecial_auto =  $wdato_esp_tri[0]; //Especialidad emergencia

  //Traigo los indicadores si el medico es de urgencias y ademas es Pediatra u Ortopedista, si no, es porque es general
  $q = " SELECT medurg, medped, medort, medesp, medtri "
      ."   FROM ".$wbasedato."_000048 "
	  ."  WHERE meduma = '".$wusuario."'"
	  ."    AND medest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  if(isset($zona))	{

	$filtro_zona = " AND mtrsal LIKE '%".$zona."%'";

  }

  if ($num > 0)
     {
      $row = mysql_fetch_array($res);

	  $wurg = $row['medurg'];
	  $wped = $row['medped'];
	  $wort = $row['medort'];
	  $wesp = explode("-",$row['medesp']);
	  $wmedtri = $row['medtri'];

	  if ($wped == "on" or $wort == "on")   //Indica que es Especialista entonces obligo a que entre por Ped u Ort
	     $wgen = "off"; // Indica que no es medico general.
     }

	// --> Obtener maestro de salas de espera: Jerson trujillo 2015-07-21
	$arraySalasEspera = array();
	$sqlObtenerSalas = "
	SELECT Salcod, Salnom
	  FROM ".$wbasedato."_000182
	 WHERE Salest = 'on'
	";
	$resObtenerSalas = mysql_query($sqlObtenerSalas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlObtenerSalas):</b><br>".mysql_error());
	while($rowObtenerSalas = mysql_fetch_array($resObtenerSalas))
		$arraySalasEspera[$rowObtenerSalas['Salcod']] = $rowObtenerSalas['Salnom'];
	
	// --> Obtener maestro de salas de espera: Jerson trujillo 2016-06-21
	$arrayPrioridades 	= array();
	$sqlPrioridades 	= "
	SELECT Pricod, Prinom
	  FROM ".$wbasedato."_000206
	 WHERE Priest  = 'on'
	";
	$resPrioridades = mysql_query($sqlPrioridades, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPrioridades):</b><br>".mysql_error());	
	while($rowPrioridades = mysql_fetch_array($resPrioridades))
		$arrayPrioridades[$rowPrioridades['Pricod']] = $rowPrioridades['Prinom'];

	//Los enfermeros de triage no ven pacientes atendidos y activos porque las conductas que ellos le dan a los pacientes son especialidades.
     if ($wgen == 'on' and $wmedtri != 'on')
     {
	  //Aca trae los pacientes que estan en Urgencias en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
	  //y que no esten en proceso ni en alta y que sean de Medicos Generales
	  $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal, mtrftc, mtrhtc, Mtrtur, Mtrgme "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000044 G"
		  ."  WHERE ubihis  = orihis "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  //."    AND mtreme LIKE '".$wesp[0]."'"
		  ."	AND Espcod  = mtreme "
		  ."	AND Espurg  = 'on' "
		  ."    AND mtrcur != 'on' "
		  ."    AND mtrcon  = concod "
		  ."    AND conalt != 'on' "
		  ."    AND conmue != 'on' "
		  ."    AND concom  = 'on' "
		  .$filtro_zona
		  ."    AND mtrftc != '0000-00-00' "
          ."    AND mtrhtc != '00:00:00' "

    //Se agrega este union para que los medicos generales puedan ver los pacientes atendidos y activos con conducta conort='on' y poder darles de alta // 27 Noviembre - Jonatan
		  ." UNION "
          ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal, mtrftc, mtrhtc, Mtrtur, Mtrgme "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E"
		  ."  WHERE ubihis  = orihis "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  ."    AND conort  = 'on'"
		  ."    AND mtrcur != 'on' "
		  ."    AND mtrcon  = concod "
		  ."    AND conalt != 'on' "
		  ."    AND conmue != 'on' "
		  ."    AND concom  = 'on' "
		  .$filtro_zona
		  ."    AND mtrftc != '0000-00-00' "
          ."    AND mtrhtc != '00:00:00' "
		  ."UNION"

		  //Este union muestra los pacientes que tienen especilidad emergencia.
		  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal, mtrftc, mtrhtc, Mtrtur, Mtrgme "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E"
		  ."  WHERE ubihis  = orihis "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  ."    AND mtreme LIKE '".$wespecial_auto."'"
		  ."    AND mtrcur != 'on' "
		  ."    AND mtrcon  = concod "
		  ."    AND conalt != 'on' "
		  ."    AND conmue != 'on' "
		  ."    AND concom  = 'on' "
		  .$filtro_zona
		  ."    AND mtrftc != '0000-00-00' "
          ."    AND mtrhtc != '00:00:00' "

		//Se agrega este union para que los medicos generales puedan ver los pacientes atendidos y activos con especialidad ortopedia, sin necesidad de que tenga conducta de ortopedia.
		." UNION "
		." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal, mtrftc, mtrhtc, Mtrtur, Mtrgme "
		." FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
		." WHERE ubihis = orihis "
		." AND oriori = '".$wemp_pmla."'" //Empresa Origen de la historia,
		." AND oriced = pacced "
		." AND oritid = pactid "
		." AND ubiald != 'on' " //Que no este en Alta Definitiva
		." AND ubisac = '".$wcco."'" //Servicio Actual
		." AND ubihis = mtrhis "
		." AND ubiing = mtring "
		." AND conort != 'on'"
		." AND mtrcur != 'on' "
		." AND mtrcon = concod "
		." AND conalt != 'on' "
		." AND conmue != 'on' "
		." AND concom = 'on' "
		.$filtro_zona
		." AND mtrftc != '0000-00-00' "
		." AND mtrhtc != '00:00:00' "
		." AND mtreme = medesp "
		." AND medort = 'on' "

	  //Pacientes con alta en proceso. 25 Marzo de 2015 Jonatan
	  ." UNION "
	  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal, mtrftc, mtrhtc, Mtrtur, Mtrgme "
	  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E "
	  ."  WHERE ubihis  = orihis "
	  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
	  ."    AND oriced  = pacced "
	  ."    AND oritid  = pactid "
	  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
	  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
	  ."    AND ubihis  = mtrhis "
	  ."    AND ubiing  = mtring "
	  ."    AND mtrcur != 'on' "
	  ."	AND ubialp = 'on'"
	  ."    AND mtrcon = concod "
	  ."    AND conalt = 'on' "
	  .$filtro_zona
	  ."    AND mtrftc != '0000-00-00' "
	  ."    AND mtrhtc != '00:00:00' "
	  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
		  ."  ORDER BY 7, 12 ";

     }
     else
     {
      //Aca trae los pacientes que estan en Urgencias en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado,
	  //y que no esten en proceso ni en alta y que sean de Medicos con especialidad(pediatria u ortopedia).
	 $q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal, mtrftc, mtrhtc, Mtrtur, Mtrgme "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E, ".$wbasedato."_000048 F"
		  ."  WHERE ubihis  = orihis "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  ."    AND mtreme LIKE '".$wesp[0]."'"
		  ."    AND mtrcur != 'on' "
		  ."    AND mtrcon  = concod "
		  ."    AND conalt != 'on' "
		  ."    AND conmue != 'on' "
		  .$filtro_zona
		  ."    AND mtrmed  = meduma "
		  ."    AND medurg  = 'on' "

		  //Pacientes con alta en proceso. 25 Marzo de 2015 Jonatan
		  ." UNION "
		  ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal, mtrftc, mtrhtc, Mtrtur, Mtrgme "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E "
		  ."  WHERE ubihis  = orihis "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  ."    AND mtreme LIKE '".$wesp[0]."'"
		  ."    AND mtrcur != 'on' "
		  ."	AND ubialp = 'on'"
		  .$filtro_zona
		  ."    AND mtrftc != '0000-00-00' "
		  ."    AND mtrhtc != '00:00:00' "
		  ."    AND mtrcon = concod "
		  ."    AND conalt = 'on' "
		  //Pacientes con conducta de ortopedia.
		  ."  UNION "
	      ." SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal, mtrftc, mtrhtc, Mtrtur, Mtrgme "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E"
		  ."  WHERE ubihis  = orihis "
		  ."    AND ubiing  = oriing "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		  ."    AND ubiptr != 'on' "             //Solo los pacientes que no esten siendo trasladados
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$wcco."'"       //Servicio Actual
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  ."    AND conort  = 'on'"
		  ."    AND mtrmed  = ''"
		  ."    AND mtreme  = '".trim($wesp[0])."'"
		  ."    AND mtrcur != 'on' "
		  ."    AND mtrcon  = concod "
		  .$filtro_zona
		  ."    AND conalt != 'on' "
		  ."    AND conmue != 'on' "
		  ."  GROUP BY 1,2,3,4,5,6,7,8,9 "
		  ."  ORDER BY 7, 12 ";
     }

  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);
  //echo $q;
  echo "<br>";
  echo "<table>";
  echo "<td class=fila1><b>Filtrar sala:</b>";

   //Array de salas
    $q_sala =  	   "  SELECT Arecod, Aredes  "
				 . "    FROM ".$wbasedato."_000020, ".$wbasedato."_000169 "
				 ."	   WHERE habcco = '".$wcco."'"
				 ."		 AND habzon = Arecod "
				 ."		 AND Areest = 'on'"
				 ." GROUP BY habzon ";
	$res_sala = mysql_query($q_sala, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_sala." - ".mysql_error());

	$array_salas = array();

	while( $row_salas = mysql_fetch_assoc($res_sala)) {

		if(!array_key_exists($row_salas['Arecod'], $array_salas )){

			$array_salas[$row_salas['Arecod']] = $row_salas;

		}

	}

	//Selecionador de zona para filtrar los pacientes atendidos y activos
	echo "<select id='zona' name='zona' onchange='enter1()'>";
			echo "<option value=''>&nbsp</option>";

			if(isset($zona) and $zona == "%"){
					$selected = "selected";
					}

			echo "<option value='%' $selected>Todas</option>";
			if(is_array($array_salas)){
				foreach($array_salas as $key => $row_sala){

					$selected1 = "";

					if(isset($zona) and $zona == $row_sala['Arecod']){
						$selected1 = "selected";
						}

					echo "<option value='".$row_sala['Arecod']."' $selected1>".$row_sala['Aredes']."</option>";
				}
			}

	echo "</select>";
  echo "</td>";
  echo "</table>";

    //Array de salas
    $q_sala =  	   "  SELECT Arecod, Aredes  "
				 . "    FROM ".$wbasedato."_000020, ".$wbasedato."_000169 "
				 ."	   WHERE habcco = '".$wcco."'"
				 ."		 AND habzon = Arecod "
				 ."		 AND Areest = 'on'"
				 ." GROUP BY habzon, habcco ";
	$res_sala = mysql_query($q_sala, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_sala." - ".mysql_error());

	$array_salas = array();

	while( $row_salas = mysql_fetch_assoc($res_sala)) {

		if(!array_key_exists($row_salas['Arecod'], $array_salas )){

			$array_salas[$row_salas['Arecod']] = $row_salas;

		}

	}

	//Array de cubiculos
	$q_cub =   " SELECT Habcod, Habcpa, Habzon, Habhis, Habing, Habvir  "
			 . "   FROM ".$wbasedato."_000020 "
			 . "  WHERE habcub = 'on'"
			 . "	AND habest = 'on' "
			 . "	AND habdis = 'on' "
			." ORDER BY habord, habcpa ";
	$res_cub = mysql_query($q_cub, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub." - ".mysql_error());

	$array_cubiculos_fisicos = array();
	$array_cubiculos_virtuales = array();

	while( $row_cubiculos = mysql_fetch_assoc($res_cub)) {
		
		//Se crean dos arreglos uno que contiene las ubicaciones fisicas y otra las virtuales, luego se valida por zona y si estan ocupadas todas las fisicas muestra el listado de las virtuales. Jonatan 21 sept 2017
		if($row_cubiculos['Habvir'] != 'on'){
			$array_cubiculos_fisicos[$row_cubiculos['Habzon']][$row_cubiculos['Habcod']] = $row_cubiculos;
		}elseif($row_cubiculos['Habvir'] == 'on'){			
			$array_cubiculos_virtuales[$row_cubiculos['Habzon']][$row_cubiculos['Habcod']] = $row_cubiculos;
		}

	}
	
	// echo "<div align=left>";
	// echo "<pre>";
	// print_r($array_cubiculos_fisicos);
	// echo "</pre>";
	// echo "</div>";
	
	// echo "<div align=left>";
	// echo "<pre>";
	// print_r($array_cubiculos_virtuales);
	// echo "</pre>";
	// echo "</div>";
	
	//Array de cubiculos asignados
	$q_cub_asignados =   " SELECT Habcod, Habcpa, Habzon, Habhis, Habing, Habord  "
						 . "   FROM ".$wbasedato."_000020 "
						 . "  WHERE habcub = 'on'"
						 . "	AND habest = 'on'";
	$res_cub_asignados = mysql_query($q_cub_asignados, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub_asignados." - ".mysql_error());

	$array_cubiculos_asignados = array();

	while( $row_cubiculos_asignados = mysql_fetch_assoc($res_cub_asignados)) {

			if($row_cubiculos_asignados['Habhis'] != ""){
			$array_cubiculos_asignados[$row_cubiculos_asignados['Habhis']."-".$row_cubiculos_asignados['Habing']] = $row_cubiculos_asignados;
			}

		}

	$q =  " SELECT concod, condes "
		. "   FROM ".$whce."_000035 "
		. "  WHERE concub = 'on' ";
	$res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_conductas_cub = array();

	//Array de conductas
	while( $row_conductas_cub = mysql_fetch_assoc($res2)) {

			$array_conductas_cub[$row_conductas_cub['concod']] = $row_conductas_cub;

		}

	$array_pacientes_atendidos = array();

	//Creacion de array de pacientes atendidos al cual se le agrega el codigo de la habitacion y el orden.
	while($row_atendidos = mysql_fetch_assoc($res)){

		if(!array_key_exists($row_atendidos['ubihis']."-".$row_atendidos['ubiing'], $array_pacientes_atendidos)){

			$row_atendidos_aux = array();

			$row_atendidos_aux['habord'] = $array_cubiculos_asignados[$row_atendidos['ubihis']."-".$row_atendidos['ubiing']]['Habord'];
			$row_atendidos_aux['habcod'] = $array_cubiculos_asignados[$row_atendidos['ubihis']."-".$row_atendidos['ubiing']]['Habcod'];

			if(is_array($row_atendidos)){
				foreach($row_atendidos as $key1 => $value1){

					$row_atendidos_aux[$key1] = $value1;
				}
			}
			$array_pacientes_atendidos[$row_atendidos['ubihis']."-".$row_atendidos['ubiing']] = $row_atendidos_aux;

		}
	}

	//Ordeno el arreglo de menor a mayor en el campo habord el cual es la primera posicion del arreglo.
	asort($array_pacientes_atendidos);
	
  $wtiempos_urgencias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tiempos_urgencias');
  
  $wtiempos_urgencias_aux = explode("-", $wtiempos_urgencias);
  $wtiempo_minimo = $wtiempos_urgencias_aux[0];
  $wtiempo_maximo = $wtiempos_urgencias_aux[1];
  
  echo "<br>";
  echo "<table>";
  echo "<tr class='tituloPagina'>";
  echo "<td align=center bgcolor=C3D9FF colspan=2><font size='2'>".count($array_pacientes_atendidos)." pacientes</font></td>";
  echo "<td align=center bgcolor=C3D9FF colspan=15>PACIENTES ATENDIDOS Y ACTIVOS</td>";
  echo "</tr>";
  echo "<tr class=encabezadoTabla>";
  echo "<th title='Tiempo basado en la hora de terminación <br>de la consulta hasta la hora actual.' class='msg'>Semáforo
		  <table>
				<tr>
				<td style='background-color:#FCEF69;font-size: 7pt;border-radius: 4px;border:1px solid #999999;padding:2px' nowrap>&nbsp;> $wtiempo_minimo horas&nbsp;</td>
				</tr>
				<tr>
				<td style='background-color:#FDCB7F;font-size: 7pt;border-radius: 4px;border:1px solid #999999;padding:2px' nowrap>&nbsp;> $wtiempo_maximo horas&nbsp;</td>
				</tr>				
				</table>
		  
		  </th>";
  echo "<th>Clasific.</th>";
  echo "<th>Turno</th>";
  echo "<th>Genero Médico</th>";
  echo "<th>Sala de espera</th>";
  echo "<th>Prioridad</th>";
  echo "<th>Nivel de triage</th>";
  echo "<th>Fecha de Ingreso</th>";
  echo "<th>Hora de Ingreso</th>";
  echo "<th>Historia</th>";
  echo "<th>Paciente</th>";
  echo "<th>Ir a Historía</th>";
  echo "<th>Conducta a Seguir</th>";
  echo "<th>Sala</th>";
  echo "<th>Cubiculo</th>";
  echo "<th>Afinidad</th>";
  echo "<th>Medico Tratante</th>";
  echo "</tr>";

  if ($num > 0)
	 {

	 $i = 0;

	 if(is_array($array_pacientes_atendidos)){

	 foreach($array_pacientes_atendidos as $key => $row)
		 {
		 $i++;
		  if (is_integer($i/2))
			 $wclass="fila1";
			else
			   $wclass="fila2";
		  //ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, C.fecha_data, pactid, pacced, mtrcur, mtrcon, C.hora_data, mtrmed, mtrtri, ubiptr, ubialp, mtrmed, mtrsal"
		  $disabled_sala = "";
		  $disabled_cub = "";
		  $whis = $row['ubihis'];
		  $wing = $row['ubiing'];
		  $wpac = $row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2'];
		  $wfin = $row['fecha_data'];     //Fecha de Ingreso
		  $wtid = $row['pactid'];
		  $wdpa = $row['pacced'];
		  $wcur = $row['mtrcur'];     //Indicador de si esta en Consulta
		  $wcon = $row['mtrcon'];    //Conducta
		  $whin = $row['hora_data'];    //Hora de Ingreso
		  $wmed = $row['mtrmed'];    //Medico
          $wtriage = $row['mtrtri']; //Nivel de triage
          $wptr = $row['ubiptr'];    //Procesos de traslado
          $walp = $row['ubialp'];    //Alta en proceso
		  $wmedico_asociado = $row['mtrmed'];    //Alta en proceso
		  $disabled = "";
		  $wnombretriage = niveltriage($wtriage);
		  $wcolor=convenciones($wfin, $whin);
		  $wsala_asignada = $row['mtrsal'];
		  $dato_cubiculoActual = $array_cubiculos_asignados[$whis."-".$wing]['Habcod'];
		  $desc_cubiculoActual = $array_cubiculos_asignados[$whis."-".$wing]['Habcpa'];
		  $wftc = $row['mtrftc'];  //Fecha de terminacion de la consulta
		  $whtc = $row['mtrhtc'];  //Hora de terminacion de la consulta
		  $wfecha_hora_term_consul = $wftc." ".$whtc; //Fecha y hora de terminacion de la consulta.
		  
		   if ($wptr=="on")    //Si la historia esta en proceso de traslado
                {
                $wclass="colorAzul4";
                }

            if ($walp=="on")    //Si la historia esta en proceso de alta
                {
                $wclass="fondoAmarillo";
                }


		  echo "<tr id='tr_atendidos$i' class=".$wclass.">";
		  //echo "<td bgcolor='".$wcolor."'>&nbsp</td>";

			// --> Consultar informacion del turno.
			$TurnoPaciente 		= $row['Mtrtur'];
			$codPrioridad 		= '';
			$wbasedatoCliame 	= consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
			$sqlInfoTur 		= "
			SELECT Atusea, Atupri, Clanom
			  FROM ".$wbasedato."_000178 AS A LEFT JOIN ".$wbasedatoCliame."_000246 AS B ON A.Atucla = B.Clacod
			 WHERE Atutur = '".$TurnoPaciente."'
			";
			$resInfoTur = mysql_query($sqlInfoTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoTur):</b><br>".mysql_error());
			if($rowInfoTur = mysql_fetch_array($resInfoTur))
			{
				$clasificAtenPac 	= $rowInfoTur['Clanom'];
				$salaEspera 		= $rowInfoTur['Atusea'];
				$codPrioridad 		= $rowInfoTur['Atupri'];
			}
			
			$color_celda = "";
					
			if($wfecha_hora_term_consul != '0000-00-00 00:00:00'){
			
				//Verifica cuantos minutos han transcurrido entre la hora de llegada del paciente de la tabla 18 y la fecha y hora actual
				$minutos = (strtotime(date("Y-m-d H:i:s")) - strtotime($wfecha_hora_term_consul)) / 60;
				$minutos = abs($minutos); 
				$minutos = floor($minutos);
				$tiempo_en_segundos = $minutos * 60;
				$formato_horas = tiempo_transcurrido($tiempo_en_segundos);
				
				$wtiempo_minimo_aux = $wtiempo_minimo * 60;
				$wtiempo_maximo_aux = $wtiempo_maximo * 60;
				
				//Si es mayor a 2 horas pinta el fondo amarillo
				if($minutos >= $wtiempo_minimo_aux){	
					$amarillo = "amarillo";
					$color_celda = 'style="background-color: #FCEF69; " ';
					
				}
				
				if($minutos >= $wtiempo_maximo_aux){  //Si es mayor a 6 horas pinta el fondo rojo
					$color_celda = 'style="background-color: #FDCB7F; "';					
				}		
			
			}
			
			echo "<td $color_celda>$formato_horas</td>";
			
			
			// --> Clasificación y Turno del paciente, Jerson Trujillo.
			echo "
				<td align='center' style='font-size:8pt'>&nbsp;".$clasificAtenPac."&nbsp;</td>
				<td align='center'>";
				if($TurnoPaciente != '')
			echo "
					<b>".substr($TurnoPaciente, 4)."</b>";
			echo "
				</td>";

			// --> Genero medico
			//$generoMedico = (($row['Mtrgme'] == 'F') ? 'Femenino' : (($row['Mtrgme'] == 'M') ? 'Masculino' : '')); // Juan C Hdez Abril 1
			$generoMedico = (($row['Mtrgme'] == 'F') ? 'Femenino' : (($row['Mtrgme'] == 'M') ? 'Masculino' : ($row['Mtrgme'] == 'C') ? 'COVID19' : ''));
			
			if($generoMedico != '')
				echo "
				<td align='center' tooltip='si' style='cursor:help' title='<span style=\"font-weight:normal;font-size:9pt\" align=\"center\">Genero médico que el paciente<br>desea que lo atienda.</span>' style='font-size:8pt'>&nbsp;".$generoMedico."&nbsp;</td>
				";
			else
				echo "<td></td>";

			// --> Sala de espera
			echo "<td align='center'style='font-size:8pt'>".$arraySalasEspera[$salaEspera]."</td>";

			// --> Prioridad 2016-06-21
			echo "<td align='center'style='font-size:8pt'>".$codPrioridad."</td>";
			
           echo "<td align=center>".$wnombretriage."</td>";
		  echo "<td align=center>   ".$wfin."</td>";
		  echo "<td align=center>   ".$whin."</td>";
		  echo "<td align=center><b>".$whis." - ".$wing."</b></td>";
		  echo "<td align=left  ><b>".$wpac."</b></td>";

		  $wpaciente_con_ordenes = buscar_paciente_ordenes($conex, $wbasedato, $whis, $wing);
		  $ir_a_ordenes = ir_a_ordenes($wemp_pmla, $wcco);
		  $fondo_verde = "";

		  //Si el centro de costos tiene activo ordenes electronicas y el paciente tambien pondra el fondo verde en la accion de ir a la historia. Jonatan 23 Octubre 2014
		  if($ir_a_ordenes == 'on'){

			if($wpaciente_con_ordenes == 'on'){

				$fondo_verde = "style='background-color:#3CB648;'";

				}

			}

		  $irhce="on";  //Permite ingresar a la hce sin dar clic sobre el radio button de ir a hce
		  if ($wcur == "on") //                                                                                activarConsulta(his,    ing,    doc,       tid,     i, irhce, especialidad, wemp_pmla, especi_usuario, fecha_consulta, hora_consulta, medico_asociado
			 echo "<td align=center $fondo_verde><input type='radio' name='wirhce_atenact$i' id='wirhce_atenact$i' onclick='activarConsulta($whis, $wing, \"$wdpa\", \"$wtid\", $i, \"\",      \"\"   , \"$wemp_pmla\"     , \"\"          , \"\"          , \"\"         , \"$wmedico_asociado\")' checked></td>";
			else
			   echo "<td align=center $fondo_verde><input type='radio' name='wirhce_atenact$i' id='wirhce_atenact$i' onclick='activarConsulta($whis, $wing, \"$wdpa\", \"$wtid\", $i, \"$irhce\",      \"\"   , \"$wemp_pmla\"     , \"\"          , \"\"          , \"\"         , \"$wmedico_asociado\")'>";

		  echo "<td align=center>";
		  echo "<select id='conducta_atendidos$i' name='wconducta_atendidos$i' conducta_atendidos$i=$wcon onchange='colocarConducta($whis, $wing, $i, \"$irhce\", \"$wftc\", \"$whtc\", \"$wptr\", \"$wcon\", this, \"$walp\", \"tr_atendidos$i\")'>";

		  if (isset($wcon))                              //Si selecciono una opcion del dropdown
			 {
			  $q = " SELECT condes "
				 . "   FROM ".$whce."_000035 "
				 . "  WHERE concod = '".$wcon."'";
			  $res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row2 = mysql_fetch_array($res2);

			  echo "<option selected value='$wcon'>".$row2[0]."</option>";
			  }
			
			// --> 	Se edita el option vacio, por solicitud de calidad (lady) ya que al seleccionar el vacio el paciente vuelve a quedar 
			//		en la lista de pacientes en espera y se reinician los tiempos lo que implicaba que los indicadores de oportunidad
			//		se ampliaran. jerson trujillo 2019-12-16
			
			// echo "<option value=''>&nbsp</option>";

		  //============================================================================================================
		  //Aca coloco todas las conductas
		  //============================================================================================================

		  // Valido primero si el medico que esta atendiendo es de triage // Abril 17 de 2012
			  $q1 = " SELECT Medtri"
				 . "   FROM ".$wbasedato."_000048 "
				 . "  WHERE Medurg = 'on' "
				 . "	AND Meduma = '".$wusuario."'"
				 . "	AND Medtri = 'on'"
				 . "	AND Medest = 'on'";
			  $res1 = mysql_query($q1, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num1 = mysql_num_rows($res1);

		  //Muestro las conductas dependiendo del usuario, si es de triage solo muestro los que tenga contri='on' en la tabla HCE_000035, sino muestro todos. //Abril 17 de 2012
			if ($num1 > 0)
				{
				$q =  " SELECT concod, condes, conesp, concub "
					. "   FROM ".$whce."_000035 "
					. "  WHERE conest = 'on' "
					. "	  AND contri = 'on'"
				   ."ORDER BY conord ";

				}
			else
				{
				 $q =  " SELECT concod, condes, conesp, concub  "
					 . "   FROM ".$whce."_000035 "
					 . "  WHERE conest = 'on' "
					 . "    AND conesp in ('*','', ' ', 'NO APLICA')"
				  ."   ORDER BY conord ";

				}
		  $res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num2 = mysql_num_rows($res2);
		  for ($j=1;$j<=$num2;$j++)
			 {
			  $row1 = mysql_fetch_array($res2);
			  echo "<option value='".$row1[0]."-".$row1[2]."-".$row1[3]."'>".$row1[1]."</option>";
			 }
		  echo "</select></td>";

		  //============================================================================================================
		//Cubiculos
		//Se consulta el cubiculo actual para que sea seleccionado, y luego se listan los que estan disponibles, se puede cambiar de cubiculo al paciente
		//y se podra disponer del cubiculo que tenia asignado anteriormente.

			$q_cub_actual =    " SELECT Habcod, Habcpa  "
							 . "   FROM ".$wbasedato."_000020 "
							 . "  WHERE habcub = 'on'"
							 . "	AND habhis = '$whis' "
							 . "	AND habing = '$wing' "
							 . "	AND habest = 'on' ";
			$res_cub_actual = mysql_query($q_cub_actual, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub_actual." - ".mysql_error());
			$row_cub_actual = mysql_fetch_array($res_cub_actual);

			$cubiculo_actual = $row_cub_actual['Habcod'];
			$cubiculo_nombre = $row_cub_actual['Habcpa'];


			if($cubiculo_actual == ""){

				$q_cub_aux =  " SELECT concub "
							. "   FROM ".$whce."_000035 "
							. "  WHERE concod = '".$wcon."'";
				$res_cub_aux = mysql_query($q_cub_aux, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub_aux." - ".mysql_error());
				$row_cub_aux = mysql_fetch_array($res_cub_aux);

				if($row_cub_aux['concub'] != 'on'){

					$disabled = "disabled";
					$cubiculo_actual = "&nbsp;&nbsp;&nbsp;&nbsp;";
					$cubiculo_nombre = "&nbsp;&nbsp;&nbsp;&nbsp;";

				}

			}

		//======================================================================================================
		//Si el paciente tiene conducta, se deja activo el seleccionador de cubiculos, solo se mostraran en la lista los pacientes que tengan conducta asociada a cubiculo.
		// if($wcon == ""){

			// $disabled_cub = "disabled";
			// $disabled_sala = "disabled";

		// }else{

			// if($dato_cubiculoActual == "" and count($array_conductas_cub[$wcon]) ==  0 ){

				// $disabled_cub = "disabled";
				// $disabled_sala = "disabled";

			// }
		// }

		//Salas
		echo "<td>";
			echo "<select id='sala_atendidos$i' name='sala_atendidos$i' $disabled_cub onchange='filtrarCubiculo($whis, $wing, \"$i\", \"on\", \"$dato_cubiculoActual\", \"$irhce\" )'>";
			if($wsala_asignada == ""){
			echo "<option value='' selected>&nbsp</option>";
			}
			if(is_array($array_salas)){
				foreach($array_salas as $key => $row_sala){
					$sala_seleccionada = "";
					if($wsala_asignada == $row_sala['Arecod']){
						$sala_seleccionada = "selected";
					}
					echo "<option value='".$row_sala['Arecod']."' $sala_seleccionada>".$row_sala['Aredes']."</option>";
				}
			}

			echo "</select>";
		echo "</td>";

		//======================================================================================================
		$array_cubiculos_aux = array();
		//Cubiculos
		echo "<td id='dato_cubiculos_atendidos$i'>";
			 echo "<select id='cubiculo_atendidos$i' name='wconducta$i' $disabled_sala onchange='reasginarCubiculo($whis, $wing, $i, \"$irhce\", \"$dato_cubiculoActual\")'>";

			echo "<option value=''>$desc_cubiculoActual</option>";

			//Si la sala esta asignada al paciente entonces tomo ese dato y solo muestro en el select los que tengan esa sala asignada de acuerdo al key del array.

					$array_cubiculos_aux = @$array_cubiculos_fisicos[$wsala_asignada];
					
					if(count($array_cubiculos_aux) == 0){
						
						$array_cubiculos_aux = @$array_cubiculos_virtuales[$wsala_asignada];
					}
										
					//Verifica si hay cubiculos para listar en la zona.
					if(is_array($array_cubiculos_aux)){

						foreach ($array_cubiculos_aux as $key_cub => $row_cub){

							$seleccionar_cub = "";
							if($array_cubiculos_asignados[$whis."-".$wing]['Habcod'] == $row_cub['Habcod']){

								$seleccionar_cub = "selected";
							}

							 echo "<option value='".$row_cub['Habcod']."' $seleccionar_cub>".$row_cub['Habcpa']."</option>";

						}
					}

			echo "</select>";
		echo "</td>";
		  //======================================================================================================
		  //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
		  $wafin=clienteMagenta($wdpa,$wtid,$wtpa,$wcolorpac);
		  if ($wafin)
			 echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
			else
			  echo "<td>&nbsp</td>";
		  //======================================================================================================

          $wnombremedico = nombremedico($wmed); // Nombre del medico asociado al paciente.

		  echo "<td align=center style='font-size:8pt'>".strtoupper($wnombremedico)."</td>";
		  echo "</tr>";
		 }
		}
	  }
	echo "</table>";
   }
//=========================================================================================================================================================================================
//=========================================================================================================================================================================================


  $wfecha=date("Y-m-d");
  $whora = (string)date("H:i:s");

  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res);

  $wnominst=$row[0];

  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
  $q = " SELECT detapl, detval, empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' "
      ."    AND empcod = detemp ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  if ($num > 0 )
     {
	  for ($j=1;$j<=$num;$j++)
	     {
	      $row = mysql_fetch_array($res);

	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];

	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];

	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];

	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];

	      if (strtoupper($row[0]) == "HCE")
	         $whce=$row[1];

	      $winstitucion=$row[2];
         }
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
  if (!isset($consultaAjax))
    {
  encabezado("SALA DE ESPERA URGENCIAS",$wactualiz, "clinica");

  //FORMA ================================================================
  echo "<form name='sala' action='Sala_de_espera_por_Especialidad.php' method=post id='pacientes'>";

  //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
  $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
      ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
      ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
      ."    AND ".$wtabcco.".ccocod = '".$wcco."' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  $row = mysql_fetch_array($res);


  //$wcco=$row[0];
  $wnomcco=$row[1];

  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' id='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name='whce' id='whce' value='".$whce."'>";
  echo "<input type='HIDDEN' name='wusuario' id='wusuario' value='".$wusuario."'>";
  echo "<input type='HIDDEN' name='wcco' id='wcco' value='".$wcco."'>";

  //===============================================================================================================================================
  //Imprimo el nombre del Médico
  //===============================================================================================================================================
  $q = " SELECT medno1, medno2, medap1, medap2 "
       ."  FROM ".$wbasedato."_000048 "
	   ." WHERE meduma = '".$wusuario."'";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res);

  echo "<p class='tituloPagina' align=center>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."</p>";
  echo "<HR align=center></hr>";  //Linea horizontal
  //===============================================================================================================================================

	// --> Pintar seleccionador de consultorios, Jerson trujillo 2015-07-23
	// --> Obtener el maestro de puestos de trabajo (Consultorios)
	$sqlVentanillas	= "
	SELECT Puecod, Puenom, Pueusu
	  FROM ".$wbasedato."_000180
	 WHERE Puetco = 'on'
	   AND Pueest = 'on'
	";
	$resVentanillas 	= mysql_query($sqlVentanillas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVentanillas):</b><br>".mysql_error());
	$consultorioActUsu	= '';
	while($rowVentanillas = mysql_fetch_array($resVentanillas))
	{
		$arrayConsultorios[$rowVentanillas['Puecod']] = $rowVentanillas['Puenom'];

		if($rowVentanillas['Pueusu'] == $wusuario)
			$consultorioActUsu = $rowVentanillas['Puecod'];
	}
	$claseTdCon = "color:#000000;font-size:8pt;padding:1px;font-family:verdana;";
	echo "
	<table width='100%'>
		<tr>
			<td align='left'>
				<div align='center'>
					<span align='center' style='padding:5px;border-radius: 4px;border:2px solid #AFAFAF;width:200px;font-family: verdana;font-weight:bold;font-size: 15pt;'>
						Puesto de trabajo:&nbsp;&nbsp;</b>
						<select id='puestoTrabajo' type='text' style='cursor:pointer;border-radius: 4px;border:1px solid #AFAFAF;width:200px' consultorioActUsu='".$consultorioActUsu."' onChange='cambiarPuestoTrabajo(this)'>
							<option value='' usuario=''>Seleccione..</option>
						";
					foreach($arrayConsultorios as $codConsultorio => $nomConsultorio)
						echo "
							<option value='".$codConsultorio."' ".(($codConsultorio == $consultorioActUsu) ? "SELECTED='SELECTED'" : "" ).">".$nomConsultorio."</option>";
					echo "
						</select>
					</span>
				</div>
			</td>
			<td align='right'>
				<table style='border-radius: 4px;border:2px solid #AFAFAF;color:#000000;font-size:8pt;padding:1px;font-family:verdana;'>
					<tr><td style='background-color:#FFD1D1;color:#000000' class='encabezadoTabla' align='center' colspan='10'><b>Tiempos de espera</b></td></tr>
					<tr>
						<td class='fila2'><b>Triage I:</b> Atención inmediata</td>
						<td class='fila2'><b>Triage II:</b> 15-30 Minutos</td>
						<td class='fila2'><b>Triage III:</b> 1 Hora</td>
						<td class='fila2'><b>Triage IV:</b> 2 Horas</td>
						<td class='fila2'><b>Triage V:</b> 4 Horas</td></tr>
				</table>
			</td>
		</tr>
	</table>
	";
	echo "<HR align=center></hr>";
	// --> Div para pintar ventana modal para seleccionar el consultorio cuando se entra por primera vez
	echo "
	<div align='center' id='ventanaPedirConsultorio' style='display:none'>
		<span align='center' style='font-family: verdana;font-size: 12pt;'>
			Por favor seleccione el consultorio <br>
			en el que esta ubicado:<br><br>
			<select id='puestoTrabajo2' type='text' style='cursor:pointer;border-radius: 4px;border:1px solid #AFAFAF;width:200px' ventanillaActUsu='".$ventanillaActUsu."' onChange='cambiarPuestoTrabajo(this)'>
				<option value='' usuario=''>Seleccione..</option>
			";
		foreach($arrayConsultorios as $codConsultorio => $nomConsultorio)
			echo "
				<option value='".$codConsultorio."'>".$nomConsultorio."</option>";
		echo "
			</select>
		</span>
	</div>";

  //===============================================================================================================================================
  //C O N  V E N C I O N E S
  //===============================================================================================================================================
  // echo "<table border=1 cellspacing=0 align=right>";
  // echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
  // echo "<tr><td bgcolor='#99FFCC'><font size=1 color='"."000000"."'>&nbsp Menos de 15 minutos</font></td>";           //Verde
  // echo "<td bgcolor='#FFFF3F'><font size=1 color='"."000000"."'>&nbsp De 16 a 30 minutos</font></td>";            //Amarillo
  // echo "<td bgcolor='#FC514E'><font size=1 color='"."000000"."'>&nbsp Mas de 30 minutos</font></td></tr>";      //Rojo
  // echo "<tr><td bgcolor='#6694E3'><font size=1 color='"."000000"."'>En proceso de traslado</font></td>";      //Rojo
  // echo "<td bgcolor='#FFFFCC'><font size=1 color='"."000000"."'>En procesos de alta</font></td>";      //Rojo
  // echo "<td bgcolor='#3CB648'><font size=1 color='"."000000"."'>Ordenes Electrónicas</font></td></tr>";      //Rojo
  // echo "</table>";
						
  //===============================================================================================================================================


  echo "<center><table>";


  } //Cierra la validacion cuando se hace una consultaAjax

  $q = " SELECT empdes, empmsa "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res);

  $wnominst=$row[0];
  $wmeta_sist_altas=$row[1];  //Esta es la meta en tiempo promedio para las altas


  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA
  //===============================================================================================================================================
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  if(isset($consultaAjax))
    {
	switch($consultaAjax)
	  {
	    case 'activarcur':
		   echo ponerConsulta($whce, $whis, $wing, $wusuario, $irhce, $wespecialidad, $wemp_pmla, $wesp_usuario, $fecha_consulta, $hora_consulta, $wmedico_asociado);
		 break;
		case 'conducta':
		   echo ponerConducta($whce, $wbasedato, $whis, $wing, $wusuario, $wconducta, $wfecha_term_consul, $whora_term_consulta, $wtipo_cubiculo, $irhce, $proc_alta);
		 break;
        case 'niveltriage':
		   echo ponerNivelTriage($whce, $wbasedato, $whis, $wing, $wusuario, $wniveltriage, $wfecha, $whora, $fecha_term_consul, $hora_term_consul);
		 break;
        case 'valida':
            echo validaEstado($wemp_pmla,$wbasedato,$wbasedatohce,$whistoria, $wingreso);
            break;
		case 'cubiculo':
		   echo ponerCubiculo($whce, $wbasedato, $whis, $wing, $wusuario, $wconducta, $wfecha_term_consul, $whora_term_consulta, $wcubiculo);
		 break;
		case 'reasignar_cubiculo':
		   echo reasignarCubiculo($whce, $wbasedato, $whis, $wing, $wusuario, $wcubiculoActual, $wcubiculoNuevo);
		 break;
		 case 'filtrarCubiculo':
		   echo filtrarCubiculo($wemp_pmla, $wsala, $posicion, $whis, $wing, $atendidos, $cubiculoActual);
		 break;

		// --> 	Actualiza el puesto de trabajo asociado a un usuario
		// 		Jerson trujillo, 2015-07-15.
		case 'cambiarPuestoTrabajo':
		{
			$wbasedatoMov 	= $wbasedato;
			$respuesta 		= array("Error" => FALSE, "Usuario" => "");

			// --> Validar que el puesto de trabajo este disponible
			$sqlValPuesTra = "
			SELECT Descripcion
			  FROM ".$wbasedatoMov."_000180, usuarios
			 WHERE Puecod = '".$puestoTrabajo."'
			   AND Pueusu != ''
			   AND Pueusu = Codigo
			";
			$resValPuesTra = mysql_query($sqlValPuesTra, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValPuesTra):</b><br>".mysql_error());
			if($rowValPuesTra = mysql_fetch_array($resValPuesTra))
			{
				$respuesta["Error"] 	= TRUE;
				$respuesta["Usuario"] 	= $rowValPuesTra['Descripcion'];
			}
			else
			{
				// --> Quitar cualquier puesto de trabajo asociado al usuario
				$sqlUpdatePues = "
				UPDATE ".$wbasedatoMov."_000180
				   SET Pueusu = ''
				 WHERE Pueusu = '".$wusuario."'
				";
				mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());

				if($puestoTrabajo != '')
				{
					// --> Asignar el nuevo puesto de trabajo
					$sqlUpdatePues = "
					UPDATE ".$wbasedatoMov."_000180
					   SET Pueusu = '".$wusuario."'
					 WHERE Puecod = '".$puestoTrabajo."'
					";
					mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());
				}
			}

			echo json_encode($respuesta);
			return;
			break;
		}
		// --> 	Libera un consultorio
		// 		Jerson trujillo, 2015-07-15.
		case 'liberarPuestoTrabajo':
		{
			$wbasedatoMov 	= $wbasedato;
			// --> Quitar cualquier puesto de trabajo asociado al usuario
			$sqlUpdatePues = "
			UPDATE ".$wbasedatoMov."_000180
			   SET Pueusu = ''
			 WHERE Pueusu = '".$wusuario."'
			";
			mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());

			// --> Asignar el nuevo puesto de trabajo
			$sqlUpdatePues = "
			UPDATE ".$wbasedatoMov."_000180
			   SET Pueusu = '".$wusuario."'
			 WHERE Puecod = '".$puestoTrabajo."'
			";
			mysql_query($sqlUpdatePues, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdatePues):</b><br>".mysql_error());

			return;
			break;
		}
		// --> 	Llamar al paciente que esta en la sala de espera, a la atencion en consulta.
		// 		Jerson trujillo, 2015-07-02
		case 'llamarPacienteAtencion':
		{
			global $wbasedato;
			global $conex;
			global $hay_unix;

			$wbasedatoMov 	= $wbasedato;
			$respuesta		= array('Error' => FALSE, 'Mensaje' => '');

			// --> Validar que el paciente no este en consulta
			$sqlConsul = "
			SELECT Descripcion
			  FROM ".$whce."_000022, usuarios
			 WHERE Mtrhis = '".$historia."'
			   AND Mtring = '".$ingreso."'
			   AND Mtrcur = 'on'
			   AND Mtrmed = Codigo
			";
			$resConsul = mysql_query($sqlConsul, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConsul):</b><br>".mysql_error());
			if($rowConsul = mysql_fetch_array($resConsul))
			{
				$respuesta['Error'] 	= TRUE;
				$respuesta['Mensaje'] 	= "El paciente ya esta en consulta con ".$rowConsul['Descripcion'];
			}
			else
			{
				// --> 	Validar que el paciente no haya sido llamado por otro usuario a consulta
				//		o llamado nuevamente a triage o que este en consulta triage
				$sqlValLla = "
				SELECT Atullc, Atuulc, Atuart, Atuurt, Atuetr
				  FROM ".$wbasedatoMov."_000178
				 WHERE Atutur = '".$turno."'
				   AND (Atullc = 'on' OR Atuart = 'on' OR Atuetr = 'on')				   
				";
				$resValLla = mysql_query($sqlValLla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlValLla):</b><br>".mysql_error());
				if($rowValLla = mysql_fetch_array($resValLla))
				{
					$respuesta['Error'] 	= TRUE;
					
					if($rowValLla['Atullc'] == 'on')
					{
						$respuesta['Mensaje'] 	= "El paciente está siendo llamado a consulta por ";
						$usuario 				=  $rowValLla['Atuulc'];
					}
					elseif($rowValLla['Atuart'] == 'on')
						{
							$respuesta['Mensaje'] 	= "El paciente está siendo llamado a reclasificación triage por ";
							$usuario 				=  $rowValLla['Atuurt'];
						}elseif($rowValLla['Atuetr'] == 'on')
							{
								$respuesta['Mensaje'] 	= "El paciente está en consulta de reclasificación de triage con ";
								$usuario 				=  $rowValLla['Atuurt'];
							}
					
					$sqlNomUsu = "
					SELECT Descripcion
					  FROM usuarios
					 WHERE Codigo = '".$usuario."'
					";
					$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomUsu):</b><br>".mysql_error());
					if($rowNomUsu = mysql_fetch_array($resNomUsu))
						$nomUsuario = $rowNomUsu['Descripcion'];
					else
						$nomUsuario = '';
					
					$respuesta['Mensaje'].= $nomUsuario;
					$respuesta['Mensaje'] = utf8_encode($respuesta['Mensaje']);
				}
				else
				{
					// --> realizar el llamado
					$sqlLlamar = "
					UPDATE ".$wbasedatoMov."_000178
					   SET Atullc = 'on',
						   Atuflc = '".date('Y-m-d')."',
						   Atuhlc = '".date("H:i:s")."',
						   Atuulc = '".$wusuario."',
						   Atucon = '".$consultorio."'
					 WHERE Atutur = '".$turno."'
					";
					mysql_query($sqlLlamar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLlamar):</b><br>".mysql_error());

					// --> Registrar en el log el llamado
					$sqlRegLLamado = "
					INSERT INTO ".$wbasedatoMov."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,				Logusu,			Seguridad,				id)
												   VALUES('".$wbasedatoMov."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'llamadoConsulta',	'".$wusuario."', 'C-".$wbasedatoMov."',	NULL)
					";
					mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());
				}
			}

			echo json_encode($respuesta);
			return;
		}
		// --> 	Cancelar el llamado a consulta del paciente que esta en la sala de espera.
		// 		Jerson trujillo, 2015-07-02
		case 'cancelarLlamarPacienteConsulta':
		{
			$wbasedatoMov = $wbasedato;

			// --> realizar la cancelacion
			$sqlCancelar = "
			UPDATE ".$wbasedatoMov."_000178
			   SET Atullc = 'off'
			 WHERE Atutur = '".$turno."'
			";
			mysql_query($sqlCancelar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCancelar):</b><br>".mysql_error());

			// --> Registrar en el log la cancelacion del llamado
			$sqlRegLLamado = "
			INSERT INTO ".$wbasedatoMov."_000179 (Medico,				Fecha_data,				Hora_data,				Logtur,			Logacc,						Logusu,			Seguridad,				id)
										   VALUES('".$wbasedatoMov."',	'".date('Y-m-d')."',	'".date("H:i:s")."',	'".$turno."', 	'cancelaLlamadoConsulta',	'".$wusuario."', 'C-".$wbasedatoMov."',	NULL)
			";
			mysql_query($sqlRegLLamado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRegLLamado):</b><br>".mysql_error());

			//echo json_encode($data);
			return;
		}
        default :
            break;
      }
    }

  $i = 1;

  if (!isset($consultaAjax))
    {
  //mostrarPacientesPropios($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, &$i);
  mostrarPacientesPorEspecialidad($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i);
  mostrarPacientesComunes($wbasedato, $whce, $wemp_pmla, $wcco, $wusuario, $i, $zona);

  echo "</form>";

  if (isset($wsup) and $wsup=="on")  //Es superusuario
     echo "<meta http-equiv='refresh' content='300;url=Sala_de_espera_por_Especialidad.php?wemp_pmla=".$wemp_pmla."&wuser=".$wusuario."&user=".$user."&wcco=".$wcco."&zona=".$zona."'>";
  else
     echo "<meta http-equiv='refresh' content='30;url=Sala_de_espera_por_Especialidad.php?wemp_pmla=".$wemp_pmla."&wuser=".$wusuario."&user=".$user."&wcco=".$wcco."&zona=".$zona."'>";


  echo "<table>";
  echo "<tr><td align=center height=21>&nbsp;</td></tr>";
  echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
} //Cierra la validacion cuando se hace una consultaAjax
include_once("free.php");
}
?>