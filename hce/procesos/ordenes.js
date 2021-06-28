/* JAVASCRIPT ORDENES PARA HCE
 *
 * MODIFICACIONES
 *
 * Mayo 4 de 2020		Edwin		Se hacen cambios varios para la interoperabilidad con laboratorio por centro de costos y POCT
 *									En la modal de al seleccionar el tipo de muestra(Sitio anatomico y tipo de muestra) se muestra la opcion Seleccione
 *									y debe haber por lo menos un sitio anatómico y tipo de muestra seleccionados
 * Enero 22 de 2020		Edwin MG	Una vez guardado las ordenes después de firmar se deja todos los examenes como viejos
 * Octubre 30 de 2019	Edwin 		Se hacen modificaciones varias para interoperabilidad con ordenes (LABORATORIO, HIRUKO)
 * Junio 5 de 2019		Edwin MG	Se valida el tiempo de recarga de la mensajería kardex
 * Noviembre 15 de 2018	Edwin 		Se agrega validación de alergias de medicamentos al prescribir los medicamentos, es decir, 
 *									si el paciente tiene una alergia activa para el principio activo no debe permitir ordenarlo.
 * Julio 30 de 2018		Edwin 		Todos los ajax.responseText se le agrega un trim
 * Julio 9 de 2018 		Edwin 		Se busca los articulos anteriores por ajax para que la carga inicial sea más liviana
 * Julio 3 de 2018 		Edwin 		Al dar clic sobre la pestaña auditoría se consulta la auditoría correspondiente para le paciente
 * Febrero 13 de 2018 	Edwin 		Los medicamentos ordenados desde una ayuda diagnóstica o por protocolo comienzan desde la ronda actual
 * Febrero 8 de 2018 	Edwin 		No se tienen en cuenta la frecuencia para cambiar la fecha y hora de inicio del medicamento
 * Noviembre 28 de 2017 Jessica 	Se envía el usuario como parámetro al ajax 37 ya que la función consultarProtocolo() de 
 *									ordenes.inc.php quedaba sin este.
 * Noviembre 27 de 2017 Jessica 	Se agrega el numero de la pestaña como parámetro a la funcion mostrar_mensaje() para que guarde 
 *									en el campo oculto pestanasVistas cuando haga clic en las pestañas y así enviarlo en las funciones 
 *									salir_sin_grabar() y grabarKardex().
 * Agosto 14 de 2017 Jessica Para los ctc de responsables contributivos, al cerrar la modal de las prescripciones en mipres
 *							 se consume el web service del ministerio, se relaciona el consecutivo en movhos_000134 o movhos_000135
 *							 y se guarda la prescripcion en las tablas del grupo mipres.
 * Abril 27 de 2017 		Se muestra el formato de control a los médicos una vez grabe la orden.
 * Marzo 7 de 2017 			Para los CTC de responsables contributivos se modifica el tamaño del iframe del ministerio para que no se salga de la modal cuando la pagina tiene zoom.
 * Diciembre 5 de 2016		Se agrega validacion para abrir y guardar los CTC de responsables contributivos al grabar la orden
 * Septiembre 20 de 2016	Se posiciona en la primera posicion del select de procedimientos cuando despues de haber seleccionado un tipo de orden agrupada
 * Septiembre 19 de 2016	No se deja usar el programa de ordenes si el browser del usuario es Mozilla y su version es inferior a la registrada en el parametro versionMozilla
 * Septiembre 13 de 2016	Se hace ajax para verificar que los articulos No Pos tienen CTC
 * Septiembre 07 de 2016	El filtro de profilaxis no se valida si el medicamento ya ha sido grabado previamente
 * Agosto 23 de 2016	Se valdida los eventos change del articulo perteneciente al dextrometer para que no guarde sin necesidad cambios en la auditoria del articulo. 
 * Agosto 05 de 2016	Se corrige error para que no se grabe las observaciones del dextrometer sin necesidad al grabar las ordenes por una enfermera. 
 * Agosto 3 de 2016 	Se modifica la modal de prescripción de NPT para que solo se permita ordenar NPT seguras teniendo en cuenta ciertas condiciones.
 * Junio 29 de 2016		Se corrige para que las observaciones del dextrometer no se guarden cada vez que se entre a ordenes a menos que hallan sido cambiadas. Para ello se agrega 
 *						un campo que dice cual era las observaciones iniciales del dextrometer en la función inicializarJquery.
 * Junio 16 de 2016		Al cambiar la frecuencia de dosis unica se borra la dosis máxima 
 * Junio 10 de 2016		Se corrige horario especial que guarda 24:00:00 en vez de 00:00:00 
 * Mayo 20 de 2016		Los articulos pueden tener configurado en Base de datos días de tratamiento por defecto y dosis máxima (movhos_000059). Se corrige error
 *						en que tenía prevalencia la configuración por defecto de los medicamentos y no lo ordenado por el medico.
 * Marzo 23 de 2016		- Se añade la funcionalidad de procedimientos agrupados, modal que permite seleccionar varios procedimientos a la vez, las acciones
 *						que toma la orden general deben aplicarse para cada uno de los procedimientos que internamente continuan funcionando como siempre.
 * Mayo 16 de 2016 		Se añade la funcionalidad de procedimientos agrupados, modal que permite seleccionar varios procedimientos a la vez, 
 *						las acciones que toma la orden general deben aplicarse para cada uno de los procedimientos que internamente continuan 
 *						funcionando como siempre.
 *						Por cada procedimiento se pueden adicionar medicamentos y son obligatorios de acuerdo a la configuración de cada procedimiento.
 *						Cuando se cambia el estado a realizado, pendiente de resultado o cancelado se suspenden los medicamentos asociados.
 *						Si uno de los medicamentos tiene como minimo una aplicación el procedimiento no podrá ser cancelado.
 * Mayo 13 de 2016		Se corrige error cuando no se llena ctc de medicamentos y se ordena nuevamente el medicamento no pedía ctc.
 * Mayo 10 de 2016		Al cargar un protocolo (funcion eleccionMedicamento), si un procedimiento no trae información por ajax no se muestra en la orden.
 * Mayo 5 de 2016		Se cambia el campo oculto entidad_responsable por el nit de la empresa(cliame_000024) para evitar hacer split por guión que puede generar errores.
 *						Se agregan las descripciones de medicamentos y procedimientos cuando se pide el ctc por cambio de responsable.
 * Febrero 29 de 2016	Cuando un procedimiento tiene CTC y en el formulario se le daba salir sin guardar, no se cerraba el ctc, esto se corrige.
 * Febrero 12 de 2016	Se corrigen errores varios al momento de quitar y actualizar un dextrometer.
 * Enero 21 de 2015		Se agregan funciones para mostrar los ctc de medicamentos y procedimientos con cambio de responsable, se muestra el ctc una vez se firma la orden y se
 *						hace clic en grabar la orden
 * Diciembre 04 de 2015	Para los insumos de infusiones se revisa que tenga la via de administración que trae por defecto el articulo genérico o si no se deja por defecto la vía
 *						la primera vía que tenga el medicamento.
 * Noviembre 11 de 2015	Si en el buscador de medicamentos se cambia la unidad de medida o de presentación no se borra la dosis ingresada, el cambio se encuentra en
 *						la función filtrarMedicamentosPorCampo.
 * Octubre 04 de 2015	Se inicializa variale conctc que estaba causando un error cuando se elegía un medicamento NO POS en horario especial
 * Junio 02 de 2015	Si se cambia la frecuencia de un medicamento de días anteriores, se actualiza la fecha y hora de inicio como corresponde.
 * Mayo 15 de 2015 Se repara la eliminacion de varios examenes al mismo tiempo.
 * Abril 15 de 2014	Se agrega validación para cuando la pestaña de altas está inactiva
 * Marzo 25 de 2015	Se valida que no se permite poner 0 en días de tratamiento ni en dosis máxima
 * Mayo 5 de 2011	Al agregar un examen o procedimiento, todos los examenes son colapsados
 */

var preguntarPorVisaulizarMedControl = false;
 
var cadenaCTCcontributivo = "" ;
 
//contiene una cadena con todos los articulos que deberían tener CTC después de grabar los articulos
var artsGrabadosCTC = "";

var insumosNPT = {}; 
 
var cadenaMensajeProcAgrupados = "";
var contprocedimientosAgrupados = 0;
var procAgrupados = {};
var medicamentos = {};

articuloPorDextrometer = false;
agregandoArticuloPorDextrometer = false;

var guardarCadenaGrabadosSinCTC = '';
var guardarCadenaExamenesGrabadosSinCTC = '';
var cadenaCTCGuardados = '';
var alertsIniciales = [];

var justificacionUltimoExamenAgregado = '';

var artLevs = {};	//objeto que mantiene los datos de un LEV

var cantidadAnterior = new Array();
var cantidadVolDilAnterior = new Array();
var valorFrecSolAnterior = new Array();
var valorEsDispensable = new Array();
var codigoAnterior = new Array();

var esLiquidoEndovenoso = false;

var arCTCArticulos = {};
var arCTCprocedimientos = {};

var datosFinales = [];

var stickers_ga = [];

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
            changeYear: true,
            changeMonth: true,
            yearRange: '-100:+0'
        };

$.datepicker.setDefaults($.datepicker.regional['esp']);

consultarAuditoria = true;






	
	//datos adicionales
	mad = 	function( options ){
		
				var title 	= options.title || '';
				var fnAccpet= options.accept || null;
				var fnClose = options.close || null;
				var data 	= options.data || {};
				var __self 	= this;
				
				var nroMuestras = data.nroMuestras;
				
				try{
					delete( data.nroMuestras );
				}
				catch(e){}
		
				var obj = $( "<div class='mad'>"
							 +"	<div class='mad-section-title'>"
							 +"		<div class='mad-title'>"+title+"</div>"
							 +"		<span class='mad-title-close mad-close' style='display:none;'></span>"
							 +"	</div>"
							 +"	<div><span class='mad-add'>Agregar</span></div>"
							 +"	<div class='mad-content'></div>"
							 +"	<div class='mad-actions'>"
							 +"		<span class='mad-accept'>Aceptar</span>"
							 +"		<span class='mad-close'>Cerrar</span>"
							 +"	</div>"
							 +"</div>" );
				
				this.createItem = function( title, data ){
					
					var hayDependencia = [];
					
					var item  =  $( "<div class='mad-row'>"
									+"	<div class='mad-cell-3 mad-item-title'>"
									+"		<label>"+title+"</label>"
									+"	</div>"
									+"	<div class='mad-cell-2 mad-item'>"
									+"		<select data-select='"+title+"'></select>"
									+"	</div>"
									+"</div>" );
									
					var iselect = $( "select", item );
								 	
					for( var x in data ){
						
						$( iselect ).append( "<option value='"+data[x].codigo+"'>"+data[x].descripcion+"</option>" );
						
						for( var y in data[x] ){
							if( y != 'codigo' && y != 'descripcion' ){
								
								if( $( "select[data-select='"+y+"']", item ).length == 0 ){
									var idp = this.createItem( y, null );
									item.append( $( "div", idp ) );
									idp.remove();
								}
								
								hayDependencia[x] = { data: data[x][y], item: $( "select[data-select='"+y+"']", item ) };
							}
						}
					}
					
					
					iselect.change(function(){
						var slOption = this.selectedIndex;

						if( hayDependencia[slOption] ){
							
							var sl = hayDependencia[slOption].item;
							var options = hayDependencia[slOption].data;
							
							$( "option", sl ).remove();
							
							$( options ).each(function(){
								$( sl ).append( "<option value='"+this.codigo+"'>"+this.descripcion+"</option>" );
							})
							
							if( $( "option", sl ).length > 1 ){
								sl[0].selectedIndex = -1;
							}
						}
						
						__self.habiltaButtonAccept();
					});
					
					if( $( "option", iselect ).length > 1 ){
						iselect[0].selectedIndex = 0;
						$( iselect ).change();
					}
					else{
						$( iselect ).change();
					}
									
					$( ".mad-content", obj ).prepend( item );
					
					return item;
				};
				
				this.create = function(){
					
					$( ".mad-close", obj ).click(function(){
						
						$(this).addClass( "mad-disabled" );
						$( ".mad-accept", obj ).addClass( "mad-disabled" );
						
						if( fnClose )
							fnClose();
						
						obj.remove();
					});
					
					//Creo el boton de aceptar
					$( ".mad-accept", obj ).click(function(){
						
						if( !$(this).hasClass( "mad-disabled" ) ){
							
							$(this).addClass( "mad-disabled" );
							$( ".mad-close", obj ).addClass( "mad-disabled" );
							
							if( fnAccpet )
								fnAccpet( __self );
							
							obj.remove();
						}
					});
					
					//Creo el boton agregar
					$( ".mad-add", obj ).click(function(){
						
						for( var x in data ){
							var item = __self.createItem( x, data[x] );
							
							//Se comenta botones de eliminar por que se debe pedir siempre el total de muestras
							// var btnEliminar = $( "<div class='mad-remove'><span>Eliminar</span><div>" );
					
							// $( "span", btnEliminar ).click(function(){
								// item.remove();
								// __self.habiltaButtonAccept();
							// })
							
							// item.append( btnEliminar );
						}
						
						__self.habiltaButtonAccept();
					});
					
					for( var x in data ){
						var item = this.createItem( x, data[x] );
						
						//Se comenta botones de eliminar por que se debe pedir siempre el total de muestras
						// var btnEliminar = $( "<div><span class='mad-remove'>Eliminar</span><div>" );
					
						// $( "span", btnEliminar ).click(function(){
							// item.remove();
							// __self.habiltaButtonAccept();
						// })
						
						// item.append( btnEliminar );
					}
					
					//Agrego tantos items como indique el nro de muestras
					for( var i = 0; i < nroMuestras-1; i++ ){
						$( ".mad-add", obj ).click();
					}
					
					//No se permite agregar muestras por el usuario
					//Por tal motivo no muestro el boton agregar
					$( ".mad-add", obj ).css({display:'none'});
					
					this.habiltaButtonAccept();
				}
				
				this.habiltaButtonAccept = function(){
					
					var habilita = false;
					
					if( $( ".mad-content .mad-row", obj ).length > 0 ){
						
						$( ".mad-content .mad-row", obj ).each(function(){
							
							var total = $( "select", this ).length;
							
							$( "select", this ).each(function(){
								if( !( $( this ).val() == '' || $( this ).val() == null || !$( this ).val() ) )
									total--;
							});
							
							habilita = total == 0;
							
							if( habilita ) 
								return false;
						});
					}
					
					if( habilita ){
						$( ".mad-accept", obj ).removeClass("mad-disabled")
					}
					else{
						$( ".mad-accept", obj ).addClass("mad-disabled");
					}
				}
				
				this.result = function(){
					var b = [];
					$( ".mad-content .mad-row", obj ).each(function(){
						
						var a = $( "select", this );
						
						var add = a.length;
						
						$( a ).each(function(){
							if( !( $( this ).val() == '' || $( this ).val() == null || !$( this ).val() ) )
								add--;
						});
						
						if( add == 0 ){
							
							if( a.length > 0 ){
								c = {};
								$( a ).each(function(){
									var d = $( this ).data( "select" )
									c[d] = {}
									c[d].codigo = $( this ).val()
									c[d].descripcion = $( "option:selected", this ).html();
									
								})
								b.push(c);
							}
						}
					});
					
					return b;
				}
				
				this.obj = obj;
				
				return this;
			}
function marcarCambioTomaMuestra( indice, contexamen, realizaUnidad, imp ){
	
	marcarCambio( indice, contexamen );
	
	if( realizaUnidad && $( "#wusuariotomamuestra"+contexamen )[0].checked ){
		
		imprimirSticker( contexamen, realizaUnidad, imp );
	}
}

function imprimirSticker( contexamen, realizaUnidad, imp ){
	
	jConfirm( "Desea imprimir el sticker para el estudio <b>" + $( "#wnmexamen"+contexamen ).val() + "</b>?", "ALERTA", function( resp ){
		if( resp ){
			
			if( !imp ){
				
				stickers_ga.push({
					cuentaExamenes	:	contexamen,
					wnmexamen		:	contexamen,
					hexcco			:	contexamen,
					hexcod			:	contexamen,
				});
			}
			else{
				$.post("../reportes/HCE_Sticker_GA.php",
					{
						consultaAjax: '',
						whis		: $("#whistoria").val(),
						wing		: $("#wingreso").val(),
						wip			: $("#wipimpresoraga").val(),
						wtor		: $("#hexcco"+contexamen ).val(),
						wnor		: $("#hexcons"+contexamen ).val(),
						witem		: $("#hexnroitem"+contexamen ).val(),
					}, 
					function(data){},
				);
			}
		}
		
		$.alerts.okButton 		= "Aceptar";
		$.alerts.cancelButton 	= "Cancelar";
	});
}

function marcarLeidoEstudioCancelado( cmp, url ){
	
	//Solo se hace si tiene el atributo plecancelado
	if( $(cmp).is("[plecancelado]") || $(cmp).is("[ple]") ){
		
		if( $(cmp).is("[plecancelado]") )
			var atr = $( cmp ).attr("plecancelado").split("-");
		else
			var atr = $( cmp ).attr("ple").split("-");
		
		var tipoOrden 	= atr[0];
		var nroOrden 	= atr[1];
		var item 		= atr[2];
		
		$.post("ordenes.inc.php",
			{
				consultaAjax		: '',
				consultaAjaxKardex	: 'actualizarEstudioCanceladoLeido',
				wbasedato			: $("#wbasedato").val(),
				wemp_pmla			: $("#wemp_pmla").val(),
				wusuario			: $("#usuario").val(),
				tipoOrden			: tipoOrden,
				nroOrden			: nroOrden,
				item				: item,
			}, 
			function(data){

				if( $.trim( data ) == 1 ){
					
					$( cmp ).removeClass("blink")
					$( cmp ).removeAttr("plecancelado");
					$( cmp ).removeAttr("ple");
				}
				
				consultarOrdenesPendientesDeVer();
			}
		);
	}
	
	// if( url )
		// mostrarModalResultadosEstudios( url );
}

function consultarOrdenesPendientesDeVer(){
	
	var ar = [];
	var total = 0;
	
	//Busco las ordenes realizas pendientes de ver (pvr pendientes de ver resultados)
	$( "[ple]", $( "#detOrdenesRealizadas" ) ).each(function(){
		
		//this es el elemento que tiene el atributo ple (pendiente de lectura)
		if( $.inArray( $( this ).attr("ple"), ar ) < 0 ){
			total++;
			ar.push( $( this ).attr("ple") );
		}
	});
	
	if( total > 0 ){
		//Pendiente de ver resultado
		//Es el id del span que se encuentra en ordenes realizadas
		$( "#pvr" )
			.html( total )
			.css({
					backgroundColor	: "green",
					color			: "white",
					display			: 'inline-flex',
					width			: '20px',
					height			: '20px',
					justifyContent	: 'center',
					borderRadius	: '50%',
					alignItems		: 'center',
					fontSize		: '7pt',
				})
			.addClass("blink");
	}
	else{
		$( "#pvr" )
			.html( "" )
			.css({
				display:"none",
			});
	}
	
	
	
	
	ar = [];
	total = 0;
	
	//Busco las ordenes realizas pendientes de ver (pvr pendientes de ver resultados)
	total = $( "[plecancelado]", $( "#detOrdenesRealizadas" ) ).length;
	
	if( total > 0 ){
		//Pendiente de ver resultado
		//Es el id del span que se encuentra en ordenes realizadas
		$( "#pvc" )
			.html( total )
			.css({
					backgroundColor	: "red",
					color			: "white",
					display			: 'inline-flex',
					width			: '20px',
					height			: '20px',
					justifyContent	: 'center',
					borderRadius	: '50%',
					alignItems		: 'center',
					fontSize		: '7pt',
				})
			.addClass("blink");
	}
	else{
		$( "#pvc" )
			.html( "" )
			.css({
				display:"none",
			});
	}
}

var OrdenAnexa = "";
function anexarOrden( cmp, tipoOrden, nroOrden ){
	
	var accion = $( "span", cmp ).html();
	
	if( accion == "Anexar" ){
		
		OrdenAnexa = nroOrden;
		
		$( ".anexar-orden" ).each(function(){
			$( "span", this ).html("Anexar");
		});
		
		$( "span", cmp ).html("Anexando Orden");
		
		$("#wselTipoServicio" ).val(tipoOrden);
		
		$("#wselTipoServicio" ).attr({disabled:true});
		$("#wprotocolo_ayd" ).attr({disabled:true});
		
		$( "#wnomproc" ).focus();
		
		//Si el buscador de examenes no se ve en pantalla, reposiciono el scroll para que se vea
		var crect 	= $( "#wnomproc" ).parent().parent().parent()[0].getBoundingClientRect();
		
		//Esto indica que no es visible el buscador de examenes o procedimientos
		if( crect.top < 0 ){
			
			//Esto es el valor actual del scroll
			var vscroll = $( "body" ).scrollTop();
			
			//Reposicionando el scroll
			$( "body" ).scrollTop( vscroll + crect.top-20 );
		}
		
		setTimeout( function(){ $( "b", $( "#wnomproc" ).parent().parent().parent() ).css({"font-size":"20pt"}).parent().addClass("fondorojo") }, 400 );
		setTimeout( function(){ $( "b", $( "#wnomproc" ).parent().parent().parent() ).css({"font-size":"10pt"}).parent().removeClass("fondorojo") }, 800 );
		setTimeout( function(){ $( "b", $( "#wnomproc" ).parent().parent().parent() ).css({"font-size":"20pt"}).parent().addClass("fondorojo") }, 1200 );
		setTimeout( function(){ $( "b", $( "#wnomproc" ).parent().parent().parent() ).css({"font-size":"10pt"}).parent().removeClass("fondorojo") }, 1600 );
		setTimeout( function(){ $( "b", $( "#wnomproc" ).parent().parent().parent() ).css({"font-size":"20pt"}).parent().addClass("fondorojo") }, 2000 );
		setTimeout( function(){ $( "b", $( "#wnomproc" ).parent().parent().parent() ).css({"font-size":"10pt"}).parent().removeClass("fondorojo") }, 2400 );
		// setTimeout( function(){ $( "b", $( "#wnomproc" ).parent().parent().parent() ).css({"font-size":"20pt"}).parent().addClass("fondorojo") }, 2800 );
		// setTimeout( function(){ $( "b", $( "#wnomproc" ).parent().parent().parent() ).css({"font-size":"10pt"}).parent().removeClass("fondorojo") }, 3200 );
		
	}
	else{
		
		OrdenAnexa = "";
		
		$( ".anexar-orden" ).each(function(){
			$( "span", this ).html("Anexar");
		});
		
		$( "span", cmp ).html("Anexar");
		
		$("#wselTipoServicio" ).val('');
		
		$("#wselTipoServicio" ).attr({disabled:false});
		$("#wprotocolo_ayd" ).attr({disabled:false});
	}
	
}





















function mostrarAuditoria(){
	
	if( consultarAuditoria ){
		
		$( "#fragment-7" ).block({ message: "<b style='font-size:20pt;'>Por favor espere...<br>Cargando datos...</b>" });
		
		try{
			
			$.post("ordenes.inc.php",
				{
					consultaAjax		: '',
					consultaAjaxKardex	: 'consultarAuditoria',
					wbasedato			: $("#wbasedato").val(),
					wemp_pmla			: $("#wemp_pmla").val(),
					whistoria			: $("#whistoria").val(),
					wingreso			: $("#wingreso").val(),
					// fechaKardex			: $('#wfechagrabacion').val(),
				}, 
				function(data){
					
					try{
						$( "#fragment-7" ).html( data.html );
						
						consultarAuditoria = false;
						
						$( "#fragment-7" ).unblock();
					}
					catch(e){
						$( "#fragment-7" ).unblock();
					}
				},
				"json"
			);
		}
		catch(e){
			$( "#fragment-7" ).unblock()
		}
	}
	
}

/*****************************************************************************************************************************
 * CTC Contributivo - Pagina del ministerio
 ******************************************************************************************************************************/

function mostrarMedicamentoControlAImprimir( index ){	

	if( !index )
		index = null;
	
	$.post("ordenes.inc.php",
		{
			consultaAjax		: '',
			consultaAjaxKardex	: '83',
			wbasedato			: $("#wbasedato").val(),
			wcenmez				: $("#wcenmez").val(),
			wemp_pmla			: $("#wemp_pmla").val(),
			whistoria			: $("#whistoria").val(),
			wingreso			: $("#wingreso").val(),
			fechaKardex			: $('#wfechagrabacion').val(),
		}, 
		function(data){
			
			var dvControl 	= $( "#dvModalMedControl" );
			var dvMeds 		= $( "#dvMedControl", dvControl );
			var dvImpresion	= $( "#dvImpresionMedControl", dvControl );
			
			var itemSelected = 0;
			
			preguntarPorVisaulizarMedControl = false;
			
			//Borro todo el contenido principal
			dvMeds.html( '' )
			dvImpresion.html('');

			//Data trae el contenido necesario para crear la info
			//Creo un span por cada medicamento en el div dvMeds
			$( data ).each(function(x){
				
				var url 	= "../../movhos/procesos/impresionMedicamentosControl.php?wemp_pmla="+$("#wemp_pmla").val()+"&imprimir=on&historia="+$("#whistoria").val()+"&id_registro="+this.id+"&editable=off"
				
				//Creo un span que sera agregado al formulario
				var newSpan = $( "<span>"+this.codigo+"-"+this.nombreGenerico+"</span>" );
				dvMeds.append( newSpan );
				
				if( index )
					if( $( "#widoriginal"+index ).val() == this.identificador )
						itemSelected = x;
				
				//Agrego funcion al span
				newSpan.click(function(){
					
						//Ojo. este this se refiere al span(newSpan)
						if( !$( this ).hasClass( "selected" ) ){
							
							dvImpresion.html('');
							$( "span", dvMeds ).removeClass( "selected" );
							$( this ).addClass( 'selected' );
							
							//Creo un iframe que mostrará la información
							dvImpresion.append( "<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>" );
						}
					})
					.css({ 
						cursor: "pointer",
					});
			});
			
			
			$( "span", dvMeds ).eq( itemSelected ).click();
			
			
			var canWidth = $(window).width()*0.8;
			if( $( "#dvModalMedControl" ).width()-50 < canWidth )
				canWidth = $( "#dvModalMedControl" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvModalMedControl" ).height()-50 < canHeight )
				canHeight = $( "#dvModalMedControl" ).height();

			$.blockUI({ 
				message	: $( "#dvModalMedControl" ) ,
				css		: { 
					left	: ( $(window).width() - canWidth-50 )/2  +'px',
					top		: ( $(window).height() - canHeight -50 )/2 +'px',
					width	: canWidth+ 25 + 'px',
					height	: canHeight+ 25 + 'px',
					cursor	: "point",
					overflow: "auto",
				 }
			});		
		},
		"json"
	);
	
}
 
function mostrarCTCcontributivo()
{
	$.post("ordenes.inc.php",
	{
		consultaAjax:   		'',
		consultaAjaxKardex:   	'81',
		wemp_pmla:     			$("#wemp_pmla").val(),
		whistoria:     			$("#whistoria").val(),
		wingreso:     			$("#wingreso").val(),
		codMedico:     			$("#usuario").val(),
		cadenaCTCcontributivo:  cadenaCTCcontributivo
		
	}, function(respuesta){

		$.blockUI({ message: respuesta ,
		css: {
			cursor	: 'auto',
			width	: "80%",
			height	: "95%",
			left	: "10%",
			top		: '15px',
		} });
		
		
		widthDivModal = $("#divCTCcontributivo").width();
		heightDivModal = $("#divCTCcontributivo").height();
		
		var valorConstanteWidth = 6;
		var valorConstanteHeight = 35;
		
		
		
		$("#iframeCTCcontributivo").width(widthDivModal-valorConstanteWidth);
		
		posicionInicialIframe = $("#iframeCTCcontributivo").position().top;
		posicionFinalIframe = heightDivModal-(posicionInicialIframe+valorConstanteHeight);
		
		$("#iframeCTCcontributivo").height(posicionFinalIframe);
		
		
		function blinkFondoAmarilloCTCcontributivo()
		{
			$("#fondoAmarilloCTCcontributivo").fadeTo(250, 0.1).fadeTo(250, 1.0);
		}
		
		setInterval(blinkFondoAmarilloCTCcontributivo, 1000);
		
	});
	
	cadenaCTCcontributivo = "";
	
}

function consultarConsecutivoMipres(fechaMipres,horaMipres,fechaDiaSiguiente,horaDiaSiguiente)
{
	var historia = $("#whistoria").val();
	var ingreso = $("#wingreso").val();
	var tipoDocumento = $("#wtipodoc").val();
	var documento = $("#wcedula").val();
	
	var consecutivoMipres = "";
	
	$.blockUI({ message: $('#msjEspere') });
	$.ajax({
		url: "CTCmipres.php",
		type: "POST",
		dataType: "json",
		data:{
			consultaAjax 	: '',
			accion			: 'consultarPrescripcionPacFec',
			wemp_pmla		: $('#wemp_pmla').val(),
			historia		: historia,
			ingreso			: ingreso,
			tipoDocumento	: tipoDocumento,
			documento		: documento,
			fechaMipres		: fechaMipres,
			hora			: horaMipres,
			fechaDiaSig		: fechaDiaSiguiente,
			horaDiaSig		: horaDiaSiguiente,
			general			: "off",
			origen			: "ordenes"
			},
			async: false,
			success:function(respuesta) {
				
				consecutivoMipres = respuesta[0];
				$.unblockUI();
			}
			
			
	});
	
	return consecutivoMipres;
}


function cerrarDivIframeCTCcontributivo(cadenaCTCcontributivos)
{
	$.unblockUI();
	
	consecutivoMipres = consultarConsecutivoMipres($('#wfecha').val(),$('#whora').val(),$('#wdiaSiguiente').val(),"00:00:00");
		
	$.post("ordenes.inc.php",
	{
		consultaAjax:   		'',
		consultaAjaxKardex:   	'85',
		wemp_pmla:     			$("#wemp_pmla").val(),
		whistoria:     			$("#whistoria").val(),
		wingreso:     			$("#wingreso").val(),
		codMedico:     			$("#usuario").val(),
		cadenaCTCcontributivo:  cadenaCTCcontributivos,
		consecutivoMipres:		consecutivoMipres
		
	}, function(respuesta){
		
		
		
	});
	
	
	// $.unblockUI();
}	

/*****************************************************************************************************************************
 * Validar entrada decimal (solo un punto)
 ******************************************************************************************************************************/
function validarEntradaDecimalSoloUnPuntoNPT(id,e) { 

	// Punto = 46
	var key = (document.all) ? e.keyCode : e.which; 
	cadena=$(id).val();
	
	//13: Enter
	// 9: Tab
	//40: Flecha abajo
	
	if(e.keyCode == 13 || e.keyCode == 9 || e.keyCode == 40)
	{
		// Obtenemos el número del atributo tabindex al que se le dio enter y le sumamos 1
		var TabIndexActual = $(id).attr('tabIndex');
		var TabIndexSiguiente = parseInt(TabIndexActual) + 1;
		
		// Se determina si el tabindex existe en el formulario
		var CampoSiguiente = $('[tabindex='+TabIndexSiguiente+']');
		
		// Si se encuentra el campo entra al if
		if(CampoSiguiente.length > 0)
		{
			CampoSiguiente.focus(); //Focus al campo encontrado
			CampoSiguiente.select(); //Seleccionar contenido
			return false; // retornamos false para detener alguna otra ejecucion en el campo
		}
		else
		{
			// Si no se encontro ningún elemento, se retorna false
			return false;
		}
		
	}
	
	//38: Flecha arriba
	if(e.keyCode == 38)
	{
		// Obtenemos el número del atributo tabindex al que se le dio enter y le sumamos 1
		var TabIndexActual = $(id).attr('tabIndex');
		var TabIndexAnterior = parseInt(TabIndexActual) - 1;
		
		// Se determina si el tabindex existe en el formulario
		var CampoAnterior = $('[tabindex='+TabIndexAnterior+']');
		
		// Si se encuentra el campo
		if(CampoAnterior.length > 0)
		{
			CampoAnterior.focus(); //Focus al campo encontrado
			CampoAnterior.select(); //Seleccionar contenido
			return false; // retornamos false para detener alguna otra ejecucion en el campo
		}
		else
		{// Si no se encontro ningún elemento, se retorna false
			return false;
		}
		
	}
		
	if(cadena.indexOf('.')==-1)
	{
		return (key <= 13 || (key >= 48 && key <= 57) || key == 46);
	}
	else
	{
		return (key <= 13 || (key >= 48 && key <= 57));
	}
	
}


/********************************************************************************
 * NUTRICIONES
 ********************************************************************************/

function autoNPT( fam, cmp, tipo ){
	$("#wnombrefamilia").val( fam );
	$("#wnombrefamilia").search( $("#wnombrefamilia").val() );

	cmp.checked = !cmp.checked;
}
 
function cerrarModalNPT(){
	
	$.unblockUI();
}
 
function abrirModalNPT(historia,ingreso,articulo,ido,realizada,codigoReal,peso,tiempoInfusion,purga,volumenTotal,observaciones,reemplazada)
{
	$.post("ordenes.inc.php",
	{
		consultaAjax:   		'',
		consultaAjaxKardex:   	'77',
		wemp_pmla:     			$("#wemp_pmla").val(),
		wcenmez:     			$("#wcenmez").val(),
		historia:				historia,
		ingreso: 				ingreso,
		articulo: 				articulo,
		ido: 					ido,
		realizada: 				realizada,
		codigoReal: 			codigoReal,
		peso: 					peso,
		tiempoInfusion: 		tiempoInfusion,
		purga: 					purga,
		volumenTotal: 			volumenTotal,
		observaciones: 			observaciones,
		reemplazada: 			reemplazada
		
	}, function(respuesta){

		$( "#dvAuxModalNutriciones" ).html( respuesta );
		
		$("input").blur(function(){
			$("#NPT_PESO").focus();
		});
		
		recalcularVolumenyPurgaModalNPT();
		
		var cadenaTooltipFormulas = $("#tooltipFormulas").val();
		
		cadenaTooltipFormulas = cadenaTooltipFormulas.split("|");
		
		for(var i = 0; i < cadenaTooltipFormulas.length-1;i++)
		{
			$( "#"+cadenaTooltipFormulas[i] ).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
		}
				
		var canWidth = $(window).width()*0.8;
		if( $( "#dvAuxModalNutriciones" ).width()-50 < canWidth )
			canWidth = $( "#dvAuxProcedimientosAgrupados" ).width();

		var canHeight = $(window).height()*0.8;;
		if( $( "#dvAuxModalNutriciones" ).height()-50 < canHeight )
			canHeight = $( "#dvAuxModalNutriciones" ).height();

	
		$.blockUI({ message: $('#modalNutriciones'),
		css: {
			overflow: 'auto',
			cursor	: 'auto',
			width	: "60%",
			height	: "80%",
			left	: "20%",
			top		: '100px',
		} });
	});
}

function cargarNutricionAnterior(historia,ingreso,articulo,ido,peso,tiempoInfusion,purga,volumenTotal,observaciones){
	
	volumenTotal = parseFloat(volumenTotal);
	
	$("#NPT_PESO").val(peso);
	$("#NPT_TIEMPOINFUSION").val(tiempoInfusion);
	$("#NPT_PURGA").val(purga);
	$("#NPT_VT").val(volumenTotal.toFixed(2));
	$("#NPT_Observaciones").val(observaciones);
	
	$.ajax({
		url: "ordenes.inc.php",
		type: "POST",
		dataType: "json",
		data:{
			consultaAjax:   		'',
			consultaAjaxKardex:   	'76',
			wemp_pmla:     			$("#wemp_pmla").val(),
			basedatos:     			$("#wbasedato").val(),
			historia:				historia,
			ingreso: 				ingreso,
			articulo: 				articulo,
			ido: 					ido
			},
			async: false,
			success:function(respuesta) {

				if(respuesta.error == 0)
				{
					arrayPrescripciones = respuesta.arrayPrescripciones;
					
					for( var x in arrayPrescripciones ){
						prescripcion = parseFloat(arrayPrescripciones[x].Prescripcion);
						$('#NPT_PRESCRIPCION'+arrayPrescripciones[x].Orden).val(prescripcion.toFixed(2));
					}
					
					recalcularVolumenyPurgaModalNPT();
				}
				else
				{
					jAlert(respuesta.mensError,"ALERTA")
				}
				
			}
	});
	
	
}

function grabarInsumosNPT(){
	
	var arrayIdos = [];
	for( var x in insumosNPT ){
		
		var articulo = $("#wnmmed"+x).html();
		codArticulo = articulo.split("-");
		
		insumosNPT[x].articulo = codArticulo[0];
		insumosNPT[x].ido=$('#widoriginal'+x).val();
		insumosNPT[x].observaciones = $('#wtxtobs'+x).val();
				
		$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax:   		'',
				consultaAjaxKardex:   	'75',
				wemp_pmla:     			$("#wemp_pmla").val(),
				basedatos: 	   			$("#wbasedato").val(),
				historia:				$('#whistoria').val(),
				ingreso: 				$('#wingreso').val(),
				wuser:    				$("#usuario").val(),
				arrayInsumosNPT:		insumosNPT[x],
				idProtocolo:			x,
				ido:					insumosNPT[x].ido,
				codArticulo:			insumosNPT[x].articulo,
				peso:					insumosNPT[x].peso,
				tiempoInfusion:			insumosNPT[x].tiempoInfusion,
				purga:					insumosNPT[x].purga,
				volumenTotal:			insumosNPT[x].volumenTotal,
				observaciones:			insumosNPT[x].observaciones
			},
			async: false,
			success:function(respuesta) {

				// console.log(respuesta);
				if(respuesta.error==1)
				{
					alert(respuesta.mensaje);
					// return;
				}
				
			}
		});
		
	}
	
}

function validarEleccionInsumosNPT(indice,tipoProtocolo){
	
	mensajeValidacion = "";
	
	valPrescripcion = false;
	$('table[id=tablaNutricionesNPT] input[id^=NPT_PRESCRIPCION]').each(function(){
		
		var prescripcion = $(this).val();
		
		if(parseInt(prescripcion)!=0)
		{
			valPrescripcion = true;
			return;
		}
	});
	
	if(valPrescripcion == false)
	{
		mensajeValidacion += "- Debe seleccionar al menos un insumo\n";
	}
	
	var pesoNPT = $("#NPT_PESO").val();
	var tiempoInfusionNPT = $("#NPT_TIEMPOINFUSION").val();
	var purgaNPT = $("#NPT_PURGA").val();
	var volumenTotal= $("#NPT_VT").val();
	
	var aguaEsterilVolumen= $("#NPT_AEV").html();
	var aguaEsterilCorreccoinPurga= $("#NPT_AECP").html();
	
	valoresEnCero = "";
	if( parseFloat(pesoNPT)<=0 ||  parseInt(volumenTotal)===0)
	{
		valoresEnCero = "Los siguientes campos deben ser mayores a cero: ";
		
		if(parseFloat(pesoNPT)<=0)
		{
			valoresEnCero += "peso, ";
		}
		
		if(parseInt(volumenTotal)===0)
		{
			valoresEnCero += "volumen total, ";
		}
		
		valoresEnCero = valoresEnCero.substr(0,valoresEnCero.length-2);
		mensajeValidacion += "- "+valoresEnCero+"\n";
	}
	
	if( parseFloat(aguaEsterilVolumen)<0 || parseFloat(aguaEsterilCorreccoinPurga)<0)
	{
		mensajeValidacion += "- El agua esteril debe ser mayor a cero \n";
	}
	
	if( parseInt(purgaNPT)<0)
	{
		mensajeValidacion += "- La purga debe ser mayor a cero \n";
	}
	
	if( parseInt(purgaNPT)>30)
	{
		mensajeValidacion += "- La purga debe ser menor o igual a 30 ml \n";
	}
	
	if( parseInt(tiempoInfusionNPT)<10)
	{
		mensajeValidacion += "- El tiempo de infusión debe ser mayor o igual a 10 horas \n";
	}
	
	if( parseInt(tiempoInfusionNPT)>24)
	{
		mensajeValidacion += "- El tiempo de infusión debe ser menor o igual a 24 horas \n";
	}
	
	if( parseInt(volumenTotal)>4000)
	{
		mensajeValidacion += "- El volumen total debe ser menor o igual a 4000 \n";
	}
	
	valoresVacios = "";
	if(pesoNPT==="" || tiempoInfusionNPT==="" || purgaNPT==="" || volumenTotal==="")
	{
		valoresVacios = "Debe llenar los siguientes campos: ";
		
		if(pesoNPT==="")
		{
			valoresVacios += "peso, ";
		}
		if(tiempoInfusionNPT==="")
		{
			valoresVacios += "tiempo de infusi&oacuten, ";
		}
		if(purgaNPT==="")
		{
			valoresVacios += "purga, ";
		}
		if(volumenTotal==="")
		{
			valoresVacios += "volumen total, ";
		}
		
		valoresVacios = valoresVacios.substr(0,valoresVacios.length-2);
		mensajeValidacion += "- "+valoresVacios+"\n";
	}
	
	if(mensajeValidacion!="")
	{
		jAlert(mensajeValidacion,"ALERTA");
	}
	else
	{
		grabarModalNPT(indice,tipoProtocolo);
	}
}

function grabarModalNPT(indice,tipoProtocolo){
	
	var cantInseguro = 0;
	var mensajeInseguros = "";
	$('table[id=tablaNutricionesNPT] span[id^=NPT_MF]').each(function(){
		  
		var mensaje = $(this).attr("mensaje");
		var mensajeOk = $(this).attr("bien");
		var idMensaje = $(this).attr("id");
		
		// idMensaje=idMensaje.replace("NPT_MF","")
		idMensaje=idMensaje.split("_");
		
		if(mensaje=="inseguro")
		{
			cantInseguro++;
			// console.log(mensaje);
			mensajeInseguros += "- "+$("#NPT_ParametroNutricional"+idMensaje[2]).html()+" debe ser "+ mensajeOk +" \n";
		}
  		
	});
	
	$('table[id=tablaNutricionesNPT] span[id^=NPT_MVAF]').each(function(){
		  
		var mensaje = $(this).attr("mensaje");
		var mensajeOk = $(this).attr("bien");
		var idMensaje = $(this).attr("id");
		
		idMensaje=idMensaje.split("_");
		
		if(mensaje=="inseguro")
		{
			cantInseguro++;
			mensajeInseguros += "- La nutrici&oacuten parenteral debe ser "+ mensajeOk +" \n";
		}
  
		
	});
	// console.log(cantInseguro);
	// console.log(mensajeInseguros);
	
	if(cantInseguro==0)
	{
		delete insumosNPT[tipoProtocolo+indice];
		
		if( !insumosNPT[tipoProtocolo+indice] )
		{
			insumosNPT[tipoProtocolo+indice] = [];
		}
			
		$('table[id=tablaNutricionesNPT] input[id^=NPT_PRESCRIPCION]').each(function(){
			
			var prescripcion = $(this).val();
			
			if(prescripcion!=0)
			{
				var idPrescripcion = $(this).attr('id');
			
				idOrdenInsumo = idPrescripcion.replace("NPT_PRESCRIPCION","");
				
				var codInsumo = $("#NPT_Insumo"+idOrdenInsumo).val();
				
				
			
				insumosNPT[tipoProtocolo+indice][insumosNPT[tipoProtocolo+indice].length] = {
					codInsumo: codInsumo,
					prescripcion: prescripcion
				}
				
				insumosNPT[tipoProtocolo+indice].peso = $("#NPT_PESO").val();
				insumosNPT[tipoProtocolo+indice].tiempoInfusion = $("#NPT_TIEMPOINFUSION").val();
				insumosNPT[tipoProtocolo+indice].purga = $("#NPT_PURGA").val();
				insumosNPT[tipoProtocolo+indice].volumenTotal = $("#NPT_VT").val();
				insumosNPT[tipoProtocolo+indice].observaciones = $("#NPT_Observaciones").val();
				
				$("#wtxtobs"+tipoProtocolo+indice).val(insumosNPT[tipoProtocolo+indice].observaciones);
				
				//agregar atributo para indicar que es npt
				$("#wnmmed"+tipoProtocolo+indice).attr("esNPT","on");
				$("#wesnpt"+tipoProtocolo+indice).val("on");
				
				//Confirmar automaticamente al ordenar
				// $("#wchkconf"+tipoProtocolo+indice).prop('checked',true);
				
			}
		});
		
		
		var onclickNPT = "";
		onclickNPT = $("#wcolmed"+tipoProtocolo+indice).attr("onclick");
		
		if(onclickNPT === undefined)
		{
			$("#wcolmed"+tipoProtocolo+indice).attr("onclick","abrirModalNutriciones('"+indice+"','"+tipoProtocolo+"','modificar');");
		}
		else
		{
			//No existe 
			if(onclickNPT.search("abrirModalNutriciones") == -1)
			{
			 	onclickNPT = "abrirModalNutriciones('"+indice+"','"+tipoProtocolo+"','modificar');"+onclickNPT;
				$("#wcolmed"+tipoProtocolo+indice).attr("onclick",onclickNPT);
			}
		}
		
		$.unblockUI();
		
		$('#botonNPT').attr('disabled',true);
		
	}
	else
	{
		jAlert(mensajeInseguros,"ALERTA");
	}
}

function salirSinGrabarModalNPT(indice,tipoProtocolo){
	
	jConfirm( "No se grabará el medicamento en la ordenes. Desea continuar?", "ALERTA", function( resp ){
		if( resp ){
			quitarArticulo( indice,tipoProtocolo, '', 'detKardexAddU', true );
			$.unblockUI();
		}
	});
}

function recalcularValoresNPT()
{
	var volumenTotal= $("#NPT_VT").val();
	volumenTotal = parseFloat(volumenTotal);
	$("#NPT_VT").val(volumenTotal.toFixed(2));
		
	
	$('table[id=tablaNutricionesNPT] input[id^=NPT_Formula_]').each(function(){
		var idFormula = $(this).attr('id');
		var formulasTotales = $(this).val();
	  
		// console.log(idFormula);
		// console.log(formulasTotales);
		
		
		idFormula = idFormula.split("_");
		idFormula = idFormula[2];
		// idFormula = idFormula.replace("NPT_F","");
		
		var operadoresFormula = /[*/+-]/g;
		var elementosFormula = formulasTotales.split(operadoresFormula);
		
		for(var i=0;i<elementosFormula.length;i++)
		{
			
			elemFormula = elementosFormula[i];
			elemFormula = elemFormula.split(/[()]/g);
			
			for (var j=0;j<elemFormula.length;j++)
			{
				if(elemFormula[j]!="")
				{
					elemFormula[j] = $.trim(elemFormula[j]);
					// console.log(elemFormula[j]);
					valorFormula = $("#NPT_"+elemFormula[j]).html();
					if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
					{
						formulasTotales = formulasTotales.replace(elemFormula[j],valorFormula);
						
					}
					else
					{
						valorFormula = $("#NPT_"+elemFormula[j]).val();
						if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
						{
							formulasTotales = formulasTotales.replace(elemFormula[j],valorFormula);
							
						}
						else
						{
							if(isNaN(parseFloat(elemFormula[j])))
							{
								formulasTotales = formulasTotales.replace(elemFormula[j],0);
							}
						}
					}
				}
			}
		}
		// console.log(formulasTotales);
		if(formulasTotales != "")
		{			
			try 
			{
				resultadoFormula= eval(formulasTotales);
			
				if(!isNaN(parseFloat(resultadoFormula)) && isFinite(parseFloat(resultadoFormula)))
				{
					resultadoFormula=resultadoFormula.toFixed(2);
					$("#NPT_"+idFormula).html(resultadoFormula);
				}
			}
			catch(err) {
				jAlert("Error en la configuración de las formulas","ALERTA");
				$("#btnGrabarNPT").attr('disabled', 'disabled');
			}
			
		}
	});
	
	
	
	// // ========================================================================
	// //					PARÁMETROS NUTRICIONALES Y FARMACÉUTICOS
	// // ========================================================================
	
	$('table[id=tablaNutricionesNPT] input[id^=NPT_F]').each(function(){
		formulaParamNutricYFarmace = $(this).val();
		idFormula= $(this).attr('id');
		
		
		idFormulasPNYF = idFormula.split("_");
		var idFijo = idFormulasPNYF[0]+"_"+idFormulasPNYF[1]+"_";
		
		if(idFijo != "NPT_Formula_" && idFijo != "NPT_FMF1_" && idFijo != "NPT_FMF2_" && idFijo != "NPT_FMVAF1_" && idFijo != "NPT_FMVAF2_" && idFijo != "NPT_FMVAF3_")
		{
			idFormula= $(this).attr('id');
			// console.log(idFormula);
			
			idFormula = idFormula.replace("NPT_F","");
		
			var operadoresFormula = /[*/+-]/g;
			var elementosFormula = formulaParamNutricYFarmace.split(operadoresFormula);
			
			// console.log(formulaParamNutricYFarmace);
			
			for(var i=0;i<elementosFormula.length;i++)
			{
				elementosFormula[i]=$.trim(elementosFormula[i]);
				elemFormula = elementosFormula[i];
				elemFormula = elemFormula.split(/[()]/g);
				
				for (var j=0;j<elemFormula.length;j++)
				{
					if(elemFormula[j]!="")
					{
						elemFormula[j] = $.trim(elemFormula[j]);
						// console.log(elemFormula[j]);
						valorFormula = $("#NPT_"+elemFormula[j]).html();
						if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
						{
							formulaParamNutricYFarmace = formulaParamNutricYFarmace.replace(elemFormula[j],valorFormula);
						}
						else
						{
							valorFormula = $("#NPT_"+elemFormula[j]).val();
							if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
							{
								formulaParamNutricYFarmace = formulaParamNutricYFarmace.replace(elemFormula[j],valorFormula);
							}
							else
							{
								if(isNaN(parseFloat(elemFormula[j])))
								{
									formulaParamNutricYFarmace = formulaParamNutricYFarmace.replace(elemFormula[j],0);
								}
							}
						}
						
					}
				}
				
			}
			// console.log(formulaParamNutricYFarmace);
				
			if(formulaParamNutricYFarmace != "")
			{
				// console.log(formulaParamNutricYFarmace);
				try 
				{
					resultadoFormula= eval(formulaParamNutricYFarmace);
				
					if(!isNaN(parseFloat(resultadoFormula)) && isFinite(parseFloat(resultadoFormula)))
					{
						resultadoFormula=resultadoFormula.toFixed(2);
						$("#NPT_RF"+idFormula).html(resultadoFormula);
					}
				}
				catch(err) {
					jAlert("Error en la configuración de las formulas de parámetros nutricionales y farmacéuticos","ALERTA");
					$("#btnGrabarNPT").attr('disabled', 'disabled');
				}
			}
		}
	});
	
	
	// ========================================================================
	//								Mensajes
	// ========================================================================
	$('table[id=tablaNutricionesNPT] input[id^=NPT_FMF]').each(function(){
		formulaMensaje = $(this).val();
		idFormula= $(this).attr('id');
		
		idMensaje = idFormula.replace("NPT_F","");
		
		var operadoresCondic = /[()]/g;
		var condiciones = formulaMensaje.split(operadoresCondic);
		
		// se organizan las condiciones y se asignan los valores
		for(var i=0;i<condiciones.length-1;i++)
		{
			if(condiciones[i]!="" && condiciones[i].charAt(0)!="{" && condiciones[i].charAt(condiciones[i].length-1)!="}")
			{
				condiciones[i]=$.trim(condiciones[i]);
				condicion=condiciones[i];
				// console.log(condicion)
				condicion=condicion.replace(/O/g,"||");
				condicion=condicion.replace(/Y/g,"&&");
				
				var operadoresCondicion = /[<>=|&]/g;
				var elementosCondicion = condicion.split(operadoresCondicion);
				
				for(var j=0;j<elementosCondicion.length;j++)
				{
					if(elementosCondicion[j]!="")	
					{
						elementosCondicion[j] = $.trim(elementosCondicion[j]);
						
						valorCondicion = $("#NPT_"+elementosCondicion[j]).html();
						if(valorCondicion!==undefined && valorCondicion!==null && valorCondicion!="")
						{
							condicion = condicion.replace(elementosCondicion[j],valorCondicion);
						}
						else
						{
							valorCondicion = $("#NPT_"+elementosCondicion[j]).val();
							if(valorCondicion!==undefined && valorCondicion!==null && valorCondicion!="")
							{
								condicion = condicion.replace(elementosCondicion[j],valorCondicion);
							}
							else
							{
								if(isNaN(parseFloat(elementosCondicion[j])))
								{
									condicion = condicion.replace(elementosCondicion[j],0);
								}
							}
						}
					}
				}
				// console.log(condicion)
				formulaMensaje=formulaMensaje.replace(condiciones[i],condicion);
			}
		}
		// console.log(formulaMensaje);
		// se agregan if y else segun el caso
		
		// formulaMensaje=formulaMensaje.replace(/[(]/g,"if(");
		formulaMensaje=formulaMensaje.replace(/[(]/g,"if(");
		
		// formulaMensaje=formulaMensaje.replace(RegExp("if(if(","g"),"if((");
		formulaMensaje=formulaMensaje.replace("if(if(","if((");
		// formulaMensaje=formulaMensaje.replace(/[if(if(]/g,"if((");
		
		// formulaMensaje=formulaMensaje.replace(RegExp("|| if","g"),"|| ");
		formulaMensaje=formulaMensaje.replace("|| if","|| ");
		// formulaMensaje=formulaMensaje.replace(/[|| if]/g,"|| ");
		
		// formulaMensaje=formulaMensaje.replace(RegExp("&& if","g"),"&& ");
		formulaMensaje=formulaMensaje.replace("&& if","&& ");
		// formulaMensaje=formulaMensaje.replace(/[&& if]/g,"&& ");
		
		formulaMensaje=formulaMensaje.replace(/[;]/g,"}else{");
		// console.log(formulaMensaje);
		var operadoresMensaje = /[{}]/g;
		var mensajes = formulaMensaje.split(operadoresMensaje);

		// se organizan y reemplazan los mensajes
		var contMensajeSeguro=0;
		for(var j=0;j<mensajes.length-1;j++)
		{
			if(mensajes[j].charAt(mensajes[j].length-1)!=")" && mensajes[j]!= "else")
			{
				if(mensajes[j]!="")
				{
					mensajeCondicional = mensajes[j];
					
					var mensajeCondicionalCompleto="";
					if(contMensajeSeguro==0)
					{
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).html("'+mensajeCondicional+'");';
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).attr("mensaje","seguro");';
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).css("color", "#4A8540");';
						$("#NPT_"+idMensaje).attr("bien",mensajeCondicional);
						formulaMensaje=formulaMensaje.replace(mensajes[j],mensajeCondicionalCompleto);
						contMensajeSeguro++;
					}
					else
					{
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).html("'+mensajeCondicional+'");';
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).attr("mensaje","inseguro");';
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).css("color", "#C02C2C");';
						
						formulaMensaje=formulaMensaje.replace(RegExp(mensajes[j],"g"),mensajeCondicionalCompleto);
						break;
						
					}
				}
			}
		}
		// console.log(formulaMensaje);
		//Se evalua la condicion
		eval(formulaMensaje);
	
	});	
	
	
	// // -------------------------------
	
	// //Via de administracion 
	
	$('table[id=tablaNutricionesNPT] input[id^=NPT_FMVAF]').each(function(){
		formulaMensaje = $(this).val();
		idFormula= $(this).attr('id');
		
		indicadorSeguridad = $(this).attr('indicadorSeguridad');
		
		idMensaje = idFormula.replace("NPT_F","");
		
		var operadoresCondic = /[()]/g;
		var condiciones = formulaMensaje.split(operadoresCondic);
		
		// se organizan las condiciones y se asignan los valores
		for(var i=0;i<condiciones.length-1;i++)
		{
			if(condiciones[i]!="" && condiciones[i].charAt(0)!="{" && condiciones[i].charAt(condiciones[i].length-1)!="}")
			{
				condiciones[i]=$.trim(condiciones[i]);
				condicion=condiciones[i];
				
				condicion=condicion.replace(/O/g,"||");
				condicion=condicion.replace(/Y/g,"&&");
				
				var operadoresCondicion = /[<>=|&]/g;
				var elementosCondicion = condicion.split(operadoresCondicion);
				
				for(var j=0;j<elementosCondicion.length;j++)
				{
					if(elementosCondicion[j]!="")	
					{
						elementosCondicion[j] = $.trim(elementosCondicion[j]);
						
						valorCondicion = $("#NPT_"+elementosCondicion[j]).html();
						if(valorCondicion!==undefined && valorCondicion!==null && valorCondicion!="")
						{
							condicion = condicion.replace(elementosCondicion[j],valorCondicion);
						}
						else
						{
							valorCondicion = $("#NPT_"+elementosCondicion[j]).val();
							if(valorCondicion!==undefined && valorCondicion!==null && valorCondicion!="")
							{
								condicion = condicion.replace(elementosCondicion[j],valorCondicion);
							}
							else
							{
								if(isNaN(parseFloat(elementosCondicion[j])))
								{
									condicion = condicion.replace(elementosCondicion[j],0);
								}
							}
						}
					}
				}
				
				formulaMensaje=formulaMensaje.replace(condiciones[i],condicion);
			}
		}
		
		// // se agregan if y else segun el caso
		// formulaMensaje=formulaMensaje.replace(/[(]/g,"if(");
		formulaMensaje=formulaMensaje.replace(/[(]/g,"if(");
		formulaMensaje=formulaMensaje.replace(/[;]/g,"}else{");
		
		var operadoresMensaje = /[{}]/g;
		var mensajes = formulaMensaje.split(operadoresMensaje);

		// se organizan y reemplazan los mensajes
		var contMensajeSeguro=0;
		for(var j=0;j<mensajes.length-1;j++)
		{
			if(mensajes[j].charAt(mensajes[j].length-1)!=")" && mensajes[j]!= "else")
			{
				var mensajeCondicionalCompleto="";
				if(mensajes[j]!="")
				{
					mensajeCondicional = mensajes[j];
					
					if(contMensajeSeguro==0)
					{
						if(idFormula == "NPT_FMVAF3_33")
						{
							$("#NPT_"+idMensaje).attr("bien",mensajeCondicional);
							mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).attr("mensaje","seguro");';
						}
						
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).html("'+mensajeCondicional+'");';
						formulaMensaje=formulaMensaje.replace(mensajes[j],mensajeCondicionalCompleto);
						contMensajeSeguro++;
					}
					else
					{
						if(idFormula == "NPT_FMVAF3_33")
						{
							mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).attr("mensaje","inseguro");';
						}
						mensajeCondicionalCompleto += '$("#NPT_"+idMensaje).html("'+mensajeCondicional+'");';
						formulaMensaje=formulaMensaje.replace(RegExp(mensajes[j],"g"),mensajeCondicionalCompleto);
					}
				}
				
			}
		}
		
		//Se evalua la condicion
		eval(formulaMensaje);
		
	});	
}

function recalcularVolumenyPurgaModalNPT()
{
	recalcularValoresNPT();
	
	$('table[id=tablaNutricionesNPT] input[id^=NPT_PRESCRIPCION]').each(function(){
		
		var idOrdenInsumo = $(this).attr('id');	
		idOrdenInsumo = idOrdenInsumo.replace("NPT_PRESCRIPCION","");
		
		var prescripcion = $(this).val();
		prescripcion = parseFloat(prescripcion);
		
		if(!isNaN(prescripcion))
		{
			$("#NPT_PRESCRIPCION"+idOrdenInsumo).val(prescripcion.toFixed(2));
		}
		else
		{
			// console.log("hola");
			$("#NPT_PRESCRIPCION"+idOrdenInsumo).val("0.00");
		}
				
		var formulaVolumen = $("#formulaVolumen"+idOrdenInsumo).val();
		
		var operadoresFormula = /[*/+-]/g;
		var elementosFormulaVolumen = formulaVolumen.split(operadoresFormula);
		
		
		// console.log(formulaVolumen);
		
		
		volumen=0;
		for(var i=0;i<elementosFormulaVolumen.length;i++)
		{
			elemFormulaVol = elementosFormulaVolumen[i];
			elemFormulaVol = elemFormulaVol.split(/[()]/g);  
			// console.log(elemFormulaVol);
			for (var j=0;j<elemFormulaVol.length;j++)
			{
				if(elemFormulaVol[j]!="")
				{
					elemFormulaVol[j] = $.trim(elemFormulaVol[j]);	
					
					var elementoFormula = elemFormulaVol[j];
					if(elemFormulaVol[j] == "PRESCRIPCION")
					{
						elementoFormula = elemFormulaVol[j]+idOrdenInsumo;	
					}
					
					// valorFormula = $("#NPT_"+elemFormulaVol[j]).html();
					valorFormula = $("#NPT_"+elementoFormula).html();
					if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
					{
						formulaVolumen = formulaVolumen.replace(elemFormulaVol[j],valorFormula);
						
					}
					else
					{
						// valorFormula = $("#NPT_"+elemFormulaVol[j]).val();
						valorFormula = $("#NPT_"+elementoFormula).val();
						if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
						{
							formulaVolumen = formulaVolumen.replace(elemFormulaVol[j],valorFormula);
						}
						else
						{
							// if(isNaN(parseFloat(elemFormulaVol[j])))
							if(isNaN(parseFloat(elementoFormula)))
							{
								formulaVolumen = formulaVolumen.replace(elemFormulaVol[j],0);
							}
						}
					}
			  	}
			}
		}
		
		// console.log(formulaVolumen);
		if(formulaVolumen != "")
		{			
			try 
			{
				volumen= eval(formulaVolumen);
				if(!isNaN(parseFloat(volumen)) && isFinite(parseFloat(volumen)))
				{
					volumen=volumen.toFixed(2);
					$("#NPT_V"+idOrdenInsumo).html(volumen);
				}
			}
			catch(err) {
				jAlert("Error en la configuración de las formulas del volumen","ALERTA");
				$("#btnGrabarNPT").attr('disabled', 'disabled');
			}
		}
		
		
		var formulaCorreccionPurga = $("#formulaPurga"+idOrdenInsumo).val();
		
		var operadoresFormula = /[*/+-]/g;
		var elementosFormulaCorreccionPurga = formulaCorreccionPurga.split(operadoresFormula);
		
		
		// console.log(formulaCorreccionPurga);
		
		
		correccionPurga=0;
		for(var i=0;i<elementosFormulaCorreccionPurga.length;i++)
		{
			elemFormulaCorrPurga = elementosFormulaCorreccionPurga[i];
			elemFormulaCorrPurga = elemFormulaCorrPurga.split(/[()]/g);  
			// console.log(elemFormulaCorrPurga);
			for (var j=0;j<elemFormulaCorrPurga.length;j++)
			{
				if(elemFormulaCorrPurga[j]!="")
				{
					elemFormulaCorrPurga[j] = $.trim(elemFormulaCorrPurga[j]);		


					var elementoFormula = elemFormulaCorrPurga[j];
					if(elemFormulaCorrPurga[j] == "V")
					{
						elementoFormula = elemFormulaCorrPurga[j]+idOrdenInsumo;	
					}
					
					// console.log(elemFormulaCorrPurga[j]);
					// valorFormula = $("#NPT_"+elemFormulaCorrPurga[j]).html();
					valorFormula = $("#NPT_"+elementoFormula).html();
					if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
					{
						formulaCorreccionPurga = formulaCorreccionPurga.replace(elemFormulaCorrPurga[j],valorFormula);
						
					}
					else
					{
						// valorFormula = $("#NPT_"+elemFormulaCorrPurga[j]).val();
						valorFormula = $("#NPT_"+elementoFormula).val();
						if(valorFormula!==undefined && valorFormula!==null && valorFormula!="")
						{
							formulaCorreccionPurga = formulaCorreccionPurga.replace(elemFormulaCorrPurga[j],valorFormula);
							
						}
						else
						{
								
							// if(isNaN(parseFloat(elemFormulaCorrPurga[j])))
							if(isNaN(parseFloat(elementoFormula)))
							{
								formulaCorreccionPurga = formulaCorreccionPurga.replace(elemFormulaCorrPurga[j],0);
							}
						}
					}
			  
				}
			}
		}
		
		// console.log(formulaCorreccionPurga);
		if(formulaCorreccionPurga != "")
		{			
			try 
			{
				correccionPurga= eval(formulaCorreccionPurga);
				if(!isNaN(parseFloat(correccionPurga)) && isFinite(parseFloat(correccionPurga)))
				{
					correccionPurga=correccionPurga.toFixed(2);
					$("#NPT_CP"+idOrdenInsumo).html(correccionPurga);
				}
			}
			catch(err) {
				jAlert("Error en la configuración de las formulas de la corrección purga","ALERTA");
				$("#btnGrabarNPT").attr('disabled', 'disabled');
			}
		}
	});
	
	recalcularValoresNPT();
}

function abrirModalNutriciones(idx,tipoprotocolo,accion)
{
	var insumos = "";
	var peso = "";
	var tiempoInfusion = "";
	var purga = "";
	var volumenTotal = "";
	var observaciones = "";
	
	if(accion=="modificar")
	{
		insumos = insumosNPT[tipoprotocolo+idx];;
		peso = insumosNPT[tipoprotocolo+idx].peso;
		tiempoInfusion = insumosNPT[tipoprotocolo+idx].tiempoInfusion;
		purga = insumosNPT[tipoprotocolo+idx].purga;
		volumenTotal = insumosNPT[tipoprotocolo+idx].volumenTotal;
		// observaciones = insumosNPT[tipoprotocolo+idx].observaciones;
		observaciones = $("#wtxtobs"+tipoprotocolo+idx).val();
	}
	
	$.post("ordenes.inc.php",
	{
		consultaAjax:   		'',
		consultaAjaxKardex:   	'74',
		wemp_pmla:     			$("#wemp_pmla").val(),
		wcenmez:     			$("#wcenmez").val(),
		idx:     				idx,
		tipoprotocolo:     		tipoprotocolo,
		historia:				$('#whistoria').val(),
		ingreso: 				$('#wingreso').val(),
		accion:					accion,
		arrayInsumos:			insumos,
		peso:					peso,
		tiempoInfusion:			tiempoInfusion,
		purga:					purga,
		volumenTotal:			volumenTotal,
		observaciones:			observaciones
	}, function(respuesta){

		$( "#dvAuxModalNutriciones" ).html( respuesta );
		
		var cadenaTooltipFormulas = $("#tooltipFormulas").val();
		
		cadenaTooltipFormulas = cadenaTooltipFormulas.split("|");
		
		for(var i = 0; i < cadenaTooltipFormulas.length-1;i++)
		{
			$( "#"+cadenaTooltipFormulas[i] ).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
		}
		
		if(accion=="modificar")
		{
			recalcularVolumenyPurgaModalNPT();
		}
			
		var canWidth = $(window).width()*0.8;
		if( $( "#dvAuxModalNutriciones" ).width()-50 < canWidth )
			canWidth = $( "#dvAuxProcedimientosAgrupados" ).width();

		var canHeight = $(window).height()*0.8;;
		if( $( "#dvAuxModalNutriciones" ).height()-50 < canHeight )
			canHeight = $( "#dvAuxModalNutriciones" ).height();

	
		$.blockUI({ message: $('#modalNutriciones'),
		css: {
			overflow: 'auto',
			cursor	: 'auto',
			width	: "60%",
			height	: "80%",
			left	: "20%",
			top		: '100px',
		} });
		
		setTimeout(function(){
			$("#NPT_PESO").focus();
		}, 500 );
	});
}

// -------------------------------------

function dttoPorFiltroAntibiotico( cmp, idx, dtto, dma ){
	
	var dmaDef = false;
	try{
		dmaDef = dmaPorFrecuencia[ $( "#wperiod"+idx ).val() ].dma;
	}
	catch(e){
		dmaDef = false;
	}
	
	if( !dmaDef ){
		try{
			dmaDef = dmaPorCondicionesSuministro[ $( "#wcondicion"+idx ).val() ].dma;
		}
		catch(e){
			dmaDef = false;
		}
	}
	
	if( dmaDef && dmaDef*1 > 0 ){
		dma = dmaDef;
		dtto = 0;
	}
	else{
		// if( !dtto || dtto*1 == 0 || isNaN(dtto) || dmaAux < dtto*1 ) dtto = 1;
	}
	
	$( "#wdiastto"+idx ).css({disabled:true});
	$( "#wdiastto"+idx ).val('');
	
	$("#wdiastto"+idx).attr({disabled:false});
	$("#wdiastto"+idx).attr({readOnly:false});
	$("#wdiastto"+idx).prop({readOnly:false});
	
	if( dtto*1 > 0 ){
		$("#wdiastto"+idx).val(dtto);
		$("#wdiastto"+idx).change();
		$("#wdiastto"+idx).keyup();
		// if( cmp.id == "wprofilaxis"+idx ){
			// $("#wdiastto"+idx).attr({disabled:true});
		// }
	}
	else{
		$("#wdiastto"+idx).val('');
		$("#wdiastto"+idx).change();
		$("#wdiastto"+idx).keyup();
	}
	
	if( dma*1 > 0 ){
		console.log( "dma: "+dma )
		$("#wdosmax"+idx).val(dma);
		$("#wdosmax"+idx).change();
		$("#wdosmax"+idx).keyup();
		// if( cmp.id == "wprofilaxis"+idx ){
			// $("#wdosmax"+idx).attr({disabled:true});
		// }
	}
	else{
		$("#wdosmax"+idx).val('');
		$("#wdosmax"+idx).change();
		$("#wdosmax"+idx).keyup();
	}
}

/********************************************************************************
 * Procedimientos agrupados
 ********************************************************************************/

function modificarArrayProcedimientosAgrupadosImprimirIndividual(tipoOrden,contprocedimientosAgru,codProc,e){
	
	Imprimir=$( "#checkboxImpProcAgrupado"+tipoOrden+contprocedimientosAgru+codProc ).prop('checked');
	
	if(Imprimir == true)
	{
		imp = "on";
	}
	else
	{
		imp = "off";
	}
	
	for( var x in procAgrupados ){
		for (var i = 0; i < procAgrupados[x].length; i++) {
			
			if( tipoOrden + contprocedimientosAgru ==  x && procAgrupados[x][i].codigo==codProc)
			{
				procAgrupados[x][i].imprimir = imp;
			}
		}
	}
	
	
	cambiarImprimirIndividualProcedimientosAgrupados(tipoOrden,contprocedimientosAgru,codProc,e);
	
	//evita que se abra la modal de procedimientos agrupados cuando cambia el checkbox
	e.stopPropagation();
}

function validarImpresionProcAgrupados()
{
	$( "[id^=checkboxImpProcAgrupado]").each(function(){
		
		var idCheckIndividualProcAgrupado = $(this).attr('id');
		
		if($("#"+idCheckIndividualProcAgrupado+":visible").length == 0)
		{
			tipoOrden = $(this).attr('tipProcAgrup');
			contprocedimientosAgru = $(this).attr('contadorProcAgrupado');
			codProc = $(this).attr('codProcedimiento');
			
			for (var i = 0; i < procAgrupados[tipoOrden+contprocedimientosAgru].length; i++) {
				
				if(procAgrupados[tipoOrden + contprocedimientosAgru][i].codigo  ==  codProc)
				{
					Imprimir=$(this).prop('checked');
			
					PosProcedimiento=procAgrupados[tipoOrden+contprocedimientosAgru][i].consecutivo;
					
					// $("#trEx"+PosProcedimiento+" input:checkbox").click();
					
					if(Imprimir== true)
					{
						$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
						$("#trEx"+PosProcedimiento+" input:checkbox").click();
						$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
						
						// procAgrupados[tipoOrden + contprocedimientosAgru][i].imprimir="on";
					}
					else
					{
						$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
						$("#trEx"+PosProcedimiento+" input:checkbox").click();
						$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
						
						// procAgrupados[tipoOrden + contprocedimientosAgru][i].imprimir="off";
					}
				}
			}
		}
		
		
	});
}
 
 
// function quitarMedArrayProcAgrupados(tipoProtocolo,idxElemento)
function quitarMedArrayProcAgrupados(tipoProtocolo,idxElemento,procAgrup)
{
	for( var x in procAgrupados )
	{
		for (var i = 0; i < procAgrupados[x].length; i++) 
		{
			var cantMed = 0;
			var cantMedSinCTC = 0;
			for( var u in procAgrupados[x][i].medicamentos )
			{
				cantMed ++;
				if(procAgrupados[ x ][i].medicamentos[ u ].tipoProtocolo == tipoProtocolo && procAgrupados[ x ][i].medicamentos[ u ].posicionActual == idxElemento)
				{
					delete procAgrupados[ x ][i].medicamentos[ u ];
					descMedi = $("#descrMedProAgrupGener_"+u).val();
					
					if(procAgrup!="procAgrupEliminado")
					{
						// jAlert("No se agregó el medicamento: "+descMedi+" porque no llenó el CTC de medicamentos");
						// alert("No se agregó el medicamento: "+descMedi+" porque no llenó el CTC de medicamentos");
						
						medObligatorio = $("#medObligatorio"+procAgrupados[ x ][i].codigo).val();
						
						if(medObligatorio == "on")
						{
							cantMedSinCTC ++;
							// console.log("------->"+cantMedSinCTC);
						}
					}
					
					
				}
			}
			
			if(cantMedSinCTC == 1 && cantMed== 1)
			{
				procAgrupados[ x ].splice(i,1);
				$('#trExAgru'+x).remove();
				// alert("No se agregó el procedimiento y el medicamento: "+descMedi+" porque no llenó el CTC de medicamentos");
			}
			
		}
	}
}

function validarMedicamentosProcAgrupados(id,codMedicamento,principiosActivos,familia,descMedicamento,desFamilia,desPrincipioActivo)
{
	var mensajeValidacion = "";
	
	if($("#ckProcMed_"+id).prop("checked") == true)
	{
		var mensajeValidacion = "";
		
		// -----------
		// Medicamento
		// -----------
		
		//Grabados
		MedicamentoExiste = $( "div [id^=wnmmed]:contains("+codMedicamento+"-)" ).length;
		
		if(MedicamentoExiste > 0)
		{
			mensajeValidacion += "El medicamento "+codMedicamento+" - "+descMedicamento+" ya existe \n";
		}
		else
		{
			//Agregados
			// $( "input[id^=wnmmed]" ).each(function(){
			$('table[id=tbDetalleAddN] input[id^=wnmmed]').each(function(){
			   if(codMedicamento==$.trim( $( this ).val().split( "-" )[0] ))
				{
					mensajeValidacion += "El medicamento "+codMedicamento+" - "+descMedicamento+" ya existe \n";
				}
			  
			});
		}
		
		
	
		
		// ----------------
		// Principio activo
		// ----------------
		
		//Grabados y agregados
		PrincipioActivo = principiosActivos.split(",");
		DescrPrincipioActivo = desPrincipioActivo.split(",");
		
		$( "[pactivo]" ).each(function(){
			pact = $( this ).attr("pactivo").split(",");
			console.log(pact);
			for(var i=0; i < pact.length; i++)
			{
				for(var j=0; j < PrincipioActivo.length; j++)
				{
					if(pact[i]==PrincipioActivo[j])
					{
						busc = mensajeValidacion.search("El principio activo "+DescrPrincipioActivo[j]+" ya existe\n");
						if(busc == -1)
						{
							
							mensajeValidacion += "El principio activo "+DescrPrincipioActivo[j]+" ya existe\n";
						}
					}
				}
				
			}
		  
		});
		
		
		// -------
		// Familia
		// -------
		//Grabados
		$( "[id^=filaTituloFamilia] > td > b" ).each(function(){
			if(familia==$.trim( $( this ).text().split( " - " )[0] ))
			{
				mensajeValidacion += "La familia "+desFamilia+" ya existe\n";
			}
		});
				
		// //Agregados
		// // console.log(desFamilia);
		// if( familiasAgregadas[ desFamilia.toUpperCase() ] )
		// {
			// mensajeValidacion += "La familia "+desFamilia+" ya existe\n";
		// }
		
		


		if(mensajeValidacion != "")
		{
			jAlert(mensajeValidacion, "ALERTA");
		}		
		
	}
	
	
	
}

function validarPrincipioActivoAgrup(codProcedimiento,idMedicamento,descProce)
{
	princActiMedPorSeleccionar = $("#princActivProcAgrup_"+idMedicamento).val();
	principiosMedPorSeleccionar = princActiMedPorSeleccionar.split(",");
	
	// $('table[id=medProcAgrup_'+codProcedimiento+'] input[id^=ckProcMed]:checked').each(function(){
	$('table[id^=medProcAgrup_] input[id^=ckProcMed]:checked').each(function(){

		codMedicamentoSeleccionado = $(this).val();
		
		idMedicamentoSeleccionado = $(this).attr('id');
		
		
		if(idMedicamentoSeleccionado != "ckProcMed_"+idMedicamento)
		{
			idProcMed = idMedicamentoSeleccionado.split("_");
						
			idMedSeleccionado = idProcMed[1]+"_"+idProcMed[2]+"_"+idProcMed[3]+"_"+idProcMed[4];
			
			princActiMedSeleccionado = $("#princActivProcAgrup_"+idMedSeleccionado).val();
			DescMedSeleccionado = $("#descProc"+idProcMed[1]).val();
			
			principiosMedSeleccionado = princActiMedSeleccionado.split(",");
			
			for(var i = 0; i < principiosMedSeleccionado.length; i++)
			{
				for(var j = 0; j < principiosMedPorSeleccionar.length; j++)
				{
					if(principiosMedPorSeleccionar[j] == principiosMedSeleccionado[i])
					{
						$("#ckProcMed_"+idMedicamento).prop('checked',false);
						
						if(DescMedSeleccionado == descProce)
						{
							jAlert("Los medicamentos seleccionados para el procedimiento "+descProce+" tienen el mismo principio activo y solo debe seleccionar uno");
						}
						else
						{
							jAlert("Uno de los medicamentos seleccionados para el procedimiento "+descProce+" tienen el mismo principio activo que uno de los medicamentos del procedimiento "+DescMedSeleccionado+"  y solo debe seleccionar uno");
						}
						// // jAlert("El medicamento seleccionado para el procedimiento "+descProce+" tiene el mismo principio activo de un medicamento previamente y solo debe seleccionar uno");
						// // jAlert("Los medicamentos seleccionados para el procedimiento "+descProce+" tienen el mismo principio activo y solo debe seleccionar uno");
						// jAlert("Uno de los medicamentos seleccionados para el procedimiento "+descProce+" tienen el mismo principio activo que uno de los medicamentos del procedimiento "+DescMedSeleccionado+"  y solo debe seleccionar uno");
					}
				}
			}
		}
	});
}
function validarMedicamentos(codProcedimiento,desProcedimiento)
{
	var checkeado = $('[id=ckProcedimiento][value='+codProcedimiento+']').prop("checked");
		
	if(checkeado == false)
	{
		var tieneMedSeleccionados = false;
		var medicamentoAplicado = false;
		var i = 0;
		var arrayMedCheck = new Array();
		
		$('table[id=medProcAgrup_'+codProcedimiento+'] input[id^=ckProcMed]').each(function(){
			
			idMedicamento = $(this).attr('id');
			
			idProcMed = idMedicamento.split("_");
							
			idArrayMed = idProcMed[1]+"_"+idProcMed[2]+"_"+idProcMed[3]+"_"+idProcMed[4];
			
			arrayMedCheck[i] = idArrayMed;
			i++;
			
			if( $(this).is(':checked') )
			{
				// $(this).prop('checked',false);
				// $(this).prop("disabled", true);
				
				// arrayMedCheck[i] = idArrayMed;
				// i++;
				tieneMedSeleccionados = true;
				
				
				protocPos = $(this).attr('attrprotpos');
				
				//Validar aplicacion
				$.ajax({
						url: "ordenes.inc.php",
						type: "POST",
						data:{
							consultaAjax:   		'',
							consultaAjaxKardex:   	'73',
							wemp_pmla:     			$("#wemp_pmla").val(),
							basedatos: 	   			$("#wbasedato").val(),
							historia:				$('#whistoria').val(),
							ingreso: 				$('#wingreso').val(),
							medicamento:   			idProcMed[2],
							ido:   					$('#widoriginal'+protocPos).val()
						},
						async: false,
						success:function(respuesta2) {

							if(respuesta2 == "on")
							{
								medicamentoAplicado = true;
								// return;
							}
						}
					}
				);
			}
			else
			{
				$(this).prop("disabled", true);
				$("#DosisAgrupada_"+idArrayMed).prop("disabled", true);
				$("#freMedAgrup_"+idArrayMed).prop("disabled", true);
			}
			
			// $("#DosisAgrupada_"+idArrayMed).prop("disabled", true);
			// $("#freMedAgrup_"+idArrayMed).prop("disabled", true);
			
			
			
		});
		
		//Validar si alguno de los medicamentos fue aplicado
		if(tieneMedSeleccionados == true)
		{
			if(medicamentoAplicado == true)
			{
				$('[id=ckProcedimiento][value='+codProcedimiento+']').prop("checked", true);
				jAlert("No puede cancelar el procedimiento, uno de los medicamentos ya fue aplicado","ALERTA");
			}
			else
			{
				for(var j=0; j < arrayMedCheck.length;j++)
				{
					$("#ckProcMed_"+arrayMedCheck[j]).prop('checked',false);
					$("#ckProcMed_"+arrayMedCheck[j]).prop("disabled", true);
					
					$("#DosisAgrupada_"+arrayMedCheck[j]).prop("disabled", true);
					$("#freMedAgrup_"+arrayMedCheck[j]).prop("disabled", true);
				}
			}
		}
	}
	else
	{
		$('table[id=medProcAgrup_'+codProcedimiento+'] input[id^=ckProcMed]').each(function(){
			
			idMedicamento = $(this).attr('id');
			
			idProcMed = idMedicamento.split("_");
							
			idArrayMed = idProcMed[1]+"_"+idProcMed[2]+"_"+idProcMed[3]+"_"+idProcMed[4];
			
			$(this).prop("disabled", false);
			$("#DosisAgrupada_"+idArrayMed).prop("disabled", false);
			$("#freMedAgrup_"+idArrayMed).prop("disabled", false);
				
		});
	}
	
}
 
function agregarMedicamentoPorProcAgrupado(codArt,frec,via,dosisArt,tipoOrdenAgrup,codProcAgrup,codMedicament)
{
	articulo = codArt;				// Codigo del articulo
	periodo = frec;					// Frecuencias
	administracion = via;			// Vías de administración
	condicion = "";				// Condiciones de suministro
	var dosis = dosisArt;				// Dosis
	var observaciones = "";		// Observaciones

	presentacion = '';			// Presentaciones o formas farmacéuticas
	medida = '';				// Unidades de medida
	// var fechaInicio = '';		// Fecha y hora de inicio de aplicación
	var fechaInicio = $("#whfinicioN999").val();		// Fecha y hora de inicio de aplicación
	var dosisMax = '';			// Dosis a aplicar
	var diasTto = '';			// Días de tratamiento

	parametros = "consultaAjaxKardex=36&wemp_pmla="+document.forms.forma.wemp_pmla.value
				 +"&basedatos="+document.forms.forma.wbasedato.value
				 +"&cenmez="+document.forms.forma.wcenmez.value
				 +"&ccoPaciente="+document.forms.forma.wservicio.value
				 +"&q="+articulo
				 +"&dos="+dosis
				 +"&adm="+administracion;

	try{

		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if( $.trim( ajax.responseText ) != '' ){
			if($.trim( ajax.responseText ) != "No se encontraron coincidencias"){

				var item = $.trim( ajax.responseText ).split( "|" );

//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));

				/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
				 * Argumentos:
				 *
				 * 0: Como se muestra en el autocomplete
				 * 1: Codigo del articulo
				 * 2: Nombre comercial del articulo
				 * 3: Nombre genérico del articulo
				 * 4: Tipo protocolo
				 * 5: (M)edicamento o (L)iquido
				 * 6: Es generico
				 * 7: Origen
				 * 8: Grupo de medicamento
				 * 9: Forma farmaceutica
				 * 10:Unidad
				 * 11:POS
				 * 12:Unidad de fraccion
				 * 13:Cantidad de fracciones
				 * 14:Vencimiento
				 * 15:Dias de estabilidad
				 * 16:Es dispensable
				 * 17:Es duplicable
				 * 18:Dias maximos sugeridos
				 * 19:Dosis maximas sugeridas
				 * 20:Via
				 * 21:Abre ventana parametrizada ----
				 * 22:Cantidad de dosis
				 * 23:Unidad de dosis
				 * 24:No enviar
				 * 25:Frecuencia
				 * 26:Vias de administracion
				 * 27:Condicion de suministro
				 * 28:Confirmada preparacion
				 * 29:Dias maximos tratamiento
				 * 30:Dosis maximas tratamiento
				 * 31:Observaciones adicionales
				 * 32:Componentes del tipo
				 * 33:Nombre personalizado del articulo
				 */

				var codigoArticulo = item[1];
				var nombreComercial = item[2];
				var nombreGenerico = item[3];
				var tipoProtocolo = item[4];
				var tipoMedicamentoLiquido = item[5];
				var esGenerico = item[6];
				var origen = item[7];
				var grupoMedicamento = item[8];
				var formaFarmaceutica = $.trim(item[9]);
				var unidadMedida = item[10];
				var pos = item[11];
				var unidadFraccion = item[12];
				var cantidadFraccion = dosis;	//item[13];
				var vencimiento = item[14];
				var diasEstabilidad = item[15];
				var dispensable = item[16];
				var duplicable = item[17];
				var diasMaximosSugeridos = item[18];
				var dosisMaximasSugeridas = item[19];
				var viaAdministracion = administracion;
				var abreVentanaFija = item[21];
				var cantidadDosisFija = item[22];
				var unidadDosisFija = item[23];
				var noEnviarFija = item[24];
				var frecuenciaFija = periodo;
				var viasAdministracionFija = item[26];
				var condicionSuministroFija = condicion;
				var confirmadaPreparacionFija = item[28];
				var diasMaximosFija = diasTto;
				var dosisMaximasFija = dosisMax;
				var observacionesFija = item[34]+observaciones;
				var componentesTipo = item[32];
				var nombrePersonalizadoDelArticulo = item[33];
				fechaInicioFija = fechaInicio;

				var codPrincipioActivo = $.trim( item[36] );
				var desPrincipioActivo = $.trim( item[35] );
				var esAntibiotico = $.trim( item[37] ) == 'on' ? true: false;

				var	noEnviar = item[33];	//Abril 25 de 2011

				if( esGenerico.length > 0 ){
					duplicable = 'on';
				}


				var idxMulArt = multiplesMedicamentos.length;
				multiplesMedicamentos[idxMulArt] = [];
				var idxMultArt2 = 0
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codigoArticulo;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	reemplazarTodo(nombreComercial," ","_");
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	reemplazarTodo(nombreGenerico," ","_");
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	origen;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	grupoMedicamento;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	formaFarmaceutica;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadMedida;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	pos;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadFraccion;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	cantidadFraccion;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	vencimiento;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasEstabilidad;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dispensable;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	duplicable;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasMaximosSugeridos;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dosisMaximasSugeridas;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	viaAdministracion;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoProtocolo;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoMedicamentoLiquido;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esGenerico;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	abreVentanaFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	cantidadDosisFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadDosisFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	noEnviarFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	frecuenciaFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	viasAdministracionFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	condicionSuministroFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	confirmadaPreparacionFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasMaximosFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dosisMaximasFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	observacionesFija;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	componentesTipo;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	noEnviar;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	'off';
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codPrincipioActivo;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	desPrincipioActivo;
				
				
				// procedimientos agrupados
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoOrdenAgrup;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codProcAgrup;
				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codMedicament;

				multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esAntibiotico;

				
				//Agrego la fecha y hora de inicio del medicamento calculada para el H.E
				multiplesMedicamentos[idxMulArt].fechaInicioFija = 	fechaInicioFija;

				//Se agrega esta propiedad para saber que tipo de articulo se va a agregar
				multiplesMedicamentos[idxMulArt].tipoArt = 	'ProcAgrup';
				
				document.getElementById("btnCerrarVentana").style.display = 'none';

				this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;

			}
		}

		if (!estaEnProceso(ajax)) {
			ajax.send(null);
		}
	}
	catch(e)
	{	
		// console.log("e---->"+e);
	}
	
	// agregarMultiplesArticulos();
}

function pintarTdAgrupado(tipoOrden,tipoProcAgrupado,idxElemento)
{
	contproceAgrupados = tipoProcAgrupado.split(tipoOrden);
	contprocedimientosAgrupados=contproceAgrupados[1];
	
	if(procAgrupados[ tipoProcAgrupado ].length > 0)
	{
			$.ajax({
				url: "ordenes.inc.php",
				type: "POST",
				data:{
					consultaAjax:   		'',
					consultaAjaxKardex:   	'69',
					wemp_pmla:     			$("#wemp_pmla").val(),
					basedatos: 	   			$("#wbasedato").val(),
					tipoOrden:    			tipoOrden,
					wuser:    				$("#usuario").val(),
					wcedula:    			$("#wcedula").val(),
					tipoDocumento:    		$("#wtipodoc").val(),
					agrupados:   			procAgrupados,
					renglon:   				idxElemento,
					contadorProcAgr:   		contprocedimientosAgrupados
				},
				async: false,
				success:function(respuesta2) {
					
					$('#trExAgru'+tipoOrden+contprocedimientosAgrupados).remove();

					if( $( "#tbprocedimientosAgrupados" ).length == 0 ){
						var tbodyTipoOrdenAgrupada = document.createElement( "tbody" );
						tbodyTipoOrdenAgrupada.id = 'tbprocedimientosAgrupados';
					}
					else{
						var tbodyTipoOrdenAgrupada = $( "#tbprocedimientosAgrupados" )[0];
					}

					$( "#encabezadoExamenes" ).after( $( tbodyTipoOrdenAgrupada ) );
					$( tbodyTipoOrdenAgrupada ).prepend( respuesta2 );

					$( "#wfsolAgru"+tipoOrden+contprocedimientosAgrupados ).datepicker({ minDate: -0 });
				}
			}
		);
	}
	else
	{
		$('#trExAgru'+tipoOrden+contprocedimientosAgrupados).remove();
	}
}


function validarCheckMarcado(codProcedimiento,desProcedimiento)
{
	var checkeado = $('[id=ckProcedimiento][value='+codProcedimiento+']').prop("checked");
	var justificacion = $('[id=txaJustificacionProced'+codProcedimiento+'').val();
	
	if(checkeado==false && justificacion != "")
	{
		jAlert("Debe marcar el procedimiento "+desProcedimiento+" si quiere guardarlo","ALERTA");
	}
}

function cambioEstadosProcAgrupados(tipoOrden,contadorActua)
{
	var medSuspendidos = 0;
	var medReactivados = 0;
	
	var mensSuspe = "";
	var mensReact = "";
	
	estadoGeneral = $("#westadoexamenProcAgrup"+tipoOrden+contadorActua).val();

	for( var x in procAgrupados ){
		for (var i = 0; i < procAgrupados[x].length; i++) {

			if( tipoOrden + contadorActua ==  x)
			{
				procAgrupados[x][i].estadoProced =  estadoGeneral;
				$("#westadoexamen"+procAgrupados[x][i].consecutivo).val(procAgrupados[x][i].estadoProced);
				$("#wmodificado4"+procAgrupados[x][i].consecutivo).val("S");

				accionMed = $('option:selected',"#westadoexamenProcAgrup"+tipoOrden+contadorActua).attr('accmed');
				
				//Suspender medicamentos				
				if(accionMed=="SUSPENDER")
				{
					for( var u in procAgrupados[x][i].medicamentos ){
						
						claseSuspend = $("#trFil"+procAgrupados[ x ][i].medicamentos[ u ].posicionActual).attr('class');
											
						if(procAgrupados[ x ][i].medicamentos[ u ].suspendido != "on" && procAgrupados[ x ][i].medicamentos[ u ].suspActual != "on"  && claseSuspend != "suspendido")
						{
							procAgrupados[ x ][i].medicamentos[ u ].suspActual = "on";
							procAgrupados[ x ][i].suspendido = "on";
							suspenderArticulo(procAgrupados[ x ][i].medicamentos[ u ].posicionActual,procAgrupados[ x ][i].medicamentos[ u ].tipoProtocolo, false);
							
							medSuspendidos++;
						}
						
					}
				}
				else
				{
					//reactivar cuando sea un estado diferente a R y C
					if(procAgrupados[ x ][i].suspendido == "on")
					{
						for( var u in procAgrupados[x][i].medicamentos ){
							
							claseSuspend = $("#trFil"+procAgrupados[ x ][i].medicamentos[ u ].posicionActual).attr('class');
							
							if(procAgrupados[ x ][i].medicamentos[ u ].suspendido != "on" && procAgrupados[ x ][i].medicamentos[ u ].suspActual == "on" && claseSuspend == "suspendido")
							{
								suspenderArticulo(procAgrupados[ x ][i].medicamentos[ u ].posicionActual,procAgrupados[ x ][i].medicamentos[ u ].tipoProtocolo, false);
								procAgrupados[ x ][i].medicamentos[ u ].suspActual = "off";
								procAgrupados[ x ][i].suspendido = "off";
								
								medReactivados++;
							}
							
						}
						
					}
				}
				
				//Volver a pintar
				$.ajax({
						url: "ordenes.inc.php",
						type: "POST",
						data:{
							consultaAjax:   		'',
							consultaAjaxKardex:   	'72',
							wemp_pmla:     			$("#wemp_pmla").val(),
							basedatos: 	   			$("#wbasedato").val(),
							historia:				$('#whistoria').val(),
							ingreso: 				$('#wingreso').val(),
							agrupados:   			procAgrupados[ x ],
							tipoOrdenAgrup:   		tipoOrden,
							contadorActualAgrup:   	contadorActua
						},
						async: false,
						success:function(respuesta2) {

							cambios = respuesta2.split("*||*");

							$( "[id^=divCantidadesProcedimiento"+x+"]" ).html( cambios[0] );
							$( "[id^=wtxtjustexamenAgru"+x+"]" ).html( cambios[1] );

							procAgrupados[ x ].estado = 'modificado';
						}
					}
				);
			}
		}
	}
	
	if(medSuspendidos > 0)
	{
		mensSuspe = "- Se suspendieron los medicamentos asociados a esta orden agrupada \n";
	}
	if(medReactivados > 0)
	{
		mensReact = "- Se reactivaron los medicamentos asociados a esta orden agrupada \n";
	}
	
	mensajeFinal = mensSuspe+mensReact;
	
	if(mensajeFinal!="")
	{
		jAlert(mensajeFinal, "ALERTA" );
	}
	
}


function OcultarTrProcedimientos(contadExamenes){
	
	filas = $("#trEx"+contadExamenes).find("td");
	cantfilas = $("#trEx"+contadExamenes).find("td").length;

	//Oculto todos los td de la fila excepto el primero (numero de orden)
	for(var i=1; i<cantfilas; i++)
	{
		$( filas[i]).hide();
	}

	cantfilas2 = $("#trEx"+contadExamenes).find("td:visible").length;
	rowspanTr = $( filas[0]).attr('rowspan');
	if(rowspanTr > 1)
	{
		var trFirst = $( "#trEx"+contadExamenes);
		var cantSiguienteTr = 0;
		for( var iii = 0; iii<rowspanTr-1; iii++ ){
			siguienteTr = trFirst.next();
			cantSiguienteTr = $( siguienteTr).find("td:visible").length;

			if(cantSiguienteTr > 1)
			{
				break;
			}
			trFirst = siguienteTr;
		}
		
		if(cantSiguienteTr > 1)
		{
			$( filas[0]).show();
		}
		else
		{
			$( filas[0]).hide();
		}
	}
	else
	{
		$( filas[0]).hide();
	}
}


function abrirModalProcedimientosAgrupados(TipoOrden,consecutivo,editable,grabado){

	$.post("ordenes.inc.php",
	{
		consultaAjax:   		'',
		consultaAjaxKardex:   	'68',
		wemp_pmla:     			$("#wemp_pmla").val(),
		basedatoshce: 	   		$("#wbasedatohce").val(),
		agrupados: 	   			procAgrupados[TipoOrden+consecutivo], // document.forms.forma.wbasedatohce.value,
		wtipo:    				TipoOrden,
		Grabado:   				grabado,
		contprocedAgrupados:   	consecutivo,
		editable:   			editable,
		wuser:		   			$("#usuario").val(),
		historia:				$('#whistoria').val(),
		ingreso: 				$('#wingreso').val()
		// estado:  			 	procAgrupados[TipoOrden+consecutivo].estado
	}, function(respuesta){

		
		$( "#dvAuxProcedimientosAgrupados" ).html( respuesta );
		
		cadProcMed = $( "#cadenaProcemientosConMed" ).val();
			
		ProcMed = cadProcMed.split("|")
		
		for(var y=0;y<ProcMed.length-1;y++)
		{
			//Tooltip por medicamento
			$( "#MedAgrupado_"+ProcMed[y] ).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
		}
		
		
		var canWidth = $(window).width()*0.8;
		if( $( "#dvAuxProcedimientosAgrupados" ).width()-50 < canWidth )
			canWidth = $( "#dvAuxProcedimientosAgrupados" ).width();

		var canHeight = $(window).height()*0.8;;
		if( $( "#dvAuxProcedimientosAgrupados" ).height()-50 < canHeight )
			canHeight = $( "#dvAuxProcedimientosAgrupados" ).height();

		$.blockUI({ message: $( "#dvAuxProcedimientosAgrupados" ),
			css: { left: ( $(window).width() - canWidth-50 )/2  +'px',
					top: ( $(window).height() - canHeight -50 )/2 +'px',
				  width: canWidth+ 25 + 'px',
				 height: canHeight+ 25 + 'px',
			   overflow: 'auto',
				 cursor: "point"
				 }
		});

	});
}

function grabarProcedimientoAgrupado(){

	for( var x in procAgrupados ){
		if( procAgrupados[x].estado != 'grabado' ){

			var arrayDetalles = {};

			if( !arrayDetalles[x] )
				arrayDetalles[x] = [];

			for (var i = 0; i < procAgrupados[x].length; i++) {

			
				for( var u in procAgrupados[x][i].medicamentos ){
					procAgrupados[x][i].medicamentos[u].ido = $('#widoriginal'+procAgrupados[x][i].medicamentos[u].tipoProtocolo+procAgrupados[x][i].medicamentos[u].posicionActual).val();
				}
				
				consecutivo = procAgrupados[x][i].consecutivo;

				arrayDetalles[x][arrayDetalles[x].length] = {
					tipoOrdenAgrupada:		$('#tipoOrdenAgru'+x).val(),
					tipoOrden:				$('#hexcco'+consecutivo).val(),
					numOrden:				$('#hexcons'+consecutivo).val(),
					numItem:				$('#hexnroitem'+consecutivo).val(),
					CodProcedimiento:		$('#hexcod'+consecutivo).val(),
					cantidad:				procAgrupados[x][i].cantidad,
					estadoProced:			procAgrupados[x][i].estadoProced,
					medicamentos:			procAgrupados[x][i].medicamentos
				}
			}

			var just = $("#wtxtjustexamenAgru"+x).val();
			var justProcedimientos = just.replace("\n", "|");
			
			var imprimir = $("#imprimir_examen_Agru"+x).prop('checked');
			if(imprimir==true)
			{
				imprimir = "on";
			}
			else
			{
				imprimir = "off";
			}

			//Enviar encabezado y array de detalles
			$.ajax({
				url: "ordenes.inc.php",
				type: "POST",
				data:{
					consultaAjax:   		'',
					consultaAjaxKardex:   	'70',
					wemp_pmla:     			$("#wemp_pmla").val(),	// document.forms.forma.wemp_pmla.value,
					basedatos: 	   			$("#wbasedato").val(), // document.forms.forma.wbasedatohce.value,
					historia:				$('#whistoria').val(),
					ingreso: 				$('#wingreso').val(),
					tipoOrden: 				$('#tipoOrdenAgru'+x).val(),
					NroOrden:				$('#NroOrdenAgru'+x).val(),
					fechaOrdAgr:			$( "#wfsolAgru"+x).val(),
					wuser:    				$("#usuario").val(),
					detalles:				arrayDetalles,
					// justificaciones:		$("#wtxtjustexamenAgru"+x).val()
					justificaciones:		justProcedimientos,
					imprimir:				imprimir
					// ayd_cod:    			id_chk
				},
				async: false,
				success:function(respuesta) {

					$('#trExAgru'+x).css("background-color", "#CCF0D2");;
					procAgrupados[x].estado = 'grabado';
				}
			});
		}
	}
}

function quitarExamenAgrupado(tipoOrden,contprocedimientosAgru){

	var confirma = confirm("\xbfDesea eliminar los procedimientos?");
	if (confirma)
	{
		//Quitar examenes
		for( var x in procAgrupados ){
			for (var i = 0; i < procAgrupados[x].length; i++) {

				if( tipoOrden + contprocedimientosAgru ==  x)
				{
					quitarExamen( procAgrupados[x][i].consecutivo,'','on','','on');
					
					for( var u in procAgrupados[x][i].medicamentos ){
										
						//Quitar medicamento
						quitarArticulo(procAgrupados[x][i].medicamentos[ u ].posicionActual,procAgrupados[x][i].medicamentos[ u ].tipoProtocolo,'','detKardexAddN','LQ','','procAgrupEliminado');				
					}
				}
			}
		}

		delete procAgrupados[tipoOrden + contprocedimientosAgru];
		$('#trExAgru'+tipoOrden+contprocedimientosAgru).remove();	//elimino el tr
	}
}

function cambiarEstadosImpresionProcAgrupados()
{
	$( "[id^=checkboxImpProcAgrupado]").each(function(){
		
		tipoOrden = $(this).attr('tipProcAgrup');
		contprocedimientosAgru = $(this).attr('contadorProcAgrupado');
		codProc = $(this).attr('codProcedimiento');
		
		for (var i = 0; i < procAgrupados[tipoOrden+contprocedimientosAgru].length; i++) {
			
			if(procAgrupados[tipoOrden + contprocedimientosAgru][i].codigo  ==  codProc)
			{
				Imprimir=$(this).prop('checked');
		
				PosProcedimiento=procAgrupados[tipoOrden+contprocedimientosAgru][i].consecutivo;
				
				// $("#trEx"+PosProcedimiento+" input:checkbox").click();
				
				if(Imprimir== true)
				{
					$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
					$("#trEx"+PosProcedimiento+" input:checkbox").click();
					$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
				}
				else
				{
					$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
					$("#trEx"+PosProcedimiento+" input:checkbox").click();
					$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
				}
			}
		}
	});
	
	// if($("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo+":visible").length > 0)
		
	$( "[id^=imprimir_examen_Agru]").each(function(){
		var idCheckAgrup =$(this).attr('id');
		idCheckAgrup=idCheckAgrup.replace("imprimir_examen_Agru","");
		// console.log(idCheckAgrup);
		cantVisible = 0;
		$( "[id^=checkboxImpProcAgrupado"+idCheckAgrup+"]").each(function(){
			//$(this).val();
			// console.log($(this));
			// console.log($(this).attr("id"));
			// console.log($(this).is(":visible"));
			// if($(this).is(":visible") == true)
			if($(this).css('display') != "none")
			{
				cantVisible++;
			}
			
		});
		// console.log("cant visible: "+cantVisible)
		if(cantVisible == 0)
		{
			$(this).prop('checked',false);
			$(this).prop('disabled',true);
		}
	  
	});
					
}
// function cambiarEstadosImpresionProcAgrupados(tipoOrden,contprocedimientosAgru)
// {
	
	// if(tipoOrden == "" && contprocedimientosAgru=="")
	// { alert("hey mmmm");
		// $( "[id^=checkboxImpProcAgrupado]").each(function(){
		
			// tipoOrden = $(this).attr('tipProcAgrup');
			// contprocedimientosAgru = $(this).attr('contadorProcAgrupado');
			// codProc = $(this).attr('codProcedimiento');
			
			// for (var i = 0; i < procAgrupados[tipoOrden+contprocedimientosAgru].length; i++) {
				
				// if(procAgrupados[tipoOrden + contprocedimientosAgru][i].codigo  ==  codProc)
				// {
					// Imprimir=$(this).prop('checked');
			
					// PosProcedimiento=procAgrupados[tipoOrden+contprocedimientosAgru][i].consecutivo;
					
					// // $("#trEx"+PosProcedimiento+" input:checkbox").click();
					
					// if(Imprimir== true)
					// {
						// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
						// $("#trEx"+PosProcedimiento+" input:checkbox").click();
						// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
					// }
					// else
					// {
						// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
						// $("#trEx"+PosProcedimiento+" input:checkbox").click();
						// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
					// }
				// }
			// }
		// });
	// }
	// else
	// {
		// for (var i = 0; i < procAgrupados[tipoOrden+contprocedimientosAgru].length; i++) {
				
				// var idCkeckbox = tipoOrden+contprocedimientosAgru+procAgrupados[tipoOrden+contprocedimientosAgru][i].codigo;
				// Imprimir=$("#checkboxImpProcAgrupado"+idCkeckbox).prop('checked');
				
				// PosProcedimiento=procAgrupados[tipoOrden+contprocedimientosAgru][i].consecutivo;
				
				// // $("#trEx"+PosProcedimiento+" input:checkbox").click();
				
				// if(Imprimir== true)
				// {
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
					// $("#trEx"+PosProcedimiento+" input:checkbox").click();
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
				// }
				// else
				// {
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
					// $("#trEx"+PosProcedimiento+" input:checkbox").click();
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
				// }
			
		// }
	// }
	
	
	
// }

function cambiarImprimirIndividualProcedimientosAgrupados(tipoOrden,contprocedimientosAgru,codProc,e){
	// Imprimir=$( "#imprimir_examen_Agru"+tipoOrden+contprocedimientosAgru ).val();
	Imprimir=$( "#checkboxImpProcAgrupado"+tipoOrden+contprocedimientosAgru+codProc ).prop('checked');
	
	
	for( var x in procAgrupados ){
		for (var i = 0; i < procAgrupados[x].length; i++) {
			
			
			
			// if( tipoOrden + contprocedimientosAgru ==  x)
			if( tipoOrden + contprocedimientosAgru ==  x && procAgrupados[x][i].codigo==codProc)
			{
				PosProcedimiento=procAgrupados[x][i].consecutivo;
				// $("#trEx"+PosProcedimiento+" input:checkbox").click();
				// $("#wmodificado4"+PosProcedimiento).val("S");
				// procAgrupados[x].estado = 'modificado';
				
				
				// $("#trEx"+PosProcedimiento+" input:checkbox").removeAttr('checked');
				$("#trEx"+PosProcedimiento+" input:checkbox").click();
				
				if(Imprimir== true)
				{
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
					$("#trEx"+PosProcedimiento+" input:checkbox").click();
					$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
					
					
					// $("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked',true);
					// $("#checkboxImpProcAgrupado"+x).attr('checked',true);
					// marcarImpresionExamen(this,"P01","189162","285100","2016-08-08","1");
				}
				else
				{
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
					$("#trEx"+PosProcedimiento+" input:checkbox").click();
					$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
					
					// $("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked',false);
					// $("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked',false);
				}
				
				// // console.log(x);
				$("#wmodificado4"+PosProcedimiento).val("S");
				procAgrupados[x].estado = 'modificado';
			}
		}
	}
	
	//evita que se abra la modal de procedimientos agrupados cuando cambia el checkbox
	e.stopPropagation();
}

function cambiarImprimirProcedimientosAgrupados(tipoOrden,contprocedimientosAgru){
	// Imprimir=$( "#imprimir_examen_Agru"+tipoOrden+contprocedimientosAgru ).val();
	Imprimir=$( "#imprimir_examen_Agru"+tipoOrden+contprocedimientosAgru ).prop('checked');
	for( var x in procAgrupados ){
		for (var i = 0; i < procAgrupados[x].length; i++) {
			if( tipoOrden + contprocedimientosAgru ==  x)
			{
				PosProcedimiento=procAgrupados[x][i].consecutivo;
				// $("#trEx"+PosProcedimiento+" input:checkbox").click();
				// $("#wmodificado4"+PosProcedimiento).val("S");
				// procAgrupados[x].estado = 'modificado';
				
				
				// $("#trEx"+PosProcedimiento+" input:checkbox").removeAttr('checked');
				// $("#trEx"+PosProcedimiento+" input:checkbox").click();
				
				if(Imprimir== true)
				{
					// $("#trEx"+PosProcedimiento+" input:checkbox").click();
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
					
					
					// $("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked',true);
					if($("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo+":visible").length > 0)
					{
						$("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked',true);
					}
					// if($("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked')==true)
					if($("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo+":visible").length > 0)
					{
						$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
						$("#trEx"+PosProcedimiento+" input:checkbox").click();
						$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',true);
						
						procAgrupados[x][i].imprimir="on";
					}
					
					
					// marcarImpresionExamen(this,"P01","189162","285100","2016-08-08","1");
				}
				else
				{
					
					
					// $("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked',false);
					if($("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo+":visible").length > 0)
					{
						$("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked',false);
					}
					
					// if($("#checkboxImpProcAgrupado"+x+procAgrupados[x][i].codigo).attr('checked')==false)
					// {
						$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
						$("#trEx"+PosProcedimiento+" input:checkbox").click();
						$("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
					// }
					
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
					// $("#trEx"+PosProcedimiento+" input:checkbox").click();
					// $("#trEx"+PosProcedimiento+" input:checkbox").prop('checked',false);
					procAgrupados[x][i].imprimir="off";
				}
				
				// console.log(x);
				$("#wmodificado4"+PosProcedimiento).val("S");
				procAgrupados[x].estado = 'modificado';
				
			}
		}
	}
}

function cambiarFechaProcedimientosAgrupados(tipoOrden,contprocedimientosAgru){
	fecha=$( "#wfsolAgru"+tipoOrden+contprocedimientosAgru ).val();
	for( var x in procAgrupados ){
		for (var i = 0; i < procAgrupados[x].length; i++) {
			if( tipoOrden + contprocedimientosAgru ==  x)
			{
				PosProcedimiento=procAgrupados[x][i].consecutivo;
				$( "#wfsol"+PosProcedimiento ).val(fecha);
				$("#wmodificado4"+PosProcedimiento).val("S");
				procAgrupados[x].estado = 'modificado';
			}
		}
	}
}



function AgregarProcedimientosAgrupado(tipoOrden){
	
	multiplesMedicamentos = [];

	var contador=0;
	var posInputs = -1;
	adicionMultiple = true;
	$.unblockUI();
	
	var msg = "";
	var medEliminados = "";
	
	var arrMedQuitar = [];
	var contMedQuitar = 0;
	
	var inputChecks = $('table[id^=tablaProcedimientosAgrupados]').find('input[id=ckProcedimiento]');
	
	var totalChecks = inputChecks.length;

	inputChecks.each( funcInternaProcedimientosAgregados );

	function funcInternaProcedimientosAgregados(){

		posInputs++;

		if( $(this).is(':checked') )
		{
			var id_chk = $(this).val();
			var cantidad= $("#cantProcAgru"+id_chk).val();
			var estadoProced= $("#estProcAgru"+id_chk).val();
			var justificacion = "";
			var imprimir = "";
			
			if( $(this).hasClass('ProcedimientoConJustificacion') )
			{
				justificacion = $("#txaJustificacionProced"+id_chk).val();
			}
			
			var arrMedicamentos = {};
			contMed = 0;
			$('table[id=medProcAgrup_'+id_chk+'] input[id^=ckProcMed]:checked').each(function(){
		
			   
			    codMedicamento = $(this).val();
			    idMedicamento = $(this).attr('id');
				
				idProcMed = idMedicamento.split("_");
								
				idArrayMed = idProcMed[1]+"_"+idProcMed[2]+"_"+idProcMed[3]+"_"+idProcMed[4];
				
				codMedicamento = $("#ckProcMed_"+idArrayMed).val();
				dosisMed = $("#DosisAgrupada_"+idArrayMed).val();
				frecuen = $("#freMedAgrup_"+idArrayMed).val();
				viaMed = $("#viaProcAgrup_"+idArrayMed).val();
				
				arrMedicamentos[idArrayMed] ={
					codMed:	codMedicamento,
					dosis: dosisMed,
					frecuencia: frecuen,
					via: viaMed
				}
			});
			
			// modificar procedimientos
			procedimientoAgrupadoRegistrado=false;
			for( var x in procAgrupados ){
				for (var i = 0; i < procAgrupados[x].length; i++) {

					if( tipoOrden + contprocedimientosAgrupados ==  x)
					{
						if( procAgrupados[x][i].codigo ==  id_chk)
						{
							procAgrupados[x][i].cantidad =  cantidad;
							procAgrupados[x][i].justificacion =  justificacion;
							procAgrupados[x][i].estadoProced =  estadoProced;
							
							$("#wtxtjustexamen"+procAgrupados[x][i].consecutivo).val(justificacion);

							//Modificar y  agregar
							for( var z in arrMedicamentos ){
								//Condicion de modificar
								if( procAgrupados[x][i].medicamentos[z] ) {
									//modificar array
									procAgrupados[x][i].medicamentos[z].codMed = arrMedicamentos[z].codMed;
									procAgrupados[x][i].medicamentos[z].dosis = arrMedicamentos[z].dosis;
									procAgrupados[x][i].medicamentos[z].frecuencia = arrMedicamentos[z].frecuencia;
									
									//modificar campos en la pestaña medicamentos
									$("#wdosis"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual).val(procAgrupados[x][i].medicamentos[z].dosis);
									$("#wperiod"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual).val(procAgrupados[x][i].medicamentos[z].frecuencia);
								}
								else
								{
									procAgrupados[x][i].medicamentos[z] = arrMedicamentos[z];
									
									agregarMedicamentoPorProcAgrupado(procAgrupados[x][i].medicamentos[z].codMed,procAgrupados[x][i].medicamentos[z].frecuencia,procAgrupados[x][i].medicamentos[z].via,procAgrupados[x][i].medicamentos[z].dosis,x,i,z);
								}
							}
							
							for( var u in procAgrupados[x][i].medicamentos ){
								//Elimina
								if( !arrMedicamentos[u] ){
									
									arrMedQuitar[contMedQuitar] = x+"|"+i+"|"+u;
									
									descMedi = $("#descrMedProAgrupGener_"+u).val();
									
									medEliminados += descMedi+",";
									
									contMedQuitar++;
								}
							}
							
							procedimientoAgrupadoRegistrado=true;
						}
					}
				}
			}

			//agregar procedimiento
		    if(procedimientoAgrupadoRegistrado==false)
		    {
				// console.log("agregar");
				nuevo="on";
				imprimir = $("#imprimirProcAgrup"+id_chk).val();
				
				procAgrupados[tipoOrden + contprocedimientosAgrupados][procAgrupados[tipoOrden + contprocedimientosAgrupados].length] = {
					consecutivo: cuentaExamenes,
					codigo: id_chk,
					justificacion: justificacion,
					cantidad: cantidad,
					estadoProced: estadoProced,
					tipo: tipoOrden,
					imprimir: imprimir,
					medicamentos: arrMedicamentos
				}

				contador=procAgrupados[tipoOrden + contprocedimientosAgrupados].length-1;

				$.ajax({
					url: "ordenes.inc.php",
					type: "POST",
					data:{
						consultaAjax:   		'',
						consultaAjaxKardex:   	'38',
						wemp_pmla:     			$("#wemp_pmla").val(),	// document.forms.forma.wemp_pmla.value,
						basedatos: 	   			$("#wbasedato").val(), // document.forms.forma.wbasedatohce.value,
						ayd_cod:    			id_chk
					},
					async: false,
					success:function(respuesta) {
// console.log(respuesta);
						var aydItem = respuesta.split( "|" );
// console.log(aydItem);
						var antCuentaExamenes = cuentaExamenes;
						
						seleccionarAyudaDiagnostica(aydItem[1],aydItem[2],aydItem[3],aydItem[4],aydItem[5],aydItem[6],aydItem[7],aydItem[8],aydItem[9],aydItem[10],aydItem[11],aydItem[12],justificacion,funcInternaOcultar);

						function funcInternaOcultar(){
							
							//Si son iguales significa que no se agrego examen
							//Por tanto se quita del arreglo procAgrupados
							if( antCuentaExamenes == cuentaExamenes ){
								procAgrupados[ tipoOrden + contprocedimientosAgrupados ].splice(contador,1);
								// contador--;
							}
							else
							{
								//Si agrego examen hay que ocultar el examen agregado
								OcultarTrProcedimientos(cuentaExamenes-1);
							}
							
														
							
							//medicamentos
							for( var x in procAgrupados ){
								for (var i = 0; i < procAgrupados[x].length; i++) {

									if( tipoOrden + contprocedimientosAgrupados ==  x)
									{
										if( procAgrupados[x][i].codigo ==  id_chk)
										{
											for( var u in procAgrupados[x][i].medicamentos ){
												
												idReal=u;
												
												agregarMedicamentoPorProcAgrupado(procAgrupados[x][i].medicamentos[u].codMed,procAgrupados[x][i].medicamentos[u].frecuencia,procAgrupados[x][i].medicamentos[u].via,procAgrupados[x][i].medicamentos[u].dosis,x,i,u);
											}
										}
									}
								}
							}
							
							funcInternPintarProcAgru();
							$('table[id^=tablaProcedimientosAgrupados]').find('input[id=ckProcedimiento]:gt('+posInputs+')').each( funcInternaProcedimientosAgregados );
							
						}
					}
				});

				return false;
			}
			// contador++ ;
		}
		else
		{
			var id_chk = $(this).val();

			var procedimientoAgrupadoDes=true;
			var procedimientoSinCheck="";
				for( var x in procAgrupados ){
					for (var i = 0; i < procAgrupados[x].length; i++) {

						if( tipoOrden + contprocedimientosAgrupados ==  x)
						{
							if( procAgrupados[x][i].codigo ==  id_chk)
							{
								procedimientoAgrupadoDes=false;
								procedimientoSinCheck=i;
								
								break;
							}
						}
					}
				}

			if(procedimientoAgrupadoDes==false)
			{
				for( var u in procAgrupados[tipoOrden + contprocedimientosAgrupados][procedimientoSinCheck].medicamentos ){
									
					//Quitar medicamento
					quitarArticulo(procAgrupados[ tipoOrden + contprocedimientosAgrupados ][procedimientoSinCheck].medicamentos[ u ].posicionActual,procAgrupados[ tipoOrden + contprocedimientosAgrupados ][procedimientoSinCheck].medicamentos[ u ].tipoProtocolo,'','detKardexAddN','LQ','','procAgrupEliminado');				
					
					descMedi = $("#descrMedProAgrupGener_"+u).val();
									
					medEliminados += descMedi+",";
				}	
				
				quitarExamen( procAgrupados[tipoOrden + contprocedimientosAgrupados][procedimientoSinCheck].consecutivo,'','on','','on');
				procAgrupados[ tipoOrden + contprocedimientosAgrupados ].splice(procedimientoSinCheck,1);
			}
			funcInternPintarProcAgru();
		}
	}


	function funcInternPintarProcAgru()
	{
		if(procAgrupados[ tipoOrden + contprocedimientosAgrupados ].length > 0)
		{
				$.ajax({
					url: "ordenes.inc.php",
					type: "POST",
					data:{
						consultaAjax:   		'',
						consultaAjaxKardex:   	'69',
						wemp_pmla:     			$("#wemp_pmla").val(),
						basedatos: 	   			$("#wbasedato").val(),
						tipoOrden:    			tipoOrden,
						wuser:    				$("#usuario").val(),
						wcedula:    			$("#wcedula").val(),
						tipoDocumento:    		$("#wtipodoc").val(),
						agrupados:   			procAgrupados[ tipoOrden + contprocedimientosAgrupados ],
						renglon:   				cuentaExamenes,
						contadorProcAgr:   		contprocedimientosAgrupados
					},
					async: false,
					success:function(respuesta2) {


						$('#trExAgru'+tipoOrden+contprocedimientosAgrupados).remove();

						if( $( "#tbprocedimientosAgrupados" ).length == 0 ){
							var tbodyTipoOrdenAgrupada = document.createElement( "tbody" );
							tbodyTipoOrdenAgrupada.id = 'tbprocedimientosAgrupados';
						}
						else{
							var tbodyTipoOrdenAgrupada = $( "#tbprocedimientosAgrupados" )[0];
						}

						$( "#encabezadoExamenes" ).after( $( tbodyTipoOrdenAgrupada ) );
						$( tbodyTipoOrdenAgrupada ).prepend( respuesta2 );

						$( "#wfsolAgru"+tipoOrden+contprocedimientosAgrupados ).datepicker({ minDate: -0 });

						procAgrupados[ tipoOrden+contprocedimientosAgrupados ].estado = '';
					}
				}
			);
			
		}


		if( totalChecks-1 == posInputs ){
			
			if( multiplesMedicamentos.length > 0 )
				agregarMultiplesArticulos();
			else
				if(strPendientesCTC!=""){
					abrirCTCMultiple();
				}
			
			//Mostrar mensajes
			if(medEliminados!="")
			{
				if(arrMedQuitar.length > 0)
				{
					for(var h=0;h < arrMedQuitar.length; h++)
					{
						//Quitar del array
						quitar = arrMedQuitar[h].split("|");
						quitarArticulo(procAgrupados[quitar[0]][quitar[1]].medicamentos[quitar[2]].posicionActual,procAgrupados[quitar[0]][quitar[1]].medicamentos[quitar[2]].tipoProtocolo,'','detKardexAddN','LQ','','procAgrupEliminado');
					}
					// cadenaMensajeProcAgrupados += "Se eliminó el medicamento: "+$.trim(medEliminados.substr(0,medEliminados.length-1))+"\n";
					
					// buscElim = cadenaMensajeProcAgrupados.search("Se eliminó el medicamento: "+$.trim(medEliminados.substr(0,medEliminados.length-1))+"\n");
					// if(buscElim == -1)
					// {
						
						// cadenaMensajeProcAgrupados += "Se eliminó el medicamento: "+$.trim(medEliminados.substr(0,medEliminados.length-1))+"\n";
					// }
				}
				
			}
		}
	}
}




function ModificarProcedimientosAgrupado(tipoOrden,contadorActual){

	multiplesMedicamentos = [];

	var medEliminados = "";
	var medSuspendidos = "";
	var medReactivados = "";

	var mensElimi = "";
	var mensSuspe = "";
	var mensReact = "";
	
	var AgregarMed = false;


	$('table[id^=tablaProcedimientosAgrupados]').find('input[id=ckProcedimiento]').each(function(){

		var id_chk = $(this).val();
		var cantidad= $("#cantProcAgru"+id_chk).val();
		var estadoProced= $("#estProcAgru"+id_chk).val();
		var justificacion = "";

		if( $(this).hasClass('ProcedimientoConJustificacion') )
		{
			justificacion = $("#txaJustificacionProced"+id_chk).val();
		}

		if( $(this).is(':checked') )
		{
			// modifica
			procedimientoAgrupadoRegistrado=false;
			for( var x in procAgrupados ){
				for (var i = 0; i < procAgrupados[x].length; i++) {

					if( tipoOrden + contadorActual ==  x)
					{
						if( procAgrupados[x][i].codigo ==  id_chk)
						{
							procAgrupados[x][i].cantidad =  cantidad;
							procAgrupados[x][i].justificacion =  justificacion;
							procAgrupados[x][i].estadoProced =  estadoProced;

							if(procAgrupados[x][i].estadoProced == "C")
							{
								procAgrupados[x][i].estadoProced =  procAgrupados[x][i].estadoAnterior;
							}

							$("#wtxtjustexamen"+procAgrupados[x][i].consecutivo).val(justificacion);
							$("#westadoexamen"+procAgrupados[x][i].consecutivo).val(procAgrupados[x][i].estadoProced);
							$("#wmodificado4"+procAgrupados[x][i].consecutivo).val("S");

							var arrMedicamentos = {};
							contMed = 0;
							$('table[id=medProcAgrup_'+id_chk+'] input[id^=ckProcMed]:checked').each(function(){
						
								codMedicamento = $(this).val();
								idMedicamento = $(this).attr('id');
								
								idProcMed = idMedicamento.split("_");
												
								idArrayMed = idProcMed[1]+"_"+idProcMed[2]+"_"+idProcMed[3]+"_"+idProcMed[4];
								
								codMedicamento = $("#ckProcMed_"+idArrayMed).val();
								dosisMed = $("#DosisAgrupada_"+idArrayMed).val();
								frecuen = $("#freMedAgrup_"+idArrayMed).val();
								viaMed = $("#viaProcAgrup_"+idArrayMed).val();
								
								arrMedicamentos[idArrayMed] ={
									codMed:	codMedicamento,
									dosis: dosisMed,
									frecuencia: frecuen,
									via: viaMed
								}
							});
														
							//Modificar y  agregar
							for( var z in arrMedicamentos ){
								//Condicion de modificar
								if( procAgrupados[x][i].medicamentos[z] )
								{
									//modificar array
									procAgrupados[x][i].medicamentos[z].codMed = arrMedicamentos[z].codMed;
									procAgrupados[x][i].medicamentos[z].dosis = arrMedicamentos[z].dosis;
									procAgrupados[x][i].medicamentos[z].frecuencia = arrMedicamentos[z].frecuencia;
									
									claseSuspend = $("#trFil"+procAgrupados[ x ][i].medicamentos[ z ].posicionActual).attr('class');
									
									//quitar suspendido
									if(procAgrupados[ x ][i].medicamentos[ z ].suspActual == "on" && claseSuspend == "suspendido")
									{
										suspenderArticulo(procAgrupados[ x ][i].medicamentos[ z ].posicionActual,procAgrupados[ x ][i].medicamentos[ z ].tipoProtocolo, false);
										procAgrupados[ x ][i].medicamentos[ z ].suspActual = "off";
										descMedi = $("#descrMedProAgrupGener_"+z).val();
										medReactivados += descMedi+",";
									}
									
									//modificar campos en la pestaña medicamentos
									$("#wdosis"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual).val(procAgrupados[x][i].medicamentos[z].dosis);
									$("#wperiod"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual).val(procAgrupados[x][i].medicamentos[z].frecuencia);
									
									$("#wmodificado"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual).val("S");
								}
								else
								{
									procAgrupados[x][i].medicamentos[z] = arrMedicamentos[z];
									
									agregarMedicamentoPorProcAgrupado(procAgrupados[x][i].medicamentos[z].codMed,procAgrupados[x][i].medicamentos[z].frecuencia,procAgrupados[x][i].medicamentos[z].via,procAgrupados[x][i].medicamentos[z].dosis,x,i,z);

									AgregarMed = true;	
									
									// campos en la pestaña medicamentos de solo lectura
									$("#wdosis"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual).attr('readonly', true);
									$("#wperiod"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual).attr('disabled', true);
									$("#wviadmon"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual).attr('disabled', true);
									
									$( "#tbDetalleAddN [idtr=trFil"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual+"] a, #tbDetalleAddU [idtr=trFil"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual+"] a, #tbDetalleAddLQ [idtr=trFil"+procAgrupados[x][i].medicamentos[z].tipoProtocolo+procAgrupados[x][i].medicamentos[z].posicionActual+"] a ").remove();
								}
							}
							
							for( var u in procAgrupados[x][i].medicamentos ){
								//Elimina
								if( !arrMedicamentos[u] ){
									
									//Validar si esta agregado o grabado
									if($('table[id=tbDetalleAdd'+procAgrupados[x][i].medicamentos[u].tipoProtocolo+'] input[id=wnmmed'+procAgrupados[x][i].medicamentos[u].tipoProtocolo+procAgrupados[x][i].medicamentos[u].posicionActual+']').length > 0)
									{
										//Eliminar
										quitarArticulo(procAgrupados[ x ][i].medicamentos[ u ].posicionActual,procAgrupados[ x ][i].medicamentos[ u ].tipoProtocolo,'','detKardexAddN','LQ','','procAgrupEliminado');
										descMedi = $("#descrMedProAgrupGener_"+u).val();
										medEliminados += descMedi+",";
										//Mostrar mensajes
										
									}
									else
									{
										//Suspender
										claseSuspend = $("#trFil"+procAgrupados[ x ][i].medicamentos[ u ].posicionActual).attr('class');
										
										if(claseSuspend != "suspendido")
										{
											suspenderArticulo(procAgrupados[ x ][i].medicamentos[ u ].posicionActual,procAgrupados[ x ][i].medicamentos[ u ].tipoProtocolo, false);
											descMedi = $("#descrMedProAgrupGener_"+u).val();
											medSuspendidos += descMedi+",";
											procAgrupados[ x ][i].medicamentos[ u ].suspActual = "on";
										}
									}	
								}
							}
							
							procedimientoAgrupadoRegistrado=true;
							break;
						}
					}
				}
			}
		}
		else
		{
			// cancela 
			var id_chk = $(this).val();

			var procedimientoAgrupadoDes=true;
			var procedimientoSinCheck="";
			for( var x in procAgrupados ){
				for (var i = 0; i < procAgrupados[x].length; i++) {

					if( tipoOrden + contadorActual ==  x)
					{
						if( procAgrupados[x][i].codigo ==  id_chk)
						{
							if( procAgrupados[x][i].estadoProced != "C")
							{
								procAgrupados[x][i].estadoAnterior = procAgrupados[x][i].estadoProced;
							}

							procAgrupados[x][i].cantidad =  cantidad;
							procAgrupados[x][i].justificacion =  justificacion;
							procAgrupados[x][i].estadoProced =  "C";

							$("#wtxtjustexamen"+procAgrupados[x][i].consecutivo).val(justificacion);
							$("#westadoexamen"+procAgrupados[x][i].consecutivo).val(procAgrupados[x][i].estadoProced);
							$("#wmodificado4"+procAgrupados[x][i].consecutivo).val("S");

							
							for( var u in procAgrupados[x][i].medicamentos ){
								//Elimina
								
								claseSuspend = $("#trFil"+procAgrupados[ x ][i].medicamentos[ u ].posicionActual).attr('class');
									
								if(claseSuspend != "suspendido")
								{
									suspenderArticulo(procAgrupados[ x ][i].medicamentos[ u ].posicionActual,procAgrupados[ x ][i].medicamentos[ u ].tipoProtocolo, false);
									
									descMedi = $("#descrMedProAgrupGener_"+u).val();
									medSuspendidos += descMedi+",";
									
									procAgrupados[ x ][i].medicamentos[ u ].suspActual = "on";
								}
							}
							
							procedimientoAgrupadoDes=false;
							procedimientoSinCheck=i;
							break;
						}
					}
				}
			}
		}
	});

	$.unblockUI();

	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{
				consultaAjax:   		'',
				consultaAjaxKardex:   	'72',
				wemp_pmla:     			$("#wemp_pmla").val(),
				basedatos: 	   			$("#wbasedato").val(),
				historia:				$('#whistoria').val(),
				ingreso: 				$('#wingreso').val(),
				agrupados:   			procAgrupados[ tipoOrden+contadorActual ],
				tipoOrdenAgrup:   		tipoOrden,
				contadorActualAgrup:   	contadorActual
			},
			async: false,
			success:function(respuesta2) {

				cambios = respuesta2.split("*||*");

				$( "[id^=divCantidadesProcedimiento"+tipoOrden+contadorActual+"]" ).html( cambios[0] );
				$( "[id^=wtxtjustexamenAgru"+tipoOrden+contadorActual+"]" ).html( cambios[1] );

				procAgrupados[ tipoOrden+contadorActual ].estado = 'modificado';
			}
		}
	);
	
	agregarMultiplesArticulos();
	
	if(medEliminados!="")
	{
		mensElimi = "- Se eliminaron los siguientes medicamentos: "+$.trim(medEliminados.substr(0,medEliminados.length-1))+"\n";
	}
	if(medSuspendidos!="")
	{
		mensSuspe = "- Se suspendieron los siguientes medicamentos: "+$.trim(medSuspendidos.substr(0,medSuspendidos.length-1))+"\n";
	}
	if(medReactivados!="")
	{
		mensReact = "- Se reactivaron los siguientes medicamentos: "+$.trim(medReactivados.substr(0,medReactivados.length-1))+" previamente suspendidos \n";
	}
	
	if(AgregarMed == false)
	{
		mensajeFinal = mensSuspe+mensReact;
	}
	else
	{
		mensajeFinal = mensElimi+mensSuspe+mensReact;
	}
	
	// // mensajeFinal = mensElimi+mensSuspe+mensReact;
	// mensajeFinal = mensSuspe+mensReact;
	
	if(mensajeFinal!="")
	{
		// jAlert("Se eliminó el medicamento: "+medEliminados.substr(0,medEliminados.length-1), "ALERTA" );
		
		if(AgregarMed == false)
		{
			jAlert(mensajeFinal, "ALERTA" );
			cadenaMensajeProcAgrupados += mensElimi;
		}
		else
		{
			// cadenaMensajeProcAgrupados += mensElimi;
			cadenaMensajeProcAgrupados += mensajeFinal;
		}
		// jAlert(mensajeFinal, "ALERTA" );
		
		// // cadenaMensajeProcAgrupados += mensajeFinal;
		// cadenaMensajeProcAgrupados += mensElimi;
	}
}


function GrabarProcedimientosAgrupado(tipoOrden,contador){

	if(procAgrupados[ tipoOrden+contador ] === undefined)
	{
		if( !procAgrupados[tipoOrden + contador] )
		procAgrupados[tipoOrden + contador] = [];
	}

	if(procAgrupados[ tipoOrden+contador ].estado == "grabado" || procAgrupados[ tipoOrden+contador ].estado == "modificado")
	{
		ModificarProcedimientosAgrupado(tipoOrden,contador);
	}
	else
	{
		AgregarProcedimientosAgrupado(tipoOrden);
		cambiarEstadosImpresionProcAgrupados();
	}
	
	$('#wselTipoServicio').val($('option:first'));
}

function validarSeleccionProcedimientoAgrupado(tipoOrden,contador){
	
	mensajeAg = "";
	i = 0;
	
	descProcJustificacion="";
	descProcMedicamento="";
	descDosisProcMedicamento="";
	descFrecProcMedicamento="";
	
	$('table[id^=tablaProcedimientosAgrupados]').find('input[id=ckProcedimiento]:checked').each(function(){
		
		var id_chk = $(this).val();
		
		// no existe
		i++;
		
		if($("#cantProcAgru"+id_chk).val() == "")
		{
			$("#cantProcAgru"+id_chk).val(1);
		}
	
		if( $(this).hasClass('ProcedimientoConJustificacion') )
		{
			justificacion = $("#txaJustificacionProced"+id_chk).val();

			if(justificacion == "" || typeof(justificacion) === "undefined")
			{
				descProcJustificacion += $("#descProc"+id_chk).val() +",";
			}
		}
		
		if($('table[id=medProcAgrup_'+id_chk+'] input[id^=ckProcMed]').length > 0)
		{
			if($('table[id=medProcAgrup_'+id_chk+'] input[id^=ckProcMed]:checked').length == 0)
			{
				valMedOblig = $("#medObligatorio"+id_chk).val();
				estadoProc = $("#estProcAgru"+id_chk).attr('estPendiente');
				
				//Validacion de seleccion de medicamento
				// if(valMedOblig=="on")
				if(valMedOblig=="on" && estadoProc=="on")
				{
					descProcMedicamento+= $("#descProc"+id_chk).val() +",";
				}
			}
			else
			{
				$('table[id=medProcAgrup_'+id_chk+'] input[id^=ckProcMed]').each(function(){
				
					if( $(this).is(':checked') )
					{
						codMedicamento = $(this).val();
						idMedicamento = $(this).attr('id');
						
						idProcMed = idMedicamento.split("_");
										
						idArrayMed = idProcMed[1]+"_"+idProcMed[2]+"_"+idProcMed[3]+"_"+idProcMed[4];
						
						dosis = $("#DosisAgrupada_"+idArrayMed).val();
						
						if(dosis == "")
						{
							descDosisProcMedicamento+= $("#descrMedProAgrupGener_"+idArrayMed).val() +",";
						}
						
						frecuencia = $("#freMedAgrup_"+idArrayMed).val();
						// console.log("dosis: "+dosis);
						if(frecuencia == "")
						{
							descFrecProcMedicamento+= $("#descrMedProAgrupGener_"+idArrayMed).val() +",";
						}
					}
				});
			}
		}
		
	});
	
	if(descProcJustificacion != "")
	{
		descProcJustificacion = "- Debe ingresar una justificacion para: "+descProcJustificacion.substr(0,descProcJustificacion.length-1)+"\n\n";
	}
	if(descProcMedicamento != "")
	{
		descProcMedicamento = "- Debe seleccionar un medicamento para: "+descProcMedicamento.substr(0,descProcMedicamento.length-1)+"\n\n";
	}
	if(descDosisProcMedicamento != "")
	{
		descDosisProcMedicamento = "- Debe ingresar la dosis para el medicamento: "+descDosisProcMedicamento.substr(0,descDosisProcMedicamento.length-1)+"\n\n";
	}
	if(descFrecProcMedicamento != "")
	{
		descFrecProcMedicamento = "- Debe seleccionar una frecuencia para el medicamento: "+descFrecProcMedicamento.substr(0,descFrecProcMedicamento.length-1)+"\n\n";
	}
	
	mensajeAg = descProcJustificacion+descProcMedicamento+descDosisProcMedicamento+descFrecProcMedicamento;
	
	// tipoOrden+contador
	// if(i > 0 || procAgrupados[tipoOrden+contador].estado != ""){
	if(i > 0 || (procAgrupados[tipoOrden+contador] !== undefined && procAgrupados[tipoOrden+contador].estado != "")){
		
		if(mensajeAg == "")
		{
			GrabarProcedimientosAgrupado(tipoOrden,contador);
		}
		else
		{
			jAlert(mensajeAg,"ALERTA");
		}
	}
	else
	{
		jAlert("Debe seleccionar al menos un procedimiento","ALERTA");
	}
}


function cancerlarModalProcedimientosAgrupados(){

	$.unblockUI();
	$('#wselTipoServicio').val($('option:first'));
}

function validarTipoOrdenAgrupada(){

	var tipoServicioSel = $("#wselTipoServicio").val();
	var Agrupada=0;

	for (var i=0; i<TiposOrdenesAgrupadas.length; i++)
	{
		if(tipoServicioSel==TiposOrdenesAgrupadas[i])
		{
			Agrupada = 1;
			break;
		}
	}

	if(Agrupada==1)
	{
		$.post("ordenes.inc.php",
		{
			consultaAjax:   		'',
			consultaAjaxKardex:   	'68',
			wemp_pmla:     			$("#wemp_pmla").val(),	// document.forms.forma.wemp_pmla.value,
			basedatoshce: 	   		$("#wbasedatohce").val(), // document.forms.forma.wbasedatohce.value,
			agrupados: 	   			procAgrupados[tipoServicioSel+contprocedimientosAgrupados], // document.forms.forma.wbasedatohce.value,
			wtipo:    				tipoServicioSel,
			Grabado:   				"off",
			contprocedAgrupados:   	contprocedimientosAgrupados,
			editable:   			"0",
			wuser:		   			$("#usuario").val(),
			historia:				$('#whistoria').val(),
			ingreso: 				$('#wingreso').val()
			// estado:  			 	procAgrupados[tipoServicioSel+contprocedimientosAgrupados].estado
		}, function(respuesta){

			$( "#dvAuxProcedimientosAgrupados" ).html( respuesta );
			
			cadProcMed = $( "#cadenaProcemientosConMed" ).val();
			
			ProcMed = cadProcMed.split("|")
			
			for(var y=0;y<ProcMed.length-1;y++)
			{
				//Tooltip por medicamento
				$( "#MedAgrupado_"+ProcMed[y] ).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
			}
			
			
			//var canWidth = 800;
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxProcedimientosAgrupados" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxProcedimientosAgrupados" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxProcedimientosAgrupados" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxProcedimientosAgrupados" ).height();

			$.blockUI({ message: $( "#dvAuxProcedimientosAgrupados" ),
				css: { left: ( $(window).width() - canWidth-50 )/2  +'px',
						top: ( $(window).height() - canHeight -50 )/2 +'px',
					  width: canWidth+ 25 + 'px',
					 height: canHeight+ 25 + 'px',
				   overflow: 'auto',
					 cursor: "point"
					 }
			});
		});
	}
	else
	{
		autocompletarParaConsultaDiagnosticas();
	}
	
}


/*--*******************************************************************************
 * Valida si debe o no mostrar el boton de confirmar dextrometer
 ********************************************************************************/
function mostrarConfirmarDextrometer(){
	
	var artDex = $( "#wdexins" ).val()
	var freDex = $( "#wdexfrecuencia" ).val()
	var esqDex = $( "#wdexesquema" ).val()

	var contenedorDosis = $( "#cntEsquema table" )
		if( contenedorDosis.length == 0 ){
			contenedorDosis = $( "#cntEsquemaActual table" );
		}

	var tieneDosis = $("[id^=wdexint][value!=0]", contenedorDosis ).length;

	if( artDex != '' && freDex != '' && esqDex != '' && tieneDosis > 0 ){
	  $( "#btConfirmaDextrometer" ).css({display:""})
	}
	else{
	  $( "#btConfirmaDextrometer" ).css({display:"none"})
	}
}

/********************************************************************************************************
 * Evento onclick del boton eliminar dextrometer
 * Elimina el esquema dextrometer
 ********************************************************************************************************/
function borrarEsquemaDextrometer(){
	
	$( "#wdexins" ).val( '' );
	$( "#wdexfrecuencia" ).val( '' );
	
	$( "#wdexesquema" ).val('');
	$( "#wdexesquema" ).change();
}

/********************************************************************************************************************************************
 *Esta función se llama cada vez que se elimina un articulo o no se desea agregar el articulo a la orden medica
 ********************************************************************************************************************************************/
function quitarArticuloDextrometer( frecuencia, codigoArticulo ){
	/****************************************************************************************
	 * Si el articulo es del dextrometer
	 * No se deja poner la opción del dextrometer
	 *****************************************************************************************/
	//Es del dextrometer si es el mismo articulo del dextrometer y tiene la misma frecuencia
	if( $( "#wdexins" ).val() == codigoArticulo && $( "#wdexfrecuencia" ).val() == frecuencia ){
		$( "#wdexins" ).val( '' );
		$( "#wdexins" ).change();
	}
	/*****************************************************************************************/
}

/**
 * Adiciona la insulina al kardex
 */
function adicionarArticuloInsulina(){

	var agregarInsulina = false;

	var artIns  = $( "#wdexins" );

	//codigo de la insulina para poder buscar el medicamento
	var familia = $( "option[value="+artIns.val()+"]", artIns ).data('familia');

	var dexFrecuencia = $( "#wdexfrecuencia" );
	var frecuencia = dexFrecuencia.val();
	
	var contenedorDosis = $( "#cntEsquema table" )
	if( contenedorDosis.length == 0 ){
		contenedorDosis = $( "#cntEsquemaActual table" );
	}

	/********************************************************************************
	 * Solo se puede agregar una insulina si tiene frecuencia, dosis y articulo
	 ********************************************************************************/
	if( familia && familia != '' ){

		if( frecuencia && frecuencia != '' ){
			//Se calcula la dosis, esta es la menor mayor a 0 del esquema
			var dosis = 0;
			$( "[id^=wdexint]", contenedorDosis ).filter( "[value!=0]" ).each(function(x){
				if( x > 0 ){
					if( this.value*1 < dosis )
						dosis = this.value*1;
				}
				else{
					dosis = this.value*1;
				}
			});

			if( dosis*1 > 0 ){
				agregarInsulina = true;
			}
		}
	}

	//Busco si existe una insulina ya agregada
	//De ser así la suspendido si ya ha sido grabado con anterioridad
	//o eliminado si no ha sido grabado
	var frecs = [];
	$( "option", dexFrecuencia ).filter("[value]").each(function(){
		if( $( this ).val() != '' ){
			// var frec = $( "[id^=wperiod]:not([id^=wperiodori])" ).filter("[value="+this.value+"]");
			var frecBuscada = this.value;
			var frec = $( "[id^=wperiod]:not([id^=wperiodori])" ).filter(function( idx ){
				return this.value == frecBuscada;
			});
			frec.each(function(){
				frecs[frecs.length] = this;
			});
		}
	});

	$( frecs ).each(function(){

	    var frec = this;

		//Si se encuentra se suspende el articulo
		if( frec.length > 0 ){

			var protocolo = frec.id.substr( "wperiod".length,1 );
			var idx = frec.id.substr( "wperiod".length+1 );
			var clase_asociada = $('#trFil'+idx).attr("class");

			var idOriginal = $( "#widoriginal"+protocolo+idx ).val()

			//Solo se suspende si ha sido grabado
			var susperderArtInsulina = true;
			if( agregarInsulina ){

				//Se actualiza los campos necesarios solo si es el mismo articulo
				var artGrabado = $( "#wnmmed"+protocolo+idx );
				//Si es un div es que fue grabado
				var codigoArticulo = "";
				if( artGrabado[0].tagName.toLowerCase() == 'div' ){
					codigoArticulo = artGrabado.html().split("-")[0];
				}
				else{
					codigoArticulo = artGrabado.val().split("-")[0];
				}

				codigoArticulo = codigoArticulo.toUpperCase();

				//Si es el mismo articulo entonces se actualiza los campos
				if( codigoArticulo == artIns.val() ){

					susperderArtInsulina = false;

					//Si el articulo está suspendido, lo activo
					if( clase_asociada == 'suspendido' ){
						suspenderArticulo(idx, protocolo,false);
					}

					//Si se va a actualizar la insulina no hay por que agregar uno nuevo
					agregarInsulina = false;

					//Se debe actulizar dosis, frecuencia, condicion y observaciones
					//Se actualiza dosis
					if( dosis != $( "#wdosis"+protocolo+idx ).val() ){
						$( "#wdosis"+protocolo+idx ).val( dosis );
						$( "#wdosis"+protocolo+idx ).change();
					}

					//Agregando frecuencia por defecto
					//Para ello agrego la frecuencia ya que no existe en las frecuencias del buscador de articulos
					var optionFre = $( "option[value="+frecuencia+"]", dexFrecuencia ).clone();
					if( frecuencia != $( "#wperiod"+protocolo+idx ).val() ){
						//Se actualiza frecuencia
						//Borro la frecuencia actual
						$( "#wperiod"+protocolo+idx+" option" ).remove();

						$( "#wperiod"+protocolo+idx ).append( optionFre );
						$( "#wperiod"+protocolo+idx ).val( frecuencia );
						$( "#wperiod"+protocolo+idx ).change();
					}

					//Actualizo condicion
					var conArtInsulina = $( "#wcondicion"+protocolo+idx+" option" );
					conArtInsulina.css({display:"none"});
					conArtInsulina.attr({disabled:true});

					if( optionFre.data( "condicion" ) != '' && optionFre.data( "condicion" ) != $( "#wcondicion"+protocolo+idx ).val() ){
						$( "#wcondicion"+protocolo+idx ).val( optionFre.data( "condicion" ) );

						conArtInsulina.filter("[value="+optionFre.data( "condicion" )+"]" ).css({display:""});
						conArtInsulina.filter("[value="+optionFre.data( "condicion" )+"]" ).attr({disabled:false});
						
						$( "#wcondicion"+protocolo+idx ).change();
					}
					else{
						conArtInsulina.eq(0).css({display:""});
						conArtInsulina.eq(0).attr({disabled:false});
					}
					
					//Agregando observaciones
					if( $( "textarea[id=wtxtobsadd"+protocolo+idx+"]" ).length > 0 ){
						if( !$( "#txtDextrometer" )[0].readOnly ){
							if( $( "#txtDextrometer" )[0].__valAnterior != $( "#txtDextrometer" ).val() ){
								$( "textarea[id=wtxtobsadd"+protocolo+idx+"]" ).val( $( "#txtDextrometer" ).val() );
								$( "textarea[id=wtxtobsadd"+protocolo+idx+"]" ).change();
							}
						}
					}
					else{
						if( !$( "#txtDextrometer" )[0].readOnly ){
							if( $( "#txtDextrometer" )[0].__valAnterior != $( "#txtDextrometer" ).val() ){
								$( "#wtxtobs"+protocolo+idx ).val( $( "#txtDextrometer" ).val() );
								$( "#wtxtobs"+protocolo+idx ).change();
							}
						}
					}
				}
			}

			if( susperderArtInsulina ){
				if( idOriginal > 0 ){
					//Solo se supende si el articulo no ha sido suspendido
					if( clase_asociada != 'suspendido' ){
						suspenderArticulo(idx, protocolo,false);
					}
				}
				else{
					//Se trata de eliminar todos las insulinas que se hallan agregado nuevas
					//Elimino el articulo
					
					var artGrabado = $( "#wnmmed"+protocolo+idx );
					var codigoArticulo = "";
					if( artGrabado[0].tagName.toLowerCase() == 'div' ){
						codigoArticulo = artGrabado.html().split("-")[0];
					}
					else{
						codigoArticulo = artGrabado.val().split("-")[0];
					}
					
					//Elimino el articulo
					var trEliminar = $( "[idtr=trFil"+protocolo+idx+"]" );
					trEliminar.remove();
					
					// quitarArticuloDextrometer( frec.value, codigoArticulo );
				}
			}
		}
	});

	if( agregarInsulina ){

		//variable global para saber si se está agregando por dextrometer
		articuloPorDextrometer = true;

		//Se agrega el articulo
		
		//Agrego las observaciones al buscador
		$( "#wtxtobservasiones" ).val(  $( "#txtDextrometer" ).val()  );

		$("#wnombrefamilia").val( familia );
		//Se busca por codigo de familia
		$("#wnombrefamilia").search();

		//La función continua en la función result del campo con id wnombrefamilia del buscador de familia
		//cuando la variable global articuloPorDextrometer es true
	}
	
	return agregarInsulina;
}

/******************************************************************************************
 * Evento onchange de la insulina
 ******************************************************************************************/
function seleccionarInsulina( cmp ){

	var tbEsquemaDextromter = $( "#cntEsquema" );

	//Busco todos las vias por defecto de la insulina y desabilito todos los options
	//y los oculto
	var opVias = $( "[id^=wdexselvia] option", tbEsquemaDextromter );

	//Obtengo el option seleccionado
	var opSelected = $( "option:selected", cmp );
	var mostrarVias = false;

	if( opSelected ){

		//Si el option tiene data-vias se continua
		if( opSelected.data( "vias") ){
			//Obtengo las vias por defecto que puedan tener
			//Las vias por defecto están como atributos data
			var viasPorDefecto = opSelected.data( "vias").split( "," );

			if( viasPorDefecto ){

				opVias.css({display:"none"})
				opVias.attr({disabled:true})

				$( viasPorDefecto ).each( function(){
					//this para este caso es el codigo de la via
					//Se muestran solo las vias que puedan ser seleccionadas segun el medicamento
					opVias.filter( "[value="+this+"]" ).css({display:""})
					opVias.filter( "[value="+this+"]" ).attr({disabled:false})
				});
			}
			else{
				mostrarVias = true;
			}
		}
		else{
			mostrarVias = true;
		}
	}
	else{
		mostrarVias = true;
	}

	if( mostrarVias ){
		//Si no hay vias se muestra todas las vias
		opVias.css({display:""})
		opVias.attr({disabled:false})
	}


	//Sigo un procedimiento similar para la unidad de dosis
	//Busco todos las vias por defecto de la insulina y desabilito todos los options
	//y los oculto
	var opUnidad = $( "[id^=wdexseludo] option", tbEsquemaDextromter );

	//Obtengo el option seleccionado
	var mostrarUnidades = false;

	if( opSelected ){

		//Si el option tiene data-unidad se continua
		if( opSelected.data( "unidad") ){
			//Obtengo las unidades por defecto que puedan tener
			//Las unidades por defecto que están como atributos data
			var unidadPorDefecto = opSelected.data( "unidad").split( "," );

			if( unidadPorDefecto ){

				opUnidad.css({display:"none"})
				opUnidad.attr({disabled:true})

				$( unidadPorDefecto ).each( function(){
					//this para este caso es el codigo de la unidad
					//Se muestran solo las unidades que puedan ser seleccionadas segun el medicamento
					opUnidad.filter( "[value="+this+"]" ).css({display:""})
					opUnidad.filter( "[value="+this+"]" ).attr({disabled:false})
				});
			}
			else{
				mostrarUnidades = true;
			}
		}
		else{
			mostrarUnidades = true;
		}
	}
	else{
		mostrarUnidades = true;
	}

	if( mostrarUnidades ){
		//Si no hay unidades se muestra todas las unidades de medida
		opUnidad.css({display:""})
		opUnidad.attr({disabled:false})
	}	
	
	if( opSelected[0].value == '' ){
		$( "#wdexesquema" ).attr({disabled:true});
	}
	else{
		$( "#wdexesquema" ).attr({disabled:false});
	}
	
	$( "#wdexesquema" ).val( '' );
	$( "#wdexesquema" ).change();
}

function setValorRango( cmp ){
	cmp.prevValue = cmp.value;
}

function changeRangoDextrometer( cmp ){

	var val = false;

	var tbEsquemaDextromter = $( "#cntEsquema" );

	//Obtengo la posicion del campo del dextrometer que se va a cambiar
	var indexCmp = $( "[id^=wdexRan]", tbEsquemaDextromter ).index( cmp );

	// Si es un rango minimo siempre será par
	var esRangoMin = indexCmp % 2 == 0 ? true: false;

	if( cmp.value > indexCmp-2 ){

		//Invierto la cadena para seguir un orden
		var minRangos = $( "[id^=wdexRan]:lt("+indexCmp+")", tbEsquemaDextromter ).get().reverse();
		var maxRangos = $( "[id^=wdexRan]:gt("+indexCmp+")", tbEsquemaDextromter );

		//Mientras el rango sea menor cambio el valor
		var valorMin = cmp.value*1;
		$( minRangos ).each(function(x){
			if( x < minRangos.length-2 ){

				if( x == 0 && esRangoMin ){
					valorMin--;
					this.value = valorMin;
				}
				else{
					if( valorMin <= this.value ){
						valorMin--;
						this.value = valorMin;
					}
					else{
						return;
					}
				}
			}
			else{
				return;
			}
		});

		//Busco todos los rangos superiores
		//Estos siempre deben ser mayores a su antecesor
		var valorMax = cmp.value*1;
		$( maxRangos ).each( function(x){

			if( x == 0 && !esRangoMin ){
				valorMax++;
				this.value = valorMax;
			}
			else{
				if( valorMax >= this.value ){
					valorMax++;
					this.value = valorMax;
				}
				else{
					return;
				}
			}
		});

		cmp.prevValue = cmp.value;

		val = true;
	}
	else{
		cmp.value = cmp.prevValue
	}

	return val;
}

function validarAccionesDextrometer(){

	var tbEsquemaDextromter = $( "#cntEsquema" );

	var btEliminarDex = $( "[name=btDexEliminar]", tbEsquemaDextromter );

	//Para la primera fila que se ve en pantalla no muestro el boton de eliminar
	//No se coge el de posicion 0 por que ese corresponde al campo oculto
	if( btEliminarDex.length == 2 ){
		$( "[name=btDexEliminar]", tbEsquemaDextromter ).eq(1).css({display:'none'});
	}
}

/**************************************************************************************************************
 * Agregar filas nuevas para el dextrometer
 **************************************************************************************************************/
function agergarFilaDextormeter(){

	var tbEsquemaDextromter = $( "#cntEsquema" );

	//Consulto los últimos campos minimo y máximo qué habían
	//ESto para colocar el rango correcto al insertar una fila
	var lastMin = $( "[id^=wdexRanMin]:last", tbEsquemaDextromter );
	var lastMax = $( "[id^=wdexRanMax]:last", tbEsquemaDextromter );

	var filaDextomter = $( "#filaCero", tbEsquemaDextromter );

	//Clono la fila del dextromter

	filaNuevaDextrometer = filaDextomter.clone();
	filaNuevaDextrometer[0].id = '';
	filaNuevaDextrometer[0].style.display = '';

	var cont = $( "#contDextrometerPred" ).val();
	//Cambio el id de todos los campos del dextrometer agregando el consecutivo
	$( "[id]", filaNuevaDextrometer ).each(function(x){
		if( this.id != '' ){
			this.id += cont;
		}
	});

	$( "#contDextrometerPred" ).val( $( "#contDextrometerPred" ).val()*1+1 );

	$( "tbody", tbEsquemaDextromter ).append( filaNuevaDextrometer );

	//Igualo los valores al de la fila inicial
	$( "[id^=wdexselvia]", filaNuevaDextrometer[0] ).val( $( "[id^=wdexselvia]", filaDextomter ).val() ) ;

	//Consulto los nuevos campos de maximo y minimo agregado
	var newMin = $( "[id^=wdexRanMin]", filaNuevaDextrometer );
	var newMax = $( "[id^=wdexRanMax]", filaNuevaDextrometer );

	//Por defecto se colo un rango igual al último rango que se tenía
	//comenzando con un rango mínimo que sería el último rango mayor + 1
	newMin.val( lastMax.val()*1+1 );
	newMax.val( newMin.val()*1+lastMax.val()*1-lastMin.val() );

	$( "[id^=wdexint]",filaNuevaDextrometer ).change(function(){
		mostrarConfirmarDextrometer()
	});
	
	validarAccionesDextrometer();
}

/**************************************************************************************************************
 * Elimina una fila del dextromter
 **************************************************************************************************************/
function eliminarFilaDextrometer( cmp ){

	var fila = cmp.parentNode.parentNode;

	//Se reacomodan todos los rangos mayores a la posicion de la fila a elminar
	//mantiendo el rango por fila pero cambiando sus valores minimos
	//según el último rango mayor

	var tbEsquemaDextromter = $( "#cntEsquema" );

	//Busco el rango minimo
	var delMin = $( "[id^=wdexRanMin]", fila );

	//Consulto cuál es el valor para el rango mínimo
	var rangoMin = delMin.val()*1-1;

	//Consulto la posicion del elemento en todos los rangominimos que halla
	var posDelMax = $( "[id^=wdexRan]", tbEsquemaDextromter ).index( delMin )+1;

	var lastRango = 0;
	//Consulto todos los elementos mayores al indice encontrado
	$( "[id^=wdexRan]:gt("+posDelMax+")", tbEsquemaDextromter ).each(function(x){

		//Cuando x es par es un rango Minimo, de lo contrario es maximo
		if( x%2 == 0 ){
			lastRango = this.value
			rangoMin++
			this.value = rangoMin;
		}
		else{
			rangoMin += this.value*1 - lastRango;
			this.value = rangoMin;
		}
	});

	//Remuevo la fila correspondiente
	$( fila ).remove();

	validarAccionesDextrometer();
}

//********************************************************************************
//* CTC PENDIENTES - CAMBIO DE RESPONSABLE
// ********************************************************************************/
function abrirCTCMultipleParaExamenesGrabados(cadenaExamenesGrabadosSinCTC,cadenaCTCExamenesGuardados)
{
	guardarCadenaExamenesGrabadosSinCTC=cadenaCTCExamenesGuardados;

	var filasNoPos = cadenaExamenesGrabadosSinCTC.split( ";" );

	console.log(cadenaExamenesGrabadosSinCTC);
	console.log(filasNoPos);
	console.log(filasNoPos.length-1);

	if(filasNoPos.length-1 > 0)
	{
		var arrNoPos = filasNoPos[0].split( "," );

		if(arrNoPos.length-1 > 0)
		{
			setTimeout( function(){ mostrarCtcProcedimientos2(  arrNoPos[0],  arrNoPos[1], cadenaExamenesGrabadosSinCTC, cadenaCTCExamenesGuardados ) }, 600 );
		}
	}
}

//Mostrar ctc procedimientos para examenes sin ctc por cambio de responsable
function mostrarCtcProcedimientos2( codExamen, cuentaExamenes, cadenaExamSinCTC, cadenaCTCExamGuardado ){
	var parametros = "";

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var wemp_pmla = document.forms.forma.wemp_pmla.value;

	parametros = "wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fecha+"&codExamen="+codExamen+"&idExamen="+cuentaExamenes+"&cadenaExamSinCTC="+cadenaExamSinCTC+"&cadenaCTCExamGuardado="+cadenaCTCExamGuardado;

	try{

		ajax=nuevoAjax();

		ajax.open("POST", "generarCTCProcedimientos.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if( $.trim( ajax.responseText ) != '' ){

			//Creo el div que contendrá todos los ctc de procedimientos
			if( !document.getElementById('ctcProcedimientos') ){
				var divAux = document.createElement( "div" );

				divAux.innerHTML = "<div id='ctcProcedimientos' style='display:none'>"
				                 + "<INPUT TYPE='hidden' name='hiProcsNoPos' id='hiProcsNoPos' value=''>"
								 + "</div>";

				document.forms[0].appendChild(divAux.firstChild);
			}

			document.getElementById('hiProcsNoPos').value += ',' + codExamen + '-' +cuentaExamenes;

			if( !document.getElementById('ctcProcTemp') ){

				var divAux = document.createElement( "div" );

				divAux.style.display = 'none';
				divAux.style.width = '80%';
				divAux.id = 'ctcProcTemp';
			}
			else{
				var divAux = document.getElementById('ctcProcTemp');
			}

			divAux.innerHTML = $.trim( ajax.responseText );

			document.forms[0].appendChild(divAux);

			$.blockUI({ message: $('#ctcProcTemp'),
						css: {
							top:  '5%',
							left: '10%',
							width: '80%',
							height: '90%',
							overflow: 'auto',
							cursor: 'auto'
						}
			});
		}

	}
	catch(e){
		//alert( "Error: " + e );
		$.unblockUI();
	}
}

//Traer justificacion para el ctc que se muestra desde el reporte de impresion
function traeJustificacionHCE2(campoChk,campoDestino,historia,ingreso,wemp_pmla,wbasedatohce){

	var campoJustificacion = document.getElementById(campoDestino);

	if(campoChk.checked == false)
	{
		if(confirm('Desea borrar el texto de la justificación?'))
		{
			campoJustificacion.value = '';
		}
		else
		{
			campoChk.checked = true;
		}
		return false;
	}

	var parametros = "consultaAjaxKardex=46&wemp_pmla="+wemp_pmla+"&basedatoshce="+wbasedatohce+"&whistoria="+historia+"&wingreso="+ingreso;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				if($.trim( ajax.responseText )!="")
				{
					if(campoChk.checked)
					{
						if(campoJustificacion.value=='' || campoJustificacion.value==' ')
							campoJustificacion.value = $.trim( ajax.responseText );
						else
							campoChk.checked = false;
					}
					else
					{
						campoJustificacion.value = '';
					}
				}
				else
				{
					// jAlert('No se encontró resumen de historía clínica para el paciente','ALERTA');
					alert('No se encontr\u00F3 resumen de histor\u00EDa cl\u00EDnica para el paciente');
					campoChk.checked = false;
				}
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

//Traer diagnostico para el ctc que se muestra desde el reporte de impresion
function traerDiagnosticoHCE2(campoChk,campoDestino,historia,ingreso,wemp_pmla,wbasedatohce){

	var campoDiagnostico = document.getElementById(campoDestino);

	if(campoChk.checked == false)
	{
		if(confirm('Desea borrar el texto del diagnostico?'))
		{
			campoDiagnostico.value = '';
		}
		else
		{
			campoChk.checked = true;
		}
		return false;
	}

	var parametros = "consultaAjaxKardex=61&wemp_pmla="+wemp_pmla+"&whce="+wbasedatohce+"&whis="+historia+"&wing="+ingreso;

	try{

		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				if($.trim( ajax.responseText )!="")
				{
					if(campoChk.checked)
					{
						if(campoDiagnostico.value=='' || campoDiagnostico.value==' ')
							campoDiagnostico.value = $.trim( ajax.responseText );
						else
							campoChk.checked = false;
					}
					else
					{
						campoDiagnostico.value = '';
					}
				}
				else
				{
					// jAlert('No se encontró diagnostico de historía clínica para el paciente','ALERTA');
					alert('No se encontr\u00F3 diagnostico de histor\u00EDa cl\u00EDnica para el paciente');
					campoChk.checked = false;
				}
			}
			//$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

function abrirCTCMultipleParaArticulosGrabados(cadenaGrabadosSinCTC,cadenaCTCGuardado)
{
	guardarCadenaGrabadosSinCTC=cadenaCTCGuardado;

	var filasNoPos = cadenaGrabadosSinCTC.split( ";" );

	console.log(cadenaGrabadosSinCTC);
	console.log(filasNoPos);
	console.log(filasNoPos.length-1);

	if(filasNoPos.length-1 > 0)
	{
		var arrNoPos = filasNoPos[0].split( "," );

		if(arrNoPos.length-1 > 0)
		{
			setTimeout( function(){ mostrarCtcArticulos3( arrNoPos[0], arrNoPos[1], arrNoPos[2], arrNoPos[3], 'off', cadenaGrabadosSinCTC, cadenaCTCGuardado ) }, 600 );
		}
	}
}

//Mostrar ctc medicamentos para articulos sin ctc por cambio de responsable
function mostrarCtcArticulos3( codArticulo, protocolo, id, idFamilia, deAlta, cadenaSinCTC, cadenaCTCGuardado  ){
	cadena = cadenaSinCTC;
	var idx = protocolo+id;

	if(deAlta && deAlta=="on")
		var txtImp = "Imp";
	else
		var txtImp = "";

	//Se verfica si tiene horario especial, si tiene horario especial solo se muestra
	//el ctc si es el último medicamento
	var hayMasHE = 0;

	for( var i = 2; i <= 24; i += 2 ){

		if( document.getElementById( 'dosisRonda' + i ) && document.getElementById( 'dosisRonda' + i ).value != '' ){

			hayMasHE++;
		}
	}

	//Si es uno significa que es el ultimo y si es 0 significa que no tenía horario especial
	if( hayMasHE == 1 || hayMasHE == 0 ){

		//Creo un objeto con todas las propiedades necesarias
		var inCodArtsCTC = { art: '',
							 inf: [],
							 idxs:[]
						   };

		iddivDetalles="divDetalle"+protocolo+(parseInt(idFamilia)+0);
		iddivDetalles2="#divDetalle"+protocolo+(parseInt(idFamilia)+0);

		var tbsContenedores = new Array( iddivDetalles, "tbDetalleAddImp"+protocolo );
		var tbsContenedores = [ $( "table", $(iddivDetalles2) )[0] ];

		for( var x = 0; x < tbsContenedores.length; x++ ){

			//Buscar todos los medicamentos que tengan el mismo código
			// var tbContenedor = $( "#"+tbsContenedores[x] )[0];	//Busco la tabla que contiene los medicamentos
			var tbContenedor = tbsContenedores[x];	//Busco la tabla que contiene los medicamentos

			if( tbContenedor ){		//Abril 15 de 2014
				/****************************************************************************************************
				 * Busco todos los input de la tabla contenedor
				 * y los guardo en un array
				 ****************************************************************************************************/
				var inCodigos = tbContenedor.getElementsByTagName( "div" );

				for( var i = 0; i < inCodigos.length; i++ ){

					//Si comineza el id con wnmmed, significa que es un campo con codigo del articulo
					if( inCodigos[i].id.substr( 0, 6 ) == "wnmmed" ){

						//Solo busco aquellos que tengan el mismo codigo
						//El codigo esta separado por un guion (-)
						if( inCodigos[i].innerHTML.substr( 0, inCodigos[i].innerHTML.indexOf( "-" ) ) == codArticulo ){
							//Guardo el campo en un array
							inCodArtsCTC.art = codArticulo;		//Codigo del articulo

							inCodArtsCTC.inf[ inCodArtsCTC.inf.length ] = {};

							var idAux = inCodigos[i].id.substr( 6 );

							inCodArtsCTC.idxs[ inCodArtsCTC.idxs.length ] = idAux;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].frecuencia = document.getElementById("wperiod"+idAux).value;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].dosis = document.getElementById("wdosis"+idAux).value;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].canManejo = document.getElementById("whcmanejo"+idAux).value;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].dosisMaxima = document.getElementById("wdosmax"+idAux).value;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].diasTto = document.getElementById("wdiastto"+idAux).value;

							var origen = document.getElementById( "wnmmed"+idAux ).innerHTML.split( "-" )[1];
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].origen = origen;

							var fechaHoraInicio = document.getElementById( 'wfinicio'+idAux ).value.split( " a las:" );

							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].fin = fechaHoraInicio[0];		//FEcha de inicio del medicamento
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].hin = fechaHoraInicio[1];		//Hora de inicio del medicamento
						}
					}
				}
				/****************************************************************************************************/
			}
		}

		/****************************************************************************************************
		 * Verifico que no exista un CTC para el articulo
		 * Si ya tiene un CTC debo abrir el formulario encontrado y modificar los campos que sean necesarios
		 ****************************************************************************************************/
		divEncontrado = false;

		var dvCtcAux = document.getElementById('ctcArticulos');

		if( dvCtcAux ){

			var dvs = dvCtcAux.getElementsByTagName( "div" );

			for( var i = 0; i < dvs.length; i++ ){
				if( dvs[i].id.substr( 0, dvs[i].id.indexOf( "-" ) ) == "dv"+codArticulo ){
					divEncontrado = dvs[i];
				}
			}
		}
		/****************************************************************************************************/

		frecuencia = document.getElementById("wperiod"+idAux).value;
		//Ahora creo una url con los parametros encontrados
		//Para ello recorro todo el objeto que requiero
		var parametros = "";

		if( inCodArtsCTC.inf.length > 0 ){

			for( var i = 0; i < inCodArtsCTC.inf.length; i++ ){

				for( var x in inCodArtsCTC.inf[i] ){
					parametros += "&" + x + "[" + i + "]=" + inCodArtsCTC.inf[ i ][ x ];
				}
			}
		}

		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;
		var fecha = document.forms.forma.wfecha.value;
		var wemp_pmla = document.forms.forma.wemp_pmla.value;
		var dosis_medico_aux = $("#dosis_medico_aux").val();
		var cadenaArticulosSinCTC = cadenaSinCTC;

		parametros = "wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fecha+"&codArticulo="+codArticulo+"&idx="+idx + parametros +"&protocolo="+protocolo+"&id="+id+"&dosis_medico_aux="+dosis_medico_aux+"&cadenaArtSinCTC="+cadenaArticulosSinCTC+"&cadenaCTCGuardados="+cadenaCTCGuardado;

		try{

			ajax=nuevoAjax();

			ajax.open("POST", "./generarCTCArticulos.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if( $.trim( ajax.responseText ) != '' ){

				if( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) ){
					document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ).parentNode.removeChild( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) );
				}

				//Creo el div que contendrá todos los ctc de procedimientos
				if( !document.getElementById('ctcArticulos') ){
					var divAux = document.createElement( "div" );

					divAux.innerHTML = "<div id='ctcArticulos' style='display:none'>"
									 + "<INPUT TYPE='hidden' name='hiArtsNoPos' id='hiArtsNoPos' value=''>"
									 + "</div>";

					document.forms[0].appendChild(divAux.firstChild);
				}

				document.getElementById('hiArtsNoPos').value += ',' + codArticulo + '-' +idx;

				if( !document.getElementById('ctcArtsTemp') ){

					var divAux = document.createElement( "div" );

					divAux.style.display = 'none';
					divAux.style.width = '80%';
					divAux.id = 'ctcArtsTemp';
				}
				else{
					var divAux = document.getElementById('ctcArtsTemp');
				}

				divAux.innerHTML = $.trim( ajax.responseText );

				//Si ya había un CTC igualo todos los campos al Ctc encontrado
				if( divEncontrado ){

					//Campos que necesito igualar
					var camposBuscar = new Array( "select", "input", "textarea" );

					//Recorro el array de los campos segun las etiquetas
					for( var i = 0; i < camposBuscar.length; i++ ){

						//Busco todas las etiquetas segun el array
						var varCmps = divAux.getElementsByTagName( camposBuscar[i] );

						//Recorro cada uno de los campos encontrados
						for( var j = 0; j < varCmps.length; j++ ){

							//Si el campo tiene un name
							//Significa que lo debo igualar al CTC que ya había encontrado
							//A excepción de posología y dosis por día
							if( varCmps[j].name != '' && varCmps[j].name != "posologiaNoPos" && varCmps[j].name != "ddNoPos" ){

								if( camposBuscar[i] != 'input'
									|| camposBuscar[i] == 'input'
									&& ( varCmps[j].type.toLowerCase() != "radio" && varCmps[j].type.toLowerCase() != "checkbox" )
								){
									//Busco el campo con el  mismo nombre en el CTC Anterior e igualos sus valores
									varCmps[j].value = $( "[name="+varCmps[j].name+"]", divEncontrado ).val();
								}
								else{

									switch( varCmps[j].type.toLowerCase() ){

										case 'radio':

											$( "[name="+varCmps[j].name+"]", divEncontrado ).each(
												function(k){
													if( varCmps[j].value == $(this).val() ){
														varCmps[j].checked = $(this).is(':checked');
													}
												}
											);

											// if( varCmps[j].checked && varCmps[j].value == $( "[name="+varCmps[j].name+"]", divEncontrado ).val() ){
												// varCmps[j].checked = true;
											// }
										break;

										case 'checkbox':
											varCmps[j].checked = $( "[name="+varCmps[j].name+"]", divEncontrado )[0].checked;
										break;
									}
								}
							}
						}
					}

					//Despues de replicar los datos, borra el div anterior
					try{
						// divEncontrado.parentNode.removeChild(divEncontrado);
					}
					catch(e){}
				}


				//Busco el campo Tiempo de tratamiento para agregar el objeto con el que se calcula la cantidad
				$( "[name=tiempoTratamientoNoPos]", divAux )[0].objArts = inCodArtsCTC;
				$( "input[value='Salir sin guardar']", divAux )[0].objArts = inCodArtsCTC;

				document.forms[0].appendChild(divAux);

				//agrego el medicamento que tiene CTC a la variable global
				// arCTCArticulos[ codArticulo ] = inCodArtsCTC.idxs.length;	//lo igualo con el total de articulos a grabar

				//Si ya había un CTC igualo todos los campos al Ctc encontrado
				if( divEncontrado ){

					//Recalculo la cantidad
					$( "[name=tiempoTratamientoNoPos]", divAux )[0].onchange();
				}

				$.blockUI({ message: $('#ctcArtsTemp'),
							css: {
								top:  '5%',
								left: '10%',
								width: '80%',
								height: '90%',
								overflow: 'auto',
								cursor: 'auto'
							}
				});
			}
		}
		catch(e){
			// alert( "Error: " + e );
			$.unblockUI();
		}
	}

}

/********************************************************************************
 * Indica si para un Lev o IC ya existe un insumo
 * Recibe el codigo del Lev y del insumo
 ********************************************************************************/
function existeInsumoParaLEVIC( codLev, insCod ){

	val = false;

	for( x in artLevs ){
		if( artLevs[ x ].insEst != 'del' ){
			if( artLevs[ x ].codLev == codLev && artLevs[ x ].insCod == insCod ){
				val = true;
				break;
			}
		}
	}

	return val;
}

function inhabilitarDiasTratamientoModalLEV( cmp ){

	if( $( "#waccN\\.12" ).eq(0).val().split(",")[0] == 'N' ){	//Días de tratamiento
		$( "#inDmaxLEV", $( "#listaComponentesLEV" ) )[0].readOnly = true;
		$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).val('');
		return;
	}

	if( cmp.value != '' ){
		$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).attr({ readOnly: true });
	}
	else{
		$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).attr({ readOnly: false });
	}
}

function inhabilitarDosisMaximaModalLEV( cmp ){

	if( $( "#waccN\\.12" ).eq(0).val().split(",")[0] == 'N' ){	//Días de tratamiento
		$( "#inDttoLEV", $( "#listaComponentesLEV" ) )[0].readOnly = true;
		$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).val('');
		return;
	}

	if( cmp.value != '' ){
		$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).attr({ readOnly: true });
	}
	else{
		$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).attr({ readOnly: false });
	}
}

function inhabilitarDiasTratamientoModalIC( cmp ){

	if( $( "#waccN\\.13" ).eq(0).val().split(",")[0] == 'N' ){	//Dosis máxima
		$( "#inDmaxIC", $( "#listaComponentesIC" ) )[0].readOnly = true;
		$( "#inDmaxIC", $( "#listaComponentesIC" ) ).val( '' );
		return;
	}

	if( cmp.value != '' ){
		$( "#inDttoIC", $( "#listaComponentesIC" ) ).attr({ readOnly: true });
	}
	else{
		$( "#inDttoIC", $( "#listaComponentesIC" ) ).attr({ readOnly: false });
	}
}

function inhabilitarDosisMaximaModalIC( cmp ){

	if( $( "#waccN\\.12" ).eq(0).val().split(",")[0] == 'N' ){	//Días de tratamiento
		$( "#inDttoIC", $( "#listaComponentesIC" ) )[0].readOnly = true;
		$( "#inDttoIC", $( "#listaComponentesIC" ) ).val('');
		return;
	}

	if( cmp.value != '' ){
		$( "#inDmaxIC", $( "#listaComponentesIC" ) ).attr({ readOnly: true });
	}
	else{
		$( "#inDmaxIC", $( "#listaComponentesIC" ) ).attr({ readOnly: false });
	}
}

/**
 * Deshabilita todos los campos de IC
 */
function deshabilitarSolucionesIC( cmp ){

	if( cmp.checked ){
		$( "[id^=tdCbSol]", $( "#listaComponentesIC" ) ).each(function(){
			var idx = this.id.substr(7);
			$( "#check_insumo"+idx, $( "#listaComponentesIC" ) )[0].checked = false;
			$( "#check_insumo"+idx, $( "#listaComponentesIC" ) ).change();

			$( "#check_insumo"+idx, $( "#listaComponentesIC" ) ).attr({disabled:true});

			$( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).attr({disabled:true});
			$( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).val('');
		});
	}
	else{
		$( "[id^=tdCbSol]" ,$( "#listaComponentesIC" ) ).each(function(){
			var idx = this.id.substr(7);
			$( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).attr({disabled:false, readOnly:false});
			$( "#check_insumo"+idx, $( "#listaComponentesIC" ) ).attr({disabled:false});
		});
	}
}

/*********************************************************************************
 * Busco
 *********************************************************************************/
function eliminarArtIgualesMultiples( codigoArticulo, familia, pActivo ){
	for( var x = 0; x < multiplesMedicamentos.length; x++ ){

		//Miro si el articulo es el que se va a eliminar
		if( multiplesMedicamentos[x][0] == codigoArticulo ){
			//Cómo es el articulo a elminar lo quito de la lista
			multiplesMedicamentos.splice( x, 1 );
			x--;
		}
	}
}

function terminarAccionesMultiplesArticulos( faltantesPorAgregar ){
	

	if( datosFinales ){
		if( datosFinales && datosFinales.length > 0 ){
			switch( datosFinales.tipoArt ){
				case 'HE':
					// datosFinales.splice(1,1);
					// datosFinales = datosFinales.splice(1,0);
					datosFinales = [];
				break;

				case 'LEVIC':
					//Se agrega esta propiedad para saber que tipo de articulo es
					var tipoArt 		= datosFinales.tipoArt;
					var tipPro 			= datosFinales.tipPro;
					var indice 			= datosFinales.indice;
					var tipo 			= datosFinales.tipo;
					var observaciones 	= datosFinales.observaciones;
					var tipoProtocolo	= datosFinales[17];

					$( "#wnombrefamilia" ).val( datosFinales.nombreFamilia );

					//Si no se agrega el último medicamento se borra todo el articulo LEV o IC
					if( $( "#wnmmed"+tipPro+(elementosLev-1) ).length == 0 ){
						quitarArticulo( indice,tipPro,'', "detKardexAdd" + tipPro, 'LQ' );
						// quitarArticulo( indice, tipoProtocolo,'','detKardexAdd'+tipoProtocolo, 'LQ');
						multiplesMedicamentos = [];
						return;
					}
					else{
						document.getElementById("btnCerrarVentana").style.display = 'none';

						if( tipo != "IC" ){
							if(!esIE){
								$( "#wcolmed"+tipPro+indice )[0].setAttribute('onClick', 'mostrarLEV( "'+tipPro+indice+'" )');
							}else{
								$( "#wcolmed"+tipPro+indice )[0].onclick = new Function('evt','mostrarLEV( "'+tipPro+indice+'" )');
							}
						}
						else{
							try{
								if(!esIE){
									$( "#wcolmed"+tipPro+indice )[0].setAttribute('onClick', 'mostrarIC( "'+tipPro+indice+'" )');
								}else{
									$( "#wcolmed"+tipPro+indice )[0].onclick = new Function('evt','mostrarIC( "'+tipPro+indice+'" )');
								}
							}
							catch(e){}
						}

						$( "#wtxtobs"+tipPro+indice ).val( datosFinales[30] );

						//Oculto la fila correspondiente el medicamento insertado
						// $( "#trFil"+tipoProtocolo+(elementosLev-1) ).css( { display: "none" } );
						$( "[idtr="+"trFil"+tipoProtocolo+(elementosLev-1)+"]" ).css( { display: "none" } );

						//Dejo la condición correspondiente de acuerdo al tipo para el articulo con codigo IC0000 o LV0000
						$( "#wcondicion"+tipPro+indice ).val( datosFinales[26] );

						//Días de tratamiento
						$( "#wdiastto"+tipPro+indice ).val( datosFinales[28] );

						//Dosis máxima
						$( "#wdosmax"+tipPro+indice ).val( datosFinales[29] );

						//Marco el cambio
						marcarCambio( tipPro,indice, $( "#wcondicion"+tipPro+indice )[0] );


						// this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;


						/************************************************************************************************
						 * No permite que se pueda modificar el Lev o la infusion en ningún campo una vez creado
						 ************************************************************************************************/
						$( "select", $( "[idtr=trFil"+tipPro+indice+"]" ) ).each(function(x){
							$( "option[value="+$( this ).val()+"]", $( this ) ).attr({selected:true});
							$( this ).attr( "disabled", true );
						})

						$( "textarea", $( "[idtr=trFil"+tipPro+indice+"]" ) ).each(function(x){
							this.readOnly = true;
						})

						$( "input", $( "[idtr=trFil"+tipPro+indice+"]" ) ).each(function(x){
							switch( this.type.toLowerCase() ){

								case 'text':
									this.readOnly = true;
								break;

								case 'checkbox':
								case 'button':
									this.disabled = true;
								break;

								default: $( this ).attr( "readOnly", true );
							}
						})
						/***********************************************************************************************/
					}
					// datosFinales.splice(1,1);
					// datosFinales = datosFinales.splice(1,0);
					datosFinales = [];
				break;
				
				case "ProcAgrup":
					var tipoProtocolo	= datosFinales[17];
					var indice 			= datosFinales.indice;
					
					$("#wdosis"+tipoProtocolo+indice).attr('readonly', true);
					$("#wperiod"+tipoProtocolo+indice).attr('disabled', true);
					$("#wviadmon"+tipoProtocolo+indice).attr('disabled', true);
					
					$( "#tbDetalleAddN [idtr=trFil"+tipoProtocolo+indice+"] a, #tbDetalleAddU [idtr=trFil"+tipoProtocolo+indice+"] a, #tbDetalleAddLQ [idtr=trFil"+tipoProtocolo+indice+"] a ").remove();
				break;
			}
		}
	}

	$( "#wnombrefamilia" ).val( '' );
	
	if( faltantesPorAgregar == 0 && strPendientesCTC!="" )
	{
		abrirCTCMultiple();
		adicionMultiple = false;
		
	}
	if( faltantesPorAgregar == 0)
	{
		if(cadenaMensajeProcAgrupados != "")
		{
			jAlert(cadenaMensajeProcAgrupados,"ALERTA");
		}
	}
}

function agregarMultiplesArticulos(){
	
	try{
		var totalMultiplesArticulos = multiplesMedicamentos.length;
	}
	catch(e){
		var totalMultiplesArticulos = 0;
	}

	//Verifico si hay acciones que ejecutar antes de seguir con la el siguiente articulo a insertar
	terminarAccionesMultiplesArticulos( totalMultiplesArticulos );

	//verifico si hay más articulos en lista
	if( multiplesMedicamentos && multiplesMedicamentos.length > 0 ){

		switch( multiplesMedicamentos[0].tipoArt ){

			case 'HE':
				agregarArticulo( "detKardexAdd"+multiplesMedicamentos[0][17]);

				//Se deja todo el array en una variable
				var datos = multiplesMedicamentos[0];

				//Creo variable global con los datos de la última carga para hacer operaciones posteriores
				fechaInicioFija = datos.fechaInicioFija;

				//Quito la posicion que se va a grabar
				multiplesMedicamentos = multiplesMedicamentos.splice(1,multiplesMedicamentos.length);

				seleccionarArticulo(
					datos[0],
					datos[1],
					datos[2],
					datos[3],
					datos[4],
					datos[5],
					datos[6],
					datos[7],
					datos[8],
					datos[9],
					datos[10],
					datos[11],
					datos[12],
					datos[13],
					datos[14],
					datos[15],
					datos[16],
					datos[17],
					datos[18],
					datos[19],
					datos[20],
					datos[21],
					datos[22],
					datos[23],
					datos[24],
					datos[25],
					datos[26],
					datos[27],
					datos[28],
					datos[29],
					datos[30],
					datos[31],
					datos[32],
					datos[33],
					datos[34],
					datos[35],
					datos[36],
					datos[37],
					datos[38],
					datos[39],
					datos[40],
					datos[41],
					datos[42],
					datos[43]
				);

				
			break;

			case 'LEVIC':

				agregarArticulo( "detKardexAdd"+multiplesMedicamentos[0][17]);

				//Se deja todo el array en una variable
				var datos = multiplesMedicamentos[0];
				datosFinales = datos;

				fechaInicioFija = datos.fechaInicioFija;

				//Quito la posicion que se va a grabar
				multiplesMedicamentos = multiplesMedicamentos.splice(1,multiplesMedicamentos.length);

				seleccionarArticulo(
					datos[0],
					datos[1],
					datos[2],
					datos[3],
					datos[4],
					datos[5],
					datos[6],
					datos[7],
					datos[8],
					datos[9],
					datos[10],
					datos[11],
					datos[12],
					datos[13],
					datos[14],
					datos[15],
					datos[16],
					datos[17],
					datos[18],
					datos[19],
					datos[20],
					datos[21],
					datos[22],
					datos[23],
					datos[24],
					datos[25],
					datos[26],
					datos[27],
					datos[28],
					datos[29],
					datos[30],
					datos[31],
					datos[32],
					datos[33],
					datos[34],
					datos[35],
					datos[36]
				);
			break;
			
			
			case 'ProcAgrup':
			
			
				//Se deja todo el array en una variable
				var datos = multiplesMedicamentos[0];
				datosFinales = datos;
				
				
				// contador
				
				tipoProtocolo = datos[17];
				
				switch (tipoProtocolo) {
					case 'N':
						posicionActual = elementosDetalle;
					break;
				case 'A':
						posicionActual = elementosAnalgesia;
					break;
				case 'U':
						posicionActual = elementosNutricion;
					break;
				case 'Q':
						posicionActual = elementosQuimioterapia;
					break;

				case 'LQ':
						posicionActual = elementosLev;
					break;
				default:
						posicionActual = elementosDetalle;
					break;
				}
				
				procAgrupados[datos[36]][datos[37]].medicamentos[datos[38]].tipoProtocolo =  tipoProtocolo;
				procAgrupados[datos[36]][datos[37]].medicamentos[datos[38]].posicionActual =  posicionActual;
				
				datosFinales.indice = posicionActual;
				
				$("#wnombrefamilia").val($("#DescFamiliaProcAgrup_"+datos[38]).val());
				
				agregarArticulo( "detKardexAdd"+multiplesMedicamentos[0][17]);

				// //Se deja todo el array en una variable
				// var datos = multiplesMedicamentos[0];

				//Creo variable global con los datos de la última carga para hacer operaciones posteriores
				fechaInicioFija = datos.fechaInicioFija;

				//Quito la posicion que se va a grabar
				multiplesMedicamentos = multiplesMedicamentos.splice(1,multiplesMedicamentos.length);

				seleccionarArticulo(
					datos[0],
					datos[1],
					datos[2],
					datos[3],
					datos[4],
					datos[5],
					datos[6],
					datos[7],
					datos[8],
					datos[9],
					datos[10],
					datos[11],
					datos[12],
					datos[13],
					datos[14],
					datos[15],
					datos[16],
					datos[17],
					datos[18],
					datos[19],
					datos[20],
					datos[21],
					datos[22],
					datos[23],
					datos[24],
					datos[25],
					datos[26],
					datos[27],
					datos[28],
					datos[29],
					datos[30],
					datos[31],
					datos[32],
					datos[33],
					datos[34],
					datos[35],
					datos[39]
				);
// alert("paso por aca");
			break;
		}
	}
}

function modificarCambiosLEV(){

	var valLevIdo = $( "#idoMostrado", $( "#listaComponentesLEV" ) ).val();

	var todoOk = true;
	var msgError = "";

	var tieneElectrolito = false;

	if( parseInt(valLevIdo) > 0 ){
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].idoLev == valLevIdo ){
				if( artLevs[x].insEle == 'on' ){
					tieneElectrolito = true;
				}
			}
		}
	}


	/************************************************************************************************************************
	 * Valido que sea valido la selección de insumos
	 * - Se puede escoger un electrolito pero no debe haber nada escrito en en los campos vol/diligencia
	 * - Si se escoge un electrólito con vol/dil tiene que haber una solución elegida por lo menos
	 * - Si se escoge una solución esta debe tener una frecuencia
	 ************************************************************************************************************************/
	$( "[id^=frecsol]", $( "#listaComponentesLEV" ) ).each(function(x){

		var idx = this.id.substr( 7 );

		if( $( "#check_insumo" + idx )[0].checked ){
			if( $( this ).val() == '' ){

				var volxdil = $( "#volxdil"+idx );

				//Se valida que halla una solución elegida si hay algo escrito en vol/dil
				// if( volxdil.length > 0 && volxdil.val()*1 > 0 ){
				if( volxdil.length > 0 ){
					var valSol = false;
					$( "[id^=check_insumo]", $( "[id^=tdCbSol]" ) ).each(function(j){
						if( this.checked ){
							valSol = true;
						}
					});

					if( !valSol ){
						todoOk = false;
						msgError = "Debe seleccionar al menos una <b>SOLUCION</b>.";
					}
				}

				if( volxdil.length > 0 && volxdil.val() == 0 ){
					todoOk = false;
					msgError = "Debe escribir un valor en <b>VOL/DIL</b> para los electrolitos seleccionados";
				}
			}

			if( $.trim( $( "#cantidad_insumo" + idx ).val() ) == "" ){
				todoOk = false;
				if( $( "#tdCbSol"+idx ).length == 0 )
					msgError = "Ingrese un valor valido para los <b>ELECTROLITOS</b>";
				else
					msgError = "Ingrese un valor para <b>VOL/TOTAL</b> de la solución";
			}
		}
	});

	var valSol = false;
	$( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesLEV" ) ) ).each(function(j){
		if( this.checked ){
			valSol = true;
		}
	});

	if( !valSol ){
		todoOk = false;
		msgError = "Debe seleccionar al menos una <b>SOLUCION</b>.";
	}

	var frecDi = $( "#slFrecDilLev", $( "#listaComponentesLEV" ) ).val();
	if( frecDi == "" ){
		msgError = "Debe seleccionar una <b>UNIDAD</b> para <b>VELOCIDAD DE INFUSIÓN</b>.";
		todoOk = false;
	}

	if( todoOk ){
		var frecDi = $( "#txFrecDilLev", $( "#listaComponentesLEV" ) ).val();
		if( frecDi == "" ){
			msgError = "Debe ingresar un valor para <b>VELOCIDAD DE INFUSIÓN</b>.";
			todoOk = false;
		}
	}

	/************************************************************************************************************************************************
	 * Para levs, si se está modificando y hay electrolito seleccionado se debe obligar a que se ingrese un valor para el electrolito
	 ************************************************************************************************************************************************/
	if( tieneElectrolito ){
		if( $( "[id^=tdCbEle]" ).find(":checked").length == 0 ){
			msgError = "Ingrese un valor valido para el volumen del electrolito.";
			todoOk = false;
		}
		else{
			var idx2 = $( "[id^=tdCbEle]", $( "#listaComponentesLEV" ) ).find(":checked")[0].id.substr( 7 );
			if( $(  "#cantidad_insumo"+idx2, $( "#listaComponentesLEV" ) ).find(":checked").val()*1 == 0 ){
				msgError = "Ingrese un valor valido para el volumen del <b>ELECTROLITO</b>.";
				todoOk = false;
			}
		}
	}

	$( "[id^=cantidad_insumo]", $( "#listaComponentesLEV" ) ).each(function(){
		if( !this.readOnly ){
			if( this.value*1 == 0 ){
				var idx2 = this.id.substr( 15 );
				if( $( "#tdCbEle"+idx2, $( "#listaComponentesLEV" ) ).length == 0 )
					msgError = "Ingrese un valor valido para la <b>SOLUCION</b>.";
				else
					msgError = "Ingrese un valor valido para el volumen del <b>ELECTROLITO</b>.";

				todoOk = false;
			}
		}
	})
	/**********************************************************************************************************************/

	if( todoOk ){

		var objAux = {};
		var mostrar = false;	//Indica si si se muestra la modal
		var codLev = "";

		if( parseInt(valLevIdo) > 0 ){
			//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
			for( var x in artLevs ){
				if( artLevs[x].idoLev == valLevIdo ){
					objAux[x] = artLevs[ x ];
					mostrar = true;
					codLev = artLevs[x].codLev;
				}
			}
		}
		else{
			//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
			for( var x in artLevs ){
				if( artLevs[x].codLev == valLevIdo ){
					objAux[x] = artLevs[ x ];
					mostrar = true;
					codLev = artLevs[x].codLev;
				}
			}
		}

		//Recorro el objeto auxiliar que contiene todos los articulos a actualizar
		for( var x in objAux ){

			$( "[id^=check_insumo]", $( "#listaComponentesLEV" ) ).each(function(){

				if( this.checked ){

					var idx = this.id.substr( 12 );

					if( $( "[ele"+artLevs[ x ].insCod+"="+idx+"]", $( "#listaComponentesLEV" ) ).length > 0 ){
						if( artLevs[ x ].insEle == 'on' ){
							artLevs[ x ].insVel = $( "#cantidad_insumo"+idx, $( "#listaComponentesLEV" ) ).val();
							artLevs[ x ].insVdi = $( "#volxdil"+idx, $( "#listaComponentesLEV" ) ).val();

							$( "#wdosis"+x ).val( artLevs[ x ].insVel );
							marcarCambio( x.substr(0,2), x.substr(2) );
						}
					}
					else if( $( "[sol"+artLevs[ x ].insCod+"="+idx+"]", $( "#listaComponentesLEV" ) ).length > 0 ){
						if( artLevs[ x ].insEle != 'on' ){
							artLevs[ x ].insVto = $( "#cantidad_insumo"+idx, $( "#listaComponentesLEV" ) ).val();

							$( "#wdosis"+x ).val( artLevs[ x ].insVto );
							marcarCambio( x.substr(0,2), x.substr(2) );
						}
					}
				}
			});

			artLevs[ x ].insFdi = $( "#slFrecDilLev", $( "#listaComponentesLEV" ) ).val();
			artLevs[ x ].insVfd = $( "#txFrecDilLev", $( "#listaComponentesLEV" ) ).val();

			$( "#slVelDil"+valLevIdo ).val( artLevs[ x ].insFdi );
			$( "#inFrecDil"+valLevIdo ).val( artLevs[ x ].insVfd );

			$( "#wdosmax"+x ).val( $( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).val() );
			$( "#wdiastto"+x ).val( $( "#inDttoLEV", $( "#listaComponentesLEV" ) ).val() );
			$( "#wdosmax"+artLevs[ x ].codLev ).val( $( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).val() );
			$( "#wdiastto"+artLevs[ x ].codLev ).val( $( "#inDttoLEV", $( "#listaComponentesLEV" ) ).val() );
			marcarCambio( artLevs[ x ].codLev.substr(0,2), artLevs[ x ].codLev.substr(2) );

			if( parseInt(valLevIdo) )
				artLevs[ x ].insEst = 'mod';
		}

		var nombre1 = '';
		$( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesLEV" ) ) ).each(function(x){
			if( this.checked ){
				fila = this.parentNode.parentNode;
				var idx = this.id.substr(12);
				nombre1 += $( "[tdSolGen"+idx+"]" ).html() +" <b>" + $( "#cantidad_insumo"+idx ).val() + " "+$( "[tdSolUVol"+idx+"]" ).html()+"</b><br>";
			}
		});

		var nombre2 = "";
		$( "[id^=check_insumo]", $( "[id^=tdCbEle]", $( "#listaComponentesLEV" ) ) ).each(function(x){
			if( this.checked ){
				fila = this.parentNode.parentNode;
				var idx = this.id.substr(12);
				nombre2 += $( "[tdEleGen"+idx+"]" ).html() +" <b>" + $( "#cantidad_insumo"+idx ).val() + " "+$( "[tdEleUVol"+idx+"]").html()+"</b> en C/<b>"+$( "#volxdil"+idx ).val()+"</b><br>";
			}
		});

		var nombre  = $.trim( nombre1 )+"+"+$.trim( nombre2 );
		// var nombre  = $.trim( nombre1 )+"+"+$.trim( nombre2 )+"para <b>"+$("#wperiod"+tipoProtocolo+indice+" option[value="+$("#wperiod"+tipoProtocolo+indice).val()+"]" ).html()
		    // nombre +="</b> a <b>"+$( "#slFrecDilLev option[value='"+$( "#slFrecDilLev", $( "#listaComponentesLEV" ) ).val()+"']", $( "#listaComponentesLEV" ) ).html();
		// var nombre = "Aquí va el nombre";


		if( parseInt(valLevIdo) ){

			nombre += $( "#trIdoLev"+$( "#idoMostrado", $("#listaComponentesLEV" ) ).val()+" td" ).eq(1).html().match( /\bpara\b.+\d{1,2}.+a/ );
			var valueOptionVel = $( "#slFrecDilLev", $("#listaComponentesLEV" ) ).val();
			nombre += " <b>"+$( "#txFrecDilLev", $("#listaComponentesLEV" ) ).val()+" "+$( "#slFrecDilLev option[value="+valueOptionVel+"]", $("#listaComponentesLEV" ) ).text()+"</b>";

			$( "#trIdoLev"+$( "#idoMostrado", $("#listaComponentesLEV" ) ).val()+" td" ).eq(1).html( nombre );

			//Recorro el objeto auxiliar que contiene todos los articulos a actualizar
			for( var x1 in objAux ){
				//Asigno las observaciones
				$("#wtxtobsadd"+x1 ).val( $( "#txObservacionesLEV", $("#listaComponentesLEV") ).val() );
				$("#wtxtobsadd"+x1 ).val( $( "#txObservacionesLEV", $("#listaComponentesLEV") ).val() );
				$("#wtxtobsadd"+codLev ).val( $( "#txObservacionesLEV", $("#listaComponentesLEV") ).val() );
				$("#wtxtobsadd"+codLev ).val( $( "#txObservacionesLEV", $("#listaComponentesLEV") ).val() );
				$("#txLEVObservaciones"+valLevIdo ).val( $( "#txObservacionesLEV", $("#listaComponentesLEV") ).val() );
			}

			$( "#tdDmaxLEV"+valLevIdo ).html( $( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).val() );
			$( "#tdDttoLEV"+valLevIdo ).html( $( "#inDttoLEV", $( "#listaComponentesLEV" ) ).val() );
		}
		else{

			nombre += $( "#wcolmed"+codLev+" div" ).eq(0).html().match( /\bpara\b.+\d{1,2}.+a/ );
			var valueOptionVel = $( "#slFrecDilLev", $("#listaComponentesLEV" ) ).val();
			nombre += " <b>"+$( "#txFrecDilLev", $("#listaComponentesLEV" ) ).val()+" "+$( "#slFrecDilLev option[value="+valueOptionVel+"]", $("#listaComponentesLEV" ) ).text()+"</b>";

			$( "#wcolmed"+codLev+" div" ).eq(0).html( nombre );

			//Recorro el objeto auxiliar que contiene todos los articulos a actualizar
			for( var x1 in objAux ){
				//Asigno las observaciones
				$("#wtxtobs"+x1 ).val( $( "#txObservacionesLEV", $("#listaComponentesLEV") ).val() );
				$("#wtxtobs"+valLevIdo ).val( $( "#txObservacionesLEV", $("#listaComponentesLEV") ).val() );
			}
		}

		var componentesLQ =  document.getElementById("wcomponentesarticulocod").value;

		$.unblockUI();

		if(isset(document.getElementById("wcomponentesarticulo")))
			document.getElementById("wcomponentesarticulo").value = "";
		if(isset(document.getElementById("wcomponentesarticulocod")))
			document.getElementById("wcomponentesarticulocod").value = "";

		// eleccionMedicamentosInsumos(componentesLQ, indice, tipoProtocolo, "IC" );

		// quitarArticulo(indice,'%','','detKardexAddN','LQ');

		grupoGenerico = "";

		document.getElementById("wcomponentesarticulo").value = "";
		document.getElementById("wcomponentesarticulocod").value = "";
	}
	else{
		jAlert( msgError, 'ALERTA' );
	}
}


function modificarCambiosIC(){

	var valLevIdo = $( "#idoMostrado", $( "#listaComponentesIC" ) ).val();

	var todoOk = true;
	var msgError = "";

	/************************************************************************************************************************
	 * Valido que sea valido la selección de insumos
	 * - Se puede escoger un electrolito
	 * - Se debe escoger una solución
	 ************************************************************************************************************************/
	var valSol = false;
	var totSol = 0;

	if( !$( "#ckSinSolucion", $( "#listaComponentesIC" ) )[0].checked ){
		$( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesIC" ) ) ).each(function(j){
			if( this.checked ){
				valSol = true;
				totSol++;

				if( $( "#cantidad_insumo"+this.id.substr(12) ).val() == 0 ){
					todoOk = false;
					this.focus;
					msgError = "Debe ingresar el <b>VOL/TOTAL</b> para la <b>SOLUCION</b>";
				}
			}
		});

		if( totSol == 0 || totSol > 1 ){
			todoOk = false;
			msgError = "Debe seleccionar una sola <b>SOLUCION</b>.";
		}
	}

	// $( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesIC" ) ) ).each(function(j){
		// if( this.checked ){
			// valSol = true;
			// totSol++;

			// if( $( "#cantidad_insumo"+this.id.substr(12) ).val() == 0 ){
				// todoOk = false;
				// this.focus;
				// msgError = "Debe ingresar el <b>VOL/TOTAL</b> para la <b>SOLUCION</b>";
			// }
		// }
	// });

	if(valSol){
		if( totSol == 0 || totSol > 1 ){
			todoOk = false;
			msgError = "Debe seleccionar una sola <b>SOLUCION</b>.";
		}
	}

	if( !$( "#slFrecDilLev", $( "#listaComponentesIC" ) )[0].disabled ){
		var frecDi = $( "#slFrecDilLev", $( "#listaComponentesIC" ) ).val();
		if( frecDi == "" ){
			msgError = "Debe seleccionar una <b>VELOCIDAD DE INFUSI&Oacute;N</b>.";
			todoOk = false;
		}

		if( todoOk ){
			if( $( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val() == "" ){
				todoOk = false;
				msgError = "Debe seleccionar una <b>VELOCIDAD DE INFUSI&Oacute;N</b>.";
			}
		}
	}


	//Verifico que se seleccione un solo electrolito
	var totEle = 0;
	var totDca = 0;
	var totVdca = 0;
	var vMin = 0;
	var vMax = 0;
	$( "[id^=check_insumo]", $( "[id^=tdCbEle]", $( "#listaComponentesIC" ) ) ).each(function(j){
		if( this.checked ){

			var idx = this.id.substr(12);

			totEle++;

			if( $( "#inICDca"+this.id.substr(12) ).val()*1 > 0 && $.trim( $( "#slICUdca"+this.id.substr(12) ).val() ) == '' ){
				totDca++;
			}

			var valDosisCalculada = $( "#inICDca"+this.id.substr(12) ).val()*1;
			var valSelDosisCalculada = $( "#slICUdca"+this.id.substr(12) ).val();
			vMin = $( "#slICUdca"+this.id.substr(12)+" option[value="+valSelDosisCalculada+"]" ).attr( "vMin" );
			vMax = $( "#slICUdca"+this.id.substr(12)+" option[value="+valSelDosisCalculada+"]" ).attr( "vMax" );

			if( valDosisCalculada < vMin*1 || valDosisCalculada > vMax*1 ){
				$( "#inICDca"+this.id.substr(12) ).val('');
				totVdca++;
			}

			if( $.trim( $( "#cantidad_insumo"+idx ).val() ) == "" ){
				todoOk = false;
				if( $( "#tdCbEle"+idx ).length > 0  )
					msgError = "Debe ingresar un valor valido para <b>DOSIS</b> del <b>MEDICAMENTO</b>";
				else
					msgError = "Debe ingresar un valor valido para <b>VOL/TOTAL*</b> del <b>SOLUCIONES</b>";
			}
		}
	});

	if( totDca > 0 ){
		todoOk = false;
		msgError = "Debe seleccionar una <b>UNIDAD</b> para la <b>DOSIS CALCULADA</b>";
	}

	if( totEle == 0 || totEle > 1 ){
		todoOk = false;
		if( totEle == 0 )
			msgError = "Debe seleccionar un <b>MEDICAMENTO</b>";
		else
			msgError = "Debe seleccionar un solo <b>MEDICAMENTO</b>";
	}

	if( todoOk && totVdca > 0 ){
		todoOk = false;
		msgError = "El valor ingresado para el <b>MEDICAMENTO</b> no es valido.<br>El rango valido es mayor a <b>"+vMin+"</b> y  menor a <b>"+vMax+"</b>";
	}
	/**********************************************************************************************************************/

	if( todoOk ){

		var objAux = {};
		var mostrar = false;	//Indica si si se muestra la modal
		var codLev = "";

		if( parseInt(valLevIdo) > 0 ){
			//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
			for( var x in artLevs ){
				if( artLevs[x].insEst != 'del' ){
					if( artLevs[x].idoLev == valLevIdo ){
						objAux[x] = artLevs[ x ];
						mostrar = true;
						codLev = artLevs[x].codLev;
					}
				}
			}
		}
		else{
			//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
			for( var x in artLevs ){
				if( artLevs[x].insEst != 'del' ){
					if( artLevs[x].codLev == valLevIdo ){
						objAux[x] = artLevs[ x ];
						mostrar = true;
						codLev = artLevs[x].codLev;
					}
				}
			}
		}

		//Recorro el objeto auxiliar que contiene todos los articulos a actualizar
		for( var x1 in objAux ){

			var puedeCambiarVelocidadInfusion = true;

			$( "[id^=check_insumo]", $( "#listaComponentesIC" ) ).each(function(){

				var idx = this.id.substr( 12 );

				if( this.checked == true ){

					if( $( "#tdCbEle"+idx, $( "#listaComponentesIC" ) ).length > 0 ){
						if( artLevs[ x1 ].insEle == 'on' ){
							artLevs[ x1 ].insVel = $( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).val();

							$( "#wdosis"+x1 ).val( artLevs[ x1 ].insVel );
							marcarCambio( x1.substr(0,2), x1.substr(2) );

							artLevs[ x1 ].insDca = $( "#inICDca"+idx, $( "#listaComponentesIC" ) ).val();
							artLevs[ x1 ].insCdc = $( "#slICUdca"+idx, $( "#listaComponentesIC" ) ).val();

							$( "#txDosisCalculada"+valLevIdo ).val( artLevs[ x1 ].insDca );
							$( "#slDosisCalculada"+valLevIdo ).val( artLevs[ x1 ].insCdc );
						}
					}
					else if( $( "#tdCbSol"+idx, $( "#listaComponentesIC" ) ).length > 0 ){
						if( artLevs[ x1 ].insEle != 'on' ){
							artLevs[ x1 ].insVto = $( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).val();

							$( "#wdosis"+x1 ).val( artLevs[ x1 ].insVto );
							marcarCambio( x1.substr(0,2), x1.substr(2) );
						}
					}
				}
				else{
					var auxAttr = $( "#tdCbSol"+idx, $( "#listaComponentesIC" ) ).attr( "sol"+artLevs[x1].insCod );
					if( auxAttr && auxAttr != "" ){
						//Si no esta chequeado se elimina
						if( !$( "#check_insumo"+auxAttr, $( "#listaComponentesIC" ) )[0].checked ){

							if( artLevs[ x1 ].insEst == 'new' ){
								quitarArticulo( x1.substr(2), x1.substr(0,2),'', "detKardexAdd" + x1.substr(0,2), 'LQ', true );
							}
							else{
								//Y se suspende el articulo
								suspenderArticulo( x1.substr(2), x1.substr(0,2), false );
							}
							artLevs[x1].insEst = 'del';
						}
					}
				}

				$( "#wdosmax"+x1 ).val( $( "#inDmaxIC", $( "#listaComponentesIC" ) ).val() );
				$( "#wdiastto"+x1 ).val( $( "#inDttoIC", $( "#listaComponentesIC" ) ).val() );
				$( "#wdosmax"+artLevs[ x1 ].codLev ).val( $( "#inDmaxIC", $( "#listaComponentesIC" ) ).val() );
				$( "#wdiastto"+artLevs[ x1 ].codLev ).val( $( "#inDttoIC", $( "#listaComponentesIC" ) ).val() );
				marcarCambio( artLevs[ x1 ].codLev.substr(0,2), artLevs[ x1 ].codLev.substr(2) );
			});

			if( puedeCambiarVelocidadInfusion ){
				artLevs[ x1 ].insFdi = $( "#slFrecDilLev", $( "#listaComponentesIC" ) ).val();
				artLevs[ x1 ].insVfd = $( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val();

				$( "#slVelDil"+valLevIdo ).val( artLevs[ x1 ].insFdi );
				$( "#inFrecDil"+valLevIdo ).val( artLevs[ x1 ].insVfd );
			}

			if( parseInt(valLevIdo) )
				if( artLevs[ x1 ].insEst != 'new' && artLevs[ x1 ].insEst != 'del' )
					artLevs[ x1 ].insEst = 'mod';
		}

		var nombre2 = "";
		var nombre3 = "";
		$( "[id^=check_insumo]", $( "[id^=tdCbEle]", $( "#listaComponentesIC" ) ) ).each(function(x){
			if( this.checked ){
				fila = this.parentNode.parentNode;
				var idx = this.id.substr(12);
				nombre2 += $( "[tdEleGen"+idx+"]" ).html() +" <b>" + $( "#cantidad_insumo"+idx ).val() + " "+$( "[tdEleUVol"+idx+"]").html()+"</b><br>";

				if( $( "#inICDca"+idx, $( "#listaComponentesIC" ) ).val() != '' )
					nombre3 = "infusi&oacute;n continua a <b>"+$( "#inICDca"+idx, $( "#listaComponentesIC" ) ).val()+" "+$( "#slICUdca"+idx+" option[value='"+$( "#slICUdca"+idx ).val()+"']", $( "#listaComponentesIC" ) ).html()+"</b>"
			}
		});

		var nombre1 = '';
		$( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesIC" ) ) ).each(function(x){
			if( this.checked ){
				fila = this.parentNode.parentNode;
				var idx = this.id.substr(12);
				var ido = $("#idoMostrado", $("#listaComponentesIC") ).val();
				// nombre1 += $( "[tdSolGen"+idx+"]" ).html() +" <b>" + $( "#cantidad_insumo"+idx ).val() + " "+$( "[tdSolUVol"+idx+"]" ).html()+"</b><br>";
				// if( $( "#txDosisCalculada"+ido ).val() )
					// nombre1 += " hasta <b>"+$( "#cantidad_insumo"+idx ).val() + " "+$( "[tdSolUVol"+idx+"]" ).html()+"</b> de "+$( "[tdSolGen"+idx+"]" ).html(); //+"<br>infusi&oacute;n continua a <b>"+$( "#txDosisCalculada"+ido ).val()+" "+$( "#slDosisCalculada"+ido+" option[value='"+$( "#slDosisCalculada"+ido ).val()+"']" ).html()+"</b>";
				// else
					// nombre1 += " hasta <b>"+$( "#cantidad_insumo"+idx ).val() + " "+$( "[tdSolUVol"+idx+"]" ).html()+"</b> de "+$( "[tdSolGen"+idx+"]" ).html(); //+"<br>infusi&oacute;n continua a <b>"+$( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val()+" "+$( "#slFrecDilLev"+" option[value='"+$( "#slFrecDilLev" ).val()+"']", $( "#listaComponentesIC" ) ).html()+"</b>";

				nombre1 += " hasta <b>"+$( "#cantidad_insumo"+idx ).val() + " "+$( "[tdSolUVol"+idx+"]" ).html()+"</b> de "+$( "[tdSolGen"+idx+"]" ).html()+"<br>"; //+"<br>infusi&oacute;n continua a <b>"+$( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val()+" "+$( "#slFrecDilLev"+" option[value='"+$( "#slFrecDilLev" ).val()+"']", $( "#listaComponentesIC" ) ).html()+"</b>";

			}
		});

		if( nombre3 == "" )
			nombre3 = "infusi&oacute;n continua a <b>"+$( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val()+" "+$( "#slFrecDilLev"+" option[value='"+$( "#slFrecDilLev" ).val()+"']", $( "#listaComponentesIC" ) ).html()+"</b>"

		if(!nombre3)
			nombre3 = "";

		var nombre  = nombre2+nombre1+nombre3;

		if( parseInt(valLevIdo) ){

			// nombre += " " + $( "#trIdoLev"+$("#idoMostrado", $("#listaComponentesIC") ).val()+" td" ).eq(1).html().match( /\bpara\b.+\d{1,2}.+$/ );

			$( "#trIdoLev"+$("#idoMostrado", $("#listaComponentesIC") ).val()+" td" ).eq(1).html( nombre );

			//Recorro el objeto auxiliar que contiene todos los articulos a actualizar
			for( var x1 in objAux ){
				//Asigno las observaciones
				// $("#wtxtobs"+x1 ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
				// $("#wtxtobs"+valLevIdo ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
				$("#wtxtobsadd"+x1 ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
				$("#wtxtobsadd"+x1 ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
				$("#wtxtobsadd"+codLev ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
				$("#wtxtobsadd"+codLev ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
				$("#txICObservaciones"+valLevIdo ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
			}

			$( "#tdDmaxIC"+valLevIdo ).html( $( "#inDmaxIC", $( "#listaComponentesIC" ) ).val() );
			$( "#tdDttoIC"+valLevIdo ).html( $( "#inDttoIC", $( "#listaComponentesIC" ) ).val() );
		}
		else{

			//nombre += " " + $( "div", $( "#wcolmed"+codLev ) ).eq(0).html().match( /\bpara\b.+\d{1,2}.+$/ );

			$( "div", $( "#wcolmed"+codLev ) ).eq(0).html( nombre );

			//Recorro el objeto auxiliar que contiene todos los articulos a actualizar
			for( var x1 in objAux ){
				//Asigno las observaciones
				$("#wtxtobs"+x1 ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
				$("#wtxtobs"+valLevIdo ).val( $( "#txObservacionesIC", $("#listaComponentesIC") ).val() );
			}
		}



		var componentesLQ =  document.getElementById("wcomponentesarticulocod").value;

		$.unblockUI();



		/**********************************************************************************************
		 * Esto solo se hace si el articulo es nuevo para el IC
		 **********************************************************************************************/
		if( true ){

			//Inserto el articulo nuevo
			var indice 			= $( "[id^=widoriginal][value="+valLevIdo+"]" )[0].id.substr(13);
			var tipoProtocolo 	= $( "[id^=widoriginal][value="+valLevIdo+"]" )[0].id.substr(11,2);
			administracionLQ 	= $( "#wviadmon"+tipoProtocolo+indice ).val();		// Vías de administración
			condicionLQ 		= $( "#wcondicion"+tipoProtocolo+indice ).val();
			observacionesLQ 	= $( "#wtxtobsadd"+tipoProtocolo+indice ).val();
			fechaInicioLQ 		= $( "#wfinicio"+tipoProtocolo+indice ).val();	// Fecha y hora de inicio de aplicación
			dosisMaxLQ 			= $( "#wdosmax"+tipoProtocolo+indice ).val();				// Dosis a aplicar
			diasTtoLQ 			= $( "#wdiastto"+tipoProtocolo+indice ).val();
			periodoLQ 			= $( "#wperiod"+tipoProtocolo+indice ).val();

			eleccionMedicamentosInsumos(componentesLQ, indice, tipoProtocolo, "IC" );
			agregarMultiplesArticulos();
		}
		/*********************************************************************************************/





		if(isset(document.getElementById("wcomponentesarticulo")))
			document.getElementById("wcomponentesarticulo").value = "";
		if(isset(document.getElementById("wcomponentesarticulocod")))
			document.getElementById("wcomponentesarticulocod").value = "";


		// quitarArticulo(indice,'%','','detKardexAddN','LQ');
		grupoGenerico = "";

		document.getElementById("wcomponentesarticulo").value = "";
		document.getElementById("wcomponentesarticulocod").value = "";
	}
	else{
		jAlert( msgError, 'ALERTA' );
	}
}

/******************************************************************************************
 * Cambia la velocidad de infusión, ya sea para los LEVs o las IC
 ******************************************************************************************/
function cambiarDosisCalculadaModalIC( cmp, valLevIdo ){

	//Solo se hace si se cambia el select
	if( cmp.tagName.toLowerCase() == "select"  ){
		var valSelect = $( "#slICUdca"+valLevIdo ).val();
		var vMin = $( "#slICUdca"+valLevIdo+" option[value="+valSelect+"]" ).attr( "vMin" );
		var vMax = $( "#slICUdca"+valLevIdo+" option[value="+valSelect+"]" ).attr( "vMax" );
		$( "#inICDca"+valLevIdo )[0].tooltipText = "Ingrese un valor entre "+vMin+" y "+vMax;

		var valor = $( "#inICDca"+valLevIdo ).val()*1;
		if( valor < vMin*1 || valor > vMax*1 ){
			$( "#inICDca"+valLevIdo ).val( '' );
		}
	}
}



/************************************************************************************
 * Valida que el campo cmp tenga un valor mínimo
 ************************************************************************************/
function validarEntradaDecimalMinDca( cmp, icIdo ){

	var val = false;

	// return validarEntradaDecimal(event);

	var validar = cmp.value;
	var esDecimal = true;

	/************************************************************************
	 * Si es un valor decimal verfico que este entre el valor
	 ************************************************************************/
	if( esDecimal ){

		var valSelect = $("#slDosisCalculada"+icIdo).val();

		if( valSelect ){
			var vMin = $( "#slDosisCalculada"+icIdo+" option[value="+valSelect+"]" ).attr( "vMin" );

			if( validar*1 >= vMin ){
				val = true;
			}
		}
	}

	if( !val ){
		cmp.value = "";
	}

	return val;
}

/***
 *
 */
function validarEntradaDecimalMaxDca( cmp, icIdo, e ){

	var val = false;

	tecla = (document.all) ? e.keyCode : e.which;

    if (tecla==8 || tecla==13)
    	return true;

	if( tecla == 0 && !document.all ){
		return true;
	}

	// return validarEntradaDecimal(event);
	var patron = /^\d+(\.\d{0,2})?$/	//expresión regular de decimal

	var validar = cmp.value+String.fromCharCode(tecla);
	var esDecimal = patron.test( validar );					//Valido que lo digitado si sea un decimal

	/************************************************************************
	 * Si es un valor decimal verfico que este entre el valor
	 ************************************************************************/
	if( esDecimal ){

		var valSelect = $("#slDosisCalculada"+icIdo).val();

		if( valSelect ){
			var vMax = $( "#slDosisCalculada"+icIdo+" option[value="+valSelect+"]" ).attr( "vMax" );

			if( validar*1 <=vMax ){
				val = true;
			}
		}
	}

	return val;
}

/******************************************************************************************
 * Cambia la velocidad de infusión, ya sea para los LEVs o las IC
 ******************************************************************************************/
function cambiarDosisCalculada( cmp, valLevIdo ){

	var objAux = {};
	var mostrar = false;	//Indica si si se muestra la modal

	if( parseInt(valLevIdo) > 0 ){	//Si es el ido
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].idoLev == valLevIdo ){
				if( artLevs[x].insEle == 'on' ){
					objAux[x] = artLevs[ x ];
					mostrar = true;
				}
			}
		}
	}
	else{	//Si es con tipoprotocolo+consecutivo
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].codLev == valLevIdo ){
				//Esto solo se aplica al medicamento de una IC
				if( artLevs[x].insEle == 'on' ){
					objAux[x] = artLevs[ x ];
					mostrar = true;
				}
			}
		}
	}

	//Recorro el objeto auxiliar que contiene todos los articulos a actualizar
	for( var x in objAux ){

		artLevs[ x ].insDca = $( "#txDosisCalculada"+artLevs[ x ].idoLev ).val();
		artLevs[ x ].insCdc = $( "#slDosisCalculada"+artLevs[ x ].idoLev ).val();
		artLevs[ x ].insEst = 'mod';

		//Solo se hace si se cambia el select
		if( cmp.tagName.toLowerCase() == "select"  ){
			var valSelect = $( "#slDosisCalculada"+artLevs[ x ].idoLev ).val();
			var vMin = $( "#slDosisCalculada"+artLevs[ x ].idoLev+" option[value="+valSelect+"]" ).attr( "vMin" );
			var vMax = $( "#slDosisCalculada"+artLevs[ x ].idoLev+" option[value="+valSelect+"]" ).attr( "vMax" );
			$( "#txDosisCalculada"+artLevs[ x ].idoLev )[0].tooltipText = "Ingrese un valor entre "+vMin+" y "+vMax;

			var valor = $( "#txDosisCalculada"+artLevs[ x ].idoLev ).val()*1;
			if( valor < vMin*1 || valor > vMax*1 ){
				$( "#txDosisCalculada"+artLevs[ x ].idoLev ).val( '' );
			}
		}
	}
}


/********************************************************************************************************************
 * Esto se hace si se cambi la fecha de un articulo LEV o IC
 * Lo que se hace es cambiar la fecha y hora de inicio de todos los articulos pertenecientes al LEV o la IC
 * Si es un LEV o IC tipo será LQ e indice traerá el ido del articulo
 ********************************************************************************************************************/
function marcarCambioFecLEVIC( tipo, idx, campo ){

	var i = 0;
	//Busco los objetos que pertenecen
	for( var x in artLevs ){

		//Si el idoLev es igual a indice es por que el articulo pertenece al LEV o IC
		if( artLevs[ x ].idoLev == idx ){

			//Cambio el valor del campo al mismo que que se halla seleccionado
			$( "#wfinicio" + x ).val( campo.value );

			//Marco el cambio por cada medicamento
			$( "#wfinicio" + x ).change();

			//Este es para el medicamento generico
			if( i == 0 ){
				//Cambio el valor del campo al mismo que que se halla seleccionado
				$( "#wfinicio" + artLevs[ x ].codLev ).val( campo.value );

				//Marco el cambio por cada medicamento
				$( "#wfinicio" + artLevs[ x ].codLev ).change();
			}

			i++;
		}
	}
}


function cancerlarModalArticulosLEVIC(){

	var indice = document.getElementById('indiceArticuloComponentes').value;
	var tipoProtocolo = document.getElementById('protocoloArticuloComponentes').value;

	quitarArticulo( indice, tipoProtocolo,'','detKardexAdd'+tipoProtocolo, 'LQ');

	document.getElementById("wcomponentesarticulo").value = "";
	document.getElementById("wcomponentesarticulocod").value = "";

	$.unblockUI();
}

function marcarCambioLEV( cmp, ido ){

	for( var x in artLevs ){

		if( artLevs[x].idoLev == ido ){


			//Las observaciones las pongo igual al escrito
			$( "#wtxtobsadd"+x ).val( $( cmp ).val() );

			//Hago el cambio por cada articulo
			marcarCambio( x.substr(0,2), x.substr(2) );


			//Las observaciones las pongo igual al escrito
			$( "#wtxtobsadd"+artLevs[x].codLev ).val( $( cmp ).val() );

			//Hago el cambio por cada articulo
			marcarCambio( artLevs[x].codLev.substr(0,2), artLevs[x].codLev.substr(2) );
		}
	}
}

/****************************************************************************************************************
 * Está función  busca todos los articulos que componen la Infusión continua y coloca la misma observación
 * a cada uno de los que componen el articulo
 *************************************************************************************************************/
function marcarCambioIC( cmp, ido ){

	for( var x in artLevs ){

		if( artLevs[x].idoLev == ido ){


			//Las observaciones las pongo igual al escrito
			$( "#wtxtobsadd"+x ).val( $( cmp ).val() );

			//Hago el cambio por cada articulo
			marcarCambio( x.substr(0,2), x.substr(2) );


			//Las observaciones las pongo igual al escrito
			$( "#wtxtobsadd"+artLevs[x].codLev ).val( $( cmp ).val() );

			//Hago el cambio por cada articulo
			marcarCambio( artLevs[x].codLev.substr(0,2), artLevs[x].codLev.substr(2) );
		}
	}
}

/**
 * Consultar
 */
porLevIC = '';
function autoLEVeIC( fam, cmp, tipo ){
	$("#wnombrefamilia").val( fam );
	$("#wnombrefamilia").search( $("#wnombrefamilia").val() );

	porLevIC = tipo;

	cmp.checked = !cmp.checked;
}

/************************************************************************
 * Registra un insumo de un articulo lev
 ************************************************************************/
function actualizarInsumoLev( idxLev ){


	var wemp_pmla = $('#wemp_pmla').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var historia = $('#whistoria').val();
	var ingreso = $('#wingreso').val();

	var wbasedato = $('#wbasedato').val();

	// var codLev = $( "#wnmmed"+tipPro+indice ).text().split( "-" )[0];
	var codLev = $( "#wnmmed"+artLevs[ idxLev ].codLev ).text().split( "-" )[0];

	// artLevs[ "LQ"+parseInt(elementosLev) ] = {
		// codLev: ultimoTipoProtocolo+ultimoIndice,
		// idoLev: '',												//Ido del LEV
		// insCod: arrInsumo[0],									//Códio del insumo
		// insIdi: '',												//Ido del insumo
		// insEle: arrInsumo[5] == 'ele' ? 'on': 'off',			//Es electrolito?
		// insVel: arrInsumo[5] == 'ele' ? arrInsumo[1]: '',		//Volumen del electrolito
		// insVdi: arrInsumo[5] == 'ele' ? arrInsumo[3]: '',		//Volumen por dilucion
		// insFso: arrInsumo[5] == 'ele' ? '': arrInsumo[4],		//Frecuencia de la solucion
		// insVto: arrInsumo[5] == 'ele' ? '': arrInsumo[1],		//Volumen total de la solución
		// insEst: 'new',											//Estado (new, mod, gra)
	// }

	// insFdi: $( "#slFrecDilLev", $( "#"+contLEV ) ).val(),	//Frecuencia de dilucion
			// insVfd

	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{
				consultaAjaxKardex: '66',
				wemp_pmla:      	wemp_pmla,
				wmovhos:           	wbasedato,
				his:           		$('#whistoria').val(),
				ing:           		$('#wingreso').val(),
				wuser:         		$('#usuario').val(),
				codLev:    			codLev,
				idolev:         	artLevs[ idxLev ].idoLev,
				codIns:   			artLevs[ idxLev ].insCod,
				idoIns:				artLevs[ idxLev ].insIdi,
				esEle: 				artLevs[ idxLev ].insEle,
				volEle: 			artLevs[ idxLev ].insVel,
				volDil: 			artLevs[ idxLev ].insVdi,
				fecSol: 			artLevs[ idxLev ].insFso,
				volTot: 			artLevs[ idxLev ].insVto,
				codFdi: 			artLevs[ idxLev ].insFdi,
				valFdi: 			artLevs[ idxLev ].insVfd,
				esInf: 				artLevs[ idxLev ].insInf,
				insDca:				artLevs[ idxLev ].insDca,	//Dosis calculada
				insCdc:				artLevs[ idxLev ].insCdc,	//Código de dosis calculada
				est: 				artLevs[ idxLev ].insEst
			},
			async: false,
			success:function(data_json){
				artLevs[ idxLev ].insEst = 'gra';
			}
		}
	);
}

/******************************************************************************************
 * Cambia la velocidad de infusión, ya sea para los LEVs o las IC
 ******************************************************************************************/
function cambiarVelocidadInfusionLevInf( valLevIdo ){

	var objAux = {};
	var mostrar = false;	//Indica si si se muestra la modal

	if( parseInt(valLevIdo) > 0 ){	//Si es el ido
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].idoLev == valLevIdo ){
				objAux[x] = artLevs[ x ];
				mostrar = true;
			}
		}
	}
	else{	//Si es con tipoprotocolo+consecutivo
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].codLev == valLevIdo ){
				objAux[x] = artLevs[ x ];
				mostrar = true;
			}
		}
	}

	//Recorro el objeto auxiliar que contiene todos los articulos a actualizar
	for( var x in objAux ){

		artLevs[ x ].insVfd = $( "#inFrecDil"+artLevs[ x ].idoLev ).val();
		artLevs[ x ].insFdi = $( "#slVelDil"+artLevs[ x ].idoLev ).val();
		artLevs[ x ].insEst = 'mod';
	}
}


/************************************************************************************
 * Muestra los datos guardados para una infusión
 ************************************************************************************/
function mostrarIC( valLevIdo ){

	var objAux = {};
	var mostrar = false;	//Indica si si se muestra la modal
	var codLev = "";

	if( parseInt(valLevIdo) > 0 ){
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].insEst != 'del' ){
				if( artLevs[x].idoLev == valLevIdo ){
					objAux[x] = artLevs[ x ];
					mostrar = true;
					codLev = artLevs[x].codLev;
				}
			}
		}
	}
	else{
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].insEst != 'del' ){
				if( artLevs[x].codLev == valLevIdo ){
					objAux[x] = artLevs[ x ];
					mostrar = true;
					codLev = artLevs[x].codLev;
				}
			}
		}
	}

	if( mostrar ){
		//Dejo todos los valoes en blanco
		$('input:text,textarea,select', $('#listaComponentesIC') ).val( '' );
		$('input:checkbox', $('#listaComponentesIC') ).attr( "checked", false );


		//Dejo los valores correspondientes de acuerdo a lo guardado
		//Recorro cada campo para ir llenando los datos
		for( var x in objAux ){

			var checkProtocolo = true;

			//Busco si es un electrolito o una solución
			var tipo = 'sol';
			if( objAux[x].insEle == 'on' ){
				tipo = 'ele';
			}

			//Esto es para saber el indice del articulo a llenar
			var idx = $( "["+tipo+objAux[x].insCod+"]", $( "#listaComponentesIC" ) ).attr( tipo+objAux[x].insCod );

			if( tipo == "sol" ){
				$( "#check_insumo"+idx, $( "#listaComponentesIC" ) )[0].checked = true;
				$( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).val( objAux[x].insVto );
				checkProtocolo = false;
			}
			else{
				$( "#check_insumo"+idx, $( "#listaComponentesIC" ) )[0].checked = true;
				$( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).val( objAux[x].insVel );
				$( "#volxdil"+idx, $( "#listaComponentesIC" ) ).val( objAux[x].insVdi );
			}

			$( "#slFrecDilLev", $( "#listaComponentesIC" ) ).val( objAux[x].insFdi );
			$( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val( objAux[x].insVfd );

			//Muestro la frecuencia, fecha y hora de inicio del medicamento
			//El dato se coge de cualquier solución o electrolito por que es igual para todos lo medicamentos
			// $("#bICFre", $( "#listaComponentesIC" ) ).text( $( "option[value="+objAux[x].insFre+"]", $( "#wmperiodicidades" ) ).text() );
			$("#bICFin", $( "#listaComponentesIC" ) ).text( objAux[x].insFin );
			$("#bICHin", $( "#listaComponentesIC" ) ).text( objAux[x].insHin );

			$("#inICDca"+idx, $( "#listaComponentesIC" ) ).val( objAux[x].insDca );
			$("#slICUdca"+idx, $( "#listaComponentesIC" ) ).val( objAux[x].insCdc );

			$("#dvObservacionesIC", $( "#listaComponentesIC" ) ).html( objAux[x].insObs );

			if( checkProtocolo )
				$( "#ckSinSolucion", $( "#listaComponentesIC" ) )[0].checked = true;
			else
				$( "#ckSinSolucion", $( "#listaComponentesIC" ) )[0].checked = false;
		}

		//Dejo las observaciones anteriores visible de antemano
		$("#dvObservacionesIC", $( "#listaComponentesIC" ) ).parent().parent().css({display:""});

		//Se deja el Ido del articulo LEV que se está mostrando
		$( "#idoMostrado", $('#listaComponentesIC') ).val( valLevIdo );

		//Muestro botones correspondientes
		$("#trNuevo", $( "#listaComponentesIC" ) ).css( { display: "none" } );
		$("#trModificar", $( "#listaComponentesIC" ) ).css( { display: "" } );
		$("[trMod]", $( "#listaComponentesIC" ) ).css( { display: "" } );
		$("[trNew]", $( "#listaComponentesIC" ) ).css( { display: "none" } );

		if( $("#dvObservacionesIC", $( "#listaComponentesIC" ) ).html() == "" ){
			$("#dvObservacionesIC", $( "#listaComponentesIC" ) ).parent().parent().css({display:"none"});
		}

		/**********************************************************************
		 * Impido que los campos se puedan modificar
		 **********************************************************************/
		$( "input:checkbox,select", $( "#listaComponentesIC" ) ).each(function(x){
			this.disabled = true;
		});

		$( "input:text", $( "#listaComponentesIC" ) ).each(function(x){
			this.readOnly = true;
		});
		/**********************************************************************/

		if( $( "#modificaIC" ).val() == 'on' ){

			var mostrarVelocidadInfusion = true;

			$( "[id^=check_insumo]", $( "#listaComponentesIC" ) ).each(function(x){

				if( this.checked ){

					var idx = this.id.substr( 12 );

					if( $( "#tdCbSol"+idx, $( "#listaComponentesIC" ) ).length == 0 ){
						$( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).attr({ readOnly: false });

						if( $( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val() == "" ){
							$( "#inICDca"+idx, $( "#listaComponentesIC" ) ).attr({ readOnly: false, disabled: false });
							$( "#slICUdca"+idx, $( "#listaComponentesIC" ) ).attr({ disabled: false });
							mostrarVelocidadInfusion = false;
						}
					}
					else{
						$( "#cantidad_insumo"+idx, $( "#listaComponentesIC" ) ).attr({ readOnly: false });
					}
				}
			});

			if( mostrarVelocidadInfusion ){
				$( "#slFrecDilLev", $( "#listaComponentesIC" ) ).attr({ disabled: false });
				$( "#txFrecDilLev", $( "#listaComponentesIC" ) ).attr({ disabled: false, readOnly: false });
			}
		}

		$( "#inDmaxIC", $( "#listaComponentesIC" ) ).attr({readOnly:false,disabled:false});
		$( "#inDttoIC", $( "#listaComponentesIC" ) ).attr({readOnly:false,disabled:false});
		if( $( "#modificaIC" ).val() == 'on' && parseInt( $( "#idoMostrado", $( "#listaComponentesIC" ) ).val() ) ){

			$( "#btGrabarModIC", $( "#listaComponentesIC" ) ).css({display: "" });

			$( "textarea", $( "#listaComponentesIC" ) ).val( $( "#wtxtobs"+codLev ).val() );
			$( "#inDttoIC", $( "#listaComponentesIC" ) ).val( $( "#wdiastto"+codLev ).val() );
			$( "#inDmaxIC", $( "#listaComponentesIC" ) ).val( $( "#wdosmax"+codLev ).val() );
			$( "#txObservacionesIC", $( "#listaComponentesIC" ) ).val( $( "#txICObservaciones"+valLevIdo ).val() );
			$( "#inDttoIC", $( "#listaComponentesIC" ) ).keyup();
			$( "#inDmaxIC", $( "#listaComponentesIC" ) ).keyup();

			/********************************************************************************
			 * Valido si se permite dosis máxima o no
			 ********************************************************************************/
			//Dejo por defecto días de tratamiento y dosis máximos como editables

			//Debo validar los permisos para los días máximos y días de tratamiento
			var perDtto = $( "#waccN\\.12" ).val().split( "," )[0] == 'S' ? true: false;
			var perDmax = $( "#waccN\\.13" ).val().split( "," )[0] == 'S' ? true: false;

			//Miro si por condicioón se permite
			var condicion = $( "#wcondicion"+codLev ).val();
			if( condicion != "" ){
				perDtto = dmaPorCondicionesSuministro[ condicion ].pdt;	//permite días de tratamiento
				perDmax = dmaPorCondicionesSuministro[ condicion ].pdm;	//permite dosis máxima
			}
			else{
				perDtto = false;
				perDmax = false;
			}

			if( !perDmax )
				$( "#inDmaxIC", $( "#listaComponentesIC" ) ).attr({readOnly:true,disabled:true});

			if( !perDtto )
				$( "#inDttoIC", $( "#listaComponentesIC" ) ).attr({readOnly:true,disabled:true});
			/********************************************************************************/

			$( "#ckSinSolucion", $( "#listaComponentesIC" ) ).attr({disabled:false, readOnly: false });
		}
		else if( !parseInt( $( "#idoMostrado", $( "#listaComponentesIC" ) ).val() ) ){
			$( "#btGrabarModIC", $( "#listaComponentesIC" ) ).css({display: "" });
			$( "textarea", $( "#listaComponentesIC" ) ).val( $( "#wtxtobs"+valLevIdo ).val() );
			$( "#inDttoIC", $( "#listaComponentesIC" ) ).val( $( "#wdiastto"+valLevIdo ).val() );
			$( "#inDttoIC", $( "#listaComponentesIC" ) ).keyup();
			$( "#inDmaxIC", $( "#listaComponentesIC" ) ).val( $( "#wdosmax"+valLevIdo ).val() );
			$( "#inDmaxIC", $( "#listaComponentesIC" ) ).keyup();
			$( "#dvObservacionesIC", $( "#listaComponentesIC" ) ).parent().parent().css({display:"none"});
			$( "#txObservacionesIC", $( "#listaComponentesIC" ) ).val( $( "#wtxtobs"+valLevIdo ).val() );
		}
		else{
			$( "#btGrabarModIC", $( "#listaComponentesIC" ) ).css({display: "none" });
			$( "textarea", $( "#listaComponentesIC" ) ).attr({disabled : true });
			$( "#dvObservacionesIC", $( "#listaComponentesIC" ) ).parent().parent().css({display:""});
		}

		var canWidth = $(window).width()*0.8;
		if( $( "#listaComponentesIC" ).width()-50 < canWidth )
			canWidth = $( "#listaComponentesIC" ).width();

		var canHeight = $(window).height()*0.8;
		if( $( "#listaComponentesIC" ).height()-50 < canHeight )
			canHeight = $( "#listaComponentesIC" ).height();

		$.blockUI({ message: $("#listaComponentesIC"),
			css: { left: ( $(window).width()-canWidth-50 )/2 +'px',
					top: ( $(window).height()-canHeight-50 )/2 +'px',
				  width: canWidth+25 + 'px',
				 height: canHeight+25 + 'px',
			   overflow: 'auto',
				 cursor: "point"
				 }
		});
	}
}


/*****************************************************************
 * Función llamada desde cerrarModalArticulos
 *
 * Se usa para validar todas las acciones de la modal para LEVS
 *****************************************************************/
function cerrarModalArticulosIC( indice, tipoProtocolo ){

	var todoOk = true;
	var msgError = "";

	/************************************************************************************************************************
	 * Valido que sea valido la selección de insumos
	 * - Se puede escoger un electrolito
	 * - Se debe escoger una solución
	 ************************************************************************************************************************/
	var valSol = false;
	var totSol = 0;
	if( !$( "#ckSinSolucion", $( "#listaComponentesIC" ) )[0].checked ){
		$( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesIC" ) ) ).each(function(j){
			if( this.checked ){
				valSol = true;
				totSol++;

				if( $( "#cantidad_insumo"+this.id.substr(12) ).val() == 0 ){
					todoOk = false;
					this.focus;
					msgError = "Debe ingresar el <b>VOL/TOTAL</b> para la <b>SOLUCION</b>";
				}
			}
		});

		if( totSol == 0 || totSol > 1 ){
			todoOk = false;
			msgError = "Debe seleccionar una sola <b>SOLUCION</b>.";
		}
	}

	if( !$( "#slFrecDilLev", $( "#listaComponentesIC" ) )[0].disabled ){
		var frecDi = $( "#slFrecDilLev", $( "#listaComponentesIC" ) ).val();
		if( frecDi == "" ){
			msgError = "Debe seleccionar una <b>VELOCIDAD DE INFUSI&Oacute;N</b>.";
			todoOk = false;
		}

		if( todoOk ){
			if( $( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val() == "" ){
				todoOk = false;
				msgError = "Debe seleccionar una <b>VELOCIDAD DE INFUSI&Oacute;N</b>.";
			}
		}
	}


	//Verifico que se seleccione un solo electrolito
	var totEle = 0;
	var totDca = 0;
	var totVdca = 0;
	var vMin = 0;
	var vMax = 0;
	$( "[id^=check_insumo]", $( "[id^=tdCbEle]", $( "#listaComponentesIC" ) ) ).each(function(j){
		if( this.checked ){

			var idx = this.id.substr(12);

			totEle++;

			if( $( "#inICDca"+this.id.substr(12) ).val()*1 > 0 && $.trim( $( "#slICUdca"+this.id.substr(12) ).val() ) == '' ){
				totDca++;
			}

			var valDosisCalculada = $( "#inICDca"+this.id.substr(12) ).val()*1;
			var valSelDosisCalculada = $( "#slICUdca"+this.id.substr(12) ).val();
			vMin = $( "#slICUdca"+this.id.substr(12)+" option[value="+valSelDosisCalculada+"]" ).attr( "vMin" );
			vMax = $( "#slICUdca"+this.id.substr(12)+" option[value="+valSelDosisCalculada+"]" ).attr( "vMax" );

			if( valDosisCalculada < vMin*1 || valDosisCalculada > vMax*1 ){
				$( "#inICDca"+this.id.substr(12) ).val('');
				totVdca++;
			}

			if( $.trim( $( "#cantidad_insumo"+idx ).val() ) == "" ){
				todoOk = false;
				if( $( "#tdCbEle"+idx ).length > 0  )
					msgError = "Debe ingresar un valor valido para <b>DOSIS</b> del <b>MEDICAMENTO</b>";
				else
					msgError = "Debe ingresar un valor valido para <b>VOL/TOTAL*</b> del <b>SOLUCIONES</b>";
			}
		}
	});

	if( totDca > 0 ){
		todoOk = false;
		msgError = "Debe seleccionar una <b>UNIDAD</b> para la <b>DOSIS CALCULADA</b>";
	}

	if( totEle == 0 || totEle > 1 ){
		todoOk = false;
		if( totEle == 0 )
			msgError = "Debe seleccionar un <b>MEDICAMENTO</b>";
		else
			msgError = "Debe seleccionar un solo <b>MEDICAMENTO</b>";
	}

	if( todoOk && totVdca > 0 ){
		todoOk = false;
		msgError = "El valor ingresado para el <b>MEDICAMENTO</b> no es valido.<br>El rango valido es mayor a <b>"+vMin+"</b> y  menor a <b>"+vMax+"</b>";
	}
	/**********************************************************************************************************************/



	if( todoOk ){

		var nombre2 = "";
		var nombre3 = "";
		$( "[id^=check_insumo]", $( "[id^=tdCbEle]", $( "#listaComponentesIC" ) ) ).each(function(x){
			if( this.checked ){
				fila = this.parentNode.parentNode;
				var idx = this.id.substr(12);
				nombre2 += $( "[tdEleGen"+idx+"]" ).html() +" <b>" + $( "#cantidad_insumo"+idx ).val() + " "+$( "[tdEleUVol"+idx+"]").html()+"</b><br>";

				if( $( "#inICDca"+idx, $( "#listaComponentesIC" ) ).val() != "" )
					nombre3 = "infusi&oacute;n continua a <b>"+$( "#inICDca"+idx, $( "#listaComponentesIC" ) ).val()+" "+$( "#slICUdca"+idx+" option[value='"+$( "#slICUdca"+idx ).val()+"']" ).html();//+"</b> para <b>"+$( "#wperiod"+tipoProtocolo+indice+" option[value='"+$( "#wperiod"+tipoProtocolo+indice ).val()+"']" ).html();+"</b>";
					// nombre3 = "infusi&oacute;n continua a <b>"+$( "#inICDca"+idx, $( "#listaComponentesIC" ) ).val()+" "+$( "#slICUdca"+idx+" option[value='"+$( "#slICUdca"+idx ).val()+"']" ).html()+"</b> para <b>"+$( "#wperiod"+tipoProtocolo+indice+" option[value='"+$( "#wperiod"+tipoProtocolo+indice ).val()+"']" ).html();+"</b>";
			}
		});

		var nombre1 = '';
		$( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesIC" ) ) ).each(function(x){
			if( this.checked ){
				fila = this.parentNode.parentNode;
				var idx = this.id.substr(12);

				nombre1 += " hasta <b>"+$( "#cantidad_insumo"+idx ).val() + " "+$( "[tdSolUVol"+idx+"]" ).html()+"</b> de "+$( "[tdSolGen"+idx+"]" ).html()+"<br>";//"<br>infusi&oacute;n continua para <b>"+$( "#wperiod"+tipoProtocolo+indice+" option[value='"+$( "#wperiod"+tipoProtocolo+indice ).val()+"']" ).html();+"</b>";
			}
		});

		if( nombre3 == "" )
			nombre3 = "infusi&oacute;n continua a "+$( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val()+" "+$( "#slFrecDilLev option[value='"+$( "#slFrecDilLev", $( "#listaComponentesIC" ) ).val()+"']", $( "#listaComponentesIC" ) ).html();//+" para <b>"+$( "#wperiod"+tipoProtocolo+indice+" option[value='"+$( "#wperiod"+tipoProtocolo+indice ).val()+"']" ).html();+"</b>";
			// nombre3 = "infusi&oacute;n continua a "+$( "#txFrecDilLev", $( "#listaComponentesIC" ) ).val()+" "+$( "#slFrecDilLev option[value='"+$( "#slFrecDilLev", $( "#listaComponentesIC" ) ).val()+"']", $( "#listaComponentesIC" ) ).html()+" para <b>"+$( "#wperiod"+tipoProtocolo+indice+" option[value='"+$( "#wperiod"+tipoProtocolo+indice ).val()+"']" ).html();+"</b>";

		var nombre  = nombre2+nombre1+nombre3;

		var newDiv = document.createElement( "div" );

		$( newDiv ).html( nombre );

		$("#wcolmed"+tipoProtocolo+indice ).append( newDiv );

		//Deje el nombre INFUSION CONTINUA POR DEFECTO
		var nombreArt = $("#wnmmed"+tipoProtocolo+indice ).val().split("-");
		$("#wnmmed"+tipoProtocolo+indice ).val( nombreArt[0]+"-"+nombreArt[1]+"-*INFUSION CONTINUA");

		$( "#wtxtobs"+tipoProtocolo+indice ).val( $("#txObservacionesIC", $( "#listaComponentesIC" ) ).val() );


		//Los campos dondo dosis calculada sea vacia los dejo sin seleccionar
		$( "[id^=inICDca]", $( "#listaComponentesIC" ) ).each(function(j){
			if( $( this ).val() == '' || $( this ).val()*1 == 0 ){
				$( "#slICUdca"+this.id.substr(7) ).val( '' );
			}
		});

		var componentesLQ =  document.getElementById("wcomponentesarticulocod").value;

		adicionMultiple = true;

		$.unblockUI();

		if(isset(document.getElementById("wcomponentesarticulo")))
			document.getElementById("wcomponentesarticulo").value = "";
		if(isset(document.getElementById("wcomponentesarticulocod")))
			document.getElementById("wcomponentesarticulocod").value = "";

		eleccionMedicamentosInsumos(componentesLQ, indice, tipoProtocolo, "IC" );
		agregarMultiplesArticulos();
		// quitarArticulo(indice,'%','','detKardexAddN','LQ');

		grupoGenerico = "";

		document.getElementById("wcomponentesarticulo").value = "";
		document.getElementById("wcomponentesarticulocod").value = "";
	}
	else{
		jAlert( msgError, 'ALERTA' );
	}

}

/************************************************************************************************
 * elimina todos los articulos correspondientes a un articulo LEV de acuerdo a su indice
 * tipo de protocolo+cont
 ************************************************************************************************/
function eliminarArtsLevPorIdx( codLev ){

	var objAux = {};	//Un objeto auxiliar

	//Busco en el objeto artLevs cuales son los articulos que se deben eliminar
	for( var x in artLevs ){
		if( artLevs[x].codLev == codLev ){
			objAux[x] = artLevs[ x ];
			mostrar = true;
		}
	}

	try{
		var j = $( "[id^=widoriginal][value="+codLev+"]" )[0].id.substr(11)
		objAux[j] = "";	//Solo se requiere el indice y se crea para borrar los del código tipo LQ0000
	}
	catch(e){
	}

	//Recorro el objeto auxiliar que contiene todos los articulos a eliminar
	for( var x in objAux ){

		var tipoProtocolo = x.substr( 0, x.length-1 );
		var idxElemento   = x.substr( -1 );

		//Si se elimina el lev se busca los datos correspondientes de cada articulo a eliminar
		quitarArticulo(idxElemento, tipoProtocolo, document.getElementById( "wperiod" + x ), '', true );
	}
}


function suspenderArtsLev( codLev, esLev ){

	// var codLev = $( "#idoMostrado", $('#listaComponentesLEV') ).val();
	var tipo = "LEV";
	if( !esLev){
		tipo = 'INF';
	}

	var clase_asociada = $( "#trIdoLev"+codLev ).attr("class");

	var accion = "";
	if(clase_asociada != 'suspendido'){

		accion = "suspender";

	}else{

		accion = "activar";

	}

	jConfirm( "¿Desea "+accion+" el articulo "+( esLev ? "LEV" : "de INFUSION" )+"?", 'Suspender '+tipo, function(r){

		if(r){

			var objAux = {};	//Un objeto auxiliar

			//Busco en el objeto artLevs cuales son los articulos que se deben eliminar
			for( var x in artLevs ){
				if( artLevs[x].idoLev == codLev ){
					objAux[x] = artLevs[ x ];
					mostrar = true;
				}
			}

			try{
				var j = $( "[id^=widoriginal][value="+codLev+"]" )[0].id.substr(11)
				objAux[j] = "";	//Solo se requiere el indice y se crea para borrar los del código tipo LQ0000
			}
			catch(e){}

			//Recorro el objeto auxiliar que contiene todos los articulos a eliminar
			for( var x in objAux ){

				var tipoProtocolo = x.substr( 0, x.length-1 );
				var idxElemento   = x.substr( -1 );

				//Si se elimina el lev se busca los datos correspondientes de cada articulo a eliminar
				suspenderArticulo(idxElemento, tipoProtocolo, false );
			}


			if( accion == "suspender" )
				$( "#trIdoLev"+codLev )[0].className = 'suspendido';
			else if( !esLev )
				$( "#trIdoLev"+codLev )[0].className = 'fila2';
			else
				$( "#trIdoLev"+codLev )[0].className = 'fila1';

			$.unblockUI();
		}

	});
}

/************************************************************************************************
 * elimina todos los articulos correspondientes a un articulo LEV
 ************************************************************************************************/
function eliminarArtsLev( esLev ){

	var codLev = $( "#idoMostrado", $('#listaComponentesLEV') ).val();
	var tipo = "LEV";
	if( !esLev){
		tipo = 'INF';
	}

	jConfirm( "¿Desea eliminar el articulo "+( esLev ? "LEV" : "de INFUSION" )+"?", 'Eliminar '+tipo, function(r){

		if(r){

			var objAux = {};	//Un objeto auxiliar

			//Busco en el objeto artLevs cuales son los articulos que se deben eliminar
			for( var x in artLevs ){
				if( artLevs[x].idoLev == codLev ){
					objAux[x] = artLevs[ x ];
					mostrar = true;
				}
			}

			try{
				var j = $( "[id^=widoriginal][value="+codLev+"]" )[0].id.substr(11)
				objAux[j] = "";	//Solo se requiere el indice y se crea para borrar los del código tipo LQ0000
			}
			catch(e){
			}

			//Recorro el objeto auxiliar que contiene todos los articulos a eliminar
			for( var x in objAux ){

				var tipoProtocolo = x.substr( 0, x.length-1 );
				var idxElemento   = x.substr( -1 );

				//Si se elimina el lev se busca los datos correspondientes de cada articulo a eliminar
				quitarArticulo(idxElemento, tipoProtocolo, document.getElementById( "wperiod" + x ), '', true );
			}

			$( "#trIdoLev"+codLev ).remove();

			$.unblockUI();
		}

	});
}


/************************************************************************************
 * Muestra los datos guardados para una infusión
 ************************************************************************************/
function mostrarLEV( valLevIdo ){

	var objAux = {};
	var mostrar = false;	//Indica si si se muestra la modal
	var codLev = "";

	if( parseInt(valLevIdo) > 0 ){
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].idoLev == valLevIdo ){
				objAux[x] = artLevs[ x ];
				mostrar = true;
				codLev = artLevs[x].codLev;
			}
		}
	}
	else{
		//Busco en el objeto artLevs cuales son los articulos que se deben mostrar
		for( var x in artLevs ){
			if( artLevs[x].codLev == valLevIdo ){
				objAux[x] = artLevs[ x ];
				mostrar = true;
				codLev = artLevs[x].codLev;
			}
		}
	}

	if( mostrar ){
		//Dejo todos los valoes en blanco
		$('input:text, select', $('#listaComponentesLEV') ).val( '' );
		$('input:checkbox', $('#listaComponentesLEV') ).attr( "checked", false );


		//Dejo los valores correspondientes de acuerdo a lo guardado
		//Recorro cada campo para ir llenando los datos
		for( var x in objAux ){

			//Busco si es un electrolito o una solución
			var tipo = 'sol';
			if( objAux[x].insEle == 'on' ){
				tipo = 'ele';
			}

			//Esto es para saber el indice del articulo a llenar
			var idx = $( "["+tipo+objAux[x].insCod+"]", $( "#listaComponentesLEV" ) ).attr( tipo+objAux[x].insCod );

			if( tipo == "sol" ){
				$( "#check_insumo"+idx, $( "#listaComponentesLEV" ) )[0].checked = true;
				$( "#cantidad_insumo"+idx, $( "#listaComponentesLEV" ) ).val( objAux[x].insVto );
			}
			else{
				$( "#check_insumo"+idx, $( "#listaComponentesLEV" ) )[0].checked = true;
				$( "#cantidad_insumo"+idx, $( "#listaComponentesLEV" ) ).val( objAux[x].insVel );
				$( "#volxdil"+idx, $( "#listaComponentesLEV" ) ).val( objAux[x].insVdi );
			}

			$( "#slFrecDilLev", $( "#listaComponentesLEV" ) ).val( objAux[x].insFdi );
			$( "#txFrecDilLev", $( "#listaComponentesLEV" ) ).val( objAux[x].insVfd );

			//Muestro la frecuencia, fecha y hora de inicio del medicamento
			//El dato se coge de cualquier solución o electrolito por que es igual para todos lo medicamentos
			//$("#bLevFre", $( "#listaComponentesLEV" ) ).text( $( "option[value="+objAux[x].insFre+"]", $( "#wmperiodicidades" ) ).text() );
			$("#bLevFin", $( "#listaComponentesLEV" ) ).text( objAux[x].insFin );
			$("#bLevHin", $( "#listaComponentesLEV" ) ).text( objAux[x].insHin );

			$("#dvObservacionesLEV", $( "#listaComponentesLEV" ) ).html( objAux[x].insObs );
		}

		//Dejo las observaciones anteriores visibles
		$("#dvObservacionesLEV", $( "#listaComponentesLEV" ) ).parent().parent().css({display:""});

		//Se deja el Ido del articulo LEV que se está mostrando
		$( "#idoMostrado", $('#listaComponentesLEV') ).val( valLevIdo );

		//Muestro botones correspondientes
		$("#trNuevo", $( "#listaComponentesLEV" ) ).css( { display: "none" } );
		$("#trModificar", $( "#listaComponentesLEV" ) ).css( { display: "" } );
		$("[trMod]", $( "#listaComponentesLEV" ) ).css( { display: "" } );
		$("[trNew]", $( "#listaComponentesLEV" ) ).css( { display: "none" } );

		//Si no hay nada en observaciones anteriores no se muestra
		if( $("#dvObservacionesLEV", $( "#listaComponentesLEV" ) ).html() == "" )
			$("#dvObservacionesLEV", $( "#listaComponentesLEV" ) ).parent().parent().css({display:"none"});

		/**********************************************************************
		 * Impido que los campos se puedan modificar
		 **********************************************************************/
		$( "input:checkbox,select", $( "#listaComponentesLEV" ) ).each(function(x){
			this.disabled = true;
		});

		$( "input:text", $( "#listaComponentesLEV" ) ).each(function(x){
			this.readOnly = true;
		});
		/**********************************************************************/

		if( $( "#modificaLev" ).val() == 'on' ){

			$( "[id^=check_insumo]", $( "#listaComponentesLEV" ) ).each(function(x){

				if( this.checked ){

					var idx = this.id.substr( 12 );

					if( $( "#tdCbSol"+idx, $( "#listaComponentesLEV" ) ).length == 0 ){
						$( "#cantidad_insumo"+idx, $( "#listaComponentesLEV" ) ).attr({ readOnly: false });

						$( "#volxdil"+idx, $( "#listaComponentesLEV" ) ).attr({ readOnly: false });
						$( "#volxdil"+idx, $( "#listaComponentesLEV" ) ).attr({ disabled: false });
					}
					else{
						$( "#cantidad_insumo"+idx, $( "#listaComponentesLEV" ) ).attr({ readOnly: false });;
					}
				}
			});

			$( "#slFrecDilLev", $( "#listaComponentesLEV" ) ).attr({ disabled: false });
			$( "#txFrecDilLev", $( "#listaComponentesLEV" ) ).attr({ readOnly: false });
		}

		$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).attr({readOnly:false,disabled:false});
		$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).attr({readOnly:false,disabled:false});
		if( $( "#modificaLev" ).val() == 'on' && parseInt( $( "#idoMostrado", $( "#listaComponentesLEV" ) ).val() ) ){
			$( "#btGrabarModLev", $( "#listaComponentesLEV" ) ).css({display: "" });
			$( "textarea", $( "#listaComponentesLEV" ) ).val( $( "#wtxtobs"+codLev ).val() );
			$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).val( $( "#wdiastto"+codLev ).val() );
			$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).keyup();
			$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).val( $( "#wdosmax"+codLev ).val() );
			$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).keyup();
			$( "#txObservacionesLEV", $( "#listaComponentesLEV" ) ).val( $( "#txICObservaciones"+valLevIdo ).val() );

			/********************************************************************************
			 * Valido si se permite dosis máxima o no
			 ********************************************************************************/
			//Dejo por defecto días de tratamiento y dosis máximos como editables

			//Debo validar los permisos para los días máximos y días de tratamiento
			var perDtto = $( "#waccN\\.12" ).val().split( "," )[0] == 'S' ? true: false;
			var perDmax = $( "#waccN\\.13" ).val().split( "," )[0] == 'S' ? true: false;

			//Miro si por condicioón se permite
			var condicion = $( "#wcondicion"+codLev ).val();
			if( condicion != "" ){
				perDtto = dmaPorCondicionesSuministro[ condicion ].pdt;	//permite días de tratamiento
				perDmax = dmaPorCondicionesSuministro[ condicion ].pdm;	//permite dosis máxima
			}
			else{
				perDtto = false;
				perDmax = false;
			}

			if( !perDmax )
				$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).attr({readOnly:true,disabled:true});

			if( !perDtto )
				$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).attr({readOnly:true,disabled:true});
			/********************************************************************************/
		}
		else if( !parseInt( $( "#idoMostrado", $( "#listaComponentesLEV" ) ).val() ) ){
			$( "#btGrabarModIC", $( "#listaComponentesLEV" ) ).css({display: "" });
			$( "textarea", $( "#listaComponentesLEV" ) ).val( $( "#wtxtobs"+valLevIdo ).val() );
			$( "#inDttolev", $( "#listaComponentesLEV" ) ).val( $( "#wdiastto"+valLevIdo ).val() );
			$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).keyup();
			$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).val( $( "#wdosmax"+valLevIdo ).val() );
			$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).keyup();
			$( "#dvObservacionesLEV", $( "#listaComponentesLEV" ) ).parent().parent().css({display:"none"});
			$( "#txObservacionesLEV", $( "#listaComponentesLEV" ) ).val( $( "#wtxtobs"+valLevIdo ).val() );
		}
		else{
			$( "#btGrabarModLev", $( "#listaComponentesLEV" ) ).css({display: "none" });
			$( "textarea", $( "#listaComponentesLEV" ) ).attr({disabled : true });
			$( "#dvObservacionesIC", $( "#listaComponentesLEV" ) ).parent().parent().css({display:""});
		}

		var canWidth = $(window).width()*0.8;
		if( $( "#listaComponentesLEV" ).width()-50 < canWidth )
			canWidth = $( "#listaComponentesLEV" ).width();

		var canHeight = $(window).height()*0.8;
		if( $( "#listaComponentesLEV" ).height()-50 < canHeight )
			canHeight = $( "#listaComponentesLEV" ).height();

		$.blockUI({ message: $("#listaComponentesLEV"),
			css: { left: ( $(window).width()-canWidth-50 )/2 +'px',
					top: ( $(window).height()-canHeight-50 )/2 +'px',
				  width: canWidth+25 + 'px',
				 height: canHeight+25 + 'px',
			   overflow: 'auto',
				 cursor: "point"
				 }
		});
	}
}

/************************************************************************
 * Elimina un insumo de un articulo Lev o IC
 ************************************************************************/
function eliminarInsumosLev( idxLev ){


	var wemp_pmla = $('#wemp_pmla').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var historia = $('#whistoria').val();
	var ingreso = $('#wingreso').val();

	var wbasedato = $('#wbasedato').val();

	// var codLev = $( "#wnmmed"+tipPro+indice ).text().split( "-" )[0];
	var codLev = $( "#wnmmed"+artLevs[ idxLev ].codLev ).text().split( "-" )[0];

	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{
				consultaAjaxKardex: '67',
				wemp_pmla:      	wemp_pmla,
				wmovhos:           	wbasedato,
				his:           		$('#whistoria').val(),
				ing:           		$('#wingreso').val(),
				codLev:    			codLev,
				idolev:         	artLevs[ idxLev ].idoLev,
				codIns:   			artLevs[ idxLev ].insCod,
				idoIns:				artLevs[ idxLev ].insIdi,
				esEle: 				artLevs[ idxLev ].insEle,
				volEle: 			artLevs[ idxLev ].insVel,
				volDil: 			artLevs[ idxLev ].insVdi,
				fecSol: 			artLevs[ idxLev ].insFso,
				volTot: 			artLevs[ idxLev ].insVto,
				codFdi: 			artLevs[ idxLev ].insFdi,
				valFdi: 			artLevs[ idxLev ].insVfd,
				esInf: 				artLevs[ idxLev ].insInf,
				insDca:				artLevs[ idxLev ].insDca,
				insCdc:				artLevs[ idxLev ].insCdc,
				insObs:				artLevs[ idxLev ].insObs,
				est: 				artLevs[ idxLev ].insEst,
				sinSol:				artLevs[ idxLev ].sinSol,
			},
			async: false,
			success:function(data_json){
				artLevs[ idxLev ].insEst = 'gra';
			}
		}
	);
}

/************************************************************************
 * Registra un insumo de un articulo lev
 ************************************************************************/
function registrarInsumoLev( idxLev ){


	var wemp_pmla = $('#wemp_pmla').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var historia = $('#whistoria').val();
	var ingreso = $('#wingreso').val();

	var wbasedato = $('#wbasedato').val();

	// var codLev = $( "#wnmmed"+tipPro+indice ).text().split( "-" )[0];
	var codLev = $( "#wnmmed"+artLevs[ idxLev ].codLev ).text().split( "-" )[0];

	// artLevs[ "LQ"+parseInt(elementosLev) ] = {
		// codLev: ultimoTipoProtocolo+ultimoIndice,
		// idoLev: '',												//Ido del LEV
		// insCod: arrInsumo[0],									//Códio del insumo
		// insIdi: '',												//Ido del insumo
		// insEle: arrInsumo[5] == 'ele' ? 'on': 'off',			//Es electrolito?
		// insVel: arrInsumo[5] == 'ele' ? arrInsumo[1]: '',		//Volumen del electrolito
		// insVdi: arrInsumo[5] == 'ele' ? arrInsumo[3]: '',		//Volumen por dilucion
		// insFso: arrInsumo[5] == 'ele' ? '': arrInsumo[4],		//Frecuencia de la solucion
		// insVto: arrInsumo[5] == 'ele' ? '': arrInsumo[1],		//Volumen total de la solución
		// insEst: 'new',											//Estado (new, mod, gra)
	// }

	// insFdi: $( "#slFrecDilLev", $( "#"+contLEV ) ).val(),	//Frecuencia de dilucion
			// insVfd

	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{
				consultaAjaxKardex: '65',
				wemp_pmla:      	wemp_pmla,
				wmovhos:           	wbasedato,
				his:           		$('#whistoria').val(),
				ing:           		$('#wingreso').val(),
				codLev:    			codLev,
				idolev:         	artLevs[ idxLev ].idoLev,
				codIns:   			artLevs[ idxLev ].insCod,
				idoIns:				artLevs[ idxLev ].insIdi,
				esEle: 				artLevs[ idxLev ].insEle,
				volEle: 			artLevs[ idxLev ].insVel,
				volDil: 			artLevs[ idxLev ].insVdi,
				fecSol: 			artLevs[ idxLev ].insFso,
				volTot: 			artLevs[ idxLev ].insVto,
				codFdi: 			artLevs[ idxLev ].insFdi,
				valFdi: 			artLevs[ idxLev ].insVfd,
				esInf: 				artLevs[ idxLev ].insInf,
				insDca:				artLevs[ idxLev ].insDca,
				insCdc:				artLevs[ idxLev ].insCdc,
				insObs:				artLevs[ idxLev ].insObs,
				est: 				artLevs[ idxLev ].insEst,
				sinSol:				artLevs[ idxLev ].sinSol,
			},
			async: false,
			success:function(data_json){
				artLevs[ idxLev ].insEst = 'gra';
			}
		}
	);
}


function grabarListaInsumosLev(){

	/********************************************************************************************************
	 * Busco los LEV a grabar
	 *
	 * - Se Lee toda la lista de levs que hallan
	 * - Se busca toda la lista de
	 ********************************************************************************************************/

	// artLevs[ "LQ"+parseInt(elementosLev) ] = {
		// codLev: ultimoTipoProtocolo+ultimoIndice,
		// idoLev: '',												//Ido del LEV
		// insCod: arrInsumo[0],									//Códio del insumo
		// insIdi: '',												//Ido del insumo
		// insEle: arrInsumo[5] == 'ele' ? 'on': 'off',			//Es electrolito?
		// insVel: arrInsumo[5] == 'ele' ? arrInsumo[1]: '',		//Volumen del electrolito
		// insVdi: arrInsumo[5] == 'ele' ? arrInsumo[3]: '',		//Volumen por dilucion
		// insFso: arrInsumo[5] == 'ele' ? '': arrInsumo[4],		//Frecuencia de la solucion
		// insVto: arrInsumo[5] == 'ele' ? '': arrInsumo[1],		//Volumen total de la solución
		// insEst: 'new',											//Estado (new, mod, gra)
	// }

	for( idxLev in artLevs ){

		//Agrego los datos faltantes correspondientes que son idoLev y insIdi
		var codigoLev = $( "#widoriginal"+artLevs[ idxLev ].codLev );

		//Si existe el ido se continua para poder guardarlo
		if( codigoLev.length > 0 ){

			artLevs[ idxLev ].idoLev = codigoLev.val();

			//Si no encuentra el ido del lev no se inserta
			if( artLevs[ idxLev ].idoLev ){

				//Busco el ido del insumo
				var codigoIns = $( "#widoriginal"+idxLev );

				//Si existe el ido del insumo lev se continua para guardarlo
				if( codigoIns.length > 0 ){

					artLevs[ idxLev ].insIdi = codigoIns.val();

					//Si encuentra el ido del insumo se registra
					if( artLevs[ idxLev ].insIdi > 0 ){

						switch( artLevs[ idxLev ].insEst ){

							case 'new':	//registra medicamento nuevo
								//Si el estado es new procedo a guardarlo
								registrarInsumoLev( idxLev );
								break;

							case 'mod':	//modifica un registro existente
								//Si el estado es new procedo a guardarlo
								actualizarInsumoLev( idxLev );
								break;

							case 'del':	//elimina un registro existente
								//Si el estado es new procedo a guardarlo
								eliminarInsumosLev( idxLev );
								break;

							default: break;
						}
					}
				}
				else{
					// console.log( "Codigo ido del insumo lev no encontrado" );
				}
			}
		}
		else{
			// console.log( "Codigo ido del lev no encontrado" );
		}
	}
	/********************************************************************************************************/
}



function validarHoraInicioPorDA( cmp, tipoProtocolo, idx ){

	if( cmp.checked ){
		
		var tiempoMarcarDaNE = $( "#tiempoMinimoMarcarDANE" ).val()*60000;
		
		var fechaHoraIni = $( "#wfinicio"+tipoProtocolo+idx  ).val().split( "a las:" );
		var fecha = $.trim( fechaHoraIni[0] ).split( "-" );
		var hora = $.trim( fechaHoraIni[1] ).split( ":" );

		date = new Date( fecha[0], fecha[1]-1, fecha[2], hora[0], 0, 0 );	//Creo objeto Date con la fecha y hora inicial del medicamento

		var fechaHoraActual = new Date();		//Objeto con la fecha y hora actual
		
		//Verifico si la fecha Actual es mayor a la fecha y hora de inicio del medicamento
		if( fechaHoraActual.getTime() + tiempoMarcarDaNE - date.getTime() >= 0 ){
			alert( "Debe cambiar la fecha y hora de inicio del medicamento para una ronda posterior" );
			cmp.checked = false;
		}
	}
}

function cambiar_estado_examen(wemp_pmla, wfec, wexam, wing, whis, wfechadataexamen, whoradataexamen, wfechagk, wordennro, wordite, campo, wid, wcco, whce, wcontrol_ordenes, wtexto_examen, westado_registro, wuser)
      {

        var wbasedato = $('#wbasedato').val();
        var westado = $("#estado_"+wid).val();

		$.post("ordenes.inc.php",
				{
					consultaAjaxKardex: '64',
					wemp_pmla:      	wemp_pmla,
					wmovhos:           	wbasedato,
					wexam:           	wexam,
					wfec:           	wfec,
					wing:    			wing,
					whis:         		whis,
					wfechadataexamen:   wfechadataexamen,
					whoradataexamen:	whoradataexamen,
					wfechagk : 			wfechagk,
					wordennro : 		wordennro,
					wordite : 			wordite,
					westado : 			westado,
					wid : 				wid,
					wcco:				wcco,
					wcontrol_ordenes:	wcontrol_ordenes,
					wtexto_examen:		wtexto_examen,
					westado_registro:	westado_registro,
					wuser:				wuser
				}
				,function(data) {}
			);
		}


function grabarAuditoriaProcSinCTC( cdProc, nomExamen ){

	var wemp_pmla = $('#wemp_pmla').val();
	var wfechagrabacion = $('#wfechagrabacion').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var wcedula = $('#wcedula').val();
	var tipoDocumento = $('#wtipodoc').val();
	var historia = $('#whistoria').val();
	var ingreso = $('#wingreso').val();


	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{

				consultaAjaxKardex:       	'63',
				wemp_pmla:					wemp_pmla,
				whistoria:					historia,
				wingreso:					ingreso,
				wfechagrabacion:			wfechagrabacion,
				wbasedato:					wbasedato,
				wbasedatohce:				wbasedatohce,
				whce:						wbasedatohce,
				wcedula:					wcedula,
				tipoDocumento:				tipoDocumento,
				wusuario:					wusuario,
				codigo_procedimiento:		cdProc,
				nombreExamen:				nomExamen

			},
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{


				}
			}
		}
	);


}


function grabarAuditoriaArtSinCTC( cdArt, tipoProtocolo, idElemento ){

	var wemp_pmla = $('#wemp_pmla').val();
	var wfechagrabacion = $('#wfechagrabacion').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var wcedula = $('#wcedula').val();
	var tipoDocumento = $('#wtipodoc').val();
	var historia = $('#whistoria').val();
	var ingreso = $('#wingreso').val();


	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{

				consultaAjaxKardex:       	'62',
				wemp_pmla:					wemp_pmla,
				whistoria:					historia,
				wingreso:					ingreso,
				wfechagrabacion:			wfechagrabacion,
				wbasedato:					wbasedato,
				wbasedatohce:				wbasedatohce,
				whce:						wbasedatohce,
				wcedula:					wcedula,
				tipoDocumento:				tipoDocumento,
				wusuario:					wusuario,
				codigo_articulo:			cdArt

			},
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{


				}
			}
		}
	);


}

/********************************************************************************
 * Deshabilita el campo de dias de tratamiento si dosis maximas tiene algun valor
 ********************************************************************************/
function inhabilitarDiasTratamientoBusc( campo, id ){

	var inDtto = document.getElementById( id );

	if( campo.readOnly && inDtto.readOnly ){
		return;
	}

	if( campo.value != '' ){
		inDtto.readOnly = true;
	}
	else{
		inDtto.readOnly = false;
	}


	if( $( "#wcondicionsum" ).val() != "" ){
		try{
			var valPdt = dmaPorCondicionesSuministro[ $( "#wcondicionsum" ).val() ].pdt;

			//Si tiene condicion y es una condicion que no permite
			if( !valPdt ){
				inDosisMaximas.readOnly = true;
			}
		}
		catch(e){
		}
	}
}

/********************************************************************************
 * Deshabilita el campo de dosis maxima si dias de tratamiento tiene algun valor
 ********************************************************************************/
function inhabilitarDosisMaximaBusc( campo, id ){

	var inDosisMaximas = document.getElementById( id );

	if( inDosisMaximas.readOnly && campo.readOnly ){
		return;
	}

	if( campo.value != '' ){
		inDosisMaximas.readOnly = true;
	}
	else{
		inDosisMaximas.readOnly = false;
	}

	if( $( "#wcondicionsum" ).val() != "" ){
		try{
			var valPdm = dmaPorCondicionesSuministro[ $( "#wcondicionsum" ).val() ].pdm;

			//Si tiene condicion y es una condicion que no permite
			if( !valPdm ){
				inDosisMaximas.readOnly = true;
			}
		}
		catch(e){
		}
	}
}



function utf8_encode(argString) {
  //  discuss at: http://phpjs.org/functions/utf8_encode/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: sowberry
  // improved by: Jack
  // improved by: Yves Sucaet
  // improved by: kirilloid
  // bugfixed by: Onno Marsman
  // bugfixed by: Onno Marsman
  // bugfixed by: Ulrich
  // bugfixed by: Rafal Kukawski
  // bugfixed by: kirilloid
  //   example 1: utf8_encode('Kevin van Zonneveld');
  //   returns 1: 'Kevin van Zonneveld'

  if (argString === null || typeof argString === 'undefined') {
    return '';
  }

  var string = (argString + ''); // .replace(/\r\n/g, "\n").replace(/\r/g, "\n");
  var utftext = '',
    start, end, stringl = 0;

  start = end = 0;
  stringl = string.length;
  for (var n = 0; n < stringl; n++) {
    var c1 = string.charCodeAt(n);
    var enc = null;

    if (c1 < 128) {
      end++;
    } else if (c1 > 127 && c1 < 2048) {
      enc = String.fromCharCode(
        (c1 >> 6) | 192, (c1 & 63) | 128
      );
    } else if ((c1 & 0xF800) != 0xD800) {
      enc = String.fromCharCode(
        (c1 >> 12) | 224, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    } else { // surrogate pairs
      if ((c1 & 0xFC00) != 0xD800) {
        throw new RangeError('Unmatched trail surrogate at ' + n);
      }
      var c2 = string.charCodeAt(++n);
      if ((c2 & 0xFC00) != 0xDC00) {
        throw new RangeError('Unmatched lead surrogate at ' + (n - 1));
      }
      c1 = ((c1 & 0x3FF) << 10) + (c2 & 0x3FF) + 0x10000;
      enc = String.fromCharCode(
        (c1 >> 18) | 240, ((c1 >> 12) & 63) | 128, ((c1 >> 6) & 63) | 128, (c1 & 63) | 128
      );
    }
    if (enc !== null) {
      if (end > start) {
        utftext += string.slice(start, end);
      }
      utftext += enc;
      start = end = n + 1;
    }
  }

  if (end > start) {
    utftext += string.slice(start, stringl);
  }

  return utftext;
}


function utf8_decode(str_data) {
  //  discuss at: http://phpjs.org/functions/utf8_decode/
  // original by: Webtoolkit.info (http://www.webtoolkit.info/)
  //    input by: Aman Gupta
  //    input by: Brett Zamir (http://brett-zamir.me)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Norman "zEh" Fuchs
  // bugfixed by: hitwork
  // bugfixed by: Onno Marsman
  // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: kirilloid
  //   example 1: utf8_decode('Kevin van Zonneveld');
  //   returns 1: 'Kevin van Zonneveld'

  var tmp_arr = [],
    i = 0,
    ac = 0,
    c1 = 0,
    c2 = 0,
    c3 = 0,
    c4 = 0;

  str_data += '';

  while (i < str_data.length) {
    c1 = str_data.charCodeAt(i);
    if (c1 <= 191) {
      tmp_arr[ac++] = String.fromCharCode(c1);
      i++;
    } else if (c1 <= 223) {
      c2 = str_data.charCodeAt(i + 1);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
      i += 2;
    } else if (c1 <= 239) {
      // http://en.wikipedia.org/wiki/UTF-8#Codepage_layout
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
      i += 3;
    } else {
      c2 = str_data.charCodeAt(i + 1);
      c3 = str_data.charCodeAt(i + 2);
      c4 = str_data.charCodeAt(i + 3);
      c1 = ((c1 & 7) << 18) | ((c2 & 63) << 12) | ((c3 & 63) << 6) | (c4 & 63);
      c1 -= 0x10000;
      tmp_arr[ac++] = String.fromCharCode(0xD800 | ((c1 >> 10) & 0x3FF));
      tmp_arr[ac++] = String.fromCharCode(0xDC00 | (c1 & 0x3FF));
      i += 4;
    }
  }

  return tmp_arr.join('');
}



function ver_ordenes_anteriores(fecha){

 $('#ordenes_anteriores_'+fecha).toggle("1000");

}

function imprimirOrdenAlta(historia,ingreso,tipoDeOrden,numeroDeOrden,nroItem,wchkimpexamen){

	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	var wfechagrabacion = $('#wfechagrabacion').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var wcedula = $('#wcedula').val();
	var tipoDocumento = $('#wtipodoc').val();

	var imprimirExamen = $("#"+wchkimpexamen);

	if($(imprimirExamen).is(':checked')) {
           imprimirExamen = 'on';
        } else {
           imprimirExamen = 'off';
        }


	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{

				consultaAjaxKardex:       	'60',
				wemp_pmla:					wemp_pmla,
				whistoria:					historia,
				wingreso:					ingreso,
				wfechagrabacion:			wfechagrabacion,
				wbasedato:					wbasedato,
				wbasedatohce:				wbasedatohce,
				whce:						wbasedatohce,
				wcedula:					wcedula,
				tipoDocumento:				tipoDocumento,
				wusuario:					wusuario,
				imprimirExamen:				imprimirExamen,
				tipoDeOrden:				tipoDeOrden,
				numeroDeOrden:				numeroDeOrden,
				nroItem:					nroItem

			},
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{


				}
			}
		}
	);

}

//Marca como impreso un articulo en la pestaña de alta.
function imprimirArtAlta(historia,ingreso,codigoArticulo, tipoProtocolo, contArticulos){

	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	var wfechagrabacion = $('#wfechagrabacion').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var wcedula = $('#wcedula').val();
	var tipoDocumento = $('#wtipodoc').val();

	var imprimirArtAlta = $('#wchkimp_alta'+tipoProtocolo+contArticulos);

	if($(imprimirArtAlta).is(':checked')) {
           imprimirArtAlta = 'on';
        } else {
           imprimirArtAlta = 'off';
        }


	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{

				consultaAjaxKardex:       	'59',
				wemp_pmla:					wemp_pmla,
				whistoria:					historia,
				wingreso:					ingreso,
				wfechagrabacion:			wfechagrabacion,
				wbasedato:					wbasedato,
				wbasedatohce:				wbasedatohce,
				whce:						wbasedatohce,
				wcedula:					wcedula,
				tipoDocumento:				tipoDocumento,
				wusuario:					wusuario,
				codigoArticulo:				codigoArticulo,
				imprimirArtAlta:			imprimirArtAlta

			},
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{


				}
			}
		}
	);

}

//Marcar todos los procedimientos de alta
function marcar_todos_proc_alta(){

	$('table[id^=examPendientesImp]').find('input:checkbox[name^=wchkimpexamen]').each(function(){

		if($('#marcarTodosProcAlta').is(':checked')){

			$(this).attr('checked','checked');
			$(this).trigger('click');
			$(this).attr('checked','checked');

		}else{
				$(this).removeAttr('checked');
				$(this).trigger('click');
				$(this).removeAttr('checked');

				}

		 });


}

//Marcar todos los articulos de alta
function marcar_todos_art_alta(){

	$('table[id^=tbDetalleImp]').find('input:checkbox[name^=wchkimp_alta]').each(function(){

		if($('#marcarTodosArtAlta').is(':checked')){

			$(this).attr('checked','checked');
			$(this).trigger('click');
			$(this).attr('checked','checked');

		}else{
				$(this).removeAttr('checked');
				$(this).trigger('click');
				$(this).removeAttr('checked');

				}

		});


}

//Marcar todos los examenes
function marcar_todos() {

	// $('table[id^=examPendientes]').find('input:checkbox[name=imprimir_examen]').each(function(){
	$('table[id^=examPendientes]').find('input:checkbox[name^=imprimir_examen]').each(function(){

		if($('#marcar_all').is(':checked')){

			$(this).attr('checked','checked');
			$(this).trigger('click');
			$(this).attr('checked','checked');

		}else{
				$(this).removeAttr('checked');
				$(this).trigger('click');
				$(this).removeAttr('checked');

				}

		});

	validarImpresionProcAgrupados();
}


function ver_articulos_anteriores( cmp, tipoProtocolo){
	
	$("#lista_articulos_anteriores"+tipoProtocolo).toggle("slow");
	
	if( $("#lista_articulos_anteriores"+tipoProtocolo)[0].consultarAuditoria === undefined )
		$("#lista_articulos_anteriores"+tipoProtocolo)[0].consultarAuditoria = true;

	
	var consultarAuditoria = $("#lista_articulos_anteriores"+tipoProtocolo)[0].consultarAuditoria;
	
	if( consultarAuditoria ){
		
		try{
			
			// $("#lista_articulos_anteriores"+tipoProtocolo).block({message: 'Cargando datos, por favor espere...' });
			// $( "<div>Cargando datos, por favor espere...</div>" ).dialg({message: 'Cargando datos, por favor espere...' });
			$( "img", cmp ).css({display:''});
			
			$.post("ordenes.inc.php",
				{
					consultaAjax		: '',
					consultaAjaxKardex	: 'consultarArticulosAnterior',
					wbasedato			: $("#wbasedato").val(),
					wcenmez				: $( "#wcenmez" ).val(),
					wemp_pmla			: $("#wemp_pmla").val(),
					historia			: $("#whistoria").val(),
					ingreso				: $("#wingreso").val(),
					wfecha				: $( "#wfecha" ).val(),
					protocolo			: tipoProtocolo,
					// fechaKardex			: $('#wfechagrabacion').val(),
				}, 
				function(data){
					try{
						$( "img", cmp ).css({display:'none'});
						$("#lista_articulos_anteriores"+tipoProtocolo).unblock();
						// $( cmp ).unblock();
						
						$("#lista_articulos_anteriores"+tipoProtocolo).html(data);
						$("#lista_articulos_anteriores"+tipoProtocolo)[0].consultarAuditoria = false;
						
						$( '.msg_tooltip', $("#lista_articulos_anteriores"+tipoProtocolo) ).tooltip();
					}
					catch(e){
						console.log("error1")
						console.log(e)
					}
				},
				// "json"
			);
		}
		catch(e){
			console.log("error2")
			console.log(e)
			$("#lista_articulos_anteriores"+tipoProtocolo).unblock();
		}
	}
}

/*****************************************************************************************************************************
 * Agrega un nuevo registro al Maestro de Estudios y Ayudas Diagnóstcas por medio de AJAX
 ******************************************************************************************************************************/
function agregarNuevoExamenAlta()
{
	var nonmbreExamen = document.getElementById('wnomprocimp').value;
	var tipoServicio = document.getElementById('wselTipoServicioImp').options[ document.getElementById('wselTipoServicioImp').selectedIndex ].value;

	if(tipoServicio=='' || tipoServicio==' ' || tipoServicio=='%')
	{
		alert('Debe seleccionar un tipo de orden para poder agregar una nueva ayuda o procedimiento');
		return false;
	}

	if(nonmbreExamen=='' || nonmbreExamen==' ')
	{
		alert('El nombre de la ayuda o procedimiento no puede estar vacío');
		return false;
	}


	parametros = "consultaAjaxKardex=44&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+tipoServicio+"&descripcion="+nonmbreExamen+"&especialidad="+document.forms.forma.wespecialidad.value;

	//alert(parametros);

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var arrItem = $.trim( ajax.responseText );

				if(arrItem && arrItem!="")
				{
					var item = arrItem.split("|");

					seleccionarAyudaDiagnosticaAlta(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);

					document.getElementById('wnomprocimp').onfocus = function(){
						if( justificacionUltimoExamenAgregado ){
							justificacionUltimoExamenAgregado.focus();
							justificacionUltimoExamenAgregado = '';
						}
					};
			//    		this.focus();
			//    		this.select();

					document.getElementById("btnCerrarVentana").style.display = 'none';
					document.getElementById('wnomprocimp').value = '';
				}

			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

/********************************************************************************************************************************
 * Este objeto guardara las acciones de eliminar articulo, suspender articulo y elminar articulo
 * Esto para poder ejecutar dichas acciones al momento de presionar el boton grabar
 * Las acciones que se ejecutan son en realidad el ajax que se realizan al ejecutar las acciones correspondientes
 * El objeto estará formado de la siguiente forma:
 *
 * accionesOrdenes: [
 *		{	tipoProtocolo-Idx1:[
 *				param1: valor1,
 *				param2: valor2,
 *				..
 *				paramN: valorN
 *			]
 * 		},
 *		{	tipoProtocolo-Idx2:[
 *				param1: valor1,
 *				param2: valor2,
 *				..
 *				paramN: valorN
 *			]
 * 		},
 *		...
 *		{	tipoProtocolo-IdxN:[
 *				param1: valor1,
 *				param2: valor2,
 *				..
 *				paramN: valorN
 *			]
 * 		}
 * ]
 *
 * Las acciones eliminaran serán
 * 	0: Suspender articulo
 * 	1: Eliminar Articulo
 * 	2: Eliminar examen o procedimiento
 *
 * Cada accion tiene su propia variable global constante para mayor claridad y son en su respectivo orden:
 * SUSPENDER_ARTICULO
 * ELIMINAR_ARTICULO
 * ELIMINAR_EXAMEN
 ********************************************************************************************************************************/
var SUSPENDER_ARTICULO = 0;
var ELIMINAR_ARTICULO = 1;
var ELIMINAR_EXAMEN = 2;
var ELIMINAR_ORDEN_EXAMEN = 3;
var ELIMINAR_ESQUEMA_DEXTROMETER = 4;

var accionesOrdenes = new Array();

/************************************************************************************************************************
 * Esta función ejecuta las acciones de Suspender Articulo, eliminar Articulo o eliminar procedimiento según el caso
 ************************************************************************************************************************/
function ejecutarAccionesOrdenes(){

	//Recorro todo el objeto
	for( var idxAccion in accionesOrdenes ){

		//Miro que accion se debe ejecutar
		switch( idxAccion*1 ){

			case SUSPENDER_ARTICULO:

				for( var idxElement in accionesOrdenes[ idxAccion ] ){
					suspenderArticuloElemento( accionesOrdenes[ idxAccion ][idxElement][0], accionesOrdenes[ idxAccion ][idxElement][1] );
				}

				break;

			case ELIMINAR_ARTICULO:

				for( var idxElement in accionesOrdenes[ idxAccion ] ){
					var aux = accionesOrdenes[ idxAccion ][idxElement];
					eliminarArticuloElemento( aux[0], aux[1], aux[2], aux[3], aux[4], aux[5], aux[6], aux[7], aux[8], aux[9], aux[10], aux[11] );
				}

				break;

			case ELIMINAR_EXAMEN:

				for( var idxElement in accionesOrdenes[ idxAccion ] ){
					var aux = accionesOrdenes[ idxAccion ][idxElement];
					eliminarExamenElemento( aux[0], aux[1], aux[2], aux[3], aux[4], aux[5], aux[6], aux[7], aux[8], aux[9], aux[10] );

					//////////////////////////////////////////////////////////
					// Elimino los datos del formulario si es una orden asociada a un formulario de HCE
					parametros = "consultaAjaxKardex=51&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&wcco="+aux[11]+"&historia="+aux[0]+"&ingreso="+aux[1]+"&firmHce="+aux[12];

					try{
						ajaxhce=nuevoAjax();

						ajaxhce.open("POST", "ordenes.inc.php",false);
						ajaxhce.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajaxhce.send(parametros);
						
					}
					catch(e){	}
					/////////////////////////////////////////////////////////
				}

				break;

			case ELIMINAR_ORDEN_EXAMEN:

				for( var idxElement in accionesOrdenes[ idxAccion ] ){
					var aux = accionesOrdenes[ idxAccion ][idxElement];
					cancelarOrdenElemento( aux[0], aux[1], aux[2], aux[3], aux[4], aux[5], aux[6] );
				}

				break;

			case ELIMINAR_ESQUEMA_DEXTROMETER:
				var aux = accionesOrdenes[ ELIMINAR_ESQUEMA_DEXTROMETER ][0];
				eliminarEsquemaDextrometerElemento( aux[0],aux[1],aux[2],aux[3],aux[4],aux[5] );
			break;

			default: break;
		}
	}

	accionesOrdenes = new Array();
}


/*****************************************************************************************************************************
 * Llamada ajax para suspender o activar un articulo del detalle de medicamentos
 ******************************************************************************************************************************/
function suspenderArticuloElemento(idxElemento,tipoProtocolo){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfechagrabacion.value;
	var usuario = document.forms.forma.usuario.value;
	var fila = document.getElementById('trFil'+idxElemento);
	var fila = $( "#wnmmed"+tipoProtocolo+idxElemento ).parent().parent()[0];
	var idOriginal = $( "#widoriginal"+tipoProtocolo+idxElemento ).val();
	var estaSuspendido = 'on';

	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);

	//Extraccion de la fecha y hora inicial de administracion
	var fh = fechaInicio.value.split("a las:");
	fechaInicio = fh[0];
	horaInicio = fh[1];

	//Extraccion del codigo articulo propiamente dicho
	var cd = "";
	if(codigoArticulo.tagName == 'INPUT'){
		cd = codigoArticulo.value.split("-");
	} else {
		cd = codigoArticulo.innerHTML.split("-");
	}

	//Alterno la suspension del medicamento
	if(fila.className == 'suspendido'){
		estaSuspendido = 'on';
	} else {
		estaSuspendido = 'off';
	}

	codigoArticulo = cd[0];

	//Llamada AJAX
	var parametros = "";
	var mensaje = "";

	parametros = "consultaAjaxKardex=16&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&codigoArticulo="+codigoArticulo+"&codUsuario="+usuario+"&fecha="+fecha+"&estado="+estaSuspendido+"&fechaInicio="+fechaInicio+"&horaInicio="+horaInicio
		+"&tipoProtocolo="+tipoProtocolo+"&idOriginal="+idOriginal;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		if($.trim( ajax.responseText ) == '1'){
			if(estaSuspendido == 'on'){
				// fila.className = 'suspendido';
				$( "#wmodificado" +tipoProtocolo+idxElemento ).val( "N" );
				mensaje = "El medicamento se ha suspendido.";
			} else {
				// fila.className = '';
				mensaje = "El medicamento se ha activado.";
			}
		}
		else {
			mensaje = "No se pudo modificar estado suspension: " + $.trim( ajax.responseText );
			alert( mensaje );
		}
	}
	catch(e){	}
}

//Esta funcion permite que el usuario cierre la ventana de ordenes y no se grabe nada de lo que haya
//quedado en los formularios.
function salir_sin_grabar(){


	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	var historia = $('#whistoria').val();
	var ingreso = $('#wingreso').val();
	var wfechagrabacion = $('#wfechagrabacion').val();
	var wusuario = $('#usuario').val();
	var wbasedato = $('#wbasedato').val();
	var wbasedatohce = $('#wbasedatohce').val();
	var wcedula = $('#wcedula').val();
	var tipoDocumento = $('#wtipodoc').val();

	$.ajax({
			url: "ordenes.inc.php",
			type: "POST",
			data:{
				consultaAjaxKardex:       	'57',
				wemp_pmla:					wemp_pmla,
				whistoria:					historia,
				wingreso:					ingreso,
				wfechagrabacion:			wfechagrabacion,
				wbasedato:					wbasedato,
				wbasedatohce:				wbasedatohce,
				whce:						wbasedatohce,
				wcedula:					wcedula,
				tipoDocumento:				tipoDocumento,
				wusuario:					wusuario,
				weditable:					$( "#weditable" ).val(),
				pestanasVistas:				$( "#pestanasVistas" ).val()

			},
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{

					cerrarModalHCE();
				}
			}
		}
	);

	if( $( "#programa" ).length > 0 ){
		window.close();
	}

	return "texto";
}



function validarFirmaDigitalVaci(){
	if( $( "#pswFirma" ).val() == '' ){
		alert( "Debe firmar la orden" );
	}
}

/**
 * Ajax que graba un examen de la tabla temporal a la tabla de detalle
 */

function grabarExamenADetalle()
{

		var wemp_pmla = document.forms.forma.wemp_pmla.value;
		// var tipoOrden = $('#hexcco'+contExamen).val();
		// var nroOrden = $('#hexcons'+contExamen).val();
		// var numeroItem = $('#hexnroitem'+contExamen).val();

		$.ajax({
				url: "ordenes.inc.php",
				type: "POST",
				data:{
					consultaAjaxKardex:       	'55',
					wemp_pmla:					wemp_pmla,
					his:						$('#whistoria').val(),
					ing:						$('#wingreso').val()
				},
				async: false,
				success:function(data_json) {

					if (data_json.error == 1)
					{
						alert(data_json.mensaje);
						return;
					}
					else{}
				}
			}
		);
}


function cerrarFormHCE(contExamen,basedatohce,formTipoOrden,historia,ingreso,alta,hce)
{
	//alert(cuentaExamenes+','+basedatohce+','+formTipoOrden+','+historia+','+ingreso)
	var prefijoalta = "";

	if(alta == 'alta'){
		prefijoalta = "Imp";
	}
	// Consulto si el formulario ha sido diligenciado en la historia clínica electrónica
	parametros = "consultaAjaxKardex=50&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatoshce="+basedatohce+"&formTipoOrden="+formTipoOrden+"&historia="+historia+"&ingreso="+ingreso;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200){
				var formDiligenciado = $.trim( ajax.responseText );
				// totalExamenesAntesGrabar = formDiligenciado;
				totalExamenesDespuesGrabar = formDiligenciado;
				// if(formDiligenciado!='ok')
				if( totalExamenesDespuesGrabar == totalExamenesAntesGrabar )
				{
					if(confirm("Si sale de este formulario sin grabar, el examen será eliminado de la orden?"))
					{
						$.unblockUI();
						quitarExamen(contExamen,prefijoalta,'on','hce',hce);
					}
				}
				else
				{
					$.unblockUI();
					$( "#hiFormHce" + contExamen ).val( totalExamenesDespuesGrabar );

					var aux1 = $( "#hiReqJus"+contExamen ).val();
					$( "#hiReqJus"+contExamen ).val( 'off' );
					grabarExamen( contExamen, false );
					$( "#hiReqJus"+contExamen ).val( aux1 );
				}

				var select = $('#wselTipoServicio'+prefijoalta);
				select.val($('option:first', select).val());
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}


}


function traeJustificacionHCE(campoChk,campoDestino){

	var campoJustificacion = document.getElementById(campoDestino);

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;

	if(campoChk.checked == false)
	{
		if(confirm('Desea borrar el texto de la justificación?'))
		{
			campoJustificacion.value = '';
		}
		else
		{
			campoChk.checked = true;
		}
		return false;
	}

	var parametros = "consultaAjaxKardex=46&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatoshce=hce"+"&whistoria="+historia+"&wingreso="+ingreso;

	try{
		//$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				if($.trim( ajax.responseText )!="")
				{
					if(campoChk.checked)
					{
						if(campoJustificacion.value=='' || campoJustificacion.value==' ')
							campoJustificacion.value = $.trim( ajax.responseText );
						else
							campoChk.checked = false;
					}
					else
					{
						campoJustificacion.value = '';
					}
				}
				else
				{
					// jAlert('No se encontró resumen de historía clínica para el paciente','ALERTA');
					jAlert('No se encontr\u00F3 resumen de histor\u00EDa cl\u00EDnica para el paciente','ALERTA');
					campoChk.checked = false;
				}
			}
			//$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}


function traerDiagnosticoHCE(campoChk,campoDestino){

	var campoDiagnostico = document.getElementById(campoDestino);

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;

	if(campoChk.checked == false)
	{
		if(confirm('Desea borrar el texto del diagnostico?'))
		{
			campoDiagnostico.value = '';
		}
		else
		{
			campoChk.checked = true;
		}
		return false;
	}

	var parametros = "consultaAjaxKardex=61&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&whce="+document.forms.forma.wbasedatohce.value+"&whis="+historia+"&wing="+ingreso;

	try{
		//$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				if($.trim( ajax.responseText )!="")
				{
					if(campoChk.checked)
					{
						if(campoDiagnostico.value=='' || campoDiagnostico.value==' ')
							campoDiagnostico.value = $.trim( ajax.responseText );
						else
							campoChk.checked = false;
					}
					else
					{
						campoDiagnostico.value = '';
					}
				}
				else
				{
					// jAlert('No se encontró diagnostico de historía clínica para el paciente','ALERTA');
					jAlert('No se encontr\u00F3 diagnostico de histor\u00EDa cl\u00EDnica para el paciente','ALERTA');
					campoChk.checked = false;
				}
			}
			//$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}


/*********************************************************************************
 * Encuentra la posicion en Y de un elemento
 *********************************************************************************/
function findPosY(obj)
{
	var curtop = 0;
	if(obj.offsetParent)
    	while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
}


/*********************************************************************************
 * Encuentra la posicion en X de un elemento
 *********************************************************************************/
function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1)
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }


function ocultar(){
	var divTitle = document.getElementById( "dvTitle" );
	divTitle.style.display = 'none';
}

function mostrar( campo ){

	try{
		clearInterval( interval );
	}
	catch(e){}

	var divTitle = document.getElementById( "dvTitle" );

	divTitle.innerHTML = campo.title;

	divTitle.style.display = '';
	divTitle.style.position = 'absolute';
	divTitle.style.top = parseInt( findPosY(campo) )- parseInt( campo.offsetHeight );
	divTitle.style.left = findPosX( campo );
	divTitle.style.background = "#FFFFDF";
	divTitle.style.borderStyle = "solid";
	divTitle.style.borderWidth = "1px";

	interval = setTimeout( "ocultar()", 3000 );
}



// function ocultar(){
	// var divTitle = document.getElementById( "dvTitle" );
	// divTitle.style.display = 'none';
// }

function mostrarDialogDiv( campo ){

	try{
		clearInterval( interval );
	}
	catch(e){}

	var divTitle = document.getElementById( "dialog" );

	//divTitle.innerHTML = campo.title;

	divTitle.style.display = '';
	divTitle.style.position = 'absolute';
	divTitle.style.top = parseInt( findPosY(campo) )+ parseInt( campo.offsetHeight );
	divTitle.style.left = findPosX( campo );
	// divTitle.style.background = "#FFFFDF";
	// divTitle.style.borderStyle = "solid";
	// divTitle.style.borderWidth = "1px";

	//interval = setTimeout( "ocultar()", 3000 );

	divTitle.parentNode.style.height = divTitle.offsetHeight+5;
}

// Cuando es adición múltiple de medicamentos esta función se encarga de abrir los formatos CTC
// de los medicamentos NO POS que puedan haber en esta adición múltiple
// Enero 30 de 2012
function abrirCTCMultiple()
{
	var filasNoPos = strPendientesCTC.split( "\r\n" );

	if(filasNoPos.length-1 > 0)
	{
		var arrNoPos = filasNoPos[0].split( "|" );
		var cadenaReemplazar = filasNoPos[0]+"\r\n";

		strPendientesCTC = strPendientesCTC.replace(cadenaReemplazar,"")
		if(arrNoPos[0]=='procedimiento')
			mostrarCtcProcedimientos(  arrNoPos[1],  arrNoPos[2] );
		else
		{
			mostrarCtcArticulos2( arrNoPos[1], arrNoPos[2], arrNoPos[3] );
		}
	}
}

// Cuando es adición por Horario Especial esta función se encarga de abrir la modal de artículos
// tantas veces como medicamentos que requieren insumos se detecten
// Abril 9 de 2013
function abrirModalMultiple()
{
	var filasModal = strPendientesModal.split( "\r\n" );

	if(filasModal.length-1 > 0)
	{
		var arrModal = filasModal[0].split( "|" );
		var cadenaReemplazar = filasModal[0]+"\r\n";

		strPendientesModal = strPendientesModal.replace(cadenaReemplazar,"")
		if(arrModal[0]!='')
		{
			// 					  codigo,    nomComercial,   nomGenerico, tipoMedLiq,    esGenerico,   idx,       componentesTipo);
			abrirModalArticulos( arrModal[1],  arrModal[2],  arrModal[3],  arrModal[4],  arrModal[5],  arrModal[6],  arrModal[7],  arrModal[8] );
		}
	}
}


/*****************************************************************************************************
 * Marzo 12 de 2013
 *
 * Busco los cambios que hallan del CTC y en caso de algún cambio se muestra nuevamente la información
 * del CTC para que el medico visualice los cambios
 *****************************************************************************************************/
function buscarCambiosCTC( dvCodArt ){

	//$( "[name=tiempoTratamientoNoPos]", divAux )[0].objArts;
	var val = false;

	if( document.getElementById( "hiArtsNoPos" ) ){

		//Este campo tiene el codigo de cada medicamento No Pos separados por -
		var artsNoPos = document.getElementById( "hiArtsNoPos" ).value;

		var artsNoPos = artsNoPos.substr( 1 );

		var arts = artsNoPos.split( "," );

		for( var i = 0; i < 1; i++ ){

			//var contenedorInfoNoPos = document.getElementById( "dv" + codArt + "-" +  protocolo + idx + "Mostrar" );

			//Busco el div que contiene toda la información
			var dvs = document.getElementById( "ctcArticulos" ).getElementsByTagName( "div" );

			for( var x = 0; x < dvs.length; x++ ){
				if( dvs[x].id.substr( 0, dvs[x].id.indexOf( "-" ) ).toLowerCase() == "dv"+dvCodArt.toLowerCase() ){
					var contenedorInfoNoPos = dvs[x];
					break;
				}
			}

			//Aquí debo buscar el campo que contiene los objetos para compara cambios
			if( contenedorInfoNoPos ){

				var objs = $( "[name=tiempoTratamientoNoPos]", contenedorInfoNoPos )[0].objArts;

				for( var x in objs.idxs ){

					var idAux = objs.idxs[ x ];

					if( document.getElementById("wperiod"+idAux) ){

						var inCodArtsCTC = objs;

						if( inCodArtsCTC.inf[ x ].frecuencia != document.getElementById("wperiod"+idAux).value ){
							inCodArtsCTC.inf[ x ].frecuencia = document.getElementById("wperiod"+idAux).value;
							val = true;
						}

						if( inCodArtsCTC.inf[ x ].dosis != document.getElementById("wdosis"+idAux).value ){
							inCodArtsCTC.inf[ x ].dosis = document.getElementById("wdosis"+idAux).value
							val = true;
						}

						if( inCodArtsCTC.inf[ x ].dosisMaxima != document.getElementById("wdosmax"+idAux).value ){
							inCodArtsCTC.inf[ x ].dosisMaxima = document.getElementById("wdosmax"+idAux).value
							val = true;
						}

						if( inCodArtsCTC.inf[ x ].diasTto != document.getElementById("wdiastto"+idAux).value ){
							inCodArtsCTC.inf[ x ].diasTto = document.getElementById("wdiastto"+idAux).value
							val = true;
						}

						//inCodArtsCTC.inf[ x ].canManejo = document.getElementById("whcmanejo"+idAux).value;
						// var origen = document.getElementById( "wnmmed"+idAux ).value.split( "-" )[1];
						// inCodArtsCTC.inf[ x ].origen = origen;

						var fechaHoraInicio = document.getElementById( 'wfinicio'+idAux ).value.split( " a las:" );
						inCodArtsCTC.inf[ x ].fin = fechaHoraInicio[0];		//Fecha de inicio del medicamento
						inCodArtsCTC.inf[ x ].hin = fechaHoraInicio[1];		//Hora de inicio del medicamento
					}
					else{
						val = true;
					}
				}

				//Busco diferencias por cantidad de articulos
				//Busco todos los articulos que tengan el mismo codigo
				//Para ello busco todo los input

				var tbsContenedores = new Array( "tbDetalleAddN", "tbDetalleAddImpN" );

				var canArticulos = 0;

				for( var x = 0; x < tbsContenedores.length; x++ ){

					var tbContenedor = $( "#"+tbsContenedores[x] )[0];	//Busco la tabla que contiene los medicamentos

					/****************************************************************************************************
					 * Busco todos los input de la tabla contenedor
					 * y los guardo en un array
					 ****************************************************************************************************/
					if( tbContenedor ){
						var inCodigos = tbContenedor.getElementsByTagName( "input" );

						for( var i = 0; i < inCodigos.length; i++ ){

							//Si comineza el id con wnmmed, significa que es un campo con codigo del articulo
							if( inCodigos[i].id.substr( 0, 6 ) == "wnmmed" ){

								//Solo busco aquellos que tengan el mismo codigo
								//El codigo esta separado por un guion (-)
								if( inCodigos[i].value.substr( 0, inCodigos[i].value.indexOf( "-" ) ) == dvCodArt ){
									canArticulos++;
								}
							}
						}
					}
				}

				//Si canArticulos es 0, significa que NO hay articulos a grabar
				if( canArticulos > 0 ){
					if( canArticulos != objs.idxs.length ){
						val = true;
					}
				}
				else{
					val = false;
				}
				/****************************************************************************************************/
			}
		}
	}

	return val;
}

//Noviembre 14 de 2012
//Copia de mostrarCtcArticulos
function mostrarCtcArticulos2( codArticulo, protocolo, id, deAlta  ){
	var idx = protocolo+id;

	if(deAlta && deAlta=="on")
		var txtImp = "Imp";
	else
		var txtImp = "";

	//Se verfica si tiene horario especial, si tiene horario especial solo se muestra
	//el ctc si es el último medicamento
	var hayMasHE = 0;

	for( var i = 2; i <= 24; i += 2 ){

		if( document.getElementById( 'dosisRonda' + i ) && document.getElementById( 'dosisRonda' + i ).value != '' ){

			hayMasHE++;
		}
	}

	//Si es uno significa que es el ultimo y si es 0 significa que no tenía horario especial
	if( hayMasHE == 1 || hayMasHE == 0 ){

		//Creo un objeto con todas las propiedades necesarias
		var inCodArtsCTC = { art: '',
							 inf: [],
							 idxs:[]
						   };

		var tbsContenedores = new Array( "tbDetalleAddN", "tbDetalleAddImpN" );

		for( var x = 0; x < tbsContenedores.length; x++ ){

			//Buscar todos los medicamentos que tengan el mismo código
			var tbContenedor = $( "#"+tbsContenedores[x] )[0];	//Busco la tabla que contiene los medicamentos

			if( tbContenedor ){		//Abril 15 de 2014
				/****************************************************************************************************
				 * Busco todos los input de la tabla contenedor
				 * y los guardo en un array
				 ****************************************************************************************************/
				var inCodigos = tbContenedor.getElementsByTagName( "input" );

				for( var i = 0; i < inCodigos.length; i++ ){

					//Si comineza el id con wnmmed, significa que es un campo con codigo del articulo
					if( inCodigos[i].id.substr( 0, 6 ) == "wnmmed" ){

						//Solo busco aquellos que tengan el mismo codigo
						//El codigo esta separado por un guion (-)
						if( inCodigos[i].value.substr( 0, inCodigos[i].value.indexOf( "-" ) ) == codArticulo ){

							//Guardo el campo en un array
							inCodArtsCTC.art = codArticulo;		//Codigo del articulo

							inCodArtsCTC.inf[ inCodArtsCTC.inf.length ] = {};

							var idAux = inCodigos[i].id.substr( 6 );

							inCodArtsCTC.idxs[ inCodArtsCTC.idxs.length ] = idAux;

							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].frecuencia = document.getElementById("wperiod"+idAux).value;;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].dosis = document.getElementById("wdosis"+idAux).value;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].canManejo = document.getElementById("whcmanejo"+idAux).value;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].dosisMaxima = document.getElementById("wdosmax"+idAux).value;
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].diasTto = document.getElementById("wdiastto"+idAux).value;

							var origen = document.getElementById( "wnmmed"+idAux ).value.split( "-" )[1];
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].origen = origen;

							var fechaHoraInicio = document.getElementById( 'wfinicio'+idAux ).value.split( " a las:" );

							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].fin = fechaHoraInicio[0];		//FEcha de inicio del medicamento
							inCodArtsCTC.inf[ inCodArtsCTC.inf.length-1 ].hin = fechaHoraInicio[1];		//Hora de inicio del medicamento

						}
					}
				}
				/****************************************************************************************************/
			}
		}

		/****************************************************************************************************
		 * Verifico que no exista un CTC para el articulo
		 * Si ya tiene un CTC debo abrir el formulario encontrado y modificar los campos que sean necesarios
		 ****************************************************************************************************/
		divEncontrado = false;

		var dvCtcAux = document.getElementById('ctcArticulos');

		if( dvCtcAux ){

			var dvs = dvCtcAux.getElementsByTagName( "div" );

			for( var i = 0; i < dvs.length; i++ ){
				if( dvs[i].id.substr( 0, dvs[i].id.indexOf( "-" ) ) == "dv"+codArticulo ){
					divEncontrado = dvs[i];
				}
			}
		}
		/****************************************************************************************************/


		//Ahora creo una url con los parametros encontrados
		//Para ello recorro todo el objeto que requiero
		var parametros = "";

		if( inCodArtsCTC.inf.length > 0 ){

			for( var i = 0; i < inCodArtsCTC.inf.length; i++ ){

				for( var x in inCodArtsCTC.inf[i] ){
					parametros += "&" + x + "[" + i + "]=" + inCodArtsCTC.inf[ i ][ x ];
				}
			}
		}

		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;
		var fecha = document.forms.forma.wfecha.value;
		var wemp_pmla = document.forms.forma.wemp_pmla.value;
		var dosis_medico_aux = $("#dosis_medico_aux").val();


		parametros = "wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fecha+"&codArticulo="+codArticulo+"&idx="+idx + parametros +"&protocolo="+protocolo+"&id="+id+"&dosis_medico_aux="+dosis_medico_aux;

		try{

			ajax=nuevoAjax();

			ajax.open("POST", "./generarCTCArticulos.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if( $.trim( ajax.responseText ) != '' ){

				if( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) ){
					document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ).parentNode.removeChild( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) );
				}

				//Creo el div que contendrá todos los ctc de procedimientos
				if( !document.getElementById('ctcArticulos') ){
					var divAux = document.createElement( "div" );

					divAux.innerHTML = "<div id='ctcArticulos' style='display:none'>"
									 + "<INPUT TYPE='hidden' name='hiArtsNoPos' id='hiArtsNoPos' value=''>"
									 + "</div>";

					document.forms[0].appendChild(divAux.firstChild);
				}

				document.getElementById('hiArtsNoPos').value += ',' + codArticulo + '-' +idx;

				if( !document.getElementById('ctcArtsTemp') ){

					var divAux = document.createElement( "div" );

					divAux.style.display = 'none';
					divAux.style.width = '80%';
					divAux.id = 'ctcArtsTemp';
				}
				else{
					var divAux = document.getElementById('ctcArtsTemp');
				}

				divAux.innerHTML = $.trim( ajax.responseText );

				//Si ya había un CTC igualo todos los campos al Ctc encontrado
				if( divEncontrado ){

					//Campos que necesito igualar
					var camposBuscar = new Array( "select", "input", "textarea" );

					//Recorro el array de los campos segun las etiquetas
					for( var i = 0; i < camposBuscar.length; i++ ){

						//Busco todas las etiquetas segun el array
						var varCmps = divAux.getElementsByTagName( camposBuscar[i] );

						//Recorro cada uno de los campos encontrados
						for( var j = 0; j < varCmps.length; j++ ){

							//Si el campo tiene un name
							//Significa que lo debo igualar al CTC que ya había encontrado
							//A excepción de posología y dosis por día
							if( varCmps[j].name != '' && varCmps[j].name != "posologiaNoPos" && varCmps[j].name != "ddNoPos" ){

								if( camposBuscar[i] != 'input'
									|| camposBuscar[i] == 'input'
									&& ( varCmps[j].type.toLowerCase() != "radio" && varCmps[j].type.toLowerCase() != "checkbox" )
								){
									//Busco el campo con el  mismo nombre en el CTC Anterior e igualos sus valores
									varCmps[j].value = $( "[name="+varCmps[j].name+"]", divEncontrado ).val();
								}
								else{

									switch( varCmps[j].type.toLowerCase() ){

										case 'radio':

											$( "[name="+varCmps[j].name+"]", divEncontrado ).each(
												function(k){
													if( varCmps[j].value == $(this).val() ){
														varCmps[j].checked = $(this).is(':checked');
													}
												}
											);

											// if( varCmps[j].checked && varCmps[j].value == $( "[name="+varCmps[j].name+"]", divEncontrado ).val() ){
												// varCmps[j].checked = true;
											// }
										break;

										case 'checkbox':
											varCmps[j].checked = $( "[name="+varCmps[j].name+"]", divEncontrado )[0].checked;
										break;
									}
								}
							}
						}
					}

					//Despues de replicar los datos, borra el div anterior
					try{
						divEncontrado.parentNode.removeChild(divEncontrado);
					}
					catch(e){}
				}



				//Busco el campo Tiempo de tratamiento para agregar el objeto con el que se calcula la cantidad
				$( "[name=tiempoTratamientoNoPos]", divAux )[0].objArts = inCodArtsCTC;
				$( "input[value='Salir sin guardar']", divAux )[0].objArts = inCodArtsCTC;

				document.forms[0].appendChild(divAux);

				//agrego el medicamento que tiene CTC a la variable global
				arCTCArticulos[ codArticulo ] = inCodArtsCTC.idxs.length;	//lo igualo con el total de articulos a grabar

				//Si ya había un CTC igualo todos los campos al Ctc encontrado
				if( divEncontrado ){

					//Recalculo la cantidad
					$( "[name=tiempoTratamientoNoPos]", divAux )[0].onchange();
				}

				$.blockUI({ message: $('#ctcArtsTemp'),
							css: {
								top:  '5%',
								left: '10%',
								width: '80%',
								height: '90%',
								overflow: 'auto',
								cursor: 'auto'
							}
				});
			}

		}
		catch(e){
			//alert( "Error: " + e );
			$.unblockUI();
		}
	}
}

//Noviembre 14 de 2012
function mostrarCtcArticulos( codArticulo, protocolo, id  ){
	var idx = protocolo+id;

	var parametros = "";

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;

	//Busco datos adicionales
	var frecuencia = document.getElementById("wperiod"+idx).value;
	var dosis = document.getElementById("wdosis"+idx).value;
	var canManejo = document.getElementById("whcmanejo"+idx).value;
	var dosisMaxima = document.getElementById("wdosmax"+idx).value;
	var diasTto = document.getElementById("wdiastto"+idx).value;
	var med = document.getElementById( "wnmmed"+idx ).value;
    med = med.split( "-" );

	var fechaInicio = document.getElementById( 'wfinicio'+idx ).value.split( " a las:" );

	var fin = fechaInicio[0];				//fecha de inicio
	var hin = fechaInicio[1]; 				//hora de inicio
	var wemp_pmla = document.forms.forma.wemp_pmla.value;

	// generarCTCprocedimientos.php?wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fechaKardex
	parametros = "wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fecha+"&codArticulo="+codArticulo+"&idx="+idx
	             +"&frecuencia="+frecuencia
				 +"&dosis="+dosis
				 +"&canManejo="+canManejo
				 +"&dosisMaxima="+dosisMaxima
				 +"&diasTto="+diasTto
				 +"&protocolo="+protocolo
				 +"&id="+id
				 +"&origen="+med[1]        //Este es el origen del medicamento (SF O CM)
				 +"&fin="+fin
				 +"&hin="+hin;

	try{

		ajax=nuevoAjax();

		ajax.open("POST", "./generarCTCArticulos.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if( $.trim( ajax.responseText ) != '' ){

			if( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) ){
				document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ).parentNode.removeChild( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) );
			}

			//Creo el div que contendrá todos los ctc de procedimientos
			if( !document.getElementById('ctcArticulos') ){
				var divAux = document.createElement( "div" );

				divAux.innerHTML = "<div id='ctcArticulos' style='display:none'>"
				                 + "<INPUT TYPE='hidden' name='hiArtsNoPos' id='hiArtsNoPos' value=''>"
								 + "</div>";

				document.forms[0].appendChild(divAux.firstChild);
			}

			document.getElementById('hiArtsNoPos').value += ',' + codArticulo + '-' +idx;

			if( !document.getElementById('ctcArtsTemp') ){

				var divAux = document.createElement( "div" );

				divAux.style.display = 'none';
				divAux.style.width = '80%';
				divAux.id = 'ctcArtsTemp';
			}
			else{
				var divAux = document.getElementById('ctcArtsTemp');
			}

			divAux.innerHTML = $.trim( ajax.responseText );

			document.forms[0].appendChild(divAux);

			$.blockUI({ message: $('#ctcArtsTemp'),
						css: {
							top:  '5%',
							left: '10%',
							width: '80%',
							height: '90%',
							overflow: 'auto',
							cursor: 'auto'
						}
			});
		}

	}
	catch(e){
		//alert( "Error: " + e );
		$.unblockUI();
	}
}


//Noviembre 08 de 2012
function mostrarCtcProcedimientos( codExamen, cuentaExamenes ){
	var parametros = "";

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var wemp_pmla = document.forms.forma.wemp_pmla.value;

	// generarCTCprocedimientos.php?wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fechaKardex
	parametros = "wemp_pmla="+wemp_pmla+"&historia="+historia+"&ingreso="+ingreso+"&fechaKardex="+fecha+"&codExamen="+codExamen+"&idExamen="+cuentaExamenes;

	try{

		ajax=nuevoAjax();

		ajax.open("POST", "generarCTCProcedimientos.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if( $.trim( ajax.responseText ) != '' ){

			//Creo el div que contendrá todos los ctc de procedimientos
			if( !document.getElementById('ctcProcedimientos') ){
				var divAux = document.createElement( "div" );

				divAux.innerHTML = "<div id='ctcProcedimientos' style='display:none'>"
				                 + "<INPUT TYPE='hidden' name='hiProcsNoPos' id='hiProcsNoPos' value=''>"
								 + "</div>";

				document.forms[0].appendChild(divAux.firstChild);
			}

			document.getElementById('hiProcsNoPos').value += ',' + codExamen + '-' +cuentaExamenes;

			if( !document.getElementById('ctcProcTemp') ){

				var divAux = document.createElement( "div" );

				divAux.style.display = 'none';
				divAux.style.width = '80%';
				divAux.id = 'ctcProcTemp';
			}
			else{
				var divAux = document.getElementById('ctcProcTemp');
			}

			divAux.innerHTML = $.trim( ajax.responseText );

			document.forms[0].appendChild(divAux);

			$.blockUI({ message: $('#ctcProcTemp'),
						css: {
							top:  '5%',
							left: '10%',
							width: '80%',
							height: '90%',
							overflow: 'auto',
							cursor: 'auto'
						}
			});

			// $( "input[name=fechaProcedimientoPrevio2]", divAux ).datepicker({
					// dateFormat: "yy-mm-dd",
					// showOn: "button",
					// buttonText: "..."
				// });

			// $( "input[name=fechaProcedimientoPrevio1]", divAux ).datepicker({
					// dateFormat: "yy-mm-dd",
					// showOn: "button",
					// buttonText: "..."
				// });


			// try{
				// document.getElementById( "ui-datepicker-div" ).style.zIndex = "10000000000";
			// }
			// catch(e){
				// alert( "Error: " + e );
			// }
		}

	}
	catch(e){
		//alert( "Error: " + e );
		$.unblockUI();
	}

}

/**********************************************************************
 * Junio 27 de 2012
 *
 * Determina si una variable ha sido declarada
 * Cumple la misma función que isset en PHP
 **********************************************************************/
function isset(variable_name)
{
	try {
		 if (typeof(eval(variable_name)) != 'undefined')
		 if (eval(variable_name) != null)
		 return true;
	 } catch(e) { }
	return false;
}


/**********************************************************************
 * Octubre 17 de 2012
 *
 * Quita espacios en blanco al principio y final de una cadena
 * Cumple la misma función que trim en PHP
 **********************************************************************/
function trim (myString)
{
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
}


/**********************************************************************
 * marzo 1 de 2013
 *
 * Abre la ventana especificada
 **********************************************************************/
function abrir_ventana ( pagina, wemp_pmla, historia, ingreso, tipoimp ) {
	var opciones="toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=670, height=470, top=20, left=40";
	pagina += "?wemp_pmla="+wemp_pmla+"&whistoria="+historia+"&wingreso="+ingreso+"&tipoimp="+tipoimp;
	window.open(pagina,"",opciones);
}


/**********************************************************************
 * Junio 19 de 2012
 *
 * Carga articulos del kardex del día anterior no suspendidos
 **********************************************************************/
function cargarMedicamentosAnteriores(historia,ingreso,fecha,cco){

	var parametros = "";

	parametros = "consultaAjaxKardex=32&wemp_pmla="+document.forms.forma.wemp_pmla.value+"wbasedato="+document.forms.forma.wbasedato.value+"&wcenmez=cenpro&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
				+"&cco="+cco

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "./ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200){

				if($.trim( ajax.responseText ) != ""){

					var listaArticulos = $.trim( ajax.responseText ).split( "@" );

					for( var i = 0; i < listaArticulos.length; i++ ){



						//Busco cada uno de los datos de los medicamentos
						var datosArticulo = listaArticulos[i].split( "','" );

						var codigo = datosArticulo[ 0 ];
						var nombre = datosArticulo[ 1 ];
						var origen = datosArticulo[ 2 ];
						var grupo = datosArticulo[ 3 ];
						var forma = datosArticulo[ 4 ];
						var unidad = datosArticulo[ 5 ];
						var pos = datosArticulo[ 6 ];
						var unidadFraccion  = datosArticulo[ 7 ];
						var cantFracciones  = datosArticulo[ 8 ];
						var vencimiento  = datosArticulo[ 9 ];
						var diasVencimiento = datosArticulo[ 10 ];
						var dispensable = datosArticulo[ 11 ];
						var duplicable = datosArticulo[ 12 ];
						var diasMaximos = datosArticulo[ 13 ];
						var dosisMaximas = datosArticulo[ 14 ];
						var via	= datosArticulo[ 15 ];
						var tipoProtocolo = datosArticulo[ 16 ];
						var noEnviar = datosArticulo[ 17 ];

						//Creo la fila para agregar el articulo
						agregarArticulo( "detKardexAdd" + tipoProtocolo);

						//Agrego la fila con los datos correspondientes
						seleccionarMedicamento(codigo, nombre, origen, grupo, forma, unidad, pos, unidadFraccion, cantFracciones, vencimiento, diasVencimiento, dispensable, duplicable, diasMaximos, dosisMaximas, via, tipoProtocolo, noEnviar )

						var indice = elementosDetalle-1;

						//Seteo cada uno de los campos a sus valores por defecto
						document.getElementById( "wdosis"+tipoProtocolo+indice ).value = datosArticulo[ 20 ];
						document.getElementById( "wperiod"+tipoProtocolo+indice ).value = datosArticulo[ 21 ];
						document.getElementById( "wviadmon"+tipoProtocolo+indice ).value = datosArticulo[ 26 ];
						//alert( "....finicio: "+datosArticulo[ 18 ] + " a las:"+datosArticulo[ 19 ] );
						document.getElementById( "wfinicio"+tipoProtocolo+indice ).value = datosArticulo[ 18 ] + " a las:"+datosArticulo[ 19 ];
						document.getElementById( "wcondicion"+tipoProtocolo+indice ).value = datosArticulo[ 22 ];
						document.getElementById( "wchkconf"+tipoProtocolo+indice ).checked = datosArticulo[ 23 ] == 'on' ? true: false;
						document.getElementById( "wdiastto"+tipoProtocolo+indice ).value = datosArticulo[ 24 ];
						document.getElementById( "wdosmax"+tipoProtocolo+indice ).value = datosArticulo[ 25 ];

						//Oculto el boton para traer los medicamentos del día anterior
						var auxTb = document.getElementById( "tbCargarMedicamentosAnteriores" );
						auxTb.style.display = 'none';

						document.getElementById("btnCerrarVentana").style.display = 'none';

					}
				}
				$.unblockUI();
			}
		}
	}catch(e){
		$.unblockUI();
		alert(e);
	}

}


function agregarDosisMaxPorCondicion( tipo, indice, campo ){

	//Si no existe campo no se ejecuta la función
	if( !campo ){
		return;
	}

	if( campo.id == "wcondicionsum" || campo.id == "wfrecuencia" ){

		//Consulto valor por defecto de la dosis maxima
		var condicion = campo.value;

		if( campo.value == "" ){
			$( "#wdosismaxima" )[0].readOnly = false;
			$( "#wdiastratamiento" )[0].readOnly = false;
		}

		if( campo.id == "wfrecuencia" ){
			try{
				var valDma = dmaPorFrecuencia[ condicion ].dma;	//Dosis maxima
			}
			catch(e){
				return;
			}

			//Si tiene una condición que no permita dosis máximas
			//no se deja poner dosis máxima
			if( $( "#wcondicionsum" ).val() )
				var valCondicion = $( "#wcondicionsum" ).val();
			else
				var valCondicion = '';

			if( valCondicion != '' ){
				var valPdm = dmaPorCondicionesSuministro[ valCondicion ].pdm;

				if( !valPdm ){
					valDma = '';
				}
			}
		}

		if( campo.id != "wfrecuencia" ){

			if( condicion != '' ){
				var valDma = dmaPorCondicionesSuministro[ condicion ].dma;	//Dosis maxima
				var valPdm = dmaPorCondicionesSuministro[ condicion ].pdm;	//Permite dosis máxima
				var valPdt = dmaPorCondicionesSuministro[ condicion ].pdt;	//Permite días de tratamiento

				if( !valPdm ){
					$( "#wdosismaxima" )[0].readOnly = true;
					$( "#wdosismaxima" ).val( "" );
				}
				else{
					$( "#wdosismaxima" )[0].readOnly = false;

					//Verificó si hay dosis máxima por frecuencia
					if( !valDma || valDma == 0 || valDma == '' ){
						try{
							var valDma = dmaPorFrecuencia[ $( "#wfrecuencia" ).val() ].dma;	//Dosis maxima
						}
						catch(e){
							var valDma = false;
						}
					}
				}

				if( !valPdt ){
					$( "#wdiastratamiento" )[0].readOnly = true;
					$( "#wdiastratamiento" ).val( "" );
				}
				else{
					$( "#wdiastratamiento" )[0].readOnly = false;
				}
			}
			else{

				//Verificó si hay dosis máxima por frecuencia
				if( !valDma || valDma == 0 || valDma == '' ){
					try{
						var valDma = dmaPorFrecuencia[ $( "#wfrecuencia" ).val() ].dma;	//Dosis maxima
					}
					catch(e){
						var valDma = false;
					}
				}

			}
		}

		//Si existe un valor por defecto de dosis maxima
		if( valDma ){
			if( valDma > 0 ){
				document.getElementById( "wdosismaxima" ).value = valDma;
				$( "#wdiastratamiento" )[0].readOnly = true;

				//Borro dias de tratamiento por si hay algo escrito,
				//ya que dosis maximas y dias de tratamiento son mutuamente excluyentes
				document.getElementById( "wdiastratamiento" ).value = '';

				// inhabilitarDiasTratamiento( document.getElementById( "wcondicionsum" ), tipo, indice )
				//document.getElementById( "wdiastto"+tipo+indice ).enabled = true;
			}
		}

		inhabilitarDosisMaximaBusc( $( "#wdiastratamiento" )[0], 'wdosismaxima' );
		inhabilitarDiasTratamientoBusc( $( "#wdosismaxima" )[0], 'wdiastratamiento' );

		return;
	}

	/********************************************************************************
	 * Junio 13 de 2012
	 *
	 * Si una condicicon de suministro tiene dosis maxima por defecto se llena
	 * la dosis maxima
	 ********************************************************************************/
	if( campo && campo.id.substr(0,10) == "wcondicion" || campo.id.substr(0,7) == "wperiod" ){

		if( campo.value == "" ){

			if( $( "#waccN\\.13" ).eq(0).val().split(",")[0] == 'N' ){	//Dosis máxima
				$( "#wdosmax"+tipo+indice )[0].readOnly = true;
			}
			else{
				$( "#wdosmax"+tipo+indice )[0].readOnly = false;
			}

			if( $( "#waccN\\.12" ).eq(0).val().split(",")[0] == 'N' ){	//D
				$( "#wdiastto"+tipo+indice )[0].readOnly = true;
			}
			else{
				$( "#wdiastto"+tipo+indice )[0].readOnly = false;
			}
		}

		if( campo.id.substr(0,7) == "wperiod" ){

			var condicion = document.getElementById( "wperiod"+tipo+indice ).value;

			try{
				var valDma = dmaPorFrecuencia[ condicion ].dma;	//Dosis maxima

				//Si tiene una condición que no permita dosis máximas
				//no se deja poner dosis máxima
				if( $( "#wcondicion"+tipo+indice ).val() != '' ){
					var valPdm = dmaPorCondicionesSuministro[ $( "#wcondicion"+tipo+indice ).val() ].pdm;

					if( !valPdm ){
						valDma = '';
					}
				}
			}
			catch(e){
				
				if( dmaPorCondicionesSuministro[ $( "#wcondicion"+tipo+indice ).val() ] ){
					var valPdm = dmaPorCondicionesSuministro[ $( "#wcondicion"+tipo+indice ).val() ].pdm;
					if( valPdm ){
						var valDma = dmaPorCondicionesSuministro[ $( "#wcondicion"+tipo+indice ).val() ].dma;
						if( valDma > 0 ) habilitarFiltrosAntibioticos( valDma );
						else habilitarFiltrosAntibioticos( 0 );
					}
					else{
						habilitarFiltrosAntibioticos( 0 );
					}
					return;
				}
				else{
					habilitarFiltrosAntibioticos( 0 );
				}
			}
		}

		if( campo.id.substr(0,7) != "wperiod" ){

			var condicion = document.getElementById( "wcondicion"+tipo+indice ).value;

			if( condicion != '' ){
				var valDma = dmaPorCondicionesSuministro[ condicion ].dma;	//Dosis maxima
				var valPdm = dmaPorCondicionesSuministro[ condicion ].pdm;	//Permite dosis máxima
				var valPdt = dmaPorCondicionesSuministro[ condicion ].pdt;	//Permite días de tratamiento

				//Predomina el perfil que tenga el médico, si no puede modificar dosis máximas o días de tratamiento no se puede modificar
				//así la condición tenga la opción de modificarlo
				//var valPdm = $( "#waccN\\.13" ).eq(0).val().split(",")[0] == 'N' ? false: valPdm;	//Dosis máxima

				//Si la condición permite dosis máxima se revisa
				//que el usuario pueda modificar dosis máxima
				if( valPdm ){
					var valPdm = $( "#waccN\\.13" ).eq(0).val().split(",")[0] == 'N' ? false: valPdm;	//Dosis máxima
				}

				//Si la condición permite días de tratamiento se revisa
				//que el usuario pueda modificar dosis máxima
				if( valPdt ){
					var valPdt = $( "#waccN\\.12" ).eq(0).val().split(",")[0] == 'N' ? false: valPdt;		//Días de tto
				}


				if( !valPdm ){
					$( "#wdosmax"+tipo+indice )[0].readOnly = true;
					valDma = false;
					// $( "#wdosmax"+tipo+indice ).val( "" );
				}
				else{
					$( "#wdosmax"+tipo+indice )[0].readOnly = false;

					//Verificó si hay dosis máxima por frecuencia
					if( !valDma || valDma == 0 || valDma == '' ){
						try{
							var valDma = dmaPorFrecuencia[ $( "#wperiod"+tipo+indice ).val() ].dma;	//Dosis maxima
						}
						catch(e){
							var valDma = false;
						}
					}
				}

				if( !valPdt ){
					$( "#wdiastto"+tipo+indice )[0].readOnly = true;
					// $( "#wdiastto"+tipo+indice ).val( "" );
				}
				else{
					$( "#wdiastto"+tipo+indice )[0].readOnly = false;
				}
			}
			else{
				//Verificó si hay dosis máxima por frecuencia
				if( !valDma || valDma == 0 || valDma == '' ){
					try{
						var valDma = dmaPorFrecuencia[ $( "#wperiod"+tipo+indice ).val() ].dma;	//Dosis maxima
					}
					catch(e){
						var valDma = false;
					}
				}
			}
		}

		//Si existe un valor por defecto de dosis maxima
		if( valDma ){
			if( valDma > 0 ){
				document.getElementById( "wdosmax"+tipo+indice ).value = valDma;

				//Borro dias de tratamiento por si hay algo escrito,
				//ya que dosis maximas y dias de tratamiento son mutuamente excluyentes
				document.getElementById( "wdiastto"+tipo+indice ).value = '';

				// inhabilitarDiasTratamiento( document.getElementById( "wdosmax"+tipo+indice ), tipo, indice )
				//document.getElementById( "wdiastto"+tipo+indice ).enabled = true;
			}
		}
		
		habilitarFiltrosAntibioticos( valDma );

		inhabilitarDosisMaximaBusc( $( "#wdiastto"+tipo+indice )[0], "wdosmax"+tipo+indice );
		inhabilitarDiasTratamientoBusc( $( "#wdosmax"+tipo+indice )[0], "wdiastto"+tipo+indice );
		
		function habilitarFiltrosAntibioticos( dma ){
			if( $( "[idtr^=trFil"+tipo+indice+"]", $( "#detKardexAddN" ) ).length > 0 ){
				if( dma && dma > 0 ){
					if( $( "#wesantibiotico"+tipo+indice ).val() == 'on' ){
						if( dma == 1 ){
							// $( "#wprofilaxis"+tipo+indice ).attr({disabled:true});
							// $( "#wprofilaxis"+tipo+indice ).attr({checked:true});
							// $( "#wtratamiento"+tipo+indice ).attr({disabled:true});
							
							$( "#wdiastto"+tipo+indice ).attr({ disabled: true });
							$( "#wdosmax"+tipo+indice ).attr({ disabled: true });
						}
						else{
							// $( "#wprofilaxis"+tipo+indice ).attr({disabled:true});
							// $( "#wtratamiento"+tipo+indice ).attr({disabled:true});
							// $( "#wtratamiento"+tipo+indice ).attr({checked:true});
							
							$( "#wdiastto"+tipo+indice ).attr({ disabled: true });
							$( "#wdosmax"+tipo+indice ).attr({ disabled: true });
						}
					}
				}
				else{
					if( $( "#wprofilaxis"+tipo+indice )[0].checked ) $( "#wprofilaxis"+tipo+indice ).click();
					
					$( "#wprofilaxis"+tipo+indice ).attr({disabled:false});
					// $( "#wprofilaxis"+tipo+indice ).attr({checked:false});
					$( "#wtratamiento"+tipo+indice ).attr({disabled:false});
					// $( "#wtratamiento"+tipo+indice ).attr({checked:false});
					
					$( "#wdiastto"+tipo+indice ).attr({ disabled: false });
					$( "#wdosmax"+tipo+indice ).attr({ disabled: false });
					
					//if( $( "#wtratamiento"+tipo+indice )[0].checked ) $( "#wtratamiento"+tipo+indice ).click();
				}
			}
		}

		return;
	}
	/********************************************************************************/
}

/************************************************************************/

//Array doble esto es para validar los medicamentos y poder sacar el mensaje si el usuario cierra con la x
//[][0]	Codigo del articulo
//[][1] NOmbre del articulo
var articulosSinGrabar = Array();	//Indica que ariculos no se han grabado correctamente
var noMostarMsjError = false;


function quitarTooltip( campo ){
	campo.parentNode.originalTooltipText = campo.parentNode.tooltipText;
	campo.parentNode.tooltipText = null
}

function reestablecerTooltip( campo ){
	campo.parentNode.tooltipText = campo.parentNode.originalTooltipText;
}

/********************************************************************************
 * Mayo 30 de 2012
 *
 * Esto permite cambiar el valor del tooltip cuando esta en consulta el kardex
 ********************************************************************************/
var titleHoras = '';

function mostrarTitleTooltip(){

	if( titleHoras != '' ){
		var a = titleHoras;
		titleHoras = '';
		return a;
	}
	else{
		return this.tooltipText;
	}
}

function mostrarTooltip( celda, valor ){

	titleHoras = valor;
}
/********************************************************************************/

function ucWords(dato)
{
	var arrayWords;
	var returndato = "";
	var len;
	arrayWords = dato.split(" ");
	len = arrayWords.length;
	for(i=0;i < len ;i++)
	{
		if(i != (len-1)){
			returndato = returndato+ucFirst(arrayWords[i])+" ";
		}
		else
		{
			returndato = returndato+ucFirst(arrayWords[i]);
		}
	}
	return returndato;
}


/********************************************************************************/

function ucFirst(string)
{
	return string.substr(0,1).toUpperCase()+string.substr(1,string.length).toLowerCase();
}


/********************************************************************************/

// Permite intercalar la clase de un elemento contenedor por otra
function quitarFilaAlta(fila,checkimp)
{
	$('#'+fila).hide();
	document.getElementById(checkimp).checked = false;
}

function quitarFilaAltaExamen(fila,checkimp)
{
	$('#'+fila).hide();
	document.getElementById(checkimp).checked = false;
}

// Permite intercalar la clase de un elemento contenedor por otra
function cambiarClase(contenedor,clase1,clase2)
{
	//$('#'+contenedor).toggleClass(claseAlterna);
	//var claseImprimir = $('#'+contenedor).attr('class');
	//if(claseImprimir=='opacar')
	if( $('#'+contenedor).hasClass(clase1) )
	{
		$('#'+contenedor).removeClass(clase1);
		$('#'+contenedor).addClass(clase2);
	}
	else
	{
		$('#'+contenedor).removeClass(clase2);
		$('#'+contenedor).addClass(clase1);
	}
}

/********************************************************************************
 * Deshabilita el campo de dias de tratamiento si dosis maximas tiene algun valor
 ********************************************************************************/
function inhabilitarDiasTratamiento( campo, tipo, id ){

	if( $( "#waccN\\.13" ).eq(0).val().split(",")[0] == 'N' ){	//Dosis máxima		
		
		var dmaDef = false;
		try{
			dmaDef = dmaPorFrecuencia[ $( "#wperiod"+tipo+id ).val() ].dma;
		}
		catch(e){
			dmaDef = false;
		}
		
		if( !dmaDef ){
			try{
				dmaDef = dmaPorCondicionesSuministro[ $( "#wcondicion"+tipo+id ).val() ].dma;
			}
			catch(e){
				dmaDef = false;
			}
		}
	
		console.log( "dmaDef" )
		console.log( dmaDef )
		if( !dmaDef ){
			console.log( dmaDef )
			console.log( "Mmmmmmmmm..........." )
			$( "#wdosmax"+tipo+id )[0].readOnly = true;
			$( "#wdosmax"+tipo+id ).val( '' );
			return;
		}
	}

	//Marzo 25 de 2015
	if( $( campo ).val() <= 0 ){
		$( campo ).val( '' );
	}

	var inDtto = document.getElementById( 'wdiastto'+tipo+id );

	if( campo.readOnly && inDtto.readOnly ){
		return;
	}

	if( campo.value != '' ){
		inDtto.readOnly = true;
	}
	else{
		inDtto.readOnly = false;
	}
}

/********************************************************************************
 * Deshabilita el campo de dosis maxima si dias de tratamiento tiene algun valor
 ********************************************************************************/
function inhabilitarDosisMaxima( campo, tipo, id ){

	if( $( "#waccN\\.12" ).eq(0).val().split(",")[0] == 'N' ){	//Días de tratamiento
		$( "#wdiastto"+tipo+id )[0].readOnly = true;
		$( "#wdiastto"+tipo+id ).val('');
		return;
	}

	//Marzo 25 de 2015
	if( $( campo ).val() <= 0 ){
		$( campo ).val( '' );
	}

	var inDosisMaximas = document.getElementById( 'wdosmax'+tipo+id );

	if( inDosisMaximas.readOnly && campo.readOnly ){
		return;
	}

	if( campo.value != '' ){
		inDosisMaximas.readOnly = true;
	}
	else{
		inDosisMaximas.readOnly = false;
	}
}

function eleccionFrecuencia( cmp ){

		frecuencia = cmp.value;

		if(frecuencia=='H.E.')
		{
			document.getElementById( "regletaGrabacion" ).style.display = '';
			document.getElementById( "wdosisfamilia" ).value = '';
			document.getElementById( "wdosisfamilia" ).disabled = true;
		}
		else
		{
			document.getElementById( "regletaGrabacion" ).style.display = 'none';
			if(!esLiquidoEndovenoso)
				document.getElementById( "wdosisfamilia" ).disabled = false;
		}

		agregarDosisMaxPorCondicion( '', '', cmp );
}

function eleccionPreviaMedicamento(campo, e){

	var evento = e || window.event;

	var k = e.keyCode || e.which;

	if( k == 13 ){
		eleccionMedicamento();
	}

	campo.onblur();
}

function calcularDifDias(fecha,dif){
	var f=new Array(),i;
	var milisegundos=parseInt(24*60*60*1000)*dif;
	fecha=fecha.split("/").reverse();
	fecha=new Date(fecha.join("/"));
	var tiempo=fecha.getTime();
	fecha.setTime(parseInt(tiempo+milisegundos));
	f[2]=fecha.getDate();
	f[1]=fecha.getMonth()+1;
	f[0]=fecha.getFullYear();
	if(f[1]<10)
		f[1]="0"+f[1];
	if(f[2]<10)
		f[2]="0"+f[2];
	return f.join("-");
}

function calcularFechaInicio(fechaInicioFija,cont2)
{
	var auxFecha = new Array(),strFecha = new Array(),auxStrFecha = new Array();
	var auxFecha2 = "";
	auxFecha = fechaInicioFija.split(":");
	
	if(cont2*1 == 24)
	{
		cont2 = "00";
	}
	
	if(cont2*1 >= auxFecha[1]*1)
	{
		fechaInicioFija = auxFecha[0]+":"+cont2+":"+auxFecha[2];
	}
	else
	{
		strFecha = auxFecha[0].split(" ");
		auxStrFecha = strFecha[0].split("-");
		auxFecha2 = auxStrFecha[2]+"/"+auxStrFecha[1]+"/"+auxStrFecha[0];
		fechaInicioFija = calcularDifDias(auxFecha2,'1');
		//// 	  		Fecha (yy-mm-dd)				a				las			 :	 hora	   :	minutos
		fechaInicioFija = fechaInicioFija + " " + strFecha[1] + " " + strFecha[2] + ":" + cont2 + ":" + auxFecha[2];
	}
	//alert("cont2: "+cont2+" - fecIni: "+fechaInicioFija);
	return fechaInicioFija;
}


// Esta variable definía si se mostraba los mensajes de validación de datos
// Estaba condicionada para no mostrar el mensaje la segunda vez que ocurriera
// Se comenta porque si no se cumple la condición siempre se debe mostrar el mensje de validación
// var mostrarAviso = 1;

// Determina si la adición del medicamento es por horario especial
var horarioEspecial = false;
// Si contHorarioEspecial = 1; asi sea adición por horario especial
// se saca mensaje 'El medicamento ya existe. Desea agregarlo?'
var contHorarioEspecial = 0;

// Cadena que contendrá los artículos de los que ya se ha mostrado el mensaje que son NO POS
var msjNoPos = '';

// Guardará los datos de artículos y procedimientos NO POS cuando se ingresan por adición múltiple, de modo
// que se pueda llamar al final la ventana modal del formulario CTC para cada artículo o procedimiento NO POS
strPendientesCTC = "";

// Guardará los datos de artículos DA cuando se ingresan por horario especial
strPendientesModal = "";


// Determina si la adición de medicamentos es múltiple (por ejemplo: Importar protocolo o Insumos Liquidos Endovenosos)
var adicionMultiple = false;

function eleccionMedicamento(porProtocolo)
{
	adicionMultiple = false;

	var articulo;
	var periodo;
	var administracion;
	var condicion;
	var presentacion;
	var medida;

	var parametros = "";

	//Variable global que indica y tiene los medicamentos adicionales a grabar
	//Se dejan siempre vacia cada vez que se va a agregar un articulo nuevo
	multiplesMedicamentos = [];
	datosFinales = [];

	if(!isset(porProtocolo))
		porProtocolo = 0;

	// Se reviza si hay algun detalle de familia desplegado y se oculta
	ocultarDetalleFliaAnterior();
	
	// --------------------------------
	// Validar si ya existe una NPT agregada
	var cantNPTAgregadas = 0;
	if($("#famNPT").val() == $("#wnombrefamilia").val())
	{
		console.log("es nutricion");
		
		$('table[id=tbDetalleAddN] input[id^=wnmmed]').each(function(){
			
			if($(this).attr('esnpt') == "on")
			{
				cantNPTAgregadas++;
				return;
			}
		});
	}
	
	if(cantNPTAgregadas!=0)
	{
		jAlert("Ya existe una NPT agregada","ALERTA");
		funcInternaLimpiarBuscador();
		return;
	}
	// --------------------------------
	
	// Si no es adición de medicamentos por protocolo haga la validación de datos
	if(porProtocolo!=1)
	{
		//////////////////////////////////////////////////////////////////////
		//////// ASIGNACIÓN DE VARIABLES PARA LOS DATOS INGRESADOS ///////////

		// Asigno variables dadas en el formulario de ingreso
		var nombreFamilia = document.getElementById( "wnombrefamilia" ).value;		// Familia de medicamentos
		var presentaciones = document.getElementById( "wpresentacion" );			// Presentaciones o formas farmacéuticas
		var unidades = document.getElementById( "wunidad" );						// Unidades de medida
		var dosis = document.getElementById( "wdosisfamilia" ).value;				// Dosis a aplicar
		var frecuencias = document.getElementById( "wfrecuencia" );					// Frecuencias
		var viasAdministracion = document.getElementById( "wadministracion" );		// Vías de administración
		var fechaInicio = document.getElementById( "wfinicioaplicacion" ).value;	// Fecha y hora de inicio de aplicación
		var condiciones = document.getElementById( "wcondicionsum" );				// Condiciones de suministro
		var diasTto = document.getElementById( "wdiastratamiento" ).value;			// Días de tratamiento
		var dosisMax = document.getElementById( "wdosismaxima" ).value;				// Dosis máxima
		var observaciones = document.getElementById( "wtxtobservasiones" ).value;	// Observaciones

		// Obtengo los indices seleccionados en los campos desplegables (select's)
		var selPresentacion = presentaciones.selectedIndex;
		var selUnidad = unidades.selectedIndex;
		var selFrecuencia = frecuencias.selectedIndex;
		var selViaAdministracion = viasAdministracion.selectedIndex;
		var selCondicion = condiciones.selectedIndex;

		// Se asigna el valor a la variable para la presentación o forma farmacéutica
		if(selPresentacion!=-1)
			presentacion = document.getElementById('wpresentacion').options[selPresentacion].value;
		else
			presentacion = "";

		// Se asigna el valor a la variable para la unidad de medida
		if(selUnidad!=-1)
			medida = document.getElementById('wunidad').options[document.getElementById( 'wunidad' ).selectedIndex].value;
		else
			medida = "";

		// Se asigna el valor a la variable para la unidad de medida
		if(selFrecuencia!=-1)
			periodo = document.getElementById('wfrecuencia').options[selFrecuencia].value;
		else
			periodo = "";


		// Se asigna el valor a la variable para la vía de administración
		if(selViaAdministracion!=-1)
			administracion = document.getElementById('wadministracion').options[selViaAdministracion].value;
		else
			administracion = "";

		// Se asigna el valor a la variable para la condición de suministro
		if(selCondicion!=-1)
			condicion = document.getElementById('wcondicionsum').options[selCondicion].value;
		else
			condicion = "";

		// Se usarán si el medicamento es líquido endovenoso
		periodoLQ = periodo;
		administracionLQ = administracion;
		fechaInicioLQ = fechaInicio;
		condicionLQ = condicion;
		diasTtoLQ = diasTto;
		dosisMaxLQ = dosisMax;
		observacionesLQ = observaciones;

		////// FIN ASIGNACIÓN DE VARIABLES PARA LOS DATOS INGRESADOS /////////
		//////////////////////////////////////////////////////////////////////


		// arRelacionesUnidad = arRelUnidad;
		// arArticulosSeleccionados = arArticulos;

		//////////////////////////////////////////////////////////////////////
		///////// VALIDACIÓN DE LOS DATOS INGRESADOS /////////////////////////

		// Si no se ha ingresado el nombre de la familia o artículo, muestre el mensaje correspondiente
		if( document.getElementById( "wnombrefamilia" ).value == "" || document.getElementById( "wnombrefamilia" ).value == " " )
		{
			jAlert("Debe ingresar el nombre del artículo","ALERTA");
			document.getElementById( "wnombrefamilia" ).focus();
			return;
		}

		// Validación presentación o forma farmacéutica
		if(selPresentacion>-1)
		{
			var presentacionMed = presentaciones.options[selPresentacion].text;
			if(presentacionMed=="" || presentacionMed==" ")
			{
				jAlert("Debe seleccionar una presentación","ALERTA");
				document.getElementById( "wpresentacion" ).focus();
				document.getElementById( "wpresentacion" ).select();
				return false;
			}
		}
		else
		{
			jAlert("Debe seleccionar una presentación","ALERTA");
			document.getElementById( "wpresentacion" ).focus();
			document.getElementById( "wpresentacion" ).select();
			return false;
		}

		// Validación unidad de medida
		if(selUnidad>-1)
		{
			var unidMedida = unidades.options[selUnidad].text;
			if(unidMedida=="" || unidMedida==" ")
			{
				jAlert("Debe seleccionar una unidad de medida","ALERTA");
				document.getElementById( "wunidad" ).focus();
				document.getElementById( "wunidad" ).select();
				return false;
			}
		}
		else
		{
			jAlert("Debe seleccionar una unidad de medida","ALERTA");
			document.getElementById( "wunidad" ).focus();
			document.getElementById( "wunidad" ).select();
			return false;
		}

		//Si la dosis es vacio no agregar
		if( ( dosis == "" || dosis*1 == 0 ) && periodo != "H.E." && !esLiquidoEndovenoso ){
			jAlert("Debe ingresar la dosis","ALERTA");
			return;
		}

		// Validación de frecuencia
		if(selFrecuencia>-1)
		{
			var frecuencia = frecuencias.options[selFrecuencia].text;
			if(frecuencia=="" || frecuencia==" ")
			{
				jAlert("Debe seleccionar la frecuencia","ALERTA");
				document.getElementById( "wfrecuencia" ).focus();
				document.getElementById( "wfrecuencia" ).select();
				return false;
			}
		}
		else
		{
			jAlert("Debe seleccionar la frecuencia","ALERTA");
			document.getElementById( "wfrecuencia" ).focus();
			document.getElementById( "wfrecuencia" ).select();
			return false;
		}

		// Validación de la vía de administración
		if(selViaAdministracion>-1)
		{
			var viaAdministracion = viasAdministracion.options[selViaAdministracion].text;
			if(viaAdministracion=="" || viaAdministracion==" ")
			{
				jAlert("Debe seleccionar una vía de administración","ALERTA");
				document.getElementById( "wadministracion" ).focus();
				document.getElementById( "wadministracion" ).select();
				return false;
			}
		}
		else
		{
			jAlert("Debe seleccionar una vía de administración","ALERTA");
			document.getElementById( "wadministracion" ).focus();
			document.getElementById( "wadministracion" ).select();
			return false;
		}

		///////// FIN VALIDACIÓN DE LOS DATOS INGRESADOS /////////////////////
		//////////////////////////////////////////////////////////////////////

		//Se declaran las variables globales
		//arArticulosSeleccionados;
		arRelacionesUnidad;
		arRelacionesPresentacion;
		arRelacionesViaAdmon;

		// 2012-12-10
		// Se comenta porque estas variables no tienen uso
		// var sel = arRelacionesUnidad[ selUnidad ];		// Unidad de medida
		// var sel2 = arRelacionesPresentacion[ selPresentacion ];	// Presentacion
		// var sel4 = arRelacionesViaAdmon[ selViaAdministracion ];	// Via administracion

		// 2012-12-14
		// alert("eleccionMedicamento: arRelacionesUnidad="+arRelacionesUnidad.toSource());
		// alert("eleccionMedicamento: selUnidad="+selUnidad);

		// alert("arRelacionesUnidad. "+arRelacionesUnidad.toSource());
		// alert("arArticulosSeleccionados: "+arArticulosSeleccionados.toSource());
		//alert("selUnidad: "+selUnidad);

		var art = "" ;

		// 2012-12-14
		/*
		// Se comenta porque ya esta validación se va a hacer en PHP por medio de AJAX
		for( var i = 0; i < arRelacionesUnidad[ selUnidad ].length; i++ )
		{

			var pos = arRelacionesUnidad[ selUnidad ][i]-1;
			// alert("selUnidad: "+selUnidad+" - i: "+i+" - pos: "+pos);
			if( arArticulosSeleccionados[ pos ][1]*1 == dosis*1 ){
				art = arArticulosSeleccionados[ pos ][0];
				break;
			}
			else if( arArticulosSeleccionados[ pos ][1]*1 > dosis*1 ){
				art = arArticulosSeleccionados[ pos ][0];
				break;
			}
			else{
				art = arArticulosSeleccionados[ pos ][0];
			}

			// 2012-12-14
			alert("Art. "+art);
		}
		*/
	}
	// 2013-01-14
	// Si es adición de medicamentos por protocolo
	else
	{

		/*
		// Ya no es necesario usarlas como array porque el ciclo se hace abajo cuando se agrega cada artículo
		var articulo = new Array();
		var periodo = new Array();
		var administracion = new Array();
		var condicion = new Array();
		var dosis = new Array();
		var observaciones = new Array();
		*/

		if(document.forms.forma.wprotocolo.value!="" && document.forms.forma.wprotocolo.value!=" ")
			var valorProtocolo = document.forms.forma.wprotocolo.value;
		else if(document.forms.forma.wprotocolo_ayd.value!="" && document.forms.forma.wprotocolo_ayd.value!=" ")
			var valorProtocolo = document.forms.forma.wprotocolo_ayd.value;
		else
			var valorProtocolo = document.forms.forma.wprotocolo.value;

		// parametros = "consultaAjaxKardex=37&wemp_pmla="+document.forms.forma.wemp_pmla.value
					 // +"&basedatos="+document.forms.forma.wbasedato.value
					 // +"&protocolo="+valorProtocolo;
		parametros = "consultaAjaxKardex=37&wemp_pmla="+document.forms.forma.wemp_pmla.value
					 +"&basedatos="+document.forms.forma.wbasedato.value
					 +"&protocolo="+valorProtocolo
					 +"&cco="+$("#wservicio").val()
					 +"&codUsuario="+$("#usuario").val();
		// parametros = "consultaAjaxKardex=37&wemp_pmla="+document.forms.forma.wemp_pmla.value
					 // +"&basedatos="+document.forms.forma.wbasedato.value
					 // +"&protocolo="+valorProtocolo
					 // +"&cco="+$("#wservicio").val();
		try {
			ajax=nuevoAjax();
			ajax.open("POST", "ordenes.inc.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if( $.trim( ajax.responseText ) != '' )
			{
				if($.trim( ajax.responseText ) != "No se encontraron coincidencias")
				{
					var protocolos = $.trim( ajax.responseText ).split( "\\" );

					/*
					for (var ipro=0;ipro<protocolos.length-1;ipro++)
					{
						var protocolo = protocolos[ipro].split( "|" );
						articulo[ipro] = protocolo[0];				// Dosis a aplicar
						periodo[ipro] = protocolo[1];				// Frecuencias
						administracion[ipro] = protocolo[2];		// Vías de administración
						condicion[ipro] = protocolo[3];				// Condiciones de suministro
						dosisMax[ipro] = protocolo[4];				// Dosis máxima
						observaciones[ipro] = protocolo[5];			// Observaciones

						nombreFamilia = '';		// Familia de medicamentos
						presentacion = '';			// Presentaciones o formas farmacéuticas
						medida = '';						// Unidades de medida
						dosis = '';				// Dosis a aplicar
						fechaInicio = '';	// Fecha y hora de inicio de aplicación
						diasTto = '';			// Días de tratamiento
					}
					*/
				}
			}

		}catch(e){	}
	}

	//Muestra en el encabezado de un medicamento nuevo
	// document.getElementById( "trEncabezadoTbAdd" ).style.display = '';
	// $( "#trEncabezadoTbAdd", $( "#tbDetalleAddDA" ) ).css( { display: '' } );

	//buscando el medicamento
	var mensaje = "";


	// 2013-01-14
	// Si no es adición de medicamentos por protocolo
	if(porProtocolo!=1)
	{
		var medicamentoPorProtocolo = false;

		if(periodo!='H.E.')
		{

			$("#frecuencia_elegida").val(periodo);
			parametros = "consultaAjaxKardex=35&wemp_pmla="+document.forms.forma.wemp_pmla.value
						 +"&basedatos="+document.forms.forma.wbasedato.value
						 +"&cenmez="+document.forms.forma.wcenmez.value
						 +"&ccoPaciente="+document.forms.forma.wservicio.value
						 +"&q="+encodeURIComponent( nombreFamilia )
						 +"&pre="+presentacion
						 +"&med="+medida
						 +"&dos="+dosis
						 +"&adm="+administracion
						 +"&eps="+$( "#pacEPS" ).val()
						 +"&bsq="+bsqFamilia
						 +"&his="+document.forms.forma.whistoria.value
						 +"&ing="+document.forms.forma.wingreso.value;

			try{

				ajax=nuevoAjax();

				ajax.open("POST", "ordenes.inc.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);

				if( $.trim( ajax.responseText ) != '' ){
					if($.trim( ajax.responseText ) != "No se encontraron coincidencias"){

						var item = $.trim( $.trim( ajax.responseText ) ).split( "|" );

		//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));

						/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
						 * Argumentos:
						 *
						 * 0: Como se muestra en el autocomplete
						 * 1: Codigo del articulo
						 * 2: Nombre comercial del articulo
						 * 3: Nombre genérico del articulo
						 * 4: Tipo protocolo
						 * 5: (M)edicamento o (L)iquido
						 * 6: Es generico
						 * 7: Origen
						 * 8: Grupo de medicamento
						 * 9: Forma farmaceutica
						 * 10:Unidad
						 * 11:POS
						 * 12:Unidad de fraccion
						 * 13:Cantidad de fracciones
						 * 14:Vencimiento
						 * 15:Dias de estabilidad
						 * 16:Es dispensable
						 * 17:Es duplicable
						 * 18:Dias maximos sugeridos
						 * 19:Dosis maximas sugeridas
						 * 20:Via
						 * 21:Abre ventana parametrizada ----
						 * 22:Cantidad de dosis
						 * 23:Unidad de dosis
						 * 24:No enviar
						 * 25:Frecuencia
						 * 26:Vias de administracion
						 * 27:Condicion de suministro
						 * 28:Confirmada preparacion
						 * 29:Dias maximos tratamiento
						 * 30:Dosis maximas tratamiento
						 * 31:Observaciones adicionales
						 * 32:Componentes del tipo
						 * 33:Nombre personalizado del articulo
						 * 34:
						 * 35:Codigo principio activo
						 * 36:Descricion principio activo
						 * 37:esAntibiotico
						 * 38:InformacionNutriciones
						 */

						var codigoArticulo = item[1];
						var nombreComercial = item[2];
						var nombreGenerico = item[3];
						var tipoProtocolo = item[4];
						var tipoMedicamentoLiquido = item[5];
						var esGenerico = item[6];
						var origen = item[7];
						var grupoMedicamento = item[8];
						// 2012-08-02
						// Se cambia para que tome el valor seleccionado en la adición del medicamento y no el de la tabla de articulos
						var formaFarmaceutica = presentacion; //item[9];
						var unidadMedida = item[10];
						var pos = item[11];
						var unidadFraccion = item[12];
						var cantidadFraccion = dosis;	//item[13];
						var vencimiento = item[14];
						var diasEstabilidad = item[15];
						var dispensable = item[16];
						var duplicable = item[17];
						var diasMaximosSugeridos = item[18];
						var dosisMaximasSugeridas = item[19];
						var viaAdministracion = administracion;
						var abreVentanaFija = item[21];
						var cantidadDosisFija = item[22];
						var unidadDosisFija = medida;		//item[23];
						var noEnviarFija = item[24];
						var frecuenciaFija = periodo;
						var viasAdministracionFija = item[26];
						var condicionSuministroFija = condicion;
						var confirmadaPreparacionFija = item[28];
						var diasMaximosFija = diasTto;
						var dosisMaximasFija = dosisMax;
						var observacionesFija = item[34]+observaciones;
						var componentesTipo = item[32];
						var nombrePersonalizadoDelArticulo = item[33];
						fechaInicioFija = fechaInicio;

						var codPrincipioActivo = item[35];
						var desPrincipioActivo = item[36];
						var esAntibiotico = $.trim( item[37] ) == 'on' ? true: false;
						var infoNutriciones = item[38];
						
						var esCompuesto 	= item[39] == 'on' ? true: false ;
						var conMedicamento1 = item[40];
						var conMedicamento2 = item[41];
						
						var familiaATC 		= item[42];
						
						var conTarifa 		= item[43];
						
						
						// if(dosis!=cantidadDosisFija)
						// {
							// if(codigoArticulo.substr(0,2)=='DA')
								// codigoArticulo = "DA0000";
							// if(codigoArticulo.substr(0,2)=='NU')
								// codigoArticulo = "NU0000";
							// if(codigoArticulo.substr(0,2)=='QT')
								// codigoArticulo = "QT0000";

							// // if(codigoArticulo.substr(0,2)=='DA' && dosis==1000)
								// // codigoArticulo = "DA0025";
						// }

						var	noEnviar = item[33];	//Abril 25 de 2011

						if( esGenerico.length > 0 ){
							duplicable = 'on';
						}

						agregarArticulo( "detKardexAdd" + tipoProtocolo );

						seleccionarArticulo(codigoArticulo,reemplazarTodo(nombreComercial," ","_"),reemplazarTodo(nombreGenerico," ","_"),origen,grupoMedicamento,formaFarmaceutica,unidadMedida,pos,unidadFraccion,cantidadFraccion,vencimiento,diasEstabilidad,dispensable,duplicable,diasMaximosSugeridos,dosisMaximasSugeridas,viaAdministracion,tipoProtocolo,tipoMedicamentoLiquido,esGenerico,abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija,condicionSuministroFija,confirmadaPreparacionFija,diasMaximosFija,dosisMaximasFija,observacionesFija,componentesTipo,noEnviar,'off',codPrincipioActivo,desPrincipioActivo,esAntibiotico,infoNutriciones,esCompuesto,conMedicamento1,conMedicamento2,medicamentoPorProtocolo,familiaATC,conTarifa);

						document.getElementById("btnCerrarVentana").style.display = 'none';

						this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
						
						funcInternaLimpiarBuscador();
					}
				}

				if (!estaEnProceso(ajax)) {
					ajax.send(null);
				}
			}
			catch(e){	}

		}
		else
		{
			contHorarioEspecial = 0;
			horarioEspecial = true;
			var frecuenciaFija = 'C24';
			$("#frecuencia_elegida").val(periodo);

			//cambioFecha = false;	// Me indica si en la función calcularFechaInicio ya se paso a las rondas del día siguiente

			var horaArranque = document.getElementById('horaArranque').value;
			var auxFechaInicioFija;

			var cont1 = 1;
			var cont2 = horaArranque;   //Desplazamiento desde la hora inicial
			auxFechaInicioFija = fechaInicio;

			multiplesMedicamentos = [];	//Creo el array en vacio

			while(cont1 <= 24)
			{

				if(document.getElementById('dosisRonda'+cont2).value!="" && document.getElementById('dosisRonda'+cont2).value!=" ")
				{
					dosis = document.getElementById('dosisRonda'+cont2).value;

					fechaInicioFija = calcularFechaInicio(auxFechaInicioFija,cont2);


					parametros = "consultaAjaxKardex=35&wemp_pmla="+document.forms.forma.wemp_pmla.value
								 +"&basedatos="+document.forms.forma.wbasedato.value
								 +"&cenmez="+document.forms.forma.wcenmez.value
								 +"&ccoPaciente="+document.forms.forma.wservicio.value
								 +"&q="+encodeURIComponent( nombreFamilia )
								 +"&pre="+presentacion
								 +"&med="+medida
								 +"&dos="+dosis
								 +"&adm="+administracion
								 +"&eps="+$( "#pacEPS" ).val()
								 +"&bsq="+bsqFamilia
								 +"&his="+document.forms.forma.whistoria.value
								 +"&ing="+document.forms.forma.wingreso.value;

					try{

						ajax=nuevoAjax();

						ajax.open("POST", "ordenes.inc.php",false);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);

						if( $.trim( ajax.responseText ) != '' ){
							if($.trim( ajax.responseText ) != "No se encontraron coincidencias"){

								var item = $.trim( ajax.responseText ).split( "|" );

				//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));

								/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
								 * Argumentos:
								 *
								 * 0: Como se muestra en el autocomplete
								 * 1: Codigo del articulo
								 * 2: Nombre comercial del articulo
								 * 3: Nombre genérico del articulo
								 * 4: Tipo protocolo
								 * 5: (M)edicamento o (L)iquido
								 * 6: Es generico
								 * 7: Origen
								 * 8: Grupo de medicamento
								 * 9: Forma farmaceutica
								 * 10:Unidad
								 * 11:POS
								 * 12:Unidad de fraccion
								 * 13:Cantidad de fracciones
								 * 14:Vencimiento
								 * 15:Dias de estabilidad
								 * 16:Es dispensable
								 * 17:Es duplicable
								 * 18:Dias maximos sugeridos
								 * 19:Dosis maximas sugeridas
								 * 20:Via
								 * 21:Abre ventana parametrizada ----
								 * 22:Cantidad de dosis
								 * 23:Unidad de dosis
								 * 24:No enviar
								 * 25:Frecuencia
								 * 26:Vias de administracion
								 * 27:Condicion de suministro
								 * 28:Confirmada preparacion
								 * 29:Dias maximos tratamiento
								 * 30:Dosis maximas tratamiento
								 * 31:Observaciones adicionales
								 * 32:Componentes del tipo
								 * 33:Nombre personalizado del articulo
								 */

								var codigoArticulo = item[1];
								var nombreComercial = item[2];
								var nombreGenerico = item[3];
								var tipoProtocolo = item[4];
								var tipoMedicamentoLiquido = item[5];
								var esGenerico = item[6];
								var origen = item[7];
								var grupoMedicamento = item[8];
								// 2012-08-02
								// Se cambia para que tome el valor seleccionado en la adición del medicamento y no el de la tabla de articulos
								var formaFarmaceutica = presentacion; //item[9];
								var unidadMedida = item[10];
								var pos = item[11];
								var unidadFraccion = item[12];
								var cantidadFraccion = dosis;	//item[13];
								var vencimiento = item[14];
								var diasEstabilidad = item[15];
								var dispensable = item[16];
								var duplicable = item[17];
								var diasMaximosSugeridos = item[18];
								var dosisMaximasSugeridas = item[19];
								var viaAdministracion = administracion;
								var abreVentanaFija = item[21];
								var cantidadDosisFija = item[22];
								var unidadDosisFija = medida;		//item[23];
								var noEnviarFija = item[24];
								// var frecuenciaFija = periodo;
								var viasAdministracionFija = item[26];
								var condicionSuministroFija = condicion;
								var confirmadaPreparacionFija = item[28];
								var diasMaximosFija = diasTto;
								var dosisMaximasFija = dosisMax;
								var observacionesFija = item[34]+observaciones;
								var componentesTipo = item[32];
								var nombrePersonalizadoDelArticulo = item[33];

								var codPrincipioActivo = item[34];
								var desPrincipioActivo = item[36];
								var esAntibiotico = $.trim( item[37] ) == 'on' ? true: false;
								
								var esCompuesto 	= item[39] == 'on' ? true: false ;
								var conMedicamento1 = item[40];
								var conMedicamento2 = item[41];
								
								var familiaATC 		= item[42];
								
								var conTarifa 		= item[43];

								var array_dosis = new Array();
								var i = 0;

								$('#regletaGrabacion').find('[id^=dosisRonda]').each(function (x) {

										if($.trim($(this).val()) != undefined && $.trim($(this).val()) != ""){

											array_dosis[i] = $(this).val();
											i++;
										}


								});

								var dosis_final = array_dosis.join( "-" );

								if($("#dosis_medico_aux").val() == ''){
									$("#dosis_medico_aux").val(dosis_final);
								}


								// if(dosis!=cantidadDosisFija)
								// {
									// if(codigoArticulo.substr(0,2)=='DA')
										// codigoArticulo = "DA0000";
									// if(codigoArticulo.substr(0,2)=='NU')
										// codigoArticulo = "NU0000";
									// if(codigoArticulo.substr(0,2)=='QT')
										// codigoArticulo = "QT0000";

									// // if(codigoArticulo.substr(0,2)=='DA' && dosis==1000)
										// // codigoArticulo = "DA0025";
								// }

								var	noEnviar = item[33];	//Abril 25 de 2011

								if( esGenerico.length > 0 ){
									duplicable = 'on';
								}




								var idxMulArt = multiplesMedicamentos.length;
								multiplesMedicamentos[idxMulArt] = [];
								var idxMultArt2 = 0
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codigoArticulo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	reemplazarTodo(nombreComercial," ","_");
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	reemplazarTodo(nombreGenerico," ","_");
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	origen;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	grupoMedicamento;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	formaFarmaceutica;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadMedida;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	pos;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadFraccion;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	cantidadFraccion;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	vencimiento;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasEstabilidad;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dispensable;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	duplicable;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasMaximosSugeridos;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dosisMaximasSugeridas;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	viaAdministracion;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoProtocolo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoMedicamentoLiquido;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esGenerico;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	abreVentanaFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	cantidadDosisFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadDosisFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	noEnviarFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	frecuenciaFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	viasAdministracionFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	condicionSuministroFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	confirmadaPreparacionFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasMaximosFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dosisMaximasFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	observacionesFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	componentesTipo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	noEnviar;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	'off';
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codPrincipioActivo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	desPrincipioActivo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esAntibiotico;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	undefined;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esCompuesto;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	conMedicamento1;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	conMedicamento2;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	medicamentoPorProtocolo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	familiaATC;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	conTarifa;

								
								//Agrego la fecha y hora de inicio del medicamento calculada para el H.E
								multiplesMedicamentos[idxMulArt].fechaInicioFija = 	fechaInicioFija;

								//Se agrega esta propiedad para saber que tipo de articulo se va a agregar
								multiplesMedicamentos[idxMulArt].tipoArt = 	'HE';
								

								document.getElementById("btnCerrarVentana").style.display = 'none';

								this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;

								fechaInicioFija = auxFechaInicioFija;

								/****************************************************************
								 * Noviembre 19 de 2012
								 ****************************************************************/
								 if(true || pos == 'N'){
									 document.getElementById('dosisRonda'+cont2).value = "";
									 //return;
								 }
								/****************************************************************/

							}
						}

						if (!estaEnProceso(ajax)) {
							ajax.send(null);
						}
					}catch(e){	}
				}

				if(cont2 == 24){
					cont2 = 0;
				}

				cont1++;
				cont2++;

				if(cont2 % 2 != 0){
					cont2++;
				}
				if(cont1 % 2 != 0){
					cont1++;
				}

				if(cont2 == horaArranque){
					break;
				}

			}
			adicionMultiple =  true;
			agregarMultiplesArticulos();

			$('#dosis_medico'+codigoArticulo).val($("#dosis_medico_aux").val());

			// funcInternaLimpiarBuscador();
		}
	}
	// Si es adición de medicamentos por protocolo
	else
	{
		var medicamentoPorProtocolo = true;
		
		adicionMultiple =  true;
		//var codigoArticulo = '';

		function funcInternaAgregar(ipro)
		{
			// for (ipro=0;ipro<protocolos.length-1;ipro++)
			if( ipro<protocolos.length-1 )
			{
				var protocolo = protocolos[ipro].split( "|" );

				if(protocolo[6]=='Medicamentos')
				{
					articulo = protocolo[0];				// Codigo del articulo
					periodo = protocolo[1];					// Frecuencias
					administracion = protocolo[2];			// Vías de administración
					condicion = protocolo[3];				// Condiciones de suministro
					var dosis = protocolo[4];				// Dosis
					var observaciones = protocolo[5];		// Observaciones
					medicamentoPorProtocolo = protocolo[10] == 'on' ? true: false;		// No dispensable

					presentacion = '';			// Presentaciones o formas farmacéuticas
					medida = '';				// Unidades de medida
					var fechaInicio = '';		// Fecha y hora de inicio de aplicación
					var dosisMax = '';			// Dosis a aplicar
					var diasTto = '';			// Días de tratamiento
					
					if( medicamentoPorProtocolo ){
						dosisMax = 1;
					}

					parametros = "consultaAjaxKardex=36&wemp_pmla="+document.forms.forma.wemp_pmla.value
								 +"&basedatos="+document.forms.forma.wbasedato.value
								 +"&cenmez="+document.forms.forma.wcenmez.value
								 +"&ccoPaciente="+document.forms.forma.wservicio.value
								 +"&q="+articulo
								 +"&dos="+dosis
								 +"&adm="+administracion;

					try{

						ajax=nuevoAjax();

						ajax.open("POST", "ordenes.inc.php",false);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);

						if( $.trim( ajax.responseText ) != '' ){
							if($.trim( ajax.responseText ) != "No se encontraron coincidencias"){

								var item = $.trim( ajax.responseText ).split( "|" );

				//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));

								/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
								 * Argumentos:
								 *
								 * 0: Como se muestra en el autocomplete
								 * 1: Codigo del articulo
								 * 2: Nombre comercial del articulo
								 * 3: Nombre genérico del articulo
								 * 4: Tipo protocolo
								 * 5: (M)edicamento o (L)iquido
								 * 6: Es generico
								 * 7: Origen
								 * 8: Grupo de medicamento
								 * 9: Forma farmaceutica
								 * 10:Unidad
								 * 11:POS
								 * 12:Unidad de fraccion
								 * 13:Cantidad de fracciones
								 * 14:Vencimiento
								 * 15:Dias de estabilidad
								 * 16:Es dispensable
								 * 17:Es duplicable
								 * 18:Dias maximos sugeridos
								 * 19:Dosis maximas sugeridas
								 * 20:Via
								 * 21:Abre ventana parametrizada ----
								 * 22:Cantidad de dosis
								 * 23:Unidad de dosis
								 * 24:No enviar
								 * 25:Frecuencia
								 * 26:Vias de administracion
								 * 27:Condicion de suministro
								 * 28:Confirmada preparacion
								 * 29:Dias maximos tratamiento
								 * 30:Dosis maximas tratamiento
								 * 31:Observaciones adicionales
								 * 32:Componentes del tipo
								 * 33:Nombre personalizado del articulo
								 */

								var codigoArticulo = item[1];
								var nombreComercial = item[2];
								var nombreGenerico = item[3];
								var tipoProtocolo = item[4];
								var tipoMedicamentoLiquido = item[5];
								var esGenerico = item[6];
								var origen = item[7];
								var grupoMedicamento = item[8];
								var formaFarmaceutica = $.trim(item[9]);
								var unidadMedida = item[10];
								var pos = item[11];
								var unidadFraccion = item[12];
								var cantidadFraccion = dosis;	//item[13];
								var vencimiento = item[14];
								var diasEstabilidad = item[15];
								var dispensable = item[16];
								var duplicable = item[17];
								var diasMaximosSugeridos = item[18];
								var dosisMaximasSugeridas = item[19];
								var viaAdministracion = administracion;
								var abreVentanaFija = item[21];
								var cantidadDosisFija = item[22];
								var unidadDosisFija = item[23];
								var noEnviarFija = item[24];
								var frecuenciaFija = periodo;
								var viasAdministracionFija = item[26];
								var condicionSuministroFija = condicion;
								var confirmadaPreparacionFija = item[28];
								var diasMaximosFija = diasTto;
								var dosisMaximasFija = dosisMax;
								var observacionesFija = item[34]+observaciones;
								var componentesTipo = item[32];
								var nombrePersonalizadoDelArticulo = item[33];
								fechaInicioFija = fechaInicio;

								var codPrincipioActivo = item[34];
								var desPrincipioActivo = item[35];
								var esAntibiotico = $.trim( item[37] ) == 'on' ? true: false;
								
								var esCompuesto 	= item[39] == 'on' ? true: false ;
								var conMedicamento1 = item[40];
								var conMedicamento2 = item[41];
								
								var familiaATC = item[42];

								// if(dosis!=cantidadDosisFija)
								// {
									// if(codigoArticulo.substr(0,2)=='DA')
										// codigoArticulo = "DA0000";
									// if(codigoArticulo.substr(0,2)=='NU')
										// codigoArticulo = "NU0000";
									// if(codigoArticulo.substr(0,2)=='QT')
										// codigoArticulo = "QT0000";

									// // if(codigoArticulo.substr(0,2)=='DA' && dosis==1000)
										// // codigoArticulo = "DA0025";
								// }

								var	noEnviar = item[33];	//Abril 25 de 2011

								if( esGenerico.length > 0 ){
									duplicable = 'on';
								}
								
								
								





								var idxMulArt = multiplesMedicamentos.length;
								multiplesMedicamentos[idxMulArt] = [];
								var idxMultArt2 = 0
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codigoArticulo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	reemplazarTodo(nombreComercial," ","_");
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	reemplazarTodo(nombreGenerico," ","_");
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	origen;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	grupoMedicamento;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	formaFarmaceutica;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadMedida;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	pos;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadFraccion;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	cantidadFraccion;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	vencimiento;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasEstabilidad;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dispensable;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	duplicable;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasMaximosSugeridos;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dosisMaximasSugeridas;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	viaAdministracion;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoProtocolo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoMedicamentoLiquido;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esGenerico;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	abreVentanaFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	cantidadDosisFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadDosisFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	noEnviarFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	frecuenciaFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	viasAdministracionFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	condicionSuministroFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	confirmadaPreparacionFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasMaximosFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dosisMaximasFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	observacionesFija;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	componentesTipo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	noEnviar;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	'off';
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codPrincipioActivo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	desPrincipioActivo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esAntibiotico;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	undefined;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esCompuesto;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	conMedicamento1;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	conMedicamento2;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	medicamentoPorProtocolo;
								multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	familiaATC;

								//Agrego la fecha y hora de inicio del medicamento calculada para el H.E
								multiplesMedicamentos[idxMulArt].fechaInicioFija = 	fechaInicioFija;

								//Se agrega esta propiedad para saber que tipo de articulo se va a agregar
								multiplesMedicamentos[idxMulArt].tipoArt = 	'HE';
								
								document.getElementById("btnCerrarVentana").style.display = 'none';

								this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
							}
						}
						funcInternaAgregar(ipro+1);
						// if (!estaEnProceso(ajax)) {
							// ajax.send(null);
						// }
					}catch(e){ console.log(e);	}

				}
				else if(protocolo[6]=='Procedimientos')
				{

					var aydCodigo = protocolo[0];				// Codigo ayuda diagnostica
					var aydJustificacion = protocolo[7];		// Justificacion ayuda diagnostica
					parametros = "consultaAjaxKardex=38&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&ayd_cod="+aydCodigo+"&ccoPaciente="+document.forms.forma.wservicio.value;

					try {
						ajax=nuevoAjax();
						ajax.open("POST", "ordenes.inc.php",false);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);

						if( $.trim( ajax.responseText ) != '' )
						{
							var aydItem = $.trim( ajax.responseText ).split( "|" );
							
							seleccionarAyudaDiagnostica(aydItem[1],aydItem[2],aydItem[3],aydItem[4],aydItem[5],aydItem[6],aydItem[7],aydItem[8],aydItem[9],aydItem[10],aydItem[11],aydItem[12],aydJustificacion,funcInternaFocus, aydItem[13] ? JSON.parse(aydItem[13] ) : {}, aydItem[14], aydItem[15] );
						}
						else{
							funcInternaAgregar(ipro+1);
							return;
						}
						
					}
					catch(e){	}

					function funcInternaFocus()
					{
						document.getElementById("btnCerrarVentana").style.display = 'none';

						this.onfocus = function(){
							if( justificacionUltimoExamenAgregado ){
								justificacionUltimoExamenAgregado.focus();
								justificacionUltimoExamenAgregado = '';
							}
						};
						// adicionMultiple =  true;
						funcInternaAgregar(ipro+1);
						// funcInternaLimpiarBuscador();

					}

				}

			}
			else{
				funcInternaLimpiarBuscador();
			}
		}

		funcInternaAgregar(0);

		adicionMultiple =  true;
	}

	function funcInternaLimpiarBuscador()
	{
		agregarMultiplesArticulos();

		//Una vez agregado el medicamento borro los datos de la consulta
		var tbNuevoBuscador = document.getElementById( "nuevoBuscador" );

		document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
		document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo

		/* 2012-11-02
		// Se comenta porque se va a obtener el elemento por el nombre
		// ya que la nueva estructura no permite obtenerlo por posición en la tabla
		tbNuevoBuscador.rows[1].cells[0].firstChild.value = '';				//Borro el valor de Nombre articulo
		tbNuevoBuscador.rows[1].cells[1].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la presentacion
		tbNuevoBuscador.rows[1].cells[2].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las unidades
		tbNuevoBuscador.rows[1].cells[3].firstChild.value = '';				//Quito el valor escrito de la dosis
		tbNuevoBuscador.rows[1].cells[4].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la frecuencia
		tbNuevoBuscador.rows[1].cells[5].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las via de administracion
		tbNuevoBuscador.rows[1].cells[7].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la condicion
		tbNuevoBuscador.rows[1].cells[8].firstChild.value = '';				//Quito el valor escrito de los dias de tratmiento
		tbNuevoBuscador.rows[1].cells[9].firstChild.value = '';				//Quito el valor escrito de la dosis maxima
		tbNuevoBuscador.rows[1].cells[10].firstChild.value = '';			//Quito el valor escrito de las observaciones
		*/

		//Quito los valores de los campos de busqueda y adición de medicamentos
		document.getElementById('wnombrefamilia').value = '';
		document.getElementById('wpresentacion').selectedIndex = -1;
		document.getElementById('wunidad').selectedIndex = -1;
		document.getElementById('wdosisfamilia').value = '';
		document.getElementById('wfrecuencia').selectedIndex = -1;
		document.getElementById('wadministracion').selectedIndex = -1;
		document.getElementById('wfinicioaplicacion').value = $('#wfinicioaplicacion').val();
		document.getElementById('wcondicionsum').selectedIndex = -1;
		document.getElementById('wdiastratamiento').value = '';
		document.getElementById('wdosismaxima').value = '';
		document.getElementById('wtxtobservasiones').value = '';

		document.getElementById('wprotocolo').value = '';

		//Quito los valores de la regleta de grabacion
		document.getElementById('dosisRonda2').value = '';
		document.getElementById('dosisRonda4').value = '';
		document.getElementById('dosisRonda6').value = '';
		document.getElementById('dosisRonda8').value = '';
		document.getElementById('dosisRonda10').value = '';
		document.getElementById('dosisRonda12').value = '';
		document.getElementById('dosisRonda14').value = '';
		document.getElementById('dosisRonda16').value = '';
		document.getElementById('dosisRonda18').value = '';
		document.getElementById('dosisRonda20').value = '';
		document.getElementById('dosisRonda22').value = '';
		document.getElementById('dosisRonda24').value = '';

		// if(strPendientesCTC!="")
				// abrirCTCMultiple();

		if(strPendientesModal!="")
			abrirModalMultiple();

		adicionMultiple = false;

		//Reinicia la fecha y hora en la seleccion de medicamentos.
		var fecha_hora_actual = $("#whfinicioN999").val();
		$("#wfinicioaplicacion").val(fecha_hora_actual);

		// 2013-01-21
		//tbNuevoBuscador.rows[1].cells[10].firstChild.onblur();
	}

}




function eleccionMedicamentoAlta()
{

	var articulo;
	var periodo;
	var administracion;
	var condicion;
	var presentacion;
	var medida;

	var parametros = "";

	// Se reviza si hay algun detalle de familia desplegado y se oculta
	// ocultarDetalleFliaAnterior();

	//////////////////////////////////////////////////////////////////////
	//////// ASIGNACIÓN DE VARIABLES PARA LOS DATOS INGRESADOS ///////////

	// Asigno variables dadas en el formulario de ingreso
	var nombreFamilia = document.getElementById( "wnombrefamiliaimp" ).value;		// Familia de medicamentos
	var presentaciones = document.getElementById( "wpresentacionimp" );			// Presentaciones o formas farmacéuticas
	var unidades = document.getElementById( "wunidadimp" );						// Unidades de medida
	var dosis = document.getElementById( "wdosisfamiliaimp" ).value;				// Dosis a aplicar
	var frecuencias = document.getElementById( "wfrecuenciaimp" );					// Frecuencias
	var viasAdministracion = document.getElementById( "wadministracionimp" );		// Vías de administración
	var fechaInicio = document.getElementById( "wfinicioaplicacionimp" ).value;	// Fecha y hora de inicio de aplicación
	var condiciones = document.getElementById( "wcondicionsumimp" );				// Condiciones de suministro
	var diasTto = document.getElementById( "wdiastratamientoimp" ).value;			// Días de tratamiento
	var dosisMax = document.getElementById( "wdosismaximaimp" ).value;				// Dosis máxima
	var cantidadAlta = document.getElementById( "wcantidadaltaimp" ).value;				// Cantidad ordenada para el alta


	var observaciones = document.getElementById( "wtxtobservasionesimp" ).value;	// Observaciones

	// Obtengo los indices seleccionados en los campos desplegables (select's)
	var selPresentacion = presentaciones.selectedIndex;
	var selUnidad = unidades.selectedIndex;
	var selFrecuencia = frecuencias.selectedIndex;
	// var selViaAdministracion = viasAdministracion.selectedIndex;
	// var selCondicion = condiciones.selectedIndex;

	// Se asigna el valor a la variable para la presentación o forma farmacéutica
	if(selPresentacion!=-1)
		presentacion = document.getElementById('wpresentacionimp').options[selPresentacion].value;
	else
		presentacion = "";

	// Se asigna el valor a la variable para la unidad de medida
	if(selUnidad!=-1)
		medida = document.getElementById('wunidadimp').options[document.getElementById( 'wunidadimp' ).selectedIndex].value;
	else
		medida = "";

	// Se asigna el valor a la variable para la unidad de medida
	if(selFrecuencia!=-1)
		periodo = document.getElementById('wfrecuenciaimp').options[selFrecuencia].value;
	else
		periodo = "";


	// Se asigna el valor a la variable para la vía de administración
	// if(selViaAdministracion!=-1)
		// administracion = document.getElementById('wadministracionimp').options[selViaAdministracion].value;
	// else
		// administracion = "";

	administracion = document.getElementById( "wadministracionimp" ).value;		// Vías de administración


	// // Se asigna el valor a la variable para la condición de suministro
	// if(selCondicion!=-1)
		// condicion = document.getElementById('wcondicionsumimp').options[selCondicion].value;
	// else
		// condicion = "";

	condicion = document.getElementById( "wcondicionsumimp" ).value;				// Condiciones de suministro


	// Se usarán si el medicamento es líquido endovenoso
	periodoLQ = periodo;
	administracionLQ = administracion;
	fechaInicioLQ = fechaInicio;
	condicionLQ = condicion;
	diasTtoLQ = diasTto;
	dosisMaxLQ = dosisMax;
	cantidadAltaLQ = cantidadAlta;
	observacionesLQ = observaciones;

	////// FIN ASIGNACIÓN DE VARIABLES PARA LOS DATOS INGRESADOS /////////
	//////////////////////////////////////////////////////////////////////


	// arRelacionesUnidad = arRelUnidad;
	// arArticulosSeleccionados = arArticulos;

	//////////////////////////////////////////////////////////////////////
	///////// VALIDACIÓN DE LOS DATOS INGRESADOS /////////////////////////

	// Si no se ha ingresado el nombre de la familia o artículo, muestre el mensaje correspondiente
	if( document.getElementById( "wnombrefamiliaimp" ).value == "" || document.getElementById( "wnombrefamiliaimp" ).value == " " )
	{
		alert("Debe ingresar el nombre del artículo");
		document.getElementById( "wnombrefamiliaimp" ).focus();
		return;
	}

	// Validación presentación o forma farmacéutica
	if(selPresentacion>-1)
	{
		var presentacionMed = presentaciones.options[selPresentacion].text;
		if(presentacionMed=="" || presentacionMed==" ")
		{
			alert("Debe seleccionar una presentación");
			document.getElementById( "wpresentacionimp" ).focus();
			document.getElementById( "wpresentacionimp" ).select();
			return false;
		}
	}
	else
	{
		alert("Debe seleccionar una presentación");
		document.getElementById( "wpresentacionimp" ).focus();
		document.getElementById( "wpresentacionimp" ).select();
		return false;
	}

	// Validación unidad de medida
	if(selUnidad>-1)
	{
		var unidMedida = unidades.options[selUnidad].text;
		if(unidMedida=="" || unidMedida==" ")
		{
			alert("Debe seleccionar una unidad de medida");
			document.getElementById( "wunidadimp" ).focus();
			document.getElementById( "wunidadimp" ).select();
			return false;
		}
	}
	else
	{
		alert("Debe seleccionar una unidad de medida");
		document.getElementById( "wunidadimp" ).focus();
		document.getElementById( "wunidadimp" ).select();
		return false;
	}

	//Si la dosis es vacio no agregar
	if( (dosis == "" || dosis*1 == 0) && periodo != "H.E." && !esLiquidoEndovenoso ){
		alert("Debe ingresar la dosis");
		return;
	}

	// Validación de frecuencia
	if(selFrecuencia>-1)
	{
		var frecuencia = frecuencias.options[selFrecuencia].text;
		if(frecuencia=="" || frecuencia==" ")
		{
			alert("Debe seleccionar la frecuencia");
			document.getElementById( "wfrecuenciaimp" ).focus();
			document.getElementById( "wfrecuenciaimp" ).select();
			return false;
		}
	}
	else
	{
		alert("Debe seleccionar la frecuencia");
		document.getElementById( "wfrecuenciaimp" ).focus();
		document.getElementById( "wfrecuenciaimp" ).select();
		return false;
	}

	// Validación de la vía de administración
	/*
	if(selViaAdministracion>-1)
	{
		var viaAdministracion = viasAdministracion.options[selViaAdministracion].text;
		if(viaAdministracion=="" || viaAdministracion==" ")
		{
			alert("Debe seleccionar una vía de administración");
			document.getElementById( "wadministracionimp" ).focus();
			document.getElementById( "wadministracionimp" ).select();
			return false;
		}
	}
	else
	{
		alert("Debe seleccionar una vía de administración");
		document.getElementById( "wadministracionimp" ).focus();
		document.getElementById( "wadministracionimp" ).select();
		return false;
	}
	*/

	///////// FIN VALIDACIÓN DE LOS DATOS INGRESADOS /////////////////////
	//////////////////////////////////////////////////////////////////////

	//Se declaran las variables globales
	//arArticulosSeleccionados;
	arRelacionesUnidad;
	arRelacionesPresentacion;
	//arRelacionesViaAdmon;

	// 2012-12-10
	// Se comenta porque estas variables no tienen uso
	// var sel = arRelacionesUnidad[ selUnidad ];		// Unidad de medida
	// var sel2 = arRelacionesPresentacion[ selPresentacion ];	// Presentacion
	// var sel4 = arRelacionesViaAdmon[ selViaAdministracion ];	// Via administracion

	// 2012-12-14
	// alert("eleccionMedicamento: arRelacionesUnidad="+arRelacionesUnidad.toSource());
	// alert("eleccionMedicamento: selUnidad="+selUnidad);

	// alert("arRelacionesUnidad. "+arRelacionesUnidad.toSource());
	// alert("arArticulosSeleccionados: "+arArticulosSeleccionados.toSource());
	//alert("selUnidad: "+selUnidad);

	var art = "" ;

	// 2012-12-14
	/*
	// Se comenta porque ya esta validación se va a hacer en PHP por medio de AJAX
	for( var i = 0; i < arRelacionesUnidad[ selUnidad ].length; i++ )
	{

		var pos = arRelacionesUnidad[ selUnidad ][i]-1;
		// alert("selUnidad: "+selUnidad+" - i: "+i+" - pos: "+pos);
		if( arArticulosSeleccionados[ pos ][1]*1 == dosis*1 ){
			art = arArticulosSeleccionados[ pos ][0];
			break;
		}
		else if( arArticulosSeleccionados[ pos ][1]*1 > dosis*1 ){
			art = arArticulosSeleccionados[ pos ][0];
			break;
		}
		else{
			art = arArticulosSeleccionados[ pos ][0];
		}

		// 2012-12-14
		alert("Art. "+art);
	}
	*/


	document.getElementById( "trEncabezadoTbAddImp" ).style.display = '';

	//buscando el medicamento
	var mensaje = "";

	parametros = "consultaAjaxKardex=35&wemp_pmla="+document.forms.forma.wemp_pmla.value
				 +"&basedatos="+document.forms.forma.wbasedato.value
				 +"&cenmez="+document.forms.forma.wcenmez.value
				 +"&ccoPaciente="+document.forms.forma.wservicio.value
				 +"&q="+nombreFamilia
				 +"&pre="+presentacion
				 +"&med="+medida
				 +"&dos="+dosis
				 +"&adm="+administracion
				 +"&eps="+$( "#pacEPS" ).val()
				 +"&bsq="+bsqFamilia
				 +"&his="+document.forms.forma.whistoria.value
				 +"&ing="+document.forms.forma.wingreso.value;

	try{

		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		if( $.trim( ajax.responseText ) != '' ){
			if($.trim( ajax.responseText ) != "No se encontraron coincidencias"){

				var item = $.trim( ajax.responseText ).split( "|" );

//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));

				/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
				 * Argumentos:
				 *
				 * 0: Como se muestra en el autocomplete
				 * 1: Codigo del articulo
				 * 2: Nombre comercial del articulo
				 * 3: Nombre genérico del articulo
				 * 4: Tipo protocolo
				 * 5: (M)edicamento o (L)iquido
				 * 6: Es generico
				 * 7: Origen
				 * 8: Grupo de medicamento
				 * 9: Forma farmaceutica
				 * 10:Unidad
				 * 11:POS
				 * 12:Unidad de fraccion
				 * 13:Cantidad de fracciones
				 * 14:Vencimiento
				 * 15:Dias de estabilidad
				 * 16:Es dispensable
				 * 17:Es duplicable
				 * 18:Dias maximos sugeridos
				 * 19:Dosis maximas sugeridas
				 * 20:Via
				 * 21:Abre ventana parametrizada ----
				 * 22:Cantidad de dosis
				 * 23:Unidad de dosis
				 * 24:No enviar
				 * 25:Frecuencia
				 * 26:Vias de administracion
				 * 27:Condicion de suministro
				 * 28:Confirmada preparacion
				 * 29:Dias maximos tratamiento
				 * 30:Dosis maximas tratamiento
				 * 31:Observaciones adicionales
				 * 32:Componentes del tipo
				 * 33:Nombre personalizado del articulo
				 * 34:
				 * 35:Codigo principio activo
				 * 36:Descricion principio activo
				 * 37:esAntibiotico
				 * 38:InformacionNutriciones
				 */

				var codigoArticulo = item[1];
				var nombreComercial = item[2];
				var nombreGenerico = item[3];
				var tipoProtocolo = item[4];
				var tipoMedicamentoLiquido = item[5];
				var esGenerico = item[6];
				var origen = item[7];
				var grupoMedicamento = item[8];
				// 2012-08-02
				// Se cambia para que tome el valor seleccionado en la adición del medicamento y no el de la tabla de articulos
				var formaFarmaceutica = presentacion; //item[9];
				var unidadMedida = item[10];
				var pos = item[11];
				var unidadFraccion = item[12];
				var cantidadFraccion = dosis;	//item[13];
				var vencimiento = item[14];
				var diasEstabilidad = item[15];
				var dispensable = item[16];
				var duplicable = item[17];
				var diasMaximosSugeridos = item[18];
				var dosisMaximasSugeridas = item[19];
				var viaAdministracion = administracion;
				var abreVentanaFija = item[21];
				var cantidadDosisFija = item[22];
				var unidadDosisFija = medida;		//item[23];
				var noEnviarFija = item[24];
				var frecuenciaFija = periodo;
				var viasAdministracionFija = item[26];
				var condicionSuministroFija = condicion;
				var confirmadaPreparacionFija = item[28];
				var diasMaximosFija = diasTto;
				var dosisMaximasFija = dosisMax;
				var cantidadAltaFija = cantidadAlta;
				var observacionesFija = item[34]+observaciones;
				var componentesTipo = item[32];
				var nombrePersonalizadoDelArticulo = item[33];
				fechaInicioFija = fechaInicio;

				var codPrincipioActivo = item[34];
				var desPrincipioActivo = item[36];
				var esAntibiotico = $.trim( item[37] ) == 'on' ? true: false;
				var infoNutriciones = item[38];
				
				var esCompuesto 	= item[39];
				var conMedicamento1 = item[40];
				var conMedicamento2 = item[41];
				
				var familiaATC 		= item[42];
				
				var conTarifa 		= item[43];

				// if(dosis!=cantidadDosisFija)
				// {
					// if(codigoArticulo.substr(0,2)=='DA')
						// codigoArticulo = "DA0000";
					// if(codigoArticulo.substr(0,2)=='NU')
						// codigoArticulo = "NU0000";
					// if(codigoArticulo.substr(0,2)=='QT')
						// codigoArticulo = "QT0000";

					// // if(codigoArticulo.substr(0,2)=='DA' && dosis==1000)
						// // codigoArticulo = "DA0025";
				// }

				var	noEnviar = item[33];	//Abril 25 de 2011

				if( esGenerico.length > 0 ){
					duplicable = 'on';
				}

				var deAlta = "on";

				agregarArticulo( "detKardexAddImp", true );

				seleccionarArticulo(codigoArticulo,reemplazarTodo(nombreComercial," ","_"),reemplazarTodo(nombreGenerico," ","_"),origen,grupoMedicamento,formaFarmaceutica,unidadMedida,pos,unidadFraccion,cantidadFraccion,vencimiento,diasEstabilidad,dispensable,duplicable,diasMaximosSugeridos,dosisMaximasSugeridas,viaAdministracion,tipoProtocolo,tipoMedicamentoLiquido,esGenerico,abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija,condicionSuministroFija,confirmadaPreparacionFija,diasMaximosFija,cantidadAltaFija,observacionesFija,componentesTipo,noEnviar,deAlta,codPrincipioActivo,desPrincipioActivo,esAntibiotico,infoNutriciones,esCompuesto,conMedicamento1,conMedicamento2,false,familiaATC,conTarifa);

				document.getElementById("btnCerrarVentana").style.display = 'none';

				this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
			}
		}

		if (!estaEnProceso(ajax)) {
			ajax.send(null);
		}
	}catch(e){	}


	//Una vez agregado el medicamento borro los datos de la consulta
	var tbNuevoBuscador = document.getElementById( "nuevoBuscadorImp" );

	// document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
	// document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo

	/* 2012-11-02
	// Se comenta porque se va a obtener el elemento por el nombre
	// ya que la nueva estructura no permite obtenerlo por posición en la tabla
	tbNuevoBuscador.rows[1].cells[0].firstChild.value = '';				//Borro el valor de Nombre articulo
	tbNuevoBuscador.rows[1].cells[1].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la presentacion
	tbNuevoBuscador.rows[1].cells[2].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las unidades
	tbNuevoBuscador.rows[1].cells[3].firstChild.value = '';				//Quito el valor escrito de la dosis
	tbNuevoBuscador.rows[1].cells[4].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la frecuencia
	tbNuevoBuscador.rows[1].cells[5].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las via de administracion
	tbNuevoBuscador.rows[1].cells[7].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la condicion
	tbNuevoBuscador.rows[1].cells[8].firstChild.value = '';				//Quito el valor escrito de los dias de tratmiento
	tbNuevoBuscador.rows[1].cells[9].firstChild.value = '';				//Quito el valor escrito de la dosis maxima
	tbNuevoBuscador.rows[1].cells[10].firstChild.value = '';			//Quito el valor escrito de las observaciones
	*/

	//Quito los valores de los campos de busqueda y adición de medicamentos
	document.getElementById('wnombrefamiliaimp').value = '';
	document.getElementById('wpresentacionimp').selectedIndex = -1;
	document.getElementById('wunidadimp').selectedIndex = -1;
	document.getElementById('wdosisfamiliaimp').value = '';
	document.getElementById('wfrecuenciaimp').selectedIndex = -1;
	// document.getElementById('wadministracionimp').selectedIndex = -1;
	document.getElementById('wadministracionimp').value = '';
	// document.getElementById('wcondicionsumimp').selectedIndex = -1;
	document.getElementById('wcondicionsumimp').value = '';
	document.getElementById('wdiastratamientoimp').value = '';
	document.getElementById('wdosismaximaimp').value = '';
	document.getElementById('wcantidadaltaimp').value = '';
	document.getElementById('wtxtobservasionesimp').value = '';

	//document.getElementById('wprotocoloimp').value = '';

	//Quito los valores de la regleta de grabacion
	document.getElementById('dosisRonda2').value = '';
	document.getElementById('dosisRonda4').value = '';
	document.getElementById('dosisRonda6').value = '';
	document.getElementById('dosisRonda8').value = '';
	document.getElementById('dosisRonda10').value = '';
	document.getElementById('dosisRonda12').value = '';
	document.getElementById('dosisRonda14').value = '';
	document.getElementById('dosisRonda16').value = '';
	document.getElementById('dosisRonda18').value = '';
	document.getElementById('dosisRonda20').value = '';
	document.getElementById('dosisRonda22').value = '';
	document.getElementById('dosisRonda24').value = '';

	if(strPendientesCTC!="")
			abrirCTCMultiple();

	if(strPendientesModal!="")
		abrirModalMultiple();

	adicionMultiple = false;

	// 2013-01-21
	//tbNuevoBuscador.rows[1].cells[10].firstChild.onblur();
}




/****************************************************************************************************************
 * params indice:			Inidice del articulo original lev o inf que crea los insumos
 * params tipPro			Tipo de protocolo del articulo lev o inf que crea los insumos
 *
 *===============================================================================================================
 * Junio 02 de 2015
 * Nota: Para los LEV (liquidos endovenosos e INF infuciones
 *
 * Se crea un objeto JSON para manejar los articulos. Este objeto solo contendrá el tipo de protocolo
 * e indice de cada insumo agregado. Esto se hace ya que los articulos se encuentran en el script
 * y toda su información se sabe de acuerdo a estos datos. A continuación se explica el objeto JSON
 * que se crea:
 * Nivel 1: [INF] ó [LEV] 				: Indica que tipo de articulo.
 * Nivel 2: [ tipPro+Indice ]	: Indica el el articulo generico lev o inf que contiene los insumos
 * Nivel 3: []							: Contiene toda la inforamción faltante de los insumos LEV o INF
 *
 * El detalle del nivel 3 es:
 * ins									: Insumo
 * ele									: Indica si es electrolito o no
 * vel									: Volumen del electrolito
 * vdi									: Volumen de dilución
 * vto									: Volumen total
 * tip 									: tipo, puede ser LEV o INF
 * idx									: Indice
 ****************************************************************************************************************/
function eleccionMedicamentosInsumos( strInsumos, indice, tipPro, tipo )
{
	//El tipo por defecto es LEV
	var contLEV = 'listaComponentesLEV';
	// if( !tipo ){
		// tipo = "LEV";
	// }

	if( tipo == "IC" ){
		contLEV = 'listaComponentesIC';
	}
	else{
		tipo = "LEV";
	}

	// Se reviza si hay algun detalle de familia desplegado y se oculta
	ocultarDetalleFliaAnterior();

	var articulo;
	var periodo;
	var administracion;
	var condicion;
	var dosis;
	var observaciones;
	var presentacion;
	var medida;
	var fechaInicio;
	var dosisMax;
	var diasTto;
	var insumoDispensable;


	// document.getElementById( "trEncabezadoTbAdd" ).style.display = '';
	// $( "#trEncabezadoTbAdd", $( "#tbDetalleAddDA" )).css( {display: '' } );

	//buscando el medicamento
	var parametros = "";
	var mensaje = "";

	var filasInsumo = strInsumos.split( "\r\n" );

	multiplesMedicamentos = [];

	//var codigoArticulo = '';
	for (var ipro=0;ipro<filasInsumo.length-1;ipro++)
	{

		var arrInsumo = filasInsumo[ipro].split( " - " );

		//Si existe el insumo en el lev no lo agrego
		var existeInsumoLevIc = existeInsumoParaLEVIC( tipPro+indice, arrInsumo[0] );
		if( existeInsumoLevIc )
			continue;

		articulo = arrInsumo[0];				// Dosis a aplicar
		periodo = periodoLQ;				// Frecuencias
		administracion = administracionLQ;		// Vías de administración
		condicion = condicionLQ;				// Condiciones de suministro
		if( $.trim( condicion ) == "" )
			condicion = ( tipo == "IC" ) ? $( "#condicionIC" ).val(): $( "#condicionLEV" ).val();					// Condiciones de suministro Siempre se deja está condición por defecto
		dosis = arrInsumo[1];				// Dosis
		observaciones = observacionesLQ;			// Observaciones
		insumoDispensable = arrInsumo[2];
		presentacion = '';			// Presentaciones o formas farmacéuticas
		medida = '';						// Unidades de medida
		fechaInicio = fechaInicioLQ;	// Fecha y hora de inicio de aplicación
		dosisMax = dosisMaxLQ;				// Dosis a aplicar
		diasTto = diasTtoLQ;			// Días de tratamiento

		if( tipo == "IC" ){
			observaciones = $( "#txObservacionesIC", $( "#"+contLEV ) ).val();
			dosisMax = $( "#inDmaxIC", $( "#"+contLEV ) ).val();			// Dosis a aplicar
			diasTto  = $( "#inDttoIC", $( "#"+contLEV ) ).val();			// Días de tratamiento
		}
		else{
			observaciones = $( "#txObservacionesLEV", $( "#"+contLEV ) ).val();
			dosisMax = $( "#inDmaxLEV", $( "#"+contLEV ) ).val();			// Dosis a aplicar
			diasTto  = $( "#inDttoLEV", $( "#"+contLEV ) ).val();			// Días de tratamiento
		}

		/************************************************************************************************************
		 * Si el articulo tiene volumen por dilución significa que hay que recalcular la dosis
		 * La dosis es la requerida por día
		 ************************************************************************************************************/
		var sumDosis = 0;
		if( arrInsumo[3] != "" ){
			for( var ix = 0; ix < filasInsumo.length-1; ix++ ){
				if( ix != ipro ){	//Si no es el mismo elemento

					var aIns = filasInsumo[ix].split( " - " );

					if( aIns[5] == "sol" && valFrecuencia[ periodo ]*1 > 0 ){	//Si no es un electrolito se hace el calculo
						sumDosis += aIns[1]/arrInsumo[3]*dosis;//*24/valFrecuencia[ periodo ];
					}
				}
			}
		}

		if( sumDosis > 0 ){
			dosis = sumDosis;
			// periodo = 'C24';
		}
		/************************************************************************************************************/

		/**************************************************************************************************
		 * Creo un JSON para guardar los datos de los medicamentos LEV
		 * Despues de grabar los medicamentos se recorre este objeto y se guarda los datos
		 **************************************************************************************************/
		var idxCompEle = $( "[ele"+arrInsumo[0]+"]", $( "#listaComponentesIC") ).attr( "ele"+arrInsumo[0] );
		var idxCompSol = $( "[sol"+arrInsumo[0]+"]", $( "#listaComponentesIC") ).attr( "sol"+arrInsumo[0] );

		if( arrInsumo[4] != '' ){
			periodo = arrInsumo[4];
		}

		var valIdoLev = "";
		if( $( "#widoriginal"+tipPro+indice ).length>0 ){
			valIdoLev = $( "#widoriginal"+tipPro+indice ).val();
		}

		if( !artLevs[ "LQ"+parseInt(elementosLev*1+ipro*1) ] )
			artLevs[ "LQ"+parseInt(elementosLev*1+ipro*1) ] = {};

		artLevs[ "LQ"+parseInt(elementosLev*1+ipro*1) ] = {
			codLev: tipPro+indice,
			idoLev: valIdoLev,												//Ido del LEV
			insCod: arrInsumo[0],									//Códio del insumo
			insIdi: '',												//Ido del insumo
			insEle: arrInsumo[5] == 'ele' ? 'on': 'off',			//Es Electrolito/Medicamento?
			insVel: arrInsumo[5] == 'ele' ? arrInsumo[1]: '',		//Volumen del electrolito
			insVdi: arrInsumo[5] == 'ele' ? arrInsumo[3]: '',		//Volumen por dilucion
			insFso: arrInsumo[5] == 'ele' ? '': arrInsumo[4],		//Frecuencia de la solucion
			insVto: arrInsumo[5] == 'ele' ? '': arrInsumo[1],		//Volumen total de la solución
			insFdi: $( "#slFrecDilLev", $( "#"+contLEV ) ).val(),	//Frecuencia de dilucion
			insVfd: $( "#txFrecDilLev", $( "#"+contLEV ) ).val(),	//Valor de la frecuencia de dilución
			insInf: ( tipo == "IC" ) ? "on" : "off",				//Indica si es infusion o lev

			insFre: periodoLQ,										//Frecuencia
			insFin: $.trim( fechaInicio.split( " a las:" )[0] ),	//Fecha de inicio
			insHin: $.trim( fechaInicio.split( " a las:" )[1]+':00' ).substr(0,8),	//Hora de incicio

			insDca: ( tipo == "IC" ) ? $( "#inICDca"+idxCompEle, $( "#"+contLEV ) ).val() : "",	//Indica si es infusion o lev
			insCdc: ( tipo == "IC" ) ? $( "#slICUdca"+idxCompEle, $( "#"+contLEV ) ).val() : "",	//Indica si es infusion o lev

			insObs: ( tipo == "IC" ) ? $( "#txObservacionesIC", $( "#"+contLEV ) ).val() : $( "#txObservacionesLEV", $( "#"+contLEV ) ).val(),	//Indica si es infusion o lev

			insEst: 'new',											//Estado ( new, mod, gra, del )

			sinSol:	( filasInsumo.length-1 == 1 && tipo == 'IC' ) ? 'on' : 'off'	//Sin solución
		}
		/*************************************************************************************************/



		parametros = "consultaAjaxKardex=36&wemp_pmla="+document.forms.forma.wemp_pmla.value
					 +"&basedatos="+document.forms.forma.wbasedato.value
					 +"&cenmez="+document.forms.forma.wcenmez.value
					 +"&ccoPaciente="+document.forms.forma.wservicio.value
					 +"&q="+articulo
					 +"&dos="+dosis
					 +"&adm="+administracion;


		try{

			ajax=nuevoAjax();

			ajax.open("POST", "ordenes.inc.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			if( $.trim( ajax.responseText ) != '' ){
				if($.trim( ajax.responseText ) != "No se encontraron coincidencias"){

					var item = $.trim( ajax.responseText ).split( "|" );
	//	    		seleccionarComponente("",reemplazarTodo(item," ","_"));

					/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
					 * Argumentos:
					 *
					 * 0: Como se muestra en el autocomplete
					 * 1: Codigo del articulo
					 * 2: Nombre comercial del articulo
					 * 3: Nombre genérico del articulo
					 * 4: Tipo protocolo
					 * 5: (M)edicamento o (L)iquido
					 * 6: Es generico
					 * 7: Origen
					 * 8: Grupo de medicamento
					 * 9: Forma farmaceutica
					 * 10:Unidad
					 * 11:POS
					 * 12:Unidad de fraccion
					 * 13:Cantidad de fracciones
					 * 14:Vencimiento
					 * 15:Dias de estabilidad
					 * 16:Es dispensable
					 * 17:Es duplicable
					 * 18:Dias maximos sugeridos
					 * 19:Dosis maximas sugeridas
					 * 20:Via
					 * 21:Abre ventana parametrizada ----
					 * 22:Cantidad de dosis
					 * 23:Unidad de dosis
					 * 24:No enviar
					 * 25:Frecuencia
					 * 26:Vias de administracion
					 * 27:Condicion de suministro
					 * 28:Confirmada preparacion
					 * 29:Dias maximos tratamiento
					 * 30:Dosis maximas tratamiento
					 * 31:Observaciones adicionales
					 * 32:Componentes del tipo
					 * 33:Nombre personalizado del articulo
					 */

					//Diciembre 04 de 2015
					var viasDeAdministracionMed = item[20].toUpperCase().split(',');

					var viaAdministracion = viasDeAdministracionMed[0];
					if( $.inArray( administracionLQ.toUpperCase(), viasDeAdministracionMed ) >= 0 ){
						viaAdministracion = administracionLQ.toUpperCase();
					}

					// Si el insumo esta marcado como no dispensable se marca como no enviar
					if(trim(insumoDispensable)=="" || trim(insumoDispensable)=="off")
						item[24] = "on";

					var codigoArticulo = item[1];
					var nombreComercial = item[2];
					var nombreGenerico = item[3];
					var tipoProtocolo = item[4];
					var tipoProtocolo = 'LQ';
					var tipoMedicamentoLiquido = 'M';
					var esGenerico = item[6];
					var origen = item[7];
					var grupoMedicamento = item[8];
					var formaFarmaceutica = $.trim(item[9]);
					var unidadMedida = item[10];
					var pos = item[11];
					var unidadFraccion = item[12];
					var cantidadFraccion = dosis;	//item[13];
					var vencimiento = item[14];
					var diasEstabilidad = item[15];
					var dispensable = item[16];
					var duplicable = item[17];
					var diasMaximosSugeridos = item[18];
					var dosisMaximasSugeridas = item[19];
					//var viaAdministracion = administracion;
					var abreVentanaFija = 'N';
					var cantidadDosisFija = item[22];
					var unidadDosisFija = item[23];
					var noEnviarFija = item[24];
					var frecuenciaFija = periodo;
					var viasAdministracionFija = item[26];
					var condicionSuministroFija = condicion;
					var confirmadaPreparacionFija = item[28];
					var diasMaximosFija = diasTto;
					var dosisMaximasFija = dosisMax;
					var observacionesFija = $.trim( item[34]+observaciones );
					var componentesTipo = item[32];
					var nombrePersonalizadoDelArticulo = item[33];
					fechaInicioFija = fechaInicio;

					var codPrincipioActivo = item[34];
					var desPrincipioActivo = $.trim(item[36]);
					var esAntibiotico = $.trim( item[37] ) == 'on' ? true: false;

					var	noEnviar = item[33];	//Abril 25 de 2011

					if( esGenerico.length > 0 ){
						duplicable = 'on';
					}








					var idxMulArt = multiplesMedicamentos.length;
					multiplesMedicamentos[idxMulArt] = [];
					var idxMultArt2 = 0
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codigoArticulo;	//0
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	reemplazarTodo(nombreComercial," ","_");	//1
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	reemplazarTodo(nombreGenerico," ","_");	//2
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	origen;	//3
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	grupoMedicamento;	//4
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	formaFarmaceutica;	//5
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadMedida;	//6
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	pos;	//7
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadFraccion;	//8
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	cantidadFraccion;	//9
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	vencimiento;	//10
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasEstabilidad;	//11
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dispensable;	//12
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	duplicable;	//13
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasMaximosSugeridos;	//14
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dosisMaximasSugeridas;	//15
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	viaAdministracion;	//16
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoProtocolo;	//17
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	tipoMedicamentoLiquido;	//18
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esGenerico;	//19
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	abreVentanaFija;	//20
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	cantidadDosisFija;	//21
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	unidadDosisFija;	//22
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	noEnviarFija;	//23
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	frecuenciaFija;	//24
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	viasAdministracionFija;	//25
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	condicionSuministroFija;	//26
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	confirmadaPreparacionFija;	//27
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	diasMaximosFija;	//28
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	dosisMaximasFija;	//29
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	observacionesFija;	//30
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	componentesTipo;	//31
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	noEnviar;	//32
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	'off';	//33
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	codPrincipioActivo;	//34
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	desPrincipioActivo;	//35
					multiplesMedicamentos[idxMulArt][idxMultArt2++] = 	esAntibiotico;	//36

					//Agrego la fecha y hora de inicio del medicamento calculada para el H.E
					multiplesMedicamentos[idxMulArt].fechaInicioFija = 	fechaInicioFija;

					//Se agrega esta propiedad para saber que tipo de articulo es
					multiplesMedicamentos[idxMulArt].tipoArt 		= 	'LEVIC';
					multiplesMedicamentos[idxMulArt].tipPro			= 	tipPro;
					multiplesMedicamentos[idxMulArt].indice 		= 	indice;
					multiplesMedicamentos[idxMulArt].tipo 			= 	tipo;
					multiplesMedicamentos[idxMulArt].observaciones	= 	observaciones;
					multiplesMedicamentos[idxMulArt].nombreFamilia	= 	$( "#wnombrefamilia" ).val();
				}
			}

			if (!estaEnProceso(ajax)) {
				ajax.send(null);
			}
		}catch(e){	}

	}

	//Una vez agregado el medicamento borro los datos de la consulta
	var tbNuevoBuscador = document.getElementById( "nuevoBuscador" );

	document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa
	document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo

	/* 2012-11-02
	// Se comenta porque se va a obtener el elemento por el nombre
	// ya que la nueva estructura no permite obtenerlo por posición en la tabla
	tbNuevoBuscador.rows[1].cells[0].firstChild.value = '';				//Borro el valor de Nombre articulo
	tbNuevoBuscador.rows[1].cells[1].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la presentacion
	tbNuevoBuscador.rows[1].cells[2].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las unidades
	tbNuevoBuscador.rows[1].cells[3].firstChild.value = '';				//Quito el valor escrito de la dosis
	tbNuevoBuscador.rows[1].cells[4].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la frecuencia
	tbNuevoBuscador.rows[1].cells[5].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de las via de administracion
	tbNuevoBuscador.rows[1].cells[7].firstChild.selectedIndex = -1;		//Quito el valor seleccionado de la condicion
	tbNuevoBuscador.rows[1].cells[8].firstChild.value = '';				//Quito el valor escrito de los dias de tratmiento
	tbNuevoBuscador.rows[1].cells[9].firstChild.value = '';				//Quito el valor escrito de la dosis maxima
	tbNuevoBuscador.rows[1].cells[10].firstChild.value = '';			//Quito el valor escrito de las observaciones
	*/

	//Quito los valores de los campos de busqueda y adición de medicamentos
	document.getElementById('wnombrefamilia').value = '';
	document.getElementById('wpresentacion').selectedIndex = -1;
	document.getElementById('wunidad').selectedIndex = -1;
	document.getElementById('wdosisfamilia').value = '';
	document.getElementById('wfrecuencia').selectedIndex = -1;
	document.getElementById('wadministracion').selectedIndex = -1;
	document.getElementById('wcondicionsum').selectedIndex = -1;
	document.getElementById('wdiastratamiento').value = '';
	document.getElementById('wdosismaxima').value = '';
	document.getElementById('wtxtobservasiones').value = '';
try{
	document.getElementById('wprotocolo').value = '';
}catch(e){}

	//Quito los valores de la regleta de grabacion
	document.getElementById('dosisRonda2').value = '';
	document.getElementById('dosisRonda4').value = '';
	document.getElementById('dosisRonda6').value = '';
	document.getElementById('dosisRonda8').value = '';
	document.getElementById('dosisRonda10').value = '';
	document.getElementById('dosisRonda12').value = '';
	document.getElementById('dosisRonda14').value = '';
	document.getElementById('dosisRonda16').value = '';
	document.getElementById('dosisRonda18').value = '';
	document.getElementById('dosisRonda20').value = '';
	document.getElementById('dosisRonda22').value = '';
	document.getElementById('dosisRonda24').value = '';

	if(strPendientesCTC!="")
			abrirCTCMultiple();

	if(strPendientesModal!="")
		abrirModalMultiple();

	adicionMultiple = false;

	// 2013-01-21
	//tbNuevoBuscador.rows[1].cells[10].firstChild.onblur();
}



// Variables globales que ayudará a llevar la sección anterior desplegada
var filaIdAnt = "";
var divIdAnt = "";
var filaTituloIdAnt = "";
var claseFilaAnt = "";

// 2012-11-08
/*************************************************************************
* Función que permite desplegar el detalle de la familia de medicamentos *
**************************************************************************/
function mostrarDetalleFlia( campo, filaId, divId, filaTituloId, claseFila )
{
	if (document.getElementById)
	{
		//se obtiene el id de la fila que contiene el detalle de la familia de medicamentos
		var el = document.getElementById(filaId);

		if(el.style.display == 'none')
		{

			if(filaIdAnt != "" && document.getElementById(filaIdAnt).display != 'none')
			{
				// Se oculta la fila desplegada anteriormente
				document.getElementById(filaIdAnt).style.display = 'none';

				// Se reestablece la clase original de la fila anterior
				document.getElementById(filaTituloIdAnt).className = claseFilaAnt;
			}

			if(divIdAnt != "" && document.getElementById(divIdAnt).display != 'none')
			{
				// Se oculta el div desplegado anteriormente
				document.getElementById(divIdAnt).style.display = 'none';
			}

			// Se le asigna una clase especial a la fila del titulo de la familia de medicamentos
			// Esto para que cambie de color cuando el detalle de la familia está desplegado
			document.getElementById(filaTituloId).className = 'tituloFamilia';

			// Despliega el detalle de la familia de medicamentos
			el.style.display = '';

			// Se obtiene el <div> que muestra el detalle de la familia de medicamentos
			var divTitle = document.getElementById(divId);

			// Despliega el <div> que muestra el detalle de la familia de medicamentos
			divTitle.style.display = '';
			divTitle.style.position = 'absolute';

			// Espacio entre el encabezado y el detalle de la familia cuando se despliega
			divTitle.style.top = parseInt( findPosY(campo) )+ parseInt( campo.offsetHeight ) + 4;
			divTitle.style.left = 0; //findPosX( campo );

			// Define el alto de la fila que contiene el detalle de la familia de medicamentos
			divTitle.parentNode.style.height = divTitle.offsetHeight;

			// Defino valores de la ultima fila desplegada para cuando se despliegue
			// una nueva poder ocultar esta que pasa a ser la anterior
			filaIdAnt = filaId;
			divIdAnt = divId;
			claseFilaAnt = claseFila;
			filaTituloIdAnt = filaTituloId;
		}
		else
		{
			// Se reestablece la clase original de la fila del titulo de la familia de medicamentos
			document.getElementById(filaTituloId).className = claseFila;

			// Se oculta el div del detalle de la familia de medicamentos
			el.style.display = 'none';

			// Se oculta el div que muestra el detalle de la familia de medicamentos
			var divTitle = document.getElementById(divId);
			divTitle.style.display = 'none';

		}
	}
}

// 2012-11-16
/*************************************************************
* Función que oculta el último detalle de familia desplegado *
**************************************************************/
function ocultarDetalleFliaAnterior( )
{
	if(filaIdAnt != "" && document.getElementById(filaIdAnt).display != 'none')
	{
		// Se oculta la fila desplegada anteriormente
		document.getElementById(filaIdAnt).style.display = 'none';

		// Se reestablece la clase original de la fila anterior
		document.getElementById(filaTituloIdAnt).className = claseFilaAnt;
	}

	if(divIdAnt != "" && document.getElementById(divIdAnt).display != 'none')
	{
		// Se oculta el div desplegado anteriormente
		document.getElementById(divIdAnt).style.display = 'none';
	}
}

/************************************************************
 * Crea options nuevos para una selecion
 *
 * @param slCampos	Como tipo select
 * @param opciones	Array
 * @return
 ************************************************************/
function creandoOptions( slCampos, opciones ){

	//options debe ser un array
	if( slCampos.tagName.toLowerCase() == "select" ){

		//Borrando los options anteriores
		var numOptions = slCampos.options.length;

		for( var i = 0; i <  numOptions; i++ ){
			slCampos.removeChild( slCampos.options[0] );
		}

		//agrengando options
		for( var i = 0; i < opciones.length; i++ ){
			var auxOpt = document.createElement( "option" );
			slCampos.options.add( auxOpt, 0 );
			auxOpt.innerHTML = opciones[i];

			slCampos.options.selectedIndex = 0;
		}
	}
}

/************************************************************
 * Crea options nuevos para una selecion
 * Se diferencia de la anterior en que aca se establece
 * un valor para el parametro value
 *
 * @param slCampos	Como tipo select
 * @param opciones	Array
 * @return
 ************************************************************/
function creandoOptionsValue( slCampos, opciones ){

	//options debe ser un array
	if( slCampos.tagName.toLowerCase() == "select" ){

		//Borrando los options anteriores
		var numOptions = slCampos.options.length;

		for( var i = 0; i <  numOptions; i++ ){
			slCampos.removeChild( slCampos.options[0] );
		}

		//agrengando options
		for( var i = 0; i < opciones.length; i++ ){
			var auxOpt = document.createElement( "option" );
			var valorOpt = opciones[i].split("-");
			auxOpt.value = valorOpt[0];
			slCampos.options.add( auxOpt, 0 );
			auxOpt.innerHTML = valorOpt[1];
			//alert(opciones[i]);

			slCampos.options.selectedIndex = 0;
		}
	}
}

var bsqFamilia = ''; //Lo que se escribio para buscar la familia
/************************************************************************************************************************
 * Autocompletar para la busqueda de medicamentos por familias
 * @return
 ************************************************************************************************************************/
function autocompletarParaBusqueMedicamentosPorFamilia(){

	// Se limpia cache del campo
	$("#wnombrefamilia").flushCache();
	$("#wnombrefamilia").unbind("result");

	// Se hace el llamado AJAX para traer los resultados a la función autocomplete
	$("#wnombrefamilia").setOptions({url:"ordenes.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value+"&historia="+document.forms.forma.whistoria.value+"&ingreso="+document.forms.forma.wingreso.value });

	$("#wnombrefamilia").autocomplete("ordenes.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value+"&historia="+document.forms.forma.whistoria.value+"&ingreso="+document.forms.forma.wingreso.value, {
		cacheLength:0,
		delay:0,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		minChars: 2,
		autoFill:false,
		formatResult: function(data, value){
			return value;
		}
	}).result(function(event, item) {

		//creando array de resultados
		var arDosis = new Array();
		var arUnidades = new Array();
		var arPresentaciones = new Array();
		var arViasAdmon = new Array();
		var arArticulos = new Array();

		// Indices para los diferentes arrays
		var j = -1;
		var k = 0;
		var l = -1;
		var m = 0;
		var n = -1;
		var p = 0;
		var q = -1;
		var r = 0;

		// Arrays de relación con los articulos
		var arRelDosis = new Array();
		var arRelUnidad = new Array();
		var arRelPresentacion = new Array();
		var arRelViaAdmon = new Array();
		
		var esAlergico = false;
		var desAlergico = "";

		esLiquidoEndovenoso = false;
		esNPT = false;
		
		agregandoArticuloPorDextrometer = false;

		for( var i = 0; i < item.length; i++ ){

			// Si viene precedido de @ es unidad y se asigna al array arUnidades
			if( item[i].substr(0,1) == "@" ){
				codigoUnidad = trim(item[i].substr(1));
				arUnidades[ arUnidades.length ] = codigoUnidad+"-"+item[++i];
				j++;
				k = 0;
			}

			// Si viene precedido de & es presentacion (forma farmaceutica) y se asigna al array arPresentaciones
			if( item[i].substr(0,1) == "&" ){
				codigoFtica = trim(item[i].substr(1));
				arPresentaciones[ arPresentaciones.length ] = codigoFtica+"-"+item[++i];
				l++;
				m = 0;
			}

			// Si viene precedido de # es via de administracion y se asigna al array arViasAdmon
			if( item[i].substr(0,1) == "#" ){
				codigoVia = trim(item[i].substr(1));
				arViasAdmon[ arViasAdmon.length ] = codigoVia+"-"+item[++i];
				n++;
				p = 0;
			}

			// Si viene precedido de $ es dosis y se asigna al array arDosis
			if( item[i].substr(0,1) == "$" ){
				valorDosis = trim(item[i].substr(1));
				arDosis[ arDosis.length ] = valorDosis;
				q++;
				r = 0;
			}

			// Si viene precedido de - es artículo, comienza la asignación de los arreglos de relación y del arreglo arArticulos
			if( item[i].substr(0,1) == "-" ){

				var pos = arArticulos.length;

				if( !arRelUnidad[j] ){
					arRelUnidad[j] = new Array();
				}

				if( !arRelPresentacion[l] ){
					arRelPresentacion[l] = new Array();
				}

				if( !arRelViaAdmon[n] ){
					arRelViaAdmon[n] = new Array();
				}

				if( !arRelDosis[q] ){
					arRelDosis[q] = new Array();
				}

				if( !arArticulos[ pos ] ){
					arArticulos[ pos ] = new Array();
					arArticulos[ pos ][0] = "";
					arArticulos[ pos ][1] = "";
				}

				arRelUnidad[j][k] = arArticulos.length;
				arRelPresentacion[l][m] = arArticulos.length;
				arRelViaAdmon[n][p] = arArticulos.length;
				arRelDosis[q][r] = arArticulos.length;
				arArticulos[ pos ][0] = item[i++].substr( 1 );
				arArticulos[ pos ][1] = item[i];
				k++;
				m++;
				p++;
				r++;

				if(arArticulos[ pos ][0]=='LQ0000' || arArticulos[ pos ][0]=='IC0000' )
					esLiquidoEndovenoso = true;
				
				if(arArticulos[ pos ][0]=='NU0000')
				{
					esNPT = true;
				}	
			}

			//Esto fue lo que se escribio para la busqueda de la familia
			// Se guarda en una variable para buscar el medicamento correcto de acuerdo
			//al nombre generico o comercial según corresponda
			if( item[i].substr(0,1) == "<" ){
				bsqFamilia = item[i].substr(1);
				esAlergico = bsqFamilia.split("--")[1];
				desAlergico = bsqFamilia.split("--")[2];
				bsqFamilia = bsqFamilia.split("--")[0];
			}
		}

		//Si es alergico no dejo escoger la familia
		if( esAlergico == 1 ){
			jAlert( "El paciente es alergico a <b>"+desAlergico+"</b>. Por tanto no se agregar&aacute; el art&iacute;culo", "ALERTA" );
			$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
			this.value = '';
			return;
		}
		else{
			if( desAlergico != '' ){
				jAlert( desAlergico, "ALERTA" );
				$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
			}
		}
		
		// 2012-12-14
		// alert("autocompletarParaBusqueMedicamentosPorFamilia: arRelUnidad="+arRelUnidad.toSource());
		// alert(arUnidades.toSource());
		// alert(arRelPresentacion.toSource());
		// alert(arPresentaciones.toSource());
		// alert(arArticulos.toSource());

		// 2012-07-09
		// Se adiciona seleccione al select de presentación de medicamentos
		//arUnidades[ arUnidades.length ] = "-seleccione-";

		/////////////////////////////////////////////////////////////////////////////////////////
		// Se reestablece valores vacio para los campos que hayan sido llenados
		document.getElementById( "regletaGrabacion" ).style.display = 'none';	//Oculto la regleta de grabación si está activa

		// if(!esLiquidoEndovenoso)
		// {
			// document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo
			// document.getElementById( "wdosisfamilia" ).value = '';
		// }
		// else
		// {
			// document.getElementById( "wdosisfamilia" ).value = 1;			//Desactivo el campo de dosis
			// document.getElementById( "wdosisfamilia" ).disabled = true;			//Desactivo el campo de dosis
		// }
		if(esLiquidoEndovenoso)
		{
			document.getElementById( "wdosisfamilia" ).value = 1;			//Desactivo el campo de dosis
			document.getElementById( "wdosisfamilia" ).disabled = true;			//Desactivo el campo de dosis
			
		}
		else if(esNPT)
		{
			document.getElementById( "wdosisfamilia" ).value = 1;			//Desactivo el campo de dosis
			document.getElementById( "wdosisfamilia" ).disabled = false;			//Desactivo el campo de dosis
			
		}
		else
		{
			document.getElementById( "wdosisfamilia" ).disabled = false;			//Activo el campo de dosis si está inactivo
			document.getElementById( "wdosisfamilia" ).value = '';
		}

		//Quito los valores de los campos de busqueda y adición de medicamentos
		document.getElementById('wpresentacion').selectedIndex = -1;
		document.getElementById('wunidad').selectedIndex = -1;
		document.getElementById('wfrecuencia').selectedIndex = -1;
		document.getElementById('wadministracion').selectedIndex = -1;
		document.getElementById('wcondicionsum').selectedIndex = -1;
		document.getElementById('wdiastratamiento').value = '';
		document.getElementById('wdosismaxima').value = '';
		document.getElementById('wtxtobservasiones').value = '';

		//Quito los valores de la regleta de grabacion
		document.getElementById('dosisRonda2').value = '';
		document.getElementById('dosisRonda4').value = '';
		document.getElementById('dosisRonda6').value = '';
		document.getElementById('dosisRonda8').value = '';
		document.getElementById('dosisRonda10').value = '';
		document.getElementById('dosisRonda12').value = '';
		document.getElementById('dosisRonda14').value = '';
		document.getElementById('dosisRonda16').value = '';
		document.getElementById('dosisRonda18').value = '';
		document.getElementById('dosisRonda20').value = '';
		document.getElementById('dosisRonda22').value = '';
		document.getElementById('dosisRonda24').value = '';
		/////////////////////////////////////////////////////////////////////////////////////////

		// Quedan definidos los arrays derelación y de artículos que también se usaran en otras funciones
		arRelacionesUnidad = arRelUnidad;
		arRelacionesPresentacion = arRelPresentacion;
		arRelacionesViaAdmon = arRelViaAdmon;
		arArticulosSeleccionados = arArticulos;

		// 2012-12-14
		// alert("autocompletarParaBusqueMedicamentosPorFamilia: arRelacionesUnidad="+arRelacionesUnidad.toSource());

		// Se asigna dinámicamente las opciones a los selects wunidad, wpresentacion, wadministracion
		creandoOptionsValue( document.getElementById( "wunidad" ), arUnidades.reverse() );
		creandoOptionsValue( document.getElementById( "wpresentacion" ), arPresentaciones.reverse() );
		creandoOptionsValue( document.getElementById( "wadministracion" ), arViasAdmon.reverse() );

		// 2012-07-09
		// Para que muestre por defecto el select vacio
		if(arUnidades.length>1)
			document.getElementById( "wunidad" ).options.selectedIndex = -1;

		if(arPresentaciones.length>1)
			document.getElementById( "wpresentacion" ).options.selectedIndex = -1;

		if(arViasAdmon.length>1)
			document.getElementById( "wadministracion" ).options.selectedIndex = -1;

		//Si solo hay una presentación para escoger ejecuto la función
		if(arPresentaciones.length == 1){
			filtrarMedicamentosPorCampo('presentacion');
		}

		// Se reviza si hay algun detalle de fmailia desplegado y se oculta
		ocultarDetalleFliaAnterior();

		//creando array de medicamentos

		if( porLevIC != '' ){

			$( "#wcondicionsum" ).val( ( porLevIC == "IC" ) ? $( "#condicionIC" ).val(): $( "#condicionLEV" ).val() );

			agregarDosisMaxPorCondicion( '', '', $( "#wcondicionsum" )[0] );

			if ( porLevIC == "IC" )
				$( "#wfrecuencia" ).val( $( "#frecIC" ).val() );
			else
				$( "#wfrecuencia" ).val( $( "#frecLev" ).val() );

			eleccionMedicamento();
		}
		porLevIC = '';

		//Nutricion parenteral
		if(esNPT)
		{
			$( "#wfrecuencia" ).val( $( "#frecuenciaNPT" ).val() );
			$( "#wcondicionsum" ).val( $( "#condicionNPT" ).val() );
			eleccionMedicamento();
		}
		
		if( articuloPorDextrometer ){
			
			articuloPorDextrometer = false;

			var artIns  = $( "#wdexins" );

			if( $( "#wadministracion option" ).length > 1 ){
				var viasdex = $( "option[value="+artIns.val()+"]", artIns ).data('vias').split( "," );
				$( "#wadministracion" ).val( viasdex[0] );
			}

			var dexFrecuencia = $( "#wdexfrecuencia" );
			var frecuencia = dexFrecuencia.val();

			try{
				var condex = $( "option[value="+frecuencia+"]", dexFrecuencia ).data('condicion');
				if( condex && condex != '' ){
					$( "#wcondicionsum" ).val( condex );
				}
			}
			catch(e){}

			//Se calcula la dosis, esta es la menor mayor a 0 del esquema
			var contDosisDextrometer =  $( "#cntEsquemaActual table" );
			if( contDosisDextrometer.length == 0 )
				var contDosisDextrometer =  $( "#cntEsquema table" );
			
			var dosis = 0;
			$( "[id^=wdexint]", contDosisDextrometer ).filter( "[value!=0]" ).each(function(x){
				if( x > 0 ){
					if( this.value*1 < dosis )
						dosis = this.value*1;
				}
				else{
					dosis = this.value*1;
				}
			});


			//Agregando dosis por defecto
			$( "#wdosisfamilia" ).val( dosis );

			//Agrego la condicion en caso de haber
			var condDex = dexFrecuencia.data( "condicion" );
			if( condDex && condDex != '' )
				$( "#wcondicionsum" ).val( condDex );

			//Agregando frecuencia por defecto
			//Para ello agrego la frecuencia ya que no existe en las frecuencias del buscador de articulos
			//Luego de agregar la frecuencia hay que eliminarla del buscador
			var optionFre = $( "option[value="+frecuencia+"]", dexFrecuencia ).clone();
			$( "#wfrecuencia" ).append( optionFre );
			$( "#wfrecuencia" ).val( frecuencia );
			try{
				//variable global que indica que el articulo es agregado del dextrometer
				agregandoArticuloPorDextrometer = true;
				
				eleccionMedicamento();
				optionFre.remove();
			}
			catch(e){
				//Elimino la frecuencia que se agrego en el buscador
				optionFre.remove();
			}
		}

	});
}


/************************************************************************************************************************
 * Autocompletar para la busqueda de medicamentos por familias en la pestaña Alta
 * @return
 ************************************************************************************************************************/
function autocompletarParaBusqueMedicamentosPorFamiliaAlta(){


	// Se limpia cache del campo
	$("#wnombrefamiliaimp").flushCache();
	$("#wnombrefamiliaimp").unbind("result");

	// Se hace el llamado AJAX para traer los resultados a la función autocomplete
	$("#wnombrefamiliaimp").setOptions({url:"ordenes.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value+"&historia="+document.forms.forma.whistoria.value+"&ingreso="+document.forms.forma.wingreso.value });

	$("#wnombrefamiliaimp").autocomplete("ordenes.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.getElementById('wcenmez').value+"&historia="+document.forms.forma.whistoria.value+"&ingreso="+document.forms.forma.wingreso.value, {
		cacheLength:0,
		delay:0,
		max: 100,
		scroll: false,
		scrollHeight: 500,
		matchSubset: false,
		matchContains: true,
		width:1000,
		minChars: 2,
		autoFill:false,
		formatResult: function(data, value){
			return value;
		}
	}).result(function(event, item) {

		//creando array de resultados
		var arDosis = new Array();
		var arUnidades = new Array();
		var arPresentaciones = new Array();
		var arViasAdmon = new Array();
		var arArticulos = new Array();

		// Indices para los diferentes arrays
		var j = -1;
		var k = 0;
		var l = -1;
		var m = 0;
		var n = -1;
		var p = 0;
		var q = -1;
		var r = 0;

		// Arrays de relación con los articulos
		var arRelDosis = new Array();
		var arRelUnidad = new Array();
		var arRelPresentacion = new Array();
		var arRelViaAdmon = new Array();

		esLiquidoEndovenoso = false;

		for( var i = 0; i < item.length; i++ ){

			// Si viene precedido de @ es unidad y se asigna al array arUnidades
			if( item[i].substr(0,1) == "@" ){
				codigoUnidad = trim(item[i].substr(1));
				arUnidades[ arUnidades.length ] = codigoUnidad+"-"+item[++i];
				j++;
				k = 0;
			}

			// Si viene precedido de & es presentacion (forma farmaceutica) y se asigna al array arPresentaciones
			if( item[i].substr(0,1) == "&" ){
				codigoFtica = trim(item[i].substr(1));
				arPresentaciones[ arPresentaciones.length ] = codigoFtica+"-"+item[++i];
				l++;
				m = 0;
			}

			// Si viene precedido de # es via de administracion y se asigna al array arViasAdmon
			if( item[i].substr(0,1) == "#" ){
				codigoVia = trim(item[i].substr(1));
				arViasAdmon[ arViasAdmon.length ] = codigoVia+"-"+item[++i];
				n++;
				p = 0;
			}

			// Si viene precedido de $ es dosis y se asigna al array arDosis
			if( item[i].substr(0,1) == "$" ){
				valorDosis = trim(item[i].substr(1));
				arDosis[ arDosis.length ] = valorDosis;
				q++;
				r = 0;
			}

			// Si viene precedido de - es artículo, comienza la asignación de los arreglos de relación y del arreglo arArticulos
			if( item[i].substr(0,1) == "-" ){

				var pos = arArticulos.length;

				if( !arRelUnidad[j] ){
					arRelUnidad[j] = new Array();
				}

				if( !arRelPresentacion[l] ){
					arRelPresentacion[l] = new Array();
				}

				if( !arRelViaAdmon[n] ){
					arRelViaAdmon[n] = new Array();
				}

				if( !arRelDosis[q] ){
					arRelDosis[q] = new Array();
				}

				if( !arArticulos[ pos ] ){
					arArticulos[ pos ] = new Array();
					arArticulos[ pos ][0] = "";
					arArticulos[ pos ][1] = "";
				}

				arRelUnidad[j][k] = arArticulos.length;
				arRelPresentacion[l][m] = arArticulos.length;
				arRelViaAdmon[n][p] = arArticulos.length;
				arRelDosis[q][r] = arArticulos.length;
				arArticulos[ pos ][0] = item[i++].substr( 1 );
				arArticulos[ pos ][1] = item[i];
				k++;
				m++;
				p++;
				r++;

				if(arArticulos[ pos ][0]=='LQ0000')
					esLiquidoEndovenoso = true;
			}

			//Esto fue lo que se escribio para la busqueda de la familia
			// Se guarda en una variable para buscar el medicamento correcto de acuerdo
			//al nombre generico o comercial según corresponda
			if( item[i].substr(0,1) == "<" ){
				bsqFamilia = item[i].substr(1);
			}

		}


		// 2012-12-14
		// alert("autocompletarParaBusqueMedicamentosPorFamilia: arRelUnidad="+arRelUnidad.toSource());
		// alert(arUnidades.toSource());
		// alert(arRelPresentacion.toSource());
		// alert(arPresentaciones.toSource());
		// alert(arArticulos.toSource());

		// 2012-07-09
		// Se adiciona seleccione al select de presentación de medicamentos
		//arUnidades[ arUnidades.length ] = "-seleccione-";

		/////////////////////////////////////////////////////////////////////////////////////////
		// Se reestablece valores vacio para los campos que hayan sido llenados
		//document.getElementById( "regletaGrabacionimp" ).style.display = 'none';	//Oculto la regleta de grabación si está activa

		if(!esLiquidoEndovenoso)
		{
			document.getElementById( "wdosisfamiliaimp" ).disabled = false;			//Activo el campo de dosis si está inactivo
			document.getElementById( "wdosisfamiliaimp" ).value = '';
		}
		else
		{
			document.getElementById( "wdosisfamiliaimp" ).value = '';			//Desactivo el campo de dosis
			document.getElementById( "wdosisfamiliaimp" ).disabled = true;			//Desactivo el campo de dosis
		}

		//Quito los valores de los campos de busqueda y adición de medicamentos
		document.getElementById('wpresentacionimp').selectedIndex = -1;
		document.getElementById('wunidadimp').selectedIndex = -1;
		document.getElementById('wfrecuenciaimp').selectedIndex = -1;
		document.getElementById('wadministracionimp').selectedIndex = -1;
		//document.getElementById('wcondicionsumimp').selectedIndex = -1;
		document.getElementById('wdiastratamientoimp').value = '';
		document.getElementById('wdosismaximaimp').value = '';
		document.getElementById('wtxtobservasionesimp').value = '';

		//Quito los valores de la regleta de grabacion
		document.getElementById('dosisRonda2').value = '';
		document.getElementById('dosisRonda4').value = '';
		document.getElementById('dosisRonda6').value = '';
		document.getElementById('dosisRonda8').value = '';
		document.getElementById('dosisRonda10').value = '';
		document.getElementById('dosisRonda12').value = '';
		document.getElementById('dosisRonda14').value = '';
		document.getElementById('dosisRonda16').value = '';
		document.getElementById('dosisRonda18').value = '';
		document.getElementById('dosisRonda20').value = '';
		document.getElementById('dosisRonda22').value = '';
		document.getElementById('dosisRonda24').value = '';
		/////////////////////////////////////////////////////////////////////////////////////////

		// Quedan definidos los arrays derelación y de artículos que también se usaran en otras funciones
		arRelacionesUnidad = arRelUnidad;
		arRelacionesPresentacion = arRelPresentacion;
		arRelacionesViaAdmon = arRelViaAdmon;
		arArticulosSeleccionados = arArticulos;

		// 2012-12-14
		// alert("autocompletarParaBusqueMedicamentosPorFamilia: arRelacionesUnidad="+arRelacionesUnidad.toSource());

		// Se asigna dinámicamente las opciones a los selects wunidad, wpresentacion, wadministracion
		creandoOptionsValue( document.getElementById( "wunidadimp" ), arUnidades.reverse() );
		creandoOptionsValue( document.getElementById( "wpresentacionimp" ), arPresentaciones.reverse() );
		creandoOptionsValue( document.getElementById( "wadministracionimp" ), arViasAdmon.reverse() );

		// 2012-07-09
		// Para que muestre por defecto el select vacio
		if(arUnidades.length>1)
			document.getElementById( "wunidadimp" ).options.selectedIndex = -1;

		if(arPresentaciones.length>1)
			document.getElementById( "wpresentacionimp" ).options.selectedIndex = -1;

		 if(arViasAdmon.length>1)
			 document.getElementById( "wadministracionimp" ).options.selectedIndex = -1;

		// Se reviza si hay algun detalle de fmailia desplegado y se oculta
		// ocultarDetalleFliaAnterior();

		//Si solo hay una presentación para escoger ejecuto la función
		if(arPresentaciones.length == 1){
			filtrarMedicamentosPorCampo('presentacion', 'imp');
		}

		//creando array de medicamentos
	});
}



/************************************************************************************************************************
 * Filtrar medicamentos por familia
 * tipoConsulta: define si se va a filtrar con base en presentación o con base en presntacion y unidad
 * @return
 ************************************************************************************************************************/
function filtrarMedicamentosPorCampo(tipoConsulta,posnombre){

	//debugger;

	if(!posnombre)
		posnombre = '';

	// Vaiable que va a tener los datos del resultado AJAX
	var item = "";

	// Si se ha seleccinado alguna presentación
	if(document.getElementById('wpresentacion'+posnombre).selectedIndex!='-1')
	{
		// Asigno la presentacion seleccionada
		var presentacionFiltro = document.getElementById('wpresentacion'+posnombre).options[ document.getElementById('wpresentacion'+posnombre).selectedIndex ].value;

		// Si se ha seleccionado una unidad
		if(tipoConsulta=='unidad' && document.getElementById('wunidad'+posnombre).selectedIndex!='-1')
			var unidad = document.getElementById('wunidad'+posnombre).options[ document.getElementById('wunidad'+posnombre).selectedIndex ].value;
		else
			var unidad = '%';

		// definición de parámetros para el llamado AJAX
		var parametros = "consultaAjaxKardex=34&wemp_pmla="+document.forms.forma.wemp_pmla.value
						 +"&basedatos="+document.forms.forma.wbasedato.value
						 +"&wcenmez="+document.getElementById('wcenmez').value
						 +"&familia="+encodeURIComponent( document.getElementById('wnombrefamilia'+posnombre).value )
						 +"&presentacion="+presentacionFiltro
						 +"&unidad="+unidad
						 +"&bsq="+bsqFamilia;	//variable global
		//alert(parametros);

		var mensaje = "";

		// Llamado AJAX para obtener los datos de búsqueda de medicamentos
		// Con los nuevos filtros de presentación y/o unidad
		try{
			ajax=nuevoAjax();

			ajax.open("POST", "ordenes.inc.php",false);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			// Asigno el resultado que se traen con el llamado AJAX
			var item = $.trim( ajax.responseText );

			if ( !estaEnProceso(ajax) ) {
				ajax.send(null);
			}
		}catch(e){	}


		// Formo el array de los resultados para la busquedad de medicamentos
		var item = item.split('|');


		// Creando array de resultados
		var arDosis = new Array();
		var arUnidades = new Array();
		var arPresentaciones = new Array();
		var arViasAdmon = new Array();
		var arArticulos = new Array();

		// Indices para los diferentes arrays
		var j = -1;
		var k = 0;
		var l = -1;
		var m = 0;
		var n = -1;
		var p = 0;
		var q = -1;
		var r = 0;

		// Arrays de relación con los articulos
		var arRelDosis = new Array();
		var arRelUnidad = new Array();
		var arRelPresentacion = new Array();
		var arRelViaAdmon = new Array();

		for( var i = 0; i < item.length; i++ ){

			// Si viene precedido de @ es unidad y se asigna al array arUnidades
			if( item[i].substr(0,1) == "@" ){
				codigoUnidad = trim(item[i].substr(1));
				arUnidades[ arUnidades.length ] = codigoUnidad+"-"+item[++i];
				j++;
				k = 0;
			}

			// Si viene precedido de & es presentacion (forma farmaceutica) y se asigna al array arPresentaciones
			if( item[i].substr(0,1) == "&" ){
				codigoFtica = trim(item[i].substr(1));
				arPresentaciones[ arPresentaciones.length ] = codigoFtica+"-"+item[++i];
				l++;
				m = 0;
			}

			// Si viene precedido de # es via de administracion y se asigna al array arViasAdmon
			if( item[i].substr(0,1) == "#" ){
				codigoVia = trim(item[i].substr(1));
				arViasAdmon[ arViasAdmon.length ] = codigoVia+"-"+item[++i];
				n++;
				p = 0;
			}

			// Si viene precedido de $ es dosis y se asigna al array arDosis
			if( item[i].substr(0,1) == "$" ){
				valorDosis = trim(item[i].substr(1));
				arDosis[ arDosis.length ] = valorDosis;
				q++;
				r = 0;
			}

			// Si viene precedido de - es artículo, comienza la asignación de los arreglos de relación y del arreglo arArticulos
			if( item[i].substr(0,1) == "-" ){

				var pos = arArticulos.length;

				if( !arRelUnidad[j] ){
					arRelUnidad[j] = new Array();
				}

				if( !arRelPresentacion[l] ){
					arRelPresentacion[l] = new Array();
				}

				if( !arRelViaAdmon[n] ){
					arRelViaAdmon[n] = new Array();
				}

				if( !arRelDosis[q] ){
					arRelDosis[q] = new Array();
				}

				if( !arArticulos[ pos ] ){
					arArticulos[ pos ] = new Array();
					arArticulos[ pos ][0] = "";
					arArticulos[ pos ][1] = "";
				}

				arRelUnidad[j][k] = arArticulos.length;
				arRelPresentacion[l][m] = arArticulos.length;
				arRelViaAdmon[n][p] = arArticulos.length;
				arRelDosis[q][r] = arArticulos.length;
				arArticulos[ pos ][0] = item[i++].substr( 1 );
				arArticulos[ pos ][1] = item[i];
				k++;
				m++;
				p++;
				r++;
			}
		}

		if(document.getElementById('wdosisfamilia'))
		{
			if(arDosis.length>=1)
			{
				//Si se escribe algo en dosis no se resetea
				// if( porLevIC == '' )
					// document.getElementById('wdosisfamilia'+posnombre).value = '';
				$("#wdosisfamilia"+posnombre).trigger('dblclick');
				$("#wdosisfamilia"+posnombre).flushCache();
				$("#wdosisfamilia"+posnombre).unbind("result");

				$("#wdosisfamilia"+posnombre).autocomplete( arDosis ,
				{
						cacheLength:0,
						max: 10,
						scroll: false,
						scrollHeight: 500,
						matchContains: false,
						width:70,
						minChars: 0,
						autoFill:false,
						delay:200,
						selectFirst:false
				});

				$("#wdosisfamilia"+posnombre).focus(function() {
					$(this).trigger('keydown');
					$(this).trigger('click');
					$(this).trigger('click');
				});

			}
			// Se comenta, la dosis siempre viene vacía
			// else if(arDosis.length==1)
			// {
				// document.getElementById('wdosisfamilia'+posnombre).value = arDosis[ arDosis.length - 1 ];
			// }
		}

		/*
		if(document.getElementById('wdosisfamilia')){

			$("#wdosisfamilia").flushCache();
			$("#wdosisfamilia").unbind("result");

			$("#wdosisfamilia").setOptions({arDosis});

			$("#wdosisfamilia").autocomplete(arDosis, {
				max: 100,
				scroll: false,
				scrollHeight: 500,
				matchContains: true,
				width:500,
				minChars: 3,
				autoFill:false,
				formatResult: function(data, value) {
					return value;
				}
			}).result(function(event, item) {

			});
		}
		*/





		// 2012-07-09
		// Se adiciona seleccione al select de presentación de medicamentos
		//arUnidades[ arUnidades.length ] = "-seleccione-";


		// Quedan definidos los arrays de relación y de artículos que también se usaran en otras funciones
		//arRelacionesPresentacion = arRelPresentacion;		// Las presentaciones solo se definen en la seleccion de la familia
		if(tipoConsulta=='presentacion')	// Se filtra unidades solo si lo que se selecciono fue presentacion
			arRelacionesUnidad = arRelUnidad;
		arRelacionesViaAdmon = arRelViaAdmon;
		//arArticulosSeleccionados = arArticulos;

		// 2012-12-14
		// alert("filtrarMedicamentosPorCampo: arRelacionesUnidad="+arRelacionesUnidad.toSource());

		// Se asigna dinámicamente las opciones a los selects wunidad, wpresentacion, wadministracion
		//creandoOptionsValue( document.getElementById( "wpresentacion" ), arPresentaciones.reverse() );		// Las presentaciones solo se definen en la seleccion de la familia
		if(tipoConsulta=='presentacion')		// Se filtra unidades solo si lo que se selecciono fue presentacion
			creandoOptionsValue( document.getElementById( "wunidad"+posnombre ), arUnidades.reverse() );

		// 2012-07-09
		// Para que muestre por defecto el select vacio
		if(arUnidades.length>1)
			document.getElementById( "wunidad"+posnombre ).options.selectedIndex = -1;

		if(arPresentaciones.length>1)
			document.getElementById( "wpresentacion"+posnombre ).options.selectedIndex = -1;


		creandoOptionsValue( document.getElementById( "wadministracion"+posnombre ), arViasAdmon.reverse() );

		if(arViasAdmon.length>1)
			document.getElementById( "wadministracion"+posnombre ).options.selectedIndex = -1;

		// Se reviza si hay algun detalle de familia desplegado y se oculta
		ocultarDetalleFliaAnterior();

		//creando array de medicamentos
	}
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

/******************************************************************************************************************************
 * Control de seleccion de fechas. Evita que se selecciona una fecha y hora antes de la fecha actual y antes de dos horas.
 * @param date
 * @param stringFecha
 * @return
 *
 * Creado: Marzo 16 de 2011
 ******************************************************************************************************************************/
function alSeleccionarFecha( date, stringFecha ){
	
	var idx = date.params.inputField.id.substring( 8 );
	
	var ultimaAplicacion = 0;
	// if( $( "#wtimeultimaaplicacion"+idx ).length > 0 ){
		// ultimaAplicacion = $( "#wtimeultimaaplicacion"+idx ).val()-2*3600;
	// }
	
	var now = new Date();
	
	var msg = "La hora no esta permitida para la fecha seleccionada"
	if( ultimaAplicacion > now.getTime() ){
		now.setTime(ultimaAplicacion);
		msg = "La hora no esta permitida para la fecha seleccionada\npor que el articulo ya ha sido aplicado anteriormente.";
	}

	var comparacion = compareDatesOnly(now, date.currentDate);
	var tiempoAntes = $( "#tiempoMinimoOrdenMedicamento" ).val()*1;

	var hours = date.currentDate.getHours();

	//Limita que no se seleccionen dias anteriores
	if (comparacion < 0) {
		desactivar = true;
	}

	var ano = date.currentDate.getFullYear();
	var mes = date.currentDate.getMonth();
	var dia = parseInt(date.currentDate.getDate())+1;
	var hora = date.currentDate.getHours();

	//Limita que no se seleccionen horas anteriores
	if (typeof(hours) != "undefined"){

		/****************************************************************************************
		 * Marzo 13 de 2012
		 *
		 * Si el medicamento es nuevo se permite colocar la hora a partir de la ronda actual,
		 * de lo contrario solo se permite a partir de la siguiente ronda
		 ****************************************************************************************/
		if( $( "#enUrgencias" ).val() == 'on' && ( !date.params.inputField.parentNode.parentNode.className || date.params.inputField.parentNode.parentNode.className == '' ) ){	//Si el articulo es nuevo
			// alert( "Nuevo" );
			if ( (hours <= now.getHours() && now.getHours()-hours > 1 ) && comparacion == 0 ) {	//Marzo 6 de 2012
				date.setDate( new Date(ano,mes,dia,hora,0,0) );
				// alert( msg );
				alert( msg );
				return true;
			}
			else if( !date.dateClicked && now.getDate() + 1 == dia - 1 && now.getMonth() == mes && now.getFullYear() == ano && now.getHours()-hours <= 1 ){
				date.setDate( new Date(ano,mes,dia-2,hora,0,0) );
				return true;
			}
		}
		else{	//Si el articulo es viejo
			// console.log( ""+hours +" <= "+ now.getHours()+" +:: "+(now.getMinutes()+tiempoAntes)/60 );
			// console.log( hours <= now.getHours()+(now.getMinutes()+tiempoAntes)/60 );
			if ( (hours <= now.getHours()+(now.getMinutes()+tiempoAntes)/60 /*&& now.getHours()-hours > 1*/ ) && comparacion == 0 ) {	//Marzo 6 de 2012
				// alert( "Viejo" );
				date.setDate( new Date(ano,mes,dia,hora,0,0) );
				alert( msg );
				return true;
			}
			else if( !date.dateClicked && now.getDate() + 1 == dia - 1 && now.getMonth() == mes && now.getFullYear() == ano && now.getHours()+(now.getMinutes()+tiempoAntes)/60-hours <= 1 ){
				date.setDate( new Date(ano,mes,dia-2,hora,0,0) );
				return true;
			}
		}
	}

	if( date.dateClicked ){
			date.params.inputField.value = date.currentDate.print(date.params.ifFormat);
			if( date.params.inputField.onchange ){
				date.params.inputField.onchange();
			}
		date.callCloseHandler();
	}
	else{
		date.params.inputField.ultimaFechaSeleccionada = date.currentDate;
	}
}


function mostrarModalResultadosEstudios( url ){
		
	$.blockUI({
		message: "<div style='height:100%;display: flex;flex-direction: column;align-items: stretch;'>"
					+"<div style='display:flex;justify-content: center;'>"
						+"<span onclick='$.unblockUI();' class=fondorojo style='margin:5px;padding:10px;color:white;font-weight:14pt;width:100px;cursor:pointer;border-radius:5px;font-weight:bold;'>Cerrar</span>"
					+"</div>"
					+"<div style='display:flex;justify-content: center;height:100%;'>"
						+"<iframe src='"+url+"' style='width:100%;height:100%;'></iframe>"
					+"</div>"
				+"</div>", 
		css:{ 
				width	: "90%", 
				height	: "90%", 
				top 	: "5%", 
				left	: "5%",
				cursor	: "default",
			},
		});
}


/**************************************************************************************************
 * Abre una ventana maximizada con la url correspondiente
 **************************************************************************************************/
function abrirVentanaVerAnteriores( url, tipoOrden, nroOrden ){
	
	$.post("ordenes.inc.php",
		{
			consultaAjax		: '',
			consultaAjaxKardex	: 'actualizarOrdenLeidaMedico',
			wbasedato			: $("#wbasedato").val(),
			wemp_pmla			: $("#wemp_pmla").val(),
			historia			: $("#whistoria").val(),
			ingreso				: $("#wingreso").val(),
			wusuario			: $("#usuario").val(),
			tipoOrden			: tipoOrden,
			nroOrden			: nroOrden,
			// fechaKardex			: $('#wfechagrabacion').val(),
		}, 
		function(data){

			if( $.trim( data ) == 1 ){
				$( ".blink-"+tipoOrden+"-"+nroOrden )
					.removeClass("blink")
					.removeAttr("ple");
			}
			
			consultarOrdenesPendientesDeVer();
		}
	);
	
	// mostrarModalResultadosEstudios( url );
}

// function abrirVentanaVerAnteriores( url ){

	// var ancho=screen.width;
	// var alto=screen.availHeight;
	// var v = window.open( url,'','scrollbars=1, width='+ancho+', height='+alto );
	// v.moveTo(0,0);
// }

/******************************************************************************
 * Se hace un efecto de pulso para que la fila parpadee
 * - Esto aplica para todas la tablas en la que se muestra los medicamentos
 *
 * Marzo 7 de 2011
 ******************************************************************************/
function inicializarPulso(){

	$( "[id^=wchkconf]" ).each(function(i){

		var fila = $( this ).parent().parent();
		var celdaChk = $( this ).parent();
		var celdaNam = $( "#wnmmed" + this.id.substr( 8 ) ).parent();

		if( fila.attr && fila[0].className.toLowerCase() != "suspendido" && $( "#hiNoParpadear" ).val() == 'on'  ){
			//guradando el nombre de la clase original
			//Se agrega atributo originalClass al campo
			if( !celdaChk[0].originalClass && celdaChk[0].className != '' ){
				celdaChk[0].originalClass = celdaChk[0].className;
			}

			if( !this.disabled && !this.checked ){

				if( celdaChk[0].className == celdaChk[0].originalClass ){

					celdaChk[0].className = "fondoAlertaConfirmar";
					celdaNam[0].className = "fondoAlertaConfirmar";
				}
				else{
					celdaChk[0].className = celdaChk[0].originalClass;
					celdaNam[0].className = celdaChk[0].originalClass;
				}
			}
			else if( celdaChk[0].originalClass ){
				celdaNam[0].className = celdaChk[0].originalClass;
				celdaChk[0].className = celdaChk[0].originalClass;
			}
		}
	});

	$( ".fondoalertaeliminar,[originalClass]" ).each(function(i){

		if(i > 0 ){

			var fila = $( this ).parent();

			var celdaInac = $( this );

			if( celdaInac.length > 0 && ( celdaInac[0].className.toLowerCase() == "fondoalertaeliminar"
				|| celdaInac[0].originalClass && celdaInac[0].originalClass.toLowerCase() == "fondoalertaeliminar" ) ){

				if( !celdaInac[0].originalClass && celdaInac[0].className != '' ){
					celdaInac[0].originalClass = celdaInac[0].className;
					$( celdaInac ).attr( "originalClass", "" );
				}

				if( celdaInac[0].className == celdaInac[0].originalClass ){
					celdaInac[0].className = fila[0].className;
				}
				else{
					celdaInac[0].className = celdaInac[0].originalClass;
				}
			}
		}
	});
}

function mostrarMensajesIniciales(){

	if( alertsIniciales && alertsIniciales.length > 0 ){
		
		var msg = alertsIniciales[0];
		if( msg[0] == '@' ){
			msg = msg.substr( 1 );
		}
		
		jAlert( msg, 'ALERTA', function(r){
			alertsIniciales.splice(0,1);
			mostrarMensajesIniciales();
		});
		if( alertsIniciales[0][0] == '@' ){
			$( "h1", $( "#popup_container" ) ).css({background:"orange", color:"black"});
			$( "#popup_content", $( "#popup_container" ) ).css({background:"#FFFE9A", color:"black"});
		}
		else{
			$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
		}
	}
}

/*****************************************************************************************************************************
 * Inicializa jquery
 ******************************************************************************************************************************/
function inicializarJquery(){
	
	$( document ).keypress(function(e){
		if( e.ctrlKey && ( e.key == 'p' || e.key == 'P' ) )
			return false;
	});
	
	if( $( "#validarBrowser" ).val() == "on" ){
	
		if( $.browser && $.browser.mozilla ){
			
			if( $.browser.version*1 > $( "#versionMozilla" ).val()*1 ){
				alert( "Actualizando version" );
				//Actualizando version
				$.ajax({
					url: "ordenes.inc.php",
					type: "POST",
					data:{
						consultaAjaxKardex: '79',
						wemp_pmla		  :	$("#wemp_pmla").val(),
						version			  : $.browser.version,
					},
					async: false,
					success:function(data_json) {
					},
					dataType: "json",
				});
				mostrarMensajesIniciales();
			}
			else if( $.browser.version*1 < $( "#versionMozilla" ).val()*1 ){
				var msgBrowser = "DEBE ACTUALIZAR EL NAVEGADOR PARA USAR ORDENES.<br>YA SE ENVIO UN CORREO A SISTEMAS CON EL REQUERIMIENTO RESPECTIVO";
				$.alerts.dialogClass = "actualizar";
				jAlert( msgBrowser, "ALERTA", function(){
					$.ajax({
						url: "ordenes.inc.php",
						type: "POST",
						data:{
							consultaAjaxKardex: '80',
							wemp_pmla		  :	$("#wemp_pmla").val(),
						},
						async: true,
						success:function(data_json) {
							// alert("Enviando correo.........");
							salir_sin_grabar();
							$.alerts.dialogClass = null;
						},
						dataType: "json",
					});
				});
			}
			else{
				mostrarMensajesIniciales();
			}
		}
		else{
			mostrarMensajesIniciales();
		}
	}
	else{
		mostrarMensajesIniciales();
	}
	
	try{
		if( $( "#txtDextrometer" ).length ){
			$( "#txtDextrometer" )[0].__valAnterior = $( "#txtDextrometer" ).val();
		}
	}
	catch(e){}

	
	
	
	
	
	
	
	//Asigno autocompletar para la busqueda de diagnosticos en el encabezado
	$( "#inCIE10" ).autocomplete("ordenes.inc.php?consultaAjaxKardex=84&wemp_pmla="+wemp_pmla.value+"&wbasedato="+wbasedato.value+"&whce="+wbasedatohce.value, {
		cacheLength		: 1,
		delay			: 300,
		max				: 100,
		scroll			: false,
		scrollHeight	: 500,
		matchSubset		: false,
		matchContains	: true,
		width			: 1000,
		autoFill		: false,
		minChars		: 3,
		// mustMatch		: true,
		formatItem		: function(data, i, n, value) {
		
			//convierto el string en json
			eval( "var datos = "+data );
			
			return datos[0].label;	//Eso es lo que se muestra al usuario
			// return value;
		},
		formatResult	: function(data, value){

			eval( "var datos = "+data )
			// return datos[0].label;
			return datos[0].value.cod;
		},
	}).result( function(event, item ){
		if( item ){
			//La respuesta es un json
			//convierto el string en formato json
			eval( "var datos = "+item );
			
			this.value = datos[0].label;
			
			$("#inCopyCIE10" ).val( this.value );
			
			$( "#btCerrarDx" ).attr({
				disabled: false,
			});
		}
		else{
			$( "#btCerrarDx" ).attr({
				disabled: true,
			});
		}
	})
	
	$( "#inCIE10" ).on({
		keypress: function(){
			$( "#btCerrarDx" ).attr({
				disabled: true,
			});
		},
		change: function(){
			if(  $(this).val() == '' ){
				$("#inCopyCIE10" ).val('');
			}
		},
		focusout: function(){
			$( this ).val( $("#inCopyCIE10" ).val() );
			
			$( "#btCerrarDx" ).attr({
				disabled: $("#inCopyCIE10" ).val() != '' ? false : true,
			});
		},
	});
	
	//Función para cerrar la modal al preguntar por el diagnóstico para articulos de control
	$( "#btCerrarDx" ).click(function(){
		if( $("#inCopyCIE10" ).val() != '' ){
			$.unblockUI();
		}
		else{
			jAlert( "Debe ingresar un <b>diagnóstico</b>.", "ALERTA" );
		}
	}).attr({
		disabled: true,
	});
	
	
	
	
	
	
	
	
	
	
	
	
	
	/************************************************************************************************************
	 * Muestra todos los mensajes que se crean inicialmente
	 ************************************************************************************************************/
	// mostrarMensajesIniciales();
	/************************************************************************************************************/

	//A los procedimientos agrupados ya grabados se les crea la propiedad grabado
	for( var x in procAgrupados ){
		procAgrupados[x].estado = 'grabado';
	}

	/************************************************************************************************************
	 * Creo el objeto artLevs de acuerdo a artLevsCopia
	 ************************************************************************************************************/
	 try{
		 if( artLevsCopia ){
			for( var i in artLevsCopia ){
				var j = $( "[id^=widoriginal][value="+artLevsCopia[ i ].insIdi+"]" )[0].id.substr(11)
				artLevs[j] = artLevsCopia[ i ];
				artLevs[j].codLev = $( "[id^=widoriginal][value="+artLevsCopia[ i ].idoLev+"]" )[0].id.substr(11)
			}

			try{
				//Borra la memoria del objeto artLevsCopia
				delete artLevsCopia;
			}
			catch(e){}
		}
	}
	catch(e){
	}
	/************************************************************************************************************/

	//setInterval( "inicializarPulso()", 500 ); //Marzo 7 de 2011
	
	//Abril 18 de 2016.  Se reactiva de nuevo para confirmar los articulos
	setInterval( "inicializarPulso()", 500 ); //Marzo 7 de 2011


	$("#tabs").tabs({ fx: {opacity: 'toggle' }, select: function(event, ui) { try{ if(fixedMenu2 && fixedMenu2 != 'undefined') fixedMenu2.hide();}catch(e){} } }); //JQUERY:  Activa los tabs para las secciones del kardex


	$("#tabs2").tabs({ fx: {opacity: 'toggle' }, select: function(event, ui) { try{ if(fixedMenu2 && fixedMenu2 != 'undefined') fixedMenu2.hide(); }catch(e){} } });


	$("#tabs").tabs('select', 1);

	$("#tabsMedicamentos").tabs();
	$("#tabsMedicamentos").tabs( 'select', 0 );

	//Para ocultar los medicamentos anteriores
	$("#btnOcultar").click(function () {
      $("#medAnt").toggle("slow");
    });

    if(document.getElementById("fixeddiv")){
    	$("#fixeddiv").draggable();
    }

     if(document.getElementById("fixeddiv2")){
    	 $("#fixeddiv2").draggable();
     }

     if(document.getElementById("movExamenes")){
    	 $("#movExamenes").draggable();
     }


     //Ciclo para colocar en funcionamiento los tooltips
     var cont1 = 0, cont2 = 0;
     while(document.getElementById("trFil"+cont1)){
    	 $('#trFil'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
//    		 document.getElementById('tr'+tipo+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
    	 cont1++;
     }

	$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
	$(".msg_tooltip_DA").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -200 });
	$(".msg_tooltip_NE").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -30 });
	$("[pactivo]").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 20 });
	$("img[title]").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 20 });

//     var protocolos = new Array('%','N','A','U','Q');
//     var tipo = "";
//     debugger;
//
//     while(cont2 < protocolos.length){
//    	 tipo = protocolos[cont2];
//	    cont1 = 0;
//	    cont2++;
//     }

	//Tooltips de protocolo de ayudas y procedimientos
     cont1 = 0;
     tipo = "4-";
     while(document.getElementById(tipo+cont1)){
		 $('#'+tipo+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -140 });
		 cont1++;
 	 }

     //Simpletree Arbol
     var indiceArbol = 1;
     if(!document.getElementById("arbol"+indiceArbol)){
    	 indiceArbol = 2;
     }
     while(document.getElementById("arbol"+indiceArbol)){
 	 var simpleTreeCollection = $('#arbol'+indiceArbol).simpleTree({
 		 	autoclose: true,
 		 	drag: false,
 		 	afterClick:function(node){
 				var elemento = $('span:first',node);

 				var indice = elemento.parent().attr("id");
 				var monda = elemento.parent().parent();
 				var monda2 = node;
 				var texto = elemento.text();

 				/*Si el elemento seleccionado es una hoja proceso la peticion, una rama no tiene efecto en este caso
 				 * Esto se logra con el id
 				 */
 				if(indice.substring(0,1) == "H"){
 					seleccionHojaAccionesComplementarias(indice,texto,this);
 				}
 			},
 			afterDblClick:function(node){ /*alert("text-"+$('span:first',node).text());*/ },
 			afterMove:function(destination, source, pos){ /*alert("destination-"+destination.attr('id')+" source-"+source.attr('id')+" pos-"+pos);*/},
 			afterAjax:function(){ /*alert('Loaded');*/ },
 			animate:true,//,docToFolderConvert:true
 			identificador:indiceArbol
 		});
 		indiceArbol++;
    }

    enfocarInicio();

    if(document.getElementById("whgrabado") && document.getElementById("whgrabado").value != ''){
    	$.growlUI('','Se ha grabado la orden');
    }
    $("#tabs").css('display','block');
    $("#tabs2").css('display','block');
    $("#msjInicio").css('display','none');

    $("#acExamenes").accordion({ collapsible: true });

    //Autocomplete para LEV
    if(document.getElementById('wnomcom')){
    	$("#wnomcom").autocomplete("ordenes.inc.php?consultaAjaxKardex=30&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&descripcion="+document.getElementById('wnomcom').value, {
    		cacheLength:0,
			max: 100,
    		scroll: true,
    		scrollHeight: 500,
    		matchContains: true,
    		width:500,
    		autoFill:false,
    		minChars: 3,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {
    		alert( item );
    		seleccionarComponente("",reemplazarTodo(item," ","_"));

//    		this.focus();
//    		this.select();
    	});
    }

    //Autocomplete para Ayudas y procedures
    if(document.getElementById('wnomproc')){
    	// $("#wnomproc").autocomplete("ordenes.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&descripcion="+document.getElementById('wnomproc').value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value, {
    	$("#wnomproc").autocomplete("ordenes.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&descripcion="+document.getElementById('wnomproc').value+"&ccoPaciente="+document.forms.forma.wservicio.value, {
			extraParams: {
				tipoServicio: function(){
					return $( "#wselTipoServicio" ).val();
				}
			},
			cacheLength:0,
    		max: 100,
    		scroll: false,
    		scrollHeight: 500,
    		matchContains: true,
    		width:500,
    		minChars: 3,
    		autoFill:false,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {
			adicionMultiple = false;
    		// Por ac{a pasa cuando el tipo de orden seleccionada es "Todos"
			document.getElementById('wnomproc').value = '';
			
			// console.log( "json" )
			// console.log( JSON.parse( item[13] ) )
			seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12], undefined, undefined, JSON.parse( item[13] ), item[14], item[15] );

    		this.onfocus = function(){
//    			alert( justificacionUltimoExamenAgregado );
    			if( justificacionUltimoExamenAgregado ){
    				justificacionUltimoExamenAgregado.focus();
    				justificacionUltimoExamenAgregado = '';
    			}
    		};
//    		this.focus();
//    		this.select();
    	});
    }

    // Se comenta porque no afecta la funcionalidad del autocompletar - 2012-11-14
	//Autocomplete para busqueda de medicamentos por familia
	if(document.getElementById('wnombrefamilia')){
    	$("#wnombrefamilia").autocomplete("ordenes.inc.php?consultaAjaxKardex=31&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&wcenmez="+document.forms.forma.wcenmez.value+"&historia="+document.forms.forma.whistoria.value+"&ingreso="+document.forms.forma.wingreso.value, {
			cacheLength:0,
			delay:0,
    		max: 100,
    		scroll: false,
    		scrollHeight: 500,
    		matchContains: true,
			matchSubset:false,
    		width:1000,
    		minChars: 2,
    		autoFill:false,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {

    		this.focus();
    		this.select();
    	});
    }

    //Componente de autocompletar para busqueda de medicamentos
    if(document.getElementById('wnombremedicamento')){
    	$("#wnombremedicamento").autocomplete("./ordenes.inc.php?consultaAjaxKardex=30&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&ccoPaciente="+document.forms.forma.wservicio.value, {
    		cacheLength:0,
			max: 100,
    		scroll: true,
    		scrollHeight: 500,
    		matchContains: true,
    		width:1000,
    		autoFill:false,
    		minChars: 4,
    		formatResult: function(data, value){
//    			debugger;
				return value;
			}
    	}).result(function(event, item) {
	//    		debugger;
				if(item != "No se encontraron coincidencias"){
	//    		seleccionarComponente("",reemplazarTodo(item," ","_"));

				/*FORMATO DE LA RESPUESTA PARA EL AUTOCOMPLETE:
				 * Argumentos:
				 *
				 * 0: Como se muestra en el autocomplete
				 * 1: Codigo del articulo
				 * 2: Nombre comercial del articulo
				 * 3: Nombre genérico del articulo
				 * 4: Tipo protocolo
				 * 5: (M)edicamento o (L)iquido
				 * 6: Es generico
				 * 7: Origen
				 * 8: Grupo de medicamento
				 * 9: Forma farmaceutica
				 * 10:Unidad
				 * 11:POS
				 * 12:Unidad de fraccion
				 * 13:Cantidad de fracciones
				 * 14:Vencimiento
				 * 15:Dias de estabilidad
				 * 16:Es dispensable
				 * 17:Es duplicable
				 * 18:Dias maximos sugeridos
				 * 19:Dosis maximas sugeridas
				 * 20:Via
				 * 21:Abre ventana parametrizada ----
				 * 22:Cantidad de dosis
				 * 23:Unidad de dosis
				 * 24:No enviar
				 * 25:Frecuencia
				 * 26:Vias de administracion
				 * 27:Condicion de suministro
				 * 28:Confirmada preparacion
				 * 29:Dias maximos tratamiento
				 * 30:Dosis maximas tratamiento
				 * 31:Observaciones adicionales
				 * 32:Componentes del tipo
				 * 33:Nombre personalizado del articulo
				 * 34:
				 * 35:Codigo principio activo
				 * 36:Descricion principio activo
				 * 37:esAntibiotico
				 * 38:InformacionNutriciones
				 */
				var codigoArticulo = item[1];
				var nombreComercial = item[2];
				var nombreGenerico = item[3];
				var tipoProtocolo = item[4];
				var tipoMedicamentoLiquido = item[5];
				var esGenerico = item[6];
				var origen = item[7];
				var grupoMedicamento = item[8];
				var formaFarmaceutica = item[9];
				var unidadMedida = item[10];
				var pos = item[11];
				var unidadFraccion = item[12];
				var cantidadFraccion = item[13];
				var vencimiento = item[14];
				var diasEstabilidad = item[15];
				var dispensable = item[16];
				var duplicable = item[17];
				var diasMaximosSugeridos = item[18];
				var dosisMaximasSugeridas = item[19];
				var viaAdministracion = item[20];
				var abreVentanaFija = item[21];
				var cantidadDosisFija = item[22];
				var unidadDosisFija = item[23];
				var noEnviarFija = item[24];
				var frecuenciaFija = item[25];
				var viasAdministracionFija = item[26];
				var condicionSuministroFija = item[27];
				var confirmadaPreparacionFija = item[28];
				var diasMaximosFija = item[29];
				var dosisMaximasFija = item[30];
				var observacionesFija = item[31];
				var componentesTipo = item[32];
				var nombrePersonalizadoDelArticulo = item[33];

				var codPrincipioActivo = item[34];
				var desPrincipioActivo = item[36];

				var	noEnviar = item[33];	//Abril 25 de 2011

				if( esGenerico.length > 0 ){
					duplicable = 'on';
				}
				
				var esAntibiotico = false;

				var infoNutriciones = item[38];
				
				agregarArticulo( "detKardex" );

				seleccionarArticulo(codigoArticulo,reemplazarTodo(nombreComercial," ","_"),reemplazarTodo(nombreGenerico," ","_"),origen,grupoMedicamento,
						formaFarmaceutica,unidadMedida,pos,unidadFraccion,cantidadFraccion,vencimiento,diasEstabilidad,
						dispensable,duplicable,diasMaximosSugeridos,dosisMaximasSugeridas,viaAdministracion,tipoProtocolo,tipoMedicamentoLiquido,
						esGenerico,abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija,
						condicionSuministroFija,confirmadaPreparacionFija,diasMaximosFija,dosisMaximasFija,observacionesFija,componentesTipo,noEnviar,codPrincipioActivo,desPrincipioActivo,esAntibiotico,infoNutriciones);

				this.value = "Generico: "+nombreGenerico+" Comercial:"+nombreComercial;
    		}

    		this.focus();
    		this.select();
    	});
    }

	// 2012-07-03
	// Inicializa accordion para los exámenes realizados
	$("#acExamenes").accordion({ collapsible: true });
	$("#null").accordion({ collapsible: true });

   //Autocompletar para la busqueda de medicamentos por familias
    autocompletarParaBusqueMedicamentosPorFamilia();
	autocompletarParaBusqueMedicamentosPorFamiliaAlta();

	// mostrarMensajeConfirmarKardex();	//Agosto 25 de 2011

	mensajeriaActualizarSinLeer = alActualizarMensajeria;

	consultarHistoricoTextoProcesado( document.forms.forma.wbasedato.value, document.forms.forma.wemp_pmla.value, document.forms.forma.whistoria.value, document.forms.forma.wingreso.value, document.getElementById( 'mesajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria' ) );	//Octubre 11 de 2011

	mensajeriaTiempoRecarga = consultasAjax( "POST", "../../../include/movhos/mensajeriaKardex.php", "consultaAjax=4&wemp="+document.forms.forma.wemp_pmla.value, false );
	mensajeriaTiempoRecarga = mensajeriaTiempoRecarga*60000;	//El tiempo que se consulta esta en minutos
	
	if( !mensajeriaTiempoRecarga || mensajeriaTiempoRecarga == 0 || isNaN( mensajeriaTiempoRecarga ) )
			mensajeriaTiempoRecarga = 10*60000;

	setInterval( "mensajeriaActualizar()", mensajeriaTiempoRecarga );

	var select = $('#wselTipoServicioImp');
	select.val($('option:first', select).val());

	var select = $('#wselTipoServicio');
	select.val($('option:first', select).val());

	cambiarEstadosImpresionProcAgrupados();
	
	
}

/****************************************************************************************************************************************************************
 * Inicializa el autocompletar de jquery para procedimientos y ayudas diangosticos segun el filttro seleccionado, esta función se llama cada vez que
 * se cambia el filtro de tipo de servicio
 * @return
 *
 * Enero 24 de 2011
 ****************************************************************************************************************************************************************/
function autocompletarParaConsultaDiagnosticas(){
// validarTipoOrdenAgrupada();
	var tipoServicioSel = $("#wselTipoServicio").val();
	var historia = $("#whistoria").val();
	var ingreso = $("#wingreso").val();

	parametros = "consultaAjaxKardex=47&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&wtipo="+tipoServicioSel;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		formTipoOrden = "";
		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200){
				// var formTipoOrden = $.trim( ajax.responseText );
				formTipoOrden = $.trim( ajax.responseText );

				if( $.trim(formTipoOrden) != "" && formTipoOrden!="" && formTipoOrden!=" ")
				{

					//////////////////////////////////////////////////////////

					// Consulto si el formulario ha sido diligenciado en la historia clínica electrónica
					parametros = "consultaAjaxKardex=50&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&formTipoOrden="+formTipoOrden+"&historia="+historia+"&ingreso="+ingreso;

					try{
						ajax=nuevoAjax();

						ajax.open("POST", "ordenes.inc.php",true);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);

						ajax.onreadystatechange=function()
						{

							if (ajax.readyState==4 && ajax.status==200){
								var formDiligenciado = $.trim( ajax.responseText );

								//Si el formulario no ha sido ingresado el dia actual se abre la modal con el formulario, sino muestra un mensaje con el mensaje
								//de que el formulario ya ha sido diligenciado el dia de hoy.

								var descripcion = $("#wselTipoServicio option:selected").text();

								if(formDiligenciado == 'ok'){
								var r = confirm("¿La "+descripcion+" ya ha sido ingresada el dia de hoy, desea agregarla?");
								}else{

								totalExamenesAntesGrabar = formDiligenciado;
									var descripcion = $("#wselTipoServicio option:selected").text();
									parametros = "consultaAjaxKardex=49&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value+"&descripcion="+descripcion;

									try{
										ajax=nuevoAjax();

										ajax.open("POST", "ordenes.inc.php",true);
										ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
										ajax.send(parametros);

										ajax.onreadystatechange=function()
										{
											if (ajax.readyState==4 && ajax.status==200){
												var strItem = $.trim( ajax.responseText );

												var item = strItem.split('|');

												seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);

												this.onfocus = function(){
													if( justificacionUltimoExamenAgregado ){
														justificacionUltimoExamenAgregado.focus();
														justificacionUltimoExamenAgregado = '';
													}
												};

												document.getElementById("btnCerrarVentana").style.display = 'none';


											}
										}
										if ( !estaEnProceso(ajax) ) {
											ajax.send(null);
										}
									}catch(e){	}

								var urlform = 'HCE.php?accion=M&ok=0&empresa='+document.forms.forma.wbasedatohce.value+'&origen='+document.forms.forma.wemp_pmla.value+'&wdbmhos='+document.forms.forma.wbasedato.value+'&wformulario='+formTipoOrden+'&wcedula='+document.forms.forma.wcedula.value+'&wtipodoc='+document.forms.forma.wtipodoc.value;

												//window.open(urlform,'_blank');
												$.blockUI({ message: $('<iframe src="'+urlform+'" width="950px" height="95%" scrolling="yes" frameborder="0" align="center"></iframe><div align="center"><input type="button" name="cerrarvtnhce" id="cerrarvtnhce" onClick="cerrarFormHCE('+cuentaExamenes+',\''+document.forms.forma.wbasedatohce.value+'\',\''+formTipoOrden+'\',\''+historia+'\',\''+ingreso+'\',\'\',\'on\')" value="Cerrar ventana" /></div>'),
															css: {
																top:  '5%',
																left: '10%',
																width: '950px',
																height: '90%',
																overflow: 'auto',
																cursor: 'auto'
															}
												});

								}

								if(r)
								{
									totalExamenesAntesGrabar = formDiligenciado;
									var descripcion = $("#wselTipoServicio option:selected").text();
									parametros = "consultaAjaxKardex=49&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value+"&descripcion="+descripcion;

									try{
										ajax=nuevoAjax();

										ajax.open("POST", "ordenes.inc.php",true);
										ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
										ajax.send(parametros);

										ajax.onreadystatechange=function()
										{
											if (ajax.readyState==4 && ajax.status==200){
												var strItem = $.trim( ajax.responseText );

												var item = strItem.split('|');

												seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);

												this.onfocus = function(){
													if( justificacionUltimoExamenAgregado ){
														justificacionUltimoExamenAgregado.focus();
														justificacionUltimoExamenAgregado = '';
													}
												};

												document.getElementById("btnCerrarVentana").style.display = 'none';


											}
										}
										if ( !estaEnProceso(ajax) ) {
											ajax.send(null);
										}
									}catch(e){	}

								var urlform = 'HCE.php?accion=M&ok=0&empresa='+document.forms.forma.wbasedatohce.value+'&origen='+document.forms.forma.wemp_pmla.value+'&wdbmhos='+document.forms.forma.wbasedato.value+'&wformulario='+formTipoOrden+'&wcedula='+document.forms.forma.wcedula.value+'&wtipodoc='+document.forms.forma.wtipodoc.value;

												//window.open(urlform,'_blank');
												$.blockUI({ message: $('<iframe src="'+urlform+'" width="950px" height="95%" scrolling="yes" frameborder="0" align="center"></iframe><div align="center"><input type="button" name="cerrarvtnhce" id="cerrarvtnhce" onClick="cerrarFormHCE('+cuentaExamenes+',\''+document.forms.forma.wbasedatohce.value+'\',\''+formTipoOrden+'\',\''+historia+'\',\''+ingreso+'\',\'\',\'on\')" value="Cerrar ventana" /></div>'),
															css: {
																top:  '5%',
																left: '10%',
																width: '950px',
																height: '90%',
																overflow: 'auto',
																cursor: 'auto'
															}
												});

								}else
								{
									var select = $('#wselTipoServicio');
									select.val($('option:first', select).val());

								}
							}
						}
						if ( !estaEnProceso(ajax) ) {
							ajax.send(null);
						}
					}catch(e){	}

					//////////////////////////////////////////////////////////

				}
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}

	//Autocomplete para Ayudas y procedures
    if( document.getElementById('wnomproc')){

		$("#wnomproc").flushCache();
		return;
    	$("#wnomproc").unbind("result");

		// 2012-06-27
		// Se comenta y se reemplaza por la siguiente línea debido a que estaba trayende 2 valores por cada selección que se hacia
		// Cuando se desplegaba las opciones y se seleccionaba y daba 'Enter' en una, se agregaba ésta y la anterior a las ordenes
		// Esto pasaba cuando se daba 'Enter', no pasaba si se hacia clic
		// $("#wnomproc").setOptions({url:"ordenes.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value});
    	$("#wnomproc").setOptions({url:"ordenes.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&ccoPaciente="+document.forms.forma.wservicio.value});

    	$("#wnomproc").autocomplete("ordenes.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value+"&ccoPaciente="+document.forms.forma.wservicio.value, {
    		cacheLength:0,
			max: 100,
    		scroll: false,
    		scrollHeight: 500,
    		matchContains: true,
    		width:500,
    		minChars: 3,
    		autoFill:false,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {
			adicionMultiple = false;

    		seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);

    		this.onfocus = function(){
    			if( justificacionUltimoExamenAgregado ){
    				justificacionUltimoExamenAgregado.focus();
    				justificacionUltimoExamenAgregado = '';
    			}
    		};
//    		this.focus();
//    		this.select();

			document.getElementById("wnomproc").value = '';
			document.getElementById("btnCerrarVentanabtnCerrarVentana").style.display = 'none';


    	});
    }
}

function autocompletarParaConsultaDiagnosticasAlta(){


	var tipoServicioSel = $("#wselTipoServicioImp").val();
	var historia = $("#whistoria").val();
	var ingreso = $("#wingreso").val();

	parametros = "consultaAjaxKardex=47&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&wtipo="+tipoServicioSel;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		formTipoOrden = "";
		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200){
				// var formTipoOrden = $.trim( ajax.responseText );
				formTipoOrden = $.trim( ajax.responseText );

				if( $.trim(formTipoOrden) !="" && formTipoOrden!="" && formTipoOrden!=" ")
				{

					//////////////////////////////////////////////////////////

					// Consulto si el formulario ha sido diligenciado en la historia clínica electrónica
					parametros = "consultaAjaxKardex=50&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatoshce="+document.forms.forma.wbasedatohce.value+"&formTipoOrden="+formTipoOrden+"&historia="+historia+"&ingreso="+ingreso;

					try{
						ajax=nuevoAjax();

						ajax.open("POST", "ordenes.inc.php",true);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);

						ajax.onreadystatechange=function()
						{

							if (ajax.readyState==4 && ajax.status==200){
								var formDiligenciado = $.trim( ajax.responseText );

								//Si el formulario no ha sido ingresado el dia actual se abre la modal con el formulario, sino muestra un mensaje con el mensaje
								//de que el formulario ya ha sido diligenciado el dia de hoy.
								var descripcion = $("#wselTipoServicioImp option:selected").text();

								if(formDiligenciado == 'ok'){
								var r = confirm("¿La "+descripcion+" ya ha sido ingresada el dia de hoy, desea agregarla?");
								}else{

									totalExamenesAntesGrabar = formDiligenciado;
									var descripcion = $("#wselTipoServicioImp option:selected").text();
									parametros = "consultaAjaxKardex=49&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicioImp').options[ document.getElementById('wselTipoServicioImp').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value+"&descripcion="+descripcion;

									try{
										ajax=nuevoAjax();

										ajax.open("POST", "ordenes.inc.php",true);
										ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
										ajax.send(parametros);

										ajax.onreadystatechange=function()
										{
											if (ajax.readyState==4 && ajax.status==200){
												var strItem = $.trim( ajax.responseText );

												var item = strItem.split('|');

												seleccionarAyudaDiagnosticaAlta(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);

												this.onfocus = function(){
													if( justificacionUltimoExamenAgregado ){
														justificacionUltimoExamenAgregado.focus();
														justificacionUltimoExamenAgregado = '';
													}
												};

												document.getElementById("btnCerrarVentana").style.display = 'none';


											}
										}
										if ( !estaEnProceso(ajax) ) {
											ajax.send(null);
										}
									}catch(e){	}

								var urlform = 'HCE.php?accion=M&ok=0&empresa='+document.forms.forma.wbasedatohce.value+'&origen='+document.forms.forma.wemp_pmla.value+'&wdbmhos='+document.forms.forma.wbasedato.value+'&wformulario='+formTipoOrden+'&wcedula='+document.forms.forma.wcedula.value+'&wtipodoc='+document.forms.forma.wtipodoc.value;

												//window.open(urlform,'_blank');
												$.blockUI({ message: $('<iframe src="'+urlform+'" width="950px" height="95%" scrolling="yes" frameborder="0" align="center"></iframe><div align="center"><input type="button" name="cerrarvtnhce" id="cerrarvtnhce" onClick="cerrarFormHCE('+cuentaExamenes+',\''+document.forms.forma.wbasedatohce.value+'\',\''+formTipoOrden+'\',\''+historia+'\',\''+ingreso+'\',\'alta\',\'on\' )" value="Cerrar ventana" /></div>'),
															css: {
																top:  '5%',
																left: '10%',
																width: '950px',
																height: '90%',
																overflow: 'auto',
																cursor: 'auto'
															}
												});

								}

								if(r)
								{
									totalExamenesAntesGrabar = formDiligenciado;
									var descripcion = $("#wselTipoServicioImp option:selected").text();
									parametros = "consultaAjaxKardex=49&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicioImp').options[ document.getElementById('wselTipoServicioImp').selectedIndex ].value+"&especialidad="+document.forms.forma.wespecialidad.value+"&descripcion="+descripcion;

									try{
										ajax=nuevoAjax();

										ajax.open("POST", "ordenes.inc.php",true);
										ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
										ajax.send(parametros);

										ajax.onreadystatechange=function()
										{
											if (ajax.readyState==4 && ajax.status==200){
												var strItem = $.trim( ajax.responseText );

												var item = strItem.split('|');

												seleccionarAyudaDiagnosticaAlta(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);

												this.onfocus = function(){
													if( justificacionUltimoExamenAgregado ){
														justificacionUltimoExamenAgregado.focus();
														justificacionUltimoExamenAgregado = '';
													}
												};

												document.getElementById("btnCerrarVentana").style.display = 'none';


											}
										}
										if ( !estaEnProceso(ajax) ) {
											ajax.send(null);
										}
									}catch(e){	}

								var urlform = 'HCE.php?accion=M&ok=0&empresa='+document.forms.forma.wbasedatohce.value+'&origen='+document.forms.forma.wemp_pmla.value+'&wdbmhos='+document.forms.forma.wbasedato.value+'&wformulario='+formTipoOrden+'&wcedula='+document.forms.forma.wcedula.value+'&wtipodoc='+document.forms.forma.wtipodoc.value;

												//window.open(urlform,'_blank');
												$.blockUI({ message: $('<iframe src="'+urlform+'" width="950px" height="95%" scrolling="yes" frameborder="0" align="center"></iframe><div align="center"><input type="button" name="cerrarvtnhce" id="cerrarvtnhce" onClick="cerrarFormHCE('+cuentaExamenes+',\''+document.forms.forma.wbasedatohce.value+'\',\''+formTipoOrden+'\',\''+historia+'\',\''+ingreso+'\',\'alta\',\'on\' )" value="Cerrar ventana" /></div>'),
															css: {
																top:  '5%',
																left: '10%',
																width: '950px',
																height: '90%',
																overflow: 'auto',
																cursor: 'auto'
															}
												});

								}
								else
								{
									var select = $('#wselTipoServicioImp');
									select.val($('option:first', select).val());

								}



							}
						}
						if ( !estaEnProceso(ajax) ) {
							ajax.send(null);
						}
					}catch(e){	}

					//////////////////////////////////////////////////////////

				}
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}


	//Autocomplete para Ayudas y procedures
    if(document.getElementById('wnomprocimp')){

		$("#wnomprocimp").flushCache();
    	$("#wnomprocimp").unbind("result");

		// 2012-06-27
		// Se comenta y se reemplaza por la siguiente línea debido a que estaba trayende 2 valores por cada selección que se hacia
		// Cuando se desplegaba las opciones y se seleccionaba y daba 'Enter' en una, se agregaba ésta y la anterior a las ordenes
		// Esto pasaba cuando se daba 'Enter', no pasaba si se hacia clic
		// $("#wnomprocimp").setOptions({url:"ordenes.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value});
    	$("#wnomprocimp").setOptions({url:"ordenes.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&ccoPaciente="+document.forms.forma.wservicio.value});

    	$("#wnomprocimp").autocomplete("ordenes.inc.php?consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+document.getElementById('wselTipoServicioImp').options[ document.getElementById('wselTipoServicioImp').selectedIndex ].value+"&ccoPaciente="+document.forms.forma.wservicio.value, {
    		cacheLength:0,
			max: 100,
    		scroll: false,
    		scrollHeight: 500,
    		matchContains: true,
    		width:500,
    		minChars: 3,
    		autoFill:false,
    		formatResult: function(data, value) {
				return value;
			}
    	}).result(function(event, item) {

    		seleccionarAyudaDiagnosticaAlta(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);

    		this.onfocus = function(){
    			if( justificacionUltimoExamenAgregado ){
    				justificacionUltimoExamenAgregado.focus();
    				justificacionUltimoExamenAgregado = '';
    			}
    		};

			document.getElementById("btnCerrarVentana").style.display = 'none';

    	});
    }
}


/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function cerrarModalHCE(){
	window.parent.parent.parent.$.unblockUI();
	if( $( "#programa" ).length > 0 ){
		window.close();
	}
}

function activarModalIframe(path){


		$.blockUI({ message:	 $('<iframe src="'+path+'"></iframe>'),
						css: 	{ left: '0px',
								  top:  '0px',
								  width: '0%',
								  cursor: ''
								 }
				 });


	}


/******************************************************************************************************************************
 *Redirecciona a la pagina inicial del kardex
 ******************************************************************************************************************************/
function inicio(){
	var nroDocumento = document.forms.forma.wcedula.value;
	var tipoDocumento = document.forms.forma.wtipodoc.value;

	//document.location.href='ordenes.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&wcedula='+nroDocumento+'&wtipodoc='+tipoDocumento+'&wfecha='+document.forms.forma.wfecha.value;

	document.location.href='HCE_iframes.php?accion=M&ok=0&empresa=hce&wcedula='+nroDocumento+'&wtipodoc='+tipoDocumento;

}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function agregarSeccionAcordeon(idAcordeon){
	$('#accordion').append('<h3><a href="#">Laboratorio</a></h3><div><p>Hemoclasificacion</p></div>').accordion('destroy').accordion();

}
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function abrirModalHCE(){
	var titulo = "Vistas asociadas";
	var nombre = "modalOrdenes";

	if(parent.parent.demograficos.document.all.txtformulario){
		parent.parent.demograficos.document.all.txtformulario.value = "ORDENES MEDICAS";
	}

	//Parametros que se requieren en la URL
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var empresa = document.forms.forma.wempresa.value;

	var nroDocumento = document.forms.forma.wcedula.value;
	var tipoDocumento = document.forms.forma.wtipodoc.value;

	var url = "http://localhost/matrix/hce/procesos/HCE.php?accion=W2&ok=0";

	url += "&empresa="+empresa;
	url += "&wcedula="+nroDocumento;
	url += "&wtipodoc="+tipoDocumento;
	url += "&whis="+historia;
	url += "&wing="+ingreso;
	url += "&wtitframe=no";

	var alto = "400";
	var ancho = "400";

	mostrarFlotante(titulo,nombre,url,alto,ancho);

}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function cerrarModalHCE(){
	window.parent.parent.parent.$.unblockUI();

	if( $( "#programa" ).length > 0 ){
		window.close();
	}
}

function cerrarModalGestion(){
	window.parent.parent.parent.$.unblockUI();
	timer = window.parent.parent.parent.setTimeout( "recargar();", 5*60000 );

	if( $( "#programa" ).length > 0 ){
		window.close();
	}
}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function deshabilitarBotonesFirmaDigitalHCE(estado){
//	document.getElementById('wcconf').disabled = estado;
	document.getElementById('wconfdisp').disabled = estado;
	//document.getElementById('btnGrabar1').disabled = estado;
    if (estado) {
        $('#btnGrabar1').hide();
        $('#btnGrabarAux').show();
    } else {
        $('#btnGrabar1').show();
        $('#btnGrabarAux').hide();
    }
	//document.getElementById('btnGrabar2').disabled = estado;

	if(estado){
		document.getElementById('whfirma').value = 'N';
	} else {
		document.getElementById('whfirma').value = 'S';
	}
}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function validarFirmaDigitalHCE(){
	var firma = document.getElementById('pswFirma').value;
	var usuario = document.forms.forma.usuario.value;

	deshabilitarBotonesFirmaDigitalHCE(true);
//	debugger;
	if(firma != "" && firma.length > 0 ){

		firma = hex_sha1(firma);

		//Consulta ajax para validacion de firma digital
		validarFirmaDigital(usuario,firma);
	}
	else if( firma == "" ){
		document.getElementById('tdEstadoFirma').className = 'fila1';
		document.getElementById('tdEstadoFirma').innerHTML = "";
		deshabilitarBotonesFirmaDigitalHCE(true);
	}
	else {
		document.getElementById('tdEstadoFirma').className = 'fondorojo';
		document.getElementById('tdEstadoFirma').innerHTML = " &nbsp; Firma Err&oacute;nea ";
		deshabilitarBotonesFirmaDigitalHCE(true);
	}


}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function consultarAyudasDiagnosticas(){
	var contenedor = document.getElementById('cntExamenes');
	var unidadRealiza = document.getElementById('wservexamen');
	var nombreAyuda = document.getElementById('wnomayu');

	var vecServicio = unidadRealiza.value.split('|');

	var parametros = "consultaAjaxKardex=27&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre="+nombreAyuda.value+"&unidadRealiza="+vecServicio[0]+"&ccoPaciente="+document.forms.forma.wservicio.value;

		try{
			$.blockUI({ message: $('#msjEspere') });
			ajax=nuevoAjax();

			ajax.open("POST", "ordenes.inc.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			ajax.onreadystatechange=function(){
				if (ajax.readyState==4 && ajax.status==200){
					contenedor.innerHTML=$.trim( ajax.responseText );
				}
				$.unblockUI();
			}
			if ( !estaEnProceso(ajax) ) {
				ajax.send(null);
			}
		}catch(e){	}
}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
	//posiciones	0	            ,   1   ,    2   ,                3                    ,   4  ,  5    ,     6   , 7,  8 ,   9  ,  10,     11
				//["BIOPSIA DE MAMA CON AGU... ", "A01", "851102", "BIOPSIA_DE_MAMA_CON_AGUJA_TRU_-CUT", "A01", "A064", "851102", "", "", "off", "off", "off"]
							          //item[1]  , item[2] ,   item[3]  ,  item[4]  , item[5], item[6]  ,     item[7]    ,    item[8]       ,      item[9]      ,        item[10]      ,item[11]
// funcInternas es una funcion que se ejecuta una vez termine el llamado al funcion agregarExamen y es opcional. Esta función no tiene parametros.
function seleccionarAyudaDiagnostica(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria, requiereJustificacion, noPos, generaOrdenIndividual, aydJustificacion, funcInternas, datosAdicionales, esOfertado, realizaUnidad ){

	var datosAdicionales = datosAdicionales || {};
	var esOfertado 		 = esOfertado || false;
	
	//Indica si debe mostrar modal al médico para que indique si el estudio se realiza en la unidad del paciente o en laboratorio
	var realizaUnidad 	 = realizaUnidad || false;	
	
	if( realizaUnidad == 'on' ){
		
		realizaUnidad = true;
		
		if( $( "#ccoRealizaEstudios" ).val() != 'on' ){
			realizaUnidad = false;
		}
	}
	else{
		realizaUnidad = false;
	}

	//Busco si ya existe el tipo de orden
	var auxTipoOrden = "del"+centroCostos;

	if( $( "[newExam=on][id^="+auxTipoOrden+"]" ).length > 0 && generaOrdenIndividual != 'on' ){
		//Si Existe tipo de orden la creo
		consecutivoOrden = $( "[newExam=on][id^="+auxTipoOrden+"]" ).eq( 0 ).attr( "id" ).substr( auxTipoOrden.length );
	}

	justificacionUltimoExamenAgregado = '';

	var esHospitalario = esAyudaHospitalaria == "on" ? true : false;

	document.getElementById('wnomproc').value = '';

	var iHTMLExamen = "";

	var tablaContenedora = document.getElementById( "examPendientes" );

	//Verifico que exista la unidad diagnostica
	var elementoCentroCostos = document.getElementById("detExamenes"+centroCostos);
	var elementoOrden = document.getElementById("del"+centroCostos+""+consecutivoOrden);
	var contenedor = document.getElementById("cntOtrosExamenes");

	var crearOrden = false;
	var crearCco = false;
	var crearTabla = false;
	var crearExamen = true;

	//Existe centro costos
	if(!document.getElementById("detExamenes"+centroCostos)){
		crearCco = true;
	}

	//Existe orden
	if(!document.getElementById("del"+centroCostos+""+consecutivoOrden)){
		crearOrden = true;
	}

	//Existe orden
	if( generaOrdenIndividual != 'on' )
	{
		if(!document.getElementById("del"+centroCostos+""+consecutivoOrden)){
			crearOrden = true;
		}
	}
	else
	{
		//Se comenta el while ya que estaba creando un bucle infinito cuando la procedimiento genera orden individual. Jonatan Lopez 23 Abril 2014.
		//while( document.getElementById("del"+centroCostos+""+consecutivoOrden) ){
		  // consecutivoOrden++;
			crearOrden = true;
		//}
	}


	if(elementoCentroCostos){
		if(!elementoOrden){
			crearOrden = true;
			crearExamen = true;
		}
	} else {
		crearCco = true;
		crearOrden = true;
		crearExamen = true;
	}

	//CREACION DE LOS ELEMENTOS
	if(!crearOrden){
		iHTMLOrden = "";
	}
	if(!crearExamen){
		iHTMLExamen = "";
	}

	if(crearCco){
		var subContenedor = document.createElement("tbody");
		subContenedor.setAttribute('id','detExamenes'+centroCostos);

		//Link
		if(esHospitalario){
			//subContenedor.innerHTML += "<br><a href='#null' onClick='intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>HOSPITALARIAS</u></center></font></b></a>";
		} else {
			//subContenedor.innerHTML += "<br><a href='#null' onClick='intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>"+nombreCentroCostos+"</u></center></font></b></a>";
		}

		iHTMLOrden = "";
		iHTMLExamen = "";

		//Concatenar examenes
		//var texto = "<div id='"+centroCostos+"'><br>"+tablaContenedora+"<tr id=del"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+iHTMLExamen+"</table>"+"</div>";
		var texto = "<tr id='trEx"+cuentaExamenes+"'><td id=del"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+iHTMLExamen+"</td>"+"</tr>";

		//Clausura de span
		subContenedor.innerHTML += texto;
		subContenedor.innerHTML = "";

		tablaContenedora.appendChild(subContenedor);
	} else {

		if(crearOrden){
			//document.getElementById(centroCostos).innerHTML += iHTMLOrden;
		}

		if(false && crearTabla){
			//document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += tablaContenedora+iHTMLExamen;
		} else {
			//document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += iHTMLExamen;
		}
	}

	try{
		$( elementoCentroCostos ).show();
	}
	catch(e){}
	//$("#acExamenes").accordion( "activate" , 1 )
	
	
	
	
	// /**************************************************************************************************
	 // * Aquí muestro la modal con los datos adicionales del examen
	 // **************************************************************************************************/
	// //Muestro la modal para mostrar los datos adicionales

	// //Creo el objeto que tiene la funcionalidad que muestra los datos adicionales del examen
	// var a = new mad({ 
					// close	: function(){ $.unblockUI();  },
					// accept	: function( objMad ){
							
								// console.log( objMad.result() );
							
								// //Creo el examen en la tabla correspondiente
								// agregarExamen(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, 0, crearOrden,requiereJustificacion, noPos, aydJustificacion, funcInternas, objMad.result() );
								// document.getElementById("btnCerrarVentana").style.display = 'none';
						
								// $.unblockUI(); 
						// },
					// title	: 'Datos adicionales',
					// data	: datosAdicionales,
				// });
	
	// //Si hya por lo menos un dato no seleccionado muestro la modal, caso contrario se graba el examen
	// a.create()
	
	
	// var datosOk = true;
	// if( $( "select", a.obj ).length > 0 ){
		// $( "select", a.obj ).each(function(){
			// if( $( this ).val() == '' || $( this ).val() == null || !$( this ).val() )
				// datosOk = false;
		// })
	// }

	// if( !datosOk ){
		// // $( document.body ).append( a.obj )

		// $.blockUI({ 
			// message	: a.obj ,
			// css : { width:'80%', 
					// left: '10%',
					// },
		// });
	// }
	// else{
		// //Creo el examen en la tabla correspondiente
		// agregarExamen(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, 0, crearOrden,requiereJustificacion, noPos, aydJustificacion, funcInternas, a.result() );
		// document.getElementById("btnCerrarVentana").style.display = 'none';
	// }

	// /***********************************************************************************************/





	
	// Creo el examen en la tabla correspondiente
	agregarExamen(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, 0, crearOrden,requiereJustificacion, noPos, aydJustificacion, funcInternas, datosAdicionales, esOfertado, realizaUnidad );

	document.getElementById("btnCerrarVentana").style.display = 'none';

	return false;
}

function seleccionarAyudaDiagnosticaAlta(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria, requiereJustificacion, noPos, generaOrdenIndividual, aydJustificacion ){


	//Busco si ya existe el tipo de orden
	var auxTipoOrden = "del"+centroCostos;

	if( $( "[id^="+auxTipoOrden+"]" ).length > 0 && generaOrdenIndividual != 'on' ){
		//Si Existe tipo de orden la creo
		consecutivoOrden = $( "[id^="+auxTipoOrden+"]" ).eq( 0 ).attr( "id" ).substr( auxTipoOrden.length );
	}


	justificacionUltimoExamenAgregado = '';

	var esHospitalario = esAyudaHospitalaria == "on" ? true : false;

	var iHTMLExamen = "";

	var tablaContenedora = document.getElementById( "examPendientesImp" );

	//Verifico que exista la unidad diagnostica
	var elementoCentroCostos = document.getElementById("detExamenes"+centroCostos+"Imp");
	var elementoOrden = document.getElementById("delImp"+centroCostos+""+consecutivoOrden);
	var contenedor = document.getElementById("cntOtrosExamenes");

	var crearOrden = false;
	var crearCco = false;
	var crearTabla = false;
	var crearExamen = true;

	//Existe centro costos
	if(!document.getElementById("detExamenes"+centroCostos+"Imp")){
		crearCco = true;
	}

	//Existe orden
	if(!document.getElementById("delImp"+centroCostos+""+consecutivoOrden)){
		crearOrden = true;
	}

	//Existe tabla contenedora
	// se comenta porque la tabla contenedora siempre va a existir
	/*
	if(!document.getElementById("detExamenes"+centroCostos+""+consecutivoOrden)){
		crearTabla = true;
	}
	*/

	if(elementoCentroCostos){
		if(!elementoOrden){
			crearOrden = true;
			crearExamen = true;
		}
	} else {
		crearCco = true;
		crearOrden = true;
		crearExamen = true;
	}

	//CREACION DE LOS ELEMENTOS
	if(!crearOrden){
		iHTMLOrden = "";
	}
	if(!crearExamen){
		iHTMLExamen = "";
	}

	if(crearCco){
		var subContenedor = document.createElement("tbody");
		subContenedor.setAttribute('id','detExamenes'+centroCostos+"Imp");

		//Link
		if(esHospitalario){
			//subContenedor.innerHTML += "<br><a href='#null' onClick='intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>HOSPITALARIAS</u></center></font></b></a>";
		} else {
			//subContenedor.innerHTML += "<br><a href='#null' onClick='intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>"+nombreCentroCostos+"</u></center></font></b></a>";
		}

		iHTMLOrden = "";
		iHTMLExamen = "";

		//Concatenar examenes
		//var texto = "<div id='"+centroCostos+"'><br>"+tablaContenedora+"<tr id=del"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+iHTMLExamen+"</table>"+"</div>";
		//var texto = "<tr id='trExImp"+cuentaExamenes+"'><td id=delImp"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+iHTMLExamen+"</td>"+"</tr>";

		//Clausura de span
		//subContenedor.innerHTML += texto;

		tablaContenedora.appendChild(subContenedor);
	} else {

		if(crearOrden){
			//document.getElementById(centroCostos).innerHTML += iHTMLOrden;
		}

		if(false && crearTabla){
			//document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += tablaContenedora+iHTMLExamen;
		} else {
			//document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += iHTMLExamen;
		}
	}

	try{
		$( elementoCentroCostos ).show();
	}
	catch(e){}
	//$("#acExamenes").accordion( "activate" , 1 )

	//Creo el examen en la tabla correspondiente
	agregarExamenAlta(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, 0, crearOrden,requiereJustificacion, noPos, aydJustificacion );

	document.getElementById("btnCerrarVentana").style.display = 'none';

	return false;
}

//function seleccionarAyudaDiagnostica(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos,esAyudaHospitalaria){
//
//	justificacionUltimoExamenAgregado = '';
//
//	var esHospitalario = esAyudaHospitalaria == "on" ? true : false;
//
//	//HTML de la orden
//	var iHTMLOrden = "<span id='del"+centroCostos+""+consecutivoOrden+"'>";
//	iHTMLOrden += "<br><a href='#null' onclick=intercalarElemento(\""+centroCostos+""+consecutivoOrden+"\"); class='fila2'>";
//	iHTMLOrden += "<b><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Orden #"+consecutivoOrden+"</u></b></a>&nbsp;&nbsp;&nbsp;";
//
////	iHTMLOrden += "<a href='#null' onclick='cancelarOrden(\""+centroCostos+"\",\""+consecutivoOrden+"\");'><img src='../../images/medical/root/borrar.png'/></a>";
//	var atr = new Array();
//	atr['onClick'] = "cancelarOrden('"+centroCostos+"','"+consecutivoOrden+"');";
//	iHTMLOrden += crearCampo("4","","4.3",atr,"<img src='../../images/medical/root/borrar.png'>");
//
//	iHTMLOrden += "<div id=\""+centroCostos+""+consecutivoOrden+"\" class='fila2'>";
//	iHTMLOrden += "<div style='display:none'><br>Observaciones de la orden: <br>";
//
////	iHTMLOrden += "<textarea id='wtxtobsexamen1' rows='2' cols='60' onkeypress='return validarEntradaAlfabetica(event);'></textarea>";
//	var atr = new Array();
//	atr['rows'] = "2";
//	atr['cols'] = "60";
//	atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";
//
//	iHTMLOrden += crearCampo("2","wtxtobsexamen"+centroCostos+""+consecutivoOrden,"4.4",atr,".");
//
//	iHTMLOrden += "</div></span>";
//
//	var iHTMLExamen = "";
//
//	var tablaContenedora = "<table align='center'><tr align='center'>";
//	tablaContenedora += "<td class='encabezadoTabla'>Acciones</td>";
//	tablaContenedora += "<td class='encabezadoTabla'>Ayuda o procedimiento</td>";
//	tablaContenedora += "<td class='encabezadoTabla'>Justificacion</td>";
//	tablaContenedora += "<td class='encabezadoTabla' style='display:none'>Resultado</td>";
//	tablaContenedora += "<td class='encabezadoTabla'>Realizar el dia</td>";
//	tablaContenedora += "<td class='encabezadoTabla'>Estado</td>";
//	tablaContenedora += "</tr>";
//	tablaContenedora += "<tbody id='detExamenes"+centroCostos+""+consecutivoOrden+"'>";
//	tablaContenedora += "</tbody>";
//	tablaContenedora += "</table>";
//
//	//Verifico que exista la unidad diagnostica
//	var elementoCentroCostos = document.getElementById(centroCostos);
//	var elementoOrden = document.getElementById(centroCostos+""+consecutivoOrden);
//	var contenedor = document.getElementById("cntOtrosExamenes");
//
//	var crearOrden = false;
//	var crearCco = false;
//	var crearTabla = false;
//	var crearExamen = true;
//
//	//Existe centro costos
//	if(!document.getElementById(centroCostos)){
//		crearCco = true;
//	}
//
//	//Existe orden
//	if(!document.getElementById(centroCostos+""+consecutivoOrden)){
//		crearOrden = true;
//	}
//
//	//Existe tabla contenedora
//	if(!document.getElementById("detExamenes"+centroCostos+""+consecutivoOrden)){
//		crearTabla = true;
//	}
//
//	if(elementoCentroCostos){
//		if(!elementoOrden){
//			crearOrden = true;
//			crearExamen = true;
//		}
//	} else {
//		crearCco = true;
//		crearOrden = true;
//		crearExamen = true;
//	}
//
//	//CREACION DE LOS ELEMENTOS
//	if(!crearOrden){
//		iHTMLOrden = "";
//	}
//	if(!crearExamen){
//		iHTMLExamen = "";
//	}
//
//	if(crearCco){
//		var subContenedor = document.createElement("div");
//
//		//Link
//		if(esHospitalario){
//			subContenedor.innerHTML += "<a href='#null' onClick='intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>Ordenes hospitalarias</u></center></font></b></a>";
//		} else {
//			subContenedor.innerHTML += "<a href='#null' onClick='intercalarElemento(\""+centroCostos+"\");' class='subtituloPagina2'><b><font size=4><center><u>Ordenes de "+nombreCentroCostos+"</u></center></font></b></a>";
//		}
//
//		//Concatenar examenes
//		subContenedor.innerHTML += "<div id='"+centroCostos+"'><br><span id=del"+centroCostos+consecutivoOrden+" class=fila2>"+iHTMLOrden+tablaContenedora+iHTMLExamen;
//
//		//Clausura de span
//		subContenedor.innerHTML += "</span>";
//
//		//Clausura de div
//		subContenedor.innerHTML += "</div>";
//
//		contenedor.appendChild(subContenedor);
//	} else {
//		if(crearOrden){
//			document.getElementById(centroCostos).innerHTML += iHTMLOrden;
//		}
//
//		if(crearTabla){
//			document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += tablaContenedora+iHTMLExamen;
//		} else {
//			document.getElementById(centroCostos+""+consecutivoOrden).innerHTML += iHTMLExamen;
//		}
//	}
//
//	try{
//		$( elementoCentroCostos ).show();
//	}
//	catch(e){}
//	//$("#acExamenes").accordion( "activate" , 1 )
//
//	//Creo el examen en la tabla correspondiente
//	agregarExamen(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, 0 );
//
//	return false;
//}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function cancelarOrden(centroCostos,consecutivoOrden){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;

	//if( confirm("Esta seguro de cancelar la orden "+consecutivoOrden+"?")){
	jConfirm( "Esta seguro de cancelar la orden "+consecutivoOrden+"?", 'Cancelar orden', function(r) {
		 if(r){
			/****************************************************************************************************************
			 * Creo la acción para ejecutarla al momento de guardar
			 ****************************************************************************************************************/
			if( !accionesOrdenes[ ELIMINAR_ORDEN_EXAMEN ] ){
				accionesOrdenes[ ELIMINAR_ORDEN_EXAMEN ] = {};
			}

			if( !accionesOrdenes[ ELIMINAR_ORDEN_EXAMEN ][ centroCostos+"-"+consecutivoOrden ] ){
				accionesOrdenes[ ELIMINAR_ORDEN_EXAMEN ][ centroCostos+"-"+consecutivoOrden ] = new Array();

				var aux = accionesOrdenes[ ELIMINAR_ORDEN_EXAMEN ][ centroCostos+"-"+consecutivoOrden ];
				aux[ aux.length ] = historia; 			//parametro1
				aux[ aux.length ] = ingreso; 			//parametro2
				aux[ aux.length ] = fecha; 				//parametro3
				aux[ aux.length ] = usuario; 			//parametro4
				aux[ aux.length ] = centroCostos; 		//parametro5
				aux[ aux.length ] = consecutivoOrden; 	//parametro6
			}
			/****************************************************************************************************************/

			/****************************************************************************************************************
			 * Agosto 12 de 2014
			 ****************************************************************************************************************/
			var contenedorCentroCostos = document.getElementById("detExamenes"+centroCostos);

			var tabla = contenedorCentroCostos.parentNode;
			var celdaExamen = document.getElementById("del"+centroCostos+""+consecutivoOrden);

			var totalExamenes = celdaExamen.rowSpan;
			var index = celdaExamen.parentNode.rowIndex;

			for( var i = 0; i < totalExamenes; i++ ){
				// grabarExamenADetalle( $( "[id^=wnmexamen]" , tabla.rows[ index ] ).attr( "id" ).substr( 9 ), '4' );
				contenedorCentroCostos.removeChild( tabla.rows[ index ] );
			}

			// mensaje = "Orden cancelada con exito";
			/******************************************************************************************************************/

			// cancelarOrdenElemento(historia,ingreso,fecha,usuario,centroCostos,consecutivoOrden);
		}
	});
}

/*****************************************************************************************************************************
 * Intercala ordenes desplegadas ya desplegar
 ******************************************************************************************************************************/
function intercalarOrdenAnterior(idElemento)
   {
    //$("#"+idElemento).toggle("normal");

    if ( document.getElementById(idElemento).style.display=='')
       {
    	document.getElementById(idElemento).style.display='none';
       }
      else
        {
	     document.getElementById(idElemento).style.display='';
        }

    //<!--$("#ex"+idElemento).toggle("normal"); -->
   }


/*****************************************************************************************************************************
 * Efecto accordion en examenes realizados
 ******************************************************************************************************************************/
   function recarga()
     {
	  var dvAux = document.createElement( "div" );

	  dvAux.innerHTML = "<INPUT type='hidden' name='mostrar'>";
      dvAux.firstChild.value = document.getElementById( "Ordenes" ).innerHTML;
      document.forms[0].appendChild( dvAux.firstChild );
      document.forms[0].submit();
     }


/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function grabarOrden(centroCostos,consecutivoOrden){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;
	var observacionesOrden = "";  //document.getElementById("wtxtobsexamen"+centroCostos+""+consecutivoOrden).value;

	grabarOrdenElemento(historia,ingreso,fecha,usuario,centroCostos,consecutivoOrden,observacionesOrden);
}
/*****************************************************************************************************************************
 * Intercala cualquier elemento en pantalla
 ******************************************************************************************************************************/
function intercalarElemento(idElemento){
	$("#"+idElemento).toggle("normal");
}
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function intercalarExamenAnterior(idElemento){
    $("#ex"+idElemento).toggle("normal");
}
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function intercalarMedicamentoAnterior(idElemento,tipoProtocolo){
			if(filaIdAnt != "" && document.getElementById(filaIdAnt).display != 'none')
			{
				// Se oculta la fila desplegada anteriormente
				document.getElementById(filaIdAnt).style.display = 'none';

				// Se reestablece la clase original de la fila anterior
				document.getElementById(filaTituloIdAnt).className = claseFilaAnt;
			}

			if(divIdAnt != "" && document.getElementById(divIdAnt).display != 'none')
			{
				// Se oculta el div desplegado anteriormente
				document.getElementById(divIdAnt).style.display = 'none';
			}


	$("#medAnt"+tipoProtocolo+idElemento).toggle("normal");
}
/*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function confirmarGeneracion(){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var whgrabado = document.forms.forma.whgrabado;

	if(historia && ingreso){

		//Deshabilita el boton de generacion
		var boton = document.getElementById("btnConfirmar");
		if(boton){
			boton.disabled = true;
			boton.value = "Procesando, por favor espere...";
		}

		if(whgrabado && whgrabado.value != ''){
			document.location.href = 'ordenes.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha+'&whgrabado='+whgrabado.value;
		} else {
			document.location.href = 'ordenes.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha;
		}
	} else {
		alert("No se encontró historia, ingreso y fecha en los parametros de entrada.");
	}
}
/******************************************************************************************************************************
 *Enfoca al campo inicial
 ******************************************************************************************************************************/
function enfocarInicio(){
	if(document.getElementById("wthistoria")){
		document.getElementById("wthistoria").focus();
	}
}
/******************************************************************************************************************************
 *Valida que la diferencia de fechas entre el servidor y el cliente no sea mas de media hora
 ******************************************************************************************************************************/
function validarFechayHoraLocal(strFechaServidor,strHoraServidor){
	var valido = true;
	var fechaObj = new Date();
	var fechaJs = new Date();

	var fechaObjSvr = strFechaServidor.split("-");

	fechaObj.setFullYear(fechaObjSvr[0]);
	fechaObj.setMonth(eval(fechaObjSvr[1]-1));
	fechaObj.setDate(fechaObjSvr[2]);

	var horaObj = strHoraServidor.split(":");

	fechaObj.setHours(horaObj[0], horaObj[1], horaObj[2], 0);

	var diferencia = fechaObj.getTime() - fechaJs.getTime();
	var minutosDiferencia = Math.floor( diferencia / (1000 * 60));

	if(Math.abs(minutosDiferencia) > 30){
		valido = false;
	}

	if(!valido){
		alert("Recuerde que la fecha y hora del equipo local deben ser las actuales.  Son " + Math.abs(minutosDiferencia) + " minutos de diferencia");
	}
}

/*****************************************************************************************************************************
 * Este metodo encapsula la creación dinámica de cada campo, segun las acciones asociadas y su tipo.
 *
 * Orden de la serializacion de acciones:
 *
 * 1. Leer
 * 2. Actualizar
 * 3. Borrar
 * 4. Crear
 *
 * ::::::::::::::Parámetros::::::::::::::
 * String Tipo		: Permite evaluar el tipo de campo se va a generar
 * String Id		: Id y name especificos del campo
 * String idAcion	: Id del campo hidden que contiene la secuencia del CRUD
 * Array atributos	: Atributos del campo:  onClick, readonly, class...
 * String valor		: Valor del campo
 *
 * El valor de retorno es el innerHTML
 *****************************************************************************************************************************/
function crearCampo(tipo,id,idAccion,atributos,valor){
	var acciones = "S,S,S,S";
	var elemento = "";
	var clave = "";
	var vecAcciones = new Array();

	//Vector de acciones
	var wacciones = document.getElementById("wacc"+idAccion);
	if(wacciones){
		vecAcciones = wacciones.value.split(",");
	} else {
		vecAcciones = acciones.split(",");
	}

	switch(tipo){
		case '1':		//Texto
			elemento = "<input type=text name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += " value=\""+valor+"\" ";

			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " readonly=readonly disabled=disabled";
			}

			elemento += "/>";
			break;
		case '2':		//Textarea
			elemento = "<textarea name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " readonly=readonly ";
			}

			//Valor
			elemento += ">"+valor+"";
			elemento += "</textarea>";

			break;
		case '3':		//Boton
			elemento = "<input type='button' name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += " value='"+valor+"' ";

			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " disabled ";
			}
			elemento += "/>";

			break;
		case '4':		//Link
			elemento = "<a href='#null' name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += ">"+valor+"";
			elemento += "</a>";

			//Lectura
			if(vecAcciones[0] == "N"){
				elemento = " ";
			}
			break;
		case '5':		//Checkbox
			elemento = "<input type=checkbox name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += " value=\""+valor+"\" ";

			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " disabled ";
			}

			elemento += "/>";
			break;
		case '6':		//Select
			elemento = "<select name='"+id+"' id='"+id+"' ";

			//Lectura
			if(vecAcciones[0] == "N"){
				elemento += " disabled ";
			}

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			elemento += ">";

			//Valor
			elemento += " "+valor+" ";
			elemento += "</select>";

			break;
		case '7':		//Rama de arbol
			break;
		case '8':		//Link que al usarse en modo lectura sale unicamente el contenido del valor
			elemento = "<a href='#null' name='"+id+"' id='"+id+"' ";

			for(clave in atributos){
				if(atributos[clave] && atributos[clave] != ""){
					elemento += " "+clave+"=\""+atributos[clave]+"\" ";
				} else {
					elemento += " "+clave+" ";
				}
			}
			//Valor
			elemento += ">"+valor+"";
			elemento += "</a>";

			//Lectura
			if(vecAcciones[0] == "N"){
				elemento = valor;
			}
			break;
	}
	return elemento;
}
/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
function filtroAcciones(idAccion,funcion){
	var acciones = "S,S,S,S";
	var elemento = "";
	var clave = "";
	var vecAcciones = new Array();
	var retorno = "false";

	//Vector de acciones
	var wacciones = document.getElementById("wacc"+idAccion);
	if(wacciones){
		vecAcciones = wacciones.value.split(",");
	} else {
		vecAcciones = acciones.split(",");
	}

	//Lectura
	if(vecAcciones[0] == "S"){
		retorno = eval(funcion);
	}

	return retorno;
}
/******************************************************************************************************************************
 * Marca TODOS los articulos como aprobados
 ******************************************************************************************************************************/
function marcarAprobacionArticulos(estado){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;
	var estadoAprobacion = estado ? "on" : "off";

	var codigosArticulos = "";

	for(var indice = 1;document.getElementById('wchkare'+indice);indice++){
		if(document.getElementById('wchkare'+indice).disabled == false){
			document.getElementById('wchkare'+indice).checked = estado;

			//Extrae el codigo del articulo actual
		 	var codigoArticulo = document.getElementById('wnmmedact'+indice);
		 	var cd = "";
		 	if(codigoArticulo.tagName == 'INPUT'){
		 		cd = codigoArticulo.value.split("-");
		 	} else {
		 		cd = codigoArticulo.innerHTML.split("-");
		 	}
		 	codigosArticulos += cd[0]+";"+cd[1]+";"+document.getElementById('whfinicio'+indice).value.split(" a las:")[0]+";"+document.getElementById('whfinicio'+indice).value.split(" a las:")[1]+"|";
		}
	}
	grabarEstadoAprobacionArticulos(historia,ingreso,fecha,codigosArticulos,estadoAprobacion,usuario);
}
/******************************************************************************************************************************
* Marca un los articulo como aprobado
******************************************************************************************************************************/
function marcarAprobacionArticulo(idxElemento){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;
	var estadoAprobacion = document.getElementById('wchkare'+idxElemento).checked ? "on" : "off";

	var codigosArticulos = "";

	//Extrae el codigo del articulo actual
 	var codigoArticulo = document.getElementById('wnmmedact'+idxElemento);
 	var cd = "";

 	if(codigoArticulo.tagName == 'INPUT'){
 		cd = codigoArticulo.value.split("-");
 	} else {
 		cd = codigoArticulo.innerHTML.split("-");
 	}
 	codigosArticulos = cd[0]+";"+cd[1]+";"+document.getElementById('whfinicio'+idxElemento).value.split(" a las:")[0]+";"+document.getElementById('whfinicio'+idxElemento).value.split(" a las:")[1]+"|";

	grabarEstadoAprobacionArticulos(historia,ingreso,fecha,codigosArticulos,estadoAprobacion,usuario);
}
/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
function consultarKardex(){
	var nroDocumento = document.forms.forma.wcedula.value;
	var tipoDocumento = document.forms.forma.wtipodoc.value;

	var esFechaValida = esFechaMenorIgualAActual(document.forms.forma.wfecha.value);
	var whgrabado = document.getElementById("whgrabado");

	//Digitó historia
	if(!nroDocumento || nroDocumento == ''){
		alert("Debe especificar un número de documento de paciente válido.");
		return;
	}

	if(esFechaValida){
		var boton = document.getElementById("btnConsultar");

		if(boton){
			boton.disabled = true;
			boton.value = "Procesando, por favor espere...";
		}

		if(whgrabado && whgrabado.value != ''){
			document.location.href = 'ordenes.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&wcedula='+nroDocumento+'&wtipodoc='+tipoDocumento+'&wfecha='+document.forms.forma.wfecha.value+'&whgrabado='+whgrabado.value;
		} else {
			document.location.href = 'ordenes.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&wcedula='+nroDocumento+'&wtipodoc='+tipoDocumento+'&wfecha='+document.forms.forma.wfecha.value;
		}
	} else {
		alert("La fecha ingresada debe ser igual o anterior a la fecha actual");
	}
}
/*****************************************************************************************************************************
 *Detecta el cierre de la ventana del kardex para evitar que se pierdan los datos
 ******************************************************************************************************************************/
function salida(){
	if(true || document.getElementById("fixeddiv2")){
		if(true || !document.getElementById( "wcconf" ).checked){
//			alert( "Se grabará y cerrará la orden, tenga en cuenta que este NO HA SIDO confirmada." );
		}

		grabarKardex();
	}
}
/*****************************************************************************************************************************
 *Detecta el cierre de la ventana del kardex para evitar que se pierdan los datos
 ******************************************************************************************************************************/
function marcarKardexConfirmado(){
	var confirmadoArriba = document.getElementById("wcconf");
	var confirmadoAbajo = document.getElementById("wconfdisp");

	if(confirmadoArriba){
		confirmadoAbajo.checked = confirmadoArriba.checked;
	}
}
/*****************************************************************************************************************************
 * Comparacion de fechas anteriores para calendario
 ******************************************************************************************************************************/
 function compareDatesOnly(date1, date2) {
		var year1 = date1.getFullYear();
		var year2 = date2.getFullYear();
		var month1 = date1.getMonth();
		var month2 = date2.getMonth();
		var day1 = date1.getDate();
		var day2 = date2.getDate();

		if (year1 > year2) {
			return -1;
		}
		if (year2 > year1) {
			return 1;
		}

		//years are equal
		if (month1 > month2) {
			return -1;
		}
		if (month2 > month1) {
			return 1;
		}

		//years and months are equal
		if (day1 > day2) {
			return -1;
		}
		if (day2 > day1) {
			return 1;
		}

		//days are equal
		return 0;


		/* Can't do this because of timezone issues
		var days1 = Math.floor(date1.getTime()/Date.DAY);
		var days2 = Math.floor(date2.getTime()/Date.DAY);
		return (days1 - days2);
		*/
	}
/*****************************************************************************************************************************
 * FUNCIONES Y METODOS
 ******************************************************************************************************************************/
 /*****************************************************************************************************************************
 * Invocación generica del calendario para fecha inicial de suministro
 ******************************************************************************************************************************/
function calendario(idx,tipoProtocolo){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicio'+tipoProtocolo+idx,button:'btnFecha'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});
}

function calendarioimp(idx,tipoProtocolo){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicioimp'+tipoProtocolo+idx,button:'btnFechaimp'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});
}

function calendarioLevIC(idx){
	Zapatec.Calendar.setup({
		weekNumbers:false,
		showsTime:true,
		timeFormat:'24',
		electric:false,
		inputField:'txFecIni'+idx,
		button:'btnFecIni'+idx,
		ifFormat:'%Y-%m-%d a las:%H:00',
		daFormat:'%Y/%m/%d',
		timeInterval:120,
		dateStatusFunc:fechasDeshabilitadas,
		onSelect:alSeleccionarFecha
	});
}

/*****************************************************************************************************************************
 * Invocación generica del calendario para fecha final de suministro
 ******************************************************************************************************************************/
function calendario2(idx){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'24',electric:false,inputField:'wffinal'+idx,button:'btnFechaFin'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
}
/*****************************************************************************************************************************
 * Invocacion generica del calendario para la fecha de realización examen
 ******************************************************************************************************************************/

function calendario3imp(idx){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfsolimp'+idx,button:'btnFechaSolImp'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',dateStatusFunc:fechasDeshabilitadas});

	return false;
}

/*****************************************************************************************************************************
 * Invocacion generica del calendario para la fecha de realización examen
 ******************************************************************************************************************************/
function calendario4(idx){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfliq'+idx,button:'btnFechaLiq'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',dateStatusFunc:fechasDeshabilitadas});
}

function calendario5(idx,tipoProtocolo){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicioaplicacion',button:'btnFecha'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});
}

function calendario6(idx,tipoProtocolo){
	Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:'wfinicioaplicacionimp',button:'btnFechaimp'+tipoProtocolo+idx,ifFormat:'%Y-%m-%d a las:%H:00',daFormat:'%Y/%m/%d',timeInterval:120,dateStatusFunc:fechasDeshabilitadas,onSelect:alSeleccionarFecha});
}

//****************************** Calendario examenes ***************************

function calendario_fecha_orden(tipocalendario){

	  $('.'+tipocalendario).datepicker({ minDate: -0 });

}


function hora_orden(tipocalendario1){

	 $('.'+tipocalendario1).timepicker({
                hourText: 'Hora',
                minuteText: 'Minuto',
                amPmText: ['AM', 'PM'],
                closeButtonText: 'Aceptar',
                nowButtonText: 'Ahora',
                deselectButtonText: 'Deseleccionar',
                defaultTime: 'now'
            });

}

/*****************************************************************************************************************************
 * Detiene la ejecucion de un evento
 ******************************************************************************************************************************/
function stopEvent(e){
    if (!e) e = window.event;
    try{
    	if (e.stopPropagation) {
    		e.stopPropagation();
    		e.preventDefault();
    	} else {
    		e.returnValue = false;
    	}
    }catch(e){}
}
/*****************************************************************************************************************************
  * Invocacion generica del calendario para la fecha de realización examen
  ******************************************************************************************************************************/
function fechasDeshabilitadas(date, year, month, day, hours, minutes){
	var now = new Date();
	var desactivar = false;
	var comparacion = compareDatesOnly(now, date);

	//Limita que no se seleccionen dias anteriores
	if (comparacion < 0) {
		desactivar = true;
	}

	//Limita que no se seleccionen horas anteriores
	if (typeof(hours) != "undefined") {
		if ((hours <= now.getHours()) && comparacion < 0) {
			desactivar = true;
		}
	}

	return desactivar;
}
/*****************************************************************************************************************************
 * Validacion de que las fechas y horas del servidor y del cliente no difieran mas de media hora
 ******************************************************************************************************************************/
function validarDiferenciaFechasServidorCliente(fechaServidor, horaServidor){

	var valido = true;
	var fechaObj = new Date();
	var fechaJs = new Date();

	var fechaObjSvr = fechaServidor.split("-");

	fechaObj.setFullYear(fechaObjSvr[0]);
	fechaObj.setMonth(eval(fechaObjSvr[1]-1));
	fechaObj.setDate(fechaObjSvr[2]);

	var horaObj = horaServidor.split(":");

	fechaObj.setHours(horaObj[0], horaObj[1], horaObj[2], 0);

	var diferencia = fechaObj.getTime() - fechaJs.getTime();
	var minutosDiferencia = Math.floor( diferencia / (1000 * 60));

	if(Math.abs(minutosDiferencia) > 30){
		valido = false;
	}
	return valido;
}

/*****************************************************************************************************************************
 * Carga de archivo de imagen de formula medica
 ******************************************************************************************************************************/
function cargarArchivo(){
	var formulario = document.getElementById('file_upload_form');
	var vinculo = document.getElementById('lkRuta');

	if(formulario){
		formulario.target = "upload_target";
		if(vinculo){
			vinculo.innerHTML = "";
		}
	}
	formulario.submit();
}
/*****************************************************************************************************************************
 * Captura de onEnter con llamada a funcion parametrizada con validacion de numeros entera
 ******************************************************************************************************************************/
function teclaEnterEntero(e,accion){
	var respuesta = validarEntradaEntera(e);
	var tecla = (document.all) ? e.keyCode : e.which;

	if(respuesta && tecla==13){
		eval(accion);
//		this[accion]();
	}
	return respuesta;
}
 /*****************************************************************************************************************************
  * Captura de onEnter con llamada a funcion parametrizada
  ******************************************************************************************************************************/
 function teclaEnter(e,accion){
	var respuesta = true;
 	var tecla = (document.all) ? e.keyCode : e.which;

 	if(tecla==13){
 		eval(accion);
// 		this[accion]();
 	}
 	return respuesta;
 }
/*****************************************************************************************************************************
 * Seleccion de un componente para adicionarse a la infusión
 ******************************************************************************************************************************/
function seleccionReemplazo(valor){
	if(valor != null){
		elementosDetalle = valor.toString();
		fixedMenu.show();
	}else {
		alert("No se capturó identificador de medicamento a modificar");
	}
}
/*****************************************************************************************************************************
 * Seleccion de un elemento del arbol de acciones complementarias
 * Las ramas hasta el momento son:
 *
 * ---ARBOL 1---
 * 1.Sondas cateteres y drenes  - Codigo 01
 * 2.Cuidados de enfermeria		- Codigo 02
 * 3.Aislamientos				- Codigo 03
 * 4.Curaciones					- Codigo 04
 * 5.Terapias					- Codigo 05
 * 6.Interconsultas				- Codigo 06
 * ---ARBOL 2---
 * 7.Medidas generales			- Codigo 07
 * 8.Decisiones					- Codigo 08
 * 9.Procedimientos				- Codigo 09
 *
 ******************************************************************************************************************************/
function seleccionHojaAccionesComplementarias(codigo,descripcion,objArbol){

	var fechaActual = new Date();
	diaActual = fechaActual.getDate();
	mesActual = fechaActual.getMonth() + 1;
	anioActual= fechaActual.getFullYear();

	fechaActual = anioActual+"-"+mesActual+"-"+diaActual;

	switch(objArbol.identificador){
		case 1:
			//Blanquear color del foco
			document.getElementById("txCuidados").style.background = "#FFFFFF";

			var elemento = "";
			var nodos = codigo.substring(1).split("-");
			var separador = "*";

			switch (nodos[0]){
			case '02':
				elemento = document.getElementById("txCuidados");
				break;
			default:
				break;
			}

			if(elemento){
				//Resalto el color de fondo del elemento
				elemento.style.background = "#CCFFCC";

				var separadorFecha = "******"+fechaActual+"*******";
				if(elemento.value.indexOf(separadorFecha)==-1){
					elemento.value += "\n\r" + separadorFecha;
				}

				if(elemento.value.indexOf(descripcion)==-1){
					elemento.value += "\n\r" + separador + descripcion;
				}
			}
			break;
		case 2:

			//Blanquear color del foco
			if( document.getElementById("txtSondas") )
				document.getElementById("txtSondas").style.background = "#FFFFFF";

			if( document.getElementById("txMedidas") )
				document.getElementById("txMedidas").style.background = "#FFFFFF";

			if( document.getElementById("txtConsentimientos") )
				document.getElementById("txtConsentimientos").style.background = "#FFFFFF";

			if( document.getElementById("txProcedimientos") )
				document.getElementById("txProcedimientos").style.background = "#FFFFFF";

			if( document.getElementById("txAislamientos") )
				document.getElementById("txAislamientos").style.background = "#FFFFFF";

			if( document.getElementById("txTerapia") )
				document.getElementById("txTerapia").style.background = "#FFFFFF";
			//document.getElementById("txtInterconsulta").style.background = "#FFFFFF";

			if( document.getElementById("txtCuraciones") )
				document.getElementById("txtCuraciones").style.background = "#FFFFFF";

			var elemento = "";
			var nodos = codigo.substring(1).split("-");
			var separador = "*";

			switch (nodos[0]){
			case '01':
				elemento = document.getElementById("txtSondas");
				break;
			case '02':
				elemento = document.getElementById("txCuidados");
				break;
			case '03':
				elemento = document.getElementById("txAislamientos");
				break;
			case '04':
				elemento = document.getElementById("txtCuraciones");
				break;
			case '05':
				elemento = document.getElementById("txTerapia");
				break;
			case '06':
				//elemento = document.getElementById("txtInterconsulta");
				break;
			case '07':
				elemento = document.getElementById("txMedidas");
				break;
			case '08':
				elemento = document.getElementById("txtConsentimientos");
				break;
			case '09':
				elemento = document.getElementById("txProcedimientos");
				break;
			default:
				break;
			}

			if(elemento){
				//Resalto el color de fondo del elemento
				elemento.style.background = "#CCFFCC";

				var separadorFecha = "******"+fechaActual+"*******";
				if(elemento.value.indexOf(separadorFecha)==-1){
					elemento.value += "\r" + separadorFecha;
				}

				if(elemento.value.indexOf(descripcion)==-1){
					elemento.value += "\r" + separador + descripcion;
				}
			}
			break;
	}
}
/*****************************************************************************************************************************
 * Seleccion de un componente para adicionarse a la infusión
 ******************************************************************************************************************************/
function expandirRama(objeto,codigo){

	//---Arbol 1
	//Blanquear color del foco
	if(document.getElementById("txtSondas")) document.getElementById("txtSondas").style.background = "#FFFFFF";
	if(document.getElementById("txCuidados")) document.getElementById("txCuidados").style.background = "#FFFFFF";
	if(document.getElementById("txAislamientos")) document.getElementById("txAislamientos").style.background = "#FFFFFF";
	if(document.getElementById("txtCuraciones")) document.getElementById("txtCuraciones").style.background = "#FFFFFF";
	if(document.getElementById("txTerapia")) document.getElementById("txTerapia").style.background = "#FFFFFF";
	if(document.getElementById("txtInterconsulta")) document.getElementById("txtInterconsulta").style.background = "#FFFFFF";

	//---Arbol 2
	//Blanquear color del foco
	if(document.getElementById("txMedidas")) document.getElementById("txMedidas").style.background = "#FFFFFF";
	if(document.getElementById("txtConsentimientos")) document.getElementById("txtConsentimientos").style.background = "#FFFFFF";
	if(document.getElementById("txProcedimientos")) document.getElementById("txProcedimientos").style.background = "#FFFFFF";

	var elemento = document.getElementById(codigo);

	if(elemento){
		var hijos = elemento.children;
		var imagen = hijos[0];

		objeto.style.background = "#CCFFCC";
		imagen.click();
	}
}
/*****************************************************************************************************************************
 * Apertura del movimiento de articulos
 ******************************************************************************************************************************/
function abrirMovimientoArticulos(tipoProtocolo){
	limpiarBuscador();
	return fixedMenu2.show(tipoProtocolo);
}
/*****************************************************************************************************************************
 * Apertura del movimiento de examenes
 ******************************************************************************************************************************/
function movimientoExamenes(){
	limpiarBuscadorExamenes();
	return movExamenes.show();
}
/*****************************************************************************************************************************
 * Seleccion de un componente para adicionarse a la infusión
 ******************************************************************************************************************************/
function seleccionarComponente(codigo, nombre){
//	debugger;
	if(cuentaInfusiones > 0){
		var idx = cuentaInfusiones-1;
		var elemento = document.getElementById("wtxtcomponentes"+idx);
		if(elemento){
			alert( "codigo: "+codigo+"   nombre: "+nombre );
			//Valido que el componente no se encuentre previamente en la infusion
			var existe = false;

			if(!existe){
				var infusion = document.getElementById('wtxtcomponentes' + idx);
  				var componente = document.createElement('option');

				componente.setAttribute('value',codigo);
				if(esIE){
					componente.setAttribute('text',nombre.replace(/_/g," "));
				} else {
					componente.innerHTML = nombre.replace(/_/g," ");
				}

				try {
    				infusion.add(componente, null); //No es IE
  				}catch(ex){
    				infusion.add(componente); //IE
  				}
			} else {
				alert('El articulo ya se encuentra en la lista o no es duplicable');
				$( "#trFil"+idx ).remove();
			}
		}else{
			alert('No se encontro elemento a agregar.');
		}
	}else{
		alert('Debe crear una nueva orden de programa antes');
	}
}
function seleccionHabitacionPaciente(){
	var nroHistoria = document.getElementById('wselhab') ? document.getElementById('wselhab').value : '';

	if(nroHistoria && nroHistoria != ''){
		if(document.getElementById('whhistoria')){
			document.getElementById('whhistoria').innerHTML = nroHistoria;
		}
//		consultarKardex();
	}
}
/*****************************************************************************************************************************
 *Consulta las historias y habitaciones de acuerdo a un servicio
 ******************************************************************************************************************************/
function consultarHabitaciones()
{
	var contenedor = document.getElementById('cntHabitacion');
	var parametros = "";

	parametros = "consultaAjaxKardex=25&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&servicio=" + document.getElementById('wsservicio').value;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				contenedor.innerHTML=$.trim( ajax.responseText );
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

/*****************************************************************************************************************************
 *
 *****************************************************************************************************************************/
function irAKardex(historia){
	document.forms.forma.wthistoria.value = historia;
	consultarKardex();
}
 /*****************************************************************************************************************************
  * Consultar medicos por especialidad
  *****************************************************************************************************************************/
function consultarMedicosEspecialidad(){
	var codEspecialidad = document.getElementById("wselesp").value;

	var parametros = "";

	parametros = "consultaAjaxKardex=19&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&especialidad="+codEspecialidad;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				var col = document.getElementById("cntSelMedicos");
				col.innerHTML = $.trim( ajax.responseText );
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Consulta el esquema dextrometer seleccionado
 *****************************************************************************************************************************/
function consultarEsquemaInsulina(){

	var codEsquema = document.getElementById("wdexesquema");
	var codInsulina = document.getElementById("wdexins");
	var codFrecuencia = document.getElementById("wdexfrecuencia");

	if(codEsquema && codEsquema.value == ""){

		quitarEsquemaDextrometer();

		if( document.getElementById("cntEsquema") )
			document.getElementById("cntEsquema").innerHTML = "";
		
		if( document.getElementById("cntEsquemaActual") )
			document.getElementById("cntEsquemaActual").innerHTML = "";
	}
	
	if(true || codEsquema && codEsquema.value && codFrecuencia && codFrecuencia.value){

		consultarEsquemaInsulinaElemento(codEsquema.value);

		if( document.getElementById("btnQuitarEsquema") )
			document.getElementById("btnQuitarEsquema").disabled = false;
	}
}
/*****************************************************************************************************************************
 * Grabar esquema dextrometer
 *****************************************************************************************************************************/
function grabarEsquemaDextrometer(){
	var codArticulo = document.getElementById("wdexins");
	var frecuencia = document.getElementById("wdexfrecuencia");
	var codEsquema = document.getElementById("wdexesquema");
	var codEsquemaAnt = document.getElementById("whdexesquemaant");

	var usuario = document.forms.forma.usuario.value;
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fechaKardex = document.forms.forma.wfechagrabacion.value;

	if(codEsquema){
		var actualizaIntervalos = !(codEsquema.value == codEsquemaAnt.value);

		/*
		if(!frecuencia || frecuencia.value == '' && !codEsquema || codEsquema.value == '' && !codArticulo || codArticulo.value == ''){
			return;
		}

		if(!frecuencia || frecuencia.value == ''){
			alert("Debe especificar una frecuencia");
			return;
		}
		*/

		//Valido que hayan ingresado las dosis y unidad de dosis del esquema y la via, las observaciones son opcionales
		var cont1 = 0;
		var dosis = "";
		var uDosis = "";
		var via = "";
		var obs = "";
		var min = "";
		var max = "";

		esDexPersonalizado = false;
		var objEsDexPersonalizado = $( "#esDexPersonalizado" )
		if( objEsDexPersonalizado && objEsDexPersonalizado.val() == 'on' )
			esDexPersonalizado = true;

		if( !esDexPersonalizado ){
			while(document.getElementById("wdexint"+cont1)){
				if(document.getElementById("wdexint"+cont1) && document.getElementById("wdexint"+cont1).value != ''
					&& document.getElementById("wdexseludo"+cont1) && document.getElementById("wdexseludo"+cont1).value != ''
					&& document.getElementById("wdexselvia"+cont1) && document.getElementById("wdexselvia"+cont1).value != ''){

				dosis += document.getElementById("wdexint"+cont1).value+"|";
				uDosis += document.getElementById("wdexseludo"+cont1).value+"|";
				via += document.getElementById("wdexselvia"+cont1).value+"|";
				obs += document.getElementById("wdexobs"+cont1).value+"|";
				} else {
					alert("Por favor revise los valores del esquema.  Debe especificar un valor de dosis, unidad y via. Linea: "+eval(cont1+1));
					return;
				}
				cont1++;
			}
		}
		else{
			//Busco por filas el valor que se requiere
			//ESto se hace por que no necesariamente los consecutivos existen
			//Por tal motivo recorro fila a fila buscando los id que tienen el datos necesario para construir los string
			$( "#cntEsquema table tr" ).each(function(x){
				if( x >= 2 ){
					dosis 	+= $( "[id^=wdexint]", $( this ) ).val()+"|";
					uDosis 	+= $( "[id^=wdexseludo]", $( this ) ).val()+"|"; //document.getElementById("wdexseludo"+cont1).value+"|";
					via 	+= $( "[id^=wdexselvia]", $( this ) ).val()+"|";//document.getElementById("wdexselvia"+cont1).value+"|";
					obs 	+= $( "[id^=wdexobs]", $( this ) ).val()+"|";//document.getElementById("wdexobs"+cont1).value+"|";
					min 	+= $( "[id^=wdexRanMin]", $( this ) ).val()+"|";
					max 	+= $( "[id^=wdexRanMax]", $( this ) ).val()+"|";

					actualizaIntervalos = true;
				}
			});
		}
		grabarEsquemaDextrometerElemento(historia, ingreso, fechaKardex, codArticulo.value, frecuencia.value, codEsquema.value, dosis, uDosis, via, obs, usuario, actualizaIntervalos, min, max );
	}
}
/*****************************************************************************************************************************
 * Seleccion de un medicamento de la lista consultada para llevarlo al detalle
 ******************************************************************************************************************************/
function quitarEsquemaDextrometer(){

	var codArticulo = document.getElementById("wdexins");
	var codEsquema = "";
	$("#wdexesquema").val("");
	$("#cntEsquema").html("");

	var usuario = document.forms.forma.usuario.value;

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;

	var fechaKardex = document.forms.forma.wfechagrabacion.value;

	//Junio 13 de 2012, quito esta validacion
	// if(!frecuencia || frecuencia.value == ''){
		// alert("No se ha especificado una frecuencia o condicion");
		// return
	// }

	/**********************************************************************
	 * Creo la acción para ejecutarla al momento de guardar
	 **********************************************************************/
	if( !accionesOrdenes[ ELIMINAR_ESQUEMA_DEXTROMETER ] ){
		accionesOrdenes[ ELIMINAR_ESQUEMA_DEXTROMETER ] = {};
	}

	//La posicion es fija por que solo se puede borrar una vez el esquema
	if( !accionesOrdenes[ ELIMINAR_ESQUEMA_DEXTROMETER ][ 0 ] ){
		accionesOrdenes[ ELIMINAR_ESQUEMA_DEXTROMETER ][ 0 ] = new Array();

		var aux = accionesOrdenes[ ELIMINAR_ESQUEMA_DEXTROMETER ][ 0 ];
		aux[ aux.length ] = historia;			//parametro1
		aux[ aux.length ] = ingreso; 			//parametro2
		aux[ aux.length ] = fechaKardex; 		//parametro3
		aux[ aux.length ] = codArticulo.value; 	//parametro4
		aux[ aux.length ] = usuario; 			//parametro5
		aux[ aux.length ] = codEsquema; 		//parametro6
	}
	/**********************************************************************/

	var codEsquemaAnt = document.getElementById("whdexesquemaant");
	codEsquemaAnt.value='';

	// eliminarEsquemaDextrometerElemento(historia, ingreso, fechaKardex, codArticulo.value, usuario, codEsquema);

	var cntEsquemaActual = document.getElementById("cntEsquemaActual");

	if(cntEsquemaActual){
		cntEsquemaActual.style.display = 'none';
		$( cntEsquemaActual ).html('');
	}

	//Limpio los valores y anulo el boton quitar
	if(document.getElementById('btnQuitarEsquema')){
		document.getElementById('btnQuitarEsquema').disabled = true;
	}
}
/*****************************************************************************************************************************
 * Seleccion de un medicamento de la lista consultada para llevarlo al detalle
 *
 * Se agregaron los siguientes protocolos:
 *
 * 1.  Normal.
 * 2.  Analgesias.
 * 3.  Nutriciones.
 * 4.  Quimioterapia.
 ******************************************************************************************************************************/
function seleccionarMedicamento(codigo, nombre, origen, grupo, forma, unidad, pos, unidadFraccion, cantFracciones, vencimiento, diasVencimiento, dispensable, duplicable, diasMaximos, dosisMaximas, via, tipoProtocolo, noEnviar ){
	var tipoProtocoloAux = "";
	var i = 0, idx = 0;

	tipoProtocoloAux = tipoProtocolo;

	switch (tipoProtocolo) {
		case 'N':
			idx = elementosDetalle-1;

			posicionActual = elementosDetalle;
//			tipoProtocoloAux = "";
		break;
		case 'A':
			idx = elementosAnalgesia-1;

			posicionActual = elementosAnalgesia;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		case 'U':
			idx = elementosNutricion-1;

			posicionActual = elementosNutricion;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		case 'Q':
			idx = elementosQuimioterapia-1;

			posicionActual = elementosQuimioterapia;
//			tipoProtocoloAux = tipoProtocolo;
		break;

		case 'LQ':
			idx = elementosLev-1;

			posicionActual = elementosLev;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		default:
//			idx = elementosDetalle-1;
			idx = elementosDetalle;

			posicionActual = elementosDetalle;
		break;
	}

	var elementoAnteriorDetalle = document.getElementById("wnmmed"+tipoProtocoloAux+idx);
	var cntDetalleKardex = document.getElementById("detKardeximp" + tipoProtocoloAux);

	var idTabla = "tbDetalle" + tipoProtocoloAux;

//	stopEvent(window.onbeforeunload);

	if(posicionActual > 0){
		var elemento = elementoAnteriorDetalle;
		forma = forma.toString();

		if(elemento){
			//Valido que el articulo no se encuentre previamente en la lista
			var existe = false;
			var adicionar = false;
			var cont1 = 0;
			var item = null;
			var cd = "";
			var cdItem = "";

			while(cont1 < elementosDetalle){
				item = document.getElementById("wnmmed"+tipoProtocoloAux+cont1);

				if(item){
					if(item.tagName != 'DIV'){
						cd = item.value.split("-");
					} else {
						cd = item.innerHTML.split("-");
					}
					cdItem = cd[0];

					if(cdItem == codigo){
						existe = true;
						break;
					}
				}
				cont1++;
			}

			//Si el articulo es duplicable se anula la validacion anterior
			if(duplicable == 'on' && existe){
				adicionar = false;
			}

			if(!adicionar){
				if(elemento.tagName != 'DIV'){
					//En esta seccion se dan los posibles avisos
					//Grupo de control
					if(grupo == 'CTR'){

						//Verifica si se genera la formula de control de forma automatica.
						if($("#medicamentosControlAuto").val() == 'on'){
							alert("Este medicamento se encuentra en el grupo de control.  La fórmula de control se generará automaticamente al grabar.");
						}else{
							alert("Este medicamento se encuentra en el grupo de control.  Recuerde diligencia el formato manual de medicamentos de control.");
						}

						elemento.value = codigo + "-" + origen + "-" + nombre.replace(/_/g," ") + " (de control)";
					} else {
						elemento.value = codigo + "-" + origen + "-" + nombre.replace(/_/g," ");
					}


					// Se establece si la cadena msjNoPos ya tiene el codigo del artículo actual
					var avisoNoPos = msjNoPos.indexOf(codigo)
					msjNoPos += codigo+',';

					//No pos
					if( $( "#pacEPS" ).val() == 'on' ){
						if(avisoNoPos == -1 && pos == 'N'){
							alert("Este medicamento es NO POS");
						}
					}

					//Vence?
//					if(vence){
//						alert("vence");
//					}

					//Si no tiene fracciones en la unidad de manejo ni unidad de fraccion se avisa
					if( (unidadFraccion == '' || cantFracciones == '') && !esLiquidoEndovenoso){
						alert('El articulo con codigo ' + codigo + ' no tiene unidad de fraccion o fracciones por unidad de manejo.  Por favor notifique a servicio farmaceutico.');
					}

					if(origen != 'CM' && document.getElementById("wchkconf"+tipoProtocoloAux+idx)){
						document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = true;
					} else {
						if(document.getElementById("wchkconf"+tipoProtocoloAux+idx))
							document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = false;
					}

					//Abril 25 de 2011
					//No enviar
					if( noEnviar == 'on' ){
						if(document.getElementById("wchkdisp"+tipoProtocoloAux+idx)){
							document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = true;
						}
					}
					else{
						if(document.getElementById("wchkdisp"+tipoProtocoloAux+idx)){
							document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = false;
						}
					}

					//Unidad dosis
					if(document.getElementById("wudosis"+tipoProtocoloAux+idx)){
						document.getElementById("wudosis"+tipoProtocoloAux+idx).value = unidadFraccion;
					}

					//Unidad manejo
					if(document.getElementById("wcundmanejo"+tipoProtocoloAux+idx)){
						document.getElementById("wcundmanejo"+tipoProtocoloAux+idx).value = unidad;
					}

					//Cantidad maxima de fracciones en la unidad de manejo
					if(document.getElementById("wdosis"+tipoProtocoloAux+idx)){
						document.getElementById("wdosis"+tipoProtocoloAux+idx).value = cantFracciones;
					}

					//Oculto para maximo de unidades del articulo
					if(document.getElementById("whcmanejo"+tipoProtocoloAux+idx)){
						document.getElementById("whcmanejo"+tipoProtocoloAux+idx).value = cantFracciones;
					}

					//Oculto vence
					if(document.getElementById("whvence"+tipoProtocoloAux+idx)){
						document.getElementById("whvence"+tipoProtocoloAux+idx).value = vencimiento;
					}

					//La forma farmaceutica se graba igual, mas no se muestra en el kardex
					if(document.getElementById("wfftica"+tipoProtocoloAux+idx)){
						document.getElementById("wfftica"+tipoProtocoloAux+idx).value = forma;
					}

					//Oculto dias vencimiento
					if(document.getElementById("whdiasvence"+tipoProtocoloAux+idx)){
						document.getElementById("whdiasvence"+tipoProtocoloAux+idx).value = diasVencimiento;
					}

					//Oculto dispensable
					if(document.getElementById("whdispensable"+tipoProtocoloAux+idx)){
						document.getElementById("whdispensable"+tipoProtocoloAux+idx).value = dispensable;
					}

					//Oculto duplicable
					if(document.getElementById("whduplicable"+tipoProtocoloAux+idx)){
						document.getElementById("whduplicable"+tipoProtocoloAux+idx).value = duplicable;
					}

					//Asigno dias maximos
					if(document.getElementById("wdiastto"+tipoProtocoloAux+idx) && parseInt(diasMaximos) > 0){
						document.getElementById("wdiastto"+tipoProtocoloAux+idx).value = diasMaximos;
					} else {
						document.getElementById("wdiastto"+tipoProtocoloAux+idx).value = '';
					}

					//Asigno dosis maximas
					if(document.getElementById("wdosmax"+tipoProtocoloAux+idx) && parseInt(dosisMaximas) > 0){
						document.getElementById("wdosmax"+tipoProtocoloAux+idx).value = dosisMaximas;
					} else {
						document.getElementById("wdosmax"+tipoProtocoloAux+idx).value = '';
					}

					//Asigno via
					if(document.getElementById("wviadmon"+tipoProtocoloAux+idx)){
						document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = via;
					} else {
						document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = '';
					}

					//Vias
//					debugger;
					document.getElementById("wviadmon"+tipoProtocoloAux+idx).innerHTML = "";
					if(via != ''){
						var opcionesMaestro = document.getElementById("wmviaadmon").options;

						var cont1 = 0;
						var splVia = via.split(",");
						var opcionTmp = null;

						while(cont1 < opcionesMaestro.length){
							opcionTmp = document.createElement("option");
							if(via.indexOf(opcionesMaestro[cont1].value) != -1){
								document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
							}

							opcionTmp.innerText = opcionesMaestro[cont1].text;
							opcionTmp.value = opcionesMaestro[cont1].value;

							cont1++;
						}
					} else {
						var opcionesMaestro = document.getElementById("wmviaadmon").options;

						var cont1 = 0;
						var splVia = via.split(",");
						var opcionTmp = null;

						while(cont1 < opcionesMaestro.length){
							opcionTmp = document.createElement("option");
							document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);

							opcionTmp.innerText = opcionesMaestro[cont1].text;
							opcionTmp.value = opcionesMaestro[cont1].value;

							cont1++;
						}
					}

					if(existe){
						alert('YA EXISTE');
					}
				}else{
					alert('Debe agregar un nuevo articulo.');
				}
			} else {
				alert('El articulo ya se encuentra en la lista.  No se puede agregar porque esta configurado como no duplicable.');
				$( "#trFil"+idx ).remove();
			}
		}else{
			alert('No se encontro elemento a agregar.');
		}
	}
	else{
		alert('Aun no ha agregado el primer articulo');
	}
}

/*****************************************************************************************************************************
 *Punto de entrada de la generación del kardex
 ******************************************************************************************************************************/
function enter() {
	var historia = document.forms.forma.whistoria.value;
	var valido = true;

	//Validacion fecha
	if(historia == ''){
		alert('Debe especificar la historia clinica del paciente');
		valido = false;
	}

	if(valido){
		document.forms.forma.submit();
	}
}
/*****************************************************************************************************************************
 *Invoca acción de grabación del kardex, del encabezado y paso de las temporales a las tablas definitivas.
 ******************************************************************************************************************************/
function grabarKardex(wimprimir){
	
		if( adicionarArticuloInsulina() ){
			return;
		}
		
		// --------------------------------
		// Validar si existe una NPT agregada
		var cantNPTAgregadas = 0;
		$('table[id=tbDetalleAddN] input[id^=wnmmed]').each(function(){
				
			if($(this).attr('esnpt') == "on")
			{
				cantNPTAgregadas++;
				// return;
			}
		});
		
		var contNPTSuspendidas = 0;
		if(cantNPTAgregadas>0)
		{
			//Suspender las nutriciones pendientes en cada pestaña
			$('table[id^=tbDetalle] input[id^=wesnpt]').each(function(){
				
				protocoloContador = $(this).attr('id'); 
				protocoloContador = protocoloContador.replace("wesnpt",""); 
				if($(this).val()=="on" && $("#wnmmed"+protocoloContador).attr('esnpt') != "on")
				{
					//Validar que no este suspendido
					fila = $(this).parent()[0];
					if(fila.className != "suspendido")
					{
						suspenderArticulo( protocoloContador.substr(1,2),protocoloContador.substr(0,1), false );
						contNPTSuspendidas++;
					}
					
				}
				
			});
			
		}
		
		if(contNPTSuspendidas!=0)
		{
			jAlert("Se suspendieron las NPT prescritas anteriormente","ALERTA");
		}
		// --------------------------------
		
	
		// /************************************************************************************************************************
		 // * Si es enfermera y el medicamento no comienza x tiempo despues de la hora actual no deja grabar
		 // * Esto no aplica para urgencias
		 // ************************************************************************************************************************/
		// var msgIncioPosterior = "";
		// if( $( "#enUrgencias" ).val() != 'on' ){

			// $( "[id^=wpendiente]" ).filter("[value=on]").each(function(){
				// if( $( "#esEnfermera" ).val() == 'on' ){

					// var index = this.id.substr( "wpendiente".length );

					// var arFHinicio = $( "#wfinicio"+index ).val().split( "a las:" );
					// var stFechaInicio = $.trim(arFHinicio[0]);
					// var stHoraInicio = $.trim(arFHinicio[1]);

					// var fh = stFechaInicio.split( "-" );
					// var vecHora = stHoraInicio.split( ":" );

					// var codigoArticulo = $( "#wnmmed"+index )[0];
					// if(codigoArticulo.tagName == 'INPUT'){
						// datosArticulos = $.trim( codigoArticulo.value );
					// }
					// else {
						// datosArticulos = $.trim( codigoArticulo.innerHTML );
					// }

					// var tmAntes = $( "#tiempoMinimoOrdenMedicamento" ).val();

					// //El tiempo es dado en minutos y javascript solo trabaja en milisengudos
					// //Por tanto convierto el tiempo a milisengundos
					// tmAntes = tmAntes*60*1000;

					// //Obtengo el tiempo actual
					// var tmActual = new Date();

					// //El medicamento por tanto debe comenzar mínimo 30 minutos despues del tiempo actual
					// var tmTiempoMinimoInicio = tmActual.getTime() + tmAntes;

					// //Creo la fecha y hora actual en milisegundos para poder comparar las fechaServidor
					// var tmFechaHoraInicio = new Date( fh[0], fh[1]-1, fh[2], vecHora[0], vecHora[1], 0, 0 );

					// //Valido que la fecha y hora de inicio comience posterior a la fecha y hora minima para dispensar
					// if( tmFechaHoraInicio.getTime() < tmTiempoMinimoInicio ){
						// msgIncioPosterior += 'El medicamento <b>' + datosArticulos + '</b> debe comenzar en rondas posteriores<br>';
					// }
				// }
			// });

			// if( msgIncioPosterior != '' ){
				// jAlert( msgIncioPosterior )
				// return;
			// }
		// }
		// /************************************************************************************************************************/

		//Se ejcutan primero las acciones correspondiente (Suspender articulo, eliminar articulo y elminar procedimiento)
		ejecutarAccionesOrdenes();

		var wemp_pmla = $('#wemp_pmla').val();

		if(!wimprimir)
			wimprimir = "";

		if( wimprimir.substr(-3) == 'alt' ){
			var alt = 'on';
			wimprimir = wimprimir.substr( 0, wimprimir.length - 3 );
		}
		else{
			var alt = 'off';
		}

		grabando=true;
		var valido = true;
		var mensaje = '';

		//Busco si hay cambios en los CTC
		//Si hay cambio en alguno se debe mostrar nuevamente el CTC al medico para que lo revise
		var x = '';

		var mostrarCTC = false;
	    for( x in arCTCArticulos ){
		   //document.getElementById("wnmmed"+tipoProtocolo+cont1).value
		   //document.getElementById("wnmmed"+tipoProtocolo+cont1).value.split( "-" )[0]
		   if( buscarCambiosCTC( x ) ){
			   //Se deja quemado los ultimos dos campo, ya que con los últimos cambios no se requieren
			   strPendientesCTC += 'articulo|'+x+'|N|0\r\n';
			   mostrarCTC = true;
		   }
	    }

	    if( mostrarCTC ){
		   abrirCTCMultiple();
		   return;
	    }

		if($("#esContributivo").val())
		{
			// guardar medicamentos y procedimientos sin ctc en cadena de contributivos
			
			cadenaMedSinCTC = $("#cadenaGuardadosSinCTCO").val();
			cadenaProcSinCTC = $("#cadenaExamenesGuardadosSinCTCO").val();
			
			if(cadenaMedSinCTC!==undefined && cadenaMedSinCTC!="")
			{
				cadenaMedicamentoSinCTC =  cadenaMedSinCTC.split(";");
				
				for(var h=0;h<cadenaMedicamentoSinCTC.length-1;h++)
				{
					cadMedicamentoSinCTC =  cadenaMedicamentoSinCTC[h].split(",");
					
					var descArticulo = $("#wnmmed"+cadMedicamentoSinCTC[1]+cadMedicamentoSinCTC[2]).html();
					
					descArticulo = descArticulo.split("-");
					cadenaCTCcontributivo += "medicamentoCR|"+cadMedicamentoSinCTC[0]+"|"+$.trim(descArticulo[2])+"|"+cadMedicamentoSinCTC[1]+"|"+cadMedicamentoSinCTC[2]+"***";
				}
			}
			
			if(cadenaProcSinCTC!==undefined && cadenaProcSinCTC!="")
			{
				cadenaProcedimientosSinCTC =  cadenaProcSinCTC.split(";");
				
				for(var h=0;h<cadenaProcedimientosSinCTC.length-1;h++)
				{
					cadProcedimientosSinCTC =  cadenaProcedimientosSinCTC[h].split(",");
					
					cadenaCTCcontributivo += "procedimientoCR|"+cadProcedimientosSinCTC[0]+"|"+$.trim($("#wnmexamen"+cadProcedimientosSinCTC[1]).val())+"|"+cadProcedimientosSinCTC[1]+"|"+cadProcedimientosSinCTC[2]+"|"+cadProcedimientosSinCTC[3]+"|"+cadProcedimientosSinCTC[4]+"***";
				}
			}
			
			// borrar las cadenas de Medicamentos y procedimientos sin CTC para que no muestre el CTC normal
			$("#cadenaGuardadosSinCTCO").val("");
			$("#cadenaExamenesGuardadosSinCTCO").val("");
				
		}
		
		// -------------------------------------------------------------------------
		//			 MEDICAMENTOS SIN CTC POR CAMBIO DE RESPONSABLE
		// -------------------------------------------------------------------------


		// Llama  a la funcion abrirCTCMultipleParaArticulosGrabados para mostrar el ctc de los medicamentos activos que no tienen.

		var cadenaGuardadosSinCTC="";
		cadenaGuardadosSinCTC=$("#cadenaGuardadosSinCTCO").val();

		if(cadenaGuardadosSinCTC != "" && cadenaGuardadosSinCTC !== undefined)
		{
			var descMdcSinCtc="";
			mdSinCTC = cadenaGuardadosSinCTC.split( ";" );
			for( var b = 0; b < (mdSinCTC.length - 1); b ++ )
			{
				mdcSinCTC = mdSinCTC[b].split( "," );
				
				descMdcSinCtc += "- "+$.trim( $("#wnmmed"+mdcSinCTC[1]+mdcSinCTC[2]).html() )+"\n";
				
			}
						
			// alert('Debe llenar los CTC de medicamentos '+descMdcSinCtc.substr(0,descMdcSinCtc.length-1)+' por cambio de responsable');
			alert('Debe llenar los siguientes CTC de medicamentos por cambio de responsable: \n\n'+descMdcSinCtc+' ');
			abrirCTCMultipleParaArticulosGrabados(cadenaGuardadosSinCTC,'');
			$("#cadenaGuardadosSinCTCO").val("");
			return;
		}

		// Graba los medicamentos sin CTC almacenados en la cadena guardarCadenaGrabadosSinCTC.
		if(guardarCadenaGrabadosSinCTC != '')
		{
			// alert("puede grabar por aca"+guardarCadenaGrabadosSinCTC);
			var filasNoPos = guardarCadenaGrabadosSinCTC.split( ";" );

			if(filasNoPos.length-1 > 0)
			{
				for( var i = 0; i < (filasNoPos.length - 1); i ++ ){

					var arrNoPos = filasNoPos[i].split( "," );

					grabarAjaxArticulos( arrNoPos[0], arrNoPos[1], arrNoPos[2],"M");

				}

			}
			
			guardarCadenaGrabadosSinCTC='';
		}


		// -------------------------------------------------------------------------
		//			 		EXAMENES SIN CTC POR CAMBIO DE RESPONSABLE
		// -------------------------------------------------------------------------

		// Llama  a la funcion abrirCTCMultipleParaExamenesGrabados para mostrar el ctc de los examenes sin realizar que no tienen.
		var cadenaExamenesGuardadosSinCTC="";
		cadenaExamenesGuardadosSinCTC=$("#cadenaExamenesGuardadosSinCTCO").val();

		if(cadenaExamenesGuardadosSinCTC != "" && cadenaExamenesGuardadosSinCTC !== undefined)
		{
			var descPrcSinCtc="";
			prSinCTC = cadenaExamenesGuardadosSinCTC.split( ";" );
			for( var b = 0; b < (prSinCTC.length - 1); b ++ )
			{
				prcSinCTC = prSinCTC[b].split( "," );
				
				descPrcSinCtc += "- "+ $.trim( $("#wnmexamen"+prcSinCTC[1]).val() )+"\n";
			}
			
			
			alert('Debe llenar los siguientes CTC de procedimientos por cambio de responsable: \n\n'+descPrcSinCtc+' ');
			abrirCTCMultipleParaExamenesGrabados(cadenaExamenesGuardadosSinCTC,'');
			$("#cadenaExamenesGuardadosSinCTCO").val("");
			return;
		}


		// Graba los examenes sin CTC almacenados en la cadena guardarCadenaExamenesGrabadosSinCTC.
		if(guardarCadenaExamenesGrabadosSinCTC != '')
		{
			var filasNoPos = guardarCadenaExamenesGrabadosSinCTC.split( ";" );

			if(filasNoPos.length-1 > 0)
			{
				for( var i = 0; i < filasNoPos.length; i ++ ){

					var arrNoPos = filasNoPos[i].split( "," );
					 grabarAjaxProcedimiento( arrNoPos[2], arrNoPos[3], arrNoPos[4], arrNoPos[1],wemp_pmla,"M");
				}
			}
			
			guardarCadenaExamenesGrabadosSinCTC='';
		}

		//Validacion de firma electronica
		if(document.getElementById('pswFirma')){
			if(document.getElementById('pswFirma').value != '' && $( "#tdEstadoFirma" ).hasClass( "fondoVerde" ) ){


				var usuario = $('#usuario').val();
				var password = $('#pswFirma').val();

				$.ajax({
						url: "ordenes.inc.php",
						type: "POST",
						data:{
							consultaAjaxKardex:     '56',
							wemp_pmla:				wemp_pmla,
							wusuario : 				usuario,
							wpassword : 			password

						},
						async: false,
						success:function(data_json) {

							if (data_json.error == 1)
							{
								alert(data_json.mensaje);
								valido = false;
								return;
							}
							else{
								valido = true;
							}
						}
					}
				);

			} else {
				valido = false;
				mensaje = 'No se puede grabar la orden sin firma digital.';
			}
		}

		if(wimprimir=='sinFirma'){
			valido = true;
			wimprimir='';
		}

		window.onbeforeunload = '';

		if(valido){

			/****************************************************************************************************
			 * Valido que no se pueda grabar el kardex con velocidad de dilución vació
			 ***************************************************************************************************/
			var valVelocidadDilucion = true;
			 //Busco en el objeto artLevs cuales son los articulos que se deben eliminar
			$( "[id^=slVelDil],[id^=inFrecDil],[id^=txDosisCalculada],[id^=slDosisCalculada]" ).each(function(x){
				if( $.trim( $( this ).val() ) == "" ){
					valVelocidadDilucion = false;
				}
			})

			if( !valVelocidadDilucion ){
				jAlert( "Hay datos incorrectos para <b>VELOCIDAD DE INFUSI&Oacute;N</b> para articulos de <b>LEVs e INFUSIONES</b>" );
				return;
			}
			/***************************************************************************************************/

			// var conf = document.getElementById('wconfdisp').checked == true ? 'on' : 'off';
			var rutaImagenOrdenMedica = '';

			if(document.getElementById("ordenActual")){
				rutaImagenOrdenMedica = document.getElementById("ordenActual").value;
			}

			if(rutaImagenOrdenMedica == '' && typeof(upload_target) != 'undefined' && upload_target.document.getElementById('hdRutaOrden')){
				var ordenIframe = upload_target.document.getElementById('hdRutaOrden').value;
				if(ordenIframe != ''){
					rutaImagenOrdenMedica = ordenIframe;
				}
			}

			//El encabezado del kardex se encuentra dividido en las pestañas
			var pestanasActivas = document.getElementById("hpestanas").value;




			if(wimprimir!='cenimp' && wimprimir!='cenimpexam')
				$.blockUI({ message: $('#msjEspere') });

			//Grabacion automatica de las pestanas
//			var arrProtocolos = new Array("%","N","A","U","Q","2","4");
			var arrProtocolos = new Array("A","N","U", "Q", "LQ","%","2","4");
			var tipoProtocolo = "", nomContenedor = "", tabla = "";
			var pelo = "",celdas = "", celda = "", componentes = "", modificado = "", limiteIteraciones = "";
			for(var cont2 = 0; cont2 < arrProtocolos.length; cont2++){
				tipoProtocolo = arrProtocolos[cont2];
				//Variador del metodo
				switch (tipoProtocolo){
					case 'A':
						limiteIteraciones = elementosAnalgesia;
						break;
					case 'U':
						limiteIteraciones = elementosNutricion;
						break;
					case 'Q':
						limiteIteraciones = elementosQuimioterapia;
						break;
					case 'LQ':
						limiteIteraciones = elementosLev;
						break;
					case 'N':
					case '%':
					case 'DA':
						nomContenedor = "tbDetalle"+tipoProtocolo;
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones = tabla.rows.length;
						}else{
							limiteIteraciones = 0;
						}
						nomContenedor = "tbDetalleAdd"+tipoProtocolo;
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones += tabla.rows.length;
						}


						nomContenedor = "tbDetalleImp"+tipoProtocolo;
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones += tabla.rows.length;
						}

						nomContenedor = "tbDetalleAddImp"+tipoProtocolo;
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones += tabla.rows.length;
						}

						limiteIteraciones = elementosDetalle;
					break;
					case '2':
						nomContenedor = "tbDetInfusiones";
						tabla = document.getElementById(nomContenedor);
						if(tabla){
							limiteIteraciones = tabla.rows.length;
						}else{
							limiteIteraciones = 0;
						}
					break;
					case '4':
						if(document.getElementById("cuentaExamenes")){
							limiteIteraciones = document.getElementById("cuentaExamenes").value;
						} else {
							limiteIteraciones = 0;
						}

						//Contabilizacion de cantidad de examenes totales, existentes y nuevos
						var incremento = eval(limiteIteraciones)-1;

						if(incremento < 0){
							incremento = 0;
						}

						while(document.getElementById("trEx"+incremento)){
							incremento++;
						}
						limiteIteraciones = cuentaExamenes;
					break;
				}

				//Iteracion por fila
				for(var cont1 = 0; cont1 < limiteIteraciones; cont1++){
					var modificado = document.getElementById("wmodificado" + tipoProtocolo + cont1);

					//Variador del metodo
					switch (tipoProtocolo){
						case 'N':
						case 'A':
						case 'U':
						case '%':
						case 'Q':
						case 'LQ':
							if( document.getElementById("wnmmed"+tipoProtocolo+cont1) ){
								if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){ //Articulo existente
									if(document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != null){
										if(document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName == "DIV"){
											if(!filtroAcciones(tipoProtocolo+".15","grabarArticuloSinValidacion('"+cont1+"','"+tipoProtocolo+"')")){
												$.unblockUI();
												grabando=false;
												return;
											}
										} else {
											if(document.getElementById("wnmmed"+tipoProtocolo+cont1) != null && document.getElementById("wnmmed"+tipoProtocolo+cont1) != 'undefined' && document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != "DIV"){
												if(!filtroAcciones(tipoProtocolo+".15","grabarArticulo('"+cont1+"','"+tipoProtocolo+"')")){
													$.unblockUI();
													grabando=false;
													return;
												}
											}
										}
									}
								}
								else {  //Articulo nuevo
									if(document.getElementById("wnmmed"+tipoProtocolo+cont1) != null && document.getElementById("wnmmed"+tipoProtocolo+cont1) != 'undefined' && document.getElementById("wnmmed"+tipoProtocolo+cont1).tagName != "DIV"){
										if(!filtroAcciones(tipoProtocolo+".15","grabarArticulo('"+cont1+"','"+tipoProtocolo+"')")){
											$.unblockUI();
											grabando=false;
											return;
										}
									}
								}
							}
						break;
						case '2':
							var indiceInfusion = document.getElementById("windiceliq"+cont1);

							if(indiceInfusion && indiceInfusion != null && indiceInfusion != 'undefined'){
								modificado = document.getElementById("wmodificado" + tipoProtocolo + indiceInfusion.value);
								if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){
									indiceInfusion = indiceInfusion.value;
									if(!filtroAcciones(tipoProtocolo+".7","grabarInfusion('"+indiceInfusion+"','"+tipoProtocolo+"')")){
										$.unblockUI();
										grabando=false;
										return;
									}
								}
							}
						break;
						case '4':

							if(isset(document.getElementById("wmodificado" + tipoProtocolo + cont1)))
							{
								modificado = document.getElementById("wmodificado" + tipoProtocolo + cont1);
								if(modificado != null && modificado != 'undefined' && modificado.value != "" && modificado.value == "S"){
									if(!filtroAcciones(tipoProtocolo+".10","grabarExamen('"+cont1+"','"+tipoProtocolo+"')")){
										$.unblockUI();
										grabando=false;
										return;
									}
									else{
										// grabarExamenADetalle( cont1, tipoProtocolo );
									}
								}
							}
						break;
					}
				}
			}
			
			//Enero 22 de 2020
			//Una vez guardado el kardex se deja todos los examenes como viejos
			$( "[newExam]" ).attr('newExam','off');
			
			/****************************************************************************************************
			 *
			 ****************************************************************************************************/
			// if( $( "#pacEPS" ).val() == 'on' ){
			if( $( "#pacEPS" ).val() == 'on' && !$("#esContributivo").val()){
				// artsGrabadosCTC += ","+cdArt+"-"+respuesta[1]+"-"+texto;
				artsGrabadosCTC = "";
				
				$( "[id^=wnmmed][pos=N]" ).each(function(){
					var idPro = this.id.substr(6);
					var dtArt = $( this ).html().split( "-" );
					artsGrabadosCTC += ","+dtArt[0]+"-"+$( "#widoriginal"+idPro ).val()+"-"+$.trim( dtArt[2] );
				});
				
				
				if( $.trim( artsGrabadosCTC ) != '' ){
					//Se graban los datos que no han sido leídos cómo leidos
					$.ajax({
						url: "ordenes.inc.php",
						type: "POST",
						data:{
							consultaAjaxKardex: '78',
							wemp_pmla		  :	wemp_pmla,
							his				  : document.forms.forma.whistoria.value,
							ing				  : document.forms.forma.wingreso.value,
							datos			  : artsGrabadosCTC.substr(1),
							user		  	  : $('#usuario').val(),
							navegador		  : $.browser,
						},
						async: false,
						success:function(data_json) {

							if (data_json.error == "1")
							{
								// alert("Todos tienen CTC");
								// valido = false;
								return;
							}
							else{
								alert( data_json.mensError );
								// valido = true;
							}
							
							//artsGrabadosCTC = "";
						},
						dataType: "json",
					});
				}
			}

			/********************************************************************************************************************
			 *	GRABAR INSUMOS NPT
			*********************************************************************************************************************/
			grabarInsumosNPT();
			
			
			/********************************************************************************************************************
			 *	GRABAR EXAMENES AGRUPADOS
			*********************************************************************************************************************/
			grabarProcedimientoAgrupado();


			/*********************************************************************************************************************
			 * Graba los insumos de un articulo LEV
			 *********************************************************************************************************************/
			grabarListaInsumosLev();
			/*********************************************************************************************************************/

			
			$.ajax({
				url		: "../../movhos/procesos/impresionMedicamentosControl.php", //?wemp_pmla=01&historia=545&ingreso=25&fechaKardex=20147&consultaAjax=10&dxCIE10='.$inCIE10.'",
				type	: "POST",
				dataType: "json",
				data	: {
					consultaAjax: '10',
					wemp_pmla	: $("#wemp_pmla").val(),
					historia	: $('#whistoria').val(),
					ingreso		: $('#wingreso').val(),
					fechaKardex	: $('#wfechagrabacion').val(),
					dxCIE10		: $( "#inCIE10" ).val(),
				},
				async	: false,
				success	:function(respuesta) {
				}
			});
			
			/********************************************************************************************************************
			 *	CTC CONTRIBUTIVOS
			*********************************************************************************************************************/
			
				//identificar si los elementos de la cadena han sido eliminados
				var contadorEliminados = 0;
				var arrayEliminados = new Array();
								
				elementosCTCcontributivo = cadenaCTCcontributivo.split("***");
				
				for(var i=0; i < elementosCTCcontributivo.length-1;i++)
				{
					elementoPendienteCTC = elementosCTCcontributivo[i].split("|");
					
					if(elementoPendienteCTC[0]=="medicamento")
					{
						var existeMed = false;
						var cadenaIdos = "";
						var cadenaDosis = "";
						var sumaDosis = 0;
						var unidadDosis = "";
						var cantidadMedDosis = 0;
						
						// buscar si existe
						$('table[id^=tbDetalleAdd] [id^=wnmmed]').each(function(){
							
							idCompleto = $(this).attr('id');
							idProtConsec = idCompleto.replace("wnmmed","");
							
							articulo = $(this).html();
							art = articulo.split("-");
							
							if(elementoPendienteCTC[1] == art[0])
							{
								existeMed = true;
								
								cadenaIdos += $("#widoriginal"+idProtConsec).val()+",";
								unidadDosis = $("#wudosis"+idProtConsec).val();
								sumaDosis += parseFloat($("#wdosis"+idProtConsec).val());
								cadenaDosis += $("#wdosis"+idProtConsec).val()+"-";
								cantidadMedDosis++;
							}
							
						});
						
						if(!existeMed)
						{
							arrayEliminados[contadorEliminados] = elementosCTCcontributivo[i];
							contadorEliminados++
						}
						else
						{
							
							cadenaIdos = cadenaIdos.substring(0,cadenaIdos.length-1);//quitar ultimo caracter
							
							if(cantidadMedDosis>1)
							{
								cadenaDosis = "("+cadenaDosis.substring(0,cadenaDosis.length-1)+")";
							}
							else
							{
								cadenaDosis = "";
							}
							
							// completar cadena por medicamento validando que todavia exista el medicamento
							cadenaCTCcontributivo = cadenaCTCcontributivo.replace(elementosCTCcontributivo[i],elementosCTCcontributivo[i]+"|"+cadenaIdos+"|"+sumaDosis+"|"+cadenaDosis+"|"+unidadDosis);
						}
						
					}
					else if(elementoPendienteCTC[0]=="procedimiento")
					{
						if($("#wnmexamen"+elementoPendienteCTC[3]).val()===undefined)
						{
							arrayEliminados[contadorEliminados] = elementosCTCcontributivo[i];
							contadorEliminados++
						}
					}
					else if(elementoPendienteCTC[0]=="medicamentoCR") // Medicamentos por cambio de responsable
					{
						var existeMed = false;
						var cadenaIdos = "";
						var cadenaDosis = "";
						var sumaDosis = 0;
						var unidadDosis = "";
						var cantidadMedDosis = 0;
						
						// buscar si existe
						$('table[id^=tbDetalle] div[id^=wnmmed]').each(function(){
							
							idCompleto = $(this).attr('id');
							idProtConsec = idCompleto.replace("wnmmed","");
							
							articulo = $(this).html();
							art = articulo.split("-");
							
							articuloSuspendido = $("[idtr=trFil"+idProtConsec+"]").hasClass("suspendido");
							
							var articuloConCTC = false;
							// if($("#wtieneCTC"+idProtConsec).val()=="on")
							if($("#wtieneCTC"+idProtConsec).val()=="on" || $("#wtieneCTC"+idProtConsec).val()=="")
							{
								articuloConCTC = true;
							}
							
							if(elementoPendienteCTC[1] == art[0] && !articuloSuspendido && !articuloConCTC)
							{
								existeMed = true;
								
								cadenaIdos += $("#widoriginal"+idProtConsec).val()+",";
								unidadDosis = $("#wudosis"+idProtConsec).val();
								sumaDosis += parseFloat($("#wdosis"+idProtConsec).val());
								cadenaDosis += $("#wdosis"+idProtConsec).val()+"-";
								cantidadMedDosis++;
							}
							
						});
						
						if(!existeMed)
						{
							arrayEliminados[contadorEliminados] = elementosCTCcontributivo[i];
							contadorEliminados++
						}
						else
						{
							
							cadenaIdos = cadenaIdos.substring(0,cadenaIdos.length-1);//quitar ultimo caracter
							
							if(cantidadMedDosis>1)
							{
								cadenaDosis = "("+cadenaDosis.substring(0,cadenaDosis.length-1)+")";
							}
							else
							{
								cadenaDosis = "";
							}
							
							cadenaCTCcontributivo = cadenaCTCcontributivo.replace(elementosCTCcontributivo[i],elementosCTCcontributivo[i]+"|"+cadenaIdos+"|"+sumaDosis+"|"+cadenaDosis+"|"+unidadDosis);
						}
					}
					else if(elementoPendienteCTC[0]=="procedimientoCR") // procedimientos por cambio de responsable
					{
						if($("#westadoexamen"+elementoPendienteCTC[3]).val()=="C")
						{
							arrayEliminados[contadorEliminados] = elementosCTCcontributivo[i];
							contadorEliminados++
						}
					}
				}
				
				// Si no existe se eliminan de la cadena
				if(arrayEliminados.length > 0)
				{
					for(var i=0; i < arrayEliminados.length;i++)
					{
						cadenaCTCcontributivo = cadenaCTCcontributivo.replace(arrayEliminados[i]+"***","")
					}
				}
				
				if( cadenaCTCcontributivo != "" ){
				   mostrarCTCcontributivo();
				   return;
				}
				
			/*********************************************************************************************************************/
			
			while( stickers_ga.length > 0 ){
									
				var ga_ex = stickers_ga.pop();
				console.log(ga_ex);
				//GET para imprimir el sticker para muestras de laboratorio
				$.post("../reportes/HCE_Sticker_GA.php",
					{
						consultaAjax: '',
						whis		: $("#whistoria").val(),
						wing		: $("#wingreso").val(),
						wip			: $("#wipimpresoraga").val(),
						wtor		: $("#hexcco"+ga_ex.cuentaExamenes ).val(),
						wnor		: $("#hexcons"+ga_ex.cuentaExamenes ).val(),
						witem		: $("#hexnroitem"+ga_ex.cuentaExamenes ).val(),
					}, 
					function(data){},
				);
			}
			
			
			if(wimprimir == 'imp')
			{

				var indicaciones = $('#windicaciones').val();

				//Si las indicaciones no estan definidas en el formulario llega undefined, cuando esto ocurre no se ejecuta la grabacion o actualizacion de ind. del egreso.
				if(indicaciones !== undefined){

					grabarIndicaciones(indicaciones, document.forms.forma.wfecha.value, document.forms.forma.whistoria.value, document.forms.forma.wingreso.value);

				}
			}


			//Se graban los datos que no han sido leídos cómo leidos
			$.ajax({
					url: "ordenes.inc.php",
					type: "POST",
					data:{
						consultaAjaxKardex: '58',
						wemp_pmla		  :	wemp_pmla,
						codigo		  	  : $('#usuario').val(),
						his				  : document.forms.forma.whistoria.value,
						ing				  : document.forms.forma.wingreso.value,
						pestanasVistas	  : $("#pestanasVistas").val()
					},
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							alert(data_json.mensaje);
							valido = false;
							return;
						}
						else{
							valido = true;
						}
					}
				}
			);

			//Se graban los datos que no han sido leídos cómo leidos
			$.ajax({
					url: "ordenes.inc.php",
					type: "POST",
					data:{
						consultaAjaxKardex: '58',
						wemp_pmla		  :	wemp_pmla,
						codigo		  	  : $('#usuario').val(),
						his				  : document.forms.forma.whistoria.value,
						ing				  : document.forms.forma.wingreso.value,
						pestanasVistas	  : $("#pestanasVistas").val()
					},
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							alert(data_json.mensaje);
							valido = false;
							return;
						}
						else{
							valido = true;
						}
					}
				}
			);

			//Grabo los examenes al detalle
			// for( var indexExamenes = 0; indexExamenes < cuentaExamenes; indexExamenes++ ){
				grabarExamenADetalle();
			// }

			//Primero se envia los mensajes de interoperabilidad con HIRUKO - IMEXHS
			// $.post( "ordenes.inc.php", 
				// { 
					// historia			: document.forms.forma.whistoria.value,
					// ingreso				: document.forms.forma.wingreso.value, 
					// consultaAjaxKardex	: 'imagenologiaHiruko', 
					// consultaAjax		: '' , 
					// wemp_pmla			: wemp_pmla,
				// }, 
				// function(data){
					
					// //Una vez terminado los proceso de envio de datos de HIRUKO - IMEX se hace los de laboratorio
					// //Esto es para crear el msgHL para realizar la orden de trabajo para laboratorio
					// $.ajax({
							// url: "ordenes.inc.php",
							// type: "POST",
							// data:{
								// consultaAjaxKardex	: 'ordenTrabajoLaboratorio',
								// wemp_pmla			: wemp_pmla,
								// wusuario		  	  	: $('#usuario').val(),
								// historia		  	: document.forms.forma.whistoria.value,
								// ingreso			  	: document.forms.forma.wingreso.value,
							// },
							// async: false,
							// success:function(data_json) {
								
							// }
						// }
					// );
				// }
			// );
					debugger;
					console.log(wemp_pmla);
					
						$.ajax({
							url: "/matrix/interoperabilidad/procesos/funcionesGeneralesEnvioHL7.php",
							type: "GET",
							data:{
								accion              :'consultarInteroperabilidades',
								wemp_pmla			: wemp_pmla
								
							},
							async: false,
							success:function(data) {
															
							if(data.includes('Hiroku')){
										$.ajax({
												url	: "ordenes.inc.php", 
												type: "POST",
												data:{ 
													historia			: document.forms.forma.whistoria.value,
													ingreso				: document.forms.forma.wingreso.value, 
													consultaAjaxKardex	: 'imagenologiaHiruko', 
													consultaAjax		: '' , 
													wemp_pmla			: wemp_pmla,
												}, 
												async: false,
												success: function(data){
													console.log('enviado hiroku');
													console.log(data);
												
												}
											});
										}
									
								if(data.includes('Dinamica')){
										$.ajax({
												url	: "ordenes.inc.php", 
												type: "POST",
												data:{
													consultaAjaxKardex	: 'insertarOrdenWs',
													wemp_pmla			: wemp_pmla,
													wusuario		  	  	: $('#usuario').val(),
													historia		  	: document.forms.forma.whistoria.value,
													ingreso			  	: document.forms.forma.wingreso.value,
							                    }, 
												async: false,
												success: function(data){
													console.log('enviado laboratorio');
													console.log(data);
												}
											});
										}	
											if(data.includes('SABBAG')){
										$.ajax({
												url	: "ordenes.inc.php", 
												type: "GET",
												data:{
													consultaAjaxKardex	: 'OrdenSABBAG',
													wemp_pmla			: wemp_pmla,
													historia		  	: document.forms.forma.whistoria.value,
													ingreso			  	: document.forms.forma.wingreso.value,
							                    }, 
												async: false,
												success: function(data){
													console.log('enviado Sabbag');
													console.log(data);
												}
											});
										}	
											if(data.includes('ordenTrabajoLaboratorio')){
										$.ajax({
												url	: "ordenes.inc.php", 
												type: "GET",
												data:{
													consultaAjaxKardex	: 'ordenTrabajoLaboratorio',
													wemp_pmla			: wemp_pmla,
													wusuario		  	: $('#usuario').val(),
													historia		  	: document.forms.forma.whistoria.value,
													ingreso			  	: document.forms.forma.wingreso.value,
							                    }, 
												async: false,
												success: function(data){
													console.log('enviado LABORATORIO');
													console.log(data);
												}
											});
										}	
							}
								});
			
				//Primero se envia los mensajes de interoperabilidad con HIRUKO - IMEXHS
		/*	$.ajax({
				url	: "ordenes.inc.php", 
				type: "POST",
				data:{ 
					historia			: document.forms.forma.whistoria.value,
					ingreso				: document.forms.forma.wingreso.value, 
					consultaAjaxKardex	: 'imagenologiaHiruko', 
					consultaAjax		: '' , 
					wemp_pmla			: wemp_pmla,
				}, 
				async: false,
				success: function(data){

					$.ajax({
							url: "ordenes.inc.php",
							type: "GET",
							data:{
								consultaAjaxKardex	: 'insertarOrdenWs',
								wemp_pmla			: wemp_pmla,
								wusuario		  	  	: $('#usuario').val(),
								historia		  	: document.forms.forma.whistoria.value,
								ingreso			  	: document.forms.forma.wingreso.value,
							},
							async: false,
							success:function(data_json) {
								//Una vez terminado los proceso de envio de datos de HIRUKO - IMEX se hace los de laboratorio
								//Esto es para crear el msgHL para realizar la orden de trabajo para laboratorio
								$.ajax({
										url: "ordenes.inc.php",
										type: "POST",
										data:{
											consultaAjaxKardex	: 'ordenTrabajoLaboratorio',
											wemp_pmla			: wemp_pmla,
											wusuario		  	  	: $('#usuario').val(),
											historia		  	: document.forms.forma.whistoria.value,
											ingreso			  	: document.forms.forma.wingreso.value,
										},
										async: false,
										success:function(data_json) {
											
										}
									}
								);
							}
						}
					
					);
				}
			});

			
*/

			var conf = document.getElementById('wconfdisp').checked == true ? 'on' : 'off';

			document.forms.forma.action = 'ordenes.php?wemp_pmla='+document.forms.forma.wemp_pmla.value
						+'&waccion=c'
						+'&wcedula='+document.forms.forma.wcedula.value
						+'&wtipodoc='+document.forms.forma.wtipodoc.value
						+'&wfecha='+document.forms.forma.wfecha.value
						+'&wfechagrabacion='+document.forms.forma.wfechagrabacion.value
						+'&primerKardex='+document.getElementById("wkardexnuevo").value
						+'&rutaOrdenMedica='+rutaImagenOrdenMedica
						+'&confirmado='+conf
						+'&firmaDigital='+document.getElementById("whfirma").value
						+'&windicaciones='
						+'&pgr_origen='+document.getElementById("pgr_origen").value;
		}

		if(wimprimir!='cenimp' && wimprimir!='cenimpexam')
			$.unblockUI();

		//$.blockUI({ message: $('#msjEspere') });

		//Grabacion automatica del dextrometer
		filtroAcciones("6.5","grabarEsquemaDextrometer()");
//		grabarEsquemaDextrometer();

		// Llamo a la ventana de impresión de la orden
		if(wimprimir=='imp' || wimprimir=='impexa'|| wimprimir=='imppro' || wimprimir=='cenimp' || wimprimir=='cenimpexa' || wimprimir=='cenimppro')
		{
			if(valido)
			{
				if(wimprimir=='cenimp' || wimprimir=='cenimpexa' || wimprimir=='cenimppro' )
				{
					//Si imprimeMedicamentos es off solo se imprime los procedimientos
					if( true || wimprimir!='cenimp' && wimprimir!='cenimpexa' ){
						if( document.getElementById( "imprimeMedicamentos" ).value != 'on' ){
							if( wimprimir != "" ){
								wimprimir = "cenimppro";
							}
						}
						else{
							//Si imprimeMedicamentos es on solo se pregunta si quiere imprimir los medicamentos
							if( document.getElementById( "imprimeMedicamentos" ).value == 'on' ){
								if( wimprimir != "" && wimprimir != "cenimp" && wimprimir != "cenimpexa" && confirm( "Desea imprimir la orden de medicamentos" ) ){
									wimprimir = "cenimp";
								}
								else if( wimprimir != "" && wimprimir != "cenimp" && wimprimir != "cenimppro" && confirm( "Desea imprimir las ordenes de procedimientos" ) ){
									wimprimir = "cenimp";
								}
							}
						}
					}

					//abrir_ventana("ordenes_cenimp.php", document.forms.forma.wemp_pmla.value, document.forms.forma.whistoria.value, document.forms.forma.wingreso.value, wimprimir);

					var parametros = "";
					var linkCenimp = "";

					// parametros = "wemp_pmla="+document.forms.forma.wemp_pmla.value+"&whistoria="+document.forms.forma.whistoria.value+"&wingreso="+document.forms.forma.wingreso.value+"&wimprimir="+wimprimir.substr( 3 );
					parametros = "wemp_pmla="+document.forms.forma.wemp_pmla.value+"&whistoria="+document.forms.forma.whistoria.value+"&wingreso="+document.forms.forma.wingreso.value+"&tipoimp="+wimprimir.substr( 3 )+"&alt="+alt+"&pacEps="+document.getElementById( "pacEPS" ).value;

					$('#linkcenimp').html('<div align="center"><img src="../../images/medical/ajax-loader3.gif" width="21" height="21">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </div>');
					$('#linkcenimp').show('fast');
					$('#linkcenimpexam').html('<div align="center"><img src="../../images/medical/ajax-loader3.gif" width="21" height="21">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </div>');
					$('#linkcenimpexam').show('fast');

					$('#linkcenimpmed').html('<div align="center"><img src="../../images/medical/ajax-loader3.gif" width="21" height="21">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </div>');
					$('#linkcenimpmed').show('fast');

					try{
						ajax=nuevoAjax();

						ajax.open("POST", "ordenes_cenimp.php",true);
						ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
						ajax.send(parametros);

						ajax.onreadystatechange=function(){

							if (ajax.readyState==4 && ajax.status==200){
								var respuesta=$.trim( ajax.responseText );
								var arrRespuesta = respuesta.split('$|--');
								//alert(arrRespuesta[1]);
								//if(respuesta=='ok'){
								var codsolicitud = arrRespuesta[1];

								linkCenimp = "<div align='center'><a href='../../hce/reportes/cenimp/Solicitud_"+codsolicitud+".pdf' target='_blank'>PDF Generado</a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </div>";

									$('#linkcenimp').html(linkCenimp);
									//$('#linkcenimp').show('slow');
									$('#linkcenimpexam').html(linkCenimp);

									$('#linkcenimpmed').html(linkCenimp);
									//$('#linkcenimpexam').show('slow');
								//}
							}

						}
						if ( !estaEnProceso(ajax) ) {
							ajax.send(null);
						}
					}catch(e){	}

				}
				else
				{
					abrir_ventana("ordenes_imp.php", document.forms.forma.wemp_pmla.value, document.forms.forma.whistoria.value, document.forms.forma.wingreso.value, wimprimir+"&alt="+alt+"&pacEps="+document.getElementById( "pacEPS" ).value );
				}
			}
			else
			{
				alert("Debe firmar digitalmente para poder realizar la impresión de a orden");
			}
		}
		else
		{
			if( !preguntarPorVisaulizarMedControl ){
				
				if(grabando && valido)
					document.forms.forma.submit();
			}
			else{
				preguntarPorVisaulizarMedControl = false;
				jConfirm( "Desea ver la orden de control?", "Alerta", function(x){
					if(x){
						mostrarMedicamentoControlAImprimir();
					}
					else{
						if(grabando && valido)
							document.forms.forma.submit();
					}
				});
				$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
			}
		}

}
/*****************************************************************************************************************************
 * Tipo de cambio en la pestaña y seccion
 ******************************************************************************************************************************/
function marcarCambio(tipo,indice, campo ){
	var bandera = document.getElementById("wmodificado"+tipo+indice);

	if(bandera && bandera.value){
		bandera.value = "S";
	}

	var bandera_alta = document.getElementById("wmodificadoimp"+tipo+indice);

	if(bandera_alta && bandera_alta.value){
		bandera_alta.value = "S";
	}

	/********************************************************************************
	 * Junio 13 de 2012
	 *
	 * Si una condicicon de suministro tiene dosis maxima por defecto se llena
	 * la dosis maxima
	 ********************************************************************************/
	agregarDosisMaxPorCondicion( tipo, indice, campo );
	/********************************************************************************/
}

/*****************************************************************************************************************************
 * Actualiza los medicamentos y procedimientos al estado de listos para impresion
 ******************************************************************************************************************************/
function actualizaImpresion( historia,ingreso,fecha )
{
	var parametros = "";

	parametros = "consultaAjaxKardex=42&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&whis="+historia+"&wing="+ingreso+"&wfec="+fecha;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var respuesta=$.trim( ajax.responseText );
				if(respuesta!='1'){
					//alert("No se pudo actualizar el artículo");
				}
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}

}


/*****************************************************************************************************************************
 * Actualiza el estado de alta del articulo enviado
 ******************************************************************************************************************************/
function marcarImpresion( campo,historia,ingreso,articulo,fecha,finicio,hinicio )
{
	var parametros = "";
	var articuloAlta = "off";

	/*
	if(campo.checked==true)
		articuloAlta = "on";
	*/
	if(campo=='quitar')
		articuloAlta = "off";

	parametros = "consultaAjaxKardex=39&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&articuloAlta="+articuloAlta+"&whis="+historia+"&wing="+ingreso+"&codigoArticulo="+articulo+"&wfecha="+fecha+"&wfecini="+finicio+"&wfecfin="+hinicio;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var respuesta=$.trim( ajax.responseText );
				if(respuesta!='1'){
					alert("No se pudo actualizar el artículo");
					if(campo.checked == true)
						campo.checked = false;
					else
						campo.checked = true;
				}
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}

}

/*****************************************************************************************************************************
 * Actualiza el estado de alta del articulo enviado
 ******************************************************************************************************************************/
function marcarImpresionExamen( campo,tipo_orden,numero_orden,examen,fecha,contExamenes )
{
	var parametros = "";
	//var examenAlta = "off";
	var imprimirExamen = "off";

	/*
	if(contExamenes!="")
	{
		var claseImprimir = $('#imgImprimir'+contExamenes).attr('class');

		if(claseImprimir == 'opacar aclarar' || claseImprimir == 'aclarar')
			imprimirExamen = "on";
	}
	*/

	if(campo!='' && campo.checked==true)
		imprimirExamen = "on";

	if(campo=='quitar')
		imprimirExamen = "off";

	parametros = "consultaAjaxKardex=40&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&imprimirExamen="+imprimirExamen+"&wcodigo_examen="+examen+"&wfecha="+fecha+"&wtipo_orden="+tipo_orden+"&wnumero_orden="+numero_orden+"&nroItem="+contExamenes;

	try{
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		// ajax.onreadystatechange=function(){
			// if (ajax.readyState==4 && ajax.status==200){
				var respuesta=$.trim( ajax.responseText );
				if(respuesta!='1'){
					alert("No se pudo actualizar la orden");
					if(campo=='quitar')
					{
						$('#'+fila).show();
					}
					else
					{
						cambiarClase('imgImprimir'+contExamenes,'opacar','aclarar');
					}

					/*
					if(campo!='')
					{
						if(campo.checked == true)
							campo.checked = false;
						else
							campo.checked = true;
					}
					*/
				}
			// }
		// }
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}

}

/*****************************************************************************************************************************
 * Punto de entrada del kardex de enfermeria.
 ******************************************************************************************************************************/
function consultarEsquemaInsulinaElemento(codEsquema){
	var contenedor = document.getElementById('cntEsquema');
	var art = $( "#wdexins" ).val();
	var parametros = "";

	parametros = "consultaAjaxKardex=05&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo="+codEsquema+"&art="+art;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				contenedor.innerHTML=$.trim( ajax.responseText );

				var cntEsquema = document.getElementById("cntEsquema");

				if(cntEsquema){
					cntEsquema.style.display = 'block';
				}
			
				validarAccionesDextrometer();
				
				$( "[id^=wdexint]", cntEsquema ).change(function(){
					mostrarConfirmarDextrometer();
				});
				
				// adicionarArticuloInsulina();
			}
			$.unblockUI();
		}
	}
	catch(e){
		console.log(e);
	}
}
 /*****************************************************************************************************************************
 * Confirma y redirecciona a la creación del kardex
 ******************************************************************************************************************************/
function confirmarGeneracion(){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;

	if(historia && ingreso){
		document.location.href = 'ordenes.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=b'+'&whistoria='+historia+'&wingreso='+ingreso+'&wfecha='+fecha;
	} else {
		alert("No se encontró historia, ingreso y fecha en los parametros de entrada.");
	}
}
/*****************************************************************************************************************************
 * ----------OPERACIONES DE INTERFAZ DE USUARIO----------------
 ******************************************************************************************************************************/
function resetArticulo(){ document.forms.forma.wnmmed.value = ''; }
/*****************************************************************************************************************************
 * Elimina un componente del multiselect de las infusiones
 ******************************************************************************************************************************/
function quitarComponente(idx){
  var elSel = document.getElementById('wtxtcomponentes' + idx);

  var i;
  for (i = elSel.length - 1; i>=0; i--) {
    if (elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}
/*****************************************************************************************************************************
 * Limpia el resultado de los buscadores
 ******************************************************************************************************************************/
function limpiarBuscador(){
	var resultados = document.getElementById('cntMedicamento');

	resultados.innerHTML = "";
}
/*****************************************************************************************************************************
 * Limpia el resultado del buscador de examenes
 ******************************************************************************************************************************/
function limpiarBuscadorExamenes(){
	var resultados = document.getElementById('cntExamenes').innerHTML = "";
}
/*****************************************************************************************************************************
 * Limpia el resultado del buscador de examenes
 ******************************************************************************************************************************/
function consultarServicioExamen(){
	var elemento = document.getElementById('wservexamen');
	var consOrden = document.getElementById('wconsserv');
	var vecParametros = "";

	consOrden.innerHTML = '';
	if(elemento && elemento.value != '' && consOrden){
		vecParametros = elemento.value.split("|");

		consOrden.innerHTML = vecParametros[1];
	}
}

/*****************************************************************************************************************************
 * Crea dinámicamente una fila en el detalle de medicamentos
 *
 * Dependiendo del tipo de protocolo se agregará a una lista u otra, los protocolos actuales son:
 *
 * 1. Normal, se agrega en la lista convencional
 * 2. Analgesia: Contenedor detAnalgesia
 ******************************************************************************************************************************/
function agregarArticulo( detKardexContenedor, deAlta ){


	if(!isset(deAlta))
	{
		var tipoProtocolo = detKardexContenedor.substr( 12 );
		detKardexContenedor = detKardexContenedor.substr( 0, 12 );

		var deAlta = false;
		// var contenedorArticulo = 'detKardexAddN';
		var contenedorArticulo = detKardexContenedor+tipoProtocolo;

		// $( "#trEncabezadoTbAdd", $( "#tbDetalleAdd" + tipoProtocolo ) ).css( { display: '' } );
		$( "#trEncabezadoTbAdd", $( "#tbDetalleAddN" ) ).css( { display: '' } );
	}
	else
	{
		var tipoProtocolo = "N";
		var contenedorArticulo = 'detKardexAddImpN';
	}

	var idx = 0;
	// var tipoProtocolo = "%";
	var cntDetalleKardex = "";

	var idFilaNueva = "", idColumnaArticulo = "", idCampoArticulo = "", idCampoDosis = "", idUnidadManejo = "";
	var idDuplicable = "", idDispensable = "", idDiasVencimiento = "", idFormaFarmaceutica = "", idOcultoFracciones = "", idOcultoVence = "", idCantidadUnidadManejo = "";
	var idUnidadDosis = "", idPeriodicidad = "", idCondicion = "", idVia = "", idFechaInicio = "", idBtnFechaInicio = "", idChkConfirmacion = "", idChkNoEnviar = "";
	var idDiasTratamiento = "", idDosisMaximas = "", idObservaciones = "", idIndiceMovimiento = "";

	var elementoAnteriorDetalle = 0;
	var posicionActual = 0;

	var tipoProtocoloAux = "";

	tipoProtocoloAux = tipoProtocolo;
	switch (tipoProtocolo) {
		case 'N':
			idx = elementosDetalle-1;

			posicionActual = elementosDetalle;
//			tipoProtocoloAux = tipoProtocolo;
		break;
	case 'A':
			idx = elementosAnalgesia-1;

			posicionActual = elementosAnalgesia;
//			tipoProtocoloAux = tipoProtocolo;
		break;
	case 'U':
			idx = elementosNutricion-1;

			posicionActual = elementosNutricion;
//			tipoProtocoloAux = tipoProtocolo;
		break;
	case 'Q':
			idx = elementosQuimioterapia-1;

			posicionActual = elementosQuimioterapia;
//			tipoProtocoloAux = tipoProtocolo;
		break;

	case 'LQ':
			idx = elementosLev-1;

			posicionActual = elementosLev;
//			tipoProtocoloAux = tipoProtocolo;
		break;
	default:
			idx = elementosDetalle-1;

			posicionActual = elementosDetalle;
		break;
	}

	// tipoProtocoloAux = "N";

	elementoAnteriorDetalle = document.getElementById("wnmmed"+tipoProtocoloAux+idx);
	cntDetalleKardex = document.getElementById(detKardexContenedor + tipoProtocoloAux);
	cntDetalleKardex = document.getElementById(detKardexContenedor + "N");	//Esto para que siempre agregue en la misma tabla

	idTabla = "tbDetalle%";

//	idFilaNueva = 'tr'+ tipoProtocoloAux + posicionActual;
	idFilaNueva = 'trFil' + posicionActual;
	idtr = 'trFil' + tipoProtocoloAux + posicionActual;

	idColumnaArticulo = 'wcolmed' + tipoProtocoloAux + posicionActual;
	idCampoArticulo = 'wnmmed'+ tipoProtocoloAux + posicionActual;
	idCampoDosis = 'wdosis' + tipoProtocoloAux + posicionActual;
	idUnidadManejo = 'wcundmanejo' + tipoProtocoloAux + posicionActual;
	idDuplicable = 'whduplicable' + tipoProtocoloAux + posicionActual;
	idDispensable = 'whdispensable' + tipoProtocoloAux + posicionActual;
	idDiasVencimiento = 'whdiasvence' + tipoProtocoloAux + posicionActual;
	idModificado = 'wmodificado' + tipoProtocoloAux + posicionActual;
	idFormaFarmaceutica = 'wfftica' + tipoProtocoloAux + posicionActual;
	idOcultoFracciones = 'whcmanejo' + tipoProtocoloAux + posicionActual;
	idOcultoVence = 'whvence' + tipoProtocoloAux + posicionActual;
	idCantidadUnidadManejo = 'wcumanejo' + tipoProtocoloAux + posicionActual;

	idUnidadDosis = 'wudosis' + tipoProtocoloAux + posicionActual;
	idPeriodicidad = 'wperiod' + tipoProtocoloAux + posicionActual;
	idCondicion = 'wcondicion' + tipoProtocoloAux + posicionActual;
	idVia = 'wviadmon' + tipoProtocoloAux + posicionActual;
	idFechaInicio = 'wfinicio' + tipoProtocoloAux + posicionActual;
	idFechahInicio = 'whfinicio' + tipoProtocoloAux + posicionActual;
	idBtnFechaInicio = 'btnFecha' + tipoProtocoloAux + posicionActual;
	idChkConfirmacion = 'wchkconf' + tipoProtocoloAux + posicionActual;
	idChkNoEnviar = 'wchkdisp' + tipoProtocoloAux + posicionActual;
	idChkimp_art = 'wchkimp_art' + tipoProtocoloAux + posicionActual;
	idDiasTratamiento = 'wdiastto' + tipoProtocoloAux + posicionActual;
	idDosisMaximas = 'wdosmax' + tipoProtocoloAux + posicionActual;
	idObservaciones = 'wtxtobs' + tipoProtocoloAux + posicionActual;
	idObservacionesAdd = 'wtxtobsadd' + tipoProtocoloAux + posicionActual;
	
	var idFamiliaATC = 'watc' + tipoProtocoloAux + posicionActual;
	
	var idEsAntibiotico = 'wesantibiotico' + tipoProtocoloAux + posicionActual;
	var idProfilaxis 	= 'wprofilaxis' + tipoProtocoloAux + posicionActual;
	var idTratamiento 	= 'wtratamiento' + tipoProtocoloAux + posicionActual;
	
	var idTimeUltimaAplicacion = "wtimeultimaaplicacion" + tipoProtocoloAux + posicionActual;

	var idEsNPT = 'wesnpt' + tipoProtocoloAux + posicionActual;
	
	var idTieneCTC = 'wtieneCTC' + tipoProtocoloAux + posicionActual;
	
	var idEsPediatrico 	= 'wespediatrico' + tipoProtocoloAux + posicionActual;
	var idConMed1 		= 'wconmed1' + tipoProtocoloAux + posicionActual;
	var idConMed2 		= 'wconmed2' + tipoProtocoloAux + posicionActual;
	
	var idPorProtocolo	= 'wporprotocolo' + tipoProtocoloAux + posicionActual;
	
	idArtProtocolo = 'whartpro' + tipoProtocoloAux + posicionActual;

	idImprimir = 'wimp' + tipoProtocoloAux + posicionActual;

	idOriginal = 'widoriginal' + tipoProtocoloAux + posicionActual;

	idIndiceMovimiento = "tr" + tipoProtocoloAux;
	
	var wdrautorizado	= 'wdrautorizado' + tipoProtocoloAux + posicionActual;
	var wjusparaautorizar	= 'wjusparaautorizar' + tipoProtocoloAux + posicionActual;
	

	if(!elementoAnteriorDetalle || elementoAnteriorDetalle.value != '' || posicionActual == 0){

		/******************************************************************************************
		 * Noviembre 21 de 2011
		 ******************************************************************************************/
		//Fecha y hora actual para colocar automaticamente la fecha y hora actual 2009-05-10 a las:10:00
		var fechaActual = new Date();

		horaActual = fechaActual.getHours();

		if( $( "#pacienteDeAyudaDx" ).val() != 'on' ){
			
			if(horaActual % 2 != 0){
				//fechaActual.setHours(fechaActual.getHours() + 1);
				fechaActual = new Date( fechaActual.getTime() + 1000*3600 );	//Creo la fecha y hora con una hora de adelanto
			} else {
				//fechaActual.setHours(fechaActual.getHours() + 2);
				fechaActual = new Date( fechaActual.getTime() + 2000*3600 );	//Creo la fecha y hora con dos horas de adelanto
			}
		}
		else{
			if(horaActual % 2 != 0){
				fechaActual = new Date( fechaActual.getTime() - 1000*3600 );	//Si es impar resto una hora
			}
		}

		diaActual = fechaActual.getDate();
		mesActual = fechaActual.getMonth() + 1;
		anioActual= fechaActual.getFullYear();
		/******************************************************************************************/

		horaActual = fechaActual.getHours();

		if(mesActual && mesActual.toString().length == 1){
			mesActual = "0" + mesActual.toString();
		}

		if(diaActual && diaActual.toString().length == 1){
			diaActual = "0" + diaActual.toString();
		}

		if( horaActual < 10 ){
			horaActual = "0"+horaActual;
		}

		fechaCompuesta = anioActual + "-" + mesActual + "-" + diaActual + " a las:" + horaActual + ":00";

		//Fila nueva
		var fila = document.createElement("tr");
		fila.setAttribute('id',idFilaNueva);
		fila.setAttribute('idtr',idtr);

		//Columnas nuevas
		var columna1 = document.createElement("td");		//Acciones
		var columna2 = document.createElement("td");		//Articulo
		var columna3 = document.createElement("td");		//Dosis
		var columna4 = document.createElement("td");		//Periodicidad
		var columna5 = document.createElement("td");		//Condición de suministro
		var columna6 = document.createElement("td");		//Forma farmaceutica
		var columna7 = document.createElement("td");		//Fecha y hora de inicio
		var columna8 = document.createElement("td");		//Dosis maxima
		var columna9 = document.createElement("td");		//Via administracion
		var columna10 = document.createElement("td");		//Confirmar
		var columna11 = document.createElement("td");		//Dias tratamiento
		var columna12 = document.createElement("td");		//Observaciones
		var columna13 = document.createElement("td");		//Unidad de manejo
		var columna14 = document.createElement("td");		//No dispensar
		var columna15 = document.createElement("td");		//Nombre protocolo
		var colFiltroAntibioticos = document.createElement("td");		//Nombre protocolo

		//Centradas
		columna1.setAttribute('align','center');
		columna10.setAttribute('align','center');
		columna14.setAttribute('align','center');
		columna15.setAttribute('align','center');

		columna2.setAttribute('id',idColumnaArticulo);

		/******************************************************************************
		CONTENIDO DE LAS COLUMNAS
		*******************************************************************************/
		//Link de grabar
		var link1 = document.createElement("a");
		link1.setAttribute('href','#null');
		if(!esIE){
			link1.setAttribute('onClick','grabarArticulo('+ posicionActual +',"'+tipoProtocolo+'");');
		}else{
			link1.onclick  = new Function('evt','grabarArticulo('+ posicionActual +',"'+tipoProtocolo+'");');
		}
		var img1 = document.createElement("img");
		img1.setAttribute('src','../../images/medical/root/grabar.png');

		link1.appendChild(img1);

//		debugger;
		//Link de borrar
		var atr = new Array();
		atr['onClick'] = "quitarArticulo('"+posicionActual+"','"+tipoProtocolo+"','','"+contenedorArticulo+"');";
		
		if( agregandoArticuloPorDextrometer )
			atr['style'] = "display:none;";

		var link2 = crearCampo("4","",tipoProtocolo+".2",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");

		//Input de articulo
		var articulo = document.createElement("input");
		articulo.setAttribute('type','text');
		articulo.setAttribute('name',idCampoArticulo);
		articulo.setAttribute('id',idCampoArticulo);
		articulo.setAttribute('size','60');
		articulo.setAttribute('readOnly','readOnly');
		articulo.className = 'campo2';
		if(!esIE){
			articulo.setAttribute('style','border:0px');
		}else{
			articulo.style.setAttribute('border','0px');
		}

		//Dosis a aplicar
		var atr = new Array();
		atr['size'] = "7";
		atr['maxLength'] = "7";
		atr['class'] = "campo2";
		atr['onKeyPress'] = "return validarEntradaDecimal(event);";

		var dosis = crearCampo("1",idCampoDosis,tipoProtocolo+".5",atr,"");

		//Unidad de manejo
		var unidadManejo = document.createElement("input");
		unidadManejo.setAttribute('type','hidden');
		unidadManejo.setAttribute('name',idUnidadManejo);
		unidadManejo.setAttribute('id',idUnidadManejo);

		//Total de fracciones dentro de la unidad de manejo Oculto
		var ocultoFracciones = document.createElement("input");
		ocultoFracciones.setAttribute('type','hidden');
		ocultoFracciones.setAttribute('name',idOcultoFracciones);
		ocultoFracciones.setAttribute('id',idOcultoFracciones);

		//Vence
		var vence = document.createElement("input");
		vence.setAttribute('type','hidden');
		vence.setAttribute('name',idOcultoVence);
		vence.setAttribute('id',idOcultoVence);
		
		//<input id="wmodificadoN0" type="hidden" value="S" name="wmodificadoN0">
		//Dias vencimiento
		var diasVencimiento = document.createElement("input");
		diasVencimiento.setAttribute('type','hidden');
		diasVencimiento.setAttribute('name',idDiasVencimiento);
		diasVencimiento.setAttribute('id',idDiasVencimiento);

		//Modificado
		var wmodificado = document.createElement("input");
		wmodificado.setAttribute('type','hidden');
		wmodificado.setAttribute('name',idModificado);
		wmodificado.setAttribute('id',idModificado);
		wmodificado.setAttribute('value','N');

		//Es dispensable
		var dispensable = document.createElement("input");
		dispensable.setAttribute('type','hidden');
		dispensable.setAttribute('name',idDispensable);
		dispensable.setAttribute('id',idDispensable);


		//Es duplicable
		var duplicable = document.createElement("input");
		duplicable.setAttribute('type','hidden');
		duplicable.setAttribute('name',idDuplicable);
		duplicable.setAttribute('id',idDuplicable);

		//Protocolo del articulo
		var artProtocolo = document.createElement("input");
		artProtocolo.setAttribute('type','hidden');
		artProtocolo.setAttribute('name',idArtProtocolo);
		artProtocolo.setAttribute('id',idArtProtocolo);

		//Protocolo del articulo
		var artImprimir = document.createElement("input");
		artImprimir.setAttribute('type','hidden');
		artImprimir.setAttribute('name',idImprimir);
		artImprimir.setAttribute('id',idImprimir);
		if(deAlta)
			artImprimir.setAttribute('value','on');
		else
			artImprimir.setAttribute('value','off');

		//Checkbox de impresion
		var chkimp = document.createElement("input");
		chkimp.setAttribute('id','wchkimp'+idArtProtocolo);
		chkimp.setAttribute('name','wchkimp'+idArtProtocolo);
		chkimp.setAttribute('type','checkbox');
		chkimp.setAttribute('style','display:none');

		//Cantidad de unidad de manejo
		var cantidadUnidadManejo = document.createElement("input");
		cantidadUnidadManejo.setAttribute('type','text');
		cantidadUnidadManejo.setAttribute('name',idCantidadUnidadManejo);
		cantidadUnidadManejo.setAttribute('id',idCantidadUnidadManejo);
		cantidadUnidadManejo.setAttribute('size','4');
		cantidadUnidadManejo.setAttribute('disabled','disabled');
		cantidadUnidadManejo.setAttribute('maxLength','4');
		cantidadUnidadManejo.setAttribute('value','1');
		cantidadUnidadManejo.className = 'campo2';
		if(!esIE){
			cantidadUnidadManejo.setAttribute('onKeyPress', 'return validarEntradaDecimal(event);');
		}else{
			cantidadUnidadManejo.onkeypress = new Function('evt','return validarEntradaDecimal(event);');
		}

		/***********************************************
		LLENA LOS SELECTS PARA UBICARLOS EN LA FILA
		************************************************/

		var unidadDosis = document.createElement("select");

		unidadDosis.setAttribute('id',idUnidadDosis);
		unidadDosis.className = 'seleccion';
		unidadDosis.setAttribute('disabled','disabled');

		//Diferencias de navegadores
		if(!esIE){
			unidadDosis.innerHTML = document.getElementById("wmunidadesmedida").innerHTML;
			unidadDosis.setAttribute('style','border:120px');
		}else {
			unidadDosis.style.setAttribute('width','120px');
			unidadDosis.style.setAttribute('style','font-family: verdana; color: black');

			//Unidades dosis
			var opcionesMaestro = document.getElementById("wmunidadesmedida").options;
			var cont1 = 0;
			var opcionTmp = null;

			while(opcionesMaestro[cont1]){
				opcionTmp = document.createElement("option");
				unidadDosis.options.add(opcionTmp);

				opcionTmp.innerText = opcionesMaestro[cont1].text;
				opcionTmp.value = opcionesMaestro[cont1].value;

				cont1++;
			}
		}


		var formaFtica = document.createElement("select");

		formaFtica.setAttribute('id',idFormaFarmaceutica);
		formaFtica.className = 'seleccion';
		formaFtica.setAttribute('disabled','disabled');

		//Diferencias de navegadores
		if(!esIE){
			formaFtica.innerHTML = document.getElementById("wmfftica").innerHTML;
			formaFtica.setAttribute('style','border:120px');

		}else {
			formaFtica.style.setAttribute('width','120px');
			formaFtica.style.setAttribute('style','font-family: verdana; color: black');

			//Unidades dosis
			var opcionesMaestro = document.getElementById("wmfftica").options;
			var cont1 = 0;
			var opcionTmp = null;

			while(opcionesMaestro[cont1]){
				opcionTmp = document.createElement("option");
				formaFtica.options.add(opcionTmp);

				opcionTmp.innerText = opcionesMaestro[cont1].text;
				opcionTmp.value = opcionesMaestro[cont1].value;

				cont1++;
			}
		}

		var atr = new Array();
		atr['class'] = "seleccion";
		atr['onChange'] = "marcarCambio('"+tipoProtocolo+"','"+posicionActual+"', this )";
		var periodicidad = '';
		if( !agregandoArticuloPorDextrometer )
			periodicidad = crearCampo("6",idPeriodicidad,tipoProtocolo+".7",atr,document.getElementById("wmperiodicidades").innerHTML);
		else
			periodicidad = crearCampo("6",idPeriodicidad,tipoProtocolo+".7",atr,document.getElementById("wdexfrecuencia").innerHTML);

		var atr = new Array();
		atr['class'] = "seleccion";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		var viaAdmon = crearCampo("6",idVia,tipoProtocolo+".8",atr,document.getElementById("wmviaadmon").innerHTML);

		var atr = new Array();
		atr['class'] = "seleccion";
		atr['onChange'] = 'agregarDosisMaxPorCondicion(\''+tipoProtocoloAux+'\','+posicionActual+', this )';
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"',this)";
		var condicion = crearCampo("6",idCondicion,tipoProtocolo+".10",atr,document.getElementById("wmcondicionessuministro").innerHTML);

		//Fecha y hora de administracion
		var fini = document.createElement("input");
		fini.setAttribute('id',idFechaInicio);
		fini.setAttribute('name',idFechaInicio);
		fini.setAttribute('type','text');
		fini.setAttribute('size','25');
		fini.setAttribute('value',fechaCompuesta);
		fini.setAttribute('readOnly','readonly');
		fini.className = 'campo2';

		//Fecha y hora de administracion Aux
		var fhini = document.createElement("input");
		fhini.setAttribute('id',idFechahInicio);
		fhini.setAttribute('name',idFechahInicio);
		fhini.setAttribute('type','hidden');
		fhini.setAttribute('size','25');
		fhini.setAttribute('value',fechaCompuesta);
		fhini.className = 'campo2';

		//Observaciones Add
		var obseradd = document.createElement("input");
		obseradd.setAttribute('id',idObservacionesAdd);
		obseradd.setAttribute('name',idObservacionesAdd);
		obseradd.setAttribute('type','hidden');
		obseradd.setAttribute('size','25');
		obseradd.setAttribute('value','');
		obseradd.className = 'campo2';

		var atr = new Array();
		atr['onClick'] = "calendario('"+posicionActual+"','"+tipoProtocoloAux+"');";

		var btnFini = crearCampo("3",idBtnFechaInicio,tipoProtocolo+".9",atr,"*");

		//Confirmado para central de mezclas
		var atr = new Array();
		var chkConf = crearCampo("5",idChkConfirmacion,tipoProtocolo+".11",atr,"");

		//Impresion del articulo
		var atr = new Array();
		atr['onClick'] = "marcarCambio('"+tipoProtocolo+"','"+posicionActual+"')";
		atr['checked'] = "checked";
		var chkImp_art = crearCampo("5",idChkimp_art,tipoProtocolo+".20",atr,"");


		//No se envia el articulo
		var atr = new Array();
		var chkDisp = crearCampo("5",idChkNoEnviar,"N.4",atr,"");

		//Dias de tratamiento
		var atr = new Array();
		atr['size'] = "3";
		atr['maxLength'] = "3";
		atr['class'] = "campo2";
		atr['onKeyPress'] = "return validarEntradaEntera(event);";
		atr['onKeyUp'] = "inhabilitarDosisMaxima( this,\'"+tipoProtocoloAux+"\', "+posicionActual+" );";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";

		var diasTto = crearCampo("1",idDiasTratamiento,tipoProtocolo+".12",atr,"");

		//Dosis maxima
		var atr = new Array();
		atr['size'] = "6";
		atr['maxLength'] = "6";
		atr['class'] = "campo2";
		atr['onKeyPress'] = "return validarEntradaEntera(event);";
		atr['onKeyUp'] = "inhabilitarDiasTratamiento( this,\'"+tipoProtocoloAux+"\',"+posicionActual+");";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";

		var dosMax = crearCampo("1",idDosisMaximas,tipoProtocolo+".13",atr,"");

		//Observaciones
		var atr = new Array();
		atr['rows'] = "2";
		atr['cols'] = "40";
		atr['onkeypress'] = "validarEntradaAlfabetica(event)";
		atr['onChange'] = "marcarCambio('"+tipoProtocoloAux+"','"+posicionActual+"')";
		var observaciones = crearCampo("2",idObservaciones,tipoProtocolo+".14",atr,"");

		//idOriginal
		var hiIdOrginal = document.createElement("input");
		hiIdOrginal.setAttribute('id',idOriginal);
		hiIdOrginal.setAttribute('name',idOriginal);
		hiIdOrginal.setAttribute('type','hidden');
		hiIdOrginal.setAttribute('value',0);
		
		
		
		
		
		//Campo es Antibiotico
		var inEsAntibiotico = document.createElement("input");
		inEsAntibiotico.setAttribute('id',idEsAntibiotico);
		inEsAntibiotico.setAttribute('name',idEsAntibiotico);
		inEsAntibiotico.setAttribute('type','hidden');
		inEsAntibiotico.setAttribute('value','');
		
		//Campo profilaxis
		var inProfilaxis = document.createElement("input");
		inProfilaxis.setAttribute('id',idProfilaxis);
		inProfilaxis.setAttribute('name','wfiltroantibiotico'+tipoProtocoloAux+posicionActual);
		inProfilaxis.setAttribute('type','radio');
		inProfilaxis.setAttribute('value',0);
		
		//Campo tratamiento
		var inTratamiento = document.createElement("input");
		inTratamiento.setAttribute('id',idTratamiento);
		inTratamiento.setAttribute('name','wfiltroantibiotico'+tipoProtocoloAux+posicionActual);
		inTratamiento.setAttribute('type','radio');
		inTratamiento.setAttribute('value',0);
		
		//Campo esPediatrico
		var ckPediatrico = document.createElement("input");
		ckPediatrico.setAttribute('id','wckpediatrico'+tipoProtocoloAux+posicionActual);
		ckPediatrico.setAttribute('name','wckpediatrico'+tipoProtocoloAux+posicionActual);
		ckPediatrico.setAttribute('type','checkbox');
		$( ckPediatrico ).css({display:'none'});
		
		
		
		
		
		//Campo es NPT
		var inEsNPT = document.createElement("input");
		inEsNPT.setAttribute('id',idEsNPT);
		inEsNPT.setAttribute('name',idEsNPT);
		inEsNPT.setAttribute('type','hidden');
		inEsNPT.setAttribute('value','');
		
		//Campo tiene CTC guardado en movhos_000134
		var inTieneCTC = document.createElement("input");
		inTieneCTC.setAttribute('id',idTieneCTC);
		inTieneCTC.setAttribute('name',idTieneCTC);
		inTieneCTC.setAttribute('type','hidden');
		inTieneCTC.setAttribute('value','');
		
		
		
		
		
		
		
		
		
		
		
		
		//Campo tiene CTC guardado en movhos_000134
		var inEsPediatrico = document.createElement("input");
		inEsPediatrico.setAttribute('id',idEsPediatrico);
		inEsPediatrico.setAttribute('name',idEsPediatrico);
		inEsPediatrico.setAttribute('type','hidden');
		inEsPediatrico.setAttribute('value','off');
		
		//Campo tiene CTC guardado en movhos_000134
		var inConMed1 = document.createElement("input");
		inConMed1.setAttribute('id',idConMed1);
		inConMed1.setAttribute('name',idConMed1);
		inConMed1.setAttribute('type','hidden');
		inConMed1.setAttribute('value','');
		
		//Campo tiene CTC guardado en movhos_000134
		var inConMed2 = document.createElement("input");
		inConMed2.setAttribute('id',idConMed2);
		inConMed2.setAttribute('name',idConMed2);
		inConMed2.setAttribute('type','hidden');
		inConMed2.setAttribute('value','');
		
		var idxArticuloControlImpresion = tipoProtocoloAux + posicionActual;
		var aArticuloControlImpresion = $( "<a href=#null id='wcontrolimp"+idxArticuloControlImpresion+"' style='display:none'>"
										  +"<img src='../../images/medical/root/americassu.png' width=17 height=17 border=0 title='ver formato de control'>"
										  +"</a>" );
		aArticuloControlImpresion.click(function(){
			mostrarMedicamentoControlAImprimir( idxArticuloControlImpresion );
		})
		$( "img",aArticuloControlImpresion ).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 20 });
		
		
		var inPorProtocolo = document.createElement("input");
		inPorProtocolo.setAttribute('id',idPorProtocolo);
		inPorProtocolo.setAttribute('name',idPorProtocolo);
		inPorProtocolo.setAttribute('type','hidden');
		inPorProtocolo.setAttribute('value','off');
		
		var inTimeUltimaAplicacion = document.createElement("input");
		inTimeUltimaAplicacion.setAttribute('id',idTimeUltimaAplicacion);
		inTimeUltimaAplicacion.setAttribute('name',idTimeUltimaAplicacion);
		inTimeUltimaAplicacion.setAttribute('type','hidden');
		inTimeUltimaAplicacion.setAttribute('value','0');
		
		
		//familias ATC
		var inATC = document.createElement("input");
		inATC.setAttribute('type','hidden');
		inATC.setAttribute('name',idFamiliaATC);
		inATC.setAttribute('id',idFamiliaATC);
		
		var inDrAutorizado = document.createElement("input");
		inDrAutorizado.setAttribute('id', wdrautorizado);
		inDrAutorizado.setAttribute('name', wdrautorizado);
		inDrAutorizado.setAttribute('type','hidden');
		inDrAutorizado.setAttribute('value','on');
		
		
		var inJusParaAutorizar = document.createElement("input");
		inJusParaAutorizar.setAttribute('id', wjusparaautorizar);
		inJusParaAutorizar.setAttribute('name', wjusparaautorizar);
		inJusParaAutorizar.setAttribute('type','hidden');
		inJusParaAutorizar.setAttribute('value','');
		

		/*******************************************************************************
		ANEXAR CONTENIDO A LAS COLUMNAS
		********************************************************************************/
//		columna1.appendChild(link1);
//		columna1.appendChild(link2);
		columna1.innerHTML += link2+chkImp_art;
		$( columna1 ).append( aArticuloControlImpresion );
		columna2.appendChild(articulo);
//		columna3.appendChild(dosis);
		columna3.innerHTML += dosis;
		columna3.appendChild(unidadDosis);
		columna3.appendChild(formaFtica);
//		columna4.appendChild(periodicidad);
		columna4.innerHTML += periodicidad;
//		columna5.appendChild(condicion);
		columna5.innerHTML += condicion;
		columna7.appendChild(fini);
//		columna7.appendChild(btnFini);
		columna7.innerHTML += btnFini;
//		columna8.appendChild(dosMax);
		columna8.innerHTML += dosMax;
//		columna9.appendChild(viaAdmon);
		columna9.innerHTML += viaAdmon;
//		columna10.appendChild(chkConf);
		columna10.innerHTML += chkConf;
//		columna11.appendChild(diasTto);
		columna11.innerHTML += diasTto;
//		columna12.appendChild(observaciones);
		columna12.innerHTML += observaciones;
//		columna14.appendChild(chkDisp);
		columna14.innerHTML += chkDisp;
		
		colFiltroAntibioticos.appendChild( inProfilaxis );
		$( colFiltroAntibioticos ).append( "<label>Profilaxis</label><br>" );
		colFiltroAntibioticos.appendChild( inTratamiento );
		$( colFiltroAntibioticos ).append( "<label>Tratamiento</label>" );
		colFiltroAntibioticos.appendChild( ckPediatrico );
		$( colFiltroAntibioticos ).append( "<label style='display:none'>Pediatrico</label>" );

		/*******************************************************************************
		ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
		********************************************************************************/

		fila.appendChild(columna1);
		fila.appendChild(columna2);
		fila.appendChild(columna15);
		fila.appendChild(columna14);
		fila.appendChild(columna3);
		fila.appendChild(columna4);
		fila.appendChild(columna9);
		fila.appendChild(columna7);
		fila.appendChild(columna5);
		fila.appendChild(columna10);
		fila.appendChild(colFiltroAntibioticos);
		fila.appendChild(columna11);
		fila.appendChild(columna8);
		fila.appendChild(columna12);
		

		//Anexo los campos hidden antes de anexar la fila
		cntDetalleKardex.appendChild(unidadManejo);
		cntDetalleKardex.appendChild(wmodificado);
		cntDetalleKardex.appendChild(fhini);
		cntDetalleKardex.appendChild(ocultoFracciones);
		cntDetalleKardex.appendChild(vence);
		cntDetalleKardex.appendChild(diasVencimiento);
		cntDetalleKardex.appendChild(dispensable);
		cntDetalleKardex.appendChild(duplicable);
		cntDetalleKardex.appendChild(artProtocolo);
		cntDetalleKardex.appendChild(artImprimir);
		cntDetalleKardex.appendChild(chkimp);
		cntDetalleKardex.appendChild(obseradd);
		cntDetalleKardex.appendChild(hiIdOrginal);
		cntDetalleKardex.appendChild(inEsAntibiotico);
		cntDetalleKardex.appendChild(inEsNPT);
		cntDetalleKardex.appendChild(inTieneCTC);
		//cntDetalleKardex.appendChild(formaFtica);

		cntDetalleKardex.appendChild(inEsPediatrico);
		cntDetalleKardex.appendChild(inConMed1);
		cntDetalleKardex.appendChild(inConMed2);
		cntDetalleKardex.appendChild(inPorProtocolo);
		cntDetalleKardex.appendChild(inTimeUltimaAplicacion);

		cntDetalleKardex.appendChild(inATC);
		
		//Campo nuevo que indica si el campo esta autorizado o no
		cntDetalleKardex.appendChild(inDrAutorizado);
		
		//Campo de justificacion para autorizar
		cntDetalleKardex.appendChild(inJusParaAutorizar);
		
		columna15.style.display = "none";



		if(deAlta==true)
		{
			columna5.style.display = "none";
			//columna9.style.display = "none";
			columna11.style.display = "none";
			columna14.style.display = "none";
			columna10.style.display = "none";
			document.getElementById('wchkimp'+idArtProtocolo).checked = true;
		}
		else
		{
			document.getElementById('wchkimp'+idArtProtocolo).checked = false;
		}

		document.getElementById('wchkimp'+idArtProtocolo).style.display="none";

		//ANEXAR LA FILA A LA TABLA
//		cntDetalleKardex.appendChild(fila);
		//Incremento del indice segun la lista
		switch (tipoProtocolo) {
			case 'N':
				elementosDetalle++;
				break;
			case 'A':
				elementosAnalgesia++;
				break;
			case 'U':
				elementosNutricion++;
				break;
			case 'Q':
				elementosQuimioterapia++;
				break;
			case 'LQ':
				elementosLev++;
				break;
			default:
				elementosDetalle++;
				break;
		}

		posicionActual++;

		var indice = 0;

		//Mueve al principio el articulo
		while(document.getElementById(idIndiceMovimiento+indice)){
			indice++;
		}
		
		//ANEXAR LA FILA A LA TABLA
		cntDetalleKardex.insertBefore(fila, cntDetalleKardex.firstChild);
	}
	else {
		//alert("Debe ingresar la información del articulo actual antes de adicionar uno nuevo");
	}
}
/*****************************************************************************************************************************
 * Crea dinámicamente una fila nueva para agregar una infusión
 ******************************************************************************************************************************/
function agregarInfusion(){
	var puedeAgregar = true;
	var cantFilas = document.getElementById("tbDetInfusiones").rows.length;

	//Fecha actual automatica
	var fechaActual = new Date();

	diaActual = fechaActual.getDate();
	mesActual = fechaActual.getMonth() + 1;
	anioActual= fechaActual.getFullYear();

	if(mesActual && mesActual.toString().length == 1){
		mesActual = "0" + mesActual.toString();
	}

	if(diaActual && diaActual.toString().length == 1){
		diaActual = "0" + diaActual.toString();
	}

	fechaCompuesta = anioActual + "-" + mesActual + "-" + diaActual;

	//Verifica si se selecciono previamente el examen
	if(cuentaInfusiones > 0){
		var idx = cuentaInfusiones-1;
		var componentes = document.getElementById('wtxtcomponentes'+idx);

		if(componentes && componentes.length){
			if(componentes.length == 0){
				puedeAgregar = false;
			}
		}
	}

	if(puedeAgregar){
		var cntDetalle = document.getElementById("detInfusiones");

	cuentaInfusiones++;

	//Fila nueva
	var fila = document.createElement("tr");
	fila.setAttribute('id','trIn'+cuentaInfusiones);

	//Columnas nuevas
	var columna1 = document.createElement("td");		//Acciones
	var columna2 = document.createElement("td");		//Fecha de solicitud
	var columna3 = document.createElement("td");		//Componentes de la infusión
	var columna4 = document.createElement("td");		//Observaciones infusión

	columna2.setAttribute('align','center');

	/******************************************************************************
	CONTENIDO DE LAS COLUMNAS
	*******************************************************************************/
	//Link de grabar
	var link1 = document.createElement("a");
	link1.setAttribute('href','#null');
	if(!esIE){
		link1.setAttribute('onClick', 'grabarInfusion('+ cuentaInfusiones +');');
	}else{
		link1.onclick  = new Function('evt','grabarInfusion('+ cuentaInfusiones +');');
	}

	var img1 = document.createElement("img");
	img1.setAttribute('src','../../images/medical/root/grabar.png');

	link1.appendChild(img1);

	//Link de borrar
	var atr = new Array();
	atr['onClick'] = "quitarInfusion('"+cuentaInfusiones +"');";

	var link2 = crearCampo("4","","2.3",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");
//	var link2 = document.createElement("a");
//	link2.setAttribute('href','#null');
//	if(!esIE){
//		link2.setAttribute('onClick','quitarInfusion('+ cuentaInfusiones +');');
//	}else{
//		link2.onclick  = new Function('evt','quitarInfusion('+ cuentaInfusiones +');');
//	}
//
//	var img2 = document.createElement("img");
//	img2.setAttribute('src','../../images/medical/root/borrar.png');

//	link2.appendChild(img2);

	//Ocultos
	var oculto1 = document.createElement("input");
	oculto1.setAttribute('type','hidden');
	oculto1.setAttribute('name','wmodificado2'+cuentaInfusiones);
	oculto1.setAttribute('id','wmodificado2'+cuentaInfusiones);
	oculto1.setAttribute('value','S');

	var oculto2 = document.createElement("input");
	oculto2.setAttribute('type','hidden');
	oculto2.setAttribute('name','windiceliq'+parseInt(cantFilas-1));
	oculto2.setAttribute('id','windiceliq'+parseInt(cantFilas-1));
	oculto2.setAttribute('value',cuentaInfusiones);

	//Anexo de fecha solicitado examen
	var fliq = document.createElement("input");
	fliq.setAttribute('id','wfliq'+cuentaInfusiones);
	fliq.setAttribute('name','wfliq'+cuentaInfusiones);
	fliq.setAttribute('type','text');
	fliq.setAttribute('size','10');
	fliq.setAttribute('value',fechaCompuesta);
	fliq.setAttribute('readOnly','readonly');
	fliq.className = 'campo2';

	var atr = new Array();
	atr['onClick'] = "calendario4('"+cuentaInfusiones +"');";

	var btnfliq = crearCampo("3","btnFechaLiq"+cuentaInfusiones,"2.4",atr,"*");

//	var btnfliq = document.createElement("input");
//	btnfliq.setAttribute('type','button');
//	btnfliq.setAttribute('id','btnFechaLiq'+cuentaInfusiones);
//	btnfliq.setAttribute('name','btnFechaLiq'+cuentaInfusiones);
//	btnfliq.setAttribute('value','*');
//
//	if(!esIE){
//		btnfliq.setAttribute('onClick', 'calendario4('+cuentaInfusiones+');');
//	}else {
//		btnfliq.onclick = new Function('evt','calendario4('+cuentaInfusiones+')');
//	}

	//Select multiple de componentes
	var atr = new Array();
	atr['onDblClick'] = "quitarComponente('"+cuentaInfusiones +"');";
	atr['multiple'] = "multiple";
	atr['size'] = "5";

	var componentes = crearCampo("6","wtxtcomponentes"+cuentaInfusiones,"2.5",atr,"");

//	var componentes = document.createElement("select");
//	componentes.setAttribute('id','wtxtcomponentes' + cuentaInfusiones);
//	componentes.setAttribute('multiple','multiple');
//	componentes.setAttribute('size','5');
//	if(!esIE){
//		componentes.setAttribute('onDblClick', 'quitarComponente('+cuentaInfusiones+');');
//	}else{
//		componentes.ondblclick  = new Function('evt','quitarComponente('+cuentaInfusiones+');');
//	}

	//Observaciones
	var atr = new Array();
	atr['rows'] = "5";
	atr['cols'] = "65";

	var observaciones = crearCampo("2","wobscomponentes"+cuentaInfusiones,"2.6",atr,"");

//	var observaciones = document.createElement("textarea");
//	observaciones.setAttribute('id','wobscomponentes' + cuentaInfusiones);
//	observaciones.setAttribute('rows','2');
//	observaciones.setAttribute('cols','60');
//	if(!esIE){
//		observaciones.setAttribute('onKeyPress', 'return validarEntradaAlfabetica(event);');
//	}else{
//		observaciones.onkeypress = new Function('evt','return validarEntradaAlfabetica(event);');
//	}

	/*******************************************************************************
	ANEXAR CONTENIDO A LAS COLUMNAS
	********************************************************************************/
//	columna1.appendChild(link1);
//	columna1.appendChild(link2);
	columna1.innerHTML += link2;
	columna1.appendChild(oculto1);
	columna1.appendChild(oculto2);
	columna2.appendChild(fliq);
//	columna2.appendChild(btnfliq);
	columna2.innerHTML += btnfliq;
//	columna3.appendChild(componentes);
	columna3.innerHTML += componentes;
//	columna4.appendChild(observaciones);
	columna4.innerHTML += observaciones;

	/*******************************************************************************
	ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
	********************************************************************************/
	fila.appendChild(columna1);
	fila.appendChild(columna2);
	fila.appendChild(columna3);
	fila.appendChild(columna4);

	//ANEXAR LA FILA A LA TABLA
	cntDetalle.appendChild(fila);

	cuentaInfusiones++;
	} else {
		alert('Por favor ingrese los componentes del liquido endovenoso antes de crear uno nueva.');
	}
}


// Compara dos fechas y retorna true si fecha >= fecha2
function compare_dates(fecha, fecha2)
  {
    var xMonth=fecha.substring(3, 5);
    var xDay=fecha.substring(0, 2);
    var xYear=fecha.substring(6,10);
    var yMonth=fecha2.substring(3, 5);
    var yDay=fecha2.substring(0, 2);
    var yYear=fecha2.substring(6,10);
    if (xYear> yYear)
    {
        return(true)
    }
    else
    {
      if (xYear == yYear)
      {
        if (xMonth> yMonth)
        {
            return(true)
        }
        else
        {
          if (xMonth == yMonth)
          {
            if (xDay>= yDay)
              return(true);
            else
              return(false);
          }
          else
            return(false);
        }
      }
      else
        return(false);
    }
}


/*****************************************************************************************************************************
 * Agrega un nuevo registro al Maestro de Estudios y Ayudas Diagnóstcas por medio de AJAX
 ******************************************************************************************************************************/
function agregarNuevoExamen()
{
	var nonmbreExamen = document.getElementById('wnomproc').value;
	var tipoServicio = document.getElementById('wselTipoServicio').options[ document.getElementById('wselTipoServicio').selectedIndex ].value;

	if(tipoServicio=='' || tipoServicio==' ' || tipoServicio=='%')
	{
		alert('Debe seleccionar un tipo de orden para poder agregar una nueva ayuda o procedimiento');
		return false;
	}

	if(nonmbreExamen=='' || nonmbreExamen==' ')
	{
		alert('El nombre de la ayuda o procedimiento no puede estar vacío');
		return false;
	}


	parametros = "consultaAjaxKardex=44&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoServicio="+tipoServicio+"&descripcion="+nonmbreExamen+"&especialidad="+document.forms.forma.wespecialidad.value;

	//alert(parametros);

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var arrItem = $.trim( ajax.responseText );

				if(arrItem && arrItem!="")
				{
					var item = arrItem.split("|");

					if(item[0] != '000' ){
					seleccionarAyudaDiagnostica(item[1],item[2],item[3],item[4],item[5],item[6],item[7],item[8],item[9],item[10],item[11],item[12]);
					}else{
						alert(item[1]);
					}

					document.getElementById('wnomproc').onfocus = function(){
						if( justificacionUltimoExamenAgregado ){
							justificacionUltimoExamenAgregado.focus();
							justificacionUltimoExamenAgregado = '';
						}
					};
			//    		this.focus();
			//    		this.select();

					document.getElementById("btnCerrarVentana").style.display = 'none';
					document.getElementById('wnomproc').value = '';
				}

			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}


/*****************************************************************************************************************************
 * Agrega dinámicamente una nueva fila para un examen de laboratorio nuevo
 ******************************************************************************************************************************/
function agregarExamen(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, nroItem, crearOrden, requiereJustificacion, noPos, aydJustificacion, funcInternas, datosAdicionales, esOfertado, realizaUnidad ){

	var datosAdicionales = datosAdicionales || {};

	if(!aydJustificacion)
		aydJustificacion = "";

	var aux = document.createElement("div");
	aux.innerHTML = nombreExamen.toUpperCase();

	//HTML de la orden
	var iHTMLOrden = "";//"<tr id='del"+centroCostos+""+consecutivoOrden+"'>";

	var atr = new Array();
	atr['onClick'] = "cancelarOrden('"+centroCostos+"','"+consecutivoOrden+"');";
	iHTMLOrden += crearCampo("4","","4.3",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");

	iHTMLOrden += "<a href='#null' onclick=intercalarElemento(\""+centroCostos+""+consecutivoOrden+"\"); style='font-size:10pt'>";
	iHTMLOrden += "<b>&nbsp;&nbsp;&nbsp;<u>Orden Nro. "+consecutivoOrden+"</u></b></a>&nbsp;&nbsp;&nbsp;";

	iHTMLOrden += "<div id=\""+centroCostos+""+consecutivoOrden+"\" class='fila2'>";
	//iHTMLOrden += "<div style='display:none'><br>Observaciones de la orden: <br>";

	var atr = new Array();
	atr['rows'] = "2";
	atr['cols'] = "60";
	atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";

	//iHTMLOrden += crearCampo("2","wtxtobsexamen"+centroCostos+""+consecutivoOrden,"4.4",atr,".");

//	iHTMLOrden += "</div></td></tr>";

	var idContenedor = centroCostos+""+consecutivoOrden;
	var idContenedor = centroCostos;
	var puedeAgregar = true;
	var esHospitalario = false;

	if(puedeAgregar){

		var cntDetalle = document.getElementById("detExamenes"+idContenedor);

		//Fecha actual automatica
		var fechaActual = new Date();

		diaActual = fechaActual.getDate();
		mesActual = fechaActual.getMonth() + 1;
		anioActual= fechaActual.getFullYear();

		horas = fechaActual.getHours();
		minutos = fechaActual.getMinutes()
		segundos = fechaActual.getSeconds()

		if(mesActual && mesActual.toString().length == 1){
			mesActual = "0" + mesActual.toString();
		}

		if(diaActual && diaActual.toString().length == 1){
			diaActual = "0" + diaActual.toString();
		}

		var fechaCompuesta = anioActual + "-" + mesActual + "-" + diaActual;
		var fechaActualOrden = anioActual+"-"+mesActual+"-"+diaActual;

		/*******************************************************************************************
		 * verificando si hay un examen igual para el mismo día
		 *******************************************************************************************/
		 permitir_registro = false;

		//Se verifica dentro de la tabla de examPendientes cuantos examenes existen con el mismo codigo. Jonatan Lopez 7 Mayo 2014
		$('table[id=examPendientes]').find("[cod_examen^="+codExamen+"]").each(function(){

			//Si encuentra almenos un examen igual evalua la fecha de realizacion, si el examen ya esta para la fecha hara que la variable permitir_registro sea igual a true
			//al ser true mostrara el mensaje de que el examen ya existe para el mismo dia.
			if ($(this).val() == fechaActualOrden){

			permitir_registro = true;

			}

		});

		var campoFecha = $('#wfechagrabacion').val();

		//Se valida si el examen existe para el mismo dia con la variable permitir_registro == true, si el false permite hacer el registro. Jonatan Lopez 7 Mayo 2014
		if( permitir_registro == true){

			nombExamen = nombreExamen.toUpperCase();
			
			// if( !confirm( "El examen "+nombExamen+" ya existe para el mismo día. ¿Desea agregarlo?" ) ){

				// if( !funcInternas ){
					// funcInternas();
				// }
				// return false;
			// }

			jConfirm( "El examen "+nombExamen+" ya existe para el mismo día. ¿Desea agregarlo?", "Alerta", function( resp ){
				if( resp ){

					mostrarModalAdicionesExamen();
				}
				else{
					if( funcInternas ){
						funcInternas();
					}
					return false;
				}
			});
		}
		else{
			mostrarModalAdicionesExamen();
		}

		/*******************************************************************************************/

	}
	else {
		alert('Por favor ingrese la información del examen antes de agregar uno nuevo.');

		if( funcInternas ){
			funcInternas();
		}
	}


	function mostrarModalAdicionesExamen(){

		console.log(datosAdicionales);
		
		try{
			if( datosAdicionales && datosAdicionales["Tipo de muestra"] ){
				if( datosAdicionales["Tipo de muestra"].length > 1 
					|| (datosAdicionales["Tipo de muestra"].length == 1 && datosAdicionales["Tipo de muestra"]["Sitio anatomico"].length > 1 ) )
					datosAdicionales["Tipo de muestra"].unshift({ codigo : "", descripcion :"Seleccione...", "Sitio anatomico" : [{codigo: "", descripcion: "Seleccione..."}] });
			}
		}
		catch(e){
			console.log( "No hay datos adicionales" )
			console.log( datosAdicionales )
		}
		
		console.log(datosAdicionales);

		/**************************************************************************************************
		 * Aquí muestro la modal con los datos adicionales del examen
		 **************************************************************************************************/
		//Muestro la modal para mostrar los datos adicionales

		//Creo el objeto que tiene la funcionalidad que muestra los datos adicionales del examen
		var a = new mad({ 
						close	: function(){ 
										$.unblockUI();  
										
										if( funcInternas ){
											funcInternas();
										}
									},
						accept	: function( objMad ){						
									$.unblockUI(); 
									datosAdicionales = objMad.result();
									funcInternaAgregarExamen();
							},
						title	: nombreExamen.toUpperCase().replace( /_/gi, ' ' )+"<br><div style='background-color:lightgray;font-size:12pt;'>DATOS ADICIONALES</div>",
						data	: datosAdicionales,
					});
		
		//Si hya por lo menos un dato no seleccionado muestro la modal, caso contrario se graba el examen
		a.create()
		
		
		var datosOk = true;
		if( $( "select", a.obj ).length > 0 ){
			$( "select", a.obj ).each(function(){
				if( $( this ).val() == '' || $( this ).val() == null || !$( this ).val() )
					datosOk = false;
			})
		}

		if( !datosOk ){
			// $( document.body ).append( a.obj )

			$.blockUI({ 
				message	: a.obj ,
				css : { 
						width:'60%', 
						left: '20%',
						top: '20%',
						overflow: 'auto',
						maxHeight: '60%',
					 },
			});
		}
		else{
			datosAdicionales = a.result();
			funcInternaAgregarExamen();
		}
		
		

		/***********************************************************************************************/
	}


		/**
		 * Se le pregunta al médico si el estudio se realiza en la unidad en que se encuentra al paciente o no
		 */
		function funcInternaAgregarExamen(){
			
			funcInternaAgregarExamen2();
			
			// if( realizaUnidad ){
				// $.alerts.okButton 		= "SI";
				// $.alerts.cancelButton 	= "NO";
				// jConfirm( "El estudio <b>" + reemplazarTodo(nombreExamen,"_"," ") + "</b> se realizará en el centro de costos <b>" + $( "#nombreServicioActual" ).val() + "</b>?", "ALERTA", function( resp ){
					// if( resp ){
						// esOfertado = false;
						// jConfirm( "Desea imprimir el sticker para el estudio <b>" + reemplazarTodo(nombreExamen,"_"," ") + "</b>?", "ALERTA", function( resp ){
							// if( resp ){
								
								// stickers_ga.push({
									// cuentaExamenes	:	cuentaExamenes,
									// wnmexamen		:	cuentaExamenes,
									// hexcco			:	cuentaExamenes,
									// hexcod			:	cuentaExamenes,
								// });
							// }
							
							// $.alerts.okButton 		= "Aceptar";
							// $.alerts.cancelButton 	= "Cancelar";
							
							// funcInternaAgregarExamen2()
						// })
					// }
					// else{
						
						// $.alerts.okButton 		= "Aceptar";
						// $.alerts.cancelButton 	= "Cancelar";
						
						// funcInternaAgregarExamen2();
					// }
				// });
			// }
			// else{
				// funcInternaAgregarExamen2();
			// }
		}
		
		function funcInternaAgregarExamen2(){

			//Fila nueva
			var fila = document.createElement("tr");
			fila.setAttribute('id','trEx'+cuentaExamenes);
			//fila.setAttribute('class','encabezadoTabla');

			/*
			// columnas anteriores
			var columna7 = document.createElement("td");		//Nro de orden
			var columna1 = document.createElement("td");		//Acciones
			var columna2 = document.createElement("td");		//Examenes
			var columna3 = document.createElement("td");		//Observaciones
			var columna4 = document.createElement("td");		//Estado
			var columna5 = document.createElement("td");		//Fecha solicitado examen
			var columna6 = document.createElement("td");		//Justificacion
			*/

			//Columnas nuevas
		//	var columna1 = document.createElement("td");		//Acciones
			var columna2 = document.createElement("td");		//Fecha solicitado examen
		//	var columna3 = document.createElement("td");		//Hora de solicitud
			var columna4 = document.createElement("td");		//Tipo de servicio
			var columna5 = document.createElement("td");		//Procedimiento
			var columna6 = document.createElement("td");		//Justificacion
			var columna7 = document.createElement("td");		//Estado
			var columna8 = document.createElement("td");		//Nro de orden
			var columna9 = document.createElement("td");		//Acciones
			var columna10 = document.createElement("td");		//Bitacora


			columna2.setAttribute('align','center');
			columna2.setAttribute('nowrap','nowrap');

			/******************************************************************************
			CONTENIDO DE LAS COLUMNAS
			*******************************************************************************/
			//Link de borrar
			var atr = new Array();
			atr['onClick'] = "quitarExamen('"+cuentaExamenes+"','','on');";

			var link2 = crearCampo("4","","4.5",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");


			//Imprimir
			var atrImp = new Array();

			atrImp['onClick'] = "marcarImpresionExamen(this,'"+centroCostos+"','"+consecutivoOrden+"','"+codExamen+"','"+fechaCompuesta+"','"+nroItem+"');";
			atrImp['checked'] = "checked";


			var chkImp = crearCampo("5","imprimir_examen","5.4",atrImp,"");
			var linkImp = "<img src='../../images/medical/hce/icono_imprimir.png' border='0' width='17' height='17'>";

			//Si la ayuda o procedimiento es hospitalario llega tambien por defecto con estado pendiente
			if(centroCostos == "H"){
				esHospitalario = true;
			}

			//Examen y estado del examen

			var atr = new Array();
			atr['rows'] = "2";
			atr['cols'] = "40";
			// atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";

			if( requiereJustificacion == "on" ){
				atr['class'] = "fondoamarillo";
			}

			var justificacion = crearCampo("2","wtxtjustexamen"+cuentaExamenes,"4.6",atr,aydJustificacion);

			var atr = new Array();
			atr['rows'] = "2";
			atr['cols'] = "40";
			atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";
			atr['readonly'] = "readonly";

			//var observaciones = crearCampo("2","wtxtobsexamen"+cuentaExamenes,"4.7",atr,"");
			var tipoDeServicio = nombreCentroCostos;

			var atr = new Array();
			atr['size'] = "10";
			atr['maxLength'] = "7";
			atr['class'] = "campo2";
			atr['onClick'] = "fecha_orden('"+cuentaExamenes+"');";
			atr['readonly'] = "readonly";
			atr['name'] = "wfsol"+cuentaExamenes;
			atr['cod_examen'] = codExamen;

			var fsol = crearCampo("1","wfsol"+cuentaExamenes,"4.8", atr, fechaCompuesta);

			//Anexo de hora solicitado examen
			// var hsol = document.createElement("input");
			// hsol.setAttribute('id','whsol'+cuentaExamenes);
			// hsol.setAttribute('name','whsol'+cuentaExamenes);
			// hsol.setAttribute('type','text');
			// hsol.setAttribute('size','10');
			// hsol.setAttribute('value',horas+":"+minutos+":"+segundos);
			// hsol.setAttribute('cod_examen',codExamen); //Se agrega el atributo con el codigo del examne , para que valide si registran el mismo codido para el mismo dia.
			// hsol.setAttribute('readOnly','readonly');
			// hsol.className = 'campo2 calendariohora';

			//Hora de examen
			// var atr = new Array();
			// atr['size'] = "10";
			// atr['class'] = "campo2";
			// atr['readonly'] = "readonly";
			// atr['name'] = "whsol"+cuentaExamenes;

			// var hsol = crearCampo("1","whsol"+cuentaExamenes,"4.11", atr, horas+":"+minutos+":"+segundos);


			var atr = new Array();
			//atr['onClick'] = "calendario_hora('"+cuentaExamenes+"');";

			var btnHsol = crearCampo("3","btnHoraSol"+cuentaExamenes,"4.8",atr,"*");

			//Texto
			var examen = document.createElement("input");
			examen.setAttribute('id','wnmexamen'+cuentaExamenes);
			examen.setAttribute('name','wnmexamen'+cuentaExamenes);
			examen.setAttribute('type','text');
			examen.setAttribute('value',reemplazarTodo(nombreExamen,"_"," "));
			examen.setAttribute('size','70');
			examen.setAttribute('readOnly','readonly');
			examen.className = 'campo2';

			examen.value = aux.innerHTML;

			//Ocultos
			var oculto = document.createElement("input");
			oculto.setAttribute('type','hidden');
			oculto.setAttribute('name','wmodificado4'+cuentaExamenes);
			oculto.setAttribute('id','wmodificado4'+cuentaExamenes);
			oculto.setAttribute('value','S');

			//Codigo del centro de costos
			var oculto1 = document.createElement("input");
			oculto1.setAttribute('type','hidden');
			oculto1.setAttribute('name','hexcco'+cuentaExamenes);
			oculto1.setAttribute('id','hexcco'+cuentaExamenes);
			oculto1.setAttribute('value',centroCostos);

			//Codigo del examen
			var oculto2 = document.createElement("input");
			oculto2.setAttribute('type','hidden');
			oculto2.setAttribute('name','hexcod'+cuentaExamenes);
			oculto2.setAttribute('id','hexcod'+cuentaExamenes);
			oculto2.setAttribute('value',codExamen);

			//Consecutivo de la orden
			var oculto3 = document.createElement("input");
			oculto3.setAttribute('type','hidden');
			oculto3.setAttribute('name','hexcons'+cuentaExamenes);
			oculto3.setAttribute('id','hexcons'+cuentaExamenes);
			oculto3.setAttribute('value',consecutivoOrden);

			//Consecutivo del item
			var oculto5 = document.createElement("input");
			oculto5.setAttribute('type','hidden');
			oculto5.setAttribute('name','hexnroitem'+cuentaExamenes);
			oculto5.setAttribute('id','hexnroitem'+cuentaExamenes);
			oculto5.setAttribute('value',0);

			//Ocultos
			var oculto4 = document.createElement("input");
			oculto4.setAttribute('type','hidden');
			oculto4.setAttribute('name','wmodificado4'+cuentaExamenes);
			oculto4.setAttribute('id','wmodificado4'+cuentaExamenes);
			oculto4.setAttribute('value','S');

			//Requiere Justificacion
			var oculto6 = document.createElement("input");
			oculto6.setAttribute('type','hidden');
			oculto6.setAttribute('name','hiReqJus'+cuentaExamenes);
			oculto6.setAttribute('id','hiReqJus'+cuentaExamenes);
			oculto6.setAttribute('value',requiereJustificacion);

			//Requiere Justificacion
			var oculto7 = document.createElement("input");
			oculto7.setAttribute('type','hidden');
			oculto7.setAttribute('name','hiReqJus'+cuentaExamenes);
			oculto7.setAttribute('id','hiReqJus'+cuentaExamenes);
			oculto7.setAttribute('value',requiereJustificacion);

			// Hidden que define si es de alta
			var altaexamen = document.createElement("input");
			altaexamen.setAttribute('id','wexamenalta'+cuentaExamenes);
			altaexamen.setAttribute('name','wexamenalta'+cuentaExamenes);
			altaexamen.setAttribute('type','hidden');
			altaexamen.setAttribute('value','off');


			//Firma
			var oculto8 = document.createElement("input");
			oculto8.setAttribute('type','hidden');
			oculto8.setAttribute('name','hiFormHce'+cuentaExamenes);
			oculto8.setAttribute('id','hiFormHce'+cuentaExamenes);
			oculto8.setAttribute('value','');
			
			//Datos adicionales
			var oculto9 = document.createElement("input");
			oculto9.setAttribute('type','hidden');
			oculto9.setAttribute('name','hiDatosAdicionales'+cuentaExamenes);
			oculto9.setAttribute('id','hiDatosAdicionales'+cuentaExamenes);
			oculto9.setAttribute('value', JSON.stringify( datosAdicionales ) );
			
			
			//Datos adicionales
			var oculto10 = document.createElement("input");
			oculto10.setAttribute('type','hidden');
			oculto10.setAttribute('name','hiOrdenAnexa'+cuentaExamenes);
			oculto10.setAttribute('id','hiOrdenAnexa'+cuentaExamenes);
			oculto10.setAttribute('value', OrdenAnexa );

			if( OrdenAnexa != "" ){
				
				$( ".anexar-orden" ).each(function(){
					$( "span", this ).html("Anexar");
				});
				
				$("#wselTipoServicio" ).val('');
		
				$("#wselTipoServicio" ).attr({disabled:false});
				$("#wprotocolo_ayd" ).attr({disabled:false});
				
				OrdenAnexa = "";
			}
			
			//Datos adicionales
			var oculto11 = document.createElement("input");
			oculto11.setAttribute('type','hidden');
			oculto11.setAttribute('name','hiEsOfertado'+cuentaExamenes);
			oculto11.setAttribute('id','hiEsOfertado'+cuentaExamenes);
			oculto11.setAttribute('value', esOfertado ? '1' : '0' );


			//Campo de estado, por defecto viene pendiente
			var textoEstado = document.createTextNode("Pendiente");

			//Texto vacio en la bitacora de gestion.
			var textoBitacora = document.createTextNode("");
			/*******************************************************************************
			ANEXAR CONTENIDO A LAS COLUMNAS
			********************************************************************************/
			columna7.innerHTML = iHTMLOrden;
		//	columna1.innerHTML += link2;
		//	columna1.align = "center";
			columna8.innerHTML += link2;
			columna8.innerHTML += linkImp;
			columna8.innerHTML += chkImp;
			columna8.align = "center";
			columna5.appendChild(examen);
			columna5.appendChild(oculto);
			columna5.appendChild(oculto1);
			columna5.appendChild(oculto2);
			columna5.appendChild(oculto3);
			columna5.appendChild(oculto5);
			columna5.appendChild(oculto6);
			columna5.appendChild(oculto7);
			columna5.appendChild(oculto8);
			columna5.appendChild(oculto9);
			columna5.appendChild(oculto10);
			columna5.appendChild(oculto11);
			columna5.appendChild(altaexamen);
			columna4.innerHTML += tipoDeServicio;
			columna6.innerHTML += justificacion;
			columna10.appendChild(textoBitacora);
			columna2.innerHTML += fsol;
			// columna3.align = "center";
			// columna3.innerHTML += hsol;
			// columna3.innerHTML += "";
			columna9.appendChild(textoEstado);

			/*******************************************************************************
			ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
			********************************************************************************/
			if( crearOrden ){
				columna7.align = 'center';
				columna7.rowSpan = 1;
				columna7.id = "del"+centroCostos+consecutivoOrden;
				$( columna7 ).attr( "newExam", "on" );		//Mayo 19 de 2015
				fila.appendChild(columna7);
			}

			fila.appendChild(columna8);
			//fila.appendChild(columna1);
			fila.appendChild(columna2);
			//fila.appendChild(columna3);
			fila.appendChild(columna4);
			fila.appendChild(columna5);
			fila.appendChild(columna6);
			fila.appendChild(columna9);
			fila.appendChild(columna10);

			var encabezadoTabla = document.getElementById( "encabezadoExamenes" );

			//ANEXAR LA FILA A LA TABLA
			cntDetalle.insertBefore( fila,cntDetalle.firstChild );

			$( "#wfsol"+cuentaExamenes ).datepicker({ minDate: -0 });
			$( "#whsol"+cuentaExamenes ).timepicker({ minDate: -0 });


			if( !crearOrden ){
				/*******************************************************************************************
				 * Si no se va a crear la fila correspondiente a orden se debe buscar la fila que tenga la
				 * cabecera de la orden y aumentar el rowspan de la primera fila
				 *******************************************************************************************/

				//fila del encabezado de la orden
				var encabezadoOrden = document.getElementById( "del"+centroCostos+consecutivoOrden );

				if( encabezadoOrden.rowSpan == 0 ){
					encabezadoOrden.rowSpan = 1;
				}

				fila.insertBefore( encabezadoOrden, fila.firstChild );

				encabezadoOrden.rowSpan = encabezadoOrden.rowSpan+1;

				// Muestra el numero de orden si se ocultó al agregar procedimientos agrupados
				existeEnAgrupados = false;
				for( var x in procAgrupados ){
					if( procAgrupados[x].estado != 'grabado' ){

						for (i = 0; i < procAgrupados[x].length; i++) {

							consecutivo = procAgrupados[x][i].consecutivo;

							if(consecutivo == cuentaExamenes)
							{
								existeEnAgrupados = true;
								break;
							}
						}
					}
				}

				if(existeEnAgrupados == false)
				{
					$("#del"+centroCostos+consecutivoOrden).show();
				}


			}
			else{
				//fila del encabezado de la orden
				var encabezadoOrden = document.getElementById( "del"+centroCostos+consecutivoOrden );
				fila.insertBefore( encabezadoOrden, fila.firstChild );
			}

			cuentaExamenes++;

			examen.style.width = "100%";

			justificacionUltimoExamenAgregado = columna5.firstChild;

			var hermanoActual = cntDetalle.parentNode.parentNode.parentNode;
			if(isset(hermanoActual) && hermanoActual)
				var hermanoAnterior = hermanoActual.previousSibling;
			if(isset(hermanoAnterior) && hermanoAnterior)
				var hermanoMayor = hermanoAnterior.previousSibling;

			var contenedorOrdenesPorCco = document.getElementById( "cntOtrosExamenes" );

			try{

				// $( "#examPendientes" )[0].insertBefore( $( cntDetalle ), $( "#examPendientes" )[0].childNodes[2]  );
				$( "#examPendientes" )[0].insertBefore( cntDetalle, $( "#examPendientes" )[0].childNodes[2]  )

				contenedorOrdenesPorCco.insertBefore( hermanoActual, contenedorOrdenesPorCco.firstChild );
				contenedorOrdenesPorCco.insertBefore( hermanoAnterior, contenedorOrdenesPorCco.firstChild );
				contenedorOrdenesPorCco.insertBefore( hermanoMayor, contenedorOrdenesPorCco.firstChild );

			//	columna6.firstChild.focus();

				//debugger;
				for( i = 0; i < contenedorOrdenesPorCco.childNodes.length; i++ ){

					if( contenedorOrdenesPorCco.childNodes[i].tagName == "DIV" && contenedorOrdenesPorCco.childNodes[i].id != '' && contenedorOrdenesPorCco.childNodes[i].id != centroCostos ){
						contenedorOrdenesPorCco.childNodes[i].style.display = 'none';
					}

				}


			}
			catch(e){
			}
				
			// var adicionMultiple;

			//Paciente No pos
			if( $( "#pacEPS" ).val() == 'on' && noPos.trim() == 'on'){
				// console.log( "validando ctc.............."+noPos );
				var entidad_responsable = $("#entidad_responsable").val(); //Entidad responsable del paciente.
				var datos_entidad_responsable = entidad_responsable.split("-"); //Nit y digito de verificacion.
				var array_entidadesCTC = Array();
				var nit_entidad_resp = datos_entidad_responsable[0]; //Nit sin el digito de verificacion.

				var entidadesConfirmanCTC = $("#entidades_confirmanCTC").val(); //Nit de entidades que confirman CTC.
				var array_entidadesCTC = entidadesConfirmanCTC.split(","); //Array de entidades que confirman CTC.

				var validar_entidad = jQuery.inArray(nit_entidad_resp, array_entidadesCTC); //Verifica si el array de entidades que confirman CTC esta la entidad responsable del paciente.

				//Si la respuesta es 0(cero) entonces el nit si se encuentra en las entidades que confirman CTC, entonces muestra un alert para llenarlo o no.
				if(validar_entidad != -1){

					//Pinta el consecutivo  de la orden.
					oculto6.value = 'off';
					grabarExamen( cuentaExamenes-1, false );
					oculto6.value = requiereJustificacion;

					// var confirmaCTCproc = confirm("¿Desea realizar el CTC del procedimiento para el paciente?");

					// if(confirmaCTCproc){
						// if(!adicionMultiple)
						// {
							// mostrarCtcProcedimientos( codExamen, cuentaExamenes-1 ); //Noviembre 08 de 2012
						// }
						// else
						// {
							// // strPendientesCTC += 'procedimiento|'+codExamen+'|'+cuentaExamenes-1+'\r\n';
							// strPendientesCTC += 'procedimiento|'+codExamen+'|'+(cuentaExamenes-1)+'\r\n';
						// }
					// }
					// else{

						// arCTCprocedimientos[codExamen] = "procsinctc";
						// if( funcInternas ){
							// funcInternas();
						// }
						// return false;
					// }


					// nombExamen
					jConfirm( "¿Desea realizar el CTC del procedimiento "+""+" para el paciente?", "Alerta", function( resp ){
						if( resp )
						{
							if(!adicionMultiple)
							{
								mostrarCtcProcedimientos( codExamen, cuentaExamenes-1 ); //Noviembre 08 de 2012
							}
							else
							{
								// strPendientesCTC += 'procedimiento|'+codExamen+'|'+cuentaExamenes-1+'\r\n';
								strPendientesCTC += 'procedimiento|'+codExamen+'|'+(cuentaExamenes-1)+'\r\n';
							}
						}
						else
						{
							arCTCprocedimientos[codExamen] = "procsinctc";
						}

						if( funcInternas ){
							funcInternas();
						}
						return false;
					});

					return false;
				}
				else{
					
					// alert("El procedimiento "+$("#wnmexamen"+(cuentaExamenes-1)).val()+" es NO POS, debe llenar el CTC");
					
					if($( "#esContributivo" ).val())
					{
						alert("El procedimiento "+$("#wnmexamen"+(cuentaExamenes-1)).val()+" es NO POS, al firmar la orden debe llenar el MIPRES en la plataforma del Ministerio de Salud");
					}
					else
					{
						alert("El procedimiento "+$("#wnmexamen"+(cuentaExamenes-1)).val()+" es NO POS, debe llenar el CTC");
					}
					
					//Pinta el consecutivo  de la orden.
					oculto6.value = 'off';
					grabarExamen( cuentaExamenes-1, false );
					oculto6.value = requiereJustificacion;

					if(!adicionMultiple)
					{
						// mostrarCtcProcedimientos( codExamen, cuentaExamenes-1 ); //Noviembre 08 de 2012
						
						if($( "#esContributivo" ).val())
						{
							// agregar procedimiento a la cadena para mostrar el CTC al grabar y crear las notas medicas
							// cadenaCTCcontributivo += "procedimiento|"+codExamen+"|"+$("#wnmexamen"+(cuentaExamenes-1)).val()+"|"+(cuentaExamenes-1)+"***";
							cadenaCTCcontributivo += "procedimiento|"+codExamen+"|"+$.trim($("#wnmexamen"+(cuentaExamenes-1)).val())+"|"+(cuentaExamenes-1)+"|"+$("#hexcco"+(cuentaExamenes-1)).val()+"|"+$("#hexcons"+(cuentaExamenes-1)).val()+"|"+$("#hexnroitem"+(cuentaExamenes-1)).val()+"***";
						}
						else
						{
							mostrarCtcProcedimientos( codExamen, cuentaExamenes-1 ); //Noviembre 08 de 2012
						}
					}
					else
					{
						// // console.log( "codExamen.............." +codExamen);
						// // console.log( "cuentaExamenes.............." +(cuentaExamenes-1));
						// strPendientesCTC += 'procedimiento|'+codExamen+'|'+(cuentaExamenes-1)+'\r\n';
						// // console.log( "La esta llenando.............." +strPendientesCTC);
						
						
						if($( "#esContributivo" ).val())
						{
							// agregar procedimiento a la cadena para mostrar el CTC al grabar y crear las notas medicas
							// cadenaCTCcontributivo += "procedimiento|"+codExamen+"|"+$("#wnmexamen"+(cuentaExamenes-1)).val()+"|"+(cuentaExamenes-1)+"***";
							cadenaCTCcontributivo += "procedimiento|"+codExamen+"|"+$.trim($("#wnmexamen"+(cuentaExamenes-1)).val())+"|"+(cuentaExamenes-1)+"|"+$("#hexcco"+(cuentaExamenes-1)).val()+"|"+$("#hexcons"+(cuentaExamenes-1)).val()+"|"+$("#hexnroitem"+(cuentaExamenes-1)).val()+"***";
						}
						else
						{
							strPendientesCTC += 'procedimiento|'+codExamen+'|'+(cuentaExamenes-1)+'\r\n';
						}
						
					}
				}
			}

			//Pinta el consecutivo  de la orden.
			oculto6.value = 'off';
			grabarExamen( cuentaExamenes-1, false );
			oculto6.value = requiereJustificacion;



			if( funcInternas ){
				funcInternas();
			}
		}
}

/*****************************************************************************************************************************
 * Agrega dinámicamente una nueva fila para un examen de laboratorio nuevo
 ******************************************************************************************************************************/
function agregarExamenAlta(centroCostos,codExamen,nombreExamen,tipoEstudio,anatomia,codigoCups,consecutivoOrden,nombreCentroCostos, nroItem, crearOrden, requiereJustificacion, noPos, aydJustificacion ){

	if(!aydJustificacion)
		aydJustificacion = "";

	var aux = document.createElement("div");
	aux.innerHTML = nombreExamen;

	//Limpia el campo de autocompletar.
	$("#wnomprocimp").val("");

	//HTML de la orden
	var iHTMLOrden = "";//"<tr id='del"+centroCostos+""+consecutivoOrden+"'>";

	// var atr = new Array();
	// atr['onClick'] = "cancelarOrden('"+centroCostos+"','"+consecutivoOrden+"');";
	// iHTMLOrden += crearCampo("4","","4.3",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");

	// iHTMLOrden += "<a href='#null' onclick=intercalarElemento(\""+centroCostos+""+consecutivoOrden+"\"); style='font-size:10pt'>";
	// iHTMLOrden += "<b>&nbsp;&nbsp;&nbsp;<u>Orden Nro. "+consecutivoOrden+"</u></b></a>&nbsp;&nbsp;&nbsp;";

	iHTMLOrden += "<div id=\""+centroCostos+""+consecutivoOrden+"imp\" class='fila2'>";
	//iHTMLOrden += "<div style='display:none'><br>Observaciones de la orden: <br>";

	var atr = new Array();
	atr['rows'] = "2";
	atr['cols'] = "60";
	atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";

	//iHTMLOrden += crearCampo("2","wtxtobsexamen"+centroCostos+""+consecutivoOrden,"4.4",atr,".");

//	iHTMLOrden += "</div></td></tr>";

	var idContenedor = centroCostos+""+consecutivoOrden;
	var idContenedor = centroCostos;
	var puedeAgregar = true;
	var esHospitalario = false;

	if(puedeAgregar){

		var cntDetalle = document.getElementById("detExamenes"+idContenedor+"Imp");

		//Fecha actual automatica
		var fechaActual = new Date();

		diaActual = fechaActual.getDate();
		mesActual = fechaActual.getMonth() + 1;
		anioActual= fechaActual.getFullYear();

		if(mesActual && mesActual.toString().length == 1){
			mesActual = "0" + mesActual.toString();
		}

		if(diaActual && diaActual.toString().length == 1){
			diaActual = "0" + diaActual.toString();
		}

		fechaCompuesta = anioActual + "-" + mesActual + "-" + diaActual;
		var fechaActualOrden = anioActual+"-"+mesActual+"-"+diaActual;

		/*******************************************************************************************
		 * verificando si hay un examen igual para el mismo día
		 *******************************************************************************************/
		/*******************************************************************************************
		 * verificando si hay un examen igual para el mismo día
		 *******************************************************************************************/
		 permitir_registro = false;

		//Se verifica dentro de la tabla de examPendientes cuantos examenes existen con el mismo codigo. Jonatan Lopez 7 Mayo 2014
		$('table[id=examPendientesImp]').find("[cod_examen^="+codExamen+"]").each(function(){

			//Si encuentra almenos un examen igual evalua la fecha de realizacion, si el examen ya esta para la fecha hara que la variable permitir_registro sea igual a true
			//al ser true mostrara el mensaje de que el examen ya existe para el mismo dia.
			if ($(this).val() == fechaActualOrden){

			permitir_registro = true;

			}

		});

		var campoFecha = $('#wfechagrabacion').val();

		//Se valida si el examen existe para el mismo dia con la variable permitir_registro == true, si el false permite hacer el registro. Jonatan Lopez 7 Mayo 2014
		if( permitir_registro == true){

			if( !confirm( "El examen ya existe para el mismo día. ¿Desea agregarlo?" ) )
				return false;

		}

		/*******************************************************************************************/

		//Fila nueva
		var fila = document.createElement("tr");
		fila.setAttribute('id','trExImp'+cuentaExamenes);
		//fila.setAttribute('class','encabezadoTabla');

		/*
		// columnas anteriores
		var columna7 = document.createElement("td");		//Nro de orden
		var columna1 = document.createElement("td");		//Acciones
		var columna2 = document.createElement("td");		//Examenes
		var columna3 = document.createElement("td");		//Observaciones
		var columna4 = document.createElement("td");		//Estado
		var columna5 = document.createElement("td");		//Fecha solicitado examen
		var columna6 = document.createElement("td");		//Justificacion
		*/

		//Columnas nuevas
		var columna7 = document.createElement("td");		//Nro de orden
		var columna8 = document.createElement("td");		//Imprimir
		//var columna1 = document.createElement("td");		//Acciones
		var columna2 = document.createElement("td");		//Fecha solicitado examen
		var columna3 = document.createElement("td");		//Tipo de servicio
		var columna4 = document.createElement("td");		//Procedimiento
		var columna5 = document.createElement("td");		//Justificacion
		//var columna6 = document.createElement("td");		//Estado

		columna7.setAttribute('align','center');
		columna8.setAttribute('align','center');
		columna2.setAttribute('align','center');
		columna2.setAttribute('nowrap','nowrap');
		columna3.setAttribute('align','center');

		/******************************************************************************
		CONTENIDO DE LAS COLUMNAS
		*******************************************************************************/
		//Link de borrar
		var atr = new Array();
		atr['onClick'] = "quitarExamen('"+cuentaExamenes+"','Imp','on');";

		var link2 = crearCampo("4","","4.5",atr,"<img src='../../images/medical/root/borrar.png' border='0' width='17' height='17'>");

		//Si la ayuda o procedimiento es hospitalario llega tambien por defecto con estado pendiente
		if(centroCostos == "H"){
			esHospitalario = true;
		}

		//Examen y estado del examen

		var atr = new Array();
		atr['rows'] = "2";
		atr['cols'] = "40";
		atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";

		if( requiereJustificacion == "on" ){
			atr['class'] = "fondoamarillo";
		}

		var justificacion = crearCampo("2","wtxtjustexamenimp"+cuentaExamenes,"4.6",atr,aydJustificacion);

		var justificacionaux = document.createElement("input");
		justificacionaux.setAttribute('id','wtxtjustexamen'+cuentaExamenes);
		justificacionaux.setAttribute('name','wtxtjustexamen'+cuentaExamenes);
		justificacionaux.setAttribute('type','hidden');
		justificacionaux.setAttribute('value',aydJustificacion);



		var atr = new Array();
		atr['rows'] = "2";
		atr['cols'] = "40";
		atr['onKeyPress'] = "return validarEntradaAlfabetica(event);";
		atr['readonly'] = "readonly";

		//var observaciones = crearCampo("2","wtxtobsexamen"+cuentaExamenes,"4.7",atr,"");
		var tipoDeServicio = nombreCentroCostos;

		//Anexo de fecha solicitado examen
		var fsol = document.createElement("input");
		fsol.setAttribute('id','wfsolimp'+cuentaExamenes);
		fsol.setAttribute('name','wfsolimp'+cuentaExamenes);
		fsol.setAttribute('type','text');
		fsol.setAttribute('size','10');
		fsol.setAttribute('value',fechaCompuesta);
		fsol.setAttribute('cod_examen',codExamen);
		fsol.setAttribute('readOnly','readonly');
		fsol.className = 'campo2';

		var fsolaux = document.createElement("input");
		fsolaux.setAttribute('id','wfsol'+cuentaExamenes);
		fsolaux.setAttribute('name','wfsol'+cuentaExamenes);
		fsolaux.setAttribute('type','hidden');
		fsolaux.setAttribute('value',fechaCompuesta);

		var atr = new Array();
		atr['onClick'] = "calendario3imp('"+cuentaExamenes+"');";

		var btnFsol = crearCampo("3","btnFechaSolImp"+cuentaExamenes,"4.8",atr,"*");

		//Imprimir
		var atrImp = new Array();

		atrImp['onClick'] = "marcarImpresionExamen(this,'"+centroCostos+"','"+consecutivoOrden+"','"+codExamen+"','"+fechaCompuesta+"','"+nroItem+"');";
		atrImp['checked'] = "checked";

		//Checkbox de impresion
		var chkimp = document.createElement("input");
		chkimp.setAttribute('id','wchkimpexamen'+cuentaExamenes);
		chkimp.setAttribute('name','wchkimpexamen'+cuentaExamenes);
		chkimp.setAttribute('type','checkbox');

		var chkImp = crearCampo("5","wchkimpexamen"+cuentaExamenes,"5.4",atrImp,"");
		var linkImp = crearCampo("4","","4.5","","<img src='../../images/medical/hce/icono_imprimir.png' border='0' width='17' height='17'>");


		// Hidden que define si es de alta
		var altaexamen = document.createElement("input");
		altaexamen.setAttribute('id','wexamenalta'+cuentaExamenes);
		altaexamen.setAttribute('name','wexamenalta'+cuentaExamenes);
		altaexamen.setAttribute('type','hidden');
		altaexamen.setAttribute('value','on');

		//Texto
		var examen = document.createElement("input");
		examen.setAttribute('id','wnmexamenimp'+cuentaExamenes);
		examen.setAttribute('name','wnmexamenimp'+cuentaExamenes);
		examen.setAttribute('type','text');
		examen.setAttribute('value',reemplazarTodo(nombreExamen,"_"," "));
		examen.setAttribute('size','70');
		examen.setAttribute('readOnly','readonly');
		examen.className = 'campo2';

		examen.value = aux.innerHTML;

		var examenaux = document.createElement("input");
		examenaux.setAttribute('id','wnmexamen'+cuentaExamenes);
		examenaux.setAttribute('name','wnmexamen'+cuentaExamenes);
		examenaux.setAttribute('type','hidden');
		examenaux.setAttribute('value',reemplazarTodo(nombreExamen,"_"," "));


		//Ocultos
		var oculto = document.createElement("input");
		oculto.setAttribute('type','hidden');
		oculto.setAttribute('name','wmodificado4'+cuentaExamenes);
		oculto.setAttribute('id','wmodificado4'+cuentaExamenes);
		oculto.setAttribute('value','S');

		//Codigo del centro de costos
		var oculto1 = document.createElement("input");
		oculto1.setAttribute('type','hidden');
		oculto1.setAttribute('name','hexccoimp'+cuentaExamenes);
		oculto1.setAttribute('id','hexccoimp'+cuentaExamenes);
		oculto1.setAttribute('value',centroCostos);

		//Codigo del examen
		var oculto2 = document.createElement("input");
		oculto2.setAttribute('type','hidden');
		oculto2.setAttribute('name','hexcodimp'+cuentaExamenes);
		oculto2.setAttribute('id','hexcodimp'+cuentaExamenes);
		oculto2.setAttribute('value',codExamen);

		//Consecutivo de la orden
		var oculto3 = document.createElement("input");
		oculto3.setAttribute('type','hidden');
		oculto3.setAttribute('name','hexconsimp'+cuentaExamenes);
		oculto3.setAttribute('id','hexconsimp'+cuentaExamenes);
		oculto3.setAttribute('value',consecutivoOrden);

		//Consecutivo del item
		var oculto5 = document.createElement("input");
		oculto5.setAttribute('type','hidden');
		oculto5.setAttribute('name','hexnroitemimp'+cuentaExamenes);
		oculto5.setAttribute('id','hexnroitemimp'+cuentaExamenes);
		oculto5.setAttribute('value',0);

		//Ocultos
		var oculto4 = document.createElement("input");
		oculto4.setAttribute('type','hidden');
		oculto4.setAttribute('name','wmodificado4'+cuentaExamenes);
		oculto4.setAttribute('id','wmodificado4'+cuentaExamenes);
		oculto4.setAttribute('value','S');

		//Requiere Justificacion
		var oculto6 = document.createElement("input");
		oculto6.setAttribute('type','hidden');
		oculto6.setAttribute('name','hiReqJusimp'+cuentaExamenes);
		oculto6.setAttribute('id','hiReqJusimp'+cuentaExamenes);
		oculto6.setAttribute('value',requiereJustificacion);

		var oculto7 = document.createElement("input");
		oculto7.setAttribute('id','wtxtjustexamenimp'+cuentaExamenes);
		oculto7.setAttribute('name','wtxtjustexamenimp'+cuentaExamenes);
		oculto7.setAttribute('type','hidden');
		oculto7.setAttribute('value',aydJustificacion);

		//Firma
		var oculto8 = document.createElement("input");
		oculto8.setAttribute('type','hidden');
		oculto8.setAttribute('name','hiFormHceimp'+cuentaExamenes);
		oculto8.setAttribute('id','hiFormHceimp'+cuentaExamenes);
		oculto8.setAttribute('value','');


		//Campo de estado, por defecto viene pendiente
		var textoEstado = document.createTextNode("Pendiente");
		/*******************************************************************************
		ANEXAR CONTENIDO A LAS COLUMNAS
		********************************************************************************/
	//	columna1.appendChild(link1);
	//	columna1.appendChild(link2);
		columna7.innerHTML = link2;
		columna8.innerHTML = linkImp+chkImp;
		columna7.appendChild(chkimp);
		columna7.appendChild(altaexamen);
	//	columna1.appendChild(chkimp);
	//	columna1.align = "center";
	//	columna2.appendChild(tipoExamen);
		columna4.appendChild(examen);
		columna4.appendChild(examenaux);
		columna4.appendChild(oculto);
		columna4.appendChild(oculto1);
		columna4.appendChild(oculto2);
		columna4.appendChild(oculto3);
		columna4.appendChild(oculto5);
		columna4.appendChild(oculto6);
		columna4.appendChild(oculto8);
	//	columna3.appendChild(observaciones);
		columna3.innerHTML += tipoDeServicio;
	//	columna4.appendChild(estadosExamen);
	//	columna6.appendChild(textoEstado);
		columna2.appendChild(fsol);
		columna2.appendChild(fsolaux);
	//	columna5.appendChild(btnFsol);
		columna2.innerHTML += btnFsol;
	//	columna6.appendChild(justificacion);
		columna5.innerHTML += justificacion;
		columna5.appendChild(oculto7);

		//Oculto campo de resultado
		//columna3.style.display='none';

		/*******************************************************************************
		ANEXAR LAS COLUMNAS NUEVAS A LA FILA NUEVA
		********************************************************************************/
		/*
		if( crearOrden ){
			columna7.align = 'center';
			columna7.rowSpan = 1;
			columna7.id = "delImp"+centroCostos+consecutivoOrden;
			fila.appendChild(columna7);
	//		fila.id = "del"+centroCostos+consecutivoOrden;
		}
		*/

		fila.appendChild(columna7);
		fila.appendChild(columna8);
		fila.appendChild(columna2);
		fila.appendChild(columna3);
		fila.appendChild(columna4);
		fila.appendChild(columna5);
		//fila.appendChild(columna6);

		//ANEXAR LA FILA A LA TABLA
		cntDetalle.insertBefore( fila,cntDetalle.firstChild );
	//	cntDetalle.appendChild(fila);

		// if( !crearOrden ){
			// /*******************************************************************************************
			 // * Si no se va a crear la fila correspondiente a orden se debe buscar la fila que tenga la
			 // * cabecera de la orden y aumentar el rowspan de la primera fila
			 // *******************************************************************************************/

			// //fila del encabezado de la orden
			// var encabezadoOrden = document.getElementById( "delImp"+centroCostos+consecutivoOrden );


			// if( encabezadoOrden.rowSpan == 0 ){
				// encabezadoOrden.rowSpan = 1;
			// }

			// fila.insertBefore( encabezadoOrden, fila.firstChild );

			// encabezadoOrden.rowSpan = encabezadoOrden.rowSpan+1;
		// }
		// else{
			// //fila del encabezado de la orden
			// var encabezadoOrden = document.getElementById( "delImp"+centroCostos+consecutivoOrden );
			// fila.insertBefore( encabezadoOrden, fila.firstChild );
		// }

		document.getElementById('wchkimpexamen'+cuentaExamenes).checked = true;
		document.getElementById('wchkimpexamen'+cuentaExamenes).style.display="none";


		marcarImpresionExamen(document.getElementById('wchkimpexamen'+cuentaExamenes),centroCostos,consecutivoOrden,codExamen,fechaCompuesta,'');


		cuentaExamenes++;

		examen.style.width = "100%";

		justificacionUltimoExamenAgregado = columna5.firstChild;

		var hermanoActual = cntDetalle.parentNode.parentNode.parentNode;
		if(isset(hermanoActual) && hermanoActual)
			var hermanoAnterior = hermanoActual.previousSibling;
		if(isset(hermanoAnterior) && hermanoAnterior)
			var hermanoMayor = hermanoAnterior.previousSibling;

		/*
		var contenedorOrdenesPorCco = document.getElementById( "cntOtrosExamenes" );

		try{
			contenedorOrdenesPorCco.insertBefore( hermanoActual, contenedorOrdenesPorCco.firstChild );
			contenedorOrdenesPorCco.insertBefore( hermanoAnterior, contenedorOrdenesPorCco.firstChild );
			contenedorOrdenesPorCco.insertBefore( hermanoMayor, contenedorOrdenesPorCco.firstChild );

		//	columna6.firstChild.focus();

			//debugger;
			for( i = 0; i < contenedorOrdenesPorCco.childNodes.length; i++ ){

				if( contenedorOrdenesPorCco.childNodes[i].tagName == "DIV" && contenedorOrdenesPorCco.childNodes[i].id != '' && contenedorOrdenesPorCco.childNodes[i].id != centroCostos ){
					contenedorOrdenesPorCco.childNodes[i].style.display = 'none';
				}

			}
		}
		catch(e){
		}
		*/

		//Paciente No pos
		if( $( "#pacEPS" ).val() == 'on' ){

			// var entidad_responsable = $("#entidad_responsable").val(); //Entidad responsable del paciente.
			// var datos_entidad_responsable = entidad_responsable.split("-"); //Nit y digito de verificacion.
			var array_entidadesCTC = Array();
			// var nit_entidad_resp = datos_entidad_responsable[0]; //Nit sin el digito de verificacion.
			
			var nit_entidad_resp = $("#entidad_responsable").val(); //Entidad responsable del paciente.

			var entidadesConfirmanCTC = $("#entidades_confirmanCTC").val(); //Nit de entidades que confirman CTC.
			var array_entidadesCTC = entidadesConfirmanCTC.split(","); //Array de entidades que confirman CTC.

			var validar_entidad = jQuery.inArray(nit_entidad_resp, array_entidadesCTC); //Verifica si el array de entidades que confirman CTC esta la entidad responsable del paciente.

			//Si la respuesta es 0(cero) entonces el nit si s encuentra en las entidades que confirman CTC, entonces muestra un alert para llenarlo o no.
			if(validar_entidad != -1){

				var confirmaCTC = confirm("¿Desea realizar el CTC del procedimiento para el paciente?");

				if(confirmaCTC){
					if(!adicionMultiple)
					{
						mostrarCtcProcedimientos( codExamen, cuentaExamenes-1 ); //Noviembre 08 de 2012
					}
					else
					{
						strPendientesCTC += 'procedimiento|'+codExamen+'|'+cuentaExamenes-1+'\r\n';
					}
				}else{

					return false;
				}
			}
			else{
				mostrarCtcProcedimientos( codExamen, cuentaExamenes-1 ); //Noviembre 08 de 2012
			}
		}

		oculto6.value = 'off';
		grabarExamen( cuentaExamenes-1, false );
		oculto6.value = requiereJustificacion;

	} else {
		alert('Por favor ingrese la información del examen antes de agregar uno nuevo.');
	}
}


// Filtra en la tabla de ordenes pendientes segun los parametros de busqueda que se hayan establecido
function consultarOrdenesPendientes(wemp_pmla,wempresa,wbasedato,whis,wing)
{
	//debugger;
	//var parametros = "consultaAjaxKardex=33&wemp_pmla="+wemp_pmla+"&wempresa="+wempresa+"&wbasedato="+wbasedato					+"&whis="+whis+"&wing="+wing+"&wfecini="+document.forms.forma.wfecini.value+"&wfecfin="+document.forms.forma.wfecfin.value+"&wtiposerv="+document.forms.forma.wtiposerv.value+"&wprocedimiento="+document.forms.forma.wprocedimiento.value+"&westadodet="+westadodet;

		var tabla = document.getElementById( "examPendientes" );
		//var totalExamenes = tabla.rows[1].cells[0].rowSpan;
		var totalExamenes = tabla.rows.length - 3;	// se resta 3 porque se omiten las filas de busqueda rápida y encabezado
		var countRowSpan = 1;

		// Datos a buscar
		fechaInicioConsulta = document.forms.forma.wfecini2.value;
		fechaFinConsulta = document.forms.forma.wfecfin2.value;
		procedimientocioConsulta = document.forms.forma.wprocedimiento2.value;
		tipoServicioConsulta = document.forms.forma.wtiposerv2.value;
		justificacionConsulta = document.forms.forma.wjustificacion2.value;
		estadoConsulta = document.forms.forma.westadodet2.value;

		for( var i = 0; i < totalExamenes; i++ ){

			inicioFila = i + 3;
			// Establece si se encontro alguna de las cadeas buscadas en la fila actual
			findFecha = false;
			findServicio = false;
			findProcedimiento = false;
			findJustificacion = false;
			findEstado = false;

			var numFilas = tabla.rows[inicioFila].cells[0].rowSpan;

			if( countRowSpan > 1 )
			{
				var indiceCeldaFecha = 2;
				var indiceCeldaServicio = 3;
				var indiceCelda = 4;
				var indiceCeldaJustificacion = 5;
				var indiceCeldaEstado = 7;
			}
			else
			{
				var indiceCeldaFecha = 3;
				var indiceCeldaServicio = 4;
				var indiceCelda = 5;
				var indiceCeldaJustificacion = 6;
				var indiceCeldaEstado = 8;
			}

			if((countRowSpan > 1))
				countRowSpan--;

			if(numFilas>1)
				countRowSpan = numFilas;

			if(isset(tabla.rows[inicioFila].cells[indiceCeldaFecha]))
			{
				// Obtengo el campo fecha de la fila actual
				var campoFecha = tabla.rows[inicioFila].cells[indiceCeldaFecha].childNodes[0];
				if( compare_dates(campoFecha.value, fechaInicioConsulta) && compare_dates(fechaFinConsulta, campoFecha.value))
				{
					findFecha = true;
				}

				campoServicio = tabla.rows[inicioFila].cells[indiceCeldaServicio].innerHTML;
				if( tipoServicioConsulta=="" || tipoServicioConsulta == " " || campoServicio == tipoServicioConsulta ){
					findServicio = true;
				}

				campo = tabla.rows[inicioFila].cells[indiceCelda].childNodes[2];
				if( procedimientocioConsulta=="" || procedimientocioConsulta==" " || campo.value == procedimientocioConsulta){
					findProcedimiento = true;
				}

				campoJustificacion = tabla.rows[inicioFila].cells[indiceCeldaJustificacion].childNodes[0];
				if( justificacionConsulta=="" || justificacionConsulta==" " || campoJustificacion.value == justificacionConsulta){
					findJustificacion = true;
				}

				/*
				campoEstado = tabla.rows[inicioFila].cells[indiceCeldaEstado].innerHTML;
				alert(campoEstado+" - "+estadoConsulta);
				if( estadoConsulta=="" || estadoConsulta==" " || campoEstado.value == estadoConsulta ){
					findEstado = true;
				}*/

				if(!findFecha || !findServicio || !findProcedimiento || !findJustificacion) //|| !findEstado)
				{
					tabla.rows[inicioFila].style.display = "none";
				}
				else
				{
					tabla.rows[inicioFila].style.display = "";
				}
			}
			//alert('Encontrado');
		}
}

/*****************************************************************************************************************************
 * Valida antes de llamar la adicion de medico tratante
 ******************************************************************************************************************************/
function adicionarMedico(){
	if(document.forms.forma.wselmed && document.forms.forma.wselmed.value != ''){

		var datos_medico = $("#wselmed").val();
		var historia = $("#whistoria").val();
		var ingreso = $("#wingreso").val();
		var usuario = $("#usuario").val();
		var codigoMatrix = $("#usuario").val();
		var fechaKardex = $("#wfechagrabacion").val();

		var vecDatosMedico = datos_medico.split("-");
		var tipoDocumento = vecDatosMedico[0];
		var nroDocumento = vecDatosMedico[1];
		var nombreMedico = vecDatosMedico[2];
		var codigoEspecialidad = vecDatosMedico[3];
		var idMedico = "";
		var tratante = document.getElementById("wchkmedtra").checked ? "on" : "off";

		insertarMedicoTratante(idMedico, tipoDocumento, nroDocumento, historia, ingreso, fechaKardex, tratante, usuario, codigoEspecialidad, nombreMedico, codigoMatrix);
	} else {
		alert("Debe seleccionar un médico para poder agregarlo.");
	}
}
/*****************************************************************************************************************************
 * Validación de la grabación de una nueva dieta
 ******************************************************************************************************************************/
function adicionarDieta(){
	if(document.forms.forma.wseldieta && document.forms.forma.wseldieta.value != ''){
		var selDieta = document.getElementById("wseldieta");
		var idDieta = selDieta.value;
		var nombreDieta = selDieta.options[selDieta.selectedIndex].text;
		var colDietas = document.forms.forma.colDietas;
		var fecha = document.forms.forma.wfechagrabacion.value;

		//Historia e ingreso
		var historia = document.forms.forma.whistoria.value;
		var ingreso = document.forms.forma.wingreso.value;

		var usuario = document.forms.forma.usuario.value;
		var cntDietas = document.getElementById('cntDietas');

		var elementoValidar = document.getElementById("Die"+idDieta);

		if((colDietas[idDieta] == null || colDietas[idDieta] == '') && !elementoValidar){
			cntDietas.innerHTML += "<span id='Die"+idDieta+"' class=vinculo>" + '<a onClick="quitarDieta('+"'"+idDieta+"'"+');" href="#null">' + nombreDieta + "</a><br/></span>";
			colDietas[idDieta] = nombreDieta;

			//Seccion ajax para hacer la insercion silenciosa
			insertarDietaKardex(idDieta, historia, ingreso, fecha, usuario);
		} else {
			alert("La dieta ya se encuentra asociada");
		}
	} else {
		alert("Debe seleccionar una dieta.");
	}
}
 /*****************************************************************************************************************************
 * Validación de la eliminación de una dieta
 ******************************************************************************************************************************/
 function quitarDieta(codigo){
	var fecha = document.forms.forma.wfecha.value;

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;

	var usuario = document.forms.forma.usuario.value;

	if(confirm("Desea quitar la dieta seleccionada?")){
		eliminarDietaElemento(historia,ingreso,fecha,usuario,codigo);
	}
	return false;
}
/*****************************************************************************************************************************
 * Valida la grabación de una infusión
 ******************************************************************************************************************************/
function grabarInfusion(idxElemento){
	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfechagrabacion.value;

	var fechaSolicitud = document.getElementById('wfliq'+idxElemento).value;
	var componentes = document.getElementById('wtxtcomponentes'+idxElemento);
	var obsComponentes = document.getElementById('wobscomponentes'+idxElemento).value;
	var usuario = document.forms.forma.usuario.value;

	//Se separan los codigos de los componentes separados por ;
	var strComponentes = "";
	var cont1 = 0;

	while(cont1 < componentes.length){
		strComponentes += componentes.options[cont1].value+"-"+componentes.options[cont1].text+";";
		cont1++;
	}

	if(strComponentes == ""){
		strComponentes = componentes.value;
	}

	/*****
	VALIDACION DE CAMPOS OBLIGATORIOS
	******/
	var valido = true;

	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		grabarInfusionElemento(historia,ingreso,fecha,strComponentes,idxElemento,obsComponentes,usuario,idxElemento,fechaSolicitud);
	}
	return valido;
}

/*****************************************************************************************************************************
 * Validación antes de quitar un articulo de la lista de medicamentos
 ******************************************************************************************************************************/
function quitarArticulo(idxElemento, tipoProtocolo, celdafila, contenedorArticulo, ctc, eliminaInsumoLQ,procAgrup ){
//	debugger;
	if( ctc ){
		ctc = "LQ";
	}
	
	if( !procAgrup ){
		procAgrup = "";
	}

	/********************************************************************************
	 * 2013-12-18
	 ********************************************************************************/
	var auxContenedor = "";
	if(celdafila == '' ){
		auxContenedor = "tbDetalleAddN";
	}
	else{
		if( contenedorArticulo == "imp" ){
			auxContenedor = "tbDetalleImpN";
		}
		else{
			auxContenedor = "tbDetalleN";
			auxContenedor = "tbDetalle"+tipoProtocolo;
		}
	}
	/********************************************************************************/

	var tipoTemporal = tipoProtocolo;
	// tipoProtocolo = "N";
	if(isset(document.getElementById('wnmmedimp'+tipoProtocolo+idxElemento)) && document.getElementById('wnmmedimp'+tipoProtocolo+idxElemento))
		var codigoArticulo = document.getElementById('wnmmedimp'+tipoProtocolo+idxElemento);
	else
		var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);

	var altaArticulo = '0';
	if(contenedorArticulo=='imp')
	{
		altaArticulo = '1';
		contenedorArticulo = '';
	}

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfecha.value;
	var usuario = document.forms.forma.usuario.value;

	//Se usa la frecuencia en caso de tener que eliminar el articulo del dextrometer
	var frecuencia = $( "#wperiod"+tipoProtocolo+idxElemento ).val();
	
	//Se envia fecha y hora de inicio en caso de que el articulo sea duplicable
	if(isset(document.getElementById('whfinicio'+tipoProtocolo+idxElemento)) && document.getElementById('whfinicio'+tipoProtocolo+idxElemento)){
		var fechaInicio = document.getElementById('whfinicio'+tipoProtocolo+idxElemento);
		var fh = fechaInicio.value.split("a las:");
		var fini = fh[0];
		var hini = fh[1];
	}
	else if(isset(document.getElementById('whfinicioimp'+tipoProtocolo+idxElemento)) && document.getElementById('whfinicioimp'+tipoProtocolo+idxElemento)){
		var fechaInicio = document.getElementById('whfinicioimp'+tipoProtocolo+idxElemento);
		var fh = fechaInicio.value.split("a las:");
		var fini = fh[0];
		var hini = fh[1];
	}

	var descripcionArticulo = "";

	if(codigoArticulo.tagName == 'INPUT'){
		descripcionArticulo = codigoArticulo.value;
	} else {
		descripcionArticulo = codigoArticulo.innerHTML;
	}

	var cd = descripcionArticulo.split("-");
	codigoArticulo = cd[0];

	tipoProtocolo = tipoTemporal;

	// Si el articulo ha sido agregado por javascript
	if(codigoArticulo == ''){

		// if(isset(document.getElementById( "wudosisN0" )))
			// document.getElementById( "trEncabezadoTbAdd" ).style.display = '';
		// else
			// document.getElementById( "trEncabezadoTbAdd" ).style.display = 'none';

		if(isset(contenedorArticulo) && contenedorArticulo != '')
		{
			var cntDetalleKardex = document.getElementById(contenedorArticulo/*+tipoProtocolo*/);
			var filaEliminar = document.getElementById("trFil"+idxElemento);
			cntDetalleKardex.removeChild(filaEliminar);
		}
		// elementosDetalle--;

		return true; //Noviembre 19 de 2012
	}
	else {

		if( !ctc ){
			var msg = "Esta seguro de eliminar el articulo "+ trim(descripcionArticulo) + "?";
		}
		else
		{
			var msg = "Si sale del CTC el medicamento no será tenido en cuenta";
		}

		var confQuitar = false;

		if( ctc != "LQ" ){
			//confQuitar = confirm( msg );
			jConfirm( msg, 'Eliminar Articulo', function(r) {

				if( r )
				{
					//Aqui quitar articulo de procedimiento agrupado
					// alert("1");
					quitarMedArrayProcAgrupados(tipoProtocolo,idxElemento,procAgrup);
					
					//Quitar insumos NPT del array
					if( insumosNPT[tipoProtocolo+idxElemento] )
					{
						delete insumosNPT[tipoProtocolo+idxElemento];
						$('#botonNPT').attr('disabled',false);
						$('#wesnpt'+tipoProtocolo+idxElemento).val('');
					}
					
					if( tipoProtocolo == "LQ" ){
						try{
							eliminarArtsLevPorIdx( tipoProtocolo+idxElemento );
						}
						catch(e){
						}
					}

					// msjNoPos= msjNoPos.replace(codigoArticulo+',', "");

					if( $( "#widoriginal" +tipoProtocolo+idxElemento  ).val()*1 > 0 ){
						/**********************************************************************
						 * Creo la acción para ejecutarla al momento de guardar
						 **********************************************************************/
						if( !accionesOrdenes[ ELIMINAR_ARTICULO ] ){
							accionesOrdenes[ ELIMINAR_ARTICULO ] = {};
						}

						if( !accionesOrdenes[ ELIMINAR_ARTICULO ][ tipoProtocolo+"-"+idxElemento ] ){
							accionesOrdenes[ ELIMINAR_ARTICULO ][ tipoProtocolo+"-"+idxElemento ] = new Array();

							var aux = accionesOrdenes[ ELIMINAR_ARTICULO ][ tipoProtocolo+"-"+idxElemento ];
							aux[ aux.length ] = historia; 			//parametro1
							aux[ aux.length ] = ingreso; 			//parametro2
							aux[ aux.length ] = fecha; 				//parametro3
							aux[ aux.length ] = codigoArticulo; 	//parametro4
							aux[ aux.length ] = usuario; 			//parametro5
							aux[ aux.length ] = idxElemento; 		//parametro6
							aux[ aux.length ] = fini; 				//parametro7
							aux[ aux.length ] = hini; 				//parametro8
							aux[ aux.length ] = tipoProtocolo; 		//parametro9
							aux[ aux.length ] = contenedorArticulo; //parametro10
							aux[ aux.length ] = altaArticulo ; 		//parametro11
							aux[ aux.length ] = $( "#widoriginal" +tipoProtocolo+idxElemento  ).val(); 		//parametro12
						}
						/**********************************************************************/
					}

					// eliminarArticuloElemento(historia,ingreso,fecha,codigoArticulo,usuario,idxElemento,fini,hini,tipoProtocolo,contenedorArticulo,altaArticulo);
					// eliminarArticuloElemento(historia,ingreso,fecha,codigoArticulo,usuario,idxElemento,fini,hini,"N","detKardexAddN",altaArticulo);

					try{ //celdafila.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.previousSibling

						celdafila.parentNode.parentNode.style.display = 'none';
						//Elimina la fila para el articulo.
						$("[idtr=trFil"+tipoProtocolo+idxElemento+"]").remove();

					}
					catch(e){

						if( $( "#trFil"+idxElemento, $( "#"+contenedorArticulo ) ).length > 0 ){
							// $( "#trFil"+idx, $( contenedorArticulo ) ).remove();
							$( "#wnmmed"+tipoProtocolo+idxElemento, $( "#"+contenedorArticulo ) ).parent().parent().remove();
						}
						else{
							 // $( "#trFil"+idx, $( "#detKardexAddN" ) ).remove();
							 $( "#wnmmed"+tipoProtocolo+idxElemento, $( "#detKardexAddN" ) ).parent().parent().remove();
						}
					}
					
					/****************************************************************************************
					 * Si el articulo es del dextrometer
					 * No se deja poner la opción del dextrometer
					 *****************************************************************************************/
					quitarArticuloDextrometer( frecuencia, codigoArticulo );
					/*****************************************************************************************/


					return true; //Noviembre 19 de 2012
				}
				else{
					return false;	//Noviembre 19 de 2012
				}

			});

		}
		else{

			//Aqui quitar articulo de procedimiento agrupado
			// alert("2" + ctc);
			quitarMedArrayProcAgrupados(tipoProtocolo,idxElemento,procAgrup);
			
			//Quitar insumos NPT del array
			if( insumosNPT[tipoProtocolo+idxElemento] )
			{
				delete insumosNPT[tipoProtocolo+idxElemento];
				$('#botonNPT').attr('disabled',false);
				$('#wesnpt'+tipoProtocolo+idxElemento).val('');
			}
			
			if( !eliminaInsumoLQ ){
				if( tipoProtocolo == "LQ" ){
					try{
						eliminarArtsLevPorIdx( tipoProtocolo+idxElemento );
					}
					catch(e){
					}
				}
			}
			
			msjNoPos= msjNoPos.replace(codigoArticulo+',', "");

			/**********************************************************************
			 * Creo la acción para ejecutarla al momento de guardar
			 **********************************************************************/
			if( !accionesOrdenes[ ELIMINAR_ARTICULO ] ){
				accionesOrdenes[ ELIMINAR_ARTICULO ] = {};
			}

			if( !accionesOrdenes[ ELIMINAR_ARTICULO ][ tipoProtocolo+"-"+idxElemento ] ){
				accionesOrdenes[ ELIMINAR_ARTICULO ][ tipoProtocolo+"-"+idxElemento ] = new Array();

				var aux = accionesOrdenes[ ELIMINAR_ARTICULO ][ tipoProtocolo+"-"+idxElemento ];
				aux[ aux.length ] = historia; 			//parametro1
				aux[ aux.length ] = ingreso; 			//parametro2
				aux[ aux.length ] = fecha; 				//parametro3
				aux[ aux.length ] = codigoArticulo; 	//parametro4
				aux[ aux.length ] = usuario; 			//parametro5
				aux[ aux.length ] = idxElemento; 		//parametro6
				aux[ aux.length ] = fini; 				//parametro7
				aux[ aux.length ] = hini; 				//parametro8
				aux[ aux.length ] = tipoProtocolo; 		//parametro9
				aux[ aux.length ] = contenedorArticulo; //parametro10
				aux[ aux.length ] = altaArticulo ; 		//parametro11
				aux[ aux.length ] = $( "#widoriginal" +tipoProtocolo+idxElemento  ).val() ; 		//parametro12
			}
			/**********************************************************************/

			// eliminarArticuloElemento(historia,ingreso,fecha,codigoArticulo,usuario,idxElemento,fini,hini,tipoProtocolo,contenedorArticulo,altaArticulo);
			// eliminarArticuloElemento(historia,ingreso,fecha,codigoArticulo,usuario,idxElemento,fini,hini,"N","detKardexAddN",altaArticulo);

			try{ //celdafila.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.previousSibling

				celdafila.parentNode.parentNode.style.display = 'none';
				//Elimina la fila para el articulo.
				$("[idtr=trFil"+tipoProtocolo+idxElemento+"]").remove();

			}
			catch(e){

				if( $( "#trFil"+idxElemento, $( "#"+contenedorArticulo ) ).length > 0 ){
					// $( "#trFil"+idx, $( contenedorArticulo ) ).remove();
					$( "#wnmmed"+tipoProtocolo+idxElemento, $( "#"+contenedorArticulo ) ).parent().parent().remove();
				}
				else{
					 // $( "#trFil"+idx, $( "#detKardexAddN" ) ).remove();
					 $( "#wnmmed"+tipoProtocolo+idxElemento, $( "#detKardexAddN" ) ).parent().parent().remove();
				}
			}
			
			/****************************************************************************************
			 * Si el articulo es del dextrometer
			 * No se deja poner la opción del dextrometer
			 *****************************************************************************************/
			//Es del dextrometer si es el mismo articulo del dextrometer y tiene la misma frecuencia
			quitarArticuloDextrometer( frecuencia, codigoArticulo );
			/*****************************************************************************************/

			return true; //Noviembre 19 de 2012
		}
	}
}
/*****************************************************************************************************************************
 * Validación antes de quitar una infusión
 ******************************************************************************************************************************/
function quitarInfusion(idxElemento){
	var historia = document.forms.forma.whistoria.value;
	var fecha = document.forms.forma.wfecha.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;

	if( confirm("Esta seguro de quitar el liquido endovenoso # "+ idxElemento + "?") ){
		eliminarInfusionElemento(historia,ingreso,fecha,usuario,idxElemento);
	}
}
/*****************************************************************************************************************************
 * Validación antes de quitar un examen de laboratorio
 ******************************************************************************************************************************/
function quitarExamen( idxElemento, prefijoAlta, nuevoExamen, ctc, hce ){
//	var codigoExamen = document.getElementById('wexamenlab'+idxElemento).value;

	var prefijoAltaLower = prefijoAlta.toLowerCase();

	var codigoExamen = document.getElementById('hexcco'+prefijoAltaLower+idxElemento).value;
	var fecha = document.forms.forma.wfecha.value;
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;

	//HCE
	var consecutivoOrden = document.getElementById("hexcons"+prefijoAltaLower+idxElemento).value;
	nroconsecutivoOrden = document.getElementById("hexcons"+prefijoAltaLower+idxElemento).value;
	var consecutivoExamen = document.getElementById("hexcod"+prefijoAltaLower+idxElemento).value;
	var nroItem = document.getElementById("hexnroitem"+prefijoAltaLower+idxElemento).value;
	var cco = document.getElementById("hexcco"+prefijoAltaLower+idxElemento).value;
	var firmForm = document.getElementById("hiFormHce"+prefijoAltaLower+idxElemento).value;

	if( !ctc ){
		var msg = "Esta seguro de eliminar el examen?";
	}
	else{
		//Si el cierre de la interfaz es desde un fomrulario hce no mostrara este mensaje.
		if(hce != 'on'){

			var msg = "Si no llena el CTC el examen no será tenido en cuenta para la orden. ¿Seguro desea cerrar?";

		}
	}

	//Si el cierre es desde hce no mostrara el mensaje de confirmacion: "Si no llena el CTC el examen no será tenido en cuenta para la orden. ¿Seguro desea cerrar?"
	//y eliminara la orden de la interfaz.
	if(hce != 'on'){

		jConfirm( msg, 'Eliminar Examen', function(r) {
			 // /**********************************************************************
			 // * Creo la acción para ejecutarla al momento de guardar
			 // **********************************************************************/

			if(r){
				if( !accionesOrdenes[ ELIMINAR_EXAMEN ] ){
					accionesOrdenes[ ELIMINAR_EXAMEN ] = {};
				}

				if( !accionesOrdenes[ ELIMINAR_EXAMEN ][ idxElemento ] ){
					accionesOrdenes[ ELIMINAR_EXAMEN ][ idxElemento ] = new Array();

					var aux = accionesOrdenes[ ELIMINAR_EXAMEN ][ idxElemento ];
					aux[ aux.length ] = historia; 			//parametro1
					aux[ aux.length ] = ingreso; 			//parametro2
					aux[ aux.length ] = codigoExamen; 		//parametro3
					aux[ aux.length ] = fecha; 				//parametro4
					aux[ aux.length ] = usuario; 			//parametro5
					aux[ aux.length ] = nroconsecutivoOrden; 	//parametro6
					aux[ aux.length ] = consecutivoExamen; 	//parametro7
					aux[ aux.length ] = idxElemento; 		//parametro8
					aux[ aux.length ] = nroItem; 			//parametro9
					aux[ aux.length ] = prefijoAlta; 		//parametro10
					aux[ aux.length ] = nuevoExamen; 		//parametro11
					aux[ aux.length ] = cco; 				//parametro12
					aux[ aux.length ] = firmForm; 			//parametro13
				}
				/**********************************************************************/


				/********************************************************************************
				 * Agosto 12 de 2014
				 * Se remueve a fila correspondiente de examen
				 ********************************************************************************/
				var prefijoAltaLower = prefijoAlta.toLowerCase();

				var centroCostosExamen = document.getElementById("hexcco"+prefijoAltaLower+idxElemento).value;
				var consecutivoOrden = document.getElementById("hexcons"+prefijoAltaLower+idxElemento).value;

				var cntExamenes = document.getElementById("detExamenes"+centroCostosExamen+prefijoAlta);

				var filaEliminar = document.getElementById("trEx"+prefijoAlta+idxElemento);
				var descr_filaEliminar = "trEx"+prefijoAlta+idxElemento;

				if(prefijoAlta!='Imp')
				{
					var celdaExamen = document.getElementById("del"+prefijoAlta+centroCostosExamen+consecutivoOrden);

					var numRows = celdaExamen.rowSpan;

					if( celdaExamen.rowSpan > 1 &&	 filaEliminar.cells[0] == celdaExamen ){
						cntExamenes.parentNode.rows[ filaEliminar.rowIndex+1 ].insertBefore( celdaExamen, cntExamenes.parentNode.rows[ filaEliminar.rowIndex+1 ].cells[0] );
					}

					celdaExamen.rowSpan = numRows;

					if( numRows > 1 ){
						numRows--
						celdaExamen.rowSpan = numRows;
					}
				}

				$("#"+descr_filaEliminar).remove();

				if( ctc == true ){
					cerrarVentanaCtc( historia, ingreso, fecha, wemp_pmla,'','' );

					//eliminar del array procedimientos agrupados
					for( var x in procAgrupados ){
						for (var i = 0; i < procAgrupados[x].length; i++) {
							tipo=procAgrupados[x][i].tipo;
							if(procAgrupados[x][i].consecutivo==idxElemento)
							{
								var arMed = procAgrupados[x][i].medicamentos
								for( var u in arMed ){
									
									if(strPendientesCTC != "")
									{
										strPendientesCTC = strPendientesCTC.replace("articulo|"+arMed[ u ].codMed+"|"+arMed[ u ].tipoProtocolo+"|"+arMed[ u ].posicionActual+"\r\n", "");
									}
									
									//Quitar medicamento
									quitarArticulo(arMed[ u ].posicionActual,arMed[ u ].tipoProtocolo,'','detKardexAddN','LQ','','procAgrupEliminado');	
								}
								
								
								procAgrupados[ x ].splice(i,1);
								i--;
								pintarTdAgrupado(tipo,x,idxElemento);
							}
						}
					}

				}
			}
		});

	}
	else{


		/**********************************************************************
			 * Creo la acción para ejecutarla al momento de guardar
			 **********************************************************************/
			if( !accionesOrdenes[ ELIMINAR_EXAMEN ] ){
				accionesOrdenes[ ELIMINAR_EXAMEN ] = {};
			}

			if( !accionesOrdenes[ ELIMINAR_EXAMEN ][ idxElemento ] ){
				accionesOrdenes[ ELIMINAR_EXAMEN ][ idxElemento ] = new Array();

				var aux = accionesOrdenes[ ELIMINAR_EXAMEN ][ idxElemento ];
				aux[ aux.length ] = historia; 			//parametro1
				aux[ aux.length ] = ingreso; 			//parametro2
				aux[ aux.length ] = codigoExamen; 		//parametro3
				aux[ aux.length ] = fecha; 				//parametro4
				aux[ aux.length ] = usuario; 			//parametro5
				aux[ aux.length ] = consecutivoOrden; 	//parametro6
				aux[ aux.length ] = consecutivoExamen; 	//parametro7
				aux[ aux.length ] = idxElemento; 		//parametro8
				aux[ aux.length ] = nroItem; 			//parametro9
				aux[ aux.length ] = prefijoAlta; 		//parametro10
				aux[ aux.length ] = nuevoExamen; 		//parametro11
				aux[ aux.length ] = cco; 				//parametro12
				aux[ aux.length ] = firmForm; 			//parametro13
			}
			/**********************************************************************/


			/********************************************************************************
			 * Agosto 12 de 2014
			 * Se remueve a fila correspondiente de examen
			 ********************************************************************************/
			var prefijoAltaLower = prefijoAlta.toLowerCase();

			var centroCostosExamen = document.getElementById("hexcco"+prefijoAltaLower+idxElemento).value;
			var consecutivoOrden = document.getElementById("hexcons"+prefijoAltaLower+idxElemento).value;

			var cntExamenes = document.getElementById("detExamenes"+centroCostosExamen+prefijoAlta);

			var filaEliminar = document.getElementById("trEx"+prefijoAlta+idxElemento);
			var descr_filaEliminar = "trEx"+prefijoAlta+idxElemento;

			if(prefijoAlta!='Imp')
			{
				var celdaExamen = document.getElementById("del"+prefijoAlta+centroCostosExamen+consecutivoOrden);

				var numRows = celdaExamen.rowSpan;

				if( celdaExamen.rowSpan > 1 &&	 filaEliminar.cells[0] == celdaExamen ){
					cntExamenes.parentNode.rows[ filaEliminar.rowIndex+1 ].insertBefore( celdaExamen, cntExamenes.parentNode.rows[ filaEliminar.rowIndex+1 ].cells[0] );
				}

				celdaExamen.rowSpan = numRows;

				if( numRows > 1 ){
					numRows--
					celdaExamen.rowSpan = numRows;
				}
			}

			$("#"+descr_filaEliminar).remove();
			/********************************************************************************/

			// eliminarExamenElemento(historia,ingreso,codigoExamen,fecha,usuario,consecutivoOrden,consecutivoExamen,idxElemento, nroItem, prefijoAlta, nuevoExamen);
			return true;

	}

	return false;
}

/*****************************************************************************************************************************
 * Define que valor toma el campo a grabar según el campo que se haya modificado
 *****************************************************************************************************************************/
function definirValorCampo(campo0,campo1,campo2)
{
	if( isset(document.getElementById(campo0)) && isset(document.getElementById(campo1)) && isset(document.getElementById(campo2)) )
	{
		var valorCampo0 = document.getElementById(campo0).value;
		var valorCampo1 = document.getElementById(campo1).value;
		var valorCampo2 = document.getElementById(campo2).value;

		if(valorCampo1!=valorCampo0)
		{
			document.getElementById(campo1).value = valorCampo1;
			document.getElementById(campo0).value = valorCampo1;
		}
		else if(valorCampo2!=valorCampo0)
		{
			document.getElementById(campo1).value = valorCampo2;
			document.getElementById(campo0).value = valorCampo2;
		}
	}
}

/*****************************************************************************************************************************
 * Debido que para la pestaña de alta se usaron campos que estan en otras pestañas
 * Se crea esta función que determina cual cambió y graba según este cambio
 * Si cambian los dos prevalece el valor del campo de la pestaña original, no la de alta
 *****************************************************************************************************************************/
function unificarCamposArticulos(idxElemento,tipoProtocolo){

	definirValorCampo('wdosisori'+tipoProtocolo+idxElemento,'wdosis'+tipoProtocolo+idxElemento,'wdosisimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wudosisori'+tipoProtocolo+idxElemento,'wudosis'+tipoProtocolo+idxElemento,'wudosisimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wffticaori'+tipoProtocolo+idxElemento,'wfftica'+tipoProtocolo+idxElemento,'wffticaimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wperiodori'+tipoProtocolo+idxElemento,'wperiod'+tipoProtocolo+idxElemento,'wperiodimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wfinicioori'+tipoProtocolo+idxElemento,'wfinicio'+tipoProtocolo+idxElemento,'wfinicioimp'+tipoProtocolo+idxElemento);

	definirValorCampo('wtxtobsori'+tipoProtocolo+idxElemento,'wtxtobs'+tipoProtocolo+idxElemento,'wtxtobsimp'+tipoProtocolo+idxElemento);

}

/*****************************************************************************************************************************
 * Debido que para la pestaña de alta se usaron campos que estan en otras pestañas
 * Se crea esta función que determina cual cambio y graba según este cambio
 * Si cambian los dos prevalece el valor del campo de la pestaña original, no la de alta
 *****************************************************************************************************************************/
function unificarCamposExamenes(idxElemento){

	definirValorCampo('wfsolori'+idxElemento,'wfsol'+idxElemento,'wfsolimp'+idxElemento);

	definirValorCampo('wnmexamen'+idxElemento,'wnmexamen'+idxElemento,'wnmexamenimp'+idxElemento);

	definirValorCampo('wtxtjustexamen'+idxElemento,'wtxtjustexamen'+idxElemento,'wtxtjustexamenimp'+idxElemento);

}

/*****************************************************************************************************************************
 * Validación de la grabación de un medicamento
 *****************************************************************************************************************************/
function grabarArticulo(idxElemento,tipoProtocolo){

	// Debido que para la pestaña de alta se usaron campos que estan en otras pestañas
	// Se crea esta función que determina cual cambio y graba según este cambio
	// Si cambian los dos prevalece el valor del campo de la pestaña original, no la de alta
	unificarCamposArticulos(idxElemento,tipoProtocolo);

	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fechaKardex = document.forms.forma.wfechagrabacion.value;
	var centroCostosGrabacion = document.forms.forma.centroCostosGrabacion.value;
	var prioridad = document.forms.forma.whusuariolactario.value;

	// tipoProtocolo = "N";

	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);
	var datosArticulos = "";
	var cantDosis = document.getElementById('wdosis'+tipoProtocolo+idxElemento);
	var unidadDosis = document.getElementById('wudosis'+tipoProtocolo+idxElemento);
	var periodicidad = document.getElementById('wperiod'+tipoProtocolo+idxElemento);

	var equivHorasFrecuencia = periodicidad.value;
	var condicion = document.getElementById('wcondicion'+tipoProtocolo+idxElemento);
	var formaFtica = document.getElementById('wfftica'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var horaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var dosisAdaptada = document.getElementById('wchkDA'+tipoProtocolo+idxElemento);
	var noEsteril = document.getElementById('wchkNE'+tipoProtocolo+idxElemento);

	var idoriginal = document.getElementById('widoriginal'+tipoProtocolo+idxElemento);
	idoriginal = idoriginal ? idoriginal.value : 0;
	
	var articulosGrabadoAnteriormente = false;

	var wdrautorizado = document.getElementById('wdrautorizado'+tipoProtocolo+idxElemento).value;
	var wjusparaautorizar = $( document.getElementById('wjusparaautorizar'+tipoProtocolo+idxElemento) ).val();
	
	
	artdosisAdaptada = false;

	if(dosisAdaptada !== null)	{
		if(dosisAdaptada.checked){
			artdosisAdaptada = true;
		}
	}

	artnoEsteril = false;

	if(noEsteril !== null)	{
		if(noEsteril.checked){
			artnoEsteril = true;
		}
	}


	var impresion = 'off';
	var deAlta = 'off';
	if(isset(document.getElementById('wimp'+tipoProtocolo+idxElemento)))
	{
		var impresion = document.getElementById('wimp'+tipoProtocolo+idxElemento).value;
		deAlta = document.getElementById('wimp'+tipoProtocolo+idxElemento).value;
	}

	if(isset(document.getElementById('wchkimp_art'+tipoProtocolo+idxElemento)))
	{
		if(document.getElementById('wchkimp_art'+tipoProtocolo+idxElemento).checked==true)
			var impresion = 'on';
		else
			var impresion = 'off';
	}


	//Via de administracion
	if(isset(document.getElementById('wviadmon'+tipoProtocolo+idxElemento)))
		var via = document.getElementById('wviadmon'+tipoProtocolo+idxElemento);
	else if(isset(document.getElementById('wviadmonimp'+tipoProtocolo+idxElemento)))
		var via = document.getElementById('wviadmonimp'+tipoProtocolo+idxElemento);
	else
		var via = '';

	var confirmar = document.getElementById('wchkconf'+tipoProtocolo+idxElemento);
	var diasTto = document.getElementById('wdiastto'+tipoProtocolo+idxElemento);

	if(deAlta!="on")
		var dosMax = document.getElementById('wdosmax'+tipoProtocolo+idxElemento).value;
	else
		var dosMax = "";

	var observacion = document.getElementById('wtxtobsori'+tipoProtocolo+idxElemento);
	var usuario = document.forms.forma.usuario.value;
	var primerKardex = (document.getElementById("wkardexnuevo") && document.getElementById("wkardexnuevo").value == 'S' ? "S" : "N");



	if( isset(document.getElementById('wcantaltaimp'+tipoProtocolo+idxElemento)) )
		var cantidadAlta = document.getElementById('wcantaltaimp'+tipoProtocolo+idxElemento).value;
	else if(deAlta=="on" && document.getElementById('wdosmax'+tipoProtocolo+idxElemento)!="")
		var cantidadAlta = document.getElementById('wdosmax'+tipoProtocolo+idxElemento).value;
	else
		var cantidadAlta = "";

	var unidadManejo = document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento) ? document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento).value : '';
	var vence = (document.getElementById('whvence'+tipoProtocolo+idxElemento) && document.getElementById('whvence'+tipoProtocolo+idxElemento).value == 'on' ? true : false);
	var cantFracciones = document.getElementById('whcmanejo'+tipoProtocolo+idxElemento) ? document.getElementById('whcmanejo'+tipoProtocolo+idxElemento).value : '';

	// var artProtocolo = document.getElementById('whartpro'+tipoProtocolo+idxElemento).value;
	var artProtocolo = tipoProtocolo;
	var noDispensar = document.getElementById('wchkdisp'+tipoProtocolo+idxElemento);

	var cantGrabar = 0;
	var cantidadManejo = 1;		//Este es el valor base de dispensacion
	var dosisSuministrar = 0;

	// equivHorasFrecuencia = parseFloat(equivHorasFrecuencia);

	//Codigo articulo
	var cd = "";
	if(codigoArticulo.tagName == 'INPUT'){
		datosArticulos = codigoArticulo.value;
		cd = codigoArticulo.value.split("-");
	} else {
		articulosGrabadoAnteriormente = true;	//Si es div fue grabado
		datosArticulos = codigoArticulo.innerHTML;
		cd = codigoArticulo.innerHTML.split("-");
	}

	codigoArticulo = cd[0];
	var origenArticulo = cd[1];
	var nombreArticulo = cd[2];

	//Extraccion de la fecha y hora inicial de administracion
	var fh = fechaInicio.value.split("a las:");
	fechaInicio = fh[0];
	horaInicio = fh[1];

	//Conversion a hora entero para la hora
	var vecHora = horaInicio.split(":");
	var intHoraInicio = vecHora[0];


	/*****
	VALIDACION DE CAMPOS OBLIGATORIOS
	******/
	var valido = true;

	//Codigo articulo
	if(!codigoArticulo || codigoArticulo == ''){
		alert('Debe ingresar el codigo del articulo: '+ datosArticulos);
		valido = false;
	}

	//Dosis
	if(valido && (!cantDosis || cantDosis.value == '')){
		alert('Debe ingresar la cantidad de dosis a suministrar: '+ datosArticulos);
		valido = false;
	}

	if(valido && eval(cantDosis.value) <= 0){
		alert('La cantidad de dosis debe ser mayor que cero: '+ datosArticulos);
		valido = false;
	}

	//Unidad de Dosis
	if(valido && (!unidadDosis || unidadDosis.value == '')){
		alert('Debe seleccionar la unidad de dosis a suministrar: '+ datosArticulos);
		valido = false;
	}

	//Periodicidad o frecuencia
	if(valido && (!periodicidad || periodicidad.value == '')){
		alert('Debe seleccionar la frecuencia con la que se debe suministrar: '+ datosArticulos);
		valido = false;
	}

	//Forma farmaceutica
	if(valido && (!formaFtica || formaFtica.value == '')){
		formaFtica.value = '00';
//		alert('Debe ingresar la forma farmaceutica');
//		valido = false;
	}

	//Fecha de inicio del tratamiento
	if(valido && (!fechaInicio || fechaInicio == '')){
		alert('Debe ingresar la fecha de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}

	//Hora de inicio del tratamiento
	if(valido && (!horaInicio || horaInicio == '')){
		alert('Debe ingresar la hora de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}

	//Via de administracion
	if(deAlta!='on')
	{
		if(valido && (!via || via.value == '')){
			alert('Debe seleccionar la vía de administración: '+ datosArticulos);
			valido = false;
		}
	}

	//La fecha de inicio debe ser mayor o igual a la del kardex
	// 2013-02-06
	// Esta validación salía al grabar aún estando las fechas bien en el formulario
	// Se comenta porque mas abajo se hace la validación correcta de estos datos
	/*
	if(valido && (!esFechaMayorIgual(fechaInicio,fechaKardex))){
		alert('La fecha de inicio de administración del artículo debe ser mayor o igual a la fecha actual: '+ datosArticulos);
		valido = false;
	}
	*/


	/*Si la hora actual de validación está entre las cero horas y las cuatro horas, todas las horas son permitidas
	 * Quiere decir esto que desde las seis de la mañana empiezan a haber restricciones de horario
	 */
	var horaValidacion = new Date().getHours();

	if(valido && horaValidacion >= 6){
		if(intHoraInicio != 0){
			if(intHoraInicio < (horaValidacion - 2)){
//				valido = false;
			}
		}
	}

	//Validacion de fechas. No se permite fecha actual y cero horas :D
	var valHoraInicio = intHoraInicio;
	var vecFechaInicio = fechaInicio.split("-");
	var fval = new Date();
	var fcomp = new Date().getHours();
	fcomp = fcomp - 2;
	fval.setHours(fcomp);

	if(intHoraInicio && intHoraInicio.substring(0,1) == "0"){
		valHoraInicio = intHoraInicio.substring(1,2);
	}

	var valDiaInicio = vecFechaInicio[2];
	if(valDiaInicio && valDiaInicio.substring(0,1) == "0"){
		valDiaInicio = valDiaInicio.substring(1,2);
	}

	if(valido && (parseInt(valDiaInicio)-parseInt(new Date().getDate())==0)){
		if(parseInt(valHoraInicio) == 0 && parseInt(new Date().getHours()) != 0){
			alert('La fecha de inicio de administración del artículo debe ser mayor o igual a la fecha actual: '+ datosArticulos);
			valido = false;
		}
	}

	var fechaInicioAnterior = fechaInicio;
	var horaInicioAnterior = horaInicio;

	//Checkbuttons
	var artConfirmado = false;
	var artNoDispensar = false;

	if(confirmar && confirmar.checked){
		artConfirmado = true;
	}

	if(noDispensar && noDispensar.checked){
		artNoDispensar = true;
	}

	//Validacion de que no se encuentre un mismo articulo con la misma fecha y hora de inicio
	var datosArticulo = "";
	var codigoValidacionArticulo = "";
	var fechaHoraValidacionArticulo = "";
	if( !$( "[idtr=trFil"+tipoProtocolo+idxElemento+"]" ).hasClass( "suspendido" ) ){
		for(var indiceArticulo=0;document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo);indiceArticulo++){
			if(indiceArticulo != idxElemento){
				if( !$( "[idtr=trFil"+tipoProtocolo+indiceArticulo+"]" ).hasClass( "suspendido" ) ){
					if(document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).tagName == 'DIV'){
						datosArticulo = document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).innerHTML;
					} else {
						datosArticulo = document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).value;
					}
					codigoValidacionArticulo = datosArticulo.split("-");
					fechaHoraValidacionArticulo = document.getElementById('whfinicio'+tipoProtocolo+indiceArticulo) ? document.getElementById('whfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:") : document.getElementById('wfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:");
					fechaHoraValidacionArticulo2 = document.getElementById('wfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:");	//Abril 26 de 2011
				}
			}
		}
	}

	var fechaInicioAnterior = fechaInicio;
	var horaInicioAnterior = horaInicio;

	//Checkbuttons
	var artConfirmado = false;
	var artNoDispensar = false;

	if(confirmar && confirmar.checked){
		artConfirmado = true;
	}

	if(noDispensar && noDispensar.checked){
		artNoDispensar = true;
	}


	var f=new Date();
	var fecha = f.getFullYear()+'-'+(f.getMonth()*1+1)*1+'-'+f.getDate()+' '+f.getHours()+':'+f.getMinutes();
	usuariodes = ucWords($("#usuariodes").val());

	var observacionadd = "";
	var nuevaobservacion = "";
	var obsmerge = "";

	if(observacion == null){

	var observaciontxt = "";

	}else{

	var observaciontxt = $.trim( observacion.value );

	}


	var claseobs = "";

	var busqueda = observaciontxt.match(/Origen:/g);
	if(busqueda)
		var numfilas = busqueda.length;
	else
		var numfilas = 0;

	if(numfilas%2 == 0)
		claseobs = "fila2";
	else
		claseobs = "fila1";

	if( $.trim( $('#wtxtobs'+tipoProtocolo+idxElemento).val() ) != '' )
	{
		observacionadd = document.getElementById('wtxtobs'+tipoProtocolo+idxElemento).value;
		if(observacionadd!="" && observacionadd!=" ")
		{
			nuevaobservacion = observacionadd+'<br><span style="font-size:7pt;">'+usuario+' - '+usuariodes+'<br>Origen:Ordenes | '+fecha+'</span>';
		}
		obsmerge = '<div class="'+claseobs+'" style="border-bottom:4px #fff solid;">'+nuevaobservacion+'</div>'+observaciontxt;
	}
	else
	{
		if(observaciontxt!="" && observaciontxt!=" ")
			obsmerge = '<div class="'+claseobs+'" style="border-bottom:4px #fff solid;">'+observaciontxt+'<br><span style="font-size:7pt;">'+usuario+' - '+usuariodes+'<br>Origen:Ordenes | '+fecha+'</span></div>';
	}
	
	//Si tiene permisos para dosis máxima
	var perDmax = $( "#waccN\\.13" ).val().split( "," )[0] == 'S' ? true: false;
	
	if( !articulosGrabadoAnteriormente && valido && $( "#wesantibiotico"+tipoProtocolo+idxElemento ).val() == "on" ){
		if( !$( "#wprofilaxis"+tipoProtocolo+idxElemento )[0].checked && !$( "#wtratamiento"+tipoProtocolo+idxElemento )[0].checked ){
			/*if( $( "#wdiastto"+tipoProtocolo+idxElemento ).val()*1 == 0  && $( "#wdosmax"+tipoProtocolo+idxElemento ).val()*1 == 0 )
				jAlert( "Debe seleccionar si el antibiotico es por <b>PROFILAXIS</b> o <b>TRATAMIENTO</b> y <br>Debe ingresar un valor mayor a 0 en d&iacute;as de tratamiento o dosis m&aacute;xima<br>para el antibiótico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>", "ALERTA" );
			else
				jAlert( "Debe seleccionar si el antibiotico es por <b>PROFILAXIS</b> o <b>TRATAMIENTO</b> para el antibiótico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>", "ALERTA" );*/
				
			
			
			var msg = "Debe seleccionar si el antibiotico es por <b>PROFILAXIS</b> o <b>TRATAMIENTO</b> ";
			if( $( "#wdiastto"+tipoProtocolo+idxElemento ).val()*1 == 0  && $( "#wdosmax"+tipoProtocolo+idxElemento ).val()*1 == 0 ){
				//Si permite dosis máxima
				if( perDmax )
					msg += " y <br>Debe ingresar un valor mayor a 0 en d&iacute;as de tratamiento o dosis m&aacute;xima<br>";
				else
					msg += " y <br>Debe ingresar un valor mayor a 0 en d&iacute;as de tratamiento<br>";
			}
			msg += "para el antibiótico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>";
			
			jAlert( msg, "ALERTA" );
			valido = false;
		}
	}
	
	if( !articulosGrabadoAnteriormente && valido && $( "#wesantibiotico"+tipoProtocolo+idxElemento ).val() == "on" ){
		if( !$( "#wprofilaxis"+tipoProtocolo+idxElemento )[0].disabled && !$( "#wtratamiento"+tipoProtocolo+idxElemento )[0].disabled ){
			if( ( $( "#wtratamiento"+tipoProtocolo+idxElemento )[0].checked || $( "#wprofilaxis"+tipoProtocolo+idxElemento )[0].checked ) && $( "#wdiastto"+tipoProtocolo+idxElemento ).val()*1 == 0  && $( "#wdosmax"+tipoProtocolo+idxElemento ).val()*1 == 0 ){
				// jAlert( "Debe ingresar un valor mayor a 0 en d&iacute;as de tratamiento o dosis m&aacute;xima para el antibi&oacute;tico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>", "ALERTA" );
				var msg = "Debe ingresar un valor mayor a 0 ";
				//Si permite dosis máxima
				if(perDmax)
					msg += "en d&iacute;as de tratamiento o dosis m&aacute;xima ";
				else
					msg += "en d&iacute;as de tratamiento ";
				msg += "para el antibi&oacute;tico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>";
				jAlert( msg, "ALERTA" );
				
				valido = false;
			}
		}
	}

	var profilaxis = $( "#wprofilaxis"+tipoProtocolo+idxElemento )[0].checked ? 'on':'off';
	var tratamiento = $( "#wtratamiento"+tipoProtocolo+idxElemento )[0].checked ? 'on':'off';
	
	var firma = $("#pswFirma").val();
	
	var conMedicamento1 = 0;
	var conMedicamento2 = 0;
	var conInsumo1 = 0;
	var conInsumo2 = 0;
	var esPediatrico = $( '#wespediatrico'+tipoProtocolo+idxElemento ).val();
	
	if( esPediatrico == 'on' ){
		
		conMedicamento1 = $( '#wconmed1'+tipoProtocolo+idxElemento ).val();
		conMedicamento2 = $( '#wconmed2'+tipoProtocolo+idxElemento ).val();
		
		if( conMedicamento1 && conMedicamento2 ){
			
			if( conMedicamento1*1 > 0 && conMedicamento2*1 > 0 ){
				
				var con = conMedicamento1/conMedicamento2;
				conInsumo1 = Math.round( cantDosis.value*1000 )/1000;
				conInsumo2 = Math.round( cantDosis.value/con*1000 )/1000;
				var dosisNueva = cantDosis.value*1+cantDosis.value/con;
				dosisNueva = Math.round( dosisNueva*1000 )/1000;
				
				cantDosis.value = dosisNueva;
			}
		}
	}
	else{
		
		conMedicamento1 = $( '#wconmed1'+tipoProtocolo+idxElemento ).val();
		conMedicamento2 = $( '#wconmed2'+tipoProtocolo+idxElemento ).val();
		
		if( conMedicamento1 && conMedicamento2 ){
			
			if( conMedicamento1*1 > 0 && conMedicamento2*1 > 0 ){
				
				var con = conMedicamento1/(conMedicamento1*1+conMedicamento2*1);
				conInsumo1 = Math.round( cantDosis.value*con*1000 )/1000;
				conInsumo2 = Math.round( cantDosis.value-conInsumo1 )/1000;
			}
		}
	}
	
	var porProtocolo = $( "#wporprotocolo"+tipoProtocolo+idxElemento ).val();
	
	
	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){		
		grabarArticuloElemento(historia,ingreso,fechaKardex,codigoArticulo,cantDosis.value,unidadDosis.value,periodicidad.value,formaFtica.value,fechaInicio,horaInicio,via.value,artConfirmado,diasTto.value,obsmerge,origenArticulo,usuario,condicion.value,dosMax,cantGrabar,unidadManejo,cantidadManejo,primerKardex,equivHorasFrecuencia,fechaInicioAnterior,horaInicioAnterior,artNoDispensar,artProtocolo,centroCostosGrabacion,prioridad,idxElemento,nombreArticulo,cantidadAlta,impresion,deAlta, firma, artdosisAdaptada,idoriginal,artnoEsteril,profilaxis,tratamiento,esPediatrico,conInsumo1,conInsumo2,porProtocolo,wdrautorizado,wjusparaautorizar);
	}
	return valido;
}


 /*****************************************************************************************************************************
  * Validación de la grabación de un medicamento para evitar validacion del dia actual contra inicio del tratamiento
  ******************************************************************************************************************************/
function grabarArticuloSinValidacion(idxElemento,tipoProtocolo){

	// Debido que para la pestaña de alta se usaron campos que estan en otras pestañas
	// Se crea esta función que determina cual cambio y graba según este cambio
	// Si cambian los dos prevalece el valor del campo de la pestaña original, no la de alta
	unificarCamposArticulos(idxElemento,tipoProtocolo);

	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fechaKardex = document.forms.forma.wfechagrabacion.value;
	var centroCostosGrabacion = document.forms.forma.centroCostosGrabacion.value;
	var prioridad = document.forms.forma.whusuariolactario.value;
	var articulosGrabadoAnteriormente = false;

	// tipoProtocolo = "N";

	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);
	var datosArticulos = "";
	var cantDosis = document.getElementById('wdosis'+tipoProtocolo+idxElemento);
	var unidadDosis = document.getElementById('wudosis'+tipoProtocolo+idxElemento);
	var periodicidad = document.getElementById('wperiod'+tipoProtocolo+idxElemento);

	var equivHorasFrecuencia = periodicidad.value;
	var condicion = document.getElementById('wcondicion'+tipoProtocolo+idxElemento);
	var formaFtica = document.getElementById('wfftica'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var horaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);
	var observacion = document.getElementById('wtxtobsori'+tipoProtocolo+idxElemento);
	var usuario = $("#usuario").val();
	var usuariodes = $("#usuariodes").val();
	var dosisAdaptada = document.getElementById('wchkDA'+tipoProtocolo+idxElemento);
	var noEsteril = document.getElementById('wchkNE'+tipoProtocolo+idxElemento);

	var idoriginal = document.getElementById('widoriginal'+tipoProtocolo+idxElemento);
	idoriginal = idoriginal ? idoriginal.value : 0;

	artdosisAdaptada = false;

	if(dosisAdaptada !== null)	{
		if(dosisAdaptada.checked){
			artdosisAdaptada = true;
		}
	}

	artnoEsteril = false;

	if(noEsteril !== null)	{
		if(noEsteril.checked){
			artnoEsteril = true;
		}
	}

	var impresion = 'off';
	var deAlta = 'off';
	if(isset(document.getElementById('wimp'+tipoProtocolo+idxElemento)))
	{
		var impresion = document.getElementById('wimp'+tipoProtocolo+idxElemento).value;
		var deAlta = document.getElementById('wimp'+tipoProtocolo+idxElemento).value;
	}

	if(isset(document.getElementById('wchkimp_alta'+tipoProtocolo+idxElemento)))
	{
		if(document.getElementById('wchkimp_alta'+tipoProtocolo+idxElemento).checked==true)
			var impresion = 'on';
		else
			var impresion = 'off';
	}


	if(isset(document.getElementById('wchkimp_art'+tipoProtocolo+idxElemento)))
	{
		var impresion = document.getElementById('wchkimp_art'+tipoProtocolo+idxElemento).checked ? 'on' : 'off';
		//var deAlta = document.getElementById('wchkimp'+tipoProtocolo+idxElemento).value;
	}


	//Via de administracion
	if(isset(document.getElementById('wviadmon'+tipoProtocolo+idxElemento)))
		var via = document.getElementById('wviadmon'+tipoProtocolo+idxElemento);
	else if(isset(document.getElementById('wviadmonimp'+tipoProtocolo+idxElemento)))
		var via = document.getElementById('wviadmonimp'+tipoProtocolo+idxElemento);
	else
		var via = '';

	var confirmar = document.getElementById('wchkconf'+tipoProtocolo+idxElemento);
	var diasTto = document.getElementById('wdiastto'+tipoProtocolo+idxElemento);

	if(deAlta!="on")
		var dosMax = document.getElementById('wdosmax'+tipoProtocolo+idxElemento).value;
	else
		var dosMax = "";

	var primerKardex = (document.getElementById("wkardexnuevo") && document.getElementById("wkardexnuevo").value == 'S' ? "S" : "N");

	

	if( isset(document.getElementById('wcantaltaimp'+tipoProtocolo+idxElemento)) )
		var cantidadAlta = document.getElementById('wcantaltaimp'+tipoProtocolo+idxElemento).value;
	else if(deAlta=="on" && document.getElementById('wdosmax'+tipoProtocolo+idxElemento)!="")
		var cantidadAlta = document.getElementById('wdosmax'+tipoProtocolo+idxElemento).value;
	else
		var cantidadAlta = "";

	var unidadManejo = document.getElementById('wcundmanejo'+tipoProtocolo+idxElemento).value;
	var vence = (document.getElementById('whvence'+tipoProtocolo+idxElemento).value == 'on' ? true : false);
	var cantFracciones = document.getElementById('whcmanejo'+tipoProtocolo+idxElemento).value;
	//var artProtocolo = document.getElementById('whartpro'+tipoProtocolo+idxElemento).value;
	var artProtocolo = tipoProtocolo;
	var noDispensar = document.getElementById('wchkdisp'+tipoProtocolo+idxElemento);

	var cantGrabar = 0;
	var cantidadManejo = 1;		//Este es el valor base de dispensacion
	var dosisSuministrar = 0;

	// equivHorasFrecuencia = parseFloat(equivHorasFrecuencia);

	//Extraccion de la fecha y hora inicial de administracion
	var vecFechaAnt = document.getElementById("whfinicio"+tipoProtocolo+idxElemento).value.split("a las:");
	var fechaInicioAnterior = vecFechaAnt[0];
	var horaInicioAnterior = vecFechaAnt[1];


	//Conversion a hora entero para la hora
	var vecHora = horaInicioAnterior.split(":");
	var intHoraInicio = vecHora[0];

	//Extraccion del codigo articulo propiamente dicho
	var cd = "";
	if(codigoArticulo.tagName == 'INPUT'){
		datosArticulos = codigoArticulo.value;
		cd = codigoArticulo.value.split("-");
	} else {
		articulosGrabadoAnteriormente = true;	//Si es div fue grabado
		datosArticulos = codigoArticulo.innerHTML;
		cd = codigoArticulo.innerHTML.split("-");
	}

	codigoArticulo = cd[0];
	var origenArticulo = cd[1];
	var nombreArticulo = cd[2];

	//Extraccion de la fecha y hora inicial de administracion
	var fh = fechaInicio.value.split("a las:");
	fechaInicio = fh[0];
	horaInicio = fh[1];

	/*****
	VALIDACION DE CAMPOS OBLIGATORIOS
	******/
	var valido = true;

	//Codigo articulo
	if(!codigoArticulo || codigoArticulo == ''){
		alert('Debe ingresar el codigo del articulo: '+datosArticulos);
		valido = false;
	}

	//Dosis
	if(valido && (!cantDosis || cantDosis.value == '')){
		alert('Debe ingresar la cantidad de dosis a suministrar: '+ datosArticulos);
		valido = false;
	}

	if(valido && eval(cantDosis.value) <= 0){
		alert('La cantidad de dosis debe ser mayor que cero: '+ datosArticulos);
		valido = false;
	}

	//Unidad de Dosis
	if(valido && (!unidadDosis || unidadDosis.value == '')){
		alert('Debe seleccionar la unidad de dosis a suministrar: '+ datosArticulos);
		valido = false;
	}

	//Periodicidad o frecuencia
	if(valido && (!periodicidad || periodicidad.value == '')){
		alert('Debe seleccionar la frecuencia con la que se debe suministrar: '+ datosArticulos);
		valido = false;
	}

	//Forma farmaceutica
	if(valido && (!formaFtica || formaFtica.value == '')){
		formaFtica.value = '00';
//		alert('Debe ingresar la forma farmaceutica');
//		valido = false;
	}

	//Fecha de inicio del tratamiento
	if(valido && (!fechaInicio || fechaInicio == '')){
		alert('Debe ingresar la fecha de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}

	//Hora de inicio del tratamiento
	if(valido && (!horaInicio || horaInicio == '')){
		alert('Debe ingresar la hora de inicio del tratamiento: '+ datosArticulos);
		valido = false;
	}

	//Via de administracion
	if(deAlta!='on' )
	{
		if(valido && (!via || via.value == '')){
			alert('Debe seleccionar la vía de administración: '+ datosArticulos);
			valido = false;
		}
	}

	//Checkbuttons
	var artConfirmado = false;
	var artNoDispensar = false;

	if(confirmar && confirmar.checked){
		artConfirmado = true;
	}

	if(noDispensar && noDispensar.checked){
		artNoDispensar = true;
	}

	//Validacion de que no se encuentre un mismo articulo con la misma fecha y hora de inicio
	var datosArticulo = "";
	var codigoValidacionArticulo = "";
	var fechaHoraValidacionArticulo = "";

	if( !$( "[idtr=trFil"+tipoProtocolo+idxElemento+"]" ).hasClass( "suspendido" ) ){
		for(var indiceArticulo=0;document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo);indiceArticulo++){
			if(indiceArticulo != idxElemento){
				if( !$( "[idtr=trFil"+tipoProtocolo+indiceArticulo+"]" ).hasClass( "suspendido" ) ){
					if(document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).tagName == 'DIV'){
						datosArticulo = document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).innerHTML;
					} else {
						datosArticulo = document.getElementById('wnmmed'+tipoProtocolo+indiceArticulo).value;
					}
					codigoValidacionArticulo = datosArticulo.split("-");
					fechaHoraValidacionArticulo = document.getElementById('whfinicio'+tipoProtocolo+indiceArticulo) ? document.getElementById('whfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:") : document.getElementById('wfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:");
					fechaHoraValidacionArticulo2 = document.getElementById('wfinicio'+tipoProtocolo+indiceArticulo).value.split("a las:");	//Abril 26 de 2011

					// if( codigoArticulo != 'IC0000' && codigoArticulo != 'LQ0000' ){
						// if(codigoValidacionArticulo[0] == codigoArticulo && fechaHoraValidacionArticulo[0] == fechaInicioAnterior && fechaHoraValidacionArticulo[1].split(":")[0] == horaInicioAnterior.split(":")[0]){
							// alert("El articulo "+trim(datosArticulo)+" ya se encuentra en la lista con la misma fecha y hora de inicio, NO SE PUEDE GRABAR");
							// valido = false;
							// break;
						// }
						// /************************************************************************
						 // * Abril 26 de 2011
						 // * fechaInicio = fh[0];
							// horaInicio = fh[1];
						 // ************************************************************************/
						// else if(codigoValidacionArticulo[0] == codigoArticulo && fechaHoraValidacionArticulo2[0] == fechaInicio && fechaHoraValidacionArticulo2[1].split(":")[0] == horaInicio.split(":")[0]){
							// alert("El articulo "+trim(datosArticulo)+" ya se encuentra en la lista con la misma fecha y hora de inicio, NO SE PUEDE GRABAR");
							// valido = false;
							// break;
						// }
						// /************************************************************************/
					// }
				}
			}
		}
	}

	var f=new Date();
	var fecha = f.getFullYear()+'-'+(f.getMonth()*1+1)*1+'-'+f.getDate()+' '+f.getHours()+':'+f.getMinutes();
	usuariodes = ucWords($("#usuariodes").val());


	var observacionadd = "";
	var nuevaobservacion = "";
	var obsmerge = "";
	// var observaciontxt = observacion.value;


	if(observacion == null)
	{
		var observaciontxt = document.getElementById('wtxtobs'+tipoProtocolo+idxElemento).value;
	}
	else
	{
		var observaciontxt = observacion.value;
	}
	observaciontxt = $.trim(observaciontxt);


	var claseobs = "";

	var busqueda = observaciontxt.match(/Origen:/g);
	if(busqueda)
		var numfilas = busqueda.length;
	else
		var numfilas = 0;

	if(numfilas%2 == 0)
		claseobs = "fila2";
	else
		claseobs = "fila1";

	if($('#wtxtobsadd'+tipoProtocolo+idxElemento).val() != '')
	{
		observacionadd = document.getElementById('wtxtobsadd'+tipoProtocolo+idxElemento).value;
		if(observacionadd!="" && observacionadd!=" ")
		{
			nuevaobservacion = observacionadd+'<br><span style="font-size:7pt;">'+usuario+' - '+usuariodes+'<br>Origen:Ordenes | '+fecha+'</span>';
		}
		obsmerge = '<div class="'+claseobs+'" style="border-bottom:4px #fff solid;">'+nuevaobservacion+'</div>'+observaciontxt;
	}
	else
	{
		if(observaciontxt!="" && observaciontxt!=" ")
			obsmerge = '<div class="'+claseobs+'" style="border-bottom:4px #fff solid;">'+observaciontxt+'<br><span style="font-size:7pt;">'+usuario+' - '+usuariodes+'<br>Origen:Ordenes | '+fecha+'</span></div>';
	}
	
	//Si tiene permisos para dosis máxima
	var perDmax = $( "#waccN\\.13" ).val().split( "," )[0] == 'S' ? true: false;
	
	if( !articulosGrabadoAnteriormente && valido && $( "#wesantibiotico"+tipoProtocolo+idxElemento ).val() == "on" ){
		if( !$( "#wprofilaxis"+tipoProtocolo+idxElemento )[0].checked && !$( "#wtratamiento"+tipoProtocolo+idxElemento )[0].checked ){
			/*if( $( "#wdiastto"+tipoProtocolo+idxElemento ).val()*1 == 0  && $( "#wdosmax"+tipoProtocolo+idxElemento ).val()*1 == 0 )
				jAlert( "Debe seleccionar si el antibiotico es por <b>PROFILAXIS</b> o <b>TRATAMIENTO</b> y <br>Debe ingresar un valor mayor a 0 en d&iacute;as de tratamiento o dosis m&aacute;xima<br>para el antibiótico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>", "ALERTA" );
			else
				jAlert( "Debe seleccionar si el antibiotico es por <b>PROFILAXIS</b> o <b>TRATAMIENTO</b> para el antibiótico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>", "ALERTA" );*/
				
			
			
			var msg = "Debe seleccionar si el antibiotico es por <b>PROFILAXIS</b> o <b>TRATAMIENTO</b> ";
			if( $( "#wdiastto"+tipoProtocolo+idxElemento ).val()*1 == 0  && $( "#wdosmax"+tipoProtocolo+idxElemento ).val()*1 == 0 ){
				//Si permite dosis máxima
				if( perDmax )
					msg += " y <br>Debe ingresar un valor mayor a 0 en d&iacute;as de tratamiento o dosis m&aacute;xima<br>";
				else
					msg += " y <br>Debe ingresar un valor mayor a 0 en d&iacute;as de tratamiento<br>";
			}
			msg += "para el antibiótico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>";
			
			jAlert( msg, "ALERTA" );
			valido = false;
		}
	}
	
	if( !articulosGrabadoAnteriormente && valido && $( "#wesantibiotico"+tipoProtocolo+idxElemento ).val() == "on" ){
		if( !$( "#wprofilaxis"+tipoProtocolo+idxElemento )[0].disabled && !$( "#wtratamiento"+tipoProtocolo+idxElemento )[0].disabled ){
			if( ( $( "#wtratamiento"+tipoProtocolo+idxElemento )[0].checked || $( "#wprofilaxis"+tipoProtocolo+idxElemento )[0].checked ) && $( "#wdiastto"+tipoProtocolo+idxElemento ).val()*1 == 0  && $( "#wdosmax"+tipoProtocolo+idxElemento ).val()*1 == 0 ){
				// jAlert( "Debe ingresar un valor mayor a 0 en d&iacute;as de tratamiento o dosis m&aacute;xima para el antibi&oacute;tico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>", "ALERTA" );
				var msg = "Debe ingresar un valor mayor a 0 ";
				//Si permite dosis máxima
				if(perDmax)
					msg += "en d&iacute;as de tratamiento o dosis m&aacute;xima ";
				else
					msg += "en d&iacute;as de tratamiento ";
				msg += "para el antibi&oacute;tico <b>"+codigoArticulo+"-"+nombreArticulo+"</b>";
				jAlert( msg, "ALERTA" );
				
				valido = false;
			}
		}
	}
	
	var profilaxis = $( "#wprofilaxis"+tipoProtocolo+idxElemento )[0].checked ? 'on':'off';
	var tratamiento = $( "#wtratamiento"+tipoProtocolo+idxElemento )[0].checked ? 'on':'off';
	
	
	var conMedicamento1 = 0;
	var conMedicamento2 = 0;
	var conInsumo1 = 0;
	var conInsumo2 = 0;
	var esPediatrico = $( '#wespediatrico'+tipoProtocolo+idxElemento ).val();
	
	if( esPediatrico == 'on' ){
		
		conMedicamento1 = $( '#wconmed1'+tipoProtocolo+idxElemento ).val();
		conMedicamento2 = $( '#wconmed2'+tipoProtocolo+idxElemento ).val();
		
		if( conMedicamento1 && conMedicamento2 ){
			
			if( conMedicamento1*1 > 0 && conMedicamento2*1 > 0 ){
				
				var con = conMedicamento1/conMedicamento2;
				conInsumo1 = Math.round( cantDosis.value*1000 )/1000;
				conInsumo2 = Math.round( cantDosis.value/con*1000 )/1000;
				var dosisNueva = cantDosis.value*1+cantDosis.value/con;
				dosisNueva = Math.round( dosisNueva*1000 )/1000;
				
				cantDosis.value = dosisNueva;
			}
		}
	}
	else{
		
		conMedicamento1 = $( '#wconmed1'+tipoProtocolo+idxElemento ).val();
		conMedicamento2 = $( '#wconmed2'+tipoProtocolo+idxElemento ).val();
		
		if( conMedicamento1 && conMedicamento2 ){
			
			if( conMedicamento1*1 > 0 && conMedicamento2*1 > 0 ){
				
				var con = conMedicamento1/(conMedicamento1*1+conMedicamento2*1);
				conInsumo1 = Math.round( cantDosis.value*con*1000 )/1000;
				conInsumo2 = Math.round( cantDosis.value-conInsumo1 )/1000;
			}
		}
	}

	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		grabarArticuloElemento(historia,ingreso,fechaKardex,codigoArticulo,cantDosis.value,unidadDosis.value,periodicidad.value,formaFtica.value,fechaInicio,horaInicio,via.value,artConfirmado,diasTto.value,obsmerge,origenArticulo,usuario,condicion.value,dosMax,cantGrabar,unidadManejo,cantidadManejo,primerKardex,equivHorasFrecuencia,fechaInicioAnterior,horaInicioAnterior,artNoDispensar,artProtocolo,centroCostosGrabacion,prioridad,idxElemento,nombreArticulo,cantidadAlta,impresion,deAlta,'',artdosisAdaptada,idoriginal,artnoEsteril,profilaxis,tratamiento,esPediatrico,conInsumo1,conInsumo2);
	}
	return valido;
}
 /*****************************************************************************************************************************
  * Validación de la grabación de un medicamento unicamente para uso del perfil
  ******************************************************************************************************************************/
 function grabarArticuloPerfil(idxElemento){
 	//Variables de la fila a actualizar
 	var historia = document.forms.forma.whistoria.value;
 	var ingreso = document.forms.forma.wingreso.value;
 	var fechaKardex = document.forms.forma.wfechagrabacion.value;

 	var codigoArticulo = document.getElementById('wnmmedact'+idxElemento);
 	var codigoArticuloNuevo = document.getElementById('wnmmed'+idxElemento);

 	var diasTto = document.getElementById('wdiastto'+idxElemento);
 	var dosisMaximas = document.getElementById('wdosmax'+idxElemento);
 	var observacion = document.getElementById('wtxtobsori'+tipoProtocolo+idxElemento);
 	var via = document.getElementById('wviadmon'+idxElemento);

 	var fechaInicio = document.getElementById('whfinicio'+idxElemento);
 	var prioridad = (document.getElementById('wchkpri'+idxElemento) && document.getElementById('wchkpri'+idxElemento).checked == true) ? "on" : "off";

 	var usuario = $('#usuario').val();

 	var unidadDosis = document.getElementById('wudosis'+idxElemento).value;
 	var formaFarm = document.getElementById('wfftica'+idxElemento).value;
 	var autorizadoCtc = document.getElementById('wautctc'+idxElemento);

 	var origenArticuloNuevo = "";

 	if(autorizadoCtc){
 		autorizadoCtc = autorizadoCtc.value;
 	} else {
 		autorizadoCtc = "";
 	}

 	//Extrae el codigo del articulo actual
 	var cd = "";
 	if(codigoArticulo.tagName == 'INPUT'){
 		cd = codigoArticulo.value.split("-");
 	} else {
 		cd = codigoArticulo.innerHTML.split("-");
 	}

 	codigoArticulo = cd[0];
 	var origenArticulo = cd[1];
 	var nombreArticulo = cd[2];

 	//Extraccion de la fecha y hora inicial de administracion
 	var fh = fechaInicio.value.split("a las:");
 	var fechaInicio = fh[0];
 	var horaInicio = fh[1];

 	//Extrae el codigo del articulo nuevo
 	var cdNuevo = "";
 	if(codigoArticuloNuevo){
 		if(codigoArticuloNuevo.tagName == 'INPUT'){
 			cdNuevo = codigoArticuloNuevo.value.split("-");
 		} else {
 			cdNuevo = codigoArticuloNuevo.innerHTML.split("-");
 		}
 		codigoArticuloNuevo = cdNuevo[0];
 		origenArticuloNuevo = cdNuevo[1];
 	}

 	/*****
 	VALIDACION DE CAMPOS OBLIGATORIOS
 	******/
 	var valido = true;

 	//Codigo articulo actual
 	if(!codigoArticulo || codigoArticulo == ''){
 		alert('No se capturó el codigo del articulo actual');
 		valido = false;
 	}

 	//Si se va a reemplazar el articulo se debe avisar
	if(valido && (codigoArticuloNuevo && codigoArticuloNuevo != '')){
		if(confirm('Esta seguro de reemplazar el artículo ' + codigoArticulo+'-'+cd[2]+ ' con ' + document.getElementById('wnmmed'+idxElemento).value + '?')){
			//Valido que el articulo que voy a reemplazar no este en la lista de articulos actual

			var temp = "";
			var cont1 = 1;
			var elemento = document.getElementById("wnmmedact"+cont1);

			while(elemento){

				if(elemento && elemento.innerHTML != ''){
					temp = elemento.innerHTML.split("-");
					/*
					if(temp[0] == codigoArticuloNuevo){
						alert("No se puede reemplazar el articulo " + cd[2] + " por el articulo " + cdNuevo[2] + " este ya se encuentra en la lista de artículos.");
						valido = false;
						break;
					}
					*/
				}
				cont1++;
				elemento = document.getElementById("wnmmedact"+cont1);
			}

		} else {
			valido = false;
		}
	}

	var f=new Date();
	var fecha = f.getFullYear()+'-'+(f.getMonth()*1+1)*1+'-'+f.getDate()+' '+f.getHours()+':'+f.getMinutes();
	usuariodes = ucWords($("#usuariodes").val());

	var observacionadd = "";
	var nuevaobservacion = "";
	var obsmerge = "";
	var observaciontxt = observacion.value;
	var claseobs = "";

	var busqueda = observaciontxt.match(/Origen:/g);
	if(busqueda)
		var numfilas = busqueda.length;
	else
		var numfilas = 0;

	if(numfilas%2 == 0)
		claseobs = "fila2";
	else
		claseobs = "fila1";

	if(document.getElementById('wtxtobsadd'+idxElemento))
	{
		observacionadd = document.getElementById('wtxtobsadd'+idxElemento).value;
		if(observacionadd!="" && observacionadd!=" ")
		{
			nuevaobservacion = observacionadd+'<br><span style="font-size:7pt;">'+usuario+' - '+usuariodes+'<br>Origen:Ordenes | '+fecha+'</span>';
		}
		obsmerge = '<div class="'+claseobs+'" style="border-bottom:4px #fff solid;">'+nuevaobservacion+'</div>'+observaciontxt;
	}
	else
	{
		if(observaciontxt!="" && observaciontxt!=" ")
			obsmerge = '<div class="'+claseobs+'" style="border-bottom:4px #fff solid;">'+observaciontxt+'<br><span style="font-size:7pt;">'+usuario+' - '+usuariodes+'<br>Origen:Ordenes | '+fecha+'</span></div>';
	}

 	/***
 	GRABACION DEL ARTICULO
 	***/
 	if(valido){
 		grabarArticuloPerfilElemento(historia,ingreso,fechaKardex,codigoArticulo,codigoArticuloNuevo,diasTto.value,obsmerge,usuario,idxElemento,unidadDosis,formaFarm,origenArticuloNuevo,via.value,dosisMaximas.value,prioridad,fechaInicio,horaInicio,autorizadoCtc);
 	}
 }
/*****************************************************************************************************************************
 * Validación para realizar la grabación de un examen
 ******************************************************************************************************************************/
function grabarExamen(idxElemento, grbAut ){

	if( grbAut == undefined )
		grbAut = true;

	//Variables de la fila a guardar o actualizar
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfechagrabacion.value;
	var impExamen = "off";
	var prefijoImp = 'imp';

	if(isset(document.getElementById('wexamenalta'+idxElemento)) || document.getElementById('wexamenalta'+idxElemento))
		var altExamen = document.getElementById('wexamenalta'+idxElemento).value;
	else
		var altExamen = "off";

	var datosAdicionales = "[]";
	var datoOrdenAnexa = "";
	var esOfertado = "";
	var usuarioTomaMuestra = "";

	if(altExamen!="on")
	{
		if(isset(document.getElementById('hexcco'+idxElemento)) && document.getElementById('hexcco'+idxElemento))
		{

			// Permite unificar el valor de los campos de la pestaña Alta y la pestaña Otras Ordenes
			unificarCamposExamenes(idxElemento);

			var claseImprimir = $('#imgImprimir'+idxElemento).attr('class');
			
			if(claseImprimir == 'opacar aclarar' || claseImprimir == 'aclarar')
				var impExamen = "on";

			if(isset(document.getElementById('wchkimpexamen'+idxElemento)) && document.getElementById('wchkimpexamen'+idxElemento).checked)
				var impExamen = "on";

			var nomExamen = document.getElementById('wnmexamen'+idxElemento).value;
			var observaciones = ""; //document.getElementById('wtxtobsexamen'+idxElemento).value;
			var justificacion = document.getElementById('wtxtjustexamen'+idxElemento).value;
			var estadoExamen = "P";
			if(document.getElementById('westadoexamen'+idxElemento)){
				estadoExamen = document.getElementById('westadoexamen'+idxElemento).value;
			}

			try{
				//Busco si el examen está chequeado o no
				if( $( "#imprimir_examen",document.getElementById('wtxtjustexamen'+idxElemento).parentNode.parentNode )[0].checked ){
					var impExamen = "on";
				}
				else{
					var impExamen = "off";
				}
			}
			catch(e){
				var impExamen = "on";
				return;
			}


			var fechaDeSolicitado = document.getElementById('wfsol'+idxElemento).value;
			//var horaDeSolicitado = document.getElementById('whsol'+idxElemento).value;
			var usuario = document.forms.forma.usuario.value;
			var existe = false;

		//	var vecIdExamen = document.getElementById('wexamenlab'+idxElemento).value.split("|");
			var codExamen = document.getElementById('hexcco'+idxElemento).value;
			var consExamen = document.getElementById('hexcod'+idxElemento).value;
			var cod_procedi = consExamen; //Codigo del procedimiento
			var consecutivoOrden = document.getElementById('hexcons'+idxElemento).value;
			var observacionesOrden = ""; //document.getElementById("wtxtobsexamen"+codExamen+""+consecutivoOrden).value;
			var numeroItem = document.getElementById("hexnroitem"+idxElemento).value;
			
			try{
				datosAdicionales = document.getElementById("hiDatosAdicionales"+idxElemento).value;
				datoOrdenAnexa = document.getElementById("hiOrdenAnexa"+idxElemento).value;
				esOfertado = document.getElementById("hiEsOfertado"+idxElemento).value;
			}
			catch(e){}
			
			try{
				usuarioTomaMuestra = document.getElementById("wusuariotomamuestra"+idxElemento).checked ? usuario : '';
				console.log( usuarioTomaMuestra );
			}
			catch(e){
				usuarioTomaMuestra = '';
			}

			// 2012-06-27
			// Se verifica primero si existe el campo para que la variable sea asignada correctamente
			if(isset(document.getElementById("hiReqJus"+idxElemento)))
				var reqJus = document.getElementById("hiReqJus"+idxElemento).value;
			else
				var reqJus = "";
			//	var observacionesOrden = "";
			var firma = document.getElementById("whfirma").value;


			/*****
			VALIDACION DE CAMPOS OBLIGATORIOS
			******/
			var valido = true;

			if(codExamen == ''){
				alert("Debe especificar un examen");
				valido = false;
			}

			/*
			if(document.getElementById('wnmexamen'+idxElemento).type == "text" && observacionesOrden == ""){
				alert("Para grabar la orden por primera vez, debe especificar observaciones");
				valido = false;
			}
			*/

			//	if( justificacion == "" && codExamen != "2251" && codExamen != "3081" ){
			if( (justificacion == "" || justificacion == " " || justificacion == "." ) && reqJus == "on" ){
				alert( "Especificar una justificación para la ayuda o procedimiento: "+nomExamen );
				valido = false;
			}
		}
		else
		{
			return true;
		}
	}
	else
	{
		if(isset(document.getElementById('hexccoimp'+idxElemento)) && document.getElementById('hexccoimp'+idxElemento))
		{
			// Permite unificar el valor de los campos de la pestaña Alta y la pestaña Otras Ordenes
			unificarCamposExamenes(idxElemento);

			var claseImprimir = $('#imgImprimir'+idxElemento).attr('class');

			if(claseImprimir == 'opacar aclarar' || claseImprimir == 'aclarar')
				var impExamen = "on";

			if(isset(document.getElementById('wchkimpexamen'+idxElemento)) && document.getElementById('wchkimpexamen'+idxElemento).checked)
				var impExamen = "on";

			var nomExamen = document.getElementById('wnmexamenimp'+idxElemento).value;
			var observaciones = ""; //document.getElementById('wtxtobsexamen'+idxElemento).value;
			var justificacion = document.getElementById('wtxtjustexamenimp'+idxElemento).value;
			var estadoExamen = "P";
			if(document.getElementById('westadoexamen'+idxElemento)){
				estadoExamen = document.getElementById('westadoexamenimp'+idxElemento).value;
			}

			var fechaDeSolicitado = document.getElementById('wfsol'+idxElemento).value;
			//var horaDeSolicitado = document.getElementById('whsol'+idxElemento).value;
			var usuario = document.forms.forma.usuario.value;
			var existe = false;

		//	var vecIdExamen = document.getElementById('wexamenlab'+idxElemento).value.split("|");
			var codExamen = document.getElementById('hexccoimp'+idxElemento).value;
			var consExamen = document.getElementById('hexcodimp'+idxElemento).value;
			var cod_procedi = consExamen
			var consecutivoOrden = document.getElementById('hexconsimp'+idxElemento).value;
			var observacionesOrden = ""; //document.getElementById("wtxtobsexamen"+codExamen+""+consecutivoOrden).value;
			var numeroItem = document.getElementById("hexnroitemimp"+idxElemento).value;

			// 2012-06-27
			// Se verifica primero si existe el campo para que la variable sea asignada correctamente
			if(isset(document.getElementById("hiReqJus"+idxElemento)))
				var reqJus = document.getElementById("hiReqJus"+idxElemento).value;
			else
				var reqJus = "";
			//	var observacionesOrden = "";
			var firma = document.getElementById("whfirma").value;

			/*****
			VALIDACION DE CAMPOS OBLIGATORIOS
			******/
			var valido = true;

			if(codExamen == ''){
				alert("Debe especificar un examen");
				valido = false;
			}

			/*
			if(document.getElementById('wnmexamen'+idxElemento).type == "text" && observacionesOrden == ""){
				alert("Para grabar la orden por primera vez, debe especificar observaciones");
				valido = false;
			}
			*/

			//	if( justificacion == "" && codExamen != "2251" && codExamen != "3081" ){
			if( (justificacion == "" || justificacion == " " || justificacion == "." ) && reqJus == "on" ){
				alert( "Especificar una justificación para la ayuda o procedimiento: "+nomExamen );
				valido = false;
			}

		}
		else
		{
			return true;
		}
	}

	var firmHCE = $( "#hiFormHce" + idxElemento ).val();

	/***
	GRABACION DEL ARTICULO
	***/
	if(valido){
		if(!existe){
			grabarExamenElemento(codExamen,nomExamen,historia,ingreso,fecha,observaciones,estadoExamen,fechaDeSolicitado,usuario,consecutivoOrden,firma,observacionesOrden,cod_procedi,justificacion,idxElemento,numeroItem,impExamen,firmHCE,altExamen,grbAut,datosAdicionales,datoOrdenAnexa,esOfertado,usuarioTomaMuestra);
		} else {
			alert('El examen ya se encuentra en la lista.  Por favor seleccione otro');
		}
	}

	return valido;
}


 /*****************************************************************************************************************************
 * ----------OPERACIONES DE SELECCION Y CONSULTA-----------
 ******************************************************************************************************************************/
function consultarMedicamento(){
	var contenedor = document.getElementById('cntMedicamento');
	var tipoMedida = document.getElementById("wunidadmed");

	var tipoArticulo = "";
	var tipoProtocolo = "";

	var ccostos = '',grupos = '';

	if(document.forms.forma.centroCostosUsuario){
		ccostos = document.forms.forma.centroCostosUsuario.value;
	}

	if(document.forms.forma.whgrupos){
		grupos = document.forms.forma.whgrupos.value;
	}

	var parametros = "";

 	var i = 0;
 	//Nombre (C)omercial o (G)enerico
   	for (i=0;i<document.forms.forma.wtipoart.length;i++){
    	if (document.forms.forma.wtipoart[i].checked){
    		tipoArticulo = document.forms.forma.wtipoart[i].value;
      		break;
    	}
    }

   	for (i=0;i<document.forms.forma.wtipoprot.length;i++){
    	if (document.forms.forma.wtipoprot[i].checked){
    		tipoProtocolo = document.forms.forma.wtipoprot[i].value;
      		break;
    	}
    }

   	//Abril 25 de 2011
   	if(  document.forms.forma.wservicio ){
   		var ccoPaciente = document.forms.forma.wservicio.value;
   	}
   	else{
   		var ccoPaciente = '';
   	}

	/*Tipos de protocolos
	 * N:  Normal, el articulo se consulta normalmente.  (maestro de la central y maestro del servicio)
	 * A:  Analgesia, el articulo se agrega a los protocolos de analgesia (tipo especial de articulo A)
	 * U:  nUtricion, el articulo se agrega a los protocolos de nutricion (tipo especial de articulos U)
	 * Q:  Quimioterapia, el articulo se agrega a los protocolos de quimioterapia (tipo especial de articulos Q)
	 */
//	alert(tipoProtocolo);
	if(document.forms.forma.wnommed.value == ''){
		parametros = "consultaAjaxKardex=02&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo="+document.forms.forma.wcodmed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos+"&tipoProtocolo="+tipoProtocolo+"&ccoPaciente="+ccoPaciente;
	} else {
		parametros = "consultaAjaxKardex=03&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre="+document.forms.forma.wnommed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos+"&tipoProtocolo="+tipoProtocolo+"&ccoPaciente="+ccoPaciente;
	}

//	alert(parametros);

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				contenedor.innerHTML=$.trim( ajax.responseText );
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

function consultarMedicamentoPerfil(){
	var contenedor = document.getElementById('cntMedicamento');
	var imagen = document.getElementById('imgCodMed');

	var ccostos = '',grupos = '*';

	if(document.forms.forma.centroCostosUsuario){
		ccostos = document.forms.forma.centroCostosUsuario.value;
	}

	var parametros = "";

	imagen.style.display = "block";

 	var i = 0;
   	for (i=0;i<document.forms.forma.wtipoart.length;i++){
    	if (document.forms.forma.wtipoart[i].checked)
      		break;
    }

    var tipoArticulo = document.forms.forma.wtipoart[i].value;
	var tipoMedida = document.getElementById("wunidadmed");

	if(document.forms.forma.wnommed.value == ''){
		parametros = "consultaAjaxKardex=02&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo="+document.forms.forma.wcodmed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos;
	} else {
		parametros = "consultaAjaxKardex=03&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre="+document.forms.forma.wnommed.value.replace(/%/, "-")+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value+"&centroCostos="+ccostos+"&gruposMedicamentos="+grupos;
	}

//	alert(parametros);

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				contenedor.innerHTML=$.trim( ajax.responseText );
				imagen.style.display = "none";
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
 /*****************************************************************************************************************************
 * ----------OPERACIONES DE SELECCION Y CONSULTA-----------
 ******************************************************************************************************************************/
function consultarComponente(){
	var contenedor = document.getElementById('cntComponente');
	var imagen = document.getElementById('imgCodCom');
	var parametros = "";

//	imagen.style.display = "block";

 	var i = 0;
   	for (i=0;i<document.forms.forma.wtipocom.length;i++){
    	if (document.forms.forma.wtipocom[i].checked)
      		break;
    }

    var tipoArticulo = document.forms.forma.wtipocom[i].value;
	var tipoMedida = document.getElementById("wunidadcom");

	if(document.forms.forma.wnomcom.value == ''){
		parametros = "consultaAjaxKardex=09&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&codigo=" + document.forms.forma.wcodcom.value+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value;
	} else {
		parametros = "consultaAjaxKardex=10&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&nombre=" + document.forms.forma.wnomcom.value+"&tipoMedicamento="+tipoArticulo+"&unidadMedida="+tipoMedida.value;
	}

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				contenedor.innerHTML=$.trim( ajax.responseText );
				imagen.style.display = "none";
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
 /*****************************************************************************************************************************
 * ----------OPERACIONES DE INSERCION Y MODIFICACION-----------
 ******************************************************************************************************************************/
/*****************************************************************************************************************************
 * Llamada ajax para inserción del esquema dextrometer
 ******************************************************************************************************************************/
function grabarEsquemaDextrometerElemento(historia, ingreso, fechaKardex, codInsulina, frec, codEsquema, dosis, uDosis, via, obs, usuario, actualizaIntervalos, min, max ){

	var actualizaInt = "";

	if(actualizaIntervalos == true){
		actualizaInt = "on";
	} else {
		actualizaInt = "off";
	}

	var parametros = "";

	parametros = "consultaAjaxKardex=22&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
					+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&fecha="+fechaKardex
					+"&codInsulina="+codInsulina+"&frecuencia="+frec+"&codEsquema="+codEsquema+"&arrDosis="+dosis
					+"&arrUDosis="+uDosis+"&arrVia="+via+"&arrObservaciones="+obs+"&actualizaIntervalos="+actualizaInt
					+"&min="+min+"&max="+max;

	var mensaje = "";

//	alert(parametros);

	try{
		//$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		//Esta funcion queda sin uso por que el ajax es sincrono
		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				switch ($.trim( ajax.responseText )) {
					case '1':
						mensaje = "Esquema dextrometer modificado con exito";
						break;
					default:
						mensaje = "No se pudo grabar el esquema dextrometer";
						break;
				}
//				alert(mensaje);
//				$.growlUI(mensaje);
			}

			if(actualizaIntervalos){
				if(document.getElementById('cntEsquemaActual')){
					document.getElementById('cntEsquemaActual').style.display = "none";
				}
			}

			if(document.getElementById('btnQuitarEsquema')){
				document.getElementById('btnQuitarEsquema').disabled = false;
			}
			//$.unblockUI();
			//$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

/*****************************************************************************************************************************
 *  Cancela una orden de procedimientos
 ******************************************************************************************************************************/
function cancelarOrdenElemento(historia,ingreso,fecha,usuario,centroCostos,consecutivoOrden){
	//debugger;
	var parametros = "consultaAjaxKardex=28&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
					+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&fecha="+fecha
					+"&centroCostos="+centroCostos+"&consecutivoOrden="+consecutivoOrden;

	var mensaje = "";

	try{
		// $.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				switch ($.trim( ajax.responseText )) {
					case '1':
						// //debugger;
						// var contenedorCentroCostos = document.getElementById("detExamenes"+centroCostos);

						// var tabla = contenedorCentroCostos.parentNode;
						// var celdaExamen = document.getElementById("del"+centroCostos+""+consecutivoOrden);

						// var totalExamenes = celdaExamen.rowSpan;
						// var index = celdaExamen.parentNode.rowIndex;

						// for( var i = 0; i < totalExamenes; i++ ){
							// grabarExamenADetalle( $( "[id^=wnmexamen]" , tabla.rows[ index ] ).attr( "id" ).substr( 9 ), '4' );
							// contenedorCentroCostos.removeChild( tabla.rows[ index ] );
						// }

						// mensaje = "Orden cancelada con exito";

// //						document.getElementById(centroCostos+""+consecutivoOrden).innerHTML = "<b>Orden cancelada...</b>";
// //						var contenedorCentroCostos = document.getElementById(centroCostos);
// //						contenedorCentroCostos.removeChild(document.getElementById("del"+centroCostos+""+consecutivoOrden));
// //						mensaje = "Orden cancelada con exito";

						break;
					default:
						mensaje = "No se pudo cancelar la orden";
						break;
				}
//				alert(mensaje);
//				$.growlUI(mensaje);
			}
			// $.unblockUI();
			// $.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}

/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function consultarOrdenes(wemp_pmla,wempresa,wbasedato,whis,wing,wfecini,wfecfin,wtiposerv,wprocedimiento,westadodet){

	//debugger;
	var parametros = "consultaAjaxKardex=33&wemp_pmla="+wemp_pmla+"&wempresa="+wempresa+"&wbasedato="+wbasedato
					+"&whis="+whis+"&wing="+wing+"&wfecini="+document.forms.forma.wfecini.value+"&wfecfin="+document.forms.forma.wfecfin.value+"&wtiposerv="+document.forms.forma.wtiposerv.value+"&wprocedimiento="+document.forms.forma.wprocedimiento.value+"&westadodet="+westadodet;
	//alert(parametros);
	var mensaje = "";
	//alert(parametros);
	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				var contenedorDetOrdenes = document.getElementById("detOrdenesRealizadas");

				// contenedorDetOrdenes.innerHTML=$.trim( ajax.responseText ); return;

				//Se pasan las filas de la tabla que se trae del ajax a la tabla de Ordenes realizadas

				//Para ello creo un div con toda la tabla que se construyo por el ajax
				var auxDiv = document.createElement( "div" );

				auxDiv.innerHTML = $.trim( ajax.responseText );

				//Ahora voy a pasar todas las filas correspondientes a los examenes terminados a la tabla de Ordenes Realizadas
				var auxtb = auxDiv.childNodes[0].childNodes[0];	//Tabla que se construye por el ajax

				var auxtb2 = contenedorDetOrdenes.firstChild.firstChild;
				for(;auxtb2.rows.length > 3;){
					// auxtb2.removeChild( auxtb2.rows[ 3 ] );
					$( auxtb2.firstChild.rows[ 3 ] ).remove();
				}

				//Las filas que contienen la información son las filas con posición 3 o más
				if( auxtb.rows.length > 3 ){
					//Se añade las filas con posicion 3 o más a la tabla
					for( ;auxtb.rows.length > 3; ){
						contenedorDetOrdenes.firstChild.firstChild.firstChild.appendChild( auxtb.rows[ auxtb.rows.length-1 ] );
					}
				}
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function grabarOrdenElemento(historia,ingreso,fecha,usuario,centroCostos,consecutivoOrden,observacionesOrden){
//	debugger;
	var parametros = "consultaAjaxKardex=29&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
				+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&fecha="+fecha
				+"&centroCostos="+centroCostos+"&consecutivoOrden="+consecutivoOrden+"&observacionesOrden="+observacionesOrden;

	var mensaje = "";

	//alert(parametros);

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				switch ($.trim( ajax.responseText )) {
					case '1':
						mensaje = "Observaciones de la orden actualizada con exito";
					break;
					default:
						mensaje = "No se actualizar la observacion de la orden";
					break;
				}
				//alert(mensaje);
				//$.growlUI(mensaje);
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if(!estaEnProceso(ajax)){
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamada ajax para inserción del medico tratante
 ******************************************************************************************************************************/
function insertarMedicoTratante(idRegistro, tipoDocumento, nroDocumento, historia, ingreso, fechaKardex, tratante, usuario, codigoEspecialidad, nombreMedico, codigoMatrix){
	var contenedor = document.getElementById('cntMedicos');
	var parametros = "";

	parametros = "consultaAjaxKardex=06&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&tipoDocumento="+tipoDocumento
			+"&numeroDocumento="+nroDocumento+"&historia="+historia+"&ingreso="+ingreso+"&codUsuario="+usuario+"&idRegistro="+idRegistro
			+"&fecha="+fechaKardex+"&tratante="+tratante+"&codigoEspecialidad="+codigoEspecialidad+"&codigoMatrix="+codigoMatrix;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				switch ($.trim( ajax.responseText )){
					case '1':
						alert('El medico ya se encontraba asociado');
						break;
					case '2':
						var subIdMedico = codigoMatrix;

						if(tratante == "on"){

							var text = "<span id='Med"+subIdMedico+"' class='vinculo'>";

							//Vinculo de quitar medico
							var atr = new Array();
							atr['onClick'] = "quitarMedico('"+codigoMatrix+"');";
							text += crearCampo("8","","5.3",atr,nombreMedico+" (Tratante)");
							text += "<br/></span>";

							contenedor.innerHTML += text;


//							contenedor.innerHTML += "<span id='Med"+subIdMedico+"' class='vinculo'>";
////							contenedor.innerHTML += "<a href='#null' onClick='quitarMedico("+"\""+codigoMatrix+"\""+");'>" + nombreMedico + "</a> (Tratante)";
//
//							//Vinculo de quitar medico
//							var atr = new Array();
//							atr['onClick'] = "quitarMedico('"+codigoMatrix+"');";
//							contenedor.innerHTML += crearCampo("8","","5.3",atr,nombreMedico+" (Tratante)");
//							contenedor.innerHTML += "<br/></span>";
						} else {

							var text = "<span id='Med"+subIdMedico+"' class='vinculo'>";

							//Vinculo de quitar medico
							var atr = new Array();
							atr['onClick'] = "quitarMedico('"+codigoMatrix+"');";
							text += crearCampo("8","","5.3",atr,nombreMedico);
							text += "<br/></span>";

							contenedor.innerHTML += text;


//							contenedor.innerHTML += "<span id='Med"+subIdMedico+"' class='vinculo'>";
////							contenedor.innerHTML += "<a href='#null' onClick='quitarMedico("+"\""+codigoMatrix+"\""+");'>" + nombreMedico + "</a>";
//
//							//Vinculo de quitar medico
//							var atr = new Array();
//							atr['onClick'] = "quitarMedico('"+codigoMatrix+"');";
//							contenedor.innerHTML += crearCampo("8","","5.3",atr,nombreMedico);
//							contenedor.innerHTML += "<br/></span>";

//							alert( contenedor.innerHTML );
						}
						break;
					case '3':
						alert('No pueden haber dos o mas medicos tratantes (responsables) al tiempo');
						break;
					default:
						alert('No se pudo asociar el medico');
						break;
				}
				$.unblockUI();
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
 /*****************************************************************************************************************************
 * Llamada de inserción de dieta nueva en el kardex
 ******************************************************************************************************************************/
function insertarDietaKardex(idRegistro, historia, ingreso, fechaKardex, usuario){
	var contenedor = document.getElementById('cntMedicamento');
	var parametros = "";

	parametros = "consultaAjaxKardex=14&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
			+"&codUsuario="+usuario+"&idRegistro="+idRegistro+"&fecha="+fechaKardex;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				contenedor.innerHTML=$.trim( ajax.responseText );
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
 /*****************************************************************************************************************************
 * Llamada ajax para la grabación de una nueva infusión
 ******************************************************************************************************************************/
 function grabarInfusionElemento(historia,ingreso,fecha,strComponentes,consecutivo,obsComponentes,usuario,idElemento,fechaSolicitud){
	var parametros = "";

	parametros = "consultaAjaxKardex=11&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&fecha="+fecha+"&componentes="+strComponentes+"&consecutivo="+consecutivo+"&codUsuario="+usuario+"&observaciones="+obsComponentes+"&fechaSolicitud="+fechaSolicitud;

//	alert(parametros);
	var mensaje = "";

	try{
//		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		//
		if($.trim( ajax.responseText ) == '0'){
			mensaje = "No se pudo grabar el liquido endovenoso";
		}

		if($.trim( ajax.responseText ) == '1'){
			var fila = document.getElementById('trIn'+idElemento);
			fila.className = 'fila1';

			mensaje = "El liquido endovenoso se ha grabado correctamente";
		}

		if($.trim( ajax.responseText ) == '2'){
			mensaje = "El liquido endovenoso se ha modificado correctamente";
		}

		/*
		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				if($.trim( ajax.responseText ) == '0'){
					mensaje = "No se pudo grabar el liquido endovenoso";
				}

				if($.trim( ajax.responseText ) == '1'){
					var fila = document.getElementById('trIn'+idElemento);
					fila.className = 'fila1';

					mensaje = "El liquido endovenoso se ha grabado correctamente";
				}

				if($.trim( ajax.responseText ) == '2'){
					mensaje = "El liquido endovenoso se ha modificado correctamente";
				}
			}
		}
		*/
		if (!estaEnProceso(ajax)) {
			ajax.send(null);
		}
	}catch(e){	}
}


function grabarIndicaciones(indicaiones,fechaKardex,historia,ingreso){

	parametros = "consultaAjaxKardex=41&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&wfecha="+fechaKardex+"&windicaiones="+indicaiones;

//	alert(parametros);

	try{
//		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		respuesta = $.trim( ajax.responseText );
		if(respuesta=='0')
			alert("Las indicaciones no han sido guardadas, verifique los datos.");

		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}


		// $.ajax({
			// url: "ordenes.inc.php",
			// contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",
			// dataType: 'json',
			// type: "POST",
			// data:{

				// consultaAjaxKardex:       	'41',
				// wemp_pmla:					wemp_pmla,
				// historia:					historia,
				// ingreso:					ingreso,
				// basedatos:					document.forms.forma.wbasedato.value,
				// wfecha:						fechaKardex,
				// windicaiones:				indicaiones

			// },
			// async: false,
			// success:function(data_json) {

				// if (data_json.error == 1)
				// {
					// alert(data_json.mensaje);
					// return;
				// }
				// else{


				// }
			// }
		// }
	// );


}


function cambiarDisplay(id) {
	if(isset(document.getElementById(id)))
	{
	  fila = document.getElementById(id);
	  if (fila.style.display != "none") {
		fila.style.display = "none"; //ocultar fila
	  }
	}
}

/*****************************************************************************************************************************
 * Llamada ajax para la inserción o modificación de un articulo
 ******************************************************************************************************************************/
function grabarArticuloElemento(historia,ingreso,fechaKardex,cdArt,cntDosis,unDosis,per,fftica,fini,hini,via,conf,dtto,obs,origenArticulo,usuario,condicion,dosMax,cantGrabar,unidadManejo,cantidadManejo,primerKardex,horasFrecuencia,fechaInicioAnt,horaInicioAnt,noDispensar,tipoProtocolo,centroCostosGrabacion,prioridad,idElemento,nombreArticulo,cantidadAlta,impresion,deAlta,firma,artdosisAdaptada,idoriginal,noEsteril,profilaxis,tratamiento,esPediatrico,conInsumo1,conInsumo2,porProtocolo,wdrautorizado,wjusparaautorizar){
	var parametros = "";
	var mensaje = "";
		// alert( "familiasAgregadasIdx["+tipoProtocolo+idElemento+"]:" + familiasAgregadasIdx[ tipoProtocolo+idElemento ] );
	parametros = "consultaAjaxKardex=01&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&codArticulo="+cdArt+"&cantDosis="+cntDosis+"&unDosis="+unDosis+"&per="+per+"&fmaFtica="+fftica+"&fini="+fini+"&hini="+hini+"&dosMax="+dosMax
		+"&via="+via+"&conf="+conf+"&dtto="+dtto+"&obs="+encodeURIComponent( obs )+"&codUsuario="+usuario+"&fechaKardex="+fechaKardex+"&origenArticulo="+origenArticulo+"&condicion="+condicion+"&cantGrabar="+cantGrabar
		+"&unidadManejo="+unidadManejo+"&cantidadManejo="+cantidadManejo+"&primerKardex="+primerKardex+"&horasFrecuencia="+horasFrecuencia+"&fIniAnt="+fechaInicioAnt.replace(" ","")+"&hIniAnt="+horaInicioAnt
		+"&noDispensar="+noDispensar+"&tipoProtocolo="+tipoProtocolo+"&centroCostosGrabacion="+centroCostosGrabacion+"&prioridad="+prioridad
		+"&nombreArticulo="+nombreArticulo+"&wcantidadAlta="+cantidadAlta+"&wimpresion="+impresion+"&walta="+deAlta+"&familia="+familiasAgregadasIdx[ tipoProtocolo+idElemento ]+"&firma="+firma+"&tipoDocumento="+$( "#wtipodoc" ).val()+"&wcedula="+$( "#wcedula" ).val()+"&artdosisAdaptada="+artdosisAdaptada+"&artnoEsteril="+noEsteril
		+"&idoriginal="+idoriginal+"&profilaxis="+profilaxis+"&tratamiento="+tratamiento
		+"&esPediatrico="+esPediatrico+"&conInsumo1="+conInsumo1+"&conInsumo2="+conInsumo2+"&porProtocolo="+porProtocolo
		+"&wdrautorizado="+wdrautorizado+"&wjusparaautorizar="+wjusparaautorizar;

	try{
//		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		respuesta = $.trim( ajax.responseText ).split("*");
//		$.unblockUI();
		var articuloNewMod = false;		//Indica si el articulo es nuevo o modificado


		switch(respuesta[0]){
			case '0':
				mensaje = "No se pudo guardar la informacion del articulo";
				alert(mensaje);
				break;
			case '1':
				articuloNewMod = true;

				/******************************************************************
				 * Si un articulo es modificado y el usuario puede cambiar el estado
				 * de confirmación, se cambia su estado a off
				 ******************************************************************/
				if( articuloNewMod ){
					if( $( "#hiCambiaConfimado" ).val() == "on" ){
						$( "#wconfdisp" ).attr( "checked", false );
					}
				}
				/******************************************************************/
				// tipoProtocolo = "N";
				var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);

				var texto = "";
				if(elemento.tagName == "DIV"){
					texto = elemento.innerHTML;
				} else {
					texto = elemento.value;
				}
				var col = document.getElementById("wcolmed"+tipoProtocolo+idElemento);

				if(col){
					var attrPos = $( elemento ).attr( "pos" );
					$( elemento ).remove();
					col.innerHTML = "<div id='wnmmed"+tipoProtocolo+idElemento+"' class='campo2' pos='"+attrPos+"'>"+texto+"</div>"+col.innerHTML;
				}

				if( arCTCArticulos[ cdArt ] && arCTCArticulos[ cdArt ] > 0 ){
					
					artsGrabadosCTC += ","+cdArt+"-"+respuesta[1]+"-"+texto;

					//Resto uno a la cantidad grabada
					//Esto para que graba justo con el ultimo articulo
					arCTCArticulos[ cdArt ]--;

					if( arCTCArticulos[ cdArt ] == 0 ){
						grabarAjaxArticulos( cdArt, tipoProtocolo, idElemento );
					}
				}

				if( arCTCArticulos[ cdArt ] && arCTCArticulos[ cdArt ] == "artsinctc" ){

					grabarAuditoriaArtSinCTC( cdArt, tipoProtocolo, idElemento );

				}

				$( "#widoriginal"+tipoProtocolo+idElemento ).val( respuesta[1] );

				$( "#wmodificado"+tipoProtocolo+idElemento ).val( "N" );	//Se deja marcado cómo no modificado
				
				try{
					//Esto solo debe salir para articulos de control
					//No siempre se encuentra por que se el elemento en la funcion seleccionarArticulo
					$( "#wcontrolimp"+tipoProtocolo+idElemento ).css({ display: "" });
				}
				catch(e){}

				//Resalto lo grabado
				var fila = document.getElementById('tr'+tipoProtocolo+idElemento);
				fila.className = 'fila1';

				mensaje = "El articulo se ha creado correctamente";
//				$.growlUI('',mensaje);
				break;
			case '2':
				articuloNewMod = true;
				// tipoProtocolo = "N";
				var elemento = document.getElementById("wnmmed"+tipoProtocolo+idElemento);
				var texto = "";

				if(elemento.tagName == "DIV"){
					texto = elemento.innerHTML;
				} else {
					texto = elemento.value;
				}

				/****************************************************************************************************
				 * Mayo 26 de 2015
				 *
				 * Si el sistema hace cambio de fecha y hora de inicio actualizo la fecha y hora de inicio de acuerdo
				 * a cómo se cambió en el sistema
				 ****************************************************************************************************/
				if( respuesta[2] == '1' ){		//Indica que si hubo cambio de fecha y hora de inicio en el sitema
					$( "#whfinicio"+tipoProtocolo+idElemento ).val( respuesta[3]+" a las:"+respuesta[4] );
					$( "#wfinicio"+tipoProtocolo+idElemento ).val( respuesta[3]+" a las:"+respuesta[4] );
					$( "#wfinicioori"+tipoProtocolo+idElemento ).val( respuesta[3]+" a las:"+respuesta[4]);

					$( "#whfinicio"+tipoProtocolo+idElemento )[0].value   = respuesta[3]+" a las:"+respuesta[4];
					$( "#wfinicio"+tipoProtocolo+idElemento )[0].value    = respuesta[3]+" a las:"+respuesta[4];
					$( "#wfinicioori"+tipoProtocolo+idElemento )[0].value = respuesta[3]+" a las:"+respuesta[4];

					fini = respuesta[3];
					hini = respuesta[4];
				}

				$( "#wmodificado"+tipoProtocolo+idElemento ).val( "N" );	//Se deja marcado cómo no modificado
				/****************************************************************************************************/

				var col = document.getElementById("wcolmed"+tipoProtocolo+idElemento);

				if(col && texto != "undefined"){
					col.innerHTML = "<div id='wnmmed"+tipoProtocolo+idElemento+"' class='campo2'>"+texto+"</div>";
				}

				mensaje = "El articulo se ha modificado correctamente";
//				$.growlUI('',mensaje);
				break;
			case '3':
				mensaje = "El articulo no se puede modificar si se encuentra suspendido.";
				alert(mensaje);
				break;
			case '4':
				articuloNewMod = true;
				mensaje = "El articulo se modificó correctamente, tenga en cuenta que ya estaba dispensado completamente.";
				var diferencia = (respuesta[1] ? respuesta[1] : 0) - (respuesta[2] ? respuesta[2] : 0);
				mensaje += "\n-Se genero un ";
				mensaje += (diferencia >= 0) ? "sobrante de " : "faltante de ";
				mensaje += Math.abs(diferencia);
				alert(mensaje);
				break;
			default:
				mensaje = "No especificado: "+$.trim( ajax.responseText );
				alert(mensaje);
				break;
		}

		/******************************************************************
		 * Si un articulo es modificado y el usuario puede cambiar el estado
		 * de confirmación, se cambia su estado a off
		 ******************************************************************/
		if( articuloNewMod ){
			if( $( "#hiCambiaConfimado" ).val() == "on" ){
				$( "#wconfdisp" ).attr( "checked", false );
			}
		}
		/******************************************************************/

		//Para las fechas anteriores cuando se crean los articulos
		if(document.getElementById("whfinicio"+tipoProtocolo+idElemento)){

			document.getElementById("whfinicio"+tipoProtocolo+idElemento).value = fini + " a las:" + hini;
		}

		$.unblockUI();
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}
	catch(e){ 
		console.log(e);	
	}
}
 /*****************************************************************************************************************************
  * Llamada ajax para la modificación de un articulo en el perfil farmacologico
  ******************************************************************************************************************************/
 function grabarArticuloPerfilElemento(historia,ingreso,fechaKardex,cdArt,cdArtNuevo,dtto,obs,usuario,idElemento,unidadDosis,formaFarm,origen,via,dosmax,prioridad,fechaInicio,horaInicio,autorizadoCtc){
 	var parametros = "";
 	var mensaje = "";

 	if(cdArtNuevo && cdArtNuevo != ''){
 		parametros = "consultaAjaxKardex=18&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
 			+"&codArticulo="+cdArt+"&dtto="+dtto+"&obs="+obs+"&codUsuario="+usuario+"&fechaKardex="+fechaKardex+"&codArticuloNuevo="+cdArtNuevo+"&unidadDosis="+unidadDosis
 			+"&formaFarm="+formaFarm+"&origen="+origen;
 	} else {
 		parametros = "consultaAjaxKardex=17&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
			+"&codArticulo="+cdArt+"&dtto="+dtto+"&obs="+obs+"&codUsuario="+usuario+"&fechaKardex="+fechaKardex+"&via="+via+"&dosisMaximas="+dosmax+"&prioridad="+prioridad+"&fechaInicio="+fechaInicio
			+"&horaInicio="+horaInicio+"&autorizadoCtc="+autorizadoCtc;
 	}

 	try{
 		$.blockUI({ message: $('#msjEspere') });
 		ajax=nuevoAjax();

 		ajax.open("POST", "ordenes.inc.php",true);
 		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 		ajax.send(parametros);

 		ajax.onreadystatechange=function()
 		{
 			if (ajax.readyState==4 && ajax.status==200)
 			{
 				var respuesta = $.trim( ajax.responseText ).split("|");

 				switch(respuesta[0]){
 					case '0':
 						mensaje = "No se pudo modificar el artículo.";
 						break;
 					case '1':
 						var elemento = document.getElementById("wnmmed"+idElemento);
 						var fftica = document.getElementById("wffticaact"+idElemento);
 						var dosis = document.getElementById("wudosisact"+idElemento);
						var texto = elemento.value;
						var opcionesMaestro = "";

						//Actualizo en pantalla los articulos
						var col = document.getElementById("wnmmedact"+idElemento);
						col.innerHTML = texto;

						if(formaFarm && formaFarm != ''){
							opcionesMaestro = document.getElementById("wmfftica").options;
							var cont1 = 0;
							while(opcionesMaestro[cont1]){
								if(opcionesMaestro[cont1].value == formaFarm){
									fftica.innerHTML = opcionesMaestro[cont1].text;
									break;
								}
								cont1++;
							}
						} else {
							fftica.innerHTML = "Sin forma farmaceutica";
						}

						if(unidadDosis && unidadDosis != ''){
							opcionesMaestro = document.getElementById("wmunidadesmedida").options;
							var cont1 = 0;

							while(opcionesMaestro[cont1]){
								if(opcionesMaestro[cont1].value == unidadDosis){
									dosis.innerHTML = opcionesMaestro[cont1].text;
									break;
								}
								cont1++;
							}
						} else {
							dosis.innerHTML = "Sin unidad de medida";
						}

						elemento.value = "";
						mensaje = "El artículo se ha reemplazado correctamente.";
 						break;
 					case '2':
 						//Calculo de la diferencia de ctc
 						var autorizado = document.getElementById("wautctc"+idElemento);
 						var usado = document.getElementById("wusadoctc"+idElemento);
 						var disponible = document.getElementById("wdispctc"+idElemento);

 						if(respuesta[1]){
 							usado.value = respuesta[1];
 						}

 						if(autorizado && autorizado.value != '' && !isNaN(parseFloat(usado.value))){
 							if(parseFloat(autorizado.value) >= parseFloat(usado.value)){
 								disponible.value = parseFloat(autorizado.value) - parseFloat(usado.value);
 							} else {
 								disponible.value = parseFloat(autorizado.value) - parseFloat(usado.value);
 							}
 						}

 						mensaje = "El artículo se ha modificado correctamente.";
 						break;
 					case '3':
 						mensaje = "El artículo no se puede modificar si se encuentra suspendido.";
 						break;
 					default:
 						mensaje = "No especificado: "+$.trim( ajax.responseText );
 						break;
 				}
 			}
 			$.unblockUI();
 			$.growlUI('',mensaje);
 		}
 		if ( !estaEnProceso(ajax) ) {
 			ajax.send(null);
 		}
 	}catch(e){ }
 }
/*****************************************************************************************************************************
 * Llamada ajax para suspender o activar un articulo del detalle de medicamentos
 ******************************************************************************************************************************/
function suspenderArticulo(idxElemento,tipoProtocolo,confirmar){

	if( confirmar == undefined )
		confirmar = true;

	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var fecha = document.forms.forma.wfechagrabacion.value;
	var usuario = document.forms.forma.usuario.value;
	var fila = document.getElementById('trFil'+idxElemento);
	var fila = $( "#wnmmed"+tipoProtocolo+idxElemento ).parent().parent()[0];
	var estaSuspendido = 'on';

	// tipoProtocolo = "N";
	var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);
	var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);

	//Extraccion de la fecha y hora inicial de administracion
	var fh = fechaInicio.value.split("a las:");
	fechaInicio = fh[0];
	horaInicio = fh[1];

	//Extraccion del codigo articulo propiamente dicho
	var cd = "";
	if(codigoArticulo.tagName == 'INPUT'){
		cd = codigoArticulo.value.split("-");
	} else {
		cd = codigoArticulo.innerHTML.split("-");
	}

	var clase_asociada = $('#trFil'+idxElemento).attr("class");

	if(clase_asociada != 'suspendido'){

		var accion = "suspender";

	}else{

		var accion = "activar";

	}

	codigoArticulo = cd[0];

	if( confirmar ){
		//Confirmacion de suspension
		//if(confirm("¿Desea "+accion+" la administración del medicamento?")){
		 jConfirm( "¿Desea "+accion+" la administración del medicamento?", accion+' medicamento', function(r) {
			 if(r){
				//Alterno la suspension del medicamento
				if(fila.className == 'suspendido'){
					estaSuspendido = 'off';
					fila.className = 'fila1';
				} else {
					estaSuspendido = 'on';
					fila.className = 'suspendido';
				}

				/**********************************************************************
				 * Creo la acción para ejecutarla al momento de guardar
				 **********************************************************************/
				if( !accionesOrdenes[ SUSPENDER_ARTICULO ] ){
					accionesOrdenes[ SUSPENDER_ARTICULO ] = {};
				}

				if( !accionesOrdenes[ SUSPENDER_ARTICULO ][ tipoProtocolo+"-"+idxElemento ] ){
					accionesOrdenes[ SUSPENDER_ARTICULO ][ tipoProtocolo+"-"+idxElemento ] = new Array();

					var aux = accionesOrdenes[ SUSPENDER_ARTICULO ][ tipoProtocolo+"-"+idxElemento ];
					aux[ aux.length ] = idxElemento;		//parametro1
					aux[ aux.length ] = tipoProtocolo; 		//parametro2
				}
				else{
					delete accionesOrdenes[ SUSPENDER_ARTICULO ][ tipoProtocolo+"-"+idxElemento ];
				}
				/**********************************************************************/
			}
		});
	}
	else{
		//Alterno la suspension del medicamento
		if(fila.className == 'suspendido'){
			estaSuspendido = 'off';
			fila.className = 'fila1';
		} else {
			estaSuspendido = 'on';
			fila.className = 'suspendido';
		}

		/**********************************************************************
		 * Creo la acción para ejecutarla al momento de guardar
		 **********************************************************************/
		if( !accionesOrdenes[ SUSPENDER_ARTICULO ] ){
			accionesOrdenes[ SUSPENDER_ARTICULO ] = {};
		}

		if( !accionesOrdenes[ SUSPENDER_ARTICULO ][ tipoProtocolo+"-"+idxElemento ] ){
			accionesOrdenes[ SUSPENDER_ARTICULO ][ tipoProtocolo+"-"+idxElemento ] = new Array();

			var aux = accionesOrdenes[ SUSPENDER_ARTICULO ][ tipoProtocolo+"-"+idxElemento ];
			aux[ aux.length ] = idxElemento;		//parametro1
			aux[ aux.length ] = tipoProtocolo; 		//parametro2
		}
		else{
			delete accionesOrdenes[ SUSPENDER_ARTICULO ][ tipoProtocolo+"-"+idxElemento ];
		}
		/**********************************************************************/
	}
}


// function suspenderArticulo(idxElemento,tipoProtocolo){
	// var historia = document.forms.forma.whistoria.value;
	// var ingreso = document.forms.forma.wingreso.value;
	// var fecha = document.forms.forma.wfechagrabacion.value;
	// var usuario = document.forms.forma.usuario.value;
	// var fila = document.getElementById('trFil'+idxElemento);
	// var fila = $( "#wnmmed"+tipoProtocolo+idxElemento ).parent().parent()[0];
	// var estaSuspendido = 'on';

	// // tipoProtocolo = "N";
	// var codigoArticulo = document.getElementById('wnmmed'+tipoProtocolo+idxElemento);
	// var fechaInicio = document.getElementById('wfinicio'+tipoProtocolo+idxElemento);

	// //Extraccion de la fecha y hora inicial de administracion
	// var fh = fechaInicio.value.split("a las:");
	// fechaInicio = fh[0];
	// horaInicio = fh[1];

	// //Extraccion del codigo articulo propiamente dicho
	// var cd = "";
	// if(codigoArticulo.tagName == 'INPUT'){
		// cd = codigoArticulo.value.split("-");
	// } else {
		// cd = codigoArticulo.innerHTML.split("-");
	// }

	// //Alterno la suspension del medicamento
	// if(fila.className == 'suspendido'){
		// estaSuspendido = 'off';
	// } else {
		// estaSuspendido = 'on';
	// }

	// codigoArticulo = cd[0];

	// //Confirmacion de suspension
	// if(confirm("¿Desea cambiar la suspensión/activación de la administración del medicamento?")){

	// //Llamada AJAX
	// var parametros = "";
	// var mensaje = "";

	// parametros = "consultaAjaxKardex=16&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		// +"&codigoArticulo="+codigoArticulo+"&codUsuario="+usuario+"&fecha="+fecha+"&estado="+estaSuspendido+"&fechaInicio="+fechaInicio+"&horaInicio="+horaInicio
		// +"&tipoProtocolo="+tipoProtocolo;

	// try{
		// $.blockUI({ message: $('#msjEspere') });
		// ajax=nuevoAjax();

		// ajax.open("POST", "ordenes.inc.php",true);
		// ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		// ajax.send(parametros);

		// ajax.onreadystatechange=function()
		// {
			// if (ajax.readyState==4 && ajax.status==200)
			// {
				// if($.trim( ajax.responseText ) == '1'){
					// if(estaSuspendido == 'on'){
						// fila.className = 'suspendido';
						// mensaje = "El medicamento se ha suspendido.";
					// } else {
						// fila.className = '';
						// mensaje = "El medicamento se ha activado.";
					// }
				// } else {
					// mensaje = "No se pudo modificar estado suspension: " + $.trim( ajax.responseText );
				// }
			// }
			// $.unblockUI();
			// $.growlUI('',mensaje);
		// }
		// if ( !estaEnProceso(ajax) ) {
			// ajax.send(null);
		// }
		// }catch(e){	}
	// }
// }
/*****************************************************************************************************************************
 * Llamada a ajax para realizar la grabación de un examen nuevo
 ******************************************************************************************************************************/
function grabarExamenElemento(codExamen,nomExamen,historia,ingreso,fecha,observaciones,estadoExamen,fechaDeSolicitado,usuario,consecutivoOrden,firma,observacionesOrden,cod_procedi,justificacion,idElemento, nroItem,impExamen,firmHCE,altExamen,grbAut,datosAdicionales,datoOrdenAnexa,esOfertado,usuarioTomaMuestra,horaDeSolicitado){
	var parametros = "";
	var mensaje = "";

	parametros = "consultaAjaxKardex=7&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
		+"&fecha="+fecha+"&codigoExamen="+codExamen+"&observaciones="+observaciones+"&estado="+estadoExamen+"&codUsuario="+usuario+"&nombreExamen="+nomExamen
		+"&fechaDeSolicitado="+fechaDeSolicitado+"&consecutivoOrden="+consecutivoOrden+"&firma="+firma+"&observacionesOrden="+observacionesOrden+"&consecutivoExamen="+cod_procedi
		+"&justificacion="+justificacion+"&numeroItem="+nroItem+"&impExamen="+impExamen+"&altExamen="+altExamen+"&firmHCE="+firmHCE+"&datosAdicionales="+datosAdicionales
		+"&ordenAnexa="+datoOrdenAnexa+"&esOfertado="+esOfertado+"&usuarioTomaMuestra="+usuarioTomaMuestra+"&cco="+$("#wservicio").val();

	try{

		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		var grabado = false;	//Noviembre 08 de 2012

		//Noviembre 08 de 2012
		var rpAjax = $.trim( ajax.responseText ).split( "|" );

		if( rpAjax[0] == '0' ){
			mensaje = "No se pudo grabar el examen";
		}

		if( rpAjax[0] == '1'){
			//Resalto lo grabado
			var fila = document.getElementById('trEx'+idElemento);

			if(!fila){
				var fila = document.getElementById('trExImp'+idElemento);
			}

			if( grbAut ){
				if( fila.className.toLowerCase() != 'fila1' && fila.className.toLowerCase() != 'fila2' )
					fila.className = 'fila1';
			}

			mensaje = "El examen se ha grabado correctamente";

			grabado = true;
		}

		if( rpAjax[0] == '2'){
			mensaje = "El examen se ha modificado correctamente";

			var fila = document.getElementById('trEx'+idElemento);

			if(!fila){
				var fila = document.getElementById('trExImp'+idElemento);
			}

			if( grbAut ){
				if( fila.className.toLowerCase() != 'fila1' && fila.className.toLowerCase() != 'fila2' )
					fila.className = 'fila1';
			}

			grabado = true;
		}

		//Noviembre 08 de 2012
		if( grabado ){

			//Si es agragado desde la pestaña de examenes
			if( fila.id.substr(0,7) != 'trExImp' ){

				//Si queda grabado y el examen es nuevo (nro de item == 0 ) le asigno el nro de item
				if( document.getElementById("hexnroitem"+idElemento).value == 0 ){
					document.getElementById("hexnroitem"+idElemento).value = rpAjax[2];
					document.getElementById("hexcons"+idElemento).value = rpAjax[1];

					var auxTdNroOrden = $( "#del" + codExamen + consecutivoOrden );
					$( "b", auxTdNroOrden ).eq(0).html( "Orden Nro. " + rpAjax[1] );
					$( "a", auxTdNroOrden ).eq(0).attr( "onClick","cancelarOrden('"+codExamen+"','"+rpAjax[1]+"');" );
					$( "#del" + codExamen + consecutivoOrden )[0].id = "del"+codExamen+rpAjax[1];

					//Se cambia el checkbox de impresion asociado al examen para que tome los datos correctamente.
					var dato_onclick = "marcarImpresionExamen(this, '"+codExamen+"', '"+rpAjax[1]+"', '"+cod_procedi+"', '"+fechaDeSolicitado+"', '"+rpAjax[2]+"' )";
					$(fila).find( "[name=imprimir_examen]").attr('onclick', dato_onclick);


				}
			}
			else{
				//Si es agregado desde la pestaña alta

				//Si queda grabado y el examen es nuevo (nro de item == 0 ) le asigno el nro de item
				if( document.getElementById("hexnroitemimp"+idElemento).value == 0 ){
					document.getElementById("hexnroitemimp"+idElemento).value = rpAjax[2];
					document.getElementById("hexconsimp"+idElemento).value = rpAjax[1];

					// var auxTdNroOrden = $( "#del" + codExamen + consecutivoOrden );
					// $( "b", auxTdNroOrden ).eq(0).html( "Orden Nro. " + rpAjax[1] );
					// $( "a", auxTdNroOrden ).eq(0).attr( "onClick","cancelarOrden('"+codExamen+"','"+rpAjax[1]+"');" );
					// $( "#del" + codExamen + consecutivoOrden )[0].id = "del"+codExamen+rpAjax[1];

				}

			}

			if( grbAut ){	//Es verdadero si se llama desde los botones de impresión o grabar
				grabarAjaxProcedimiento( codExamen, rpAjax[1], rpAjax[2], idElemento, document.forms.forma.wemp_pmla.value  );
			}

			if(arCTCprocedimientos[cod_procedi] && arCTCprocedimientos[cod_procedi] == "procsinctc"){

				grabarAuditoriaProcSinCTC( cod_procedi, nomExamen );

			}

		}

		if (!estaEnProceso(ajax)) {
			ajax.send(null);
		}
	}catch(e){	}
}

/*****************************************************************************************************************************
 * Llamada a ajax para realizar la grabación la aprobación del regente
 ******************************************************************************************************************************/
function grabarAprobacionRegente11(idElemento){
	var parametros = "";

	var historia = document.forms.forma.whistoria.value;
  	var ingreso = document.forms.forma.wingreso.value;
  	var fechaKardex = document.forms.forma.wfechagrabacion.value;
  	var usuario = document.forms.forma.usuario.value;
  	var estado = document.getElementById('wchkapr').checked ? "on" : "off";

  	var mensaje = "";

 	parametros = "consultaAjaxKardex=20&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso
			+"&codUsuario="+usuario+"&fecha="+fechaKardex+"&estado="+estado;

 	try{
 		$.blockUI({ message: $('#msjEspere') });
 		ajax=nuevoAjax();

 		ajax.open("POST", "ordenes.inc.php",true);
 		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 		ajax.send(parametros);

 		ajax.onreadystatechange=function()
 		{
 			if (ajax.readyState==4 && ajax.status==200)
 			{
 				switch($.trim( ajax.responseText )){
 					case '0':
 						mensaje = "No se pudo grabar la aprobación del regente";
 						break;
 					case '1':
 						if(document.getElementById('wchkapr').checked){
 							mensaje = "El perfil se aprobó por el regente.";
 						} else {
 							mensaje = "El perfil NO se aprobó por el regente.";
 						}
						break;
	 				default:
	 					mensaje = "No especificado: "+$.trim( ajax.responseText );
	 					break;
	 			}
	 		}
 			$.unblockUI();
 			$.growlUI('',mensaje);
	 	}
	 	if ( !estaEnProceso(ajax) ) {
	 		ajax.send(null);
		}
 	}catch(e){ }
 }
 /*****************************************************************************************************************************
 * -----------OPERACIONES DE ELIMINACION---------------
 ******************************************************************************************************************************/
 function quitarMedico(cedula_medico){
	var fecha = document.forms.forma.wfecha.value;
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;

	if(confirm("Desea quitar el medico seleccionado?")){
		eliminarMedicoElemento(historia,ingreso,fecha,usuario,cedula_medico);
	}
	return false;
}
 /*****************************************************************************************************************************
  * Elimina una alergia por fecha
  ******************************************************************************************************************************/
function quitarAlergia(fecha){
	var historia = document.forms.forma.whistoria.value;
	var ingreso = document.forms.forma.wingreso.value;
	var usuario = document.forms.forma.usuario.value;

	if(confirm("Desea quitar la alergia seleccionada?")){
		eliminarAlergiaElemento(historia,ingreso,fecha,usuario);
	}
}
 /*****************************************************************************************************************************
 * Llamado a ajax para la eliminación de un medico tratante
 ******************************************************************************************************************************/
function eliminarMedicoElemento(historia,ingreso,fecha,usuario,cedula_medico){
	var parametros = "";
	var mensaje = "";

	parametros = "consultaAjaxKardex=13&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
			+"&cedula_medico="+cedula_medico+"&codUsuario="+usuario;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				var prueba = $.trim( ajax.responseText );
				if($.trim( ajax.responseText ) == 1){
					var cntMedicos = document.getElementById("cntMedicos");
					var filaEliminar = document.getElementById("Med"+cedula_medico);

				 	cntMedicos.removeChild(filaEliminar);

				 	mensaje = "El medico se ha retirado con exito";
				 }

			 //No pudo eliminar
			 if($.trim( ajax.responseText ) == 0){
				 mensaje = 'No se pudo retirar el medico';
			 }
		}
		$.unblockUI();
		$.growlUI('',mensaje);
	}
	if ( !estaEnProceso(ajax) ) {
		ajax.send(null);
	}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamado a ajax para la eliminación de una alergia
 ******************************************************************************************************************************/
function eliminarAlergiaElemento(historia,ingreso,fecha,usuario){
	var parametros = "";
	var mensaje = "";

	parametros = "consultaAjaxKardex=21&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
			+"&codUsuario="+usuario+"&descripcion=";

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){
				if($.trim( ajax.responseText ) == 1){

					var cntDetalleAlergias = document.getElementById("detAlergias");
					var filaEliminar = document.getElementById("trAle"+fecha);

					cntDetalleAlergias.removeChild(filaEliminar);
					mensaje = 'La alergia ha sido eliminada con exito';
				 }

				//No pudo eliminar
				if($.trim( ajax.responseText ) == 0){
					mensaje = 'No se pudo modificar la alergia';
				}
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamado ajax para función de eliminación de dieta
 ******************************************************************************************************************************/
function eliminarDietaElemento(historia,ingreso,fecha,usuario,codigoDieta){
	var parametros = "";
	var mensaje = "";

	parametros = "consultaAjaxKardex=15&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha
			+"&idRegistro="+codigoDieta+"&codUsuario="+usuario;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function(){
			if (ajax.readyState==4 && ajax.status==200){

				//Eliminado correctamente
				if($.trim( ajax.responseText ) == 1){
					var cntDietas = document.getElementById("cntDietas");
					var filaEliminar = document.getElementById("Die"+codigoDieta);
				 	cntDietas.removeChild(filaEliminar);

				 	var colDietas = document.forms.forma.colDietas;
					colDietas[codigoDieta] = '';
					mensaje = 'La dieta se ha retirado con exito';
				 }

			 //No pudo eliminar
			 if($.trim( ajax.responseText ) == 0){
				 mensaje = 'No se pudo eliminar la dieta';
			 }
		}
		$.unblockUI();
		$.growlUI('',mensaje);
	}
	if ( !estaEnProceso(ajax) ) {
		ajax.send(null);
	}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamada ajax para eliminación del articulo
 ******************************************************************************************************************************/
function eliminarArticuloElemento(historia,ingreso,fecha,cdArt,usuario,idx,fini,hini,tipoProtocolo,contenedorArticulo,altaArticulo,idOriginal){
//	debugger;
	var parametros = "consultaAjaxKardex=4&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value
			+"&historia="+historia+"&ingreso="+ingreso+"&codArticulo="+cdArt+"&fecha="+fecha+"&codUsuario="+usuario+"&fechaInicio="+fini+"&horaInicio="+hini
			+"&tipoProtocolo="+tipoProtocolo+"&altaArticulo="+altaArticulo+"&idOriginal="+idOriginal;

	try{
		//$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				if( $( "#trFil"+idx, $( contenedorArticulo ) ).length > 0 ){
					// $( "#trFil"+idx, $( contenedorArticulo ) ).remove();
					$( "#wnmmed"+tipoProtocolo+idx, $( contenedorArticulo ) ).parent().parent().remove();
				}
				else{
					 // $( "#trFil"+idx, $( "#detKardexAddN" ) ).remove();
					 $( "#wnmmed"+tipoProtocolo+idx, $( "#detKardexAddN" ) ).parent().parent().remove();
				}

				// var cntDetalleKardex = document.getElementById(contenedorArticulo); //detKardexAddU
				// var filaEliminar = document.getElementById("trFil"+idx);

				// cntDetalleKardex.removeChild(filaEliminar);

			}
			//$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamada ajax para eliminación de una infusión
 ******************************************************************************************************************************/
function eliminarInfusionElemento(historia,ingreso,fecha,usuario,idx){
	var parametros = "";

	parametros = "consultaAjaxKardex=12&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha+"&codUsuario="+usuario+"&consecutivo="+idx;

	try{
		ajax = nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				var cntDetalleKardex = document.getElementById("detInfusiones");
				var filaEliminar = document.getElementById("trIn"+idx);

				cntDetalleKardex.removeChild(filaEliminar);
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Llamada ajax para eliminar un examen de laboratorio
 ******************************************************************************************************************************/
function eliminarExamenElemento(historia,ingreso,codigoExamen,fecha,usuario,consecutivoOrden,consecutivoExamen,idx, nroItem, prefijoAlta, nuevoExamen){
//	debugger;
	var parametros = "";
	var mensaje = "";

	parametros = "consultaAjaxKardex=8&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha+"&codExamen="+codigoExamen+"&codUsuario="+usuario+"&consecutivoOrden="+consecutivoOrden+"&consecutivoExamen="+consecutivoExamen+"&numeroItem="+nroItem;

	// var prefijoAltaLower = prefijoAlta.toLowerCase();

	try{
		// $.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{

				//Eliminado correctamente
				if( $.trim( ajax.responseText ) == 1 ){
				 }

				 //No pudo eliminar
				 if($.trim( ajax.responseText ) == 0){
					 mensaje = 'No se pudo eliminar el examen';
				 }
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}


/*****************************************************************************************************************************
 * Llamada ajax para eliminar un esquema dextrometer
 ******************************************************************************************************************************/
function eliminarEsquemaDextrometerElemento(historia, ingreso, fechaKardex, codArticulo, usuario, codEsquema){
	var parametros = "";
	var mensaje = "";

	parametros = "consultaAjaxKardex=23&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fechaKardex
			+"&codInsulina="+codArticulo+"&codUsuario="+usuario+"&codEsquema="+codEsquema;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				//Eliminado correctamente
				// if($.trim( ajax.responseText ) == 1){
					// var cntEsquema = document.getElementById("cntEsquema");
					// var cntEsquemaActual = document.getElementById("cntEsquemaActual");

					// if(cntEsquema){
						// cntEsquema.style.display = 'none';
					// }

					// if(cntEsquemaActual){
						// cntEsquemaActual.style.display = 'none';
						// $( cntEsquemaActual ).html('');
					// }

					// //Limpio los valores y anulo el boton quitar
					// if(document.getElementById('btnQuitarEsquema')){
						// document.getElementById('btnQuitarEsquema').disabled = true;
					// }
				 // }

				 //No pudo eliminar
				if($.trim( ajax.responseText ) == 0){
					mensaje = 'No se pudo eliminar el esquema';
				}
			}
			$.unblockUI();
			$.growlUI('',mensaje);
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}
	catch(e){}
}

familiasAgregadas = {};
familiasAgregadasIdx = {};
/*****************************************************************************************************************************
 * Seleccion de un medicamento de la lista consultada para llevarlo al detalle
 *
 * Se agregaron los siguientes protocolos:
 *
 * 1.  Normal.
 * 2.  Analgesias.
 * 3.  Nutriciones.
 * 4.  Quimioterapia.
 ******************************************************************************************************************************/
function seleccionarArticulo(codigo, nombreComercial, nombreGenerico, origen, grupo, forma, unidad, pos, unidadFraccion, cantFracciones,
		vencimiento, diasVencimiento, dispensable, duplicable, diasMaximos, dosisMaximas, via, tipoProtocolo, tipoMedLiquido, esGenerico,
		abreVentanaFija,cantidadDosisFija,unidadDosisFija,noEnviarFija,frecuenciaFija,viasAdministracionFija, condicionSuministroFija,
		confirmadaPreparacionFija,diasMaximosFija,dosisMaximasFija,observacionesFija,componentesTipo,noEnviar,deAlta,codPrincipioActivo,
		desPrincipioActivo,esAntibiotico,infoNutriciones,esCompuesto,conMedicamento1,conMedicamento2,porProtocolo,familiaATC,conTarifa){
			
	porProtocolo = porProtocolo ? porProtocolo: false;
	
	var famsAtc = [];
	if( familiaATC && familiaATC != '' ){
		
		varAtcFamilias = familiaATC.split("@@");
		
		$( varAtcFamilias ).each(function(){
			let dataFams = this.split("-");
			
			famsAtc.push({
				codigo 		: dataFams[0],
				descripcion : dataFams[1],
			})
		});
	}
	
	/*
	 * Existe familia ATC
	 */
	var exist_fam_atc = false;
	var famsATCexist = [];
	 
	$( famsAtc ).each(function(){
		
		// $( "#wnmmed"+$( "[id^=watc][data-atc-n02b]" )[0].id.substr( "watc".length ) ).val() || $( "#wnmmed"+$( "[id^=watc][data-atc-n02b]" )[0].id.substr( "watc".length ) ).html()
		
			if( $( "[id^=watc][data-atc-"+this.codigo+"]" ).length > 0 ){
				
				var __selfCodigoATC = this;
				
				$( "[id^=watc][data-atc-"+__selfCodigoATC+"]" ).each(function(){
					
					let codartatcrep = $( "#wnmmed"+$( this )[0].id.substr( "watc".length ) ).val() || $( "#wnmmed"+$( this )[0].id.substr( "watc".length ) ).html() || null;
					
					if( codartatcrep ){
						exist_fam_atc = true;
						famsATCexist.push( __selfCodigoATC );
						return false;
					}
				})
				
			}
	});
	
	if( exist_fam_atc && horarioEspecial && contHorarioEspecial>1){
		exist_fam_atc = false;
	}
	
	/*************************************************************************************
	 * Existe Principio Activo
	 *************************************************************************************/
	var existePrincipioActivo = false;
	
	var pActivos = codPrincipioActivo.split( "," );
	var desPActivos = desPrincipioActivo.split( "," );
	var msgPActivos = "";

	for( var iPActivos=0; iPActivos < pActivos.length; iPActivos++ ){

		//Expresino regular que dice si existe el codigo de principio activo en la familia
		var patt = new RegExp("\\b"+$.trim( pActivos[iPActivos] )+"\\b", "igm" );
		var pattPac = new RegExp("\\b"+$.trim( desPActivos[iPActivos] )+"\\b", "igm");

		$( "[pActivo]" ).each(function(k){

			if( $( this ).attr( "pActivo" ) != '' ){

				//Prueba si el codigo existe en las familias
				if( patt.test( $( this ).attr( "pActivo" ) ) && !pattPac.test( msgPActivos ) ){
					existePrincipioActivo = true;
					return false;
				}
			}
		});
	}
	
	/****************************************************************************************
	 * Existe familia
	 ****************************************************************************************/
	var existe_familia = false;
	 
	var nombreFamilia = document.getElementById( "wnombrefamilia" ).value;		// Familia de medicamentos
	$( "#tbDetalleN > tbody > tr > td > b" ).each(function(){

		if( nombreFamilia != "" ){
			//Verifico que no halla una familia agregada
			if( $.trim( $( this ).text().split( " - " )[1] ) == nombreFamilia ){
				existe_familia = true;
				return false;
			}
		}
	});


	if(!deAlta)
		deAlta = "off";

	var tipoProtocoloAux = "";
	var i = 0, idx = 0;

	$.alerts.okButton = "Sí";		//Dejo por defecto el valor sí para el boton de aceptar
	$.alerts.cancelButton = "No";	//Dejo por defecto el valor no para el boton cancelar

	tipoProtocoloAux = tipoProtocolo;
	// tipoProtocoloAux = "N";

//	switch (tipoProtocolo) {
	switch (tipoProtocoloAux) {
		case 'N':
			idx = elementosDetalle-1;

			posicionActual = elementosDetalle;
//			tipoProtocoloAux = "";
		break;
		case 'A':
			idx = elementosAnalgesia-1;

			posicionActual = elementosAnalgesia;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		case 'U':
			idx = elementosNutricion-1;

			posicionActual = elementosNutricion;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		case 'Q':
			idx = elementosQuimioterapia-1;

			posicionActual = elementosQuimioterapia;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		case 'LQ':
			idx = elementosLev-1;

			posicionActual = elementosLev;
//			tipoProtocoloAux = tipoProtocolo;
		break;
		default:
//			idx = elementosDetalle-1;
			idx = elementosDetalle-1;

			posicionActual = elementosDetalle;
		break;
	}
	
	//Mayo 20 de 2016
	//Si el médico seteo dosis máxima o un día de tratamiento
	//los campos de dosis máxima preconfigurados y días de tratamientos se dejan vacíos
	if( $.trim( diasMaximosFija ) != '' || $.trim( dosisMaximasFija ) != '' ){
		diasMaximos = '';
		dosisMaximas = '';
	}

	var mFechaInicio = "";

	var elementoAnteriorDetalle = document.getElementById("wnmmed"+tipoProtocoloAux+idx);
	// var cntDetalleKardex = document.getElementById("detKardexAdd" + tipoProtocoloAux);
	var cntDetalleKardex = document.getElementById("detKardexAddN");	//Al agregar un medicamento nuevo siempre está en este contenedor

	var idTabla = "tbDetalle" + tipoProtocoloAux;

	stopEvent(window.onbeforeunload);

	if(posicionActual > 0){
		var elemento = elementoAnteriorDetalle;
		//forma = forma.toString();

		if(elemento){


			/************************************************************************************************************************************
			 * Si el articulo es generico (DA0000, DD0000, DC0000, ... ) se busca en la lista de articulos y si está con la misma fecha y
			 * hora de inicio del articulo generico a grabar se cambia el codigo del articulo generico a otro que no se encuentre
			 ************************************************************************************************************************************/
			//Siempre se trae la DA000 si es generico
			if( codigo.toUpperCase() == "DA0000" ){

				cantFracciones = 1;
				unidadDosisFija = "BO";
				unidadFraccion = "BO";
				// mUnidadDosis = "BO";
				// mCantDosis = 1;
				// forma

				//Se hace una copia de los articulos genericos
				var daGenericasCopy = daGenericas.slice();

				var cambioArticulo = true;
				while( cambioArticulo ){

					cambioArticulo = false;

					//Se busca todos los articulos con el mismo código
					$( "[id^=wnmmed][value^="+codigo.toUpperCase()+"],[id^=wnmmed][innerHTML^="+codigo.toUpperCase()+"]", $( "[id^=fragment]" ) ).each(function(x){

						//Articulo que se encuentra en la lista de agregados o grabados
						var fii = $( "[id^=wfinicio]", this.parentNode.parentNode ).val().split( "a las:" );

						//Fecha y hora de inicio del articulo a grabar
						var fif = fechaInicioFija.split( "a las:" );

						//Se busca si el articulo tiene la misma fecha y hora de inicio a uno que se encuentre en la lista
						if( $.trim( fii[0] ) == $.trim( fif[0] ) && $.trim( fii[1] ).substr(0,5) == $.trim( fif[1] ).substr( 0, 5 ) ){

							//Borro del array el articulo generico que ya existe con fecha y hora de inicio
							for( var iii = 0; iii < daGenericasCopy.length; iii++ ){
								if( daGenericasCopy[iii] == codigo ){
									daGenericasCopy.splice( iii, 1 );	//Borro el articulo generico encontrado
									break;
								}
							}

							for( var iii = 0; iii < daGenericasCopy.length; iii++ ){
								if( daGenericasCopy[iii] != codigo ){
									codigo = daGenericasCopy[iii];
									daGenericasCopy.splice( iii, 1 );	//Borro el articulo generico encontrado
									cambioArticulo = true;
									return;
									break;
								}
							}
						}
					});
				}
			}
			/************************************************************************************************************************************/

			//Valido que el articulo no se encuentre previamente en la lista
			var existe = false;
			var adicionar = false;
			var cont1 = 0;
			var item = null;
			var cd = "";
			var cdItem = "";

			// while(cont1 < elementosDetalle)
			while(cont1 < Math.max(elementosDetalle,elementosAnalgesia,elementosNutricion,elementosQuimioterapia,elementosLev) ){
				item = document.getElementById("wnmmed"+tipoProtocoloAux+cont1);

				if(item){
					if(item.tagName != 'DIV'){
						cd = item.value.split("-");
					} else {
						cd = item.innerHTML.split("-");
					}
					cdItem = $.trim( cd[0] );

					if( codigo != 'IC0000' && codigo != 'LQ0000' ){
						if(cdItem == codigo){
							existe = true;
							break;
						}
					}
				}
				cont1++;
			}
			
			if( exist_fam_atc || existe_familia || existePrincipioActivo )
				existe = false;


			if( deAlta == "on" )
				var datFecHor = $( "#wfinicioaplicacionimp" ).val().split( "a las:" );
			else
				var datFecHor = $( "#wfinicioaplicacion" ).val().split( "a las:" );
			var datFecHor = fechaInicioFija.split( "a las:" );
			while(cont1 < elementosDetalle){
				//Se verifica si hay datos para el articulo.
				if($("#wnmmed"+tipoProtocoloAux+cont1).length > 0){

					item = document.getElementById("whfinicio"+tipoProtocoloAux+cont1);

					if( !$( item ).parent().parent().hasClass( "suspendido" ) ){
						var datos_cod_art = "";
						var cod_art = "";

						//Extrae la informacion del div que contiene el codigo del articulo.
						var datos_cod_art = $("#wnmmed"+tipoProtocoloAux+cont1).html().split("-");

						//Si no hay informacion para la posicion buscara en el input.
						if( datos_cod_art == "" ){

							var datos_cod_art = $("#wnmmed"+tipoProtocoloAux+cont1).val().split("-");

						}

						var cod_art = datos_cod_art[0];

						if(item && idx != cont1 ){
							if(item.tagName != 'DIV'){
								cd = item.value.split("a las:");
							} else {
								cd = item.innerHTML.split("a las:");
							}

							var fecha = $.trim(cd[0]);
							var hora = $.trim(cd[1]);
						}

						item = document.getElementById("wfinicio"+tipoProtocoloAux+cont1);

						if(item && idx != cont1 ){
							if(item.tagName != 'DIV'){
								cd = item.value.split("a las:");
							} else {
								cd = item.innerHTML.split("a las:");
							}

							var fecha = $.trim(cd[0]);
							var hora = $.trim(cd[1]);
						}
					}
				}
				cont1++;

			}

			var preguntarPrincipioActivo = true;

			//Si el articulo es duplicable se anula la validacion anterior
			if(duplicable == 'on' && existe){
				adicionar = false;
			}

			if(duplicable == 'off' && existe){
				adicionar = true;
			}

			if(horarioEspecial)
				contHorarioEspecial++;

			nombreArticuloAlert =  trim(nombreComercial.replace(/_/g," "));
			var preguntarFamilia = true;

			if( codigo == 'IC0000' || codigo == 'LQ0000' ){
				var preguntarFamilia = false;
			}

			if( adicionMultiple ){
				preguntarFamilia = false;
			}
			
			if( exist_fam_atc ){
				preguntarPrincipioActivo = false;
				preguntarFamilia 		 = false;
				existe 					 = false;
				existePrincipioActivo	 = false;
				existe_familia	 		 = false;
			}
			
			if( existePrincipioActivo ){
				preguntarFamilia	= false;
				existe_familia	 	= false;
				existe 				= false;
			}
			
			if(existe_familia){
				existe 				= false;
			}



			//Creo obejto deferred
			var objDfr = $.Deferred();
			objDfr.resolve( true );
			

			if(existe && !horarioEspecial){
				preguntarFamilia = false;

				if( artLevs[ tipoProtocoloAux+idx ] ){

					// if( artLevs[ tipoProtocoloAux+idx ].insInf != 'on' && artLevs[ tipoProtocoloAux+idx ].insEle == 'off' ){	//Si es un LEV
					if( artLevs[ tipoProtocoloAux+idx ].insInf != 'on' ){	//Si es un LEV

						/********************************************************************************
						 * Para LEV sale el mensaje de que existe el articulo si:
						 *	1. Es un electrolito
						 *	2. Si solo se ha elegido solución y ningún electrolito para el lev
						 ********************************************************************************/
						var esLevInsPregunta = false;	//Indica si debe preguntar si existe el articulo

						//Si es electrolito se pregunta si desea agregar
						esLevInsPregunta = (artLevs[ tipoProtocoloAux+idx ].insEle == 'on')? true: false;

						//Si es una solución pregunto solo si desea agregar solo si no hay un electrolito para el lev
						if( !esLevInsPregunta ){
							esLevInsPregunta = true;
							//Busco todos los articulos, si encuentra un electrolito lo pongo en false
							for( var iArtLevs in artLevs ){
								if( iArtLevs != tipoProtocoloAux+idx ){
									if( artLevs[iArtLevs].idoLev == artLevs[tipoProtocoloAux+idx].idoLev ){
										if( artLevs[iArtLevs].insEle == 'on' ){
											esLevInsPregunta = false;
										}
									}
								}
							}
						}

						if( esLevInsPregunta ){

							if( artLevs[ tipoProtocoloAux+idx ].insEle == 'on' ){
								//Se cambia el nombre del articulo al nombre alterno
								var idListaLev = $( "[ele"+codigo+"]", $( "#listaComponentesLEV" ) ).attr("ele"+codigo);
								nombreArticuloAlert = $( "[tdelegen"+idListaLev+"]", $( "#listaComponentesLEV" ) ).html();
							}
							else{
								//Se cambia el nombre del articulo al nombre alterno
								var idListaLev = $( "[sol"+codigo+"]", $( "#listaComponentesLEV" ) ).attr("sol"+codigo);
								nombreArticuloAlert = $( "[tdsolgen"+idListaLev+"]", $( "#listaComponentesLEV" ) ).html();
							}
							
							var objDfr = $.Deferred();

							jConfirm('El medicamento ' + nombreArticuloAlert + ' ya existe. Desea agregarlo?','ALERTA',function(x){

								if(!x){
									var trEliminar = document.getElementById("trFil"+idx);
									cntDetalleKardex.removeChild(trEliminar);

									objDfr.resolve( false );

									eliminarArtIgualesMultiples( codigo );
									agregarMultiplesArticulos();
									
									quitarArticuloDextrometer( frecuenciaFija, codigo );
								}
								else{
									objDfr.resolve( true );
								}
							});
							$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});

							// if(!confirm('El medicamento ' + nombreArticuloAlert + ' ya existe. Desea agregarlo?'))
							// {
								// var trEliminar = document.getElementById("trFil"+idx);
								// cntDetalleKardex.removeChild(trEliminar);

								// return false;
							// }
						}
					}
					else if( artLevs[ tipoProtocoloAux+idx ].insInf == 'on' && artLevs[ tipoProtocoloAux+idx ].insEle == 'on' ){	//Si es una infusión

						var objDfr = $.Deferred();

						if( artLevs[ tipoProtocoloAux+idx ].insEle == 'on' ){
							//Se cambia el nombre del articulo al nombre alterno
							var idListaLev = $( "[ele"+codigo+"]", $( "#listaComponentesIC" ) ).attr("ele"+codigo);
							nombreArticuloAlert = $( "[tdelegen"+idListaLev+"]", $( "#listaComponentesIC" ) ).html();
						}

						jConfirm('El medicamento ' + nombreArticuloAlert + ' ya existe. Desea agregarlo?','ALERTA',function(x){

							if(!x){
								var trEliminar = document.getElementById("trFil"+idx);
								cntDetalleKardex.removeChild(trEliminar);

								objDfr.resolve( false );

								eliminarArtIgualesMultiples( codigo );
								agregarMultiplesArticulos();
								
								quitarArticuloDextrometer( frecuenciaFija, codigo );
							}
							else{
								objDfr.resolve( true );
							}
						});
						$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
					}
				}
				else{
					var objDfr = $.Deferred();

					jConfirm('El medicamento ' + nombreArticuloAlert + ' ya existe. Desea agregarlo?','ALERTA',function(x){

						if(!x){
							var trEliminar = document.getElementById("trFil"+idx);
							cntDetalleKardex.removeChild(trEliminar);

							objDfr.resolve( false );

							eliminarArtIgualesMultiples( codigo );
							agregarMultiplesArticulos();
							
							quitarArticuloDextrometer( frecuenciaFija, codigo );
						}
						else{
							objDfr.resolve( true );
						}
					});
					$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
				}
			}
		
			//Se le agrega la tarifa del paciente
			document.getElementById("wdrautorizado"+tipoProtocoloAux+idx).value = conTarifa;
		
			$.when( objDfr.promise() ).then(function(x){

				if( !x ){
					return;
				}

				//Creo obejto deferred
				var objDfr = $.Deferred();
				objDfr.resolve( true );

				var nombreFamilia = document.getElementById( "wnombrefamilia" ).value;
				if(existe && horarioEspecial && contHorarioEspecial==1){
					preguntarFamilia = false;

					var objDfr = $.Deferred();

					jConfirm('El medicamento ' + nombreArticuloAlert + ' ya existe. Desea agregarlo?','ALERTA',function(x){

						if(!x){
							var trEliminar = document.getElementById("trFil"+idx);
							cntDetalleKardex.removeChild(trEliminar);

							objDfr.resolve( false );
							eliminarArtIgualesMultiples( codigo );
							agregarMultiplesArticulos();
							
							quitarArticuloDextrometer( frecuenciaFija, codigo );
						}
						else{
							objDfr.resolve( true );
						}
					});
					$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
				}
				else{

					if($("#frecuencia_elegida").val() == "H.E."){

						preguntarFamilia = false;
					}

					if(existe && horarioEspecial && contHorarioEspecial>1){
						preguntarFamilia = false;
					}

					var preguntaAgregados = false;
					var nombreFamilia = document.getElementById( "wnombrefamilia" ).value;		// Familia de medicamentos
					$( "#tbDetalleN > tbody > tr > td > b" ).each(function(){

						if( preguntarFamilia && nombreFamilia != "" ){
							//Verifico que no halla una familia agregada
							if( $.trim( $( this ).text().split( " - " )[1] ) == nombreFamilia ){
								preguntaAgregados = true;
								preguntarPrincipioActivo = false;
								preguntarFamilia = false;

								objDfr = $.Deferred();
								jConfirm('La familia ' + nombreFamilia.toUpperCase() + ' ya existe. Desea agregarlo?','ALERTA',function(x){

									if(!x){
										var trEliminar = document.getElementById("trFil"+idx);
										cntDetalleKardex.removeChild(trEliminar);

										objDfr.resolve( false );

										eliminarArtIgualesMultiples( codigo  );
										agregarMultiplesArticulos();
										
										quitarArticuloDextrometer( frecuenciaFija, codigo );
									}
									else{
										objDfr.resolve( true );
									}
								});
								$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
							}
						}
					});


					if($("#frecuencia_elegida").val() == "H.E."){

						preguntarFamilia = false;
					}

					try{
						if( !preguntaAgregados && preguntarFamilia ){
							if( familiasAgregadas
								&& familiasAgregadas[ nombreFamilia.toUpperCase() ]
								//&& $( "#detKardexAddN > tr > td > input[name^=wnmmed][value^="+codigo+"]" ).length > 0
							){

								preguntarFamilia = false;
								for( var indexFamily = 0; indexFamily < familiasAgregadas[ nombreFamilia.toUpperCase() ].length; indexFamily++ )
								{
									if( $( "input[name^=wnmmed][value^="+familiasAgregadas[ nombreFamilia.toUpperCase() ][indexFamily]+"]",$( "#detKardexAddN > tr > td" ) ).length > 0 )
									{
										preguntarFamilia = true;
										break;
									}
								}

								if( preguntarFamilia && nombreFamilia != "" )
								{
									preguntarPrincipioActivo = false;

									objDfr = $.Deferred();
									jConfirm('La familia ' + nombreFamilia.toUpperCase() + ' ya existe. Desea agregarlo?','ALERTA',function(x){

										if(!x){
											var trEliminar = document.getElementById("trFil"+idx);
											cntDetalleKardex.removeChild(trEliminar);

											objDfr.resolve( false );
											agregarMultiplesArticulos();
											
											quitarArticuloDextrometer( frecuenciaFija, codigo );
										}
										else{
											objDfr.resolve( true );
										}
									});
									$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"})
								}
							}
						}
					}
					catch(e){}

					if( codPrincipioActivo != '' && preguntarPrincipioActivo ){

						var pActivos = codPrincipioActivo.split( "," );
						var desPActivos = desPrincipioActivo.split( "," );
						var msgPActivos = "";

						for( var iPActivos=0; iPActivos < pActivos.length; iPActivos++ ){

							//Expresino regular que dice si existe el codigo de principio activo en la familia
							var patt = new RegExp("\\b"+$.trim( pActivos[iPActivos] )+"\\b", "igm" );
							var pattPac = new RegExp("\\b"+$.trim( desPActivos[iPActivos] )+"\\b", "igm");

							$( "[pActivo]" ).each(function(k){

								if( $( this ).attr( "pActivo" ) != '' ){

									//Prueba si el codigo existe en las familias
									if( patt.test( $( this ).attr( "pActivo" ) ) && !pattPac.test( msgPActivos ) ){
										msgPActivos = msgPActivos + "Ya se han agregrado articulos con el principio activo "+$.trim( desPActivos[iPActivos] )+"\n";
									}
								}
							});
						}

						if( $.trim( msgPActivos ) != "" ){

							objDfr = $.Deferred();

							$.alerts.okButton = "Sí";		//Dejo por defecto el valor sí para el boton de aceptar
							$.alerts.cancelButton = "No";	//Dejo por defecto el valor no para el boton cancelar
							jConfirm(msgPActivos+"Desea agregarlo?",'ALERTA',function(x){

								if(!x){
									var trEliminar = document.getElementById("trFil"+idx);
									cntDetalleKardex.removeChild(trEliminar);

									objDfr.resolve( false );

									eliminarArtIgualesMultiples( codigo,'', codPrincipioActivo );
									agregarMultiplesArticulos();
									
									quitarArticuloDextrometer( frecuenciaFija, codigo );
								}
								else{
									objDfr.resolve( true );
								}
							});
							$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"})
						}
					}
				}

				$.when( objDfr.promise() ).then(function(x){

					if( !x ){
						return;
					}
					
					/************************************************************************************************************************
					 *
					 ************************************************************************************************************************/
					
					
					var objDfr = $.Deferred();
					
					if( exist_fam_atc ){
						
						var txtFamsATC 		= "";
						var txtArtsFamsATC 	= "";
						$( famsATCexist ).each(function(x){
							if( x > 0 ) txtFamsATC += " y ";
							txtFamsATC += this.codigo+"-"+this.descripcion;
							
							$( "[id^=watc][data-atc-"+this.codigo+"]" ).each(function(){
								
								let codartatcrep = $( "#wnmmed"+$( this )[0].id.substr( "watc".length ) ).val() || $( "#wnmmed"+$( this )[0].id.substr( "watc".length ) ).html() || null;
								console.log( codartatcrep )
								if( codartatcrep ){
									
									let codigo 		= $( this ).val().split("-")[0];
									let descripcion = $( this ).val().split("-")[2];
									txtArtsFamsATC += "<tr><td>"+codigo+"</td><td>"+descripcion+"</td></tr>";
								}
							})
						})
						
						if( txtArtsFamsATC != '' ){
							txtArtsFamsATC = "<table class='clfamatctab'>"+txtArtsFamsATC+"</table>"
						}
						
						jConfirm( "El paciente ya tiene ordenado al menos un medicamento del mismo grupo terapeútico <b>("+txtFamsATC+")</b><br>"+txtArtsFamsATC+"Desea agregar el medicamento?","ALERTA DUPLICIDAD TERAPEUTICA",function(x){
							if(!x){
								var trEliminar = document.getElementById("trFil"+idx);
								cntDetalleKardex.removeChild(trEliminar);

								objDfr.resolve( false );
								agregarMultiplesArticulos();
								
								quitarArticuloDextrometer( frecuenciaFija, codigo );
							}
							else{
								objDfr.resolve( true );
							}
						});
						$( "h1", $( "#popup_container" ) )
							.addClass("blink")
							.css({background:"#feaaa4", color:"black"})
					}
					else{
						objDfr.resolve( true );
					}
					/************************************************************************************************************************/
					
					$.when( objDfr.promise() ).then(function(x){
						
						var objDfr = $.Deferred();
						
						if(x)
						{
							if( conTarifa != 'on' )
							{
								$('#taJusParaArtsSinTarifas').val('');
								
								$.blockUI({ message: $('#question'), css: { width: '500px' } });

								$('#btnAceptarAST')
									.attr({disabled:true})
									.off( "click" )
									.click(function() {
										$( "#wjusparaautorizar"+tipoProtocoloAux+idx ).val( $('#taJusParaArtsSinTarifas').val() );
										
										// update the block message
										objDfr.resolve( true );
											
										$.unblockUI();
										
										$( this ).off( "click" );
										
										return false;
										
									});
									
								
								var esDelGrupoGenerico = null;
								try{
									var esDelGrupoGenerico = grupoGenerico == undefined ? null : grupoGenerico;
								}
								catch(e){}
								
								
								$('#btnCerrarAST')
									.off( "click" )
									.click(function() {
										objDfr.resolve( false );
											
										var trEliminar = document.getElementById("trFil"+idx);
										cntDetalleKardex.removeChild(trEliminar);

										$.unblockUI();
										
										$( this ).off( "click" );
										
										if( esDelGrupoGenerico && $.trim( esDelGrupoGenerico ) != '' ){
											// cancerlarModalArticulosLEVIC();
											console.log( document.getElementById('indiceArticuloComponentes').value );
											quitarArticulo( document.getElementById('indiceArticuloComponentes').value, 'LQ','','detKardexAddLQ', 'LQ');
										}
										
										return false;
									});
							}
							else{
								objDfr.resolve( true );
							}
						}
						else{
							objDfr.resolve( true );
						}
						
						$.when( objDfr.promise() ).then(function(x){
							
							if(!adicionar){

								$( "#trFil"+idx ).attr( "pActivo", codPrincipioActivo );

								var informacion = " - <div style='font-family:verdana;font-size:10pt'><b>"+nombreComercial+"</b>";
								informacion += "<br><br><b>Nombre Comercial:</b> " + nombreGenerico;
								// informacion += "<br><br>"+ cantidadDosisFija +" "+ $( "[value="+unidadFraccion+"]", $( "#wmfftica" ) ).text() + " POR " + $( "[value="+unidad+"]", $( "#wmfftica" ) ).text();
								informacion += "</div>";
								informacion = informacion.replace( /_+/g, " " );
								$( "#wcolmed"+tipoProtocoloAux+idx ).attr( "title", informacion );
								$( "#wcolmed"+tipoProtocoloAux+idx ).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });

								//Creo variable nueva que indica que tipo de protocolo le corresponde la familia
								if( !familiasAgregadasIdx[ tipoProtocoloAux+idx ] ){
									familiasAgregadasIdx[ tipoProtocoloAux+idx ] = new Array();
								}

								familiasAgregadasIdx[ tipoProtocoloAux+idx ] = nombreFamilia.toUpperCase();

								//creo un array global, cada vez que se adicione un medicamentos queda la familia y el codigo del medicamento
								//Esto para que al momento de adicionar un medicamento se pueda verificar si ya se agrego la familia o no
								if( !familiasAgregadas[ nombreFamilia.toUpperCase() ] ){
									familiasAgregadas[ nombreFamilia.toUpperCase() ] = new Array();
								}

								familiasAgregadas[ nombreFamilia.toUpperCase() ][ familiasAgregadas[ nombreFamilia.toUpperCase() ].length ] = codigo;

								var mCantDosis = cantFracciones;
								var mUnidadDosis = unidadFraccion;
								var mDiasTto = diasMaximos;
								var mDosisTto = dosisMaximas;
								var mVia = via;

								var mNoEnviar = "";
								var mFrecuencia = "";
								var mCondicion = "";
								var mConfirmaPreparacion = "";
								var mObservaciones = "";

								mNoEnviar = noEnviarFija;
								mFrecuencia = frecuenciaFija;
								mCondicion = condicionSuministroFija;
								mConfirmaPreparacion = confirmadaPreparacionFija;
								mObservaciones = observacionesFija;

								//Marca de no enviar
								if(document.getElementById("wchkdisp"+tipoProtocoloAux+idx))
								{
									document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = false;

									//Enero 29 de 2013
									// Si es un insumo no dispensable marquese como no enviar
									if(mNoEnviar == "on")
										document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = true;

									//Abril 25 de 2011
									//Si es ustock marquese como no enviar
									if( noEnviar == 'on' )
										document.getElementById("wchkdisp"+tipoProtocoloAux+idx).checked = true;
								}

								//Frecuencia
								if(document.getElementById("wperiod"+tipoProtocoloAux+idx) && mFrecuencia != ""){
									document.getElementById("wperiod"+tipoProtocoloAux+idx).value = mFrecuencia;
								}

								//Condicion
								if(document.getElementById("wcondicion"+tipoProtocoloAux+idx) && mCondicion != ""){
									document.getElementById("wcondicion"+tipoProtocoloAux+idx).value = mCondicion;
								}

								//Confirma preparación
								if( $( "#enUrgencias" ).val() == 'on' ){
									document.getElementById("wchkconf"+tipoProtocoloAux+idx).checked = true;
								}
								else if(origen == 'CM' && document.getElementById("wchkconf"+tipoProtocoloAux+idx) && mConfirmaPreparacion != ""){
									if(mConfirmaPreparacion == "on"){
										document.getElementById("wchkconf"+tipoProtocoloAux+idx).checked = true;
									}
									else{
										document.getElementById("wchkconf"+tipoProtocoloAux+idx).checked = false;
									}
								}

								//Observaciones
								if(document.getElementById("wtxtobs"+tipoProtocoloAux+idx)&& mObservaciones != ""){
									document.getElementById("wtxtobs"+tipoProtocoloAux+idx).value = mObservaciones;
								}

								//Si el articulo tiene parametros preestablecidos, se cargan en el detalle
								if(cantidadDosisFija != ""){
									mCantDosis = cantidadDosisFija;
								}

								if(unidadDosisFija != ""){
									mUnidadDosis = unidadDosisFija;
								}

								if(diasMaximosFija != ""){
									mDiasTto = diasMaximosFija;
								}

								if(dosisMaximasFija != ""){
									mDosisTto = dosisMaximasFija;
								}

								if(viasAdministracionFija != ""){
									mVia = viasAdministracionFija;
								}

								if(fechaInicioFija && fechaInicioFija!=""){
									mFechaInicio = fechaInicioFija;
								}

								//Abre la ventana emergente
								// if(abreVentanaFija == "S" && componentesTipo != "" || tipoMedLiquido == "L"){
								if(abreVentanaFija == "S" && componentesTipo != "" || abreVentanaFija == "S" && infoNutriciones != "" || tipoMedLiquido == "L"){
									// if(mFrecuencia!="H.E.")
									if( $( "#wfrecuencia" ).val() !="H.E.")
									{
										if(componentesTipo != "")
										{
											abrirModalArticulos(codigo,nombreComercial,nombreGenerico,tipoMedLiquido,esGenerico,idx,componentesTipo,tipoProtocoloAux);
										}
										else if(infoNutriciones != "")
										{
											// abrirModalNutriciones(idx,tipoProtocoloAux);
											abrirModalNutriciones(idx,tipoProtocoloAux,"nuevo");
										}
									}
									else
									{
										strPendientesModal += 'articulo|'+codigo+'|'+nombreComercial+'|'+nombreGenerico+'|'+tipoMedLiquido+'|'+esGenerico+'|'+idx+'|'+componentesTipo+'|'+tipoProtocoloAux+'\r\n';
									}
									
									// if( $( "#wfrecuencia" ).val() !="H.E.")
										// abrirModalArticulos(codigo,nombreComercial,nombreGenerico,tipoMedLiquido,esGenerico,idx,componentesTipo,tipoProtocoloAux);
									// else
										// strPendientesModal += 'articulo|'+codigo+'|'+nombreComercial+'|'+nombreGenerico+'|'+tipoMedLiquido+'|'+esGenerico+'|'+idx+'|'+componentesTipo+'|'+tipoProtocoloAux+'\r\n';
								}

								if(elemento.tagName != 'DIV'){

									//En esta seccion se dan los posibles avisos
									//Grupo de control
									var nombreArticuloGrabar = nombreComercial;

									if(grupo == 'CTR'){
										//Verifica si se genera la formula de control de forma automatica.
										if($("#medicamentosControlAuto").val() == 'on'){
											jAlert("Este medicamento se encuentra en el grupo de control.  La fórmula de control se generará automaticamente al grabar.","ALERTA",function(){
												preguntarPorVisaulizarMedControl = true;
												if( $( "#inCIE10" ).val() == '' )
													$.blockUI({ message: $( "#dvCIE10" ) });
											});
										}
										else{
											jAlert("Este medicamento se encuentra en el grupo de control.  Recuerde diligencia el formato manual de medicamentos de control.","ALERTA");
										}

										nombreArticuloGrabar = nombreComercial.replace(/_/g," ") + " (de control)";
									}
									else{
										//Se elimna este campo por que solo es necesario si es un articulo de control
										$( "#wcontrolimp"+tipoProtocoloAux+idx ).remove();
										
										nombreArticuloGrabar =  nombreComercial.replace(/_/g," ");
									}
									elemento.value = codigo + "-" + origen + "-" + nombreArticuloGrabar;

									//Se agrega atributo value al input para que se pueda comparar con otros en acciones posteriores.
									$( elemento ).attr("value", codigo + "-" + origen + "-" + nombreArticuloGrabar );

									// Se establece si la cadena msjNoPos ya tiene el codigo del artículo actual
									var avisoNoPos = msjNoPos.indexOf(codigo)
									msjNoPos += codigo+',';

									//Si no tiene fracciones en la unidad de manejo ni unidad de fraccion se avisa
									if( (mUnidadDosis == '' || mCantDosis == '') && !esLiquidoEndovenoso ){
										alert('El articulo con codigo ' + codigo + ' no tiene unidad de fraccion o fracciones por unidad de manejo.  Por favor notifique a servicio farmaceutico.');
									}

									//Habilita o inhabilita la marca de confirmacion de preparacion
									if(origen != 'CM' && document.getElementById("wchkconf"+tipoProtocoloAux+idx)){
										document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = true;
									}
									else{
										if(document.getElementById("wchkconf"+tipoProtocoloAux+idx))
											document.getElementById("wchkconf"+tipoProtocoloAux+idx).disabled = false;
									}

									//Unidad dosis
									if(document.getElementById("wudosis"+tipoProtocoloAux+idx)){
										document.getElementById("wudosis"+tipoProtocoloAux+idx).value = mUnidadDosis;
									}

									//Unidad manejo
									if(document.getElementById("wcundmanejo"+tipoProtocoloAux+idx)){
										document.getElementById("wcundmanejo"+tipoProtocoloAux+idx).value = unidad;
									}

									//Cantidad maxima de fracciones en la unidad de manejo
									if(document.getElementById("wdosis"+tipoProtocoloAux+idx)){
										document.getElementById("wdosis"+tipoProtocoloAux+idx).value = mCantDosis;
										// 2012-06-27
										// Se adiciona el atributo "readonly" para que este campo siempre sea de solo lectura, que no se pueda modificar
										if(!adicionMultiple)
											document.getElementById("wdosis"+tipoProtocoloAux+idx).readOnly = true;
									}

									//Oculto para maximo de unidades del articulo
									if(document.getElementById("whcmanejo"+tipoProtocoloAux+idx)){
										document.getElementById("whcmanejo"+tipoProtocoloAux+idx).value = mCantDosis;
									}

									//Oculto vence
									if(document.getElementById("whvence"+tipoProtocoloAux+idx)){
										document.getElementById("whvence"+tipoProtocoloAux+idx).value = vencimiento;
									}

									//Oculto para maximo de unidades del articulo
									if(document.getElementById("whartpro"+tipoProtocoloAux+idx)){
										document.getElementById("whartpro"+tipoProtocoloAux+idx).value = tipoProtocolo;
									}

									//La forma farmaceutica se graba igual, mas no se muestra en el kardex
									if(document.getElementById("wfftica"+tipoProtocoloAux+idx)){
										document.getElementById("wfftica"+tipoProtocoloAux+idx).value = forma;
									}

									//Oculto dias vencimiento
									if(document.getElementById("whdiasvence"+tipoProtocoloAux+idx)){
										document.getElementById("whdiasvence"+tipoProtocoloAux+idx).value = diasVencimiento;
									}

									//Oculto dispensable
									if(document.getElementById("whdispensable"+tipoProtocoloAux+idx)){
										document.getElementById("whdispensable"+tipoProtocoloAux+idx).value = dispensable;
									}

									//Oculto duplicable
									if(document.getElementById("whduplicable"+tipoProtocoloAux+idx)){
										document.getElementById("whduplicable"+tipoProtocoloAux+idx).value = duplicable;
									}

									//Asigno dias maximos
									if(document.getElementById("wdiastto"+tipoProtocoloAux+idx) && parseInt(mDiasTto) > 0){
										if( !esAntibiotico )
											document.getElementById("wdiastto"+tipoProtocoloAux+idx).value = mDiasTto;
									}
									else{
										document.getElementById("wdiastto"+tipoProtocoloAux+idx).value = '';
									}

									//Asigno dosis maximas
									if(document.getElementById("wdosmax"+tipoProtocoloAux+idx) && parseInt(mDosisTto) > 0){
										document.getElementById("wdosmax"+tipoProtocoloAux+idx).value = mDosisTto;
									}
									else{
										document.getElementById("wdosmax"+tipoProtocoloAux+idx).value = '';
									}

									//Asigno via
									if(document.getElementById("wviadmon"+tipoProtocoloAux+idx)){
										document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = mVia;
									}
									else{
										document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = '';
									}

									if(document.getElementById("wfinicio"+tipoProtocoloAux+idx) && mFechaInicio){
										document.getElementById("wfinicio"+tipoProtocoloAux+idx).value = mFechaInicio;
									}
									// else
									//	document.getElementById("wviadmon"+tipoProtocoloAux+idx).value = '';
									//
									if(document.getElementById("whfinicio"+tipoProtocoloAux+idx) && mFechaInicio){
										document.getElementById("whfinicio"+tipoProtocoloAux+idx).value = mFechaInicio;
									}
									
									//Si se agrega el articulo por protcolo el campo wporprotocolo queda activo
									if( porProtocolo ){
										$( "#wporprotocolo"+tipoProtocoloAux+idx ).val('on');
										$( "#wperiod"+tipoProtocoloAux+idx ).attr({disabled:false});
									}
									
									$( "#wnmmed"+tipoProtocoloAux+idx ).attr( "pos", pos );
									
									$( famsAtc ).each(function(){
									
										$( "#watc"+tipoProtocoloAux+idx ).attr( "data-atc-"+this.codigo, this.descripcion );
										$( "#watc"+tipoProtocoloAux+idx ).val( elemento.value );
									})
									
									//Dosis máxima por profilaxis
									//var dmaxProfilaxis = 24/valFrecuencia[ $( "#wperiod"+tipoProtocoloAux+idx ).val() ];
									
									if( esAntibiotico ){
										
										$( "#trPrescripcionPediatrica" ).css({ display: '' });
										if( !esCompuesto )
											$( "#trPrescripcionPediatrica" ).css({ display: 'none' });
										
										$( "#wesantibiotico"+tipoProtocoloAux+idx ).val( 'on' );
										$( "#wdosmax"+tipoProtocoloAux+idx ).prop({readOnly:true});
										$( "#wdosmax"+tipoProtocoloAux+idx ).attr({readOnly:true});
										$( "#wdosmax"+tipoProtocoloAux+idx ).val('');
										
										//No permito que la dosis máxima sea superior a la 24/frecuencia
										$( "#wdosmax"+tipoProtocoloAux+idx ).change(function(){
											if( $( "#wprofilaxis"+tipoProtocoloAux+idx )[0].checked ){
												var dmaxSup = 24/valFrecuencia[ $( "#wperiod"+tipoProtocoloAux+idx ).val() ];
												if( dmaxSup <= 1 ) dmaxSup = 1;
												if( $( this ).val() > dmaxSup ){
													jAlert( "La dosis máxima no debe ser superior a "+dmaxSup, "ALERTA" );
													$( this ).val( dmaxSup );
												}
											}
										});
										
										//No permito que la dosis máxima sea superior a la 24/frecuencia
										$( "#wdiastto"+tipoProtocoloAux+idx ).change(function(){
											if( $( "#wprofilaxis"+tipoProtocoloAux+idx )[0].checked ){
												var dmaxSup = 1;
												if( $( this ).val() > dmaxSup ){
													console.log( $( this ).val() );
													jAlert( "Los d&iacute;as de tratamiento no puede ser superior a "+dmaxSup, "ALERTA" );
													$( this ).val( dmaxSup );
												}
											}
										});
									}
									else
										$( "#wesantibiotico"+tipoProtocoloAux+idx ).val( 'off' );
									
									if( !esAntibiotico ){
										$( "[name=wfiltroantibiotico"+tipoProtocoloAux+idx+"]" ).css({display:"none"});
										$( "[name=wfiltroantibiotico"+tipoProtocoloAux+idx+"]" ).parent().find("label").css({display:"none"});
									}
									
									$( "#wprofilaxis"+tipoProtocoloAux+idx ).attr('onClick',"dttoPorFiltroAntibiotico(this,'"+tipoProtocoloAux+idx+"','1','"+(mDosisTto*1)+"')");
									// $( "#wprofilaxis"+tipoProtocoloAux+idx ).attr('onClick',"dttoPorFiltroAntibiotico(this,'"+tipoProtocoloAux+idx+"','1','0')");
									$( "#wtratamiento"+tipoProtocoloAux+idx ).attr('onClick',"dttoPorFiltroAntibiotico(this,'"+tipoProtocoloAux+idx+"','"+(mDiasTto*1)+"','"+(mDosisTto*1)+"')");

									//Vias
				//					debugger;
									document.getElementById("wviadmon"+tipoProtocoloAux+idx).innerHTML = "";
									if(via != ''){
										var opcionesMaestro = document.getElementById("wmviaadmon").options;

										var cont1 = 0;
										var splVia = via.split(",");

										/************************************************************************************
										 * Marzo 7 de 2011
										 ************************************************************************************/
										//Si tiene mas de una via se agregar un campo mas en blanco
										//para que agregue el option 'seleccione'
										if( splVia.length > 1 ){
											splVia = (","+via).split(",");
										}

										var opcionTmp = null;

										while(cont1 < opcionesMaestro.length){

											for( var i = 0 ; i < splVia.length; i++ ){

												if( splVia[i] == opcionesMaestro[cont1].value ){

													opcionTmp = document.createElement("option");

													opcionTmp.text = opcionesMaestro[cont1].text;
													opcionTmp.value = opcionesMaestro[cont1].value;

													document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);
												}
											}

											cont1++;
										}
										/************************************************************************************/

									}
									else{
										var opcionesMaestro = document.getElementById("wmviaadmon").options;

										var cont1 = 0;
										var splVia = mVia.split(",");
										var opcionTmp = null;

										while(cont1 < opcionesMaestro.length){
											opcionTmp = document.createElement("option");
											document.getElementById("wviadmon"+tipoProtocoloAux+idx).options.add(opcionTmp);

											opcionTmp.text = opcionesMaestro[cont1].text;
											opcionTmp.value = opcionesMaestro[cont1].value;

											cont1++;
										}
									}

									//Si es un articulo agregado por dextrometer
									//Se deja los campos correspondientes de solo lectura
									if( agregandoArticuloPorDextrometer ){
										
										var arrIds = [ "wperiod"+tipoProtocoloAux+idx, "wcondicion"+tipoProtocoloAux+idx, "wviadmon"+tipoProtocoloAux+idx ];
										$( arrIds ).each(function(){
											//this son los ids que se encuentran en el array y son textos
											var opVias = $( "#"+this+" option" );
											var valVia = $( "#"+this ).val();

											opVias.css({display:"none"});
											opVias.attr({disabled:true});

											//this para este caso es el codigo de la via
											//Se muestran solo las vias que puedan ser seleccionadas segun el medicamento
											opVias.filter( "[value="+valVia+"]" ).css({display:""});
											opVias.filter( "[value="+valVia+"]" ).attr({disabled:false});
										});

										//Las observaciones son iguales a las del dextrometer
										$( "#wtxtobs"+tipoProtocoloAux+idx ).val( $( "#txtDextrometer" ).val() );
										$( "#wtxtobs"+tipoProtocoloAux+idx ).attr({readonly:true});

										//No se puede editar dias de tratamiento
										$( "#wdiastto"+tipoProtocoloAux+idx ).attr({readonly:true});
									}
									
									borrarDosisMaximaPorFrecuencia( $( "#wperiod"+tipoProtocoloAux+idx ) );
									
									$("#wperiod"+tipoProtocoloAux+idx).change();
									$("#wcondicion"+tipoProtocoloAux+idx).change();
																
									if( esAntibiotico ){
										
										var perDmax = true; //$( "#waccN\\.13" ).val().split( "," )[0] == 'S' ? true: false;

										$( "#dvModalFiltroAntibiotico" ).html( $( "#dvFiltroAntibiotico" ).html());
										
										$( "#dvModalFiltroAntibiotico #bTituloFiltroAntibiotico" ).html( nombreArticuloAlert.toUpperCase() );
										
										$( "#dvModalFiltroAntibiotico #bFrecuencia" ).html( $( "#wperiod"+tipoProtocoloAux+idx+" option:selected" ).text().toUpperCase() );
										if( $( "#wcondicion"+tipoProtocoloAux+idx ).val() != '' )
											$( "#dvModalFiltroAntibiotico #bCondicion" ).html( $( "#wcondicion"+tipoProtocoloAux+idx+" option:selected" ).text().toUpperCase() );
										else
											$( "#dvModalFiltroAntibiotico #bCondicion" ).parent().css({display:"none"});
										
										var radioProfilaxis 	= $( "#ckFiltroProfilaxis", $( "#dvModalFiltroAntibiotico" ) );
										var radioTratamiento 	= $( "#ckFiltroTratamiento", $( "#dvModalFiltroAntibiotico" ) );
										var inFiltroDtto 		= $( "#dvModalFiltroAntibiotico input:text" ).eq(0);
										var inFiltroDmax 		= $( "#dvModalFiltroAntibiotico input:text" ).eq(1);
										
										var btnGrabar = $( "#dvModalFiltroAntibiotico input:button[value=Grabar]" );
										var btnCerrar = $( "#dvModalFiltroAntibiotico input:button[value=Cerrar]" );
										
										// radioProfilaxis.click(function(){
										// $( "#dvModalFiltroAntibiotico input:radio" ).click(function(){
										$( "[id^=ckFiltroProfilaxis]", $( "#dvModalFiltroAntibiotico" ) ).click(function(){
										
											if( mDiasTto*1 > 0 ){
												inFiltroDmax.val( '' );
												inFiltroDtto.val( mDiasTto );
												// inFiltroDtto.change();
											}
											else if(mDosisTto*1 > 0 ){
												inFiltroDtto.val( '' );
												inFiltroDmax.val( mDosisTto );
												// inFiltroDmax.change();
											}
											else if( this.id == 'ckFiltroProfilaxis' ){
												if( inFiltroDmax.val()*1 == 0 )
													inFiltroDtto.val( 1 );
												// inFiltroDtto.change();
											}
											
											if( inFiltroDmax.val()*1 > 0 ){
												inFiltroDmax.change();
											}
											else if( inFiltroDtto.val()*1 > 0 ){
												inFiltroDtto.change();
											}
										});
										
										//Asigno función al boton cerrar
										btnCerrar.click(function(){
											// $.unblockUI();
											// setTimeout( function(){ fnInternaCTC() }, 500 );
											jConfirm( "Está seguro que desea eliminar el artículo","ALERTA",function(x){
												if(x){
												$.unblockUI();
													setTimeout( function(){ agregarMultiplesArticulos(); }, 500 );
													quitarArticulo(idx, tipoProtocoloAux, document.getElementById( "wperiod"+tipoProtocoloAux+idx ), '', true );
												}
											});
										});
										
										if( esCompuesto ){
											$( '#wconmed1' + tipoProtocoloAux + idx ).val(conMedicamento1);
											$( '#wconmed2' + tipoProtocoloAux + idx ).val(conMedicamento2);
											
											$( "#wckpediatrico"+ tipoProtocoloAux + idx ).css({display:''});									
											$( "label", $( "#wckpediatrico"+ tipoProtocoloAux + idx ).parent() ).css({display: ''});
										}
										
										//Asigno función al boton Grabar
										btnGrabar.click(function(){
											
											$( "#wprofilaxis"+tipoProtocoloAux+idx )[0].checked = radioProfilaxis[0].checked;
											$( "#wtratamiento"+tipoProtocoloAux+idx )[0].checked = radioTratamiento[0].checked;
											$( "#wdiastto"+tipoProtocoloAux+idx ).val( inFiltroDtto.val() );
											$( "#wdosmax"+tipoProtocoloAux+idx ).val( inFiltroDmax.val() );
											
											var filtroMsg = "";
											var filtroValido = false;
											if( $( "#dvModalFiltroAntibiotico [name=ckFiltroAntibiotico]:checked" ).length > 0 ){
												if( inFiltroDtto.val()*1 > 0 || inFiltroDmax.val()*1 > 0  ){
													filtroValido = true;
												}
												else{
													if( inFiltroDtto.val()*1 <= 0 && inFiltroDmax.val()*1 <= 0 && perDmax )
														filtroMsg = "Debe ingresar un valor mayor a 0 para <b>DOSIS M&Aacute;XIMA</b> o <b>D&Iacute;AS DE TRATAMIENTO</b>";
													else if( perDmax && inFiltroDmax.val()*1 <= 0 )
														//Muestra el mensaje solo si tiene permiso para dosis máxima
														filtroMsg = "Debe ingresar un valor mayor a 0 para <b>DOSIS M&Aacute;XIMA</b>";
													else if( inFiltroDtto.val()*1 <= 0 )
														filtroMsg = "Debe ingresar un valor mayor a 0 para <b>D&Iacute;AS DE TRATAMIENTO</b>";
												}
											}
											else{
												filtroMsg = "Debe seleccionar si el antibiótico es por <b>PROFILAXIS</b> o <b>TRATAMIENTO</b>";
											}
											
											if( $( "#trPrescripcionPediatrica" ).css( "display" ) != 'none' ){
												if( $( "[name=rdPrescripcionPediatrica]:checked" ).length == 0 ){
													filtroValido = false;
													filtroMsg = "Por favor indique si la prescripcion es Pediatrica o no.";
												}
											}
											
											if( filtroValido ){
												
												if( $( "#rdPediatricoSi", $( "#dvModalFiltroAntibiotico" ) )[0].checked ){
													
													if( esCompuesto && conMedicamento1 && conMedicamento2 ){
														
														if( conMedicamento1*1 > 0 && conMedicamento2*1 > 0 ){
															
															$( '#wespediatrico' + tipoProtocoloAux + idx ).val( 'on' );
															$( '#wconmed1' + tipoProtocoloAux + idx ).val(conMedicamento1);
															$( '#wconmed2' + tipoProtocoloAux + idx ).val(conMedicamento2);
															$( '#wckpediatrico'+ tipoProtocoloAux + idx )[0].checked = true;
															
															$( "#wckpediatrico"+ tipoProtocoloAux + idx ).click(function(){
																if( this.checked ){
																	$( '#wespediatrico'+ tipoProtocoloAux + idx ).val( 'on' );
																}
																else{
																	$( '#wespediatrico'+ tipoProtocoloAux + idx ).val( 'off' );
																}
															}).css({display:''});
															
															$( "label", $( "#wckpediatrico"+ tipoProtocoloAux + idx ).parent() ).css({display: ''});
														}
													}
												}
												
												$.unblockUI();
												setTimeout( function(){ fnInternaCTC() }, 500 );
											}
											else{
												jAlert( filtroMsg, "ALERTA" );
											}
										});
										
										//Si tiene permisos para dosis máxima, permito escribir las dosis máxima
										if( !perDmax ){
											inFiltroDmax.attr({
												disabled:true,
												readOnly:true,
											});
										}
										//No permito que la dosis máxima sea superior a la 24/frecuencia
										//Asigno el evento change al filtro de Dosis máxima
										inFiltroDmax.change(function(){
											
											$( this ).css({ disabled: false });
											
											var dmaDef = false;
											try{
												dmaDef = dmaPorFrecuencia[ $( "#wperiod"+tipoProtocoloAux+idx ).val() ].dma;
											}
											catch(e){
												dmaDef = false;
											}
											
											if( !dmaDef ){
												try{
													dmaDef = dmaPorCondicionesSuministro[ $( "#wcondicion"+tipoProtocoloAux+idx ).val() ].dma;
												}
												catch(e){
													dmaDef = false;
												}
											}
											
											if( dmaDef && dmaDef*1 > 0 ){
												$( this ).val( dmaDef*1 );
												$( this ).attr({ disabled: true });
											}
											
											if( radioProfilaxis[0].checked ){
												var dmaxSup = 24/valFrecuencia[ $( "#wperiod"+tipoProtocoloAux+idx ).val() ];
												if( dmaxSup <= 1 ) dmaxSup = 1;
												if( dmaDef && dmaDef*1>0){
													dmaxSup = Math.min(dmaDef*1,dmaxSup);
												}
												if( $( this ).val() > dmaxSup ){
													jAlert( "La dosis máxima no debe ser superior a "+dmaxSup, "ALERTA" );
													$( this ).val( dmaxSup );
												}
											}
											
											if( $(this).val() != '' ){
												inFiltroDtto.attr({readOnly: true});
											}
											else{
												inFiltroDtto.attr({readOnly: false});
											}
										});
										
										//No permito que la dosis máxima sea superior a la 24/frecuencia
										//Asigno el evento change al filtro de Días de tratamiento
										inFiltroDtto.change(function(){
											
											$( this ).attr({ readOnly: false });
											
											var dmaDef = false;
											try{
												dmaDef = dmaPorFrecuencia[ $( "#wperiod"+tipoProtocoloAux+idx ).val() ].dma;
											}
											catch(e){
												dmaDef = false;
											}
											
											if( !dmaDef ){
												try{
													dmaDef = dmaPorCondicionesSuministro[ $( "#wcondicion"+tipoProtocoloAux+idx ).val() ].dma;
												}
												catch(e){
													dmaDef = false;
												}
											}
											
											if( ( dmaDef && dmaDef*1 > 0 ) || inFiltroDmax.val()*1 > 0 ){
												$( this ).val( '' );
												$( this ).attr({ readOnly: true });
											}
											
											if( radioProfilaxis[0].checked ){
												// $( this ).val(1);
												var dmaxSup = 1;
												if( $( this ).val()*1 > dmaxSup ){
													jAlert( "Los d&iacute;as de tratamiento no puede ser superior a "+dmaxSup, "ALERTA" );
													$( this ).val( dmaxSup );
												}
											}
											
											if( $(this).val() != '' ){
												inFiltroDmax.attr({readOnly: true});
											}
											else{
												inFiltroDmax.attr({readOnly: false});
											}
										});
										
										inFiltroDtto.change();
										inFiltroDmax.change();
										
										$.blockUI({ message: $( "#dvModalFiltroAntibiotico" ) });
									}
									else
										fnInternaCTC();
										// agregarMultiplesArticulos();
									
									
									/**
									 * Valida si el medicamento es de CTC o no
									 */
									function fnInternaCTC(){
										var conctc = false;
										//Paciente No pos
										if( $( "#pacEPS" ).val() == 'on' ){

											if(avisoNoPos == -1 && pos == 'N'){

												if( $( "#esMedico" ).val() == 'on' ){
													// var entidad_responsable = $("#entidad_responsable").val(); //Entidad responsable del paciente.
													// var datos_entidad_responsable = entidad_responsable.split("-"); //Nit y digito de verificacion.
													var array_entidadesCTC = Array();
													// var nit_entidad_resp = datos_entidad_responsable[0]; //Nit sin el digito de verificacion.
													
													var nit_entidad_resp = $("#entidad_responsable").val(); //Entidad responsable del paciente.

													var entidadesConfirmanCTC = $("#entidades_confirmanCTC").val(); //Nit de entidades que confirman CTC.
													var array_entidadesCTC = entidadesConfirmanCTC.split(","); //Array de entidades que confirman CTC.

													var validar_entidad = jQuery.inArray(nit_entidad_resp, array_entidadesCTC); //Verifica si el array de entidades que confirman CTC esta la entidad responsable del paciente.
													conctc = true;
													//Si la respuesta es 0(cero) entonces el nit si s encuentra en las entidades que confirman CTC, entonces muestra un alert para llenarlo o no.
													if(validar_entidad != -1){

														var confirmaCTC = confirm("¿Desea realizar el CTC del medicamento para el paciente?");

														if(!confirmaCTC){
															$( "#wnmmed"+tipoProtocoloAux+idx ).attr( "pos", "P" );
															arCTCArticulos[ codigo ] = "artsinctc";
															conctc = false;
															return false;
														}
													}
													else{
														// alert("El medicamento " + nombreArticuloAlert + " es NO POS");
														if($( "#esContributivo" ).val())
														{
															alert("El medicamento " + nombreArticuloAlert + " es NO POS, al firmar la orden debe llenar el MIPRES en la plataforma del Ministerio de Salud");
														}
														else
														{
															alert("El medicamento " + nombreArticuloAlert + " es NO POS");
														}
													}
												}
												else{
													alert("El medicamento " + nombreArticuloAlert + " es NO POS");
												}
											}
										}

										if(pos == 'N'){

											if(conctc){
												if(!adicionMultiple)
												{
													// //if( !arCTCArticulos[ codigo ] ){
														// mostrarCtcArticulos2( codigo, tipoProtocoloAux, idx, deAlta );
													// //}
													
													//Validacion CTC contributivo para que pida el CTC sino que se abra la pagina del ministerio parar llenarlo
													if($( "#esContributivo" ).val())
													{
														// agregar medicamento a la cadena para mostrar el CTC al grabar y crear las notas medicas
														cadenaCTCcontributivo += "medicamento|"+codigo+"|"+$.trim(nombreArticuloAlert)+"|"+tipoProtocoloAux+"|"+idx+"***";
														
													}
													else
													{
														mostrarCtcArticulos2( codigo, tipoProtocoloAux, idx, deAlta );
													}
													
												}
												else
												{
													// strPendientesCTC += 'articulo|'+codigo+'|'+tipoProtocoloAux+'|'+idx+'\r\n';
													
													if($( "#esContributivo" ).val())
													{
														// agregar medicamento a la cadena para mostrar el CTC al grabar y crear las notas medicas
														cadenaCTCcontributivo += "medicamento|"+codigo+"|"+$.trim(nombreArticuloAlert)+"|"+tipoProtocoloAux+"|"+idx+"***";
													}
													else
													{
														strPendientesCTC += 'articulo|'+codigo+'|'+tipoProtocoloAux+'|'+idx+'\r\n';
													}
												}
											}
										}
										
										agregarMultiplesArticulos();
									}
								}
								else{
									jAlert('Debe agregar un nuevo articulo.', 'ALERTA');
								}
							}
							else {
								$.alerts.okButton = "Aceptar";		//Dejo por defecto el valor sí para el boton de aceptar
								$.alerts.cancelButton = "Cancelar";	//Dejo por defecto el valor no para el boton cancelar
								jAlert('El articulo ya se encuentra en la lista.  No se puede agregar porque esta configurado como no duplicable.', 'ALERTA');
								$( "#trFil"+idx ).remove();
							}
						});
					
					});
				});
			});
		}
		else{
			$.alerts.okButton = "Aceptar";		//Dejo por defecto el valor sí para el boton de aceptar
			$.alerts.cancelButton = "Cancelar";	//Dejo por defecto el valor no para el boton cancelar
			jAlert('No se encontro elemento a agregar.','Alerta');
		}
	}else{
		$.alerts.okButton = "Aceptar";		//Dejo por defecto el valor sí para el boton de aceptar
		$.alerts.cancelButton = "Cancelar";	//Dejo por defecto el valor no para el boton cancelar
		jAlert('Aun no ha agregado el primer articulo','Alerta');
	}

	//Se deja por defecto el nombre de los botones como aceptar y cancelar respectivamente para los confirms y alertas
	$.alerts.okButton = "Aceptar";		//Dejo por defecto el valor sí para el boton de aceptar
	$.alerts.cancelButton = "Cancelar";	//Dejo por defecto el valor no para el boton cancelar
}

function pulsar(e) {
	tecla=(document.all) ? e.keyCode : e.which;
  if(tecla==13) return false;
}


/*****************************************************************************************************************************
 * Limpia el resultado del buscador de examenes
 ******************************************************************************************************************************/
function limpiarBuscadorExamenes(){
	var resultados = document.getElementById('cntExamenes').innerHTML = "";
}
/*****************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function validarFirmaDigital(usuario,firma){
	var contenedor = document.getElementById('tdEstadoFirma');
	var parametros = "";
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	parametros = "consultaAjaxKardex=26&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&usuarioHce=" + usuario + "&firma=" + firma+ "&wemp_pmla=" + wemp_pmla;
	try{
		//$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				var clase = "";
				var mensaje = "";
				var campoFirma = "";

				if($.trim( ajax.responseText ) != ''){

					switch($.trim( ajax.responseText )){
						case '1':
							clase = "fondoVerde";
							campoFirma = firma;
							mensaje = " &nbsp; Firma Correcta ";
							$('.blink').stop(true, true).fadeOut();
							$('.blink').stop(true, true).fadeIn();

							deshabilitarBotonesFirmaDigitalHCE(false);
							break;
						case '2':
							clase = "fondoRojo";
							campoFirma = "";
							mensaje = "<div class='blink'> &nbsp; Firma Err&oacute;nea </div>";
							break;
						default:
							clase = "fondoRojo";
							campoFirma = "";
							mensaje = "<div class='blink'> &nbsp; Firma Err&oacute;nea </div>";
							break;
					}
					//Resultado
					contenedor.className = clase;
					contenedor.innerHTML = mensaje;
					//document.getElementById('pswFirma').value = campoFirma;
					document.getElementById('whfirma').value = campoFirma;
				}
			}
			//$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}
}
/*****************************************************************************************************************************
 * Apertura de ventana modal para combinar los liquidos y las nutriciones
 * abrirModalArticulos(codigo,nombreComercial.replace(/_/g," "),nombreGenerico.replace(/_/g," "),tipoMedLiquido,esGenerico,idx,componentesTipo)
 ******************************************************************************************************************************/
function abrirModalArticulos(){
//	debugger;

	if( arguments[0] != 'LQ0000' && arguments[0] != 'IC0000' ){
		$( "#dvMsgConfiguracion" ).html( '' );
		$( "#dvMsgConfiguracion" ).css({display:'none'});
		
		var codigo = arguments[0];
		var nombreComercial = arguments[1];
		var nombreGenerico = arguments[2];
		var tipo = arguments[3];
		var indiceArticulo = arguments[5];
		var componentesTipo = arguments[6];

		grupoGenerico = arguments[4];

		// alert(codigo+" ------- "+nombreComercial+" ------- "+nombreGenerico+" ------- "+tipo+" ------- "+grupoGenerico+" ------- "+indiceArticulo+" ------- "+componentesTipo);

		//Inicializo las variables de los componentes genericos
		document.getElementById('articuloComponentes').innerHTML = "";

		//Para cada componente arrojado en la consulta, asigno la siguiente estructura:
		var vecComponentes = componentesTipo.split(";");
		var cont1 = 0;
		var cont2 = 0;	//lleva la cuenta de los articulos impresos
		var referencia = "";
		var articulos = "<table align='center' border='0'>";
		var articulito = "";
		while(cont1 < vecComponentes.length){
			
			// articulito = vecComponentes[cont1].split("**");
			// if( articulito[6] == '' ){
				// if(articulito.length > 1 && articulito[2]){
					// referencia = "adicionarComponenteArticulo('"+articulito[0]+"','"+reemplazarTodo(articulito[2]," ","_")+"','"+cont1+"','"+articulito[1]+"',"+articulito[4]+",this);";
					// articulos += "<td><input type='checkbox' name='check_insumo"+cont1+"' id='check_insumo"+cont1+"' onClick="+referencia+"></td><td>"+articulito[3]+"</td><td><input type'text' size='2' name='cantidad_insumo"+cont1+"' id='cantidad_insumo"+cont1+"' onChange="+referencia+" /> "+articulito[5]+"</td>";
				// }
			// }
			// else{
				// $( "#dvMsgConfiguracion" ).html( articulito[6] );
				// $( "#dvMsgConfiguracion" ).css({display:''});
			// }
			
			
			// cont1++;
			
			if( cont2%2 == 0 )
				articulos += "<tr>";
			
			if( vecComponentes[cont1] ){
				articulito = vecComponentes[cont1].split("**");
				if( articulito[6] == '' ){
					if(articulito.length > 1 && articulito[2]){
						referencia = "adicionarComponenteArticulo('"+articulito[0]+"','"+reemplazarTodo(articulito[2]," ","_")+"','"+cont1+"','"+articulito[1]+"',"+articulito[4]+",this);";
						articulos += "<td><input type='checkbox' name='check_insumo"+cont1+"' id='check_insumo"+cont1+"' onClick="+referencia+"></td><td>"+articulito[3]+"</td><td><input type'text' size='2' name='cantidad_insumo"+cont1+"' id='cantidad_insumo"+cont1+"' onChange="+referencia+" /> "+articulito[5]+"</td>";
					}
					cont2++;
				}
				else{
					
					if( $( "#dvMsgConfiguracion" ).html() == '' )
						$( "#dvMsgConfiguracion" ).html( articulito[6] );
					else
						$( "#dvMsgConfiguracion" ).html( $( "#dvMsgConfiguracion" ).html()+"<br>"+articulito[6] );
					
					$( "#dvMsgConfiguracion" ).css({display:''});
				}
			}

			if( cont2 == 1 && vecComponentes.length > 1 ){
				articulos += "<td style='width:30px' rowspan='"+(Math.ceil( vecComponentes.length/2 ) )+"'></td>";
			}
			
			cont1++;
			
			if( cont2%2 == 0 || cont1 == vecComponentes.length )
				articulos += "</tr>";
			
		}
		document.getElementById('listaComponentes').innerHTML = articulos;

		articulos += "</table>";

		//Indice del articulo en el que se ponen los componentes des
		document.getElementById('indiceArticuloComponentes').value = indiceArticulo;
		document.getElementById('protocoloArticuloComponentes').value = arguments[7];

		$.blockUI({ message: $('#modalArticulos'),
							css: {
								overflow: 'auto',
								cursor	: 'auto',
								width	: "60%",
								left	: "20%",
								top		: ( $(window).height() - 300 )/2 +'px',
							} });
	}
	else{

		var indice = document.getElementById('indiceArticuloComponentes').value;
		var tipoProtocolo = document.getElementById('protocoloArticuloComponentes').value;

		if( arguments[0] == 'LQ0000' ){
			var codigo = arguments[0];
			var nombreComercial = arguments[1];
			var nombreGenerico = arguments[2];
			var tipo = arguments[3];
			var indiceArticulo = arguments[5];
			var componentesTipo = arguments[6];

			grupoGenerico = arguments[4];

			// alert(codigo+" ------- "+nombreComercial+" ------- "+nombreGenerico+" ------- "+tipo+" ------- "+grupoGenerico+" ------- "+indiceArticulo+" ------- "+componentesTipo);

			//Inicializo las variables de los componentes genericos
			document.getElementById('articuloComponentes').innerHTML = "";

			//Para cada componente arrojado en la consulta, asigno la siguiente estructura:
			var vecComponentes = componentesTipo.split(";");
			var cont1 = 0;
			var referencia = "";


			document.getElementById('listaComponentes').innerHTML = articulos;

			//Indice del articulo en el que se ponen los componentes des
			document.getElementById('indiceArticuloComponentes').value = indiceArticulo;
			document.getElementById('protocoloArticuloComponentes').value = arguments[7];


			//Dejo todos los valoes en blanco
			$('input:text, select', $('#listaComponentesLEV') ).val( '' );
			$('input:checkbox', $('#listaComponentesLEV') ).attr( "checked", false );

			//Muestro botones correspondientes
			$("#trNuevo", $( "#listaComponentesLEV" ) ).css( { display: "" } );
			// $("#trModificar", $( "#listaComponentesLEV" ) ).css( { display: "none" } );
			$("[trMod]", $( "#listaComponentesLEV" ) ).css( { display: "none" } );
			$("[trNew]", $( "#listaComponentesLEV" ) ).css( { display: "" } );

			//Observaciones
			$("#txObservacionesLEV", $( "#listaComponentesLEV" ) ).val( $( "#wtxtobservasiones" ).val() );

			$("#bLevFre", $( "#listaComponentesLEV" ) ).text( $( "option[value="+$( "#wperiod"+grupoGenerico+indiceArticulo ).val()+"]", $( "#wfrecuencia" ) ).text() );
			var arFinHin = $( "#wfinicio"+grupoGenerico+indiceArticulo ).val().split( "a las:" );
			$("#bLevFin", $( "#listaComponentesLEV" ) ).text( $.trim( arFinHin[0] ) );
			$("#bLevHin", $( "#listaComponentesLEV" ) ).text( $.trim( arFinHin[1] ) );

			/**********************************************************************
			 * Impido que los campos se puedan modificar
			 **********************************************************************/
			$( "input:checkbox,textarea,select", $( "#listaComponentesLEV" ) ).each(function(x){
				this.disabled = false;
			});

			$( "input:text", $( "#listaComponentesLEV" ) ).each(function(x){
				this.readOnly = false;
			});

			$( "#inDttoLEV", $( "#listaComponentesLEV" ) ).val( $( "#wdiastratamiento" ).val() );
			$( "#inDttoLEV", $( "#listaComponentesLEV" ) )[0].onkeyup();
			$( "#inDmaxLEV", $( "#listaComponentesLEV" ) ).val( $( "#wdosismaxima" ).val() );
			$( "#inDmaxLEV", $( "#listaComponentesLEV" ) )[0].onkeyup();
			/**********************************************************************/

			$( "[id^=volxdil]", $( "#listaComponentesLEV" ) ).attr({disabled:true});

			var canWidth = $(window).width()*0.8;
			if( $( "#listaComponentesLEV" ).width()-50 < canWidth )
				canWidth = $( "#listaComponentesLEV" ).width();

			var canHeight = $(window).height()*0.8;
			if( $( "#listaComponentesLEV" ).height()-50 < canHeight )
				canHeight = $( "#listaComponentesLEV" ).height();

			$.blockUI({ message: $("#listaComponentesLEV"),
				css: { left: ( $(window).width()-canWidth-50 )/2 +'px',
						top: ( $(window).height()-canHeight-50 )/2 +'px',
					  width: canWidth+25 + 'px',
					 height: canHeight+25 + 'px',
				   overflow: 'auto',
					 cursor: "point"
					 }
			});
		}
		else{

			var codigo = arguments[0];
			var nombreComercial = arguments[1];
			var nombreGenerico = arguments[2];
			var tipo = arguments[3];
			var indiceArticulo = arguments[5];
			var componentesTipo = arguments[6];

			grupoGenerico = arguments[4];

			// alert(codigo+" ------- "+nombreComercial+" ------- "+nombreGenerico+" ------- "+tipo+" ------- "+grupoGenerico+" ------- "+indiceArticulo+" ------- "+componentesTipo);

			//Inicializo las variables de los componentes genericos
			document.getElementById('articuloComponentes').innerHTML = "";

			//Para cada componente arrojado en la consulta, asigno la siguiente estructura:
			var vecComponentes = componentesTipo.split(";");
			var cont1 = 0;
			var referencia = "";


			document.getElementById('listaComponentes').innerHTML = articulos;

			//Indice del articulo en el que se ponen los componentes des
			document.getElementById('indiceArticuloComponentes').value = indiceArticulo;
			document.getElementById('protocoloArticuloComponentes').value = arguments[7];


			//Dejo todos los valoes en blanco
			$('input:text, select', $('#listaComponentesIC') ).val( '' );
			$('input:checkbox', $('#listaComponentesIC') ).attr( "checked", false );

			//Muestro botones correspondientes
			$("#trNuevo", $( "#listaComponentesIC" ) ).css( { display: "" } );
			// $("#trModificar", $( "#listaComponentesIC" ) ).css( { display: "none" } );
			$("[trMod]", $( "#listaComponentesIC" ) ).css( { display: "none" } );
			$("[trNew]", $( "#listaComponentesIC" ) ).css( { display: "" } );

			//Observaciones
			$("#txObservacionesIC", $( "#listaComponentesIC" ) ).val( $( "#wtxtobservasiones" ).val() );

			$("#bICFre", $( "#listaComponentesIC" ) ).text( $( "option[value="+$( "#wperiod"+grupoGenerico+indiceArticulo ).val()+"]", $( "#wfrecuencia" ) ).text() );
			var arFinHin = $( "#wfinicio"+grupoGenerico+indiceArticulo ).val().split( "a las:" );
			$("#bICFin", $( "#listaComponentesIC" ) ).text( $.trim( arFinHin[0] ) );
			$("#bICHin", $( "#listaComponentesIC" ) ).text( $.trim( arFinHin[1] ) );

			$("[id^=inICDca]", $( "#listaComponentesIC" ) ).each(function(x){
				this.disabled = false;
			});

			$("[id^=slICUdca]", $( "#listaComponentesIC" ) ).each(function(x){
				this.disabled = false;
			});

			$("[id^=slFrecDilLev]", $( "#listaComponentesIC" ) )[0].disabled = false;

			/**********************************************************************
			 * Impido que los campos se puedan modificar
			 **********************************************************************/
			$( "input:checkbox,textarea,select", $( "#listaComponentesIC" ) ).each(function(x){
				this.disabled = false;
			});

			$( "input:text", $( "#listaComponentesIC" ) ).each(function(x){
				this.readOnly = false;
				this.disabled = false;
			});

			$( "#inDttoIC", $( "#listaComponentesIC" ) ).val( $( "#wdiastratamiento" ).val() );
			$( "#inDttoIC", $( "#listaComponentesIC" ) )[0].onkeyup();
			$( "#inDmaxIC", $( "#listaComponentesIC" ) ).val( $( "#wdosismaxima" ).val() );
			$( "#inDmaxIC", $( "#listaComponentesIC" ) )[0].onkeyup();
			/**********************************************************************/

			$( "[id^=inICDca],[id^=slICUdca]", $("#listaComponentesIC") ).attr({disabled:true});
			$( "#txFrecDilLev", $("#listaComponentesIC") ).attr({disabled:false});

			var canWidth = $(window).width()*0.8;
			if( $( "#listaComponentesIC" ).width()-50 < canWidth )
				canWidth = $( "#listaComponentesIC" ).width();

			var canHeight = $(window).height()*0.8;
			if( $( "#listaComponentesIC" ).height()-50 < canHeight )
				canHeight = $( "#listaComponentesIC" ).height();

			$.blockUI({ message: $("#listaComponentesIC"),
				css: { left: ( $(window).width()-canWidth-50 )/2 +'px',
						top: ( $(window).height()-canHeight-50 )/2 +'px',
					  width: canWidth+25 + 'px',
					 height: canHeight+25 + 'px',
				   overflow: 'auto',
				     cursor: "point"
					 }
			});
		}
	}
}

function salirSinGrabarModalArticulosNPT(){
	
	var indice = document.getElementById('indiceArticuloComponentes').value;
	var tipoProtocolo = document.getElementById('protocoloArticuloComponentes').value;
	
	jConfirm( "No se grabará el medicamento en la ordenes. Desea continuar?", "ALERTA", function( resp ){
		if( resp ){
			quitarArticulo( indice,tipoProtocolo, '', 'detKardexAddU', true );
			$.unblockUI();
		}
	});
}

/*****************************************************************************************************************************
 * Cerrado de ventana modal para combinar los liquidos y las nutriciones
 ******************************************************************************************************************************/
function cerrarModalArticulos( tipo ){
//	debugger;

	var indice = document.getElementById('indiceArticuloComponentes').value;
	var tipoProtocolo = document.getElementById('protocoloArticuloComponentes').value;

	if(grupoGenerico != "LQ")
	{
		var cerrarModal = true;
		
		var cerrarModal = $( "[id^=check_insumo]:checked", $( "#modalArticulos" ) ).length > 0 ? true: false;
		cerrarModal = $( "[id^=check_insumo]", $( "#modalArticulos" ) ).length == 0 ? true: cerrarModal;
		
		if( cerrarModal ){	//aquí.......
			document.getElementById("wtxtobs"+tipoProtocolo+indice).value += document.getElementById("wcomponentesarticulo").value;
			document.getElementById("wtxtobs"+tipoProtocolo+indice).value = $.trim( document.getElementById("wtxtobs"+tipoProtocolo+indice).value );
			document.getElementById("wtxtobs"+tipoProtocolo+indice).style.width = "100%";
			document.getElementById("wcomponentesarticulo").value = "";
			document.getElementById("wcomponentesarticulocod").value = "";
			$.unblockUI();

			grupoGenerico = "";

			setTimeout("abrirModalMultiple()", 1000);
		}
		else{
			jAlert( "Debe seleccionar al menos un articulo", "ALERTA" );
		}
	}
	else
	{
		if( tipo != "IC" ){
			cerrarModalArticulosLEV( indice, tipoProtocolo );
		}
		else{
			cerrarModalArticulosIC( indice, tipoProtocolo );
		}

		return;
	}
}

/*****************************************************************
 * Función llamada desde cerrarModalArticulos
 *
 * Se usa para validar todas las acciones de la modal para LEVS
 *****************************************************************/
function cerrarModalArticulosLEV( indice, tipoProtocolo ){

	var todoOk = true;
	var msgError = "";

	/************************************************************************************************************************
	 * Valido que sea valido la selección de insumos
	 * - Se puede escoger un electrolito pero no debe haber nada escrito en en los campos vol/diligencia
	 * - Si se escoge un electrólito con vol/dil tiene que haber una solución elegida por lo menos
	 * - Si se escoge una solución esta debe tener una frecuencia
	 ************************************************************************************************************************/
	$( "[id^=frecsol]", $( "#listaComponentesLEV" ) ).each(function(x){

		var idx = this.id.substr( 7 );

		if( $( "#check_insumo" + idx )[0].checked ){
			if( $( this ).val() == '' ){

				var volxdil = $( "#volxdil"+idx );

				//Se valida que halla una solución elegida si hay algo escrito en vol/dil
				// if( volxdil.length > 0 && volxdil.val()*1 > 0 ){
				if( volxdil.length > 0 ){
					var valSol = false;
					$( "[id^=check_insumo]", $( "[id^=tdCbSol]" ) ).each(function(j){
						if( this.checked ){
							valSol = true;
						}
					});

					if( !valSol ){
						todoOk = false;
						msgError = "Debe seleccionar al menos una <b>SOLUCION</b>.";
					}
				}

				if( volxdil.length > 0 && volxdil.val() == 0 ){
					todoOk = false;
					msgError = "Debe escribir un valor en <b>VOL/DIL</b> para los electrolitos seleccionados";
				}
			}

			if( $.trim( $( "#cantidad_insumo" + idx ).val() ) == "" ){
				todoOk = false;
				if( $( "#tdCbSol"+idx ).length == 0 )
					msgError = "Ingrese un valor valido para los <b>ELECTROLITOS</b>";
				else
					msgError = "Ingrese un valor para <b>VOL/TOTAL</b> de la solución";
			}
		}
	});

	var valSol = false;
	$( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesLEV" ) ) ).each(function(j){
		if( this.checked ){
			valSol = true;
		}
	});

	if( !valSol ){
		todoOk = false;
		msgError = "Debe seleccionar al menos una <b>SOLUCION</b>.";
	}

	var frecDi = $( "#slFrecDilLev", $( "#listaComponentesLEV" ) ).val();
	if( frecDi == "" ){
		msgError = "Debe seleccionar una <b>UNIDAD</b> para <b>VELOCIDAD DE INFUSIÓN</b>.";
		todoOk = false;
	}

	if( todoOk ){
		var frecDi = $( "#txFrecDilLev", $( "#listaComponentesLEV" ) ).val();
		if( frecDi == "" ){
			msgError = "Debe ingresar un valor para <b>VELOCIDAD DE INFUSIÓN</b>.";
			todoOk = false;
		}
	}
	/**********************************************************************************************************************/



	if( todoOk ){

		var nombre1 = '';
		$( "[id^=check_insumo]", $( "[id^=tdCbSol]", $( "#listaComponentesLEV" ) ) ).each(function(x){
			if( this.checked ){
				fila = this.parentNode.parentNode;
				var idx = this.id.substr(12);
				nombre1 += $( "[tdSolGen"+idx+"]" ).html() +" <b>" + $( "#cantidad_insumo"+idx ).val() + " "+$( "[tdSolUVol"+idx+"]" ).html()+"</b><br>";
			}
		});

		var nombre2 = "";
		$( "[id^=check_insumo]", $( "[id^=tdCbEle]", $( "#listaComponentesLEV" ) ) ).each(function(x){
			if( this.checked ){
				fila = this.parentNode.parentNode;
				var idx = this.id.substr(12);
				nombre2 += $( "[tdEleGen"+idx+"]" ).html() +" <b>" + $( "#cantidad_insumo"+idx ).val() + " "+$( "[tdEleUVol"+idx+"]").html()+"</b> en C/<b>"+$( "#volxdil"+idx ).val()+"</b><br>";
			}
		});

		// var nombre  = $.trim( nombre1 )+"+"+$.trim( nombre2 )+"para <b>"+$("#wperiod"+tipoProtocolo+indice+" option[value="+$("#wperiod"+tipoProtocolo+indice).val()+"]" ).html()
		var nombre  = $.trim( nombre1 )+"+"+$.trim( nombre2 );//+"para <b>"+$("#wperiod"+tipoProtocolo+indice+" option[value="+$("#wperiod"+tipoProtocolo+indice).val()+"]" ).html()
		    nombre +="</b> a <b>"+$( "#txFrecDilLev", $( "#listaComponentesLEV" ) ).val()+" "+$( "#slFrecDilLev option[value='"+$( "#slFrecDilLev", $( "#listaComponentesLEV" ) ).val()+"']", $( "#listaComponentesLEV" ) ).html();
		// var nombre = "Aquí va el nombre";

		var newDiv = document.createElement( "div" );

		$( newDiv ).html( nombre );

		$("#wcolmed"+tipoProtocolo+indice ).append( newDiv );


		var componentesLQ =  document.getElementById("wcomponentesarticulocod").value;

		adicionMultiple = true;

		$.unblockUI();

		if(isset(document.getElementById("wcomponentesarticulo")))
			document.getElementById("wcomponentesarticulo").value = "";
		if(isset(document.getElementById("wcomponentesarticulocod")))
			document.getElementById("wcomponentesarticulocod").value = "";

		eleccionMedicamentosInsumos(componentesLQ, indice, tipoProtocolo, "LEV" );
		agregarMultiplesArticulos();
		// quitarArticulo(indice,'%','','detKardexAddN','LQ');

		grupoGenerico = "";

		document.getElementById("wcomponentesarticulo").value = "";
		document.getElementById("wcomponentesarticulocod").value = "";
	}
	else{
		jAlert( msgError, 'ALERTA' );
	}


}

function trim_ord (myString)
{
	return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
}

function str_replace_ord (search, replace, subject, count) {
        j = 0,
        temp = '',
        repl = '',
        sl = 0,        fl = 0,
        f = [].concat(search),
        r = [].concat(replace),
        s = subject,
        ra = Object.prototype.toString.call(r) === '[object Array]',        sa = Object.prototype.toString.call(s) === '[object Array]';
    s = [].concat(s);
    if (count) {
        this.window[count] = 0;
    }
    for (i = 0, sl = s.length; i < sl; i++) {
        if (s[i] === '') {
            continue;
        }        for (j = 0, fl = f.length; j < fl; j++) {
            temp = s[i] + '';
            repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
            s[i] = (temp).split(f[j]).join(repl);
            if (count && s[i] !== temp) {                this.window[count] += (temp.length - s[i].length) / f[j].length;
            }
        }
    }
    return sa ? s : s[0];
}



/******************************************************************************************************************************
 *Valida que la diferencia de fechas entre el servidor y el cliente no sea mas de media hora
 ******************************************************************************************************************************/
function adicionarComponenteArticulo(codigo,nombre,i,esDispensable,cantidadFracciones,cmp){

	var componentes = document.getElementById('wcomponentesarticulo').value;
	var componentescod = document.getElementById('wcomponentesarticulocod').value;

	if( cmp != $( "#check_insumo"+i )[0] ){
		if( !$( "#check_insumo"+i )[0].checked && $( "#cantidad_insumo"+i ).val()*1 > 0 ){
			$( "#check_insumo"+i )[0].checked = true;
		}
		else if( $( "#check_insumo"+i )[0].checked && $( "#cantidad_insumo"+i ).val()*1 == 0 ){
			$( "#check_insumo"+i )[0].checked = false;
		}
	}

	if(document.getElementById('check_insumo'+i).checked)
	{
		if(document.getElementById('cantidad_insumo'+i).value == "" || document.getElementById('cantidad_insumo'+i).value == " ")
			document.getElementById('cantidad_insumo'+i).value = cantidadFracciones;

		var strquitar = trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantidadAnterior[i] + "\r" + "\n";
		var strquitarcod = trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantidadAnterior[i] + " - " + esDispensable + "\r" + "\n";

		componentes = str_replace_ord(strquitar,"",componentes);
		componentescod = str_replace_ord(strquitarcod,"",componentescod);

		document.getElementById('wcomponentesarticulo').value = componentes;
		document.getElementById('wcomponentesarticulocod').value = componentescod;

		var cantComponente = document.getElementById('cantidad_insumo'+i).value;
		if(componentes.indexOf(nombre.replace(/_/g," "))==-1)
		{
			document.getElementById('wcomponentesarticulo').value += trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantComponente + "\r" + "\n";
			document.getElementById('wcomponentesarticulocod').value += trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantComponente + " - " + esDispensable + "\r" + "\n";
		}

		cantidadAnterior[i] = document.getElementById('cantidad_insumo'+i).value;
	}
	else
	{
		var cantComponente = document.getElementById('cantidad_insumo'+i).value;

		var strquitar = trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantComponente + "\r" + "\n";
		var strquitarcod = trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantComponente + "\r" + "\n";

		componentes = str_replace_ord(strquitar,"",componentes);
		componentescod = str_replace_ord(strquitarcod,"",componentescod);

		document.getElementById('wcomponentesarticulo').value = componentes;
		document.getElementById('wcomponentesarticulocod').value = componentescod;

		document.getElementById('cantidad_insumo'+i).value = "";
	}
}

function borrarDatosDosisCalculada( cmp ){

	if( $( cmp ).val() != "" ){
		$( "[id^=inICDca]" ).val("");
		$( "[id^=inICDca]" ).attr("disabled", true );
		$( "[id^=slICUdca]" ).val("");
		$( "[id^=slICUdca]" ).attr("disabled", true );
	}
	else{
		$( "[id^=inICDca]" ).attr("disabled", false );
		$( "[id^=slICUdca]" ).attr("disabled", false );
	}
}

function verificarVelocidadInfusion(){

	var habilitar = true;
	//Si no hay nada seleccionado en dosis calculada se puede seleccionar velocidad de infusión
	$( "[id^=inICDca]", $( "#listaComponentesIC" ) ).each(function(x){
		if( $( "#check_insumo"+this.id.substr(7) )[0].checked && $( this ).val() > 0 ){
			var sl = $( "#slFrecDilLev option[value='']", $( "#listaComponentesIC" ) );
			sl.attr( "selected", "selected" );
			$( "#slFrecDilLev", $( "#listaComponentesIC" ) )[0].disabled = true;
			$( "#txFrecDilLev", $( "#listaComponentesIC" ) )[0].disabled = true;
			habilitar = false;
			return;
		}
	});

	if( habilitar ){
		$( "#slFrecDilLev", $( "#listaComponentesIC" ) )[0].disabled = false;
		$( "#txFrecDilLev", $( "#listaComponentesIC" ) )[0].disabled = false;
	}
}

/******************************************************************************************************************************
 *Valida que la diferencia de fechas entre el servidor y el cliente no sea mas de media hora
 ******************************************************************************************************************************/
function adicionarComponenteArticuloIC(codigo,nombre,i,esDispensable,cantidadFracciones,cmp,ele){

	var componentes = document.getElementById('wcomponentesarticulo').value;
	var componentescod = document.getElementById('wcomponentesarticulocod').value;

	codigoAnterior[i] = codigo;

	if( cmp != $( "#check_insumo"+i )[0] ){
		if( !$( "#check_insumo"+i )[0].checked && $( "#cantidad_insumo"+i ).val()*1 > 0 ){
			$( "#check_insumo"+i )[0].checked = true;
		}
		else if( $( "#check_insumo"+i )[0].checked && $( "#cantidad_insumo"+i ).val()*1 == 0 ){
			$( "#check_insumo"+i )[0].checked = false;
		}
	}

	if( $( "#check_insumo"+i )[0].checked ){

		if( ele ){
			$( "[id^=check_insumo]", $( "#listaComponentesIC [id^=tdCbEle]" ) ).each(function(x){
				if( this != $( "#check_insumo"+i )[0] && this.checked ){

					var idx = this.id.substr(12);

					var tipMed = 'ele'
					var nombre2 = $( "[tdEleGen"+idx+"]", $( "#listaComponentesIC" ) ).html();
					var codigo2 = codigoAnterior[idx];

					var strquitar = trim_ord ( nombre2.replace(/_/g," ") ) + " - " + cantidadAnterior[idx] + " - " + cantidadVolDilAnterior[idx] + " - " + valorFrecSolAnterior[idx]  + " - " + tipMed + "\r\n";
					var strquitarcod = trim_ord ( codigo2.replace(/_/g," ") ) + " - " + cantidadAnterior[idx] + " - " + esDispensable + " - " + cantidadVolDilAnterior[idx] + " - " + valorFrecSolAnterior[idx]  + " - " + tipMed + "\r\n";

					componentes = str_replace_ord(strquitar,"",componentes);
					componentescod = str_replace_ord(strquitarcod,"",componentescod);

					document.getElementById('wcomponentesarticulo').value = componentes;
					document.getElementById('wcomponentesarticulocod').value = componentescod;

					$( this ).click();
				}
			});
		}
		else{

			$( "[id^=check_insumo]", $( "#listaComponentesIC [id^=tdCbSol]" ) ).each(function(x){
				if( this != $( "#check_insumo"+i )[0] && this.checked ){

					var idx = this.id.substr(12);

					var tipMed = 'sol'
					var nombre2 = $( "[tdSolGen"+idx+"]", $( "#listaComponentesIC" ) ).html();
					var codigo2 = codigoAnterior[idx];

					var strquitar = trim_ord ( nombre2.replace(/_/g," ") ) + " - " + cantidadAnterior[idx] + " - " + cantidadVolDilAnterior[idx] + " - " + valorFrecSolAnterior[idx]  + " - " + tipMed + "\r\n";
					var strquitarcod = trim_ord ( codigo2.replace(/_/g," ") ) + " - " + cantidadAnterior[idx] + " - " + esDispensable + " - " + cantidadVolDilAnterior[idx] + " - " + valorFrecSolAnterior[idx]  + " - " + tipMed + "\r\n";

					componentes = str_replace_ord(strquitar,"",componentes);
					componentescod = str_replace_ord(strquitarcod,"",componentescod);

					document.getElementById('wcomponentesarticulo').value = componentes;
					document.getElementById('wcomponentesarticulocod').value = componentescod;

					$( this ).click();
				}
			});
		}
	}

	if(document.getElementById('check_insumo'+i).checked)
	{
		// if( ele ){
			// if(document.getElementById('cantidad_insumo'+i).value == "" || document.getElementById('cantidad_insumo'+i).value == " ")
				// document.getElementById('cantidad_insumo'+i).value = cantidadFracciones;
		// }

		var voldil = $('#volxdil'+i);

		var tipMed = voldil.length > 0 ? 'ele': 'sol' ;	//Indico si es solucióno electrolito

		voldil = voldil.length > 0 ? voldil.val() : "";

		var strquitar = trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantidadAnterior[i] + " - " + cantidadVolDilAnterior[i] + " - " + valorFrecSolAnterior[i]  + " - " + tipMed + "\r\n";
		var strquitarcod = trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantidadAnterior[i] + " - " + esDispensable + " - " + cantidadVolDilAnterior[i] + " - " + valorFrecSolAnterior[i]  + " - " + tipMed + "\r\n";

		componentes = str_replace_ord(strquitar,"",componentes);
		componentescod = str_replace_ord(strquitarcod,"",componentescod);

		document.getElementById('wcomponentesarticulo').value = componentes;
		document.getElementById('wcomponentesarticulocod').value = componentescod;

		var cantComponente = document.getElementById('cantidad_insumo'+i).value;

		var frecsol = $( "#frecsol"+i );
		frecsol = frecsol.length > 0 ? frecsol.val() : "";


		if( voldil*1 > 0 ){
			if( frecsol != "" ){
				$( "#frecsol"+i ).val( '' );
			}
		}

		if(componentes.indexOf(nombre.replace(/_/g," "))==-1)
		{
			document.getElementById('wcomponentesarticulo').value += trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantComponente + " - " + voldil  + " - " + frecsol  + " - " + tipMed + "\r\n";
			document.getElementById('wcomponentesarticulocod').value += trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantComponente + " - " + esDispensable + " - " + voldil + " - " + frecsol + " - " + tipMed + "\r\n";
		}

		cantidadAnterior[i] = document.getElementById('cantidad_insumo'+i).value;

		cantidadVolDilAnterior[i] = voldil;

		valorFrecSolAnterior[i] = frecsol;

		valorEsDispensable[i] = esDispensable;

		if( $( "#txFrecDilLev", $("#listaComponentesIC") ).val() == '' )
			$( "#inICDca"+i+",#slICUdca"+i, $("#listaComponentesIC") ).attr({disabled:false});
		else
			$( "#inICDca"+i+",#slICUdca"+i, $("#listaComponentesIC") ).attr({disabled:true});
	}
	else
	{
		var cantComponente = document.getElementById('cantidad_insumo'+i).value;

		var frecsol = $( "#frecsol"+i );
		frecsol = frecsol.length > 0 ? frecsol.val() : "";

		var voldil = $('#volxdil'+i);

		var tipMed = voldil.length > 0 ? 'ele': 'sol' ;	//Indico si es solucióno electrolito

		voldil = voldil.length > 0 ? voldil.val() : "";

		if( frecsol != "" ){
			$( "#frecsol"+i ).val( '' );
		}

		var strquitar = trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantComponente + " - " + voldil + " - " + frecsol + " - " + tipMed + "\r\n";
		var strquitarcod = trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantComponente + " - " + valorEsDispensable[i] + " - " + voldil + " - " + frecsol + " - " + tipMed + "\r\n";

		componentes = str_replace_ord(strquitar,"",componentes);
		componentescod = str_replace_ord(strquitarcod,"",componentescod);

		document.getElementById('wcomponentesarticulo').value = componentes;
		document.getElementById('wcomponentesarticulocod').value = componentescod;

		document.getElementById('cantidad_insumo'+i).value = "";

		$( "#inICDca"+i+",#slICUdca"+i, $("#listaComponentesIC") ).attr({disabled:true});

		//Esto solo aplica para la Infusión contiuna
		if( $( "#inICDca"+i ).length > 0 ){
			$( "#inICDca"+i ).val("");
			$( "#slICUdca"+i ).val("");

			verificarVelocidadInfusion();
		}
	}
}

/******************************************************************************************************************************
 *Valida que la diferencia de fechas entre el servidor y el cliente no sea mas de media hora
 ******************************************************************************************************************************/
function adicionarComponenteArticuloLEV(codigo,nombre,i,esDispensable,cantidadFracciones,cmp){

	var componentes = document.getElementById('wcomponentesarticulo').value;
	var componentescod = document.getElementById('wcomponentesarticulocod').value;

	if( cmp != $( "#check_insumo"+i )[0] ){
		if( !$( "#check_insumo"+i )[0].checked && $( "#cantidad_insumo"+i ).val()*1 > 0 ){
			$( "#check_insumo"+i )[0].checked = true;
		}
		else if( $( "#check_insumo"+i )[0].checked && $( "#cantidad_insumo"+i ).val()*1 == 0 ){
			$( "#check_insumo"+i )[0].checked = false;
		}
	}

	if(document.getElementById('check_insumo'+i).checked)
	{
		// if(document.getElementById('cantidad_insumo'+i).value == "" || document.getElementById('cantidad_insumo'+i).value == " ")
			// document.getElementById('cantidad_insumo'+i).value = cantidadFracciones;

		var voldil = $('#volxdil'+i);

		var tipMed = voldil.length > 0 ? 'ele': 'sol' ;	//Indico si es solucióno electrolito

		voldil = voldil.length > 0 ? voldil.val() : "";

		var strquitar = trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantidadAnterior[i] + " - " + cantidadVolDilAnterior[i] + " - " + valorFrecSolAnterior[i]  + " - " + tipMed + "\r\n";
		var strquitarcod = trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantidadAnterior[i] + " - " + esDispensable + " - " + cantidadVolDilAnterior[i] + " - " + valorFrecSolAnterior[i]  + " - " + tipMed + "\r\n";

		componentes = str_replace_ord(strquitar,"",componentes);
		componentescod = str_replace_ord(strquitarcod,"",componentescod);

		document.getElementById('wcomponentesarticulo').value = componentes;
		document.getElementById('wcomponentesarticulocod').value = componentescod;

		var cantComponente = document.getElementById('cantidad_insumo'+i).value;

		var frecsol = $( "#frecsol"+i );
		frecsol = frecsol.length > 0 ? frecsol.val() : "";


		if( voldil*1 > 0 ){
			if( frecsol != "" ){
				$( "#frecsol"+i ).val( '' );
			}
		}

		if(componentes.indexOf(nombre.replace(/_/g," "))==-1)
		{
			document.getElementById('wcomponentesarticulo').value += trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantComponente + " - " + voldil  + " - " + frecsol  + " - " + tipMed + "\r\n";
			document.getElementById('wcomponentesarticulocod').value += trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantComponente + " - " + esDispensable + " - " + voldil + " - " + frecsol + " - " + tipMed + "\r\n";
		}

		cantidadAnterior[i] = document.getElementById('cantidad_insumo'+i).value;

		cantidadVolDilAnterior[i] = voldil;

		valorFrecSolAnterior[i] = frecsol;

		valorEsDispensable[i] = esDispensable;

		$( "#volxdil"+i, $( "#listaComponentesLEV" ) ).attr({disabled:false});
	}
	else
	{
		var cantComponente = document.getElementById('cantidad_insumo'+i).value;

		var frecsol = $( "#frecsol"+i );
		frecsol = frecsol.length > 0 ? frecsol.val() : "";

		var voldil = $('#volxdil'+i);

		var tipMed = voldil.length > 0 ? 'ele': 'sol' ;	//Indico si es solucióno electrolito

		voldil = voldil.length > 0 ? voldil.val() : "";

		if( frecsol != "" ){
			$( "#frecsol"+i ).val( '' );
		}

		var strquitar = trim_ord ( nombre.replace(/_/g," ") ) + " - " + cantComponente + " - " + voldil + " - " + frecsol + " - " + tipMed + "\r\n";
		var strquitarcod = trim_ord ( codigo.replace(/_/g," ") ) + " - " + cantComponente + " - " + valorEsDispensable[i] + " - " + voldil + " - " + frecsol + " - " + tipMed + "\r\n";

		componentes = str_replace_ord(strquitar,"",componentes);
		componentescod = str_replace_ord(strquitarcod,"",componentescod);

		document.getElementById('wcomponentesarticulo').value = componentes;
		document.getElementById('wcomponentesarticulocod').value = componentescod;

		document.getElementById('cantidad_insumo'+i).value = "";

		$( "#volxdil"+i, $( "#listaComponentesLEV" ) ).attr({disabled:true});

		//Esto solo aplica para la Infusión contiuna
		if( $( "#inICDca"+i ).length > 0 ){
			$( "#inICDca"+i ).val("");
			$( "#slICUdca"+i ).val("");

			verificarVelocidadInfusion();
		}
	}
}
/******************************************************************************************************************************
 *Valida que la diferencia de fechas entre el servidor y el cliente no sea mas de media hora
 ******************************************************************************************************************************/
function eliminarComponentesArticulos(){
	document.getElementById("wcomponentesarticulo").value = "";
	document.getElementById("wcomponentesarticulocod").value = "";
}
/*****************************************************************************************************************************
*
******************************************************************************************************************************/
function grabarEstadoAprobacionArticulos(historia,ingreso,fecha,codigosArticulos,estadoAprobacion,codUsuario){
	var parametros = "";
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	parametros = "consultaAjaxKardex=26&wemp_pmla="+document.forms.forma.wemp_pmla.value+"&basedatos="+document.forms.forma.wbasedato.value+"&historia="+historia+"&ingreso="+ingreso+"&fecha="+fecha+"&codigosArticulos="+codigosArticulos
				+"&codUsuario="+codUsuario+"&estadoAprobacion="+estadoAprobacion+"&wemp_pmla="+wemp_pmla;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();

		ajax.open("POST", "ordenes.inc.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200){
				if($.trim( ajax.responseText ) == "1"){
					$.unblockUI();
					$.growlUI('','Aprobación/desaprobación realizada exitosamente');
				}
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){}
}

//Muestra mensaje tooltip
function mostrar_mensaje(pestana){
	
	pestana=String(pestana);
	
	var pestanasVistas = $("#pestanasVistas").val();
	
	if(pestanasVistas!="")
	{
		var arrayPestanasVistas = pestanasVistas.split(",");
		
		if(jQuery.inArray( pestana, arrayPestanasVistas )==-1)
		{
			pestanasVistas += pestana+",";
			$("#pestanasVistas").val(pestanasVistas);
		}
		
	}
	else
	{
		$("#pestanasVistas").val(pestana+",");
	}
	
	
	
	if($('#imgAddExam').is(':visible')){
		  $('#imgAddExam').mouseover();
	}else{

		setTimeout(function(){
		  $('#imgAddExam').mouseover();
		}, 3200);

	}


	if($('#imgAddExamImp').is(':visible')){
		 $('#imgAddExamImp').mouseover();
	}else{

		setTimeout(function(){
		  $('#imgAddExamImp').mouseover();
		}, 3200);

	}

}

$(document).ready(function () {
	
	try{
		consultarOrdenesPendientesDeVer();
	}
	catch(e){}


	//Se deja por defecto el nombre de los botones como aceptar y cancelar respectivamente para los confirms y alertas
	$.alerts.okButton = "Aceptar";		//Dejo por defecto el valor sí para el boton de aceptar
	$.alerts.cancelButton = "Cancelar";	//Dejo por defecto el valor no para el boton cancelar

   //Controla le mensaje emergente para avisar que no se puede ingresar mas examenes por el boton + de lenguaje americas.
   var mensaje = $('#mensajelenguajeAmericas').val();
   $('#imgAddExam').gips({ 'theme': 'red', text: mensaje, placement: 'right' });
   $('#imgAddExamImp').gips({ 'theme': 'red', text: mensaje, placement: 'right' });
   //----

   $('input#wprocedimiento2').quicksearch('#examPendientes .find');
   $('input#wprocedimiento').quicksearch('div#detOrdenesRealizadas table .find');
   var tiempo_cierre = $("#tiempo_cierre_automatico").val();

   if(tiempo_cierre ){
	   //Variable que contiene la funcion salir_sin_grabar que despues de cinco minutos se activa.
		var timerHandle = setTimeout(function() {
			  salir_sin_grabar();
		}, tiempo_cierre * 60 * 1000 );

		//Esta funcion reunicia el cerrado automatico.
		function resetTimer() {
			window.clearTimeout(timerHandle);
			timerHandle = setTimeout(function() {  salir_sin_grabar(); }, tiempo_cierre * 60 * 1000);
		}
		//Si hay movimiento del mouse la funcion de cerrado automatico se reinicia.
		$(document).on('mousemove', function(e){
			resetTimer();
		});

		//Si hay movimiento del teclado la funcion de cerrado automatico se reinicia.
		$(document).on('keypress', function(e){
				resetTimer();
			});
   }
   
	$( "#wdexins,#wdexfrecuencia,#wdexesquema" ).change(function(){
		setTimeout( function(){ mostrarConfirmarDextrometer() }, 1000 );
	});
	
	$( "[id^=wperiod]" ).each(function(x){
		borrarDosisMaximaPorFrecuencia( this );
	});

	calendario_fecha_orden('calendariofecha');
	hora_orden('calendariohora');
	
	$( "[href=#fragment-7]" ).click(function(){
		mostrarAuditoria();
	});
	
	$('#taJusParaArtsSinTarifas').blur(function(){
		if( $(this).val() != '' ){
			$( "#btnAceptarAST" ).attr({disabled:false});
		}
		else{
			$( "#btnAceptarAST" ).attr({disabled:true});			
		}
	})
	
});

function borrarDosisMaximaPorFrecuencia( cmp ){
	
	cmp.borrarDosisMaxima = false;
	cmp.ultimaFrecuencia = $( cmp ).val();
	
	try{
		$( cmp ).change(function(){
			
			var fila = $( this ).parent().parent();
			
			if( $( "[id^=wesantibiotico]", fila ).val() != 'on' || $( "[name^=wfiltroantibiotico]:checked", fila ).length == 0 ){
				
				this.borrarDosisMaxima = false;
				
				var cmpDma = $( "[id^=wdosmax]", fila );
				var cnd = $( "[id^=wcondicion]", fila ); //Condición
				
				var dmaFrec = this.ultimaFrecuencia;
				
				var valDma = ''
				try{
					var valDma = dmaPorFrecuencia[ dmaFrec ].dma;
				}
				catch(e){}
				
				var cndDma = '';
				try{
					cndDma = dmaPorCondicionesSuministro[ cnd.val() ].dma;				
				}
				catch(e){}
				
				if( valDma && valDma == cmpDma.val() ){
					if( cndDma != cmpDma.val() ){
						this.borrarDosisMaxima = true;
						cmpDma.val( '' );
					}
				}
			}
		})
		.click(function(){
			this.ultimaFrecuencia = $( this ).val();
		});
	}
	catch(e){
		console.log(e);
	}
}


