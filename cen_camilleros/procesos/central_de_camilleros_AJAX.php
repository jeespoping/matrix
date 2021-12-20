<?php
include_once("conex.php");


    /**
     * Lógica de los llamados AJAX del todo el programa
     */
    if(isset($accion))
    {
        

        

        include_once("root/comun.php");
        // $wbasedato       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'root');
		$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
        $wbasedato = "root";

        $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'');
        $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

        switch($accion)
        {
            /*
             * Verifica si el paciente indicado en la solicitud mostrada ya tiene un proceso de traslado (Entrega)
             * En caso de ya tener un proceso de entrega, no se permitirá la anulación de la solicitud mostrada.
            */
            case 'verificar_entrega_paciente':

                /*
                 * Verifica si la solicitud enviada tiene la historia nula o vacía para permitir su anulación inmediata.
                 */
                if($historia == "" || $historia == '' || $historia == null)
                {
                    $data["resultado"] = 0;
                }
                else
                {
                    $sql_verif = "SELECT ".$wmovhos."_000017.Fecha_data, ".$wmovhos."_000017.Hora_data, ubihis, ubiing, ubisac, ubisan, eyrtip"
                        . " FROM ".$wmovhos."_000018, ".$wmovhos."_000017"
                            . " WHERE ubihis = '" . $historia . "'"
                                        . " AND ubihis = Eyrhis"
                                        . " AND Ubiing = Eyring"
                                        . " AND Ubihac = Eyrhde"
                                        . " AND Ubihan = Eyrhor"
                                        . " AND Ubisac = Eyrsde"
                                        . " AND ubialp = 'off'"
                                        . " AND ubiald = 'off'"
                                        . " AND ubiptr = 'on'"
                                        . " -- AND eyrtip = 'Entrega'"
                                        . " AND Eyrest = 'on'"
                                . " ORDER BY ".$wmovhos."_000018.Fecha_data DESC, ".$wmovhos."_000018.Hora_data DESC, "
                                                    . " ".$wmovhos."_000017.Fecha_data DESC, ".$wmovhos."_000017.Hora_data DESC;";

                    $res = mysql_query($sql_verif, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql_verif . " - " . mysql_error());

                    $data["resultado"] = 0;
                    if($res)
                    {
                        $num = mysql_num_rows($res);
                        $data["resultado"] = $num;
                    }
                    else
                    {
                        $data["error"] = 1;
                        $data["Query_Err"] = "Error: " . mysql_errno() . " - en el query  " . $sql_verif . " - " . mysql_error();
                        $data["mensaje"] = "No se pudo verificar la entrega del paciente.";
                    }
                }

            break;

            default :
                $data['mensaje'] = $no_exec_sub;
                $data['error'] = 1;
            break;
        }
        echo json_encode($data);
        return;
    }

