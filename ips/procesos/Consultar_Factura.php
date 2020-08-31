<?php
include_once("conex.php");
//VAlidaci�n de usuario
$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}

if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

//Si el usuario no es v�lido se informa y no se abre el reporte
if (!$usuarioValidado and !isset($peticionAjax) )
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";
}
if (!$usuarioValidado and isset($peticionAjax) )
{
	echo "session die";
}
// Fin IF si el usuario no es v�lido
require_once("conex.php");


function anular_factura($wffue, $wffac)
{
	global $wbasedato;
	global $wemp_pmla;
	global $conex;
	global $wuser;
	global $wfecha;
	global $hora;
	global $wusuario;
	global $tablaConceptos;

	///aca hay que seguir anulando en la tabla 66 y 65 y en la 106 pero sabiendo si es el responsable principal o no, para saber en que campo
	// de la tabla 106 se devuelve la plata o el valor.

	//Verificar que no tenga recibo de caja, que no halla sido enviada o radicada.
	//Verificar que no tenga nota credito o debito

	//BUSCO SI LA FACTURA TIENE ALGUN DOCUMENTO ANEXO DESPUES DE HECHA
	$q = " SELECT count(*) "
	 ."   FROM ".$wbasedato."_000021 "
	 ."  WHERE rdeffa  = '".$wffue."'"
	 ."    AND rdefac  = '".$wffac."'"
	 ."    AND rdeest  = 'on' "
	 ."    AND rdereg  = '' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	if ($row[0] == 0)
	{
	 //BUSCO QUE LA FACTURA TENGA EL ESTADO GENERADA, QUE ES EL UNICO EN QUE SE PUEDE ANULAR
	 $q = " SELECT count(*) "
	     ."   FROM ".$wbasedato."_000018 "
	     ."  WHERE fenffa = '".$wffue."'"
	     ."    AND fenfac = '".$wffac."'"
	     ."    AND fenest = 'on' "
	     ."    AND fenesf ='GE' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $num = mysql_num_rows($res);
	 $row = mysql_fetch_array($res);
	 if (($row[0] > 0))
	    {
	     //ACA BUSCO QUE LA FACTURA CORRESPONDA AL PERIODO CONTABLE ACTUAL
	     //DE LO CONTRARIO NO SE DEJA ANULAR
	     $q = " SELECT count(*) "
		     ."   FROM ".$wbasedato."_000018 "
		     ."  WHERE fenffa = '".$wffue."'"			//fenffa=fuente factura
		     ."    AND fenfac = '".$wffac."'"			//fenfac=Numero factura
		     ."    AND fenest = 'on' "
		     ."    AND fenesf ='GE' "
		     ."    AND year(fenfec) = '".date("Y")."'"
		     ."    AND month(fenfec) = '".date("m")."'";
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $num = mysql_num_rows($res);
		 $row = mysql_fetch_array($res);

		 if ($row[0]>0)
	        {
		     $q = "UPDATE ".$wbasedato."_000018 "
			     ."   SET fenest = 'off' "
			     ." WHERE fenffa = '".$wffue."'"
			     ."   AND fenfac = '".$wffac."'"
			     ."   AND fenest = 'on' ";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 $q = "UPDATE ".$wbasedato."_000065 "
			     ."   SET fdeest = 'off' "
			     ." WHERE fdefue = '".$wffue."'"
			     ."   AND fdedoc = '".$wffac."'"
			     ."   AND fdeest = 'on' ";
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 //ELIMINO LA RELACION CON LOS ABONOS
			 $q = " UPDATE ".$wbasedato."_000021 "
		         ."    SET rdeffa  = '', "
		         ."        rdefac  = '' "
			     ."  WHERE rdeffa  = '".$wffue."'"
			     ."    AND rdefac  = '".$wffac."'"
			     ."    AND rdeest  = 'on' "
			     ."    AND rdereg != '' ";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 $q = "SELECT rcfffa, rcffac, rcfreg, rcfval, fencod, fenhis, fening, rcftip, gruabo, tcarfex, tcarfre "
			     ."  FROM ".$wbasedato."_000066, ".$wbasedato."_000018, ".$tablaConceptos.", ".$wbasedato."_000106 "
			     ." WHERE rcfffa     = '".$wffue."'"
			     ."   AND rcffac     = '".$wffac."'"
			     ."   AND rcfest     = 'on' "
			     ."   AND rcfffa     = fenffa "
			     ."   AND rcffac     = fenfac "
			     ."   AND rcfreg     = ".$wbasedato."_000106.id "
			     ."   AND tcarconcod = grucod "
			     ." GROUP BY 1,2,3,4,5,6,7,8,9 ";
			 $res_fac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $num = mysql_num_rows($res_fac);

			 if ($num > 0)
			    {
			     for ($i=1;$i<=$num;$i++)
			        {
				     $row = mysql_fetch_array($res_fac);

				     //ACA ACTIVO LOS CARGOS DE LA FACTURA QUE SE ESTA ANULANDO, EN LA PROPORCION QUE SE FACTURO
				     if ($row[7] == "E")           //Excedente. Este dato viene del campo 'rcftip' tipo de factura
				        {
					     if ($row[8] == "off")     //Si no es un abono
					        {
						     if ($row[9] > 0)      //Si si se facturo como excedente
						        {
						         $q = " UPDATE ".$wbasedato."_000106 "
							         ."    SET tcarfex  = tcarfex - ".$row[3]
							         ."  WHERE id       = ".$row[2]
							         ."    AND tcarest  = 'on' "
							         ."    AND tcarfex >= ".$row[3];
						        }
						       else                //Se facturo como excedente pero siendo reconocido -- Abril 16 de 2008
						          {
							       $q = " UPDATE ".$wbasedato."_000106 "
						         	   ."    SET tcarfre  = tcarfre - ".$row[3]
						               ."  WHERE id       = ".$row[2]
						               ."    AND tcarest  = 'on' "
						               ."    AND tcarfre >= ".$row[3];
							      }
						     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					        }
					       else                           //Si es Abono
					          {
						       if (abs($row[9]) > 0)      //Si si se facturo como excedente -- Abril 16 de 2008
						          {
								   $q = " UPDATE ".$wbasedato."_000106 "
								       ."    SET tcarfex       = tcarfex - ".$row[3]
								       ."  WHERE id            = ".$row[2]
								       ."    AND tcarest       = 'on' "
								       ."    AND abs(tcarfex) >= ".abs($row[3]);
							      }
							     else
							        {
								     $q = " UPDATE ".$wbasedato."_000106 "
								         ."    SET tcarfre       = tcarfre - ".$row[3]
								         ."  WHERE id            = ".$row[2]
								         ."    AND tcarest       = 'on' "
								         ."    AND abs(tcarfre) >= ".abs($row[3]);
								    }
							   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							  }
						}

				     if ($row[7] == "R")          //Reconocido. Este dato viene del campo 'rcftip' tipo de factura
				        {
					     if ($row[8] == "off")    //Si no es un abono
					        {
					         $q = " UPDATE ".$wbasedato."_000106 "
						         ."    SET tcarfre = tcarfre - ".$row[3]
						         ."  WHERE id                                  = ".$row[2]
						         ."    AND tcarest                             = 'on' "
						         ."    AND tcarfre                            >= ".$row[3];
						     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						    }
					       else                  //Si es Abono
					          {
							   $q = " UPDATE ".$wbasedato."_000106 "
							       ."    SET tcarfre = tcarfre - ".$row[3]
							       ."  WHERE id                                  = ".$row[2]
							       ."    AND tcarest                             = 'on' "
							       ."    AND abs(tcarfre)                        >= ".abs($row[3]);
							   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							  }
						}

					//Anulo el registro en la tabla 000066
					 $q = " UPDATE ".$wbasedato."_000066 "
				         ."    SET rcfest = 'off' "
				         ."  WHERE rcfreg = ".$row[2]
				         ."    AND rcfffa = '".$row[0]."'"
				         ."    AND rcffac = '".$row[1]."'";
				         //."    AND rcfest = 'on' ";
				     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				     //**************************
			         //Aca grabo la auditoria
			         //**************************
			         $q= " INSERT INTO ".$wbasedato."_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis    ,   auding    ,   audreg                , audacc         ,   audusu      , Seguridad) "
			            ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$row[5]."','".$row[6]."','".$row[0]."-".$row[1]."', 'Anulo Factura','".$wusuario."', 'C-".$wusuario."')";
			         $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				    }
				}
	         } //Fin del if ($num > 1) que indica que la factura es del mismo periodo al actual
	        else
	          {
			    return('LA FACTURA NO CORRESPONDE AL PERIODO ACTUAL, NO SE PUEDE ANULAR');
			  }
	    } //Fin del if ($num > 1)
	   else
	      {
		      return ("LA FACTURA YA ESTA ANULADA O YA HA SIDO ENVIADA NO SE PUEDE ANULAR");
		  }
	}
	else
	  {
	       return (" LA FACTURA TIENE UN DOCUMENTO ANEXO (Recibo o Nota), NO SE PUEDE ANULAR ");
	  }
	   return("ok");
}
if( $peticionAjax  == "anularFactura" ){
	$wfecha    = date('Y-m-d');
	$hora      = date("H:i:s");
	$wusuario  = $wuser;
	$anulacion = str_replace("\\", "", $anulacion);
	$anulacion = json_decode( $anulacion, true );

	$respuesta = anular_factura($anulacion['fuenteFactura'], $anulacion['numeroFactura']);
	if( $respuesta == "ok" ){
		foreach( $anulacion['causas'] as $key=>$codigoCausa ){

			$q = "INSERT INTO {$wbasedato}_000071 (`Medico`,`Fecha_data`,`Hora_data`,`Docfue`,`Docnum`,`Doccau`,`Docest`,`Seguridad`)
					  VALUES ('{$wbasedato}', '{$wfecha}', '{$hora}', '{$anulacion['fuenteFactura']}', '{$anulacion['numeroFactura']}', '{$codigoCausa}', 'on', 'C-{$wuser}')";
			$res = mysql_query( $q, $conex );
		}

		if( trim($anulacion['observacion']) != "" ){
			$q = "UPDATE {$wbasedato}_000018
				     SET Fenobs = '".utf8_decode($anulacion['observacion'])."'
				   WHERE Fenffa = '{$anulacion['fuenteFactura']}'
				     AND Fenfac = '{$anulacion['numeroFactura']}'";
			$res = mysql_query( $q, $conex );
		}
	}
	echo $respuesta;
	return;
}
?>

