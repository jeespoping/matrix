<?php
include_once("conex.php");

if( isset($consultaAjax) == false ){

?>
	<html>
	<head>
	<title>Medicamentos Matrix vs Unix</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/smartpaginator.css" rel="stylesheet" /> <!-- Autocomplete -->

	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->

	<style>

		.tborder
		{
			/*border: solid black;*/
		}
		.visibilidad
		{
			display:none;
		}

		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}

		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 16pt;
		}

		.ui-autocomplete{
			max-width: 	250px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}


		select {
			width:200px;
			font-size:12px;
		}



		.encabezadoTabla1
		{
			 background-color: #2A5DB0;
			 color: #FFFFFF;
			 font-size: 8.5pt;
			 font-weight: bold;
		}
		.fila11
		{
			 background-color: #C3D9FF;
			 color: #000000;
			 font-size: 8.5pt;
		}
		.fila22
		{
			 background-color: #E8EEF7;
			 color: #000000;
			 font-size: 8.5pt;
		}

		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 6pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}


	</style>



	</script>
<script>

//------------------------------------------------------------------
// medicamentosUnixMatrix
// Autor : Felipe Alvarez
//
// Descripcion : El programa es un sencillo reporte de comparacion de los medicamentos que se grabaron
//				 por matrix y pasaron a unix por medio del integrador . los medicamentos y materiales se
//				 comparan especificamente en el estado de facturable y no facturable, este estado debe
//				 quedar igual en los dos sistemas
//
// Nota:		 El programa solo deja seleccionar un dia , esto se hizo para no sobrecargar el sistema
//				 ya que la consulta es pesada y va a los dos sistemas.
//
/*

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  CAMBIOS PARA MIGRACION
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	CODIGO	|	FECHA		|	AUTOR 	|	DESCRIPCION	
----------------------------------------------------------------------------------------------------------------------------------------------
	MIGRA_1	|	2019-02-27	|	Jerson	|	Se quita caracter & del llamado de la función
	MIGRA_2	|	2019-02-27	|	Jerson	|	Se quita caracter & del llamado de la función

	
----------------------------------------------------------------------------------------------------------------------------------------------
*/

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		cargar_datapicker();


	});



	function cargar_datapicker()
	{
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
		ponerdatepicker();
	}function cargar_datapicker()
	{
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
		ponerdatepicker();
	}

	function ponerdatepicker()
	{
		$(".datepicker").datepicker({
							showOn: "button",
							buttonImage: "../../images/medical/root/calendar.gif",
							buttonImageOnly: true,
							defaultTime: 'now'
						}).attr("disabled","disabled");


	}


	function consultarMedicamentos()
	{

		var permitir = false;


		var fechaini = $("#fechainicio").val();
		var fechafin = $("#fechafin").val();

		$("#botones").hide();

		if(fechaini != '' && fechafin !='')
		{
			permitir = true;
		}
		if (permitir  == false)
		{
			alert ("seleccione fechas validas");
		}
		else
		{

			if($("#cco").val() =='')
			{
				alert("Debe seleccionar centro de costos");
				return;
			}

			$("#botonbuscar").hide();
			$("#divbotonoculto").show();

			//alert($("#cco").val());

			$.post("medicamentosUnixMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'consultarMedicamentos',
				fechainicio:	   fechaini,
				fechafin:		   fechafin,
				cco:			   $("#cco").val()


			},function(data) {
				//alert("hola");
				$("#resultados").html(data);

				$("#divbotonoculto").hide();
				$("#botonbuscar").show();
				$("#botones").show();
				$('#filtro').quicksearch('#tableresultados .find');
				$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });



			});
		}


	}

	function validarfechas(ele)
	{
		ele = jQuery(ele);
		var valor = ele.val();
		$(".datepicker").val(valor);
        consultarcco();

		//alert("hola");
	}

	function consultarcco(){
	//	alert($('#wemp_pmla').val());

		$.post("medicamentosUnixMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'consultarcco',
				fechaini:	  	  $("#fechainicio").val(),
				fechafin:		  $("#fechainicio").val()


			},function(data) {

				//alert(data);
				$("#divselect").html(data);


			});

	}

	function exit(){

		window.close()

	}

	function ocultarbuenos()
	{
		$(".bueno").hide();

	}

	function vertodos()
	{

		$(".bueno").show();
		$(".malo").show();
	}

	function verlog(documento,linea,historia,ingreso, fuente,cardetreg)
	{


			//alert("documento:"+documento+"-linea:"+linea+"historia"+historia+"ingreso:"+ingreso+"fuente:"+fuente+"cardetreg:"+cardetreg);


			$("#explicacionlog").html("");
			$.post("medicamentosUnixMatrix.php",
			{
				consultaAjax:     '',
				wemp_pmla:        $('#wemp_pmla').val(),
				accion:           'consultarlog',
				wdocumento:		   documento,
				wlinea:			   linea,
				whistoria:		   historia,
				wingreso:		   ingreso,
				wfuente:		   fuente,
				wreg:			   cardetreg



			},function(data) {

				//alert(data);

				$("#explicacionlog").html(data);


			});

			// abrir modal de explicacion del caso
			$( "#explicacionlog" ).dialog({

			height: 400,
			width:  800,
			modal: true,
			title: "Resultados log",
			      buttons: {
					Cerrar: function() {
					  $( this ).dialog( "close" );
					}
				}
			});
	}



