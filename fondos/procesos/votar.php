<?php
include_once("conex.php");

@session_start();

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");

include_once("root/comun.php");


$conex = obtenerConexionBD("matrix");

$wactualiz="Julio 21 de 2014";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = aplicacion($conex, $wemp_pmla, "fondos" );
$wtalhumas = traer_talumas($conex, "informacion_empleados"); //Trae todas las empresas que tengan tablas de talento humano.
$wdatos_votacion = aplicacion($conex, $wemp_pmla, "VotacionAspirantesFe" );
$wdatos_inscripcion = aplicacion($conex, $wemp_pmla, "InscripcionDelegadosFe" );
$wcolumnas_votacion = aplicacion($conex, $wemp_pmla, "ColumnasVotacion" );
$wlogoempresa = strtolower( $institucion->baseDeDatos );
$wusuario = substr($user,2,7);

$key = substr($user, 2, strlen($user)); //se eliminan los dos primeros digitos

if(is_numeric($key))
{
	if(strlen($key) == 7 AND "'".substr($key, 2)."'" !== "'".$wemp_pmla."'")
	{

		$wemp_pmla1=(substr($key, 0,2)); //el wemp_pmla son los dos primeros digitos
	    $key2 = substr( $key, -5 );
	}
	else
	{
		$wemp_pmla1=$wemp_pmla;
		$key2 = substr( $key, -5 );
	}

}
else
{
	$key2=$key;
	$wemp_pmla1=$wemp_pmla;
}


mysql_select_db("matrix") or die("No se selecciono la base de datos");

//Trae las tablas que contienen los centros de costos de las empresas.
function traer_tablas_cco($conex, $aplicacion){


	$q =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_ccos = array();

while($row = mysql_fetch_array($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Detval'], $array_ccos))
        {
            $array_ccos[$row['Detval']] = $row;
        }

    }

	return $array_ccos;
}

function traer_talumas($conex, $aplicacion){


	$q =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_talhumas = array();

while($row = mysql_fetch_array($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Detemp'], $array_talhumas))
        {
            $array_talhumas[$row['Detemp']] = $row;
        }

    }

	return $array_talhumas;
}

function empresas(){


	global $conex;

	 $q =  " SELECT Empcod, Empdes "
		  ."   FROM root_000050";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$array_empresas = array();

	while($row = mysql_fetch_array($res))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Empcod'], $array_empresas))
        {
            $array_empresas[$row['Empcod']] = $row;
        }

    }

	return $array_empresas;
}


//Trae una aplicacion con el filtro de empresa.
function aplicacion($conex, $wemp_pmla, $aplicacion){


	 $q =  " SELECT Detval "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'"
		  ."	AND Detemp = '".$wemp_pmla."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$waplicacion = $row['Detval'];

	return $waplicacion;
}



//Trae la ruta de la foto para el usuario que esta ingresando.
function getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$wcedula = 'not_foto',$sex='M')
{
    $extensiones_img = array(   '.jpg','.Jpg','.jPg','.jpG','.JPg','.JpG','.JPG','.jPG',
                                '.png','.Png','.pNg','.pnG','.PNg','.PnG','.PNG','.pNG');
    $wruta_fotos = "../../images/medical/tal_huma/";
    $wfoto = "silueta".$sex.".png";

    $wfoto_em = '';
    $ext_arch = '';


	foreach($extensiones_img as $key => $value)
	{
		$ext_arch = $wruta_fotos.trim($wcedula).$value;
		// echo "<!-- Foto encontrada: $ext_arch -->";
		if (file_exists($ext_arch))
		{
			$wfoto_em = $ext_arch;
			break;
		}
	}


    if ($wfoto_em == '')
    {
        $wfoto_em = $wruta_fotos.$wfoto;
    }

    return $wfoto_em;
}