<html>
<head>
<title>CONSULTAR FACTURA IPS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<style>
	.botona{
			font-size:13px;
			font-family:Verdana,Helvetica;
			font-weight:bold;
			color:white;
			background:#638cb5;
			border:0px;
			margin-left: 1%;
			cursor: pointer;
		 }
	// --> Estilo
	fieldset{
		border: 2px solid #e2e2e2;
	}

	legend{
		border: 2px solid #e2e2e2;
		border-top: 0px;
		font-family: Verdana;
		background-color: #e6e6e6;
		font-size: 11pt;
	}
</style>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script src="../../../include/root/toJson.js" type="text/javascript"></script>
<script type="text/javascript">
	function enter()
	{
	   document.forms.consultar_factura.submit();
	}

	function enter1()
	{
	   document.forms.consultar_factura.submit();
	   alert ("Pulse de nuevo la tecla ENTER");
	}

	function anular()
   	  {
		/*if(confirm('�Est� seguro que desea ** ANULAR ** la Factura?')) {
			location.href=url;
	   	}*/
	   	$("#tr_causas").toggle();
	   	/*if($("#tr_causas").is(":visible") && $("tr[tipo='causa']").size() <= 0 ){
	   		alerta( "Para realizar la anulaci�n \n debe Seleccionar la(s) Causa(s)" );
	   	}*/

   	  }
	function valida_enviar(form)
	{
		//Inicio de validaciones
		if(form.wfue.value!='' && (form.wfac.value=='' || form.wfac.value=='%'))
			 {
			   alert("Debe entrar tambien un valor para la factura");
			   form.wfue.focus();
			   return false;
			 }

		form.submit();
	}

	function agregarCausa( select ){
		var select = jQuery( select );
		var causaSeleccionada = select.find("option:selected");
		var codigoCausa = $( causaSeleccionada ).val();
		var descripcion = $( causaSeleccionada ).html();
		if( $("tr[tipo='causa']").size() % 2 == 1 )
			clase = 'fila1';
			else
				clase = 'fila2';
		var fila = "<tr tipo='causa' codigoCausa='"+codigoCausa+"' texto='"+descripcion+"' class='"+clase+"' colspan='2'><td><div align='left' style='width:100%;'><font size='3' color='blue' style='cursor:pointer;' onclick='eliminarCausa(this)'>Eliminar</font>&nbsp;&nbsp;&nbsp;"+codigoCausa+"&nbsp;&nbsp;"+descripcion+"</div></td></tr>";
		$("#tr_slc").after( fila );
		$(causaSeleccionada).remove();
	}

	function eliminarCausa(eliminar){
		var fila = $(eliminar).parent().parent().parent();
		var codigoCausa = $( fila ).attr("codigoCausa");
		var descripcion = $( fila ).attr("texto");
		var opcion = "<option value='"+codigoCausa+"'>"+descripcion+"</option>";
		$("#cauSel").append(opcion);
		$(fila).remove();
	}

	function alerta( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
							$.unblockUI();
						}, 2000 );
	}

	function anularFactura( url ){

		var fuenteFactura = $("#fuenteFactura").val();
		var numeroFactura = $("#numeroFactura").val();
		var observacion   = $("#wobs").val();
		var anulacion     = new Object();
		var wbasedato     = $("#wbasedato").val();
		var wusuario      = $("#wusuario").val();
		var tablaConceptos= $("#tablaConceptos").val();
		if( $("tr[tipo='causa']").size() == 0 ){
			alerta("Debe seleccionar al menos una Causa");
			return;
		}
		anulacion.fuenteFactura = fuenteFactura;
		anulacion.numeroFactura = numeroFactura;
		anulacion.observacion   = observacion;
		anulacion.causas        = new Array();

		$("tr[tipo='causa']").each(function (x){
			anulacion.causas.push( $(this).attr('codigoCausa') );
		});

		anulacion = $.toJSON( anulacion );

		$.ajax({
                 url: "Consultar_Factura.php",
                type: "POST",
               async: false,
              before: $.blockUI({ message: $('#msjEspere') }),
                data: {
                      peticionAjax: "anularFactura",
				   		 wemp_pmla: $("#wemp_pmla").val(),
			             anulacion: anulacion,
			             wbasedato: wbasedato,
						 tablaConceptos: tablaConceptos
                      },
                success: function(data)
                {
                	$.unblockUI();
                	if( data != "ok" ){
                		alerta( data );
                	}else{
                		location.href=url;
                	}
                }
            });
	}
	function retornar( url ){
		window.location = url;
	}

