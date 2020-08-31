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

$wactualiz="Abril 29 de 2014";

//==========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION                                                                                                                              \\
//=========================================================================================================================================\\

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
mysql_select_db("matrix") or die("No se selecciono la base de datos");

$wentidad = $institucion->nombre;
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = consultaraliasporaplicacion($conex, $wemp_pmla, "gescal" ); //Trae el nombre para el control de la base de datos correspondiente.
$wtalhuma = consultaraliasporaplicacion($conex, $wemp_pmla, "talhuma" );
$wtabcco = consultaraliasporaplicacion($conex, $wemp_pmla, 'tabcco');
$wlogoempresa = strtolower( $institucion->baseDeDatos );
$wusuario = substr($user,2,7);

function consultarNombreCC($cc)
{

	global $conex;
	global $wtabcco;

	$sql1 = "SELECT Cconom
			   FROM ".$wtabcco."
			  WHERE Ccocod='".$cc."'
			    AND Ccoest='on'";
	$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
	
	return $res1;			
}

function consultarNombrePersonaDetecto($wtalhuma,$wtabcco,$codUsu,$wemp_pmla,$ccDet)
{
	
	global $conex;	
	global $wtalhuma;
	global $wtabcco;
	
	$sql2 = "Select Ideno1,Ideno2,Ideap1,Ideap2,Ideext, Ideeml, Cardes, Cconom, Ccocod
                         FROM ".$wtalhuma."_000013,root_000079, ".$wtabcco."
                        WHERE Ideuse='".substr($codUsu,-5)."-".$wemp_pmla."'
                          AND Idecco='".$ccDet."'
                          AND Ideest='on'
                          AND Ideccg=Carcod
                          AND Idecco=Ccocod";
	$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
	
	return $res2;
}

function consultarEstadoEvento($est)
{
	
	global $conex;
	
	$sql5="SELECT Estdes,Estcol
			 FROM root_000093
			WHERE Estcod='".$est."'
			  AND Estest='on'";
	$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
	
	return $res5;
}


function consultarCausasNC($wbasedato,$evento,$id)
{
	global $conex;
	
	$sql4="SELECT Caucod
			 FROM ".$wbasedato."_000002
			WHERE Cautip='".$evento."'
			  AND Caneid='".$id."'
			  AND Cauest='on'";
	$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num4 = mysql_num_rows( $res4 );
				
	$opciones = '';
	
	if ($num4>0)
	{ //$opciones = array();
		for( $j = 0; $rows4 = mysql_fetch_array($res4); $j++ )
		{
			$opciones.= '-'.trim($rows4['Caucod']);
		}
		$opciones = substr( $opciones, 1 );
	}
	
	return $opciones;
}

function consultarCausasEA($wbasedato,$id)
{
	global $conex;
	
	$sql4="select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Caucon, root_000094.Tipcod,".$wbasedato."_000002.Id
				FROM ".$wbasedato."_000002, root_000091,root_000094
				WHERE Caneid = '".$id."'
				AND ".$wbasedato."_000002.Caucod=root_000091.Caucod
				AND root_000091.Caucla=root_000094.Tipcod
				AND root_000091.Cautip=root_000094.Tiptip
				AND root_000094.Tipest='on'
				AND ".$wbasedato."_000002.Cauest='on'";
				$res4 = mysql_query( $sql4, $conex ) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );
				//$rows4 = mysql_fetch_array( $res4 );
				$causa=array();

				for( $k = 0; $rows4 = mysql_fetch_array($res4); $k++ )
				{
					$causa[]=$rows4['Caucod']." ".$rows4['Caucon']." ".$rows4['Tipcod']." ".$rows4['Id'];
				}
				
	return $causa;
}

//funcion que muestra los datos de la persona que van a ingresar la no conformidad o el evento adverso
function datosPersonaDetecto($wcod_detecto)
{
	global $conex;	
	global $wemp_pmla;
	global $tabcco;
	global $wtalhuma;
	global $wtabcco;

	//Se seleccionan los datos del usuario
	$sql = "SELECT Descripcion
			  FROM usuarios
			 WHERE Codigo = '".$wcod_detecto."'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$rows = mysql_fetch_array( $res );	

	return $rows;
}


