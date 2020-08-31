<?php
include_once("conex.php");


function consultarLogoEmpresarial( $conex, $codigo ){

	$val = "";

	$q = " SELECT Empilz
			 FROM root_000050
			WHERE Empcod = '".$codigo."'
			  AND empest = 'on'";

	$res = mysql_query( $q, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error() );
	$num = mysql_num_rows( $res );

	if ($num > 0)
	{
		$rows = mysql_fetch_array( $res );
		$val = $rows[ 'Empilz' ];
	}

	return $val;
}

if( $accion ){

	$consultaAjax = '';






	switch( $accion ){

		case 'imprimirSticker':

			include_once("root/comun.php");

			$wclisur = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion" );
			$wmovhos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );


			//Consulto los pacientes hospitalizados
			$sql = "SELECT Pachis, Oriing, Pachis, Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pacdir, Pacbar, Bardes, Ingsei, Cconom, Ingcem, Ingent, Pactel, Seldes
					  FROM ".$wclisur."_000100
				INNER JOIN root_000037
						ON pachis = orihis
				 LEFT JOIN ".$wclisur."_000101
						ON inghis = orihis
					   AND ingnin = oriing
				 LEFT JOIN root_000034
						ON barcod = pacbar
					   AND barmun = paciu
				 LEFT JOIN ".$wmovhos."_000011
						ON ccocod = ingsei
				 LEFT JOIN ".$wclisur."_000105
						ON selcod = Pactam
					   AND seltip = '14'
					 WHERE pacact = 'on'
					   AND oriori = '".$wemp_pmla."'
					   AND pachis = '".$historia."'
					   AND ingnin = '".$ingreso."'
					 ";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );

			if( $num > 0 ){

				while( $rows = mysql_fetch_array( $res ) ){

					$nombreCompleto = $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ];
					$identificacion = $rows[ 'Pactdo' ]." ".$rows[ 'Pacdoc' ];
					$historia 		= $rows[ 'Pachis' ];
					$ingreso		= $rows[ 'Oriing' ];
					$direccion		= $rows[ 'Pacdir' ];
					$barrio			= $rows[ 'Bardes' ];
					$telefono		= $rows[ 'Pactel' ];
					$entidad		= $rows[ 'Ingcem' ]."-".$rows[ 'Ingent' ];
					$servicio		= $rows[ 'Cconom' ];
					$tipoServicio	= $rows[ 'Seldes' ];
				}
			}

			$logo 	= consultarLogoEmpresarial( $conex, $wemp_pmla );

			$impresionZPL = "^XA

							^FO200,50
							".$logo."

							^FX INFORMACION DEL PACIENTE
							^CFQ
							^FO0,250^FDNOMBRE DEL PACIENTE^FS
							^CFR
							^FO0,290^FD".$nombreCompleto."^FS

							^CFQ
							^FO0,350^FDCEDULA^FS
							^FO350,350^FDHISTORIA^FS

							^CFR
							^FO0,390^FD".$identificacion."^FS
							^FO350,390^FD".$historia."-".$ingreso."^FS

							^CFQ
							^FO0,450^FDDIRECCION^FS
							^CFR
							^FO0,490^FD".substr( $direccion, 0, 43 )."^FS
							^FO0,530^FD".substr( $direccion, 43 )."^FS

							^CFQ
							^FO0,590^FDBARRIO^FS
							^CFR
							^FO0,630^FD".substr( $barrio, 0, 43 )."^FS

							^CFQ
							^FO0,690^FDTELEFONO^FS
							^CFR
							^FO0,730^FD".$telefono."^FS

							^CFQ
							^FO0,790^FDENTIDAD^FS
							^CFR
							^FO0,830^FD".substr( $entidad, 0, 43 )."^FS
							^FO0,870^FD".substr( $entidad, 43 )."^FS

							^CFQ
							^FO0,930^FDSERVICIO^FS
							^CFR
							^FO0,970^FD".$servicio."^FS

							^CFQ
							^FO0,1030^FDTIPO DE SERVICIO^FS
							^CFR
							^FO0,1070^FD".$tipoServicio."^FS

							^XZ";

// echo trim( $impresionZPL );
// exit();
			// $wip 	= "132.1.24.125";
			$wip 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "ipImpresoraSticker" );

			$data = array();

			$addr	= $wip;
			@$fp 	= fsockopen( $addr, 9100, $errno, $errstr, 30);
			if( !$fp ){
				$data[ 'error' ] = 1;
				$data[ 'msg' ]	 = utf8_encode( "Error al imprimir sticker: ".$errstr." ($errno)" );
			}
			else
			{
				fputs($fp,$impresionZPL);
				$data[ 'error' ] = 0;
				$data[ 'msg' ]	 = "Sticker impreso";
				fclose($fp);
			}
			sleep(5);

			echo json_encode( $data );

		break;

		default: break;
	}

	exit();
}

?>
<head>
  <title>CARGOS IPS</title>
  <script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
</head>
<body onload=ira()>
<script type="text/javascript">


	function abrirVentana( url ){

		$.get( url,
			{},
			function(data){
				alert( data.msg );
			},
			"json",
		);

		// window.open( url, '','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0' );
	}

	function enter()
	{
		document.forms.cargos.submit();
	}

	function MostrarPaquetes(wcodpaq,wbasedato)
   	{
	   	opciones = " width=700, ";
		opciones = opciones+" height=300,";
		//var objBoton= document.getElementById('botBuscarPac');
	    opciones = opciones+" top=150,";
		opciones = opciones+" left=200,";
		opciones = opciones+" status=Yes,";
		opciones = opciones+" menubar=No,";
		opciones = opciones+" resizable=yes,";
		opciones = opciones+" scrollbars=yes,";
		opciones = opciones+" alwaysRaised";

		document.cargos.method='POST';
		document.cargos.action="GetPaquetes.php?wcodpaq="+wcodpaq+"&wbasedato="+wbasedato;

		var winPaquetes=window.open('', 'Paquetes', opciones);
		document.cargos.target="Paquetes";
		document.cargos.submit();
   	}

   	function cerrarVentana()
	 {
      top.close()
     }

</script>


<?php
  /************************************************
   *     PROGRAMA PARA LA GRABACION DE CARGOS     *
   *           DE PACIENTES CLINICA               *
   ************************************************/

//===================================================================================================================================
//PROGRAMA                   : cargos_ips.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Abril 4 de 2005
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="2020-01-09";
//DESCRIPCION
//====================================================================================================================================\\
//Este programa se hace con el objetivo de registrar los cargos de los pacientes de la clinica del sur, basandose en el ingreso por   \\
//admisiones, el programa para la grabacion del cargo se basa en el responsable e ingreso de la historia clinica, teniendo en cuenta  \\
//las tarifas de dicha empresa, además debe permitir grabar desde diferentes unidades de la clinica y los siguientes tipos de cargos: \\
//Cargos de pacientes procedimientos, consultas, cirugia, ayudas diagnosticas, material medico quirurgico, medicamentos, honorarios,  \\
//anestesiologia, etc.                                                                                                                \\
//====================================================================================================================================\\

//========================================================================================================================================\\
//========================================================================================================================================\\
// ACTUALIZACIONES
//========================================================================================================================================\\
//2020-01-09 Camilo zapata: se agrega condicion para garantizar que no se hagan movimientos de venta para bodegas que requieren traslado automático si no hay saldo suficiente en estas y que el traslado automático realmente se haya realizado, por lo tanto para cualquier caso que no implique este comportamiento (aprovechamientos, devoluciones o ventas directas) se permite continuar normalmente.
// 2018-05-28 Edwin MG
// Al seleccionar un paciente se puede imprimir el sticker correspondiente
//========================================================================================================================================\\
// 2016-07-13 Jonatan
// Si existe la variable $wsucursal en la url, utilizara ese valor como centro de costos de traslado automatico, sino utilizara el valor por defecto
// de la root_000051 para TrasladoAutomaticoenCargo (1270).
//========================================================================================================================================\\
// 2016-04-04 camilo
// para pacientes particulares, si el código del responsable es igual al valor de la variable codigoempresaparticular en la 51 el nombre del responsable que se presenta en el
// encabezado se cambia por: "PARTICULAR"
// // 2015-05-27 camilo
// Se modificó la función "restringirModificacion" de tal manera que restrinja tambien las facturas radicadas aunque el ingreso siga activo siempre
// y cuando exista el parámetro en la 51 que indique que se debe realizar esa validación.
  //========================================================================================================================================\\
// 2015-03-27 camilo
// Se modificó la función "restringirModificacion" de tal manera que utiliza el mismo principio de evitar cargos a facturas radicadas solo que
// se pueden habilitar estas por medio del registro en la tabla 233
  //========================================================================================================================================\\
// 2015-03-25 camilo
// Se modificó la función "restringirModificacion" de tal manera que no utiliza la tabla 233 para mirar si hay historias inactivas, habilitadas
// sino que, se verifica si alguna factura asociada al paciente ya está radicada, en caso de ser así, entonecs no permitirá editar los cargos.
//============================================================================================================================================
// Octubre 10 de 2014 Camilo
// Se agregó la función "restringirModificacion" la cual solo le va a permitir agregar a cargos a las hitorias inactivas que estén previamente
// autorizadas en la tabla 000233, y que ademas pertenezcan a empresas que tengan esta función autorizada, por medio del parámetro
// "permiteCargosAinactivos" en la tabla root_000051
//========================================================================================================================================\\
// Marzo 19 de 2014 Jonatan
// Se agregan dos validaciones, una de ellas es la relacion de codigo del concepto con el nombre que se encuentra en la interfaz, la segunda es
// la relacion de codigo de articulo con el nombre de articulo que esta en la interfaz, si en agun caso no es correcta la relacion, no permitira
// la grabacion del cargo.
//========================================================================================================================================\\
// Febrero 10 de 2014
// Se agrega onchange='enter()' en 2 input text donde se ingresa el concepto (NAME='wcodcon'), que no lo tenian para que asi al cambiar el
// codigo del concepto valide nuevamente la informacion ya cargada.
// Jerson Trujillo.
//========================================================================================================================================\\//========================================================================================================================================\\
// Enero 09 de 2014
// Se agrega onchange='enter()' para que al cambiar de articulo consulte de nuevo la informacion asociada al articulo. (Alrededor de la linea 3214)
//========================================================================================================================================\\
// Octubre 21 de 2013 Jonatan
// Se agrega validacion para que cuando un auxiliar que maneje bodega cargue articulos por aprovechamiento, siempre use el cco de
// medicina domiciliaria para que el registro de la tabla clisur_000106 quede con este cco.
//=========================================================================================================================================
// Septiembre 05 de 2013 Jonatan
// Se configura la seccion cuando el que esta haciendo los cargos sea una bodega para que lo que mueva salga de su cco y lo traslade a
// medicina domiciliaria y de medicina domiciliaria venda, para las devoluciones se devuelve del cco de med. domiciliaria asi mismo y
// luego de medicina domiciliaria al cco del usuario que maneja bodega.
//==========================================================================================================================================
//Junio 24 de 2013 Jonatan
//Se hace la validacion en caso de que el concepto mueva inventario, obligar a que se seleccione el cco si el usuario que ingresa maneja bodega.
//=========================================================================================================================================\\
//Junio 21 de 2013 Jonatan
//Se agrega en la informacion del paciente (arriba a la derecha), el tipo de servicio que posee, si tiene medicina domiciliaria.
//=========================================================================================================================================\\
//Camilo Zapata: Mayo 9 2013, se agregó un fragmento de código que no permitirá la grabación cuando la sesion haya expirado.
//verificando el valor $_SESSION['user']
//Diciembre 17 de 2012
//Se corrije el la multiplicacion en el costos del centro de costos origen al hacer el registro en la tabla 11 (Linea 1952)
//Octubre 19 de 2012
//Se cambia la variable del centro de costos de donde se traslada el producto para que se registre con el costo de ese centro de costos (1070)
//antes trabajaba con el 1270, en la funcion de trasladoautomatico.
//Se agrega el recalculo del costo promedio ponderado del producto cuando se da un el traslado del producto y una devolucion.
//Se modifica el script para que permita trasladar inventario del centro de costos de bodega hacia un usuario de bodega que se encuentra
//logueado en el sistema, este usuario tiene el campo Cjebod en on en la tabla 30 de clisur.
//=========================================================================================================================================\\
// Marzo 21 de 2012 - Julio 16 de 2012 (Se reinserta esta actualizacion, ya que no quedo en lo ultimo que se sbio antes de esta fecha)
// Se agrega la validacion para que solo deje grabar conceptos de inventario cuando la variable Cjebod en la tabla clisur_000030 se
// encuentre en on para el usuario y solamente para ese centro de costos
// y ademas si el concepto maneja bodega. (Esta linea fue agregada el dia 24 de junio de 2013)
//
//____________________________________________________________________________________________
//========================================================================================================================================\\
// Marzo 1 de 2012
// Se agrega una validacion en la funcion de trasladoautomatico en cuanto a las devoluciones, si las existencias son iguales a cero
// y $wdevol == 'on', se detiene la funcion.
//
//________________________________________________________________________________________________________________________________________\\                                                                                                                         \\
//========================================================================================================================================\\
// Febrero 23 de 2012
// Se modifica en la funcion de trasladoautomatico el consecutivo del concepto 002.
//________________________________________________________________________________________________________________________________________\\
//========================================================================================================================================\\
// Febrero 14 de 2012
// Se agrega a la funcion trasladoautomatico el procedimiento que permite la devolucion de un producto para el centro de costos configurado
// en la variable $wccotrasladar.
//________________________________________________________________________________________________________________________________________\\
// Febrero 8 de 2012                                                                                                                                       \\
// Se agrega la funcion trasladoautomatico la cual permite modificar el stock de productos del Kardex hacia el centro de costos necesario,
// el procedimiento es el siguiente:
// Con la funcion consultarAliasPorAplicacion se traen los siguientes datos de la tabla root_000051
//		$wccotrasladar = consultarAliasPorAplicacion($conex, $wemp_pmla, 'TrasladoAutomaticoenCargo');
//		$wbodega = consultarAliasPorAplicacion($conex, $wemp_pmla, 'BodegaPrincipal');
//		$wconceptraslado = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ConceptodeTraslado');
// Cada declaracion de variables permite configurar a quien se le transladará el stock desde la bodega($wbodega) hacia el centro de costos
// a transladar($wccotrasladar), permitiendo que los productos que se encuentren en cero para el centro de costos ($wccotrasladar)
// puedan tener stock automaticamente.
//________________________________________________________________________________________________________________________________________\\
// SEPTIEMBRE 8 DE 2011:		                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
// Se modifica la función 'mostrar' para que tenga en cuenta cuando un recibo se le han hecho cuadres no lo deje anular. También se 	  \\
// adicionó un query de borrado cuando se anula un recibo para que borre lo que haya pendiente en la tabla 000037 - Mario Cadavid	  	  \\
//________________________________________________________________________________________________________________________________________\\
// AGOSTO 23 DE 2011:		                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
// Se modifica la función 'mostrar' para que tenga en cuenta los permisos en la tabla 000081, donde se define si el usuario actual puede  \\
// anular y/o imprimir el documento. También se modificó el diseño adaptándo a los estilos del css actual de Matrix - Mario Cadavid		  \\
//________________________________________________________________________________________________________________________________________\\
//JULIO 13 DE 2010:		                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se valida que el codigo de paquete exista antes de poder grabar, para ello se modifica la funcion validacion_datos_a_grabar()			  \\
//________________________________________________________________________________________________________________________________________\\
//JULIO 13 DE 2010:		                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se valida que el codigo de paquete exista antes de poder grabar, para ello se modifica la funcion validacion_datos_a_grabar()			  \\
//________________________________________________________________________________________________________________________________________\\
//SEPTIEMBRE 17 DE 2009:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa porque al ser campo pacnin tipo caracter esta ordenando solo el 1er numero y esto generaba que siempre mostrara \\
//hasta el 9 ingreso como el ultimo ingreso activo cuando la historia tenia mas, entonces se modifico el query para que le orden lo tomara\\
//numericamente.                                                                                                                          \\
//Se cambio tambien, para que muestre todos los cargos grabados a la historia, sin importar el usuario que la esta consultando, pero se   \\
//mantuvo la validacion que solo deje anular los cargos que son grabados por el usuario que esta consultando o por un usuario tipo :      \\
//'administrativo' el cual se define en la tabla 000030                                                                                   \\
//Se modifica tambien para que muestre un mensaje indicando que tiene cargos pendientes de un ingreso anterior. Se crea la funcion        \\
//'Cargos_anteriores()'                                                                                                                   \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//MAYO 11 DE 2009:                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica para que cuando se anule un cargo y este pertenezca a SOE afecte o modifique la cantidad facturada en la tabla de presupu-  \\
//esto de SOE (_000131) y asi se mantiene la integridad de la información.                                                                \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//FEBRERO 18 DE 2009:                                                                                                                     \\
//________________________________________________________________________________________________________________________________________\\
//Se le adiciona al programa la posibilidad de poder hacer devoluciones en conceptos de inventarios, para esto se puso en la pantalla un  \\
//indicador de 'Devolucion' que indica cuando este chuliado, que se va a realizar una devolucion al inventario de un medicamento o de un  \\
//dispositivo medico, el programa valida tanto para devoluciones que mueven inventario, como para devoluciones de aprovechamientos, pero  \\
//eso si en ambos casos el concepto debe ser de inventarios, Se crea el campo 'tcardev' en la tabla _000106.                              \\
//Las devoluciones generan un documento o movimiento en los inventarios de entrada, este movimiento se valora o costea al valor (costo)que\\
//se tenga en ese momento en el kardex. Cuando se factura se muestran tanto los cargos (facturacion) como las devoluciones, pero al       \\
//generar la factura se agrupan por concepto.                                                                                             \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//________________________________________________________________________________________________________________________________________\\
//JULIO 14 DE 2008:                                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
//Se actualiza el programa para que cuando se graben articulos que mueven inventarios y son definidos como aprovechamiento, solo se valide\\
//la existencia si no se define en la grabacion como aprovechamiento.                                                                     \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//JUNIO 5 DE 2008:                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//Se actualiza el programa para que se pueden ingresar codigo porpios de la clinica o codigos del proveedor basados en la hologacion de la\\
//tabla 000009.                                                                                                                           \\
//________________________________________________________________________________________________________________________________________\\
//SEPTIEMBRE 5 DE 2007:                                                                                                                   \\
//________________________________________________________________________________________________________________________________________\\
//Se creo la tabla 000083 en donde se crean las diferentes clasificaciones de los articulos y los conceptos de facturacion, para que se   \\
//pueda facturar por cualquier concepto los articulos que mueven inventarios, en donde la clasificacion del articulo debe coincidir con la\\
//clasificacion del concepto.                                                                                                             \\
//Tambien se creo la opción para que se pueda grabar o facturar cargos de inventarios por aprovechamiento, es decir, que se facture pero  \\
//que no mueva inventarios y a la vez que se pueda anular el cargo y tampoco mueva inventarios, si fue grabado como aprovechamiento. Para \\
//esto se creo un campo nuevo (Tcarapr) en la tabla 000106, en donde se graba 'on' si es aprovechamiento y 'off' si no lo es; este        \\
//aprovechamiento lo determina el usuario que graba el cargo indicandolo en un 'checkbox' llamado aprovechamiento y solo se activa si en  \\
//el maestro de Articulos indica que el articulo se puede grabar como aprovechamiento.                                                    \\
//________________________________________________________________________________________________________________________________________\\
//SEPTIEMBRE 4 DE 2007:                                                                                                                   \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica la validación al momento de grabar el cargo, se creo la funcion verificar_datos_a_grabar, en esta función se valida cada uno\\
//de los datos a ingresar, porque esta ocurriendo que se digitaba el dato y quedaba validado pero antes de dar click en grabar, modifican \\
//alguno de los datos y quedaba grabado con ese cambio, entonces con esta función se hace una doble validación.                           \\
//                                                                                                                                        \\                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//J U L I O  1 8  DE 2007:                                                                                                                \\
//________________________________________________________________________________________________________________________________________\\
//Se corrige el programa por que solo estaba teniendo las tarifas de los procedimientos (en el caso de UVR) cuando era por rango de UVR,  \\
//es decir cuando el rango de UVR era 0 y 0 no lo tenia en cuenta.                                                                        \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//J U L I O  1 8  DE 2007:                                                                                                                \\
//________________________________________________________________________________________________________________________________________\\
//Se corrige el programa por que solo estaba teniendo las tarifas de los procedimientos (en el caso de UVR) cuando era por rango de UVR,  \\
//es decir cuando el rango de UVR era 0 y 0 no lo tenia en cuenta.                                                                        \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//F E B R E R O  1  DE 2007:                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que permita modificar la fecha de grabación de los cargos, pero teniendo los siguientes cuidados:          \\
// 1) La fecha del cargo en la tabla 106 es la digita el usuario, pero la fecha del campo fecha_data es la real                           \\
// 2) La fecha para los movimientos de inventario sigue siendo la real, es decir para las tabla 10 y 11                                   \\
// 3) La fecha para los movimeintos de recibos de caja o abonos sigue siendo la real. en la tablas 20, 21 y 22                            \\
// 4) La fecha para la grabación de la auditoria sigue siendo la real. tabla 107                                                          \\
//                                                                                                                                        \\
// Se hace un control para que la fecha de grabación no pueda ser superior a la fecha actual o real.                                      \\
// Esta cambio se hace para poder realizar atrasos en la facturación y poder hacer refacturaciones cuando hay cambios de tarifas.         \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//E N E R O  25 DE 2007:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que cuando se inserte el cargo en la tabla 000106, se tome el id de esta con la funcion mysql_insert_id(), \\
//y asi poder asegurar la relación de esta tabla con la 000021 cuando son cargos de abono y tambien se agiliza la grabación del cargo     \\
//porque se evita la busqueda del ultimo id por medio de un query.                                                                        \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//E N E R O  23 DE 2007:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa que cuando la opción de colocar centro de costo sea uno solo lo coloque por defecto, sin que el usuario tenga   \\
//la necesidad de seleccionarlo.                                                                                                          \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//E N E R O  18 DE 2007:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica la forma de grabar cargos cuando la liquidación es por UVR, debido a que esto se dividio en dos formas, por Rangos de UVR y \\
//la forma tradicional de facturar por UVR que es dandole un valor a cada punto.                                                          \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//D I C I E M B R E  12 DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//Se corrige la forma de calcular el valor del procedimiento cuando se liquida por UVR, esta recalculando cada se daba OK.                \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//N O V I E M B R E  10 DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se pueda seleccionar el centro de costo para cada cargo que se grabe, validando que cuando sea un      \\
//concepto de inventarios valide la cantidad existente y cuando sea de abonos se graba el cargo con el centro de costos que tiene         \\
//asignado el usuario, además se modifica que cuando el concepto sea de abonos no permita digitar cantidad en el campo cantidad.          \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  18 y 20 DE 2006:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que valide por cada cargo que la historia e ingreso exista, debido a que al grabar el cargo estaban        \\
//digitando el numero de historia seguido de un guion y el numero de ingreso. Pero quedaba grabado asi, y luego no se encontraba el numero\\
//de historia que tenia la factura.                                                                                                       \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  17 DE 2006:                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para cuando se vaya a generar un cargo de abono se valide que el centro de costo si tiene una fuente y un       \\
//consecutivo valido para los recibos de caja.                                                                                            \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  11 DE 2006:                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
//Se actualiza el programa para que se pueden hacer abonos por refacturación sin que se generen recibos de caja o sea que no se afecta    \\
//la caja, además se controló que no deje grabar cargos sin número de historia.                                                           \\
//                                                                                                                                        \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\


include_once("root/comun.php");

session_start();
if( !isset($_SESSION['user']) )
{
	echo ' <br /><br /><br /><br />
	<div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
	[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
	</div>';
	return;
}
// Validación de usuario
if (!isset($user))
{
	if (!isset($_SESSION['user']))
	{
		session_register("user");
	}
}

//Codigo de usuario que ingreso al sistema
if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));

$usuario = new Usuario();

$usuario->codigo = $wuser;

