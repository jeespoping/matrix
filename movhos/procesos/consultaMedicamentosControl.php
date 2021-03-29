<?php
include_once("conex.php");
/****************************************************************************************************************
 * 												FUNCIONES
 ****************************************************************************************************************/
 
function pintarDatos($conex, $wemp_pmla, $wbasedato){
	
	if( $_POST ){

		//Limpio los datos de busqueda
		$txArticulo 			= trim( $_POST['txArticulo'] );
		$txNombrePaciente 		= trim( $_POST['txNombrePaciente'] );
		$txHistoria 			= trim( $_POST['txHistoria'] );
		$txNroIdentificacion	= trim( $_POST['txNroIdentificacion'] );
		$txNroOrden 			= trim( $_POST['txNroOrden'] );
		$txFechaFinal 			= trim( $_POST['txFechaFinal'] );
		$txFechaInicial 		= trim( $_POST['txFechaInicial'] );

		//Esta bandera indica si hay filtros para buscar los medicamentos
		$buscarMedicamentos = false;

		//Inicializo los filtros en vacio
		$filtroNombre 			 = "";
		$filtroArticulo 		 = "";
		$filtroHistoria 		 = "";
		$filtroNroIdentificacion = "";
		$filtroNroOrden 		 = "";
		$filtroRangoFechas 		 = "";


		//Se hace el filtro de historia en caso de tenerlo
		if( !empty( $txNombrePaciente ) ){

			$nombrePaciente = "%".str_replace( " ", "%", $txNombrePaciente )."%";

			$filtroNombre = " AND CONCAT( Pacno1, ' ' , Pacno2, ' ' , Pacap1, ' ' , Pacap2, ' '  ) LIKE \"".$nombrePaciente."\" ";
			$buscarMedicamentos = true;
		}

		//Se hace el filtro de historia en caso de tenerlo
		if( !empty( $txArticulo ) ){

			list( $codArticulo, $nombreGenerico ) = explode( "-", $txArticulo );

			$filtroArticulo = " AND Ctrart = '".$codArticulo."' ";
			$buscarMedicamentos = true;
		}

		//Se hace el filtro de historia en caso de tenerlo
		if( !empty( $txHistoria ) ){
			$filtroHistoria = " AND Ecthis = '".$txHistoria."' ";
			$buscarMedicamentos = true;
		}

		//Creando filtro por Nro de identificación
		if( !empty( $txNroIdentificacion ) ){
			$filtroNroIdentificacion = " AND Oriced = '".$txNroIdentificacion."' ";
			$buscarMedicamentos = true;
		}

		//Creando filtro por Nro de identificación
		if( !empty( $txNroOrden ) ){
			$filtroNroOrden = " AND Ctrcon = '".$txNroOrden."' ";
			$buscarMedicamentos = true;
		}

		//Validando filtro por rango de fechas
		if( !empty( $txFechaFinal ) && !empty( $txFechaInicial ) ){

			$fecUnixInicial = strtotime( $txFechaInicial );
			$fecUnixFinal 	= strtotime( $txFechaFinal );

			if( $fecUnixInicial !== false && $fecUnixFinal !== false && $fecUnixFinal >= $fecUnixInicial ){
				$filtroRangoFechas = " AND Ctrfge BETWEEN '".$txFechaInicial."' AND '".$txFechaFinal."' ";
				$buscarMedicamentos = true;
			}
		}


		if( $buscarMedicamentos ){

			//Consulto los datos a ser mostrados
			$sql = "(SELECT
							Artgen, Artcom, Artfar, Perequ, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*, a.id as ctcid
					   FROM
							{$wbasedato}_000222 h, {$wbasedato}_000133 a, {$wbasedato}_000026 b, {$wbasedato}_000043 c, {$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g
					  WHERE Ecthis = Ctrhis
					    AND Ecting = Ctring
						AND artcod = ctrart
						AND percod = ctrper
						AND ubihis = ctrhis
						AND ubiing = ctring
						AND ccocod = ubisac
						AND orihis = ubihis
						AND oriing = ubiing
						AND oritid = pactid
						AND oriced = pacced
						AND oriori = '$wemp_pmla'
						AND ctrest = 'on'
						$filtroNombre
						$filtroArticulo
						$filtroHistoria
						$filtroNroIdentificacion
						$filtroNroOrden
						$filtroRangoFechas )
					UNION
					(SELECT
							Artgen, Artcom, '' as Artfar, Perequ, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*, a.id as ctcid
					   FROM
							{$wbasedato}_000222 h, {$wbasedato}_000133 a, cenpro_000002 b, {$wbasedato}_000043 c, {$wbasedato}_000018 d, {$wbasedato}_000011 e, root_000036 f, root_000037 g
					  WHERE Ecthis = Ctrhis
					    AND Ecting = Ctring
						AND artcod = ctrart
						AND percod = ctrper
						AND ubihis = ctrhis
						AND ubiing = ctring
						AND ccocod = ubisac
						AND orihis = ubihis
						AND oriing = ubiing
						AND oritid = pactid
						AND oriced = pacced
						AND ctrest = 'on'
						AND oriori = '$wemp_pmla'
						$filtroNombre
						$filtroArticulo
						$filtroHistoria
						$filtroNroIdentificacion
						$filtroNroOrden
						$filtroRangoFechas )
					ORDER BY
						ctrhis, ctring, ubisac, ubihac, ctrcon desc
					";
			// echo ".....<pre>$sql</pre>";

			echo "<br>";
			echo "<br>";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );

			$total = 0;
			$totalAImprimir = 0;
			if( $num > 0 ){

				for( $i = 0; $rows = mysql_fetch_array( $res );  ){

					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'hab' ] = $rows[ 'Ubihac' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'nom' ] = $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'tot' ]++;
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'his' ] = $rows[ 'Ctrhis' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'ing' ] = $rows[ 'Ctring' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'med' ][$rows[ 'ctcid' ]][ 'nombreComercial' ] = trim( $rows[ 'Artcom' ] );

					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'med' ][$rows[ 'ctcid' ]][ 'fechaGeneracion' ] = $rows[ 'Ctrfge' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'med' ][$rows[ 'ctcid' ]][ 'nroOrden' ] = $rows[ 'Ctrcon' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'med' ][$rows[ 'ctcid' ]][ 'ido' ] = $rows[ 'Ctrido' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'med' ][$rows[ 'ctcid' ]][ 'codigo' ] = $rows[ 'Ctrart' ];
					@$pacientes[ $rows[ 'Ctrhis' ]."-".$rows[ 'Ctring' ]  ][ 'med' ][$rows[ 'ctcid' ]][ 'imp' ] = $rows[ 'Ctrimp' ] == 'on' ? true: false;

					$cconom = $rows[ 'Cconom' ];
					$total++;
					$totalAImprimir++;
				}
			}
			// echo "<pre>"; var_dump($pacientes); echo "</pre>";
			// if( $total > 0 ){
			if( count($pacientes) > 0 ){

				//creo una fila mas con la información del paciente que se quiere imprimir
				echo "<div>";

				echo "<table align='center'>";

				//Pintando encabezado de tabla
				echo "<tr class='encabezadotabla' align='center'>";
				echo "<td rowspan=2>Historia</td>";
				echo "<td rowspan=2>Nombre</td>";
				echo "<td colspan=3>Medicamento</td>";
				echo "<td rowspan=2>Imprimir</td>";
				echo "<td rowspan=2>Auditor&iacute;a</td>";
				echo "</tr>";
				
				echo "<tr class=encabezadotabla>";
				echo "<td>Fecha de<br>generaci&oacute;n</td>";
				echo "<td>Articulo</td>";
				echo "<td>Nro. Orden</td>";
				echo "</tr>";

				$k = 0;
				$j = 0;

				foreach( $pacientes as $keyPacientes => $hisPacientes ){

					$class2 = "fila".($j%2+1)."";

					echo "<tr class='$class2'>";

					// echo "<td align='center' rowspan=".$hisPacientes['tot'].">";
					echo "<td align='center' rowspan=".count( $hisPacientes[ 'med' ] ).">";
					echo $keyPacientes;
					echo "</td>";

					// echo "<td rowspan=".$hisPacientes['tot'].">";
					echo "<td rowspan=".count( $hisPacientes[ 'med' ] ).">";
					echo utf8_encode( $hisPacientes[ 'nom' ] );
					echo "</td>";
					
					$k = 0;
					foreach($hisPacientes[ 'med' ] as $key => $value){
						$class1 = "fila".($j%2+1)."";
						if( $k > 0 )
							echo "<tr class='$class1'>";
						echo "<td>";
						echo $value['fechaGeneracion'];
						echo "</td>";
						echo "<td width='350px'>";
						echo $value['nombreComercial'];
						echo "</td>";
						echo "<td style='text-align:center'>";
						echo $value[ 'nroOrden' ];
						echo "</td>";
						echo "<td align='center'>";
						// echo "<a href='impresionMedicamentosControl.php?wemp_pmla=$wemp_pmla&imprimir=on".( $value['imp'] ? "&reimprimir=on" : '' )."&historia={$hisPacientes[ 'his' ]}&id_registro={$key}' target='_blank'>Imprimir</a>";
						
						if( !$value['imp'] ){
							echo "<a href='impresionMedicamentosControl.php?wemp_pmla=$wemp_pmla&imprimir=on&historia={$hisPacientes[ 'his' ]}&id_registro={$key}' target='_blank' onClick='mostrarUrls( this );'>Imprimir</a>";
							echo "<a href='impresionMedicamentosControl.php?wemp_pmla=$wemp_pmla&imprimir=on&reimprimir=on&historia={$hisPacientes[ 'his' ]}&id_registro={$key}' target='_blank' style='display:none'>Imprimir</a>";
						}
						else{
							echo "<a href='impresionMedicamentosControl.php?wemp_pmla=$wemp_pmla&imprimir=on&reimprimir=on&historia={$hisPacientes[ 'his' ]}&id_registro={$key}' target='_blank'>Imprimir</a>";
						}
						
						echo "</td>";
						echo "<td align='center'><a href='#' onClick='consultarAuditoria( \"".$hisPacientes[ 'nom' ]."\", \"".$hisPacientes[ 'his' ]."\", \"".$hisPacientes[ 'ing' ]."\", \"".$value[ 'codigo' ]."\", \"".$value[ 'ido' ]."\" );'>Auditor&iacute;a</a></td>";
						echo "</tr>";
						$j++;
						$k++;
					}
				}

				echo "</table>";
				echo "</div>";
			}
			else{
				echo "<center><b>NO HAY CONCIDENCIA CON LOS PARAMETROS SUMINISTRADOS</b></center>";
			}
		}
		else{
			echo "<center><b>NO HAY CONCIDENCIA CON LOS PARAMETROS SUMINISTRADOS</b></center>";
		}
	}
}

