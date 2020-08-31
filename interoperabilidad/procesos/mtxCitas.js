	
	$.mtxCitas = function( options ){
		
		this.historia 	= options.historia || null;
		this.ingreso 	= options.ingreso || null;
		this.wemp_pmla 	= options.wemp_pmla || "01";
		this.cco_sede 	= options.cco_sede || "";
		var fnAccpt		= options.accept || null;
		
		var paciente 	= null;
		
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
			
				console.log(data);
				
				var validar = data.validar;
				var mostrar = data.mostrar;
				
				if( validar ){
					
					if( mostrar ){
						
						datos.modalidad = data.modalidades;
						datos.prioridad = data.prioridades;
						datos.sala 		= data.salas;
						datos.defaults	= data.defaults;
						datos.cita		= data.datosCita;
						paciente 		= data.paciente;
						
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
						modal += "<div><div class='mtx-col-4 mtx-label'><label>Prioridad</label></div><div class='mtx-col-8'><select data-tipo='prioridad'></select></div></div>";
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
			
			var slModalidad = $( "select[data-tipo='modalidad']", objModal );
			var slSala 		= $( "select[data-tipo='sala']", objModal );
			var slPrioridad = $( "select[data-tipo='prioridad']", objModal );
			var btnAccept 	= $( ".mtx-btn-accpet > a", objModal );
			
			$( objModal ).dialog({
					width:"600px",
					modal: true,
				});
			
			var tieneDatosValidos = false;
			
			function habilitarBotonAceptar(){
				
				tieneDatosValidos = true;
				
				$( "select", objModal ).each(function(){
					
					if(  !$( this ).val() ||  $( this ).val() == "" ){
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
			
			$( slPrioridad ).on( 'change', function(){
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
				
			for( var x in datos.modalidad ){
				$( slModalidad ).append( "<option value='"+datos.modalidad[x].codigo+"'>"+datos.modalidad[x].descripcion+"</option>" );
			}
			
			for( var x in datos.prioridad ){
				$( slPrioridad ).append( "<option value='"+datos.prioridad[x].codigo+"'>"+datos.prioridad[x].descripcion+"</option>" );
			}
			
			slModalidad[0].selectedIndex = -1;
			slSala[0].selectedIndex = -1;
			slPrioridad[0].selectedIndex = -1;
			console.log(datos.defaults.prioridad)
			if( datos.defaults ){
				
				if( datos.defaults.prioridad )
					$( slPrioridad ).val( datos.defaults.prioridad );
				
				if( datos.defaults.modalidad ){
					$( slModalidad ).val( datos.defaults.modalidad );
					$( slModalidad ).change();
				}	
				
				if( datos.defaults.sala ){
					$( slSala ).val( datos.defaults.sala );
					$( slSala ).change();
				}	
			}
			
			habilitarBotonAceptar();
		}
		
		// var validar = true;
		// var mostrar = true;
		
		
		// if( validar ){
			
			// if( mostrar ){
				
				// // var objModal = $( modal );
				
				// // $( objModal ).dialog();
				
				// moduloModal();
			// }
			// else{
			// }
		// }
	}
	
	// setTimeout(
		// function(){ $.mtxCitas({
			// historia 	: 212353,
			// ingreso 	: 21,
			// wemp_pmla 	: '01',
			// accept		: function(){ alert("realizando accion") }
		// }) },
		// 1000
	// )
	