<head>
  <title>REGISTRO ENTREGA NUTRICIONES - IPOD'S</title>

  <link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
  <link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
  <link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
  <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
  
  <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />

  <script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
  <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
  
  <script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>

  <style type="text/css">

    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:40pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:30pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}

  </style>


</head>

<script type="text/javascript">

function alCambiarRondaPiso( cmp ){
	
	$( "[name = wfecha_actual]" ).val( $( "option:selected", cmp ).attr( "fechaactual" ) );
}

//Crea options nuevos para una selecion
function creandoOptions( slCampos, opciones ){

	//options debe ser un array
	if( slCampos.tagName.toLowerCase() == "select" ){

		//Borrando los options anteriores
		var numOptions = slCampos.options.length;

		for( var i = 0; i <  numOptions; i++ ){
			slCampos.removeChild( slCampos.options[0] );
		}

		opciones.reverse();

		//agrengando options
		for( var i = 0; i < opciones.length; i++ ){
			var auxOpt = document.createElement( "option" );
			slCampos.options.add( auxOpt, 0 );
			auxOpt.innerHTML = opciones[i];

			slCampos.options.selectedIndex = 0;
		}
	}
}


function ocultarOptionsHoras( horInicial, horFinal ){

	var campoHoras = document.getElementsByName( "whora_par_actual" );

	var agregar24 = false;	//bandera que indica hay hora a agregar como 24, Este programa no maneja ronda de las 00

	campoHoras = campoHoras[0];

	var opciones = new Array();;

	try{
		for( var i = horInicial; i <= horFinal; i += 2 ){

			if( i < 10 ){
				if( i == 0 ){
					agregar24 = true;	//El programa no maneja las 00 como hora si no las 24, por eso se agrega al final
				}
				else{
					opciones[ opciones.length ] = '0' + i;
				}
			}
			else{
				if( i == 24 ){	//cancelo agregacion de hora 24 por si ya lo hay
					agregar24 = false;
				}

				opciones[ opciones.length ] = '' + i;
			}
		}

		if( agregar24 ){
			opciones[ opciones.length ] = '24';
		}

		creandoOptions( campoHoras, opciones )
	}
	catch(e){
		alert(e);
	}
}


function alCambiarDia( campo, horaInicialAyer, horaFinalAyer, horaInicialHoy, horaFinalHoy, horaInicialManana, horaFinalManana ){

	switch( campo.options[ campo.selectedIndex ].text ){

		case 'AYER':
			horInicial = horaInicialAyer;
			horFinal = horaFinalAyer;
			break;

		case 'HOY':
			horInicial = horaInicialHoy;
			horFinal = horaFinalHoy;
			break;

		case 'MAÑANA':
			horInicial = horaInicialManana;
			horFinal = horaFinalManana;
			break;
	}

	ocultarOptionsHoras( horInicial, horFinal );
}

function cambiarUrl( campo,url, i ){

	//alert( url+ "&dosisIpd["+i+"]=" +campo.options[ campo.selectedIndex ].text );

	window.location = url+ "&dosisIpd["+i+"]=" +campo.options[ campo.selectedIndex ].text;
}

function parpadear() {
  var blink = document.all.tags("BLINK")
  for (var i=0; i < blink.length; i++)
    blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : ""
}

function empezar() {
    setInterval("parpadear()",500);

	//Septiembre 05 de 2013
	$( "div[class!=titulopagina]]", $( "[obs]" ) ).css( "font-size", "22pt" );
	$( "div[class!=titulopagina]]", $( "[obs]" ) ).each(function(x){
		if( x > 0 ){
			$( this ).css({display: "none" });
		}
	});

	$( "[obs]" ).each(function(x){
		$( "div", this ).eq(0).css({display: "" })
	});

	if( $( "#permisoUsoAplicacion" ).length > 0 ){
		
		if( $( "#permisoUsoAplicacion" ).val() != '1' ){
			
			$( "select,input,textarea" ).each(function(){
				$( this ).attr({disabled:true});
			})
			
			// $( "a" ).not(":last").each(function(){
			$( "a" ).each(function(){
				$( this ).attr({href:"#null"});
			})
			
			jAlert( "Usted <b>"+dataUsuario.codigo+"-"+dataUsuario.descripcion+"</b> está haciendo uso indebido del sistema de información, no puede cambiar los datos de acceso", "ALERTA" );
			$( "h1", $( "#popup_container" ) ).css({background:"#feaaa4", color:"black"});
		}
	}
}
window.onload = empezar;


function Falta_aplicar()
  {
	alert ("EN LAS HABITACIONES QUE SE MUESTRAN, NO SE HA TERMINADO DE APLICAR LA RONDA ANTERIOR");
	//window.close();
  }

function validar_browser()
  {
	var browserName=navigator.appName;
	var code=navigator.appCodeName;
	var agente=navigator.userAgent;

	var pos_brow = agente.indexOf("Safari");
	var pos_movi = agente.indexOf("Mobile");

	if (pos_brow <= 0 || pos_movi <= 0)
	   {
	    alert ("ESTE PROGRAMA SOLO PUEDER USUARSE DESDE LOS ** IPOD's **");
	    window.close();
       }
  }

function enter()
	{
	 document.forms.apl_ipods.submit();
	}

function cerrarVentana()
	 {
      window.close()
     }
	 
function enter2( i, valor )
{
	$( "form" ).eq(0).append( "<input type='hidden' name='wido["+i+"]' value='"+valor+"'>" )
	document.forms.apl_ipods.submit();
}

function enter3( i, valor )
{
	if( $( "[name=pac_historia]" ).val() != '' ){
		
		$( "form" ).eq(0).append( "<input type='hidden' name='wido["+i+"]' value='"+valor+"'>" )
		document.forms.apl_ipods.submit();
	}
	else{
		jAlert("Debe ingresar una historia","ALERTA");
	}
}


</script>

<body>

<?php
include_once("conex.php");
  /*********************************************************
   *               APLICACION DE MEDICAMENTOS              *
   *    EN LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
   *     				CONEX, FREE => OK				   *
   *********************************************************/

//==================================================================================================================================
//PROGRAMA                   : Entrega_ipods.ph
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Agosto 25 de 2010
//FECHA ULTIMA ACTUALIZACION : Ene 25 de 2012 ";
//DESCRIPCION
//=============================================================================================================================================\\
//=============================================================================================================================================\\
//     Programa para el registro de la aplicación de medicamentos, basando siempre en las frecuencias definidas por la enfremera en el         \\
//     Kardex electronico y posteriormente basado en la Orden Medica electronica hecha por el médico.                                          \\
//     ** Funcionamiento General:                                                                                                              \\
//     Se selecciona el servicio en el que se esta, se despliega la lista de paciente en el servicio y se va ingresando a cada uno de          \\
//     ellos y dando click en el botón APLICAR de cada uno de los medicamentos si se lo aplico.                                                \\
//     Para ANULAR se da click en el botón ANULAR.                                                                                             \\
//                                                                                                                                             \\
//     ** Restricciones:                                                                                                                       \\
//        * El programa solo puede ser y solo se dejará usar, en los IPOD's.                                                                   \\
//        * NO deja aplicar Rondas posteriores a la que este sucediendo en ese momento (esto lo hace automaticamente)       				   \\
// 		  * Se puede aplicar o desatrazar máximo la ronda anterior a la que este en curso.                                                     \\
//                                                                                                                                             \\
//     Tabla(s) : movhos_000015 mov_000004, movhos_000053, movhos_000054, movhos_000060                                                        \\
//         * 000015: Se registra o anula la aplicación                                                                                         \\
//         * 000004: Se actualiza el saldo del medicamento para el paciente                                                                    \\
//         * 000053: Se consulta el kardex del día si existe, si no, se busca el del día anterior                                              \\
//         * 000054: Se consulta el detalle de mediamentos y que la frecuencia coincida con la Ronda                                           \\
//         * 000060: En esta tabla se consulta lo mismo que en la anterior, solo cuando el kardex esta abierto por otro usuario                \\
//=============================================================================================================================================\\
// Notas:  																														               \\
//        																														               \\
// - Los articulos de tipo insulina son de dosis variable si son agregados en ordenes por dextrometer y esten configurados en movhos 59        \\
// - Los articulos de quimio si están como no enviar no requieren saldo para ser aplicados										               \\
// - Los demás articulos no mencionados anteriormente deben tener saldo para poder ser aplicados								               \\
// - Los articulos que tienen condición de tipo A NECESIDAD pueden ser aplicados en cualquier ronda								               \\
// - Los articulos se aplican de acuerdo a la fecha y hora de inicio del articulo y la frecuencia								               \\
//        																														               \\
//=============================================================================================================================================\\
//=============================================================================================================================================\\

//=============================================================================================================================================\\
//M O D I F I C A C I O N E S                                                                                                                  \\
//=============================================================================================================================================\\
// Enero 08 de 2021	Edwin MG:		- Se corrige mensaje de rondas anteriores pendientes para la aplicación de medicamentos.
//=============================================================================================================================================\\
// Diciembre 10 de 219	Edwin MG:	- Si es un cco de ayuda dx, no se valida el saldo en carro debido a que un cco de ayuda dx no tiene saldo  \\
//									  en carro
//=============================================================================================================================================\\
// Marzo 6 de 2019 		Edwin MG:	- Se muestra el cco de urgencias y pide zonas según el cco
//=============================================================================================================================================\\
// Diciembre 27 de 2018 Edwin MG:	- Se corrige el mensaje de advertencia para la hora de las 24(media noche)
//=============================================================================================================================================\\
// Diciembre 26 de 2018 Edwin MG:	- Se muestra un mensaje de advertencia al usuario si se va aplicar una ronda no permitida por el sistema.
//=============================================================================================================================================\\
// Mayo 7 de 2018 		Edwin MG:	- Los articulos MMQ no se muestran para cco diferentes a ugencias
//=============================================================================================================================================\\
// Abril 24 de 2018 	Edwin MG:	- Los articulos MMQ no se muestran por que se deben aplicar por el programa de APLICACION DE INSUMOS
//=============================================================================================================================================\\
//Febrero 15 de 2018	Edwin MG. 	- Los articulos pertenecientes a ayudas diagnósticas no se muestran
//=============================================================================================================================================\\
//Diciembre 18 de 2017	Jessica 	- Se agrega el llamado a la función consultarDiagnosticoPaciente() de comun.php que devuelve la lista 
// 									  de los diagnósticos actuales del paciente			
//=============================================================================================================================================\\
//Septiembre 20 de 2017	Edwin		- Se modifica para que se tenga en cuenta el campo ido del articulo al realizar alguna 					   \\
//									  accion( aplicar, anular, justificar)			   														   \\
//=============================================================================================================================================\\
//Diciembre 12 de 2016	Jessica		- Se modifica el nombre de la funcion anular() por anularAplicacionMovhos(), cambio realizado en 		   \\
// 									  movhos.inc.php para evitar conflictos con otros scripts.												   \\																						   \\
//=============================================================================================================================================\\
//Diciembre 05 de 2016	Jessica		- Se modifican las alertas para que traiga las ingresadas en movhos_000220.								   \\																						   \\
//=============================================================================================================================================\\
//Junio 21 de 2016	Edwin MG.		- Cuando un articulo de tipo Insulina y es agregado por medicamentos se le pone una M debajo del código	   \\
//									y si es agregado por Dextrometer se muestra una D.														   \\
//									- Se agregan notas																						   \\
//=============================================================================================================================================\\
//Mayo 20 de 2016	Jessica Madrid  - Se comenta la función anular() y se agrega dicha función en movhos.inc.php para que pueda ser utilizada  \\
//									desde Entrega_ipods.ph y gestionEnfermieria.php														   \\
//									- Se comenta parte del codigo de la aplicación de medicamentos y se incluye en la funcion  				   \\
//									aplicarMedicamento() en movhos.inc.php para que tambien pueda ser utilizada desde gestión de enfermeria    \\ 
//									para la aplicacion de medicamentos asociados a un procedimiento.										   \\
//=============================================================================================================================================\\
//Abril 26 de 2016 		Jonatan.	Se repara el enlace de retornar cuando se aplica o anula un medicamento, a no retornaba a la lista de pacientes
//									cuando el piso tiene zonas.
//=============================================================================================================================================\\
//Marzo 09 de 2016		Edwin MG.	Un medicamento se considera suspendido si esta en urgencias, sin mirar las 4 horas que tiene efectivas para\\
//									para aplicar																							   \\
//=============================================================================================================================================\\
//Mayo 22 de 2015		Edwin MG.	Se cambia la lectura de articulos de la siguietne manera: Si hay kardex el día actual, lee los articulos   \\
//									del día actual esten confirmados o no, si no hay kardex el día actual se lee los articulos del día anterior\\
//									Las funciones que hacen esta lectura son query_articulos y query_articulos_kardex_no_confirmado		       \\
//=============================================================================================================================================\\
//Abril 27 de 2015		Edwin MG.	Se agrega la vía de administración en el registro de la aplicación para un medicamento  				   \\
//=============================================================================================================================================\\
//Abril 6 de 2015		Edwin MG.	Para la funcion validar_aplicacion en el query que revisa los sados, se quita el filtro de que el cco sea  \\
//									de traslado. Esto para que deje aplicar saldos provenientes de urgencias								   \\
//=============================================================================================================================================\\
//Marzo 04 de 2015		Edwin MG.	Se permite aplicar medicamentos de lactarios desde urgencias.											   \\
//=============================================================================================================================================\\
//Febrero 19 de 2015	Edwin MG	Se cambia la función de buscarSiEstaSuspendido por buscarSiEstaSuspendidoInc que se encuentra en el        \\
//									inlude movhos.inc.php y tienes los mismos parametros que la función buscarSiEstaSuspendido. Esto se hace   \\
//									para que halla compatibilidad con el programa de gestión de enfermería el cual usa la función              \\
//									buscarSiEstaSuspendidoInc																				   \\
//=============================================================================================================================================\\
//Noviembre 18 de 2013 Camilo ZZ.   se modifica el programa para que tenga en cuenta los articulos pendientes de la tabla 000143 ademas de     \\
//                                  se empieza a trabajar con el campo de cantidad recibida, el cual es nuevo en la tabla 0000003 y 000143     \\
//=============================================================================================================================================\\
//Septiembre 27 de 2013	Edwin MG.	Se corrige movimiento de saldo para los articulos del stock, no se movía el saldo del paciente debido a    \\
//									solo se validaba que el saldo se moviera solo en los cco que fueran de traslado (1050, 1051)			   \\
//=============================================================================================================================================\\
//Septiembre 09 de 2013	Edwin MG.	Se muestra la última observación para los medicamentos.													   \\
//=============================================================================================================================================\\
//Septiembre 05 de 2013	Edwin MG.	Para las observaciones se aumenta el tamaño de letra. Esto se debe a que el kardex guarda un histórico en  \\
//									formato html.
//=============================================================================================================================================\\
//Julio 3 de 2013		Edwin MG.	Si el kardex, no se encuentra confirmado el día actual ni el anterior, se revisa el día siguiente si esta  \\
//									confirmado, de ser así, se puede proceder a aplicar medicamentos.										   \\
//=============================================================================================================================================\\
//Marzo 1 de 2013		Edwin MG.	Se valida que el encabezado de movimiento al momento de consultar los articulos en el carro este activo    \\
//=============================================================================================================================================\\
//Enero 29 de 2013		Edwin MG.	Si el kardex se encuentra sin confirmar y hay medicamentos para la ronda seleccionada, aparece un mensaje  \\
//									indicando que el kardex esta sin confirmar y no permite ingresar a la ronda a aplicar medicamentos.		   \\
//=============================================================================================================================================\\
//Diciembre 20 de 2012	Edwin MG.	Para los medicamentos de aplicación variable, se tiene en cuenta el cco en que es variable. Si el cco	   \\
//									del paciente no se encuentra en la lista de cco, la aplicación es normal, de lo contrario la aplicación es \\
//									variable. Si el cco es *, indica que es variable para todos los cco.									   \\
//=============================================================================================================================================\\
//Octubre 11 de 2012 	Edwin MG.	Para los usuarios de lactario, segun root_000025, se les permite aplicar medicamentos de lactario hasta	   \\
//									el día siguiente.																						   \\
//									Si el articulo esta suspendido aparece en rojo															   \\
//=============================================================================================================================================\\
//Septiembre 20 de 2012 Edwin MG.	No se realiza cambio de dosis si la dosis nueva a aplicar es 0 o vacía.									   \\
//=============================================================================================================================================\\
//Septiembre 18 de 2012 Mario Cadavid.	Se creó la función valHoraStrtoTime() que retorna la hora correcta para la función strtotime		   \\
// 										la hora correcta en cuanto a lo requerido por el programa, ya que para calcular por ejemplo la dosis   \\
// 										máxima se envia strtotime(2012-09-18 24:00:00) == strtotime(2012-09-19 00:00:00) por ende calcula 	   \\
// 										la dosis máxima con relacion a las 00 de sept 19 mientras que lo que se requiere es las 00 de sept 18  \\
//=============================================================================================================================================\\
//Agosto 22 de 2012 Edwin MG.	Se permite dosis variable al aplicar para medicamentos que así lo permitan, indicados en la tabla de fraccion  \\
//								de medicamentos (movhos_000059).  Además no se muestran los articulos que sobrepasen la fecha y hora de 	   \\
//								terminacion de articulos por dosis maxima o días de tratamiento.											   \\
//=============================================================================================================================================\\
//Agosto 14 de 2012.	Edwin MG.	Para los medicamentos que son de stock o que estan como no enviar, se valida si hay saldo del medicamento. \\
//									Si hay saldo del medicamento se descuenta la aplicación de los saldos, de lo contrario no se mueven los    \\
//									saldos.																									   \\
//=============================================================================================================================================\\
//Junio 26 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos    \\
// 								   de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de 	   \\
//								   la primera funcion.																						   \\
//=============================================================================================================================================\\
//Mayo 31 de 2012	Edwin MG.	Cambio query que consulta el saldo por recibir en el carro, para que tenga en cuenta los articulos de CM	   \\
//								ya que esto permitia aplicar medicamentos sin recibir del carro.											   \\
//								No dejaba anular medicamentos aplicados a las 00.															   \\
//=============================================================================================================================================\\
//Mayo 25 de 2012	Edwin MG.	Todo medicamento, aunque sea de stock, la cantidad a aplicar es cantidad_de_fraccion/(cantidad_de_manejo)	   \\
//=============================================================================================================================================\\
//Mayo 9 de 2012	Edwin MG.	Se deja el formato de ronda a aplicar de la siguiente manera: {HoraMilitar}:00 - {AM|PM}					   \\
//=============================================================================================================================================\\
//Febrero 27 de 2012																														   \\
//Se corrige funcion buscarSiEstaSuspendido, para que busque si un medicamento se encuentra suspendido entre dos rondas.					   \\
//=============================================================================================================================================\\
//Febrero 7 de 2012                                                                                                                            \\
//Se adiciona la restriccion de que si no se ha terminado la ronda anterior no deje aplicar nada de la ronda actual, para esto se creo un campo\\
//en la tabla de Centros de costo (000011) llamadado "ccores" que cuando esta en 'on' hace la verificacion y cuando este en 'off', tambien se  \\
//creo la funcion estaAplicadoCentodeCostosporRonda() y esta su vez si encuentra un medicamento obligatorio sin aplicar muestra las habitaciones\
//que faltan y devuelve un 'false' que a su vez llama la funcion JavScript 'Falta_Aplicar()' la cual muestra un MSJ y cierra la ventana.       \\
//=============================================================================================================================================\\
//Noviembre 17 de 2011                                                                                                                         \\
//Se adiciona el campo "ccoipo" en la tabla movhos_000011, que indica si este cco aplica los medicamentos con IPODs o no, con esto el programa \\
//valida si este programa se puede utilizar o no desde un computador portatil o de escritorio.                                                 \\
//=============================================================================================================================================\\
//Julio 26 de 2011                                                                                                                             \\
//Se adiciona un campo de justificaion por cada medicamento en el caso de que no se aplique, es decir, poder colocar una justificacion de      \\
//porque no se aplico; para esto se creo la tabla _000113 en la cual se guarda por cada articulo que tenga justificacion un registro, el cual  \\
//esta compuesto por historia, ingreso, articulo, fecha, ronda y justificacion, en el caso que luego de colocar la justificacion se aplique, el\\
//programa automaticamente borra el registro de la tabla _000113, además una vez aplicado el medicamento ya no aparece la opcion de poder      \\
//colocar una justificacion, si se anula la aplicacion vuelve y aparece el campo de justificacion.                                             \\
//=============================================================================================================================================\\
//Julio 21 de 2011                                                                                                                             \\
//Si no se ha generado kardex en el día actual, el programa se basa entonces en el del día anterior, para esto se creo una columna en la lista \\
//de pacientes del IPOD que indica si el kardex esta actualizado 'Hoy' o 'Ayer', esto sirve como ayuda.                                        \\
//=============================================================================================================================================\\
//Julio 12 de 2011                                                                                                                             \\
//Se modifica para que solo salgan las historias que tengan medicamentos para la RONDA solicitada o que sean A NECESIDAD                       \\
//=============================================================================================================================================\\
//Mayo 27 de 2011                                                                                                                              \\
//Se creo el campo 'ccozon' en la tabla _000011, en el cual se colocan todas las zonas en que se divide el Cco separadas por coma. También se  \\
//el campo 'habzon' en la tabla _000020, en el cual se debe colocar la zona del Cco en la que esta la cama, debe ser con el mismo nombre se    \\
//coloco en la 000011. Ej: Norte,Sur,Oriente,A,B, etc. en la '_000011' y 'Norte' en la Habitacion '710' del Cco=1186 en la tabla '_000020'.    \\
//=============================================================================================================================================\\
//=============================================================================================================================================\\
//Mayo 25 de 2011                                                                                                                              \\
//Se crea un campo en la tabla 000059 que indica si se aplica o no con IPOD el articulo, si esta en 'on' indica que no se aplica con el IPOD,  \\
//aunque este aparezca en el Kardex; esto es porque hay articulos del stock que pueden aparecer en el kardex pero al grabarlos o facturarlos   \\
//por la auxiliar de enfermería quedan de una vez aplicados.                                                                                   \\
//=============================================================================================================================================\\
//=============================================================================================================================================\\
//Abril 18 de 2011                                                                                                                             \\
//Se crean los campos 'aplufr' y 'apldos', en done se graban la unidad de la fraccion y la dosis exacta aplicada en unidades de fraccion       \\
//=============================================================================================================================================\\
//Abril 12 de 2011                                                                                                                             \\
//Se devuelve la modificacion anterior, pero se corrige en la funcion Hora_par(), que cuando la resta de la hora actual le de cero, coloque    \\
//hora_par_anterior = 24                                                                                                                       \\
//=============================================================================================================================================\\
//Abril 11 de 2011                                                                                                                             \\
//Se modifica para que la hora de busqueda cuando sean las 24 busque con 00:00 que es con la ronda que queda aplicada, esto evita un doble reg.\\
//============================================================================================================================================ \\
//Marzo 17 de 2011                                                                                                                             \\
//=============================================================================================================================================\\
//Se coloca un blockUI al APLICAR y al ANULAR para que no pueda registrar mas de una vez cuando el sistema este lento.                         \\
//=============================================================================================================================================\\
//Marzo 7 de 2011                                                                                                                              \\
//=============================================================================================================================================\\
//Se modifica para que no deje registras o aplicar cuando se desconecte la session de Matrix, eso al parecer estaba generando descuadres en    \\
//los saldos.                                                                                                                                  \\
//=============================================================================================================================================\\
//Febrero 10 de 2011                                                                                                                           \\
//=============================================================================================================================================\\
//Se mejora el programa para que permita aplicar los medicamentos que tienen 'NO ENVIAR' en el kardex y que tambien deje aplicar aquellos      \\
//medicamentos que son del STOCK, esto implica hacer el registro en la tabla movhos_000015 pero no en la _000004, permitiendo anular tambien.  \\
//La modificación se realizó en muchas partes por eso no se coloca la fecha en la líneas afectadas.                                            \\
//=============================================================================================================================================\\
//Enero 24 de 2011                                                                                                                             \\
//=============================================================================================================================================\\
//Febrero 8 de 2011                                                                                                                            \\
//=============================================================================================================================================\\
//Se modifica para que deje aplicar los medicamentos del STOCK y los que digan NO ENVIAR en el kardex                                          \\
//Enero 24 de 2011                                                                                                                             \\
//=============================================================================================================================================\\
//Se corrige la cantidad de unidades o fracciones a aplicar, basado en la tabla movhos_000008 que también es base para la grabacion de cargos  \\
//=============================================================================================================================================\\
//Octubre 6 de 2010                                                                                                                            \\
//=============================================================================================================================================\\
//Se adiciona el diagnostico, basado en el campo 'kardia' del kardex movhos_000053                                                             \\
//=============================================================================================================================================\\