//Funcion que registra el voto del asociado.
function votar($wemp_pmla, $wbasedato, $wcedula, $wtipo, $wano_votacion, $wconsecutivo_votacion, $wcod_votado, $wced_votado, $wempresa){

	global $conex;
	global $wbasedato;
	global $wusuario;

	$wfecha = date("Y-m-d");
    $whora  = date("H:i:s");

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');

	//Log de votacion.
	$q = " INSERT INTO ".$wbasedato."_000004 (   Medico  ,    Fecha_data,      Hora_data,        Rvoano         ,               Rvovot           , Rvocod      ,   Rvoced, 			 Rvoemp     , Rvoest, Seguridad     ) "
					  ."               VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wano_votacion."' , '".$wconsecutivo_votacion."','".$wusuario."', '".$wcedula."', '".$wempresa."', 'on', 'C-".$wusuario."')";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	//Registro de voto.
	$q = " INSERT INTO ".$wbasedato."_000005 (   Medico  ,    Fecha_data,      Hora_data,        Votano         ,            Votvot           , Votcod      ,          Votced        ,      Votemp     ,Votest, Seguridad     ) "
					  ."               VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wano_votacion."' , '".$wconsecutivo_votacion."','".$wcod_votado."', '".$wced_votado."', '".$wempresa."',  'on', 'C-".$wbasedato."')";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	$datamensaje['mensaje'] = "Gracias por su voto";
	$datamensaje['mensaje_html'] = "<div align=center style='background-color:yellow; height:20px; width: 200px;'><b>Su voto fue registrado.</b></div><br>";
	$datamensaje['tipo'] = $wtipo;

	 echo json_encode($datamensaje);
     return;
}


function verificar_votacion($wusuario, $wcedula, $wano_votacion, $wconsecutivo_votacion){

	global $conex;
	global $wbasedato;

	$q_reg_vot =   " SELECT Rvoest "
			      ."   FROM ".$wbasedato."_000004"
			      ."  WHERE Rvocod = '".$wusuario."'"
				  ."	AND Rvoced = '".$wcedula."'"
				  ."	AND Rvoano = '".$wano_votacion."'"
				  ."	AND Rvovot = '".$wconsecutivo_votacion."'"
			      ."    AND Rvoest = 'on'";
	$res_reg_vot = mysql_query($q_reg_vot,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asociados." - ".mysql_error());
	$row_reg_vot = mysql_fetch_array($res_reg_vot);
	$westado_voto = $row_reg_vot['Rvoest'];

	return $westado_voto;

}

if(isset($consultaAjax))
	{

	switch($consultaAjax){

		case 'votar':
					{
					echo votar($wemp_pmla, $wbasedato, $wcedula, $wtipo, $wano_votacion, $wconsecutivo_votacion, $wcod_votado, $wced_votado, $wempresa);
					}
		break;

		default: break;

		}
	return;
	}

