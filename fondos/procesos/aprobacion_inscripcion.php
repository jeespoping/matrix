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

$wactualiz="Marzo 2 de 2016";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
mysql_select_db("matrix") or die("No se selecciono la base de datos");

$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wtalhumas = traer_talumas($conex, "informacion_empleados"); //Trae todas las empresas que tengan tablas de talento humano.
$wbasedato = aplicacion($conex, $wemp_pmla, "votaciones" ); //Trae el nombre para el contro de la base de datos del fondos de empleados.
$wtabcco = aplicacion($conex, $wemp_pmla, 'tabcco');
$wlogoempresa = strtolower( $institucion->baseDeDatos );
$wusuario = substr($user,2,7);


//Configuracion de la votacion
$q_conf_vot =    " SELECT * "
				."   FROM ".$wbasedato."_000001 "
			    ."	WHERE Votapl = '".$aplicacion."' $filtro_caracteristica "
			    ."	  AND Votest = 'on'";
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
	
if(count($array_conf_vot) > 1){
	
	echo "Imprimir select con el filtro de las votaciones activas Elegir eleccion.";
}else{
	
	foreach($array_conf_vot as $key => $value){
		
		$cod_vot = $key;
		
	}
}
	
	
//print_r($array_conf_vot);
//Temas asociados a la votacion
$q_temas =   " SELECT * "
			."   FROM ".$wbasedato."_000002 "
			."	WHERE Temapl = '".$aplicacion."'"			
			."	  AND Temest = 'on'";
$res_temas = mysql_query($q_temas,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_temas." - ".mysql_error());

$array_temas = array();

	while($row_temas = mysql_fetch_assoc($res_temas))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_temas['id'], $array_temas))
        {
            $array_temas[$row_temas['id']] = $row_temas;
            $array_temas_aux[$row_temas['id']] = $row_temas;
        }

    }


$wano_votacion = $array_conf_vot[$cod_vot]['Votapl'];
$wconsecutivo_votacion = $array_conf_vot[$cod_vot]['Votcod'];
$westado_votaciones = $array_conf_vot[$cod_vot]['Votest'];
$wcontrol_voto_blanco_est = $array_conf_vot[$cod_vot]['Votbla'];
$wcontrol_suplente_est = $array_conf_vot[$cod_vot]['Votsup'];
$westado_inscripciones = $array_conf_vot[$cod_vot]['Votein'];
$wcon_inscripcion = $array_conf_vot[$cod_vot]['Vothin'];

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
            $array_ccos[] = $row;
        }
		
    }
	
	
	return $array_ccos;
}

function aplicacion($conex, $wemp_pmla, $aplicacion){

	
	 $q =  " SELECT Detval "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$waplicacion = $row['Detval'];

	return $waplicacion;
}


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


function cambiar_estado($wemp_pmla, $wbasedato, $westado, $wcedula, $aplicacion, $wconsecutivo_votacion){

	global $conex;
	global $wbasedato;
	global $wusuario;
	global $aplicacion;
	global $wconsecutivo_votacion;	
	
	$wfecha = date("Y-m-d");
    $whora  = date("H:i:s");
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'color'=>'');
	
	
	$q_valida =    " SELECT Votced "
				  ."   FROM ".$wbasedato."_000005"
				  ."  WHERE Votced = '".$wcedula."'"
				  ."	AND Votapl = '".$aplicacion."'"
				  ."	AND Votvot = '".$wconsecutivo_votacion."'"
				  ."	AND Votest = 'on'";
	$res_valida = mysql_query($q_valida,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_valida." - ".mysql_error());
	$num_valida = mysql_num_rows($res_valida);
	
	if($num_valida > 0){
	
		$datamensaje['mensaje'] = "El asociado con su suplente no se pueden desaprobar porque tienen votos activos asociados.";
		$datamensaje['error'] = 1;
		
	}else{
	
	
	$q =  " UPDATE ".$wbasedato."_000003"
		 ."	   SET Insapr = '".$westado."'"
		 ."  WHERE Insced = '".$wcedula."'"
		 ."	   AND Insapl = '".$aplicacion."'"
		 ."	   AND Insvot = '".$wconsecutivo_votacion."'"
		 ."	   AND Insest = 'on'";
	$res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	$wfilas_afectadas = mysql_affected_rows();
	
	$datamensaje['color'] = ($westado == 'on') ? "#90F8A5" : "";	
	
	}
		
	 echo json_encode($datamensaje);
     return;
}