session_start();
if (!isset($user))
	if(!isset($_SESSION['user']))
	  {
	   session_register("user");
	  }


if(!isset($_SESSION['user']))
	echo "error";
else
{

  

  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  


  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  $wuser1=explode("-",$user);
  $wusuario=trim($wuser1[1]);

     	                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="Octubre 27 de 2021";               	 // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //

  encabezado("ENTREGA DE NUTRICIONES",$wactualiz, "clinica");

  $url = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
  function validar_usu($password, $username){
	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	$password = sha1($password);
	$sql = "SELECT 1
				  FROM usuarios
				 WHERE codigo  = '".$username."' 
				   AND password = '".$password."'; ";
	$res = mysql_query($sql,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	$cant = mysql_num_rows($res);
	return $cant;
	}

	function devolver($url, $wemp_pmla, $wfecha_actual,  $whora_par_actual, $whis, $wing, $wcco, $whab, $wpac, $wzona){
		
		$url_code = array(
			'wemp_pmla' => $wemp_pmla,
			'wfecha_actual' => $wfecha_actual,
			'whora_par_actual' => $whora_par_actual,
			'whis' => $whis,
			'wing' => $wing,
			'wcco' => $wcco,
			'whab' => $whab,
			'wpac' => $wpac,
			'walp' => 'off',
			'wzona' => $wzona
		);
		
		$url = explode("?", $url);
		$url_code = http_build_query($url_code, null, '&', PHP_QUERY_RFC3986);
		$url = $url[0]."?".$url_code;
		
		return $url;
	}
	
	if(isset($firma2) && $firma2 == "on"){
		
		if(isset($password) && isset($username)){
			
			$cant = validar_usu($password, $username);
			
			if($cant > 0){
				
				$url = explode('&firma2', $url);

				$url = $url[0]."&autenticacion=true&wrecibe=".$username;
				header("Location: ". $url );
			}else{
				
				
				
				$url = explode('&firma2', $url);

				$url = $url[0]."&firma2=on&autenticacion=0";
				header("Location: ". $url );
				
				exit();
				
			}
			
		}else{
		if(isset($autenticacion)&& $autenticacion == "0"){
		
			echo "<script>alert('Credenciales incorrectas')</script>";
				
		}	
		echo "<form action='Entrega_ipods.php' method='get'>	";
		echo "	<div class='tipoScreen02'>";
		echo "		<table border=0 style='width : 100%'>";	
		
		foreach ($_GET as $key => $value) {
			if(is_array($value)){
				foreach ($value as $key2 => $value2) {
					echo"<input name='{$key}[{$key2}]' value='{$value2}'style='height:35px !important; width: 40%; font-size:15px;' type='text' name='codigo' hidden size=18 maxlength=8></td>";
				}
			}else{
				echo"<input name='{$key}' value='{$value}'style='height:35px !important; width: 40%; font-size:15px;' type='text' name='codigo' hidden size=18 maxlength=8></td>";
			}
			
		}


		echo "			<tr><td align=center id=tipo5 colspan=2><font size='6' style='padding-right:20%;'>Ingresa el usuario quien recibe la nutrición</font></td></tr>";
		echo "          <tr><td align=center class='' colspan=2></td></tr>";
		echo "			<tr><td class='encabezadoTabla'align=center id=tipo4><font  size='6'>C&oacute;digo</font></td><td align=left><input name='username' class='input-login' style='height:35px !important; width: 40%; font-size:15px;' type='text' name='codigo' size=18 maxlength=8></td></tr>";
		echo "			<tr><td class='encabezadoTabla' align=center id=tipo4><font  size='6'>Clave</font></td><td align=left><input name='password' class='input-login' style='height:35px !important; width: 40%;  font-size:15px;' type='password' name='password' size=18 maxlength=30></td></tr>";
		echo "          <tr><td align=center class='tipo1a' colspan=2></td></tr>";
		echo "			<tr><td align=center colspan=2><input type='submit' value='Validar' style='font-size:30px; padding-top:1px; width: 30%;'></td></tr>";
		echo "		</table>";
		echo "	</div>";
		echo "</form>";
		
		$url = devolver($url, $wemp_pmla, $wfecha_actual,  $whora_par_actual, $whis, $wing, $wcco, $whab, $wpac, $wzona);
		
		echo "<table style='width : 70%'>";
		echo "<tr><td align=center colspan=2><A HREF='".$url."' class=tipo3V>Retornar</A></td></tr>";
		echo "</table>";
			   
		}
		
		exit();
		
		
	}


function consultaCentrosCostosNoDomiciliarios( $conex, $wbasedato, $esDomiciliario = false ){
	
  	$coleccion = array();
	
	if( $esDomiciliario )
	{
		$sql = "SELECT Ccocod, UPPER( Cconom )
				  FROM ".$wbasedato."_000011
				 WHERE Ccoest  = 'on' 
				   AND ( Ccohos  = 'on' AND ccodom = 'on' )
			  ORDER BY Ccoord, Ccocod; ";
	}
	else
	{
		$sql = "SELECT Ccocod, UPPER( Cconom )
				  FROM ".$wbasedato."_000011
				 WHERE Ccoest  = 'on' 
				   AND ( Ccohos  = 'on' AND ccodom != 'on' )
					OR Ccourg  = 'on'
			  ORDER BY Ccoord, Ccocod; ";
	}
					  
	$res1 = mysql_query($sql,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	$num1 = mysql_num_rows($res1);

  	if ($num1 > 0 )
  	{
  		for ($i=1;$i<=$num1;$i++)
  		{
  			$cco = new centroCostosDTO();
  			$row1 = mysql_fetch_array($res1);

  			$cco->codigo = $row1[0];
  			$cco->nombre = $row1[1];

  			$coleccion[] = $cco;
  		}
  	}
	
  	return $coleccion;
}

function consultarSiPuedeAnular( $historia, $ingreso, $medicamento, $ido )
{
	
	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	
	$qMedDeProc = "SELECT Reltor,Relnro,Relpro,Relite,Detesi  
					 FROM ".$wbasedato."_000195,".$whce."_000028,".$wbasedato."_000045 
					WHERE Relhis='".$historia."' 
					  AND Reling='".$ingreso."' 
					  AND Relmed='".$medicamento."' 
					  AND Relido='".$ido."'
					  AND Reltor=Dettor
					  AND Relnro=Detnro
					  AND Relpro=Detcod
					  AND Relite=Detite
					  AND Eexcod=Detesi
					  AND Eexrea='on';";
					  
	$resMedDeProc = mysql_query($qMedDeProc, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qMedDeProc . " - " . mysql_error());
	$numMedDeProc = mysql_num_rows($resMedDeProc);
	
	$noPuedeAnular = false;
	
	if($numMedDeProc > 0)
	{
		$noPuedeAnular = true;
	}
		
	return $noPuedeAnular;
}
  

	function esInsulina( $conex, $wbasedato, $art ){

		$val = false;
				   
		$sql = "SELECT * 
				  FROM ".$wbasedato."_000068
				 WHERE Arkcod = '".$art."'
			       AND Arktip = 'I' 
			       AND Arkest = 'on' 
				   ";
			   
		$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){
			$val = true;
		}

		return $val;
	}
	
	/**
	 * Estos son los articulos que son del IDC
	 */
	function esQuimio( $conex, $wbasedato, $art ){

		$val = false;
				   
		$sql = "SELECT * 
				  FROM ".$wbasedato."_000068
				 WHERE Arkcod = '".$art."'
			       AND Arktip = 'Q' 
			       AND Arkest = 'on' 
				   ";
			   
		$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){
			$val = true;
		}

		return $val;
	}
	
	function esArtDextrometer( $conex, $wbasedato, $historia, $ingreso, $art, $fecha ){

		$val = false;
	
		 // -- AND kadess = 'on'
		$sql = "SELECT * 
				  FROM ".$wbasedato."_000054, ".$wbasedato."_000070
				 WHERE kadhis = infhis
				   AND kading = infing
			       AND kadart = infade
			       AND kadper = inffde
			       AND kadsus != 'on'
			       AND inffec = kadfec
			       AND kadhis = '".$historia."'
			       AND kading = '".$ingreso."'
			       AND kadart = '".$art."'
			       AND kadfec = '".$fecha."'";
			   
		$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){
			$val = true;
		}

		return $val;
	}
  
  /****************************************************************************************
   * Consulta si un kardex para una fecha está confirmado o no
   ****************************************************************************************/
  function consultarKardexConfirmadoPorCcoIpods( $historia, $ingreso, $fecha, $cco = '*' ){

	global $conex;
	global $wbasedato;

	$val = false;

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000053
			WHERE
				karhis = '$historia'
				AND karing = '$ingreso'
				AND fecha_data = '$fecha'
				AND karest = 'on'
				AND karcco LIKE '$cco'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){

		$rows = mysql_fetch_array( $res );

		if( $rows[ 'Karcon' ] == 'on' ){
			$val = true;
		}
	}

	return $val;
  }

  /************************************************************************
   * Consulta el cco de lactario
   ************************************************************************/
  function consultarCcoLactario( $conex, $wbasedato ){

	$val = false;

	$sql = "SELECT
				Ccocod
			FROM
				{$wbasedato}_000011
			WHERE
				ccolac = 'on'
				AND ccoest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		$rows = mysql_fetch_array( $res );
		$val = $rows['Ccocod'];
	}

	return $val;
  }

  /**********************************************************************
   * consulta si un usuario es de lactario
   **********************************************************************/
  function esUsuarioLactario( $conex, $wbasedato, $usuario ){

	$val = false;

	$sql = "SELECT
				*
			FROM
				root_000025,
				{$wbasedato}_000011
			WHERE
				empleado = '$usuario'
				AND ccocod = cc
				AND ccolac = 'on'
				AND ccoest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){
		$val = true;
	}

	return $val;
  }

  /************************************************************************************************************************************
   * Calculo de la nueva regleta
   *
   * - Calculo primero cuanto falta por dispensar hasta la hora de terminacion del medicamento
   * - Ya de acuerdo a la dosis calculo cuantas aplicaciones se pueden hacer
   * - De acuerdo a la frecuencia, calculo cuantas horas faltan por aplicar de acuerdo tambien a las aplicaciones calculadas en el punto anterior
   * - Al restar las horas la fecha y hora de terminacion del articulo con las horas calculadas, da la hora de terminacion del medicamento
   * - Por ultimo, puede haber una pequeña fraccion que fraccion que falte por dispensar, esa fraccion corresponde a lo faltan para la hora
   *   calculada
   * - Cualquier fraccion antes de la hora calculada debe quedar dispensada
   * - Cualquier hora posterior a la hora calculada no esta dispensada
   ************************************************************************************************************************************
   * Parametros
   *
   * $frecuencia					horas en segundos
   * $fechorFinArticulo				fecha y hora de terminacion del articulo en segundos
   * $fechaKardex					Fecha en formato YYYY-MM-DD
   * $fechorInicio					Fecha y hora de inicio del medicamento
   ************************************************************************************************************************************/
  function cambiarRegleta( $regletaOriginal, $fechaKardex, $fechorInicio, $frecuencia, $cantidadManejo, $nuevaDosis, $canADispensar, $canDispensada, $saldoArticulo, $fechorFinArticulo ){

	$val = array();

	$nuevaFraccion = round( $nuevaDosis/$cantidadManejo, 3 );

	$datosRegleta = explode( ",", $regletaOriginal );

	// Primero calculo cuanto falta por dispensar exactamente hasta la hora de terminacion del medicamento
	if( $canADispensar - $canDispensada > 0 ){
		$falante = ( $canADispensar - $canDispensada )*$cantidadManejo - $saldoArticulo;	//Se resta el saldo de articulo por que es un sobrante de dicho articulo
	}
	else{
		$falante = 0;
	}

	//Calculo Cuantas aplicaciones alcanza para el faltante de acuerdo a la nueva dosis
	$aplFaltantes = floor( $falante/$nuevaDosis );

	//Calculo el total de horas que cubren dichas aplicaciones
	$totalHoras = $aplFaltantes*$frecuencia;

	//A la fecha y hora de terminacion del medicamento resto el total de horas calculadas
	$fechorUltimaDispensacion =  $fechorFinArticulo - $totalHoras;

	//Por utimo caculo la fraccion sobrante que corresponde a la hora ultima de dispensacion
	$fraccion = ( $nuevaDosis - $falante%$nuevaDosis )/$cantidadManejo;

	$fraccion = round( $fraccion, 3 );

	//Si la fraccion es igual a 1 significa que no hay sobrante
	// if( $fraccion == 1 ){
		// $fraccion = 0;
	// }

	//Obtengo la ultima hora de la regleta
	$ultimoDatoRegleta = explode( "-", $datosRegleta[ count($datosRegleta)-1 ] );

	//Devuelvo estos datos por si se llegan a necesitar
	$val[ 'ultimaDispensacion' ] = $fechorUltimaDispensacion - $frecuencia;


	$val[ 'fraccionUltDis' ] = $fraccion;

	if( !empty( $regletaOriginal ) ){

		/************************************************************************************
		 * Procedo a crear la nueva regleta
		 ************************************************************************************/
		$valRegleta = '';

		//Fecha y hora de inicio del día para el kardex
		$fechorInicioDia = strtotime( $fechaKardex." 02:00:00" );

		$fechorTerminacion = strtotime( $fechaKardex." ".$ultimoDatoRegleta[ 0 ] )+24*3600;

		//Calculo la nueva dosis en fraccion
		$newDosFraccion = round( $nuevaDosis/$cantidadManejo, 3 );

		for( $i = $fechorInicioDia; $i <= $fechorTerminacion; $i += 2*3600 ){

			if( $fechorInicio <= $i ){	//Si el medicamento ya comienza

				if( ( $i - $fechorInicio )%$frecuencia == 0 ){	//Si pertenece a la ronda

					if( $i < $fechorUltimaDispensacion ){	//Si es menor a la ultima ronda dispensada
						$valRegleta .= ",".date( "H:i:s", $i )."-$newDosFraccion-$newDosFraccion";
					}
					elseif( $i == $fechorUltimaDispensacion ){	//Si es igual a la ultima ronda dispensada
						$valRegleta .= ",".date( "H:i:s", $i )."-$newDosFraccion-$fraccion";
					}
					else{	//Si es mayor a la ultima ronda dispensada
						$valRegleta .= ",".date( "H:i:s", $i )."-$newDosFraccion-0";
					}
				}
				else{	//Si no pertenece a la ronda
					$valRegleta .= ",".date( "H:i:s", $i )."-0-0";
				}
			}
			else{	//Si el medicamento no ha comenzado aun
				$valRegleta .= ",".date( "H:i:s", $i )."-0-0";
			}
		}
		/************************************************************************************/

		$val[ 'Regleta' ] = substr( $valRegleta, 1 );
	}
	else{
		$val[ 'Regleta' ] = '';
	}

	return $val;
  }

  function cambiarDosisKardex( $conex, $wbasedato, $historia, $ingreso, $fechaKardex, $articulo, $fini, $hini, $frecuencia, $diasTratamiento, $dosisMaxima, $ido, $nuevaDosis, $fechaAplicacion, $horaAplicacion, $esStock )
    {
	  global $wemp_pmla;

	  $val = false;

	  $deTemporal = false;

	  //Consulto los datos del kardex para el medicamento
	  $sql = "SELECT
				*
			  FROM
				{$wbasedato}_000054
			  WHERE
				kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadfec = '$fechaKardex'
				AND kadart = '$articulo'
				AND kadfin = '$fini'
				AND kadhin = '$hini'
				AND kadido = '$ido'
			";

	  $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	  $num = mysql_num_rows( $res );

	  if(  $num == 0  ){

		  //Busco en la temporal
		  $sql = "SELECT
					*
				  FROM
					{$wbasedato}_000060
				  WHERE
					kadhis = '$historia'
					AND kading = '$ingreso'
					AND kadfec = '$fechaKardex'
					AND kadart = '$articulo'
					AND kadfin = '$fini'
					AND kadhin = '$hini'
					AND kadido = '$ido'
				";

		  $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		  $num = mysql_num_rows( $res );

		  $deTemporal = true;
	  }

	  if( $num > 0 ){

		$rowsKardex = mysql_fetch_array( $res );

		//Consulto la hora de Corte de dispensacion
		$horaCorteDispensacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horaCorteDispensacion" );
		$frecuencia = $frecuencia*3600;	//Convierto la frecuencia a segundos, se da inicialmente en horas
		// echo "<br>.......fechorApli: $fechaAplicacion $horaAplicacion";
		$fechorAplicaciones = strtotime( "$fechaAplicacion $horaAplicacion" );	//Fecha y hora de aplicacion en formato unix

		$cantidadManejo = $rowsKardex[ 'Kadcma' ];
		$saldoArticulo = $rowsKardex[ 'Kadsal' ];
		$dosis = $rowsKardex[ 'Kadcfr' ];

		$cantidadADispensar = $rowsKardex[ 'Kadcdi' ];

		$cantidadDispensada = $rowsKardex[ 'Kaddis' ];

		/************************************************************************************
		 * Busco la hora de terminacion del medicamento
		 * Son tres casos
		 *  - Antes o igual a la hora de corte
		 *  - Segun la dosis maxima
		 *  - Segun días de tratamiento
		 ************************************************************************************/

		//Fecha y hora de inicio del medicamento en formato Unix
		$fechorInicio = strtotime( "$fini $hini" );

		//Fecha y hora de corte para el medicamento
		$fechorFin = strtotime( $fechaKardex." $horaCorteDispensacion:00:00" ) + 24*3600;

		//Si tiene dosis maximas
		if( trim( $dosisMaxima ) != '' ){
			$fechorFin = min( $fechorFin, $fechorInicio+( trim( $dosisMaxima ) - 1 )*$frecuencia );
		}

		//Si tiene días maximos
		if( trim( $diasTratamiento ) != '' ){
										  //									Calculo de fecha final del medicamento por dias de tratamiento
			$fechorFin = min( $fechorFin, $fechorInicio + floor( ( strtotime( $fini )+trim( $diasTratamiento )*24*3600 - $fechorInicio)/$frecuencia )*$frecuencia );
		}

		//la fecha final debe ser mayor a la hora de inicio
		if( $fechorInicio < $fechorFin )
		  {

			//Esto calculo el total de aplicaciones que hay desde la fecha de inicio del medicamento
			//hasta la hora de finalizacion del medicamento
			$auxCan = floor( ( $fechorFin - $fechorInicio )/$frecuencia );

			//Por tanto, la fecha y hora final de terminacion del medicamento es
			//total de aplicaciones por frecuencia mas la fecha de inicio
			$fechorFin = $fechorInicio + $auxCan*$frecuencia;	//Hora de terminacion del medicamento
		  }
		 /************************************************************************************************/

		/************************************************************************************************************
		 * Consulto cuanto hay de saldo a favor segun el kardex para el medicamento segun los datos actuales
		 ************************************************************************************************************/
		$datosKardexActual = cambiarRegleta( $rowsKardex[ 'Kadcpx' ], $rowsKardex[ 'Kadfec' ], $fechorInicio, $frecuencia, $cantidadManejo, $rowsKardex[ 'Kadcfr' ], $rowsKardex[ 'Kadcdi' ], $rowsKardex[ 'Kaddis' ], $saldoArticulo, $fechorFin );

		// echo "<br>.....<pre>"; var_dump( $datosKardexActual ); echo "</pre><br>";

		//Este dato ayudara a calcular correctamente la cantidad a dispensar
		//El saldo a Favor es la cantidad de articulo que aun no se ha gastado segun el kardex
		$saldoAFavor = ( ( $datosKardexActual[ 'ultimaDispensacion' ] - $fechorAplicaciones )/$frecuencia + 1)*$dosis + $datosKardexActual[ 'fraccionUltDis' ]*$cantidadManejo;

		if( $saldoAFavor < 0 ){
			$saldoAFavor = 0;
		}

		/************************************************************************************************************/

		/*********************************************************************************************************
		 * Consulto cuanto es el saldo gastado
		 *********************************************************************************************************/
		if( $fechorFin == $datosKardexActual[ 'ultimaDispensacion' ] ){

			$saldoGastado = $cantidadDispensada*$cantidadManejo - $saldoAFavor - $rowsKardex[ 'Kadsal' ];

			if( $cantidadDispensada - $cantidadADispensar > 0 ){	//Significa que se cargo mas de lo debido y por tanto resto el sobrante
				$saldoGastado -= ( $cantidadDispensada - $cantidadADispensar )*$cantidadManejo;
			}
		}
		else{
			$saldoGastado = $cantidadDispensada*$cantidadManejo - $saldoAFavor;
		}
		/************************************************************************************************************/


		 //Como ya tengo la hora de aplicacion, cuento cuantas aplicaciones hay hasta la fecha y hora de terminacion del medicamento
		 // echo "<br>......".date( "Y-m-d H:i:s", $fechorFin );
		 // echo "<br>......".date( "Y-m-d H:i:s", $fechorAplicaciones );
		 // echo "<br>.....aplicacionesFaltantes: ".$aplicacionesFaltantes = ( $fechorFin - $fechorAplicaciones )/$frecuencia+1;
		 $aplicacionesFaltantes = ( $fechorFin - $fechorAplicaciones )/$frecuencia+1;

		 //Calculo el saldo del articulo de acuerdo a la nueva dosis
		 $saldoArticuloCambio = $cantidadManejo - $aplicacionesFaltantes*$nuevaDosis%$cantidadManejo;

		 //Calculo el saldo del medicamento
		 // echo "<br>.....saldoArticulo = abs( $saldoArticuloCambio - ( $cantidadManejo - $saldoArticulo - $dosis*$aplicacionesFaltantes ) )%$cantidadManejo;";
		 // echo "<br>Hmmmmmm.....".$saldoArticulo = abs( $saldoArticuloCambio - ( $cantidadManejo - $saldoArticulo - $dosis*$aplicacionesFaltantes ) )%$cantidadManejo;
		 $saldoArticulo = abs( $saldoArticuloCambio - ( $cantidadManejo - $saldoArticulo - $dosis*$aplicacionesFaltantes ) )%$cantidadManejo;

		 /**************************************************************************************************************
		  * Calculo la cantidad a dispensar y el saldo de dispensacion para la nueva dosis
		  **************************************************************************************************************/
		 $cantidadDispensar = ceil( $rowsKardex[ 'Kadcan' ]*$nuevaDosis/$cantidadManejo );

		 //calculo la cantidad real a dispensar para el kardex, esto se hace sumando el saldo gastado a la cantidad faltante por dispensar
		 // echo "<br>.....canDispensarReal = ceil( ( $aplicacionesFaltantes*$nuevaDosis + $saldoGastado )/$cantidadManejo );";

		 $canDispensarReal = ceil( ( $aplicacionesFaltantes*$nuevaDosis + $saldoGastado )/$cantidadManejo );

		 $saldoDispensacion = $cantidadDispensar - $canDispensarReal;

		 if( $saldoDispensacion < 0 ){
			$saldoDispensacion = -1*$saldoDispensacion;
		 }
		 else{
			$saldoDispensacion = 0;
		 }
		 /**************************************************************************************************************/

		 /**************************************************************************************************************
		  * Modifico cantidad dispensada solo si la cantidad que falta por dispensar es mayor a la cantidad necesaria
		  * que falta por dispensar desde la ultima aplicacion hasta terminar el medicamento
		  **************************************************************************************************************/
		 $extraDis = '0';
		 if( $canDispensarReal - $rowsKardex[ 'Kaddis' ] < $cantidadDispensar + $saldoDispensacion - $rowsKardex[ 'Kaddis' ] ){
			// echo "<br>.........extraDis = $cantidadDispensar + $saldoDispensacion - {$rowsKardex[ 'Kaddis' ]} - ( $canDispensarReal - {$rowsKardex[ 'Kaddis' ]} )";
			$extraDis = $cantidadDispensar + $saldoDispensacion - $rowsKardex[ 'Kaddis' ] - ( $canDispensarReal - $rowsKardex[ 'Kaddis' ] );

			if( $extraDis < 0 ){
				$extraDis = 0;
			}
		 }
		 /**************************************************************************************************************/

		 /**************************************************************************************************************
		  * Consulto los datos para la nueva dosis
		  **************************************************************************************************************/
		 $datosNuevaDosis = cambiarRegleta( $rowsKardex[ 'Kadcpx' ], $rowsKardex[ 'Kadfec' ], $fechorInicio, $frecuencia, $cantidadManejo, $nuevaDosis, $cantidadDispensar+$saldoDispensacion, $rowsKardex[ 'Kaddis' ]+$extraDis, $saldoArticulo, $fechorFin );
		 /**************************************************************************************************************/

		 if( $esStock == 'on' ){
			$saldoDispensacion = 0;
		 }

		 if( !$deTemporal ){

			 //Actualizo el kardex
			 $sql = "UPDATE {$wbasedato}_000054
					SET
						kadcdi = $cantidadDispensar+$saldoDispensacion,
						kadsal = '$saldoArticulo',
						kadcfr = '$nuevaDosis',
						kadsad = '$saldoDispensacion',
						Kadcpx = '{$datosNuevaDosis[ 'Regleta' ]}',
						Kadron = '".date( "H", $datosNuevaDosis[ 'ultimaDispensacion' ] )."',
						Kadfro = '".date( "Y-m-d", $datosNuevaDosis[ 'ultimaDispensacion' ] )."',
						Kaddan = '{$rowsKardex[ 'Kadcfr' ]}',
						Kadfum ='".date( "Y-m-d" )."',
						Kadhum ='".date( "H:i:s" )."',
						Kaddis = Kaddis + $extraDis
					WHERE
						id = '{$rowsKardex[ 'id' ]}'
					";
		 }
		 else{

			 //Actualizo el kardex
			 $sql = "UPDATE {$wbasedato}_000060
					SET
						kadcdi = $cantidadDispensar+$saldoDispensacion,
						kadsal = '$saldoArticulo',
						kadcfr = '$nuevaDosis',
						kadsad = '$saldoDispensacion',
						Kadcpx = '{$datosNuevaDosis[ 'Regleta' ]}',
						Kadron = '".date( "H", $datosNuevaDosis[ 'ultimaDispensacion' ] )."',
						Kadfro = '".date( "Y-m-d", $datosNuevaDosis[ 'ultimaDispensacion' ] )."',
						Kaddan = '{$rowsKardex[ 'Kadcfr' ]}',
						Kadfum ='".date( "Y-m-d" )."',
						Kadhum ='".date( "H:i:s" )."',
						Kaddis = Kaddis + $extraDis
					WHERE
						id = '{$rowsKardex[ 'id' ]}'
					";
		 }

		 $resUpKardex = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		 $val = true;
	  }

	  return $val;
    }

  /************************************************************************************
   * Consulta si un medicamento tiene dosis variable o no y devuelve toda la inforamcion necesaria
   * en un arreglo
   ************************************************************************************/
  function consultarDosisVariable( $conex, $wbasedato, $articulo, $cco, $ccoPac )
	{

	  $val = false;

	  $sql = "SELECT
				Defrci, Defcai, Defcas, Defesc, Defavc
			  FROM
				{$wbasedato}_000059
			  WHERE
				defcco = '$cco'
				AND defart = '$articulo'
				AND defest = 'on'
			";

	  $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	  $num = mysql_num_rows( $res );

	  if( $num > 0 ){
		$val = mysql_fetch_array( $res );

		$val[ 'Defavc' ] = trim( $val[ 'Defavc' ] );

		$val[ 'Defrci' ] = ( $val[ 'Defrci' ] == 'on' )? true : false;

		//Solo si es de cantidad variable
		if( $val[ 'Defrci' ] )
		  {
			$estaCco = false;

			/************************************************************************
			 * Diciembre 20 de 2012
			 ************************************************************************/
			//Si el cco de aplicacion es diferente a * tengo que verificar que
			//el cco del paciente se pueda aplicar cantidad variable
		    if( $val[ 'Defavc' ] != '*' ){

				$ccosApl = explode( ",", $val[ 'Defavc' ] );

				foreach( $ccosApl as $keyCcoApl => $valueCcoApl ){

					if( $valueCcoApl == $ccoPac[0] ){
						$estaCco = true;
						break;
					}
				}

				if( !$estaCco ){
					$val[ 'Defrci' ] = false;
				}
			}
			/************************************************************************/
		  }
	  }
	  else{
		$val[ 'Defrci' ] = false;
		$val[ 'Defcai' ] = 0;
		$val[ 'Defcas' ] = 0;
		$val[ 'Defesc' ] = 1;
	  }

	  return $val;
    }


  //*******************************************************************************************************************************************
  //F U N C I O N E S
  //===========================================================================================================================================
  function mostrar_empresa($wemp_pmla)
     {
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;



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
		  for ($i=1;$i<=$num;$i++)
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
	         }
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";

	  $winstitucion=$row[2];

	  ///encabezado("Aplicación Medicamentos IPOD´s ",$wactualiz, "clinica");
     }

  function elegir_centro_de_costo()
     {
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  global $wcco;


	  global $whora_par_actual;
	  global $whora_par_anterior;

	  global $wfecha_actual;

	  global $wusuario;

	  global $wemp_pmla;
	  
	  global $consultaPorHistoria;
	  
	  global $servicioDomiciliario;

	  if( !isset($consultaPorHistoria) ){
		  
		  echo "<center><table>";
		  echo "<tr class=encabezadoTabla><td align=center><font size=20>ENTREGA DE NUTRICIONES</font></td></tr>";
		  echo "</table>";

		  echo "<br><br>";

	  
		  
		  if( !esUsuarioLactario( $conex, $wbasedato, $wusuario ) ){	//Si el usuario no es de lacatario

			  //Seleccionar RONDA
			  echo "<center><table>";
			  echo "<tr class=fila1><td align=center><font size=20>Seleccione Ronda : </font></td></tr>";
			  echo "</table>";

			  echo "<center><table>";

			  echo "<tr><td align=rigth><select name='whora_par_actual' onChange='alCambiarRondaPiso( this );' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px;'>";
			  //echo "<option selected>".$whora_par_actual."</option>";

			  $selected = "selected";
			  // $j indica la cantidad de horas a restar mientras que $i es la hora que se muestra
			  //Al seleccionar la hora se cambia la fecha a la que se requiera
			  $rondasAMostrar = consultarAliasPorAplicacion( $conex, $wemp_pmla, "mostrarRondasAnteriorsIpods" );
			  
			  for( $j = $rondasAMostrar*2-2, $i = strtotime( $wfecha_actual." ".$whora_par_actual.":00:00 UTC" ); $j >= 0; $i -=2*3600, $j-=2 ){
				echo "<option $selected fechaactual='".gmdate( "Y-m-d", $i)."'>".( gmdate( "H", $i ) == 0 ? '24': gmdate( "H", $i ) )."</option>";
				$selected = "";
			  }
			  
			  /*
			  if( $whora_par_actual < 2 ){
				  echo "<option>".$whora_par_anterior."</option>";
			  }
			  else{
				  for( $i = $whora_par_actual-2; $i >= 0; $i -=2 ){
					echo "<option>".gmdate( "H", $i*3600 )."</option>";
				  }
			  }*/
			  
			  
			  echo "</select></td></tr>";

			  echo "<input type='HIDDEN' name='wfecha_actual' value='".$wfecha_actual."'>";
		  }
		  else{	//Si es usuario de lactarios

			  //Consulto la hora de corte de dispensacion del día siguiente
			  $horaCorteDispensacion = consultarAliasPorAplicacion($conex,$wemp_pmla,"horaCorteDispensacion");;

			  $mostarDatosAyer = false;	//indica si debe mostrar datos para aplicar del día anterior

			  if( date( "Y-m-d", time() - 2*3600 ) == date( "Y-m-d", time() - 24*3600 ) ){
				$mostarDatosAyer = true;
			  }

			  //La hora de corte del día siguiente es la hora de corte de dispensación o la hora par acual
			  //Esto para limitar la hora de aplicación del día siguiente.
																	//Esto se hace para que siempre sea una hora entre 0 - 23, por si whora_par_actual es 24
			  $horaCorteDispensacion =  min( $horaCorteDispensacion, gmdate( "H", $whora_par_actual*3600 ) );


			  //Seleccionar RONDA
			  echo "<center><table>";
			  echo "<tr class=fila1><td align=center><font size=20>Seleccione Ronda : </font></td></tr>";
			  echo "</table>";

			  echo "<center><table>";

			  //Selección del día
			  echo "<tr>";

			  if( $mostarDatosAyer || $whora_par_anterior == 24 ){
				echo "<td align=rigth><select name='wfecha_actual' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px' onChange='alCambiarDia( this, 22, 22, 2, 24, 0, $horaCorteDispensacion );'>";
			  }
			  else{
				echo "<td align=rigth><select name='wfecha_actual' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px' onChange='alCambiarDia( this, 22, 22, $whora_par_anterior, 24, 0, $horaCorteDispensacion );'>";
			  }

			  if( $mostarDatosAyer ){
				echo "<option value='".date( "Y-m-d", time() - 24*3600 )."'>AYER</option>";
			  }

			  echo "<option value='".date( "Y-m-d" )."' selected>HOY</option>";
			  echo "<option value='".date( "Y-m-d", time()+24*3600 )."'>MAÑANA</option>";
			  echo "</select>";
			  echo "</td>";

			  echo "<td align=rigth><select name='whora_par_actual' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px'>";

			  if( $mostarDatosAyer ){
				$whora_par_anterior_2 = 2;
			  }
			  else{
				if( $whora_par_anterior == 24 ){
					$whora_par_anterior_2 = 2;
				}
				else{
					$whora_par_anterior_2 = $whora_par_anterior;
				}
			  }

			  for( $rn = $whora_par_anterior_2*3600; $rn <= 24*3600; $rn+=7200 ){
				if( $whora_par_actual == gmdate( "H", $rn ) ){
					if( $rn == 24*3600 ){
						echo "<option selected>24</option>";
					}
					else{
						echo "<option selected>".gmdate( "H", $rn )."</option>";
					}
				}
				else{
					if( $rn == 24*3600 ){
						echo "<option>24</option>";
					}
					else{
						echo "<option>".gmdate( "H", $rn )."</option>";
					}
				}
			  }

			  echo "</select></td></tr>";
		  }

		  echo "</table>";

		  echo "<br><br><br>";

		  //Seleccionar CENTRO DE COSTOS

		  //**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
						$cco="Ccohos,Ccourg";
						$sub="on";
						$tod="";
						$ipod="on";
						//$cco=" ";
						// $centrosCostos = consultaCentrosCostos($cco);
						
						$esServicioDomiciliario = false;
						if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' ){
							$esServicioDomiciliario = true;
							echo "<input type='HIDDEN' name='servicioDomiciliario' value='".$servicioDomiciliario."'>";
						}
						
						$centrosCostos = consultaCentrosCostosNoDomiciliarios( $conex, $wbasedato, $esServicioDomiciliario );

						echo "<table align='center' border=0>";
						$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);

						echo $dib;
						echo "</table>";
	  }
	  else{
		  echo "<center>";
		  echo "<b class='encabezadoTabla' style='font-size: 20pt;font-weight:bold;'>APLICACION DE MEDICAMENTOS</b>";
		  
		  echo "<span class=fila1 style='font-size:20pt;display:block;margin:20px;width:500px;padding:5px;font-weight:bold;'>Seleccione Ronda : </span>";
		  
		  echo "<select name='whora_par_actual' onChange='alCambiarRondaPiso( this );' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px;'>";
		  //echo "<option selected>".$whora_par_actual."</option>";

		  $selected = "selected";
		  // $j indica la cantidad de horas a restar mientras que $i es la hora que se muestra
		  //Al seleccionar la hora se cambia la fecha a la que se requiera
		  $rondasAMostrar = consultarAliasPorAplicacion( $conex, $wemp_pmla, "mostrarRondasAnteriorsIpods" );
		  
		  for( $j = $rondasAMostrar*2-2, $i = strtotime( $wfecha_actual." ".$whora_par_actual.":00:00 UTC" ); $j >= 0; $i -=2*3600, $j-=2 ){
			echo "<option $selected fechaactual='".gmdate( "Y-m-d", $i)."'>".( gmdate( "H", $i ) == 0 ? '24': gmdate( "H", $i ) )."</option>";
			$selected = "";
		  }
		  
		  echo "</select>";
		  
		  echo "<input type='HIDDEN' name='wfecha_actual' value='".$wfecha_actual."'>";
		  
		  echo "<label style='display:block;font-size:15pt;font-weight:bold;margin: 15px 0;'>Historia</label>";
		  echo "<span style='margin: 15px;display:block;'><input type='text' style='font-size:20pt;' name='pac_historia'></span>";
		  echo "<span style='margin: 15px;display:block;'><input type='button' value='Aceptar' onclick='enter3()' class='tipo4V'></span>";
		  echo "";
	  }
    }
	             //echo "<br>wcco ".$wcco;
  //============================================================================================================================
  //Mayo 26 de 2011
  //============================================================================================================================
  function CcoTieneZonas()
     {
      global $conex;
	  global $wbasedato;
	  global $wcco;

	  $wcco1 = explode("-",$wcco);
	  
	  $tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, trim($wcco1[0]) );
	  
	  //echo "wcco1".$wcco1;

	  // $q = " SELECT ccozon "
	      // ."   FROM ".$wbasedato."_000011 "
		  // ."  WHERE ccocod = '".trim($wcco1[0])."'";
	
		$q = "SELECT Arecod, Aredes
			  FROM ".$tablaHabitaciones.", ".$wbasedato."_000169
			 WHERE habcco = '".trim($wcco1[0])."'
			   AND habzon = Arecod
		  GROUP BY habzon, habcco";
		  
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($res);
	  $wcan = mysql_num_rows($res);   //Si el explode devuelve algo es porque hay zonas para el Cco
	  // $wcan = COUNT(EXPLODE(",",$row[0]));   //Si el explode devuelve algo es porque hay zonas para el Cco

	  // if ($wcan > 1)
	  if ($wcan > 0)
	     return true;      //Tiene Zonas
		else
          return false;	   //No tiene Zonas
	 }

  function SeleccionarZona()
     {
      global $conex;
	  global $wbasedato;
	  global $wcco;
	  global $wzona;
	  
	  global $servicioDomiciliario;

	  $wcco1 = explode("-",$wcco);
	  
	  $tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, trim($wcco1[0]) );

	  /////////////
	  echo "<center><table>";
      // $q = " SELECT ccozon "
	      // ."   FROM ".$wbasedato."_000011 "
		  // ."  WHERE ccocod = '".trim($wcco1[0])."'";

	  $q = "SELECT Arecod, Aredes
			  FROM ".$tablaHabitaciones.", ".$wbasedato."_000169
			 WHERE habcco = '".trim($wcco1[0])."'
			   AND habzon = Arecod
		  GROUP BY habzon, habcco";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      // $row = mysql_fetch_array($res);

	  // $wzon = explode(",",$row[0]);   //Devuelve las zonas

	  echo "<tr class=fila1><td align=center><font size=30>Seleccione la Zona : </font></td></tr>";
	  echo "</table>";
	  echo "<br><br><br>";
	  echo "<center><table>";
	  echo "<tr><td align=center><select name='wzona' style=' font-size:40px; font-family:Verdana, Arial, Helvetica, sans-serif;' onchange='enter()'>";
	  echo "<option>&nbsp</option>";
	  // for ($i=0;$i<=(count($wzon)-1);$i++)
	     // {
	      // echo "<option>".$wzon[$i]."</option>";
         // }
		while( $row = mysql_fetch_array($res) ){
			echo "<option value='".$row['Arecod']."'>".$row['Aredes']."</option>";;
		}
		
		if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' ){
			echo "<input type='HIDDEN' name='servicioDomiciliario' value='".$servicioDomiciliario."'>";
		}
		 
      echo "</select></td></tr>";
      echo "</table>";
	}
  //============================================================================================================================
  //Termina la modificacion del Mayo 26 de 2011
  //============================================================================================================================

  function elegir_historia($wzona)
     {
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  global $wemp_pmla;

	  global $wcco;
	  global $wnomcco;

	  global $whab;
	  global $whis;
	  global $wing;
	  global $wpac;
	  global $wtid;                                      //Tipo documento paciente
	  global $wdpa;
	  global $wfecha;

	  global $whora_par_actual;
	  global $wfecha_actual;
	  global $wfecha_a_buscar; //Abril de 23 de 2013

	  global $wusuario;
	  
	  global $servicioDomiciliario;

	  $wfecha_a_buscar = $wfecha_actual;	//Abril 24 de 2013

	  $wcco1=explode("-",$wcco);
	  
	  $tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $wcco1[0] );

	  if ($wzona == "")
	     $wzona = "%";

	  //Selecciono todos los pacientes del servicio seleccionado
	  $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, ubialp " //, pactid, pacced "
	      ."   FROM ".$tablaHabitaciones.", ".$wbasedato."_000018, root_000036, root_000037 "
	      ."  WHERE habcco        = '".$wcco1[0]."'"
	      ."    AND habali       != 'on' "            //Que no este para alistar
	      ."    AND habdis       != 'on' "            //Que no este disponible, osea que este ocupada
	      // ."    AND habcod        = ubihac "
	      ."    AND ubihis        = orihis "
	      ."    AND ubiing        = oriing "
	      ."    AND ubiald       != 'on' "
	      ."    AND ubiptr       != 'on' "
	      ."    AND ubisac        = '".$wcco1[0]."'"
	      ."    AND oriori        = '".$wemp_pmla."'"  //Empresa Origen de la historia,
	      ."    AND oriced        = pacced "
		  ."    AND oritid        = pactid "
	      ."    AND habhis     	  = ubihis "
	      ."    AND habing        = ubiing "
		  ."    AND ( UPPER(habzon) LIKE '".$wzona."' "
		  ."	 OR habzon NOT IN( SELECT arecod FROM ".$wbasedato."_000169 WHERE areest = 'on' ) )" //2019-03-06. Se agregar filtro de zona
		  //."    AND ubihis        = '264521' "
		  ."  GROUP BY 1,2,3,4,5,6,7 "
	      ."  ORDER BY Habord, Habcod ";

	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	  echo "<center><table>";

	  echo "<tr class=titulo>";
	  echo "<td colspan=5 align=center><font size=6><b>Servicio o Unidad: ".$wcco."</b></font></td>";
	  echo "</tr>";

	  echo "<tr class=encabezadoTabla>";
      echo "<th colspan=5><font size=6>Fecha Actual: </font><font size=6 color='00FF00'>".$wfecha_actual."&nbsp;&nbsp;&nbsp;&nbsp;</font><font size=6>Ronda a Aplicar: </font><font size=6 color='00FF00'>".$whora_par_actual.":00</font></th>";
      echo "</tr>";

	  echo "<tr class=encabezadoTabla>";
	  echo "<th><font size=6>Habitacion</font></th>";
	  echo "<th><font size=6>Historia</font></th>";
	  echo "<th><font size=6>Paciente</font></th>";
	  echo "<th><font size=6>Acción</font></th>";
	  echo "<th><font size=6>Kardex<br>Actualizado</font></th>";    //Julio 21 de 2011
	  echo "</tr>";

	  $whabant = "";
	  if ($num > 0)
	     {
		  $wclass="fila2";
		  for($i=1;$i<=$num;$i++)
			 {
			   $row = mysql_fetch_array($res);

			   if ($wclass=="fila1")
			      $wclass="fila2";
                 else
                    $wclass="fila1";

               $whab = $row[0];
			   $whis = $row[1];
			   $wing = $row[2];
			   $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
			   //$wtid = $row[7];                                      //Tipo documento paciente
			   //$wdpa = $row[8];                                      //Documento del paciente
			   $walp = $row[7];                                        //Indicador de Alta en Proceso

			   if( !esUsuarioLactario( $conex, $wbasedato, $wusuario ) ){
					$ccoQueryArts = '*';
				}
				else{
					// $ccoQueryArts = '1120';
					$ccoQueryArts = consultarCcoLactario( $conex, $wbasedato );
				}


			   $hoyNoConfirmado = false;	//Hoy no confimado
				//print_r(mysql_fetch_array($res1));
			   //Valido si tiene medicamentos para la RONDA o A NECESIDAD
			   query_articulos($whis, $wing, $wfecha_actual, $res1, $ccoQueryArts );
			   $num1 = mysql_num_rows($res1);
			   $wreg[0]=0;
			   $wkardex_Actualizado="Hoy";                             //Julio 21 de 2011




			   /********************************************************************************
			    * Enero 29 de 2013
				********************************************************************************/
			   if( $num1 == 0 and $wreg[0] == 0 ){	//Hay kardex hoy pero no esta confirmado
					
					//exit();
				    //Verifico si hay kardex hoy pero no esta confirmado
                   $q = " SELECT COUNT(*) "
				       ."   FROM ".$wbasedato."_000053 "
					   ."  WHERE karhis = '".$whis."'"
					   ."    AND karing = '".$wing."'"
					   ."    AND fecha_data = '".$wfecha_actual."'"
					   ."    AND karcon = 'off' "
					   ."    AND karcco = '$ccoQueryArts' ";
					   
				   $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	               $wreg = mysql_fetch_array($res1);

					if( $wreg[0] > 0 ){
					   
					   $hoyNoConfirmado = true;
					   
					   //Si wfecha_actual no es la misma del día actual traigo los articulos no confirmados
					   query_articulos_kardex_no_confirmado($whis, $wing, $wfecha_actual, $res1, $ccoQueryArts );
					   $num1 = mysql_num_rows($res1);
					   $wreg[0]=0;
					   $wkardex_Actualizado="Hoy";                             //Julio 21 de 2011

					   /************************************************************************************************
					    * Julio 3 de 2013
					    ************************************************************************************************/
					   // $hoyNoConfirmado = !consultarKardexConfirmadoPorCcoIpods( $whis, $wing, date( "Y-m-d", strtotime( $wfecha_actual." 00:00:00" ) + 24*3600 ), $ccoQueryArts );
					   
					   // if( $hoyNoConfirmado ){
							// $num1 = 0;
						// }
					   /************************************************************************************************/
					}
			   }
			   /********************************************************************************/
			  if( $wfecha_actual >= date( "Y-m-d" ) ){
			  
			   if ($num1==0)   //Si entra aca es porque NO hay articulos en fecha_actual para la RONDA indicada (hora_par_actual) o no hay Kardex generado
				  {
				   //Verifico si NO hay Kardex en esta fecha
				   $q = " SELECT COUNT(*) "
					   ."   FROM ".$wbasedato."_000053 "
					   ."  WHERE karhis = '".$whis."'"
					   ."    AND karing = '".$wing."'"
					   ."    AND fecha_data = '".$wfecha_actual."'"
					   // ."    AND karcon = 'on' "
					   ."    AND karcco = '$ccoQueryArts' ";
					  
				   $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				   $wreg = mysql_fetch_array($res1);
				  }

			   //Si no hay articulos para esa RONDA y NO HAY Kardex generado, traigo kardex del dia anterior, porque puede ser que
			   //en el Kardex del dia de hoy no hallan articulos para la RONDA actual, pero en el kardex del dia anterior si habia.
			   if ($num1 == 0 and $wreg[0]==0)     //wreg[0]==0: Indica que NO hay kardex de la fecha_actual
			      {
				   // $dia = time()-(1*24*60*60);   //Te resta un dia. (2*24*60*60) te resta dos y //asi...
				   // $wayer = date('Y-m-d', $dia); //Formatea dia

				   $dia = 1*24*60*60;   //Te resta un dia. (2*24*60*60) te resta dos y //asi...
				   $wayer = date('Y-m-d', strtotime( $wfecha_actual." 00:00:00" )- $dia ); //Formatea dia

				   query_articulos($whis, $wing, $wayer, $res1, $ccoQueryArts );
				   $num1 = mysql_num_rows($res1);

				   /********************************************************************************
				    * Enero 29 de 2013
					********************************************************************************/
				   if( $num1 == 0 ){	//Indica que no había kardex confirmado, por tanto intento traer los articulos con kardex sin confirmar

					   query_articulos_kardex_no_confirmado($whis, $wing, $wayer, $res1, $ccoQueryArts );
					   $num1 = mysql_num_rows($res1);

					   $hoyNoConfirmado = true;

					   /************************************************************************************************
					    * Julio 3 de 2013
					    ************************************************************************************************/
					   // $hoyNoConfirmado = !consultarKardexConfirmadoPorCcoIpods( $whis, $wing, $wfecha_actual, $ccoQueryArts );
					   /************************************************************************************************/
				   }
				   /********************************************************************************/

				   $wkardex_Actualizado="Ayer";                       //Julio 21 de 2011
				  }
			   }


			   if ($num1 > 0)
                  {
					
				   echo "<tr class=".$wclass.">";
				   echo "<td align=center><font size=6><b>".$whab."</b></font></td>";
				   echo "<td align=center><font size=6><b>".$whis."</b></font></td>";
				   echo "<td align=left  ><font size=6><b>".$wpac."</b></font></td>";

				   if( esUsuarioLactario( $conex, $wbasedato, $wusuario ) ){
					if( $whora_par_actual == "00" ){
						$whora_par_actual = "24";
					}
				   }

				   if( true || !$hoyNoConfirmado )
						echo "<td align=center><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&whab=".$whab."&wpac=".$wpac."&walp=".$walp."&wzona=".$wzona.( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."' class=tipo3V>Ver</A></td>";
					else
						echo "<td align='center' class='suspendido'><font size=6>Kardex No confirmado</font></td>";	//Enero 29 de 2013

				   //Julio 21 de 2011 - Se queitan las palabras 'Hoy' o 'Ayer'
				   //Asi estaba antes:  echo "<td align=center bgcolor=FFFF99><font size=6><b>".$wkardex_Actualizado."</b></font></td>";
				   if ($wkardex_Actualizado=="Ayer")
				      echo "<td align=center bgcolor=FFFF99><font size=6><b> </b></font></td>";
					  else
					     echo "<td align=center><font size=6><b> </b></font></td>";
				   //======================================================================================================
				   //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
				   /* $wafin=clienteMagenta($wdpa,$wtid,&$wtpa,&$wcolorpac);
				   if ($wafin)
					 {
					  echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
					 }
					else
					   echo "<td>&nbsp</td>";
						*/
				   //======================================================================================================

				   echo "</tr>";
				   echo "<tr><td colspan=4>&nbsp;</td></tr>";
				  }
			 }
		  }
		 else
		    echo "NO HAY HABITACIONES OCUPADAS";
	  echo "</table>";
	 }


  //La funcion comentada es para mostrar las habitaciones como TIPO BOTON, la anterior muestra como tipo lista y con los datos de los pacientes

  /*
  function elegir_historia()
     {
	  global $user;
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;
	  global $wemp_pmla;

	  global $wcco;
	  global $wnomcco;

	  global $whab;
	  global $whis;
	  global $wing;
	  global $wpac;
	  global $wtid;                                      //Tipo documento paciente
	  global $wdpa;

	  global $whora_par_actual;

	  $wcco1=explode("-",$wcco);

	  //Selecciono todos los pacientes del servicio seleccionado
	  $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2 " //, pactid, pacced "
	      ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000018, root_000036, root_000037 "
	      ."  WHERE habcco  = '".$wcco1[0]."'"
	      ."    AND habali != 'on' "            //Que no este para alistar
	      ."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
	      ."    AND habcod  = ubihac "
	      ."    AND ubihis  = orihis "
	      ."    AND ubiing  = oriing "
	      ."    AND ubiald != 'on' "
	      ."    AND ubiptr != 'on' "
	      ."    AND ubisac  = '".$wcco1[0]."'"
	      ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
	      ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
	      ."    AND habhis  = ubihis "
	      ."    AND habing  = ubiing "
	      ."  GROUP BY 1,2,3,4,5,6,7 "
	      ."  ORDER BY 1 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	  echo "<center><table>";

	  echo "<tr class=fila1>";
	  echo "<td colspan=12 align=center><font size=6><b>Servicio o Unidad: ".$wcco."</b></font></td>";
	  echo "</tr>";

	  echo "<tr class=encabezadoTabla>";
      echo "<th colspan=12 align=center><font size=6>Hora Aplicación: </font><font size=6 color='00FF00'>".$whora_par_actual.":00</font></th>";
      echo "</tr>";

	  $whabant = "";
	  if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);
		  for($i=1;$i<=$num;$i++)
			 {
			   if (is_integer($i/2))
                  $wclass="fila1";
                 else
                    $wclass="fila2";

               echo "<tr><td colspan=12>&nbsp;</td></tr>";
               echo "<tr class=".$wclass.">";

			   $j=1;
			   while ($j <= 6 and $i <= $num)
			      {
				   //echo "<td align=center><font size=8><b>".$whab."</b></font></td>";
			       //echo "<td align=center><font size=6><b>".$whis."</b></font></td>";
			       //echo "<td align=left  ><font size=6><b>".$wpac."</b></font></td>";

			       $whab = $row[0];
			       $whis = $row[1];
			       $wing = $row[2];
			       $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];

			       echo "<td align=center><A HREF='Entrega_ipods.ph?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."' class=tipo3V>".$whab."</A></td>";
			       $row = mysql_fetch_array($res);
			       $j++;
			       $i++;
	              }
		       echo "</tr>";
		       echo "<tr><td colspan=7>&nbsp;</td></tr>";
			 }
		  }
		 else
		    echo "NO HAY HABITACIONES OCUPADAS";
	  echo "</table>";
	 }
  */

