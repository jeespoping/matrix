<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Tarjeta de dispositivo medico implantable
 * Fecha		:	2015-10-04
 * Por			:	Felipe Alvarez Sanchez
 * Descripcion	:
 * Condiciones  :
 *********************************************************************************************************

 Actualizaciones:

 Agosto 8 de 2018 	Jessica Madrid  - En la función pintarPorHistoriaIngresoFiltros() se corrige el query que obtiene el lote en 
									  cliame_000240 ya que si la variable $wturno estaba vacía (cuando se imprime desde HCE) mostraba 
									  todos los lotes de ese insumo para los pacientes sin turno.
 Junio 05 de 2018 	Edwin MG		- Se modifica la impresión de tarjetas implantables para cco diferentes de cirugía y así mostrar una tarjeta por cada día de dispensación
 Mayo 16 de 2018 	Edwin MG		- Al imprimir la tarjeta, en la dirección del fabricante se le agrega htmlentities para que los 
									  datos se envíen correctamente al recibir peticiones ajax
 Mayo 10 de 2018 	Jessica Madrid	- Se crea el campo Artfab en movhos_000026 con el código del fabricante y se modifica la función 
									  consultarDatosFabricante() para evitar consultar la tabla cliame_000006 (proveedores) ya que un 
									  proveedor puede tener varios fabricantes y de esta forma solo habría relación uno a uno. 
 Marzo 26 de 2018 Edwin MG: 		- Se corrige para que muestre bien los lotes
 Marzo 06 de 2018 Edwin MG: 		- Se hacen cambio varios para:
									  * Mostrar los dispositivos implantables dispensados en pisos
									  * Se crea la funcion pintarPorHistoriaIngresoFiltros para obtener los datos necesarios para 
									    imprimir los datos correspondientes del turno de cirugía o el paciente
									  * Se crea funcion generarTarjetaPorDatos que imprime la tarjeta correspondiente
									- Se hac
 Febrero 16 de 2018 Jessica Madrid: - Se agregan algunos campos a la impresión 
									- Se unifica la impresión HCE y el reporte en una sola función 
 Enero 25 de 2016 Jessica Madrid: Se agrega funcion para pintar la tarjeta por historia e ingreso, además 
								  funcion que retorna un html para la construccion del pdf de impresion de  
								  formularios HCE y reportes (solimp.php y HCE_Impresion.php)
 Agosto 13 2015 Juan C. Hdez : Se modifica la palabra medicamento por Dispositivo en la impresión

 **********************************************************************************************************/

$wactualiz = "2018-08-08";

if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');