function datos_registro($wemp_pmla, $wbasedato, $datos){

	global $conex;
	global $wbasedato;
	global $wtabcco;
	
	$datos = base64_decode($datos);
	$datos = unserialize($datos);
	//print_r($datos);
	$data = array('mensaje'=>'', 'error'=>0, 'html'=>'');
	
	$array_usuario = datosPersonaDetecto($datos['Ncecpd']);
	
	$query_cco1 = "SELECT Ccocod, Cconom
			        FROM ".$wtabcco."
			       WHERE Ccoest = 'on' 
			    ORDER BY Cconom ";
	$res_cco1 = mysql_query( $query_cco1 ) or die( mysql_errno()." - Error en el query $query_cco1 - ".mysql_error() );

	$arr_cco1 = array();
	while($row_cco1 = mysql_fetch_array($res_cco1))
	{
		//Se verifica si el cco ya se encuentra en el arreglo, si no esta lo agrega.
		if(!array_key_exists($row_cco1['Ccocod'], $arr_cco1))
		{
			$arr_cco[$row_cco1['Ccocod']] = array();
		}

		$row_cco1['Cconom'] = str_replace( $caracteres, $caracteres2, $row_cco1['Cconom'] );
		$row_cco1['Cconom'] = utf8_decode( $row_cco1['Cconom'] );
		//Aqui se forma el arreglo, con clave el servicio => codigo del cco y su INFORMACIÓN.
		$arr_cco1[$row_cco1['Ccocod']] = trim($row_cco1['Ccocod'])."-".trim($row_cco1['Cconom']);

	}	
	
	$ccodetecto_texto = $arr_cco1[$datos['Nceccd']];
	$ccodetecto_texto = ($ccodetecto_texto != '') ? $ccodetecto_texto : $datos['Nceccd'];
	
	$data['html'] .= "<table border=1 cellspacing=0>";
	$data['html'] .= "<tr><td align=left class=encabezadotabla >PERSONA QUE DETECTO:  </td><td >".utf8_encode($array_usuario['Descripcion'])."</td></tr>";
	$data['html'] .= "<tr><td align=left class=encabezadotabla>UNIDAD QUE DETECTÓ: </td><td >".utf8_encode($ccodetecto_texto)."</td></tr>";
	$data['html'] .= "<tr><td align=left class=encabezadotabla>UNIDAD QUE GENERÓ: </td><td >".utf8_encode($arr_cco1[$datos['Nceccg']])."</td></tr>";
	$data['html'] .= "<tr><td class=encabezadotabla>DESCRIPCION:</td><td align=justify >".utf8_encode($datos['Ncedes'])."</td></tr>";
	$data['html'] .= "<tr><td class=encabezadotabla>OBSERVACIONES:</td><td align=justify>".utf8_encode($datos['Nceobs'])."</td></tr>";
	$data['html'] .= "</table>";

	echo json_encode($data);
	return;

}

function traer_datos_accion($id_accion){


	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	
	$sql_acciones = "  SELECT *, ".$wbasedato."_000002.id as cauid, ".$wbasedato."_000003.id as accid
						 FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000001
						WHERE ".$wbasedato."_000002.id = ".$wbasedato."_000003.Accaid
						  AND ".$wbasedato."_000001.id = ".$wbasedato."_000002.Caneid						
						  AND ".$wbasedato."_000003.id = '".$id_accion."'";
	$res_acciones =  mysql_query( $sql_acciones, $conex ) or die( mysql_errno()." - Error en el query $sql_acciones - ".mysql_error() );
	
	$row = mysql_fetch_array($res_acciones);
	
	return $row;


}


