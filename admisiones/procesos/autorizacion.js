/**
 * * PROGRAMA					: AUTORIZACIONES PARA ACCEDER A LA HISTORIA
 * * AUTOR						: Ing. Joel David Payares Hernández.
 * * FECHA CREACION				: 13 de Mayo de 2021.
 * * FECHA ULTIMA ACTUALIZACION	: 
 * * DESCRIPCION				: Archivo con todos los script que permiten la interacción del front
 * *							  con el back permitiendo así el flujo correcto del programa.
 * *
 * *							 - Aquí estan todos los controles del front y la asignación de estilos
 * *							   y acciones.
 */
class autorizacionHCE
{
	static Init()
	{
		autorizacionHCE.activarControles();
	}

	/**
	 * * Metodo que permite activar controles de la vista.
	 */
	static activarControles()
	{
		const GLOBAL_WEMP_PMLA = $("input#wemp_pmla").val();

		/**
		 * * Activar click span personas autorizadas.
		 */
		$("body").on("click", "span.add-person-aut", function()
		{
			autorizacionHCE.addFila2( 'tabla_personas_autorizadas' );
		});

		/**
		 * * Activar click span personas que reclaman.
		 */
		$("body").on("click", "span.add-person-recl", function()
		{
			autorizacionHCE.addFila2( 'tabla_personas_reclaman' );
		});

		/**
		 * * Activar click boton guardar.
		 */
		$("body").on("click", "button.guardar-datos", function()
		{
			let datos = autorizacionHCE.datosFormulario();

			$( "div#div-carga-guardar" ).removeClass("oculto");
			$( "button.guardar-datos" ).prop("disabled", true);
			$( "button.regresar" ).prop("disabled", true);

			autorizacionHCE.guardarAutorizacion( GLOBAL_WEMP_PMLA, datos );
		});

		/**
		 * * Activar click boton regresar.
		 */
		$("body").on("click", "button.regresar", function()
		{
			autorizacionHCE.limpiar();
		});

		/**
		 * * Activar click button consultar parametros.
		 */
		$("body").on("click", "button.consultar-datos", function()
		{

			if ( autorizacionHCE.validar( "form-consultar-parametros" ) )
			{	
				$( "div#div-carga" ).removeClass("oculto");
				$( "button.consultar-datos" ).prop('disabled', true);
				$( "input#Authis" ).prop('disabled', true);
				$( "input#Auting" ).prop('disabled', true);

				let data = [
					{
						name	:	"Authis",
						value	:	$( "input#Authis" ).val()
					},
					{
						name	:	"Auting",
						value	:	$( "input#Auting" ).val()
					}
				];

				autorizacionHCE.consultarDatos( GLOBAL_WEMP_PMLA, data );
			}
		});

		/**
		 * * Agrega clase form-control y persona-autorizada a todos los input, slect y textarea de la vista.
		*/
		$.each( $( "body table#tabla_personas_autorizadas" ).find( "input.txt-autoriza, select" ), function()
		{
			$( this ).addClass("form-control form-control-sm persona-autorizada");
		});

		/**
		 * * Agrega clase form-control y persona-reclama a todos los input, slect y textarea de la vista.
		 */
		$.each( $( "body table#tabla_personas_reclaman" ).find( "input.txt-reclama, input.txt-reclama, textarea#aut_obs, select" ), function()
		{
			$( this ).addClass("form-control form-control-sm persona-reclama");
		});

		/**
		 * * Agrega clase form-control y persona-reclama a todos los input, slect y textarea de la vista.
		 */
		$.each( $( "body" ).find( "textarea#aut_obs" ), function()
		{
			$( this ).addClass("form-control form-control-sm");
		});
		
		/**
		 * * Elimina propiedad onclick de span.
		 */
		$.each( $( "body" ).find( "span#spn_tabla_diagnostico, span#spn_tabla_diagnostico" ), function()
		{
			$( this ).removeAttr( "onclick" );
		});

		/**
		 * * Centra todos los th con class encabezadotabla.
		 */
		$.each( $( "body" ).find( "th.encabezadotabla" ), function()
		{
			$( this ).css("text-align", "center");
		});

		/**
		 * * Evento click sobre botón close de alert.
		 */
		$("body").on("click", "button.close", function()
		{
			$( "div#alert-message" ).addClass( 'oculto' );
		});

		/**
		 * * Evento click sobre botón close de alert.
		 */
		$("body").on("click", "button.close-respuesta", function()
		{
			$( "div#alert-respuesta" ).addClass( 'oculto' );
		});
	}

