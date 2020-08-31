/*********************************************************************************************************************
 * FUNCIONES PARA AJAX
 *********************************************************************************************************************/

/************************************************************************************************
 * Esta funcion crea un objeto json necesario para poder enviar la información por el ajax de
 * Jquery. El objeto que devuelve la funcion es de la siguiente forma
 *
 * obJson = {
 *		campo1.name : campo1.value,
 *		campo2.name : campo2.value,
 *		campo3.name : campo3.value,
 *		.
 *		.
 *		.
 * }
 *
 * Donde campoi es el elemento html que contiene el valor a enviar para procesar en el ajax
 *
 * Cada campo se encuentra en contenedor, este puede ser un div, form, td, tr, table, etc...
 * En general un campo que puede contener entre sus etiquetas otros elementos html
 *
 * Si no se especifica un contenedor, el contenedor será window.
 *
 * Nota: Requiere jquery para que funcione este programa
 *     - Si se requiere enviar informacion adicional en el ajax de jquery deberá agregar dicha
 *		 información al objeto json devuelto por esta función de la siguiente forma:
 *
 *			var obj = cearUrlPorCamposJson();	//obj es el objeto devuetlo por la función
 *			obj.nombre = 'Dina Vazquez';		//Agregar la propiedad nombre al objeto
 *
 *		 También se puede hacer de esta forma:
 *
 *			var obj = cearUrlPorCamposJson();		//obj es el objeto devuetlo por la función
 *			obj[ "nombre" ] = 'Dina Vazquez';		//Agregar la propiedad nombre al objeto
 ************************************************************************************************/
function cearUrlPorCamposJson( contenedor, atributo, obJson ){

	if( !obJson ){
		var obJson = {};	//Así se crea un objeto
	}

	if( !atributo ){
		var atributo = 'name';
	}

	try{

		//elementos html que se quieren procesar
		var elHtml = $( "input,textarea,select", contenedor );

		//Si es igual a 0, verifco si es un solo elemento
		if( elHtml.length == 0 ){
			elHtml = $( contenedor );
		}

		if( elHtml.length > 0 ){

			//recorro todos los elementos formando un elemento json
			//cuya clave es el nombre del elemento y su valor el value del elemento
			elHtml.each(
				function(x){

					if( $( this ).attr( atributo ) ){

						switch( this.type.toLowerCase() )
						{
							case 'checkbox':
								if( this.checked == true ){
									// obJson[ this.name ] = "on";
									obJson[ $( this ).attr( atributo ) ] = "on";
								}
								else{
									// obJson[ this.name ] = "off";
									obJson[ $( this ).attr( atributo ) ] = "off";
								}
								break;

							case 'radio':
								if( this.checked == true ){
									// obJson[ this.name ] = $( this ).val();
									obJson[ $( this ).attr( atributo ) ] = $( this ).val();
								}
								break;

							default:
								if( true || $( this ).val() != '' ){
									// obJson[ this.name ] = $( this ).val();
									obJson[ $( this ).attr( atributo ) ] = $( this ).val();
								}
								break;
						}
					}
				}
			);
		}

		return obJson;	//le quito el & inicial
	}
	catch(e){
		alert( "Error: " + e );
		return false;
	}
}

/***************************************************************************************
 * Setea todos los datos
 *
 * Esta función llena todos los elementos de un contenedor (entendiendo por contenedor
 * a un elemento HTML que pueda contener otros elementos HTML como lo son los div, td,
 * table, document, etc. ) que tengan el atributo name igual a la propiedad del objeto
 * json y asignandole a dicho elemento el mismo valor que tenga la propiedad del json.
 *
 * El tercer parametro busca el atributo por el cual se desean setear los datos. Por
 * defecto el tercer parametro es name
 *
 * Nota:
 *	- Se le puede dar valor a cualquier elemento que soporte la propiedad value o la
 *    propiedad innerHTML
 *  - Hay elementos que no soportan la propiedad name como lo es un div, sin embargo
 *    se le puede asignar como atributo en las etiquetas html, ejem:
 *
 *			<div name='dvContenedor'>...</div>
 *  - Si contenedor es vacio tomará como contenedor el elemento window por defecto
 ***************************************************************************************/