function pintarAuditoria( $auditoria ){
	
	if( $auditoria ){
		
		echo "<br>";
		echo "<div>";
		echo "<table align='center'>";
		echo "<tr class=encabezadoTabla>";
		echo "<td>Historia</td>";
		echo "<td>Paciente</td>";
		echo "<td>Articulo</td>";
		echo "</tr>";
		
		echo "<tr class=fila1>";
		echo "<td>".$auditoria[0]['historia']."-".$auditoria[0]['ingreso']."</td>";
		echo "<td>".$auditoria['infoPaciente']['nombre']."</td>";
		echo "<td>".$auditoria[0]['articulo']."-".$auditoria[0]['nombreGenerico']."</td>";
		echo "</tr>";
		
		echo "</table>";
		echo "</div>";
		
		echo "<br>";
		echo "<br>";
		
		echo "<table align='center' border=0 style='width:90%'>";

		echo "<tr align='center' class='encabezadoTabla'>";
		echo "<td>Usuario</td>";
		echo "<td>Fecha y hora</td>";
		echo "<td>Mensaje</td>";
		echo "<td>Referencia</td>";
		echo "</tr>";

		$cont1 = 0;
		foreach( $auditoria as $key => $historia ){
			
			if( is_numeric( $key ) ){
				if($cont1 % 2 == 0){
					echo "<tr class='fila1'>";
				} else {
					echo "<tr class='fila2'>";
				}

				echo "<td>".utf8_encode( $historia['usuario'] )."</td>";
				echo "<td>".$historia['fecha']." - ".$historia['hora']."</td>";
				echo "<td>".utf8_encode( $historia['mensaje'] )."</td>";
				echo "<td>".utf8_encode( $historia['descripcion'] )."</td>";
					
				echo "</tr>";
					
				$cont1++;
			}
		}
			
		echo "</table>";
		echo "<br>";
		echo "<div style='text-align:center;'>";
		echo "<input type='button' value='Cerrar' onClick='$.unblockUI();'>";
		echo "</div>";
	}
} 

