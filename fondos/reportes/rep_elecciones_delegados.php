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

$wactualiz="Marzo 7 de 2016";

//==========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION                                                                                                                              \\
//=========================================================================================================================================\\
//En muestra el resultado de las votacion de delegados para la asamblea del fondo de empleados.                                      \\
//=========================================================================================================================================\\
//Julio 21 de 2014 (Jonatan Lopez)
//Se controla si se muestra la informacion del suplente con un parametro en la root_000051 ControlSuplenteVotaciones.
//=========================================================================================================================================\\
//Febrero 28 de 2014 (Jonatan Lopez)
//Se agrega el total de votos al final de cada empresa, ademas se muestra el cco de las empresas que no tienen nombre de cco.
//=========================================================================================================================================\\
//Marzo 7 de 2016
//Se corrige el reporte para que no aparezcan los empleados que no fueron seleccionados
//==========================================================================================================================================
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

$wano_votacion = $array_conf_vot[$cod_vot]['Votano'];
$wconsecutivo_votacion = $array_conf_vot[$cod_vot]['Votcod'];
$westado_votaciones = $array_conf_vot[$cod_vot]['Votest'];
$wcontrol_voto_blanco_est = $array_conf_vot[$cod_vot]['Votbla'];
$wcontrol_suplente_est = $array_conf_vot[$cod_vot]['Votsup'];
$westado_inscripciones = $array_conf_vot[$cod_vot]['Votein'];
$wcon_inscripcion = $array_conf_vot[$cod_vot]['Vothin'];


//Trae todas las tablas que contiene la informacion de talento humano de las empresas.
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


//Trae la cantidad de escaños por empresa, ordenados de menor a mayor
function traer_escanos_empresa($conex, $aplicacion){

	
	 $q =  " SELECT Detval, Detemp "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	$array_escanos = array();

while($row = mysql_fetch_assoc($res))
    {
		$array_posicion_escanos = explode("-",$row['Detval']);
		$wposicion = $array_posicion_escanos[0];
		$wescanos = $array_posicion_escanos[1];
		
        //Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row['Detemp'], $array_escanos))
        {
            $array_escanos[$row['Detemp']] = array('posicion'=>$wposicion, 'escanos'=>$wescanos);
        }
		
    }	
	
	asort($array_escanos);
	
	return $array_escanos;
}


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

//Trae una aplicacion de la tabla 51 de root.
function aplicacion($conex, $wemp_pmla, $aplicacion){

	
	 $q =  " SELECT Detval "
		  ."   FROM root_000051"
		  ."  WHERE Detapl = '".$aplicacion."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$waplicacion = $row['Detval'];

	return $waplicacion;
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

//Funcion que aprueba la inscipcion de los ppales con su suplente.
function cambiar_estado($wemp_pmla, $wbasedato, $westado, $wcedula){

	global $conex;
	global $wbasedato;
	global $wusuario;
	global $wano_votacion;
	global $wconsecutivo_votacion;
	
	$wfecha = date("Y-m-d");
    $whora  = date("H:i:s");
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'color'=>'');
	
	//Cambio de estado para la cedula inscrita como ppal.
	$q = " UPDATE ".$wbasedato."_000003"
		 ."	   SET Insapr = '".$westado."'"
		 ."  WHERE Insced = '".$wcedula."'"
		 ."    AND Insano = '".$wano_votacion."'"
		 ."    AND Insvot = '".$wconsecutivo_votacion."'";
	$res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	$wfilas_afectadas = mysql_affected_rows();
	
	//Cambio de color de fondo en el td del aprobado.
	$datamensaje['color'] = ($westado == 'on') ? "#90F8A5" : "";	
		
	 echo json_encode($datamensaje);
     return;
}

//Funcion que gusrda las observaciones.
function guardar_observaciones($wemp_pmla, $wbasedato, $wobservaciones, $wcedula){

	global $conex;
	global $wbasedato;
	global $wusuario;
	global $wano_votacion;
	global $wconsecutivo_votacion;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');	
	
	 $q = " UPDATE ".$wbasedato."_000003"
		 ."	   SET Insobs = '".$wobservaciones."'"
		 ."  WHERE Insced = '".$wcedula."'"
		 ."    AND Insano = '".$wano_votacion."'"
		 ."    AND Insvot = '".$wconsecutivo_votacion."'";
	$res = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		
	 echo json_encode($datamensaje);
     return;
}