	/**
	 * * Metodo para crear el array de datos a enviar a guardar
	 */
	static datosFormulario()
	{
		let data = [];
		let personas_autorizadas = [];
		let personas_reclaman = [];
		let autorizacion = [];

		data = [ { "Authis" : $( "input#Authis" ).val(), "Auting" : ($( "input#Auting" ).val() != "" ? $( "input#Auting" ).val() : $( "input#egr_ingtxtNumIng" ).val() ) } ]

		if ( $( 'input:radio[name=aut_inf_radAut]:checked' ).val() == undefined )
		{
			swal(
				'Autorizaci&oacute;n con fines cientificos',
				'Por favor responsa si autoriza el uso de su informaci&oacute;n con fines cientificos, es obligatorio!',
				'error'
			);

			$( 'div.radio-uso-informacion' ).addClass( 'error' );

			return;
		}
		else
		{
			$( 'div.radio-uso-informacion' ).removeClass( 'error' );
			
			autorizacion = [ { [$('input:radio[name=aut_inf_radAut]:checked').attr('id')] : $('input:radio[name=aut_inf_radAut]:checked').val() } ];

			if ( $( '#aut_obs' ).val() != '' )
			{
				autorizacion = [ ...autorizacion, { [ $( '#aut_obs' ).attr('id') ] : $( '#aut_obs' ).val() } ]
			}
		}

		data = [ ...data , autorizacion ]

		$("table#tabla_personas_autorizadas tr.fila_personas_autorizadas").each(function (index_fila, fila) {
			const myElement = $( fila ).find( 'input.persona-autorizada, select.persona-autorizada' );
			let persona = [];

			for (let i = 0; i < myElement.length; i++) {
				persona = [ ...persona, { [myElement[i].name] : myElement[i].value } ]
			}

			personas_autorizadas[index_fila] = [...persona];
		});

		data = [ ...data, personas_autorizadas ]

		$("table#tabla_personas_reclaman tr.fila_personas_reclaman").each(function (index_fila, fila) {
			const myElement = $( fila ).find( 'input.persona-reclama, select.persona-reclama' );
			let persona = [];

			for (let i = 0; i < myElement.length; i++) {
				persona = [ ...persona, { [myElement[i].name]: myElement[i].value } ]
			}

			personas_reclaman[index_fila] = [...persona];
		});

		data = [ ...data, personas_reclaman ]

		return data;
	}