/*********************************************************************************************************
 * Consulta los datos de la auditoría para un medicamento en específico
 *********************************************************************************************************/ 
function consultarHistorialCambiosKardex( $conex, $wbasedato, $historia, $ingreso, $articulo, $ido ){

	$coleccion = array();

	// AND ".$wbasedato."_000055.Fecha_data <= '$fecha'
	$q = "SELECT
			a.Fecha_data,a.Hora_data,Kaudes,Kaumen,SUBSTRING(a.Seguridad FROM INSTR(a.Seguridad,'-')+1) codigoUsuario, Descripcion, Artcod, Artcom, Artgen
		FROM
			".$wbasedato."_000055 a, ".$wbasedato."_000026 b, usuarios
		WHERE
			Kauhis = '$historia'
			AND Kauing = '$ingreso'
			AND Codigo = SUBSTRING(a.Seguridad FROM INSTR(a.Seguridad,'-')+1)
			AND Kauido = '".$ido."'
			AND Kaudes LIKE '%".$articulo."%'
			AND artcod = '".$articulo."'
		ORDER BY
			Fecha_data DESC, Hora_data DESC";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$cont1 = 0;

		while( $info = mysql_fetch_array($res) ){
			
			$cont1++;
			
			$cambio = array(
				'historia'	 		=> $historia,
				'ingreso'	 		=> $ingreso,
				'fecha'				=> $info['Fecha_data'],
				'hora'		 		=> $info['Hora_data'],
				'descripcion'		=> $info['Kaudes'],
				'mensaje'	 		=> $info['Kaumen'],
				'usuario'	 		=> $info['codigoUsuario']." - ".$info['Descripcion'],
				'articulo' 	 		=> $info['Artcod'],
				'nombreComercial' 	=> $info['Artgen'],
				'nombreGenerico' 	=> $info['Artcom'],
			);

			$coleccion[] = $cambio;
		}
	}
	return $coleccion;
}
 
