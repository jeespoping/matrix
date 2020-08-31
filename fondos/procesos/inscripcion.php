<?php
include_once("conex.php");
session_start();

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");
      
include_once("root/comun.php");


$conex = obtenerConexionBD("matrix");
$wactualiz="2016-02-16";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}


$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "votaciones" );
$wlogoempresa = strtolower( $institucion->baseDeDatos );
$wfecha_max_insc_asoc = aplicacion($conex, $wemp_pmla, "FechaValidaInscripcionAsociado" );

//Configuracion de la votacion
$q_conf_vot =    " SELECT * "
				."   FROM ".$wbasedato."_000001 "
			    ."	WHERE Votapl = '".$aplicacion."'"				
			    ."	  AND Votact = 'on'";
$res_conf_vot = mysql_query($q_conf_vot,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_conf_vot." - ".mysql_error());

$array_conf_vot = array();

	while($row_conf_vot = mysql_fetch_assoc($res_conf_vot))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_conf_vot['Votcod'], $array_conf_vot))
        {
            $array_conf_vot[$row_conf_vot['Votcod']] = $row_conf_vot;
        }

    }


foreach($array_conf_vot as $key => $value){
	
	$cod_vot = $key;
	
}


$wconsecutivo_votacion = $array_conf_vot[$cod_vot]['Votcod'];  //Codigo de la votacion activa

//Temas asociados a la votacion
$q_temas =   " SELECT * "
			."   FROM ".$wbasedato."_000002 "
			."	WHERE Temapl = '".$aplicacion."' $filtro_tipo "
			."	  AND Temcvo = '".$wconsecutivo_votacion."'"
			."	  AND Temest = 'on'";
$res_temas = mysql_query($q_temas,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_temas." - ".mysql_error());

$array_temas = array();

	while($row_temas = mysql_fetch_assoc($res_temas))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_temas['id'], $array_temas))
        {			
            $array_temas[$row_temas['id']] = $row_temas;
        }

    }

	//print_r($array_conf_vot);
	
$westado_votaciones = $array_conf_vot[$cod_vot]['Votact'];  //Verifica si las votaciones estan cerradas.
$westado_inscripcion = $array_conf_vot[$cod_vot]['Vothin'];  //Verifica si las votaciones estan cerradas.
$wcontrol_voto_blanco_est = $array_conf_vot[$cod_vot]['Votbla'];  //Voto en blanco.
$wcontrol_suplente_est = $array_conf_vot[$cod_vot]['Votsup']; //Suplente.
$wcon_inscripcion = $array_conf_vot[$cod_vot]['Vothin'];  //Habilitado para inscripcion.
$wtalhumas = traer_talumas($conex, "informacion_empleados", $aplicacion, $wbasedato); //Trae todas las empresas que tengan tablas de talento humano.
$numcolumnas = $array_conf_vot[$cod_vot]['Votcol'];	//Cantidad de columnas en las que se dividira el tarjeton de votacion.


$key = substr($user, 2, strlen($user)); //se eliminan los dos primeros digitos


