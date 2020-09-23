<?php
include_once("conex.php");


$accion_iq = "";
if(!empty($emp))
	$wemp_pmla = $emp;
$existeFacturacionERP = true;
$desde_CargosPDA = true;
if( !isset($facturacionErp) ){
	$existeFacturacionERP = false;
	echo "<html>";
	echo "<head>";
	echo "<title>CARGOS </title>";
?>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
<script type="text/javascript">

function msgReemplazo( resp ){
 	
 	try{
		
		var respuesta = resp.split("|");

		switch(respuesta[0]){
			
			case '0':
				mensaje = "No se pudo modificar el artículo.";
				break;
				
			case '8':
				auxMensaje = " Puede dispensar los medicamentos que correspondan a partir de la siguiente ronda.";
				
			case '1':
				mensaje = "El artículo se ha reemplazado correctamente. Los calculos son realizados a partir de la siguiente ronda.";
				break;
				
			case '2':
				mensaje = "El artículo se ha modificado correctamente.";
				break;
				
			case '3':
				mensaje = "El artículo no se puede modificar si se encuentra suspendido.";
				break;
				
			case '4':
				mensaje = "El artículo ya existe y es no duplicable.";
				break;
				
			case '5':
				mensaje = "No se pudo modificar el articulo.\nYa existe el artículo con la misma fecha y hora de inicio";
				break;
				
			case '6':
				mensaje = "No se pudo modificar el articulo. El articulo no tiene la misma via de administración.";
				break;
				
			case '7':
				mensaje = "No se pudo modificar el articulo. El articulo nuevo no tiene vias de administración registradas en la tabla DEFINICION FRACCIONES POR ARTICULOS.";
				break;
			
			default:
				mensaje = "No especificado: "+resp;
				break;
		}
		
		jAlert( mensaje, "ALERTA" );
		// alert( resp );
 			
 	}
	catch(e){ 
	}
 }

window.onload = function(){
	
	if( $( lote ).length > 0  && $( "[name=art\\[can\\]]" ).length > 0 ){
		
		$( "#ACEPTAR" ).attr({disabled:true});
		
		function habilitarACEPTAR(){			
			
			if( $( lote ).length > 0  && $( "[name=art\\[can\\]]" ).length > 0 ){
				if( $.trim( $( lote ).val() ) != '' && $( "[name=art\\[can\\]]" ).val()*1 > 0 ){
					$( "#ACEPTAR" ).attr({disabled:false});
				}
				else{
					$( "#ACEPTAR" ).attr({disabled:true});
				}
			}
		}
		
		$( lote ).change(function(){
		
			var cantidad = $( "[name=art\\[can\\]]" );
			
			if( cantidad.length > 0 ){
				
				var canLote = $( "option:selected", this ).data( "cantidad" );
				
				if( cantidad.length>0 ){
					if( cantidad.val() > canLote ){
						cantidad.val( canLote );
						alert( "La cantidad a dispensar no puede ser mayor a "+canLote );
						
						try{
							$( "[name=art\\[can\\]]" ).val( canLote );
							$( aMax ).html( canLote );
						}
						catch(e){}
					 }
					 else{
						habilitarACEPTAR();
					 }
				}
				else{
					habilitarACEPTAR();
				}
			}
			else{
				habilitarACEPTAR();
			}
			
		});
		
		$( "[name=art\\[can\\]]" ).change(function(){
			
			if( $( lote ).length > 0 ){
				
				var canLote = $( "option:selected", lote ).data( "cantidad" );

				if( canLote && canLote > 0 ){
					
					if( canLote < $( this ).val() ){
						alert( "La cantidad a dispensar no puede ser mayor a "+canLote );
						$( this ).val( canLote );
					}
				}
			}
			
			habilitarACEPTAR();
		});
	}
	
	
	
	
	setInterval(function() {
		$('.blink').effect("pulsate", {}, 5000);
	}, 1000);
}

function mostrarAlerta( articulo ){
	alert( articulo );
}

function checkMMQ( cargar ){

	if( cargar.checked == true ){
		cargar.value = "on";
	}
	else{
		cargar.value = "off";
	}
	window.document.carga.submit();

}


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

function inhabilitarCampos(){

	var btAceptar = document.getElementById( "ACEPTAR" );

	if( btAceptar ){
		btAceptar.disabled = true;
	}

	var btCodigo = document.forms[0].elements[ "artcod" ];

	if( btCodigo ){
		btCodigo.readonly = true;
	}
}

function reemplazar( cmp, valIdo ){

	var inIdos = document.getElementById( 'idoReemplazo' );
	
	if( cmp.className.toLowerCase() != 'gray'){
		cmp.className = 'gray';
		if( inIdos.value != '' )
			inIdos.value += ","+valIdo
		else
			inIdos.value = valIdo
	}
	else{
		inIdos.value = inIdos.value.replace( new RegExp( "\\b"+valIdo+"\\b", "g" ) , "" );	//Se borra el ido del articulo
		
		inIdos.value = inIdos.value.replace( new RegExp( "^,|,$", "g" ) , "" );	//SE borran las comas al inicio o final de la cadena
			
		cmp.className = 'colorAzul5';
	}
}

function cancelarReemp( ){
	document.getElementById( 'idoReemplazo' ).value = "";
	document.forms[0].submit();
}

function aceptarReemp(){
	document.forms[0].submit();
}
</script>

<?php
echo "<div id='dvTitle' style='display:none;position:absolute'></div>";
if(isset($historia))
{
	$pac['his']=$historia;
}
if(isset($artcod))
{
	$art['cod']=strtoupper( $artcod );
}

?>
  
<style type="text/css">
   	<!--Fondo Azul no muy oscuro y letra blanca -->
   	.tituloSup{color:#2a5db0;background:#FFFFF;font-family:Verdana;font-weight:bold;text-align:center;font-size:10pt;}
   	.tituloSup1{color:#c3d9ff;background:#FFFFF;font-family:Verdana;font-weight:bold;text-align:center;font-size:10pt;}
   	.titulo1{color:#FFFFFF;background:#2a5db0;font-family:Verdana;font-weight:bold;text-align:center;font-size:10pt;}
   	.titulo2{color:#003366;background:#c3d9ff;font-size:10pt;font-family:Verdana;text-align:center;}
   	.titulo3{color:#003366;background:#e8eef7;font-size:10pt;font-family:Verdana;text-align:center;}
   	.titulo4{color:#ffffff;background:purple;font-size:10pt;font-family:Verdana;text-align:center;}
   	.titulo5{color:#003366;background:pink;font-size:10pt;font-family:Verdana;text-align:center;}
   	.texto{color:#2a5db0;background:#FFFFFF;font-size:9pt;font-family:Verdana;text-align:center;}
   	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Verdana;font-weight:bold;text-align:center;}
   	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Verdana;font-weight:bold;text-align:center;}
   	.errorTitulo{color:#FF0000;font-size:12pt;font-family:Verdana;font-weight:bold;text-align:center;}
   	.alert{background:#FFFF00;color:#000000;font-size:12pt;font-family:Verdana;font-weight:bold;text-align:center;}
   	.warning{background:#FF6600;color:#000000;font-size:12pt;font-family:Verdana;font-weight:bold;text-align:center;}
   	.error{background:#FF0000;color:#000000;font-size:12pt;font-family:Verdana;font-weight:bold;text-align:center;}
	.gray{background-color: #cccccc}
	
	td fieldset{
		boder: 2px solid #e0e0e0;
	}
	
	legend{
		background-color: #e6e6e6;
		border-color: -moz-use-text-color #e0e0e0 #e0e0e0;
		border-image: none;
		border-style: none solid solid;
		border-width: 0 2px 2px;
		font-family: Verdana;
		font-size: 11pt;
	}
	
	.alert {
		font-size: 10pt;
	}
	
</style>

</head>
<BODY BGCOLOR="#FFFFFF" onSubmit='inhabilitarCampos()'>
<?php
}

/**
 * SISTEMA DE GRABACIÓN DE CARGOS POR MATRIX
 *
 * A) Ingresa usuario y se valida:
 *    1) Usuario existe en la tabla usuarios y root_000025
 *    2) El usuario esta asociado a un centro de costos de en la tabla 000011.
 *    Si no se cumple 1) o 2) muestra un error.
 *
 * B) El centro de costos al que esta asociado el usuario debe cumplir con alguna de las siguientes condiciones:
 *    1) Permite seleccionar centro de costos (000011.Ccosel == "on" <==> $cco['sel']==true).
 *       Sí el centro de costos permite celeccionar centro de costos, el programa pide el código del centro de costos precedido por CB.
 *       Al ingresar el centro de costos precedido por CB., el programa valida el formato (CB."cuatro digitos de centro de costo), si el formato es valido
 *       valida que este en la tabla 000011 y que este habilitado para facturar (000011.Ccofac=="on", como en 2) ).
 *    2) El centro de costos permite facturar 000011.Ccofac=="on" <==> $cco['fac'] .
 *
 *    Si existe algun problema con el centro de costos o el usuario hay un mensaje de error.
 *
 *    3) Sí el centro de costos solo aplica automaticamente si se carga del centro de costos(000011.Ccoasc=='on' <==> $cco['asc']==true ),
 *       entonces es necesario pedir la fecha y hora de la ronda de aplicación.
 *       Se validan que la fecha y hora sean validas, se almacenan en $fecApl y en $ronApl, y se pide la historia.
 *
 * C) Pide la historia y da la posibilidad de señalar, en un checkbox provisto paratal fin, que el paciente esta en proceso de alta.
 *
 *
 * D) Al ingresar la historia:
 *    1) Se valida si hay conexión odbc por medio de la función: connectOdbc(&$conex_o, 'inventarios').
 *       Sí hay conexión:
 *       1a) Llama a la función ivartcba para que actualice la tabla de relación de codigos de artículos de la institución y códigos de proveedor, sí es que no se ha actualizado en el día.
 *       1b) Llama a la función ivart para que actualice la tabla de relación de codigos de artículos de la institución, sí es que no se ha actualizado en el día.
 * 	  2) Se llama la función HistoriaMatrix, que valida si:la historia es cero (0) que el centro de costos este habilitado para trabajar con esta historia,
 *       y si la historia tiene caracteres alfanúmericos que estos correspondal al prefijo de la historia por MATRIX del centro de costos.
 *
 *
 *
 *
 * <b>A PARTIR DE AQUI SE DIVIDE LO QUE SE HACE CUANDO NO HAY UNIIX Y LO QUE HACE CUANDO HAY UNIX</b>
 *
 * <b>CON CONEXIÓN ODBC</b>
 *
 * E) LLama a la función validacionHistoriaUnix en donde se valida si la historia existe en inpac
 *    y de ser así verifica si esta en las tablas root_000036, root_000037 y 000018, si el paciente, con su ingreso actual,
 *    sí no esta en alguna de las tablas la función lo registra.
 *
 *    Sí la historia no esta activa en UNIX sale error.
 *
 * F) Se realizan las validaciones de MATRIX para la historia (ver abajo).
 *
 * G) Sí as validaciones salen bien, entonces el sistema llama a la función actualizacionDetalleRegistros(), esta función es bastante compleja
 *    e importante para el funcionamiento del sistema de cargos, se recomienda leer su documentación para el entendimiento de este programa.
 *    De forma muy escueta se puede decir que su función en este programa consiste en determinar si hay algun problema con un cargo que se haya
 *    hecho a la historia $pac['his'] durante el ingreso $pac['ing'].  El problema sera visible al usuario si en la pantalla el  table data <td>
 *    donde esta la información del paciente (nombre y habitación) no es azul si no de otro color, dentro del programa esto se logra porque
 *    actualizacionDetalleregistro() retorna un string que se almacena en $classHis y esta es la variable que se usa como clase del table data
 *    donde se imprime la información del usuario así <td class='".."'>, los valores que puede tomar classHis (osea los que puede retornar la función) segun
 *    los problemas encontrados con la historia son:
 *    1) titulo1: Todo esta bien, los registros activos estan en itdro con estado procesado droest="P", o estan en itdro con estado sin procesar (droest="S") pero la fecha corresponde a la actual.
 *    2) alert:   Hay registros de detalle sin procesar (S) y la fecha del encabezado no corresponde a la actual.
 *    3) warning: por lo menos un detalle en M, ni siquiera un encabezado, con un detalle es suficiente.
 *    4) error: por lo menos un encabezado que Fenues=I, es decir hay por lo menos un registro inconsistente en itdro.
 *
 * H) Pide el código de un artículo.
 *    Sí el centro de costos permite aprovechamiento ($cco['apr']== true, 000011.Ccoapr) ademas del campo para digitar o leer el artículo
 *    hay un check box para que el usuario señale si el artículo a cargar o devolver es por fuente de aprovechamiento.
 *
 * I) Se realizan las Validaciones de existencia del artículo en Matrix para el artículo (ver abajo "Validaciones en Matrix para el artículo").
 *
 * J) Se realizan las valida que el artículo tenga tarifa y saldo en UNIX, por el llamado a la función tarifaSaldo, sí el centro de costos no permite
 *    negativos ($cco['nce']== true <==> 000011.Cconce=='on' y no es un aprovechamoento ($aprov ==false) o una devolución ($tipTrans == "D") la
 *    función valida que haya suficiente cantidad de eartículo en el Cco para realizar el cargo.
 *
 * K) Si es una devolución $tipTrans == "D"  se verifica que exists suficiente cantidad para devolver (llamado a la función validacionDevolucion() ):
 *    1) Sí aplica automáticamente ($cco['apl'] == true <==> 000011.Ccoapl == 'on') se busca saldo en la tabla 000030.
 *    2) Sí NO aplica automáticamente ($cco['apl'] == false <==> 000011.Ccoapl == 'off') se busca saldo en la tabla 000004.
 *    Sí no hay suficiente cantidad saca error.
 *
 * L) Si es correcto se pide el consecutivo del documento ($dronum)en Matrix y la línea de ese documento ($drolin) por medio de un llamado a la función Numeracion().
 *
 * M) Se registra el cargo en itdro .
 *
 * N) Se realizan los registros correspondientes en Matrix(ver abajo "Registros de Cargo en Matrix").
 *
 * Ñ) Se muestra en pantalla dentro del textarea el articulo, con su nombre y cantidad.
 *
 * O) Se piden mas artículos.
 *
 *
 *
 * <b>SIN CONEXIÓN ODBC</b>
 *
 * E) LLama a la función infoPaciente(), la cual valida que este en las tablas de paciente de Matrix (root_000036, root_000037, 000018) y trae los datos allí contenidos.
 *    Sí el paciente no esta en esas tablas no se les puede cargar artículos.
 *
 * F) Se realizan las validaciones de MATRIX para la historia (ver abajo).
 *
 * G) Sí as validaciones salen bien, entonces el sistema pide el código de un artículo.
 *    Sí el centro de costos permite aprovechamiento ($cco['apr']== true, 000011.Ccoapr) ademas del campo para digitar o leer el artículo
 *    hay un check box para que el usuario señale si el artículo a cargar o devolver es por fuente de aprovechamiento.
 *
 * H) Se realizan las Validaciones de existencia del artículo en Matrix para el artículo (ver abajo "Validaciones en Matrix para el artículo").
 *
 * I) Si es una devolución $tipTrans == "D"  se verifica que exists suficiente cantidad para devolver (llamado a la función validacionDevolucion() ):
 *    1) Sí aplica automáticamente ($cco['apl'] == true <==> 000011.Ccoapl == 'on') se busca saldo en la tabla 000030.
 *    2) Sí NO aplica automáticamente ($cco['apl'] == false <==> 000011.Ccoapl == 'off') se busca saldo en la tabla 000004.
 *    Sí no hay suficiente cantidad saca error.
 *
 * J) Si es correcto se pide el consecutivo del documento ($dronum)en Matrix y la línea de ese documento ($drolin) por medio de un llamado a la función Numeracion(), que además hace el registro del encabezado del cargo (000002) sí es necesario.
 *
 * K) En este momento el artículo puedo o no permitir negativos ($art['neg'], 000008.Areneg), pero puede que en el momento que se establesca la conexión con UNIX
 *    y sea momento de verificar el saldo ya no este este parámetro igual.  Eso hace necesario almacenar temporalmente el parámetro, el programa lo hace de la siguiente forma:
 * 	  1) Si el programa NO permite negativos guarda en la variable $art['ari']="N*".$art['ari'], esta variable será almacenada en 000003.Fdeari.
 * 	  2) Si el programa Permite negativos guarda en la variable $art['ari']="P*".$art['ari'], esta variable será almacenada en 000003.Fdeari.
 *
 * L) Se realizan los registros correspondientes en Matrix(ver abajo "Registros de Cargo en Matrix").
 *
 * M) Se muestra en pantalla dentro del textarea el articulo, con su nombre y cantidad.
 *
 * N) Se piden mas artículos.
 *
 *
 *
 * <b>VALIDACIONES DE MATRIX PARA LA HISTORIA</b>
 *
 * A) El paciente no tiene alta definitiva $pac['ald']==false <==> 000018.Ubiald=='off'
 * B) El paciente no esta en proceso de traslado $pac['ptr'] ==false <==> 000018.Ubiptr=='off'
 * C) El paciente no esta en proceso de alta $pac['alp']==false <==> 000018.Ubialp=='off' o si esta en proceso de alta el usuario activo el checkbox que informa que el sabe que esta enproceso de alta $pac['ald']==true <==> 000018.Ubiald=='on' and $alp==true.
 * D) Sí el centro de costos es hospitalario ($cco['hos'] == true <==> 000011.Ccohos ==true, el paciente esta ubicado en ese centro de costos $pac['ubi'] == $cco['cod'] <==> 000018.Ubisac==$cco['cod'].
 *
 * Sí el paciente no cumple con alguna de las anteriores no se le pueden hacer cargos o devoluciones.
 *
 * <b>VALIDACIONES DE MATRIX PARA EL ARTÍCULO</b>
 *
 * A) Se guarda en $art['ari'] el código inicial digitado o leido por el usuario.
 * B) Se llama a la función BARCOD() para que recorte el código como es debido si supera los 14 caracteres.
 * C) Se llama a la función ArticuloCba() por si es un código de proveedor traiga en $art['cod'] el código propio de la clínica.
 * D) Se llama la función de artículos articulosEspeiales de este modo:
 *    1) Sí el código es un código especial retorna el codigo de la clinica, es decir 000008.Areces=$art['cod'] entonces $art['cod']=000008.Arecod.
 * 	  2) Se llenan las variables:
 * 	     2a) $art['var'] si el artículo tiene cantidad variable.
 *       2b) $art['can'] cantidad por defecto.
 *       2c) $art['max'] Cantidad máxima.
 *       2d) $art['neg'] permite negativos.
 *       Problema potencial: se permiten cargas fracciones por cantidad variable y no hay modo de determinar que artículos si lo deben permitir y cuales no.
 * E) Se llama a la función articuloExiste() para verificar el estado del artículo y recuperar el nombre($art['nom']), grupo($art['gru']) y unidad de medida ($art['uni']).
 *
 * Sí el artículo es de cantidad variable ($art['cva'] == true) no hace ninguna otra validación, si no que procesde a:
 * F) Pedir cantidad para hacer el cargo.
 * G) Validar que la cantidad no sea mayor a $art['max']
 * H) Validar que no sea menor que cero.
 * Si  alguna de las dos validaciones sale mal vuelve a pedir cantidad hasta que el usuario digite una cantidad válida.
 *
 *
 *
 * <b>REGISTROS DE CARGO EN MATRIX</b>
 *
 * A) Registrar el detalle de cargo en la tabla 000003 por medio del llamado a la función registrarDetalleCargo.
 * B) Pasan cosas diferentes dependiendo de las caracteristicas del centro de costos:
 *    1) El centro de costos aplica ($cco['apl'] == true <==> 000011.Ccoapl =='on')
 * 		 1a) Se llama a la función registrarSaldosAplicacion(), lo que mofifica el saldo de aplicación en la tabla 000030.
 *       1b) Se registra la aplicación en la tabla 000015 con la fecha actual como fecha de la ronda (000015.Aplfec) y la hora actual como hora de la ronda (000015.Aplron), por medio de la funcion registrarAplicacion.
 *    2) El centro de costos NO aplica ($cco['apl'] == false <==> 000011.Ccoapl =='off')
 *       2a) Se llama a la función registrarSaldosNoApl() , que modifica los saldos sencillos que estan en la tabla 000004.
 *       Sí es un un cargo de grabación ($tipTrans == 'C'), y el centro de costos aplica solo cuando se carga desde ahi ($cco['asc'] == true <==>000011.Ccoasc = 'on').
 *       2b) Se llama nuevamente a la función registrarSaldosNoApl() para aque haga una salida que va a corresponder a la aplicación que se efectuara en 2c), es decir se envía como parametro de tipo de transacción "D" de devolución para que sea una salida.
 *       2c) Efectuar una aplicación en la tabla 000015, con ronda de aplicación $ronApl y fecha de ronda $fecApl, a través d ela función registrarAplicacion().
 *
 *
 *
 * <b>MANEJO DE ERRORES</b>
 *
 * El programa maneja los errores por medio del arreglo $error, este se envía a las diferentes dfunciones y estas, de haber un error, retornan el mensaje así:
 *  $error[ok]:Descripción corta del error.
 *  $error[codInt]String[4]:Código del error interno, debe corresponder a alguno de la tabla 000010.
 *  $error[codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.
 *  $error[descSis]:Descripción del error del sistema.
 *
 * La función registrarError() se llama enviando como parámetro el arreglo, con esto se registra el error en la tabla 000005 y seeobtiene la clase "class" que se mostrara en la pantalla.
 * Es decir, cuando hay un error la tabla debe cambiar de color para indicarselo al usuario, ese cambio de color esta dictado por el class del <td>, lo que se obtiene
 * es el valor de class para el <td>
 *
 * @modified Septiembre 23 de 2020 (Edwin) 	   - Se valida que la fecha no sea vacia en la funcion consultarRegistroKardexPorArticulo. Si es vacia en la consulta se cambia el valor de fecha por 0000-00-00.
 * @modified Noviembre 6 de 2019  (Edwin)  	   - Al dispensar se tiene en cuenta el saldo del cco desde dónde se dispensa
 * @modified Agosto 28 de 2019  (Edwin)   	   - Si se están actualizando tarifas no se permite dispensar
 * @modified Julio 29 de 2019 (Edwin)	   	   - Se hacen cambios varios para poder dispensar los articulos de ayudas dx y generen saldos para poder aplicar articulos desde gestion de enfermería
 *												 Esto aplica si el cco es de ayuda dx y el campo ccosca está inactivo(off) en la tabla movhos_000011
 * @modified Julio 08 de 2019 (Edwin)	   	   - Si el paciente tiene ordenes y el paciente se encuentra en un cco de ayuda dx genera el saldo cargado para poder aplicar los articulos en el
 *												 programa de gestión de enfermería.
 * @modified Junio 19 de 2019 (Edwin)	   	   - Se corrige proceso de contingencia, las devoluciones de minibolsan hacían un proceso de cargo en unix
 * @modified Febrero 12 de 2019 (Edwin)	   	   - Cuando no hay conexión a Unix y se carga una minibolsa no se mueve el número de línea del medicamento a cargar
 * @modified Diciembre 12 de 2018 (Jessica)	   - Se comenta la validación de pacientes particulares que evita que los productos codificados o minibolsas se desglosen (se facturen sus insumos)
 * @modified Octubre 10 de 2018  (Edwin)	   - En ayudas diagnósticas (hemodinamia), al dispensar un artículo y quedar aplicado, en la aplicación no mostraba la última ronda
 *												 correcta de aplicación cuando la dosis del médica era mayor a la unidad del medicamento.
 *												 Ejemplo: Dosis ordenada 2 tableta, al dispensar 1 tableta, está primera tableta no quedaba con la ronda correcta de aplicación, 
 * 												 siempre mostraba la ronda 00:00:00 - AM en las aplicaciones
 * @modified Agosto 06 de 2018  (Edwin)   	   - Tanto las validaciones de paciente activo se hacen por matrix.
 *											   - Al hacer reemplazo de articulo se deja las observaciones del articulo anterior
 * @modified Junio 25 de 2018  (Edwin)		   - Al reemplazar articulos automáticos, en caso de un intento de reemplazo y no se realizce se muestra el por que no se hace el reemplazo.
 * @modified Junio 20 de 2018  (Edwin)		   - Cada vez que se ingresa una historia, se llama a la función ejecutarLogUnix, la cuál se encuentra en el include cargosSF.inc.php, para ejecutar los queries
 *												 de movimiento de inventario en unix (sifue, ivsal, ivmov, ivmovdet) que no se ejecutaron por bloqueo de tablas.
 * @modified Junio 8 de 2018 (Jonatan)		   - Se repara la funcion esUrgencias para que tome el prefijo de la tabla movhos correctamente.
 * @modified Mayo 31 de 2018 (Jonatan)		   - Se valida en la funcion esUrgencias si la variable $servicio tiene datos, si es asi, ejecuta la funcion.
 * @modified Mayo 7 de 2018  (Edwin)		   - Los articulos del gupo E00 del kardex (movhos_000054) de un paciente que no se encuente en urgencias, se marcan como no enviar para que no se puedan dispensar
 * @modified Abril 09 de 2018  (Edwin)   	   - Los cco de ayuda diagnóstica pueden grabar a los pacientes que se encuentran en urgencias
 * @modified Marzo 01 de 2018  (Edwin)   	   - Se modifica script para que se inserte el cco de donde se dispenda y el nombre del médico tratante en la tabla de lotes(cliame 000240) 
 * 												 para articulos implantables
 *											   - En la función articulosxpaciente no se tiene en cuenta la hora de recibo del paciente en piso en los calculos de la regleta, cuando se pasa de un cco 
 * 												 de urgencias, ingreso o cirugía debido a que un cco de ayuda dx solo dispensa para su propio centro de costos
 * @modified Febrero 21 de 2018  (Edwin)   	   - Para los articulos implantables, al dispensarlos pide el lote
 * @modified Febrero 15 de 2018  (Edwin)   	   - Los cco de ayuda dx no agregan articulos al carro, para ello se modifica la función agregarAlCarro
 * @modified Febrero 5 de 2018  (Edwin)   	   - Si un paciente se encuentra en una ayuda dx con ordenes, se dispensa de acuerdo a las ordnes
 *											   - Se habilita aprovechamiento el cual solo estaba activo para cirugía (ver 2010-09-24)
 * @modified Diciembre 18 de 2017  (Jessica)   Se adiciona el ingreso como parametro de la funcion consultarAlergiasDiagnosticosAnteriores() 
 *											   y se comenta el llamado a la función consultarAlergiaAlertas() ya que consultarAlergiasDiagnosticosAnteriores() 
 *											   llama a esta funcion
 * @modified Noviembre 27 de 2017  (Edwin MG)  Se quitan las funciones consultarInsumosProducto y registrarInsumosProducto y se muevan al script cargosSF.inc.php en include/movhos.
 *											   Esto por que son funciones que se encuetran iguales en devoluciones.php, cargos.php en movhos/procesos y facilita el mantenimiento de las
 *											   aplicaciones.
 *											   Adcionalmente se corrige el calculo de la cantidad del insumo a facturar para las minibolsas en la función registrarInsumosProducto. 
 *											   Se estaba tomando como factor de conversión de unidades el valor encontrado en la tabla de Articulos especiales (movhos_000008) el campo CANTIDAD POR DEFECTO(Arecde)
 *											   según el cco en que se dispensa y el valor de conversión correcto es el campo CONVERSION(Appcnv) en la tabla RELACION DE INSUMOS POR PRESENTACION(cenpro_000009)
 * @modified Noviembre 21 de 2017  (Edwin MG)  Los productos codificados o minibolsas no se desglosan(no se factura sus insumos) para los pacientes particulares
 * @modified Octbure 5 de 2017.	   (Edwin MG)  No se le puede dispensar articulos a pacientes en traslado
 * @modified Septiembre 15 de 2017 (Edwin MG)  Se crea la función ArticulosXPacienteCM que hace lo mismo que la función ArticulosXPaciente pero para productos de CM
 * @modified Septiembre 14 de 2017 (Edwin MG)  Se agrega parametro para manejar los articulos con condición tipo a necesidad como articulos sin condición de tipo a necesidad para los pacientes
 *											   que se encuentran ubicados en un cco con el parametros ciclos de 24 horas activo(ccoc24=on en movhos_000011)
 * @modified Septiembre 13 de 2017 (Edwin MG)  Para cargar articulso en minibolsas, se tiene en cuenta el codigo de la empresa * en la configuación que significa que es para todos los responsables
 * @modified Agosto 2 de 2017 (Edwin MG)   	   Si un articulo ya fue aplicado completamente no se busca las aplicaciones que tiene este.
 * @modified Abril 17 de 2017 (Edwin MG)	   En la funcion crearKardexAutomaticamente, al crear el encabezado del kardex se agrega el campo Karmeg
 * @modified Enero 24 de 2017 (Edwin MG)   	   Si el paciente está en un cco diferente de urgencias, se llama al proceso que cambia los articulos pertenecientes a LEV o IC marcados como ENVIAR
 *											   y los cambia a NO ENVIAR.
 * @modified Enero 05 de 2017 (Edwin MG)   	   Se quita la validación de que si es un medicamento a necesidad no y tiene el saldo se dispensa. Para qué los medicamentos a necesidad no 
 *											   dejen dispensar más de la dosis se cambia la frecuencia a 24 horas y hora de inicio.
 * @modified Diciembre 27 de 2016 (Edwin MG)   Se cambia mensaje cuando el usuario no tiene permisos de dispensar
 * @modified Diciembre 14 de 2016 (Edwin MG)   En la funcion crearKardexAutomaticamente, se agrega también que pueda generar los articulos de control a imprimir
 * @modified Diciembre 15 de 2016	(Jessica)  Se modifican las alertas para que traiga las ingresadas en movhos_000220
 * @modified Octubre 26 de 2016		(Edwin MG) Se corrige para que al momento de cargar MMQ no sume en la lista cuando se está pidiendo la cantidad variable
 * @modified Septiembre 22 de 2016	(Edwin MG) En la función recalcularDosisMaxima, se tienen en cuenta desde que horas comienza un medicamento
 *											   para hacer el calculo de dosis máxima y se agregan comentarios a la función.
 *											   Para el reemplazo automático se valida si el articulo fue leído con código de barras.
 * @modified Agosto 01 de 2016		(Edwin MG) Se cambia query para que no facture los insumos de productos inactivos en central de mezclas
 * @modified Abril 06 de 2016 (Edwin MG) Se muestran las alergias del perfil
 * @modified Septiembre 14 de 2015(Edwin MG) Se hacen los siguientes cambios:
 *											 - Si un medicamento es el reemplazo de otro, se tiene en cuenta las aplicaciones del medicamento anterior.
 *											 - Si hay más de un medicamento por reemplazar se pregunta por cuál quiere reemplazar.
 * @modified Septiembre 22 de 2015(Edwin MG) - Los articulos que tienen CTC, y están por agotarse se marcan con color igual al del perfil (amarillooscuro)
 * @modified Agosto 13 de 2015    (Edwar JS) Nuevo parámetro en la función "grabarArticuloPorPda" llamado "conexUnixParam", si el nuevo parámetro tiene un valor
 *                                           es porque se hizo una conexión global a unix y no debe ser cerrada por la instrucción odbc_close_all. Específicamente se desarrolló porque
 *                                           en liquidación de cirugía se hace una única conexión para todo el proceso de grabación y un posible bloqueo de tablas en unix, el odbc_close
 *                                           de esta función estaba generando que fallara el bloqueo o solo funcionara para algunos cargos y al momento de cerrar la conexión global
 *                                           del programa de liquidación salía error porque ya no se encontraba el link de conexión a unix.
 * @modified Julio 06 de 2015 		(Edwin MG) En la llamda de la función cargarArticulosADefinitivo se agrega parametro faltante
 * @modified Mayo 04 de 2015		(Edwin MG) Si un paciente está en urgencias y tienen proceso de traslado, solo deja dispensar si no se ha hecho la entrega del paciente
 * @modified Abril 27 de 2015		(Edwin MG) El reemplazo de articulos equivalentes es válido para cualquier cco, antes solo estaba para urgencias.
 * @modified Abril 09 de 2015		(Edwin MG) Se oculta la columna de saldo en la pantalla de la PDA.
 * @modified Abril 07 de 2015		(Edwin MG) Se añade saldo en la pantalla de PDA para los articulos que se muestran
 * @modified Mazo 26 de 2015		(Edwin MG) Se corrige la fracción en la función articulosxpaciente
 * @modified Mazo 17 de 2015		(Edwin MG) Para la función grabarArticuloPorPda se agrega la fecha y hora de aplicación en que se quiere que un articulo quede aplicado
 * @modified Febrero 25 de 2015		(Edwin MG) Se valida que solo los cambios realizado en Junio 27 de 2012 no sean cco de urgencias o de traslado
 * @modified Febrero 06 de 2015		(Edwin MG) Se comentan todas las llamadas a la petición de camilleros a solicitud de Beatriz Orrego
 * @modified Enero 22 de 2015 		(Edwin MG) - Se permite hacer cargos o devoluciones a pacientes que no se encuentren activos en UNIX desde cirugía
 * @modified Enero 21 de 2015 		(Edwin MG) - Se permite en la funcion grabarArticuloPorPda pacientes de alta siempre y cuando el cco desde donde se graba sea de cirugía
 * @modified Diciembre 29 de 2014 	(Edwin MG) - Se crean copias de las funciones getCCo e infoPaciente con el nombre getCcoPrima e infoPacientePrima respectivamente. Estas funciones se usan en
 *												 grabarArticuloPorPda y su código se encuentra en el archivo include cargosSF.inc.php de include/movhos
 * @modified Diciembre 29 de 2014 	(Edwin MG) - Mejoras varias en la función grabarArticuloPorPda
 * @modified Diciembre 26 de 2014 	(Edwin MG) - Se permite dispensar para urgencias la cantidad máxima según configuración en movhos_000008.
 *											   - Para cualquier servicio se sugiere la cantidad a dispensar
 * @modified Diciembre 16 de 2014 	(Edwin MG) - Se valida que solo se pueda cargar medicamentos desde urgencias cuando urgencias maneja ordenes a pacientes que se encuentren en urgencias
 * @modified Diciembre 10 de 2014 	(Edwin MG) - La reglata para urgencias se calcula mirando los saldos y aplicaciones del medicamento.
 * @modified Diciembre 03 de 2014 	(Edwin MG) - Para la función grabarArticuloPorPda, si se dispensa un articulo desde un cco de urgencia, el medicamento se deja grabar y facturar
 * @modified Noviembre 06 de 2014 	(Edwin MG) - Para urgencias y si el paciente tiene ordenes, lo articulos material medico quirurgico aplica automaticamente
 *												 Esta modficación hace que el articulo funcione como si fuera un articulo especial(movhos_000008) de aplicación automática.
 *												 Esto hace que el sistema mueva el saldo del articulo corespondiente en la tabla de saldos de pacientes(movhos_000004) en el campo salida total unix (spausa)
 * 												 y se haga una aplicación automática (movhos_000015).
 *												 Cómo se esta cargando un articulo el saldo del articulo también se mueve en entrada total Unix(spauen).
 *												 Si el paciente no tiene ordenes, los articulos en urgencias se siguen aplicando automáticamente. este movimiento implica:
 *														* Se mueve saldo en Saldos Paciente aplicado(movhos_000030)
 *														* Se registra aplicación en (movhos_000015)
 *											   - Se agrega función grabarArticuloPorPda, el cual se llama desde un programa externo (facturacion erp) y tiene las mismas validaciones
 *												 que el programa de cargos de PDA.
 *											   - Se agrega variable $facturacionErp, el cual hace que no se vea nada en el programa si esta existe. Esta variable fue agregada para poder llamar la función
 *												 grabarArticuloPorPda desde programas externos.
 * @modified Septiembre 04 de 2014 	(Edwin MG) Despues de terminar de dispensar se muestra el total de articulos dispensados por la PDA
 * @modified Noviembre 21 de 2013 	(Edwin MG) Al momento de cargar un producto codificado (articulo que está tanto en CM como en SF) se facturan sus insumos.
 *											   También se puede configurar un medicamento a facturar en lugar de otro en la tabla de articulos especiales (movhos_000008)
 * @modified Septiembre 24 de 2013 	(Edwin MG). Se crea campo nuevo en tabla de condiciones que permite cargar medicamentos a necesidad como si no lo fueran. Esto para poder
 *												cargar medicamentos a necesidad suficiente según el tiempo de dispensación y no con la regla actual que dice: lo suficiente para una dosis.
 * @modified Septiembre 12 de 2013 	(Edwin MG). Se cambia calculo para horas por centro de costos para que soporte mas de 24 horas.
 * @modified Julio 24 de 2013 	(Mario Cadavid). Se le da prioridad a la tabla 000091 de movimiento hospitalario para la aplicacón automatica
 *								Si el indicador Arsapl de la 000091  de movimiento hospitalario viene encendido se aplica automaticamente el artículo
 *								asi el indicador Areapl de la tabla 000008 de movimiento hospitalario no esté encendido
 * @modified Julio 4 de 2013 	(Mario Cadavid). Se suspende temporalmente la validación de aplicacion automatica (Es decir se devuelve el cambio hecho
 *								en Junio 6 de 2013) Esto ya que configurando un articulo en la tabla 000091 de movimiento hospitalario para que
 *								aplicara automaticamente, no lo hacia debido a que el articulo no estaba en la tabla movhos_000008. Queda entonces pendiente
 *								definir si la tabla 000091 de movimiento hospitalario es prioridad para aplicación automatica independiente si está o no
 *								en la tabla 000008 de movimiento hospitalario
 * @modified Julio 3 de 2013 	(Mario Cadavid). Se devuelve el cambio hecho en Junio 11 de 2013 ya que este cambio solo debe aplicar para central de mezclas
 *								Se devuelve el cambio hecho en Junio 26 de 2013 ya que este cambio solo debe aplicar para central de mezclas
 * @modified Julio 3 de 2013 	(Mario Cadavid). Se agregó la condición if(trim($historia)=='' || $historia=='0') $ind = 1; de modo que cuando
 *								la historia se 0 o vacia no se quede en un bucle en el ciclo que quita el 0 si es el primer caracter de la historia
 * @modified Junio 26 de 2013 	(Mario Cadavid). Se agregó la validación de servicio anterior cuando éste es urgencias ($pac['san'] == $ccoUrgencias)
 *								y el paciente está en proceso de traslado se permite hacer cargos desde otros centros de costo y el cargo se hace a
 *								el centro de costo de urgencia (ver cambio con fecha 2013-06-26)
 * @modified Junio 19 de 2013 	(Mario Cadavid). En el cambio hecho e Junio 6 de 2013, se cambio la consulta que se hace en la función
								consultarAplicacionAutoArticuloStock, ya no se consulta la tabla 000008 de movimiento hospitalario sino la 000091
 * @modified Junio 11 de 2013 	(Mario Cadavid). Se agrega la condición !$pac['ptr'] en la validacion de cargos hacia urgencias
								(ver cambio con fecha 2013-06-11) Esta condicion hace que si el paciente está en proceso de traslado y es de urgencias,
								se permite hacer cargos desde otros centros de costo
 * @modified Junio 6 de 2013 	(Mario Cadavid). Se agregaron las funciones consultarRegistroKardexPorArticulo, consultarRegistroStockArticulo y consultarAplicacionAutoArticuloStock, las cuales sirven para validar antes de registrar la grabación o anulación del cargo.
								Se validan las siguientes reglas: si el artículo está en kardex y es un artículo del stock y es un artículo especial que no tiene aplicación automática => se obliga a que la condición sea que no se aplica lo que se carga. Pero si es anulación y se cumplen estas mismas reglas, no se hace la anulación.
 * @modified Abril 15 de 2013 	(Edwin MG). Se modifica el programa para que al cargar un producto, se carguen adicionalmente y automáticamente otros articulos con las siguientes caracteristicas
 *											- Los articulos adicionales no van al carro
 *											- Todo artículo adicional queda también aplicado automáticamente
 *											- Todo artículo adicional mueve el saldo de dicho artículo para el saldo del paciente
 *											- Se tiene en cuenta este proceso en la devolución del medicamento
 *											Los medicamentos adicionales están configurados en la tabla movhos_000153
 * @modified Marzo 18 de 2013 		Si un paciente fue dado de alta desde urgencias, se permite cargar medicamentos hasta a lo mas x horas desde que fue dado de alta. El tiempo x
 *									es parametrizado desde root_000051 como tiempoEgresoUrgencia.
 * @modified Febrero 25 de 2013		Se hacen cambios varios para el proceso de contingencia, el cual consiste en que si no hay conexión con unix, los movimientos de los cargos son registrados
 *									en la tabla de paso (movhos_000141) y una vez se encuentre conexión con UNIX los registros son grabado en UNIX y en la tabla de detalle de movimiento(movhos_000003)
 * @modified Diciembre 11 de 2012	En la lista de articulos por dispensar, se muestra la unidad de manejo.
 * @modified Octubre 18 de 2012		Se quita la restricción del kardex al momento de grabar, la cual es que cantidad a dispensar menos la cantidad dispensada (kadcdi-kaddis) sea mayor a 0
 *									La restricción queda validada solo por la regleta.
 * @modified Octubre 02 de 2012		Al consultar las aplicaciones de un medicamento se valida que sea superior o igual a la fecha y hora de inicio del medicamento.
 * @modified Septiembre 25 de 2012	Cuando un medicamento es a necesidad, se valida que al cambiar la fecha y hora de inicio para que se pueda dispensar el medicamento sea mayor
 *									a la ronda de recibo por traslado del paciente.
 *									Al crear la regleta virtual, se tiene en cuenta la fracción del medicamento con respecto a las aplicaciones.
 * @modified Agosto 30 de 2012		Las llamadas a la funcion consultarCantidadAplicaciones, se aumentan el tiempo final de busqueda de cantidades aplicadas para buscar aplicaciones
 *									posteriores a la ronda actual, ya que lactario puede aplicar rondas posteriores a la actual.
 * @modified Agosto 27 de 2012		Se crea tiempo de dispensación por cco, no afecta a medicamentos de lactario.
 * @modified Agosto 22 de 2012		Al momento de cargar, se tiene en cuenta la cantidad de saldo en piso y la cantidad aplicada de las ultimas rondas para generar la regleta.
 * @modified Agosto 15 de 2012		Si al momento de cargar un medicamento, hay mas de uno en el kardex, se tratan los medicamentos como si fueran uno, teniendo en cuenta el saldo en piso
 *									y la cantidad aplicada por rondas.
 * @modified Junio 27 de 2012		Si un medicamento se devuelve y es de aplicacion automatica, cancela la aplicacion automatica y devuelve el medicamento
 * @modified Junio 3 de 2012		Dejaba grabar articulos suspendidos en el kardex, si tenia saldo de dispensacion ( cdi - dis )
 * @modified Mayo 17 de 2012		Solo se aplica automaticamente los medicamentos si el paciente se encuentra en un cco que es de aplicacion automatica quedando los saldos en la tabla 30 de movhos.
 *									Si un medicamento es especial y es de aplicacion automatica se carga y se descuenta de saldos del paciente (movhos_000004) y queda aplicado (movhos_000015)
 * @modified Mayo 14 de 2012		En el carro solo aparecen los medicamentos que son dispensados desde un cco de traslado (SF o CM) y reciban carro y no sean MMQ
 * @modified Mayo 9 de 2012			Se quita aplicacion automatica de MMQ.
 * @modified Mayo 9 de 2012			Cuando un medicamento es aplicado automaticamente queda con la ronda de aplicacion de la siguiente manera {Horamilitar:00} - {AM|PM}.
 * @modified Abril 26 de 2012		Se realizan cambio para el proceso de contingencia.
 * @modified Marzo 26 de 2012		Se muestra mensaje que dice que el kardex esta abirto
 *									Se corrige creación de regleta
 * @modified Marzo 12 de 2012		Se deja dispensar medicamentos con varias hora de anticipación según el tipo de medicamento
 * @modified Febrero 24 de 2012		Se corrige funcion actualizandoAplicacionFraccion al momento de actualizar la dosis en la aplicacion de un medicamento
 * @modified Noviembre 8 de 2011	Se graba la cantidad del medicamento segun la tabla de definicion de fraccion(movhos_000059) al aplicar un medicamento
 * @modified Noviembre 1 de 2011.	Cuando se carga un medicamento, se registra tambien la fecha de la ultima ronda grabada (campo kadfro de movhos 000054).
 * @modified Octubre 31 de 2011.	Cuando se carga un medicamento al paciente, la fecha de la ronda queda grabada.
 * @modified Septiembre 19 de 2011.	Cuando un medicamento aparece dos veces en el kardex, uno a necesidad y otro sin condicion o una condicion de suministro que no es a necesidad,
 *									dejaba grabar mas de lo que se muestra.  Se corrige el inconveniente.
 * @modified Septiembre 13 de 2011.	El grupo V00 ya no es material médico quirúrgico ya que no se encuentra en este grupo ningún médicamento que pueda ser considerado MMQ.
 *									Petición realizada por Beatriz Orrego el día 8 de septiembre
 *									Si un medicamento es a necesidad, siempre debe aparacer si no tiene saldo en piso, si hay saldo, no debe mostrarse.
 * @modified 2011-08-31 S un paciente viene de urgencias el mismo día, se recalcula la regleta del día para que pida las cantidades necesarias a partir de la hora de recibo en piso.
 * @modified 2011-08-30 Se corrige carga de medicamento a las 00:00:00. Los medicamentos no se veian ni se podian dispensar.
						Cuando un paciente es trasladado a piso se recalcula la regleta de acuerdo a la cantidad dispensada.
 * @modified 2011-08-27 Se agrega demas tablas temporales a la hora de crear el kardex automatico
 * @modified 2011-07-25 Se cambia la dispensacion para cada ciertas horas, segun el parametro DISPENSACION de la tabla root_000051.  Segun este tiempo, el programa pide para cada paciente
 * 						una cantidad x del medicamento que cubre las dosis a aplicar al paciente durante ese rango de tiempo.  Para este modificacion se cambia la funcion ArticulosXPaciente
 * 						y registrarArticulosKE.  Nota: Tener en cuenta que este programa es basado en el programa movhos/procesos/cargos.php
 * @modified 2011-06-20 Se puede dispensar cualquier medicamento aunque no sea visible en la lista
 * @modified 2010-11-23 Se suspende temporalmente la restricción de que una persona que aprueba el articulo no puede cargar linea 816, de la funcion preCondicionesKE, y se cambia nuevamente el query de registrarArticulosKE para que no valide si fue el que graba es el mismo que el que apreuba
 * @modified 2009-02-09 Se permite que un centro de costo hospitalario le grabe a otro, esto se valida en funcion buscar_si_puede_grabar_a_otro_cco, que se le envia el cco que graba y el cco donde esta el paciente y valida que se le pueda grabar, segun la tabla _000058.
 * @modified 2008-02-02 Color rosado para devoluciones
 * @modified 2007-11-14 Se evita grabacion de ceros y se avisa si la factura ya se tiro
 * @modified 2007-11-09 Se quita indicador de carro de dispensacion
 * @modified 2007-11-06 Indicador de carro de dispensacion
 * @modified 2007-09-27 Se hace la conexión odbc a facturación antes de llamar a ValidacionHistoriaUnix
 * @modified 2007-09-27 estaba ignorando la regla de aplicar cuando el centro de costos donde esta el paciente aplica por que no se llenaba el arreglo del centro de costos del paciente antes de llamar a getCco() de forma adecuada ($ccoPac=$pac['sac'];), se soluciona para que si lo haga bien ($ccoPac['cod']=$pac['sac'];).
 * @modified 2007-09-27 se cambian los terminos de verificación del paciente por fuera de unix, si infoPaciente() retorna false es por que el paciente no ha sido admitido, por lo cual no se debe dejar hacer cargos pues puede presentar conflictos con los centros de costos.
 * @modified 2007-09-26 Se hace necesario pedir los minutos de la ronda, así que se modifica el programa para pedir los minutos de la ronda, y construir $ronApl con los minutos incluidos.
 * @modified 2007-09-23 Desaparece la variable $art['apl'] por que no se necesita su uso.
 * @modified 2007-09-18 Desaparece el arrglo $trans por cambios del 2007-09-17 de la función registrarAplicaicon(), en donde ya recibe individualmente el número y la linea de la transacción y ya no hay descarte.
 * @modified 2007-09-18 Se camba $usu['codM'] por $usuario
 * @modified 2007-09-18 De ahora en adelante se van a realizar 2 movimientos simultaneos, se hace una entrada a los saldos normales a través de registrarSaldosNoApl(), y una aplicación que corresponde a un registro de aplicación por medio de registrarAplicación y una salidada de los saldos a través de registrarSladosNoApl().  Eso ocurre cuando $cco['asc']== true and $cco['apl']==false and $tipTrans == "C", es decir que el centro de costos aplique lo que carga a sus pacientes y no sea de aplicación automática, y sea un cargo No una devolución.
 * @modified 2007-09-17 Dada la creación de las nuevas funciones y tablas para saldo de paciente, una para el saldo normal (tabla:000004 funcion:registrarSaldoNoApl) y otra para el saldo de aplicación (tabla:000030, función:registrarSaldoAplicacion), see hacen cambios para que use las funciones adecuadamente.
 * @modified 2007-09-12 Se empiezan a usar las variable $fecApl y $ronApl dentro del programa pra enviarlas al registrar Aplicacion y para enviarlas por hidden.
 * @modified 2007-09-11 Cuando el centro de costos aplica automáticamente el material que se carga allá ($cco['asc']==true -Aplica Solo en el Centro de costos-), es necesario pedir la fecha y la hora de la ronda de  los artículos que se van a cargar, NO APLICA PARA DEVOLUCIONES!!!!!
 * @modified 2007-09-11 Se agrga la variable $cco['asc'] que dice si el centro de costos de donde se esta grabando aplica automáticamente los artículos que esta grabando, mas NO los artículos que cargan desde otros centros de costos a los pacientes que allí se encuentran.
 * @modified 2007-09-04 Cambio la función Artículo Cba en fxValidacionArticulo.php, por lo tanto se quita $cco de los parámetros de la función ArticuloCba.
 * @modified 2007-09-04 En la sección en donde $conex_o==0, es decir que no hay conexión con UNIx se modifica se borra la línea $pac['ing']=0 que existia en donde despues de llamar a la función infoPaciente, por que en la varsión de latas cuando no hay unix los registros deben quedar con el ingreso que trae esta función.
 * @modified 2007-09-04 Se empieza a usar la variable $art['apl'], la cual indica si el artículo aplica, se usa parasaber si es necesario llamar a la función registrarAplicacion, y ademas se crea el hidden que la envía a la siguiente pantalla cuando el artículo tiene cantida vriable.
 * @modified 2007-09-03 Se deja de usar la función pacienteDeAltapara tanto normal como por fuera de UNIX para saber si el paciente esta de alta, y se hacen las validaciones con $pac['alp'] para saber si el alta esta en proceso de alta, $pac['ald'] para saber si ya se le dio el alta definitiva, y $pac['alp'] para saber si esta en proceso de traslado.
 * @modified 2007-08-15 Secrea la variable $cco['apr'], que determina si el centro de costos tiene derecho a cargar aprovechamientos.
 * @modified 2007-08-03 Si se aplica automáticamente ya no depende del centro de costo del que se esta grabando si no adicionalmente del centro de costos en donde esta el paciente.
 * @modified 2007-07-04 Se pone el $pac['his']=ltrim(rtrim($pac['his'])) para que cuando se lee por código de barras que generalmente añade un espacio no haya problemas.
 * @modified 2007-06-19 Se modifica la parte del paciente para cuando no hay UNIX, se sube la parte den donde se llenan los datos del paciente para encima de llamar a la función de alta.
 * @modified 2007-06-18 Se comentan los hidden de cco['phm'] prefijo por matrix, y $cco['hcr'], pues no son necesarios si no cuando se va a validar la historia y eso se hace una sola vez.
 * @modified 2007-06-18 Se incluye un if en el hidden de $cco['apl'] pues se estaba enviando directamente el booleano y esto produce errores.
 * @modified 2007-06-18 Se incluye un if en el hidden de $cco['neg'] pues se estaba enviando directamente el booleano y esto produce errores.
 * @modified 2007-06-17 Se cambian los $cco['des'], por $cco['cod']
 * @modified 2007-06-15 Se cambian los parámetros de entrada de modo que sea posible leer el código de un centro de costos cuando elEl centro de costos a elegir permite selección.
 * @modified 2007-06-15 Se realiza el cambio para validar cuando NO hay UNIX que si el apciente esta en proceso de Alta solo pueda hacer cargos si el usuario seleccióno el checbox con nombre 'apl'
 * @modified 2007-06-15 Se realiza el cambio para validar cuando NO hay UNIX que si el centro de costos es hospitalario, este sea el mismo donde el paciente se encuantra actualmente según la tabla 000018
 * @modified 2007-06-14 Se modifica para que cuando pide la historia pregunte si el paciente esta en proceso de alta
 * @modified 2007-06-14 Se realiza el cambio para validar cuando hay UNIX que si el apciente esta en proceso de Alta solo pueda hacer cargos si el usuario seleccióno el checbox con nombre 'apl'
 * @modified 2007-06-14 Se realiza el cambio para validar cuando hay UNIX que si el centro de costos es hospitalario, este sea el mismo donde el paciente se encuantra actualmente según la tabla 000018
 * @modified 2010-01-04 No se puede cargar articulos si la cuenta del paciente ya fue facturada. Por Edwin Molina Grisales.
 * @modified 2010-01-04 Todos los articulos son cargados al carro, siempre y cuando el centro de costos donde se encuentra el paciente recibe carros. Por Edwin Molina Grisales.
 * @modified 2010-05-26 Al devolver un articulo, se busca si este esta en el carro (fdedis = on), si esta en el carro, se coloca en el campo fdecad la cantidad devuelta de dicho articulo. Si la cantidad devuleta
 * 						de dicho articulo es igual a la cantidad cargada, el campo fdedis se marca como off, en caso contrario se marca como on. Si la cantidad a devolver es mayor a la cantidad encontrada en el carro
 *                      para dicho registro se busca el siguiente registro con dicho articulo y se repite el proceso. Por Edwin Molina Grisales.
 * @modified 2010-09-24 Se modifica programa para que se cargue articulos por aprovechamiento solo para centros de costos de urg.
 *
 * @wvar String[10] $fecApl Fecha de aplicación si $cco['asc']==true.
 * @wvar String[8]	$ronApl ronda de aplicación si $cco['asc']==true.
 */

/************************************************************
 * FUNCIONES
 ************************************************************/
 
function marcarArticulosNoEnviar( $conex, $wbasedato, $wemp_pmla, $his, $ing, $fecha ){
	
	$val = false;
	
	$gruposNoVisibles = array();
	if( true ){
		$gruposNoVisibles = consultarAliasPorAplicacion( $conex, $wemp_pmla, "gruposNoVisiblesPerfil" );
		$gruposNoVisibles = explode( ',', $gruposNoVisibles );
	}
	
	$validarGruposVisiblesPerfil = '';
	foreach( $gruposNoVisibles as $key => $value ){
		if( $validarGruposVisiblesPerfil != '' )
			$validarGruposVisiblesPerfil .= ',';
		$validarGruposVisiblesPerfil .= ",'$value'";
	}
	
	$sql = "UPDATE ".$wbasedato."_000054, ".$wbasedato."_000026
			   SET kadess = 'on'
			 WHERE kadhis = '".$his."'
			   AND Kading = '".$ing."'
			   AND artcod = kadart
			   AND kadfec = '".$fecha."'
			   AND SUBSTRING_INDEX( artgru, '-', 1 ) IN ( '' $validarGruposVisiblesPerfil )
			   AND kadess = 'off'";

	$res = mysql_query( $sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	
	if( mysql_affected_rows() > 0 )
		$val = true;
	
	return $val;
}
 
function esCcoCiclos24Horas( $conex, $wbasedato, $wcco ){
	
	$val = false;
	
	$sql = "SELECT *
			FROM ".$wbasedato."_000011
		   WHERE ccocod = '".$wcco."'
		     AND Ccoc24 = 'on'
		   ";

	$res = mysql_query( $sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 )
		$val = true;
	
	return $val;
}
 
function registrarAuditoriaKardexPDA( $conex, $wbasedato, $historia, $ingreso, $descripcion, $fechaKardex, $mensaje, $idOriginal, $usuario ){

	$q = "INSERT INTO ".$wbasedato."_000055
				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaudes, Kaufec, Kaumen, Kauido, Seguridad)
			VALUES
				('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$descripcion."','".$fechaKardex."','".$mensaje."','".$idOriginal."','A-".$usuario."')";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}
 

/****************************************************************************************************************************************
 * Consulta el total de aplicaciones que ha tenido un medicamento
 ****************************************************************************************************************************************/
function consultarTotalAplicacionesEfectivas( $conex, $wbasedato, $his, $ing, $articulo, $fechorInicial, $fechorFinal, $ido = false, $fechorTraslado = 0 ){

	$val = 0;

	if( !$ido ){
		$sql = "SELECT
					Aplcan,Apldos,Aplufr,UNIX_TIMESTAMP( CONCAT( Aplfec,' ', SUBSTRING( Aplron, 1, 2 ) ) ) as Aplfec
				FROM
					{$wbasedato}_000015
				WHERE
					aplhis = '$his'
					AND apling = '$ing'
					AND aplart = '$articulo'
					AND aplfec >= '".date( "Y-m-d", $fechorInicial )."'
					AND aplido != ''
					AND aplest = 'on'
				";
	}
	else{
		$sql = "SELECT
					Aplcan,Apldos,Aplufr,UNIX_TIMESTAMP( CONCAT( Aplfec,' ', SUBSTRING( Aplron, 1, 2 ) ) ) as Aplfec
				FROM
					{$wbasedato}_000015
				WHERE
					aplhis = '$his'
					AND apling = '$ing'
					AND aplart = '$articulo'
					AND aplido = '$ido'
					AND aplest = 'on'
				";
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

			if( $rows['Aplfec'] >= 0 && $rows['Aplfec'] > $fechorTraslado && $rows['Aplfec'] <= $fechorFinal ){
				$val += 1;	//Suma de articulos en unidades enteras
			}
		}
	}

	return $val;
}

/************************************************************************************
 * Devuelve el valor nuevo para una dosis máxima para un medicamento
 *
 * $rowsart		es un array asociativo con los campos necesarios para
 *				realizar la operación. Estos son:
 *				Kadfin: Fecha de inicio
 *				Kadhin: Fecha de inicio
 *				Kadido: Id original
 *				Kaddma:	Dosis maxima
 *				Perequ:	Frecuencia del articulo
 * $horAnt		Indica con cuantas horas antes se puede aplicar un medicamento
 ***********************************************************************************/
function recalcularDosisMaxima( $conex, $wbasedato, $his, $ing, $art, $rowsart, $horAnt, $esANecesidad ){
	
	/******************************************************************************************************************************************************
	 * La dosis máxima se calcula desde la fecha y hora de inicio del medicamento
	 * Pero ya debe ser hasta que el medicamento termine todas sus aplicaciones, es decir no se sabe cuando termina el medicamento.
	 * Por tanto lo que se pretende es recalcular la dosis máxima a partir de la fecha y hora de inicio para darle un tiempo de terminacion
	 ******************************************************************************************************************************************************/
	
	$fechorInicial 	= strtotime( $rowsart[ 'Kadfin' ]." ".$rowsart[ 'Kadhin' ] );
	$frecuencia 	= $rowsart[ 'Perequ' ]*3600;
	$dosisMaxima 	= $rowsart[ 'Kaddma' ];
	$ido			= $rowsart[ 'Kadido' ];
	
	if( !$esANecesidad ){
		
		$fechorFinal 	= time()-$horAnt*3600;
		
		if( $fechorFinal>=$fechorInicial ){
			//La hora de dispensacion es mayor a la fecha y hora de inicio
			
			//Solo cuento las aplicaciones antes de que se pueda dispensar el medicamento, esto debido a que más adelante
			//Se busca entre las rondas que puede dispensar que se ha aplicado
			$aplicacionesEfectivas = consultarTotalAplicacionesEfectivas( $conex, $wbasedato, $his, $ing, $art, $fechorInicial, $fechorFinal, $ido, 0 );
		}
		else{
			//Si el medicamento comienza posterior se busca todas las dosis aplicadas sin tener en cuenta la ronda actual
			//Si comienza posterior a la ronda actual, cuanto todas las aplicaciones hasta minimo el tiempo actual o la fecha y hora de inicio
			$aplicacionesEfectivas = consultarTotalAplicacionesEfectivas( $conex, $wbasedato, $his, $ing, $art, $fechorInicial, min( $fechorInicial-1, time() ), $ido, 0 );
		}
	}
	else{
		$fechorFinal 	= time()-2*3600*0;
		//Consulta el total de aplicaciones efectivas realizadas
		$aplicacionesEfectivas = consultarTotalAplicacionesEfectivas( $conex, $wbasedato, $his, $ing, $art, $fechorInicial, $fechorFinal, $ido, 0 );
	}
	
	$dosisFaltantes = $dosisMaxima-$aplicacionesEfectivas;

	//Nunca las dosis faltantes pueden ser menores a 0
	if( $dosisFaltantes < 0 )
		$dosisFaltantes = 0;
	
	$aplicacionesTotales = 0;
	
	if( !$esANecesidad ){
		
		//Calculo cuantas aplicaciones totales hay
		$aplicacionesTotales = 0;
		
		if( $fechorFinal>=$fechorInicial )
			$aplicacionesTotales = floor( ( $fechorFinal-$fechorInicial )/$frecuencia )+1;
	}
	
	$aplicacionesTotales += $dosisFaltantes;
	
	return $aplicacionesTotales;
}
 
function pintarSeleccionArticulosReemplazo( $conex, $wbasedato, $cod, $res ){
	
	$num = mysql_num_rows($res);

	if( $num > 0 ){
		
		//Consulto en nombre del articulo que va a reemplazar
		$sql = "SELECT *
				  FROM ".$wbasedato."_000026
				 WHERE artcod = '".$cod."'";
		
		$resArt = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$rowsArt = mysql_fetch_array( $resArt );
	
		echo "<div>";
		echo "<table width=270 border=0 id='tbArtsReemp'>";
			
		for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
		
			if( $i == 0 ){
				echo "<tr class=titulo1>";
				echo "<td colspan='4'>".$cod."-".$rowsArt[ 'Artgen' ]."</td>";
				echo "</tr>";
				echo "<tr class=titulo1>";
				echo "<td colspan='4'>Seleccione articulo a reemplazar</td>";
				echo "</tr>";
				
				echo "<tr class=titulo2>";
				echo "<td>Articulo</td>";
				echo "<td>Inicio</td>";
				echo "<td>Cond.</td>";
				echo "<td>D. Max</td>";
				echo "</tr>";
			}
		
			echo "<tr class=colorAzul5 style='color:black;text-align:left;font-size:10pt' onClick='reemplazar( this,".$rows[ 'Kadido' ]." )'>";
			echo "<td>".$rows[ 'Kadart' ]."-".trim( $rows[ 'Artgen' ] )."</td>";
			echo "<td>".$rows[ 'Kadfin' ]." ".$rows['Kadhin']."</td>";
			echo "<td>".$rows[ 'Kadcnd' ]."</td>";
			echo "<td>".$rows[ 'Kaddma' ]."</td>";
			echo "</tr>";
		}
		
		echo "<tr style='color:black;text-align:left;font-size:10pt'>";
		echo "<td colspan='4'>";
		echo "<table width=270 border=0>";
		// echo "<tr class=titulo2 style='color:black;text-align:left;font-size:10pt;height:40'>";
		echo "<tr class=titulo2 style='color:black;text-align:left;font-size:10pt;'>";
		// echo "<td style='width:50%' align=center onClick='reemplazar( \"%\" )'>Todos</td>";
		// echo "<td align=center onClick='reemplazar( \"\" )'>Ninguno</td>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=4 align='center'>";
		echo "<INPUT type='button' onClick='aceptarReemp();' value='ACEPTAR'>";
		echo "<INPUT type='button' onClick='cancelarReemp()' value='CANCELAR'>";
		echo "</tr>";
		echo "</table>";
		
		echo "</tr>";
		
		echo "</table>";
		echo "</div>";
	}
	
	//Pinto todas las variables globales
	foreach( $_POST as $key => $value  ){
		if( strtolower( gettype( $value ) ) != "array" ){
			echo "<INPUT type='hidden' name='$key' value='$value'>";
		}
		else{
			foreach( $value as $k => $v  ){
				echo "<INPUT type='hidden' name='".$key."[".$k."]' value='".$v."'>";
			}
		}
	}
	
	echo "<INPUT type='hidden' name='idoReemplazo' id='idoReemplazo' value=''>";
	
	exit;
}

/************************************************************************************************************************
 * Consulta la fecha y ronda del día anterior
 ************************************************************************************************************************/
function consultarFechaHoraRondaKardexAnterior( $conex, $wbasedato, $his, $ing, $fecha, $art, $ido ){

	$val = false;

	$sql = "SELECT
				Kadfro, Kadron
			FROM
				".$wbasedato."_000054 a
			WHERE
				kadhis = '$his'
				AND kading = '$ing'
				AND kadfec = '$fecha'
				AND kadart = '$art'
				AND kadido = '$ido'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

	if( $rows =  mysql_fetch_array( $res ) ){
		if( $rows[ 'Kadfro' ] != '0000-00-00' && !empty( $rows['Kadron'] ) ){
			$val =  Array( $rows[ 'Kadfro' ], $rows['Kadron'] );
		}
	}

	return $val;
}





/********************************************************************************************************************************
 * Consulto el articulo equivalente a cargar si tiene
 ********************************************************************************************************************************/
function consultarArticuloEquivalente( $cco, $art ){

	global $conex;
	global $bd;

	$val = false;

	$sql = "SELECT
				Areaeq, Areceq, c.Artuni as Artuni
			FROM
				{$bd}_000008 a, {$bd}_000026 b, {$bd}_000026 c
			WHERE
				areces = b.artcod
				AND b.artest = 'on'
				AND arecco = '$cco'
				AND areces = '$art'
				AND areaeq != ''
				AND c.artcod = areaeq
				AND c.artest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){

		if( !empty( $rows['Areaeq'] ) ){
			$val = $rows;

			//Debo consultar la cantidad equivalente del articulo equivalente
			$val[ 'Arefra' ] = consultarFraccion( Array( "cod" => $rows['Areaeq'] ), Array( "cod" => $cco ) );
		}
	}

	return $val;
}

/********************************************************************************************************************************
 * Determina si una condición a necesidad para un medicamento se trata al
 * momento de dispensar como no a necesidad
 ********************************************************************************************************************************/
function tratarComoNoANecesidad( $condicion ){

	global $conex;
	global $bd;

	$val = false;

	$sql = "SELECT *
			FROM
				{$bd}_000042
			WHERE
				Concod = '$condicion'
				AND Contip = 'AN'
				AND Contmo = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = true;
	}

	return $val;
}

/********************************************************************************************************************************
 * Consulta si un medicamento a cargar tiene mas articulos asociados a dispensar automáticamente
 ********************************************************************************************************************************/
function consultarArticulosACargarAutomaticamente( $pro ){

	global $conex;
	global $bd;
	global $conex_o;

	$sql = "SELECT *
			FROM
				{$bd}_000153, {$bd}_000026
			WHERE
				Acppro = '$pro'
				AND Acpest = 'on'
				AND artcod = acpart
				AND artest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return $res;
}

function procesoContingencia(){

	global $conex;
	global $bd;
	global $conex_o;
	global $wcenpro;
	global $emp;

	if( $conex_o != 0 ){

		//Consulto todos los registro de la tabla de paso
		$sql = "SELECT
					Fencco, Fenhis, Fening, Fenfue, Fentip, Artuni, b.*
				FROM
					{$bd}_000002 a, {$bd}_000143 b, {$bd}_000026 c
				WHERE
					fennum = fdenum
					AND fenest = 'on'
					AND fdeest = 'on'
					AND fdeest = 'on'
					AND fdeart = artcod
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el quey $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		//echo "Total de registros a procesar: ".$num;

		if( $num > 0 ){
			
			$arFuentes = [];
			
			//Consulto todas las fuentes de cargos
			$sql = "SELECT Ccofca, Ccoaca
					  FROM {$bd}_000011
					 WHERE ccofac = 'on'
					   AND ccoest = 'on'
					";

			$resFuentes = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el quey $sql - ".mysql_error() );

			while( $rowsFuentes = mysql_fetch_array( $resFuentes ) ){
				
				if( !in_array( $rowsFuentes['Ccofca'], $arFuentes ) ){
					$arFuentes[] = $rowsFuentes['Ccofca'];
				}
				
				if( !in_array( $rowsFuentes['Ccoaca'], $arFuentes ) ){
					$arFuentes[] = $rowsFuentes['Ccoaca'];
				}
			}
			// $fecha = date( "Y-m-d" );

			while( $rows = mysql_fetch_array( $res ) ){
				
				// Tipo de transaccion: C para Cargo y D para Devolución
				// Fentip Siempre comienza con una C para Cargo o una D para devolución
				$tipTrans = 'C';
				if( !in_array( $rows[ 'Fenfue' ], $arFuentes ) )
					$tipTrans = 'D';

				$fecha = $rows[ 'Fecha_data' ];

				$dronum = $rows[ 'Fdenum' ];
				$drolin = $rows[ 'Fdelin' ];
				$fuente = $rows[ 'Fenfue' ];
				$pac[ 'his' ] = $rows[ 'Fenhis' ];
				$pac[ 'ing' ] = $rows[ 'Fening' ];
				$cco[ 'cod' ] = $rows[ 'Fencco' ];

				//Seteo el articulo
				$art[ 'uni' ] = $rows[ 'Artuni' ];
				$art[ 'cod' ] = $rows[ 'Fdeart' ];
				$art[ 'can' ] = $rows[ 'Fdecan' ];

				$art['fra'] = consultarFraccion( $art, $cco );


				//Consulto las empresas a las que se requiere el cambio de articulo equivalente
				$responsablesEq = consultarAliasPorAplicacion( $conex, $emp, "empresaConEquivalenciaMedEInsumos" );
				// $tipoEmpresaParticular = consultarAliasPorAplicacion( $conex, $emp, "tipoempresaparticular" );
				$resPaciente = consultarResponsable( $conex, $pac['his'], $pac['ing'] );
				$admiteEquivalencia = false;
				
				// if( $tipoEmpresaParticular != $resPaciente['tipoEmpresa'] ){
					$responsablesEq = explode( ",", $responsablesEq );
					$admiteEquivalencia = array_search( $resPaciente['responsable'], $responsablesEq ) === false ? false: true;
					$admiteEquivalencia = $admiteEquivalencia === false && array_search( '*', $responsablesEq ) !== false ? true: false;

					$artProducto = consultarInsumosProducto( $wcenpro, $bd, $art['cod'] );
				// }

				if( mysql_num_rows($artProducto) == 0 || !$admiteEquivalencia ){

					/****************************************************************************
					 * Noviembre 12 de 2013
					 ****************************************************************************/
					//Consulto código equivalente
					$artEq = consultarArticuloEquivalente( $cco[ 'cod' ], $art[ 'cod' ] );
					$auxArtEq = $art;
					if( !empty( $artEq ) && $admiteEquivalencia ){
						$art['uni'] = $artEq['Artuni'];
						$art['can'] = $artEq['Areceq']*$artEq['Arefra']*$art['can']/$art['fra'];		//Convierto la cantidad a cargar en la nueva para el medicamento equivalente
						$art['cod'] = $artEq['Areaeq'];													//Reemplazo el código del articulo por el código equivalente
						$art['fra'] = $artEq['Arefra'];
					}
					/****************************************************************************/

					$registra = registrarItdro( $dronum, $drolin, $fuente, $fecha, $cco, $pac, $art, $error );

					/************************************************************************************
					 * Febrero 27 de 2014
					 ************************************************************************************/
					if( !empty( $artEq ) && $registra && $admiteEquivalencia ){
						
						registrarLogArticuloEquivalente( $conex, $bd, $auxArtEq, $art, $dronum, $drolin, 'off' );
						
						//Se hace un ajuste de entrada para cada uno de los insumos iguale a la cantidad dispensado
						list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntrada" ) );
						if( $tipTrans != 'C' ){
							list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalidaDevolucion" ) );
						}
						ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
					}
					/************************************************************************************/

					$art = $auxArtEq;			//Noviembre 12 de 2013

					/************************************************************************************
					 * Febrero 27 de 2014
					 ************************************************************************************/
					if( !empty( $artEq ) && $registra && $admiteEquivalencia ){
						//Se hace un ajuste de Salida de inventario para el articulo que se va dispensar
						list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalida" ) );
						if( $tipTrans != 'C' ){
							list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntradaDevolucion" ) );
						}
						ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
					}
					/************************************************************************************/
				}
				else{

					$artsAjustar = array();

					for( $i = 0; $rowsIns = mysql_fetch_array($artProducto); $i++ ){

						$auxArt = $art;

						if( $i > 0 ){
							$drolin++;
						}

						$art['fra'] = 1;
						if( $rowsIns[ 'Unicm' ] != $rowsIns[ 'Unisf' ] ){
							$art['fra'] = consultarFraccion( $art, $cco );
						}

						$art['uni'] = $rowsIns[ 'Unisf' ];
						$art['cod'] = $rowsIns[ 'Artcod' ];
						$art['can'] = ceil($art['can']/$art['fra']*$rowsIns[ 'Pdecan' ]/$rowsIns[ 'Appcnv' ])*$art['fra'];

						$registra = registrarItdro( $dronum, $drolin, $fuente, $fecha, $cco, $pac, $art, $error );
						
						registrarLogArticuloEquivalente( $conex, $bd, $auxArt, $art, $dronum, $drolin, 'on' );

						$artsAjustar[] = $art;

						$art = $auxArt;
					}

					if( !empty( $artsAjustar ) && count( $artsAjustar ) > 0 ){

						//Se hace un ajuste de entrada para cada uno de los insumos igual a la cantidad dispensado
						list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntrada" ) );
						if( $tipTrans != 'C' ){
							list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalidaDevolucion" ) );
						}
						ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], $artsAjustar );
					}

					// $art = $auxArtEq;	//Noviembre 12 de 2013

					//Se hace un ajuste de entrada para cada uno de los insumos igual a la cantidad dispensado
					list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalida" ) );
					if( $tipTrans != 'C' ){
						list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntradaDevolucion" ) );
					}
					ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
				}



				if( $registra ){

					//Inserto todo el registro tal como esta en la 000143 a la tabla 000002 de movhos
					$sqlInsert = "INSERT INTO {$bd}_000003( Medico, Fecha_data, Hora_data, Fdenum, Fdelin, Fdeart, Fdeari, Fdecan, Fdeubi, Fdeinf, Fdeinh, Fdeinu, Fdelot, Fdeest, Fdeser, Fdedis, Fdecad, Seguridad )
									  SELECT
											Medico, Fecha_data, Hora_data, Fdenum, Fdelin, Fdeart, Fdeari, Fdecan, Fdeubi, Fdeinf, Fdeinh, Fdeinu, Fdelot, Fdeest, Fdeser, Fdedis, Fdecad, Seguridad
										FROM
											{$bd}_000143 b
										WHERE
											id = '{$rows[ 'id' ]}'
										";

					$resInsert = mysql_query( $sqlInsert, $conex ) or die( mysql_errno()." - Error en el quey $sqlInsert - ".mysql_error() );

					//Si es mayor a 0 significa que si se inserto el registro en movhos_000003
					if( mysql_affected_rows() > 0 ){

						//actualizo el estado de la tabla de paso para que no se tenga en cuenta para realizar el procesos posteriormente
						$sqlUpdate = "UPDATE
										  {$bd}_000143 b
									  SET
										  fdeest = 'off'
									  WHERE
										  id = '{$rows[ 'id' ]}'
									";

						$resUpdate = mysql_query( $sqlUpdate, $conex ) or die( mysql_errno()." - Error en el quey $sqlUpdate - ".mysql_error() );

						if( mysql_affected_rows() == 0 ){
							//echo "No se actualizo el registro en tabla de paso para el articulo {$art[ 'cod' ]} con doc $drolin - $dronum e id {$rows[ 'id' ]}<br>";
						}
					}
					else{
						// echo "No se inserto el registro en la tabla de movimientos para el articulo {$art[ 'cod' ]} con doc $drolin - $dronum e id {$rows[ 'id' ]}<br>";
					}
				}
				else{
					// echo "Hubo un error al registrar el articulo {$art[ 'cod' ]} con doc $drolin - $dronum en itdro<br>";
				}
			}
		}
		else{
			//echo "<center><b>SIN DATOS A PROCESAR</b></center>";
		}
	}
	else{
		//echo "<center>NO HAY CONEXION CON UNIX</center>";
	}
}

/****************************************************************************************************************
 * Consulta el tiempo de dispensación para un cco, si no se encuentra tiempo de dispensación,
 * esta función devuelve false
 *
 * Nota: El tiempo de dispensación es devuelta en segundos
 ****************************************************************************************************************/
function consultarHoraDispensacionPorCco( $conex, $wbasedato, $cco ){

	$val = false;

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000011
			WHERE
				ccocod = '$cco'
				AND ccoest = 'on'
				AND ccotdi != '00:00:00'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array($res) ){
		// $val = strtotime( date("Y-m-d {$rows['Ccotdi']}") ) - strtotime( date("Y-m-d 00:00:00" ) );
		// Septiembre 12 de 2013
		list( $hor ) = explode( ":", $rows['Ccotdi'] );
		$val = $hor*3600;
	}

	return $val;
}

/************************************************************************************
 * Fecha y hora final de terminacion del medicamento
 *
 * Consulta la fecha y hora final del medicamento
 * La fecha y hora final del medicamento son:
 * - La ultima aplicacion menor o igual a la hora de corte de dispensacion del día siguiente de la fecha del kardez
 * - Ultima aplicacion aplicacion por dosis máxima.
 * - Ultima aplicacion por dias de tratamiento.
 ************************************************************************************/
function consultarFechaHoraFinalMedicamento( $horaCorteDispensacion, $fechaKardex, $fini, $hini, $dosisMaxima, $diasTratamiento, $frecuencia ){

	//la frecuencia esta dada en horas, se pasa a segundos
	$frecuencia = $frecuencia*3600;

	//Fecha y hora de inicio del medicamento en formato Unix
	$fechorInicio = strtotime( "$fini $hini" );

	//Fecha y hora de corte para el medicamento
	$fechorFin = strtotime( $fechaKardex." $horaCorteDispensacion:00:00" ) + 24*3600;

	//Si tiene dosis maximas
	if( trim( $dosisMaxima ) != '' ){
		$fechorFin = min( $fechorFin, $fechorInicio+( trim( $dosisMaxima ) - 1 )*$frecuencia );
	}

	//Si tiene días de tratamiento
	if( trim( $diasTratamiento ) != '' ){
									  //									Calculo de fecha final del medicamento por dias de tratamiento
		$fechorFin = min( $fechorFin, $fechorInicio + floor( ( strtotime( $fini )+trim( $diasTratamiento )*24*3600 - $fechorInicio)/$frecuencia )*$frecuencia );
	}

	//la fecha final debe ser mayor o igual a la hora de inicio
	if( $fechorInicio <= $fechorFin )
	{
		//Esto calculo el total de aplicaciones que hay desde la fecha de inicio del medicamento
		//hasta la hora de finalizacion del medicamento
		$auxCan = floor( ( $fechorFin - $fechorInicio )/$frecuencia );

		//Por tanto, la fecha y hora final de terminacion del medicamento es
		//total de aplicaciones por frecuencia mas la fecha de inicio
		$fechorFin = $fechorInicio + $auxCan*$frecuencia;	//Hora de terminacion del medicamento
	}
	else{
		//Si entro aqui es por que hay una fecha y hora de terminacion del medicamento
		$fechorFin = false;
	}

	return $fechorFin;
}

/********************************************************************************
 * Combina una o varias regletas
 *
 * Solo puede combinar regletas que sean para la misma fecha de kardex
 *
 * $fecha					fecha para la cual fueron creados las regletas
 * $regletas				Array[][] con la información de las regletas a combinar
 *							[][ 0 ] Regleta
 *							[][ 1 ]		Fecha y hora final del medicamento por dias de tratamiento, false o 0 si no la tiene
 *
 * 		Nota: Cada regleta debe tener la fecha y hora de finalizacion del medicamento
 *			  por dosis máxima o días de tratamiento
 ********************************************************************************/
function combinarRegletas( $fecha, $regletas, $sinDis = false ){

	$datosRegleta = "";	//informacion final de la regleta

	if( count($regletas) > 1 ){

		foreach( $regletas as $keyRegleta => $valueRegleta ){

			//En la regleta esta los datos por fecha de kardex separada or un coma (,)
			$datos = explode( ",", $valueRegleta[0] );
			$fechorFin = $valueRegleta[1];

			$auxDia = array();	//Para controlar cuando pasa de día
			$fechaRegleta = strtotime( $fecha." 00:00:00" );

			foreach( $datos as $keyDatos => $valueDatos ){

				//La informacion por hora de kardex trae: la hora, cantidad a dispensar y cantidad dispensada separada por coma (,)
				// $hora	Hora aplicacion
				// $cdi		Cantidad a dispensar
				// $dis		Cantidad a dispensada
				list( $hora, $cdi, $dis ) = explode( "-", $valueDatos );

				if( $sinDis ){
					$dis = 0;
				}

				//Si la hora ya existe debo aumentar la fecha en un día
				if( isset( $auxDia[$hora] ) || $hora == "00:00:00" ){
					$fechaRegleta = strtotime( $fecha." 00:00:00" ) + 24*3600;
				}

				$auxDia[$hora] = 1;

				//convierto la fecha y hora en formato unix
				if( strtoupper( $hora ) != 'ANT' ){
					$fechorRegleta = $fechaRegleta + substr( $hora, 0, 2 )*3600;
				}
				else{
					$fechorRegleta = 'Ant';
				}

				if( $fechorFin == 0 || $fechorFin == false ){
					@$datosRegleta[ $fechorRegleta ][ 'cdi' ] += $cdi;
					@$datosRegleta[ $fechorRegleta ][ 'dis' ] += $dis;
				}
				else{
					//Si es posterior a la hora de terminacion del medicamento, cdi y dis es 0
					if( $fechorFin < $fechorRegleta ){
						@$datosRegleta[ $fechorRegleta ][ 'cdi' ] += 0;
						@$datosRegleta[ $fechorRegleta ][ 'dis' ] += 0;
					}
					else{
						@$datosRegleta[ $fechorRegleta ][ 'cdi' ] += $cdi;
						@$datosRegleta[ $fechorRegleta ][ 'dis' ] += $dis;
					}
				}
			}
		}
	}

	return $datosRegleta;
}

/****************************************************************************************************
 * Convierte la informacion de una regleta en un string
 *
 * $regleta			Contiene la informacion completa de la regleta
 * $sinDis			Si es diferente a falso la cantidad dispensada es 0
 ****************************************************************************************************/
function regletaToString( $regleta, $sinDis = false ){

	$val = '';

	if( count( $regleta ) > 0 ){

		foreach( $regleta as $keyRegletas => $valueRegleta ){

			if( trim( $keyRegletas ) != '' ){

				if( strtoupper( $keyRegletas ) != 'ANT' ){
					//La clave del array contiene la fecha y hora del articulo para el que fue creado
					//interesa la hora
					if( !$sinDis ){
						$val .= ",".date( "H:i:s", $keyRegletas )."-".$valueRegleta[ 'cdi' ]."-".$valueRegleta[ 'dis' ];
					}
					else{
						$val .= ",".date( "H:i:s", $keyRegletas )."-".$valueRegleta[ 'cdi' ]."-0";
					}
				}
				else{
					if( !$sinDis ){
						$val = ",Ant-".$valueRegleta[ 'cdi' ]."-".$valueRegleta[ 'dis' ].$val;
					}
					else{
						$val = ",Ant-".$valueRegleta[ 'cdi' ]."-0".$val;
					}
				}
			}
		}
	}

	//Esto elimina la coma(,) inicial
	$val = substr( $val,1 );

	return $val;
}

/************************************************************************************************
 * Crea la regleta para un medicamento sin cantidades a dispensar
 *
 * $fechorInicioDia			fecha y hora de incio del día en formato Unix
 * $fechorTerminacion 		fecha y hora de terminacion del medicamento en formato Unix
 * $fechorInicio			fecha y hora de inicio del medicmaento en formato Unix
 * $frecuencia				frecuencia en horas
 * $dosis					dosis del medicamneto
 * $fechorFinalArticulo		fecha y hora final en que termina el medicamento en formato Unix
 * $fechorTraslado			fecha y hora de traslado en formato Unix
 ************************************************************************************************/
function crearRegletaVirtual( $fechorInicioDia, $fechorTerminacion ,$fechorInicio, $frecuencia, $dosis, $fechorFinalArticulo, $fechorTraslado = 0 ){

	$valRegleta = array();

	$frecuencia = $frecuencia*3600;

	//Calculo la nueva dosis en fraccion
	$dosis = round( $dosis, 3 );

	for( $i = $fechorInicioDia; $i <= $fechorTerminacion; $i += 2*3600 ){

		$valRegleta[$i]['cdi'] = 0;
		$valRegleta[$i]['dis'] = 0;

		if( $fechorInicio <= $i && $fechorTraslado < $i ){	//Si el medicamento ya comienza

			if( ( $i - $fechorInicio )%$frecuencia == 0 && $i <= $fechorFinalArticulo ){	//Si pertenece a la ronda

				$valRegleta[$i]['cdi'] = $dosis;
				$valRegleta[$i]['dis'] = 0;
			}
		}
	}

	return $valRegleta;
}

/****************************************************************************************************
 * Convierte la informacion de una regleta en un string
 *
 * $regleta			Contiene la informacion completa de la regleta
 * $fechorInicial	Fecha Inicial del articulo
 * $sinDis			Si es diferente a falso la cantidad dispensada es 0
 ****************************************************************************************************/
function regletaVirtualToString( $regleta, $fechorInicial, $sinDis = false ){

	$val = '';

	if( !empty($regleta) && count( $regleta ) > 0 ){

		$ant = array();	//Indica si hay cantidad por dispensar antes de la ronda

		$ant[ 'cdi' ] = 0;
		$ant[ 'dis' ] = 0;

		foreach( $regleta as $keyRegletas => $valueRegleta ){

			if( trim( $keyRegletas ) != '' ){

				if( $keyRegletas > $fechorInicial ){
					//La clave del array contiene la fecha y hora del articulo para el que fue creado
					//interesa la hora
					if( !$sinDis ){
						$val .= ",".date( "H:i:s", $keyRegletas )."-".$valueRegleta[ 'cdi' ]."-".$valueRegleta[ 'dis' ];
					}
					else{
						$val .= ",".date( "H:i:s", $keyRegletas )."-".$valueRegleta[ 'cdi' ]."-0";
					}
				}
				else{
					if( !$sinDis ){	//Si se quiere crear la regleta sin la cantidad dispensada
						// $val = ",Ant-".$valueRegleta[ 'cdi' ]."-".$valueRegleta[ 'dis' ].$val;

						@$ant[ 'cdi' ] += $valueRegleta[ 'cdi' ];
						@$ant[ 'dis' ] += $valueRegleta[ 'dis' ];
					}
					else{
						// $val = ",Ant-".$valueRegleta[ 'cdi' ]."-0".$val;

						@$ant[ 'cdi' ] += $valueRegleta[ 'cdi' ];
						@$ant[ 'dis' ] = 0;
					}
				}
			}
		}

		if( $ant['cdi'] != $ant['dis'] ){
			$val = ",Ant-".$ant['cdi']."-".$ant['dis']."$val";
		}
	}

	//Esto elimina la coma(,) inicial
	$val = substr( $val,1 );

	return $val;
}

/********************************************************************************
 * Combina una o varias regletas
 *
 * Solo puede combinar regletas que sean para la misma fecha de kardex
 *
 * $regletas				Array[][] con la información de las regletas a combinar
 *							[][ 0 ] Regleta
 *							[][ 1 ]		Fecha y hora final del medicamento por dias de tratamiento, false o 0 si no la tiene
 *
 * 		Nota: Cada regleta debe tener la fecha y hora de finalizacion del medicamento
 *			  por dosis máxima o días de tratamiento
 ********************************************************************************/
function combinarRegletasVirtuales( $regletas, $sinDis = false ){

	$datosRegleta = [];	//informacion final de la regleta

	if( count($regletas) > 0 ){

		foreach( $regletas as $keyRegleta => $valueRegleta ){

			foreach( $valueRegleta[0] as $keyDatos => $valueDatos ){

				@$datosRegleta[ $keyDatos ][ 'cdi' ] += $valueDatos['cdi'];

				if( !$sinDis ){
					@$datosRegleta[ $keyDatos ][ 'dis' ] += $valueDatos['dis'];
				}
				else{
					@$datosRegleta[ $keyDatos ][ 'dis' ] = 0;
				}
			}
		}
	}

	return $datosRegleta;
}

/************************************************************************************
 * Carga una cantidad a varias regletas en conjunto y devuelve la fecha y hora en formato unix
 * que fue dispensada completamente
 ************************************************************************************/
function cargarCantidadRegletasVirtuales( &$regletas, $cantidad ){

	if( count($regletas) > 0 ){

		$fechorInicial = 0;
		$fechorFinal = 0;

		//Recorro las distintas regletas
		foreach( $regletas as $keyRegleta => $valueRegleta ){

			//Obtengo el primer y ultimo elemento del array para saber cuando comienza
			//una regleta
			if( $fechorInicial == 0 ){
				$fechorInicial = key( $valueRegleta[0] );
			}
			else{
				$fechorInicial = min( $fechorInicial, key( $valueRegleta[0] ) );
			}

			end( $valueRegleta[0] );
			$fechorFinal = max( $fechorFinal, key( $valueRegleta[0] ) );

			reset( $valueRegleta[0] );
		}

		//Recorro las regletas sumando el valor correspondiente
		for( $i = $fechorInicial; $i <= $fechorFinal; $i += 2*3600 ){

			foreach( $regletas as $keyRegleta => $valueRegleta ){

				//Obtengo la diferencia de la cantidad a dispensar y la dispensada
				$canFaltante = $valueRegleta[0][ $i ][ 'cdi' ] - $valueRegleta[0][ $i ][ 'dis' ];

				//Solo hago la operación si la cantidad faltante es mayor a 0
				if( $canFaltante > 0 ){

					if( $cantidad - $canFaltante >= 0  ){	//Solo si la cantidad que falta por cargar es mayor al faltante por hora
						$regletas[ $keyRegleta ][0][ $i ][ 'dis' ] = $regletas[ $keyRegleta ][0][ $i ][ 'cdi' ];
						$regletas[ $keyRegleta ][ 'fecUltDis' ] = $i;	//fecha y hora de la ultima dispensacion

						$cantidad -= $canFaltante;
					}
					else{	//Si es menor
						$regletas[ $keyRegleta ][0][ $i ][ 'dis' ] += $cantidad;

						$cantidad = 0;
					}
				}

				$cantidad = round( $cantidad, 3 );

				//Si la cantidad a cargar es igual a 0 termino la operacion
				if( $cantidad == 0 ){
					return;
				}
			}
		}
	}
}

/**************************************************************************************************
 * Calcula cuanto es la cantidad a dispensar hasta una hora segun la regletas
 **************************************************************************************************/
function cantidadADispensarRegletasVirtual( $regletas, $fechorFinal ){

	$val = 0;

	if( count( $regletas ) ){

		foreach( $regletas as $keyRegleta => $valueRegleta ){

			foreach( $valueRegleta[0] as $keyDate => $valueDate ){

				if( $keyDate <= $fechorFinal ){
					$val += $valueDate[ 'cdi' ];
				}
			}
		}
	}

	return $val;
}

/************************************************************************************
 * Actualiza los datos correspondientes para el medicamento en el kardex
 ************************************************************************************/
function actualizarRegleta( $conex, $wbasedato, $regleta, $fechorRonda, $id ){

	$val = false;

	if( !empty( $fechorRonda ) && $fechorRonda > 0 ){

		$sql = "UPDATE {$wbasedato}_000054
				SET
					kadcpx = '$regleta',
					kadron = '".date( "H", $fechorRonda )."',
					kadfro = '".date( "Y-m-d", $fechorRonda )."'
				WHERE
					id = '$id'
				";
	}
	else{

		$sql = "UPDATE {$wbasedato}_000054
				SET
					kadcpx = '$regleta',
					kadron = '',
					kadfro = '0000-00-00'
				WHERE
					id = '$id'
				";
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		$val = true;
	}

	return $val;
}

/****************************************************************************************************************
 * Recorre las distintas regletas virtuales y actualiza el registro correspondiente
 ****************************************************************************************************************/
function actualizarRegletas( $conex, $wbasedato, $regletas, $fecha ){

	foreach( $regletas as $keyRegleta => $valueRegleta ){

		$nuevaRegleta = regletaVirtualToString( $valueRegleta[0], $fecha );
		$id = $valueRegleta[ 'id' ];

		actualizarRegleta( $conex, $wbasedato, $nuevaRegleta, $valueRegleta[ 'fecUltDis' ], $id );
	}
}

/******************************************************************************************
 * Indica si pertenece a una ronda o no segun la regleta Virtual
 ******************************************************************************************/
function perteneceARondaPorRegletaVirtual( $regleta, $fechorMaxima ){

	$val = false;

	if( count($regleta) > 1 ){

		foreach( $regleta as $keyHora => $valueDatos ){

			if( $keyHora <= $fechorMaxima && $valueDatos['cdi'] > $valueDatos['dis'] ){
				$val = true;
			}
		}
	}

	return $val;
}


/******************************************************************************************
 * Busca si hay una aplicacion de un articulo desde un cco
 ******************************************************************************************/
function articulosAplicadosDesdeCco( $conex, $wbasedatos, $historia, $ingreso, $articulo, $cco ){

	$val = false;

	//Busco si hay alguna aplicacion desde el cco de costos que cargo
	$sql = "SELECT *
			FROM {$wbasedatos}_000015
			WHERE Aplhis = '$historia'
				AND Apling = '$ingreso'
				AND Aplart = '$articulo'
				AND Aplcco = '$cco'
				AND Aplest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		$val = true;
	}

	return $val;
}

/******************************************************************************************
 * Consulta la hora militar, sin minutos y segundos, de la ultima ronda dispensada total
 * o parcialmente
 ******************************************************************************************/
function consultarUltimaRondaDispensadaParcial( $vectorAplicaciones, $ronda = false ){

	$val = "";

	if( empty( $vectorAplicaciones ) ){
		return $val;
	}

	$rondas = explode( ",", $vectorAplicaciones );

	$hoy = true;			//Indica si la ronda ya paso pora la hora 00:00
	$diaUltimaRonda = true;	//Indica si la ultima ronda fue del dia actual o posterior

	/****************************************************************
	 * Septiembre 8 de 2011
	 ****************************************************************/
	if( $ronda ){

		//Consulto la ultima ronda posible de dispensar
		$timeRonda = strtotime( date( "Y-m-d" )." $ronda" );

		if( time() > $timeRonda ){
			$esPrimera = false;
		}
		else{
			$esPrimera = true;
		}

		if( $ronda == "00:00:00" ){
			$esPrimera = true;
		}

		for( $i = 0; $i < count($rondas); $i++ ){

			$valores = explode( "-", $rondas[$i] );

			/**************************************************************************
			 * Octubre 26 de 2011
			 * - Si encuentra la hora 00:00:00 significa que la ronda es del dia siguiente
			 **************************************************************************/
			if( $valores[0] == "00:00:00" ){
				$hoy = false;
			}
			/**************************************************************************/

			if( $valores[0] != "Ant" ){

				// if( $valores[1] > 0 ){
				// if( $valores[1] > 0 && $valores[1] == $valores[2] ){
				if( $valores[1] > 0 && $valores[2] > 0 ){
					$val = $valores[0];
					$diaUltimaRonda = $hoy;
				}
			}

			//Si la ronda leida es igual a la que viene por parametro
			//debo parar
			// if( $valores[0] == $ronda && $esPrimera ){
				// break;
			// }
			// elseif( $valores[0] == $ronda ){
				// $esPrimera = true;
			// }
		}

		list( $val ) = explode( ":", $val );
		$val .= "|".$diaUltimaRonda;
	}
	else{	//Si no se mando ronda significa que calcula la ultima que fue dispensada, total o parcialmente

		for( $i = 0; $i < count($rondas); $i++ ){

			$valores = explode( "-", $rondas[$i] );

			if( $valores[0] != "Ant" ){

				// if( $valores[1] > 0 && $valores[1] == $valores[2] ){
				if( $valores[2] > 0 ){	//Septiembre 8 de 2011
					$val = $valores[0];
				}
			}
		}

		list( $val ) = explode( ":", $val );
	}

	return $val;
}



/********************************************************************************
 * Coloca una marca pasada por parametro a un cargo de articulo
 ********************************************************************************/
function actualizandoCargo( $conex, $wbasedato, $num, $lin, $marca ){

	$val = false;

	if( trim( $marca ) != '' ){

		$sql = "UPDATE
					{$wbasedato}_000003
				SET
					fdedis = '$marca'
				WHERE
					fdenum = '$num'
					AND fdelin = '$lin'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		if( mysql_affected_rows() > 0 ){

			$sql = "INSERT INTO log_contingencia VALUES( $num, $lin )";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

			$val = true;
		}
	}

	return false;
}

/********************************************************************************
 * Consulta el valor de una aplicacion en root_000051
 ********************************************************************************/
function consultarValorPorAplicacion( $conex, $emp, $aplicacion ){

	$val = '';

	$sql = "SELECT
				*
			FROM
				root_000051
			WHERE
				detapl = '$aplicacion'
				AND detemp = '$emp'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 'Detval' ];
	}

	return $val;
}


function contingencia( $conex ){

	global $contingencia;
	global $fhContingencia;
	global $fhGrabacionContingencia;
	global $marcaContingencia;
	global $emp;

	$contingencia = consultarValorPorAplicacion( $conex, $emp, 'CONTINGENCIA' );

	if( $contingencia == 'on' ){

		$fechaContingencia = consultarValorPorAplicacion( $conex, $emp, 'fechaContingencia' );
		$horaContingencia = consultarValorPorAplicacion( $conex, $emp, 'horaContingencia' );
		$fechaGrabacionContingencia = consultarValorPorAplicacion( $conex, $emp, 'fechaGrabacionContingencia' );
		$horaGrabacionContingencia = consultarValorPorAplicacion( $conex, $emp, 'horaGrabacionContingencia' );

		//Convierto a fecha unix la hora de contingencia
		$fhContingencia = strtotime( "$fechaContingencia $horaContingencia" );

		//Convierto a fecha unix la hora de Grabacion contingencia
		$fhGrabacionContingencia = strtotime( "$fechaGrabacionContingencia $horaGrabacionContingencia" );

		//Si la fecha Actual es mayor a la fecha de contingencia y antes de la fecha y hora de contingencia
		//la marca que se carga en dispensacion es PC (PreContingencia)
		if( empty($marcaContingencia) ){
			if( time() > $fhGrabacionContingencia && $fhGrabacionContingencia < $fhContingencia ){
				$marcaContingencia = "PC";
			}
		}
	}
}


/************************************************************************************
 * Busca si un paciente tiene el kardex abierto
 ************************************************************************************/
function buscarKardexAbierto( $conex, $wbasedato, $historia, $ingreso, $fecha ){

	$val = '';

	$sql = "SELECT
				Karusu, Descripcion
			FROM
				{$wbasedato}_000053, usuarios
			WHERE
				karhis = '$historia'
				AND karing = '$ingreso'
				AND fecha_data = '$fecha'
				AND kargra = 'off'
				AND codigo = karusu
				AND activo = 'A'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 'Karusu' ]." - ".$rows[ 'Descripcion' ];
	}

	return $val;
}

/****************************************************************************************
 * graba el usuario que esta usando la PDA en el encabezado del kardex
 ****************************************************************************************/
function grabarUsuarioEncabezadoKardexPDA( $conex, $wbasedato, $fecha, $historia, $ingreso, $usuario ){

	return false;

	$val = false;

	$sql = "UPDATE
				{$wbasedato}_000053
			SET
				karusp = '$usuario'
			WHERE
				karhis = '$historia'
				AND karing = '$ingreso'
				AND fecha_data = '$fecha'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		$val = true;
	}

	return $val;
}

/******************************************************************************************
 * Esta función quita los medicamentos que ya no se pueden aplicar segun rondas anteriroes
 *
 * $conex 				Conexión a la base de datos
 * $wbasedato			Base de datos
 * $registro			Registro del kardex
 * $rondasAnteriores	total de rondas que se puede dispensar
 ******************************************************************************************
 * Proceso:
 *
 * - Mirar si la ronda anterior da para rondas correspondientes al mismo día
 * - Si la rondas a quitar, son de días anteriores mirar la regleta tiene ronda ficticia Ant
 *   y quitar las posibles cantidades que halla de saldo del día anterior
 * - Si las rondas a quitar son del mismo día entonces solo poner todo como dispensado,
 *   cuidando de que lo dispensado aparezca tambien como dispensado en el kardex
 ******************************************************************************************/
function quitarRondasADispensar(  $conex, $wbasedato, $registro, $rondasAnteriores, $fechaConsulta ){

	global $procesoContingencia;

	//Si se esta ejecutando el proceso de contingencia, no borro saldos
	if( !empty($procesoContingencia) && $procesoContingencia == 'on' ){
		return true;
	}

	$val = false;

	//Si no hya registro o la regleta esta vacia no se hace nada
	if( empty( $registro ) || empty( $registro[ 'Kadcpx' ] ) ){
		return $val;
	}

	//Calculo la ronda anterior a dispensar	 segun las rondas anteriores
	$ultimaRondaDispensada = date( "H", time() - $rondasAnteriores*2*3600 );

	$fechaUltimaRondaDispensada = date( "Y-m-d", time() - $rondasAnteriores*2*3600 );	//Fecha de ultima Ronda dispensada

	$ultimaRondaDispensada = gmdate( "H:i:s", intval( $ultimaRondaDispensada/2 )*2*3600 );	//todo tiene que estar dispensado hasta esta ronda

	$fechorUltimaRondaDispensada = strtotime( $fechaUltimaRondaDispensada." ".$ultimaRondaDispensada );

	//Consulto la ronda Actual
	$rondaActual = $fechorUltimaRondaDispensada + $rondasAnteriores*2*3600;

	//Consulto la fecha y hora actual a media noche del día actual, esto con el fin poder comparar con la ultima ronda a comparar
	$comienzoDia = strtotime( date( "Y-m-d 00:00:00" ) );

	//Si la ultima ronda a dispensar es menor igual entonces es por que puede tener Ant
	if( $fechorUltimaRondaDispensada <= $comienzoDia ){

		//Debo mirar hay cantidad del día anteriror en la regleta
		//solo se debe mirar si comienza la regleta con Ant
		if( strtoupper( substr( $registro['Kadcpx'], 0, 3 ) ) == "ANT" ){

			//Busco la información de la regleta
			$infoRegleta = explode( ",", $registro[ 'Kadcpx' ] );

			//Solo me interesa la primera posicion que es la correspondiente a los dias anteriores
			//Esto separa cada uno de los datos por posiciones
			//0: Ronda
			//1: Cantidad a dispensar
			//2: Cantidad dispensada
			$infoRegleta = explode( "-", $infoRegleta[0] );

			//Si la cantidad dispensada es igual a la cantidad a dispensar, significa que no hay saldo anterior
			if( $infoRegleta[1] > $infoRegleta[2] ){

				//Consulto la frecuencia en horas
				$frecuencia = $registro[ 'Perequ' ];

				if( true || $frecuencia > 0 ){
					//Debo mirar cuantas aplicaciones alcanza el medicamento segun la diferencia
					$aplicaciones = ceil( ( $infoRegleta[1] - $infoRegleta[2] )/( $registro[ 'Kadcfr' ]/$registro[ 'Kadcma' ] ) );

					//Consulto la ultima ronda de aplicacion antes de la ultima ronda de aplicacion
					$ultimoSuministro = suministroAntesFechaCorte( date( "Y-m-d", $comienzoDia ),
																   date( "H:i:s", $comienzoDia ),
																   trim( $registro['Kadfin'] ),
																   trim( $registro['Kadhin'] ),
																   $frecuencia );

					//Consulto la utlima ronda dispensada segun la regleta
					$ultimoSuministro -= ( $aplicaciones-1 )*$frecuencia*3600;

					//Si la ultima ronda dispensada es mayor o igual a ultimo suministro
					//quiere decir que si hay un saldo por quitar
					if( $fechorUltimaRondaDispensada >= $ultimoSuministro && $ultimoSuministro > 0 ){

						//Calculo cuantas aplicaciones hay hasta la ultima ronda que debe estar dispensado
						$apl = intval( ( $fechorUltimaRondaDispensada - $ultimoSuministro )/( $frecuencia*3600 ) )+1;

						//Calculo cuanto es la cantidad a dispensar adicional que se debe tener
						$addDispensar = $apl*( $registro[ 'Kadcfr' ]/$registro[ 'Kadcma' ] );

						$val = true;
					}
				}
			}
		}
	}
	else{	//Si la ultima ronda corresponde al día actual sin contar la ronda de las 00

		//Calculo cuanto se debe dispensar hasta la ultima ronda
		$canADispensar = cantidadTotalADispensarRonda( $registro[ 'Kadcpx' ], $ultimaRondaDispensada );

		//Calculo cuanto ha sido el total a dispensar hasta la ultima ronda que debe estar dispensada
		$canDispensada = cantidadTotalDispensadaRonda( $registro[ 'Kadcpx' ], $ultimaRondaDispensada ); //funcion del kardex

		//Si la cantidad a Dispensar hasta la ultima ronda es mayor que la cantidad dispensada
		//debo agregar la diferencia  a la regleta
		if( $canADispensar > $canDispensada ){

			$addDispensar = $canADispensar - $canDispensada;

			$val = true;
		}
	}


	if( $val ){	//Si entra es por que se puede actualizar el registro

		if( $addDispensar > 0 ){

			//Agrego la cantidad adicional a la regleta
			$cpx = crearAplicacionesCargadasPorHoras( $registro[ 'Kadcpx' ], $addDispensar );


			//Busco la ultima ronda que fue cargada
			$ultimaRonda = consultarUltimaRondaDispensada( $cpx, $rondaActual );

			//Miro si pertenece la ronda al día actual o siguiente
			list( $ultimaRonda, $fechaRonda ) = explode( "|", $ultimaRonda );

			if( $fechaRonda < 1 ){
				$fechaRonda = date( "Y-m-d", strtotime( $fechaConsulta ) + 3600*24 );
			}
			else{
				$fechaRonda = $fechaConsulta;
			}


			//Actualizo el registro correspondiente
			$sql = "UPDATE {$wbasedato}_000054
					SET
						kaddis = kaddis+FLOOR( $addDispensar ),
						kadcpx = '$cpx',
						kadron = '$ultimaRonda',
						kadfro = '$fechaRonda'
					WHERE
						id = '".$registro[ 'id' ]."'
					";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el queyr $sql -".mysql_error() );

			if( mysql_affected_rows( ) > 0 ){
				$val = true;
			}
			else{
				$val = false;
			}
		}
	}

	return $val;
}


function consultarRegistroKardexPorId( $conex, $wbd, $id ){

	$val = false;

	$sqlAnt = "SELECT
					*
				FROM
					{$wbd}_000054
				WHERE
					id = '$id'
				";

	$resAnt = mysql_query( $sqlAnt, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $resAnt ) ){
		$val = $rows;
	}

	return $val;
}



/**
 * Consulta si un artículo existe en kardex
 *
 * @param array $art
 * @param array $pac
 * @return unknown_type
 *
 * Nota:  Esta funcion es para determinar si un articulo actualmente pertenece
 * al kardex del paciente
 */
function consultarRegistroKardexPorArticulo( $art, $pac ){

	global $bd;
	global $conex;
	global $fecDispensacion;

	//Septiembre 23 de 2020
	//Se valida que la fecha no sea vacia
	$sql = "SELECT
				Kadart
			FROM
				{$bd}_000054
			WHERE
				Kadhis = '{$pac['his']}'
				AND Kading = '{$pac['ing']}'
				AND Kadart = '{$art['cod']}'
				AND Kadfec = '".( empty( $fecDispensacion ) ? '0000-00-00' : $fecDispensacion )."'
				AND Kadest = 'on'";

	$res = mysql_query( $sql, $conex );
	$num = mysql_num_rows( $res );

	if( $num > 0 )
		return true;
	else
		return false;

}


/**
 * Consulta si un artículo pertenece al stock del centro de costo
 *
 * @param array $art
 * @param array $cco
 * @return unknown_type
 *
 * Nota:  Esta funcion es para determinar si un artículo pertenece al stock
 * del centro de costo
 */
function consultarRegistroStockArticulo( $art, $cco ){

	global $bd;
	global $conex;
	global $fecDispensacion;

	$sql = "SELECT
				Arscod
			FROM
				{$bd}_000091
			WHERE
				Arscco = '{$cco['cod']}'
				AND Arscod = '{$art['cod']}'
				AND Arsest = 'on'";

	$res = mysql_query( $sql, $conex );
	$num = mysql_num_rows( $res );

	if( $num > 0 )
		return true;
	else
		return false;

}


/**
 * Consulta si un artículo esta en la tabla de articulos especiales y si tiene aplicación automática
 *
 * @param array $art
 * @param array $cco
 * @return unknown_type
 *
 * Nota:  Esta funcion es para determinar si un artículo esta en la tabla de stock por centro de costo
 * y si tiene aplicación automática
 */
function consultarAplicacionAutoArticuloStock( $art, $cco ){

	global $bd;
	global $conex;
	global $fecDispensacion;

	$aplicacionAuto = false;

	$sql = "SELECT
				Arsapl
			FROM
				{$bd}_000091
			WHERE
				Arscco = '{$cco['cod']}'
				AND Arscod = '{$art['cod']}'
				AND Arsest = 'on'";

	$res = mysql_query( $sql, $conex );
	//$num = mysql_num_rows( $res );

	if($rows = mysql_fetch_array( $res ))
	{
		if($rows['Arsapl']=='on')
			$aplicacionAuto = true;
	}
	// else
	// {
		// $aplicacionAuto = true;
	// }

	return $aplicacionAuto;

}


/**********************************************************************************
 * Consulta
 **********************************************************************************/
function consultarTipoProtocoloPorArticulo( $conex, $wbasedato, $cco, $articulo ){

	$sql = "SELECT
				Arktip
			 FROM
				{$wbasedato}_000068
			 WHERE
				arkcco = '$cco'
				AND arkcod = '$articulo'
				AND arkest = 'on'
			 ";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		if( $row = mysql_fetch_array( $res ) ){
			return $row[ 'Arktip' ];
		}
	}

	return false;
}

/********************************************************************************
 * Actualiza correctamente la fraccion segun la tabla de fraccciones por articulo
 * (movhos_000015) al aplicar un medicamento
 *
 * Noviembre 15 de 2011
 ********************************************************************************/
function actualizandoAplicacionFraccion( $his, $ing, $cco, $art, $num, $lin, $ccoFraccion ){

	global $conex;
	global $bd;

	$fraccionArticulo = consultarFraccionPorArticulo( $conex, $bd, $art['cod'], $ccoFraccion );

	if( !empty( $fraccionArticulo['unidad'] ) && $fraccionArticulo['fraccion'] > 0 ){

		if( strtolower( $art['uni'] ) != strtolower( $fraccionArticulo['unidad'] ) ){

			if( empty( $art['fra'] ) ){
				$art['fra'] = 1;
			}

			$sql = "UPDATE
						{$bd}_000015
					SET
						Aplufr = '{$fraccionArticulo['unidad']}',
						Apldos = '".( ($art['can']/$art['fra'])*$fraccionArticulo['fraccion'] )."'
					WHERE
						Aplhis = '{$his}'
						AND Apling = '{$ing}'
						AND Aplart = '{$art['cod']}'
						AND Aplcco = '{$cco['cod']}'
						AND Aplnum = '$num'
						AND Apllin = '$lin'
					"; //echo "......<pre>$sql</pre>";

			$resApl = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

			if( mysql_affected_rows() > 0 ){
				return true;
			}
			else{
				return false;
			}
		}
	}
	else{
		return false;
	}
}

/************************************************************************************************************
 * Consulta la unidad y fraccion de un articulo segun la tabla de fracciones movhos_000059
 ************************************************************************************************************/
function consultarFraccionPorArticulo( $conex, $wbasedato, $articulo, $cco ){

	$val = Array();

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000059
			WHERE
				defart = '$articulo'
				AND defcco = '$cco'
				AND defest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		$rows = mysql_fetch_array( $res );
		$val['unidad'] = $rows['Deffru'];
		$val['fraccion'] = $rows['Deffra'];
	}

	return $val;
}

 /************************************************************************
  * Consulta la primera ronda que no este totalmente cargada
  ************************************************************************/
 function consultarRondaNoCargada( $horasAplicar, &$cantidadADispensar ){

	$val = 0;

	if( empty( $horasAplicar ) ){
		return $val;
	}

	$exp = explode( ",", $horasAplicar );

	for( $i = 0; $i < count( $exp ); $i++ ){

		$valores = explode( "-", $exp[$i] );

		if( $valores[0] != "Ant" ){

			if( $valores[1] > $valores[2] ){

				$val = $exp[$i];
				$cantidadADispensar = $valores[1] - $valores[2];

				break;
			}
		}
	}

	return $val;
 }


/************************************************************************************
 * Segun la condicion de suministro del articulo, dice si dicha condición
 * es considerada a necesidad o no
 *
 * Septiembre 2011-09-11
 ************************************************************************************/
function esANecesidad( $wbasedato, $conex, $condicion ){

	$val = false;

	if( !empty( $condicion ) ){

		$sql = "SELECT
					Contip
				FROM
					{$wbasedato}_000042
				WHERE
					concod = '$condicion'
				";

		$resAN = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrowsAN = mysql_num_rows( $resAN );

		if( $numrowsAN ){

			$rowsAN = mysql_fetch_array( $resAN );

			if( $rowsAN[ 'Contip' ] == 'AN' ){
				$val = true;
			}
		}
	}

	return $val;
}

/**
 *
 */
function arreglarVector( $array ){

	$val = "";

	if( empty($array) ){
		return "";
	}

	foreach( $array as $key => $value ){
		$inicial = $key;
		break;
	}

	for( $i = 1; $i < $inicial; $i++ ){

		$array2[ $i ] = '-';
	}

	for( $i = $inicial; $i < count( $array )+$inicial; $i++ ){
		$array2[ $i ] = $array[ $i ];
	}

	return $array2;
}

/******************************************************************************************
 * Consulta la hora militar, sin minutos y segundos, de la ultima rondadispensada
 *
 * Modificaciones:
 *
 * Septiembre 8 de 2011.	Se considera dispensada una ronda si tiene al menos un articulo
 *							dispensado en esa ronda, anteriormente se consideraba dispensada
 *							si la cantidad a dispensar en una ronda era igual a la cantidad
 *							dispensada
 ******************************************************************************************/
function consultarUltimaRondaDispensada( $vectorAplicaciones, $ronda = false, $parcial = false ){

	$val = "";

	if( empty( $vectorAplicaciones ) ){
		return $val;
	}

	$rondas = explode( ",", $vectorAplicaciones );

	$hoy = true;			//Indica si la ronda ya paso pora la hora 00:00
	$diaUltimaRonda = true;	//Indica si la ultima ronda fue del dia actual o posterior

	/****************************************************************
	 * Septiembre 8 de 2011
	 ****************************************************************/
	if( $ronda ){

		//Consulto la ultima ronda posible de dispensar
		$timeRonda = strtotime( date( "Y-m-d" )." $ronda" );

		if( time() > $timeRonda ){
			$esPrimera = false;
		}
		else{
			$esPrimera = true;
		}

		if( $ronda == "00:00:00" ){
			$esPrimera = true;
		}

		for( $i = 0; $i < count($rondas); $i++ ){

			$valores = explode( "-", $rondas[$i] );

			/**************************************************************************
			 * Octubre 26 de 2011
			 * - Si encuentra la hora 00:00:00 significa que la ronda es del dia siguiente
			 **************************************************************************/
			if( $valores[0] == "00:00:00" ){
				$hoy = false;
			}
			/**************************************************************************/

			if( $valores[0] != "Ant" ){

				// if( $valores[1] > 0 ){
				if( $valores[1] > 0 && $valores[1] == $valores[2] ){
					$val = $valores[0];
					$diaUltimaRonda = $hoy;
				}
			}

			//Si la ronda leida es igual a la que viene por parametro
			//debo parar
			// if( $valores[0] == $ronda && $esPrimera ){
				// break;
			// }
			// elseif( $valores[0] == $ronda ){
				// $esPrimera = true;
			// }
		}

		list( $val ) = explode( ":", $val );
		$val .= "|".$diaUltimaRonda;
	}
	else{	//Si no se mando ronda significa que calcula la ultima que fue dispensada, total o parcialmente

		for( $i = 0; $i < count($rondas); $i++ ){

			$valores = explode( "-", $rondas[$i] );

			if( $valores[0] != "Ant" ){

				if( !$parcial ){
					
					if( $valores[1] > 0 && $valores[1] == $valores[2] ){
					// if( $valores[2] > 0 ){	//Septiembre 8 de 2011
						$val = $valores[0];
					}
				}
				else{
					if( $valores[1] > 0 && $valores[2] > 0 ){
						$val = $valores[0];
					}
				}
			}
		}

		list( $val ) = explode( ":", $val );
	}

	return $val;
}


/************************************************************
 * FUNCIONES PARA CREAR KARDEX ELECTRONICA AUTOMATICAMENTE
 ************************************************************/


/********************************************************************************
 * Consulta la hora de corte para crear el kardex automaticamente
 ********************************************************************************/
function consultarHoraCorteKardex( $conex ){

	global $wemp;

	$sql = "SELECT
				Detval
			FROM
				root_000051
			WHERE
				detapl = 'Hora corte kardex'
				AND detemp = '$wemp'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );

	if( $numrows > 0){

		if( $rows =  mysql_fetch_array( $res ) ){

			return $rows[0];
		}
		else{
			return false;
		}
	}
	else{
		$val = false;
	}

	return $val;
}

 /**************************************************************************************************************
  * Crea un kardex automaticamente si ha pasado la media noche, esto valido solo si el kardex no se ha generado en
  * el día y no esta abierto.  Esto solo es permitido hasta la HORA CORTE KARDEX (root_000051)
  *
  * Agosto 8 de 2011
  **************************************************************************************************************/
function crearKardexAutomaticamente( $conex, $wbd, $pac, $fecha ){

	global $wbasedato;
	global $usuario;
	global $emp;
	global $wemp_pmla;

	$wemp_pmla = $emp;

	$wbasedato = $wbd;

	//Obtengo la fehca del día anterior
	$ayer = date( "Y-m-d", strtotime( $fecha." 00:00:00" ) - 24*3600 );

	//Creo un array con los diferentes cco de costos que se deben crear
	$sql = "SELECT Karcco
			FROM
				{$wbd}_000053
			WHERE
				karhis = '{$pac['his']}'
				AND karing = '{$pac['ing']}'
				AND fecha_data = '$ayer'
			";

	$resCcos = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	$numCcos = mysql_num_rows( $resCcos );

	if( $numCcos > 0 ){	//Indica que hubo kardex el día anterior y por tanto puede hacerse el kardex automatico

		//Consulto si se ha generado kardex el día de hoy, es decir si tiene encabezado
		$sql = "SELECT *
				FROM
					{$wbd}_000053
				WHERE
					karhis = '{$pac['his']}'
					AND karing = '{$pac['ing']}'
					AND fecha_data = '$fecha'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );

		//Si no existe kardex
		if( $numrows < $numCcos ){	//Si la cantidad de kardex generados hoy es menor a los del día anterior quiere decir que faltan kardex por generar

			//verifico que no halla articulos en la temporal
			$sql = "SELECT *
					FROM
						{$wbd}_000060
					WHERE
						kadhis = '{$pac['his']}'
						AND kading = '{$pac['ing']}'
						AND kadfec = '$fecha'
					";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$numrows = mysql_num_rows( $res );

			if( $numrows == 0 ){	//No se ha generado kardex ni esta abierto

				$sql = "SELECT *
						FROM
							{$wbd}_000053
						WHERE
							karhis = '{$pac['his']}'
							AND karing = '{$pac['ing']}'
							AND fecha_data = '$ayer'
							AND kargra != 'on'
						";

				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$numrows = mysql_num_rows( $res );

				if( $numrows == 0 ){

					//Verifico que no se halla pasado la hora de corte kardex
					$corteKardex = true; //consultarHoraCorteKardex( $conex );

					if( $corteKardex ){

						//if( true || time() < strtotime( "$fecha $corteKardex" ) ){
						if( true ){

							//Si la hora actual es menor a la hora corte del kardex

							//Creo kardex nuevo para el día actual
							$auxUsuario = $usuario;
							$usuario = consultarUsuarioKardex($auxUsuario);
							$usuario->esUsuarioLactario = false;
							$usuario->gruposMedicamentos = false;

							/*********************************************************************************************************************
							 * Junio 12 de 2012
							 *********************************************************************************************************************/
							for( ;$rowsCcos = mysql_fetch_array($resCcos); ){

								//Busco si el cco es de lactario
								$sql = "SELECT
											ccolac
										FROM
											{$wbd}_000011
										WHERE
											ccocod = '".trim( $rowsCcos[ 'Karcco' ] )."'
											AND ccolac = 'on'
										";

								$resLac = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".msyql_error() );
								$numLac = mysql_num_rows( $resLac );

								$ccos[ trim( $rowsCcos[ 'Karcco' ] ) ] = false;

								if( $numLac > 0 ){
									$ccos[ trim( $rowsCcos[ 'Karcco' ] ) ] = true;
								}
							}
							/*********************************************************************************************************************/

							foreach( $ccos as $keyCcos => $valueCcos ){

								//Consulto si se ha generado kardex el día de hoy, es decir si tiene encabezado
								$sql = "SELECT *
										FROM
											{$wbd}_000053
										WHERE
											karhis = '{$pac['his']}'
											AND karing = '{$pac['ing']}'
											AND fecha_data = '$fecha'
											AND karcco = '$keyCcos'
										";

								$resKardexHoy = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql -".mysql_error() );
								$numKardexHoy = mysql_num_rows( $resKardexHoy );

								if( $numKardexHoy == 0 ){	//Esto se hace si no se ha generado kardex

									$usuario->centroCostosGrabacion = $keyCcos;
									$usuario->esUsuarioLactario = $valueCcos;	//El valor del array dice si el cco es de lactario o no

									$paciente = consultarInfoPacienteKardex( $pac['his'], '' );
									$kardexAc = consultarKardexPorFechaPaciente( $fecha, $paciente );
									
									
							
									//Dejo los articulos del día anterior en la tabla definitiva por si se quedaron en la temporal
									cargarArticulosADefinitivo( $pac['his'], $pac['ing'], $ayer, false, $keyCcos );
									cargarExamenesADefinitivo($pac['his'], $pac['ing'], $ayer);
									cargarInfusionesADefinitivo($pac['his'], $pac['ing'], $ayer);
									cargarMedicoADefinitivo($pac['his'], $pac['ing'],$ayer);
									cargarDietasADefinitivo($pac['his'], $pac['ing'],$ayer);
									
									//Cargos los datos del día anterior al actual
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "N", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "Q", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "A", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );
									cargarArticulosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha, "U", $kardexAc->descontarDispensaciones, $kardexAc->horaDescuentoDispensaciones );

									cargarArticulosADefinitivo( $pac['his'], $pac['ing'], $fecha, false, $keyCcos );

									/************************************************************************************************
									 * Agosto 27 de 2011
									 ************************************************************************************************/
									if( $keyCcos == '*' ){	//Solo lo hace enfermería
										cargarExamenesAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarInfusionesAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarMedicosAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );
										cargarDietasAnteriorATemporal( $pac['his'], $pac['ing'], $fecha, $fecha );


										cargarExamenesADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarInfusionesADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarMedicoADefinitivo( $pac['his'], $pac['ing'], $fecha );
										cargarDietasADefinitivo( $pac['his'], $pac['ing'], $fecha );
									}
									/************************************************************************************************/

									//Creo encabezado del kardex tal cual esta el día anterior
									$sql = "INSERT INTO
												{$wbd}_000053(Medico,Fecha_data,Hora_data,Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karpal,Kardie,Karmez,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,Karare,Karcco,Karusu,Karfir,Karmeg,Karsuc,Karaut,Karord,Seguridad)
											SELECT
															  Medico,'".$fecha."','".date( "H:i:s" )."',Karhis,Karing,Karobs,Karest,Kardia,Karrut,Kartal,Karpes,Karale,Karcui,Karter,Karcon,Karson,Karcur,Karint,Kardec,Karpal,Kardie,Karmez,Kardem,Karcip,Kartef,Karrec,Kargra,Karanp,Karais,karare,Karcco,Karusu,Karfir,Karmeg,Karsuc,'on',Karord,Seguridad
												FROM
													{$wbd}_000053
												WHERE
													Karhis = '{$pac['his']}'
													AND karing = '{$pac['ing']}'
													AND fecha_data = '$ayer'
													AND karcco = '$keyCcos'
											";

									$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

									if( mysql_affected_rows() > 0 ){

										//Dejo todos los registros del kardex como estaban antes
										$sql = "SELECT
													*
												FROM
													{$wbd}_000054
												WHERE
													kadhis = '{$pac['his']}'
													AND kading = '{$pac['ing']}'
													AND kadfec = '$fecha'
													AND kadcco = '$keyCcos'
												";

										$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
										$numrows = mysql_num_rows( $res );

										if( $numrows > 0 ){

											for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){

												$sqlAnt = "SELECT
																*
															FROM
																{$wbd}_000054
															WHERE
																id = '{$rows['Kadreg']}'
															";

												$resAnt = mysql_query( $sqlAnt, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

												if( $rowsAnt = mysql_fetch_array($resAnt) ){

													$sqlAct = "UPDATE
																	{$wbd}_000054
																SET
																	kadare = '{$rowsAnt['Kadare']}',
																	kadcon = '{$rowsAnt['Kadcon']}'
																WHERE
																	id = '{$rows['id']}'
																";

													$resAct = mysql_query( $sqlAct, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
												}
											}
										}
									}
								}
							}							
							
							$medicamentosControlAuto = consultarAliasPorAplicacion( $conex, $wemp_pmla, "MedicamentosControlAuto" );
				
							if( $medicamentosControlAuto == 'on' ){
								
								$opciones = array(
								  'http'=>array(
									'method'=>"GET",
									'header'=>"Accept-language: en\r\n",
									'content'=>"user=".$user,
								  )
								);
								$contexto = stream_context_create($opciones);
								$url = 'http://'.$_SERVER['HTTP_HOST'];
								@$varGet = file_get_contents( $url."/matrix/movhos/procesos/impresionMedicamentosControl.php?wemp_pmla=".$wemp_pmla."&historia=".$pac['his']."&ingreso=".$pac['ing']."&fechaKardex=".$fecha."&consultaAjax=10", false, $contexto );
							}

							$usuario = $auxUsuario;
						}
						else{
							return false;
						}
					}
					else{
						return false;
					}
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}
}

/************************************************************
 * FIN FUNCIONES PARA CREAR KARDEX ELECTRONICA AUTOMATICAMENTE
 ************************************************************/


/************************************************************************************************
 * crea un vector con las cantidades cargadas durante el dia segun la hora de aplicaciones
 * @return unknown_type
 *
 * Julio 14 de 2011
 ************************************************************************************************/
function crearAplicacionesDevueltasPorHoras( $vector, $cantidad ){

	if( $cantidad == 0 ){
		return $vector;
	}

	if( empty( $vector ) ){
		return "";
	}
	elseif( !empty( $vector ) ){
		//Obtengo las horas de aplicacion del medicamento para el paciente
		$exp = explode( ",", $vector );
	}

	$canDispensada = cantidadDispensadaRonda( $vector, "00:00:00" );	//se necesita el total del dia

	$cantidad = $cantidad -( ceil($canDispensada) - $canDispensada );

	//Busco la ultima hora dispensada
	$ultimoDispensado = -1;
	if( !empty($vector) ){

		for( $i = 0; $i < count( $exp ); $i++ ){
			$valores = explode( "-", $exp[$i] );

			if( $valores[2] > 0 ){
				$ultimoDispensado =  $i;
			}
		}
	}

	$nuevoAplicaciones = "";

	if( !empty($vector) ){

		$acumuloFraccionCargada = 0;

		for( $i =$ultimoDispensado; $i >= 0; $i-- ){

			if( $cantidad > 0 ){

				$valores = explode( "-", $exp[$i] );

				if( $valores[2] > 0 ){

					if( $valores[2] >= $cantidad ){
						$restar = $cantidad;
					}
					else{
						$restar = $valores[2];
					}

					$exp[$i] = $valores[0]."-".$valores[1]."-".( round ( $valores[2]-$restar, 1 ) );

					$cantidad -= $restar;

					$i++;
				}
			}
			else{
				break;
			}
		}

		for( $i = 0; $i < count($exp); $i++ ){

			if( $i == 0 ){
				$nuevoAplicaciones = $exp[$i];
			}
			else{
				$nuevoAplicaciones .= ",".$exp[$i];
			}
		}
	}

	return $nuevoAplicaciones;
}

/************************************************************************************************
 * crea un vector con las cantidades cargadas durante el dia seguna la hora de aplicaciones
 * @return unknown_type
 *
 * Julio 14 de 2011
 ************************************************************************************************/
function crearAplicacionesCargadasPorHoras( $vector, $cantidad ){

	if( $cantidad == 0 ){
		return $vector;
	}

	if( empty( $vector ) ){
		return "";
	}
	elseif( !empty( $vector ) ){
		//Obtengo las horas de aplicacion del medicamento para el paciente
		$exp = explode( ",", $vector );
	}

	$nuevoAplicaciones = "";

	if( !empty($vector) ){

		$acumuloFraccionCargada = 0;

		for( $acumuloFraccionCargada = 0, $j = 0; $acumuloFraccionCargada < $cantidad; $j++ ){

			if( $j < count($exp) ){

				/************************************
				 * Formato para valores
				 * [0]	Hora
				 * [1]	Cantidad a dispensar
				 * [2]	Cantidad dispensada
				 * @var unknown_type
				 ************************************/
				$valores = explode( "-", $exp[$j] );

				if( $valores[1] - $valores[2] > 0 ){

					if( $cantidad - $acumuloFraccionCargada >= $valores[1] - $valores[2] ){
						$fraccionCargada = $valores[1] - $valores[2];
					}
					else{
						$fraccionCargada = $cantidad - $acumuloFraccionCargada;
					}

					$acumuloFraccionCargada += $fraccionCargada;

					$exp[$j] = $valores[0]."-".$valores[1]."-".( round ( $valores[2]+$fraccionCargada, 3 ) );

					$j--;
				}
			}
			else{
				break;
			}
		}

		for( $i = 0; $i < count($exp); $i++ ){

			if( $i == 0 ){
				$nuevoAplicaciones = $exp[$i];
			}
			else{
				$nuevoAplicaciones .= ",".$exp[$i];
			}
		}
	}

	return $nuevoAplicaciones;
}

/*************************************************************************************************
 * Calcula la cantidad dispensada hasta una ronda dada
 *
 * @return unknown_type
 *
 * Modificaciones:
 * Agosto 19 de 2011.		Se verifica que ronda se debe mirar, la primera o la segunda hora
 *************************************************************************************************/
function cantidadDispensadaRonda( $horasAplicar, $ronda, $esPrimera = true ){

	$val = 0;

	if( empty( $horasAplicar ) ){
		return $val;
	}

	//Verifico si es la primera o segunda hora en la regleta
	//para esto se mira si ya paso la hora de la ronda
	//La ronda siempre es la que sigue, nunca se muestra rondas antes que la actual
	if( $esPrimera ){
		$timeRonda = strtotime( date( "Y-m-d" )." $ronda" );
		if( time() > $timeRonda ){
			$esPrimera = false;
		}
		else{
			$esPrimera = true;
		}
	}

	if( $ronda == "00:00:00" ){
		$esPrimera = true;
	}

	$exp = explode( ",", $horasAplicar );

	for( $i = 0; $i < count( $exp ); $i++ ){

		$valores = explode( "-", $exp[$i] );

		if( $ronda == $valores[0] && $esPrimera ){

			$val = $valores[2];
			break;
		}
		elseif( $ronda == $valores[0] ){
			$esPrimera = true;
		}
	}

	return $val;
}



/*************************************************************************************************
 * Calcula la cantidad dispensada hasta una ronda dada
 *
 * @return unknown_type
 *
 * Modificaciones:
 * Agosto 19 de 2011.		Se verifica que ronda se debe mirar, la primera o la segunda hora
 *************************************************************************************************/
function cantidadADispensarRonda( $horasAplicar, $ronda, $esPrimera = true ){

	$val = 0;

	if( empty( $horasAplicar ) ){
		return $val;
	}

	//Verifico si es la primera o segunda hora en la regleta
	//para esto se mira si ya paso la hora de la ronda
	//La ronda siempre es la que sigue, nunca se muestra rondas antes que la actual
	if( $esPrimera ){
		$timeRonda = strtotime( date( "Y-m-d" )." $ronda" );
		if( time() > $timeRonda ){
			$esPrimera = false;
		}
		else{
			$esPrimera = true;
		}
	}

	if( $ronda == "00:00:00" ){
		$esPrimera = true;
	}

	$exp = explode( ",", $horasAplicar );

	for( $i = 0; $i < count( $exp ); $i++ ){

		$valores = explode( "-", $exp[$i] );

		if( $ronda == $valores[0] && $esPrimera ){

			$val = $valores[1];
			break;
		}
		elseif( $ronda == $valores[0] ){
			$esPrimera = true;
		}
	}

	return $val;
}



/****************************************************************************************
 * Calcula la cantidad sin dispensar hasta una ronda dada
 *
 * @return unknown_type
 ****************************************************************************************/
function cantidadSinDispensarRondas( $horasAplicar, $ronda, $esPrimera = true ){

	$val = 0;

	if( empty( $horasAplicar ) ){
		return $val;
	}

	if( $esPrimera ){
		if( $ronda == "00:00:00" ){
			$timeRonda = strtotime( date( "Y-m-d" )." $ronda" )+24*3600;
		}
		else{
			$timeRonda = strtotime( date( "Y-m-d" )." $ronda" );
		}

		if( time() > $timeRonda ){
			$esPrimera = false;
		}
		else{
			$esPrimera = true;
		}
	}
	else{
		if( $ronda == '00:00:00' ){
			$esPrimera = true;
		}
	}

	$exp = explode( ",", $horasAplicar );

	for( $i = 0; $i < count( $exp ); $i++ ){

		$valores = explode( "-", $exp[$i] );

		if( $ronda == $valores[0] && $esPrimera ){

			break;
		}
		elseif( $ronda == $valores[0] ){
			$esPrimera = true;
		}

		$val += $valores[1]-$valores[2];
	}

	return $val;
}


/**
 * Indica si un artciulo peretenece a una ronda o no
 *
 * @param $fechaActual
 * @param $horaActual
 * @param $tiempoPreparacion
 * @param $fechaIncio
 * @param $horaInicio
 * @param $frecuencia
 * @param $horaSiguiente
 * @param $despuesHoraCortePx
 * @param $dosisMaximas
 * @param $diasTto
 * @return unknown_type
 */
function perteneceRonda( $fechaActual, $horaActual, $tiempoPreparacion, $fechaIncio, $horaInicio, $frecuencia, &$horaSiguiente, &$despuesHoraCortePx, $dosisMaximas, $diasTto ){

	$horaIncioActual = false;
	$despuesHoraCortePx = false;

	$porDosisDias = true;	//Indica si por Dosis Maximas o dias de tratamiento el medicamento pertenece a la ronda

	//Fecha inicio debe ser menor o igual que la fecha de actual
	if( $fechaIncio <= $fechaActual ){

		//convierto las fechas
		$fechorActual = strtotime( "$fechaActual $horaActual" );
		$fechorInicio = strtotime( "$fechaIncio $horaInicio" );
		$fechorfinal = $fechorActual+$tiempoPreparacion*3600;

		//Dosis maximas
		if(!empty($dosisMaximas) ){
			$fechorDosisMaximas = $fechorInicio+$frecuencia*($dosisMaximas-1)*3600;
		}

		//Dias de tratamiento
		if( !empty($diasTto) ){
			$fechorDiasTto = strtotime( "$fechaIncio 23:59:59" )*$diasTto*24*3600;	//Calculo cuando termina el medicamento por dias de tto
		}

		if( !empty($diasTto) ){
			if( $fechorInicio <= $fechorDiasTto ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}

		if( !empty($dosisMaximas) && $porDosisDias ){
			if( $fechorInicio <= $fechorDosisMaximas ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}

		$horaIncioActual = $fechorInicio;
		$horaSiguiente = $horaIncioActual;
		//Sumo la frecuencia hasta el día en que comience el medicamento
		for( $i = 0 ; $fechorInicio < $fechorActual && $porDosisDias; $i++ ){

			$fechorInicio += $frecuencia*3600;
			$horaIncioActual = $fechorInicio;

			$porDosisDias = true;

			if( !empty($diasTto) ){
				if( $horaIncioActual <= $fechorDiasTto ){
					$porDosisDias = true;
				}
				else{
					$porDosisDias = false;
				}
			}

			if(!empty($dosisMaximas) && $porDosisDias ){
				if( $horaIncioActual <= $fechorDosisMaximas ){
					$porDosisDias = true;
				}
				else{
					$porDosisDias = false;
				}
			}
		}

		if( $horaIncioActual >= $fechorActual && $horaIncioActual < $fechorfinal ){

			if( !empty($diasTto) ){
				if( $horaIncioActual > $fechorDiasTto ){
					return false;
				}
			}

			if( !empty($dosisMaximas) ){
				if( $horaIncioActual > $fechorDosisMaximas ){

					return false;
				}
			}

			if( $horaIncioActual >= $fechorActual ){

				$despuesHoraCortePx = true;
			}

			$horaSiguiente = $horaIncioActual;
			return true;
		}
		else{
			return false;
		}
	}

	return false;
}

/************************************************************************************************************************************
 * Calcula el total de aplicaciones de un medicamento, segun la ronda de produccion
 *
 * @param $inicioRonda
 * @param $tiempoProduccion
 * @param $inicioArticulo		Incio del articulo igual o superior a hora inicio de ronda
 * @return unknown_type
 ************************************************************************************************************************************/
function calcularCantidadAplicacionesRondaProduccion( $fechaActual, $inicioRonda, $tiempoProduccion, $inicioArticulo, $frecuencia, $dosisMaximas, $diasTto ){

	$porDosisDias = true;

	$iniRonda = strtotime( "$fechaActual $inicioRonda" );

	$iniArt = $inicioArticulo;

	$finRonda = $iniRonda+$tiempoProduccion*3600;


	//Dosis maximas
	if(!empty($dosisMaximas) ){
		$fechorDosisMaximas = $inicioArticulo+$frecuencia*($dosisMaximas-1)*3600;
	}

	//Dias de tratamiento
	if( !empty($diasTto) ){
		$fechorDiasTto = strtotime( date( "Y-m-d", $inicioArticulo )." 23:59:59" )*$diasTto*24*3600;	//Calculo cuando termina el medicamento por dias de tto
	}

	if( !empty($diasTto) ){
		if( $inicioArticulo <= $fechorDiasTto ){
			$porDosisDias = true;
		}
		else{
			$porDosisDias = false;
		}
	}

	if( !empty($dosisMaximas) && $porDosisDias ){
		if( $inicioArticulo <= $fechorDosisMaximas ){
			$porDosisDias = true;
		}
		else{
			$porDosisDias = false;
		}
	}

	$val = 0;

	for( $i = 0; $iniArt < $finRonda && $porDosisDias; $i++ ){
		$iniArt += $frecuencia*3600;
		$val++;

		if( !empty($diasTto) ){
			if( $iniArt <= $fechorDiasTto ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}

		if( !empty($dosisMaximas) && $porDosisDias ){
			if( $iniArt <= $fechorDosisMaximas ){
				$porDosisDias = true;
			}
			else{
				$porDosisDias = false;
			}
		}
	}

	return $val;
}

/************************************************************************************************
 * Trae la informacion correspondiente al tipo de articulo
 *
 * @param $conex
 * @param $wbasedato
 * @param $tipo
 * @return unknown_type
 ************************************************************************************************/
function consultarInfoTipoArticulos( $conex, $wbasedato ){

	global $tempRonda;

	$val = "";

	$tiposAriculos = Array();

	//Consultando los tipos de protocolo
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000099 a
			WHERE
				Tarest = 'on'
				AND tarhcp != '00:00:00'
				AND tarhcd != '00:00:00'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );

	if( $numrows > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['codigo'] = $rows[ 'Tarcod' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['nombre'] = $rows[ 'Tardes' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['tiempoPreparacion'] = $rows[ 'Tarpre' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCorteProduccion'] = $rows[ 'Tarhcp' ];
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCaroteDispensacion'] = $rows[ 'Tarhcd' ];

			$aux = consultarUltimaRonda( $conex, $wbasedato, $rows[ 'Tarcod' ], ( strtotime( "1970-01-01 ".$rows[ 'Tarhcp' ] ) - strtotime( "1970-01-01 00:00:00" ) )/3600 );

			$auxfec = "";
			@list( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'] ) = explode( " ", $aux );

			$tiposAriculos[ $rows[ 'Tarcod' ] ]['proximaRonda'] = proximaRondaProduccion( $auxfec, $tiposAriculos[ $rows[ 'Tarcod' ] ]['ronda'], $rows[ 'Tarpre' ] );

			$tiposAriculos[ $rows[ 'Tarcod' ] ]['tieneArticulos'] = 0;
			$tiposAriculos[ $rows[ 'Tarcod' ] ]['totalArticulosSinFiltro'] = 0;


			if( $rows[ 'Tarcod' ] == "N" ){
				//Agosto 29 de 2012
				$tempRonda = substr( $tiposAriculos[ $rows[ 'Tarcod' ] ]['horaCaroteDispensacion'],0,2 )*3600;
			}
		}
	}

	return $tiposAriculos;
}

/********************************************************************************************************************************************
 * Consulta la ultima ronda creada para un tipo de articulo
 *
 * @param $tipo
 * @return unknown_type
 ********************************************************************************************************************************************/
function consultarUltimaRonda( $conex, $wbasedato, $tipo, $timeAdd ){

	$val = date("Y-m-d 00:00:00");

	$fecha = date( "Y-m-d", strtotime( date("Y-m-d H:i:s") )-24*3600 );

	/************************************************************************
	 * Agosto 18 de 2011
	 ************************************************************************/
	$proximaHora = ( intval( date( "H", time()+$timeAdd*3600 )/2 )*2 ).":00:00";

	return $proximaHora;
	/************************************************************************/

	$proximaHora = ( ceil( date( "H" )/2 )*2 ).":00:00";

	//Consulto la ultima ronda que se hizo para un tipo de articulo
	//desde el día anterior, esto por que la siguiente ronda puede ser a la medianoche

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000106
			WHERE
				ecptar = '$tipo'
				AND ecpfec >= '$fecha'
				AND ecpest = 'on'
				AND ( ecpmod = 'P'
				OR (
					ecpmod = 'N'
					AND  UNIX_TIMESTAMP( NOW() )  > UNIX_TIMESTAMP(CONCAT( ecpfec,' ',ecpron ) )
				)
				OR (
					ecpmod = 'N'
					AND  ecpron = '$proximaHora'
				) )
			ORDER BY
				ecpfec desc, ecpron desc
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res) ){
		$val = $rows[ 'Ecpfec' ]." ".$rows[ 'Ecpron' ];
	}

	return $val;
}

/************************************************************************************************************************
 * Proxima ronda de produccion
 *
 * @return unknown_type
 ************************************************************************************************************************/
function proximaRondaProduccion( $fecha, $hora, $tiempo ){

	$val = strtotime( "$fecha $hora" )+3600*$tiempo;

	return $val;
}

/**********************************************************************************************
 * Si se hace una aplicacion se actualiza el campo Unidad de fraccion y cantidad de fraccion
 * segun el kardex en la tabla 15 de movhos
 *
 * @return unknown_type
 **********************************************************************************************/
function actualizandoAplicacion( $idKardex, $cco, $art, $num, $lin, $ronda ){
	// return;	//noviembre 8 de 2011
	
	//Solo se hace si es ayuda dx
	if( !$cco['ayu'] )
		return;
	
	global $conex;
	global $bd;

	$sql = "SELECT
				Kadhis, Kading, Kadcfr, Kadufr, Kadido, Kadfec
			FROM
				{$bd}_000054
			WHERE
				id = '$idKardex'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );

	if( $numrows > 0 ){

		$rows = mysql_fetch_array( $res );

		// $sql = "UPDATE
					// {$bd}_000015
				// SET
					// Aplufr = '{$rows['Kadufr']}',
					// Apldos = '{$rows['Kadcfr']}',
					// Aplido = '{$rows['Kadido']}'
				// WHERE
					// Aplhis = '{$rows['Kadhis']}'
					// AND Apling = '{$rows['Kading']}'
					// AND Aplart = '{$art['cod']}'
					// AND Aplcco = '{$cco['cod']}'
					// AND Aplnum = '$num'
					// AND Apllin = '$lin'
				// ";
				
		$sql = "UPDATE
					{$bd}_000015
				SET
					Aplido = '{$rows['Kadido']}',
					Aplfec = '{$rows['Kadfec']}',
					Aplron = '".(gmdate("H:00 - A",$ronda*3600 ))."'
				WHERE
					Aplhis = '{$rows['Kadhis']}'
					AND Apling = '{$rows['Kading']}'
					AND Aplart = '{$art['cod']}'
					AND Aplcco = '{$cco['cod']}'
					AND Aplnum = '$num'
					AND Apllin = '$lin'
				";

		$resApl = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		if( mysql_affected_rows() > 0 ){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}

/**
 * Devuelve el el codigo de la central del camillero y el nombre del camillero de acuerdo al codigo de usuario de matrix
 */
function buscarCodigoNombreCamillero(){

	global $conex;
	global $bd;

	global $bdCencam;

	$bdCencam = "cencam";

	$val = '';

	$sql = "SELECT
				codigo, nombre
			FROM
				{$bdCencam}_000002 a,{$bdCencam}_000006 b
			WHERE
				b.codcen = 'SERFAR'
				AND cenest = 'on'
				AND a.codced = cenope
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['codigo']." - ".$rows['nombre'];
	}

	return $val;
}



/************************************************************
 * Indica si el paciente tiene articulos aun para dispensar
 *
 * @param $lista
 * @return unknown_type
 ************************************************************/
function hayArticulosConSaldo( $lista ){

	for( $i = 0; $i < count($lista); $i++ ){
		if( @$lista[$i][1] != 0 ){
			return true;
		}
	}

	return false;
}

function nombreCco( $codigo ){

	global $conex;
	global $bd;

	$val = '';

	$sql = "SELECT
				Cconom
			FROM
				{$bd}_000011
			WHERE
				ccocod = '$codigo'
				AND ccoest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['Cconom'];
	}

	return $val;
}

function nombreCcoCentralCamilleros( $codigo ){

	global $conex;
	global $bd;

	$val = '';

	$sql = "SELECT
				Nombre
			FROM
				cencam_000004
			WHERE
				SUBSTRING_INDEX( cco, '-', 1 ) = '$codigo'
				AND Estado = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['Nombre'];
	}

	return $val;
}

/**
 * Crea una petición a la Central de camilleros
 *
 * @param $origen		Centro de costos de origen que pide el camillero
 * @param $motivo		Motivo de la petición
 * @param $hab			Habitación destino, debe aparecer la habitación y el nombre del paciente
 * @param $destino		Nombre cco destino
 * @param $solicita		Quien solicita el servicio
 * @param $cco			Nombre del sevicioq que solicita el servicio
 * @return unknown_type
 */
function crearPeticionCamillero( $origen, $motivo, $hab, $destino, $solicita, $cco, $camillero ){

	global $conex;
	global $bdCencam;

	$bdCencam = "cencam";

	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );

	$sql = "INSERT INTO
				{$bdCencam}_000003(    medico  , fecha_data, hora_data,   Origen ,  Motivo  , Habitacion, Observacion,   Destino ,  Solicito  , Ccosto, Camillero   , Hora_respuesta, Hora_llegada, Hora_cumplimiento, Anulada, Observ_central, Central, Usu_central,   Seguridad   )
							VALUES( '$bdCencam',  '$fecha' ,  '$hora' , '$origen', '$motivo',   '$hab'  ,     ''     , '$destino', '$solicita', '$cco', '$camillero',   '$hora'     ,  '00:00:00' ,     '00:00:00'   ,   'No' ,    ''         ,'SERFAR',   ''      , 'C-$bdCencam' )
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
}

function peticionCamillero( $cbCrearPeticion1, $ccoCam, $hab, $solicita, $origen, $destino, $paciente ){

	if( $cbCrearPeticion1 == 'off' ){

	}
	elseif( $cbCrearPeticion1 == 'on' ){

		$motivo = 'DESPACHO DE MEDICAMENTOS';

		$nomCcoDestino = nombreCcoCentralCamilleros( $destino );

		$val = crearPeticionCamillero( nombreCcoCentralCamilleros( $origen ), $motivo, "<b>Hab: ".$hab."</b><br>".$paciente, $nomCcoDestino, str_replace( "-", "", $solicita ), $nomCcoDestino, buscarCodigoNombreCamillero()  );
	}
}

/**
 * Busca si un artículo existe y esta activo dentro de la tabla de artículos de MATRIX
 * en Central de produccion.
 *
 *
 * @table 000002 de CENPRO SELECT
 *
 * @version 2007-07-17
 * @param Array	$art	Información del artículo.</br>
 * 						Información que debe estar en el arreglo antes de llamar la función:
 * 						[cod]:Código del artículo.</br>
 * 						Información que la función ingresa al arreglo
 * 						[nom]:Nombre generico del artículo.</br>
 * 						[uni]:Uidades del artículo.
 * 						[gru]:grupo al que pertenece el artículo.
 * @param Array	$error	Información del error</br>
 * 						[ok]:Descripción corta.</br>
 * 						[codInt]String[4]:Código del error interno, debe corresponder a alguno de la tabla 000010</br>
 * 						[codSis]:Error del sistema, si fue un error que se pued ecapturar, como los errores de Mysql.</br>
 * 						[descSis]:Descripción del error del sistema.
 * @return Boolean
 */

/**
 * Indica si el articulo fue aprobado por el regente o no
 * @param $art
 * @param $regente
 * @return unknown_type
 */
function articuloAprobadoPorRegente( $art, $codigoRegente, $pac ){

	global $conex;
	global $bd;
	global $fecDispensacion;
	global $usuario;

	$sql = "SELECT
				kaudes
			FROM
				{$bd}_000055
			WHERE
				fecha_data = '".$fecDispensacion."'
				AND kaudes like '%{$art['cod']}%'
				AND seguridad = 'A-$codigoRegente'
				AND kaumen = 'Articulo aprobado'
				AND kauhis = '{$pac['his']}'
				AND kauing = '{$pac['ing']}'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$numrows = mysql_num_rows( $res );

	if( $numrows > 0 ){

		$cadena = "";

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){

			$exp = explode( ",", $rows[ 'kaudes' ] );

			if( $i == 0 ){
				$ori = $exp[1];
			}

			$fechaKardex = $exp[2];
			$horaKardex = $exp[3];

			$cadena .= "AND kadfin = '$fechaKardex'
		       		 	AND kadhin != '$horaKardex'
		       		 ";
		}


		$sql="SELECT
					a.id
				FROM
					{$bd}_000054 a, {$bd}_000053 b
				WHERE
					kadart = '{$art['cod']}'
					AND kadcdi > kaddis+0
					AND kadfec = '$fecDispensacion'
					AND kadhis = '{$pac['his']}'
		       		AND kading = '{$pac['ing']}'
		       		AND kadori = '$ori'
		       		{$cadena}
		       		AND kadsus != 'on'
		       		AND karhis = kadhis
		       		AND karing = kading
		       		AND b.fecha_data = kadfec
		       		AND karcco = kadcco
		       		AND karcon = 'on'
		       		AND karest = 'on'
		       		AND kadare = 'on'
				GROUP BY kadart";

		$res2 = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );

		if( $rows2 = mysql_fetch_array( $res2 ) ){
			return false;
		}
		else{
			return true;
		}
	}
	else{
		return false;
	}
}

/**
 * Coloca la bandera del carro en On si se puede agregar al carro
 *
 * @param $art
 * @param $cco
 * @return unknown_type
 *
 * Modificaciones:
 * Mayo 14 de 2012. Se agrega un medicamento al carro, si el cco desde donde se dispensa en de traslado
 */
function agregarAlCarro( &$art, $cco, $accion, $ccodis ){

	global $conex;
	global $bd;

	if( !$ccodis['ayu'] && $accion == 'C' && $ccodis['tras'] && $ccodis['cod'] != $cco && !esMMQ( $art['cod'] ) ){
		//Se agrega al carro, si el centro de costo tiene el campo ccorec en on y la fecha actual
		//y hora actual es mayor o meno a fecha_data y hora_data de movhos 000011
		$sql = "SELECT
					*
				FROM
					{$bd}_000011
				WHERE
					ccorec = 'on'
					AND DATE_FORMAT(NOW(),'%Y-%m-%d %T') >= CONCAT(Fecha_data,' ',Hora_data)
					AND ccocod = '{$cco}'
					AND ccoest = 'on'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );

		if( $numrows > 0 ){
			$art['dis'] = 'on';
		}
		else{
			$art['dis'] = 'off';
		}
	}
}

/**
 * Indica si el articulo es no pos
 *
 * @param $cod
 * @return unknown_type
 */
function esNoPos( $cod ){

	global $conex;
	global $bd;

	$val = false;

	if( $cod != '' ){

		$sql = "SELECT
					artpos
				FROM
					{$bd}_000026
				WHERE
					artcod = '$cod'
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		if( $rows = mysql_fetch_array( $res ) ){
			if( $rows[0] == 'N' ){
				$val = true;
			}
			else{
				$val = false;
			}
		}
		else{
			$val = false;
		}
	}
	else{
		$val = false;
	}

	return $val;
}

/**
 * Graba la hora de dispensacion para un articulo que se carga despues de
 * facturar y el paciente se encuentra en alta en proceso
 *
 * @param $his
 * @param $ing
 * @param $id
 * @return unknown_type
 */
function grabarDespuesDeFacturar( $his, $ing, $id ){

	global $conex;
	global $bd;

	$sql = "UPDATE
				{$bd}_000022
			SET
				cuegdf = 'on',
				cuehgf = '".date("H:i:s")."'
			WHERE
				id = '$id'
				AND cuehis = '$his'
				AND cueing = '$ing'";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
}

function generandoInventarios(){

	$gen = false;

	$horaActual = date("H:i:s");
	$fechaActual = date("Y-m-d");

	if( $fechaActual == date("Y-m-t") && ( $horaActual >= "23:55:00" && $horaActual <= "23:59:59" ) ){
		$gen = true;
	}
	elseif( $fechaActual == date("Y-m-01") && ( $horaActual >= "00:00:00" && $horaActual <= "00:05:00" ) ){
		$gen = true;
	}

	return $gen;
}

/**
 * Determina si el paciente con KE tiene  las condiciones necesarias para
 * un articulo en el KE
 *
 * @param array $pac			Informacion del paciente
 * @param array $art			Informacion del articulo
 * @param bool $ke				Indica si tiene(true) KE o no (false)
 * @param array $artValido		Indica si el articulo es valido
 * @param string $tipTrans		Tipo de transaccion (C=Cargo, D=Devolucion)
 * @param array $cco			Informacion del Centro de Costos
 * @return $bool
 */

function preCondicionesKE( $pac, &$art, $ke, $artValido, $tipTrans, $cco, &$nka ){

	global $usuario;
	//En caso de ser Kardex Electronico (ke) intentará grabar en tabla 54
	//en caso contrario no hara la grabación
	$regbool = false;

	//Precondiciones para grabar en el KE
	//- Tener KE
	//- Que el articulo exista
	//- Que se encuentre en la lista de pacientes
	//- Si es Material medico quirgico debe dejar grabar
	//- Si el articulo no necesariamente tiene que estar
	//  en el KE dejar grabar

	//Si el articulo es Material Medico Quirurgico deja grabar
	//aunque tenga KE
	$val = false;								//Indica si cumple las conidiciones necesarias para gragbar
	$listartpac = ArticulosXPaciente( $pac );	//Lista de articulos por paciente del KE
	$poslist = enLista( $listartpac, $art );	//Lista de articulos por paciente del KE
	$nka = false;

	if( $ke && $tipTrans == "D" ){

		$val = true;
	}
	else{

		//Si no es de trasaldo deja grabar
		if( !$cco['tras'] ){
			$val = true;
		}

		//Se verifica que el articulo no sea nka
		//NKA = No necesario en el KE
		if( !$val && $poslist == -1 ){
			$nka = esNKA( $art, $cco );
			if( $nka ){
				$val = true;
			}
		}

		//Verificando si el articulo es MMQ
		//MMQ = Material Médico Quirurgico
		if( $artValido && !empty($art['cod']) && !$val ){
			if( esMMQ( $art['cod'] ) ){
				$val = true;
				if( $poslist == -1 ){	//Si es MMQ y no está en lista se aplicaautomaticamente
					$art[ 'preApl' ] = true;
				}
			}
		}

		//No se puede cargar un articulo por la misma persona que aprueba el articulo
		//Se suspende temporalmente 2010-11-23
		if( false && articuloAprobadoPorRegente( $art, $usuario, $pac ) ){
			echo "<script>alert(\"No puede cargar un articulo que\\nusted mismo halla aprobado\");</script>";
			return false;
		}

		//Si el articulo no el centro de costos es de traslado,
		//no es NKA o MMQ, verificar que este en la lista
		//el articulo sea valido, que tenga KE, que sea visible en la
		//lista de dispensacion y la cantidad a grabar no sobrepase
		//la cantidad faltante por dispensar
		if( !$val ){
			//Se quita esta validacion temporalemnte, si esta en la lista aunque no esta visible se graba
//			if( $ke && $artValido && $tipTrans == "C" && $poslist < 3
//			&& $poslist > -1 && !$art['cva'] && $listartpac[$poslist][1] >= $art['can']/$art['fra'] )
//			{
//				$regbool = true;
//			}

			//2011-06-20
			if( $ke && $artValido && $tipTrans == "C" && $poslist > -1
				&& !$art['cva'] && !$listartpac[$poslist][4] && $listartpac[$poslist][1] >= $art['can']/$art['fra'] )
			{
				$regbool = true;
				
				//Si es POS, solo se puede grabar si tiene articulos aprobados por CTC
				if( $listartpac[$poslist][5] == 'N' && $listartpac[$poslist][10] && $listartpac[$poslist][10] >= 100 ){
					$regbool = false;
				}
			}

			if( $ke && $regbool ){
				$val = true;
			}
			else if( $ke && $tipTrans == "C" ){
				$val = false;
			}
			else{
				$val = true;
			}
		}
	}

	return $val;
}

/**
 * Consulta la fraccion de un articulo
 *
 * @param array $art
 * @param array $cco
 * @return unknown_type
 *
 * Nota:  Esta funcion es para determinara cual es la cantidad a cargar
 * para un articulo
 */
function consultarFraccion( $art, $cco ){

	global $bd;
	global $conex;

	$frac = 1;

	$sql = "SELECT
				a.*, Ccourg
			FROM
				{$bd}_000008 a, {$bd}_000011 b
			WHERE
				areces = '{$art['cod']}'
				AND arecco = '{$cco['cod']}'
				AND ccocod = arecco
				";

	$res = mysql_query( $sql, $conex );

	if( $rows = mysql_fetch_array( $res ) ){

		if( $rows['Ccourg'] == 'on' ){
			if( $rows['Arecva'] == 'on' ){
				$frac = $rows['Arecma'];
			}
			else{
				$frac = $rows['Arecde'];
			}
		}
		else{
			$frac = $rows['Arecde'];
		}
	}

	return $frac;
}

/**
 * Retorna verdarero (true) en caso de que el articulo no tiene que estar en el KE, en caso
 * contraio, la funcion retornara falso (false)
 *
 * @param $art
 * @param $cco
 * @return unknown_type
 */
function esNKA( $art, $cco ){

	global $bd;
	global $conex;
	global $procesoContingencia;

	$nka = false;

	//Busco si el articulo es nka
	$sql = "SELECT
				*
			FROM
				{$bd}_000059
			WHERE
				defcco = '{$cco['cod']}'
				AND defart = '{$art['cod']}'
				AND defnka = 'on'";

	$res = mysql_query( $sql, $conex );

	if( $rows = mysql_fetch_array( $res ) ){
		$nka = true;
	}

	return $nka;
}

/**
 * Indica si el centro de costos es de traslado o no
 * @param $cco				Informaion del centro de costos
 * @return unknown_type
 */
function esTraslado( $cco ){
	global $bd;
	global $conex;

	$val = false;

	$q = "SELECT
			Ccocod
		FROM
			".$bd."_000011
		WHERE
			Ccotra = 'on' AND
			Ccocod = '$cco' ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$val = true;
	}

	return $val;
}

/**
 * Dice si un articulo es Material Medico Quirurgico
 *
 * @param $art
 * @return unknown_type
 *
 * Nota: SE considera material medico quirurgico si el grupo del
 * articulo no se encuentra en la taba 66 o pertenezca al grupo E00 o V00
 *
 * Modificacion:
 * Septiembre 8 de 2011.	Ya no se considera MMQ los articulos del grupo V00
 */
function esMMQ( $art ){

	global $conex;
	global $bd;

	$esmmq = false;

	$sql = "SELECT
				artcom, artgen, artgru, melgru, meltip
			FROM
				{$bd}_000026 LEFT OUTER JOIN {$bd}_000066
				ON melgru = SUBSTRING_INDEX( artgru, '-', 1 )
			WHERE
				artcod = '$art'
			";

	$res = mysql_query( $sql, $conex );

	if( $rows = mysql_fetch_array( $res ) ){
		if( (empty( $rows['melgru'] ) || $rows['melgru'] == 'E00' ) && !empty($rows['artcom']) ){
			$esmmq = true;
		}
		else{
			$esmmq = false;
		}
	}

	return $esmmq;
}

/****************************************************************************************************************
 * Imprime en pantalla una tabla con los elementos de un array de dos dimensiones
 *
 * @param array[][] $datos		Array de dos dimensiones con datos
 * @param string $class			Estilo para las filas de la tabla
 * @param opcional $filas		Total de filas que se mostraran en la tabla
 * @param opcional $columnas	Total de columnas que se mostraran en pantalla
 *
 * Modificado:	Diciembre 11 de 2012
 ****************************************************************************************************************/
function pintarTabla( $datos, $class, $filas = 3, $columnas = 3 ){

	global $idsArtsConSaldo;
// var_dump( $datos );
	if( $datos['error'][0] ){
		echo "<table align='center' class='$class' cellspacing=1>";
		
		echo "<tr class=encabezadotabla>";
		echo "<td colspan=2>MEDICAMENTOS</td>";
		echo "<td colspan=2>Cantidad</td>";
		echo "</tr>";
		
		for( $i=0; $i < $filas; $i++){

			$color = "class='colorAzul4'";
			if( $i%2 == 0)
				$color = "class='colorAzul5'";

			if( $datos[$i][5] == 'N' ){
				$color = "bgcolor='yellow'";
			}
			
			if( $datos[$i][10] ){
				$color = "class=fondoAmarilloOscuro";
			}
			echo "<tr>";
			
			//Código del articulo
			echo "<td $color style='color:black;text-align:center;width:100px;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][0]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".trim($datos[$i][2])."</td>";
			
			//Nombre articulo
			echo "<td $color style='color:black;text-align:left;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][0]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".trim($datos[$i][0])."</td>";

			//Aqui va el valor a cargar
			echo "<td $color style='color:black;text-align:right;width:30px;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][0]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".trim($datos[$i][1])."</td>";

			//Aqui va la unidad de manejo
			echo "<td $color style='color:black;text-align:right;width:30px;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][0]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".trim($datos[$i][8])."</td>";

			echo "<td style='display:none' bgcolor='#cccccc' style='color:black;text-align:right' onMouseDown='mostrar(this);'  title='Saldo:".number_format($datos[$i][9],1,".","")."'>".number_format($datos[$i][9],1,".","")."</td>";

			echo "</tr>";
			
			/*for( $j = 0; $j < $columnas; $j++ ){
				if( $j == $columnas-3 ){
					//Aqui va el valor a cargar
					echo "<td $color style='color:black;text-align:right;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][$j]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".trim($datos[$i][1])."</td>";
				}
				elseif( $j == $columnas-2 ){
					//Aqui va la unidad de manejo
					echo "<td $color style='color:black;text-align:right;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][$j]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".trim($datos[$i][8])."</td>";
				}
				elseif( $j == $columnas-1 ){
					echo "<td style='display:none' bgcolor='#cccccc' style='color:black;text-align:right' onMouseDown='mostrar(this);'  title='Saldo:".number_format($datos[$i][9],1,".","")."'>".number_format($datos[$i][9],1,".","")."</td>";
				}
				else{	//Nombre articulo
					echo "<td $color style='color:black;text-align:left' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][$j]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".trim($datos[$i][$j])."</td>";
				}
			}*/
		}
		echo "</table>";
		
	}
	else{
		echo "<table align='center' class='$class' width='95%' cellspacing=0>";
		for( $i=0; $i < $filas; $i++){
			echo "<tr class='colorAzul5'>
					<td colspan='2'>{$datos[$i][0]}</td></tr>";
		}
		echo "</table>";
	}
	
	/**************************************************************************************************
	 * Pongo todos los articulos que tengan saldo, con el fin de poder saber cuáles puedo reemplazar
	 **************************************************************************************************/
	foreach( $datos as $key => $value ){
		if( trim( $value[1] ) > 0 ){
			$artEnListaSaldo .= "-".$datos[$key][2];
		}
		else{}
	}
	echo "<INPUT type='hidden' name='artEnListaSaldo' value='".substr( $artEnListaSaldo, 1 )."'> ";
	echo "<INPUT type='hidden' name='idsArtsConSaldo' value='".$idsArtsConSaldo."'> ";
	/**************************************************************************************************/
}

// function pintarTabla( $datos, $class, $filas = 3, $columnas = 3 ){

	// global $idsArtsConSaldo;

	// if( $datos['error'][0] ){
		// echo "<table align='center' class='$class' width='95%' cellspacing=0>";
		// for( $i=0; $i < $filas; $i++){
			// echo "<tr>";

			// $color = "class='colorAzul4'";
			// if( $i%2 == 0)
				// $color = "class='colorAzul5'";

			// if( $datos[$i][5] == 'N' ){
				// $color = "bgcolor='yellow'";
			// }
			
			// if( $datos[$i][10] ){
				// $color = "class=fondoAmarilloOscuro";
			// }

			// for( $j = 0; $j < $columnas; $j++ ){
				// if( $j == $columnas-3 ){
					// //Aqui va el valor a cargar
					// echo "<td $color style='color:black;text-align:right;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][$j]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".substr($datos[$i][1],0,27)."</td>";
				// }
				// elseif( $j == $columnas-2 ){
					// //Aqui va la unidad de manejo
					// echo "<td $color style='color:black;text-align:right;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][$j]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".substr($datos[$i][8],0,27)."</td>";
				// }
				// elseif( $j == $columnas-1 ){
					// echo "<td style='display:none' bgcolor='#cccccc' style='color:black;text-align:right' onMouseDown='mostrar(this);'  title='Saldo:".number_format($datos[$i][9],1,".","")."'>".number_format($datos[$i][9],1,".","")."</td>";
				// }
				// else{	//Nombre articulo
					// echo "<td $color style='color:black;text-align:left' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][$j]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".substr($datos[$i][$j],0,27)."</td>";
				// }
			// }
			// echo "</tr>";
		// }
		// echo "</table>";
		
	// }
	// else{
		// echo "<table align='center' class='$class' width='95%' cellspacing=0>";
		// for( $i=0; $i < $filas; $i++){
			// echo "<tr class='colorAzul5'>
					// <td colspan='2'>{$datos[$i][0]}</td></tr>";
		// }
		// echo "</table>";
	// }
	
	// /**************************************************************************************************
	 // * Pongo todos los articulos que tengan saldo, con el fin de poder saber cuáles puedo reemplazar
	 // **************************************************************************************************/
	// foreach( $datos as $key => $value ){
		// if( trim( $value[1] ) > 0 ){
			// $artEnListaSaldo .= "-".$datos[$key][2];
		// }
		// else{}
	// }
	// echo "<INPUT type='hidden' name='artEnListaSaldo' value='".substr( $artEnListaSaldo, 1 )."'> ";
	// echo "<INPUT type='hidden' name='idsArtsConSaldo' value='".$idsArtsConSaldo."'> ";
	// /**************************************************************************************************/
// }


function ArticulosXPacienteCM( $pac ){

	global $conex;
	global $bd;
	global $cco;
	global $centraldemezclas;
	global $fecDispensacion;
	global $tipTrans;
	global $tmpDispensacion;

	global $horaCorteDispensacion;

	global $tempRonda;

	global $procesoContingencia;
//	global $wcenmez;

	global $idsArtsConSaldo;
	
	global $emp;

	if( $tipTrans == "C" ){

		$datos = array();		//Guarda los articulos con saldo positivos
		$vacios = array();		//Guarda los articulos con saldo en 0
		$numrows = false;

		$ori = 'CM';

		if( $cco['cod'] == $centraldemezclas ){
			$ori = 'CM';
		}

		//Buscando al paciente en Kardex electrónico por orden de prioridad y  estanteria
		//con origen en servicio farmaceutico (SF)				
		$sql = "SELECT 
					  Kadcdi, Kaddis, Kadart, Artcom, Kadsus, 'P' as Artpos, Perequ, Kadfin, Kadhin, 'NU' as Kadpro, Kadcfr, Kadcma, Kaddma, Kaddia, Kadcpx, a.id, Kadsad, Kadreg, Kadcnd, '' as Artubi, Kadpri, Kadido, Artuni, Kadron, Kadfro, Kadaan
				FROM 
					{$bd}_000054 a, 
					cenpro_000002 b, 
					cenpro_000001 c,
					{$bd}_000043 d
				WHERE 
					kadhis='{$pac['his']}'  
					AND kading='{$pac['ing']}'
					AND a.kadfec = '".date("Y-m-d")."'
					AND kadcon = 'on'
					AND kadsus != 'on'
					AND kadori = 'CM'
					AND artcod = kadart
					AND kadare = 'on'
					AND kadper = percod
					AND arttip = tipcod
				ORDER BY kadart
				"; 

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		$info = consultarInfoTipoArticulos( $conex, $bd );

		$articulos = Array();

		for( $j = 0; $r = mysql_fetch_array( $res ); $j++ ){

			@$r[ 'Kaddia' ] = trim( $r['Kaddia'] );
			@$r[ 'Kaddma' ] = trim( $r['Kaddma'] );

			$auxProtocoloNew = consultarTipoProtocoloPorArticulo( $conex, $bd, $cco['cod'], $r[ 'Kadart' ] );

			if( !empty( $auxProtocoloNew ) ){
				$r[ 'Kadpro' ] = $auxProtocoloNew;
			}

			$tiempoPreparacion = $tmpDispensacion;

			$corteProduccion   = $info[ $r[ 'Kadpro' ] ]['horaCorteProduccion'];
			$corteDispensacion = $info[ $r[ 'Kadpro' ] ]['horaCaroteDispensacion'];

			//Agosto 29 de 2012
			$corteDispensacion = substr( $info[ $r[ 'Kadpro' ] ]['horaCaroteDispensacion'],0 , 2 )*3600;

			$disCco = false;

			$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $pac['sac'] );	//consulto el tiempo de dispensación por cco

			if( strtoupper( $r[ 'Kadpro' ] ) != 'LC' && $disCco ){
				$corteDispensacion = $disCco;
			}

			$temprxRonda = ( intval( ( date( "H" )+intval( $corteDispensacion/3600 ) )/$tiempoPreparacion )*$tiempoPreparacion );

			$info[ $r[ 'Kadpro' ] ]['proximaRonda'] = strtotime( date( "Y-m-d" )." 00:00:00" ) + $temprxRonda*3600;

			//Limito la proxima ronda hasta la hora de corte del día siguiente
			$info[ $r[ 'Kadpro' ] ]['proximaRonda'] = min( $info[ $r[ 'Kadpro' ] ]['proximaRonda'], strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 );

//			list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s", $info[ $r[ 'Kadpro' ] ]['proximaRonda']-$tiempoPreparacion*3600 ) );
			list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s", $info[ $r[ 'Kadpro' ] ]['proximaRonda'] ) );

			$horaSiguiente = "";

			$perteneceRonda = perteneceRonda( $fecpxr, $ronda, $tiempoPreparacion, $r['Kadfin'], $r['Kadhin'], $r['Perequ'], $horaSiguiente, $despuesHoraCortePx, $r['Kaddma'], $r['Kaddia'] );


			//Para todos los pisos por defecto es 4 horas
			// $antHoras = 4;	//Controla cuantas horas antes puede dispensar
			$antHoras = $corteDispensacion/3600;	//Controla cuantas horas antes puede dispensar
			if( $cco['urg'] ){
				//Para urgencias se deja 22
				$antHoras = 42;	//Controla cuantas horas antes puede dispensar
			}

			/******************************************************************
			 * Marzo 12 de 2012
			 ******************************************************************/
			//Quito las rondas que ya no se pueden aplicar
			quitarRondasADispensar(  $conex, $bd, $r, $antHoras-2, $fecDispensacion );
			/******************************************************************/

			/****************************************************************
			 * Septiembre 18 de 2013
			 ****************************************************************/
			if( tratarComoNoANecesidad( $r['Kadcnd'] ) ){
				$r['Kadcnd'] = '';
			}
			/****************************************************************/

			/********************************************************************************
			 * Septiembre 12 de 2011
			 *
			 * - Verifico si el articulo es a necesidad
			 * - Si es a necesidad, busco si el articulo no tiene saldo en piso
			 * - Si no tiene saldo en piso se muestra
			 * - Si no es a necesidad no se muestra
			 ********************************************************************************/
			$esANecesidad = esANecesidad( $bd, $conex, $r['Kadcnd'] );

			$esCiclos24 = esCcoCiclos24Horas( $conex, $bd, $pac['sac'] );
			
			if( $esCiclos24 )
				$esANecesidad = false;
			// if( $cco['urg'] ){
				// $esANecesidad = false;
				// $r['Kadcnd'] = '';
			// }

			$mostrarArticulo = true;
			$requiereSaldo = false;
			if( $esANecesidad ){

				$fraccion = 1; //consultarFraccion( Array( "cod"=>$r[ 'Kadart' ] ), $cco );
				$saldo = tieneSaldoEnPiso(  $bd, $conex, $ori, $pac['his'], $pac['ing'], $r['Kadart'] );

				if( !empty($procesoContingencia) && $procesoContingencia == 'on' ){
					// $val = true;
					$saldo = 0;
				}

				if( $saldo/$fraccion >= ($r['Kadcfr']/$r['Kadcma']) ){
					$requiereSaldo = false;
					$mostrarArticulo = false;

					// $r['Kadsus'] = "off";	//Suspendo para que no se vea
				}
				else{
					$requiereSaldo = true;
					$perteneceRonda = true;	//Siempre debe pedir si es a necesidad y no hay saldo

					$r['Kadcpx'] = "$ronda-".( ceil( $r['Kadcfr']/$r['Kadcma']-$saldo/$fraccion ) )."-0";

					$r['Kadcdi'] = $r['Kaddis']+ceil( $r['Kadcfr']/$r['Kadcma'] );
				}
			}
			/********************************************************************************/










			/****************************************************************************
			 * Agosto 15 de 2012
			 ****************************************************************************/
			$regletas = Array();

			$fechorTrasladoRep = 0;

			//consulto la fecha y hora de traslado desde urgencia
			$horaTrasladoRep = consultarHoraTrasladoUrgencias( $conex, $bd, $pac['his'], $pac['ing'], $fecDispensacion );

			if( !$horaTrasladoRep ){
				$horaTrasladoRep = consultarHoraTrasladoUrgencias( $conex, $bd, $pac['his'], $pac['ing'], date( "Y-m-d", strtotime( $fecDispensacion )-24*3600 ) );

				if( $horaTrasladoRep ){
					$fechorTrasladoRep = strtotime( date( "Y-m-d", strtotime( $fecDispensacion )-24*3600 )." ".$horaTrasladoRep );
				}
			}
			else{
				$fechorTrasladoRep = strtotime( $fecDispensacion." ".$horaTrasladoRep );
			}

			$corteDispensacionRep = $corteDispensacion;

			/******************************************************************************************
			 * Agosto 28 de 2012
			 *
			 * No permito crear una regleta mas alla de la hora de corte del día siguiente
			 ******************************************************************************************/
			$corteDispensacionRep = min( $corteDispensacionRep, $horaCorteDispensacion*3600+2*3600 );
			/******************************************************************************************/

			$canAplicada = 0;

			//Busco si hay articulos repetidos
			$hayRepetidos = false;
			for( $jj = 0; $r2 = mysql_fetch_array( $res ); $jj++ ){

				if( $r2['Kadart'] == $r['Kadart'] ){

					$j++;

					if( $r2['Kadsus'] != 'on' ){
						
						//Si es a necesidad y tiene saldo no lo muestro
						//De lo contrario, le coloco frecuencia cada 24 horas y fecha y hora de inicio
						//la cambio para que muestro lo necesario
						$esANecesidadRep = esANecesidad( $bd, $conex, $r2['Kadcnd'] );
						
						if( $esCiclos24 )
							$esANecesidadRep = false;
						
						//Recalculo dosis máxima, esto por que se requiere que un medicamento se dispense hasta que cumpla el total de dosis a aplicar
						//y el sistema hasta ahora calculo hasta cumplir la dosis maxima por frecuencia
						if( trim( $r2[ 'Kaddma' ] ) != '' ){
							$r2[ 'Kaddma' ] = recalcularDosisMaxima( $conex, $bd, $pac['his'], $pac['ing'], $r2['Kadart'], $r2, $antHoras, $esANecesidadRep );
							
							//Agosto 2 de 2017
							if( $r2[ 'Kaddma' ] == 0 ){
								continue;
							}
						}
						
						$agregarRepetido = true;	//Indica si el medicamento es repetido

						$r['Kadcdi'] += $r2['Kadcdi'];
						$r['Kaddis'] += $r2['Kaddis'];

						//Consulto la cantidad aplicada para este medicamento, hasta maximo la hora de corte de dispensación del día siguiente
						$canAplicadaRep = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r2['Kadart'], max( strtotime( $r2[ 'Kadfin' ]." ".$r2[ 'Kadhin' ] ), time()-$antHoras*3600 ), min( time()+$corteDispensacion, strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 ), $r2[ 'Kadido' ], $fechorTrasladoRep );
						$canAplicada += $canAplicadaRep;

						/****************************************************************
						 * Septiembre 18 de 2013
						 ****************************************************************/
						if( tratarComoNoANecesidad( $r2['Kadcnd'] ) ){
							$r2['Kadcnd'] = '';
						}
						/****************************************************************/


						// if( $cco['urg'] ){
							// $esANecesidadRep = false;
							// $r['Kadcnd'] = '';
						// }

						if( $esANecesidadRep ){

							$fraccionRep = 1; //consultarFraccion( Array( "cod"=>$r2[ 'Kadart' ] ), $cco );
							$saldoRep = tieneSaldoEnPiso(  $bd, $conex, $ori, $pac['his'], $pac['ing'], $r2['Kadart'] );

							/***********************************************************************************************************************
							 * Enero 05 de 2017
							 * Ya no se valida que si tiene saldo un medicamento a necesidad no se dispensa
							 * solo se cambia la frecuencia a 24 horas y la hora de inicio para que no dispense más de la dosis debida
							 ***********************************************************************************************************************/
							if( false && $saldoRep/$fraccionRep >= ($r2['Kadcfr']/$r2['Kadcma']) ){
								$agregarRepetido = false;
							}
							else{
								//cambio la fecha y hora de inicio y la frecuencia para que salga lo suficiente para una dosis
								//Primero verifico si la fecha y hora de dispensacion sea mayor a la hora de traslado
								//esto por que no se puede dispensar nada en la ronda en que se recibe un paciente desde urgencias
								if( strtotime( date( "Y-m-d" )." ".gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 ) ) >= $fechorTrasladoRep ){
									$r2['Kadfin'] = date( "Y-m-d" );
									$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 );
								}
								else{
									$r2['Kadfin'] = date( "Y-m-d" );
									$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H", $fechorTrasladoRep + 2*3600)/2 )*2*3600 );
								}

								$r2['Perequ'] = 24;

								//Aumento dosis tanto como se halla dispensado las ultimas dosis para que siempre pueda dispensar
								$r2['Kadcfr'] += $canAplicadaRep*$r2['Kadcma'];
							}
						}

						if( $agregarRepetido ){
							//Con las funciones virtuales
							$fhFinMed = consultarFechaHoraFinalMedicamento( $horaCorteDispensacion, $fecDispensacion, $r2[ 'Kadfin' ], $r2[ 'Kadhin' ], $r2[ 'Kaddma' ], $r2[ 'Kaddia' ], $r2[ 'Perequ' ] );

							$regletas[] = Array( 0 			 => crearRegletaVirtual( strtotime( $fecDispensacion." 00:00:00" )-($antHoras-2)*3600, strtotime( $fecDispensacion." 00:00:00" )+22*3600+$corteDispensacionRep, strtotime( $r2['Kadfin']." ".$r2['Kadhin'] ), $r2[ 'Perequ' ], $r2[ 'Kadcfr' ]/$r2[ 'Kadcma' ], $fhFinMed, $fechorTrasladoRep ),
												 'id' 		 => $r2[ 'id' ],
												 'fecUltDis' => 0
												);
						}

						$hayRepetidos = true;
					}
				}
				else{
					break;
				}
			}

			if( true || $hayRepetidos ){

				if( $r2 ){
					@mysql_data_seek( $res, $j+1 );
				}

				if( $r['Kadsus'] != 'on' ){
					
					//Recalculo dosis máxima, esto por que se requiere que un medicamento se dispense hasta que cumpla el total de dosis a aplicar
					//y el sistema hasta ahora calculo hasta cumplir la dosis maxima por frecuencia
					if( trim( $r[ 'Kaddma' ] ) != '' )
						$r[ 'Kaddma' ] = recalcularDosisMaxima( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], $r, $antHoras, $esANecesidad );

					//Consulto la cantidad aplicada para este medicamento, hasta maximo la hora de corte de dispensación del día siguiente
					$canAplicadaRep = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], max( strtotime( $r[ 'Kadfin' ]." ".$r[ 'Kadhin' ] ), time()-$antHoras*3600 ), min( time()+$corteDispensacion, strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 ), $r[ 'Kadido' ], $fechorTrasladoRep );
					$canAplicada += $canAplicadaRep;
					
					/************************************************************************************************
					 * Si tuvo reemplazo se tiene en cuenta las aplicaciones anteriores del medicamento
					 ************************************************************************************************/
					if( $r['Kadaan'] != '' ){
						list( $aanArt, $aanFec ) = explode( ",", $r[ 'Kadaan' ] );
						
						//Solo se hace cambio de articulo por qué al hacer reemplazo quedan los mismos datos que el medicamento anterior
						if( $aanFec == date( "Y-m-d", time()-$antHoras*3600 ) ){
							$canAplicadaRepAan = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $aanArt, max( strtotime( $r[ 'Kadfin' ]." ".$r[ 'Kadhin' ] ), time()-$antHoras*3600 ), min( time()+$corteDispensacion, strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 ), '', $fechorTrasladoRep );
							
							//Es la cantidad aplicada con el medicamento anterior, debo traducirlo al actual
							$aanFracc = consultarFraccionPorArticulo( $conex, $bd, $aanArt, '1050' );
							
							if( $aanFracc['unidad'] == $r['Kadcfr'] ){
								$canAplicadaRepAan = $canAplicadaRepAan*$aanFracc['fraccion']/$r[ 'Kadcma' ];
							}
							
							$canAplicada += $canAplicadaRepAan;
							$canAplicadaRep += $canAplicadaRepAan;
						}
					}
					/************************************************************************************************/

					$agregarRepetido = true;

					if( $esANecesidad ){

						/************************************************************************************************************************
						 * Enero 05 de 2017
						 * Ya no se valida que si tiene saldo un medicamento a necesidad no se dispensa
						 * solo se cambia la frecuencia a 24 horas y la hora de inicio para que no dispense más de la dosis debida
						 ************************************************************************************************************************/
						if( false && $saldo/$fraccion >= ($r['Kadcfr']/$r['Kadcma']) ){
							$agregarRepetido = false;
						}
						else{
							//cambio la fecha y hora de inicio y la frecuencia para que salgo lo suficiente para una dosis
							//Primero verifico si la fecha y hora de dispensacion sea mayor a la hora de traslado
							//esto por que no se puede dispensar nada en la ronda en que se recibe un paciente desde urgencias
							if( strtotime( date( "Y-m-d" )." ".gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 ) ) >= $fechorTrasladoRep ){
								$r['Kadfin'] = date( "Y-m-d" );
								$r['Kadhin'] = gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 );
							}
							else{
								$r2['Kadfin'] = date( "Y-m-d" );
								$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H", $fechorTrasladoRep+2*3600 )/2 )*2*3600 );
							}

							$r['Perequ'] = 24;

							//Aumento dosis tanto como se halla dispensado las ultimas dosis para que siempre pueda dispensar
							$r['Kadcfr'] += $canAplicadaRep*$r['Kadcma'];
						}
					}

					if( $agregarRepetido ){
						$fhFinMed = consultarFechaHoraFinalMedicamento( $horaCorteDispensacion, $fecDispensacion, $r[ 'Kadfin' ], $r[ 'Kadhin' ], $r[ 'Kaddma' ], $r[ 'Kaddia' ], $r[ 'Perequ' ] );

						$regletas[] = Array( 0    		 => crearRegletaVirtual( strtotime( $fecDispensacion." 00:00:00" )-($antHoras-2)*3600, strtotime( $fecDispensacion." 00:00:00" )+22*3600+$corteDispensacionRep,strtotime( $r['Kadfin']." ".$r['Kadhin'] ), $r[ 'Perequ' ], $r[ 'Kadcfr' ]/$r[ 'Kadcma' ], $fhFinMed, $fechorTrasladoRep ),
											 'id' 		 => $r[ 'id' ],
											 'fecUltDis' => 0
											 );
					}
				}
				else{
					$r['Kadsus'] = 'off';
				}

				//Consulto si hay saldo en piso
				$saldoPiso = tieneSaldoEnPiso( $bd, $conex, $ori, $pac['his'], $pac['ing'], $r['Kadart'] );

				//Si esta en 0 es que esta vacio
				if( !$saldoPiso ){
					$saldoPiso = 0;
				}

				// $fraccionArticulo = consultarFraccion( Array( "cod"=>$r[ 'Kadart' ] ), $cco );
				$fraccionArticulo = 1;

				//Consulto la cantidad aplicada de las ultimas dos rondas
				// $canAplicada = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], time()-4*3600, time(), false, $fechorTrasladoRep );

				$canDisVirtual = cantidadADispensarRegletasVirtual( $regletas, time()-$antHoras*3600 );

				//Carga una cantidad a la regletas
				cargarCantidadRegletasVirtuales( $regletas, $canDisVirtual+$canAplicada/$fraccionArticulo+round( $saldoPiso, 3 ) );

				//Combino las regletas
				$regletaCombinada = combinarRegletasVirtuales( $regletas, false );

				//la convierto a string
				$stRegletaCombinada = regletaVirtualToString( $regletaCombinada, strtotime( $fecDispensacion." 00:00:00") );

				$r[ 'Kadcpx' ] = $stRegletaCombinada;

				//verifico que halla algo que cargar para las rondas correspondientes
				$mostrarArticulo = $perteneceRonda = perteneceARondaPorRegletaVirtual( $regletaCombinada, $info[ $r[ 'Kadpro' ] ]['proximaRonda'] );

				actualizarRegletas( $conex, $bd, $regletas, strtotime( $fecDispensacion." 00:00:00" ) );
				
				//Si se va a mostrar el articulo busco pongo los id en una variable para que solo salgan los id que se pueden reemplazar
				if( $mostrarArticulo ){
					foreach( $regletas as $key => $value ){
						if( perteneceARondaPorRegletaVirtual( $value[0], $info[ $r[ 'Kadpro' ] ]['proximaRonda'] ) )
							$idsArtsConSaldo .= ",".$value['id'];
					}
				}
			}
			else{
				//devuelvo a la posicion en que inicio originalmente
				@mysql_data_seek( $res, $j+1 );
			}
			/****************************************************************************/









			//Se muestra el articulo si pertenece a la ronda y la hora actual esta entre la hora de corte de dispensacion y la ronda que se va a mostar
			$sindis = cantidadSinDispensarRondas( $r[ 'Kadcpx' ], $ronda, date( "Y-m-d" ) == $fecpxr );
			$can = 0;	//Septiembre 22 de 2011

			if( $mostrarArticulo && ( ( $perteneceRonda && time() >= $info[ $r[ 'Kadpro' ] ]['proximaRonda']-$corteDispensacion && time() <= $info[ $r[ 'Kadpro' ] ]['proximaRonda'] ) ) ){

				$can = cantidadADispensarRonda( $r['Kadcpx'], $ronda, date( "Y-m-d" ) == $fecpxr );

				if( $esANecesidad && $requiereSaldo ){

					//Busco una ronda que no este cargada o este parcialmente cargada y pido esa cantidad
					$auxCpx = consultarRondaNoCargada( $r['Kadcpx'], $can );
					$sindis = 0;

					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][0] += $can+$sindis;											//Cantidad a dispensar
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][1] += 0;

				}
				else{

					//Cantidad a dispensar
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][0] += $can+$sindis;
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][1] += cantidadDispensadaRonda( $r[ 'Kadcpx' ], $ronda, date( "Y-m-d" ) == $fecpxr );	//$r['Kaddis'];	//Cantidad dispensada
				}

				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][2] = $r['Kadart'];	//Codigo del articulo
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][3] = $r['Artcom'];	//Nombre comercial
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][4] = $r['Kadsus'];	//Articulo supendido
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][5] = $r['Artpos'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][6] += $r['Kadcdi'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][7] += $r['Kaddis']; //cantidadSinDispensarRondas( $r[ 'Kadcpx' ], "00:00:00" );	//Posicion	//Agosto 10 de 2011
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][8] = $r['Artuni'];  	//Diciembre 11 de 2012
			}
			elseif( $mostrarArticulo && ( $sindis > 0 ) ){

				if( $esANecesidad && $requiereSaldo ){

					//Busco una ronda que no este cargada o este parcialmente cargada y pido esa cantidad
					$auxCpx = consultarRondaNoCargada( $r['Kadcpx'], $can );
					$sindis = 0;

					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][0] += $can+$sindis;											//Cantidad a dispensar
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][1] += 0;
				}
				else{

					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][0] += $can+$sindis; //Cantidad a dispensar
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][1] += 0;//cantidadDispensadaRonda( $r[ 'Kadcpx' ], $ronda ); //$r['Kaddis'];	//Cantidad dispensada
				}

				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][2] = $r['Kadart'];	//Codigo del articulo
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][3] = $r['Artcom'];	//Nombre comercial
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][4] = $r['Kadsus'];	//Articulo supendido
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][5] = $r['Artpos'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][6] += $r['Kadcdi'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][7] += $r['Kaddis'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][8] = $r['Artuni'];	//Diciembre 11 de 2012
			}
		}

		$i = 0;
		foreach( $articulos as $key => $value ){
			$arts[ $i ] = $value;
			$i++;
		}

		//se ordenan los articulos en el siguiente orden:
		//primero los articulos con saldo positivo
		//luego los de saldo 0 y que se encuentren suspendidos
		//Esto con el fin de manejar los errores correctamente
		for($i = 0, $j = 0, $tag = true, $k = 0; $i < 3 || $tag; $k++ ){

			if( !isset( $arts[ $k ] ) ){
				$arts[ $k ] = false;
			}

			$rows = $arts[ $k ];

			if( $rows ){
				if( $rows[0]-$rows[1] > 0 /*&& $rows[6] - $rows[7] > 0*/ && $rows[4] != 'on'  ){
					$numrows = true;
					$datos[$i][0]=$rows[3];
					$datos[$i][1]=ceil( $rows[0]-$rows[1] );//( ceil( $rows[0]-$rows[1] ) <= $rows[6] - $rows[7] ) ? ceil( $rows[0]-$rows[1] ) : $rows[6] - $rows[7];
					$datos[$i][2]=$rows[2];
					$datos[$i][3]=$rows[1];
					$datos[$i][4]=false;
					$datos[$i][5]=$rows[5];
					$datos[$i][6]=$rows[6];
					$datos[$i][7]=$rows[7];
					$datos[$i][8]=$rows[8];	//Diciembre 11 de 2012
					$datos[$i][9]=tieneSaldoEnPiso( $bd, $conex, $ori, $pac['his'], $pac['ing'], $rows[2] );
					$datos[$i][10]=consultarCTCAgotado( $conex, $bd, $pac['his'], $pac['ing'], $rows[2], $emp );
					$i++;
				}
				else{
					$vacios[$j][0]=$rows[3];
					$vacios[$j][1]=ceil( $rows[0]-$rows[1] );//( ceil( $rows[0]-$rows[1] ) <= $rows[6] - $rows[7] ) ? ceil( $rows[0]-$rows[1] ) : $rows[6] - $rows[7];
					$vacios[$j][2]=$rows[2];
					$vacios[$j][3]=$rows[1];
					$vacios[$j][5]=$rows[5];
					$vacios[$j][6]=$rows[6];
					$vacios[$j][7]=$rows[7];
					$vacios[$j][8]=$rows[8];	//Diciembre 11 de 2012
					$vacios[$j][9]=tieneSaldoEnPiso( $bd, $conex, $ori, $pac['his'], $pac['ing'], $rows[2] );
					$vacios[$j][10]=consultarCTCAgotado( $conex, $bd, $pac['his'], $pac['ing'], $rows[2], $emp );

					if($rows[4] != 'on'){
						$vacios[$j][4]=false;
					}
					else{
						$vacios[$j][4]=true;
					}
					$j++;
				}
			}
			else{
				$datos[$i][0]="&nbsp;";
				$datos[$i][1]="&nbsp;";
				$datos[$i][2]="&nbsp;";
				$datos[$i][3]="&nbsp;";
				$datos[$i][5]="&nbsp;";
				$datos[$i][6]="&nbsp;";
				$datos[$i][7]="&nbsp;";
				$datos[$i][8]="";	//Diciembre 11 de 2012
				$datos[$i][9]=0;
				$datos[$i][10]=false;
				$i++;
				$tag = false;
			}
		}

		for( $i = 0; $i < count( $vacios ); $i++ ){
			$datos[ count( $datos) ] = $vacios[$i];
		}

		//En caso de que no hallan articulos a dispensar
		//Se construye un mensaje para mostrar en el centro de la pantalla
		$datos['error'][0]=true;
		if ( !$numrows ){
			$datos[0][0]="NO HAY";
			$datos[1][0]="ARTICULOS A";
			$datos[2][0]="DISPENSAR";
			$datos['error'][0]=false;
		}
		
		return $datos;
	}
	else{

		$datos = array();		//Guarda los articulos con saldo positivos
		$vacios = array();		//Guarda los articulos con saldo en 0
		$numrows = false;

		$ori = 'SF';

		if( $cco['cod'] == $centraldemezclas ){
			$ori = 'CM';
		}

		//Buscando al paciente en Kardex electrónico por orden de prioridad y  estanteria
		//con origen en servicio farmaceutico (SF)
		$sql = "(SELECT
					sum(kadcdi), sum(kaddis), kadart, artcom, kadsus, artpos
		        FROM
					{$bd}_000054 a, {$bd}_000026 b, {$bd}_000053 c
		        WHERE
		        	kadhis='{$pac['his']}' AND
		        	kading='{$pac['ing']}' AND
		        	kadori='$ori' AND
		        	kadpri='on' AND
		        	a.kadfec = '$fecDispensacion' AND
		        	artcod = kadart AND
					karhis = kadhis AND
					karing = kading AND
					karcco = kadcco AND
					c.fecha_data = kadfec AND
					karcon = 'on' AND
					karest = 'on' AND
					kadare = 'on'
		        GROUP BY kadart
		        ORDER BY artubi, artcom ASC)
		        UNION
		        (SELECT
					sum(kadcdi), sum(kaddis), kadart, artcom, kadsus, artpos
		        FROM
					{$bd}_000054 a, {$bd}_000026 b, {$bd}_000053 c
		        WHERE
		        	kadhis='{$pac['his']}' AND
		        	kading='{$pac['ing']}' AND
		        	kadori='$ori' AND
		        	kadpri<>'on' AND
		        	a.kadfec = '$fecDispensacion' AND
		        	artcod = kadart AND
		        	karhis = kadhis AND
					karing = kading AND
					karcco = kadcco AND
					c.fecha_data = kadfec AND
					karcon = 'on' AND
					karest = 'on'  AND
					kadare = 'on'
		        GROUP BY kadart, kadsus
		        ORDER BY artubi, artcom ASC)";

		$res = mysql_query( $sql, $conex );

		//se ordenan los articulos en el siguiente orden:
		//primero los articulos con saldo positivo
		//luego los de saldo 0 y que se encuentren suspendidos
		//Esto con el fin de manejar los errores correctamente
		for($i = 0, $j = 0, $tag = true; $i < 3 || $tag; ){
			if( $rows = mysql_fetch_array( $res ) ){
				if( $rows[0]-$rows[1] > 0 && $rows[4] != 'on'  ){
					$numrows = true;
					$datos[$i][0]=$rows[3];
					$datos[$i][1]=$rows[0]-$rows[1];
					$datos[$i][2]=$rows[2];
					$datos[$i][3]=$rows[1];
					$datos[$i][4]=false;
					$datos[$i][5]=$rows[5];;
					$i++;
				}
				else{
					$vacios[$j][0]=$rows[3];
					$vacios[$j][1]=$rows[0]-$rows[1];
					$vacios[$j][2]=$rows[2];
					$vacios[$j][3]=$rows[1];
					$vacios[$j][5]=$rows[5];

					if($rows[4] != 'on'){
						$datos[$i][4]=false;
					}
					else{
						$datos[$i][4]=true;
					}
					$j++;
				}
			}
			else{
				$datos[$i][0]="&nbsp;";
				$datos[$i][1]="&nbsp;";
				$datos[$i][2]="&nbsp;";
				$datos[$i][3]="&nbsp;";
				$datos[$i][5]="&nbsp;";
				$i++;
				$tag = false;
			}

		}

		for( $i = 0; $i < count( $vacios ); $i++ ){
			$datos[ count( $datos) ] = $vacios[$i];
		}

		//En caso de que no hallan articulos a dispensar
		//Se construye un mensaje para mostrar en el centro de la pantalla
		$datos['error'][0]=true;
		if ( !$numrows ){
			$datos[0][0]="NO HAY";
			$datos[1][0]="ARTICULOS A";
			$datos[2][0]="DISPENSAR";
			$datos['error'][0]=false;
		}

		return $datos;
	}
}


/**
 * Consulta los articulos en Kardex Electrónico cargados a un paciente
 *
 * @param 	array 	$pac		Paciente a cargar articulo
 *
 * @return 	string $arts		Articulos para mostrar (maximo 3)
 * 								[$i] Fila con los datos correpondientes al articulo
 * 								[$i][0] Nombre comercial del articulo
 * 								[$i][1] Cantidad que falta por dispensar
 * 								[$i][2] Codigo del Articulo
 * 								[$i][3] Cantidad dispensada
 * 								[$i][4] Indica si el articulo esta suspendido o no
 * 								[3][0]  Campo bool. Indica si el paciente tiene articulos o no
 * 								[$i][5] Campo bool. Indica si el articulo es pos o no
 */
function ArticulosXPaciente( $pac ){

	@ArticulosXPacienteCM( $pac );

	global $conex;
	global $bd;
	global $cco;
	global $centraldemezclas;
	global $fecDispensacion;
	global $tipTrans;
	global $tmpDispensacion;

	global $horaCorteDispensacion;

	global $tempRonda;

	global $procesoContingencia;
//	global $wcenmez;

	global $idsArtsConSaldo;
	
	global $emp;

	if( $tipTrans == "C" ){

		$datos = array();		//Guarda los articulos con saldo positivos
		$vacios = array();		//Guarda los articulos con saldo en 0
		$numrows = false;

		$ori = 'SF';

		if( $cco['cod'] == $centraldemezclas ){
			$ori = 'CM';
		}

		if( $cco['ayu'] ){
			
			//Buscando al paciente en Kardex electrónico por orden de prioridad y  estanteria
			//con origen en servicio farmaceutico (SF)
			$sql = "(SELECT
						Kadcdi, Kaddis, Kadart, Artcom, Kadsus, Artpos, Perequ, Kadfin, Kadhin, Kadpro, Kadcfr, Kadcma, Kaddma, Kaddia, Kadcpx, a.id, Kadsad, Kadreg, Kadcnd, Artubi, Kadpri, Kadido, Artuni, Kadron, Kadfro, Kadaan
					FROM
						{$bd}_000054 a,
						{$bd}_000026 b,
						{$bd}_000053 c,
						{$bd}_000043 d
					WHERE
						kadhis='{$pac['his']}' AND
						kading='{$pac['ing']}' AND
						kadori='$ori' AND
						kadpri = 'on' AND
						a.kadfec = '$fecDispensacion' AND
						artcod = kadart AND
						karhis = kadhis AND
						karing = kading AND
						karcco = kadcco AND
						c.fecha_data = kadfec AND
						karest = 'on' AND
						kadest = 'on' AND
						kadper = percod AND
						kadhis IN(
							SELECT Ekxhis
							  FROM {$bd}_000208 e
							 WHERE Ekxhis = a.Kadhis
							   AND Ekxing = a.Kading
							   AND Ekxart = a.Kadart
							   AND Ekxido = a.Kadido
							   AND Ekxfec = a.Kadfec
							   AND Ekxayu = '".$cco['cod']."'
						))
					UNION
					(SELECT
						Kadcdi, Kaddis, Kadart, Artcom, Kadsus, Artpos, Perequ, Kadfin, Kadhin, Kadpro, Kadcfr, Kadcma, Kaddma, Kaddia, Kadcpx, a.id, Kadsad, Kadreg, Kadcnd, Artubi, Kadpri, Kadido, Artuni, Kadron, Kadfro, Kadaan
					FROM
						{$bd}_000054 a,
						{$bd}_000026 b,
						{$bd}_000053 c,
						{$bd}_000043 d
					WHERE
						kadhis='{$pac['his']}' AND
						kading='{$pac['ing']}' AND
						kadori='$ori' AND
						kadpri <> 'on' AND
						a.kadfec = '$fecDispensacion' AND
						artcod = kadart AND
						karhis = kadhis AND
						karing = kading AND
						karcco = kadcco AND
						c.fecha_data = kadfec AND
						karest = 'on' AND
						kadest = 'on' AND
						kadper = percod AND
						kadhis IN(
							SELECT Ekxhis
							  FROM {$bd}_000208 e
							 WHERE Ekxhis = a.Kadhis
							   AND Ekxing = a.Kading
							   AND Ekxart = a.Kadart
							   AND Ekxido = a.Kadido
							   AND Ekxfec = a.Kadfec
							   AND Ekxayu = '".$cco['cod']."'
						))
					ORDER BY
						Kadpri DESC, Artubi ASC, Artcom ASC
					";
			
		}
		else
		{
			
			//Buscando al paciente en Kardex electrónico por orden de prioridad y  estanteria
			//con origen en servicio farmaceutico (SF)
			$sql = "(SELECT
						Kadcdi, Kaddis, Kadart, Artcom, Kadsus, Artpos, Perequ, Kadfin, Kadhin, Kadpro, Kadcfr, Kadcma, Kaddma, Kaddia, Kadcpx, a.id, Kadsad, Kadreg, Kadcnd, Artubi, Kadpri, Kadido, Artuni, Kadron, Kadfro, Kadaan
					FROM
						{$bd}_000054 a,
						{$bd}_000026 b,
						{$bd}_000053 c,
						{$bd}_000043 d
					WHERE
						kadhis='{$pac['his']}' AND
						kading='{$pac['ing']}' AND
						kadori='$ori' AND
						kadpri = 'on' AND
						a.kadfec = '$fecDispensacion' AND
						artcod = kadart AND
						karhis = kadhis AND
						kadess != 'on' AND
						karing = kading AND
						karcco = kadcco AND
						c.fecha_data = kadfec AND
						kadare = 'on' AND
						karest = 'on' AND
						kadest = 'on' AND
						kadper = percod)
					UNION
					(SELECT
						Kadcdi, Kaddis, Kadart, Artcom, Kadsus, Artpos, Perequ, Kadfin, Kadhin, Kadpro, Kadcfr, Kadcma, Kaddma, Kaddia, Kadcpx, a.id, Kadsad, Kadreg, Kadcnd, Artubi, Kadpri, Kadido, Artuni, Kadron, Kadfro, Kadaan
					FROM
						{$bd}_000054 a,
						{$bd}_000026 b,
						{$bd}_000053 c,
						{$bd}_000043 d
					WHERE
						kadhis='{$pac['his']}' AND
						kading='{$pac['ing']}' AND
						kadori='$ori' AND
						kadpri <> 'on' AND
						a.kadfec = '$fecDispensacion' AND
						artcod = kadart AND
						kadess != 'on' AND
						karhis = kadhis AND
						karing = kading AND
						karcco = kadcco AND
						c.fecha_data = kadfec AND
						karest = 'on' AND
						kadare = 'on' AND
						kadest = 'on' AND
						kadper = percod )
					ORDER BY
						Kadpri DESC, Artubi ASC, Artcom ASC
					";
		}

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		$info = consultarInfoTipoArticulos( $conex, $bd );

		$articulos = Array();

		for( $j = 0; $r = mysql_fetch_array( $res ); $j++ ){

			@$r[ 'Kaddia' ] = trim( $r['Kaddia'] );
			@$r[ 'Kaddma' ] = trim( $r['Kaddma'] );

			$auxProtocoloNew = consultarTipoProtocoloPorArticulo( $conex, $bd, $cco['cod'], $r[ 'Kadart' ] );

			if( !empty( $auxProtocoloNew ) ){
				$r[ 'Kadpro' ] = $auxProtocoloNew;
			}

			$tiempoPreparacion = $tmpDispensacion;

			$corteProduccion   = $info[ $r[ 'Kadpro' ] ]['horaCorteProduccion'];
			$corteDispensacion = $info[ $r[ 'Kadpro' ] ]['horaCaroteDispensacion'];

			//Agosto 29 de 2012
			$corteDispensacion = substr( $info[ $r[ 'Kadpro' ] ]['horaCaroteDispensacion'],0 , 2 )*3600;

			$disCco = false;

			$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $pac['sac'] );	//consulto el tiempo de dispensación por cco

			if( strtoupper( $r[ 'Kadpro' ] ) != 'LC' && $disCco ){
				$corteDispensacion = $disCco;
			}

			$temprxRonda = ( intval( ( date( "H" )+intval( $corteDispensacion/3600 ) )/$tiempoPreparacion )*$tiempoPreparacion );

			$info[ $r[ 'Kadpro' ] ]['proximaRonda'] = strtotime( date( "Y-m-d" )." 00:00:00" ) + $temprxRonda*3600;

			//Limito la proxima ronda hasta la hora de corte del día siguiente
			$info[ $r[ 'Kadpro' ] ]['proximaRonda'] = min( $info[ $r[ 'Kadpro' ] ]['proximaRonda'], strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 );

//			list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s", $info[ $r[ 'Kadpro' ] ]['proximaRonda']-$tiempoPreparacion*3600 ) );
			list( $fecpxr, $ronda ) = explode( " ", date( "Y-m-d H:i:s", $info[ $r[ 'Kadpro' ] ]['proximaRonda'] ) );

			$horaSiguiente = "";

			$perteneceRonda = perteneceRonda( $fecpxr, $ronda, $tiempoPreparacion, $r['Kadfin'], $r['Kadhin'], $r['Perequ'], $horaSiguiente, $despuesHoraCortePx, $r['Kaddma'], $r['Kaddia'] );


			//Para todos los pisos por defecto es 4 horas
			// $antHoras = 4;	//Controla cuantas horas antes puede dispensar
			$antHoras = $corteDispensacion/3600;	//Controla cuantas horas antes puede dispensar
			if( $cco['urg'] ){
				//Para urgencias se deja 22
				$antHoras = 42;	//Controla cuantas horas antes puede dispensar
			}
			
			$ccoSaldoAux = false;
			$ccoSaldoAux = $cco['cod'];
			if( $cco['ayu'] ){
				//Para urgencias se deja 22
				$antHoras = 8;	//Controla cuantas horas antes puede dispensar
				$ccoSaldoAux = $cco['cod'];
			}

			/******************************************************************
			 * Marzo 12 de 2012
			 ******************************************************************/
			//Quito las rondas que ya no se pueden aplicar
			quitarRondasADispensar(  $conex, $bd, $r, $antHoras-2, $fecDispensacion );
			/******************************************************************/

			/****************************************************************
			 * Septiembre 18 de 2013
			 ****************************************************************/
			if( tratarComoNoANecesidad( $r['Kadcnd'] ) ){
				$r['Kadcnd'] = '';
			}
			/****************************************************************/

			/********************************************************************************
			 * Septiembre 12 de 2011
			 *
			 * - Verifico si el articulo es a necesidad
			 * - Si es a necesidad, busco si el articulo no tiene saldo en piso
			 * - Si no tiene saldo en piso se muestra
			 * - Si no es a necesidad no se muestra
			 ********************************************************************************/
			$esANecesidad = esANecesidad( $bd, $conex, $r['Kadcnd'] );

			$esCiclos24 = esCcoCiclos24Horas( $conex, $bd, $pac['sac'] );
			
			if( $esCiclos24 )
				$esANecesidad = false;
			// if( $cco['urg'] ){
				// $esANecesidad = false;
				// $r['Kadcnd'] = '';
			// }

			$mostrarArticulo = true;
			$requiereSaldo = false;
			if( $esANecesidad ){

				$fraccion = 1; //consultarFraccion( Array( "cod"=>$r[ 'Kadart' ] ), $cco );
				$saldo = tieneSaldoEnPiso(  $bd, $conex, $ori, $pac['his'], $pac['ing'], $r['Kadart'], $ccoSaldoAux );

				if( !empty($procesoContingencia) && $procesoContingencia == 'on' ){
					// $val = true;
					$saldo = 0;
				}

				if( $saldo/$fraccion >= ($r['Kadcfr']/$r['Kadcma']) ){
					$requiereSaldo = false;
					$mostrarArticulo = false;

					// $r['Kadsus'] = "off";	//Suspendo para que no se vea
				}
				else{
					$requiereSaldo = true;
					$perteneceRonda = true;	//Siempre debe pedir si es a necesidad y no hay saldo

					$r['Kadcpx'] = "$ronda-".( ceil( $r['Kadcfr']/$r['Kadcma']-$saldo/$fraccion ) )."-0";

					$r['Kadcdi'] = $r['Kaddis']+ceil( $r['Kadcfr']/$r['Kadcma'] );
				}
			}
			/********************************************************************************/










			/****************************************************************************
			 * Agosto 15 de 2012
			 ****************************************************************************/
			$regletas = Array();

			$fechorTrasladoRep = 0;

			if( !$cco['ayu'] ){
				
				//consulto la fecha y hora de traslado desde urgencia
				$horaTrasladoRep = consultarHoraTrasladoUrgencias( $conex, $bd, $pac['his'], $pac['ing'], $fecDispensacion );

				if( !$horaTrasladoRep ){
					$horaTrasladoRep = consultarHoraTrasladoUrgencias( $conex, $bd, $pac['his'], $pac['ing'], date( "Y-m-d", strtotime( $fecDispensacion )-24*3600 ) );

					if( $horaTrasladoRep ){
						$fechorTrasladoRep = strtotime( date( "Y-m-d", strtotime( $fecDispensacion )-24*3600 )." ".$horaTrasladoRep );
					}
				}
				else{
					$fechorTrasladoRep = strtotime( $fecDispensacion." ".$horaTrasladoRep );
				}
			}

			$corteDispensacionRep = $corteDispensacion;

			/******************************************************************************************
			 * Agosto 28 de 2012
			 *
			 * No permito crear una regleta mas alla de la hora de corte del día siguiente
			 ******************************************************************************************/
			$corteDispensacionRep = min( $corteDispensacionRep, $horaCorteDispensacion*3600+2*3600 );
			/******************************************************************************************/

			$canAplicada = 0;

			//Busco si hay articulos repetidos
			$hayRepetidos = false;
			for( $jj = 0; !$cco['ayu'] && $r2 = mysql_fetch_array( $res ); $jj++ ){

				if( $r2['Kadart'] == $r['Kadart'] ){

					$j++;

					if( $r2['Kadsus'] != 'on' ){
						
						//Si es a necesidad y tiene saldo no lo muestro
						//De lo contrario, le coloco frecuencia cada 24 horas y fecha y hora de inicio
						//la cambio para que muestro lo necesario
						$esANecesidadRep = esANecesidad( $bd, $conex, $r2['Kadcnd'] );
						
						if( $esCiclos24 )
							$esANecesidadRep = false;
						
						//Recalculo dosis máxima, esto por que se requiere que un medicamento se dispense hasta que cumpla el total de dosis a aplicar
						//y el sistema hasta ahora calculo hasta cumplir la dosis maxima por frecuencia
						if( trim( $r2[ 'Kaddma' ] ) != '' ){
							$r2[ 'Kaddma' ] = recalcularDosisMaxima( $conex, $bd, $pac['his'], $pac['ing'], $r2['Kadart'], $r2, $antHoras, $esANecesidadRep );
							
							//Agosto 2 de 2017
							if( $r2[ 'Kaddma' ] == 0 ){
								continue;
							}
						}
						
						$agregarRepetido = true;	//Indica si el medicamento es repetido

						$r['Kadcdi'] += $r2['Kadcdi'];
						$r['Kaddis'] += $r2['Kaddis'];

						//Consulto la cantidad aplicada para este medicamento, hasta maximo la hora de corte de dispensación del día siguiente
						$canAplicadaRep = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r2['Kadart'], max( strtotime( $r2[ 'Kadfin' ]." ".$r2[ 'Kadhin' ] ), time()-$antHoras*3600 ), min( time()+$corteDispensacion, strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 ), $r2[ 'Kadido' ], $fechorTrasladoRep );
						$canAplicada += $canAplicadaRep;

						/****************************************************************
						 * Septiembre 18 de 2013
						 ****************************************************************/
						if( tratarComoNoANecesidad( $r2['Kadcnd'] ) ){
							$r2['Kadcnd'] = '';
						}
						/****************************************************************/


						// if( $cco['urg'] ){
							// $esANecesidadRep = false;
							// $r['Kadcnd'] = '';
						// }

						if( $esANecesidadRep ){

							$fraccionRep = 1; //consultarFraccion( Array( "cod"=>$r2[ 'Kadart' ] ), $cco );
							$saldoRep = tieneSaldoEnPiso(  $bd, $conex, $ori, $pac['his'], $pac['ing'], $r2['Kadart'], $ccoSaldoAux );

							/***********************************************************************************************************************
							 * Enero 05 de 2017
							 * Ya no se valida que si tiene saldo un medicamento a necesidad no se dispensa
							 * solo se cambia la frecuencia a 24 horas y la hora de inicio para que no dispense más de la dosis debida
							 ***********************************************************************************************************************/
							if( false && $saldoRep/$fraccionRep >= ($r2['Kadcfr']/$r2['Kadcma']) ){
								$agregarRepetido = false;
							}
							else{
								//cambio la fecha y hora de inicio y la frecuencia para que salga lo suficiente para una dosis
								//Primero verifico si la fecha y hora de dispensacion sea mayor a la hora de traslado
								//esto por que no se puede dispensar nada en la ronda en que se recibe un paciente desde urgencias
								if( strtotime( date( "Y-m-d" )." ".gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 ) ) >= $fechorTrasladoRep ){
									$r2['Kadfin'] = date( "Y-m-d" );
									$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 );
								}
								else{
									$r2['Kadfin'] = date( "Y-m-d" );
									$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H", $fechorTrasladoRep + 2*3600)/2 )*2*3600 );
								}

								$r2['Perequ'] = 24;

								//Aumento dosis tanto como se halla dispensado las ultimas dosis para que siempre pueda dispensar
								$r2['Kadcfr'] += $canAplicadaRep*$r2['Kadcma'];
							}
						}

						if( $agregarRepetido ){
							//Con las funciones virtuales
							$fhFinMed = consultarFechaHoraFinalMedicamento( $horaCorteDispensacion, $fecDispensacion, $r2[ 'Kadfin' ], $r2[ 'Kadhin' ], $r2[ 'Kaddma' ], $r2[ 'Kaddia' ], $r2[ 'Perequ' ] );

							$regletas[] = Array( 0 			 => crearRegletaVirtual( strtotime( $fecDispensacion." 00:00:00" )-($antHoras-2)*3600, strtotime( $fecDispensacion." 00:00:00" )+22*3600+$corteDispensacionRep, strtotime( $r2['Kadfin']." ".$r2['Kadhin'] ), $r2[ 'Perequ' ], $r2[ 'Kadcfr' ]/$r2[ 'Kadcma' ], $fhFinMed, $fechorTrasladoRep ),
												 'id' 		 => $r2[ 'id' ],
												 'fecUltDis' => 0
												);
						}

						$hayRepetidos = true;
					}
				}
				else{
					break;
				}
			}

			if( true || $hayRepetidos ){

				if( $r2 ){
					@mysql_data_seek( $res, $j+1 );
				}

				if( $r['Kadsus'] != 'on' ){
					
					//Recalculo dosis máxima, esto por que se requiere que un medicamento se dispense hasta que cumpla el total de dosis a aplicar
					//y el sistema hasta ahora calculo hasta cumplir la dosis maxima por frecuencia
					if( trim( $r[ 'Kaddma' ] ) != '' ){
						
						if( $cco['ayu'] && $cco['asc'] ){

							$canAplRecalDma = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], strtotime( $r[ 'Kadfin' ]." ".$r[ 'Kadhin' ] ), time()+24*3600, $r[ 'Kadido' ], 0 );
							$canFracPorDma = $r[ 'Kadcfr' ]/$r['Kaddma'];
							
							$r[ 'Kaddma' ] = $r[ 'Kaddma' ] - floor( $canAplRecalDma/$canFracPorDma );
							
							// $r[ 'Perequ' ] = 24;
							
							// if( $r[ 'Kaddma' ]*1 > 0 )
								// $r[ 'Kadcfr' ] = $r[ 'Kadcfr' ]*$r[ 'Kaddma' ];
							
							// $r[ 'Kaddma' ] = '';
						}
						else{
							$r[ 'Kaddma' ] = recalcularDosisMaxima( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], $r, $antHoras, $esANecesidad );
						}
					}

					//Consulto la cantidad aplicada para este medicamento, hasta maximo la hora de corte de dispensación del día siguiente
					$canAplicadaRep = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], max( strtotime( $r[ 'Kadfin' ]." ".$r[ 'Kadhin' ] ), time()-$antHoras*3600 ), min( time()+$corteDispensacion, strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 ), $r[ 'Kadido' ], $fechorTrasladoRep );
					$canAplicada += $canAplicadaRep;
					
					/************************************************************************************************
					 * Si tuvo reemplazo se tiene en cuenta las aplicaciones anteriores del medicamento
					 ************************************************************************************************/
					if( $r['Kadaan'] != '' ){
						list( $aanArt, $aanFec ) = explode( ",", $r[ 'Kadaan' ] );
						
						//Solo se hace cambio de articulo por qué al hacer reemplazo quedan los mismos datos que el medicamento anterior
						if( $aanFec == date( "Y-m-d", time()-$antHoras*3600 ) ){
							$canAplicadaRepAan = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $aanArt, max( strtotime( $r[ 'Kadfin' ]." ".$r[ 'Kadhin' ] ), time()-$antHoras*3600 ), min( time()+$corteDispensacion, strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" )+24*3600 ), '', $fechorTrasladoRep );
							
							//Es la cantidad aplicada con el medicamento anterior, debo traducirlo al actual
							$aanFracc = consultarFraccionPorArticulo( $conex, $bd, $aanArt, '1050' );
							
							if( $aanFracc['unidad'] == $r['Kadcfr'] ){
								$canAplicadaRepAan = $canAplicadaRepAan*$aanFracc['fraccion']/$r[ 'Kadcma' ];
							}
							
							$canAplicada += $canAplicadaRepAan;
							$canAplicadaRep += $canAplicadaRepAan;
						}
					}
					/************************************************************************************************/

					$agregarRepetido = true;

					if( $esANecesidad ){

						/************************************************************************************************************************
						 * Enero 05 de 2017
						 * Ya no se valida que si tiene saldo un medicamento a necesidad no se dispensa
						 * solo se cambia la frecuencia a 24 horas y la hora de inicio para que no dispense más de la dosis debida
						 ************************************************************************************************************************/
						if( false && $saldo/$fraccion >= ($r['Kadcfr']/$r['Kadcma']) ){
							$agregarRepetido = false;
						}
						else{
							//cambio la fecha y hora de inicio y la frecuencia para que salgo lo suficiente para una dosis
							//Primero verifico si la fecha y hora de dispensacion sea mayor a la hora de traslado
							//esto por que no se puede dispensar nada en la ronda en que se recibe un paciente desde urgencias
							if( strtotime( date( "Y-m-d" )." ".gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 ) ) >= $fechorTrasladoRep ){
								$r['Kadfin'] = date( "Y-m-d" );
								$r['Kadhin'] = gmdate( "H:i:s", floor( date( "H" )/2 )*2*3600 );
							}
							else{
								$r2['Kadfin'] = date( "Y-m-d" );
								$r2['Kadhin'] = gmdate( "H:i:s", floor( date( "H", $fechorTrasladoRep+2*3600 )/2 )*2*3600 );
							}

							$r['Perequ'] = 24;

							//Aumento dosis tanto como se halla dispensado las ultimas dosis para que siempre pueda dispensar
							$r['Kadcfr'] += $canAplicadaRep*$r['Kadcma'];
						}
					}

					if( $agregarRepetido ){
						$fhFinMed = consultarFechaHoraFinalMedicamento( $horaCorteDispensacion, $fecDispensacion, $r[ 'Kadfin' ], $r[ 'Kadhin' ], $r[ 'Kaddma' ], $r[ 'Kaddia' ], $r[ 'Perequ' ] );

						$regletas[] = Array( 0    		 => crearRegletaVirtual( strtotime( $fecDispensacion." 00:00:00" )-($antHoras-2)*3600, strtotime( $fecDispensacion." 00:00:00" )+22*3600+$corteDispensacionRep,strtotime( $r['Kadfin']." ".$r['Kadhin'] ), $r[ 'Perequ' ], $r[ 'Kadcfr' ]/$r[ 'Kadcma' ], $fhFinMed, $fechorTrasladoRep ),
											 'id' 		 => $r[ 'id' ],
											 'fecUltDis' => 0
											 );
					}
				}
				else{
					$r['Kadsus'] = 'off';
				}

				//Consulto si hay saldo en piso
				$saldoPiso = tieneSaldoEnPiso( $bd, $conex, $ori, $pac['his'], $pac['ing'], $r['Kadart'], $ccoSaldoAux );
				
				//Como es de ayuda dx aplica automáticamente, por tanto el saldo es lo mismo que lo aplicado
				if( $cco['ayu'] ){
					//consulto con fecha a futuro(time()+24*3600) debido a que la aplicación es automática(se aplica al momento de dispensar el articulo) y se dispensa el articulo con antelación.
					if( $cco['asc'] ){
						$saldoPiso = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], $antHoras*3600, time()+24*3600, $r[ 'Kadido' ], 0 );
						$canAplicada = 0;
					}
				}

				//Si esta en 0 es que esta vacio
				if( !$saldoPiso ){
					$saldoPiso = 0;
				}

				// $fraccionArticulo = consultarFraccion( Array( "cod"=>$r[ 'Kadart' ] ), $cco );
				$fraccionArticulo = 1;

				//Consulto la cantidad aplicada de las ultimas dos rondas
				// $canAplicada = consultarCantidadAplicaciones( $conex, $bd, $pac['his'], $pac['ing'], $r['Kadart'], time()-4*3600, time(), false, $fechorTrasladoRep );

				$canDisVirtual = cantidadADispensarRegletasVirtual( $regletas, time()-$antHoras*3600 );

				//Carga una cantidad a la regletas
				cargarCantidadRegletasVirtuales( $regletas, $canDisVirtual+$canAplicada/$fraccionArticulo+round( $saldoPiso, 3 ) );

				//Combino las regletas
				$regletaCombinada = combinarRegletasVirtuales( $regletas, false );

				//la convierto a string
				$stRegletaCombinada = regletaVirtualToString( $regletaCombinada, strtotime( $fecDispensacion." 00:00:00") );

				$r[ 'Kadcpx' ] = $stRegletaCombinada;

				//verifico que halla algo que cargar para las rondas correspondientes
				$mostrarArticulo = $perteneceRonda = perteneceARondaPorRegletaVirtual( $regletaCombinada, $info[ $r[ 'Kadpro' ] ]['proximaRonda'] );

				actualizarRegletas( $conex, $bd, $regletas, strtotime( $fecDispensacion." 00:00:00" ) );
				
				//Si se va a mostrar el articulo busco pongo los id en una variable para que solo salgan los id que se pueden reemplazar
				if( $mostrarArticulo ){
					foreach( $regletas as $key => $value ){
						if( perteneceARondaPorRegletaVirtual( $value[0], $info[ $r[ 'Kadpro' ] ]['proximaRonda'] ) )
							$idsArtsConSaldo .= ",".$value['id'];
					}
				}
			}
			else{
				//devuelvo a la posicion en que inicio originalmente
				@mysql_data_seek( $res, $j+1 );
			}
			/****************************************************************************/









			//Se muestra el articulo si pertenece a la ronda y la hora actual esta entre la hora de corte de dispensacion y la ronda que se va a mostar
			$sindis = cantidadSinDispensarRondas( $r[ 'Kadcpx' ], $ronda, date( "Y-m-d" ) == $fecpxr );
			$can = 0;	//Septiembre 22 de 2011

			if( $mostrarArticulo && ( ( $perteneceRonda && time() >= $info[ $r[ 'Kadpro' ] ]['proximaRonda']-$corteDispensacion && time() <= $info[ $r[ 'Kadpro' ] ]['proximaRonda'] ) ) ){

				$can = cantidadADispensarRonda( $r['Kadcpx'], $ronda, date( "Y-m-d" ) == $fecpxr );

				if( $esANecesidad && $requiereSaldo ){

					//Busco una ronda que no este cargada o este parcialmente cargada y pido esa cantidad
					$auxCpx = consultarRondaNoCargada( $r['Kadcpx'], $can );
					$sindis = 0;

					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][0] += $can+$sindis;											//Cantidad a dispensar
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][1] += 0;

				}
				else{

					//Cantidad a dispensar
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][0] += $can+$sindis;
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][1] += cantidadDispensadaRonda( $r[ 'Kadcpx' ], $ronda, date( "Y-m-d" ) == $fecpxr );	//$r['Kaddis'];	//Cantidad dispensada
				}

				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][2] = $r['Kadart'];	//Codigo del articulo
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][3] = $r['Artcom'];	//Nombre comercial
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][4] = $r['Kadsus'];	//Articulo supendido
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][5] = $r['Artpos'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][6] += $r['Kadcdi'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][7] += $r['Kaddis']; //cantidadSinDispensarRondas( $r[ 'Kadcpx' ], "00:00:00" );	//Posicion	//Agosto 10 de 2011
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][8] = $r['Artuni'];  	//Diciembre 11 de 2012
			}
			elseif( $mostrarArticulo && ( $sindis > 0 ) ){

				if( $esANecesidad && $requiereSaldo ){

					//Busco una ronda que no este cargada o este parcialmente cargada y pido esa cantidad
					$auxCpx = consultarRondaNoCargada( $r['Kadcpx'], $can );
					$sindis = 0;

					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][0] += $can+$sindis;											//Cantidad a dispensar
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][1] += 0;
				}
				else{

					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][0] += $can+$sindis; //Cantidad a dispensar
					@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][1] += 0;//cantidadDispensadaRonda( $r[ 'Kadcpx' ], $ronda ); //$r['Kaddis'];	//Cantidad dispensada
				}

				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][2] = $r['Kadart'];	//Codigo del articulo
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][3] = $r['Artcom'];	//Nombre comercial
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][4] = $r['Kadsus'];	//Articulo supendido
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][5] = $r['Artpos'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][6] += $r['Kadcdi'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][7] += $r['Kaddis'];	//Posicion
				@$articulos[ $r['Kadart']."-".$r['Kadsus'] ][8] = $r['Artuni'];	//Diciembre 11 de 2012
			}
		}

		$i = 0;
		foreach( $articulos as $key => $value ){
			$arts[ $i ] = $value;
			$i++;
		}

		//se ordenan los articulos en el siguiente orden:
		//primero los articulos con saldo positivo
		//luego los de saldo 0 y que se encuentren suspendidos
		//Esto con el fin de manejar los errores correctamente
		for($i = 0, $j = 0, $tag = true, $k = 0; $i < 3 || $tag; $k++ ){

			if( !isset( $arts[ $k ] ) ){
				$arts[ $k ] = false;
			}

			$rows = $arts[ $k ];

			if( $rows ){
				if( $rows[0]-$rows[1] > 0 /*&& $rows[6] - $rows[7] > 0*/ && $rows[4] != 'on'  ){
					$numrows = true;
					$datos[$i][0]=$rows[3];
					$datos[$i][1]=ceil( $rows[0]-$rows[1] );//( ceil( $rows[0]-$rows[1] ) <= $rows[6] - $rows[7] ) ? ceil( $rows[0]-$rows[1] ) : $rows[6] - $rows[7];
					$datos[$i][2]=$rows[2];
					$datos[$i][3]=$rows[1];
					$datos[$i][4]=false;
					$datos[$i][5]=$rows[5];
					$datos[$i][6]=$rows[6];
					$datos[$i][7]=$rows[7];
					$datos[$i][8]=$rows[8];	//Diciembre 11 de 2012
					$datos[$i][9]=tieneSaldoEnPiso( $bd, $conex, $ori, $pac['his'], $pac['ing'], $rows[2], $ccoSaldoAux );
					$datos[$i][10]=consultarCTCAgotado( $conex, $bd, $pac['his'], $pac['ing'], $rows[2], $emp );
					$i++;
				}
				else{
					$vacios[$j][0]=$rows[3];
					$vacios[$j][1]=ceil( $rows[0]-$rows[1] );//( ceil( $rows[0]-$rows[1] ) <= $rows[6] - $rows[7] ) ? ceil( $rows[0]-$rows[1] ) : $rows[6] - $rows[7];
					$vacios[$j][2]=$rows[2];
					$vacios[$j][3]=$rows[1];
					$vacios[$j][5]=$rows[5];
					$vacios[$j][6]=$rows[6];
					$vacios[$j][7]=$rows[7];
					$vacios[$j][8]=$rows[8];	//Diciembre 11 de 2012
					$vacios[$j][9]=tieneSaldoEnPiso( $bd, $conex, $ori, $pac['his'], $pac['ing'], $rows[2], $ccoSaldoAux );
					$vacios[$j][10]=consultarCTCAgotado( $conex, $bd, $pac['his'], $pac['ing'], $rows[2], $emp );

					if($rows[4] != 'on'){
						$vacios[$j][4]=false;
					}
					else{
						$vacios[$j][4]=true;
					}
					$j++;
				}
			}
			else{
				$datos[$i][0]="&nbsp;";
				$datos[$i][1]="&nbsp;";
				$datos[$i][2]="&nbsp;";
				$datos[$i][3]="&nbsp;";
				$datos[$i][5]="&nbsp;";
				$datos[$i][6]="&nbsp;";
				$datos[$i][7]="&nbsp;";
				$datos[$i][8]="";	//Diciembre 11 de 2012
				$datos[$i][9]=0;
				$datos[$i][10]=false;
				$i++;
				$tag = false;
			}
		}

		for( $i = 0; $i < count( $vacios ); $i++ ){
			$datos[ count( $datos) ] = $vacios[$i];
		}

		//En caso de que no hallan articulos a dispensar
		//Se construye un mensaje para mostrar en el centro de la pantalla
		$datos['error'][0]=true;
		if ( !$numrows ){
			$datos[0][0]="NO HAY";
			$datos[1][0]="ARTICULOS A";
			$datos[2][0]="DISPENSAR";
			$datos['error'][0]=false;
		}
		
		return $datos;
	}
	else{

		$datos = array();		//Guarda los articulos con saldo positivos
		$vacios = array();		//Guarda los articulos con saldo en 0
		$numrows = false;

		$ori = 'SF';

		if( $cco['cod'] == $centraldemezclas ){
			$ori = 'CM';
		}

		//Buscando al paciente en Kardex electrónico por orden de prioridad y  estanteria
		//con origen en servicio farmaceutico (SF)
		$sql = "(SELECT
					sum(kadcdi), sum(kaddis), kadart, artcom, kadsus, artpos
		        FROM
					{$bd}_000054 a, {$bd}_000026 b, {$bd}_000053 c
		        WHERE
		        	kadhis='{$pac['his']}' AND
		        	kading='{$pac['ing']}' AND
		        	kadori='$ori' AND
		        	kadpri='on' AND
		        	a.kadfec = '$fecDispensacion' AND
		        	artcod = kadart AND
					karhis = kadhis AND
					karing = kading AND
					karcco = kadcco AND
					c.fecha_data = kadfec AND
					karcon = 'on' AND
					karest = 'on' AND
					kadare = 'on'
		        GROUP BY kadart
		        ORDER BY artubi, artcom ASC)
		        UNION
		        (SELECT
					sum(kadcdi), sum(kaddis), kadart, artcom, kadsus, artpos
		        FROM
					{$bd}_000054 a, {$bd}_000026 b, {$bd}_000053 c
		        WHERE
		        	kadhis='{$pac['his']}' AND
		        	kading='{$pac['ing']}' AND
		        	kadori='$ori' AND
		        	kadpri<>'on' AND
		        	a.kadfec = '$fecDispensacion' AND
		        	artcod = kadart AND
		        	karhis = kadhis AND
					karing = kading AND
					karcco = kadcco AND
					c.fecha_data = kadfec AND
					karcon = 'on' AND
					karest = 'on'  AND
					kadare = 'on'
		        GROUP BY kadart, kadsus
		        ORDER BY artubi, artcom ASC)";

		$res = mysql_query( $sql, $conex );

		//se ordenan los articulos en el siguiente orden:
		//primero los articulos con saldo positivo
		//luego los de saldo 0 y que se encuentren suspendidos
		//Esto con el fin de manejar los errores correctamente
		for($i = 0, $j = 0, $tag = true; $i < 3 || $tag; ){
			if( $rows = mysql_fetch_array( $res ) ){
				if( $rows[0]-$rows[1] > 0 && $rows[4] != 'on'  ){
					$numrows = true;
					$datos[$i][0]=$rows[3];
					$datos[$i][1]=$rows[0]-$rows[1];
					$datos[$i][2]=$rows[2];
					$datos[$i][3]=$rows[1];
					$datos[$i][4]=false;
					$datos[$i][5]=$rows[5];;
					$i++;
				}
				else{
					$vacios[$j][0]=$rows[3];
					$vacios[$j][1]=$rows[0]-$rows[1];
					$vacios[$j][2]=$rows[2];
					$vacios[$j][3]=$rows[1];
					$vacios[$j][5]=$rows[5];

					if($rows[4] != 'on'){
						$datos[$i][4]=false;
					}
					else{
						$datos[$i][4]=true;
					}
					$j++;
				}
			}
			else{
				$datos[$i][0]="&nbsp;";
				$datos[$i][1]="&nbsp;";
				$datos[$i][2]="&nbsp;";
				$datos[$i][3]="&nbsp;";
				$datos[$i][5]="&nbsp;";
				$i++;
				$tag = false;
			}

		}

		for( $i = 0; $i < count( $vacios ); $i++ ){
			$datos[ count( $datos) ] = $vacios[$i];
		}

		//En caso de que no hallan articulos a dispensar
		//Se construye un mensaje para mostrar en el centro de la pantalla
		$datos['error'][0]=true;
		if ( !$numrows ){
			$datos[0][0]="NO HAY";
			$datos[1][0]="ARTICULOS A";
			$datos[2][0]="DISPENSAR";
			$datos['error'][0]=false;
		}

		return $datos;
	}
}

/********************************************************************************************************
 * Registra en Kardex el articulo dispensado.  En caso de que se trate de una transaccion
 * de cargos la cantidad a dispensar aumenta en uno, y en caso contrario (devolucion), la cantidad dispensada
 * disminuye en 1
 *
 * @param 	array 	$art		Articulo a grabar al paciente
 * @param 	array 	$pac		Paciente al que se le va a grabar el articulo
 *
 * @return 	true				En caso de que la actualizacion de la cantidad dispensada sea exitoso
 * @return 	false				En caso de que la actualización de la cantidad despensada fracase
 *
 * Nota: Si el articulo es no necesario en el KE, entonces debe dejar grabar.
 * Si el articulo es no necesario en el KE hay que verificar si esta en el KE.
 * Si esta en el KE descontar.
 *
 * Modificaciones:
 *
 * Septiembre 16 de 2011. 	Se agrega parametro saldo, este indica cuanto el saldo del articulo en piso
 *							antes de grabar
 ********************************************************************************************************/
function registrarArticuloKE( $art, $pac, $trans = "C", &$idRegistro, $tras = true, $saldo, $num, $lin, $aplicado, &$ronda ){

	$art['fra'] = 1;	//La fracción debe ser siempre uno para este caso, ya que la fracción existe en la 59

	global $conex;
	global $bd;
	global $cco;
	global $centraldemezclas;
	global $fecDispensacion;
	global $usuario;

	global $tmpDispensacion;
	global $tempRonda;

	$ori = 'SF';

	if( $cco['cod'] == $centraldemezclas ){
		$ori = 'CM';
	}

	//Las reglas de validacion del KE son solo validos
	//para los centros de costos de traslado
	if( !$tras ){
		return false;
	}

	if( $trans == "C" ){

		//Amarro la grabacion para que no se pueda grabar el articulo por la misma persona que lo grabo
		$sqlid="SELECT
					max(a.id) as id
				FROM
					{$bd}_000054 a, {$bd}_000053 b
				WHERE
					kadart = '{$art['cod']}'
					AND kadcdi > kaddis+0
					AND kadfec = '$fecDispensacion'
					AND kadhis = '{$pac['his']}'
		       		AND kading = '{$pac['ing']}'
		       		AND kadori = '$ori'
		       		AND kadsus != 'on'
		       		AND karhis = kadhis
		       		AND karing = kading
		       		AND b.fecha_data = kadfec
		       		AND karcco = kadcco

		       		AND karest = 'on'
		       		AND kadare = 'on'
		       		AND kadhis NOT IN(
					       		SELECT
									kauhis
								FROM
									{$bd}_000055
								WHERE
									fecha_data = '".$fecDispensacion."'
									AND kaudes = CONCAT( '{$art['cod']}', ',', '$ori', ',',kadfin,',', kadhin )
									AND seguridad = 'A-$usuario'
									AND kaumen = 'Articulo aprobado'
									AND kauhis = kadhis
									AND kauing = kading
		       		)
				GROUP BY kadart";

		if( $cco['ayu'] ){
			
						//Se suspende la grabacion por la misma persona que lo grabo por tal motivo no se borra el query anterior
			$sqlid="SELECT
						a.id as id, kadcpx, kadcnd, kadcfr, kadcma
					FROM
						{$bd}_000054 a, {$bd}_000053 b
					WHERE
						kadart = '{$art['cod']}'
						AND kadfec = '$fecDispensacion'
						AND kadhis = '{$pac['his']}'
						AND kading = '{$pac['ing']}'
						AND kadori = '$ori'
						AND kadsus != 'on'
						AND karhis = kadhis
						AND karing = kading
						AND b.fecha_data = kadfec
						AND karcco = kadcco
						AND karest = 'on'
						AND kadcnd NOT IN ( SELECT concod FROM {$bd}_000042 WHERE contip = 'AN' )
					UNION ALL
					SELECT
						a.id as id, kadcpx, kadcnd, kadcfr, kadcma
					FROM
						{$bd}_000054 a, {$bd}_000053 b, {$bd}_000042 c
					WHERE
						kadart = '{$art['cod']}'
						AND kadfec = '$fecDispensacion'
						AND kadhis = '{$pac['his']}'
						AND kading = '{$pac['ing']}'
						AND kadori = '$ori'
						AND kadsus != 'on'
						AND karhis = kadhis
						AND karing = kading
						AND b.fecha_data = kadfec
						AND karcco = kadcco
						AND karest = 'on'
						AND kadcnd = concod
						AND contip = 'AN'
					";

		}
		else{
			//Se suspende la grabacion por la misma persona que lo grabo por tal motivo no se borra el query anterior
			$sqlid="SELECT
						a.id as id, kadcpx, kadcnd, kadcfr, kadcma
					FROM
						{$bd}_000054 a, {$bd}_000053 b
					WHERE
						kadart = '{$art['cod']}'
						AND kadfec = '$fecDispensacion'
						AND kadhis = '{$pac['his']}'
						AND kading = '{$pac['ing']}'
						AND kadori = '$ori'
						AND kadsus != 'on'
						AND karhis = kadhis
						AND karing = kading
						AND b.fecha_data = kadfec
						AND karcco = kadcco
						AND kadare = 'on'
						AND kadess != 'on'
						AND karest = 'on'
						AND kadcnd NOT IN ( SELECT concod FROM {$bd}_000042 WHERE contip = 'AN' )
					UNION ALL
					SELECT
						a.id as id, kadcpx, kadcnd, kadcfr, kadcma
					FROM
						{$bd}_000054 a, {$bd}_000053 b, {$bd}_000042 c
					WHERE
						kadart = '{$art['cod']}'
						AND kadfec = '$fecDispensacion'
						AND kadhis = '{$pac['his']}'
						AND kading = '{$pac['ing']}'
						AND kadori = '$ori'
						AND kadsus != 'on'
						AND karhis = kadhis
						AND karing = kading
						AND b.fecha_data = kadfec
						AND karcco = kadcco
						AND kadare = 'on'
						AND kadess != 'on'
						AND karest = 'on'
						AND kadcnd = concod
						AND contip = 'AN'
					";
			
		}

		$resid = mysql_query( $sqlid, $conex ) or die( mysql_errno()." - Error en el query $sqlid - ".mysql_error() );

		/************************************************************************************************************/



		$info = consultarInfoTipoArticulos( $conex, $bd );




		/**************************************************************************************************************
		 * Marzo 12 de 2012
		 **************************************************************************************************************/
		$auxProtocolo = consultarTipoProtocoloPorArticulo( $conex, $bd, $cco['cod'], $art['cod'] );
		/**************************************************************************************************************/

		if( !empty( $auxProtocolo ) ){
			$tempRonda = substr( $info[ $auxProtocolo ]['horaCaroteDispensacion'], 0, 2 );
		}

		/****************************************************************************************************
		 * Agosto 27 de 2012
		 ****************************************************************************************************/
		$disCco = consultarHoraDispensacionPorCco( $conex, $bd, $pac['sac'] );

		if( $disCco ){
			$tempRonda = $disCco;
		}
		/****************************************************************************************************/

		// echo "<br>........kkkk2222: ".$tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/$tmpDispensacion )*$tmpDispensacion );
		$tempRonda = gmdate( "H:i:s", ( intval( ( date( "H" ) )/$tmpDispensacion )*$tmpDispensacion )*3600 )+$tempRonda*3600;

		$rondaActual = $tempRonda;
		/************************************************************************************************************/

		//$id = '';
		for( $i = 0;$row = mysql_fetch_array( $resid ); $i++ ){

			/***************************************************************************************************
			 * Septiembre 13 de 2011
			 ***************************************************************************************************/
			//Si es a necesidad aumento la cantidad dispensada, para que siempre pueda pedir algo sin importar la situacion
			$esANecesidad = esANecesidad( $bd, $conex, $row[ 'kadcnd' ] );

			if( $esANecesidad ){
				$row[1] = "$rondaActual-".ceil( $row[ 'kadcfr' ]/$row[ 'kadcma' ] )."-0";

				$fraccion = $art['fra'];//consultarFraccion( Array( "cod"=>$art['cod'] ), $cco );
				//$saldo = tieneSaldoEnPiso(  $bd, $conex, $ori, $pac['his'], $pac['ing'], $art['cod'] );

			}
			/***************************************************************************************************/

			if( !$esANecesidad || ( $esANecesidad && $saldo/$fraccion < $row['kadcfr']/$row['kadcma'] ) ){

				$exp = explode( ",",$row[1] );

				foreach( $exp as $key => $value ){

					$b = explode( "-", $value );

					if( $b[0] == $rondaActual && $b[1] - $b[2] > 0 ){
						$id = $row[0];
						$cancdi = $b[1] - $b[2] + cantidadSinDispensarRondas( $row[1], $rondaActual );
					}
				}

				//Si id es vacio significa que no pertenece a la ronda y por tanto va a grabar una cantida que no fue dispensada con anterioridad
				if( ( empty($id) || $id == '') && cantidadSinDispensarRondas( $row[1], $rondaActual ) > 0 ){
					$id = $row[0];
					$cancdi = cantidadSinDispensarRondas( $row[1], $rondaActual );
				}
			}
		}

		$row[0] = @$id;

		if( empty($row[0]) ){
			$row[0] = "";
		}

		$idRegistro = $row[0];

		$sql = "SELECT
					id, kadcdi-kaddis, kadcpx, kadcfr, kadcma, kadcnd, kadido, kadart, kadcfr, kadufr, kadfin, kadhin
				FROM
					{$bd}_000054
				WHERE
					id = '{$row[0]}'";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		$row = mysql_fetch_array( $res );


		if( empty( $row['kadcpx'] ) ){
			//echo "<br>......::<pre>"; var_dump( obtenerVectorAplicacionMedicamentos( date( "Y-m-d" ), date( "Y-m-d" ), "02:00:00", 4 ) ); echo"</pre>";
		}
		elseif( !empty($row['kadcpx']) ){
			//Obtengo las horas de aplicacion del medicamento para el paciente
			$exp = explode( ",", $row['kadcpx'] );
			$row[1] = ceil( $cancdi );
		}

		if( $row[1] < ($art['can']/$art['fra']) ){

			$art['can'] = ($art['can']/$art['fra']-$row[1])*$art['fra'];
//			echo "........".$art['can'];
			//Creo nuevo array con con la cantidad aplicada por paciente
			$nuevoAplicaciones = "";
			$ultimaRonda = "";
			$fechaRonda = "0000-00-00";
			if( !empty($row['kadcpx']) ){

				$nuevoAplicaciones = crearAplicacionesCargadasPorHoras( $row['kadcpx'], $row[1] );

				if( $cco['ayu'] ){
					$ultimaRonda = consultarUltimaRondaDispensada( $nuevoAplicaciones, false, true );
				}
				else
					$ultimaRonda = consultarUltimaRondaDispensada( $nuevoAplicaciones, $rondaActual );

				list( $ultimaRonda, $fechaRonda ) = explode( "|", $ultimaRonda );

				if( $fechaRonda < 1 ){
					$fechaRonda = date( "Y-m-d", strtotime( $fecDispensacion ) + 3600*24 );
				}
				else{
					$fechaRonda = $fecDispensacion;
				}
			}

			/***************************************************************************************************
			 * Septiembre 13 de 2011
			 ***************************************************************************************************/
			//Si es a necesidad aumento la cantidad dispensada, para que siempre pueda pedir algo sin importar la situacion
			$esANecesidad = esANecesidad( $bd, $conex, $row[ 'kadcnd' ] );

			$cdiAdicional = 0;
			if( $esANecesidad ){
				$cdiAdicional = $row[1];
			}
			/***************************************************************************************************/

			//Actualizando registro con el articulo cargado hora y cantidad dispensada
			$sql = "UPDATE
						{$bd}_000054
			       	SET
			       		kaddis = kaddis+".$row[1].",
			       		kadhdi = '".date("H:i:s")."',
						kadron = '$ultimaRonda',
						kadfro = '$fechaRonda',
			       		kadcpx = '$nuevoAplicaciones',
						kadcdi = kadcdi+$cdiAdicional
			        WHERE

			       		kadart = '{$art['cod']}' AND
			       		kadhis = '{$pac['his']}' AND
			       		kading = '{$pac['ing']}' AND
			       		kadori = '$ori' AND
			       		kadfec = '$fecDispensacion' AND
			       		id = {$row[0]}";

			$res = mysql_query( $sql, $conex );

			if( $res && mysql_affected_rows() > 0 ){
				$ronda111 = '';
				return registrarArticuloKE( $art, $pac, $trans, $a, true, $saldo+$row[1], $num, $lin, $aplicado, $ronda111 );
			}
			else{
				return false;
			}
		}
		else{

			//Creo nuevo array con con la cantidad aplicada por paciente
			$nuevoAplicaciones = "";
			$ultimaRonda = "";
			$fechaRonda = "0000-00-00";
			if( !empty($row['kadcpx']) ){

				$nuevoAplicaciones = crearAplicacionesCargadasPorHoras( $row['kadcpx'], $art['can']/$art['fra'] );

				if( $cco['ayu'] ){
					$ultimaRonda = consultarUltimaRondaDispensada( $nuevoAplicaciones, false, true );
				}
				else
					$ultimaRonda = consultarUltimaRondaDispensada( $nuevoAplicaciones, $rondaActual );

				list( $ultimaRonda, $fechaRonda ) = explode( "|", $ultimaRonda );

				if( $fechaRonda < 1 ){
					$fechaRonda = date( "Y-m-d", strtotime( $fecDispensacion ) + 3600*24 );
				}
				else{
					$fechaRonda = $fecDispensacion;
				}
			}


			/***************************************************************************************************
			 * Septiembre 13 de 2011
			 ***************************************************************************************************/
			//Si es a necesidad aumento la cantidad dispensada, para que siempre pueda pedir algo sin importar la situacion
			$esANecesidad = esANecesidad( $bd, $conex, $row['kadcnd'] );

			$cdiAdicional = 0;
			if( $esANecesidad ){
				$cdiAdicional = $art['can']/$art['fra'];
			}
			/***************************************************************************************************/
			
			//guardo la ultima ronda dispensada en la variable ronda que viene por parametro
			$ronda = substr( $ultimaRonda, 0, 2 );

			//Actualizando registro con el articulo cargado hora y cantidad dispensada
			$sql = "UPDATE
						{$bd}_000054
			       	SET
			       		kaddis = kaddis+".$art['can']/$art['fra'].",
			       		kadhdi = '".date("H:i:s")."',
			       		kadcpx = '$nuevoAplicaciones',
						kadron = '$ultimaRonda',
						kadfro = '$fechaRonda',
						kadcdi = kadcdi+$cdiAdicional
			        WHERE

			       		kadart = '{$art['cod']}' AND
			       		kadhis = '{$pac['his']}' AND
			       		kading = '{$pac['ing']}' AND
			       		kadori = '$ori' AND
			       		kadsus != 'on' AND
			       		kadfec = '$fecDispensacion' AND
			       		id = {$row[0]}";

			$res = mysql_query( $sql, $conex );

			if( $res && mysql_affected_rows() > 0 ){

				/************************************************************************
				 * Abril 23 de 2012
				 ************************************************************************/
				if( !$aplicado ){	//Si fue aplicado no se hace el proceso

					global $contingencia;
					global $marcaContingencia;
					global $fhGrabacionContingencia;
					global $fhContingencia;
					global $procesoContingencia;

					if( !empty( $procesoContingencia ) && $procesoContingencia == 'on' ){
						actualizandoCargo( $conex, $bd, $num, $lin, $marcaContingencia );
					}
					elseif( !empty( $contingencia ) && $contingencia == 'on' ){

						if( time() > $fhGrabacionContingencia ){
							actualizandoCargo( $conex, $bd, $num, $lin, $marcaContingencia );
						}
					}
				}
				/************************************************************************/
				
				/************************************************************************************************************************************
				 * Octubre 7 de 2016
				 * Si el articulo fue aplicado se registra en la auditoría del kardex
				 ************************************************************************************************************************************/
				$descripcion= $row['kadart'].",".$row['kadfin'].",".$row['kadhin'].",".$row['kadcfr'].",".$row['kadufr'].",".$row['kadido']; 
				$fechaKardex= $fecDispensacion;
				$mensaje 	= "Articulo dispensado";
				$idOriginal = $row['kadido'];
				
				registrarAuditoriaKardexPDA( $conex, $bd, $pac['his'], $pac['ing'], $descripcion, $fechaKardex, $mensaje, $idOriginal, $usuario );
				/************************************************************************************************************************************/
				
				return true;
			}
			else{
				return false;
			}
		}
	}
	else{

		$dev2 = false;

		$sql = "SELECT
					id, kaddis, kadcpx
				FROM
					{$bd}_000054
				WHERE
					kaddis > 0 AND
					kadart = '{$art['cod']}' AND
					kadhis = '{$pac['his']}' AND
					kading = '{$pac['ing']}' AND
					kadfec = '".date("Y-m-d")."'
				GROUP BY
					kadart, kaddis
				ORDER BY
					id DESC
				";

		$res = mysql_query( $sql, $conex );

		if( $rows = mysql_fetch_array( $res ) ){

			if( $rows[1] < $art['can']/$art['fra'] ){

				$dev2 = true;
				$art1 = $art;
				$art1['can'] = ($art['can']/$art['fra'] - $rows[1])*$art['fra'];

				$art['can'] = $rows[1]*$art['fra'];
			}

			$cpx = crearAplicacionesDevueltasPorHoras( $rows[2], $art['can']/$art['fra'] );

			$ultimaRonda = consultarUltimaRondaDispensada( $cpx );

			//Si la cantidad a dispensar queda en 0
			//La hora de dispensacion debe quedar en 00:00:00
			if( $rows[1] == $art['can']/$art['fra'] ){

				$sql = "UPDATE
							{$bd}_000054
						SET
							kaddis = kaddis-".$art['can']/$art['fra'].",
							kadhdi = '00:00:00',
							kadron = '$ultimaRonda',
							kadcpx = '$cpx'
						WHERE
							id='{$rows[0]}'";
			}
			else{
				$sql = "UPDATE
							{$bd}_000054
						SET
							kaddis = kaddis-".$art['can']/$art['fra'].",
							kadron = '$ultimaRonda',
							kadcpx = '$cpx'
						WHERE
							id='{$rows[0]}'";
			}

			$result = mysql_query( $sql, $conex );

			if( $result && mysql_affected_rows() > 0 ){

				if( $dev2 ){
					$ronda111 = '';
					return registrarArticuloKE( $art1, $pac, "D", $a, true, $saldo, '', '', false, $ronda111 );
				}
				else{
					return true;
				}
			}
			else{
				return false;
			}
		}
	}
}

/**
 * Indica si el paciente se encuentra en Kardex Electronico o no
 *
 * @param array $pac	Paciente al que se le va a cargar los articulos
 * @return bool $ke		Devuelve true en caso de se Kardex electronico, en caso contrario false
 */

//Antes del 19 de agosto de 2009
function esKE( &$pac ){

	global $conex;
	global $bd;

	$ke = false;
	$pac['con'] = false;	//Confirmado
	$pac['keact']=false;	//KE Actualizado
	$pac['kegra']=false;	//KE Grabado
	$pac['ke']=false;		//Tiene KE

	if( empty($pac['ing']) ){
		return false;
	}

	$sql = "SELECT
				*
			FROM
				{$bd}_000053 b
	        WHERE
	        	karhis = '{$pac['his']}'
			";

	$restb53 = mysql_query( $sql, $conex );

	if( $row1 = mysql_fetch_array($restb53) ){

		$pac['ke']=true;
		$ke = true;
	}

	//Buscando si el KE electronico esta grabado y confirmado
	$sql = "SELECT
				Kargra, Karcon, Karing as Karing, fecha_data as Karfec
			FROM
				{$bd}_000053 b
	        WHERE
	        	karhis = '{$pac['his']}'
	        	AND ( karcon = 'on' )
	        	AND karest = 'on'
	        ORDER BY b.fecha_data DESC";

	$restb53 = mysql_query( $sql, $conex );

	if( $row1 = mysql_fetch_array($restb53) ){

		$pac['ke']=true;
		$ke = true;

		if( $row1['Karing'] == $pac['ing'] ){
			//Buscando si el KE esta grabado
			if( true || $row1['Kargra'] != 'off' ){
				$pac['kegra']=true;
			}
			else{
				$pac['kegra']=false;
			}

			//Buscando si el KE esta confirmado
			if( $row1['Karcon'] != 'off' ){
				$pac['con']=true;
			}
			else{
				$pac['con']=false;
			}

			$pac['keact']=true;
		}
	}

	//Busca kardex electronico para el paciente con la fecha mas reciente
	$sql = "SELECT
				Karcon, Kargra, a.kadfec as afd, b.Fecha_data as bfd
			FROM
				{$bd}_000054 a, {$bd}_000053 b
	        WHERE
	        	kadhis = '{$pac['his']}' AND
	        	kading = '{$pac['ing']}' AND
	        	kadhis = karhis AND
	        	kading = karing AND
	        	(
	        		(b.fecha_data >= '2009-08-13' AND
	        		b.hora_data > '06:59:59')
	        		OR
	        		(b.fecha_data > '2009-08-13')
	        	) AND
	        	a.Kadfec = b.Fecha_data AND
	        	a.kadcco = karcco AND
	        	karest = 'on'
	        ORDER BY a.Kadfec DESC, karcon DESC";

	$res = mysql_query( $sql, $conex );
	if( mysql_num_rows($res) > 0 ){

		$rows = mysql_fetch_array( $res );
		$pac['ke']=true;

		//Verificando el el paciente Tiene KE confirmado
		if( $rows['Karcon'] == "on" ){
			$pac['con'] = true;
		}
		else{
			$pac['con'] = false;
		}

		//Verificando que el KE esta grabado
		if( true || $rows['Kargra'] != "off" ){
			$pac['kegra']=true;
		}
		else{
			$pac['kegra']=false;
		}

		//Verificando que el KE esta actualizado
		if( true || ( $rows['afd'] == date("Y-m-d") && $rows['bfd'] == date("Y-m-d") ) ){
			$pac['keact']=true;
		}
		else{
			$pac['keact']=false;
		}

		$ke = true;
	}

	return $ke;
}

/**
 * Retorna la posicion en la que se encuentra el articulo en la lista de ARTICULOS
 * ASOCIADOS AL PACIENTE comenzando desde cero (0) sin contar las posiciones vacias.
 * En caso de no encontrarse en la lista, la funcion retorna -1
 *
 * @param $dat[][]		Datos de la lista
 * @param $art[]		Último articulo ingresado al sistema
 *
 * @return integer
 */
function enLista( $dat, $art ){

	$enlista = -1;
	$vacios = 0;

	if( !empty( $art ) ){
		for( $i = 0; $i < count( $dat )-1; $i++ ){
			if( strtoupper( $dat[$i][2] ) == strtoupper( $art['cod'] ) ){
				return $i;//-$vacios;
			}
			if ( $dat[$i][2] == "&nbsp;" ){
				$vacios++;
			}
		}
	}

	return $enlista;
}

/**
 * Consulta si el centro de costos seleccionado es urgencias, a traves del campo ccoUrg
 *
 * @param unknown_type $servicio
 */
function esUrgencias($servicio){

	global $conex;
	global $bd;

	$es = false;
	
	if( is_object( $servicio ) )
		return false;
	
	if($servicio != ''){
		
		$q = "SELECT
					Ccourg
				FROM
					movhos_000011
				WHERE
					Ccocod = '".$servicio."'
				";

		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		if($num>0)
		{
			$rs = mysql_fetch_array($err);

			($rs['Ccourg'] == 'on') ? $es = true : $es = false;
		}
	}
	
	return $es;
}

function pacienteIngresado($historiaClinica, $ingresoHistoriaClinica){
	global $conex;
	global $bd;

	$es = false;

	$q = "SELECT
				*
		 	FROM
		 		".$bd."_000018
			WHERE
				Ubihis = '".$historiaClinica."'
				AND Ubiing   = '".$ingresoHistoriaClinica."'
			";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}


function buscar_si_puede_grabar_a_otro_cco($ccoori,$ccodes,&$wexiste_rel){
	global $conex;
	global $bd;

	$es = false;

	$q = "SELECT * "
		." 	FROM ".$bd."_000058 "
		." WHERE Ccoori = '".$ccoori."'"
		."   AND Ccodes = '".$ccodes."'"
		."   AND Ccoest = 'on' " ;
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	  $wexiste_rel = 'on';
	 else
	    $wexiste_rel = 'off';
}


function pacienteConMovimientoHospitalario($historiaClinica, $ingresoHistoriaClinica){

	global $conex;
	global $bd;

	$es = false;

	$q = "SELECT
				*
		 	FROM
		 		".$bd."_000017
			WHERE
				Eyrhis = '".$historiaClinica."'
				AND Eyring = '".$ingresoHistoriaClinica."'
				AND Eyrest = 'on'";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}

function consultarCcoUrgencias(){
	global $bd;
	global $conex;

	$q = "SELECT
			Ccocod
		FROM
			".$bd."_000011
		WHERE
			Ccourg = 'on'; ";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = "";

	if($filas > 0){
		$fila = mysql_fetch_row($res);

		$cco = $fila[0];
	}
	return $cco;
}

include_once("movhos/validacion_hist.php");
include_once("movhos/fxValidacionArticulo.php");
include_once("movhos/registro_tablas.php");
include_once("movhos/otros.php");
include_once("root/comun.php");
include_once("movhos/cargosSF.inc.php");

include_once("ips/funciones_facturacionERP.php");

$wtitulo = "DISPENSACION ARTICULOS";
$wactualiz = "Diciembre 12 de 2018";

if( !$existeFacturacionERP )
	unset($facturacionErp);

if( !isset($facturacionErp) ){
encabezado($wtitulo, $wactualiz, 'clinica');

$serviciofarmaceutico = '1050';
$centraldemezclas = '1051';

$tempRonda = "";
$huboReemplazo = false;

echo "<center><table border='0' style='background-color: #FAFAFA;'>";

if ( $tipTrans == "C" || consultarhorario($horario,$emp))
{
if($tipTrans == "D")
{
	echo "<tr><td align=center class='errorTitulo'>DEVOLUCIÓN</td></tr>";
	$class='titulo5';
}
else
{
	$class='titulo2';
}
?>		<form name="carga" action="" method=post>		<?php

/*
 * 2008-09-22:: Modificacion cuando es de urgencias.
 */
$ccoUrgencias = consultarCcoUrgencias();


if( isset( $ultPac ) && !empty( $ultPac ) ){
	$ultPac = explode( "-", $ultPac );
	grabarUsuarioEncabezadoKardexPDA( $conex, $bd, $fecDis, $ultPac[0], $ultPac[1], '' );
}

if( !isset($cargados) ){
	$cargados = "off";	//Indica si se ha cargado algun articulo
}

$nka = false;
if( !isset( $cargarmmq ) ){
	$cargarmmq = 'off';
}

$user = trim( $user );
if (isset($user) and !isset($usuario)){
	// $usuario=substr($user,1);
	$usuario=explode("-",$user);
	$usuario=$usuario[count($usuario)-1];
}

$wcenpro = consultarAliasPorAplicacion( $conex, $emp, "cenmez" );
$wcliame = consultarAliasPorAplicacion( $conex, $emp, "cliame" );

//encabezao del kardex
//Se pone aca porque la variable usuario es seteado por el kardex automaticamente
$aux = @$usuario;
include_once("movhos/kardex.inc.php");

//Verifica si las funciones ejecutarLogUnix y procesoContingencia ya seejecutaron
//valores posibles: 0 no se ha ejecutado, caso contrario ya se ejecutrado
if( !isset( $ejecutarProcesosUnix ) )
	$ejecutarProcesosUnix = 0;

$usuario = $aux;

$tmpDispensacion = consultarTiempoDispensacion( $conex, $emp );

if( false && isset( $cbCrearPeticion ) && $cbCrearPeticion == 'on' ){	//Febrero 06 de 2015. Se desactiva la petición de camilleros a solicitud de Beatriz Orrego

	peticionCamillero( $cbCrearPeticion, $ccoCam, $hab, $solicita, $origen, $destino, $paciente  );

	echo "<tr>";
	echo "<td class='titulo2'>SE HA SOLICITADO CAMILLERO</td>";
	echo "</tr>";
}

if( isset( $cbCrearPeticion ) ){
	echo "<INPUT type='hidden' value='' name='cbCrearPeticion'>";
}

/************************************************************************************************
 * Abril 23 de 2012
 ************************************************************************************************/
contingencia( $conex );
/************************************************************************************************/

if(!isset($usuario) )
{
	echo "<tr><td class='titulo1'>CÓDIGO NOMINA: </font>";
	?>	<input type='text' cols='10' name='usuario'></td></tr>
	<script language="JAVASCRIPT" type="text/javascript">
	document.carga.usuario.focus();
	</script><!--<?php
	echo"<tr><td align=center class='".$class."'><input type='submit' value='ACEPTAR' id='ACEPTAR'></td></tr></form>";
}
elseif (!isset($pac['his']))
{
	if(!isset($ccoCod))
	{
		//El carnet de la clínica tiene un espacio, se recorta
		if(strlen($usuario) ==6 or strlen($usuario)==8)
		{
			$usuario=substr($usuario, 1);
		}

		//Busqueda del centro de costos al que pertenece el usuario.

		$q="SELECT Cc "
		."FROM 	root_000025 "
		."WHERE	Empleado = '".$usuario."' ";
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		if($num >0)
		{
			$row=mysql_fetch_array($err);
			$cco['cod']=$row['Cc'];
			$ok=getCco($cco,$tipTrans,$emp);
			echo "<input type='hidden' name ='cco[cod]' value='".$cco['cod']."' >";
		}
		else
		{
			/*No esta el CC en Matrix*/

			$pac['his']='0';
			$art['cod']="NO APLICA";
			$error['codInt']="0002";
			$cco['cod']='0000';
			if($err == "")
			{
				$error['codSis']=mysql_errno($conex);
				$error['descSis']=str_replace("'","*",mysql_error($conex));
			}
			else
			{
				$error['codSis']=$err;
				$error['descSis']=$err;
			}

			//registrarError("NO INFO", $cco, 0, 0, $pac, $art, $error, &$color, &$warning);
			registrarError('NO INFO',$cco,'NO INFO','0', '0',$pac,$art,$error, $color,$warning,$usuario);
			//Diciembre 27 de 2016
			$printError="<CENTER>EL USUARIO NO TIENE PERMISO PARA REALIZAR CARGOS<br>DEBE ESTAR REGISTRADO EN <B>RELACION DE CODIGOS DE NOMINA CON CC<B>";
			$ok=false;
		}
	}
	else
	{
		//Determinar que el centro de costos haya sido leido.
		$pos=strpos(strtoupper($ccoCod),"UN.");
		if($pos === 0)//Tiene que ser triple igual por que si no no funciona
		{
			$cco['cod']=substr($ccoCod,3);
			if(!getCco($cco,$tipTrans,$emp))
			{
				$printError="EL CENTRO DE COSTOS NO EXISTE O NO ESTA HABILITADO PARA REALIZAR CARGOS";
				$ok=false;
			}
			else
			{
				$cco['sel']=false;
				$ok=true;
			}
		}
		else
		{
			$printError="EL CENTRO DE COSTOS NO FUE LEIDO POR EL CODIGO DE BARRAS ADECUADO";
			$ok=false;
		}

	}

	if($ok)
	{
		if( $cco['sel'] )
		{
			echo "<tr><td class='titulo1'>USUARIO: ".$usuario;
			echo "<tr><td class='titulo1' ><b>CC: ";
			?>	<input type='password' size='7' name='ccoCod' onload=''>
			<script language="JAVASCRIPT" type="text/javascript">
			document.carga.ccoCod.focus();
			</script>
			<?php
			echo "</td></tr>";
		}
		else
		{
			echo "<tr><td class='tituloSup'>".$cco['cod']."-".$cco['nom']."</td></tr>";
			echo "<tr><td class='tituloSup'>USUARIO: ".$usuario."</b>";
			if($cco['asc'] AND $tipTrans == 'C')
			{
				if(isset($year1))
				{
					/**
					 * Revisar fecha de la ronda
					 */
					if(substr($month1,0,1)==0)
					$month=substr($month1,1,1);
					else
					$month=$month1;

					if(substr($day1,0,1)==0)
					$day=substr($day1,1,1);
					else
					$day=$day1;


					if(checkdate($month, $day, $year1))
					{
						$fecApl=$year1."-".$month1."-".$day1;
						$ronApl=$horRonApl.":".$minApl." - ".$merApl;
						echo "<input type='hidden' name='fecApl' value='".$fecApl."'>";
						echo "<input type='hidden' name='year1' value='".$year1."'>";
						echo "<input type='hidden' name='month1' value='".$month1."'>";
						echo "<input type='hidden' name='day1' value='".$day1."'>";
						echo "<input type='hidden' name='ronApl' value='".$ronApl."'>";
						echo "<tr><td class='tituloSup'>Aplicación ".$fecApl." ".str_replace(" ", "",$ronApl)."</b>";
						$preguntarHis=true;
						$preguntarFechaYRonda=false;
					}
					else
					{
						$ok=false;
						$preguntarFechaYRonda=true;
						$preguntarHis=false;
					}
				}
				else
				{
					$year1=date('Y');
					$month1=date('m');
					$day1=date('d');
					$preguntarFechaYRonda=true;
					$preguntarHis=false;
				}

				if($preguntarFechaYRonda )
				{
					?>-->
					<script language="JAVASCRIPT" type="text/javascript">
					window.onload=function(){ document.carga.submit() };
					</script>
					<?php


					echo "<tr>";
					echo "<td class='".$class."' ><b>Fecha: ";
					echo " <select name='year1' >";
					for($f=2004;$f<2051;$f++)
					{
						if($f == $year1)
						echo "<option selected>".$f."</option>";
						else
						echo "<option>".$f."</option>";
					}
					echo "</select> <select name='month1' >";
					for($f=1;$f<13;$f++)
					{
						if( $f == $month1)
						if($f < 10)
						echo "<option selected>0".$f."</option>";
						else
						echo "<option selected>".$f."</option>";
						else
						if($f < 10)
						echo "<option>0".$f."</option>";
						else
						echo "<option>".$f."</option>";
					}
					echo "</select> <select name='day1'>";
					for($f=1;$f<32;$f++) {
						if($f == $day1)
						if($f < 10)
						echo "<option selected>0".$f."</option>";
						else
						echo "<option selected>".$f."</option>";
						else
						if($f < 10)
						echo "<option>0".$f."</option>";
						else
						echo "<option>".$f."</option>";
					}
					echo "</select>";


					echo "<tr>";
					echo "<td class='".$class."' ><b>Ronda: <select name='horRonApl'>";
					$hora = (string)date("G");

					for($i=0;$i<24;$i++)
					{
						if($i==$hora)
						{
							// echo "<option selected>".$hora."</option>";
							echo "<option selected>".gmdate("H",intval($hora/2)*2*3600)."</option>";
						}
						// else
						// {
							// echo "<option>".$i."</option>";
						// }
					}
					echo "</select>";


					echo ":<select name='minApl'>";
					echo "<option selected>00</option>";
					// for($i=1;$i<10;$i++)
					// {
						// echo "<option>0".$i."</option>";
					// }
					// for($i=10;$i<61;$i++)
					// {
						// echo "<option>".$i."</option>";
					// }
					echo "</select>";
					echo "&nbsp;<select name='merApl'>";
					$meridiano = (string)date("A");
					echo "<option selected>".$meridiano."</option>";
					if($meridiano == "PM")
					{
						echo "<option>AM</option>";
					}
					else
					{
						echo "<option>PM</option>";
					}
					echo "</td></tr>";
					echo "</td></tr>";
				}
				echo "<input type='hidden' name='cco[cod]' value='".$cco['cod']."'>";
				echo "<input type='hidden' name='ccoCod' value='".$ccoCod."'>";
				echo "<input type='hidden' name='cco[asc]' value='1'>";
				//echo "<input type='hidden' name='cco[sel]' value=''>";
			}
			else
			{
				$preguntarHis=true;
			}

			if($preguntarHis)
			{
				//Interfaz ppal donde se solicita historia
				echo "<tr>";
				echo "<td class='".$class."' ><b>N° HISTORIA: ";
				?>-->	<input type='text' size='9' name='historia'>
				<script language="JAVASCRIPT" type="text/javascript">
				document.carga.historia.focus();
				</script><?php

				/*if(!$cco['hos'] and $tipTrans='C')
				{
				echo "</td></tr>";
				echo "<td class='titulo2' ><b>Carro: <input type='checkbox' name='car'></td></tr>";
				}*/
				echo "</td></tr>";
				echo "<td class='".$class."' ><b>Alta en Proceso: <input type='checkbox' name='alp'></td></tr>";
				echo "<td class='".$class."' ><b>Cargar MMQ: <input type='checkbox' name='cargarmmq'></td></tr>";
				echo "<input type='hidden' name='cco[cod]' value='".$cco['cod']."'>";
				echo "<INPUT type='hidden' name='fecDispensacion' value='".date("Y-m-d")."'>";
				echo "<INPUT type='hidden' name='fechorDispensacion' value='".date("Y-m-d H:i:s")."'>";
				/*FIN::::PANTALLA DE INGRESO DE nUMERO DE HISTORIA*/
			}
		}
		echo "<input type='hidden' name ='usuario' value='".$usuario."' >";
		echo"<tr><td  class='".$class."'><input type='submit' value='ACEPTAR' id='ACEPTAR'></td></tr></form>";
	}
	else
	{
		/*No esta el CC en Matrix*/

		$pac['his']='0';
		$art['cod']="NO APLICA";
		$error['codInt']="0002";
		$cco['cod']='0000';
		if(isset($err))
		{
			if($err == "")
			{
				$error['codSis']=mysql_errno($conex);
				$error['descSis']=str_replace("'","*",mysql_error($conex));
			}
			else
			{
				$error['codSis']=$err;
				$error['descSis']=$err;
			}
		}
		else
		{
			$error['codSis']='.';
			$error['descSis']='.';
		}
		//registrarError("NO INFO", $cco, 0, 0, $pac, $art, $error, &$color, &$warning);
		registrarError('NO INFO',$cco,'NO INFO','0', '0',$pac,$art,$error, $color,$warning,$usuario);
		echo "<tr><td class='errorTitulo'>".$printError;
		echo "</td></tr>";
		echo "<tr><td class='tituloSup'>";
		ECHO "<BR/><B><A HREF='cargos.php?tipTrans=".$tipTrans."&emp=".$emp."&bd=".$bd."'>Retornar</a>";
		echo "</td></tr></table>";
	}
}
else{

	// connectOdbc(&$conex_o, 'inventarios');

	if(!isset($cco['fap'])){
		/*Busqueda de los datos del Centro de Costos*/
		getCco($cco,$tipTrans, $emp);
	}

	if(isset($aprov))
	{
		$fuente=$cco['fap'];
		$aprov=true;
		if(isset($aprovEstadoAnterior) )
		{
			if($aprovEstadoAnterior == 0)
			$dronum = "";
		}
		else
		{
			$dronum = "";
		}
	}
	else
	{
		$fuente=$cco['fue'];
		$aprov=false;
		if(isset($aprovEstadoAnterior) )
		{
			if($aprovEstadoAnterior  == 1)
			$dronum = "";
		}
		else
		{
			$dronum = "";
		}
	}

	$cco['tras'] = esTraslado( $cco['cod'] );
	
	global $wbasedato;
	$wbasedato = $bd;
	// consultarAlergiasDiagnosticosAnteriores($pac['his'],&$alergiasAnteriores,&$diagnosticosAnteriores);
	$actualizandoTarifas = actualizandoTarifaMedicamentos();
	
	unset($wbasedato);
	
	if(!isset($pac['act']))
	{
		$pac['his']=ltrim(rtrim($pac['his']));
		$ind=0;

		// 2013-07-03
		if(trim($historia)=='' || $historia=='0')
			$ind = 1;

		while ($ind==0)
		{
			if (substr($pac['his'],0,1)=='0')
			{
				$pac['his']=substr($pac['his'],1);
			}
			else
			{
				$ind=1;
			}
		}
		$pac['act']=HistoriaMatrix($cco, $pac, $warning, $error);

		if($pac['act'] )
		{
			/*Si el nombre no esta setiado es por que no es historia Cero o una historia por MATRIX*/
			// $conex_f = odbc_connect('facturacion','','');
			// $pac['act']=ValidacionHistoriaUnix(&$pac, &$warning, &$error);

			// //2013-07-03
			// // Se comenta el siguiente cambio ya que este cambio solo debe aplicar para central de mezclas
			// //2013-06-26
			// // if( $pac['ptr'] && isset( $pac['san'] ) && $pac['san'] == $ccoUrgencias )
				// // $pac['sac'] = $pac['san'];

// //				$cco['tras'] = esTraslado( $cco['cod'] );
			// odbc_close($conex_f);
			
			$pac['act']=infoPacientePrima($pac,$emp);
			if( !$pac['act'] ){
				$warning= "NO EXISTE UN PACIENTE ACTIVO CON ESA HISTORIA EN EL SISTEMA, INTENTELO NUEVAMENTE!!!";
			}
			
			esKe( $pac );

			//Si no existe $cco['tras'] se le da valor con el llamado a la funcion pacienteKardexOrdenes, esto verifica si el paciente tiene ordenes activas
			//el dia de hoy, ademas de verificar si el paciente es de urgencias, si es asi la posicion $cco['tras'] sera true, pero si true el valor $cco['apl'] sera false, esto
			//con el fin de que el centro de costos no aplique automaticamente, sabiendo que el centro de costos esta configurado como de aplicacion automatica.
			if( !$cco['tras'] ){
				$cco['tras'] = ( pacienteKardexOrdenes( $conex, $bd, $pac[ 'his' ], $pac[ 'ing' ], date( "Y-m-d" ) ) && ( $cco['urg'] ) ) ? true: false;
				if( $cco['tras'] ){
					$cco['apl'] = false;
				}
			}

			/******************************************************************************************
			 * Grabo el usuario que esta intentando grabar por PDA
			 ******************************************************************************************/
			grabarUsuarioEncabezadoKardexPDA( $conex, $bd, date( "Y-m-d" ), $pac['his'], $pac['ing'], $usuario );
			/******************************************************************************************/

			/********************************************************************************
			 * Marzo 21 de 2012
			 * Recalculo las dosis necesarias para el kardex
			 ********************************************************************************/
			recalcularKardex( $conex, $bd, $pac['his'], $pac['ing'], date( "Y-m-d" ) );
			/********************************************************************************/

			/********************************************************************************
			 * Agosto 8 de 2011
			 ********************************************************************************/
			crearKardexAutomaticamente( $conex, $bd, $pac, date( "Y-m-d" ) );
			/********************************************************************************/
			
			/********************************************************************************
			 * Mayo 5 de 2018
			 ********************************************************************************/
			if( !$cco['urg'] )
				marcarArticulosNoEnviar( $conex, $bd, $emp, $pac['his'], $pac['ing'], date( "Y-m-d" ) );
			/********************************************************************************/


			if( irAOrdenes( $conex, $bd, '', $pac['sac'] ) == 'on' && pacienteKardexOrdenes( $conex, $bd, $pac[ 'his' ], $pac[ 'ing' ], date( "Y-m-d" ) ) ){
				$pac[ 'con' ] = true;
			}
			
			/**************************************************************************************************
			 * Enero 24 de 2017
			 * Se busca los articulos que son LQ o IC que esten como enviar y se dejan como no enviar
			 * Esto se hace por que son articulos que fueron creados en urgencias y ahora están en piso
			 * y dichos articulos son del stock.
			 **************************************************************************************************/
			if( $pac['sac'] != $ccoUrgencias ){
				$opciones = array(
				  'http'=>array(
					'method'=>"GET",
					'header'=>"Accept-language: en\r\n",
					'content'=>"user=".$user,
				  )
				);
				$contexto = stream_context_create($opciones);
				$url = 'http://'.$_SERVER['HTTP_HOST'];
				$url .= "/matrix/hce/procesos/ordenes.inc.php?wemp_pmla=".$emp."&his=".$pac[ 'his' ]."&ing=".$pac[ 'ing' ]."&consultaAjaxKardex=82";
				@$varGet = file_get_contents( $url, false, $contexto );
			}
			/**************************************************************************************************/

			/************************************************************************************************
			 * Marzo 18 de 2013
			 ************************************************************************************************/
			//Si está inactivo debo buscar la información del paciente
			if( true ){

				$pac2 = $pac;
				infoPacientePrima($pac2, $emp);

				//Consulto la fecha y hora en que fue dado de alta el pacient
				$sql = "SELECT *
						FROM
							{$bd}_000018
						WHERE
							ubihis = '{$pac2[ 'his' ]}'
							AND ubiing = '{$pac2[ 'ing' ]}'
						";

				$resEgresoUrgencias = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

				if( $rowsEgresoUrgencias = mysql_fetch_array( $resEgresoUrgencias ) ){

					if( !$pac['act'] ){
						$pac2['ald'] = $rowsEgresoUrgencias[ 'Ubiald' ] == "on" ? true: false;
						$pac2['alp'] = $rowsEgresoUrgencias[ 'Ubialp' ] == "on" ? true: false;
						$pac2['ptr'] = $rowsEgresoUrgencias[ 'Ubiptr' ] == "on" ? true: false;
						$pac2['sac'] = $rowsEgresoUrgencias[ 'Ubisac' ];
					}

					//Si el paciente tiene alta definitiva
					if( $pac2['ald'] ){

						//Si el último cco en que estuvo el paciente fue urgencias
						if( esUrgencias( $pac2['sac'] ) ){

							//Busco si le han dado alta definitiva en menos de x horas
							//segun parametro en root_000051
							$egresoUrgencias = consultarValorPorAplicacion( $conex, $emp, 'tiempoEgresoUrgencia' )*3600;

							//Si fue dado de alta en menos de x horas activo el paciente
							if( time() <= strtotime( $rowsEgresoUrgencias[ 'Ubifad' ]." ".$rowsEgresoUrgencias[ 'Ubihad' ] )+$egresoUrgencias ){
								$pac2['act'] = true;
								$pac2['ald'] = false;
								$pac2['alp'] = false;

								$pac = $pac2;	//igualo para que tenga todos los datos del paciente.
							}
						}
					}
				}
			}
			/************************************************************************************************/

			if( empty($pac['mue']) ){
				$pac['mue'] = false;
			}

			if($pac['act'])
			{
				if( !generandoInventarios() ){
					if(!$pac['mue'])
					{
						if( ($pac['con'] && $pac['ke']) || !$pac['ke'] || !$cco['tras'] || $tipTrans != "C" || $cargarmmq == "on" )
						{
							if( ($pac['kegra'] && $pac['ke']) || !$pac['ke'] || !$cco['tras'] || $tipTrans != "C"  || $cargarmmq == "on" )
							{
								if( ($pac['keact'] && $pac['ke'] ) || !$pac['ke'] || !$cco['tras'] || $tipTrans != "C"  || $cargarmmq == "on" )
								{
									/**
									 * Si el paciente esta de alta la función retornara true, lo que significa que hay que inactivar el paciente
									 * pues si esta de alta no le pueden cargar artículos.
									 */
									if(!$pac['ald'])
									{
										/*
										 * 2008-09-22:: Si el paciente esta en urgencias, tiene movimiento hospitalario (registro activo en la tabla 17) y
										 * además se encuentra ingresado en la 18, se permite el proceso de cargos normal.
										 * Se usa flag "entrar" para permitir que la aplicación ingrese bajo esta nueva condicion.
										 */
										$entrar = false;
										//==================================================================================================
										//Octbure 5 de 2017. No se le puede dispensar articulos a pacientes en traslado
										//A la fecha de publicación, solo está en proceso de traslado si se le ha hecho entrega de paciente.
										//==================================================================================================
										// if(isset($pac['ing'])){

											// // 2013-06-26
											// // if(($pac['sac'] == $ccoUrgencias || $pac['san'] == $ccoUrgencias) && pacienteIngresado($pac['his'],$pac['ing'])){

											// //2013-07-03
											// // Se devuelve el cambio anterior ya que este cambio solo debe aplicar para central de mezclas
											// if(($pac['sac'] == $ccoUrgencias || $pac['san'] == $ccoUrgencias) && pacienteConMovimientoHospitalario($pac['his'],$pac['ing']) && pacienteIngresado($pac['his'],$pac['ing'])){
												// $entrar = true;
											// }
										// }

										if(!$pac['ptr'] or $entrar)
										{
											//inicio de la modificación 2007-06-14
											if($pac['alp'])
											{
												$rsAlp['id'] = 0;

												//busco si ya si tiro la factura
												$q1 = "SELECT * "
												."       FROM ".$bd."_000022 "
												."      WHERE Cuehis = '".$pac['his']."' "
												."        AND Cueing = '".$pac['ing']."' "
												."        AND Cuegen = 'on' ";
												$err1=mysql_query($q1,$conex);

												echo mysql_error();
												$gene=mysql_num_rows($err1);

												//El paciente esta en proceso de Alta
												if(!isset($alp))
												{
													//El usuario no selecciono el check box de alta en proceso.  No puede realizar cargos.
													$pac['act']=false;
													$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." ESTA EN PROCESO DE ALTA";
													if($gene>0)
													{
														$warning = $warning. " Y LA FACTURA DEL PACIENTE YA HA SIDO GENERADA ";
													}
													$error['codInt']='0011';
													$error['codSis']=".";
													$error['descSis']=".";
													$error['clas']="#ff0000";
													$error['ok']="NO PASO, PACIENTE EN PROCESO DE ALTA ";
												}
												else
												{
													if($gene>0)
													{
														$factura='LA FACTURA DEL PACIENTE YA HA SIDO GENEREDA. FAVOR COMUNICARSE CON EL FACTURADOR';
													}
												}

												$rsAlp = mysql_fetch_array( $err1 );

												//Msanchez-> 2008-10-22:: Si la cuenta en proceso de facturación, no puede grabar cargos.
												$q1 = "SELECT
															*
														FROM
															".$bd."_000022
														WHERE
															Cuehis = '".$pac['his']."'
															AND Cueing = '".$pac['ing']."'
															AND Cuecok = 'on'
															AND Cuegen = 'off' ";

												$err1=mysql_query($q1,$conex);

												echo mysql_error();
												$cuentasEnProcesoFacturacion = mysql_num_rows($err1);

												if( $cuentasEnProcesoFacturacion > 0 && !isset($alp) )
												{
													$pac['act']=false;
													$warning = "LA CUENTA SE ENCUENTRA EN PROCESO DE FACTURACION. FAVOR COMUNICARSE CON EL FACTURADOR";
													$error['codInt']='0007';
													$error['codSis']=".";
													$error['descSis']=".";
													$error['clas']="#ff0000";
													$error['ok']="NO PASO, PACIENTE EN PROCESO DE FACTURACION";
												}
												elseif( $cuentasEnProcesoFacturacion > 0 ){
													$factura = "LA CUENTA SE ENCUENTRA EN PROCESO DE FACTURACION, COMUNIQUESE CON EL FACTURADOR";
												}
												//2008-10-22:: Fin cambio

												if( empty( $rsAlp ) ){
													$rsAlp = mysql_fetch_array( $err1 );
												}
											}

											if($pac['act'])
											{
												if($cco['hos'] )
												{
													if($cco['cod'] != $pac['sac'])
													{
														buscar_si_puede_grabar_a_otro_cco($cco['cod'],$pac['sac'],$wexiste_rel);  //Mando el cco origen y el cco del paciente (destino) y busco en la tabla

														if ($wexiste_rel=="off")
														{
															//El usuario no selecciono el check box de alta en proceso.  No puede realizar cargos.
															$pac['act']=false;
															$warning = "EL CENTRO DE COSTOS ".$cco['cod']." ES HOSPITALARIO<br> Y POR LO TANTO NO PUEDE HACERLE CARGOS O DEVOLUCIONES AL PACIENTE ".$pac['his']."<br>PUES ESTE SE ENCUENTRA EN EL CENTRO DE COSTOS ".$pac['sac'];
															$error['codInt']='0012';
															$error['codSis']=".";
															$error['descSis']=".";
															$error['clas']="#ff0000";
															$error['ok']="NO PASO, PACIENTE EN PROCESO DE ALTA ";
														}
													}
												}

												//Diciembre 16 de 2014
												if($cco['urg'] )
												{
													if($cco['cod'] != $pac['sac'])
													{
														buscar_si_puede_grabar_a_otro_cco($cco['cod'],$pac['sac'],$wexiste_rel);  //Mando el cco origen y el cco del paciente (destino) y busco en la tabla

														if ($wexiste_rel=="off")
														{
															//El usuario no selecciono el check box de alta en proceso.  No puede realizar cargos.
															$pac['act']=false;
															$warning = "EL CENTRO DE COSTOS ".$cco['cod']." ES DE URGENCIAS<br> Y POR LO TANTO NO PUEDE HACERLE CARGOS O DEVOLUCIONES AL PACIENTE ".$pac['his']."<br>PUES ESTE SE ENCUENTRA EN EL CENTRO DE COSTOS ".$pac['sac'];
															$error['codInt']='0012';
															$error['codSis']=".";
															$error['descSis']=".";
															$error['clas']="#ff0000";
															$error['ok']="NO PASO, PACIENTE EN PROCESO DE ALTA ";
														}
													}
												}
											}
										}
										else
										{
											if( $cco['urg'] ){
												$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." <BR> ESTA EN PROCESO DE TRASLADO Y SE LE HA<BR>HECHO ENTREGA DEL PACIENTE AL PISO,<BR>POR LO TANTO <BR> NO SE LE PUEDE NI CARGAR NI DEVOLVER <BR> NINGUN ARTÍCULO!!!";
											}
											else{
												$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." <BR> ESTA EN PROCESO DE TRASLADO, POR LO TANTO <BR> NO SE LE PUEDE NI CARGAR NI DEVOLVER <BR> NINGUN ARTÍCULO!!!";
											}
											$error['codInt']='0010';
											$error['codSis']=".";
											$error['descSis']=".";
											$error['clas']="#ff0000";
											$error['ok']="NO PASO, PACIENTE EN TRASLADO";
											$pac['act']=false;
										}
									}
									else
									{
										$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." FUE DADO DE ALTA DE LA INSTITUCIÓN!!!";
										$error['codInt']='0009';
										$error['codSis']=".";
										$error['descSis']=".";
										$error['clas']="#ff0000";
										$error['ok']="NO PASO, PACIENTE DE ALTA";
										$pac['act']=false;
									}
								}else{
									$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." NO TIENE KARDEX ELECTRONICO ACTUALIZADO!!!";
									$error['codInt']='0015';
									$error['codSis']=".";
									$error['descSis']=".";
									$error['clas']="#ff0000";
									$pac['act']=false;
								}
							}
							else{
								$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." NO TIENE KARDEX ELECTRONICO GRABADO!!!";
								$error['codInt']='0016';
								$error['codSis']=".";
								$error['descSis']=".";
								$error['clas']="#ff0000";
								$pac['act']=false;
							}
						}
						else{
							if( !$pac['con'] ){
								$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." NO SE LE HA CONFIRMADO EL KARDEX ELECTRONICO!!!";
								$error['codInt']='0014';
								$error['codSis']=".";
								$error['descSis']=".";
								$error['clas']="#ff0000";
								$pac['act']=false;
							}
						}
					}
					else
					{
						$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." FALLECIÓ!!!";
						$error['codInt']='0013';
						$error['codSis']=".";
						$error['descSis']=".";
						$error['clas']="#ff0000";
						$pac['act']=false;
					}
				}
				else{

					$fcFinalInventarios = "";

					if( date("Y-m-d") == date("Y-m-t") ){
						$fcFinalInventarios = date("Y-m-d", mktime(0,0,0,date("m"), date("d")+1, date("Y") ) );
						$fcFinalInventarios .= " A LAS 00:05:00";
					}
					else{
						$fcFinalInventarios = date("Y-m-d")." A LAS 00:05:00";
					}

					$warning = "SE ESTAN GENERANDO INVENTARIOS, INTENTE A PARTIR DEL ".$fcFinalInventarios;
					$error['codInt']='0017';
					$error['codSis']=".";
					$error['descSis']=".";
					$error['clas']="#ff0000";
					$pac['act']=false;
				}
			}
		}
		else
		{
			//2007-07-16
			$classHis="titulo1";
		}

		if($pac['act'])
		{
			/**
			 * El paciente esta activo
			 * Es necesario saber si el centro de costos en donde esta matriculado el paciente aplica AUTOMÁTICAMENTE
			 */
			$ccoPac['cod']=$pac['sac'];
			if(getCco($ccoPac, $tipTrans, $emp))
			{
				if($ccoPac['apl'])
				{
					$cco['apl']=true;

					$cco['apl']=( irAOrdenes( $conex, $bd, '', $ccoPac['cod'] ) == 'on' && pacienteKardexOrdenes( $conex, $bd, $pac[ 'his' ], $pac[ 'ing' ], date( "Y-m-d" ) ) && ( $cco['urg'] ) ) ? false: true;
				}

				// 2013-06-11
				// Se agrega la condición !$pac['ptr'] Esta condicion hace que si el paciente está en proceso de traslado
				// y es de urgencias, se permite hacer cargos desde otros centros de costo
				//if(!$pac['ptr'] and $ccoPac['urg'] and $cco['cod']!=$ccoPac['cod'])

				// Se devuelve el cambio anterior ya que este cambio solo debe aplicar para central de mezclas
				if(!$cco['ayu'] && $ccoPac['urg'] and $cco['cod']!=$ccoPac['cod'])
				{
					$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." NO HA SIDO TRASLADADO DEL SERVICIO DE URGENCIAS!!!";
					$error['codInt']='0010';
					$error['codSis']=".";
					$error['descSis']=".";
					$error['clas']="#ff0000";
					$error['ok']="NO PASO, PACIENTE EN TRASLADO";
					$pac['act']=false;
				}
			}
			else
			{
				$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." NO HA SIDO TRASLADADO DEL SERVICIO DE ADMISIONES!!!";
				$error['codInt']='0010';
				$error['codSis']=".";
				$error['descSis']=".";
				$error['clas']="#ff0000";
				$error['ok']="NO PASO, PACIENTE EN TRASLADO";
				$pac['act']=false;
			}
		}
	}

	//Se verifica si están actualizando facturas
	if( $actualizandoTarifas->actualizando ){
		$warning = $actualizandoTarifas->USU." ESTA ACTUALIZANDO TARIFAS. INTENTELO MAS TARDE.";
		$error['codInt']='0010';
		$error['codSis']=".";
		$error['descSis']=".";
		$error['clas']="#ff0000";
		$error['ok']="ACTUALIZANDO TARIFAS";
		$pac['act']=false;
	}
	
	/************************************************************************************************************************
	 * Si el cco es de ayuda dx lo marco como si fuera de traslado para que muestre la lista de articulos a dispensar
	 ************************************************************************************************************************/
	if( $cco['ayu'] ){
		$cco['tras'] = true;
		if( !$cco['asc'] ){
			$cco['asc'] = false;
			$cco['apl'] = false;
		}
	}
	
	if( $pac['act'] ){
		
		if ($cco['apl'])
		{
			$color="titulo4";
		}
		else
		{
			$color="titulo2";
		}

		$warning="";
		if( !isset($artsReemplazados ) )
			$artsReemplazados = "";
		
		
		if(isset($art['cod']))
		{
			// connectOdbc(&$conex_o, 'inventarios');
			
			if( true )
			{	
				
				// if( true || esUrgencias( $cco['cod'] ) ){
				if( $tipTrans == 'C' && !isset( $art[can] ) ){
					
					$art['cod']=BARCOD($art['cod']);
					ArticuloCba($art);
				
					//Si en la session ya se reemplazo una vez no se pregunta de nuevo
					$yaFueReemplazado = preg_match( "/\b".$art[ 'cod']."\b/i", $artsReemplazados );	//Es 0 si no se encuentra en la lista
					
					if( $yaFueReemplazado === 0 ){
					
						//Miro si el articulo no está en la lista para dispensar
						$estaParaDispensar = preg_match( "/\b".$art[ 'cod']."\b/i", $artEnListaSaldo );	//Es 0 si no se encuentra en la lista
					
						if( $estaParaDispensar === 0 ){	//Si está en la lista para dispensar no se hace reemplazo
							//Consulto código equivalente
							// $artEq = consultarArticuloEquivalente( $cco[ 'cod' ], $art[ 'cod' ] );
							$artEq = consultarArticuloOriginal( $cco[ 'cod' ], $art[ 'cod' ] );

							if( !empty($artEq) ){

								if( !tieneSaldoEnPiso( $wbasedato, $conex, 'SF', $pac['his'], $pac['ing'], $artEq[ 'Areces' ] ) ){
								
									$condicionWhereIdo = "";
									$hayQueReemplazar = false;
									
									if( isset( $idoReemplazo ) ){
										$hayQueReemplazar = true;
										if( !empty( $idoReemplazo ) )
											$condicionWhereIdo = " AND kadido IN (".$idoReemplazo.")";
										else
											$condicionWhereIdo = " AND kadido IN ('')";
									}
							
									if( $cco['ayu'] )
									{
										$sql = "SELECT
													a.*
												FROM
													{$bd}_000054 a,
													{$bd}_000026 b,
													{$bd}_000053 c,
													{$bd}_000043 d
												WHERE
													kadhis='".$pac['his']."' AND
													kading='".$pac['ing']."' AND
													kadori='SF' AND
													a.kadfec = '".$fecDispensacion."' AND
													kadart = '".$artEq[ 'Areces' ]."' AND 
													artcod = kadart AND
													karhis = kadhis AND
													karing = kading AND
													karcco = kadcco AND
													c.fecha_data = kadfec AND
													karest = 'on' AND
													kadest = 'on' AND
													kadper = percod
													AND a.id IN( ''$idsArtsConSaldo )
													$condicionWhereIdo
													AND kadhis IN(
														SELECT Ekxhis
														  FROM {$bd}_000208 e
														 WHERE Ekxhis = a.Kadhis
														   AND Ekxing = a.Kading
														   AND Ekxart = a.Kadart
														   AND Ekxido = a.Kadido
														   AND Ekxfec = a.Kadfec
														   AND Ekxayu = '".$cco['cod']."'
													)";
									}
									else
									{	
										$sql = "SELECT *
												  FROM ".$wbasedato."_000054 A, ".$wbasedato."_000059 B, ".$wbasedato."_000026 C
												 WHERE
													kadhis = '".$pac['his']."'
													AND kading = '".$pac['ing']."'
													AND kadfec = '".$fecDispensacion."'
													AND kadart = '".$artEq[ 'Areces' ]."'
													AND defart = kadart
													AND defest = 'on'
													AND defcco = '1050'
													AND kadsus != 'on'
													AND kadare = 'on'
													AND kadest = 'on'
													AND kadess = 'off'
													AND artcod = defart
													AND artest = 'on'
													AND A.id IN( ''$idsArtsConSaldo )
													$condicionWhereIdo
												";
									}
									
									$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
									$num = mysql_num_rows($res);
									if( !$hayQueReemplazar && $num > 1 ){
										pintarSeleccionArticulosReemplazo( $conex, $wbasedato, $art[ 'cod' ], $res );
									}
									else if( $num > 0 ){

										while( $rows = mysql_fetch_array($res) ){
											//																									 $dtto,$obs,$unidadDosis,$formaFarm,$origen,$fechaInicio,$horaInicio
											$reemplazo = reemplazarArticuloDetallePerfil($bd, $pac['his'], $pac['ing'], $fecDispensacion,$artEq[ 'Areces' ],$art[ 'cod' ], $rows[ 'Kaddia' ], $rows[ 'Kadobs' ],$rows[ 'Deffru' ],'','SF', $rows[ 'Kadfin' ], $rows[ 'Kadhin' ],$usuario, $rows[ 'Kadido' ], $cco['ayu'] );
											$huboReemplazo = true;
											if( $reemplazo ){
												
												list( $codigoRespuestaReemplazo ) =  explode( "|", $reemplazo );
												
												if( $codigoRespuestaReemplazo == 1 || $codigoRespuestaReemplazo == 2 || $codigoRespuestaReemplazo == 8  ){
											
													if( empty($artsReemplazados) ){
														$artsReemplazados = $art[ 'cod' ];
													}
													else{
														$artsReemplazados .= "-".$art[ 'cod' ];
													}
												}
												else{
													
													?>
														<script>
															msgReemplazo( '<?=$reemplazo?>' );
														</script>
													<?php
													break;
												}
											}
										}
									}
								}
							}
						}
					}
				}
				echo "<INPUT type='hidden' name='artsReemplazados' id='artsReemplazados' value='".$artsReemplazados."'>";


				/*Validación Artículo*/
				/*Buscar si existe el articulo y el codigo es el del proveedor*/
				if(!isset($art['ini'])) {
					$art['ini']=$art['cod'];
				}
				$art['cod']=BARCOD($art['cod']);

				if(!isset($artValido)) {
					/*Si esta setiado es por que es una cantidad variable*/
					ArticuloCba($art);
					ArticulosEspeciales($cco, $art);
					$artValido = ArticuloExiste($art, $error);
				}

				if($artValido)
				{

					//Consultando las fracciones del articulo
					//Esto para determinara valor unitario por articulo
					$art['fra'] = consultarFraccion( $art, $cco );

					/*Si esta set artgen implica que el codigo existe en el sistema Ademas:
					isset $pacnom and $pacnom != 0 osea que la historia esta activa
					$cc != "" es decir el centro de costos existe*/
					if(!$art['cva'])
					{
						if($art['can'] <= $art['max'])
						{
							if( $art['can'] > 0 )
							{
								// $tarSal=TarifaSaldo($art, $cco,$tipTrans,$aprov, &$error);
								$tarSal=TarifaSaldoMatrix( $art, $cco, $tipTrans, $aprov, $error );	//Se cambia TarifaSaldo por TarifaSaldoMatrix
								if(!$tarSal) {
									$artValido=false;
								}

								if($tipTrans == "D" and $artValido)
								{
									/*Validación de Devolucion*/
									$artValido=validacionDevolucion($cco, $pac, $art, $aprov,false, $error);

									/************************************************************************************************************************
									 * Febrero 25 de 2015 Se valida para cco de costos que sean diferentes a urgenicas y traslado
									 * Junio 27 de 2012
									 ************************************************************************************************************************/
									//Si no hay saldo suficiente para devolver y no es de un cco de aplicacion automatica
									if( !$artValido && !$cco['apl'] && ( esMMQ( $art['cod'] )  || ( !$cco[ 'urg' ] && !$cco[ 'tras' ] ) ) ){
										if( articulosAplicadosDesdeCco( $conex, $bd, $pac['his'], $pac['ing'], $art['cod'], $cco['cod'] ) ){
											if( haySaldoPacienteCargado( $conex, $bd, $pac['his'], $pac['ing'], $art['cod'], $cco['cod'] ) ){
												$artValido = true;
												$anularAplicacion = true;
												$error = array();
											}
										}
									}
									/************************************************************************************************************************/
								}
							}else{
								/*El artículo es válido no así la cantidad*/
								/*Debe volver a preguntar la cantidad*/
								$error['codInt']='2003';
								$error['codSis']='NO APLICA';
								$error['descSis']='NO APLICA';
								if(!isset($dronum))
								{
									$dronum=0;
								}
								registrarError($odbc, $cco, $fuente, $dronum, 0, $pac, $art, $error, $color, $warning, $usuario);
								$art['cva']=true; //Para que vuelva a pedir la cantidad.
							}
						}else{
							/*Cantidad mayor al máximo permitido*/
							/*Debe volver a preguntar la cantidad
							Artículo válido cantidad invalida*/
							$error['codInt']='2004';
							$error['codSis']='NO APLICA';
							$error['descSis']='NO APLICA';
							$art['cva']=true;  //Para que vuelva a pedir la cantidad.
							if(!isset($dronum))
							{
								$dronum=0;
							}
							registrarError($odbc, $cco, $fuente, $dronum, 0, $pac, $art, $error, $color, $warning, $usuario);
						}
					}
				}

				/*Fin de las Validaciones para el artículo*/
				/*Empieza el ingreso de datos a la BD*/

				$val = preCondicionesKE( $pac, $art, $ke, $artValido, $tipTrans, $cco, $nka );

				//Septiembre 16 de 2011
				//Se busca el saldo que hay en piso antes de grabar, esto con el fin de poder grabar en el kardex los medicamentos a necesidad
				$saldoEnPiso = tieneSaldoEnPiso(  $bd, $conex, ( $cco['cod'] == $centraldemezclas )? 'CM': 'SF', $pac['his'], $pac['ing'], $art['cod'] );

				//Si hay un medicamento a necesidad dejo el saldo en 0 en contingencia para que se grabe sin problemas
				if( !empty($procesoContingencia) && $procesoContingencia == 'on' ){
					// $val = true;
					$saldoEnPiso = 0;
				}
				
				
				
				$implantable = $art['imp'];
				if( $art['imp'] && !empty( $lote ) ){
					$implantable = false;
				}

				if( $val && $artValido and !$art['cva'] && !$implantable )
				{
					connectOdbc($conex_o, 'inventarios');
					
					//tabla matrix si no hay conexión
					$tablaCargosMatrix = "000143";
					if( $conex_o != 0 ){
				
						//Si hay conexión
						$tablaCargosMatrix = "000003";
						
						ivartCba($usuario);
						ivart($usuario);
						
						actualizacionDetalleRegistros($pac,$array);

						//Se ejecuta lo que haga falta en unix
						//Esto solo se hace una vez al cargar un articulo
						if( $ejecutarProcesosUnix == 0 ){
					
							$ejecutarProcesosUnix = 1;
							
							
							ejecutarLogUnix( $conex_o, $conex, $bd );
							
							/****************************************************************************************************************
							 * Enero 25 de 2013
							 *
							 * Cuando hay unix activo intento pasar todos los registros de la tabla de movimiento de paso(movhos_000143)
							 * a la tabla de movimiento(movhos_000003) y registrar en Itdro
							 ****************************************************************************************************************/
							procesoContingencia();
							/****************************************************************************************************************/
						}
					}
					
					/*Buscar los consecutivos */
					$artValido=Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, $date, $cns, $dronum, $drolin, $pac['dxv'], $usuario, $error );

					/*Registrar en UNIX*/
					if($artValido)
					{
						if( $conex_o != 0 ){
							
							//Consulto las empresas a las que se requiere el cambio de articulo equivalente
							$responsablesEq = consultarAliasPorAplicacion( $conex, $emp, "empresaConEquivalenciaMedEInsumos" );
							// $tipoEmpresaParticular = consultarAliasPorAplicacion( $conex, $emp, "tipoempresaparticular" );
							$resPaciente = consultarResponsable( $conex, $pac['his'], $pac['ing'] );
							$admiteEquivalencia = false;
							
							// if( $tipoEmpresaParticular != $resPaciente['tipoEmpresa'] ){
								$responsablesEq = explode( ",", $responsablesEq );
								$admiteEquivalencia = array_search( $resPaciente['responsable'], $responsablesEq ) === false ? false: true;
								$admiteEquivalencia = $admiteEquivalencia === false && array_search( '*', $responsablesEq ) !== false ? true: false;

								$reInsProducto = consultarInsumosProducto( $wcenpro, $bd, $art[ 'cod' ] );
							// }

							if( @mysql_num_rows( $reInsProducto ) == 0 || !$admiteEquivalencia ){

								/****************************************************************************
								 * Noviembre 12 de 2013
								 ****************************************************************************/
								//Consulto código equivalente
								$artEq = consultarArticuloEquivalente( $cco[ 'cod' ], $art[ 'cod' ] );
								$auxArtEq = $art;
								if( !empty( $artEq ) && $admiteEquivalencia ){
									$art['uni'] = $artEq[ 'Artuni' ];
									$art['can'] = $artEq['Areceq']*$artEq['Arefra']*$art['can']/$art['fra'];		//Convierto la cantidad a cargar en la nueva para el medicamento equivalente
									$art['cod'] = $artEq['Areaeq'];													//Reemplazo el código del articulo por el código equivalente
									$art['fra'] = $artEq['Arefra'];
								}
								/****************************************************************************/
								
								if( $conex_o != 0 )
									$artValido =registrarItdro($dronum, $drolin, $fuente, $date, $cco, $pac, $art, $error);
								
								//Octubre 13 de 2015. Cargos ERp
								CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );

								/************************************************************************************
								 * Febrero 27 de 2014
								 ************************************************************************************/
								if( !empty( $artEq ) && $artValido && $admiteEquivalencia ){

									registrarLogArticuloEquivalente( $conex, $bd, $auxArtEq, $art, $dronum, $drolin, 'off' );

									//Se hace un ajuste de entrada para cada uno de los insumos iguale a la cantidad dispensado
									list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntrada" ) );
									if( $tipTrans != 'C' ){
										list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalidaDevolucion" ) );
									}
									ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
								}
								/************************************************************************************/

								$art = $auxArtEq;			//Noviembre 12 de 2013

								/************************************************************************************
								 * Febrero 27 de 2014
								 ************************************************************************************/
								if( !empty( $artEq ) && $artValido && $admiteEquivalencia ){
									//Se hace un ajuste de Salida de inventario para el articulo que se va dispensar
									list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalida" ) );
									if( $tipTrans != 'C' ){
										list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntradaDevolucion" ) );
									}
									ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
								}
								/************************************************************************************/
							}
							else{
								registrarInsumosProducto( $reInsProducto, $cco, $dronum, $drolin, $fuente, $date, $pac, $art, $error, $tipTrans, $aprov );

								//Se hace un ajuste de entrada para cada uno de los insumos igual a la cantidad dispensado
								list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteSalida" ) );
								if( $tipTrans != 'C' ){
									list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $emp, "ajusteEntradaDevolucion" ) );
								}
								ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
								
								CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );
							}
						}
						else{
							
							// agregarAlCarro( $art, $art['ser'], $tipTrans, $cco );
							// $artValido = registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario, &$error, "000143" );
							CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );
							
							//Busco si es un producto busco los insumos del producto codificado
							$reInsProducto = consultarInsumosProducto( $wcenpro, $bd, $art[ 'cod' ] );

							if( mysql_num_rows( $reInsProducto ) > 0 ){

								//Solo muevo la numeración tantas veces sea necesario
								//Esto por que se debe mantener el valor de los drolin correctamente
								//al momento de procesar la contingencia del kardex
								$drolinAux = 0;
								for( $i = 0; $rowsIns =  mysql_fetch_array( $reInsProducto ); $i++ ){
									if( $i > 0 ){
										Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, $date, $cns, $dronum, $drolinAux, $pac['dxv'], $usuario, $error );
									}
								}
							}
						}



						if($artValido)
						{
							$art['ubi']='US';
							$art['lot']=" ";
							$art['ser']=$pac['sac'];
							if(isset($car) and !$cco['apl'])
							{
								$art['dis']='on';
							}
							agregarAlCarro( $art, $art['ser'], $tipTrans, $cco );
							$artValido = registrarDetalleCargo($date, $dronum, $drolin, $art, $usuario,$error,$tablaCargosMatrix);
							
							//Si es implantable debo registrar el lote
							if( $art['imp'] && !empty( $lote ) ){
								
								$turno = '';	//No se manejan turnos
								
								$fechaLote 	= date( "Y-m-d" );
								$horaLote 	= date( "H:i:s" );
								
								$canLote = 0;
								$devLote = 0;
								if( $tipTrans != 'C' ){
									$devLote = $art['can'];
								}
								else{
									$canLote = $art['can'];
								}
								
								$estadoLote = 'on';
								
								$medTratante = consultar_MedicoTratante( $conex, $bd, $pac['his'], $pac['ing'], date("Y-m-d") );
								
								registrarLote( $conex, $wcliame, $turno, $art['cod'], $lote, $canLote, $devLote, $fechaLote, $horaLote, $usuario, $estadoLote, $pac['his'], $pac['ing'], 'on', $cco['cod'], $medTratante );
								
								//Dejo esto en false para que no pida nuavamente el lote
								$art['imp'] = false;
							}

							if($artValido)
							{

								/**************************************************************************************************
								 * Abril 15 de 2013
								 **************************************************************************************************/
								$resAut = consultarArticulosACargarAutomaticamente( $art['cod'] );

								if( $resAut ){

									$numResAut = mysql_num_rows( $resAut );

									if( $numResAut > 0 ){

										while( $rowsResAut = mysql_fetch_array( $resAut ) ){

											$art2['cod'] = $rowsResAut[ 'Artcod' ];
											$art2['can'] = $rowsResAut[ 'Acpcan' ]*$art['can'];
											$art2['ini'] = $rowsResAut[ 'Artcod' ];
											$art2['ser'] = $art['ser'];
											$art2['ubi'] = $art['ubi'];
											$art2['nom'] = $rowsResAut[ 'Artcom' ];
											$art2['uni'] = $rowsResAut[ 'Artuni' ];

											$artValido = Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, $date, $cns, $dronum, $drolin2, $pac['dxv'], $usuario, $error );
											
											if( $conex_o != 0 )
												$artValido = registrarItdro($dronum, $drolin2, $fuente, $date, $cco, $pac, $art2, $error);
											
											$artValido = registrarDetalleCargo($date, $dronum, $drolin2, $art2, $usuario,$error,$tablaCargosMatrix);
											CargarCargosErp( $conex, $bd, "cliame", $art2, $tipTrans, $dronum, $drolin2 );

											$ardrolin2[ $art2['cod'] ] = $drolin2;

											if( $cco['apl'] ){
												registrarSaldosAplicacion($pac,$art2,$cco,$aprov,$usuario,$tipTrans,false,$error);
											}
											elseif( $tipTrans == 'C' ){
												registrarSaldosNoApl($pac, $art2,$cco,$aprov,$usuario,$tipTrans,false,$error);
												registrarSaldosNoApl($pac, $art2,$cco,$aprov,$usuario,"D",false,$error);
											}


											$fecApl2=$date;
											// $ronApl=date("G:i - A");
											$ronApl2=gmdate("H:00 - A", floor( date( "H" )/2 )*2*3600 );
											registrarAplicacion($pac, $art2, $cco,$aprov,$fecApl2,$ronApl2, $usuario, $tipTrans, $dronum,$ardrolin2[ $art2['cod'] ], $error);
											actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $cco, $art2, $dronum, $ardrolin2[ $art2['cod'] ], 1050 );
										}

										mysql_data_seek( $resAut, 0 );	//reseteo nuevamente la consulta por si toca hacer la aplicación automática
									}
								}
								/**************************************************************************************************/
								
								
								/***************************************************************************
								 * Enero 2 de 2013
								 *
								 * Si hubo un registro en el detalle, entonces debo mover la tabla de saldos
								 ***************************************************************************/
								realizarMovimientoSaldos( $conex, $bd, $tipTrans, $cco[ 'cod' ], $art[ 'cod' ], $art[ 'can' ] );
								/************************************************************************/
								
								/**************************************************************************************************************
								 * Marzo 28 de 2011
								 **************************************************************************************************************/
								$auxArt = $art;

								//Si $art['apl'] no esta se busca nuevamente el articulo, ya que puede ser articulo variable
								//y no se encuentra seteado
								if( !isset($art['apl']) ){
									ArticulosEspeciales($cco, $art);

									// //Noviembre 06 de 2014
									// //verifico si es urgencias y si es mmmq
									// //Si es así el medicamento aplica automaticamente
									// if( $art['apl'] != 'on' ){
										// if( irAOrdenes( $conex, $bd, '', $ccoPac['cod'] ) == 'on' && pacienteKardexOrdenes( $conex, $bd, $pac[ 'his' ], $pac[ 'ing' ], date( "Y-m-d" ) ) ){
											// if( esMMQ( $art['cod'] ) && esUrgencias( $cco['cod'] ) ){
												// if(strtoupper($art['cod']) != 'E1AB03'){
													// $art['apl'] = 'on';
												// }else{
													// $art['apl'] = 'off';
												// }
											// }
										// }
									// }
								}

								if( isset( $art[ 'preApl' ] ) && $art[ 'preApl' ] == true ){
									$art['apl'] = 'on';
								}

								$art['can'] = $auxArt['can'];

								/**************************************************************************************************************/
								$registroAplicacion = false;	//Mayo 6 de 2011
								                               //Febrero 19 de 2009
								if($artValido and $cco['apl'] )
								{
									/**
									 * Aplica automáticamente
									 * * $cco['apl'] El centro de costo que carga o el centro de costos donde esta el paciente aplica automáticamente.
									 */

									//Modificar el saldo de aplicación
									$artValido=registrarSaldosAplicacion($pac,$art,$cco,$aprov,$usuario,$tipTrans,false,$error);

									if($artValido)
									{
										if(!isset($fecApl))
										{
											$fecApl=$date;
											// $ronApl=date("G:i - A");
											$ronApl=gmdate("H:00 - A", floor( date( "H" )/2 )*2*3600 );
										}

										//Registrar la aplicación del artículo
										$artValido = registrarAplicacion($pac, $art, $cco,$aprov,$fecApl,$ronApl, $usuario, $tipTrans, $dronum, $drolin, $error);
										$registroAplicacion = true;	//Mayo 6 de 2011

										actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $cco, $art, $dronum, $drolin, 1050 );	//Noviembre 8 de 2011
									}
								}
								else
								{
									// Valido si el artículo existe en kardex
									$artKardex = consultarRegistroKardexPorArticulo( $art, $pac );
									$artStock = consultarRegistroStockArticulo( $art, $cco );
									$artAplicaAuto = consultarAplicacionAutoArticuloStock( $art, $cco );

									if( !isset($anularAplicacion) ){

										//No es de aplicación automática se afecta el saldo del artículo al paciente.
										$artValido=registrarSaldosNoApl($pac, $art,$cco,$aprov,$usuario,$tipTrans,false,$error);

										$ascCco = $cco['asc'];

										// Si el artículo está en kardex y es un artículo del stock
										// y es un artículo especial que no tiene aplicación automática
										if($tipTrans != 'D' && $artKardex && $artStock && !$artAplicaAuto)
										{
											// Se pone en false inhabilitando la condición del servicio que carga
											// es decir, se obliga a que la condición sea que no se aplica lo que se carga
											$ascCco = false;
											if( $ascCco or ( isset($art['apl']) and $art['apl']=="on" ) ){
												$art['apl']= "off";
											}
										}

										// Julio 24 de 2013
										if($artAplicaAuto)
											$art['apl'] = "on";

										//Marzo 28 de 2011. Modificacion: Mayo 17 de 2012
										if( $tipTrans == 'C' and ( $ascCco or ( isset($art['apl']) and $art['apl']=="on" ) ) )
										{
											/**
											 * Es un cargo, no una devolución, y el centro de costos que esta cargando aplica cuando se carga de ese centro de costos.
											 *
											 * Esto quiere decir que antes de ingresar la historia se pidio una fecha de aplicación y una hora de aplicación, y se toma
											 * como si fueran dos transacciones independientes, un cargo normal ewn el que se usa registrarSaldosNoApl, y una aplicación
											 * para la cual se hace una salida por medio de registrarSaldosNoApl() y se regitra una aplicación a través de
											 * registrarAplicacion().
											 */

											//Se hace una salida por que se hace
											$artValido=registrarSaldosNoApl($pac, $art,$cco,$aprov,$usuario,"D",false,$error);

											if($artValido)
											{
												if(!isset($fecApl))
												{
													$fecApl=$date;
													// $ronApl=date("G:i - A");
													$ronApl=gmdate("H:00 - A", floor( date( "H" )/2 )*2*3600 );
												}
												//Registrar la aplicación del artículo
												$artValido = registrarAplicacion($pac, $art, $cco,$aprov,$fecApl,$ronApl, $usuario, $tipTrans,$dronum,$drolin, $error);
												$registroAplicacion = true;	//Mayo 6 de 2011

												actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $cco, $art, $dronum, $drolin, 1050 );	//Noviembre 8 de 2011
											}
										}
									}
									else
									{
										// Se define por defecto que la devolución corresponde a una aplicación automática
										$aplicaAuto = true;

										// Si el artículo está en kardex y es un artículo del stock
										// y es un artículo especial que no tiene aplicación automática
										if($artKardex && $artStock && !$artAplicaAuto)
										{
											// Se pone en false el estado de aplicación automática
											$aplicaAuto = false;
										}

										if($aplicaAuto)
										{
											/************************************************************************************************************
											 * Junio 27 de 2012
											 *
											 * Es una devolucion de una aplicacion automatica
											 * Por tal motivo no afecto saldos, debido a que corresponde una anulacion de aplicacion y una devolucion
											 * La anulacion de la aplicacion disminuye la salida en matrix y la devolucion la aumenta
											 ************************************************************************************************************/
											$artValido = registrarAplicacion($pac, $art, $cco,$aprov,$fecApl,$ronApl, $usuario, $tipTrans, $dronum,$drolin, $error);
											/************************************************************************************************************/
										}
									}
								}

								$art = $auxArt;	//Marzo 28 de 2011

								if( $cargados == "off" && $artValido ){
									$cargados = "on";
								}

								if( $ke && !$art['cva'] && $artValido ){

									$regbool = registrarArticuloKE( $art, $pac, $tipTrans, $idRegistro, ( ( $cco['tras'] || $cco['urg'] || $cco['ayu'] )? true: false ), $saldoEnPiso, $dronum, $drolin, $registroAplicacion, $rondaAplicada );

									/************************************************************************************************************************
									 * Mayo 6 de 2011
									 ************************************************************************************************************************/
									if( $tipTrans == "C" && $registroAplicacion && !empty($idRegistro) ){
										actualizandoAplicacion( $idRegistro, $cco, $art, $dronum, $drolin, $rondaAplicada );
									}
									/*************************************************************************************************************************/

									if( $regbool && isset($gene) && isset( $rsAlp['id'] ) ){
//										if( $factura == 'LA FACTURA DEL PACIENTE YA HA SIDO GENEREDA. FAVOR COMUNICARSE CON EL FACTURADOR' ){
										if( $gene > 0 ){
											grabarDespuesDeFacturar( $pac['his'], $pac['ing'], $rsAlp['id'] );
											echo "<script>alert('LA FACTURA DEL PACIENTE YA\\nHA SIDO GENERADA. FAVOR\\nCOMUNICARSE CON EL FACTURADOR\\nURGENTE');</script>";
										}
									}
									else{
										//Si no fue cargado al kardex y es precontingencia debe marcar el registro de cargo como PC
										if( !$regbool && !$registroAplicacion ){	//No fue cargado en el kardex ni aplicado

											if( !empty( $contingencia ) && $contingencia == 'on' ){	//Si es contingencia dejo la maraca PC

												if( time() > $fhGrabacionContingencia ){
													actualizandoCargo( $conex, $bd, $dronum, $drolin, $marcaContingencia );
												}
											}
										}
									}
								}

								//se hace la devolucion del carro
//								if($tipTrans == 'Anulado')//se debe poner !=C
								if( $tipTrans != 'C' )//se debe poner !=C
								{
									if($aprov)
									{
										$letra='A';
									}
									else
									{
										$letra='P';
									}
									//aca voy a consultar cuantos elementos estan en el carro
									$q = "  SELECT ".$bd."_000003.id, fdecan, fdecad "
									."        FROM ".$bd."_000002, ".$bd."_000003 "
									."       WHERE Fenhis=".$pac['his']." "
									."         AND Fening=".$pac['ing']." "
									."         AND Fencco='".$cco['cod']."' "
									."         AND Fentip='C".$letra."' "
									."         AND Fdenum=Fennum "
									."         AND Fdeart='".$art['cod']."' "
									."         AND Fdedis='on' "
									."         AND Fdeest='on' ";

									$err1 = mysql_query($q,$conex);
									echo mysql_error();
									$num1 = mysql_num_rows($err1);

									if($num1 >0)
									{

										$cantidadACargar = $art['can']/1;//; $art['fra'];

										for( $i = 0; ($row1 = mysql_fetch_array($err1)) && $cantidadACargar > 0; $i++ ){

											if( $cantidadACargar <= $row1[1]-$row1[2] ){

												$cantidadCargada = $cantidadACargar;
												$cantidadACargar = 0;

												if( $cantidadCargada+$row1[2] == $row1[1] ){
													$estado = 'off';
												}
												else{
													$estado = 'on';
												}
											}
											else{
												$cantidadCargada = $row1[1]-$row1[2];
												$cantidadACargar = $cantidadACargar - $cantidadCargada;

												$estado = 'off';
											}

											$q = "  UPDATE ".$bd."_000003 "
											."         SET fdedis = '$estado', "
											."             fdecad = fdecad+'".$cantidadCargada."' "
											."    WHERE id = ".$row1[0]
											."         AND Fdeart='".$art['cod']."' "
											."         AND Fdedis='on' "
											."         AND Fdeest='on' ";
											$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
										}
									}
									else
									{
										//aca voy a consultar cuantos elementos estan en el carro y son de la central
//										$q = "SELECT Fdenum "
//										."        FROM ".$bd."_000002, ".$bd."_000003 "
//										."       WHERE Fenhis=".$pac['his']." "
//										."         AND Fening=".$pac['ing']." "
//										."         AND Fencco='".$cco['cod']."' "
//										."         AND Fentip='C".$aprov."' "
//										."         AND Fdenum=Fennum "
//										."         AND Fdeari='".$art['cod']."' "
//										."         AND Fdedis='on' "
//										."         AND Fdeest='on' ";
//
//										$err1 = mysql_query($q,$conex);
//										echo mysql_error();
//										$num1 = mysql_num_rows($err1);
//										$row1=mysql_fetch_array($err1);
//										if($num1 >0)
//										{
//											$q = " UPDATE ".$bd."_000003 "
//											."    SET fdedis = 'off' "
//											."  WHERE Fdenum = ".$row1[0]
//											."         AND Fdeari='".$art['cod']."' "
//											."         AND Fdedis='on' "
//											."         AND Fdeest='on' ";
//											$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
//										}
									}
								}


							}//dentro de estas llaves se registran los saldos, y las aplicaciones si es del caso
						}//dentro de estas llaves se registran el detalle del cargo, los saldos, y las aplicaciones si es del caso
					}//dentro de estas llaves se registra en el cargo en itdro, el detalle del cargo en MATRIX, los saldos, y las aplicaciones si es del caso
				
					odbc_close_all();
				}

				if(!$artValido)
				{
					IF(!isset($dronum))
					{
						$dronum = 0;
					}
					IF(!isset($drolin))
					{
						$drolin = 0;
					}
					registrarError($odbc, $cco, $fuente, $dronum, $drolin, $pac, $art, $error, $color, $warning, $usuario);
				}
			
			}
			
		}
	}
	

	/************************************************************************************************************/
	consultarInfoTipoArticulos( $conex, $wbasedato );

	/****************************************************************************************************
	 * Agosto 27 de 2012
	 ****************************************************************************************************/
	$disCco = consultarHoraDispensacionPorCco( $conex, $wbasedato, $pac['sac'] );

	if( $disCco ){
		$tempRonda = $disCco;
	}
	/****************************************************************************************************/
	$horasDispensacion = $tempRonda;
	$tempRonda = ( intval( ( date( "H" )+intval( $tempRonda/3600 ) )/$tmpDispensacion )*$tmpDispensacion );

	// if( $tempRonda >= 24 ){

		// if( $tempRonda-24 >= 10 ){
			// $tempRonda = ($tempRonda-24).":00:00";
		// }
		// else{
			// $tempRonda = "0".($tempRonda-24).":00:00";
		// }
	// }
	// else{
		// if( $tempRonda >= 10 ){
			// $tempRonda = " $tempRonda:00:00";
		// }
		// else{
			// $tempRonda = " 0$tempRonda:00:00";
		// }
	// }

	// echo "<tr><td class='tituloSup' align='center'><b>RONDA: ".$tempRonda."</b></b>";
	$rondaMaximaDispensacion = gmdate( "H:i:s", min( ($horaCorteDispensacion+24)*3600,$tempRonda*3600 ) );
	// echo "<tr>";
	// echo "<td class='tituloSup' align='center'><b>RONDA: ".$rondaMaximaDispensacion."</b></b>";
	// echo "</tr>";
	
	
	/**************************************************************************************************
	* Se terminan los procesos de comprobación de datos y artículos
	* A partir de aqui se pide y se muestra información en pantalla
	**************************************************************************************************/

	//Información del usuario
	
	$userPDA = consultarUsuario($conex,$usuario);
	
	echo "<td>";
	
	echo "<table>";
	echo "<tr>";
	
	echo "<td style='width:1000px;'>";
	echo "<fieldset style='border: 2px solid #e0e0e0;height:60px;'>";
	echo "<legend>Informaci&oacute;n del usuario</legend>";
	
	
	echo "<div style='margin:0 auto;'>";
	echo "<table style='margin: 0 auto;'>";
	
	//Centro de costos
	// echo "<tr>"; 
	echo "<td class='titulo2' style='text-align:right;'><b>CCO:</b></td>"; 
	echo "<td class='titulo3' style='text-align:left;'>".$cco['cod']."-".$cco['nom']."</td>"; 
	// echo "</tr>";
	
	//Usuario
	// echo "<tr>"; 
	echo "<td class='titulo2' style='text-align:right;'><b>USUARIO:</b></td>"; 
	echo "<td class='titulo3' style='text-align:left;'> ".$userPDA->descripcion."</b></td>";
	// echo "</tr>";
	
	//Fecha de dispensacion
	// echo "<tr>";
	echo "<td class='titulo2' style='text-align:right;'><b>FECHA:</b></td>";
	echo "<td class='titulo3' style='text-align:left;'>";
	if (isset($date) )
		echo str_replace("-","/",$date)." ".date("H:i:s");
	else
		echo date('Y/m/d')." ".date("H:i:s");
	echo "</td>";
	// echo "</tr>";
	
	//Ronda
	if( $tipTrans != 'D' ){
		echo "<tr>";
		echo "<td class='titulo2' colspan=2><b>RONDA MAXIMA DE DISPENSACION:</b></td>";
		echo "<td class='titulo3' colspan=4 style='background-color:yellow'><b>".$rondaMaximaDispensacion."(".($horasDispensacion/3600)." horas de dispensaci&oacute;n)</b></td>";
		echo "</tr>";
	}
	
	echo "</table>";
	
	echo "</div>"; 
	echo "</fieldset>";
	
	echo "</td>";
	
	// // Consultar alertas en movhos_000220
	// $alergiasAnteriores = consultarAlergiaAlertas($pac['his'], $pac['ing']);
	
	consultarAlergiasDiagnosticosAnteriores($pac['his'],$pac['ing'],$alergiasAnteriores,$diagnosticosAnteriores);
	
	//Antecedentes alergicos
	if( $tipTrans != 'D' && $alergiasAnteriores != ''  ){
		echo "<tr>";
		echo "<td style='width:400px;'>";
		
		echo "<fieldset style='border: 2px solid #e0e0e0;height:110px;'>";
		echo "<legend style='font-size:10pt;'>ALERGIAS</legend>";
		echo "<div id='alergias' style='width:97%;background:#FFFFCC;padding:5px;font-size:10pt;'>";
		// echo "<b>ALERGIAS</b>";
		// echo "<div style='border: 1px solid black; background:#FFF;padding:3px;overflow:auto;height:80px;'>";
		echo "<div style='border: 1px solid black; background:#FFF;padding:3px;overflow:auto;height:90%;width:100%;'>";
		echo str_replace( "\n","<br>", $alergiasAnteriores );
		echo "</div>";
		echo "</div>";
		echo "</fieldset>";
		
		echo "</td>";
		echo "</tr>";
	}
	
	echo "</tr>";
	echo "</table>";
	echo "</td>";
	
	echo "<tr>";
	
	if( $tipTrans == "C" ){
		// echo "</table>";
		//Información de articulos a cargar
		echo "<tr><td><fieldset style='border: 2px solid #e0e0e0;'><legend>Paciente</legend>";
		echo "<table align=center style='width:90%;'>";
		echo "<tr>"; 
		echo "<td>";
	}

	if(isset($factura))
	{
		echo "<tr><td align=center><font color='red'>".$factura."</td></font></tr>";
	}
	/************************************************************************************************************/



	$hayArticulosPorDispensar = hayArticulosConSaldo( ArticulosXPaciente( $pac ) );

	if( $cargados == "on" ){
		echo "<INPUT type='hidden' value='on' name='cargados'>";
	}
	else{
		echo "<INPUT type='hidden' value='off' name='cargados'>";
	}

	if( isset($peticionCamillero) ){
		//Febrero 06 de 2015. Se desactiva la petición de camilleros a solicitud de Beatriz Orrego
		// echo "<INPUT type='hidden' value='on' name='peticionCamillero'>";

		// echo "<tr>";
		// echo "<td class='titulo2'>SE HA SOLICITADO CAMILLERO</td>";
		// echo "</tr>";

		$can = cantidadArticulosDispensado( $conex, $bd, $pac['his'], $pac['ing'], $fechorDispensacion );

		echo "<tr>";
		echo "<td class='titulo2'><b>$can ARTICULOS DISPENSADOS</b></td>";
		echo "</tr>";
	}

	if( $cco['tras'] && $tipTrans == "C" && $cargados == "on" && !$hayArticulosPorDispensar && !isset($peticionCamillero) ){

		//Febrero 06 de 2015. Se desactiva la petición de camilleros a solicitud de Beatriz Orrego
		// $nomCcoDestino = nombreCcoCentralCamilleros( $pac['sac'] );
		// $motivo = 'DESPACHO DE MEDICAMENTOS';
		// crearPeticionCamillero( nombreCcoCentralCamilleros( $cco['cod'] ), $motivo, "<b>Hab: ".$pac['hac']."</b><br>".$pac['nom'], $nomCcoDestino, str_replace( "-", "", $usuario ), $nomCcoDestino, buscarCodigoNombreCamillero() );

		// echo "<INPUT type='hidden' value='on' name='peticionCamillero'>";

		// echo "<tr>";
		// echo "<td class='titulo2'>SE HA SOLICITADO CAMILLERO</td>";
		// echo "</tr>";

		$can = cantidadArticulosDispensado( $conex, $bd, $pac['his'], $pac['ing'], $fechorDispensacion );

		echo "<tr>";
		echo "<td class='titulo2'><b>$can ARTICULOS DISPENSADOS</b></td>";
		echo "</tr>";
	}


	if(isset($pac['act']) and $pac['act'] )
	{
		/*Si existe el numero de historia y el CC*/
		// echo "<tr><td class='$classHis'><b>".$pac['nom']." (".$pac['hac'].")</td></tr>";
		echo "<tr>";
		echo "<td>";
		
		/******************************************************************************************************************************
		 * Octubre 14 de 2016
		 * Información del paciente
		 ******************************************************************************************************************************/
		echo "<table align='center'>";
		
		echo "<tr>"; 
		// echo "<td class='$classHis' style='text-align:right;'>NOMBRE:</td>";
		echo "<td class='fila1' style='text-align:right;'>NOMBRE:</td>";
		echo "<td class='fila2'>"; 
		echo "<b>".$pac['nom']."</b>";
		echo "</td>"; 
		// echo "</tr>"; 
		
		// echo "<tr>"; 
		// echo "<td class='$classHis' style='text-align:right;'>IDENTIFICACION:</td>";
		echo "<td class='fila1' style='text-align:right;'>IDENTIFICACION:</td>";
		echo "<td class='fila2'>"; 
		echo "<b>".$pac['tid']." ".$pac['doc']."";
		echo "</td>"; 
		// echo "</tr>";
		
		// echo "<tr>"; 
		// echo "<td class='$classHis' style='text-align:right;'>HISTORIA:</td>";
		echo "<td class='fila1' style='text-align:right;'>HISTORIA:</td>";
		echo "<td class='fila2'>"; 
		echo "<b>".$pac['his']."-".$pac['ing']."";
		echo "</td>"; 
		// echo "</tr>";
		
		// echo "<tr>";
		// echo "<td class='$classHis' style='text-align:right;'>HABITACION:</td>";
		echo "<td class='fila1' style='text-align:right;'>HABITACION:</td>";
		echo "<td class='fila2' style='background-color:yellow'>"; 
		echo "<b>".$pac['hac'];
		echo "</td>"; 
		echo "</tr>";
		
		echo "</table>";
		/********************************************************************************************************************************/

		echo "</td>";
		echo "</tr>";
		// echo"<tr><td><br></td></tr>";
		
		/****************************************************************************************
		 * Octubre 10 de 2016
		 * Muestro la leyenda
		 ****************************************************************************************/
		if( $tipTrans == 'C' ){
			echo "<tr>";
			echo "<td>";
			
			echo "<table align='right'>";
			echo "<tr>";
			echo "<td class='fondoAmarilloOscuro'>CTC agotado</td>";
			echo "<td bgColor='yellow'>No Pos</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "</td>";
			echo "</tr>";
		}
		/****************************************************************************************/
		
		
		
		$inputAprov = "";

		if( true || $cco['urg'] ){ //2010-09-24
	//	if( !$pac['ke'] ){
			if( !$ke || $tipTrans != "C" || !$cco['tras'] ){

				if($aprov)
				{
					if($cco['apr'])
					{
						if($art['cva'])
						{
							$inputAprov = "<input type='checkbox' name='aprov' checked>Aprov";
						}
						else
						{
							$inputAprov = "<input type='checkbox' name='aprov'>Aprov";
						}
					}
					$inputAprov .= "<input type='hidden' name='aprovEstadoAnterior' value='1'>";
				}
				else {
					if($cco['apr'])
					{
						$inputAprov = "<input type='checkbox' name='aprov'>Aprov";
					}
					$inputAprov .= "<input type='hidden' name='aprovEstadoAnterior' value='0'>";
				}
			}
			else{
				if($aprov)
				{
					if($cco['apr'])
					{
						if($art['cva'])
						{
							$inputAprov = "<input type='checkbox' name='aprov' checked>Aprov";
						}
						else
						{
							$inputAprov = "<input type='checkbox' name='aprov'>Aprov";
						}
					}
					$inputAprov .= "<input type='hidden' name='aprovEstadoAnterior' value='1'>";
				}else {
					if($cco['apr'])
					{
						$inputAprov = "<input type='checkbox' name='aprov'>Aprov";
					}
					$inputAprov .= "<input type='hidden' name='aprovEstadoAnterior' value='0'>";
				}
			}
		}
		
		

		$dat = ArticulosXPaciente( $pac );
		
		if( isset($art['cod']) and $artValido and ( $art['cva'] || ( $art['imp'] && empty($lote) ) ) )
		{
			/*Cantidad variable*/
			/*Debe digitarse cantidad*/
			// $dat = ArticulosXPaciente( $pac );
			
			/*Se envian todas las variables que +*/
			echo "<input type ='hidden' name='art[cod]' value='".$art['cod']."'>";
			echo "<input type ='hidden' name='art[ini]' value='".$art['ini']."'>";
			echo "<input type ='hidden' name='art[nom]' value='".$art['nom']."'>";
			echo "<input type ='hidden' name='art[uni]' value='".$art['uni']."'>";
			echo "<input type ='hidden' name='art[max]' value='".$art['max']."'>";
			echo "<input type ='hidden' name='art[neg]' value='".$art['neg']."'>";
			echo "<input type ='hidden' name='artValido' value='".$artValido."'>";
			echo "<input type ='hidden' name='art[cva]' value=''>";
		}
		else
		{
			if( !isset($art) ){
				$art = array();
				$art['cva'] = '';
				$art['cod'] = '';
				$art['nom'] = '';
				$art['esp'] = false;
			}
			// $dat = '';
			// $dat = ArticulosXPaciente( $pac );
			$ke = esKE( $pac );
//			$regbool = false;

			//Solo si es devolucion
			// if( !$ke || $tipTrans != "C" || !$cco['tras'] ){
			if( false ){
				
				echo "<tr><td class='".$color."'>".$warning."<b>ART: </font>";
				?>			
					<input type='text' name="artcod" size='14'>
					<script language="JAVASCRIPT" type="text/javascript">
						document.carga.artcod.focus();
					</script>
				 <?php
				echo $inputAprov;
				echo "</td></tr>";

			}


			if(isset($cns) and $artValido and isset($dronum) and $dronum != "")
			{
			 	/*En las variables $artPrevios y Show se acumulan los tres ultimos articulos ingresados al sistema
			 	Se separan los articulos y se seleccionan los ultimos 3*/
			 	if( $ke && $tipTrans == "C"  && $cco['tras'] ){
//			 		$artMostrar = ArticulosXPaciente( $pac );
			 		// $dat = ArticulosXPaciente( $pac );
			 		$artPrevios="";
			 	}
			 	else{
			 		if( $tipTrans == "C" || ($tipTrans != "C" && $val ) ){

			 			if( esNoPos( $art['cod'] ) && $tipTrans == "C" ){
							echo "<script>alert('EL ARTICULO\\n{$art['nom']}\\n ES NO POS');</script>";
						}
						
						$t=explode("*****",$artPrevios);	//separa en una mtriz los articulos						
						@$artPrevios="*****".$cns."//".$art['cod']."//".$art['nom']."//".$art['can']."//".$art['uni']."//".$artPrevios;
						@$artMostrar=$cns.")".$art['nom']." Cant.(".$art['can']." ".$art['uni'].")".chr(13).$t[1].chr(13).$t[2];
			 		}
			 		else{
			 			$cns = $cns-1;
			 		}
			 	}
			}
			else if( $ke ){
//			 	$artMostrar = ArticulosXPaciente( $pac );
			 	// $dat = ArticulosXPaciente( $pac );
			 	$artPrevios="";
			}

		}//fin del else

	

	
	
		/************************************************************************************************************************
		 * Interfaz para cargar
		 *************************************************************************************************************************/
	
		echo "<tr><td class=".$color." ><b>ARTICULOS ASOCIADOS:</b></font>";
		
		$poslist = enLista( $dat, $art );
		
		$mostrarMensaje = true;
		
		
		// if( $ke && $tipTrans == "C" && $cco['tras'] ){
		if( true ){
			
			if( $ke && $tipTrans == "C" && $cco['tras'] ){
				// echo "<tr><td class=".$color." ><b>ARTICULOS ASOCIADOS:</b></font>";
				echo "<tr><td class=fila2 ></font>";

				if( true || $cco['urg'] ){	//Octubre 10 de 2016. Se muestra todos los articulos
					pintarTabla($dat, $color, count($dat)-2, 4 );
				}
				else{
					pintarTabla($dat, $color, 3, 4 );
				}
			}
			
			$pedirArticulo = true;
			
			if( $artValido ){
			
				if( $art['cva'] ){
					$pedirArticulo = false;
				}
				
				if( $art['imp'] && empty($lote) ){
					$pedirArticulo = false;
				}
			}
			
			if( $pedirArticulo ){
				echo "<tr><td class='".$color."'>".$warning." <b>ART: </b></font>";
				?>	<input type='text' name="artcod" size='14'>
					<script language="JAVASCRIPT" type="text/javascript">
					document.carga.artcod.focus();
					</script>
				<?php
					echo $inputAprov;
			}
			else{
				
				$mostrarMensaje = false;
				
				if( $art['cva'] ){
					
					echo "<tr>";
					echo "<td class='".$color."'>";
					echo "<font color=#000066><b>$warning Artículo:</b> <font size='2'>".$art['cod']."-".$art['nom']."<br>";
					echo "(Cant. Máx <a id=aMax>".$art['max']."</a> <b>".$art['uni']."</b>)</font> <b>Cantidad:</b></font>";
					
					?> 
						<input type='text' name="art[can]" size="3"
						value="<?php
							if( $poslist > -1 ){
								echo min( $dat[ $poslist ][1], $art['max'] );
							}
							else{
								echo $art['can'];
							}
						?>">
						<script language="JAVASCRIPT"
						type="text/javascript">
						document.carga.art[can].focus();
						</script>
					<?php
					
					
					echo "</td>";
					echo "</tr>";
					
				}
				
				if( $art['imp'] ){
					
					if( $tipTrans != "C" ){
						$lotesDisponibles = consultarListaLotesCargados( $conex, $wcliame, $art['cod'], $pac['his'], $pac['ing'] );
						// var_dump( $lotesDisponibles );
					}
					
					if( !$art['cva'] ){
						echo "<tr>";
						echo "<td class='".$color."'>";
						echo "<font color=#000066><b>$warning Artículo:</b> <font size='2'>".$art['cod']."-".$art['nom']."<br></font>";
						echo "<b>Cantidad: ".$art['can']."</b></font>";
						echo "<input type ='hidden' name='art[cva]' value=''>";
						echo "<input type ='hidden' name='art[can]' value='".$art['can']."'>";
						echo "</td>";
						echo "</tr>";
					}
					
					echo "<tr><td class='".$color."'>";
					
					if( $tipTrans == "C" || empty($lotesDisponibles) ){
						echo "<b>LOTE</b>: <INPUT type='text' name='lote' id='lote' value='".$lote."'>";
					}
					else{
						echo "<b>LOTE<b>: <select name='lote' id='lote' >";
						echo "<option value=''>Seleccione...</option>";
						foreach( $lotesDisponibles as $key => $value ){
							echo "<option value='".$value['lote']."' data-cantidad='".$value['can']."'>".$value['lote']."</option>";
						}
						echo "</select>";
					}
					
					echo "<input type ='hidden' name='art[imp]' value='1'>";
					
					echo "</td></tr>";
				}
				
				if( $aprov ){
					echo "<input type ='hidden' name='aprov' value='".$aprov."'>";
				}
				
				if( $aprovEstadoAnterior ){
					echo "<input type ='hidden' name='aprovEstadoAnterior' value='".$aprovEstadoAnterior."'>";
				}
			}
			
			
			// echo "<tr><td class=".$color."><b>ARTICULOS ASOCIADOS:</b></font><br>";
			// echo '<textarea ALIGN="CENTER"  ROWS="3" name="artMostrar" cols="28"  color:"'.$color.'" style="font-family: Arial; font-size:14" readonly>'.$artMostrar.'</textarea></font>';
			// var_dump( $artPrevios );
			echo "<br>";
			if( !empty($artPrevios) ){
				
				$informacionARticulos = explode( "*****", $artPrevios );
				
				if( count( $informacionARticulos ) > 0 ){
					
					echo "<tr><td>";
					echo "<table align='center'>";
					
					echo "<tr class='encabezadotabla'>";
					echo "<td>Cons.</td>";
					echo "<td colspan=2 align=center>Artculo</td>";
					echo "<td colspan=2 align=center>Cantidad</td>";
					echo "</tr>";
					
					foreach( $informacionARticulos as $key => $valueInfoARticulos ){
						
						$datosArt = explode( "//", $valueInfoARticulos );
						
						echo "<tr class='fila1'>";
						
						//Consecutivo
						echo "<td style='text-align:center;'>";
						echo $datosArt[0];
						echo "</td>";
						
						//Código
						echo "<td style='text-align:center;'>";
						echo $datosArt[1];
						echo "</td>";
						
						//Nombre
						echo "<td>";
						echo $datosArt[2];
						echo "</td>";
						
						//Cantidad
						echo "<td style='text-align:center;'>";
						echo $datosArt[3];
						echo "</td>";
						
						//Unidad							
						echo "<td style='text-align:center;'>";
						echo $datosArt[4];
						echo "</td>";
						
						echo "</tr>";
					}
					
					echo "</table>";
					
					echo "</td></tr>";
				}
			}
			
		}
		else{
				
			// echo "<tr><td class=".$color."><b>ARTICULOS ASOCIADOS:</b></font><br>";
			// echo '<textarea ALIGN="CENTER"  ROWS="3" name="artMostrar" cols="28"  color:"'.$color.'" style="font-family: Arial; font-size:14" readonly>'.$artMostrar.'</textarea></font>';
			// var_dump( $artPrevios );
			echo "<br>";
			if( !empty($artPrevios) ){
				
				$informacionARticulos = explode( "*****", $artPrevios );
				
				if( count( $informacionARticulos ) > 0 ){
					
					echo "<tr><td>";
					echo "<table align='center'>";
					
					echo "<tr class='encabezadotabla'>";
					echo "<td>Cons.</td>";
					echo "<td colspan=2 align=center>Artculo</td>";
					echo "<td colspan=2 align=center>Cantidad</td>";
					echo "</tr>";
					
					foreach( $informacionARticulos as $key => $valueInfoARticulos ){
						
						$datosArt = explode( "//", $valueInfoARticulos );
						
						echo "<tr class='fila1'>";
						
						//Consecutivo
						echo "<td style='text-align:center;'>";
						echo $datosArt[0];
						echo "</td>";
						
						//Código
						echo "<td style='text-align:center;'>";
						echo $datosArt[1];
						echo "</td>";
						
						//Nombre
						echo "<td>";
						echo $datosArt[2];
						echo "</td>";
						
						//Cantidad
						echo "<td style='text-align:center;'>";
						echo $datosArt[3];
						echo "</td>";
						
						//Unidad							
						echo "<td style='text-align:center;'>";
						echo $datosArt[4];
						echo "</td>";
						
						echo "</tr>";
					}
					
					echo "</table>";
					
					echo "</td></tr>";
				}
			}
		}
	
		/*************************************************************************************************************************/
	
		If(isset($cns) and $cns != 0) {
			if($artValido and !$art['cva'] ){
				/*Si existe artgen y es diferente de vacio es por que se ingresaron datos al sistema*/
				echo "<input type='hidden' name='cns' value='".($cns+1)."'>";
			}
			else
			{
				echo "<input type='hidden' name='cns' value='".$cns."'>";
			}

			echo "<input type='hidden' name='dronum' value='".$dronum."'>";
			echo "<input type='hidden' name='date' value='".$date."'>";
			echo "<input type='hidden' name='artPrevios' value='".$artPrevios."'>";
			
		}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		

		$kardexAbierto = buscarKardexAbierto( $conex, $wbasedato, $pac[ 'his' ], $pac[ 'ing' ], $fecDispensacion );

		if( empty( $procesoContingencia ) || $procesoContingencia != 'on' ){
			if( !empty( $kardexAbierto ) ){
				echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
				mostrarAlerta(\"El Kardex del paciente con historia ".$pac[ 'his' ]."-".$pac[ 'ing' ]." se encuentra abierto por $kardexAbierto\");
				</script>";
			}
		}

		if( !isset($regbool) ){
			$regbool = false;
		}

		if( isset($art) && $tipTrans == "C" && $ke ){

//			if( $cco['urg'] ){
//
//				if( @$artValido && ( $poslist == -1 || ( $poslist > 2 ) ) ){
//					echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
//					  		mostrarAlerta(\"El Articulo \\n{$art['cod']}-{$art['nom']}\\nfue cargado al paciente\");
//				      		</script>";
//				}
//			}

			if( !esMMQ( $art['cod']) && $cco['tras'] && !$nka ){
				
				//Si el articulo no se encuentra en la lista de articulos
				//a cargar para un paciente aparece un mensaje de alerta
				if( $poslist > -1 && !$regbool  && $dat[ $poslist ][5] == 'N' && $dat[ $poslist ][10] >= 100 ){
					echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
						 mostrarAlerta(\"El Articulo: {$art['cod']}-{$art['nom']}\\nes No POS y la cantidad autorizada por CTC ya esta dispensado\");
						 </script>";
				}
				
				
				//Si el articulo no se encuentra en la lista de articulos
				//a cargar para un paciente aparece un mensaje de alerta
				if( !$huboReemplazo &&  $poslist == -1 && !$regbool && !empty($art['cod']) && !empty( $art['nom'] ) ){
					echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
						 mostrarAlerta(\"El Articulo: {$art['cod']}-{$art['nom']}\\nno esta grabado al paciente\");
						 </script>";
				}

				//Si el articulo no se encuentra en la lista mostrada en pantalla
				//y se intentó guardar en el sistema aparece en pantalla una alerta
				//Octbure 11 de 2016. Se deshabilita el mensaje por que ya todo articulo se ve en la lista
				if( false && 2 < $poslist && $dat[ $poslist ][1] > 0 && !$regbool && !empty($art['cod']) ){
						echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
						 mostrarAlerta(\"El Articulo no se encuentra en la lista\");
						 </script>";
				}

				////2011-06-20
				//Mensaje temporal
				//Si fue guardado y no se encuentra en la lista (hago referencia a la lista que se ve en la PDA)
				//Octbure 11 de 2016. Se deshabilita el mensaje por que ya todo articulo se ve en la lista
				if( false && 2 < $poslist && $dat[ $poslist ][1] >= 0 && $regbool && !empty($art['cod']) ){

					if( $dat[ $poslist ][1] > 0 ){	//Si fue dispensado, no esta en la lista y aun tiene saldo para dispensar
						echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
							 mostrarAlerta(\"El Articulo \\n{$art['cod']}-{$art['nom']}\\nfue cargado al paciente\");
							 </script>";
					}
					elseif( $dat[ $poslist ][1] == 0 && $dat[ 0 ][1] > 0 && $dat[ 1 ][1] > 0 && $dat[ 2 ][1] > 0 ){	//Si no esta en la lista, fue dispensado y hay articulos en la lista de PDA
						// echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
							 // mostrarAlerta(\"El Articulo \\n{$art['cod']}-{$art['nom']}\\nfue cargado al paciente\");
							 // </script>";
					}
				}

				//Si el articulo no se encuentra en la lista mostrada en pantalla
				//y no se ve en pantalla y tiene saldo 0
				//Octbure 11 de 2016. Se deshabilita el mensaje por que ya todo articulo se ve en la lista
				if( false && 2 < $poslist && $dat[ $poslist ][1] == 0 && !$regbool && !empty($art['cod']) && !empty( $art['nom'] ) ){
					echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
						  mostrarAlerta(\"Articulo \\n{$art['cod']}-{$art['nom']}\\nfue dispensado\");
						  </script>";
				}

				//Si el articulo esta en lista, tiene cantidad a dispensar mayor a 0
				//y la cantidad a guardar es superior a la cantidad a dispensar
				if( $poslist > -1 && $poslist < 3  && !$regbool && !empty($art['cod']) && !empty( $art['nom'] )
				 	&& $dat[$poslist][1] < $art['can']/$art['fra'] && !$art['cva'] ){
				 	echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
					  mostrarAlerta(\"El articulo\\n{$art['cod']}-{$art['nom']}\\nes especial, la cantidad por defecto\\nes mayor a la faltante por dispensar\");
					  </script>";
				}
			}
			else{
				$mmqDispensado = false;
				$poslist = enLista( $dat, $art );
				if( $nka ){
					$mmqDispensado = true;
					echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
					  mostrarAlerta(\"El Articulo \\n{$art['cod']}-{$art['nom']}\\nfue cargado al paciente\");
				      </script>";
				}
				else if( $cco['tras'] && $artValido && ( !$art['imp'] || ( $art['imp'] && !empty($lote) ) ) ){
					$mmqDispensado = true;
					if( !esUrgencias( $cco['cod'] ) ){
						// echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
						  // mostrarAlerta(\"El Articulo \\n{$art['cod']}-{$art['nom']}\\nes Material Medico Quirurgico\\ y fue cargado al paciente\");
						  // </script>";
					}
				}
				
				/**************************************************************************************************************
				 * Octubre 10 de 2016
				 * Se crea un array con los campos necessario para pintar los MMQ grabados o articulos que no necesitan estar en 
				 * kardex para ser dispensados
				 **************************************************************************************************************/
				// $art['cva'] es cantidad variable y solo es TRUE cuando se requiere pedir la cantidad a dispensar
				if( !$art['cva'] && $mmqDispensado ){ 
					if( empty($listaMMQ[ $art['cod'] ]) ){
						$listaMMQ[ $art['cod'] ] = array();
					}
					@$listaMMQ[ $art['cod'] ]['codigo'] 	= $art['cod'];
					@$listaMMQ[ $art['cod'] ]['nombre'] 	= $art['nom'];
					@$listaMMQ[ $art['cod'] ]['cantidad'] 	+= $art['can'];
					@$listaMMQ[ $art['cod'] ]['unidad'] 	= $art['uni'];
				}
				/**************************************************************************************************************/
			}
		}
		else if( isset($art) && $tipTrans != "C" && $ke ){
			// if( $tipTrans != "C" && $poslist == -1 && $cco['tras'] && !empty($art['nom']) && !empty($art['cod']) && $mostrarMensaje ){
				// echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
					  // mostrarAlerta(\"El Articulo \\n{$art['cod']}-{$art['nom']}\\nno esta cargado al paciente\");
				      // </script>";
			// }

			if( $tipTrans != "C" && $poslist > -1 && $cco['tras'] && $dat[$poslist][3] == 0 && !$regbool ){
				echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
					  mostrarAlerta(\"El articulo ya fue devuelto\");
				      </script>";
			}

			if( $tipTrans != "C" && $poslist > -1 && $cco['tras'] && $dat[$poslist][3] < $art['can']/$art['fra']
			    && !$art['cva'] && !$regbool && $dat[$poslist][3] > 0 ){
				echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
					  mostrarAlerta(\"La cantidad a devolver es mayor a la cargada\");
				      </script>";
			}
		}

		echo "<input type='hidden' name='usuario' value='".$usuario."'>";
		echo "<input type='hidden' name='tipTrans' value='".$tipTrans."'>";
		echo "<input type='hidden' name='emp' value='".$emp."'>";

//		echo "<input type='hidden' name='pac[nom]' value='".$pac['nom']."'>";
		echo "<input type='hidden' name='pac[hac]' value='".$pac['hac']."'>";
		echo "<input type='hidden' name='pac[sac]' value='".$pac['sac']."'>";
		echo "<input type='hidden' name='pac[his]' value='".$pac['his']."'>";
		echo "<input type='hidden' name='pac[ing]' value='".$pac['ing']."'>";
//		echo "<input type='hidden' name='pac[act]' value='1'>";
		echo "<input type='hidden' name='pac[dxv]' value='".$pac['dxv']."'>";
		echo "<input type='hidden' name='classHis' value='".$classHis."'>";

		echo "<input type='hidden' name='pac[ke]' value='".$pac['ke']."'>";
		echo "<input type='hidden' name='pac[con]' value='".$pac['con']."'>";
		echo "<input type='hidden' name='pac[mue]' value='".$pac['mue']."'>";

		echo "<input type='hidden' name='cargarmmq' value='".$cargarmmq."'>";
		echo "<INPUT type='hidden' name='fecDispensacion' value='$fecDispensacion'>";
		echo "<INPUT type='hidden' name='fechorDispensacion' value='$fechorDispensacion'>";

		if( isset($alp) ){
			echo "<input type='hidden' name='alp' value = 'on'>";
		}

		echo "<input type='hidden' name='ke' value='".$ke."'>";
		if (isset($car))
		{
			echo "<input type ='hidden' name='car' value='on'>";
		}


		echo "<input type='hidden' name='cco[cod]' value='".$cco['cod']."'>";
		echo "<input type='hidden' name='cco[nom]' value='".$cco['nom']."'>";
//		echo "<input type='hidden' name='cco[nom]' value='".$cco['tras']."'>";

		if($cco['neg'])//2007-06-18 No se enviaban número se enviaban boooleanos, por ello se crea el if
		{
			echo "<input type='hidden' name='cco[neg]' value='1'>";
		}
		else
		{
			echo "<input type='hidden' name='cco[neg]' value=''>";
		}

		if($cco['asc'])//2007-09-12 Se crea
		{
			echo "<input type='hidden' name='cco[asc]' value='1'>";
			if($tipTrans == "C")
			{
				echo "<input type='hidden' name='fecApl' value='".$fecApl."'>";
				echo "<input type='hidden' name='ronApl' value='".$ronApl."'>";
			}
		}
		else
		{
			echo "<input type='hidden' name='cco[asc]' value=''>";
		}

		if($cco['apl'])//2007-06-18 No se enviaban número se enviaban boooleanos, por ello se crea el if
		{
			echo "<input type='hidden' name='cco[apl]' value='1'>";//Se aplica automáticamente
		}
		else
		{
			echo "<input type='hidden' name='cco[apl]' value=''>";// NO Se aplica automáticamente
		}

		if($cco['apr'])//2007-06-18 No se enviaban número se enviaban boooleanos, por ello se crea el if
		{
			echo "<input type='hidden' name='cco[apr]' value='1'>";//Permite Aprovechamientos
		}
		else
		{
			echo "<input type='hidden' name='cco[apr]' value=''>";//NO Permite aprovechamientos
		}
		echo "<input type='hidden' name='cco[fue]' value='".$cco['fue']."'>";//Fuente sencilla
		echo "<input type='hidden' name='cco[fap]' value='".$cco['fap']."'>";//Fuente de aprovechamientos
		echo "<input type='hidden' name='cco[hos]' value='".$cco['hos']."'>";//El centro de costos es hospitalario?
		echo "<input type='hidden' name='cco[phm]' value='".$cco['phm']."'>";//El centro de costos es hospitalario?
		echo "<input type='hidden' name='cco[urg]' value='".$cco['urg']."'>";//El centro de costos es de urgencias?
		echo "<input type='hidden' name='cco[ayu]' value='".$cco['ayu']."'>";//El centro de costos es de urgencias?
		
		
		echo "<input type='hidden' name='ejecutarProcesosUnix' value='".$ejecutarProcesosUnix."'>";//El centro de costos es de urgencias?

		
		
		
		
		
		
		
		
		
		/******************************************************************************************
		 * Octubre 10 de 2016
		 * Pintar la lista de articulo MMQ
		 ******************************************************************************************/
		if( !empty( $listaMMQ ) ){
			
			if( count( $listaMMQ > 0 ) ){
				
				echo "<tr class='titulo3'>";
				echo "<td align=center>";
				
				echo "<table>";
				
				echo "<tr class=titulo1>";

				echo "<td colspan=4> MATERIAL MMQ DISPENSADOS</td>";
				echo "</tr>";
				
				echo "<tr class=titulo1>";
				echo "<td>C&oacute;digo</td>";
				echo "<td>Nombre</td>";
				echo "<td colspan=2>Cantidad</td>";
				//echo "<td>Unidad</td>";
				echo "</tr>";
				
				foreach( $listaMMQ as $keyArticulo => $valueArticulo ){
					
					// $keyArticulo es el codigo del articulo
					// Hay un array por posición, es decir que $valueArticulo es un array y tiene los siguientes campos
					// nombre, codigo, cantidad y unidad

					echo "<tr class=fila1>";
					echo "<td><input type='text' name='listaMMQ[".$keyArticulo."][codigo]' readonly style='width:70px; border: 0; background: 0' value='".$valueArticulo['codigo']."'></td>";
					echo "<td><input type='text' name='listaMMQ[".$keyArticulo."][nombre]' readonly style='width:400px;border: 0; background: 0' value='".trim($valueArticulo['nombre'])."'></td>";
					echo "<td><input type='text' name='listaMMQ[".$keyArticulo."][cantidad]' readonly style='width:35px; text-align:right;border: 0; background: 0' value='".$valueArticulo['cantidad']."'></td>";
					echo "<td><input type='text' name='listaMMQ[".$keyArticulo."][unidad]' readonly style='width:40px; border: 0; background: 0' value='".$valueArticulo['unidad']."'></td>";
					echo "</tr>";
				}
				
				echo "</table>";
				echo "</td>";
				echo "</tr>";
			}
		}
		/******************************************************************************************/
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		echo "<tr><td class='".$class."'>";

		if( $cco['tras'] && $tipTrans == "C" && $cargados == "on" && $hayArticulosPorDispensar ){
			echo "<A HREF='cargos.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&cbCrearPeticion=on&ccoCam={$cco['cod']}&hab={$pac['hac']}&solicita=$usuario&origen={$cco['cod']}&destino={$pac['sac']}&paciente={$pac['nom']}&opcionRetornar=1&ultPac={$pac['his']}-{$pac['ing']}&fecDis=$fecDispensacion&fechorDis=$fechorDispensacion'>Retornar Usuario</a> </font>";
		}
		else{
			echo "<A HREF='cargos.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ultPac={$pac['his']}-{$pac['ing']}&fecDis=$fecDispensacion&fechorDis=$fechorDispensacion'>Retornar Usuario</a> </font>";
		}

		if( $cco['tras'] && $tipTrans == "C" && $cargados == "on" && $hayArticulosPorDispensar ){
			echo "&nbsp; &nbsp;<A HREF='cargos.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ccoCod=UN.".$cco['cod']."&cbCrearPeticion=on&ccoCam={$cco['cod']}&hab={$pac['hac']}&solicita=$usuario&origen={$cco['cod']}&destino={$pac['sac']}&paciente={$pac['nom']}&opcionRetornar=2&ultPac={$pac['his']}-{$pac['ing']}&fecDis=$fecDispensacion&fechorDis=$fechorDispensacion'>Retornar Usuario+CC</a> </font>";
		}
		else{
			echo "&nbsp; &nbsp;<A HREF='cargos.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ccoCod=UN.".$cco['cod']."&ultPac={$pac['his']}-{$pac['ing']}&fecDis=$fecDispensacion&fechorDis=$fechorDispensacion'>Retornar Usuario+CC</a> </font>";
		}

		echo "</td></tr>";
		echo"<tr><td class='titulo3'><input type='submit' value='ACEPTAR' id='ACEPTAR'></td></tr></form>";

		echo "<tr><td class='".$class."'>";

		if( $cco['tras'] && $tipTrans == "C" && $cargados == "on" &&  $hayArticulosPorDispensar ){
				echo "<A HREF='cargos.php?tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&cbCrearPeticion=on&ccoCam={$cco['cod']}&hab={$pac['hac']}&solicita=$usuario&origen={$cco['cod']}&destino={$pac['sac']}&paciente={$pac['nom']}&opcionRetornar=3&ultPac={$pac['his']}-{$pac['ing']}&fecDis=$fecDispensacion&fechorDis=$fechorDispensacion'>Retornar</a>";
		}
		else{
			echo "<A HREF='cargos.php?tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ultPac={$pac['his']}-{$pac['ing']}&fecDis=$fecDispensacion&fechorDis=$fechorDispensacion'>Retornar</a>";
		}

		echo "&nbsp; &nbsp;<A HREF='reporte.php?pac[his]=".trim($pac['his'])."&amp;pac[ing]=".trim($pac['ing'])."&amp;cc=".$cco['cod']."&tipTrans=".$tipTrans."&usuario=".$usuario."&emp=".$emp."&bd=".$bd."'>Reporte dia paciente</a>";
		echo "</td></tr>";
	}
	else
	{
		echo "<tr><td class='errorTitulo'>";
		echo $warning;

		echo "</td></tr>";
		echo "<tr><td class='".$class."'>";
		echo "<A HREF='cargos.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ultPac={$pac['his']}-{$pac['ing']}&fecDis=$fecDispensacion&fechorDis=$fechorDispensacion'>Retornar con Usuario</a>&nbsp; &nbsp;";
		echo "<A HREF='cargos.php?tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ultPac={$pac['his']}-{$pac['ing']}&fecDis=$fecDispensacion&fechorDis=$fechorDispensacion'>Retornar</a>";
		echo "</td></tr>";

		$art['cod']="NO APLICA";
		registrarError($odbc, $cco, $fuente,  0, 0, $pac, $art, $error, $color, $warning, $usuario);
	}
	
	
	if( $tipTrans == "C" ){
		echo "</table>";
		echo "</fieldset>";
		echo "<br><center><input type='button' value='Cerrar' style='width:100px' onClick='cerrarVentana();'></center>";
	}
	
	odbc_close_all();
}
}
else
   {
   ?>
    <script>
        alert ("!!!! ATENCION !!!! En este momento no se puede hacer Devoluciones por INVENTARIO FISICO");
        document.forms.ventas.submit();
    </script>
   <?php
   }

echo "</table>";

if( $tipTrans != "C" ){
	echo "<br><center><input type='button' value='Cerrar' style='width:100px' onClick='cerrarVentana();'></center>";
}

echo "</form>";


}

if( !isset($facturacionErp) ){
	echo "</body>";
	echo "</html>";
}

// grabarArticuloPorPda( "0104686", "1018", "C", "01", "581834", "B2BA04", 1, "2016-06-07", "09",$num, $lin, &$warning );
    // // grabarArticuloPorPda( "0104686", "1016", "D", "01", "20272", "N1AB05", 1, "2015-02-01", "09", $num, $lin, &$warning );
// echo $num."-".$lin."-".$warning;
?>
