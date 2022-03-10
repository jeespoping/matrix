<?php
$wemp_pmla = $_REQUEST['wemp_pmla'];
include_once("conex.php");
$accion_iq = "";
if(!empty($emp))
	$wemp_pmla = $emp;
$existeFacturacionERP = true;
$desde_CargosPDA = true;
?>
<html>
<head>
<title>CARGOS </title>

<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript">

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

function abrirVentana( path ){
	window.open( path, "nuevo", "directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no" );
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
						
						setTimeout( function(){habilitarACEPTAR();}, 200 );
						
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

function redireccionar( url ){
	window.location = url;	
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
</script>

<?php
echo "<div id='dvTitle' style='display:none;position:absolute'></div>"; 
if(isset($historia))
{
	$pac['his']=$historia;
}
if(isset($artcod))
{
	$art['cod']=$artcod;
}

?>


<style type="text/css">    	
   	<!--Fondo Azul no muy oscuro y letra blanca -->
   	.tituloSup{color:#2a5db0;background:#FFFFF;font-family:Verdana;font-weight:bold;text-align:center;font-size:10pt;}
   	.tituloSup1{color:#c3d9ff;background:#FFFFF;font-family:Verdana;font-weight:bold;text-align:center;font-size:10pt;}
   	.titulo1{color:#FFFFFF;background:#2a5db0;font-family:Verdana;font-weight:bold;text-align:center;font-size:10pt;}
   	.titulo2{color:#003366;background:#c3d9ff;font-size:10pt;font-family:Verdana;text-align:center;}
   	.titulo3{color:#003366;background:#e8eef7;font-size:10pt;font-family:Tahoma;text-align:center;}
   	.titulo4{color:#ffffff;background:purple;font-size:10pt;font-family:Tahoma;text-align:center;}
   	.titulo5{color:#003366;background:pink;font-size:10pt;font-family:Verdana;text-align:center;}
   	.texto{color:#2a5db0;background:#FFFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
   	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   	.acumulado2{color:#003366;background:#FFDBA8;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
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
	
</style>

</head>
<BODY BGCOLOR="#FFFFFF" onSubmit='inhabilitarCampos()'>
<?php
/***********************************************************************************************************************************************************
 * NOTAS:
 * =======================================================================================================================================================
 * Fecha: Junio 10 de 216
 * Por: Edwin MG
 * -	Los articulos que se encuentren ordenados y sean del stock a excepción de:
 *  		2. Articulos LEV e IC:	Los articulos que fueron ordenados cómo LEV o IC puden no ser dispensables, estos no pueden ser aplicados por Ipods
 *									y por tanto deben ser aplicados automáticamente. Se tiene en cuenta que los articulos LEV e IC son una conformación de uno 
 *									o más articulos y se sabe que no son dispensables por pertenecer a una orden de articulos LEV o IC(movhos 171) 
 *									y  los articulos no dispensables están marcados en la tabla 000098 de movhos.
 * 		que se encuentran ordenados generan saldos al ser dispensados desde piso.
 * - 	Las Insulinas deben estar odenadas para poderse dipsenar. Estos son los articulos que se encuentran en la tabla de Stock(movhos 91) marcados 
 *		con tipo I (Insulina)
 * =======================================================================================================================================================
 ************************************************************************************************************************************************************/
 
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
 * @modified Agosto 06 de 2018  (Edwin)   	   - Las validaciones de paciente activo se hacen con respecto a matrix
 * @modified Marzo 01 de 2018  (Edwin)   	   - Se modifica script para que se inserte el cco de donde se dispenda y el nombre del médico tratante en la tabla de lotes(cliame 000240) 
 * 												 para articulos implantables
 * @modified Febrero 21 de 2018  (Edwin)   	   - Para los articulos implantables, al dispensarlos pide el lote
 * @modified Febrero 15 de 2018  (Edwin)   	   - Los cco de ayuda dx no agregan articulos al carro, para ello se modifica la función agregarAlCarro
 * @modified Febrero 5 de 2018  (Edwin MG)     - Si el paciente está en un cco de ayuda dx y tiene ordenes, se dispensa de acuerdo a las ordenes
 *											   - Se habilita aprovechamiento el cual solo estaba activo para cirugía (ver 2010-09-24)
 * @modified Noviembre 27 de 2017  (Edwin MG)  Se quitan las funciones consultarInsumosProducto y registrarInsumosProducto y se muevan al script cargosSF.inc.php en include/movhos.
 *											   Esto por que son funciones que se encuetran iguales en devoluciones.php, cargos.php en movhos/procesos y facilita el mantenimiento de las
 *											   aplicaciones.
 *											   Adcionalmente se corrige el calcula de la cantidad del insumo a facturar para las minibolsas en la función registrarInsumosProducto. 
 *											   Se estaba tomando como factor de conversión de unidades el valor encontrado en la tabla de Articulos especiales (movhos_000008) el campo CANTIDAD POR DEFECTO(Arecde)
 *											   según el cco en que se dispensa y el valor de conversión correcto es el campo CONVERSION(Appcnv) en la tabla RELACION DE INSUMOS POR PRESENTACION(cenpro_000009)
 * @modified Noviembre 21 de 2017 	(Edwin MG)  Los productos codificados o minibolsas no se desglosan(no se factura sus insumos) para los pacientes particulares
 * @modified Octbure 30 de 2017 	(Edwin MG)  No se permite dispensar articulos MMQ si es una ayuda dx qué comenzó con el programa de Dispensacioón de insumos botiquin
 * @modified Septiembre 13 de 2017 (Edwin MG)  Para cargar articulso en minibolsas, se tiene en cuenta el cco * en la configuación que significa que es para todos los responsables
 * @modified Septiembre 9 de 2017	(Edwin MG) - Si se graba desde un cco de ayuda, no se valida que el paciente este en cco de urgencias
 * @modified Agosto 29 de 2017		(Edwin MG) - No se impide cargar insumos para los cco de ayuda diágnotica
 *											   - Se deshabilita el boton aceptar al hacer clic
 * @modified Agosto 14 de 2017		(Edwin MG) Se impide cargar insumos por este programa si el cco está habilitado para dispensar insumos
 * @modified Octubre 26 de 2016		(Edwin MG) Se corrige para que al momento de cargar MMQ no sume en la lista cuando se está pidiendo la cantidad variable
 * @modified Octubre 25 de 2016		(Edwin MG) Se corrige para que no muestre en la lista de medicamentos a dispensar los articulos suspendidos y que no tengan saldo
 * @modified Octubre 03 de 2016		(Edwin MG) Se hace cambios para que se aplique automaticamente articulos desde cco de ayudas dx a piso de acuerdo al servicio temporal del paciente(ubiste)
 * @modified Agosto 01 de 2016		(Edwin MG) Se cambia query para que no facture los insumos de productos inactivos en central de mezclas
 * @modified Junio 29 de 2016		(Edwin MG) Los articulos cargados por piso y que generan saldo, validan el saldo para dejar dispensar o no. Si hay saldo suficiente para aplicar 
 *											   no deja dispensar.
 * @modified Junio 10 de 2016		(Edwin MG) Los articulos no dispensables LEV e IC se aplican automáticamente (ver la seccion notas)
 * @modified Agosto 04 de 2015		(Edwin MG) Se corrige validación al momento de cargar articulos equivalentes
 * @modified Febrero 06 de 2015		(Edwin MG) Se comentan todas las llamadas a la petición de camilleros a solicitud de Beatriz Orrego
 * @modified Enero 07 de 2015		(Edwin MG) Se crea función actualizarRegistros.
 * @modified Octbure 31 de 2014		(Edwin MG) Se agrega a la url que direcciona a cargospda_cpx.php alp(alta en proceso) y cargarmmg (carga MMQ). 	
 * @modified Septiembre 04 de 2014 	(Edwin MG) Despues de terminar de dispensar se muestra el total de articulos dispensados por la PDA
 * @modified Noviembre 21 de 2013 	(Edwin MG) Al momento de cargar un producto codificado (articulo que está tanto en CM como en SF) se facturan sus insumos.
 *											   También se puede configurar un medicamento a facturar en lugar de otro en la tabla de articulos especiales (movhos_000008)
 * @modified Noviembre 15 de 2013 	(Edwin MG) Se crea funcion consultar_procesoHorasGrabacion que cambia las horas para cargar medicamentos en los días de inventario
 *									La configuración de la activación está dado por la tabla root_000050.
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
 *								consultarAplicacionAutoArticuloStock, ya no se consulta la tabla 000008 de movimiento hospitalario sino la 000091
 * @modified Junio 11 de 2013 	(Mario Cadavid). Se agrega la condición !$pac['ptr'] en la validacion de cargos hacia urgencias 
 *								(ver cambio con fecha 2013-06-11). Esta condicion hace que si el paciente está en proceso de traslado y es de 
 *								urgencias, se permite hacer cargos desde otros centros de costo
 * @modified Junio 6 de 2013 	(Mario Cadavid). Se agregaron las funciones consultarRegistroKardexPorArticulo, consultarRegistroStockArticulo y consultarAplicacionAutoArticuloStock, las cuales sirven para validar antes de registrar la grabación o anulación del cargo. Se validan las siguientes reglas: si el artículo está en kardex y es un artículo del stock y es un artículo especial que no tiene aplicación automática => se obliga a que la condición sea que no se aplica lo que se carga. Pero si es anulación y se cumplen estas mismas reglas, no se hace la anulación.
 * @modified Abril 15 de 2013 	(Edwin MG). Se modifica el programa para que al cargar un producto, se carguen adicionalmente y automáticamente otros articulos con las siguientes caracteristicas
 *											- Los articulos adicionales no van al carro
 *											- Todo artículo adicional queda también aplicado automáticamente
 *											- Todo artículo adicional mueve el saldo de dicho artículo para el saldo del paciente
 *											- Se tiene en cuenta este proceso en la devolución del medicamento
 *											Los medicamentos adicionales están configurados en la tabla movhos_000153
 * @modified Marzo 13 de 2013 	(Edwin MG). Si un paciente fue dado de alta desde urgencias, se permite cargar medicamentos hasta a lo mas x horas desde que fue dado de alta. El tiempo x
 *											es parametrizado desde root_000051 como tiempoEgresoUrgencia.
 * @modified Febrero 25 de 2013 (Edwin MG). Cambios varios para cuando no hay conexión con UNIX. Entre ellos se registra el movimiento en tabla de paso
 *											y se mira los saldos en matrix y no en UNIX.
 * @modified Junio 27 de 2012		Para cco que no son de aplicacion automatica, y se carga el medicamento y queda aplicado, al devolver el medicamento, desaplica el articulo y hace la devolucion de la cantidad correspondiente
 * @modified Junio 3 de 2012		Dejaba grabar articulos suspendidos en el kardex, si tenia saldo de dispensacion ( cdi - dis )
 * @modified Mayo 17 de 2012		Solo se aplica automaticamente los medicamentos si el paciente se encuentra en un cco que es de aplicacion automatica quedando los saldos en la tabla 30 de movhos.
 *									Si un medicamento es especial y es de aplicacion automatica se carga y se descuenta de saldos del paciente (movhos_000004) y queda aplicado (movhos_000015)
 * @modified Mayo 14 de 2012		En el carro solo aparecen los medicamentos que son dispensados desde un cco de traslado (SF o CM) y reciban carro y no sean MMQ
 * @modified Mayo 9 de 2012			Se quita aplicacion automatica de MMQ.
 * @modified Mayo 9 de 2012			Cuando un medicamento es aplicado automaticamente queda con la ronda de aplicacion de la siguiente manera {Horamilitar:00} - {AM|PM}.
 * @modified Abril 26 de 2012		Se realizan cambio para el proceso de contingencia.
 * @modified Febrero 24 de 2012		Se corrige funcion actualizandoAplicacionFraccion al momento de actualizar la dosis en la aplicacion de un medicamento
 * @modified Noviembre 8 de 2011	Se graba la cantidad del medicamento segun la tabla de definicion de fraccion(movhos_000059) al aplicar un medicamento
 * @modified Septiembre 8 de 2011.	El grupo V00 ya no es material médico quirúrgico ya que no se encuentra en este grupo ningún médicamento que pueda ser considerado MMQ.
 *									Petición realizada por Beatriz Orrego el día 8 de septiembre.
 *									Se agrega variable $usuario a la funcion redireccionarACargosCpx, para que no vuelva a pedir usuario para cargos de ciclos de produccion.
 * @modified Julio 28 de 2011.	Si un piso esta funcionando con ciclos de produccion (indicado en la tabla movhos_000011, campo ccocpx), redirecciona al programa de cargospda_cpx.php, el cual
 * 								funciona de acuerdo a los ciclos de producción. 
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

/**********************************************************************************************************************************************************
 * FUNCIONES
 **********************************************************************************************************************************************************/
 
function tieneKardexAyuda( $conex, $wbasedato, $his, $ing, $codcco, $fecha ){
					
	$val = false;
	
	$sql = "SELECT * 
			  FROM ".$wbasedato."_000208
			 WHERE Ekxhis = '".$his."'
			   AND Ekxing = '".$ing."'
			   AND Ekxfec = '".$fecha."'
			   AND Ekxayu = '".$codcco."'
			 UNION
			SELECT *
			  FROM ".$wbasedato."_000209
			 WHERE Ekxhis = '".$his."'
			   AND Ekxing = '".$ing."'
			   AND Ekxfec = '".$fecha."'
			   AND Ekxayu = '".$codcco."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = true;
	}
	
	return $val;
}

function esPacienteServicioTemporal( $conex, $wbasedato, $his, $ing ){
	
	$val = false;
	
	$sql = "SELECT Ubiste 
			  FROM ".$wbasedato."_000018
			 WHERE ubihis = '".$his."'
			   AND ubiing = '".$ing."'
			   AND ubiste != ''
			   ";
		   
	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Ubiste' ];
	}

	return $val;
}
 
/**
 * Indica si un articulo es una insulina
 */ 
function esInsulinaPDA( $conex, $wbasedato, $art, $cco ){

	$val = false;
			   
	$sql = "SELECT * 
			  FROM ".$wbasedato."_000091
			 WHERE Arscod = '".$art."'
			   AND Arstip = 'I' 
			   AND Arsest = 'on'
			   AND Arscco = '".$cco."'
			   ";
		   
	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		$val = true;
	}

	return $val;
}
 
/************************************************************************
 * Busco la cantidad de saldo en piso, si no hay saldo en piso, se devuelve un booleano false
 * de lo contrario devuelve la cantidad de saldo que hay en piso
 ************************************************************************/
function consultarSaldoPDA( $conex, $wbasedato, $his, $ing, $art, $cco = '%' ){

	$val = false;

	//Central de mezclas tiene la caracteristica de que es de traslado, factura y con inventario en matriz (ccotra = ccofac = ccoima = on)
	//Servicio Farmaceutico tiene la caracteristica de que es de traslado, factura y no tiene inventario en matriz
	$sqlSaldo = "SELECT
					SUM( spauen - spausa ) as Saldo
				 FROM
					{$wbasedato}_000011 a,
					{$wbasedato}_000004 b
				 WHERE
					ccofac = 'on'
					AND spahis = '$his'
					AND spaing = '$ing'
					AND spaart = '$art'
					AND spacco = ccocod
					AND ccocod LIKE '".$cco."'
				 ";

	$resSaldo = mysql_query( $sqlSaldo, $conex ) or die( mysql_errno()." - Error en el query $sqlSaldo - ".mysql_error() );
	$numrowsSaldo = mysql_num_rows( $resSaldo );

	if( $numrowsSaldo > 0 ){
		$rows = mysql_fetch_array( $resSaldo );

		if( $rows['Saldo'] > 0 ){
			$val = $rows['Saldo'];
		}
	}

	return $val;
}
 
function cambiarDosisKardex( $conex, $wbasedato, $his, $ing, $art, $fechaKardex, $nuevadosis ){
	 			
	$val = false;
	
	//AND kadess = 'on'
	if( $nuevadosis > 0 ){
		$sql = "UPDATE ".$wbasedato."_000054, ".$wbasedato."_000070
				   SET kadcfr = Round( ".$nuevadosis."*Kadcma, 3 )
				 WHERE kadhis = infhis
				   AND kading = infing
				   AND kadart = infade
				   AND kadper = inffde
				   AND kadsus != 'on'
				   AND inffec = kadfec
				   AND kadhis = '".$his."'
				   AND kading = '".$ing."'
				   AND kadart = '".$art."'
				   AND kadfec = '".$fechaKardex."'";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
		
		if( mysql_affected_rows() > 0 ){
			$val = true;
		}
	}
	
	return $val;
}
 
/*********************************************************************************************************************
 * Actualiza el estado de los registros de la tabla DETALLE DE CARGOS (campo fdeubi) de todos los pacientes de los
 * último dos días
 * Creado: Enero 07 de 2015
 *********************************************************************************************************************/
function actualizarRegistros( $conex, $wbasedato ){

	global $wemp_pmla;

	$sql = "SELECT Fenhis, Fening			
			  FROM {$wbasedato}_000002 a, {$wbasedato}_000003 b
			 WHERE a.fecha_data BETWEEN '".date( "Y-m-d", time() - 24*3600 )."' AND '".date( "Y-m-d" )."'
			   AND fennum = fdenum
			   AND fenest = 'on'
			   AND fdeubi != 'UP'
		  GROUP BY 1, 2
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

	while( $rows = mysql_fetch_array( $res ) ){
		
		$pac = array();
		$array = array();
		
		$pac['dxv']=false;
		$pac[ 'his' ] = $rows[ 'Fenhis' ];
		$pac[ 'ing' ] = $rows[ 'Fening' ];
		$pac[ 'act' ] = infoPacientePrima( $pac, $wemp_pmla );
		actualizacionDetalleRegistros( $pac, $array );
	}
} 
 
 
 
/******************************************************************************************
 * Consulto si se debe ejecutar el proceso de horas de grabación
 ******************************************************************************************/
function consultar_procesoHorasGrabacion(){

	global $conex;
	global $wemp_pmla;

	// $sql = "SELECT
				// Emphin, Emphfi, Empfec 	
			// FROM
				// root_000050
			// WHERE
				// Empcod = '$emp'
				// AND Empest = 'on'
			// ";
	
	// $res =  mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_errno() );
	
	// if( $rows = mysql_fetch_array( $res ) ){
		
		// //Si la fecha actual está entre la hora inicial y final se incluye el proceso de grabación
		// if( time() >= strtotime( $rows['Empfec']." ".$rows['Emphin'] ) && time() <= strtotime( $rows['Empfec']." ".$rows['Emphfi'] )+3600 ){
			// include_once( "./proceso_horasGrabacion.php" );
		// }
	// }
	
	$rows = array();
	list( $rows['Empfec'], $rows['Emphin'], $rows['Emphfi'] ) = explode( ",", consultarAliasPorAplicacion( $conex, $wemp_pmla, "procesoHorasGrabacion" ) );
	
	$rows['Emphin'] .= ":00:00";
	// $rows['Emphfi'] .= ":00:00";
	
	//Si la fecha actual está entre la hora inicial y final se incluye el proceso de grabación
	// if( time() >= strtotime( $rows['Empfec']." ".$rows['Emphin'] ) && time() <= strtotime( $rows['Empfec']." ".$rows['Emphfi'] )+3600 ){
	if( time() >= strtotime( $rows['Empfec']." ".$rows['Emphin'] ) && time() <= strtotime( $rows['Empfec']." ".$rows['Emphin'] )+3600*$rows['Emphfi']+3600 ){
		
		include_once( "./proceso_horasGrabacion.php" );
	}
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
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_errno() );
	
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
function consultarValorPorAplicacion( $conex, $wemp_pmla, $aplicacion ){

	$val = '';

	$sql = "SELECT
				*
			FROM
				root_000051
			WHERE
				detapl = '$aplicacion'
				AND detemp = '$wemp_pmla'
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
	global $wemp_pmla;
	
	$contingencia = consultarValorPorAplicacion( $conex, $wemp_pmla, 'CONTINGENCIA' );
	
	if( $contingencia == 'on' ){
	
		$fechaContingencia = consultarValorPorAplicacion( $conex, $wemp_pmla, 'fechaContingencia' );
		$horaContingencia = consultarValorPorAplicacion( $conex, $wemp_pmla, 'horaContingencia' );
		$fechaGrabacionContingencia = consultarValorPorAplicacion( $conex, $wemp_pmla, 'fechaGrabacionContingencia' );
		$horaGrabacionContingencia = consultarValorPorAplicacion( $conex, $wemp_pmla, 'horaGrabacionContingencia' );
		
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


function consultarDosisEsquemaDextrometer( $conex, $bd, $pac, $art ){
	
	$val = false;
	
	//Dosis por dextrometer
	//Se busca la dosis más alta en caso de tener dextrometer
	$sql = "SELECT MAX( Inddos*1 ) as Dosis
			  FROM  ".$bd."_000070 a, ".$bd."_000071 b
			 WHERE Indhis =  '".$pac['his']."'
			   AND Inding =  '".$pac['ing']."'
			   AND Indfec =  '".date( "Y-m-d" )."'
			   AND Infhis =  Indhis
			   AND Infing =  Inding
			   AND Inffec =  Indfec
			   AND Infade =  '".$art['cod']."'
			   ";
			   
	$resMaxDosIns = mysql_query( $sql, $conex ) or die( mysql_errno()." - $sql - ".mysql_error() );
	if( $rowaxDosIns = mysql_fetch_array($resMaxDosIns) ){
		if( $rowaxDosIns[ 'Dosis' ] != 'NULL' ){
			$val = $rowaxDosIns[ 'Dosis' ];
		}
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
function consultarRegistroKardexPorArticulo( $art, $pac, &$rowsConsulta ){
	
	global $bd;
	global $conex;
	global $fecDispensacion;
	global $serviciofarmaceutico;
	
	$sql = "SELECT
				Kadart, Kadido, Kadess, Kadcfr, Kadcma, Kadper, Pertip, Defcas
			FROM
				{$bd}_000054 a, {$bd}_000043 b, {$bd}_000059 c
			WHERE
				Kadhis = '{$pac['his']}'
				AND Kading = '{$pac['ing']}'
				AND Kadart = '{$art['cod']}'
				AND Kadfec = '{$fecDispensacion}'
				AND Kadsus != 'on'
				AND Kadest = 'on'
				AND Kadper = Percod
				AND Defart = Kadart
				AND Defcco = '".$serviciofarmaceutico."'
				";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		while( $rows = mysql_fetch_array( $res ) ){
			
			$dosisDextrometer = consultarDosisEsquemaDextrometer( $conex, $bd, $pac, $art );
			
			if( $dosisDextrometer !== false ){
				$rows[ 'Defcas' ] = $dosisDextrometer;
			}
			
			$rowsConsulta = $rows;
			
			$esDispensable = esDispensableLEVIC( $conex, $bd, $art['cod'], $rows[ 'Kadido' ], $pac['his'], $pac['ing'] );
			
			$esDispensable = $esDispensable == 'on' ? true: false;
			
			//Si está como no enviar y no es dispensable se aplica automáticamente
			if( $rows['Kadess'] == 'on' && $esDispensable ){
				return false;
			}
		}
		
		return true;
	}
	else{
		return false;
	}
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
 * Consulta si un artículo está en el stock del centro de costo y tiene aplicación automática
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
 
/*********************************************************************************
 * Actualiza correctamente la fraccion segun la tabla de fraccciones por articulo
 * (movhos_000015) al aplicar un medicamento
 *
 * Noviembre 15 de 2011
 *********************************************************************************/
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
						Apldos = '".( ($art['can'])*$fraccionArticulo['fraccion'] )."'
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
 
/************************************************************************************************************
 * Indica si un centro de costos comenzo ya con el ciclo de producción
 * 
 * @param $cco
 * @return unknown_type
 ************************************************************************************************************/
function tieneCpx( $cco ){
	
	global $bd;
	global $conex;
	
	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$bd}_000011
			WHERE
				ccocod = '$cco'
				AND ccocpx = 'on'
				AND ccoest = 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en elquery $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$val = true;
	}
	
	return $val;
}

/************************************************************************************
 * redirecciona la pagina al programa de cargos de ciclos de producccion
 * @param $cco
 * @param $fechaDispensacion
 * @param $historia
 * @return unknown_type
 ************************************************************************************/
function redireccionarACargosCpx( $cco, $fechaDispensacion, $historia = '', $usuario = '' ){
	
	global $bd;
	global $wemp_pmla;
	global $tipTrans;
	global $alp;
	global $cargarmmq;

	$addUrlAlp = "";
	if( !empty($alp) ){
		$addUrlAlp = "&alp=on";
	}
	
	$addUrlMmq = "";
	if( !empty($cargarmmq) ){
		$addUrlMmq = "&cargarmmq=$cargarmmq";
	}
	
	$url="cargospda_cpx.php?wemp_pmla=$wemp_pmla&bd=$bd&tipTrans=$tipTrans&fecDispensacion=$fechaDispensacion&cco[cod]=$cco&historia=$historia&usuario=$usuario&fechorDispensacion=".date( "Y-m-d H:i:s" ).$addUrlAlp.$addUrlMmq;
	
	echo "<script>";
	echo "redireccionar('".$url."');";
	echo "</script>";	
}

/**********************************************************************************************
 * Si se hace una aplicacion se actualiza el campo Unidad de fraccion y cantidad de fraccion
 * segun el kardex en la tabla 15 de movhos
 * 
 * @return unknown_type
 **********************************************************************************************/
function actualizandoAplicacion( $idKardex, $cco, $art, $num, $lin ){
	return;
	global $conex;
	global $bd;
	
	$sql = "SELECT
				Kadhis, Kading, Kadcfr, Kadufr
			FROM
				{$bd}_000054
			WHERE
				id = '$idKardex'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		$sql = "UPDATE
					{$bd}_000015
				SET
					Aplufr = '{$rows['Kadufr']}',
					Apldos = '{$rows['Kadcfr']}'
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
			"; //echo "<br><br>$camillero.....".$sql;
	
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
		//y hora actual es mayor or meno a fecha_data y hora_data de movhos 000011
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

function preCondicionesKE( $pac, $art, $ke, $artValido, $tipTrans, $cco, &$nka ){
	
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
//				$regbool = true; //registrarArticuloKE( $art, $pac );
//			}
			
			//2011-06-20 
			if( $ke && $artValido && $tipTrans == "C" && $poslist > -1 
				&& !$art['cva'] && !$listartpac[$poslist][4] && $listartpac[$poslist][1] >= $art['can']/$art['fra'] )
			{
				$regbool = true; //registrarArticuloKE( $art, $pac );
				
				//Si es POS, solo se puede grabar si tiene articulos aprobados por CTC
				if( $listartpac[$poslist][5] == 'N' && $listartpac[$poslist][7] && $listartpac[$poslist][7] >= 100 ){
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
				* 
			FROM
				{$bd}_000008
			WHERE
				areces = '{$art['cod']}'
				AND arecco = '{$cco['cod']}'";
				
	$res = mysql_query( $sql, $conex );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
//		$sql = "SELECT
//					deffra 
//				FROM
//					{$bd}_000059
//				WHERE
//					defart = '{$art['cod']}'
//					AND defest = 'on'
//					AND deffru = '{$art['uni']}'
//					AND defcco = '{$cco['cod']}'";
//
//		$res1 = mysql_query( $sql, $conex );
//		
//		if( $rows1 = mysql_fetch_array( $res1 ) ){
//			$frac = $rows1['deffra'];
//		}
		$frac = $rows['Arecde'];
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
 ****************************************************************************************************************/
function pintarTabla( $datos, $class, $filas = 3, $columnas = 2 ){
	// var_dump( $datos );
	if( $datos['error'][0] ){
		echo "<table align='center' class='$class' cellspacing=0>";
		
		echo "<tr class=encabezadotabla>";
		echo "<td colspan=2>MEDICAMENTOS</td>";
		echo "<td colspan=2>Cantidad</td>";
		echo "</tr>";
		
		for( $i=0; $i < $filas; $i++){
			
			if( $datos[$i][1] > 0 ){
				echo "<tr>";
			
				$color = "class='colorAzul4'";
				if( $i%2 == 0)
					$color = "class='colorAzul5'";
					
				if( $datos[$i][5] == 'N' ){
					$color = "bgcolor='yellow'";
				}
				
				if( $datos[$i][7] ){
					$color = "class=fondoAmarilloOscuro";
				}
			
				//Código del articulo
				echo "<td $color style='color:black;text-align:center;width:100px;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][0]} (T:{$datos[$i][6]},G:{$datos[$i][7]})'>".trim($datos[$i][2])."</td>";
				
				//Nombre del articulo
				echo "<td $color style='text-align:left;color:black;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][0]}'>".trim($datos[$i][0])."</td>";
				
				//Cantidad
				echo "<td $color style='color:black;text-align:right' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][0]}'>".trim($datos[$i][1])."</td>";
				
				//Unidad
				echo "<td $color style='color:black;text-align:left' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][0]}'>".trim($datos[$i][6])."</td>";
					
				echo "</tr>";
			}
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
}

/*function pintarTabla( $datos, $class, $filas = 3, $columnas = 2 ){
	
	if( $datos['error'][0] ){
		echo "<table align='center' class='$class' width='95%' cellspacing=0>";
		for( $i=0; $i < $filas; $i++){
			echo "<tr>";
		
			$color = "class='colorAzul4'";
			if( $i%2 == 0)
				$color = "class='colorAzul5'";
				
			if( $datos[$i][5] == 'N' ){
				$color = "bgcolor='yellow'";
			}
		
			for( $j = 0; $j < $columnas; $j++ ){
				if( $j == 1 )
					echo "<td $color style='color:black;text-align:right;' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][$j]}'>".substr($datos[$i][$j],0,27)."</td>";
				else
					echo "<td $color style='color:black;text-align:left' onMouseDown='mostrar(this);'  title='{$datos[$i][2]}-{$datos[$i][$j]}'>".substr($datos[$i][$j],0,27)."</td>";
			}
			echo "</tr>";
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
}*/

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
	
	global $conex;
	global $bd;
	global $cco;
	global $centraldemezclas;
	global $fecDispensacion;
	global $wemp_pmla;
//	global $wcenmez;
	
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
				karest = 'on'
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
				karest = 'on'
	        GROUP BY kadart, kadsus
	        ORDER BY artubi, artcom ASC)";
				
	$sql = "(SELECT 
				sum(kadcdi), sum(kaddis), kadart, artcom, kadsus, artpos, Artubi, Kadpri, Artuni
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
	        GROUP BY kadart)
	        UNION
	        (SELECT 
				sum(kadcdi), sum(kaddis), kadart, artcom, kadsus, artpos, Artubi, Kadpri, Artuni
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
	        GROUP BY kadart, kadsus)
	        ORDER BY Kadpri DESC, Artubi ASC, artcom ASC";

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
				$datos[$i][6]=$rows['Artuni'];;
				$datos[$i][7]=consultarCTCAgotado( $conex, $bd, $pac['his'], $pac['ing'], $rows[2], $wemp_pmla );
				$i++;
			}
			else{
				$vacios[$j][0]=$rows[3];
				$vacios[$j][1]=$rows[0]-$rows[1];
				$vacios[$j][2]=$rows[2];
				$vacios[$j][3]=$rows[1];
				$vacios[$j][5]=$rows[5];
				$datos[$i][6]=$rows['Artuni'];;
				$datos[$i][7]=consultarCTCAgotado( $conex, $bd, $pac['his'], $pac['ing'], $rows[2], $wemp_pmla );
				
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
			$datos[$i][7]=false;
			$i++;
			$tag = false;
		}
	}
	
	//Octubre 25 de 2016. Se comentan estas líneas por que ya no se debe mostrar los articulos suspendidos al usuario o que no tengan saldo a dispensar
	// for( $i = 0; $i < count( $vacios ); $i++ ){
		// $datos[ count( $datos) ] = $vacios[$i];
	// }
	
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
 ********************************************************************************************************/
function registrarArticuloKE( $art, $pac, $trans = "C", &$idRegistro, $tras = true, $num, $lin, $aplicado ){
	
	$art['fra'] = 1; 	//La fracción debe ser siempre uno para este caso, ya que la fracción existe en la 59
	
	global $conex;
	global $bd;
	global $cco;
	global $centraldemezclas;
	global $fecDispensacion;
	global $usuario;
	
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
		       		AND karcon = 'on'
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
									
		//Se suspende la grabacion por la misma persona que lo grabo por tal motivo no se borra el query anterior						
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
		       		AND karcon = 'on'
		       		AND karest = 'on'
		       		AND kadare = 'on'
				GROUP BY kadart";
					
//		$sql = "SELECT
//				kaudes
//			FROM
//				{$bd}_000055
//			WHERE
//				fecha_data = '".$fecDispensacion."'
//				AND kaudes like '%{$art['cod']}%'
//				AND seguridad = 'A-$codigoRegente'
//				AND kaumen = 'Articulo aprobado'
//				AND kauhis = '{$pac['his']}'
//				AND kauing = '{$pac['ing']}'
//			"; echo ".......<pre>$sql</pre>";
				
		$resid = mysql_query( $sqlid, $conex ) or die( mysql_errno()." - Error en el query $sqlid - ".mysql_error() );

		if( $row = mysql_fetch_array( $resid ) ){
			$id = $row[0];
		}
		else{
			$row[0] = "";
		}
		
		$idRegistro = $row[0];

		$sql = "SELECT
					id, kadcdi-kaddis
				FROM
					{$bd}_000054
				WHERE
					id = '{$row[0]}'";
					
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		$row = mysql_fetch_array( $res );
		
		
		if( $row[1] < ($art['can']/$art['fra']) ){
			
			$art['can'] = ($art['can']/$art['fra']-$row[1])*$art['fra'];
			
			//Actualizando registro con el articulo cargado hora y cantidad dispensada
			$sql = "UPDATE
						{$bd}_000054
			       	SET 
			       		kaddis = kaddis+".$row[1].",
			       		kadhdi = '".date("H:i:s")."'
			        WHERE 
			        	kadcdi >= kaddis+".$row[1]." AND 
			       		kadart = '{$art['cod']}' AND 
			       		kadhis = '{$pac['his']}' AND  
			       		kading = '{$pac['ing']}' AND 
			       		kadori = '$ori' AND
			       		kadsus != 'on' AND
			       		kadfec = '$fecDispensacion' AND
			       		kadare = 'on' AND 
			       		id = {$row[0]}";
						
//				$sql = "UPDATE
//						{$bd}_000054
//			       	SET 
//			       		kaddis = kaddis+".$row[1].",
//			       		kadhdi = '".date("H:i:s")."'
//			        WHERE 
//			        	kadcdi >= kaddis+".$row[1]." AND 
//			       		kadart = '{$art['cod']}' AND 
//			       		kadhis = '{$pac['his']}' AND  
//			       		kading = '{$pac['ing']}' AND 
//			       		kadori = '$ori' AND
//			       		kadsus != 'on' AND
//			       		kadfec = '$fecDispensacion' AND
//			       		id = {$row[0]}";
	
			$res = mysql_query( $sql, $conex );
	
			if( $res && mysql_affected_rows() > 0 ){
				return registrarArticuloKE( $art, $pac, $trans, $a, $num, $lin, $aplicado );
			}
			else{
				return false;
			}
		}
		else{
			//Actualizando registro con el articulo cargado hora y cantidad dispensada
			$sql = "UPDATE
						{$bd}_000054
			       	SET 
			       		kaddis = kaddis+".$art['can']/$art['fra'].",
			       		kadhdi = '".date("H:i:s")."'
			        WHERE 
			        	kadcdi >= kaddis+".($art['can']/$art['fra'])." AND 
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
				if( !$aplicado ){
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
					max(id), kaddis
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
				";

		$res = mysql_query( $sql, $conex );
		
		if( $rows = mysql_fetch_array( $res ) ){
			
			if( $rows[1] < $art['can']/$art['fra'] ){

				$dev2 = true;
				$art1 = $art;
				$art1['can'] = ($art['can']/$art['fra'] - $rows[1])*$art['fra'];
				 
				$art['can'] = $rows[1]*$art['fra'];
			}

			//Si la cantidad a dispensar queda en 0
			//La hora de dispensacion debe quedar en 00:00:00
			if( $rows[1] == $art['can']/$art['fra'] ){
			
				$sql = "UPDATE
							{$bd}_000054
						SET
							kaddis = kaddis-".$art['can']/$art['fra'].",
							kadhdi = '00:00:00'
						WHERE
							id='{$rows[0]}'";
			}
			else{
				$sql = "UPDATE
							{$bd}_000054
						SET
							kaddis = kaddis-".$art['can']/$art['fra']."
						WHERE
							id='{$rows[0]}'";
			}
						
			$result = mysql_query( $sql, $conex );
			
			if( $result && mysql_affected_rows() > 0 ){
				
				if( $dev2 ){
					return registrarArticuloKE( $art1, $pac, "D", $a, true, $num, $lin, $aplicado );
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
	        	AND karcon = 'on'
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

	$q = "SELECT 
				Ccourg 
		 	FROM 
		 		".$bd."_000011
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


$db = $_REQUEST['db'];


include_once("ips/funciones_facturacionERP.php");

//$wtitulo = "DISPENSACION ARTICULOS";
//$wactualiz = "Octbure 10 de 2016";
//encabezado($wtitulo, $wactualiz, 'clinica');

$wactualiz = '2022-02-21';
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
encabezado("DISPENSACION ARTICULOS",$wactualiz, $wbasedato1);

if( !$existeFacturacionERP )
	unset($facturacionErp);

$serviciofarmaceutico = ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
$centraldemezclas = ccoUnificadoCM(); //Se obtiene el Codigo de Central de Mezclas


$ccoSF=ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
$ccoCM=ccoUnificadoCM(); //Se obtiene el Codigo de Central de Mezclas

echo "<center><table border='0'>";

//$q = " SELECT detapl, detval, empdes "
//      ."   FROM root_000050, root_000051 "
//      ."  WHERE empcod = '".$emp."'"
//      ."    AND empest = 'on' "
//      ."    AND empcod = detemp "; 
//  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
//  $num = mysql_num_rows($res); 
//  
//  if ($num > 0 )
//     {
//	  for ($i=1;$i<=$num;$i++)
//	     {   
//	      $row = mysql_fetch_array($res);
//	      
//	      if ($row[0] == "cenmez")
//	         $wcenmez=$row[1];
//	         
//	      if ($row[0] == "afinidad")
//	         $wafinidad=$row[1];
//	         
//	      if ($row[0] == "movhos")
//	         $wbasedato=$row[1];
//	         
//	      if ($row[0] == "tabcco")
//	         $wtabcco=$row[1];
//	         
//	      $winstitucion=$row[2];   
//         }  
//     }
//    else{
//       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
//    }

if ( $tipTrans == "C" || consultarhorario($horario,$wemp_pmla))
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
?>		
<form name="carga" action="" method=post>		
<?php
/*
 * 2008-09-22:: Modificacion cuando es de urgencias.
 */
$ccoUrgencias = consultarCcoUrgencias();

if( isset( $ultPac ) && !empty( $ultPac ) ){

	$ultPac = explode( "-", $ultPac );
	if( !empty($fecDis) ){
		grabarUsuarioEncabezadoKardexPDA( $conex, $bd, $fecDis, $ultPac[0], $ultPac[1], '' );
	}
}

if( !isset($cargados) ){
	$cargados = "off";	//Indica si se ha cargado algun articulo
}

$nka = false;
if( !isset( $cargarmmq ) ){
	$cargarmmq = 'off';
}

if (isset($user) and !isset($usuario)){
	$usuario=substr($user,1);
}

$wcenpro = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
$ccoDispensaInsumos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "ccoHabilitadosDispensacionInsumos" );
$ccoDispensaInsumos = explode( ",", $ccoDispensaInsumos );


if( isset( $cbCrearPeticion ) && $cbCrearPeticion == 'on' ){
	
	// peticionCamillero( $cbCrearPeticion, $ccoCam, $hab, $solicita, $origen, $destino, $paciente  );
	
	// echo "<tr>";
	// echo "<td class='titulo2'>SE HA SOLICITADO CAMILLERO</td>";
	// echo "</tr>";

	$can = cantidadArticulosDispensado( $conex, $bd, $ultPac[0], $ultPac[1], $fechorDis );
	echo "<tr>";
	echo "<td class='titulo2'><b>$can ARTICULOS DISPENSADOS</b></td>";
	echo "</tr>";
	
//	$cbCrearPeticion = '';
	
//	echo "<table align='center'>";
//	echo "<tr><td class='tituloSup'>";
//	
//	switch( @$opcionRetornar ){
//		case 1:
//			echo "<A HREF='cargos.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."'><b>Continuar</b></a>";
//			echo "<INPUT type='hidden' name='tipTrans' value='$tipTrans'>";
//			echo "<INPUT type='hidden' name='usuario' value='$usuario'>";
//			echo "<INPUT type='hidden' name='emp' value='$emp'>";
//			echo "<INPUT type='hidden' name='bd' value='$bd'>";
//			break;
//		
//		case 2:
//			echo "<A HREF='cargos.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."&ccoCod=".$ccoCod."'><b>Continuar</b></a>";
//			echo "<INPUT type='hidden' name='tipTrans' value='$tipTrans'>";
//			echo "<INPUT type='hidden' name='usuario' value='$usuario'>";
//			echo "<INPUT type='hidden' name='emp' value='$emp'>";
//			echo "<INPUT type='hidden' name='bd' value='$bd'>";
//			echo "<INPUT type='hidden' name='ccoCod' value='$ccoCod'>";
//			break;
//		
//		case 3;
//		default:
//			echo "<A HREF='cargos.php?tipTrans=".$tipTrans."&amp;emp=".$emp."&bd=".$bd."'><b>Continuar</b></a>";
//			echo "<INPUT type='hidden' name='tipTrans' value='$tipTrans'>";
//			echo "<INPUT type='hidden' name='emp' value='$emp'>";
//			echo "<INPUT type='hidden' name='bd' value='$bd'>";
//			break;
//	}
//	
//	echo "</td></tr></table>";
//	echo "</table>";
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
			$ok=getCco($cco,$tipTrans,$wemp_pmla);
			echo "<input type='hidden' name ='cco[cod]' value='".$cco['cod']."' >";
			$cco['sel']	= true;
			$ok=true;
		}
		else
		{
			$cco['sel']	= true;
			$ok=true;
			
			/*No esta el CC en Matrix*/

			// $pac['his']='0';
			// $art['cod']="NO APLICA";
			// $error['codInt']="0002";
			// $cco['cod']='0000';
			// if($err == "")
			// {
				// $error['codSis']=mysql_errno($conex);
				// $error['descSis']=str_replace("'","*",mysql_error($conex));
			// }
			// else
			// {
				// $error['codSis']=$err;
				// $error['descSis']=$err;
			// }

			// //registrarError("NO INFO", $cco, 0, 0, $pac, $art, $error, &$color, &$warning);
			// registrarError('NO INFO',$cco,'NO INFO','0', '0',$pac,$art,$error, &$color,$warning,$usuario);
			// $printError="<CENTER>EL CODIGO DE USUARIO NO EXISTE";
			// $ok=false;
		}
	}
	else
	{
		//Determinar que el centro de costos haya sido leido.
		$pos=strpos(strtoupper($ccoCod),"UN.");
		if($pos === 0)//Tiene que ser triple igual por que si no no funciona
		{
			$cco['cod']=substr($ccoCod,3);
			if(!getCco($cco,$tipTrans,$wemp_pmla))
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
			//echo "<tr><td class='titulo1'>USUARIO: ".$usuario;
			
			
			//Información del usuario
	
			$userPDA = consultarUsuario($conex,$usuario);
			
			echo "<td>";
			
			echo "<table>";
			echo "<tr>";
			
			echo "<td style='width:400px;'>";
			echo "<fieldset style='border: 2px solid #e0e0e0;height:40px;'>";
			echo "<legend>Informaci&oacute;n del usuario</legend>";
			
			
			echo "<div style='margin:0 auto;'>";
			echo "<table style='margin: 0 auto;'>";
			
			//Usuario
			// echo "<tr>"; 
			echo "<td class='titulo2' style='text-align:right;'><b>USUARIO:</b></td>"; 
			echo "<td class='titulo3' style='text-align:left;'> ".$usuario." - ".$userPDA->descripcion."</b></td>";
			// echo "</tr>";
			
			echo "</table>";
			
			echo "</div>"; 
			echo "</fieldset>";
			
			echo "</td>";
			
			
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
			// echo "<tr><td class='tituloSup'>".$cco['cod']."-".$cco['nom']."</td></tr>";
			// echo "<tr><td class='tituloSup'>USUARIO: ".$usuario."</b>";
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
						// echo "<tr><td class='tituloSup'>Aplicación ".$fecApl." ".str_replace(" ", "",$ronApl)."</b>";
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
					?>
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
							echo "<option selected>".gmdate( "H", intval($hora/2)*2*3600 )."</option>";
							// echo "<option selected>".$hora."</option>";
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
			}
			else
			{
				$preguntarHis=true;
			}

			if($preguntarHis)
			{
				
				//Información del usuario
	
				$userPDA = consultarUsuario($conex,$usuario);
				
				echo "<td>";
				
				echo "<table>";
				echo "<tr>";
				
				echo "<td>";
				echo "<fieldset style='border: 2px solid #e0e0e0;height:40px;'>";
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
				echo "<td class='titulo3' style='text-align:left;'> ".$usuario." - ".$userPDA->descripcion."</b></td>";
				// echo "</tr>";
				
				echo "</table>";
				
				echo "</div>"; 
				echo "</fieldset>";
				
				echo "</td>";

				echo "<tr>";
				echo "<td class='".$class."' ><b>N° HISTORIA: ";
				?>	<input type='text' size='9' name='historia'>	
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
		echo"<tr><td  class='".$class."'><input type='submit' value='ACEPTAR' id='ACEPTAR'></td></tr></table>";
		echo "<br><center><input type='button' value='Cerrar' style='width:100px' onClick='cerrarVentana();'></center>";
		echo"</form>";
		
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
		ECHO "<BR/><B><A HREF='cargospda.php?tipTrans=".$tipTrans."&wemp_pmla=".$wemp_pmla."&bd=".$bd."'>Retornar</a>";
		echo "</td></tr></table>";
	}
}
else{

	if(!isset($cco['fap'])){
		/*Busqueda de los datos del Centro de Costos*/
		getCco($cco,$tipTrans, $wemp_pmla);
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
			
			$pac['act']=infoPacientePrima($pac,$wemp_pmla);
			if( !$pac['act'] ){
				$warning= "NO EXISTE UN PACIENTE ACTIVO CON ESA HISTORIA EN EL SISTEMA, INTENTELO NUEVAMENTE!!!";
			}
			
			/*Si el nombre no esta setiado es por que no es historia Cero o una historia por MATRIX*/
			// $conex_f = odbc_connect('facturacion','','');
			// $pac['act']=ValidacionHistoriaUnix(&$pac, &$warning, &$error);
			
			//Si el paciente está en un servicio temporal y entro a grabar con el mismo servicio temporal del paciente
			//Se trata al paciente cómo si estuviera en ese servicio.
			//Po tal motivo cambio el servicio del paciente
			$servicoTemporal = esPacienteServicioTemporal( $conex, $bd, $pac['his'], $pac['ing'] );
			if( $servicoTemporal ){
				if( $cco['cod'] == $servicoTemporal ){
					$pac['sac'] = $servicoTemporal;
				}
			}
			
			//2013-07-03
			// Se comenta el siguiente cambio ya que este cambio solo debe aplicar para central de mezclas
			//2013-06-26
			// if( $pac['ptr'] && isset( $pac['san'] ) && $pac['san'] == $ccoUrgencias )
				// $pac['sac'] = $pac['san'];
			/***********************************************************************************************
			 * Noviembre 12 de 2013
			 ***********************************************************************************************/					
			consultar_procesoHorasGrabacion();
			/***********************************************************************************************/	
			
			$ccoOrdenes = ( irAOrdenes( $conex, $bd, $wemp_pmla, $pac['sac'] ) == 'on' ) ? true: false;
				
			if( !$ccoOrdenes ){
				$ccoOrdenes = ( irAOrdenes( $conex, $bd, $wemp_pmla, $cco['cod'] ) == 'on' ) ? true: false;
			}
			
			$esAyudacpx = false;
			if( $cco['ayu'] ){
				$tieneKardexAyuda = tieneKardexAyuda( $conex, $bd, $pac['his'], $pac['ing'], $cco['cod'], date("Y-m-d") );
			}
			
			/************************************************************************************************
			 * Julio 28 de 2011 
			 * 
			 * Si el paciente esta en un centro de costos donde se maneja el ciclo, se direcciona el programa
			 * a cargos de ciclos de produccion (cargospda_cpx.php)
			 ************************************************************************************************/
			if( isset( $pac['sac'] ) ){
				if( $tieneKardexAyuda || ( tieneCpx( $pac['sac'] ) && $cco['tras'] == 'on' ) || ( $ccoOrdenes && tieneCpx( $pac['sac'] ) && pacienteKardexOrdenes( $conex, $bd, $pac[ 'his' ], $pac[ 'ing' ], date( "Y-m-d" ) ) && $cco['urg'] == 'on' ) ){
					redireccionarACargosCpx( $cco['cod'], $fecDispensacion, $pac['his'], $usuario );
				}
			}
			/************************************************************************************************/
			
			/************************************************************************************************
			 * Marzo 12 de 2013 
			 ************************************************************************************************/
			//Si está inactivo debo buscar la información del paciente
			if( true ){
				
				$pac2 = $pac;
				infoPacientePrima($pac2, $wemp_pmla);
				
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
							$egresoUrgencias = consultarValorPorAplicacion( $conex, $wemp_pmla, 'tiempoEgresoUrgencia' )*3600;
								
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
			
			/******************************************************************************************
			 * Grabo el usuario que esta intentando grabar por PDA
			 ******************************************************************************************/
			// grabarUsuarioEncabezadoKardexPDA( $conex, $wbasedato, $fecDispensacion, $pac['his'], $pac['ing'], $usuario );
			/******************************************************************************************/
			
			esKe( $pac );
//				$cco['tras'] = esTraslado( $cco['cod'] );
			// odbc_close($conex_f);
			
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
										if(isset($pac['ing'])){
											
											// 2013-06-26
											// if(($pac['sac'] == $ccoUrgencias || $pac['san'] == $ccoUrgencias) && pacienteIngresado($pac['his'],$pac['ing'])){
											
											//2013-07-03
											// Se devuelve el cambio anterior ya que este cambio solo debe aplicar para central de mezclas												
											if(($pac['sac'] == $ccoUrgencias || $pac['san'] == $ccoUrgencias) && pacienteConMovimientoHospitalario($pac['his'],$pac['ing']) && pacienteIngresado($pac['his'],$pac['ing'])){
												$entrar = true;
											}
										}

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
											}
										}
										else
										{
											$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." <BR> ESTA EN PROCESO DE TRASLADO, POR LO TANTO <BR> NO SE LE PUEDE NI CARGAR NI DEVOLVER <BR> NINGUN ARTÍCULO!!!";
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
								$warning = "EL PACIENTE CON HISTORIA:".$pac['his']." NO SE LE HA CONFIRMADO EL KARDEX ELECETRONICO!!!";
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
			if(getCco($ccoPac, $tipTrans, $wemp_pmla))
			{
				if($ccoPac['apl'])
				{
					$cco['apl']=true;
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

	
	if($pac['act']){
		
		if ($cco['apl'])
		{
			$color="titulo4";
		}
		else
		{
			$color="titulo2";
		}

		$warning="";
		if(isset($art['cod']))
		{
			// connectOdbc(&$conex_o, 'inventarios');
			
			if( true )
			{
				
				//xxxxxxxxxxxxxxxxxxxxxx
				
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
//					if( !$artValido ){
//						!$artValido = artExist( &$art, &$error );
//					}
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
									 * Junio 27 de 2012
									 ************************************************************************************************************************/
									//Si no hay saldo suficiente para devolver y no es de un cco de aplicacion automatica
									if( !$artValido && !$cco['apl'] ){
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
				
				// if( !empty($procesoContingencia) && $procesoContingencia == 'on' ){
					// $nka = true;
					// $val = true;
				// }				
				
				$mostrarValidacion = false;
				if( !$cco['ayu'] ){
					if( ( in_array( trim( $pac['sac'] ), $ccoDispensaInsumos ) || in_array( '*', $ccoDispensaInsumos ) ) && esMMQ( $art['cod'] ) ){
						$mostrarValidacion = true;
					}
				}
				else{
					if( ( in_array( trim( $cco['cod'] ), $ccoDispensaInsumos ) || in_array( '*', $ccoDispensaInsumos ) ) && esMMQ( $art['cod'] ) ){
						$mostrarValidacion = true;
					}
				}

				if( $mostrarValidacion ){
					$artValido = false;
					// $warning = "<div style='background-color:yellow;'>LOS INSUMOS DEBE DISPENSARLOS <a style='cursor:pointer;' onClick='abrirVentana(\"./botiquinDispensacionInsumos.php?wemp_pmla=".$emp."&slBotiquin=".$cco['cod']."\");' target='blank'><B>AQUI</B></a></div>";
					$warning = "<div style='background-color:yellow;'><label style='color:black;'>LOS INSUMOS DEBE DISPENSARLOS POR EL PROGRAMA DE <B>DISPENSACION DE INSUMOS</B></label></div>";
					$error['codInt']="9999";
					$error['codSis']='NO APLICA';
					$error['descSis']='NO APLICA'; 
				}
				else{
					
					$implantable = $art['imp'];
					if( $art['imp'] && !empty( $lote ) ){
						$implantable = false;
					}
					
					if( $val && $artValido and !$art['cva'] && !$implantable )
					{
						$msgNoInsulinaKardex = "<script>alert( 'El articulo debe estar ordenado por el medico\\n para poderlo dispensar' )</script>";
						
						$enKardex = false;
						if( $tipTrans == 'C' && $cco['cod'] != $serviciofarmaceutico ){
							//Si es una insulina en el stock, debe estar en el kardex para poderlo dispensar
							$esInsulina = esInsulinaPDA( $conex, $bd, $art['cod'], $cco['cod'] );
							if( $esInsulina ){	//Si es insulina
								$enKardex = consultarRegistroKardexPorArticulo( $art, $pac, $rowsInsulinaKardex );
								
								if( $enKardex ){
									//Se cambia la cantidad a cargar de acuerdo a la dosis prescrita por el médico
									$cantidadParaDosis = $rowsInsulinaKardex['Defcas']/$rowsInsulinaKardex['Kadcma'];
									if( strtoupper( $rowsInsulinaKardex['Pertip'] ) != 'I' ){
										$cantidadParaDosis = ceil( $rowsInsulinaKardex['Kadcfr']/$rowsInsulinaKardex['Kadcma'] );
									}
									
									//Verifico que no haya saldo suficiente, si no hay saldo suficiente se deja dispensar
									$saldoInsulina = consultarSaldoPDA( $conex, $bd, $pac['his'], $pac['ing'], $art['cod'], $cco['cod'] );
									
									if( $saldoInsulina*1 >= $cantidadParaDosis*1 ){
										//Cambio el mensaje
										$msgNoInsulinaKardex = "<script>alert( 'Hay saldo suficiente para aplicar el articulo.' )</script>";
										$enKardex = false;
									}
								}
							}
							else{	//Si es un articulo diferente a insulina
								$enKardex = consultarRegistroKardexPorArticulo( $art, $pac, $rowsInsulinaKardex );
								
								//Si está en kardex debe dejar grabar si no hay saldo suficiente para aplicar
								if( $enKardex ){
									//Se cambia la cantidad a cargar de acuerdo a la dosis prescrita por el médico
									$cantidadParaDosis = ceil( $rowsInsulinaKardex['Kadcfr']/$rowsInsulinaKardex['Kadcma'] );
									
									//Verifico que no haya saldo suficiente, si no hay saldo suficiente se deja dispensar
									$saldoArticulo = consultarSaldoPDA( $conex, $bd, $pac['his'], $pac['ing'], $art['cod'], $cco['cod'] );
								
									if( $saldoArticulo*1 >= $cantidadParaDosis*1 ){
										//Cambio el mensaje
										$msgNoInsulinaKardex = "<script>alert( 'Hay saldo suficiente para aplicar el articulo.' )</script>";
										$enKardex = false;
									}
								}
								else{
									//Si es un articulo normal debe dejar grabar aunque no este en kardex
									$enKardex = true;
								}
							}
							
							if( !$enKardex ){
								echo $msgNoInsulinaKardex;
							}
						}
						else{
							$enKardex = true;
						}
						
						if( $enKardex ){
							
							/*Buscar los consecutivos */
							$artValido=Numeracion($pac, $fuente, $tipTrans, $aprov, $cco, $date, $cns, $dronum, $drolin, $pac['dxv'], $usuario, $error );

							/*Registrar en UNIX*/
							if($artValido)
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
								}
								
								
								//Consulto las empresas a las que se requiere el cambio de articulo equivalente
								$responsablesEq = consultarAliasPorAplicacion( $conex, $wemp_pmla, "empresaConEquivalenciaMedEInsumos" );
								$tipoEmpresaParticular = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tipoempresaparticular" );
								$resPaciente = consultarResponsable( $conex, $pac['his'], $pac['ing'] );
								$admiteEquivalencia = false;
								
								if( $tipoEmpresaParticular != $resPaciente['tipoEmpresa'] ){
									$responsablesEq = explode( ",", $responsablesEq );
									$admiteEquivalencia = array_search( $resPaciente['responsable'], $responsablesEq ) === false ? false: true;
									$admiteEquivalencia = $admiteEquivalencia === false && array_search( '*', $responsablesEq ) !== false ? true: false;
								
									$reInsProducto = consultarInsumosProducto( $wcenpro, $bd, $art[ 'cod' ] );
								}
							
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
									
									CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );
									
									/************************************************************************************
									 * Febrero 27 de 2014
									 ************************************************************************************/
									if( !empty( $artEq ) && $artValido && $admiteEquivalencia ){
									
										registrarLogArticuloEquivalente( $conex, $bd, $auxArtEq, $art, $dronum, $drolin, 'off' );
										
										//Se hace un ajuste de entrada para cada uno de los insumos igual a la cantidad dispensado
										list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $wemp_pmla, "ajusteEntrada" ) );
										if( $tipTrans != 'C' ){
											list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $wemp_pmla, "ajusteSalidaDevolucion" ) );
										}
										ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
									}
									/************************************************************************************/
									
									$art = $auxArtEq;	//Noviembre 12 de 2013
									
									/************************************************************************************
									 * Febrero 27 de 2014
									 ************************************************************************************/
									if( !empty( $artEq ) && $artValido && $admiteEquivalencia ){	//Agosto 04 de 2015
										//Se hace un ajuste de Salida de inventario para el articulo que se va dispensar
										list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $wemp_pmla, "ajusteSalida" ) );
										if( $tipTrans != 'C' ){
											list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $wemp_pmla, "ajusteEntradaDevolucion" ) );
										}
										ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
									}
									/************************************************************************************/
								}
								else{
									registrarInsumosProducto( $reInsProducto, $cco, $dronum, $drolin, $fuente, $date, $pac, $art, $error, $tipTrans, $aprov );
									
									//Se hace un ajuste de entrada para cada uno de los insumos igual a la cantidad dispensado
									list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $wemp_pmla, "ajusteSalida" ) );
									if( $tipTrans != 'C' ){
										list( $fue, $concepto ) = explode( "-", consultarAliasPorAplicacion( $conex, $wemp_pmla, "ajusteEntradaDevolucion" ) );
									}
									ajustarInventario( $conex, $conex_o, $fue, $concepto, $cco[ 'cod' ], Array( 0 => $art ) );
									
									CargarCargosErp( $conex, $bd, "cliame", $art, $tipTrans, $dronum, $drolin );
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
										
										registrarLote( $conex, $wcliame, $cco['cod'], $art['cod'], $lote, $canLote, $devLote, $fechaLote, $horaLote, $usuario, $estadoLote, $pac['his'], $pac['ing'], 'on', $cco['cod'], $medTratante );
										
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
													actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $cco, $art2, $dronum, $ardrolin2[ $art2['cod'] ], $ccoSF );
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
										}
										
										$art['can'] = $auxArt['can'];
										
										/**************************************************************************************************************/
										$registroAplicacion = false;	//Mayo 6 de 2011
																	   //Febrero 19 de 2009
										if( $artValido and $cco['apl'] )
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
												
												actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $cco, $art, $dronum, $drolin, $ccoSF );	//Noviembre 8 de 2011
											}
										}
										else
										{
											// Valido si el artículo existe en kardex
											$artKardex = consultarRegistroKardexPorArticulo( $art, $pac, $rowsInsulinaKardex );
											$artStock = consultarRegistroStockArticulo( $art, $cco );
											$artAplicaAuto = consultarAplicacionAutoArticuloStock( $art, $cco );
												
											if( !isset($anularAplicacion) )
											{
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
													if( isset($art['apl']) and $art['apl']=="on" ){
														$art['apl']="off";
													}
												}
												
												// Julio 24 de 2013
												if($artAplicaAuto)
													$art['apl'] = "on";
												
												//Marzo 07 de 2016
												// Si es el articulo está en el kardex no se aplica automaticamente
												if( $artKardex && $artStock ){
													$art['apl'] = "off";
													$ascCco = false;
												}
												
												//Marzo 28 de 2011. Modificacion: Mayo 17 de 2012
												if ( $tipTrans == 'C' and ( $ascCco or (isset($art['apl']) and $art['apl']=="on") ) )	
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
														
														actualizandoAplicacionFraccion( $pac['his'], $pac['ing'], $cco, $art, $dronum, $drolin, $ccoSF );	//Noviembre 8 de 2011												
													}
												}
												// else{
													// if($artKardex && $artStock && !$artAplicaAuto){
														// //Si no se aplica automaticamente es por que es del stock
														
														// //Consulto el saldo del articulo
														// $nuevoSaldo = consultarSaldoPDA( $conex, $bd, $pac['his'], $pac['ing'], $art['cod'], $cco['cod'] );
														// cambiarDosisKardex( $conex, $bd, $pac['his'], $pac['ing'], $art['cod'], $fecDispensacion, $nuevoSaldo );
													// }
												// }
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
												
												if( $artKardex && $artStock ){
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
																					
											$regbool = registrarArticuloKE( $art, $pac, $tipTrans, $idRegistro, ( ( $cco['tras'] || $cco['urg'] )? true: false ), $dronum, $drolin, $registroAplicacion );
											
											/************************************************************************************************************************
											 * Mayo 6 de 2011
											 ************************************************************************************************************************/
											if( $tipTrans == "C" && $registroAplicacion && !empty($idRegistro) ){
												actualizandoAplicacion( $idRegistro, $cco, $art, $dronum, $drolin );
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
						}
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
			
				//xxxxxxxxxxxxxxxxxxxxxx
				
			}
			
		}
	}
	
	
	/**************************************************************************************************
	* Se terminan los procesos de comprobación de datos y artículos
	* A partir de aqui se pide y se muestra información en pantalla
	**************************************************************************************************/
	
	// echo "<tr><td class='tituloSup'>".$cco['cod']."-".$cco['nom']."</td></tr>";
	// echo "<tr><td class='tituloSup'>USUARIO: ".$usuario."</b>";
	
	// if(isset($cco['asc']) and $cco['asc'] and $tipTrans == "C")
	// {
		// echo "<tr><td class='tituloSup'>Aplicación ".$fecApl." ".str_replace(" ", "",$ronApl)."</b>";
	// }

	// if (isset($date) )
	// echo "<br>(".str_replace("-","/",$date)." ".date("H:i:s").")</td></font></tr>";
	// else
	// echo "<br>(".date('Y / m / d')." ".date("H:i:s").")</td></font></tr>";



	
	
	
	
	
	//Información del usuario
	
	$userPDA = consultarUsuario($conex,$usuario);
	
	echo "<td>";
	
	echo "<table>";
	echo "<tr>";
	
	echo "<td style='width:1000px;'>";
	echo "<fieldset style='border: 2px solid #e0e0e0;height:40px;'>";
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
	
	// //Ronda
	// // echo "<tr>";
	// echo "<td class='titulo2'><b>RONDA:</b></td>";
	// echo "<td class='titulo3'><b>".$rondaMaximaDispensacion."</b></td>";
	// // echo "</tr>";
	
	echo "</table>";
	
	echo "</div>"; 
	echo "</fieldset>";
	
	echo "</td>";
	
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
		
		$can = cantidadArticulosDispensado( $conex, $bd, $pac['his'], $pac['ing'], $fechorDis );
		
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
		
		$can = cantidadArticulosDispensado( $conex, $bd, $pac['his'], $pac['ing'], $fechorDis );
		
		echo "<tr>";
		echo "<td class='titulo2'><b>$can ARTICULOS DISPENSADOS</b></td>";
		echo "</tr>";
	}
	
	
	$dosInsulina = false;
	if( $tipTrans == "C" ){
		
		$dosisDextrometer = consultarDosisEsquemaDextrometer( $conex, $bd, $pac, $art );
		
		if( $dosisDextrometer ){
			if( $dosisDextrometer !== false && $dosisDextrometer > 0 ){
				$dosInsulina = $dosisDextrometer;
				
				//Verifico que no haya saldo suficiente, si no hay saldo suficiente se deja dispensar
				$saldoInsulina = consultarSaldoPDA( $conex, $bd, $pac['his'], $pac['ing'], $art['cod'], $cco['cod'] );
				if( $saldoInsulina > 0 && $dosInsulina > $saldoInsulina ){
					$dosInsulina -= $saldoInsulina;
				}
			}
			else{
				$dosInsulina = false;
			}
		}
	}
	
	if(isset($pac['act']) and $pac['act'] )
	{
		/*Si existe el numero de historia y el CC*/
		// echo "<tr><td class='$classHis'><b>".$pac['nom']." (".$pac['hac'].")</td></tr>";
		
		
		
		
		
		
		
		
		
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
				// echo "</td></tr>";
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
				}
				else {
					if($cco['apr'])
					{
						$inputAprov = "<input type='checkbox' name='aprov'>Aprov";
					}
					$inputAprov .= "<input type='hidden' name='aprovEstadoAnterior' value='0'>";
				}
			}
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
		$dat = ArticulosXPaciente( $pac );

		if(isset($art['cod']) and $artValido and ( $art['cva'] || ( $art['imp'] && empty($lote) ) ) )
		{
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
			
			// if( !$ke || $tipTrans != "C" || !$cco['tras'] ){
			if( false ){
				echo "<tr>";
				echo "<td class='".$color."'>".$warning."<b>ART: </font> ";

				?>
					<input type='text' name="artcod" size='14'>   
					<script language="JAVASCRIPT" type="text/javascript">
					document.carga.artcod.focus();
					</script>
				 <?php
				echo $inputAprov;
				echo "</td>";
				echo "</tr>";
			}
			 
			 
			 if(isset($cns) and $artValido and isset($dronum) and $dronum != "")
			 {
			 	/*En las variables $artPrevios y Show se acumulan los tres ultimos articulos ingresados al sistema
			 	Se separan los articulos y se seleccionan los ultimos 3*/
			 	if( $ke && $tipTrans == "C"  && $cco['tras'] ){
//			 		$regbool = registrarArticuloKE( $art, $pac );
//			 		$artMostrar = ArticulosXPaciente( $pac );
			 		// $dat = ArticulosXPaciente( $pac );
			 		$artPrevios="";
			 	}
			 	else{
			 		if( $tipTrans == "C" || ( $tipTrans != "C" && $val ) ){
			 			
			 			if( esNoPos( $art['cod'] ) && $tipTrans == "C" ){
							echo "<script>alert('EL ARTICULO\\n{$art['nom']}\\n ES NO POS');</script>";
						}
						
						$t=explode("*****",$artPrevios);//separa en una mtriz los articulos
						@$artPrevios="*****".$cns."//".$art['cod']."//".$art['nom']."//".$art['can']."//".$art['uni'].$artPrevios;
						@$artMostrar=$cns.")".$art['nom']." Cant.(".$art['can']." ".$art['uni'].")".chr(13).$t[1].chr(13).$t[2];
						
			 		}
			 		else{
			 			$cns = $cns-1;
			 		}
			 		
			 	}
			 }
			 else if( $ke ){
			 	// $dat = ArticulosXPaciente( $pac );
			 	$artPrevios="";
			 }
			 
		}//fin del else
			
	

		/**
		* Impresión de los artículos asociados
		**/

		echo "<tr><td class=".$color." ><b>ARTICULOS ASOCIADOS:</b><br></td></tr>";
		
		$poslist = enLista( $dat, $art );
		
		/* Si el codigo del articulo existe en pantalla debe imprimirse en el textarea correspondiente*/
		// if( $ke && $tipTrans == "C"  && $cco['tras'] ){
		if( true ){
			
			// echo "<tr><td class=".$color." ><b>ARTICULOS ASOCIADOS:</b><br></td></tr>";
			
			if( $ke && $tipTrans == "C"  && $cco['tras'] ){
				echo "<tr><td class=titulo3 >";
				pintarTabla($dat, $color, count($dat) ); 
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
				?>	
					<input type='text' name="artcod" size='14'>   
					<script language="JAVASCRIPT" type="text/javascript">
						document.carga.artcod.focus();
					</script>
				<?php
				
				echo $inputAprov;
			}
			else{
				
				if( $art['cva'] ){
					
					if( $dosInsulina!==false )
						$art['can'] = $dosInsulina;
					
					echo "<tr><td class='".$color."'><font color=#000066><b>$warning Artículo:</b> <font size='2'>".$art['cod']."-".$art['nom']."<br>";
					echo "(Cant. Máx ".$art['max'].$art['uni'].")</font> <b>Cantidad:</b></font>";
					?> 
						<input type='text' name="art[can]" size="3" value="<?php echo $art['can'];?>"> 
						<script language="JAVASCRIPT" type="text/javascript">
							document.carga.art[can].focus();
						</script> 
					<?php
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
						echo "<select name='lote' id='lote' >";
						echo "<option></option>";
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
			
			$pedirArticulo = true;
			
			if( $art['cva'] ){
				$pedirArticulo = false;
			}
			
			if( $art['imp'] && empty($lote) ){
				$pedirArticulo = false;
			}
			
			
			if( $pedirArticulo ){
				echo "<tr><td class='".$color."'>".$warning." <b>ART: </b></font>";
				?>	
					<input type='text' name="artcod" size='14'>   
					<script language="JAVASCRIPT" type="text/javascript">
						document.carga.artcod.focus();
					</script>
				<?php
				
				echo $inputAprov;
			}
			else{
				
				if( $art['cva'] ){
					
					if( $dosInsulina!==false )
						$art['can'] = $dosInsulina;
					
					echo "<tr><td class='".$color."'><font color=#000066><b>$warning Artículo:</b> <font size='2'>".$art['cod']."-".$art['nom']."<br>";
					echo "(Cant. Máx ".$art['max'].$art['uni'].")</font> <b>Cantidad:</b></font>";
					?> 
						<input type='text' name="art[can]" size="3" value="<?php echo $art['can'];?>"> 
						<script language="JAVASCRIPT" type="text/javascript">
							document.carga.art[can].focus();
						</script> 
					<?php
				}
				
				if( $art['imp'] ){
					
					
					if( $tipTrans != "C" ){
						$lotesdisponibles = consultarListaLotesCargados( $conex, $wcliame, $art['cod'], $pac['his'], $pac['ing'] );
						var_dump( $lotesdisponibles );
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
					echo "<b>LOTE</b>: <INPUT type='text' name='lote' id='lote' value='".$lote."'>";
					echo "<input type ='hidden' name='art[imp]' value='1'>";
					
					echo "</td></tr>";
				}
			}
			
			
			
			
			
			
			// echo "<tr><td class=".$color."><b>ARTICULOS ASOCIADOS:</b><br>";
			// echo '<textarea ALIGN="CENTER"  ROWS="3" name="artMostrar" cols="28"  color:"'.$color.'" style="font-family: Arial; font-size:14" readonly>'.$artMostrar.'</textarea></font>';
			
			// echo '<textarea ALIGN="CENTER"  ROWS="3" name="artMostrar" cols="28"  color:"'.$color.'" style="font-family: Arial; font-size:14" readonly>'.$artMostrar.'</textarea></font>';
			// var_dump( $artPrevios );
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
			
		
		
		
		If(isset($cns) and $cns != 0) {
			
			if($artValido and !$art['cva'] ) 	{
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
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		$kardexAbierto = buscarKardexAbierto( $conex, $bd, $pac[ 'his' ], $pac[ 'ing' ], $fecDispensacion );
		
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
				if( $poslist > -1 && !$regbool  && $dat[ $poslist ][5] == 'N' && $dat[ $poslist ][7] >= 100 ){
					echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
						 mostrarAlerta(\"El Articulo: {$art['cod']}-{$art['nom']}\\nes No POS y la cantidad autorizada por CTC ya esta dispensado\");
						 </script>";
				}
				
				//Si el articulo no se encuentra en la lista de articulos
				//a cargar para un paciente aparece un mensaje de alerta
				if( $poslist == -1 && !$regbool && !empty($art['cod']) && !empty( $art['nom'] ) ){
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
				//Si fue dispensado y no se encuentra en la lista (hago referencia a la lista que se ve en la PDA)
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
					// echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
					  // mostrarAlerta(\"El Articulo \\n{$art['cod']}-{$art['nom']}\\nes Material Medico Quirurgico\\ y fue cargado al paciente\");
				      // </script>";
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
			if( $tipTrans != "C" && $poslist == -1 && $cco['tras'] && !empty($art['nom']) && !empty($art['cod']) ){
				echo "<script language=\"JAVASCRIPT\" type=\"text/javascript\">
					  mostrarAlerta(\"El Articulo \\n{$art['cod']}-{$art['nom']}\\nno esta cargado al paciente\");
				      </script>";
			}

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
		echo "<input type='hidden' name='emp' value='".$wemp_pmla."'>";
		echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";

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
					echo "<td><input type='text' name='listaMMQ[".$keyArticulo."][nombre]' readonly style='width:400px;border: 0; background: 0' value='".trim( $valueArticulo['nombre'] )."'></td>";
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
			echo @"<A HREF='cargospda.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;wemp_pmla=".$wemp_pmla."&bd=".$bd."&cbCrearPeticion=on&ccoCam={$cco['cod']}&hab={$pac['hac']}&solicita=$usuario&origen={$cco['cod']}&destino={$pac['sac']}&paciente={$pac['nom']}&opcionRetornar=1&ultPac={$pac['his']}-{$pac['ing']}'>Retornar Usuario</a> </font>";
		}
		else{
			echo @"<A HREF='cargospda.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;wemp_pmla=".$wemp_pmla."&bd=".$bd."&ultPac={$pac['his']}-{$pac['ing']}'>Retornar Usuario</a> </font>";
		}
		
		if( $cco['tras'] && $tipTrans == "C" && $cargados == "on" && $hayArticulosPorDispensar ){
				echo @"&nbsp; &nbsp;<A HREF='cargospda.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;wemp_pmla=".$wemp_pmla."&bd=".$bd."&ccoCod=UN.".$cco['cod']."&cbCrearPeticion=on&ccoCam={$cco['cod']}&hab={$pac['hac']}&solicita=$usuario&origen={$cco['cod']}&destino={$pac['sac']}&paciente={$pac['nom']}&opcionRetornar=2&ultPac={$pac['his']}-{$pac['ing']}'>Retornar Usuario+CC</a> </font>";
		}
		else{
			echo @"&nbsp; &nbsp;<A HREF='cargospda.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;wemp_pmla=".$wemp_pmla."&bd=".$bd."&ccoCod=UN.".$cco['cod']."&ultPac={$pac['his']}-{$pac['ing']}'>Retornar Usuario+CC</a> </font>";
		}

		echo "</td></tr>";
		echo"<tr><td class='titulo3'><input type='submit' value='ACEPTAR' id='ACEPTAR'></td></tr></form>";

		echo "<tr><td class='".$class."'>";
		
		if( $cco['tras'] && $tipTrans == "C" && $cargados == "on" &&  $hayArticulosPorDispensar ){
				echo @"<A HREF='cargospda.php?tipTrans=".$tipTrans."&amp;wemp_pmla=".$wemp_pmla."&bd=".$bd."&cbCrearPeticion=on&ccoCam={$cco['cod']}&hab={$pac['hac']}&solicita=$usuario&origen={$cco['cod']}&destino={$pac['sac']}&paciente={$pac['nom']}&opcionRetornar=3&ultPac={$pac['his']}-{$pac['ing']}'>Retornar</a>";
		}
		else{
			echo @"<A HREF='cargospda.php?tipTrans=".$tipTrans."&amp;wemp_pmla=".$wemp_pmla."&bd=".$bd."&ultPac={$pac['his']}-{$pac['ing']}'>Retornar</a>";
		}
		
		echo "&nbsp; &nbsp;<A HREF='reporte.php?pac[his]=".trim($pac['his'])."&amp;pac[ing]=".trim($pac['ing'])."&amp;cc=".$cco['cod']."&tipTrans=".$tipTrans."&usuario=".$usuario."&wemp_pmla=".$wemp_pmla."&bd=".$bd."'>Reporte dia paciente</a>";
		echo "</td></tr>";
	}
	else
	{
		echo "<tr><td class='errorTitulo'>";
		echo $warning;
		
		echo "</td></tr>";
		echo "<tr><td class='".$class."'>";
		echo @"<A HREF='cargospda.php?usuario=".$usuario."&amp;tipTrans=".$tipTrans."&amp;wemp_pmla=".$wemp_pmla."&bd=".$bd."&ultPac={$pac['his']}-{$pac['ing']}'>Retornar con Usuario</a>&nbsp; &nbsp;";
		echo @"<A HREF='cargospda.php?tipTrans=".$tipTrans."&amp;wemp_pmla=".$wemp_pmla."&bd=".$bd."&ultPac={$pac['his']}-{$pac['ing']}'>Retornar</a>";
		echo "</td></tr>";

		$art['cod']="NO APLICA";
		registrarError($odbc, $cco, $fuente,  0, 0, $pac, $art, $error, $color, $warning, $usuario);
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
echo "</form>";
?>
</body>
</html>