function setDatos( datosJson, contenedor, atributo ){

	//Recorro todos los elementos del objeto json
	for( var x in datosJson ){

		if( !atributo ){
			atributo = "name";
		}

		//x es la propiedad del elemento
		//Busco si hay un objeto con el mismo nombre que la propiedad del elemento
		var elemento = $( "["+atributo+"^="+x+"]", contenedor );

		//Si hay un elemento procedo a asignarle el valor
		if( elemento.length > 0 ){

			//Según el elemento le asigno el valor
			elemento.each(
				function( i ){

					//Si hay un campo nulo lo dejo como vacío
					if( !datosJson[x] ){
						datosJson[x] = '';
					}

					switch( this.tagName.toLowerCase() )
					{
						case 'input':
						{
							//datosJson[pacno1] == datosJson.pacno1
							//Si es un input reviso que caso es
							//Ya que si es radio solo uno de ellos puede tener el valor checkeado
							switch(this.type.toLowerCase() ){

								case 'radio':
									if( $( this ).val() == datosJson[x] ){
										$( this ).attr( 'checked', true );
									}
									break;

								case 'checkbox':

									if( datosJson[x] == 'on' ){
										$( this ).attr( 'checked', true );
									}
									else{
										$( this ).attr( 'checked', false );
									}
									break;

								default:
									if( datosJson[x] != "NULL" )
										$( this ).val( datosJson[x] );
									break;
							}
						}
						break;

						case 'select':{

							//Si el objeto contiene datos json
							//significa que se quiere llenar los options
							//reemplazando los que ya habían
							if( datosJson[x].options ){
								creandoOptions( this, datosJson[x].options );

								if( datosJson[x].value ){
									$( this ).val( datosJson[x].value );
								}
							}
							else{
								if( datosJson[x].value ){
									$( this ).val( datosJson[x].value );
								}
								else{
									$( this ).val( datosJson[x] );
								}
							}
						}
						break;

						case 'textarea':
						{
							$( this ).val( datosJson[x] );
						}
						break;

						default:
						{
							if( !this.value ){
								$( this ).html( datosJson[x] );
							}
							else{
								$( this ).val( datosJson[x] );
							}
						}
						break;
					}
				}
			);
		}
	}
}

/************************************************************************************************
 * Crea options nuevos para una selecion
 *
 * @param slCampos        Como tipo select
 * @param opciones        Array de objetos Json que tiene cada posicion dos propiedades:
 *						  des		Este campo indica que debe ir entre las etiquetas options
 *						  val		Value que tendrá el objeto option
 ************************************************************************************************/
function creandoOptions( slCampos, opciones )
{
    //options debe ser un array
    if( slCampos.tagName.toLowerCase() == "select" )
	{
	    //Borrando los options anteriores
	    var numOptions = slCampos.options.length;

	    for( var i = 0; i <  numOptions; i++ )
	    {
		   slCampos.removeChild( slCampos.options[0] );
	    }

		//recorro todas las opciones
		for( var opt in opciones ){

			var auxOpt = document.createElement( "option" );

			slCampos.options.add( auxOpt, slCampos.options.length );

			auxOpt.innerHTML = opciones[ opt ].des;

			if( opciones[ opt ].val /*&& opciones[ opt ].val != ''*/ ){
				auxOpt.value = opciones[ opt ].val;

				if( opciones[ opt ].val == "" ){
					$( auxOpt ).attr( "value", "" );
				}
			}
		}

		//Si es unica opción, lo dejo seleccionado
		if( slCampos.options.length == 1 ){
			slCampos.options.selectedIndex = 0;
		}
		else{
			//de lo contrario no selecciono nada
			slCampos.options.selectedIndex = -1;
		}
    }
}

