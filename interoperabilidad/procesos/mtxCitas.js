	
	$.mtxCitas = function( options ){
		
		this.historia 	= options.historia || null;
		this.ingreso 	= options.ingreso || null;
		this.wemp_pmla 	= options.wemp_pmla || "01";
		this.cco_sede 	= options.cco_sede || "";
		var fnAccpt		= options.accept || null;
		
		var paciente 	= null;
		var sede 		= null;
		
		var __self = this;
		
		
		var datos = {};
		var modal = "";
		
		$.get("../../interoperabilidad/procesos/IoImagenologia.php",
		{
			consultaAjax		: '',
			accion				: 'consultarMaestros',
			wemp_pmla			: __self.wemp_pmla,
			historia			: __self.historia,
			ingreso				: __self.ingreso,
			cco_sede			: __self.cco_sede,
		}, 
		function(data){

				var validar = data.validar;
				var mostrar = data.mostrar;
				
				if( validar ){
					
					if( mostrar ){
						
						datos.modalidad 	= data.modalidades;
						datos.modalidades_cup 	= data.modalidades_cup;
						datos.prioridad 	= data.prioridades;
						datos.sala 			= data.salas;
						datos.defaults		= data.defaults;
						datos.cita			= data.datosCita;
						datos.indicaciones	= data.indicaciones;
						paciente 			= data.paciente;
						sede 				= data.sede;
						
						modal += "<div class='mtx-ct' title='RECEPCION HIRUKO'>";
						modal += "<div class='mtx-ct-title' style='display:none;'>AGENDA</div>";
						modal += "<div class='mtx-ct-pac'>";
						modal += paciente.nombreCompleto ? paciente.nombreCompleto : '';
						modal += "<br>"+paciente.tipoDocumento+' '+paciente.nroDocumento;
						modal += "<br>"+__self.historia+"-"+__self.ingreso;
						
						if( !data.datosCita ){
							modal += "<span class='mtx-cita-out'>Sin Cita</span>";
						}
						else{
							modal += "<span class='mtx-cita-on'>Con Cita para las <b>"+data.datosCita.Mvchor+"</b></span>";
						}
						
						
						modal += "</div>";
						modal += "<div class='mtx-ct-container'>";
						modal += "<div><div class='mtx-col-4 mtx-label'><label>Modalidad</label></div><div  class='mtx-col-8'><select data-tipo='modalidad'></select></div></div>";
						modal += "<div><div class='mtx-col-4 mtx-label'><label>Sala</label></div><div class='mtx-col-8'><select data-tipo='sala'></select></div></div>";
						modal += "<div><div class='mtx-col-4 mtx-label'><label>Sede</label></div><div class='mtx-col-8'><select data-tipo='sede'></select></div></div>";
						modal += "<div><div class='mtx-col-4 mtx-label'><label>Prioridad</label></div><div class='mtx-col-8'><select data-tipo='prioridad'></select></div></div>";
						modal += "<div class='mtx-medico-remitente'><div class='mtx-col-4 mtx-label'><label>M&eacute;dico remitente</label></div><div class='mtx-col-8'><INPUT type='text' data-tipo='medico-remitente' data-idmedico=''></div></div>";
						modal += "<div class='mtx-indicacion'><div class='mtx-col-4 mtx-label'><label>Indicaci&oacute;n</label></div><div class='mtx-col-8'><select data-tipo='indicador'></select></div></div>";
						modal += "</div>";
						modal += "<div class='mtx-ct-actions'>";
						modal += "<span class='mtx-btn mtx-btn-accpet'><a href='#null'>ACEPTAR</a></span>";
						modal += "</div>";
						modal += "</div>";
						
						moduloModal();
						
						$("[role=button]").hide();
					}
				}
				
				if( !validar || !mostrar ){
					try{
						fnAccpt();
					}
					catch(e){};
				}
				
			},
			"json"
		);
		
		
		
		
		function moduloModal(){
			
			var objModal = $( modal );
			
			var slModalidad 		= $( "select[data-tipo='modalidad']", objModal );
			var slSala 				= $( "select[data-tipo='sala']", objModal );
			var slSede 				= $( "select[data-tipo='sede']", objModal );
			var slPrioridad 		= $( "select[data-tipo='prioridad']", objModal );
			var inMedicoRemitente 	= $( "input[data-tipo='medico-remitente']", objModal );
			var slIndicaciones 		= $( "select[data-tipo='indicador']", objModal );
			var btnAccept 			= $( ".mtx-btn-accpet > a", objModal );
			
			$( objModal ).dialog({
					width:"680px",
					modal: true,
				});
			
			var tieneDatosValidos = false;
			
			function habilitarBotonAceptar(){
				
				tieneDatosValidos = true;
				var codigoMedico = $( inMedicoRemitente ).data( "idmedico" );
				
				$( "select:visible,input:visible", objModal ).each(function(){
					
					if(  !$( this ).val() ||  $( this ).val() == "" || codigoMedico == '' ){
						tieneDatosValidos = false;
					}
				});
				
				if( !tieneDatosValidos ){
					$( btnAccept ).parent().addClass( "mtx-btn-disabled" );
				}
				else{
					$( btnAccept ).parent().removeClass("mtx-btn-disabled");
				}
			}
			
			$( slModalidad ).on( 'change', function(){
				$( slSala ).trigger("setSalas")
				habilitarBotonAceptar()
			});
			
			$( slSala )
				.on( 'change', function(){
					habilitarBotonAceptar()
				})
				.on('setSalas', function(){
					
					var total = 0;
					
					slSala.html("");
					
					var salas = datos.sala[ slModalidad.val() ];
					
					for( var x in salas ){
						total++;
						$( slSala ).append( "<option value='"+salas[x].codigo+"'>"+salas[x].descripcion+"</option>" );
					}
					
					if( total > 1 )
						slSala[0].selectedIndex = -1;
				});
			$( slSede ).append( "<option value='" + sede.codigo + "'>[" + sede.cco + "] " + sede.descripcion.toUpperCase() + "</option>" );
			
			$( slPrioridad ).on( 'change', function(){
				habilitarBotonAceptar()
			});
			
			$( slIndicaciones ).on( 'change', function(){
				habilitarBotonAceptar()
			});
			
			$( inMedicoRemitente ).on( 'change', function(){
				habilitarBotonAceptar()
			});
			
			$( btnAccept ).on( 'click', function(){
				
				if( tieneDatosValidos ){
					
					tieneDatosValidos = false;
					
					$.post("../../interoperabilidad/procesos/IoImagenologia.php",
					{
						consultaAjax		: '',
						accion				: 'crearMensaje',
						wemp_pmla			: __self.wemp_pmla,
						historia			: __self.historia,
						ingreso				: __self.ingreso,
						modalidad			: slModalidad.val(),
						sala				: slSala.val(),
						prioridad			: slPrioridad.val(),
						cco_sede			: __self.cco_sede,
						medico				: $( inMedicoRemitente ).data( "medico" ),
						indicacion			: $( slIndicaciones ).val(),
						idCita				: datos && datos.cita && datos.cita.id ? datos.cita.id : '',
					}, 
					function(data){
						
							if( fnAccpt ){
								try{
									fnAccpt();
								}
								catch(e){};
							}
							
							$( objModal ).dialog("close");
							
							tieneDatosValidos = true;
						},
						"text"
					);
				}
			});
				
			if( typeof datos.cita !== 'undefined' )
			{
				for( var x in datos.modalidad ){
					$( slModalidad ).append( "<option value='"+datos.modalidad[x].codigo+"'>"+datos.modalidad[x].descripcion+"</option>" );
				}
			}
			else
			{
				for( var x in datos.modalidades_cup ){
					$( slModalidad ).append( "<option value='"+datos.modalidades_cup[x].codigo+"'>"+datos.modalidades_cup[x].descripcion+"</option>" );
				}
			}
			
			for( var x in datos.prioridad ){
				$( slPrioridad ).append( "<option value='"+datos.prioridad[x].codigo+"'>"+datos.prioridad[x].descripcion+"</option>" );
			}
			
			if( datos.indicaciones.length > 0 ){
				$( slIndicaciones ).append( "<option value=''>Seleccione...</option>" );
				for( var x in datos.indicaciones ){
					$( slIndicaciones ).append( "<option value='"+datos.indicaciones[x].descripcion+"'>"+datos.indicaciones[x].descripcion+"</option>" );
				}
			}
			else{
				$( ".mtx-indicacion" ).css({ display:"none" });
			}
			
			slModalidad[0].selectedIndex = -1;
			slSala[0].selectedIndex = -1;
			slPrioridad[0].selectedIndex = -1;

			if( datos.defaults ){
				
				if( datos.defaults.prioridad ){
					$( slPrioridad ).val( datos.defaults.prioridad );
					
					//Deshabilitito todas las opciones menos la que tenga por defecto
					// $( "option", slPrioridad )
					// 	.not( $("option:selected", slPrioridad ) )
					// 	.attr({disabled:true})
					// 	.prop({disabled:true})
					// 	.css({display:"none"});
				}
				
				if( datos.defaults.modalidad ){
					$( slModalidad ).val( datos.defaults.modalidad );
					$( slModalidad ).change();
					
					//Deshabilitito todas las opciones menos la que tenga por defecto
					// $( "option", slModalidad )
					// 	.not( $("option:selected", slModalidad ) )
					// 	.attr({disabled:true})
					// 	.prop({disabled:true})
					// 	.css({display:"none"});
				}	
				
				if( datos.defaults.sala ){
					$( slSala ).val( typeof datos.cita.Mvcsal === 'undefined' ? datos.defaults.sala : datos.cita.Mvcsal );
					$( slSala ).change();
					
					// //Deshabilitito todas las opciones menos la que tenga por defecto
					// $( "option", slSala )
						// .not( $("option:selected", slSala ) )
						// .attr({disabled:true})
						// .prop({disabled:true})
						// .css({display:"none"});
				}	
				
				if( sede.codigo ){
					$( slSede ).val( sede.codigo );
					$( slSede ).change();
				}
				if( datos.defaults.indicaciones ){
					$( slIndicaciones ).val( datos.defaults.indicaciones );
					$( slIndicaciones ).change();
					
					//Deshabilitito todas las opciones menos la que tenga por defecto
					$( "option", slIndicaciones )
						.not( $("option:selected", slIndicaciones ) )
						.attr({disabled:true})
						.prop({disabled:true})
						.css({display:"none"});
				}	
			}

			//Si es una orden (tiene tipo de orden, numero de orden e item ) no se muestra el medico remitente
			if( datos.cita && datos.cita.Mvctor != '' && datos.cita.Mvcnro > 0 && datos.cita.Mvcite > 0 ){
				$( ".mtx-medico-remitente" ).css({ display:"none" });
			}

            habilitarBotonAceptar();

			//Autocompletar para el medico remitente
			$( inMedicoRemitente ).autocomplete({
				minLength: 	3,
				source: 	"../../interoperabilidad/procesos/IoImagenologia.php?consultaAjax=&accion=consultarMedicosRemitentes&tipoOrden="+sede.tipoOrden+"&wemp_pmla="+__self.wemp_pmla,
				select: 	function( event, ui ){

					$( inMedicoRemitente ).data( 'idmedico', ui.item.codigo );
					$( inMedicoRemitente ).data( 'medico', ui.item );
					
					habilitarBotonAceptar();
				},
				change: function ( event, ui ){
					if (ui.item === null || ui.item === undefined)
					{
						$( inMedicoRemitente ).data( 'medico', '' );
						$( inMedicoRemitente ).data( 'idmedico', '' );
						
						habilitarBotonAceptar();
					}
					
				},
			});
		}
	}