@session_start();
if (!isset($consultaAjax))
{

?>
<head>
  <title>CENTRAL DE CAMILLEROS</title>
</head>
<body>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
 <!-- PNotify -->
<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.brighttheme.css" rel="stylesheet">

<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link rel="stylesheet" href="../../../include/root/jatt.css" type="text/css">
<script src="../../../include/root/jquery.jatt.js" type="text/javascript"></script>

<script type="text/javascript">


    $(document).ready(function() {

        reload = setTimeout("enter()",document.getElementById('wtiempo_refresh').value*1000);
	
		$('#buscarTabla').quicksearch('#tbInformacion .find');
	

    });

	function doTimer(s)
	{
	if (!timer_is_on)
	  {
	  timer_is_on=1;
	  reload = setTimeout("enter()",s*1000);
	  timedCount(s);

	  }
	}

	function reactivar(wtiempo_refresh)
	 {
	  reload = setTimeout("enter()",wtiempo_refresh*1000);
	 }

	function detener()
	{

	clearTimeout(t);
	clearTimeout(reload);
	timer_is_on=0;
	}

	function enter()
	 {
	  document.forms.central.submit();
	 }

	function cerrarVentana()
	 {
      window.close();
     }
	 
	function filtrarOrigen()
	{
      alert ("hola");
    }


    function confirmarhab (wid, fecha, hora, codigo, historia, data_json)
    {


         //Confirma que desea actualizarle la habitacion al paciente por vacio.
            var answer =  confirm(data_json.mensaje);

            //Se ejecuta de nuevo el script con el parametro confirmacion=ok para que al validar si la actualice a vacio.
            if (answer)
                {

                    $.post("central_de_camilleros_AJAX.php",  //whabitacion$wid
                        {

                            consultaAjax:   	'asignarhabitacion',
                            wemp_pmla:      	$("#wemp_pmla").val(),
                            wfecha:             fecha,
                            whora:              hora,
                            wid:                wid,
                            wcodigo:            codigo,
                            whab:               $("#whabitacion"+wid+" option:selected").text(),
                            whis:               historia,
                            wconfirmar:         'ok'


                        },function(data_json) {

                                jAlert(data_json.mensaje, "Alerta");
                                cerraremergente();

                                    },
                                    "json"

                                );



                        }
                else //Si cancela entonces simplemente se actualiza la pagina.
                {
                    cerraremergente();
                }


    }

    window.onload=function(){
    var pos=window.name || 0;
    window.scrollTo(0,pos);
    }
    window.onunload=function(){
    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
    }

    function asignarhabitacion(wid, fecha, hora, codigo, historia, este, cco_origen, motivo, wusuario, esAyuda, wing, traslado_desde_ayuda)
    {
        var hab = $("#whabitacion"+wid+" option:selected").val();
        
		if(hab != ''){
		
		var whabitaciones = JSON.parse($("#whabitaciones").val());		
		var wser_destino = whabitaciones[hab]['Habcco'];
		
		}
	
		var ccocirugia = $("#ccocirugia").val();
        if (historia == '')
        {
            $.blockUI({ message:    '<img src="../../images/medical/ajax-loader.gif" >',
                css:    {
                    width:  'auto',
                    height: 'auto'
                }
            });
            $.post("central_de_camilleros_AJAX.php",  //whabitacion$wid
            {
                consultaAjax:       'asignarhabitacion',
                wemp_pmla:          $("#wemp_pmla").val(),
                wfecha:             fecha,
                whora:              hora,
                wid:                wid,
                wcodigo:            codigo,
                whab:               hab,
                whis:               historia,
                wconfirmar:         '',
                wsolohab:           'off'
            }
            ,function(data_json) {
                if(data_json.notificacion == 1)
                {
                    //
                }
                else
                {
                    $.blockUI({ message: data_json.formulario,
                        css: {  left:   '30%',
                            top:    '10%',
                            width:  '40%',
                            height: 'auto'
                        }
                    });
                }
            },
            "json"
            );
        }
        else
        {		
			
			var habitacion_asignada = $('#whabitacion'+wid+' option[value^="selected_"]').text();
			//Se verifica que al paciente no se le haya hecho la entrega para permitir el cambio de habitación.
			$.post("central_de_camilleros_AJAX.php",
			{
				consultaAjax            : '',
				wemp_pmla               : $('#wemp_pmla').val(),
				accion                  : 'verificar_entrega_paciente',
				historia                : historia
				}, function(data) {
				// --> data.error indica si hay un error  en el llamado de la funcion
				if(data.error == 1)
				{
					jAlert("Error: " + data.mensaje, "Alerta");
				}
				else
				{
					// En caso de que al paciente ya se le haya entregado, se deshabilitará el
					// select y se le mostrará la habitación que tiene asignada para su recibo.
					if(data.resultado > 0 && cco_origen != ccocirugia) { //Esto le permite a los pacientes de cirugia tener asignacoin de cama.
						$("#whabitacion"+wid).attr('disabled','disabled');
						$("#whabitacion"+wid).val(habitacion_asignada);
						$('input[id_check="chkSol_' + wid + '"]').attr('checked',false);
						$('input[id_check="chkSol_' + wid + '"]').attr('disabled','disabled');
						jAlert("El paciente ya se encuentra entregado.\nNo se puede cambiar de habitación hasta no ser recibido y realizar una nueva solicitud de traslado.", "Alerta");
					}
					else
					{
						
						//Aqui se hace el proceso de traslado al piso cuando un paciente es ambulatorio y la hora es mayor a las 18:00.
						if(esAyuda == '1' && hab != '' && traslado_desde_ayuda == '1'){
							
							jConfirm('¿El paciente con historia '+historia+' será entregado a la habitación '+hab+', ¿Esta seguro?', 'Mover paciente', function(r) {

							if(r){	
							
									$.post("central_de_camilleros_AJAX.php",
									{
										consultaAjax:       'grabarhiseing',
										wemp_pmla:          $("#wemp_pmla").val(),
										wfecha:             fecha,
										whora:              hora,
										wid:                wid,
										wcodigo:            codigo,
										whab:               hab,
										whis:               historia,
										wconfirmar:         hab,
										wsolohab:           'on',
										wusuario:			wusuario
									}
									,function(data_json) {
										if(data_json.notificacion == 1)
										{
											//
										}
										else
										{
											if( isJson(data_json) ){
												data_json = eval( '('+data_json+')' );
												if(data_json.error == 1 ){
													jAlert( data_json.mensaje, "Alerta");
												}else{
												
												//Aqui se hace el proceso de traslado al piso cuando un paciente es ambulatorio y la hora es mayor a las 18:00.
												$.post("../../movhos/procesos/Ent_y_Rec_Pac_gestion.php",
													{
														consultaAjax 	: 	'',
														operacion 		: 	'moverPacHosp',
														wemp_pmla		: 	$("#wemp_pmla").val(),
														wbasedato		: 	$("#wbasedato").val(),
														whis			: 	historia,
														wing			: 	wing,
														wcco			: 	cco_origen,
														wccodes1		:	wser_destino,
														whabdes1		:	hab,
														went_rec		:	'Ent',
														wgrabar			:	'ok',
														wid				:   wid
														
													}
													,function(data_json) {
														if(data_json.notificacion == 1)
														{
															//
														}
														else
														{
															if( isJson(data_json) ){
																data_json = eval( '('+data_json+')' );
																if(data_json.error == 1 ){
																	jAlert( data_json.mensaje, "Alerta");
																}
																enter();
																}else{
																//enter();
															}
														}
													});
												
												//----												
													//enter();
												}
											}
										}
									});
								}else{
									enter();
								}
							});
							
						}else{
							
							//Pacientes que no son de ayuda							
							$.post("central_de_camilleros_AJAX.php",
									{
										consultaAjax:       'grabarhiseing',
										wemp_pmla:          $("#wemp_pmla").val(),
										wfecha:             fecha,
										whora:              hora,
										wid:                wid,
										wcodigo:            codigo,
										whab:               hab,
										whis:               historia,
										wconfirmar:         hab,
										wsolohab:           'on',
										wusuario:			wusuario
									}
									,function(data_json) {
										if(data_json.notificacion == 1)
										{
											//
										}
										else
										{
											if( isJson(data_json) ){
												data_json = eval( '('+data_json+')' );
												if(data_json.error == 1 ){
													jAlert( data_json.mensaje, "Alerta");
												}
												enter();
												}else{
												enter();
											}
										}
									});
							
							
						}
					}
				}
			}, 'json');
		
        }
    }


	function isJson(value) {
		try {
			eval('(' + value + ')');
			return true;
		} catch (ex) {
			return false;
		}
	}

     function grabarhiseing(basedato, fecha, hora, wid, hab, cco)
        {

        var whis = $("#historiamodal").val();

        //Validaciones para que la historia y el ingreso no sean vacios, ademas que sean numeros.
        if(whis == '')
            {
                jAlert('Debe registrar la historia del paciente', 'Alerta');
                return;
            }

         if(isNaN(whis))
            {
                jAlert('La historia debe ser un número', 'Alerta');
                return;
            }

		$.post("central_de_camilleros_AJAX.php",
				{

                    consultaAjax:   	'grabarhiseing',
					wemp_pmla:      	$("#wemp_pmla").val(),
                    basedato:           basedato,
					wfecha:             fecha,
                    whora:              hora,
                    wid:                wid,
                    whab:               hab,
                    wcco:               cco,
                    whis:               $("#historiamodal").val()

				}
                ,function(data_json) {

                    if (data_json.error == 1)
                    {
                        jAlert(data_json.mensaje, "Alerta");
                        $("#historiamodal").val('');
                        return;
                    }
                    else
                    {
                        jAlert(data_json.mensaje, "Alerta");
                        cerraremergente();
                    }

            },
            "json"
        );
    }


    function cerraremergente()
    {

       $.unblockUI();
       $('#central').submit();
    }

	function colocarCamillero(wid, wusuario, campo, historia, i, esUrgencias)
	 {
	  
	  var opcionSeleccionada = campo.options[ campo.selectedIndex ].text;
	 
	 //Si es urgencias verifica si puede asignarle cama al paciente.
	 if(esUrgencias == '1'){
	  if($("#wtramitesok_"+i).is(':checked') ) {  
            var marcado = 'on';
        } else {  
            var marcado = 'off';
        } 
		
	 }
	  var parametros = "consultaAjax=camillero&wemp_pmla="+document.getElementById("wemp_pmla").value+"&wid="+wid+"&wcentral="+document.getElementById("wcentral").value+"&wusuario="+wusuario+"&wcamillero="+opcionSeleccionada+"&whis="+historia+"&tramitesok="+marcado;

      try
		  {

                try {
			$.blockUI({ message: $('#msjEspere') });
		} catch(e){ }
		    var ajax = nuevoAjax();
			ajax.open("POST", "central_de_camilleros_AJAX.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

                    ajax.onreadystatechange=function()
                    {
                            var contenedor = document.getElementById('select'+wid);

                            if (ajax.readyState==4)
                            {
                                    contenedor.innerHTML=ajax.responseText;

                            }
                            try {
                                    $.unblockUI();
                                } catch(e){ }
                    }


                  }catch(e){	}
		}

	function marcartramitesok(wid, i, esUrgencias )
	  {
		
		if($("#wtramitesok_"+i).is(':checked')) {  
            var marcado = 'on';
			$("#marcadook_"+wid).css( "background-color","3CB648");
        } else {  
            var marcado = 'off';			
			$("#marcadook_"+wid).css( "background-color","CCCCFF");
        }  
		
		if(marcado == 'off' && $("#whabitacion"+wid).val() != '' ){
				
				$("#wtramitesok_"+i).attr('checked','checked');
				$("#marcadook_"+wid).css( "background-color","3CB648");
				jAlert("La habitación destino debe estar vacia para poder desmarcar este cajón.");				
				return;
		}
		
		
		$.post("central_de_camilleros_AJAX.php",
				{

                    consultaAjax:   	'marcartramitesok',
					wemp_pmla:      	$("#wemp_pmla").val(),
                    wid:				wid,
					marcado:			marcado
                    

				}
                ,function(data_json) {

                    if (data_json.error == 1)
                    {
                        jAlert(data_json.mensaje, "Alerta");                       
                        return;
                    }
                    else
                    {
						if(esUrgencias == 1){
							if(marcado == 'on'){
								$("#whabitacion"+wid).removeAttr("disabled");
							}else{
								 $("#whabitacion"+wid).attr( "disabled", "disabled" );
							}
						}
                       
                    }

            },
            "json"
        );
	  }
	  
	  function marcarLlegada(wid, fecha, hora, i )
	  {
		var parametros = "consultaAjax=llegada&wemp_pmla="+document.getElementById("wemp_pmla").value+"&wcentral="+document.getElementById("wcentral").value+"&wid="+wid+"&ihora="+hora+"&ifecha="+fecha;
		try
		  {
			var campo=document.getElementById('wcamillero['+i+']');

			if (campo.options[ campo.selectedIndex ].text != "")
			   {
				var ajax = nuevoAjax();
				ajax.open("POST", "central_de_camilleros_AJAX.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				//	 	jAlert(ajax.responseText, "Alerta");
			   }
			  else
				{
				 jAlert ("No puede marcar LLEGADA porque no ha seleccionado el dato anterior", "Alerta");
				 document.getElementById('wllegada['+i+']').checked=false;
				}

		  }catch(e){ jAlert(e, "Alerta"); }
	  }

	function marcarCumplimiento(wid, fecha, hora, i )
	  {

        var habitacion = $("#whabitacion"+wid+" option:selected").text();
        var central =      	$("#wcentral").val();

        if(habitacion == '' && central == 'CAMAS')
            {
                jAlert('Debe seleccionar una habitacion y asignar historia, en caso de no tenerla.', 'Alerta');
                document.getElementById('wcumplimiento['+i+']').checked=false;
                return;
            }

		var parametros = "consultaAjax=cumplimiento&wemp_pmla="+document.getElementById("wemp_pmla").value+"&wcentral="+document.getElementById("wcentral").value+"&wid="+wid+"&ihora="+hora+"&ifecha="+fecha;
		try
		  {
			var campo=document.getElementById('wllegada['+i+']');
			if (campo.checked==true)
			  {
			   var ajax = nuevoAjax();
			   ajax.open("POST", "central_de_camilleros_AJAX.php",false);
			   ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			   ajax.send(parametros);
			   //	 	jAlert(ajax.responseText, "Alerta");

			   var fila = document.getElementById('wcumplimiento['+i+']').parentNode.parentNode;	//obtengo la fila
			   fila.style.display = 'none';
			  }
			 else
			   {
			    jAlert ("No puede marcar CUMPLIMIENTO porque no ha marcado la LLEGADA", "Alerta");
			    document.getElementById('wcumplimiento['+i+']').checked=false;
			   }

		  }catch(e){ jAlert(e, "Alerta"); }
	  }

    function marcarAnular(wid, wanu , i, whis)
    {
        $.post("central_de_camilleros_AJAX.php",
        {
            consultaAjax            : '',
            wemp_pmla               : $('#wemp_pmla').val(),
            accion                  : 'verificar_entrega_paciente',
            historia                : whis
        }, function(data) {
            // --> data.error indica si hay un error  en el llamado de la funcion
            if(data.error == 1)
            {
                jAlert("Error: " + data.mensaje, "Alerta");
            }
            else
            {
                if(data.resultado > 0) {
                    $("#whabitacion"+wid).attr('disabled','disabled');
                    $('input[id_check="chkSol_' + wid + '"]').attr('checked',false);
                    $('input[id_check="chkSol_' + wid + '"]').attr('disabled','disabled');
                    jAlert("El paciente ya se encuentra entregado.No se puede anular la solicitud.\nPrimero debe anular la entrega del paciente para poder anular la solicitud.\nPara anular la entrega debe ir a la opción MODIFICAR TRASLADOS - ANULACION ENTREGA PACIENTES ubicada en MOVIMIENTO HOSPITALARIO.", "Alerta");
                }
                else
                {
                   var pregunta = confirm("¿Seguro que desea anular esta solicitud?")
                   if (pregunta){

                    var parametros = "consultaAjax=anular&wemp_pmla="+document.getElementById("wemp_pmla").value+"&wcentral="+document.getElementById("wcentral").value+"&wid="+wid+"&wanu="+wanu;
                    try
                      {
                       var campo=document.getElementById('wllegada['+i+']');
                       if (campo.checked==false)
                          {
                           var ajax = nuevoAjax();
                           ajax.open("POST", "central_de_camilleros_AJAX.php",false);
                           ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                           ajax.send(parametros);
                           //       jAlert(ajax.responseText, "Alerta");
                           data_json = ajax.responseText;
                            if( isJson(data_json) ){
                                data_json = eval( '('+data_json+')' );
                                if(data_json.error == 1 ){
                                    jAlert( data_json.mensaje, "Alerta");
                                }
                                enter();
                            }else{
                                    enter();
                            }

                           var fila = document.getElementById('wanulada['+i+']').parentNode.parentNode; //obtengo la fila
                           fila.style.display = 'none';                                                 //oculto la fila
                           enter();
                          }
                         else
                           {
                            jAlert ("No puede ANULAR porque ya marco LLEGADA", "Alerta");
                            document.getElementById('wanulada['+i+']').checked=false;
                           }
                      }catch(e){ jAlert(e, "Alerta"); }
                    }
                    else{
                        document.getElementById('wanulada['+i+']').checked=false;
                    }
                }
            }
        }, 'json');
	  }

    function grabarObservaciononchange(wid, campo)
	  {

        var texto = escape(campo.value);
		var parametros = "consultaAjax=observacion&wemp_pmla="+document.getElementById("wemp_pmla").value+"&wcentral="+document.getElementById("wcentral").value+"&wid="+wid+"&wtexto="+texto;
		try
		  {
		    var ajax = nuevoAjax();
			ajax.open("POST", "central_de_camilleros_AJAX.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);
			//		jAlert(ajax.responseText, "Alerta");
			}catch(e){ jAlert(e, "Alerta"); }
	  }

	function grabarObservacion(wid, i, texto)
	  {

        var texto = escape(document.getElementById("wobscc["+i+"]").value);
		var parametros = "consultaAjax=observacion&wemp_pmla="+document.getElementById("wemp_pmla").value+"&wcentral="+document.getElementById("wcentral").value+"&wid="+wid+"&wtexto="+texto;
		try
		  {
		    var ajax = nuevoAjax();
			ajax.open("POST", "central_de_camilleros_AJAX.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);
			//		jAlert(ajax.responseText, "Alerta");
			}catch(e){ jAlert(e, "Alerta"); }
	  }

      $(function(){
			$.jatt();
			$(".tramiteok").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
        });

</script>
<?php
}

function traer_pacientes_remision_urgencias(){
	
	global $wcencam;
	global $conex;
	global $wemp_pmla;
	
	$hce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	
	$q_rem =  " SELECT Concod "
			. "   FROM ".$hce."_000035 "
			. "  WHERE conrem = 'on'";
	$res_rem = mysql_query($q_rem, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_rem." - ".mysql_error());
	$row_rem = mysql_fetch_array($res_rem);
	$cond_remision = $row_rem['Concod'];
	
	$q = " SELECT CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, pactid, pacced, ingnre, ubihis, ubiing, mtrmed, Mtrftc, Mtrhtc
		   FROM root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016, ".$hce."_000022, ".$wmovhos."_000011
		  WHERE ubihis  = Inghis
		    AND ubiing  = Inging
		    AND ubihis  = orihis 
		    AND ubiing  = oriing 
		    AND oriori  = '".$wemp_pmla."'
		    AND oriced  = pacced 
		    AND oritid  = pactid 
		    AND Mtrcon = '".$cond_remision."'
		    AND Mtrhis = Ubihis
		    AND Mtring = Ubiing 
			AND ubisac = Ccocod
			AND ccourg = 'on'
			AND Ubiald = 'off'
		  GROUP BY 1, 2, 3, 4, 5 
		  ORDER BY CONCAT(Mtrftc,' ',Mtrhtc)";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if($num > 0){
		echo "<center>";
		echo "<table>";
		echo "<tr class='encabezadoTabla'>";
		echo "<td colspan=7 align=center>Pacientes en Remisión</td>";
		echo "</tr>";
		echo "<tr class='encabezadoTabla' align=center>";
		echo "<td><font size='1'>Fecha de terminación <br>consulta</font></td><td><font size='1'>Hora terminación <br>consulta</font></td><td><font size='1'>Historia</font></td><td><font size='1'>Medico tratante</font></td><td><font size='1'>Nombre Paciente</font></td><td><font size='1'>Responsable</font></td><td><font size='1'>Zona / Ubicación</font></td>";
		
		while($row = mysql_fetch_array($res)){
			
			$wubicacion= '';
			$wmednom = '';
			
			//Buscar el medico tratante
			$q =   " SELECT medno1, medno2, medap1, medap2 "
				 . "   FROM ".$wmovhos."_000048 "
				 . "  WHERE meduma = '".$row['mtrmed']."'";
			$res2 = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num1 = mysql_num_rows($res2);
			
			if ($num1 > 0)
				 {
				 $row1 = mysql_fetch_array($res2);
				 $wmednom = $row1[0]." ".$row1[1]." ".$row1[2]." ".$row1[3];
				 }
				 
			//Buscar la ubicacion del paciente.
			$q_hab =   " SELECT Habcpa, Habzon "
				     . "   FROM ".$wmovhos."_000020 "
				     . "  WHERE habhis = '".$row['ubihis']."'"
				     . "    AND habing = '".$row['ubiing']."'";
			$res_hab = mysql_query($q_hab, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_hab." - ".mysql_error());
			$num_hab = mysql_num_rows($res_hab);
			
			if ($num_hab > 0)
				 {
				 $row_hab = mysql_fetch_array($res_hab);
				 
				$q_zona =   " SELECT Aredes "
						  . "   FROM ".$wmovhos."_000169 "
						  . "  WHERE Arecod = '".$row_hab['Habzon']."'";
				$res_zona = mysql_query($q_zona, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_zona." - ".mysql_error());
				$row_zona = mysql_fetch_array($res_zona); 
				
				 if($row_zona['Aredes'] != ''){
					 
					 $zona = $row_zona['Aredes']." / ";
				 } 
				 
				 $wubicacion = $zona.$row_hab['Habcpa'];
				
				 }
			
			$class3 = "class='fila".((($i+1)%2)+1)."'";
			echo "<tr $class3>";
			echo "<td>".$row['Mtrftc']."</td><td>".$row['Mtrhtc']."</td><td>".$row['ubihis']."-".$row['ubiing']."</td><td>".$wmednom."</td><td>".$row['Nombre']."</td><td>".$row['ingnre']."</td><td>".$wubicacion."</td>";
			echo "</tr>";
			$i++;
			
		}
		
		echo "</tr>";
		echo "</table>";		
		echo "</center>";
	}
	
}


function usuariotramiteok($wusuario){
	
	global $wcencam;
	global $conex;	
	
	$existe = false;
	
	$q = "SELECT *
		    FROM root_000051
		   WHERE Detapl = 'UsuariosTramitesOk'
		     AND Detval LIKE '%".$wusuario."%'; ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if($num > 0){
		
		$existe = true;
		
	}
	
	return $existe;
}

//Funcion que marca el paciente con tramites de autorizacion de traslado revisados
function marcartramitesok($wemp_pmla, $wid, $marcado){
	
	global $wcencam;
	global $conex;	
	global $wusuario;
	
	$wfecha = date('Y-m-d');
	$whora = date("H:i:s");
	
	 $datamensaje = array('mensaje'=>'', 'error'=>0); 
	 
	if($marcado == 'on'){
	
		$q =  " UPDATE ".$wcencam."_000003 "
			. "    SET Tramiteok = 'on', Usutramitepok = '".$wusuario."', Fechatramiteok = '".$wfecha."', Horatramiteok = '".$whora."'"
			. "  WHERE id = '".$wid."'";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	
	}else{
		
		$q =  " UPDATE ".$wcencam."_000003 "
			. "    SET Tramiteok = 'off', Usutramitepok = '".$wusuario."', Fechatramiteok = '".$wfecha."', Horatramiteok = '".$whora."'"
			. "  WHERE id = '".$wid."'";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	}
	
	$update = mysql_affected_rows();
	
	if($update == 0){
		
		$datamensaje['error'] = 1;
		$datamensaje['mensaje'] = "No se actualizo el autorizado ok. Favor comuicarse con soporte.";
		
	}
	
	echo json_encode($datamensaje);
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


function registrarAsignacion($wid)
{
    global $conex;
    global $wcencam;
    global $wfecha;
    global $whora_actual;
    global $wusuario;


    $q =     "  SELECT Acaids "
            ."    FROM ".$wcencam."_000010"
            ."   WHERE Acaids   = '".$wid."'"
            ."     AND Acaest   = 'on'";
    $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $row = mysql_fetch_array($res);

    //Si el id ya tiene registro en on en la tabla 10 de cencam, no hara registro de datos.
    if ($row['Acaids'] == '')
        {
        $q =  " INSERT INTO ".$wcencam."_000010(   Medico       ,   Fecha_data,   Hora_data,   Acaids,   Acaest,   Acarea, Seguridad     ) "
                                . "    VALUES('".$wcencam."','".$wfecha."','".$whora_actual."','".$wid."'        ,'on'   ,'off' , 'C-" . $wusuario . "')";
        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
        }


}

function verificarhistoriaactiva($whis, $wemp_pmla)
{

    global $wbasedato;
    global $conex;

    // Aca se verifica si el paciente esta activo y que no esten en proceso de traslado
    $q = " SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, ubiptr, ubisac "
        ."   FROM root_000036, root_000037, ".$wbasedato."_000018"
        ."  WHERE ubihis  = '".$whis."'"
        ."    AND ubihis  = orihis "
        ."    AND ubiing  = oriing "
        ."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
        ."    AND oriced  = pacced "
        ."    AND oritid  = pactid "
        ."  GROUP BY 1, 2, 3, 4, 5, 6 "
        ."  ORDER BY ubihis, ubiing ";  //se agrega el campo orden
    $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());  
    $row = mysql_fetch_array($res);

    return $row;
}

 function grabarhiseing($wemp_pmla,$wbasedato, $wfecha, $whora, $wid, $whab, $whis, $wsolohab, $wusuario)
    {

        global $conex;
        global $wbasedato;
        global $wcencam;

        $datamensaje = array('mensaje'=>'', 'error'=>0);

       //Consulta si la historia ya tenia la habitacion asignada el dia de hoy.
       $q =     "  SELECT Hab_asignada "
                ."    FROM ".$wcencam."_000003"
                ."   WHERE Historia       = '".$whis."'"
                ."     AND Hab_asignada   = '".$whab."'"
                ."     AND Anulada   = 'No'"
                ."     AND Fecha_llegada   = '0000-00-00'"
                ."     AND Fecha_cumplimiento   = '00:00:00'"
                ."     AND id     = '".$wid."'";
        $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
        $row = mysql_fetch_array($res);

        //Si la solicitud no tiene habitacion asignada
        if($row['Hab_asignada'] == '')
        {
            if ($wsolohab != 'on')
            {

               //Aqui se verifica si la historia esta activa
                $wverificahistoria = verificarhistoriaactiva($whis, $wemp_pmla);
                $wservicio_actual = $wverificahistoria['ubisac'];
				
				$esCir = esCirugia($wservicio_actual);
				$whistoriaentraslado = $esCir ? 'off' : $wverificahistoria['ubiptr']; //Si el paciente es de cirugia no tiene en cuenta el procesos de traslado.
				
                if ($wverificahistoria != '')
                {					
					
                    //Consulta si la historia ya tenia la habitacion asignada.
                   $q_verif =       "  SELECT id, Observacion "
                                    ."    FROM ".$wcencam."_000003"
                                    ."   WHERE Historia           = '".$whis."'"
                                    ."     AND Fecha_cumplimiento = '0000-00-00'"
                                    ."     AND Hora_cumplimiento = '00:00:00'"
                                    ."     AND Anulada = 'No'";
                    $res_verif = mysql_query( $q_verif, $conex ) or die( mysql_errno()." - Error en el query $q_verif - ".mysql_error() );
                    $row_verif = mysql_fetch_array($res_verif);
                    $num_verif = mysql_num_rows($res_verif);

                    if ($whistoriaentraslado == 'on')
                    {
                        $datamensaje['mensaje'] = "La historia $whis se encuentra en proceso de traslado, por lo tanto no se puede asignar.";
						$datamensaje['error'] = 1;
						echo json_encode($datamensaje);
                        return;
                    }

                   if ($num_verif > 0)
                    {
                       $datamensaje['mensaje'] = "La historia ya tiene asociada la solicitud ".$row_verif['id']." favor revisar.";
					   $datamensaje['error'] = 1;
					   echo json_encode($datamensaje);
                       return;
                    }
                    else
                    {
					
					//Consulto las observaciones de la solicitud.
					$q_reg =       "  SELECT id, Observacion "
                                    ."    FROM ".$wcencam."_000003"
                                    ."   WHERE id = '".$wid."'";
                    $res_reg = mysql_query( $q_reg, $conex ) or die( mysql_errno()." - Error en el query $q_reg - ".mysql_error() );
                    $row_reg = mysql_fetch_array($res_reg);                   
					
					$esUrg = es_urgencias($wservicio_actual);	
					$observacion_reg = $row_reg['Observacion'];
					
					//Reemplazo el texto de la historia en la observacion por la historia y su numero.
					$obs_con_his = str_replace("Historia :","Historia : $whis",$observacion_reg);					
					
					if($esUrg){
						 
						 //En este caso de marca tramites ok con el usuario que esta asignando la historia.
						 //Actualizo la historia e ingreso del paciente en la tabla 3 de cencam para los pacientes que no tienen historia en la tabla 3 de cencam
						$q =  " UPDATE ".$wcencam."_000003 "
							. "    SET Historia = '".$whis."', Hab_asignada = '".$whab."', Fec_asigcama = '".$wfecha."', Hora_asigcama = '".$whora."', Usu_central = '".$wusuario."', Tramiteok = 'on', Usutramitepok = '".$wusuario."', Fechatramiteok = '".$wfecha."', Horatramiteok = '".$whora."', Observacion = '".$obs_con_his."'"
							. "  WHERE id = '".$wid."'";
						$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
						
					}else{
						
						 //Actualizo la historia e ingreso del paciente en la tabla 3 de cencam para los pacientes que no tienen historia en la tabla 3 de cencam
						$q =  " UPDATE ".$wcencam."_000003 "
							. "    SET Historia = '".$whis."', Hab_asignada = '".$whab."', Fec_asigcama = '".$wfecha."', Hora_asigcama = '".$whora."', Usu_central = '".$wusuario."', Observacion = '".$obs_con_his."'"
							. "  WHERE id = '".$wid."'";
						$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
					}
                   

                    //La habitacion se pone en proceso de ocupacion
                    $q =  " UPDATE ".$wbasedato."_000020 "
                        . "    SET Habpro = 'on'"
                        . "  WHERE Habcod = '".$whab."'";
                    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

                    $datamensaje['mensaje'] = "Historia registrada.";

                    registrarAsignacion($wid); //Registro de datos en la tabla 10 de cencam
                    }
                }
                else
                {
                   $datamensaje['mensaje'] = "La historia ".$whis." no esta activa en el sistema.";
                   $datamensaje['error'] = 1;
                }

            }
            else
            {

                //Consulto los datos de la solicitud para saber si estan cambiando el camillero
                /*$q_cam =  "   SELECT Hab_asignada"
                            ."     FROM ".$wcencam."_000003 "
                            ."    WHERE Hab_asignada  != '".$whab."'"
                            ."	    AND id           = '".$wid."'";
                $res_cam = mysql_query($q_cam,$conex) or die (mysql_errno()." - ".mysql_error());
                $row = mysql_fetch_array($res_cam);
                $whabitacion = $row['Hab_asignada']; //Habitacion actual del paciente
                //Si hay resultado para esta consulta es porque quiere cambiar de habitacion al paciente, por lo tanto debe permitir que la cama anterior
                //sea tomada y pone la habitacion en vacio.
                if ($whabitacion != ''){
                        //La habitacion se pone en proceso de ocupacion = 'off'
                        $q =  " UPDATE ".$wbasedato."_000020 "
                            . "    SET Habpro = 'off'"
                            . "  WHERE Habcod = '".$whabitacion."'";
                        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

                        //Actualizo la historia e ingreso del paciente en la tabla 3 de cencam
                        $q =  " UPDATE ".$wcencam."_000003 "
                            . "    SET Hab_asignada = '',
                                       Fec_asigcama = '0000-00-00',
                                       Hora_asigcama = '00:00:00' "
                            . "  WHERE id = '".$wid."'";
                        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
                }*/
               //Si ha seleccionado habitacion la actualiza para la solicitudes que ya tienen historia en la tabla 3 de cencam
                if($whab != '')
                    {
						/* AQUI VIENE CUANDO DESEA CAMBIAR LA HABITACION QUE YA LE FUE ASIGNADA */
						/*
							EL PROGRAMA DE ENTREGA Y RECIBO DE PACIENTES CAMBIA LOS CAMPOS Fecha_cumplimiento Y Hora_cumplimiento
							CUANDO SE REALIZA UNA ENTREGA.
							SI ESOS DATOS ESTAN CAMBIADOS NO PUEDO ACTUALIZAR LA HABITACION PORQUE LA ENTREGA YA SE HIZO
						*/
						$q = "	SELECT * "
							."    FROM  ".$wcencam."_000003 "
							."   WHERE id = '".$wid."'"
							."     AND Hora_cumplimiento = '00:00:00'";

						$resq = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						$num = mysql_num_rows($resq);
						if( $num > 0 ){
								//Consulto los datos de la solicitud para saber si estan cambiando el camillero
								$q_cam =  "   SELECT Hab_asignada"
											."     FROM ".$wcencam."_000003 "
											."    WHERE Hab_asignada  != '".$whab."'"
											."	    AND id           = '".$wid."'";
								$res_cam = mysql_query($q_cam,$conex) or die (mysql_errno()." - ".mysql_error());
								$row = mysql_fetch_array($res_cam);
								$whabitacion = $row['Hab_asignada']; //Habitacion actual del paciente
								//Si hay resultado para esta consulta es porque quiere cambiar de habitacion al paciente, por lo tanto debe permitir que la cama anterior
								//sea tomada y pone la habitacion en vacio.
								if ($whabitacion != ''){
										//La habitacion anterior se pone en proceso de ocupacion = 'off'
										$q =  " UPDATE ".$wbasedato."_000020 "
											. "    SET Habpro = 'off'"
											. "  WHERE Habcod = '".$whabitacion."'";
										$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
								}

							//Actualizo la historia e ingreso del paciente en la tabla 3 de cencam
							$q =  " UPDATE ".$wcencam."_000003 "
								. "    SET Hab_asignada = '".$whab."', Fec_asigcama = '".$wfecha."', Hora_asigcama = '".$whora."' , Usu_central = '".$wusuario."'  "
								. "  WHERE id = '".$wid."'";
							$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

							//La habitacion se pone en proceso de ocupacion igual a off ya que el usuario
							//selecciono habitacion, pero el paciente ya tiene historia asignada.
							$q =  " UPDATE ".$wbasedato."_000020 "
								. "    SET Habpro = 'on'"
								. "  WHERE Habcod = '".$whab."'";
							$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
						}else{
							$datamensaje['mensaje'] = "La historia ya tiene ENTREGA y no es posible cambiar la habitacion";
							$datamensaje['error'] = 1;
							echo json_encode($datamensaje);
							return;
						}
                    }
                 else
                    {
								//Consulto los datos de la solicitud para saber si estan cambiando el camillero
								$q_cam =  "   SELECT Hab_asignada"
											."     FROM ".$wcencam."_000003 "
											."    WHERE Hab_asignada  != '".$whab."'"
											."	    AND id           = '".$wid."'";
								$res_cam = mysql_query($q_cam,$conex) or die (mysql_errno()." - ".mysql_error());
								$row = mysql_fetch_array($res_cam);
								$whabitacion = $row['Hab_asignada']; //Habitacion actual del paciente
								//Si hay resultado para esta consulta es porque quiere cambiar de habitacion al paciente, por lo tanto debe permitir que la cama anterior
								//sea tomada y pone la habitacion en vacio.
								if ($whabitacion != ''){
										//La habitacion se pone en proceso de ocupacion = 'off'
										$q =  " UPDATE ".$wbasedato."_000020 "
											. "    SET Habpro = 'off'"
											. "  WHERE Habcod = '".$whabitacion."'";
										$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

										//Actualizo la historia e ingreso del paciente en la tabla 3 de cencam
										$q =  " UPDATE ".$wcencam."_000003 "
											. "    SET Hab_asignada = '',
													   Fec_asigcama = '0000-00-00', Usu_central = '".$wusuario."' ,
													   Hora_asigcama = '00:00:00' "
											. "  WHERE id = '".$wid."'";
										$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
								}

						//Consulta la cama asociada al id que se actualizara para cambiarle el estado habpro a off.
						$q = "  SELECT Hab_asignada "
							."    FROM ".$wcencam."_000003"
							."   WHERE id     = '".$wid."'";
						$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
						$row = mysql_fetch_array($res);

						$whabitacion = $row['Hab_asignada'];

						//La habitacion se pone en proceso de ocupacion igua a off ya que el usuario
						//selecciono habitacion vacio, pero el paciente ya tiene historia asignada.
						$q =  " UPDATE ".$wbasedato."_000020 "
							. "    SET Habpro = 'off'"
							. "  WHERE Habcod = '".$whabitacion."'";
						$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

						//Actualizo la historia e ingreso del paciente en la tabla 3 de cencam
						$q =  " UPDATE ".$wcencam."_000003 "
							. "    SET Hab_asignada = '".$whab."', Usu_central = '".$wusuario."' "
							. "  WHERE id = '".$wid."'";
						$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

						//Actualizo el estado a off en la tabla 3 de cencam
						$q =  " UPDATE ".$wcencam."_000010 "
							. "    SET Acaest = 'off' "
							. "  WHERE Acaids = '".$wid."'";
						$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
                    }

                    if ($whab != '') // Se evalua esto para cuando se selecciona la habitacion vacia.
                        {
                        registrarAsignacion($wid);
                        }

            }
        }
        else
        {
            if ($whab != '')
            {
                $datamensaje['mensaje'] = "La historia ya esta asignada a la habitacion, favor revisar.";
                $datamensaje['error'] = 1;
            }
            else
            {
                //Actualizo la historia e ingreso del paciente en la tabla 3 de cencam
                $q =  " UPDATE ".$wcencam."_000003 "
                    . "    SET Hab_asignada = '".$whab."'"
                    . "  WHERE id = '".$wid."'";
                $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

                //La habitacion se pone en proceso de ocupacion = 'off'
                $q =  " UPDATE ".$wbasedato."_000020 "
                    . "    SET Habpro = 'off', Fecha_llegada = '0000-00-00', Hora_llegada = '00:00:00'"
                    . "  WHERE Habcod = '".$whab."'";
                $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
            }
        }

     echo json_encode($datamensaje);
    }

 function asignarhabitacion($wemp_pmla, $wfecha, $whora, $wid, $whab, $whis, $wconfirmar )
    {

		global $wemp_pmla;
        global $wbasedato;
        global $conex;
        global $wcencam;
		global $wcco;
		if( !isset($wcco) ){
			$wcco = "";
		}

        $datamensaje = array('mensaje'=>'', 'notificacion'=>0 , 'formulario'=>'');

        if($whab != '')
        {
            //Formulario que solicita la historia e ingreso para el paciente.
            $datamensaje['formulario'] .= "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:300px;overflow:auto;'><center><br><br><br>";
            $datamensaje['formulario'] .= "<table style='text-align: center; width: 100px;' border='0' rowspan='2' colspan='1'>
                                            <tr class = fila1>
                                            <td colspan=2><b>Asignar historia<b></td>
                                            </tr>
                                            <tr class = fila1>
                                            <td>Habitaci&oacute;n seleccionada:</td>
                                            <td><b>".$whab."</b></td>
                                            </tr>
                                            <tr class = fila1>
                                            <td>Historia:</td>
                                            <td><input name='historiamodal' id='historiamodal'></td>
                                            </tr>
                                        </table>
										<br><b>Si la historia del paciente esta ubicada en urgencias se <br>marcar&aacute el cajon de tramites ok autom&aacuteticamente.</b><br>";
            $datamensaje['formulario'] .= "<br><INPUT TYPE='button' value='Grabar' id='insumos' onClick='grabarhiseing(\"$wbasedato\",\"$wfecha\",\"$whora\",\"$wid\",\"$whab\",\"$wcco\")'><br>";
            $datamensaje['formulario'] .= "<br><INPUT TYPE='button' value='Cerrar' onClick=' cerraremergente();' style='width:100'><br><br>";
            $datamensaje['formulario'] .= "</center></div>";


        }
        else
        {

            if ($wconfirmar == '')
            {
                $datamensaje['mensaje'] = "Seguro que desea actualizar la habitacion?";
                $datamensaje['notificacion'] = 1;
            }
            else
            {
                //Actualizo la historia e ingreso del paciente en la tabla 3 de cencam
                $q =  " UPDATE ".$wcencam."_000003 "
                    . "    SET Historia = '".$whis."', Hab_asignada = '' "
                    . "  WHERE id = '".$wid."'";
                $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

                $datamensaje['mensaje'] = "El paciente ya no tiene habitacion asignada.";
            }


        }

       echo json_encode($datamensaje);

    }

function ponerCamillero($wid, $wcentral, $wusuario, $wcamillero, $whis, $tramitesok)
  {

   global $conex;
   global $wbasedato;
   global $wcencam;
   global $wusuario;
   global $wfecha;
   global $whora_actual;
   global $esAyuda;

   //Consulto los datos de la solicitud para saber si estan cambiando el camillero
   $q_cam =  "   SELECT Camillero"
            ."     FROM ".$wcencam."_000003 "
            ."    WHERE id           = '".$wid."'";
   $res_cam = mysql_query($q_cam,$conex) or die (mysql_errno()." - ".mysql_error());
   $row = mysql_fetch_array($res_cam);
   $wcamilleroactual = $row['Camillero']; //Camillero asignado

   if ($wcamilleroactual != $wcamillero)
    {

       //Consulta la cama asociada al id que se actualizara para cambiarle el estado habpro a off.
        $q = "  SELECT Hab_asignada "
            ."    FROM ".$wcencam."_000003"
            ."   WHERE id     = '".$wid."'";
        $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
        $row = mysql_fetch_array($res);
        $whabitacion = $row['Hab_asignada'];

        //La habitacion se pone en proceso de ocupacion = 'off'
        $q =  " UPDATE ".$wbasedato."_000020 "
            . "    SET Habpro = 'off'"
            . "  WHERE Habcod = '".$whabitacion."'";
        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

        //Actualizo la historia e ingreso del paciente en la tabla 3 de cencam
        $q =  " UPDATE ".$wcencam."_000003 "
            . "    SET Hab_asignada = '' "
            . "  WHERE id = '".$wid."'";
        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());


        //Verifica si ya fue cancelado el registro desde la seleccion de habitacion vacia.
         $q =    "  SELECT Acaids "
                ."    FROM ".$wcencam."_000010"
                ."   WHERE Acaids   = '".$wid."'"
                ."     AND Acaest   = 'off'";
        $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
        $row = mysql_fetch_array($res);

        if ($row['Acaids'] != '')
            {
            $q =     " DELETE FROM ".$wcencam."_000010 "
                    ."  WHERE Acaids = '".$wid."'"
                    ."    AND Acaest = 'on'";
             $res1 = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

            }
            else
            {
            //Actualizo el estado en la tabla 3 de cencam
            $q =  " UPDATE ".$wcencam."_000010 "
                . "    SET Acaest = 'off' "
                . "  WHERE Acaids = '".$wid."'";
            $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
            }

    }

   //Actualizamos el camillero
   $q= "  UPDATE ".$wcencam."_000003 "
	  ."     SET Camillero       = '".$wcamillero."', "
	  ."         Hora_respuesta  = '".$whora_actual."', "
	  ."         Fecha_respuesta = '".$wfecha."', "
	  ."         Central         = '".$wcentral."', "
	  ."         Usu_central     = '".$wusuario."' "
	  ."   WHERE Id = ".$wid;
   $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

   $wtipocama = explode("-", $wcamillero);

   //Se cambia la relacion de la tabla cencam_000002 con la tabla cencam_000007 ya que esta contiene los tipos de cama requeridos. //08 Octubre 2013 Jonatan
   //Consultar camas disponibles
    $q_disp= "  SELECT habcod "
            ."    FROM ".$wbasedato."_000020, ".$wcencam."_000007 "
            ."   WHERE Habtip = Tipcod"
            ."     AND Habest = 'on' "
            ."     AND Habhis = ''"
            ."     AND Habing = ''"
            ."     AND Habdis = 'on'"
            ."     AND Habpro in ('off','')"
            ."     AND Habtip = '".trim($wtipocama[0])."'"
			."     AND Habest = 'on' "              //Juanc Ene 13 2020
            ."   ORDER BY Habcod, Habord " ;
    $resdisp = mysql_query( $q_disp, $conex ) or die( mysql_errno()." - Error en el query $q_disp - ".mysql_error() );
    $numdisp = mysql_num_rows($resdisp);

    //Se registra la asignacion en la tabla 10 de cencam
    registrarAsignacion($wid);
	
	if($tramitesok == 'off'){
		$select_camas = "disabled";
	}
	
	$info_paciente = consultarInfoPacientePorHistoria($conex, $whis, $wemp_pmla);
	$ing = $info_paciente->ingresoHistoriaClinica;
    
    //Si hay camas disponibles se mostrara el listado, sino mostara el listado vacio.
    if ($numdisp != 0)
    {
    echo "<td id=tdhab$wid bgcolor=".$wcolor."  >";
    echo "<SELECT id='whabitacion$wid' style='width:5em;' $select_camas onchange=\"asignarhabitacion( '$wid', '$wfecha' , '$whora_actual', '$wcodigo','$whis', this, '$wcco', '$motivo' , '$wusuario', '$esAyuda', '$ing'  )\">";
    echo "<option> </option>";
        for($j=0;$j<$numdisp;$j++)
        {
            $rowdisp = mysql_fetch_array($resdisp);
            echo "<option value=".$rowdisp[0].">".$rowdisp[0]."</option>";
        }

    echo "</SELECT></td>";
    }
    else
    {
        echo "<td id=tdhab$i bgcolor=".$wcolor.">";
        echo "<SELECT id='whabitacion$i' style='width:5em;' $select_camas onchange=\"asignarhabitacion( '$wid', '$wfecha' , '$whora_actual', '$wcodigo','$whis',this , '$wcco', '$motivo' , '$wusuario', '$esAyuda', '$ing' )\">";
        echo "<option></option>";
        echo "</SELECT></td>";
    }



  }


function marcarLlegada__($wid, $fecha, $hora)
{

	global $conex;
	global $wcencam;
	global $wfecha;
	global $whora_actual;

	//Esta validacion inactiva la llegada.
	if ($fecha == '0000-00-00' and $hora == '00:00:00')
	{
		$wfecha = '0000-00-00';
		$whora_actual = '00:00:00';
	}

	$q = "   UPDATE ".$wcencam."_000003 "
		."      SET Hora_llegada   = '".$whora_actual."',"
		."	   	   Fecha_llegada  = '".$wfecha."'"
		."    WHERE Id = ".$wid;
	$rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
}

/**
 * Metodo que permite marcar la llegada del camillero a la habitación,
 * tambien nos permite tener en cuenta si el motivo de la solicitud es
 * por alta definitiva y está en una habitación, se realiza el proceso
 * de alistar habitación para su limpieza, esto con el fin de medir los
 * tiempos de servicio de Sodexo.  
 *
 * @param [Identificador de solicitud] $wid
 * @param [Fecha de llegada] $fecha
 * @param [Hora de llegada] $hora
 * @param [Identificador de empresa] $wemp_pmla
 * @return void
 * 
 * @author Joel David Payares Hernández
 * @since Julio 13 de 2021
 */
function marcarLlegada($wid, $fecha, $hora, $wemp_pmla)
{
	global $conex;
	global $wcencam;
	global $wemp_pmla;
	global $wfecha;
	global $whora_actual;
	global $wusuario;

	$tablaHabitaciones = consultarTablaHabitaciones( $conex, 'movhos', $wcco );
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	//Esta validacion inactiva la llegada.
	if ($fecha == '0000-00-00' && $hora == '00:00:00')
	{
		$wfecha = '0000-00-00';
		$whora_actual = '00:00:00';
	}

	$q = "   UPDATE ".$wcencam."_000003 "
		."      SET Hora_llegada   = '".$whora_actual."',"
		."			Fecha_llegada  = '".$wfecha."'"
		."    WHERE Id = ".$wid;

	mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	$datosPacienteAlta = obtenerDatosPacienteAlta( $wid, $wcencam, $wbasedato );

	if( $datosPacienteAlta['Motivo'] == 'PACIENTE DE ALTA' &&
			( $datosPacienteAlta['Habitacion'] != 'null' ) )
	{
		/**
		 * * Se hace solicitud de limpieza de habitación, teniendo en cuenta que el
		 * * motivo se paciente de alta y que este en una habitación
		 */
		$q = "
		  UPDATE	{$tablaHabitaciones}
			 SET	Habali = 'on'
		   WHERE	Habcod = '{$datosPacienteAlta['Habitacion']}'
		";

		$err = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		/**
		 * Se adiciona segmento de código que inserta un registro en central de habitación con la información de la habitación
		 * en la movhos 25.
		 * @author Joel David Payares Hernández
		 * @since Julio 13 de 2021
		 */
		$q1 = " INSERT INTO ".$wbasedato."_000025 (		Medico     ,    Fecha_data     ,  Hora_data  ,				movhab					,  movemp ,	  movfec   ,  movhem  ,  movhdi  , movobs ,					movfal				 ,					movhal				 ,   movfdi	  ,	   Seguridad	) "
		     ."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora_actual."','".$datosPacienteAlta['Habitacion']."',	''	  ,'0000-00-00','00:00:00','00:00:00',	''	 ,'".$datosPacienteAlta['FechaEgreso']."','".$datosPacienteAlta['HoraEgreso']."','0000-00-00','C-".$wusuario."')";
		$err = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}
}

/**
 * Metodo que permite obtener la información del paciente de la solicitud
 * realizada, y así poder validar el motivo y si está en habitación.
 *
 * @param [Identificador de solicitud] $wid
 * @param [Tabla camilleros] $wcencam
 * @param [Identificador de empresa] $wemp_pmla
 * @return [Array] Datos de respuesta del paciente.
 * 
 * @author Joel David Payares Hernández
 * @since Julio 13 de 2021
 */
function obtenerDatosPacienteAlta( $wid, $wcencam, $wbasedato )
{
	$sql = "
		  SELECT	{$wcencam}_000003.Historia,
					{$wcencam}_000003.Motivo,
					{$wcencam}_000003.Habitacion
			FROM	{$wcencam}_000003
		   WHERE	{$wcencam}_000003.id = {$wid}
		";

	$resultQuery = mysql_query($sql, $conex) or die (mysql_errno() . " - " . mysql_error());

	$cantidad_registros = mysql_num_rows( $resultQuery );
	$respuesta = mysql_fetch_array( $resultQuery );

	if( $cantidad_registros > 0 )
	{
		$q="
			  SELECT	Fecha_egre_serv as FechaEgreso,
						Hora_egr_serv as HoraEgreso,
						Num_ingreso as Ingreso,
						Historia_clinica as Historia
				FROM	{$wbasedato}_000033
			   WHERE	Historia_clinica = '{$respuesta[0]}'
			ORDER BY	Fecha_data Desc
			   LIMIT	1";

		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$rowdia = mysql_fetch_array($err);
		$rowdia['Motivo'] = $respuesta[1];
		$rowdia['Habitacion'] = json_decode( $respuesta[2] )[0] == '' ? 'null' : json_decode( $respuesta[2] )[0];
	}

	return $rowdia;
}

function marcarCumplimiento($wid, $fecha, $hora)
  {

   global $conex;
   global $wcencam;
   global $wfecha;
   global $whora_actual;

   //Se actualiza la fecha y la hora de cumplimiento.
   $q = "   UPDATE ".$wcencam."_000003 "
	   ."      SET Hora_cumplimiento = '".$whora_actual."',"
	   ."          Fecha_cumplimiento = '".$wfecha."'"
	   ."    WHERE Id = ".$wid;
   $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

  //Se marca el la solicitud como realizada.
    $q = "   UPDATE ".$wcencam."_000010 "
	   ."      SET Acarea = 'on'"
	   ."    WHERE Acaids = ".$wid.""
       ."      AND Acaest = 'on'";
    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

  }

function marcarAnular($wid, $wanu)
  {

   global $conex;
   global $wcencam;
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora_actual;

   //Cuando se hace la "Entrega" el campo Hora_cumplimiento ya no es "00:00:00"
   $q = "	SELECT * "
		."    FROM  ".$wcencam."_000003 "
		."   WHERE id = '".$wid."'"
		."     AND Hora_cumplimiento = '00:00:00'";

	$resq = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($resq);
	if( $num > 0 ){
		   $q = "   UPDATE ".$wcencam."_000003 "
			   ."      SET Anulada = '".$wanu."', Usu_anula='".$wusuario."', Fecha_anula = '".$wfecha."', Hora_anula = '".$whora_actual."'"
			   ."    WHERE Id = ".$wid;
		   $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		   //Consultar cama asignada
			$q= "  SELECT Hab_asignada "
					."    FROM ".$wcencam."_000003 "
					."   WHERE id = ".$wid."" ;
			$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$row = mysql_fetch_array($res);
			$whab = $row['Hab_asignada'];

			//Se actualiza la habitacion para que este disponible
			$q =  " UPDATE ".$wbasedato."_000020 "
				. "    SET Habpro = 'off'"
				. "  WHERE Habcod = '".$whab."'";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	}else{
		$datamensaje['mensaje'] = "El paciente ya fue entregado a la habitacion, debe cancelarse la entrega";
		$datamensaje['error'] = 1;
		echo json_encode($datamensaje);
		return;
	}
  }

function grabarObservacion($wid, $wtexto)
  {
   global $conex;
   global $wcencam;

   $q = "   UPDATE ".$wcencam."_000003 "
	   ."      SET Observ_central = '".$wtexto."'"
	   ."    WHERE Id = ".$wid;
   $rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
  }


  /***************************************************
	*	           CENTRAL DE CAMILLEROS             *
	*				CONEX, FREE => OK				 *
	**************************************************/
	//===================================================================================================================================
//PROGRAMA                   : central_de_camilleros_AJAX.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             :
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Sep 09 de 2021";
//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES
/*
//========================================================================================================================================\\
Septiembre 09 de 2021 - Joel Payares Hernández
	Se modifica el metodo marcarLlegada, adicionando el segmento sql que permita insertar el registro de la habitación en la central de
	de habitación en la tabla movhos 25.
//========================================================================================================================================\\
Julio 13 de 2021 - Joel Payares Hernández
	Se modifica el metodo marcarLlegada, para obtener información del paciente como lo es: historia, ingreso, habitación habitada. Estos datos
	para poder actualizar los registos en la tabla de habitación, y colocar en estado limpieza.
//========================================================================================================================================\\
Enero 13 de 2020 Juan Carlos Hernández
	Se modifican los querys de consulta a la tabla movhos_000020, para se tenga en cuenta en habitaciones disponibles tambiém el estado = 'on'
//========================================================================================================================================\\
Abril 5 de 2018 Jonatan Lopez
	Se muestra la fecha y hora de tramite marcado en ok.
//========================================================================================================================================\\
Marzo 7 de 2017 Jonatan Lopez
	Se muestra en la parte superior los pacientes que tienen conducta de remision en la tabla hce_000022.
//========================================================================================================================================\\
Octubre 4 de 2016 Jonatan Lopez
	Se realiza a entrega de pacientes que tengan centro de costos de ayuda diagnostica y se le asigna habitacion, esto solo es posible despues de 
	las 6 pm con el parametro HoraInicioTrasladoAyuda de la root_000051.
//========================================================================================================================================\\
Septiembre 26 de 2016 Jonatan Lopez
  Cuando se asigna historia a un paciente en las solicitudes de cirugia se agrega el numero de historia en la observacion de la solicitud.
//========================================================================================================================================\\
Septiembre 20 de 2016 Jonatan Lopez
   Se controla el cajon de tramites ok para que solo este activo para los usuarios configurados en el parametro UsuariosTramitesOk de la 
   tabla root_000051.
   Se puede asignar historia a una solicitud que no la tenga, despues de seleccionar la habitacion que se va a asignar.
//========================================================================================================================================\\
//Agosto 23 de  2016 Jonatan Lopez
//Se controla la asignacion de cama para urgencias con el cajon de tramiteok, al seleccionarlo, se activa el seleccionador de camas.
//========================================================================================================================================\\
//Julio 25 de 2016 Jonatan Lopez
//Se agregan acciones que permiten validar pacientes con alta definitiva, asignar cama a pacientes de cirugia que esten en proceso de traslado.
//========================================================================================================================================\\
//Febrero 05 de 2016: Jessica Madrid Mejía
//Se agrega buscador y se añade la fecha para calcular los tiempos de atencion de la solicitud ya que solo tenía encuenta la hora
//========================================================================================================================================\\
// Octubre 30 de 2015: Eimer Castro
// Se realiza una nueva validación para que se permita anular las solicitudes realizadas desde centrales que no manejen pacientes con historia.
// Octubre 20 de 2015: Eimer Castro
// Se crea la la validación en post verificar_entrega_paciente para verificar si a la solicitud de traslado de un paciente ya se le efectuó el
// proceso de entrega y por ende no permitir la anulación de la solicitud si así fue. la consulta se realiza en las tablas movhos_000017 y
// movhos_000018. También se hace la verificación para no cambiar de cama al paciente si este ya fue entregado. Se cambian los alert por jAlert.
// Octubre 09 de 2013
// Se agrega union de la tabla cencam_000007, donde este la tabla cencam_000002 ya que la tabla cencam_000007 contiene los tipos de cama
// en una sola tabla y asi se genera mejor control.
//========================================================================================================================================\\
//Abril 25 de 2013: Frederick Aguirre
//Se valida que, cuando voy a anular la asignacion de habitacion, no este efectiva la ENTREGA.
//========================================================================================================================================\\
//Abril 16 de 2013: Frederick Aguirre
//Se valida que, cuando voy a cambiar la habitacion asignada, no este efectiva la ENTREGA.
///========================================================================================================================================\\
//Marzo 16 de 2013: Jonatan Lopez
//Se agregan campos en la tabla cencam_000003 para registrar el usuario que anula, la fecha y la hora, funcion marcarAnular.
///========================================================================================================================================\\
//Diciembre 18 de 2012: Jonatan Lopez
//==========================================================================================================================================
//Se agrega un mensaje de confirmacion para la anulacion de las solicitudes.                                                                                                                 \\
//========================================================================================================================================\\
////========================================================================================================================================\\
//Diciembre 06 de 2012: Jonatan Lopez
//==========================================================================================================================================
//Se agrega fec_asigcama y hora_asigcama para guardar la fecha y la hora de asignacion de la cama.                                                                                                                     \\
//========================================================================================================================================\\
//Noviembre 30 de 2012: Jonatan Lopez
//==========================================================================================================================================
//Se agrega una columna para que se le asigne habitacion al paciente, si no tiene historia el sistema se la pedira al seleccionar una habitacion,
//además se validara si la historia esta activa en el sistema.
// ==========================================================================================================================================
// Junio 06 de 2012 :   Camilo Zapata
// ==========================================================================================================================================
// - Se modificó el programa para que registre no solo la hora de cualquier acción(respuesta, cumplimiento, llegada) sino tambien la fecha.
// ==========================================================================================================================================
// Febrero 16 de 2012 // Jonatan Lopez
//Se elimina la validacion para la hora de ingreso del usuario, permitiendo que el usuario que ingrese al panel sea el asignado a la central.
//
//========================================================================================================================================\\
// Febrero 13 de 2012 // Jonatan Lopez
//Se agrega checkbox al lado derecho de text area donde se escribien las observaciones, al seleccionar el textarea graba la informacion en la
// base de datos, ademas se configura el javascript para detener la actualizacion de la pagina, esto ocurre cuando el mouse ingresa al textarea
// de edicion, cuando el mouse esta inactivo por cierto tiempo se inicia de nuevo la carga automatica de la pagina.
//
//========================================================================================================================================\\
*/
if(!isset($_SESSION['user']) and !isset($user))
	echo "error usuario no esta registrado";
else
{
    

    include_once("root/magenta.php");
    include_once("root/comun.php");

    $conex = obtenerConexionBD("matrix");

    


    $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
    $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
    $whorainiciotrasladoayuda = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HoraInicioTrasladoAyuda');

    $wfecha=date("Y-m-d");
	$hora = date("H:i:s");
    $whora_actual = date("H:i:s");

  	$key = substr($user,2,strlen($user));

	if (strpos($user,"-") > 0)
          $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

// Funcion que crea un arreglo con todas las habitacoin y se le agrega el nombre del centro de costos.
function todaslashab(){
	
	
	global $wbasedato;
	global $conex;

	//Consulto todas las habitaciones
	$q_hab = "SELECT TRIM(Habcod) AS Habcod, Habcco
			FROM ".$wbasedato."_000020 ";
	$res_hab = mysql_query($q_hab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_hab . " - " . mysql_error());

	//Consulto todos los centros de costo
	$q_cco = "SELECT Ccocod, Cconom
			FROM ".$wbasedato."_000011 ";
	$res_cco = mysql_query($q_cco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cco . " - " . mysql_error());

	$cco = array();
	
	//Creo un arreglo con la informacion de los centros de costo.
	while($fila_cco = mysql_fetch_array($res_cco)){
		
		if(!array_key_exists($fila_cco['Ccocod'], $cco)){
			
			$cco[$fila_cco['Ccocod']] = $fila_cco;		
		}
		
	}
	
	$hab = array();
	//Creo un arreglo con la informacion de las habitacion y le agrego el nombre del centro de costos al que pertenece.
	while($fila_hab = mysql_fetch_array($res_hab)){
		
		if(!array_key_exists(trim($fila_hab['Habcod']), $hab)){
			
			$cod_hab = trim($fila_hab['Habcod']);
			$hab[$cod_hab] = $fila_hab;	
			$hab[$cod_hab]['nombre_cco'] = $cco[$fila_hab['Habcco']]['Cconom'];	
			
		}				
		
	}	
	// echo "<pre>";
	// print_r($hab);
	// echo "</pre>";
	
	return $hab;
}	  
	  
function esAyuda($wcco){

	global $wbasedato;
	global $conex;

	$es = false;

	$q = "SELECT Ccoayu
		 	FROM ".$wbasedato."_000011
		   WHERE Ccocod = '".$wcco."'
			 AND Ccoayu = 'on'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccoayu'] == 'on') ? $es = true : $es = false;
	}

	return $es;
}

	  //Consulta si un centro de costos es de cirugia
function esCirugia($wcco){

	global $wbasedato;
	global $conex;

	$es = false;

	$q = "SELECT Ccocir
		 	FROM ".$wbasedato."_000011
		   WHERE Ccocod = '".$wcco."'
			 AND Ccourg != 'on'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccocir'] == 'on') ? $es = true : $es = false;
	}

	return $es;
}
	  
function es_urgencias($wcco)
{
	global $wbasedato;
	global $conex;

	$q = " SELECT count(*) "
		."   FROM ".$wbasedato."_000011 "
		."  WHERE ccocod = '".$wcco."' "
		."    AND (ccourg='on')";
	$rescir = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$rowcir = mysql_fetch_array($rescir);

	if($rowcir[0]>0)
		return true;
	else
		return false;
}
	  
function actualizar_operador($wcentral, $wcodope, $whorope)
      {

	   global $wcentral;
	   global $wcodope;
	   global $whorope;
	   global $hora;
	   global $wusuario;
	   global $conex;
       global $wcencam;
       global $wbasedato;


	   //Traigo el operario que tiene actualmente y la hora hasta la que se queda en turno
	   $q = " SELECT cenope, cenhop "
	       ."   FROM ".$wcencam."_000006 "
	       ."  WHERE codcen = '".$wcentral."'"
	       ."    AND cenest = 'on' ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);

	   $wope_reg=$row[0];
	   $whor_reg=$row[1];

	   $whora=explode(":",$hora);

//ANALIZAR ESTO COMO FUNCIONARIA CON LA TABLA cencam7
	   //Busco si el usuario es un camillero de la misma central para colocarlo como operario de la central
	   //esto beneficia mucho la central de Servicio Farmaceutico
	   $q = " SELECT COUNT(*) "
	       ."   FROM ".$wcencam."_000002 "
	       ."  WHERE codced  = '".$wusuario."'"
	       ."    AND central = '".$wcentral."'";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   $row = mysql_fetch_array($res);


	   if ($row[0] > 0)  //Si entra es porque si es un Camillero de la misma Central
	      {
	       //============================================================================================================================
		   //Según la hora actual coloco cual es el turno del operario y lo coloco en el registro de la central en la tabla cencam_000006
		   //============================================================================================================================
		   if (strval($whora[0]) >= 7 and strval($whora[0]) < 19)
		        $whor_gra="19:00:00";
		     else
		        $whor_gra="07:00:00";
		   //============================================================================================================================

					 if ($wusuario != $wope_reg)   //Si el usuario que entro es diferente al que esta como operario, entonces entro a cambiarlo
					    {
						 $q = " UPDATE ".$wcencam."_000006 "
						     ."    SET cenope  = '".$wusuario."', "
						     ."        cenhop  = '".$whor_gra."'  "
						     ."  WHERE codcen  = '".$wcentral."'  "
						     ."    AND cenest  = 'on' ";
						 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				        }


	      }
	  }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ACA SE COLOCA EL HORARIO DE ATENCION EN CENTRAL DE CAMILLEROS POR MATRIX
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$whora_atencion="LAS 24 HORAS DE LUNES A DOMINGO";
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));


        ///////////////////////////////////////////////////////////////////////////////
    //////////////////////////////// A J A X  /////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////////
    if (isset($consultaAjax))
        {
        switch($consultaAjax)
            {
            case 'camillero':
                {
                echo ponerCamillero($wid, $wcentral, $wusuario, $wcamillero, $whis, $tramitesok);
                }
                break;
            case 'anular':
                {
                echo marcarAnular($wid, $wanu);
                }
                break;
            case 'llegada':
                // echo marcarLlegada($wid, $ifecha, $ihora);
                echo marcarLlegada($wid, $ifecha, $ihora, $wemp_pmla);
                break;
            case 'cumplimiento':
                {
                echo marcarCumplimiento($wid, $ifecha, $ihora);
                }
                break;
            case 'observacion':
                {
                echo grabarObservacion($wid, $wtexto);
                }
                break;

            case 'asignarhabitacion':
                {
                echo asignarhabitacion($wemp_pmla, $wfecha, $whora, $wid, $whab , $whis, $wconfirmar );
                }
                break;

             case 'grabarhiseing':
                {
                echo grabarhiseing($wemp_pmla,$wbasedato, $wfecha, $whora, $wid, $whab, $whis, $wsolohab, $wusuario);
                }
                break; 
			case 'marcartramitesok':
                {
                echo marcartramitesok($wemp_pmla, $wid, $marcado);
                }
                break;
            default :
                break;
            }
        return;
        }

    ///////////////////////////////////////////////////////////////////////////////   asignarhabitacion(wid, fecha, hora, codigo, historia, ingreso, este)

