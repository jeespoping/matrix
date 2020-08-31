<?php
include_once("conex.php");
if (!isset($consultaAjax))
{
    
?>
<head>
  <title>REPORTE SOLICITUD CAMILLEROS</title>
</head>
<body>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>

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
        yearRange: '-20:+0'
        };
$.datepicker.setDefaults($.datepicker.regional['esp']);

function mostrar_detalle(id)
	{	
	
	if($("#"+id).is(':visible'))
		{
		$("#"+id).hide('1000');
		}
	else
		{
		$("#"+id).show('1000');
		}
	
	}

//Muestra los datos del reporte
function mostrar_datos()
 {

    $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
                    css: 	{
                                width: 	'auto',
                                height: 'auto'
                            }
                });

    var worigen = $("#origen").val();
    var wmotivo = $("#motivo").val();  
	var whabitacion_origen = $("#habitacion_origen").val();
	var wdestino = $("#destino").val();
	var wanulada = $("#anulada").val();
	var wcentral = $("#central").val();	
	var whistoria = $("#historia").val();
	var whab_asignada = $("#hab_asignada").val();
	var wfecha_inicial = $("#wfecha_inicial").val();
	var wfecha_final = $("#wfecha_final").val();
    var wbasedatos = $("#wbasedatos").val();
    var wemp_pmla = $("#wemp_pmla").val();  
	var wusuario = $("#wusuario").val();	
	
    $.post("rep_solicitud_camilleros.php",
            {
                consultaAjax:       	'mostrar_datos',               			
				worigen :				worigen,
				wmotivo : 				wmotivo,  
				whabitacion_origen : 	whabitacion_origen,
				wdestino :				wdestino,
				wanulada : 				wanulada,
				wcentral :				wcentral,
				whistoria : 			whistoria,
				whab_asignada : 		whab_asignada,
				wfecha_inicial : 		wfecha_inicial,
				wfecha_final : 			wfecha_final,
				wbasedatos :			wbasedatos,
				wemp_pmla : 			wemp_pmla,
				wusuario : 				wusuario

            }
            ,function(data_json) {

                if (data_json.error == 1)
                {
                    alert(data_json.mensaje);
                    $.unblockUI();
                    return;
                }
                else
                {                   
                    $("#datos_reporte").html(data_json.table);
                    $.unblockUI();
                }

        },
        "json"
    );
}

$(document).ready(function() {
    $("#wfecha_inicial").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true,
	  maxDate:"+1D"
    });

    $("#wfecha_final").datepicker({
      showOn: "button",
      buttonImage: "../../images/medical/root/calendar.gif",
      buttonImageOnly: true,
	  maxDate:"+1D"
    });
 
    
});
</script>

<?php
}
/* ***********************************************************************************
   * PROGRAMA PARA GENERAR EL REPORTE DE DESCUENTOS ESCALONADOS EN UN LAPSO DE FECHAS
   ***********************************************************************************/

//==================================================================================================================================
//PROGRAMA                   : rep_solicitud_camilleros.php
//AUTOR                      : Jonatan Lopez Aguirre.
//FECHA CREACION             : Septiembre 16 de 2013
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Septiembre 16 de 2013";
//DESCRIPCION
//==============================================================================================================================================\\
//Este reporte muestra todo las solicitudes a la central de camilleros o mensajero interno, las cuales se extraen de la tabla cencam_000003.          																							  \\
//===============================================================================================================================================\\