/***************************************************************************************************
 * Consulta el código y nombre de un articulo
 ***************************************************************************************************/
function consultarArticulos( $conex, $wbasedato, $articulo ){
	
	$val = array();
	
	// Busco si existe encabezado
	$sql = "SELECT Artcod, Artgen
			  FROM ".$wbasedato."_000026
			 WHERE ( artcod  = '".$articulo."'
			    OR artgen LIKE '%".$articulo."%'
			    OR artcom LIKE '%".$articulo."%'
			   )
			   AND artest = 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		while( $rows = mysql_fetch_array( $res) ){
			// $val[ "suggestions" ][] = array( 
				// 'value'     => utf8_encode( $rows['Artcod']."-".$rows['Artgen'] ),
				// 'data'      => utf8_encode( $rows['Artcod'] ),
			// );
			$val[] = utf8_encode( trim( $rows['Artcod'] )."-".trim( $rows['Artgen'] ) );
		}
	}
	
	return $val;
}
 


/************************************************************************************************************************
 * Consulta algunos datos demográficos de la tabla de ingeso de pacientes (cliame_000100)
 ************************************************************************************************************************/
function datosDemograficosPorIngreso( $conex, $wbasedato, $wcliame, $historia, $ingreso ){

	$val = false;

	// Busca la información demográfica del paciente (Departamento, ciudad, sexo, direccion, telefono)
	$sql = "SELECT Pactel, Paciu, b.Nombre as Ciudad, Pacdep, c.Descripcion as Departamento, Pacdir, Pacsex, Pacfna
			  FROM {$wcliame}_000100 a, root_000002 c, root_000006 b
			 WHERE pachis = '".$historia."'
			   AND b.codigo = paciu
			   AND c.codigo = Pacdep; ";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows($res);

	if($num > 0){

		$row = mysql_fetch_array($res);

		$val = array(
			'telefono' 			 => $row['Pactel'],
			'codigoCiudad' 		 => $row['Paciu'],
			'ciudad' 			 => $row['Ciudad'],
			'codigoDepartamento' => $row['Pacdep'],
			'departamento'		 => $row['Departamento'],
			'direccion'			 => $row['Pacdir'],
			'sexo'				 => $row['Pacsex'],
			'descripcionSexo'	 => $row['Pacsex'] == 'M' ? 'Masculino': 'Femenino',
			'fechaNacimiento'	 => $row['Pacfna'],
		);
	}

	return $val;
}

