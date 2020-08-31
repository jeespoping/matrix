<?php
include_once("conex.php");  header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<html>
<head>
<title>HCE - [ORDENES]</title>

<link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- Autocomplete -->
<link type="text/css" href="../../../include/root/jquery.simpletree.css" rel="stylesheet" />

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script>

/******************************************************************
 * AJAX
 ******************************************************************/

/******************************************************************
 * Realiza una llamada ajax a una pagina
 * 
 * met:		Medtodo Post o Get
 * pag:		Página a la que se realizará la llamada
 * param:	Parametros de la consulta
 * as:		Asincronro? true para asincrono, false para sincrono
 * fn:		Función de retorno del Ajax, no requerido si el ajax es sincrono
 *
 * Nota: 
 * - Si la llamada es GET las opciones deben ir con la pagina.
 * - Si el ajax es sincrono la funcion retorna la respuesta ajax (responseText)
 * - La funcion fn recibe un parametro, el cual es el objeto ajax
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
//			this.ajax.onreadystatechange = this.fnchange;
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
/************************************************************************/

/******************************************************************************************
 * Consulto por ajax la prescripción médica del artículo
 ******************************************************************************************/
function consultarPrescripcionCTC( his, ing, art, div, id, fec ){

	var vwemp_pmla = document.getElementById( "wemp_pmla" );
	var fechaActual = new Date();
	diaActual = fechaActual.getDate();
	mesActual = fechaActual.getMonth() + 1;
	anioActual= fechaActual.getFullYear();
	
	fec = anioActual+"-"+mesActual+"-"+diaActual;
			
	if( true ){
		//Creo la url para buscar los protocolos segun los parametros ingresado
		var parametros = "whistoria="+his+"&wingreso="+ing+"&art="+art+"&ide="+id+"&fec="+fec;
		alert( parametros );
		//hago la grabacion por ajax del articulo
		consultasAjax( "POST", "ordenes_imp.php?wemp_pmla="+vwemp_pmla.value, 
						parametros, 
						true, 
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								
								//Esta función llena los datos del protocolo
								document.getElementById( div ).innerHTML = ajax.responseText;
							}
						}
					);
	}
}

/**********************************************************************
 * muestra u oculta un campo segun su id
 **********************************************************************/
function mostrar( campo ){
	
	if( campo.style.display == 'none' ){
		campo.style.display = '';
	}
	else{
		campo.style.display = 'none';
	}
}

/************************************************************************
 * Busca la fila siguiente para mostrar o ocultar la fila
 * Por tanto campo es una Fila
 ************************************************************************/
function mostrarFila( campo ){

	var tabla = campo.parentNode;
	var index = campo.rowIndex;

	mostrar( tabla.rows[ index+1 ] );
	
	// for( var i = index+2;i < tabla.rows.length; i++ ){
		// mostrar( tabla.rows[ i ] );
	// }
}
</script>

<style>
  td {
    font-family: Arial;
    font-size: 6.5pt;
  }
  	
</style>

</head>

<body>
<?php
/**************************************************************************************************************
 * Impresion de formulas de control
 *
 * Fecha de creación:	2012-10-09
 * Por:					Edwin Molina Grisales
 **************************************************************************************************************/
/**************************************************************************************************************
 * DESCRIPCION:
 *
 * Al grabar las ordenes médicas se genera una orden de impresión para los medicamentos de control que el médico
 * halla ordenada.
 *
 * La orden debe ir por la cantidad segun el perfil, es decir la cantidad requerida hasta el día siguiente a la
 * hora de corte.
 *
 * ESPECIFICACIOENS:
 *
 * - El programa graba los articulos de control que el medico halla ordenado para un paciente.
 * - No se permite mandar mas de un medicamento de control a la vez con la misma frecuencia (???)
 * - 
 **************************************************************************************************************/
 
 
/****************************************************************************************************************
 * 												FUNCIONES
 ****************************************************************************************************************/
function consultarDiagnostico( $conex, $whce, $his, $ing ){
	
	$val = "";

	$sql = "SELECT ".$whce."_000051.movdat, ".$whce."_000051.Fecha_data, ".$whce."_000051.Hora_data
			  FROM ".$whce."_000051
			 WHERE ".$whce."_000051.movdat != ''
			   AND ".$whce."_000051.movdat != ' '
			   AND ".$whce."_000051.movcon = 182
			   AND movhis='".$his."'
			   AND moving='".$ing."'
			UNION ALL
			SELECT ".$whce."_000052.movdat, ".$whce."_000052.Fecha_data, ".$whce."_000052.Hora_data 
			  FROM ".$whce."_000052
			 WHERE ".$whce."_000052.movdat != ''
			   AND ".$whce."_000052.movdat != ' '
			   AND ".$whce."_000052.movcon = 141
			   AND movhis='".$his."'
			   AND moving='".$ing."'
			UNION ALL
			SELECT ".$whce."_000063.movdat, ".$whce."_000063.Fecha_data, ".$whce."_000063.Hora_data
			  FROM ".$whce."_000063
			 WHERE ".$whce."_000063.movdat != ''
			   AND ".$whce."_000063.movdat != ' '
			   AND ".$whce."_000063.movcon = 240
			   AND movhis='".$his."' 
			   AND moving='".$ing."'
			ORDER BY Fecha_data DESC, Hora_data DESC";
			
	$res = mysql_query($sql,$conex) or die (mysql_errno()." - ".mysql_error());
	$nummed = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
	}
	
	return $val;
}
 