/*********************************************************************************************************************
 * FIN FUNCIONES PARA AJAX
 *********************************************************************************************************************/

/*********************************************************************************************************************
 * FUNCIONES PARA MARCA DE AGUA
 *********************************************************************************************************************/


/********************************************************************************
 * Valida que los campos obligatorios (son los que tienen atributo msgError)
 * tengan algun valor para guardar
 *
 * Nota:
 * - Si el elemento que sea obligatorio tiene mascara, no se valida, ya que para
 *   está función tiene ya un valor por defecto. Actualmente la única mask que se
 *   valida es la de hora
 ********************************************************************************/
function validarCampos( contenedor )
{
	var todo_ok = true;

	camposConError = new Array();

	$( "[msgerror][omitirRequerido!='si']", contenedor ).each(function(){

		if( !this.disabled && this.style.display != 'none' ){

			switch( this.tagName.toLowerCase() ){

				case 'input':

					switch( this.type.toLowerCase() )
					{
						case 'radio':
						{
							var a = $( "[name="+this.name+"]:checked" )[0];
							if( !a )
							{
								this.aqClase = "campoRequerido";
								this.aqAttr = "msgerror";
								// activarMarcaAqua( this );
								todo_ok = false;

								camposConError[ camposConError.length ] = this;
							}
						}
						break;

						default:
							if( $( this ).val() == '' || (this.aqAttr && $( this ).val() == $( this ).attr( this.aqAttr ) ) )
							{


								if( this.id != "ing_vretxtValRem" && this.id != "restxtCodRes"){
									this.aqClase = "campoRequerido";
									this.aqAttr = "msgerror";
									activarMarcaAqua( this );
									todo_ok = false;

									camposConError[ camposConError.length ] = this;
								}

								// valor remitido obligatorio cuando está especificado que el paciente viene remitido y el ingreso es por accidente de tránsito
								if( this.id == "ing_vretxtValRem" && $("#pac_remradPacRems").is(":checked") && $("#ing_caiselOriAte").val() == "02" ){

									this.aqClase = "campoRequerido";
									this.aqAttr = "msgerror";
									activarMarcaAqua( this );
									todo_ok = false;

									camposConError[ camposConError.length ] = this;
								}

							}
						break;
					}
				break;

				default:
					if( $( this ).val() == '' || ( this.aqAttr && $( this ).val() == $( this ).attr( this.aqAttr ) ) )
					{
						this.aqClase = "campoRequerido";
						this.aqAttr = "msgerror";
						activarMarcaAqua( this );
						todo_ok = false;

						camposConError[ camposConError.length ] = this;
					}
				break;
			}
		}
	});

	return todo_ok;
}

// function validarCampos( contenedor )
// {
	// var todo_ok = true;

	// $( "[msgerror]", contenedor ).each(function(){

		// if( !this.disabled ){
			// if( $( this ).val() == '' || $( this ).val() == $( this ).attr( this.aqAttr ) )
			// {
				// this.aqClase = "campoRequerido";
				// this.aqAttr = "msgerror";
				// activarMarcaAqua( this );
				// todo_ok = false;
			// }
		// }
	// });

	// return todo_ok;
// }

/********************************************************************************
 * Inicializa las mascara de agua según el atributo que se ponga
 * y la clase de error que se desee manejar
 ********************************************************************************/