if(is_numeric($key))
{	
	if(strlen($key) == 7 AND "'".substr($key, 2)."'" != "'".$wemp_pmla."'")
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

///  AREA DE FUNCIONES

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

//Trae las empresas registradas en la tabla 50 de root.
function empresas(){

	
	global $conex;
	
	 $q =  " SELECT Empcod, Empdes "
		  ."   FROM root_000050";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$array_empresas = array();
	
	while($row = mysql_fetch_array($res))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Empcod'], $array_empresas))
        {
            $array_empresas[$row['Empcod']] = $row;
        }
		
    }
	
	return $array_empresas;
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


function aplicacion($conex, $wemp_pmla, $aplicacion){

	
	 $q =  " SELECT Detval "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'"
		  ."    AND Detemp = '".$wemp_pmla."'";
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
    
	// comentado para que no aparezca foto a nadie, determinación temporal.
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

function tipo_inscripcion($wtipo){

	global $conex;
	global $wbasedato;
	
	$q_tipo_ins =  " SELECT Tipdes "
			      ."   FROM ".$wbasedato."_000007"
			      ."  WHERE Tipcod = '".$wtipo."'"
			      ."    AND Tipest = 'on'";
	$res_tipo_ins = mysql_query($q_tipo_ins,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asociados." - ".mysql_error());
	$row_insc = mysql_fetch_array($res_tipo_ins);
	$wtipo_incripcion = $row_insc['Tipdes'];
	
	return $wtipo_incripcion;

}

function validar_clave_suplente($wdatos_suplente, $wclave_del_suplente, $wempresa){

	global $conex;
	global $wbasedato;
	
	$array_datos_suple = explode("|",$wdatos_suplente);
	$wcod_suplente = explode("-",$array_datos_suple[1]);
	
	$wclave_del_suplente = substr($wclave_del_suplente, 0, 8); //Solo los primeros 8 caracteres
	
	$wempresa_real_suplente = ($array_datos_suple[5] != "") ? $array_datos_suple[5] : $array_datos_suple[4];
	
	
	// echo$q_usuario =   " SELECT Password "
			      // ."   FROM usuarios "
			      // ."  WHERE codigo LIKE '%".$wcod_suplente[0]."%'"
				  // ."    AND Password = '".$wclave_del_suplente."'"
				  // ."    AND Empresa = '".$wempresa."'"
			      // ."    AND Activo = 'A'";
				  
	$q_usuario =   " SELECT Password "
			      ."   FROM usuarios "
			      ."  WHERE codigo LIKE '%".$wcod_suplente[0]."%'"
				  ."    AND Password = '".$wclave_del_suplente."'"
				  ."    AND Empresa = '".$wempresa_real_suplente."'"
			      ."    AND Activo = 'A'";			  
	$res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asociados." - ".mysql_error());
	$row_usuario = mysql_fetch_array($res_usuario);
	$wcodigo_usuario = $row_usuario['Password'];
	
	return $wcodigo_usuario;

}


function validar_clave_ppal($wusuario, $wclave_ppal, $wempresa){

	global $conex;
	global $wbasedato;
	
	$wclave_ppal = substr($wclave_ppal, 0, 8); //Solo los primeros 8 caracteres
	
	$q_usuario_ppal =   " SELECT Password "
					  ."   FROM usuarios"
					  ."  WHERE codigo = '".$wusuario."'"
					  ."    AND Password = '".$wclave_ppal."'"
					  ."    AND Empresa = '".$wempresa."'"
					  ."    AND Activo = 'A'";
	$res_usuario_ppal = mysql_query($q_usuario_ppal,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asociados." - ".mysql_error());
	$row_usuario_ppal = mysql_fetch_array($res_usuario_ppal);
	$wcodigo_usuario_ppal = $row_usuario_ppal['Password'];
	
	return $wcodigo_usuario_ppal;

}

function inscribirse($wemp_pmla, $wusuario, $wcedula, $wcentro_costos, $wtipo_inscripcion, $wclave_del_ppal, $wclave_del_suplente, $wdatos_suplente, $wnombre_suplente, $wempresa_asociado, $wempresa_real, $aplicacion, $wconsecutivo_votacion)
    {

    global $conex;
	global $wbasedato;
	global $wdatos_inscripcion;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'tipo_inscripcion'=>''); 
	$wfecha = date("Y-m-d");
    $whora  = date("H:i:s");	
		
	$array_datos_usuarios = explode("|",$wdatos_suplente);	
	$wcedula_suplente = $array_datos_usuarios[0];
	$wcodigo_suplente = $array_datos_usuarios[1];
	$wempresa_suplente = $array_datos_usuarios[2];
	$wcentro_costos_suplente = $array_datos_usuarios[3];
	
	$validar_clave_ppal = validar_clave_ppal($wusuario, $wclave_del_ppal, $wempresa_real);
	$validar_clave_suplente = validar_clave_suplente($wdatos_suplente, $wclave_del_suplente, $wempresa_real);		
		
	if($validar_clave_ppal == ''){
	
		$datamensaje['mensaje'] = "La clave del asociado principal, no es correcta.";
		$datamensaje['error'] = 1;		
	
	}else{
	
		if($validar_clave_suplente == ''){
		
			$datamensaje['mensaje'] = "La clave del asociado suplente, no es correcta.";
			$datamensaje['error'] = 1;
			
		}else{
			//2014-02-14 Verificar que el asociado principal y suplente no existan en la tabla ni como principal ni como suplente
			$qq = " SELECT Inscod, Inscsu 
				     FROM ".$wbasedato."_000003
					WHERE (Insced IN ('".$wcedula."','".$wcedula_suplente."')
					   OR  Insces IN ('".$wcedula."','".$wcedula_suplente."')) 
					  AND  Insest = 'on'
					  AND Insapl = '".$aplicacion."'
					  AND Insvot = '".$wconsecutivo_votacion."'";
			$res1 = mysql_query($qq, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
			$num1 = mysql_num_rows( $res1 );			
			if( $num1 > 0 ){
				$datamensaje['mensaje'] = "El asociado principal o el suplente ya estan preinscritos.";
				$datamensaje['error'] = 1;
			}else{
				$q = " INSERT INTO ".$wbasedato."_000003 (   Medico  ,    Fecha_data,      Hora_data,           Insvot               ,    Inscod    ,    Insced    ,              Insemp     ,          Inscco      ,  Insapr,          Instip         ,         Inscsu        ,        Insces         ,         Insesu         ,           Insccs             ,     Insapl      , Insest,     Seguridad     ) "
						  ."                     VALUES ('".$wbasedato."','".$wfecha."','".$whora."', '".$wconsecutivo_votacion ."','".$wusuario."', '".$wcedula."', '".$wempresa_asociado."', '".$wcentro_costos."',  'off' , '".$wtipo_inscripcion."','".$wcodigo_suplente."','".$wcedula_suplente."','".$wempresa_suplente."','".$wcentro_costos_suplente."','".$aplicacion."', 'on'  , 'C-".$wusuario."')";
				$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

				$wtext_tipo = tipo_inscripcion($wtipo_inscripcion);
				$datamensaje['mensaje_html'] = "<div align=center>Se ha preinscrito como aspirante a principal <br> con <b>".$wnombre_suplente."</b> como suplente. <br><br><input type='button' id='inscribirse_ppal' value='Cancelar Inscripción' onclick='cancelar_inscripcion(\"$wemp_pmla\",\"$wcedula\")' ></div>";
				$datamensaje['mensaje'] = "Se ha preinscrito como aspirante a principal con ".$wnombre_suplente." como suplente.";
				$datamensaje['tipo_inscripcion'] = $wtext_tipo;
			}
		}
	}
	
    echo json_encode($datamensaje);
    return;

    }

	
function cancelar_inscripcion($wemp_pmla, $wcedula_ppal){

	global $conex;
	global $wbasedato;

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'tipo_inscripcion'=>''); 

	$q_cancela_ins =   " DELETE FROM ".$wbasedato."_000003"
				      //."    SET Insest = 'off'"
					  ."  WHERE Insced = '".$wcedula_ppal."'";
	$q_cancela_ins = mysql_query($q_cancela_ins,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cancela_ins." - ".mysql_error());
	
	$datamensaje['mensaje'] = "Ha sido borrada su inscripción";
		
	echo json_encode($datamensaje);
    return;

}	