function obtenerVectorAplicacionMedicamentos($fechaActual, $fechaInicioSuministro, $horaInicioSuministro, $horasPeriodicidad)
   {
	$arrAplicacion = array();

	$horaPivote = 1;

	$caracterMarca = "*";

	$vecHoraInicioSuministro   = explode(":",$horaInicioSuministro);
	$vecFechaInicioSuministro  = explode("-",$fechaInicioSuministro);

	$vecFechaActual			   = explode("-",$fechaActual);

	$fechaActualGrafica 	= mktime($horaPivote, 0, 0, date($vecFechaActual[1]), date($vecFechaActual[2]), date($vecFechaActual[0]));
	$fechaSuministroGrafica = mktime(intval($vecHoraInicioSuministro[0]), 0, 0, date($vecFechaInicioSuministro[1]), date($vecFechaInicioSuministro[2]), date($vecFechaInicioSuministro[0]));

	$horasDiferenciaHoyFechaSuministro = ROUND(($fechaActualGrafica - $fechaSuministroGrafica)/(60*60));

	if($horasDiferenciaHoyFechaSuministro <= 0 && abs($horasDiferenciaHoyFechaSuministro) >= 24)
	  {
	   $caracterMarca = "";
	  }

	/************************************************************************************************************************************************
	 * Febrero 22 de 2011
	 ************************************************************************************************************************************************/
	if( date( "Y-m-d", $fechaActualGrafica+(24*3600) ) == date( "Y-m-d", $fechaSuministroGrafica ) && $vecHoraInicioSuministro[0] == "00" ){
		$caracterMarca = "";
	}
	/************************************************************************************************************************************************/

	if ($horasPeriodicidad <= 0)
	  {
	   $horasPeriodicidad = 1;
	  }

	$horaUltimaAplicacion = abs($horasDiferenciaHoyFechaSuministro) % $horasPeriodicidad;

	$cont1 = 1;   //Desplazamiento de 24 horas
	$cont2 = 0;   //Desplazamiento desde la hora inicial

	$inicio = false;	//Guia de marca de hora inicial

	if ($fechaActual == $fechaInicioSuministro)
	   {
		$cont1 = intval($vecHoraInicioSuministro[0]);
		$arrAplicacion[$cont1] = $caracterMarca;

		while($cont1 <= 24)
		  {
			$out = "-";
			if ($cont2 % $horasPeriodicidad == 0)
			   {
			    $out = $caracterMarca;
			   }
			$cont2++;

			$arrAplicacion[$cont1] = $out;
			$cont1++;
		  }
	   }
	  else
	    {
		 while ($cont1 <= 24)
		   {
		    $out = "-";
			//Hasta llegar a la aplicacion
			if ($cont1 == abs($horaPivote+$horasPeriodicidad-$horaUltimaAplicacion) || ($cont1==1 && $horaUltimaAplicacion == 0))
			  {
			   $out = $caracterMarca;
			   $inicio = true;
			  }

			if ($inicio)
			  {
			   if($cont2 % $horasPeriodicidad == 0)
			     {
				  $out = $caracterMarca;
				 }
			   $cont2++;
		      }
			$arrAplicacion[$cont1] = $out;
			$cont1++;
		   }
	    }
	return $arrAplicacion;
   }

  //Se pregunta por cada medicamento si ya fue presionado el boton de APLICAR, para llevarlo a una variable unica de Aplicados y poderla enviar en el HREF
  function aplicados($num)
    {
	 global $waplicados;
	 global $wapl;

	 for ($i=1;$i<=$num;$i++)
	   {
		if (isset($wapl[$i]))
		   $waplicados=$waplicados."&wapl[".$i."]=".$wapl[$i];
	   }
	}

  //Se consigue la hora PAR anterior a la hora actual(si es hora impar) si no se deja la hora PAR actual
  function hora_par()
    {
	 global $whora_par_actual;
	 global $whora_par_anterior;
	 global $wfecha;
	 global $wfecha_actual;


	 $whora_Actual=date("H");
	 $whora_Act=($whora_Actual/2);

	 $wfecha_actual=date("Y-m-d");

	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
		 $whora_par_actual=$whora_Actual-1;
	     if ($whora_par_actual=="00" or $whora_par_actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
	    }
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
			else
		       $whora_par_actual=$whora_Actual;
	     }

	  if ($whora_Actual=="02" or $whora_Actual=="2")        //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	     $whora_par_anterior="24";
	    else
	      {
		   if (($whora_par_actual-2) == "00")               //Abril 12 de 2011
		      $whora_par_anterior="24";
		     else
	            $whora_par_anterior = $whora_par_actual-2;
		  }

	  if (strlen($whora_par_anterior) == 1)
	     $whora_par_anterior="0".$whora_par_anterior;

	  if (strlen($whora_par_actual) == 1)
	     $whora_par_actual="0".$whora_par_actual;
	}


  function buscoSiYaFueAplicado($whis, $wing, $wart, $wcco, $wdosis, $whora_par_actual, $wfecha_actual, &$wjustificacion, $wido)
    {
	 global $user;
	 global $conex;
	 global $wcenmez;
	 global $wafinidad;
	 global $wbasedato;
	 global $wtabcco;
	 global $winstitucion;
	 global $wactualiz;
	 global $wemp_pmla;
	 global $wfecha;

	 //===============================================================
	 //Paso la hora a formato de 12 horas
	 //===============================================================
	 if ($whora_par_actual < 12)
	    $whora_a_buscar=trim($whora_par_actual).":00 - AM";
	   else
	     if ($whora_par_actual == 12)
	        $whora_a_buscar=trim($whora_par_actual).":00 - PM";
	       else
	          if ($whora_par_actual == "24")                           //Abril 11 de 2011
	             $whora_a_buscar=($whora_par_actual-12).":00 - AM";    //El registro se hace con 00:00 y no con 12:00 AM por eso resto 24, antes restaba 12
	            else
	               $whora_a_buscar=($whora_par_actual-12).":00 - PM";

	//Dejo el formato a 24 horas con meridiano (AM - PM)
	$whora_a_buscar = gmdate( "H:00 - A", $whora_par_actual*3600 );
	//===============================================================


	 //===============================================================
	 $q = " SELECT COUNT(*) "
		     ."   FROM ".$wbasedato."_000298 "
		     ."  WHERE Enthis = '".$whis."'"
		     ."    AND Enting = '".$wing."'"
		     ."    AND Entfec = '".$wfecha_actual."'"
	         ."    AND Entron like '".trim($whora_a_buscar)."'"
		     ."    AND Entart = '".$wart."'"
		     ."    AND Entest = 'on' "
			 ."    AND Entido = ".$wido;
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);

	 if ($row[0] > 0)
	    return true;
	   else   //Si NO tiene aplicacion busco si tiene Justificacion de porque NO se aplico
	      {
		   //===============================================================
		   //Busco si tiene Justificacion
		   //===============================================================
		   $q = " SELECT jusjus "
			   ."   FROM ".$wbasedato."_000113 "
			   ."  WHERE jushis = '".$whis."'"
			   ."    AND jusing = '".$wing."'"
			   ."    AND jusfec = '".$wfecha_actual."'"
			   ."    AND jusron LIKE '".trim($whora_a_buscar)."'"
			   ."    AND jusart = '".$wart."'"
			   ."    AND jusido = ".$wido;
		   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   $num = mysql_num_rows($res);

		   if ($num > 0)
			  {
			   $row = mysql_fetch_array($res);
			   $wjustificacion = $row[0];
			  }
		     else
			    $wjustificacion="";

		   return false;     //Indica que no esta aplicado
		  }
    }

  function esANecesidad($wcond)
    {
	 global $conex;
	 global $wbasedato;
	 global $wfecha;

	 $q = " SELECT contip "
	     ."   FROM ".$wbasedato."_000042 "      //Tabla condiciones de administracion
	     ."  WHERE concod = '".$wcond."'";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);

	 if ($row[0] == "AN")      //'AN' : significa que es A NECESIDAD y es un tipo
	    return true;
       else
	      return false;
    }

  /**************************************************************************************************************
   * Busca si un medicamento fue suspendido en las ultimas dos rondas
   **************************************************************************************************************/
  function buscarSiEstaSuspendido($whis, $wing, $wart, $whora, $wfecha_actual, $wido)
    {
	 global $conex;
	 global $wbasedato;

	 if( $whora == 24 ){
		$whora = 0;
	 }

     //Convierto la ronda actual en formato Unix
	 $unixRonda = strtotime( "$wfecha_actual ".($whora).":00:00" );
	 $unixRonda -= 2*3600;

	 //Calculo la ronda final de aplicacion
	 $unixRondaPosterior = $unixRonda + 4*3600 - 1;

	 //Calculo la fecha y hora de las rondas a buscar el medicamento suspendido
	 $fhRondaAnterior = date( "Y-m-d H:i:s", $unixRonda );
	 $fhRondaPosterior = date( "Y-m-d H:i:s", $unixRondaPosterior );

	 // $q = " SELECT COUNT(*)  "
	     // ."   FROM ".$wbasedato."_000055 A "
	     // ."  WHERE kauhis  = '".$whis."'"
	     // ."    AND kauing  = '".$wing."'"
	     // ."    AND kaufec  = '".$wfecha_actual."'"
	     // ."    AND kaudes  = '".$wart."'"
	     // ."    AND kaumen  = 'Articulo suspendido' "
	     // ."    AND hora_data BETWEEN '".($whora-2).":00:00' AND '".($whora+2).":00:00'"   //Si la hora de suspensión esta entre la RONDA anterior y la actual se puede aplicar (No se toma como suspendido)
		 // ."    AND kauido  = ".$wido;

	 //Consulto si el medicamneto esta suspendido o no entre dos fechas
	 $q = " SELECT COUNT(*)  "
	     ."   FROM ".$wbasedato."_000055 A "
	     ."  WHERE kauhis  = '".$whis."'"
	     ."    AND kauing  = '".$wing."'"
	     ."    AND kaudes  = '".$wart."'"
	     ."    AND kaumen  = 'Articulo suspendido' "
		 ."    AND UNIX_TIMESTAMP( CONCAT( Kaufec,' ', Hora_data ) ) BETWEEN UNIX_TIMESTAMP( '$fhRondaAnterior' ) AND UNIX_TIMESTAMP( '$fhRondaPosterior' ) "
		 ."    AND kauido  = ".$wido;
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);

	 if ($row[0] > 0)
	    return "off";  //Indica que el articulo fue suspendido hace menos de dos horas, es decir que se puede aplicar, asi este suspendido
	   else
	      return "on"; //Indica que fue Suspendido hace mas de dos horas
	}

  function esdelStock($wart, $wcco)
    {
	 global $conex;
	 global $wbasedato;

	 //=======================================================================================================
	 //Busco si el articulo hace parte del stock     Febrero 8 de 2011
	 //=======================================================================================================
	 $q = " SELECT COUNT(*) "
	     ."   FROM ".$wbasedato."_000091 "
	     ."  WHERE Arscco = '".trim($wcco)."' "
	     ."    AND Arscod = '".$wart."'"
	     ."    AND Arsest = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);
	 //=======================================================================================================

	 if ($row[0] == 0)
	    return "off";
	   else
	      return "on";
	}

  function noAplicaConIPOD($wart, $wcco)             // Mayo 25 de 2011
    {
	 global $conex;
	 global $wbasedato;

	 //=======================================================================================================
	 //Busco si el articulo hace parte del stock     Febrero 8 de 2011
	 //=======================================================================================================
	 $q = " SELECT COUNT(*) "
	     ."   FROM ".$wbasedato."_000059 "
	     ."  WHERE (defcco = '".trim($wcco)."' "
		 ."     OR  defcco = '1050') "
	     ."    AND  defart = '".$wart."'"
	     ."    AND  defest = 'on' "
		 ."    AND  defipo = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);
	 //=======================================================================================================

	 if ($row[0] == 0)
	    return "off";     //Indica que se aplica con el IPOD
	   else
	      return "on";    //Indica que NO se aplica con el IPOD

	}


  function validar_aplicacion($whis, $wing, $wart, $wcco, $wdosis, &$wcant_aplicar, $wuniman, $warticulodelStock, $noenviar, &$saldo, &$saldoSinRecibir )
    {
	 global $wusuario;
	 global $conex;
	 global $wcenmez;
	 global $wafinidad;
	 global $wbasedato;
	 global $wtabcco;
	 global $winstitucion;
	 global $wactualiz;
	 global $wemp_pmla;

	 global $wmensaje;
	 
	 global $ccoayuda;
	 
	 $saldo = 0;

	 //Si se perdio la session NO DEJO GRABAR
	 if (isset($wusuario) and trim($wusuario)!="")
	    {
		 $wsalart = 0;
		 $wsalsre = 0;

		$wartfra=1;

		 $wcant_aplicar = ($wdosis/$wuniman)*$wartfra;

		 //=======================================================================================================
		 //Traigo el saldo del articulo y el Cco
		 //=======================================================================================================
		 if( empty($ccoayuda) ){
			 
			 $q = " SELECT SUM(spauen-spausa), spacco "
				 ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
				 ."  WHERE spahis  = '".$whis."'"
				 ."    AND spaing  = '".$wing."'"
				 ."    AND spaart  = '".$wart."'"
				 ."    AND ((spacco = '".trim($wcco)."' "
				 ."    AND  spacco = ccocod ) "
				 ."     OR (spacco  = ccocod "
				 //."    AND  ccotra  = 'on' "             //Permite hacer traslados	//Abril 6 de 2015. Se descomenta está línea
				 ."    AND  ccofac  = 'on')) "            //Puede facturar (cargos)
				 //."    AND  ROUND((spauen-spausa),3) >= '".$wcant_aplicar."' "            //Abril 6 de 2015
				 ."  AND ccoayu != 'on' "
				 ."  GROUP BY 2 "
				 ."  ORDER BY 1 DESC ";
		 }
		 else{
			  $q = " SELECT SUM(spauen-spausa), spacco "
				 ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
				 ."  WHERE spahis  = '".$whis."'"
				 ."    AND spaing  = '".$wing."'"
				 ."    AND spaart  = '".$wart."'"
				 ."    AND ((spacco = '".trim($wcco)."' "
				 ."    AND  spacco = ccocod ) "
				 ."     OR (spacco  = ccocod "
				 //."    AND  ccotra  = 'on' "             //Permite hacer traslados	//Abril 6 de 2015. Se descomenta está línea
				 ."    AND  ccofac  = 'on')) "            //Puede facturar (cargos)
				 //."    AND  ROUND((spauen-spausa),3) >= '".$wcant_aplicar."' "            //Abril 6 de 2015
				 ."  AND ccoayu = 'on' "
				 ."  GROUP BY 2 "
				 ."  ORDER BY 1 DESC ";
		 }
		 
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $row = mysql_fetch_array($res);

		 if ($row[0] > 0)
			{
			 $wsalart=round( $row[0], 3 );   //Saldo del articulo
			 $wccoapl=$row[1];   //C. Costo que grabo
			 $saldo = $wsalart;	 //Julio 24 de 2012
			}
		 //=======================================================================================================

		 if ($warticulodelStock=="on")
			{
			 //Mayo 25 de 2012. La cantidad a aplicar siempre es ($wdosis/$wuniman)*$wartfra;
			 // $wcant_aplicar=$wdosis;        //Si es del stock o sea que no se factura por las PDA's entonces aplico la cantidad que dice en el Kardex
			}
		 //=======================================================================================================

		 //Siempre se debe tratar como que no sea del stock para que no aplique si no hay saldo suficiente
		 if (true || $warticulodelStock=="off")   //Si entra es porque el articulo NO es del STOCK  ojo
			{
				if( empty($ccoayuda) ){
				 //Traigo la cantidad SIN RECIBIR de este articulo
				 // $q = " SELECT COUNT(*) "
					 // ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
					 // ."  WHERE fenhis = '".$whis."'"
					 // ."    AND fening = '".$wing."'"
					 // ."    AND ((fencco = '".trim($wcco)."'"
					 // ."    AND   fencco = ccocod ) "
					 // ."     OR  (fencco = ccocod "
					 // ."    AND   ccotra = 'on' "
					 // ."    AND   ccofac = 'on')) "
					 // ."    AND   fennum = fdenum "
					 // ."    AND   fdeart = '".$wart."'"
					 // ."    AND   fdedis = 'on' "
					 // ."    AND   fdeest = 'on' ";

				//Traigo la cantidad SIN RECIBIR de este articulo
				//Este query es para los de SF

				 $q  = "SELECT SUM( fdecan )
						  FROM
					(   SELECT SUM(fdecan - fdecar) fdecan"
					 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
					 ."  WHERE fenhis = '".$whis."'"
					 ."    AND fening = '".$wing."'"
					 ."    AND ((fencco = '".trim($wcco)."'"
					 ."    AND   fencco = ccocod ) "
					 ."     OR  (fencco = ccocod "
					 ."    AND   ccotra = 'on' "
					 ."    AND   ccoima != 'on' "		//Mayo 31 de 2012
					 ."    AND   ccofac = 'on')) "
					 ."    AND   fennum = fdenum "
					 ."    AND   fdeart = '".$wart."'"
					 ."    AND   fdedis = 'on' "
					 ."    AND   fdeest = 'on' "
					 ."    AND   fenest = 'on' "		//Marzo 5 de 2013
					 ." HAVING COUNT(*) > 0 ";
				$q .= "  UNION ALL"
					 ." SELECT SUM(fdecan - fdecar) fdecan"
					 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000143, ".$wbasedato."_000011 "
					 ."  WHERE fenhis = '".$whis."'"
					 ."    AND fening = '".$wing."'"
					 ."    AND ((fencco = '".trim($wcco)."'"
					 ."    AND   fencco = ccocod ) "
					 ."     OR  (fencco = ccocod "
					 ."    AND   ccotra = 'on' "
					 ."    AND   ccoima != 'on' "       //Mayo 31 de 2012
					 ."    AND   ccofac = 'on')) "
					 ."    AND   fennum = fdenum "
					 ."    AND   fdeart = '".$wart."'"
					 ."    AND   fdedis = 'on' "
					 ."    AND   fdeest = 'on' "
					 ."    AND   fenest = 'on' "        //Marzo 5 de 2013
					 ." HAVING COUNT(*) > 0 ";
				//Este segundo query es para los de CM
				 $q .="  UNION ALL"
					 ." SELECT COUNT( DISTINCT fdenum ) fdecan"
					 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
					 ."  WHERE fenhis = '".$whis."'"
					 ."    AND fening = '".$wing."'"
					 ."    AND ((fencco = '".trim($wcco)."'"
					 ."    AND   fencco = ccocod ) "
					 ."     OR  (fencco = ccocod "
					 ."    AND   ccotra = 'on' "
					 ."    AND   ccoima = 'on' "		//Mayo 31 de 2012
					 ."    AND   ccofac = 'on')) "
					 ."    AND   fennum = fdenum "
					 ."    AND   fdeari = '".$wart."'"
					 ."    AND   fdedis = 'on' "
					 ."    AND   fdeest = 'on' "
					 ."    AND   fdelot != '' "
					 ."    AND   fenest = 'on' "		//Marzo 5 de 2013
					 ." HAVING COUNT(*) > 0 ";
				 $q .="  UNION ALL"
					 ." SELECT COUNT( DISTINCT fdenum ) fdecan "
					 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000143, ".$wbasedato."_000011 "
					 ."  WHERE fenhis = '".$whis."'"
					 ."    AND fening = '".$wing."'"
					 ."    AND ((fencco = '".trim($wcco)."'"
					 ."    AND   fencco = ccocod ) "
					 ."     OR  (fencco = ccocod "
					 ."    AND   ccotra = 'on' "
					 ."    AND   ccoima = 'on' "        //Mayo 31 de 2012
					 ."    AND   ccofac = 'on')) "
					 ."    AND   fennum = fdenum "
					 ."    AND   fdeari = '".$wart."'"
					 ."    AND   fdedis = 'on' "
					 ."    AND   fdeest = 'on' "
					 ."    AND   fdelot != '' "
					 ."    AND   fenest = 'on' "        //Marzo 5 de 2013
					 ." HAVING COUNT(*) > 0 ) AS cantidades";
				 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				 $row = mysql_fetch_array($res);

				 $wsalsre = $row[0] or 0;  //Cantidad SIN RECIBIR		//Mayo 31 de 2012.	Si la consulta no arroja resulta entonces es 0
				 $saldoSinRecibir = $wsalsre;
			}
			else{
				 $wsalsre = $saldoSinRecibir = 0;
			}

			 if ($wsalart >= $wcant_aplicar and $wsalart > 0)
				{
				 if ($wsalart > $wsalsre)                   //Saldo del articulo es mayor a la cantidad que falta por recibir PUEDE APLICARSE maximo la diferencia
					 {
					  if (($wsalart-$wsalsre) >= $wcant_aplicar)   //Si la diferencia es mayor o igual a la dosis PUEDE APLICARSE
						 {
						  return true;
						 }
						else
						   {
							if ($wsalsre > 0)
							   $wmensaje = "No hay SALDO suficiente para Entregar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";
							  else
								 $wmensaje = "No hay SALDO suficiente para entregar";

							return false;
						   }
					 }
					else
					   {
						if ($wsalsre > 0)
						   $wmensaje = "No hay SALDO suficiente para Entregar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";
						  else
							 {
							  if ($wsalart > 0)
								 $wmensaje = "No hay DOSIS pendientes de entregar<br>o es del Stock";
							 }
						return false;
					   }
				}
			   else
				 {
				  //Si no tiene saldo suficiente, pero dice NO ENVIAR dejo de todas maneras registrar la aplicación.
				  // $esInsulina = esInsulina( $conex, $wbasedato, $wart );
				  $esQuimio = esQuimio( $conex, $wbasedato, $wart );
				  
				  if ($esQuimio && $noenviar=="on")      //Febrero 8 de 2011
					 {
					  return true;
					 }
					else
					  {
					   $wmensaje = "No hay SALDO suficiente para entregar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";
					   return false;
					  }
				 }
			}
		   else     //Si entra es porque ES del STOCK
			  {
			   // ==============================================================================
			   // Mayo 25 de 2011
			   // ==============================================================================
			   $wNoIpod = noAplicaConIPOD($wart, trim($wcco));    ///// No Aplica con Ipod /////

			   if ($wNoIpod == "off")
				  return true;                                    //Antes del Mayo 25 de 2011 solo estaba esta linea en el else
				 else
					{
					 $wmensaje = "Del Stock, al Facturarlo queda aplicado";
					 return false;
					}
			   // ==============================================================================
			  }
		}
	  else
	     {
		  ?>
		    <script>
			  alert("1. Debe cerrar esta ventana, Actualizar la pantalla (simbolo de flecha circular)  y volver a ingresar a la opción ** Entrega Ipods **");
		    </script>
		  <?php
		 }
	}


  //En esta funcion se busca si la el articulo ya ha sido aplicado en la ronda, para que no queden duplicados
  //porque la tabla 000015 no tiene indice unico y ya es muy dificil crearselo
  function buscarsiExiste($wfecha, $whora_a_grabar, $whis, $wing, $wart, $wido)
    {
	 global $wbasedato;
	 global $conex;

	 $q = " SELECT count(*) "
	     ."   FROM ".$wbasedato."_000298 "
	     ."  WHERE Enthis = '".$whis."'"
	     ."    AND Enting = '".$wing."'"
	     ."    AND Entfec = '".$wfecha."'"
	     ."    AND Entron = '".$whora_a_grabar."'"
	     ."    AND Entart = '".$wart."'"
	     ."    AND Entest = 'on' "
		 ."    AND Entido = ".$wido;
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);

	 if ($row[0] > 0)
	    return true;       //Si devuelve este valor es porque si existe
	   else
	      return false;    //No existe, entonces si se puede grabar
	}