	/**
	 * * Metodo para hacer petición ajax y consultar datos
	 * * básicos de paceinte.
	 * 
	 * @param {Element} formulario	Elemento formulario 
	 */
	static consultarDatos( wemp_pmla, data )
	{
		$.ajax({
			url		:	"metodos_autorizacion.php?accion=consultarDatos&&wemp_pmla=" + wemp_pmla,
			type	:	"POST",
			data,
			success :	function(respuesta){
				$( "div#div-carga" ).addClass("oculto");

				respuesta = $.parseJSON(respuesta);

				switch( respuesta.state_mgs )
				{
					case "exitosa":
						$( "span#message" ).html( 'Consultado exitosamente!!' );
						$( "div#alert-message" ).removeClass( 'alert-warning' );
						$( "div#alert-message" ).removeClass( 'alert-danger' );
						$( "div#alert-message" ).addClass( 'alert-success' );
						$( "div#alert-message" ).removeClass( 'oculto' );

						setTimeout(function(){

							let array_edad = respuesta.data.Paciente.Edad.split(' ');

							$( "div.consulta-parametros" ).addClass("oculto");
							$( "div.respuesta-consulta" ).removeClass("oculto");
							$( "button.consultar-datos" ).prop('disabled', false);
							$( "input#Authis" ).prop('disabled', false);
							$( "input#Auting" ).prop('disabled', false);

							$( "input#nom_pac" ).val( decodeURIComponent( escape( respuesta.data.Paciente.Nombre_Paciente ) ) );
							$( "input#pac_doctxtNumDoc" ).val( respuesta.data.Paciente.Documento );
							$( "input#pac_edatxtEdad" ).val( array_edad[0] + ' ' + decodeURIComponent( escape('Años') ) );
							$( "input#pac_sextxtSexo" ).val( respuesta.data.Paciente.Sexo );
							$( "input#pac_epstxtEps" ).val( '[' + respuesta.data.Paciente.NitResponsable + '] - ' + decodeURIComponent( escape( respuesta.data.Paciente.NombreResponsable.split('-->')[0] ) ) );
							$( "input#egr_histxtNumHis" ).val( $( "input#Authis" ).val() );
							$( "input#egr_ingtxtNumIng" ).val( $( "input#Auting" ).val() != "" ? $( "input#Auting" ).val() : respuesta.data.Paciente.Ingreso );
							$( "input#ing_feitxtFecIng" ).val( respuesta.data.Paciente.FechaIngreso );
							$( "input#ing_hintxtHorIng" ).val( respuesta.data.Paciente.HoraIngreso );

							if( respuesta.data.Autorizacion.Autoriza != undefined || respuesta.data.Autorizacion.Autoriza != 'undefined' )
							{
								respuesta.data.Autorizacion.Autoriza == 'on' ? $("input#aut_inf_radAutS").prop("checked", true) : $("input#aut_inf_radAutN").prop("checked", true);
							}

							$( "textarea#aut_obs" ).val( 
								( respuesta.data.Autorizacion.Observacion == '' || respuesta.data.Autorizacion.Observacion == undefined ||
								respuesta.data.Autorizacion.Observacion == 'undefined' ) ? '' :
								decodeURIComponent( escape( respuesta.data.Autorizacion.Observacion ) ) );

							$( "input#pac_tdoselTipoDoc" ).val( autorizacionHCE.tipoDocumento( respuesta.data.Paciente.TipoDocumento ) );

							if ( respuesta.data.Persona_Autorizada.length > 0 || respuesta.data.Persona_Reclaman.length > 0)
							{
								autorizacionHCE.llenarPersonas( respuesta.data );
							}
						}, 1500);

						break;
					case "exitosa-parcial":
						$( "div.consulta-parametros" ).removeClass("oculto");
						$( "div.respuesta-consulta" ).addClass("oculto");
						$( "button.consultar-datos" ).prop('disabled', false);
						$( "input#Authis" ).prop('disabled', false);
						$( "input#Auting" ).prop('disabled', false);

						$( "span#message" ).html( '' + respuesta.description + ' ' + respuesta.data );
						$( "div#alert-message" ).removeClass( 'alert-success' );
						$( "div#alert-message" ).removeClass( 'alert-danger' );
						$( "div#alert-message" ).addClass( 'alert-warning' );
						$( "div#alert-message" ).removeClass( 'oculto' );
						break;
					default:
						$( "span#message" ).html( 'Respuesta no esperada' + respuesta.description );
						$( "div#alert-message" ).removeClass( 'alert-success' );
						$( "div#alert-message" ).removeClass( 'alert-warning' );
						$( "div#alert-message" ).addClass( 'alert-danger' );
						$( "div#alert-message" ).removeClass( 'oculto' );
						break;
				}
			}
		});
	}