function marcarAqua( contenedor, atributo, clase )
{
	if( !atributo )
		atributo = "msgAqua";

	if( !clase )
		clase = "inputblank";

	$( "["+atributo+"]", contenedor ).each(function(){

		if( !$( this ).attr( "aqua" ) ){

			$( this ).attr( "aqua", true );

			this.aqClase = clase;
			this.aqAttr = atributo;

			//Asignado evento
			$(this).focus(function(){
				if( $(this).val() == $(this).attr( this.aqAttr )
					|| ( $(this).attr( "hora" ) != undefined && $(this).val() == '__:__:__' ) )
				{
					desactivarMarcaAqua( this );
				}
			})
			.blur(function() {
				if ( $(this).val() == '' ) {
					activarMarcaAqua( this );
				}
			});
		}
	});
}

/****************************************************************
 * Activa la marca de agua
 ****************************************************************/
function activarMarcaAqua( campo ){
	$(campo)
		.addClass( campo.aqClase )
		.val( $(campo).attr( campo.aqAttr ) );
}

/****************************************************************
 * Desactiva la marca de agua
 ****************************************************************/
function desactivarMarcaAqua( campo ){
	$(campo)
		.removeClass( campo.aqClase )
		.val('');

	//Si el campo tiene atributo hora le dejo el valor correspondiente del atributo
	if( $(campo).attr( "hora" ) != undefined ){
		$(campo).val( '__:__:__' );
	}
}

/****************************************************************
 * Inicia la marca de agua según el atributo con el que se alla
 * llamado inicialmente
 ****************************************************************/
function iniciarMarcaAqua( contenedor ){

	$( "[aqua]", contenedor ).each(function(){

		if( !this.disabled ){
			if ( $(this).val() == '' ) {
				$(this)
				.addClass( this.aqClase )
				.val( $(this).attr( this.aqAttr ) );
			}
		}
		else{
			$(this)
				.val( $(this).attr( this.aqAttr ) );
		}
	});
}

// function resetAqua( contenedor ){

	// $( "[aqua]", contenedor ).each(function(){

		// if( $(this).val() == '' || $(this).attr( this.aqAttr ) == $(this).val() ){

			// $(this).val( $(this).attr( "msgAqua" ) );
			// $(this).removeClass( this.aqClase )
			// $(this).addClass( "inputblank" );
		// }

		// this.aqClase = "inputblank";
		// this.aqAttr = "msgAqua";
	// });
// }

/************************************************************************************************
 * Revisa los campos en un contenedor y acomda según las condiciones la marca de agua
 ************************************************************************************************/
function resetAqua( contenedor ){

	$( "[aqua]", contenedor ).each(function(){

		//Si el campo esta habilitado trato de crear la marca de agua
		if( !this.disabled ){

			//Si el campo esta vacío o tiene el valor de la marca de agua activo la marca de agua
			if( $(this).val() == '' || $(this).attr( this.aqAttr ) == $(this).val() ){
				//activo la marca de agua
				activarMarcaAqua( this );
			}
			else{
				//Desactivo la marca de agua
				$(this).removeClass( this.aqClase )
			}
		}
		else{
			//Si el campo está deshabilitado desactivo siempre la marca de agua
			$(this).removeClass( this.aqClase )
		}

	});

}


/*********************************************************************************************************************
 * FIN DE FUNCIONES PARA MARCA DE AGUA
 *********************************************************************************************************************/