/*
  function anular($whis, $wing, $wwart, $wcco, $wwdosis, $wwcanfra, $whora_par_actual, $wfecha_actual, $wwnoenviar, $wwarticulodelStock, $wido)
   {
	global $wbasedato;
	global $conex;
	global $wusuario;

	if (isset($wusuario) and trim($wusuario)!="")
	   {
	    //Marzo 17 de 2011
		echo "<script language='Javascript'> $.blockUI({ menssage: 'Un momento por Favor'}); </script>";

	    $wcco1=explode("-",$wcco);

		if ($whora_par_actual < 12)
			$whora_buscar=trim($whora_par_actual).":00 - AM";
		   else
			 if ($whora_par_actual == 12)
				$whora_buscar=trim($whora_par_actual).":00 - PM";
			   else
				  if ($whora_par_actual == "24")
					 $whora_buscar=($whora_par_actual-12).":00 - AM";
					else
					   {
					    //$whora_buscar=($whora_par_actual-12).":00 - PM";
						$whora_buscar=($whora_par_actual).":00 - PM";
					   }

		//Dejo el formato a 24 horas con meridiano (AM - PM)
		$whora_buscar = gmdate( "H:00 - A", $whora_par_actual*3600 );	//Mayo 31 de 2012

		//On
		 //echo $whora_buscar."<br>";

		//Traigo la cantidad aplicada, porque puede ser que desde el momento de la aplicación hasta la anulación hallan cambiado las fracciones
		$q= " SELECT aplcan, aplnen, aplsal "
		   ."   FROM ".$wbasedato."_000015 "
		   ."  WHERE aplhis = '".$whis."'"
		   ."    AND apling = '".$wing."'"
		   ."    AND aplart = '".$wwart."'"
		   ."    AND aplcco = '".trim($wcco1[0])."'"
		   ."    AND aplron = '".$whora_buscar."'"
		   ."    AND aplest = 'on' "
		   ."    AND aplfec = '".$wfecha_actual."'"
		   ."    AND aplido = ".$wido;

		   //On
		   //if ($whis=="434451")
		   //   echo $q."<br>";

		$resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($resanu);
		if ($num > 0)
		   {
			$rowcan = mysql_fetch_array($resanu);     //Este es la cantidad aplicada en esa ronda y es la cantidad que se debe restar en la salida de la tabla 000004
			$wcanapl=$rowcan[0];
			$wnoenviado=$rowcan[1];
		   }
		  else
		     {
			  $wcanapl=0;
			  $wnoenviado="off";
			 }

		// if ($wwnoenviar == "off" and $wwarticulodelStock=="off")	//Esto debe hacerse para todos los medicamentos
		if (true)	//Esto debe hacerse para todos los medicamentos
		   {
			//Aca traigo el saldo del articulo para el paciente, de acuerdo con la prioridad del centro de costo
			$q = " SELECT min(ccopap), spacco "  //Tomo la prioridad MAXIMA del centro costo que tenga saldo del articulo
				."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
				."  WHERE spahis = ".$whis
				."    AND spaing = ".$wing
				."    AND spaart = '".strtoupper($wwart)."'"
				."    AND spacco = ccocod "
				."    AND spausa > 0 "					  //Solo los que tengan el articulo aplicado
				."  GROUP BY 2 ";
			$resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$rowanu = mysql_fetch_array($resanu);
			$wccoapl=$rowanu[1];

			//Traigo la cantidad de salidas, porque si la aplicacción a anular es mayor a lo que hay en salidas no se deja hacer la anulación
			$q= " SELECT spausa "
			   ."   FROM ".$wbasedato."_000004 "                                     //Salidas Unix
			   ."  WHERE spahis = '".$whis."'"
			   ."    AND spaing = '".$wing."'"
			   ."    AND spaart = '".$wwart."'"
			   ."    AND spacco = '".trim($wccoapl)."'";
			$resusa = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($resusa);

			if ($num > 0)
			   {
				$rowusa = mysql_fetch_array($resusa);     //Este es la cantidad aplicada en esa ronda y es la cantidad que se debe restar en la salida de la tabla 000004
				$wcanusa=$rowusa[0];
			   }
			  else
				 $wcanusa=0;

		   }
		  else
			{
			 $wcanusa=0;
			 $wccoapl=$wcco1[0];
			}

		if (($wcanapl > 0 and $wcanusa >= $wcanapl) or $wwnoenviar == "on")
		   {
		    //Anulo la aplicación
			$q= " UPDATE ".$wbasedato."_000015 "
			   ."    SET aplest = 'off' "
			   ."  WHERE aplhis = '".$whis."'"
			   ."    AND apling = '".$wing."'"
			   ."    AND aplart = '".$wwart."'"
			   ."    AND aplcco = '".trim($wcco1[0])."'"
			   ."    AND aplron = '".$whora_buscar."'"
			   ."    AND aplest = 'on' "
			   ."    AND aplfec = '".$wfecha_actual."'"
			   ."    AND aplido = ".$wido;
			$resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			// if ($wnoenviado!="on")   //Solo si el articulo realmente no fue enviado
			if ( $rowcan[2] == "on" )   //Agosto 14 de 2012. Se hace si mueve saldo
			   {
			    //Esto se hace porque puede que el articulo diga no enviar en el Kardex, pero antes si se habia enviado, es decir,
                //esto sirve para controlar que solo se afecte el saldo de las aplicaciones enviadas
				$q= " UPDATE ".$wbasedato."_000004 "
				   ."    SET spausa = spausa - ".round($wcanapl,3)                       //Salidas Unix
				   ."  WHERE spahis = '".$whis."'"
				   ."    AND spaing = '".$wing."'"
				   ."    AND spaart = '".$wwart."'"
				   ."    AND spacco = '".trim($wccoapl)."'";
				$resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			   }
		   }
		  else
			 {
			  if ($wwarticulodelStock=="off")
				 {
				  ?>
				   <script>
					  alert("No se puede ANULAR porque la cantidad aplicada es Mayor al Saldo en salidas");
				   </script>
				  <?php
				 }
				else
				  {
				   $q= " UPDATE ".$wbasedato."_000015 "
					  ."    SET aplest = 'off' "
					  ."  WHERE aplhis = '".$whis."'"
					  ."    AND apling = '".$wing."'"
					  ."    AND aplart = '".$wwart."'"
					  ."    AND aplcco = '".trim($wcco1[0])."'"
					  ."    AND aplron = '".$whora_buscar."'"
					  ."    AND aplest = 'on' "
					  ."    AND aplfec = '".$wfecha_actual."'"
					  ."    AND aplido = ".$wido;
				   $resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  }
			 }
		//Marzo 17 de 2011
		echo "<script>$.unblockUI();</script>";
	   }
	  else
		 {
		  ?>
			<script>
			  alert("2. Debe cerrar esta ventana, Actualizar la pantalla (simbolo de flecha circular)  y volver a ingresar a la opción ** Aplicacion Ipods **");
			</script>
		  <?php
		 }
   }
*/

  function justificacionNoAplicacion($i, $wjustificacion, $waplicado, $whis, $wing, $wfecron, $wronda, $wart, $wido)
   {
    global $conex;
	global $wbasedato;

	global $wjus;

	if ($waplicado != "on")
	   {
		//Seleccionar JUSTIFICACIONES
		$q = " SELECT juscod, jusdes "
			."   FROM ".$wbasedato."_000023"
			."  WHERE justip = 'A' "       //A: Aplicacion en Ipod
			."    AND jusest = 'on' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		echo "<tr>";
		echo "<td colspan=3 bgcolor=FFFF99><font size=5><b>Seleccione la Justificacion de NO Aplicar: </b></font></td>";
		// echo "<td align=center colspan=3 bgcolor=FFFF99><select name='wjus[".$i."]' size='1' style=' font-size:20px; font-family:Verdana, Arial, Helvetica, sans-serif; height:25px' onchange='enter()'>";
		echo "<td align=center colspan=3 bgcolor=FFFF99><select name='wjus[".$i."]' size='1' style=' font-size:20px; font-family:Verdana, Arial, Helvetica, sans-serif; height:25px' onchange='enter2( $i, $wido )'>";

		echo "<option selected>".$wjustificacion."</option>";
		for ($j=1;$j<=$num;$j++)
		   {
			$row = mysql_fetch_array($res);
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		   }
		echo "<option> </option>";
		echo "</select></td></tr>";
	   }
	  else
         {
		  //Si ya se habia colocado una justificacion, pero luego se aplico el medicamento
		  //entonces elimino la justificaion que tenia, siempre lo hago por descarte, asi no
		  //se hubiese colocado justificacion
          $q = " DELETE FROM ".$wbasedato."_000113 "
		      ."  WHERE jushis = '".$whis."'"
			  ."    AND jusing = '".$wing."'"
			  ."    AND jusart = '".$wart."'"
			  ."    AND jusfec = '".$wfecron."'"
			  ."    AND jusron = '".$wronda."'"
			  ."    AND jusido = ".$wido;
		  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         }
   }


