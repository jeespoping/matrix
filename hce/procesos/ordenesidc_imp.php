<?php
include_once("conex.php"); header("Content-Type: text/html;charset=ISO-8859-1"); ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>Ordenes Médicas</title>

  <script type='text/javascript'>

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

	// Llama al script de impresión de los CTC para que traiga la impresión del CTC
	// del artículo enviado
	function consultarCTCArticulo( his, ing, art, div )
	{
		var vwemp_pmla = document.getElementById( "wemp_pmla" );

		var parametros = "imprimir=on&historia="+his+"&art="+art;

		consultasAjax( "POST", "impresionCTCArticulosNoPosIDC.php?wemp_pmla="+vwemp_pmla.value,
						parametros,
						true,
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								document.getElementById( div ).innerHTML = '<div style="page-break-after: always;">'+ajax.responseText+'</div>';
							}
						}
					);
	}

	// Llama a este mismo script para hacer la impresión solo del medicamento correspondiente al CTC
	// para eso se envia el parámetro art
	function consultarArticulo( his, ing, art, div )
	{
		var vwemp_pmla = document.getElementById( "wemp_pmla" );

		var parametros = "whistoria="+his+"&wingreso="+ing+"&art="+art;

		consultasAjax( "POST", "ordenesidc_imp.php?wemp_pmla="+vwemp_pmla.value,
						parametros,
						true,
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								document.getElementById( div ).innerHTML = '<div style="page-break-after: always;">'+ajax.responseText+'</div>';
							}
						}
					);
	}

	// Llama al script de impresión de los CTC para que traiga la impresión del CTC
	// del examen enviado
	function consultarCTCProcedimiento( his, ing, exa, div, ordnro )
	{
		var vwemp_pmla = document.getElementById( "wemp_pmla" );

		var parametros = "imprimir=on&historia="+his+"&exa="+exa+"&ordnro="+ordnro;

		consultasAjax( "POST", "impresionCTCProcedimientosNoPosIDC.php?wemp_pmla="+vwemp_pmla.value,
						parametros,
						true,
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								document.getElementById( div ).innerHTML = '<div style="page-break-after: always;">'+ajax.responseText+'</div>';
							}
						}
					);
	}

	// Llama a este mismo script para hacer la impresión solo del medicamento correspondiente al CTC
	// para eso se envia el parámetro pro
	function consultarProcedimiento( his, ing, pro, div )
	{
		var vwemp_pmla = document.getElementById( "wemp_pmla" );

		var parametros = "whistoria="+his+"&wingreso="+ing+"&pro="+pro;

		consultasAjax( "POST", "ordenesidc_imp.php?wemp_pmla="+vwemp_pmla.value,
						parametros,
						true,
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){
								document.getElementById( div ).innerHTML = '<div style="page-break-after: always;">'+ajax.responseText+'</div>';
							}
						}
					);
	}


  </script>

  <style type="text/css">
	html, body, table, td {
		font-family: Arial;
		font-size: 7pt;
	}
	table, td {
		font-family: Arial;
		font-size: 6.5pt;
	}
	.encabezado {
		font-size: 6.5pt;
		font-weight: bold;
	}
	.encabezadoExamen {
		text-align: right;
		font-size: 10pt;
	}
	.encabezadoEmpresa {
		text-align: left;
		font-size: 8pt;
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
		height:31px !important;
		height:27px;
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
	.encabezadoImpresion {
		font-family: Arial;
		font-size: 8pt;
	}

  </style>
</head>


<body>

<?php
  /******************************************************************
   * 	  			IMPRESIÓN DE ORDENES MÉDICAS					*
   * -------------------------------------------------------------- *
   * Este script imprime la orden médica .							*
   ******************************************************************/
	/*
	 * Autor: John M. Cadavid. G.
	 * Fecha creacion: 2013-03-01
	 * Modificado:
	 */
	/******************************************************************
	 * Modificaciones:
	 * Abril 14 de 2020	Edwin MG: Se cambia la ruta de la firma que se encontraba en /images/medical/hce/ con el número de documento del médico con extensión .png para el nombre del archivo
	 *							  a images/medical/hce/Firmas/ con el codigo de matrix y extensión .png para el nombre de la imagen.
	 *							  Esto se hizo para que la imagen de la firma quede unificada, ya que ahí se encuentran las imagenes de las firmas de cada médico
	 * Noviembre 10 de 2016: Se agrega en REFERENCIA EXTERNA el campo Exámen físico.
	 * Agosto 18 de 2016 Edwin MG: Se corrige para que las ordenes de REFERENCIA EXTERNA tengan el salto de línea adecuadamente.
	 * Mayo 11 de 2016 Jonatan: Se agrega la impresion del formulario de remision externa en caso de que el paciente tenga este tipo de formulario asociado.
	 * Septiembre 30 de 2015	Se crea parametro nuevo en el maestro de frecuencia, que indica si debe imprimirse la unidad o un medicamento corriente.
	 * Septiembre 7 de 2015 - Para las ordenes de medicamentos, ayudas o procedimiento no POS ya no se imprime la orden acompañante que salía con CTC
	 * Septiembre 1 de 2015 - Se agrega el filtro de nuevo en 'on' para la tabla hceidc_000017, con esto se sabe si el procedimiento esta homologado o no. Jonatan
	 * Julio 29 de 2015 - Se agrega la impresion de los datos para el formulario 000142. Jonatan
	 * Julio 9 de 2014  - Se hace cambio para que la impresión de procedimientos salgan 5 por hoja a media carta
	 * Abril 1 de 2014	- Se aumenta fuente para el encabezado de la impesión
	 * Marzo 12 de 2014 - Se modifica la impresión de los procedimientos que tienen formularios HCE asociados
	 * Febrero 25 de 2014- Para la impresión de incapacidad se imprime el nro de días en letras
	 * Febrero 18 de 2014- Al imprimir se mostraba varios medicamentos con una misma condición, se corrige
	 * Enero 29 de 2014: - Para las ordenes que tienen formulario emergente, se corrige la información del paciente, ya que si se imprimía solo una orden de este tipo
	 *					   no salía la información demográfica del paciente
	 *					 - Ya no se imprime DIAGNÓSTICO QUE JUSTIFICA LA TRANSFUSIÓN por que este ya fue eliminado del formulario de TRANSFUSIONES de HCE
	 *					 - Para la impresión de orden de TRANSFUSIÓN, se cambia observaciones por justificación
	 * Enero 21 de 2014: - Se quita resumen de historia clínica para orden de hospitalización
	 *					 - Se corrige el motivo al imprimir una orden de hospitalización ya que estaba mostrando para el motivo las observaciones
	 ******************************************************************/

/****************************************************
 **************** VARIABLES GLOBALES ****************
 ****************************************************/
	$altoimagen = 0;
	$anchoimagen = 0;

/****************************************************
 ********************* FUNCIONES ********************
 ****************************************************/
 
	/****************************************************************
	 * Consulta los datos del CTC de un artículo
	 ****************************************************************/
	function consultarCTCArticulo( $conex, $wbasedato, $wemp_pmla, $historia, $art, $cco, $fin, $hin, $ido ){
	
		global $conex;
		
		$val = '';
	
		$sql = "SELECT
					Artgen, Artcom, Artfar, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Ubisac, Ubihac, Pactid, Pacced, Cconom, Kadufr, a.*, Ctcpon
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000011 g, {$wbasedato}_000060 h
				WHERE
					ctcest = 'on'
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
					AND kadfin = '$fin'
					AND kadhin = '$hin'
					AND kadido = '$ido'
				UNION
				SELECT
					Artgen, Artcom, Artfar, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Ubisac, Ubihac, Pactid, Pacced, Cconom, Kadufr, a.*, Ctcpon
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000011 g, {$wbasedato}_000054 h
				WHERE 
					ctcest = 'on'
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
					AND kadfin = '$fin'
					AND kadhin = '$hin'
					AND kadido = '$ido'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if( $rows = mysql_fetch_array( $res ) ){
			$val = $rows;
		}
		
		return $val;
	}

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

	// Función que retorna la edad con base en la fecha de nacimiento
	function obtenerSexo($sexo)
	{
		if($sexo=='F')
			return "Femenino";
		else
			return "Masculino";
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

	function valorBoleanoHCE($valor)
	{
		$strValor = explode("-",$valor);
		return $strValor[1];
	}


	function dimensionesImagen($idemed)
	{
		global $altoimagen;
		global $anchoimagen;

		// Obtengo las propiedades de la imagen, ancho y alto
		// list($widthimg, $heightimg) = getimagesize('../../images/medical/hce/'.$idemed.'.png');
		// if($heightimg==0)
			// $heightimg=1;

		$altoimagen = '27';
		// $anchoimagen = (27 * $widthimg) / $heightimg;
		// if($anchoimagen<81)
			$anchoimagen = 81;
	}

	function pintarEncabezado_ref_ext($tituloOrden,$whistoria,$nroOrden, $datosFormulario)
	{
		global $ingnre;
		global $dia;
		global $mes;
		global $anio;
		global $institucion;

		global $pacced;
		global $pactid;
		global $pacno1;
		global $pacno2;
		global $pacap1;
		global $pacap2;
		global $pacnac;
		global $pacsex;
		global $diagnostico;
		
		
		if(isset($nroOrden) && trim($nroOrden)!="")
			$textoNumero = ' Orden Nro. '.$nroOrden;
		else
			$textoNumero = ' &nbsp; ';


		// Inicio tabla logo
		echo '
				  <table style="border: 0pt none ; text-align: left; size=10pt" cellpadding="2" cellspacing="2">
					<tbody>
					  <tr>
						<td style="width: 5%;" class="encabezadoImpresion">
							<img src="../../images/medical/root/'.$institucion->baseDeDatos.'.jpg" width="148" heigth="53" border="0" align="left">
						  </td>
						  <td style="width: 170px; text-align:left; font-size:10pt; font-weight: bold;" >
							<div class="encabezadoEmpresa">INSTITUTO DE CANCEROLOGIA<br />Nit. 800.149.026-4</div>
						  </td>
						  <td style="width: 170px; text-align:left; font-size:10pt; font-weight: bold;" >
							<div >'.$textoNumero.'</div>
						  </td>
						  <td style="width: 170px; text-align:left; font-size:10pt; font-weight: bold;" >
							<div>'.$tituloOrden.'</div>
						  </td>
					  </tr>
					</tbody>
				  </table>	
			  </td>
			</tr>

			<tr>
			  <td class="encabezadoImpresion">';

		// Inicio tabla encabezado
		echo '
				<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td class="encabezadoImpresion">';


		// Inicio informacion del prestador
		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr  style="background-color:#EEEDED;">
							  <td class="encabezadoImpresion">
								&nbsp; <b>Información del Prestador</b>
							  </td>
							  <td style="width:31px; text-align:center;" class="encabezadoImpresion">
								<b>Día</b>
							  </td>
							  <td style="width:31px; text-align:center;" class="encabezadoImpresion">
								<b>Mes</b>
							  </td>
							  <td style="width:31px; text-align:center;" class="encabezadoImpresion">
								<b>Año</b>
							  </td>
							</tr>
						  </tbody>
						</table>
			';

		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr style="height:17px;">
							  <td style="width:7%;" class="encabezadoImpresion">
								&nbsp;
							  </td>
							  <td style="width:63%;" class="encabezadoImpresion">
								
							  </td>
							  <td style="width:17%; text-align:center;" class="encabezadoImpresion">
								<b>Fecha de solicitud</b>
							  </td>
							  <td style="width:4%; text-align:center;" class="encabezadoImpresion">
								<b>'.$dia.'</b>
							  </td>
							  <td style="width:4%; text-align:center;" class="encabezadoImpresion">
								<b>'.$mes.'</b>
							  </td>
							  <td style="width:5%; text-align:center;" class="encabezadoImpresion">
								<b>'.$anio.'</b>
							  </td>
							  ';

		echo '
							</tr>
						  </tbody>
						</table>
			';
		
		//  Información del Prestador
		echo '<table style="text-align: left; width: 740px;" border="0" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td style="border-right: 1px solid;"><b>Nombre:</b></td>
					  <td style="border-right: 1px solid;"><b>NIT:</b></td>
					  <td style="border-right: 1px solid;"><b>Codigo:</b></td>
					  <td style="border-right: 1px solid;"><b>Dirección:</b></td>
					  <td style="border-right: 1px solid;"><b>Teléfono:</b></td>
					  <td style="border-right: 1px solid;"><b>Departamento:</b></td>
					  <td >Municipio:</td>
					</tr>
					<tr>
					  <td style="border-right: 1px solid;">'.$datosFormulario[6].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[8].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[10].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[12].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[13].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[16].'</td>
					  <td>'.$datosFormulario[18].'</td>
					</tr>
				  </tbody>
				</table>';	
		
		
		// Inicio datos del paciente
		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr  style="height:17px;background-color:#EEEDED;">
							  <td colspan="6" class="encabezadoImpresion">
								&nbsp; <b>Datos del paciente</b>
							  </td>							  
							</tr>
						  </tbody>
						</table>';		
		
		if($pacsex == 'F'){
			
			$pacsex = "Femenino";
		}
		
		if($pacsex == "M"){
			
			$pacsex = "Masculino";
		}
		
		echo '<table style="text-align: left; width: 740px;" border="0" cellpadding="2" cellspacing="2">
				  <tbody>
				  <tr>
					  <td style=" border-right: 1px solid;"><b>Nombre:</b></td>
					  <td style=" border-right: 1px solid;"><b>Tipo doc:</b></td>
					  <td style=" border-right: 1px solid;"><b>Identificación:</b></td>
					  <td><b>Sexo:</b></td>
					  <td></td>										  
					</tr>
					<tr>
					  <td style="border-right: 1px solid; border-bottom: 1px solid;">'.$pacno1.' '.$pacno2.' '.$pacap1.' '.$pacap2.'</td>
					  <td style="border-right: 1px solid; border-bottom: 1px solid;">'.$pactid.'</td>
					  <td style="border-right: 1px solid; border-bottom: 1px solid;">'.$pacced.'</td>
					  <td style="border-bottom: 1px solid;">'.$pacsex.'</td>
					  <td style="border-bottom: 1px solid;"></td>				 
					 
					</tr>
					<tr>
					  <td style=" border-right: 1px solid;"><b>Fecha de nacimiento:</b></td>
					  <td style=" border-right: 1px solid;"><b>Teléfono:</b></td>
					  <td style=" border-right: 1px solid;"><b>Dirección residencia:</b></td>
					  <td style=" border-right: 1px solid;"><b>Departamento residencia:</b></td>
					  <td><b>Municipio de residencia:</b></td>										  
					</tr>
					<tr>
					  <td style="border-right: 1px solid;">'.$pacnac.'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[26].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[27].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[109].'</td>
					  <td>'.$datosFormulario[110].'</td>				 
					 
					</tr>
				  </tbody>
				</table>';
				
		//Entidad responsable del pago
		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr  style="height:17px;background-color:#EEEDED;">
							  <td colspan="6" class="encabezadoImpresion">
								&nbsp; <b>Entidad responsable del pago</b>
							  </td>							  
							</tr>
						  </tbody>
						</table>';		
		
			
		echo '<table style="text-align: left; width: 740px;" border="0" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td style="border-right: 1px solid;"><b>Nombre</b>: '.$datosFormulario[30].'</td>
					  <td>Código:</td>					  										  
					</tr>
					
				  </tbody>
				</table>';
				
		//Datos del responsable
		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr  style="height:17px;background-color:#EEEDED;">
							  <td colspan="6" class="encabezadoImpresion">
								&nbsp; <b>Datos del responsable</b>
							  </td>							  
							</tr>
						  </tbody>
						</table>';		
		
			
		echo '<table style="text-align: left; width: 740px;" border="0" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td style="border-right: 1px solid;"><b>Tipo de doc.</b>:</td>
					  <td style="border-right: 1px solid;"><b>Número de identificación:</b></td>
					  <td style="border-right: 1px solid;"><b>Nombre:</b></td>					  
					  <td style="border-right: 1px solid;"><b>Dirección de residencia:</b></td>										  
					  <td style="border-right: 1px solid;"><b>Teléfono:</b></td>								  
					  <td style="border-right: 1px solid;"><b>Departamento residencia:</b></td>								  
					  <td><b>Municipio de residencia:</b></td>										  
					</tr>
					<tr>
					  <td style="border-right: 1px solid;">'.$datosFormulario[35].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[37].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[39].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[41].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[43].'</td>				 
					  <td style="border-right: 1px solid;">'.$datosFormulario[45].'</td>
					  <td>'.$datosFormulario[45].'</td>					 
					</tr>
				  </tbody>
				</table>';
				
		//Profesional al que solicita la referencia y servicio al cual se remite
		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr  style="height:17px;background-color:#EEEDED;">
							  <td colspan="6" class="encabezadoImpresion">
								&nbsp; <b>Profesional al que solicita la referencia y servicio al cual se remite</b>
							  </td>							  
							</tr>
						  </tbody>
						</table>';		
		
			
		echo '<table style="text-align: left; width: 740px;" border="0" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td style="border-right: 1px solid;"><b>Nombre:</b></td>
					  <td style="border-right: 1px solid;"><b>Teléfono:</b></td>
					  <td style="border-right: 1px solid;"><b>Teléfono celular:</b></td>
					  <td style="border-right: 1px solid;"><b>Servicio que solicita referencia:</b></td>
					  <td><b>Servicio para el cual se solicita la referencia:</b></td>
					</tr>
					<tr>
					  <td style="border-right: 1px solid;">'.$datosFormulario[50].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[52].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[56].'</td>
					  <td style="border-right: 1px solid;">'.$datosFormulario[54].'</td>
					  <td>'.$datosFormulario[114].'</td>					  				 
					</tr>
				  </tbody>
				</table>';

	}
	
	
	function pintarEncabezado($tituloOrden,$whistoria,$nroOrden)
	{
		global $ingnre;
		global $dia;
		global $mes;
		global $anio;
		global $institucion;

		global $pacced;
		global $pactid;
		global $pacno1;
		global $pacno2;
		global $pacap1;
		global $pacap2;
		global $diagnostico;


		if(isset($nroOrden) && trim($nroOrden)!="")
			$textoNumero = ' Orden Nro. '.$nroOrden;
		else
			$textoNumero = ' &nbsp; ';


		// Inicio tabla logo
		echo '
				  <table style="border: 0pt none ; width: 100%; text-align: left; margin-left: auto; margin-right: auto;size=10pt" cellpadding="2" cellspacing="2">
					<tbody>
					  <tr class="encabezado">';

		// Contenido tabla logo
		echo '
						<td style="width: 15%;" class="encabezadoImpresion">
							<img src="../../images/medical/root/'.$institucion->baseDeDatos.'.jpg" width="148" heigth="53" border="0" align="left">
						  </td>
						  <td style="width: 30%;" class="encabezadoImpresion">
							<div class="encabezadoEmpresa">INSTITUTO DE CANCEROLOGIA<br />Nit. 800.149.026-4</div>
						  </td>
						  <td style="width: 15%;" class="encabezadoImpresion">
							<div class="encabezadoExamen">'.$textoNumero.'</div>
						  </td>
						  <td style="width: 40%;" class="encabezadoImpresion">
							<div class="encabezadoExamen">'.$tituloOrden.'</div>
						  </td>';

		// Fin tabla logo
		echo '
					  </tr>
					</tbody>
				  </table>';

		// División tabla principal
		echo '
			  </td>
			</tr>

			<tr>
			  <td class="encabezadoImpresion">';

		// Inicio tabla encabezado
		echo '
				<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td class="encabezadoImpresion">';


		// Inicio tablas datos paciente
		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr  style="height:17px;background-color:#EEEDED;">
							  <td colspan="6" class="encabezadoImpresion">
								&nbsp; <b>Información del Usuario</b>
							  </td>
							  <td style="width:31px; text-align:center;" class="encabezadoImpresion">
								<b>Día</b>
							  </td>
							  <td style="width:31px; text-align:center;" class="encabezadoImpresion">
								<b>Mes</b>
							  </td>
							  <td style="width:31px; text-align:center;" class="encabezadoImpresion">
								<b>Año</b>
							  </td>
							</tr>
						  </tbody>
						</table>
			';

		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr style="height:17px;">
							  <td style="width:7%;" class="encabezadoImpresion">
								&nbsp;EPS:
							  </td>
							  <td style="width:63%;" class="encabezadoImpresion">
								&nbsp;'.$ingnre.'
							  </td>
							  <td style="width:17%; text-align:center;" class="encabezadoImpresion">
								<b>Fecha de solicitud</b>
							  </td>
							  <td style="width:4%; text-align:center;" class="encabezadoImpresion">
								<b>'.$dia.'</b>
							  </td>
							  <td style="width:4%; text-align:center;" class="encabezadoImpresion">
								<b>'.$mes.'</b>
							  </td>
							  <td style="width:5%; text-align:center;" class="encabezadoImpresion">
								<b>'.$anio.'</b>
							  </td>
							  ';

		echo '
							</tr>
						  </tbody>
						</table>
			';

		echo '
						<table style="width: 100%; text-align: left; margin-left: auto; margin-right: auto;border-top: 0px solid rgb(51, 51, 51);" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr style="height:17px;">
							  <td style="text-align:left;" class="encabezadoImpresion">
								Nro Id.
							  </td>
							  <td style="text-align:left;" class="encabezadoImpresion">
								'.$pacced.'
							  </td>
							  <td style="text-align:right;" class="encabezadoImpresion">
								Tipo Id.
							  </td>
							  <td class="encabezadoImpresion">
								'.$pactid.' &nbsp;&nbsp;&nbsp;&nbsp;
							  </td>
							  <td class="encabezadoImpresion">
								Nombre:
							  </td>
							  <td class="encabezadoImpresion">
								'.$pacno1.' '.$pacno2.' '.$pacap1.' '.$pacap2.'
							  </td>
							  <td class="encabezadoImpresion">
								Nro Historia Clínica
							  </td>
							  <td style="text-align:left;" class="encabezadoImpresion">
								'.$whistoria.'
							  </td>
							</tr>
						  </tbody>
						</table>
			';


		echo '
						<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr style="height:21px;">
							  <td class="encabezadoImpresion">
								&nbsp;Diagnóstico: '.$diagnostico.'
							  </td>
							</tr>
						  </tbody>
						</table>
			';
		// Fin tablas datos paciente


		// Fin tabla encabezado
		echo '
					  </td>
					</tr>
				  </tbody>
				</table>';
	}

	function pintarPiePagina()
	{
		global $altoimagen;
		global $anchoimagen;
		global $medtdo;
		global $meddoc;
		global $medno1;
		global $medno2;
		global $medap1;
		global $medap2;
		global $medreg;
		global $espnom;
		global $wusuario;

		// División tabla principal
		echo '
			  </td>
			</tr>

			<tr>
			  <td>';


		// Inicio tabla profesional
		echo '
				<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td>';

		echo '
						<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr>
							  <td>
								<br /><b>PROFESIONAL</b>
							  </td>
							</tr>
						  </tbody>
						</table>
			';

		echo '
						<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
						  <tbody>
							<tr>
							  <td><div class="campoFirma">';
		if(file_exists('../../images/medical/hce/Firmas/'.$wusuario.'.png'))
		{
			echo '				<img src="../../images/medical/hce/Firmas/'.$wusuario.'.png" height="'.$altoimagen.'" width="'.$anchoimagen.'" border="0" />';
		}
		else
		{
			echo '&nbsp;';
		}
		echo '				  </div></td>
							</tr>
							<tr>
							  <td>
								&nbsp;Nombre del Médico: '.$medap1.' '.$medap2.', '.$medno1.' '.$medno2.' <br />
								&nbsp;Identificación: '.$medtdo.' '.$meddoc.' <br />
								&nbsp;Registro Médico: '.$medreg.' <br />
								&nbsp;Especialidad: '.$espnom.' <br />
							  </td>
							</tr>
						  </tbody>
						</table>
			';

		// Fin tabla profesional
		echo '
					  </td>
					</tr>
				  </tbody>
				</table>';

		// Inicio tabla pie de página
		echo '
				<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
				  <tbody>
					<tr>
					  <td style="text-align:center;" calss="descripcion">
						Carrera 70 No 1 – 135 Torre 5 Medellín/Colombia Conmutador 340 9393
					  </td>
					</tr>
				  </tbody>
				</table>';
		// Fin tabla pie de página

	}


	function pintarSaltosLinea($num)
	{
		echo "<br /><br />";
		for($i=0;$i<$num;$i++)
		{
			echo "<br />";
		}
	}

/////////////////////////// FIN FUNCIONES /////////////////////////
///////////////////////////////////////////////////////////////////


session_start();


// Si el usuario no está registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
	echo "<br /> La sessión de usuario ha caducado. Vuelva a entrar a Matrix";
else	// Si el usuario está registrado inicia el programa
{
	include_once("root/comun.php");

	$conex = obtenerConexionBD("matrix");

	echo '<input type="hidden" name="wemp_pmla" id="wemp_pmla" value="'.$wemp_pmla.'">';

	// Se obtiene el valor para $wbasedato
	$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$institucion = consultarInstitucionPorCodigo($conex,$wemp_pmla);

	// Obtengo los datos del usuario
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));
	
	
	$wdiagnostico = consultarDiagnostico( $conex, $wbasedatohce, $whistoria, $wingreso );

	// Aca se coloca la ultima fecha de actualización
	$wactualiz = " Noviembre 10 de 2016";

	/***********************************************
	*********** P R I N C I P A L ******************
	************************************************/

	// Titulo de la página
	$titulo = "ORDENES MEDICAS";

	// Defino parámetros de impresión por página
	$salto_pagina = false;
	$seccion_par = true;
	$medicamentosPorHoja = 8;
	$procedimientosPorHoja = 10;

	// Se asigna el valor por defecto para el filtro de impresión
	$filtroArtImprimir = " "; 	// " AND Kadimp = 'on' ";
	$filtroProImprimir = " AND Detimp = 'on' ";

	$mostrarSoloArticulos = false;
	$mostrarSoloProcedimientos = false;
	$mostrarSoloCTC = false;
	$mostrarSoloCTCArt = false;
	$mostrarSoloCTCPro = false;

	// Si tipoimp viene con el valor impart se establece que solo se va a imprimir los artículos
	if(isset($tipoimp) && $tipoimp=="impart")
		$mostrarSoloArticulos = true;

	// Si tipoimp viene con el valor imppro se establece que solo se va a imprimir los procedimientos
	if(isset($tipoimp) && $tipoimp=="imppro")
		$mostrarSoloProcedimientos = true;

	// Si tipoimp viene con el valor impctc se establece que solo se va a imprimir los formularios de CTC
	if(isset($tipoimp) && $tipoimp=="impctc")
		$mostrarSoloCTC = true;
		
	// Si tipoimp viene con el valor impctc se establece que solo se va a imprimir los formularios de CTC
	if(isset($tipoimp) && $tipoimp=="impctcart"){
		$mostrarSoloCTCArt = true;
	}
		
	// Si tipoimp viene con el valor impctc se establece que solo se va a imprimir los formularios de CTC
	if(isset($tipoimp) && $tipoimp=="impctcpro"){
		$mostrarSoloCTCPro = true;
	}

	// Se define si solo se va a imprimir un medicamento o procedimiento especifico
	// Esto pasa cuando se llama desde la impresión de CTC, solo se imprime
	// el medicamento o el procedimiento correspondiente al CTC solicitado
	$filtroProcedimiento = "";
	if(isset($pro) && $pro!="")
	{
		$filtroProcedimiento = "	  AND Detcod = '".$pro."' ";
		$mostrarSoloProcedimientos = true;
		$filtroProImprimir = " ";
	}
	$filtroArticulo = "";
	if(isset($art) && $art!="")
	{
		$filtroArticulo = "	  AND Kadart = '".$art."' ";
		$mostrarSoloArticulos = true;
		$filtroArtImprimir = " ";
	}


	// Datos cronológicos
	$anio = date("Y");
	$mes = date("m");
	$dia = date("d");

	if(isset($fec) && $fec!='')
		$fecha = $fec;
	else
		$fecha = date("Y-m-d");

	$observaciones = "";

	// Se define el filtro para consultar el médico tratante
	if(isset($ide) && $ide!="")
		$filtroMedico = " Meddoc = '".$ide."' ";
	else
		$filtroMedico = " Meduma = '".$wusuario."' ";

	// Consulto los datos del médico
	$q = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Medreg, Medtel, Espnom "
		."   FROM ".$wbasedato."_000048, ".$wbasedato."_000044 b "
		." 	WHERE ".$filtroMedico." "
		."	  AND Medest = 'on'  "
		."	  AND ( Medesp = Espcod OR  Medesp = CONCAT(Espcod,'-',Espnom) ) ";
	$resmed = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$nummed = mysql_num_rows($resmed);
	$rowmed = mysql_fetch_array($resmed);

	// Se calculan las dimensiones para la imagen de la firma del médico
	dimensionesImagen($rowmed['Meddoc']);

	// Datos del médico tratante
	$medtdo = $rowmed['Medtdo'];
	$meddoc = $rowmed['Meddoc'];
	$medno1 = $rowmed['Medno1'];
	$medno2 = $rowmed['Medno2'];
	$medap1 = $rowmed['Medap1'];
	$medap2 = $rowmed['Medap2'];
	$medreg = $rowmed['Medreg'];
	$espnom = $rowmed['Espnom'];


	// Arreglo que me guardará la lista de medicamentos No Pos ordenados
	$medicamentosNoPos = array();

	// Arreglo que me gusradará la lista de procedimientos No Pos ordenados
	$examenesNoPos = array();


	if( !( $mostrarSoloCTCPro || $mostrarSoloCTCArt ) )
	if($mostrarSoloArticulos || (!$mostrarSoloProcedimientos && !$mostrarSoloCTC))
	{

		/**************************************************************************
		********************* IMPRESIÓN MEDICAMENTOS INTERNOS *********************
		***************************************************************************/

		// Consulto la orden y datos del paciente
		$q = " SELECT b.Fecha_data Fecha, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo, Kadido "
			."   FROM ".$wbasedato."_000060 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000036 g, root_000037 h "
			." 	WHERE Kadhis = '".$whistoria."' "
			."	  AND Kading = '".$wingreso."'  "
			// ."	  AND a.Fecha_data = Kadfec "
			// ."	  AND Kadfec = '".$fecha."' "	//2016-01-15. La idea es poder imprimir cualquier medicamento así no se halla ordenado la última vez
			.$filtroArtImprimir
			."	  AND Kadest = 'on'  "
			."	  AND Kadint = 'on'  "
			.$filtroArticulo
			."	  AND Kadart = Artcod "
			."	  AND Kadhis = Inghis "
			."	  AND Kading = Inging "
			."	  AND Inghis = Ubihis "
			."	  AND Inging = Ubiing "
			."	  AND Kadhis = Orihis  "
			."	  AND Kading = Oriing  "
			."	  AND Kadimp = 'on'  "
			."	  AND Oriced = Pacced "
			."	  AND Oritid = Pactid  "
			."	  AND Oriori = '".$wemp_pmla."'  ";
			// ."	GROUP BY Kadhis, Kading, Kadart ";
		//	."	ORDER BY Fecha DESC";

		// $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		// $num = mysql_num_rows($res);

		// // Si no se encontró en la tabla 000060 de movimiento hospitalario, busque en la tabla 000054
		// if($num==0)
		// {

			$q .= " UNION ";

			$q .= "SELECT b.Fecha_data Fecha, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, '' AS Ingdir, '' AS Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo, Kadido "
				."   FROM ".$wbasedato."_000054 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000036 g, root_000037 h  "
				." 	WHERE Kadhis = '".$whistoria."' "
				."	  AND Kading = '".$wingreso."'  "
				// ."	  AND a.Fecha_data = Kadfec "
				// ."	  AND Kadfec = '".$fecha."' "	//2016-01-15. La idea es poder imprimir cualquier medicamento así no se halla ordenado la última vez
				.$filtroArtImprimir
				."	  AND Kadest = 'on'  "
				."	  AND Kadint = 'on'  "
				.$filtroArticulo
				."	  AND Kadart = Artcod "
				."	  AND Kadhis = Inghis "
				."	  AND Kading = Inging "
				."	  AND Inghis = Ubihis "
				."	  AND Inging = Ubiing "
				."	  AND Kadhis = Orihis  "
				."	  AND Kading = Oriing  "
				."	  AND Kadimp = 'on'  "
				."	  AND Oriced = Pacced "
				."	  AND Oritid = Pactid  "
				."	  AND Oriori = '".$wemp_pmla."'  "
				// ."	GROUP BY Kadhis, Kading, Kadart "
				."	ORDER BY Fecha DESC";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);
		// }


		// Si se encontraron medicamentos ordenados
		if($num > 0)
		{
			$row = mysql_fetch_array($res);

			$ingnre = $row['Ingnre'];
			$pacced = $row['Pacced'];
			$pactid = $row['Pactid'];
			$pacno1 = $row['Pacno1'];
			$pacno2 = $row['Pacno2'];
			$pacap1 = $row['Pacap1'];
			$pacap2 = $row['Pacap2'];
			$pacnac = $row['Pacnac'];
			$pacsex = $row['Pacsex'];
			$diagnostico = $wdiagnostico;

			// Cambiar numero limite items
			$cont = 0;
			$cont2 = 0;

			// Ciclo para mostrar la lista de medicamentos
			for($i=0;$i<$num;$i++)
			{
				$cont++;
				$cont2++;

				// Cambiar numero limite items
				// if($num > 4)
				// {
					// $salto_pagina = true;
					// $seccion_par = false;
				// }

				// Cambiar numero limite items
				if($cont==1)
				{
					// Control del salto de página
					if(!$seccion_par)
					{
						$salto_pagina = false;
						$seccion_par = true;
					}
					else
					{
						$salto_pagina = true;
						$seccion_par = false;
					}

					//if($salto_pagina)
					//{
						// Inicio div principal
						echo '
						<div style="page-break-after: always;">';
					//}

					echo '
					   <div>';

					$subEncabezado = '<br />Manejo Interno';
					$encColumnaDosis = 'Dosis';
					$encColumnaDTto = 'Duraci&oacute;n';
					if(isset($art) && $art!=""){
						$subEncabezado = '<br />Con CTC';
						
						$encColumnaDosis = 'Posolog&iacute;a';
						$encColumnaDTto = 'D&iacute;as. Tto';
						
						$rowDatCTC = consultarCTCArticulo( $conex, $wbasedato, $wemp_pmla, $row['Kadhis'], $row['Kadart'], '%', $row['Kadfin'], $row['Kadhin'], $row['Kadido'] );
						
						if( !empty( $rowDatCTC[ 'Ctcpon' ] ) ){
							$row['Kadcfr'] = $rowDatCTC[ 'Ctcpon' ];
						}
					}

					// Inicio tabla principal
					echo '
					<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
					  <tbody>

						<tr>
						  <td>';

							pintarEncabezado('ORDEN MEDICAMENTOS'.$subEncabezado,$whistoria,'');

					// Inicio tabla detalle
					echo '
							<table style="width: 100%;" cellpadding="2" cellspacing="2">
							  <tbody>
								<tr>
								  <td>';

					// Inicio tablas lista detalle
					echo '
									<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
									  <tbody>
										<tr>
										  <td style="height:17px;background-color:#EEEDED;">
											&nbsp; <b>Servicios Solicitados</b>
										  </td>
										</tr>
										<tr>
										  <td  style="height:11px;">
											&nbsp;
										  </td>
										</tr>
									  </tbody>
									</table>
						';
					if(isset($art) && $art!=""){
						echo '
										<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
										  <tbody>
											<tr style="height:17px;">
											  <td style="width:30%;">
												<b>&nbsp;Medicamento</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;Presentacion</b>
											  </td>
											  <td style="width:9%;">
												<b>&nbsp;Cantidad</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;'.$encColumnaDosis.'</b>
											  </td>
											  <td style="width:9%;">
												<b>&nbsp;V&iacute;a</b>
											  </td>
											  <td style="width:11%;">
												<b>&nbsp;Frecuencia</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;'.$encColumnaDTto.'</b>
											  </td>
											</tr>';
					}
					else{
						// Inicio tabla con lista de medicamentos
						echo '
										<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
										  <tbody>
											<tr style="height:17px;">
											  <td style="width:30%;">
												<b>&nbsp;Medicamento</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;Presentacion</b>
											  </td>
											  <td style="width:9%;">
												<b>&nbsp;Cantidad</b>
											  </td>
											</tr>';
					}

				}

				// // Consulto los datos de la unidad
				$q = " SELECT  Unides "
					."   FROM ".$wbasedato."_000027 "
					." 	WHERE  Unicod = '".$row['Kaduma']."' ";
				$resuni = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$numuni = mysql_num_rows($resuni);
				$rowuni = mysql_fetch_array($resuni);

				// // Consulto los datos de la presentacion
				$q = " SELECT  Ffanom "
					."   FROM ".$wbasedato."_000046 "
					." 	WHERE  Ffacod = '".$row['Kadffa']."' ";
				$resffa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$numffa = mysql_num_rows($resffa);
				$rowffa = mysql_fetch_array($resffa);

				// // Consulto los datos de la via de administracion
				$q = " SELECT  Viades "
					."   FROM ".$wbasedato."_000040 "
					." 	WHERE  Viacod = '".$row['Kadvia']."' ";
				$resvia = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$numvia = mysql_num_rows($resvia);
				$rowvia = mysql_fetch_array($resvia);

				// Consulto los datos de la frecuencia de administracion
				$q = " SELECT  Percan, Peruni, Pertip, Periun "
					."   FROM ".$wbasedato."_000043 "
					." 	WHERE  Percod = '".$row['Kadper']."' ";
				$resfre = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$numfre = mysql_num_rows($resfre);
				$rowfre = mysql_fetch_array($resfre);

				if( strtolower( trim($rowfre['Periun']) )!='on' )
					$frecuencia = 'Cada '.$rowfre['Percan'].' hora(s)';
				else
					$frecuencia = $rowfre['Peruni'];


				// Consulto el nombre de la familia del medicamento
				$qfam =  " SELECT Famnom, Relcon, Reluni "
						."   FROM ".$wbasedato."_000114 a, ".$wbasedato."_000115 b "
						." 	WHERE Relart = '".$row['Kadart']."' "
						."	  AND Relest = 'on' "
						."	  AND Relfam = Famcod  "
						."	  AND Famest = 'on' "
						."	ORDER BY b.id DESC";

				$resfam = mysql_query($qfam,$conex) or die (mysql_errno()." - ".mysql_error());
				$numfam = mysql_num_rows($resfam);

				// Si se encontró familia asigne esto a la variable que se imprimirá
				// Se comenta la siguiente línea porque se imprimirá el nombre del medicamento genérico no la familia
				/*
				if($numfam>0)
				{
					$rowfam = mysql_fetch_array($resfam);
					$medicamento = $rowfam['Famnom'].' '.$rowfam['Relcon'].' '.$rowfam['Reluni'];
				}
				*/

				$medicamento = $row['Artgen'];

				if($row['Kaddia'] && trim($row['Kaddia'])!="" && $row['Kaddia']!='0')
					$diasTto = $row['Kaddia'].' dias';
				else
					$diasTto = '';


				if(isset($art) && $art!=""){
					echo '
										<tr>
										  <td>
											&nbsp;'.$medicamento.'
										  </td>
										  <td>
											&nbsp;'.$rowffa['Ffanom'].'
										  </td>
										  <td>
											&nbsp;'.$row['Kaddma'].'
										  </td>
										  <td>
											&nbsp;'.$row['Kadcfr'].'
										  </td>
										  <td>
											&nbsp;'.$rowvia['Viades'].'
										  </td>
										  <td>
											&nbsp;'.$frecuencia.'
										  </td>
										  <td>
											&nbsp;'.$diasTto.'
										  </td>
										</tr>';
				}
				else{
					echo '
									<tr style="height:17px;">
									  <td>
										&nbsp;'.$medicamento.'
									  </td>
									  <td>
										&nbsp;'.$rowffa['Ffanom'].'
									  </td>
									  <td>
										&nbsp;'.$row['Kaddma'].'
									  </td>
									</tr>';
				}

				//$observaciones .= "<br />".$row['Kadobs'];

				if(trim($row['Kadobs'])!="")
				{
					echo '<tr><td colspan="7" height="17"> &nbsp; &nbsp; '.$row['Kadobs'].'</td></tr>';
					$cont++;
				}

				$row = mysql_fetch_array($res);

				// Si se ha llegado al límite de artísuclos por hoja o al final de articulos encontrados
				if($cont==$medicamentosPorHoja || $cont2==$num)
				{
							echo '
											  </tbody>
											</table>
								';
							// Fin tabla con lista de medicamentos


							// Fin tablas lista detalle

							// Fin tabla detalle
							echo '
										  </td>
										</tr>
									  </tbody>
									</table>';


							// División tabla principal
							echo '
								  </td>
								</tr>

								<tr>
								  <td>';


							pintarPiePagina();


							// Fin tabla principal
							echo '
								  </td>
								</tr>
							  </tbody>
							</table>';

						// Fin div principal
						echo '
						   </div>';

					//if($seccion_par)
					  echo '
					  </div>';

					$cont = 0;
				}

			}
		}

		/******************* FIN IMPRESIÓN MEDICAMENTOS INTERNOS *******************/



		/**************************************************************************
		********************* IMPRESIÓN MEDICAMENTOS EXTERNOS *********************
		***************************************************************************/

		// Consulto la orden y datos del paciente
		$q = " SELECT b.Fecha_data Fecha, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo, Artonc, Kadido "
			."   FROM ".$wbasedato."_000060 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000036 g, root_000037 h "
			." 	WHERE Kadhis = '".$whistoria."' "
			."	  AND Kading = '".$wingreso."'  "
			// ."	  AND a.Fecha_data = Kadfec "
			// ."	  AND Kadfec = '".$fecha."' "	//2016-01-15. La idea es poder imprimir cualquier medicamento así no se halla ordenado la última vez
			.$filtroArtImprimir
			."	  AND Kadest = 'on'  "
			."	  AND Kadint != 'on'  "
			.$filtroArticulo
			."	  AND Kadart = Artcod "
			."	  AND Kadhis = Inghis "
			."	  AND Kading = Inging "
			."	  AND Inghis = Ubihis "
			."	  AND Inging = Ubiing "
			."	  AND Kadhis = Orihis  "
			."	  AND Kading = Oriing  "
			."	  AND Kadimp = 'on'  "
			."	  AND Oriced = Pacced "
			."	  AND Oritid = Pactid  "
			."	  AND Oriori = '".$wemp_pmla."'  ";
			// ."	GROUP BY Kadhis, Kading, Kadart ";
		//	."	ORDER BY Fecha DESC";

		// $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		// $num = mysql_num_rows($res);

		// // Si no se encontró en la tabla 000054 de movimiento hospitalario, busque en la tabla 000060
		// if($num==0)
		// {
			$q .= " UNION ";

			$q .= "SELECT b.Fecha_data Fecha, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, '' AS Ingdir, '' AS Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo, Artonc, Kadido "
				."   FROM ".$wbasedato."_000054 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000036 g, root_000037 h  "
				." 	WHERE Kadhis = '".$whistoria."' "
				."	  AND Kading = '".$wingreso."'  "
				// ."	  AND a.Fecha_data = Kadfec "
				// ."	  AND Kadfec = '".$fecha."' "	//2016-01-15. La idea es poder imprimir cualquier medicamento así no se halla ordenado la última vez
				.$filtroArtImprimir
				."	  AND Kadest = 'on'  "
				."	  AND Kadint != 'on'  "
				.$filtroArticulo
				."	  AND Kadart = Artcod "
				."	  AND Kadhis = Inghis "
				."	  AND Kading = Inging "
				."	  AND Inghis = Ubihis "
				."	  AND Inging = Ubiing "
				."	  AND Kadhis = Orihis  "
				."	  AND Kading = Oriing  "
				."	  AND Kadimp = 'on'  "
				."	  AND Oriced = Pacced "
				."	  AND Oritid = Pactid  "
				."	  AND Oriori = '".$wemp_pmla."'  "
				// ."	GROUP BY Kadhis, Kading, Kadart "
				."	ORDER BY Artonc, Fecha DESC";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);
		// }

		// Si se encontraron medicamentos ordenados
		if($num > 0)
		{
			$row = mysql_fetch_array($res);

			$ingnre = $row['Ingnre'];
			$pacced = $row['Pacced'];
			$pactid = $row['Pactid'];
			$pacno1 = $row['Pacno1'];
			$pacno2 = $row['Pacno2'];
			$pacap1 = $row['Pacap1'];
			$pacap2 = $row['Pacap2'];
			$pacnac = $row['Pacnac'];
			$pacsex = $row['Pacsex'];
			$diagnostico = $wdiagnostico;
			
			$numArr[0] = 0;			//Cuenta el total de articulos NO oncológicos
			$numArr[1] = 0; 		//Cuenta el total de articulos oncológicos
			
			//Se hacen dos impresiones para medicamentos externos
			// - La primera es para no oncologicos
			// - La segunda es para oncologicos
			//Es la misma impresión por tanto se recorre dos veces la impresión
			//Para hacerlo se debe conocer cuantos registros son de cada uno
			//Para ello recorro toda la consulta contando cuantos son
			do{
				if( strtolower( $row[ 'Artonc' ] ) != 'on' ){
					$numArr[0]++;	//Cuenta el total de articulos NO oncológicos
				}
				else{
					$numArr[1]++;
				}
			}while( $row = mysql_fetch_array($res) );
			
			//Devuelvo el puntero de la constulta a su posición 0 para hacer la impresión completa
			mysql_data_seek( $res, 0 );
			$row = mysql_fetch_array($res);

			for($kkk=0;$kkk<2;$kkk++){
				// Cambiar numero limite items
				$cont = 0;
				$cont2 = 0;
				$num = $numArr[$kkk];

				// Ciclo para mostrar la lista de medicamentos
				for($i=0;$i<$num;$i++)
				{
					$cont++;
					$cont2++;

					// Cambiar numero limite items
					// if($num > 4)
					// {
						// $salto_pagina = true;
						// $seccion_par = false;
					// }

					// Cambiar numero limite items
					if($cont==1)
					{
						// Control del salto de página
						if(!$seccion_par)
						{
							$salto_pagina = false;
							$seccion_par = true;
						}
						else
						{
							$salto_pagina = true;
							$seccion_par = false;
						}

						//if($salto_pagina)
						//{
							// Inicio div principal
							echo '
							<div style="page-break-after: always;">';
						//}

						echo '
						   <div>';

						$subEncabezado = '';
						$encColumnaDosis = 'Dosis';
						$encColumnaDTto = 'Duraci&oacute;n';
						if(isset($art) && $art!=""){
							$subEncabezado = '<br />Con CTC';
							$encColumnaDosis = 'Posolog&iacute;a';
							$encColumnaDTto = 'D&iacute;as. Tto';
							
							$rowDatCTC = consultarCTCArticulo( $conex, $wbasedato, $wemp_pmla, $row['Kadhis'], $row['Kadart'], '%', $row['Kadfin'], $row['Kadhin'], $row['Kadido'] );
							
							if( !empty( $rowDatCTC[ 'Ctcpon' ] ) ){
								$row['Kadcfr'] = $rowDatCTC[ 'Ctcpon' ];
							}
						}

						// Inicio tabla principal
						echo '
						<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
						  <tbody>

							<tr>
							  <td>';

								pintarEncabezado('ORDEN MEDICAMENTOS'.$subEncabezado,$whistoria,'');


						// Inicio tabla detalle
						echo '
								<table style="width: 100%;" cellpadding="2" cellspacing="2">
								  <tbody>
									<tr>
									  <td>';

						// Inicio tablas lista detalle
						echo '
										<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
										  <tbody>
											<tr>
											  <td style="height:17px;background-color:#EEEDED;">
												&nbsp; <b>Servicios Solicitados</b>
											  </td>
											</tr>
											<tr>
											  <td  style="height:11px;">
												&nbsp;
											  </td>
											</tr>
										  </tbody>
										</table>
							';

						// Inicio tabla con lista de medicamentos
						echo '
										<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
										  <tbody>
											<tr style="height:17px;">
											  <td style="width:30%;">
												<b>&nbsp;Medicamento</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;Presentacion</b>
											  </td>
											  <td style="width:9%;">
												<b>&nbsp;Cantidad</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;'.$encColumnaDosis.'</b>
											  </td>
											  <td style="width:9%;">
												<b>&nbsp;V&iacute;a</b>
											  </td>
											  <td style="width:11%;">
												<b>&nbsp;Frecuencia</b>
											  </td>
											  <td style="width:10%;">
												<b>&nbsp;'.$encColumnaDTto.'</b>
											  </td>
											</tr>';

					}

					// // Consulto los datos de la unidad
					$q = " SELECT  Unides "
						."   FROM ".$wbasedato."_000027 "
						." 	WHERE  Unicod = '".$row['Kaduma']."' ";
					$resuni = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$numuni = mysql_num_rows($resuni);
					$rowuni = mysql_fetch_array($resuni);

					// // Consulto los datos de la presentacion
					$q = " SELECT  Ffanom "
						."   FROM ".$wbasedato."_000046 "
						." 	WHERE  Ffacod = '".$row['Kadffa']."' ";
					$resffa = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$numffa = mysql_num_rows($resffa);
					$rowffa = mysql_fetch_array($resffa);

					// // Consulto los datos de la presentacion
					$q = " SELECT  Viades "
						."   FROM ".$wbasedato."_000040 "
						." 	WHERE  Viacod = '".$row['Kadvia']."' ";
					$resvia = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$numvia = mysql_num_rows($resvia);
					$rowvia = mysql_fetch_array($resvia);

					// Consulto los datos de la frecuencia de administracion
					$q = " SELECT  Percan, Peruni, Pertip, Periun "
						."   FROM ".$wbasedato."_000043 "
						." 	WHERE  Percod = '".$row['Kadper']."' ";
					$resfre = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$numfre = mysql_num_rows($resfre);
					$rowfre = mysql_fetch_array($resfre);

					if( strtolower( trim($rowfre['Periun']) )!='on')
						$frecuencia = 'Cada '.$rowfre['Percan'].' hora(s)';
					else
						$frecuencia = $rowfre['Peruni'];


					// Consulto el nombre de la familia del medicamento
					$qfam =  " SELECT Famnom, Relcon, Reluni "
							."   FROM ".$wbasedato."_000114 a, ".$wbasedato."_000115 b "
							." 	WHERE Relart = '".$row['Kadart']."' "
							."	  AND Relest = 'on' "
							."	  AND Relfam = Famcod  "
							."	  AND Famest = 'on' "
							."	ORDER BY b.id DESC";

					$resfam = mysql_query($qfam,$conex) or die (mysql_errno()." - ".mysql_error());
					$numfam = mysql_num_rows($resfam);
					
					
					// Consulto el nombre de la codición
					$qCnd =  " SELECT Condes "
							."   FROM ".$wbasedato."_000042 a "
							." 	WHERE Concod = '".$row['Kadcnd']."' ";

					$rescnd = mysql_query($qCnd,$conex) or die (mysql_errno()." - ".mysql_error());
					$numcnd = mysql_num_rows($rescnd);
					$desCnd = "";
					if( $rowsCnd =  mysql_fetch_array($rescnd) ){
						$desCnd = $rowsCnd[ 'Condes' ];
					}

					// Si se encontró familia asigne esto a la variable que se imprimirá
					// Se comenta la siguiente línea porque se imprimirá el nombre del medicamento genérico no la familia
					/*
					if($numfam>0)
					{
						$rowfam = mysql_fetch_array($resfam);
						$medicamento = $rowfam['Famnom'].' '.$rowfam['Relcon'].' '.$rowfam['Reluni'];
					}
					*/

					if($row['Kaddia'] && trim($row['Kaddia'])!="" && $row['Kaddia']!='0')
						$diasTto = $row['Kaddia'].' dias';
					else
						$diasTto = '';


					$medicamento = $row['Artgen'];

					echo '
									<tr>
									  <td>
										&nbsp;'.$medicamento.'
									  </td>
									  <td>
										&nbsp;'.$rowffa['Ffanom'].'
									  </td>
									  <td>
										&nbsp;'.$row['Kaddma'].'
									  </td>
									  <td>
										&nbsp;'.$row['Kadcfr'].'
									  </td>
									  <td>
										&nbsp;'.$rowvia['Viades'].'
									  </td>
									  <td>
										&nbsp;'.$frecuencia.'
									  </td>
									  <td>
										&nbsp;'.$diasTto.'
									  </td>
									</tr>';

					//$observaciones .= "<br />".$row['Kadobs'];

					if(trim($row['Kadobs'])!="")
					{
						echo '<tr><td colspan="7" height="17" style="vertical-align:top"> &nbsp; &nbsp; '.$row['Kadobs'].'</td></tr>';
						$cont++;
					}
					
					if(trim($desCnd)!="")
					{
						echo '<tr><td colspan="7" height="17" style="vertical-align:top"> &nbsp; &nbsp; Condici&oacute;n: '.$desCnd.'</td></tr>';
						$cont++;
					}

					if($cont==$medicamentosPorHoja || $cont2==$num)
					{
							echo '
											  </tbody>
											</table>
								';
							// Fin tabla con lista de medicamentos


							// Fin tablas lista detalle

							// Fin tabla detalle
							echo '
										  </td>
										</tr>
									  </tbody>
									</table>';


							// División tabla principal
							echo '
								  </td>
								</tr>

								<tr>
								  <td>';

							pintarPiePagina();


							// Fin tabla principal
							echo '
								  </td>
								</tr>
							  </tbody>
							</table>';

						// Fin div principal
						echo '
						   </div>';

						//if($seccion_par)
						  echo '
						  </div>';

						$cont = 0;
					}

					$row = mysql_fetch_array($res);
				}
			}
		}

		/******************* FIN IMPRESIÓN MEDICAMENTOS EXTERNOS *******************/
	}

	if( !( $mostrarSoloCTCPro || $mostrarSoloCTCArt ) )
	if($mostrarSoloProcedimientos || (!$mostrarSoloArticulos && !$mostrarSoloCTC))
	{

		/*****************************************************************
		*********************** IMPRESIÓN EXAMENES ***********************
		******************************************************************/

		// // Consulto los tipos de orden del paciente
		// $q = " SELECT a.Fecha_data, Ordhis, Ording, Ordtor, Ordnro, Tipfrm, Tipanx, Ordtor, Tipaut "
			// ."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000015 d, ".$wbasedatohce."_000047 e "
			// ." 	WHERE Ordhis = '".$whistoria."' "
			// ."	  AND Ording = '".$wingreso."'  "
			// ."	  AND Ordest = 'on'  "
			// ."	  AND Ordtor = Dettor "
			// ."	  AND Ordnro = Detnro "
		// //	.$filtroPestana
			// .$filtroProcedimiento
			// ."	  AND Detest = 'on'	"
			// ."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
			// .$filtroProImprimir
			// ."	  AND Detcod = e.Codigo "
			// ."	  AND e.Tipoestudio = d.Codigo"
			// ."	GROUP BY Ordtor, Ordnro "
			// ."	UNION "
			// ." SELECT a.Fecha_data, Ordhis, Ording, Ordtor, Ordnro, Tipfrm, Tipanx, Ordtor, Tipaut "
			// ."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000015 d, ".$wbasedatohce."_000017 e "
			// ." 	WHERE Ordhis = '".$whistoria."' "
			// ."	  AND Ording = '".$wingreso."'  "
			// ."	  AND Ordest = 'on'  "
			// ."	  AND Ordtor = Dettor "
			// ."	  AND Ordnro = Detnro "
		// //	.$filtroPestana
			// .$filtroProcedimiento
			// ."	  AND Detest = 'on'	"
			// ."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
			// .$filtroProImprimir
			// ."	  AND Detcod = e.Codigo "
			// ."	  AND e.Tipoestudio = d.Codigo"
			// ."	  AND nuevo = 'on' "
			// ."	GROUP BY Ordtor, Ordnro "
			// ."	ORDER BY Tipanx DESC, Fecha_data DESC ";
			
		// Consulto los tipos de orden del paciente
		$q = " SELECT a.Fecha_data, Ordhis, Ording, Ordtor, Ordnro, Tipfrm, Tipanx, Ordtor, Tipaut "
			."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000015 d, ".$wbasedatohce."_000047 e "
			." 	WHERE Ordhis = '".$whistoria."' "
			."	  AND Ording = '".$wingreso."'  "
			."	  AND Ordest = 'on'  "
			."	  AND Ordtor = Dettor "
			."	  AND Ordnro = Detnro "
			.$filtroProcedimiento
			."	  AND Detest = 'on'	"
			."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
			.$filtroProImprimir
			."	  AND Detcod = e.Codigo "
			."	  AND d.Codigo = a.Ordtor "
			."	GROUP BY Ordtor, Ordnro "
			."	UNION "
			." SELECT a.Fecha_data, Ordhis, Ording, Ordtor, Ordnro, Tipfrm, Tipanx, Ordtor, Tipaut "
			."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000015 d, ".$wbasedatohce."_000017 e "
			." 	WHERE Ordhis = '".$whistoria."' "
			."	  AND Ording = '".$wingreso."'  "
			."	  AND Ordest = 'on'  "
			."	  AND Ordtor = Dettor "
			."	  AND Ordnro = Detnro "
			.$filtroProcedimiento
			."	  AND Detest = 'on'	"
			."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
			.$filtroProImprimir
			."	  AND Detcod = e.Codigo "
			."	  AND d.Codigo = a.Ordtor "
			."	  AND nuevo = 'on' "
			."	GROUP BY Ordtor, Ordnro "
			."	ORDER BY Tipanx DESC, Fecha_data DESC ";
		$restip = mysql_query($q,$conex) or die (mysql_errno()." - $q ".mysql_error());

		$ordenAnexa = "";
		$examenesPorHoja = 8;

		// Array para almacenar las ordenes anexas
		// El índice indica el tipo de orden y el valor indica el contenido HTML de la orden
		$arrOrdAnx = array();


		while($rowtip = mysql_fetch_array($restip))
		{
			if(trim($rowtip['Tipanx'])!="")
				$ordenAnexa = trim($rowtip['Tipanx']);

			$ordenActual = trim($rowtip['Ordtor']);

			if($ordenAnexa != $ordenActual)
			{
				// Control del salto de página
				if(!$seccion_par)
				{
					$salto_pagina = false;
					$seccion_par = true;
				}
				else
				{
					$salto_pagina = true;
					$seccion_par = false;
				}
			}

			// if($num > 15)
			// {
				// $salto_pagina = true;
				// $seccion_par = false;
			// }

			// Si e tipo de orden no está relacionado con un formulario de historia clínica eléctrónica
			if(trim($rowtip['Tipfrm'])=="")
			{

				// Consulto la orden y datos del paciente
				$q = " SELECT 	c.Fecha_data Fecha, c.Hora_data Hora, Ordhis, Ording, Detfec, Detjus, j.Codigo as Codigo, Descripcion, Servicio, b.Cconom AS NombreServicio, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, g.Ccocod AS CodigoCco, g.Cconom AS NombreCco, Ingtel, '' AS Ingdir, '' AS Ingmun, NoPos, Ordnro, Detite "
					."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON b.Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000047 d, ".$wbasedato."_000016 e, ".$wbasedato."_000018 f LEFT JOIN ".$wbasedato."_000011 g ON Ubisac = g.Ccocod, root_000036 h, root_000037 i, root_000012 j "
					." 	WHERE Ordhis = '".$whistoria."' "
					."	  AND Ording = '".$wingreso."'  "
					."	  AND Ordest = 'on'  "
					."	  AND Ordtor = '".$rowtip['Ordtor']."' "
					."	  AND Ordnro = '".$rowtip['Ordnro']."' "
					."	  AND Ordtor = Dettor "
					."	  AND Ordnro = Detnro "
				//	.$filtroPestana
					.$filtroProcedimiento
					."	  AND Detest = 'on'	"
					."	  AND Detcod = d.Codigo "
					."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
					.$filtroProImprimir
					// ."	  AND Servicio = Ordtor "	//2016-01-18. Quito esta la condición por que se pudo haber guardardo el registro con un servicio diferente al actual
					."	  AND Ordhis = Inghis "
					."	  AND Ording = Inging "
					."	  AND Inghis = Ubihis "
					."	  AND Inging = Ubiing "
					."	  AND Ordhis = Orihis  "
					."	  AND Ording = Oriing  "
					."	  AND Oriced = Pacced "
					."	  AND Oritid = Pactid "
					."	  AND Oriori = '".$wemp_pmla."'  "
					."	  AND d.Codcups = j.Codigo  "
					."	GROUP BY Detcod, Detite "
					."	UNION "
					." SELECT 	c.Fecha_data Fecha, c.Hora_data Hora, Ordhis, Ording, Detfec, Detjus, '' as Codigo, Descripcion, Servicio, b.Cconom AS NombreServicio, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, g.Ccocod AS CodigoCco, g.Cconom AS NombreCco, Ingtel, '' AS Ingdir, '' AS Ingmun, NoPos, Ordnro, Detite "
					."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON b.Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000017 d, ".$wbasedato."_000016 e, ".$wbasedato."_000018 f LEFT JOIN ".$wbasedato."_000011 g ON Ubisac = g.Ccocod, root_000036 h, root_000037 i "
					." 	WHERE Ordhis = '".$whistoria."' "
					."	  AND Ording = '".$wingreso."'  "
					."	  AND Ordest = 'on'  "
					."	  AND Ordtor = '".$rowtip['Ordtor']."' "
					."	  AND Ordnro = '".$rowtip['Ordnro']."' "			
					."	  AND Ordtor = Dettor "
					."	  AND Ordnro = Detnro "
				//	.$filtroPestana
					.$filtroProcedimiento
					."	  AND Detest = 'on'	"
					."	  AND Detcod = d.Codigo "
					."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
					.$filtroProImprimir
					// ."	  AND Servicio = Ordtor "	//2016-01-18. Quito esta la condición por que se pudo haber guardardo el registro con un servicio diferente al actual
					."	  AND Ordhis = Inghis "
					."	  AND Ording = Inging "
					."	  AND Inghis = Ubihis "
					."	  AND Inging = Ubiing "
					."	  AND Ordhis = Orihis  "
					."	  AND Ording = Oriing  "
					."	  AND Oriced = Pacced "
					."	  AND Oritid = Pactid "
					."	  AND Oriori = '".$wemp_pmla."'  "
					."	  AND d.Nuevo = 'on'  "
					."	GROUP BY Detcod, Detite "
					."	ORDER BY Fecha, Hora ";

				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);


				// Si se encontraron procedimienos ordenados
				if($num > 0)
				{

					$row = mysql_fetch_array($res);

					if( empty($pacced) ){
						
						$ingnre = $row['Ingnre'];
						$pacced = $row['Pacced'];
						$pactid = $row['Pactid'];
						$pacno1 = $row['Pacno1'];
						$pacno2 = $row['Pacno2'];
						$pacap1 = $row['Pacap1'];
						$pacap2 = $row['Pacap2'];
						$pacnac = $row['Pacnac'];
						$pacsex = $row['Pacsex'];
						$diagnostico = $wdiagnostico;
					}
					
					


					// Ciclo para mostrar la lista de procedimientos
					for($i=0;$i<$num;$i++)
					{
						if($ordenAnexa != $ordenActual && $i%$procedimientosPorHoja == 0)
						{
							if($rowtip['Tipanx']!="")
							{
								$seccion_par = true;
								$salto_pagina = true;
								echo '</div>';
							}

							//if($salto_pagina)
							//{
							  // Inicio div principal
							  echo '
							  <div style="page-break-after: always;">';
							//}

							$subEncabezado = '';
							if(isset($pro) && $pro!="")
								$subEncabezado = '<br />Con CTC';

							echo '
							   <div>';

								// Inicio tabla principal
								echo '
								<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
								  <tbody>

									<tr>
									  <td>';

										pintarEncabezado($row['NombreServicio'].$subEncabezado,$whistoria,$row['Ordnro']);

						}
						elseif( $i%$procedimientosPorHoja == 0 )
						{
							echo '<div id="anx'.$ordenAnexa.'" style="display:none;">';

							// Encabezado Orden Anexa
							echo '
									<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
									  <tbody>
										<tr>
										  <td style="height:11px;">
											&nbsp;
										  </td>
										</tr>
										<tr>
										  <td style="height:21px;background-color:#EEEDED;">
											&nbsp; <b>Orden Anexa</b>
										  </td>
										</tr>
										<tr>
										  <td  style="height:11px;">
											&nbsp;
										  </td>
										</tr>
									  </tbody>
									</table>
								';
						}

						if( $i%$procedimientosPorHoja == 0 ){
							// Inicio tabla detalle
							echo '
									<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
									  <tbody>
										<tr>
										  <td>';
						}

						if($ordenAnexa != $ordenActual && $i%$procedimientosPorHoja == 0 )
						{
							// Inicio tablas lista detalle
							echo '
											<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
											  <tbody>
												<tr>
												  <td style="height:11px;">
													&nbsp;
												  </td>
												</tr>
												<tr>
												  <td style="height:21px;background-color:#EEEDED;">
													&nbsp; <b>Servicios Solicitados</b>
												  </td>
												</tr>
												<tr>
												  <td  style="height:11px;">
													&nbsp;
												  </td>
												</tr>';

							if(trim($rowtip['Tipaut'])=='on' )
							{
								echo '			<tr>
												  <td  style="height:11px;">
													Autorizar a nombre del Instituto de Cancerología
												  </td>
												</tr>
												<tr>
												  <td  style="height:11px;">
													&nbsp;
												  </td>
												</tr>';
							}

							echo '
											  </tbody>
											</table>
								';
							
							echo '
										<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
										  <tbody>

											<tr>
											  <td style="width:7%";>CUPS</td>
											  <td style="width:45%;">
												<b></b>
											  </td>
											  <td style="width:48%;">
												<b>Justificaci&oacute;n</b>
											  </td>
											</tr>';
						}
												
						
						
						$procNombreCodigo = $row['Descripcion'];
						if( empty( $row['Codigo'] ) ){
							$row['Codigo'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
						}
						
						
						echo '
										<tr>
										  <td>
											'.$row['Codigo'].'
										  </td>
										  <td>
											&nbsp;'.$procNombreCodigo.'
										  </td>
										  <td>
											&nbsp;'.$row['Detjus'].'
										  </td>
										</tr>
										';

						$row = mysql_fetch_array($res);
						
						//Pinta la firma en caso de que se vaya a pintar un nuevo encabezado
						//o ya se termine te pintar todas la filas
						if( $i == $num-1 || ( $i > 0 && ($i+1)%$procedimientosPorHoja == 0 ) ){
						
						
							echo '
											  </tbody>
											</table>
								';


							// Fin tablas lista detalle

							// Fin tabla detalle
							echo '
										  </td>
										</tr>
									  </tbody>
									</table>';


							if($ordenAnexa == $ordenActual)
								echo "</div>";

							if(trim($rowtip['Tipanx'])!="")
								echo '<div id="ordenAnexa"></div>';


							if($ordenAnexa != $ordenActual)
							{

									pintarPiePagina();

									// Fin tabla principal
									echo '
										  </td>
										</tr>
									  </tbody>
									</table>';

								// Fin div principal
								echo '
								   </div>';

								//if($seccion_par)
								  echo '
								  </div>';
							}
						
						}
					}
				}
			}
			else
			{

				// Consulto la orden y datos del paciente
			    $q = " SELECT 	c.Fecha_data Fecha, c.Hora_data Hora, Ordhis, Ording, Detfec, Detjus, Codigo, Descripcion, Servicio, b.Cconom AS NombreServicio, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, g.Ccocod AS CodigoCco, g.Cconom AS NombreCco, Ingtel, '' AS Ingdir, '' AS Ingmun, Ordnro, Detifh, Detite, Detcod, Dettor, Detnro "
					."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON b.Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000047 d, ".$wbasedato."_000016 e, ".$wbasedato."_000018 f LEFT JOIN ".$wbasedato."_000011 g ON Ubisac = g.Ccocod, root_000036 h, root_000037 i "
					." 	WHERE Ordhis = '".$whistoria."' "
					."	  AND Ording = '".$wingreso."'  "
					."	  AND Ordest = 'on'  "
					."	  AND Ordtor = '".$rowtip['Ordtor']."' "
					."	  AND Ordnro = '".$rowtip['Ordnro']."' "
					."	  AND Ordtor = Dettor "
					."	  AND Ordnro = Detnro "		
					.$filtroProcedimiento
					."	  AND Detest = 'on'	"
					."	  AND Detcod = Codigo "
					."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
					.$filtroProImprimir
					// ."	  AND Servicio = Ordtor "	//2016-01-18. Quito esta la condición por que se pudo haber guardardo el registro con un servicio diferente al actual
					."	  AND Ordhis = Inghis "
					."	  AND Ording = Inging "
					."	  AND Inghis = Ubihis "
					."	  AND Inging = Ubiing "
					."	  AND Ordhis = Orihis  "
					."	  AND Ording = Oriing  "
					."	  AND Oriced = Pacced "
					."	  AND Oritid = Pactid "
					."	  AND Oriori = '".$wemp_pmla."'  "
					."  GROUP BY Dettor, Detnro, Detite "
					." UNION  "
					." SELECT 	c.Fecha_data Fecha, c.Hora_data Hora, Ordhis, Ording, Detfec, Detjus, Codigo, Descripcion, Servicio, b.Cconom AS NombreServicio, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, g.Ccocod AS CodigoCco, g.Cconom AS NombreCco, Ingtel, '' AS Ingdir, '' AS Ingmun, Ordnro, Detifh, Detite, Detcod, Dettor, Detnro "
					."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON b.Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000017 d, ".$wbasedato."_000016 e, ".$wbasedato."_000018 f LEFT JOIN ".$wbasedato."_000011 g ON Ubisac = g.Ccocod, root_000036 h, root_000037 i "
					." 	WHERE Ordhis = '".$whistoria."' "
					."	  AND Ording = '".$wingreso."'  "
					."	  AND Ordest = 'on'  "
					."	  AND Ordtor = '".$rowtip['Ordtor']."' "
					."	  AND Ordnro = '".$rowtip['Ordnro']."' "
					."	  AND Ordtor = Dettor "
					."	  AND Ordnro = Detnro "		
					.$filtroProcedimiento
					."	  AND Detest = 'on'	"
					."	  AND Detcod = Codigo "
					."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
					.$filtroProImprimir
					// ."	  AND Servicio = Ordtor "	//2016-01-18. Quito esta la condición por que se pudo haber guardardo el registro con un servicio diferente al actual
					."	  AND d.Nuevo = 'on'  "
					."	  AND Ordhis = Inghis "
					."	  AND Ording = Inging "
					."	  AND Inghis = Ubihis "
					."	  AND Inging = Ubiing "
					."	  AND Ordhis = Orihis  "
					."	  AND Ording = Oriing  "
					."	  AND Oriced = Pacced "
					."	  AND Oritid = Pactid "
					."	  AND Oriori = '".$wemp_pmla."'  "
					."  GROUP BY Dettor, Detnro, Detite "
					."	ORDER BY Fecha, Hora ";
				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);

				// Si se encontraron procedimienos ordenados
				if($num > 0)
				{
					while( $row = mysql_fetch_array($res) ){
						
						
						
						$ingnre = $row['Ingnre'];
						$pacced = $row['Pacced'];
						$pactid = $row['Pactid'];
						$pacno1 = $row['Pacno1'];
						$pacno2 = $row['Pacno2'];
						$pacap1 = $row['Pacap1'];
						$pacap2 = $row['Pacap2'];
						$pacnac = $row['Pacnac'];
						$pacsex = $row['Pacsex'];
						$diagnostico = $wdiagnostico;
						
						
						
						//////////////////////////////////////////////////////////////////////////////////
						// Si es una orden de una Solicitud de Transfusión Sanguínea
						if(trim($rowtip['Tipfrm'])=="000068")
						{
							// Consulto los datos de la orden
							$qfrm = "  SELECT Detpro, Detcon, Detnpa, movdat "
									."   FROM ".$wbasedatohce."_000002 a, ".$wbasedatohce."_000068 b, ".$wbasedatohce."_000036 c "
									." 	WHERE Detpro = '000068' "
									."	  AND Detpro = movpro "
									."	  AND Detcon = movcon "
									."	  AND movhis = '".$whistoria."' "
									."	  AND moving = '".$wingreso."'  "
									."	  AND c.id = '".$row['Detifh']."'  "
									."	  AND b.fecha_data = c.fecha_data  "
									."	  AND b.hora_data = c.hora_data  ";

							$resfrm = mysql_query($qfrm,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfrm = mysql_num_rows($resfrm);


							// Si se encontraron procedimienos ordenados
							if($numfrm > 0)
							{

								$datosFormulario = array();

								while($rowfrm = mysql_fetch_array($resfrm))
								{
									$indice = $rowfrm['Detcon'];
									$datosFormulario[$indice] = $rowfrm['movdat'];
								}

								if($ordenAnexa != $ordenActual)
								{

									if($rowtip['Tipanx']!="")
									{
										$seccion_par = true;
										$salto_pagina = true;
										echo '</div>';
									}

									//if($salto_pagina)
									//{
									  // Inicio div principal
									  echo '
									  <div style="page-break-after: always;">';
									//}

									echo '
									   <div>';

										// Inicio tabla principal
										echo '
										<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
										  <tbody>

											<tr>
											  <td>';

												pintarEncabezado($row['NombreServicio'],$whistoria,$row['Ordnro']);
								}
								else
								{
									echo '<div id="anx'.$ordenAnexa.'" style="display:none;">';

									// Encabezado Orden Anexa
									echo '
											<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
											  <tbody>
												<tr>
												  <td style="height:11px;">
													&nbsp;
												  </td>
												</tr>
												<tr>
												  <td style="height:21px;background-color:#EEEDED;">
													&nbsp; <b>Orden Anexa</b>
												  </td>
												</tr>
												<tr>
												  <td  style="height:11px;">
													&nbsp;
												  </td>
												</tr>
											  </tbody>
											</table>
										';
								}

									// Inicio tabla detalle
									echo '
											<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
											  <tbody>
												<tr>
												  <td>';

								if($ordenAnexa != $ordenActual)
								{
									// Inicio tablas lista detalle
									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr>
														  <td style="height:11px;">
															&nbsp;
														  </td>
														</tr>
														<tr>
														  <td style="height:21px;background-color:#EEEDED;">
															&nbsp; <b>Servicios Solicitados</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
										';

									if(trim($rowtip['Tipaut'])=='on')
									{
										echo '			<tr>
														  <td  style="height:11px;">
															Autorizar a nombre del Instituto de Cancerología
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>';
									}

									echo '				  </tbody>
													</table>
										';
								}


									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>

														<tr height="19px">
														  <td style="width:30%;">
															<u>Valor de la hemoglobina</u> &nbsp; '.$datosFormulario[9].'
														  </td>
														  <td style="width:33%;">
															<u>Valor de hematrocito</u> &nbsp; '.$datosFormulario[11].'
														  </td>
														  <td style="width:37%;">
															<u>Valor de recuento de plaquetas</u> &nbsp; '.$datosFormulario[13].'
														  </td>
														</tr>

														<tr height="19px">
														  <td style="width:30%;">
															<u>Glóbulos rojos empacados</u> &nbsp; '.$datosFormulario[15].'
														  </td>
														  <td style="width:33%;">
															<u>Concentrado de plaquetas</u> &nbsp; '.$datosFormulario[17].'
														  </td>
														  <td style="width:37%;">
															<u>Plaquetas por aféresis</u> &nbsp; '.$datosFormulario[19].'
														  </td>
														</tr>

														<tr height="19px">
														  <td colspan="3">
															<u>Otros</u> &nbsp; '.$datosFormulario[21].'
														  </td>
														</tr>


														<tr height="19px">
														  <td style="width:30%;">
															<u>¿Transfundir urgente?</u> &nbsp; '.valorBoleanoHCE($datosFormulario[24]).'
														  </td>
														  <td style="width:33%;">
															<u>¿Ha sido transfundido antes?</u> &nbsp; '.valorBoleanoHCE($datosFormulario[26]).'
														  </td>
														  <td style="width:37%;" colspan="2">
															<u>¿Firma consentimiento informado?</u> &nbsp; '.valorBoleanoHCE($datosFormulario[28]).'
														  </td>
														</tr>

														<tr height="19px">
														  <td style="width:30%;" colspan="3">
															<u>¿Ha tenido reacciones transfusionales?</u> &nbsp; '.$datosFormulario[21].' &nbsp; &nbsp;
															<u>¿Cuáles reacciones?</u> &nbsp; '.$datosFormulario[30].'
														  </td>
														</tr>';


									echo '
													  </tbody>
													</table>
										';


									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr style="height:24px;">
														  <td>
															&nbsp;Justificaci&oacute;n: '.$row['Detjus'].'
														  </td>
														</tr>
													  </tbody>
													</table>
										';

																		// Fin tablas lista detalle

									// Fin tabla detalle
									echo '
												  </td>
												</tr>
											  </tbody>
											</table>';


								if($ordenAnexa == $ordenActual)
									echo "</div>";

								if(trim($rowtip['Tipanx'])!="")
									echo '<div id="ordenAnexa"></div>';


								if($ordenAnexa != $ordenActual)
								{

										pintarPiePagina();

										// Fin tabla principal
										echo '
											  </td>
											</tr>
										  </tbody>
										</table>';

									// Fin div principal
									echo '
									   </div>';

									//if($seccion_par)
									  echo '
									  </div>';
									 //else
										pintarSaltosLinea(0);
								}
							}
						}
						//////////////////////////////////////////////////////////////////////////////////
						// Si es una orden de una Solicitud de Hospitalización
						elseif(trim($rowtip['Tipfrm'])=="000069")
						{
							// Consulto los datos de la orden
							$qfrm = "  SELECT Detpro, Detcon, Detnpa, movdat "
									."   FROM ".$wbasedatohce."_000002 a, ".$wbasedatohce."_000069 b, ".$wbasedatohce."_000036 c  "
									." 	WHERE Detpro = '000069' "
									."	  AND Detpro = movpro "
									."	  AND Detcon = movcon "
									."	  AND movhis = '".$whistoria."' "
									."	  AND moving = '".$wingreso."'  "
									."	  AND c.id = '".$row['Detifh']."'  "
									."	  AND b.fecha_data = c.fecha_data  "
									."	  AND b.hora_data = c.hora_data  ";

							$resfrm = mysql_query($qfrm,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfrm = mysql_num_rows($resfrm);


							// Si se encontraron procedimienos ordenados
							if($numfrm > 0)
							{
								$datosFormulario = array();

								while($rowfrm = mysql_fetch_array($resfrm))
								{
									$indice = $rowfrm['Detcon'];
									$datosFormulario[$indice] = $rowfrm['movdat'];
								}

								if($ordenAnexa != $ordenActual)
								{

									if($rowtip['Tipanx']!="")
									{
										$seccion_par = true;
										$salto_pagina = true;
										echo '</div>';
									}

									//if($salto_pagina)
									//{
									  // Inicio div principal
									  echo '
									  <div style="page-break-after: always;">';
									//}

									echo '
									   <div>';

										// Inicio tabla principal
										echo '
										<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
										  <tbody>

											<tr>
											  <td>';

												pintarEncabezado($row['NombreServicio'],$whistoria,$row['Ordnro']);
								}
								else
								{
									echo '<div id="anx'.$ordenAnexa.'" style="display:none;">';

									// Encabezado Orden Anexa
									echo '
											<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
											  <tbody>
												<tr>
												  <td style="height:11px;">
													&nbsp;
												  </td>
												</tr>
												<tr>
												  <td style="height:21px;background-color:#EEEDED;">
													&nbsp; <b>Orden Anexa</b>
												  </td>
												</tr>
												<tr>
												  <td  style="height:11px;">
													&nbsp;
												  </td>
												</tr>
											  </tbody>
											</table>
										';
								}


									// Inicio tabla detalle
									echo '
											<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
											  <tbody>
												<tr>
												  <td>';

								if($ordenAnexa != $ordenActual)
								{
									// Inicio tablas lista detalle
									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr>
														  <td style="height:11px;">
															&nbsp;
														  </td>
														</tr>
														<tr>
														  <td style="height:21px;background-color:#EEEDED;">
															&nbsp; <b>Servicios Solicitados</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
													  </tbody>
													</table>
										';

									if(trim($rowtip['Tipaut'])=='on')
									{
										echo '
														<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
														  <tbody>
															<tr style="height:24px;">
															  <td>
																&nbsp;Autorizar a nombre del Instituto de Cancerología
															  </td>
															</tr>
														  </tbody>
														</table>
											';
									}
								}

									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr style="height:24px;">
														  <td>
															&nbsp;<b>Hospitalización</b>
														  </td>
														</tr>
													  </tbody>
													</table>
										';

									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>

														<tr height="21px">
														  <td style="width:12%;">
															<u>Días</u>
														  </td>
														  <td style="width:18%;">
															<u>A partir de</u>
														  </td>
														  <td style="width:20%;">
															<u>¿Requiere UCI?</u>
														  </td>
														  <td style="width:50%;">
															<u>Motivo</u>
														  </td>
														</tr>

														<tr height="21px">
														  <td>
															&nbsp;'.$datosFormulario[5].'
														  </td>
														  <td>
															&nbsp;'.$datosFormulario[6].'
														  </td>
														  <td>
															&nbsp;'.$datosFormulario[7].'
														  </td>
														  <td>
															&nbsp;'.$datosFormulario[8].'
														  </td>
														</tr>';




									echo '
													  </tbody>
													</table>
										';


									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr style="height:24px;">
														  <td>
															&nbsp;Observaciones: '.$datosFormulario[10].'
														  </td>
														</tr>
													  </tbody>
													</table>
										';


										// Fin tablas lista detalle

									// Fin tabla detalle
									echo '
												  </td>
												</tr>
											  </tbody>
											</table>';



								if($ordenAnexa == $ordenActual)
									echo "</div>";

								if(trim($rowtip['Tipanx'])!="")
									echo '<div id="ordenAnexa"></div>';


								if($ordenAnexa != $ordenActual)
								{

										pintarPiePagina();

										// Fin tabla principal
										echo '
											  </td>
											</tr>
										  </tbody>
										</table>';

									// Fin div principal
									echo '
									   </div>';

									//if($seccion_par)
									  echo '
									  </div>';
									//else
										//pintarSaltosLinea(3);
								}
							}
						}
						//////////////////////////////////////////////////////////////////////////////////
						// Si es una orden de una Incapacidad
						elseif(trim($rowtip['Tipfrm'])=="000074")
						{
							// Consulto los datos de la orden
							$qfrm = "  SELECT Detpro, Detcon, Detnpa, movdat "
									."   FROM ".$wbasedatohce."_000002 a, ".$wbasedatohce."_000074 b, ".$wbasedatohce."_000036 c  "
									." 	WHERE Detpro = '000074' "
									."	  AND Detpro = movpro "
									."	  AND Detcon = movcon "
									."	  AND movhis = '".$whistoria."' "
									."	  AND moving = '".$wingreso."'  "
									."	  AND c.id = '".$row['Detifh']."'  "
									."	  AND b.fecha_data = c.fecha_data  "
									."	  AND b.hora_data = c.hora_data  ";

							$resfrm = mysql_query($qfrm,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfrm = mysql_num_rows($resfrm);


							// Si se encontraron procedimienos ordenados
							if($numfrm > 0)
							{
								$datosFormulario = array();

								while($rowfrm = mysql_fetch_array($resfrm))
								{
									$indice = $rowfrm['Detcon'];
									$datosFormulario[$indice] = $rowfrm['movdat'];
								}

								if($ordenAnexa != $ordenActual)
								{

									if($rowtip['Tipanx']!="")
									{
										$seccion_par = true;
										$salto_pagina = true;
										echo '</div>';
									}

									//if($salto_pagina)
									//{
									  // Inicio div principal
									  echo '
									  <div style="page-break-after: always;">';
									//}

									echo '
									   <div>';

										// Inicio tabla principal
										echo '
										<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
										  <tbody>

											<tr>
											  <td>';

												pintarEncabezado($row['NombreServicio'],$whistoria,$row['Ordnro']);
								}
								else
								{
									echo '<div id="anx'.$ordenAnexa.'" style="display:none;">';

									// Encabezado Orden Anexa
									echo '
											<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
											  <tbody>
												<tr>
												  <td style="height:11px;">
													&nbsp;
												  </td>
												</tr>
												<tr>
												  <td style="height:21px;background-color:#EEEDED;">
													&nbsp; <b>Orden Anexa</b>
												  </td>
												</tr>
												<tr>
												  <td  style="height:11px;">
													&nbsp;
												  </td>
												</tr>
											  </tbody>
											</table>
										';
								}


									// Inicio tabla detalle
									echo '
											<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
											  <tbody>
												<tr>
												  <td>';

								if($ordenAnexa != $ordenActual)
								{
									// Inicio tablas lista detalle
									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr>
														  <td style="height:11px;">
															&nbsp;
														  </td>
														</tr>
														<tr>
														  <td style="height:21px;background-color:#EEEDED;">
															&nbsp; <b>Servicios Solicitados</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
										';

										if(trim($rowtip['Tipaut'])=='on')
										{
											echo '			<tr>
															  <td  style="height:11px;">
																Autorizar a nombre del Instituto de Cancerología
															  </td>
															</tr>
															<tr>
															  <td  style="height:11px;">
																&nbsp;
															  </td>
															</tr>';
										}

										echo '			  </tbody>
													</table>
										';
								}

									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>

														<tr height="21px">
														  <td colspan="4">
															<b>Incapacidad: Enfermedad general</b>
														  </td>
														</tr>

														<tr height="21px">
														  <td style="width:30%;">
															<u>Fecha inicial(DD-MM-AAAA)</u>
														  </td>
														  <td style="width:30%;">
															<u>Fecha final(DD-MM-AAAA)</u>
														  </td>
														  <td style="width:20%;">
															<u>Días</u>
														  </td>
														  <td style="width:20%;">
															<u>¿Prorroga?</u>
														  </td>
														</tr>

														<tr height="21px">
														  <td>
															<b>'.date( "d-m-Y", strtotime( $datosFormulario[8] ) ).'</b>
														  </td>
														  <td>
															<b>'.date( "d-m-Y", strtotime( $datosFormulario[11] ) ).'</b>
														  </td>
														  <td>
															<b>'.$datosFormulario[9].( trim( $datosFormulario[10] ) != '' ? ' ('.trim( $datosFormulario[10] ).')':'' ).'</b>
														  </td>
														  <td>
															<b>'.$datosFormulario[7].'</b>
														  </td>
														</tr>';


									echo '
													  </tbody>
													</table>
										';


									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr style="height:24px;">
														  <td>
															&nbsp;Observaciones: '.$datosFormulario[13].'
														  </td>
														</tr>
													  </tbody>
													</table>
										';

										// Fin tablas lista detalle

									// Fin tabla detalle
									echo '
												  </td>
												</tr>
											  </tbody>
											</table>';


								if(trim($rowtip['Tipanx'])!="")
									echo '<div id="ordenAnexa"></div>';


								if($ordenAnexa == $ordenActual)
									echo "</div>";


								if($ordenAnexa != $ordenActual)
								{

										pintarPiePagina();

										// Fin tabla principal
										echo '
											  </td>
											</tr>
										  </tbody>
										</table>';

									// Fin div principal
									echo '
									   </div>';

									//if($seccion_par)
									  echo '
									  </div>';
									// else
										//pintarSaltosLinea(3);
								}
							}
						}
						//////////////////////////////////////////////////////////////////////////////////
						// Si es una orden de una Remisión o Contraremisión
						elseif(trim($rowtip['Tipfrm'])=="000076")
						{
							// Consulto los datos de la orden
							$qfrm = "  SELECT Detpro, Detcon, Detnpa, movdat "
									."   FROM ".$wbasedatohce."_000002 a, ".$wbasedatohce."_000076 b, ".$wbasedatohce."_000036 c  "
									." 	WHERE Detpro = '000076' "
									."	  AND Detpro = movpro "
									."	  AND Detcon = movcon "
									."	  AND movhis = '".$whistoria."' "
									."	  AND moving = '".$wingreso."'  "
									."	  AND c.id = '".$row['Detifh']."'  "
									."	  AND b.fecha_data = c.fecha_data  "
									."	  AND b.hora_data = c.hora_data  ";

							$resfrm = mysql_query($qfrm,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfrm = mysql_num_rows($resfrm);


							// Si se encontraron procedimienos ordenados
							if($numfrm > 0)
							{
								$datosFormulario = array();

								while($rowfrm = mysql_fetch_array($resfrm))
								{
									$indice = $rowfrm['Detcon'];
									$datosFormulario[$indice] = $rowfrm['movdat'];
								}

								if($ordenAnexa != $ordenActual)
								{

									if($rowtip['Tipanx']!="")
									{
										$seccion_par = true;
										$salto_pagina = true;
										echo '</div>';
									}

									//if($salto_pagina)
									//{
									  // Inicio div principal
									  echo '
									  <div style="page-break-after: always;">';
									//}

									echo '
									   <div>';

										// Inicio tabla principal
										echo '
										<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
										  <tbody>

											<tr>
											  <td>';

												pintarEncabezado($row['NombreServicio'],$whistoria,$row['Ordnro']);
								}
								else
								{
									echo '<div id="anx'.$ordenAnexa.'" style="display:none;">';

									// Encabezado Orden Anexa
									echo '
											<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
											  <tbody>
												<tr>
												  <td style="height:11px;">
													&nbsp;
												  </td>
												</tr>
												<tr>
												  <td style="height:21px;background-color:#EEEDED;">
													&nbsp; <b>Orden Anexa</b>
												  </td>
												</tr>
												<tr>
												  <td  style="height:11px;">
													&nbsp;
												  </td>
												</tr>
											  </tbody>
											</table>
										';
								}


									// Inicio tabla detalle
									echo '
											<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
											  <tbody>
												<tr>
												  <td>';

								if($ordenAnexa != $ordenActual)
								{
									// Inicio tablas lista detalle
									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr>
														  <td style="height:11px;">
															&nbsp;
														  </td>
														</tr>
														<tr>
														  <td style="height:21px;background-color:#EEEDED;">
															&nbsp; <b>Servicios Solicitados</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
													  </tbody>
													</table>
										';


									if(trim($rowtip['Tipaut'])=='on')
									{
										echo '
														<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
														  <tbody>
															<tr style="height:24px;">
															  <td>
																&nbsp;Autorizar a nombre del Instituto de Cancerología
															  </td>
															</tr>
														  </tbody>
														</table>
											';
									}
								}

									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>

														<tr height="21px">
														  <td style="width:50%;">
															<b>Remisión Interna</b>
														  </td>
														</tr>

														<tr height="28px">
														  <td style="width:50%;">
															<u>Servicio</u>
														  </td>
														</tr>

														<tr height="21px">
														  <td style="width:50%;">
															'.$datosFormulario[7].'
														  </td>
														</tr>';


									echo '
													  </tbody>
													</table>
										';


									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr style="height:24px;">
														  <td>
															&nbsp;Observaciones: '.$datosFormulario[9].'
														  </td>
														</tr>
													  </tbody>
													</table>
										';

									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr style="height:24px;">
														  <td>
															&nbsp;Resumen historia clínica:
														  </td>
														</tr>
													  </tbody>
													</table>
										';

										// Fin tablas lista detalle

									// Fin tabla detalle
									echo '
												  </td>
												</tr>
											  </tbody>
											</table>';


								if($ordenAnexa == $ordenActual)
									echo "</div>";

								if(trim($rowtip['Tipanx'])!="")
									echo '<div id="ordenAnexa"></div>';


								if($ordenAnexa != $ordenActual)
								{

										pintarPiePagina();

										// Fin tabla principal
										echo '
											  </td>
											</tr>
										  </tbody>
										</table>';

									// Fin div principal
									echo '
									   </div>';

									//if($seccion_par)
									  echo '
									  </div>';
									// else
										//pintarSaltosLinea(1);
								}
							}
						}						
						//Si es formulario de otras especialidades
						elseif(trim($rowtip['Tipfrm'])=="000142")
						{
							// Consulto los datos de la orden
							$qfrm = "  SELECT Detpro, Detcon, Detnpa, movdat "
									."   FROM ".$wbasedatohce."_000002 a, ".$wbasedatohce."_000142 b, ".$wbasedatohce."_000036 c  "
									." 	WHERE Detpro = '000142' "
									."	  AND Detpro = movpro "
									."	  AND Detcon = movcon "
									."	  AND movhis = '".$whistoria."' "
									."	  AND moving = '".$wingreso."'  "
									."	  AND c.id = '".$row['Detifh']."'  "
									."	  AND b.fecha_data = c.fecha_data  "
									."	  AND b.hora_data = c.hora_data  ";

							$resfrm = mysql_query($qfrm,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfrm = mysql_num_rows($resfrm);


							// Si se encontraron procedimienos ordenados
							if($numfrm > 0)
							{
								$datosFormulario = array();

								while($rowfrm = mysql_fetch_array($resfrm))
								{
									$indice = $rowfrm['Detcon'];
									$datosFormulario[$indice] = $rowfrm['movdat'];
								}

								if($ordenAnexa != $ordenActual)
								{

									if($rowtip['Tipanx']!="")
									{
										$seccion_par = true;
										$salto_pagina = true;
										echo '</div>';
									}

									//if($salto_pagina)
									//{
									  // Inicio div principal
									  echo '
									  <div style="page-break-after: always;">';
									//}

									echo '
									   <div>';

										// Inicio tabla principal
										echo '
										<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
										  <tbody>

											<tr>
											  <td>';

												pintarEncabezado($row['NombreServicio'],$whistoria,$row['Ordnro']);
								}
								else
								{
									echo '<div id="anx'.$ordenAnexa.'" style="display:none;">';

									// Encabezado Orden Anexa
									echo '
											<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
											  <tbody>
												<tr>
												  <td style="height:11px;">
													&nbsp;
												  </td>
												</tr>
												<tr>
												  <td style="height:21px;background-color:#EEEDED;">
													&nbsp; <b>Orden Anexa</b>
												  </td>
												</tr>
												<tr>
												  <td  style="height:11px;">
													&nbsp;
												  </td>
												</tr>
											  </tbody>
											</table>
										';
								}


									// Inicio tabla detalle
									echo '
											<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
											  <tbody>
												<tr>
												  <td>';

								if($ordenAnexa != $ordenActual)
								{
									// Inicio tablas lista detalle
									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>
														<tr>
														  <td style="height:11px;">
															&nbsp;
														  </td>
														</tr>
														<tr>
														  <td style="height:21px;background-color:#EEEDED;">
															&nbsp; <b>Servicios Solicitados</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
													  </tbody>
													</table>
										';


									if(trim($rowtip['Tipaut'])=='on')
									{
										echo '
														<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
														  <tbody>
															<tr style="height:24px;">
															  <td>
																&nbsp;Autorizar a nombre del Instituto de Cancerología
															  </td>
															</tr>
														  </tbody>
														</table>
											';
									}
								}

									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>

														<tr height="21px">
														  <td style="width:10%;">
															<b>Referencia a</b>
														  </td> 
														  <td style="width:50%;">
															'.$datosFormulario[6].'
														  </td>
														</tr>

														<tr height="28px">
														  <td style="width:10%;">
															<b>Otra</b>
														  </td>
														  <td style="width:50%;">
															'.$datosFormulario[7].'
														  </td>
														</tr>

														<tr height="21px">
														  <td style="width:10%;">
															<b>Motivo de la referencia</b>
														  </td><td style="width:50%;">
															'.$datosFormulario[10].'
														  </td>
														</tr>';


									echo '
													  </tbody>
													</table>
										';

										// Fin tablas lista detalle

									// Fin tabla detalle
									echo '
												  </td>
												</tr>
											  </tbody>
											</table>';


								if($ordenAnexa == $ordenActual)
									echo "</div>";

								if(trim($rowtip['Tipanx'])!="")
									echo '<div id="ordenAnexa"></div>';


								if($ordenAnexa != $ordenActual)
								{

										pintarPiePagina();

										// Fin tabla principal
										echo '
											  </td>
											</tr>
										  </tbody>
										</table>';

									// Fin div principal
									echo '
									   </div>';

									//if($seccion_par)
									  echo '
									  </div>';
									// else
										//pintarSaltosLinea(1);
								}
							}
						}
						//Si es formulario de referencia externa
						elseif(trim($rowtip['Tipfrm'])=="000147"){
							
							
							// Consulto los datos de la orden
							$qfrm_campos =  "  SELECT Detpro, Detcon, Detnpa, Detdes "
											."   FROM ".$wbasedatohce."_000002 "
											." 	WHERE Detpro = '000147' ";
							$resfrm_campos = mysql_query($qfrm_campos,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfrm_campos = mysql_num_rows($resfrm_campos);

							if($numfrm_campos > 0)
							{
								$camposFormulario = array();

								while($rowfrm_campos = mysql_fetch_array($resfrm_campos))
								{
									$indice_campos = $rowfrm_campos['Detcon'];
									$camposFormulario[$indice_campos] = $rowfrm_campos['Detdes'];
								}
							}
							
							// Consulto los datos de la orden
							$qfrm = "  SELECT Detpro, Detcon, Detnpa, movdat "
									."   FROM ".$wbasedatohce."_000002 a, ".$wbasedatohce."_000147 b, ".$wbasedatohce."_000036 c  "
									." 	WHERE Detpro = '000147' "
									."	  AND Detpro = movpro "
									."	  AND Detcon = movcon "
									."	  AND movhis = '".$whistoria."' "
									."	  AND moving = '".$wingreso."'  "
									."	  AND c.id = '".$row['Detifh']."'  "
									."	  AND b.fecha_data = c.fecha_data  "
									."	  AND b.hora_data = c.hora_data  ";
							$resfrm = mysql_query($qfrm,$conex) or die (mysql_errno()." - ".mysql_error());
							$numfrm = mysql_num_rows($resfrm);


							// Si se encontraron procedimienos ordenados
							if($numfrm > 0)
							{
								$datosFormulario = array();

								while($rowfrm = mysql_fetch_array($resfrm))
								{
									$indice = $rowfrm['Detcon'];
									$datosFormulario[$indice] = $rowfrm['movdat'];
								}

								if($ordenAnexa != $ordenActual)
								{

									if($rowtip['Tipanx']!="")
									{
										$seccion_par = true;
										$salto_pagina = true;
										echo '</div>';
									}

									//if($salto_pagina)
									//{
									  // Inicio div principal
									  echo '
									  <div style="page-break-after: always;">';
									//}

									echo '
									   <div>';

										// Inicio tabla principal
										echo '
										<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
										  <tbody>

											<tr>
											  <td>';

												pintarEncabezado_ref_ext($row['NombreServicio'],$whistoria,$row['Ordnro'], $datosFormulario);
								}
								


									// Inicio tabla detalle
									echo '
											<table style="border: 0px; width: 100%;" cellpadding="2" cellspacing="2">
											  <tbody>';

								if($ordenAnexa != $ordenActual)
								{
									// Inicio tablas lista detalle
									echo '
													<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
													  <tbody>														
														<tr>
														  <td style="height:21px;background-color:#EEEDED;" class="encabezadoImpresion">
															&nbsp; <b>Información clínica relevante</b>
														  </td>
														</tr>
														<tr>
														  <td  style="height:11px;">
															&nbsp;
														  </td>
														</tr>
													  </tbody>
													</table>
										';


									if(trim($rowtip['Tipaut'])=='on')
									{
										echo '
														<table style="width: 100%; text-align: left;" cellpadding="0" cellspacing="0">
														  <tbody>
															<tr style="height:24px;">
															  <td>
																&nbsp;Autorizar a nombre del Instituto de Cancerología
															  </td>
															</tr>
														  </tbody>
														</table>
											';
									}
								}

									echo "			<table style='text-align: left; width: 100%; height: auto;' border='0' cellpadding='2' cellspacing='2'>
													  <tbody>";
													  //Resumen de la anamnesis
														if($datosFormulario[59] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1' width='200px'><b>".$camposFormulario[58].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[59]."</td>
														</tr>";
														}
														//Examen físico normal?:
														if($datosFormulario[61] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[61].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[61]."</td>
														</tr>";
														}
														//Exámen físico
														if($datosFormulario[116] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[116].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[116]."</td>
														</tr>";
														}
														//Cabeza
														if($datosFormulario[63] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[62].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[63]."</td>
														</tr>";
														}
														//Órganos de los sentidos
														if($datosFormulario[65] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[64].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[65]."</td>
														</tr>";
														}
														//Cuello
														if($datosFormulario[67] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[66].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[67]."</td>
														</tr>";
														}
														//Tórax
														if($datosFormulario[69] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[68].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[69]."</td>
														</tr>";
														}
														//Axila
														if($datosFormulario[71] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[70].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[71]."</td>
														</tr>";
														}
														//Mamas
														if($datosFormulario[73] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[72].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[73]."</td>
														</tr>";
														}
														//Cardiopulmonar
														if($datosFormulario[75] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[74].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[75]."</td>
														</tr>";
														}
														//Abdomen
														if($datosFormulario[77] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[76].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[77]."</td>
														</tr>";
														}
														//Extremidades
														if($datosFormulario[79] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[78].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[79]."</td>
														</tr>";
														}
														//Genitourinario
														if($datosFormulario[81] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[80].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[81]."</td>
														</tr>";
														}
														//Neurológico
														if($datosFormulario[83] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[82].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[83]."</td>
														</tr>";
														}
														//Linfático y vascular
														if($datosFormulario[85] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[84].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[85]."</td>
														</tr>";
														}
														//Tacto rectal
														if($datosFormulario[87] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[86].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[87]."</td>
														</tr>";
														}
														//Tacto vaginal
														if($datosFormulario[89] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[88].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[89]."</td>
														</tr>";
														}
														//Piel
														if($datosFormulario[91] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[90].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[91]."</td>
														</tr>";
														}
														//Osteomuscular
														if($datosFormulario[93] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[92].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[93]."</td>
														</tr>";
														}
														//Otros
														if($datosFormulario[96] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[95].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[96]."</td>
														</tr>";
														}
														//Fecha y resultado de exámenes auxiliares diagnóstico
														if($datosFormulario[98] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[97].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[98]."</td>
														</tr>";
														}														
														//Resumen de la evolución
														if($datosFormulario[100] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[99].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[100]."</td>
														</tr>";
														}
														//Diagnóstico
														if($datosFormulario[102] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[101].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[102]."</td>
														</tr>";
														}
														//Complicaciones
														if($datosFormulario[104] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[103].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[104]."</td>
														</tr>";
														}
														//Tratamientos aplicados
														if($datosFormulario[106] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[105].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[106]."</td>
														</tr>";
														}
														//Motivos de remisión
														if($datosFormulario[108] != ''){ 
									echo "				<tr>
														  <td colspan='1' rowspan='1'><b>".$camposFormulario[107].":</b></td>
														  <td rowspan='1' colspan='11'>".$datosFormulario[108]."</td>
														</tr>";
														}
														
									echo "			  </tbody>
													</table>
													";


								if($ordenAnexa == $ordenActual)
									echo "</div>";

								if(trim($rowtip['Tipanx'])!="")
									echo '<div id="ordenAnexa"></div>';

								// Agosto 18 de 2016
								echo '											  
										  </tbody>
										</table>';
								

								if($ordenAnexa != $ordenActual)
								{

										pintarPiePagina();

										// Fin tabla principal
										echo '											  
										  </tbody>
										</table>';

									// Fin div principal
									echo '
									   </div>';

									//if($seccion_par)
									  echo '
									  </div>';
									// else
										//pintarSaltosLinea(1);
								}
							}
							
							
						}
					}
				}
			}

		}

		/******************* FIN IMPRESIÓN EXAMENES *******************/
	}




	// Consulto la orden y datos del paciente
	$q = " SELECT b.Fecha_data Fecha, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, Ingdir, Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo "
		."   FROM ".$wbasedato."_000060 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000036 g, root_000037 h "
		." 	WHERE Kadhis = '".$whistoria."' "
		."	  AND Kading = '".$wingreso."'  "
		// ."	  AND a.Fecha_data = Kadfec "
		// ."	  AND Kadfec = '".$fecha."' "		//2016-01-15. La idea es poder imprimir cualquier medicamento así no se halla ordenado la última vez
		.$filtroArtImprimir
		."	  AND Kadest = 'on'  "
	//	."	  AND Kadint = 'on'  "
	//	.$filtroArticulo
		."	  AND Kadart = Artcod "
		."	  AND Kadhis = Inghis "
		."	  AND Kading = Inging "
		."	  AND Inghis = Ubihis "
		."	  AND Inging = Ubiing "
		."	  AND Kadhis = Orihis  "
		."	  AND Kading = Oriing  "
		."	  AND Kadimp = 'on'  "
		."	  AND Oriced = Pacced "
		."	  AND Oritid = Pactid  "
		."	  AND Oriori = '".$wemp_pmla."'  ";
		// ."	GROUP BY Kadhis, Kading, Kadart ";
	//	."	ORDER BY Fecha DESC";

	// $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	// $num = mysql_num_rows($res);

	// // Si no se encontró en la tabla 000060 de movimiento hospitalario, busque en la tabla 000054
	// if($num==0)
	// {

		$q .= " UNION ";

		$q .= "SELECT b.Fecha_data Fecha, Kadhis, Kading, Kadart, Kadcma, Kadvia, Kaduma, Kaddia, Kadest, Kadess, Kadper, Kadffa, Kadfin, Kadhin, Kadfec, Kadcon, Kadobs, Kadori, Kadsus, Kadcnd, Kadcal, Kadcan, Kaddis, Kadcfr, Kadufr, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, Ccocod, Cconom, Ingtel, '' AS Ingdir, '' AS Ingmun, Artgen, Artpos, Kaddma, Kadpos, Kadupo "
			."   FROM ".$wbasedato."_000054 b, ".$wbasedato."_000026 c, ".$wbasedato."_000016 d, ".$wbasedato."_000018 e LEFT JOIN ".$wbasedato."_000011 f ON Ubisac = Ccocod, root_000036 g, root_000037 h  "
			." 	WHERE Kadhis = '".$whistoria."' "
			."	  AND Kading = '".$wingreso."'  "
			// ."	  AND a.Fecha_data = Kadfec "
			// ."	  AND Kadfec = '".$fecha."' "		//2016-01-15. La idea es poder imprimir cualquier medicamento así no se halla ordenado la última vez
			.$filtroArtImprimir
			."	  AND Kadest = 'on'  "
		//	."	  AND Kadint = 'on'  "
		//	.$filtroArticulo
			."	  AND Kadart = Artcod "
			."	  AND Kadhis = Inghis "
			."	  AND Kading = Inging "
			."	  AND Inghis = Ubihis "
			."	  AND Inging = Ubiing "
			."	  AND Kadhis = Orihis  "
			."	  AND Kading = Oriing  "
			."	  AND Kadimp = 'on'  "
			."	  AND Oriced = Pacced "
			."	  AND Oritid = Pactid  "
			."	  AND Oriori = '".$wemp_pmla."'  "
			// ."	GROUP BY Kadhis, Kading, Kadart "
			."	ORDER BY Fecha DESC";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);
	// }

	// Si se encontraron medicamentos ordenados
	if($num > 0)
	{
		$row = mysql_fetch_array($res);

		// Ciclo para mostrar la lista de medicamentos
		for($i=0;$i<$num;$i++)
		{
			// Si es medicamento No Pos lo adiciono al arrelo de estos
			if($row['Artpos']=='N')
				array_push($medicamentosNoPos, $row['Kadart']);

			$row = mysql_fetch_array($res);
		}

		$numNoPos = count($medicamentosNoPos);
		if($numNoPos>0)
		{
			
			for($i=0; $i<$numNoPos; $i++)
			{
			
				// if( !$mostrarSoloCTCArt )
				if( !( $mostrarSoloCTCPro || $mostrarSoloCTCArt ) )
				{

					if( $mostrarSoloCTC )
					{
						echo '<div style="page-break-after: always;">';
						echo "<div id='divctcart".$medicamentosNoPos[$i]."'>";
						echo "</div>";
						echo "</div>";

						echo "<script>";
						echo "consultarCTCArticulo( '".$whistoria."', '".$wingreso."', '".$medicamentosNoPos[$i]."', 'divctcart".$medicamentosNoPos[$i]."' );";
						echo "</script>";
					}
				}
				elseif( $mostrarSoloCTCArt ){
					echo '<div style="page-break-after: always;">';
					echo "<div id='divctcart".$medicamentosNoPos[$i]."'>";
					echo "</div>";
					echo "</div>";

					echo "<script>";
					echo "consultarCTCArticulo( '".$whistoria."', '".$wingreso."', '".$medicamentosNoPos[$i]."', 'divctcart".$medicamentosNoPos[$i]."' );";
					echo "</script>";
				}
			}
			
			// $mostrarSoloCTC = $auxSoloCTC;
		}
	}


	/*****************************************************************
	*********************** IMPRESIÓN EXAMENES ***********************
	******************************************************************/

	$examenesNoPosOrdenItem = Array();

	// // Consulto los tipos de orden del paciente
	// $q = " SELECT a.Fecha_data, Ordhis, Ording, Ordtor, Ordnro, Tipfrm, Tipanx, Ordtor, Tipaut "
		// ."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000015 d, ".$wbasedatohce."_000047 e "
		// ." 	WHERE Ordhis = '".$whistoria."' "
		// ."	  AND Ording = '".$wingreso."'  "
		// ."	  AND Ordest = 'on'  "
		// ."	  AND Ordtor = Dettor "
		// ."	  AND Ordnro = Detnro "
	// //	.$filtroPestana
	// //	.$filtroProcedimiento
		// ."	  AND Detest = 'on'	"
		// ."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
		// .$filtroProImprimir
		// ."	  AND Detcod = e.Codigo "
		// ."	  AND e.Tipoestudio = d.Codigo"
		// ."	GROUP BY Ordtor, Ordnro "
		// ."	UNION "
		// ." SELECT a.Fecha_data, Ordhis, Ording, Ordtor, Ordnro, Tipfrm, Tipanx, Ordtor, Tipaut "
		// ."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000015 d, ".$wbasedatohce."_000017 e "
		// ." 	WHERE Ordhis = '".$whistoria."' "
		// ."	  AND Ording = '".$wingreso."'  "
		// ."	  AND Ordest = 'on'  "
		// ."	  AND Ordtor = Dettor "
		// ."	  AND Ordnro = Detnro "
	// //	.$filtroPestana
	// //	.$filtroProcedimiento
		// ."	  AND Detest = 'on'	"
		// ."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
		// .$filtroProImprimir
		// ."	  AND Detcod = e.Codigo "
		// ."	  AND e.Tipoestudio = d.Codigo"
		// ."    AND nuevo = 'on' "
		// ."	GROUP BY Ordtor, Ordnro "
		// ."	ORDER BY Tipanx DESC, Fecha_data DESC "; 
		
	// Consulto los tipos de orden del paciente
	// Se quita la relación de la tabla 000047 con la 15, esto debido a que puedo haber sido grabado de una forma diferente a lo que esta configurado acutalmente
	//La tabla 27 quedara relacionado con la tabla 15
	$q = " SELECT a.Fecha_data, Ordhis, Ording, Ordtor, Ordnro, Tipfrm, Tipanx, Ordtor, Tipaut "
		."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000015 d, ".$wbasedatohce."_000047 e "
		." 	WHERE Ordhis = '".$whistoria."' "
		."	  AND Ording = '".$wingreso."'  "
		."	  AND Ordest = 'on'  "
		."	  AND Ordtor = Dettor "
		."	  AND Ordnro = Detnro "
		."	  AND Detest = 'on'	"
		."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
		.$filtroProImprimir
		."	  AND Detcod = e.Codigo "
		."	  AND d.Codigo = a.Ordtor "
		."	GROUP BY Ordtor, Ordnro "
		."	UNION "
		." SELECT a.Fecha_data, Ordhis, Ording, Ordtor, Ordnro, Tipfrm, Tipanx, Ordtor, Tipaut "
		."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000015 d, ".$wbasedatohce."_000017 e "
		." 	WHERE Ordhis = '".$whistoria."' "
		."	  AND Ording = '".$wingreso."'  "
		."	  AND Ordest = 'on'  "
		."	  AND Ordtor = Dettor "
		."	  AND Ordnro = Detnro "
		."	  AND Detest = 'on'	"
		."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
		.$filtroProImprimir
		."	  AND Detcod = e.Codigo "
		."	  AND d.Codigo = a.Ordtor "
		."    AND nuevo = 'on' "
		."	GROUP BY Ordtor, Ordnro "
		."	ORDER BY Tipanx DESC, Fecha_data DESC "; 
	$restip = mysql_query($q,$conex) or die (mysql_errno()." - $q ".mysql_error());

	while($rowtip = mysql_fetch_array($restip))
	{

		if(trim($rowtip['Tipfrm'])=="")
		{

			// Consulto la orden y datos del paciente
			$q = " SELECT 	c.Fecha_data Fecha, c.Hora_data Hora, Ordhis, Ording, Detfec, Detjus, Codigo, Descripcion, Servicio, b.Cconom AS NombreServicio, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, g.Ccocod AS CodigoCco, g.Cconom AS NombreCco, Ingtel, '' AS Ingdir, '' AS Ingmun, NoPos "
				."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON b.Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000047 d, ".$wbasedato."_000016 e, ".$wbasedato."_000018 f LEFT JOIN ".$wbasedato."_000011 g ON Ubisac = g.Ccocod, root_000036 h, root_000037 i "
				." 	WHERE Ordhis = '".$whistoria."' "
				."	  AND Ording = '".$wingreso."'  "
				."	  AND Ordest = 'on'  "
				."	  AND Ordtor = '".$rowtip['Ordtor']."' "
				."	  AND Ordnro = '".$rowtip['Ordnro']."' "
				."	  AND Ordtor = Dettor "
				."	  AND Ordnro = Detnro "
			//	.$filtroPestana
			//	.$filtroProcedimiento
				."	  AND Detest = 'on'	"
				."	  AND Detcod = Codigo "
				."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
				.$filtroProImprimir
				// ."	  AND Servicio = Ordtor "	//2016-01-18. Quito esta la condición por que se pudo haber guardardo el registro con un servicio diferente al actual
				."	  AND Ordhis = Inghis "
				."	  AND Ording = Inging "
				."	  AND Inghis = Ubihis "
				."	  AND Inging = Ubiing "
				."	  AND Ordhis = Orihis  "
				."	  AND Ording = Oriing  "
				."	  AND Oriced = Pacced "
				."	  AND Oritid = Pactid "
				."	  AND Oriori = '".$wemp_pmla."'  "
				."	GROUP BY Detcod "
				."	UNION "
				." SELECT 	c.Fecha_data Fecha, c.Hora_data Hora, Ordhis, Ording, Detfec, Detjus, Codigo, Descripcion, Servicio, b.Cconom AS NombreServicio, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Ubihac, g.Ccocod AS CodigoCco, g.Cconom AS NombreCco, Ingtel, '' AS Ingdir, '' AS Ingmun, NoPos "
				."   FROM ".$wbasedatohce."_000027 a LEFT JOIN ".$wbasedato."_000011 b ON b.Ccocod = Ordtor, ".$wbasedatohce."_000028 c, ".$wbasedatohce."_000017 d, ".$wbasedato."_000016 e, ".$wbasedato."_000018 f LEFT JOIN ".$wbasedato."_000011 g ON Ubisac = g.Ccocod, root_000036 h, root_000037 i "
				." 	WHERE Ordhis = '".$whistoria."' "
				."	  AND Ording = '".$wingreso."'  "
				."	  AND Ordest = 'on'  "
				."	  AND Ordtor = '".$rowtip['Ordtor']."' "
				."	  AND Ordnro = '".$rowtip['Ordnro']."' "
				."	  AND Ordtor = Dettor "
				."	  AND Ordnro = Detnro "
			//	.$filtroPestana
			//	.$filtroProcedimiento
				."	  AND Detest = 'on'	"
				."	  AND Detcod = Codigo "
				."	  AND (Detesi = 'Pendiente' OR Detesi = 'PendienteResultado') "
				.$filtroProImprimir
				// ."	  AND Servicio = Ordtor "	//2016-01-18. Quito esta la condición por que se pudo haber guardardo el registro con un servicio diferente al actual
				."    AND nuevo = 'on' "
				."	  AND Ordhis = Inghis "
				."	  AND Ording = Inging "
				."	  AND Inghis = Ubihis "
				."	  AND Inging = Ubiing "
				."	  AND Ordhis = Orihis  "
				."	  AND Ording = Oriing  "
				."	  AND Oriced = Pacced "
				."	  AND Oritid = Pactid "
				."	  AND Oriori = '".$wemp_pmla."'  "
				."	GROUP BY Detcod "
				."	ORDER BY Fecha, Hora ";
 
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);


			// Si se encontraron procedimienos ordenados
			if($num > 0)
			{
				$row = mysql_fetch_array($res);

				// Ciclo para asignar los procedimientos NO POS
				for($i=0;$i<$num;$i++)
				{
					// Si es procedimiento No Pos lo adiciono al arrelo de estos
					if($row['NoPos']=='on'){
						array_push($examenesNoPos, $row['Codigo']);
						array_push($examenesNoPosOrdenItem, $rowtip['Ordtor']."-".$rowtip['Ordnro']);
					}

					$row = mysql_fetch_array($res);
				}
			}
		}

	}

	$numNoPos = count($examenesNoPos);
	if($numNoPos>0)
	{

		for($i=0; $i<$numNoPos; $i++)
		{

			// if( !$mostrarSoloCTCPro )
			if( !( $mostrarSoloCTCPro || $mostrarSoloCTCArt ) )
			{
			
				if($mostrarSoloCTC )
				{
					echo '<div style="page-break-after: always;">';
					echo "<div id='divctcpro".$examenesNoPos[$i]."_".$examenesNoPosOrdenItem[$i]."'>";
					echo "</div>";
					echo "</div>";

					echo "<script>";
					echo "consultarCTCProcedimiento( '".$whistoria."', '".$wingreso."', '".$examenesNoPos[$i]."', 'divctcpro".$examenesNoPos[$i]."_".$examenesNoPosOrdenItem[$i]."','".$examenesNoPosOrdenItem[$i]."' );";
					echo "</script>";
				}
			}
			elseif($mostrarSoloCTCPro){
				echo '<div style="page-break-after: always;">';
				echo "<div id='divctcpro".$examenesNoPos[$i]."_".$examenesNoPosOrdenItem[$i]."'>";
				echo "</div>";
				echo "</div>";

				echo "<script>";
				echo "consultarCTCProcedimiento( '".$whistoria."', '".$wingreso."', '".$examenesNoPos[$i]."', 'divctcpro".$examenesNoPos[$i]."_".$examenesNoPosOrdenItem[$i]."','".$examenesNoPosOrdenItem[$i]."' );";
				echo "</script>";
			}
		}
	}

}

echo '<script> if(document.getElementById("ordenAnexa")) { document.getElementById("ordenAnexa").innerHTML = document.getElementById("anx'.$ordenAnexa.'").innerHTML; } </script>';


echo '</body>
</html>';
?>