</script>
</head>

<?php

}

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if(isset($accion))
{
	include_once("root/comun.php");
	include_once("movhos/movhos.inc.php");
	include_once("root/magenta.php");
	switch($accion)
	{

		case "consultarMedicamentos" :
		{
			consultarMedicamentos($wemp_pmla,$fechainicio,$fechafin,$cco);
			break;

		}

		case "consultarcco" :
		{
			consultarcco($fechaini, $fechafin,$wemp_pmla);
			break;

		}

		case "consultarlog" :
		{
			consultarlog($wdocumento, $wlinea, $whistoria, $wingreso, $wfuente ,$wreg);
			break;
		}

	}
	return;

}

function consultarlog($wdocumento, $wlinea, $whistoria, $wingreso, $wfuente,$wreg)
{
	global $conex;
	global $conexUnix;
	$wemp_pmla = "01";
	$conex = obtenerConexionBD("matrix");
	$wbasedato = 'movhos';



	//echo "<table align=center ><><tr><td>Historia:</td><td>".$whistoria."</td>"


	$findme   	= '|';
	$pos 		= strpos($wreg, $findme);


	//MIGRA_1
	conexionOdbc($conex, $wbasedato, $conexUnix, 'facturacion');

	if($pos === false)
	{


		 $sqlu = "SELECT logfec , logusu, logpro,idenom,ideap1
				   FROM Falog , Siide
				  WHERE logva1  = '".$whistoria." - ".$wingreso."'
					AND logva2  = '".$wfuente." - ".$wdocumento."'
					AND logreg  = '".$wreg."'
					AND logtip  = 'M'
					AND logusu = idecod
					GROUP BY 1,2,3,4,5

					UNION

				 SELECT logfec , logusu, logpro,idenom,ideap1
				   FROM ivlog, Siide
			      WHERE logva1  = '".$wfuente."'
			        AND logva2  = '".$wdocumento."'
				    AND logtip  = 'M'
					AND logusu = idecod
				  GROUP BY 1,2,3,4,5
				  ORDER BY 1 DESC";
	}
	else
	{
		$wreg = str_replace('|', ',', $wreg);

		$sqlu = "SELECT logfec , logusu, logpro,idenom,ideap1
				   FROM Falog , Siide
				  WHERE logva1  = '".$whistoria." - ".$wingreso."'
					AND logva2  = '".$wfuente." - ".$wdocumento."'
					AND logreg  IN ( ".$wreg." )
					AND logtip  = 'M'
					AND logusu = idecod
					GROUP BY 1,2,3,4,5

					UNION

				 SELECT logfec , logusu, logpro,idenom,ideap1
				   FROM ivlog, Siide
			      WHERE logva1  = '".$wfuente."'
			        AND logva2  = '".$wdocumento."'
				    AND logtip  = 'M'
					AND logusu = idecod
				  GROUP BY 1,2,3,4,5
				  ORDER BY 1 DESC";


	}


	//echo $sqlu;
	$resu = odbc_do( $conexUnix, $sqlu );

	$i=0;
	$encabezado = "<br><br><br><table align='center'><tr class='encabezadoTabla'><td colspan='4'>Resultado de Analisis de Modificacion en Logs de Unix</td></tr>
	<tr class=encabezadoTabla ><td colspan='4'>Historia : ".$whistoria."-".$wingreso."</td></tr>
	<tr class=encabezadoTabla align='left' class='fila1'>
	<td><b>Tipo de Registro</b></td>
	<td><b>Fecha de Registro</b></td>
	<td><b>Usuario </b></td>
	<td><b>Programa </b></td><tr>";
	if($resu)
	{
		$tr='';
		while( odbc_fetch_row($resu))
		{
			if (($i%2)==0)
			$wcf="fila1";  // color de fondo de la fila
			else
			$wcf="fila2"; // color de fondo de la fila
			$i++;

			$tr .=" <tr align='left' style='background-color: rgb(153, 153, 153); font-size: 10pt;'><td colspan='4'>Registro ".$i."</td></tr>
				   <tr class='".$wcf."'><td>Modificacion</td>
				   <td>".odbc_result($resu,1)."</td>
				   <td>".odbc_result($resu,2)."-".odbc_result($resu,4)." ".odbc_result($resu,5)." </td>
				   <td>".odbc_result($resu,3)."</td></tr>";


		}
	}



	if ($i>0)
	{
		echo $encabezado;
		echo $tr;
		echo "</table>";
		//echo "<br><br><table align='center'><tr><td><input type='button' value='Cerrar' onclick='cerrarmodal()'></td></tr></table>";
	}
	else
	{
		echo "<br><br><br><table align='center'><tr><td>No se encontraron datos</td></tr></table>";
	}


}

/*
Funcion que trae todos los articulos que mueven inventario  grabados en la tabla cliame_000106 Matrix en determinados rangos de fechas
y consulta su estado de facturacion en Unix
*/