if (!isset($consultaAjax))
{

	$q = " SELECT nomcen, centre, cenvig, cenest, cenope, cenhop "
	    ."   FROM ".$wcencam."_000006 "
	    ."  WHERE codcen = '".$wcentral."'";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	  {
	   $row = mysql_fetch_array($res);
	   $wnomcen               = $row[0];
	   $wtiempo_refresh       = $row[1];	//Tiempo en SEGUNDOS que tarda la pantalla en referescarse o actualizarse
	   $wvigencia_solicitudes = $row[2];    //Tiempo en HORAS que puede verse una solicitud que no se halla terminado
	   $westado_central       = $row[3];    //Estado de la central 'on': activa, 'off':inactiva
	   $wcodope               = $row[3];    //Codigo del operador de la central
	   $whorope               = $row[3];    //Hora hasta la que esta el operario

	   encabezado("CENTRAL ".$wnomcen,$wactualiz, "clinica");
	   actualizar_operador($wcentral, $wcodope, $whorope);
	  }
	 else
	    echo "<tr><td align=center bgcolor=#fffffff colspan=13><font size=3 text color=#CC0000><b>FALTA DEFINIR EN LA TABLA cencam_000006 EL CODIGO DE LA CENTRAL</b></font></td></tr>";



	if ($westado_central=="on")
	  {
	    //====================================================================================================================================
	    //COMIENZA LA FORMA
	    //echo "<form name=central action='central_de_camilleros_AJAX.php' method=post>";
		echo "<form name=central id=central method=post>";
	    echo "<input type='HIDDEN' ID='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='HIDDEN' ID='wcentral' value='".$wcentral."'>";
		echo "<input type='HIDDEN' ID='wtiempo_refresh' value='".$wtiempo_refresh."'>";
		$wdatos_habitaciones = todaslashab();
		echo "<input type='HIDDEN' ID='whabitaciones' value='".json_encode($wdatos_habitaciones)."'>";
		
		$hora1 = strtotime( date("H:m:s") ); //Hora en la que inicia el traslado desde centro de costos de ayuda directo al piso al asignar la habitacion.
		$hora2 = strtotime( $whorainiciotrasladoayuda );
		$hora_traslado_ayuda = false;		
		
		//Valida si la fecha actual del servidor es mayor a la hora de inicio de traslado de pacientes de ayuda (6 pm), datos de la tabla root_000051 (Detapl= HoraInicioTrasladoAyuda).
		if( $hora1 > $hora2 ) {
			$hora_traslado_ayuda = true ;
		} 
		
		if ($wcentral == 'CAMAS'){
		
			traer_pacientes_remision_urgencias();
		
		}
		
		echo $hora_traslado_ayuda;
	    echo "<HR align=center></hr>";		
		echo "<td  align='right' colspan=5 width='40%'><span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>Buscar:&nbsp;&nbsp;</b><input id='buscarTabla' type='text' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF'></td>";
		
		//===================================================================================================================================================
	    // QUERY PRINCIPAL
	    //===================================================================================================================================================
	    // ACA TRAIGO TODAS LAS SOLICITUDES HECHAS QUE NO TENGAN MAS DE DOS HORAS DE ESPERA
	    //===================================================================================================================================================
	   $q = "  SELECT A.Hora_data, Origen, Motivo, Observacion, Destino, Solicito, Camillero, "
	        ."         Hora_llegada, Hora_Cumplimiento, A.Id, Habitacion, Observ_central, A.fecha_data, A.Historia, A.Hab_asignada,A.Fecha_data, Fechatramiteok, Horatramiteok, tramiteok "
		    ."    FROM ".$wcencam."_000003 A, ".$wcencam."_000001 B"
		    ."   WHERE Anulada           = 'No' "
			."     AND TIMESTAMPDIFF(HOUR,CONCAT(A.Fecha_data,' ',A.Hora_data),CONCAT('".$wfecha."',' ','".$hora."')) <= ".$wvigencia_solicitudes
		    ."     AND Hora_cumplimiento = '00:00:00' "
		    ."     AND Motivo            = Descripcion "
		    ."     AND A.central         = '".$wcentral."'"		  
		    ."   ORDER BY A.Fecha_data desc, A.Hora_data desc";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());  //or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());

		echo "<center><table id='tbInformacion' border=0>";
	    echo "<tr><td align=left bgcolor=#fffffff colspan=3><font size=2 text color=#CC0000><b>Hora: ".$hora."</b></font></td><td align=left bgcolor=#fffffff colspan=3><font size=2 text color=#CC0000><b>Cantidad de Solicitudes: ".$num."</b></font></td></tr>";

	    echo "<tr class='encabezadoTabla'>";
	    echo "<th><font size=1>Fecha</font></th>";
	    echo "<th><font size=1>Hora</font></th>";
	    echo "<th><font size=1>Origen</font></th>";
	    echo "<th><font size=1>Motivo</font></th>";
	    echo "<th><font size=1>Habitación origen</font></th>";
	    echo "<th><font size=1>Observacion</font></th>";
	    echo "<th><font size=1>Destino</font></th>";
	    echo "<th><font size=1>Solicitado por</font></th>";

        if ($wcentral == 'CAMAS')
        {
            echo "<th><font size=1>Tipo de Cama</font></th>";
        }
        else
        {
            echo "<th><font size=1>Camillero/Ubicación</font></th>";
        }
		
		 if ($wcentral == 'CAMAS')
        {
        echo "<th><font size=1>Ok</font></th>";
        }
	    echo "<th><font size=1>Lle</font></th>";
	    echo "<th><font size=1>Cum</font></th>";
	    echo "<th><font size=1>Anu</font></th>";
        if ($wcentral == 'CAMAS')
        {
        echo "<th><font size=1>Habitación destino</font></th>";
        }
	    echo "<th><font size=1>Observ.Central</font></th>";
        echo "<th><font size=1>Grabar</font></th>";
        echo "<th><font size=1>Nro de<br>Solicitud</font></th>";
	    echo "</tr>";

	    for ($i=$num;$i>=1;$i--)
		   {
			$row = mysql_fetch_array($res);
			
		    // $a1=$row[0];
		    // $a2=date("H:i:s");
		    // $a3=((integer)substr($a2,0,2)-(integer)substr($a1,0,2))*60 + ((integer)substr($a2,3,2)-(integer)substr($a1,3,2)) + ((integer)substr($a2,6,2)-(integer)substr($a1,6,2))/60;
			$tramiteok = $row['tramiteok'];
			$fechahoratramiteok = $row['Fechatramiteok']." ".$row['Horatramiteok'];
			$checkok = "";
			$solo_lectura_camas = "";			
			$tramiteactivo = "";
			//Fecha y hora acutal en segundos
		    $a2 = time();
			//Convierte la fecha de la base de datos a segundos
			$a1 = strtotime( $row[ 'Fecha_data' ]." ".$row[ 'Hora_data' ] );
			
			//diferencia entre fecha actual y hora actual en minutos
			//Como todo esta en segundos se dive entre 60 para convertir a minutos
			$a3 = ($a2-$a1)/60;

			
		    //Aca configuro la presentacion de los colores segun el tiempo de respuesta
		    if ($a3 > 5)                 //$a3 > 5
		       {
		        $wcolor = "CCCCFF";      //Lila
		        $wcolorfor = "000000";   //Negro
	           }
		    if ($a3 > 2.5 and $a3 <= 5)  //$a3 > 2.5 and $a3 <= 5
		       {
		        $wcolor = "FFFF66";      //Amarillo
		        $wcolorfor = "000000";   //Negro
	           }
		    if ($a3 <= 2.5)              //$a3 <= 2.5
		       {
			    $wcolor = "99FFCC";      //Verde
			    $wcolorfor = "000000";   //Negro
	           }

			echo "<tr bgcolor=".$wcolor." class='find'>";
			echo "<td><font size=1 color=".$wcolorfor.">".$row[12]."</font></td>";                  // Fecha de solicitud
		    echo "<td><font size=1 color=".$wcolorfor.">".$row[0]."</font></td>";                   // Hora de solicitud
		    echo "<td><font size=1 color=".$wcolorfor.">".$row[1]."</font></td>";                   // Origen
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[2]."</font></td>";                   // Motivo
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[10]."</font></td>";                  // Habitacion
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[3]."</font></td>";                   // Observacion
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[4]."</font></td>";                   // Destino
			
			$qcco = "  SELECT Cco "
					."    FROM ".$wcencam."_000004 "
					."   WHERE Nombre = '".$row['Origen']."'";
		    $rescco = mysql_query($qcco,$conex) or die (mysql_errno()." - ".mysql_error());
		    $rowcco = mysql_fetch_array($rescco);	
			
			$cco_origen = explode("-",$rowcco['Cco']);
			$cco_origen = trim($cco_origen[0]);
			
			$motivo = $row['Motivo'];
			
			$wid = $row[9];
	        //============================================================================================================================
	        //TRAIGO EL NOMBRE DE QUIEN SOLICITO EL SERVICIO
	        $q = "  SELECT Descripcion "
	            ."    FROM usuarios "
	            ."   WHERE Codigo = '".$row[5]."'"
	            ."     AND Activo = 'A' ";
		    $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		    $row1 = mysql_fetch_array($res2);

		    if ($row1[0] == "")
		       $wnomusu = $row[5];                                                                        // Sin Nombre de Solicitado por
		      else
		         $wnomusu = $row1[0];                                                                     // Solicitado por
		    echo "<td><font size=1 color=".$wcolorfor.">".$wnomusu."</font></td>";    // Nombre de quien solicito el servicio
		    //============================================================================================================================

		    //============================================================================================================================
		    //MUESTRO LOS CAMILLEROS
			$wcodigo = substr($row[6],0,(strpos($row[6],"-")-1));

			$whis = $row['Historia'];  // Historia
            if($whis == "" || $whis == '' || $whis == null) {
               $whis = '';
            }
            $whab = $row['Hab_asignada'];  // Habitacion asignada
			
			if ($wcentral == 'CAMAS')
            {
				$ultimo_ingreso = consultarUltimoIngresoHistoria($conex, $whis);
				$datos_paciente = consultarUbicacionPaciente($conex, $wbasedato, $whis, $ultimo_ingreso);
				
				$ald_text_area = "";
				$solo_lectura_alta_def = "";
				$solo_lectura_alta_proc = "";
				$texto_alta = "";
				
				if($datos_paciente->altaDefinitiva == 'on'){
					
					$ald_text_area = "style='background-color: #FDC6C6;'";
					$solo_lectura_alta_def = "disabled";
					$texto_alta = "<font size=1>En alta definitiva</font>";
					
				}
				
				//Valida el centro de costos actual del paciente.
				$esUrgencias  = es_urgencias($datos_paciente->servicioActual);
				$esAyuda = esAyuda($datos_paciente->servicioActual);
				
				 if($esUrgencias and $tramiteok != 'on'){
					
					$solo_lectura_camas = "disabled";			
					
				}
				
				if($esUrgencias){
					
					//Valida si el usuario puede marcar tramites ok.
					$usuariotramiteok = usuariotramiteok($wusuario);
					
					if($usuariotramiteok){						
						$tramiteactivo = "";
					}else{
						$tramiteactivo = "disabled";
					}
				
				}
			}
			
			
			//El tipo lo utilizo para poder ordenar el query por nombre, trayendo primero el registro que selecciono el usuario
			//Se agrega el union de la tabla 7 de cencam ya que esta contiene los tipos de cama con sus codigos. //09 octubre de 2013 Jonatan
			$q = " SELECT * FROM (SELECT Codigo, Nombre, 1 AS Tip "
				."    FROM ".$wcencam."_000002 "
				."   WHERE Codigo = '".$wcodigo."'"
				."     AND Unidad != 'INACTIVO' "
				."     AND central = '".$wcentral."'"
				."   UNION "
				."  SELECT Codigo, Nombre, 2 AS Tip "
				."    FROM ".$wcencam."_000002 "
				."   WHERE Codigo != '".$wcodigo."'"
				."     AND Unidad != 'INACTIVO' "
				."     AND central = '".$wcentral."'"
				."   UNION "
				."  SELECT tipcod as Codigo, tipdes as Nombre, 3 AS Tip "
				."    FROM ".$wcencam."_000007 "
				."   WHERE tipcen = '".$wcentral."'"
				."     AND tipcod = '".$wcodigo."'"
				."   UNION "
				."  SELECT tipcod as Codigo, tipdes as Nombre, 4 AS Tip "
				."    FROM ".$wcencam."_000007 "
				."   WHERE tipcen = '".$wcentral."'"
				."     AND tipcod != '".$wcodigo."') as t"
				." GROUP BY Nombre"
				." ORDER BY Tip, Nombre " ;
			$rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$numcam = mysql_num_rows($rescam) or die (mysql_errno()." - ".mysql_error());

			echo "<td>$texto_alta";
			echo "<SELECT id='wcamillero[".$i."]' onchange=\"colocarCamillero( $wid, '$wusuario', this, '$whis', '$i', '$esUrgencias')\">";
			if (trim($row[6]) == "")      //Si no viene ningún dato desde el registro de la tabla, muestro un nulo
			   echo "<option> </option>";
			for($j=0;$j<$numcam;$j++)
			   {
				$rowcam = mysql_fetch_array($rescam);
				echo "<option>".$rowcam[0]." - ".$rowcam[1]."</option>";
			   }
			if (trim($row[6]) != "")      //Si no viene ningún dato desde el registro de la tabla, muestro un nulo
			   echo "<option> </option>";
			echo "</SELECT></td>";			
			
			if ($wcentral == 'CAMAS')
            {
				if($tramiteok == 'on'){
					$checkok = "checked";
					$wcolor = "#3CB648";
					$fecha_hora_ok = "Fecha y hora trámites realizados: <br>".$fechahoratramiteok;
				}
				
				echo "<td align=center id='marcadook_".$wid."' bgcolor='".$wcolor."'><INPUT TYPE=CHECKBOX class='tramiteok' title='".$fecha_hora_ok."' $tramiteactivo $checkok title='TRAMITES OK' ID='wtramitesok_$i' onclick=\"marcartramitesok( $wid, $i, '$esUrgencias')\"></td>";
			}
		    //============================================================================================================================
		    //Evaluo si la LLEGADA ya habia sido dada, si no, evaluo si se dio dando click en checkbox, si no la muestro desmarcada
		    //Tiene hora de llegada pero no tiene asignado camillero, entonces coloco la hora de llegada en ceros
		    if (strpos($row[6],"-") > 0)   //Si hay camillero
			  {
			   if ($row[7] == "00:00:00")   //Si NO hay llegada
			      echo "<td align=center ><INPUT TYPE=CHECKBOX   title='LLEGADA' ID='wllegada[".$i."]' onclick=\"marcarLlegada( $wid, '', '', $i)\"></td>";
				  else
				     echo "<td align=center><INPUT TYPE=CHECKBOX   title='LLEGADA' ID='wllegada[".$i."]' onclick=\"marcarLlegada( $wid,'0000-00-00', '00:00:00', $i)\" CHECKED></td>";
			  }
			 else
				echo "<td align=center><INPUT TYPE=CHECKBOX   title='LLEGADA' ID='wllegada[".$i."]' onclick=\"marcarLlegada( $wid, '', '', $i)\"></td>";


			//============================================================================================================================
		    //Evaluo si el CUMPLIMIENTO ya habia sido dado, si no, evaluo si se dio dando click en checkbox, si no lo muestro desmarcado
		    echo "<td align=center><INPUT TYPE=CHECKBOX   title='CUMPLIDO' ID='wcumplimiento[".$i."]' onclick=\"marcarCumplimiento( $wid, '$wfecha', '$whora_actual', $i)\"></td>";

			//============================================================================================================================
		    //Evaluo si ha sido ANULADA la solicitud
		    if (strpos($row[6],"-") > 0 and $row[7] == "00:00:00")        //Tiene camillero pero NO llegada
                echo "<td align=center><INPUT TYPE=CHECKBOX   title='ANULAR' ID='wanulada[".$i."]' onclick=\"marcarAnular( '$wid', 'Si', '$i', '$whis')\" id_check='chkSol_" . $wid . "'></td>";
			else
			     if ($row[7] == "00:00:00")   //No Tiene llegada dejo anular
                    echo "<td align=center><INPUT TYPE=CHECKBOX   title='ANULAR' ID='wanulada[".$i."]' onclick=\"marcarAnular( '$wid', 'Si', '$i', '$whis')\" id_check='chkSol_" . $wid . "'></td>";
			     else	                      //Tiene llegada NO dejo anular
				     echo "<td align=center><INPUT TYPE=CHECKBOX   title='ANULAR' ID='wanulada[".$i."]' onclick=\"marcarAnular( '$wid', 'No', '$i', '$whis')\" id_check='chkSol_" . $wid . "'></td>";

        //Este seleccionador solo sale para la central de camas
           if ($wcentral == 'CAMAS')
            {						
				
           //Consultar camas disponibles
            $q_disp= "  SELECT habcod, habcco "
                    ."    FROM ".$wbasedato."_000020, ".$wcencam."_000007 "
                    ."   WHERE Habtip = tipcod"
                    ."     AND Habdis = 'on'"
                    ."     AND Habpro in ('off','','NO APLICA')"
                    ."     AND Habtip = '".$wcodigo."'"
					."     AND Habest = 'on' "                    //JuanC Ene 13 de 2020
                    ."   ORDER BY Habcod, Habord " ;
			$resdisp = mysql_query($q_disp,$conex) or die (mysql_errno()." - ".mysql_error());
			$numdisp = mysql_num_rows($resdisp);
			
			$codCcoCirugia = consultarCcoCirugia();
			$info_paciente = consultarInfoPacientePorHistoria($conex, $whis, $wemp_pmla);
			$ing = $info_paciente->ingresoHistoriaClinica;
			
			echo "<input type=hidden value='$codCcoCirugia' id='ccocirugia'>";
			
	        echo "<td id=tdhab$wid>$texto_alta";
			echo "<div id=select$wid>
                      <SELECT id='whabitacion$wid' style='width:5em;' $solo_lectura_camas onchange=\"asignarhabitacion( '$wid', '$wfecha' , '$hora', '$wcodigo','$whis', this, '$cco_origen', '$motivo' , '$wusuario', '$esAyuda', '$ing', '$hora_traslado_ayuda')\">";
                echo "<option value=''></option>";
                //Si el paciente ya tiene habitacion asiganada, se mostrara, en caso contrario mostrara las habitaciones disponibles.
                if (trim($whab) != "")
                {
                    echo "<option selected value='selected_".$whab."'>".$whab."</option>";
                }
                for($j=0;$j<$numdisp;$j++)
                {
                    $rowdisp = mysql_fetch_array($resdisp);
                    echo "<option value='".trim($rowdisp[0])."'>".$rowdisp[0]."</option>";
                }

			echo "</SELECT></div></td>";
            }
	        //===========================================================================================================================
	        //Observacion de la central de camilleros
	        echo "<td align=left><textarea ID='wobscc[".$i."]' rows=3 cols=30 $ald_text_area onkeydown='detener()' onkeyup='doTimer(30)' onChange='grabarObservaciononchange( $wid, this)'>".$row[11]."</textarea></td>";
                             // Habitacion
			echo "<td align=center><font size=1><INPUT TYPE=CHECKBOX onClick='grabarObservacion( $wid, $i)' onmouseover='reactivar($wtiempo_refresh)' >Grabar Observ.</font></td>";
            echo "<td align=center>".$wid."</td>";

			echo "</tr>";
	       } //FIN DEL FOR

	   echo "<tr></tr>";
	   echo "<tr></tr>";
	   echo "<tr></tr>";
	   echo "<tr><td align=center colspan=13><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";

	   echo "</BIG>";
	   echo "</center></table>";
	   echo "<HR align=center></hr>";  //Linea horizontal

	   echo "<table border=1 align=right>";
	   echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
	   echo "<tr><td colspan=3 bgcolor="."CCCCFF"."><font size=2 color='"."000000"."'>&nbsp Mas de cinco (5) minutos</font></td></tr>";      //Lila
	   echo "<tr><td colspan=3 bgcolor="."FFFF66"."><font size=2 color='"."000000"."'>&nbsp De 2.5 a 5 minutos</font></td></tr>";            //Amarillo
	   echo "<tr><td colspan=3 bgcolor="."99FFCC"."><font size=2 color='"."000000"."'>&nbsp Menos de 2.5 minutos</font></td></tr>";          //Verde
	   echo "<input type='HIDDEN' id= 'recargar' name=recargar onfocus='reactivar($wtiempo_refresh)'>";
	   echo "</table>";

	   echo "</form>";

	   //echo "<meta http-equiv='refresh' content='".$wtiempo_refresh.";url=central_de_camilleros_AJAX.php?wemp_pmla=".$wemp_pmla."&wcentral=".$wcentral."'>";
	  }
     else
       {
	    echo "<br><br>";
        echo "<center><table>";
        echo "<tr><td align=center bgcolor=#fffffff><font size=5 text color=#CC0000><b>LA CENTRAL ESTA INACTIVA</b></font></td></tr>";
        echo "</table>";
       }
}

echo "<input type='HIDDEN' id= 'wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='HIDDEN' id= 'recargar'>";
echo "<input type='HIDDEN' id= 'wcentral' value='".$wcentral."'>";
echo "<input type='HIDDEN' id= 'wbasedato' value='".$wbasedato."'>";
echo "<script>

	c=document.getElementById('wtiempo_refresh').value;
	var t;
	var timer_is_on=0;

	function timedCount(s)
	{
	document.getElementById('wtiempo_refresh').innerHTML=c;
	c=c-1;
	t=setTimeout('timedCount()',1000);
	}

	</script>";
include_once("free.php");
}
?>
