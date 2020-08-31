<?php
include_once("conex.php");

/*************************************************************************************************************
 * Programa: 	salaEsperaConsultorio.php
 * Por:			Edwin Molina Grisales
 * Descripcion:	Muestra a un Medico los pacientes que se encuentran esperando para ser atendidos.
 * 
 *************************************************************************************************************/

/*************************************************************************************************************
 * 											  FUNCIONES
 ************************************************************************************************************/

/**
 * 
 * @return unknown_type
 */
function filtroMedico(){
	
	global $conex;
	global $wbasedato;
	
	$sql = "SELECT
				
			FROM
				root_000017
			WHERE
				medusu = ''
			";
	
	$res= mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		echo "<table align='center'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>";
		echo "FILTRO POR MEDICOS";
		echo "</td>";
		echo "<tr class='fila1'>";
		echo "<td>";
		
		echo "<SELECT name='slFiltroProfesional' id='slFiltroProfesional' onChange='filtroPorMedicos( this );' style='width:100%'>";
		echo "<option></option>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			echo "<option value='{$rows['Medusu']}'>{$rows['Mednom']}</option>";	
		}
		
		echo "</SELECT>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
	}
	else{
		
	}	
}


/**
 * Desbloquea los registros que quedaron cogidos como historia abierta
 * 
 * @return unknown_type
 */
function desbloqueandoRegistros( $key ){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	$sql = "UPDATE
				{$wbasedato}_000017
			SET
				espesp = 'on'
			WHERE
				espfec = '".date( "Y-m-d" )."'
				AND espesp != 'on'
				AND espate != 'on'
				AND espest = 'on'
				AND espmed = '$key'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0){
		return true;	
	}
	else{
		return false;
	}
}

/**
 * Inidca si el médica ya tiene abierta una historia Medica
 * 
 * @return unknown_type
 */
function historiaAbierta(){
	
	global $conex;
	global $wbasedato;
	global $key;
	
	$val = '';
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000017
			WHERE
				espfec = '".date( "Y-m-d" )."'
				AND espesp != 'on'
				AND espate != 'on'
				AND espest = 'on'
				AND espmed = '$key';
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0){
		return true;	
	}
	else{
		return false;
	}
}

/**
 * Marca a un paciente como atendido
 * 
 * @param $id
 * @return unknown_type
 */
function marcarAtendido( $id ){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	$sql = "UPDATE
				{$wbasedato}_000017
			SET
				espate = 'on'
			WHERE
				id = '$id'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0){
		return true;	
	}
	else{
		return false;
	}
	
}

/**
 * Busca si el paciente tiene mas ingresos para el mismo día y que el ingreso sea superior al actual
 * 
 * @param $his
 * @param $ing
 * @return unknown_type
 */
function conMasIngresos( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000017
			WHERE
				esphis = '$his'
				AND esping > '$ing'
				AND espfec = '".date( "Y-m-d" )."'
				AND espest = 'on'
				AND espate = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$val = true;
	}
	else{
		$val = false;
	}
	
	return $val;
}

/**
 * Busca si el paciente tiene ya tiene una Justificación CTC
 * 
 * @param $his		Historia
 * @param $ing		Ingreso
 * @param $fecha	Fecha
 * @param $sol		Solución
 * @param $tabla	Tabla de la olución
 * @return unknown_type
 */
function tieneCTC( $his, $ing, $fecha, &$sol, &$tabla ){
	
	global $conex;
	global $wbasedato;
	global $infoMedico;
	
	$sol = '';
	$tabla = '';
	
	$exp = explode( "-", $infoMedico->bdEmpresas );

	if( $exp[0] == $wbasedato ){
		
		//Buscando la solución del CTC
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000011, {$wbasedato}_000003
				WHERE
					inging = '$ing'
					AND inghis = '$his'
					AND ingfin = '$fecha'
					AND empnit = SUBSTRING_INDEX( ingemp, '-', 1 )
					AND empctc != ''
					AND empctc != 'NO APLICA'
				";
	}
	else{
		
		//Buscando la solución del CTC
		$sql = "SELECT
					*, ctc as Empctc
				FROM
					{$exp[0]}_{$exp[1]}, {$wbasedato}_000003
				WHERE
					inging = '$ing'
					AND inghis = '$his'
					AND ingfin = '$fecha'
					AND nit = SUBSTRING_INDEX( ingemp, '-', 1 )
					AND ctc != ''
					AND ctc != 'NO APLICA'
				";
	}
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows =  mysql_fetch_array( $res ) ){
		
		$solucionCTC = $wbasedato;
		$sol = $solucionCTC; 
		$tablaCTC = $rows['Empctc'];
		$tabla = $tablaCTC;
		
		$sql = "SELECT
					*
				FROM
					{$solucionCTC}_$tablaCTC
				WHERE
					ctchis = '$his'
					AND ctcing = '$ing'
					AND ctcfdd = '$fecha'
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			return $rows['id'];
		}
		else{
			return 0;
		}
	}
	else{
		return 0;
	}
}

