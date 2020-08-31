<html>

<head>
<title>MATRIX - [PEDIDOS DEL STOCK]</title>

<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script>
</head>

<body onLoad='javascript:inicial();'>
<script type="text/javascript">
/******************************************************************************************************************************
 *
 ******************************************************************************************************************************/
function inicial(){
	if(document.getElementById("wtarticulo")){
		document.getElementById("wtarticulo").focus();
	}
}

/******************************************************************************************************************************
 *Accion de realizar el pedido
 ******************************************************************************************************************************/
 function realizarPedido(){
		var wemp_pmla = document.forms.forma.wemp_pmla.value;

		var parametros = document.forms.forma.wsservicio.value.split("-");

		var cco = parametros[0];
		var nombreCco = parametros[1];


		if(cco != ''){
	 		document.location.href = 'pedidosStock.php?wemp_pmla='+wemp_pmla+'&waccion=a'+'&wcco='+cco+'&nombreCco='+nombreCco;
	 	} else {
	 		alert("Debe especificar un centro de costos");
	 	}
	}
/******************************************************************************************************************************
 *Accion de realizar el pedido
 ******************************************************************************************************************************/
 function consultarPedido(){
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	var cco = document.forms.forma.wsservicio.value;
	var consecutivo = document.forms.forma.wconsecutivo.value;

	if(cco != ''){
		if(consecutivo != ''){
	 		document.location.href = 'pedidosStock.php?wemp_pmla='+wemp_pmla+'&waccion=c'+'&wcco='+cco+'&wconsecutivo='+consecutivo;
		} else {
			alert("Debe digitar un numero de pedido");
		}
 	} else {
 		alert("Debe especificar un centro de costos");
 	}
 }
 /******************************************************************************************************************************
  *Accion de realizar el pedido
  ******************************************************************************************************************************/
  function consultarPedidoServicio(){
 	var wemp_pmla = document.forms.forma.wemp_pmla.value;
 	var cco = document.forms.forma.wsservicio.value;

	if (cco=="")
	{
		cco="%";
	}

 	if(cco != ''){
 		document.location.href = 'pedidosStock.php?wemp_pmla='+wemp_pmla+'&waccion=f'+'&wcco='+cco;
  	} else {
  		alert("Debe especificar un centro de costos");
  	}
  }

/******************************************************************************************************************************
 *Redirecciona a la pagina inicial
 ******************************************************************************************************************************/
 function inicio(){
 	document.location.href='pedidosStock.php?wemp_pmla='+document.forms.forma.wemp_pmla.value;
 }
 /******************************************************************************************************************************
  *Redirecciona a la pagina inicial
  ******************************************************************************************************************************/
 function inicioListado(){
	 	document.location.href='pedidosStock.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=e';
 }
 /******************************************************************************************************************************
  *Redirecciona a la pagina inicial luego de grabar
  ******************************************************************************************************************************/
  function grabarInicio(mensaje){
  		document.location.href='pedidosStock.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&wmensaje='+mensaje;
  }
  /******************************************************************************************************************************
   *Redirecciona a la pagina inicial luego de grabar
   ******************************************************************************************************************************/
   function grabarInicioLista(mensaje){
   		document.location.href='pedidosStock.php?wemp_pmla='+document.forms.forma.wemp_pmla.value+'&waccion=e'+'&wmensaje='+mensaje;
   }
 /*****************************************************************************************************************************
  * Captura de onEnter con llamada a funcion parametrizada
  ******************************************************************************************************************************/
 function teclaEnter(e,accion){
	accion = accion.toString();
	var respuesta = true;
 	var tecla = (document.all) ? e.keyCode : e.which;

 	if(tecla==13){
 		eval(accion);
 	}
 	return respuesta;
 }

 /******************************************************************************************************************************
  *
  ******************************************************************************************************************************/
 function validarCantidad(id){
	 var maxPedido = document.getElementById("wtmaxpedido"+id);
	 var pedido = document.getElementById("wtpedido"+id);
	 var valido = false;

	 if(pedido && pedido.value && !isNaN(pedido.value) && maxPedido && maxPedido.value && !isNaN(maxPedido.value)){
		 try{
			if(parseInt(pedido.value) <= 0){
				pedido.value = '';
				alert("El valor a pedir debe ser mayor que cero.  Verifique #"+id);
				return;
			}
	 		valido = true;
		 }catch(e){}
	 } else {
		 if(pedido.value == ''){
			 valido = true;
		 } else {
			 pedido.value = '';
			 alert("Debe especificar una cantidad de pedido valida.  Verifique #"+id);
		 }
	 }

	 if(valido){
		 var elemento = document.getElementById("wtarticulo");
		 elemento.focus();
		 elemento.select();
	 } else {
		 pedido.focus();
		 pedido.select();
	 }
 }
 /******************************************************************************************************************************
 *
 ******************************************************************************************************************************/
 function validarCantidad1(id){
	 var maxPedido = document.getElementById("wtmaxpedido"+id);
	 var pedido = document.getElementById("wtpedido"+id);
	 var valido = false;

	 if(pedido && pedido.value && !isNaN(pedido.value) && maxPedido && maxPedido.value && !isNaN(maxPedido.value)){
		 try{
			if(parseInt(pedido.value) <= 0){
				pedido.value = '';
				alert("El valor a pedir debe ser mayor que cero.  Verifique #"+id);
				return;
			}
	 		valido = true;
		 }catch(e){}
	 } else {
		 if(pedido.value == ''){
			 valido = true;
		 } else {
			 pedido.value = '';
			 alert("Debe especificar una cantidad de pedido valida.  Verifique #"+id);
		 }
	 }

	 if(!valido){
		 pedido.focus();
		 pedido.select();
	 }
}
/******************************************************************************************************************************
*
******************************************************************************************************************************/
function abrirVentana(consecutivo){
	var codigo = "";

	if(document.getElementById("whcodigo" + consecutivo)){
		codigo = document.getElementById("whcodigo" + consecutivo).value;
	}

	if(codigo != ''){
		document.getElementById("wtvalarticulo").value = codigo;
		document.getElementById("wtvalcodigo").value = '';
		document.getElementById("wstxtresultado").value = '';
	}

	$.blockUI({ message: $('#validador') });
}
/******************************************************************************************************************************
*
******************************************************************************************************************************/
function terminarValidacion(){
	$.unblockUI();
}
/******************************************************************************************************************************
*
******************************************************************************************************************************/
function validarSaltarArticulo(){
	var codigo = document.getElementById("wtarticulo").value.toUpperCase();
	var equivalente = false;
	var mensaje = "";

		var parametros = "";
		parametros = "consultaAjaxInclude=01&basedatos="+document.forms.forma.wbasedato.value+"&codigoBarras="+codigo;

		try{
			ajax=nuevoAjax();

			ajax.open("POST", "../../../include/Movhos/movhos.inc.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			ajax.onreadystatechange=function(){
				if (ajax.readyState==4 && ajax.status==200){
					if(ajax.responseText != ''){
						codigo = ajax.responseText;
					}

					var existe = false;
					var cont1 = 1;

					//Consulta del articulo
					while(document.getElementById("whcodigo"+cont1)){
						if(codigo == document.getElementById("whcodigo"+cont1).value){
							existe = true;
							break;
						}
						cont1++;
					}

					//Salto al focus del articulo
					if(existe){
						document.getElementById("wtpedido"+cont1).focus();
						document.getElementById("wtpedido"+cont1).select();
					} else {
						document.getElementById("wtarticulo").focus();
						document.getElementById("wtarticulo").select();
						alert("El articulo no esta en la lista");
					}
				}
			}
			if ( !estaEnProceso(ajax) ) {
				ajax.send(null);
			}
		}catch(e){	}
}
/*****************************************************************************************************************************
 * Metodo de compensacion de despacho
 ******************************************************************************************************************************/