?>
<!DOCTYPE html>
<html lang="es-ES">
<head>
<meta charset="utf-8">
<title>Votaciones para principal y suplente</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<style type="text/css">
    .border1{
	-moz-border-radius: 1em;
	-webkit-border-radius: 1em;
	border-radius: 1em;
	}

	#div_tabla{
	height: auto;
	text-align:center;
	padding: 10px;
	width: 180px;
	border: #CCCCCC solid 2px;
	background: #FFFFFF;
	}

	hr {border: 0; height: 12px; box-shadow: inset 0 12px 12px -12px #000000;}
	#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
	#tooltip div{margin:0; width:auto;}
	.bordeAbajo{
		border-bottom: 2px dotted #72A3F3;
	}
</style>
<script type="text/javascript">
function ver_imagen_grande(){


}

var mouseSobreRadio = false;

function votar(wemp_pmla, wbasedato, cedula, i, tipo, ano_votacion, consecutivo_votacion, cod_votado, ced_votado, empresa, Elemento){

	var nomSuplente 	= $(Elemento).parent().parent().prev().find("[nomSup]").text()+" "+$(Elemento).parent().parent().prev().find("[nomSup]").next().text();
	var nomPrincipal	= $(Elemento).parent().parent().prev().prev().find("[nomPri]").text()+" "+$(Elemento).parent().parent().prev().prev().find("[nomPri]").next().text();
	
	if( mouseSobreRadio == false )
		return;
		
	if( nomSuplente == "" && nomPrincipal == "" ){
		nomSuplente = "EN BLANCO";
		nomPrincipal = "EN BLANCO";
	}

	if(confirm('Su voto sera por:\n\n - '+nomPrincipal+"\n- "+nomSuplente))
	{
		$.post("votar.php",
					{
						consultaAjax:			'votar',
						wemp_pmla:				wemp_pmla,
						wbasedato:				wbasedato,
						wcedula:				cedula,
						wtipo:					tipo,
						wano_votacion:			ano_votacion,
						wconsecutivo_votacion: 	consecutivo_votacion,
						wcod_votado:			cod_votado,
						wced_votado:			ced_votado,
						wempresa:				empresa

					}
					,function(data_json) {

						if (data_json.error == 1)
						{
							alert("Error: "+data_json);
							return;
						}
						else
						{
						$('#radio_ppales_'+i).prop('checked', false);
						$('#radio_suplentes_'+i).prop('checked', false);

						jQuery("input[name='radio_button_"+data_json.tipo+"']").each(function(i) {
							jQuery(this).attr('disabled', 'disabled');
							});

						alert(data_json.mensaje);

						$('#mensaje_votacion').append(data_json.mensaje_html);


						}

				},
				"json"
			);
	}
	else
	{
		$(Elemento).removeAttr("checked");
	}
}


function limpiarbusqueda(){

 $.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

$('input#id_search').val('');


location.reload();


}

function cerrarVentana(){
  window.close();
 }


$(document).ready( function () {
			//Si ponen el mouse sobre los radio de clase "radiovotar" se pone la vble mouseSobreRadio en true
			//para que permita realizar el voto
			$(".radiovotar").mouseover(function(){
				mouseSobreRadio = true;
			});

			// --> Activar tooltip
			$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			//Permite que al escribir en el campo buscar, se filtre la informacion del grid
			$('input#id_search').quicksearch('div#div_tabla_asociados table tbody .find');

			//$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			$('.lightbox_suplente').click(function() {
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');

				$.blockUI({
					message: $('#'+id),
					css: {
						top:  ($(window).height() - 750) /2 + 'px',
						left: ($(window).width() - 500) /2 + 'px',
						width: 'auto'
					}
				});

				$('.blockOverlay').attr('title','Click para cerrar').click($.unblockUI);
			});


			$('.lightbox_ppal').click(function() {
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');

				$.blockUI({
					message: $('#'+id),
					css: {
						top:  ($(window).height() - 750) /2 + 'px',
						left: ($(window).width() - 500) /2 + 'px',
						width: 'auto'
					}
				});

				$('.blockOverlay').attr('title','Click para cerrar').click($.unblockUI);
			});


        });


</script>
<Body>
<?php

//==========================================================================================================================================
//PROGRAMA				      :Pograma donde se listan los asociados aprobados para que sean votados.
//AUTOR				          :Jonatan Lopez Aguirre.
//FECHA CREACION			  :Enero 31 de 2014
//FECHA ULTIMA ACTUALIZACION  :Enero 31 de 2014

encabezado("Votaciones para principal y suplente",$wactualiz, $wlogoempresa);

//=========================================================================================================================================\\
//Julio 21 de 2014 (Jonatan Lopez)
//Se controla si se muestra la informacion del suplente con un parametro en la root_000051 ControlSuplenteVotaciones.
//=========================================================================================================================================\\

//Parametros para validar los estados de la votacion.
$array_datos_votacion = explode("-",$wdatos_votacion);
$wano_votacion = $array_datos_votacion[0];
$wconsecutivo_votacion = $array_datos_votacion[1];
$westado_votaciones = $array_datos_votacion[2];


$wdatos_inscripcion = aplicacion($conex, $wemp_pmla, "InscripcionDelegadosFe" );
$array_datos_inscripcion = explode("-",$wdatos_inscripcion);
$westado_inscripciones = $array_datos_inscripcion[2];

$wdatos_inscripcion = aplicacion($conex, $wemp_pmla, "ControlVotoenBlanco" );
$array_control_voto_blanco = explode("-",$wdatos_inscripcion);
$wcontrol_voto_blanco_cons = $array_control_voto_blanco[0];
$wwcontrol_voto_blanco_est = $array_control_voto_blanco[1];

$wdatos_inscripcion = aplicacion($conex, $wemp_pmla, "ControlSuplenteVotaciones" );
$array_control_suplente = explode("-",$wdatos_inscripcion);
$wcontrol_suplente_cons = $array_control_suplente[0];
$wcontrol_suplente_est = $array_control_suplente[1];