/**
 * Muestra la tabla de pacientes atendidos por el doctor
 * @param $key
 * @return unknown_type
 */
function pacientesAtendidos( $key ){

	global $conex;
	global $wbasedato;
	global $wbasecitas;
	
	$infoMedico = new classMedico( $key );
	
	//Buscando pacientes atendidos
	$sql = "SELECT
				Esphis, Esping, Espfec, Espdoc, Espnpa, b.id as id
			FROM
				{$wbasedato}_000017 a, 
				{$wbasedato}_000001 b
			WHERE
				espest = 'on'
				AND espate = 'on'
				AND esphis = hclhis
				AND esping = hcling
				AND hclfec = espfec
				AND espfec = '".date("Y-m-d")."'
				AND espmed = '$key'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		echo "<center><b>PACIENTES ATENTIDOS</b></center>";
		echo "<br><br>";
		
		echo "<table id='tbAtendidos' align='center'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td style='width:80;text-align:center'>Historia</td>";
		echo "<td style='width:80;text-align:center'>Ingreso</td>";
		echo "<td style='width:250;text-align:center'>Nombre del Paciente</td>";
		echo "<td style='width:150;text-align:center'>Nro de Documento</td>";
		echo "<td style='width:100;text-align:center'>Modificar<br>Historia</td>";
		echo "<td style='width:100;text-align:center'>CTC</td>";
		echo "<td style='width:100;text-align:center'>Impresion</td>";
		echo "</tr>";

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			//Busco si tiene mas ingresos, y que el ingreso sea mayor en la sala de espera, si es así no se puede modificar la historia
			//y el paciente ya este atendido
			$conMasIngresos = conMasIngresos( $rows['Esphis'], $rows['Esping'] );
			
			$fila = "class='fila".($i%2+1)."'";
			
			$urlHC = "../../det_registro.php?id={$rows['id']}&pos1={$wbasedato}&pos2=2010-03-15&pos3=16:16:46&pos4=000001&pos5=0&pos6={$wbasedato}&tipo=P&Valor=&Form=000001-{$wbasedato}-C-HC%20{$wbasedato}o&call=0&change=0&key=$key&Pagina=1";
			$urlHC .= "&nid={$rows['Espdoc']}"; 
//			$urlImpresion = "impresion.php?his={$rows['Esphis']}&doc=$key&ing={$rows['Esping']}";
			$urlImpresion = "../../{$infoMedico->grupoSolucion}/procesos/impresion.php?doc=$key&txHis={$rows['Esphis']}&sala";
			
			$urlCTCnuevo = '';
			$idCTC = tieneCTC( $rows['Esphis'], $rows['Esping'], date("Y-m-d"), $sol, $tabla );
			
			$url = '';
			
			if( !empty($sol) && !empty($tabla) ){
				$urlCTCnuevo = "../../det_registro.php?id=$idCTC&pos1={$sol}&pos2=0&pos3=0&pos4=$tabla&pos5=0&pos6={$sol}&tipo=P&Valor=&Form=$tabla-{$sol}-C-JUSTIFICACIONES%20CTC%20POR%20EMPRESAS&call=0&change=0&key=$key&Pagina=1";
				$urlCTCnuevo .= "&his={$rows['Esphis']}&ing={$rows['Esping']}";
				
				$url = "<INPUT type='radio' name='rbCTC' onclick=\"javascript: abrirVentana( '$urlCTCnuevo' );\">";
			}
			
			echo "<tr $fila>";
			echo "<td align='center'>{$rows['Esphis']}</td>";
			echo "<td align='center'>{$rows['Esping']}</td>";
			echo "<td>".htmlentities( $rows['Espnpa'] )."</td>";
			echo "<td align='center'>{$rows['Espdoc']}</td>";
			
			if( !$conMasIngresos ){
				echo "<td align='center'><INPUT type='radio' name='rbHC' onclick=\"javascript: abrirVentana( '$urlHC' );\"></td>";
			}
			else{
				echo "<td align='center' bgcolor='yellow'>CON OTRO INGRESO</td>";
			}
			
			echo "<td align='center'>$url</td>";
			echo "<td align='center'><a href='$urlImpresion' target='_blank'>Impresion</a></td>";
			echo "</tr>";
			
		}
		
		echo "</table>";
	}
	else{
		echo "<center><b>NO HAY PACIENTES ATENDIDOS</b></center>";
	}
	
}

