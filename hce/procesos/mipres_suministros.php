<?php
include_once("conex.php");

//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:	Este script permite reportar o informar todos los suministros o tecnologias realizados a los pacientes
// de Prescripciones de Tecnologías en Salud No Financiadas con Recursos de la UPC o Servicios Complementarios
// MIPRES No PBSUPC .
// Manuales originales :
// https://www.minsalud.gov.co/sites/rid/Lists/BibliotecaDigital/RIDE/DE/OT/documentacion-web-services-suministro-v1.0.pdf
// https://www.minsalud.gov.co/sites/rid/Lists/BibliotecaDigital/RIDE/DE/OT/entrega-datos-proveedores-v1.5.pdf
// https://www.minsalud.gov.co/sites/rid/Lists/BibliotecaDigital/RIDE/DE/OT/modulo-dispensador-proveedor-v11.pdf
// https://www.minsalud.gov.co/sites/rid/Lists/BibliotecaDigital/RIDE/DE/OT/causas-no-entrega-dispensacion.pdf
// https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/Help
// https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/Swagger/ui/index  Uso web services
//   FACTURACION
// https://www.minsalud.gov.co/sites/rid/Lists/BibliotecaDigital/RIDE/DE/OT/anexo-tecnico-reporte-facturacion.pdf
// https://wsmipres.sispro.gov.co/WSFACMIPRESNOPBS/Swagger/ui/index Uso web services
// ------------------------------------------------------------
// 							MOVIMIENTOS
// ------------------------------------------------------------
// mipres_000037 - Tabla de estado del mipres de suministros (Direccionamiento , Programacion , Entrega y Reporte de Entrega)
// ------------------------------------------------------------
// mipres_000036 - 1. Tipos de Tecnologias Disponibles ( Medicamentos, Procedimientos,Dispositivos , Nutruciones y Servicios)
// mipres_000038 - 2. Maestro con las Causas de NO Entrega
// mipres_000039 - 3. Maestro que asocia las tencologias con los articulos internos .

// Registros necesarios :
// En root_000051
// 'urlWebserviceSuministroMipres' , direccion de acceso a los web services https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api
// 'fechatokenWebserviceSuministroMipres' fecha y hora en que se genero el token diario (mas 12 horas) ,
//                porque la validez del token es de 12 horas .
// 'tokenDiarioWebserviceSuministroMipres' token diario que se usa en el llamado de todos los webservices
// 'codigoSedeMipres' , codigo de la sede que se usara en la Programacion de los Mipres de Suministros.
// 'ingresomanualMipres' , indica si se van a permitir ingresos manuales , es decir el consumo no aparece en la base de datos
// y el usuario completa esta informacion manualmente ( Cantidad, Tecnologia , Precio o Tarifa y la fecha de la aplicacion o consumo )
// 'modomipresambulatorioxbuscaringresospacientes'  , se creo para que los ingresos de los pacientes se busquen desde la fecha de la
// prescripcion hacia fechas posteriores los ingresos de los pacientes (modo ambulatorio , como funciona el instituto de cancerologia)
// , a diferencia de la configuracion normal que se tenia antes
// que era buscar desde la fecha de la prescripcion hacia atras los ingresos de los pacientes ..

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES 
//-------------------------------------------------------------------------------------------------------------------------------------------- 
			$wactualiz='2020-06-18';// para pasar a produccion la variable $esproduccion debe estar en true y de manera visual se quitan los puntos a $wactualiz
//--------------------------------------------------------------------------------------------------------------------------------------------  

//--------------------------------------------------------------------------------------------------------------------------------------------   
// 2020-04-15 Actualizacion del reporte de impresion de facturacion , tambien se le adicionan todos los IDs.
// 2020-01-21 Se corrige el llamado  del reporte de facturacion para que se demore menos , evitando la actualizacion innecesaria de IDS

// 2019-12-18 El instituto de cancerologia ( IDC ) ,solicita que las prescripciones realizadas por los medicos en sus consultorios y que sean
// direccionados al IDC sea posible hacer la entrega , el reporte de entrega y la facturacion de estas prescripciones.
// Para realizar esto es necesario crear los registros necesarios en las tablas de mipres_1 y mipres_2 (medicamentos) o en la tabla que corresponda
//  por ahora solo se desarrollo la parte de medicamentos que es la que utiliza el IDC .
// Se creo el metodo crearRegistrosMp1XJson (para crear los registros en las tablas mipres_1 y mipres_2)
//  y se modifico el metodo generarCmdsSqlXInsertUpdateXJson ( para generar los comandos sql para la tabla mipres_1)
// El campo de seguridad en estas tablas termina con el sufijo = xDir , para indicar que estos datos fueron ingresados usando la informacion
//  del  Json de Direccionamiento			
// 2019-08-05 Minsalud crea un nuevo mipres , el mipres de Facturacion 
// donde se debe consultar el numero de factura , el valor del coopago , la cuota moderadora , con la cual fue facturado el mipres.
//                                                                                                                    \\
//  2019-07-29 Por solcitudd del ministerio de salud es necesario agregar dos nuevos campos cuando se hace la Entrega de las 
// tecnologias , los campos son Tipo de Identificacion del paciente y numero de identificacion del paciente .
// https://www.minsalud.gov.co/sites/rid/Lists/BibliotecaDigital/RIDE/DE/OT/anexo-tecnico-mod-dispensador-proveedores-v1.8.pdf
// 	2019-07-11	- Freddy Saenz  Se modifica la aplicacion para que el  orden de los ingresos para el IDC (modo ambulatorio) se ordenen
//      ascendentemente y no descendentemente como ocurre en en la clinica ( hospitalizado ) .
//    Se cambia el envio en lote para que use los Json que estan en cada fila de la tabla de prescripciones , pero ahora se enviaran
//    todas las entregas primero y despues todos los reportes de entrega .
//    para los que se envian via ambito de entrega ( no tienen ID principal ) se deja como funcionaba antes.
// 	2019-07-05	- Freddy Saenz  Se configura la aplicacion para que se comporte segun requerimientos de ingreso del IDC , es
//      decir los ingresos de los pacientes se realizan despues de la fecha de la prescripcion y no antes como ocurre con los pacientes
//      de la clinica.


//----------------------------------------------------------------------------
// Informacion basica de la aplicacion
// Consiste en :
//
//  VENTANA PRINCIPAL  
//----------------------------------------------------------------------------
//  Opciones de Busqueda
// (pintarFiltros)  ==> buscarPrescripcionesSuministros
//----------------------------------------------------------------------------
//  Tabla Html con los resultados de busqueda
//  ( pintarPrescripcionesEnTabla ) , despliega la informacion necesaria del mipres
// 
//----------------------------------------------------------------------------
//
//
//  Botones o acciones de javascript , hacen llamados ajax , con la variable accion a
//  $.post("mipres_suministros.php"
//
// Crear Jsons :
// creararrxenvio , para Ambito de Entrega
// creaarrxenvioconID , para Entrega
// creararrxreporte. , para Reporte de Entrega
// creararrxprogramacion , para Programación
// creararrfacturacion , para Facturacion
//
// La información de los json se guarda en campos invisibles en la tabla de la información de los mipres en la fila correspondiente
// infoenviomipres_0 .. n , guarda el json creado con creararrxenvio
// infoenvioIDmipres_0..n , guarda el json creado con creaarrxenvioconID
// infoenvioreporte_0..n , guarda el json creado con creararrxreporte
// Para la programación , el son se genera en el momento del envio .
// 
// Usar web services :
// hacer_entrega
// hacer_reporte_entrega
// hacer_programacion
// hacer_facturacion
// Generar nuevo token
// get_generar_token
// get_generar_token_facturacion
// Actualizar IDs
// actualizarIDsPrescripcionesPeriodo
// 
// 
// Hacer anulaciones:
// anular_entrega
// anular_reporte_entrega
// anular_programacion
// anular_facturacion
// Mostrar información en celda de entrega
// datacellenvio
// 
// Mostrar información en celda de reporte de entrega
// datacellreporte
// 
// Buscar historias e ingresos 
// consultarHistoriasXCedula
// 
// infocantidadvlrycodigomipres , en este metodo se consulta la cantidad , el valor unitario de la tecnología solicitada , incluye buscar la tecnología aplicada , su cantidad y valor unitario .
// Basada en la tecnologia del mipres se busca la tecnología aplicada  usando:
// codigosxprincipioactivo , principio activo , tabla mipres_000039
// codigosmismocum , el cum 
// palabraclavenombre, o por el nombre genérico en el maestro de los artículos usando como patron de búsqueda el principio activo
// 
// sumarMedicamentosMipres , realiza las suma de cantidades aplicadas
// sumarMedicamentosxcentralmezclas , igual que el anterior pero lo realiza sobre las preparaciones de la central de mezclas
// valorcobradoMedicamento , busca el valor unitario de la tecnología aplicada
// 
// generarCmdsSqlXInsertUpdateXJson , basado en un Json crea los comandos SQL de
// Insert , update con clave principal , para ser usados en el método insertupdatemipressuministro
// que actualiza la tabla mipres_000037
// 
// 
// En javascript 
// enviarSeleccion , funcion para enviar ( Entrega y Reporte de Entrega) en bloque una seleccion de mipres
// usarSeleccion , como mipres activos deja los mipres seleccionados
// 
// Actualizar o modificar 
// "actualizarFechaAplicacion" , modifica fecha de aplicacion
// "actualizarValorUnitario" , modifica el valor unitario
// "actualizarCausanoentrega", modifica la causa de no entrega
// "actualizarCodigo", modifica el codigo de la tecnologia entregada
// "actualizarCantidad", modifica la cantidad entregada
// 
//----------------------------------------------------------------------------



//Mipres de Facturacion 5 de Agosto 2019 Freddy Saenz
// Informacion necesari para realizar este mipres 
// Nit de la Eps
// Numero de la factura
// Cuota Moderadora
// Coopago
// Preguntas : 1. Quien realizara este mipres (facturacion?)
// 2. Si el mipres tiene varias entregas , que factura se genera 
// segun el diseño de campos la facturacion se hace por mipres total y no por entrega , porque el reporte de facturacion
// no tiene el numero de entrega
// 3. Si no se hizo direccionamiento , pero si se hace entrega como se reporta esta facturacion.
//
// Es necesario modificar la estructura de la tabal mipres_000037  , para agregar el ID de facturacion ,el estado de facturacion y el Json correspondiente



if(!isset($_SESSION['user']) && $proceso!="actualizar")
{
		echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
								[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
						</div>';
		return;
}
else
{
	include_once("root/comun.php");

	if(!isset($wemp_pmla))
	{
		$wemp_pmla = "01";
	}
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];


	mysql_select_db("matrix");
	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex,$wemp_pmla , 'movhos');//'movhos';//
	//root_000051 SELECT Detval FROM root_000051 WHERE Detemp = '01' AND Detapl = 'mipres';
	$wbasedatomipres = consultarAliasPorAplicacion($conex,$wemp_pmla , 'mipres');

	//3 de abril 2019 , Freddy Saenz
	$wbasedatoroot = 'root';//no se crea un consultarAliasPorAplicacion porque es el kernel
	//siempre sera root y por eso no se crea en root_000051 ($conex,$wemp_pmla , 'root');
	$wbasedatocliame = consultarAliasPorAplicacion($conex,$wemp_pmla , 'cliame');

	$wsedemipres = consultarAliasPorAplicacion($conex,$wemp_pmla , 'codigoSedeMipres');

	$ingresoManualMipres = consultarAliasPorAplicacion($conex,$wemp_pmla , 'ingresomanualMipres');

	//$ingresoManualMipres = 1;//permitir ingresar el mipres de consumo manualmente.
	$wbasedatocenpro = 'cenpro';//no aparece multiempresa , si se necesita multiempresa central de mezclas hay que crear una preferencia en root51.

	$wfecha=date("Y-m-d");
	$whora = date("H:i:s");

	$wmodoambulatorio = consultarAliasPorAplicacion($conex,$wemp_pmla , 'modomipresambulatorioxbuscaringresospacientes');;//= 1busca los ingresos de los pacientes con fecha posterior a la fecha del mipres
	//para los hospitalizados = 0, busca solo las fechas anteriores a la fecha del mipres

	//$esproduccion = false;//true; //  OJO , debe estar en true si la aplicacion ya pasa a produccion
	//para que no genere tokens nuevos en desarrollo es que se deja en falso . Si se genera un token
	// nuevo en desarrollo este token inhabilita el token que esta en produccion . Esta es la razon por la cual se deja en falso en desarrollo.
	// Lo que se hace en desarrollo es copiar el token de produccion en la tabla root_000051 y se actualiza la fecha y hora de validez
	// de un token. 
// -----------------

	//11 mayo 2020 crear un registro que indique si se esta trabajando en modo desarrollo o produccion
	// esto debido a que en desarrollo no se pueden genarar tokens nuevos porque la aplicacion ya esta en uso y puede inhabilitar los tokens que ya
	// se han generado durante el dia . La idea para desarrollo es copiar los tokens de produccion a la base de datos de desarrollo (o test si se necesita)



	$wmipresenproduccion = consultarAliasPorAplicacion( $conex , $wemp_pmla , 'mipresenproduccion' );

	if ( $wmipresenproduccion == "0" ){
		$esproduccion = false;
	}else{
		
		$esproduccion = true;
	}





// -------------------------


	$errorenvioseleccion = "";//para mostrar una alerta cuando se produzcan errores al enviar por seleccion .






//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================



	function filtrosXMipres (){
		$arropciones = array(
			'1' => 'No enviadas'
			,'2' => 'Altas en la Fecha...'//hoy
			,'3' => 'Direccionados para la Fecha...'//hoy
			,'4' => 'Programados en la Fecha...'//hoy

			,'5' => 'Con Entrega en la fecha ...'
			,'6' => 'Con Reporte de Entrega en la fecha ...'

			,'7' => 'Enviadas >= 1 semana '
			,'8' => 'Prescripciones >= 1 semana'
			,'9' => 'Facturacion enviada en la fecha ...'

			);
		return $arropciones;
	}



	function causasnoentrega (){



		global $conex;
		global $wbasedatomipres;

		$query = " SELECT Noecod,Noenom
									FROM ".$wbasedatomipres."_000038
									WHERE Noeest = 'on'
									ORDER BY Noeord ";

		$resQuery = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$numQuery = mysql_num_rows($resQuery);

		$arregloQuery =  array();
		if($numQuery > 0)
		{
			//mysql_fetch_arrayli
			while($rowsQuery = mysql_fetch_array($resQuery))
			{
				$arregloQuery[$rowsQuery['Noecod']] = $rowsQuery['Noenom'];
			}

		}

		return $arregloQuery;

	}//function causasnoentrega ()

	function selectcausanoentrega ( $cta , $valorsel , $parametrosfuncionenvio ){



		$arrcausas = causasnoentrega();
		$parametros = $parametrosfuncionenvio;//" this, \"$cta\" " ;
		$html ="	<select name='actualizarcausanoentrega".$cta."' id='actualizarcausanoentrega".$cta."' style='width:90%;display:inline;' onchange='actualizarcausanoentrega(".$parametros.");'>";
		$html .= "<option value='0'>Causa No Entrega:</option>";
		//Modificacion 21 de Marzo ,Freddy Saenz , Mipres para proveedores
		foreach($arrcausas as $codcausa => $valcausa)
		  {
			$seleccionado = "";
			if ( intval($codcausa) == $valorsel ){
				$seleccionado = "selected";
			}
			$html .= "	<option value='".$codcausa."' ".$seleccionado." >".$valcausa."</option>";
		  }

		$html .= "	</select>";
		return $html ;
	}	//function selectcausanoentrega ( $cta , $valorsel )


//Se crea una nueva tabla para las tecnoligias desponibles , se genera entonces la consulta correspondiente.
	function tiposDeTecnologias (){


		global $conex;
		global $wbasedatomipres;

		$query = " SELECT Teccod,Tecnom
									FROM ".$wbasedatomipres."_000036
									WHERE Tecest = 'on'
									ORDER BY Tecord ";

		$resQuery = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$numQuery = mysql_num_rows($resQuery);

		$arregloQuery =  array();
		if($numQuery > 0)
		{
			//mysql_fetch_arrayli
			while($rowsQuery = mysql_fetch_array($resQuery))
			{
				$arregloQuery[$rowsQuery['Teccod']] = $rowsQuery['Tecnom'];
			}

		}

		return $arregloQuery;

	}//function tiposDeTecnologias ()
	
	
	


	function consultarAmbitoAtencion($codAmbitoAtencion)
	{
		global $conex;
		global $wbasedatomipres;

		if($codAmbitoAtencion=="")
		{
			$queryAmbitoAtencion = "  SELECT Amacod,Amades
										FROM mipres_000014;";//27 junio 2019 ".$wbasedatomipres."


			$resAmbitoAtencion = mysql_query($queryAmbitoAtencion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryAmbitoAtencion . " - " . mysql_error());
			$numAmbitoAtencion = mysql_num_rows($resAmbitoAtencion);

			$arrayAmbitosAtencion = array();
			if($numAmbitoAtencion>0)
			{
				while($rowsAmbitoAtencion = mysql_fetch_array($resAmbitoAtencion))
				{
					$arrayAmbitosAtencion[$rowsAmbitoAtencion['Amacod']] = $rowsAmbitoAtencion['Amades'];
				}

			}

			return $arrayAmbitosAtencion;
		}
		else
		{
			$queryAmbitoAtencion = "  SELECT Amades
										FROM mipres_000014
									   WHERE Amacod='".$codAmbitoAtencion."';";


			$resAmbitoAtencion = mysql_query($queryAmbitoAtencion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryAmbitoAtencion . " - " . mysql_error());
			$numAmbitoAtencion = mysql_num_rows($resAmbitoAtencion);

			$ambitoAtencion = $codAmbitoAtencion;
			if($numAmbitoAtencion>0)
			{
				$rowsAmbitoAtencion = mysql_fetch_array($resAmbitoAtencion);

				$ambitoAtencion = utf8_encode($rowsAmbitoAtencion['Amades']);
			}

			return $ambitoAtencion;
		}


	}

	function consultarTiposDocumentoMipres()
	{
		global $conex;
		global $wbasedatomipres;

		$queryTiposDocumentos = " SELECT Tdicod,Tdides
									FROM mipres_000011;";//27 junio 2019  ".$wbasedatomipres."

		$resTiposDocumentos = mysql_query($queryTiposDocumentos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTiposDocumentos . " - " . mysql_error());
		$numTiposDocumentos = mysql_num_rows($resTiposDocumentos);

		$detalleTiposDocumentos = array();
		if($numTiposDocumentos>0)
		{
			while($rowsTiposDocumentos = mysql_fetch_array($resTiposDocumentos))
			{
				$detalleTiposDocumentos[$rowsTiposDocumentos['Tdicod']] = $rowsTiposDocumentos['Tdides'];
			}

		}

		return $detalleTiposDocumentos;
	}

	
	
	function pintarFiltros()
	//muestra las diferentes opciones de busqueda.
	{
		global $wfecha;
		global $wemp_pmla;
		global $wfechaactualizacionIDs;
		global $conex;

		$tokendia = tokenwebservice($wemp_pmla);
		$tokendiafacturacion = tokenfacturacionwebservice($wemp_pmla);
		
		$arrayTipDoc = consultarTiposDocumentoMipres();
		$arrayAmbitosAtencion = consultarAmbitoAtencion("");
//
		$fechavalideztoken = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'fechatokenWebserviceSuministroMipres' );
		
		$fechavalideztokenfacturacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'fechatokenWebserviceFacturacionMipres' );


		$urlfacturacion = urlwebservicefacturacion($wemp_pmla);
		$urlfacturacion = str_replace("/api", "/Swagger/ui/index" , $urlfacturacion);
		$urlsuministros = urlwebservice($wemp_pmla);
		$urlsuministros = str_replace("/api", "/Swagger/ui/index" , $urlsuministros);
		
				//Modificacion 21 de Marzo 2019 ,Freddy Saenz , se agregan filtros adicionales para el mipres por proveedor,
		$arrayFiltrosMipresProveedor = filtrosXMipres ();
		$arrtiposdetecnologias = tiposDeTecnologias();
		$nit = nitwebservice($wemp_pmla);
		
		$html = "";

		$encabezado1 = "<table align='center' width='100%'>";
		//
		$encabezado1 .= "<tr>";
		$encabezado1 .= "<td>&nbsp;&nbsp;&nbsp;&nbsp;Busqueda General</td>";
		//class='encabezadotabla'
		$encabezado1 .= "<td  class='encabezadotabla'  align='center'>Mipres</td>";
		$encabezado1 .= "<td  class='encabezadotabla'  align='center' >Token</td>";
		$encabezado1 .= "<td  class='encabezadotabla'  align='center' >Valido Hasta</td>";
		$encabezado1 .= "<td  class='encabezadotabla'  align='center' >NIT/Ultima Actualizacion de IDs</td>";					
		$encabezado1 .= "</tr>";
		
		$encabezado1 .= "<tr>";
		$encabezado1 .= "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		//class='encabezadotabla'
		$encabezado1 .= "<td  class='encabezadotabla'><a href='".$urlsuministros."' target='_blank' style='color:white'>Suministros</a></td>";//
		$encabezado1 .= "<td  class='encabezadotabla'  align='center' ><div id='idtokendia'>".$tokendia."</div> </td>";
		$encabezado1 .= "<td  class='encabezadotabla'  align='center' ><div id='idvalideztoken'>".$fechavalideztoken."</div> </td>";
		$encabezado1 .= "<td  class='encabezadotabla' align='center' ><div id='idnit'>".$nit."</div></td>";					
		$encabezado1 .= "</tr>";

//4 de Octubre 2019, Freddy Saenz , se agrega la informacion del token de que se usa en los web services del mipres de facturacion

		$encabezado1 .= "<tr>";
		$encabezado1 .= "<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		$encabezado1 .= "<td  class='encabezadotabla'><a href='".$urlfacturacion."' target='_blank' style='color:white' >Facturacion</a></td>";
		$encabezado1 .= "<td  class='encabezadotabla'  align='center' ><div id='idtokendiafacturacion'>".$tokendiafacturacion."</div> </td>";
		$encabezado1 .= "<td  class='encabezadotabla'  align='center' ><div id='idvalideztokenfacturacion'>".$fechavalideztokenfacturacion."</div> </td>";
		
		$encabezado1 .= "<td class='encabezadotabla' align='center'  ><div id='fechaultimaactualizacionIDs' >". $wfechaactualizacionIDs."</div> </td>";
		$encabezado1 .= "</tr>";	
		
		$encabezado1 .= "</table>";

		$html .= "	<div id='tabPrescripciones'> ";

//$html .= " <h3>Busqueda General</h3>
$html .= " <h3>$encabezado1</h3>

<div>

	<table align='center' width='100%'>

	  <tr class='encabezadoTabla'>
		<td colspan='6' align='center'>Par&aacute;metros de b&uacute;squeda</td>
	  </tr>


	  <tr>
		<td class='fila1' width='10%'>
		  <b>Fecha inicial :</b>
		</td>
		<td class='fila2' width='20%'>
		  <input type='text' id='fechaInicial' name='fechaInicial' readOnly='readOnly' value='".date("Y-m-d")."'>
		</td>
		<td class='fila1'  width='10%'>
		  <b>Fecha Final:</b>
		</td>
		<td class='fila2'  width='20%'>
		  <input type='text' id='fechaFinal' name='fechaFinal' readOnly='readOnly' value='".date("Y-m-d")."'>
		</td>

		<td class='fila1' width='10%'>
	  <b style='display:inline;'>Estado del Mipres</b>
		</td>

			<td class='fila2'>

		  <select name='filtroestadomipres' id='filtroestadomipres'  style='width:90%;display:inline;' >
				<option value='VER-TODOS'>VER TODOS</option>";
	$html .= "  <option value='SinDireccionamiento'>Sin Direccionamiento</option>
				<option value='Direccionamiento'>En Direccionamiento</option>
				<option value='Programacion'>En Programacion</option>
				<option value='Envio'>En Entrega</option>
				
				<option value='Completas'>Completas</option>
				<option value='Anuladas'>Anuladas</option>
				<option value='Completa-SinEntrega'>Aplicacion Total sin Entrega</option>

			</select>

			</td>

	  </tr>


	  <tr>
		<td class='fila1' width='80px'>
		  <b>Paciente:</b>
		</td>
		<td class='fila2'>
		  <select id='tipDocPac' name='tipDocPac' style='width:50%' onchange='habilitarDocumento(this);' habilitarCampo='docPac'>
			<option value=''>Tipo documento</option>";
			foreach($arrayTipDoc as $codTipDoc => $valueTipDoc)
			{
	$html .= "								<option value='".$codTipDoc."'>".$codTipDoc."-".$valueTipDoc."</option>";
			}
	$html .= "						</select>
			<input type='text' id='docPac' name='docPac' style='display:none;'>
		  </td>


		<td class='fila1' width='10%'>
			<b>Profesional de la salud:</b>
		</td>

		<td class='fila2' width='30%'>
			<select id='tipDocMed' name='tipDocMed' style='width:50%' onchange='habilitarDocumento(this);' habilitarCampo='docMed'>
			<option value=''>Tipo documento</option>";

				foreach($arrayTipDoc as $codTipDoc => $valueTipDoc)
				{
					$html .= "	<option value='".$codTipDoc."'>".$codTipDoc."-".$valueTipDoc."</option>";
				}
				$html .= "	</select>

			<input type='text' id='docMed' name='docMed' style='display:none;'>
		</td>





		  <td class='fila1' width='80px'>
			<b>Tipo de prescripci&oacute;n:</b>
		  </td>
		  <td class='fila2'>
			<select id='tipoPrescripcion' name='tipoPrescripcion'  style='width:90%'>
			  <option value=''>VER TODOS</option>";

			  foreach($arrtiposdetecnologias as $codtipoprescripcion => $valuetipoprescripcion)
			  // <input type='text' id='nroPrescripcion' name='nroPrescripcion' style='width:90%' onkeypress='return soloNumerosymas(event);'>
			  {
	  $html .= "									<option value='".$codtipoprescripcion."'>".$valuetipoprescripcion."</option>";
			  }
	  $html .= "						</select>


		  </td>

	  </tr>




	  <tr>

	<td class='fila1' width='80px'>
        <b>EPS:</b>
     </td>
    <td class='fila2'>
        <input type='text' id='txtResponsable' name='txtResponsable'>
        <input type='hidden' id='txtCodResponsable' name='txtCodResponsable'>
    </td>

	
		<td class='fila1' width='80px'>
		  <b style='display:inline;'>Filtro Facturacion:</b>
		</td>
		<td class='fila2'>
		  <select name='filtroFacturacion' id='filtroFacturacion'  style='width:90%;display:inline;'>
			<option value='' selected>VER TODOS</option>
			<option value='Facturadas' >Facturadas Con Envio</option>
			<option value='NO-Facturadas' >Facturadas Sin Envio</option>
			<option value='SinFactura' >Sin Factura</option>
			<option value='FacturaIgualAEntregado' >Vlr. Fact=Vlr.Entregado</option>
			<option value='FacturaNoIgualAEntregado' >Vlr. Fact Diferente al Vlr.Entregado</option>
			
			

		  </select>
		  
		</td>



		
		

		<td class='fila1' width='80px'>
		  <b>&Aacute;mbito de atenci&oacute;n:</b>
		</td>
		<td class='fila2'>
		  <select name='filtroAmbitoAtencion' id='filtroAmbitoAtencion'  style='width:90%;display:inline;' >
			<option value=''>VER TODOS</option>";
			  foreach($arrayAmbitosAtencion as $codAmbAte => $valueAmbAte)//  <td class='fila2' style='width:90%;display:inline;'>
			  {
	$html .= "									<option value='".$codAmbAte."'>".$valueAmbAte."</option>";
			  }
	$html .= "						</select>
		</td>


	  </tr>


<tr>
<td colspan='6'> <input type='button' id='btnBuscar' value='Buscar' onclick='pintarPrescripciones(1);'></td>
</tr>


	</table>

</div>

<h3>&nbsp;&nbsp;&nbsp;Busqueda por Prescripcion o IDs</h3>
<div>

 <table align='center' width='100%'>

 <tr>
 		<td  class='fila1' width='80px'>
		  <b>* N&uacute;mero de prescripci&oacute;n (+):</b>
		</td>
		<td colspan='5' class='fila2' width='120px' >

		  <textarea   id='nroPrescripcion' name='nroPrescripcion' rows='2' style='width:90%' onkeypress='return soloNumerosymas(event);'></textarea>

		</td>
	</tr>

 	  <tr>

		<td class='fila1' width='15%'>
		  <b>ID Prescripcion:</b>
		</td>
		<td class='fila2' width='20%'>

		  <input type='text' id='vIDbuscar' name='vIDbuscar' onkeypress='return soloNumeros(event);'>
		</td>
		<td class='fila1'  width='10%'>
		  <b>ID de Entrega:</b>
		</td>

		<td class='fila2'  width='20%'>
		<input type='text' id='vIDEntregabuscar' name='vIDEntregabuscar' onkeypress='return soloNumeros(event);'>
		</td>

		<td class='fila1'  width='15%'>
		<b>ID de Reporte:</b>
		</td>

		<td class='fila2'  width='20%'>
		<input type='text' id='vIDReportebuscar' name='vIDReportebuscar' onkeypress='return soloNumeros(event);'>
		</td>
		
	  </tr>

 	  <tr>

		<td class='fila1' width='15%'>
		  <b>ID Facturacion:</b>
		</td>
		<td class='fila2' width='20%'>

		  <input type='text' id='vIDFacturacionbuscar' name='vIDFacturacionbuscar' onkeypress='return soloNumeros(event);'>
		</td>
		<td class='fila1'  width='10%'>
		  <b>ID de Programacion:</b>
		</td>

		<td class='fila2'  width='20%'>
		<input type='text' id='vIDProgramacionbuscar' name='vIDProgramacionbuscar' onkeypress='return soloNumeros(event);'>
		</td>

		<td class='fila1'  width='15%'>
		<b>Numero de Factura:</b>
		</td>

		<td class='fila2'  width='20%'>
		<input type='text' id='vNumeroFacturabuscar' name='vNumeroFacturabuscar' onkeypress='return soloNumeros(event);'>
		</td>
		
	  </tr>	  

<tr>
<td colspan='5'> <input type='button' id='btnBuscar2' value='Buscar' onclick='pintarPrescripciones(2);'></td>";

$html .= "<td colspan='1'> <input type='button' id='btnActualizarIDs' value='Actualizar IDs Prescripcion' onclick='actualizarIDs();'></td>";
//$html .= "<td colspan='1'> <input type='button' id='btnTestJson' value='Test Json' onclick='generarJson();'></td>";//para borrar 9 agosto 2019
// reporte diario de prescripciones
$html .= "
</tr>


 </table>
 </div>



<h3>&nbsp;&nbsp;&nbsp;Busqueda por Web Services</h3>
<div>

  <table align='center' width='100%'>

 <tr>

 <td colspan='6'>
 <hr style='color: #0056b2;' />
 </td>
  </tr>


   <tr>

    <td  class='fila2' width='80px'>
      <b style='display:inline;'>Web Services Mipres Suministros</b>
    </td>
    <td colspan='3' class='fila2'>

		<select name='filtroMipresProveedor' id='filtroMipresProveedor'  style='width:90%;display:inline;' onchange='habilitarFechaMipres(this);'>
		<option value='0'>VER TODOS</option>";
		//Modificacion 21 de Marzo ,Freddy Saenz , Mipres para proveedores
		foreach($arrayFiltrosMipresProveedor as $codBusqMp => $valBusqMp)
		  {
			$html .= "	<option value='".$codBusqMp."'>".$valBusqMp."</option>";
		  }

		$html .= "	</select>

		<br>
		<b style='display:inline;'>Fecha de la busqueda:</b>

		<input type='text' id='fechamipresprovedores' name='fechamipresprovedores'   readOnly='readOnly' value='".date("Y-m-d")."'>

   </td>

  </tr>

  <tr>
<td colspan='2'> <input type='button' id='btnBuscar3' value='Buscar' onclick='pintarPrescripciones(3);'></td>
</tr>



</table>

</div>

</div>";//div principal del acordion




$html .= "

<div id='idbotonreporteprescripciones'>
<input type='button' id='btnReporteDiarioPrescripciones' value='Reporte Diario de Prescripciones' style='width:100%; color:black; background-color:#62bbe8;  height:30px; font-weight: 500; font-size: medium; border-radius: 12px;' onclick='freportediariomipres();'> 
</div>

<div id='listaPrescripciones'>
</div>


<div id='infoenvio'>
</div>


";
//$html .= "<footer><input type='button' id='btnOcultar' value='Ocultar' onclick='ocultarBusquedas();'></footer>";

/*
		<td class='fila2' width='80px'>
		  <b style='display:inline;'>Filtro MIPRES:</b>

		  <select name='filtroMipres' id='filtroMipres'  style='width:90%;display:inline;'>
			<option value='' selected>VER TODOS</option>
			<option value='SinOrdenes' >Sin &oacute;rdenes</option>
		  </select>
		</td>*/
		
		return $html;
	}//function pintarFiltros()



	function actualizarIDsPrescripcionesPeriodo($wemp_pmla,$fechaInicial,$fechaFinal , $nroPrescripcionlista )

	//Actualiza en la base de datos los IDs  de las prescripciones , numeros generados por la aplicacion de mipres de suminstros
	//del ministerio de salud . Los tres ID son : ID de la prescripcion , ID de la entrega e ID del reporte de entrega.
	{
		// , $noprescripcion
		global $wbasedatoroot;
		global $wmodoambulatorio;

		$arrresult = array();
		$arrvacio = array ();

		$fecha1 = $fechaInicial;

		$arrIDsunicos = array();
		$ctaids = 0;

		if ($nroPrescripcionlista != ""){
			//Actualizar por numero de prescripcion .
			$nroPrescripcion2 = str_replace(" " , "" , $nroPrescripcionlista);//quitar los espacios
			$nroPrescripcion2 = str_replace("\r" , "+" , $nroPrescripcion2);//quitar los returns
			$nroPrescripcion2 = str_replace("\n" , "+" , $nroPrescripcion2);//quitar los saltos de linea
			$nroPrescripcion2 = str_replace("++" , "+" , $nroPrescripcion2);//quitar algunos blancos

			//$arrprescripciones  = explode ( "+" , $nroPrescripcionlista );
			$arrprescripciones  = explode ( "+" , $nroPrescripcion2 );

			for ($i=0;$i<count($arrprescripciones);$i++){
				$nroPrescripcion = $arrprescripciones[$i];//ir recorriendo el listado de prescripciones
				if ($nroPrescripcion == ""){//si esta en blanco continuar con la siguiente prescripcion.
					continue;
				}
				


// ID de facturacion de una prescripcion
/* Comentado hasta que se active el NIT de la clinica para utilizar los web services del ministerio. */
				$xxxservicio = "FacturacionXPrescripcion";
				$resultreporte = get_wsxxx_x_prescripcionfacturacion($xxxservicio , $nroPrescripcion , $wemp_pmla);
				
				
				if(!is_string($resultreporte) ){

					$jsonMipres = $resultreporte;//json_decode($resultreporte);

					if(count($jsonMipres)>0)
					{
						$arrvaluetmp = array();
						foreach($jsonMipres as $key => $value)
						{
							//$vclaveidunica  = $value["Id"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							foreach($value as $key2 => $value2){
								if ($key2 == "ID" ){
									$arrvaluetmp["ID2"] = $value2;//el ID de facturacion es diferente del ID de Suministros
								}else{
									$arrvaluetmp[$key2] = $value2;
								}
								
							}
							$value["ID2"] = $value["ID"];
							$value["ID"] = "";//el ID de facturacion es diferente del ID de Suministros

							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo


							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $arrvaluetmp );//$value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;



						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)

				}//if(!is_string($resultreporte) )
				else{//no se pudo actualizar
					//return $arrvacio ;
					//pero interntar con las otras actualizaciones
				}				
				//12 JUNIO 2020 , se cambia para que primero se haga la programacion y despues el direccionamiento

				//REALIZADO EL CAMBIO DE ORDEN. 1. DIRECCIONAMIENTO 2.PROGRAMACION.
				
				// EMPIEZA LA PROGRAMACION
				//CORRECCION CASO prescripción # 20200403229001055205 con fecha maxima de entrega = 2020-05-06 (direccionamiento ) cuando deberia ser 2020-06-11(programacion)
				$xxxservicio = "ProgramacionXPrescripcion";
				$resultpro = get_wsxxx_x_prescripcion($xxxservicio , $nroPrescripcion , $wemp_pmla);
				if(!is_string($resultpro) ){
					$jsonMipres = $resultpro ;//json_decode($resultpro);//de texto a json

					if(count($jsonMipres)>0)
					{
						foreach($jsonMipres as $key => $value)
						{
							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo


							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;



						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)

				}//if(!is_string($resultpro) )
				else{//no se pudo actualizar
					return $arrvacio ;
				}
				// TERMINA LA PROGRAMACION

				// EMPIEZA EL DIRECCIONAMIENTO
				$xxxservicio = "DireccionamientoXPrescripcion";
				$resultdir = get_wsxxx_x_prescripcion($xxxservicio , $nroPrescripcion , $wemp_pmla);
				if(!is_string($resultdir) ) {

					$jsonMipres = $resultdir;//json_decode($resultdir);

					if(count($jsonMipres)>0)


					{


						foreach($jsonMipres as $key => $value)
						{
							//17 de Diciembre 2019 Freddy Saenz , crear registros en las tablas mipres_1 y mipres_2 , si el direccionamiento
							//no tiene registros en estas tablas.
							if ( $wmodoambulatorio != 0 ){//aplica para el idc
								$vnumregmp1 = crearRegistrosMp1XJson( $value );//crear los registros en la tabla mipres_1 , si el registro no existe.
							}

							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo

							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;



						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)

				}//if(!is_string($resultdir) )
				else{//no se pudo actualizar
					return $arrvacio ;
				}
				// TERMINA EL DIRECCIONAMIENTO



				$xxxservicio = "EntregaXPrescripcion";
				$resultentrega = get_wsxxx_x_prescripcion($xxxservicio , $nroPrescripcion , $wemp_pmla);
				if(!is_string($resultentrega) ){
					$jsonMipres = $resultentrega;// json_decode($resultentrega);

					if(count($jsonMipres)>0)
					{
						foreach($jsonMipres as $key => $value)
						{//
							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo

							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;


						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)
				}//if(!is_string($resultentrega) ){
				else{//no se pudo actualizar
					return $arrvacio ;
				}

				$xxxservicio = "ReporteEntregaXPrescripcion";
				$resultreporte = get_wsxxx_x_prescripcion($xxxservicio , $nroPrescripcion , $wemp_pmla);
				if(!is_string($resultreporte) ){

					$jsonMipres = $resultreporte;//json_decode($resultreporte);

					if(count($jsonMipres)>0)
					{
						foreach($jsonMipres as $key => $value)
						{
							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo

							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;



						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)

				}//if(!is_string($resultreporte) )
				else{//no se pudo actualizar
					return $arrvacio ;
				}




// Fin ID de facturacion de una prescripcion				
				
				
			}//for ($i=0;$i<count($arrprescripciones);$i++)

		}//actualizacion por prescripcion

		else{//if ($nroPrescripcion != "")

			while ($fecha1 <= $fechaFinal){





				$resultdir =  get_wsxxx_x_fecha("DireccionamientoXFecha",$fecha1 , $wemp_pmla);


				if(!is_string($resultdir) ) // != false)
				{
					$jsonMipres = $resultdir;//json_decode($resultdir);

					if(count($jsonMipres)>0)
					{
						foreach($jsonMipres as $key => $value)
						{

							//18 de Diciembre 2019 Freddy Saenz , crear registros en las tablas mipres_1 y mipres_2 , si el direccionamiento
							//no tiene registros en estas tablas.
							if ( $wmodoambulatorio != 0 ){//aplica para el idc
								$vnumregmp1 = crearRegistrosMp1XJson( $value );//crear los registros en la tabla mipres_1 , si el registro no existe.
							}

							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo

							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;

							$vFecRepEntrega = "";


						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)
				}//if($resultdir != false)
				elseif ($resultdir != "[]") {//no se pudo actualizar, pero habia informacion , 17 Diciembre 2019 Freddy Saenz
//var_dump($resultdir);						
					return $arrvacio ;
				}


				$resultpro =  get_wsxxx_x_fecha("ProgramacionXFecha",$fecha1 , $wemp_pmla);

				if(!is_string($resultpro) ) // != false)
				{
					$jsonMipres = $resultpro ;//json_decode($resultpro);//de texto a json

					if(count($jsonMipres)>0)
					{
						foreach($jsonMipres as $key => $value)
						{
							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo

							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;



						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)
				}//if($resultdir != false)
				elseif ($resultpro != "[]") {//no se pudo actualizar, pero habia informacion , 17 Diciembre 2019 Freddy Saenz
//var_dump($resultpro);						
					return $arrvacio ;
				}


				$resultentrega =  get_wsxxx_x_fecha("EntregaXFecha",$fecha1 , $wemp_pmla);

				if(!is_string($resultentrega) )// != false)
				{
					$jsonMipres = $resultentrega;// json_decode($resultentrega);

					if(count($jsonMipres)>0)
					{
						foreach($jsonMipres as $key => $value)
						{
							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;



						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)
				}//if($resultentrega != false)
				elseif ($resultentrega != "[]") {//no se pudo actualizar, pero habia informacion , 17 Diciembre 2019 Freddy Saenz
//var_dump($resultentrega);						
					return $arrvacio ;
				}



				$resultreporte =  get_wsxxx_x_fecha("ReporteEntregaXFecha",$fecha1 , $wemp_pmla);

				if(!is_string($resultreporte) ) // != false)
				{
					$jsonMipres = $resultreporte;//json_decode($resultreporte);

					if(count($jsonMipres)>0)
					{
						foreach($jsonMipres as $key => $value)
						{
							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;



						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)
				}//if($resultreporte != false)
				elseif ($resultreporte != "[]") {//no se pudo actualizar, pero habia informacion , 17 Diciembre 2019 Freddy Saenz
//var_dump($resultreporte);						
					return $arrvacio ;
				}
//IDs de facturacion
/* */
				$resultreporte =  get_wsxxx_x_fechafacturacion("FacturacionXFecha",$fecha1 , $wemp_pmla);

				if(!is_string($resultreporte) ) // != false)
				{
					$jsonMipres = $resultreporte;//json_decode($resultreporte);

					if(count($jsonMipres)>0)
					{
						foreach($jsonMipres as $key => $value)
						{
							$arrvaluetmp = array();
							//$vclaveidunica  = $value["Id"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							$vclaveidunica  = $value["NoPrescripcion"]."_".$value["TipoTec"]."_".$value["ConTec"]."_".$value["NoEntrega"];//9 Agosto 2019 ,Freddy Saenz se cambia operador -> por ["campo"]//clave con numero de entrega 9 julio 2019
							foreach($value as $key2 => $value2){
								if ($key2 == "ID" ){
									$arrvaluetmp["ID2"] = $value2;//el ID de facturacion es diferente del ID de Suministros
								}else{
									$arrvaluetmp[$key2] = $value2;
								}
								
							}

							$value["ID2"] = $value["ID"];
							$value["ID"] = "";//el ID de facturacion es diferente del ID de suministros.

							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
									$arrresult[] = $value;//evitar duplicados
							}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
							$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $arrvaluetmp ) ;// $value )	;
							$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;



						}//foreach($jsonMipres as $key => $value)

					}//if(count($jsonMipres)>0)
				}//if($resultreporte != false)
				elseif ($resultreporte != "[]") {//no se pudo actualizar, pero habia informacion , 17 Diciembre 2019 Freddy Saenz
//var_dump($resultreporte);						
					return $arrvacio ;
				}
				
				
//actualizar IDs x fecha
		
				

				//actualizar a fecha1

				$wfechahoy=date("Y-m-d");
				if ($fecha1 < $wfechahoy){//si es anterior a hoy .  No se puede actualizar a la fecha actual , porque
				//se pueden hacer direccionamientos , programaciones , envios y reportes de envio durante el dia actual (hoy).
					$where  = "Detapl = 'fechaactualizacionIDsSuministroMipres' ";
					$where  .= " AND Detemp = '".$wemp_pmla."' ";

					$setcampos = "Detval = '".$fecha1. "' ";
					$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);

				}







				$fecha1 = date("Y-m-d",strtotime($fecha1."+ 1 days"));
			}//while ($fecha1 <= $fechaFinal)
		}//else if ($nroPrescripcion != "")


		return $arrresult;

	}//function actualizarIDsPrescripcionesPeriodo($wemp_pmla,$fechaInicial,$fechaFinal , $nroPrescripcion)


	function actualizarLineaWebservice($numprescripcion , $vconsecutivo , $vtipotec , $vID , $ID2enJson ,$vID2 , $vTecnologia , $vFecRepEntrega )
	{

		$tablaActualizar = tablaxtipotec($vtipotec);
		$sufijoid2entabla = "";
		$prefijoTabla  = "";
		switch ($ID2enJson){
			case "IDEntrega":
				$sufijoid2entabla = "IDe";//ej. MedIDe con e de sufijo
				break;
			case "IDReporteEntrega":
				$sufijoid2entabla = "IDr";//ej. MedIDr con r de sufijo
				break;

		}
		$sufijoTec = "";
		if ($vTecnologia != ""){
			$sufijoTec = "cst";//codigo de tecnologia a entregar
		}
		$numlineasup = 0;
		$where = "";
		if ($vID != 0){
			switch($vtipotec){
				case "M":
					$setcampos = " MedID = $vID ";

					$prefijoTabla   = "Med";

					$where = " Mednop = '" . $numprescripcion . "' AND Medcom = '" . $vconsecutivo . "' ";
					break;
				case "P":
					$setcampos = " ProID = $vID ";
					$prefijoTabla   = "Pro";
					$where = " Pronop = '" . $numprescripcion . "' AND Procop = '" . $vconsecutivo . "' ";
					break;
				case "D":
					$setcampos = " DisID = $vID ";
					$prefijoTabla   = "Dis";
					$where = " Disnop = '" . $numprescripcion . "' AND Discod = '" . $vconsecutivo . "' ";
					break;
				case "N":
					$setcampos = " NutID = $vID ";
					$prefijoTabla   = "Nut";
					$where = " Nutnop = '" . $numprescripcion . "' AND Nutcon = '" . $vconsecutivo . "' ";
					break;
				case "S":
					$setcampos = " SerID = $vID ";
					$prefijoTabla   = "Ser";
					$where = " Sernop = '" . $numprescripcion . "' AND Sercos = '" . $vconsecutivo . "' ";
					break;

			}//switch($vtipotec)
			if ($where != ""){
				if ( ($sufijoid2entabla != "") && ($prefijoTabla!= "")  && ($vID2 != 0) ){
					$setcampos .= ", $prefijoTabla".$sufijoid2entabla." = '".$vID2."' ";//ej. MedIDr
				}
				if ($sufijoTec != ""){
					$setcampos .= ", $prefijoTabla".$sufijoTec." = '".$vTecnologia."' ";//ej. MedIDr
				}
				if (  ( $vFecRepEntrega != "" ) && (  $vFecRepEntrega != "0000-00-00" ) ) {
					$setcampos .= ", $prefijoTabla"."fen = '".$vFecRepEntrega."' ";//ej. MedIDr
				}
				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
			}
		}//if ($vID != 0)
		return $numlineasup;
	}//actualizarLineaWebservice($numprescripcion , $vconsecutivo , $vtipotec , $vID , $ID2enJson ,$vID2 )

	function queryEstadoMiPres ($vtipotec , $filtroestadomipres){

		$qEstado = "";
		$prefijoTabla = "Sum";//todo sobre la misma tabla
		/*
		switch($vtipotec){
			case "M":
				$prefijoTabla = "Med";
				break;
			case "P":
				$prefijoTabla = "Pro";
				break;
			case "D":
				$prefijoTabla = "Dis";
				break;

			case "N":
				$prefijoTabla = "Nut";
				break;
			case "S":
				$prefijoTabla = "Ser";
				break;


		}*/
		if ($prefijoTabla != ""){
			switch($filtroestadomipres){
				case "SinDireccionamiento":
/*  22 junio 2019 ,no filtrar aqui, porque realmente no debe usar la tabla mipres_000037 , posteriormente debe buscar el ID =0 , pero no a nivel de base de datos.
					$qEstado = " AND ".$prefijoTabla."ID = 0 ";//ID prescripcion
					$qEstado .= " AND ".$prefijoTabla."IDd = 0 ";//ID direcionamiento
					$qEstado .= " AND ".$prefijoTabla."IDp = 0 ";//ID programacion
					$qEstado .= " AND ".$prefijoTabla."IDe = 0 ";//ID Entrega
					$qEstado .= " AND ".$prefijoTabla."IDr = 0 ";//ID Reporte Entrega
*/
					break;

				case "Direccionamiento":
					$qEstado = " AND ".$prefijoTabla."ID > 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDd > 0 ";//ID direcionamiento
					$qEstado .= " AND ".$prefijoTabla."IDp = 0 ";//ID programacion
					$qEstado .= " AND ".$prefijoTabla."IDe = 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDr = 0 ";
					$qEstado .= " AND ".$prefijoTabla."edi = 1 ";//estado direccionamiento 1 = activo
					break;

				case "Programacion":
					$qEstado = " AND ".$prefijoTabla."ID > 0 ";
				//	$qEstado .= " AND ".$prefijoTabla."IDd = 0 ";//ID direcionamiento
					$qEstado .= " AND ".$prefijoTabla."IDp > 0 ";//ID programacion  1 = activo
					$qEstado .= " AND ".$prefijoTabla."IDe = 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDr = 0 ";
					$qEstado .= " AND ".$prefijoTabla."epr = 1 ";//estado programacion  = activo
					break;


				case "Envio":
					$qEstado = " AND ".$prefijoTabla."ID > 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDe > 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDr = 0 ";
					$qEstado .= " AND ".$prefijoTabla."een = 1 ";//estado envio 1 = activo

					break;
				case "Reporte-Entrega"://16 septiembre 2019 , ya no se usa esta opcion.
					$qEstado = " AND ".$prefijoTabla."ID > 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDe > 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDr > 0 ";
					$qEstado .= " AND ".$prefijoTabla."ere = 1 ";//estado reporte de entrega 1 = activo
					break;

				case "Completas"://
					$qEstado = " AND ".$prefijoTabla."ID > 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDe > 0 ";
					$qEstado .= " AND ".$prefijoTabla."IDr > 0 ";
					$qEstado .= " AND ".$prefijoTabla."ere >= 1 ";//estado reporte de entrega 2 = procesado
//$qEstado .= " AND ".$prefijoTabla."ere = 2 ";//estado reporte de entrega 2 = procesado
					break;

				case "Anuladas"://anuladas
					$qEstado = " AND ".$prefijoTabla."ID > 0 ";
					$qEstado .= " AND ( ".$prefijoTabla."edi = 0 ";//estado direccionamiento 0 = ANULADO
					$qEstado .= " OR    ".$prefijoTabla."epr = 0 ";//estado rprogramacion  0 = ANULADO
					$qEstado .= " OR    ".$prefijoTabla."ere = 0 ";//estado reporte de entrega  0 = ANULADO
					$qEstado .= " OR    ".$prefijoTabla."een = 0 )";//estado entrega  0 = ANULADO

				default:
					break;
			}

		}//if ($prefijoTabla != "")
		return $qEstado;
	}//function queryEstadoMiPres ($vtipotec , $filtroestadomipres)



	//Busquedas , Basado en ctcmipres.php , query del detalle de la prescripcion y no de solo el encabezado (mipres_000001)
	//22 de Agosto 2019, Freddy Saenz , nuevos criterios de busqueda //$vIDFacturacionbuscar , $vIDProgramacionbuscar , $vNumeroFacturabuscar
	function buscarPrescripcionesSuministros($wemp_pmla,$fechaInicial,$fechaFinal,$tipDocPac,$docPac,$tipDocMed,$docMed,$codEps,$tipoPrescrip,$nroPrescripcion,$filtroMipres,$ambitoAtencion , $fitrompproveedor , $fechamipresprovedores , $vIDbuscarstr,$vIDEntregabuscar, $vIDReportebuscar , $filtroestadomipres  , $cadidclaveprincipal , $vIDFacturacionbuscar , $vIDProgramacionbuscar , $vNumeroFacturabuscar )
	{


		
		global $conex;
		global $wbasedato;
		global $wbasedatomipres;

		
		if (is_string($vIDbuscarstr)){
			$vIDbuscar = intval($vIDbuscarstr);
		}
		else{
			$vIDbuscar = $vIDbuscarstr;
		}
		$resDetmipres = array();

		$fechaInicialMonitorMipres = consultarAliasPorAplicacion($conex,$wemp_pmla , 'fechaInicioMonitorMipres');

		$arrayPrescripciones = array();
		
		/*
		if($filtroMipres=="SinOrdenes")
		{
			$queryPrescripciones = "SELECT Ctcmip AS Prescripcion
										FROM ".$wbasedato."_000134
									 WHERE Ctcmip!=''
										 AND (Ctcacc='E' OR Ctcacc='EM')

									 UNION

									SELECT Ctcmia AS Prescripcion
										FROM ".$wbasedato."_000134
									 WHERE Ctcmia!=''
										 AND (Ctcacc='E' OR Ctcacc='EM')

									 UNION

									SELECT Ctcmip AS Prescripcion
										FROM ".$wbasedato."_000135
									 WHERE Ctcmip!=''
										 AND (Ctcacc='E' OR Ctcacc='EM')

									 UNION

									SELECT Ctcmia AS Prescripcion
										FROM ".$wbasedato."_000135
									 WHERE Ctcmia!=''
										 AND (Ctcacc='E' OR Ctcacc='EM');";



			$resPrescripciones = mysql_query($queryPrescripciones, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPrescripciones . " - " . mysql_error());
			$numPrescripciones = mysql_num_rows($resPrescripciones);

			if( $numPrescripciones > 0 )
			{
				while($rowsPrescripciones = mysql_fetch_array($resPrescripciones))
				{
					$arrayPrescripciones[$rowsPrescripciones['Prescripcion']] = $rowsPrescripciones['Prescripcion'];
				}
			}
			
		}
*/

		$filtroPaciente = "";
		if($tipDocPac!="" && $docPac != "")
		{
			$filtroPaciente = " AND Pretip='".$tipDocPac."'
								AND Preidp='".$docPac."'";
		}

		$filtroMedico = "";
		if($tipDocMed!="" && $docMed != "")
		{
			$filtroMedico = " AND Pretim='".$tipDocMed."'
								AND Preidm='".$docMed."'";
		}

		$filtroEps = "";
		if($codEps!="")
		{
			$filtroEps = " AND Preeps='".$codEps."'";
		}

		$filtroNroPrescripcion = "";
		if($nroPrescripcion != "")
		{
			$nroPrescripcion2 = $nroPrescripcion;
			$nroPrescripcion2 = str_replace(" " , "" , $nroPrescripcion2);//quitar los espacios
			$nroPrescripcion2 = str_replace("\r" , "+" , $nroPrescripcion2);//quitar los returns
			$nroPrescripcion2 = str_replace("\n" , "+" , $nroPrescripcion2);//quitar los saltos de linea
			$nroPrescripcion2 = str_replace("++" , "+" , $nroPrescripcion2);//quitar algunos blancos

			$nroPrescripcion2 = "'" . str_replace("+" , "','" , $nroPrescripcion2) . "'";//convierta la cadena a+b+c por  'a','b','c' para ser usada en un query
		//	$filtroNroPrescripcion = " AND Prenop='".$nroPrescripcion2."'";//27 mayo 2017
			$filtroNroPrescripcion = " AND Prenop IN ( $nroPrescripcion2 ) ";//seleccionar varias prescripcioes al tiempo.
		}

		$filtroAmbitoAtencion = "";
		if($ambitoAtencion!="")
		{
			$filtroAmbitoAtencion = " AND Precaa='".$ambitoAtencion."'";
		}


		$tablaFiltroTipoPresc = "";

		if (!is_numeric($vIDbuscar)) {
			$vIDbuscar = 0;
		}
		if (!is_numeric($vIDEntregabuscar)) {
			$vIDEntregabuscar = 0;
		}
		if (!is_numeric($vIDReportebuscar)) {
			$vIDReportebuscar = 0;
		}
		if (!is_numeric($vIDFacturacionbuscar)) {
			$vIDFacturacionbuscar = 0;
		}
		if (!is_numeric($vIDProgramacionbuscar)) {
			$vIDProgramacionbuscar = 0;
		}
		if (!is_numeric($vNumeroFacturabuscar)) {
			$vNumeroFacturabuscar = 0;
		}
		$sumaIDs  = $vIDbuscar + $vIDEntregabuscar + $vIDReportebuscar ;
		//22 agosto 2019 , Freddy Saenz , nuevos criterios de busqueda e implementacion del modulo de facturacion
		$sumaIDs += $vIDFacturacionbuscar + $vIDProgramacionbuscar + $vNumeroFacturabuscar;
	//	if( ($filtroNroPrescripcion != "")  ||($vIDbuscar != 0) ||($vIDEntregabuscar != 0) || ($vIDReportebuscar != 0) )//29 mayo 2019
		if( ($filtroNroPrescripcion != "")  || ($sumaIDs != 0)  )//29 mayo 2019
		{//$filtroNroPrescripcion == "") 5 de junio 2019
			// si no tiene numero de prescripción debe tener en cuenta el rango de fecha
			//$filtroFecha = "Prefep BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'";
			$filtroFecha = "Prenop != '' ";//crear un filtro trivial
		}
		else
		{
			//$filtroFecha = "Prefep BETWEEN '".$fechaInicialMonitorMipres."' AND '".date('Y-m-d')."'";
			$filtroFecha = "Prefep BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'";
			if ( ($fechaInicial == "" ) || ($fechaFinal == "") ){
				$filtroFecha = "Prenop != '' ";//crear un filtro trivial
			}
		}


		//Modificacion 21 marzo 2019 , Freddy Saenz
				/*	'1' => 'No enviadas'
			,'2' => 'Altas de Hoy'
			,'3' => 'Direccionados Hoy'
			,'4' => 'Programados para hoy'
			,'5' => 'Enviadas >= 1 semana '
			,'6' => 'Prescripciones >= 1 semana');*/
		$filtromipresprovstr = "";

		if (is_nan($fitrompproveedor)){
			if ($fitrompproveedor == ""){
				$filtroEnteroProv = 0;
			}else{
				$filtroEnteroProv = intval($fitrompproveedor);
			}

		}else{
			$filtroEnteroProv = $fitrompproveedor;
		}
		switch ($filtroEnteroProv) {
			case 0://
				break;
			case 1://No enviados
				$filtromipresprovstr = " AND Sumfem = '0000-00-00' ";//Prefen
				$filtromipresprovstr .= " AND Prefep >= '". $fechamipresprovedores ."' ";//19 junio 2019
				break;
			case 2: //altas hoy
				//,Ubiing falta el ingreso
				$fechaHoy = $fechamipresprovedores ;//date('Y-m-d');
				$selectaltas = "SELECT Ubihis  FROM ".$wbasedato."_000018 WHERE Ubifad = $fechaHoy and Ubiald = 'on' ";
				$filtromipresprovstr = " AND Prehis IN ($selectaltas) ";
				break;

			case 3:// direccionados hoy



				$fecha = $fechamipresprovedores;// "2019-03-13";//date("Y-m-d");

				$vprescripciones  =  fprescripciones_getXFecha("DireccionamientoXFecha",$fecha,$wemp_pmla);
				if ($vprescripciones != ""){
					$vprescripciones = "'" . str_replace("," , "','" , $vprescripciones) . "'";//convierta la cadena a,b,c a  'a','b','c' para ser usada en un query
					$filtromipresprovstr = " AND Prenop IN ( $vprescripciones ) ";
				}else{
					$filtromipresprovstr = " AND Prenop IN ( '00' ) ";
				}
				break;
			case 4 : //Programados para hoy
			//ejecutar el web service
			//
//get /api/ProgramacionXFecha/{nit}/{token}/{fecha}
//buscar por el valor retornado del campo NoPrescripcion

				$fecha = $fechamipresprovedores;//"2019-03-13";//date("Y-m-d");


				$vprescripciones  =  fprescripciones_getXFecha("ProgramacionXFecha",$fecha , $wemp_pmla);
				if ($vprescripciones != ""){
					$vprescripciones = "'" . str_replace(",","','",$vprescripciones) . "'";
					$filtromipresprovstr = " AND Prenop IN ( $vprescripciones ) ";
				}

				break;

			case 7:
				$fechaHoy = date('Y-m-d');
				$vhaceunasemana = date("d-m-Y",strtotime($fechaHoy."- 7 days"));
				$filtromipresprovstr = " AND Sumfem >= $vhaceunasemana ";//Prefen
				$filtromipresprovstr .= " AND Sumfem != '0000-00-00' ";//5 julio 2019 , este encontrando las pendientes tambien
				break;
			case 8://Prescripciones desde hace una semana en adelante
				$fechaHoy = date('Y-m-d');
				$vhaceunasemana = date("d-m-Y",strtotime($fechaHoy."- 7 days"));
				$filtromipresprovstr = " AND Prefep >= $vhaceunasemana ";
				break;
				
			case 9://Facturas enviadas en la fecha
				break;
				
			default:
				break;
			//
			//SELECT Ubihis ,Ubiing FROM ".$wbase2dato."_000018 WHERE Ubifad = $fechaHoy and Ubiald = 'on';
		}
//		$filtromipresprovstr = "";
		//	Prefep,Prehop,Pretip,Preidp,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps,Epsdes

		//se debe dejar en el query el campo CodSerTecAEntregar de ultimo o al menos despues del campo de identificacion
		// del paciente , por que cuando se usen los datos de la consulta le sistema busca la tarifa del paciente
		// para posteriormente  buscar la tecnologia (medicamento , procedimiento , nutrucion ) que se le realizara al paciente
		//si se deja primero CodSerTecAEntregar no va a encontrar tarifa y por lo tanto no mostrara la tecnologia que se le debe
		//realizar al paciente.

//21 junio 2019 la tabla mipres_000037 cuando se necesite
		$usarrmipres37 = false;
		 if ( ( $filtroestadomipres != "VER-TODOS" )  ||  ($cadidclaveprincipal != "" ) ){
			 $usarrmipres37 = true;
		 }else if( $filtroEnteroProv == 1 ){//no enviados
			  $usarrmipres37 = true;
		 }else if( $filtroEnteroProv == 7 ){//entregadas desde una fecha hasta hoy
				 $usarrmipres37 = true;
				 //22 Agosto 2019 Freddy Saenz , nuevos criterios de busqueda
	 	 }else if ( $sumaIDs != 0 ) {//( ($vIDbuscar != 0) ||($vIDEntregabuscar != 0) || ($vIDReportebuscar != 0) )
			  $usarrmipres37 = true;
		 }else if ( $filtroNroPrescripcion != "" ){//mostrar todas las entregas cuando se hace busqueda por prescripcion.
			  $usarrmipres37 = true;
		 }

//19 Junio 2019
		$wherecomunAdicional = "";
		$wherecomun =  " $filtroFecha " ;

		if ($filtroNroPrescripcion != "" ){

		}else{
			$wherecomunAdicional =  "  $filtroPaciente $filtroMedico $filtroEps" ;
			$wherecomunAdicional .= "  $filtroAmbitoAtencion $filtromipresprovstr ";

		}
//Sumcon
//Query por Medicamentos
		$condicionxtipoprescripcionX = "";
		$tablaxestadomipres = "";

		$queryestado1 = queryEstadoMiPres("M",$filtroestadomipres);//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno

		if ( $usarrmipres37 ) {// hacer join con la tabla mipres 37
			$condicionxtipoprescripcionX = " AND Prenop = Sumnop  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumtip = 'M'  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumcon = Medcom  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumnop = Prenop   ";//20 mayo 2019
			
			
			
			$tablaxestadomipres = " , " . $wbasedatomipres."_000037";//si se necesita ya tiene la coma incluida
		}//if ($filtroestadomipres == ""VER-TODOS"")




		$queryMipres1 = "SELECT  '0' as ID , Mednop as NoPrescripcion , 'M' as TipoTec,Medcom as ConTec ,Medctf as SumcanPrescripcion
							,Prefep,Prehop,Pretip,Preidp as NoIDPaciente,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps  , Preepr";
		if ( $usarrmipres37 ){
			$queryMipres1 .= " , Sumcst as CodSerTecAEntregar   , ".$wbasedatomipres."_000037.id as claveidmipressuministros	";
			$queryMipres1 .= " , Sumnen as NoEntrega ";//3 julio 2019 , numero de entrega
		}else{
			$queryMipres1 .= " , '' as CodSerTecAEntregar   , 0 as claveidmipressuministros	";
			$queryMipres1 .= " , 1 as NoEntrega ";//3 julio 2019 , numero de entrega
		}

		$queryMipres1 .= "	FROM ".$wbasedatomipres."_000001,mipres_000033
							,".$wbasedatomipres."_000002 $tablaxestadomipres
							WHERE 	".$wherecomun."
							    AND Preeps=Epscod
							    AND Mednop = Prenop    ";//20 junio 2019

		$queryMipres1 .= $condicionxtipoprescripcionX;//20 mayo Filtro por M(edicamenteos)
		//22 agosto 2019  Freddy Saenz , nuevos criterios de busqueda
		//$sumaIDs += $vIDFacturacionbuscar + $vIDProgramacionbuscar + $vNumeroFacturabuscar;
		$queryxIDs = "";
		if ( $sumaIDs != 0 ){

			if ($vIDbuscar != 0){
				$queryxIDs .= " AND SumID = $vIDbuscar ";
			}
			if ($vIDEntregabuscar != 0){
				$queryxIDs .= " AND SumIDe = $vIDEntregabuscar ";
			}
			if ($vIDReportebuscar != 0){
				$queryxIDs .= " AND SumIDr = $vIDReportebuscar ";
			}
			
			if ($vIDFacturacionbuscar != 0){
				$queryxIDs .= " AND SumIDf = $vIDFacturacionbuscar ";//ID de facturacion
			}
			if ($vIDProgramacionbuscar != 0){
				$queryxIDs .= " AND SumIDp = $vIDProgramacionbuscar ";//ID de programacion
			}
			if ($vNumeroFacturabuscar != 0){
				$queryxIDs .= " AND Sumnfa = $vNumeroFacturabuscar ";//numero de factura
			}			
		}
		if ($cadidclaveprincipal != "" ){
			$queryMipres1 .= " AND  ".$wbasedatomipres."_000037.id IN ( $cadidclaveprincipal ) ";
			$qbasico1 = "";

		}else{
			$qbasico1 = $queryMipres1;//para usar si hay anulados , filtrar por paciente


			$queryMipres1 .= " $filtroNroPrescripcion  ";
			$queryMipres1 .= " $queryxIDs  ";//22 agosto 2019 Freddy Saenz , nuevos criterios de busqueda


			$queryMipres1 .= $queryestado1;//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno
			$queryMipres1 .= $wherecomunAdicional;
						//ORDER BY Prefep,Prehop,Prepnp,Presnp,Prepap,Presap";
		}



//Query por Procedimientos
		$condicionxtipoprescripcionX = "";
		$tablaxestadomipres = "";
		if ( 	$usarrmipres37 ){// hacer join con la tabla mipres 37

			$condicionxtipoprescripcionX = " AND Prenop=Sumnop  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumtip = 'P'  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumcon = Procop   ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumnop = Prenop  ";//20 mayo 2019
			$tablaxestadomipres = " , " . $wbasedatomipres."_000037";//si se necesita ya tiene la coma incluida
		}

		$queryestado2 = queryEstadoMiPres("P",$filtroestadomipres);//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno //Procfo
		$queryMipres2 = "SELECT   '0' as ID ,Pronop as NoPrescripcion ,'P' as TipoTec ,Procop as ConTec ,Procat  as SumcanPrescripcion
							,Prefep,Prehop,Pretip,Preidp as NoIDPaciente,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps , Preepr
							, Procup as CodSerTecAEntregar  ";
		if ( 	$usarrmipres37 ){// hacer join con la tabla mipres 37
			$queryMipres2 .="	,  ".$wbasedatomipres."_000037.id as claveidmipressuministros ";
			$queryMipres2 .= " , Sumnen as NoEntrega ";//3 julio 2019 , numero de entrega
		}else{
			$queryMipres2 .="	,  0 as claveidmipressuministros ";
			$queryMipres2 .= " , 1 as NoEntrega ";//3 julio 2019 , numero de entrega
		}

		$queryMipres2 .=" FROM ".$wbasedatomipres."_000001,mipres_000033
							,".$wbasedatomipres."_000004 $tablaxestadomipres
						  WHERE 	".$wherecomun."
							  AND Preeps=Epscod
							  AND Pronop = Prenop    ";//20 junio 2019

		$queryMipres2 .= $condicionxtipoprescripcionX;//20 mayo Filtro por procedimientos
		if ($cadidclaveprincipal != "" ){
			$queryMipres2 .= " AND  ".$wbasedatomipres."_000037.id IN ( $cadidclaveprincipal ) ";
			$qbasico2 = "";

		}else{


			$qbasico2 = $queryMipres2;//para usar si hay anulados , filtrar por paciente
			$queryMipres2 .= " $filtroNroPrescripcion  ";
			$queryMipres2 .= " $queryxIDs  ";//22 agosto 2019 Freddy Saenz , nuevos criterios de busqueda

			$queryMipres2 .= $queryestado2;//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno
			$queryMipres2 .= $wherecomunAdicional;
		}
//var_dump($queryMipres2);



// Query por dispositivos
		$condicionxtipoprescripcionX = "";
		$tablaxestadomipres = "";
		if ( 	$usarrmipres37 ){//no hacer join con la tabla mipres 37

			$condicionxtipoprescripcionX = " AND Prenop=Sumnop  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumtip = 'D'  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumcon = Discod  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumnop = Prenop  ";//20 mayo 2019
			$tablaxestadomipres = " , " . $wbasedatomipres."_000037";//si se necesita ya tiene la coma incluida
		}

		$queryestado5 = queryEstadoMiPres("D",$filtroestadomipres);//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno //Discfo
		$queryMipres5 = "SELECT   '0' as ID  ,Disnop as NoPrescripcion ,'D' as TipoTec ,Discod as ConTec ,Discat  as SumcanPrescripcion
										,Prefep,Prehop,Pretip,Preidp as NoIDPaciente,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps
										, Discod as CodSerTecAEntregar  , Preepr ";

		if ( 	$usarrmipres37 ){// hacer join con la tabla mipres 37
			$queryMipres5 .= " ,  ".$wbasedatomipres."_000037.id as claveidmipressuministros ";
			$queryMipres5 .= " , Sumnen as NoEntrega ";//3 julio 2019 , numero de entrega
		}else{
			$queryMipres5 .= " ,  0 as claveidmipressuministros ";
			$queryMipres5 .= " , 1 as NoEntrega ";//3 julio 2019 , numero de entrega
		}

		$queryMipres5 .= " FROM ".$wbasedatomipres."_000001,mipres_000033
								,".$wbasedatomipres."_000005 $tablaxestadomipres
						WHERE 	".$wherecomun."
								AND Preeps=Epscod
								AND Disnop = Prenop    ";//20 junio 2019

		$queryMipres5 .= $condicionxtipoprescripcionX;//20 mayo Filtro por dispositivos
		if ($cadidclaveprincipal != "" ){
			$queryMipres5 .= " AND  ".$wbasedatomipres."_000037.id IN ( $cadidclaveprincipal ) ";
			$qbasico5 = "";

		}else{

			$qbasico5 = $queryMipres5;//para usar si hay anulados , filtrar por paciente
			$queryMipres5 .= " $filtroNroPrescripcion  ";
			$queryMipres5 .= " $queryxIDs  ";//22 agosto 2019 Freddy Saenz , nuevos criterios de busqueda

			$queryMipres5 .= $queryestado5;//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno
			$queryMipres5 .= $wherecomunAdicional;
		}


//	Query por nutriciones
		$condicionxtipoprescripcionX = "";
		$tablaxestadomipres = "";
		if ( 	$usarrmipres37 ){// hacer join con la tabla mipres 37

			$condicionxtipoprescripcionX = " AND Prenop=Sumnop  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumtip = 'N'  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumcon = Nutcon  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumnop = Prenop  ";//20 mayo 2019
			$tablaxestadomipres = " , " . $wbasedatomipres."_000037";//si se necesita ya tiene la coma incluida
		}

		$queryestado6 = queryEstadoMiPres("N",$filtroestadomipres);//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno //Nutcfo
		$queryMipres6 = "SELECT  '0' as ID  , Nutnop as NoPrescripcion ,'N' as TipoTec ,Nutcon as ConTec ,Nutctf  as SumcanPrescripcion
							,Prefep,Prehop,Pretip,Preidp as NoIDPaciente,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps  , Preepr
							, Nutdpn as CodSerTecAEntregar ";
		if ( 	$usarrmipres37 ){// hacer join con la tabla mipres 37
			$queryMipres6 .= " ,  ".$wbasedatomipres."_000037.id as claveidmipressuministros ";
			$queryMipres6 .= " , Sumnen as NoEntrega ";//3 julio 2019 , numero de entrega
		}else{
			$queryMipres6 .= " ,  0 as claveidmipressuministros ";
			$queryMipres6 .= " , 1 as NoEntrega ";//3 julio 2019 , numero de entrega
		}
		$queryMipres6 .= "	FROM ".$wbasedatomipres."_000001,mipres_000033
								,".$wbasedatomipres."_000006 $tablaxestadomipres
							WHERE 	".$wherecomun."
							   AND Preeps=Epscod
							   AND Nutnop = Prenop     ";//20 junio 2019

		$queryMipres6 .= $condicionxtipoprescripcionX;//20 mayo Filtro por nutriciones
		if ($cadidclaveprincipal != "" ){
			$queryMipres6 .= " AND  ".$wbasedatomipres."_000037.id IN ( $cadidclaveprincipal ) ";
			$qbasico6 = "";

		}else{


			$qbasico6 = $queryMipres6;//para usar si hay anulados , filtrar por paciente
			$queryMipres6 .= " $filtroNroPrescripcion  ";
			$queryMipres6 .= " $queryxIDs  ";//22 agosto 2019 Freddy Saenz , nuevos criterios de busqueda

			$queryMipres6 .= $queryestado6;//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno
			$queryMipres6 .= $wherecomunAdicional;
		}
// Query servicios complementarios
		$condicionxtipoprescripcionX = "";
		$tablaxestadomipres = "";
		if ( 	$usarrmipres37 ){// hacer join con la tabla mipres 37

			$condicionxtipoprescripcionX = " AND Prenop=Sumnop  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumtip = 'S'  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumcon = Sercos  ";//20 mayo 2019
			$condicionxtipoprescripcionX .= " AND Sumnop = Prenop  ";//20 mayo 2019
			$tablaxestadomipres = " , " . $wbasedatomipres."_000037";//si se necesita ya tiene la coma incluida
		}

		$queryestado7 = queryEstadoMiPres("S",$filtroestadomipres);//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno //Sercaf
		$queryMipres7 = "SELECT   '0' as ID  ,Sernop as NoPrescripcion ,'S' as TipoTec ,Sercos as ConTec ,Sercat  as SumcanPrescripcion
							,Prefep,Prehop,Pretip,Preidp as NoIDPaciente,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps
							, Sercsc as CodSerTecAEntregar , Preepr ";
		if ( 	$usarrmipres37 ){// hacer join con la tabla mipres 37
			$queryMipres7 .= " ,  ".$wbasedatomipres."_000037.id as claveidmipressuministros ";
			$queryMipres7 .= " , Sumnen as NoEntrega ";//3 julio 2019 , numero de entrega
		}else{
			$queryMipres7 .= " ,  0 as claveidmipressuministros ";
			$queryMipres7 .= " , 1 as NoEntrega ";//3 julio 2019 , numero de entrega
		}
		$queryMipres7 .= "	FROM ".$wbasedatomipres."_000001,mipres_000033
								,".$wbasedatomipres."_000007 $tablaxestadomipres
						    WHERE 	".$wherecomun."
							     AND Preeps=Epscod
							     AND Sernop = Prenop     ";//20 junio 2019

		$queryMipres7 .= $condicionxtipoprescripcionX;//20 mayo filtro por servicios complementarios
		if ($cadidclaveprincipal != "" ){
			$queryMipres7 .= " AND  ".$wbasedatomipres."_000037.id IN ( $cadidclaveprincipal ) ";
			$qbasico7 = "";

		}else{


			$qbasico7 = $queryMipres7;//para usar si hay anulados , filtrar por paciente
			$queryMipres7 .= " $filtroNroPrescripcion  ";
			$queryMipres7 .= " $queryxIDs  ";//22 agosto 2019 Freddy Saenz , nuevos criterios de busqueda

			$queryMipres7 .= $queryestado7;//Direccionamiento o Programacion, Entrega o Reporte de Entrega o ninguno
			$queryMipres7 .= $wherecomunAdicional;
		}
		switch($tipoPrescrip){//filtro por tipo (tabla) de prescripcion
			case "M":
				$qUnion  = $queryMipres1;
				break;
			case "P":
				$qUnion  = $queryMipres2;
				break;
			case "D":
				$qUnion  = $queryMipres5;
				break;
			case "N":
				$qUnion  = $queryMipres6;
				break;
			case "S":
				$qUnion  = $queryMipres7;
				break;
			default :

				$qUnion  = $queryMipres1 . " UNION " . $queryMipres2 ;
				$qUnion .=  " UNION " . $queryMipres5;
				$qUnion .=  " UNION " . $queryMipres6;
				$qUnion .=  " UNION " . $queryMipres7;
				break;

		}

		
		if ($qUnion != ""){
//			$qUnion .= " ORDER BY 2 , 4 , 3 ";//Prefep,Prehop,Prepnp,Presnp,Prepap,Presap ";
//Modificacion 4 julio 2019 Freddy Saenz, se agrega el orden por numero de entrega
			$qUnion .= " ORDER BY NoPrescripcion , ConTec , TipoTec , NoEntrega";//Prefep,Prehop,Prepnp,Presnp,Prepap,Presap ";

		}
			//;
//var_dump($qUnion);




		$resMipres = mysql_query($qUnion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qUnion . " - " . mysql_error());
		$numMipres = mysql_num_rows($resMipres);




		$arrayMipres = array();
		$cta = 0;
		if($numMipres>0)
		{
			//var_dump($qUnion);

			$arrIDsunicos = array();

			$arrdet = array ();
			$ctaids = 0;
			while($rowsmipres = mysql_fetch_array($resMipres))

			{
				//$arrdet[] = $rowsmipres;



				//$vclaveidunica  = $rowsmipres->NoPrescripcion."_".$rowsmipres->TipoTec."_".$rowsmipres->ConTec;
				$vclaveidunica  = $rowsmipres["NoPrescripcion"]."_".$rowsmipres["TipoTec"]."_".$rowsmipres["ConTec"]."_".$rowsmipres["NoEntrega"]   ;
				//3 julio 2019 ,con numero de entrega.
				if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
					$arrIDsunicos["$ctaids"] = $vclaveidunica;//
					$ctaids++;
					$arrdet[] = $rowsmipres;//evitar duplicados
				}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
				if ($rowsmipres["Preepr"] == '2'){//prescripciones anuladas
					$idPaciente = $rowsmipres["NoIDPaciente"];
					$numinicprescripcion = $rowsmipres["NoPrescripcion"];
					$qxpaciente = "";



					switch ($rowsmipres["TipoTec"]){
						case "M"://medicamentos
							$qxpaciente = $qbasico2;

						case "P"://procedimientos
							$qxpaciente = $qbasico4;
							break;
						case "N"://Nutriciones
							$qxpaciente = $qbasico6;
							break;
						case "D"://Dispositivos
							$qxpaciente = $qbasico5;
							break;
						case "S"://Servicios
							$qxpaciente = $qbasico7;
							break;
					}//switch ($rowsmipres["TipoTec"])
					if ($qxpaciente != "" ){
						$qxpaciente .=  " AND Preidp ='" . $idPaciente  ."' " ;//para usar si hay anulados , filtrar por paciente
						$qxpaciente .= " AND Prenop > '" . $numinicprescripcion . "' ";//Sumnop


						$resxpaciente = mysql_query($qxpaciente, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qxpaciente . " - " . mysql_error());
						while($rowsxpaciente = mysql_fetch_array($resxpaciente)){//recorres las prescripciones que reemplazaron a los anulados
							$vclaveidunica  = $rowsxpaciente["NoPrescripcion"]."_".$rowsxpaciente["TipoTec"]."_".$rowsxpaciente["ConTec"]."_".$rowsmipres["NoEntrega"]   ;
							if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
								$arrIDsunicos["$ctaids"] = $vclaveidunica;//
								$ctaids++;
								$arrdet[] = $rowsxpaciente;//agregar las prescripciones que reemplazaron a los anulados.
							}
						}//while($rowsxpaciente = mysql_fetch_array($resxpaciente))

					}//if ($qxpaciente != "" )


				}//if ($rowsmipres["Preepr"] == '2'){//prescripciones anuladas


			}//while($rowsmipres = mysql_fetch_array($resMipres))

			//var_dump($arrdet);
			return $arrdet;



		}else{
			$arrvacio = array();
			
			//var_dump($nroPrescripcion2);

			if ( $nroPrescripcion2 != "" ){
			
				$wherestr = " Prenop IN ( $nroPrescripcion2 ) ";
				$vselectpreIDs = "Prenop";//clave principal
				$arrFilaIDs =  select1filaquery($vselectpreIDs, $wbasedatomipres."_000001", $wherestr); //ver si existe el registro
				if ( count ( $arrFilaIDs ) == 0 ){//no existe el registro , se escribio mal

				}else{//si existe el registro ,pero  falta informacion en alguna otra tabla
					$velemento["query"] = $qUnion;
					$arrvacio["0"] = $velemento;//para poder depurar el query
		
				}
	
			}


			return $arrvacio ;

		}


	}//function buscarPrescripcionesSuministros($wemp_pmla,$fechaInicial,$fechaFinal,$tipDocPac,$docPac,$tipDocMed,$docMed,$codEps,$tipoPrescrip,$nroPrescripcion,$filtroMipres,$ambitoAtencion,$fitrompproveedor , $fechamipresprovedores , $vIDbuscar,$vIDEntregabuscar, $vIDReportebuscar)

function enviarlistadoxarreglos ( $arrinfo ){

	foreach($arrinfo as $key => $value){
		$arrJsons = explode("|", $value);//si la entrega se va a realizar usando el ambito de entrega.

	}


}


//envio x listado o seleccion

function enviarAutomatico( $arrinfo , $enviar ){
	global $wbasedato;
	global $wbasedatomipres;
	global $wbasedatocliame;
	global $wmodoambulatorio;

	global $errorenvioseleccion;



	$txtws  = "";
	$vreportestr = "";
//	return count($arrinfo);
//	echo  " envio automatico <br> ".count($arrinfo)."<br>  ";
	foreach($arrinfo as $key => $value)//1 foreach enviarAutomatico $key => $value
	{
		$numprescripcion ="";
		$vconsecutivo = 0;
		$vtipotec = "";
		$vCodTec  = "";
		$tipoID = "";
		$ceduladir = "";
		$fechaMipres = "";
		$cantidatTotAEntregar = 0;
		$vID = 0;
		$noentrega = 1;
		$fechacargo = "";
		$valormodificado = 0;

		foreach($value as $key2 => $value2){////2 foreach enviarAutomatico $key => $value $value as $key2 => $value2
			switch($key2){
				case "NoPrescripcion":
					$numprescripcion = $value2;
					break;
				case "ConTec":
					$vconsecutivo = intval($value2);
					break;
				case "TipoTec":
					$vtipotec = $value2;
					break;
				case "CodSerTecEntregado"://17 julio 2019 CodSerTecEntregado
				case "CodSerTecAEntregar"://
					$vCodTec = $value2;
					break;
				case "TipoIDPaciente":
				case "Pretip":
					if ($tipoID == ""){
						$tipoID = $value2;
					}

					break;
				case "NoIDPaciente":
					$ceduladir = $value2;
					break;

				case "Prefep":
				//fechaMipres
					//$fechaMipres = $value2;
					break;

				case "FecEntrega":
					$fechaMipres = $value2;
					break;

				case "CantTotAEntregar":
					$cantidatTotAEntregar = intval( $value2);
					break;
				case "ID":
					$vID = intval($value2);

					break;
					//23 mayo 2019
				case "NoEntrega":
					$noentrega = intval($value2);
					break;


			}
		}




		if ( ($fechaMipres != "")  ) {//&& ($vID != "")
//			echo $numprescripcion . " con fecha mipres $fechaMipres ";
//	Modificacion 3 julio 2019  , si la entrega no es la primera , la fecha del mipres ya no es valida , y es necesario entonces usar
// como fecha , la fecha maxima de entrega .


			//ENVIO AUTOMATICO
			//4 de julio , Freddy Saenz , se modifica el script para que los ingresos de los pacientes los busque con fecha posterior
			//a la fecha del mipres ( para ambulatorios ) , se configura la variable $wmodoambulatorio , para que el modo de busqueda
			//de hospitalizados se conserve.
			$wherestr = " Sumnop = '".$numprescripcion."' and Sumtip = '".$vtipotec."' AND Sumcon = '".$vconsecutivo."'";
			$wherestr .= " AND Sumnen = '".$noentrega ."' ";


			$fechamaximaentrega = "";

			$fechamaximaentrega = select1campoquery("Sumfmx", $wbasedatomipres."_000037", $wherestr);
			if ( ($wmodoambulatorio != 0) || ($noentrega > 1 ) ) {

				if ($noentrega > 1 ){//3 JULIO 2019 , incluir la entrega en la clave principal
					$noentrega_1 = $noentrega - 1;
					$wherestr = " Sumnop = '".$numprescripcion."' and Sumtip = '".$vtipotec."' AND Sumcon = '".$vconsecutivo."'";

					$wherestr .= " AND Sumnen = '".$noentrega_1 ."' ";
					$fechamaximaentregaanterior = select1campoquery("Sumfmx", $wbasedatomipres."_000037", $wherestr);
					$arrhistoriascc = consultarHistoriasXCedula($ceduladir, $fechamaximaentregaanterior , $fechamaximaentrega);
				}else{
					$arrhistoriascc = consultarHistoriasXCedula( $ceduladir, $fechaMipres , $fechamaximaentrega );
				}


			}else{
				$arrhistoriascc = consultarHistoriasXCedula( $ceduladir, $fechaMipres , ""  );//no se usa la fecha maxima de entrega para
					//	uso en modo hospitalizado (primero el ingreso y despues las aplicaciones )
					//ambulatorio primero se prescribe y despues el ingreso y las apliaciones
			}

			if ( count($arrhistoriascc) > 0) {


				$histxmedi = $arrhistoriascc[0]['Inghis']; //'33094';
				$ingxhist = $arrhistoriascc[0]['Ingnin']; // '3';
				$ingtar = $arrhistoriascc[0]['Ingtar'];
				$causanoentrega = 0;

				$arrinfomipres = infocantidadvlrycodigomipres($numprescripcion , $vconsecutivo , $fechaMipres , $vCodTec , $histxmedi, $ingxhist, $ingtar , $vtipotec , "" , $noentrega , $fechamaximaentrega);
				if (count($arrinfomipres)>0){



					$codcum = $arrinfomipres[0]['Cum'];
					$codarticulo = $arrinfomipres[0]['CodArticulo'];//codigo que trae el mipres
					$codArtInterno = $arrinfomipres[0]['CodInterno'];//CodInterno en la bd matrix
					$cantart = $arrinfomipres[0]['Cantidad'];
					$valormed = $arrinfomipres[0]['Valor'];
					$valormodificado = $arrinfomipres[0]['ValorModificado'];

					$vdescripcionInterna = $arrinfomipres[0]['DescInterna'];
					$codservxmipres = $arrinfomipres[0]['CodDeEntrega'];//el codigo que se usara en el mipres de envio y programacion.
					$cantidadOriginal = $arrinfomipres[0]['CantidadOriginal'];
					$vID = $arrinfomipres[0]['ID'];//ver si ya tenia resultado antes
					$fechacargo = $arrinfomipres[0]["FechaCargo"] ;

					$causanoentrega = $arrinfomipres[0]["CausaNoEntrega"] ;//29 mayo


					if ($vtipotec ==  "P"){

						$wherestr = " Tcarprocod = '".$codArtInterno."' and Tcarhis = '".$histxmedi."' AND Tcaring = '".$ingxhist."'";
						$vfechaentrega = select1campoquery("Tcarfec", $wbasedatocliame."_000106", $wherestr);

					}else{//envioautomatico
						$wherestr = " Aplart = '".$codArtInterno."' and Aplhis = '".$histxmedi."' AND Apling = '".$ingxhist."'";
						$vfechaentrega = select1campoquery("Aplfec", $wbasedato."_000015", $wherestr);

					}


					if (($vfechaentrega == "") && ($fechacargo != "")){
						$vfechaentrega = $fechacargo ;
					}



					$lote = "";
					$estadoentrega= 1;

					$arrenvio = array();
					$datareporte = array();//$cantart
					
					//$vCodTec
					$idtablamipres37 = 0;
					if ( $vCodTec != "" ) {//17 julio 2019 ($codservxmipres != "")){ //($cantart > 0) //si hay cantidades y tiene codigo para mipres
//($cantart > 0)  && 


						$ventregatotal = 0;
						if ( $cantart >= $cantidatTotAEntregar ){
							//$cantart = $cantidatTotAEntregar;//la cantidad no puede ser superior a la cantidad maxima, 27 mayo 2019
							//en el envio automatico la cantidad corresponde a la cantidad maxima a enviar .
							$ventregatotal = 1;
						}
						if ($vID == 0){
							$arrenvio = creararrxenvio ($numprescripcion,$vtipotec,$vconsecutivo,$tipoID,$ceduladir,$noentrega,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote);
							//para comentar .
							//$resxborrar->ID = "12343";
							//$vID = $resxborrar->ID;//
							
							//crear el registro en la tabla mipres_000037 si no existe.
							//29 julio 2019
							$wherestr = "Sumnop = '".$numprescripcion."'  AND Sumtip = '".$vtipotec."' AND Sumcon = '".$vconsecutivo."' " ;
							$wherestr .= " AND Sumnen = '".$noentrega."' ";
							$vselectpreIDs = "Id";//clave principal
							$arrFilaIDs =  select1filaquery($vselectpreIDs, $wbasedatomipres."_000037", $wherestr); //informacion de los IDs de la prescripcion
							if ( count ( $arrFilaIDs ) == 0 ){//no existe el registro
							
								$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $arrenvio )	;
								$numfilas = insertupdatemipressuministro($arrtemp);
								$arrFilaIDs =  select1filaquery($vselectpreIDs, $wbasedatomipres."_000037", $wherestr); //informacion de los IDs de la prescripcion
								if ( count( $arrFilaIDs ) > 0 ){//ahora si debe de existir el registro
									$idtablamipres37 = $arrFilaIDs['SumID'];
								}
							}else{
								$idtablamipres37 = $arrFilaIDs['SumID'];
							}

						}else{
							$arrenvio = creaarrxenvioconID($vID,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote , $tipoID , $ceduladir , $numprescripcion );//Se agregan 3 parametros mas , cambio el mipres segun requerimientos del 26 de Julio 2019 ,Freddy Saenz
						}

						if ( $vID != 0){
							$cantart = $cantidatTotAEntregar;//17 de julio , Freddy Saenz usar el json ya validado
							$datareporte = creararrxreporte($vID,$estadoentrega,$causanoentrega,$cantart,$valormed);
						}



						//echo $arrenvio->NoPrescripcion. " con flecha <br> no funciona ";
						//$venviostr = json_encode($arrenvio);//arreglo a texto
						$venviostr = json_encode($value);//arreglo a texto 17 julio 2019 , usar el json que ya fue revisado
						

						
						//echo $venviostr . "<br> ";
						// 29 julio 2019 $txtws .= $venviostr;


						if ($enviar){//modo aplicacion
							
							$respentrega = hacer_entrega($venviostr);//$value
						}else{//modo pruebas
							$respentrega = true ;//hacer_entrega($venviostr);
							//var_dump( $venviostr );
						}


						if (!is_string($respentrega) ){//la respuesta es correcta , hacer el reporte de entrega
							//echo $vreportestr . "<br> ";
							if ( $vID == 0){

								$idresp = "";
								//10 de julio 2019 , se estaba usando el mismo indice para el tercer y cuarto foreach.
								////3 foreach enviarAutomatico $key => $value $value as $key2 => $value2 $respentrega as $key => $value
								foreach($respentrega as $key3 => $value3){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
									foreach($value3 as $key4 => $value4 ){
										////4 foreach enviarAutomatico $key => $value $value as $key2 => $value2 $respentrega as $key => $value
										//
										switch($key4){
										case "Id":
											$idresp = $value4;
											break;

										}//switch($key2)
									}//foreach($value as $key2 => $value2 )

								}//foreach($vJson as $key => $value)


								$vID = $idresp;//$respentrega->ID;//

								//actualizar el ID
								$cantart = $cantidatTotAEntregar;//17 de julio , Freddy Saenz usar el json ya validado
								$datareporte = creararrxreporte($vID,$estadoentrega,$causanoentrega,$cantart,$valormed);
							}
							if ($enviar){//modo aplicacion
								$vreportestr = json_encode($datareporte);//arreglo a texto
								$respreporte = hacer_reporte_entrega($vreportestr);
								if (is_string($respreporte) ){//probablemente sea un error
									$errorenvioseleccion .= $respreporte;
								}	
							}

						}else{//se produjo un error
							if ( is_string($respentrega) ){//probablemente sea un error
								$errorenvioseleccion .= $respentrega;
							}						
							
							
						}
						if ( $idtablamipres37 != 0 ){//29 de julio 2019
							if ( $txtws != "" ){
								$txtws .= " , ";
							}
							$txtws .= " $idtablamipres37 ";
						}
						//$txtws .= $vreportestr;



					} //if ($cantart>0){//si hay cantidades



				}//if (count($arrinfomipres)>0)


			}//if (count($arrhistoriascc) > 0)
		}//if ($fechaMipres != "")
	}//foreach($arrinfo as $key => $value)


	return $txtws;//retornar los IDs de las prescripciones enviadas .

}//function enviarAutomatico( $arrinfo )



	//  busquedas //


		//En la descripcion del medicamento aparecen los palabras claves por las cuales se pueden buscar los medicamentos
	//que van o fueron aplicados a los pacientes.
	//ej [SACUBITRILO] 24,3mg/1U ; [VALSARTAN] 25,7mg/1U
	// de aqui se pueden obtener SACUBITRILO y VALSARTAN
	function palabraclavenombre ( $nombre , $dosis, $campolike )
	{
		global $conex;
		global $wbasedato;

		if ($campolike == ""){
			$campolikewhere = "Artgen";
		}else{
			$campolikewhere = $campolike;
		}
		$arrpalabras  = explode ( "[" , $nombre );



		//empezar en uno
		$arrresult = array();
		$arrPartes = array();
		$dosis2 = "";//21 de junio 2019 , filtrar por dosis
		for ($i=1;$i<count($arrpalabras);$i++){//empieza en 1 para evitar lo que esta antes del [
			$cad = $arrpalabras[$i];
			$pos = strpos($cad, "]");
			if ($pos === false) {
				if ( str_replace(" ","" ,$cad )  != "" ){

			//		$dosis2 = $cad;//filtrar por dosis 21 junio 2019
			//		$dosis2 = str_replace(" ","" ,$dosis2 );

				}

			}else{
				$cad = substr($cad, 0, $pos);
				//$arrresult["$i"] = $cad;
				if ($cad != ""){
					$arrPartes["$i"] = "$cad";
					array_push($arrresult,$cad);
				}
			}

		}


		for ($i=0;$i<count($arrresult);$i++){
			$arrtokens  = explode ( " " , $arrresult[$i] );
			for ($j=0;$j<count($arrtokens);$j++){
				$indice = (100 * $i) + $j;
				if ($arrtokens[$j] != ""){
					//array_push($arrPartes,$indice , $arrtokens[$j] );
					$arrPartes["$indice"] = "$arrtokens[$j]";

				}
			}
		}
		$qLikes = "";


		$arrPartesUnico = array_unique($arrPartes);


		foreach($arrPartesUnico as $key=>$value)
		{
			if ( $arrPartesUnico[$key] != ""){//$arrPartes
				if ($qLikes != ""){

					$qLikes = $qLikes . " OR ";

				}
				$qLikes = $qLikes . " $campolikewhere LIKE '%" . $value . "%' " ;//$arrPartes

			}

		}


/*
		for ($i=0;$i<count($arrPartesUnico);$i++){//$arrPartes
			if ( $arrPartesUnico[$i] != ""){//$arrPartes
				if ($qLikes != ""){

					$qLikes = $qLikes . " OR ";

				}
				$qLikes = $qLikes . " $campolikewhere LIKE '%" . $arrPartesUnico[$i] . "%' " ;//$arrPartes

			}

		}
	*/

		$arrayproductos = array();


		if ($qLikes != "" ){
			$qLikes = "( $qLikes ) AND Artpos = 'N' ";
			$q = " SELECT Artcod , Artcom , Artgen , Artcum FROM ".$wbasedato."_000026 WHERE " . $qLikes ;



			if ( $dosis2 != ""){//$dosis
				$q2 = " SELECT count(*) as cuenta FROM ".$wbasedato."_000026 WHERE " . $qLikes . " AND ( $campolikewhere LIKE '%". $dosis2 . "%' ) ";//con dosis
				$resxcantidad = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				$rowsxcta = mysql_fetch_array($resxcantidad);
				$numfilas = mysql_num_rows($resxcantidad);
				if ($numfilas > 0){
					if ( $rowsxcta['cuenta']  > 0 ) {
						$q .=  " AND ( $campolikewhere LIKE '%" . $dosis2 . "%' ) ";//con dosis
					}

				}

			}
			$resnumprod = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$numproductos = mysql_num_rows($resnumprod);

			if($numproductos>0)
			{
				while($rowsproductos = mysql_fetch_array($resnumprod))
				{
					$vtxtcum = "";
					if ($rowsproductos['Artcum'] != ""){
						$vtxtcum = $rowsproductos['Artcum']  ;
					}else{
						$vtxtcum = "";// "SIN CUM ";
					}

					$arrayproductos[ $rowsproductos['Artcod'] ] = $vtxtcum . "/" .    utf8_encode( $rowsproductos['Artgen'] ) . "/" . utf8_encode( $rowsproductos['Artcom'] )  . "/X Nombre";

				}

			}

		}

		//return $arrPartes;
		return $arrayproductos;//$arrPartes;//$arrresult;
	}//function palabraclavenombre ( $nombre , $dosis , $campolike)


/// =====================================
// Muestra la informacion que aparece el la celde de envio , es decir donde aparece el boton para Enviar el mipres.
	function datacellenvio($cta, $arrenvio ,$cantart,$cantidatTotAEntregar,$vIDEntrega,$vIDReporteEntrega,$codservxmipres ,$valormed , $vID,$estadoentrega,$causanoentrega,$arrenvioID,$vtipotec,$numprescripcion,$vconsecutivo , $fechaaplicacion , $esIngresoManualParametro )//informacion del mipres de envio ,celda en la tabla de despliegue
	{
		global $ingresoManualMipres;
		global $wbasedatomipres;
		
		$varrjsonatxt = "";

		$txtInfoXdialogo = arregloatablahtml($arrenvio);
		$txtInfoIDXdialogo = arregloatablahtml($arrenvioID);


		$varrjsonatxt = json_encode($arrenvio)	;

		$clasexcelda = "";
		if ( ( ($cta) % 2 )== 0){//par+1
			$clasexcelda =  " class='fila2'";//"2";
		}else{
			$clasexcelda  =  " class='fila1'";
		}
//bgcolor="#FF0000"

		$vtxdataenvio  = "";


		$dividcantidad = "<div id = 'divcantidad_".$cta."' >" . $cantart . "</div>";
		$dividcodtecnologia = crearidcampovalor("idcodigotecnologia_" ,$codservxmipres , $cta);
				//"<div id = 'divcantidad_".$cta."' >" . $cantart . "</div>";

		$dividbotoncant = "<div id = 'divcantboton_".$cta."' > ".$cantart."</div>";

		$parametrosfuncionenvio = " \"$cantart\" ,\"$cta\", \"$cantidatTotAEntregar\" , \"$vIDEntrega\" , \"$vIDReporteEntrega\" , \"$codservxmipres\" , \"$valormed\" , \"$vID\", \"$estadoentrega\", \"$causanoentrega\" ";// , \"".$cadjsonenvio."\"
		$parametrosfuncionenvio .= " , \"$vtipotec\" , \"$numprescripcion\" , \"$vconsecutivo\" ";
		$parametrosfuncionenvio .= " , \"$fechaaplicacion\" ";//poder modificar la fecha de aplicacion de la tecnologia

		if ( ( intval($vIDEntrega) != 0 ) || ( intval($vIDReporteEntrega) != 0 ) ){
		//ya se entrego

			$wherestr = " SumID = '".$vID."' ";
			$arrFila = select1filaquery("Sumjen, Sumjre ", $wbasedatomipres."_000037", $wherestr);//Buscar los json en entrega y de reporte de entrega.
			//$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000001", $wherestr); //informacion de la prescripcion

			if ( count($arrFila) > 0){

//	ver jsontxt , cantidad , tecnologia y tarifa	
				//18 septiembre 2019 , usar la informacion de lo enviado.
				$jsonentregatxt = $arrFila["Sumjen"];
				$varrjsonentrega = json_decode($jsonentregatxt , true );
				$cantart = $varrjsonentrega["CantTotEntregada"];
				//$codservxmipres = $varrjsonentrega["CodSerTecEntregado"];

			}
			
	//{"ID":8363854,"IDEntrega":2734270,"NoPrescripcion":"20190808173013646066","TipoTec":"M","ConTec":1,"TipoIDPaciente":"CC","NoIDPaciente":"15455419","NoEntrega":1,"CodSerTecEntregado":"019971860-01","CantTotEntregada":"2","EntTotal":1,"CausaNoEntrega":null,"FecEntrega":"2019-07-06 00:00","NoLote":null,"TipoIDRecibe":"CC","NoIDRecibe":"15455419","EstEntrega":2,"FecAnulacion":null,"CodigosEntrega":null}
	//CantTotEntregada  CodSerTecEntregado 
//{"ID":8363854,"IDReporteEntrega":2630054,"NoPrescripcion":"20190808173013646066","TipoTec":"M","ConTec":1,"TipoIDPaciente":"CC","NoIDPaciente":"15455419","NoEntrega":1,"EstadoEntrega":1,"CausaNoEntrega":null,"ValorEntregado":3105386,"CodTecEntregado":"019971860-01","CantTotEntregada":"2","NoLote":null,"FecEntrega":"2019-07-06 00:00","FecRepEntrega":"2019-09-10 11:28","EstRepEntrega":1,"FecAnulacion":null}
//3105386		
			$ventregastr  = "Entregado " . "($cantart / $cantidatTotAEntregar)";
			$botonenvio = " <input type='button' id='bInfoEnvio".$cta."' value='". $ventregastr. "' onclick='verenvio( $cta , $vID );'>";
//			$botonenvio .= "<div id = 'divcantidad_".$cta."' style='display:none' >" . $cantart . "</div>";//agregar el id de cantidad pero
			//no mostrarla nuevamente.
	//


			$vtxtabla  = "<table align='center' width='100%' >";

			if (intval($vIDReporteEntrega) != 0)	{
						$vtxtabla .= "<tr class='encabezadotabla' >";

			}else{
						$vtxtabla .= "<tr $clasexcelda >";

			}
			$vtxtabla .= "<td colspan='3'  align='center'  >$botonenvio</td> ";

	//		$vtxtabla .= "  ";
			$vtxtabla .= "</tr>";
			//No mostrar nuevamente la cantidad , solo se genera el ID de la cantidad para ser usado posteriormente.
			if ($cantart <= $cantidatTotAEntregar){
				$vtxtabla .= "<tr style='display:none;'><td><div id = 'divcantidad_".$cta."'  >" . $cantart . "</div></td></tr>";//style='display:none'
				
			}else{//en la facturacion se usa la cantidad , no permitir que se facture mas de lo solicitado en el mipres.
				$vtxtabla .= "<tr style='display:none;'><td><div id = 'divcantidad_".$cta."'  >" . $cantidatTotAEntregar . "</div></td></tr>";//style='display:none'
				
			}
			$vtxtabla .= "<tr style='display:none;'><td>$dividcodtecnologia</td></tr>";//style='display:none'


			$vtxtabla .= "</table>";
			$vtxdataenvio = $vtxtabla;//" $botonenvio ";

		}elseif ( ( ($cantart > 0) || ($esIngresoManualParametro==1) ) && ($codservxmipres != "") )//
		//16 julio 2019 , ingreso manual ,para activar el envio con cantidad = 0
		//$esIngresoManualParametro  , permitir que se envien cantidades cero cuando se hace un ingreso manual
		{ //ya tiene informacion del envio
			//Hay informacion para el envio



			if ($cantart >= $cantidatTotAEntregar ){//se puede entregar todo el mipres
			//Entrega TOTAL
				$ventregastr = "";
				if ($cantart > $cantidatTotAEntregar){//mayor estricto
					$ventregastr = "( $cantart / $cantidatTotAEntregar)";
					$cantart = $cantidatTotAEntregar;//27 mayo 2019 ,NO PERMITIR ENVIAR MAS DE LA CANTIDAD TOTAL.
				}
//actualizar el div con la cantidad correcta.
		$dividcantidad = "<div id = 'divcantidad_".$cta."' >" . $cantart . "</div>";
		$dividbotoncant = "<div id = 'divcantboton_".$cta."' > ".$cantart."</div>";
				
				
				
				
				
				$botonenvio = " <input type='button' id='bInfoEnvio".$cta."' value='TOTAL".$ventregastr."' onclick='verenvio( $cta , $vID );'>";
				$clasexceldacompleta = " class='prescripcioncompleta' bgcolor='#66D066' ";//color verde 11 julio 2019

				$vtxtabla  = "<table align='center' width='100%' >";

				$vtxtabla .= "<tr $clasexceldacompleta  align='center' >";
			//	$vtxtabla .= "<td>&nbsp;</td>";
				$vtxtabla .= "<td colspan='3'> $botonenvio </td>";
				$vtxtabla .= "</tr>";




				//boton para modificar la cantidad
				$botonenvio = "   <input type='button' id='bEditarCantidad".$cta."' value='&#9998;' onclick='actualizarcantidad( $parametrosfuncionenvio );'>";//&#9998; caracter de lapiz (editar)
				$vtxtabla .= "<tr $clasexceldacompleta>";
				$vtxtabla .= "<td>Cant. </td>";
				$vtxtabla .= "<td>$dividcantidad</td>";
				$vtxtabla .= "<td>$botonenvio </td>";
				$vtxtabla .= "</tr>";

				//boton para modificar el codigo
				$botonenvio = " <input type='button' id='bEditarCodigo".$cta."' value='&#9998;' onclick='actualizarcodigo( $parametrosfuncionenvio );'>";
				$vtxtabla .= "<tr $clasexceldacompleta>";
				$vtxtabla .= "<td>Cod. </td>";
				$vtxtabla .= "<td>$dividcodtecnologia</td>";
				$vtxtabla .= "<td>$botonenvio </td>";
				$vtxtabla .= "</tr>";


				if ($ingresoManualMipres != 0){//se ingreso manualmente , se puede modificar el valor unitario y la fecha de aplicacion.

					//Editar Valor Unitario
					$botonenvio = " <input type='button' id='bEditarValorUnitario".$cta."' value='&#9998;' onclick='actualizarvalorunitario( $parametrosfuncionenvio );'>";
					$vtxtabla .= "<tr $clasexceldacompleta >";
					$vtxtabla .= "<td>Vlr x 1 </td>";
					$vtxtabla .= "<td>$valormed </td>";
					$vtxtabla .= "<td>$botonenvio </td>";
					$vtxtabla .= "</tr>";

					//Editar Fecha de Aplicacion o consumo
					$botonenvio = " <input type='button' id='bEditarValorUnitario".$cta."' value='&#9998;' onclick='actualizarfechaaplicacion( $parametrosfuncionenvio );'>";
					$vtxtabla .= "<tr $clasexceldacompleta >";
					$vtxtabla .= "<td>F.Aplicacion</td>";
					$vtxtabla .= "<td><div id='idfechaaplicacion_".$cta."' >$fechaaplicacion</div> </td>";
					$vtxtabla .= "<td>$botonenvio </td>";
					$vtxtabla .= "</tr>";


				}//if ($ingresoManualMipres != 0){//se ingreso manualmente , se puede modificar el valor unitario y la fecha de aplicacion.


				//causas de NO entrega , solo cuando la entrega es Total.
				$selcausasnoentrega = selectcausanoentrega($cta , $causanoentrega , $parametrosfuncionenvio.", this " );//29 mayo.
				$vtxtabla .= "<tr $clasexceldacompleta >";
				$vtxtabla .= "<td colspan='3'  align='center'  >";

				$vtxtabla .= "  $selcausasnoentrega</td>";
				$vtxtabla .= "</tr>";

				//boton de envio
				$botonenvio = "<input type='button' id='bEnvioMipres_".$cta."' value='Enviar".$ventregastr."' onclick='enviarEnvio( $cta , $vID ,\"$vtipotec\" , \"$numprescripcion\" , $vconsecutivo );'>";
				$vtxtabla .= "<tr $clasexceldacompleta >";
				$vtxtabla .= "<td>&nbsp;</td>";
				$vtxtabla .= "<td colspan='2'>$botonenvio </td>";
				$vtxtabla .= "</tr>";


				$vtxtabla .= "</table>";


			}else{//if ($cantart> = $cantidatTotAEntregar>){//es una entrega parcial
			//Entrega Parcial
				$vtxtabla  = "<table align='center' width='100%' >";
$clasexceldaparcial = " class='prescripcioncompleta'  ";//

				$vtxtabla .= "<tr $clasexcelda >";
				$ventregastr  = "Parcial " . "($cantart/ $cantidatTotAEntregar)";
				$botonenvio = " <input type='button' id='bInfoEnvio".$cta."' value='". $ventregastr. "' onclick='verenvio($cta , $vID );'>";
				$vtxtabla .= "<td colspan='3' align='center'>$botonenvio </td>";
				$vtxtabla .= "</tr>";

				$vtxtabla .= "<tr $clasexceldaparcial >";
				//Editar Cantidad
				$botonenvio = " <input type='button' id='bEditarCantidad".$cta."' value='&#9998;' onclick='actualizarcantidad( $parametrosfuncionenvio );'>";////&#9998; caracter de lapiz (editar)
				$vtxtabla .= "<td>Cant. </td>";
				$vtxtabla .= "<td>$dividcantidad</td>";
				$vtxtabla .= "<td>$botonenvio </td>";
				$vtxtabla .= "</tr>";

				//Editar Codigo
				$botonenvio = " <input type='button' id='bEditarCodigo".$cta."' value='&#9998;' onclick='actualizarcodigo( $parametrosfuncionenvio );'>";
				$vtxtabla .= "<tr $clasexceldaparcial >";
				$vtxtabla .= "<td>Cod. </td>";
				$vtxtabla .= "<td>$dividcodtecnologia</td>";
				$vtxtabla .= "<td>$botonenvio </td>";
				$vtxtabla .= "</tr>";

				if ($ingresoManualMipres != 0){//se ingreso manualmente , se puede modificar el valor unitario y la fecha de aplicacion.

					//Editar Valor Unitario
					$botonenvio = " <input type='button' id='bEditarValorUnitario".$cta."' value='&#9998;' onclick='actualizarvalorunitario( $parametrosfuncionenvio );'>";
					$vtxtabla .= "<tr $clasexceldaparcial >";
					$vtxtabla .= "<td>Vlr x 1 </td>";
					$vtxtabla .= "<td>$valormed </td>";
					$vtxtabla .= "<td>$botonenvio </td>";
					$vtxtabla .= "</tr>";

					//Editar Fecha de Aplicacion o consumo
					$botonenvio = " <input type='button' id='bEditarValorUnitario".$cta."' value='&#9998;' onclick='actualizarfechaaplicacion( $parametrosfuncionenvio );'>";
					$vtxtabla .= "<tr $clasexceldaparcial >";
					$vtxtabla .= "<td>F.Aplicacion</td>";
					//$vtxtabla .= "<td>$fechaaplicacion </td>";
					$vtxtabla .= "<td><div id='idfechaaplicacion_".$cta."' >$fechaaplicacion</div> </td>";
					$vtxtabla .= "<td>$botonenvio </td>";
					$vtxtabla .= "</tr>";


				}//if ($ingresoManualMipres != 0){//se ingreso manualmente , se puede modificar el valor unitario y la fecha de aplicacion.


				$botonenvio = "<input type='button' id='bEnvioMipres_".$cta."' value='Enviar".$ventregastr."' onclick='enviarEnvio( $cta , $vID ,\"$vtipotec\" , \"$numprescripcion\" , $vconsecutivo );'>";

				$vtxtabla .= "<tr $clasexcelda >";
				$vtxtabla .= "<td colspan='3' align='center' >$botonenvio </td>";
				$vtxtabla .= "</tr>";



				$vtxtabla .= "</table>";
			}//else if ($cantart >= $cantidatTotAEntregar ){//se puede entregar todo el mipres


			$vtxdataenvio = $vtxtabla;//" $botonenvio ";

		}elseif ( ($cantart == 0) && ( $esIngresoManualParametro==0 ) ){//no se ha entregado nada .
				$vtxdataenvio = " Sin Entrega "; //informacion del envio

		}elseif($codservxmipres == ""){

				$vtxtabla  = "<table align='center' width='100%' >";

				$vtxtabla .= "<tr $clasexcelda >";
				$vtxtabla .= "<td colspan='3' align='center'>Sin codigo mipres</td>";
				$vtxtabla .= "</tr>";
				$vtxtabla .= "<tr $clasexcelda >";
				$vtxtabla .= "<td colspan='3'>(Artcum,maestro articulos)</td>";
				$vtxtabla .= "</tr>";

				$txtusr = "";
				if ( ($cantart > 0 ) || ( $esIngresoManualParametro==1  ) ){
					$vtxtabla .= "<tr $clasexcelda >";
					$vtxtabla .= "<td align='center' colspan='3'>Con Entrega</td>";
					$vtxtabla .= "</tr>";
					$vtxtabla .= "<tr $clasexcelda >";
					$vtxtabla .= "<td align='center' colspan='3'>($cantart / $cantidatTotAEntregar)</td>";
					$vtxtabla .= "</tr>";

				}


				//Editar Codigo
				$botonenvio = " <input type='button' id='bEditarCodigo".$cta."' value='&#9998;' onclick='actualizarcodigo( $parametrosfuncionenvio );'>";
				$vtxtabla .= "<tr $clasexcelda >";
				$vtxtabla .= "<td>Cod. </td>";
				$vtxtabla .= "<td>$dividcodtecnologia</td>";
				$vtxtabla .= "<td>$botonenvio </td>";
				$vtxtabla .= "</tr>";

				$vtxtabla  .= "</table>";

				$vtxdataenvio = " $vtxtabla "; //informacion del envio



		} else {
			//	$html .= "<td> [ { $txtxdir }] </td>";//informacion del envio
				$vtxdataenvio = "Sin envio. "; //informacion del envio
		}


		$varrjsonIDatxt = json_encode($arrenvioID)	;
		$vtxdataenvio .= "<input type='hidden' id='infoenviomipres_".$cta."' value='".$varrjsonatxt."'>";//aqui se guarda el json con la informacion que se enviara al ws.
		$vtxdataenvio .= "<input type='hidden' id='infoenvioIDmipres_".$cta."' value='".$varrjsonIDatxt."'>";//aqui se guarda el json ID con la informacion que se enviara al ws.
		$vtxdataenvio .= "<input type='hidden' id='infoalertamipres_".$cta."' value='".$txtInfoXdialogo."'>";//aqui se guarda el json con la informacion que se mostrara con una alerta.
		$vtxdataenvio .= "<input type='hidden' id='infoalertaIDmipres_".$cta."' value='".$txtInfoIDXdialogo."'>";//aqui se guarda el json ID con la informacion que se mostrara con una alerta.


		$vtxdataenvio .= "<input type='hidden' id='infovlrtecnologiamipres_".$cta."' value='".$valormed."'>";//aqui se guarda el valor de la tecnologia


		$estadoentrega= 1;
		$datareporte = creararrxreporte( $vID , $estadoentrega , $causanoentrega , $cantart , $valormed );
		$cadenvio = json_encode($datareporte)	;

		$txtInfoXreporte = arregloatablahtml($datareporte);


		$vtxdataenvio .= "<input type='hidden' id='infoalertareporte_".$cta."' value='".$txtInfoXreporte."'>";//aqui se guarda el json con la informacion que se mostrara con una alerta.
		$vtxdataenvio .= "<input type='hidden' id='infoenvioreporte_".$cta."' value='".$cadenvio."'>";//aqui se guarda el json que se enviara via webservice



		return $vtxdataenvio;


	}//function datacellenvio($cta, $arrenvio ,$cantart,$cantidatTotAEntregar,$vIDEntrega,$vIDReporteEntrega,$codservxmipres,$valormed , $vID,$estadoentrega,$causanoentrega )


	function datacellreporte( $cta , $datareporte , $vIDReporteEntrega )
	{
		$vtxdatareporte  = "";
		if ($vIDReporteEntrega !=0 ){

			$botonreporte = " <input type='button' id='bInfoReporte".$cta."' value='Reporte Enviado' onclick='verreporte( $cta );'>";
			$vtxdatareporte = "$botonreporte";

		}
		elseif (count($datareporte)>0)
		{ //ya tiene informacion del envio

			$botonreporte = " <input type='button' id='bInfoReporte".$cta."' value='Tiene reporte' onclick='verreporte( $cta );'>";

			$botonreporte .= "<br> <input type='button' id='bHacerReporte_".$cta."' value='Enviar Reporte' onclick='enviarReporte( $cta );'>";//style="display:none"

			$vtxdatareporte  = "$botonreporte";
		} else {
			$vtxdatareporte  = "Sin reporte."; //informacion del reporte de entrega
		}


		return $vtxdatareporte;
	}//datacellreporte


	//Dado un arrelgo lo convierte al texto html correspondiente a una tabla
	function arregloatablahtml ($arreglo){


			$txtInfoXreporte = "<table>";
			$txtInfoXreporte .= "<tr><td>#</td><td>Campo</td><td>Valor</td> </tr> ";// class='fila1' align='center'
			$vlinxinfo = 1;
			foreach($arreglo as $keyreporte => $valuereporte) {
				if (is_string($keyreporte))
				{//no mostrar claves numericas , generadas automaticamente
					if (($vlinxinfo%2)==0){
						$txtInfoXreporte .= "<tr csspar><td> $vlinxinfo</td>";
					}else{
						$txtInfoXreporte .= "<tr cssimpar><td> $vlinxinfo</td>";
					}
					$txtInfoXreporte .= "<td>$keyreporte  : </td>";
					$txtInfoXreporte .= "<td>$valuereporte</td>";
					$txtInfoXreporte .= "</tr>";

					$vlinxinfo++;

				}//if (is_string($keyreporte))

			}//foreach($arreglo as $keyreporte => $valuereporte)

			$txtInfoXreporte .= "</table>";
			return $txtInfoXreporte;

	}//function arregloatablahtml ($arreglo)

	//borra las filas duplicadas de un arreglo 
	// 16 Abril 2020 , Freddy Saenz
	function borrardupliacosarregloconclave ( $arreglo ){
		$arrIDsunicos = array();
		$arrresult = array();
		$ctaids = 0;
		foreach($arreglo as $key => $value)
		{
		 
			 $vclaveidunica  = $value["clave"];
		   
			if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
				$arrIDsunicos["$ctaids"] = $vclaveidunica;//
				$ctaids++;
				$arrresult[] = $value;//evitar duplicados
			}	//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
		
		
		
		
		}//foreach($jsonMipres as $key => $value)
		return $arrresult;
	}//function borrardupliacosarregloconclave (){

// abril 20 , 2020 Freddy Saenz
//para el reporte , despues de eliminado los repetidos , ahora se busca el valor de cada tecnologia
	function buscarvalorarticuloxarreglo ( $arreglo ){

		$arrresult = array();
		$ctaids = 0;
		$acumulador = 0;

		foreach($arreglo as $key => $value)
		{
			global $wbasedatomipres;
			global $wbasedatocliame;

			$codpactivo = $value["CodTec"];//PActivo
			$tarifa = $value["Tarifa"];//parece que el error esta en la tarifa
			$vtipo = $value["Tipo"];
			$cantidad = $value["Cantidad"];
			$valor = 0;
			$arrFila = array();

			switch ( $vtipo ) {
				case "M" :
					$vselectpre  = " Mtavac , Mtavan , Mtafec , Mtatar , Mtaart ";
					$arrFila = array();
					$arrFila2 = array();

					$wherestr = " Patcoa = Mtaart and Patdmp = '".$codpactivo."' ";//no filtrar por tarifa , al final se buscara , pero si no existe
					//le asignara el de mayor valor
					//$wherestr .= "  AND Mtatar = '".$tarifa."' ";
					
					$orderby = " Mtavac DESC ";
					$arrFila2 = selectEnArregloquery( $vselectpre , $wbasedatomipres."_000039 ," . $wbasedatocliame."_000026" , $wherestr , $orderby);
					// selectEnArregloquery
					//$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000039 ,".$wbasedatocliame."_000026", $wherestr); //valor de la tecnologia , se usan 2 tablas
					if ( count($arrFila2) == 0){//13 junio 2020

						//$arrcodmedicamentos = palabraclavenombre($codpactivo, "" , ""); 
						$arrcodmedicamentos = codigosxprincipioactivo($codpactivo);//asociar codigo de la nutricion con el codigo del maestro de articulos
						$vcodartxbusqueda = "";//
						foreach($arrcodmedicamentos as $codpalclave => $nomclave) {// Artcod = Patcoa 
							if ( $codpalclave != ""){
								if ( $vcodartxbusqueda != "" ){
									$vcodartxbusqueda .= " , "; //ir separando los codigos por comas
								}
								$vcodartxbusqueda .= "'" . $codpalclave . "'";//codigos entre comillas
							}
						}
						if ( $vcodartxbusqueda != "" ){
							//var_dump($codpactivo);
							//var_dump($vcodartxbusqueda);
							$vselectpre  = " Mtavac , Mtavan , Mtafec , Mtatar , Mtaart ";
							$wherestr = "  Mtatar = '" . $tarifa . "'  AND Mtaart IN ( $vcodartxbusqueda ) ";
							$arrFila =  select1filaquery( $vselectpre , $wbasedatocliame . "_000026" , $wherestr ); //valor de la tecnologia , se usan 2 tablas
	
						}

						
	
					}else{
						$posenarreglo = 0;
						$posencontrado = -1;
						foreach($arrFila2 as $key1 => $valor1) {
							$posencontrado++;
							if ( $valor1["Mtatar"] == $tarifa ){
								$posenarreglo = $posencontrado;
							}

						}
						//if ( in_array( $tarifa,$arrFila2 )){
							//$posenarreglo = array_search($tarifa,$arrFila2,true);
						//}
						
						$arrFila = $arrFila2[$posenarreglo];//seleccionar el primer elemento o el que corresponda a la tarifa.
						
						if ( $arrFila["Mtatar"] != $tarifa ){
							$arrFila["Mtavac"] = -1 * $arrFila["Mtavac"];
						}
					}
//palabrax
//




					break;
				default:
					$vselectpre  = " Mtavac , Mtavan , Mtafec , Mtatar , Mtaart ";
					
					$wherestr = " Mtaart = '".$codpactivo."' ";
					$wherestr .= "  AND Mtatar = '".$tarifa."' ";
					$arrFila =  select1filaquery($vselectpre, $wbasedatocliame."_000026", $wherestr); //valor de la tecnologia

					break;
			}


			$vsubtotal = 0;
			if ( count($arrFila) > 0){
				//
				$valor = $arrFila['Mtavac'];
				
				$value["Valor"] = $valor;
				$vsubtotal = $valor * $cantidad ;
				if ( $valor < 0){
					$vsubtotal = -$vsubtotal;
					$acumulador +=  ( ( -1 * $valor ) * $cantidad );
				}else{
					$acumulador +=  ( $valor * $cantidad );
				}
				if ( $vtipo == "M"){
					$value["CodTec"] = $arrFila['Mtaart'];//inicialmente viene el principio activo , pero ya esta en la columna Tecnologia
					//se puede dejar el codigo de tecnologia como el codigo del articulo
				}
				

				$value["Subtotal"] = $vsubtotal;
				$value["Acumulado"] = number_format($acumulador, 2, ',', '.');//12 junio 2020 $acumulador;
			}else{
				if ( $vtipo == "M"){
					$value["CodTec"] =  "*?* " . $value["CodTec"];
				}
				
				$value["Subtotal"] = $vsubtotal;
				$value["Acumulado"] = number_format($acumulador, 2, ',', '.');//12 junio 2020 $acumulador;
			}
			$arrresult[] = $value;//devuele el arreglo inicial , con la actualizacion de informacion

//if (!in_array($vclaveidunica,$arrIDsunicos) ){//ver que no este en el arreglo
		
		
		
		
		}//foreach($jsonMipres as $key => $value)
		return $arrresult;
	}//function buscarvalorarticuloxarreglo (){		


	function borrarcolumnasarreglo ( $arreglo , $arrtitulosinactivos ) {
		$arrresult = array();
		$ctaids = 0;
		$acumulador = 0;

		foreach($arreglo as $key => $value){
			$arrelemento = array();

			foreach($value as $key2 => $value2){
				if (!in_array($key2,$arrtitulosinactivos) ){//si no esta en el arreglo , imprimirlo (valor , arreglo)
					$arrelemento[$key2] = $value2;
				}
/* ya no se usa.
				switch ( $key2 ){
					//arrtitulosinactivos
					case "clave" :
						$acumulador = 0;
					break;
					default:
						$arrelemento[$key2] = $value2;
					break;
				}
*/

			}
			$arrresult[] = $arrelemento;
		}
		return $arrresult;
	}//borrarcolumnasarreglo

	
	//Dado un arrelgo lo convierte al texto html correspondiente a una tabla
	function arreglofilasatablahtml ($arreglo){


		$txtInfoXreporte = "";//"<table>";
		//$txtInfoXreporte .= "<tr><td>#</td><td>Campo</td><td>Valor</td> </tr> ";// class='fila1' align='center'
		$vlinxinfo = 1;
		$filatitulo = "";
		$filastabla  = "";



		foreach($arreglo as $keyreporte => $value1){
			if (($vlinxinfo%2)==0){
				$filastabla .= "<tr csspar><td> $vlinxinfo</td>";
			}else{
				$filastabla .= "<tr cssimpar><td> $vlinxinfo</td>";
			}

		
			foreach($value1 as $keyreporte => $valuereporte) {
				if (is_string($keyreporte))
				{//no mostrar claves numericas , generadas automaticamente
					if ($vlinxinfo == 1 ){
						$filatitulo .= "<td>$keyreporte</td>";
					}
					
					$filastabla .= "<td>$valuereporte</td>";
					

					

				}//if (is_string($keyreporte))


			}//foreach($arreglo as $keyreporte => $valuereporte)
			$filastabla .= "</tr>";
			$vlinxinfo++;
		}//foreach($arreglo as $keyreporte => $value1){
		$txtInfoXreporte = "<table></tr><td>#</td>$filatitulo</tr>$filastabla</table>";
		return $txtInfoXreporte;

	}//function arregloatablahtml ($arreglo)

	function archivoatextocsv( $arreglo , $separador ){
		$txtInfoXreporte = "";
		
		$vlinxinfo = 1;
		$filatitulo = "";
		$filastabla  = "";
		if ( $separador == ""){
			$separador = ";";
		}
		


		foreach( $arreglo as $keyreporte => $value1 ) {
	
			

			$lineainfo = " $vlinxinfo   ";
			//
			$cta = 0;
			foreach($value1 as $keyreporte => $valuereporte) {
				if (is_string($keyreporte))
				{//no mostrar claves numericas , generadas automaticamente
					if ($vlinxinfo == 1 ){
						if ( $filatitulo != "" ){
							$filatitulo .= "$separador";
						}
						$filatitulo .= "$keyreporte";
					}
					if ($lineainfo != "" ){
						
					}
					$lineainfo .= "$separador";//siempre va a tener un separador  , por el consecutivo que se lleva.
					if ( $cta == 0 ){
						$lineainfo .= "#$valuereporte";//numero de la prescripcion.
					}else{
						$lineainfo .= "\"$valuereporte\"";
					}
					
					$cta++;

					

				}//if (is_string($keyreporte))


			}//foreach($arreglo as $keyreporte => $valuereporte)
			$filastabla .= $lineainfo ;
			$filastabla .= "\n";
			$vlinxinfo++;
		}//foreach($arreglo as $keyreporte => $value1){
		$txtInfoXreporte = "# $separador $filatitulo \n $filastabla ";
		return $txtInfoXreporte;

	}//function archivoatextocsv( $arreglo ){

	



	//Genera un arreglo con la informacion de ambito de entrega, es esta informacion que sera enviada usando el web service correspondiente
	function  creararrxenvio ($numprescripcion,$vtipotec,$vconsecutivo,$tipoID,$ceduladir,$noentrega,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote)

	{
		global $wbasedatomipres;

		if (  ($ceduladir == "") || ($tipoID == "") ){

			$wherestr = "Prenop = '".$numprescripcion."' ";
			$vselectpre = "Preidp,Pretip" ;
			$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000001", $wherestr); //informacion de la prescripcion

			if ( count($arrFila) > 0){
				//
				$ceduladir = $arrFila['Preidp'];
				$tipoID = $arrFila['Pretip'];
			}

		}



		$arrenvio = array();

		$arrenvio["NoPrescripcion"] = "$numprescripcion";
		$arrenvio["TipoTec"] = "$vtipotec";
		$arrenvio["ConTec"] = $vconsecutivo;
		$arrenvio["TipoIDPaciente"] = "$tipoID";
		$arrenvio["NoIDPaciente"] = "$ceduladir";
		$arrenvio["NoEntrega"] = $noentrega;


		$arrenvio["CodSerTecEntregado"] = "$codservxmipres";
		$arrenvio["CantTotEntregada"] = "$cantart";
		$arrenvio["EntTotal"] = $ventregatotal;//0 = NO , 1 = SI

		$arrenvio["CausaNoEntrega"] = $causanoentrega;//0;
		$arrenvio["FecEntrega"] = "$vfechaentrega";
		$arrenvio["NoLote"] = $lote;//"";

		return $arrenvio;

	}//function creararrxenvio ($numprescripcion,$vtipotec,$vconsecutivo,$tipoID,$ceduladir,$noentrega,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote)

	//Genera un arreglo con la informacion de Entrega, es esta informacion que sera enviada usando el web service correspondiente
	//Difiere de creararrxenvio , porque en esta funcion se usa un ID de identificacion.
	function creaarrxenvioconID($vID,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote , $tipoID , $ceduladir , $numprescripcion )//Se agregan 3 parametros mas , cambio el mipres segun requerimientos del 26 de Julio 2019 ,Freddy Saenz
	{
		
		global $wbasedatomipres;

		if (  ($ceduladir == "") || ($tipoID == "") ){

			$wherestr = "Prenop = '".$numprescripcion."' ";
			$vselectpre = "Preidp,Pretip" ;
			$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000001", $wherestr); //informacion de la prescripcion

			if ( count($arrFila) > 0){
				//
				$ceduladir = $arrFila['Preidp'];
				$tipoID = $arrFila['Pretip'];
			}

		}		
		
		$arrenvio["ID"] = $vID;
		$arrenvio["CodSerTecEntregado"] = "$codservxmipres";
		$arrenvio["CantTotEntregada"] = "$cantart";
		$arrenvio["EntTotal"] = $ventregatotal;//0 = NO , 1 = SI

		$arrenvio["CausaNoEntrega"] = $causanoentrega;//0;
		$arrenvio["FecEntrega"] = "$vfechaentrega";
		$arrenvio["NoLote"] = $lote;//"";

		//29 julio 2019 ,Freddy Saenz , Nuevos campos , solicitud del ministerio.
		//"TipoIDRecibe": "string",
		//"NoIDRecibe": "string"
		
		$arrenvio["TipoIDRecibe"] = "$tipoID";
		$arrenvio["NoIDRecibe"] = "$ceduladir";


  
		return $arrenvio;
	}

	//Genera un arreglo con la informacion de Reporte de Entrega, es esta informacion que sera enviada usando el web service correspondiente
	function creararrxreporte( $vID , $estadoentrega , $causanoentrega , $cantart , $valormed ){
		$datareporte = array();

		$vlrtotal = round ( $cantart * $valormed , 2 );//redondeo a 2 decimales
		$datareporte["ID"] = $vID;
		$datareporte["EstadoEntrega"] = $estadoentrega;
		$datareporte["CausaNoEntrega"] = $causanoentrega;
		$datareporte["ValorEntregado"] = "$vlrtotal";

		/*
		$datareporte = array(
			"ID" => $vID,
			"EstadoEntrega" => 1, //0 no se entrega , 1 si se entrega
			"CausaNoEntrega" => 0,
			"ValorEntregado" => "$vlrtotal"

		);*/
		return $datareporte;

	}//function creararrxreporte($vID,$estadoentrega,$causanoentrega,$cantart,$valormed)


	function creararrxprogramacion($vID , $fechamax, $tipoIDsedeprov , $noIDsedeprov , $codsedeprov , $codtecnologia , $cantidadxentregar )
	{

		global $wemp_pmla;

		if ( $noIDsedeprov == ""){
			$noIDsedeprov = nitwebservice($wemp_pmla);
		}


		$dataprogramacion = array();

		$dataprogramacion["ID"] = $vID;
		$dataprogramacion["FecMaxEnt"] = "$fechamax";
		$dataprogramacion["TipoIDSedeProv"] = "$tipoIDsedeprov";
		$dataprogramacion["NoIDSedeProv"] = "$noIDsedeprov";
		$dataprogramacion["CodSedeProv"] = "$codsedeprov";
		$dataprogramacion["CodSerTecAEntregar"] = "$codtecnologia";
		$dataprogramacion["CantTotAEntregar"] = "$cantidadxentregar";


		return $dataprogramacion;
	/*
  "ID": 2100116,
  "FecMaxEnt": "2019-05-21",
  "TipoIDSedeProv": "NI",
  "NoIDSedeProv": "800149026",
  "CodSedeProv": "PROV000810",
  "CodSerTecAEntregar": "20025916-01",
  "CantTotAEntregar": "10"*/
	}//function creararrxprogramacion($vID , $fechamax, $tipoIDsedeprov , $noIDsedeprov , $codsedeprov , $codtecnologia , $cantidadxentregar )

	
	//5 de Agosto 2019
	// Mipres de Facturacion
	function creararrfacturacion ($numprescripcion,$vtipotec,$vconsecutivo,$tipoID,$ceduladir,$noentrega,$codservxmipres,$cantart,$valormed , $codeps ,$nitEps , $numfactura, $cuotamoderadora , $coopago)
	{
		$datafacturacion = array();
		$valormed = intval ( str_replace(" " , "" , $valormed )   );
		$vlrtotal = round ( $cantart * $valormed , 2 );//redondeo a 2 decimales

		$datafacturacion["NoPrescripcion"] = "$numprescripcion";
		$datafacturacion["TipoTec"] = "$vtipotec";
		$datafacturacion["ConTec"] = $vconsecutivo;
		$datafacturacion["TipoIDPaciente"] = "$tipoID";
		$datafacturacion["NoIDPaciente"] = "$ceduladir";
		
		$datafacturacion["NoEntrega"] = $noentrega;//20 agosto 2019 , faltaba este campo en la documentacion

		$datafacturacion["NoFactura"] = "$numfactura";
		$datafacturacion["NoIDEPS"] = "$nitEps";
		
		$datafacturacion["CodEPS"] = "$codeps";
		
		$datafacturacion["CodSerTecAEntregado"] = "$codservxmipres";
	//cambiado la clave a CantUnMinDis	$datafacturacion["CantUnMinPres"] = "$cantart";
		$datafacturacion["CantUnMinDis"] = "$cantart";
		
		$datafacturacion["ValorUnitFacturado"] = "$valormed";//1
		$datafacturacion["ValorTotFacturado"] = "$vlrtotal";
		
		$datafacturacion["CuotaModer"] = "$cuotamoderadora";
		$datafacturacion["Copago"] = "$coopago";



		return $datafacturacion;
		
	}//function creararrfacturacion 


	//Despliega el resultado de la busqueda ($arrinfo) en la ventana
	//Se realizan busquedas adicionales para completar la informacion que se necesita.
	function pintarPrescripcionesEnTabla ( $arrinfo  , $filtroestadomipres  , $filtroFacturacion ){ // 22 junio , filtrar por SinDireccionamiento

	
	
		global $wbasedato;
		global $wbasedatomipres ;
		global $wbasedatocliame;
		global $wemp_pmla;
		global $ingresoManualMipres; //31 mayo 2019
		global $wmodoambulatorio;

		//global $vusarunixxmipresfacturacion;//16 septiembre 2019 , existen 2 maneras de consultar la facturacion , 1 Usando Unix Servinte 2 Matrix (tablas clisur_000018 o idc_000018)
		global $conex;
		
		$vusarunixxmipresfacturacion  = consultarAliasPorAplicacion( $conex , $wemp_pmla , 'usarunixxmipresfacturacion' );//16 septiembre 2019 

	
		
		$nit = nitwebservice($wemp_pmla);
		$codsedeprov = codigosedeproveedor($wemp_pmla)	;

		if ( is_string($arrinfo) ){//capturar errores
			return "<table class='fila2' align='center'><tr> <td> $arrinfo</td> </tr> </table>";
		}

		if (count($arrinfo)==0){//no se encontraron registros
			return "<table class='fila2'  align='center'><tr> <td>-</td> </tr> </table>";
		}
		
		$html = " ";
		$html .= "<table align='center' width='100%' id='tablePrescripciones' >";// 5 julio 2019 , find filtro de busquedas

		//ID":1123319,"IDDireccionamiento":1103301,"NoPrescripcion":"20190327143011099046","TipoTec":"M","ConTec":2,"TipoIDPaciente":"CC","NoIDPaciente":"3306145"
		$cta = 0;
		$filas = 1;//22 junio 2019 , empezar  en 1 //0;

		$arrxmostrar = array();
		$arrxmostrar[] = "ID";
		$arrxmostrar[] = "NoPrescripcion";
		$arrxmostrar[] = "TipoTec";
		$arrxmostrar[] = "ConTec";// se mostrara con la prescripcion
		$arrxmostrar[] = "CantTotAEntregar";
		$arrxmostrar[] = "Prefep";
		$arrxmostrar[] = "Prehop";
		$arrxmostrar[] = "Pretip";
		$arrxmostrar[] = "NoIDPaciente";
		$arrxmostrar[] = "Prepnp";
		$arrxmostrar[] = "Presnp";
		$arrxmostrar[] = "Prepap";
		$arrxmostrar[] = "Presap";
		$arrxmostrar[] = "Preeps";
		$arrxmostrar[] = "Epsdes";
		$arrxmostrar[] = "CodSerTecAEntregar";
		$arrxmostrar[] = "TipoIDPaciente";

		$arrlstEps = array();
		$titulotabla = "";

		$arrnomostrar = array();//campos que no se van a mostrarCodigos
		for ($i=1;$i<60;$i++){
			$arrnomostrar['titulo'] = "$i";
		}

		$ctafilastabla = 0;


		$vID = 0;

		$vIDEntrega = 0;
		$vIDReporteEntrega = 0;
		$vIDDireccionamiento = 0;
		$vIDProgramacion = 0;

//var_dump($arrinfo);
		$classFila = "";
		foreach($arrinfo as $key => $value)// recorrer los registros
		{
			$html1linea = "";
//var_dump($value);

			if ( ( ( $filas + 1 ) % 2) == 0) {
				$classFila =  "<tr class='fila2 find' align='center'>";//5 julio 2019 find
				//$html1linea .= "<tr class='fila2' align='center'>";
			} else {
				$classFila = "<tr class='fila1 find' align='center'>";//5 julio 2019 find
				//$html1linea .= "<tr class='fila1' align='center'>";
			}
			// 22 junio 2019 , se pasa al final ,$filas++;

			$causanoentrega = 0;//29 mayo

			$query = "";
			$vID = 0;
			$vIDEntrega = 0;
			$vIDReporteEntrega = 0;
			$vIDDireccionamiento = 0;
			$vIDProgramacion = 0;
			$vIDFacturacion = 0;

			$cantidatTotAEntregar = 0;
			$txtxdir = "";
			$fechadir = "";
			$ceduladir = "";
			$numprescripcion = "";
			$ingtar = "";
			$ingfecha = "";
			$cantart = 0;
			$cantidadOriginal = 0;
			$valormed = 0;
			$codarticulo = "";
			$vtipotec = "";
			$codcum = "";

			$noentrega = 1;
			$tipoID = "";
			$vdescprincipioactivo = "";
			$queryvalores = "";
			$cadxdebug = "";

			$codArtInterno = "";
			$vdescripcionInterna  = "";//nombre del medicamento , procedimiento , nutricion en el matrix .
			$codservxmipres = "";//codigo que se usara en el mipres.

			$tablaActualizar ="";
			$fechaMipres = "";
			$nomPaciente = "";
			$vtxtcoltabla = "";
			$medico = "";
			
			$divEps = "";
			$eps = "";
			$horaMipres = "";
			$fechaEntrega = "";
			$fechaReporteEntrega = "";
			$fechaentregamipres = "";

			$valormodificado = 0;

			$vtxthtmldir = "";

			$vEstadoPrescripcion = -1;
			$vclaveIdsuministros = 0;

			$codigoOriginal = "";
			$codservxmipres = "";

			$fechamaximaentrega = "";

			$fechaentregaxprescripcion = "";
			$cantidaddelaprescripcion = 0;//cantidad prescrita
			$vestadodireccionamiento = 1;//en proceso  11 de Julio 2019 , que muestre si el direccionamiento esta anulado(Freddy Saenz)
			$vestadoprogramacion = 1;
			$vestadoentrega = 1;
			$vestadoreporteentrega = 1;

			if (count($value) > 0)
			{



				foreach($value as $key2 => $value2)
				{

					$vtxthtmldir = arregloatablahtml($value);
					if ($txtxdir != "") {
						$txtxdir .= " , ";
					}
					$txtxdir .= str_replace("'", "\"", " \"$key2\" : \"$value2\" ");


					switch ($key2) {

						case "query": //error de busqueda
							if ($value2 !== "0" ){
								$tablaerror = "<table align='center'><tr class='fila2' align='center'> <td> Falta Informacion: $key2 $value2 x </td> </tr> </table>" ;
								return $tablaerror;
							}
							
							break;

						case "ID": //ID unico
							if ( intval($value2) !=  0) {
								$vID = $value2 ;//intval($value2);
							}

							break;
						case "IDEntrega":
							$vIDEntrega = intval($value2);
							break;

						case "IDDireccionamiento":
							$vIDDireccionamiento = intval($value2);
							break;

						case "IDProgramacion":
							$vIDProgramacion = intval($value2);
							break;
						case "IDReporteEntrega":
							$vIDReporteEntrega = intval( $value2);
							break;

						case "IDjson": //ID unico
							if ( ($value2 != "") &&( $vID == 0 ) ){
								$vID = intval($value2);
							}
							break;
						case "IDEntregajson": //ID unico
							if ( ($value2 != "") &&( $vIDEntrega == 0 ) ) {
								$vIDEntrega = intval($value2);
							}
							break;
						case "IDReporteEntregajson": //ID unico
							if ( ($value2 != "") &&( $vIDReporteEntrega == 0 ) ) {
								$vIDReporteEntrega = intval($value2);
							}
							break;



						case "NoPrescripcion":
							$numprescripcion = $value2;

							break; //solo se necesitan la prescripcion y el consecutivo , el tipo de prescripcion lo da la tablas

						case "ConTec": //consecutivo

							$vconsecutivo = $value2;

							break; //solo se necesitan la prescripcion y el consecutivo , el tipo de prescripcion lo da la tablas
							//medicamentos , procedimientos .

							//case "TipoIDPaciente"://tipo de identificacion CC ,TI ,
						case "FecDireccionamiento":
							$fechadir = $value2;
							break;
						case "TipoIDPaciente":
							$tipoID = $value2;
							break;

						case "Prefep":
							$fechaMipres = $value2;
							break;

							//Buscar la fecha de entrega
						case "FecEntrega":
						case "FechaEntregaMiPres":
						//Sumfen
							$fechaentregamipres = $value2;
							break;
						case "Prehop":
							$horaMipres = $value2;
							break;

						case "docPac"://tabla de mipres_000001
						case "NoIDPaciente": // numero de identificacion


							$ceduladir = $value2;
							$wherestr = "Prenop = '".$numprescripcion."' ";
							$vselectpre = "Prefep,Prehop,Pretip,Preidp,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps,Pretip" ;//,Epsdes";
							$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000001", $wherestr); //informacion de la prescripcion

							if ( count($arrFila) > 0){
								//
								$ceduladir = $arrFila['Preidp'];
								$fechaMipres = $arrFila['Prefep'];
								$horaMipres = $arrFila['Prehop'];
								$nomPaciente = $arrFila['Prepnp'] . "<BR>" . $arrFila['Presnp'] . "<BR>" . $arrFila['Prepap'] . "<BR>" . $arrFila['Presap'];
								$medico	=  $arrFila['Prepnm'] . "<BR>" . $arrFila['Presnm'] . "<BR>" . $arrFila['Prepam'] . "<BR>" . $arrFila['Presam'];
								
								
								$eps =  $arrFila['Preeps'];
								$divEps = "<div id='diveps-".$cta."'>$eps</div>" ;//5 agosto 2019
								
								$wherestr = "Epscod = '".$eps."' ";
								if ($tipoID == ""){
									$tipoID = $arrFila['Pretip'];
								}

								$desceps =  select1campoquery("Epsdes", "mipres_000033", $wherestr);//27 junio 2019 , no se necesita prefijo mipres
								//if (in_array($eps,$arrlstEps)
							$arrlstEps[ $arrFila['Preeps'] ]= $desceps;
								//$eps .= "<br>$desceps";9 octubre 2019 error lo estaba pegando en el json
								$divEps .= "<br>$desceps"; ;//5 agosto 2019
								
							}else{
								$fechaMipres =  select1campoquery("Prefep", $wbasedatomipres."_000001", $wherestr); //fecha de prescripcion
							}

//Buscar historia clinica y numero de ingreso
/*
							$histxmedi = ""; //'33094';
							$ingxhist = ""; // '3';
							$ingtar = "";
							if ($fechaMipres != "") {
								$arrhistoriascc = consultarHistoriasXCedula($ceduladir, $fechaMipres);
								if (count($arrhistoriascc) > 0) {
									$histxmedi = $arrhistoriascc[0]['Inghis']; //'33094';
									$ingxhist = $arrhistoriascc[0]['Ingnin']; // '3';
									$ingtar = $arrhistoriascc[0]['Ingtar'];

								}

							}
*/
							break;
						case "TipoTec": //"M" ,"P" , medicamentos , procedimientos , etc
							$vtipotec = $value2;
							$tablaActualizar = tablaxtipotec($vtipotec);


							break;
						case "CodTecEntregado"://viene de un reporte de entrega
						case "CodSerTecEntregado"://viene de una entrega
						case "CodSerTecAEntregar":
						//Aqui esta el nucleo del mipres.
							$codSerTecAEntregar = $value2;
	/*
							$arrinfomipres = infocantidadvlrycodigomipres($numprescripcion , $vconsecutivo , $fechaMipres , $codSerTecAEntregar , $histxmedi, $ingxhist, $ingtar , $vtipotec , "" , $noentrega);

							if (count($arrinfomipres)>0){


								$codcum = $arrinfomipres[0]['Cum'];
								$codarticulo = $arrinfomipres[0]['CodArticulo'];//codigo que trae el mipres
								$codArtInterno = $arrinfomipres[0]['CodInterno'];//codigo en la bd matrix
								$cantart = $arrinfomipres[0]['Cantidad'];
								$valormed = $arrinfomipres[0]['Valor'];
								$vdescripcionInterna = $arrinfomipres[0]['DescInterna'];
								$codservxmipres = $arrinfomipres[0]['CodDeEntrega'];//codigo que se usara en el web service de envio y de programacion
								$cantidadOriginal = $arrinfomipres[0]['CantidadOriginal'];
								$codigoOriginal = $arrinfomipres[0]['CodigoOriginal'];

								$causanoentrega = $arrinfomipres[0]['CausaNoEntrega'];//29 mayo


								$valormodificado = $arrinfomipres[0]['ValorModificado'];//31 mayo
							}
*/
							break;

						case "SumcanPrescripcion"://9 de julio 2019
							$cantidaddelaprescripcion = $value2;
							break;
						case "Sumcan"://mostrar en la tablePrescripciones
							$cantidaddelaprescripcion = $value2;
							break;


//Sumcan
						case "CantTotEntregada"://viene de una entrega o de un reporte de entrega.
						case "CantTotAEntregar":
							$cantidatTotAEntregar = intval($value2) ;
							break;
						case "NoEntrega":
							$noentrega = intval($value2);
							break;

						case "FecEntrega":
							$fechaEntrega = $value2;
							break;

						case "FecRepEntrega":
							$fechaReporteEntrega = $value2;
							break;

						case "Preepr"://1 = modificado , 2 = Anulado  4 = Activo
							$vEstadoPrescripcion= $value2;
							break;

						case "claveidmipressuministros"://usar como clave principal
							$vclaveIdsuministros = $value2;
							break;
							
						case "IDFacturacion":
							$vIDFacturacion  = $value2;// 0;

					}






				} //foreach($value as $key2 => $value2)




			}//if (count($value) > 0)




			//si tiene registros
//Buscar los IDs en la tabla del mipres 37 , 21 mayo 2019
			$wherestr = "Sumnop = '".$numprescripcion."'  AND Sumtip = '".$vtipotec."' AND Sumcon = '".$vconsecutivo."' " ;
			if ($noentrega != 0 ){//3 julio 2019 , el numero de entrega tambien hace parte de la clave
				$wherestr .= " AND Sumnen = '".$noentrega."' ";
			}

			//$fechamaximaentrega = "";
//---------------------------------------------
// Se consulta la informacion grabada en la base de datos

			$vselectpreIDs = "SumID,SumIDd,SumIDp,SumIDe,SumIDr,Sumnen,Sumima,Sumcae,Sumcoe,Sumvlr, Sumfen , id , Sumcat, Sumfmx , Sumfem , Sumedi ,Sumepr,Sumeen,Sumere " ;//,Epsdes";
			$vselectpreIDs .= ",Sumnfa , Sumvco , Sumvcm , SumIDf , Sumjen , Sumjre ";//numero de factura
			$arrFilaIDs =  select1filaquery($vselectpreIDs, $wbasedatomipres."_000037", $wherestr); //informacion de los IDs de la prescripcion
$vjsonentregarealizadatxt  = "";
$vjsonreporteentregarealizadatxt = "";
			$numerodeentrega = 1;
			if ($noentrega != 0){
				$numerodeentrega = $noentrega;
			}

		
		
			$esIngresoManual = 0;//31 mayo 2019
			$cantidadManual = 0;
			$codigoManual = "" ;
			$vlrManual = 0;
			$semodificocantidad = 0;//17 julio 2019 
			
			$vnumerodefactura = 0;//22 agosto 2019
			$vlrcoopagofactura = 0;
			$vlrcuotamoderadorafactura = 0;
			
			if ( count($arrFilaIDs) >0 ){//si hay registros

				$vID = $arrFilaIDs["SumID"];
				$vIDProgramacion = $arrFilaIDs["SumIDp"];
				$vIDDireccionamiento = $arrFilaIDs["SumIDd"];
				$vIDEntrega = $arrFilaIDs["SumIDe"];
				$vIDReporteEntrega = $arrFilaIDs["SumIDr"];
				$numerodeentrega =   $arrFilaIDs["Sumnen"];
				$esIngresoManual =   $arrFilaIDs["Sumima"];//Ingreso manual
				$cantidadManual = $arrFilaIDs["Sumcae"];//cantidad editada
				$codigoManual = $arrFilaIDs["Sumcoe"];//codigo editado
				$vlrManual = $arrFilaIDs["Sumvlr"];//valor editado
				$fechaentregamipres =  $arrFilaIDs["Sumfen"];
				if (  $arrFilaIDs["Sumfen"] != ""){

				}
				if ( $vclaveIdsuministros == 0 ){
					 $vclaveIdsuministros = $arrFilaIDs["id"];
				}
				//cantidatTotAEntregar
				//3 julio 2019 , si tiene cantidad total configurada , ej. x entregas
				if (  $arrFilaIDs["Sumcat"] != 0 ){//puede ser cero si es ingreso manual


					$cantidatTotAEntregar = $arrFilaIDs["Sumcat"];
				}
				$fechamaximaentrega  = $arrFilaIDs["Sumfmx"];
				$fechaentregaxprescripcion  = $arrFilaIDs["Sumfem"];
				$vestadodireccionamiento = $arrFilaIDs["Sumedi"];
				$vestadoprogramacion = $arrFilaIDs["Sumepr"];
				$vestadoentrega = $arrFilaIDs["Sumeen"];
				$vestadoreporteentrega = $arrFilaIDs["Sumere"];

				// 17 Julio 2019 , Freddy Saenz , saber que la cantida se modifico,
				//porque el valor cero no estaba permitido.
				$semodificocantidad = $arrFilaIDs["Sumcam"];
				
				//22 agosto 2019 Freddy Saenz , consultar el numero de factura asociada.
$vnumerodefactura           = $arrFilaIDs["Sumnfa"];
$vlrcoopagofactura          = $arrFilaIDs["Sumvco"];
$vlrcuotamoderadorafactura  = $arrFilaIDs["Sumvcm"];
$vIDFacturacion             = $arrFilaIDs["SumIDf"];
$vjsonentregarealizadatxt   = $arrFilaIDs["Sumjen"];
$vjsonreporteentregarealizadatxt   = $arrFilaIDs["Sumjre"];
			}else{//crear un registro con la informacion de la prescripcion



	//	$numfilas = insertupdatemipressuministro($arrtemp)

				$wherestr = "Prenop = '".$numprescripcion."' ";
				$vselectpre = "Prenop,Prefep,Prehop,Pretip,Preidp,Prepnp,Presnp,Prepap,Presap,Prenop,Prehis,Preing,Pretip,Preidp,Prepam,Presam,Preeps" ;//,Epsdes";
				$arrFila =  select1filaquery( $vselectpre, $wbasedatomipres."_000001", $wherestr ); //informacion de la prescripcion



				$arraytabla = array();

				if ( count($arrFila) > 0){

					$arraytabla["Sumnop"] = $arrFila['Prenop'];
					$arraytabla["Sumhis"] = $arrFila['Prehis'];
					$arraytabla["Suming"] = $arrFila['Preing'];

					$arraytabla["Sumtid"] = $arrFila['Pretip'];//OJO PRETIP no corresponde a Sumtip
					$arraytabla["Sumtip"] = $vtipotec;
					$arraytabla["Sumcon"] = $vconsecutivo;

					$arraytabla["Sumnid"] = $arrFila['Preidp'];//numero de identificacion del paciente
					$arraytabla["Sumnen"] = $numerodeentrega;//1;// primera entrega
					$arraytabla["Sumest"] = "on";
					$arraytabla["Sumfep"] = $arrFila['Prefep'];
					$arraytabla["Sumhop"] = $arrFila['Prehop'];
					$arraytabla["Sumcan"] = $cantidatTotAEntregar;

					$arraytabla["SumID"] = $vID;
					$arraytabla["SumIDd"] = $vIDDireccionamiento;
					$arraytabla["SumIDp"] = $vIDProgramacion;
					$arraytabla["SumIDe"] = $vIDEntrega;

					$arraytabla["Sumcne"] = $causanoentrega;


/*

					$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $arraytabla )	;
					$numfilas = insertupdatemipressuministro( $arrtemp );//$vtxt;
				*/
				}

			//	$arrresult = selectEnArregloquery( $qselect , $qfrom , $qwhere , $orderby );



			}////if (count($arrFilaIDs)>0){//si hay registros

//5 julio 2019 , no tiene direccionamiento todavia , usar la cantidad de la prescripcion .
			if (($cantidaddelaprescripcion != 0) && ( $cantidatTotAEntregar == 0) ) {
				$cantidatTotAEntregar = $cantidaddelaprescripcion;
			}
				// 22 de junio 2019 , sin direccionamiento implica que no tienen ID todavia , pero el filtro no se puede realizar en el buscarPrescripcionesSuministros
			if ( (  $filtroestadomipres == "SinDireccionamiento" ) && ( $vID != 0 ) ){
				continue;//no seguir , no cumple la condiccion
			}

			$vdivdata = $filas . "-" . $vID . "-" . $numprescripcion . "_" . $vconsecutivo . "_" . $vtipotec ;//id para poder modificar la informacion
			//de la linea




			$html1linea .= "<td>$filas<input type='checkbox' name='seleccionprescripciones' id = 'seleccionprescripcion_".$cta."' value=".$vID."-".$numprescripcion."_".$vconsecutivo."_".$vtipotec.">";
			$html1linea .= "</td>";
//	&#128465;


//Para consultar los ingresos de los pacientes usando ademas de la fecha del mipres , la fecha maxima de entrega ,
//se pasa el codigo a este sitio  , se mueve del switch
//4 de Julio 2019 Freddy Saenz

			$histxmedi = ""; //'33094';
			$ingxhist = ""; // '3';
			$ingtar = "";
			if ($fechaMipres != "") {

				if ( ($wmodoambulatorio != 0 ) || ($noentrega > 1 ) ) {


					if ($noentrega > 1 ){//3 JULIO 2019 , incluir la entrega en la clave principal
						$noentrega_1 = $noentrega - 1;
						$wherestr = " Sumnop = '".$numprescripcion."' and Sumtip = '".$vtipotec."' AND Sumcon = '".$vconsecutivo."'";

						$wherestr .= " AND Sumnen = '".$noentrega_1 ."' ";
						$fechamaximaentregaanterior = select1campoquery("Sumfmx", $wbasedatomipres."_000037", $wherestr);
						$arrhistoriascc = consultarHistoriasXCedula($ceduladir, $fechamaximaentregaanterior , $fechamaximaentrega);
					}else{
						$arrhistoriascc = consultarHistoriasXCedula($ceduladir, $fechaMipres , $fechamaximaentrega);
					}




				}else{
					$arrhistoriascc = consultarHistoriasXCedula($ceduladir, $fechaMipres , "");//no se tiene en cuenta la fecha maxima de entrega
				}


				if (count($arrhistoriascc) > 0) {
					$histxmedi = $arrhistoriascc[0]['Inghis']; //'33094';
					$ingxhist = $arrhistoriascc[0]['Ingnin']; // '3';
					$ingtar = $arrhistoriascc[0]['Ingtar'];
					$ingfecha = $arrhistoriascc[0]['Ingfei'];//fecha de ingreso 4 de Julio 2019
				}

			}

			$arrinfomipres = infocantidadvlrycodigomipres($numprescripcion , $vconsecutivo , $fechaMipres , $codSerTecAEntregar , $histxmedi, $ingxhist, $ingtar , $vtipotec , "" , $noentrega , $fechamaximaentrega);

			if (count($arrinfomipres)>0){


				$codcum = $arrinfomipres[0]['Cum'];
				$codarticulo = $arrinfomipres[0]['CodArticulo'];//codigo que trae el mipres
				$codArtInterno = $arrinfomipres[0]['CodInterno'];//codigo en la bd matrix
				$cantart = $arrinfomipres[0]['Cantidad'];
				$valormed = $arrinfomipres[0]['Valor'];
				$vdescripcionInterna = $arrinfomipres[0]['DescInterna'];
				$codservxmipres = $arrinfomipres[0]['CodDeEntrega'];//codigo que se usara en el web service de envio y de programacion
				$cantidadOriginal = $arrinfomipres[0]['CantidadOriginal'];
				$codigoOriginal = $arrinfomipres[0]['CodigoOriginal'];

				$causanoentrega = $arrinfomipres[0]['CausaNoEntrega'];//29 mayo


				$valormodificado = $arrinfomipres[0]['ValorModificado'];//31 mayo
			}




//-------------------------------------CELDA DE IDS -------------------------


//Aqui empieza la celda con los IDs.
			if ($vIDReporteEntrega != 0){//ya fue entregado
				$html1linea .= "<td width='70px' class = 'encabezadotabla' align='center' bgcolor='#0000FF'>";//1.ID|2.Dir|3.Prog|4.Ent|5.Rep";//id
//5 julio 2019 find , filtro por campo
			}else{
				$html1linea .= "<td width='70px'>";//1.ID|2.Dir|3.Prog|4.Ent|5.Rep";//id

			}//if ($vIDReporteEntrega != 0){//ya fue entregado

			$vDivID = "<div id = 'ID-".$cta."' >$vID</div>";
			$vDividregistrotabla = "<div id = 'idregistrotabla-".$cta."' > $vclaveIdsuministros  </div>";
	//		$vDividregistrotabla = "<input type='hidden' 'idregistrotabla-".$cta."' value='".$vclaveIdsuministros."'> ";

			$vdivIDdireccionamiento = "<div id = 'IDDireccionamiento-".$cta."' >$vIDDireccionamiento</div>";
			$botondir = "Dir.";
			if ($vIDDireccionamiento != 0 ){
				    $parametrosDir = " $cta , \"$vID\" , \"$numprescripcion\" , \"$vtipotec\" , \"$vconsecutivo\" ";//27 mayo 2019
					$botondir = " <input type='button' id='btndir".$vID."' value='Dir.' onclick='verdir( $parametrosDir );'>";
				//	$vdivIDdireccionamiento .= " $botondir ";
			}


			$vdivIDprogramacion = "<div id = 'IDProgramacion-".$cta."' >".$vIDProgramacion."</div>";
			$botonprog = "Prog.";
			if ($vIDProgramacion != 0 ){
					$parametrosProg = " $cta , \"$vIDProgramacion\" ";
					$botonprog = " <input type='button' id='btnprog".$vID."' value='Prog.' onclick='verprog( $parametrosProg );'>";

			}
			$vhtmlIDprogramacion =  "<input type='button' id='bAnularProgramacion".$cta."' value='&#10060;' onclick='anularprogramacion( $cta , \"$vID\" , \"$vIDEntrega\" , \"$vIDReporteEntrega\" , \"$numprescripcion\" , \"$vconsecutivo\" , \"$vtipotec\"  , \"$vIDProgramacion\" );'>";

			$vdivIDentrega =  "<div id = \"IDEntrega-".$cta."\" >$vIDEntrega</div>";
			$vhtmlIDentrega =  "<input type='button' id='bAnularEntrega".$cta."' value='&#10060;' onclick='anularentrega( $cta , \"$vID\" , \"$vIDEntrega\" , \"$vIDReporteEntrega\" , \"$numprescripcion\" , \"$vconsecutivo\" , \"$vtipotec\" );'>";
//U+274C
//CROSS MARK  &#10060;
			$vdivIDreporteentrega =  "<div id = \"IDReporteEntrega-".$cta."\" >$vIDReporteEntrega</div>";			
			$vhtmlIDreporte =  "<input type='button' id='bAnularReporte".$cta."' value='&#10060;' onclick='anularreporte( $cta ,\"$vID\" , \"$vIDEntrega\" , \"$vIDReporteEntrega\" , \"$numprescripcion\" , \"$vconsecutivo\" , \"$vtipotec\" );'>";
	
//5 agosto 2019 , se agrega el ID de Facturacion.
//$vIDFacturacion = 0;	
			$vdivIDFacturacion =  "<div id = \"IDFacturacion-".$cta."\" >$vIDFacturacion</div>";			
			$vhtmlIDFacturacion =  "<input type='button' id='bAnularFacturacion".$cta."' value='&#10060;' onclick='anularfacturacion( $cta , \"$numprescripcion\" , \"$vconsecutivo\" , \"$vtipotec\" );'>";
			
			//18 DE JUNIO 2019
			if ( intval($vEstadoPrescripcion) == 2 ){//si esta anulado
/*	Preguntar si se desactivan estos botones cuando esta anulada la prescripcion.
				$botondir = "";
				$botonprog = "";
				$vhtmlIDprogramacion = "";
				$vhtmlIDentrega = "";
				$vhtmlIDreporte = "";
				*/
			}



			$clasexcelda = "";
			if ( ( $filas % 2 )== 0){//par
				$clasexcelda = "2";
			}else{
				$clasexcelda = "1";
			}
//11 julio 2019
			if ( $codservxmipres != ""){//si tiene codigo de entrega basado en el consumo
				$parametrosdlg = " \"$cta\" ,  \"$vID\"  , \"$numprescripcion\" ,  \"$vconsecutivo\" , \"$vtipotec\" , \"$cantidatTotAEntregar\"  , \"$codservxmipres\" , \"NI\" , \"$nit\" , \"$codsedeprov\" , \"$vIDEntrega\" ";
				$parametrosdlg .= " , \"$numerodeentrega\" ";

			}else{//usar el codigo de la tecnologia cuando no hay consumo.
				$parametrosdlg = " \"$cta\" ,  \"$vID\"  , \"$numprescripcion\" ,  \"$vconsecutivo\" , \"$vtipotec\" , \"$cantidatTotAEntregar\"  , \"$codSerTecAEntregar\" , \"NI\" , \"$nit\" , \"$codsedeprov\" , \"$vIDEntrega\" ";
				$parametrosdlg .= " , \"$numerodeentrega\" ";

			}
//			$parametrosdlg = " \"$cta\" ,  \"$vID\"  , \"$numprescripcion\" ,  \"$vconsecutivo\" , \"$vtipotec\" , \"$cantidatTotAEntregar\"  , \"$codservxmipres\" , \"NI\" , \"$nit\" , \"$codsedeprov\" , \"$vIDEntrega\" ";
//			$parametrosdlg .= " , \"$numerodeentrega\" ";


			//18 DE JUNIO 2019
			if ( (intval($vEstadoPrescripcion) == 2 ) || ($vestadodireccionamiento == 0) ){//si esta anulado
			//o el direccionamiento esta anulado
				$vhtmlProgramacion = "ANULADO";
				if ($vestadodireccionamiento == 0){
					$vhtmlProgramacion .= " DIR.";
				}
			}else{
				$vhtmlProgramacion =  "<input type='button' id='bVerProgramacion".$cta."' value='Ver Programacion' onclick='dlgDeProgramacion( ".$parametrosdlg." );'>";

			}

//Tabla con los IDs en la segunda celda de la tabla de prescripciones
			$html1linea .=  "<table align='center' width='100%'>";

			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>ID:  </td><td  colspan='2'> $vDivID </td></tr>";
			$txtanuladodir = "";
			if ($vestadodireccionamiento == 0){//esta anulado
				$txtanuladodir = "<br>ANULADO DIR.";
			}
			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>$botondir $txtanuladodir</td><td colspan='2'>$vdivIDdireccionamiento </td> </tr>";


			$txtanuladoprog = "";
			if ($vestadoprogramacion == 0){//esta anulado
				$txtanuladoprog = "<br>ANULADA PROG.";
			}
			$txtanuladoentrega = "";
			if ($vestadoentrega == 0){//esta anulado
				$txtanuladoentrega = "<br>ANULADA ENTREGA";
			}

			$txtanuladoreporteentrega = "";
			if ($vestadoreporteentrega == 0){//esta anulado
				$txtanuladoreporteentrega = "<br>ANULADO REPORTE";
			}

			//5 agosto 2019 , texto de anulacion de facturacion
			$txtanuladofacturacion = "";
			$vestadofacturacion = 1;//falta crear campos en la estructura
			if ($vestadofacturacion == 0){//esta anulado
				$txtanuladofacturacion = "<br>ANULADA FACTURACION";
			}

			//12 julio 2019 Freddy Saenz, usar id para poder actualizar con ajax
		$dividanuladoprog =  "<div id = \"idanuladoprog_".$cta."\" >$txtanuladoprog</div>";
		$dividanuladoentrega =  "<div id = \"idanuladoentrega_".$cta."\" >$txtanuladoentrega</div>";
		$dividanuladoreporteentrega =  "<div id = \"idanuladoreporteentrega_".$cta."\" >$txtanuladoreporteentrega</div>";
		//5 agosto 2019 , id de anulacion para  facturacion
		$dividanuladofacturacion =  "<div id = \"idanuladofacturacion_".$cta."\" >$txtanuladofacturacion</div>";


			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>$botonprog $dividanuladoprog</td><td>$vdivIDprogramacion</td><td> $vhtmlIDprogramacion  </td> </tr>";//12 julio 2019

			$html1linea .=  "<tr class='fila".$clasexcelda."'><td  colspan='3' >$vhtmlProgramacion</td> </tr>";

			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>Ent.:$dividanuladoentrega</td><td>$vdivIDentrega</td><td> $vhtmlIDentrega</td></tr>";//12 julio 2019
			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>Rep.:$dividanuladoreporteentrega</td><td>$vdivIDreporteentrega</td><td> $vhtmlIDreporte</td> </tr>";//12 julio 2019
//5 agosto 2019 , agregada la linea para el ID de facturacion
			
			if  (intval($vEstadoPrescripcion) == 2 ){//esta anulada la prescripcion
				$botonfacturacion = "Fact.";
			}else{
				$botonfacturacion = " <input type='button' id='btnfacturacion".$vID."' value='Fact.' onclick='dlgDePFacturacion( ".$parametrosdlg." );'>";
				
			}
//			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>Fact.:$dividanuladofacturacion</td><td>$vdivIDFacturacion</td><td> $vhtmlIDFacturacion</td> </tr>";//12 julio 2019

			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>$botonfacturacion $dividanuladofacturacion</td><td>$vdivIDFacturacion</td><td> $vhtmlIDFacturacion</td> </tr>";//12 julio 2019

			$botonimprimirfact = " <input type='button' id='btnimpfacturacion".$vID."' value='Imprimir Facturacion' onclick='imprimirfacturacion( ".$cta." );'>";

			$html1linea .=  "<tr class='fila".$clasexcelda."'><td  colspan='3'>$botonimprimirfact</td> </tr>"; 


			
		
			$html1linea .=  "</table>";


			$html1linea .= "<input type='hidden' id='infoalertadir_".$cta."' value='".$vtxthtmldir."'>";

			$html1linea .="</td>";
//-------------------------------------------

			//$html1linea .= "<td>$numprescripcion<br>[ $vconsecutivo ]</td>";
			//checkbox de seleccion



			$divEntrega = "<div id='idnumerodeentrega_".$cta."' >$numerodeentrega</div>";
			//Numero de la prescripcion

//------------------------------------- CELDA DE PRESCRIPCION -------------------------

			// $idprescricionfila = "<div id='idprescripcionfila_".$cta."' >$numprescripcion</div> ";//28 junio 2019
			$idprescricionfila = crearidcampovalor ( "idprescripcionfila_" , $numprescripcion , $cta );
			if ($vIDReporteEntrega != 0){//20 junio , no marcar toda la fila de azul, sino algunas celdas.
				$html1linea .= "<td class = 'encabezadotabla' align='center' bgcolor='#0000FF' > ";
				
				//$idprescricionfila<br>[ $vconsecutivo ]<br><br>Entrega #<br>$divEntrega</td>";

			}else{
				$html1linea .= "<td> ";

			}
			
			$divconsecutivo = crearidcampovalor ( "idcampoconsecutivo_" , $vconsecutivo , $cta );//10 septiembre 2019 , agregar ids a todos los campos consultables
			
			
			$html1linea .= "$idprescricionfila" . "<br>$divconsecutivo<br>Entrega #<br>$divEntrega";//$vconsecutivo
			$divfechamaxentrega = crearidcampovalor ( "idcampofechamaxentrega_" , $fechamaximaentrega , $cta );//10 septiembre 2019 , agregar ids a todos los campos consultables

/*			$html1linea .=  "<table align='center' width='100%'>";
			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>$idprescricionfila</td></tr>";
			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>$divconsecutivo</td></tr>";
			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>Entrega #</td></tr>";
			$html1linea .=  "<tr class='fila".$clasexcelda."'><td>$divEntrega</td></tr>";
*/			
			if ( ( $fechamaximaentrega != "") ){ //($numerodeentrega > 1) &&
				$html1linea .= "<br>F.Max.Entrega<br>$divfechamaxentrega";//fechamaximaentrega
//				$html1linea .=  "<tr class='fila".$clasexcelda."'><td>F.Max.Entrega</td></tr>";					
//				$html1linea .=  "<tr class='fila".$clasexcelda."'><td>$divfechamaxentrega</td></tr>";
			}
//			$html1linea .=  "</table>";
		
			
			$html1linea .= "</td>";

			//------------------------------------- TIPO DE TECNOLOGIA -------------------------
			//tipo de tecnologia

			$divtipotecnologia = crearidcampovalor ( "idtipotecnologia_" , $vtipotec , $cta );//10 septiembre 2019 , agregar ids a todos los campos consultables						
			$html1linea .= "<td>$divtipotecnologia</td>";//vtipotec
			
			//Fecha Mipres
//------------------------------------- CELDA FECHA DEL MIPRES -------------------------			
			$html1linea .= "<td> <div id='idfechamipres_".$cta."' >$fechaMipres</div> <br>$horaMipres</td>";





			//fecha de entrega
//------------------------------------- CELDA FECHA DE ENTREGA DEL MIPRES -------------------------
			//4 julio 2019 - aqui va el campo Sumfem
			//if ($fechaentregamipres != "" )
			$fechaentregaxprescripcion = "";
			if ( $esIngresoManual != 0 ){
				$html1linea .= "<td>$fechaentregaxprescripcion</td>";

			}else{

				if ( ( $fechaentregaxprescripcion != "") && ( $fechaentregaxprescripcion != "0000-00-00") ){
					$html1linea .= "<td>$fechaentregaxprescripcion</td>";
				}elseif ($fechaReporteEntrega != ""){
					$html1linea .= "<td>$fechaEntrega<br>Reporte: $fechaReporteEntrega</td>";
				}else if  ( ( $fechaentregamipres != "") && ( $fechaentregamipres != "0000-00-00") ) {
					$html1linea .= "<td>$fechaentregamipres<br>Sin Reporte </td>";
				}else{
					$html1linea .= "<td>$fechaEntrega<br>Sin Reporte </td>";
				}
			}//if ( $esIngresoManual != 0 ){

//------------------------------------- CELDA DATOS DEL PACIENTE -------------------------
			//Datos del paciente
			$divnumeroidpaciente = crearidcampovalor ( "idnumeroidpaciente_" , $ceduladir , $cta );//10 septiembre 2019 , agregar ids a todos los campos consultables
			$divtipoidpaciente = crearidcampovalor ( "idtipoidpaciente_" , $tipoID , $cta );//10 septiembre 2019 , agregar ids a todos los campos consultables
			
			$html1linea .= "<td>$divtipoidpaciente<br>$divnumeroidpaciente<br>$nomPaciente</td>";// tipoID ceduladir

//------------------------------------- CELDA DE LA TECNOLOGIA Y LA CANTIDAD PRESCRITA -------------------------
			//Tecnologia prescrita y cantidad
			$dividtecnologiaxprescripcion = crearidcampovalor("tecnologiaxprescripcion_" , $codSerTecAEntregar , $cta);
			$html1linea .= "<td>$dividtecnologiaxprescripcion<br>Cant.<br>[$cantidatTotAEntregar]</td>";//$codSerTecAEntregar


//------------------------------------- CELDA CON LA HISTORIA CLINICA EL NUMERO DE INGRESO LA TARIFA USADA Y LA FECHA DE INGRESO -------------------------
			//numero de historia clinica e ingreso //tarifa del paciente.
			$html1linea .= "<td>Historia:<br>$histxmedi-$ingxhist<br>Tarifa:<br>$ingtar<br>F.Ingreso:<br>$ingfecha</td>"; //historia clinica
			//tarifa del paciente.
		//	$html1linea .= "<td>Tarifa: $ingtar </td>"; //tarifa del paciente .


			$tipoStr = "";
			$vdescripcionmipres = "";
			switch ($vtipotec) {
				case "M":
					$tipoStr = "Cum. ";
					//			SELECT Meddmp FROM mipres_000002 WHERE Mednop = '20190327103011109579' AND Medcom = '' ;
					$wherestr = "Mednop = '".$numprescripcion."' AND Medcom ='".$vconsecutivo."' ";
					$vdescripcionmipres = select1campoquery("Meddmp", $wbasedatomipres."_000002", $wherestr);

					break;
				case "P":
					$tipoStr = "Cups. ";
					$queryCups = " SELECT Cupdes FROM mipres_000025 WHERE Cupcod = '".$codcum."';";//27 junio 2019 , no se usa el prefijo mipres
					$wherestr = "Cupcod = '".$codcum."' ";
					$vdescripcionmipres = select1campoquery("Cupdes", "mipres_000025", $wherestr);//27 junio 2019
					break;
				case "N":
					$tipoStr = "Invima. ";
					if ($codarticulo != ""){
						$wherestr = "Lpncod = '".$codarticulo."' ";
						$vdescripcionmipres = select1campoquery("Lpnnom", "mipres_000030", $wherestr);//5 julio 2019 multiempresa

					}
				//SELECT Lpnnom FROM mipres_000030 where Lpncod = '141001';

					break;
				case "D":
					$tipoStr = "Dispositivo. ";
					if ($codarticulo != ""){
						$wherestr = "Tdmcod = '".$codarticulo."' ";
						$vdescripcionmipres = select1campoquery("Tdmdes", "mipres_000026", $wherestr);//5 julio 2019 multiempresa
					}
					break;
				case "S":
					$tipoStr = "Servicio. ";
					if ($codarticulo != ""){
						$wherestr = "Sctcod = '".$codarticulo."' ";
						$vdescripcionmipres = select1campoquery("Sctdes", "mipres_000028", $wherestr);//5 julio 2019 multiempresa
					}
					break;

			}

			$espacios = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";//8 espacios
			$titulostrid = "IDs<br>Prescripciones";
			if ( ($cta == 0)  && ($titulotabla == "") ) {
				//
				//$vchecktodos = " <input type='button' id='bmarcartodos' value='&#9745;' onclick='marcarodesmarcartodos(  );'>";
				$vchecktodos .= "<input type='checkbox' name='bmarcartodos' id = 'bmarcartodos' value='1' onclick='marcarodesmarcartodos( this );' >";

				$botoncopyprescripcion = "   <input type='button' id='bcopiarprescripcionesapuntador' value='#' background-color='transparent' border='none' onclick='copiarprescripcionesapuntador( );'>";

				$titulotabla =  "<td>$botoncopyprescripcion<br>$vchecktodos</td>";// "";
				$titulotabla .= "<td>$titulostrid</td>";
				$titulotabla .= "<td>Prescripci&oacute;n</td>";//28 junio 2019

				$titulotabla .= "<td>Tipo</td>";

				$titulotabla .= "<td>Fecha</td>";

				$titulotabla .= "<td>F. Entrega</td>";



			//	$titulotabla .= "<td>Id.Pac</td>";
				$titulotabla .= "<td>Paciente</td>";
				$titulotabla .= "<td>Tecnologia</td>";




				$titulotabla .= "<td>H.Cl&iacute;nica</td>";
			//	$titulotabla .= "<td>Tarifa</td>";
				$titulotabla .= "<td>Matrix</td>";
				$titulotabla .= "<td>Pedido</td>";
				$titulotabla .= "<td>Entregado</td>";
				$titulotabla .= "<td>Info.Entrega</td>";
				$titulotabla .= "<td>Info.Reporte</td>";

				$titulotabla .= "<td>M&eacute;dico</td>";
				$titulotabla .= "<td>Eps</td>";



				$titulotabla .= "<td>Adicional</td>";
				$titulotabla .= "<td style='display:none;'>Clave</td>";//columna casi invisible
			}
			//17 junio 2019 boton de consumo

			$parametrosaplicacion  = " $cta , \"$histxmedi\" , \"$ingxhist\" , \"$codArtInterno\" , \"$vtipotec\" ,  \"$cantart\" ,  \"$numprescripcion\" ,   \"$vconsecutivo\" , \"$vdescripcionmipres\" ,\"$noentrega\"  ";
			$vbotoncantidad  =  "<input type='button' id='bCantidadAplicada".$cta."' value='".$cantart."' onclick='infocantidadaplicada( $parametrosaplicacion );'>";


			//columna del consumo interno
			$codinternoxcum = $codarticulo;


			
//------------------------------------- CELDA CON LA INFORMACION ENCONTRADA EN MATRIX -------------------------		
// 15 agosto 2019 , Freddy Saenz , modificacion  para agregar el numero de factura 
			$facturastr = "";
			$htmlFact = "";
			$numfactura = 0;
//			$vlrcoopagofactura = 0;
//			$vlrcuotamoderadorafactura = 0;
			$vlrunitariofactura = 0;
			$vlrtotalfactura = 0;
			$cantidadfactura = 0;
			$niteps = "";
			$cantidaddefacturas = 0;
			if ( $cantart > 0 ){
				
				if ( $vusarunixxmipresfacturacion == 0 ) {//no se usa el Unix para consultar las facturas
				//usar la tabla clisur_000018 o idc_000018
					$arrFacturas = facturaycopagomipressintecnologia ( $histxmedi , $ingxhist );
				}elseif  ( $vtipotec == "P" ) {//si es un procedimiento
					$arrFacturas = facturaycopagomipres ( $histxmedi , $ingxhist , $codArtInterno , "'0'" , $vtipotec) ;//0616 medicamentos , pendiente los otros codigos
					
				}else{
					$arrFacturas = facturaycopagomipres ( $histxmedi , $ingxhist , $codArtInterno , "'0616','0626'" , $vtipotec ) ;//0616 medicamentos , pendiente los otros codigos
					
				}
				

	
				$seleccionado = "";
				$cantidaddefacturas = count ($arrFacturas);
				$visibleinfofact = false;
				if ( $cantidaddefacturas > 0 ){
					
					
					$htmlFact ="	<select name='facturasmipres".$cta."' id='facturasmipres".$cta."' style='width:90%;display:inline;' onchange='actualizarfacturas(".$parametrosaplicacion." , this );'>";
					if ( $cantidaddefacturas > 1){
						$htmlFact .= "<option value='0'>Facturas ($cantidaddefacturas)</option>";
					}else{//exatamente una factura
						$visibleinfofact = true;
						$htmlFact .= "<option value='0'>Facturas</option>";
					}
					
					//Modificacion 21 de Marzo ,Freddy Saenz , Mipres para proveedores
					$ctafact = 0;
					foreach($arrFacturas as $key => $value)
					{
						//18 de octubre 2019 
					
						$seleccionado = "";
						if ( $ctafact == 0 ){//inicialmente seleccionar el primero
							//$seleccionado = "selected";
							$numfactura = $arrFacturas[$key]["numero"];
							$vlrcoopagofactura = $arrFacturas[$key]["coopago"];
							$vlrunitariofactura = $arrFacturas[$key]["vlrunitario"];
							$cantidadfactura = $arrFacturas[$key]["cantidad"];
							$vlrtotalfactura = $arrFacturas[$key]["vlrtotal"];
							$niteps = $arrFacturas[$key]["niteps"];
							$vlrcuotamoderadorafactura = $arrFacturas[$key]["cuotamoderadora"];

						}

						//mostrar el seleccionado

						
						if ( ( $vnumerodefactura != "0" ) && ( $vnumerodefactura == $arrFacturas[$key]["numero"] ) ){// con != 0 , no funciona para valores
						//tipo cadena ,Ejemplo si el numero de la factura es  CRE0001521153 
							$seleccionado = "selected";
							$numfactura = $arrFacturas[$key]["numero"];
							$vlrcoopagofactura = $arrFacturas[$key]["coopago"];
							$vlrunitariofactura = $arrFacturas[$key]["vlrunitario"];
							$vlrtotalfactura = $arrFacturas[$key]["vlrtotal"];
							$cantidadfactura = $arrFacturas[$key]["cantidad"];
							$niteps = $arrFacturas[$key]["niteps"];
							$vlrcuotamoderadorafactura = $arrFacturas[$key]["cuotamoderadora"];
							$visibleinfofact = true;
							
						}
						$ctafact++;
						$lineafact = $arrFacturas[$key]["numero"] . " $" . $arrFacturas[$key]["coopago"];  
						$lineafact .= " Cantidad " . $arrFacturas[$key]["cantidad"];//cantidad del articulo en la factura
						$lineafact .= " Vlr Unitario " . $arrFacturas[$key]["vlrunitario"];//valor unitario en la factura
						$lineafact .= " Vlr Total " . $arrFacturas[$key]["vlrtotal"];//valor total en la factura

						$infofac = json_encode($arrFacturas[$key] );//guardar la informacion de la factura en el elemento del combobox correspondiente.
						$htmlFact .= "	<option value='" . $arrFacturas[$key]["numero"] . "' " . $seleccionado . " infofact='" . $infofac . "' >$lineafact" . "</option>";

					//	
					/*	if ( intval($codcausa) == $valorsel ){
							$seleccionado = "selected";
						}*/
					}

					$htmlFact .= "	</select>";
				}
	
				$facturastr = $htmlFact;// "Fact. " . 
				
			}
			
			

			

	
			
			
			
			
			$htmltablefact =  "<table align='center' width='200px'>";//width='100%'

			//Agregar un distintivo al codigo interno (matrix) , si es diferente al pedido
			if ($codArtInterno != $codarticulo){//las nutriciones usan otro codigo
				$codinternoxcum = $codArtInterno.".";

			}
			if ($codinternoxcum == ""){
				$codinternoxcum = $codSerTecAEntregar."-";
			}			
			
			$dividvalortecnologia = crearidcampovalor("valortecnologia_" , number_format($valormed, 2, ',', '.')  , $cta) ;//0 sin decimal (, separador decimal), . para miles
			
			$dividtecnologixconsumoxcum = crearidcampovalor("tecnologiaxprescripcionxmatrix_" , $codinternoxcum , $cta);//28 octubre 2019 creacion de id
			
			$htmltablefact .=  "<tr class='fila".$clasexcelda."'><td>$tipoStr</td><td  colspan='2' width='120px'>$codcum</td></tr>";
			$htmltablefact .=  "<tr class='fila".$clasexcelda."'><td>$dividtecnologixconsumoxcum</td><td colspan='2'>$X $vbotoncantidad</td></tr>";//$codinternoxcum
			$htmltablefact .=  "<tr class='fila".$clasexcelda."'><td>Vlr(1) $</td><td colspan='2' style='text-align: right;'>$dividvalortecnologia</td></tr>";//valormed
			$estilofact = "style='display:none;'";//no mostrar la informacion de las facturas por defecto
			if ( $facturastr != "" ) {
				if ( $cantidaddefacturas > 1 ){//si hay mas de una factura , mostrar el combobox
					$htmltablefact .=  "<tr class='fila".$clasexcelda."'><td colspan='3'>$facturastr</td></tr>";
					
				}else{
					//$htmltablefact .=  "<tr class='fila".$clasexcelda."'><td colspan='3'>Factura</td></tr>";					
				}
				$estilofact = "";
							
			}
			$estiloinfofact = "";
			if ($visibleinfofact==false){//no hay ninguna factura seleccionada o no hay mas de una factura disponible
				
				// $estilofact = "style='display:none;'";//no mostrar la informacion de las facturas por defecto
				$numfactura = 0;
				$vlrcoopagofactura = 0;
				$vlrcuotamoderadorafactura = 0;
				$cantidadfactura = 0;
				$vlrunitariofactura = 0;
				$vlrtotalfactura = 0;
				$niteps = 0;
			}
			$vtxtfact = "#";
			if ($numfactura == 0 ){//No tiene factura aun.
				$vtxtfact = "";
			}//
	//$cantart
			if ( $vjsonentregarealizadatxt != "" ){//ya se entrego
				$arrjsonentregarealizada = json_decode( $vjsonentregarealizadatxt , true );
				$canttotalentrega = $arrjsonentregarealizada["CantTotEntregada"];
				if ( $cantart != $canttotalentrega ){
					$cantart = $canttotalentrega ;
				}
				
			}
			$vlrtotalentregadojson = $valormed*$cantart;
			$vesdifreporteycalculo = false;//mostrar cuando es diferente el valor facturado del valor entregado , y de las cantidades reportadas
			//con el calculo del consumo de las tecnologias.
			$vtxterrodif = "";
			$clasexdifvlrunit = "";
			
			//18 septiembre 2019 , diferenciar las que tienen valor unitario diferente .
			if ( $vjsonreporteentregarealizadatxt != "" ){//ya se entrego
				$arrjsonreporteentregarealizada = json_decode( $vjsonreporteentregarealizadatxt , true );
				$vlrtotalentregadojson = $arrjsonreporteentregarealizada["ValorEntregado"];
				//$vcantidadentregadojson = $arrjsonreporteentregarealizada["CantTotEntregada"];
				if ( $vlrtotalentregadojson != ($valormed*$cantart ) ) {//valores diferentes, como las cantidades son iguales , la diferencia esta en el vlr unitario
					$vesdifreporteycalculo = true;
					$vtxterrodif = "(Vlr(1) Entrega y Consumo)";//Mostrarlo en amarillo
					$clasexdifvlrunit = " bgcolor='#FFFF00'  ";//amarillo
				}
				
			}
			if ( $vesdifreporteycalculo == false ){
				$vesdifreporteycalculo = ( $vlrtotalfactura !=   $vlrtotalentregadojson );
				if ($vesdifreporteycalculo){
					$vtxterrodif = "(Ver Factura y Entrega)";
					$clasexdifvlrunit = " bgcolor='#FF0000'  ";//rojo
				}
				
			}
			if ( $vesdifreporteycalculo == false ){
				$vesdifreporteycalculo = ( ( $vlrunitariofactura*$cantidadfactura ) != ( $vlrtotalentregadojson ) ); 
				if ($vesdifreporteycalculo){
					$vtxterrodif = "(Vlr(1) Factura y Entrega)";
					$clasexdifvlrunit = " bgcolor='#FF0000'  ";//rojo
				}
			}
			//
			
			$htmltablefact .=  "<tr class='fila".$clasexcelda."' $estilofact><td>Factura $vtxtfact</td><td colspan='2' style='text-align: right;'><div id='idnumfact_".$cta."'>$numfactura</div></td></tr>";
			$htmltablefact .=  "<tr class='fila".$clasexcelda."' $estilofact><td>Copago</td><td colspan='2' style='text-align: right;'><div id='idcoopagofact_".$cta."'>". number_format($vlrcoopagofactura, 2, ',', '.') . "</div></td></tr>";//0 sin decimal (, separador decimal), . para miles
			$htmltablefact .=  "<tr class='fila".$clasexcelda."' $estilofact><td>C.Moderadora</td><td colspan='2' style='text-align: right;'><div id='idcuotamoderadorafact_".$cta."'>" . number_format($vlrcuotamoderadorafactura, 2, ',', '.') . "</div></td></tr>";//0 sin decimal (, separador decimal), . para miles
			$htmltablefact .=  "<tr class='fila".$clasexcelda."' $estilofact><td>Cantidad</td><td colspan='2' style='text-align: right;'><div id='idcantidadfact_".$cta."'>" . $cantidadfactura . "</div></td></tr>";
			
			$htmltablefact .=  "<tr class='fila".$clasexcelda."' $estilofact><td>Vlr(1) Fact.</td><td colspan='2' style='text-align: right;'><div id='idvlrunitariofact_".$cta."'>". number_format($vlrunitariofactura, 2, ',', '.') . "</div></td></tr>";//0 sin decimal (, separador decimal), . para miles


			$htmltablefact .=  "<tr class='fila".$clasexcelda."' $estilofact  ><td>Total Fact. $vtxterrodif</td><td colspan='2' style='text-align: right;' $clasexdifvlrunit><div id='idvlrtotalfact_".$cta."'>". number_format($vlrtotalfactura, 2, ',', '.') . "</div></td></tr>";//0 sin decimal (, separador decimal), . para miles
		
			$htmltablefact .=  "<tr class='fila".$clasexcelda."' $estilofact><td>Vlr.Entregado</td><td colspan='2' style='text-align: right;'><div id='idvlrentregadoreporte_".$cta."'>". number_format($vlrtotalentregadojson, 2, ',', '.')  ."</div></td></tr>";//0 sin decimal (, separador decimal), . para miles

			$htmltablefact .=  "<tr class='fila".$clasexcelda."' style='display:none;'><td>Nit Eps</td><td colspan='2'><div id='idniteps_".$cta."'>$niteps</div></td></tr>";

//30 agosto 2019	
//$vlrcuotamoderadorafactura = $vlrcuotamoderadorafactura
// $vlrcoopagofactura = $vlrcoopagofactura;
//$vlrunitario = $valormed
		$arrfacturacion = creararrfacturacion ($numprescripcion , 	$vtipotec ,$vconsecutivo , $tipoID , $ceduladir ,	 $numerodeentrega , $codservxmipres , $cantart ,  $valormed ,$eps , $niteps , $numfactura ,  $vlrcuotamoderadorafactura , $vlrcoopagofactura);
					
//		$arrfacturacion = creararrfacturacion ($numprescripcion , 	$vtipotec ,$vconsecutivo , $tipoID , $ceduladir ,	 $numerodeentrega , $codservxmipres , $cantart ,  $vlrunitario ,$eps , $niteps , $numfactura ,  $vlrcuotamoderadorafactura , $vlrcoopagofactura);
		$varrjsonatxt = json_encode($arrfacturacion);
		//2
		$vtxdatafacturacion .= "<input type='hidden' id='infofacturacionmipres_".$cta."' value='".$varrjsonatxt."'>";//aqui se guarda el json con la informacion que se enviara al ws.
//$vtxdatafacturacion = "";

		$htmltablefact .=  "<tr class='fila".$clasexcelda."' style='display:none;'><td colspan='3'>$vtxdatafacturacion</td></tr>";


			$htmltablefact .=  "</table>";

			$html1linea .= "<td>$htmltablefact</td>" ;


			
		//	$html1linea .= "<td>$tipoStr : $codcum = $codinternoxcum X $vbotoncantidad Vlr(1) =$ $valormed <br> $facturastr </td>"; //tarifa del paciente .
	


//--------------------------------------------------------------------------------	
		//28 agosto 2019 , Freddy Saenz , se agrega un nuevo critero de busqueda
		if ( $filtroFacturacion == '' ){//todos
			
		}elseif( $filtroFacturacion == 'Facturadas' ){//ID de facturacion distinto de cero
			if( ( $numfactura != 0 ) && ( $vIDFacturacion != 0 ) ){//facturada
				
			}else{
				continue;
			}
		}elseif( $filtroFacturacion == 'NO-Facturadas' ){//tienen factura pero no tienen id todavia
			if(  (  $vIDFacturacion == 0 ) && ( $cantidaddefacturas > 0 ) ) {//NO facturada

			}else{
				continue;
			}
			
		}elseif( $filtroFacturacion == 'SinFactura' ){//no tienen factura asociada.
			if ( $numfactura != "0" ){//tiene factura asociada
				continue;
			}elseif ( $cantidaddefacturas > 0 ){//o por lo menos tiene una factura para asociar.
				continue;
			}else{
				
			}
		}elseif ( $filtroFacturacion == 'FacturaIgualAEntregado'  ) {//$vlrtotalfactura == ($valormed * $cantidadfactura)
			if ( ( $vesdifreporteycalculo == false ) && ( $vIDEntrega != 0 ) ) {//Ya se entrego y el valor de la factura es igual a el valor de lo entregado
//no hay diferencia entre lo entregado y lo facturado TENER EN CUENTA ESTE REGISTRO
			}else{
				continue;
			}
		}elseif ( $filtroFacturacion == 'FacturaNoIgualAEntregado'  ) {
			//$vesdifreporteycalculo == false  //$vlrtotalfactura == ($valormed * $cantidadfactura)
			if ( ( $vesdifreporteycalculo == false ) ) {//Ya se entrego y el valor de la factura es igual a el valor de lo entregado	
//no hay diferencia entre lo entregado y lo facturado , NO TENER EN CUENTA ESTE REGISTRO , se hace un continue		
				continue;				
				
			}elseif ( $numfactura == "0" ){
				continue;
			}elseif ( $vIDEntrega == 0 ){
				continue;
			}//&& ( $vIDEntrega != 0 )
		}
			




	

//------------------------------------- CELDA CON LA INFORMACION DE LA TECNOLOGIA SOLICITAD EN EL MIPRES -------------------------	
			//nombre de la tecnologia como aparece en el mipres , 26 junio 2019
			if (( $vtipotec=="M") || ( $vtipotec=="P")){
				$html1linea .= "<td> $vdescripcionmipres </td>"; //descripcion de lo que se le esta realizando al paciente

			} else if ( $codarticulo != "" ){
				$html1linea .= "<td> $vdescripcionmipres <br> Tecnologia: <br> $codarticulo</td>"; //descripcion de lo que se le esta realizando al paciente , se agrega
				//el codigo de la tecnologia solicitado.
			} else{
				$html1linea .= "<td> $vdescripcionmipres </td>"; //descripcion de lo que se le esta realizando al paciente , se agrega

			}



//31 de Mayo 2019
			if ( ( $esIngresoManual != 0 )  ){
				//16 julio 2019 , Freddy Saenz , la cantidad editada puede ser cero.
				if ( ( $semodificocantidad != 0 ) || ( $cantidadManual != 0 )  ) {
					//17 Julio 17 , se necesita un nuevo campo para determinar cuando es cero
					$cantart = $cantidadManual;
				}	
				if ( $codigoManual != "" ){
					$codservxmipres = $codigoManual;
				}
				//$valormed = $vlrManual;
				if ($vlrManual != 0){
					$valormed = $vlrManual;
				}
									
			}
			
			$vceldaentregado = "";
			//nombre del articulo despachado como aparece en la base de datos Matrix.
			if ( ( ( $vdescripcionInterna == "" ) &&  ($vIDEntrega == 0) && ( $vIDReporteEntrega == 0 ) )  &&  (intval($vEstadoPrescripcion) != 2 ) ){//poder modicar editar , cantida, codigo , valor
				//filtrar que no este anulado
				if ( ($ingresoManualMipres != 0 ) & ( $esIngresoManual == 0) ) {//se puede hacer un ingreso manual , pero todavia no se ha hecho.
					//
					$botonIngresoManual = " <input type='button' id='bIngresoManual' value='Ingreso Manual' onclick='ingresomanual( ".$parametrosdlg." );'>";
					$vceldaentregado = $botonIngresoManual;
				}else if ($esIngresoManual != 0 ){
					$vceldaentregado = "Ingreso Manual"; //nombre interno el la clinica
				}else{
					$vceldaentregado = $vdescripcionInterna; //nombre interno el la clinica
				}

			}else{
				$vceldaentregado = $vdescripcionInterna;//nombre interno el la clinica
			}
//------------------------------------- CELDA CON LO APLICADO O ENTREGADO -------------------------				
			
			$html1linea .= "<td> <div id = 'celdaentregado-".$cta . "' > $vceldaentregado </div> </td>";
//$vfechaentrega pintarPrescripcionesEnTabla
			//como el ingreso manual puede ser por cambio de codigo o cantidad , es necesario conservar la fecha de aplicacion
			//16 julio 2019 Freddy Saenz
			if ($vtipotec == "P"){
				$wherestr = " Tcarprocod = '".$codArtInterno."' and Tcarhis = '".$histxmedi."' AND Tcaring = '".$ingxhist."'";
				$vfechaentrega = select1campoquery("Tcarfec", $wbasedatocliame."_000106", $wherestr);

			}else {
				$wherestr = " Aplart = '".$codArtInterno."' and Aplhis = '".$histxmedi."' AND Apling = '".$ingxhist."'";
				$vfechaentrega = select1campoquery("Aplfec", $wbasedato."_000015", $wherestr);

			//	echo "<script>jAlert('Fecha de where ".str_replace("'","",$wherestr)."','ALERTA')</script>";

			}
			if ( ($vfechaentrega == "")  || ($vfechaentrega == "0000-00-00") ){
				$vfechaentrega = $fechaentregamipres;
			}
				


			$arrenvio = array();
			$arrenvioID = array();
			$datareporte = array();
	//		$causanoentrega = 0;
			$estadoentrega= 0;
			if ( ( ($cantart > 0)  || ($esIngresoManual == 1 ) )  && ($codservxmipres!="")){ //si hay cantidades y tiene codigo para mipres
				//Si es un ingreso manual se permite la cantidad cero en la entrega
				// 16 julio 2019 Freddy Saenz
				$ventregatotal = 0;
				if ( $cantart >= $cantidatTotAEntregar ){
					$cantart = $cantidatTotAEntregar;// la cantidad no debe ser mayor a la cantidad total , 27 mayo 2019
					$ventregatotal = 1;
				}
				if  ( ($esIngresoManual == 1 ) && ( $cantart == 0 ) ){//16 julio 2019 , Freddy Saenz , hacer entregas totales con cantidad cero.
					$ventregatotal = 0;//18 julio 2019 , entrega total en cero ,no se entrego nada 
				}

				$lote = "";
				$arrenvio = creararrxenvio ($numprescripcion,$vtipotec,$vconsecutivo,$tipoID,$ceduladir,$noentrega,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote);
				$arrenvioID = creaarrxenvioconID($vID,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote , $tipoID , $ceduladir , $numprescripcion );//Se agregan 3 parametros mas , cambio el mipres segun requerimientos del 26 de Julio 2019 ,Freddy Saenz
				$estadoentrega= 1;
				$datareporte = creararrxreporte($vID,$estadoentrega,$causanoentrega,$cantart,$valormed);



			} //if ($cantart>0){//si hay cantidades


			$cadenvio = json_encode($datareporte)	;


			//18 DE JUNIO 2019
			if ( (intval($vEstadoPrescripcion) == 2 ) || ($vestadodireccionamiento == 0) ){//si esta anulado
			//11 julio 2019 se incluye el direccionamiento anulado
				$classFila =  "<tr class = 'prescripcioncompleta find' bgcolor='#FF0000'  align='center'>";//rojo para los anulados 5 julio 2019 find
				$vtxdataenvio = "ANULADO";
				$vtxdatareporte = "ANULADO";
				if ( $vestadodireccionamiento == 0) {
					$vtxdataenvio .= " &nbsp;DIRECCIONAMIENTO";
					$vtxdatareporte .= " &nbsp;DIRECCIONAMIENTO";

				}
			}else{

	//Celda de Entrega  //2 pintarPrescripcionesEnTablaecho

//01 $esIngresoManual , tiene la busqueda en mipres_000037
				$vtxdataenvio =  datacellenvio( $cta , $arrenvio , $cantart , $cantidatTotAEntregar , $vIDEntrega , $vIDReporteEntrega , $codservxmipres , $valormed , $vID,$estadoentrega,$causanoentrega,$arrenvioID,$vtipotec,$numprescripcion,$vconsecutivo , $vfechaentrega , $esIngresoManual);
				//16 julio 2019 , ingreso manual ,para activar el envio con cantidad = 0
				$pos = strpos($vtxdataenvio, "#66D066");//ver cuales filas estan listas para enviar.

				if ($pos === false) // no se encontro
				{
					if ( intval($vIDReporteEntrega) != 0 ){//resaltar las que estan completas.
						//$classFila =  "<tr class = 'encabezadotabla' align='center' bgcolor='#0000FF' >";//azul
						//20 junio , no marcar toda la fila de azul, sino algunas celdas.
					}

				}else{

					$classFila =  "<tr class = 'prescripcioncompleta find' bgcolor='#66D066'  align='center'>";//5 julio 2019 find


				}
				$vtxdatareporte  = datacellreporte( $cta , $datareporte , $vIDReporteEntrega );


			}//if (intval($vEstadoPrescripcion) == 2 ){//si esta anulado

//------------------------------------- CELDA CON EL JSON DE LA ENTREGA -------------------------	

			$html1linea .= "<td> <div id = 'divenvio-".$cta . "' >$vtxdataenvio</div> </td>";

//------------------------------------- CELDA CON EL JSON DEL REPORTE DE ENTREGA -------------------------	
//Celda del reporte de entrega

			$html1linea .= "<td> <div id = 'reporte-".$cta . "' >$vtxdatareporte</div> </td>";

//------------------------------------- CELDA CON LA INFORMACION DEL MEDICO QUE HACE LA PRESCRIPCION -------------------------
			//medico
			$html1linea .= "<td>$medico</td>";
			
//------------------------------------- CELDA DE LA EPS A LA QUE PERTENECE EL PACIENTE DE LA PRESCRIPCION -------------------------			
			//Eps
//$html1linea .= "<td>$eps</td>";
			$html1linea .= "<td>$divEps</td>";// 20 agosto 2019


//Informacion Adicional

			$vtxtfilaarreglo = json_encode($value);
			$vtxdatafila = "<input type='hidden' id='infoJsonFila_".$cta."' value='".$vtxtfilaarreglo."'>";//aqui se guarda la informacion de la fila



			$txtxdir = str_replace( "'" , "\"" , $cadenvio );// "[ { $txtxdir }] ";
			$prefijoxUp= "Sum";


			switch ($vtipotec) {
				case "M":
				case "P":
				case "N":

				case "D":
				case "S":

					if ($vID != 0){



						$setcampos = "";//$prefijoxUp."jdi =  '".$txtxdir."' ";//" ".$prefijoxUp."ID = '".$vID."' , ".
						$separadoramposset = "";
						if ($codSerTecAEntregar != ""){
							$setcampos .=  "  ".$prefijoxUp."cst = '".$codSerTecAEntregar."' ";
						}
						if ($vIDEntrega != 0) {
							if ($setcampos != ""){
								$separadoramposset .=  " , ";
							}
							$setcampos .=  $separadoramposset . $prefijoxUp."IDe = '".$vIDEntrega."' ";
						}
						$separadoramposset = "";
						if ($vIDReporteEntrega != 0){
							if ($setcampos != ""){
								$separadoramposset =  " , ";
							}

							$setcampos .= $separadoramposset . $prefijoxUp."IDr = '".$vIDReporteEntrega."' ";
						}
						//$where = clavemi presdetalle($vtipotec , $numprescripcion , $vconsecutivo , $vID);
						$where = claveppalmipres( $vID ,$vtipotec , $numprescripcion , $vconsecutivo , $numerodeentrega );//3 DE JULIO 2019
	//


						$numlineasup = 0;
						if ($setcampos != ""){//si hay algo por modificar.
							$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
						}


						if ($cantidadOriginal != $cantart){
							$vtxtmodificacion = "Cantidad Modificada $cantidadOriginal ";

						}
						if ($codservxmipres != $codigoOriginal){
							if ($vtxtmodificacion != ""){
								$vtxtmodificacion .= "<br>";
							}
							$vtxtmodificacion .= "Codigo Modificado $codigoOriginal ";
						}
						if ($vtxtmodificacion == ""){
							$vtxtmodificacion =  "";//"$where $numlineasup";
						}

						$html1linea .= "<td> $vtxtmodificacion $vtxdatafila ";

						$html1linea .= "</td>";
					}else{
						$vtxtmodificacion = "";
						if ($cantidadOriginal != $cantart){
							$vtxtmodificacion = "Cantidad Modificada $cantidadOriginal ";

						}
						if ($vtxtmodificacion != ""){
							$vtxtmodificacion .= "<br>";
						}

						$html1linea .= "<td>$vtxtmodificacion Sin ID $vtxdatafila </td>";
					}
					break;


				default :
					$html1linea .= "<td> $vtipotec $vtxdatafila </td>";
					break;

			}




			$html1linea .= "<td style='display:none;'>$vDividregistrotabla</td>";//columna invisible .


			$html1linea = $classFila . $html1linea;//permite cambiar el color de la primera columna.

			$html1linea .= "</tr>";


			// 22 junio 2019 , filtrar por las completas (ya se ha aplicado o consumida toda la tecnologia)
			if ( ( $filtroestadomipres == "Completa-SinEntrega") && (  ($vIDEntrega == 0) || ( $vIDReporteEntrega == 0 ) ) ) {
			  if (	( $cantart < $cantidatTotAEntregar ) || ( $cantidatTotAEntregar == 0) ) {//no se ha aplicado la totalidad
			  //filtrar por la cantidad total
					continue;
				}
			}

			if($cta ==0){
				$html .= "<tr class='fila2' align='center'>$titulotabla</tr>";
			}



			$html .= $html1linea;
			$cta++;
			$ctafilastabla++;
			$filas++;// 22 junio 2019

		}//foreach($arrinfo as $key => $value) recorrer los registros



		$html .= "</table>";

		$botonesencabezado = "";
		if ($cta > 0){//si hay elementos

			$botonesencabezado = "<table>";
			$botonesencabezado .= "<tr>";
			$botonesencabezado .= "<td class='encabezadotabla' align='center'> <input type='button' id='btnEnviarListado' value='Enviar Seleccion' onclick='enviarSeleccion();'></td>";
			$botonesencabezado .= "<td class='encabezadotabla' align='center'> <input type='button' id='btnUsarSeleccion' value='Usar Seleccion' onclick='usarSeleccion();'> </td>";

			$botonesencabezado .= "<td class='encabezadotabla' align='center' colspan='3'>
				<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
				Filtrar:&nbsp;&nbsp;</b> <input id='buscar' type='text' placeholder='filtro' style='border-radius: 4px;border:1px solid #AFAFAF;'>
				</span>	</td>";
	//uso del filtro. 5 julio 2019

		   $botonesencabezado .= "<td class='encabezadotabla' align='center'> <input type='button' id='btnEnviarFacturacionSel' value='Enviar Facturacion x Seleccion' onclick='enviarFacturacionSeleccion();'> </td>";
//16 abril 2020 , solicitud de reporte
	//se pasa en la parte general de busquedas , para que este activo siempre	   $botonesencabezado .= "<td class='encabezadotabla' align='center'> <input type='button' id='btnReporteDiarioPrescripciones' value='Reporte Diario de Prescripciones' onclick='freportediariomipres();'> </td>";

			$botonesencabezado .= "</tr>";
			$botonesencabezado .= "</table>";
		}else{//if ($cta > 0){//si hay elementos
			$botonesencabezado = "<table>";
			$botonesencabezado .= "<tr>";
//16 abril 2020 , solicitud de reporte
			$botonesencabezado .= "<td class='encabezadotabla' align='center'> <input type='button' id='btnReporteDiarioPrescripciones' value='Reporte Diario de Prescripciones' onclick='freportediariomipres();'> </td>";

			$botonesencabezado .= "</tr>";
			$botonesencabezado .= "</table>";
		}


		return $botonesencabezado . $html;//reubicados los botones
	}//function pintarPrescripcionesEnTabla ($arrinfo ,$filtroestadomipres  )


	function crearidcampovalor ( $campo , $valor , $posicion ){
		
		return "<div id='" . $campo . $posicion. "'>$valor</div>" ;//10 septiembre 2019
	}

	//12 junio 2019
//Varios articulos con el mismo cum
	function codigosmismocum ( $codcum )
	{
		global $wbasedato;
		global $conex;


		$arrayproductos = array();
		if ($codcum == ""){
			return $arrayproductos;
		}
		$q = " SELECT Artcod , Artcom , Artgen , Artcum FROM ".$wbasedato."_000026 WHERE Artcum = '" . $codcum . "' ";
		if (!( $resnumprod = mysql_query($q, $conex) ) ){//7 de mayo 2020
			var_dump("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());	

		}
		//$resnumprod = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numproductos = mysql_num_rows($resnumprod);

		if ($numproductos > 0)
		{
			while($rowsproductos = mysql_fetch_array($resnumprod))
			{
				$vtxtcum = "";
				if ($rowsproductos['Artcum'] != ""){
					$vtxtcum = $rowsproductos['Artcum']  ;
				}else{
					$vtxtcum = "";// "SIN CUM ";
				}

				$arrayproductos[ $rowsproductos['Artcod'] ] = $vtxtcum . "/" .    utf8_encode( $rowsproductos['Artgen'] ) . "/" . utf8_encode( $rowsproductos['Artcom'] ) . "/X Cum" ;

			}

		}
		return $arrayproductos;//$arrPartes;//$arrresult;

	}//function codigosmismocum


	//Busqueda por principio activo
	// 25 de junio 2019
	function codigosxprincipioactivo ( $principioactivo )
	{
		global $wbasedatomipres;
		global $wbasedato;
		global $conex;


		$arrayproductos = array();
		if ($principioactivo == ""){
			return $arrayproductos;
		}
		$q = " SELECT Patcoa ,  Artcom , Artgen , Artcum  ";
		$q .= " FROM ".$wbasedatomipres."_000039,".$wbasedato."_000026 ";
		$q .= " WHERE Artcod = Patcoa AND  Patdmp = '" . $principioactivo . "' ";
		if (!( $resnumprod = mysql_query($q, $conex) ) ) {
			var_dump ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		}
	//	$resnumprod = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numproductos = mysql_num_rows($resnumprod);

		if ($numproductos > 0)
		{
			while($rowsproductos = mysql_fetch_array($resnumprod))
			{
				$vtxtcum = "";
				if ($rowsproductos['Artcum'] != ""){
					$vtxtcum = $rowsproductos['Artcum']  ;
				}else{
					$vtxtcum = "";// "SIN CUM ";
				}

				$arrayproductos[ $rowsproductos['Patcoa'] ] = $vtxtcum . "/" .    utf8_encode( $rowsproductos['Artgen'] ) . "/" . utf8_encode( $rowsproductos['Artcom'] ) . "/X P. Activo" ;

			}

		}
		return $arrayproductos;//$arrPartes;//$arrresult;

	}//function codigosxprincipioactivo



	//Consulta el consumo en la  base de datos matrix.
	function infocantidadvlrycodigomipres($numprescripcion , $vconsecutivo , $fechaMipres , $coddemipres , $histxmedi, $ingxhist, $ingtar , $vtipotec , $noentrega , $fechamaximaentrega )
	{

		global $wbasedatomipres;
		global $wbasedato;

		$arrresult = array();

		$codcum = $coddemipres;
		$codarticulo = $coddemipres;
		$codArtInterno = $coddemipres;
		$cantart = 0;
		$valormed = 0;
		$vdescripcionInterna = "";
		$codservxmipres = "";
		$vlrModificado = 0;

		$cantmodificada = 0;
		$codmodificado = "";
		$causanoentrega = 0;
		//echo " tarifa $ingtar ";
		$vID = 0;
		$fechaCargo = "";
		$encontrado = 0;
		$vdescripcionInterna = "";
		$vcodtecnoligiaprescripcion = "";//codigo de la tecnoligia que tiene el mipres.

		if ($ingtar != "") {

			$wherestr = "Sumnop = '".$numprescripcion."' AND  Sumcon = '".$vconsecutivo."' AND  Sumtip = '".$vtipotec."' ";
			$wherestr .= " AND Sumnen = '".$noentrega."' ";
			if ($noentrega != 0){//3 julio 2019 , usar el numero de entrega como parte de la clave .
//10 julio 2019
			}

			$arrFila = select1filaquery("Sumcae,Sumcoe,Sumvlr,SumID,Sumcne,Sumvlr", $wbasedatomipres."_000037", $wherestr);//29 mayo
			if ( count($arrFila) > 0){

				$cantmodificada = $arrFila['Sumcae'];
				$codmodificado = $arrFila['Sumcoe'];
				$vID = $arrFila['SumID'];
				$causanoentrega = $arrFila['Sumcne'];
				$vlrModificado = $arrFila['Sumvlr'];
			}

			$codprincipioactivo = "";
			//26 de junio 2019 , usar la tabla que asocia codigo de la tecnologia con el codigo del articulo en matrix.
			switch ($vtipotec) {//buscar usando la tabla intermedia que asocia el codigo de la tecnologia o el principio activo con el maestro
			//de articulos.
/*				case "M"://tiene un comportamiento diferente a los de Nutricion , Dispositivos o Servicios
					$wherestr = "Mednop = '".$numprescripcion."' AND  Medcom = '".$vconsecutivo."' ";
					$arrFila = select1filaquery("Meddmp", $wbasedatomipres."_000002", $wherestr);
					if ( count($arrFila) > 0){
						$codprincipioactivo = $arrFila['Meddmp'];
					}
					break;*/

				case "N":
					$wherestr = "Nutnop='".$numprescripcion."' AND Nutcon = '".$vconsecutivo."' ";

					$arrFila = select1filaquery("Nutdpn", $wbasedatomipres."_000006", $wherestr);
					if ( count($arrFila) > 0){
						$codprincipioactivo = $arrFila['Nutdpn'];
					}
					break;
				case "D":
					$wherestr = "Disnop='".$numprescripcion."' AND Discod = '".$vconsecutivo."' ";
					$arrFila = select1filaquery("Discdi", $wbasedatomipres."_000005", $wherestr);
					if ( count($arrFila) > 0){
						$codprincipioactivo = $arrFila['Discdi'];
					}
					break;
				case "S":
					$wherestr = "Sernop='".$numprescripcion."' AND Sercos = '".$vconsecutivo."' ";

					$arrFila = select1filaquery("Sercsc", $wbasedatomipres."_000007", $wherestr);
					if ( count($arrFila) > 0){
						$codprincipioactivo = $arrFila['Sercsc'];
					}


			}//switch ($vtipotec) {//buscar usando la tabla intermedia

			if ( $codprincipioactivo != "" ){//buscar por el codigo de la tecnologia o principio activo
				$vcodtecnoligiaprescripcion = $codprincipioactivo;

				$arrcodmedicamentos = codigosxprincipioactivo($codprincipioactivo);//asociar codigo de la nutricion con el codigo del maestro de articulos

				//$arrcodmedicamentos contiene los codigos internos (maestro de articulos)
				// $codarticulo es el codigo de la nutricion
				foreach($arrcodmedicamentos as $codpalclave => $nomclave) {

					if ($encontrado == 0) {
						$cantart = sumarMedicamentosMipres($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
						//19 de Junio 2019
						if ($cantart == 0){//ver si esta en central de mezclas
							$cantart = sumarMedicamentosxcentralmezclas($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega ); // , );//
						}//if ($cantart == 0){//ver si esta en central de mezclas
						if ($cantart > 0) {

							//$codarticulo = $codpalclave;
							//27 de junio 2019 $valormed = valorTarifaMedicamento( $codpalclave, $ingtar, $fechaMipres ); //
							$valormed = valorcobradoMedicamento($codpalclave, $histxmedi, $ingxhist, $ingtar, $fechaMipres);
							if ($valormed != 0) {
								$encontrado = 1;
								//$vdescripcionInterna = $nomclave;
								$codArtInterno = $codpalclave;

								if ($codservxmipres == ""){
									$wherestr = "Artcod = '".$codpalclave. "' ";
									//$codservxmipres = select1campoquery("Artcum", $wbasedato."_000026", $wherestr);

									$arrFila = select1filaquery("Artcod,Artcum,Artgen,Artcom", $wbasedato."_000026", $wherestr);
									if (count($arrFila)>0){
										$codservxmipres =  $arrFila['Artcum'];
										$vdescripcionInterna = $codservxmipres . "/" .    utf8_encode( $arrFila['Artgen'] ) . "/" . utf8_encode( $arrFila['Artcom'] )  . "//X Tecnologia";
									}
								}



							}//if ($valormed != 0)
						}// if ($cantart > 0)


					}// if ($encontrado == 0)

				}//foreach($arrcodmedicamentos as $codpalclave => $nomclave)
			}//if ($codprincipioactivo != "" ){//buscar por el codigo de la tecnologia o principio activo


			if ($encontrado == 0) {//se busco por codigo de la tecnologia y no se encontro


				switch ($vtipotec) {
					case "M":

						if ($codarticulo != "") {

							//---
							$wherestr = "Artcum = '".$coddemipres."' ";//buscar el codigo  basado en este cum.
							$codarticulo = select1campoquery("Artcod", $wbasedato."_000026", $wherestr); //codigo del articulo en matrix


						}

						//$encontrado = 0;

						$wherestr = "Mednop = '".$numprescripcion."' AND  Medcom = '".$vconsecutivo."' ";
						$arrFila = select1filaquery("Meddmp", $wbasedatomipres."_000002", $wherestr);
						if ( count($arrFila) > 0){
							$vdescprincipioactivo = $arrFila['Meddmp'];
						}


						//12 junio 2019, varios articulos con el mismo cum
				//		if ($codarticulo != ""){
						if (  ( $encontrado == 0 ) && ($coddemipres != "") ) {


						//if ( ( $codarticulo == "") && ( $encontrado == 0 ) && ($coddemipres != "") ) {	21 junio 2019
							$arrcodmedicamentos = codigosmismocum($coddemipres);


							foreach($arrcodmedicamentos as $codpalclave => $nomclave) {


								if ($encontrado == 0) {
									$cantart = sumarMedicamentosMipres($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
									//19 de Junio 2019
									if ($cantart == 0){//ver si esta en central de mezclas
										$cantart = sumarMedicamentosxcentralmezclas($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
									}//if ($cantart == 0){//ver si esta en central de mezclas
									if ($cantart > 0) {

										$codarticulo = $codpalclave;
										//27 de junio 2019 $valormed = valorTarifaMedicamento($codarticulo, $ingtar, $fechaMipres); //
										$valormed = valorcobradoMedicamento($codpalclave, $histxmedi, $ingxhist, $ingtar, $fechaMipres);
										if ($valormed != 0) {
											$encontrado = 1;
											$vdescripcionInterna = $nomclave;
											//$codservxmipres = $codcum;
											if ($vdescripcionInterna != ""){
												$arrpartes = explode("/",$vdescripcionInterna);
												//en la descripcion aparece el codigo cum, el nombre generico y el nombre comecial
												//separados por un /
												//cum/nombre generico/nombre comercial
												if (count($arrpartes) > 0){
													$codservxmipres = $arrpartes[0];
													if ($codservxmipres != ""){
														$vdescripcionInterna = "CUM: " . $vdescripcionInterna;
													}
												}
											}//if ($vdescripcionInterna!= "")
												//else{$vdescripcionInterna=" vacio ".$nomclave;

												//}

										}//if ($valormed != 0)
									}// if ($cantart > 0)


								}// if ($encontrado == 0)

							}//foreach($arrcodmedicamentos as $codpalclave => $nomclave)



						}//if ($codarticulo != "")

	//25 junio 2019 buscar por el principio activo

						if (  ( $encontrado == 0 ) && ($vdescprincipioactivo != "") ) {
						//if ( ( $codarticulo == "") && ( $encontrado == 0 ) && ($coddemipres != "") ) {	21 junio 2019
							$arrcodmedicamentos = codigosxprincipioactivo($vdescprincipioactivo);


							foreach($arrcodmedicamentos as $codpalclave => $nomclave) {


								if ($encontrado == 0) {
									$cantart = sumarMedicamentosMipres($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
									//19 de Junio 2019
									if ($cantart == 0){//ver si esta en central de mezclas
										$cantart = sumarMedicamentosxcentralmezclas($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
									}//if ($cantart == 0){//ver si esta en central de mezclas
									if ($cantart > 0) {

										$codarticulo = $codpalclave;
										//27 de junio 2019 $valormed = valorTarifaMedicamento($codarticulo, $ingtar, $fechaMipres); //
										$valormed = valorcobradoMedicamento($codpalclave, $histxmedi, $ingxhist, $ingtar, $fechaMipres);
										if ($valormed != 0) {
											$encontrado = 1;
											$vdescripcionInterna = $nomclave;
											//$codservxmipres = $codcum;
											if ($vdescripcionInterna != ""){
												$arrpartes = explode("/",$vdescripcionInterna);
												//en la descripcion aparece el codigo cum, el nombre generico y el nombre comecial
												//separados por un /
												//cum/nombre generico/nombre comercial
												if (count($arrpartes) > 0){
													$codservxmipres = $arrpartes[0];
													if ($codservxmipres != ""){
														$vdescripcionInterna = "CUM: " . $vdescripcionInterna;
													}
												}
											}//if ($vdescripcionInterna!= "")
												//else{$vdescripcionInterna=" vacio ".$nomclave;

												//}

										}//if ($valormed != 0)
									}// if ($cantart > 0)


								}// if ($encontrado == 0)

							}//foreach($arrcodmedicamentos as $codpalclave => $nomclave)



						}//if ($codarticulo != "")


	// 25 fin busqueda por principio activo


						if ( ($vdescprincipioactivo != "" )  && ( $encontrado == 0 ) ){//$codarticulo == "")


							$arrcodmedicamentos = palabraclavenombre($vdescprincipioactivo, "" , ""); //$vdosis);


							foreach($arrcodmedicamentos as $codpalclave => $nomclave) {


								if ($encontrado == 0) {
									$cantart = sumarMedicamentosMipres($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
									//19 de Junio 2019
									if ($cantart == 0){//ver si esta en central de mezclas
										$cantart = sumarMedicamentosxcentralmezclas($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
									}//if ($cantart == 0){//ver si esta en central de mezclas

									if ($cantart > 0) {

										$codarticulo = $codpalclave;
										//27 de junio 2019 $valormed = valorTarifaMedicamento($codarticulo, $ingtar, $fechaMipres); //
										$valormed = valorcobradoMedicamento($codpalclave, $histxmedi, $ingxhist, $ingtar, $fechaMipres);
										if ($valormed != 0) {
											$encontrado = 1;
											$vdescripcionInterna = $nomclave;
											//$codservxmipres = $codcum;
											if ($vdescripcionInterna != ""){
												$arrpartes = explode("/",$vdescripcionInterna);
												//en la descripcion aparece el codigo cum, el nombre generico y el nombre comecial
												//separados por un /
												//cum/nombre generico/nombre comercial
												if (count($arrpartes) > 0){
													$codservxmipres = $arrpartes[0];
													if ($codservxmipres != ""){
														$vdescripcionInterna = "CUM: " . $vdescripcionInterna;
													}
												}
											}//if ($vdescripcionInterna!= "")
												//else{$vdescripcionInterna=" vacio ".$nomclave;

												//}

										}//if ($valormed != 0)
									}// if ($cantart > 0)
									if ($encontrado != 0) {//se encontro por nombre , se debe actualizar el principio activo

									}

								}// if ($encontrado == 0)

							}//foreach($arrcodmedicamentos as $codpalclave => $nomclave)
						}//if ($codarticulo == "")
						if ($codArtInterno != $codarticulo){
							$codArtInterno = $codarticulo;
						}
						if ($encontrado == 0) {
							$cantart = sumarMedicamentosMipres($codarticulo, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
							//19 de junio 2019
							if ($cantart == 0){//ver si esta en central de mezclas
								$cantart = sumarMedicamentosxcentralmezclas($codarticulo, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
							}//if ($cantart == 0){//ver si esta en central de mezclas

							if ($cantart > 0) {
								//27 de junio 2019 $valormed = valorTarifaMedicamento($codarticulo, $ingtar, $fechaMipres); //
								$valormed = valorcobradoMedicamento($codarticulo, $histxmedi, $ingxhist, $ingtar, $fechaMipres);
								$wherestr = "Artcod = '".$codarticulo."' ";
								$vdescripcionInterna = select1campoquery("Artgen", $wbasedato."_000026", $wherestr) . "/X $codarticulo"; //nombre generico


								$codservxmipres = select1campoquery("Artcum", $wbasedato."_000026", $wherestr); //nombre generico

							}

						}


						break;
					case "P"://Procedimientos
						if ($codcum == "") { //aqui es el cups.
							$wherestr = "Pronop = '".$numprescripcion."' AND  Procop = '".$vconsecutivo. "' ";

							//$codcum = select1campoquery("Procup", $wbasedatomipres."_000004", $wherestr);

							$arrFila = select1filaquery("Procup", $wbasedatomipres."_000004", $wherestr);
							if ( count($arrFila) > 0){
								$codcum = $arrFila['Procup'];

							}


							$codarticulo = $codcum;
						}
						$codservxmipres = $codcum;
						$arrprodecimientos = valorycantidadprocedimiento($codcum, $histxmedi, $ingxhist, $ingtar, $fechaMipres);
						//$queryvalores = $codcum. " ".$histxmedi." ".$ingxhist;
						if (count($arrprodecimientos) > 0) {
							$cadxdebug = " encontrado proced $codcum ";

							$cantart = $arrprodecimientos[0]['Tcarcan']; //cantidad
							$valormed = $arrprodecimientos[0]['Tcarvun']; //valor unitario
							$fechacargo  = $arrprodecimientos[0]['Tcarfec'];
							if ($arrprodecimientos[0]['Tcarvre'] > $arrprodecimientos[0]['Tcarvun']) { //si el valor con recargo es superior
								$valormed = $arrprodecimientos[0]['Tcarvre'];
							}
							$vdescripcionInterna =  $arrprodecimientos[0]['Tcarpronom'];//nombre del procedimiento

						}
						if ($codArtInterno != $codarticulo){
							$codArtInterno = $codarticulo;
						}


						break;
						case "N"://nutriciones
						//$codservxmipres hay que poner el registro invima
							$encontrado = 0;
							$codarticulo = "";
							$wherestr = "Nutnop='".$numprescripcion."' AND Nutcon = '".$vconsecutivo."' ";

							$arrFila = select1filaquery("Nutdpn", $wbasedatomipres."_000006", $wherestr);
							if ( count($arrFila) > 0){
								$codarticulo = $arrFila['Nutdpn'];
							}





							if ( ( $encontrado == 0 ) &&  ($codarticulo != "") ) {
								$wherestr = "Lpncod='".$codarticulo."' ";
								$vdescripcionmipres = select1campoquery( "Lpnnom", "mipres_000030", $wherestr );//5 julio 2019 multiempresa


								if ($vdescripcionmipres != ""){

									$encontrado = 0;
									$caddescripcion = str_replace(" ","][","[".$vdescripcionmipres."]");//simular palabaras claves entre []
									$arrcodmedicamentos = palabraclavenombre($caddescripcion, "" ,"Artcom" ); //$vdosis);
									foreach($arrcodmedicamentos as $codpalclave => $nomclave)
									{
									//	echo $codpalclave . " nombre " . $nomclave;
										if ($encontrado == 0) {
											$cantart = sumarMedicamentosMipres($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
											if ($cantart > 0) {
												//$codarticulo = $codpalclave;
												// 27 junio 2019 , se usa el valor cobrado y no el de la tarifa $valormed = valorTarifaMedicamento($codpalclave, $ingtar, $fechaMipres);
												$valormed = valorcobradoMedicamento($codpalclave, $histxmedi, $ingxhist, $ingtar, $fechaMipres);

												if ($valormed != 0) {
													$encontrado = 1;
													$codArtInterno = $codpalclave;
													$vdescripcionInterna = $nomclave;

													if ($codservxmipres == ""){
														$wherestr = "Artcod = '".$codpalclave. "' ";
														$codservxmipres = select1campoquery("Artcum", $wbasedato."_000026", $wherestr);



													}
												}

											}//if ($cantart > 0) {


										}//if ($encontrado == 0)

									}//foreach($arrcodmedicamentos as $codpalclave => $nomclave)
								}//if ($vdescripcionmipres != "")
							}//if ($codarticulo != "")

							break;


					case "D":
						$wherestr = "Disnop='".$numprescripcion."' AND Discod = '".$vconsecutivo."' ";
						$arrFila = select1filaquery("Discdi", $wbasedatomipres."_000005", $wherestr);
						if ( count($arrFila) > 0){
							$codarticulo = $arrFila['Discdi'];
						}

							if ($codarticulo != ""){

								$wherestr = "Tdmcod = '".$codarticulo."' ";
								$vdescripcionmipres = select1campoquery("Tdmdes", "mipres_000026", $wherestr);//5 julio 2019 multiempresa

								if ($vdescripcionmipres != ""){

									$encontrado = 0;
									$caddescripcion = str_replace(" ","][","[".$vdescripcionmipres."]");//simular palabaras claves entre []
									$arrcodmedicamentos = palabraclavenombre($caddescripcion, "" ,"Artcom" ); //$vdosis);
									foreach($arrcodmedicamentos as $codpalclave => $nomclave)
									{
									//	echo $codpalclave . " nombre " . $nomclave;
										if ($encontrado == 0) {
											$cantart = sumarMedicamentosMipres($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
											if ($cantart > 0) {
												//$codarticulo = $codpalclave;
												//27 de junio 2019 ,se usa el valor cobrado y no el de la tarifa $valormed = valorTarifaMedicamento($codpalclave, $ingtar, $fechaMipres);
												$valormed = valorcobradoMedicamento($codpalclave, $histxmedi, $ingxhist, $ingtar, $fechaMipres);

												if ($valormed != 0) {
													$encontrado = 1;
													$codArtInterno = $codpalclave;
													$vdescripcionInterna = $nomclave;

													if ($codservxmipres == ""){
														$wherestr = "Artcod = '".$codpalclave. "' ";
														$codservxmipres = select1campoquery("Artcum", $wbasedato."_000026", $wherestr);
													}
												}

											}//if ($cantart > 0) {


										}//if ($encontrado == 0)

									}//foreach($arrcodmedicamentos as $codpalclave => $nomclave)
								}//if ($vdescripcionmipres != "")
							}//if ($codarticulo != "")




						break;

					case "S"://Servicios complementarios



							$wherestr = "Sernop='".$numprescripcion."' AND Sercos = '".$vconsecutivo."' ";
							//$codarticulo = select1campoquery("Sercsc", $wbasedatomipres."_000007", $wherestr);

							$arrFila = select1filaquery("Sercsc", $wbasedatomipres."_000007", $wherestr);
							if ( count($arrFila) > 0){
								$codarticulo = $arrFila['Sercsc'];
							}

							if ($codarticulo != ""){

								$wherestr = "Tsccod='".$codarticulo."' ";
								$vdescripcionmipres = select1campoquery("Tscdes", "mipres_000027", $wherestr);//5 julio 2019 ,multiempresa

								if ($vdescripcionmipres != ""){

									$encontrado = 0;
									$caddescripcion = str_replace(" ","][","[".$vdescripcionmipres."]");//simular palabaras claves entre []
									$arrcodmedicamentos = palabraclavenombre($caddescripcion, "" ,"Artcom" ); //$vdosis);
									foreach($arrcodmedicamentos as $codpalclave => $nomclave)
									{
									//	echo $codpalclave . " nombre " . $nomclave;
										if ($encontrado == 0) {
											$cantart = sumarMedicamentosMipres($codpalclave, $histxmedi, $ingxhist , $fechamaximaentrega); // , );//
											if ($cantart > 0) {
												//$codarticulo = $codpalclave;
												//27 de junio 2019 $valormed = valorTarifaMedicamento($codpalclave, $ingtar, $fechaMipres);
												$valormed = valorcobradoMedicamento($codpalclave, $histxmedi, $ingxhist, $ingtar, $fechaMipres);

												if ($valormed != 0) {
													$encontrado = 1;
													$codArtInterno = $codpalclave;
													$vdescripcionInterna = $nomclave;

													if ($codservxmipres == ""){
														$wherestr = "Artcod = '".$codpalclave. "' ";
														$codservxmipres = select1campoquery("Artcum", $wbasedato."_000026", $wherestr);
													}
												}

											}//if ($cantart > 0) {


										}//if ($encontrado == 0)

									}//foreach($arrcodmedicamentos as $codpalclave => $nomclave)
								}//if ($vdescripcionmipres != "")
							}//if ($codarticulo != "")




						break;


					default:
						break;
				}//switch ($vtipotec)

			}//if ($encontrado == 0) {//se busco por codigo de la tecnologia y no se encontro
			//switch ($vtipotec)



			$arrresult[0]["CantidadOriginal"] = $cantart;//la cantidad pudo haber sido modificada por el usuario
			if ($cantmodificada != 0){
				if ($cantart != $cantmodificada ){
					$cantart = $cantmodificada;
				}
			}

			$arrresult[0]["CodigoOriginal"] = $codservxmipres;//el codigo del mipres ha sido modificado por el usuario
			if ($codmodificado != ""){
				if ($codmodificado != $codservxmipres){
						$codservxmipres = $codmodificado;
				}
			}
			$arrresult[0]["ValorOriginal"] = $valormed;
			if ( $vlrModificado != 0){
				$valormed = $vlrModificado;

			}


			$arrresult[0]["Cum"] = $codcum;//cum, cups ,
			$arrresult[0]["CodArticulo"] = $codarticulo;//codigo que trae el mipres.
			$arrresult[0]["CodInterno"] = $codArtInterno;//codigo en la bd matrix
			$arrresult[0]["Cantidad"] = $cantart;
			$arrresult[0]["Valor"] = $valormed;
			$arrresult[0]["DescInterna"] = $vdescripcionInterna;
			$arrresult[0]["CodDeEntrega"] = $codservxmipres;// es el codigo que se usara para realizar el mipres de entrega
			$arrresult[0]["ID"] = $vID;// ID del detalle del mipre
			$arrresult[0]["FechaCargo"] = $fechacargo;
			$arrresult[0]["CausaNoEntrega"] = $causanoentrega;
			$arrresult[0]["CodTenologiaPrescripcion"] = $vcodtecnoligiaprescripcion;//26 junio 2019 , visualizar el codigo de la tecnologia solicitado.


		}//if ($ingtar != "")





		return $arrresult;

	}//infocantidadvlrycodigomipres($numprescripcion , $vconsecutivo , $fechaMipres , $coddemipres , $histxmedi, $ingxhist, $ingtar , $vtipotec )




	//Funcion generica para actualizar campos en un registro de la base de datos.
	//Solo actualiza un registro , si encuentra mas no actualiza nada.
	function update1registro ($tabla, $setcampos , $where){
		global $conex;
		$q = " SELECT count(*) as cuenta ";
		$q .= "   FROM  $tabla ";
		$q .= "   WHERE  ".$where." ";

		$resultado = 0;
		$resquery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numElementos = mysql_num_rows($resquery);

		$rowsquery = mysql_fetch_array($resquery);



		if( $rowsquery['cuenta'] == 1 )//$numElementos
		{
			$queryUp = " UPDATE $tabla ";
			$queryUp .= "  SET " . $setcampos . " ";
			$queryUp .= "  WHERE ".$where." ";



			$resquery = mysql_query($queryUp, $conex) or die("Error: ".mysql_errno()." - en el query: ".$queryUp." - ".mysql_error());
			$resultado = 1;//1 registro actualizado .
		}
		return $resultado ;


	}//function update1registro ($tabla, $setcampos , $where)



//Genera comandos sql para insertar o actualizar un registro en la base de datos usando como entrada
//un arreglo Json .

//Genera comandos sql para insertar o actualizar un registro en la base de datos usando como entrada
//un arreglo Json .
//{"ID":26603276,"IDProgramacion":21169061,"NoPrescripcion":"20200403229001055205","TipoTec":"M","ConTec":1,"TipoIDPaciente":"CC","NoIDPaciente":"1026141460","NoEntrega":1,"FecMaxEnt":"2020-05-06","TipoIDSedeProv":"NI","NoIDSedeProv":"800149026","CodSedeProv":"PROV000810","CodSerTecAEntregar":"21535-1","CantTotAEntregar":"2","FecProgramacion":"2020-05-26 16:45","EstProgramacion":1,"FecAnulacion":null}

//INSERT INTO mipres_000037 (Sumnop , Sumcon , ...) values ( "20200403229001055205" , 1 , ...)
//UPDATE 
function generarCmdsSqlXInsertUpdateXJson (  $arrJson  ){




	$setcampos = "";
	$vsufijocampojson = "";
	$vestadomipres = "";//Direccionamiento D , Programacion P , Entrega E , Reporte de Entrega R
	//$vjsonatxt = json_encode($arrJson);//arreglo json a texto
	$keyID = "";
	$key2 = "";
	$sepkey2 = "";

	$insertcampos = "";

	$updatesql = "";
	$insertvalores = "";
	$arrxdatabase = array();
	$strkeyID = "";
	$strkey2 = "";

	$cadres = "";


	$arrresult["IDkey"] = "" ;//en las claves del arreglo quedaran los resultados de los comandos sql.
	$arrresult["key2"] = "" ;
	$arrresult["insertcampos"] = "";
	$arrresult["insertvalores"] = "";
	$arrresult["updatesql"] = "";


	$arrresult["key2Mp1"] = "" ;
	$arrresult["insertcamposMp1"] = "";
	$arrresult["insertvaloresMp1"] = "";
	$arrresult["CodtecnologiaMp1"] = "";
	$arrresult["CanttecnologiaMP1"] = "";
	$arrresult["TipotecnologiaMP1"] = "";
	$arrresult["FechaMaxEntrega"] = "";
	$arrresult["FechaDireccionamiento"] = "";
//11 Diciembre 2019 Freddy Saenz
	$arrxdatabaseMp1 = array();
	$arrxdatabaseMp1["IDkey"] = "" ;//en las claves del arreglo quedaran los resultados de los comandos sql.
	$arrxdatabaseMp1["key2"] = "" ;
	$arrxdatabaseMp1["insertcampos"] = "";
	$arrxdatabaseMp1["insertvalores"] = "";
	$arrxdatabaseMp1["updatesql"] = "";

	$vcodtecnologiaDir = "";
	$canttecnologiaDir = 0;
	$tipotecnologiaDir = "";

	$updatesqlMp1 = "";
	$insertvaloresMp1 = "";
	$strkeyIDMp1 = "";
	$strkey2Mp2 = "";


	$numprescripcion = "";
	$valueIDmipres = "";

	//No actualizar el ID o dejarlo en cero en caso de anulacion.
	// 8 de Noviembre 2019 Freddy Saenz
	$vestadoanuladoprogramacion     = -1;
	$vestadoanuladodireccionamiento = -1;
	$vestadoanuladoreporteentrega   = -1;
	$vestadoanuladoentrega          = -1;
	$vestadoanuladofacturacion      = -1;
	
	$arregloxtecnologia = array();
	$arregloxtecnologia["Prescripcion"] = "";//numero de la prescripcion
	$arregloxtecnologia["Tipo"] = "";//tipo de prescripcion
	$arregloxtecnologia["Consecutivo"] = "";//consecutivo de la tecnologia , es este caso del medicamento
	$arregloxtecnologia["Entrega"] = "";//numero de entrega.
	$arregloxtecnologia["Tecnologia"] = "";//tecnologia
	$arregloxtecnologia["CantidadTecnologia"] = "0";//tecnologia
	$arregloxtecnologia["Identificacion"] = "";//Cedula del paciente
	$arregloxtecnologia["FechaMaxEntrega"] = "";
	$arregloxtecnologia["FechaDireccionamiento"] = "";



	foreach( $arrJson as $key1 => $value1)

	{
		

		$value = $value1;
		
		if ( ( is_integer( $value1) ) || ( is_numeric( $value1) ) ) 
		//if ($value1 == 0 )
		{
			
		}else if ( $value === null ){//quitar los null , pero no los ceros
			$value = "";
			if ( $value1 === 0 ){
				$value = 0;
			}
		}else if( $value == "null" ) {
			$value = "";
		}


		if  ( is_string($value) ) {
			if ( $value != "" ) {//poner comillas si tiene valor
				$value = "'"  . $value . "'" ;//si es un string agregarle las comillas sencillas.
			}

		}
		$key = $key1;

		$campoIDx = "";



//		if ( ($value != "") && ( $value != "null" ) ){//si tiene valor
		if  ( !( $value === "" ) )  {//si tiene valor , estaba filtrando los ceros validos.

			$cadres .= " $key = $value ";
			$cadres2 = " $key1 = $value1 ";



			switch($key1){
				//clave principal del" . $wbasedatomipres . " de suministro
				case 'SumID':
				case 'ID':
					if (intval($value1) != 0 ){
						$valueIDmipres = $value1;
						if ( is_string($valueIDmipres) ){
							$valueIDmipres = "'"  . $valueIDmipres . "'" ;//si es un string agregarle las comillas sencillas.
						}
						$arrxdatabase = creararregloinsertmipressuministro ( "SumID" , $valueIDmipres, $insertcampos , $insertvalores , $updatesql , $valueIDmipres , "" , $strkeyID, $strkey2  );


					}


					//SumID = $value;
					//SumID =  879234,

				
					break;

				case 'Sumnop':
				case  "NoPrescripcion":
					$numprescripcion = $value;

					$arrxdatabase = creararregloinsertmipressuministro ( "Sumnop" , $value , $insertcampos , $insertvalores , $updatesql , "" , $value , $strkeyID, $strkey2  );
					//Sumnop = $value;
$arrxdatabaseMp1 = creararregloinsertmipressuministro ("Prenop" , $value , $insertcamposMp1 , $insertvaloresMp1 , $updatesqlMp1 , "" , $value, "" , $strkey2Mp2  );
$arregloxtecnologia["Prescripcion"] = $value;//numero de la prescripcion
				
					break;
// "20190318142010959863",

				case 'Sumtip':
				case  "TipoTec":
					$tipotecnologiaDir = $value;
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumtip" , $value , $insertcampos , $insertvalores , $updatesql , "" , $value , $strkeyID, $strkey2  );
					//= $value;
$arregloxtecnologia["Tipo"] = $value;//tipo de prescripcion
				
					break;
// "M",

				case 'Sumcon':
				case  "ConTec":
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumcon" , $value , $insertcampos , $insertvalores , $updatesql , "" , $value, $strkeyID, $strkey2  );
					//= $value;
$arregloxtecnologia["Consecutivo"] = $value;//consecutivo de la tecnologia , es este caso del medicamento

				
					break;
//1,

				case 'Sumnen':	//Numero de entrega
				case  "NoEntrega"://3 julio 2019 , hace parte de la llave
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumnen" , $value , $insertcampos , $insertvalores , $updatesql , "" , $value, $strkeyID, $strkey2  );
					//Sumnen = $value;
$arregloxtecnologia["Entrega"] = $value;//numero de entrega.				
					break;
// 1,

				case 'Sumtid':
				case  "TipoIDPaciente":
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumtid" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					//= $value;
				
$arrxdatabaseMp1 = creararregloinsertmipressuministro ("Pretip" , $value , $insertcamposMp1 , $insertvaloresMp1 , $updatesqlMp1 , "" , "" , "" , $strkey2Mp2  );
					break;
// "CC",

				case 'Sumnid':
				case  "NoIDPaciente":
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumnid" , $value , $insertcampos , $insertvalores , $updatesql , "" , "", $strkeyID, $strkey2  );
					//Sumnid = $value;
$arregloxtecnologia["Identificacion"] = $value;//Cedula del paciente			
$arrxdatabaseMp1 = creararregloinsertmipressuministro ("Preidp" , $value , $insertcamposMp1 , $insertvaloresMp1 , $updatesqlMp1 , "" , "" , "" , $strkey2Mp2  );
					break;
// "70285734",

	//fin clave principal" . $wbasedatomipres . " de suministro

				case 'Sumnse':
				case  "NoSubEntrega": //NO hace parte de la clave
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumnse" , $value , $insertcampos , $insertvalores , $updatesql , "" , "", $strkeyID, $strkey2  );
					//= $value;
				
					break;
// 1,

				case 'SumIDd':
				case  "IDDireccionamiento":
					$arrxdatabase = creararregloinsertmipressuministro ( "SumIDd" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					$campoIDx = "Sumjdi";//para grabar el Json de direccionamiento
					//SumIDd = $value;
				
					break;
// 1192243,

				case 'SumIDe':
				case  "IDEntrega":
					$arrxdatabase = creararregloinsertmipressuministro ( "SumIDe" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					$campoIDx = "Sumjen";//para grabar el Json de Entrega
					//SumIDe = $value;
				
					break;
// 119287,

				case 'SumIDp':
				case  "IDProgramacion":
					$arrxdatabase = creararregloinsertmipressuministro ( "SumIDp" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					$campoIDx = "Sumjpr";//para grabar el Json de Programacion
					//SumIDp = $value;
				
					break;
// 631393,

				case 'SumIDr':
				case  "IDReporteEntrega":
					$arrxdatabase = creararregloinsertmipressuministro ( "SumIDr" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					$campoIDx = "Sumjre";//para grabar el json de Reporte de Entrega
					//SumIDr = $value;
				
					break;
// 100458,


				case 'SumIDf':
				case  "IDFacturacion":
					$arrxdatabase = creararregloinsertmipressuministro ( "SumIDf" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					$campoIDx = "Sumjfa";//para grabar el json de Facturacion
					//SumIDr = $value;
				
					break;
// 100458,

				case  "ID2"://deberia aparecer como ID , pero como se usan 2 IDs reemplazaria al ID de Suministros
					$arrxdatabase = creararregloinsertmipressuministro ( "SumID2" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
				
					break;

					
					
				case 'Sumcan'://1 grabar en la base de datos
				case  "CantTotAEntregar":
					$canttecnologiaDir = $value;
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumcan" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					// $value;
					$arregloxtecnologia["CantidadTecnologia"] = $value;//cantidad de tecnologia

					if ($key1 == "CantTotAEntregar"){


						$strkeyID = $arrxdatabase["IDkey"];
						$strkey2 = $arrxdatabase["key2"];
						$insertcampos = $arrxdatabase["insertcampos"];
						$insertvalores = $arrxdatabase["insertvalores"];
						$updatesql = $arrxdatabase["updatesql"];
						$arrxdatabase = creararregloinsertmipressuministro ( "Sumcat" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					}
				
					break;
// "1",


				case  "CantTotEntregada"://29 mayo 2019
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumcan" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );

					//Sumcan = $value;
					if ($key1 == "CantTotEntregada"){

						$strkeyID = $arrxdatabase["IDkey"];
						$strkey2 = $arrxdatabase["key2"];
						$insertcampos = $arrxdatabase["insertcampos"];
						$insertvalores = $arrxdatabase["insertvalores"];
						$updatesql = $arrxdatabase["updatesql"];
						$arrxdatabase = creararregloinsertmipressuministro ( "Sumcat" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					}
				
					break;
// "string",
				case "Sumcat":
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumcat" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
				
					break;

				case 'Sumcne':
				case  "CausaNoEntrega":
					if ( $value != "null")  {
						$arrxdatabase = creararregloinsertmipressuministro ( "Sumcne" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );

					}
					//= $value;
				
					break;
// null,
				case  "CodEPS":
					//= $value;
				
$arrxdatabaseMp1 = creararregloinsertmipressuministro ("Preeps" , $value , $insertcamposMp1 , $insertvaloresMp1 , $updatesqlMp1 , "" , "" , "" , $strkey2Mp2  );
					break;
// "EPS037",
				case  "CodMunEnt":
					//= $value;
				
					break;
//"05079",
				case  "CodSedeProv":
					//= $value;
				
					break;
//"PROV000807",

				case 'Sumcst':
				case  "CodSerTecAEntregar":
					$vcodtecnologiaDir = $value;
					
					$arregloxtecnologia["Tecnologia"] = $value;

				case  "CodTecEntregado":
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumcst" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					//Sumcst = $value;
				
					break;
// "020076236-20",

				case  "DirPaciente":
					//= $value;
				
					break;
// "CL 12   15 19",
				case  "EntTotal":
					//= $value;
					$arrxdatabase = creararregloinsertmipressuministro ( "Sument" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );

				
					break;
// 1,

					
				case 'Sumeen':
				case  "EstadoEntrega"://0 ANULADO 1 ACTIVO 2 FINALIZADO
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumeen" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					//Sumese = $value;
					$vestadoanuladoentrega = $value;
				
					break;
//1,

				case 'Sumedi':
				case  "EstDireccionamiento"://0 ANULADO 1 ACTIVO 2 FINALIZADO

					$arrxdatabase = creararregloinsertmipressuministro ( "Sumedi" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					//= $value;

					$vestadoanuladodireccionamiento = $value;

					
				
					break;
// 1,

			//	case 'Sumeen':
				case  "EstEntrega"://0 ANULADO 1 ACTIVO 2 FINALIZADO
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumeen" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					//Sumese = $value;
					$vestadoanuladoentrega = $value;
				
					break;
// 2,

				case 'Sumepr':
				case  "EstProgramacion"://0 ANULADO 1 ACTIVO 2 FINALIZADO
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumepr" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					//= $value;

					$vestadoanuladoprogramacion     = $value;

				
					break;
// 2,

				case 'Sumere':
				case  "EstRepEntrega"://0 ANULADO 1 ACTIVO 2 FINALIZADO
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumere" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					//= $value;
						
					$vestadoanuladoreporteentrega   = $value;
				
					break;
// 2,
				case  "FecAnulacion":
					//= $value;
				
					break;
// null
				case  "FecDireccionamiento":
					//= $value;
					$arregloxtecnologia["FechaDireccionamiento"] = $value ;				
					break;
// "2019-04-01 18:17",
				case "Sumfen":
				case  "FecEntrega"://viene fecha y hora
				//date($format, $value);
					//$vsolofecha = date("Y-m-d", $value);//12 julio 2019 , ya no funciona con la hora , se debe eliminar de la fecha
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumfen" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );

					//= $value;
				
					break;
//"2019-03-18 00:00",

				case 'Sumfmx':
				case  "FecMaxEnt"://FecMaxEnt , Sumfmx
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumfmx" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
					//Sumfmx = $value;
					$arregloxtecnologia["FechaMaxEntrega"] = $value;//$tipotecnologiaDir ;

				
					break;
// "2019-04-19",
				case  "FecProgramacion":
					//= $value;
				
					break;
// "2019-04-02 18:15",
				case  "FecRepEntrega"://En la fecha del reporte de entrega cerrar el" . $wbasedatomipres . " .
					//$vsolofecha = date("Y-m-d", $value);//12 julio 2019 , ya no funciona con la hora , se debe eliminar de la fecha
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumfem" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );

					//= $value;
				
					break;
// "2019-04-02 18:23",

				case  "NoIDEPS":
					//= $value;
				
					break;
//"900156264",

				case  "NoIDProv":
					//= $value;
				
$arrxdatabaseMp1 = creararregloinsertmipressuministro ("Preidi" , $value , $insertcamposMp1 , $insertvaloresMp1 , $updatesqlMp1 , "" , "" , "" , $strkey2Mp2  );
					break;
// "800067065",
				case  "NoIDSedeProv":
					//= $value;
				
					break;
// "800067065",
				case  "NoLote":
					//= $value;
				
					break;
// "string",



				case  "TipoIDProv":
					//= $value;
				
$arrxdatabaseMp1 = creararregloinsertmipressuministro ("Precii" , $value , $insertcamposMp1 , $insertvaloresMp1 , $updatesqlMp1 , "" , "" , "" , $strkey2Mp2  );
					break;
// "NI",
				case  "TipoIDSedeProv":
					//= $value;
				
					break;
// "NI",

				case "Sumven":
				case  "ValorEntregado":
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumven" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );

					//= $value;
				
					break;
// 25917,
//24 de Octubre , Freddy Saenz nuevos campos usadoos en el" . $wbasedatomipres . " de Facturacion
				case  "NoFactura"://No hace parte de la clave principal (ID) ni de la secundaria ( x prescricion )
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumnfa" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
				
					break;

				case  "Copago"://Copago No hace parte de la clave principal (ID) ni de la secundaria ( x prescricion )
					$vlrfloat  = floatval ( $value ); 
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumvco" , $vlrfloat , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
				
					break;

					
				case  "CuotaModer"://Cuota Moderadora No hace parte de la clave principal (ID) ni de la secundaria ( x prescricion )
					$vlrfloat  = floatval ( $value ); 
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumvcm" , $vlrfloat , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );
				
					break;

					
				case "EstFacturacion"://estado de facturacion.//0 ANULADO 1 ACTIVO 2 FINALIZADO
					$arrxdatabase = creararregloinsertmipressuministro ( "Sumefa" , $value , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );

					$vestadoanuladofacturacion      = $value;
				
					break;


			}//switch($value)



		}//	if ($value != "")

		$vhayanulado = $vestadoanuladoprogramacion;
		$vhayanulado *= $vestadoanuladodireccionamiento;
		$vhayanulado *= $vestadoanuladoreporteentrega;
		$vhayanulado *= $vestadoanuladoentrega;
		$vhayanulado *= $vestadoanuladofacturacion;
		
		if ( $vhayanulado == 0 ){
			//hay almenos uno anulado 
//enviar un arreglo en blanco


		}
	
			
		$strkeyID = $arrxdatabase["IDkey"];
		$strkey2 = $arrxdatabase["key2"];
		$insertcampos = $arrxdatabase["insertcampos"];
		$insertvalores = $arrxdatabase["insertvalores"];
		$updatesql = $arrxdatabase["updatesql"];

		//mipres_1
	$strkey2Mp2       = $arrxdatabaseMp1["key2"]   ;
	$updatesqlMp1     = $arrxdatabaseMp1["updatesql"]  ;
	$insertcamposMp1  = $arrxdatabaseMp1["insertcampos"] ; 
	$insertvaloresMp1 = $arrxdatabaseMp1["insertvalores"]   ;



		if ( $campoIDx  != ""){//agregar el campo del json
			$varjson = "'" . json_encode ($arrJson) . "' ";//json a texto, con comillas sencillas
			//$value1 porque no debe tener comillas sencillas
			$arrxdatabase = creararregloinsertmipressuministro ( $campoIDx , $varjson , $insertcampos , $insertvalores , $updatesql , "" , "" , $strkeyID, $strkey2  );

			$strkeyID = $arrxdatabase["IDkey"];
			$strkey2 = $arrxdatabase["key2"];
			$insertcampos = $arrxdatabase["insertcampos"];
			$insertvalores = $arrxdatabase["insertvalores"];
			$updatesql = $arrxdatabase["updatesql"];

		}//if ( $campoIDx = != ""){//agregar el campo del json


	}//foreach($arrJson as $key => $value)

	$vinsertmp1 = $arrxdatabaseMp1["insertcampos"];
	$vkeytmp1   = $arrxdatabaseMp1["key2"];



//campos necesarios para grabar en la tabla" . $wbasedatomipres . "_000001 si fuera necesario
	$arrxdatabase["key2Mp1"]           = $arrxdatabaseMp1["key2"] ;
	$arrxdatabase["updatesqlMp1"]      = $arrxdatabaseMp1["updatesql"] ;
	$arrxdatabase["insertcamposMp1"]   = $arrxdatabaseMp1["insertcampos"] ;
	$arrxdatabase["insertvaloresMp1"]  = $arrxdatabaseMp1["insertvalores"] ;
	$arrxdatabase["CodtecnologiaMp1"]  = $arregloxtecnologia["Tecnologia"]; //$vcodtecnologiaDir  ;
	$arrxdatabase["CanttecnologiaMP1"] = $arregloxtecnologia["CantidadTecnologia"] ;//$canttecnologiaDir ;
	$arrxdatabase["TipotecnologiaMP1"] = $arregloxtecnologia["Tipo"] ;//$tipotecnologiaDir ;
	$arrxdatabase["Prescripcion"]      = $arregloxtecnologia["Prescripcion"] ;//$tipotecnologiaDir ;
	$arrxdatabase["Consecutivo"]       = $arregloxtecnologia["Consecutivo"] ;//$tipotecnologiaDir ;
	$arrxdatabase["Entrega"]           = $arregloxtecnologia["Entrega"] ;//$tipotecnologiaDir ;
	$arrxdatabase["Identificacion"]    = $arregloxtecnologia["Identificacion"];
	$arrxdatabase["FechaMaxEntrega"]        = $arregloxtecnologia["FechaMaxEntrega"] ;//$tipotecnologiaDir ;
	$arrxdatabase["FechaDireccionamiento"]  = $arregloxtecnologia["FechaDireccionamiento"];






	if ($numprescripcion  == $valueIDmipres ){//esta agregando el numero de la prescripcion al campo ID
		return $arrxdatabase;

	}

	if ($valueIDmipres != ""){
	//	$arrxdatabase = creararregloinsertmipressuministro ( "SumID" , $valueIDmipres, $insertcampos , $insertvalores , $updatesql , $valueIDmipres , "" , $strkeyID, $strkey2  );

	}




	return $arrxdatabase;


}//function generarCmdsSqlXInsertUpdateXJson ( $vID , $arrJson)


//basado en un numero de prescripcion , extrae la fecha de la prescripcion.
//ej : 20190919117014495111 se obtiene 2019-09-19 
function ffechadenummeromipres( $prescripcionIn ) {

	$fechastr = "";
	$prescripcion = str_replace("'" , "" , $prescripcionIn);//la comilla sencilla desajusta el algoritmo
	for ( $i=0; $i<strlen($prescripcion) ; $i++ ){

		if ( $i < 8 ){
			$fechastr .= $prescripcion[ $i ] ;
			if (( $i==3 )|| ( $i==5 )){
				$fechastr .= "-";
			}			
		}



	}
	return $fechastr;


}

function crearRegistrosMp1XJson ( $txt ){//crear los registros en la tabla mipres_1 y mipres_2
	//basados en el Json de direccionamiento si no estan creados

	global $wbasedatomipres;
	global $conex;
	global $wbasedatocliame  ;
	global $wmodoambulatorio ;
	
	$numregistroscreados  = 0;


	$var1 = $txt;//json_decode( $txt , true ) ;

	$arrresult =  generarCmdsSqlXInsertUpdateXJson( $var1 );//con el Json generar comandos sql para insert o update  para la tabal de mipres_1

	$identificacion  = $arrresult["Identificacion"];
	$prescripcion    = $arrresult["Prescripcion"];
	$codtecnologia   = $arrresult["CodtecnologiaMp1"];
	$canttecnologia  = $arrresult["CanttecnologiaMP1"];
	$tipotecnologia  = $arrresult["TipotecnologiaMP1"];
	$consecutivo     = $arrresult["Consecutivo"];
	$numentrega      = $arrresult["Entrega"];//falta usar este campo.
	$fechamaxentrega = $arrresult["FechaMaxEntrega"]; 
	$fechadireccionamiento = $arrresult["FechaDireccionamiento"]; 

	$fechaprescripcion = ffechadenummeromipres( $prescripcion );//basado en el numero de la prescripcion obtener la fecha de la prescripcion (devuelve en string)
	
    $fechadireccionamiento = str_replace("'" , "" , $fechadireccionamiento);//OJO con la comilla sencilla , retorna fecha 1970
    $fechamaxentrega       = str_replace("'" , "" , $fechamaxentrega);//OJO con la comilla sencilla , retorna fecha 1970
    $tipotecnologia = str_replace("'" , "" , $tipotecnologia);
    $fechaprescripcion = str_replace("'" , "" , $fechaprescripcion);



	$fecha1 = date("Y-m-d H:s",strtotime($fechadireccionamiento."- 15 days"));//buscar desde 15 dias antes de la fecha de direccionamiento
//importante el formato con Horas y segundos, porque puede devolver fechas de 1970


	$arrfilasprescripcion = select1filaquery("Prenop", $wbasedatomipres . "_000001", "Prenop = $prescripcion ");//Ver si el registro en mipres_1 existe.
	//si no hay registro en la tabla mipres_1 crearlo basado en el Json leido
	if ( count( $arrfilasprescripcion ) == 0 ) {//No existe el registro , hay que crear el registro

		//crear los campos del registro en la tabla mipres_1 , basados en el Json leido
		$campos = "  Medico, Fecha_data, Hora_data , Preest , Prefep , Prehop , " . $arrresult["insertcamposMp1"];
		$campos .= " , Seguridad ";//19 dic 2019 campo de seguridad

		$valores = " '" . $wbasedatomipres ."'  , '" . date("Y-m-d") . "' , '".date("H:i:s") . "' , 'on' ";
		$valores .= " , '" . $fechaprescripcion . "' , '"."01:01:01". "' , ". $arrresult["insertvaloresMp1"];
		$valores .= " , '" . "C-$wbasedatomipres". "xDir' ";//19 dic valor campo de seguridad

//		$valores .= " , '" . date("Y-m-d", strtotime( $fechaprescripcion )."+0 days" ). "' , '".date("H:i:s") . "' , ". $arrresult["insertvaloresMp1"];
//date("Y-m-d", strtotime($fechaprescripcion) )


		$cmdselect =  "Pactdo , Pacdoc,Pacno1,Pacno2,Pacap1,Pacap2, Pachis , Pacsex ";//tipo de documento, documento , primer nombre , segundo nombre , primer apellido , segundo apellido.
		$tablaselectPac = $wbasedatocliame  . "_000100";//
		$where = " Pacdoc =  $identificacion  ";
		$arrfilas = select1filaquery( $cmdselect, $tablaselectPac, $where );
		if ( count( $arrfilas ) > 0 ) {//agregar la informacion del paciente , si existe

			//$campos .= " , Pretip  ,  Preidp  , Prepnp , Presnp , Prepap , Presap , Prehis ";
			$campos .= " , Prepnp , Presnp , Prepap , Presap , Prehis ";

			//$valores .= ", '" . $arrfilas["Pactdo"] . "' ";//tipo de documento, viene del json
			//$valores .= ", '" . $arrfilas["Pacdoc"] . "' ";//numero de documento , viene del json
			$valores .= ", '" . $arrfilas["Pacno1"] . "' ";//primer nombre del paciente
			$valores .= ", '" . $arrfilas["Pacno2"] . "' ";//segundo nombre del paciente
			$valores .= ", '" . $arrfilas["Pacap1"] . "' ";//primer apellido del paciente
			$valores .= ", '" . $arrfilas["Pacap2"] . "' ";//segundo apellido del paciente.
			$valores .= ", '" . $arrfilas["Pachis"] . "' ";//historia clinica del paciente
			//falta buscar el ingreso
			$tablaselectIngPac = $wbasedatocliame  . "_000101";//
			$where = " Inghis =  '" . $arrfilas["Pachis"] . "' ";
			$where .= " AND Ingfei >=  '" . $fecha1 . "' ";//buscar entre 15 dias antes del direccionamiento
			$where .= " AND Ingfei <=  '" . $fechamaxentrega . "' ";//y la fecha maxima de entrega

			if ( $wmodoambulatorio != 0 ){//aplica para el idc
				//para revisar este ordenamiento
				$where .= " ORDER BY Ingfei ASC , Ingnin ASC  ";//ordenar de menor a mayor por fecha de ingreso
			}else{
				$where .= " ORDER BY Ingfei DESC , Ingnin DESC  ";
			}

			$arrfilasIngreso = select1filaquery( "Inghis,Ingnin" , $tablaselectIngPac, $where );
			if ( count( $arrfilasIngreso ) > 0 ) {//buscar el numero de ingreso
				$campos  .= " , Preing ";
				$valores .= ", '" . $arrfilasIngreso["Ingnin"] . "' ";//numero del ingreso
			}

		}

		$cmdinsert = " INSERT INTO " . "" . $wbasedatomipres . "_000001 (" . $campos . ") VALUES ( " . $valores . " ) ";
		$resquery = mysql_query( $cmdinsert, $conex) or die("Error: ".mysql_errno()." - en el query: ".$cmdinsert." - ".mysql_error());
		$numregistroscreados++;



		switch ($tipotecnologia) {
			case 'M'://por ahora solo crear el registro en la tabla de medicamentos.
				# code...

			
$q1 = " select Mednop from " . $wbasedatomipres . "_000002" . " where Mednop = $prescripcion AND Medcom = $consecutivo";


				$arrfilastecnologia = select1filaquery("Mednop",  $wbasedatomipres . "_000002", "Mednop = $prescripcion AND Medcom = $consecutivo ");
				if ( count( $arrfilastecnologia ) == 0 ) {//agregar la tecnologia si no existe
					$camposTec   = "  Medico, Fecha_data, Hora_data , Medest ";
					$camposTec  .= " , Mednop, Medcom , Medctf , Meddmp ";//numero de prescripcion , consecutivo , cantidad y tecnologia (principio activo)

					$valoresTec  =  " '" . $wbasedatomipres . "'  , '".date("Y-m-d")."' , '".date("H:i:s") ."' , 'on' ";
					$valoresTec .=  ",   $prescripcion ";//prescripcion , OJO el json tiene la comilla sencilla .
					$valoresTec .=  ",  $consecutivo " ;//consecutivo
					$valoresTec .=  ",  $canttecnologia ";//cantidad
					$valoresTec .=  ",  $codtecnologia " ;//y tecnologia

					$camposTec  .= " , Seguridad ";//19 dic 2019 campo de seguridad
					$valoresTec .= " , '" . "C-$wbasedatomipres". "xDir' ";//19 dic valor campo de seguridad


					$cmdinsertTecn = " INSERT INTO " . "" . $wbasedatomipres . "_000002 (" . $camposTec . ") VALUES ( " .$valoresTec . " ) ";
					$resquery = mysql_query( $cmdinsertTecn, $conex) or die("Error: ".mysql_errno()." - en el query: ".$cmdinsertTecn." - ".mysql_error());
					$numregistroscreados++;

				}//if ( count( $arrfilastecnologia ) == 0 ) {//agregar la tecnologia si no existe

				break;
			
			default:
				# code...
				break;
		}


		//$cmdinserttec = 
		//CodtecnologiaMp1":"'228256-02'","CanttecnologiaMP1":"'1'","TipotecnologiaMP1":"'M'","Prescripcion":"'20190510100011893650'","Consecutivo":1,"Entrega":1}


	}


	return $numregistroscreados;


}//function crearRegistrosMp1XJson ( $txt ){//crear los registros en la tabla mipres_1 y mipres_2


//Genera el comando sql para hacer un insert el la tabla del mipres , basado en un arreglo que contiene los campos y valores
// que se van a insertar .
function insertupdatemipressuministro ( $arrinfo ){

	global $wbasedatomipres;
	global $conex;

	$insertvalores = $arrinfo["insertvalores"];
	$insertcampos = $arrinfo["insertcampos"];
	$updatesql = $arrinfo["updatesql"];
	$strkeyID = $arrinfo["IDkey"];
	$strkey2 = $arrinfo["key2"];

	$q = " INSERT INTO ".$wbasedatomipres."_000037 ( Medico , Fecha_data , Hora_data ";
	$q .=  " , " . $insertcampos . " ,Seguridad  ) ";
	$q .= " VALUES ('".$wbasedatomipres."','".date("Y-m-d")."','".date("H:i:s")."' " ;
	$q .=  " , " . $insertvalores . ",'C-".$wbasedatomipres."' ) ";

	$numup = 0;
	$where = "";
	if ($strkeyID != ""){
		$where  = $strkeyID;
	}elseif( $strkey2 != ""){
		$where  = $strkey2;
	}



	if ($where != ""){
		$setcampos = $updatesql;

		
		$numup = update1registro($wbasedatomipres."_000037", $setcampos, $where);
		if ($numup == 0){
			$resquery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numup = 1;//falta ver el resultado del query en numero de registros insertados.
		}
	}


	return $numup;

}


//Genera comandos sql para insertar o modificar registros basado en los campos de entrada
//$campo de la base de datos , $valor que se va asignar al campo en la base de datos , $insercampos si es un insert son los campos
// $insertvalores si es un insert son los valores , $updatesql comando sql formado por la dupla campo = valor
//$valorkeyID Si es el campo ID principal de la tabla , $valorkey2 , indica si el campo hace parte de la la clave secundaria
// $strkeyID es el comando sql de la clave ID
// $strkey2 el el comando sql de la clave secundaria .
function creararregloinsertmipressuministro ( $campo , $valor , $insertcampos , $insertvalores , $updatesql , $valorkeyID , $valorkey2 , $strkeyID, $strkey2  )
{

	$arrresult = array();
	$pos = strpos($insertcampos, " ". $campo . " ");//se agregan espacios antes y despues.
	if ($pos === false) // no se encontro
	{
	//	if ( $valor != "" ) {//si tiene informacion , no permite ceros.
		if (!( $valor === "" ) ){//si tiene informacion

			if ( $valorkeyID != "" ){
				if ( $strkeyID != "" ){
					 $strkeyID= $valor;
				}
			}
			if ( $valorkey2 != ""){
				if ( $strkey2 != "" ){
					$strkey2 .= " AND ";
				}
				$strkey2 .= " $campo = $valor ";//ir creando la clave secundaria de la forma campo=valor and campo2=valor2 , etc.
			}
			if ( $insertcampos  != "" ){
				$insertcampos .= " , ";

			}
			$insertcampos .= " $campo ";
			if ( $insertvalores  != "" ){
				$insertvalores .= " , ";
			}
			$insertvalores .= " $valor ";

			if ( ($strkeyID == "") || ( intval($valor) != 0 ) ) {//si no es la clave principal.
				if ( $updatesql != "" ){
					$updatesql .= " , ";

				}
				$updatesql .= " $campo = $valor ";
			}


		}//if ($valor != "") {//si tiene informacion

	}//if ($pos === false) {// no se encontro


	$arrresult["ID"] = $strkeyID;
	$arrresult["key2"] = $strkey2;
	$arrresult["insertcampos"] = $insertcampos;
	$arrresult["insertvalores"] = $insertvalores;
	$arrresult["updatesql"] = $updatesql;

	return $arrresult;

}//function creararregloinsertmipressuministro ( $campo , $valor , $insertcampos , $insertvalores , $updatesql , $valorkeyID , $valorkey2 , $strkeyID, $strkey2  )





// ==============================================

// ==============================================
//  WEB SERVICES
// ==============================================

	function llamarws_x_curl($method, $url2, $data)//$data en text si existe.
	{//method = POST , GET , PUT
	// $url = direccion del servicio web .
	// $data es el JSON en texto , usar json_encode.
	//	$url = replace


		$url = str_replace(" " , "" , $url2);//quitar los espacios 5 julio 2019
//https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/AnularProgramacion/800149026/h9948isYdO3Gf9OHavJgp03wcX6aFnq8zPeq5bmvkdY=/ 2707748
//aqui hay un error con el espacios

		$result = array();//" error ";
		if($ch = curl_init($url)) {
			if ($data != ""){
				$fields = $data ;
				//json_encode($data);//utf8_encode($data);//json_decode($data);//(is_array($data)) ? json_encode($data) : $data; //http_build_query($data)


	/*

				$arrOpciones = array ('Content-Type:'.'application/json',
				'Accept:'.'application/json');//curl no soporta arreglos asociativos ."Content-Type" => " application/json" );
*/
				$arrOpciones = array ('Content-Length: ' . strlen($fields),
								  'Content-Type:' . ' application/json ');


			}else{
				$arrOpciones = array('Content-Type: application/json');
			}

			switch ($method){
				case "POST":
					curl_setopt($ch, CURLOPT_POST, 1);
					if ($data != ""){
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

					}
					break;

				case "PUT":

					if ($data != ""){//$data){//
						curl_setopt($ch, CURLOPT_POST, 0);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

					}else{//anular
						curl_setopt($ch, CURLOPT_PUT, true);//linea que faltaba para que funcionara el PUT
					}

					break;

				default:
					if ($data != ""){
						$url = sprintf("%s?%s", $url, http_build_query($data));
					}
					break;
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			if ($data != ""){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $arrOpciones); //,'Content-Type:'." multipart/form-data"

				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

			}

			$result = curl_exec($ch);
			$error = "";// curl_error($ch);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			//$msg = "Error ";
			$msg = "";//suponer que no hay error.
			if (($status>=200) && ($status <= 299) ){
				$msg = "";
			}elseif (($status>=300) && ($status <= 399) ){
				$msg = "Error ".$status;
			}elseif (($status>=400) && ($status <= 499) ){
				switch($status ){
					case 400:
						$msg = "Bad Request ";
						break;
					case 403:
						$msg = "Forbidden";
						break;
					case 404:
						$msg = "Not Found";
						break;
					case 411:
						$msg = "Error 411";
						break;
					case 415:
						$msg = "Error 415 ";
						break;
					case 422://ya fue programado
						$msb = "Error 422 direccionamiento ya programado o informacion ya enviada (duplicada) ";
						//El direccionamiento 1732432 ya fue programado
						break;
					default:
						$msg = "Error >400 = ".$status;
						break;
				}//switch($status )
			}elseif (($status >= 500) && ($status <= 599) ){
				switch($status ){
					case 500:
						$msg = "Internal Server Error";
						break;
					case 503:
						$msg = "Service Unavailable";
						break;
					default:
						$msg = "Error >500 = ".$status;
						break;

				}
			}else{
				$msg = "Error <".$status."> ";
			}

			curl_close($ch);
			if ($msg  != ""){//si hay error

				//echo "<script>jAlert('$error Error del web service : $result <br>$status $msg<br>$url<br>Token del dia en: tokenDiarioWebserviceSuministroMipres','ALERTA')</script>";


				return "Error| $msg $url $status $result <br> $data ";

			}//if ($msg  != "")



			$result = str_replace("\\","",$result );//Importante eliminar los \

			return json_decode($result , true );//respuesta correcta ,convierte el resultado a un Arreglo Json
//1 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto

		}//	if($ch = curl_init($url))
		return "Error|En metodo curl_init";


	}//function llamarws_x_curl($method, $url, $data)




	function llamarws_x_curloriginal($method, $url, $data)
	{
		/*
[
  {
    "Id": 2343899,
    "IdEntrega": 277484
  }
]*/
//var_dump($data);
//return $data;
			if ($data){
//				$fields = (is_array($data)) ? json_encode($data) : $data; //http_build_query($data)
				$fields = $data;
				$arrOpciones = array ('Content-Length: ' . strlen($fields),
								  'Content-Type:' . ' application/json ');
				$url = sprintf(" -d %s %s", $fields , $url );
			}

		$result = array();//" error ";
		if($ch = curl_init($url)) {
		/*	if ($data){
				$fields = (is_array($data)) ? json_encode($data) : $data; //http_build_query($data)
				$arrOpciones = array ('Content-Length: ' . strlen($fields),
								  'Content-Type:' . ' application/json ');

			}
*/
			switch ($method){
				case "POST":
					curl_setopt($ch, CURLOPT_POST, 1);
					if ($data){
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					}

					break;
				case "PUT":
					 if ($data){
						// $url = sprintf(" -d %s %s", $fields , $url );
					 }else{
//						curl_setopt($ch, CURLOPT_POST, 0);
					 }
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

					/*

*/
					break;
				default:
					if ($data){

				//	curl -X PUT --header "Content-Type: application/json" --header "Accept: application/json" -d "{

						$url = sprintf("%s?%s", $url, http_build_query($data));
					}
					break;
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields))); //,'Content-Type:'." multipart/form-data"
			if ($data){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $arrOpciones); //,'Content-Type:'." multipart/form-data"
			//	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

			}

			$result = curl_exec($ch);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$msg = "Error ";
			switch($status ){
				case 200:
					$msg = "";
					break;
				case 400:
					$msg = "Bad Request";
					break;
				case 403:
					$msg = "Forbidden";
					break;
				case 404:
					$msg = "Not Found";
					break;
				case 500:
					$msg = "Internal Server Error";
					break;
				case 503:
					$msg = "Service Unavailable";
					break;
				default :
					//$result = false;// "{ [ \"error\" : \"$status $msg\" ] } ";
					//return false;
					break;

			}
			curl_close($ch);
			if ($msg  != ""){
				$resultError = array ("ErrorWS" => $msg. " $url");
 //echo "<script>jAlert('Error numero el Web Service: $msg  en la URL: $url ','ALERTA')</script>";
  echo "<script>jAlert('Error del web service : $result <br>$status $msg<br>$url','ALERTA')</script>";

			//	var_dump($resultError);
			//	var_dump($result);
				$arrCero = array();
				return $arrCero; //false;//false;
			}//if ($msg  != "")
			return $result;//respuesta correcta ,(int) $status;

		}//	if($ch = curl_init($url))
		return $result;// arreglo en blanco false;
//		return $result;

	}//function llamarws_x_curl($method, $url, $data)




	function llamarws2_x_curl($method, $url, $data)
	{

		$result = " error ";
		if($ch = curl_init($url)) {
			if ($data){
				$fields = (is_array($data)) ? json_encode($data) : $data; //http_build_query($data)
				$arrOpciones = array ('Content-Length: ' . strlen($fields),
									'Content-Type:' . ' application/json ');

			}

			switch ($method){
				case "POST":
					curl_setopt($ch, CURLOPT_POST, 1);
					if ($data){
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
					}

					break;
				case "PUT":
					curl_setopt($ch, CURLOPT_POST, 0);
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

					break;
				default:
					if ($data){
						$url = sprintf("%s?%s", $url, http_build_query($data));
					}
					break;
			}
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields))); //,'Content-Type:'." multipart/form-data"
			if ($data){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $arrOpciones); //,'Content-Type:'." multipart/form-data"
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

			}

			$result = curl_exec($ch);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			curl_close($ch);
			return $result;//(int) $status;

		}
		return $result;

	}//function llamarws_x_curl($method, $url, $data)

	//Obtiene el nit asociado a la empresa que esta ejecutando los web services.
	function nitwebservice ($wemp_pmla){//$wemp_pmla
		global $conex;
		return consultarAliasPorAplicacion( $conex, $wemp_pmla, 'nitWebserviceMipres' );// "800067065";
		//
	}

	//Obtiene el codigo de sede para realizar la programacion de tecnologias en los mipres direccionados.
	function codigosedeproveedor ($wemp_pmla){
		global $conex;
		return consultarAliasPorAplicacion( $conex, $wemp_pmla, 'codigoSedeMipres' );
		//
	}



//token diario usado en el mipres de suministros
		function tokenwebservice($wemp_pmla){
			global $conex;
			global $wbasedatoroot;
			global $esproduccion ;

			$tokenRaiz = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceMipres' );
			$fechavalidez = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'fechatokenWebserviceSuministroMipres' );

			//$whoy=date("Y-m-d");
			$whoy=date("Y-m-d H:i:s");//4 de junio 2019 , debe incluir la hora
			$whoy12horasdespues = date("Y-m-d H:i:s",strtotime($whoy."+ 12 hours"));//fecha validez

			if ( $fechavalidez < $whoy ){// no es valido el token ya expiro

				//actualizar fechatokenWebserviceSuministroMipres
				if ( $esproduccion == false  ){//true debe quedar el  codigo del else, porque permite generar  un nuevo token cuando el actual
				//esta vencido.
				
					$where  = "Detapl = 'fechatokenWebserviceSuministroMipres' ";
					$where  .= " AND Detemp = '".$wemp_pmla."' ";
					//$setcampos = "Detval = '".date("Y-m-d"). "' ";
					$setcampos = "Detval = '".$whoy12horasdespues. "' ";
					
					//$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);
					
					//falta filtrar por empresa
					$tokenhoy = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenDiarioWebserviceSuministroMipres' );

					return $tokenhoy;//"0Xh6gijRilikxCwftRSS12gz6GPLVmhi7okoKh9hwhw=";

				}else{//probada la generacion del token

					$tokenhoy = get_generar_token();
					if (!(is_string($tokenhoy) ) ) {
						$tokenhoy = json_encode ( $tokenhoy );
					}
					$pos = strpos($tokenhoy, "Message");// "Error");
					$pos2 = strpos($tokenhoy, "Error");// "Error"); 2
					if ( $tokenhoy == "" ){//
						return "Error|token en blanco - servicio web no esta funcionando";
					}
					if  ( ( $pos === false )  && ( $pos2 === false ) ) {//no hay error

						
					//$whoy=date("Y-m-d");
						$where  = "Detapl = 'tokenDiarioWebserviceSuministroMipres' ";
						$where  .= " AND Detemp = '".$wemp_pmla."' ";
						$setcampos = "Detval = '".$tokenhoy. "' ";
						//falta filtrar por empresa
						$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);

						$where  = "Detapl = 'fechatokenWebserviceSuministroMipres' ";
						$where  .= " AND Detemp = '".$wemp_pmla."' ";
						//$setcampos = "Detval = '".$whoy. "' ";
						$setcampos = "Detval = '".$whoy12horasdespues. "' ";
						$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);

					}//if ($pos === false)
						
					return $tokenhoy;

				}




			}else{
				$tokenhoy = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenDiarioWebserviceSuministroMipres' );

				return $tokenhoy;
			}

			//"0Xh6gijRilikxCwftRSS1wbpxlfUKQRgTjzQY8xMwEQ=";
			//$tokenWSmipres = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceProveedorMipres' );

		}//function tokenwebservice($wemp_pmla)

		//Token diario usado en el mipres de facturacion
		function tokenfacturacionwebservice($wemp_pmla){
			global $conex;
			global $wbasedatoroot;
			global $esproduccion ;

			$tokenRaiz = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceFacturacionMipres' );
			$fechavalidez = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'fechatokenWebserviceFacturacionMipres' );

			//$whoy=date("Y-m-d");
			$whoy=date("Y-m-d H:i:s");//4 de junio 2019 , debe incluir la hora
			$whoy12horasdespues = date("Y-m-d H:i:s",strtotime($whoy."+ 12 hours"));//fecha validez

			if ( $fechavalidez < $whoy ){// no es valido el token ya expiro

				//actualizar fechatokenWebserviceFacturacionMipres
				//8 de Octubre 2019
				//false ){ //para descomentar .//
				if ( $esproduccion == false  ){//true debe quedar el  codigo del else, porque permite generar  un nuevo token cuando el actual
				//esta vencido.
					$where  = "Detapl = 'fechatokenWebserviceFacturacionMipres' ";
					$where  .= " AND Detemp = '".$wemp_pmla."' ";
					//$setcampos = "Detval = '".date("Y-m-d"). "' ";
					$setcampos = "Detval = '".$whoy12horasdespues. "' ";
					
					//$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);
					
					//falta filtrar por empresa
					$tokenhoy = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenDiarioWebserviceFacturacionMipres' );

					return $tokenhoy;//"0Xh6gijRilikxCwftRSS12gz6GPLVmhi7okoKh9hwhw=";

				}else{//probada la generacion del token

					$tokenhoy = get_generar_token_facturacion();
					
					
					if (!(is_string($tokenhoy) ) ) {
						$tokenhoy = json_encode ( $tokenhoy );
					}
					$pos = strpos($tokenhoy, "Message");// "Error"); 2
					$pos2 = strpos($tokenhoy, "Error");// "Error"); 2
					if ( $tokenhoy == "" ){//
						return "Error|token en blanco - servicio web no esta funcionando";
					}
					 if  ( ( $pos === false )  && ( $pos2 === false ) )  {//no hay error ($pos === false)

						
					//$whoy=date("Y-m-d");
						$where  = "Detapl = 'tokenDiarioWebserviceFacturacionMipres' ";
						$where  .= " AND Detemp = '".$wemp_pmla."' ";
						$setcampos = "Detval = '".$tokenhoy. "' ";
						//falta filtrar por empresa
						$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);

						$where  = "Detapl = 'fechatokenWebserviceFacturacionMipres' ";
						$where  .= " AND Detemp = '".$wemp_pmla."' ";
						//$setcampos = "Detval = '".$whoy. "' ";
						$setcampos = "Detval = '".$whoy12horasdespues. "' ";
						$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);

					}//if ($pos === false)
						
					return $tokenhoy;

				}




			}else{
				$tokenhoy = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenDiarioWebserviceFacturacionMipres' );

				return $tokenhoy;
			}

			//"0Xh6gijRilikxCwftRSS1wbpxlfUKQRgTjzQY8xMwEQ=";
			//$tokenWSmipres = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceProveedorMipres' );

		}//function tokenfacturacionwebservice($wemp_pmla)		
		

	function nuevo_token_actualizadoBD (){
		global $wbasedatoroot;
		global $wemp_pmla;


		$whoy=date("Y-m-d H:i:s");//4 de junio 2019 , debe incluir la hora
		$whoy12horasdespues = date("Y-m-d H:i:s",strtotime($whoy."+ 12 hours"));//fecha validez

		$tokenhoy = get_generar_token();

		$where  = "Detapl = 'tokenDiarioWebserviceSuministroMipres' ";
		$where  .= " AND Detemp = '".$wemp_pmla."' ";
		$setcampos = "Detval = '".$tokenhoy. "' ";

		$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);

		$where  = "Detapl = 'fechatokenWebserviceSuministroMipres' ";
		$where  .= " AND Detemp = '".$wemp_pmla."' ";
		$setcampos = "Detval = '".$whoy12horasdespues. "' ";
		$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);

		return $tokenhoy;

	}//function nuevo_token_actualizadoBD

	//Url raiz para ejecutar los web services .
	function urlwebservice($wemp_pmla){
		global $conex;
		return consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebserviceSuministroMipres' );
		//"https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api";

	}

	//Url raiz para ejecutar los web services de facturacion .
	function urlwebservicefacturacion($wemp_pmla){
		global $conex;
		return consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebserviceFacturacionMipres' );
		//"https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api";

	}//function urlwebservicefacturacion($wemp_pmla)

	
	function get_url_x_fecha ($xxxservicio  , $fecha , $wemp_pmla)
	{
		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url = "$urlapi/$xxxservicio/";
		$url .= "$nit/$token/$fecha";

		return $url;

	}
	
	
	function get_url_x_fecha_facturacion ($xxxservicio  , $fecha , $wemp_pmla)
	{
		$nit = nitwebservice($wemp_pmla);
		$token = tokenfacturacionwebservice($wemp_pmla);
		$urlapi = urlwebservicefacturacion($wemp_pmla);

		$url = "$urlapi/$xxxservicio/";
		$url .= "$nit/$token/$fecha";

		return $url;

	}
	

	//Hace el llamado a web services PUT
	function fconsumir_webservice_put($url , $context){
		global $esproduccion;

		$result = llamarws_x_curl ( "PUT" , $url ,$context);

		if (is_string($result)){


			$pos = strpos($result, "EL TOKEN EXPIR");//token expirado
			if ($pos === false) {
			}else{//se debe generar un nuevo token , pero no se puede hacer aqui, porque generar un nuevo token hace llamado a este metodo
			
				if ( $esproduccion ){
					//10 junio 2019 ,se genera un llamado circular
				//	$nuevotoken = nuevo_token_actualizadoBD();
//	$result = llamarws_x_curl ( "PUT" , $url ,$context);

				}
			}//if ($pos === false) {
		}//error en el web service
		else{//10 de julio 2019 , capturar el error
					//  "Message": "Error NIT: 800149026",
  //"Errors": [
  //  "La programación 988533 ya fue entregada"
 // ]




			$resultstring = "";

			if ( (is_array($result))  ||  (is_object($result)) ){
				foreach($result as $key =>$value){


					if ($key == "Message"){
						if (is_string($value) ){
							$resultstring .= $value." ";
						}

					}else if ($key == "Errors"){
						$resultstring .= "Errores : ";
						foreach($value as $key2 =>$value2){
							$resultstring .= $value2. " ";
						}
					}

				}//foreach($result as $key =>$value)

				//var_dump($resultstring);

				if ($resultstring != ""){
					// echo "<script>jAlert('"." $resultstring<br>$context','ALERTA')</script>";

					$result = "Error| $resultstring";//15 julio 2019 , Freddy Saenz , para que muestre el error al final
					//en una alerta . Error| se configuraron al final para informar que se produjo un error ,lo errores con
					// Message no se estaban mostrando.
				}
			}//if (is_array($result))

		}


		return $result;

	}//function fconsumir_webservice_put($url , $context)

	//Hace el llamado a web services GET
	function fconsumir_webservice_get($url){
		global $esproduccion;

		$result = llamarws_x_curl ( "GET", $url,"");

		return $result;


	}//function fconsumir_webservice_get($url)

	//WEB SERVICES GENERICOS
	//get /api/DireccionamientoXFecha/{nit}/{token}/{fecha}
	//get /api/ProgramacionXFecha/{nit}/{token}/{fecha}
	//get /api/EntregaXFecha/{nit}/{token}/{fecha}
	//get /api/ReporteEntregaXFecha/{nit}/{token}/{fecha}
	// wsxxx puede ser : DireccionamientoXFecha,ProgramacionXFecha,EntregaXFecha y ReporteEntregaXFecha
	function get_wsxxx_x_fecha($xxxservicio ,  $fecha , $wemp_pmla)
	{


		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url = "$urlapi/$xxxservicio/";
		$url .= "$nit/$token/$fecha";
		$jsonMipres =  fconsumir_webservice_get($url, false);//file_get_contents($url, false) ;//fconsumir _webservice_curl( "GET" , $url , false );


		return $jsonMipres;
	}//function get_wsxxx_x_fecha($xxxservicio  , $fecha , $wemp_pmla)

	
	function get_wsxxx_x_fechafacturacion($xxxservicio ,  $fecha , $wemp_pmla)
	{


		$nit = nitwebservice($wemp_pmla);
		$token = tokenfacturacionwebservice($wemp_pmla);//se usa el token de facturacion
		$urlapi = urlwebservicefacturacion($wemp_pmla);//1. 8 de octubre 2019 Freddy Saenz difiere del url de suministros (get_wsxxx_x_fecha)

		$url = "$urlapi/$xxxservicio/";
		$url .= "$nit/$token/$fecha";
		$jsonMipres =  fconsumir_webservice_get($url, false);//file_get_contents($url, false) ;//fconsumir _webservice_curl( "GET" , $url , false );


		return $jsonMipres;
	}//function get_wsxxx_x_fecha($xxxservicio  , $fecha , $wemp_pmla)

	
	
	//get /api/DireccionamientoXPrescripcion/{nit}/{token}/{noPrescripcion}
	//get /api/ProgramacionXPrescripcion/{nit}/{token}/{noPrescripcion}
	//get /api/EntregaXPrescripcion/{nit}/{token}/{noPrescripcion}
	//get /api/ReporteEntregaXPrescripcion/{nit}/{token}/{noPrescripcion}
	// wsxxx puede ser : DireccionamientoXPrescripcion,ProgramacionXPrescripcion,EntregaXPrescripcion y ReporteEntregaXPrescripcion


	function get_wsxxx_x_prescripcion($xxxservicio , $noprescripcion , $wemp_pmla){

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);


		$url =  "$urlapi/$xxxservicio/";
		$url .= "$nit/$token/$noprescripcion";
		$jsonMipres = fconsumir_webservice_get($url );
		return $jsonMipres;
	}

	function get_wsxxx_x_prescripcionfacturacion($xxxservicio , $noprescripcion , $wemp_pmla){

		$nit = nitwebservice($wemp_pmla);
		$token = tokenfacturacionwebservice($wemp_pmla);
		$urlapi = urlwebservicefacturacion($wemp_pmla);//2. 8 de octubre 2019 Freddy Saenz  difiere de get_wsxxx_x_prescripcion


		$url =  "$urlapi/$xxxservicio/";
		$url .= "$nit/$token/$noprescripcion";
		$jsonMipres = fconsumir_webservice_get($url );
		return $jsonMipres;
	}

	
	
	
	//get /api/DireccionamientoXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}
	//get /api/ProgramacionXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}
	//get /api/EntregaXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}
	//get /api/SuministroXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}

	function get_wsxxx_x_paciente_fecha($xxxservicio  ,  $fecha ,  $tipodoc , $numdoc ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/$xxxservicio/";
		$url .= "$nit/$fecha/$token/$tipodoc/$numdoc";
		$jsonMipres = fconsumir_webservice_get($url );
		return $jsonMipres;
	}
	//WEB SERVICES DE DIRECCIONAMIENTO.

	//put /api/AnularEntrega/{nit}/{token}/{IdEntrega}
	function anular_entrega ( $IdEntrega ){
		global $wemp_pmla;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);


		$url =  "$urlapi/AnularEntrega/";
		$url .= "$nit/$token/$IdEntrega";
		//$jsonMipres = fconsumir_webservice_put($url ,false );
		$jsonMipres = llamarws_x_curl ( "PUT" , $url ,  "");
		return $jsonMipres;
	}

	//put /api/AnularReporteEntrega/{nit}/{token}/{IdReporteEntrega}
	function anular_reporte_entrega (  $IdSuministro ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);


		$url =  "$urlapi/AnularReporteEntrega/";
		$url .= "$nit/$token/$IdSuministro";
		//$jsonMipres = fconsumir_webservice_put($url , false);

		$jsonMipres = llamarws_x_curl ( "PUT" , $url , "");
		return $jsonMipres;
	}

	function anular_programacion (  $Idprogramacion ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);


		$url =  "$urlapi/AnularProgramacion/";
		$url .= "$nit/$token/$Idprogramacion";
		//$jsonMipres = fconsumir_webservice_put($url , false);
//"https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/AnularProgramacion/800149026/h9948isYdO3Gf9OHavJgp6bIcI9FNcmXu_f_i_76l1U%3D/2707543"

		$jsonMipres = llamarws_x_curl ( "PUT" , $url , "");
		return $jsonMipres;
	}

	function anular_facturacion (  $Idfacturacion ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenfacturacionwebservice($wemp_pmla);
		$urlapi = urlwebservicefacturacion($wemp_pmla);//3. 8 de octubre 2019 Freddy Saenz Freddy Saenz difiere del url de suministros


		$url =  "$urlapi/FacturacionAnular/";
		$url .= "$nit/$token/$Idfacturacion";

		$jsonMipres = llamarws_x_curl ( "PUT" , $url , "");
		return $jsonMipres;
	}	
	
	
/* para borrar no se usa

	function actualizarRespuestaWSenBD( $respuestaWS , $data , $numprescripcion , $vconsecutivo , $vtipotec , $vID  ){

		$where = "";
		$tablaActualizar = tablaxtipotec($vtipotec);
		if ($vID == 0){//no tiene ID
			$where = clavemi presdetalle($vtipotec , $numprescripcion , $vconsecutivo , $vID);

		}else{//La busqueda debe ser por ID y en varias tablas
			$where = " SumID = '".$vID."' ";
		}


		if (!is_string( $respuestaWS )){//se pudo enviar correctamente == true

			$identrega = "";
			$jsonRes = $respuestaWS;//ya viene convertido cuando la respuesta es correcta .json_decode($respuestaWS);//convertir a arreglo
			foreach($jsonRes as $key => $value){
				foreach($value as $key2 => $value2){
					switch($key2){
						case "IdEntrega":
							$identrega = $value2;// $jsonRes['IdEntrega'];
							break;

					}//switch($key2)
				}//foreach($value as $key2 => $value2
			}//foreach($jsonRes as $key => $value)

//			$identrega = $jsonRes['IdEntrega'];



			//21 de mayo 2019
			$setcampos = " SumIDe = $identrega " ;
			//aqui se actualiza el estado de la entrega
			$setcampos .= " , Sumeen = 2 " ;//2 es completa
		    $setcampos .= " , Sumjen = '".$data."' " ;

			if (($tablaActualizar != "") && ($where != "") ) {
				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
			}else{ //recorrer todas las tablas a ver a cual le pertenece el ID


			}


		}else{
			$setcampos = "  Sumjen = '".$data."' " ;

			if (($tablaActualizar != "") && ($where != "") ) {
				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
			}


		}
		$bd = true;
		return $bd;
	}//function actualizarRespuestaWSenBD( $respuestaWS , $data , $numprescripcion , $vconsecutivo , $vtipotec , $vID  )


*/

	//put /api/EntregaAmbito/{nit}/{token}
	function hacer_entrega (  $data ){//data en texto
		global $wemp_pmla ;
		global $wbasedatomipres;//31 mayo 2019


		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$noprescripcion = "";
		$vID = 0;
		$vconsecutivo = 0;
		$vtipotec = "";
		$nroentrega = 0;

		//{"NoPrescripcion":"20190401127011163622","TipoTec":"M","ConTec":1,"TipoIDPaciente":"CC","NoIDPaciente":"32302863","NoEntrega":1,"CodSerTecEntregado":"6555","CantTotEntregada":"1","EntTotal":1,"CausaNoEntrega":"0","FecEntrega":"","NoLote":""}

		$data2 = json_decode ($data , true );//texto a json
//2 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto		
//		["NoPrescripcion"]=> string(20) "20190201155010236655" ["TipoTec"]=> string(1) "M" ["ConTec"]=> string(1) "1" ["TipoIDPaciente"]=> string(2) "CC" ["NoIDPaciente"]=> string(8) "71291284" ["NoEntrega"]=> int(1) ["CodSerTecEntregado"]=> string(12) "019956203-04" ["CantTotEntregada"]=>
		foreach( $data2 as $key => $value){
			switch($key){
				case "NoPrescripcion":
					$noprescripcion = $value;
					break;
				case "ID":
					$vID = intval($value);
					break;
				case "ConTec":
					$vconsecutivo = intval($value) ;
					break;
				case "TipoTec":
					$vtipotec = $value;
					break;
				case "NoEntrega"://3 julio 2019 incluir el numero de entrega en la clave principal
					$nroentrega = $value;
					break;

			}
		}
		$wsxxx  = "EntregaAmbito";//En caso de que no tenga ID la prescripcion.
		if ($vID != 0){//Si tiene ID , se debe enviar usando el web service Entrega
			$wsxxx  = "Entrega";
		}else{

		}
		$url =  "$urlapi/$wsxxx/";
		$url .= "$nit/$token";
		$jsonMipres = fconsumir_webservice_put($url , $data );//data en json , si existe

		if (!is_string($jsonMipres) ){


			$jsonRes = $jsonMipres;//ya esta como arreglo Json json_decode($jsonMipres);
		
			$idresp = "";
			$id2resp = "";
			foreach($jsonRes as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
				foreach($value as $key2 => $value2 ){
					switch($key2){
					case "Id":
						$idresp = $value2;
						break;
					case "IdEntrega"://IDEntrega
						$id2resp = $value2;
						break;

					}//switch($key2)
				}//foreach($value as $key2 => $value2 )

			}//foreach($vJson as $key => $value)

			//$vID = intval($idresp);

			//$idreporteentrega = $jsonRes['IdReporteEntrega'];
			if ( (intval($id2resp) != 0) && (intval($idresp) != 0) ) {

				if  ( ($vID == 0) || ( $wsxxx  == "EntregaAmbito" ) ) {//no tiene ID o es por ambito de entrega
					//17 julio 2017 , usar la clave principal cuando sea por ambito de entrega.
					//$where = clave mipresdetalle($vtipotec , $noprescripcion , $vconsecutivo , $vID);
					$where = claveppalmipres( $vID  , $vtipotec , $noprescripcion , $vconsecutivo , $nroentrega);//3 julio 2019

				}else{//La busqueda debe ser por ID y en varias tablas
					$where = " SumID = '".$vID."' ";
				}


				$tablaActualizar = $wbasedatomipres."_000037";
				$setcampos  = " SumIDe = '".$id2resp."' ";
				//24 mayo 2019 aqui va la actualizacion del estado
				$setcampos .= " , Sumeen = 2 ";//procesada
				$setcampos .= " , Sumjen = '".$data."' ";
				if ($vID == 0){
					$setcampos .= " , SumID = '".$idresp."' ";
					$setcampos .= " , Sumeam = 1 ";//17 julio 2019 , entrega por ambito de entrega
				}




				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);

			}//if ( (intval($idreporteentrega)!=0) && ($vID != 0) )


		}//if ($jsonMipres==true)






		return $jsonMipres;

	}//function hacer_entrega (  $data )


	/* Data EntregaAmbito
	{
		"NoPrescripcion": "string",
		"TipoTec": "string",
		"ConTec": 0,
		"TipoIDPaciente": "string",
		"NoIDPaciente": "string",
		"NoEntrega": 0,
		"CodSerTecEntregado": "string",
		"CantTotEntregada": "string",
		"EntTotal": 0,
		"CausaNoEntrega": 0,
		"FecEntrega": "string",
		"NoLote": "string"
	}
	respuesta
	{
		"Id": 0,
		"IdEntrega": 0
	}
	*/

	//put /api/ReporteEntrega/{nit}/{token}
	function hacer_reporte_entrega ( $data ){
		global $wemp_pmla ;
		global $wbasedatomipres;


		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$vfechaentrega = date("Y-m-d");


		$url =  "$urlapi/ReporteEntrega/";
		$url .= "$nit/$token";
		$vID = 0;// $data['ID'];//viene como string ,no tiene valor
		$jsonMipres = fconsumir_webservice_put($url , $data );

		if (!is_string($jsonMipres) ){
			$jsonRes = $jsonMipres;//ya esta como arreglo Json json_decode($jsonMipres);
			
			$idresp = "";
			$idreporteentrega = "";
			foreach($jsonRes as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
				foreach($value as $key2 => $value2 ){
					switch($key2){
					case "Id":
						$idresp = $value2;
						break;
					case "IdReporteEntrega":
						$idreporteentrega = $value2;
						break;

					}//switch($key2)
				}//foreach($value as $key2 => $value2 )

			}//foreach($vJson as $key => $value)

			$vID = intval($idresp);

			//$idreporteentrega = $jsonRes['IdReporteEntrega'];
			if ( (intval($idreporteentrega) != 0) && ($vID != 0) ) {

				$tablaActualizar = $wbasedatomipres."_000037";
				$setcampos  = " SumIDr = '".$idreporteentrega."' ";

//4 julio 2019 , no se actualiza aqui , se actualiza posteriormente , si la fecha esta en blanco
     			$setcampos .= " , Sumfem = $vfechaentrega ";//2

				//24 mayo 2019 aqui va la actualizacion del estado
				$setcampos .= " , Sumere = 2 ";//procesada
				$setcampos .= " , Sumjre = '".$data."' ";
				$where = " SumID = '".$vID."' ";
				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);



			}//if ( (intval($idreporteentrega)!=0) && ($vID != 0) )


		}//if ($jsonMipres==true)


		return $jsonMipres;

	}//function hacer_reporte_entrega ( $data )
	/* Data ReporteEntrega
	{
		"ID": 0,
		"EstadoEntrega": 0,
		"CausaNoEntrega": 0,
		"ValorEntregado": "string"
	}
	respuesta
	{
		"Id": 0,
		"IdReporteEntrega": 0
	}
	*/

	/*
	{
		"NoPrescripcion": "string",
		"TipoTec": "string",
		"ConTec": 0,
		"TipoIDPaciente": "string",
		"NoIDPaciente": "string",
		"NoEntrega": 0,
		"CodSerTecEntregado": "string",
		"CantTotEntregada": "string",
		"EntTotal": 0,
		"CausaNoEntrega": 0,
		"FecEntrega": "string",
		"NoLote": "string"
	}*/


	function hacer_programacion( $data ){

		global $wemp_pmla ;
		global $wbasedatomipres;


		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$vfechaentrega=date("Y-m-d");


		$url =  "$urlapi/Programacion/";
		$url .= "$nit/$token";
		$vID = 0;// $data['ID'];//viene como string ,no tiene valor
		$jsonMipres = fconsumir_webservice_put($url , $data );

		if (!is_string($jsonMipres) ){
			$idresp = "";
			$id2resp = "";
			$jsonRes = $jsonMipres;//faltaba esta linea , 5 julio 2019 ya esta como arreglo Json json_decode($jsonMipres);
			foreach($jsonRes as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
				foreach($value as $key2 => $value2 ){
					switch($key2){
					case "Id":
						$idresp = $value2;
						break;
					case "IdProgramacion":
						$id2resp = $value2;
						break;

					}//switch($key2)
				}//foreach($value as $key2 => $value2 )

			}//foreach($vJson as $key => $value)

			$vID = intval($idresp);

			if ( (intval($id2resp) != 0) && ($vID != 0) ) {

				$tablaActualizar = $wbasedatomipres."_000037";
				$setcampos  = " SumIDp = '".$id2resp."' ";
				//24 mayo 2019 aqui va la actualizacion del estado
				$setcampos .= " , Sumepr = 2 ";//procesada
				$setcampos .= " , Sumjpr = '".$data."' ";
				$where = " SumID = '".$vID."' ";
				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);


				
				
				
				
			}//if ( (intval($idreporteentrega)!=0) && ($vID != 0) )

		}
		return $jsonMipres;

	}//function hacer_programacion($data)
	
	
	
	//6 de agosto de 2019 , creacion del llamado del web service que hace la facturacion
	
	function hacer_facturacion( $data ){

		global $wemp_pmla ;
		global $wbasedatomipres;


		$nit = nitwebservice($wemp_pmla);
		$token = tokenfacturacionwebservice($wemp_pmla);
		$urlapi = urlwebservicefacturacion($wemp_pmla);//4. 8 de octubre 2019 Freddy Saenz  Freddy Saenz difiere del url de suministros

		$vfechaentrega=date("Y-m-d");


		$url =  "$urlapi/Facturacion/";
		$url .= "$nit/$token";
		$vID = 0;// $data['ID'];//viene como string ,no tiene valor
		$jsonMipres = fconsumir_webservice_put($url , $data );
	
//claveppalmipres

		if (!is_string($jsonMipres) ){
			
			$idresp = "";
			$id2resp = "";
			$jsonRes = $jsonMipres;//faltaba esta linea , 5 julio 2019 ya esta como arreglo Json json_decode($jsonMipres);
			foreach($jsonRes as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
				foreach($value as $key2 => $value2 ){
					switch($key2){
					case "Id":
						$idresp = $value2;
						break;
					case "IdFacturacion":
						$id2resp = $value2;
						break;

					}//switch($key2)
				}//foreach($value as $key2 => $value2 )

			}//foreach($vJson as $key => $value)

			$vID = intval($idresp);

			if ( (intval($id2resp) != 0) && ($vID != 0) ) {
//OJO EL id de facturacion es diferente del ID de suministros.
				$tablaActualizar = $wbasedatomipres."_000037";
				
				// este ID es diferente del ID del mipres de suministros $where = " SumID = '".$vID."' ";
				$arrjsondata   = json_decode( $data, true);
				$numprescripcion           = $arrjsondata["NoPrescripcion"];
				$vtipotec                  = $arrjsondata["TipoTec"];
				$vconsecutivo              = $arrjsondata["ConTec"];
				$nroentrega                = $arrjsondata["NoEntrega"];
				
				$vlrcuotamoderadorafactura =   floatval( str_replace( ",", "." , $arrjsondata["CuotaModer"] ) ) ;
				$vlrcopago                 =   floatval ( str_replace( ",", "." , $arrjsondata["Copago"] ) ) ;
				$nrofactura                =   $arrjsondata["NoFactura"];
 
 				$setcampos  = " SumIDf = '".$id2resp."' ";
				//24 mayo 2019 aqui va la actualizacion del estado
				$setcampos .= " , Sumefa = 2 ";//procesada
				$setcampos .= " , Sumjfa = '".$data."' ";
				$setcampos .= " , Sumvco = $vlrcopago ";
				$setcampos .= " , Sumvcm = $vlrcuotamoderadorafactura ";
				$setcampos .= " , Sumnfa = '".$nrofactura."' ";
				$setcampos .= " , SumID2 = '".$idresp."' ";
				

				$where = claveppalmipres( 0 , $vtipotec , $numprescripcion , $vconsecutivo , $nroentrega);//3 de julio 2019
													
				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
				
				$fechainicial  = date("Y-m-d");
				$fechafinal    = $fechainicial;
				//cuando $numprescripcion tiene valor no se usan las fechas ( $fechainicial $fechafinal )
				$arrprescripciones = actualizarIDsPrescripcionesPeriodo	( $wemp_pmla , $fechainicial , $fechafinal , $numprescripcion )	;		
				
			}//if ( (intval($idreporteentrega)!=0) && ($vID != 0) )

		}
		return $jsonMipres;

	}//function hacer_facturacion($data)
	
	
	
	
	

	//get /api/DireccionamientoXFecha/{nit}/{token}/{fecha}
	function get_direccionamiento_x_fecha( $fecha ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

	//	$url = "https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/DireccionamientoXFecha/";
		$url = "$urlapi/DireccionamientoXFecha/";
		$url .= "$nit/$token/$fecha";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/DireccionamientoXPrescripcion/{nit}/{token}/{noPrescripcion}
	function get_direccionamiento_x_preescricion( $noprescripcion ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/DireccionamientoXPrescripcion/";
		$url .= "$nit/$token/$noprescripcion";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//
	//get /api/DireccionamientoXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}
	//https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/DireccionamientoXPacienteFecha/800067065/2019-03-09/0Xh6gijRilikxCwftRSS16oQENVGbctFxcB3_jmcQB0/CC/8263290

	function get_direccionamiento_x_paciente_fecha( $fecha  , $tipodoc , $numdoc){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/DireccionamientoXPacienteFecha/";
		$url .= "$nit/$fecha/$token/$tipodoc/$numdoc";
		$jsonMipres = fconsumir_webservice_get($url );

	}
	//
	//put /api/AnularDireccionamiento/{nit}/{token}/{IdDireccionamiento}
	//
	//put /api/EntregaAmbito/{nit}/{token}

	//
	//put /api/Programacion/{nit}/{token}

	//curl -v -X PUT http://localhost:80/clients/marta -d '{"address":"Calle Hispanidad" }

	//get /api/ProgramacionXFecha/{nit}/{token}/{fecha}
	function get_programacion_x_fecha( $fecha ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

	//	$url = "https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/DireccionamientoXFecha/";
		$url = "$urlapi/ProgramacionXFecha/";
		$url .= "$nit/$token/$fecha";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/ProgramacionXPrescripcion/{nit}/{token}/{noPrescripcion}
	function get_programacion_x_preescricion( $noprescripcion){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/ProgramacionXPrescripcion/";
		$url .= "$nit/$token/$noprescripcion";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/ProgramacionXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}
	function get_programacion_x_paciente_fecha( $fecha , $tipodoc , $numdoc){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/ProgramacionXPacienteFecha/";
		$url .= "$nit/$fecha/$token/$tipodoc/$numdoc";
		$jsonMipres = fconsumir_webservice_get($url );

	}
	//
	//put /api/AnularProgramacion/{nit}/{token}/{IdProgramacion}

	//put /api/Entrega/{nit}/{token}

	//get /api/EntregaXFecha/{nit}/{token}/{fecha}
	function get_entrega_x_fecha( $fecha ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

	//	$url = "https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/DireccionamientoXFecha/";
		$url = "$urlapi/EntregaXFecha/";
		$url .= "$nit/$token/$fecha";
		$jsonMipres = fconsumir_webservice_get($url );

	}
	//get /api/EntregaXPrescripcion/{nit}/{token}/{noPrescripcion}
	function get_entrega_x_preescricion( $noprescripcion ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/EntregaXPrescripcion/";
		$url .= "$nit/$token/$noprescripcion";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/EntregaXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}
	function get_entrega_x_paciente_fecha( $fecha  , $tipodoc , $numdoc){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);


		$url =  "$urlapi/EntregaXPacienteFecha/";
		$url .= "$nit/$fecha/$token/$tipodoc/$numdoc";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//
	//put /api/ReporteEntrega/{nit}/{token}

	//get /api/ReporteEntregaXFecha/{nit}/{token}/{fecha}
	function get_reporte_entrega_x_fecha( $fecha ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

	//	$url = "https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/DireccionamientoXFecha/";
		$url = "$urlapi/ReporteEntregaXFecha/";
		$url .= "$nit/$token/$fecha";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/ReporteEntregaXPrescripcion/{nit}/{token}/{noPrescripcion}
	function get_reporte_entrega_x_preescricion( $noprescripcion ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/ReporteEntregaXPrescripcion/";
		$url .= "$nit/$token/$noprescripcion";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/ReporteEntregaXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}
	function get_reporte_entrega_x_paciente_fecha( $fecha  , $tipodoc , $numdoc){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/ReporteEntregaXPacienteFecha/";
		$url .= "$nit/$fecha/$token/$tipodoc/$numdoc";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//put /api/Suministro/{nit}/{token}

	//get /api/SuministroXFecha/{nit}/{token}/{fecha}
	function get_suministro_x_fecha(  $fecha ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

	//	$url = "https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/DireccionamientoXFecha/";
		$url = "$urlapi/SuministroXFecha/";
		$url .= "$nit/$token/$fecha";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/SuministroXPrescripcion/{nit}/{token}/{noPrescripcion}
	function get_suministro_x_preescricion( $noprescripcion ){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/SuministroXPrescripcion/";
		$url .= "$nit/$token/$noprescripcion";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/SuministroXPacienteFecha/{nit}/{fecha}/{token}/{tipodoc}/{numdoc}
	function get_suministro_x_paciente_fecha( $fecha  , $tipodoc , $numdoc){
		global $wemp_pmla ;

		$nit = nitwebservice($wemp_pmla);
		$token = tokenwebservice($wemp_pmla);
		$urlapi = urlwebservice($wemp_pmla);

		$url =  "$urlapi/SuministroXPacienteFecha/";
		$url .= "$nit/$fecha/$token/$tipodoc/$numdoc";
		$jsonMipres = fconsumir_webservice_get($url );

	}

	//get /api/GenerarToken/{nit}/{token}
	function get_generar_token(  ){//TOKEN es diferente aqui , es el que genera todos los tokens.
		global $wemp_pmla ;
		global $conex;

		$urlapi = urlwebservice($wemp_pmla);
		$nit = nitwebservice($wemp_pmla);
		$tokenRaiz = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceMipres' );

		$url =  "$urlapi/GenerarToken/";
		$url .= "$nit/$tokenRaiz";//$token
		$jsonMipres = fconsumir_webservice_get($url );
		return $jsonMipres;

	}//function get_generar_token(  )
	
//get /api/GenerarToken/{nit}/{token} 
	function get_generar_token_facturacion(  ){//TOKEN es diferente aqui , es el que genera todos los tokens.
		global $wemp_pmla ;
		global $conex;

		$urlapi = urlwebservicefacturacion($wemp_pmla);//5. borrar mayuscula
		$nit = nitwebservice($wemp_pmla);
		$tokenRaiz = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceFacturacionMipres' );

		$url =  "$urlapi/GenerarToken/";
		$url .= "$nit/$tokenRaiz";//$token
		$jsonMipres = fconsumir_webservice_get($url );
		return $jsonMipres;

	}//function get_generar_token_facturacion(  )
	
	//$xxxservicio = "DireccionamientoXFecha"
	//$xxxservicio = "ProgramacionXFecha"
	//get /api/ProgramacionXFecha/{nit}/{token}/{fecha}
	//get /api/ReporteEntregaXFecha/{nit}/{token}/{fecha}

	function fprescripciones_getXFecha($xxxservicio , $fecha , $wemp_pmla)
	{
		//global $wemp_pmla ;

		$tmp  = "'00'";//cadena vacia, no encontro nada
		$result = get_wsxxx_x_fecha ($xxxservicio,$fecha , $wemp_pmla);
//		var_dump($result);
		if($result != false)
		{
			$jsonMipres = json_decode($result , true);
//3 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto
			if(count($jsonMipres)>0)
			{
				$tmp  = "";//si hay registros  , empezar con una cadena vacia .
				foreach($jsonMipres as $key => $value)
				{

					if ($tmp == "")	{
						$tmp = $value["NoPrescripcion"];//9 agosto 2019, eliminado el operador ->
					}else{
						$tmp .= "," . $value["NoPrescripcion"];//NoPrescripcion;
					}
					/*
					foreach($value as $key1 => $value1){
						if ($key1 == "NoPrescripcion"){

							if ($tmp == "")	{
								$tmp = $value1;
							}else{
								$tmp .= "," . $value1;
							}

						}
					}*/

				}

			}else{//sin registros

			}
		}//if($result != false)
		else {

		}


		return $tmp;

	}//function fprescripciones_getXFecha($xxxservicio,$fecha ,$wemp_pmla)


	function jsonATexto ($vjson){

$html = "<table>";

			$txt2 = "";
			//$jsonMipres = json_decode($vjson);
			$jsonMipres = $vjson;
			if(count($jsonMipres)>0){
				foreach($jsonMipres as $key => $value){
					$html .= "<tr>";
					foreach($value as $key1 => $value1){
						if ($txt2 != ""){
							$txt2 .= " , ";
						}
$html .= "<td>$value1</td>";
						$txt2 .= " \"$key1\"  : " . " \"$value1\" ";
						//echo " $value1 ";
					}
$html .= "</tr>";
				}
			}
$html .= "</table>";
//echo $html;
			return $txt2;

		}

	function finfoprescripciones_getXFecha($xxxservicio,$fecha , $wemp_pmla){
		$tmp  = "";
		$result =  get_wsxxx_x_fecha($xxxservicio,$fecha , $wemp_pmla);
		if($result != false)
		{
			echo "consulto en info ";
			$jsonMipres = $result;// json_decode($result);

			if(count($jsonMipres)>0)
			{
				return $jsonMipres;

			}else{
				return false;
			}
		}

	}

	//Busca los ingresos de un paciente usando su numero de identificacion

	function consultarHistoriasXCedula( $cedula , $fechafinal , $fechamaximaentrega )
	{
		global $conex;
		global $wbasedatoroot;
		global $wbasedatocliame;
		global $wmodoambulatorio;


		$q .= " SELECT Inghis , Ingnin , Ingfei , Inghin , Ingtar , B.Fecha_data as Fechadata , B.Hora_data as Horadata ";
		$q .= " FROM " . $wbasedatocliame . "_000100 A, " . $wbasedatocliame . "_000101 B  ";
		$q .= " WHERE pacdoc = '" . $cedula. "'  ";
		$q .= " AND pachis = inghis  ";
		//4 julio 2019 Freddy Saenz , se modifica para tenga en cuenta cuando la prescripcion tiene varias entregas
		// se base en la fecha maxima de entrega y no solamente la fecha del mipres ($fechafinal)

		if ($wmodoambulatorio != 0){//configuracion para el IDC
		
		/*
			if ( ($fechamaximaentrega != "" ) && ($fechamaximaentrega != '0000-00-00') ) {
				$q .= " AND Ingfei >= '".$fechafinal."'  ";
				$q .= " AND Ingfei <= '".$fechamaximaentrega."'  ";
				//$fecha1 = date("Y-m-d",strtotime($fechamaximaentrega."+ 7 days"));
			}else{
				$fecha1 = date("Y-m-d",strtotime($fechafinal."- 15 days"));
				//$q .= " AND Ingfei <= '".$fechafinal."'  ";
				$q .= " AND Ingfei = '".$fechafinal."'  ";//si se deja <= trae informacion cuando todavia no se ha direccionado el mipres
				// e ingresos que no corresponden
			}
			*/
			//27 de Julio 2019 , Freddy Saenz , Si no fue direccionada la entrega anterior , debe buscar
			// los ingreso 15 dias antes de la fecha maxima de la entrega actual 
			if ( ( $fechafinal == "" ) || ( $fechafinal == '0000-00-00' ) ) {
				$fecha1 = date("Y-m-d",strtotime($fechamaximaentrega."- 15 days"));
				$q .= " AND Ingfei >= '".$fecha1."'  ";
				$q .= " AND Ingfei <= '".$fechamaximaentrega."'  ";
				
				
			}else if ( ( $fechamaximaentrega == "" ) || ( $fechamaximaentrega == '0000-00-00') ){
				$fecha1 = date("Y-m-d",strtotime($fechafinal."+ 15 days"));
				$q .= " AND Ingfei >= '".$fechafinal."'  ";
				$q .= " AND Ingfei <= '".$fecha1."'  ";
				
				
			}else{
				$q .= " AND Ingfei >= '".$fechafinal."'  ";
				$q .= " AND Ingfei <= '".$fechamaximaentrega."'  ";
				//$fecha1 = date("Y-m-d",strtotime($fechamaximaentrega."+ 7 days"));
				
			}
			
		}else{
			$q .= " AND Ingfei <= '".$fechafinal."'  ";
		}

	/*

		if ( ($fechamaximaentrega != "" ) && ($fechamaximaentrega != '0000-00-00') ) {
			if ($wmodoambulatorio != 0){
				$q .= " AND Ingfei >= '".$fechafinal."'  ";
			}
			$q .= " AND Ingfei <= '".$fechamaximaentrega."'  ";

		}else{
			$q .= " AND Ingfei <= '".$fechafinal."'  ";
		}
		*/
		if ( $wmodoambulatorio != 0 ){//aplica para el idc
			$q .= " ORDER BY Ingfei ASC , Ingnin ASC  ";
		}else{
			$q .= " ORDER BY Ingfei DESC , Ingnin DESC  ";
		}




		/*
		$q = "SELECT Inghis , Ingnin , Ingfei , Inghin , Ingtar , Fecha_data , Hora_data ";
		$q .= " FROM " . $wbasedatocliame . "_000101 ";
		$q .= " WHERE Inghis IN ";
		$q .= "       (SELECT Orihis FROM " . $wbasedatoroot . "_000037 WHERE Oriced = '" . $cedula. "') ";
		$q .= "  AND Ingfei <=  '".$fechafinal."' ";
		$q .= " ORDER BY Ingfei DESC , Ingnin DESC  ";*/

		$resQuery = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$numElementos = mysql_num_rows($resQuery);
		$arrresult = array();

		if($numElementos>0)
		{
			$ctaHistorias = 0;
			while($rowsQuery = mysql_fetch_array($resQuery))
			{

				$arrresult[$ctaHistorias]['Inghis'] = $rowsQuery['Inghis'] ;
				$arrresult[$ctaHistorias]['Ingnin'] = $rowsQuery['Ingnin'] ;
				$arrresult[$ctaHistorias]['Ingfei'] = $rowsQuery['Ingfei'] ;//fecha de ingreso
				$arrresult[$ctaHistorias]['Inghin'] = $rowsQuery['Inghin'] ;//hora de ingreso

				$arrresult[$ctaHistorias]['Ingtar'] = $rowsQuery['Ingtar'] ;//tarifa
				$arrresult[$ctaHistorias]['Fecha_data'] = $rowsQuery['Fechadata'] ;//Ojo sin underscore _
				$arrresult[$ctaHistorias]['Hora_data'] = $rowsQuery['Horadata'] ;

				$ctaHistorias++;

			}
		}

		return $arrresult;
	} //function consultarHistoriasXCedula( $cedula )

	function sumarMedicamentosxcentralmezclas ($codarticulo, $historia, $ingreso , $fechamaximaentrega) {

		global $conex;
		global $wbasedato; //movhos
		$sumacant = 0;
		global $wbasedatocenpro;

		$wherestr = " Apppre = '". $codarticulo . "' ";
		$codcentralmezclas = select1campoquery("Appcod", $wbasedatocenpro."_000009", $wherestr );


		if ($codcentralmezclas != ""){

			$q = " SELECT Aplcan , Aplhis , Apling ";
			$q .= "  FROM ".$wbasedato."_000015  ";
			$q .= "  WHERE  Aplhis = '".$historia."'";
			$q .= "   AND  Apling = '".$ingreso."'";
			$q .= "   AND  Aplest = 'on' ";
			if ( ( $fechamaximaentrega != "" )  && ( $fechamaximaentrega != '0000-00-00' ) ) {
				//$q .= "   AND  Aplfec <= '".$fechamaximaentrega."' ";
			}


			$q .= "   AND Aplart IN  " ;
			$q .= "  ( SELECT Pdepro from ". $wbasedatocenpro ."_000003 WHERE Pdeins = '".$codcentralmezclas . "' ) " ;

			$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numElementos = mysql_num_rows($resQuery);

			$sumacant = 0;
			if ($numElementos > 0) {

				$sumacant = 0;
				$cta = 0;
				while ($rowsQuery = mysql_fetch_array($resQuery)) {
					$sumacant += $rowsQuery['Aplcan'];
					$cta++;
				}


			}//if ($numElementos > 0)

		}//if ($codcentralmezclas != "")
//SELECT Aplcan FROM movhos_000015 WHERE  Aplhis = '304102' AND  Apling = '19' AND Aplart IN
//( SELECT Pdepro from cenpro_000003 WHERE Pdeins = (SELECT Appcod from cenpro_000009 WHERE Apppre = 'L01B06') )

		if ( ( $sumacant >0 ) && ( $sumacant < 1 ) ) {//no se estaban viendo los valores menores de uno
			return $sumacant;
		}else{//parte techo.
			return ceil($sumacant);//round($sumacant);
		}

	}//function sumarMedicamentosxcentralmezclas ($codarticulo, $historia, $ingreso)


	function sumarMedicamentosMipres($codarticulo, $historia, $ingreso , $fechamaximaentrega) {

		global $conex;
		global $wbasedato; //movhos
		$q = " SELECT Aplcan , Aplhis , Apling ";
		$q .= "  FROM ".$wbasedato."_000015  ";
		$q .= "  WHERE Aplart = '".$codarticulo."'";
		$q .= "   AND  Aplhis = '".$historia."'";
		$q .= "   AND  Apling = '".$ingreso."'";
		$q .= "   AND  Aplest = 'on' ";
		if ( ( $fechamaximaentrega != "" )  && ( $fechamaximaentrega != '0000-00-00' ) ) {
			//$q .= "   AND  Aplfec <= '".$fechamaximaentrega."' ";
		}




		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numElementos = mysql_num_rows($resQuery);

		$sumacant = 0;
		if ($numElementos > 0) {

			$sumacant = 0;
			$cta = 0;
			while ($rowsQuery = mysql_fetch_array($resQuery)) {
				$sumacant += $rowsQuery['Aplcan'];
				$cta++;
			}


		}//if ($numElementos > 0)


		//return $sumacant;
		if ( ( $sumacant >0 ) && ( $sumacant < 1 ) ) {//no se estaban viendo los valores menores de uno
			return $sumacant;
		}else{//parte techo.
			return ceil($sumacant);//round($sumacant);
		}


	}//function sumarMedicamentosMipres($codarticulo, $historia, $ingreso)

	function mostrarAplicacionesDetalle ( $codarticulo , $historia, $ingreso , $vtipotec  , $numprescripcion ,   $vconsecutivo ) {

		global $conex;
		global $wbasedato; //movhos
		global $wbasedatocliame;
		global $wbasedatocenpro;

		$arrresp = array ();
		$wherestr = "";
		$codcentralmezclas = "";
		$q = "";

		if ($vtipotec == "P"){//si es un procedimiento
			$q = "SELECT Tcarprocod as Codigo,Tcarpronom as Procedimiento , Tcarcan as cantidad ,  Fecha_data as fecha ,Hora_data as hora ";
			$q .= " FROM ".$wbasedatocliame."_000106  ";
			$q .= "  WHERE Tcarprocod = '".$codarticulo."'";
			$q .= "   AND  Tcarhis = '".$historia."'";
			$q .= "   AND  Tcaring = '".$ingreso."'";
			$q .= "  ORDER BY Fecha_data,Hora_data ";


		}else{//no es un procedimiento

			$q = " SELECT count(*) as cuenta";
			$q .= "  FROM ".$wbasedato."_000015  ";
			$q .= "  WHERE Aplart = '".$codarticulo."'";
			$q .= "   AND  Aplhis = '".$historia."'";
			$q .= "   AND  Apling = '".$ingreso."'";
			$q .= "   AND  Aplest = 'on' ";
			$q .= "  ORDER BY Aplfec,Aplron ";
			$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$rowsQuery = mysql_fetch_array($resQuery);
			$numregistros = $rowsQuery["cuenta"];

			if ($numregistros == 0) {//se realiza en central de mezclas
				$wherestr = " Apppre = '". $codarticulo . "' ";
				$codcentralmezclas = select1campoquery("Appcod", $wbasedatocenpro ."_000009", $wherestr );
				if ($codcentralmezclas != "" ){
					$q = " SELECT  Aplart as codigo , Apldes as Preparacion , Aplcan as cantidad ,Aplfec as fecha , Aplron  as ronda ";
					$q .= "  FROM ".$wbasedato."_000015  ";
					$q .= "  WHERE  Aplhis = '".$historia."'";
					$q .= "   AND  Apling = '".$ingreso."'";
					$q .= "   AND  Aplest = 'on' ";
					$q .= "   AND Aplart IN  " ;
					$q .= "  ( SELECT Pdepro FROM ".$wbasedatocenpro."_000003 WHERE Pdeins = '".$codcentralmezclas . "' ) " ;
					$q .= "  ORDER BY Aplfec,Aplron ";

				}

			}else{
				$q = " SELECT Aplart as Codigo , Apldes as Descripcion , Aplcan as cantidad ,Aplfec as fecha , Aplron  as ronda ";
				$q .= "  FROM ".$wbasedato."_000015  ";
				$q .= "  WHERE Aplart = '".$codarticulo."'";
				$q .= "   AND  Aplhis = '".$historia."'";
				$q .= "   AND  Apling = '".$ingreso."'";
				$q .= "   AND  Aplest = 'on' ";
				$q .= "  ORDER BY Aplfec,Aplron ";

			}//if ($codcentralmezclas != "") {//se realiza en central de mezclas


		}//if ($vtipotec == "P"){//si es un procedimiento

		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numElementos = mysql_num_rows($resQuery);

		$sumacant = 0;
		$numcolumnas = 0;
		$html  = "Prescripcion: $numprescripcion $vtipotec [$vconsecutivo]  <br> ";
		if ($numElementos > 0) {

			$sumacant = 0;
			//$html  .= "<table align='center' width='100%'>";
			$htmencabezado  = "Registro de Aplicaciones <br> $html";
			$html = "";
			$htmldetalle = "";
			$i = 0;
			$sumacant = 0;
			while ($rowsQuery = mysql_fetch_array($resQuery)) {

				if ( ($i%2) == 0 ){
					$htmldetalle  .= "<tr class='fila2' align='center'>";
				}else{
					$htmldetalle  .= "<tr class='fila1' align='center'>";
				}

					foreach( $rowsQuery as $key => $value){
						if ( !is_numeric($key) ){
							if ($i == 0){

								$htmencabezado  .= "<td> $key </td>";
							}
							$arrresp[$key] = $value;
							$htmldetalle  .= "<td> $value </td>";


						}


					}
					if ($i == 0){
						$htmencabezado  .= "<td> Acumulado </td>";
						$numcolumnas++;
					}


					$sumacant += $rowsQuery['cantidad'];
					$htmldetalle  .= "<td> $sumacant </td>";

				$i++;
				$htmldetalle  .= "</tr>";
			}
			$html  .= "<table  align='center' width='100%'><tr class='fila1'  align='center' width='100%' colspan='".$numcolumnas."' > $htmencabezado </tr> $htmldetalle </table>";
			$html .= " <br>&nbsp; Cantidad Total: $sumacant &nbsp;&nbsp;&nbsp;";
		}
		//return $sumacant;
		return $html;

	}//function mostrarAplicacionesDetalle ($codarticulo, $historia, $ingreso)

	function valorycantidadprocedimiento( $codarticulo, $historia, $ingreso, $tarifa, $fechacargo ) {
		global $conex;
		global $wbasedatocliame;

		$arrresult = array();
		$q = "SELECT Tcarcan , Tcarvun ,Tcarvre ,  Tcarhis , Tcaring , Tcarfec, Tcarsin ,Tcarprocod , Tcartar ,Tcarpronom, Fecha_data ,Hora_data ";
		$q .= " FROM ".$wbasedatocliame."_000106  ";
		$q .= "  WHERE Tcarprocod = '".$codarticulo."'";
		$q .= "   AND  Tcarhis = '".$historia."'";
		$q .= "   AND  Tcaring = '".$ingreso."'";

		//  $q .= "   AND  Tcarconcod = '"."0700"."'";//no todo procedimiento lo hace el laboratorio
		//$q .= "   AND  Tcartar = '".$tarifa."'";//el query retorna la tarifa
		//$q .= "   AND  Tcarfec >= '" . "2017-01-01" . "'" ;

		$q .= "   AND   Tcarest = 'on' ";

		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno().
			" - en el query: ".$q.
			" - ".mysql_error());
		$numElementos = mysql_num_rows($resQuery);

		if ($numElementos > 0) {
			$ctaproc = 0;

			while ($rowsQuery = mysql_fetch_array($resQuery)) {

				$arrresult[$ctaproc]['Tcarcan'] = $rowsQuery['Tcarcan'];
				$arrresult[$ctaproc]['Tcarvun'] = $rowsQuery['Tcarvun'];
				$arrresult[$ctaproc]['Tcarvre'] = $rowsQuery['Tcarvre']; //valor dcon recargo
				$arrresult[$ctaproc]['Tcarhis'] = $rowsQuery['Tcarhis']; //historia
				$arrresult[$ctaproc]['Tcaring'] = $rowsQuery['Tcaring']; // ingreso
				$arrresult[$ctaproc]['Tcarfec'] = $rowsQuery['Tcarfec'];
				$arrresult[$ctaproc]['Tcarsin'] = $rowsQuery['Tcarsin'];
				$arrresult[$ctaproc]['Tcarprocod'] = $rowsQuery['Tcarprocod'];
				 $arrresult[$ctaproc]['Tcarpronom'] = $rowsQuery['Tcarpronom'];

				$arrresult[$ctaproc]['Tcartar'] = $rowsQuery['Tcartar']; //tarifa
				$arrresult[$ctaproc]['Fecha_data'] = $rowsQuery['Fecha_data'];
				$arrresult[$ctaproc]['Hora_data'] = $rowsQuery['Hora_data'];

				$ctaproc++;

			}

		}
		return $arrresult;
	}

	//27 de junio 2019
	function valorcobradoMedicamento ( $codarticulo, $historia, $ingreso, $tarifa, $fechacargo){//busca en los movimientos el valor registrado
		global $conex;
		global $wbasedatocliame;

		$q = "SELECT Tcarvun  ";
		$q .= " FROM ".$wbasedatocliame."_000106  ";
		$q .= "  WHERE Tcarprocod = '".$codarticulo."'";
		$q .= "   AND  Tcarhis = '".$historia."'";
		$q .= "   AND  Tcaring = '".$ingreso."'";
		//$q .= "   AND  Tcarfec >= '" . $fechacargo. "'" ;

		$q .= "   AND   Tcarest = 'on' ";
		$q .= "   AND   Tcardev != 'on' ";//27 junio 2019 , que no sea una devolucion
		$q .= "  ORDER BY Tcarfec DESC ";	//dejar los ultimos de primero

		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numElementos = mysql_num_rows($resQuery);

		$valormedicamento = 0;
		if ( $numElementos > 0 ) {
			$rowsQuery = mysql_fetch_array($resQuery);
			if ( $rowsQuery != false ){
				$valormedicamento = $rowsQuery['Tcarvun']; //Valor cobrado en ese momento
			}


		}
		return $valormedicamento;

	}

	function valorTarifaMedicamento($codarticulo, $codtarifa, $fechacompra) {//Busca en las tarifas
		global $conex;
		global $wbasedatocliame;



		$q = "SELECT Mtaart,Mtavan , Mtavac , Mtafec , Mtaest  ";
		$q .= " FROM ".$wbasedatocliame."_000026 ";
		$q .= " WHERE Mtaart = '".$codarticulo."' ";
		$q .= "   AND Mtatar = '".$codtarifa."' ";




		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numElementos = mysql_num_rows($resQuery);

		$valormedicamento = 0;
		if ( $numElementos > 0 ) {
			$rowsQuery = mysql_fetch_array($resQuery);
			if ($fechacompra < $rowsQuery['Mtafec']) {
				$valormedicamento = $rowsQuery['Mtavan']; //Valor anterior
			} else {
				$valormedicamento = $rowsQuery['Mtavac']; //Valor actual
			}

		}
		return $valormedicamento;

	} //function valorTarifaMedicamento ( $codarticulo , $codtarifa , $fechacompra )

	function select1campoquery($camposelect, $from, $where) {
		global $conex;
		$q = "SELECT $camposelect  ";
		$q .= " FROM $from ";
		$q .= " WHERE $where ";

	//	$resQuery = mysql_query($q, $conex) or die("Error: "." - en el query: ".$q." - ");
		if (!($resQuery = mysql_query($q, $conex))){
			
			var_dump("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			die();
		}
		//$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numElementos = mysql_num_rows($resQuery);

		$camporesp = "";
		if ($numElementos > 0) {
			$rowsQuery = mysql_fetch_array($resQuery);
			$camporesp = $rowsQuery[$camposelect];



		}
		return $camporesp;

	} //function select1campoquery( $camposelect , $from , $where  )


	function select1filaquery($camposselect, $from, $where) {
		global $conex;
		$q = "SELECT $camposselect  ";
		$q .= " FROM $from ";
		$q .= " WHERE $where ";

		//$resQuery = mysql_query($q, $conex) or die("Error: "." - en el query: ".$q." - ");//die
		if ( ! ($resQuery = mysql_query($q, $conex) )){
			var_dump("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());//die
			die();
		}
		//$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());//die
		$numElementos = mysql_num_rows($resQuery);

		$camporesp = "";
		$arrresp = array();

		if ($numElementos > 0) {



			while($rowsQuery = mysql_fetch_array($resQuery))
			{
					foreach( $rowsQuery as $key => $value){
						$arrresp[$key] = $value;
					}



			}

		}
		return $arrresp;

	} //function select1filaquery( $camposelect , $from , $where  )



	function selectEnArregloquery( $camposselect, $from, $where , $orderby ) {
		global $conex;
		$q = "SELECT $camposselect  ";
		$q .= " FROM $from ";
		$q .= " WHERE $where ";
		if ($orderby != "" ){
			$q .= " ORDER BY  $orderby ";
		}

		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());//die
		$numElementos = mysql_num_rows($resQuery);

		$camporesp = "";
		$arrresp = array();

		if ($numElementos > 0) {


			$cta = 0;
			while($rowsQuery = mysql_fetch_array($resQuery))
			{
				foreach( $rowsQuery as $key => $value){
					$arrresp[$cta][$key] = $value;
				}


				$cta++;
			}

		}
		return $arrresp;

	} //function selectEnArregloquery( $camposelect , $from , $where  )


	function queryAArreglo( $query ) {
		global $conex;
		$q = $query ;


		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numElementos = mysql_num_rows($resQuery);

		$camporesp = "";
		$arrresp = array();

		if ($numElementos > 0) {


			$cta = 0;
			while($rowsQuery = mysql_fetch_array($resQuery))
			{
				foreach( $rowsQuery as $key => $value){
					$arrresp[$cta][$key] = $value;
				}


				$cta++;
			}

		}
		return $arrresp;

	} //function queryAArreglo( $query )



	function abrirDlgIngresoManual ($cantidad , $codigo , $vlrunitario , $fechaentrega ){



		$html = "<table align='center' width='100%'>";

		$html .= "<tr class='fila1' >";

		$html .= "<td>Cantidad</td>";
		$html .= "<td>Codigo</td>";
		$html .= "<td>Vlr. Unitario</td>";
		$html .= "<td>Fecha Entrega<br>(Consumo)</td>";
		$html .= "</tr>";

$html .= "<tr class='fila2' >";
		$html .= "<td>";
		$varfecha = "Cantidad_IngManual";
		$html .= " <input type='text' id='".$varfecha."' name='".$varfecha."' readOnly='readOnly' value='".$cantidad."'> ";
		$html .= "</td>";

		$html .= "<td>";
		$varfecha = "Codigo_IngManual";
		$html .= " <input type='text' id='".$varfecha."' name='".$varfecha."' readOnly='readOnly' value='".$codigo."'> ";
		$html .= "</td>";

		$html .= "<td>";
		$varfecha = "VlrUnitario_IngManual";
		$html .= " <input type='text' id='".$varfecha."' name='".$varfecha."' readOnly='readOnly' value='".$vlrunitario."'> ";
		$html .= "</td>";

		$html .= "<td>";
		$varfecha = "FEntrega_IngManual";
		$html .= " <input type='text' id='".$varfecha."' name='".$varfecha."' readOnly='readOnly' value='".$fechaentrega."'> ";
		$html .= "</td>";
$html .= "</tr>";


		$html .= "</table>";






		return $html ;

	}//function abrirDlgIngresoManual

	
	
	//6 de agosto 2019 mostrar en un dialogo la informacion de facturacion 
	function infoxdlgfacturacion( $cta , $nroPrescripcion , $vtipotec , $vconsecutivo ,$codtecnologia , $cantidad , $tipoid , $numid , $eps , $niteps , $numfactura , $cuotamoderadora , $coopago ,  $vlrunitario , $vlrtotal ,  $numerodeentrega  , $vID , $vIDFacturacion ){
		global $conex;
		$html = "";



		global $wbasedatomipres;

		if (  ($tipoid == "") || ($numid == "") ){

			$wherestr = "Prenop = '".$nroPrescripcion."' ";
			$vselectpre = "Preidp,Pretip" ;
			$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000001", $wherestr); //informacion de la prescripcion

			if ( count($arrFila) > 0){
				//
				$numid = $arrFila['Preidp'];
				$tipoid = $arrFila['Pretip'];
			}

		}


		$html .= "<table align='center' width='100%'>";

		$html .= "<tr class='fila1' >";

		$html .= "<td>Prescripcion</td>";
		$html .= "<td>Tipo</td>";		
		$html .= "<td>Item #</td>";
		$html .= "<td>Tipo Doc.</td>";
		$html .= "<td>Numero Doc.</td>";
		$html .= "<td># Entrega</td>";

		$html .= "<td>Numero Fact.</td>";		
		
		$html .= "<td>Cod. EPS</td>";
		$html .= "<td>Nit EPS</td>";

		$html .= "<td>Cod.Tecnologia</td>";

		$html .= "<td>Cantidad</td>";

		$html .= "<td>Vlr. Unitario</td>";
		$html .= "<td>Vlr. Total</td>";
		$html .= "<td>Cuota Moderadora</td>";
		$html .= "<td>Coopago</td>";
	
		$html .= "</tr>";

		$html .= "<tr class='fila2' >";


		
		$html .= "<tr class='fila2' >";

		$html .= "<td>$nroPrescripcion</td>";
		$html .= "<td>$vtipotec</td>";		
		$html .= "<td>$vconsecutivo</td>";
		$html .= "<td>$tipoid</td>";
		$html .= "<td>$numid</td>";

		$html .= "<td>$numerodeentrega</td>";

				
		$html .= "<td>$numfactura</td>";		
		
		$html .= "<td>$eps</td>";
		$html .= "<td>$niteps</td>";

		$html .= "<td>$codtecnologia</td>";

		$html .= "<td>$cantidad</td>";

		$html .= "<td>$vlrunitario</td>";
		$html .= "<td>$vlrtotal</td>";
		$html .= "<td>$cuotamoderadora</td>";
		$html .= "<td>$coopago</td>";
	
		$html .= "</tr>";


		$html .= "</table>";

		
		$varestadofact = 1;// "estadofact_".$cta;//$i;
		$selected1 = "";
		$selected2 = "";
		$selected0 = "";
$estadofacturacion = $varestadofact;// 1;//pendiente pasar por parametro

		switch ($estadofacturacion) {
			case 1:
				$selected1 = "selected";
				break;
			case 2:
				$selected2 = "selected";
				break;
			case 0:
				$selected0 = "selected";
				break;
			default:
				$selected1 = "selected";
				break;
		}
		
		$html .= "<table align='center' width='100%'>";
		 
		$html .= "<tr class='fila1' >";
		$poslinea = $cta + 1;
		$html .= "<td>Elemento de la busqueda: $poslinea</td>";
		$html .= "<td>ID Principal : $vID</td>";
		$vdivIDFacturacion =   crearidcampovalor("IDFacturacionDlg-" , $vIDFacturacion , $cta  );
		
		//$vdivIDFacturacion =  "<div id = \"IDFacturacionDlg-".$cta."\" >$vIDFacturacion</div>";	
		$html .= "<td>ID de Facturacion : $vdivIDFacturacion</td>";
		$html .= "</tr >";
	

		$html .= "<tr class='fila2' >";
		$html .= "<td>";
		$html .= " <select name='".$varestadofact."' id='".$varestadofact."'  style='width:90%;display:inline;' >";
		$html .= "	<option value='1'>Estado</option>";//si no se selecciona nada dejarlo como activo por defecto
		$html .= "  <option value='1' $selected1 >Activo</option> ";
		$html .= "  <option value='2' $selected2 >Procesado</option> ";
		$html .= "  <option value='0' $selected0 >Anulado</option> ";
		$html .= "  </select>	";
		
		$html .= "</td>";

		$arrfacturacion = creararrfacturacion ($nroPrescripcion , 	$vtipotec ,$vconsecutivo , $tipoid , $numid ,	 $numerodeentrega , $codtecnologia , $cantidad ,  $vlrunitario ,$eps , $niteps , $numfactura ,  $cuotamoderadora , $coopago);
		$varrfactatxt = arregloatexto($arrfacturacion);

		$vfacturaciontxt = arregloatablahtml($arrfacturacion);
		$varrjsonatxt = json_encode($arrfacturacion);

//1
//		$vtxdatafacturacion .= "<input type='hidden' id='infofacturacionmipres_".$cta."' value='".$varrjsonatxt."'>";//aqui se guarda el json con la informacion que se enviara al ws.
$vtxdatafacturacion = "";

		$varbotonFact = "bEnviarFacturacion_".$cta;
		$varbotonverFact = "bverFacturacion_".$cta;

		if ( $vIDFacturacion != 0 ){//ya se envio la facturacion
			$wherestr = "SumIDf = '".$vIDFacturacion."' ";
			$vselectpre = "Sumjfa" ;
			$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000037", $wherestr); //informacion de la prescripcion

			if ( count($arrFila) > 0){
				//
				$vfacturaciontxt2 = $arrFila['Sumjfa'];
				if ( $vfacturaciontxt2 != "" ){					
				
					
					$arrfacttmp = json_decode( $vfacturaciontxt2 , true );
					$vfacturaciontxt = arregloatablahtml($arrfacttmp);
				}

			}
			
		}
	//	$botonverfacturacion = "<input type='button' id='".$varbotonverFact."' value='Ver Facturacion.' onclick='verinfo(\"" . $vfacturaciontxt . "\"  );'>";//&#9998; caracter de lapiz (editar)
		$botonverfacturacion = "<input type='button' id='".$varbotonverFact."' value='Ver Facturacion.' onclick='verinfofacturacion(". $cta . ",\"" . $vfacturaciontxt . "\"  );'>";//&#9998; caracter de lapiz (editar)

		$botonfacturacion = "<input type='button' id='".$varbotonFact."' value='Enviar Facturacion' onclick='enviarfacturacion(" . $cta . "  );'>";//&#9998; caracter de lapiz (editar)

		$botoncopiarinforact = "<input type='button' id='".$varbotonFact."' value='Copiar Informacion' onclick='copiarinformacionfact(" . $cta . "  );'>";//&#9998; caracter de lapiz (editar)

		$html .= "<td colspan='2' >";
		$html .= " $botonverfacturacion  $botonfacturacion $vtxdatafacturacion $botoncopiarinforact ";//27 mayo 2019
		$html .= "</td>";
		
		$html .= "</tr>";
		$html .= "</table>";
		
	//$varrfactatxt
		

		
		
		return $html;
	}//function infoxdlgfacturacion
	
	function arregloatexto( $arreglo ){
		$txt = "";
		foreach( $arreglo as $key => $value ){
			$txt .= "\"$key\" : ";
			if (is_string($value)){
				$txt .= " \"$value\" " ;
			}else{
				$txt .= $value;
			}
			$txt .= "\n";
		}
		return $txt;
		
	}
	//Genera el html para crear una tabla (html) con toda la informacion de la programacion de un mipres.
	function infoxdlgprogramacion( $nroPrescripcion , $vtipotec , $vconsecutivo , $canttotal , $codsede , $codtecnologia , $numerodeentrega ,$filapaginaprincipal  ,$tipoIDprov , $numIDprov ,$wbasedatomipres )
	{

	//	global $wbasedatomipres;
		global $conex;

		$qselect  = "  Sumnen , SumID , SumIDd , SumIDp , SumIDe , SumIDr ";
		$qselect  .= "  ,Sumcan , Sumcae , Sumcst ,Sumcoe ,Sumfmx , Sumedi, Sumepr , Sumeen , Sumere ,Sumcsp ";
		$qfrom  = $wbasedatomipres . "_000037 ";
		$qwhere  = " Sumnop = '" . $nroPrescripcion . "' ";//numero de mipres
		$qwhere .= "       AND Sumtip = '" . $vtipotec . "' ";//tipo de mipres
		$qwhere .= "       AND Sumcon = '" . $vconsecutivo . "' ";//consecutivo de tecnologia en el mipres
		if ($numerodeentrega != 0){//3 de julio 2019 , numero de entrega como parte de la clave principal
			$qwhere .= "       AND Sumnen = '".$numerodeentrega."' ";
		}
		$orderby  = "";

		$q = " SELECT " . $qselect . " FROM " . $qfrom . " WHERE " . $qwhere;
	//	$q = " SELECT " . $qselect . " FROM " . $qfrom . " LIMIT 10 ";// " WHERE " . $qwhere;
		if ($orderby != ""){
			$q .= " ORDER BY " .  $orderby ;
		}
//		var_dump($q);


/*
	//	$numfilas = insertupdatemipressuministro($arrtemp)

		$wherestr = "Prenop = '".$numprescripcion."' ";
		$vselectpre = "Prefep,Prehop,Pretip,Preidp,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps" ;//,Epsdes";
		$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000001", $wherestr); //informacion de la prescripcion


		$wherestr = "Prenop = '".$numprescripcion."' ";
		$vselectpre = "Prefep,Prehop,Pretip,Preidp,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps" ;//,Epsdes";
		$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000001", $wherestr); //informacion de la prescripcion

		if ( count($arrFila) > 0){
			$fechaMipres = $arrFila['Prefep'];
			$tipoID = $arrFila['Pretip'];
		}

	//	$arrresult = selectEnArregloquery( $qselect , $qfrom , $qwhere , $orderby );
*/

		$html = "";
		$vfecha=date("Y-m-d");
		$vmessiguiente = date("Y-m-d",strtotime($vfecha."+ 7 days"));


		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$cta = 0;
		$htmlquery = "";
		$vidprogramacion = 0 ;
		$idmipres =0 ;
		while($rowsQuery = mysql_fetch_array($resQuery))
		{
			$idmipres =0 ;
			$vidprogramacion = 0 ;
			$videntrega = 0 ;
			$cantidad = 0 ;
			$fechamax = "";
			$estadoprogramacion = 1;

			$idmipres = $rowsQuery['SumID'];
			$vidprogramacion = $rowsQuery['SumIDp'];
			$videntrega = $rowsQuery['SumIDe'];
			$cantidad = $rowsQuery['Sumcan'];
			if ($rowsQuery['Sumcae'] != 0){
				$cantidad = $rowsQuery['Sumcae'];
			}

			$fechamax = $rowsQuery['Sumfmx'];
			$estadoprogramacion = $rowsQuery['Sumepr'];

			$htmlquery .= "<tr class='fila1' >";
//canttotal
//			$htmlquery .= filatablalistadoprogramacion( $cta , $idmipres , $vidprogramacion , $videntrega , $cantidad , $fechamax , $estadoprogramacion,$nroPrescripcion,$vtipotec,$vconsecutivo,$canttotal,$codsede,$codtecnologia , $filapaginaprincipal  ,$tipoIDprov , $numIDprov  );
			$htmlquery .= filatablalistadoprogramacion( $cta , $idmipres , $vidprogramacion , $videntrega , $canttotal , $fechamax , $estadoprogramacion,$nroPrescripcion,$vtipotec,$vconsecutivo,$canttotal,$codsede,$codtecnologia , $filapaginaprincipal  ,$tipoIDprov , $numIDprov  );
			$htmlquery .= "</tr>";


			$cta++;
		}






		$html .= "<table align='center' width='100%'>";

		$html .= "<tr class='fila1' >";

		$html .= "<td>";
		$html .= "Prescripcion";
		$html .= "</td>";

		$html .= "<td>";
		$html .= "Tipo";
		$html .= "</td>";

		$html .= "<td>Orden</td>";
		$html .= "<td># de Entrega</td>";


		$html .= "<td>Cantidad</td>";
		$html .= "<td>Tecnologia</td>";
		$html .= "<td>Cod.Sede</td>";

		$html .= "</tr>";

		$html .= "<tr class='fila1' >";


		$html .= "<td> $nroPrescripcion </td> ";

		$html .= "<td> $vtipotec </td> ";
		$html .= "<td> $vconsecutivo </td>";
		$html .= "<td> $numerodeentrega </td>";

		
		
		$botonenvio = "   <input type='button' id='bEditarcodsede' value='&#9998;' onclick='actualizarcodigosede( );'>";//&#9998; caracter de lapiz (editar)
		$paramxtecnologia = "\"$idmipres\" , \"$vidprogramacion\" , \"$codtecnologia\" , \"$iddivtecnologiaprogramacion\" , \"$filapaginaprincipal\" ";

		$botontecnologia = "";
		if ( $vidprogramacion == 0 ){//no se ha programado todavia
			$botontecnologia = "   <input type='button' id='bEditartecnologia' value='&#9998;' onclick='actualizartecnologiaprogramacion( ". $paramxtecnologia. " );'>";//&#9998; caracter de lapiz (editar)
			
		}

		$html .= "<td><id id='idcantidadtotalprogramacion'> ".$canttotal."</id> </td>";//OK
//

		$html .= "<td><id id='iddivtecnologiaprogramacion'> " . $codtecnologia . "</id>$botontecnologia</td>";//poder editar la  tecnologia en la programacion

		$html .= "<td><id id='idcodigosedeprogramacion'> ".$codsede."</id></td>";//8 de Noviembre 2019 Freddy Saenz se inactiva  $botonenvio 


		$html .= "</tr>";

		$html .= "</table>";



		$html .= "<table  align='center' id ='tablaprogramaciones' width='100%' >";
		$html .= "<tr class='fila1' >";

		$html .= "<td>Fila</td>";//9 de Julio 2019 aparecia Entrega , pero crea confusion para el usuario
		$html .= "<td>ID</td>";
		$html .= "<td>ID-Programacion</td>";
		//$html .= "<td>Anular</td>"; no se realizara la anulacion en esta ventana , se realizara en la ventana principal .

		$html .= "<td>ID-Entrega</td>";

		$html .= "<td colspan='2'>Cantidad</td>";//2 columnas

		$html .= "<td>Fecha Maxima de Entrega</td>";

		$html .= "<td>Estado Programacion</td>";

		$html .= "<td>Programar</td>";//columna para el boton de envio

		$html .= "</tr>";

		if ($htmlquery != ""){
			$html .= $htmlquery;
		}
		$cantidad  = $canttotal;

		$maxfilas = 12;


		$html .= "</table>";
		$parametrosprog = " \"$nroPrescripcion\" , \"$vtipotec\" , \"$vconsecutivo\" , \"$canttotal\" , \"$codsede\" , \"$vID\" , \"$codtecnologia\" ";
		//28 mayo , no se necesita .$html .=  "<input type='button' id='bGrabarProgramacion_' value='Grabar la Programacion' onclick='grabarprogramacion( ".$parametrosprog. " );'>";

		return $html;


	}//function infoxdlgprogramacion( $nroPrescripcion , $vtipotec , $vconsecutivo , $canttotal , $codsede , $codtecnologia , $numerodeentrega ,


	//Genera una de la tabla de programaciones de un mipres
	function filatablalistadoprogramacion ( $i , $idmipres , $vidprogramacion , $videntrega , $cantidad , $fechaprog , $estadoprogramacion,$nroPrescripcion,$vtipotec,$vconsecutivo,$canttotal,$codsede,$codtecnologia , $filalistadoprogramacion ,$tipoIDprov , $numIDprov  ){

		$maxfilas = 12;

		$divEntregaDlg = "<div id='iddlgprogramacionentrega_" . $i . "' > ". ($i+1) ."</div> ";
		$html = "<td>" . $divEntregaDlg . "</td>";

		$divID = "<div id='IDdlgprogramacion_" . $i . "' > ". $idmipres ."</div> ";//consecutivo de linea
		$divProgramacion = "<div id= 'IDprogramaciondlgprogramacion_".$i."' > ".$vidprogramacion." </div> ";
		$divEntrega = "<div id= 'IDentregadlgprogramacion_" . $i . "' > " . $videntrega . " </div> ";

		$html .= "<td> " . $divID . " </td>";
		$html .= "<td> " . $divProgramacion . " </td>";

		$html .= "<td> " . $divEntrega . " </td>";


		$varcantidad = "idcantidadprog_".$i;
	//campo que permite drag y drop para poder distribuir la cantidad total en varias entregas.
//		$html .= "<td> <input type='text' id='".$varcantidad."' name='".$varcantidad."' readOnly='readOnly' value='".$cantidad."'  ondrop='drop(event)' ondragover='allowDrop(event)' draggable='true' ondragstart='drag(event)'>  </td>";
		$html .= "<td> <input type='text' id='".$varcantidad."' name='".$varcantidad."' readOnly='readOnly' value='".$cantidad."' >  </td>";



		$html .= "<td> </td> ";//no se modifica la ultima cantidad .


		$html .= "<td>";
		$varfecha = "fechaprogramacion_".$i;
		$html .= " <input type='text' id='".$varfecha."' name='".$varfecha."' readOnly='readOnly' value='".$fechaprog."'> ";


		$html .= "</td>";



		$html .= "<td>";
		$varestadoprog = "estadoprog_".$i;
		$selected1 = "";
		$selected2 = "";
		$selected0 = "";


		switch ($estadoprogramacion) {
			case 1:
				$selected1 = "selected";
				break;
			case 2:
				$selected2 = "selected";
				break;
			case 0:
				$selected0 = "selected";
				break;
			default:
				$selected1 = "selected";
				break;
		}
		$html .= " <select name='".$varestadoprog."' id='".$varestadoprog."'  style='width:90%;display:inline;' >";
		$html .= "	<option value='1'>Estado</option>";//si no se selecciona nada dejarlo como activo por defecto
		$html .= "  <option value='1' $selected1 >Activo</option> ";
		$html .= "  <option value='2' $selected2 >Procesado</option> ";
		$html .= "  <option value='0' $selected0 >Anulado</option> ";
		$html .= "  </select>	";

		$arrprogramacion = creararrxprogramacion ($idmipres ,$fechaprog ,$tipoIDprov , $numIDprov , $codsede , $codtecnologia , $cantidad	);

		$vprogramaciontxt = arregloatablahtml($arrprogramacion);
		$vprogramaciontxt = str_replace("'" , "\"", $vprogramaciontxt);

		$html .= "</td>";

		$varbotonProg = "bEnviarPrograma_".$i;
		$varbotonverProg = "bverProgramacion_".$i;
		$varparametros = " $i  ,  \"$idmipres\"  , \"$tipoIDprov\" , \"$numIDprov\" , \"$codsede\" , \"$codtecnologia\" , \"$filalistadoprogramacion\"   ";

	//	$varparametros = $i . " , " . " \"$idmipres\"" . " , \"" .$tipoIDprov . "\" , \"" . $numIDprov . "\" , \"" .$codsedeprov . "\" , \"" . $codservxmipres."\" ";

		$botonverprogramacion = "<input type='button' id='".$varbotonverProg."' value='Ver Progr.' onclick='verprogramacionendlg(" . $varparametros . "   );'>";//&#9998; caracter de lapiz (editar)

		$botonprogramacion = "<input type='button' id='".$varbotonProg."' value='Programar' onclick='enviarprogramacion(" . $varparametros . "  );'>";//&#9998; caracter de lapiz (editar)
		$html .= "<td> $botonverprogramacion <br> ".$botonprogramacion."</td> ";//27 mayo 2019

		return $html;


	}//function filalistadoprogramacion ($i , $idmipres , $vidprogramacion , $videntrega , $cantidad , $fechaprog , $estadoprogramacion )


	//$txtprogramacion = $arrpartes[0] de la forma = consecutivo (subentrega , cantidad , fecha maxima entrega y estado de la programacion)
	// ver javascrip grabarprogramacion
	function grabarProgramacionDB ( $txtprogramacion , $vID , $numprescripcion , $vconsecutivo ,  $vtipotec , $codtecnologia  ){
		global $wsedemipres;
		$resp = $txtprogramacion;
		$arrpartes = explode("+" , $txtprogramacion );
		if ( count($arrpartes) < 3 ){
			return "Falta Informacion";
		}
		$subentrega = $arrpartes[0];
		$cantidad = $arrpartes[1];
		$fechamaxima = $arrpartes[2];
		if ( count ($arrpartes)  == 3){
			$estadoprog = 1;
		}else if  ($arrpartes[3] == ""){
			$estadoprog = 1;
		}else{
			$estadoprog = $arrpartes[3];
		}



		$arrregistro = array ();

		$arrregistro["Sumnop"] = $numprescripcion;
		$arrregistro["Sumcon"] = $vconsecutivo;
		$arrregistro["Sumtip"] =  $vtipotec;//OJO PRETIP no corresponde a Sumtip
		//$arrregistro["Sumnen"] = 1;// cambiar por el valor, por ahora todas seran la entrega uno
		$arrregistro["Sumnse"] = intval($subentrega) + 1 ;// numero de subentrega
		$arrregistro["SumID"] = $vID;
		$arrregistro["Sumepr"] = $estadoprog;
		$arrregistro["Sumest"] = "on";
		/*

		$arrregistro["Sumcan"] = $cantidad;//no se actualizan estos campos.
		$arrregistro["Sumfmx"] = $fechamaxima;
*/

		$arrregistro["Sumcsp"] = $wsedemipres;//sede del mipres
		$arrregistro["Sumcst"] = $codtecnologia;// codigo de la tecnologia


		$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $arrregistro )	;
		$numfilas = insertupdatemipressuministro($arrtemp);
		if ($numfilas != 0){
			$resp = "OK "+ $subentrega;

		}
		return $resp;
	}//function grabarProgramacionDB ( $txtprogramacion , $vID , $numprescripcion , $vconsecutivo ,  $vtipotec , $codtecnologia  )


function textojsonaarreglo ($texto)
{
	$arrenvio = array();
	$vtxtarrenvio = str_replace("\\","",$texto);
	$arrenvio2 = json_decode($vtxtarrenvio , true);//de texto a arreglo.
//4 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto
	foreach($arrenvio2 as $clave => $valor){
		$arrenvio[$clave] = $valor;
	}

	return $arrenvio;

}//function textojsonaarreglo ($texto)






	function ftest($who)
	{

		$url = "https://wsmipres.sispro.gov.co/WSSUMMIPRESNOPBS/api/DireccionamientoXFecha/800067065/0Xh6gijRilikxCwftRSS1xRciWzR2PC8oHZUi24cXmM=/2019-03-07";

		$result = file_get_contents($url, false);
		$jsonMipres = "---";

		$tmp = "";

		if ($result != false) {
			$jsonMipres = json_decode($result , true);
//15 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto
			if (count($jsonMipres) > 0) {

				// comienzo

				foreach($jsonMipres as $key => $value) {

					$tmp .= "Prescripcion ".$value -> NoPrescripcion;
					$tmp .= " <br>Tipo de tecnologia  ".$value -> TipoTec;
					$tmp .= " <br>consecutivo ".$value -> ConTec;
					$tmp .= " <br>tipo id ".$value -> TipoIDPaciente;
					$tmp .= " <br> numero id ".$value -> NoIDPaciente;
					$tmp .= " <br> numero de entrega ".$value -> NoEntrega;
					$tmp .= " <br> numero de subentrega ".$value -> NoSubEntrega;
					$tmp .= " <br> tipo id proveedor ".$value -> TipoIDProv;
					$tmp .= " <br< numero id proveedor ".$value -> NoIDProv;
					$tmp .= " <br> codigo de municipio  ".$value -> CodMunEnt;
					$tmp .= " <br> fecha maxima entrega ".$value -> FecMaxEnt;
					$tmp .= " <br> cantidad total a entregar ".$value -> CantTotAEntregar;
					$tmp .= " <br> direccion paciente ".$value -> DirPaciente;
					$tmp .= " <br> cod. servicio a entregar ".$value -> CodSerTecAEntregar;
					$tmp .= " <br> no id eps ".$value -> NoIDEPS;
					$tmp .= " <br> cod. eps ".$value -> CodEPS;
					$tmp .= " <br> fecha de direccionamiento ".$value -> FecDireccionamiento;
					$tmp .= " <br> estado direccionamiento ".$value -> EstDireccionamiento;
					$tmp .= " <br> fecha de anulacion ".$value -> FecAnulacion;
					$tmp .= "  ";
					$tmp .= "  ----------------------------  ";
					foreach($value as $key1 => $value1) {
						$tmp .= " clave ".$key1.
						" valor ".$value1.
						" ";
						$tmp .= "  ---------------------------- ";
						//$tmp .= " clave " . $key1 . " valor " . $value1;
						if ($key1 == "NoPrescripcion") {

						}
						elseif($key1 == "TipoTec") {

						}
						elseif($key1 == "ConTec") {

						}
						elseif($key1 == "TipoIDPaciente") {

						}
					}

				}
				$jsonMipres = $tmp;

				////fin

			} else {
				$jsonMipres = " la cuenta ".count($jsonMipres);
			}

		}
		return $jsonMipres;
	} //function ftest($who){


	function fbuscarxdireccionamiento($fecha)
	{

		global $wemp_pmla;

		$url2 = get_url_x_fecha("DireccionamientoXFecha", $fecha, $wemp_pmla);
		$arrinfo =  llamarws_x_curl("GET", $url2, "");//file_get_contents($url2, false);


		return $arrinfo;

	}//function fbuscarxdireccionamiento()

	function fbuscarxprogramacion($fecha)
	{
		global $wemp_pmla;

		$url2 = get_url_x_fecha("ProgramacionXFecha", $fecha , $wemp_pmla);
		$arrinfo =  llamarws_x_curl("GET", $url2, "");//file_get_contents($url2, false);


		return $arrinfo;

	}//function fbuscarxprogramacion()

	function fbuscarxentrega($fecha)
	{
		global $wemp_pmla;

		$url2 = get_url_x_fecha("EntregaXFecha", $fecha , $wemp_pmla);
		$arrinfo =  llamarws_x_curl("GET", $url2, "");//file_get_contents($url2, false);


		return $arrinfo;

	}//function fbuscarxentrega()


	function fbuscarxreportedeentrega($fecha)
	{
		global $wemp_pmla;

		$url2 = get_url_x_fecha("ReporteEntregaXFecha", $fecha , $wemp_pmla);
		$arrinfo =  llamarws_x_curl("GET", $url2, "");//file_get_contents($url2, false);



		return $arrinfo;

	}//function fbuscarxreportedeentrega()


	function fbuscarxfacturaenviada($fecha)
	{
		global $wemp_pmla;

		$url2 = get_url_x_fecha_facturacion("FacturacionXFecha", $fecha , $wemp_pmla);
		$arrinfo2 =  llamarws_x_curl("GET", $url2, "");//file_get_contents($url2, false);
		$contador = 0;
		$arrinfo = array();
		if (is_array ($arrinfo2 ) ) {
				
			foreach( $arrinfo2 as $key => $value){//listado de elementos
				foreach( $value as $key2 => $value2){//listado de elementos
					if ($key2 == "ID"){//no se puede usar el mismo ID porque borra el ID de suministros.
						$arrinfo["$contador"]["ID2"] = $value2;
						$arrinfo["$contador"]["ID"] = "";
					}
					$arrinfo["$contador"][$key2 ] = $value2;
				}
				$contador++;

			}
		}

		
		return $arrinfo;

	}//function fbuscarxreportedeentrega()
	
	
	function tablaxtipotec($vtipotec){
		global $wbasedatomipres;

		$tabla = "";
		$tabla = $wbasedatomipres."_000037";
		/*
		switch ($vtipotec) {
			case 'M':
				$tabla = $wbasedatomipres."_000002";
				break;
			case 'P':
				$tabla = $wbasedatomipres."_000004";
				break;
			case 'D':
				$tabla = $wbasedatomipres."_000005";
				break;
			case 'N':
				$tabla = $wbasedatomipres."_000006";
				break;
			case 'S':
				$tabla = $wbasedatomipres."_000007";
				break;
			default:
				// code...
				break;
		}*/
		return $tabla;
	}//function tablaxtipotec($vtipotec)

	function clavemipresdetalle($vtipotec , $numprescripcion , $vconsecutivo , $vID ){//21 mayo agregado el parametro $vID
		$key = "";

		if ($vID == 0){
			$key = " Sumnop = '" . $numprescripcion . "' AND Sumcon = '" . $vconsecutivo . "' AND Sumtip = '" . $vtipotec . "' ";
		}else{
			$key = " SumID = '" . $vID . "'  ";//es la clave principal de las prescripciones

		}


		return $key;

	}//function clavemipresdetalle($vtipotec , $numprescripcion , $vconsecutivo)

//3 Julio 2019 ,
//Genera la clave principal de busqueda dentro de la tabla mipres_000037
	function claveppalmipres( $vID , $vtipotec , $numprescripcion , $vconsecutivo , $nroentrega  ){

		$key = "";

		if ($vID == 0){
			$key = " Sumnop = '" . $numprescripcion . "' AND Sumcon = '" . $vconsecutivo . "' AND Sumtip = '" . $vtipotec . "' ";
			$key .= " AND Sumnen = '".$nroentrega."' ";
		}else{
			$key = " SumID = '" . $vID . "'  ";//es la clave principal de las prescripciones

		}


		return $key;

	}//function claveppalmipres( $vID , $vtipotec , $numprescripcion , $vconsecutivo , $nroentrega  )

	
	//Poder dejar el Nit que trae la consulta , sin el diferenciador que a veces se le agrega
	//Ej : 900156264NP dejarlo como 900156264
	function extraernumeroxnit ( $numerostr ){


		$cadena = $numerostr ;
		$resp = "" ;

		for ( $i=0 ; $i < strlen($cadena) ; $i++ ){
			if ( ( $cadena[$i] >= "0" ) && ( $cadena[$i] <= "9" ) )
			{
				$resp .= $cadena[$i];
			}else{//solo caracteres numericos , si llega uno diferente , terminar de leer y retornar lo leido.
				break;
				//$resp .= $cadena[$i]; 
			}
			

		}
	
		return $resp;
	}



	function facturaycopagomipressintecnologia ( $numhistoria , $numingreso ){
		global $conex;
		global $wbasedatocliame;
		
		$q   = " SELECT Fenffa , Fenfac as Factura ,Fennit as nitesp , Fenval as vlrtotal , Fencop as coopago , Fencmo as cuotamoderadora";
		$q  .= "  FROM " . $wbasedatocliame . "_000018 ";//clisur o idc 
		$q  .= "  WHERE Fenhis = '" . $numhistoria . "' AND Fening = '" . $numingreso . "' ";


		$resQuery = mysql_query($q, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$cta = 0;
		$arrfacturasresp = array();
		
		while($rowsQuery = mysql_fetch_array($resQuery))
		{//
			$documento = $rowsQuery["Factura"];
			$arrfacturasresp[  $documento ] ["numero"] = $documento ;//documento es la factura
			$arrfacturasresp[  $documento ] ["coopago"] = $rowsQuery["coopago"] ;//  ;//valor del coopago
			$arrfacturasresp[  $documento ] ["cuotamoderadora"] =  $rowsQuery["cuotamoderadora"] ;//$rowsQuery["cuotamoderadora"] ;//cuota moderadora
			$arrfacturasresp[  $documento ] ["cantidad"] = 1 ;//cantidad del articulo (tecnologia)
			$arrfacturasresp[  $documento ] ["vlrunitario"] =   $rowsQuery["vlrtotal"] ; //$rowsQuery["vlrtotal"] ;//valor unitario
			$arrfacturasresp[  $documento ] ["vlrtotal"] = $rowsQuery["vlrtotal"] ;//$rowsQuery["vlrtotal"];//valor total no funciono number_format( 
			$arrfacturasresp[  $documento ] ["niteps"] =  extraernumeroxnit( $rowsQuery["nitesp"] );//8 de noviembre 2019 Freddy Saenz , quitar diferenciadores en el Nit
		}	
		return $arrfacturasresp;
		
	}
	
	function facturaycopagomipres ( $numhistoria , $numingreso , $codtecnologia , $codconcepto ,$vtipotec )
	{


		
		$arrfacturasresp = array();
		//$vtipotec viene como parametro
		$conex_o   = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");


		//Buscar las facturas que tiene el paciente
		$queryfamov  = " SELECT movfue, movdoc , carfacreg  , carfacval , movcer  ";//string , numerico , AQUI ESTAN LOS NUMEROS DE LAS FACTURAS
		if ($vtipotec != "P"){//no es un procedimiento
			$queryfamov  .= " , drodetcan as cantidad , drodetpre as vlrunitario , drodettot as vlrtotal ";
		}else{
			$queryfamov  .= " , 1 as cantidad , cardetvun as vlrunitario , cardettot as vlrtotal  ";
		}
		//
		$queryfamov .= " FROM famov , facarfac ";
		$queryfamov .= " , facardet  ";
		if ($vtipotec != "P"){//no es un procedimiento
			$queryfamov .= ", ivdrodet ";//buscar en ivdrodet el codigo del articulo aplicado al paciente
		}// si es un procedimiento solo buscar en facardet.
		
		$queryfamov .= " WHERE movhis = $numhistoria ";//numerico
		$queryfamov .= "  AND  movnum = $numingreso ";//numerico
		$queryfamov .= "  AND  movfuo = '01' ";//string
		$queryfamov .= "  AND  movanu = '0' ";//string
		$queryfamov .= "  AND  movfue = carfacfue ";//string
		$queryfamov .= "  AND  movdoc = carfacdoc ";//numerico
		$queryfamov .= "  AND  carfacanu = '0' ";//string

		$queryfamov .= "  AND  carfacreg = cardetreg ";// IN  ( $queryfacardet ) ";//string
		$queryfamov .= "  AND  cardethis = $numhistoria ";//numero de historia
		$queryfamov .= "  AND  cardetnum = $numingreso ";//e ingreso
		$queryfamov .= "  AND  cardetanu = '0' ";//cargo no anulado

		if ($vtipotec != "P"){
			$queryfamov .= "  AND  drodetanu = '0' ";//string
		
			$queryfamov .= "  AND  cardetfue = drodetfue ";//string
			$queryfamov .= "  AND  cardetdoc = drodetdoc ";//numerico
			$queryfamov .= "  AND  cardetite = drodetite ";//numerico
			$queryfamov .= "  AND  drodetart = '".$codtecnologia."' ";//A06A10 codigo del articulo
			$queryfamov .= "  AND  cardetcon IN ( ".$codconcepto." )";//medicamentos 0616

		}else{
			$queryfamov .= "  AND  cardetcod = '".$codtecnologia."' ";//codigo cups de la tecnologia

		}
		$numfactura = 0;
		$fuente = 0;
		$vlrcoopago = 0;
		$documento = 0;
		$niteps = "";
		
		$cta = 0;
		$err_o = odbc_do($conex_o , $queryfamov);
		$arrNumFacturas = array ();
		$cadIDstr = "";
		$respuestastr = "";
		$acumulacantidad = 0;
		$acumulavlrtotal = 0;
		
		while (odbc_fetch_row($err_o)){
			
			$fuente       = odbc_result($err_o , 'movfue');//fuente
			$documento    = odbc_result($err_o , 'movdoc');//documento
			$numregistro  = odbc_result($err_o , 'carfacreg');//numero de registro en la tabla de facturacion

			//$vlrunitario  = odbc_result($err_o , 'carfacval');//valor unitario
			$vlrxdevolucion = odbc_result($err_o , 'carfacval');//6 noviembre 2019 Freddy Saenz , Poder determinar los valores negativos para
			// ver si es una devolucion
			$vlrunitario  = odbc_result($err_o , 'vlrunitario');//valor unitario
			$vlrtotal     = odbc_result($err_o , 'vlrtotal');//valor total
			$niteps       = odbc_result($err_o , 'movcer');//nit de la eps
			$cantidad       = odbc_result($err_o , 'cantidad');//cantidad de articulos
			
			if ( $vlrxdevolucion < 0 ){
				$cantidad    *= -1 ;
				//$vlrunitario *= -1 ;
				
			}
			//el resultado del query es una seleccion de registros , donde se debe acumular la cantidad .
			$acumulacantidad += $cantidad;
			$acumulavlrtotal += ( $cantidad * $vlrunitario );
			
			$arrNumFacturas[$numregistro] = $documento;// guardar el numero de factura
			$arrfacturasresp[  $documento ] ["numero"] = $documento ;//documento es la factura
			$arrfacturasresp[  $documento ] ["coopago"] = 0 ;//valor del coopago
			$arrfacturasresp[  $documento ] ["cuotamoderadora"] = 0 ;//cuota moderadora
			//number_format($valormed, 2, ',', '.')// 
			
			$arrfacturasresp[  $documento ] ["cantidad"] = $acumulacantidad;///$cantidad ;//cantidad del articulo (tecnologia)
			$arrfacturasresp[  $documento ] ["vlrunitario"] = $vlrunitario ;// $ ;//valor unitario
			$arrfacturasresp[  $documento ] ["vlrtotal"] =  $acumulavlrtotal;// $;//$vlrtotal ;//$cantidad * $vlrunitario ;//valor total
			$niteps = str_replace(" ","",$niteps);//borrar los espacios
			$niteps = str_replace("\n","",$niteps);//borrar los espacios
			$niteps = str_replace("\r","",$niteps);//borrar los espacios
			$posmenos  = strpos($niteps, "-");
			if ( $posmenos > 0 ){
				$niteps = substr($niteps, 0 , $posmenos );// la longitud es $posmenos
			}
			$arrfacturasresp[  $documento ] ["niteps"] = extraernumeroxnit ( $niteps ) ;//nit de eps o responsable de la factura
			//8 de noviembre 2019 Freddy Saenz , quitar diferenciadores en el Nit
			
			
			$cta++;
			if ( $respuestastr != "" ) {
				$cadIDstr .= " , ";
				$respuestastr .= " , ";
			}
			$cadIDstr .= " $numregistro ";
			$respuestastr .= " $documento ";//numero de la factura
			

			
		}
		if ( $respuestastr == "" ){
			return  $arrfacturasresp;//arreglo vacio " 0 | 0 ";//pendiente enviar un arreglo
		}
		
	

		
		//aqui se calcula el coopago
		$querycacar  = " SELECT carval ,carfac , carced ";//coopagos
		// AQUI ESTAN LOS NUMEROS DE LAS FACTURAS y el nit de la eps o empresa responsable
		$querycacar .= " FROM cacar ";//EL QUERY SE HACE SOBRE EL NUMERO DE LAS FACTURAS
		
		$querycacar .= " WHERE carhis = $numhistoria ";//historia
		$querycacar .= "  AND  carnum = $numingreso ";//ingreso
		$querycacar .= "  AND  carfuo = '01' ";//string
		$querycacar .= "  AND  caranu = '0' ";//no anulado
		$querycacar .= "  AND  carfca = $fuente ";//fuente 20 , facturacion
		
		$querycacar .= "  AND  carfac IN  ( $respuestastr ) ";//facturas que se encontraron
		$querycacar .= "  AND  carfue = '38' ";//fuente 38 , son los coopagos

		$cta = 0;
		$err_o = odbc_do($conex_o , $querycacar );

		$niteps = "";
		while (odbc_fetch_row($err_o)){
			$numfactura  = odbc_result($err_o , 'carfac');
			$vlrcoopago  = odbc_result($err_o , 'carval');

			//$niteps      = odbc_result($err_o , 'movcer');//nit de la eps
			
		    $arrfacturasresp[  $numfactura ] ["coopago"] += $vlrcoopago;
			
		//	$arrfacturasresp[  $numfactura ] ["niteps"]   = $niteps;

		}


		odbc_close($conex_o);
		odbc_close_all(); 
		return $arrfacturasresp;//. "|" . $numfactura . "|" . $vlrcoopago ;
		
		
		
	}///function facturaycopagomipres

//Modificacion 27 Abril 2020 , Freddy Saenz
//Genera el reporte diario de mipres  , solicitado por el IDC
	function fReporteDiarioMipres ( $fechareporte , $fechahastareporte , $vsalidareporte , $separador )	{//17 junio , agregado segundo parametro , fechahastareporte

		global $wbasedatomipres;
		global $wbasedatocliame;


	
//MEDICAMENTOS	
		$q1M = " SELECT Prenop as Prescripcion, 'M' as Tipo , Prefep as Fecha ,Pretip as TipoID, Preidp as Identificacion,Prepnp as Nombre1 , Presnp as Nombre2 , Prepap as Apellido1 ,Presap as Apellido2 , Preeps as codEps ";
		$q1M .= " , Epsdes as EPS , Medcom as Item , Meddmp as CodTec , Ingtar as Tarifa, Meddmp as Tecnologia , Medctf as Cantidad , 0 as Valor ,0 as Subtotal , 0 as Acumulado ";//, '' as CodSerTecAEntregar 
		$q1M .= " , Concat(Prenop,'_M_',Medcom) as clave ";
		$q1M .= " FROM ".$wbasedatomipres."_000001 , mipres_000033 , ".$wbasedatomipres."_000002 , ".$wbasedatocliame."_000100 , " . $wbasedatocliame . "_000101 ";    
		$q1M .= " WHERE Prefep >= '".$fechareporte."'  ";
		$q1M .= " AND Prefep <= '".$fechahastareporte."'  ";
		$q1M .= " AND Preeps = Epscod AND Prenop = Mednop  ";
		$q1M .= " AND Pachis = Inghis ";
		$q1M .= " AND Pacdoc = Preidp ";
		$q1M .= " AND Ingfei >= Prefep  ";

//PROCEDIMIENTOS
		$q2P = " SELECT Prenop as Prescripcion, 'P' as Tipo , Prefep as Fecha ,Pretip as TipoID, Preidp as Identificacion,Prepnp as Nombre1 , Presnp as Nombre2 , Prepap as Apellido1 ,Presap as Apellido2 , Preeps as codEps ";
		$q2P .= " , Epsdes as EPS , Procop as Item , Procup as CodTec  , Ingtar as Tarifa , Cupdes as Tecnologia  ,Procat  as Cantidad  , 0 as Valor ,0 as Subtotal , 0 as Acumulado ";//, Procup as CodSerTecAEntregar
		$q2P .= " , Concat(Prenop,'_P_',Procop) as clave ";
		$q2P .= " FROM ".$wbasedatomipres."_000001 , mipres_000033 , ".$wbasedatomipres."_000004 , ".$wbasedatocliame."_000100 , " . $wbasedatocliame . "_000101 ";
		$q2P .= " , mipres_000025 ";//descripcion del procedimiento
		$q2P .= " WHERE Prefep >= '".$fechareporte."' ";
		$q2P .= " AND Prefep <= '".$fechahastareporte."'  ";
		$q2P .= " AND Preeps = Epscod  AND Prenop = Pronop   ";
		$q2P .= " AND Pachis = Inghis ";
		$q2P .= " AND Pacdoc = Preidp ";
		$q2P .= " AND Ingfei >= Prefep  ";
		$q2P .= " AND Cupcod = Procup  ";

//DISPOSITIVOS
		$q3D = " SELECT Prenop as Prescripcion,'D' as Tipo ,  Prefep as Fecha ,Pretip as TipoID, Preidp as Identificacion,Prepnp as Nombre1 , Presnp as Nombre2 , Prepap as Apellido1 ,Presap as Apellido2 , Preeps as codEps ";
		$q3D .= "  , Epsdes as EPS , Discod as Item  , Discdi as CodTec  , Ingtar as Tarifa  , Tdmdes as Tecnologia ,Discat  as Cantidad , 0 as Valor  ,0 as Subtotal , 0 as Acumulado   ";//, Discdi as CodSerTecAEntregar
		$q3D .= " , Concat(Prenop,'_D_',Discod) as clave ";
		$q3D .= " FROM ".$wbasedatomipres."_000001 , mipres_000033 , ".$wbasedatomipres."_000005 , ".$wbasedatocliame."_000100 , " . $wbasedatocliame . "_000101 ";
		$q3D .= " , mipres_000026 ";//descripcion del dispositivo
		$q3D .= " WHERE Prefep >= '".$fechareporte."'  ";
		$q3D .= " AND Prefep <= '".$fechahastareporte."'  ";
		$q3D .= " AND Preeps = Epscod AND Prenop = Disnop  ";
		$q3D .= " AND Pachis = Inghis ";
		$q3D .= " AND Pacdoc = Preidp ";
		$q3D .= " AND Ingfei >= Prefep  ";
		$q3D .= " AND Tdmcod = Discdi  ";

//NUTRICIONES
		$q4N = " SELECT Prenop as Prescripcion,'N' as Tipo ,  Prefep as Fecha ,Pretip as TipoID, Preidp as Identificacion,Prepnp as Nombre1 , Presnp as Nombre2 , Prepap as Apellido1 ,Presap as Apellido2 , Preeps as codEps ";
		$q4N .= " , Epsdes as EPS , Nutcon as Item , Nutdpn as CodTec , Ingtar as Tarifa , Lpnnom as Tecnologia,Nutctf  as Cantidad  , 0 as Valor ,0 as Subtotal , 0 as Acumulado  ";// , Nutdpn as CodSerTecAEntregar
		$q4N .= " , Concat(Prenop,'_N_',Nutcon) as clave ";
		$q4N .= " FROM ".$wbasedatomipres."_000001 , mipres_000033 , ".$wbasedatomipres."_000006 , ".$wbasedatocliame."_000100 , " . $wbasedatocliame . "_000101 ";
		$q4N .= " , mipres_000030 ";//descripcion de la nutricion
		$q4N .= " WHERE Prefep >= '".$fechareporte."'  ";
		$q4N .= " AND Prefep <= '".$fechahastareporte."'  ";
		$q4N .= " AND Preeps = Epscod AND Prenop = Nutnop  ";
		$q4N .= " AND Pachis = Inghis ";
		$q4N .= " AND Pacdoc = Preidp ";
		$q4N .= " AND Ingfei >= Prefep  ";
		$q4N .= " AND Lpncod = Nutdpn  ";

//SERVICIOS COMPLEMENTARIOS
		$q5S = " SELECT Prenop as Prescripcion,'S' as Tipo ,  Prefep as Fecha ,Pretip as TipoID, Preidp as Identificacion,Prepnp as Nombre1 , Presnp as Nombre2 , Prepap as Apellido1 ,Presap as Apellido2 , Preeps as codEps ";
		$q5S .= " , Epsdes as EPS , Sercos as Item  , Sercsc as CodTec  , Ingtar as Tarifa  , Sctdes as Tecnologia ,Sercat  as Cantidad , 0 as Valor  ,0 as Subtotal , 0 as Acumulado";//, Sercsc as CodSerTecAEntregars
		$q5S .= " , Concat(Prenop,'_S_',Sercos) as clave ";
		$q5S .= " FROM ".$wbasedatomipres."_000001 , mipres_000033 ,".$wbasedatomipres."_000007 , ".$wbasedatocliame."_000100 , " . $wbasedatocliame . "_000101 ";
		$q5S .= " , mipres_000028 ";//descripcion del servicio
		$q5S .= " WHERE Prefep >= '".$fechareporte."'  ";
		$q5S .= " AND Prefep <= '".$fechahastareporte."'  ";
		$q5S .= " AND Preeps = Epscod AND Prenop = Sernop ";
		$q5S .= " AND Pachis = Inghis ";
		$q5S .= " AND Pacdoc = Preidp ";
		$q5S .= " AND Ingfei >= Prefep  ";
		$q5S .= " AND Sctcod = Sercsc  ";

		$qunion = " $q1M UNION $q2P UNION $q3D UNION $q4N UNION $q5S ORDER BY Prescripcion , Item ";// ConTec ";

		//claveppalmipres($vID , $vtipotec , $numprescripcion , $vconsecutivo , $nroentrega);//3 de julio 2019
		$vselectpre = "Prenop, Prefep ,Pretip , Preidp ,Prepnp , Presnp , Prepap ,Prepap , Preeps , Epsdes ";
		//AND Preeps=Epscod
		$arrFila =  queryAArreglo($qunion); //informacion de la prescripcion
		if ( count( $arrFila ) >  0	){
			//$txt = json_encode($arrFila);
			//echo $txt;
			$arrFila = borrardupliacosarregloconclave( $arrFila ) ;//
			$arrFila = buscarvalorarticuloxarreglo( $arrFila ) ;
		
			
			if ( $vsalidareporte == "Archivo"){
				$arrFila = borrarcolumnasarreglo( $arrFila, [ 'clave'  ] ) ;//borra la columna clave ,para que no se mostrare en el reporte.
				$vfacturaciontxt = archivoatextocsv( $arrFila , $separador );
			}else{
				$arrFila = borrarcolumnasarreglo( $arrFila, [ 'clave' , 'codEps' ] ) ;//borra la columna clave ,para que no se mostrare en el reporte.
				$vfacturaciontxt = arreglofilasatablahtml($arrFila);
			}
			
			

		}else{
			$vfacturaciontxt = "";
			
		}
		
		return $vfacturaciontxt;

}//function fReporteDiarioMipres ( $fechareporte )	




//=======================================================================================================================================================
//  FIN FUNCIONES MIPRES WEBSERVICES SUMINISTROS
//=======================================================================================================================================================



//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================






if(isset($accion))
{

	global $wbasedatomipres;//20 junio 2019
	//global $wemp_pmla;
	global $errorenvioseleccion;
	global $wbasedatocliame ; //17 abril 2020
	global $wbasedato;//17 abril 2020



	switch($accion)
	{
		case "consultarinfofacturacionyreporte"://Imprimir la facturacion
			$vfacturaciontxt = "";
			if ( $vID != 0 ){//ya se envio la facturacion //( $vIDFacturacion != 0 )
				//numprescripcion : numprescripcion,//8 Noviembre 2019 , Freddy Saenz , para poder actualizar los ids por prescripcion .
									
				$fechainicial  = date("Y-m-d");
				$fechafinal    = $fechainicial;
				//cuando $numprescripcion tiene valor no se usan las fechas ( $fechainicial $fechafinal )
				//$arrprescripciones = actualizarIDsPrescripcionesPeriodo	( $wemp_pmla , $fechainicial , $fechafinal , $numprescripcion )	;		
			
			
			
			
				$nombreprestador = consultarAliasPorAplicacion($conex,$wemp_pmla , 'nombrePrestadorMipres');
			
				$wherestr = "SumID = '".$vID."' ";
				$vselectpre = "Sumjfa , Sumjre" ;//jsons de facturacion y de reporte de entrega
				$vselectpre .= " , SumIDe , SumIDp , SumIDd , SumIDf , SumID2 " ;//solicitud de que aparezcan todos los IDs al momento  de imprimir

				$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000037", $wherestr); //informacion de la prescripcion

				if ( count($arrFila) > 0){
					//
					$vfacturaciontxt2 = $arrFila['Sumjfa'];//este es el json de facturacion
					$vreporteentregatxt2 = $arrFila['Sumjre'];//este es el json de reporte de entrega
					$videntrega2020 = $arrFila['SumIDe'];//aqui esta el ID de entrega
					$vidprogramacion2020 = $arrFila['SumIDp'];//aqui esta el ID de programacion
					$viddireccionamiento2020 = $arrFila['SumIDd'];//aqui esta el ID de direccionamiento.
					$vidfacturacion2020 = $arrFila['SumIDf'];//aqui esta el ID de facturacion .
					$vidmipresfact2020 = $arrFila['SumID2'];//aqui esta el ID del modulo de facturacion .
					$vfacturaciontxt = "";
/* 					if ( $vfacturaciontxt2 != "" ){	
					//
						$vfacturaciontxt = "<head><title>$nombreprestador</title></head>"  . $vfacturaciontxt ;
						//16 Octubre 2019 Freddy Saenz , en el reporte mostrar ID y no ID2
						$vfacturaciontxt2 = str_replace("ID2" , "ID" , $vfacturaciontxt2 );//mostrar ID y no ID2
						
						$arrfacttmp = json_decode( $vfacturaciontxt2 , true );
						$vfacturaciontxt = "INFORME DE FACTURACION<br>" . arregloatablahtml($arrfacttmp);
					}
					if ( $vreporteentregatxt2 != "" ){
						if ( $vfacturaciontxt != "" ){
							$vfacturaciontxt .= "<p style='page-break-before: always'>";//salto de pagina
						}
						$vfacturaciontxt .= "<head><title>$nombreprestador</title></head>";
						$arrreportetmp = json_decode( $vreporteentregatxt2 , true );
						$vreporteentregatxt = arregloatablahtml($arrreportetmp);
						$vfacturaciontxt .= "<br>REPORTE DE ENTREGA<br>" . $vreporteentregatxt;
					} */
//15 DE ABRIRL 2020 , se solicita invertir el orden de impresion .Primero el reporte de entrega y despues el Informe de facturacion
					if ( $vreporteentregatxt2 != "" ){

						$vfacturaciontxt = "<head><title>$nombreprestador</title></head>";
						$arrreportetmp = json_decode( $vreporteentregatxt2 , true );

						if ( $viddireccionamiento2020 != 0 ){
							$arrreportetmp["IDDireccionamiento"] = $viddireccionamiento2020;
						}
						if ( $vidprogramacion2020 != 0 ){
							$arrreportetmp["IDIDProgramacion"] = $vidprogramacion2020;
						}
						if ( $videntrega2020 != 0 ){
							$arrreportetmp["IDEntrega"] = $videntrega2020;
						}
						if ( $vidmipresfact2020 != 0 ){
							$arrreportetmp["ID2-Fact"] = $vidmipresfact2020;
						}
						if ( $vidfacturacion2020 != 0 ){
							$arrreportetmp["IDFacturacion"] = $vidfacturacion2020;
						}

						//15 abril 2020 agregar los IDs pendientes de entrega , direccionamiento , programacion 
						$vreporteentregatxt = arregloatablahtml($arrreportetmp);
						
						$vfacturaciontxt .= "<br>REPORTE DE ENTREGA<br>" . $vreporteentregatxt;
					}
					if ( $vfacturaciontxt2 != "" ){	
					//
						if ( $vfacturaciontxt != "" ){
							$vfacturaciontxt .= "<p style='page-break-before: always'>";//salto de pagina
						}
						$vfacturaciontxt .= "<head><title>$nombreprestador</title></head>"   ;
						//16 Octubre 2019 Freddy Saenz , en el reporte mostrar ID y no ID2
						$vfacturaciontxt2 = str_replace("ID2" , "ID" , $vfacturaciontxt2 );//mostrar ID y no ID2
						
						$arrfacttmp = json_decode( $vfacturaciontxt2 , true );
						//15 abril 2020 agregar los IDs pendientes de entrega , direccionamiento , programacion 
						if ( $viddireccionamiento2020 != 0 ){
							$arrfacttmp["IDDireccionamiento"] = $viddireccionamiento2020;
						}
						if ( $vidprogramacion2020 != 0 ){
							$arrfacttmp["IDIDProgramacion"] = $vidprogramacion2020;
						}
						if ( $videntrega2020 != 0 ){
							$arrfacttmp["IDEntrega"] = $videntrega2020;
						}
						$arrfacttmp["ID1-Suministros"] = $vID;

						$vfacturaciontxt .= "INFORME DE FACTURACION<br>" . arregloatablahtml($arrfacttmp);
					}




				}
				
			}else{
				$vfacturaciontxt = "No se encontro informacion de la facturacion";
			}
			echo $vfacturaciontxt;
				

			break;
			
		case "consultarinfofacturacion"://Desde el dialogo de facturacion
			$vfacturaciontxt = "No se encontro informacion";
			if ( $vIDFacturacion != 0 ){//ya se envio la facturacion
			//$vIDFacturacion
				$wherestr = "SumIDf = '".$vIDFacturacion."' ";
				$vselectpre = "Sumjfa" ;
				$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000037", $wherestr); //informacion de la prescripcion

				if ( count($arrFila) > 0){
					//
					$vfacturaciontxt2 = $arrFila['Sumjfa'];
					if ( $vfacturaciontxt2 != "" ){		
						//16 Octubre 2019 Freddy Saenz , en el reporte mostrar ID y no ID2
						$vfacturaciontxt2 = str_replace("ID2" , "ID" , $vfacturaciontxt2 );//mostrar ID y no ID2
						
						$arrfacttmp = json_decode( $vfacturaciontxt2 , true );
						$vfacturaciontxt = arregloatablahtml($arrfacttmp);
					}else{
						$vfacturaciontxt = "JSON de Facturacion en Blanco";
					}

				}else{
					$vfacturaciontxt = "No hay JSON de Facturacion";
				}
				
			}
			echo $vfacturaciontxt;
		
			break;

		case "enviarFacturacionSeleccion":
			$errorenvioseleccion = "";
			//ENVIO DE FACTURACION
			//vlistajson
			$arrIDentregaJsons = explode("|", $cadidjsonenvio);//informacion de la entrega
			foreach($arrIDentregaJsons as $key => $value){
				$cad = str_replace("\\","",$value);
				if ($cad != ""){//16 julio 2016 Freddy Saenz , para evitar el error del foreach
					$respentrega = hacer_facturacion($cad);
					if (is_string($respentrega) ){//probablemente sea un error
						$errorenvioseleccion .= $respentrega;
					}else{//actualizar la informacion de la factura en el mipres
						
						
					}
				}
				
			}
			$arrinfo = buscarPrescripcionesSuministros($wemp_pmla,$fechaInicial,$fechaFinal,$tipDocPac,$docPac,$tipDocMed,$docMed,$codEps,$tipoPrescrip,$nroPrescripcion,$filtroMipres,$ambitoAtencion,$fitrompproveedor , $fechamipresprovedores , $vIDbuscar,$vIDEntregabuscar, $vIDReportebuscar, $filtroestadomipres , $cadidclaveprincipal , $vIDFacturacionbuscar , $vIDProgramacionbuscar , $vNumeroFacturabuscar );
			$data = pintarPrescripcionesEnTabla( $arrinfo , "" , "" );//22 junio filtrar por sindireccionamientoe
//, $filtroFacturacion
			if ( ( ( $accion == "enviarSeleccion")  ||  ( $accion == "enviarFacturacionSeleccion" ) ) && ( $errorenvioseleccion != "") ){
				$data = "<table align='center'><tr class='fila2' align='center'> <td> $errorenvioseleccion </td> </tr> </table>" . $data;

			}
			$errorenvioseleccion = "";
			
			echo $data;
			break;
			
			
			
		case "enviarSeleccion":
		
			 $errorenvioseleccion = "";
			 //13 septiembre 2019 , se enviaran el lote la programacion , la entrega y el reporte de entrega
			//PROGRAMACION
			if ($cadidjsonprogramacion != ""){//si se va a enviar la programacion

			
			 	$nit = nitwebservice($wemp_pmla);
				$codsedeprov = codigosedeproveedor($wemp_pmla)	;
				$arrIDentregaJsons = explode("|", $cadidjsonprogramacion);//informacion de la programacion
				foreach($arrIDentregaJsons as $key => $value){
					$cad = str_replace("\\","",$value);
					$vcadjson = json_decode($cad , true );
					$vcadjson["TipoIDSedeProv"] = "NI";//esta informacion no fue asignada en el llamado ajax del javascript
					$vcadjson["NoIDSedeProv"] = "$nit";
					$vcadjson["CodSedeProv"] = "$codsedeprov";
					$cad = json_encode($vcadjson);
					if ($cad != ""){//16 julio 2016 Freddy Saenz , para evitar el error del foreach
						$respentrega = hacer_programacion($cad);
						if (is_string($respentrega) ){//probablemente sea un error
							$errorenvioseleccion .= $respentrega;
						}
					}
					
				}
			}//if ($cadidjsonprogramacion != ""){//si se va a enviar la programacion

			//ENTREGA
			$arrIDentregaJsons = explode("|", $cadidjsonenvio);//informacion de la entrega
			foreach($arrIDentregaJsons as $key => $value){
				$cad = str_replace("\\","",$value);
				if ($cad != ""){//16 julio 2016 Freddy Saenz , para evitar el error del foreach
					$respentrega = hacer_entrega($cad);
					if (is_string($respentrega) ){//probablemente sea un error
						$errorenvioseleccion .= $respentrega;
					}
				}
				
			}
			//forzar un tiempo antes de hacer el reporte de entrega
			//29 de julio 2019
			
			$cta1 = 0;
			for ($i=0;$i<1000;$i++){
				$cta1 += $i;
			}
			
			
			//REPORTE DE ENTREGA
			$arrreporteentregaJsons = explode("|", $cadjsonreporteenvio);//informacion del reporte de entrega
			foreach($arrreporteentregaJsons as $key => $value){
				$cad = str_replace("\\","",$value);
				if ($cad != ""){//16 julio 2016 Freddy Saenz , para evitar el error del foreach
					$respentrega = hacer_reporte_entrega($cad);
					if (is_string($respentrega) ){//probablemente sea un error
						$errorenvioseleccion .= $respentrega;
					}
				}
				
			}

			//ENTREGA Y REPORTE DE ENTREGA , USANDO AMBITO DE ENTREGA
			$arrinfo = array();
			$arrJsons = explode("|", $vlistajson);//si la entrega se va a realizar usando el ambito de entrega.

			$ctaxenvio = 0;
			foreach($arrJsons as $key => $value){

				$cad = str_replace("\\","",$value);
				if ($cad != ""){
					$cadJson = json_decode($cad , true);
//5 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto
					$arrinfo[] = $cadJson;
					$ctaxenvio++;
					

				}

			}

			if ( $ctaxenvio > 0 ) {
				
				$datatmp = "";
				$datatmp = enviarAutomatico($arrinfo , true);//aqui se hace el envio del listado, para activar ,el segundo
				// $errorenvioseleccion se usa en enviarAutomatico
				if ( $datatmp != "" ){
					if ( $cadidclaveprincipal != "" ){
						$cadidclaveprincipal .= " , ";
					}
					$cadidclaveprincipal .= $datatmp;
				}
				
			}


		//No se hace break , para aprovechar el usar seleccion .

		case 'usarSeleccion'://la busqueda es igual , solo cambia el destino de la busqueda
			//$vlistaprescripciones  , son las prescripciones que se enviaran
			//usarSeleccion
			//$vlistajson

			if ($cadidclaveprincipal != ""){
				//22 de Agosto 2019, Freddy Saenz , nuevos criterios de busqueda //$vIDFacturacionbuscar , $vIDProgramacionbuscar , $vNumeroFacturabuscar
				$arrinfo = buscarPrescripcionesSuministros($wemp_pmla,$fechaInicial,$fechaFinal,$tipDocPac,$docPac,$tipDocMed,$docMed,$codEps,$tipoPrescrip,$nroPrescripcion,$filtroMipres,$ambitoAtencion,$fitrompproveedor , $fechamipresprovedores , $vIDbuscar,$vIDEntregabuscar, $vIDReportebuscar, $filtroestadomipres , $cadidclaveprincipal , $vIDFacturacionbuscar , $vIDProgramacionbuscar , $vNumeroFacturabuscar );

			}else{



				$arrinfo = array();
				$arrJsons = explode("|" , $vlistajson);
				$arrJsonsIDs = explode("|" , $vlistaIDsjson);
				$vID = 0;
				$vIDEnvio = 0;
				$vIDReporteEntrega = 0;
				$vIDProgramacion = 0;
				$vIDDireccionamiento = 0;

				$cta = 0;
				foreach( $arrJsons as $key => $value ){
					$cad = str_replace("\\" , "" , $value);
					$cadJson = json_decode( $cad , true );
//6 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto					
					$arrinfo[] = $cadJson;
					$cta++;
				}
			}
			$data = pintarPrescripcionesEnTabla( $arrinfo , "" , "" );//22 junio filtrar por sindireccionamientoe
//, $filtroFacturacion
			if ( ( ( $accion == "enviarSeleccion")  ||  ( $accion == "enviarFacturacionSeleccion" ) ) && ( $errorenvioseleccion != "") ){
				$data = "<table align='center'><tr class='fila2' align='center'> <td> $errorenvioseleccion </td> </tr> </table>" . $data;

			}
			$errorenvioseleccion = "";
			
			echo $data;
			//echo $vlistajson;
			//echo $vlistaIDsjson;

			break;



		case 'pintarReportePrescripcionMipres':



		//pintarPrescripcionMipres
		//fechamipresprovedores


			$arrinfo = array();
			switch ($fitrompproveedor){
				case 0:
				case 1:
				case 2:
				case 7:
				case 8:
					$cadidclaveprincipal = "";
					//pintarReportePrescripcionMipres
				//$vIDFacturacionbuscar	: vIDFacturacionbuscar,
			//$vIDProgramacionbuscar	: vIDProgramacionbuscar ,
			//$vNumeroFacturabuscar	: vNumeroFacturabuscar
				
				//22 de Agosto 2019, Freddy Saenz , nuevos criterios de busqueda //$vIDFacturacionbuscar , $vIDProgramacionbuscar , $vNumeroFacturabuscar
				//$fitrompproveedor deberia llegar en blanco , se hace la busqueda en las otras opciones de este switch
					$arrinfo = buscarPrescripcionesSuministros($wemp_pmla,$fechaInicial,$fechaFinal,$tipDocPac,$docPac,$tipDocMed,$docMed,$codEps,$tipoPrescrip,$nroPrescripcion,$filtroMipres,$ambitoAtencion,$fitrompproveedor , $fechamipresprovedores , $vIDbuscar,$vIDEntregabuscar, $vIDReportebuscar, $filtroestadomipres  , $cadidclaveprincipal  , $vIDFacturacionbuscar , $vIDProgramacionbuscar , $vNumeroFacturabuscar );

					break;

				case 3:
					$arrinfo = fbuscarxdireccionamiento($fechamipresprovedores);

					break;
				case 4:
					$arrinfo = fbuscarxprogramacion($fechamipresprovedores);

					break;

				case 5:
					$arrinfo = fbuscarxentrega($fechamipresprovedores);

					break;
				case 6:
					$arrinfo = fbuscarxreportedeentrega($fechamipresprovedores);

					break;
					
				case 9://facturas enviadas en la fecha
					$arrinfo = fbuscarxfacturaenviada($fechamipresprovedores);

					break;



				default :
					echo "no implementado ";
			}//switch ($fitrompproveedor)



			switch($accion){
				case "pintarReportePrescripcionMipres":
					if ( ($fitrompproveedor <= 6) ||  ($fitrompproveedor ==9 ) ) {
						if ( !is_string($arrinfo) ){//recorrer el arreglo , solo si no se produjeron errores
							foreach( $arrinfo as $key => $value ){

								$arrtemp = generarCmdsSqlXInsertUpdateXJson ( $value )	;
								$numfilas = insertupdatemipressuministro($arrtemp);//$vtxt;

							}
						
						}
						
					}//if ($fitrompproveedor < = 6)
					$data = pintarPrescripcionesEnTabla($arrinfo , $filtroestadomipres , $filtroFacturacion );// 22 junio 2019 ,filtrar por sindireccionamientoe , porque no es posible hacerlo  en buscarPrescripcionesSuministros
					//var_dump($data);
					echo $data;
					break;

			}



			break;//case 'pintarReportePrescripcionMipres':


/*No se usa
		case "generarNuevoToken":

			$tokenhoy = get_generar_token();
			$whoy=date("Y-m-d");

			$where  = "Detapl = 'tokenDiarioWebserviceSuministroMipres' ";
			$where  .= " AND Detemp = '".$wemp_pmla."' ";
			$setcampos = "Detval = '".$tokenhoy. "' ";
			$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);

			$where  = "Detapl = 'fechatokenWebserviceSuministroMipres' ";
			$where  .= " AND Detemp = '".$wemp_pmla."' ";
			$setcampos = "Detval = '".$whoy. "' ";
			$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);
			echo $tokenhoy;
			break;
*/

		case  "hacerEnvio":
			//echo $infoenvio;

			$respenvio = "";

			if ($vID == 0){//si no tiene ID hacer ambito de entrega

				$infoenvio = str_replace("\\","",$infoenvio);
				$vstr = $infoenvio;
				$vJson = json_decode($infoenvio , true );//convierte el texto a json
//7 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto
			}else{
				$vtxtIDarrenvio = str_replace("\\","",$vtxtIDarrenvio);

				$vstr = $vtxtIDarrenvio;
				$vJson =json_decode($vtxtIDarrenvio , true);//texto a json
//1 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto
			}

			if (count($vJson) > 0 ) {


				$vjsonresp = hacer_entrega($vstr);//se envia en texto , la representacion del json
				if (!is_string($vjsonresp) ){//respuesta correcta
				// [{"Id":2455345,"IdEntrega":295600}]
				//Error = [{"Id":2455345,"IdEntrega":295600}]
					//$vjsonresp = $vjsonrespStr; //$json_decode($vjsonrespStr); ya esta codificada
					$tablaActualizar = tablaxtipotec($vtipotec);
					$idresp = "";
					$idenvio = "";
					foreach($vjsonresp as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
						foreach($value as $key2 => $value2 ){
							switch($key2){
							case "Id":
								$idresp = $value2;
								break;
							case "IdEntrega":
								$idenvio = $value2;
								break;

							}//switch($key2)
						}//foreach($value as $key2 => $value2 )

					}//foreach($vJson as $key => $value)

					$respenvio = $idresp."|".$idenvio;



				}else{//viene un string con el error correspondiente
						
					$vjsonresp = str_replace( "Error|" , "" , $vjsonresp);//29 julio 2019 , para evitar doble concatenacion
					$respenvio = "Error| $vjsonresp  ";	//$vjsonrespStr 4 julio 2019 , estaba por fuera de los if y generaba errores cuando no existian.
				}//if (!is_string($vjsonrespStr) ){//respuesta correcta




		//		$vjsonresp->ID
			//	$vjsonresp->IdEntrega
			}else{
				$respenvio = "Error| no paso por count $vJson";
			}

			echo $respenvio;


			break;
		case  "hacerReporte":
			//$vID
			$respreporte = "";
			$infoenvio = str_replace("\\","",$infoenvio);
		//	echo $infoenvio;


			$vJson = json_decode($infoenvio , true );//convierte el texto a json
//9 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto			
			$seguir = 1;


			if (  ( $vID == 0) ){//si no tiene ID y no se ha enviado
				$seguir = 0;
				$infoenvio = " No tiene ID ";
			}else if ( (is_array( $vJson  ) ) || (is_object( $vJson  ) ) ){//($vJson["ID"] == 0 ){
				$arrtmp = array();
				foreach($vJson as $key => $value ){
					switch($key){
						case "ID":
							if ($value == 0){
								$arrtmp[$key] = $vID;
							}else{
								$arrtmp[$key] = $value;
							}
							break;
						default:
							$arrtmp[$key] = $value;
							break;

						
					}
				}
				
					
				$infoenvio = json_encode($arrtmp);

				

			}else{
				
				

			}
			
			if ( ( count($vJson) > 0 )  && ($seguir != 0 ) ){

				$idresp  = "";
				$idreporte = "";
				$vstr = $infoenvio;
				$vjsonresp = hacer_reporte_entrega($infoenvio) ;//($vJson);
				if (!is_string($vjsonresp)){//ok, correcto

					foreach($vjsonresp as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
						foreach($value as $key2 => $value2 ){
							switch($key2){
							case "Id":
								$idresp = $value2;
								break;
							case "IdReporteEntrega":
								$idreporte = $value2;
								break;

							}//switch($key2)
						}//foreach($value as $key2 => $value2 )

					}//foreach($vJson as $key => $value)


					$respreporte = $idresp . "|" . $idreporte;

				}else{//error en el web service
					//$respreporte = $vjsonresp;
					$vjsonresp = str_replace( "Error|" , "" , $vjsonresp);//29 julio 2019 , para evitar doble concatenacion
					$respreporte = "Error| $vjsonresp  ";
				}

			//	$vjsonresp->ID
			//	$vjsonresp->IdReporteEntrega
			}else{
				$respreporte = "Error| $infoenvio ";
			}
			echo $respreporte;

			break;

		case "hacerProgramacion":
			$idresp  = "";
			$idprogramacion = "";

			if ($tipoIDsedeprov == "" ){
				$tipoIDsedeprov = "NI";
			}

			$arrprog = creararrxprogramacion($vID ,$fechamax ,$tipoIDsedeprov , $noIDsedeprov , $codsedeprov , $codtecnologia , $cantidadxentregar	);
			$vstr = json_encode($arrprog);//arreglo a texto


			$infoprog  = json_encode ($arrprog);//arreglo json a texto

			$vjsonresp = hacer_programacion($infoprog) ;//($vJson);
			if (!is_string($vjsonresp)){//ok, correcto
			//[{"Id": 2100116, "IdProgramacion": 1338334}]
				foreach($vjsonresp as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
					foreach($value as $key2 => $value2 ){
						switch($key2){
						case "Id":
							$idresp = $value2;
							break;
						case "IdProgramacion":
							$idprogramacion = $value2;
							break;

						}//switch($key2)
					}//foreach($value as $key2 => $value2 )

				}//foreach($vJson as $key => $value)



				$respprogramacion = $idresp . "|" . $idprogramacion;
			}else{
				$vjsonresp = str_replace( "Error|" , "" , $vjsonresp);//29 julio 2019 , para evitar doble concatenacion
				$respprogramacion = "Error| $vjsonresp  ";
				//$respprogramacion = $vjsonresp;
			}
			echo $respprogramacion;


			break;

			
		case "hacerFacturacion":	
		
		/*
				
		//$arrfacturacion = creararrfacturacion ($nroPrescripcion , 	$vtipotec ,$vconsecutivo , $tipoid , $numid ,	 $numerodeentrega , $codtecnologia , $cantidad ,  $vlrunitario ,$eps , $niteps , $numfactura ,  $cuotamoderadora , $coopago);
		//$varrfactatxt = arregloatexto($arrfacturacion);
					accion		        	: 'hacerFacturacion',
					wemp_pmla	        	: $('#wemp_pmla').val(),
					fechaInicial        	: $("#fechaInicial").val(),
					fechaFinal	        	: $("#fechaFinal").val(),
					tipDocPac	        	: $("#tipDocPac").val(),
					docPac		        	: $("#docPac").val(),
					tipDocMed		        : $("#tipDocMed").val(),
					docMed			        : $("#docMed").val(),
					codEps			        : $("#txtCodResponsable").val(),
					tipoPrescrip	        : $("#tipoPrescripcion").val(),
					nroPrescripcion      	: $("#nroPrescripcion").val(),
					filtroMipres	        :  "",//30 agosto 2019$("#filtroMipres").val(),
					ambitoAtencion	        : $("#filtroAmbitoAtencion").val(),
					fitrompproveedor        : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),
					vID                     : vvlrcampoID,//16 julio 2019 ,enviar el ID , si no fue actualizado

		*/
		
			$respfacturacion = "";
			$idfacturacion = 0;
			
			$cad = str_replace("\\","",$infoenvio);
			if ($cad != ""){//16 julio 2016 Freddy Saenz , para evitar el error del foreach
				$vjsonresp = hacer_facturacion($cad);
				if (is_string($vjsonresp) ){//probablemente sea un error
					$vjsonresp = str_replace( "Error|" , "" , $vjsonresp);//29 julio 2019 , para evitar doble concatenacion
					$respfacturacion = "Error| $vjsonresp  ";
					
				}else{
					foreach($vjsonresp as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
						foreach($value as $key2 => $value2 ){
							switch($key2){
							case "Id":
								$idresp = $value2;
								break;
							case "IdFacturacion":
								$idfacturacion = $value2;
								break;

							}//switch($key2)
						}//foreach($value as $key2 => $value2 )

					}//foreach($vJson as $key => $value)
					$respfacturacion = $idresp . "|" . $idfacturacion;
				
				}
			}
			echo $respfacturacion;
			break;
			

		case "procesoactualizarIDs":
			$arrayPrescripciones = actualizarIDsPrescripcionesPeriodo($wemp_pmla,$fechaInicial,$fechaFinal , "");//
			//$arrayPrescripciones2 = array_map("unserialize", array_unique(array_map("serialize", $arrayPrescripciones2)));

		//	$data = pintarPrescripcionesEnTabla( $arrayPrescripciones );

			echo "actualizados los IDs";

			break;
		case "generarreportemipres":
		//	$q = " SELECT Patcoa ,  Artcom , Artgen , Artcum  ";
	//$q .= " FROM ".$wbasedatomipres."_000039,".$wbasedato."_000026 ";
	//$q .= " WHERE Artcod = Patcoa AND  Patdmp = '" . $principioactivo . "' ";
//fechaInicial    : fechareporte,
//fechaFinal      : vfechahastareporte,
		//	$vtxtreporte = fReporteDiarioMipres ( $fechareporte );

			$vtxtreporte = fReporteDiarioMipres ( $fechaInicial , $fechaFinal , $vsalidareporte , $separador );
			echo $vtxtreporte;
					


			//echo "generando reporte";
			break;

		case "actualizarIDs":

			$arrayPrescripciones = actualizarIDsPrescripcionesPeriodo($wemp_pmla,$fechaInicial,$fechaFinal , $nroPrescripcion);//
			//$arrayPrescripciones2 = array_map("unserialize", array_unique(array_map("serialize", $arrayPrescripciones2)));

			$data = pintarPrescripcionesEnTabla( $arrayPrescripciones , "" , "" );

			echo $data;


			break;

		case "anularEntrega":
			$idenvio = $vIDEntrega;//dejarlo en cero si se puede anular
			$arrre2 = array ();
			$resp = anular_entrega($vIDEntrega);//$arrre2;// probando

			if (is_string($resp) ){//error en el web service, se devuelve un string con el error
				echo $idenvio;
			}else{


				$idenvio = 0;//se pudo anular correctamente
				$tablaActualizar = tablaxtipotec($vtipotec);
				$where = " SumIDe = '".$vIDEntrega."' ";// 3 julio 2019 clave mipresdetalle($vtipotec , $numprescripcion , $vconsecutivo , 0 );
				$setcampos = " SumIDe = '".$idenvio."' ";
				$setcampos .= " , Sumeen = 0 ";//estado de entrega, anulado
				$esxambito = select1campoquery( "Sumeam" , $tablaActualizar , $where );
				
				$respuesta = $idenvio;
 				if ( $esxambito == 1 ) {
					$setcampos .= " , SumID = '0' ";//cuando es por ambito de entrega se debe dejar en cero el ID para poder realizar una nueva entrega.
					$respuesta = "AMBITO";//indicar que se habia hecho por ambito de entrega.
				}
				$setcampos .= " , Sumeam = 0 ";

				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
				echo $respuesta;//17 julio 2019 $idenvio;


			}//if (count($vJson)==0)



			break;


		case "anularReporte":

			$arrre2 = array ();
			$idreporte = $vIDReporteEntrega;//dejarlo en cero si se puede anular
			$resp = anular_reporte_entrega($vIDReporteEntrega);//$arrre2;//probando
			//$vJson = json_decode($resp);//convierte el texto a json
			if (is_string($resp) ){//count($vJson)==0){//error en el web service
				echo $vIDReporteEntrega;
			}else{

				$idreporte = 0;//se pudo anular correctamente
				$tablaActualizar = tablaxtipotec($vtipotec);
				$where = " SumIDr = '".$vIDReporteEntrega."' ";//3 julio 2019 clave mipresdetalle($vtipotec , $numprescripcion , $vconsecutivo , 0 );
				$setcampos = " SumIDr = '".$idreporte."' ";
				$setcampos .= " , Sumfem = '0000-00-00' ";//5 julio 2019 , desmarcar la fecha de entrega del mipres
				$setcampos .= ",  Sumere = 0 ";//estado de reporte de entrega, anulado

				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
				echo $idreporte;


			}//if (count($vJson)==0)


			break;
		case "anularProgramacion"://vIDProgramacion

			$arrre2 = array ();
			$idprogramacion = $vIDProgramacion;//dejarlo en cero si se puede anular
			$resp =anular_programacion($vIDProgramacion);// $arrre2;//probando
			//$vJson = json_decode($resp);//convierte el texto a json
			if (is_string($resp) ){//count($vJson)==0){//error en el web service
				echo $vIDProgramacion;
			}else{

				$idprogramacion = 0;//se pudo anular correctamente
				$tablaActualizar = tablaxtipotec($vtipotec);
				$where = " SumIDp = '".$vIDProgramacion."' ";//3 julio 2019 clave mipresdetalle($vtipotec , $numprescripcion , $vconsecutivo , 0 );
				$setcampos = " SumIDp = '".$idprogramacion."' ";
				$setcampos .= " , Sumepr = 0 ";//estado de la programacion, anulado

				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
				echo $idprogramacion;
			}//if (count($vJson)==0)


			break;

		case "anularFacturacion"://vIDFacturacion

			$arrre2 = array ();
			$idfacturacion = $vIDFacturacion;//dejarlo en cero si se puede anular
			$resp =anular_facturacion($vIDFacturacion);// $arrre2;//probando
			
			if (is_string($resp) ){//count($vJson)==0){//error en el web service
				echo $vIDFacturacion;
			}else{

				$idfacturacion = 0;//se pudo anular correctamente
				$tablaActualizar = tablaxtipotec($vtipotec);
				$where = " SumIDf = '".$vIDFacturacion."' ";//3 julio 2019 clave mipresdetalle($vtipotec , $numprescripcion , $vconsecutivo , 0 );
				$setcampos = " SumIDf = '".$idfacturacion."' ";
				$setcampos .= " , Sumefa = 0 ";//estado de la programacion, anulado

				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
				echo $idfacturacion;
			}//if (count($vJson)==0)


			break;




		case "actualizarFechaAplicacion":
		case "actualizarValorUnitario":

		case "actualizarCausanoentrega":

		case "actualizarCodigo":
		case "actualizarCantidad":
		
			
			$vtipotecInicial = $vtipotec;
			$numprescripcionInicial =  $numprescripcion;
			$vconsecutivoInicial  = $vconsecutivo;
			$nroentregaInicial = $nroentrega;//3 de julio 2019

		//$cantart contiene la nueva cantidad
			$arrenvio = array();
			$arrenvioID = array();
			$vtxtarrenvio = str_replace("\\","",$vtxtarrenvio);

			$arrenvio2 = json_decode($vtxtarrenvio , true );//de texto a arreglo.
//10 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto			
			$numelem = count($arrenvio2);
			if ($numelem == 0){//buscar en la base de datos.

//
				//$where = clavemi presdetalle($vtipotec , $numprescripcion , $vconsecutivo , $vID);//basado en la tabla _000037
				$where = claveppalmipres($vID , $vtipotec , $numprescripcion , $vconsecutivo , $nroentrega);//3 de julio 2019
				$vselectpre = "Sumcan , Sumcae,Sumcst , Sumcoe,Sumvlr , Sumcne , Sumfen , Sumnen , Sumtid ,Sumnid ";
				$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000037", $where); //informacion de la prescripcion


				if ( count($arrFila) > 0){



					if ($accion == "actualizarCantidad"){
						//$cantart = $cantart;
					}else{
						if ($arrFila['Sumcae'] != 0){//se modifico la cantidad
							$cantart = $arrFila['Sumcae'];
						}else{
							$cantart = $arrFila['Sumcan'];//usar la cantidad original
						}//if ($arrFila['Sumcae'] != 0){//se modifico la cantidad

					}
					if ($accion == "actualizarCodigo"){
						//$codservxmipres = $codservxmipres;
					}else{
						if ($arrFila['Sumcoe'] != ""){//se modifico el codigo
							$codservxmipres = $arrFila['Sumcoe'];
						}else{
							$codservxmipres = $arrFila['Sumcst'];//usar el codigo original
						}//if ($arrFila['Sumcoe'] != ""){//se modifico el codigo

					}
					if ($accion == "actualizarCausanoentrega"){
						//$causanoentrega = $causanoentrega
					}else{
						$causanoentrega = $arrFila['Sumcne'];
					}

					if ($accion == "actualizarValorUnitario"){
						//$valormed = $valormed
					}else{
						$valormed = $arrFila['Sumvlr'];
					}
					if ($accion == "actualizarFechaAplicacion"){
						//$vfechaentrega = $vfechaentrega
					}else{
						$vfechaentrega = $arrFila['Sumfen'];
					}
			//		case "actualizarFechaAplicacion":
			//		case "actualizarValorUnitario":







					$noentrega = $arrFila['noentrega'];
					$tipoID = $arrFila['Sumtid'];
					$ceduladir = $arrFila['Sumnid'];
					if ($tipoID == ""){
						$wherePre1 = " Prenop = '". $numprescripcion."' ";

						$vselectpre1 = "Pretip , Preidp ";
						$arrFilaPre1 =  select1filaquery($vselectpre1, $wbasedatomipres."_000001", $wherePre1); //informacion de la prescripcion
						if (count($arrFilaPre1) > 0 ){
							if ($ceduladir == ""){
								$ceduladir = $arrFilaPre1['Preidp'];
							}
							$tipoID =  $arrFilaPre1['Pretip'];
						}
					}
					$ventregatotal = 1;


					$lote = "";
					$arrenvio = creararrxenvio ($numprescripcion,$vtipotec,$vconsecutivo,$tipoID,$ceduladir,$noentrega,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote);
					$arrenvioID = creaarrxenvioconID($vID,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote , $tipoID , $ceduladir , $numprescripcion );//Se agregan 3 parametros mas , cambio el mipres segun requerimientos del 26 de Julio 2019 ,Freddy Saenz

			//		$arrenvioID = textojsonaarreglo($vtxtIDarrenvio);


				}//if ( count($arrFila) > 0)






			}//if ($numelem==0){//buscar en la base de datos.
			else{

			$arrenvioID2 = textojsonaarreglo($vtxtIDarrenvio);


//actualizar el arreglo de los IDs


			foreach($arrenvioID2 as $clave => $valor){
				switch($clave){
					case "CantTotEntregada":
						if ($accion == "actualizarCantidad"){
							$arrenvioID[$clave] = $cantart;
							
						}else{
							$arrenvioID[$clave] = $valor;
						}

						break;
					case "CodSerTecEntregado":
						switch($accion){
							case "actualizarCodigo":
								$arrenvioID[$clave] = $codservxmipres;//actualizar el codigo de la tecnologia
								break;
							default ://case "actualizarCausanoentrega": "actualizarCantidad"
								$arrenvioID[$clave] = $valor;
								break;

						}

						break;

					case "FecEntrega":

						switch($accion){
							case "actualizarFechaAplicacion":
								$arrenvioID[$clave] = $vfechaentrega;//actualizar el codigo de la tecnologia
								break;
							default ://case "actualizarCausanoentrega": "actualizarCantidad"
								$arrenvioID[$clave] = $valor;
								break;

						}



						break;
					default:
						$arrenvioID[$clave] = $valor;

				}

			}//foreach($arrenvioID2 as $clave => $valor)


//

			foreach($arrenvio2 as $clave => $valor){
				switch($clave){
					case "CantTotEntregada":
						if ($accion == "actualizarCantidad"){
							$arrenvio[$clave] = $cantart;
							
						}else{
							$arrenvio[$clave] = $valor;
						}

						break;
					case "NoPrescripcion":
						$arrenvio[$clave] = $valor;
						$numprescripcion = $valor;
						break;
					case "ConTec":
						$arrenvio[$clave] = $valor;
						$vconsecutivo = $valor;
						break;
					case "TipoTec":
						$arrenvio[$clave] = $valor;
						$vtipotec = $valor;
						break;
					case "CodSerTecEntregado":
						switch($accion){
							case "actualizarCodigo":
								$arrenvio[$clave] = $codservxmipres;//actualizar el codigo de la tecnologia
								break;
							default ://case "actualizarCausanoentrega": "actualizarCantidad"
								$arrenvio[$clave] = $valor;
								break;

						}


						//$codservxmipres = $valor;
						break;

					case "FecEntrega":

						switch($accion){
							case "actualizarFechaAplicacion":
								$arrenvio[$clave] = $vfechaentrega;//actualizar la fecha de la aplicacion
								break;
							default ://case "actualizarCausanoentrega": "actualizarCantidad"
								$arrenvio[$clave] = $valor;
								break;

						}



						break;
					default:
						$arrenvio[$clave] = $valor;

				}

			}//foreach($arrenvio2 as $clave => $valor)







//			$arrenvioID = textojsonaarreglo($vtxtIDarrenvio);
}//else no tiene elementos el arreglo ,fue una modificacion manaul  //if ($numelem==0){//buscar en la base de datos.



			switch($accion){
				case "actualizarCantidad":
					$sufijocampo = "cae";
					break;
				case "actualizarCodigo":
					$sufijocampo = "coe";
					break;
				case "actualizarCausanoentrega":
					$sufijocampo = "cne";
					break;
				case "actualizarFechaAplicacion":
					$sufijocampo = "fen";
					break;
				case "actualizarValorUnitario":
					$sufijocampo = "vlr";
					break;
			}

//		case "actualizarFechaAplicacion":
//		case "actualizarValorUnitario":

			$setcampos = "";
			$setcampos = " Sum".$sufijocampo;//21 de mayo 2019

			switch($accion){
				case "actualizarCantidad":
					$setcampos .= " = $cantart ";
					break;
				case "actualizarCodigo":
					$setcampos .= " = '".$codservxmipres ."' ";
					break;
				case "actualizarCausanoentrega":
					$setcampos .= " = ".$causanoentrega ." ";
					break;
				case "actualizarFechaAplicacion":
					$setcampos .= " = '".$vfechaentrega ."' ";
					break;
				case "actualizarValorUnitario":
					$setcampos .= " = ".$valormed ." ";
					break;
			}//Faltaba ponerlo manual para que lo tenga en cuenta .
			$setcampos .= " , Sumima = 1 ";
			//16 de julio 2019 , Freddy Saenz , cuando se modifica la cantidad y se deja en cero , no habia manera de 
			//dejar un cero como cantidad  modificada
			if ($accion == "actualizarCantidad" ){
				$setcampos .= " , Sumcam = 1 ";
			}



			if ($numprescripcionInicial != ""){//$numprescripcion
				//var_dump($arrenvio);

				$tablaActualizar = tablaxtipotec($vtipotecInicial);
				$where = claveppalmipres( $vID , $vtipotecInicial , $numprescripcionInicial , $vconsecutivoInicial , $nroentregaInicial);//3 de julio 2019
				//$where = clave mipresdetalle($vtipotecInicial , $numprescripcionInicial , $vconsecutivoInicial , $vID);
				$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
				//$arrenvio["CantTotEntregada"] = $cantart;//actualizar la cantidad,
				//3 actualizar informacion
				
//02 ingresomanual = 1 , se esta ingresando manualmente informacion	
				$esIngresoManual = 1;
				$vtxdataenvio = datacellenvio( $cta , $arrenvio , $cantart , $cantidatTotAEntregar , $vIDEntrega , $vIDReporteEntrega , $codservxmipres , $valormed , $vID , $estadoentrega , $causanoentrega , $arrenvioID , $vtipotecInicial , $numprescripcionInicial , $vconsecutivoInicial , $vfechaentrega , $esIngresoManual);
				//16 julio 2019 , ingreso manual ,para activar el envio con cantidad = 0
				echo $vtxdataenvio;

			}else{
				echo "No se pudo realizar la actualizacion";
			}


			break;

		case "actualizarCodigoSede":

			$where  = "Detapl = 'codigoSedeMipres' ";
			$where  .= " AND Detemp = '".$wemp_pmla."' ";
			$setcampos = "Detval = '".$codsede. "' ";
			$numup = update1registro($wbasedatoroot."_000051", $setcampos, $where);
			echo $codsede;

			break;

		case "actualizartecnologiaxprogramacion":
			global $wbasedatomipres;
			$where  = "SumID = '". $vID . "' ";

			$setcampos = "Sumcst = '".$codtecnologia. "' ";
			$numup = update1registro($wbasedatomipres."_000037", $setcampos, $where);
			echo $codtecnologia;

			break;

					
			
		case "abrirDlgIngresoManual":

			$infohtmlxingresomanual = abrirDlgIngresoManual($cantidad , $codigo , $vlrunitario , $fechaentrega ) ;
			echo $infohtmlxingresomanual ;
			break;

		case "abrirDlgProgramacion":
			//Busca las programaciones asociadas a un mipres .

			//$nroPrescripcion =
		    //$vtipotec
		    //$vconsecutivo
			global $wsedemipres;
			global $wbasedatomipres;
			//$codsede = $wsedemipres;//12 julio 2019 ,hay que borrar espacios , porque no programa en caso contrario
			$codsede = str_replace(" ","",$wsedemipres );
			//$cantidad
			//$codtecnologia

			$infohtmlxprogramacion = infoxdlgprogramacion( $nroPrescripcion , $vtipotec , $vconsecutivo, $cantidad, $codsede , $codtecnologia , $numerodeentrega , $cta  ,$tipoIDprov , $numIDprov , $wbasedatomipres) ;

			echo $infohtmlxprogramacion ;

			break;

			//5 agosto 2019 , crear la informacion del mipres de facturacion

		case "abrirDlgFacturacion":
			//Busca las programaciones asociadas a un mipres .

			//$nroPrescripcion =
		    //$vtipotec
		    //$vconsecutivo
			global $wsedemipres;
			global $wbasedatomipres;
			//$codsede = $wsedemipres;//12 julio 2019 ,hay que borrar espacios , porque no programa en caso contrario
			$codsede = str_replace(" ","",$wsedemipres );
			//$cantidad
			//$codtecnologia

/*			
			nroPrescripcion : numprescripcion,
		    vtipotec        : vtipotec,
		    vconsecutivo    : vconsecutivo,
			canttotal       : canttotal,
			codsedeprov     : codsedeprov,
			cantidad        : canttotal,
			codtecnologia   : codservxmipres,
			cta             : cta,//27 mayo 2019
			tipoIDprov      : tipoIDprov ,
			numIDprov       : numIDprov,
			eps             : eps,
			niteps          : niteps,
			numfactura      : numfactura,
			cuotamoderadora : cuotamoderadora,	
			coopago         : coopago ,	
			tipoid : tipoid,	
			numid         : numid ,			
			numerodeentrega : numerodeentrega
			vID             : vID,
			vIDFacturacion      : vIDFacturacion
	

			$cta , $nroPrescripcion , $vtipotec , $vconsecutivo ,$codtecnologia , $cantidad , $tipoid , $numid , $eps , $niteps , $numfactura , $cuotamoderadora , $coopago
*/			
			
			
			
			$infohtmlxfacturacion = infoxdlgfacturacion( $cta , $nroPrescripcion , $vtipotec , $vconsecutivo ,$codtecnologia , $cantidad , $tipoid , $numid , $eps , $niteps , $numfactura , $cuotamoderadora , $coopago , $vlrunitario , $vlrtotal ,  $numerodeentrega , $vID , $vIDFacturacion );
			//$nroPrescripcion , $vtipotec , $vconsecutivo, $cantidad, $codsede , $codtecnologia , $numerodeentrega , $cta  ,$tipoIDprov , $numIDprov , $wbasedatomipres) ;

			echo $infohtmlxfacturacion ;

			break;
			
			
			

		case "grabarProgramacion":


			$tablaActualizar = tablaxtipotec($vtipotec);
			$setcampos = "Sumepr = $estadoprog ";//estado de progamacion 1 activo 2 procesado , 0 anulado
			//$where = clave mipresdetalle($vtipotec , $numprescripcion , $vconsecutivo , $vID);
			$where = claveppalmipres(  $vID , $vtipotec , $numprescripcion , $vconsecutivo , $nroentrega );//3 julio 2019


			$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);

			echo "ID $vID = $numprescripcion";

			break;

		case mostrarDireccionamiento:
			$jsonprogramacion = "";
			$tablaActualizar = tablaxtipotec($vtipotec);
			if ($vID != 0){
				$wherestr = " SumID = '".$vID."' ";
			}else{
				$wherestr = " Sumnop = '".$numprescripcion."' and Sumtip = '".$vtipotec."' AND Sumcon = '".$vconsecutivo."'";
				if ($nroentrega != 0){//3 JULIO 2019 , incluir la entrega en la clave principal
					$wherestr .= " AND Sumnen = '".$nroentrega ."' ";
				}
			}
			$jsonprogramacion = select1campoquery("Sumjdi", $tablaActualizar, $wherestr);
			$vtextoajson = json_decode($jsonprogramacion , true );
//11 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto			
			$vjsonatabla = arregloatablahtml($vtextoajson);
			echo $vjsonatabla; // $jsonprogramacion;

			break;

		case "ingresoManual":


			if ( $vfechamipres != "" ){
				$vfechaentrega = $vfechamipres;
			}else{
				$vfechaentrega = Date("Y-m-d");
			}


			$causanoentrega = 0;
			$ventregatotal = 1;
			$codservxmipres  = "";
			$cantart = 0;
			$valormed = 0;
			$estadoentrega = 1;


			$tablaActualizar = tablaxtipotec($vtipotec);
			$setcampos = "Sumcae = Sumcan ";
			$setcampos .= " , Sumcoe = Sumcst";
			$setcampos .= " , Sumvlr = $vlrtecnologia ";
			$setcampos .= " , Sumima = 1 ";//fue un ingreso manual
			$setcampos .= " , Sumfen = '". $vfechaentrega. "' ";//1 fecha de entrega 10 junio 2019

			//$where = clave mipresdetalle($vtipotec , $numprescripcion , $vconsecutivo , $vID);

			$where = claveppalmipres( $vID , $vtipotec , $numprescripcion , $vconsecutivo , $nroentrega );//3 de julio 2019

			$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);



			$vselectpre = "Sumcan , Sumcae,Sumcst , Sumcoe,Sumvlr , Sumcne , Sumfen , Sumnen , Sumtid ,Sumnid , Sumnen ";

			$arrFila =  select1filaquery($vselectpre, $wbasedatomipres."_000037", $where); //informacion de la prescripcion

			if ( count($arrFila) > 0){
				$codservxmipres = $arrFila['Sumcoe'];
				$cantart = $arrFila['Sumcae'];
				$valormed = $arrFila['Sumvlr'];
				$noentrega =   $arrFila['Sumnen'];
				$tipoID  =   $arrFila['Sumtid'];
				$ceduladir = $arrFila['Sumnid'];
				$vfechaentrega = $arrFila['Sumfen'];//fecha de entrega 10 junio 2019
			}



			$lote = "";
			$arrenvio = creararrxenvio ($numprescripcion,$vtipotec,$vconsecutivo,$tipoID,$ceduladir,$noentrega,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote);
			$arrenvioID = creaarrxenvioconID($vID,$codservxmipres,$cantart,$ventregatotal,$causanoentrega,$vfechaentrega,$lote , $tipoID , $ceduladir, $numprescripcion );//Se agregan 3 parametros mas , cambio el mipres segun requerimientos del 26 de Julio 2019 ,Freddy Saenz

//03 ingreso manual = 1 case "ingresoManual":
			$esIngresoManual = 1;
			$vtxdataenvio = datacellenvio($cta, $arrenvio ,$cantart,  $cantart , $vIDEntrega,$vIDReporteEntrega,$codservxmipres,$valormed , $vID,$estadoentrega,$causanoentrega,$arrenvioID ,$vtipotec,$numprescripcion,$vconsecutivo , $vfechaentrega , $esIngresoManual);
			//16 julio 2019 , ingreso manual ,para activar el envio con cantidad = 0
			//$estadoentrega= 1;
			$datareporte = creararrxreporte($vID,$estadoentrega,$causanoentrega,$cantart,$valormed);
			$vtxdatareporte  = datacellreporte( $cta , $datareporte , $vIDReporteEntrega );

			echo $vtxdataenvio ."|" . $vtxdatareporte ;//pegar las dos areas en una sola




			break;
//23 agosto 2019 , Freddy Saenz , cambiar la factura asociada con el mipres.
		case "cambiarFacturamipres":
			$arrFact = json_decode($infofact , true );


			$tablaActualizar = tablaxtipotec($vtipotec);
			$setcampos = "Sumnfa = '".$numfactura."' ";//es un valor de tipo string
			$setcampos .= " , Sumvco = $vlrcoopagofactura ";
			$setcampos .= " , Sumvcm = $vlrcuotamoderadorafactura ";

			$vID  = 0;
			$where = claveppalmipres( $vID , $vtipotec , $numprescripcion , $vconsecutivo , $numentrega );//3 de julio 2019

			$numlineasup = update1registro ($tablaActualizar, $setcampos , $where);
			if ($numlineasup == 1){
				echo "ok";
			}else{
				echo "error, no se actualizo la factura ";
			}

			
		//	echo $arrFact["numero"];
			break;
		case "infocantidadaplicada":
		    $arrresul = mostrarAplicacionesDetalle( $codArtInterno ,$histxmedi ,  $ingxhist , $vtipotec ,$numprescripcion ,   $vconsecutivo );
			echo $arrresul;
			break;

		case "infojsonenvio":

			$wherestr = " SumID = '".$vID."' ";
			$jsontxt = select1campoquery("Sumjen", $wbasedatomipres."_000037", $wherestr);
			$txttabla = "SIN INFORMACION";
			if ($jsontxt != ""){
				$arrjson = json_decode($jsontxt , true);
//12 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto				
				$txttabla = arregloatablahtml($arrjson);
			}
			echo $txttabla;
			break;

		case "infojsonreporteenvio":
			$vID = str_replace(" ","",$vID );
			$wherestr = " SumID = '".$vID."' ";
			$jsontxt = select1campoquery("Sumjre", $wbasedatomipres."_000037", $wherestr);
			$txttabla = "SIN INFORMACION";
			if ($jsontxt != ""){
				$arrjson = json_decode($jsontxt , true );
//13 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto				
				$txttabla = arregloatablahtml($arrjson);
			}
			echo $txttabla;
			break;


		case "testJson":

			

			echo $fuente . "|" . $documento;

			
			//$infotest = "[{\"Id\":2455345,\"IdEntrega\":295600}] ";
//	[{"Id":3303911,"IdReporteEntrega":519945}]
			$infotest = str_replace("\\","",$infotest );//Importante eliminar los \" y dejar una "
			//echo $infotest;
			$vjsonrespStr = json_decode($infotest , true );//de texto a arreglo
//14 - 6 agosto 2019 Freddy Saenz se agrega true , para obtener un arreglo y no un objeto			
			$idresp = "";
			$idenvio = "";
			foreach($vjsonrespStr as $key => $value){//la respuesta un un arreglo de json, pero el arreglo es de un solo elemento
				foreach($value as $key2 => $value2 ){
					switch($key2){
					case "Id":
						$idresp = $value2;
						break;
					case "IdEntrega":
						$idenvio = $value2;
						break;
					case "IdReporteEntrega":
						$idenvio = $value2;
						break;

					}//switch($key2)
				}//foreach($value as $key2 => $value2 )

			}//foreach($vJson as $key => $value)
			$idresp  =  $vjsonrespStr[0]["Id"];//$vjsonrespStr[0]->Id;//$vjsonrespStr["Id"];
			$idenvio  = $vjsonrespStr[0]["IdReporteEntrega"];//$//$vjsonrespStr[0]->IdReporteEntrega;//$vjsonrespStr["IdReporteEntrega"];
			
			
			echo $idresp . "|" . $idenvio;
//var_dump($vjsonrespStr[0]->Id);

			//Error = [{"Id":2455345,"IdEntrega":295600}]

			
			
			break;




	}//switch($accion)
}//if(isset($accion))
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	?>
	<html>
	<head>
		<title>MIPRES Suministros</title>
	</head>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

		<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>


		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>


	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	$(document).ready(function() {

		$("#msjEspere").hide();

			$.datepicker.regional['esp'] = {
			closeText: 'Cerrar',
			prevText: 'Antes',
			nextText: 'Despues',
			monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
			'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
			'Jul','Ago','Sep','Oct','Nov','Dic'],
			dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
			dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
			dayNamesMin: ['D','L','M','M','J','V','S'],
			weekHeader: 'Sem.',
			dateFormat: 'yy-mm-dd',
			yearSuffix: ''
		};

		$.datepicker.setDefaults($.datepicker.regional['esp']);



		iniciarDatepicker("fechaInicial","Seleccione la fecha inicial");
		iniciarDatepicker("fechaFinal","Seleccione la fecha final");
		iniciarDatepicker("fechaInicialN","Seleccione la fecha inicial");
		iniciarDatepicker("fechaFinalN","Seleccione la fecha final");
		iniciarDatepicker("fechaInicialJP","Seleccione la fecha inicial");
		iniciarDatepicker("fechaFinalJP","Seleccione la fecha final");
		iniciarDatepicker("fechaInicialR","Seleccione la fecha inicial");
		iniciarDatepicker("fechaFinalR","Seleccione la fecha final");

		iniciarDatepicker("fechamipresprovedores","Seleccione la fecha del mipres");

		
		iniciarDatepicker("fechadialogo2020","Seleccione la fecha del reporte");//25 abril 2020 , boton para seleccionar fechas .


		// --> Activar tabs jquery
		$( "#tabsMipres" ).tabs({
			heightStyle: "content"
		});

		$("#tabsMipres").show();
		cargarAutocompleteEps();

		if ( valorcampoxid("fechacorrectaactualizacionids") != valorcampoxid("fechaultimaactualizacionIDs") ){
			//se realizo una actualizacion
			document.getElementById("fechaultimaactualizacionIDs").innerHTML = valorcampoxid("fechacorrectaactualizacionids");
		}
		document.getElementById("fechacorrectaactualizacionids").innerHTML = "";


/*
		if($("#fechaInicialJuntaProfesionales").val()!="" && $("#fechaInicialJuntaProfesionales").val()!=undefined)
		{
			jAlert("Desde el "+$("#fechaInicialJuntaProfesionales").val()+" tiene prescripciones activas pendientes que requieren junta de profesionales","ALERTA");
			$("#fechaInicialJP").val($("#fechaInicialJuntaProfesionales").val());
			$("#tabsMipres").tabs({ selected: 2 });
			$("#estadoJM").val("2");
			pintarPrescripcionesJP();
		}
**/

		$( function() {//Desplegar la ventana usando accordiones, areas que se escondes o que se activan.
			$( "#tabPrescripciones" ).accordion();
		  } );

	});//$(document).ready(function() {


	function iniciarDatepicker( campo , descripcion )
	{
		$("#"+campo).datepicker({

			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonText: descripcion,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			minDate:$("#fechaInicioMonitorMipres").val(),
			maxDate:new Date()
		});
	}//function iniciarDatepicker(campo,descripcion)

	function iniciarDatepickerSinImagen( campo , descripcion )
	{
		$("#"+campo).datepicker({

			changeMonth: true,
			changeYear: true,

			//showOn: "button",
			//buttonImage: "../../images/medical/root/calendar.gif",
			//buttonText: descripcion,
			dateFormat: 'yy-mm-dd'//,
			//buttonImageOnly: true,

			//minDate:$("#fechaInicioMonitorMipres").val(),
			//maxDate:new Date()
		});
	}//function iniciarDatepicker(campo,descripcion)


	function iniciarDateprogramacion(campo,descripcion)
	{
		$("#"+campo).datepicker({

			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonText: descripcion,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			minDate: new Date(),
			maxDate: $("#fechamaxprogramacion").val()

		});
	}//function iniciarDateprogramacion(campo,descripcion)


	function iniciarTooltip(tooltip)
	{
		//Tooltip
		var cadenaTooltip = $("#"+tooltip).val();

		cadenaTooltip = cadenaTooltip.split("|");


		for(var i = 0; i < cadenaTooltip.length-1;i++)
		{
			$( "#"+cadenaTooltip[i] ).tooltip();
		}

	}//function iniciarTooltip(tooltip)



	function abrirModalConInfo(info , num )
	{//pintarPrescripcionMipres



		data = "<div id='modalMipres'><br><br>" + info ;
		data += "<input type='button' value='Cerrar' onclick='cerrarModal();'>";
		data += " Valores negativos indican que no se encontro valor con la tarifa asociada , se usa la tarifa de mayor valor. En el acumulado su valor se suma ";

		//Informar como se puede dejar la cantidad en cero.
		//23 mayo , las cantidades son fijas no se pueden actualizar .
		// data += " * La  Cantidad se puede arrastrar y soltar entre filas (drag and drop) ";

		data += "</div> ";

		$( "#dvAuxModalMipres" ).html( data );

		var canWidth = $(window).width()*0.4;
		if( $( "#dvAuxModalMipres" ).width()-50 < canWidth ){
			canWidth = $( "#dvAuxModalMipres" ).width();

		}

		var canHeight = $(window).height()*0.4;
		if( $( "#dvAuxModalMipres" ).height()-50 < canHeight ){
			canHeight = $( "#dvAuxModalMipres" ).height();
		}



//iniciarDateprogramacion("fechaprogramacion_0","Seleccione la fecha de programacion");//num es variable
//iniciarDateprogramacion("fechaprogramacion_1","Seleccione la fecha de programacion");//num es variable
		for (i=0;i<num;i++){

		//	iniciarDateprogramacion("fechaprogramacion_"+i,"Seleccione la fecha de programacion");//num es variable

		}

		$.blockUI({ message: $('#modalMipres'),
		css: {
			overflow: 'auto',
			cursor	: 'auto',
			width	: "97%",
			height	: "50%",
			left	: "2.5%",
			top		: '100px',
		} });


	}//function abrirModalConInfo(info , num )
// style='width:90% display: block;'
//style='width:90% display: block background-color: #4CAF50;' 

//background-color='#4CAF50' border='none' display='block' width='90%'
	function abrirDlgFecha2020 ( vfecha  , vnomfuncion ){
		//+date("Y-m-d")+
		info =  "<table style='width:100%' align='center' >";

		info +=  "<tr><td colspan ='2' align='center' style='font-size: medium;'>Fecha Inicial</td></tr>";
		info +=  "<tr><td colspan ='2' align='center' ><input type='text' id='fechadialogo2020' name='fechadialogo2020' readOnly='readOnly' style='font-size: medium;' value='" + vfecha + "'> </td></tr>";
	
		info +=  "<tr><td colspan ='2' align='center' style='font-size: medium;'>Fecha Final</td></tr>";
		info +=  "<tr><td colspan ='2' align='center' ><input type='text' id='fechahastadialogo2020' name='fechahastadialogo2020' readOnly='readOnly' style='font-size: medium;' value='" + vfecha + "'> </td></tr>";
	
		info +=  "<tr><td colspan ='2' align='center' style='font-size: medium;'>Generar por</td></tr>";
		info +=  "<tr>";
		info +=  "<td colspan ='2' align='center' style='font-size: medium;'>";
		info +=  " <select name='opcionesreporte' id='opcionesreporteid' style='font-size: medium;'> ";
		info +=  " <option selected='selected' value='Archivo'>Archivo Texto</option>";
		info +=  " <option value='Impresora'>Impresora</option>";
		info +=  " <option value='Ventana'>Pantalla</option>";
	
		info +=  "</td>";
		info +=  "</tr>";



		info +=  "<tr>";
	
		info +=  "<td align='center' width='50%'><input type='button' style='width:90%; color:black; background-color:#E8E8E8;  height:30px; font-weight: 500; font-size: medium; border-radius: 12px;' value='Cancelar' onclick='cerrarModal();' ></td>";
		info +=  "<td align='center' width='50%'><input type='button' style='width:90%; color:black; background-color:#E8E8E8;  height:30px; font-weight: 500; font-size: medium; border-radius: 12px;' value='OK' onclick='"+vnomfuncion+"();' ></td>";
		
		info +=  "</tr>";
		info +=  "</table>";

		data = "<div id='modalMipres'><br>" + info ;
		data += "</div> ";

		//Informar como se puede dejar la cantidad en cero.
		//23 mayo , las cantidades son fijas no se pueden actualizar .
		// data += " * La  Cantidad se puede arrastrar y soltar entre filas (drag and drop) ";

		
		//jAlert(data);
		//iniciarDatepicker("fechadialogo2020","Seleccione la fecha:");
		//$('#modalMipres').addClass('blockMsg');
		//$('#ui-datepicker-div').addClass('blockMsg');

		$( "#dvAuxModalMipres" ).html( data );
		//iniciarDatepickerSinImagen("fechadialogo2020","Seleccione la fecha-:");
		iniciarDatepicker("fechadialogo2020","Seleccione la fecha:");
		//17 junio 2020
		iniciarDatepicker("fechahastadialogo2020","Seleccione la fecha:");

		$('#modalMipres').addClass('blockMsg');
		$('#ui-datepicker-div').addClass('blockMsg');

		var canWidth = $(window).width()*0.4;
		if( $( "#dvAuxModalMipres" ).width()-50 < canWidth ){
			canWidth = $( "#dvAuxModalMipres" ).width();

		}

		var canHeight = $(window).height()*0.4;
		if( $( "#dvAuxModalMipres" ).height()-50 < canHeight ){
			canHeight = $( "#dvAuxModalMipres" ).height();
		}
		$.blockUI({ message: $('#modalMipres'),
		css: {
			overflow: 'auto',
			cursor	: 'auto',
			width	: '300px', //"85%",
			height	: "230px",//"40%", altura de la ventana
			left	: "40%",//"2.5%", tratar de centrar el dialogo.
			top		: '40%', //100px',
		} });
		
		

	}//function abrirDlgFecha2020 (info){


	function downloadreporte (filename, text) {
    var element = document.createElement('a');
    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
    element.setAttribute('download', filename);

    element.style.display = 'none';
    document.body.appendChild(element);

    element.click();

    document.body.removeChild(element);
}

//por aqui voy
	function abrirDlgOpcionesImprimir(  ){//vnomfuncion

		info =  "<table style='width:100%' align='center' >";


		
		info +=  "<tr><td colspan ='2' align='center' style='font-size: medium;'>Generar por</td></tr>";
		info +=  "<tr>";
		info +=  "<td colspan ='2' align='center' style='font-size: medium;'>";
		info +=  " <select name='opcionesreporte' id='opcionesreporteid'> ";
		info +=  " <option selected='selected' value='Archivo'>Archivo Texto</option>";
		info +=  " <option value='Impresora'>Impresora</option>";
		info +=  " <option value='Ventana'>Ventana</option>";
		info +=  " <option value='-'>-</option>";

		info +=  "</td>";
		info +=  "</tr>";

		info +=  "<tr>";
	
		info +=  "<td align='center' width='50%'><input type='button' style='width:90%; color:black; background-color:#E8E8E8;  height:30px; font-weight: 500; font-size: medium; border-radius: 12px;' value='OK' onclick='cerrarModal();' ></td>";
		//info +=  "<td align='center' width='50%'><input type='button' style='width:90%; color:black; background-color:#E8E8E8;  height:30px; font-weight: 500; font-size: medium; border-radius: 12px;' value='OK' onclick='"+vnomfuncion+"();' ></td>";

	info +=  "</tr>";

		info +=  "</tr>";
		info +=  "</table>";

		data = "<div id='modalMipres'><br>" + info ;
		data += "</div> ";
		$( "#dvAuxModalMipres" ).html( data );

		$('#modalMipres').addClass('blockMsg');

		$.blockUI({ message: $('#modalMipres'),
		css: {
			overflow: 'auto',
			cursor	: 'auto',
			width	: '300px', //"85%",
			height	: "100px",//"40%", altura de la ventana
			left	: "40%",//"2.5%", tratar de centrar el dialogo.
			top		: '40%', //100px',
		} });

	}



	function seleccionarEnModal( funcionausar )
	{
		vfechaseleccionada  = $('#fechadialogo2020').val();
		//17 junio 2020
		vfechahastaseleccionada  = $('#fechahastadialogo2020').val();
		
		alert("seleccionado"+vfechaseleccionada);
		funcionausar(vfechaseleccionada , vfechahastaseleccionada);
	
		$.unblockUI();
	}


	function cerrarModal()
	{
		$.unblockUI();
	}


	

	function habilitarDocumento(elemento)
	{
		campo = $(elemento).attr('habilitarCampo');
		if($(elemento).val()!="")
		{
			$("#"+campo).show();
		}
		else
		{
			$("#"+campo).hide();
			$("#"+campo).val("");
		}
	}


		function cargarAutocompleteEps()
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarListaEps',
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {

			var arrayEps = data;
			var eps	= new Array();
			var index		= -1;

			for (var codEps in arrayEps)
			{
				index++;
				eps[index] = {};
				eps[index].value  = codEps;
				eps[index].label  = codEps+"-"+arrayEps[codEps];
				eps[index].nombre = arrayEps[codEps];

			}

			$( "#txtResponsable" ).autocomplete({
				minLength: 	0,
				source: 	eps,
				select: 	function( event, ui ){
					$( "#txtResponsable" ).val(ui.item.nombre);
					$( "#txtCodResponsable" ).val(ui.item.value);

					return false;
				},
				change: function( event, ui ) {
					if ( !ui.item ) {

						if(ui.item!==undefined)
						{
							// No se ha seleccionado una auxiliar válida
							jAlert("No se ha seleccionado una eps v&aacute;lida","ALERTA");
							$( "#txtResponsable" ).val("");
							$( "#txtCodResponsable" ).val("");
						}
					}
				}
			});

		},'json');

	}


	function soloNumeros(e){
		var key = window.Event ? e.which : e.keyCode;

		return ((key >= 48 && key <= 57) || key<= 8) ;
	}

	function soloNumerosymas(e){
		var key = window.Event ? e.which : e.keyCode;

		return ( (key >= 48 && key <= 57) || ( ( key<= 8) || (key == 43 ) ) ) ;
	}



	function pintarPrescripciones( opcion )//opcion permite identificar cual busqueda se va a realizar
	// 1 es general,  2 por IDs y numero de prescripcion y 3 por los web services ofrecidos.
	{




		document.body.style.cursor = 'wait';//$("body").css("cursor", "progress");
		document.getElementById('btnBuscar').style.cursor = 'wait';

//		jAlert(" seleccionado " + $("#filtroMipresProveedor").val() +" "+opcion );



		mostrarOcultarbotones('hidden');//27 mayo 2019


		//4 de junio , se crean 3 botones de busqueda , cada uno con diferentes criterios de busqueda.
		fechaInicial	= "";
		fechaFinal		= "";
		tipDocPac		= "";
		docPac			= "";
		tipDocMed		= "";
		docMed			= "";
		codEps			= "";
		tipoPrescrip	= "";

		filtroMipres	= "";
		ambitoAtencion	= "";
		filtroestadomipres	= "";

		//fitrompproveedor = ""; este era el error
		fechamipresprovedores	= "";

		nroPrescripcion	 = "";

		vIDbuscar	          = "0";
		vIDEntregabuscar	  = "0";
		vIDReportebuscar	  = "0";
//22 de Agosto 2019 , nuevos criterios de busqueda
		vIDFacturacionbuscar  = "0";
		vIDProgramacionbuscar = "0";
		vNumeroFacturabuscar  = "0";

		fitrompproveedor = $("#filtroMipresProveedor").val();
		filtroFacturacion = "";//28 agosto 2019 , nuevo criterio de busqueda
		
		switch(opcion){
			case 1:

				fechaInicial	=  $("#fechaInicial").val();
				fechaFinal		=  $("#fechaFinal").val();
				tipDocPac		=  $("#tipDocPac").val();
				docPac			= $("#docPac").val();
				tipDocMed		= $("#tipDocMed").val();
				docMed			= $("#docMed").val();
				codEps			= $("#txtCodResponsable").val();
				tipoPrescrip	= $("#tipoPrescripcion").val();
				filtroMipres	= "";//30 agosto 2019 $("#filtroMipres").val();
				ambitoAtencion	= $("#filtroAmbitoAtencion").val();
				filtroestadomipres	= $("#filtroestadomipres").val();
				filtroFacturacion =  $("#filtroFacturacion").val();//28 agosto 2018 , Freddy Saenz , nuevo criterio de busqueda.
				fitrompproveedor  = 0 ;

				break;

			case 2://busquedas por IDs o por numero de prescripcion
				nroPrescripcion	        = $("#nroPrescripcion").val();
				vIDbuscar	            =  $("#vIDbuscar").val();
				vIDEntregabuscar	    = $("#vIDEntregabuscar").val();
				vIDReportebuscar	    = $("#vIDReportebuscar").val();
				
//22 de Agosto 2019 , nuevos criterios de busqueda				
				vIDFacturacionbuscar	=  $("#vIDFacturacionbuscar").val();
				vIDProgramacionbuscar	= $("#vIDProgramacionbuscar").val();
				vNumeroFacturabuscar	= $("#vNumeroFacturabuscar").val();


				fitrompproveedor  = 0 ;

				break;

			case 3://busquedas web services

				fechamipresprovedores	= $("#fechamipresprovedores").val();

				break;

			default:
				jAlert("No implementado ");
				return;
				break;
		}



		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarReportePrescripcionMipres',
			wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
			fechaInicial	: fechaInicial,
			fechaFinal		: fechaFinal,
			tipDocPac		: tipDocPac,
			docPac			: docPac,
			tipDocMed		: tipDocMed,
			docMed			: docMed,
			codEps			: codEps,
			tipoPrescrip	: tipoPrescrip,
			filtroMipres	: filtroMipres,
			ambitoAtencion	: ambitoAtencion,
			filtroestadomipres	: filtroestadomipres,


			fitrompproveedor : fitrompproveedor,
			fechamipresprovedores	: fechamipresprovedores,


			nroPrescripcion     	: nroPrescripcion,
			vIDbuscar	            : vIDbuscar,
			vIDEntregabuscar	    : vIDEntregabuscar,
			vIDReportebuscar	    : vIDReportebuscar,

			//22 de Agosto 2019, Freddy Saenz , nuevos criterios de busqueda				
			vIDFacturacionbuscar	: vIDFacturacionbuscar,
			vIDProgramacionbuscar	: vIDProgramacionbuscar ,
			filtroFacturacion       : filtroFacturacion,
			vNumeroFacturabuscar	: vNumeroFacturabuscar


		}
		, function(data) {
			document.body.style.cursor = 'auto';
			document.getElementById('btnBuscar').style.cursor = 'auto';

			$("#listaPrescripciones").html(data);

			$('#buscar').quicksearch('#tablePrescripciones .find');

			mostrarOcultarbotones('visible');//27 mayo 2019
		} );//},'json');
		//document.body.style.cursor = 'auto';

	}//pintarPrescripciones


	function actualizarIDs(){
		nroPrescripcion = $("#nroPrescripcion").val();
		if ( nroPrescripcion != "" ){
			mensaje = "Desea actualizar los IDs para la Prescripcion: "+nroPrescripcion+" ?";


		}else{
			return;//ya no se realiza por aqui
			mensaje = "Desea actualizar los IDs del periodo:"+$("#fechaInicial").val()+" - "+$("#fechaFinal").val()+" ?";
		}


		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){




				document.body.style.cursor = 'wait';//$("body").css("cursor", "progress");
				document.getElementById('btnActualizarIDs').style.cursor = 'wait';

				mostrarOcultarbotones('hidden');//27 mayo 2019


				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'actualizarIDs',
					wemp_pmla		: $('#wemp_pmla').val(),// 21 junio 2019
					fechaInicial	: "0000-00-00",//$("#fechaInicial").val(),
					fechaFinal		: "0000-00-00",// $("#fechaFinal").val(),
					tipDocPac		: $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					filtroMipres	: "",// 30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val()

				}
				, function(data) {
					document.body.style.cursor = 'auto';
					document.getElementById('btnActualizarIDs').style.cursor = 'auto';

					$("#listaPrescripciones").html(data);
					$("#fechaultimaactualizacionIDs").html( $("#fechaFinal").val() );

					$('#buscar').quicksearch('#tablePrescripciones .find');

					mostrarOcultarbotones('visible');//27 mayo 2019

				} );//},'json');

			}

		});	//jConfirm
			//document.body.style.cursor = 'auto';

	}//function actualizarIDs()

	function valorcampoxid( vcampoid ){
		vcampo = document.getElementById(vcampoid).innerHTML;
		//16 de Octubre 2019 Freddy Saenz , se modifico el formato , se usa punto(.) como separador de miles
		
		vcampo = vcampo.replace(/\./g,"");//OJO el punto es un caracter especial  vcampo.replace(/./g,""); borrar los puntos (indican miles)
		//9 de Octubre 2019 Freddy Saenz
		return vcampo.replace(/ /g,"");//debido al formateo de numeros se esta agregando un espacion intemedio //
		//document.getElementById(vcampoid).innerHTML;
	}

	function generarJson(){//despliega un Json en una alerta



		vjson = prompt("Digite el json");//[{"Id":3303911,"IdReporteEntrega":519945}]
		if (vjson == ""){
			//return;
		}

		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'testJson',
			wemp_pmla		: $('#wemp_pmla').val(),
			infotest        : vjson
		}
		, function(data) {
			arrData = data.split("|");
			if (arrData[0] == "Error"){
				jAlert("Error ");
				//return;
			}
			 alert("ID "+arrData[0]);
			  alert("ID-secundario "+arrData[1]);
		  alert("json "+data);

		} );//},'json');


	}//function generarJson()

	function copiarprescripcionesapuntador()//copiar los numeros de las prescripciones al apuntador
	{
		//28 junio 2019
		vtxtcopy = "";
		$('input[name="seleccionprescripciones"]:checked').each(function() {

			idcheck = this.id;//borrar seleccionprescripcion_", nos queda el numero del check ($cta)
			cta = idcheck.replace(/seleccionprescripcion_/g, "");
			vnumprescripcion = valorcampoxid("idprescripcionfila_"+cta);
			if ( vtxtcopy != ""){
				vtxtcopy += "+";
			}
			vtxtcopy += vnumprescripcion.replace(" ","");
		});


		if ( vtxtcopy != "" ){//type='hidden'
			var dummy = $('<input id="vidtmpapuntador">').val(vtxtcopy).appendTo('body').select();
			//var dummy = $('<input type=\'hidden\'>').val(vtxtcopy).appendTo('body').select();
			//vtxtcopy.select();

		/* Copy the text inside the text field */
			document.execCommand("copy");

			setTimeout(() => { $("#vidtmpapuntador").remove(); }, 1000);
		}


	}//function copiarprescripcionesapuntador()//copiar los numeros de las prescripciones al apuntador


	function enviarSeleccion()
	{

		numseleccionados = 0;
		vlistaprescripciones = "";
		cadjson  = "";
		cadjsonxseleccion = "";
		cadidclaveprincipal  = "";

		cadidjsonenvio = "";//10 julio 2019
		cadjsonreporteenvio = "";//10 julio 2019
		cadidjsonprogramacion = "";//13 septiembre 2019

		//$('input[name="seleccionprescripciones"]:checked').each(function() {
	//	$('input[name="seleccionprescripciones"]').each(function() {
		$('input[name="seleccionprescripciones"]:checked').each(function() {
			idcheck = this.id;//borrar seleccionprescripcion_", nos queda el numero del check ($cta)

			cta = idcheck.replace(/seleccionprescripcion_/g, "");
			cad2 = $("#infoenviomipres_"+cta).val()  ;//informacion del mipres en json

			envioIDjson = $("#infoenvioIDmipres_"+cta).val()  ;//18 octubre 2019 //10 julio 2019 informacion del mipres en json  cadID = $("#infoenvioIDmipres_"+cta).val()  ;
			envioreportejson = $("#infoenvioreporte_"+cta).val()  ;//10 julio 2019

			vvlrcampoID =  valorcampoxid("ID-"+cta)  ;
			vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta)  ;
			vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta) ;
			vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta) ;
			
			vvlrcampoIDdireccionamiento = valorcampoxid("IDDireccionamiento-"+cta) ;
			

			vIDclaveprincipal = valorcampoxid("idregistrotabla-"+cta);

			idcheck = "infoJsonFila_"+cta;
			cadjsonxsel =  $("#"+idcheck).val() ;//informacion del mipres en json
			//cad2 = document.getElementById(idcheck).value;

			if ( vvlrcampoIDentrega == "0" ){//no se ha enviado todavia

			//NO reenviar lo que ya se ha enviado.
				if ( (cad2.length >= 5)  && (envioreportejson.length >= 5 ) ){//si tiene informacion para enviar
				//agregada condicion para reporte de entrega , 29 julio 2019 Freddy Saenz
					if (vlistaprescripciones != ""){
						vlistaprescripciones += "|";

						cadjsonxseleccion  += "|";


					}
					vlistaprescripciones += this.value;
					cadjsonxseleccion   += cadjsonxsel;


					if ( vvlrcampoID != "0" ){//se puede enviar normalmente, ya tiene ID
						if ( cadidjsonenvio != ""){
							cadidjsonenvio += "|";
						}
						if (  cadjsonreporteenvio != "" ){
							cadjsonreporteenvio  += "|";
						}
						cadidjsonenvio += envioIDjson;//10 julio 2019
						cadjsonreporteenvio += envioreportejson;//10 julio 2019

						vFechaMax = valorcampoxid("idcampofechamaxentrega_"+cta);
						vvlrcantidad  = valorcampoxid("divcantidad_"+cta);
						vcodtecnologia  = valorcampoxid("idcodigotecnologia_"+cta) ;
						if ( ( vFechaMax != "0000-00-00" ) && ( vcodtecnologia != "" ) ) {//Tiene fecha maxima de entrega
						//tiene codigo de tecnologia
							//y no tiene programacion enviada pero tiene direccionamiento
							
							if ( ( vvlrcampoIDprogramacion == "0" )  && ( vvlrcampoIDdireccionamiento != "0" ) ) {

								//enviar la programacion


								var cadinfo =  '{ "ID" :  '  + vvlrcampoID +'  ';
								cadinfo =  cadinfo +  ' ,  "FecMaxEnt" :  "'               +  vFechaMax +'" ';

								cadinfo =  cadinfo +  ' ,  "TipoIDSedeProv" :  "'       +  "" +'" ';
								cadinfo =  cadinfo +  ' ,  "NoIDSedeProv" :  "'        +  "" +'" ';
								cadinfo =  cadinfo +  ' ,  "CodSedeProv" : "'            +  "" +'" ';

								cadinfo =  cadinfo +  ' ,  "CodSerTecAEntregar" :  "' +  valorcampoxid("idcodigotecnologia_"+cta) +'" ';
								cadinfo =  cadinfo +  ' ,  "CantTotAEntregar" :  "'         +  valorcampoxid("divcantidad_"+cta) +'" ';


								cadinfo =  cadinfo +  ' } ' ;

								if ( cadidjsonprogramacion != "" ){
									cadidjsonprogramacion += "|";
								}
								cadidjsonprogramacion +=  cadinfo;


							}
						}



					}else{//envio por ambito de entrega

						if ( cadjson != "" ){
							cadjson  += "|";
						}
						cadjson  += cad2;//es esta cadena la que se debe enviar en el post,para despues hacer un split y adicionar a un arreglo

					}



				}//if (cad2.length>= 5){//si tiene informacion para enviar

			}//if ( vvlrcampoIDentrega == 0 ){//no se ha enviado todavia

			numseleccionados++;
//No filtrar los ids de los seleccionados , para despues poder mostrarlos y hacer una revision de porque no se puedieron enviar
			vnumeroreg = parseInt(vIDclaveprincipal);
			if ( vnumeroreg != 0 ){
				if ( cadidclaveprincipal != ""){
					cadidclaveprincipal += ",";
				}
				cadidclaveprincipal += " " + vnumeroreg + " ";

			}
			
			
		});//$('input[name="seleccionprescripciones"]:checked').each(function()



		cadjson = cadjson.replace(/'/g, "\"");
		cadjsonxseleccion = cadjsonxseleccion.replace(/'/g, "\"");

		if ( numseleccionados == 0){
			jAlert("No hay registros seleccionados ");
			return ;
		}

		if ( ( cadjson + cadidjsonenvio )== "" ){
			jAlert("Los registros seleccionados no tienen ningun consumo ");
			return;
		}

		mensaje = "Desea enviar el listado ?";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){





				document.body.style.cursor = 'wait';//$("body").css("cursor", "progress");
				document.getElementById('btnEnviarListado').style.cursor = 'wait';
				document.getElementById('btnEnviarListado').style.visibility = 'hidden';
				mostrarOcultarbotones('hidden');//27 mayo 2019

				$.post("mipres_suministros.php",
				{
					consultaAjax    	: '',
					accion			  : 'enviarSeleccion',
					wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
					fechaInicial	:  $("#fechaInicial").val(),
					fechaFinal		:  $("#fechaFinal").val(),
					tipDocPac		:  $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					filtroMipres	: "" , //30 agosto 2019 , $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					filtroestadomipres	: $("#filtroestadomipres").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					vIDbuscar	    :  $("#vIDbuscar").val(),
					vIDEntregabuscar	: $("#vIDEntregabuscar").val(),
					vIDReportebuscar	: $("#vIDReportebuscar").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val()	,

					cadidjsonenvio : cadidjsonenvio,//10 julio 2019
					cadjsonreporteenvio : cadjsonreporteenvio,//10 julio 2019
					cadidjsonprogramacion : cadidjsonprogramacion,//13 septiembre 2019

					vlistajson        : cadjson,
					cadjsonxseleccion : cadjsonxseleccion,  //27 mayo 2019, se usar para cargar nuevamente la seleccion actual
					cadidclaveprincipal : cadidclaveprincipal//usar los ids de los registros

				}
				, function(data) {


					document.body.style.cursor = 'auto';
					document.getElementById('btnEnviarListado').style.cursor = 'auto';

					$("#listaPrescripciones").html(data);

					$('#buscar').quicksearch('#tablePrescripciones .find');

					mostrarOcultarbotones('visible');//27 mayo 2019
					document.getElementById('btnEnviarListado').style.visibility = 'visible';

				} );//},'json');
			}
		});	//jConfirm

	}//function enviarSeleccion()

	//JAVASCRIPT DEL JSON DE FACTURACION
	//javacript con la generacion del Json de Facturacion , los datos de nit ,eps , son completados en el PHP debido a que son consultas
	// a la base de datos.
	function enviarFacturacionSeleccion(){

	
		numseleccionados = 0;
		vlistaprescripciones = "";
		cadjson  = "";
		cadjsonxseleccion = "";
		cadidclaveprincipal  = "";

		cadidjsonenvio = "";//10 julio 2019
		cadjsonreporteenvio = "";//10 julio 2019


		$('input[name="seleccionprescripciones"]:checked').each(function() {

			idcheck = this.id;//borrar seleccionprescripcion_", nos queda el numero del check ($cta)

			cta = idcheck.replace(/seleccionprescripcion_/g, "");
			
			vvlrcampoID = valorcampoxid("ID-"+cta);
			vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
			vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
			vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);

			vIDclaveprincipal = valorcampoxid("idregistrotabla-"+cta);

			vvlrcampoIDFacturacion = valorcampoxid("IDFacturacion-"+cta);//30 de agosto 2019

			vcodtecnoligaxfact  = valorcampoxid("idcodigotecnologia_"+cta)  ;
			
			cad2 = $("#infofacturacionmipres_"+cta).val()  ;//informacion del mipres en json

			if ( ( vvlrcampoID != 0 ) && ( vcodtecnoligaxfact != "") ) {//si tiene ID principal y tiene codigo de tecnologia
			
			//por aqui esta el error.
		//cadinfo +=  ' , "NoPrescripcion" :  '  +  valorcampoxid("idprescripcionfila_"+cta);


				//vtotal = valorcampoxid("divcantidad_"+cta);
				//vtotal *=  valorcampoxid("valortecnologia_"+cta);
				
				vtotal    = parseInt ( valorcampoxid("idvlrentregadoreporte_"+cta) );
				vcantidad = valorcampoxid("divcantidad_"+cta);
				if (vcantidad != 0 ){
					vlrunitario = vtotal / vcantidad;//1
				}else{
					vlrunitario = 0;
				}
				//16 octubre 2019 Freddy Saenz , el web service de facturacion usa como separador decimal la coma (,)
				// pero la division por cantidad usa como separador decimal el punto (.)
				//vlrunitario = Math.round ( vlrunitario ) ;//sin decimales
				vlrunitario = Math.round ( vlrunitario *100 ) / 100;//solo 2 decimales
				vlrunitstr =  vlrunitario.toString() ;
				vlrunitstr = vlrunitstr.replace(/\./g, ",");//reemplazar el punto(.) decimal por coma

				var cadinfo =  '{ "NoPrescripcion" :  "'  + valorcampoxid("idprescripcionfila_"+cta)+'" ';

				cadinfo =  cadinfo +  ' ,  "TipoTec" :  "'               +  valorcampoxid("idtipotecnologia_"+cta)+'" ';

				cadinfo =  cadinfo +  ' ,  "ConTec" :  '                +  valorcampoxid("idcampoconsecutivo_"+cta);//numerico
				cadinfo =  cadinfo +  ' ,  "TipoIDPaciente" :  "'       +  valorcampoxid("idtipoidpaciente_"+cta) +'" ';

				cadinfo =  cadinfo +  ' ,  "NoIDPaciente" :  "'        +  valorcampoxid("idnumeroidpaciente_"+cta) +'" ';
				cadinfo =  cadinfo +  ' ,  "NoEntrega" :  '            +  valorcampoxid("idnumerodeentrega_"+cta);//numerico

				cadinfo =  cadinfo +  ' ,  "NoFactura" : "'            +  valorcampoxid("idnumfact_"+cta) +'" ';
				cadinfo =  cadinfo +  ' ,  "NoIDEPS" :  "'            +  valorcampoxid("idniteps_"+cta) +'" ';

				cadinfo =  cadinfo +  ' ,  "CodEPS" :  "'              +  valorcampoxid("diveps-"+cta) +'" '; 
				cadinfo =  cadinfo +  ' ,  "CodSerTecAEntregado" :  "' +  valorcampoxid("idcodigotecnologia_"+cta) +'" ';
//Usar mejor las cantidades y valores de los consumos(registrados usando los web services) y no los de facturacion debido a que se pueden producir errores con el manejo de la unidad de medida.
				cadinfo =  cadinfo +  ' ,  "CantUnMinDis" :  "'         +  valorcampoxid("divcantidad_"+cta) +'" ';
				cadinfo =  cadinfo +  ' ,  "ValorUnitFacturado" :  "'         +  vlrunitstr +'" ';//valorcampoxid("valortecnologia_"+cta)//2
				//cadinfo =  cadinfo +  ' ,  "ValorUnitFacturado" :  '   +  valorcampoxid("valortecnologia_"+cta);  //aqui esta el error

				cadinfo +=  ' ,  "ValorTotFacturado" :  "'   +   valorcampoxid("idvlrentregadoreporte_"+cta) +'" ' ;//vtotal ;
				cadinfo =  cadinfo +  ' ,  "CuotaModer" :  "'  +  valorcampoxid("idcuotamoderadorafact_"+cta) +'" ';
				cadinfo =  cadinfo +  ' ,  "Copago" : "' +  valorcampoxid("idcoopagofact_"+cta) +'" ';
				cadinfo =  cadinfo +  ' } ' ;	/*	*/


					idcheck = "infoJsonFila_"+cta;
					cadjsonxsel =  $("#"+idcheck).val() ;//informacion del mipres en json
					
					factura = valorcampoxid("idnumfact_"+cta);


					if ( ( vvlrcampoIDFacturacion == 0 ) && (factura != 0 ) ) {//no se ha enviado todavia
						//	alert(cad2+" zz enviar facturacion seleccion check "+cta);

					//NO reenviar lo que ya se ha enviado.
						if  ( cadinfo.length >= 5 )  {//si tiene informacion para enviar
						//agregada condicion para reporte de entrega , 29 julio 2019 Freddy Saenz
							if (vlistaprescripciones != ""){
								vlistaprescripciones += "|";

								cadjsonxseleccion  += "|";
								cadjson += "|";

							}
							vlistaprescripciones += this.value;
							cadjsonxseleccion   += cadjsonxsel;
							//cadjson += cadinfo ;

						//	alert(cadjsonxseleccion+" enviar facturacion seleccion check "+cta);

							if ( vvlrcampoID != 0 ){//se puede enviar normalmente, ya tiene ID
								if ( cadidjsonenvio != ""){
									cadidjsonenvio += "|";
								}

								cadidjsonenvio += cadinfo;//10 julio 2019 , este es el json que se enviara.


							}



						}//if (cad2.length>= 5){//si tiene informacion para enviar

					}//if ( vvlrcampoIDentrega == 0 ){//no se ha enviado todavia

			
			}//if ( vvlrcampoID != 0 ) {//si tiene ID principal

		
			envioIDjson = $("#infofacturacionmipres_"+cta).val()  ;

			


			numseleccionados++;
//No filtrar los ids de los seleccionados , para despues poder mostrarlos y hacer una revision de porque no se puedieron enviar
			vnumeroreg = parseInt(vIDclaveprincipal);
			if ( vnumeroreg != 0 ){
				if ( cadidclaveprincipal != ""){
					cadidclaveprincipal += ",";
				}
				cadidclaveprincipal += " " + vnumeroreg + " ";

			}
			
			
		});//$('input[name="seleccionprescripciones"]:checked').each(function()




		cadjsonxseleccion = cadjsonxseleccion.replace(/'/g, "\"");

		if ( numseleccionados == 0){
			jAlert("No hay registros seleccionados ");
			return ;
		}

		if ( (  cadidjsonenvio )== "" ){
			jAlert("Los registros seleccionados ya fueron enviados o no se ha Reportado la Entrega o no tienen Facturas asociadas ");
			return;
		}

		mensaje = "Desea enviar el listado de Facturas ?";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){





				document.body.style.cursor = 'wait';//$("body").css("cursor", "progress");
				document.getElementById('btnEnviarFacturacionSel').style.cursor = 'wait';
				document.getElementById('btnEnviarFacturacionSel').style.visibility = 'hidden';
				mostrarOcultarbotones('hidden');//27 mayo 2019

				$.post("mipres_suministros.php",
				{
					consultaAjax    	: '',
					accion			  : 'enviarFacturacionSeleccion',
					wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
					fechaInicial	:  $("#fechaInicial").val(),
					fechaFinal		:  $("#fechaFinal").val(),
					tipDocPac		:  $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					filtroMipres	: "" , //30 agosto 2019 , $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					filtroestadomipres	: $("#filtroestadomipres").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					vIDbuscar	    :  $("#vIDbuscar").val(),
					vIDEntregabuscar	: $("#vIDEntregabuscar").val(),
					vIDReportebuscar	: $("#vIDReportebuscar").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val()	,

					cadidjsonenvio : cadidjsonenvio,//10 julio 2019
					cadjsonreporteenvio : cadjsonreporteenvio,//10 julio 2019


					vlistajson        : cadjson,
					cadjsonxseleccion : cadjsonxseleccion,  //27 mayo 2019, se usar para cargar nuevamente la seleccion actual
					cadidclaveprincipal : cadidclaveprincipal//usar los ids de los registros

				}
				, function(data) {


					document.body.style.cursor = 'auto';
					document.getElementById('btnEnviarFacturacionSel').style.cursor = 'auto';

					$("#listaPrescripciones").html(data);

					$('#buscar').quicksearch('#tablePrescripciones .find');

					mostrarOcultarbotones('visible');//27 mayo 2019
					document.getElementById('btnEnviarFacturacionSel').style.visibility = 'visible';

				} );//},'json');
			}
		});	//jConfirm
	
	}
	
	
	function usarSeleccion(){
		//https://stackoverflow.com/questions/38185847/get-a-list-of-all-checked-checkboxes



		numseleccionados = 0;
		vlistaprescripciones = "";
		cadjson  = "";
		cadidjson = "";
		cadidclaveprincipal  = "";

		$('input[name="seleccionprescripciones"]:checked').each(function() {
			numseleccionados++;
			idcheck = this.id;//borrar seleccionprescripcion_", nos queda el numero del check ($cta)

			cta = idcheck.replace(/seleccionprescripcion_/g, "");
			//alert("cta "+cta);
			idcheck = "infoJsonFila_"+cta;
			cad2 =  $("#"+idcheck).val() ;//informacion del mipres en json
			//cad2 = document.getElementById(idcheck).value;


			vID = "0";
			vIDEntrega = "0";
			vIDReporteEntrega = "0";
			vID = $("#ID-"+cta).val() ;

			vIDEntrega = $("#IDEntrega-"+cta).val() ;
			vIDReporteEntrega = $("#IReporteDEntrega-"+cta).val() ;

			vIDclaveprincipal = valorcampoxid("idregistrotabla-"+cta);
		//	vIDclaveprincipal = document.getElementById("idregistrotabla-"+cta).value;
			//$("#idregistrotabla-"+cta).val() ;


			vIDProgramacion = "0" ;
			vIDDireccionamiento = "0" ;


	// Los Ids Actualizados con la informacion actualizada.

			cadconIds = " , \"IDjson\":\"" + vID + "\"";//debe tener una coma al comienzo
			cadconIds += "," + "\"IDEntregajson\":\"" + vIDEntrega + "\"";
			cadconIds += "," + "\"IDReporteEntregajson\":\"" + vIDReporteEntrega + "\"";
			cadconIds += "," + "\"IDDireccionamientojson\":\"" + vIDDireccionamiento + "\"";
			cadconIds += "," + "\"IDProgramacionjson\":\"" + vIDProgramacion + "\"";
			vnumeroreg = parseInt(vIDclaveprincipal);
			if ( vnumeroreg != 0 ){
				if ( cadidclaveprincipal != ""){
					cadidclaveprincipal += ",";
				}
				cadidclaveprincipal += " " + vnumeroreg + " ";
			}

			if (vlistaprescripciones != ""){
				vlistaprescripciones += "|";
				cadjson  += "|";
				cadidjson  += "|";
			}
			vlistaprescripciones += this.value;
		//	cadjson  += cad2  ;//es esta cadena la que se debe enviar en el post,para despues hacer un split y adicionar a un arreglo
		// Json1 = {a ,b,c,d } y Json2(sin llaves) = ,f,g  => Json1+Json2 =  {a ,b,c,d ,f,g  }
			cadjson  += cad2.substring( 0 , cad2.length - 1 ) + cadconIds + "}"  ;//reemplaza el ultimo } y se agrega la informacion de los IDs al final.
			cadidjson +=  cadconIds;

		});

		cadjson = cadjson.replace(/'/g, "\"");
//		cadidjson = cadidjson.substring( 0 , cadidjson.length - 1 ) + cadconIds + "}";
		//cadidjson = cadidjson.replace(/\}/g, "\"");

		if ( numseleccionados == 0){
			jAlert("No hay registros seleccionados ");
			return ;
		}

		mensaje = "Desea usar la seleccion ?";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){


				document.body.style.cursor = 'wait';//$("body").css("cursor", "progress");
				document.getElementById('btnUsarSeleccion').style.cursor = 'wait';

				mostrarOcultarbotones('hidden');//27 mayo 2019

				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'usarSeleccion',
					wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
					fechaInicial	:  $("#fechaInicial").val(),
					fechaFinal		:  $("#fechaFinal").val(),
					tipDocPac		:  $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					filtroMipres	: "",//30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					filtroestadomipres	: $("#filtroestadomipres").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					
					vIDbuscar	            :  $("#vIDbuscar").val(),
					vIDEntregabuscar	    : $("#vIDEntregabuscar").val(),
					vIDReportebuscar      	: $("#vIDReportebuscar").val(),
					
					fechamipresprovedores	:  $("#fechamipresprovedores").val()	,
//22 de Agosto 2019, Freddy Saenz , nuevos criterios de busqueda //$vIDFacturacionbuscar , $vIDProgramacionbuscar , $vNumeroFacturabuscar
					vIDFacturacionbuscar	:  $("#vIDFacturacionbuscar").val(),
					vIDProgramacionbuscar	:  $("#vIDProgramacionbuscar").val(),
					vNumeroFacturabuscar	:  $("#vNumeroFacturabuscar").val(),

					vlistajson      : cadjson ,
					vlistaIDsjson   : cadidjson,
					 
					filtroFacturacion :  $("#filtroFacturacion").val(),//28 agosto 2018 , Freddy Saenz , nuevo criterio de busqueda.
					cadidclaveprincipal : cadidclaveprincipal//usar los ids de los registros
				}
				, function(data) {

					document.body.style.cursor = 'auto';
					document.getElementById('btnUsarSeleccion').style.cursor = 'auto';

					$("#listaPrescripciones").html(data);

					$('#buscar').quicksearch('#tablePrescripciones .find');

					mostrarOcultarbotones('visible');//27 mayo 2019
				} );//},'json');
			}
		});	//jConfirm


	}//function usarSeleccion()



	//El sistema genera un token diario , pero la vigencia de un token es de solo 12 horas
	//puede ser necesario generar un token despues de estas 12 horas en el mismo dia
	function generarnuevotoken()
	{

//		jAlert("El sistema genera un nuevo token cada dia");
		//Se usa el jConfirm porque en confirm solo aparece en posterior de la ventana de jAlert.
		mensaje = 'Desea generar un nuevo Token ?';

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'generarNuevoToken',
					wemp_pmla		: $('#wemp_pmla').val()

				}
				, function(data) {


					$("#idtokendia").html(data);
					jAlert("Nuevo Token : " +data);

				} );//},'json');
			}
		});	//jConfirm

	}//function generarnuevotoken()



	function actualizarcantidad(cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed , vID, estadoentrega, causanoentrega ,vtipotec , numprescripcion , vconsecutivo , fechaaplicacion )
	{

		mensaje = "Desea modificar la cantidad "+cantart+ " ? ";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){




				jPrompt("Digite la nueva cantidad (max="+cantidatTotAEntregar+") ", cantart, 'Prompt Dialog', function(respuesta) {
				if( respuesta ) {//se ingreso informacion

					cantnueva = respuesta;



					if (cantnueva == null){
						return;
					}

					if  (cantnueva == 0) {
						//Si es posible dejar la cantidad en cero .
						//jAlert("Con cero se anula el mipres ");
						//return;

					}else if ( cantnueva > cantidatTotAEntregar ){
						jAlert("Cantidad superior al maximo registrado ");
						return;
					}else{

					}

					mostrarOcultarbotones('hidden');//27 mayo 2019

					cad2 = $("#infoenviomipres_"+cta).val()  ;


					cadID = $("#infoenvioIDmipres_"+cta).val()  ;

					nroentrega = valorcampoxid("idnumerodeentrega_"+cta);//3 julio 2019 , usar numero de entrega en la clave principal Freddy Saenz



					$.post("mipres_suministros.php",
					{
						consultaAjax 	: '',
						accion			: 'actualizarCantidad',

						wemp_pmla		: $('#wemp_pmla').val(),
						fechaInicial	: $("#fechaInicial").val(),
						fechaFinal		: $("#fechaFinal").val(),
						tipDocPac		: $("#tipDocPac").val(),
						docPac			: $("#docPac").val(),
						tipDocMed		: $("#tipDocMed").val(),
						docMed			: $("#docMed").val(),
						codEps			: $("#txtCodResponsable").val(),
						tipoPrescrip	: $("#tipoPrescripcion").val(),
						nroPrescripcion	: $("#nroPrescripcion").val(),
						filtroMipres	: "",//30 agosto 2019 ,$("#filtroMipres").val(),
						ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
						fitrompproveedor : $("#filtroMipresProveedor").val(),
						fechamipresprovedores	: $("#fechamipresprovedores").val(),

						cta : cta,
						cantart : cantnueva,
						cantanterior : cantart,
						cantidatTotAEntregar : cantidatTotAEntregar,
						vIDEntrega : vIDEntrega,
						vIDReporteEntrega : vIDReporteEntrega,
						codservxmipres : codservxmipres,
						valormed : valormed,

						numprescripcion : numprescripcion,
						vtipotec        : vtipotec,
						vconsecutivo    : vconsecutivo ,

						vID :vID,
						estadoentrega : estadoentrega,
						causanoentrega :causanoentrega,
						vtxtarrenvio : cad2,
						vfechaentrega : fechaaplicacion,
						nroentrega    :  nroentrega,
						vtxtIDarrenvio : cadID




					}
					, function(data) {



					//	document.getElementById('divenvio-'+cta).innerHTML = data;

						$("#divenvio-"+cta).html(data);

						jAlert("Actualizada la cantidad de "+cantart + " por " +cantnueva);
						$('#buscar').quicksearch('#tablePrescripciones .find');

						mostrarOcultarbotones('visible');//27 mayo 2019

					} );//},'json');
				}
			});//del jPrompt("Digite la nueva cantidad (max="+cantidatTotAEntregar+")

			}
		});	//jConfirm



	}//actualizarcantidad(cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed  )



	function actualizarcodigo(cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed , vID, estadoentrega, causanoentrega , vtipotec , numprescripcion , vconsecutivo , fechaaplicacion )
	{

		mensaje = "Desea modificar el codigo de la tecnologia usada :"+codservxmipres+ " ? ";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

	//		codigonuevo = prompt("Digite el nuevo codigo ",codservxmipres);//
			//modificando 30
				jPrompt("Digite el nuevo codigo ", codservxmipres, 'Prompt Dialog', function(respuesta) {
				if( respuesta ) {//se ingreso informacion
					codigonuevo = respuesta;

					if (codigonuevo == null){
						return;
					}

					if  (codigonuevo == "") {
						jAlert("no se ingreso informacion ");
						return;

					}

					mostrarOcultarbotones('hidden');//27 mayo 2019

					cad2 = $("#infoenviomipres_"+cta).val()  ;


					cadID = $("#infoenvioIDmipres_"+cta).val()  ;

					nroentrega = valorcampoxid("idnumerodeentrega_"+cta);//3 julio 2019 , usar numero de entrega en la clave principal Freddy Saenz




					$.post("mipres_suministros.php",
					{
						consultaAjax 	: '',
						accion			: 'actualizarCodigo',

						wemp_pmla		: $('#wemp_pmla').val(),
						fechaInicial	: $("#fechaInicial").val(),
						fechaFinal		: $("#fechaFinal").val(),
						tipDocPac		: $("#tipDocPac").val(),
						docPac			: $("#docPac").val(),
						tipDocMed		: $("#tipDocMed").val(),
						docMed			: $("#docMed").val(),
						codEps			: $("#txtCodResponsable").val(),
						tipoPrescrip	: $("#tipoPrescripcion").val(),
						nroPrescripcion	: $("#nroPrescripcion").val(),
						filtroMipres	:  "",//30 agosto 2019$("#filtroMipres").val(),
						ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
						fitrompproveedor : $("#filtroMipresProveedor").val(),
						fechamipresprovedores	: $("#fechamipresprovedores").val(),

						cta : cta,
						cantart : cantart,
						cantanterior : cantart,
						cantidatTotAEntregar : cantidatTotAEntregar,
						vIDEntrega : vIDEntrega,
						vIDReporteEntrega : vIDReporteEntrega,
						codservxmipres : codigonuevo,
						valormed : valormed,
						codigoanterior : codservxmipres,
						vfechaentrega : fechaaplicacion,

						numprescripcion : numprescripcion,
						vtipotec        : vtipotec,
						vconsecutivo    : vconsecutivo ,


						vID :vID,
						estadoentrega : estadoentrega,
						causanoentrega :causanoentrega,
						vtxtarrenvio : cad2,
						nroentrega   : nroentrega,

						vtxtIDarrenvio : cadID




					}
					, function(data) {


						$("#divenvio-"+cta).html(data);

						jAlert("Actualizado el codigo de "+codservxmipres + " por " +codigonuevo);
						$('#buscar').quicksearch('#tablePrescripciones .find');

						mostrarOcultarbotones('visible');//27 mayo 2019


					} );//},'json');

				}
			});//jPrompt("Digite el nuevo codigo ", codservxmipres, 'Prompt Dialog', function(respuesta)

			}
		});	//jConfirm


	}//actualizarcodigo(cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed  )


	function actualizarvalorunitario(cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed , vID, estadoentrega, causanoentrega , vtipotec , numprescripcion , vconsecutivo , fechaaplicacion )
	{

		mensaje = "Desea modificar el valor unitario  :"+valormed+ " ? ";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

				jPrompt("Digite el nuevo valor ", valormed, 'Prompt Dialog', function(respuesta) {
				if( respuesta ) {//se ingreso informacion
					codigonuevo = respuesta;

					if (respuesta == null){
						return;
					}

					if  (respuesta == "") {
						jAlert("no se ingreso informacion ");
						return;

					}

					mostrarOcultarbotones('hidden');//27 mayo 2019

					cad2 = $("#infoenviomipres_"+cta).val()  ;


					cadID = $("#infoenvioIDmipres_"+cta).val()  ;

					nroentrega = valorcampoxid("idnumerodeentrega_"+cta);//3 julio 2019 , usar numero de entrega en la clave principal Freddy Saenz



					$.post("mipres_suministros.php",
					{
						consultaAjax 	: '',
						accion			: 'actualizarValorUnitario',

						wemp_pmla		: $('#wemp_pmla').val(),
						fechaInicial	: $("#fechaInicial").val(),
						fechaFinal		: $("#fechaFinal").val(),
						tipDocPac		: $("#tipDocPac").val(),
						docPac			: $("#docPac").val(),
						tipDocMed		: $("#tipDocMed").val(),
						docMed			: $("#docMed").val(),
						codEps			: $("#txtCodResponsable").val(),
						tipoPrescrip	: $("#tipoPrescripcion").val(),
						nroPrescripcion	: $("#nroPrescripcion").val(),
						filtroMipres	:  "",//30 agosto 2019 $("#filtroMipres").val(),
						ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
						fitrompproveedor : $("#filtroMipresProveedor").val(),
						fechamipresprovedores	: $("#fechamipresprovedores").val(),

						cta                  : cta,
						cantart              : cantart,
						cantanterior         : cantart,
						cantidatTotAEntregar : cantidatTotAEntregar,
						vIDEntrega           : vIDEntrega,
						vIDReporteEntrega    : vIDReporteEntrega,
						codservxmipres       : codservxmipres,
						valormed             : respuesta,//valormed,
						codigoanterior       : codservxmipres,
						vfechaentrega        : fechaaplicacion,

						numprescripcion      : numprescripcion,
						vtipotec             : vtipotec,
						vconsecutivo         : vconsecutivo ,


						vID                  : vID,
						estadoentrega        : estadoentrega,
						causanoentrega       : causanoentrega,
						vtxtarrenvio         : cad2,
						nroentrega           : nroentrega,

						vtxtIDarrenvio       : cadID




					}
					, function(data) {


						$("#divenvio-"+cta).html(data);

						jAlert("Actualizado el valor de "+valormed + " por " +respuesta);
						$('#buscar').quicksearch('#tablePrescripciones .find');

						mostrarOcultarbotones('visible');//27 mayo 2019


					} );//},'json');

				}
			});//jPrompt("Digite el nuevo codigo ", codservxmipres, 'Prompt Dialog', function(respuesta)

			}
		});	//jConfirm


	}//actualizarvalorunitario(cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed  )

	function actualizarfechaaplicacion(cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed , vID, estadoentrega, causanoentrega , vtipotec , numprescripcion , vconsecutivo , fechaaplicacion )
	{
	//	vvlrcampofechamipres1 = valorcampoxid("idfechamipres_"+cta);
		vvlrcampofechamipres = valorcampoxid("idfechaaplicacion_"+cta);



		if (vvlrcampofechamipres != ""){
			vfechaxdefecto = vvlrcampofechamipres;
		}else{
			vfechaxdefecto = fechaaplicacion;
		}
		mensaje = "Desea modificar la Fecha de Aplicacion :"+vfechaxdefecto+ " ? ";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

				jPrompt("Digite la nueva fecha aaaa-mm-dd ", vfechaxdefecto, 'Prompt Dialog', function(respuesta) {
				if( respuesta ) {//se ingreso informacion
					codigonuevo = respuesta;

					if (respuesta == null){
						return;
					}

					if  (respuesta == "") {
						jAlert("no se ingreso informacion ");
						return;

					}

					mostrarOcultarbotones('hidden');//27 mayo 2019

					cad2 = $("#infoenviomipres_"+cta).val()  ;


					cadID = $("#infoenvioIDmipres_"+cta).val()  ;

					nroentrega = valorcampoxid("idnumerodeentrega_"+cta);//3 julio 2019 , usar numero de entrega en la clave principal Freddy Saenz




					$.post("mipres_suministros.php",
					{
						consultaAjax 	: '',
						accion			: 'actualizarFechaAplicacion',

						wemp_pmla		: $('#wemp_pmla').val(),
						fechaInicial	: $("#fechaInicial").val(),
						fechaFinal		: $("#fechaFinal").val(),
						tipDocPac		: $("#tipDocPac").val(),
						docPac			: $("#docPac").val(),
						tipDocMed		: $("#tipDocMed").val(),
						docMed			: $("#docMed").val(),
						codEps			: $("#txtCodResponsable").val(),
						tipoPrescrip	: $("#tipoPrescripcion").val(),
						nroPrescripcion	: $("#nroPrescripcion").val(),
						filtroMipres	:  "",//30 agosto 2019 $("#filtroMipres").val(),
						ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
						fitrompproveedor : $("#filtroMipresProveedor").val(),
						fechamipresprovedores	: $("#fechamipresprovedores").val(),

						cta : cta,
						cantart : cantart,
						cantanterior : cantart,
						cantidatTotAEntregar : cantidatTotAEntregar,
						vIDEntrega : vIDEntrega,
						vIDReporteEntrega : vIDReporteEntrega,
						codservxmipres : codservxmipres,
						valormed : valormed,
						codigoanterior : codservxmipres,

						vfechaentrega : respuesta,

						numprescripcion : numprescripcion,
						vtipotec        : vtipotec,
						vconsecutivo    : vconsecutivo ,


						vID :vID,
						estadoentrega : estadoentrega,
						causanoentrega :causanoentrega,
						vtxtarrenvio : cad2,
						nroentrega   : nroentrega,

						vtxtIDarrenvio : cadID




					}
					, function(data) {


						$("#divenvio-"+cta).html(data);

						jAlert("Actualizada la fecha de aplicacion "+fechaaplicacion + " por " +respuesta);
						$('#buscar').quicksearch('#tablePrescripciones .find');

						mostrarOcultarbotones('visible');//27 mayo 2019


					} );//},'json');

				}
			});//jPrompt("Digite el nuevo codigo ", codservxmipres, 'Prompt Dialog', function(respuesta)

			}
		});	//jConfirm


	}//actualizarfechaaplicacion(cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed  )




	function actualizarcausanoentrega ( cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed , vID, estadoentrega, causanoentrega , vtipotec , numprescripcion , vconsecutivo , fechaaplicacion , elemento )
	{


		cad = "";

		descripcion = elemento.options[elemento.selectedIndex].text;

		mensaje = "Desea cambiar la causa de NO Entrega a: ["+elemento.value+"] "+descripcion+" ? ";


		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){





				mostrarOcultarbotones('hidden');//27 mayo 2019

				cad2 = $("#infoenviomipres_"+cta).val()  ;
				cadID = $("#infoenvioIDmipres_"+cta).val()  ;
				nroentrega = valorcampoxid("idnumerodeentrega_"+cta);//3 julio 2019 , usar numero de entrega en la clave principal Freddy Saenz

				valornuevo = elemento.value;



				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'actualizarCausanoentrega',

					wemp_pmla		: $('#wemp_pmla').val(),
					fechaInicial	: $("#fechaInicial").val(),
					fechaFinal		: $("#fechaFinal").val(),
					tipDocPac		: $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					filtroMipres	:  "",//30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),


					cta : cta,
					cantart : cantart,
					cantanterior : cantart,
					cantidatTotAEntregar : cantidatTotAEntregar,
					vIDEntrega : vIDEntrega,
					vIDReporteEntrega : vIDReporteEntrega,
					codservxmipres : codservxmipres,
					valormed : valormed,
					codigoanterior : codservxmipres,

					numprescripcion : numprescripcion,
					vtipotec        : vtipotec,
					vconsecutivo    : vconsecutivo ,

					vfechaentrega : fechaaplicacion,

					vID :vID,
					estadoentrega : estadoentrega,
					causanoentrega : valornuevo, //causanoentrega,
					vtxtarrenvio : cad2,
					nroentrega       :  nroentrega,

					vtxtIDarrenvio : cadID




				}
				, function(data) {


					//$("#divenvio-"+cta).html(data);
					//jAlert(data);
					jAlert("Actualizado la causa de NO Entrega :  " +elemento.value);
					$('#buscar').quicksearch('#tablePrescripciones .find');

					mostrarOcultarbotones('visible');//27 mayo 2019


				} );//},'json');

			}
		});	//jConfirm

	}//function actualizarcausanoentrega ( cantart ,cta, cantidatTotAEntregar , vIDEntrega , vIDReporteEntrega , codservxmipres, valormed , vID, estadoentrega, causanoentrega , elemento



	function actualizarcodigosede (  )
	{

		//PROV000807
		codsedeprogramacion = valorcampoxid("idcodigosedeprogramacion");


		codsede = prompt("Digite el codigo de la sede: ", codsedeprogramacion);
		if (!codsede){
			return;
		}
		respuesta = codsede;

		if (respuesta == null){
			return;
		}

		if  (respuesta == "") {
			jAlert("no se ingreso informacion ");
			return;

		}






		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'actualizarCodigoSede',

			wemp_pmla		: $('#wemp_pmla').val(),
			codsede         : respuesta



		}
		, function(data) {


			$("#idcodigosedeprogramacion").html(data);

		//	jAlert("Actualizado el codigo de la sede de : "+codsedeprogramacion + " por " +respuesta);



		} );//},'json');



	}//function actualizarcodigosede ( codsede

//
	function actualizartecnologiaprogramacion( idmipres ,vidprogramacion , codtecnologia , iddivtecnologiaprogramacion , filapaginaprincipal ){
		codtecnologia = valorcampoxid("iddivtecnologiaprogramacion");

		if ( idmipres == 0 ){
			jAlert("No tiene ID principal ");
			return;
		}
		codsede = prompt("Digite la tecnologia : ", codtecnologia);
		if (!codsede){
			return;
		}
		respuesta = codsede;

		if (respuesta == null){
			return;
		}

		if  (respuesta == "") {
			jAlert("no se ingreso la tecnologia ");
			return;

		}






		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'actualizartecnologiaxprogramacion',

			wemp_pmla		: $('#wemp_pmla').val(),
			vID             : idmipres ,
			vIDProgramacion : vidprogramacion ,
			
			codtecnologia         : respuesta



		}
		, function(data) {


			$("#iddivtecnologiaprogramacion").html(data);
			
			$("#"+"idcodigotecnologia_"+filapaginaprincipal).html(data);
			$("#"+"tecnologiaxprescripcion_"+filapaginaprincipal).html(data);


			jAlert("Actualizado el codigo de la tecnologia : "+codtecnologia + " por " +respuesta);



		} );//},'json');
		
	}




	function cerrarVentana()
	{
		top.close();
	}






	function clickpopestadopre(){


		//document.getElementById(event.target.value).innerHTML = event.target.value;

		var id = event.target.value;
		var id2 = id.substr(0,id.length-2);
		var idtmp;
		//alert("id "+id2);

		for ( i=1 ; i<5 ; i++){//estados del mi pres

			idtmp = id2 + "_" + i.toString();
			document.getElementById(idtmp).style.visibility = 'hidden';
		}
		document.getElementById(event.target.value).style.visibility = 'visible';
//		document.getElementById(event.target.value).innerHTML = event.target.value;

	}

	function habilitarFechaMipres(elemento){
		//alert("click");
		//alert(elemento);
		//alert(elemento.value);
		switch (elemento.value) {
			case '2':
			case '3':
			case '4':
			case '7':
			case '8':
		//	alert("Seleccione la fecha ...");

			//document.getElementById("fechamipresprovedores").style.visibility = 'visible';
			//iniciarDatepicker("fechamipresprovedores","Seleccione la fecha mipres");
				break;
			default:
//			alert("otras opciones");
//				document.getElementById("fechamipresprovedores").style.visibility = 'hidden';
//			document.getElementById("fechamipresprovedores").style.visibility = 'visible';


				break;
		}
	}


	function mostrarCodigos ( elemento ){
		jAlert(elemento.value);
	}


	function verdir( cta , vID , numprescripcion , vtipotec , vconsecutivo ) {//muestra el direccionamiento


		nroentrega = valorcampoxid("idnumerodeentrega_"+cta);//3 julio 2019 , usar numero de entrega en la clave principal Freddy Saenz
		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'mostrarDireccionamiento',
			wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
			numprescripcion : numprescripcion,
		    vtipotec        : vtipotec,
		    vconsecutivo    : vconsecutivo,
			vID             : vID,
			nroentrega      : nroentrega,//3 julio 2019
			cta             : cta


		}
		, function(data) {

			document.body.style.cursor = 'auto';
			document.getElementById('btnBuscar').style.cursor = 'auto';
			jAlert(data);


		} );//},'json');



		//	cad2 = cad2.replace(/===/g, "\"");


	}//function verdir( cta , vID , numprescripcion , vtipotec , vconsecutivo )

	
	function verfacturacion( cta ){
		eps =  valorcampoxid("diveps-"+cta);
		alert(eps);
	}
	
//a la fecha dt se agregan n meses
function add_months( dt , n )
 {
		year = dt.getFullYear();//getYear , no sirve , porque le resta al año 1900
		month = dt.getMonth()+1;
		days = dt.getDate();//getDay es el dia de la semana

		months = month + (n % 12);
		vpasames = 0;
		if (months > 12){
			vpasames = 1
			months = months % 12;
		}

		years = year +  Math.trunc( n / 12 ) + vpasames;
		daysStr = days;
		if (days < 10){
			daysStr = "0"+days;
		}
		monthsstr = months;
		if (months < 10){
			monthsstr = "0"+months;
		}
		return years + "-" + monthsstr + "-" + daysStr;
	 //  return new Date(years  , months , days  );

	 //  return new Date(dt.setMonth(dt.getMonth() + n));
 }

	 //drag and drop

	 function allowDrop(ev) {
	  ev.preventDefault();
	}



	function drag(ev) {
	  ev.dataTransfer.setData("text", ev.target.id);
	//  ev.dataTransfer.setData("text", ev.target.value);
	}

	function drop(ev) {

		 ev.preventDefault();

		 campo = ev.dataTransfer.getData("text");
		 campodestino = ev.target.id;



		 pointA = document.getElementById(campo);
		 pointB = document.getElementById(campodestino);




		posorigen = campo.replace("idcantidadprog_", "");
		posdestino = campodestino.replace("idcantidadprog_", "");

		if (posorigen == posdestino ){
			jAlert("Accion no permitida");
			return ;
		}
		posorigenxalerta =  parseInt( posorigen ) + 1 ;
		posdestinoxalerta =  parseInt ( posdestino ) + 1 ;

		//jAlert(posorigen+ " "+posdestino);


		 valIdprogramacion = document.getElementById("IDprogramaciondlgprogramacion_"+posorigen).innerHTML;
		 if ( valIdprogramacion != 0 ){
			 jAlert("Origen : Ya tiene programacion, Fila # " + posorigenxalerta );
			 return ;
		 }

		 valIdentrega = document.getElementById("IDentregadlgprogramacion_"+posorigen).innerHTML;
		 if ( valIdentrega != 0 ){
			 jAlert("Origen : Ya tiene entrega, Fila # "+ posorigenxalerta );
			 return ;
		 }

		 valIdprogramaciondestino = document.getElementById("IDprogramaciondlgprogramacion_"+posdestino).innerHTML;
		 if ( valIdprogramaciondestino != 0 ){
			 jAlert("Destino : Ya tiene programacion, Fila # " + posdestinoxalerta ) ;
			 return ;
		 }

		 valIdentregadestino = document.getElementById("IDentregadlgprogramacion_"+posdestino).innerHTML;
		 if ( valIdentregadestino != 0 ){
			 jAlert("Destino : Ya tiene entrega,  Fila # " + posdestinoxalerta );
			 return;
		 }

		canta = parseInt( pointA.value );
		cantb = parseInt( pointB.value );//ev.target.id.value );//pointB.value );


		varsuma = canta + cantb;
		document.getElementById(campo).value = 0;
		document.getElementById(ev.target.id).value = varsuma ;


	}//function drop(ev)

	//Abre un dialogo donde se muestra o se crea la programacion de  un mipres.
	function dlgDeProgramacion( cta , vID  , numprescripcion , vconsecutivo , vtipotec ,  canttotal , codservxmipres , tipoIDprov , numIDprov , codsedeprov , videntrega , numerodeentrega ){

		if (vID == 0 ){
			jAlert("No tiene ID con el cual asociar la programacion");
			return ;
		}
		
		
		vtecnologiaxprescripcion = valorcampoxid("tecnologiaxprescripcion_"+cta);
		if ( vtecnologiaxprescripcion != codservxmipres ){// si se modifico la tecnologia que se va a programar
		//es diferente de la registrada anteriormente en el consumo .
			codservxmipres = vtecnologiaxprescripcion;
		}
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		
		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'abrirDlgProgramacion',
			wemp_pmla		: $('#wemp_pmla').val(),

			nroPrescripcion : numprescripcion,
		    vtipotec        : vtipotec,
		    vconsecutivo    : vconsecutivo,
			canttotal       : canttotal,
			codsedeprov     : codsedeprov,
			cantidad        : canttotal,
			codtecnologia   : codservxmipres,
			cta             : cta,//27 mayo 2019
			tipoIDprov      : tipoIDprov ,
			numIDprov       : numIDprov,

			numerodeentrega : numerodeentrega

		}
		, function(data) {

			maxfilas = 12;
			abrirModalConInfo(data , maxfilas);//12);


		} );//},'json');


	}//function dlgDeProgramacion(elemento , cta , vID  , numprescripcion , vconsecutivo , vtipotec , canttotal )


	
		//Abre un dialogo donde se muestra o se crea la Facturacion del mipres
	function dlgDePFacturacion( cta , vID  , numprescripcion , vconsecutivo , vtipotec ,  canttotal , codservxmipres , tipoIDprov , numIDprov , codsedeprov , videntrega , numerodeentrega ){

		vIDtmp =  valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);
		
		if (vIDtmp == 0 ){
			jAlert("No tiene ID con el cual asociar la Facturacion");
			return ;
		}
		vID = vIDtmp;
		
		eps             =  valorcampoxid("diveps-"+cta);
		numfactura      =  valorcampoxid("idnumfact_"+cta);
		
		if (numfactura == 0 ){
			jAlert("No hay factura asociada");
			return ;
		}		
		if (vvlrcampoIDentrega == 0 ){
			jAlert("No tiene Reporte de Entrega");
			return ;
		}		

		
		coopago         =  valorcampoxid("idcoopagofact_"+cta);
		//Usar las cantidades y valores del consumo y no de la facturacion , puede crear problemas con las unidades de medida
//		cantidadfact    =  valorcampoxid("idcantidadfact_"+cta);
//		vlrunitario     =  valorcampoxid("idvlrunitariofact_"+cta);
		
		
		vlrunitario     =  valorcampoxid("valortecnologia_"+cta);

		vIDFacturacion  =  valorcampoxid("IDFacturacion-"+cta);
		cuotamoderadora =  valorcampoxid("idcuotamoderadorafact_"+cta);;
		
		canttotal       =  valorcampoxid("divcantidad_"+cta);
		cantidadfact    =  valorcampoxid("divcantidad_"+cta);
		
		vlrtotal        =  valorcampoxid("idvlrentregadoreporte_"+cta) ;
		
		vlrtotaltmp       =  parseInt( valorcampoxid("idvlrentregadoreporte_"+cta) );//  parseInt ( canttotal ) * parseInt ( vlrunitario ) ;//cantidadfact
		if (canttotal != 0 ){
			vlrunitario     =   vlrtotaltmp / canttotal ;
		}else{
			vlrunitario = 0;
		}
		
		//16 octubre 2019 Freddy Saenz , el web service de facturacion usa como separador decimal la coma (,)
		// pero la division por cantidad usa como separador decimal el punto (.)
		//vlrunitario = Math.round ( vlrunitario ) ;//sin decimales
		vlrunitario = Math.round ( vlrunitario *100 ) / 100;
		vlrunitstr =  vlrunitario.toString() ;
		vlrunitstr = vlrunitstr.replace(/\./g, ",");//reemplazar el punto(.) decimal por coma

		niteps          =  valorcampoxid("idniteps_"+cta);
		
		tipoid          = "";
		numid           = "";		



		
		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'abrirDlgFacturacion',
			wemp_pmla		: $('#wemp_pmla').val(),

			nroPrescripcion : numprescripcion,
		    vtipotec        : vtipotec,
		    vconsecutivo    : vconsecutivo,
			canttotal       : canttotal,
			codsedeprov     : codsedeprov,
			cantidad        : canttotal,
			codtecnologia   : codservxmipres,
			cta             : cta,//27 mayo 2019
			tipoIDprov      : tipoIDprov ,
			numIDprov       : numIDprov,
			eps             : eps,
			niteps          : niteps,
			numfactura      : numfactura,
			cuotamoderadora : cuotamoderadora,	
			coopago         : coopago ,	
			tipoid          : tipoid,	
			numid           : numid ,		
			vlrunitario     : vlrunitstr , //vlrunitario ,
			vlrtotal		: vlrtotal ,	
			numerodeentrega : numerodeentrega ,
			vID             : vID,
			vIDFacturacion  : vIDFacturacion

		}
		, function(data) {

			maxfilas = 12;
			abrirModalConInfo(data , maxfilas);//12);


		} );//},'json');


	}//function dlgDePFacturacion(elemento , cta , vID  , numprescripcion , vconsecutivo , vtipotec , canttotal )


	



	function ingresomanual( cta , vID  , numprescripcion , vconsecutivo , vtipotec ,  canttotal , codservxmipres , tipoIDprov , numIDprov , codsedeprov , videntrega , numerodeentrega )
	{

		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);

		vvlrcampofechamipres = valorcampoxid("idfechamipres_"+cta);



		if  ( vvlrcampoIDentrega != 0 ) //videntrega != 0 )
		{
			jAlert (" Ya tiene entrega "+vvlrcampoIDentrega);
			return;

		}

		if  ( vvlrcampoIDreporte != 0 ) //videntrega != 0 )
		{
			jAlert (" Ya tiene resporte de entrega "+vvlrcampoIDreporte);
			return;

		}




			mensaje = "Desea que el consumo sea igual a lo registrado en el Mipres <br>(Ingreso Manual, Codigo+Cantidad+Tarifa+Fecha Aplicacion)?";
			vlrtecnologia  = 0;


			jConfirm( mensaje, 'Confirmation', function( r )
			{
				if ( r )
				{


					jPrompt("Digite el valor unitario $? ", '', 'Prompt Dialog', function(respuesta) {
						if( respuesta )
						{//se ingreso informacion
							vlrtecnologia = respuesta;
							nroentrega = valorcampoxid("idnumerodeentrega_"+cta);//3 julio 2019 , usar numero de entrega en la clave principal Freddy Saenz





							$.post("mipres_suministros.php",
							{
								consultaAjax 	: '',
								accion			: 'ingresoManual',
								wemp_pmla		: $('#wemp_pmla').val(),
								vID             : vID,
								numprescripcion : numprescripcion,
								vconsecutivo    : vconsecutivo,
								vtipotec        : vtipotec,
								canttotal       : canttotal,
								codservxmipres  : codservxmipres,
								vlrtecnologia   : vlrtecnologia,

								cta : cta,
								vIDentrega : 0,
								vIDReporteEntrega : 0,
								vfechamipres   : vvlrcampofechamipres,
								nroentrega     : nroentrega,
								valormed : vlrtecnologia



							}
							, function(data) {
								document.body.style.cursor = 'auto';
								document.getElementById('btnBuscar').style.cursor = 'auto';

								arrData = data.split("|");
								if ( arrData.length < 2){
									jAlert("Error en el ingreso manual ");
									return;
								}


								$("#divenvio-"+cta).html(arrData[0]);
								$("#reporte-"+cta).html(arrData[1]);
								$("#celdaentregado-"+cta).html("");//no debe mostrar informacion

								$('#buscar').quicksearch('#tablePrescripciones .find');

								mostrarOcultarbotones('visible');//27 mayo 2019



/*
								maxfilas = 12;
								abrirModalConInfo(data , maxfilas);//12);*/


							} );//},'json');
							//document.body.style.cursor = 'auto';
						}//if( respuesta )
					});//jPrompt("Digite el nuevo codigo ", codservxmipres, 'Prompt Dialog', function(respuesta)



				}//if ( r ){
			});	//jConfirm jConfirm( mensaje, 'Confirmation', function( r )




	}//function ingresomanual( cta , vID  , numprescripcion , vconsecutivo , vtipotec ,  canttotal , codservxmipres , tipoIDprov , numIDprov , codsedeprov , videntrega , numerodeentrega )




	function actualizarcantidadProgramacion(  posicion ){

		vsig = posicion+1;
		vidprogramacion = document.getElementById("IDprogramaciondlgprogramacion_"+posicion).innerHTML;
		vidprogramacionsig = document.getElementById("IDprogramaciondlgprogramacion_"+vsig).innerHTML;
//	alert(vidprogramacion+ " siguiente "+vidprogramacionsig);


		cantidadactual = document.getElementById("idcantidadprog_"+posicion).value;
		if (cantidadactual == 0){
			jAlert("Cantidad en cero , arrastre la cantidad una fila con valor");
		}


		cantnuevastr = "";
		jPrompt("Digite la nueva cantidad ", cantidadactual, 'Prompt Dialog', function(r) {
			if( r ) {

				cantnuevastr = r;


				jAlert(cantnuevastr);


				nuevastr = prompt("Digite la nueva cantidad ");
				if (nuevastr == null){
					jAlert("sin informacion ");
					return;
				}
				nueva  = parseInt(nuevastr);
				if (nueva == 0){
					jAlert("Para dejar en cero arrastre el valor actual a una nueva fila");
					return ;
				}


				if (nueva > cantidadactual){
					jAlert("Cantidad superior al total: "+nueva+ " > " +cantidadactual);
					return ;
				}


				document.getElementById("idcantidadprog_"+posicion).value = nueva;
				vdif = cantidadactual-nueva ;

				cantidaddestino = 0;
				cantidaddestino = cantidaddestino + parseInt(document.getElementById("idcantidadprog_"+vsig).value);

				vdif = vdif + cantidaddestino;//aumentar la cantidad en el distino.

				document.getElementById("idcantidadprog_"+vsig).value = vdif;

			}

		});

	}//function actualizarcantidadProgramacion(  posicion )


	function verprog( cta , vIDProgramacion  ) {
		jAlert("ver programacion ");
	}


	function verenvio( cta , vID ) {

		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);



		if ( vID == 0 ){
			cad2 = $("#infoalertamipres_"+cta).val();
			cad2 = cad2.replace(/<tr cssimpar>/g, "<tr class='fila1' align='center'>");
			cad2 = cad2.replace(/<tr csspar>/g, "<tr class='fila2' align='center'>");

			jAlert(cad2);

		}else if ( vvlrcampoIDentrega == 0 ) {
			cad2 = $("#infoalertaIDmipres_"+cta).val();
			cad2 = cad2.replace(/<tr cssimpar>/g, "<tr class='fila1' align='center'>");
			cad2 = cad2.replace(/<tr csspar>/g, "<tr class='fila2' align='center'>");

			jAlert(cad2);

		}else{

				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'infojsonenvio',
					wemp_pmla		: $('#wemp_pmla').val(),
					vID             : vID

				}
				, function(data) {
					jAlert(data);

				} );//},'json');

		}







	}//function verenvio( cta , vID)

	function verreporte( cta ) {

		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);

		if ( vvlrcampoIDreporte == 0 ){//no se ha enviado todavia
			cad2 = $("#infoalertareporte_"+cta).val();

			cad2 = cad2.replace(/<tr cssimpar>/g, "<tr class='fila1' align='center'>");
			cad2 = cad2.replace(/<tr csspar>/g, "<tr class='fila2' align='center'>");

			jAlert(cad2);


		}else{//ya se envio

			$.post("mipres_suministros.php",
			{
				consultaAjax 	: '',
				accion			: 'infojsonreporteenvio',
				wemp_pmla		: $('#wemp_pmla').val(),
				vID             : vvlrcampoID

			}
			, function(data) {
				jAlert(data);

			} );//},'json');

		}


	}//function verreporte( cta )

	function verinfoprogramacion ( info ){

		jAlert ( info );
	}

	function verinfo( info ){

		jAlert ( info );
	}
	
	//Nueva funcionalidad para imprimir  reporte 03-10-2020 Mavila :)
	function openPrintDialogue( info , encabezado ){		
		//Se toma envio información a un div con los datos :)
		$("#printDiv").append('<h1>'+ encabezado +'</h1>'+'<br><h2>'+ info +'</h2>');
		//Se obtiene la información del div :)
		var mydiv = document.getElementById("printDiv");
		//Se envian datos del div a un iframe para imprimirlo :)
		window.frames["print_frame"].document.body.innerHTML = $(mydiv).html();
        window.frames["print_frame"].window.focus();
        window.frames["print_frame"].window.print();
	}
	//Funcionalidad anterior comentada por error en firefox 03-10-2020 Mavila :)
	/*
	function openPrintDialogue( info , encabezado )//imprime info 
	{
	  $('<iframe>', {
		name: 'myiframe',
		//title: 'encabezado' ,//probando esta linea
		//about: 'titulo',

		class: 'printFrame'
	  })
	  .appendTo('body')
	  .contents().find('body')
	  .append('<h1>'+ encabezado +'</h1>'+'<br><h2>'+ info +'</h2>'

	  );
	  //popTitle : 'Ordenes',
	  
	  	 // .appendTo('head')
	  //.contents().find('title')
	  //.append('encabezado')
	  
	 // window.frames['myiframe'].href = "mipres.clinica.las.americas";
	 // window.frames['myiframe'].title  = "encabezado";//18 de octubre 2019 Freddy Saenz , ponerle nombre al documento .test.
	  window.frames['myiframe'].focus();
	 // window.frames['myiframe'].style
	  window.frames['myiframe'].print();

	 setTimeout(() => { $(".printFrame").remove(); }, 1000);
	}//function openPrintDialogue( info , encabezado )//imprime info */
	
	function imprimirfacturacion ( cta ){
		
		vIDFacturacion = valorcampoxid("IDFacturacion-"+cta);
		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);
		
		vidnombreprestadormipres = $('#idnombreprestadormipres').val();// valorcampoxid("idnombreprestadormipres");
		
		if ( vIDFacturacion == 0 ){
		
			jAlert ("No se ha enviado la factura " );
			//openPrintDialogue(info);
		}else{//buscarlo en la base de datos.

			$.post("mipres_suministros.php",
			{
				consultaAjax 	: '',
				accion			: 'consultarinfofacturacionyreporte',
				wemp_pmla		: $('#wemp_pmla').val(),
				vID             : vvlrcampoID ,
				vIDreporte      : vvlrcampoIDreporte ,
				vIDFacturacion  : vIDFacturacion
				
			}
			, function(data) {
				//jAlert(data);
				//
				openPrintDialogue( data , vidnombreprestadormipres );
			} );//},'json');
			
		}
	}
	
	
	
	function verinfofacturacion ( cta , info ){//desde la ventana de dialogo.
		//vIDFacturacion = valorcampoxid("IDFacturacion-"+cta);
		
		vIDFacturacion = valorcampoxid("IDFacturacionDlg-"+cta);
		numprescripcion = valorcampoxid("idprescripcionfila_"+cta);//poder actualizar la informacion de los IDs cuando se va a imprimir
		//la informacion de las facturas , debido a que cuando se hace la facturacion no siempre actualiza los IDs y el Json de facturacion
		
		if (vIDFacturacion == 0 ){
		
			jAlert ("Sin Envio \r"+ info );
			//openPrintDialogue(info);
		}else{//buscarlo en la base de datos.

			$.post("mipres_suministros.php",
			{
				consultaAjax 	: '',
				accion			: 'consultarinfofacturacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				numprescripcion : numprescripcion , //8 Noviembre 2019 , Freddy Saenz , para poder actualizar los ids por prescripcion .
				vIDFacturacion  : vIDFacturacion
				
			}
			, function(data) {
				jAlert(data);
				//openPrintDialogue(data);
			} );//},'json');
			
		}
	}//function verinfofacturacion ( cta , info )

    function actualizarfacturas ( cta , histxmedi , ingxhist , codArtInterno , vtipotec , cantidad , numprescripcion ,   vconsecutivo , vdescripcionmipres , numentrega ,elemento ) 
	{
		
		vIDFacturacion = valorcampoxid("IDFacturacion-"+cta);
		if (vIDFacturacion != 0 ){
			jAlert("La factura ya fue enviada , primero anule el ID de envio de facturacion");
			return;
		}
		descripcion = elemento.options[elemento.selectedIndex].text;
		informacion = elemento.options[elemento.selectedIndex]["attributes"]["infofact"]["nodeValue"] ;//contiene la informacion adicional de la factura
		arregloinfo = JSON.parse(informacion);
		
		numfactura         =   arregloinfo["numero"]  ;
		vlrcoopagofactura  =   arregloinfo["coopago"]  ;
		vlrunitariofactura =   arregloinfo["vlrunitario"]  ;
		cantidadfactura    =   arregloinfo["cantidad"]  ;
		niteps             =   arregloinfo["niteps"]  ;
		vlrcuotamoderadorafactura  =   arregloinfo["cuotamoderadora"]  ;
		vlrtotalfactura    =   arregloinfo["vlrtotal"]  ;
		

//infofact = elemento.infofact;


		mensaje = "Desea cambiar la Factura por: ["+elemento.value+"] "+descripcion+" ? ";
		
		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){
		

		
					$.post("mipres_suministros.php",
					{
						consultaAjax 	: '',
						accion			: 'cambiarFacturamipres',
						wemp_pmla		: $('#wemp_pmla').val(),
						histxmedi       : histxmedi,
						ingxhist        : ingxhist,
						codArtInterno   : codArtInterno,
						
						numprescripcion : numprescripcion ,
						vtipotec        : vtipotec,
						vconsecutivo    : vconsecutivo,
						numentrega      : numentrega ,
						
						numfactura                : numfactura ,			
						vlrcoopagofactura         : vlrcoopagofactura, 
						vlrcuotamoderadorafactura : vlrcuotamoderadorafactura ,
						vlrunitariofactura        : vlrunitariofactura ,
						cantidadfactura           : cantidadfactura ,
						niteps                    : niteps ,
						infofact                  : informacion
					}
					, function(data) {
						if (data == "ok"){
							alert("actualizada la factura a:"+numfactura);
							
							$("#idnumfact_"+cta).html(numfactura);
							$("#idcoopagofact_"+cta).html(vlrcoopagofactura);
							$("#idcantidadfact_"+cta).html(cantidadfactura);
							$("#idvlrunitariofact_"+cta).html(vlrunitariofactura);
							$("#idniteps_"+cta).html(niteps);
							$("#idcuotamoderadorafact_"+cta).html(vlrcuotamoderadorafactura);
							$("#idvlrtotalfact_"+cta).html(vlrtotalfactura);
/*							

							document.getElementById('idnumfact_"+cta').style.visibility             = 'visible';
							document.getElementById('idcoopagofact_"+cta').style.visibility         = 'visible';
							document.getElementById('idcantidadfact_"+cta').style.visibility        = 'visible';
							document.getElementById('idvlrunitariofact_"+cta').style.visibility     = 'visible';
							document.getElementById('idcuotamoderadorafact_"+cta').style.visibility = 'visible';
							*/

						}else{
							alert (data);	
						}


					} );//},'json');
				}//if ( r ){
			});	//jConfirm				
		
	//	$("#IDFacturacion-"+cta).html(elemento.value);

	}//function actualizarfacturas ( cta , histxmedi , ingxhist , codArtInterno , vtipotec , cantidad , numprescripcion ,   vconsecutivo , vdescripcionmipres , numentrega ,elemento ) 
			
	

	function infocantidadaplicada( cta , histxmedi , ingxhist , codArtInterno , vtipotec , cantidad , numprescripcion ,   vconsecutivo , vdescripcionmipres , numentrega )
	{


		if ( cantidad == 0  ){
			jAlert(" No tiene aplicacion para:  "+vdescripcionmipres);
			return;
		}

		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'infocantidadaplicada',
			wemp_pmla		: $('#wemp_pmla').val(),
			histxmedi       : histxmedi,
			ingxhist        : ingxhist,
			codArtInterno   : codArtInterno,
			vtipotec         : vtipotec,
			numprescripcion : numprescripcion ,
			vconsecutivo    : vconsecutivo

		}
		, function(data) {
			abrirModalConInfo(data , 1);

		} );//},'json');


	}//function infocantidadaplicada( cta , histxmedi , ingxhist , codArtInterno , vtipotec , cantidad , numprescripcion ,   vconsecutivo)

	function mostrarOcultarbotones ( mostrar ){// visible o hidden
	/*
		document.getElementById('btnUsarSeleccion').style.visibility = $mostrar;
		document.getElementById('btnEnviarListado').style.visibility = $mostrar;
		document.getElementById('btnActualizarIDs').style.visibility = $mostrar;
			document.getElementById('btnActualizarToken').style.visibility = $mostrar;

		*/

		document.getElementById('btnBuscar').style.visibility = mostrar;
		document.getElementById('btnBuscar2').style.visibility = mostrar;
		document.getElementById('btnBuscar3').style.visibility = mostrar;


	}//function mostrarOcultarbotones ( mostrar )

	function enviarReporte( cta  ) {
		// accion hacerReporte
		cad2 = $("#infoenvioreporte_"+cta).val()  ;


		cadAlerta = cad2.replace(/,\"/g, " ,\n\"");//importante el espacio porque existen numeros con decimales

		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);

		numEnvio =  document.getElementById('IDEntrega-'+cta).innerHTML;


		if (numEnvio == 0){
			jAlert("Primero se debe hacer el envio "+numEnvio);
		    return;
		}
		mensaje = "Enviar el reporte: \n"+cadAlerta+"\n?";


		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

				document.body.style.cursor = 'wait';//$("body").css("cursor", "progress");
				document.getElementById('btnBuscar').style.cursor = 'wait';
				//jAlert(" seleccionado " + $("#filtroMipresProveedor").val() );

				document.getElementById('bHacerReporte_'+cta).style.visibility = 'hidden';

				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion		        	: 'hacerReporte',
					wemp_pmla	        	: $('#wemp_pmla').val(),
					fechaInicial        	: $("#fechaInicial").val(),
					fechaFinal	        	: $("#fechaFinal").val(),
					tipDocPac	        	: $("#tipDocPac").val(),
					docPac		        	: $("#docPac").val(),
					tipDocMed		        : $("#tipDocMed").val(),
					docMed			        : $("#docMed").val(),
					codEps			        : $("#txtCodResponsable").val(),
					tipoPrescrip	        : $("#tipoPrescripcion").val(),
					nroPrescripcion      	: $("#nroPrescripcion").val(),
					filtroMipres	        :  "",//30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	        : $("#filtroAmbitoAtencion").val(),
					fitrompproveedor        : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),
					vID                     : vvlrcampoID,//16 julio 2019 ,enviar el ID , si no fue actualizado
					infoenvio               : cad2

				}
				, function(data) {
					document.body.style.cursor = 'auto';
					document.getElementById('btnBuscar').style.cursor = 'auto';

					//$("#infoenvio").html(data);
					arrData = data.split("|");
					verror = "";
					if ( arrData.length > 1){
						verror = arrData[1];
					}
					if (arrData[0] == "Error"){
						jAlert("Error al hacer el reporte de entrega "+verror);
						return;
					}

					$("#ID-"+cta).html(arrData[0]);
					if ( arrData.length > 1){
						$("#IDReporteEntrega-"+cta).html(arrData[1]);
						$("#idanuladoreporteentrega_"+cta).html("");//12 JULIO 2019 ACTUALIZAR EL ESTADO DE ANULACION

					}
					$('#buscar').quicksearch('#tablePrescripciones .find');
		//jAlert("respuesta del envio del reporte "+data);//funciono bien
		//respuesta del envio del reporte 3303911|519945

		//respuesta del envio del reporte 2475534|276827 , actualizo los campos correctamente ,pero no la base de datos.


				} );//},'json');
				//document.body.style.cursor = 'auto';

			}
		});	//jConfirm
	}//function enviarReporte( cta  )

	function verprogramacionendlg( posicion , vID  ,tipoIDsedeprov ,noIDsedeprov , codsedeprov ,  codtecnologia , filalistadoprogramacion  ){
		//	i+", " + vID +" , " +tipoIDprov +" , " + numIDprov +" , " +codsedeprov +" , "+ codservxmipres
		//$vID , $fechamax, $tipoIDsedeprov , $noIDsedeprov , $codsedeprov , $codtecnologia , $cantidadxentregar )
		//varcantidad = "idcantidadprog_"+posicion;
	//	varfecha = "fechaprogramacion_"+posicion;


		cantidadxentregar = $("#idcantidadprog_"+posicion).val()  ;
		if (cantidadxentregar == 0){
			jAlert("No hay cantidad para programar (0) ");
		//	return;
		}

		fechamax = $("#fechaprogramacion_"+posicion).val()  ;

		valIdprogramacion = document.getElementById("IDprogramaciondlgprogramacion_"+posicion).innerHTML;

		
		valIdentrega = valorcampoxid("IDEntrega-"+filalistadoprogramacion);
		//valIdentrega = document.getElementById("IDentregadlgprogramacion_"+posicion).innerHTML;

		//Poder modificar el codigo de la tecnologia que se va a programar .
		vtecnologiaenprogramacion = valorcampoxid("iddivtecnologiaprogramacion");
		if ( vtecnologiaenprogramacion != codtecnologia ){
			codtecnologia  = vtecnologiaenprogramacion;
		}
		if ( codtecnologia == "" ){
			jAlert("No hay codigo de la Tecnologia para realizar la programacion");
			return ;
		}

	//		html += " <input type='text' id='"+varfecha+"' name='"+varfecha+"' readOnly='readOnly' value='"+vmessiguiente+"'> ";
		cadAlerta = "{";
		cadAlerta += "\"ID\"" + " :  "  + vID;
		cadAlerta += ",\n\"FecMaxEnt\"" + " :  \""  + fechamax + "\"";
		cadAlerta += ",\n\"TipoIDSedeProv\"" + " :  \""  + tipoIDsedeprov + "\"";
		cadAlerta += ",\n\"NoIDSedeProv\"" + " :  \""  + noIDsedeprov + "\"";
		cadAlerta += ",\n\"CodSedeProv\"" + " :  \""  + codsedeprov + "\"";
		cadAlerta += ",\n\"CodSerTecAEntregar\"" + " : \""  + codtecnologia + "\"";
		cadAlerta += ",\n\"CantTotAEntregar\"" + " :  \"" + cantidadxentregar + "\"";
		cadAlerta += "}";
		jAlert(cadAlerta);
		
	}

	//utilizar el web service para enviar la programacion
	function enviarprogramacion( posicion , vID  ,tipoIDsedeprov ,noIDsedeprov , codsedeprov ,  codtecnologia , filalistadoprogramacion  ){
		//	i+", " + vID +" , " +tipoIDprov +" , " + numIDprov +" , " +codsedeprov +" , "+ codservxmipres
		//$vID , $fechamax, $tipoIDsedeprov , $noIDsedeprov , $codsedeprov , $codtecnologia , $cantidadxentregar )
		//varcantidad = "idcantidadprog_"+posicion;
	//	varfecha = "fechaprogramacion_"+posicion;


		cantidadxentregar = $("#idcantidadprog_"+posicion).val()  ;
		if (cantidadxentregar == 0){
			jAlert("No hay cantidad para programar (0) ");
		//	return;
		}

		fechamax = $("#fechaprogramacion_"+posicion).val()  ;

		valIdprogramacion = document.getElementById("IDprogramaciondlgprogramacion_"+posicion).innerHTML;
		if (valIdprogramacion != 0){
			jAlert("Ya esta programado , ID de Programacion =" + valIdprogramacion);
			return ;
		}
		valIdentrega = valorcampoxid("IDEntrega-"+filalistadoprogramacion);
	//	valIdentrega = document.getElementById("IDentregadlgprogramacion_"+posicion).innerHTML;
		if (valIdentrega != 0){
			jAlert("Ya esta enviado , ID de Envio =" + valIdentrega);
			return ;
		}
		//Poder modificar el codigo de la tecnologia que se va a programar .
		vtecnologiaenprogramacion = valorcampoxid("iddivtecnologiaprogramacion");
		if ( vtecnologiaenprogramacion != codtecnologia ){
			codtecnologia  = vtecnologiaenprogramacion;
		}
		if ( codtecnologia == "" ){
			jAlert("No hay codigo de la Tecnologia para realizar la programacion");
			return ;
		}

	//		html += " <input type='text' id='"+varfecha+"' name='"+varfecha+"' readOnly='readOnly' value='"+vmessiguiente+"'> ";
		cadAlerta = "{";
		cadAlerta += "\"ID\"" + " :  "  + vID;
		cadAlerta += ",\n\"FecMaxEnt\"" + " :  \""  + fechamax + "\"";
		cadAlerta += ",\n\"TipoIDSedeProv\"" + " :  \""  + tipoIDsedeprov + "\"";
		cadAlerta += ",\n\"NoIDSedeProv\"" + " :  \""  + noIDsedeprov + "\"";
		cadAlerta += ",\n\"CodSedeProv\"" + " :  \""  + codsedeprov + "\"";
		cadAlerta += ",\n\"CodSerTecAEntregar\"" + " : \""  + codtecnologia + "\"";
		cadAlerta += ",\n\"CantTotAEntregar\"" + " :  \"" + cantidadxentregar + "\"";
		cadAlerta += "}";


		mensaje = "Desea Hacer la programacion :\n "+cadAlerta+"\n?";


		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){






				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'hacerProgramacion',
					wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
					vID 				: vID,
					fechamax 			: fechamax,
					tipoIDsedeprov 		: tipoIDsedeprov,
					noIDsedeprov 		: noIDsedeprov,
					codsedeprov 		: codsedeprov,
					codtecnologia	 	: codtecnologia,
					cantidadxentregar 	: cantidadxentregar

				}
				, function(data) {
					document.body.style.cursor = 'auto';
					document.getElementById('btnBuscar').style.cursor = 'auto';
					document.body.style.cursor = 'auto';


					arrData = data.split("|");
					
					if (arrData[0] == "Error"){
						jAlert("No se pudo realizar la programacion ");
						if ( arrData.length > 1 ){
							jAlert("Error = "+arrData[1]);
						}
						return ;
					}
					if ( arrData.length > 1 ){//4 julio agregada la condicion.
					

		
						$("#IDProgramacion-"+filalistadoprogramacion).html(arrData[1]);//ID de la prescripcion
						$("#IDprogramaciondlgprogramacion_"+posicion).html(arrData[1]);//ID de la prescripcion
						$("#idanuladoprog_"+filalistadoprogramacion).html("");//12 JULIO 2019 ACTUALIZAR EL ESTADO DE ANULACION
					}



				} );//},'json');
			}
		});	//jConfirm



	}//function enviarprogramacion( posicion )


	
	function enviarfacturacion( cta  ) {
		// accion hacerReporte
			//cad2 = $("#infofacturacionmipres_"+cta).val()  ;
		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);

		
		numEnvio =  document.getElementById('idnumerodeentrega_'+cta).innerHTML;


		if (vvlrcampoID == 0){
			jAlert("No tiene ID principal "+vvlrcampoID);
		    return;
		}
		
		vIDFacturacion = valorcampoxid("IDFacturacion-"+cta);
		if (vIDFacturacion != 0 ){
			jAlert("Ya fue enviada la facturacion ");
			return;
		}
		vcodtecnoligaxfact  = valorcampoxid("idcodigotecnologia_"+cta)  ;
		if  ( vcodtecnoligaxfact == "" )
		{
			jAlert("No hay codigo de Facturacion ");
			return;
		}
		vtotal    = parseInt ( valorcampoxid("idvlrentregadoreporte_"+cta) );
		vcantidad = valorcampoxid("divcantidad_"+cta);
		if (vcantidad != 0 ){
			vlrunitario = vtotal / vcantidad;//2
		}else{
			vlrunitario = 0;
		}
		
		//
		vlrunitario = Math.round ( vlrunitario *100 ) / 100;//2 decimales
		//vlrunitario = Math.round ( vlrunitario ) ;//sin decimales.
		vlrunitstr = vlrunitario.toString() ;
		vlrunitstr = vlrunitstr.replace(/\./g, ",");//reemplazar el punto(.) decimal por coma
		
		var cadinfo =  '{ "NoPrescripcion" :  "'  + valorcampoxid("idprescripcionfila_"+cta)+'" ';

		cadinfo =  cadinfo +  ' ,  "TipoTec" :  "'               +  valorcampoxid("idtipotecnologia_"+cta)+'" ';

		cadinfo =  cadinfo +  ' ,  "ConTec" :  '                +  valorcampoxid("idcampoconsecutivo_"+cta);//numerico
		cadinfo =  cadinfo +  ' ,  "TipoIDPaciente" :  "'       +  valorcampoxid("idtipoidpaciente_"+cta) +'" ';

		cadinfo =  cadinfo +  ' ,  "NoIDPaciente" :  "'        +  valorcampoxid("idnumeroidpaciente_"+cta) +'" ';
		cadinfo =  cadinfo +  ' ,  "NoEntrega" :  '            +  valorcampoxid("idnumerodeentrega_"+cta);//numerico

		cadinfo =  cadinfo +  ' ,  "NoFactura" : "'            +  valorcampoxid("idnumfact_"+cta) +'" ';
		cadinfo =  cadinfo +  ' ,  "NoIDEPS" :  "'            +  valorcampoxid("idniteps_"+cta) +'" ';

		cadinfo =  cadinfo +  ' ,  "CodEPS" :  "'              +  valorcampoxid("diveps-"+cta) +'" '; 
		cadinfo =  cadinfo +  ' ,  "CodSerTecAEntregado" :  "' +  valorcampoxid("idcodigotecnologia_"+cta) +'" ';
//Usar mejor las cantidades y valores de los consumos y no los de facturacion debido a que se pueden producir errores con el manejo de la unidad de medida.
		cadinfo =  cadinfo +  ' ,  "CantUnMinDis" :  "'         +  valorcampoxid("divcantidad_"+cta) +'" ';
		cadinfo =  cadinfo +  ' ,  "ValorUnitFacturado" :  "'         +  vlrunitstr +'" ';//valorcampoxid("valortecnologia_"+cta)//3
		//cadinfo =  cadinfo +  ' ,  "ValorUnitFacturado" :  '   +  valorcampoxid("valortecnologia_"+cta);  //aqui esta el error

		cadinfo +=  ' ,  "ValorTotFacturado" :  "'  + valorcampoxid("idvlrentregadoreporte_"+cta) +'" ' ;// vtotal ;
		cadinfo =  cadinfo +  ' ,  "CuotaModer" :  "'  +  valorcampoxid("idcuotamoderadorafact_"+cta) +'" ';
		cadinfo =  cadinfo +  ' ,  "Copago" : "' +  valorcampoxid("idcoopagofact_"+cta) +'" ';
		cadinfo =  cadinfo +  ' } ' ;	/*	*/
		cadAlerta = cadinfo.replace(/,\"/g, " ,\n\"");//importante el espacio , porque hay numeros con decimales 


//		cadAlerta = cad2.replace(/,/g, ",\n");
		//jAlert ( "no implementado aun "+cadAlerta );



		mensaje = "Enviar la facturacion: \n"+cadAlerta+"\n?";


		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

				document.body.style.cursor = 'wait';//$("body").css("cursor", "progress");
				document.getElementById('btnBuscar').style.cursor = 'wait';
				//jAlert(" seleccionado " + $("#filtroMipresProveedor").val() );
//"".$cta;
				document.getElementById('bEnviarFacturacion_'+cta).style.visibility = 'hidden';
				document.getElementById('bverFacturacion_'+cta).style.visibility = 'hidden';
				
				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion		        	: 'hacerFacturacion',
					wemp_pmla	        	: $('#wemp_pmla').val(),
					fechaInicial        	: $("#fechaInicial").val(),
					fechaFinal	        	: $("#fechaFinal").val(),
					tipDocPac	        	: $("#tipDocPac").val(),
					docPac		        	: $("#docPac").val(),
					tipDocMed		        : $("#tipDocMed").val(),
					docMed			        : $("#docMed").val(),
					codEps			        : $("#txtCodResponsable").val(),
					tipoPrescrip	        : $("#tipoPrescripcion").val(),
					nroPrescripcion      	: $("#nroPrescripcion").val(),
					filtroMipres	        :  "",//30 agosto 2019$("#filtroMipres").val(),
					ambitoAtencion	        : $("#filtroAmbitoAtencion").val(),
					fitrompproveedor        : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),
					vID                     : vvlrcampoID,//16 julio 2019 ,enviar el ID , si no fue actualizado
					infoenvio               : cadinfo //cad2

				}
				, function(data) {
					document.body.style.cursor = 'auto';
					document.getElementById('btnBuscar').style.cursor = 'auto';

					//$("#infoenvio").html(data);
					arrData = data.split("|");
					verror = "";
					if ( arrData.length > 1){
						verror = arrData[1];
					}
					if (arrData[0] == "Error"){
						jAlert("Error al hacer la facturacion "+verror);
						return;
					}

				//	$("#ID-"+cta).html(arrData[0]);
					if ( arrData.length > 1){
						$("#IDFacturacion-"+cta).html(arrData[1]);
						$("#IDFacturacionDlg-"+cta).html(arrData[1]);
						$("#idanuladofacturacion_"+cta).html("");//12 JULIO 2019 ACTUALIZAR EL ESTADO DE ANULACION

					}
					document.getElementById('bEnviarFacturacion_'+cta).style.visibility = 'visible';
					document.getElementById('bverFacturacion_'+cta).style.visibility = 'visible';
					$('#buscar').quicksearch('#tablePrescripciones .find');
		//jAlert("respuesta del envio del reporte "+data);//funciono bien
		//respuesta del envio del reporte 3303911|519945

		//respuesta del envio del reporte 2475534|276827 , actualizo los campos correctamente ,pero no la base de datos.


				} );//},'json');
				//document.body.style.cursor = 'auto';

			}
		});	//jConfirm
	}//function enviarFacturacion( cta  )

	
	function copiarinformacionfact( cta  ) {
		// accion hacerReporte
		cad2 = $("#infofacturacionmipres_"+cta).val()  ;
		cadAlerta = cad2.replace(/,\"/g, " ,\r\n\"");//agregado \r 8 noviembre 2019 1 importante el espacio porque hay numeros con decimales 
		
		if ( cadAlerta != "" ){//type='hidden'
			var dummy = $('<input id="vidtmpapuntador">').val(cadAlerta).appendTo('body').select();
			//var dummy = $('<input type=\'hidden\'>').val(vtxtcopy).appendTo('body').select();
			//vtxtcopy.select();

		/* Copy the text inside the text field */
			document.execCommand("copy");
			setTimeout(() => { $("#vidtmpapuntador").remove(); }, 1000);
			alert(cadAlerta);
		}
		
	}
	
	
	function enviarEnvio(  cta , vID , vtipotec , numprescripcion , vconsecutivo ) {
		//accion hacerEnvio
		cad2 = $("#infoenviomipres_"+cta).val()  ;

		cadID = $("#infoenvioIDmipres_"+cta).val()  ;
		vvlrcampoID = valorcampoxid("ID-"+cta);
		
		if ( parseInt ( vvlrcampoID ) != 0 ) {//  vID != 0 ){
			cadAlerta = cadID.replace(/,\"/g, " ,\n\"");//importante el espacio porque existen numeros con decimales poder ver el texto linea por linea y no en un solo renglon
		}else{
			cadAlerta =  cad2.replace(/,\"/g, " ,\n\"");
		}

		mensaje = "Desea Hacer el Envio:\n "+cadAlerta+"\n?";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){



				document.body.style.cursor = 'wait';//$("body").css("cursor", "progress");
				document.getElementById('btnBuscar').style.cursor = 'wait';

				document.getElementById('bEnvioMipres_'+cta).style.visibility = 'hidden';//no permitir que se envie de nuevo
				//jAlert(" seleccionado " + $("#filtroMipresProveedor").val() );


				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'hacerEnvio',
					wemp_pmla		: $('#wemp_pmla').val(),
					fechaInicial	: $("#fechaInicial").val(),
					fechaFinal		: $("#fechaFinal").val(),
					tipDocPac		: $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					filtroMipres	:  "",//30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),

					vtipotec : vtipotec,
					numprescripcion : numprescripcion,
					vconsecutivo :vconsecutivo,

					vID : vvlrcampoID ,//vID,
					infoenvio : cad2,
					vtxtIDarrenvio : cadID


				}
				, function(data) {
					document.body.style.cursor = 'auto';
					document.getElementById('btnBuscar').style.cursor = 'auto';
					document.body.style.cursor = 'auto';

					arrData = data.split("|");
					if (arrData[0] == "Error"){
						jAlert("No se pudo realizar el Envio del mipres ");
						if ( arrData.length > 1 ){
							jAlert("Error = "+arrData[1]);
						}
						return ;
					}
					$("#ID-"+cta).html(arrData[0]);//ID de la prescripcion
					if ( arrData.length > 1 ){
						$("#IDEntrega-"+cta).html(arrData[1]);// ID de la entrega , segunda posicion
					}
					$("#idanuladoentrega_"+cta).html("");//12 JULIO 2019 ACTUALIZAR EL ESTADO DE ANULACION
					//$("#infoenvio").html(arrData[0]);

					//$("#infoenvio").html(data);
		//			jAlert("respuesta del envio "+data);
					$('#buscar').quicksearch('#tablePrescripciones .find');



				} );//},'json');

			}

		});	//jConfirm

	}//function enviarEnvio(  cta , vID , vtipotec , numprescripcion , vconsecutivo )


	function anulardlgprogramacion ( vidprogramacion  , videntrega  , posicion )	{

		vvlrcampoID = valorcampoxid("ID-"+posicion);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+posicion);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+posicion);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+posicion);

		if  (vvlrcampoIDprogramacion == 0 ) // vidprogramacion == 0 )
		{
			jAlert (" No esta registrada la programacion ");
			return;

		}
		if  ( vvlrcampoIDentrega != 0 ) //videntrega != 0 )
		{
			jAlert (" Ya fue entregada esta programacion , ID-entrega = "+videntrega);
			return;

		}


		mensaje = "Desea Anular la programacion con ID  : "+vvlrcampoIDprogramacion+"?";//vidprogramacion
		//pendiente cambiar
		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

			}

		});	//jConfirm




	}//function anulardlgprogramacion ( vidprogramacion  , videntrega  , posicion )

//Actualiza en la  base de datos.
	function grabarprogramacion( nroPrescripcion , vtipotec , vconsecutivo , canttotal , codsede , vID , codtecnologia){


		cadenvio = "";
		separador = "+";
		i = 0;

		//for (i= 0 ; i<0; i++){

		campocantidad  = "idcantidadprog_"+i;
		campofechamax = "fechaprogramacion_"+i;
		campoestadoprog = "estadoprog_"+i;
		cantidad = document.getElementById(campocantidad).value ;
		fechamax = document.getElementById(campofechamax).value ;
		estadoprog  = document.getElementById(campoestadoprog).value ;
		nroentrega = valorcampoxid("idnumerodeentrega_"+cta);//3 julio 2019 , usar numero de entrega en la clave principal Freddy Saenz




		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'grabarProgramacion',

			wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
			vID             : vID,
			numprescripcion : nroPrescripcion,
			vconsecutivo    : vconsecutivo,
			vtipotec        : vtipotec,
			codtecnologia   : codtecnologia,
			estadoprog      : estadoprog,
			fechamax        : fechamax,
			nroentrega      : nroentrega,
			cadenvio        : cadenvio



		}
		, function(data) {

			jAlert("Grabada la programacion para "+data);
			//se deben actualizar los ids .


			$('#buscar').quicksearch('#tablePrescripciones .find');

			mostrarOcultarbotones('visible');

		} );//},'json');





	}//function grabarprogramacion( nroPrescripcion , vtipotec , vconsecutivo , canttotal , codsede , vID , codtecnologia)

	function anularprogramacion( cta , vID , vIDEntrega , vIDReporteEntrega , numprescripcion , vconsecutivo , vtipotec  , vIDProgramacion )
	{

		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);


		if (vvlrcampoIDreporte != 0){ //vIDReporteEntrega
			jAlert (" Primero debe Anular el reporte de entrega");
			return;

		}
		if ( vvlrcampoIDentrega != 0){//vIDEntrega
			jAlert (" Primero debe Anular la  entrega");
			return;
		}

		if ( vvlrcampoIDprogramacion  == 0){ //vIDProgramacion
			jAlert("No tiene Programacion ");
			return;
		}

		mensaje = "Desea Anular la programacion con ID  : "+vvlrcampoIDprogramacion+"\nPrescripcion:"+numprescripcion+"_"+vconsecutivo+"_"+vtipotec+"?";//vIDProgramacion

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'anularProgramacion',

					wemp_pmla		: $('#wemp_pmla').val(),
					fechaInicial	: $("#fechaInicial").val(),
					fechaFinal		: $("#fechaFinal").val(),
					tipDocPac		: $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					filtroMipres	:  "",//30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),


					cta               : cta,
					vID               : vvlrcampoID,//vID,
					vIDEntrega        : vvlrcampoIDentrega,//vIDEntrega,
					vIDReporteEntrega : vvlrcampoIDreporte,//vIDReporteEntrega,

					numprescripcion   : numprescripcion,
					vconsecutivo      : vconsecutivo,
					vtipotec          : vtipotec,
					vIDProgramacion   : vvlrcampoIDprogramacion //vIDProgramacion




				}
				, function(data) {

					if ( data == "0"){
							$("#IDProgramacion-"+cta).html(data);
							$("#idanuladoprog_"+cta).html("ANULADA");//12 JULIO 2019 MOSTRAR TEXTO ANULACION
							jAlert("Anulada la programacion "+vIDProgramacion);
					}else{
						alert("No se pudo anular "+data);
					}


					$('#buscar').quicksearch('#tablePrescripciones .find');

					mostrarOcultarbotones('visible');//27 mayo 2019



				} );//},'json');

			}
		});	//jConfirm

	}//function anularprogramacion( cta , vID , vIDEntrega , vIDReporteEntrega , numprescripcion , vconsecutivo , vtipotec  , vIDProgramacion )

	function anularentrega( cta , vID , vIDEntrega , vIDReporteEntrega , numprescripcion , vconsecutivo , vtipotec )
	{

		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);

		if ( vvlrcampoIDreporte != 0){//vIDReporteEntrega
			jAlert (" Primero debe Anular el reporte de entrega");
			return;

		}
		if ( vvlrcampoIDentrega == 0){//vIDEntrega
			jAlert("No tiene Entrega ");
			return;
		}

		mensaje = "Desea Anular la entrega con ID de entrega : "+vvlrcampoIDentrega+"\nPrescripcion:"+numprescripcion+"_"+vconsecutivo+"_"+vtipotec+"?";//vIDEntrega

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){





				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'anularEntrega',

					wemp_pmla		: $('#wemp_pmla').val(),
					fechaInicial	: $("#fechaInicial").val(),
					fechaFinal		: $("#fechaFinal").val(),
					tipDocPac		: $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					filtroMipres	:  "",//30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),


					cta : cta,
					vID : vvlrcampoID,//vID,
					vIDEntrega : vvlrcampoIDentrega,//vIDEntrega,
					vIDReporteEntrega : vvlrcampoIDreporte,//vIDReporteEntrega,

					numprescripcion : numprescripcion,
					vconsecutivo : vconsecutivo,
					vtipotec : vtipotec



				}
				, function(data) {

					if ( data == "0"){
						$("#IDEntrega-"+cta).html(data);
						$("#idanuladoentrega_"+cta).html("ANULADA");//12 JULIO 2019 MOSTRAR TEXTO ANULACION
						jAlert("Anulado el envio "+vIDEntrega);
					}else if ( data == "AMBITO"){//17 de julio , Freddy Saenz  para anular los que fueron por ambito de entrega
						$("#IDEntrega-"+cta).html("0");
						$("#idanuladoentrega_"+cta).html("ANULADA");//12 JULIO 2019 MOSTRAR TEXTO ANULACION
						$("#ID-"+cta).html("0");//aqui esta lo diferente , se puede dejar en cero el ID
						jAlert("Anulado el envio "+vIDEntrega);
					}else {
						jAlert("No se pudo anular "+data);
					}


					$('#buscar').quicksearch('#tablePrescripciones .find');

					mostrarOcultarbotones('visible');//27 mayo 2019

				} );//},'json');

			}
		});	//jConfirm



	}//function anularentrega( cta , vID , vIDEntrega , vIDReporteEntrega , numprescripcion , vconsecutivo , vtipotec )

	function anularreporte( cta , vID , vIDEntrega , vIDReporteEntrega , numprescripcion , vconsecutivo , vtipotec )
	{

		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);


		if (vvlrcampoIDreporte == 0){
			jAlert("No tiene Reporte de Entrega ");
			return;
		}

		mensaje = "Desea Anular en Reporte de Entrega : "+vvlrcampoIDreporte+"\nPrescripcion:"+numprescripcion+"_"+vconsecutivo+"_"+vtipotec+"?";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){





				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'anularReporte',

					wemp_pmla		: $('#wemp_pmla').val(),
					fechaInicial	: $("#fechaInicial").val(),
					fechaFinal		: $("#fechaFinal").val(),
					tipDocPac		: $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					filtroMipres	:  "",//30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),


					cta : cta,

					vID : vvlrcampoID,//vID,
					vIDEntrega : vvlrcampoIDentrega,//vIDEntrega,
					vIDReporteEntrega : vvlrcampoIDreporte,//vIDReporteEntrega,


					numprescripcion : numprescripcion,
					vconsecutivo : vconsecutivo,
					vtipotec : vtipotec



				}
				, function(data) {



					if (data == "0"){
						$("#IDReporteEntrega-"+cta).html(data);
						$("#idanuladoreporteentrega_"+cta).html("ANULADA");//12 JULIO 2019 MOSTRAR TEXTO ANULACION
						jAlert("Anulado el Reporte de Entrega "+vIDReporteEntrega);
					}else{
						jAlert("No se pudo anular el reporte de entrega :"+data);
					}


					$('#buscar').quicksearch('#tablePrescripciones .find');
					mostrarOcultarbotones('visible');//27 mayo 2019

				} );//},'json');

			}
		});	//jConfirm



	}//function anularreporte( cta , vID , vIDEntrega , vIDReporteEntrega , numprescripcion , vconsecutivo , vtipotec )

	
	function anularfacturacion( cta , numprescripcion , vconsecutivo , vtipotec ){
		vvlrcampoIDFacturacion = valorcampoxid("IDFacturacion-"+cta);
		if (vvlrcampoIDFacturacion == 0){
			jAlert("No tiene ID de Facturacion ");
			return;
		}
		
//		alert("no implementado "+vvlrcampoIDFacturacion);
//return;

		vvlrcampoID = valorcampoxid("ID-"+cta);
		vvlrcampoIDentrega = valorcampoxid("IDEntrega-"+cta);
		vvlrcampoIDprogramacion = valorcampoxid("IDProgramacion-"+cta);
		vvlrcampoIDreporte = valorcampoxid("IDReporteEntrega-"+cta);
	



		mensaje = "Desea Anular la Facturacion : "+vvlrcampoIDFacturacion+"\nPrescripcion:"+numprescripcion+"_"+vconsecutivo+"_"+vtipotec+"?";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){





				$.post("mipres_suministros.php",
				{
					consultaAjax 	: '',
					accion			: 'anularFacturacion',

					wemp_pmla		: $('#wemp_pmla').val(),
					
					fechaInicial	: $("#fechaInicial").val(),
					fechaFinal		: $("#fechaFinal").val(),
					tipDocPac		: $("#tipDocPac").val(),
					docPac			: $("#docPac").val(),
					tipDocMed		: $("#tipDocMed").val(),
					docMed			: $("#docMed").val(),
					codEps			: $("#txtCodResponsable").val(),
					tipoPrescrip	: $("#tipoPrescripcion").val(),
					nroPrescripcion	: $("#nroPrescripcion").val(),
					filtroMipres	:  "",//30 agosto 2019 $("#filtroMipres").val(),
					ambitoAtencion	: $("#filtroAmbitoAtencion").val(),
					fitrompproveedor : $("#filtroMipresProveedor").val(),
					fechamipresprovedores	: $("#fechamipresprovedores").val(),


					cta : cta,

					vID : vvlrcampoID,//vID,
					vIDEntrega : vvlrcampoIDentrega,//vIDEntrega,
					vIDReporteEntrega : vvlrcampoIDreporte,//vIDReporteEntrega,
					
					vIDFacturacion  : vvlrcampoIDFacturacion,

					numprescripcion : numprescripcion,
					vconsecutivo : vconsecutivo,
					vtipotec : vtipotec



				}
				, function(data) {



					if (data == "0"){
						$("#IDFacturacion-"+cta).html(data);
						$("#idanuladofacturacion_"+cta).html("ANULADA");//12 JULIO 2019 MOSTRAR TEXTO ANULACION
						jAlert("Anulado la Facturacion "+vvlrcampoIDFacturacion);
					}else{
						jAlert("No se pudo anular la Facturacion :"+data);
					}


					$('#buscar').quicksearch('#tablePrescripciones .find');
					mostrarOcultarbotones('visible');//27 mayo 2019

				} );//},'json');

			}
		});	//jConfirm
		
		
		
		
	}

	//Permite marcar o desmarcar ( check box ) todas las filas de las prescripciones
	function marcarodesmarcartodos(element){

		vmarcarstr = "Desmarcar";
		if (element.checked){
			vmarcarstr = "Marcar";
		}


		mensaje = "Desea "+vmarcarstr+" Todos "+"?";

		jConfirm( mensaje, 'Confirmation', function(r) {
			if ( r ){

				$('input[name="seleccionprescripciones"]').each(function() {
					this.checked = element.checked;


				});

			}
		});	//jConfirm


	}//function marcarodesmarcartodos(element)



	function mostrarFecha( data ){
		//$("#mostrarprogreso").html(data);
		document.getElementById("mostrarprogreso").innerHTML = data;

	}

	function procesoactualizarIDs ( fechainicial , fechafinal )
	{


		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'procesoactualizarIDs',
			wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
			fechaInicial    : fechainicial,
		    fechaFinal      : fechafinal

		}
		, function(data) {

			document.getElementById("mostrarprogreso").innerHTML = data;

		} );//},'json');


	}//function procesoactualizarIDs ( fechainicial ,fechafinal )


	function llamarReportediariomipres (  ){

		separador = "";

		vfechareporte  = $('#fechadialogo2020').val();
		vfechahastareporte  = $('#fechahastadialogo2020').val();

		//valores posibles Archivo , Impresora , Ventana ,
		vsalidareporte = $('#opcionesreporteid').val();
		
		vidnombreprestadormipres = $('#idnombreprestadormipres').val();
		$.unblockUI();//cerrar el dialogo de fechas.

		fechareporte = vfechareporte;// r;
//por aqui voy
		
		//modalMipres
		//$.unblockUI();//cerrar el dialogo de opciones de impresion
	
	//	$.unblockUI();
		separador = ";";
		if ( vsalidareporte == "Archivo"){
			separador = ";";//prompt("Digite el separador" , ";" )
			/*jPrompt("Digite el separador" , separador ,  'Dialogo Separador' , function(sep) {
				separador = sep;
			});*/
		}
				

		$.post("mipres_suministros.php",
		{
			consultaAjax 	: '',
			accion			: 'generarreportemipres',
			wemp_pmla		: $('#wemp_pmla').val(),//21 junio 2019
			fechaInicial    : fechareporte,
			fechaFinal      : vfechahastareporte,
			fechareporte    : fechareporte,
			separador       : separador ,
			vsalidareporte  : vsalidareporte //Impresora , Archivo , Ventana

		}
		, function(data) {
			if (data == ""){//no se encontro informacion
				jAlert("No se encontro informacion");
				return ;
			}

			data = data.replace(/<tr cssimpar>/g, "<tr class='fila1' align='center'>");
			data = data.replace(/<tr csspar>/g, "<tr class='fila2' align='center'>");


			if ( vsalidareporte == "Impresora"  ){//imprimir el resporte
			
			
				openPrintDialogue( data , vidnombreprestadormipres );

			
			}else if( vsalidareporte == "Ventana"  ) {//mostrarlo en la ventana
				maxfilas = 12;
				abrirModalConInfo( data , maxfilas);//12);

			
			}else{
				vnomarchivo = "mipres"+fechareporte+"_"+vfechahastareporte+".csv";
				jPrompt("Digite el nombre del archivo" , vnomarchivo ,  'Dialogo Exportar', function(r) {
					nombrearchivo = r;
					downloadreporte(nombrearchivo , data );

					//alert("Generado el archivo "+nombrearchivo);//downloadreport genera una alerta.
				});

				
			}
		});//},'json');





/*
		jPrompt("Digite la fecha aaaa-mm-dd",vFechahoy , 'Prompt Dialog', function(r) {	
		
			if( r ) {

							
			}//if (r)
		} );
*/


	}//function llamarReportediariomipres ( )
//16 abril 2020 , funcion para capturar la informacion de la fecha .

	function freportediariomipres()
	{
		var testdate = Date();
		vFechahoy = $.datepicker.formatDate( "yy-mm-dd" , new Date(testdate));
		abrirDlgFecha2020( vFechahoy , 'llamarReportediariomipres' );
		//jAlert(vfechaseleccionada);

		//return ;
		

	}

//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>





<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
	.detalles{
		font-family: verdana;
		font-size: 7pt;
		color: #0033FF;
		font-weight: bold;
		text-decoration: underline;
		cursor:pointer;
	}
	.verPrescripcion{
		font-family: verdana;
		font-weight: bold;
		color:#0033ff;
		cursor:pointer;
	}
	.prescripcioncompleta{
		font-family: verdana;
		font-size: 10pt;

	}



	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	// aqui empieza la aplicacion
		$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
		$wbasedato1 = strtolower( $institucion->baseDeDatos );

		if ($wemp_pmla == 01 || $wemp_pmla == 10 )
		{
			$logo = $wbasedato1;
		}
		else
		{
			$logo = "logo_".$wbasedato1;
		}
		// -->	ENCABEZADO
		encabezado("Mipres de Suministros y Facturacion", $wactualiz, $logo);


		$wfechahoy=date("Y-m-d");




		$wfechaEn12meses = date("Y-m-d",strtotime($wfechahoy."+12 months"));

		echo "	<input type='hidden' id='wbasedato' value='".$wbasedato."'>
					<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>
			<input type='hidden' id='fechamaxprogramacion' value='".$wfechaEn12meses."'>";

		echo "<div id='mostrarprogreso' ></div>";//4 de junio 2019

	//mostrar un mensaje en la ventana

		echo "	<div id='msjEspere' style='display:inline;' align='center'>
					<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...
				</div>";


				//Buscar la fecha de la ultima actualizacion de los IDs.
		$tablaselect = $wbasedatoroot."_000051";
		$where  = "Detapl = 'fechaactualizacionIDsSuministroMipres' ";
		$where  .= " AND Detemp = '".$wemp_pmla."' ";
		$arrfilas = select1filaquery("Detval", $tablaselect, $where);
		$wfechaactualizacionIDs = "";

		if (count($arrfilas)> 0) {
			$wfechaactualizacionIDs = $arrfilas["Detval"];
		}else{//no hay registro creado

		
			$fechahace1semana = date("Y-m-d",strtotime($wfechahoy."- 15 day"));//15 dias antes , para que cuando se instale la primera busque los IDs de los ultmos 15 dias.
	
			$insertroot51 = "INSERT INTO ".$wbasedatoroot."_000051 ";
			$insertroot51 .= "(`Medico`, `Fecha_data`, `Hora_data`, `Detemp`, `Detapl`, `Detval`, `Detdes`, `Seguridad`,`id`) ";
			$insertroot51 .= " VALUES ";
		//	$insertroot51 .= "('root', '".date("Y-m-d")."','".date("H:i:s")."', '".$wemp_pmla."', 'fechaactualizacionIDsSuministroMipres', '2019-03-01', 'Fecha de la ultima actualizacion de los IDs de los mipres de suministro', 'C-root' , '')";
			$insertroot51 .= "('root', '".date("Y-m-d")."','".date("H:i:s")."', '".$wemp_pmla."', 'fechaactualizacionIDsSuministroMipres', '".$fechahace1semana."', 'Fecha de la ultima actualizacion de los IDs de los mipres de suministro', 'C-root' , '')";
			$resquery = mysql_query($insertroot51, $conex) or die("Error: ".mysql_errno()." - en el query: ".$insertroot51." - ".mysql_error());

		}






		$wfechaactualizacionIDs = consultarAliasPorAplicacion($conex,$wemp_pmla , 'fechaactualizacionIDsSuministroMipres');
		
		
		$vusarunixxmipresfacturacion  = consultarAliasPorAplicacion( $conex , $wemp_pmla , 'usarunixxmipresfacturacion' );//16 septiembre 2019 Freddy Saenz , 
		// 
		//existen 2 maneras  de consultar la numeracion de la facturacion : 1 usando el unix 2 usando matrix ( las 2 formas mutuamente excluyentes)
		// si $vusarunixxmipresfacturacion = 0 se usa matrix , $vusarunixxmipresfacturacion = 1 se usa en unix , 
		
		$nombreprestador = consultarAliasPorAplicacion($conex,$wemp_pmla , 'nombrePrestadorMipres');
		
		echo "	<input type='hidden' id='idnombreprestadormipres' value='".$nombreprestador."'>" ;
		echo "	<input type='hidden' id='idquerybusquedamipres' value='".""."'>" ;
		
		echo pintarFiltros();



		$wfechahoy=date("Y-m-d");
		$wfechaayer = date("Y-m-d",strtotime($wfechahoy."- 1 day"));//un dia menos

		$tablaselect = $wbasedatoroot."_000051";
		$where  = "Detapl = 'fechaactualizacionIDsSuministroMipres' ";
		$where  .= " AND Detemp = '".$wemp_pmla."' ";

		$fecha1 = "" ;
		$arrFila =   select1filaquery("Detval", $tablaselect, $where);

		if ( count($arrFila) > 0){
			$fecha1 = $arrFila['Detval'];
		}

		if ($fecha1 == ""){
			//29 julio 2019, las nuevas instalaciones de la aplicacion , empezaran con los IDs al dia
			$fecha1 = $wfechaayer; '2019-03-01';
		}


		if ($fecha1 < $wfechaayer){//la ultima actualizacion fue antes de ayer.
//actualizar los IDs si la fecha actual es posterior a la ultima actualizacion.

			$arrayPrescripciones = actualizarIDsPrescripcionesPeriodo($wemp_pmla,$fecha1,$wfechaayer , "" );
			if ( count($arrayPrescripciones) > 0){//Si el arreglo tiene elementos , indica que se pudieron actualizar los IDs.
				//Actualizar los IDs , desde la ultima fecha actualizada , hasta el dia de ayer.
				$wfechaactualizacionIDs = $wfechaayer;//actualizar la ultima fecha de actualizacion

				echo "<script>jAlert('Actualizados los IDs hasta el dia : ".$wfechaactualizacionIDs."','ALERTA')</script>";


			}


		}//if ($fecha1 <$wfechaayer){//la ultima actualizacion fue antes de ayer.

		echo "<div id='fechacorrectaactualizacionids' >$wfechaactualizacionIDs</div>";//4 de junio 2019








	// Modal mipres


		echo "<div id='dvAuxModalMipres' style='display:none'></div>";

		echo "<p align=center><span><input type='button' value='Cerrar ventana' onclick='cerrarVentana();'></span></p>";


	?>


		<!-- Iframe y div para imprimir la información de los informes de mipres Mavila 05-10-2020 :)-->
		<iframe name="print_frame" style="display:none" width="0" height="0" frameborder="0" src="about:blank">
        </iframe>
		<div id="printDiv" name="printDiv" style="display:none"></div>
	</BODY>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>
<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
} //del else //if(isset($accion)) //sobra o falta 22 abril 2019

}//del else if(!isset($_SESSION['user']) && $proceso!="actualizar") //Fin de session
?>