//Buscar en la tabla de usuario que cco le pertenece al usuario.
$q_user = "SELECT Descripcion, Empresa
			 FROM usuarios
			WHERE Codigo = '".$wusuario."'			
			  AND Activo = 'A'";
$res_user = mysql_query($q_user, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_user . " - " . mysql_error());
$row_user = mysql_fetch_array($res_user);

$empresa_tabla_usuarios = $row_user['Empresa'];

//Verifica si hay votaciones abiertas, en caso no haber ninguna muestra un mensaje diciendo que no estan abiertas, ademas las inscripciones deben estar cerradas.
if($westado_votaciones == 'on' and $westado_inscripciones == 'off'){

foreach($wtalhumas as $key_a => $value){
//Consulto la informacion del usuario por el codigo en la tabla talhuma_000013
$q[] =    " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, '".$value['Detval']."' as '".$value['Detval']."'"
		 ."	  FROM ".$value['Detval'].""
		 ."	 WHERE (Ideuse = '".$key2."-".$wemp_pmla1."'"
		 ."     OR Ideuse = '".$key."' OR Ideuse = '".$key2."-".$empresa_tabla_usuarios."')"
		 ."    AND Ideest = 'on'"
		 ."    AND Ideced != '' ";

	}

//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
$q = implode(" UNION ", $q);
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$row = mysql_fetch_assoc($res);
$num = mysql_num_rows($res);

if($num > 0){

	$genero = $row['Idegen'];
	$wcedula = $row['Ideced'];
	$wnombre1 = strtolower($row['Ideno1']);
	$wnombre2 = strtolower($row['Ideno1']);
	$wapeliido1 = strtolower($row['Ideap1']);
	$wapellido2 = strtolower($row['Ideap2']);
	$wcentro_costos = $row['Idecco'];

	//Traigo la informacion de la tabla de empleados ppal.
	$q_asoc =   " SELECT Asoemp "
			   ."   FROM ".$wbasedato."_000006 "
			   ."  WHERE Asoced = '".$wcedula."'"
			   ."    AND Asoest = 'on'";
	$res_asoc = mysql_query($q_asoc,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asoc." - ".mysql_error());
	$num_asoc = mysql_num_rows($res_asoc);

	//Verificar si el usuario se encuentra asociado al fondo de empleados.
	if($num_asoc == 0)
		{
		echo "<br>\n<br>\n".
			" <H1 align=center>No se encuentra en este fondo, no puede votar por delegado.<FONT COLOR='RED'>" .
			" </FONT></H1>\n</CENTER>";
		return;
		}

	$row_asoc = mysql_fetch_array($res_asoc);
	$wempresa_asociado = $row_asoc['Asoemp']; //Empresa del asociado de la tabla de asociados.

	$array_inscritos_ppales = array();
	$array_cco_empresa = array();

	foreach($wtalhumas as $key_b => $value){

	//Consulta los suplente principales y los agrega al arreglo inscritos
	$query_inscritos_ppales[] = "  SELECT Insced, Insapr, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insemp, Inscco, Insesu, Insccs
								   FROM ".$wbasedato."_000003, ".$value['Detval']."
								  WHERE Insced = Ideced
									AND Insest = 'on'
									AND Instip = '01'
									AND Insapr = 'on'
									AND Insemp = '".$wempresa_asociado."'
									AND Insano = '".$wano_votacion."'
									AND Insvot = '".$wconsecutivo_votacion."'
									AND Ideest = 'on'";

	}

	//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
	$query_inscritos_ppales = implode(" UNION ", $query_inscritos_ppales);	
	$res_inscritos_ppales = mysql_query($query_inscritos_ppales);
	$array_inscritos_ppales = array();

	while($row_inscritos_ppales = mysql_fetch_assoc($res_inscritos_ppales))
		{

			if(!array_key_exists($row_inscritos_ppales['Insced'], $array_inscritos_ppales))
			{
				$array_inscritos_ppales[$row_inscritos_ppales['Insced']] = $row_inscritos_ppales;
			}

			if(!array_key_exists($row_inscritos_ppales['Idecco'], $array_cco_empresa))
			{
				$array_cco_empresa[$row_inscritos_ppales['Idecco']] = $row_inscritos_ppales['Idecco'];
			}

		}

		// echo "<pre>";
	// echo "<div>";
	// print_r($array_inscritos_ppales);
	// echo "</div>";
	// echo "<pre>";

	//Recorro todos los talhumas y creo la union de todos ellos, esto para buscar los suplentes.
	foreach($wtalhumas as $key_c => $value){

	$query_suplentes[] = " SELECT Insced, Insapr, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insemp, Inscco, Insesu, Insccs
						   FROM ".$wbasedato."_000003, ".$value['Detval']."
						  WHERE Insces = Ideced
							AND Insest = 'on'
							AND Insapr = 'on'
							AND Insemp = '".$wempresa_asociado."'
							AND Insano = '".$wano_votacion."'
							AND Insvot = '".$wconsecutivo_votacion."'
							AND Ideest = 'on'";
		}

	//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
	$query_suplentes = implode(" UNION ", $query_suplentes);
	$res_suplentes = mysql_query($query_suplentes);
	$array_suplentes = array();

	while($row_suplentes = mysql_fetch_assoc($res_suplentes))
		{
			//Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_suplentes['Insces'], $array_suplentes))
			{
				$array_suplentes[$row_suplentes['Insces']] = $row_suplentes;
			}

		}

	// echo "<pre>";
	// echo "<div>";
	// print_r($array_suplentes);
	// echo "</div>";
	// echo "<pre>";


	$array_centros_costo = array();
	//Busco las tablas de centros de costos.
	$wtabla_cco = traer_tablas_cco($conex, "costoscer");

	//Consultar el los centros de costos de las empresas.
	$array_empresas_cco = array();

	foreach($wtabla_cco as $key_tablas_cco => $value_tablas_cco){

		//Consulto en las tablas los datos de los centros de costos.
		$q_cco =   " SELECT * "
				  ."   FROM ".$value_tablas_cco['Detval']."";
		$res_ccos = mysql_query($q_cco,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cco." - ".mysql_error());

		//Creo un arreglo inicial de empresas.
		if(!array_key_exists($value_tablas_cco['Detemp'], $array_empresas_cco))
		{
			$array_empresas_cco[$value_tablas_cco['Detemp']] = array();
		}

		//A cada empresa le relaciono sus centros de costos.
		while($row_ccos = mysql_fetch_assoc($res_ccos))
		{
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_ccos['Ccocod'], $array_empresas_cco[$value_tablas_cco['Detemp']]))
			{

				$array_empresas_cco[$value_tablas_cco['Detemp']][$row_ccos['Ccocod']] = $row_ccos;
			}

		}

	}

		// echo "<pre>";
		// print_r($array_empresas_cco);
		// echo "</pre>";
	///==================== Asociados al fondo con registro activo ==================

