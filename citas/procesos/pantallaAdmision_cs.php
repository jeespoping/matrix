<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<?php
if (isset($accion) and $accion == 'actualizar')  //atendido cambia el campo asistida y atendido
 { 
    $horaAten=date("H:i:s");

	if ($caso == 3 or $caso == 1)
	{
		$sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Hora_aten = '".$horaAten."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}
	else if ($caso == 2 and $valCitas != "on")    //el asiste de clisur utiliza la funcion vieja
	{
		$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Hora_aten = '".$horaAten."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	return;
}
else if (isset($accion) and $accion == 'cancelar')  //cancelar
  {

	if ($caso == 3 or $caso == 1)
	{
		 $sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Activo = '".$est."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";
	}
	else if ($caso = 2)
	{

		$sql = "UPDATE
				".$wemp_pmla."_000009
			SET
				Activo = '".$est."',
				Causa = '".$causa."'

			WHERE
				id = '$id'";
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return;
}
else if (isset($accion) and $accion == 'actualizar1')  //no asiste
 { 

	if ($caso == 3 or $caso == 1)
	{
		$sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}
	else if ($caso == 2 and $valCitas != "on")
	{
		$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}
	else if ($caso == 2 and $valCitas == "on")
	{
		$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."',
						Causa = '".$causa."'
					WHERE
						id = '$id'";
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return;
}
else if (isset($accion) and $accion == 'demora')  //atendido cambia el campo asistida y atendido cuando han pasado 15 minutos de la cita se pide causa
 { 	



    $horaAten=date("H:i:s");

	if ($caso == 3 or $caso == 1)
	{
		$sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Hora_aten = '".$horaAten."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}
	else if ($caso == 2 and $valCitas != "on")    //el asiste de clisur utiliza la funcion vieja
	{
		$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."',
						Atendido = '".$est."',
						Hora_aten = '".$horaAten."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";   //agregar el campo atendida
	}

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	return;
}
?>

<html>
<head>
<title>Agenda Medica</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<link   rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
<link   rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
<style type="text/css">
	.ui-multiselect { background:white; background-color:white; color: black; font-weight: normal; font-family: verdana; border-color: gray; border: 3px; height:20px; width:450px; overflow-x:hidden;text-align:left;font-size: 10pt;}

	.ui-multiselect-menu { background:white; background-color:white; color: black; font-weight: normal; font-size: 10pt;height: 450px;}

	.ui-multiselect-header { background:white; background-color:lightgray; color: black;font-weight: normal;}


    .ui-multiselect-checkboxes {
        max-height: 400px;
     }

</style>
<script>

	$(document).ready( function (){
		
		$('#slDoctor').multiselect({
                     numberDisplayed: 1,
                     selectedList:1,
                     height: "auto",
                     multiple:false
        }).multiselectfilter();

    });    
	idRadio = '';
	accion = '';
	est = '';
	func = '';
	campoCancela = '';

	function abrirVentana( adicion, citas, solucion, wdoc, mostrarCausa, wtdo ){

		// alert("entro abrirVentana " +mostrarCausa);
		var auxDiv = document.createElement( "div" );
		auxDiv.innerHTML = "<INPUT type='hidden' name='admision' value='"+adicion+"'>";
		document.forms[0].appendChild( auxDiv.firstChild );
		/*se le adiciona al div causa_demora unos parametros para poder consultarlos despues desde
		otra funcion*/
		$('#causa_demora')[0].adicion = adicion;
		$('#causa_demora')[0].citas = citas;
		$('#causa_demora')[0].solucion = solucion;
		$('#causa_demora')[0].wdoc = wdoc;
		$('#causa_demora')[0].wtdo = wtdo;

		if (mostrarCausa == 'on')
		{
			$.blockUI({ message: $('#causa_demora') });

		}
		else
		{
			abrirVentanaAdmision(adicion, citas, solucion, wdoc, wtdo)
		}

	}

	function abrirVentanaAdmision(adicion, citas, solucion, wdoc, wtdo)
	{
		var abrirNuevaAdmision = $("#abrirNuevaAdmision").val();
		var ancho=screen.width;
		var alto=screen.availHeight;
		// var v = window.open( 'admision.php?ok=9&empresa='+solucion+'&idCita='+adicion+'&wemp2='+citas,'','scrollbars=1, width='+ancho+', height='+alto );  //original
		if(abrirNuevaAdmision != "on"){
			var v = window.open( '../../IPS/Procesos/admision.php?wdoc='+wdoc+'&wtdo='+wtdo+'&ok=1&empresa='+solucion+'&idCita='+adicion+'&wemp2='+citas,'','scrollbars=1, width='+ancho+', height='+alto ); //con cambios
		}else{
			var wemp_pmlaAux = $("#wemp_pmla").val();
			var v = window.open( '../../admisiones/procesos/admision_erp.php?wemp_pmla='+wemp_pmlaAux+'&TipoDocumentoPacAm='+wtdo+'&DocumentoPacAm='+wdoc+'&AgendaMedica=on&solucionCitas='+citas,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes' ); //con cambios
		}
		v.moveTo(0,0);
	}

	function abrirAdmisionNueva(){

		var wemp_pmlaAux = $("#wemp_pmla").val();
		var v = window.open( '../../admisiones/procesos/admision_erp.php?wemp_pmla='+wemp_pmlaAux+'&AgendaMedica=on','','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes' ); //con cambios
	}

	function asistida( adicion,mostrarCausa ){

		var auxDiv = document.createElement( "div" );
		auxDiv.innerHTML = "<INPUT type='hidden' name='asistida' value='"+adicion+"'>";
		//auxDiv.innerHTML += "<INPUT type='hidden' name='asistida' value=''>";
		if (mostrarCausa == 'on')
		{

			$.blockUI({ message: $('#causa_demora') });

			//Busco el select de causa para el div correspondiente
			// var contenedorCancela = document.getElementById( "causa_demora" );

			// campoCancela = document.getElementById( "causa_demora" ).getElementsByTagName( "select" );
			//auxDiv.innerHTML += "<INPUT type='hidden' name='cau_demora' value='"+campoCancela[0].value+"'>";
		}
		document.forms[0].appendChild( auxDiv.firstChild );

		//hacer todo antes del submit

	}



	function asiste(id_radio, id, mostrarCausa)
		{

			var valora = $('[name="'+id_radio+'"]:checked').val();

			if (valora!='on')
			{
				valora = 'off';
			}

			if (mostrarCausa == 'off')
			{
				$.post("pantallaAdmision_cs.php",
					{
						wemp_pmla:      $('#solucionCitas').val(),
						consultaAjax:   '',
						id:          	id,
						accion:         'actualizar',
						est: 			valora,
						caso:			$('#caso').val()
					}
					,function(data) {

						if(data.error == 1)
						{

						}
						else
						{
							document.location.reload(true);
						}
				});

			}
			else
			{
				$.blockUI({ message: $('#causa_demora') });

				idRadio = id;
				accion = 'demora';
				est = valora;
				func = respuestaAjaxDemora;

				//Busco el select de causa para el div correspondiente
				var contenedorCancela = document.getElementById( "causa_demora" );

				campoCancela = document.getElementById( "causa_demora" ).getElementsByTagName( "select" );
				//alert('on');
			}
		}

		function cancela(id_radio1, id)
		{


			var valorc = $('[name="'+id_radio1+'"]:checked').val();
			if (valorc!='I')
			{
				valorc = 'A';
			}

			mes_confirm = 'Confirma que desea cancelar la cita?';
			if(confirm(mes_confirm))
			{

				$.blockUI({ message: $('#causa_cancelacion') });

				idRadio = id;
				accion = 'cancelar';
				est = valorc;
				func = respuestaAjaxCancela;

				//Busco el select de causa para el div correspondiente
				var contenedorCancela = document.getElementById( "causa_cancelacion" );

				campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );



				// $.post("pantallaAdmision_cs.php",
					// {
						// wemp_pmla:      $('#solucionCitas').val(),
						// consultaAjax:   '',
						// id:          	id,
						// accion:         'cancelar',
						// est: 			valorc,
						// caso:			$('#caso').val()

					// }
					// ,function(data) {

						// if(data.error == 1)
						// {
							// alert("No se pudo realizar la cancelacion");
						// }
						// else
						// {
							// alert("Cita Cancelada"); // update Ok.
							// document.location.reload(true);
						// }
					// }
				// );
			}
			else
			{
				$("#"+id_radio1).removeAttr("checked");
				//return false;
			}
		}


		function no_asiste(id_radio2, id)
		{


			var valora = $('[name="'+id_radio2+'"]:checked').val();

			if (valora!='off')
			{
				valora = 'off';
			}

			mes_confirm = 'Confirma que desea marcar la cita como no asistida?';
			if(confirm(mes_confirm))
			{
				$.blockUI({ message: $('#causa_noasiste') });

					idRadio = id;
					accion = 'actualizar1';
					est = valora;
					func = respuestaAjaxNoAsiste;

						//Busco el select de causa para el div correspondiente
						var contenedorCancela = document.getElementById( "causa_noasiste" );

						campoCancela = document.getElementById( "causa_noasiste" ).getElementsByTagName( "select" );

					//alert(valora);
					// $.post("pantallaAdmision_cs.php",
						// {
							// wemp_pmla:      $('#solucionCitas').val(),
							// consultaAjax:   '',
							// id:          	id,
							// accion:         'actualizar1',
							// est: 			valora,
							// caso:			$('#caso').val(),
							// tipo:			$('#causa_noasiste').getElementsByTagName( "causa" ).value
						// }
						// ,function(data) {

							// if(data.error == 1)
							// {

							// }
							// else
							// {
								// document.location.reload(true);
							// }
					// });

			}
			else
			{
				$("#"+id_radio2).removeAttr("checked");
				//return false;
			}
		}


	function llamarAjax()
	{
		var radioadmision = $('[name="rdAdmision"]:checked').val();
		var radioasistida = $('[name="rdAsistida"]:checked').val();

		if(radioadmision == "on" || radioasistida == "on")
		{
			if (radioadmision == "on")
			{
				abrirVentanaAdmision($('#causa_demora')[0].adicion, $('#causa_demora')[0].citas, $('#causa_demora')[0].solucion, $('#causa_demora')[0].wdoc, $('#causa_demora')[0].wtdo);
			}

			//agregar el select al form porque cuando se hace el submit jquery lo saca del form
			document.forms[0].appendChild( document.getElementById( "causa_demora" ).getElementsByTagName( "select" )[0] );
			document.forms[0].submit();
		}
		else
		{
			//Busco el select de causa para el div correspondiente
			// var contenedorCancela = document.getElementById( "causa_cancelacion" );

			// var campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );

			//Asigno el valor seleccionado de la causa
			tipo = campoCancela[0].options[ campoCancela[0].selectedIndex ].text;

			if( idRadio != '' && accion != ''  && est != '' && func != '' && tipo !='' ){

				$.post("pantallaAdmision_cs.php",
						{
							wemp_pmla:      $('#solucionCitas').val(),
							consultaAjax:   '',
							id:          	idRadio,
							accion:         accion,
							est: 			est,
							caso:			$('#caso').val(),
							causa:			tipo
						}
						,func
					);
			}

			idRadio = '';
			accion = '';
			est = '';
			func = '';
			campoCancela = '';

			// var contenedorCancela = document.getElementById( "causa_cancelacion" );

			// var campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );;

			campoCancela.selectedIndex = 0;
		}

	}

		function respuestaAjaxNoAsiste(data) {

			if(data.error == 1)
			{

			}
			else
			{
				document.location.reload(true);
			}
		}

		function respuestaAjaxCancela(data){

			if(data.error == 1)
			{
				alert("No se pudo realizar la cancelacion");
			}
			else
			{
				alert("Cita Cancelada"); // update Ok.
				document.location.reload(true);
			}
		}

		function respuestaAjaxDemora(data){

			if(data.error == 1)
			{

			}
			else
			{
				document.location.reload(true);
			}
		}

	function imprimir(wemp_pmla,caso,wsw,solucionCitas,slDoctor,valCitas,wfec)
	{
		var v = window.open( 'impresionAgenda.php?wemp_pmla='+wemp_pmla+'&caso='+caso+'&wsw='+wsw+'&solucionCitas='+solucionCitas+'&slDoctor='+slDoctor+'&valCitas='+valCitas+'&wfec='+wfec,'','scrollbars=1', 'width=300', 'height=300' );
		// document.all.item("no_imprimir").style.display='none'
		// window.print()
		// document.all.item("no_imprimir").style.display=''
	}
</script>
</head>

<?php
/**
 * Programa:	pantallaAdmision_cs.php
 * Por:			Edwin Molina Grisales
 * Fecha:		2010-01-13
 * Descripcion:	Este programa muestra una lista de todos los pacientes con cita médica, con la
 * 				posibilidad de filtrar la lista por médico, y permitir hacerles la admision a
 * 				cada paciente con o sin cita.
 */

/**
 * Variables del sistema
 *
 * $slDoctor		Filtro por Doctor. Contiene el nombre del doctor por el que esta filtrado la lista
 * $idCita			Identificador unico de la cita que se le hace la admision
 * $filtro			Codigo del doctor por el que es filtrado el paciente
 */

 /*
 Modificaciones:
 2016-03-16 Se modifica el script para que abra la admisión nueva si existe el parámetro  abrirNuevaAdmision
 2013-07-03 Se modifica el script por un error en una variable quedo asi en caso = 2 and valCitas!=on
 2013-05-16 Se modifica el script con la variable fest que es la que valida si se asignan citas los dias festivos, si esta en on,
							  es porque si se asignan citas los festivos en esa unidad.
 2013-04-25: Se organiza el las citas por medicos que le faltaba un paramero de mostrar causa de demora en el ingreso. Viviana Rodas
 2013-03-04: Se le agrega el campo Hora_aten a la funcion marcarAsistida para que guarde la hora cuando le dan clic en asistida.
 2013-01-16: Se agrega la entidad responsable a la lista de citas Viviana Rodas
 2012-12-26: Se agregan los colores a las celdas de hora, medico, paciente Viviana Rodas
 2012-12-20: Se agregan las causas de demora en la atencion, solo las pide cuando pasaron 15 minutos despues de la cita, tambien que
			 imprima la agenda de cualquier dia. Viviana Rodas
 2012-12-19: Se envia en la url de recarga la variable wfec para que cuando recargue la pagina se quede en la fecha que tenia el usuario. Viviana Rodas
 2012-12-18: Se agrega a la tabla de la agenda la columna usuario para que se visualice el usuario que asigno la cita. Viviana Rodas
 2012-12-17: Se agrega la variable tipoAtencion para evaluar cuando las citas son de cardiologia muestre el campo tipo atencion que se ingresa cuando se
			  asigna la cita. Viviana Rodas
 2012-12-07: Se agrega la opcion de imprimir la agenda de los pacientes. Viviana Rodas
 2012-12-06: Se agrega color diferente de fondo en la celda de medico o en la celda de medico para diferenciarlos. Viviana Rodas
 2012-11-26: Se cambian las consultas que traen las citas de el dia actual para que traigan el campo asistida!=on tambien el campo asistida, ademas se
 consulta tambien que el campo causa se igual a ''. Viviana Rodas.
 2012-11-10: Se agregan las causas para que cuando se marque la cita como cancelada guarde la causa en la base de datos, tambien para el campo no asiste,
 para que guarde la causa de la no asistencia.
 2012-10-26: Se cambian los nombres de los encabezados de la tabla, ahora es ingreso a la atencion que guarda la hora exacta en la que el paciente,
 ingreso a ser atendido por el medico, cancela pone ese registro en la tabla de citas como inactivo, no asiste guarda en la base de datos el campo asistida en off.
 2012-10-19: Se modifica toda la agenda para que nuestre la agenda diaria de las citas de todas las unidades de la clinica, incluyendo la clinica del sur con sus citas de medicos y de equipos, esta agenda permite visualizar todos los medicos o equipos dependiendo del tipo de cita que sea, o tambien filtrar por uno deseado, tambien se puede navegar entre fechas para ver las citas de dias anteriores o de dias posteriores, solo en el dia actual se muestran las columnas asiste o atendido, cancela, no asiste y en el caso de la clinica del sur admision, la lista de pacientes se muestran  por orden de cita, ademas cuando se graba el campo asiste se guarda null para que cuando en la agenda se chequee asiste se le cambie el estado a on u off dependiendo. Viviana Rodas

 2012-10-30: Se crea un blockUI para ingresar las causas de cancelacion y no asistencia, las cuales se pueden seleccionar, cuando se chequea el radiobutton de cancelada o de no asiste. Viviana Rodas
 2012-10-31: Se agrega el link de Asignar cita en la parte superior de la tabla que lista los pacientes, tambien se agrego el campo causa a la consulta que trae los pacienes con cita en la clinica del sur, para que cuando se le de no asiste si lo borre de la lista.Viviana Rodas
 */

/********************************************************************************************************
 * 												FUNCIONES
 *******************************************************************************************************/

function marcarAsistida( $id, $causa ){

	global $conex;
	global $solucionCitas;
	$horaAten=date("H:i:s");

	if( isset($id) ){

		if( !empty($id) ){

			 $sql = "UPDATE
						{$solucionCitas}_000009
					SET
						asistida = 'on',
						atendido = 'on',
						Hora_aten = '".$horaAten."',
						Causa = '".$causa."'
					WHERE
						id = '$id'";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

			if( mysql_affected_rows() > 0 ){
				return true;
			}
			else{
				return false;
			}

		}

	}
}


function guardarCausaAdmision( $id, $causa ){

	global $conex;
	global $solucionCitas;
	$horaAten=date("H:i:s");

	if( isset($id) ){

		if( !empty($id) ){

			 $sql = "UPDATE
						{$solucionCitas}_000009
					SET
						Hora_aten = '".$horaAten."',
						Causa = '".$causa."'

					WHERE
						id = '$id'";

			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

			if( mysql_affected_rows() > 0 ){
				return true;
			}
			else{
				return false;
			}

		}

	}
}


function causas($tipo)
	{

				global $conex;

				$sql5="select Caucod, Caudes, Cautip, Cauest  from root_000086 where Cautip = '".$tipo."' and Cauest ='on' group by Caudes";
				$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );

				echo "<table>";
				echo "<tr class='encabezadotabla'  align=center>";
				echo "<td width='100%' colspan='2'>Seleccione la causa:</td>";
				echo "</tr> ";

				echo "<tr>";
				echo "<td align=center><select name='causa' onchange='javascript: llamarAjax();'>";
				echo "<option></option>";

				for( $i = 0; $rows5 = mysql_fetch_array( $res5 ); $i++ )
				{

					if( $causa != trim( $rows5['Caucod'] )." - ".trim( $rows5['Caudes'] ) )
					{
						echo "<option>".$rows5['Caucod']." - ".$rows5['Caudes']."</option>";
					}
					else
					{
						echo "<option selected>".$rows5['Caucod']." - ".$rows5['Caudes']."</option>";
					}



				}
				echo "</select>";
				echo "</td></tr>";
				echo "</table>";

				//return;
	}

function consultarAplicacion( $conex, $wemp_pmla, $aplicacion ){

	$query = " SELECT Detval
				 FROM root_000051
				WHERE detemp = '$wemp_pmla'
				  AND detapl = '$aplicacion'";
	$rs    = mysql_query( $query, $conex );
	$row   = mysql_fetch_array( $rs );
	return( $row['Detval'] );
}


/********************************************************************************************************
 * 											FIN DE FUNCIONES
 *******************************************************************************************************/

/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/
echo "<body>";

include_once("root/comun.php");


if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}



echo "<input type='HIDDEN' name= 'caso' id= 'caso' value='".$caso."'>";
echo "<input type='HIDDEN' name= 'wsw' id= 'wsw' value='".@$wsw."'>";
echo "<input type='HIDDEN' name= 'solucionCitas' id= 'solucionCitas' value='".$solucionCitas."'>";
echo "<input type='HIDDEN' name= 'valCitas' id= 'valCitas' value='".@$valCitas."'>";
echo "<input type='HIDDEN' name= 'wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";

//El usuario se encuentra registrado
// - - - - - - - - - - - - - - - - - - - - - Modificaciones - - - - - - - - - - - - - - - -
// 
// 2020-01-08  Arleyda Insignares C. Se modifica select que lista los médicos, a un multiselect de selección única para la utilización del filtro (busqueda por texto).

// session_start();
if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
}
else
{
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower( $institucion->baseDeDatos );
$wentidad = $institucion->nombre;

$abrirNuevaAdmision = consultarAplicacion( $conex, $wemp_pmla, "abrirNuevaAdmision" );
echo "<input type='HIDDEN' name= 'abrirNuevaAdmision' id= 'abrirNuevaAdmision' value='".$abrirNuevaAdmision."'>";


	if ($wemp_pmla == 01)
	{
		encabezado("AGENDA MEDICA", "2016-03-16", $wbasedato );
	}
	else
	{
		encabezado("AGENDA MEDICA", "2016-03-16", "logo_".$wbasedato );
	}

	if (!isset($wfec))
	{
		$wfec = date("Y-m-d");
	}
	$horaActSec=time();
	$horaAct=date("H:i", $horaActSec );

	if (!isset($valCitas))
	{
		$valCitas = "off";
	}

	if ($caso ==1 and $solucionCitas=='citasca')
	{
		$tipoAtencion='on';
	}
	else
	{
		$tipoAtencion='off';
	}

	if( !isset( $ret ) ){
		$ret = 'off';
	}


	if( isset($asistida) ){
		marcarAsistida( $asistida, $causa );
	}

	if( isset($admision) )
	{
		guardarCausaAdmision( $admision, $causa );
	}

	//Buscando el doctor por el que fue filtrado
	if( !isset( $slDoctor ) ){
		$nmFiltro = "% - Todos";
		$filtro = '%';
		$slDoctor = "% - Todos";
	}
	else{
		$nmFiltro = $slDoctor;
		$exp = explode( " - ", $slDoctor);
		$filtro = $exp[0];
	}

	if (!isset($fest))
	{
		$fest = "off";
	}

	echo "<form name='pantalla' method=post>";
	echo "<br><br>";


	if ($caso == 2 and $valCitas=="on")
	{
		$sql = "SELECT
			Mednom, Medcod
		FROM
			{$wbasedato}_000051
		WHERE
			Medcid != ''
			AND Medest = 'on'
		ORDER BY Mednom";
	}
	else if ($caso == 3 or $caso == 1)
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000003 where activo='A' ";
	}

	else if ($caso == 2 and $valCitas!="on")
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000010 where activo='A' group by descripcion order by descripcion";
	}


	$res1 = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );


	//Filtro por doctor o equipo
	echo "<table align=center>";
	echo "	<tr>";
	if ($caso == 2)
	{
		echo "	<td class='encabezadotabla' align=center>Filtro por Profesional</td>";
	}
	else
	{
		echo "	<td class='encabezadotabla' align=center>Filtro por Equipo</td>";
	}
	echo "	</tr>";
	echo "	<tr>";
	echo "  <td class='fila1' style='width: 600px;'><select id='slDoctor' name='slDoctor' multiple='multiple' style='width: 603px;height: 400px;' onchange='javascript: document.forms[0].submit();'>";
	// -> 2020-01-08   Se modifica select a multiselect para la utilización del filtro por texto clave
	//echo "	<td class='fila1'><select name='slDoctor' onchange='javascript: document.forms[0].submit();'>";
	echo "	<option>% - Todos</option>";

	for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ ){

		if ($caso == 2 and $valCitas=="on")
		{

			$rows['Medcod'] = trim( $rows['Medcod'] );
			$rows['Mednom'] = trim( $rows['Mednom'] );

			if( $slDoctor != trim( $rows['Medcod'] )." - ".trim( $rows['Mednom'] ) )
			{
				echo "<option>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
		}
		else if ($caso == 1 or $caso == 3)
		{
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );

			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			{
				echo "<option>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		else if ($caso == 2 and $valCitas!= "on")
		{

			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );

			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )

			{
				echo "<option>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}

	}//for

	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";


	$dia=date("l", strtotime( $wfec ) );
	$diaNum=date("d",strtotime( $wfec ));
	$mes=date("F",strtotime( $wfec ));
	$anio=date("Y",strtotime( $wfec ));

	// Obtenemos y traducimos el nombre del día
	if ($dia=="Monday") $dia="Lunes";
	if ($dia=="Tuesday") $dia="Martes";
	if ($dia=="Wednesday") $dia="Miércoles";
	if ($dia=="Thursday") $dia="Jueves";
	if ($dia=="Friday") $dia="Viernes";
	if ($dia=="Saturday") $dia="Sabado";
	if ($dia=="Sunday") $dia="Domingo";

	// Obtenemos y traducimos el nombre del mes
	if ($mes=="January") $mes="Enero";
	if ($mes=="February") $mes="Febrero";
	if ($mes=="March") $mes="Marzo";
	if ($mes=="April") $mes="Abril";
	if ($mes=="May") $mes="Mayo";
	if ($mes=="June") $mes="Junio";
	if ($mes=="July") $mes="Julio";
	if ($mes=="August") $mes="Agosto";
	if ($mes=="September") $mes="Septiembre";
	if ($mes=="October") $mes="Octubre";
	if ($mes=="November") $mes="Noviembre";
	if ($mes=="December") $mes="Diciembre";


	//tabla para navegar en las fechas de las citas
	$wfecAnt = date( "Y-m-d", strtotime($wfec) - 24*3600 );
	$wfecSig = date( "Y-m-d", strtotime($wfec) + 24*3600 );

	echo "<br>";
	echo "<table border='0' align='center'>";
	echo "<th class='encabezadotabla' colspan='3'>Seleccione la fecha:</th>";
	echo "</table>";
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td><a href='../../citas/procesos/pantallaAdmision_cs.php?solucionCitas=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&caso=".$caso."&wsw=".@$wsw."&wfec=".$wfecAnt."&valCitas=".@$valCitas."&fest=".$fest."' title='Atras'><img src='../../images/medical/citas/atras.jpg' alt='Atras'  height='30' width='30' border=0/></a></td>";
	if ($wfec == date("Y-m-d"))
	{
		$hoy = "Hoy:";
	}
	else
	{
		$hoy ="";
	}
	echo "<td class='fila1' ><font size='4'><b>".$hoy."</b> ".$dia." ".$diaNum." de ".$mes ." de ".$anio."</font></td>";
	echo "<td><a href='../../citas/procesos/pantallaAdmision_cs.php?solucionCitas=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&caso=".$caso."&wsw=".@$wsw."&wfec=".$wfecSig."&valCitas=".@$valCitas."&fest=".$fest."' title='Adelante'><img src='../../images/medical/citas/adelante.jpg' alt='Adelante'  height='30' width='30' border=0/></a></td>";
	echo "</tr>";
	echo "</table>";

	//fin tabla para navegar en las fechas de las citas

	echo "<br><br>";
	echo "<center><a href='../../citas/procesos/calendar.php?empresa=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&caso=".$caso."&wsw=".@$wsw."&fest=".$fest."&consultaAjax=' target='_blank'>Asignar cita</a></center>";
	if ($caso == 2 and $valCitas == "on")
	{
		if($abrirNuevaAdmision != "on" ){
			echo "<br><br>";
			echo "<center><a href='../../IPS/Procesos/admision.php?ok=9&empresa=$wbasedato&wemp2=citascs' target='_blank'>Admision sin cita</a></center>";
		}else{
			echo "<br><br>";
			echo "<center><input type='button' value='Admision sin cita' onclick='abrirAdmisionNueva();'></center>";
		}
    }



	//Aqui comienza la lista de pacientes

	//Buscando los pacientes que tienen cita
	//y no van para interconsulta
	if ($caso == 2 and $valCitas =="on")
	{
	  $sql = "SELECT
				fecha,
				cod_equ,
				TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,
				hf,
				nom_pac,
				mednom,
				b.id,
				b.tipoA,
				b.tipoS,
				b.cedula,
				b.usuario,
				b.Nit_res,

				IF(cedula IN ((SELECT pacdoc FROM {$wbasedato}_000100 WHERE pacdoc = cedula AND pacact = 'on' )),'on','off') as act
			FROM
				{$wbasedato}_000051 a,
				{$solucionCitas}_000009 b

			WHERE
				medcid = cod_equ
				AND medcod like '$filtro'
				AND fecha = '".$wfec."'
				AND atendido != 'on'
				AND asistida != 'on'
				AND Causa = ''
				AND nom_pac != 'CANCELADA'
				AND activo = 'A'
				AND cedula NOT IN (SELECT espdoc FROM {$wbasedato}_000141 WHERE espdoc = cedula AND esphor = TIME_FORMAT( CONCAT(hi,'00'), '%H:%i:%s') AND espmed = medcod )
			ORDER BY hi, mednom, nom_pac
			";

	} //AND asistida != ''  falta atendido
	else if ($caso == 3 or $caso == 1)
	{
		$sql = "select cod_med,cod_equ,cod_exa,fecha,TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,tipoA,cedula,Asistida,id from ".$solucionCitas."_000001 where fecha='".$wfec."' and cod_equ like '".$filtro."' and Atendido != 'on' and Asistida != 'on' and Activo='A' and Causa='' order by hi, cod_equ";
		//and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."'  validar el equipo  like '$filtro'

	}
	else if ($caso == 2 and $valCitas != "on") // error se tenia solucionCitas en vez de valCitas
	{
		$sql = "select cod_equ,cod_exa,fecha,TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,hf,nom_pac,nit_res,telefono,edad,comentario,usuario,activo,Asistida,id,cedula from ".$solucionCitas."_000009 where fecha='".$wfec."' and cod_equ like '".$filtro."' and Atendido != 'on' and Asistida != 'on' and Activo='A' and Causa='' order by hi, cod_equ";
		//and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."'   validar medico
	}
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	echo "<br><br>";
	echo "<table align='center'>";
	$color[0]="#99FFFF"; //
	$color[1]="#CC9999";//
	$color[2]="#00CC99";//
	$color[3]="#CCFF99";//
	$color[4]="#CCCCFF";//
	$color[5]="#EAADEA";//
	$color[6]="#00CCCC";//
	$color[7]="#E6E8FA";//
	$color[8]="#999966";
	$color[9]="#FF9900";
	$color[10]="#FFFF33";
	$color[11]="#0099FF";
	$color[12]="#00FF99";
	$color[13]="#CC99CC";
	$color[14]="#CCCCCC";

	if( $num > 0 )
	{

		for( $i = 0, $k = 0; $rows = mysql_fetch_array( $res ); $i++ )
		{

			if( !isset( $array_colors[ $rows['cod_equ'] ] ) )
			{
				$array_colors[ $rows['cod_equ'] ] = $color[$k];
				$k++;
			}

			$color_fondo = $array_colors[ $rows['cod_equ'] ];

			$j=$i+1;
			//Definiendo la clase por cada fila
			if( $i%2 == 0 )
			{
				$class = "class='fila1'";
			}
			else
			{
				$class = "class='fila2'";
			}

			//para mostrar el nombre del equipo, del medico y del examen
			if ($caso == 3 or $caso == 1)
			{
				$sql1 = "select Codigo,Descripcion from ".$solucionCitas."_000003 where Codigo = '{$rows['cod_equ']}' and activo = 'A'";
				$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
				$rows1 = mysql_fetch_array( $res1 );


				$sql2 ="select Codigo, Nombre from ".$solucionCitas."_000008 where Codigo = '{$rows['cod_med']}' and activo = 'A'";
				$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
				$rows2 = mysql_fetch_array( $res2 );


				$sql3 ="select Codigo, Descripcion from ".$solucionCitas."_000006 where Codigo = '{$rows['cod_exa']}' and Cod_equipo = '{$rows['cod_equ']}' and activo = 'A'";
				$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
				$rows3 = mysql_fetch_array( $res3 );

				$sql4 ="select descripcion,nit from ".$solucionCitas."_000002 where nit = '{$rows['nit_resp']}' and activo = 'A'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				$rows4 = mysql_fetch_array( $res4 );


			}

			if ($caso == 2 and $valCitas != "on")
			{
				$sql4="select Codigo, Descripcion  from ".$solucionCitas."_000010 where codigo='{$rows['cod_equ']}' and activo = 'A' group by Codigo, Descripcion";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				$rows4 = mysql_fetch_array( $res4 );

				$sql5="select nit, Descripcion  from ".$solucionCitas."_000002 where nit='{$rows['nit_res']}' and activo = 'A'";
				$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
				$rows5 = mysql_fetch_array( $res5 );
			}

			if ($caso == 2 and $valCitas == "on")
			{
				$sql5="select nit, Descripcion  from ".$solucionCitas."_000002 where nit='{$rows['Nit_res']}' and activo = 'A'";
				$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
				$rows5 = mysql_fetch_array( $res5 );

				$sql6="SELECT Selcod, Seldes
				FROM {$wbasedato}_000100, {$wbasedato}_000105
				WHERE Pacdoc = '".$rows['cedula']."'
				AND Pactdo = Selcod
				AND Seltip = '01'";
				$res6 = mysql_query( $sql6, $conex ) or die( mysql_errno()." - Error en el query $sql6 - ".mysql_error() );
				$rows6 = mysql_fetch_array( $res6 );
				$tipodoc="";
				$tipodoc= $rows6['Selcod']."-".$rows6['Seldes'];

				$sql7="SELECT pachis
					FROM {$wbasedato}_000100
					WHERE '".$rows['cedula']."'= pacdoc";
				$res7 = mysql_query( $sql7, $conex ) or die( mysql_errno()." - Error en el query $sql6 - ".mysql_error() );
				$rows7 = mysql_fetch_array( $res7 );
			}

			//validacion de la hora para las causas de demora
			//se pasa a segundos la hora de la cita y se le suman 15 minutos
			$horaCitaseg=strtotime(date("Y-m-d")." ".$rows['hi']);
			$horaCita=($horaCitaseg+900);
			//$hora=date("H:i",$horaCita);

			if ($horaActSec >=$horaCita)
			{
				 $mostrarCausa='on';
			}
			else
			{
				$mostrarCausa='off';
			}

			//mostrar el encabezado de la tabla
			//citas caso 1 o caso 3
			if( $i == 0  and ($caso ==1 or $caso==3)){
				echo "	<tr class='encabezadotabla'  align=center>";
				echo "		<td style='width:40'>";
				echo "			Num";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Fecha";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Documento";
				echo "		</td>";
				echo "		<td>";
				echo "			Nombre del Paciente";
				echo "		</td>";
				echo "		<td>";
				echo "			Responsable";
				echo "		</td>";
				echo "		<td>";
				echo "			Doctor";
				echo "		</td>";
				echo "		<td>";
				echo "			Equipo";
				echo "		</td>";
				echo "		<td>";
				echo "			Examen";
				echo "		</td>";
				if ($tipoAtencion=='on')
				{
					echo "		<td>";
					echo "			Tipo Atencion";
					echo "		</td>";
				}
				echo "		<td>";
				echo "			Usuario";
				echo "		</td>";

				if ( $wfec==date("Y-m-d"))
				{
					echo "		<td style='width:90'>";
					echo "			Ingreso a la atencion";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			Cancelada";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			No asiste";
					echo "		</td>";

				}
				echo "	</tr>";
			}

			if ($i == 0 and $caso == 2 and $valCitas == "on")  //citas clinica del sur
			{
				echo "	<tr class='encabezadotabla'  align=center>";
				echo "		<td style='width:40'>";
				echo "			Num";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Fecha";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Documento";
				echo "		</td>";
				echo "		<td>";
				echo "			Nombre del Paciente";
				echo "		</td>";
				echo "		<td>";
				echo "			Responsable";
				echo "		</td>";
				echo "		<td>";
				echo "			Doctor";
				echo "		</td>";
				echo "    <td>";
				echo "			Servicio";
				echo "		</td>";
				echo "		<td>";
				echo "			Tipo Servicio";
				echo "		</td>";
				echo "		<td>";
				echo "			Usuario";
				echo "		</td>";
				echo "		<td>";
				echo "			Historia";
				echo "		</td>";
				if ( $wfec==date("Y-m-d"))
				{
					echo "		<td style='width:90'>";
					echo "			Admision";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			Asiste";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			Cancelada";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			No asiste";
					echo "		</td>";

				}
				echo "	</tr>";
			}

			if ($i == 0 and @$valCitas != "on" and $caso==2)   //citas caso 2 diferentes a clinica del sur
			{
				echo "	<tr class='encabezadotabla'  align=center>";
				echo "		<td style='width:40'>";
				echo "			Num";
				echo "		</td>";
				echo "		<td style='width:90'>";
				echo "			Fecha";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Hora";
				echo "		</td>";
				echo "		<td style='width:50'>";
				echo "			Documento";
				echo "		</td>";
				echo "		<td>";
				echo "			Nombre del Paciente";
				echo "		</td>";
				echo "		<td>";
				echo "			Responsable";
				echo "		</td>";
				echo "		<td>";
				echo "			Doctor";
				echo "		</td>";
				echo "		<td>";
				echo "			Usuario";
				echo "		</td>";
				if ( $wfec==date("Y-m-d"))
				{
					echo "		<td style='width:90'>";
					echo "			Ingreso a la atencion";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			Cancelada";
					echo "		</td>";
					echo "		<td style='width:90'>";
					echo "			No asiste";
					echo "		</td>";
				}
				echo "	</tr>";
			}



			//mostrar la informacion de las citas de esa fecha
			if ($caso ==1 or $caso==3)
			{
				echo "	<tr $class>";
				echo "		<td align=center>";
				echo "			".$j."";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['fecha']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."' align=center>";
				echo "			{$rows['hi']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."' align=center>";
				echo "			{$rows['cedula']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."'>";
				echo "			{$rows['nom_pac']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."'>";
				echo "			{$rows4['descripcion']}";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."'>";
				echo "			".$rows2['Codigo']."-".$rows2['Nombre']."";
				echo "		</td>";
				echo "		<td bgcolor='".$color_fondo."'>";
				echo "			".$rows1['Codigo']."-".$rows1['Descripcion']."";
				echo "		</td>";
				echo "		<td>";
				echo "			".$rows3['Codigo']."-".$rows3['Descripcion']."";
				echo "		</td>";
				if ($tipoAtencion=='on')
				{
					echo "		<td>";
					echo "		{$rows['tipoA']}";
					echo "		</td>";
				}
				echo "		<td align=center>";
				echo "			{$rows['usuario']}";
				echo "		</td>";

				if ( $wfec==date("Y-m-d"))
				{

					echo "		<td align=center>";
					echo "			<input type='radio' name='rdAsiste".$rows['id']."' id='rdAsiste".$rows['id']."' onclick='asiste(\"rdAsiste".$rows['id']."\",\"".$rows['id']."\",\"".$mostrarCausa."\")' value='on'>";
					echo "		</td>";

					echo "		<td align=center>";
					echo "			<input type='radio' name='rdCancela".$rows['id']."' id='rdCancela".$rows['id']."' onclick='cancela(\"rdCancela".$rows['id']."\",\"".$rows['id']."\")' value='I'>";
					echo "		</td>";

					echo "		<td align=center>";
					echo "			<input type='radio' name='rdno_asiste".$rows['id']."' id='rdno_asiste".$rows['id']."' onclick='no_asiste(\"rdno_asiste".$rows['id']."\",\"".$rows['id']."\")' value='off'>";
					echo "		</td>";

				}

			}

			//clinica del sur
			if (@$valCitas == "on" and $caso == 2)
			{
				$atencion=$rows['tipoA'];
				$atencion=explode("-", $atencion);
				@$atencion=$atencion[1];
				$servicio=$rows['tipoS'];
				$servicio=explode("-", $servicio);
				@$servicio=$servicio[1];

				echo "	<tr $class>";
				echo "		<td align=center>";
				echo "			".$j."";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['fecha']}";
				echo "		</td>";
				echo "		<td align=center bgcolor=''>";
				echo "			{$rows['hi']}";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['cedula']}";
				echo "		</td>";
				echo "		<td bgcolor=''>";
				echo "			{$rows['nom_pac']}";
				echo "		</td>";
				echo "		<td bgcolor=''>";
				echo "			{$rows5['Descripcion']}";
				echo "		</td>";
				echo "		<td bgcolor=''>";
			    echo "			{$rows['mednom']}";
			    echo "		</td>";
				echo "		<td>";
				echo "			$atencion";
				echo "		</td>";
				echo "		<td>";
				echo "			$servicio";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows['usuario']}";
				echo "		</td>";
				echo "		<td align=center>";
				echo "			{$rows7['pachis']}";
				echo "		</td>";
				if ( $wfec==date("Y-m-d"))
				{
					$wdoc=$rows['cedula'];
					if( $rows['act'] != 'on' ){
						echo "		<td align=center>";
						echo "			<input type='radio' name='rdAdmision' value='on' onclick=\"javascript: abrirVentana( {$rows['id']}, '$solucionCitas', '$wbasedato','$wdoc','$mostrarCausa','$tipodoc' );\">";
						echo "		</td>";
					}
					else{
						echo "		<td align=center>";
						echo "		</td>";
					}

					if( $rows['act'] == 'on' ){
						echo "		<td align=center>";
						echo "			<input type='radio' name='rdAsistida' value='on' onclick=\"javascript: asistida( {$rows['id']}, '$mostrarCausa' );\">";
						echo "		</td>";
					}
					else{
						echo "		<td align=center>";
						echo "		</td>";
					}
						echo "		<td align=center>";
						echo "			<input type='radio' name='rdCancela".$rows['id']."' id='rdCancela".$rows['id']."' onclick='cancela(\"rdCancela".$rows['id']."\",\"".$rows['id']."\")' value='I'>";
						echo "		</td>";

						echo "		<td align=center>";
						echo "			<input type='radio' name='rdno_asiste".$rows['id']."' id='rdno_asiste".$rows['id']."' onclick='no_asiste(\"rdno_asiste".$rows['id']."\",\"".$rows['id']."\")' value='off'>";
						echo "		</td>";
				}
			}



			     //****************************revisar lo de asiste *************************
				if ($caso==2 and $valCitas!="on")
				{
					echo "	<tr $class>";
					echo "		<td align=center>";
					echo "			".$j."";
					echo "		</td>";
					echo "		<td align=center>";
					echo "			{$rows['fecha']}";
					echo "		</td>";
					echo "		<td align=center bgcolor=''>";
					echo "			{$rows['hi']}";
					echo "		</td>";
					echo "		<td bgcolor='' align=center>";
					echo "			{$rows['cedula']}";
					echo "		</td>";
					echo "		<td bgcolor=''>";
					echo "			{$rows['nom_pac']}";
					echo "		</td>";
					echo "		<td bgcolor=''>";
					echo "			{$rows5['Descripcion']}";  //responsable
					echo "		</td>";
					echo "		<td bgcolor=''>";
					echo "			".$rows4['Codigo']."-".$rows4['Descripcion']."";
					echo "		</td>";
					echo "		<td align=center>";
					echo "			{$rows['usuario']}";
					echo "		</td>";
					if ( $wfec==date("Y-m-d"))
					{

						echo "		<td align=center>";
						echo "			<input type='radio' name='rdAsiste".$rows['id']."' id='rdAsiste".$rows['id']."' onclick='asiste(\"rdAsiste".$rows['id']."\",\"".$rows['id']."\",\"".$mostrarCausa."\")' value='on'>";
						echo "		</td>";

						echo "		<td align=center>";
						echo "			<input type='radio' name='rdCancela".$rows['id']."' id='rdCancela".$rows['id']."' onclick='cancela(\"rdCancela".$rows['id']."\",\"".$rows['id']."\")' value='I'>";
						echo "		</td>";

						echo "		<td align=center>";
						echo "			<input type='radio' name='rdno_asiste".$rows['id']."' id='rdno_asiste".$rows['id']."' onclick='no_asiste(\"rdno_asiste".$rows['id']."\",\"".$rows['id']."\")' value='off'>";
						echo "		</td>";

					}

				}

			echo "	</tr>";
		} //for
	}
	else{
		echo "<center>NO HAY CITAS ASIGNADAS PARA HOY</center>";
	}


	echo "</table>";

	if ($caso == 2 and $valCitas == "on")
	{
		if($abrirNuevaAdmision != "on" ){
			echo "<br><br>";
			echo "<center><a href='../../IPS/Procesos/admision.php?ok=9&empresa=$wbasedato&wemp2=citascs' target='_blank'>Admision sin cita</a></center>";
		}else{
			echo "<br><br>";
			echo "<center><input type='button' value='Admision sin cita' onclick='abrirAdmisionNueva();'></center>";
		}
    }
	echo "<br>";

	echo "<br>";
	echo "<center><a href='../../citas/procesos/calendar.php?empresa=".$solucionCitas."&wemp_pmla=".$wemp_pmla."&caso=".$caso."&wsw=".@$wsw."&fest=".$fest."&consultaAjax=' target='_blank'>Asignar cita</a></center>";


	echo "<meta name='met' id='met' http-equiv='refresh' content='40;url=pantallaAdmision_cs.php?solucionCitas=".$solucionCitas."&wemp_pmla=$wemp_pmla&caso=".$caso."&wsw=".@$wsw."&slDoctor=$slDoctor&valCitas=".$valCitas."&wfec=".$wfec."&fest=".$fest."'>";

	echo "<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' />";
	echo "<br><br>";

	echo "<table align='left'><tr><td><a onclick='javascript:imprimir(\"$wemp_pmla\",\"$caso\",\"$wsw\",\"$solucionCitas\",\"$slDoctor\",\"$valCitas\",\"$wfec\")'><b>Imprimir</b></a></td></tr></table>";

	//div causa cancelacion
	echo "<div id='causa_cancelacion' style='display:none'>";
	echo "<center>";
	$tipo = "C";
    causas($tipo);
	echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
	echo "</center>";
	echo "</div>";

	//div causa no asiste
	echo "<div id='causa_noasiste' style='display:none'>";
	echo "<center>";
	$tipo = "NA";
    causas($tipo);
	echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
	echo "</center>";
	echo "</div>";


	//div causa demora
	echo "<div id='causa_demora' style='display:none'>";
	echo "<center>";
	$tipo = "DA";
    causas($tipo);
	echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
	echo "</center>";
	echo "</div>";

	echo "</form>";
	echo "</body>";
	echo "</html>";



}
?>