/****************************************************************************************************************
 * 												FIN DE FUNCIONES
 ****************************************************************************************************************/
 

/**************************************************************************************************************
 * Consulta e impresion de ordenes de control
 *
 * Fecha de creación: 2016-12-01
 * Por				: Edwin Molina Grisales
 * Descripción		: Permite consultar los formatos de medicamentos de control e imprimirlos
 **************************************************************************************************************/

include_once("root/comun.php");
include_once("root/montoescrito.php");



$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
$wcliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cliame" );
$whce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );

if( $_GET[ 'consultaAjax' ] )
	$consultaAjax = $_GET[ 'consultaAjax' ];

if( !empty( $consultaAjax ) ){
	
	switch( $consultaAjax ){
		
		case 'consultarArticulos': 
			$articulo = utf8_decode( $_GET[ 'term' ] );
			$result = consultarArticulos( $conex, $wbasedato, $articulo );
			
			echo json_encode( $result );
		break;
		
		
		case 'consultarAuditoria':
		
			$historia 	= $_POST['historia'];
			$ingreso	= $_POST['ingreso'];
			$articulo	= $_POST['articulo'];
			$ido		= $_POST['ido'];
			$nombre		= utf8_decode( $_POST['nombre'] );
		
			$auditoria = consultarHistorialCambiosKardex( $conex, $wbasedato, $historia, $ingreso, $articulo, $ido );
			$datosDemo = datosDemograficosPorIngreso( $conex, $wbasedato, $wcliame, $historia, $historia );
			
			$auditoria[ 'infoPaciente' ] = $datosDemo;
			$auditoria[ 'infoPaciente' ]['nombre'] = $nombre;
			pintarAuditoria( $auditoria );
		break;
		
		case 'consultarDatos': 
			pintarDatos( $conex, $wemp_pmla, $wbasedato );
		break;
		
		default: break;
	}
	
	exit();
}

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

$wuser1=explode("-",$user);
$wusuario=trim($wuser1[1]);

?>

<title>IMPRESION MEDICAMENTOS DE CONTROL</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<script src="../../../include/root/print.js" type="text/javascript"></script>



<!-- <link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/> -->
<!--<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script> -->

<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>

<script>

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
	
	
/************************************************************************************************
 * Muestra la url correspondiente para imprimir correctamente
 ************************************************************************************************/
function mostrarUrls( cmp ){
	$( "a", cmp.parentNode ).css({display:""});
	$( cmp ).hide();
}

function limpiarParametrosConsulta(){
	 $("input:text").val( '' );
}

/************************************************************************
 * Consulta la auditoria de un medicamento en específico
 ************************************************************************/