//
function mostrar_datos($wemp_pmla, $wbasedato, $estado_acc, $tipo_evento, $wfecha_inicial, $wfecha_final, $proximas){

	
	global $conex;
	global $wtabcco;
	
	$data = array('mensaje'=>'', 'error'=>0, 'html'=>''); 
	
	$wtabcco = $wtabcco;
	$wfecha_hoy = date('Y-m-d');
	
	$sql_procesos_cco = "  SELECT Procod, Prodes
							 FROM ".$wbasedato."_000008
							WHERE Proest = 'on'";
	$res_procesos_cco = mysql_query( $sql_procesos_cco, $conex );

	$array_procesos_cco = array();

	while($row_procesos_cco = mysql_fetch_array($res_procesos_cco)){

		if(!array_key_exists($row_procesos_cco['Procod'], $array_procesos_cco)){
		
			$array_procesos_cco[$row_procesos_cco['Procod']] = array('Procod'=>$row_procesos_cco['Procod'], 'Prodes'=>$row_procesos_cco['Prodes']);
		}
	}
	
	//Si seleccional el cajon de proximas a realizar se filtrara dependiendo de la fecha de proximas reuniones a partir de la fecha actual.
	$filtro_fecha = ($proximas != 'ok') ?  "AND ".$wbasedato."_000003.Fecha_data BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'" : "AND Accfpr >= '".$wfecha_hoy."'";
	
	//Consultar las acciones correctivas registradas
	$sql_acciones = "  SELECT *, ".$wbasedato."_000002.id as cauid, ".$wbasedato."_000003.id as accid
						 FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000001
						WHERE ".$wbasedato."_000002.id = ".$wbasedato."_000003.Accaid
						  AND ".$wbasedato."_000001.id = ".$wbasedato."_000002.Caneid
						  $filtro_fecha
						  AND Accest LIKE '".$estado_acc."'
						  AND Ncecne LIKE '".$tipo_evento."'
					 ORDER BY ".$wbasedato."_000003.Fecha_data";
	$res_acciones =  mysql_query( $sql_acciones, $conex ) or die( mysql_errno()." - Error en el query $sql_acciones - ".mysql_error() );
	$num_acciones = mysql_num_rows($res_acciones);
	
	$query_est_acc = "SELECT Estcod, Estdes
					   FROM root_000093
			          WHERE Estest = 'on'
					    AND Esttip = 'NCA' ";
    $res_est_acc = mysql_query( $query_est_acc ) or die( mysql_errno()." - Error en el query $query_est_acc - ".mysql_error() );

    $arr_est_acc = array();
    while($row_est_acc = mysql_fetch_array($res_est_acc))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_est_acc['Estcod'], $arr_est_acc))
        { 
			//Aqui se forma el arreglo
			$arr_est_acc[$row_est_acc['Estcod']] = $row_est_acc['Estdes'];
		}

    }
	
	
	//se traen las causas 
	 $sql_causas = "SELECT ".$wbasedato."_000002.Caucod as codigo,".$wbasedato."_000002.Cautip,Caneid,".$wbasedato."_000002.id AS id_causa, root_000091.Caudes 
					  FROM ".$wbasedato."_000002,root_000091 
					 WHERE ".$wbasedato."_000002.Caucod=root_000091.Caucod
					   AND ".$wbasedato."_000002.Cautip=root_000091.Cautip";		
	$res_causas = mysql_query( $sql_causas, $conex ) or die( mysql_errno()." - Error en el query $sql_causas - ".mysql_error() ); 
	
	$arr_causas = array();
    while($row_causas = mysql_fetch_assoc($res_causas))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_causas['codigo'], $arr_causas))
        { 
			//Aqui se forma el arreglo
			$arr_causas[$row_causas['codigo']] = $row_causas;
		}

    }
	
	//print_r($arr_causas);
	
	if ($num_acciones > 0)
	{		
		
		$data['html'] .= "<table align='center' >";
		$data['html'] .="<th class='encabezadotabla' align=center colspan='4'>$num_acciones ACCIONES CORRECTIVAS</th>";
		$data['html'] .="<tr><td>";
		$data['html'] .="<div>";		
		$data['html'] .="<center><table>"; 	
		$data['html'] .= "<head>";
		$data['html'] .= "<tr class='encabezadotabla' align=center>";
		    $data['html'] .= "<td align='center'>Numero</td>";
			$data['html'] .= "<td align='center'>Causa</td>";
			$data['html'] .= "<td align='center'>Accion</td>";
			$data['html'] .= "<td align='center'>Fecha</td>";
			$data['html'] .= "<td align='center'>Fecha prox Reunion</td>";
			$data['html'] .= "<td align='center'>Seguimiento</td>";
			$data['html'] .= "<td align='center'>Responsable</td>";
			$data['html'] .= "<td align='center'>Estado</td>";
		$data['html'] .= "</tr>";
		$data['html'] .= "</head>";		
		
		$i = 0;
		$j = 0; //Contador de reuniones cumplidas
		
		$sql_text = " SELECT Seguid, Segtex, Fecha_data
						FROM ".$wbasedato."_000013
					   WHERE Segest='on'
					ORDER BY Fecha_data";
		$res_text = mysql_query( $sql_text, $conex ) or die( mysql_errno()." - Error en el query $sql_text - ".mysql_error() );
		
		$array_seguimientos = array();
		
		while($row_text = mysql_fetch_array($res_text)){
				
				//$wseguimiento .= utf8_encode("\n".$row_text['Segtex']."\n");
				$array_seguimientos[$row_text['Seguid']][] = array('fecha_seg'=>$row_text['Fecha_data'],'seguimientos' => $row_text['Segtex']);
				
				}
		
		$array_seguimientos_cumplidos = array();
		
		while($row_acciones = mysql_fetch_assoc($res_acciones))
		{				
					
		  if (is_integer($i/2))
			   $wclass="fila1";
			else
			   $wclass="fila2";
			
			$wseguimientos = '';
			
			//Concatena los seguimientos de la tabla gescal_000013, solo si el id esta en el arreglo.
			if(array_key_exists($row_acciones['accid'],$array_seguimientos )){
			
				foreach($array_seguimientos[$row_acciones['accid']] as $key => $vl)
				{
					$wseguimientos .= "\n\n".utf8_encode($vl["seguimientos"])."\n";
				}			
			}
						
			$array_serializado = base64_encode(serialize($row_acciones)); //Array que contiene la informacion del registro.
			
			$data['html'] .= "<tr class='$wclass' align=center >";
			$data['html'] .= "<td align='center' style='cursor:pointer;' onclick='datos_registro(\"$wemp_pmla\",\"$wbasedato\", \"".$row_acciones['Caneid']."\", \"".$row_acciones['Ncecne']."\", \"".$row_acciones['Ncenum']."\")'><b>".$row_acciones['Ncenum']."</b><input type=hidden id='reg_".$row_acciones['Caneid']."' value='".$array_serializado."'></td>";
			$data['html'] .= "<td align='left' style=''>".utf8_encode($arr_causas[$row_acciones['Caucod']]['Caudes'])."</td>";
			$data['html'] .= "<td align='left' style=''>".utf8_encode($row_acciones['Accdes'])."</td>";
			$data['html'] .= "<td align='left' style=''>".$row_acciones['Accfre']."</td>";
			$data['html'] .= "<td align='left' style=''>".$row_acciones['Accfpr']."</td>";
			$data['html'] .= "<td align='center' style=''><textarea rows='10' cols='40'>".utf8_encode($row_acciones['Accseg'].$wseguimientos)."</textarea></td>";
			$data['html'] .= "<td align='left' style=''>".$row_acciones['Accres']."</td>";
			$data['html'] .= "<td align='left' nowrap=nowrap>".$arr_est_acc[$row_acciones['Accest']]."</td>";					
			$data['html'] .= "</tr>";
		
			$i++;
			
			//Busca la fecha del ultimo seguimiento
			$sql_text = " SELECT Seguid, Segtex, MAX(Fecha_data) as ult_seg
						    FROM ".$wbasedato."_000013
					       WHERE Segest='on'
						     AND Seguid = '".$row_acciones['accid']."'";
			$res_text = mysql_query( $sql_text, $conex ) or die( mysql_errno()." - Error en el query $sql_text - ".mysql_error() );
			$row_seg = mysql_fetch_array($res_text);
			
			if($row_seg['ult_seg'] >= $row_acciones['Accfpr'] ){
				
					$j++;
					
					array_push($array_seguimientos_cumplidos, $row_acciones['accid']); 
					
				
				}			
			}
			
		}
		else
		{
			$data['html'] .= "<table align='center'><th class='encabezadotabla'>NO TIENE ACCIONES CORRECTIVAS</th></table>";
		}

		$data['html'] .="</table></center>"; //tabla interna
		$data['html'] .="</table>";
		$data['html'] .= "</div>";	
		
		//print_r($data['html']);
		
		$data['reuniones_cumplidas'] .= "<table>";
		$data['reuniones_cumplidas'] .= "<tr>";
		$data['reuniones_cumplidas'] .= "<td class=encabezadotabla>Reuniones cumplidas:</td><td class=fila1 style='cursor:pointer;' onclick='ver_seguimientos_cumplidos()'><b>".count($array_seguimientos_cumplidos)."</b></td>";
		$data['reuniones_cumplidas'] .= "<tr>";
		$data['reuniones_cumplidas'] .= "</table>";
			
		
		$data['html_reuniones_cumplidas'] .= "<table align='center' >";		
		$data['html_reuniones_cumplidas'] .="<tr><td>";
		$data['html_reuniones_cumplidas'] .="<div>";		
		$data['html_reuniones_cumplidas'] .="<center><table>"; 	
		$data['html_reuniones_cumplidas'] .= "<head>";
		$data['html_reuniones_cumplidas'] .= "<tr class='encabezadotabla' align=center>";
		    $data['html_reuniones_cumplidas'] .= "<td align='center'>Numero</td>";
			$data['html_reuniones_cumplidas'] .= "<td align='center'>Causa</td>";
			$data['html_reuniones_cumplidas'] .= "<td align='center'>Accion</td>";
			$data['html_reuniones_cumplidas'] .= "<td align='center'>Fecha</td>";
			$data['html_reuniones_cumplidas'] .= "<td align='center'>Fecha prox Reunion</td>";
			$data['html_reuniones_cumplidas'] .= "<td align='center'>Seguimiento</td>";
			$data['html_reuniones_cumplidas'] .= "<td align='center'>Responsable</td>";
			$data['html_reuniones_cumplidas'] .= "<td align='center'>Estado</td>";
		$data['html_reuniones_cumplidas'] .= "</tr>";
		$data['html_reuniones_cumplidas'] .= "</head>";	
		
		if(count($array_seguimientos_cumplidos) > 0){
		
		foreach($array_seguimientos_cumplidos as $llave => $valor)	{
		
		$datos_accion = traer_datos_accion($valor);
		
		//Concatena los seguimientos de la tabla gescal_000013, solo si el id esta en el arreglo.
			if(array_key_exists($datos_accion['accid'],$array_seguimientos )){
			
				foreach($array_seguimientos[$datos_accion['accid']] as $key => $vl)
				{
					$wseguimientos_html .= "\n\n".utf8_encode($vl["seguimientos"])."\n";
				}			
			}
		
		
		$data['html_reuniones_cumplidas'] .= "<tr class='$wclass' align=center >";
		$data['html_reuniones_cumplidas'] .= "<td align='center' style='cursor:pointer;' onclick='datos_registro(\"$wemp_pmla\",\"$wbasedato\", \"".$datos_accion['Caneid']."\", \"".$datos_accion['Ncecne']."\", \"".$datos_accion['Ncenum']."\")'><b>".$datos_accion['Ncenum']."</b><input type=hidden id='reg_".$datos_accion['Caneid']."' value='".$array_serializado."'></td>";
		$data['html_reuniones_cumplidas'] .= "<td align='left' style=''>".utf8_encode($arr_causas[$datos_accion['Caucod']]['Caudes'])."</td>";
		$data['html_reuniones_cumplidas'] .= "<td align='left' style=''>".utf8_encode($datos_accion['Accdes'])."</td>";
		$data['html_reuniones_cumplidas'] .= "<td align='left' style=''>".$datos_accion['Accfre']."</td>";
		$data['html_reuniones_cumplidas'] .= "<td align='left' style=''>".$datos_accion['Accfpr']."</td>";
		$data['html_reuniones_cumplidas'] .= "<td align='center' style=''><textarea rows='10' cols='40'>".utf8_encode($datos_accion['Accseg'].$wseguimientos_html)."</textarea></td>";
		$data['html_reuniones_cumplidas'] .= "<td align='left' style=''>".utf8_encode($datos_accion['Accres'])."</td>";
		$data['html_reuniones_cumplidas'] .= "<td align='left' nowrap=nowrap>".$arr_est_acc[$datos_accion['Accest']]."</td>";					
		$data['html_reuniones_cumplidas'] .= "</tr>";
		
		
		}
		
		$data['html_reuniones_cumplidas'] .= "</table>";
		}else{
		$data['html_reuniones_cumplidas'] = "";
		}
		
		
	echo json_encode($data);
	return;
}