if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{

include_once("root/comun.php");
include_once("movhos/movhos.inc.php");





$wbasedatos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

$wfecha = date ("Y-m-d");
$whora = (string)date("H:i:s");


// Se incializan variables de fecha hora y usuario
if (strpos($user, "-") > 0)
    $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
    else
        $wuser=$user;

		
function nombre_usuario($wcodigo)
	{
	
	global $conex;
		
	//Nombre del usuario
	$q_usuario = " SELECT descripcion "
				."   FROM usuarios "
				."  WHERE codigo = '".$wcodigo."'";
	$res_usuario = mysql_query($q_usuario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuario." - ".mysql_error());
	$row_usuario = mysql_fetch_array($res_usuario);
	$wnombre = $row_usuario['descripcion'];
	
	return $wnombre;
	
	}

//Esta funcion muestra la informacion segun los datos recibidos.
function mostrar_datos($wbasedatos, $wemp_pmla, $worigen, $wmotivo, $whabitacion_origen, $wdestino, $wanulada,$wcentral, $whistoria, $whab_asignada, $wfecha_inicial, $wfecha_final, $wusuario)
    {

    global $conex;
	global $wcencam;
	
	$wfecha_actual = date ("Y-m-d");	
    $datamensaje = array('mensaje'=>'', 'error'=>0);    
    
		
	$query1 = "   SELECT Fecha_data, Hora_data, origen, motivo, habitacion, destino, anulada, central, historia, hab_asignada, solicito, ccosto, camillero, 
						 fecha_respuesta, hora_respuesta, fecha_llegada, hora_llegada, fecha_cumplimiento, hora_cumplimiento, observ_central, fec_asigcama, 
						 hora_asigcama, usu_central, usu_anula, fecha_anula, hora_anula, id
					FROM ".$wcencam."_000003
				   WHERE origen LIKE '%".$worigen."%'
					 AND motivo LIKE '%".$wmotivo."%'
					 AND habitacion LIKE '%".$whabitacion_origen."%'
					 AND Fecha_data BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'
					 AND destino LIKE '%".$wdestino."%'
					 AND anulada LIKE '%".$wanulada."%'
					 AND central LIKE '%".$wcentral."%'
					 AND historia LIKE '%".$whistoria."%'
					 AND hab_asignada LIKE '%".$whab_asignada."%'					
				ORDER BY Fecha_data DESC, hora_data DESC";
    $res1 = mysql_query( $query1, $conex) or die( mysql_errno()." - Error en el query $query_lab - ".mysql_error() );
    $num1 = mysql_num_rows($res1);

        
    $texto_html .= "<br><br><br>";	
    $texto_html .= "<table id='datos_reporte' style='text-align: center;' border=1 cellspacing=0>";
    $texto_html .= "<tr class=encabezadotabla>
						<td>Fecha y hora de solicitud</td>
						<td>Origen</td>
						<td>Motivo</td>
						<td>Destino</td>
						<td>Anulada</td>
						<td>Central</td>
						<td>Historia</td>
						<td>Hab. Asignada</td>
						<td>Solicito</td>
						<td>Detalle</td>
					</tr>";

    $i = 1; //Variable para controlar el estilo de los td

    while($row1 = mysql_fetch_array($res1))
        {

        if (is_integer($i/2))
                   $wclass="fila1";
                else
                   $wclass="fila2";        
               
		$usu_solicito = nombre_usuario($row1['solicito']);
		
		if($row1['hab_asignada'] == '' or $row1['historia'] == '')
			{
			$row1['hab_asignada'] = "&nbsp;";
			$row1['historia'] = "&nbsp;";
			
			}
		
		//Aqui busco el nombre de la central.
		$q = "  SELECT codcen, nomcen "
			."    FROM ".$wcencam."_000006 "
			."   WHERE cenest = 'on' "		
			."     AND codcen = '".$row1['central']."' "
			."   ORDER BY nomcen " ;
		$rescen = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$row_cen = mysql_fetch_array($rescen);
	
        $texto_html .= "<tr class=".$wclass." id='".$row1['id']."'>
                            <td>".$row1['Fecha_data']." ".$row1['Hora_data']."</td>                       
                            <td>".$row1['origen']."</td>
                            <td>".$row1['motivo']."</td>
                            <td>".$row1['destino']."</td>
                            <td>".$row1['anulada']."</td>
                            <td>".$row_cen['nomcen']."</td>
                            <td>".$row1['historia']."</td>
							<td>".$row1['hab_asignada']."</td>
							<td>".$row1['solicito']."-".utf8_encode($usu_solicito)."</td>
							<td><a href='javascript:' onclick='mostrar_detalle(\"detalle_".$row1['id']."\");'>Ver</a></td>"; 
        $texto_html .= "</tr>";        
		
		$usu_anula = nombre_usuario($row1['usu_anula']);
		
		if($row1['observ_central'] == '')
			{
			$row1['observ_central'] = "&nbsp;";
			}
		
		$texto_html .= "<tr id='detalle_".$row1['id']."' class=".$wclass." style = 'display:none'>
                         <td colspan=10>
						 <br>
						 <table  border=1 cellspacing=0>
						 <tr class=encabezadotabla>							                     
                            <td>Camillero o tipo de cama</td>
                            <td>Fecha y Hora de Respuesta</td>
                            <td>Fecha y Hora de Llegada</td>
                            <td>Fecha y Hora de Cumplimiento</td>
                            <td>Fecha y Hora de Asign. de Cama</td>
							<td>Fecha y Hora de Anulacion</td>
							<td>Usuario que anula</td>	
                            <td>Observaciones Central</td>													
						 </tr>
						  <tr>
							<td nowrap>".$row1['camillero']."</td>                       
                            <td nowrap>".$row1['fecha_respuesta']." ".$row1['hora_respuesta']."</td>
                            <td nowrap>".$row1['fecha_llegada']." ".$row1['hora_llegada']."</td>
                            <td nowrap>".$row1['fecha_cumplimiento']." ".$row1['hora_cumplimiento']."</td>
                            <td nowrap>".$row1['fec_asigcama']." ".$row1['hora_asigcama']."</td>
                            <td nowrap>".$row1['fecha_anula']." ".$row1['hora_anula']."</td>
                            <td nowrap>".$row1['usu_anula']."-".utf8_encode($usu_anula)."</td>
							<td nowrap>".$row1['observ_central']."</td>
						 </tr>
						 </table>
						 <br>
						 </td>"; 
        $texto_html .= "</tr>";
        $i++;
        }

     $texto_html .= "</table>";
	
	 
     $datamensaje['table'] = $texto_html;
     
     echo json_encode($datamensaje);

    }

//Este segmento interactua con los llamados ajax

//Si la variable $consultaAjax tiene datos entonces busca la funcion que trae la variable.
if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {

                    case 'mostrar_datos':
                        {
                            echo mostrar_datos($wbasedatos, $wemp_pmla, $worigen, $wmotivo, $whabitacion_origen, $wdestino, $wanulada,$wcentral, $whistoria, $whab_asignada, $wfecha_inicial, $wfecha_final, $wusuario);
                        }
                    break;

                    default : break;
                }
            return;
            }


  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L
  //===========================================================================================================================================
  //===========================================================================================================================================
    echo "<form name='rep_desc_escalonados' id='rep_desc_escalonados' action=''>";
    echo "<input type='HIDDEN' id='wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' id='wbasedatos' value='".$wbasedatos."'>";
    echo "<input type='HIDDEN' id='wusuario' value='".$wuser."'>";

	encabezado("REPORTE SOLICITUD DE CAMILLEROS", $wactualiz, "clinica");

    //========================== Origen de la solicitud ================================
	//
    $q =  " SELECT nombre, cco "
			 ."   FROM ".$wcencam."_000004"
			 ."  WHERE Estado = 'on' "
			 ."    AND (Uso   = 'I'"
			 ."     OR  Uso   = 'A') "      //A: Indica que puede ser Interno o Externo
			 ."  ORDER BY 1 ";
	$res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($res);

    $arr_orig = array();
    while($row_orig = mysql_fetch_array($res))
    {

        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_orig['cco'], $arr_orig))
        {
            $arr_orig[$row_orig['cco']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_orig[$row_orig['cco']] = $row_orig['nombre'];

    }

    $select_orig .=  "<select id='origen'>";
    $select_orig .=  "<option value=''>Seleccione...</option>";
    $select_orig .=  "<option value='%'>Todos</option>";

    foreach ($arr_orig as $key => $value) {

            $select_orig .=  "<option value='".$value."'>".$value."</option>";
    }

    $select_orig .=  "</select>";
	

    //===================================== TRAIGO LOS MOTIVOS DE LLAMADO =======================================================
	
	$q = "  SELECT Descripcion "
		."    FROM ".$wcencam."_000001"
		."   WHERE Estado = 'on' "
		."     AND (Uso   = 'I'"
		."      OR  Uso   = 'A') "       //Indica que el uso puede ser (I)nterno o (E)xterno
		."   ORDER BY Descripcion ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());
	
	$arr_mot = array();
	
    while($row_mot = mysql_fetch_array($res))
    {

        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_mot['Descripcion'], $arr_mot))
        {
            $arr_mot[$row_mot['Descripcion']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_mot[$row_mot['Descripcion']] = $row_mot['Descripcion'];

    }

    $select_mot .=  "<select id='motivo'>";
    $select_mot .=  "<option value=''>Seleccione...</option>";
    $select_mot .=  "<option value='%'>Todos</option>";

    foreach ($arr_mot as $key => $value) {

            $select_mot .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_mot .=  "</select>";
	
	   
	//============================= DESTINOS ======================================================
	
	$q =  " SELECT nombre "
		 ."   FROM ".$wcencam."_000004 "
		 ."  WHERE Estado = 'on' "
		 ."    AND (Uso   = 'I'"
		 ."     OR  Uso   = 'A') "
		 ."  ORDER BY 1 ";
	$res = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($res);
	
	$arr_dest = array();
	
    while($row_dest = mysql_fetch_array($res))
    {

        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_dest['nombre'], $arr_dest))
        {
            $arr_dest[$row_dest['nombre']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_dest[$row_dest['nombre']] = $row_dest['nombre'];

    }

    $select_dest .=  "<select id='destino'>";
    $select_dest .=  "<option value=''>Seleccione...</option>";
    $select_dest .=  "<option value='%'>Todos</option>";

    foreach ($arr_dest as $key => $value) {

            $select_dest .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_dest .=  "</select>";
   
    //==================================== Tipo de habitacion =======================================================
	//
     $q = "  SELECT Codigo, Nombre, 2 AS Tip "
		."    FROM ".$wcencam."_000002 "
		."   WHERE Unidad != 'INACTIVO' "
		."     AND central = '".$wcentral_camas."'"
		."   ORDER BY Tip, Nombre " ;
	$rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   
    $arr_tip_hab = array();
	
    while($row_tip_hab = mysql_fetch_array($rescam))
    {

        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_tip_hab['nombre'], $arr_tip_hab))
        {
            $arr_tip_hab[$row_tip_hab['nombre']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_tip_hab[$row_tip_hab['nombre']] = $row_tip_hab['nombre'];

    }

    $select_tip_hab .=  "<select id='tip_hab'>";
    $select_tip_hab .=  "<option value=''>Seleccione...</option>";
    $select_tip_hab .=  "<option value='%'>Todos</option>";

    foreach ($arr_tip_hab as $key => $value) {

            $select_tip_hab .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_tip_hab .=  "</select>";
   
    
	//======================================== Central ===================================================
	//
    $q = "  SELECT codcen, nomcen "
		."    FROM ".$wcencam."_000006 "
		."   WHERE cenest = 'on' "		
		."   ORDER BY nomcen " ;
	$rescen = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
   
    $arr_central = array();
	
    while($row_central = mysql_fetch_array($rescen))
    {

        //Se verifica si el producto ya se encuentra en el arreglo, si no esta lo agrega.
        if(!array_key_exists($row_central['codcen'], $arr_central))
        {
            $arr_central[$row_central['codcen']] = array();
        }

        //Aqui se forma el arreglo, con clave nit => nombre entidad
        $arr_central[$row_central['codcen']] = $row_central['nomcen'];

    }

    $select_central .=  "<select id='central'>";
    $select_central .=  "<option value=''>Seleccione...</option>";
    $select_central .=  "<option value='%'>Todos</option>";

    foreach ($arr_central as $key => $value) {

            $select_central .=  "<option value='".$key."'>".$value."</option>";
    }

    $select_central .=  "</select>";
	
	//========================================= FORMULARIO ==================================================
		
    echo "<br>";
    echo "<center>";
    echo "<table style='text-align: center;' border='0' cellpadding='0' cellspacing='2'>
			  <tbody>
				<tr class=encabezadotabla><td colspan=5>Reporte solicitud de camilleros <br>o mensaje interno</td>
				<tr class=fila1>
				  <td align=left>Origen:</td>
				  <td align=left>$select_orig</td>
				  <td>&nbsp;&nbsp;</td>
				  <td align=left>Motivo:</td>
				  <td align=left>$select_mot</td>
				</tr>
				<tr class=fila1>
				  <td align=left>Habitacion que solicita:</td>
				  <td align=left ><input id='habitacion_origen'></td>
				  <td></td>
				  <td align=left>Destino</td>
				  <td align=left>$select_dest</td>
				</tr>
				<tr class=fila1> 
				  <td align=left>Centro de Costos</td>
				  <td align=left >$select_orig</td>
				  <td></td>
				  <td align=left>Central</td>
				  <td align=left>$select_central</td>
				</tr>
				<tr class=fila1>
				  <td align=left>Anulada</td>
				  <td align=left >
				  <select id=anulada >
				  <option value=''></option>
				  <option value='Si'>Si</option>
				  <option value='No'>No</option>
				 </select>				  
				  </td>
				  <td></td>
				  <td align=left>Hab. Asignada</td>
				  <td align=left><input id='hab_asignada'></td>
				</tr>
				<tr class=fila1>
				  <td align=left>Historia</td>
				  <td align=left ><input id='historia'></td>
				  <td></td>
				  <td align=left></td>
				  <td align=left ></td>
				</tr>
				<tr class=fila1>
				  <td align=left>Fecha inicial:</td>
				  <td align=left ><input id='wfecha_inicial' value='".$wfecha."'></td>
				  <td></td>
				  <td align=left>Fecha final:</td>
				  <td align=left ><input id='wfecha_final' value='".$wfecha."'></td>
				</tr>
				<tr class=fila2>
				  <td colspan='5' rowspan='1' align=center>				  
				  <table style='text-align: center; ' border='0'>
					<tbody>
					  <tr>
						<td><input value='Enviar' id='a' type='button' onclick='mostrar_datos();'></td>
						<td></td>
						<td><input id='a' value='Limpiar' type='button'></td>
					  </tr>
					</tbody>
				  </table>
				  </td>
				</tr>
			  </tbody>
			</table>";
    
    echo "<center>
            <div id='datos_reporte'></div>
          </center>";
    
    
}
?>