/**
 * Indica si un paciente en sala de espera tiene ya una historia creada
 * 
 * @param $his		Historia
 * @param $ing		Ingreso
 * @return unknown_type
 */
function tieneHistoria( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$id = 0;
	
	$sql = "SELECT
				id
			FROM
				{$wbasedato}_000001
			WHERE
				hclhis = '$his'
				AND hcling = '$ing'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$id = $rows['id'];		
	}
	
	return $id;
}

function tablaSalaEspera( $key ){
	
	global $conex;
	global $wbasedato;
	global $wbasecitas;
	
	//Buscando pacientes que esten atendidos y con cita y no esten en la sala de espera
	$sql = "SELECT
				*, a.id as idCita
			FROM
				{$wbasedato}_000017 a, {$wbasedato}_000003 b
			WHERE
				espfec = '".date("Y-m-d")."'
				AND espate != 'on'
				AND espest = 'on'
				AND esphis = inghis
				AND esping = inging
				AND ingfin = espfec
				AND espmed = '$key'
			GROUP BY 
				inghis, inging, espfec, esphor
			";
				//AND espesp = 'on'
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		echo "<center><b>PACIENTES EN SALA DE ESPERA</b></center><BR><BR>";
		
		echo "<table id='tbSalaEspera' align='center'>";
		
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td style='width:100'>Hora de<br>Ingreso</td>";
		echo "<td style='width:100'>Fecha de<br>la cita</td>";
		echo "<td style='width:100'>Hora de<br>la cita</td>";
		echo "<td style='width:100'>Historia</td>";
		echo "<td style='width:150'>Nro. de Documento</td>";
		echo "<td  style='width:250'>Nombre del Paciente</td>";
		echo "<td style='width:250'>Empresa</td>";
		echo "<td style='width:150'>Motivo Consulta</td>";
		echo "<td style='width:100'>Ir a Historia/En Consulta</td>";
		echo "</tr>";

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			//Busco si tiene mas ingresos, y que el ingreso sea mayor en la sala de espera, si es así no se puede modificar la historia
			//y el paciente ya este atendido
			$conMasIngresos = conMasIngresos( $rows['Esphis'], $rows['Esping'] );
			
			if( !$conMasIngresos ){
			
				$fila = "class='fila".($i%2+1)."'";
				
				$hasHis = tieneHistoria( $rows['Esphis'], $rows['Esping'] );
				
				if( $hasHis > 0 ){
					$url = "../../det_registro.php?id=$hasHis&pos1={$wbasedato}&pos2=2010-02-25&pos3=08:11:12&pos4=000001&pos5=0&pos6={$wbasedato}&tipo=P&Valor=&Form=000001-{$wbasedato}-C-HC%20{$wbasedato}&call=0&change=0&key=$key&Pagina=1";
					$url .= "&nid=".$rows['Espdoc']."&idCita=".$rows['idCita'];
				}
				else{
					$url = "../../det_registro.php?id=0&pos1={$wbasedato}&pos2=0&pos3=0&pos4=000001&pos5=0&pos6={$wbasedato}&tipo=P&Valor=&Form=000001-{$wbasedato}-C-HC%20{$wbasedato}&call=0&change=0&key=$key&Pagina=0";
					$url .= "&nid=".$rows['Espdoc']."&idCita=".$rows['idCita'];
				}
				
				echo "<tr $fila>";
				echo "<td align='center'>{$rows['Inghin']}</td>";
				echo "<td align='center'>{$rows['Espfec']}</td>";
				echo "<td align='center'>{$rows['Esphor']}</td>";
				echo "<td align='center'>{$rows['Esphis']}-{$rows['Esping']}</td>";
				echo "<td align='center'>{$rows['Espdoc']}</td>";
				echo "<td>".htmlentities( $rows['Espnpa'] )."</td>";
				echo "<td align='center'>{$rows['Ingemp']}</td>";
				echo "<td align='center'>{$rows['Esptin']}</td>";
				
				if( $rows['Espesp'] == 'on' ){
//					echo "<td style='text-align:center'><a href='javascript: abrirVentana( \"$url\" );'>Ir a Historia</a></td>";
					echo "<td style='text-align:center'><INPUT type='radio' name='rbHCespera' onclick='javascript: historiaAbierta( \"$url\" );'></td>";
				}
				else{
					echo "<td style='background-color:yellow;text;text-align:center'>EN CONSULTA</td>";
				}
	
				echo "</tr>";
			}
			else{
				marcarAtendido( $rows['idCita'] );
				$i--;
			}
			
		}
		
		echo "</table>";
	}
	else{
		echo "<center><b>NO HAY PACIENTES EN LA SALA DE ESPERA</b></center>";
	}
	
}