function guardar_observaciones($wemp_pmla, $wbasedato, $wobservaciones, $wcedula, $aplicacion, $wconsecutivo_votacion){

	global $conex;
	global $wbasedato;
	global $wusuario;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');
	
	
	echo $q = " UPDATE ".$wbasedato."_000003"
		 ."	   SET Insobs = '".$wobservaciones."'"
		 ."  WHERE Insced = '".$wcedula."'"
		 ."	   AND Insapl = '".$aplicacion."'"
		 ."	   AND Insvot = '".$wconsecutivo_votacion."'";
	$res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

		
	 echo json_encode($datamensaje);
     return;
}


if(isset($consultaAjax))
	{
	
	switch($consultaAjax){
		
		case 'cambiar_estado':  
					{
					echo cambiar_estado($wemp_pmla, $wbasedato, $westado, $wcedula, $aplicacion, $wconsecutivo_votacion);
					}					
		break;
		
		case 'guardar_observaciones':  
					{
					echo guardar_observaciones($wemp_pmla, $wbasedato, $wobservaciones, $wcedula, $aplicacion, $wconsecutivo_votacion);
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
<title>Lista de candidatos</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

<style type="text/css">
    
</style>
<script type="text/javascript">

function ver_imagen_grande(){


}

function cambiar_estado(wemp_pmla, wbasedato, cedula, i, ano_inscripcion, consecutivo_votacion){
	
	
	 $.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
	
	
	var valor_estado = $('#estado_'+i).is(':checked');
	
	if(valor_estado == true){
		estado = 'on';
	}else{
		estado = 'off';
	}
		
	$.post("aprobacion_inscripcion.php",
				{
					consultaAjax:       		'cambiar_estado', 
					wemp_pmla:					wemp_pmla,					
					wbasedato:					wbasedato,
					westado:					estado,
					wcedula:					cedula,
					aplicacion:			ano_inscripcion,
					wconsecutivo_votacion:		consecutivo_votacion

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						$('#estado_'+i).prop('checked', true);						
						alert(data_json.mensaje);
						$.unblockUI();						
						return;
					}
					else
					{                   
					
					//alert(data_json.mensaje);
					$('#td_estado_'+i).css('background-color', data_json.color);
					  $.unblockUI();
					
					
					}

			},
			"json"
		);
}


function guardar_observaciones(wemp_pmla, wbasedato, cedula, i, aplicacion, wconsecutivo_votacion){
	
	
	var observaciones = $('#observaciones_'+i).val();
		
	$.post("aprobacion_inscripcion.php",
				{
					consultaAjax:       		'guardar_observaciones', 
					wemp_pmla:					wemp_pmla,					
					wbasedato:					wbasedato,
					wobservaciones:				observaciones,
					wcedula:					cedula,
					aplicacion:					aplicacion,
					wconsecutivo_votacion:		wconsecutivo_votacion

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					 
					}
					else
					{                   
					
					//alert(data_json.mensaje);	
					// $('#div_estado_'+i).hide();
					// $('#estado_'+i).show();
					// $('#div_estado_'+i).removeAttr("style");
                    // $('#div_estado_'+i).html('');	
					
					}

			},
			"json"
		);
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
           
			//Permite que al escribir en el campo buscar, se filtre la informacion del grid
			$('input#id_search').quicksearch('div#div_asociados_inscritos table tbody .find');
			
			$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			
			$('.lightbox_ppal').click(function() {
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');								
				$.blockUI({ 
					message: $('#'+id), 
					css: { 
						top:  ($(window).height() - 700) /2 + 'px', 
						left: ($(window).width() - 700) /2 + 'px', 
						width: '400px'
					} 
				}); 
				
				$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
			});

			$('.lightbox_suplente').click(function() {
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');								
				$.blockUI({ 
					message: $('#'+id), 
					css: { 
						top:  ($(window).height() - 700) /2 + 'px', 
						left: ($(window).width() - 700) /2 + 'px', 
						width: '400px'
					} 
				}); 
				
				$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
			});
				
        });