	/**
	 * * Metodo para crear los select de manera estática permitiendo recibir datos
	 * * como parametro.
	 * 
	 * @param {String}	tipoSelect	Tipo de select a construir  
	 * @param {String}	clase		Clase a asignar 
	 * @param {Int}		index		Posición del elemento 
	 */
	static crearSelect( tipoSelect, clase, index ){
		let select = '';
		
		if( tipoSelect == 'cc' )
		{
			select = `
				<select id="dau_tdo_${index}" name="dau_tdo" egresoautomatico="no" class="form-control form-control-sm ${clase}">
					<option value="">Seleccione...</option>
					<option value="CC">CEDULA DE CIUDADANIA</option>
					<option value="TI">TARJETA DE IDENTIDAD</option>
					<option value="MS">MENOR SIN IDENTIFICACION</option>
					<option value="AS">ADULTO SIN IDENTIFICACION</option>
					<option value="CE">CEDULA DE EXTRANJERIA</option>
					<option value="RC">REGISTRO CIVIL</option>
					<option value="PA">PASAPORTE</option>
					<option value="NU">NUMERO UNICO DE IDENTIFICACION</option>
					<option value="NI">NIT</option>
					<option value="CD">CARNE DIPLOMATICO</option>
					<option value="NV">CERTIFICADO DE NACIDO VIVO</option>
					<option value="SC">SALVOCONDUCTO</option>
					<option value="PE">PERMISO ESPECIAL DE PERMANENCIA</option>
				</select>`;
		}
		else if( tipoSelect == 'par' )
		{
			select = `
				<select id="dau_par_${index}" name="dau_par" egresoautomatico="no" class="form-control form-control-sm ${clase}">
					<option value="">Seleccione...</option>
					<option value="01">Padres</option>
					<option value="02">Hermanos</option>
					<option value="03">Hijos</option>
					<option value="04">Esposo(a) o compañero(a)</option>
					<option value="05">Otro familiar</option>
					<option value="06">Tio(a)</option>
					<option value="07">Primo(a)</option>
					<option value="08">Nieto(a)</option>
					<option value="09">Abuelo(a)</option>
				</select>`;
		}

		return select;
	}

	/**
	 * * Metodo para llenar los datos de las personas autorizadas.
	 * 
	 * @param {Array}	Persona_Autorizada	Array de personas autorizadas  
	 * @param {Array}	Persona_Reclaman	Array de personas que reclaman  
	 */
	static llenarPersonas( { Persona_Autorizada, Persona_Reclaman } )
	{
		if( Persona_Autorizada.length > 0 )
		{
			$("table#tabla_personas_autorizadas tr").each(function (index_fila, fila) {
				$(fila).remove();
			});

			Persona_Autorizada.map(( persona, index ) => {

				let fila = `
					<tr class='fila2 fila_personas_autorizadas'>
						<td>
							${ autorizacionHCE.crearSelect( 'cc', 'tipo-doc-persona-autorizada persona-autorizada', `${index}_personas_autorizadas` ) }
							<input type='hidden' name='dau_tip' class='persona-autorizada' egresoAutomatico='no' value='1' >
						</td>
						<td><input type='text' name='dau_doc' class='form-control form-control-sm txt-autoriza persona-autorizada' msgaqua='Documento' egresoAutomatico='no' value='${ persona.Daudoc }'></td>
						<td><input type='text' name='dau_nom' class='form-control form-control-sm txt-autoriza persona-autorizada' msgaqua='Nombre'    egresoAutomatico='no' value='${ persona.Daunom }'></td>
						<td>${ autorizacionHCE.crearSelect( 'par', 'parentezco-persona-autorizada persona-autorizada', `${index}_personas_autorizadas` ) }</td>
					</tr>`;

				$('#tabla_personas_autorizadas').append(fila);

				$("#dau_tdo_"+index+"_personas_autorizadas option[value='" + persona.Dautdo + "']").attr("selected", true);
				$("#dau_par_"+index+"_personas_autorizadas option[value='" + persona.Daupar + "']").attr("selected", true);
			} );
		}

		if( Persona_Reclaman.length > 0 )
		{
			$("table#tabla_personas_reclaman tr").each(function (index_fila, fila) {
				$(fila).remove();
			});

			Persona_Reclaman.map( ( persona, index ) => {

				let fila = `
					<tr class='fila2 fila_personas_reclaman'>
						<td>
							${ autorizacionHCE.crearSelect( 'cc', 'tipo-doc-persona-reclama persona-reclama', `${index}_personas_reclaman` ) }
							<input type='hidden' name='dau_tip' value='2' calss='persona-reclama' egresoAutomatico='no'>
						</td>
						<td><input type='text' name='dau_doc' class='form-control form-control-sm txt-reclama persona-reclama' msgaqua='Documento' egresoAutomatico='no' value='${ persona.Daudoc }'></td>
						<td><input type='text' name='dau_nom' class='form-control form-control-sm txt-reclama persona-reclama' msgaqua='Nombre'    egresoAutomatico='no' value='${ persona.Daunom }'></td>
						<td>${ autorizacionHCE.crearSelect( 'par', 'parentezco-persona-reclama persona-reclama', `${index}_personas_reclaman` ) }</td>
					</tr>`;

				$('#tabla_personas_reclaman').append(fila);

				$("#dau_tdo_"+index+"_personas_reclaman option[value='" + persona.Dautdo + "']").attr("selected", true);
				$("#dau_par_"+index+"_personas_reclaman option[value='" + persona.Daupar + "']").attr("selected", true);
			} );
		}
	}