function consultarMedicamentos($wemp_pmla,$fechainicio,$fechafin,$cco)
{

	global $conex;
	global $conexUnix;
	$wemp_pmla = "01";
	$conex = obtenerConexionBD("matrix");
	$wbasedato = 'movhos';

	//MIGRA_2
	conexionOdbc($conex, $wbasedato, $conexUnix, 'facturacion');

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

	// Selecciono  los cargos que mueven inventario  que tengan linea (Tcardoi !=''), estos seran la base para ir a unix a comparar
	// su estado facturable o no .
	$sql = "SELECT Tcardoi,Tcarlin,Tcarfac , ".$wbasedato."_000106.id , Tcarprocod , Tcarhis, Tcaring , Artcom , ".$wbasedato."_000106.id
			  FROM ".$wbasedato."_000106 LEFT JOIN  ".$wbasedato_mov."_000026 ON Artcod = Tcarprocod
			 WHERE  Tcardoi !=''
			   AND  Tcarfec = '".$fechainicio."'
			   AND  Tcarser ='".$cco."'
			   ORDER BY   ".$wbasedato."_000106.id DESC  ";

	//antes la condicion de dias estaba asi , pero por motivos de velocidad se cambio
	// AND  Tcarfec BETWEEN '".$fechainicio."' AND '".$fechafin."'

	$res = mysql_query( $sql, $conex  ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	// Encabezado de la tabla resultado
	$html.= "<table id='filtrotable' align ='left'>
			 <tr>
					<td Class='encabezadoTabla'>Buscar</td>
					<td Class='encabezadoTabla' ><input type='text' id='filtro' ></td>
					<td colspan=10></td>
				</tr>
			 </table>
			 <br>
			 <br>
			 <table align='center' align ='center' id='tableresultados'>
				<tr Class='encabezadoTabla1'>
					<td>#</td>
					<td>Historia</td>
					<td>Articulo</td>
					<td>Politica</td>
					<td>Doc y Lin Matrix</td>
					<td>Fuente</td>
					<td>Estado Facturacion Matrix</td>
					<td>Articulo Unix</td>
					<td>Doc y Lin En Unix</td>
					<td>Estado Facturacion Facardet</td>
					<td>Estado Facturacion Ivdrodet</td>
					<td>Resultado</td>
					<td></td>
			</tr>";
	//--------------------------
	$t=0;
	$contador = 0;
	$condicion = true;
	$title ='';
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$arr_documentos = array();
	$arr_itdrodoc 	= array();

	while($row = mysql_fetch_array($res))
	{

		$auxcardetreg='';

		$t++;
		if (($t%2)==0)
			$wcf="fila11";  // color de fondo de la fila
		else
			$wcf="fila22"; // color de fondo de la fila



		$consulta_politica = "SELECT Audpol
								FROM ".$wbasedato."_000107
							   WHERE  Audreg = '".$row['id']."'";
		$res1 = mysql_query( $consulta_politica, $conex  ) or die( mysql_errno()." - Error en el query $consulta_politica - ".mysql_error() );

		//echo $t."-".$consulta_politica."<br>";
		$title ='';
		while($row1 = mysql_fetch_array($res1))
		{
			$title .= utf8_encode($row1['Audpol']);

		}


		//$title = utf8_encode($row['Audpol']);
		$poneralert = false;
		if( $title !='')
		{
			$poneralert = true;
		}

		$html3="<td>".$t."</td><td>".$row['Tcarhis']."-".$row['Tcaring']."</td>";
		if($poneralert)
		{
			$html3.="<td nowrap=nowrap  >".$row['Tcarprocod']." - ".utf8_encode(substr($row['Artcom'], 0, 20))."</td><td class='tooltip'  style='cursor:pointer' title='".$title."' align='center'><img width='15' height='15' src='../../images/medical/sgc/Mensaje_alerta.png' ></td>";
		}
		else
		{
			$html3.="<td nowrap=nowrap >".$row['Tcarprocod']." - ".utf8_encode(substr($row['Artcom'], 0, 20))." </td><td></td>";
		}




		// Se consulta el numero y la linea de cada medicamento en movhos_000003
		// y asi averiguar su estado
		$sql1 = "SELECT  Fdeubi
				  FROM ".$wbasedato_mov."_000003
				 WHERE  Fdenum = '".$row['Tcardoi']."'
				   AND  Fdelin = '".$row['Tcarlin']."' ";
		$res1 = mysql_query( $sql1, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );



		$estado ='';
		if($row1 = mysql_fetch_array($res1))
		{
			$estado = $row1['Fdeubi'];
		}

		// el proceso solo sigue si se encuentra en estado UP = procesado a unix , US = Unix sin procesar (esto es porque quedan muchos
		// medicamentos US en la tabla movhos_000003 seria ideal que no quedaran
		//if ($estado =='UP' || $estado =='US') esta linea se comento para que fuera siempre  y evaluara los estados de facturacion sin importar los estados
		if (true)
		{

			//--------------------------------------------------------------
			// valido si tiene regla que divide un articulo entre varios en la tabla movhos_000158
			// a veces algunos medicamentos se parten en varios componentes y pasan a unix divididos ,
			// entonces hay que hacer un analisis particular para estos
			$sqlvalidacion = "SELECT Logdoc , Logpro
								FROM ".$wbasedato."_000106 ,  ".$wbasedato_mov."_000158
							   WHERE Tcardoi = '".$row['Tcardoi']."'
								 AND Tcarlin = '".$row['Tcarlin']."'
								 AND Tcarprocod = '".$row['Tcarprocod']."'
								 AND Tcardoi = Logdoc
								 AND Tcarlin = Loglin ";
			$resvalidacion = mysql_query( $sqlvalidacion, $conex  ) or die( mysql_errno()." - Error en el query $sqlvalidacion - ".mysql_error() );

			//echo $t."-".$sqlvalidacion."<br>";

			// La variable validacion se usara para saber si el medicamento se parte en varios o se reemplaza por otro
			$validacion ='no';

			$facturableaux ='';
			if($rowvalidacion = mysql_fetch_array($resvalidacion))
			{
				$validacion = 'si';
				$esdereemplazo ='no';
				if( $rowvalidacion['Logpro'] =='on')
				{
					$esdereemplazo = 'si';
				}
				$facturableaux = $row['Tcarfac'];
			}
			//-------------------------------------------
			//-------------------------------------------


			// se hace una comparacion de si existe en el vector de documentos ya la fuente no se busca, sino que
			// se extrae el valor del  array
			if(!array_key_exists($row['Tcardoi'], $arr_documentos))
			{
				 $arr_documentos[$row['Tcardoi']] = "";

				// se averigua la fuente del registro grabado
				$sql2 = "   SELECT  Fenfue
							  FROM  ".$wbasedato_mov."_000002
							 WHERE  Fennum  = '".$row['Tcardoi']."'";
				$res2 = mysql_query( $sql2, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );



				$fuente ='';
				if($row2 = mysql_fetch_array($res2))
				{
					$fuente = $row2['Fenfue'];
					$arr_documentos[$row['Tcardoi']] = $fuente;
				}
				//echo $t."-".$sql2."<br>";

			}
			else
			{
				$fuente = $arr_documentos[$row['Tcardoi']];
			}

			$html3 .="<td>".$row['Tcardoi']."-".$row['Tcarlin']."</td>";
			$html3 .= "<td align='center'>".$fuente."</td><td align='center'>".$row['Tcarfac']."</td>";

			// si hay conexion a unix haga
			if( $conexUnix ){
				$documentoppal ='';

				// se crea un array para documentos de itdrodoc , esto para hacer mas agil el proceso y no se hagan
				// tantos querys a unix  , si ya se hizo la consulta no se vuelve a realizar
				if(!array_key_exists($row['Tcardoi'], $arr_itdrodoc))
				{
					$arr_itdrodoc[$row['Tcardoi']] = "";

					// se consulta en drodocdoc el documento con que se grabo a Unix , la tabla ITDRODOC
					// es una tabla puente entre Matrix y Unix  averiguo con los datos de Matrix con que documento
					// quedo en unix y sigo trabajando con este.
					$sqlu = "SELECT drodocdoc
							  FROM ITDRODOC
							 WHERE drodocnum  = '".$row['Tcardoi']."'
							   AND drodocfue  = '".$fuente."'";
					$resu = odbc_do( $conexUnix, $sqlu );

					if($resu)
					{
						if( odbc_fetch_row($resu))
						{
							$documentoppal = odbc_result($resu,1);
							$arr_itdrodoc[$row['Tcardoi']] = $documentoppal;

							//echo $t."-".$sqlu."<br>";
						}
					}

				}
				else
				{
					$documentoppal = $arr_itdrodoc[$row['Tcardoi']];
				}






				if( $documentoppal !='' )
				{

					$i = 0;
					$entro ='no';
					if( $documentoppal !='' )
					{
						$entro ='si';

						// para los articulos que no se partan en varios o no tengan regla en la movhos_000158
						if($validacion!='si')
						{


							/*
							Acontinuacion se la consulta de como quedaron los registros en
							FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
							cliame_000106
							*/
							// Selecciono si es facturable
							/*$selectiv   = " SELECT drodetfac , drodetart
											  FROM IVDRODET
											 WHERE drodetfue = '".$fuente."'
											   AND drodetdoc = '".$documentoppal."'
											   AND drodetite = '".$row['Tcarlin']."'";*/

							// Selecciono si es facturable
							$selectiv   = " SELECT drodetfac , drodetart,cardetfac,cardetreg
											  FROM IVDRODET , FACARDET
											 WHERE drodetfue = '".$fuente."'
											   AND drodetdoc = '".$documentoppal."'
											   AND drodetite = '".$row['Tcarlin']."'
											   AND drodetfue = cardetfue
											   AND drodetdoc = cardetdoc
											   AND drodetite = cardetite ";


							$resiv = odbc_do( $conexUnix, $selectiv );
							$drodetfac = odbc_result($resiv,1);
							$drodetart = odbc_result($resiv,2);
							$cardetfac = odbc_result($resiv,3);
							$cardetreg = odbc_result($resiv,4);

							$linea = '';
							$estaarticulo = true;

							/*
							Se agrega esta nueva validacion  , donde se ve si el articulo en Matrix corresponde al de Unix.

							Explicacion: En Matrix se graba un documento y linea  por cada articulo grabado, Esto mismo se hace en Unix  , Existe
							una tabla en Unix donde hay relacion del documento y linea Matrix con documento y linea Unix  generalmente son distintos los documentos
							pero el numero de linea coincide. Para estar seguros de que el articulo en Matrix corresponda al de Unix se  compara tambien el articulo
							si es el articulo se trabaja con la linea  de matrix porque se sabe que es la misma sino se busca en todo el documento unix la linea que corresponde
							a la linea en matrix

							Si sí corresponde   se consulta el estado de facturable o no en Facardet
							*/
							if($row['Tcarprocod'] == $drodetart)
							{
								//$html3.= "<td>No</td>";

								// Se consulta el estado de Facardet de si es facturable o no
								// $selectfacar = "   SELECT cardetfac
													 // FROM FACARDET
													// WHERE cardetfue = '".$fuente."'
													  // AND cardetdoc = '".$documentoppal."'
													  // AND cardetite = '".$row['Tcarlin']."'
													  // AND cardethis = '".$row['Tcarhis']."'
													  // AND cardetnum = '".$row['Tcaring']."'";



								// $resfacar = odbc_do( $conexUnix, $selectfacar );
								// $cardetfac = odbc_result($resfacar,1);

							    $linea= $row['Tcarlin'];
							}
							else
							{
								// Entro aqui si las lineas de matrix vs Unix no son las mismas
								//$html3.= "<td>SI</td>";

								// Hago una busqueda del articulo y documento y asi hallo la nueva linea
								$selectiv   = " SELECT drodetite
												  FROM IVDRODET
												 WHERE drodetfue = '".$fuente."'
												   AND drodetdoc = '".$documentoppal."'
												   AND drodetart = '".$row['Tcarprocod']."' ";

								$resiv = odbc_do( $conexUnix, $selectiv );
								//$drodetfac = odbc_result($resiv,1);
								//$drodetart = odbc_result($resiv,2);
								$drodetlinea = odbc_result($resiv,1);


								// Con la nueva linea busco el estado de facturacion en unix del articulo en IVDRODET
								$selectiv   = " SELECT drodetfac , drodetart
												  FROM IVDRODET
												 WHERE drodetfue = '".$fuente."'
												   AND drodetdoc = '".$documentoppal."'
												   AND drodetite = '".$drodetlinea."'";

								$resiv = odbc_do( $conexUnix, $selectiv );
								$drodetfac = odbc_result($resiv,1);
								$drodetart = odbc_result($resiv,2);

								//----------------------------------

								//----------------------------------
								// Con la nueva linea busco el estado de facturacion en unix del articulo en IVDRODET
								$selectfacar = "   SELECT cardetfac,cardetreg
													 FROM FACARDET
													WHERE cardetfue = '".$fuente."'
													  AND cardetdoc = '".$documentoppal."'
													  AND cardetite = '".$drodetlinea."'
													  AND cardethis = '".$row['Tcarhis']."'
													  AND cardetnum = '".$row['Tcaring']."'";

								$resfacar = odbc_do( $conexUnix, $selectfacar );
								$cardetfac = odbc_result($resfacar,1);
								$cardetreg = odbc_result($resfacar,2);


								$linea = $drodetlinea;
								if($linea =='')
								{
									$estaarticulo = false;
								}

							}

							if ($linea=='')
							{
								// si la Linea es vacia es porque no hay articulo grabado en unix
								$html3.= "<td>no hay articulo</td>";
							}
							else
							{

								// Query matrix donde se busca el nombre del articulo que se grabo en unix
								$selectnombre ="SELECT Artcom FROM  ".$wbasedato_mov."_000026  WHERE Artcod = '".$drodetart."' ";
								$resselec = mysql_query($selectnombre,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar varios ): ".$selectnombre." - ".mysql_error());
								$nombre = '';
								if($rowres = mysql_fetch_array($resselec))
								{
								  $nombre = $rowres['Artcom'];
								}


								$html3.= "<td nowrap='nowrap'>".$drodetart."-".utf8_encode(substr($nombre, 0, 20) )."</td>";
							}

							$html3.= "<td>".$documentoppal."-".$linea."</td>";
							$html3.="<td align='center' >".$cardetfac."</td>";
							$html3.= "<td align='center' >".$drodetfac."</td>";

							/*
							Despues de consultar los estados de facturacion en FACARDET , IVDRODET y Cliame_000106 se comparan para ver si son iguales
							o diferentes
							*/

							$condicion = true;
							if(($cardetfac == $drodetfac  &&  $drodetfac == $row['Tcarfac'])  )
							{

								$html4= "<td>Iguales</td><td></td></tr>";
							}
							else
							{
								$condicion = false;
								$contador++;
								$html4= "<td><font  color='red'>Diferente</font></td><td style='cursor:pointer ' onclick='verlog(".$documentoppal." , ".$linea." , ".$row['Tcarhis']." , ".$row['Tcaring']." , \"".$fuente."\", ".$cardetreg.")'>ver</td></tr>";
							}
							if(!$estaarticulo)
							{
								$html4= "<td><font  color='red'>No existe el articulo en unix</font></td></tr>";
								if ($condicion == false)
								{
									$html4= "<td><font  color='red'>Diferente <br> No existe el articulo en unix</font></td><td></td></tr>";

								}
								else
								{
									$contador++;
								}
								$condicion = false;
							}

							if($condicion )
							{
								$html.="<tr class='".$wcf." bueno find'>".$html3."".$html4;
							}
							else
							{
								$html.="<tr class='".$wcf." malo find'>".$html3."".$html4;
							}
						}
						else
						{
							// si tiene una regla donde parte el registro por dos

							// Consulto los medicamentos y veo cuales son su reglas y en cuantos
							// articulos se parte el medicamento

							if ($esdereemplazo =='si')
							{
								$querycenpro = "SELECT  Pdeins
												  FROM  ".$wcenmez."_000003
												 WHERE  Pdepro ='".$row['Tcarprocod']."'";

								$resquerycenpro=  mysql_query( $querycenpro, $conex  ) or die( mysql_errno()." - Error en el query $querycenpro - ".mysql_error() );

								$p=-1;
								$variablereemplazo 	  = '';
								$auxvariablereemplazo = '';
								while($rowquerycenpro = mysql_fetch_array($resquerycenpro))
								{
									$p++;
									$auxvariablereemplazo = $auxvariablereemplazo.",".(($row['Tcarlin']*1) + $p);

								}

								$variablereemplazo = substr($auxvariablereemplazo,1);
								 //$variablereemplazo = $auxvariablereemplazo;

							}
							else
							{
								$variablereemplazo = $row['Tcarlin'];
							}

							$sqlval			 = "  SELECT Logdoc,Loglin,Logaor,Logare
													FROM ".$wbasedato_mov."_000158
												   WHERE Logdoc = '".$row['Tcardoi']."'
													 AND Loglin IN ( ".$variablereemplazo." ) ";

							//echo $sqlval;
							//AND Logaor = '".$row['Tcarprocod']."'

							$resval =  mysql_query( $sqlval, $conex  ) or die( mysql_errno()." - Error en el query $sqlval - ".mysql_error() );

							$bandera = true;
							$cardetfacaux = "";
							$drodetfacaux = "";
							$auxdoclin="";
							$procedimientosiguales = true;
							$auxprocedimientos = "";
							$auxprolin ='';

							while($rowval = mysql_fetch_array($resval))
							{



								// se selecciona el estado del registro en ivdrodet facturable o no facturable
								$selectiv   = " SELECT drodetfac, drodetart
												  FROM IVDRODET
												 WHERE drodetfue = '".$fuente."'
												   AND drodetdoc = '".$documentoppal."'
												   AND drodetite = '".$rowval['Loglin']."'";

								$resiv = odbc_do( $conexUnix, $selectiv );
								$drodetfac = odbc_result($resiv,1);
								$drodetart = odbc_result($resiv,2);

								/*
								Se agrega esta nueva validacion  , donde se ve si el articulo en Matrix corresponde al de Unix.

								Explicacion: En Matrix se graba un documento y linea  por cada articulo grabado, Esto mismo se hace en Unix  , Existe
								una tabla en Unix donde hay relacion del documento y linea Matrix con documento y linea Unix  generalmente son distintos los documentos
								pero el numero de linea coincide. Para estar seguros de que el articulo en Matrix corresponda al de Unix se  compara tambien el articulo
								si es el articulo se trabaja con la linea  de matrix porque se sabe que es la misma sino se busca en todo el documento unix la linea que corresponde
								a la linea en matrix

								Si sí corresponde   se consulta el estado de facturable o no en Facardet
								*/

								if($drodetart == $rowval['Logare'] )
								{

									$selectfacar = "   SELECT cardetfac,cardetreg
														FROM FACARDET
														WHERE cardetfue = '".$fuente."'
														AND cardetdoc = '".$documentoppal."'
														AND cardetite = '".$rowval['Loglin']."'
														AND cardethis = '".$row['Tcarhis']."'
													    AND cardetnum = '".$row['Tcaring']."'";

									$resfacar  = odbc_do( $conexUnix, $selectfacar );
									$cardetfac ="";
									$cardetfac = odbc_result($resfacar,1);
									$cardetreg = odbc_result($resfacar,2);
									// $cardetreg = 2;
									$linea =  $rowval['Loglin'];
								}
								else
								{

									// Entro aqui si las lineas de matrix vs Unix no son las mismas
									//$html3.= "<td>SI</td>";

									// Hago una busqueda del articulo y documento y asi hallo la nueva linea

									$selectiv   = " SELECT drodetite
													  FROM IVDRODET
													 WHERE drodetfue = '".$fuente."'
													   AND drodetdoc = '".$documentoppal."'
													   AND drodetart = '".$rowval['Logare']."' ";

									$resiv = odbc_do( $conexUnix, $selectiv );
									//$drodetfac = odbc_result($resiv,1);
									//$drodetart = odbc_result($resiv,2);
									$drodetlinea = odbc_result($resiv,1);
									$existeprocedimiento = true;
									if ($drodetlinea =='')
									{
										$existeprocedimiento = false;
									}



									// se selecciona el estado del registro en ivdrodet facturable o no facturable
									$selectiv   = " SELECT drodetfac, drodetart
													  FROM IVDRODET
													 WHERE drodetfue = '".$fuente."'
													   AND drodetdoc = '".$documentoppal."'
													   AND drodetite = '".$drodetlinea."'";

									$resiv = odbc_do( $conexUnix, $selectiv );
									$drodetfac = odbc_result($resiv,1);
									$drodetart = odbc_result($resiv,2);

									$selectfacar = "   SELECT cardetfac,cardetreg
														 FROM FACARDET
														WHERE cardetfue = '".$fuente."'
														  AND cardetdoc = '".$documentoppal."'
														  AND cardetite = '".$drodetlinea."'
														  AND cardethis = '".$row['Tcarhis']."'
													      AND cardetnum = '".$row['Tcaring']."'";

									$resfacar  = odbc_do( $conexUnix, $selectfacar );
									$cardetfac ="";
									$cardetfac = odbc_result($resfacar,1);
									$cardetreg = odbc_result($resfacar,2);
									// $cardetreg = 1;

									$procedimientosiguales = false;
									$linea =  $drodetlinea;

									if($linea =='')
									{
										$estaarticulo = false;
									}

								}
								//$html3.= "<td>".odbc_result($resu,1)."-".$linea."</td>";



								if($cardetfac =='')
								{
									$cardetfac = '-';
								}

								if ($drodetfac=='')
								{
									$drodetfac = '-';
								}


								if($row['Tcarfac'] != $cardetfac)
								{
									$bandera = false;
								}

								if($row['Tcarfac'] != $drodetfac)
								{
									$bandera = false;
								}

								$auxdoclin = $auxdoclin."<br>".odbc_result($resu,1)."-".$linea;

								if($rowval['Logare']!='' )
								{
									// consulto el nombre del articulo
									$selectnombre ="SELECT Artcom FROM  ".$wbasedato_mov."_000026  WHERE Artcod = '".$rowval['Logare']."' ";
									$resselec = mysql_query($selectnombre,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar varios ): ".$selectnombre." - ".mysql_error());
									$nombre = '';
									if($rowres = mysql_fetch_array($resselec))
									{
									  $nombre = $rowres['Artcom'];
									}

								}

								$auxprolin = $auxprolin."<br>".$rowval['Logare'] ."-".utf8_encode(substr($nombre, 0, 20));

								$auxcardetreg = $auxcardetreg."|".$cardetreg;
								$drodetfacaux = $drodetfacaux."<br>".$drodetfac;
								$cardetfacaux = $cardetfacaux."<br>".$cardetfac;


							}


							$html3.= "<td nowrap='nowrap'>".$auxprolin."</td>";
							$html3.= "<td>".$auxdoclin."</td>";

							if($procedimientosiguales)
							{
								//$html3.="<td></td>";
								$procedimientosdistintos = false;
							}
							else
							{
								//$html3.="<td><font color='red'>Los Articulos son distintos <br>".$auxprocedimientos." </font></td>";
								$procedimientosdistintos = true;
							}
							$html3.= "<td align='center'>".$cardetfacaux."</td>";

							$html3.= "<td align='center'>".$drodetfacaux."</td>";
							$condicion = true;
							if ($bandera == false )
							{
								$condicion = false;
								$contador++;
								$auxcardetreg = substr($auxcardetreg,1);
								$html3.= "<td><font  color='red'>Diferente</font></td><td style='cursor:pointer ' onclick='verlog(".$documentoppal." , ".$linea." , ".$row['Tcarhis']." , ".$row['Tcaring']." , ".$fuente.", \"".$auxcardetreg."\")' >ver</td>";
							}
							else
							{
								$html3.= "<td>Iguales</td><td></td>";
							}
							$html3.="</tr>";

							if($condicion)
							{

								$html.="<tr class='".$wcf." bueno find'>".$html3;
							}
							else
							{
								$html.="<tr class='".$wcf." malo find'>".$html3;
							}

						}

					}
					if($entro=='no')
					{
						$contador++;
						$html.= "<tr class='".$wcf." malo find' >".$html3."<td>-</td><td>-</td><td>-</td><td>-</td><td><font  color='red'>Diferente<br>no esta integrado</font></td><td></td>";
					}



				}
				else
				{
					$contador ++;
					$html.="<tr class='".$wcf." malo find'>".$html3."<td>Articulo no ha sido integrado</td><td></td><td></td><td></td><td><font  color='red'>Diferente <br> No existe el articulo en unix</font></td><td></td></tr>";

				}

			}
			else
			{
				$html.= "<td>no conexion</td>";
			}
		}
		else
		{
			$contador ++;
			// se averigua la fuente del registro grabado
			$sql2 = "   SELECT  Fenfue
						  FROM  ".$wbasedato_mov."_000002
						 WHERE  Fennum  = '".$row['Tcardoi']."'";
			$res2 = mysql_query( $sql2, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );



			$fuente ='';
			if($row2 = mysql_fetch_array($res2))
			{
				$fuente = $row2['Fenfue'];
				$arr_documentos[$row['Tcardoi']] = $fuente;
			}
			$html.= "<tr class='".$wcf." malo find'>".$html3."<td>".$row['Tcardoi']."-".$row['Tcarlin']."</td><td align='center'>".$fuente."</td><td align='center'>".$row['Tcarfac']."</td><td></td><td></td><td></td><td></td><td><font  color='red'>Diferente <br> No existe el articulo en unix</font></td><tr>";
		}


	}

	$html	.= "</table><br><br><br>";
	$html2	.= "<table><tr class='encabezadoTabla'><td colspan='2'>Resumen</td></tr>
		  <tr>
			<td class='encabezadoTabla'>Total de registros</td><td class='fila1'>".$t."</td>
		  </tr>
		  <tr>
			<td class='encabezadoTabla'>Total de registros distintos</td><td class='fila1'>".$contador."</td>
		  </tr>
		  </table><br><br><br>";

	echo $html2;
	echo $html;

	//Liberacion de conexion Matrix
	liberarConexionBD($conex);

	//Liberacion de conexion Unix
	liberarConexionOdbc($conexUnix);
	odbc_close_all();


}

function consultarcco ($fechaini, $fechafin,$wemp_pmla)
{
	global $conex;
	global $conexUnix;
	$wemp_pmla = "01";

	$conex = obtenerConexionBD("matrix");

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$select = "SELECT Tcarser , Cconom
			  FROM ".$wbasedato."_000106 , ".$wbasedato_mov."_000011
			 WHERE  Tcardoi !=''
			   AND  Tcarfec BETWEEN '".$fechaini."' AND '".$fechafin."'
			   AND  Tcarser = Ccocod
			 GROUP BY Tcarser ";

	$res = mysql_query( $select, $conex  ) or die( mysql_errno()." - Error en el query $select - ".mysql_error() );
	//$num = mysql_num_rows( $res );
	$html.= "<select id='cco' ><option value=''>Seleccione ...</option>";

	while($row = mysql_fetch_array($res))
	{
		$html.=  "<option value='".$row['Tcarser']."'>".$row['Tcarser']."-".$row['Cconom']."</option>";
	}

	$html.= "</select >";
	echo $html;




}

?>
 <body>
	<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
	<?php


	include_once("root/comun.php");
	include_once("movhos/movhos.inc.php");
	//include_once("root/magenta.php");



	/********************************************************************************************
	****************************** INICIO APLICACIÓN ********************************************
	********************************************************************************************/
	$wbasedato = "";
	$wactualiz = " Noviembre 25 de 2015 ";


	//Variable para determinar la empresa
	if(!isset($wemp_pmla))
	{
		$wemp_pmla = '01';
	}

	encabezado("Estado Medicamentos Unix VS Matrix ", $wactualiz, "clinica");

	echo "<br><br><br>";
	echo "<table align='center' >
			<tr class='encabezadoTabla' align='center'>
				<td colspan='4'>
					Seleccione fechas
				</td>
			</tr>
			<tr class='fila1'>
				<td>
					<b>Fecha inicio</b>
				</td>
				<td >
					<input type='text' class='datepicker' id='fechainicio' onchange='validarfechas(this)' value='".date("Y-m-d")."'>
				</td>
				<td>
					<b>Fecha Fin</b>
				</td>
				<td>
					<input type='text' class='datepicker' id='fechafin' onchange='validarfechas(this)'  value='".date("Y-m-d")."'>
				</td>
			</tr>
			<tr class='fila1'>

				<td align='center' colspan='4'><b>Centro de costos</b><div id='divselect'><select id='cco' >";
					$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
					$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
					$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
					$select = "SELECT Tcarser , Cconom
							  FROM ".$wbasedato."_000106 , ".$wbasedato_mov."_000011
							 WHERE  Tcardoi !=''
							   AND  Tcarfec BETWEEN '".date("Y-m-d")."' AND '".date("Y-m-d")."'
							   AND  Tcarser = Ccocod
							 GROUP BY Tcarser ";

					$res = mysql_query( $select, $conex  ) or die( mysql_errno()." - Error en el query $select - ".mysql_error() );
					//$num = mysql_num_rows( $res );
					echo "<option value=''>Seleccione ...</option>";

					while($row = mysql_fetch_array($res))
					{
						echo "<option value='".$row['Tcarser']."'>".$row['Tcarser']."-".$row['Cconom']."</option>";
					}

					/*
					$select = "SELECT Ccocod ,	Cconom
					             FROM movhos_000011
								WHERE Ccoest ='on'";
					$res = mysql_query( $select, $conex  ) or die( mysql_errno()." - Error en el query $select - ".mysql_error() );
					//$num = mysql_num_rows( $res );
					while($row = mysql_fetch_array($res))
					{
						echo "<option value='".$row['Ccocod']."'>".$row['Ccocod']."-".$row['Cconom']."</option>";
					}
					*/
	echo"		</select></div></td>
			</tr>
			<tr class='fila2' align='center'>
				<td colspan='4' >
					<input type='button' value='Buscar' id='botonbuscar' onclick='consultarMedicamentos()'><div id='divbotonoculto' style='display:none'><button id='botoncargar' ><img class='' border='0' src='../../images/medical/ajax-loader2.gif' title='Cargando..' ></button></div><input type='button' value='Cerrar' onclick='exit()'><br><br><div style='display:none' id='botones'><input type='button' onclick='ocultarbuenos()' value='Mostrar solo diferentes'><input type='button' onclick='vertodos()' value='Mostrar Todos'></div>
				</td>
			</tr>
		  </table>";

	echo "<br><br><center><div  id='resultados'></div></center>";

	echo "<table><tr><td></td></tr></table>";
	echo "<div id='explicacionlog'  style='display:none'>";
	echo "</div>";



	?>
    </body>
</html>