if(isset($consultaAjax))
	{
	
	switch($consultaAjax){
		
		case 'mostrar_datos':  
					{
					echo mostrar_datos($wemp_pmla, $wbasedato, $estado_acc, $tipo_evento, $wfecha_inicial, $wfecha_final, $proximas);
					}					
		break;
		
		case 'datos_registro':  
					{
					echo datos_registro($wemp_pmla, $wbasedato, $datos);
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
<title>Reporte de acciones correctivas</title>
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />

<style type="text/css">
    
</style>
<script type="text/javascript">

$.datepicker.regional['esp'] = {
        closeText: 'Cerrar',
        prevText: 'Antes',
        nextText: 'Despues',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
        'Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        yearSuffix: '',
        changeYear: true,
        changeMonth: true,
        };
		
$.datepicker.setDefaults($.datepicker.regional['esp']);

function ver_seguimientos_cumplidos(wemp_pmla, wbasedato, array_seguimientos){

		
		$("#html_reuniones_cumplidas" ).dialog({
						show: {
						effect: "blind",
						duration: 100
						},
						hide: {
						effect: "blind",
						duration: 100
						},
						autoOpen: false,
						
						height:'auto',				
						width: '1100px',
						dialogClass: 'fixed-dialog',
						modal: true,
						title: "Acciones correctivas con seguimiento cumplido"
						});
					
				
		$('#html_reuniones_cumplidas').dialog('open');


}

function datos_registro(wemp_pmla, wbasedato, id, tipo_evento, id_nc_ea){

	
	var datos = $("#reg_"+id).val();
	
	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
	
	$.post("rep_acciones_correctivas.php",
				{
					consultaAjax:       'datos_registro',
					wemp_pmla:			wemp_pmla,					
					wbasedato:			wbasedato,
					datos:				datos
				

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					 
					}
					else
					{   
					
					$("#modal_datos" ).dialog({
						show: {
						effect: "blind",
						duration: 100
						},
						hide: {
						effect: "blind",
						duration: 100
						},
						autoOpen: false,
						
						height:'auto',				
						width: '1100px',
						dialogClass: 'fixed-dialog',
						modal: true,
						title: "Resumen "+tipo_evento+" "+id_nc_ea
						});
					
					 $.unblockUI();
					$('#modal_datos').html(data_json.html);
					$('#modal_datos').dialog('open');
					
					
					}

			},
			"json"
		);

}

//Funcion que guarda las observaciones.
function mostrar_datos(wemp_pmla, wbasedato){
	
	
	var estado_acc = $('#estado_acc').val();
	var tipo_evento = $('#tipo_evento').val();
	var wfecha_inicial = $('#wfecha_inicial').val();
	var wfecha_final = $('#wfecha_final').val();
	var proximas = '';
	
	if($("#proximas").is(':checked')){
	
		var proximas = 'ok';
		
	}
		
	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
	
	$.post("rep_acciones_correctivas.php",
				{
					consultaAjax:       'mostrar_datos',
					wemp_pmla:			wemp_pmla,					
					wbasedato:			wbasedato,
					estado_acc:			estado_acc,
					tipo_evento:		tipo_evento,
					wfecha_inicial:		wfecha_inicial,
					wfecha_final:		wfecha_final,
					proximas:			proximas

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
					 
					}
					else
					{   
					
					$.unblockUI();
					$('#reuniones_cumplidas').html(data_json.reuniones_cumplidas);
					$('#html_reuniones_cumplidas').html(data_json.html_reuniones_cumplidas);
					$('#resultado').html(data_json.html);
					
					}

			},
			"json"
		);
}


function limpiarbusqueda(){


location.reload();


}

function cerrarVentana(){
  window.close();	
 }


$(document).ready(function() {

    $("#wfecha_inicial").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true
    });

    $("#wfecha_final").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true
	 
    });
   
    
});