</script>
<Body>
<?php

//==========================================================================================================================================
//PROGRAMA				      :Aprobacion de inscripcion para delegados.                                                                   
//AUTOR				          :Jonatan Lopez Aguirre.                                                                        
//FECHA CREACION			  :Enero 31 DE 2014
//FECHA ULTIMA ACTUALIZACION  :Enero 31 DE 2014                                                                             


encabezado("Lista de candidatos",$wactualiz, $wlogoempresa);

//================================================================================
// Marzo 2 de 2016 Jessica 		- Se modifica la condicion para habilitar la aprobación para que funcione cuando las inscripciones y votaciones esten cerradas
// Julio 20 de 2014 Jonatan
// Se valida si debe aparecer la informacion del suplente segun las variables $wcontrol_suplente_cons $wcontrol_suplente_est de la root_000051.



$westado_checkbox = '';
$westado_observa = '';

//Si el parametro de consulta esta en la url, los checkbox de aprobacion estaran inactivos.
if(isset($wconsulta) and $wconsulta=='on'){
// $westado_inscripcion = 'off';
$westado_inscripcion = 'on';
$westado_votacion == 'on';
}

//Si las inscripciones se cerraron, ya no se podran aprobar o desaprobar inscritos, ademas el campo de observaciones sera de solo lectura.
// if($westado_inscripcion == 'off'){
//Si las inscripciones o votaciones estan abiertas no se podran aprobar o desaprobar inscritos.
if($westado_inscripcion == 'on' || $westado_votacion == 'on'){

$westado_checkbox = "disabled";
//$westado_observa = "readonly";

}
$query_inscritos = array();

//Busco todos los asociados inscritos como principales, teniendo en cuenta las tablas de talhuma, la variable $wtalhumas se encuentra declarada como global en la parte superior del codigo.
foreach($wtalhumas as $key => $value){

$query_inscritos[] = "SELECT Insced, Insapr, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insobs, Insemp, Inscco, Insesu, Insccs, ".$wbasedato."_000003.Fecha_data as fecha_registro, ".$wbasedato."_000003.Hora_data as hora_registro
					   FROM ".$wbasedato."_000003, ".$value['Detval']."
				      WHERE Insced = Ideced					    
						AND Insapl = '".$aplicacion."'
						AND Insvot = '".$wconsecutivo_votacion."'
						AND Insest = 'on'
						AND Ideest = 'on'";

}
//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
$query_consultas = implode(" UNION ", $query_inscritos);
//Se agrega order by por fecha de registro y hora
$query_consultas = $query_consultas." ORDER BY fecha_registro, hora_registro DESC";
//Ejecuto la consulta final construida de forma dinamica.
$res_inscritos = mysql_query($query_consultas,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_consultas." - ".mysql_error());

$array_inscritos = array();

while($row_inscritos = mysql_fetch_assoc($res_inscritos))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_inscritos['Insced'], $array_inscritos))
        {
            $array_inscritos[$row_inscritos['Insced']] = $row_inscritos;
        }
		
    }

	// echo "<pre>";
 // print_r($array_inscritos);
 // echo "</pre>";


$query_suplentes = array();
//Busco todos los asociados inscritos como suplentes, teniendo en cuenta las tablas de talhuma, la variable $wtalhumas se encuentra declarada como global en la parte superior del codigo.
foreach($wtalhumas as $key => $value){

$query_suplentes[] = "SELECT Insced, Insapr, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insobs, Insemp, Inscco, Insesu, Insccs
					   FROM ".$wbasedato."_000003, ".$value['Detval']."
				      WHERE Insces = Ideced
						AND Insapl = '".$aplicacion."'
						AND Insvot = '".$wconsecutivo_votacion."'
					    AND Insest = 'on'
						AND Ideest = 'on'";

}