//Valida codigo de usuario en sesion si no esta registrado en el sistema termina la ejecucion
if (!isset($_SESSION['user']))
{
	die('usuario no autenticado.  Por favor cierre esta ventana y vuelva a ingresar a Matrix.');
}
else
{
$seguridad = $usuario->codigo;
$conex = obtenerConexionBD("matrix");

  session_register("wpagook");
  session_register("wprestamo");




  $query =   "   SELECT Empcod, Empbda
                   FROM root_000050
                  WHERE Empcod = '$wemp_pmla'";
  $res = mysql_query($query, $conex) or die(mysql_error() . " - Error en el query: $query - " . mysql_error());
  $row = mysql_fetch_array($res);
  $wbasedato = $row['Empbda'];

  //$conexunix = odbc_pconnect('facturacion','infadm','1201')
  //					    or die("No se ralizo Conexion con el Unix");

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  //$wactualiz="(Versión Abril 15 de 2006)";                     // Aca se coloca la ultima fecha de actualizacion de este programa \\
                                                            // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= \\

  $wfecha=date("Y-m-d");
  $hora = (string)date("H:i:s");

  echo "<form name='cargos' action='' method=post>";

  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wccotrasladar' value='".$wsucursal."'>";

  //ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
  $wbasedato = strtolower($wbasedato);
  $q =  " SELECT Cjecco, Cjecaj, Cjetin, cjetem, cjeadm, cjebod  "
       ."   FROM ".$wbasedato."_000030 "
       ."  WHERE Cjeusu = '".$wusuario."'"
       ."    AND Cjeest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  if ($num > 0)
     {
      $row = mysql_fetch_array($res);

      $pos = strpos($row[0],"-");
      $wcco = substr($row[0],0,$pos);
      $wnomcco = substr($row[0],$pos+1,strlen($row[0]));
      $wbod = $row['cjebod'];
      $pos = strpos($row[1],"-");
      $wcaja = substr($row[1],0,$pos);
      $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));

      $wcajadm=$row[4];

      $wtiping = $row[2];
	  global $wbod;
      if (!isset($wtipcli)) $wtipcli = $row[3];
     }
    else
       echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR";

  $wcol=6;  //Numero de columnas que se tienen o se muestran en pantalla

  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  function bisiesto($year)
	{
     return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
	}


  function validar_fecha($dato)
	{
     $fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	 if(ereg($fecha,$dato,$occur))
	   {
	    if($occur[2] < 0 or $occur[2] > 12)
	      return false;
	    if(($occur[3] < 0   or  $occur[3] > 31) or
	       ($occur[2] == 4  and $occur[3] > 30) or
	       ($occur[2] == 6  and $occur[3] > 30) or
		   ($occur[2] == 9  and $occur[3] > 30) or
		   ($occur[2] == 11 and $occur[3] > 30) or
		   ($occur[2] == 2  and $occur[3] > 29 and bisiesto($occur[1])) or
		   ($occur[2] == 2  and $occur[3] > 28 and !bisiesto($occur[1])))
		    return false;
		 return true;
	   }
	  else
	     return false;
	}
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

  function consultarAliasAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){
	 $q = "   SELECT Detval
				FROM root_000051
			   WHERE Detemp = '".$codigoInstitucion."'
				 AND Detapl = '".$nombreAplicacion."'";
	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} else {
		return false;
	}
	return $alias;
}



  //FUNCION QUE PERMITE EL TRASLADO AUTOMATICO DE MEDICAMENTOS 8 Febrero 2012 /Jonatan López
  function trasladoautomatico(&$wexiste, $wdevol, $wbodega, $wconceptraslado, $wccogra, $wvaltar, $wno2, $waprovecha, $wconinv, $wpaquete, $wprocod, $wfecha, $hora, $wconmvto, $wcantidad, $wusuario, $whistoria, $wing )
    {

	  global $conex;
	  global $wbasedato;
	  global $wid;
	  global $whistoria;
      global $wing;
      global $wno1;
      global $wno2;
      global $wap1;
      global $wap2;
      global $wdoc;
      global $wcodemp;
      global $wnomemp;
      global $wfecing;
      global $wser;
      global $wcodcon;
      global $wnomcon;
      global $wprocod;
      global $wpronom;
      global $wcodter;
      global $wnomter;
      global $wporter;
      global $wcantidad;
      global $wvaltar;
      global $wrecexc;
      global $wfacturable;
      global $wcco;
      global $wccogra;
      global $wfeccar;
      global $wcontip;
      global $wconinv;
      global $wok;
      global $wcodpaq;
      global $wpaquete;
	  global $wtipfac;
	  global $esTrasladoAutomatico;
	  global $trasladoRealizado;

	//TRAIGO LAS EXISTENCIAS DEL CENTRO DE COSTOS DE LA VARIABLE $wbodega
	$esTrasladoAutomatico = true;
	$q= "SELECT karexi, Karcod, Karcco "
	   ."  FROM ".$wbasedato."_000007 "
	   ." WHERE karcco = '".$wbodega."'"
	   ."   AND karcod = '".$wprocod."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row_exik = mysql_fetch_array($res);
	$wexisteKardex = $row_exik['karexi'];

	//TRAIGO EL NOMBRE DEL CENTRO DE COSTOS ASOCIADO A LA VARIABLE $wbodega
	$q= "SELECT Ccocod, Ccodes "
	   ."  FROM ".$wbasedato."_000003"
	   ." WHERE Ccocod = '".$wbodega."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$wnombrecco = strtoupper($row['Ccodes']);

	// VALIDO SI LA CANTIDAD DE PRODUCTOS ES MAYOR A LA QUE HAY EN EL KARDEX, SI ES MAYOR SE DETIENE LA FUNCION Y NO MUESTRA EL MENSAJE DEL
	// INVENTARIO DEL KARDEX ACTUAL PARA ESTE PRODUCTO
	if ($wcantidad > $wexisteKardex)
		{
		$trasladoRealizado = false;
		return false;
		}

	//SI LAS DEVOLUCIONES PARA LA HISTORIA E INGRESO ASOCIADAS A UN ARTICULOS SON IGUALES A CERO, DETIENE LA FUNCION trasladoautomatico
	if ($wdevol=='on')
		{
		$trasladoRealizado = false;
		return false;
		}
	elseif($wexiste == 0 and $wdevol=='on')
	{
		$trasladoRealizado = false;
		return false;
	}

	//VALIDO SI LA CANTIDAD DE PRODUCTOS EN EL KARDEX EN IGUAL A CERO O SI LA CANTIDAD EXISTENTE EN EL KARDEX EN MENOR A LA SOLICITADA
	//SI ES ASI SE DETIENE LA FUNCION
	if ($wexisteKardex == 0 or $wexisteKardex < $wcantidad)
		{
			$trasladoRealizado = false;
			echo "<script>
				    alert ('NO HAY INVENTARIO EN $wnombrecco PARA ESTE PRODUCTO');
		          </script>";
			return false;
		}

	 // BLOQUEA LAS TABLAS DEL KARDEX PARA ACTUALIZAR LOS CENTROS DE COSTOS DE TRASLADO
	  $q = "lock table ".$wbasedato."_000007 LOW_PRIORITY WRITE";
	  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			//ACTUALIZO EN LA TABLA DEL -- <SALDOS EN LINEA> -- DEL  **** KARDEX ****
	  if ($wexiste >= 0 and $wexisteKardex > 0 and $wdevol!='on')
		    {
			$q= " UPDATE ".$wbasedato."_000007 "
			   ."    SET karexi = karexi - ".$wcantidad
			   ."  WHERE karcco = '".$wbodega."'"
			   ."    AND karcod = '".$wprocod."'";
		    $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


             //Recalculo el costo promedio del articulo en el centro de costos destino
            $q= "SELECT karexi, karpro "
                ."  FROM ".$wbasedato."_000007 "
                ." WHERE karcco = '".$wbodega."'"
                ."   AND karcod = '".$wprocod."'";
            $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $row_cos = mysql_fetch_array($res_cos);

            $wexist_actuales_origen = $row_cos[0];
            $wcosto_pro_actual_origen = $row_cos[1];

            $q7= "SELECT karexi, karpro "
                ."  FROM ".$wbasedato."_000007 "
                ." WHERE karcco = '".$wccogra."'"
                ."   AND karcod = '".$wprocod."'";
            $res_cos = mysql_query($q7,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q7." - ".mysql_error());
            $row_cos = mysql_fetch_array($res_cos);

            $wexist_actuales_destino = $row_cos[0];
            $wcosto_pro_actual_destino = $row_cos[1];

            //Nuevo costo promedio del articulo en el centro de costos a grabar
            $wnuevocospro = (($wexist_actuales_destino * $wcosto_pro_actual_destino) + ($wcantidad * $wcosto_pro_actual_origen))/($wexist_actuales_destino + $wcantidad);
            $wnuevocospro = round($wnuevocospro,4);

            $q1= " UPDATE ".$wbasedato."_000007 "
                ."    SET Karpro = ".$wnuevocospro." "
                ."  WHERE Karcco = '".$wccogra."'"
                ."    AND Karcod = '".$wprocod."'";
            $err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


			//ACTUALIZA EL STOCK DEL CENTRO DE COSTOS
		    $q1= " UPDATE ".$wbasedato."_000007 "
			    ."    SET karexi = karexi + ".$wcantidad
			    ."  WHERE karcco = '".$wccogra."'"
			    ."    AND karcod = '".$wprocod."'";
		    $res3 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());



			//TRAIGO LAS NUEVAS EXISTENCIAS DEL KARDEX PARA REEMPLAZAR LA VARIABLE $wexiste
			$q= "SELECT karexi, Karcod, Karcco "
			   ."  FROM ".$wbasedato."_000007 "
			   ." WHERE karcco = '".$wccogra."'"
			   ."   AND karcod = '".$wprocod."'";
			$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row_exi = mysql_fetch_array($res2);
			$wexiste = $row_exi['karexi'];
			}
	    $q= " UNLOCK TABLES";
	    $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	  /////////////////////////////////////////////////////////////////////////////////
	  //ACTUALIZO Y TOMO EL CONSECUTIVO Y EL CODIGO DEL CONCEPTO DE VENTA EN INVENTARIO

	  $q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  $q1= " UPDATE ".$wbasedato."_000008 "
		  ."    SET concon = concon + 1 "
		  ."  WHERE concod = ".$wconceptraslado." ";
	  $err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  $q= " SELECT concon, concod "
		 ."   FROM ".$wbasedato."_000008 "
		 ."  WHERE concod = ".$wconceptraslado." ";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($err);
	  $wnromvto=$row[0];
	  $wconmvto=$row[1];                            //Concepto de Salida (Ventas)

	  $q = " UNLOCK TABLES";
	  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  /////////////////////////////////////////////////////////////////////////////////
	  //ACA DESCARGO DEL INVENTARIO ===================================================
	  /////////////////////////////////////////////////////////////////////////////////

	  //===================================================================================================================================
	  //===================================================================================================================================
	  //GRABO EN LA TABLA DEL -- <ENCABEZADO> -- DEL **** MOVIMIENTO DE INVENTARIOS ****
	  $q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     , mendan, menpre, mennit, menusu, menfac, menest, Seguridad        ) "
		 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvto."','".$wconceptraslado."','".$wbodega."','".$wccogra."', '.'   , 0     , '0'   , '.'   , '.'   ,   'on', 'C-".$wusuario."')";
	  $res = mysql_query($q,$conex) or die ("Error en la 10 para la funcion translado: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	  //===================================================================================================================================
	  //===================================================================================================================================
	  //GRABO EN LA TABLA DEL -- <DETALLE> -- DE  **** MOVIMIENTO DE INVENTARIOS ****
	  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //============================================================================================================================

	  //=========================================
	  //TRAIGO EL COSTO PROMEDIO DEL ARTICULO
	  $q= "SELECT karpro "
		 ."  FROM ".$wbasedato."_000007 "
		 ." WHERE karcco = '".$wbodega."'"
		 ."   AND karcod = '".$wprocod."'";
	  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row_cos = mysql_fetch_array($res2);

	  if ($row_cos[0] == "")
		 $row_cos[0]=0;

	  //=========================================
	  //GRABO EL DETALLE DEL ARTICULO
	  $q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan      ,  mdevto                    , mdepiv , mdeest, Seguridad        ) "
		 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconceptraslado."','".$wnromvto."','".$wprocod."',".$wcantidad.",".($wcantidad*$row_cos[0]).", '0'    , 'on'  , 'C-".$wusuario."')";
	  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());





	}

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA MOSTRAR LAS OPCIONES DE RECIBOS DE DINERO - FORMAS DE PAGO
  //function formasdepago($fk,$wfpa,$wdocane,$wobsrec,$wvalfpa,$wtotrec)
  function formasdepago($fk)
    {
	    global $fk;
	    global $wbasedato;
	    global $conex;
	    global $wcf;
	    global $wcf2;
	    global $wcol;
	    global $colspan;
	    global $wubica;
	    global $wautori;
	    global $obliga;

	    global $wfpa;
	    global $wdocane;
	    global $wobsrec;
	    global $wvalfpa;
	    global $wtotrec;
	    global $wrecexcfpa;

	    global $wbandes;


	    echo '</table>';
	    echo "<br>";
	    echo "<br>";
	    //echo "<table width='90%'>";
	    echo "<table border='0'>";
	    echo "<tr><td align=center colspan=15 class=encabezadoTabla><b>D E T A L L E &nbsp;&nbsp; D E L&nbsp;&nbsp; A B O N O</b></td></tr>";

	    for ($j=1;$j<=$fk;$j++)
	        {

		      echo "<tr>";

		      $q =  " SELECT fpacod, fpades "
			       ."   FROM ".$wbasedato."_000023 "
			       ."  WHERE fpaest = 'on' "
			       ."  ORDER BY fpacod ";

			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res) ;


			  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //FORMA DE PAGO
			  echo "<td align=left class=".$wcf2." colspan=1><b>Forma de pago:</b><select name='wfpa[".$j."]' onchange='enter()'>";
			  for ($i=1;$i<=$num;$i++)
			    {
			      $row = mysql_fetch_array($res);
			      $comp= $row[0]." - ".$row[1];
			      if (isset($wfpa[$j]) and $wfpa[$j]==$comp)
			      		echo "<option selected>".$wfpa[$j]."</option>";
			      else
			      	{
			      	 echo "<option>".$row[0]." - ".$row[1]."</option >";
			      	 if (!isset ($wfpa[$j]) and $i==1)
			      		$wfpa[$j]=$row[0]." - ".$row[1];
		      		}
		        }
			  echo "</select></td>";


			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //DOCUMENTO ANEXO
			  if (isset($wdocane[$j]) and $wdocane[$j]!='') //Si ya fue digitado el documento anexo
			     echo "<td class=".$wcf2." colspan=1><b>Dcto Anexo:</b><INPUT TYPE='text' NAME='wdocane[".$j."]' VALUE='".$wdocane[$j]."'></td>";  //wdocane
			    else
			       echo "<td class=".$wcf2." colspan=1><b>Dcto Anexo:</b><INPUT TYPE='text' NAME='wdocane[".$j."]' ></td>";                        //wdocane


			  //////busco para la opcion seleccionada, si es tarjeta o cheque
			  $expefpa=explode('-',$wfpa[$j]);
			  $q =  " SELECT fpache, fpatar "
			       ."   FROM ".$wbasedato."_000023 "
			       ."  WHERE fpacod='".$expefpa[0]."' ";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res) ;
			  $row = mysql_fetch_array($res);

			  if ($row[0]=='on' or $row[1]=='on')
				{
				 $obliga[$j]='on';
				 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			     //BANCO, consulto lista de bancos
			     echo "<td class=".$wcf2." colspan=1><b>Datos del Banco:</b><select name='wobsrec[".$j."]' >";

			     $q =  " SELECT bancod, bannom "
			          ."   FROM ".$wbasedato."_000069 "
			          ."  WHERE banest='on' ";
			     $resu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			     $num1 = mysql_num_rows($resu) ;
				 for ($y=1;$y<=$num1;$y++)
				    {
		 		     $banc = mysql_fetch_array($resu);

		 	         if (isset($wobsrec[$j]) and $wobsrec[$j]==$banc[0].'-'.$banc[1])    //Si ya fue digitado la observacion
		                echo "<option selected>".$banc[0].'-'.$banc[1]."</option >";     //wobsrec
		               else
		                  echo "<option>".$banc[0].'-'.$banc[1]."</option>";
				    }
				 echo "</select></td>";

				 $colspan=9;
				 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			     //PLAZA
			     if (isset($wubica[$j])) //Si ya fue digitada la plaza
			       {
				    If ($wubica[$j]=='1-Local')
				       $otro='2-Otras plazas';
				  	  else
				  	     $otro='1-Local';
			        echo "<td class=".$wcf2." colspan=1><b>Ubicacion:</b><select name='wubica[".$j."]' ><option selected>".$wubica[$j]."</option ><option>".$otro."</option></select></td>";
		           }
			      else
			         echo "<td class=".$wcf2." colspan=1><b>Ubicacion:</b><select name='wubica[".$j."]' ><option selected>1-Local</option ><option>2-Otras plazas</option></select></td>";                        //wdocane
			     ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			     //NUMERO DE AUTORIZACION
			     if (isset($wautori[$j])) //Si ya fue digitada la autorizacion
			        echo "<td class=".$wcf2." colspan=1><b>Nº autorizacion:</b><INPUT TYPE='text' NAME='wautori[".$j."]' VALUE='".$wautori[$j]."'></td>";     //wobsrec
			       else
			          echo "<td class=".$wcf2." colspan=1><b>Nº autorizacion:</b><INPUT TYPE='text' NAME='wautori[".$j."]' ></td>";                           //wobsrec
			    }
			   else
				  {
				   $obliga[$j]='off';
				   $colspan=9;
				   /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			       //OBSERVACIONES
			       if (isset($wobsrec[$j])) //Si ya fue digitado la observacion
			          echo "<td class=".$wcf2." colspan=1><b>Observ.:</b><INPUT TYPE='text' NAME='wobsrec[".$j."]' VALUE='".$wobsrec[$j]."'></td>";     //wobsrec
			         else
			           {
			            echo "<td class=".$wcf2." colspan=1><b>Observ.:</b><INPUT TYPE='text' NAME='wobsrec[".$j."]' ></td>";                           //wobsrec
				        // espacios en blanco para nivelar
			            echo "<td class=".$wcf2." colspan=1><b>&nbsp;</td>";
			            echo "<td class=".$wcf2." colspan=1><b>&nbsp;</td>";
			           }
			      }

			  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //RECONOCIDO O EXCEDENTE PARA LA FORMA DE PAGO
			  if (!isset($wrecexcfpa) or $wrecexcfpa=="" )
			     echo "<td align=center class=".$wcf2." colspan=1><b>(R)ec/(E)xc<br></b><INPUT TYPE='text' NAME='wrecexcfpa' SIZE='1' MAXLENGTH='1' VALUE='R' onkeypress='if (event.keyCode != 69 & event.keyCode != 82 & event.keyCode != 101 & event.keyCode != 114)  event.returnValue = false'></td>";
			    else
			       echo "<td align=center class=".$wcf2." colspan=1><b>(R)ec-(E)xc<br></b><INPUT TYPE='text' NAME='wrecexcfpa' SIZE='1' MAXLENGTH='1' VALUE='".strtoupper($wrecexcfpa)."' onkeypress='if (event.keyCode != 69 & event.keyCode != 82 & event.keyCode != 101 & event.keyCode != 114)  event.returnValue = false'></td>";


		      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		      //Con la siguiente instrucción en Javascript se ubica el cursor en el ultimo campo del valor de la forma de pago osea en: $wvalfpa[$j] : en el VALOR
		      ?>
		       <script>
		          //function ira(){document.Recibos_y_notas.elements.length-180.focus();}
		          //function ira(){document.Recibos_y_notas.elements[document.Recibos_y_notas.elements.length-1].focus();}
		          ///function ira(){document.Recibos_y_notas.elements[document.Recibos_y_notas.elements.length-4].focus();}
		       </script>
		      <?php


		      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		      //VALOR
              if (isset($wvalfpa[$j]) and $wvalfpa > 0  and $wvalfpa[$j] != '' and $wvalfpa[$j]>0) //Si ya fue digitado el valor y es mayor a cero
		        {
			     $wpagado=0;
		         for ($y=1;$y<=$j;$y++)
		            {
			         $wvalfpa[$y]=str_replace(",","",$wvalfpa[$y]);    //Le quito el formato al número
		             $wpagado=$wpagado+$wvalfpa[$y];
	                }

		         $wvalfpa[$j]=str_replace(",","",$wvalfpa[$j]); //Esto se hace para quitarle el formato que trae el número
		         echo "<td class=".$wcf2." colspan=2><b>Valor:</b><INPUT TYPE='text' NAME='wvalfpa[".$j."]' VALUE='".number_format($wvalfpa[$j],2,'.',',')."'></td>";       //wvalfpa
		         if (($wtotrec-$wpagado) > 0 )
		            echo "<td class=".$wcf2." colspan=1><b>Saldo: </b>".number_format(($wtotrec-$wpagado),2,'.',',')."</td>";            //wtotventot-wtotfpa
		           else
		              echo "<td class=".$wcf2." colspan=1><b>Saldo: </b>".number_format((0),2,'.',',')."</td>";                             //wtotventot-wtotfpa
		        }
		       else
		          echo "<td class=".$wcf2." colspan=2><b>Valor: </b><INPUT TYPE='text' NAME='wvalfpa[".$j."]'></td>";  //wvalfpa


		      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		      //BANCO EN EL QUE SE CONSIGNA O DESTINO
		      echo "<td class=".$wcf2." colspan=1><b>En que Banco se consigna:<br></b><select name='wbandes[".$j."]' onchange='enter()'>";
	          $wfpago=explode("-",$wfpa[$j]);
		      $q= " SELECT bancod, bannom "
		         ."   FROM ".$wbasedato."_000069, ".$wbasedato."_000023 "
		         ."  WHERE banest = 'on' "
		         ."    AND banrec = 'on' "
		         ."    AND bancod = fpacba "
		         ."    AND fpacod = '".$wfpago[0]."'"
		         ."    AND fpaest = 'on' "
		         ."  UNION "
		         ." SELECT bancod, bannom "
		         ."   FROM ".$wbasedato."_000069 "
		         ."  WHERE banest = 'on' "
		         ."    AND bancag = 'on' "
		         ."    AND banrec = 'on' ";

		      $resban = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $numban = mysql_num_rows($resban) ;
		      $banc = mysql_fetch_array($resban);

              echo "<option selected>".$banc[0].'-'.$banc[1]."</option >";
              $resu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $num1 = mysql_num_rows($resu);

			  for ($y=1;$y<=$numban;$y++)
			     {
				  $banc = mysql_fetch_array($resban);
	              echo "<option>".$banc[0].'-'.$banc[1]."</option>";
			     }
			  echo "</select></td>";

		      echo "</tr>";
		      echo "<input type='hidden' NAME='obliga[".$j."]' value='".$obliga[$j]."'>";
			}
    }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FEBRERO 18 DE 2009: Toda esta funcion fue creada en esta fecha
  //*****************
  //                               Conc.Factu, Insumo  , Hist. , Ingreso, Cant. maxima para devolver
  function buscar_saldo_en_historia($wcodcon , $wprocod, $whis , $wing, $waprovecha )
     {
	  global $wbasedato;
	  global $conex;
	  global $wexidev;
	  //global $waprovecha;

	  if ($waprovecha=="on") //Si es devolucion de aprovechamiento
	    {
		  $q = " SELECT SUM(tcarcan) "
		      ."   FROM ".$wbasedato."_000106 "
		      ."  WHERE tcarhis    = '".$whis."'"
		      ."    AND tcaring    = '".$wing."'"
		      ."    AND tcarconcod = '".$wcodcon."'"       //Concepto de facturacion
		      ."    AND tcarprocod = '".$wprocod."'"
		      ."    AND tcardev   != 'on' "
		      ."    AND tcarapr    = 'on' ";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);

		  if ($num > 0)
		    {
			  $row = mysql_fetch_array($res);
			  $wcangra=$row[0];

			  $q = " SELECT SUM(tcarcan) "
			      ."   FROM ".$wbasedato."_000106 "
			      ."  WHERE tcarhis    = '".$whis."'"
			      ."    AND tcaring    = '".$wing."'"
			      ."    AND tcarconcod = '".$wcodcon."'"    //Concepto de Facturacion
			      ."    AND tcarprocod = '".$wprocod."'"
			      ."    AND tcardev    = 'on' "
			      ."    AND tcarapr    = 'on'";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row = mysql_fetch_array($res);
			  $wcandev=$row[0];

			  $wexidev=($wcangra-$wcandev);  //Esta es la cantidad maxima que se puede devolver
			}
		    else
		      $wexidev=0;
	    }
	      else  //Si no es devolucion de aprovechamiento
	        {
			  $q = " SELECT SUM(tcarcan) "
			      ."   FROM ".$wbasedato."_000106 "
			      ."  WHERE tcarhis    = '".$whis."'"
			      ."    AND tcaring    = '".$wing."'"
			      ."    AND tcarconcod = '".$wcodcon."'"       //Concepto de facturacion
			      ."    AND tcarprocod = '".$wprocod."'"
			      ."    AND tcardev   != 'on' "
			      ."    AND tcarapr    = 'off' ";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if ($num > 0)
			    {
				  $row = mysql_fetch_array($res);
				  $wcangra=$row[0];

				  $q = " SELECT SUM(tcarcan) "
				      ."   FROM ".$wbasedato."_000106 "
				      ."  WHERE tcarhis    = '".$whis."'"
				      ."    AND tcaring    = '".$wing."'"
				      ."    AND tcarconcod = '".$wcodcon."'"    //Concepto de Facturacion
				      ."    AND tcarprocod = '".$wprocod."'"
				      ."    AND tcardev    = 'on' "
				      ."    AND tcarapr    = 'off'";
				  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row = mysql_fetch_array($res);
				  $wcandev=$row[0];

				  $wexidev=($wcangra-$wcandev);  //Esta es la cantidad maxima que se puede devolver
				}
			    else
			       $wexidev=0;
		    }
	}
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


  function validar_datos_a_grabar()
    {
	  global $conex;
      global $wbasedato;
      global $wusuario;
	  global $whistoria;
      global $wing;
      global $wno1;
      global $wno2;
      global $wap1;
      global $wap2;
      global $wdoc;
      global $wcodemp;
      global $wnomemp;
      global $wfecing;
      global $wser;
      global $wcodcon;
      global $wnomcon;
      global $wprocod;
      global $wpronom;
      global $wcodter;
      global $wnomter;
      global $wporter;
      global $wcantidad;
      global $wvaltar;
      global $wrecexc;
      global $wfacturable;
      global $wcco;
      global $wccogra;
      global $wfeccar;
      global $wcontip;
      global $wconinv;
      global $wok;
      global $wcodpaq;
      global $wpaquete;

	  $wok="on";

      //Verifico que la historia y numero de ingreso exista
      $q = " SELECT COUNT(*) "
          ."   FROM ".$wbasedato."_000100, ".$wbasedato."_000101 "
          ."  WHERE pachis = '".$whistoria."'"
          ."    AND pachis = inghis "
          ."    AND ingnin = '".$wing."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);
	  if ($num > 0)
	    {
	      $row = mysql_fetch_array($res);
	      if ($row[0]>0)
	         $wok="on";
	        else
	            {
		        $wok="off";
		        ?>
			      <script>
			        alert ("LA HISTORIA DIGITADA NO EXISTE");
	              </script>
	  		    <?php
		        }
        }

	if( $wok == "on" )
	{

      	if( isset($wpaquete) && $wpaquete == "on" )
		{
	      if( !isset($wcodpaq) || $wcodpaq == "" )
		    {
		  	$wok="off";
		  	?>
			      <script>
			      		alert ( "NO HA INGRESADO CODIGO DEL PAQUETE" );
	              </script>
		  	<?php
		    }
      	}
    }

      //Verifico que el concepto exista
      if ($wok=="on")
        {
	      $q = " SELECT COUNT(*) "
	          ."   FROM ".$wbasedato."_000004 "
	          ."  WHERE grucod = '".$wcodcon."'"
	          ."    AND gruest = 'on' ";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);
		  if ($num > 0)
		    {
		      $row = mysql_fetch_array($res);
		      if ($row[0]>0)
		         $wok="on";
		        else
		            {
			        $wok="off";
			        ?>
				      <script>
				        alert ("EL CONCEPTO NO EXISTE O ESTA INACTIVO");
		              </script>
		  		    <?php
			        }
	        }
        }

		//Verifico que el concepto si coincida con la descripcion en la interfaz
      if ($wok=="on")
        {
	      $q_conc = " SELECT COUNT(*) "
	          ."   FROM ".$wbasedato."_000004 "
	          ."  WHERE grucod = '".$wcodcon."'"
			  ."	AND grudes = '".$wnomcon."'"
	          ."    AND gruest = 'on' ";
	      $res_conc = mysql_query($q_conc,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_conc." - ".mysql_error());
		  $row_conc = mysql_fetch_array($res_conc);
		      if ($row_conc[0]>0)
		         $wok="on";
		        else
		            {
			        $wok="off";
			        ?>
				      <script>
				        alert ("LA DESCRIPCION DEL CONCEPTO NO COINCIDE CON EL CODIGO DEL CONCEPTO, AL SELECCIONAR ACEPTAR SE MOSTRARÁ CORRECTAMENTE LA INFORMACIÓN.");
		              </script>
		  		    <?php
			        }

        }

      //Verifico que el centro de costo exista
      if ($wok=="on")
        {
	      $q = " SELECT COUNT(*) "
	          ."   FROM ".$wbasedato."_000003 "
	          ."  WHERE ccocod = '".$wccogra."'"
	          ."    AND ccoest = 'on' ";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);
		  if($num > 0)
		    {
		      $row = mysql_fetch_array($res);

		      if ($row[0]>0)
		         $wok="on";
		        else
		            {
			        $wok="off";
			        ?>
				      <script>
				        alert ("EL CENTRO DE COSTO NO EXISTE O ESTA INACTIVO");
		              </script>
		  		    <?php
			        }
	        }
        }

      //Verifico que el CODIGO DEL PROCEDIMIENTO exista
      if ($wok=="on")
        {
	      if ($wconinv !="on")
	        {
		      $q = " SELECT COUNT(*) "
		          ."   FROM ".$wbasedato."_000103 "
		          ."  WHERE procod = '".$wprocod."'"
		          ."    AND proest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if($num > 0)
			    {
			      $row = mysql_fetch_array($res);
			      if ($row[0]>0)
			         $wok="on";
			        else
			            {
				        $wok="off";
				        ?>
					      <script>
					        alert ("EL PROCEDIMIENTO NO EXISTE O ESTA INACTIVO");
			              </script>
			  		    <?php
				        }
		        }
	        }
	        else
	            {
		        $q = " SELECT COUNT(*) "
			        ."   FROM ".$wbasedato."_000001 "
			        ."  WHERE artcod = '".$wprocod."'"
			        ."    AND artest = 'on' ";
			    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				if ($num > 0)
				    {
				    $row = mysql_fetch_array($res);
				    if ($row[0]>0)
				       $wok="on";
				      else
				        {
					        $wok="off";
					        ?>
						      <script>
						        alert ("EL ARTICULO NO EXISTE O ESTA INACTIVO");
				              </script>
				  		    <?php
					    }
			        }

	            }
        }

      //Verifico que el NOMBRE DEL PROCEDIMIENTO exista
      if($wok=="on")
        {
	      if($wconinv !="on")
	        {
		      $q = " SELECT COUNT(*) "
		          ."   FROM ".$wbasedato."_000103 "
		          ."  WHERE pronom = '".$wpronom."'"
		          ."    AND proest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if($num > 0)
			    {
			      $row = mysql_fetch_array($res);
			      if ($row[0]>0)
			         $wok="on";
			        else
			            {
				        $wok="off";
				        ?>
					      <script>
					        alert ("EL NOMBRE DEL PROCEDIMIENTO NO EXISTE O FUE CAMBIADO");
			              </script>
			  		    <?php
				        }
		        }

				if(isset($wprocod)){
				  $q_pro =   " SELECT COUNT(*) "
							."   FROM ".$wbasedato."_000103"
							."  WHERE procod = '".$wprocod."'"
							."    AND pronom = '".$wpronom."' ";
				  $res_pro = mysql_query($q_pro,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_pro." - ".mysql_error());
				  $row_pro = mysql_fetch_array($res_pro);

					if ($row_pro[0]>0)
						 $wok="on";
						else
							{
							$wok="off";
							?>
							  <script>
								alert ("EL CODIGO DEL PROCEDIMIENTO NO CORRESPONDE CON LA DESCRIPCION, AL SELECCIONAR ACEPTAR SE MOSTRARÁ CORRECTAMENTE LA INFORMACIÓN.");
							  </script>
							<?php
							}

				}

	        }
	        else
	            {
		        $q = " SELECT COUNT(*) "
			        ."   FROM ".$wbasedato."_000001 "
			        ."  WHERE artnom = '".$wpronom."'"
			        ."    AND artest = 'on' ";
			    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);
				if ($num > 0)
				    {
				    $row = mysql_fetch_array($res);
				    if ($row[0]>0)
				       $wok="on";
				      else
				        {
				          $wok="off";
				          ?>
				            <script>
					          alert ("EL NOMBRE DEL ARTICULO NO EXISTE O FUE CAMBIADO");
				            </script>
				  	      <?php
					    }
			        }
				//Se valida si el codigo y el nombre del articulo en el formulario si corresponder a los resgistros de la tabla ".$wbasedato."_000001",
				//la cual contiene la relacion codigo, nombre de articulo, ademas solo hace esta validacion si en el formulario se envia el codigo del articulo. Jonatan Lopez
				if(isset($wprocod)){
				  $q_art =   " SELECT COUNT(*) "
							."   FROM ".$wbasedato."_000001"
							."  WHERE artcod = '".$wprocod."'"
							."    AND artnom = '".$wpronom."' ";
				  $res_art = mysql_query($q_art,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_art." - ".mysql_error());
				  $row_art = mysql_fetch_array($res_art);

					 if ($row_art[0]>0)
						 $wok="on";
						else
							{
							$wok="off";
							?>
							  <script>
								alert ("EL CODIGO DEL INSUMO NO CORRESPONDE CON LA DESCRIPCION; AL SELECCIONAR ACEPTAR SE MOSTRARÁ EL NOMBRE CORRECTO.");
							  </script>
							<?php
							}
						}
				}


	    }

      //Verifico que el NIT DEL TERCERO exista
      if($wok=="on")
        {
	      if($wcontip =="C")  //Si es compartido
	        {
		      $q = " SELECT COUNT(*) "
		          ."   FROM ".$wbasedato."_000051 "
		          ."  WHERE meddoc = '".$wcodter."'"
		          ."    AND medest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if($num > 0)
			    {
			      $row = mysql_fetch_array($res);
			      if ($row[0]>0)
			         $wok="on";
			        else
			            {
				        $wok="off";
				        ?>
					      <script>
					        alert ("EL TERCERO NO EXISTE O ESTA INACTIVO");
			              </script>
			  		    <?php
				        }
		        }
	        }
        }

      //Verifico que el NOMBRE DEL TERCERO exista
      if($wok=="on")
        {
	      if($wcontip =="C")  //Si es compartido
	        {
		      $q = " SELECT COUNT(*) "
		          ."   FROM ".$wbasedato."_000051 "
		          ."  WHERE mednom = '".$wnomter."'"
		          ."    AND medest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res);
			  if ($num > 0)
			    {
			      $row = mysql_fetch_array($res);
			      if ($row[0]>0)
			         $wok="on";
			        else
			            {
				        $wok="off";
				        ?>
					      <script>
					        alert ("EL NOMBRE TERCERO NO EXISTE O FUE CAMBIADO");
			              </script>
			  		    <?php
				        }
		        }
	        }
        }

      //Verifico que el campo de RECONOCIDO O EXCEDENTE este digitado
      if($wok=="on")
        {
	      if($wrecexc!="R" and $wrecexc!="E")  //Si es compartido
	        {
		      $wok="off";
			  ?>
			    <script>
			        alert ("DEBE COLOCAR SI ES RECONOCIDO O EXCEDENTE (R/E)");
			    </script>
			  <?php
			}
        }

      //Verifico que el campo de FACTURABLE este digitado
      if($wok=="on")
        {
	      if($wfacturable!="S" and $wfacturable!="N")  //Si es compartido
	        {
		      $wok="off";
			  ?>
			    <script>
			        alert ("DEBE COLOCAR FACTURABLE (S/N)");
			    </script>
			  <?php
			}
        }

      //Verifico que el PORCENTAJE del tercero exista
      if($wok=="on")
        {
	      if($wcontip =="C")  //Si es compartido
	        {
		      if(!isset($wporter) or trim($wporter)=="")
		        {
			      $wok="off";
				  ?>
				    <script>
				        alert ("EL TERCERO NO TIENE DEFINIDO UN PORCENTAJE");
				    </script>
				  <?php
				}
			}
        }

      //Verifico que la CANTIDAD exista y sea mayor a cero
      if($wok=="on")
        {
	      if(!isset($wcantidad) or $wcantidad<=0)
	        {
		      $wok="off";
			  ?>
			    <script>
			        alert ("CANTIDAD DEBE SE MAYOR A CERO");
			    </script>
			  <?php
			}
        }

      //Verifico que la TARIFA exista y sea mayor a cero
      if($wok=="on")
        {
	      if (!isset($wvaltar))
	        {
		     $wok="off";
			  ?>
			    <script>
			        alert ("NO EXISTE TARIFA PARA EL PROCEDIMIENTO");
			    </script>
			  <?php
			}
        }

      //Verifico que la FECHA exista
      if($wok=="on")
        {
	      if(!isset($wfeccar) or trim($wfeccar)=="")
	        {
		      $wok="off";
			  ?>
			    <script>
			        alert ("LA FECHA DIGITADA NO EXISTE O NO SE DIGITO");
			    </script>
			  <?php
			}
        }

    }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Para buscar si existen cargos anteriores al ingreso actual sin facturar
  function cargos_anteriores()
    {
	    global $conex;
        global $wbasedato;
        global $wcajadm;
        global $whistoria;
        global $wing;
        global $wcargos_sin_facturar;

	    $q =  " SELECT tcaring "
	         ."   FROM ".$wbasedato."_000106 "
	         ."  WHERE tcarhis = '".$whistoria."'"
	         ."    AND tcaring <= '".(intval($wing)-1)."'+0"
	         ."    AND tcarest = 'on' "
	         ."    AND tcarvto <> (tcarfex+tcarfre) "    //Trae los cargos con valores negativos o positivos
	         ."    AND tcarfac = 'S' "
	         ."  GROUP BY 1 " ;
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num = mysql_num_rows($res);

	    if ($num > 0)
	        {
		    $wingresos = "";
		    for ($i=1;$i<=$num;$i++)
		       {
			    $row = mysql_fetch_array($res);
			    $wingresos= $wingresos.$row[0].", ";
		       }

		       $wmensaje="HAY CARGOS PENDIENTES DE FACTURAR DEL(OS) SIGUIENTE(S) INGRESO(S): ".$wingresos;

		       echo '<script language="javascript">';
			   echo 'alert ("'.$wmensaje.'")';
			   echo '</script>';

			   $wcargos_sin_facturar="ok";
		    }
	}


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA MOSTRAR LOS ARTICULOS SELECCIONADOS PARA LA VENTA
  function mostrar()
       {
	     global $whistoria;
	     global $wing;
	     global $wno1;
	     global $wno2;
	     global $wap1;
         global $wap2;
         global $wdoc;
         global $wcodemp;
         global $wnomemp;
         global $wfecing;
         global $wser;
         global $wcodcon;
         global $wnomcon;
         global $wprocod;
         global $wpronom;
         global $wcodter;
         global $wnomter;
         global $wporter;
         global $wcantidad;
         global $wvaltar;
         global $wrecexc;
         global $wfacturable;
         global $conex;
         global $wbasedato;
         global $wusuario;
         global $wcco;
         global $wnomcco;
         global $wccogra;
         global $wcf;
         global $wcf2;
         global $wpaquete;
         global $wdevol;
         global $wcodpaq;
         global $wnompaq;
         global $wcajadm;
         global $wcargos_sin_facturar;
		 global $wemp_pmla;

         if (!isset($wcargos_sin_facturar) or $wcargos_sin_facturar=="")
            {
	         cargos_anteriores();
	        }
	     echo "<input type='HIDDEN' name='wcargos_sin_facturar' value='".$wcargos_sin_facturar."'>";

         //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	     //ACA TRAIGO TODOS LOS CARGOS GRABADOS EN EL CENTRO DE COSTO Y USUARIO
	     if ($wcajadm!="on")  //Si el Usuario no es Administrativo o Supervisor Valido los cargos por usuario, solo traigo los del usuario
	        {
		     $q = " SELECT tcarusu, tcarhis, tcaring, tcarfec, tcarsin, tcarres, tcarno1, tcarno2, tcarap1, tcarap1,tcardoc, tcarser, "
		         ."        tcarconcod, tcarconnom, tcarprocod, tcarpronom, tcartercod, tcarternom, tcarterpor, tcarcan, tcarvun, tcarvto, "
		         ."        tcarrec, tcarfac, tcarfec, id, tcartfa, tcarfex, tcarfre, tcarapr, tcardev "
		         ."   FROM ".$wbasedato."_000106 "
		         //."  WHERE tcarusu = '".$wusuario."'"
		         ."  WHERE tcarhis = '".$whistoria."'"
		         ."    AND tcaring = '".$wing."'"
		         //."    AND tcarser = '".$wcco."'"
		         ."    AND tcarest = 'on' "
		         ."  ORDER BY id desc";
	        }
	       elseif($wcajadm=="on")   //Si el usuario es Administrativo o Supervisor muestro todos los cargos de la historia sin importar quien los grabo
	            {
			      $q = " SELECT tcarusu, tcarhis, tcaring, tcarfec, tcarsin, tcarres, tcarno1, tcarno2, tcarap1, tcarap1,tcardoc, tcarser, "
			          ."        tcarconcod, tcarconnom, tcarprocod, tcarpronom, tcartercod, tcarternom, tcarterpor, tcarcan, tcarvun, tcarvto, "
			          ."        tcarrec, tcarfac, tcarfec, id, tcartfa, tcarfex, tcarfre, tcarapr, tcardev "
			          ."   FROM ".$wbasedato."_000106 "
			          ."  WHERE tcarhis = '".$whistoria."'"
			          ."    AND tcaring = '".$wing."'"
			          ."    AND tcarest = 'on' "
			          ."  ORDER BY id desc";
		        }

	     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $num = mysql_num_rows($res);

	     if ($num > 0)
	        {
		     $wtotcta=0;
	         $wtotpac=0;
	         $wtotres=0;

	         echo "</table>";
	         echo "<center><table border='0'>";

	         echo "<br>";
	         //echo "<tr><td colspan=14>&nbsp</td></tr>";

	         //echo "<tr><td align=center colspan=14 class=".$wcf2."><b>DETALLE DE LA CUENTA</b></td></tr>";
	         echo "<tr><td align=center colspan=18 class=encabezadoTabla><b>D E T A L L E &nbsp;&nbsp; D E &nbsp;&nbsp; L A &nbsp;&nbsp; C U E N T A</b></td></tr>";
		     echo "<tr>";
		     echo "<th class=encabezadoTabla>&nbsp;</th>";
		     echo "<th class=encabezadoTabla>Registro</th>";
		     echo "<th class=encabezadoTabla>Fecha</th>";
		     echo "<th class=encabezadoTabla colspan=1>CCosto</th>";
		     echo "<th class=encabezadoTabla colspan=2>Concepto</th>";
		     echo "<th class=encabezadoTabla colspan=2>Procedimiento</th>";
		     echo "<th class=encabezadoTabla colspan=1>Aprov.</th>";
			 echo "<th class=encabezadoTabla colspan=1>Tip.Fac</th>";
			 echo "<th class=encabezadoTabla colspan=2>Tercero</th>";
			 echo "<th class=encabezadoTabla>Cantidad</th>";
			 echo "<th class=encabezadoTabla>V/r Unit.</th>";
			 echo "<th class=encabezadoTabla>V/r Total</th>";
			 echo "<th class=encabezadoTabla>Rec/Exc</th>";
			 echo "<th class=encabezadoTabla>Fact.</th>";
			 echo "<th class=encabezadoTabla>&nbsp;</th>";
			 echo "</tr>";

			 //Con este for se recorren todos los cargos
			 for ($i=1;$i<=$num;$i++)
	            {
	             $row = mysql_fetch_array($res);

	             if ($i%2==0)
	                $wcolor="fila1";
	               else
	                  $wcolor="fila2";

	             $q= " SELECT gruabo, grumca, gruinv "
	                ."   FROM ".$wbasedato."_000004 "
	                ."  WHERE grucod = '".$row[12]."'";
	             $resabo = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	             $rowabo = mysql_fetch_array($resabo);
	             $wmueinv=$rowabo[2];

	             if ($rowabo[0] == "on")
	                {
		             $q = " SELECT rdefue, rdenum, rdecco "
		                 ."   FROM ".$wbasedato."_000021 "
		                 ."  WHERE rdereg = ".$row[25];
		             $resdoc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		             $numdoc = mysql_num_rows($res);

		             if ($numdoc > 0)
		                {
	                     $rowdoc = mysql_fetch_array($resdoc);
	                     $wfudoc=$rowdoc[0];
	                     $wnrdoc=$rowdoc[1];
	                     $wccdoc=$rowdoc[2];
                        }
	                }

				 if(isset($wfudoc) && $wfudoc)
					$fuentedoc = $wfudoc;
				 else
					$fuentedoc = -1;

				 // Consulto la tabla de permisos para definir que acciones puede ejecutar el usuario actual
				 $qper = " SELECT Perfue, Perusu, Pergra, Permod, Percon, Peranu, Perimp "
						."   FROM ".$wbasedato."_000081 "
						."  WHERE Perfue = '".$fuentedoc."' "
						."    AND Perest = 'on' "
						."    AND Perusu = '".$wusuario."' ";
				 $resper = mysql_query($qper,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qper." - ".mysql_error());
				 $rowper = mysql_fetch_array($resper);

				 // Consulto si el recibo tiene cuadre de caja activo
				 $qcca =  " SELECT Cencua, Cenest "
						 ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000073, ".$wbasedato."_000074  "
						 ."  WHERE Rdereg = ".$row[25]
						 ."    AND Rdefue = Cdefue"
						 ."    AND Rdenum = Cdenum"
						 ."	   AND Rdecco = Cdecco"
						 ."    AND Cdecua = Cencua"
						 ."    AND Cdecaj = Cencaj"
						 ."    AND Cdecco = Cencco"
						 ."	   AND Cdeveg > 0 "
						 ."    AND Cenest = 'on'";
				 $rescca = mysql_query($qcca,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcca." - ".mysql_error());
				 $numcca = mysql_num_rows($rescca);

				 $perimp = 'on';
				 if(isset($rowper['Perimp']) && $rowper['Perimp']!='on')
					$perimp = 'off';

	             echo "<tr>";
	             if ($rowabo[0] == "on" and $rowabo[1]=="on" and $perimp=='on')  //Indica que es abono y mueve caja
			        echo "<td align=center class='".$wcolor."'><A href='Imp_documento.php?wfuedoc=".$wfudoc."&amp;wnrodoc=".$wnrdoc."&amp;wcco=".$wccdoc."&amp;wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'> Imprimir </A></td>";
			       else
			     echo "<td align=LEFT class='".$wcolor."'>&nbsp;</td>";
			     echo "<td align=RIGHT  class='".$wcolor."'><b>".$row[25]."</b></td>";                                 //Registro o Id
			     echo "<td align=LEFT   class='".$wcolor."'>".$row[24]."</td>";                                        //Fecha de Grabacion
			     echo "<td align=LEFT   class='".$wcolor."'>".$row[11]."</td>";                                        //Centro de Costo que grabo
			     echo "<td align=LEFT   class='".$wcolor."'>".$row[12]."</td>";                                        //Codigo concepto
			     echo "<td align=LEFT   class='".$wcolor."'>".$row[13]."</td>";                                        //Descripcion
			     if ($row[14] != "")
			        echo "<td align=LEFT   class='".$wcolor."'>".$row[14]."</td>";                                     //Codigo Procedimiento
			       else
			          echo "<td align=LEFT class='".$wcolor."'>&nbsp;</td>";
	             if ($row[15] != "")
	                echo "<td align=LEFT   class='".$wcolor."'>".$row[15]."</td>";                                     //Descripcion
	               else
	                  echo "<td align=LEFT class='".$wcolor."'>&nbsp;</td>";
	             if ($row[29] == "on")                                                                                                       //Aprovechamiento
	                echo "<td align=CENTER class='".$wcolor."'><b>Si</b></td>";                                        //Aprovechamiento Si
	               else
	                  echo "<td align=CENTER class='".$wcolor."'>No</td>";                                             //Aprovechamiento No
	             if ($row[30]=="on")
	                echo "<td align=CENTER class='".$wcolor."'>Devolución</td>";                                       //Tipo de facturar
	               else
	                  echo "<td align=CENTER class='".$wcolor."'>".$row[26]."</td>";                                   //Tipo de facturar
	             if ($row[16] != "")
	                echo "<td align=LEFT class='".$wcolor."'>".$row[16]."</td>";                                       //Nit Tercero
	               else
	                  echo "<td align=LEFT class='".$wcolor."'>&nbsp;</td>";                                            //Nit Tercero
	             if ($row[17] != "")
	                echo "<td align=LEFT class='".$wcolor."'>".$row[17]."</td>";                                       //Nombre
	               else
	                echo "<td align=LEFT class='".$wcolor."'>&nbsp;</td>";                                            //Nombre

				 echo "<td align=RIGHT  class='".$wcolor."'>".number_format($row[19],2,'.',',')."</td>";               //Cantidad
			     echo "<td align=RIGHT  class='".$wcolor."'>".number_format($row[20],0,'.',',')."</td>";               //Valor unitario
			     echo "<td align=RIGHT  class='".$wcolor."'>".number_format(($row[19]*$row[20]),0,'.',',')."</td>";    //Valor total
			     echo "<td align=CENTER class='".$wcolor."'>".$row[22]."</td>";                                        //Reconocido o Excedente
			     echo "<td align=CENTER class='".$wcolor."'>".$row[23]."</td>";                                        //Facturable (S/N)

				 $peranu = 'on';
				 if(isset($rowper['Peranu']) && $rowper['Peranu']!='on')
					$peranu = 'off';

			     if ($row[27]==0 and $row[28]==0 and $wmueinv!="on" and $peranu=='on' and $numcca==0)
			        echo "<td align=center class='".$wcolor."'><A href='cargos_ips.php?wid=".$row[25]."&wanular=S"."&wusuario=".$wusuario."&whistoria=".$whistoria."&wing=".$wing."&wfecing=".$wfecing."&wser=".$wser."&wcodemp=".$wcodemp."&wnomemp=".$wnomemp."&wno1=".$wno1."&wno2=".$wno2."&wap1=".$wap1."&wap2=".$wap2."&wdoc=".$wdoc."&wcco=".$wcco."&wccogra=".$wccogra."&wbasedato=".$wbasedato."&wnomcco=".$wnomcco."&wpaquete=".$wpaquete."&wcodpaq=".$wcodpaq."&wnompaq=".$wnompaq."&wemp_pmla=".$wemp_pmla."'>Anular</A></td>";
			       else
			         if ($row[27]==0 and $row[28]==0 and ($wmueinv=="on" or $peranu!='on' or $numcca>0))
			            echo "<td align=center class='".$wcolor."'><b>&nbsp;</b></td>";
			           else
			            echo "<td align=center class='".$wcolor."'><b>Facturado</b></td>";

				 echo "</tr>";

			     if ($row[23] == "S")
			        {
				     $wtotcta=$wtotcta+($row[19]*$row[20]);
				     if ($row[22] == "R")
				        $wtotres=$wtotres+($row[19]*$row[20]);
				       else
				          $wtotpac=$wtotpac+($row[19]*$row[20]);
			        }
			    }
			  echo "<tr>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=11><b><span class=textoMedio>TOTAL PACIENTE</span></b></td>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=4><span class=textoMedio><b>".number_format($wtotpac,0,'.',',')."</span></b></td>";
	          echo "<td align=CENTER class=colorAzul5 colspan=1><span class=textoMedio><b>E</span></b></td>";
	          echo "<td align=CENTER class=colorAzul5 colspan=1><span class=textoMedio><b>S</span></b></td>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=1><span class=textoMedio><b>&nbsp;</span></b></td>";
	          echo "</tr>";

	          echo "<tr>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=11><span class=textoMedio><b>TOTAL RESPONSABLE</span></b></td>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=4><span class=textoMedio><b>".number_format($wtotres,0,'.',',')."</span></b></td>";
	          echo "<td align=CENTER class=colorAzul5 colspan=1><span class=textoMedio><b>R</span></b></td>";
	          echo "<td align=CENTER class=colorAzul5 colspan=1><span class=textoMedio><b>S</span></b></td>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=1><span class=textoMedio><b>&nbsp;</span></b></td>";
	          echo "</tr>";

	          echo "<tr>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=11><span class=textoMedio><b>TOTAL CUENTA</span></b></td>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=4><span class=textoMedio><b>".number_format($wtotcta,0,'.',',')."</span></b></td>";
	          echo "<td align=RIGHT class=colorAzul5 colspan=3><span class=textoMedio><b>&nbsp;</span></b></td>";
	          echo "</tr>";
	        }
	}

    function restringirModificacion($wbasedato, $wemp_pmla, $conex, $whistoria, $wing){

        // consulta si el paciente ya está egresado, sino tiene egreso entonces es porque está activo
         $queryI = " SELECT  count(*)
                       FROM  {$wbasedato}_000108
                      WHERE Egrhis = '{$whistoria}'
                        AND Egring = '{$wing}'";
         $resI = mysql_query($queryI,$conex) or die ("Error: ".mysql_errno()." - en el query: <pre>".$queryI."</pre> - ".mysql_error());
         $paciente_activo = "off";
         $rowUP = mysql_fetch_array($resI);
        if($rowUP[0] < 1 ){
            $paciente_activo = "on";
        }

        $q51 = " SELECT Detval  FROM root_000051 WHERE Detemp = '".$wemp_pmla."'AND Detapl = 'permiteCargosAinactivos'";//--> se usa este parámetro para saber si se restringe las facturas radicadas, así seguirá funcionando igual en las demas empresas
        $res51 = mysql_query($q51, $conex) or die ("Error: ".mysql_errno()." - en el query: <pre>".$query."</pre> - ".mysql_error());
        $num51 = mysql_num_rows($res51);
        $row   = mysql_fetch_array( $res51 );
        $restringirFacturasRadicadas = true;//--> validar estado de la factura para pacientes activos
        if( $num51 == 0 ){
            //return(false);
            $restringirFacturasRadicadas = false;
        }

        if( $paciente_activo == "on" ){
            //--> ,modificación 2015-05-27 -> acá se returnaba false de una, si el paciente estaba activo
          if( !$restringirFacturasRadicadas ){
                return(false);
          }else{//--> no importa si el paciente está activo, si la factura está radicada no debe permitir realizar cambios

                $qfacturas = "SELECT COUNT(*) facturasRadicadas
                                FROM {$wbasedato}_000018, {$wbasedato}_000144
                               WHERE fenhis = '$whistoria'
                                 AND fening = '$wing'
                                 AND fenesf = estcod
                                 AND Estdes = 'Radicada'";
                $rs        = mysql_query( $qfacturas, $conex );
                $row = mysql_fetch_assoc( $rs );

                if( $row['facturasRadicadas']  > 0 ){
                    $query = " SELECT count(*)  FROM {$wbasedato}_000233 WHERE Hachis = '".$whistoria."' AND Hacing = '$wing' AND Hacfem >= '".date('Y-m-d')."' AND Hacest='on'";
                    $res2   = mysql_query($query, $conex) or die ("Error: ".mysql_errno()." - en el query: <pre>".$query."</pre> - ".mysql_error());
                    $row2   = mysql_fetch_array( $res2 );
                    if ($row2[0]*1 > 0){
                        return(false);
                    }else{
                        return(true);
                    }
                }else{
                    return(false);
                }
          }
        }else{

                if( !$restringirFacturasRadicadas ){
                    return(false);
                }
                $qfacturas = "SELECT COUNT(*) facturasRadicadas
                                FROM {$wbasedato}_000018, {$wbasedato}_000144
                               WHERE fenhis = '$whistoria'
                                 AND fening = '$wing'
                                 AND fenesf = estcod
                                 AND Estdes = 'Radicada'";
                $rs        = mysql_query( $qfacturas, $conex );
                $row = mysql_fetch_assoc( $rs );

                if( $row['facturasRadicadas']  > 0 ){
                    $query = " SELECT count(*)  FROM {$wbasedato}_000233 WHERE Hachis = '".$whistoria."' AND Hacing = '$wing' AND Hacfem >= '".date('Y-m-d')."' AND Hacest='on'";
                    $res2   = mysql_query($query, $conex) or die ("Error: ".mysql_errno()." - en el query: <pre>".$query."</pre> - ".mysql_error());
                    $row2   = mysql_fetch_array( $res2 );
                    if ($row2[0]*1 > 0){
                        return(false);
                    }else{
                        return(true);
                    }
                }else{
                    return(false);
                }

        }
    }

  //===========================================================================================================================================
  //INICIO DEL PROGRAMA
  //===========================================================================================================================================