function grabar_justificacion($wart, $wjust, $whis, $wing, $wfecron, $wronda, $wido)
   {
    global $wusuario;
    global $wbasedato;
	global $conex;
	global $wfecha;
	global $whora;

	$wjust=trim($wjust);

	if (!empty($wjust))   //Si hay justificacion
	   {
	    $q = " DELETE FROM ".$wbasedato."_000113 "
			."  WHERE jushis = '".$whis."'"
			."    AND jusing = '".$wing."'"
			."    AND jusart = '".$wart."'"
			."    AND jusfec = '".$wfecron."'"
			."    AND jusron = '".$wronda."'"
			."    AND jusido = ".$wido;
	     $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	    $q = " INSERT INTO ".$wbasedato."_000113(   Medico       ,   Fecha_data ,   Hora_data ,   jushis  ,   jusing  , jusart    ,   jusfec     ,    jusron    ,   jusjus   ,  jusido , Seguridad        ) "
		    ."                            VALUES('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$whis."','".$wing."','".$wart."','".$wfecron."', '".$wronda."','".$wjust."',".$wido.", 'C-".$wusuario."') ";

		$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   }
	  else
	    {
         $q = " DELETE FROM ".$wbasedato."_000113 "
			."  WHERE jushis = '".$whis."'"
			."    AND jusing = '".$wing."'"
			."    AND jusart = '".$wart."'"
			."    AND jusfec = '".$wfecron."'"
			."    AND jusron = '".$wronda."'"
			."    AND jusido = ".$wido;
	     $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}
   }