if( isset($consultaAjax) == false ){

?>
	<html>
	<head>
	<title>Generar Tarjeta</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<!--<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>-->
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<style>

		.tborder
		{
			/*border: solid black;*/
		}
		.visibilidad
		{
			display:none;
		}



	</style>
	<script>


	</script>
<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {

		$('input#buscador_pacientes').quicksearch('#tabla_pacientes .find');

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

		activarSeleccionadorFecha();


	});

	function activarSeleccionadorFecha()
	{
		$("#fechas_cirugias").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(dateText, inst) {

				 var date = $(this).val();
				 actualizarlista(date);
			}


		});
	}

	function actualizarlista(date)
	{

		$('#fechas_cirugias').val(date);
		var wemp_pmla = $("#wemp_pmla").val();
		$('#buscador_pacientes').val('');
		$.post('tarjetaDispositivosImplantables.php',
		{
			consultaAjax: '',
			wemp_pmla: wemp_pmla,
			action: "vistaInicial",
			wfecha:  date


		} ,
		function(data)
		{
			$("#ppal").html(data);

			$('input#buscador_pacientes').quicksearch('#tabla_pacientes .find');
			$("#div1").show();
			$("#tarjeta_dispositivos").hide();

		});



	}

	function generartarjeta(wturno,wfecha,whistoria,wingreso)
	{

		var wemp_pmla = $("#wemp_pmla").val();
		//alert(wturno);
		$.post('tarjetaDispositivosImplantables.php',
		{
			consultaAjax: '',
			   wemp_pmla: wemp_pmla,
			      action: "generartarjeta",
			      wturno: wturno,
			      wfecha: wfecha,
			   whistoria: whistoria,
			    wingreso: wingreso,
			      wfecha: $("#fechas_cirugias").val(),


		} ,
		function(data)
		{
			//alert(data);
			$("#tarjeta_dispositivos").html(data);
			$("#div1").hide();
			$("#tarjeta_dispositivos").show();
		});


	}

	function imprimir()
	{

		var imprimir = $("#imprimir").html();
		// var printWindow = window.open('', '', 'height=1000,width=1500');
		// printWindow.document.write('<html><head><title></title>');
		// printWindow.document.write('</head><style>.fila1 {background-color: #c3d9ff; color: #000000;font-size: 10pt;}</style><body >');
		// printWindow.document.write('<div style="font-size: 5pt" >'+imprimir+'</div>');
		// printWindow.document.write('</body></html>');
		// printWindow.document.close();
		// printWindow.print();

		var contenido = "<html><body onload='window.print();window.close();'>"
					+"<style type='text/css'> "
						+""
								+".fila1 {"
										+"color: #000000;"
										+"font-size: 8pt;"
										+"border-left: 1px #000000 solid;"
										+"border-top: 1px #000000 solid;"
									+"}"
								+".fila2 {"
										+"color: #000000;"
										+"font-size: 8pt;"
										+"border-left: 1px #000000 solid;"
										+"border-top: 1px #000000 solid;"
										+"}"
								+".encabezadoTabla {"
										+"color: #000000;"
										+"font-size: 9pt;"
										+"font-weight:bold;"
										+"}"
								+".mensaje{"
										+"color: #676767;"
										+"font-family: verdana;"
										+"font-weight:bold;"
										+"font-size: 8pt;"
										+"}";
						contenido +=""
					+"</style>";
				contenido = contenido +imprimir+ "</body></html>";
				var ventana = window.open('', '', '');
				ventana.document.open();
				ventana.document.write(contenido);
				ventana.document.close();
				ventana.print();


	}


</script>
</head>

<?php

}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");
include_once("movhos/movhos.inc.php");