/*********************************************************************************
 * AQUI COMIENZA EL PROGRAMA
 ********************************************************************************/

  global $wexidev; //FEBRERO 18 DE 2009:

  $wcf="fila2";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="fila1";  //COLOR DEL FONDO 2  -- Azul claro

  //===========================================================================================================================================
  //ACA COMIENZA EL ENCABEZADO DE LA VENTA

  // Definición del encabezado del aplicativo
  encabezado("REGISTRO DE CARGOS CLINICOS", $wactualiz, "clisur");

  echo "<center><table border='0'>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //ACA SE GRABAN LOS CARGOS
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if(isset($wgrabar))
    {
       if(isset($wconabo) && $wconabo=="on")
	   { $wccogra==$wcco;  }

		global $wdevol;
		global $waprovecha;
		global $wconmvto;
		global $wtrasladoauto;
		global $wauto;

	   //SEPTIEMBRE  4  DE 2007:
       validar_datos_a_grabar();

       if ($wok=="on")   //Si entra es porque paso la validación de todos los campos
       //===========================================================================
         {

		 //SEPTIEMBRE 5  DE 2007: Valido que si es por aprovechamiento pueda garbarse
          if (isset($waprovecha) and ($waprovecha=="on") and (!isset($wdevol) or $wdevol=="off"))
		  {  $wexiste=$wcantidad; }

		// FEBRERO 8 DE 2012: Declaracion de variables que permiten el proceso de traslado automatico Jonatan Lopez -------
		// JULIO 13 DE 2016: Si existe la variable $wsucursal en la url, utilizara ese valor, sino utilizara el valor por defecto de la root_000051 para TrasladoAutomaticoenCargo.
		echo "<input type='HIDDEN' name='wccotrasladar' value='".$wsucursal."'>";

		if(isset($wsucursal) and $wsucursal != ""){
			$wccotrasladar = $wsucursal;
		}else{
			$wccotrasladar = consultarAliasAplicacion($conex, $wemp_pmla, 'TrasladoAutomaticoenCargo');
		}

		$wbodega = consultarAliasAplicacion($conex, $wemp_pmla, 'BodegaPrincipal');
		$wconceptraslado = consultarAliasAplicacion($conex, $wemp_pmla, 'ConceptodeTraslado');
		$esTrasladoAutomatico = false;
		$trasladoRealizado    = true;
		if($wccotrasladar == true and $wbodega == true and $wconceptraslado == true and $wccogra == $wccotrasladar and (!isset($waprovecha) or $waprovecha=="off") and (isset($wconinv) and $wconinv=="on") and $wbod != 'on')
			{
			//Funcion que permite traslado automatico desde un centro de costos declarado en $wbodega hacia $wccotrasladar.
			trasladoautomatico($wexiste, $wdevol, $wbodega, $wconceptraslado, $wccogra, $wvaltar, $wno2, $waprovecha, $wconinv, $wpaquete, $wprocod, $wfecha, $hora, $wconmvto, $wcantidad, $wusuario, $whistoria, $wing );
			$wauto = 'on';
			}


        if ($wbod == 'on' and $wccotrasladar == true and $wbodega == true and $wconceptraslado == true and (!isset($waprovecha) or $waprovecha=="off") and (isset($wconinv) and $wconinv=="on"))
        {
          //============Septiembre 03 de 2013 Jonatan====================================================================================
		  //Para el caso de las bodegas se cambian las variables de los centros de costos a grabar y de bodega.
		  //El centro de costos a grabar sera el 1270.
          $wccogra = $wccotrasladar;

		  //El centro de costos de la bodega será el del usuario de bodega que esta activo en el sistema. (Ej:5075)
		  $wbodega = $wcco;
		  //==================================================================================================
          trasladoautomatico($wexiste, $wdevol, $wbodega, $wconceptraslado, $wccogra, $wvaltar, $wno2, $waprovecha, $wconinv, $wpaquete, $wprocod, $wfecha, $hora, $wconmvto, $wcantidad, $wusuario, $whistoria, $wing );
          $wauto = 'on';
        }


        if( !$esTrasladoAutomatico or ( $esTrasladoAutomatico && $trasladoRealizado ) or (isset($wdevol) and $wdevol=="on") or (isset($waprovecha) or $waprovecha=="on") ){/*2020-01-09 esta condicion busca garantizar que no se hagan movimientos de venta para bodegas que requieren traslado automático si no hay saldo suficiente en estas y que el traslado automático realmente se haya realizado, por lo tanto para cualquier caso que no implique este comportamiento (aprovechamientos, devoluciones o ventas directas) se permite continuar normalmente.*/

			//Se agrega esta validacion para que cuando un auxiliar que maneje bodega cargue articulos por aprovechamiento, siempre use el cco de medicina domiciliaria en el registro de la tabla clisur_000106.
			if ($wbod == 'on' and $wccotrasladar == true and $wbodega == true and $wconceptraslado == true and (!isset($waprovecha) or $waprovecha=="on") and (isset($wconinv) and $wconinv=="on"))
			{
			 //Se reasigna la variable que viene de la interfaz $wccogra con el valor de $wccotrasladar (1270).
			 $wccogra = $wccotrasladar;

			}


			 //---------------------------------------
			 //On if ((isset($wexiste) and ($wexiste >= $wcantidad or isset($waprovecha))and ($wconser == "A"or $wconser == "H")) or ($wconinv!="on" and $wconser=="H")) //tipo de servicio es (P)os o (A)mbos y valido que la cantidad a grabar sea menor a la que existe en el kardex
	          if((isset($wexiste) and ($wexiste >= $wcantidad) and ($wconser == "A" or $wconser == "H")) or ($wconinv!="on" and $wconser=="H")) //tipo de servicio es (P)os o (A)mbos y valido que la cantidad a grabar sea menor a la que existe en el kardex
		        {
			     //echo "<script>alert ('despues de existe $wexiste');</script>";
			      if(isset($wconinv) and $wconinv=="on" and (!isset($waprovecha) or $waprovecha=="off"))  //Si mueve inventarios y no es un aprovechamiento
			        {

					  //Si es un concepto de devolucion se multiplica la cantidad por menos 1
				      $q = "lock table ".$wbasedato."_000007 LOW_PRIORITY WRITE";
					  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					  //DESCARGO DEL KARDEX
					  //===================================================================================================================================
				      //ACTUALIZO EN LA TABLA DEL -- <SALDOS EN LINEA> -- DEL  **** KARDEX ****
					  if ($wauto == 'on' and !isset($wdevol) and $wdevol!="on")
						  {

							$q= " UPDATE ".$wbasedato."_000007 "
					           ."    SET karexi = karexi - ".$wcantidad
					           ."  WHERE karcco = '".$wccogra."'"
					           ."    AND karcod = '".$wprocod."'"
					           ."    AND karexi > 0 ";
							$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$q= " UNLOCK TABLES";
							$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


						  }
						  elseif ($wauto == 'on' and isset($wdevol) and $wdevol=="on")
									{

									}
					  else{
					  if(isset($wdevol) and $wdevol=="on")
				        {
						  $q= " UPDATE ".$wbasedato."_000007 "
					         ."    SET karexi  = karexi - ".($wcantidad*(-1))
					         ."  WHERE karcco  = '".$wccogra."'"
					         ."    AND karcod  = '".$wprocod."'"
					         ."    AND karexi >= 0 ";

				        }
						//NO PERMITE REALIZAR NINGUN PROCESO SI EL CENTRO DE COSTOS DE LA VARIABLE $wccotrasladar ES IGUAL A $wccogra 14 Feb 2012
					     else
				            {
						    $q= " UPDATE ".$wbasedato."_000007 "
					           ."    SET karexi = karexi - ".$wcantidad
					           ."  WHERE karcco = '".$wccogra."'"
					           ."    AND karcod = '".$wprocod."'"
					           ."    AND karexi > 0 ";
				            }
						$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$q= " UNLOCK TABLES";
						$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						}


					  /////////////////////////////////////////////////////////////////////////////////
				      //ACTUALIZO Y TOMO EL CONSECUTIVO Y EL CODIGO DEL CONCEPTO DE VENTA EN INVENTARIO
					  $q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
					  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					  if(isset($wdevol) and $wdevol=='on')
					    {
						  //Traigo el Concepto de Salida para las ventas
						  $q= " SELECT concod "
					         ."   FROM ".$wbasedato."_000008 "
					         ."  WHERE conmve = 'on' ";
					      $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					      $row = mysql_fetch_array($err);
						  $wconven=$row[0];

						  $q= " UPDATE ".$wbasedato."_000008 "
					         ."    SET concon = concon + 1 "
					         ."  WHERE concan = '".$wconven."'";          //Concepto de Salida
					      $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

						  //Busco el concepto de entrada (devolucion) basado en el concepto de salida para las ventas
						  $q= " SELECT concon, concod "
					         ."   FROM ".$wbasedato."_000008 "
					         ."  WHERE concan = '".$wconven."'";          //Concepto de Salida
					      $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					      $row = mysql_fetch_array($err);
						  $wnromvto=$row[0];
					      $wconmvto=$row[1];                              //Concepto de entrada (Devolucion)
						}
					    else
				            {
						    $q= " UPDATE ".$wbasedato."_000008 "
					           ."    SET concon = concon + 1 "
					           ."  WHERE conmve = 'on' ";
					        $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					        $q= " SELECT concon, concod "
					           ."   FROM ".$wbasedato."_000008 "
					           ."  WHERE conmve = 'on' ";
					        $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					        $row = mysql_fetch_array($err);
						    $wnromvto=$row[0];
					        $wconmvto=$row[1];                            //Concepto de Salida (Ventas)
				            }
				      $q = " UNLOCK TABLES";
					  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				    }
				    else
				        {
						 $wnromvto="";
						 $wconmvto="";
			            }

			      if(($wcantidad*$wvaltar) > 0 or $wdevol=="on")
			        {
			          if (!isset($wno2) or $wno2 == "")
			             $wno2=" ";

			          if (trim($wccogra) == "")
			             $wccogra=$wcco;

			          //SEPTIEMBRE 5  DE 2007:
			          if (!isset($waprovecha))
			             $waprovecha="off";

			          //FEBRERO 16 DE 2009:
			          if (!isset($wdevol) or $wconinv=="off")
			             $wdevol="off";

			          $q= " INSERT INTO ".$wbasedato."_000106 (   Medico       ,   Fecha_data ,   Hora_data,   tcarusu     ,   tcarhis       ,   tcaring  ,   tcarfec     ,   tcarsin ,   tcarres                 ,   tcarno1 ,   tcarno2  ,   tcarap1 ,   tcarap2 ,   tcardoc ,   tcarser    ,   tcarconcod ,   tcarconnom ,   tcarprocod ,   tcarpronom ,   tcartercod ,   tcarternom ,   tcarterpor ,   tcarcan      ,   tcarvun    ,   tcarvto                      ,   tcarrec                ,   tcarfac                    ,   tcartfa    ,tcarest,   tcarnmo     ,   tcarcmo     ,    tcarapr       ,   tcardev   , Seguridad) "
				         ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$whistoria."' ,'".$wing."' ,'".$wfeccar."' ,'".$wser."','".$wcodemp."-".$wnomemp."','".$wno1."','".$wno2."' ,'".$wap1."','".$wap2."','".$wdoc."','".$wccogra."','".$wcodcon."','".$wnomcon."','".$wprocod."','".$wpronom."','".$wcodter."','".$wnomter."','".$wporter."','".$wcantidad."','".$wvaltar."','".round($wcantidad*$wvaltar)."','".strtoupper($wrecexc)."','".strtoupper($wfacturable)."','".$wtipfac."','on'   ,'".$wnromvto."','".$wconmvto."', '".$waprovecha."','".$wdevol."', 'C-".$wusuario."')";
				      $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			        }

			         $wid=mysql_insert_id();   //Esta función devuelve el id despues de un insert, siempre y cuando el campo sea de autoincremento

			      //////////////////////////////////////////////////////////////////////////////////////
			      //SI ES UN CARGO CORRESPONDIENTE A UN ** PAQUETE ** ACA GRABO EL MOVIMIENTO DE PAQUETE
			      //////////////////////////////////////////////////////////////////////////////////////
			      if(isset($wpaquete) and $wpaquete=="on")
			        {
				      $q= " INSERT INTO ".$wbasedato."_000115 (   Medico       ,   Fecha_data,   Hora_data,   movpaqhis  ,   movpaqing,    movpaqcod ,  movpaqreg,   movpaqcon  , movpaqest, Seguridad        ) "
				         ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,".$whistoria.",".$wing."   ,'".$wcodpaq."',".$wid."   ,'".$wcodcon."', 'on'     , 'C-".$wusuario."')";
				      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			        }


			      /////////////////////////////////////////////////////////////////////////////////
			      //ACA DESCARGO DEL INVENTARIO ===================================================
			      /////////////////////////////////////////////////////////////////////////////////
			      											 //SEPTIEMBRE 5  DE 2007:
			      											 //
			      if(isset($wconinv) and $wconinv=="on" and (!isset($waprovecha) or $waprovecha=="off"))
			        {
				      //===================================================================================================================================
				      //===================================================================================================================================
				      //GRABO EN LA TABLA DEL -- <ENCABEZADO> -- DEL **** MOVIMIENTO DE INVENTARIOS ****
				      $q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     , mendan, menpre, mennit, menusu, menfac, menest, Seguridad        ) "
				         ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvto."','".$wconmvto."','".$wccogra."','".$wccogra."', '.'   , 0     , '0'   , '.'   , '.'   ,   'on', 'C-".$wusuario."')";
				      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


				      //===================================================================================================================================
			          //===================================================================================================================================
			          //GRABO EN LA TABLA DEL -- <DETALLE> -- DE  **** MOVIMIENTO DE INVENTARIOS ****
			          //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
					  //============================================================================================================================

			          //=========================================
			          //TRAIGO EL COSTO PROMEDIO DEL ARTICULO
			          $q= "SELECT karpro "
			             ."  FROM ".$wbasedato."_000007 "
				         ." WHERE karcco = '".$wccogra."'"
				         ."   AND karcod = '".$wprocod."'";
				         //."   AND karexi > 0 ";
				      $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				      $row_cos = mysql_fetch_array($res2);

				      if ($row_cos[0] == "")
				         $row_cos[0]=0;

				      //=========================================
			          //GRABO EL DETALLE DEL ARTICULO
			          $q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan      ,  mdevto                    , mdepiv , mdeest, Seguridad        ) "
			             ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconmvto."','".$wnromvto."','".$wprocod."',".$wcantidad.",".($wcantidad*$row_cos[0]).", '0'    , 'on'  , 'C-".$wusuario."')";
			          $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				//DEVOLUCION EN CASO DE ESTAR ACTIVA LA FUNCION trasladoautomatico
				if(isset($wdevol) and $wdevol=="on" and $wauto =='on' or $wexidev > 0)
					  {

					  $q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
					  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					  //Traigo el Concepto de translado
					   $q= " SELECT concod "
						 ."   FROM ".$wbasedato."_000008 "
						 ."  WHERE concod = ".$wconceptraslado." ";
					  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					  $row = mysql_fetch_array($err);
					  $wcont=$row[0];

					  $q= " UPDATE ".$wbasedato."_000008 "
						 ."    SET concon = concon + 1 "
						 ."  WHERE concod = '".$wcont."'";          //Concepto de Salida
					  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					   //Busco el concepto de entrada (devolucion) basado en el concepto de salida para las ventas
					  $q= " SELECT concon, concod "
						 ."   FROM ".$wbasedato."_000008 "
						 ."  WHERE concod = '".$wcont."'";          //Concepto de Salida
					  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					  $row = mysql_fetch_array($err);
					  $wnromvto=$row[0];
					  $wconmvto=$row[1];                              //Concepto de entrada (Devolucion)

					  $q = " UNLOCK TABLES";
					  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	                  //=============================================================================================================
	                  //Recalculo el costo promedio del articulo en el centro de costos destino (Bodega)

	                  // Consulto que hay en el centro de costos origen (1270)
	                    $q= " SELECT karexi, karpro "
	                        ."  FROM ".$wbasedato."_000007 "
	                        ." WHERE karcco = '".$wccogra."'"
	                        ."   AND karcod = '".$wprocod."'";
	                    $res_cos = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	                    $row_cos = mysql_fetch_array($res_cos);
	                    $wexist_actuales_origen = $row_cos[0];
	                    $wcosto_pro_actual_origen = $row_cos[1];

	                    //Consulto que hay en el centro de costos destino (Bodega)
	                    $q7= "SELECT karexi, karpro "
	                        ."  FROM ".$wbasedato."_000007 "
	                        ." WHERE karcco = '".$wbodega."'"
	                        ."   AND karcod = '".$wprocod."'";
	                    $res_cos = mysql_query($q7,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q7." - ".mysql_error());
	                    $row_cos = mysql_fetch_array($res_cos);
	                    $wexist_actuales_destino = $row_cos[0];
	                    $wcosto_pro_actual_destino = $row_cos[1];

	                    //Nuevo costo promedio del articulo en el centro de costos destino (Bodega)
	                    $wnuevocospro = (($wexist_actuales_destino * $wcosto_pro_actual_destino) + ($wcantidad * $wcosto_pro_actual_origen))/($wexist_actuales_destino + $wcantidad);
	                    $wnuevocospro = round($wnuevocospro,4);

	                    $q1= " UPDATE ".$wbasedato."_000007 "
	                        ."    SET Karpro = ".$wnuevocospro." "
	                        ."  WHERE Karcco = '".$wbodega."'"
	                        ."    AND Karcod = '".$wprocod."'";
	                    $err = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	                  //Se actualizan las existencias del centro de costos destino, en este caso la bodega.
					  $q1= " UPDATE ".$wbasedato."_000007 "
						   ."    SET karexi = karexi - ".($wcantidad*(-1))
						   ."  WHERE karcco = '".$wbodega."'"
						   ."    AND karcod = '".$wprocod."'";
					  $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());


					  //=============================================================================================================


					  $q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     , mendan, menpre, mennit, menusu, menfac, menest, Seguridad        ) "
													." VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvto."','".$wconmvto."','".$wccogra."','".$wbodega."', '.'   , 0     , '0'   , '.'   , '.'   ,   'on', 'C-".$wusuario."')";
					  $res = mysql_query($q,$conex) or die ("Error en la 10 para la funcion translado devolucion: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					  //GRABO EL DETALLE DEL ARTICULO
					  $q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan      ,  mdevto                    , mdepiv , mdeest, Seguridad        ) "
												 ."    VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconmvto."','".$wnromvto."','".$wprocod."',".$wcantidad.",".($wcantidad*$wcosto_pro_actual_origen).", '0'    , 'on'  , 'C-".$wusuario."')";
					  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


					  }

			        }

				  //**************************
			      //Aca grabo la auditoria
			      //**************************
			      $q= " INSERT INTO ".$wbasedato."_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis       ,   auding  ,   audreg  , audacc ,   audusu     , Seguridad) "
			         ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$whistoria."','".$wing."','".$wid."', 'Grabo','".$wusuario."', 'C-".$wusuario."')";
			      $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  	  }else{
		  	  	/*echo "<script>
					    alert ('NO HAY INVENTARIO EN $wnombrecco PARA ESTE PRODUCTO');
			          </script>";*/
				//return false;
		  	  }

			  $wcodcon    ="";
		      $wnomcon    ="";
		      $wccogra    ="";
		      $wprocod    ="";
		      $wpronom    ="";
		      $wcodter    ="";
		      $wporter    ="";
		      $wcantidad  ="";
		      $wrecexc    ="";
		      $wfacturable="";

		      unset($wcodcon);
			  unset($wnomcon);
			  unset($wccogra);
			  unset($wprocod);
			  unset($wpronom);
			  unset($wcodter);
			  unset($wnomter);
			  unset($wporter);
			  unset($wcantidad);
			  unset($wvaltar);
			  unset($wrecexc);
			  unset($wfacturable);
			  unset($wgrabar);
			  unset($wdevol);
			  unset($waprovecha);   //SEPTIEMBRE 5  DE 2007:

			  ?>
			    <script>
			        function ira(){document.cargos.wcodcon.focus();}
			    </script>
			  <?php
		     }
		    else
		        {
			    //FEBRERO 18 DE 2009: la parte THEN del if
			     if(isset($wdevol) and $wdevol=="on" and $wexidev==0)    //Esto lo hago para sacar el mensaje adecuado segun el tipo de concepto y si tiene saldo en la historia
			        {
				    ?>
				     <script>
				       alert ("LA HISTORIA NO TIENE CARGADA LA CANTIDAD DIGITADA");
		               function ira(){document.cargos.wcantidad.focus();}
				     </script>
				    <?php
				    }
			      else
			        {
					   ?>
					    <script>
					      alert ("LA CANTIDAD DIGITADA ES MAYOR A LA EXISTENTE EN EL INVENTARIO");
			              function ira(){document.cargos.wcantidad.focus();}
					    </script>
					   <?php

					    echo "Cantidad Existente: ".$wexiste."<br>";
		            }
			    }
		}
	}

  echo "<tr>";

  if (isset($whis))
    {
      $whistoria=$whis;
      echo "<input type='HIDDEN' name='whistoria' value='".$whistoria."'>";
    }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //HISTORIA CLINICA
  //*************************************************************************************************************************************************************************************************************************************************************************************
  if(isset($whistoria)) //Si ya fue digitado el documento del cliente
    {

	  if($whistoria != "" and strpos($whistoria,"-")==0)  //Por si digitan la historia junto con el número de ingreso, no deje grabar
        {
         $q= " SELECT MAX(ingnin+0) "
	         ."  FROM ".$wbasedato."_000100, ".$wbasedato."_000101 "
	         ." WHERE pachis = '".$whistoria."'"
	         ."   AND pachis = inghis ";
	      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $num1 = mysql_num_rows($res1);
	      $row1 = mysql_fetch_array($res1);

	      if (!isset($wing) or ($wing==""))
	         $wwing=$row1[0];
	        else
	          $wwing=$wing;

	      if(isset($wwing) and $wwing != "")
	        {
		      $q= "SELECT pachis,  pacno1, pacno2, pacap1, pacap2, pacdoc, ingcem, ingent, ingfei, ingsei, ingnin, ingtar, pactam "
		         ."  FROM ".$wbasedato."_000100, ".$wbasedato."_000101 "
		         ." WHERE pachis = '".$whistoria."'"
		         ."   AND pachis = inghis "
		         ."   AND ingnin = ".$wwing;
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $num1 = mysql_num_rows($res1);

		      if($num1 > 0)
		        {
			      $row1 = mysql_fetch_array($res1);
		          $whis=$row1[0];
		          if (!isset($wing) or ($wing == ""))
		             $wing=$row1[10];
		          $wno1=$row1[1];
		          $wno2=$row1[2];
		          $wap1=$row1[3];
		          $wap2=$row1[4];
		          $wdoc=$row1[5];
		          $wcodemp=$row1[6]; //Este es el Codigo de la empresa no el NIT
		          $wnomemp=$row1[7];
		          $wfecing=$row1[8];
		          $wser=$row1[9];
		          $wtar=$row1[11];
                  $wpactam=$row1[12]; //Tipo de servicio
		          if (!isset($wfeccar))
		             $wfeccar=$wfecha;

				  ?>
				    <script>
				      function ira(){document.cargos.wcodcon.focus();}
				    </script>
				  <?php
		        }
		    }
            else
                {
		        $whis="";
		        $wing="";
		        $wno1="";
		        $wno2="";
		        $wap1="";
		        $wap2="";
		        $wdoc="";
		        $wcodemp="";
		        $wnomemp="";
		        $wfecing="";
		        $wser="";
		        $wtar="";
	            }
	      echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
          echo "<input type='HIDDEN' name='wno1' value='".$wno1."'>";
          echo "<input type='HIDDEN' name='wno2' value='".$wno2."'>";
          echo "<input type='HIDDEN' name='wap1' value='".$wap1."'>";
          echo "<input type='HIDDEN' name='wap2' value='".$wap2."'>";
          echo "<input type='HIDDEN' name='wdoc' value='".$wdoc."'>";
          echo "<input type='HIDDEN' name='wcodemp' value='".$wcodemp."'>";
          echo "<input type='HIDDEN' name='wnomemp' value='".$wnomemp."'>";
          echo "<input type='HIDDEN' name='wfecing' value='".$wfecing."'>";
          echo "<input type='HIDDEN' name='wser' value='".$wser."'>";
	      echo "<td align=left class=".$wcf." colspan=1><b> Historia:<br></b><INPUT TYPE='text' NAME='whistoria' VALUE='".$whistoria."' onchange='enter()'></td>";   //whistoria
	     }
        else
           echo "<td align=left class=".$wcf." colspan=1><b> Historia:<br></b><INPUT TYPE='text' NAME='whistoria' VALUE='".$whistoria."' onchange='enter()'></td>";  //whistoria
     }
    else
       echo "<td align=left class=".$wcf." colspan=1><b> Historia:<br></b><INPUT TYPE='text' NAME='whistoria' onchange='enter()'></td>";                           //whistoria

  //*************************************************************************************************************************************************************************************************************************************************************************************

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //INGRESO NRO:
  if(isset($wing))
    {
	  $q = " SELECT count(*) "
	      ."   FROM ".$wbasedato."_000101 "
	      ."  WHERE inghis = '".$whistoria."'"
	      ."    AND ingnin = '".$wing."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      if($row[0] == 0)
        {
	      ?>
		    <script>
		      alert ("LA HISTORIA CON ESTE NUMERO DE INGRESO NO EXISTE");
              function ira(){document.cargos.wing.focus();}
		    </script>
		  <?php

		 echo "<td align=left class=".$wcf."><b>Ingreso Nro:<br><INPUT TYPE='text' NAME='wing' value='' onchange='enter()'></b></td>";
        }
        else
         echo "<td align=left class=".$wcf."><b>Ingreso Nro:<br><INPUT TYPE='text' NAME='wing' value='".$wing."' onchange='enter()'></b></td>";
    }
    else
       echo "<td align=left class=".$wcf." colspan=1><b> Ingreso Nro:<br></b><INPUT TYPE='text' NAME='wing' onchange='enter()'></td>";                           //wingreso


  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA_ING:
  if (isset($wfecing))
     echo "<td align=left class=".$wcf." colspan=1><b>Fecha Ing:<br>".$wfecing."</b></td>";
    else
       echo "<td align=left class=".$wcf." colspan=1><b>Fecha Ing:<br></b>&nbsp</td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA DE LOS CARGOS:
  if (isset($wfeccar))
     if ($wfeccar > $wfecha)
        {
         echo "<td align=left class=".$wcf."><b>Fecha:<br><INPUT TYPE='text' NAME='wfeccar' value='".$wfecha."' onblur='enter()'></b></td>";
         ?>
		    <script>
		      alert ("LA FECHA DE LOS CARGOS NO PUEDE MAYOR A LA FECHA ACTUAL");
              function ira(){document.cargos.wfeccar.focus();}
		    </script>
		  <?php
	    }
       else
          echo "<td align=left class=".$wcf."><b>Fecha:<br><INPUT TYPE='text' NAME='wfeccar' value='".$wfeccar."' onblur='enter()'></b></td>";
    else
       echo "<td align=left class=".$wcf."><b>Fecha:<br><INPUT TYPE='text' NAME='wfeccar' value='".$wfecha."' onchange='enter()'></b></td>";


  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Servicio de Ingreso
  if (isset($wser))
     {
	  $q = "SELECT ccodes "
          ."  FROM ".$wbasedato."_000003 "
          ." WHERE ccocod = '".$wser."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      echo "<td align=left class=".$wcf." colspan=1><b>Servicio de Ingreso:<br>".$row[0]."</b></td>";
     }
    else
       echo "<td align=left class=".$wcf." colspan=1><b>Servicio de Ingreso:<br></b>&nbsp</td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //RESPONSABLE:
  if (isset($wcodemp) && isset($wtar))
     {
      $wnomempAux =   ( $wcodemp == consultarAliasPorAplicacion($conex, $wemp_pmla, "codigoempresaparticular") ) ? "PARTICULAR" : $wnomemp;
       //TIPO DE SERVICIO
     $wpactam1 = explode("-", $wpactam);
     $wpactam_cod = $wpactam1[0];
     $query = "SELECT Selcod, Seldes  from ".$wbasedato."_000105 where Selcod='".$wpactam_cod."' and Selest='on'";
     $errq = mysql_query($query,$conex) or die(mysql_errno()."error en el query - ".$query." ".mysql_error());
     $row_tam = mysql_fetch_array($errq);
     //Si no tiene tipo de servicio, no se imprime.
     if(trim($wpactam_cod)!='')
     {
         $tipo_servicio = "<br><b><p style='background-color:#FFFFCC;'>Tipo de servicio: ".$row_tam['Seldes']."</b></p>";
     }

      echo "<td align=left style='width:250px;' class=".$wcf." colspan=1 rowspan=2><b>Responsable:<br>".$wcodemp." - ".$wnomempAux."</b><br><br>";
      $q = "SELECT tarcod, tardes "
          ."  FROM ".$wbasedato."_000025 "
          ." WHERE tarcod = '".$wtar."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
	  echo "<b>Tarifa :".$row[0]."-".$row[1]."</b>".$tipo_servicio."</td>";
      echo "</tr>";
     }
    else
       echo "<td align=left class=".$wcf." colspan=1><b>Responsable:<br></b>&nbsp</td>";



  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //PACIENTE:
  if (isset($wno1))
     echo "<td align=left class=".$wcf." colspan=2><b>Paciente:<br>".$wno1."&nbsp;".$wno2."&nbsp;".$wap1."&nbsp;".$wap2."&nbsp;"."</b></td>";
    else
       echo "<td align=left class=".$wcf." colspan=2><b>Paciente:<br></b>&nbsp</td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //DOCUMENTO:
  if (isset($wdoc))
     echo "<td align=left class=".$wcf." colspan=1><b>Documento:<br>".$wdoc."</b></td>";
    else
       echo "<td align=left class=".$wcf." colspan=1><b>Documento:<br></b>&nbsp</td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //SERVICIO ACTUAL:
  if (isset($wservicio))
     echo "<td align=left class=".$wcf." colspan=2><b>Servicio:<br>".$wservicio."</b></td>";
    else
       echo "<td align=left class=".$wcf." colspan=2><b>Servicio:<br>".$wnomcco."</b></td>";

  echo "</tr>";

  if( isset( $whistoria ) && !empty( $whistoria ) && isset( $wing ) && !empty( $wing ) ){
	  $url = "./Cargos_ips.php?wemp_pmla=".$wemp_pmla."&historia=".$whistoria."&ingreso=".$wing."&accion=imprimirSticker";
	  echo "<tr style='text-align:center;'>";
	  echo "<td colspan=6>";
	  echo "<a href=#null onclick=\"abrirVentana('".$url."');\">Imprimir sticker</a>";
	  echo "</td>";
	  echo "</tr>";
  }

  echo "</table>";

  echo "<br>";

  //====================================================================================================================================================================\\
  //BARRA DE CARGOS ====================================================================================================================================================\\
  //====================================================================================================================================================================\\
  // Consulta si en root_51 si hay restricción para modificar solo último y penúltimo ingreso
  $restringe = restringirModificacion($wbasedato, $wemp_pmla, $conex, $whistoria, $wing);

  // si existe parameto en root_51, y si es ultimo o penultimo ingreso
  $css = '';
  if($restringe)
  {
      $css = 'style="display:none;"';
  }
  echo "<div ".$css."><center><table border='0'>";
  echo "<tr>";
  echo "<td align=center colspan=15 class=encabezadoTabla><b>I N G R E S O &nbsp&nbsp&nbsp D E &nbsp&nbsp&nbsp C A R G O S</b></td>";
  echo "</tr>";


  if (!isset($wpaquete) and !isset($wdevol))
     {
	  echo "<tr>";
      echo "<td class=".$wcf2." colspan=13 align=left><b>Paquete</b><input type='CHECKBOX' name='wpaquete' SIZE=2 onclick='enter()'> &nbsp;&nbsp;&nbsp;&nbsp; <b>Devolucion</b><input type='CHECKBOX' name='wdevol' SIZE=2 onclick='enter()'></td>";
     }
    else
       if (isset($wdevol))
          echo "<td class=".$wcf2." colspan=12 align=left><b>Devolucion</b><input type='CHECKBOX' name='wdevol' SIZE=2 CHECKED onclick='enter()'></td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //** PAQUETE **INDICA SI ES UN PAQUETE
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (isset($wpaquete) and $wpaquete=="on")
     if ($wpaquete=="on")
        {
	     echo "<tr>";
         echo "<td class=".$wcf2." colspan=1 align=center><b>Paquete</b><input type='CHECKBOX' name='wpaquete' SIZE=2 CHECKED onclick='enter()'></td>";
        }
    ///else
       ///echo "<td class=".$wcf2." colspan=1 align=center><b>Paquete</b><input type='CHECKBOX' name='wpaquet' SIZE=2 onclick='enter()'></td>";

  if (isset($wpaquete) and $wpaquete=="on")
     {
	   if (isset($wcodpaq) and ($wcodpaq != ""))
	     {
		  $q =  " SELECT paqcod, paqnom "
		       ."   FROM ".$wbasedato."_000113 "
		       ."  WHERE paqest  = 'on' "
		       ."    AND paqcod  = '".$wcodpaq."'"
		       ."  ORDER BY paqnom ";

	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
	      if ($num > 0)
	         {
		      $row = mysql_fetch_array($res);
		      $wcodpaq = $row[0];   //Codigo del paquete
		      $wnompaq = $row[1];   //Nombre del paquete
		      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodpaq' VALUE='".$wcodpaq."' size = 20 ></td>";                    //wcodpaq
		      //$id="MostrarPaquetes('".$wcodpaq."','".$wbasedato."')";  //Para mostra el Grid de los paquetes
              //echo "<td class=encabezadoTablaalign=center><IMG SRC='/matrix/images/medical/root/BINOCS.ICO' OnClick=".$id."></td>";
			  echo "<td align=left class=".$wcf." colspan=11><INPUT TYPE='text' NAME='wnompaq' VALUE='".$wnompaq."' size = 110 onchange='enter()' ondblclick='enter()' ></td>";   //wnompaq

		      ?>
			    <script>
			      function ira(){document.cargos.wcodcon.focus();}
			    </script>
			  <?php

	         }
	        else
	           {
		        echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodpaq' size = 20 onchange='enter()'></td>";                                         //wcodpaq
			    echo "<td align=left class=".$wcf." colspan=11><INPUT TYPE='text' NAME='wnompaq' size = 120 onchange='enter()' ondblclick='enter()' ></td>";                   //wnompaq
	           }
	     }
	    else
	       {
	        if (isset($wnompaq) and ($wnompaq != ""))
			   {
				$wnompaq=str_replace(" ","%",$wnompaq);
			    $q =  " SELECT paqcod, paqnom "
			         ."   FROM ".$wbasedato."_000113 "
			         ."  WHERE paqest = 'on' "
			         ."    AND paqnom like '%".$wnompaq."%'"
			         ."  ORDER BY paqnom ";
			    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

			    if ($num > 0)
			       {
				    for ($i=1;$i<=$num;$i++)
				       {
					    $row = mysql_fetch_array($res);
				        if ($num == 1)
				           {
					        $wcodpaq=$row[0];
				            echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodpaq' value='".$wcodpaq."' size=20 onchange='enter()'></td>";
				           }
			              else
			                 {
				              if ($num > 1)           //Si entra por aca es porque el concepto tiene varios registros con el nombre muy similar
				                 $wcodpaq=$row[0];
			                 }
				        $wnompaq1[$i]=$row[1];
				       }

				    echo "<td align=left class=".$wcf." colspan=11><b></b><select name='wnompaq' onchange='enter()' ondblclick='enter()' >";
					for ($i=1;$i<=$num;$i++)
					   {
					    echo "<option>".$wnompaq1[$i]."</option>";
					    if ($num == 1)
					       $wnompaq=$wnompaq1[$i];
					   }
					echo "</select></td>";
				   }
	              else
	                 {
			 	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodpaq' size=20 onchange='enter()'></td>";                                    //wcodter
					  echo "<td align=left class=".$wcf." colspan=11><INPUT TYPE='text' NAME='wnompaq' size=120 onchange='enter()' ondblclick='enter()' ></td>";              //wnomter
				     }
		       }
		      else
		           {
			        ?>
				      <script>
				        function ira(){document.cargos.wcodpaq.focus();}
				      </script>
				    <?php

			        echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodpaq' size=20 onchange='enter()'></td>";                                    //wcodter
					echo "<td align=left class=".$wcf." colspan=11><INPUT TYPE='text' NAME='wnompaq' size=120 onchange='enter()' ondblclick='enter()' ></td>";              //wnomter
			       }
	       }
     }

  echo "<tr>";
  ///if (!isset($wpaquete))
  ///   echo "<th colspan=1 class=encabezadoTabla>&nbsp</th>";
  echo "<th colspan=2 class=encabezadoTabla>Concepto o Grupo:</th>";
  echo "<th colspan=1 class=encabezadoTabla>CCosto:</th>";
  echo "<th colspan=2 class=encabezadoTabla>Procedimiento o Insumo:</th>";
  echo "<th colspan=2 class=encabezadoTabla>Tercero:</th>";
  if (isset($wconinv) and $wconinv=="on") echo "<th colspan=1 class=encabezadoTabla>Aprove<br>chamiento</th>";  //SEPTIEMBRE 5  DE 2007:
  echo "<th colspan=1 class=encabezadoTabla>Cantidad:</th>";
  echo "<th colspan=1 class=encabezadoTabla>Valor Unit.:</th>";
  echo "<th colspan=1 class=encabezadoTabla>Valor Total:</th>";
  echo "<th colspan=1 class=encabezadoTabla>Rec/Exc:</th>";
  echo "<th colspan=1 class=encabezadoTabla>Fact.(S/N):</th>";
  echo "</tr>";


  ///if (!isset($wpaquete) and !isset($wdevol))
  ///   echo "<td class=".$wcf2." colspan=1 align=center><b>Paquete</b><input type='CHECKBOX' name='wpaquete' SIZE=2 onclick='enter()'></td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CONCEPTO O GRUPO
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (isset($wcodcon) and ($wcodcon != ""))
     {
	//21 de Marzo de 2012
	//Esta validacion permite identificar si el usuario puede grabar conceptos

	if ($wbod == 'on')
		{

		  $q =  " SELECT Grucod, Gruinv, Gruest"
			   ."   FROM ".$wbasedato."_000004"
			   ."  WHERE gruest = 'on' "
			   ."    AND grucod = '".$wcodcon."'"
			   ."    AND gruinv = 'on'";
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
		 $wccogra = $wcco;
		}
	else
		{
		$num=1;
		}
	if($num > 0)
		{
	  if (!isset($wpaquete) or $wpaquete != "on")
		  $q =  " SELECT grucod, grudes, gruarc, gruser, grutip, grumva, gruinv, gruabo, grutab, grumca "
		       ."   FROM ".$wbasedato."_000004 "
		       ."  WHERE gruest = 'on' "
		       ."    AND grucod = '".$wcodcon."'"
		       ."    AND gruser in ('A','H') "  //Solo trae los servicios de ambos (Pos y Hospitalario) u Hospitalario
		       ."  ORDER BY grudes ";
		 else
	        $q =  " SELECT  grucod, grudes, gruarc, gruser, grutip, grumva, gruinv, gruabo, grutab, grumca, "
		         ."         paqdetpro, pronom, paqdetvac, paqdetfec, paqdetvan "
	             ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000114, ".$wbasedato."_000103 "
		         ."  WHERE  gruest = 'on' "
		         ."    AND  grucod = '".$wcodcon."'"
		         ."    AND  gruser in ('A','H') "  //Solo trae los servicios de ambos (Pos y Hospitario) u Hospitario
		         ."    AND  grucod = paqdetcon "
		         ."    AND  paqdetcod = '".$wcodpaq."'"
		         ."    AND  paqdetest = 'on' "
		         //."    AND  (TRIM(mid(paqdettar,1,instr(paqdettar,'-')-1)) = '".$wtar."'"
		         //."     OR   TRIM(mid(paqdettar,1,instr(paqdettar,'-')-1)) = '*') "
		         ."    AND  (TRIM(paqdettar) = '".$wtar."'"
		         ."     OR   TRIM(paqdettar) = '*') "
		         ."    AND  paqdetpro = procod "
		         ."  ORDER  BY grudes ";

	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

      if ($num > 0)
         {
	      $row = mysql_fetch_array($res);
	      $wcodcon = $row[0];   //Codigo del concepto
	      $wnomcon = $row[1];   //Nombre del concepto
	      $warctar = $row[2];   //Archivo para validar las tarifas
	      $wconser = $row[3];   //Tipo de servicio (P)OS, (H)OSPITALARIO o (A)MBOS
	      $wcontip = $row[4];   //Tipo de concepto (P)ropio o (C)ompartido
	      $wconmva = $row[5];   //Indica si el valor se puede colocar al momento de grabar el cargo
	      $wconinv = $row[6];   //Indica si mueve inventarios
	      $wconabo = $row[7];   //indica si es un concepto de abono
	      $wcontab = $row[8];   //Tipo de Abono
	      $wconmca = $row[9];   //Indica si el concepto mueve caja

	      if (isset($wpaquete) and $wpaquete == "on")
	         {
		      $wprocod = $row[10];
		      $wpronom = $row[11];
		      $wpvac   = $row[12];
		      $wpfec   = $row[13];
		      $wpvan   = $row[14];

		      //if ($wfecha < $wpfec)   //Aca evaluo si tomo el valor anterior o el actual
		      if ($wfeccar < $wpfec)    //Aca evaluo si tomo el valor anterior o el actual
			     $wvaltar = $wpvan;
			    else
			       $wvaltar = $wpvac;

			  echo "<input type='HIDDEN' name='wprocod' value='".$wprocod."'>";
		      echo "<input type='HIDDEN' name='wpronom' value='".$wpronom."'>";
		      echo "<input type='HIDDEN' name='wvaltar' value='".$wvaltar."'>";
		     }

	      echo "<input type='HIDDEN' name='wconser' value='".$wconser."'>";
	      echo "<input type='HIDDEN' name='wcontip' value='".$wcontip."'>";
	      echo "<input type='HIDDEN' name='wconmva' value='".$wconmva."'>";
	      echo "<input type='HIDDEN' name='wconinv' value='".$wconinv."'>";
	      echo "<input type='HIDDEN' name='wconabo' value='".$wconabo."'>";
	      echo "<input type='HIDDEN' name='wcontab' value='".$wcontab."'>";
	      echo "<input type='HIDDEN' name='wconmca' value='".$wconmca."'>";


	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodcon' VALUE='".$wcodcon."' size = 6 onchange='enter()' ></td>";                                  //wcodcon
	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomcon' VALUE='".$wnomcon."' size = 20 onchange='enter()' ondblclick='enter()'></td>";   //wnomcon

	      ?>
		    <script>
		      function ira(){document.cargos.wprocod.focus();}
		    </script>
		  <?php
		 }
        else
           {
	        echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodcon' size = 6  onchange='enter()'></td>";                                                     //wcodcon
		    echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomcon' size = 20 onchange='enter()' ondblclick='enter()'></td>";                      //wnomcon
           }
     }
  	else
	{
			 echo "<script> alert('El usuario no puede grabar para este concepto');
						   function ira(){document.cargos.wcodcon.focus();}
				  </script>";

			}

	}
    else
       {
	    if (isset($wnomcon) and ($wnomcon != ""))
		   {
			if (!isset($wpaquete) or $wpaquete != "on")
			   {
				$wnomcon=str_replace(" ","%",$wnomcon);
			    $q =  " SELECT grucod, grudes, gruarc "
			         ."   FROM ".$wbasedato."_000004 "
			         ."  WHERE gruest = 'on' "
			         ."    AND grudes like '%".$wnomcon."%'"
			         ."    AND gruser in ('A','H') "
			         ."  ORDER BY grudes ";
		       }
		      else
		         $q =  " SELECT grucod, grudes, gruarc "
			          ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000114 "
			          ."  WHERE gruest = 'on' "
			          ."    AND grudes like '%".$wnomcon."%'"
			          ."    AND gruser in ('A','H') "  //Solo trae los servicios de ambos (Pos y Hospitario) u Hospitario
			          ."    AND grucod = paqdetcon "
			          ."    AND paqdetcod = '".$wcodpaq."'"
			          ."    AND paqdetest = 'on' "
			          ."  ORDER BY grudes ";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

		    $sw=0;
		    if ($num > 0)
		       {
			    for ($i=1;$i<=$num;$i++)
			       {
				    $row = mysql_fetch_array($res);
			        if ($num == 1)
			           {
				        $wcodcon=$row[0];
			            echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodcon' value='".$wcodcon."' size=6  onchange='enter()' ></td>";
			            echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomcon' value='".$wnomcon."' size=20 onchange='enter()' ondblclick='enter()'></td>";
			            $sw=1;

			            $q =  " SELECT grucod, grudes, gruarc, gruser, grutip, grumva, gruinv, gruabo, grutab, grumca "
						     ."   FROM ".$wbasedato."_000004 "
						     ."  WHERE gruest = 'on' "
						     ."    AND grucod = '".$wcodcon."'"
						     ."    AND gruser in ('A','H') "  //Solo trae los servicios de ambos (Pos y Hospitario) u Hospitario
						     ."  ORDER BY grudes ";
						$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());

						$row = mysql_fetch_array($res);
						$wcodcon = $row[0];   //Codigo del concepto
						$wnomcon = $row[1];   //Nombre del concepto
						$warctar = $row[2];   //Archivo para validar las tarifas
						$wconser = $row[3];   //Tipo de servicio (P)OS, (H)OSPITALARIO o (A)MBOS
						$wcontip = $row[4];   //Tipo de concepto (P)ropio o (C)ompartido
						$wconmva = $row[5];   //Indica si el valor se puede colocar al momento de grabar el cargo
						$wconinv = $row[6];   //Indica si mueve inventarios
						$wconabo = $row[7];   //indica si es un concepto de abono
						$wcontab = $row[8];   //Tipo de Abono
						$wconmca = $row[9];   //indica si el concepto mueve caja

						echo "<input type='HIDDEN' name='wcodcon' value='".$wcodcon."'>";
	                    echo "<input type='HIDDEN' name='wnomcon' value='".$wnomcon."'>";
	                    echo "<input type='HIDDEN' name='warctar' value='".$warctar."'>";
	                    echo "<input type='HIDDEN' name='wconser' value='".$wconser."'>";
	                    echo "<input type='HIDDEN' name='wcontip' value='".$wcontip."'>";
	                    echo "<input type='HIDDEN' name='wconmva' value='".$wconmva."'>";
	                    echo "<input type='HIDDEN' name='wconinv' value='".$wconinv."'>";
	                    echo "<input type='HIDDEN' name='wconabo' value='".$wconabo."'>";
	                    echo "<input type='HIDDEN' name='wcontab' value='".$wcontab."'>";
	                    echo "<input type='HIDDEN' name='wconmca' value='".$wconmca."'>";

						?>
					      <script>
					       function ira(){document.cargos.wprocod.focus();}
					      </script>
					    <?php

			           }
		              else
		                 {
			              if ($num > 1)           //Si entra por aca es porque el concepto tiene varios registros con el nombre muy similar
			                 $wcodcon[$i]=$row[0];
			             }
			        $wnomcon1[$i]=$row[1];
			       }

			       if ($sw==0)
			          {
				       echo "<td align=left class=".$wcf." colspan=1><b></b><select name='wnomcon' onchange='enter()' ondblclick='enter()' >";

					   for ($i=1;$i<=$num;$i++)
					      {
					       echo "<option>".$wnomcon1[$i]."</option>";
					      }
					   echo "</select></td>";
				      }
			   }
              else
                 {
		 	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodcon' size=6 onchange='enter()'></td>";                        //wcodcon
				  echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomcon' size=20 onchange='enter()' ondblclick='enter()'></td>";    //wnomcon
			     }
		   }
	      else
	         {
		      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodcon' size=6 onchange='enter()'  value='' ></td>";                          //wcodcon
		      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomcon' size=20 onchange='enter()' value='' ondblclick='enter()'></td>";      //wnomcon
		      $wccogra="";
		      echo "<input type='HIDDEN' name='wccogra' value='".$wccogra."'>";
			 }
	   }

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CENTRO DE COSTO DE GRABACION:
  if (isset($wconabo) and $wconabo != "on")
     {
	  if (isset($wccogra) and trim($wccogra)!="")
	     {
		  if (isset($wcodcon) and isset($wcodemp) and $wcodcon != "" and $wcodemp != "")
		     {
			  $q = "  SELECT relconcco "
			       ."   FROM ".$wbasedato."_000077, ".$wbasedato."_000024 "
			       ."  WHERE relconcon = '".$wcodcon."'"
			       ."    AND relconest = 'on' "
			       ."    AND relcontem = mid(emptem,1,instr(emptem,'-')-1) "
			       ."    AND empcod    = '".$wcodemp."'";
			  $rescco = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $numcco = mysql_num_rows($rescco);

			  if ($numcco==0)
		         {
		          $q =  " SELECT relconcco "
			           ."   FROM ".$wbasedato."_000077 "
			           ."  WHERE relconcon = '".$wcodcon."'"
				       ."    AND relconest = 'on' "
				       ."    AND relcontem = '*'  ";
				  $rescco = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		          $numcco = mysql_num_rows($rescco);
	             }
			 }
		    else
		       $numcco=0;
		  echo "<td align=left class=".$wcf." colspan=1><b></b><select name='wccogra' onchange='enter()'>";

		  if ($numcco > 1)              //Si hay mas de un centro de costo, hago esto, si no muestra abajo el unico que hay
		     //============================================================================================================
		     //Agosto 3 de 2007
		     for ($i=1;$i<=$numcco;$i++)
		         {
			      $rowcco = mysql_fetch_array($rescco);
				//Valido si el usuario es una bodega y le asigno al centro de costos, el centro de costos del usuario. 26 Marzo
    				if ($wbod == 'on')
					{
					$rowcco[0] = $wcco;
					}
			      if ($wccogra == $rowcco[0] and $numcco>1)
			         echo "<option selected>".$rowcco[0]."</option>";    //Esto era lo unico que estaba antes de agosto 3 con $wccogra en vez $rowcco[0]
	                else
		               {
			            if ($numcco > 1 and (trim($wccogra)=="" or trim($wccogra)==" "))
			               echo "<option selected>&nbsp</option>";

		                echo "<option>".$rowcco[0]."</option>";
	                   }
		         }
	         //==========================================================================================================

	      //=============================================================================================================
		  for ($i=1;$i<=$numcco;$i++)
		     {
			  $rowcco = mysql_fetch_array($rescco);

		       //Valido si el usuario es una bodega y le asigno al centro de costos, el centro de costos del usuario. 26 Marzo
               //ademas agrega si el concepto seleccionado maneja inventario. 21 Junio 2013
			   if ($wbod == 'on' and $wconinv == 'on')
				{
				$rowcco[0] = $wcco;
				}
//

		      echo "<option>".$rowcco[0]."</option>";
	         }
		  echo "</select></td>";
	     }
	    else
	       {
		    if (isset($wcodcon) and isset($wcodemp) and $wcodcon != "" and $wcodemp != "")
			  {
		       $q =  " SELECT relconcco "
			        ."   FROM ".$wbasedato."_000077, ".$wbasedato."_000024 "
			        ."  WHERE relconcon                                = '".$wcodcon."'"
			        ."    AND relconest                                = 'on' "
			        ."    AND relcontem                                = mid(emptem,1,instr(emptem,'-')-1) "
			        ."    AND relcontem                                = mid(emptem,1,instr(emptem,'-')-1) "
			        ."    AND empcod                                   = '".$wcodemp."'";
			   $rescco = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		       $numcco = mysql_num_rows($rescco);

		       if ($numcco==0)
		          {
		           $q =  " SELECT relconcco "
			            ."   FROM ".$wbasedato."_000077 "
			            ."  WHERE relconcon = '".$wcodcon."'"
			            ."    AND relconest = 'on' "
			            ."    AND relcontem = '*'  ";
			       $rescco = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		           $numcco = mysql_num_rows($rescco);
	              }
	          }
		     else
		        $numcco=0;
		    echo "<td align=left class=".$wcf." colspan=1><b></b><select name='wccogra' onchange='enter()'>";

	        if ($numcco > 1)                  //Si hay mas de un centro de costo, muestro el campo en blanco por defecto
	           echo "<option selected>&nbsp</option>";

	        for ($i=1;$i<=$numcco;$i++)
		        {
			     $rowcco = mysql_fetch_array($rescco);

                 //Valido si el usuario es una bodega y le asigno al centro de costos, el centro de costos del usuario. 26 Marzo
                 //ademas agrega si el concepto seleccionado maneja inventario. 21 Junio 2013
                 if ($wbod == 'on' and $wconinv == 'on')
                    {
                    $rowcco[0] = $wcco;
                    }

		         echo "<option>".$rowcco[0]."</option>";
		        }
		    echo "</select></td>";
	       }
     }
    else
       echo "<td align=left class=".$wcf." colspan=1>&nbsp</td>";


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //PROCEDIMIENTO O INSUMO
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (isset($wprocod) and ($wprocod != "") and isset($warctar) and ($warctar != "" and ($wconabo == "off" or $wconabo == "")))
     {
	  //ACA SEGUN EL ARCHIVO DE VALIDACION DE TARIFAS SE TRAEN LOS PROCEDIMIENTOS
	  $q="";
	  switch ($warctar)
	    {
		 case "000026":  //Tarifas de medicamentos y material
	         {

		      //Modificacion hecha el 5 de Junio de 2008
		      //Con esto hago que si se ingreso el codigo del proveedor se busque en la homologacion y traiga el codigo de
		      //la clinica, si no es el codigo del proveedor sigue buscando con el codigo ingreso la tarifa y el saldo.
		      $q = " SELECT mid(axpart,1,instr(axpart,'-')-1) "
		          ."   FROM ".$wbasedato."_000009 "
		          ."  WHERE axpcpr = '".$wprocod."'"
		          ."    AND axpest = 'on' ";
		      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	          $num = mysql_num_rows($res);
	          if ($num > 0)
	             {
		          $row = mysql_fetch_array($res);
	              $wprocod = $row[0];
                 }

              //AVERIGUO SI EL ARTICULO MANEJA APROVECHAMIENTO
		      $q = " SELECT artapv "
		          ."   FROM ".$wbasedato."_000001 "
		          ."  WHERE artcod = '".$wprocod."'"
                  ."    AND artest = 'on' ";
              $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	          $num = mysql_num_rows($res);
	          $rowapv = mysql_fetch_array($res);

	          if ($rowapv[0] <> "on" or !isset($waprovecha))   //Si no maneja aprovechamiento
	             {
		          $q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec, karexi, 'C', '0' "
					   ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000007, ".$wbasedato."_000004 "
					   ."  WHERE artcod                            = '".$wprocod."'"
					   ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
					   ."    AND artest                            = 'on' "
					   //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
					   ."    AND arttip                            = grutia "                   //CAMBIO DE SEPTIEMBRE 5  DE 2007:
					   ."    AND grucod                            = '".$wcodcon."'"            //  "    "      "      "  "   "
					   ."    AND mtaest                            = 'on' "
					   //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
					   ."    AND karcco                            = '".$wccogra."'"
				       ."    AND karcod                            = artcod "
				       ."    AND karexi                            >= 0 "
				       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = '".$wtar."'"
					   ."  ORDER BY 2 ";
				 }
			    else                //Si el articulo maneja aprovechamiento no valido todavia la existencia de cantidades
			       {
		            $q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec, 0, 'C', '0' "
					     ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000004 "
					     ."  WHERE artcod                            = '".$wprocod."'"
		                 ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
					     ."    AND artest                            = 'on' "
					     //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
					     ."    AND arttip                            = grutia "                   //CAMBIO DE SEPTIEMBRE 5  DE 2007:
					     ."    AND grucod                            = '".$wcodcon."'"            //  "    "      "      "  "   "
					     ."    AND mtaest                            = 'on' "
					     //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
					     ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = '".$wtar."'"
					     ."  ORDER BY 2 ";
			       }
			       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	               $num = mysql_num_rows($res);

	               if ($num==0)   //Si no existe el articulo con la tarifa del paciente lo busco con la tarifa asterisco (*)
			          {
				       //On if ($rowapv[0] <> "on")   //Si no maneja aprovechamiento
				       if (!isset($waprovecha) or $waprovecha=="off")   //Si no maneja aprovechamiento
	             		  {
					       $q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec, karexi, 'C', '0' "
							    ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000007, ".$wbasedato."_000004 "
							    ."  WHERE artcod                            = '".$wprocod."'"
							    ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
							    ."    AND artest                            = 'on' "
							    //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
							    ."    AND arttip                            = grutia "           //CAMBIO DE SEPTIEMBRE 5  DE 2007:
					            ."    AND grucod                            = '".$wcodcon."'"    //  "    "      "      "  "   "
							    ."    AND mtaest                            = 'on' "
							    //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
							    ."    AND karcco                            = '".$wccogra."'"
						        ."    AND karcod                            = artcod "
						        ."    AND karexi                            >= 0 "
						        ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = '*' "
				                ."  ORDER BY 2 ";
				          }
			             else   //Si maneja aprovechamiento todavia no valido la existencia
			                {
					         $q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec, 0, 'C', '0' "
							      ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000004 "
							      ."  WHERE artcod                            = '".$wprocod."'"
							      ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
							      ."    AND artest                            = 'on' "
							      //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
						          ."    AND arttip                            = grutia "           //CAMBIO DE SEPTIEMBRE 5  DE 2007:
					              ."    AND grucod                            = '".$wcodcon."'"    //  "    "      "      "  "   "
							      ."    AND mtaest                            = 'on' "
							      //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
							      ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = '*' "
				                  ."  ORDER BY 2 ";
			                }
				      }
			 }
			 break;
		 case "000104":  //Tarifas de procedimientos y examenes
	         {
		      if (!isset($wpaquete) or $wpaquete != "on")
		         {
			      $q =  " SELECT proemptfa "
			           ."   FROM ".$wbasedato."_000070"
			           ."  WHERE proempcod = '".$wprocod."'"
			           ."    AND proempemp = '".$wcodemp."'"
			           ."    AND proempest = 'on' ";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	              $num = mysql_num_rows($res);

	              if ($num > 0)
			         {
				      //Por aca ingresa si el codigo esta relacionado con la empresa por la que viene el paciente
				      //y tiene prelación para tomar el valor a cobrar segun el tipo de liquidacion que diga en la
				      //tabla _000070 - Relacion procedimientos - empresas
				      $q =  " SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, 'CODIGO', propun "
						   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104, ".$wbasedato."_000070 "
						   ."  WHERE procod                                  = '".$wprocod."'"
						   ."    AND procod                                  = mid(tarcod,1,instr(tarcod,'-')-1) "
						   ."    AND proest                                  = 'on' "
						   ."    AND tarest                                  = 'on' "
						   ."    AND (mid(tarcon,1,instr(tarcon,'-')-1)      = '".$wcodcon."'  "
						   ."     OR  tarcon                                 = '".$wcodcon."') "
						   ."    AND (mid(tartar,1,instr(tartar,'-')-1)      = '".$wtar."'"
						   ."     OR  tartar                                 = '".$wtar."') "
						   ."    AND procod                                  = proempcod "
						   ."    AND proempemp                               = '".$wcodemp."'"
						   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = 'CODIGO'"

						   ." UNION "

					       ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, 'UVR', propun "
						   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104, ".$wbasedato."_000070 "
						   ."  WHERE procod                                  like '%".$wprocod."%'"
						   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = mid(tarcod,1,instr(tarcod,'-')-1) "
						   ."    AND proest                                  = 'on' "
						   ."    AND (mid(tarcon,1,instr(tarcon,'-')-1)      = '".$wcodcon."'  "
						   ."     OR  tarcon                                 = '".$wcodcon."') "
						   ."    AND (mid(tartar,1,instr(tartar,'-')-1)      = '".$wtar."'"
						   ."     OR  tartar                                 = '".$wtar."') "
					       ."    AND tarest                                  = 'on' "
					       ."    AND procod                                  = proempcod "
						   ."    AND proempemp                               = '".$wcodemp."'"
						   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = 'UVR'"
						   ."    AND propun                                 >= taruvi "
						   ."    AND propun                                 <= taruvf "

						   ." UNION "

						   //Modificacion JULIO 18 DE 2007
					       ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, 'UVR', propun "
						   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104, ".$wbasedato."_000070 "
						   ."  WHERE procod                                  LIKE '%".$wprocod."%'"
						   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = mid(tarcod,1,instr(tarcod,'-')-1) "
						   ."    AND proest                                  = 'on' "
						   ."    AND (mid(tarcon,1,instr(tarcon,'-')-1)      = '".$wcodcon."'  "
						   ."     OR  tarcon                                 = '".$wcodcon."') "
						   ."    AND (mid(tartar,1,instr(tartar,'-')-1)      = '".$wtar."'"
						   ."     OR  tartar                                 = '".$wtar."') "
					       ."    AND tarest                                  = 'on' "
					       ."    AND procod                                  = proempcod "
						   ."    AND proempemp                               = '".$wcodemp."'"
						   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = 'UVR'"
						   ."    AND taruvi                                  = 0 "
						   ."    AND taruvf                                  = 0 "
						   //=============================

						   ." UNION "

					       ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, 'GQX', propun "
						   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104, ".$wbasedato."_000070 "
						   ."  WHERE procod                                  LIKE '%".$wprocod."%'"
						   ."    AND mid(progqx,1,instr(progqx,'-')-1)       = mid(tarcod,1,instr(tarcod,'-')-1) "
						   ."    AND proest                                  = 'on' "
						   ."    AND (mid(tarcon,1,instr(tarcon,'-')-1)      = '".$wcodcon."'  "
						   ."     OR  tarcon                                 = '".$wcodcon."') "
						   ."    AND (mid(tartar,1,instr(tartar,'-')-1)      = '".$wtar."'"
						   ."     OR  tartar                                 = '".$wtar."') "
					       ."    AND tarest                                  = 'on' "
					       ."    AND procod                                  = proempcod "
						   ."    AND proempemp                               = '".$wcodemp."'"
						   ."    AND mid(proemptfa,1,instr(proemptfa,'-')-1) = 'GQX'"
					       ."  ORDER BY 2 ";
					 }
			        else
			           {
				        $q =  " SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, protfa, propun "
						     ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
						     ."  WHERE procod                             = '".$wprocod."'"
	                         ."    AND protfa                             = 'CODIGO' "
						     ."    AND procod                             = mid(tarcod,1,instr(tarcod,'-')-1) "
						     ."    AND proest                             = 'on' "
						     ."    AND tarest                             = 'on' "
						     ."    AND (mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'  "
							 ."     OR  tarcon                            = '".$wcodcon."') "
						     ."    AND (mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
							 ."     OR  tartar                            = '".$wtar."') "

					         ." UNION "

					         ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, protfa, 'rango' "
						     ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
						     ."  WHERE procod                             LIKE '%".$wprocod."%'"
						     ."    AND protfa                             = 'UVR' "
						     ."    AND protfa                             = mid(tarcod,1,instr(tarcod,'-')-1) "
						     ."    AND proest                             = 'on' "
						     ."    AND (mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'  "
							 ."     OR  tarcon                            = '".$wcodcon."') "
						     ."    AND (mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
							 ."     OR  tartar                            = '".$wtar."') "
					         ."    AND tarest                             = 'on' "
					         ."    AND propun                            >= taruvi "
						     ."    AND propun                            <= taruvf "

					         ." UNION "

					         ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, protfa, propun "
						     ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
						     ."  WHERE procod                             LIKE '%".$wprocod."%'"
						     ."    AND protfa                             = 'UVR' "
						     ."    AND protfa                             = mid(tarcod,1,instr(tarcod,'-')-1) "
						     ."    AND proest                             = 'on' "
						     ."    AND (mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'  "
							 ."     OR  tarcon                            = '".$wcodcon."') "
						     ."    AND (mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
							 ."     OR  tartar                            = '".$wtar."') "
					         ."    AND tarest                             = 'on' "
					         ."    AND taruvi                             = taruvf "

					         ." UNION "

					         ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec, 0, protfa, propun "
						     ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
						     ."  WHERE procod                             LIKE '%".$wprocod."%'"
						     ."    AND protfa                             = 'GQX' "
						     ."    AND mid(progqx,1,instr(progqx,'-')-1)  = mid(tarcod,1,instr(tarcod,'-')-1) "
						     ."    AND proest                             = 'on' "
						     ."    AND (mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'  "
							 ."     OR  tarcon                            = '".$wcodcon."') "
						     ."    AND (mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
							 ."     OR  tartar                            = '".$wtar."') "
					         ."    AND tarest                             = 'on' "
					         ."  ORDER BY 2 ";
					   }
			     }  //Fin del then de if wpaquete != "on"
	            else
	               {  //Si entra aca es porque se liquida por PAQUETE
	                $q =  " SELECT procod, procod, pronom, paqdetvac, 0, paqdetvan, paqdetfec, 0, 'PAQUETE', '0' "
					     ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000114 "
					     ."  WHERE procod    = '".$wprocod."'"
                         ."    AND procod    = paqdetpro "
					     ."    AND proest    = 'on' "
					     ."    AND paqdetest = 'on' "
					     ."    AND paqdetcon = '".$wcodcon."'"
					     //."    AND  (TRIM(mid(paqdettar,1,instr(paqdettar,'-')-1)) = '".$wtar."'"
		                 //."     OR   TRIM(mid(paqdettar,1,instr(paqdettar,'-')-1)) = '*') "
		                 ."    AND  (TRIM(paqdettar) = '".$wtar."'"
		                 ."     OR   TRIM(paqdettar) = '*') "
					     ."    AND paqdetcod = '".$wcodpaq."'"
					     ."  ORDER BY 2 ";
					}
			 }      //Fin del case de tarifas de la tabla 000104
		     break;
	    }

	  if ($q=="")
	     {
	      ?>
		    <script>
		      alert ("EL CONCEPTO O GRUPO NO TIENE ARCHIVO DE TARIFAS DEFINIDO");
              function ira(){document.cargos.wprocod.focus();}
		    </script>
		  <?php
	     }
	    else
	       {
		    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
            if ($num == 0)
               {
	            ?>
			      <script>
			        alert ("EL PROCEDIMIENTO O INSUMO NO EXISTE O NO TIENE ESTA TARIFA DEFINIDA o NO ESTA DEFINIDO PARA EL CONCEPTO SELECCIONADO o NO TIENE EXISTENCIA PARA EL CENTRO DE COSTOS SELECCIONADO o LA HOMOLAGACION ESTA MAL HECHA (Código definido como del proveedor)");
	                function ira(){document.cargos.wprocod.focus();}
			      </script>
			    <?php
			   }
		   }

      if ($num > 0)
         {
	      $row = mysql_fetch_array($res);

	      $wprocod = $row[1];  //Codigo del insumo
	      $wpronom = $row[2];  //Nombre del insumo
	      $wprovac = $row[3];  //Valor Actual
	      $wproiva = $row[4];  //% IVA
	      $wprovan = $row[5];  //Valor Anterior
	      $wprofec = $row[6];  //Fecha cambio de tarifa
	      $wexiste = $row[7];  //Indica la cantidad existente cuando el concepto mueve inventarios o es del POS
	      $wtipfac = $row[8];  //Indica como se factura el procedimiento o articulo (C)odigo, (G)rupo Qx, (U)VR
	      $wpunuvr = $row[9];  //Numero de puntos que se facturan para el procedimiento seleccionado


	      //****************
	      //FEBRERO 18 DE 2009:====================================================
	      if (isset($wdevol) and $wdevol=="on")
	         {
		      if (!isset($waprovecha))
		         $waprovecha="off";

		      buscar_saldo_en_historia($wcodcon, $wprocod, $whis, $wing, $waprovecha);
		      $wexiste=$wexidev;
             }
	      //=====================================================================

	      //==============================================================================================================
	      //Como antes no validé la existencia, si el articulo es de aprovechamiento, aca traigo la existencia
	      //==============================================================================================================
	      /* //On  //FEBRERO 18 DE 2009:
	      if (isset($rowapv[0]) and $rowapv[0]=="on")
	         {
		      $q = " SELECT karexi "
		          ."   FROM ".$wbasedato."_000007 "
		          ."  WHERE karcco = '".$wccogra."'"
		          ."    AND karcod = '".$wprocod."'"
		          ."    AND karexi > 0 ";
		      $resexi = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
              $num = mysql_num_rows($resexi);   // or die (mysql_errno()." - ".mysql_error());
              if ($num > 0)
                 {
                  $rowexi=mysql_fetch_array($resexi);
                  $wexiste=$rowexi[0];
                 }
             } */
          //==============================================================================================================
	      echo "<input type='HIDDEN' name='wexiste' value='".$wexiste."'>";
	      echo "<input type='HIDDEN' name='wtipfac' value='".$wtipfac."'>";

	      if ($wconmva!="S")
	         {
		      switch ($wtipfac)
		         {
			       case "C":                                      //Para Medicamentos y Material MQX
		      	       {
			      	    //if ($wfecha < $wprofec)                 //Aca evaluo si tomo el valor anterior o el actual
			      	    if ($wfeccar < $wprofec)                  //Aca evaluo si tomo el valor anterior o el actual
				           $wvaltar = $wprovan;
				          else
				             $wvaltar = $wprovac;

				        if (isset($wdevol) and $wdevol=="on")
				           $wvaltar=($wvaltar*(-1));
				       }
				       break;
		           case "CODIGO":
		      	       {
			      	    //if ($wfecha < $wprofec)                 //Aca evaluo si tomo el valor anterior o el actual
			      	    if ($wfeccar < $wprofec)                  //Aca evaluo si tomo el valor anterior o el actual
				           $wvaltar = $wprovan;
				          else
				             $wvaltar = $wprovac;
				       }
				       break;
	               case "GQX":
		      	       {
		                //if ($wfecha < $wprofec)                 //Aca evaluo si tomo el valor anterior o el actual
		                if ($wfeccar < $wprofec)                  //Aca evaluo si tomo el valor anterior o el actual
				           $wvaltar = $wprovan;
				          else
				             $wvaltar = $wprovac;
	                   }
	                   break;
	               case "UVR":
		      	       {
			      	    //Las UVR se liquidan d e dos formas:
			      	    //(1): Si esta la tarifa definida en un rango de UVR se toma el valor total grabado en el maestro de tarifas
			      	    //     es decir NO se multiplica por UVR por un valor individual de la UVR.
			      	    //(2): Si no se tiene un rango de UVR's se liquida la cantidad de UVR del procedimiento por un valor unitario
			      	    //     de la UVR.
		                //if ($wfecha < $wprofec)                 //Aca evaluo si tomo el valor anterior o el actual
		                if ($wfeccar < $wprofec)                  //Aca evaluo si tomo el valor anterior o el actual
		                   if ($wpunuvr == 'rango')
		                      $wvaltar = $wprovan;                //Valor anterior del rango de UVR's
				             else
				                $wvaltar = $wprovan*$wpunuvr;     //Valor anterior por las UVR
				          else
				             if ($wpunuvr == 'rango')
		                        $wvaltar = $wprovac;              //Valor actual del rango de UVR's
				               else
				                  $wvaltar = $wprovac*$wpunuvr;   //Valor actual por las UVR
	                   }
	                   break;
	             }
             }

		  //Se agrega onchange='enter()' para que al cambiar de articulo consulte de nuevo la informacion asociada al articulo. Jonatan Lopez 09 Enero de 2014.
          echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' VALUE='".$wprocod."' onchange='enter()' size = 6 ></td>";                                  //wcodcon
	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' VALUE='".$wpronom."' size = 20 ondblclick='enter()'></td>";   //wnomcon

	      if ($wcontip == "C")
	         {
		      ?>
			    <script>
			      function ira(){document.cargos.wcodter.focus();}
			    </script>
			  <?php
		     }
		    else
		       {
			    ?>
				  <script>
				    function ira(){document.cargos.wcantidad.focus();}
				  </script>
				<?php
			   }
         }
        else
           {
	        echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' size = 6  onchange='enter()'></td>";                                                     //wcodcon
		    echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' size = 20 onchange='enter()' ondblclick='enter()' ></td>";                      //wnomcon
           }
     }
    else
       {
	    if (isset($wpronom) and ($wpronom != "") and isset($warctar) and ($warctar != "") and ($wconabo == "off" or !isset($wconabo)))
		   {
			$q="";
			switch ($warctar)
			    {
				 case "000026":  //Tarifas de medicamento y material
			         {
			          $q =  " SELECT artcod, artcod, artnom, mtavac, artiva, mtavan, mtafec "             //CAMBIO DE SEPTIEMBRE 5  DE 2007:
						   ."   FROM ".$wbasedato."_000001, ".$wbasedato."_000026, ".$wbasedato."_000007, ".$wbasedato."_000004 "
						   ."  WHERE artnom                            like '%".$wpronom."%'"
						   ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
						   ."    AND artest                            = 'on' "
						   //."    AND mid(artgru,1,instr(artgru,'-')-1) = '".$wcodcon."'"
						   ."    AND arttip                            = grutia "                         //CAMBIO DE SEPTIEMBRE 5  DE 2007:
				           ."    AND grucod                            = '".$wcodcon."'"                  //  "    "       "     "   "  "
						   ."    AND mtaest                            = 'on' "
						   //."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
						   ."    AND karcco                            = '".$wccogra."'"
					       ."    AND karcod                            = artcod "
					       ."    AND karexi                            > 0 "
					       ."    AND (mid(mtatar,1,instr(mtatar,'-')-1) = '".$wtar."'"
					       ."     OR  mid(mtatar,1,instr(mtatar,'-')-1) = '*') "
					       ."  GROUP BY 1,2,3 "
					       ."  ORDER BY 2 ";
					 }
				     break;
				 case "000104":  //Tarifas de procedimiento y examenes
			         {
				      if (!isset($wpaquete) or $wpaquete != "on")
					      $q =  " SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec "
							   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
							   ."  WHERE pronom                            like '%".$wpronom."%'"
							   ."    AND protfa                            = 'CODIGO' "
							   ."    AND procod                            = mid(tarcod,1,instr(tarcod,'-')-1) "
							   ."    AND proest                            = 'on' "
							   ."    AND mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'"
							   ."    AND mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
						       ."    AND tarest                            = 'on' "

						       ." UNION "

						       ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec "
							   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
							   ."  WHERE pronom                            like '%".$wpronom."%'"
							   ."    AND protfa                            = 'UVR' "
							   ."    AND protfa                            = mid(tarcod,1,instr(tarcod,'-')-1) "
							   ."    AND proest                            = 'on' "
							   ."    AND mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'"
							   ."    AND mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
						       ."    AND tarest                            = 'on' "

						       ." UNION "

						       ." SELECT procod, procod, pronom, tarvac, 0, tarvan, tarfec "
							   ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000104 "
							   ."  WHERE pronom                            like '%".$wpronom."%'"
							   ."    AND protfa                            = 'GQX' "
							   ."    AND mid(progqx,1,instr(progqx,'-')-1) = mid(tarcod,1,instr(tarcod,'-')-1) "
							   ."    AND proest                            = 'on' "
							   ."    AND mid(tarcon,1,instr(tarcon,'-')-1) = '".$wcodcon."'"
							   ."    AND mid(tartar,1,instr(tartar,'-')-1) = '".$wtar."'"
						       ."    AND tarest                            = 'on' "
						       ."  ORDER BY 2 ";
						 else
						    $q =  " SELECT procod, procod, pronom, paqdetvac, 0, paqdetvan, paqdetfec "
							     ."   FROM ".$wbasedato."_000103, ".$wbasedato."_000114 "
							     ."  WHERE pronom    like '%".$wpronom."%'"
		                         ."    AND procod    = paqdetpro "
							     ."    AND proest    = 'on' "
							     ."    AND paqdetest = 'on' "
							     ."    AND paqdetcon = '".$wcodcon."'"
							     ."    AND paqdettar = '".$wtar."'"
							     ."    AND paqdetcod = '".$wcodpaq."'"
							     ."  ORDER BY 2 ";
					 }
				     break;
			    }

			if ($q=="") //Si entre por aca es porque no tiene archivo definido el concepto
			   {
			    ?>
				  <script>
				    alert ("EL CONCEPTO O GRUPO NO TIENE ARCHIVO DE TARIFAS DEFINIDO");
	                function ira(){document.cargos.wnompro.focus();}
				  </script>
				<?php
			   }

			  else
			     {
				  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		          $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	             }

	        $sw=0;
	        if ($num > 0)
		       {
			    for ($i=1;$i<=$num;$i++)
			       {
				    $row = mysql_fetch_array($res);
			        if ($num == 1)
			           {
				        $sw="1";
				        $wprocod=$row[1];
				        $wpronom=$row[2];
			            echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' value='".$wprocod."' size=6 ></td>";
			            echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' value='".$wpronom."' size=20 onchange='enter()'></td>";
			           }
		              else
		                 {
			              if ($num > 1)                 //Si entra por aca es porque el concepto tiene varios registros con el nombre muy similar
			                 $wprocod1[$i]=$row[1];     //$wprocod=$row[1];
			             }
			        $wpronom1[$i]=str_replace(" ","%",$row[2]);
			       }
			    if ($sw==0)
			       {
				    echo "<td align=left class=".$wcf." colspan=1><b></b><SELECT name='wpronom' onchange='enter()' ondblclick='enter()' >";

				    for ($i=1;$i<=$num;$i++)
				       {
					    echo "<option>".$wpronom1[$i]."</option>";
				        $wpronom=str_replace(" ","%",$wpronom1[$i]);
				       }
				    echo "</select></td>";
                   }
		       }
              else
                 {
		 	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' size=6  onchange='enter()'></td>";                        //wprocod
				  echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' size=20 onchange='enter()'></td>";    //wpronom
			     }
	       }
	      else
	         {
		      if (!isset($wcodcon) or !isset($wconabo) or $wconabo == "off" or $wconabo == "")
		         {
		          echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wprocod' size=6  value='' onchange='enter()'></td>";                          //wprocod
		          echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wpronom' size=20 value='' onchange='enter()' ondblclick='enter()' ></td>";      //wpronom
			     }
			    else
			       { //No tiene procedimiento porque es un abono
				    echo "<td align=left class=".$wcf." colspan=1>&nbsp</td>";
				    echo "<td align=left class=".$wcf." colspan=1>&nbsp</td>";
			       }

			  $wprocod = "";  //Codigo del insumo
	          $wpronom = "";  //Nombre del insumo
	          $wprovac = 0;   //Valor Actual
	          $wproiva = 0;   //% IVA
	          $wprovan = 0;   //Valor Anterior
	          $wprofec = "";  //Fecha cambio de tarifa
	          $wexiste = "";  //Indica la cantidad existente cuando el concepto mueve inventarios o es del POS
	          $wtipfac = "";  //Indica como se factura el procedimiento o articulo (C)odigo, (G)rupo Qx, (U)VR
	          $wpunuvr = 0;   //Numero de puntos que se fcaturan para el procedimiento seleccionado
	          $wvaltar = 0;
			 }
       }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //TERCERO O MEDICO QUE PRESTA EL SERVICIO
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $wporter=0;
  if (isset($wcodter) and ($wcodter != ""))
     {
	  $q =  " SELECT meddoc, mednom, relpor "
	       ."   FROM ".$wbasedato."_000051, ".$wbasedato."_000102, ".$wbasedato."_000004 "
	       ."  WHERE medest                                  = 'on' "
	       ."    AND meddoc                                  = '".$wcodter."'"
	       ."    AND meddoc                                  = trim(mid(relmed,1,instr(relmed,'-')-1)) "
		   ."    AND trim(mid(relgru,1,instr(relgru,'-')-1)) = '".$wcodcon."'"
		   ."    AND relest                                  = 'on' "
		   ."    AND trim(mid(relgru,1,instr(relgru,'-')-1)) = grucod "
		   ."    AND grutip                                  = 'C' "
	       ."  ORDER BY mednom ";

      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());
      if ($num > 0)
         {
	      $row = mysql_fetch_array($res);
	      $wcodter = $row[0];   //Codigo del tercero
	      $wnomter = $row[1];   //Nombre del tercero
	      $wporter = $row[2];   //Porcentaje de participacion
	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodter' VALUE='".$wcodter."' size = 15 ></td>";                      //wcodter
	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomter' VALUE='".$wnomter."' size = 20 onchange='enter()' ondblclick='enter()' ></td>";   //wnomter


	      ?>
		    <script>
		      function ira(){document.cargos.wcantidad.focus();}
		      function ira(){document.cargos.wcantidad.select();}
		    </script>
		  <?php

         }
        else
           {
	        echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodter' size = 15 onchange='enter()'></td>";                                         //wcodter
		    echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomter' size = 20 onchange='enter()' ondblclick='enter()' ></td>";                      //wnomter
           }
     }
    else
       {
        if (isset($wnomter) and ($wnomter != ""))
		   {
			$wnomter=str_replace(" ","%",$wnomter);
		    $q =  " SELECT meddoc, mednom, relpor "
		         ."   FROM ".$wbasedato."_000051, ".$wbasedato."_000102, ".$wbasedato."_000004 "
		         ."  WHERE medest                                  = 'on' "
		         ."    AND mednom                                  like '%".$wnomter."%'"
		         ."    AND meddoc                                  = trim(mid(relmed,1,instr(relmed,'-')-1)) "
		         ."    AND trim(mid(relgru,1,instr(relgru,'-')-1)) = '".$wcodcon."'"
		         ."    AND relest                                  = 'on' "
		         ."    AND trim(mid(relgru,1,instr(relgru,'-')-1)) = grucod "
		         ."    AND grutip                                  = 'C' "
		         ."  ORDER BY mednom ";
		    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;

		    $sw=0;
		    if ($num > 0)
		       {
			    for ($i=1;$i<=$num;$i++)
			       {
				    $row = mysql_fetch_array($res);
			        if ($num == 1)
			           {
				        $sw=1;
			            $wcodter=$row[0];
			            $wnomter=$row[1];
                        $wporter=$row[2];   //Porcentaje de participacion

			            echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodter' value='".$wcodter."' size=6 ></td>";
			            echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomter' value='".$wnomter."' size=20 ondblclick='enter()' ></td>";              //wnomter
		               }
		              else
		                 {
			              if ($num > 1)           //Si entra por aca es porque el concepto tiene varios registros con el nombre muy similar
			                 $wcodter=$row[0];
		                 }
			        $wnomter1[$i]=$row[1];
			       }

			    if ($sw==0)
			       {
				    echo "<td align=left class=".$wcf." colspan=1><b></b><select name='wnomter' onchange='enter()' ondblclick='enter()' >";
				    for ($i=1;$i<=$num;$i++)
				       {
				        echo "<option>".$wnomter1[$i]."</option>";
				        if ($num == 1)
				           $wnomter=$wnomter1[$i];
				       }
				    echo "</select></td>";
			       }
		       }
              else
                 {
		 	      echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodter' size=15 onchange='enter()'></td>";                                    //wcodter
				  echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomter' size=20 onchange='enter()' ondblclick='enter()' ></td>";              //wnomter
			     }
	         }
	        else
	           {
		        if (isset($wcontip) and $wcontip == "C")
		           {
		 	        echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcodter' size=15 value='' onchange='enter()'></td>";                         //wcodter
				    echo "<td align=left class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wnomter' size=20 value='' onchange='enter()' ondblclick='enter()' ></td>";   //wnomter
			       }
				  else
				     {
					  $wcodter="";
					  $wnomter="";
					  $wporter=0;
					  echo "<input type='HIDDEN' name='wcodter' value='".$wcodter."'>";
					  echo "<input type='HIDDEN' name='wnomter' value='".$wnomter."'>";
					  echo "<input type='HIDDEN' name='wporter' value='".$wporter."'>";
					  echo "<td align=left class=".$wcf." colspan=1>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>"; //wcodter
				      echo "<td align=left class=".$wcf." colspan=1>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>"; //wnomter
			         }
			   }
          }

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //APROVECHAMIENTO
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (isset($warctar) and $warctar=="000026" and isset($wconinv) and $wconinv=="on")
	 {
      $q= " SELECT artapv "
         ."   FROM ".$wbasedato."_000001 "
         ."  WHERE artcod = '".$wprocod."'"
         ."    AND artest = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);

      //SEPTIEMBRE 5  DE 2007:
      if ($row[0]=="on")
         {
	 	  if (!isset($waprovecha))
		     echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='checkbox' NAME='waprovecha'></td>";
		    else
		       if ($waprovecha=="on")
		          echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='checkbox' NAME='waprovecha' CHECKED></td>";
		         else
		            echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='checkbox' NAME='waprovecha'></td>";
	     }
	    else
	       echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='checkbox' NAME='waprovecha' DISABLED></td>";
     }
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CANTIDAD
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (isset($wconabo) and $wconabo == "on") //and isset($wconmca) and $wconmca == "on" )
     {
      echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcantidad' SIZE=5 value='1' DISABLED></td>";
      $wcantidad=1;
     }
    else
	  if ((!isset($wcantidad) or $wcantidad==""))
	     echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcantidad' SIZE=5 value='1'></td>";
	    else
	       echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wcantidad' SIZE=5 VALUE='".number_format($wcantidad,2,'.',',')."'></td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //VALOR UNITARIO
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (((isset($wvaltar) and $wvaltar != "" and isset($wconabo) and $wconabo != "on") or (isset($paquete) and $wpaquete == "on")))
     {
	  if ((isset($wconmva) and $wconmva=="N"))
         {
          echo "<td align=center class=".$wcf." colspan=1>".number_format($wvaltar,0,'.',',')."</td>";
          echo "<input type='HIDDEN' name='wvaltar' value='".$wvaltar."'>";
         }
        else
           if (!isset($wvaltar) or (isset($paquete) and $wpaquete == "on"))
              echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wvaltar' VALUE='0' SIZE=10 onchange='enter()'></td>";
             else
                echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wvaltar' VALUE='".$wvaltar."' SIZE=10 onchange='enter()'></td>";
     }
    else
       if (isset($wconmva))
          {
	       if ($wconmva=="N") //Indica que no se digita el valor a cobrar
              echo "<td align=center class=".$wcf." colspan=1>&nbsp</td>";
             else
                if ($wconmva=="S" and isset($wtipfac) and ($wtipfac=="CODIGO"))
                   echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wvaltar' SIZE=10 onchange='enter()'></td>";
                  else
                     echo "<td align=center class=".$wcf." colspan=1>&nbsp</td>";
          }
         else
            echo "<td align=center class=".$wcf." colspan=1>&nbsp</td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //VALOR TOTAL
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (isset($wvaltar) and isset($wcantidad))
     echo "<td align=center class=".$wcf." colspan=1>".number_format(($wcantidad*$wvaltar),0,'.',',')."</td>";
    else
       echo "<td align=center class=".$wcf." colspan=1>&nbsp</td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //RECONOCIDO O EXCEDENTE
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (!isset($wrecexc) or $wrecexc=="" )
      if (isset($wconabo) and $wconabo!="on")
         echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wrecexc' SIZE='1' MAXLENGTH='1' VALUE='R' onkeypress='if (event.keyCode != 69 & event.keyCode != 82 & event.keyCode != 101 & event.keyCode != 114)  event.returnValue = false'></td>";
        else
           echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wrecexc' SIZE='1' MAXLENGTH='1' VALUE='R' onkeypress='if (event.keyCode != 69 & event.keyCode != 82 & event.keyCode != 101 & event.keyCode != 114)  event.returnValue = false' DISABLED></td>";
     else
        if (isset($wconabo) and $wconabo!="on")
           echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wrecexc' SIZE='1' MAXLENGTH='1' VALUE='".strtoupper($wrecexc)."' onkeypress='if (event.keyCode != 69 & event.keyCode != 82 & event.keyCode != 101 & event.keyCode != 114)  event.returnValue = false'></td>";
          else
             echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wrecexc' SIZE='1' MAXLENGTH='1' VALUE='".strtoupper($wrecexc)."' onkeypress='if (event.keyCode != 69 & event.keyCode != 82 & event.keyCode != 101 & event.keyCode != 114)  event.returnValue = false' DISABLED></td>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FACTURABLE (S/N)
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (!isset($wfacturable) or $wfacturable=="" )
     echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wfacturable' SIZE='1' VALUE='S' MAXLENGTH='1' onkeypress='if (event.keyCode != 69 & event.keyCode != 82 & event.keyCode != 101 & event.keyCode != 114)  event.returnValue = false'></td>";
    else
        echo "<td align=center class=".$wcf." colspan=1><INPUT TYPE='text' NAME='wfacturable' SIZE='1' VALUE='".strtoupper($wfacturable)."' MAXLENGTH='1' onblur='enter()' onkeypress='if (event.keyCode != 78 & event.keyCode != 83 & event.keyCode != 110 & event.keyCode != 115)  event.returnValue = false'></td>";
  echo "</tr>";

  if (isset($wvaltar) and isset($wporter))
     echo "<input type='HIDDEN' name='wporter' value='".$wporter."'>";


  echo "<tr><td class=".$wcf2." colspan=15 align=center><b>Grabar</b><input type='radio' name='wgrabar' SIZE=2></td></tr>";           //wcons
  //echo "<tr><td align=center class=encabezadoTabla colspan=14><input type='button' value='OK' Onclick='enter()'></td></tr>";
  echo "<tr><td align=center colspan=15><input type='submit' value='OK'></td></tr>";

  echo "</table></div>";