$q =  " SELECT Asoced, Asoemp, Asoemr "
	 ."   FROM ".$wbasedato."_000006"
	 ."	 WHERE Asoest = 'on'";
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

$array_asociados = array();

	while($row_asociados = mysql_fetch_assoc($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_asociados['Asoced'], $array_asociados))
        {
            $array_asociados[$row_asociados['Asoced']] = $row_asociados;
        }

    }


	// echo "<pre>";
 // print_r($array_asociados);
 // echo "</pre>";

//=====================

	$whabilitar_votaciones = '';

	echo "<br><br><br>";
	echo "<form id='form_inscritos_ppales'>";
	echo "<center>";
	echo "<table>";
	echo "<tr>";
	echo "<td class=encabezadotabla>Buscar</td>";
	echo "<td class=encabezadotabla><input id='id_search' type='text' value='' name='search'></td>";
	echo "<td><img width='auto' width='15' height='15' border='0' onclick='limpiarbusqueda();' title='Reiniciar Busqueda' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></td>";
	echo "</tr>";
	echo "</table>";
	echo "<center>";
	echo "<br><br>";
	echo "<div align=center id='div_tabla_asociados' style='width: auto;'>";

	$numcolumnas = $wcolumnas_votacion;	//Cantidad de columnas en las que se dividira el tarjeton de votacion.

	echo "<table width='40%' id='tabla_principales'>"; //Tabla de principales

		   //Deshabilita los radio button para las votaciones si el tercer parametro la root_000051 (VotacionAspirantesFe) esta en off.
		   $westado_actual_votacion = verificar_votacion($wusuario, $wcedula, $wano_votacion, $wconsecutivo_votacion);

		   if($westado_votaciones == 'on'){

				//Verifica si el asocaido no ha votado.
				if($westado_actual_votacion == ''){

					$whabilitar_votaciones = "";
					$vista_mensaje = 'display:none;'; //Si no ha votado, el tr del mensaje no se mostrará.

				}else{

				//Si ya voto, lo botones se inhabilitaran.
				$whabilitar_votaciones = "disabled";
				$wmensaje = "<div align=center style='background-color:yellow; height:20px; width: 200px;'><b>Su voto fue registrado.</b></div><br>";

				$vista_mensaje = '';
				}

		   }else{
		   $whabilitar_votaciones = "disabled"; //Si ya voto, lo botones se inhabilitaran.
		   $vista_mensaje = 'display:none;'; //Si no ha votado pero las votaciones estan cerradas, el tr del mensaje no se mostrará.
		   }

		   $wempresas = empresas(); //Array de empresas

		   if (count($array_inscritos_ppales) > 0) {
			 echo "<div id='mensaje_votacion' >".$wmensaje."</div>";

			 $i = 1;
			 $wcentro_costos_ppal= '';
			 $wcentro_costos_suple = '';
			 foreach($array_inscritos_ppales as $key_ppales => $valores_ppal){

				   $resto = ($i % $numcolumnas);
				   if($resto == 1){ /*si es el primer elemento creamos una nueva fila*/
					 echo "<tr class='find'>";
					}

				 echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";

				 $wgenero_ppal = ($valores_ppal['Idegen'] == '') ? "M" : $valores_ppal['Idegen'];
				 $wgenero_suplente = ($array_suplentes[$valores_ppal['Insces']]['Idegen'] == '') ? "M" : $array_suplentes[$valores_ppal['Insces']]['Idegen'];
				 //Trae la foto del usuario.
				 $foto_ppal = '<img class="imagen lightbox_ppal" id=fotografia_ppal_'.$i.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,$valores_ppal['Insced'],$wgenero_ppal).'"/>';

				 $foto_suplente = '<img class="imagen lightbox_suplente" id=fotografia_suplente_'.$i.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,$valores_ppal['Insces'],$wgenero_suplente).'"/>';

				//Centro de costos asociado al ppal.
				$wcentro_costos_ppal = $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Cconom'];
				//Si no esta con nombre lo busca con descripcion.
				$wcentro_costos_ppal = ($wcentro_costos_ppal == '') ? $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Ccodes'] : $array_empresas_cco[$wempresa_asociado][$valores_ppal['Idecco']]['Cconom'];
				//Si no esta con nombre ni con descripcion, imprime la empresa.
				 if($wcentro_costos_ppal == ''){

					$wcentro_costos_ppal = $wempresas[$valores_ppal['Insemp']]['Empdes'];
				}

				//Centro de costos asociado al suplente.
				$wcentro_costos_suple = $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Cconom'];
				//Si no esta con nombre lo busca con descripcion.
				$wcentro_costos_suple = ($wcentro_costos_suple == '') ? $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Ccodes'] : $array_empresas_cco[$wempresa_asociado][$array_suplentes[$valores_ppal['Insces']]['Idecco']]['Cconom'];
				//Si no esta con nombre ni con descripcion, imprime la empresa.
				if($wcentro_costos_suple == ''){

					$wcentro_costos_suple = $wempresas[$valores_ppal['Insesu']]['Empdes'];
				}


				//Si el asociado ppal tiene datos en empresa real en la tabla de asociados (fondos_000006 - Asoemr), entonces pondra esa en el reporte, sino, pondra la empresa que se encuentra en el campo Asoemp.
				if($array_asociados[$valores_ppal['Insced']]['Asoemr'] != ''){
				$wempresa_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemr']]['Empdes'];
				$wcentro_costos_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemr']]['Empdes']; //Esta variable se reasignara en caso de que el asociado tenga datos en empresa real.
				}else{
				$wempresa_ppal = $wempresas[$array_asociados[$valores_ppal['Insced']]['Asoemp']]['Empdes'];
				}

				//Si el asociado suplente tiene datos en empresa real en la tabla de asociados (fondos_000006 - Asoemr), entonces pondra esa en el reporte, sino, pondra la empresa que se encuentra en el campo Asoemp.
				if($array_asociados[$valores_ppal['Insces']]['Asoemr'] != ''){
				$wempresa_suplente = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemr']]['Empdes'];
				$wcentro_costos_suple = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemr']]['Empdes'];	//Esta variable se reasignara en caso de que el asociado tenga datos en empresa real.
				}else{
				$wempresa_suplente = $wempresas[$array_asociados[$valores_ppal['Insces']]['Asoemp']]['Empdes'];
				}


				 echo "<td class='bordeAbajo'><br>
							<div id='div_tabla' class='border1'>
							<div style='font-size:12px; color:#000000;'><b>Principal</b></div>
							<div>&nbsp;</div>
							<div>".$foto_ppal." <img class='fotografia_ppal_".$i."' id='imagen_grande_ppal_".$i."' src='".getFoto($conex,$wempresa_asociado,$wbasedato,'',$valores_ppal['Insced'],$valores_ppal['Idegen'])."' style='display:none' /></div>
							<div><hr></div>
							<div nomPri style='font-size:18px; color:#13189F; display:block;'>".ucfirst($valores_ppal['Ideno1'])." ".ucfirst($valores_ppal['Ideno2'])."</div>
							<div style='font-size:12px; color:#13189F;'>".ucfirst($valores_ppal['Ideap1'])." ".ucfirst($valores_ppal['Ideap2'])."</div>
							<div style='font-size:10px; color:#000000;'><b>".utf8_encode($wempresa_ppal)."</b></div>
							<div style='font-size:9px; color:#D02114;'>".utf8_encode($wcentro_costos_ppal)."</div>
						</div><br></td>";
				
				//Verifico si se debe mostrar el suplente.
				if($wcontrol_suplente_cons == $wconsecutivo_votacion and $wcontrol_suplente_est == 'on'){
				
				echo "<td class='bordeAbajo'><br><div id='div_tabla' class='border1'>
							<div style='font-size:12px; color:#000000;'><b>Suplente</b></div>
							<div>&nbsp;</div>
							<div>".$foto_suplente." <img class='fotografia_suplente_".$i."' id='imagen_grande_suplente_".$i."' src='".getFoto($conex,$wempresa_asociado,$wbasedato,'',$valores_ppal['Insces'],$valores_ppal['Idegen'])."' style='display:none' /></div>
							<div><hr></div>
							<div nomSup style='font-size:18px; color:#13189F;'>".ucfirst($array_suplentes[$valores_ppal['Insces']]['Ideno1'])." ".ucfirst($array_suplentes[$valores_ppal['Insces']]['Ideno2'])."</div>
							<div style='font-size:12px; color:#13189F; display:inline;'>".ucfirst($array_suplentes[$valores_ppal['Insces']]['Ideap1']." ".$array_suplentes[$valores_ppal['Insces']]['Ideap2'])."</div>
							<div style='font-size:10px; color:#000000;'><b>".utf8_encode($wempresa_suplente)."</b></div>
							<div style='font-size:9px; color:#D02114;'>".utf8_encode($wcentro_costos_suple)."</div>
						</div><br></td>";
				}else{
				
				echo "<td></td>";
				
				}
				
				//Radio buton para votar.				
				echo "
					<td colspan=2 style='padding: 3px;'>
						<div align='center' style='padding: 3px;font-size: 8pt; font-family: verdana;color:#2A5DB0;border: 1px solid #72A3F3;'>
							<b>VOTAR</b>
							<input type=radio class='radiovotar' name='radio_button_principal' tooltip='si' title='Click para votar' style='cursor:pointer' id='radio_ppales_".$i."' ".$whabilitar_votaciones." onclick='votar(\"$wemp_pmla\",\"$wbasedato\",\"".$wcedula."\", \"$i\",\"principal\", \"".$wano_votacion."\", \"".trim($wconsecutivo_votacion)."\", \"".$valores_ppal['Ideuse']."\" , \"".$valores_ppal['Ideced']."\",\"$wempresa_asociado\", this);'>
						</div>
					</td>";
				
				 /*mostramos el valor del campo especificado*/
				if($resto == 0){
				  /*cerramos la fila*/
				  echo "</tr>";
				}
			   $i++;

			 }

		 if($resto != 0){
		  /*Si en la &uacute;ltima fila sobran columnas, creamos celdas vac&iacute;as*/
		   for ($j = 0; $j < ($numcolumnas - $resto); $j++){
			 echo "<td></td>";
			}
		   echo "</tr>";
		  }
		  echo "</table>";
		  echo "<br><br>";
		
		//Verifico si se debe mostrar el voto en blanco.
		if($wcontrol_voto_blanco_cons == $wconsecutivo_votacion and $wwcontrol_voto_blanco_est == 'on'){	
		
		  $foto_blanco = '<img class="imagen lightbox_ppal" id=fotografia_ppal_'.$i.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,'','M').'"/>';
		  $foto_blanco_suplente = '<img class="imagen lightbox_suplente" id=fotografia_suplente_'.$i.' width=100px height=110px src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$wusuario,'','M').'"/>';
				
		  echo "<center>";
		  echo "<table>";
		  echo "<tr>";
		  echo "<td><div id='div_tabla' class='border1'>
					<div style='font-size:12px; color:#000000;'><b>Principal</b></div>
					<div>&nbsp;</div>
					<div>".$foto_blanco." <img class='fotografia_ppal_".$i."' id='imagen_grande_ppal_".$i."' src='".getFoto($conex,$wempresa_asociado,$wbasedato,'','','M')."' style='display:none' /></div>
					<div><hr></div>
					<div style='font-size:18px; color:#13189F; display:block;'>Voto en blanco</div>
					<div style='font-size:12px; color:#13189F;'></div>
					<div style='font-size:10px; color:#000000;'></div>
					<div style='font-size:9px; color:#D02114;'></div>
				</div></td>";
		  echo "<td><div id='div_tabla' class='border1'>
					<div style='font-size:12px; color:#000000;'><b>Suplente</b></div>
					<div>&nbsp;</div>
					<div>".$foto_blanco_suplente." <img class='fotografia_suplente_".$i."' id='imagen_grande_suplente_".$i."' src='".getFoto($conex,$wempresa_asociado,$wbasedato,'','','M')."' style='display:none' /></div>
					<div><hr></div>
					<div style='font-size:18px; color:#13189F;'>Voto en blanco</div>
					<div style='font-size:12px; color:#13189F; display:inline;'></div>
					<div style='font-size:10px; color:#000000;'></div>
					<div style='font-size:9px; color:#D02114;'></div>
				</div></td>";
		  echo "
				<td colspan=2 style='padding: 3px;'>
					<div align='center' style='padding: 3px;font-size: 8pt; font-family: verdana;color:#2A5DB0;border: 1px solid #72A3F3;'>
						<b>VOTAR</b><br>
						<input type=radio class='radiovotar' name='radio_button_principal' tooltip='si' title='Click para votar' style='cursor:pointer' id='radio_ppales_".$i."' ".$whabilitar_votaciones." onclick='votar(\"$wemp_pmla\",\"$wbasedato\",\"".$wcedula."\", \"$i\",\"principal\", \"".$wano_votacion."\", \"".trim($wconsecutivo_votacion)."\", \"00000\" , \"00000000\",\"$wempresa_asociado\", this);'>
					</div>
				</td>";
		  echo "</tr>";
		  echo "</table>";
		  echo "</center>";
		}else{
				
				echo "<td></td>";
				
				}

		}

	echo "</div>";
	echo "</form>";
	}else{

	echo "<br>\n<br>\n".
			" <H1 align=center>Solamente puede votar por los inscritos de su empresa.<FONT COLOR='RED'>" .
			" </FONT></H1>\n</CENTER>";

	}

}else{

	echo "<br>\n<br>\n".
        " <H1 align=center>No hay votaciones abiertas en este momento.</H1>";

}

echo "<br><br>";
echo "<center>
	<div><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";

?>
</BODY>
</html>