//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
$query_suplentes = implode(" UNION ", $query_suplentes);
//Ejecuto la consulta final construida de forma dinamica.
$res_suplentes = mysql_query($query_suplentes,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_suplentes." - ".mysql_error());

$array_suplentes = array();

while($row_suplentes = mysql_fetch_assoc($res_suplentes))
    {
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_suplentes['Insces'], $array_suplentes))
        {
            $array_suplentes[$row_suplentes['Insces']] = $row_suplentes;
        }
		
    }

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
	

$wempresas = empresas(); //Array de empresas, la variable $wempresas se encuentra declarada como global en la parte superior del codigo.	
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

//=========================================== IMPRESION DE LOS INSCRITOS =======================================================	
echo "<br><br><br>";
echo "<form id='form_inscritos'>"; 
echo "<center>";
echo "<table>";
echo "<tr>";
echo "<td class=encabezadotabla>Buscar</td>";		
echo "<td class=encabezadotabla><input id='id_search' type='text' value='' name='search'></td>";			
echo "<td><img width='auto' width='15' height='15' border='0' onclick='limpiarbusqueda();' title='Reiniciar Búsqueda' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></td>";
echo "</tr>";
echo "</table>";
echo "<center>";
echo "<br><br>";		
echo "<div id='div_asociados_inscritos' style='width: auto;'>"; 	

echo "<center><table border=0 cellspacing=1 >";
echo "<thead>";

//Controlo si se muestra la informacion del suplente.
	if($wcontrol_suplente_est == 'on'){
		
		$encabe_suplente = "<td align=center>Suplente</td>";
	}else{

	$encabe_suplente = "";		
	
	}

echo "<tr class=encabezadoTabla>	
		<td align=center>Principal</td>
		$encabe_suplente
		<td align=center>Nombre</td>
		<td align=center>Apellidos</td>
		<td align=center>Empresa</td>
		<td align=center>Area</td>
		<td align=center>Observaciones</td>
		<td align=center>Aprobar</td>
	</tr>";
echo "</thead>";

$i=1;