function dimensionesImagen($idemed)
{
	global $altoimagen;
	global $anchoimagen;

	// Obtengo las propiedades de la imagen, ancho y alto
	@list($widthimg, $heightimg) = getimagesize('../../images/medical/hce/'.$idemed.'.png');
	
	$altoimagen = '27';
	
	@$anchoimagen = (27 * $widthimg) / $heightimg;
	
	if($anchoimagen<81)
		$anchoimagen = 81;
}


 
 
/****************************************************************************************************
 * Calcula la edad del paciente de acuerdo a la fecha de nacimiento
 ****************************************************************************************************/
function calcularEdad( $fecNac ){

	$ann=(integer)substr($fecNac,0,4)*360 +(integer)substr($fecNac,5,2)*30 + (integer)substr($fecNac,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($aa - $ann)/360;
	$meses=(($aa - $ann) % 360)/30;
	if ($ann1<1){
		$dias1=(($aa - $ann) % 360) % 30;
		$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
	} else {
		$dias1=(($aa - $ann) % 360) % 30;
		$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
	}
	
	return floor( $ann1 );
}


function consultarDatosTablaHCE( $conex, $whce, $tabla, $campo, $his, $ing ){

	$val = "";

	$sql = "SELECT Movdat 
			FROM
				{$whce}_{$tabla}
			WHERE
				movpro = '$tabla'
				AND movcon = '$campo'
				AND movhis = '$his'
				AND moving = '$ing'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Movdat' ];
	}
	
	return $val;
}
 
/****************************************************************************************************
 * Cambia el estado de la impresion
 ****************************************************************************************************/
function cambiarEsadoImpresionPorId( $conex, $wbasedato, $id, $usuario ){

	$val = false;

	$sql = "UPDATE 
				{$wbasedato}_000134
			SET
				Ctcimp = 'on',
				Ctcuim = '$usuario',
				Ctcfim = '".date( "Y-m-d" )."',
				Ctchim = '".date( "H:i:s" )."'
			WHERE
				id = '$id'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows( ) > 0 ){
		$val = true;
	}
	
	return $val;
}
 
/**********************************************************************
 * Consulta la informacion de un medico
 **********************************************************************/
function consultarInformacionMedico( $conex, $wbasedato, $codigo ){

	$val = false;

	$sql = "SELECT
				a.*, Espcod, Espnom
			FROM
				{$wbasedato}_000048 a, {$wbasedato}_000044 b
			WHERE
				Meduma = '$codigo'
				AND Medest = 'on'
				AND SUBSTRING_INDEX( medesp, '-', 1 ) = espcod
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	
	return $val;
}
 
/****************************************************************************************************************
 * Cambia el estado de la impresion segun el parametro estado
 ****************************************************************************************************************/
function cambiarEstadoImpresion( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $codMedico, $estado ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000134
			SET
				Ctrimp = '$estado'
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrmed = '$codMedico'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}


/****************************************************************************************************************
 * Cambia el estado del registro segun el campo estado. on para activarlo, off para desactivarlo
 ****************************************************************************************************************/
function cambiarEstadoRegistro( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $codMedico, $estado ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000134
			SET
				Ctrest = '$estado'
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrmed = '$codMedico'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}

/****************************************************************************************************************
 * 												FIN DE FUNCIONES
 ****************************************************************************************************************/



include_once("root/comun.php");



$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));
  
$wuser1=explode("-",$user);
$wusuario=trim($wuser1[1]);

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$whce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );

if( $consultaAjax ){	//si hay ajax

	// switch( $consultaAjax ){
	
		// case '10':
			// generarArticulosAImprimir( $conex, $wbasedato, $historia, $ingreso, $fechaKardex, $wusuario );
			// break;
			
		// default: break;
	// }
}
else{	//si no hay ajax

	include_once("root/montoescrito.php");

	$institucion = consultarInstitucionPorCodigo($conex,$wemp_pmla);
	
	echo "<form>";
	
	if( !isset($imprimir) ){
	
		$wactualiz = "Octubre 11 de 2012";
		
		encabezado("IMPRESION FORMULARIOS CTC DE MEDICAMENTOS",$wactualiz, "clinica");
				
		$sql = "SELECT
					Artgen, Artcom, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f, {$wbasedato}_000060 g
				WHERE "
			//	."	ctcimp = 'off' "
				."	ctcest = 'on'
					AND artcod = ctcart
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ubiald != 'on'
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadfec = ctcfkx
				UNION
				SELECT
					Artgen, Artcom, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f, {$wbasedato}_000054 g
				WHERE "
			//	."	ctcimp = 'off' "
				."	ctcest = 'on'
					AND artcod = ctcart
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ubiald != 'on'
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadfec = ctcfkx
				ORDER BY
					ubisac, ubihac
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
		
			echo "<br>";
			echo "<center><b>Click sobre el centro de costos para ver u ocultar el detalle</b></center>";
			echo "<br>";
		
			echo "<table align='center'>";
			
			echo "<tr class='encabezadotabla' align='center'>";
			
			echo "<td>Cco</td>";
			echo "<td>Nombre Cco</td>";
			echo "<td>Total</td>";
			
			echo "</tr>";
		
			$ccoAnt = '';
		
			$total = 0;
			$totalAImprimir = 0;
		
		
			$rows = mysql_fetch_array( $res );
			$ccoAnt = $rows[ 'Ubisac' ];
			
			for( $i = 0; ;  ){
				
				if( $ccoAnt == $rows[ 'Ubisac' ] ){
				
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'hab' ] = $rows[ 'Ubihac' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'nom' ] = $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ];;
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'tot' ]++;
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'his' ] = $rows[ 'Ctchis' ];
				
					$cconom = $rows[ 'Cconom' ];
					$total++;
					$totalAImprimir++;
					$rows = mysql_fetch_array( $res );
				}
				elseif( $total > 0 ){
					
					$class = "fila".($i%2+1)."";
				
					echo "<tr class='$class' onClick='mostrarFila( this );' style='cursor: hand; cursor: pointer;'>";
					
					echo "<td align='center'>";
					echo $ccoAnt;
					echo "</td>";
					
					echo "<td>";
					echo $cconom;
					echo "</td>";
					
					echo "<td align='center'>";
					echo $total;
					echo "</td>";
					
					echo "</tr>";
					
					$total = 0;
					$ccoAnt = $rows[ 'Ubisac' ];
					$i++;					
					
					//creo una fila mas con la información del paciente que se quiere imprimir
					if( true ){
						
						echo "<tr style='display:none'>";
						echo "<td colspan=3>";
					
						echo "<table align='center'>";
						
						echo "<tr class='encabezadotabla' align='center'>";
						echo "<td>Habitaci&oacute;n</td>";
						echo "<td>Historia</td>";
						echo "<td>Nombre</td>";
						echo "<td>Articulos</td>";
						echo "<td style='width:75'>Impresion por<br>paciente</td>";
						
						echo "</tr>";
						
						$k = 0;
						
						foreach( $pacientes as $keyPacientes => $hisPacientes ){
							
							// foreach( $hisPacientes  as $keyInf => $valueInf ){
							
								$class2 = "fila".($k%2+1)."";
							
								echo "<tr class='$class2'>";
								
								echo "<td align='center'>";
								echo $hisPacientes[ 'hab' ];
								echo "</td>";
								
								echo "<td align='center'>";
								echo $keyPacientes;
								echo "</td>";
								
								echo "<td>";
								echo $hisPacientes[ 'nom' ];
								echo "</td>";
								
								echo "<td align='center'>";
								echo $hisPacientes[ 'tot' ];
								echo "</td>";
								
								echo "<td align='center'><a href='impresionCTCArticulosNoPos.php?wemp_pmla=$wemp_pmla&imprimir=on&historia={$hisPacientes[ 'his' ]}'>imprimir</a></td>";
								
								echo "</tr>";
								
								$k++;
							// }
						}
						
						echo "</table>";
						
						echo "</td>";
						echo "</tr>";
					}
					
					$pacientes = "";	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
					
					if( !$rows ){
						break;
					}
				}
			}
			
			echo "<tr class='encabezadotabla' align='center'>";
			echo "<td colspan=2>Total</td>";
			echo "<td>$totalAImprimir</td>";
			echo "</tr>";
			
			echo "</table>";
			
			//Para ingresar historia e ingreso
			echo "<br>";
			
			echo "<center><b>Nota:</b> Si no ingresa historia se imprimen todo los pacientes</center>";
			echo "<br>";
			
			echo "<table align='center'>";
			echo "<tr class='encabezadotabla' align='center'>";
			echo "<td colspan='2' style='width:300'>";
			echo "INGRESE LA HISTORIA A IMPRIMIR";
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='fila1'>";
			echo "<b>Historia</b>";
			echo "</td>";
			echo "<td class='fila2' align='center'>";
			echo "<INPUT type='text' name='historia' style='width:100'>";
			echo "</td>";
			echo "</tr>";
			echo "</table>";
		}
		else{
			echo "<center><b>NO SE ENCONTRARON CTC PARA IMPRIMIR</b></center>";
		}
		
		echo "<INPUT type='hidden' value='on' name='imprimir' id='imprimir'>";
		echo "<INPUT type='hidden' value='$wemp_pmla' name='wemp_pmla' id='wemp_pmla'>";
		
		echo "<br>";
		
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td>";
		echo "<INPUT type='submit' value='Imprimir' style='width:100'>";
		echo "</td>";
		echo "<td>";
		echo "<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
	}
	else{
	
	?>
	<style>
	  td {
		font-family: verdana;
		font-size: 6.5pt;
	  }
	  
	  table{
		border-collapse: collapse;
	  }
	  
	  .encabezado {
		font-size: 6.5pt;
		font-weight: bold;
	}
	.encabezadoExamen {
		text-align: right;
		font-size: 8pt;
	}
	.encabezadoEmpresa {
		text-align: left;
		font-size: 6.5pt;
	}
	.filaEncabezado {
		border-bottom: 1px solid rgb(51, 51, 51);
		border-left: 1px solid rgb(51, 51, 51);
	}
	.filaEncabezadoFin {
		border-bottom: 1px solid rgb(51, 51, 51);
		border-right: 1px solid rgb(51, 51, 51);
		border-left: 1px solid rgb(51, 51, 51);
	}
	.campoFirma {
		border-bottom: 1px solid rgb(51, 51, 51);
		width:208px;
		height:24px;
	}
	.descripcion
	{
		font-size: 5.5pt;
		text-align:justify;
	}
	.total
	{
		font-size: 6.5pt;
		height: 27px;
		text-align:right;
		text-valign:bottom;
	} 
	</style>
	<?php
		
		//Si la historia es vacia, significa que imprime todo
		if( empty( $historia ) ){
			$historia = '%';
		}
		
		//Si el cco es vacio, significa que imprime todo
		if( empty( $cco ) ){
			$cco = '%';
		}
		
		//Si el articulo no se ha seteado, se imprime todos los articulos
		if( empty( $art ) ){
			$art = '%';
		}
		
		//Consulto los articulos a imprimir
		$sql = "SELECT
					Artgen, Artcom, Artfar, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Ubisac, Ubihac, Pactid, Pacced, Cconom, Kadufr, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000011 g, {$wbasedato}_000060 h
				WHERE "
			//	."	ctcimp = 'off' "
				."	ctcest = 'on'
					AND artcod = ctcart
					AND orihis = ctchis
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ccocod = ubisac
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco'
					AND ctcart LIKE '$art'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadimp = 'on'
				UNION
				SELECT
					Artgen, Artcom, Artfar, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Ubisac, Ubihac, Pactid, Pacced, Cconom, Kadufr, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000011 g, {$wbasedato}_000054 h
				WHERE "
			//	."	ctcimp = 'off' "
				."	ctcest = 'on'
					AND artcod = ctcart
					AND orihis = ctchis
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ccocod = ubisac
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco'
					AND ctcart LIKE '$art'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadimp = 'on'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		echo "<div style='width:20cm' align='center'>";
		
		echo "<INPUT type='hidden' name='wemp_pmla' id='wemp_pmla' value='$wemp_pmla'>";
		
		for( $i = 0; $rowsCTC = mysql_fetch_array( $res ); $i++ ){
		
			//Hago un salgo de linea si se va a imprimir otra factura
			if( $i > 0 ){
				echo "<div style='page-Break-After:always'></div>";
			}
			
			//Consulto la información del medico
			// $rowsMed = consultarInformacionMedico( $conex, $wbasedato, $rowsCTC[ 'Ctcmed' ] );
			$rowsMed = consultarInformacionMedico( $conex, $wbasedato, $wusuario );
			
			$nombreMedico = $rowsMed[ 'Medno1' ]." ".$rowsMed[ 'Medno2' ]." ".$rowsMed[ 'Medap1' ]." ".$rowsMed[ 'Medap2' ];
			$especialidadMedico = $rowsMed[ 'Espnom' ];
			$nroDocumentoMedico = $rowsMed[ 'Meddoc' ];
			$registroMedico = $rowsMed[ 'Medreg' ];
			
			dimensionesImagen($rowmed['Meddoc']);
			
			if(file_exists("../../images/medical/hce/{$rowsMed[ 'Meddoc' ]}.png"))
				$firmaMedico = "<img src='../../images/medical/hce/{$rowsMed[ 'Meddoc' ]}.png' width='$anchoimagen' heigth='$altoimagen'>";	//$infoMedico[ 'Firma' ]; //*****Aun no se sabe la tabla de firma
			else
				$firmaMedico = "";
			
			echo "<div id='dv{$rowsCTC[ 'Ctchis' ]}_{$rowsCTC[ 'Ctcing' ]}_{$rowsCTC[ 'Ctcart' ]}'>";
			echo "</div>";
			echo "<script>";
			echo "consultarPrescripcionCTC( '{$rowsCTC[ 'Ctchis' ]}', '{$rowsCTC[ 'Ctcing' ]}', '{$rowsCTC[ 'Ctcart' ]}', 'dv{$rowsCTC[ 'Ctchis' ]}_{$rowsCTC[ 'Ctcing' ]}_{$rowsCTC[ 'Ctcart' ]}', $nroDocumentoMedico, '{$rowsCTC[ 'Ctcfkx' ]}' );";
			echo "</script>";
			
			/************************************************************************************************
			 * Busco todos los datos necesarios antes de la impresión
			 ************************************************************************************************/
			
			list( $anoSolicitud, $mesolicitud, $diaSolicitud ) = explode( "-", date("Y-m-d") );	//$rowsCTC[ 'Ctcfso' ] );
			
			
			//Consultando la entidad promotora de salud
			$entidadPromotoraSalud = $rowsCTC[ '' ];
			
			
			
			$tipoUsuario = $rowsCTC[ 'Ctctus' ];
			$noAfiliacion = $rowsCTC[ 'Ctcnoa' ];
			$tipoSolicitud = $rowsCTC[ 'Ctctso' ];
			
			/********************************************************************************
			 * Tipo de atención (hospitalario, ambulatorio, hospitalario urgente)
			 ********************************************************************************/
			$hospitalario = "";
			$ambulatorio = "";
			$hospitalarioUrgente = "";
			
			switch( $rowsCTC[ 'Ctctat' ] ){
				
				case 'HOSPITALARIO':
					$hospitalario = 'X';
					break;
				
				case 'AMBULATORIO':
					$ambulatorio = "X";
					break;
					
				case 'HOSPITALARIOURGENTE':
					$hospitalarioUrgente = "X";
					break;
				
				default: break;
			}
			
			$nombrePaciente = $rowsCTC[ 'Pacno1' ]." ".$rowsCTC[ 'Pacno2' ];
			$apellidosPaciente = $rowsCTC[ 'Pacap1' ]." ".$rowsCTC[ 'Pacap2' ];
			
			$nroDocumento =  $rowsCTC[ 'Pactid' ]." ".$rowsCTC[ 'Pacced' ];
			$historia = $rowsCTC[ 'Ctchis' ];
			$ingreso = $rowsCTC[ 'Ctcing' ];
			
			$edad = calcularEdad( $rowsCTC[ 'Pacnac' ] );
			$cama = $rowsCTC[ 'Ubihac' ];
			
			//Consulto los diagnosticos del paciente
			//$diagnosticos = consultarDatosTablaHCE( $conex, $whce, "000051", 156, $historia, $ingreso );
			// $diagnosticos = strip_tags( consultarDatosTablaHCE( $conex, $whce, "000069", 49, $historia, $ingreso ) );
			// $diagnosticos = strip_tags( consultarDiagnostico( $conex, $whce, $historia, $ingreso ) );
			$diagnosticos = $rowsCTC[ 'Ctcdxc' ];;
			
			//Enfermedad de alto riesgo? es booleano
			if( $rowsCTC[ 'Ctcear' ] == 'on' ){
				$enfermedadAltoRiesgoSi = "X";
				$enfermedadAltoRiesgoNo = "";
			}
			else{
				$enfermedadAltoRiesgoSi = "";
				$enfermedadAltoRiesgoNo = "X";
			}
			
			$unidadSolicitaMedicamento = $rowsCTC[ 'Ubisac' ]." - ".$rowsCTC[ 'Cconom' ];
			//$descripcionCasoClinico = consultarDatosTablaHCE( $conex, $whce, "000051", 4, $historia, $ingreso );;
			$descripcionCasoClinico = $rowsCTC[ 'Ctcdcc' ];
			$observacionesRespuestaClinicaPos = htmlentities( $rowsCTC[ 'Ctcorp' ] );
			$principioActivoPos = htmlentities( $rowsCTC[ 'Ctcpap' ] );
			
			$posologiaPos = htmlentities( $rowsCTC[ 'Ctcpop' ] );
			$presentacionPos = htmlentities( $rowsCTC[ 'Ctcprp' ] );
			$dosisDiaPos = htmlentities( $rowsCTC[ 'Ctcddp' ] );
			$cantidadPos = htmlentities( $rowsCTC[ 'Ctccap' ] );
			
			$tiempoTratamientoPos = htmlentities( $rowsCTC[ 'Ctcttp' ] );
			$principioActivoAlternativa = htmlentities( $rowsCTC[ 'Ctcpaa' ] );
			$posologiaAlternativa = htmlentities( $rowsCTC[ 'Ctcpoa' ] );
			$presentacionAlternativa = htmlentities( $rowsCTC[ 'Ctcpra' ] );
			$dosisDiaAlternativa = htmlentities( $rowsCTC[ 'Ctcdda' ] );
			$cantidadAlternativa = $rowsCTC[ 'Ctccaa' ];
			$tiempoTratamientoAlternativa = htmlentities( $rowsCTC[ 'Ctctta' ] );
			
			//Respuesta clinica Pos
			$noMejoria = "";
			$reaccionAdversa = "";
			$intolerancia = "";
			$noAplica = "";
			
			switch( $rowsCTC[ 'Ctcrcp' ] ){
			
				case 'NO MEJORIA':
					$noMejoria = "X";
					break;
				
				case 'REACCION ADVERSA':
					$reaccionAdversa = "X";
					break;
					
				case 'INTOLERANCIA':
					$intolerancia = "X";
					break;
					
				default:
					$noAplica = "X";
					break;
			}
			
			//Existe riesgo, campo booleano
			if( $rowsCTC[ 'Ctcerp' ] == "on" ){
				$existeRiesgoPosSi = "X";
				$existeRiesgoPosNo = "";
			}
			else{
				$existeRiesgoPosSi = "";
				$existeRiesgoPosNo = "X";
			}
			
			$principioActivoNoPos =  htmlentities( $rowsCTC[ 'Ctcpan' ] );
			$posologiaNoPos =  htmlentities( $rowsCTC[ 'Ctcpon' ] );//." ".$rowsCTC[ 'Kadufr' ];
			$presentacionNoPos =  htmlentities( $rowsCTC[ 'Ctcprn' ] );
			$dosisDiaNoPos =  htmlentities( $rowsCTC[ 'Ctcddn' ] );
			$tiempoTratamientoNoPos =  htmlentities( $rowsCTC[ 'Ctcttn' ] );
			$cantidadTotalNoPos = $rowsCTC[ 'Ctccan' ];
			$nombreComercialNoPos = $rowsCTC[ 'Artcom' ];
			
			$categoriaNoPos = $rowsCTC[ 'Ctccfn' ];
			$registroInvimaNoPos = $rowsCTC[ 'Ctcrin' ];
			$efectoTerapeuticoDeseadoNoPos =  htmlentities( $rowsCTC[ 'Ctcedt' ] );
			$tiempoEsperadoNoPos =  htmlentities( $rowsCTC[ 'Ctctre' ] );
			$efectosSecundariosNoPos =  htmlentities( $rowsCTC[ 'Ctcert' ] );
			$grupoTerapeuticoReemplazo = $rowsCTC[ 'Ctcgte' ];
			
			$principioActivoReemplazo = $rowsCTC[ 'Ctcpar' ];
			
			$presentacionReemplazo = $rowsCTC[ 'Ctcprr' ];
			$dosisDiaReemplazo = $rowsCTC[ 'Ctcddr' ];
			$tiempoRespuestaReemplazo =  htmlentities( $rowsCTC[ 'Ctcttr' ] );
			$bibliografia =  htmlentities( $rowsCTC[ 'Ctcbbo' ] );
			$observacionesExisteRiesgoPos =  htmlentities( $rowsCTC[ 'Ctcoer' ] );
			
			/************************************************************************************************/
			
			
			
			
			/****************************************************************************************************
			 * Impresion del formulario
			 ****************************************************************************************************/
			
			?>
			
			<table width="712" border="1" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="708" height="70"><table width="709" height="48" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td width="148"><img src='../../images/medical/root/<?php echo $institucion->baseDeDatos; ?>.jpg' width=148 heigth=53></td>
					<td width="561" align="center"><b>JUSTIFICACIÓN DE MEDICAMENTOS NO INCLUIDOS EN EL PLAN OBLIGATORIO DE SALUD</b></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td colspan="3"><div align="center">FECHA</div></td>
					<td rowspan="2"><div align="center">ENTIDAD PROMOTORA DE SALUD</div></td>
				  </tr>
				  <tr>
					<td><div align="center">DIA</div></td>
					<td><div align="center">MES</div></td>
					<td><div align="center">AÑO</div></td>
					</tr>
				  <tr>
					<td align="center"><b><?php echo $diaSolicitud;?></b></td>
					<td align="center"><b><?php echo $mesolicitud;?></b></td>
					<td align="center"><b><?php echo $anoSolicitud;?></b></td>
					<td>&nbsp;<b><?php echo $entidadPromotoraSalud;?></b></td>
				  </tr>
				  
				  <tr>
					<td colspan="4"><div align="center">
					  
					  <table width="661" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="90">TIPO USUARIO</td>
						  <td width="199" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $tipoUsuario;?></b></td>
						  <td width="100">No. AFILIACION</td>
						  <td width="100" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $noAfiliacion;?></b></td>
						  <td width="120">TIPO DE SOLICITUD</td>
						  <td width="58" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $tipoSolicitud;?></b></td>
						</tr>
					  </table>
					  <br>
					  <table width="683" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="124">HOSPITALARIO</td>
						  <td width="60" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $hospitalario;?></b></td>
						  <td width="122">AMBULATORIO</td>
						  <td width="64" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $ambulatorio;?></b></td>
						  <td width="193">HOSPITALARIO URGENTE</td>
						  <td width="106" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $hospitalarioUrgente;?></b></td>
						</tr>
					  </table>
					</div></td>
					</tr>
				</table></td>
			  </tr>
			  <tr>
				<td ><div align="center"><b>DATOS DEL PACIENTE</b></div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td colspan="4">NOMBRE DEL PACIENTE</td>
					<td width="227" rowspan="7">&nbsp;</td>
				  </tr>
				  <tr>
					<td colspan="4">&nbsp;<b><?php echo $nombrePaciente;?></b></td>
					</tr>
				  <tr>
					<td colspan="4">APELLIDOS DEL PACIENTE</td>
					</tr>
				  <tr>
					<td colspan="4">&nbsp;<b><?php echo $apellidosPaciente;?></b></td>
					</tr>
				  <tr>
					<td width="82"><div align="center">EDAD</div></td>
					<td width="132"><div align="center">IDENTIFICACION</div></td>
					<td width="161"><div align="center">HISTORIA CLINICA</div></td>
					<!-- <td width="94"><div align="center">CAMA</div></td> -->
					</tr>
				  <tr>
					<td align="center">&nbsp;<b><?php echo $edad;?></b></td>
					<td align="center">&nbsp;<b><?php echo $nroDocumento;?></b></td>
					<td align="center">&nbsp;<b><?php echo $historia."-".$ingreso;?></b></td>
					<!-- <td align="center">&nbsp;<b><?php /*echo $cama;*/?></b></td> -->
					</tr>
				  <tr>
					<td colspan="4">DIAGNOSTICOS
						<BR><b><?php echo $diagnosticos;?></b><BR>
					  </td>
					</tr>
				  <tr>
					<td colspan="2">ENFERMEDAD DE ALTO COSTO</td>
					<td colspan="3">UNIDAD QUE SOLICITA EL MEDICAMENTO</td>
				  </tr>
				  <tr>
					<td colspan="2"><table width="200" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="20">SI</td>
						  <td width="73" style="border-bottom:solid 1px;"><b><?php echo $enfermedadAltoRiesgoSi;?></b></td>
						  <td width="33">NO</td>
						  <td width="73" style="border-bottom:solid 1px;"><b><?php echo $enfermedadAltoRiesgoNo;?></b></td>
						</tr>
					  </table>
					</td>
					<td colspan="3">&nbsp;<b><?php echo $unidadSolicitaMedicamento;?></b></td>
				  </tr>
				  <tr>
					<td colspan="5">DESCRIPCION DEL CASO CLINICO
					<BR><b><?php echo $descripcionCasoClinico;?></b><BR>
					  </td>
					</tr>
				</table></td>
			  </tr>
			  <tr>
				<td><div align="center"><b>MEDICAMENTOS POS PREVIAMENTE UTILIZADOS</b></div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td>PRINCIPIO ACTIVO: <b><?php echo $principioActivoPos;?></b></td>
					<td>POSOLOGIA: <b><?php echo $posologiaPos;?></b></td>
					<td>PRESENTACION: <b><?php echo $presentacionPos;?></b></td>
				  </tr>
				  <tr>
					<td>DOSIS/DIA: <b><?php echo $dosisDiaPos;?></b></td>
					<td>CANTIDAD: <b><?php echo $cantidadPos;?></b></td>
					<td>TIEMPO TRATAMIENTO: <b><?php echo $tiempoTratamientoPos;?></b></td>
				  </tr>
				  
				</table></td>
			  </tr>
			  <tr>
				<td><div align="center"><b>NO EXISTEN ALTERNATIVAS EN EL POS</b></div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td>PRINCIPIO ACTIVO: <b><?php echo $principioActivoAlternativa;?></b></td>
					<td>POSOLOGIA: <b><?php echo $posologiaAlternativa;?></b></td>
					<td>PRESENTACION: <b><?php echo $presentacionAlternativa;?></b></td>
				  </tr>
				  <tr>
					<td>DOSIS/DIA: <b><?php echo $dosisDiaAlternativa;?></b></td>
					<td>CANTIDAD: <b><?php echo $cantidadAlternativa;?></b></td>
					<td>TIEMPO TRATAMIENTO: <b><?php echo $tiempoTratamientoAlternativa;?></b></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td><b>RESPUESTA CLINICA Y PARACLINICA ALCANZADA CON MEDICAMENTOS POS</b></td>
			  </tr>
			  <tr>
				<td><div align="center">
					<table width="678" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td width="77">No mejoría</td>
						<td width="73" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $noMejoria;?></b></td>
						<td width="124">Reacción adversa</td>
						<td width="99" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $reaccionAdversa;?></b></td>
						<td width="84">Intolerancia</td>
						<td width="68" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $intolerancia;?></b></td>
						<td width="68">No aplica</td>
						<td width="67" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $noAplica;?></b></td>
					  </tr>
						  </table>
				  </div>
				  <BR>Observaciones
				  <BR><b><?php echo $observacionesRespuestaClinicaPos;?></b><BR>
				</td>
			  </tr>
			  <tr>
				<td><table width="712" border="0" cellspacing="0" cellpadding="0">
					<tr>
					  <td width="533">EXISTE RIESGO INMINENTE PARA LA SALUD Y LA VIDA DEL PACIENTE:</td>
					  <td width="29">SI</td>
					  <td width="56" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $existeRiesgoPosSi;?></b></td>
					  <td width="36">NO</td>
					  <td width="59" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $existeRiesgoPosNo;?></b></td>
					</tr>
				  </table>
				  </td>
			  </tr>
			  <tr>
				<td>&nbsp;<b><?php echo $observacionesExisteRiesgoPos;?></b></td>
			  </tr>
			  
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			<!--- </table>
			
			<div style='page-Break-After:always'></div>
			
			<table width="712" border="1" cellspacing="0" cellpadding="0"> -->
			  <tr>
				<td><div align="center">MEDICAMENTO NO POS SOLICITADO</div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td>PRINCIPIO ACTIVO</td>
					<td>POSOLOGIA</td>
					<td>PRESENTACION Y FORMA FARMACEUTICA</td>
				  </tr>
				  <tr>
					<td>&nbsp;<b><?php echo $principioActivoNoPos;?></b></td>
					<td>&nbsp;<b><?php echo $posologiaNoPos?></b></td>
					<td>&nbsp;<b><?php echo $presentacionNoPos;?></b></td>
				  </tr>
				  <tr>
					<td>DOSIS/DIA: <b><?php echo $dosisDiaNoPos;?></b></td>
					<td>TIEMPO DE TRATAMIENTO: <b><?php echo $tiempoTratamientoNoPos;?> d&iacute;as</b></td>
					<td>CANTIDAD TOTAL: <b><?php echo $cantidadTotalNoPos;?></b></td>
				  </tr>
				  <tr>
					<td>NOMBRE COMERCIAL</td>
					<td>CATEGORIA FARMACEUTICA</td>
					<td>REGISTRO INVIMA</td>
				  </tr>
				  <tr>
					<td>&nbsp;<b><?php echo $nombreComercialNoPos;?></b></td>
					<td>&nbsp;<b><?php echo $categoriaNoPos;?></b></td>
					<td>&nbsp;<b><?php echo $registroInvimaNoPos;?></b></td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td><div align="center">INDICACIONES TERAPEÚTICAS CON MEDICAMENTOS NO POS:</div></td>
			  </tr>
			  <tr>
				<td>EFECTO TERAPEÚTICO DESEADO AL TRATAMIENTO:
				  <br>&nbsp;<b><?php echo $efectoTerapeuticoDeseadoNoPos;?></b>
				</td>
			  </tr>
			  <tr>
				<td>TIEMPO DE RESPUESTA ESPERADO:
				  <br>&nbsp;<b><?php echo $tiempoEsperadoNoPos;?></b>
				</td>
			  </tr>
			  <tr>
				<td>EFECTOS SECUNDARIOS Y POSIBLES RIESGOS AL TRATAMIENTO:
				<br>&nbsp;<b><?php echo $efectosSecundariosNoPos;?></b>
				</td>
			  </tr>
			  <tr>
				<td>MEDICAMENTOS EN EL PLAN OBLIGATORIO DE SALUD DEL MISMO GRUPO TERAPEUTICO QUE REEMPLAZA O SUSTITUYE EL MEDICAMENTO NO POS SOLICITADO</td>
			  </tr>
			  <tr>
				<td>
					<br>
				  <table width="588" border="0" cellspacing="0" cellpadding="0" align=center>
					<tr>
					  <td width="128">Grupo terapeútico:</td>
					  <td width="140" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $grupoTerapeuticoReemplazo;?></b></td>
					  <td width="117">Principio activo:</td>
					  <td width="203" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $principioActivoReemplazo;?></b></td>
					</tr>
				  </table>
				  <br>
				  <table width="692" border="0" cellspacing="0" cellpadding="0" align=center>
					<tr>
					  <td width="96">Presentacion</td>
					  <td width="134" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $presentacionReemplazo;?></b></td>
					  <td width="73">Dosis día</td>
					  <td width="132" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $dosisDiaReemplazo;?></b></td>
					  <td width="140">Tiempo de tratamiento</td>
					  <td width="117" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $tiempoRespuestaReemplazo;?></b></td>
					</tr>
				  </table>
				  <br>
				</td>
			  </tr>
			  <tr>
				<td>BIBLIOGRAFIA
				<br>
				<b><?php echo $bibliografia;?></b>
				<br>
				</td>
			  </tr>
			  <tr>
				<td><div align="center">MEDICO TRATANTE</div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td><div align="center">NOMBRE COMPLETO</div></td>
					<td><div align="center">ESPECIALIDAD</div></td>
					<td><div align="center">No. CÉDULA</div></td>
					<td><div align="center">REGISTRO MEDICO</div></td>
				  </tr>
				  <tr>
					<td>&nbsp;<b><?php echo $nombreMedico;?></b></td>
					<td align="center">&nbsp;<b><?php echo $especialidadMedico;?></b></td>
					<td align="center">&nbsp;<b><?php echo $nroDocumentoMedico;?></b></td>
					<td align="center">&nbsp;<b><?php echo $registroMedico;?></b></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td><p>FIRMA Y SELLO</p>
				&nbsp;<b><?php echo $firmaMedico;?></b></td>
			  </tr>
			</table>
			<?php
			 
			/****************************************************************************************************/
			
			
			
			//marco como impreso el articulo
			cambiarEsadoImpresionPorId( $conex, $wbasedato, $rowsCTC[ 'id' ], $wusuario );
		}
		
		echo "</div>";
	}
	
	echo "</form>";
}
?>
</body>
</html>