</script>
<Body>
<?php

//==========================================================================================================================================
//PROGRAMA				      :Reporte de acciones correctiva.                                                                   
//AUTOR				          :Jonatan Lopez Aguirre.                                                                        
//FECHA CREACION			  :
//FECHA ULTIMA ACTUALIZACION  :                                                                          


encabezado("Reporte de acciones correctivas",$wactualiz, $wlogoempresa);

	//=========================================== IMPRESION DE LOS RESULTADOS =======================================================	

echo "<form>"; 
echo "<center>";

	//========================= Eventos ======================================
	$query_evento = "SELECT Concod, Condes
					   FROM ".$wbasedato."_000006
			          WHERE Conest = 'on'";
    $res_evento = mysql_query( $query_evento ) or die( mysql_errno()." - Error en el query $query_evento - ".mysql_error() );

    $arr_evento = array();
    while($row_evento = mysql_fetch_array($res_evento))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_evento['Concod'], $arr_evento))
        {
            $arr_evento[$row_evento['Concod']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_evento[$row_evento['Concod']] = $row_evento['Condes'];

    }

    $select_evento .=  "<select id='tipo_evento'>";  
    $select_evento .=  "<option value='%'>Todos</option>";

    foreach ($arr_evento as $key => $value) {

            $select_evento .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_evento .=  "</select>";
	
	//======================= Estados de las acciones correctivas ==========================
	
	$query_est_acc = "SELECT Estcod, Estdes
					   FROM root_000093
			          WHERE Estest = 'on'
					    AND Esttip = 'NCA' ";
    $res_est_acc = mysql_query( $query_est_acc ) or die( mysql_errno()." - Error en el query $query_est_acc - ".mysql_error() );

    $arr_est_acc = array();
    while($row_est_acc = mysql_fetch_array($res_est_acc))
    {
        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_est_acc['Estcod'], $arr_est_acc))
        {
            $arr_est_acc[$row_est_acc['Estcod']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_est_acc[$row_est_acc['Estcod']] = $row_est_acc['Estdes'];

    }

    $select_est_acc .=  "<select id='estado_acc'>";
    $select_est_acc .=  "<option value='%'>Todos</option>";

    foreach ($arr_est_acc as $key => $value) {

            $select_est_acc .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_est_acc .=  "</select>";
	
	
	//======================================================================================
		

 echo "<table style='text-align: center; width: auto;'>
          <tbody>           
            <tr class=encabezadotabla align=left id='tipoc_cedula'>
                <td colspan=2>&nbsp;</td>               
            </tr>
            <tr class=fila1 align=left >
                <td><b>Evento:</b></td>
                <td>
                 $select_evento
                </td>
            </tr>
			<tr class=fila1 align=left>
                <td><b>Estado:</b></td>
                <td>
                 $select_est_acc
                </td>
            </tr>
            <tr class=fila1>
                <td align=left><b>Fecha inicial:</b></td>
                <td align=left><input type=text id='wfecha_inicial' value='".date("Y-m-d")."'>
                </td>
            </tr>
            <tr class=fila1>
                <td align=left><b>Fecha final:</b></td>
                <td align=left><input type=text id='wfecha_final' value='".date("Y-m-d")."'>
                </td>
            </tr>
			 <tr class=fila1>
                <td align=left><b>Próximas a realizar:</b></td>
                <td align=left><input type=checkbox id='proximas'>
                </td>
            </tr>
           </tbody>
           </table>";
	echo " <table>";
    echo "  <tr>";
    echo "      <td>";
    echo "      <input type=button onclick='mostrar_datos(\"$wemp_pmla\", \"$wbasedato\");' value=Enviar><input type=reset onclick='limpiarbusqueda();' value=Limpiar>";
    echo "      </td>";
    echo "  </tr>";
    echo " </table>";
echo "</center>";
echo "</form>";

echo "<br><br>";
echo "<div align=right id='reuniones_cumplidas'></div>";
echo "<div align=center id='html_reuniones_cumplidas' style='display:none;'></div>";
echo "<div align=center id='resultado'></div>";
echo "<div align=center id='modal_datos'></div>";

echo "<br>";
echo "<center>
	<div><input type=button onclick='cerrarVentana();' value='Cerrar Ventana'></div>
	</center>";

?>
</BODY>
</html>