//------------------> div nuevos cargos

  //=====================================================================================
  //ANULACION
  //=====================================================================================
  if (isset($wanular) and ($wanular == 'S'))
     {
      $wanurec="on";  //Por defecto coloca la variable de anular recibo en on, esto permite que si el cargo no es de abono
	                  //se permita anular el cargo. Porque de lo contrario la posibilidad de anularlo lo determina la
	                  //verificacion que se haga en los cuadres de caja en la parte de abajo.
	  ///////////////////////////////////////////////////////////////////////////////////
	  //TRAIGO ALGUNOS DATOS DEL CARGO ==================================================
	  //SI EL CONCEPTO MUEVE INVENTARIO =================================================
	  ///////////////////////////////////////////////////////////////////////////////////
	  if ($wcajadm=="on")   //Si es usuario Administrador lo deja anular cualquier cargo
	     {
		  $q= "  SELECT tcarconcod, tcarprocod, tcarnmo, tcarcmo, tcarcan, gruinv, gruabo, grumca, tcarser, tcarapr, tcarhis, tcaring "
		     ."    FROM ".$wbasedato."_000106, ".$wbasedato."_000004 "
	         ."   WHERE ".$wbasedato."_000106.id    = ".$wid
	         ."     AND                  tcarest    = 'on' "
	         ."     AND                  tcarconcod = grucod ";
	     }
		else                //Si NO es administrador solo puede anular los cargos grabados por el
		   {
			$q= "  SELECT tcarconcod, tcarprocod, tcarnmo, tcarcmo, tcarcan, gruinv, gruabo, grumca, tcarser, tcarapr, tcarhis, tcaring "
			   ."    FROM ".$wbasedato."_000106, ".$wbasedato."_000004 "
		       ."   WHERE ".$wbasedato."_000106.id    = ".$wid
		       ."     AND                  tcarest    = 'on' "
		       ."     AND                  tcarconcod = grucod "
		       ."     AND                  tcarusu    = '".$wusuario."'";     //Condiciono que solo sea el usuario que lo grabo
		   }
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
      if ($num > 0)
         {
          $row = mysql_fetch_array($res);
          $wconfac = $row[0]; //Concepto de facturacion
          $wcodart = $row[1]; //Codigo del producto
          $wnromto = $row[2]; //Numero de movimietno con el que se cargo en inventario
          $wconmto = $row[3]; //Concepto de Inventarios
          $wcanart = $row[4]; //Cantidad registrada del articulo o procedimiento
          $wconinv = $row[5]; //Indica que SI mueve o NO inventarios
          $wanuabo = $row[6]; //Indica que es Abono o No
          $wabomca = $row[7]; //Indica que es Abono y SI mueve caja o No
          $wccogra = $row[8]; //Centro de costos que grabo
          $waprove = $row[9]; //Si es un aprovechamiento
          $whis    = $row[10];//Historia
          $wing    = $row[11];//Ingreso
         }
        else
           {
            $wconinv="";       //Indica que NO mueve inventarios
            $wanuabo="";       //Indica que el cargos es de un ABONO
            $wabomca="";       //Indica que el cargos es de un ABONO y SI mueve caja
           }


      /////////////////////////////////////////////////////////////////////////////////
      //ACA CARGO AL INVENTARIO ======================================================
      /////////////////////////////////////////////////////////////////////////////////

      if (isset($wconinv) and $wconinv=="on" and isset($waprove) and $waprove=="off")
         {
	      $q= "  SELECT mdevto, mdepiv "
		     ."    FROM ".$wbasedato."_000011 "
	         ."   WHERE mdedoc = '".$wnromto."'"
	         ."     AND mdecon = '".$wconmto."'"
	         ."     AND mdeart = '".$wcodart."'";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $num = mysql_num_rows($res);
	      if ($num > 0)
	         {
		      $row = mysql_fetch_array($res);
		      $wcosmto=$row[0];  //Costo total del articulo en el mvto de inventarios
		      $wpivmto=$row[1];  //Porcentaje de IVA
	         }

          $q = "lock table ".$wbasedato."_000007 LOW_PRIORITY WRITE";
		  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		  //===================================================================================================================================
		  //CARGO EN EL KARDEX
		  //===================================================================================================================================
	      //ACTUALIZO EN LA TABLA DEL -- <SALDOS EN LINEA> -- DEL  **** KARDEX ****
		  $q= "UPDATE ".$wbasedato."_000007 "
	         ."   SET karexi = karexi + ".$wcanart
	         ." WHERE karcco = '".$wccogra."'"
	         ."   AND karcod = '".$wcodart."'";
	      $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	      $q= " UNLOCK TABLES";
		  $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		  /////////////////////////////////////////////////////////////////////////////////
	      //ACTUALIZO Y TOMO EL CONSECUTIVO Y EL CODIGO DEL CONCEPTO DE VENTA EN INVENTARIO
		  $q = "lock table ".$wbasedato."_000008 LOW_PRIORITY WRITE";
		  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	      $q= "UPDATE ".$wbasedato."_000008 "
	         ."   SET concon = concon + 1 "
	         ." WHERE concan = '".$wconmto."'"
	         ."   AND conest = 'on' ";
	      $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	      $q= "   SELECT concon, concod "
	         ."     FROM ".$wbasedato."_000008 "
	         ."    WHERE concan = '".$wconmto."'"
	         ."      AND conest = 'on' ";
	      $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $row = mysql_fetch_array($err);
		  $wnromvto=$row[0];
	      $wconmvto=$row[1];
	      $q = " UNLOCK TABLES";
		  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		  //===================================================================================================================================
	      //===================================================================================================================================
	      //GRABO EN LA TABLA DEL -- <ENCABEZADO> -- DEL **** MOVIMIENTO DE INVENTARIOS ****
	      $q= " INSERT INTO ".$wbasedato."_000010 (   Medico       ,   Fecha_data,   Hora_data,   menano      ,   menmes      ,   menfec    ,  mendoc       ,   mencon      ,   mencco     ,   menccd     ,   mendan     , menpre, mennit, menusu, menfac, menest, Seguridad        ) "
	         ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".date("Y")."','".date("m")."','".$wfecha."','".$wnromvto."','".$wconmvto."','".$wccogra."','".$wccogra."','".$wnromto."', 0     , '0'   , '.'   , '.'   ,   'on', 'C-".$wusuario."')";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


          //===================================================================================================================================
          //===================================================================================================================================
          //GRABO EN LA TABLA DEL -- <DETALLE> -- DE  **** MOVIMIENTO DE INVENTARIOS ****
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  //===================================================================================================================================

          //=========================================
          //TRAIGO EL COSTO PROMEDIO DEL ARTICULO
          //$q= "SELECT karpro "
          //   ."  FROM ".$wbasedato."_000007 "
	      //   ." WHERE karcco = '".$wcco."'"
	      //   ."   AND karcod = '".$wprocod."'"
	      //   ."   AND karexi > 0 ";
	      //$res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	      //$row_cos = mysql_fetch_array($res2);

	      //=========================================
          //GRABO EL DETALLE DEL ARTICULO - DEVOLUCION
          $q= " INSERT INTO ".$wbasedato."_000011 (   Medico       ,   Fecha_data,   Hora_data,   mdecon      ,  mdedoc       ,   mdeart     ,  mdecan    ,  mdevto    ,   mdepiv     , mdeest, Seguridad        ) "
             ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wconmvto."','".$wnromvto."','".$wcodart."',".$wcanart.",".$wcosmto.",'".$wpivmto."', 'on'  , 'C-".$wusuario."') ";
          $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         }
        else
          if ($wanuabo=="on" and $wabomca=="on" )   //Indica que es un Abono y ACA ANULO EN LAS TABLAS DE RECIBOS DE CAJA LOS DOCUMENTOS DEL ABONO
             {
	          $q = " SELECT rdefue, rdenum, count(*) "
	              ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000020 "
	              ."  WHERE rdereg = ".$wid
	              ."    AND rdefue = rfpfue "
	              ."    AND rdenum = rfpnum "
	              ."    AND rfpecu in ('S','P') "  //Que el recibo este sin cuadrar o pendiente de cuadre o Incompleto
	              ."    AND rdefue = renfue "
	              ."    AND rdenum = rennum "
	              ."    AND rencaj = rfpcaf "      //Solo se permiten anular recibos de la misma caja
	              ."  GROUP BY 1,2 ";
	          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	          $num = mysql_num_rows($res);
	          $row = mysql_fetch_array($res);
	          if ($row[2] > 0)
	             {
		          $wfrec=$row[0];
		          $wdrec=$row[1];
	              $wanurec="on";      //Indica que el recibo esta en los cuadres pero todo su valor esta pendiente de cuadrar
                 }
		        else
		           $wanurec="off";    //Indica que ya se cuadro parte o todo el recibo

              if ($wanurec=="on")
                 {
	              //ANULO EL ENCABEZADO DEL RECIBO
	              $q= "UPDATE ".$wbasedato."_000020 "
	                 ."   SET renest = 'off' "
	                 ." WHERE renfue = '".$wfrec."'"
	                 ."   AND rennum = '".$wdrec."'";
	              $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	              //ANULO EL DETALLE DEL RECIBO
	              $q= "UPDATE ".$wbasedato."_000021 "
	                 ."   SET rdeest = 'off' "
	                 ." WHERE rdefue = '".$wfrec."'"
	                 ."   AND rdenum = '".$wdrec."'";
	              $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	              //ANULO EL ENCABEZADO DEL RECIBO
	              $q= "UPDATE ".$wbasedato."_000022 "
	                 ."   SET rfpest = 'off' "
	                 ." WHERE rfpfue = '".$wfrec."'"
	                 ."   AND rfpnum = '".$wdrec."'";
	              $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	              //BORRO LOS CUADRES PENDIENTES
	              $q= "DELETE FROM ".$wbasedato."_000037 "
	                 ." WHERE Cdefue = '".$wfrec."'"
	                 ."   AND Cdenum = '".$wdrec."'";
	              $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				}
             }


	  if ($wanurec=="on")
	     {
		  if ($wcajadm=="on")
		     $wusu_anula="%";
		    else
		      $wusu_anula=$wusuario;


		  //ANULO EN LA TABLA DE CARGOS DE PAQUETES
		  $q="  UPDATE ".$wbasedato."_000115 "
	        ."     SET movpaqest = 'off' "
	        ."   WHERE movpaqreg = ".$wid
	        ."     AND seguridad LIKE '".$wusu_anula."'";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $wanular='N';

		  //ANULA EN LA TABLA DE CARGOS
	      $q="  UPDATE ".$wbasedato."_000106 "
	        ."     SET tcarest = 'off' "
	        ."   WHERE id = ".$wid
	        ."     AND tcarusu LIKE '".$wusu_anula."'";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	      if (mysql_affected_rows()>0)
	         {
		      //===========================================================================================================================================================================
		      //Mayo 11 de 2009 ============================================================================================================================================================
		      if (strtoupper($wbasedato)=="SOE")  //Pregunto si la anulacion se esta haciendo en la empresa '07' (SOE), si si entro modificar la tabla de presupuesto, modifcando la cantidad facturada
		         {
			      //DISMINUYO LA CANTIDAD FACTURADA EN LA TABLA DE PRESUPUESTOS DE SOE
			      $q="  UPDATE ".$wbasedato."_000131 "
			        ."     SET ptocfa  = ptocfa - ".$wcanart
			        ."   WHERE ptohis  = '".$whis."'"
			        ."     AND ptoing  = '".$wing."'"
			        ."     AND ptocpt  = '".$wconfac."'"
			        ."     AND ptopro  = '".$wcodart."'"
			        ."     AND ptocfa >= ".$wcanart;
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			     }
			  //===========================================================================================================================================================================

		      $wanular='N';

		      //ACA GRABO LA AUDITORIA
		      $q= " INSERT INTO ".$wbasedato."_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis       ,   auding  ,   audreg  , audacc ,   audusu      , Seguridad) "
		         ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$whistoria."','".$wing."','".$wid."', 'Anulo','".$wusuario."', 'C-".$wusuario."')";
		      $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	         }
         }
        else
          {
	       ?>
	        <script>
	          alert ("** ATENCION ** NO SE PUEDE ANULAR PORQUE CORRESPONDE A UN ABONO Y ESTA YA SE INCLUYO EN UN CUADRE DE CAJA");
            </script>
	       <?php
	      }

     }   //fin del if $wanular


  //====================================================================================================================================================
  //====================================================================================================================================================
  //DE ACA EN ADELANTE SALE LA FORMA DE PAGO CUANDO EL CONCEPTO ES DE *** ABONO ***
  //====================================================================================================================================================
  if (isset($wconabo) and $wconabo=="on" and isset($whis) and $whis!="")  //Indica que es un abono y que mueve caja
     {
	  if ($wconmca=="on")   //Indica que es un abono y que mueve caja
	     {
		  //Aca traigo la fuente y el consecutivo del centro de costo para saber si tiene fuente y consecutivo
		  //Si no tiene estos dos datos bien no hace la grabación del cargo.
		  $q = "SELECT ccofrc, ccorci "                //Fuente Recibo de Caja y Consecutivo
			      ."  FROM ".$wbasedato."_000003 "
			      ." WHERE ccocod='".$wcco."'"
			      ."   AND ccoest = 'on' ";
		  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $row = mysql_fetch_array($err);
		  $wfuerec   =$row[0];               //Fuente Recibo de Caja
		  $wnrorec   =$row[1];               //Consecutivo recibo de caja

		  if (is_numeric($wnrorec) and $wnrorec>0 and $wfuerec != "NO APLICA")
	 	     $wtiene_consecutivo="on";
		    else
		       $wtiene_consecutivo="off";

		  if ($wtiene_consecutivo == "on")
	         {
			  echo "<table border='0'>";
			  if (!isset($wvalfpa[1]))  //No ha ingresado el primer valor de la forma de pago.
			     {
			      $fk=1;
			     }
			    else
			       {
			        $wtotfpa=0;
			        for ($j=1;$j<=$fk;$j++)   //Aca sumo todas las formas de pago, para saber si se muestra el campo de cambio
			           {
			           	if( !isset( $wvalfpa[$j] ) or trim($wvalfpa[$j]) == "" )
			           		$wvalfpa[$j] = 0;
				        $wvalfpa[$j]=str_replace(",","",$wvalfpa[$j])*1;    //Le quito el formato al número
			            $wtotfpa=$wtotfpa+$wvalfpa[$j];
			           }
		           }
		      formasdepago($fk);

		      if (isset($fk)) echo "<input type='HIDDEN' name='fk' value='".$fk."'>";


		      //ACA SE VALIDA QUE SE HALLAN DIGITADO TODOS LOS DATOS NECESARIOS PARA LA FORMA DE PAGO SELECCIONADA
			  $wvalida="on";
			  if ($obliga[1]=="on" and $wtotfpa > 0)
			     if ($wobsrec[1]=="" or $wobsrec[1]== " ")
			        $wvalida="off";
			       elseif ($wubica[1]=="" or $wubica[1]==" ")
			              $wvalida="off";
			             elseif ($wautori[1]=="" or $wautori[1]==" ")
			                    $wvalida="off";
			                   elseif ($wdocane[1]=="" or $wdocane[1]==" ")
			                          $wvalida="off";

			  if ($wvalida == "off")
		         {
			      ?>
			       <script>
			         alert ("FALTAN DATOS POR REGISTRAR PARA ESTA FORMA DE PAGO");
		           </script>
			      <?php
			     }

		      if (isset($wtotfpa) and $wtotfpa > 0 and $wvalida == "on")
		         { ////
		           $wvaltar=$wtotfpa*(-1); //Coloco el valor negativo para los abonos
				   $wprocod="";
				   $wpronom="";
				   $wnromvto="";
				   $wconmvto="";
				   $wtipfac="ABONO";

				   if (!isset($wno2))
				      $wno2="";

				   if (!isset($wccogra) || trim($wccogra) == "")
				      $wccogra=$wcco;

				   //SEPTIEMBRE 5  DE 2007:
				   if (!isset($waprovecha))
		             $waprovecha="off";

		           //GRABAR EN LA TABLA DE CARGOS ======================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
				   $q= " INSERT INTO ".$wbasedato."_000106 (   Medico       ,   Fecha_data ,   Hora_data,   tcarusu     ,   tcarhis       ,   tcaring  ,   tcarfec    ,   tcarsin ,   tcarres                 ,   tcarno1 ,   tcarno2  ,   tcarap1 ,   tcarap2 ,   tcardoc ,   tcarser    ,   tcarconcod ,   tcarconnom ,   tcarprocod ,   tcarpronom ,   tcartercod ,   tcarternom ,   tcarterpor ,   tcarcan      ,   tcarvun    ,   tcarvto                      ,   tcarrec       ,   tcarfac        ,   tcartfa    ,tcarest,   tcarnmo     ,   tcarcmo     ,   tcarapr       , Seguridad) "
				      ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$whistoria."' ,'".$wing."' ,'".$wfeccar."','".$wser."','".$wcodemp."-".$wnomemp."','".$wno1."','".$wno2."' ,'".$wap1."','".$wap2."','".$wdoc."','".$wccogra."','".$wcodcon."','".$wnomcon."','".$wprocod."','".$wpronom."','".$wcodter."','".$wnomter."','".$wporter."','".$wcantidad."','".$wvaltar."','".round($wcantidad*$wvaltar)."','".$wrecexcfpa."','".$wfacturable."','".$wtipfac."','on'   ,'".$wnromvto."','".$wconmvto."','".$waprovecha."', 'C-".$wusuario."')";
				   $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				   $wid=mysql_insert_id();   //Esta función devuelve el id despues de un insert, siempre y cuando el campo sea de autoincremento

				  //**************************
			      //Aca grabo la auditoria
			      //**************************
			      $q= " INSERT INTO ".$wbasedato."_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis       ,   auding  ,   audreg  , audacc ,   audusu      , Seguridad) "
			         ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$whistoria."','".$wing."','".$wid."', 'Grabo','".$wusuario."', 'C-".$wusuario."')";
			      $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


			      //***********************************************************************************************************************************
			      //***********************************************************************************************************************************
			      //***********************************************************************************************************************************
			      //* DESDE ACA GRABO EN LAS TABLAS DE LOS RECIBOS DE CAJA ****************************************************************************
			      //***********************************************************************************************************************************
			      //***********************************************************************************************************************************
			      //***********************************************************************************************************************************
			      //ACA ACTUALIZO Y TOMO EL <CONSECUTIVO> DE **** RECIBOS ****
				  //EN LA TABLA DE CENTROS DE COSTO **************************
				  $q = "lock table ".$wbasedato."_000003 LOW_PRIORITY WRITE";
				  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				  $q =  " UPDATE ".$wbasedato."_000003 "
					   ."    SET ccorci = ccorci + 1 "         //Consecutivo Recibo de Caja Inicial
					   ."  WHERE ccocod = '".$wcco."'"
					   ."    AND ccorci < ccorcf "
					   ."    AND ccoest = 'on' ";

				  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				  $q = "SELECT ccofrc, ccorci "                //Fuente Recibo de Caja y Consecutivo
				      ."  FROM ".$wbasedato."_000003 "
				      ." WHERE ccocod='".$wcco."'"
				      ."   AND ccoest = 'on' ";
				  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				  $row = mysql_fetch_array($err);
				  $wfuerec   =$row[0];               //Fuente Recibo de Caja
				  $wnrorec   =$row[1];               //Consecutivo recibo de caja

				  $q = " UNLOCK TABLES";
				  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  //***********************************************************************************************************************************


				  //===================================================================================================================================
			      //===================================================================================================================================
			      //GRABO EN LA TABLA DEL -- <ENCABEZADO> -- EN EL **** RECIBO DE CAJA SIN FACTURA ****
			      $q= " INSERT INTO ".$wbasedato."_000020 (   Medico       ,   Fecha_data,   Hora_data,  renfue    ,  rennum    ,  renvca                            ,   rencod     ,   rennom     ,   rencaj,      renusu      ,   rencco  , renest,   renfec    , renobs, Seguridad        ) "
					 ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,".$wfuerec.",".$wnrorec.",".number_format($wtotfpa,0,'.','').",'".$wcodemp."','".$wnomemp."','".$wcaja."','".$wusuario."','".$wcco."', 'on'  ,'".$wfecha."', ''    , 'C-".$wusuario."')";
				  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


			      //===================================================================================================================================
			      //===================================================================================================================================
			      //GRABO EN LA TABLA DEL -- <DETALLE DE FACTURAS> -- EN EL **** RECIBO DE CAJA SIN FACTURA ****
			      $q= " INSERT INTO ".$wbasedato."_000021 (   Medico       ,   Fecha_data,   Hora_data,  rdefue    ,  rdenum     ,  rdecco  , rdefac, rdevta,   rdevca                            ,  rdehis      ,  rdeing , rdeffa, rdecon, rdevco, rdesfa, rdeest,   rdereg, Seguridad        ) "
				      ."                           VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,".$wfuerec.",".$wnrorec.",'".$wcco."', ''    , ''    , ".number_format($wtotfpa,0,'.','').",".$whistoria.",".$wing.", ''    , ''    , 0     , 0     ,'on'   , ".$wid.", 'C-".$wusuario."')";
			      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


			      $wvalcam=0;
			      ////////////////////////////////////////////////////////////////////////
			      ///ACA TRAIGO CADA UNA DE LAS FORMA DE PAGO DEL RECIBO DE CAJA
			      for ($j=1;$j<=$fk;$j++)
			        {
			         $pos = strpos($wfpa[$j],"-");
			         $wcfpa = substr($wfpa[$j],0,$pos-1);

			         $wdane=$wdocane[$j];
			         $wobs=$wobsrec[$j];
			         if ($j == $fk)
			            $wvfpa=$wvalfpa[$j]-$wvalcam;  //Si es la ultima forma de pago le resto lo del valor de la devuelta
			           else
			              $wvfpa=$wvalfpa[$j];
			         $wvcam=$wvalcam;

			         if (isset($wubica[$j]))
			            $wplaza=$wubica[$j];
			           else
			              $wplaza="";

			         if (isset($wautori[$j]))
			            $wauto =$wautori[$j];
			           else
			              $wauto="";

			         //===================================================================================================================================
			         //===================================================================================================================================
			         //GRABO EN LA TABLA DEL -- <DETALLE DE FORMAS DE PAGO> -- EN EL ** RECIBO DE CAJA **
			         $wbanco_des=explode("-",$wbandes[$j]);
			         $q= " INSERT INTO ".$wbasedato."_000022 (   Medico       ,   Fecha_data,   Hora_data,  rfpfue    ,  rfpnum    ,   rfpcco  ,   rfpfpa   ,  rfpvfp  ,   rfpdan,      rfpobs  ,   rfppla    ,   rfpaut   , rfpecu, rfpest,    rfpcaf   ,    rfpbai           ,    rfpban           , Seguridad        ) "
			 	        ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,".$wfuerec.",".$wnrorec.",'".$wcco."','".$wcfpa."',".$wvfpa.",'".$wdane."','".$wobs."','".$wplaza."','".$wauto."', 'S'   , 'on'  , '".$wcaja."', '".$wbanco_des[0]."', '".$wbanco_des[0]."', 'C-".$wusuario."')";
			         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			        }

				  $wcodcon    ="";
			      $wnomcon    ="";
			      $wccogra    ="";
			      $wprocod    ="";
			      $wpronom    ="";
			      $wcodter    ="";
			      $wporter    ="";
			      $wcantidad  ="";
			      $wrecexcfpa ="";
			      $wfacturable="";
			      $wconabo    ="";
			      $wconmca    ="";
			      $wtotfpa    =0;
			      $wconabo    ="off";

			      echo "<input type='HIDDEN' name='wcodcon' value='".$wcodcon."'>";
			      echo "<input type='HIDDEN' name='wnomcon' value='".$wnomcon."'>";
			      echo "<input type='HIDDEN' name='wccogra' value='".$wccogra."'>";
			      echo "<input type='HIDDEN' name='wprocod' value='".$wprocod."'>";
			      echo "<input type='HIDDEN' name='wpronom' value='".$wpronom."'>";
		          echo "<input type='HIDDEN' name='wconabo' value='".$wconabo."'>";
		          echo "<input type='HIDDEN' name='wconmca' value='".$wconmca."'>";

		          unset($wcodcon);
				  unset($wnomcon);
				  unset($wccogra);
				  unset($wprocod);
				  unset($wpronom);
				  unset($wcodter);
				  unset($wnomter);
				  unset($wporter);
				  unset($wcantidad);
				  unset($wvaltar);
				  unset($wrecexc);
				  unset($wfacturable);
				  unset($wgrabar);

				  ?>
			       <script>
			         enter();
		           </script>
			      <?php


				  ?>
				    <script>
				        function ira(){document.cargos.wcodcon.focus();}
				    </script>
				  <?php
			     } ////
			  echo "</table>";
	         }//Fin del then de if $wtiene_consecutivo
	        else
	           {
			      ?>
			       <script>
			         alert ("NO EXISTE FUENTE, NI CONSECUTIVO PARA RECIBOS EN ESTE CENTRO DE COSTO");
		           </script>
			      <?php
			     }
		 } //Fin del then de if ($wconmca) SI mueve caja
        else
           {
	        $wccogra=$wcco;

	        //*********
	        echo "<table border='0'>";
			  if (!isset($wvalfpa[1]))  //No ha ingresado el primer valor de la forma de pago.
			     {
			      $fk=1;
			     }
			    else
			       {
			        $wtotfpa=0;
			        for ($j=1;$j<=$fk;$j++)   //Aca sumo todas las formas de pago, para saber si se muestra el campo de cambio
			           {
				        $wvalfpa[$j]=str_replace(",","",$wvalfpa[$j]);    //Le quito el formato al número
				        $wvalfpa[$j] = ( trim( $wvalfpa[$j] == "") ) ? 0 : $wvalfpa[$j];//-->Cambio de migracion.
			            $wtotfpa=$wtotfpa+$wvalfpa[$j];
			           }
		           }
		      formasdepago($fk);

		      if (isset($fk)) echo "<input type='HIDDEN' name='fk' value='".$fk."'>";


		      //ACA SE VALIDA QUE SE HALLAN DIGITADO TODOS LOS DATOS NECESARIOS PARA LA FORMA DE PAGO SELECCIONADA
			  $wvalida="on";
			  if ($obliga[1]=="on" and $wtotfpa > 0)
			     if ($wobsrec[1]=="" or $wobsrec[1]== " ")
			        $wvalida="off";
			       elseif ($wubica[1]=="" or $wubica[1]==" ")
			              $wvalida="off";
			             elseif ($wautori[1]=="" or $wautori[1]==" ")
			                    $wvalida="off";
			                   elseif ($wdocane[1]=="" or $wdocane[1]==" ")
			                          $wvalida="off";

			  if ($wvalida == "off")
		         {
			      ?>
			       <script>
			         alert ("FALTAN DATOS POR REGISTRAR PARA ESTA FORMA DE PAGO");
		           </script>
			      <?php
			     }

		      if (isset($wtotfpa) and $wtotfpa > 0 and $wvalida == "on")
		         { ////

		          //*********
		          $wvaltar=$wtotfpa*(-1); //Coloco el valor negativo para los abonos
                  $wprocod="";
		          $wpronom="";
			      $wnromvto="";
			      $wconmvto="";
			      $wtipfac="ABONO";

			      if (!isset($wno2))
			         $wno2="";

			      if (trim($wccogra)=="")
			         $wccogra=$wcco;

			      //SEPTIEMBRE 5  DE 2007:
			      if (!isset($waprovecha))
		             $waprovecha="off";

		          //GRABAR EN LA TABLA DE CARGOS ======================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
			      $q= " INSERT INTO ".$wbasedato."_000106 (   Medico       ,   Fecha_data ,   Hora_data,   tcarusu     ,   tcarhis       ,   tcaring  ,   tcarfec    ,   tcarsin ,   tcarres                 ,   tcarno1 ,   tcarno2  ,   tcarap1 ,   tcarap2 ,   tcardoc ,   tcarser    ,   tcarconcod ,   tcarconnom ,   tcarprocod ,   tcarpronom ,   tcartercod ,   tcarternom ,   tcarterpor ,   tcarcan      ,   tcarvun    ,   tcarvto                      ,   tcarrec       ,   tcarfac        ,   tcartfa    ,tcarest,   tcarnmo     ,   tcarcmo     ,   tcarapr       , Seguridad) "
			         ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$whistoria."' ,'".$wing."' ,'".$wfeccar."','".$wser."','".$wcodemp."-".$wnomemp."','".$wno1."','".$wno2."' ,'".$wap1."','".$wap2."','".$wdoc."','".$wccogra."','".$wcodcon."','".$wnomcon."','".$wprocod."','".$wpronom."','".$wcodter."','".$wnomter."','".$wporter."','".$wcantidad."','".$wvaltar."','".round($wcantidad*$wvaltar)."','".$wrecexcfpa."','".$wfacturable."','".$wtipfac."','on'   ,'".$wnromvto."','".$wconmvto."','".$waprovecha."', 'C-".$wusuario."')";
			      $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                  $wid=mysql_insert_id();   //Esta función devuelve el id despues de un insert, siempre y cuando el campo sea de autoincremento

                  /*
			      $q= "SELECT id "
			         ."  FROM ".$wbasedato."_000106 "
			         ." WHERE fecha_data = '".$wfecha."'"
			         ."   AND Hora_data  = '".$hora."'"
			         ."   AND tcarusu    = '".$wusuario."'"
			         ."   AND tcarhis    = '".$whistoria."'"
			         ."   AND tcaring    = '".$wing."'"
			         ."   AND tcarfec    = '".$wfeccar."'"
			         ."   AND tcarsin    = '".$wser."'"
			         ."   AND tcarres    = '".$wcodemp."-".$wnomemp."'"
			         ."   AND tcarno1    = '".$wno1."'"
			         ."   AND tcarap1    = '".$wap1."'"
			         ."   AND tcarap2    = '".$wap2."'"
			         ."   AND tcardoc    = '".$wdoc."'"
			         //."   AND tcarser    = '".$wcco."'"
			         ."   AND tcarconcod = '".$wcodcon."'"
			         ."   AND tcarconnom = '".$wnomcon."'"
			         ."   AND tcarprocod = '".$wprocod."'"
			         ."   AND tcarpronom = '".$wpronom."'"
			         ."   AND tcartercod = '".$wcodter."'"
			         ."   AND tcarternom = '".$wnomter."'"
			         ."   AND tcarterpor = '".$wporter."'"
			         ."   AND tcarcan    = '".$wcantidad."'"
			         ."   AND tcarvun    = '".$wvaltar."'"
			         ."   AND tcarrec    = '".$wrecexcfpa."'"
			         ."   AND tcarfac    = '".$wfacturable."'"
			         ."   AND tcartfa    = '".$wtipfac."'";
			      $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      $row2 = mysql_fetch_array($res2);
			      $wid=$row2[0];
                  */


			      //**************************
		          //Aca grabo la auditoria
		          //**************************
		          $q= " INSERT INTO ".$wbasedato."_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis       ,   auding  ,   audreg  , audacc ,   audusu      , Seguridad) "
		             ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$whistoria."','".$wing."','".$wid."', 'Grabo','".$wusuario."', 'C-".$wusuario."')";
		          $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


		          $wcodcon    ="";
				  $wnomcon    ="";
				  $wccogra    ="";
				  $wprocod    ="";
				  $wpronom    ="";
				  $wcodter    ="";
				  $wporter    ="";
				  $wcantidad  ="";
				  $wrecexcfpa ="";
				  $wfacturable="";
				  $wconabo    ="";
				  $wconmca    ="";
				  $wtotfpa    =0;
				  $wconabo    ="off";

				  echo "<input type='HIDDEN' name='wcodcon' value='".$wcodcon."'>";
				  echo "<input type='HIDDEN' name='wnomcon' value='".$wnomcon."'>";
				  echo "<input type='HIDDEN' name='wccogra' value='".$wccogra."'>";
				  echo "<input type='HIDDEN' name='wprocod' value='".$wprocod."'>";
				  echo "<input type='HIDDEN' name='wpronom' value='".$wpronom."'>";
			      echo "<input type='HIDDEN' name='wconabo' value='".$wconabo."'>";
			      echo "<input type='HIDDEN' name='wconmca' value='".$wconmca."'>";

			      unset($wcodcon);
				  unset($wnomcon);
				  unset($wccogra);
				  unset($wprocod);
				  unset($wpronom);
				  unset($wcodter);
				  unset($wnomter);
				  unset($wporter);
				  unset($wcantidad);
				  unset($wvaltar);
				  unset($wrecexc);
				  unset($wfacturable);
				  unset($wgrabar);

				  ?>
				    <script>
				      enter();
			        </script>
				  <?php
                 }
	       }
	 }
  mostrar();

  echo "<table border='0'>";
  echo "<tr><td align=left><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
}
?>