function consultarAuditoria( nombre, historia, ingreso, articulo, ido ){
	
	$.ajax({
		url: "consultaMedicamentosControl.php?wemp_pmla=<?=(string)$wemp_pmla;?>",
		type: "POST",
		// dataType: "json",
		data:{
			consultaAjax:			'consultarAuditoria',
			nombre:					nombre,
			historia:				historia,
			ingreso: 				ingreso,
			articulo: 				articulo,
			ido: 					ido,
		},
		async: false,
		success:function(respuesta) {
			
			$.blockUI({ message: "<div>"+respuesta+"</div>",
				css: {
					overflow: 'auto',
					cursor	: 'auto',
					width	: "60%",
					height	: "80%",
					left	: "20%",
					top		: '100px',
				} 
			});
		}
	});
}


function consultarDatos(){
	
	$.blockUI({message: "<div>Espere un momento por favor...</div>" });
	
	$.ajax({
		url: "consultaMedicamentosControl.php?wemp_pmla=<?=(string)$wemp_pmla;?>",
		type: "POST",
		data:{
			consultaAjax		: 'consultarDatos',
			txArticulo 			: $( '#txArticulo' ).val(),
			txNombrePaciente 	: $( '#txNombrePaciente' ).val(),
			txHistoria 			: $( '#txHistoria' ).val(),
			txNroIdentificacion	: $( '#txNroIdentificacion' ).val(),
			txNroOrden 			: $( '#txNroOrden' ).val(),
			txFechaFinal 		: $( '#txFechaFinal' ).val(),
			txFechaInicial 		: $( '#txFechaInicial' ).val(),
		},
		async: false,
		success:function(respuesta) {			
			$.unblockUI();
			$("#dvResultados" ).html( respuesta );
		}
	});
}

$(function() {

	$("#txFechaInicial").datepicker({maxDate:0});
	$("#txFechaFinal").datepicker({maxDate:0});

	$( "#btBuscar" ).click(function(){

		var val = false;
		var msg = "Debe ingresar al menos un campo";

		$( "input:text" ).each(function(){
			if( $.trim( $( this ).val() ) != "" ){
				val = true;
			}
		});
		
		if( val ){
			if( $( "#txFechaInicial" ).val() != "" && $( "#txFechaFinal" ).val() == "" ){
				msg = "Debe ingresar la Fecha Final";
				val = false;
			}
		}
		
		if( val ){
			if( $( "#txFechaFinal" ).val() != "" && $( "#txFechaInicial" ).val() == "" ){
				msg = "Debe ingresar la Fecha Inicial";
				val = false;
			}
		}

		//Se puede hacer el submit sí y solo sí uno de los campos es difernte de vació
		if( val ){
			// document.forms[0].submit();
			consultarDatos();
		}
		else{
			// jAlert( "Debe ingresar al menos un campo", "Alerta" );
			jAlert( msg, "ALERTA" );
		}
	});
	
	
	
	$( "#txFechaInicial" )
		.change(function(){	//asigno un envento a la fecha inicial
			//La fecha final debe ser igual o superior a la fecha inicial
			$( "#txFechaFinal" ).datepicker( "option", "minDate", $( this ).val() );
		})
		.change();	//Ejectuo la accion change una vez se carga la página
	

	$("#txArticulo").autocomplete({
		source		: "consultaMedicamentosControl.php?consultaAjax=consultarArticulos&wemp_pmla=<?=(string)$wemp_pmla;?>",
		cacheLength	:0,
		minLength	: 3,
		delay		: 500,
		select		: function( data, value ){
			return data;
		}
	});
	
});

</script>

<style>
.ui-autocomplete {
    max-height: 100px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 100px;
  }
 </style>