//=========================================================================================================
// Septiembre 18 de 2012
// Función que retorna la hora correcta para la función strtotime
// Antes de PHP 5.3.0, 24:00 no era un formato válido y strtotime() devolvía FALSE.
// En la versión actual de PHP (5.3.3) en el servidor strtotime(2012-09-18 24:00:00) == strtotime(2012-09-19 00:00:00) y para éste programa
// se necesita strtotime(2012-09-18 24:00:00) == strtotime(2012-09-18 00:00:00)
//
//=========================================================================================================
function valHoraStrToTime($hora)
  {
	if($hora=='24')
		return '00';
	else
		return $hora;
  }

//Consulta si un centro de costos es de urgencias
function esUrgencias($conex, $wbasedato, $servicio){
		
	$es = false;

	$q = "SELECT
				Ccourg
		 	FROM
		 		".$wbasedato."_000011
			WHERE
				Ccocod = '".$servicio."' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccourg'] == 'on') ? $es = true : $es = false;
	}

	return $es;
}

//=========================================================================================================
// Noviembre 17 de 2011
//=========================================================================================================
function validar_ipods($wcco)
  {
   global $conex;
   global $wbasedato;
   global $wemp_pmla;

   global $whora_par_actual;
   global $wfecha_actual;

   global $wusuario;

   $wcc = explode("-",$wcco);

   $q = " SELECT ccoipo, ccores "
       ."   FROM ".$wbasedato."_000011 "
	   ."  WHERE ccocod = '".trim($wcc[0])."'";
   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

   $row = mysql_fetch_array($res);

	$whora_aux=$whora_par_actual;

	if( true /*!esUsuarioLactario( $conex, $wbasedato, $wusuario )*/ ){

		if ($row[0] == "on")
		  {
		   //On
		   ?>
		   <script>
			 validar_browser();
		   </script>
		   <?php
		  }

		$wrondaAnterior=($whora_par_actual-2);

		if( !esUsuarioLactario( $conex, $wbasedato, $wusuario ) ){
			if ($whora_par_actual>="14" and $whora_par_actual<="22" and (string)date("H") < "12")
			 {
			  $dia = time()-(1*24*60*60); //Te resta un dia
			  $wayer = date('Y-m-d', $dia); //Formatea dia
			  $wfecha_actual=$wayer;
			 }
		}

		//En el sgte if se evalua que NO haya quedado sin aplicar algún medicamento en la ronda anterior
		//si no es asi, entonces se va por el else y muestra de q habitaciones quedo faltando y hasta
		//q esto no sea solucionado no deje aplicar nada de la ronda actual
		//Tambien se evalua que el Centro de Costos tenga predido el indicador de Aplicacion con Restriccion (ccores)
		if ($whora_par_actual == "24")
		   {
			$dia = strtotime( $wfecha_actual )-(1*24*60*60); //Te resta un dia
			$wayer = date('Y-m-d', $dia); //Formatea dia
			$wfec_act=$wayer;
		   }
		   else
			  $wfec_act=$wfecha_actual;

		//Solo hace la validación para kardex de enfermería
		if( !esUsuarioLactario( $conex, $wbasedato, $wusuario ) ){
			$ccoQueryArts = '*';
		}
		else{
			$ccoQueryArts = consultarCcoLactario( $conex, $wbasedato );
		}

		$wok=estaAplicadoCcoPorRonda( trim($wcc[0]), $wfec_act, $wrondaAnterior, $habitacionesFaltantes, $ccoQueryArts );

		if ($row[1] == "on" and !$wok)
		   {
			$wtodas_hab="";
			//Muestro las habitaciones
			for ($i=0; $i < count($habitacionesFaltantes); $i++)
			   {
				$wtodas_hab=$wtodas_hab.$habitacionesFaltantes[$i].", ";
			   }

			$wmsj = "*** A T E N C I O N ***  EN LAS HABITACIONES: ( ".$wtodas_hab." ) NO SE HA TERMINADO DE APLICAR LA RONDA DE LAS: ( ".$wrondaAnterior." ), DEBE APLICAR O JUSTIFICAR SU NO APLICACION";

			echo " <script>
				   alert ('$wmsj');

				   window.location.href='Entrega_ipods.php?wemp_pmla=".$wemp_pmla.( isset($consultaPorHistoria) ? '&consultaPorHistoria=on' : '' )."'
				   </script> ";

		   }
		$whora_par_actual=$whora_aux;
	}
  }
//=========================================================================================================