	/**
	 * * Metodo que permiten obtener el tipo de documento
	 * 
	 * @param {String} TipoDocumento
	 */
	static tipoDocumento( TipoDocumento )
	{
		let respuesta = '';

		switch( TipoDocumento )
		{
			case 'CC':
				respuesta = 'CEDULA DE CIUDADANIA';
				break;
			case 'TI':
				respuesta = 'TARJETA DE IDENTIDAD';
				break;
			case 'MS':
				respuesta = 'MENOR SIN IDENTIFICACION';
				break;
			case 'AS':
				respuesta = 'ADULTO SIN IDENTIFICACION';
				break;
			case 'CE':
				respuesta = 'CEDULA DE EXTRANJERIA';
				break;
			case 'RC':
				respuesta = 'REGISTRO CIVIL';
				break;
			case 'PA':
				respuesta = 'PASAPORTE';
				break;
			case 'NU':
				respuesta = 'NUMERO UNICO DE IDENTIFICACION';
				break;
			case 'NI':
				respuesta = 'NIT';
				break;
			case 'CD':
				respuesta = 'CARNE DIPLOMATICO';
				break;
			case 'NV':
				respuesta = 'CERTIFICADO DE NACIDO VIVO';
				break;
			case 'SC':
				respuesta = 'SALVOCONDUCTO';
				break;
			case 'PE':
				respuesta = 'PERMISO ESPECIAL DE PERMANENCIA';
				break;
			default:
				respuesta = 'TIPO DE DOCUMENTO NO DEFINIDO';
				break;
		}

		return respuesta;
	}

	/**
	 * * Petición ajax para insertar autorización, personas autorizadas y personas que reclaman
	 * 
	 * @param {String} wemp_pmla 
	 * @param {Array} datos 
	 */
	static guardarAutorizacion( wemp_pmla, datos )
	{
		$.ajax({
			url		:	"metodos_autorizacion.php?accion=guardarAutorizacion&&wemp_pmla=" + wemp_pmla,
			type	:	"POST",
			data	:	{
				"Datos" : datos
			},
			success : function( respuesta )
			{
				$( "div#div-carga-guardar" ).addClass("oculto");

				respuesta = $.parseJSON(respuesta);

				switch( respuesta.state_mgs )
				{
					case "created":
					case "updated":
						$( "span#message-respuesta" ).html( respuesta.description );
						$( "div#alert-respuesta" ).removeClass( 'alert-danger' );
						$( "div#alert-respuesta" ).addClass( 'alert-success' );
						$( "div#alert-respuesta" ).removeClass( 'oculto' );

						$( "button.guardar-datos" ).prop("disabled", false);
						$( "button.regresar" ).prop("disabled", false);
						break;
					default:
						$( "span#message-respuesta" ).html( respuesta.description );
						$( "div#alert-respuesta" ).removeClass( 'alert-success' );
						$( "div#alert-respuesta" ).addClass( 'alert-danger' );
						$( "div#alert-respuesta" ).removeClass( 'oculto' );

						$( "button.guardar-datos" ).prop("disabled", false);
						$( "button.regresar" ).prop("disabled", false);
						break;
				}
			}
		});
	}