</script>

</head>
<body>
<?php
  /************************************************
   *     PROGRAMA PARA LA CONSULTA DE FACTURAS    *
   ************************************************/

//==========================================================================================================================================
//PROGRAMA				      : REPORTE DE MOVIMIENTO DE ART�CULOS POR CONCEPTO                                                             |
//AUTOR                      : Juan Carlos Hern�ndez M.																						|
//FECHA CREACION             : Agosto 11 de 2006																							|
//FECHA ULTIMA ACTUALIZACION  : MAYO 24 de 2013.                                                                                       |
//DESCRIPCION			      : 																											|
//                                                                                                                                          |
//==========================================================================================================================================

//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES
//========================================================================================================================================\\
//	-->	2014-07-24, Camilo Zapata
//		se realiza la consulta de que el usuario posea  permisos de anular las facturas( fuente 20), en la tabla 81, 
//-------------------------------------------------------------------------------------------------------------------------------------------
//========================================================================================================================================\\
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------
// MAYO 24 DE 2013: Camilo Zapata
// SE hizo un filtro en la funcion ver_factura, no en el query sino en la parte donde se arma el html, que pregunta si un m�dico tiene mas de
//un registro en el maestre de m�dicos, con el fin de que solo coja el activo, en caso de que solo tenga un registro se permite pintar normal-
//mente                                                                                                                 \\
//========================================================================================================================================\\
// DICIEMBRE 29 DE 2011: Luis Haroldo Zapata Arismendy
//
// Se organiza el reporte para se muestre en pantalla en orden de fecha, fuente y n�mero de factura.                                                                                                                                   \\
// El orden  de fecha queda organizado de forma descendente para que muestre los datos mas actuales.
//=======================================================================================================================================//
// S E P T I E M B R E  24  DE 2011:                                                                                                      \\
//________________________________________________________________________________________________________________________________________\\
// Se quito la condici�n "medest = 'on'" en la consulta de conceptos  - Frederick Aguirre																										  \\
//
//
// S E P T I E M B R E  28  DE 2011:                                                                                                      \\
//________________________________________________________________________________________________________________________________________\\
// Se adicion� la condici�n "medest = 'on'" en la consulta de conceptos ya que se estaba mostrando registros para doctores que estan      \\
// deshabilitados - Mario Cadavid																										  \\
//																																		  \\
//________________________________________________________________________________________________________________________________________\\
// M A R Z O  30  DE 2011:                                                                                                         		  \\
//________________________________________________________________________________________________________________________________________\\
// En el detalle de la factura se adicion� la columna "Causa / Observacion" donde se muestra las causas  de devoluciones o glosas y las   \\
// observaciones que se hagan a los documentos 																							  \\
//																																		  \\
//________________________________________________________________________________________________________________________________________\\
//D I C I E M B R E  30  DE 2010:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
//En las consultas de facturas por far_pmla se estaban repitiendo las filas con los mismos registros, se modifico el Query y se corrigi�  \\
//Tambien se actualiz� los estilos de la aplicaci�n bas�ndose en la hoja de estilos por defecto del programa							  \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//J U L I O  21  DE 2008:                                                                                                                 \\
//________________________________________________________________________________________________________________________________________\\
//Se actualiza para que el funciona tanto para facturas Hospitalarias como POS                                                            \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//A B R I L  16  DE 2008:                                                                                                                 \\
//________________________________________________________________________________________________________________________________________\\
//Se hace modificacion a la anulaci�n de las facturas, porque se detecto que generan facturas como excedente, pero en realidad son        \\
//por reconocido, es decir los cargos facturados quedaron como excedente en la 000066 pero en la 106 se facturaron en la de reconocido.   \\
//Esto ocurre cuando el paciente es particular                                                                                            \\
//________________________________________________________________________________________________________________________________________\\
//________________________________________________________________________________________________________________________________________\\
//F E B R E R O  6  DE 2007:                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
//Se actualiza el programa para que se pueden hacer consultar facturas por el nombre del responsable (paciente) o documento de            \\
//identificaci�n del paciente o inclusive por ambos o en combinaci�n con el resto de campos de la pantalla.                               \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//F E B R E R O  2  DE 2007:                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa en la anulacion de facturar para que devuelva los valores correctos dependiendo de si se facturo excedente o    \\
//Reconocido utilizando el campo rcftip.                                                                                                  \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//D I C I E M B R E  15  DE 2006:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se pueden visualizar todos los documentos anexos a una factura, mostrandolos cronologicamente y a la   \\
//muestra el valor de cada documento, cuando se trata de envios o radicaci�n muestra el valor de todo el envio o radicaci�n y cuando es   \\
//un recibo o nota muestra el valor que corresponde para esa factura.                                                                     \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//N O V I E M B R E  30  DE 2006:                                                                                                         \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se elimina la relacion de la factura con los abonos cuando esta los posee, en la tabla 000021.         \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E  30  DE 2006:                                                                                                             \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se puedan anular las facturas y liberen de nuevo los cargos y poderlos volver a facturar, adem�s se    \\
//graba en la tabla de auditoria (000107) el registro de anulacion, teniendo en cuenta la historia, ingreso, fuente y numero de factura   \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\

include_once("root/comun.php");

$wactualiz="2014-07-24";
session_start();

//Llamo a la funci�n para formar el encabezado del reporte llev�ndole T�tulo, Fecha e Imagen o logo
encabezado("CONSULTA DE FACTURAS",$wactualiz,"clinica");

// Funci�n que permite cambiar el texto enviado por URL para evitar conversi�n de caracteres
function convertir_url ($cadena) {
	$cadena = str_replace("%","___",$cadena);
	return $cadena;
}

// Retorna el valor original de los par�metros de URL que se cambi� con la funci�n anterior
function desconvertir_url ($cadena) {
	$cadena = str_replace("___","%",$cadena);
	return $cadena;
}