<?php

	//Limpio los datos de busqueda
	$txArticulo 			= "";
	$txNombrePaciente 		= "";
	$txHistoria 			= "";
	$txNroIdentificacion	= "";
	$txNroOrden 			= "";
	$txFechaFinal 			= "";
	$txFechaInicial 		= "";
	
	if( $_POST ){
		//Limpio los datos de busqueda
		$txArticulo 			= trim( $_POST['txArticulo'] );
		$txNombrePaciente 		= trim( $_POST['txNombrePaciente'] );
		$txHistoria 			= trim( $_POST['txHistoria'] );
		$txNroIdentificacion	= trim( $_POST['txNroIdentificacion'] );
		$txNroOrden 			= trim( $_POST['txNroOrden'] );
		$txFechaFinal 			= trim( $_POST['txFechaFinal'] );
		$txFechaInicial 		= trim( $_POST['txFechaInicial'] );
	}

	?>
	<style>
		td {
			font-size: 10pt;
		}

		td.fila1{
			font-weight: bold;
		}

		tr.encabezadotabla{
			text-align: center;
		}

		input[type="text"]{
			width: 100%;
		}
		
		input[type="button"]{
			width: 100px;
			margin: 5px;
		}
		
		td > div > div{
			float: left;
			width: 110px;
			margin: 10px;
		}
	</style>
	<?php



	$institucion = consultarInstitucionPorCodigo($conex,$wemp_pmla);

	$wactualiz = "Diciembre 01 de 2016";
	encabezado("CONSULTA DE FORMULAS DE CONTROL", $wactualiz, "clinica");
	?>
	
	<form method=post>

	<br>
	<table align='center'>
		<tr class=encabezadotabla>
			<td colspan=2>INGRESE LOS DATOS DE BUSQUEDA</td>
		</tr>
		<tr>
			<td class=fila1>Articulo</td>
			<td class=fila2>
				<input type='text' name='txArticulo' id='txArticulo' value='<?=$txArticulo ?>'>
			</td>
		</tr>
		<tr>
			<td class=fila1>Historia</td>
			<td class=fila2>
				<input type='text' name='txHistoria' id='txHistoria' onkeypress='return validarEntradaDecimal( event );' value='<?=$txHistoria ?>'>
			</td>
		</tr>
		<tr>
			<td class=fila1>Nombre del paciente</td>
			<td class=fila2>
				<input type='text' name='txNombrePaciente' id='txNombrePaciente' value='<?=$txNombrePaciente ?>'>
			</td>
		</tr>
		<tr>
			<td class=fila1>Nro. Identificaci&oacute;n</td>
			<td class=fila2>
				<input type='text' name='txNroIdentificacion' id='txNroIdentificacion' onkeypress='return validarEntradaDecimal( event );' value='<?=$txNroIdentificacion ?>'>
			</td>
		</tr>
		<tr>
			<td class=fila1>Nro. Orden</td>
			<td class=fila2>
				<input type='text' name='txNroOrden' id='txNroOrden' onkeypress='return validarEntradaDecimal( event );' value='<?=$txNroOrden ?>'>
			</td>
		</tr>
		<tr class=encabezadotabla>
			<td colspan='2'>Fecha de generacion</td>
		</tr>
		<tr>
			<td colspan=2 class=fila2>
				<table>
					<tr>
						<td class=fila1>Desde</td>
						<td class=fila2>
							<input type='text' name='txFechaInicial' id='txFechaInicial' value='<?=$txFechaInicial ?>'>
						</td>
						<td class=fila1>hasta</td>
						<td class=fila2>
							<input type='text' name='txFechaFinal' id='txFechaFinal' value='<?=$txFechaFinal ?>'>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr align=Center>
			<td colspan=2>
				<div style='width:100%;margin:0 auto;'>
					<div>
						<input type='button' name='btBuscar' id='btBuscar' value='Buscar'>
					</div>
					<div>
						<input type='button' name='btCerrar' id='btLimpiar' value='Limpiar' onClick='limpiarParametrosConsulta();'>
					</div>
					<div>
						<input type='button' name='btCerrar' id='btCerrar' value='Cerrar' onClick='cerrarVentana();'>
					</div>
				</div>
			</td>
		</tr>
	</table>
	
	<div id='dvResultados'>
	</div>

	</form>