function formatoCampos(){

	/****************************************************************
	 * formatea hora para un campo
	 ****************************************************************/
	//Masked input
	$.mask.definitions['H']='[012]';
    $.mask.definitions['N']='[012345]';
    $.mask.definitions['n']='[0123456789]';

	$("[hora]").mask("Hn:Nn:Nn");

	$("[hora]").keyup(function(){

		if ( $(this).val().substring(0,1) == "2" && $(this).val().substring(0,2)*1 > 23 )
		{
			$(this).val( "2_:__:__" );
			$(this).caret(1);
		}
	});

	/****************************************************************
	 * formatea fecha para un campo
	 ****************************************************************/
	$( "[fecha]" ).datepicker({
		dateFormat:"yy-mm-dd",
		fontFamily: "verdana",
		dayNames: [ "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo" ],
		monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
		dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
		dayNamesShort: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
		monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
		changeMonth: true,
		changeYear: true,
		yearRange: "c-100:c+100"
	});

	/****************************************************************
	 * Permite solo numeros para un campo
	 ****************************************************************/
	$( "[numerico]" ).on({
		keypress: function(e){
			var key = e.keyCode || e.which;

			if( key != 9 && key != 8 ){
				if( String.fromCharCode(key).search( /[0-9]/g ) == -1 ){
					e.preventDefault();
				}
			}
		}
	});

	/****************************************************************
	 * Permite solo espacios y letras mayúsculas o minusculas para un campo
	 ****************************************************************/
	$( "[alfabetico]" ).on({
		keypress: function(e){
			var key = e.keyCode || e.which;

			if( key != 9 && key != 8 ){
				if( String.fromCharCode(key).search( /[A-Za-zÑñÁáÉéÍíÓóÚú \s.]/g ) == -1 ){
					e.preventDefault();
				}
			}
		}
	});


	/****************************************************************
	 * Permite solo espacios y letras mayúsculas o minusculas para un campo
	 ****************************************************************/
	$( "[correo]" ).on({
		blur: function(e){
			var key = e.keyCode || e.which;

			if( key != 9 && key != 8 ){
				var cadena = $( this ).val() + String.fromCharCode(key);
				console.log( cadena )
				// if( cadena.search( /^[^0-9][a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*\.[a-zA-Z]{2,4}$/g ) == -1 ){
				if( cadena.search( /^[\D][\w-]+(\.[\w]+)*@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/g ) == -1 ){
					alert( "correo invalido" )
					e.preventDefault();
				}
			}
		}
	});
	//$regular="/^[^0-9][a-zA-Z0-9_-]+([.][a-zA-Z0-9_-]+)*[@][a-zA-Z0-9_-]+([.][a-zA-Z0-9_-]+)*[.][a-zA-Z]{2,4}$/";

	$( "[depend]" ).each(function(x){
		$( "#"+$( this ).attr( "depend" ) ).on({
			blur: function(){
				if( $( this ).val() != '' && $( this ).val() != $( this ).attr( "msgError" ) ){
					if( $( "[depend=" + this.id + "]" ).val() == $( "[depend=" + this.id + "]" ).attr( "msgError" ) ){
						$( "[depend=" + this.id + "]" ).val( '' );
					}

					$( "[depend=" + this.id + "]" ).attr( "restAqua", $( "[depend=" + this.id + "]" ).attr( "msgError" ) );
					$( "[depend=" + this.id + "]" ).removeAttr( "msgError" );
					$( "[depend=" + this.id + "]" )[0].aqClase = '';
					$( "[depend=" + this.id + "]" ).removeAttr( "aqua" );
					$( "[depend=" + this.id + "]" ).removeClass( "campoRequerido" );

					//Se descomenta está línea
					// $( "[depend=" + this.id + "]" ).blur();
				}
				else{
					$( "[depend=" + this.id + "]" ).attr( "msgError", $( "[depend=" + this.id + "]" ).attr( "restAqua" ) );
					$( "[depend=" + this.id + "]" )[0].aqClase = 'campoRequerido';
					$( "[depend=" + this.id + "]" ).attr( "aqua", "true" );
					$( "[depend=" + this.id + "]" ).removeAttr( "restAqua" );


					if( $( "[depend=" + this.id + "]" ).val() == '' || $( "[depend=" + this.id + "]" ).val() == $( "[depend=" + this.id + "]" ).attr( "msgError" ) ){
						$( "[depend=" + this.id + "]" ).addClass( "campoRequerido" );
						$( "[depend=" + this.id + "]" ).val( $( "[depend=" + this.id + "]" ).attr( "msgError" ) );
					}
				}
			}
		});
	});

	// marcarAqua( '', 'msgError', 'campoRequerido' );
	// iniciarMarcaAqua();
}