/*************************************************************************************************************
 * 											  FIN FUNCIONES
 ************************************************************************************************************/


/*************************************************************************************************************
 * 											  INICIO DEL PROGRAMA
 ************************************************************************************************************/

if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
	exit;
}

include_once("root/comun.php");

include_once( "./funcionesGenerales.php" );

$conex = obtenerConexionBD("matrix");

$infoMedico = new classMedico( substr($user, 2, strlen($user)) );

$wbasedato = $infoMedico->bdHC;

//$key = substr($user, 2, strlen($user));

$wemp_pmla = '01';

//$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

if(@$consultaAjax){
	
	switch( $consultaAjax ){
		
		case 10:
			tablaSalaEspera( $codMed );
			break;
			
		case 11:
			pacientesAtendidos( $codMed );
			break;
			
		case 12:
			echo historiaAbierta();
			break;
			
		default:
			break;
	}
	
}
else{
	
	$wbasecitas = $infoMedico->bdCitas;
	
	$key = substr( $user, 2, strlen($user) );
?>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script> 	<!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery-ui-1.7.2.custom.js"></script> 	<!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script> <!-- Block UI -->
<script type="text/javascript" src="../../../include/root/ui.core.js"></script>	<!-- Nucleo jquery -->
<script>

/************************************************************
 * AJAX DE MAURICIO
 ***********************************************************/

 	/******************************************************************
 	 * Realiza una llamada ajax a una pagina
 	 * 
 	 * met:		Medtodo Post o Get
 	 * pag:		Página a la que se realizará la llamada
 	 * param:	Parametros de la consulta
 	 * as:		Asincronro? true para asincrono, false para sincrono
 	 * fn:		Función de retorno del Ajax
 	 *
 	 * Nota: Si la llamada es GET las opciones deben ir con la pagina.
 	 ******************************************************************/
	function consultasAjax( met, pag, param, as, fn ){

		this.metodo = met;
		this.parametros = param; 
		this.pagina = pag;
		this.asc = as;
		this.fnchange = fn; 

		try{
			this.ajax=nuevoAjax();
	
			this.ajax.open( this.metodo, this.pagina, this.asc );
			this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			this.ajax.send(this.parametros);
	
			if( this.asc ){
				var xajax = this.ajax;
//				this.ajax.onreadystatechange = this.fnchange;
				this.ajax.onreadystatechange = function(){ fn( xajax ) };
				
				if ( !estaEnProceso(this.ajax) ) {
					this.ajax.send(null);
				}
			}
			else{
				return this.ajax.responseText;
			}
		}catch(e){	}
	}

/************************************************************
 * AJAX
 ***********************************************************/

 	/**************************************************
 	 * Crea un mensaje de Dialogo para el usuario
 	 **************************************************/
	function cuadroDeDialogo( mensaje ){

		$.blockUI({ message: "<br><center><b>"+mensaje+"</b><center><br><br><center><input type='button' onclick='javascript: $.unblockUI();' value='Aceptar'></center><br>",
					css:{ backgroundColor: '#E8EEF7' } 
				  });
	}
 
 	/********************************************************
 	 * Indica si tiene una Historia clinica abierta, si no la tiene
 	 * abierta abre la pagina
 	*********************************************************/
	function historiaAbierta( url ){

		var txtRes = consultasAjax( "POST", "salaEsperaConsultorio.php", "consultaAjax=12&key="+document.forms[0].elements['hiKey'].value );
//		alert( "........."+txtRes );
		if( txtRes != '1' ){
			abrirVentana( url );
		}
		else{
//			alert( "No puede abrir mas de una historia a la vez" );
//			$.blockUI({ message: "<br><center><b>No puede abrir mas de una historia a la vez</b><center><br><br><center><input type='button' onclick='javascript: $.unblockUI();' value='Aceptar'></center><br>",
//						css:{ backgroundColor: '#E8EEF7' } 
//				});

			cuadroDeDialogo( "No puede abrir mas de una historia a la vez" );

		}
	}

	/********************************************************
 	 * Abre una ventana nueva con la dirección dada
 	*********************************************************/
	function abrirVentana( url ){
		
		var ancho=screen.width;
		var alto=screen.availHeight;
		var v = window.open( url, '', 'scrollbars=1, width='+ancho+', height='+alto );
		v.moveTo(0,0);
		
	}

	function verAtendidos( campo ){

		if( campo.checked == true ){

			document.getElementById( "dvAtendidos" ).style.display = "";
						
		}
		else{
			
			document.getElementById( "dvAtendidos" ).style.display = "none";
		}
	}

	function pacientesEspera( ajax ){
		
		if ( ajax.readyState==4 && ajax.status==200 ){
			
			var aux = document.getElementById( "dvEspera" );
			aux.innerHTML = ajax.responseText;

			var espera = document.getElementById( "tbSalaEspera" ); 
				
			if( espera ){
				if( espera.rows.length == 1 ){
					aux.innerHTML = "<center><b>NO HAY PACIENTES EN LA SALA DE ESPERA</b></center>";
				} 
			}
		}
	}

	function pacientesAtendidos( ajax ){

		if ( ajax.readyState==4 && ajax.status==200){
			
			var aux = document.getElementById( "dvAtendidos" );
			aux.innerHTML = ajax.responseText;
		}
	}
	
	
	function iniciar(){

		var codMed = document.forms[0].elements[ 'hiCodigoMedico' ].value;

		var pacientes = new consultasAjax( "POST", "salaEsperaConsultorio.php", "consultaAjax=10"+"&codMed="+codMed, true, pacientesEspera );
		var atendidos = new consultasAjax( "POST", "salaEsperaConsultorio.php", "consultaAjax=11"+"&codMed="+codMed, true, pacientesAtendidos );

		setTimeout( "iniciar()", 60000 );
	}

	function filtroPorMedicos( campo ){

		if( campo.options[ campo.selectedIndex ].text != '' ){
			iniciar();
		}
		else{
			return;
		}

	}

	window.onload = function(){
		iniciar();

		window.document.body.oncontextmenu = function(){ return false; }
		window.document.body.onselectstart = function(){ return true; }
		window.document.body.ondragstart = function(){ return false; }
	}


	function detenerEventos( evt ){

		if( !evt ){
			evt = event;
		}
		
		var key = evt.keyCode ? evt.keyCode : evt.which ;

		if ( (key <= 123 && key >= 112) ){
			
			if (evt && evt.stopPropagation) 
				evt.stopPropagation();
			else if (evt) {
				evt.cancelBubble = true;
				evt.returnValue = false;
			}
			if (evt && evt.preventDefault) 
				evt.preventDefault(); //Para Mozilla Firefox

			evt.keyCode = 0;
			return false;
		}
		
	}

	window.document.onkeydown = detenerEventos;
</script>

<?php

	$doctorName = $infoMedico->nombre;
	$titulo = "SALA DE ESPERA DEL DR. ".strtoupper( $doctorName );
	encabezado( $titulo, "2010-01-13", "fmatrix" );
	
	desbloqueandoRegistros( $key );
	
	echo "<form>";
	
	echo "<br><br>";
	
	echo "<div id='dvEspera'>";
//	tablaSalaEspera( $key );
	echo "</div>";
	
	echo "<br><br>";
	
	echo "<p align='right'>Ver Atendidos  <INPUT type='checkbox' name='cbVerAtentedios' id='cbVerAtentedios' onclick='javascript: verAtendidos( this );'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>";
	
	echo "<div id='dvAtendidos' style='display:none;'>";
//	pacientesAtendidos( $key );
	echo "</div>";
	
	echo "<br><br>";
	echo "<center>";
	echo "<a id='lkImpresion' href='../../{$infoMedico->grupoSolucion}/procesos/impresion.php?doc={$infoMedico->codigo}' target='_blank'>Consultar e imprimir</a>";
//	echo "<a id='lkImpresion' href='impresion.php?doc={$infoMedico->codigo}' target='_blank'>Consultar e imprimir</a>";
	
	echo "</center>";
	echo "<br><br>";
	echo "<center><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript: cerrarVentana();'></center>";
	
//	echo "<meta http-equiv='refresh' content='60;url=salaEsperaConsultorio.php'>";

	echo "<INPUT type='hidden' name='hiCodigoMedico' value='{$infoMedico->codigo}'>";
	echo "<INPUT type='hidden' name='hiKey' value='$key'>";
	
	echo "</form>";
}

/*************************************************************************************************************
 * 											  FIN DEL PROGRAMA
 ************************************************************************************************************/
?>