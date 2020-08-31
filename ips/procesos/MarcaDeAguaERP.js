/*********************************************************************************************************************
 * FUNCIONES PARA MARCA DE AGUA
 *********************************************************************************************************************/

function validarCampos( contenedor )
{
	var todo_ok = true;

	$( "[msgerror]", contenedor ).each(function(){

		if( $( this ).val() == '' || $( this ).val() == $( this ).attr( this.aqAttr ) )
		{
			this.aqClase = "campoRequerido";
			this.aqAttr = "msgerror";
			activarMarcaAqua( this );
			todo_ok = false;
		}
	});
	
	return todo_ok;
}

function iniciarMsgError(contenedor)
{
}

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
			$(this).focus(function() {
				if ($(this).val() == $(this).attr( this.aqAttr ) ){
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

function activarMarcaAqua( campo ){
	$(campo)
		.addClass( campo.aqClase )
		.val( $(campo).attr( campo.aqAttr ) );
}

function desactivarMarcaAqua( campo ){
	$(campo)
		.removeClass( campo.aqClase )
		.val('');
}

function iniciarMarcaAqua( contenedor ){
	
	$( "[aqua]", contenedor ).each(function(){
		if ( $(this).val() == '' ) {
			$(this)
			.addClass( this.aqClase )
			.val( $(this).attr( this.aqAttr ) );
		}
	});
}

function resetAqua( contenedor ){

	$( "[aqua]", contenedor ).each(function(){
	
		if( $(this).val() == '' || $(this).attr( this.aqAttr ) == $(this).val() ){
		
			$(this).val( $(this).attr( "msgAqua" ) );
			$(this).removeClass( this.aqClase )
			$(this).addClass( "inputblank" );
		}
	
		this.aqClase = "inputblank";
		this.aqAttr = "msgAqua";
	});
}

/*********************************************************************************************************************
 * FIN DE FUNCIONES PARA MARCA DE AGUA
 *********************************************************************************************************************/