	/**
	 * * Metodo para clonar la primera fila de la tabla.
	 * 
	 * @param {String}	tabla_referencia	Identificador de tabla  
	 */
	static addFila2(tabla_referencia)
	{
		let clon;
		
		let cant = $("#"+tabla_referencia+" >tr").length;

		// let clon = $("#"+tabla_referencia+" >tr").clone(false);
		if ( cant > 1 ) {
			clon = $("#"+tabla_referencia+" >tr").eq(-1).clone(false);
		}
		else
		{
			clon = $("#"+tabla_referencia+" >tr").eq(0).clone(false);
		}
		
		$("#"+tabla_referencia).append(clon);
		
		if ( cant > 1 ) {
			// let clon = $("#"+tabla_referencia+" >tr").eq(cant).clone(false);
			$("#"+tabla_referencia+" >tr").eq(cant-1).find("input[type=text],input[type=hidden],select").val("");
		}
		else
		{
			$("#"+tabla_referencia+" >tr").eq(-1).find("input[type=text],input[type=hidden],select").val("");
		}

		$.each( $( "body table#tabla_personas_autorizadas" ).find( "input[name='dau_tip']" ), function()
		{
			$( this ).val(1);
		});
		
		$.each( $( "body table#tabla_personas_reclaman" ).find( "input[name='dau_tip']" ), function()
		{
			$( this ).val(2);
		});
	}

	/**
	 * * Metodo para validar que los elementos del formulario esten diligenciados.
	 * 
	 * @param {String}	form	Identificador del formulario  
	 */
	static validar( form )
	{
		$( "#" + form ).validate({
			rules: {
				Authis: "required",
				Auting: "required",
			},
			errorElement: 'span',
			errorPlacement: function (error, element) {
				error.addClass('invalid-feedback');
				element.closest('.form-group').append(error);
			},
			highlight: function (element) {
				$(element).addClass('error');
				swal(
					'Datos incompletos',
					'Los campos sombreados en rojo son obligatorio, por favor diligencielos!',
					'error'
				);
			},
			unhighlight: function (element) {
				$(element).removeClass('error');
			}
		});

		return $( "#" + form ).valid();
	}

	/**
	 * * Metodo para limpiar los elementos del formulario y ocultar los divs necesarios.
	 * 
	 * @param {String}	form	Identificador del formulario  
	 */
	static limpiar()
	{
		// * Limpiar inputs
		$( "input#nom_pac" ).val( '' );
		$( "input#pac_doctxtNumDoc" ).val( '' );
		$( "input#pac_edatxtEdad" ).val( '' );
		$( "input#pac_sextxtSexo" ).val( '' );
		$( "input#pac_epstxtEps" ).val( '' );
		$( "input#egr_histxtNumHis" ).val( '' );
		$( "input#egr_ingtxtNumIng" ).val( '' );
		$( "input#pac_tdoselTipoDoc" ).val( '' );
		$( "input#ing_feitxtFecIng" ).val( '' );
		$( "input#ing_hintxtHorIng" ).val( '' );
		$( "input#Authis" ).val( '' );
		$( "input#Auting" ).val( '' );

		// * Habilitar inputs, botones y divs
		$( "div.consulta-parametros" ).removeClass("oculto");
		$( "div.respuesta-consulta" ).addClass("oculto");
		
		$( "div#alert-message" ).removeClass( 'alert-success' );
		$( "div#alert-message" ).addClass( 'alert-danger' );
		$( "div#alert-message" ).addClass( 'oculto' );
		
		$( "button.consultar-datos" ).prop('disabled', false);
		$( "div#div-carga" ).addClass("oculto");
		$( "input#Authis" ).prop('disabled', false);
		$( "input#Auting" ).prop('disabled', false);

		$("input#aut_inf_radAutS").prop("checked", false);
		$("input#aut_inf_radAutN").prop("checked", false);

		$.each( $( "body table#tabla_personas_reclaman" ).find( "input.txt-reclama, textarea#aut_obs, select" ), function()
		{
			$( this ).val("");
			console.log( $( this ) );
		});

		$.each( $( "body table#tabla_personas_autorizadas" ).find( "input.txt-autoriza, textarea#aut_obs, select" ), function()
		{
			$( this ).val("");
			console.log( $( this ) );
		});
	}
}

autorizacionHCE.Init();