if(isset($consultaAjax))
	{
	
	switch($consultaAjax){
		
		case 'cambiar_estado':  
					{
					echo cambiar_estado($wemp_pmla, $wbasedato, $westado, $wcedula);
					}					
		break;
		
		case 'guardar_observaciones':  
					{
					echo guardar_observaciones($wemp_pmla, $wbasedato, $wobservaciones, $wcedula);
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
<title>Resultados elecciones</title>
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

function validar_tema(){
	
	var dato_tema = $("#tema").val();
	
	if(dato_tema == ''){
		
		alert("Debe seleccionar un tema.");
		return;
	}else{
		document.forms["form_resultados_vot"].submit();
	}

}

//funcion js que cambia cambia el estado de la aprobacion.
function cambiar_estado(wemp_pmla, wbasedato, cedula, i){
	
	
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
					wcedula:					cedula

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					 
					}
					else
					{                   
					
					//alert(data_json.mensaje);
					$('#td_estado_'+i).css('background-color', data_json.color);
					
					
					}

			},
			"json"
		);
}

//Funcion que guarda las observaciones.
function guardar_observaciones(wemp_pmla, wbasedato, cedula, i){
	
	
	var observaciones = $('#observaciones_'+i).val();
		
	$.post("aprobacion_inscripcion.php",
				{
					consultaAjax:       		'guardar_observaciones', 
					wemp_pmla:					wemp_pmla,					
					wbasedato:					wbasedato,
					wobservaciones:				observaciones,
					wcedula:					cedula

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					 
					}
					else
					{   
					
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
			$('input#id_search').quicksearch('div#div_asociados_inscritos table .find');
			
			$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			
			//Permite que la foto se vea grande para el ppal.
			$('.lightbox_ppal').click(function() {
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');								
				$.blockUI({ 
					message: $('#'+id), 
					css: { 
						top:  ($(window).height() - 700) /2 + 'px', 
						left: ($(window).width() - 700) /2 + 'px', 
						width: 'auto'
					} 
				}); 
				
				$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
			});

			//Permite que la foto se vea grande para el suplente.
			$('.lightbox_suplente').click(function() {
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');								
				$.blockUI({ 
					message: $('#'+id), 
					css: { 
						top:  ($(window).height() - 700) /2 + 'px', 
						left: ($(window).width() - 700) /2 + 'px', 
						width: 'auto'
					} 
				}); 
				
				$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
			});
				
        });


</script>
<Body>
<?php

//==========================================================================================================================================
//PROGRAMA				      :Reporte de resultados para asamblea del fondo de empleados.                                                                   
//AUTOR				          :Jonatan Lopez Aguirre.                                                                        
//FECHA CREACION			  :Enero 31 de 2014
//FECHA ULTIMA ACTUALIZACION  :Enero 31 de 2014                                                                             


encabezado("Resultados elecciones",$wactualiz, $wlogoempresa);



//Si la variable $ver esta activa, entonces permitir ver los resultados, asi el estado de las votaciones se encuentre en off, 
//la variable $westado_votacion se reasignara a off para que se muetren los resultados.
if(isset($ver) and $ver == 'on'){

$westado_votacion = 'off';

}

if($westado_votacion == 'off'){
		
	
//Busco todos los asociados inscritos como principales, ademas cuenta los votos de cada uno, 
//teniendo en cuenta las tablas de talhuma; la variable $wtalhumas se encuentra declarada como global en la parte superior del codigo.
foreach($wtalhumas as $key => $value){

	$query_votados[] = " SELECT Insced, Insapr, Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse, Inscsu, Insces, Insobs, Insemp, Inscco, Insesu, Insccs, Votced, COUNT(Votced) as Votos, Asoemp, Asoemr
						   FROM ".$wbasedato."_000003, ".$wbasedato."_000005, ".$value['Detval'].", ".$wbasedato."_000006
						  WHERE Insced = Ideced
							AND Votced = Ideced
							AND Asoced = Votced
							AND Votemp = Insemp
							AND Votapl = '".$aplicacion."'
							AND Votvot = '".$wconsecutivo_votacion."'
							AND Votest = 'on'
							AND Insvot = '".$wconsecutivo_votacion."'
							AND Insest = 'on'
							AND Ideest = 'on'							
					   GROUP BY Votced 
				   HAVING COUNT(Votced) > 0
						  UNION
				   SELECT '00000000' as Insced, '' as Insapr, '' as Idegen, 'enblanco' as Ideced, 'VOTO' as Ideno1, 'EN' as Ideno2, 'BLANCO' as Ideap1, '' as Ideap2, '' as Idecco, '' as Ideccg, '' as Ideuse, '' as Inscsu, '' as Insces, '' as Insobs, '".$value['Detemp']."' as Insemp, '' as Inscco, '' as Insesu, '' as Insccs, Votced, COUNT(Votced) as Votos, '' as Asoemr, '' as Asoemr
						   FROM ".$wbasedato."_000005
						  WHERE Votapl = '".$aplicacion."'
							AND Votvot = '".$wconsecutivo_votacion."'
							AND Votemp = '".$value['Detemp']."'
							AND Votced = '00000000'
							AND Votest = 'on'
					   GROUP BY Votced 
				   HAVING COUNT(Votced) > 0";
	}

	//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
	$query_consultas = implode(" UNION ", $query_votados);

	//Se agrega order by por fecha de registro y hora
	if ($wvotos=='on')
	   $query_consultas = $query_consultas." ORDER BY Votos DESC";
	  else
         $query_consultas = $query_consultas." ORDER BY Votos DESC";	  
         // $query_consultas = $query_consultas." ORDER BY Insced DESC";	  
	
	

	//Ejecuto la consulta final construida de forma dinamica.
	$res_votados = mysql_query($query_consultas,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_consultas." - ".mysql_error());

	$array_votados_por_empresas = array();	

	while($row_votados = mysql_fetch_assoc($res_votados))
		{
			//Cre un arreglo de empresas, luego dentro de cada empresa agrego por los que han votado de ella.
			if(!array_key_exists($row_votados['Insemp'], $array_votados_por_empresas))
			{
				$array_votados_por_empresas[$row_votados['Insemp']] = array();
			}					
			
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_votados['Insced'], $array_votados_por_empresas[$row_votados['Insemp']]))
			{
				//Dentro de cada empresa agrego al ppal por el que votaron.
				$array_votados_por_empresas[$row_votados['Insemp']][$row_votados['Insced']] = $row_votados;
				
				
			}		
			
		}
	
	//rsort($array_votados_por_empresas);
	// echo "-------------------------";
	// echo "<pre>";
	 // print_r($query_consultas);
	 // // print_r($array_votados_por_empresas);
	 // echo "</pre>";	
	
	
	//====================================== Array principales ======================================================
	//Busco todos los asociados inscritos como principales, teniendo en cuenta las tablas de talhuma, la variable $wtalhumas se encuentra declarada como global en la parte superior del codigo.
	foreach($wtalhumas as $key => $value){

	$query_votantes[] = " SELECT Idegen, Ideced, Ideno1, Ideno2, Ideap1, Ideap2, Idecco, Ideccg, Ideuse
						   FROM ".$wbasedato."_000004, ".$value['Detval']."
						  WHERE Rvoced = Ideced
							AND Rvoapl = '".$aplicacion."'
							AND Rvovot = '".$wconsecutivo_votacion."'
							AND Rvoest = 'on'
							AND Ideest = 'on'";
	}

	//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
	$query_votantes = implode(" UNION ", $query_votantes);

	//Ejecuto la consulta final construida de forma dinamica.
	$res_votantes = mysql_query($query_votantes,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_votantes." - ".mysql_error());

	$array_votantes = array();

	while($row_votantes = mysql_fetch_assoc($res_votantes))
		{
			//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
			if(!array_key_exists($row_votantes['Ideced'], $array_votantes))
			{
				$array_votantes[$row_votantes['Ideced']] = $row_votantes;
			}
			
		}
		
	// echo "<pre>";
	// // print_r($array_votantes);
	// print_r($query_votantes);
	// echo "</pre>";
	
	//============================================ Array suplentes ===================================================
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
	 

	 // echo "<pre>";
	 // print_r($array_suplentes);
	 // echo "</pre>";	
	//============================= Arreglo de centros de costo por empresa ============================
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


	
	//=========================================== IMPRESION DE LOS RESULTADOS =======================================================	
	echo "<br><br><br>";
	echo "<form id='form_resultados_vot'>"; 
	echo "<center>";
	echo "<table>";
	echo "<tr>";
	echo "<td class=encabezadotabla>Buscar</td>";		
	echo "<td class=encabezadotabla><input id='id_search' type='text' value='' name='search'>
	<input id='wemp_pmla' type='hidden' value='$wemp_pmla' name='wemp_pmla'>
	<input id='aplicacion' type='hidden' value='$aplicacion' name='aplicacion'>
	<input id='ver' type='hidden' value='$ver' name='ver'></td>";			
	echo "<td><img width='auto' width='15' height='15' border='0' onclick='limpiarbusqueda();' title='Reiniciar Búsqueda' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></td>";
	echo "</tr>";
	echo "</table>";
	echo "<table>";
	echo "<tr><td align=center>TEMA</td></tr>";
	echo "<tr>";	
	echo "<td>";
	echo "<select id='tema' name=tema onchange='validar_tema();' >";	
	echo  "<option value=''></option>";
	if($tema !=''){
			echo  "<option value='$array_temas[$tema]' selected>".utf8_encode($array_temas[$tema]['Temdes'])."</option>";
	}
	unset($array_temas[$tema]);
	foreach($array_temas as $key => $value){
		
		echo  "<option value='".$value['id']."'>".utf8_encode($value['Temdes'])."</option>";
		
	}
	
	echo "</select>";
	
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	echo "<center>";
	echo "<br>";		
	echo "<div id='div_asociados_inscritos' style='width: auto;'>"; 

	if(!isset($tema)){	
		exit();
	}else{
		if($tema == ''){
			$filtro_tema_total = "";
		}else{
		    $filtro_tema_total = "	  AND Vottem = '".$tema."'";
		}
	}
	
	echo "<center>";
	echo "<br>";		
	echo "<div id='div_asociados_inscritos' style='width: auto;'>"; 

	//Votos totales en el ano y consecutivo activo.
	$q_votos =   " SELECT count(Votced) as votos_total"
				."   FROM ".$wbasedato."_000005"
				."  WHERE Votapl = '".$aplicacion."'"
				."  $filtro_tema_total "
				."    AND Votest = 'on'";
	$res_votos = mysql_query($q_votos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_votos." - ".mysql_error());
	$row_votos = mysql_fetch_array($res_votos);

	if ($wvotos=='on')             //Marzo 4 de 2014
	    {
		 echo "<center>";
		 echo "<table border='0'>
				  <tbody>			
					<tr>
					  <td style='font-size:14pt;'>Votos totales:</td>
					  <td style='font-size:14pt;'>".$row_votos['votos_total']."</td>
					</tr>
				  </tbody>
				</table>";
		 echo "</center>";
		 echo "<br>";	
	    }

	//Array de empresas, la variable $wempresas se encuentra declarada como global en la parte superior del codigo.
	$wempresas = empresas();
	$array_emp_posic_escanos = traer_escanos_empresa($conex,"NumeroEscanos"); //Areglo que contiene la empresa con su posicion y el numero de escaños.
	
	$dato_foto_suplente = "";
	$dato1_suplente = "";
	$dato2_suplente = "";
	$emp_suplente = "";
	$cco_suplente = "";
	
	echo "<center>";
	foreach ($array_emp_posic_escanos as $key => $array_posicion) {
			
			//Se verifica primero si la empresa existe en el arreglo de votados por empresa, si esta hace la impresion.
			if(array_key_exists($key,$array_votados_por_empresas)){
				
				//Array votados sera un nuevo array que contendra los votados por empresa.
				$array_votados = $array_votados_por_empresas[$key];
				// echo "<pre>";
				 // print_r($array_votados);
				 // echo "</pre>";	
				 
				echo "<table border=0 cellspacing=1 style='width:100%'>";
				echo "<thead>";
				echo "<tr class='encabezadoTabla find'><td style='width:100%; color:yellow; font-size:14px;' colspan='9' align=center>".utf8_encode($wempresas[$key]['Empdes'])."</td></tr>";

				//Controlo si se muestra la informacion del suplente.
				if($wcontrol_suplente_est == 'on'){
					
					$encabe_suplente = "<td align=center width=90px >Suplente</td>";
				}else{
			
				$encabe_suplente = "";		
				
				}
				
				
				if ($wvotos=='on')             //Marzo 4 de 2014
				   {
					echo "<tr class='encabezadoTabla find'>		
							<td align=center width=10px>Posición</td>
							<td align=center width=90px>Principal</td>
							$encabe_suplente
							<td align=center width=180px>Nombre</td>
							<td align=center width=200px>Apellidos</td>
							<td align=center width=270px>Empresa</td>
							<td align=center width=300px>Area</td>
							<td align=center width=180px>Observaciones</td>
							<td align=center width=20px>Votos</td>
						  </tr>";
				   }
				  else
                     {
					  echo "<tr class='encabezadoTabla find'>		
							 <td align=center width=90px>Principal</td>
							 $encabe_suplente
							 <td align=center width=180px>Nombre</td>
							 <td align=center width=200px>Apellidos</td>
							 <td align=center width=270px>Empresa</td>
							 <td align=center width=300px>Area</td>
							 <td align=center width=180px>Observaciones</td>
							</tr>";
					 } 				  
				echo "</thead>";
				$i=1; //Variable de control de datos.
				$j = 0; //Variable que controla la posicion en la que queda cada asociado.
				$wvoto_anterior = '';
				foreach ($array_votados as $key2 => $value) {				
								
					if($wcuantos_votos == ''){
						$wvoto_anterior = '';						
					}
					
					//Le asigno a la variable cuantos votos, el valor actual del registro, para luego compararlo con la cantidad de votos del registro anterior,
					//esto controla si el ultimo asociado en el escaño, tien la misma cantidad de votos del anterior y le pondra la misma posicion.
					$wcuantos_votos = $value['Votos'];
					
					($class == "#C3D9FF" ) ? $class = "#E8EEF7" : $class = "#C3D9FF";	//Cambio de color de fondo de cada linea.
					
					$checked = ''; //Controla el valor los checkbox.
					
					//Si esta aprobado el registro el cajon se chequea y el fondo queda verde.
					if($value['Insapr'] == 'on'){
					
						$checked = "checked";
						$class_td_checkbox = "#90F8A5"; //Color verde
						
					}	
					
					//Si no tiene genero en la tabla de taluma que corresponde al asociado ppal, pondra genero masculino.
					$genero_ppal = ($value['Idegen'] == '') ? "M" : $value['Idegen'];
					
					//Si no tiene genero en la tabla de taluma que corresponde al asociado suplente, pondra genero masculino.
					$genero_suplente = ($array_suplentes[$value['Insces']]['Idegen'] == '') ? "M" : $array_suplentes[$value['Insces']]['Idegen'];
					
					//Traer la foto del asociado ppal
					$foto_ppal = '<img class="imagen lightbox_ppal" id=fotografia_ppal_'.$i.'_'.$key2.' width=90px height=100px src="'.getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insced'],$genero_ppal).'"/>';
					
					//Traer la foto del suplente
					$foto_suplente = '<img class="imagen lightbox_suplente" id=fotografia_suplente_'.$i.'_'.$key2.' width=90px height=100px src="'.getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insces'],$genero_suplente).'"/>';
					
					//Si el registro cedula con valor 'enblanco', pondra como foto la frase 'VOTO EN BLANCO'
					$foto_ppal = ($value['Ideced'] == 'enblanco') ? "VOTO EN BLANCO" : $foto_ppal;
					$foto_suplente = ($value['Ideced'] == 'enblanco') ? "VOTO EN BLANCO" : $foto_suplente;					
					
					//Si el centro de costos con el que viene el usuario no tiene datos en el campo nombre, entonces buscara en el array_empresas_cco en la posicion descripcion, esto porque la tabla costosyp_000005 
					//lo tiene como nombre y la tabla clisur_000003 lo tiene como descripcion.
					$wcentro_costos_ppal = $array_empresas_cco[$value['Insemp']][$value['Inscco']]['Cconom'];		
					$wcentro_costos_ppal = ($wcentro_costos_ppal == '') ? $array_empresas_cco[$value['Insemp']][$value['Inscco']]['Ccodes'] : $array_empresas_cco[$value['Insemp']][$value['Inscco']]['Cconom'];
					
					//Si con ninguno de los dos encuentra datos, entonces pondra el nombre de la empresa.
					if($wcentro_costos_ppal == ''){
					
						$wcentro_costos_ppal = $wempresas[$value['Insemp']]['Empdes'];
					}
					
					//Si el centro de costos con el que viene el usuario no tiene datos en el campo nombre, entonces buscara en el array_empresas_cco en la posicion descripcion, esto porque la tabla costosyp_000005 
					//lo tiene como nombre y la tabla clisur_000003 lo tiene como descripcion.
					$wcentro_costos_suple = $array_empresas_cco[$value['Insesu']][$value['Insccs']]['Cconom'];		
					$wcentro_costos_suple = ($wcentro_costos_suple == '') ? $array_empresas_cco[$value['Insesu']][$value['Insccs']]['Ccodes'] : $array_empresas_cco[$value['Insesu']][$value['Insccs']]['Cconom'];
					
					//Si con ninguno de los dos encuentra datos, entonces pondra el nombre de la empresa.
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
					
					
					//Aqui se controla que posicion y los escaños, si el numero de la fila es menor o igual a la cantiad de escaños, incrementa la posicion, sino si,
					//el voto anterior es es igual al voto actual, deja el contador donde esta, sino, pondra la posicion en vacio.
					if($i <= $array_posicion['escanos']){
					
					$j++; //Posicion incrementa.
					
					}elseif(($wvoto_anterior*1) == ($wcuantos_votos*1)){					
						
					}else{
					$j = " ";	//Posicion sera vacio.		
					}
					
					//Controlo si se muestra la informacion del suplente.
					//if($wcontrol_suplente_cons == $wconsecutivo_votacion and $wcontrol_suplente_est == 'on'){
					if($wcontrol_suplente_est == 'on'){				
					
					$dato_foto_suplente = "<td style='background-color: ".$class.";' align=center>".$foto_suplente." <img class='fotografia_suplente_".$i."_".$key2."' id='imagen_grande_suplente_".$i."_".$key2."' src='".getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insces'],$genero_suplente)."' style='display:none' /></td>";
					$dato1_suplente = "<br><br> <b>Suplente:</b> <br>".utf8_encode($array_suplentes[$value['Insces']]['Ideno1'])." ".utf8_encode($array_suplentes[$value['Insces']]['Ideno2'])."";
					$dato2_suplente = "<br><br><b>Suplente:</b> <br> ".utf8_encode($array_suplentes[$value['Insces']]['Ideap1'])." ".utf8_encode($array_suplentes[$value['Insces']]['Ideap2'])."";
					$emp_suplente = "<br><br><b>Suplente:</b><br>".utf8_encode($wempresa_suplente)."";
					$cco_suplente = "<br><br><b>Suplente:</b><br> ".utf8_encode($wcentro_costos_suple)."";
					
					}
					
					//Impresion de los datos del asociado ppal y el suplente.
					if ($wvotos=='on')    //Maro 4 de 2014
				       {
						echo "<tr class='".$class." find'>
								<td nowrap align=center width=10px style='background-color: ".$class.";font-size: 20pt;'>".$j."</td>
								<td style='background-color: ".$class.";' align=center>".$foto_ppal." <img class='fotografia_ppal_".$i."_".$key2."' id='imagen_grande_ppal_".$i."_".$key2."' src='".getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insced'],$genero_ppal)."' style='display:none' /></td>
								$dato_foto_suplente
								<td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal ddd:</b> <br>".utf8_encode($value['Ideno1'])." ".utf8_encode($value['Ideno2'])." $dato1_suplente</td>
								<td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($value['Ideap1'])." ".utf8_encode($value['Ideap2'])." $dato2_suplente</td>
								<td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($wempresa_ppal)." $emp_suplente</td>
								<td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($wcentro_costos_ppal)." $cco_suplente</td>
								<td nowrap style='background-color: ".$class.";'>".$value['Insobs']."</td>
								<td nowrap style='background-color: #90F8A5;' align=center id='td_estado_$i' '><b>".$value['Votos']."</b></td>
						  </tr>";
					   }
					  else
                        {
							if ($value['Insced'] <> "00000000")  //Que el delegado sea diferente al voto en blanco Marzo 4 de 2014
							{
								if($j !=" ") 
								{
									 echo "<tr class='".$class." find'>
												<td style='background-color: ".$class.";' align=center>".$foto_ppal." <img class='fotografia_ppal_".$i."_".$key2."' id='imagen_grande_ppal_".$i."_".$key2."' src='".getFoto($conex,$wemp_pmla,$wbasedato,$wusuario,$value['Insced'],$genero_ppal)."' style='display:none' /></td>
												$dato_foto_suplente
												<td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b> <br>".utf8_encode($value['Ideno1'])." ".utf8_encode($value['Ideno2'])." $dato1_suplente</td>
												<td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($value['Ideap1'])." ".utf8_encode($value['Ideap2'])." $dato2_suplente</td>
												<td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($wempresa_ppal)." $emp_suplente</td>
												<td nowrap style='background-color: ".$class.";font-size: 10pt;'><b>Principal:</b><br>".utf8_encode($wcentro_costos_ppal)." $cco_suplente</td>
												<td nowrap style='background-color: ".$class.";'>".$value['Insobs']."</td>
										  </tr>";  
								}
						 
							}
						}					  
													
					$i++;
					$wvoto_anterior = $wcuantos_votos;
					
				}
				echo "<tr class='find'><td colspan=9>&nbsp;</td></tr>";
				echo "</table>";
			}
	}
	echo "</table>";
	echo "</div>";
	echo "</center>";
	echo "<br><br>";

	if ($wvotos=='on')             //Marzo 4 de 2014
	   {
		//Impresion de los votos totales y el detalle de las empresas votantes.
		echo "<center>";
		echo "<table border='0'>
				  <tbody>			
					<tr>
					  <td style='font-size:14pt;'>Votos totales:</td>
					  <td style='font-size:14pt;'>".$row_votos['votos_total']."</td>
					</tr>
				  </tbody>
				</table>";
		echo "</center>";
	   //}
	
		//////////////////////////////=====================================================================
		//Impresion de los votos totales por empresa y centro de costos.
		
		
		
		
		//Consulta por todos los que han votado.
		foreach($wtalhumas as $key_votos => $value_votos){

		$query_votos[] = "   SELECT Ideced, Idecco, Rvoemp
							   FROM ".$wbasedato."_000004, ".$value_votos['Detval']."
							  WHERE Rvoced = Ideced
								AND Rvoapl = '".$aplicacion."'
								AND Rvovot = '".$wconsecutivo_votacion."'
								AND Rvoest = 'on'
								AND Ideest = 'on'";
		}

		//Uno el arreglo anterior con la instruccion UNION para asi tener toda la informacion.
		$query_votos = implode(" UNION ", $query_votos);

		//Ejecuto la consulta final construida de forma dinamica.
		$res_votos_total = mysql_query($query_votos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_consultas." - ".mysql_error());

		$array_votos_total = array();
		$array_votos_total_emp = array();	

		while($row_votos_total = mysql_fetch_assoc($res_votos_total))
			{
				//Se verifica si el dato ya se encuentra en el arreglo, si no esta lo agrega.
				if(!array_key_exists($row_votos_total['Ideced'], $array_votos_total))
				{
					$array_votos_total[$row_votos_total['Ideced']] = $row_votos_total;
				}
				//Arreglo de empresas que han votado
				if(!array_key_exists($row_votos_total['Rvoemp'], $array_votos_total_emp))
				{
					$array_votos_total_emp[$row_votos_total['Rvoemp']] = array();
				}
				
				//Agrego a cada empresa los empleados con su centro de costo.
				if(!array_key_exists($row_votos_total['Idecco'], $array_votos_total_emp[$row_votos_total['Rvoemp']]))
				{
					$array_votos_total_emp[$row_votos_total['Rvoemp']][$row_votos_total['Idecco']] = array();
				}
							
				$array_votos_total_emp[$row_votos_total['Rvoemp']][$row_votos_total['Idecco']][] = $row_votos_total;
				
			}

		// echo "<div align=left>";	
		// echo "<pre>";
		// print_r($array_votos_total_emp);
		// echo "</pre>";
		// echo "</div>";
		$array_votos_total_emp_aux = array();
		
		echo "<center>";
		echo "<table border='0' width=350px>
				  <tbody>";
		//Recorro el array_votos_total_emp (array[empresa][cco][votantes]) para mostrar la estadistica
		foreach($array_votos_total_emp as $key_estadistica => $value_estadistica){
			
			//Creo un nuevo arreglo$array_votos_total_emp_aux, con la siguiente composicion $array_votos_total_emp_aux[empresa][cco] => cantidad de votos.
			if(!array_key_exists($key_estadistica, $array_votos_total_emp_aux))
				{
					$array_votos_total_emp_aux[$key_estadistica] = array();
				}
			
			foreach($value_estadistica as $cco => $value_cco){
					
					if(!array_key_exists($cco, $array_votos_total_emp_aux[$key_estadistica] )){
						$cco = ($cco == '') ? $key_estadistica : $cco; 
						 //$array_votos_total_emp_aux[empresa][cco] => cantidad de votos por cco.
						 $array_votos_total_emp_aux[$key_estadistica][$cco] = count($value_cco);
					}
				}
		
		}
		
		// echo "<div align=left>";	
		// echo "<pre>";
		// print_r($array_votos_total_emp_aux);
		// echo "</pre>";
		// echo "</div>";
		//Recorro el $array_votos_total_emp_aux[empresa][cco] => cantidad de votos, y luego ordeno los votos de mayor a menor por empresa.
		foreach($array_votos_total_emp_aux as $key_emp => $cco){
			$wtotal = 0;
			//Imprimo en un tr el nombre de la empresa
			echo "<tr class=encabezadoTabla>";
			echo "<td colspan=2>".utf8_encode($wempresas[$key_emp]['Empdes'])."</td>";
			echo "</tr>";
					
			$class='fila1'; //Variable para controlar el estilo de las filas en el reporte.		
			arsort($cco);
			
			foreach($cco as $cco_aux => $datos){
			
				($class == "fila2" )? $class = "fila1" : $class = "fila2";			
				
				$wtext_cco = $array_empresas_cco[$key_emp][$cco_aux]['Cconom'];			
				//Si no trae el centro de costos en la posicion Cconom, entonces lo busco con Ccodes
				if(trim($wtext_cco) == ''){
					
					$wtext_cco = $array_empresas_cco[$key_emp][$cco_aux]['Ccodes'];
				}
				
				//Si no esta con Ccodes, se imprime la empresa.
				if(trim($wtext_cco) == ''){			
					$wtext_cco = $wempresas[$key_emp]['Empdes']." - ".$cco_aux;
				}
				
				//Imprimo en este nuevo cliclo, los centros de costos y la cantidad total de votos por cco.
				echo "<tr class='".$class."'>";
				echo "<td nowrap>".utf8_encode($wtext_cco).'</td><td width=50px align=center><b>'.$datos."</b></td>";			
				echo "</tr>";
				
				$wtotal = $wtotal+$datos;
			}
			
			echo "<tr class='encabezadoTabla'>";
			echo "<td>Total</td><td width=50px align=center><b>".$wtotal."</b></td>";			
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan=2>&nbsp;</td></td>";			
			echo "</tr>";
			
		}
		
		echo "	  </tbody>
				</table>";
		echo "</center>";
		
		//if ($wvotos=='on')             //Marzo 4 de 2014
		//   {
			//Impresion de los votos totales y el detalle de las empresas votantes.
			echo "<center>";
			echo "<table border='0'>
					  <tbody>			
						<tr>
						  <td style='font-size:14pt;'>Votos totales:</td>
						  <td style='font-size:14pt;'>".$row_votos['votos_total']."</td>
						</tr>
					  </tbody>
					</table>";
			echo "</center>";
	   }
	
	
	echo "<br><br>";
}else{

	echo "<br>\n<br>\n".
        " <H1 align=center>No se pueden ver los resultados en este momento.</H1>";

}

echo "<br>";
echo "<center>
	<div><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";
echo "</form>";	
?>
</BODY>
</html>