$conex = obtenerConexionBD("matrix");
$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbasedatoscliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
$wbasedatocirugia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');
$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));



	function generarTarjetaPorDatos($wemp_pmla,$datosTarjeta, $fecha,$impresionHCE="")
	{
		$html = "";
		
		$logo = "../../images/medical/root/logo_clinica.jpg";
		$styleTable = "";
		$botones = "<table align='center'>
						<tr>
							<td><input type='button' onclick='imprimir()' value='Imprimir'></td>
							<td><input type='button' onclick='actualizarlista(\"".$fecha."\")' value='Retornar'></td>
						<tr>
					</table>";
					
		if($impresionHCE=="on")
		{
			$httpHost = $_SERVER['HTTP_HOST'];
			$logo = "http://".$httpHost."/matrix/images/medical/root/logo_clinica.jpg";
			
			$styleTable = "style=font-size:8pt";
			$botones = "";
		}
		
		$html.= "<div id='imprimir'>";
		
		$saltaPagina = false;
		foreach( $datosTarjeta as $key => $tarjeta ){
			
			if( $saltaPagina )
				$html .= "<br><div style='page-break-after: always'></div>";
			
			$saltaPagina = true;
			
			$html.= "
					<table width='98%'  cellspacing='0' border='1' bordercolor='#999999' ".$styleTable.">
					<tr>
						<td class='tborder' colspan='2' width='30%'align='center'><img  width='220' heigth='90' src='".$logo."'></td>
						<td class='tborder' colspan='4' width='40%' align='center'><b>TARJETA DE DISPOSITIVO MEDICO <br> IMPLANTABLE</b></td>
						<td class='tborder' colspan='2' rowspan='7' width='30%' align='center'>(Espacio para etiqueta del paciente)</td>
					</tr>
					<tr >
						<td class='tborder fila1' colspan='2'><b>FECHA:</b></td><td colspan='4'  class='tborder fila2'>".$tarjeta['fecha']."</td>
					</tr>
					<tr>
						<td class='tborder fila1' colspan='2'><b>UNIDAD:</b></td><td colspan='4'  class='tborder fila2'>".$tarjeta['centroDeCostos']."</td>
					</tr>
					<tr>
						<td class='tborder fila1' colspan='2'><b>NOMBRE DEL IMPLANTE:</b></td><td colspan='4'  class='tborder fila2'>".$tarjeta['cirugia']."</td>
					</tr>
					<tr >
						<td class='tborder fila1' colspan='2' ><b>NOMBRE Y APELLIDOS DEL PACIENTE: </b></td><td colspan='4'  class='tborder fila2'>".htmlentities( $tarjeta['nombre'] )."</td>
					</tr>
					<tr >
						<td class='tborder fila1' colspan='2'><b>IDENTIFICACION:</b></td><td   class='tborder fila2' colspan='2'>".$tarjeta['documento']."</td><td class='tborder fila2' ><b>HISTORIA: </b></td><td class='tborder fila2'>".$tarjeta['historia']."-".$tarjeta[ 'ingreso' ]."</td>
					</tr>
					<tr>
						<td class='tborder fila1' colspan='2'><b>NOMBRE DEL MEDICO:</b></td><td colspan='4'  class='tborder fila2'>".htmlentities( $tarjeta['medico'] )."</td>
					</tr>
					<tr>
						<td class='tborder fila1' colspan='8' align='center'><b>DISPOSITIVOS IMPLANTABLES</b></td>
					</tr>";
					$i=0;
				

			$html.="<tr class='fila1' align='center'>
						<td colspan='2'><b>DESRIPCI&Oacute;N Y REFERENCIA</b></td>
						<td colspan='1'><b>CANT</b></td>
						<td colspan='1'  ><b>LOTE(S)</b></td>
						<td colspan='1'><b>REGISTRO INVIMA</b></td>
						<td colspan='1'><b>MARCA</b></td>
						<td colspan='1'><b>FABRICANTE/<br>DISTRIBUIDOR</b></td>
						<td colspan='1'><b>DIRECCI&Oacute;N FABRICANTE</b></td>
					</tr>";

			foreach( $tarjeta[ 'medicamentos' ] as $key => $articulo )
			{
				
				$html.="<tr>
							<td class='tborder fila2' colspan='2' >".htmlentities( $articulo['nombreComercial'] )."</td>
							<td  class='tborder fila2' colspan='1' align='center'>".$articulo['cantidad']."</td>
							<td  class='tborder fila2' colspan='1'>".$articulo['lotes']."</td>
							<td class='tborder fila2' colspan='1' >".$articulo['registroInvima']."</td>
							<td class='tborder fila2' colspan='1' >".$articulo['marca']."</td>
							<td class='tborder fila2' colspan='1' >".$articulo['fabricante']."</td>
							<td class='tborder fila2' colspan='1' >".htmlentities( $articulo['direccionFabricante'] )."</td>
						</tr>";

			}
			
			$direccionTelefono = "";
			$arrayEmpresa = consultarDireccionEmpresa($wemp_pmla);
			if(count($arrayEmpresa)>0)
			{
				$direccionTelefono = $arrayEmpresa['direccion']." TEL: ".$arrayEmpresa['telefono'];
			}
			
			$html.="<tr class='fila1'><td colspan='8'>&nbsp</td></tr><tr>
					<td class='tborder fila2' colspan='8' width='100%'><br><b>NOMBRE Y FIRMA DE QUIEN RECIBE: ___________________________________________________________________<br><br></b></td>

					</tr>
					<tr class='fila1'><td colspan='8' align='center'>".$direccionTelefono."</td></tr><tr>
					</table>";
					// </div>
					// <br>
					// ".$botones."
					// ";
		}

		$html .= "</div>
					<br>
					".$botones."
					";
		
		return $html;
	}



	function pintarPorHistoriaIngresoFiltros( $conex,$wemp_pmla, $whistoria, $wingreso, $fecha, $wturno, $impresionHCE )
	{
		
		//Este arraya contiene todos los datos a imprimir
		$dataTarjetas = array();
		
		$wbasedatocirugia 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tcx' );
		$wbasedatoscliame 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'facturacion' );
		$wbasedatomovhos 	= consultarAliasPorAplicacion( $conex, $wemp_pmla, 'movhos' );

		$queryTurnos ="";

		//Si se ha ingresado una historia o un ingreso
		//El querie se realizará por historia e ingreso
		if( !empty( $whistoria ) && !empty( $wingreso ) && empty( $fecha ) ){
			$filtro = " Turhis = '".$whistoria."' AND Turnin = '".$wingreso."' ";
		}
		
		//Si se manda un turno el querie buscara por el turno
		if( !empty( $wturno ) && $wturno*1 > 0 ){
			$filtro = "Turtur = '".$wturno."'";
		}
		
		
		if( !empty( $filtro ) ){
			
		
			$queryTurnos = "SELECT  Turnom,Turdoc,Turmed,Turfec,Cconom,Turhis,Turnin, Turtur
							 FROM ".$wbasedatocirugia."_000011, ".$wbasedatocirugia."_000012,  ".$wbasedatomovhos."_000011
							WHERE  ".$filtro."
							  AND  Turqui =  Quicod
							  AND  Quicco =  Ccocod ";
		
			
			$res = mysql_query($queryTurnos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryTurnos." - ".mysql_error());
			$num = mysql_num_rows($res);
			
			$html = "";		
			
			if($num > 0)
			{
				while($row = mysql_fetch_array($res))
				{				
					$Select_estado_mercado = "SELECT Mpaefa ,Mpaela ,Mpacfa ,  Mpacal, Arteim, Mpacan, Mpadev, Artreg, Artcom, Artcod, Artfab
												FROM ".$wbasedatoscliame."_000207  ,  ".$wbasedatomovhos."_000026
											   WHERE Mpatur = '".$row['Turtur']."'
												 AND Mpacom = Artcod
												 AND Mpacan > Mpadev";
					$res_estado = 	mysql_query($Select_estado_mercado,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$Select_estado_mercado." - ".mysql_error());

					$sumamedicamentos	= 0;
					$sumacerrados		= 0;
					$medicamentos		= array();
					
					//Indica si hay articulo implantables
					$medicamentos_implantables  = 'off';
					while($row_estado = mysql_fetch_array($res_estado))
					{
						
						//Si es un articulo implantables
						if($row_estado['Arteim']=='on')
						{
							$fabricante = "";
							$direccionFabricante = "";
							$arrayFabricante = consultarDatosFabricante($row_estado['Artfab']);
							if(count($arrayFabricante)>0)
							{
								$fabricante 		 = $arrayFabricante['descripcion'];
								$direccionFabricante = $arrayFabricante['direccion'];
							}
							
							$select_lote = "SELECT Lotlot
											  FROM ".$wbasedatoscliame."_000240
											 WHERE Lottur = '".$row['Turtur']."'
											   AND Lotins = '".$row_estado['Artcod']."'
											   AND Lotcan > Lotdev ";

							$res_lote = 	mysql_query($select_lote,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$select_lote." - ".mysql_error());
							$lote = ' ';
							while($row_lote = mysql_fetch_array($res_lote))
							{
								$lote .= $row_lote['Lotlot'].",";
							}
							$lote = substr ($lote, 0, strlen($lote) - 1);
							
							$medicamentos_implantables  = 'on';
							
							
							$medicamentos[ $row_estado['Artcod'] ] = array(
																			'codigo' 	  		  => $row_estado['Artcod'],
																			'nombreComercial' 	  => $row_estado['Artcom'],
																			'cantidad' 	  		  => (($row_estado['Mpacan'] - $row_estado['Mpadev']) * 1),
																			'lotes' 	 		  => $lote,
																			'registroInvima'	  => $row_estado['Artreg'],
																			'fabricante' 		  => $fabricante,
																			'marca'		 		  => $fabricante,
																			'direccionFabricante' => $direccionFabricante,
																		);
						}

						$sumamedicamentos = $sumamedicamentos + ($row_estado['Mpaefa']*1);
						$sumamedicamentos = $sumamedicamentos + ($row_estado['Mpaela']*1);
						$sumacerrados 	  = $sumacerrados + ($row_estado['Mpacfa']*1);
						$sumacerrados 	  = $sumacerrados + ($row_estado['Mpacal']*1);
					}

					if($medicamentos_implantables =='on')
					{
						//Reglar para verificar si el mercado está cerrado o no
						if(($sumamedicamentos == $sumacerrados) and ($sumamedicamentos !=0 and $sumacerrados !=0) )
						{
							//Array con la información para generar una o varias tarjetas
							$tarjeta = array(
								'nombre' 		=> $row['Turnom'],
								'documento' 	=> $row['Turdoc'],
								'medico'		=> substr($row['Turmed'],0,-1),
								'fecha'			=> $row['Turfec'],
								'centroDeCostos'=> $row['Cconom'],
								'historia'		=> $row['Turhis'],
								'ingreso'		=> $row['Turnin'],
								'cirugia' 		=> consultarCirugiaMayor( $row['Turhis'], $row['Turnin'], $row['Turtur'] ),
								'medicamentos' 	=> $medicamentos,
							);
							
							//Agrego una tarjeta a imprimir
							$dataTarjetas[] = $tarjeta;
						}
					}
				}
			}
		}
		
		
		
		
		
		//Datos de impresion para articulos implantables cargados desde PDA o gestion de insumos
		if( !empty( $whistoria ) && !empty( $wingreso ) ){
			
			if( $fecha ){
				$validarFecha = " AND Lotfmo = '".$fecha."' ";
			}
			
			$queryLotes = " SELECT Lotmed, Lotcco, Cconom, Lotfmo, Lothis, Loting, Lotins, Artcom, Artreg, Lotlot, SUM( Lotcan ) as Lotcan, SUM( Lotdev ) as Lotdev, Artfab
							   FROM ".$wbasedatoscliame."_000240, ".$wbasedatomovhos."_000026 , ".$wbasedatomovhos."_000011 
							  WHERE Lothis = '".$whistoria."' 
							    AND Loting = '".$wingreso."' 
							    AND Lotdpi = 'on' 
								    ".$validarFecha." 
								AND Artcod = Lotins
								AND Ccocod = Lotcco
						   GROUP BY Lotmed, Lotcco, Lotfmo, Lothis, Loting, Lotins, Lotlot 
						     HAVING SUM(Lotcan) - SUM(Lotdev) > 0
						   ORDER BY Lotmed, Lotcco, Lothis, Loting, Loting, Lotlot ";
			
			$res 		  = mysql_query($queryLotes,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryLotes." - ".mysql_error());
			$num 		  = mysql_num_rows($res);
			$medicamentos = array();
						 
			if( $num > 0 ){
				
				$rows = mysql_fetch_array( $res );
				
				$ccoAnt = $rows['Lotcco'];
				$medicotAnt = $rows['Lotmed'];
				$fechaAnt	= $rows['Lotfmo'];
				
				do{
					
					$medico 	= $rows['Lotmed'];
					$nombreCco 	= $rows['Cconom'];
					$fechaFmo	= $rows['Lotfmo'];
					
					$fabricante = "";
					$direccionFabricante = "";
					$arrayFabricante = consultarDatosFabricante($rows['Artfab']);
					if(count($arrayFabricante)>0)
					{
						$fabricante 		 = $arrayFabricante['descripcion'];
						$direccionFabricante = $arrayFabricante['direccion'];
					}
					
					if( !isset( $medicamentos[$rows['Lotins']] ) ){
						
						$medicamentos[ $rows['Lotins'] ] = array(
																'codigo' 	  		  => $rows['Lotins'],
																'nombreComercial' 	  => $rows['Artcom'],
																'cantidad' 	  		  => (($rows['Lotcan'] - $rows['Lotdev']) * 1),
																'lotes' 	 		  => $rows['Lotlot'],
																'registroInvima'	  => $rows['Artreg'],
																'fabricante' 		  => $fabricante,
																'marca'		 		  => $fabricante,
																'direccionFabricante' => $direccionFabricante,
															);
					}
					else{
						$medicamentos[ $rows['Lotins'] ][ 'cantidad' ] += $rows['Lotcan'] - $rows['Lotdev'];
						$medicamentos[ $rows['Lotins'] ][ 'lotes' ] .= ",".$rows['Lotlot'];
					}
					
					$rows = mysql_fetch_array( $res );
					
					if( !$rows || $ccoAnt != $rows['Lotcco'] || $medicotAnt != $rows['Lotmed'] || $fechaAnt	!= $rows['Lotfmo'] ){
						
						//Consulto información del paciente (la función se encuentra en comun.php y devuelve un objeto)
						$paciente = consultarInfoPacientePorHistoria( $conex, $whistoria, $wemp_pmla );
						
						$tarjeta = array(
							'nombre' 		=> $paciente->nombre1." ".$paciente->nombre2." ".$paciente->apellido1." ".$pacinete->apellido2,
							'documento' 	=> $paciente->documentoIdentidad,
							'medico'		=> $medico,
							'fecha'			=> $fechaFmo,
							'centroDeCostos'=> $nombreCco,
							'historia'		=> $whistoria,
							'ingreso'		=> $wingreso,
							'cirugia' 		=> "",
							'medicamentos' 	=> $medicamentos,
						);
						
						$dataTarjetas[] = $tarjeta;
						
						
						if( $rows ){
							$ccoAnt 	= $rows['Lotcco'];
							$medicotAnt = $rows['Lotmed'];
							$fechaAnt	= $rows['Lotfmo'];
							
							$medicamentos = array();
						}
						
					}
				}
				while( $rows );
				
			}
		}
		
		$html = generarTarjetaPorDatos( $wemp_pmla ,$dataTarjetas, $fecha, $impresionHCE );
		
		return $html;
	}
	
	



	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial($wfecha){

		global $wemp_pmla;
		global $wactualiz, $conex;


		$wbasedatomedico = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$wbasedatoscliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
		$wbasedatocirugia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tcx');



		$selectpacientes= "SELECT Turfec, Turhis, Turnin,Turnom,Turtur,Turhin
							 FROM ".$wbasedatocirugia."_000011
							WHERE Turfec = '".$wfecha."' ";
							
		$selectpacientes= "SELECT Turfec, Turhis, Turnin,Turnom,Turtur,Turhin, 'off' as Lotdpi
							 FROM ".$wbasedatocirugia."_000011
							WHERE Turfec = '".$wfecha."' 
							UNION
						   SELECT a. Fecha_data as Turfec, Lothis as Turhis, Loting as Turnin, CONCAT( Pacno1, ' ', Pacno2, ' ', Pacap1, Pacap2 ) as Turnom, '' as Turtur, Min( a.Hora_data ) as Turhin, Lotdpi
						     FROM ".$wbasedatoscliame."_000240 a, root_000036 b, root_000037 c
							WHERE a.Fecha_data = '".$wfecha."' 
							  AND Lotdpi = 'on'
							  AND orihis = lothis
							  AND Pactid = oritid
							  AND Pacced = Oriced
							  AND Oriori = '".$wemp_pmla."'
						 GROUP BY 1, 2, 3, 4, 5
						   HAVING SUM( Lotcan )- SUM(Lotdev) > 0";

		$html= "<div id='vista_inicial'>
				<table width='70%' align='center' id='tabla_pacientes'>
					<tr class='encabezadoTabla'>
						<td>Num</td>
						<td>Fecha</td>
						<td>Hora Inicio</td>
						<td>Historia</td>
						<td>Ingreso</td>
						<td>Turno</td>
						<td>Nombre Paciente</td>
						<td width='20%'>Estado</td>
					</tr>";
		$res = 	mysql_query($selectpacientes,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$selectmedicamentos." - ".mysql_error());
		$i=0;
		$html_imprimir='';
		$html_no_imprir='';
		while($row = mysql_fetch_array($res))
		{

			if (($i%2)==0)
				$wcf="fila1";  // color de fondo de la fila
			else
				$wcf="fila2"; // color de fondo de la fila

			if( $row['Lotdpi'] != 'on' ){

				$Select_estado_mercado = "SELECT Mpaefa ,Mpaela ,Mpacfa ,  Mpacal,Arteim
											FROM ".$wbasedatoscliame."_000207  ,  ".$wbasedatomedico."_000026
										   WHERE Mpatur = '".$row['Turtur']."'
											 AND Mpacom = Artcod
											 AND Mpacan > Mpadev";
				$res_estado = 	mysql_query($Select_estado_mercado,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$Select_estado_mercado." - ".mysql_error());

				$sumamedicamentos	=0;
				$sumacerrados		=0;
				$medicamentos_implantables  = 'off';
				while($row_estado = mysql_fetch_array($res_estado))
				{
					if($row_estado['Arteim']=='on')
					{
						$medicamentos_implantables  = 'on';
					}

					$sumamedicamentos = $sumamedicamentos + ($row_estado['Mpaefa']*1);
					$sumamedicamentos = $sumamedicamentos + ($row_estado['Mpaela']*1);
					$sumacerrados 	  = $sumacerrados + ($row_estado['Mpacfa']*1);
					$sumacerrados 	  = $sumacerrados + ($row_estado['Mpacal']*1);
				}
			}
			else{
				//si fue cargado por PDA se asume que son implantables
				//Además no hay mercado para los articulos cargados por PDA
				$medicamentos_implantables  = 'on';
				$sumamedicamentos = 1;
				$sumacerrados = 1;
			}

			if($medicamentos_implantables =='on')
			{
				$i++;
				$html.="<tr class='".$wcf." find'   >
						<td>".$i."</td>
						<td>".$row['Turfec']."</td>
						<td>".$row['Turhin']."</td>
						<td>".$row['Turhis']."</td>
						<td>".$row['Turnin']."</td>
						<td>".$row['Turtur']."</td>
						<td>".$row['Turnom']."</td>
						<td align='center'>";



					if(($sumamedicamentos == $sumacerrados) and ($sumamedicamentos !=0 and $sumacerrados !=0) )
					{
						if( !empty( $row['Turtur'] ) ){
							$wturno 	= $row['Turtur'];
							$whistoria 	= '';
							$wingreso 	= '';
						}
						else{
							$wturno 		= '';
							$whistoria 	= $row['Turhis'];
							$wingreso 	= $row['Turnin'];
						}
						
						$html.="<div style='cursor : pointer' onclick='generartarjeta(\"".$wturno."\",\"".$wfecha."\",\"".$whistoria."\",\"".$wingreso."\")'><font color='green'>Imprimir</font><div></td>";
						// $html.="<div style='cursor : pointer' onclick='generartarjeta(\"".$row['Turtur']."\")'><font color='green'>Imprimir</font><div></td>";
					}
					else
					{
						$html.="<div ><font color='red'>Mercado sin cerrar</font><div></td>";
					}
				$html.="</tr>";
			}


		}

		$html.= "</table>
					</div>
					<br><br>
				<table align='center'><tr><td><input type='button' value='Cerrar Ventana' onclick='window.close();'></td></tr></table>";


		echo $html;

	}
	
	
	
	
	function imprimirTarjetaHCE($conex,$wemp_pmla,$historia,$ingreso)
	{
		// $html = pintarPorHistoriaIngreso($wemp_pmla,$historia,$ingreso,"on");
		$html = pintarPorHistoriaIngresoFiltros($conex, $wemp_pmla,$historia,$ingreso, "", "", "on");
		return $html;
	}

	function consultarCirugiaMayor($historia,$ingreso,$wturno)
	{
		global $conex;
		global $wbasedatoscliame;
		
		$queryCirugia = " SELECT Enlpro,Pronom,a.id 
							FROM ".$wbasedatoscliame."_000199 a,".$wbasedatoscliame."_000103 b
						   WHERE Enlhis='".$historia."' 
							 AND Enling='".$ingreso."' 
							 AND Enltur='".$wturno."' 
							 AND Procod=Enlpro
						ORDER BY a.id 
						   LIMIT 1;";
						 
		$resCirugia= mysql_query($queryCirugia,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryCirugia." - ".mysql_error());
		$numCirugia = mysql_num_rows($resCirugia);
		
		$cirugia = "";
		if($numCirugia > 0)
		{
			$rowsCirugia = mysql_fetch_array($resCirugia);
			$cirugia = $rowsCirugia['Pronom'];
		}	
		
		return $cirugia;
	}
	
	function consultarDatosFabricante($codigoFabricante)
	{
		global $conex;
		global $wbasedatoscliame;
		
		$arrayFabricante = array();
		// if($codigoFabricante!="")
		if($codigoFabricante!="" && $codigoFabricante!="NO APLICA")
		{
			$queryFabricante = "SELECT Fabdes,Fabdir 
								  FROM ".$wbasedatoscliame."_000248 
								 WHERE Fabcod='".$codigoFabricante."' 
								   AND Fabest='on';";
							 
			$resFabricante = mysql_query($queryFabricante,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryFabricante." - ".mysql_error());
			$numFabricante = mysql_num_rows($resFabricante);
			
			
			if($numFabricante > 0)
			{
				$rowsFabricante = mysql_fetch_array($resFabricante);
				
				$arrayFabricante['descripcion'] = $rowsFabricante['Fabdes'];
				$arrayFabricante['direccion'] = $rowsFabricante['Fabdir'];
			}
		}
		
		return $arrayFabricante;
	}
	
	function consultarDireccionEmpresa($wemp_pmla)
	{
		global $conex;
		
		$queryEmpresa = " SELECT Empdir,Emptel 
							FROM root_000050 
						   WHERE Empcod='".$wemp_pmla."';";
						 
		$resEmpresa = mysql_query($queryEmpresa,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryEmpresa." - ".mysql_error());
		$numEmpresa = mysql_num_rows($resEmpresa);
		
		$arrayEmpresa = array();
		if($numEmpresa > 0)
		{
			$rowsEmpresa = mysql_fetch_array($resEmpresa);
			
			$arrayEmpresa['direccion'] = $rowsEmpresa['Empdir'];
			$arrayEmpresa['telefono'] = $rowsEmpresa['Emptel'];
		}
		
		return $arrayEmpresa;
	}

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//