//===========================================================================================================================================
//*******************************************************************************************************************************************



  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L
  //===========================================================================================================================================
  //===========================================================================================================================================
  $dataUsuario = consultarUsuario($conex,$wusuario);
  
  $permisoUsoAplicacion = "1";
  
    
  echo "<form name='apl_ipods' action='Entrega_ipods.php' method=post>";

  if (!isset($wfecha)) $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");

  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

  mostrar_empresa($wemp_pmla);
  
  if( !empty( $pac_historia ) ){
	  
		$whis 		= $pac_historia;
		$wing 		= consultarUltimoIngresoHistoria($conex, $whis, $wemp_pmla );
	  
		$info_pac	= informacionPacienteM18( $conex, $wbasedato, $wemp_pmla, $historia, $wing );
		$ubi_pac 	= consultarUbicacionPaciente( $conex, $wbasedato, $whis, $wing, $wemp_pmla );
		
		$wpac 			= trim( $info_pac['primerNombre']." ".$info_pac['segundoNombre'] )." ".trim( $info_pac['primerApellido']." ".$info_pac['segundoApellido'] );
		$wcco			= $ubi_pac->servicioActual."-".consultarCentroCosto($conex, $ubi_pac->servicioActual, $wbasedato )->nombre;
		$whab 			= $ubi_pac->habitacionActual;
		$walp 			= 'off';
		$wzona 			= '';
		$wfecha_actual 	= date("Y-m-d");
		
		$wcenmez 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'cenmez');
		echo "<input type='HIDDEN' name='pac_historia' value='".$pac_historia."'>";
  }

  if (!isset($wcco))
     {
      hora_par();

	  elegir_centro_de_costo();

      // echo "<input type='HIDDEN' name='wfecha_actual' value='".$wfecha_actual."'>";

      echo "<br><br><br>";
  	  echo "<table>";
  	  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()' class=tipo4V></td></tr>";
  	  echo "</table>";
     }
	else
       {
		// ******CAMBIO PARA LA CONTIGENCIA, para que no me valide el navegador ni me bloquee por los medicamentos pendientes por aplicar
		if(!isset($contigen))
		{
		//****** FIN CAMBIO

	    //=====================
	    // Noviembre 17 de 2011
		//=====================
	    validar_ipods($wcco);
		//=====================
		}  /// ******CAMBIO PARA LA CONTIGENCIA

		if( !esUsuarioLactario( $conex, $wbasedato, $wusuario ) ){
			if ($whora_par_actual=="22" and (string)date("H") < "12")
			 {
			  $dia = time()-(1*24*60*60); //Te resta un dia
			  $wayer = date('Y-m-d', $dia); //Formatea dia
			  $wfecha_actual=$wayer;
			 }
			else
			  $wfecha = date("Y-m-d");
		}
		else{
			$wfecha = date("Y-m-d");
		}

	    echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
		if (isset($wzona))
		   echo "<input type='HIDDEN' name='wzona' value='".$wzona."'>";
		  else
             $wzona="";

		echo "<input type='HIDDEN' name='wfecha' value='".$wfecha."'>";
	    echo "<input type='HIDDEN' name='whora_par_actual' value='".$whora_par_actual."'>";
	    echo "<input type='HIDDEN' name='wfecha_actual' value='".$wfecha_actual."'>";
		
		if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' ){
			echo "<input type='HIDDEN' name='servicioDomiciliario' value='".$servicioDomiciliario."'>";
		}

		if (isset($wusuario) and TRIM($wusuario))
		   {
			if (isset($whis))
			   {
				echo "<input type='HIDDEN' name='whis' value='".$whis."'>";
				echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
				echo "<input type='HIDDEN' name='whab' value='".$whab."'>";
				echo "<input type='HIDDEN' name='wpac' value='".$wpac."'>";

				$fechaKardex = $wfecha_actual;	//Esta variable indica cual fue la fecha de consulta para el kardex
				$wfecha_a_buscar = $fechaKardex;	//Abril 24 de 2013

				$wcc = explode( "-", $wcco );
				$esUrgencias = esUrgencias( $conex, $wbasedato, trim( $wcc[0] ) );
				if( $esUrgencias ){
					$ccoQueryArts = '%';
				}
				else if( !esUsuarioLactario( $conex, $wbasedato, $wusuario ) ){
					$ccoQueryArts = '*';
				}
				else{
					// $ccoQueryArts = '1120';
					$ccoQueryArts = consultarCcoLactario( $conex, $wbasedato );
				}

				query_articulos($whis, $wing, $wfecha_actual, $res, $ccoQueryArts );
				$num = mysql_num_rows($res);

				if ($num == 0)  //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
				   {
				     query_articulos_kardex_no_confirmado( $whis, $wing, $wfecha_actual, $res, $ccoQueryArts );
					 $num = mysql_num_rows($res);
					// $dia = time()-(1*24*60*60);   //Te resta un dia. (2*24*60*60) te resta dos y //asi...
					// $wayer = date('Y-m-d', $dia); //Formatea dia

					if( $num == 0 && $wfecha_actual >= date( "Y-m-d" ) ){
						//resto un día de la $wfecha_actual
						$dia = (1*24*60*60);   //Te resta un dia. (2*24*60*60) te resta dos y //asi...
						$wayer = date('Y-m-d', strtotime( $wfecha_actual." 00:00:00" ) - $dia ); //Formatea dia

						$fechaKardex = $wayer;

						query_articulos($whis, $wing, $wayer, $res, $ccoQueryArts );
						$num = mysql_num_rows($res);

						/************************************************************************************************
						 * Julio 3 de 2013
						 ************************************************************************************************/
						//Si no está confirmado hay kardex el día anterior pero está confirmado para días posteriores
						//dejo aplicar
						if( $num == 0 )
						{
						   query_articulos_kardex_no_confirmado( $whis, $wing, $wayer, $res, $ccoQueryArts );
						   $num = mysql_num_rows($res);
						}
						/************************************************************************************************/
					  }
				   }

				echo "<center><table>";
				echo "<tr class=encabezadoTabla>";
				echo "<th><font size=6>Habitación: "."</font><font size=9 color='00FF00'>".$whab."&nbsp;&nbsp;&nbsp;&nbsp;</font><font size=6>Historía: ".$whis."</font></th>";
				echo "</tr>";
				echo "<tr class=encabezadoTabla>";
				echo "<th><font size=6>Paciente: </font><font size=6 color='00FF00'>".$wpac."</font></th>";
				echo "</tr>";
				echo "<tr class=encabezadoTabla>";
				echo "<th><font size=6>Fecha Actual: </font><font size=6 color='00FF00'>".$wfecha_actual."&nbsp;&nbsp;&nbsp;&nbsp;</font><font size=6>Ronda a Aplicar: </font><font size=6 color='00FF00'>".$whora_par_actual.":00</font></th>";
				echo "</tr>";
				
				if( !esUsuarioLactario( $conex, $wbasedato, $wusuario ) ){
					
					if( $whora_par_actual === "0" )
						$whora_par_actual = "00";
					
					if( !empty($wfecha_actual) && !empty( $whora_par_actual ) ){
						
						  $fechaHoraAplicar = strtotime( $wfecha_actual." ".( $whora_par_actual == "24" ? "00" : $whora_par_actual ).":00:00" );

						  //Total de rondas que se muestran al usuario
						  $rondasMostradas = consultarAliasPorAplicacion( $conex, $wemp_pmla, "mostrarRondasAnteriorsIpods" );

						  if( $fechaHoraAplicar < time()-$rondasMostradas*2*3600 || $fechaHoraAplicar > time() ){
							  $permisoUsoAplicacion = "0";
						  }
					  }
				}
				  
				echo "<input type='HIDDEN' name='permisoUsoAplicacion' id='permisoUsoAplicacion' value='".$permisoUsoAplicacion."'>";


				if (isset($walp) and $walp == "on")
				   {
					echo "<tr>";
					echo "<th bgcolor='FFCC66'><font size=6><b><blink id=blink>EN PROCESO DE ALTA</blink></b></font></th>";
					echo "</tr>";
				   }
				echo "</table>";

				echo "<br>";

				//Octubre 6 de 2010
				//=================================================================================================================
				//Traigo el Diagnostico
				$q = " SELECT kardia, fecha_data "
					."   FROM ".$wbasedato."_000053 "
					."  WHERE karhis  = '".$whis."'"
					."    AND karing  = '".$wing."'"
					."    AND karcco  = '*' "
					."    AND karcon  = 'on' "
					."  ORDER BY 2 desc ";
				$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row1 = mysql_fetch_array($res1);

				// echo "<center><table>";
				// echo "<tr><td bgcolor='FFFF99'><font size=6><b>Diagnostico  : </b>".$row1[0]."</font></td></tr>";        //Diagnostico
				// echo "</table>";
				
				// Consultar alertas en movhos_000220
				$alergiasAnteriores = consultarAlergiaAlertas($whis, $wing);
				
				// Consultar los diagnosticos actuales del paciente
				$diagnosticos = consultarDiagnosticoPaciente($conex,$wbasedato,$whis,$wing,false);
				
				echo "<center><table>";
				// echo "<tr><td bgcolor='FFFF99'><font size=6><b>Diagnostico  : </b>".$row1[0]."</font></td></tr>";        //Diagnostico
				echo "<tr><td bgcolor='FFFF99'><font size=6><b>Diagnostico  : <br></b>".str_replace( "\n","<br>", $diagnosticos)."</font></td></tr>";        //Diagnostico
				if($alergiasAnteriores!="")
				{
					echo "<tr><td bgcolor='FFFF99'><font size=6><b>Alertas  : <br></b>".str_replace( "\n","<br>", $alergiasAnteriores )."</font></td></tr>";        //Alertas
				}
				echo "</table>";
				//=================================================================================================================

				echo "<br>";

				echo "<center><table>";
				echo "<tr class=encabezadoTabla>";
				echo "<th colspan=2><font size=5>Nutrición</font></th>";
				echo "<th><font size=5>Dosis</font></th>";
				echo "<th><font size=5>Vía</font></th>";
				echo "<th><font size=5>Condición</font></th>";
				echo "<th><font size=5>Frecuencia</font></th>";
				echo "<th colspan=2><font size=5>Acción</font></th>";

				$waplicados="";

				///////CAMBIO PARA CONTIGENCIA, Objetivo: buscar solo el medicamento que necesito aplicar de la contigencia
				$encontro_art=0;
				if(isset($contigen))
				{
					for ($i=1;$i<=$num;$i++)
					{
						$row = mysql_fetch_array($res);
						if($row[22]==$ido)
						{
							$i=$num+1;
							$num=1;
							$encontro_art=1;
						}
					}
					if($encontro_art==0)		//reinicio el fech array y destruyo el wapl que mande en la aplicacion de la contigencia
						{
							mysql_data_seek($res, 0);
							unset($wapl);
						}
				}
				///////FIN CAMBIO PARA CONTIGENCIA
				
				$ccoDispensaInsumos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "ccoHabilitadosDispensacionInsumos" );
				$ccoDispensaInsumos = explode( ",", $ccoDispensaInsumos );
				
				$dispensaInsumos = ( in_array( trim( $wcc[0] ), $ccoDispensaInsumos ) || in_array( '*', $ccoDispensaInsumos ) ) ? true : false;

				aplicados($num);   //Por cada submit hago esto, es decir, llevo a una variable todos los medicamentos aplicados para cuando regrese el submit
				$j=1;

				for ($i=1;$i<=$num;$i++)   //Recorre cada uno de los medicamentos
				{
					if($encontro_art==0) // Si no estamos en contigencia realizo el fetch_array completo
					{
						$row = mysql_fetch_array($res);
					}
					
					$dispensable = esDispensableInc( $conex, $wbasedato, $row[1], $row[22], $whis, $wing );
					
					//Si es un medicamento que no es dispensable y está cómo no enviar no se muestra
					if( ( $row[16] == 'on' && $dispensable == 'on' ) || ( esArticuloGenerico( $row[1] ) && esArticuloGenericoLevIC( $conex, $wbasedato, $row[1], $row[22], $whis, $wing ) ) ){
						continue;
					}
					
					 //Febrero 15 de 2018
					 $esArticuloAyudaDx = false;
					 if( empty( $ccoayuda ) ){
						$esArticuloAyudaDx = esArticuloDeAyudaDiagnostica( $conex, $wbasedato, $whis, $wing, $fechaKardex, $row['1'], $row[22] );
							
						$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $whis, $wing );
						
						//Si epaciente está en una ayuda dx y el articulo pertenece a la misma ayuda dx no lo valido
						if( !empty( $ubicacionPaciente->servicioTemporal ) ){
							$esArticuloDeAyudaTemporal = esArticuloDeAyudaDiagnosticaPorCco( $conex, $wbasedato, $whis, $wing, $fechaKardex, $row['1'], $row[22], $ubicacionPaciente->servicioTemporal );
							
							if( $esArticuloDeAyudaTemporal )
								continue;
						}
					 }
					 
					 if( $esArticuloAyudaDx )
						 continue;

					 //Si es un cco que se debe dispensar por el programa de DISPENSACION DE INSUMOS no se muestra
					 $esMmq = esMMQInc( $conex, $wbasedato, $row[1] );
					 if( !$esUrgencias && $dispensaInsumos && $esMmq )
						 continue;
					
					$dosisLevIC = consultarDosisAAplicarLevInc( $conex, $wbasedato, $row[1], $row[22], $whis, $wing );
					$dosisOriLevIC = $row[7];
					if( $dosisLevIC !== false ){
						$row[7] = $dosisLevIC;
					}

					// if (isset($wanular[$i]) and $wanular[$i]=="on" and $row[1]==$wart[$i])
					if( isset( $wanular ) && in_array( "on", $wanular ) and in_array( $row[1], $wart ) && in_array( $row[22], $wido ) )
					{
						
						 anularEntregaMovhos($whis, $wing, $wart[$i], $wcco, $wdosis[$i], $wcanfra[$i], $whora_par_actual, $wfecha_actual, $wnoenviar[$i], $wStock[$i], $wido[$i]);
						
						$url = devolver($url, $wemp_pmla, $wfecha_actual,  $whora_par_actual, $whis, $wing, $wcco, $whab, $wpac, $wzona);
						
						header("Location: ". $url );

						
							
						
					}

					if( $whora_par_actual == 24 ){
						//                                                         Fecha Actual 									 Articulo, fecha Inicio aplicacion, hora inicio aplicacion, frecuencia
						$arrAplicacion = obtenerVectorAplicacionMedicamentos( date( "Y-m-d", strtotime( $wfecha_actual ) - 24*3600 ), $row[4]                ,  $row[5]              , $row[6]);
					}
					else{
						//                                                 Fecha Actual Articulo, fecha Inicio aplicacion, hora inicio aplicacion, frecuencia
						$arrAplicacion = obtenerVectorAplicacionMedicamentos($wfecha_actual        , $row[4]                ,  $row[5]              , $row[6]);
					}
					$horaArranque = 0;

					$caracterMarca = "*";

					//======================================================================
					if ($row[16]=="on")
					   $wnoenviar[$i]="on";
					  else
					  $wnoenviar[$i]="off";  //Indica si el medicamento se Envia o No
					//======================================================================

					$wufr  =$row[8];         //Cantidad de fracciones que tiene la presentacion del medicamento
					$wdosis=$row[7];         //Dosis a plicar, viene del Kardex u Ordenes

					$wok=false;              //Indicador para verificar si el articulo se puede aplicar o no, según la validaciones de la función 'validar_aplicacion()'
					$waplicado="off";

					//========================================================================================================================================================================
					$wanecesidad=false;
					$wcond="";
					$wfrec="";              //Frecuencia o periodicidad

					//Pregunto si es una condicion a NECESIDAD
					if ($row[10]!="")       //Indica que puede ser un medicamento a Necesidad
					   {
						//Traigo la descripcion de la CONDICION
						$q = " SELECT condes, conpdv "
							."   FROM ".$wbasedato."_000042 "
							."  WHERE concod = '".$row[10]."'";
						$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$row1 = mysql_fetch_array($res1);

						$wcond=$row1[0];   //Descripcion de la condicion

						//           Condición
						$wanecesidad=esANecesidad($row[10]);
						$wok=true;   //Indica que si esta validada la aplicación, osea que se puede aplicar
					   }
					//=======================================================================================================================================================================

					//Traigo la descripcion de la FRECUENCIA
					$q = " SELECT percan, peruni, Pertip "
						."   FROM ".$wbasedato."_000043 "
						."  WHERE percod = '".$row[15]."'";
					$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row1 = mysql_fetch_array($res1);

					if ($row1[0] > 1)
					   $wfrec="Cada ".$row1[0]."&nbsp;".$row1[1];
					   //."S";   //Descripcion de la FRECUENCIA
					  else
						 $wfrec="Cada ".$row1[0]."&nbsp;".$row1[1];     //Descripcion de la FRECUENCIA
					 
					if( $row1[2] == 'I' )
						$wfrec=$row1[1];     //Descripcion de la FRECUENCIA
					
					//Permite dosis variable si es del dextrometer
					$wpermiteDosisVariable = false;
					if( strtoupper( $row1['Pertip'] ) == 'I' )
						$wpermiteDosisVariable = true;
					
					$esInsulina = false;
					$esInsulina = esInsulina( $conex, $wbasedato, $row[ 1 ] );
					
					$esQuimio = false;
					$esQuimio = esQuimio( $conex, $wbasedato, $row[ 1 ] );

					$wcco1=explode("-",$wcco);


					$dosVar = consultarDosisVariable( $conex, $wbasedato, $row[1], '1050', $wcco1 );

					//===============================================================
					//Paso la hora a formato de 12 horas
					//===============================================================
					if ($whora_par_actual < 12)
						$whora_a_grabar=trim($whora_par_actual).":00 - AM";
					   else
						 if ($whora_par_actual == 12)
							$whora_a_grabar=trim($whora_par_actual).":00 - PM";
						   else
							  if ($whora_par_actual == "24")
								 $whora_a_grabar=($whora_par_actual-12).":00 - AM";
								else
								   $whora_a_grabar=($whora_par_actual-12).":00 - PM";

					//Dejo el formato a 24 horas con meridiano (AM - PM)
					$whora_a_grabar = gmdate( "H:00 - A", $whora_par_actual*3600 );
					//===============================================================

					//Traigo la descripcion de la VIA
					$q = " SELECT viades "
						."   FROM ".$wbasedato."_000040 "
						."  WHERE viacod = '".$row[11]."'";
					$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row1 = mysql_fetch_array($res1);

					$wvia=$row1[0];   //Descripcion de la condicion
					$wcodvia=$row[11];   //Código de la condicion

					//Grabo la justificacion siempre, por cada articulo
					// if (isset($wjus[$i]))    //Articulo          Justific   His    Ing    Fecha Actual    Hora ronda       Kadido (id original en kardex)
					if( isset($wjus) && in_array( $row[22], $wido ) )    //Articulo          Justific   His    Ing    Fecha Actual    Hora ronda       Kadido (id original en kardex)
					{
						$indexKey = array_keys( $wido );
						$indexKey = $indexKey[0];
						grabar_justificacion(strtoupper($row[1]), $wjus[$indexKey], $whis, $wing, $wfecha_actual, $whora_a_grabar, $row[22]);
					}





					/**************************************************************************************************
					 * Si el medicamento es superior a dosis maxima o días de tratamiento no debe aparecer
					 **************************************************************************************************/
					$noMostarPorDMTTO = false;//No mostrar por dias de tratamiento y dosis maxima

						// dosis maxima				días de tratamiento
					if( trim( $row[17] ) != '' || trim( $row[18] ) != '' ){
						
						//Dosis máxima
						//Se muestra el medicamento siempre y cuando no se halla cumplido todas las dosis máxima
						$fechorInicial 	= strtotime( $row[ 4 ]." ".valHoraStrToTime($row[ 5 ]).":00:00" );
						$fechorFinal	= strtotime( $wfecha_actual." ".valHoraStrToTime($whora_par_actual).":00:00" ) - 2*3600;
						
						$noFueAplicadoEfectiva = false;
						if( $dosisLevIC ){
							$totalAplicaciones = consultarTotalAplicacionesEfectivasEnDosisInc( $conex, $wbasedato, $whis, $wing, $row[1], $fechorInicial, $fechorFinal, $row[22] );
							
							if( $dosisOriLevIC > $totalAplicaciones ){
								$noFueAplicadoEfectiva = true;
							}
						}
						else{
							$totalAplicaciones = consultarTotalAplicacionesEfectivasInc( $conex, $wbasedato, $whis, $wing, $row[1], $fechorInicial, $fechorFinal, $row[22] );
							
							if( trim( $row[17] ) > $totalAplicaciones ){
								$noFueAplicadoEfectiva = true;
							}
						}
						
						if( $noFueAplicadoEfectiva ){
							
							if( !$wanecesidad ){
								//Si la ronda a aplicar es la ronda actual y además:
								//Si faltan aplicaciones y la ronda anterior también debe aparecer el medicamento y el medicamento fue justificado en la ronda anterior
								//Entonces el medicamento debe aparecer
								//Si no es la ronda actual se debe mostrar
								
								$horaActual = floor( date( "H" )/2 )*2;
								if( $whora_par_actual == $horaActual ){
									
									$rondaAnterior = strtotime( $wfecha_actual." ".valHoraStrToTime($whora_par_actual).":00:00" )-2*3600;
									
									$apareceEnRondaAnterior = false;
									if( $fechorInicial <= $rondaAnterior && ($rondaAnterior - $fechorInicial)%($row[6]*3600) == 0 ){
										$apareceEnRondaAnterior = true;
									}
									
									//Miro si debe aparecer en la ronda anterior
									if( $apareceEnRondaAnterior ){
										//Esto busca si fue aplicado o tiene justificacion
										$fueAplicado = buscoSiYaFueAplicado($whis, $wing, $row[1], trim($wcco1[0]), $row[7], date( "H", $rondaAnterior), date( "Y-m-d", $rondaAnterior), $wjustificacion, $row[22] );
										
										//Si NO fue aplicado y no tiene justificación debe aparecer en la ronda seleccionada
										if( $fueAplicado || $wjustificacion == '' ){
											$noMostarPorDMTTO = true;
										}
									}
								}
							}
						}
						else{
							//Solo aparece si el medicamento fue aplicado
							$fueAplicado = buscoSiYaFueAplicado($whis, $wing, $row[1], trim($wcco1[0]), $row[7], $whora_par_actual, $wfecha_actual, $wjustificacion, $row[22] );
							
							if( !$fueAplicado ){
								$noMostarPorDMTTO = true;
							}
						}
					}
					/**************************************************************************************************/


					//Explicación del IF ****************************************************************************************************************************************************
					//Entra a este if cuando : Cuando el ('arrAplicacion[$cont1]' esta setiado desde la funcion 'obtenerVectorAplicacionMedicamentos' y esa posición del arreglo
					//tiene el caracter '*' y cont1 corresponda con la hora de aplicación actual) O (cuando el articulo sea 'a necesidad' y el contador este en la hora de aplicación actual)
											  //Quito esta condicion por que el query lo valida
					if ( ( !$noMostarPorDMTTO //&& isset($arrAplicacion[(int)$whora_par_actual]) && $arrAplicacion[(int)$whora_par_actual] == $caracterMarca
							&& strtotime( $row[ 4 ]." ".valHoraStrToTime($row[ 5 ]).":00:00" ) <= strtotime( $wfecha_actual." ".valHoraStrToTime($whora_par_actual).":00:00" )	//valido que la fecha y hora de inicio sea mayor a la fecha y ronda seleccionada para aplicar
						 )
						 //or $wanecesidad
					){
					    if (is_integer($j/2))
						   $wclass="fila1";
						  else
							 $wclass="fila2";
						$j++;
						                       // Articulo      Cco
						$wStock[$i]  = esdelStock($row[1], trim($wcco1[0]));         ///// STOCK /////
						

						//Si se ingreso una nueva dosis desde el Ipod
						$cambioDosis = false;
						if( isset( $dosisIpd[$i] ) ){
							// if( !empty( $dosisIpd[$i] ) && $dosisIpd[$i] != $wdosis ){
							$indexKey = array_keys( $dosisIpd );
							$indexKey = $indexKey[0];
							
							if( !empty( $dosisIpd[$indexKey] ) && $dosisIpd[$indexKey] != $wdosis && in_array( $row[22], $wido ) ){

								//cambio la dosis
								if( cambiarDosisKardex( $conex, $wbasedato, $whis, $wing, $fechaKardex, $row[ 1 ], $row[ 4 ], $row[ 5 ].":00:00", $row[ 6 ], $row[ 18 ], $row[ 17 ], $row[ 22 ], $dosisIpd[$i], $wfecha_actual, gmdate( "H:i:s", $whora_par_actual*3600 ), $wStock[$i] ) ){
									$cambioDosis = $wdosis;					//Indica si cambio la dosis
									$wdosis = $dosisIpd[$i];
									$wcant_aplicar = $wdosis/$row[ 12 ];
									$row[7] = $wdosis;
								}
							}
					    }

						$wsuspendido="off";
						if ($row[9]=="on")   //Si esta suspendido verifico que no halla sido dentro la ronda actual
						{
							//Si es urgencias el medicamento está suspendido
							if( $esUrgencias ){
								$wsuspendido="on";
							}
							else{
															 // Hist   Ing    Articulo, hora              , Fecha actual  , kadido (id original del kardex)
								$wsuspendido=buscarSiEstaSuspendidoInc($whis, $wing, $row[1] ,  $whora_par_actual, $wfecha_actual, $row[22]);
							}
						}

						if ($wsuspendido=="off")   //No esta suspendido
						   {
							//Si el medicamento se encuentra suspendido
							if( $row[9] == "on" ){
								$wclass = 'suspendido';
							}

						    echo "<tr class=".$wclass.">";
							
							/***************************************************************************************************************************************
							 * Codigo Medicamento
							 * Junio 21 de 2016
							 ***************************************************************************************************************************************/
							echo "<td><font size=5>".$row[1];                           //Codigo Medicamento
							// Si es una insulina se le agrega una D si el articulo fue agregado en ordenes por Dextrometer o M si es por Medicamento
							if( $esInsulina )
								echo ( $wpermiteDosisVariable ) ? "<br>(Dext.)" : "<br>(Medic.)";                       		//Codigo Medicamento
							echo "</font></td>";                           				//Codigo Medicamento
							/***************************************************************************************************************************************/
							
							echo "<td><font size=6>".$row[2]."</font></td>";                           //Nombre Medicamento
							echo "<td align=right><font size=6>".round( $row[7], 3 )."  ".$row[8]."</font></td>";  //Dosis

							                     //  his    ing    articulo      cco         dosis    ronda              fecha           justificacion     kadido (id original kardex)
							if (buscoSiYaFueAplicado($whis, $wing, $row[1], trim($wcco1[0]), $row[7], $whora_par_actual, $wfecha_actual, $wjustificacion, $row[22]))
							   {
							    $waplicado="on";
							   }
							  else
								{
								 if (!isset($wgrabar) or $wgrabar != "on")
									{

									   //                                      Articulo Cco              Dosis                     Unidad de Manejo, si es del stock, noenviar
									   $wok = validar_aplicacion($whis , $wing, $row[1], trim($wcco1[0]), $row[7], $wcant_aplicar, $row[12]        , $wStock[$i]    , $row[16], $saldoArticulo, $sinRecibir );
									   
									   //Es una insulina y por seguridad se deja aplicar máximo el saldo del articulo
									   //en caso de tener saldo
									   if( true||$esInsulina && $saldoArticulo > 0 ){
											$dosVar[ 'Defcas' ] = min( $dosVar[ 'Defcas' ], $saldoArticulo*$row[12] );
											// echo "<pre>saldoArticulo: "; echo $saldoArticulo; echo "</pre>";
											// var_dump( $ok );
											if($saldoArticulo == 0 ){
												$wStock[$i] = 'off';
												$dosVar[ 'Defrci' ] = false;
											}
											
											//Es dosis variable solo si la condición lo permite
											if( !$wpermiteDosisVariable ){
												$dosVar[ 'Defrci' ] = false;
											}
										}
									   
										if( $saldoArticulo - $sinRecibir > 0 )
										{
										   if( !$dosVar[ 'Defrci' ] || ( $dosVar[ 'Defrci' ] && isset( $dosisIpd[$i] ) ) )	//Si el medicamento NO es de dosis variable, verifico la aplicacion
										   {

											 if( !$wok && $cambioDosis && $saldoArticulo > 0 )
											 {
												echo "<script>
													alert( \"Para el articulo {$row[1]}-".trim( $row[2] )." la dosis cambio de $cambioDosis {$row[8]} a $wdosis {$row[8]}.\\n".str_replace( "</b>","",str_replace( "<b>","",$wmensaje ) )."\" );
												</script>";
												$wapl[$i] = "off";
												$wok = true;
											 }
										   }
										   else
										   {
											 $wok = true;
										   }
										}
									}
								}

							echo "<td align=center><font size=5>".$wvia."</font></td>";
							echo "<td align=center><font size=5>".$wcond."</font></td>";
							echo "<td align=center><font size=5>".$wfrec."</font></td>";

							// if (isset($wapl[$i]) and $wapl[$i]=="on" and $wok==true and $waplicado=="off")
							if( isset($wapl) && in_array( "on", $wapl ) && $wok==true && $waplicado == "off" && in_array( $row[22], $wido ) )
							   {
								// if (!isset($wanular) or $wanular != "on")       //Grabar o Aplicar
								if (!isset($wanular) or !in_array( "on", $wanular ) )       //Grabar o Aplicar
								   {
								    //Marzo 17 de 2011
								    echo "<script language='Javascript'> $.blockUI({ menssage: 'Un momento por Favor'}) </script>";
																								  //Cod.Articulo kadido
									// $wexiste=buscarsiExiste($wfecha, $whora_a_grabar, $whis, $wing, $row[1],     $row[22]);
									$wexiste=buscarsiExiste($wfecha_actual, $whora_a_grabar, $whis, $wing, $row[1],     $row[22]);

									if ($wexiste == false)   //Esto se hace para controlar la duplicidad de registros
									   {
										   
									    //Verifico que no este desconectada la SESSION
										if (isset($wusuario) and trim($wusuario)!="")
										   {											   
											//Agosto 14 de 2012.	Siempre se deja pasar para que descuetne de los saldos de ser necesario.
											if ( true || $wStock[$i]=="off" )     //Si entra aca es porque NO es del STOCK, puede que se envie o no pero si tiene saldo lo descuenta
											  {
													
												  //Funcion en movhos.inc.php, utilizada tambien desde gestión de enfermeria para la aplicacion de medicamentos asociados a un procedimiento
												  EntregarNutricion($wbasedato,$whis,$wing,$row[1],$wcant_aplicar,$wdosis,$whora_a_grabar,$row[2],$wcco,$wcco1[0],$wusuario, $wrecibe, $wfecha_actual,$wnoenviar[$i],$wufr,$row[22],$wcodvia);
												  $url = devolver($url, $wemp_pmla, $wfecha_actual,  $whora_par_actual, $whis, $wing, $wcco, $whab, $wpac, $wzona);
												  header("Location: ". $url );
											  }
										   }
										else
										   {
											?>
											 <script>
											   alert("3. Debe cerrar esta ventana, Actualizar la pantalla (simbolo de flecha circular)  y volver a ingresar a la opción ** Aplicacion Ipods **");
											 </script>
											<?php
										   }
									   }
									   $waplicado="on";  //Esto se porque si wexiste==on es porque ya esta aplicado
										
									echo "<td align=center rowspan=4 bgcolor='00FF00'><font size=6>Entregado</font></td>";
									$noPuedeAnular = consultarSiPuedeAnular( $whis, $wing, $row[1], $row[22]);
									if(!$noPuedeAnular)
									{
										echo "<td align=center rowspan=4><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wanular[".$i."]=on&wapl[".$i."]=off&wart[".$i."]=".$row[1]."&wdosis[".$i."]=".$row[7]."&wcanfra[".$i."]=".$row[12]."&wnoenviar[".$i."]=".$wnoenviar[$i]."&wStock[".$i."]=".$wStock[$i]."&wido[".$i."]=".$row[22]."&wzona=".$wzona.( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."' class=tipo3V>Anular</A></td>";
									}
									

									//Marzo 17 de 2011
									echo "<script>$.unblockUI()</script>";
								   }
								  else
									{
									   if( !$dosVar[ 'Defrci' ] ) //si NO pide cantidad al aplicar
									     {
									       echo "<td align=center rowspan=4 colspan=2><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on&wzona=".$wzona.( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )." ' class=tipo3V>Entregar</A></td>";
										 }
									   else
										 {
										    echo "<td align=center rowspan=4 colspan=2>555";
											echo "<select class=tipo3V name='dosisIpd[$i]' onChange='cambiarUrl( this, \"Entrega_ipods.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco.( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on\", $i )'>";
											echo "<option></option>";

											for( $inc = $dosVar[ 'Defcai' ]; $inc <= $dosVar[ 'Defcas' ]; $inc += $dosVar[ 'Defesc' ] )
											 {
											   echo "<option>$inc</option>";
											 }

											echo "</select>";
											echo "</td>";
										 }
									}
							   }
							  else
								 {
								  if ($wok == true and $waplicado=="off")
									 {
									  if ($wStock[$i]=="on")
										if( !$dosVar[ 'Defrci' ] ) //si NO pide cantidad al aplicar
										    echo "<td align=center rowspan=4 colspan=2><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on&wzona=".$wzona.( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )." ' class=tipo3V>Entregar (Stock)</A></td>";
										 else
										    {
										      echo "<td align=center rowspan=4 colspan=2><font class=tipo4V>(Stock)<br></font>";
											  echo "<select class=tipo3V name='dosisIpd[$i]' onChange='cambiarUrl( this, \"Entrega_ipods.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco.( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on\", $i )'>";
											  echo "<option></option>";

											  for( $inc = $dosVar[ 'Defcai' ]; $inc <= $dosVar[ 'Defcas' ]; $inc += $dosVar[ 'Defesc' ] )
											   {
												 echo "<option>$inc</option>";
											   }

											  echo "</select>";
											  echo "</td>";
											}
										else
										   if( !$dosVar[ 'Defrci' ] ) //si NO pide cantidad al aplicar
										      echo "<td align=center rowspan=4 colspan=2><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on&wzona=".$wzona.( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."&firma2=on' class=tipo3V>Entregar</A></td>";
										   else
										    {
										      echo "<td align=center rowspan=4 colspan=2>";
											  echo "<select class=tipo3V name='dosisIpd[$i]' onChange='cambiarUrl( this, \"Entrega_ipods.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' ).$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on\", $i )'>";
											  echo "<option></option>";

											  for( $inc = $dosVar[ 'Defcai' ]; $inc <= $dosVar[ 'Defcas' ]; $inc += $dosVar[ 'Defesc' ] )
											   {
												 echo "<option>$inc</option>";
											   }

											  echo "</select>";
											  echo "</td>";
											}
									 }
									else
									   if ($waplicado != "on")
										  {
										   echo "<td align=center rowspan=4 bgcolor='00FF00' colspan=2><font size=4>".$wmensaje."</font></td>";
										  }
										 else
										   {
										    echo "<td align=center rowspan=4 bgcolor='00FF00'><font size=6>Entregado</font></td>";
											$noPuedeAnular = consultarSiPuedeAnular( $whis, $wing, $row[1], $row[22]);
											if(!$noPuedeAnular)
											{
												echo "<td align=center rowspan=4><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wanular[".$i."]=on&wapl[".$i."]=off&wart[".$i."]=".$row[1]."&wdosis[".$i."]=".$row[7]."&wcanfra[".$i."]=".$row[12]."&wnoenviar[".$i."]=".$wnoenviar[$i]."&wStock[".$i."]=".$wStock[$i]."&wido[".$i."]=".$row[22]."&wzona=".$wzona.( !empty( $pac_historia ) ? '&pac_historia='.$pac_historia : '' ).( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."' class=tipo3V>Anular</A></td>";
											}
											// echo "<td align=center rowspan=4><A HREF='Entrega_ipods.ph?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wanular[".$i."]=on&wapl[".$i."]=off&wart[".$i."]=".$row[1]."&wdosis[".$i."]=".$row[7]."&wcanfra[".$i."]=".$row[12]."&wnoenviar[".$i."]=".$wnoenviar[$i]."&wStock[".$i."]=".$wStock[$i]."&wido[".$i."]=".$row[22]."&wzona=".$wzona."' class=tipo3V>Anular</A></td>";
										   }
								 }

							echo "</tr>";

							echo "<tr class=".$wclass."><td colspan=6 bgcolor='FFFF99' obs><font size=5><b>Observaciones: </b>".$row[13]."</font></td></tr>";         //Observaciones
							// echo "<tr class=".$wclass."><td colspan=6 bgcolor='FFCC66'><font size=5><b>Alertas      : </b>".$row[14]."</font></td></tr>";         //Alertas

												                                                                           //         Artciulo kadido (id original kardex)
							justificacionNoAplicacion($i, $wjustificacion, $waplicado, $whis, $wing, $wfecha_actual, $whora_a_grabar, $row[1], $row[22]);
							echo "<tr><td colspan=4>&nbsp;</td></tr>";
							echo "<tr><td colspan=4>&nbsp;</td></tr>";
						   }
					   }
				}
				echo "</table>";

				echo "<br><br>";
				echo "<table>";
				if( empty($pac_historia) )
					echo "<tr><td><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&wzona=".$wzona.( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."' class=tipo3V>Retornar</A></td></tr>";
				else
					echo "<tr><td><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla.( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."&consultaPorHistoria=on' class=tipo3V>Retornar</A></td></tr>";
				echo "</table>";
			   }
			  else
				 {
				  //Mayo 26 de 2011
				  if (CcoTieneZonas())
				     {
					  if (!isset($wzona) or trim($wzona) == "")
					     {
						  $wzona=strtoupper(SeleccionarZona());
					      echo "<br><br><br><br>";
						 }
						else
						   elegir_historia($wzona);
					 }
					else                             //Termina modificacion de Mayo 26 de 2011
				       elegir_historia("");

				  echo "<br><br>";
				  echo "<table>";
				  if(empty($pac_historia))
					echo "<tr><td><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&wzona=".$wzona.( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."' class=tipo3V>Retornar</A></td></tr>";
				  else	
					echo "<tr><td><A HREF='Entrega_ipods.php?wemp_pmla=".$wemp_pmla.( !empty( $servicioDomiciliario ) ? '&servicioDomiciliario='.$servicioDomiciliario : '' )."&consultaPorHistoria=on' class=tipo3V>Retornar</A></td></tr>";
				
				  echo "</table>";
				 }
		 }
		else
           {
            ?>
			  <script>
			    alert("¡ATENCION!. Debe cerrar esta ventana, Luego actualizar la pantalla (presionando el simbolo de flecha circular arriba)  y volver a ingresar a la opción ** Aplicacion Ipods **");
			  </script>
		    <?php
           }
       }

} // if de register

?>
<script type="text/javascript">
var dataUsuario = <?= json_encode( $dataUsuario ) ?>;
</script>