foreach ($array_inscritos as $key => $value) {
		
		($class == "#C3D9FF" ) ? $class = "#E8EEF7" : $class = "#C3D9FF";	//Cambio de color de fondo de cada linea.
		($class_td_checkbox == "#C3D9FF" ) ? $class_td_checkbox = "#E8EEF7" : $class_td_checkbox = "#C3D9FF"; //Cambio de color de fondo del cajon de chequeo, dependiendo de la linea.
		$checked = ''; //Controla el valor los checkbox.
		
		//Si esta aprobado el registro el cajon se chequea y el fondo queda verde.
		if($value['Insapr'] == 'on'){
		
			$checked = "checked";
			$class_td_checkbox = "#90F8A5"; //Color verde
			
		}	
		
		$genero_ppal = ($value['Idegen'] == '') ? "M" : $value['Idegen'];
		$genero_suplente = ($array_suplentes[$value['Insces']]['Idegen'] == '') ? "M" : $array_suplentes[$value['Insces']]['Idegen'];
		
		//Traer la foto del asociado ppal
		$foto_ppal = '<img class="imagen lightbox_ppal" id=fotografia_ppal_'.$i.' width=90px height=100px src="'.getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insced'],$genero_ppal).'"/>';
		
		//Traer la foto del suplente
		$foto_suplente = '<img class="imagen lightbox_suplente" id=fotografia_suplente_'.$i.' width=90px height=100px src="'.getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insces'],$genero_suplente).'"/>';
		
		$wcentro_costos_ppal = $array_empresas_cco[$value['Insemp']][$value['Inscco']]['Cconom'];		
		$wcentro_costos_ppal = ($wcentro_costos_ppal == '') ? $array_empresas_cco[$value['Insemp']][$value['Inscco']]['Ccodes'] : $array_empresas_cco[$value['Insemp']][$value['Inscco']]['Cconom'];
		
		if($wcentro_costos_ppal == ''){
		
			$wcentro_costos_ppal = $wempresas[$value['Insemp']]['Empdes'];
		}
		
		
		$wcentro_costos_suple = $array_empresas_cco[$value['Insemp']][$value['Insccs']]['Cconom'];		
		$wcentro_costos_suple = ($wcentro_costos_suple == '') ? $array_empresas_cco[$value['Insemp']][$value['Inscco']]['Ccodes'] : $array_empresas_cco[$value['Insemp']][$value['Insccs']]['Cconom'];
		
		if($wcentro_costos_suple == ''){
		
			$wcentro_costos_suple = $wempresas[$value['Insesu']]['Empdes'];
		}
		
		//Si el asociado ppal tiene datos en empresa real en la tabla de asociados (fondos_000006 - Asoemr), entonces pondra esa en el reporte, sino, pondra la empresa que se encuentra en el campo Asoemp.
		if($array_asociados[$value['Insced']]['Asoemr'] != '' and $array_asociados[$value['Insced']]['Asoemr'] != 'NO APLICA'){		
		$wempresa_ppal = $wempresas[$array_asociados[$value['Insced']]['Asoemr']]['Empdes'];
		$wcentro_costos_ppal = $wempresas[$array_asociados[$value['Insced']]['Asoemr']]['Empdes']; //Esta variable se reasignara en caso de que el asociado tenga datos en empresa real.
		}else{
		$wempresa_ppal = $wempresas[$array_asociados[$value['Insced']]['Asoemp']]['Empdes'];
		}
		
		//Si el asociado suplente tiene datos en empresa real en la tabla de asociados (fondos_000006 - Asoemr), entonces pondra esa en el reporte, sino, pondra la empresa que se encuentra en el campo Asoemp.
		if($array_asociados[$value['Insces']]['Asoemr'] != '' and $array_asociados[$value['Insces']]['Asoemr'] != 'NO APLICA'){		
		$wempresa_suplente = $wempresas[$array_asociados[$value['Insces']]['Asoemr']]['Empdes'];
		$wcentro_costos_suple = $wempresas[$array_asociados[$value['Insces']]['Asoemr']]['Empdes'];	//Esta variable se reasignara en caso de que el asociado tenga datos en empresa real.
		}else{
		$wempresa_suplente = $wempresas[$array_asociados[$value['Insces']]['Asoemp']]['Empdes'];		
		}	
		
		//Si las variables $wempresa_ppal o $wempresa_suplente no tienen datos, entonces pondra en pantalla la empresa con la que se inscribio (Esto aplica para el voto en blanco)
		$wempresa_ppal = ($wempresa_ppal != "") ? $wempresa_ppal : $wempresas[$value['Insemp']]['Empdes'];			
		$wempresa_suplente = ($wempresa_suplente != "") ? $wempresa_suplente : $wempresas[$value['Insesu']]['Empdes'];
		
		//Controlo si se muestra la informacion del suplente.
		if($wcontrol_suplente_est == 'on'){
	
		
		$dato_foto_suplente = " <td >".$foto_suplente." <img class='fotografia_suplente_".$i."' id='imagen_grande_suplente_".$i."' src='".getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insces'],'M')."' style='display:none' /></td>";
		$dato1_suplente = "<br><br> <b>Suplente:</b> <br>".utf8_encode($array_suplentes[$value['Insces']]['Ideno1'])." ".utf8_encode($array_suplentes[$value['Insces']]['Ideno2'])."";
		$dato2_suplente = "<br><br><b>Suplente:</b> <br> ".utf8_encode($array_suplentes[$value['Insces']]['Ideap1'])." ".utf8_encode($array_suplentes[$value['Insces']]['Ideap2'])."";
		$emp_suplente = "<br><br><b>Suplente:</b><br>".utf8_encode($wempresa_suplente)."";
		$cco_suplente = "<br><br><b>Suplente:</b><br> ".utf8_encode($wcentro_costos_suple)."";
		
		}
		
		//Impresion de los datos del asociado ppal y el suplente.
		echo "<tr class='".$class." find'>			  
			  <td >".$foto_ppal." <img class='fotografia_ppal_".$i."' id='imagen_grande_ppal_".$i."' src='".getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insced'],$value['Idegen'])."' style='display:none' /></td>
			  $dato_foto_suplente
			  <td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b> <br>".utf8_encode($value['Ideno1'])." ".utf8_encode($value['Ideno2'])." $dato1_suplente </td>
			  <td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($value['Ideap1'])." ".utf8_encode($value['Ideap2'])."  $dato2_suplente </td>
			  <td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($wempresa_ppal)." $emp_suplente</td>
			  <td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($wcentro_costos_ppal)." $cco_suplente</td>
			  <td nowrap style='background-color: ".$class.";'><textarea id='observaciones_".$i."' ".$westado_observa." onchange='guardar_observaciones(\"$wemp_pmla\",\"$wbasedato\",\"".$value['Insced']."\",\"$i\",\"$aplicacion\",\"$wconsecutivo_votacion\");'>".$value['Insobs']."</textarea></td>			 
			  <td nowrap style='background-color: ".$class_td_checkbox.";' align=center id='td_estado_$i' '><input type=checkbox id='estado_$i' ".$westado_checkbox." onclick='cambiar_estado(\"$wemp_pmla\",\"$wbasedato\",\"".$value['Insced']."\",\"$i\",\"$aplicacion\",\"$wconsecutivo_votacion\");' ".$checked."></td>
			</tr>";
			
		$i++;
		
    }