if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "generartarjeta"){
		$html = pintarPorHistoriaIngresoFiltros($conex, $_REQUEST['wemp_pmla'], $_REQUEST['whistoria'], $_REQUEST['wingreso'], $_REQUEST['wfecha'],$_REQUEST['wturno'],"");
		echo $html;
	}
	if( $action == "vistaInicial"){
		vistaInicial($_REQUEST['wfecha'],$_REQUEST['wemp_pmla']);
	}
	if( $action == "consultarHtmlImpresionHCE"){
		$data = array();
		$data['html'] = imprimirTarjetaHCE($conex,$wemp_pmla,$historia,$ingreso);
		echo json_encode($data);
		return;
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************
else
{
	

	
	
?>
 <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
			
				$wfecha	= date("Y-m-d");
				echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
				encabezado("Tarjeta de dispositivo medico implantable", $wactualiz, "clinica");
					 
				if(isset($whistoria) && isset($wingreso))
				{
					// $turnos = pintarPorHistoriaIngreso($wemp_pmla,$whistoria,$wingreso,"off");
					$turnos = pintarPorHistoriaIngresoFiltros($conex, $wemp_pmla,$whistoria,$wingreso, "", "", "on");
					echo $turnos;
				}
				else
				{
					echo "<div id='div1'>";
					echo"<table  width='70%' align='center'>
							<tr class='encabezadoTabla'><td>Buscar</td><td colspan='2'><input type='text' id='buscador_pacientes'></td><td>Fecha</td><td><input type='text' id='fechas_cirugias' value='".$wfecha."'></td></tr>
						</table>";
					echo "<div id='ppal'>";
								vistaInicial($wfecha, $wemp_pmla);
					echo  "</div>";
					echo "</div>";
					echo"<div id='tarjeta_dispositivos'>
						 </div>";
				}	 
			
}						
			?>

    </body>
</html>