if(isset($consultaAjax))
	{
	
	switch($consultaAjax){

		case 'inscribirse':  
					{
					echo inscribirse($wemp_pmla, $wusuario, $wcedula, $wcentro_costos, $wtipo_inscripcion, $wclave_del_ppal, $wclave_del_suplente, $wdatos_suplente, $wnombre_suplente, $wempresa_asociado, $wempresa_real, $aplicacion, $wconsecutivo_votacion);
					}					
		break;
		
		case 'cancelar_inscripcion':
				{
				echo cancelar_inscripcion($wemp_pmla, $wcedula_ppal);
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
<title>Inscripcion de candidatos</title>
<meta charset="utf-8">
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<style type="text/css">
    .imagen {
		height: auto;
		width: 250px;
}
	
	.border1{
	-moz-border-radius: 1em;
	-webkit-border-radius: 1em;
	border-radius: 1em;
	}
	
	#div_tabla{
	margin: 2em 0;
	height: auto;
	text-align:center;
	padding: 10px;
	width: 20%;
	border: #CCCCCC solid 2px;
	background: #FFFFFF;
	}
	
	hr {border: 0; height: 12px; box-shadow: inset 0 12px 12px -12px #000000;}
</style>
<script type="text/javascript">

function cerrarVentana(){
  window.close();	
 }

function limpiarbusqueda(){

 $.blockUI({ message:	'Cargando...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

$('#id_search').val('');
$('#suplentes').val(' '); 


location.reload();


}

function pulsar(e) {
	tecla=(document.all) ? e.keyCode : e.which;
  if(tecla==13) return false;
}


function inscribirse(wempresa_asociado, wusuario, wcedula, wtipo_inscripcion, wcentro_costos, wempresa_real, aplicacion, wconsecutivo_votacion)
{
		
		var clave_del_ppal = $('#clave_del_ppal').val();
		var clave_del_suplente = $('#clave_del_suplente').val();
		var datos_suplente = $('#suplentes').val();
		var nombre_suplente = $('#suplentes option:selected').html();
		var wemp_pmla = $('#wemp_pmla').val();
		
		$.post("inscripcion.php",
				{
					consultaAjax:       		'inscribirse', 
					wemp_pmla:					wemp_pmla,
					wempresa_asociado:			wempresa_asociado,
					wusuario:					wusuario,
					wcedula:					wcedula,
					wcentro_costos:				wcentro_costos,
					wtipo_inscripcion:			wtipo_inscripcion,
					wclave_del_ppal:			clave_del_ppal,
					wclave_del_suplente:		clave_del_suplente,
					wdatos_suplente:			datos_suplente,
					wnombre_suplente:			nombre_suplente,
					wempresa_real:				wempresa_real,
					aplicacion:					aplicacion,
					wconsecutivo_votacion:		wconsecutivo_votacion					

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						alert(data_json.mensaje);
						return;
					}
					else
					{       
						
						 $('#accion_inscripcion').hide();
						 $('#mensaje_inscrito').append(data_json.mensaje_html);						
						alert(data_json.mensaje);
						$('#form_contrasenas').dialog('close');
					
				
					}

			},
			"json"
		);
	
}



function abrir_modal_claves(){
	
	var ced_cod_suplente = $('#suplentes').val();
	
	if(ced_cod_suplente == ''){
		
		alert('Debe seleccionar un suplente de la lista.');
		return;
		}
	
	$("#form_contrasenas").dialog( "open" );

	}

	
function cancelar_inscripcion(wemp_pmla, cedula_ppal)	{


		$.blockUI({ message:	'Cargando...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
		
		$.post("inscripcion.php",
				{
					consultaAjax:       		'cancelar_inscripcion', 
					wemp_pmla:					wemp_pmla,
					wcedula_ppal:				cedula_ppal
					

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						alert(data_json.mensaje);
						return;
					}
					else
					{       
															
						alert(data_json.mensaje);						
						location.reload();
				
					}

			},
			"json"
		);

}
$(document).ready( function () {

        //Permite que al escribir en el campo buscar, se filtre la informacion
		$('input#id_search').quicksearch('#suplentes option');		
			
		$("#form_contrasenas" ).dialog({
				show: {
				effect: "blind",
				duration: 100
				},
				hide: {
				effect: "blind",
				duration: 100
				},
				autoOpen: false,
				// maxHeight:600,
				height:'auto',				
				width: 'auto',
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Ingresar contraseñas "/*,
				open:function(){
				var s = $('#cont_dlle_modal').height();
				var s2 = $(this).dialog( "option", "maxHeight" );
				if(s < s2){
				$(this).height(s);
				}
				}*/
				});
		
			
        });


</script>
<BODY>
<?php

//==========================================================================================================================================
//PROGRAMA				      :Inscripcion de asociados para delegado.                                                                  
//AUTOR				          :Jonatan Lopez Aguirre.                                                                        
//FECHA CREACION			  :Enero 22 de 2014 
//-----------------------------------------------------------------------------------------------------------------------------------------            
// Actualizaciones
// 2016-02-16: Jessica Madrid Mejía 	- Se agrega empresa real y relacionada para validacion de contraseña del suplente.
//										- Se modifican la condicion Ideuse de la consulta principal que trae los datos del empleado
//											que se va a inscribir para que traiga el usuario correcto.
//-----------------------------------------------------------------------------------------------------------------------------------------                                                                

encabezado("Inscripcion de candidatos",$wactualiz, $wlogoempresa);



if($westado_inscripcion == 'on'){

foreach($wtalhumas as $key_a => $value){

// $q[] =  " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse "
					 // ."	  FROM ".$value['Detval'].""
					 // ."	 WHERE (Ideuse = '".$key2."-".$wemp_pmla1."'"
					 // ."     OR Ideuse = '".$key."' OR Ideuse = '".$key2."-".$key_a."')"
					 // ."    AND Ideest = 'on'"
					 // ."    AND Ideced != '' ";
		
$q[] =  " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse "
					 ."	  FROM ".$value['Detval'].""
					 ."	 WHERE (Ideuse = '".$key2."-".$wemp_pmla1."'"
					 ."     OR Ideuse = '".$key."')"
					 ."    AND Ideest = 'on'"
					 ."    AND Ideced != '' ";
					 
}


//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
$q = implode(" UNION ", $q);
	
$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

$row = mysql_fetch_assoc($res);

$genero = $row['Idegen'];
$wcedula = $row['Ideced'];
$wnombre1 = strtolower($row['Ideno1']);
$wnombre2 = strtolower($row['Ideno2']);
$wapeliido1 = strtolower($row['Ideap1']);
$wapellido2 = strtolower($row['Ideap2']);
$wcentro_costos = $row['Idecco'];

$num_res = mysql_num_rows($res);
if($num_res==0)
{
	foreach($wtalhumas as $key_a => $value){

		$q2[] =  " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse "
			 ."	  FROM ".$value['Detval'].""
			 ."	 WHERE (Ideuse = '".$key2."-".$key_a."')"
			 ."    AND Ideest = 'on'"
			 ."    AND Ideced != '' ";
	}
	
	$q2 = implode(" UNION ", $q2);
	$res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
	
	$row2 = mysql_fetch_assoc($res2);

	$genero = $row2['Idegen'];
	$wcedula = $row2['Ideced'];
	$wnombre1 = strtolower($row2['Ideno1']);
	$wnombre2 = strtolower($row2['Ideno2']);
	$wapeliido1 = strtolower($row2['Ideap1']);
	$wapellido2 = strtolower($row2['Ideap2']);
	$wcentro_costos = $row2['Idecco'];

}

//Traigo la informacion de la tabla de empleados ppal.
$q_asoc =   " SELECT Asoemp, Asoins, Asoemr "
		   ."   FROM ".$wbasedato."_000006 "
		   ."  WHERE Asoced = '".$wcedula."'"
		   ."    AND Asoest = 'on'";
$res_asoc = mysql_query($q_asoc,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asoc." - ".mysql_error());
$num_asoc = mysql_num_rows($res_asoc);

//Verificar si el usuario se encuentra asociado al fondo de empleados.
if($num_asoc == 0)
	{
	echo "<br>\n<br>\n".
        " <H1 align=center>No se encuentra asociado a este fondo.<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>";
	return;
	}

$row_asoc = mysql_fetch_array($res_asoc);
$wempresa_asociado = $row_asoc['Asoemp']; // Empresa del asociado de la tabla de asociados.
$wempresa_real = $row_asoc['Asoemr']; // Empresa real del asociado.
$wfecha_inscripcion = $row_asoc['Asoins']; // Fecha de inscripcion al fondo de empleados.


foreach($wtalhumas as $key_b => $value){

//Consulta todos los ppales con sus suplentes.
$query_ppales_y_delegado[] = " SELECT Insced, Insapr, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Insces
							     FROM ".$wbasedato."_000003, ".$value['Detval']."
							    WHERE Insced = Ideced
								  AND Insvot = '".$wconsecutivo_votacion."'
								  AND Insapl = '".$aplicacion."'
								  AND Insemp = '".$wempresa_asociado."'
 								  AND Insest = 'on'
								  AND Ideest = 'on'";
								
}

$query_ppales_y_delegado = implode(" UNION ", $query_ppales_y_delegado);								
$res_ppales_y_delegado = mysql_query($query_ppales_y_delegado); 

$array_ppales_y_delegado = array();

while($row_ppales_y_asoc = mysql_fetch_assoc($res_ppales_y_delegado))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_ppales_y_asoc['Insced'], $array_ppales_y_delegado))
        {
            $array_ppales_y_delegado[$row_ppales_y_asoc['Insced']] = array();
        }
		
		//Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_ppales_y_asoc['Insces'], $array_ppales_y_delegado))
        {
            $array_ppales_y_delegado[$row_ppales_y_asoc['Insces']] = array();
        }
		
    }

// echo "<pre>";	
// echo "<div>";	
// print_r($array_ppales_y_delegado);
// echo "</div>";	
// echo "<pre>";

//Consulta todos los asociados y su informacion personal.
foreach($wtalhumas as $key_c => $value){

$query_asociados[] = " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse,Asoemp,Asoemr
					     FROM ".$wbasedato."_000006, ".$value['Detval']."
					    WHERE Asoced = Ideced
						  AND Asoemp = '".$wempresa_asociado."'
						  AND Asoest = 'on'
						  AND Ideest = 'on'
						  AND Asoins <= '".$wfecha_max_insc_asoc."'";
}

$query_asociados = implode(" UNION ", $query_asociados);
$query_asociados = $query_asociados." ORDER BY Ideno1";
$res_asociados = mysql_query($query_asociados);

$array_asociados = array();
$array_asociados_todos = array();

//Se crea el areglo con los asociados de la empresa.
while($row_asociados = mysql_fetch_assoc($res_asociados))
    {
       
        if(!array_key_exists($row_asociados['Ideced'], $array_asociados))
        {
            $array_asociados[$row_asociados['Ideced']] = $row_asociados;
        }		
		
		//Se crea este array para tener la informacion de todos lo asociados, ya que al array_asociados se le eliminaran los que ya estan inscritos como ppales o suplentes.
        if(!array_key_exists($row_asociados['Ideced'], $array_asociados_todos))
        {
            $array_asociados_todos[$row_asociados['Ideced']] = $row_asociados;
        }
		
    }

	//Elimino del arreglo de asociados, los que ya estan inscrito como ppales, como delegados y el asociado que se encuentra en la interfaz.	
	unset($array_asociados[$wcedula]);
	foreach($array_ppales_y_delegado as $key_datos => $value){
	
		unset($array_asociados[$key_datos]);
		
	}
	
	
	
// echo "<pre>";	
// echo "<div>";	
// print_r($array_asociados_todos);
// echo "</div>";	
// echo "<pre>";	

$wempresa_real = ($wempresa_real != "") ? $wempresa_real : $wempresa_asociado;

$wentidad_aux = $institucion_aux->nombre;
$institucion_aux = consultarInstitucionPorCodigo($conex, $wempresa_real);
$wlogoempresa_ins = strtolower( $institucion_aux->baseDeDatos );

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
	
//Trae el de la empresa asociada al usuario.
$logo_empresa = "<img width='120' heigth='80' src='../../images/medical/root/".$wlogoempresa_ins.".jpg'>";

//Si no tiene logo asociado entonces pondra el de la clinica.
if(!file_exists($logo_empresa)){

$logo_empresa = "<img width='120' heigth='80' src='../../images/medical/root/clinica.jpg'>";

}

//Trae la foto del usuario.
$genero = ($genero == '') ? "M" : $genero;
$foto = '<img class="imagen" src="'.getFoto($conex,$wempresa_asociado,$wbasedato,$key2,$wcedula,$genero).'"/>';

//Centro de costos asociado. 
$wnomcco = $array_empresas_cco[$wempresa_asociado][$wcentro_costos]['Cconom'];		
//Si no esta con nombre lo busca con descripcion.
$wnomcco = ($wnomcco == '') ? $array_empresas_cco[$wempresa_asociado][$wcentro_costos]['Ccodes'] : $array_empresas_cco[$wempresa_asociado][$wcentro_costos]['Cconom'];
//Si no esta con nombre ni con descripcion, imprime la empresa.			
 if($wnomcco == ''){

	$wnomcco = $wempresas[$wempresa_asociado]['Empdes'];
}			

//Consulta si el usuario ya esta inscrito como principal o como suplente.
 $q_inscrito =  " SELECT Insapr, '01' as Instip, Insces as acompana"
			   ."   FROM ".$wbasedato."_000003"
			   ."  WHERE Insced = '".$wcedula."'"
			   ."    AND Insest = 'on'"
			   ."    AND Insvot = '".$wconsecutivo_votacion."'"
			   ."	 AND Insapl = '".$aplicacion."'"
			   ."  UNION "
			   ." SELECT Insapr, '02' as Instip, Insced as acompana"
			   ."   FROM ".$wbasedato."_000003"
			   ."  WHERE Insces = '".$wcedula."'"
			   ."    AND Insest = 'on'"
			   ."    AND Insvot = '".$wconsecutivo_votacion."'"
			   ."	 AND Insapl = '".$aplicacion."'";
$res_inscrito = mysql_query($q_inscrito,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_asociados." - ".mysql_error());
$num_inscrito = mysql_num_rows($res_inscrito);
$row_insc = mysql_fetch_array($res_inscrito);

$waprobado = $row_insc['Insapr'];
$wtipo_incripcion = $row_insc['Instip'];
$nombre_tipo = tipo_inscripcion($wtipo_incripcion);
$wced_ppal_o_suplente = $row_insc['acompana']; //Cedula del acompañanate, sea ppal u suplente.

$wdescripcion_tipo = ($nombre_tipo == '') ? $nombre_tipo = "Principal" : $nombre_tipo;

//Aqui valido que tipo de inscripcion tiene el acompañante.
if($wtipo_incripcion == '01'){
$wdescripcion_tipo_aux =  'suplente';
}else{
$wdescripcion_tipo_aux = 'principal';
}

$wnombre_suplente_o_ppal = $array_asociados_todos[$wced_ppal_o_suplente]['Ideno1']." ".$array_asociados_todos[$wced_ppal_o_suplente]['Ideno2']." ".$array_asociados_todos[$wced_ppal_o_suplente]['Ideap1']." ".$array_asociados_todos[$wced_ppal_o_suplente]['Ideap2']."";
$wempresas = empresas();


echo "<form id='form_inscripcion'>"; 
echo "<input type=hidden id='wemp_pmla' value= '".$wemp_pmla."'></input>";		
echo "<center>";
echo "<div id='div_tabla' class='border1'>";
	echo "<div>".$logo_empresa."</div>";     //Logo
	echo "<div>&nbsp;</div>";     			 //Espacio
	echo "<div>".$foto."</div>";    		 //Foto
	echo "<div><hr></div>";					 //Separador
	echo "<div style='font-size:35px; color:#13189F;'>".ucfirst($wnombre1)." ".ucfirst($wnombre2)."</div>"; //Nombre 1
	echo "<div style='font-size:20px; color:#13189F;'>".ucfirst($wapeliido1)." ".ucfirst($wapellido2)."</div>";  //Apellidos
	echo "<div style='font-size:15px; color:#D02114;'>".utf8_encode(ucwords($wempresas[$wempresa_real]['Empdes']))."</div>"; 		//Empresa del asociado
	echo "<div style='font-size:15px; color:#D02114;'>".ucwords($wnomcco)."</div>"; 		//Centro de costos asociado.
echo "</div>";


$visible = ''; //Esta variable controla el mensaje de la inscripcion, cambia de valor si el usuario esta inscrito como ppla o suplente.
$wboton_cancelar_insc = ''; //Inicializo la variable, esta se reasignara si el asociado esta inscrito y es ppal.

//Valida si el asociado lleva mas de seis meses en el fondo.
if($wfecha_inscripcion <= $wfecha_max_insc_asoc ){
	
	//Si esta inscrito con perfil ppal o suplente ingresa por aqui.
	if($num_inscrito > 0){
				
			if($waprobado == 'off'){
			
				$text_boton = "";
				$texto = "Se ha preinscrito como aspirante a ".$wdescripcion_tipo.", <br>con <b>".utf8_encode($wnombre_suplente_o_ppal)." </b>como ".$wdescripcion_tipo_aux.".";
				$visible = "style='display:none'";
				
				//Si el asociaso es ppal, podra cancelar la inscipcion, ya que aun no se han aprobado la inscripcion.
				if($wtipo_incripcion == '01'){
				$wboton_cancelar_insc = "<input type='button' id='inscribirse_ppal' value='Cancelar Inscripción' onclick='cancelar_inscripcion(\"$wemp_pmla\",\"$wcedula\")' >";
				}
				
				}else{
				
				$text_boton = "";
				$texto = "Se ha inscrito como aspirante a ".$wdescripcion_tipo.", <br>con <b>".utf8_encode($wnombre_suplente_o_ppal)." </b>como ".$wdescripcion_tipo_aux.".";
				$visible = "style='display:none'";			
				
				}
						

	}else{

		$text_boton = "<input type='button' id='inscribirse_ppal' value='Hacer inscripción principal y suplente' onclick='abrir_modal_claves()' >";
			
		}
}else{
			
			$text_boton = "";
			$texto = "No se puede inscribir ya que su fecha de afiliacion es menor a seis meses.";
			$visible = "style='display:none'";
			
			}

$text_boton = ($westado_inscripcion == 'on') ? $text_boton : "Se han cerrado las inscripciones";

echo "<br>";
echo "<div id='form_contrasenas' style='display:none;' title='Ingresar contraseñas'>		
		<table border='0' >
		  <tbody>
			<tr>
			  <td align=center class=encabezadotabla>Contraseña del principal</td>
			  <td></td>
			  <td align=center class=encabezadotabla>Contraseña suplente</td>
			</tr>
			<tr>
			  <td><input type='password' id='clave_del_ppal'></td>
			  <td></td>
			  <td><input type='password' id='clave_del_suplente'></td>
			</tr>
			<tr>
			  <td align=center colspan='3' rowspan='1'><input type='button' id='inscribirse_ppal' value='Inscribirse' onclick='inscribirse(\"$wempresa_asociado\", \"$key\", \"$wcedula\",\"01\", \"$wcentro_costos\", \"$wempresa_real\", \"$aplicacion\", \"$wconsecutivo_votacion\")' /></td>
			</tr>
		  </tbody>
		</table>					
	  </div>";

echo "<table border='0' id='accion_inscripcion' ".$visible.">
	  <tbody>
		<tr>
		  <td colspan='1' rowspan='1' align=center>
		  <table>
			<tr>
			<td class=encabezadotabla>Buscar suplente:</td>	
			<td class=encabezadotabla><input id='id_search' type='text' value='' onkeypress='return pulsar(event);' ></td>			
			<td><img width='15' height='15' border='0' onclick='limpiarbusqueda();' title='Reiniciar Búsqueda' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></td>
			</tr>
		  </table>		 
		</tr>
		<tr>
		  <td colspan='1' rowspan='1'>&nbsp;</td>
		</tr>
		<tr>
		  <td colspan='1' rowspan='1' align=center class=fila1>Seleccionar suplente:</td>
		</tr>
		<tr>
		  <td colspan='1' rowspan='1' align=center><select id='suplentes' >";
			echo "<option value = ''></option>";

			foreach($array_asociados as $key => $asociados){
				
				//Si tiene segundo apellido lo imprime.
				$asociados['Ideno2'] = ($asociados['Ideno2'] != '') ? $asociados['Ideno2'] : "";

				// echo "<option value=".$asociados['Ideced']."|".$asociados['Ideuse']."|".$wempresa_asociado."|".$asociados['Idecco'].">".utf8_encode($asociados['Ideno1']." ".$asociados['Ideno2']." ".$asociados['Ideap1']." ".$asociados['Ideap2'])."</option>";
				
				//Se agrega empresa real del asociado suplente
				echo "<option value=".$asociados['Ideced']."|".$asociados['Ideuse']."|".$wempresa_asociado."|".$asociados['Idecco']."|".$asociados['Asoemp']."|".$asociados['Asoemr'].">".utf8_encode($asociados['Ideno1']." ".$asociados['Ideno2']." ".$asociados['Ideap1']." ".$asociados['Ideap2'])."</option>";
				
			}

			echo "</select>
		</td>
		</tr>
		<tr>
		  <td colspan='1' rowspan='1'>&nbsp;</td>
		</tr>
		
		<tr>
		  <td colspan='1' rowspan='1' align=center>".$text_boton."</td>
		</tr>		
		<tr>
		  <td colspan='1' rowspan='1'></td>
		</tr>
		
	  </tbody>
	</table>";
	
echo "<div id='mensaje_inscrito'>".$texto."</div>"; //Muestra el texto de que se ha preinscrito o inscrito.
echo "<div ><p>".$wboton_cancelar_insc."</p></div>"; //Muestra el boton de cancelar inscripción.


echo "</form>";	
}else{

echo "<br>\n<br>\n".
        " <H1 align=center>No hay inscripciones abiertas en este momento.<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>";

}

echo "<center>
	<div><br><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";


?>
</BODY>
</html>