echo "</table>";
echo "</div>";
echo "<br><br>";

//Inscritos no aprobados
 $q_inscritos =  " SELECT count(Insced) as inscritos"
				."   FROM ".$wbasedato."_000003"
				."  WHERE Insapl = '".$aplicacion."'"
				."    AND Insvot = '".$wconsecutivo_votacion."'"
				."	  AND Insest = 'on'";
 $res_inscritos = mysql_query($q_inscritos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_inscritos." - ".mysql_error());
 $row_inscritos = mysql_fetch_array($res_inscritos);


//Inscritos aprobados
 $q_inscritos_apr =  " SELECT count(Insced) as aprobados "
					."   FROM ".$wbasedato."_000003"
					."	WHERE Insapr = 'on'"
					."    AND Insapl = '".$aplicacion."'"
					."    AND Insvot = '".$wconsecutivo_votacion."'"
					."	  AND Insest = 'on' ";
 $res_inscritos_apr = mysql_query($q_inscritos_apr,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_inscritos_apr." - ".mysql_error());
 $row_inscritos_apr = mysql_fetch_array($res_inscritos_apr);
 
 
 //Inscritos no aprobados
  $q_inscritos_no_apr =  " SELECT count(Insced) as noaprobados"
						."   FROM ".$wbasedato."_000003"
						."	WHERE Insapr = 'off'"
						."    AND Insest = 'on' ";
 $res_inscritos_no_apr = mysql_query($q_inscritos_no_apr,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_inscritos_no_apr." - ".mysql_error());
 $row_inscritos_no_apr = mysql_fetch_array($res_inscritos_no_apr);

echo "<center>";
echo "<table border='0'>
		  <tbody>
			<tr class=encabezadoTabla>
			  <td colspan='2' rowspan='1'>Estadisticas</td>
			</tr>
			<tr class=fila1>
			  <td>Inscritos:</td>
			  <td>".$row_inscritos['inscritos']."</td>
			</tr>
			<tr class=fila2>
			  <td>Aceptados:</td>
			  <td>".$row_inscritos_apr['aprobados']."</td>
			</tr>
			<tr class=fila1>
			  <td>No aceptados:</td>
			  <td>".$row_inscritos_no_apr['noaprobados']."</td>
			</tr>			
		  </tbody>
		</table>";
echo "</center>";
echo "<br><br>";
 
echo "<center>
	<div><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";
echo "</form>";	
?>
</BODY>
</html>
