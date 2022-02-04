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

		// --> Estylo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		/*Firefox*/
		[tipo=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}

		select {
			width:200px;
			font-size:12px;
		}


	</style>
	<script>


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

</script>
</head>

<?php

}

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
$wemp_pmla = $_REQUEST['wemp_pmla'];

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

	}
	return;

}


function consultarMedicamentos($wemp_pmla,$fechainicio,$fechafin,$cco)
{


	global $conex;
	global $conexUnix;



	// $wemp_pmla = "01";



	$conex = obtenerConexionBD("matrix");
	$wbasedato = 'movhos';


	conexionOdbc($conex, $wbasedato, &$conexUnix, 'facturacion');

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion");
	$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");


	// Selecciono  los cargos que mueven inventario que no tengan el campo Tcaraun igual a on , esto
	// quiere decir los que no se les ha hecho el proceso de actualizar medicamento
	$sql = "SELECT Tcardoi,Tcarlin,Tcarfac , ".$wbasedato."_000106.id , Tcarprocod , Tcarhis, Tcaring , Artcom
			  FROM ".$wbasedato."_000106 LEFT JOIN  ".$wbasedato_mov."_000026 ON Artcod = Tcarprocod
			 WHERE  Tcardoi !=''
			   AND  Tcarfec BETWEEN '".$fechainicio."' AND '".$fechafin."'
			   AND  Tcarser ='".$cco."'";

	//AND  Tcaraun ='';

	$res = mysql_query( $sql, $conex  ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	$html.= "<table id='filtrotable' align ='left'>
			 <tr>
					<td Class='encabezadoTabla'>Buscar</td>
					<td Class='encabezadoTabla' ><input type='text' id='filtro' ></td>
					<td colspan=10></td>
				</tr>
			 </table>
			 <br>
			 <br>
			 <table align ='left' id='tableresultados'>
				<tr Class='encabezadoTabla'>
					<td>#</td>
					<td>Historia</td>
					<td>Articulo</td>
					<td>Doc y Lin Matrix</td>
					<td>Fuente</td>
					<td>Estado Facturacion Matrix</td>
					<td>Articulo Unix</td>
					<td>Doc y Lin En Unix</td>
					<td>Estado Facturacion Facardet</td>
					<td>Estado Facturacion Ivdrodet</td>
					<td>Resultado</td>
			</tr>";
	$t=0;
	$contador = 0;
	$condicion = true;
	while($row = mysql_fetch_array($res))
	{
		$t++;
		if (($t%2)==0)
			$wcf="fila1";  // color de fondo de la fila
		else
			$wcf="fila2"; // color de fondo de la fila



		$html3="<td>".$t."</td><td>".$row['Tcarhis']."-".$row['Tcaring']."</td><td nowrap=nowrap>".$row['Tcarprocod']." - ".utf8_encode($row['Artcom'])."</td>";
		$wbasedato_mov = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
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
		if ($estado =='UP' || $estado =='US')
		{

			//--------------------------------------------------------------
			// valido si tiene regla que divide un articulo entre varios en la tabla movhos_000158
			// a veces algunos medicamentos se parten en varios componentes y pasan a unix divididos ,
			// entonces hay que hacer un analisis particular para estos
			$sqlvalidacion = "SELECT Logdoc
								FROM ".$wbasedato."_000106 ,  ".$wbasedato_mov."_000158
							   WHERE Tcardoi = '".$row['Tcardoi']."'
								 AND Tcarlin = '".$row['Tcarlin']."'
								 AND Tcarprocod = '".$row['Tcarprocod']."'
								 AND Tcardoi = Logdoc
								 AND Tcarlin = Loglin ";
			$resvalidacion = mysql_query( $sqlvalidacion, $conex  ) or die( mysql_errno()." - Error en el query $sqlvalidacion - ".mysql_error() );

			$validacion ='no';
			$facturableaux ='';
			if($rowvalidacion = mysql_fetch_array($resvalidacion))
			{
				$validacion = 'si';
				$facturableaux = $row['Tcarfac'];
			}
			//-------------------------------------------
			//-------------------------------------------

			// se averigua la fuente del registro grabado
			$sql2 = "   SELECT  Fenfue
						  FROM  ".$wbasedato_mov."_000002
						 WHERE  Fennum  = '".$row['Tcardoi']."'";
			$res2 = mysql_query( $sql2, $conex  ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );


			$fuente ='';
			if($row2 = mysql_fetch_array($res2))
			{
				$fuente = $row2['Fenfue'];
			}
			$html3 .="<td>".$row['Tcardoi']."-".$row['Tcarlin']."</td>";
			$html3 .= "<td>".$fuente."</td><td>".$row['Tcarfac']."</td>";

			// si hay conexion a unix haga
			if( $conexUnix ){

				// se consulta en drodocdoc el documento con que se grabo a Unix , la tabla ITDRODOC
				// es una tabla puente entre Matrix y Unix  averiguo con los datos de Matrix con que documento
				// quedo en unix y sigo trabajando con este.
				$sqlu = "SELECT drodocdoc
						  FROM ITDRODOC
						 WHERE drodocnum  = '".$row['Tcardoi']."'
						   AND drodocfue  = '".$fuente."'";
				$resu = odbc_do( $conexUnix, $sqlu );

				if( $resu )
				{

					$i = 0;
					$entro ='no';
					while( odbc_fetch_row($resu) )
					{
						$entro ='si';

						// para los articulos que no se partan en varios o no tengan regla en la movhos_000158
						if($validacion!='si')
						{


							/*
							Acontinuacion se hace una validacion de como quedo los registros en
							FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
							cliame_000106 si esto es igual se cambia el estado de actualizado en unix
							en la tabla cliame_000106
							*/
							// Selecciono si es facturable
							$selectiv   = " SELECT drodetfac , drodetart
											  FROM IVDRODET
											 WHERE drodetfue = '".$fuente."'
											   AND drodetdoc = '".odbc_result($resu,1)."'
											   AND drodetite = '".$row['Tcarlin']."'";

							$resiv = odbc_do( $conexUnix, $selectiv );
							$drodetfac = odbc_result($resiv,1);
							$drodetart = odbc_result($resiv,2);




							$linea = '';
							$estaarticulo = true;
							if($row['Tcarprocod'] == $drodetart)
							{
								//$html3.= "<td>No</td>";

								// Selecciono si es facturable
								$selectfacar = "   SELECT cardetfac
													 FROM FACARDET
													WHERE cardetfue = '".$fuente."'
													  AND cardetdoc = '".odbc_result($resu,1)."'
													  AND cardetite = '".$row['Tcarlin']."'";

								$resfacar = odbc_do( $conexUnix, $selectfacar );
								$cardetfac = odbc_result($resfacar,1);

							    $linea= $row['Tcarlin'];
							}
							else
							{

								//$html3.= "<td>SI</td>";
								$selectiv   = " SELECT drodetite
												  FROM IVDRODET
												 WHERE drodetfue = '".$fuente."'
												   AND drodetdoc = '".odbc_result($resu,1)."'
												   AND drodetart = '".$row['Tcarprocod']."' ";

								$resiv = odbc_do( $conexUnix, $selectiv );
								//$drodetfac = odbc_result($resiv,1);
								//$drodetart = odbc_result($resiv,2);
								$drodetlinea = odbc_result($resiv,1);


								$selectiv   = " SELECT drodetfac , drodetart
												  FROM IVDRODET
												 WHERE drodetfue = '".$fuente."'
												   AND drodetdoc = '".odbc_result($resu,1)."'
												   AND drodetite = '".$drodetlinea."'";

								$resiv = odbc_do( $conexUnix, $selectiv );
								$drodetfac = odbc_result($resiv,1);
								$drodetart = odbc_result($resiv,2);

								//----------------------------------

								//----------------------------------

								$selectfacar = "   SELECT cardetfac
													 FROM FACARDET
													WHERE cardetfue = '".$fuente."'
													  AND cardetdoc = '".odbc_result($resu,1)."'
													  AND cardetite = '".$drodetlinea."'";

								$resfacar = odbc_do( $conexUnix, $selectfacar );
								$cardetfac = odbc_result($resfacar,1);


								$linea = $drodetlinea;
								if($linea =='')
								{
									$estaarticulo = false;
								}

							}
							if ($linea=='')
							{
								$html3.= "<td>no hay articulo</td>";
							}
							else
							{

								$selectnombre ="SELECT Artcom FROM  ".$wbasedato_mov."_000026  WHERE Artcod = '".$drodetart."' ";
								$resselec = mysql_query($selectnombre,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar varios ): ".$selectnombre." - ".mysql_error());
								$nombre = '';
								if($rowres = mysql_fetch_array($resselec))
								{
								  $nombre = $rowres['Artcom'];
								}


								$html3.= "<td nowrap='nowrap'>".$drodetart."-".utf8_encode($nombre)."</td>";
							}

							$html3.= "<td>".odbc_result($resu,1)."-".$linea."</td>";
							$html3.="<td>".$cardetfac."</td>";
							$html3.= "<td>".$drodetfac."</td>";



							$condicion = true;
							if(($cardetfac == $drodetfac  &&  $drodetfac == $row['Tcarfac'])  )
							{

								$html4= "<td>Iguales</td></tr>";
							}
							else
							{
								$condicion = false;
								$contador++;
								$html4= "<td><font  color='red'>Diferente</font></td></tr>";
							}
							if(!$estaarticulo)
							{
								$html4= "<td><font  color='red'>No existe el articulo en unix</font></td></tr>";
								if ($condicion == false)
								{
									$html4= "<td><font  color='red'>Diferente <br> No existe el articulo en unix</font></td></tr>";

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
							$sqlval			 = "  SELECT Logdoc,Loglin,Logaor,Logare
													FROM ".$wbasedato_mov."_000158
												   WHERE Logdoc = '".$row['Tcardoi']."'
													 AND Logaor = '".$row['Tcarprocod']."'";

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


								/*
								Acontinuacion se hace una validacion de como quedo los registros en
								FACARDET e IVDRODET y se mira si corresponde a lo que esta en la tabla
								cliame_000106 si esto es igual se cambia el estado de actualizado en unix
								en la tabla cliame_000106
								*/


								// se selecciona el estado del registro en ivdrodet facturable o no facturable
								$selectiv   = " SELECT drodetfac, drodetart
												  FROM IVDRODET
												 WHERE drodetfue = '".$fuente."'
												   AND drodetdoc = '".odbc_result($resu,1)."'
												   AND drodetite = '".$rowval['Loglin']."'";

								$resiv = odbc_do( $conexUnix, $selectiv );
								$drodetfac = odbc_result($resiv,1);
								$drodetart = odbc_result($resiv,2);


								//$auxprocedimientos = $auxprocedimientos."<br>".$drodetart."-".$rowval['Logare'];

								if($drodetart == $rowval['Logare'] )
								{

									$selectfacar = "   SELECT cardetfac
														FROM FACARDET
														WHERE cardetfue = '".$fuente."'
														AND cardetdoc = '".odbc_result($resu,1)."'
														AND cardetite = '".$rowval['Loglin']."'";

									$resfacar  = odbc_do( $conexUnix, $selectfacar );
									$cardetfac ="";
									$cardetfac = odbc_result($resfacar,1);
									$linea =  $rowval['Loglin'];
								}
								else
								{

									//$html3.= "<td>SI</td>";
									$selectiv   = " SELECT drodetite
													  FROM IVDRODET
													 WHERE drodetfue = '".$fuente."'
													   AND drodetdoc = '".odbc_result($resu,1)."'
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
													   AND drodetdoc = '".odbc_result($resu,1)."'
													   AND drodetite = '".$drodetlinea."'";

									$resiv = odbc_do( $conexUnix, $selectiv );
									$drodetfac = odbc_result($resiv,1);
									$drodetart = odbc_result($resiv,2);

									$selectfacar = "   SELECT cardetfac
														 FROM FACARDET
														WHERE cardetfue = '".$fuente."'
														  AND cardetdoc = '".odbc_result($resu,1)."'
														  AND cardetite = '".$drodetlinea."'";

									$resfacar  = odbc_do( $conexUnix, $selectfacar );
									$cardetfac ="";
									$cardetfac = odbc_result($resfacar,1);

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
									$selectnombre ="SELECT Artcom FROM  ".$wbasedato_mov."_000026  WHERE Artcod = '".$rowval['Logare']."' ";
									$resselec = mysql_query($selectnombre,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar varios ): ".$selectnombre." - ".mysql_error());
									$nombre = '';
									if($rowres = mysql_fetch_array($resselec))
									{
									  $nombre = $rowres['Artcom'];
									}

								}

								$auxprolin = $auxprolin."<br>".$rowval['Logare'] ."-".utf8_encode($nombre);


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
							$html3.= "<td>".$cardetfacaux."</td>";

							$html3.= "<td>".$drodetfacaux."</td>";
							$condicion = true;
							if ($bandera == false )
							{
								$condicion = false;
								$contador++;
								$html3.= "<td><font  color='red'>Diferente</font></td>";
							}
							else
							{
								$html3.= "<td>iguales</td>";
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
						$html.= "<tr class='".$wcf." malo find' >".$html3."<td>-</td><td>-</td><td>-</td><td>-</td><td><font  color='red'>Diferente<br>no esta integrado</font></td>";
					}



				}
				else
				{
					$html.= "<td>no esta en unix</td></tr>";

				}

			}
			else
			{
				$html.= "<td>no conexion</td>";
			}
		}
		else
		{
			$html.= "<tr class='".$wcf." malo find'>".$html3."<td>no estado</td>";
			$html.= "<td>".$row['Tcardoi']."</td>";
			$html.= "<td>".$row['Tcarlin']."</td><tr>";
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
	// $wemp_pmla = "01";


	// $wemp_pmla = "01";



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
	$wactualiz = "2021-11-19";

	//Variable para determinar la empresa
	// if(!isset($wemp_pmla))
	// {
	// 	$wemp_pmla = '01';
	// }

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


	?>
    </body>
</html>

