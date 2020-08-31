<?php
include_once("conex.php");  header("Content-Type: text/html;charset=ISO-8859-1"); ?>
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

/**
 *
 */
function consultarPrescripcionCTC( his, ing, art, div, id ){

	var vwemp_pmla = document.getElementById( "wemp_pmla" );
	
	if( true ){
									
		var parametros = "whistoria="+his+"&wingreso="+ing+"&pro="+art+"&ide="+id;
		
		//hago la grabacion por ajax del articulo
		consultasAjax( "POST", "ordenes_imp.php?wemp_pmla="+vwemp_pmla.value, 
						parametros, 
						true, 
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
							
								//Esta función llena los datos del protocolo
								document.getElementById( div ).innerHTML = ajax.responseText+"<div style='page-Break-After:always'></div>";
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
}
</script>

<style>
  td {
    font-family: Arial;
    font-size: 6.5pt;
  }
</style>

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
 
function dimensionesImagen($codmed)
{
	global $altoimagen;
	global $anchoimagen;

	if(file_exists('../../images/medical/hce/Firmas/'.$codmed.'.png')) {
		// Obtengo las propiedades de la imagen, ancho y alto
		list($widthimg, $heightimg) = getimagesize('../../images/medical/hce/Firmas/'.$codmed.'.png');
	} else {
		$widthimg = '181';
		$heightimg = '27';
	}
	
	$anchoimagen = '181';
	
	//$altoimagen = floor( (181 * $heightimg/$widthimg) );
	$altoimagen = $heightimg + floor( ( $anchoimagen-$widthimg )*($heightimg/$widthimg) );
	
	// if($altoimagen>54)
		// $altoimagen = 54;
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
				{$wbasedato}_000135
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

	$sql = "UPDATE {$wbasedato}_000135
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

	$sql = "UPDATE {$wbasedato}_000135
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

// Función que retorna la edad con base en la fecha de nacimiento
function calcularEdad($fechaNacimiento) 
{
	$ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($aa - $ann)/360;
	$meses=(($aa - $ann) % 360)/30;
	if ($ann1<1){
		$dias1=(($aa - $ann) % 360) % 30;
		// $wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		$wedad=(string)(integer)$meses." mes(es) ";
	} else {
		$dias1=(($aa - $ann) % 360) % 30;
		//$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
		$wedad=(string)(integer)$ann1." a&ntilde;o(s) ";
	}

	return $wedad;
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

$institucion = consultarInstitucionPorCodigo($conex,$wemp_pmla);

if( $consultaAjax ){	//si hay ajax

	
}
else{	//si no hay ajax

	include_once("root/montoescrito.php");

	echo "<form>";
	
	if( !isset($imprimir) ){
	
		$wactualiz = "Octubre 11 de 2012";
		
		encabezado("IMPRESION FORMULARIOS CTC DE PROCEDIMIENTOS",$wactualiz, "clinica");
		
		//Busca los cco que se tienen medicamentos a imprimir
		$sql = "SELECT
					Ubihac, Ubisac, Cconom, Ctchis, Ctcing, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*
				FROM
					{$wbasedato}_000135 a, {$whce}_000017 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f
				WHERE "
					//." ctcimp = 'off' "
					." ctcest = 'on'
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = codigo
					AND ubisac = ccocod
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
				ORDER BY
					ubisac, ubihac
				";
				
		$sql = "SELECT
					Ubihac, Ubisac, Cconom, Ctchis, Ctcing, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, a.*
				FROM
					{$wbasedato}_000135 a, {$whce}_000047 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f
				WHERE "
					//." ctcimp = 'off' "
					." ctcest = 'on'
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = codigo
					AND ubisac = ccocod
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
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
			
			for( $i = 0;; ){
				
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
						echo "<td>Procedimientos</td>";
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
								
								echo "<td align='center'><a href='impresionCTCProcedimientosNoPos.php?wemp_pmla=$wemp_pmla&imprimir=on&historia={$hisPacientes[ 'his' ]}'>imprimir</a></td>";
								
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
		
		//Si la historia es vacia, significa que imprime todo
		if( empty( $historia ) ){
			$historia = '%';
		}
		
		//Si el cco es vacio, significa que imprime todo
		if( empty( $cco ) ){
			$cco = '%';
		}
		
		//Consulto los articulos a imprimir
		$sql = "SELECT
					Codigo, Descripcion, Codcups, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ubisac, Ubihac, a.*, Ingtel, Ingnre
				FROM
					{$wbasedato}_000135 a, {$whce}_000017 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000016 g
				WHERE "
					//." ctcimp = 'off' "
					." ctcest = 'on'
					AND orihis = ctchis
					AND oriing = ctcing
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND inghis = ubihis
					AND inging = ubiing
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = Codigo
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco' 
			   GROUP BY ctcpro 
				";
				
		//Consulto los articulos a imprimir
		$sql = "SELECT
					Codigo, Descripcion, Codcups, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ubisac, Ubihac, a.*, Ingtel, Ingnre
				FROM
					{$wbasedato}_000135 a, {$whce}_000047 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000016 g
				WHERE "
					//." ctcimp = 'off' "
					." ctcest = 'on'
					AND orihis = ctchis
					AND oriing = ctcing
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND inghis = ubihis
					AND inging = ubiing
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = Codigo
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco' 
			   GROUP BY ctcpro 
				";
				
		//Consulto los articulos a imprimir
		$sql = "SELECT
					Codigo, Descripcion, Codcups, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ubisac, Ubihac, a.*, Ingtel, Ingnre
				FROM
					{$wbasedato}_000135 a, {$whce}_000047 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000016 g, {$whce}_000027 h,  {$whce}_000028 i
				WHERE "
					//." ctcimp = 'off' "
					." ctcest = 'on'
					AND orihis = ctchis
					AND oriing = ctcing
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND inghis = ubihis
					AND inging = ubiing
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ctcpro = Codigo
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco' 
					AND ordtor = dettor
					AND detnro = ctcnro
					AND dettor = ctctor
					AND detest = 'on'
					AND ordest = 'on'
					AND ctcpro = '$exa'
					AND detcod = '$exa'
					AND CONCAT( Dettor,'-',Detnro ) = '$ordnro'
			   GROUP BY ctcpro, ctctor, ctcnro 
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
		echo "<div style='width:20cm' align='center'>";
		
		echo "<INPUT type='hidden' name='wemp_pmla' id='wemp_pmla' value='$wemp_pmla'>";
		
		$rowsCTC = mysql_fetch_array( $res );
		
			//Hago un salgo de linea si se va a imprimir otra factura
			// if( $i > 0 ){
				// echo "<div style='page-Break-After:always'></div>";
			// }
			
			//Consulto la información del medico
			// $rowsMed = consultarInformacionMedico( $conex, $wbasedato, $rowsCTC[ 'Ctcmed' ] );
			$rowsMed = consultarInformacionMedico( $conex, $wbasedato, $wusuario );
			$nombreMedico = $rowsMed[ 'Medno1' ]." ".$rowsMed[ 'Medno2' ]." ".$rowsMed[ 'Medap1' ]." ".$rowsMed[ 'Medap2' ];
			$especialidadMedico = $rowsMed[ 'Espnom' ];
			$nroDocumentoMedico = $rowsMed[ 'Meddoc' ];
			$registroMedico = $rowsMed[ 'Medreg' ];
			
			dimensionesImagen($rowsCTC[ 'Ctcmed' ]);
			
			//Firma			
			if(file_exists('../../images/medical/hce/Firmas/'.$rowsCTC[ 'Ctcmed' ].'.png')){
				//$firmaMedico = "<img src='../../images/medical/hce/Firmas/{$rowsCTC[ 'Ctcmed' ]}.png' width='$anchoimagen' height='$altoimagen'>";
				$firmaMedico = "&nbsp;";
			}else{
				$firmaMedico = "&nbsp;";
			}
			/************************************************************************************************/
			
			echo "<div id='dv{$rowsCTC[ 'Ctchis' ]}_{$rowsCTC[ 'Ctcing' ]}_{$rowsCTC[ 'Ctcpro' ]}'>";
			echo "</div>";
			echo "<script>";
			echo "consultarPrescripcionCTC( '{$rowsCTC[ 'Ctchis' ]}', '{$rowsCTC[ 'Ctcing' ]}', '{$rowsCTC[ 'Ctcpro' ]}', 'dv{$rowsCTC[ 'Ctchis' ]}_{$rowsCTC[ 'Ctcing' ]}_{$rowsCTC[ 'Ctcpro' ]}', $nroDocumentoMedico );";
			echo "</script>";
			
			/************************************************************************************************
			 * Busco todos los datos necesarios antes de la impresión
			 ************************************************************************************************/
			$nombrePaciente = $rowsCTC[ 'Pacno1' ]." ".$rowsCTC[ 'Pacno2' ];
			$apellido1Paciente = $rowsCTC[ 'Pacap1' ];
			$apellido2Paciente = $rowsCTC[ 'Pacap2' ];
			
			$nroDocumento =  $rowsCTC[ 'Pacced' ];
			$tipDocumento =  $rowsCTC[ 'Pactid' ];
			
			$telefonoPaciente =  $rowsCTC[ 'Ingtel' ];
			$responsablePaciente =  $rowsCTC[ 'Ingnre' ];
			$edadPaciente = calcularEdad($rowsCTC[ 'Pacnac' ]);
			
			$historia = $rowsCTC[ 'Ctchis' ];
			$ingreso = $rowsCTC[ 'Ctcing' ];
			
			$fechaSolicitud = date("Y-m-d");	//$rowsCTC[ 'Ctcfge' ];
			
			// $diagnosticoCie10 = strip_tags( str_ireplace( "</OPTION>", "<br>", consultarDatosTablaHCE( $conex, $whce, "000051", 156, $historia, $ingreso ) ), "<br>" );
			$diagnosticoCie10 = strip_tags( consultarDiagnostico( $conex, $whce, $historia, $ingreso ) );
			$descripcionCasoClinico = consultarDatosTablaHCE( $conex, $whce, "000051", 4, $historia, $ingreso );
			$descripcionCasoClinico = $rowsCTC[ 'Ctcdcc' ];
			
			$fechaProcedimiento1 = $rowsCTC[ 'Ctcpp1' ];
			$razonProcedimiento1 = $rowsCTC[ 'Ctcrp1' ];
			
			$fechaProcedimiento2 = $rowsCTC[ 'Ctcpp2' ];
			$razonProcedimiento2 = $rowsCTC[ 'Ctcrp2' ];
			
			$nombreProcedimiento = $rowsCTC[ 'Descripcion' ];
			$cups = $rowsCTC[ 'Codcups' ];
			
			$frecuenciaUso = $rowsCTC[ 'Ctcfus' ];
			$cantidadSolicitada = $rowsCTC[ 'Ctccas' ];
			$diasTratamiento = $rowsCTC[ 'Ctcdtt' ];
			
			$justificacion = $rowsCTC[ 'Ctcjus' ];
			
			/************************************************************************************
			 * tipo de atención
			 ************************************************************************************/
			$ambulatorio = "";
			$hospitalario = "";
			$urgencias = "";
			
			switch( strtoupper( $rowsCTC[ 'Ctctat' ] ) ){
				
				case 'HOSPITALARIO':{
					$ambulatorio = "";
					$hospitalario = "X";
					$urgencias = "";
				} break;
				
				case 'AMBULATORIO': {
					$ambulatorio = "X";
					$hospitalario = "";
					$urgencias = "";
				} break;

				case 'URGENCIAS': {
					$ambulatorio = "";
					$hospitalario = "";
					$urgencias = "X";
				} break;				
			}
			/************************************************************************************/
			
			/************************************************************************************
			 * Tipo de servicio
			 ************************************************************************************/
			$servicioUnico = "";
			$servicioConRepeticion = "";
			$servicioSucesivo = "";
			
			switch( strtoupper( $rowsCTC[ 'Ctctse' ] ) ){
				
				case 'UNICOCONREPETICION':{
					$servicioUnico = "";
					$servicioConRepeticion = "X";
					$servicioSucesivo = "";
				} 
				break;
				
				case 'SUCESIVO': {
					$servicioUnico = "";
					$servicioConRepeticion = "";
					$servicioSucesivo = "X";
				} 
				break;
				
				case 'UNICO': {
					$servicioUnico = "X";
					$servicioConRepeticion = "";
					$servicioSucesivo = "";
				} 
				break;
			}
			/************************************************************************************/
			
			
			/******************************************************************************************
			 * Proposito de lo solicitado
			 ******************************************************************************************/
			$promocion = "";
			$prevencion = "";
			$diagnostico = "";
			$tratamiento = "";
			$rehabilitacion = "";
			
			switch( strtoupper( $rowsCTC[ 'Ctcpso' ] ) ){
			
				case 'PROMOCION':{
					$promocion = "X";
				} 
				break;
				
				case 'PREVENCION': {
					$prevencion = "X";
				} 
				break;
				
				case 'DIAGNOSTICO': {
					$diagnostico = "X";
				} 
				break;
				
				case 'TRATAMIENTO': {
					$tratamiento = "X";
				} 
				break;
				
				case 'REHABILITACION': {
					$rehabilitacion = "X";
				} 
				break;
			}
			/******************************************************************************************/
			
			
			/****************************************************************************************************
			 * Impresion del formulario
			 ****************************************************************************************************/
			 
			 $diaOrden = date("d");
			 $mesOrden = date("m");
			 $anioOrden = date("Y");
			 
			 $siRPNU = $noRPNU = "";
			 ( strtoupper( $rowsCTC[ 'Ctcepo' ] ) != 'NO' ) ? $siRPNU = "X": $noRPNU = "X";
			 
			 $razonesParaNoUtilizarlas = $rowsCTC[ 'Ctcrnu' ];
			
			?>
			
			<table width="672" border="0" style="border-collapse:collapse" cellpadding="0">
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td width="165"><img src='../../images/medical/root/<?php echo $institucion->baseDeDatos; ?>.jpg' width=148 heigth=53></td>
					<td width="367" align="center">
						<p align="center"><h2>JUSTIFICACION INSUMOS Y PROCEDIMIENTOS NO POS</h2></p>
						<b>Fecha </b> &nbsp; <table width="240" border="1" style="border-collapse:collapse;border-style:solid;height:21px" cellpadding="0"><tr><td align="center">D&iacute;a </b> </td><td align="center"><?php echo $diaOrden; ?> </td><td align="center">Mes </b> </td><td align="center"><?php echo $mesOrden; ?> </td><td align="center"><b> A&ntilde;o </b> </td><td align="center"><?php echo $anioOrden; ?> </td></tr></table><br />
					</td>
				  </tr>
				</table>				
				<br /><br />
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td width="270" colspan="3"><b>Nombre del paciente</b></td>
					<td width="221" colspan="3"><b>Primer Apellido</b></td>
					<td width="221" colspan="2"><b>Segundo Apellido</b></td>
				  </tr>
				  <tr>
					<td colspan="3"><?php echo $nombrePaciente; ?></td>
					<td colspan="3"><?php echo $apellido1Paciente; ?></td>
					<td colspan="2"><?php echo $apellido2Paciente; ?></td>
				  </tr>
				  <tr>
					<td width="100"><b>Nro Identificaci&oacute;n </b></td>
					<td colspan="3"><?php echo $nroDocumento; ?></td>
					<td width="80"><b>Tipo Id </b></td>
					<td><?php echo $tipDocumento; ?></td>
					<td width="120"><b>Nro Historia Cl&iacute;nica </b></td>
					<td><?php echo $historia; ?></td>
				  </tr>
				  <tr>
					<td><b>Edad </b></td>
					<td><?php echo $edadPaciente; ?></td>
					<td><b>EPS </b></td>
					<td colspan="3"><?php echo $responsablePaciente; ?></td>
					<td><b>Tel&eacute;fono </b></td>
					<td><?php echo $telefonoPaciente; ?></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td width="112"><b>Diagnóstico: </b></td>
					<td width="600" colspan="7"><?php echo $diagnosticoCie10;?></td>
				  </tr>
				  <tr>
					<td width="162" colspan="2">Solicitud de Tratamiento: </td>
					<td width="150" align="center"> Ambulatorio: </td>
					<td width="33" align="center"> <?php echo $ambulatorio;?> </td>
					<td width="150" align="center"> Hospitalario: </td>
					<td width="33" align="center"> <?php echo $hospitalario;?> </td>
					<td width="150" align="center"> Urgencias: </td>
					<td width="34" align="center"> <?php echo $urgencias;?> </td>
				  </tr>
				</table>
				</td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td><b>Caso Clínico:</b></td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td height="31px"><?php echo $descripcionCasoClinico;?></td>
				  </tr>
				</table>
				</td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>


			  <tr>
				<td><b>PROCEDIMIENTO O INSUMO NO POS SOLICITADO</b></td>
			  </tr>
			  <tr>
				<td style="border-style:solid;border-top-width:1px;border-left-width:1px;border-right-width:1px;border-bottom-width:1px">
				  <table width="712" border="0">
					<tr>
					  <td width="420">Nombre: <b><?php echo $nombreProcedimiento;?></b></td>
					  <td width="282">CUPS ó Reg. INVIMA: <b><?php echo $cups;?></b></td>
					</tr>
				  </table>      
				</td>
			  </tr>
				  
			  <tr>
				<td>&nbsp;</td>
			  </tr>

			  <tr>
				<td><b>JUSTIFICACION PARA EL USO DEL PROCEDIMIENTO O INSUMO NO POS SOLICITADO</b></td>
			  </tr>
			  <tr>
				<td style="border-style:solid;border-top-width:1px;border-left-width:1px;border-right-width:1px;border-bottom-width:1px">
					<?php echo $justificacion;?>
				</td>
			  </tr>
			  
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td><b>OPCIONES POS (DESCRIBIRLAS)</b></td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse;border-style:solid" cellpadding="0">
				  <tr>
					<td width="162">Existen Opciones POS: </td>
					<td width="70" align="center"> No: </td>
					<td width="30" align="center"> <?php echo $noRPNU; ?> </td>
					<td width="70" align="center"> Si: </td>
					<td width="30" align="center"> <?php echo $siRPNU; ?> </td>
					<td width="350" align="center">  </td>
				  </tr>
				  <tr>
					<td colspan="6"> 
						Razones para no utilizarlas:<br />
						<?php echo $razonesParaNoUtilizarlas;?> 
					</td>
				  </tr>
				</table>
				</td>
			  </tr>

			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>
				<table width="712" border="1" style="border-collapse:collapse">
				  <tr>
					<td width="322"><p>Nombre Médico: <b><?php echo $nombreMedico;?></b></p>
					  <p>Documento identidad: <b><?php echo $nroDocumentoMedico;?></b></p>
					  <p>Especialidad: <b><?php echo $especialidadMedico;?></b></p>
					  <p>Registro Médico: <b><?php echo $registroMedico;?></b></p></td>
					<td width="184" align="center">
					<table width="165" height="120" border="0">
					  <tr>
						<td width="182" height="93" style="border-bottom-width:1px;border-bottom-style:solid">&nbsp;<b><?php echo $firmaMedico;?></b></td>
					  </tr>
					  <tr>
						<td align="center">Firma y sello</td>
					  </tr>
					</table></td>
					<td width="184">
					Fecha de Recibo:
					  <table width="165" height="101" border="0" cellspacing="0" align="center">
					  <tr>
						<td height="70" style="border-bottom-width:1px;border-bottom-style:solid">&nbsp;</td>
					  </tr>
					  <tr>
						<td height="23" align="center">Firma Recibo</td>
					  </tr>
					</table></td>
				  </tr>
				</table>
				<p>&nbsp;</p>    </td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			</table>
			
			<?php
			 
			/****************************************************************************************************/
			
			
			
			//marco como impreso el articulo
			cambiarEsadoImpresionPorId( $conex, $wbasedato, $rowsCTC[ 'id' ], $wusuario );
		
		
		echo "</div>";
	}
	
	echo "</form>";
}
?>