if (!$usuarioValidado and !isset($peticionAjax) )
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";
}
else //Si el usuario es v�lido comenzamos con el reporte
{  //Inicio ELSE reporte

 //Conexion base de datos
 



 // Consulto los datos de la empresa actual y los asigno a la variable $empresa
 $consulta = consultarInstitucionPorCodigo($conex, $wemp_pmla);
 $empresa = $consulta->baseDeDatos;

	echo "<form name='consultar_factura' id='consultar_factura' action='Consultar_Factura.php' method=post onSubmit='return valida_enviar(this);'>";


    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////
    //Aca traigo las variables necesarias de la empresa
	$q = " SELECT empdes, emphos "
	    ."   FROM root_000050 "
	    ."  WHERE empcod = '".$wemp_pmla."'"
	    ."    AND empest = 'on' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	$wnominst=$row[0];
	$whosp=$row[1];

	/////////////////////////////////////////////////////////////////////////////////////////
	//Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	$q = " SELECT detapl, detval, empdes, empbda, emphos "
	    ."   FROM root_000050, root_000051 "
	    ."  WHERE empcod = '".$wemp_pmla."'"
	    ."    AND empest = 'on' "
	    ."    AND empcod = detemp ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0 )
	   {
	    for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);

	      $wbasedato=strtolower($row[3]);   //Base de dato de la empresa
	      $wemphos=$row[4];     //Indica si la facturacion es Hospitalaria o POS

	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];

	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];

	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];

	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];

	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];

	      $winstitucion=$row[2];
	     }
	   }
	  else
	    echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	/////////////////////////////////////////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////////////////////

	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	echo "<input type='hidden' id='tablaConceptos' value='".$tablaConceptos."'>";
	//----------------------------------------------------------------------------------------------

  $wcol=6;  //Numero de columnas que se tienen o se muestran en pantalla

  //===========================================================================================================================================
  function ver_factura($wffue, $wffac)
    {

	 global $wbasedato;
	 global $wemp_pmla;
	 global $conex;
	 global $wcf;
	 global $wcf2;
	 global $wclfa;
	 global $wclfg;
	 global $whosp;
	 global $tablaConceptos;
	 global $wuser;

	 $doctores = array();
	 $query = "SELECT meddoc
				 FROM {$wbasedato}_000051";
	 $rs    = mysql_query( $query, $conex );

	 while( $rowmed = mysql_fetch_array( $rs ) ){
		( !isset( $doctores[$rowmed['meddoc']]) ) ? $doctores[$rowmed['meddoc']] = 1 : $doctores[$rowmed['meddoc']] ++;
	 }
	 $doctores['0'] = 1; //artificio para que funcione normal en uvglobal quienes tienen cargos al documento 0

	 /*
	 $q = " SELECT fenffa, fenfac, fenfec, fencod, fennit, empnom, fenval, fenviv, fencop, fencmo, fendes, fenabo, fenvnd, fenvnc, fensal, fenesf, "
         ."        fenhis, fening, fencre, fenpde, fenrec, fentop, fenrln, fenest, pacap1, pacap2, pacno1, pacno2, fendev, fenrbo "
         ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000024, ".$wbasedato."_000100 "
     */
     $q = " SELECT fenffa, fenfac, fenfec, fencod, fennit, empnom, fenval, fenviv, fencop, fencmo, fendes, fenabo, fenvnd, fenvnc, fensal, fenesf, "
         ."        fenhis, fening, fencre, fenpde, fenrec, fentop, fenrln, fenest, fennpa, fendev, fenrbo, fenobs ,Fendpa"
         ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000024 " //, ".$wbasedato."_000100 "
         ."  WHERE fenffa like '".$wffue."'"
         ."    AND fenfac like '".$wffac."'"
         ."    AND fencod = empcod ";
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $num = mysql_num_rows($res);
	     $row = mysql_fetch_array($res);

		 $wfue       = $row[0];
	     $wfac       = $row[1];
	     $wfec       = $row[2];
	     $wcem       = $row[3];
	     $wnit       = $row[4];
	     $wnem       = $row[5];
	     $wval       = $row[6];
	     $wiva       = $row[7];
	     $wcop       = $row[8];
	     $wcmo       = $row[9];
	     $wdes       = $row[10];
	     $wabo       = $row[11];
	     $wvnd       = $row[12];
	     $wvnc       = $row[13];
	     $wsal       = $row[14];
	     $wesf       = $row[15];
	     $whis       = $row[16];
	     $wing       = $row[17];
	     $wcre       = $row[18];
	     $wpde       = $row[19];
	     $wrec       = $row[20];
	     $wtop       = $row[21];
	     $wrln       = $row[22];
	     $west       = $row[23];
	     //$wap1       = $row[24];
	     //$wap2       = $row[25];
	     //$wno1       = $row[26];
	     //$wno2       = $row[27];
	     $wnpa       = $row[24];
	     $wdev       = $row[25];
	     $wrbo       = $row[26];
	     $wobservacion= $row[27];

	     echo "<br>";
	     echo "<center><table border='0'>";
		
		  /*
			Query para poner nombre completo paciente
		  */
		  $qnombrepaciente = "SELECT Clinom FROM ".$wbasedato."_000041 WHERE Clidoc = '".$row['Fendpa']."' ";
		  $resqnom = mysql_query($qnombrepaciente,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qnombrepaciente." - ".mysql_error());
	      $numqnom = mysql_num_rows($resqnom);
	      $rowqnom = mysql_fetch_array($resqnom);
		  
		  
		  
	     //ACA PINTO EL ENCABEZADO DE LA FACTURA
	     echo "<tr>";
		 echo "<td align=left class='fila2' colspan=1><font text color=".$wclfg."> Fuente:<br><b>".$wfue."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=1><font text color=".$wclfg."> Factura:<br><b>".$wfac."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=1><font text color=".$wclfg."> Historia:<br><b>".$whis."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=1><font text color=".$wclfg."> Ingreso:<br><b>".$wing."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=2><font text color=".$wclfg."> Responsable:<br><b>".$wcem."-".$wnit."-".$wnem."</b></font></td>";
		 //echo "<td align=left class='fila2' colspan=3><font text color=".$wclfg."> Usuario:<br><b>".$wno1." ".$wno2." ".$wap1." ".$wap2."</b></font></td>";
		 echo "<td align=left class='fila2' colspan=3><font text color=".$wclfg."> Usuario:<br><b>".$rowqnom['Clinom']."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Cant.Resp.:<br><b>".$wcre."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> % Reconoc.:<br><b>".$wrec."</b></font></td>";
		 echo "</tr>";
		 echo "<tr>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Valor Factura:<br><b>".number_format($wval,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Devoluci�n:<br><b>".number_format($wdev,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> IVA:<br><b>".number_format($wiva,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Descuento:<br><b>".number_format($wdes,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> V/r Abonos:<br><b>".number_format($wabo,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> V/r CMO:<br><b>".number_format($wcmo,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> V/r Copago:<br><b>".number_format($wcop,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> V/r Recibos:<br><b>".number_format($wrbo,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Vlr Notas Debito:<br><b>".number_format($wvnd,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Vlr Notas Credito:<br><b>".number_format($wvnc,0,'.',',')."</b></font></td>";
		 echo "<td align=center class='fila2' colspan=1><font text color=".$wclfg."> Saldo:<br><b>".number_format($wsal,0,'.',',')."</b></font></td>";
		 echo "</tr>";
		 echo "<tr>";
		 $westcar= '';
		 switch ($wesf)
	          {
	           case "GE":
	               { $westcar="GENERADA"; }
	               break;
	           case "EV":
	               { $westcar="ENVIADA"; }
	               break;
	           case "RD":
	               { $westcar="RADICADA"; }
	               break;
	           case "DV":
	               { $westcar="DEVUELTA"; }
	               break;
	           case "GL":
	               { $westcar="GLOSADA"; }
	               break;
	          }

	          echo "<td align=left class='fila1' colspan=6><b>Estado en Cartera: <b>".$westcar."</b></td>";
		 if ($west == "on")
		    $westfac="ACTIVA";
		   else
		      $westfac="** ANULADA **";
		 echo "<td align=left class='fila1' colspan=5><b> Estado Factura: ".$westfac."</b></td>";
		 echo "</tr>";
		 echo "</table>";



	     $q = "  SELECT fdecco, ccodes, fdecon, grudes, fdeter, mednom, fdepte, fdevco, fdevde, fdeest, medest estado "
	         ."    FROM ".$wbasedato."_000065, ".$wbasedato."_000003, ".$tablaConceptos.", ".$wbasedato."_000051 "
	         ."   WHERE fdefue = '".$wfue."'"
	         ."     AND fdedoc = '".$wfac."'"
	         ."     AND fdecco = ccocod "
	         ."     AND fdecon = grucod "
	         ."     AND fdeter != '' "
	         ."     AND fdeter != ' ' "
	         ."     AND fdeter != 'NO APLICA' "
	         ."     AND fdeter != '0' "
	         ."     AND fdeter = meddoc "
			 //."	  GROUP BY 1,2,3,4,5,7,8,9,10"
			 //."     AND medest = 'on' "			// 2011-09-28  ----- //  2012-09-24

	         ."   UNION "

	         ."  SELECT fdecco, ccodes, fdecon, grudes, fdeter, '', fdepte, fdevco, fdevde, fdeest, '' estado"
	         ."    FROM ".$wbasedato."_000065, ".$wbasedato."_000003, ".$tablaConceptos." "
	         ."   WHERE fdefue = '".$wfue."'"
	         ."     AND fdedoc = '".$wfac."'"
	         ."     AND fdecco = ccocod "
	         ."     AND fdecon = grucod "
	         ."     AND (fdeter = '' "
	         ."      OR  fdeter = ' ' "
	         ."      OR  fdeter = 'NO APLICA' "
	         ."      OR  fdeter = '0' ) ";
	     $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $num1 = mysql_num_rows($res1);

	     if ($num1 > 0)
	        {
	         echo "<br>";
	         echo "<center><table border='0'>";
	         echo "<tr class='encabezadoTabla'>";
	         echo "<th colspan=2>CONCEPTO</th>";
	         echo "<th colspan=2>CENTRO DE COSTO</th>";
	         echo "<th colspan=3>TERCERO</th>";
	         echo "<th colspan=1>VALOR BRUTO</th>";
	         echo "<th colspan=1>DESCUENTO</th>";
	         echo "<th colspan=1>VALOR NETO</th>";
	         echo "</tr>";

	         $wtotcon=0;
	         $wtotdes=0;
	         for ($j=1;$j<=$num1;$j++)
	            {
					 $row1 = mysql_fetch_array($res1);
					 ( $row1[4]=="" or ( $doctores[$row1[4]]*1 == 1 ) ) ? $mostrarDirecto = true : $mostrarDirecto = false;
					 ( $doctores[$row1[4]]*1 > 1 ) ? $filtroMedico = true : $filtroMedico = false;
					 if( ( $mostrarDirecto ) or ( $filtroMedico and ($row1['estado'] == 'on') ) )
					 {
						 if ($j%2==0)
						   $wclass="fila1";
						 else
						   $wclass="fila2";
						 echo "<tr>";
						 echo "<td class='".$wclass."'>".$row1[2]."</td>";	 //CONCEPTO
						 echo "<td class='".$wclass."'>".$row1[3]."</td>";     //CONCEPTO
						 if ($row1[0] == "") 									 //PROCEDIMIENTO
							{
							 echo "<td class='".$wclass."'>&nbsp</td>";
							 echo "<td class='".$wclass."'>&nbsp</td>";
							}
						   else
							  {
							   echo "<td class='".$wclass."'>".$row1[0]."</td>";
							   echo "<td class='".$wclass."'>".$row1[1]."</td>";
							  }
						 if ($row1[4] == "")  									 //TERCERO
							{
							 echo "<td class='".$wclass."'>&nbsp</td>";
							 echo "<td class='".$wclass."'>&nbsp</td>";
							 echo "<td class='".$wclass."'>&nbsp</td>";
							}
						   else
							  {
							   echo "<td class='".$wclass."'>".$row1[4]."</td>";
							   echo "<td class='".$wclass."'>".$row1[5]."</td>";
							   echo "<td class='".$wclass."'>".$row1[6]."%</td>";
							  }
						 echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[7],0,'.',',')."</td>";
						 echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[8],0,'.',',')."</td>";
						 echo "<td align=RIGHT class='".$wclass."'>".number_format($row1[7]-$row1[8],0,'.',',')."</td>";

						 $wtotcon=$wtotcon+$row1[7];
						 $wtotdes=$wtotdes+$row1[8];
					 }
		        }
		     echo "<tr class='encabezadoTabla'>";
		     echo "<td align=RIGHT colspan=7><b>Totales</b></td>";
	         echo "<td align=RIGHT colspan=1><b>".number_format($wtotcon,0,'.',',')."</b></td>";
	         echo "<td align=RIGHT colspan=1><b>".number_format($wtotdes,0,'.',',')."</b></td>";
	         echo "<td align=RIGHT colspan=1><b>".number_format($wtotcon-$wtotdes,0,'.',',')."</b></td>";
	         echo "</tr>";

	         if (isset($whosp) and strtoupper($whosp)=="ON")
	            {
	             echo "<input type='hidden' id='fuenteFactura' value='".$wfue."'>";
          		 echo "<input type='hidden' id='numeroFactura' value='".$wfac."'>";
				
				 $puedeAnular = "off";
				 $queryAnular = " SELECT Peranu
								    FROM {$wbasedato}_000081
								   WHERE Perusu = '{$wuser}'
								     AND Perfue = '{$wfue}'";
				 $rsAnular   = mysql_query( $queryAnular, $conex );
				 while( $rowAnular = mysql_fetch_array( $rsAnular ) ){
					$puedeAnular = $rowAnular['Peranu'];
				 }
	             $causas=consultarCausas( $west, $wfue, $wfac );
				  if( $puedeAnular == "on" ){ 
					 echo "<tr>";
					 ( $west == "on" ) ? $anularFactura = "<font style='cursor:pointer;' color='blue' size='2' onclick='anular()'> Anular Factura </font>" : $anularFactura = "";
					 echo "<td class='fila1' colspan='10' align=center><b>{$anularFactura}</b></td>";
					 echo "</tr>";
				  }
					 
				 // FILAS PARA LA ANULACI�N DE FACTURAS.
				 if($west == "on" and $puedeAnular == "on" ){
				 	$visibilidad = "display:none;";
				 	$textHabilitado = "";
				 }else{
				 	$visibilidad = "";
				 	$textHabilitado = "disabled";
				 }
				 if( $puedeAnular == "on" ){
					 echo "<tr id='tr_causas' style='{$visibilidad}'>";
						echo "<td colspan='10'></br><div align='center'>";
							echo pintarCausas( $causas, $west  );
						echo "</div>";
						echo "</br>";
						echo "<div align='center'><table border=0 width='85%'><tr><td class=encabezadoTabla align=center><b>OBSERVACION:</b> </td></tr><tr><td class='fila2' align=center><b><textarea {$textHabilitado} name='wobs' id='wobs' cols='80' rows='3'>{$wobservacion}</textarea></td></tr></table>";
						($west !=  "on") ? $botonVisible = "style='display:none'" : $botonVisible = "";
						echo "<br><input type='button' value='Anular' {$botonVisible} onclick='anularFactura( \"/matrix/ips/procesos/Consultar_factura.php?wfactura=".$wfue."-".$wfac."&amp;wemp_pmla=".$wemp_pmla."&amp;wbasedato=".$wbasedato."&amp;wffue=".$wfue."&amp;tablaConceptos=".$tablaConceptos."&amp;wffac=".$wfac."\");'>";
						echo "</div></br>";
						echo "</td>";
					 echo "</tr>";
				 }
			    }


			 echo "<tr>";
	         $wimpfac="<A href='/matrix/ips/reportes/r003-imp_factura.php?wfactura=".$wfue."-".$wfac."&amp;wemp_pmla=".$wemp_pmla."&amp;wbasedato=".$wbasedato."' TARGET='_blank'> ";
			 echo "<td class='fila1' colspan=5 align=center><b>".$wimpfac." Imprimir Factura</b></td>";
			 $wdetfac="<A href='/matrix/ips/reportes/imp_det_factura.php?wfue=".$wfue."&wfac=".$wfac."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'> ";
			 echo "<td class='fila1' colspan=10 align=center><b>".$wdetfac." Detalle de Cargos</b></td>";
			 echo "</tr>";
	        }
	  echo "</table>";
    }

  //===========================================================================================================================================
  function mov_factura($wffue, $wffac)
    {

	 global $wbasedato;
	 global $wemp_pmla;
	 global $conex;
	 global $wcf;
	 global $wcf2;
	 global $wclfa;
	 global $wclfg;


	 $q = " SELECT renfue, rennum, renfec, rencco, renvca, renobs "
         ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000021 "
         ."  WHERE rdeffa like '".$wffue."'"
         ."    AND rdefac like '".$wffac."'"
         ."    AND rdefue = renfue "
         ."    AND rdenum = rennum "
         ."    AND rdecco = rencco "
         ."    AND rdeest = 'on' "
         ."    AND rdeest = renest "
         ."  ORDER BY 3 desc,1,2 ";
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $num = mysql_num_rows($res);

	     if ($num > 0)
	        {
		     echo "<br>";
	         echo "<center><table border='0'>";
	         echo "<tr><td align=center colspan=13 class='titulo'><b>M O V I M I E N T O S &nbsp; A N E X O S &nbsp; A &nbsp; L A &nbsp; F A C T U R A</b></td></tr>";
	         echo "<tr class='encabezadoTabla'>";
	         echo "<th colspan=2>FUENTE</th>";
	         echo "<th colspan=1>NRO DTO</th>";
	         echo "<th colspan=1>FECHA</th>";
	         echo "<th colspan=1>VALOR</th>";
	         echo "<th colspan=1>CAUSA / OBSERVACION</th>";
	         echo "<th colspan=1>&nbsp</th>";
	         echo "</tr>";

		     for ($i=1;$i<=$num;$i++)
		         {
		         if ($i%2==0)
			       $wclass="fila1";
			     else
			       $wclass="fila2";

			      $row = mysql_fetch_array($res);

				  $wdocfue       = $row[0];
			      $wdocnum       = $row[1];
			      $wdocfec       = $row[2];
			      $wdoccco       = $row[3];
			      $wdocval       = $row[4];
			      $wdocobs       = $row[5];

			      echo "<tr>";
				  echo "<td align=center class='".$wclass."' colspan=1>".$wdocfue."</font></td>";
				  $q = " SELECT cardes "
				      ."   FROM ".$wbasedato."_000040 "
				      ."  WHERE carfue = '".$wdocfue."'";
				  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row1 = mysql_fetch_array($res1);

				  echo "<td align=left class='".$wclass."' colspan=1>".$row1[0]."</td>";
				  echo "<td align=center class='".$wclass."' colspan=1>".$wdocnum."</td>";
				  echo "<td align=center class='".$wclass."' colspan=1>".$wdocfec."</td>";
				  echo "<td align=right class='".$wclass."' colspan=1>".number_format($wdocval,0,'.',',')."</td>";
				  echo "<td align=left class='".$wclass."' colspan=1>";

				  $q=" 	SELECT caucod, caunom
						  FROM ".$wbasedato."_000072, ".$wbasedato."_000071
						 WHERE cauest='on'
						   AND Caudev='on'
						   AND Caucod=Doccau
						   AND Docfue='".$wdocfue."'
						   AND Docnum='".$wdocnum."' ";
				  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $num1 = mysql_num_rows($res1);
				  if($num1>0)
				  {
					echo "Causa:";
					for ($j=1;$j<=$num1;$j++)
					{
						$row1 = mysql_fetch_array($res1);
						echo " - ".strtolower($row1['caunom']);
					}
				  }
				  if(isset($wdocobs) && $wdocobs!="")
					echo "<br>Observaci&oacute;n: ".$wdocobs;
				  echo "</td>";
				  echo "<td class='".$wclass."' align=center><A href='Imp_documento.php?wfuedoc=".$wdocfue."&wnrodoc=".$wdocnum."&wcco=".$wdoccco."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."' TARGET='_blank'>Ver</A></td>";
				  //echo "<td bgcolor='".$wclass."' align=center><font size=3><A href='Consultar_Factura.php?wffue  =".$row[0]."&amp;wffac=".$row[1]."&amp;wbasedato=".$wbasedato."&amp;$wver=on'> Ver</A></font></td>";
		          echo "</tr>";
			     }
		     echo "</table>";
	        }

	 }

 function pintarCausas($causas, $estadoFactura )
	{
		$selectCausas = "";
		$numCausas = count( $causas );
		if( $estadoFactura == "on" ){
			$selectCausas = "<table align='center' width='85%' border=1>";
			$selectCausas .= "<tr>";
			$selectCausas .= "<td class='encabezadoTabla' colspan=2 align='center'><b>CAUSAS  DE ANULACI&Oacute;N</b></td>";
			$selectCausas .= "</tr>";
			$selectCausas .= "<tr id='tr_slc'>";
			$selectCausas .= "<td class='botona' colspan='2' align='center'><select name='cauSel' id='cauSel' onchange='agregarCausa( this )'>";
			$selectCausas .= "<option value='--' selected>{$causas['--']}</option>";

			foreach ($causas as $key=>$dato)
			{
				if ($key !="--")
				{
					$selectCausas .= "<option value='$key'>".$causas[$key]."</option>";
				}
			}
				$selectCausas .= "</select></td></tr></table>";
		}else{ // si ya est� anulada se va a construir las filas para mostrar las causas
			
				$selectCausas = "<table align='center' width='85%' border=1>";
				$selectCausas .= "<tr>";
				$selectCausas .= "<td class='encabezadoTabla' colspan=2 align='center'><b>CAUSAS  DE ANULACI&Oacute;N</b></td>";
				$selectCausas .= "</tr>";
				$i = 0;
				foreach ($causas as $key=>$dato)
				{
					( is_int($i/2) ) ? $class= "fila1" : $class = "fila2";
					$i++;
					$selectCausas .= "<tr class='{$class}'><td>";
					if ($key !="--")
					{
						$selectCausas .= $key."&nbsp;&nbsp;".$causas[$key];
					}else{
						if( $numCausas==1 )
							$selectCausas .= $causas[$key];
					}
					$selectCausas .= "</td></tr>";
				}
					$selectCausas .= "</table>";	
		}
		return( $selectCausas);
	}
 function consultarCausas( $west, $wfuente="", $wnumDoc="" )
	{
	 	global $conex;
	 	global $wbasedato;
	 	global $wemp_pmla;

	 	if( $west == "on" ){
		 	$q="select caucod, caunom from ".$wbasedato."_000072 where cauest='on' and cauafa='on' order by caunom ";
		 	$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 	$num1 = mysql_num_rows($res1);

		 	while( $row = mysql_fetch_array( $res1 ) ){
		 		$causas[$row['caucod']] = $row['caunom'];
		 	}

		 	$causas['--'] = " Seleccione ";
	    }else{

	    	$q = " SELECT Doccau
	    			 FROM ".$wbasedato."_000071
	    		    WHERE Docfue = '{$wfuente}'
	    		      AND Docnum = '{$wnumDoc}'
	    		      AND Docest = 'on'";
		 	$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 	$num1 = mysql_num_rows($res1);

		 	$aux = array();

		 	while( $row = mysql_fetch_array( $res1 ) ){
		 		array_push($aux, "'".$row[0]."'" );
		 	}
		 	if( count($aux) >0 ){
			 	$aux = implode( ",", $aux );

			 	$q="select caucod, caunom from ".$wbasedato."_000072 where caucod IN (".$aux.") order by caunom ";
			 	$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 	$num1 = mysql_num_rows($res1);
			 	while( $row = mysql_fetch_array( $res1 ) ){
			 		$causas[$row['caucod']] = $row['caunom'];
			 	}
			 }
		 	$causas['--'] = " Sin Especificar ";
	    }
		return( $causas );
	}
  //===========================================================================================================================================
  //INICIO DEL PROGRAMA
  //===========================================================================================================================================

  global $wconfirma_anular;

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' id='wbasedato' value='".$wbasedato."'>";

 /* if (isset($wanular) and $wanular=="on")
     {
     	anular_factura($wffue,$wffac);
     }*/

  if (isset($wffue) and $wffue != "" and isset($wffac) and $wffac !="")
     {
      ver_factura($wffue,$wffac);
      mov_factura($wffue,$wffac);
     }
    else
	  //if (((!isset($wfue) and !isset($wfac)) or (!isset($whis)) or (!isset($wing) and !isset($whis))) and !isset($wval))
	  if ((!isset($wfue) and !isset($wfac)) or ((!isset($whis) or !isset($wing) and !isset($whis) and !isset($wval)) or (!isset($wdoc) or !isset($wnom) and !isset($wdoc) and !isset($wnom))))
	     {
	      echo "<br>";
	      echo "<div align='center'><fieldset id='' style='padding:15px;width:800px'>";
	      echo "<legend class='fieldset'>Par&aacute;metros de busqueda</legend>";
	      echo "<div>";
		  echo "<table border='0' style='width:60%;'>";
		  echo "<tr>";
			  echo "<td align=left width='10%' class='fila1' colspan=1><b> Fuente:</b></td><td width='40%' class='fila2'><INPUT TYPE='text' NAME='wfue'></td>";
			  echo "<td align=left width='10%' class='fila1' colspan=1><b> Factura:</b></td><td width='40%' class='fila2'><INPUT TYPE='text' NAME='wfac'></td>";
		  echo "</tr>";
		  echo "<tr>";
			  echo "<td align=left width='10%' class='fila1' colspan=1><b> Historia:</b></td><td width='40%' class='fila2'><INPUT TYPE='text' NAME='whis'></td>";
			  echo "<td align=left width='10%' class='fila1' colspan=1><b> Ingreso:</b></td><td width='40%' class='fila2'><INPUT TYPE='text' NAME='wing'></td>";
		  echo "</tr>";
		   echo "<tr>";
		  	echo "<td align=left class='fila1' colspan='2'><b> Documento de Identificaci�n: </b></td><td colspan='2' class='fila2'> <INPUT TYPE='text' NAME='wdoc' SIZE=34 ></td>";
		  echo "</tr>";
		  echo "<tr>";
		  	echo "<td align=left class='fila1' colspan='4'><b> Nombre del paciente o persona responsable (comodin=%): &nbsp; </b><INPUT TYPE='text' NAME='wnom' SIZE=71></td>";
		  echo "</tr>";
		  echo "</table></br>";

		 /* echo "<table border='0'><tr>";
		  echo "<td align=left class='fila1'><b> Nombre del paciente o persona responsable (comodin=%): &nbsp; </b><INPUT TYPE='text' NAME='wnom' SIZE=71></td>";
		  echo "<td align=left class='fila1'><b> Documento de Identificaci�n: </b>&nbsp; <INPUT TYPE='text' NAME='wdoc' SIZE=34 ></td>";
		  echo "</tr></table></td>";*/
		  echo "</div>";
		  echo "</fieldset></div>";
		 }
	    else
	       {
		    if ($wfue!="" or $wfac!="" or $whis!="" or $wnom!="" or $wdoc!="")
		       {

			     if (!isset($wfue) or $wfue == "")
			        $wfue="%";
			     if (!isset($wfac) or $wfac == "")
			        $wfac="%";
			     if (!isset($whis) or $whis == "")
			        {
			         $whis_i=0;
			         $whis_f=9999999999;
		            }
		           else
		              {
			           $whis_i=$whis;
			           $whis_f=$whis;
		              }
			     if (!isset($wing) or $wing == "")
			        {
			         $wing_i=0;
			         $wing_f=999;
		            }
			       else
		              {
				       $wing_i=$wing;
				       $wing_f=$wing;
			          }

			     if (!isset($wnom) or $wnom == "")
			        $wnom="%";
			     if (!isset($wdoc) or $wdoc == "")
			        $wdoc="%";

				// Si al pasar las variables por URL tomaron otro valor aca les asigno el valor original
			    if(isset($wfac))
					$wfac = desconvertir_url($wfac);
				if(isset($wnom))
					$wnom = desconvertir_url($wnom);
				if(isset($wfue))
					$wfue = desconvertir_url($wfue);
				if(isset($wdoc))
					$wdoc = desconvertir_url($wdoc);
				if(isset($whis))
					$whis = desconvertir_url($whis);
				if(isset($wing))
					$wing = desconvertir_url($wing);

				/*
			     $q = " SELECT fenffa, fenfac, fenfec, fencod, fennit, empnom, fenval, fensal, fenesf, "
			         ."        fenhis, fening, fenest, pacap1, pacap2, pacno1, pacno2, fennpa, fendpa "
			         ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000024, ".$wbasedato."_000100 "
			     */

			     $q = " SELECT fenffa, fenfac, fenfec, fencod, fennit, empnom, fenval, fensal, fenesf, "
			         ."        fenhis, fening, fenest, fennpa, fendpa "
			         ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000024 " //, ".$wbasedato."_000100 "
			         ."  WHERE fenffa like '".$wfue."'"
			         ."    AND fenfac like '".$wfac."'"
			         ."    AND fenhis between ".$whis_i." AND ".$whis_f
			         ."    AND fening between ".$wing_i." AND ".$wing_f
			         ."    AND fencod = empcod "
			         ///."    AND fenhis = pachis "
			         ."    AND fendpa like '".$wdoc."'"
			         ."    AND fennpa like '".$wnom."'"
			         ."    GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14 "
			         ///."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18 ";
					 ."    ORDER BY fenfec desc,fenffa,fenfac";				//Se actualiza el reporte para que se muestre en orden de fecha,
			     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());								//fuente y numero de factura
			     $num = mysql_num_rows($res);

			     if ($num > 0)
			        {
				     echo "<br>";
			         echo "<center><table border='0'>";

			         echo "<tr><td colspan=13 class='fila2'><b>Cantidad de Facturas encontradas: ".$num."</b></td></tr>";

				     echo "<tr class='encabezadoTabla'>";
			         echo "<th>&nbsp</th>";
				     echo "<th>FUENTE</th>";
				     echo "<th>FACTURA</th>";
				     echo "<th>FECHA</th>";
				     echo "<th>HISTORIA</th>";
				     echo "<th>DOCUMENTO</th>";
				     echo "<th>PACIENTE</th>";
				     echo "<th>RESPONSABLE</th>";
				     echo "<th>VALOR</th>";
				     echo "<th>SALDO</th>";
				     echo "<th>ESTADO CARTERA</th>";
				     echo "<th>ESTADO FACTURA</th>";
				     echo "<th>&nbsp</th>";
				     echo "</tr>";

				     $wver="";

				     for ($i=1;$i<=$num;$i++)
				        {
					     $row = mysql_fetch_array($res);

					     if ($i%2==0)
			                $wclass="fila1";
			               else
			                  $wclass="fila2";

						 $westcar = '';
			             //Le coloco nombre a los estados de la factura en cartera
			             switch ($row[8])
			                {
					           case "GE":
					               { $westcar="GENERADA"; }
					               break;
					           case "EV":
					               { $westcar="ENVIADA"; }
					               break;
					           case "RD":
					               { $westcar="RADICADA"; }
					               break;
					           case "DV":
					               { $westcar="DEVUELTA"; }
					               break;
					           case "GL":
					               { $westcar="GLOSADA"; }
				            }


			             //Le coloco nombre a los estados de la factura en el archivo
			             switch ($row[11])
			                {
				             case "on":
				               { $westreg="ACTIVA*"; }
				               BREAK;
				             case "off":
				               { $westreg="ANULADA"; }
				               BREAK;
				            }
							
						  /*
							Query para poner nombre completo paciente
						  */
						  $qnombrepaciente = "SELECT Clinom FROM ".$wbasedato."_000041 WHERE Clidoc = '".$row['Fendpa']."' ";
						  $resqnom = mysql_query($qnombrepaciente,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qnombrepaciente." - ".mysql_error());
						  $numqnom = mysql_num_rows($resqnom);
						  $rowqnom = mysql_fetch_array($resqnom);

					     echo "<tr>";
					     echo "<td class='".$wclass."' align=center><A href='Consultar_Factura.php?wffue=".$row[0]."&wffac=".$row[1]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&$wver=on&wfue=".convertir_url($wfue)."&wfac=".convertir_url($wfac)."&whis=".convertir_url($whis)."&wnom=".convertir_url($wnom)."&wdoc=".convertir_url($wdoc)."'> Ver</A></td>";
					     echo "<td class='".$wclass."'>".$row[0]."</td>";                 						//Fuente Factura
					     echo "<td class='".$wclass."'>".$row[1]."</td>";                 						//Numero Factura
					     echo "<td class='".$wclass."'>".$row[2]."</td>";                 						//Fecha
					     echo "<td class='".$wclass."'>".$row[9]."/".$row[10]."</td>";    						//Historia
					     //echo "<td class='".$wclass."'>".$row[17]."</td>";                						//Documento Responsable Factura
					     //echo "<td class='".$wclass."'>".$row[14]." ".$row[15]." ".$row[12]." ".$row[13]."</td>"; //Nombre Paciente
					     echo "<td class='".$wclass."'>".$row[13]."</td>";                						//Documento Responsable Factura
					     echo "<td class='".$wclass."'>".$rowqnom['clinom']."</td>";                                        //Nombre Paciente
					     echo "<td class='".$wclass."'>".$row[4]." ".$row[5]."</td>";								//Empresa Responsable
					     echo "<td class='".$wclass."' align=right>".number_format($row[6],0,'.',',')."</td>";	//Valor Neto Factura
					     echo "<td class='".$wclass."' align=right>".number_format($row[7],0,'.',',')."</td>"; 	//Saldo Factura
					     echo "<td class='".$wclass."' align=center><b>".$westcar."</b></td>";											//Estado Factura en Cartera
					     echo "<td class='".$wclass."' align=center><b>".$westreg."</b></td>";										//Estado Factura
					     echo "<td class='".$wclass."' align=center><A href='Consultar_Factura.php?wffue=".$row[0]."&wffac=".$row[1]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&$wver=on&wfue=".convertir_url($wfue)."&wfac=".convertir_url($wfac)."&whis=".convertir_url($whis)."&wnom=".convertir_url($wnom)."&wdoc=".convertir_url($wdoc)."'> Ver</A></td>";
					     echo "</tr>";
				        }
					}
			   }
		   }


  echo "<div align='center'>";
  if(isset($wffue) and isset($wffac))
  	echo "<input type='button' value='Retornar' onclick='retornar(\"Consultar_Factura.php?wemp_pmla=".$wemp_pmla."&wfue=".$wfue."&wfac=".$wfac."&whis=".$whis."&wnom=".$wnom."&wdoc=".$wdoc."\")'>";
  elseif(isset($wfue) or isset($wfac) or isset($whis) or isset($wnom) or isset($wdoc))
  	echo "<input type='button' value='Retornar' onclick='retornar(\"Consultar_Factura.php?wemp_pmla=".$wemp_pmla."\" )'>";
  else
    echo "<input type='button' value='Buscar' onclick='enter();'></div>";
//Mensaje de espera para generacion
echo "<div id='msjEspere' style='display:none;'>";
echo "<br /><br />
	<img width='13' height='13' src='../../images/medical/ajax-loader7.gif' />&nbsp;<font style='font-weight:bold; color:#2A5DB0; font-size:13pt' >Anulando</font>
	<br /><br /><br />";
echo "</div>";

//Mensaje de alertas
	echo "<div id='msjAlerta' style='display:none;'>";
	echo '<br>';
	echo "<img src='../../images/medical/root/Advertencia.png'/>";
	echo "<br><br><div id='textoAlerta'></div><br><br>";
	echo '</div>';

  //echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
}
?>
</body>
</html>