function compensarDespacho(){
	var servicioADespachar = document.getElementById("wsservicio").value;
	var servicioCargo = servicioADespachar;
	var documentoUnix = document.getElementById("wtdocumento").value;
	var fuenteTrasladoUnix = document.getElementById("wtfuente").value;

	var mensaje = "";
	var parametros = "";
	parametros = "consultaAjaxInclude=04&basedatos="+document.forms.forma.wbasedato.value+"&servicioCargo="+servicioCargo+"&servicioADespachar="+servicioADespachar+"&documentoUnix="+documentoUnix+"&fuenteTrasladoUnix="+fuenteTrasladoUnix;

		try{
			$.blockUI({ message: $('#msjEspere') });
			ajax=nuevoAjax();

			ajax.open("POST", "../../../include/Movhos/movhos.inc.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			ajax.onreadystatechange=function(){
				if (ajax.readyState==4 && ajax.status==200){
					if(ajax.responseText.indexOf("|") != -1){
						var vecArticulos = ajax.responseText.split(";");
						var vecItem = new Array();

						if(vecArticulos.length > 0){
							for(cont1 = 0; cont1 < vecArticulos.length;cont1++){
								vecItem = vecArticulos[cont1].split("|");

								var cont2 = 1;
								while(document.getElementById("whcodigo"+cont2)){
									if(vecItem[0] == document.getElementById("whcodigo"+cont2).value){
										document.getElementById("wtdespacho"+cont2).value = vecItem[1];

										//Diferencia de despacho en unix y cantidad digitada
										calcularDiferencia(cont2);
										break;
									}
									cont2++;
								}
							}
						} else {
							alert("No se encontro nada a compensar");
						}
					} else {
						alert("No se encontro el documento unix: '"+documentoUnix+"' para compensar");

						var cont2 = 1;
						while(document.getElementById("whcodigo"+cont2)){
							document.getElementById("wtdespacho"+cont2).value = '';
							calcularDiferencia(cont2);
							cont2++;
						}
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
 *
 ******************************************************************************************************************************/
function calcularDiferencia(posicion){
	var pedido 		= parseInt(document.getElementById("whpedido"+posicion).value);
	var despacho 	= parseInt(document.getElementById("wtdespacho"+posicion).value);

	if(!isNaN(despacho)){
		document.getElementById("wtdiferencia"+posicion).value = pedido - despacho;
	} else {
		document.getElementById("wtdiferencia"+posicion).value = '';
	}

	document.getElementById("tr"+posicion).className = "fila2";

	if(despacho < pedido || (pedido - despacho) < 0){
		document.getElementById("tr"+posicion).className = "fondoRojo";
	}

	if(pedido == despacho){
		document.getElementById("tr"+posicion).className = "fondoVerde";
	}
}
/*****************************************************************************************************************************
 * Captura de onEnter con llamada a funcion parametrizada con validacion de numeros entera
 ******************************************************************************************************************************/
function teclaEnterEntero(e,accion){
	var respuesta = validarEntradaEntera(e);
	var tecla = (document.all) ? e.keyCode : e.which;

	if(respuesta && tecla==13){
		eval(accion);
	}
	return respuesta;
}
/******************************************************************************************************************************
*
******************************************************************************************************************************/
function grabarPedido(){
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	var observaciones = document.getElementById("wtaobservaciones").value;
	var cco = document.forms.forma.wcco.value;
	var nombreCco = document.forms.forma.nombreCco.value;

	document.getElementById("btnGrabar").disabled = true;

	//Recorro los campos con los valores de pedido
	var pedido = "";
	var cntPedida = 0;
	var cntPendiente = 0;
	var cont1 = 1;
	var datos = "";

	while(document.getElementById("wtpedido"+cont1)){
		cntPedida = parseFloat(document.getElementById("wtpedido"+cont1).value == '' ? '0' : document.getElementById("wtpedido"+cont1).value);
		cntPendiente = parseFloat(document.getElementById("wtpendiente"+cont1).value == '' ? '0' : document.getElementById("wtpendiente"+cont1).value);

		pedido = cntPedida + cntPendiente;

		if(pedido > 0){
			datos += document.getElementById("whcodigo"+cont1).value+"|"+document.getElementById("wtmaxpedido"+cont1).value+"|"+pedido+";";
		}
		cont1++;
	}

	if(datos != ''){
		document.location.href = 'pedidosStock.php?wemp_pmla='+wemp_pmla+'&waccion=b'+'&wcco='+cco+'&wobservaciones='+observaciones+'&wdatos='+datos+'&nombreCco='+nombreCco;
	} else {
		alert("No digitó ninguna cantidad a pedir.");
	}
	document.getElementById("btnGrabar").disabled = false;
}
/******************************************************************************************************************************
*
******************************************************************************************************************************/
function anularPedido(){
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	var observaciones = document.getElementById("wtaobservaciones").value;
	var cco = document.forms.forma.wcco.value;
	var consecutivo = document.forms.forma.wconsecutivo.value;
	var nombreCco = document.forms.forma.nombreCco.value;

	if(cco != '' && consecutivo != ''){
		document.location.href = 'pedidosStock.php?wemp_pmla='+wemp_pmla+'&waccion=d'+'&wcco='+cco+'&wobservaciones='+observaciones+'&wconsecutivo='+consecutivo+'&nombreCco='+nombreCco;
	} else {
		alert("Revise los parametros.");
	}
}
/******************************************************************************************************************************
*
******************************************************************************************************************************/
function marcarAtendido(){
	var wemp_pmla = document.forms.forma.wemp_pmla.value;
	var observaciones = document.getElementById("wtaobservaciones").value;

	var cco = document.forms.forma.wsservicio.value;
	var nombreCco = document.forms.forma.nombreCco.value;
	var consecutivo = document.forms.forma.wconsecutivo.value;

	var fuenteUnix = document.forms.forma.wtdocumento.value;
	var documentoUnix = document.forms.forma.wtfuente.value;

	//Recorro los campos con los valores de despacho
	var cont1 = 1;
	var datos = "";

	while(document.getElementById("wtdespacho"+cont1)){
		if(document.getElementById("wtdespacho"+cont1).value != ''){
			datos += document.getElementById("whcodigo"+cont1).value+"|"+document.getElementById("wtdespacho"+cont1).value+"|"+document.getElementById("whpedido"+cont1).value+"|"+fuenteUnix+"|"+documentoUnix+";";
		} else {
			datos += document.getElementById("whcodigo"+cont1).value+"|0;";
		}
		cont1++;
	}

	if(cco != '' && consecutivo != ''){
		document.location.href = 'pedidosStock.php?wemp_pmla='+wemp_pmla+'&waccion=g'+'&wcco='+cco+'&wobservaciones='+observaciones+'&wconsecutivo='+consecutivo+'&nombreCco='+nombreCco+'&wdatos='+datos;
	} else {
		alert("Revise los parametros.");
	}
}
/******************************************************************************************************************************
*
******************************************************************************************************************************/
function verDetallePedido(cco,consecutivo){
	var wemp_pmla = document.forms.forma.wemp_pmla.value;

 	if(cco != ''){
 		document.location.href = 'pedidosStock.php?wemp_pmla='+wemp_pmla+'&waccion=h&wcco='+cco+'&wconsecutivo='+consecutivo;
 	} else {
  		alert("Debe especificar un centro de costos");
  	}
}

</script>
<?php
include_once("conex.php");
/**
 * Inicio de la aplicacion
 *
 * MODIFICACIONES:
 * Julio 9 de 2012 Viviana Rodas
 * Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos de un grupo seleccionado y dibujarSelect * que dibuja el select con los centros de costos obtenidos de la primera funcion.
 *
 * 2010-08-18  (Msanchez):  Se modifica la aplicación para que el codigo de fuente de unix sea solicitado por pantalla, ya que pueden ser dos fuentes por servicio.
 */
include_once("movhos/movhos.inc.php");

/******************************
 * CLASES
 ******************************/
class EncabezadoPedido{
	var $fechaPedido = "";
	var $horaPedido = "";
	var $servicio = "";
	var $consecutivo = "";
	var $esAnulado = "";
	var $esAnulable = "";
	var $esAtendido = "";
	var $fechaAnulacion = "";
	var $horaAnulacion = "";
	var $observaciones = "";
	var $usuarioAtendio = "";
	var $fechaAtencion = "";
	var $horaAtencion = "";
	var $usuario = "";
}

class DetallePedido{
	var $fechaPedido = "";
	var $horaPedido = "";
	var $consecutivo = "";
	var $servicio = "";
	var $articulo = "";
	var $saldo = "";
	var $cantidad = "";
	var $presentacion = "";
	var $despachado = "";

	var $documentoUnix = "";
	var $fuenteUnix = "";
}

class ArticuloConsumo{
	var $codigo = "";
	var $nombre = "";
	var $unidad = "";
}
/******************************
 * METODOS
 ******************************/
function consultarUltimoConsecutivoPedido($servicio){
	global $wbasedato;
	global $conex;

	$consecutivo = "1";

	$q = "SELECT
			Ccoped
		FROM
			".$wbasedato."_000011
		WHERE
			Ccocod = '$servicio';";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){
		$info = mysql_fetch_array($res);
		$consecutivo = $info['Ccoped'];
	}
	return $consecutivo;
}

function grabarEncabezado($encabezado,$usuario){
	global $wbasedato;
	global $conex;

	$q = "INSERT INTO ".$wbasedato."_000086 (
				Medico,Fecha_data,Hora_data,Enpser,Enpcon,Enpanu,Enpfan,Enpobs,Enpuan,Enphan,Enpatn,Enpuat,Enpfat,Enphat,Seguridad ) VALUES
			('movhos','".date("Y-m-d")."','".date("H:i:s")."','".$encabezado->servicio."','".$encabezado->consecutivo."','".$encabezado->esAnulado."','".$encabezado->fechaAnulacion."','$encabezado->observaciones','','00:00:00','off','','0000-00-00','00:00:00','A-$usuario')";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$q = "UPDATE ".$wbasedato."_000011 SET
			Ccoped = Ccoped + 1
		WHERE
			Ccocod = '$encabezado->servicio';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

function grabarDetalle($col,$usuario){
	global $wbasedato;
	global $conex;

	foreach ($col as $articulo){
		$q = "INSERT INTO ".$wbasedato."_000087 (
				Medico,Fecha_data,Hora_data,Depcon,Depser,Depart,Depsal,Depcan,Depdes,Depdou,Depftu,Seguridad ) VALUES
			('movhos','".date("Y-m-d")."','".date("H:i:s")."','".$articulo->consecutivo."','".$articulo->servicio."','".$articulo->articulo."','".$articulo->saldo."','$articulo->cantidad','0','$articulo->documentoUnix','$articulo->fuenteUnix','A-$usuario')";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}
}

function consultarEncabezadoPedido($servicio,$consecutivo){
	global $wbasedato;
	global $conex;

	$ultimoConsecutivo = "";
	$encabezado = new EncabezadoPedido();

	if($consecutivo != ""){   //Consulta de pedido por numero y servicio
		$q3 = "SELECT
				Ccoped
			FROM
				".$wbasedato."_000011
			WHERE
				Ccocod = '$servicio';";
		$res3 = mysql_query($q3,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$info3 = mysql_fetch_array($res3);

		$ultimoConsecutivo = $info3['Ccoped'];

		$q = "SELECT
				Fecha_data,Hora_data,Enpser,Enpcon,Enpanu,Enpfan,Enpobs,Enpuan,Enphan,Enpatn,Enpuat,Enpfat,Enphat,(SELECT Descripcion FROM usuarios WHERE Codigo = SUBSTRING_INDEX(".$wbasedato."_000086.Seguridad,'-',1)) Nombre
			FROM
				".$wbasedato."_000086
			WHERE
				Enpser = '$servicio'
				AND Enpcon = '$consecutivo';";
	} else {			      //Consulta de ultimo pedido
		$q = "SELECT
				Fecha_data,Hora_data,Enpser,Enpcon,Enpanu,Enpfan,Enpobs,Enpuan,Enphan,Enpatn,Enpuat,Enpfat,Enphat,(SELECT Descripcion FROM usuarios WHERE Codigo = SUBSTRING_INDEX(".$wbasedato."_000086.Seguridad,'-',1)) Nombre
			FROM
				".$wbasedato."_000086
			WHERE
				Enpser = '$servicio'
				AND Enpanu = 'off'
				AND Enpatn = 'off'
				AND Enpcon = (SELECT MAX(CAST(Enpcon AS SIGNED)) FROM ".$wbasedato."_000086 WHERE Enpser = '$servicio');";
	}

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){
		$info = mysql_fetch_array($res);

		$encabezado->consecutivo = $info['Enpcon'];
		$encabezado->esAnulado = $info['Enpanu'];
		$encabezado->fechaAnulacion = $info['Enpfan'];
		$encabezado->horaAnulacion = $info['Enphan'];
		$encabezado->fechaPedido = $info['Fecha_data'];
		$encabezado->horaPedido = $info['Hora_data'];
		$encabezado->observaciones = $info['Enpobs'];
		$encabezado->servicio = $info['Enpser'];
		$encabezado->usuario = (isset($info['Nombre']) && !empty($info['Nombre'])) ? $info['Nombre'] : "";
		$encabezado->usuarioAnulacion = $info['Enpuan'];
		$encabezado->esAtendido = $info['Enpatn'];
		$encabezado->usuarioAtendio = $info['Enpuat'];
		$encabezado->fechaAtencion = $info['Enpfat'];
		$encabezado->horaAtencion = $info['Enphat'];

		if($ultimoConsecutivo == $encabezado->consecutivo && $encabezado->esAtendido == "off"){
			$encabezado->esAnulable = true;
		} else {
			$encabezado->esAnulable = false;
		}
	}

	return $encabezado;
}

function consultarDetallePedido($consecutivo,$servicio){
	global $wbasedato;
	global $conex;

	$col = array();

	$q = "SELECT
			Fecha_data,Hora_data,Depcon,Depser,Depart,Depsal,Depcan,Depdes,(SELECT Descripcion FROM usuarios WHERE Codigo = SUBSTRING_INDEX(".$wbasedato."_000087.Seguridad,'-',1)) Nombre
		FROM
			".$wbasedato."_000087
		WHERE
			Depcon = '$consecutivo'
			AND Depser = '$servicio';";

		$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		for($cont1 = 0; $cont1 < $num;$cont1++){

			$info = mysql_fetch_array($res);
			$elemento = new DetallePedido();

			//*************************Descripcion del articulo
			$q2 = "SELECT Artcom, Artuni FROM ".$wbasedato."_000026 WHERE Artcod = '".$info['Depart']."'";
			$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$info2 = mysql_fetch_array($res2);
			//*************************

			//*************************Descripcion de la unidad
			$q3 = "SELECT Unides FROM ".$wbasedato."_000027 WHERE Unicod = '".$info2['Artuni']."'";
			$res3 = mysql_query($q3,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
			$info3 = mysql_fetch_array($res3);
			//*************************

			$elemento->articulo = $info['Depart']." - ".$info2['Artcom'];
			$elemento->cantidad = $info['Depcan'];
			$elemento->consecutivo = $info['Depcon'];
			$elemento->fechaPedido = $info['Fecha_data'];
			$elemento->horaPedido = $info['Hora_data'];
			$elemento->saldo = $info['Depsal'];
			$elemento->servicio = $info['Depser'];
			$elemento->usuario = (isset($info['Nombre']) && !empty($info['Nombre'])) ? $info['Nombre'] : "";
			$elemento->presentacion = $info2['Artuni']." - ".$info3['Unides'];
			$elemento->despachado = $info['Depdes'];

			$col[] = $elemento;
		}
	return $col;
}

function anularPedido($servicio,$consecutivo,$observaciones,$usuario){
	global $wbasedato;
	global $conex;

	$q = "UPDATE ".$wbasedato."_000086 SET
			Enpanu = 'on',
			Enpfan = '".date("Y-m-d")."',
			Enphan = '".date("H:i:s")."',
			Enpobs = '$observaciones',
			Enpuan = '$usuario'
		WHERE
			Enpser = '$servicio'
			AND Enpcon = '$consecutivo'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	return true;
}

function actualizarDespachoDetalle($detalle){
	global $wbasedato;
	global $conex;

	$q = "UPDATE ".$wbasedato."_000087 SET
			Depdes = '$detalle->despachado',
			Depdou = '$detalle->documentoUnix',
			Depftu = '$detalle->fuenteUnix'
		WHERE
			Depser = '$detalle->servicio'
			AND Depcon = '$detalle->consecutivo'
			AND Depart = '$detalle->articulo'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	return true;
}

function marcarPedidoAtendido($servicio,$consecutivo,$observaciones,$usuario){
	global $wbasedato;
	global $conex;

	$q = "UPDATE ".$wbasedato."_000086 SET
			Enpatn = 'on',
			Enpfat = '".date("Y-m-d")."',
			Enphat = '".date("H:i:s")."',
			Enpobs = CONCAT(Enpobs,'".chr(13).chr(13)."','$observaciones'),
			Enpuat = '$usuario'
		WHERE
			Enpser = '$servicio'
			AND Enpcon = '$consecutivo'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	return true;
}

function consultarPedidosPendientes($servicio){
	global $wbasedato;
	global $conex;

	$col = array();

	$q = "SELECT
			Fecha_data,Hora_data,Enpser,Enpcon,Enpanu,Enpfan,Enpobs,Enpuan,Enphan,Enpatn,Enpuat,Enpfat,Enphat,(SELECT Descripcion FROM usuarios WHERE Codigo = SUBSTRING(".$wbasedato."_000086.Seguridad FROM INSTR(".$wbasedato."_000086.Seguridad,'-')+1)) Nombre
		FROM
			".$wbasedato."_000086
		WHERE
			Enpser LIKE '$servicio'
			AND Enpanu != 'on'
			AND Enpatn != 'on'
		ORDER BY
			Fecha_data,Hora_data,Enpser;";

		$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		for($cont1 = 0; $cont1 < $num;$cont1++){

			$info = mysql_fetch_array($res);

			$elemento = new EncabezadoPedido();

			$elemento->consecutivo 		= $info['Enpcon'];
			$elemento->esAnulado 		= $info['Enpanu'];
			$elemento->esAtendido 		= $info['Enpatn'];
			$elemento->fechaAnulacion 	= $info['Enpfan'];
			$elemento->fechaAtencion 	= $info['Enpfat'];
			$elemento->fechaPedido 		= $info['Fecha_data'];
			$elemento->horaAnulacion 	= $info['Enphan'];
			$elemento->horaAtencion 	= $info['Enphat'];
			$elemento->horaPedido		= $info['Hora_data'];
			$elemento->observaciones	= $info['Enpobs'];

			//***************************************************************************
			$q2 = "SELECT Cconom FROM ".$wbasedato."_000011 WHERE Ccocod = '".$info['Enpser']."'";
			$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$info2 = mysql_fetch_array($res2);
			//***************************************************************************

			$elemento->servicio			= $info['Enpser']." - ".$info2['Cconom'];
			$elemento->usuario			= $info['Nombre'];
			$elemento->usuarioAtendio	= $info['Enpuat'];

			$col[] = $elemento;
		}
	return $col;
}

function consultarArticulosConsumo(){
	global $wbasedato;
	global $conex;

	$col = array();

	$q = "SELECT
			Artcod,Artcom,Artuni
		FROM
			".$wbasedato."_000026
		WHERE
			Artest = 'on'
			AND SUBSTRING_INDEX( Artgru, '-', 1 ) = 'VVG'
		ORDER BY
			Artcom;";

		$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		for($cont1 = 0; $cont1 < $num;$cont1++){

			$info = mysql_fetch_array($res);

			$elemento = new ArticuloConsumo();

			//***************************************************************************
			$q2 = "SELECT Unides FROM ".$wbasedato."_000027 WHERE Unicod = '".$info['Artuni']."'";
			$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$info2 = mysql_fetch_array($res2);
			//***************************************************************************

			$elemento->codigo 			= $info['Artcod'];
			$elemento->nombre 			= $info['Artcom'];
			$elemento->unidad 			= $info['Artuni']." - ".$info2['Unides'];

			$col[] = $elemento;
		}
	return $col;
}

function consultarArticulosCco($servicio){
	global $wbasedato;
	global $conex;

	$col = array();

	$q = "SELECT
			Artcod,Artcom,Artuni
		FROM
			".$wbasedato."_000091, ".$wbasedato."_000026
		WHERE
			Arsest = 'on'
			AND Artest = 'on'
			AND Artcod = Arscod
			AND Arscco = '$servicio'
		ORDER BY
			Artcom;";

		$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		for($cont1 = 0; $cont1 < $num;$cont1++){

			$info = mysql_fetch_array($res);

			$elemento = new ArticuloConsumo();

			//***************************************************************************
			$q2 = "SELECT Unides FROM ".$wbasedato."_000027 WHERE Unicod = '".$info['Artuni']."'";
			$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
			$info2 = mysql_fetch_array($res2);
			//***************************************************************************

			$elemento->codigo 			= $info['Artcod'];
			$elemento->nombre 			= $info['Artcom'];
			$elemento->unidad 			= $info['Artuni']." - ".$info2['Unides'];

			$col[] = $elemento;
		}
	return $col;
}
function centrosCostosPedido(){
	global $wbasedato;
	global $conex;

  	$q = "SELECT
				ccocod, UPPER(Cconom)
			FROM
				".$wbasedato."_000011
			WHERE
  				Ccostk = 'on'
			ORDER by 1;";

  	$res1 = mysql_query($q,$conex);
  	$num1 = mysql_num_rows($res1);

  	$coleccion = array();

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

/******************************************************************************************
 * Inicio de la aplicacion
 ******************************************************************************************/
$usuarioValidado = true;
$wactualiz = "2012-07-09";

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

//Estas variables se incluyen para variar la empresa y el codigo de base de datos (esquema a apuntar).  Por defecto sera la 01
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

//Encabezado
encabezado("Pedidos del stock",$wactualiz,"clinica");
$usuarioValidado = true;

if (!$usuarioValidado){
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} else {
	//Conexion base de datos
	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$winstitucion = $institucion->nombre;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$conexunix = odbc_connect('inventarios','informix','sco') or die("No se ralizo Conexion con el Unix");

	//Mensaje de espera
	echo "<div id='msjEspere' style='display:none;'>";
    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...";
	echo "</div>";

	echo "<form name='forma' action='pedidosStock.php' method=post>";

  	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";

	if(!isset($waccion)){
		$waccion = "";
	}

	$wcco1=explode("-",$wcco);
	if(count($wcco1)>1)
	{
		$wcco=$wcco1[0];
		$nombreCco=$wcco1[1]." ".$wcco1[2];
	}
	if(isset($nombreCco) && !empty($nombreCco)){
		echo "<input type='hidden' name='nombreCco' value='$nombreCco'>";
	}


	switch ($waccion){
		case 'a': //Consulta

			echo "<input type=hidden name=wcco value='$wcco'>";

			echo "<table align='center' border=0>";

			echo '<span class="subtituloPagina2">';
			echo "Validaci&oacute;n de art&iacute;culo";
			echo "</span>";
			echo "<br>";
			echo "<br>";

			echo "<tr>";
			echo "<td class=fila1 width=150>Articulo</td>";
			echo "<td class=fila2><INPUT TYPE='text' id='wtarticulo' NAME='wtarticulo' value='' SIZE=10 onkeypress='return teclaEnter(event,"."\"validarSaltarArticulo();\");' class='textoNormal'></td>";
			echo "</tr>";

			echo "<tr><td class=fila1 colspan=2 align=center>Observaciones</td></tr>";
			echo "<tr>";
			echo "<td class=fila2 colspan=2><textarea id='wtaobservaciones' cols=35 rows=4></textarea></td>";
			echo "</tr>";

			echo "</table>";

			echo "<br>";
			echo "<br>";

			//Consulta del ultimo pedido realizado con el fin de traer los saldos pendientes de despacho
			$ultimoPedido = consultarEncabezadoPedido($wcco,"");
			$detalleUltimoPedido = consultarDetallePedido($ultimoPedido->consecutivo,$wcco);

			$arrSaldosAnteriores = array();

			foreach ($detalleUltimoPedido as $articulo){
				$vecArticulo = explode(" - ",$articulo->articulo);

				if($articulo->cantidad != $articulo->despachado){
					$arrSaldosAnteriores[$vecArticulo[0]] = $articulo->cantidad - $articulo->despachado;
				}
			}

			//Si no existe listado de articulos propio en la tabla 91.
			//Articulos del maestro propio
			$colCco = consultarArticulosCco($wcco);

			if(count($colCco)>0){
				echo "<table align='center' border=0>";

				echo "<tr><td align=center colspan=6><br><input type='button' id='btnGrabar' value='Grabar pedido' onClick='javascript:grabarPedido();'>&nbsp;|&nbsp;<input type='button' value='Regresar' onClick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr>";

				echo "<tr><td colspan=6 align=left><br><b>Articulos del centro de costos</b><br></td></tr>";

				echo "<tr class=encabezadoTabla>";
				echo "<td>#</td>";
				echo "<td>Código</td>";
				echo "<td>Descripción</td>";
				echo "<td>Presentación</td>";
				echo "<td>Pendiente <br>despacho</td>";
				echo "<td>Pedido</td>";
				echo "</tr>";

				$cont2 = 1;
				$i = 1;
				$wclass = "";

				foreach ($colCco as $art){
					$wclass == "fila1" ? $wclass = "fila2" : $wclass = "fila1";

					echo "<tr class=".$wclass.">";
					echo "<td>$i<INPUT TYPE='hidden' id='whcodigo$i' NAME='whcodigo$i' value='".$art->codigo."'></td>";
					echo "<td>".$art->codigo."</td>";
					echo "<td>".$art->nombre."</td>";
					echo "<td>".$art->unidad."</td>";
					echo "<td align=center>";

					if(isset($arrSaldosAnteriores[$art->codigo]) && $arrSaldosAnteriores[$art->codigo] != ''){
						echo "<INPUT TYPE='text' id='wtpendiente$i' NAME='wtpendiente$i' value='".$arrSaldosAnteriores[$art->codigo]."' SIZE=10 class='textoNormal' readonly>";
					} else {
						echo "<INPUT TYPE='text' id='wtpendiente$i' NAME='wtpendiente$i' value='' SIZE=10 class='textoNormal' readonly>";
					}
					echo "</td>";
					echo "<td align=center>";
					echo "<INPUT TYPE='hidden' id='wtmaxpedido$i' NAME='wtmaxpedido$i' value='0'>";
					echo "<INPUT TYPE='text' id='wtpedido$i' NAME='wtpedido$i' value='' SIZE=10 onChange='javascript:validarCantidad1($i);' onkeypress='return teclaEnter(event,"."\"validarCantidad($i);\");' class='textoNormal'>";
					echo "</td>";

					$i++;
					$cont2++;
				}
			} else {

				//Verifico que no existan pedidos pendientes
				$encabezado = consultarEncabezadoPedido($wcco,"");
				$q = "SELECT salart, artnom, saluni, uninom, (salant+salent-salsal) AS saldo "
				."   FROM ivsal, ivart, ivuni "
				."  WHERE salser                  = '".$wcco."'"
				."    AND salano                  = '".date("Y")."'"
				."    AND salmes                  = '".date("m")."'"
				."    AND (salant+salent-salsal) != 0 "
				."    AND salart                  = artcod "
				."    AND artuni                  = unicod "
				."  ORDER BY 2 ";

				$res = odbc_do($conexunix,$q);

				echo "<table align='center' border=0>";

				echo "<tr><td align=center colspan=6><br><input type='button' id='btnGrabar' value='Grabar pedido' onClick='javascript:grabarPedido();'>&nbsp;|&nbsp;<input type='button' value='Regresar' onClick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr>";

				echo '<span class="subtituloPagina2">';
				echo "Servicio o Unidad: $nombreCco";
				echo "</span>";
				echo "<br>";
				echo "<br>";

				echo "<tr class=encabezadoTabla>";
				echo "<td>#</td>";
				echo "<td>Código</td>";
				echo "<td>Descripción</td>";
				echo "<td>Presentación</td>";
				echo "<td>Saldo Actual</td>";
				echo "<td>Pendiente <br>despacho</td>";
				echo "<td>Pedido</td>";
				//	  		echo "<td>Validacion</td>";
				echo "</tr>";

				$i=1;
				$wclass = "";

				while(odbc_fetch_row($res)){
					$wclass == "fila1" ? $wclass = "fila2" : $wclass = "fila1";

					echo "<tr class=".$wclass.">";
					echo "<td>$i<INPUT TYPE='hidden' id='whcodigo$i' NAME='whcodigo$i' value='".odbc_result($res,1)."'></td>";
					echo "<td>".odbc_result($res,1)."</td>";
					echo "<td>".odbc_result($res,2)."</td>";
					echo "<td>".odbc_result($res,3)." - ".odbc_result($res,4)."</td>";
					echo "<td align=right><INPUT TYPE='text' id='wtmaxpedido$i' NAME='wtmaxpedido$i' value='".odbc_result($res,5)."' SIZE=13 onKeyPress='return validarEntradaDecimal(event);' class='textoNormal' readonly></td>";
					echo "<td align=center>";
					if(isset($arrSaldosAnteriores[odbc_result($res,1)]) && $arrSaldosAnteriores[odbc_result($res,1)] != ''){
						echo "<INPUT TYPE='text' id='wtpendiente$i' NAME='wtpendiente$i' value='".$arrSaldosAnteriores[odbc_result($res,1)]."' SIZE=10 class='textoNormal' readonly>";
					} else {
						echo "<INPUT TYPE='text' id='wtpendiente$i' NAME='wtpendiente$i' value='' SIZE=10 class='textoNormal' readonly>";
					}
					echo "</td>";
					echo "<td align=right><INPUT TYPE='text' id='wtpedido$i' NAME='wtpedido$i' value='' SIZE=10 onChange='javascript:validarCantidad1($i);' onkeypress='return teclaEnter(event,"."\"validarCantidad($i);\");' class='textoNormal'></td>";

					$i++;
				}

				//Articulos de consumo
				$colConsumo = consultarArticulosConsumo();

				if(count($colConsumo)>0){

					echo "<tr><td colspan=6 align=left><br><b>Articulos de consumo</b><br></td></tr>";

					echo "<tr class=encabezadoTabla>";
					echo "<td>#</td>";
					echo "<td>Código</td>";
					echo "<td>Descripción</td>";
					echo "<td>Presentación</td>";
					echo "<td>Pendiente <br>despacho</td>";
					echo "<td>Pedido</td>";
					echo "</tr>";

					$cont2 = 1;

					foreach ($colConsumo as $artConsumo){
						$wclass == "fila1" ? $wclass = "fila2" : $wclass = "fila1";

						echo "<tr class=".$wclass.">";
						echo "<td>$i<INPUT TYPE='hidden' id='whcodigo$i' NAME='whcodigo$i' value='".$artConsumo->codigo."'></td>";
						echo "<td>".$artConsumo->codigo."</td>";
						echo "<td>".$artConsumo->nombre."</td>";
						echo "<td>".$artConsumo->unidad."</td>";
						echo "<td align=center>";
						if(isset($arrSaldosAnteriores[$artConsumo->codigo]) && $arrSaldosAnteriores[$artConsumo->codigo] != ''){
							echo "<INPUT TYPE='text' id='wtpendiente$i' NAME='wtpendiente$i' value='".$arrSaldosAnteriores[$artConsumo->codigo]."' SIZE=10 class='textoNormal' readonly>";
						} else {
							echo "<INPUT TYPE='text' id='wtpendiente$i' NAME='wtpendiente$i' value='' SIZE=10 class='textoNormal' readonly>";
						}
						echo "</td>";
						echo "<td align=center><INPUT TYPE='hidden' id='wtmaxpedido$i' NAME='wtmaxpedido$i' value='0'><INPUT TYPE='text' id='wtpedido$i' NAME='wtpedido$i' value='' SIZE=10 onChange='javascript:validarCantidad1($i);' onkeypress='return teclaEnter(event,"."\"validarCantidad($i);\");' class='textoNormal'></td>";

						$i++;
						$cont2++;
					}
				}
			}
			echo "<tr><td align=center colspan=6><br><input type='button' id='btnGrabar' value='Grabar pedido' onClick='javascript:grabarPedido();'>&nbsp;|&nbsp;<input type='button' value='Regresar' onClick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr>";

			echo "</table>";
			break;
		case 'b': //Grabar
			if(isset($wdatos) && !empty($wdatos)){
				if(isset($wcco)){
					$col = array();

					//Consulto el ultimo consecutivo de pedido
					$consecutivo = consultarUltimoConsecutivoPedido($wcco)+1;

					//Crear el encabezado
					$encabezado = new EncabezadoPedido();

					$encabezado->consecutivo = $consecutivo;
					$encabezado->esAnulado = "off";
					$encabezado->fechaAnulacion = "0000-00-00";
					$encabezado->fechaPedido = date("Y-m-d");
					$encabezado->horaPedido = date("H:i:s");
					$encabezado->observaciones = $wobservaciones;
					$encabezado->servicio = $wcco;


					$vecArticulos = explode(";",$wdatos);  //Division por articulo

					//Iteracion sobre todos los articulos
					foreach ($vecArticulos as $articulo){
						$vecParametros = explode("|",$articulo);  //Division por datos

						if($vecParametros[0] != ''){

							//Crear el encabezado
							$detalle = new DetallePedido();

							$detalle->fechaPedido = $encabezado->fechaPedido;
							$detalle->horaPedido = $encabezado->horaPedido;
							$detalle->articulo = $vecParametros[0];
							$detalle->saldo = $vecParametros[1];
							$detalle->cantidad = $vecParametros[2];
							$detalle->consecutivo = $encabezado->consecutivo;
							$detalle->servicio = $encabezado->servicio;


							//Completar documento y fuente
							$detalle->fuenteUnix = "";
							$detalle->documentoUnix = "";

							$col[] = $detalle;
						}
					}

					//Grabar encabezado del stock
					grabarEncabezado($encabezado,$wuser);

					//Grabar detalle
					grabarDetalle($col,$wuser);

					//Si el pedido anterior no tiene despacho completo, el saldo se trajo al pedido que acaba de grabarse
					//Consulta del ultimo pedido realizado con el fin de traer los saldos pendientes de despacho
					$pedidoAnteriorDespachadoCompletamente = true;

					$detalleUltimoPedido = consultarDetallePedido($consecutivo-1,$wcco);
					foreach ($detalleUltimoPedido as $articulo){
						if($articulo->cantidad != $articulo->despachado){
							$pedidoAnteriorDespachadoCompletamente = false;
							break;
						}
					}

					//El pedido anterior se marca como atendido si no esta despachado completamente y se realizo el actual
					$pedidoAnteriorDespachadoCompletamente ? $observacionAtencion = "" : $observacionAtencion = "Reemplazado por pedido $consecutivo";

					$atendidoAnterior = marcarPedidoAtendido($wcco,$consecutivo-1,$observacionAtencion,$wuser);
				} else {
					mensajeEmergente("No se especificó centro de costos");
				}
			} else {
				mensajeEmergente("Nada que grabar en el pedido");
			}

			funcionJavascript("grabarInicio('Pedido número $encabezado->consecutivo grabado, para el servicio $encabezado->servicio - $nombreCco');");
			break;
		case 'c': //Consultar pedido
			echo "<input type=hidden name=wcco value='$wcco'>";
			echo "<input type=hidden name=wconsecutivo value='$wconsecutivo'>";

			if(isset($wcco) && !empty($wcco) && isset($wconsecutivo) && !empty($wconsecutivo)){
				$col = array();

				//Crear el encabezado
				$encabezado = new EncabezadoPedido();

				$vecCco=$wcco;
				$vecCco1=$nombreCco;


				//echo "<input type=hidden name=nombreCco value='$vecCco1'>";

				$encabezado = consultarEncabezadoPedido($vecCco,$wconsecutivo);
				$col = consultarDetallePedido($encabezado->consecutivo,$encabezado->servicio);

				if(count($col) > 0){
					echo "<table align='center' border=0>";

					echo "<tr>";

					//Consecutivo
					echo "<td class=fila1>";
					echo "N&uacute;mero de pedido";
					echo "</td>";

					echo "<td class=fila2 colspan=2>";
					echo "$encabezado->consecutivo";
					echo "</td>";

					echo "</tr>";
					echo "<tr>";

					//Centro de costos
					echo "<td class=fila1>";
					echo "Centro de costos";
					echo "</td>";

					echo "<td class=fila2 colspan=2>";
					echo "$encabezado->servicio $vecCco1";
					echo "</td>";

					echo "</tr>";
					echo "<tr>";

					//Estado anulacion
					echo "<td class=fila1>";
					echo "Estado";
					echo "</td>";

					echo "<td class=fila2 colspan=2>";
					if($encabezado->esAnulado == 'on'){
						echo "Anulado";
					} else {
						if($encabezado->esAtendido == 'on'){
							echo "Atendido";
						} else {
							echo "Pendiente";
						}
					}
					echo "</td>";
					echo "</tr>";

					echo "<tr><td class=fila2 colspan=3 align=center>Observaciones</td></tr>";
					echo "<tr>";
					if($encabezado->esAnulado == 'off' && $encabezado->esAnulable){
						echo "<td align=center colspan=3><textarea id='wtaobservaciones' cols=35 rows=4>$encabezado->observaciones</textarea></td>";
					} else {
						echo "<td align=center colspan=3><textarea id='wtaobservaciones' cols=35 rows=4 readonly>$encabezado->observaciones</textarea></td>";
					}
					echo "</tr>";

					echo "<tr><td align=center colspan=6><br>";
					if($encabezado->esAnulado == 'off' && $encabezado->esAnulable){
						echo "<input type='button' id='btnGrabar' value='Anular pedido' onClick='javascript:anularPedido();'>&nbsp;|&nbsp;";
					}
					echo "<input type='button' value='Regresar' onClick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b><br><br></td></tr>";

					echo "<tr class=encabezadoTabla>";
					echo "<td>Articulo</td>";
					echo "<td>Presentación</td>";
					echo "<td>Pedido</td>";

					if($encabezado->esAtendido == 'on'){
						echo "<td>Despachado</td>";
					}
					echo "</tr>";

					$clase="fila2";

					//Iteracion sobre todos los articulos
					foreach ($col as $articulo){
						$clase == "fila1" ? $clase = "fila2" : $clase = "fila1";

						echo "<tr class=$clase>";

						echo "<td>$articulo->articulo</td>";
						echo "<td>$articulo->presentacion</td>";
						echo "<td align=right>$articulo->cantidad</td>";
						if($encabezado->esAtendido == 'on'){
							echo "<td align=right>$articulo->despachado</td>";
						}
						echo "</tr>";
					}

					echo "<tr><td align=center colspan=6><br>";
					if($encabezado->esAnulado == 'off' && $encabezado->esAnulable){
						echo "<input type='button' id='btnGrabar' value='Anular pedido' onClick='javascript:anularPedido();'>&nbsp;|&nbsp;";
					}
					echo "<input type='button' value='Regresar' onClick='javascript:inicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b><br><br></td></tr>";

					echo "</table>";
				} else {
					funcionJavascript("grabarInicio('No existe el numero de pedido.');");
				}
			}
			break;
		case 'd': //Anular pedido
			if(isset($wcco) && !empty($wcco) && isset($wconsecutivo) && !empty($wconsecutivo)){
				$vecCco=$wcco;

				echo "usuario '$wuser'";
				if(anularPedido($vecCco,$wconsecutivo,$wobservaciones,$wuser)){
					funcionJavascript("grabarInicio('El pedido número $wconsecutivo del servicio $vecCco - $nombreCco ha sido anulado.');");
				}
			}
			break;
		case 'e': //Filtro para el encargado de recibir los pedidos
			echo "<table align='center' border=0>";

			echo '<span class="subtituloPagina2">';
			echo "Pedidos pendientes";
			echo "</span>";
			echo "<br>";
			echo "<br>";
			echo "</table>";

			//Servicio

			$cco="Ccostk";
		    $sub="off";
		    $tod="Todos";
		    $ipod="off";
		    //$cco=" ";
		    $centrosCostos = consultaCentrosCostos($cco);

		    echo "<table align='center' border=0 >";
		    $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wsservicio");

		    echo $dib;
		    echo "</table>";

		//	echo "</td>";
		//	echo"</tr>";
			echo"<table align=center>";
			echo "<tr>";

			if(isset($wmensaje) && !empty($wmensaje)){
				echo "<td class=fila2 align=center colspan=2>";
				echo "<font size=4><b>$wmensaje</b></font>";
				echo "</td>";
			} else {
				echo "<td>";
				echo "<br>";
				echo "</td>";
			}
			echo "</tr>";

			echo "<tr><td align=center colspan=2><input type='button' value='Consultar' onClick='javascript:consultarPedidoServicio();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr></center>";

			echo "</table>";
			break;
		case 'f': //Consultar pedido
			$vecCco = $wcco;

			echo "<input type=hidden name=wcco value='$wcco'>";
			$colEnc = consultarPedidosPendientes($vecCco);

			if(count($colEnc)>0){
				echo "<table align='center' border=0>";
				echo "<tr class=encabezadoTabla>";
				echo "<td>N&uacute;mero de pedido</td>";
				echo "<td>Servicio</td>";
				echo "<td>Fecha pedido</td>";
				echo "<td>Hora pedido</td>";
				echo "<td>Observaciones</td>";
				echo "<td>Solicitado por</td>";
				echo "<td>Ver</td>";
				echo "</tr>";

				$clase="fila2";

				//Iteracion sobre todos los articulos
				foreach ($colEnc as $encabezado){
					$clase == "fila1" ? $clase = "fila2" : $clase = "fila1";

					echo "<tr class=$clase>";

					echo "<td>$encabezado->consecutivo</td>";
					echo "<td>$encabezado->servicio</td>";
					echo "<td>$encabezado->fechaPedido</td>";
					echo "<td>$encabezado->horaPedido</td>";
					echo "<td>$encabezado->observaciones</td>";
					echo "<td>$encabezado->usuario</td>";
					echo "<td align=right><a href='#1' onClick='javascript:verDetallePedido(\"$encabezado->servicio\",\"$encabezado->consecutivo\");'>Detalle</a></td>";
					echo "</tr>";
				}
				echo "<tr><td align=center colspan=6><br>";
				echo "<input type='button' value='Regresar' onClick='javascript:inicioListado();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b><br><br></td></tr>";

				echo "</table>";
			} else {
				funcionJavascript("grabarInicioLista('No hay pedidos pendientes en el servicio $vecCco - $nombreCco');");
			}
			break;
		case 'g': //Marcar pedido
			if(isset($wcco) && !empty($wcco) && isset($wconsecutivo) && !empty($wconsecutivo)){

				$despachoCompleto = true;

				$vecCco = $wcco;

				//Registro de los valores despachados
				$vecArticulos = explode(";",$wdatos);  //Division por articulo

				//Iteracion sobre todos los articulos
				foreach ($vecArticulos as $articulo){
					$vecParametros = explode("|",$articulo);  //Division por datos

					if($vecParametros[0] != ''){

						//Crear el detalle
						$detalle = new DetallePedido();

						$detalle->servicio 		= $vecCco;
						$detalle->articulo 		= $vecParametros[0];
						$detalle->consecutivo 	= $wconsecutivo;
						$detalle->despachado 	= $vecParametros[1];
						$detalle->cantidad	 	= $vecParametros[2];
						$detalle->fuenteUnix 	= $vecParametros[3];
						$detalle->documentoUnix	= $vecParametros[4];

						//Si hay alguna diferencia entre las cantidades pedidas y despachadas el pedido NO SE DESPACHO COMPLETO
						if($detalle->despachado != $detalle->cantidad){
							$despachoCompleto = false;
						}
						actualizarDespachoDetalle($detalle);
					}
				}

				if($despachoCompleto && marcarPedidoAtendido($vecCco,$wconsecutivo,$wobservaciones,$wuser)){
					funcionJavascript("grabarInicioLista('El pedido número $wconsecutivo del servicio $vecCco - $nombreCco ha sido atendido.');");
				} else {
					funcionJavascript("grabarInicioLista('El pedido número $wconsecutivo del servicio $vecCco - $nombreCco ha sido atendido parcialmente.');");
				}
			}
			break;
		case 'h':
			if(isset($wcco) && !empty($wcco)){
				$col = array();

				$vecCco = $wcco;

				//Crear el encabezado
				$encabezado = new EncabezadoPedido();

				echo "<input type=hidden name=wsservicio id=wsservicio value='$vecCco'>";
				//echo "<input type=hidden name=nombreCco id=nombreCco value='$nombreCco'>";


				$encabezado = consultarEncabezadoPedido($vecCco,$wconsecutivo);

				echo "<input type=hidden name=wconsecutivo value='$encabezado->consecutivo'>";

				$col = consultarDetallePedido($encabezado->consecutivo,$encabezado->servicio);

				if(count($col) > 0){
					echo "<table align='center' border=0>";

					echo "<tr>";

					//Consecutivo
					echo "<td class=fila1>";
					echo "N&uacute;mero de pedido";
					echo "</td>";

					echo "<td class=fila2 colspan=3>";
					echo "$encabezado->consecutivo";
					echo "</td>";

					echo "</tr>";
					echo "<tr>";

					//Centro de costos
					echo "<td class=fila1>";
					echo "Centro de costos";
					echo "</td>";

					echo "<td class=fila2 colspan=3>";
					//echo "$encabezado->servicio - $vecCco  mombre";
				    echo "$encabezado->servicio ";
					echo "</td>";

					echo "</tr>";
					echo "<tr>";

					//Estado
					echo "<td class=fila1>";
					echo "Estado";
					echo "</td>";

					echo "<td class=fila2 colspan=3>";
					if($encabezado->esAnulado == 'on'){
						echo "Anulado";
					} else {
						echo "Pendiente";
					}
					echo "</td>";

					echo "</tr>";
					echo "<tr>";

					//Fuente en Unix
					echo "<td class=fila1>";
					echo "Fuente en unix";
					echo "</td>";
					echo "<td class=fila2 colspan=3>";
					echo "<INPUT TYPE='text' id='wtfuente' NAME='wtfuente' value='' SIZE=5 class='textoNormal'>";
					echo "</td>";
					echo "</tr>";

					//Documento en unix para compensar
					echo "<td class=fila1>";
					echo "Documento en unix";
					echo "</td>";

					echo "<td class=fila2 colspan=3>";
					echo "<INPUT TYPE='text' id='wtdocumento' NAME='wtdocumento' value='' SIZE=20 class='textoNormal'>&nbsp;|&nbsp;";
					echo "<input type='button' id='btnGrabar' value='Compensar despacho' onClick='javascript:compensarDespacho();'>";
					echo "</td>";
					echo "</tr>";

					echo "<tr><td class=fila2 colspan=4 align=center>Observaciones</td></tr>";
					echo "<tr>";
					echo "<td align=center colspan=4><textarea id='wtaobservaciones' cols=35 rows=4></textarea></td>";
					echo "</tr>";

					echo "<tr><td align=center colspan=6><br>";

					echo "<input type='button' id='btnGrabar' value='Marcar atendido' onClick='javascript:marcarAtendido();'>&nbsp;|&nbsp;";
					echo "<input type='button' value='Regresar' onClick='javascript:history.back(-1);'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b><br><br></td></tr>";

					echo "<tr class=encabezadoTabla align=center>";
					echo "<td>Articulo</td>";
					echo "<td>Presentación</td>";
					echo "<td>Pedido</td>";
					echo "<td>Despachado</td>";
					echo "<td>Diferencia</td>";
					echo "</tr>";

					$clase="fila2";
					$i = 1;

					//Iteracion sobre todos los articulos
					foreach ($col as $articulo){
						$clase == "fila2";

						echo "<tr class=$clase id='tr$i'>";

						$vecArticulo = explode(" - ",$articulo->articulo);

						echo "<td>$articulo->articulo<INPUT TYPE='hidden' id='whcodigo$i' NAME='whcodigo$i' value='".$vecArticulo[0]."'></td>";
						echo "<td>$articulo->presentacion</td>";
						echo "<td align=right><INPUT TYPE='hidden' id='whpedido$i' NAME='whpedido$i' value='".$articulo->cantidad."'>$articulo->cantidad</td>";
						echo "<td align=center><INPUT TYPE='text' id='wtdespacho$i' NAME='wtdespacho$i' value='' onkeypress='return validarEntradaEntera(event);' onChange='javascript:calcularDiferencia(\"$i\");' SIZE=6 class='textoNormal'></td>";
						echo "<td align=center><INPUT TYPE='text' id='wtdiferencia$i' name='wtdiferencia$i' value='' SIZE=6 class='textoNormal' readonly></td>";
						echo "</tr>";

						$i++;
					}

					echo "<tr><td align=center colspan=6><br>";
					echo "<input type='button' id='btnGrabar' value='Marcar atendido' onClick='javascript:marcarAtendido();'>&nbsp;|&nbsp;";
					echo "<input type='button' value='Regresar' onClick='javascript:history.back(-1);'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b><br><br></td></tr>";

					echo "</table>";
				} else {
					//					funcionJavascript("grabarInicioLista('No hay pedidos pendientes para el servicio $vecCco[0]');");
				}
			}
			break;
		default://Filtro
			echo "<table align='center' border=0>";

			echo '<span class="subtituloPagina2">';
			echo "Par&aacute;metros de consulta";
			echo "</span>";
			echo "<br>";
			echo "<br>";

			//Servicio
			echo"</table>";
			//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		    $cco="Ccostk";
		    $sub="off";
		    $tod="";
		    $ipod="off";
		    //$cco=" ";
		    $centrosCostos = consultaCentrosCostos($cco);

		    echo "<table align='center' border=0 >";
		    $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wsservicio");

		    echo $dib;
		    echo "</table>";

			echo "<table align='center' border=0 width=402>";

			echo "<tr>";

			if(isset($wmensaje) && !empty($wmensaje)){
				echo "<td class=fila2 align=center colspan=2>";
				echo "<font size=4><b>$wmensaje</b></font>";
				echo "</td>";
			} else {
				echo "<td>";
				echo "<br>";
				echo "</td>";
			}
			echo "</tr>";

			//Consecutivo
			echo "<tr>";

			echo "<td class='fila1' >N&uacute;mero de pedido</td>";
			echo "<td class='fila2' align=center width=250>";
			echo "<INPUT TYPE='text' id='wtconsecutivo' NAME='wconsecutivo' value='' SIZE=10 onKeyPress='return teclaEnterEntero(event,"."\"consultarConsecutivo();\");' class='textoNormal'>";
			echo "</td>";

			echo"</tr>";

			echo "<tr><td align=center colspan=2><br><input type='button' value='Realizar pedido' onClick='javascript:realizarPedido();'>&nbsp;|&nbsp;<input type='button' value='Consultar pedido' onClick='javascript:consultarPedido();'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></b></td></tr></center>";

			echo "</table>";
			break;
	}
	echo "</form>";
		
	odbc_close($conexunix);
	odbc_close_all();

}
?>