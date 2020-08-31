<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Felipe Alvarez Sanchez
//FECHA DE CREACION:
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	$wactualiz='2018-09-05';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//	2018-09-05	- Jessica Madrid Mejía:	- Se agrega un trim al código de la habitación ya que al grabar la configuración por habitación 
// 										  o consultar las tarifas al tener un espacio adicional no se grababan los cambios y al consultar 
// 										  las tarifas no mostraba nada.
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	

	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	include_once("root/comun.php");
	$conex = obtenerConexionBD("matrix");
	include_once("ips/funciones_facturacionERP.php");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	function traer_buscadores()
	{
		$html = "<table width='30%' >
						<tr class='encabezadoTabla'><td colspan='2'>Buscar</td></tr>
						<tr class='fila1'>
							<td>
								Centro de costos
							</td>
							<td>
								Habitación
							</td>

						</tr>
						<tr class='fila2'>
							<td>
								<input type='text' id='buscador_cco'><img width='12' border='0' height='12' title='Busque el nombre o parte del nombre del centro de costo' src='../../images/medical/HCE/lupa.PNG'>
							</td>
							<td>
								<input type='text' id='buscador_hab' ><img width='12' border='0' height='12' title='Busque el nombre o parte del nombre de la Habitacion' src='../../images/medical/HCE/lupa.PNG'>
							</td>
						</tr>
				 </table>";

		return $html;
	}



	function traer_camas($cod_cco='' , $cod_hab='')
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		if($cod_cco=='*')
			$cod_cco='';

		$html= "<table width='90%'>
					<tr class='encabezadoTabla'>
						<td>Centro de Costos</td>
						<td>Habitación</td>
						<td>Codigo en pantalla</td>
						<td>Tipo de Habitación Hospitalaria</td>
						<td>Tipo de Habitación  Facturación</td>
						<td>Tarifa(s)</td>
					</tr>";

		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$wbasedato_cemcam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

		$q_camas = 	 "SELECT Habcod,Habcco,Habtip,Habtfa,Cconom,Tipdes,Pronom,Procod,Habcpa
					    FROM	".$wbasedato_movhos."_000020
						INNER JOIN ".$wbasedato_movhos."_000011
							ON (  Habcco = Ccocod)
						LEFT JOIN ".$wbasedato_cemcam."_000007
							ON(Habtip = Tipcod )
						LEFT JOIN ".$wbasedato."_000103
							ON (Habtfa = Procod AND Protip = 'H')
						  WHERE Habest = 'on' ";

		if($cod_cco != '')
			$q_camas = 	$q_camas  ." AND Habcco = '".$cod_cco."' ";

		if($cod_hab != '' )
			$q_camas = 	$q_camas  . " AND Habcod = '".$cod_hab."' ";

		$q_camas = 	$q_camas  ."ORDER BY Habcco , Habcod ";



		$res_camas = mysql_query($q_camas,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_camas." - ".mysql_error());
		$num_camas = mysql_num_rows($res_camas);
		$i=0;

		// se trae un <select> generico
		$select_camas_facturacion = select_camas_facturacion();
		$select_camas_hospitalarias = select_camas_hospitalarias();
		$mostrar = 'no';
		if(mysql_num_rows($res_camas)>0)
		{
			$mostrar ='si';
		}
		//traer_camas_hospitalarias();
		while($row_camas = mysql_fetch_array($res_camas))
		{
				$row_camas["Habcod"] = trim($row_camas["Habcod"]);
			
				$aux_select_camas_faturacion   = $select_camas_facturacion;
				$aux_select_camas_hospitalarias=$select_camas_hospitalarias;
				if (($i%2)==0)
					$wcf="fila1";  // color de fondo de la fila
				else
					$wcf="fila2"; // color de fondo de la fila
				//------
				$i++;

				$html.="
					<tr class='".$wcf."'>
						<td>".$row_camas['Habcco']."-".$row_camas['Cconom']."</td>
						<td>".$row_camas['Habcod']."</td>
						<td><input id='input_cambio_".$row_camas['Habcod']."' type='text' value='".$row_camas['Habcpa']."' onchange='cambionombre(\"".$row_camas['Habcod']."\")'></td>
					";
				//---Imprime datos de tipo de cama hospitalaria
				if($row_camas['Tipdes']=='')
				{
					//se hacen unos reemplazos para  cambiar el <select> generico a especifico
					$aux_select_camas_hospitalarias = str_replace('tipo_hab_hospitalaria_','tipo_hab_hospitalaria_'.$row_camas["Habcod"].'',$aux_select_camas_hospitalarias);
					$aux_select_camas_hospitalarias = str_replace('parametro2',$row_camas["Habcod"],$aux_select_camas_hospitalarias);
					//------------------------------------------------------------------------
					$html.="	<td>".$aux_select_camas_hospitalarias."</td>";
				}
				else
				{
					$aux_select_camas_hospitalarias = str_replace('tipo_hab_hospitalaria_','tipo_hab_hospitalaria_'.$row_camas["Habcod"].'',$aux_select_camas_hospitalarias);
					$aux_select_camas_hospitalarias = str_replace('parametro2',$row_camas["Habcod"],$aux_select_camas_hospitalarias);
					$aux_select_camas_hospitalarias = str_replace("value='".$row_camas['Habtip']."'", "value='".$row_camas['Habtip']."' selected",$aux_select_camas_hospitalarias);
					$aux_select_camas_hospitalarias = str_replace("class='campoRequerido'", " ",$aux_select_camas_hospitalarias);

					$html.="	<td>".$aux_select_camas_hospitalarias."</td>";
				}
				//--------------------------------
				//--------------------------------

				//---Imprime datos de tipo de cama facturacion
				if($row_camas['Procod']=='')
				{
					//se hacen unos reemplazos para  cambiar el <select> generico a especifico
					$aux_select_camas_faturacion = str_replace('tipo_hab_facturacion_','tipo_hab_facturacion_'.TRIM($row_camas["Habcod"]).'',$aux_select_camas_faturacion);
					$aux_select_camas_faturacion = str_replace('parametro1',TRIM($row_camas["Habcod"]),$aux_select_camas_faturacion);
					//------------------------------------------------------------------------
					$html.="	<td>".$aux_select_camas_faturacion."</td>";
				}
				else
				{
					//se hacen unos reemplazos para  cambiar el <select> generico a especifico
					$aux_select_camas_faturacion = str_replace('tipo_hab_facturacion_','tipo_hab_facturacion_'.$row_camas["Habcod"].'',$aux_select_camas_faturacion);
					$aux_select_camas_faturacion = str_replace('parametro1',$row_camas["Habcod"],$aux_select_camas_faturacion);
					$aux_select_camas_faturacion = str_replace("value='".$row_camas['Habtfa']."'", "value='".$row_camas['Habtfa']."' selected",$aux_select_camas_faturacion);
					$aux_select_camas_faturacion = str_replace("class='campoRequerido'", ' ',$aux_select_camas_faturacion);
					//--------------------------------------------------------------------------
					$html.="	<td>".$aux_select_camas_faturacion."</td>";
				}
				//-----------------------------------------
				//-----------------------------------------
				if($row_camas['Procod']!='')
					$html.= "<td width='100' ><div id='div_tar_".TRIM($row_camas["Habcod"])."'><a style='cursor : pointer' onclick='traer_tarifas(\"".TRIM($row_camas["Habcod"])."\")'>Ver tarifa(s)</a></td>";
				else
					$html.= "<td width='100'><div id='div_tar_".TRIM($row_camas["Habcod"])."'></div></td>";

				$html.= "</tr>";
		}

		$html.="</table>";
		if($mostrar=='si')
			echo $html;
		else
			echo "<table align='center'><tr><td>No hay datos para mostrar</td></tr></table>";

	}


	function select_camas_facturacion()
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		$html	="<select   id='tipo_hab_facturacion_' class='campoRequerido' onchange='grabar_tipo_habitacion_facturacion(\"parametro1\")' onclick='validaClaseFac(\"parametro1\")' >";

		$q_tip_hab  = "SELECT Procod,Pronom  "
					 ."  FROM  ".$wbasedato."_000103"
					 ." WHERE  Protip='H' ";
		$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
		if(mysql_num_rows($res_tip_hab)>0)
		{
			$html   .="<option value='' >Seleccione un tipo</option>";
		}
		while($row_tip_hab = mysql_fetch_array($res_tip_hab))
		{
			$html   .="<option value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
		}

		$html   .="</select>";
		return  $html;
	}

	function select_camas_hospitalarias()
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		$html	="<select  id='tipo_hab_hospitalaria_' class='campoRequerido' onchange='grabar_tipo_habitacion_hospitalaria(\"parametro2\")' onclick='validaClaseHosp(\"parametro2\")' >";

		$wbasedato_cemcam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

		$q_tip_hab  = "SELECT Tipcod,Tipdes "
					 ."  FROM  ".$wbasedato_cemcam."_000007"
					 ." WHERE  Tipest='on' ";

		$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());

		if(mysql_num_rows($res_tip_hab)>0)
		{
			$html   .="<option value='' >Seleccione un tipo</option>";
		}
		while($row_tip_hab = mysql_fetch_array($res_tip_hab))
		{
			$html   .="<option value='".$row_tip_hab['Tipcod']."' >".$row_tip_hab['Tipdes']."</option>";
		}

		$html   .="</select>";
		return  $html;

	}

	function cargar_hiddens_para_autocomplete()
	{
		// --> Centros de costos
		echo "<input type='hidden' id='hidden_cco' value='".json_encode(Obtener_array_cco())."'>";
		echo "<input type='hidden' id='hidden_hab' value='".json_encode(Obtener_array_habitacion())."'>";
	}

	function grabar_tipo_hab_hosp($cod_hab, $cod_tip_hab)
	{

		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wuse;

		$wfecha=date("Y-m-d");
		$whora = date("H:i:s");

		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

		$q_select = " SELECT Habcod , Habtip"
					."  FROM ".$wbasedato_movhos."_000020"
					." WHERE   Habcod = TRIM('".$cod_hab."') ";

		$res_select = mysql_query($q_select,$conex) or die("Error en el query: ".$q_tarifas."<br>Tipo Error:".mysql_error());
		$row_select = mysql_fetch_array($res_select);



		$q  = 		"UPDATE  ".$wbasedato_movhos."_000020"
					 ." SET Habtip = '".$cod_tip_hab."' "
					 ." WHERE  Habcod = TRIM('".$cod_hab."') ";

		$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		$q_log = "INSERT INTO ".$wbasedato."_000179 "
				."		   (					Cloant								,			Clomov				,			Cloope				,		Clousu	,	Cloest,		Seguridad		,		Medico		,Fecha_data		,Hora_data)"
				."  VALUES ('".$row_select['Habcod']."-".$row_select['Habtip']."'	,'".$cod_hab."-".$cod_tip_hab."','Modificacion: Cambio de tipo Hospitalaria','".$wuse."',	'on',	'C-".$wbasedato."'	,'".$wbasedato."'	,'".$wfecha."'	,'".$whora."')";

		$res = mysql_query($q_log,$conex) or die("Error en el query: ".$q_log."<br>Tipo Error:".mysql_error());

	}


	function grabar_tipo_hab_fact($cod_hab, $cod_tip_hab)
	{

		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wuse;

		$wfecha=date("Y-m-d");
		$whora = date("H:i:s");
		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

		$q_select = " SELECT Habcod , Habtfa"
					."  FROM ".$wbasedato_movhos."_000020"
					." WHERE   Habcod = TRIM('".$cod_hab."') ";



		$res_select = mysql_query($q_select,$conex) or die("Error en el query: ".$q_tarifas."<br>Tipo Error:".mysql_error());
		$row_select = mysql_fetch_array($res_select);


		$q  = 		"UPDATE  ".$wbasedato_movhos."_000020"
				     ." SET Habtfa = '".$cod_tip_hab."' "
				   ." WHERE  Habcod = TRIM('".$cod_hab."') ";



		$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		$q_log = "INSERT INTO ".$wbasedato."_000179 "
				."		   (					Cloant								,			Clomov				,			Cloope				,		Clousu	,	Cloest,		Seguridad		,		Medico		,Fecha_data		,Hora_data)"
				."  VALUES ('".$row_select['Habcod']."-".$row_select['Habtfa']."'	,'".$cod_hab."-".$cod_tip_hab."','Modificacion: Cambio de tipo facturacion','".$wuse."',	'on',	'C-".$wbasedato."'	,'".$wbasedato."'	,'".$wfecha."'	,'".$whora."')";

		$res = mysql_query($q_log,$conex) or die("Error en el query: ".$q_log."<br>Tipo Error:".mysql_error());

	}

	function traer_tarifas($wcod_habs)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		$q_tarifas     = "SELECT ".$wbasedato."_000104.Tarcon,".$wbasedato."_000104.Tartar, ".$wbasedato."_000104.Tarvac,".$wbasedato."_000104.Tarcod ,Pronom ,Procod,Grucod,Grudes,".$wbasedato."_000025.Tardes"
						."  FROM  ".$wbasedato."_000104,  ".$wbasedato."_000103  ,".$wbasedato."_000200 ,".$wbasedato."_000025"
						." WHERE  ".$wbasedato."_000104.Tarcod ='".$wcod_habs."' "
						."   AND  ".$wbasedato."_000104.Tarcod = Procod "
						."   AND  Grucod = ".$wbasedato."_000104.Tarcon "
						."   AND  ".$wbasedato."_000025.Tarcod = ".$wbasedato."_000104.Tartar"
						."   AND  ".$wbasedato."_000104.Tarest !='off' "
						."   AND ".$wbasedato."_000025.Tarest!='off'";

		$res_tarifas = mysql_query($q_tarifas,$conex) or die("Error en el query: ".$q_tarifas."<br>Tipo Error:".mysql_error());
		$i=0;
		$html = "<table align='center'>";
		$html   .="<tr class='encabezadoTabla'><td></td><td>tarifa</td><td>Valor</td></tr>";
		while($row_tarifas = mysql_fetch_array($res_tarifas))
		{

			if (($i%2)==0)
				$wcf="fila1";  // color de fondo de la fila
			else
				$wcf="fila2"; // color de fondo de la fila
			//------
			$i++;

			$html   .="<tr class='".$wcf." letrapequena'><td>".$i."</td><td>".$row_tarifas['Tartar']."-".$row_tarifas['Tardes']."</td><td>".number_format($row_tarifas['Tarvac'])."</td></tr>";
		}

		$html .= "</table>";
		echo $html;
	}

	function grabar_CodigoPantalla($wcod_hab,$wnom_cpa)
	{

		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		global $wuse;

		$wfecha=date("Y-m-d");
		$whora = date("H:i:s");
		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

		$q_select = " SELECT Habcod , Habcpa"
					."  FROM ".$wbasedato_movhos."_000020"
					." WHERE   Habcod = TRIM('".$wcod_hab."') ";

		$res_select = mysql_query($q_select,$conex) or die("Error en el query: ".$q_tarifas."<br>Tipo Error:".mysql_error());
		$row_select = mysql_fetch_array($res_select);

		$q  = 		"UPDATE  ".$wbasedato_movhos."_000020"
				     ." SET Habcpa = '".$wnom_cpa."' "
				   ." WHERE  Habcod = TRIM('".$wcod_hab."') ";

		$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

			$q_log = "INSERT INTO ".$wbasedato."_000179 "
				."		   (					Cloant								,			Clomov				,			Cloope				,		Clousu	,	Cloest,		Seguridad		,		Medico		,Fecha_data		,Hora_data)"
				."  VALUES ('".$row_select['Habcod']."-".$row_select['Habcpa']."'	,'".$wcod_hab."-".$wnom_cpa."','Modificacion: Cambio de nombre pantalla','".$wuse."',	'on',	'C-".$wbasedato."'	,'".$wbasedato."'	,'".$wfecha."'	,'".$whora."')";

		$res = mysql_query($q_log,$conex) or die("Error en el query: ".$q_log."<br>Tipo Error:".mysql_error());



	}





//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================

if(isset($accion))
{

	switch($accion)
	{
		case 'traer_camas':
		{

			traer_camas ($cod_cco, $cod_hab);
			break;
			return;
		}
		case 'grabar_tipo_hab_hosp':
		{
			grabar_tipo_hab_hosp($cod_hab, $cod_tip_hab);
			break;
			return;
		}
		case 'grabar_tipo_hab_fact':
		{
			grabar_tipo_hab_fact($cod_hab, $cod_tip_hab);
			break;
			return;
		}
		case 'traer_tarifas':
		{
			traer_tarifas($wcod_habs);
			break;
			return;

		}
		case 'grabar_CodigoPantalla':
		{
			grabar_CodigoPantalla($wcod_hab,$wnom_cpa);
			break;
			return;
		}

	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	?>
	<html>
	<head>
	  <title>Administracion de Habitaciones</title>
	</head>

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
	var url_add_params = addUrlCamposCompartidosTalento();

	var ArrayValores ;
	$(document).ready(function() {

		// --> Cargar tooltips
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });


		traer_camas( );

		//-llenado de buscadores
		cargar_cco (eval('(' + $("#hidden_cco").val() + ')'));
		cargar_hab (eval('(' + $("#hidden_hab").val() + ')'));

		$("#buscador_hab").keyup(function(){

			if ($(this).val() == "")
			traer_camas();

		});

		$("#buscador_cco").keyup(function(){

			if ($(this).val() == "")
			traer_camas();

		});


	});

	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajes").html("<BLINK><img width='15' height='15' src='../../images/medical/root/info.png' /></BLINK>&nbsp;"+mensaje);
		$("#div_mensajes").css({"width":"300","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes").hide();
		$("#div_mensajes").show(500);
	}


	function esperar (  )
	{
		$.blockUI({ message:        '<img src="../../images/medical/ajax-loader.gif" >',
			css:   {
					   width:  'auto',
					   height: 'auto'
				   }
		});
	}

	function cambionombre(cod_hab)
	{


		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'grabar_CodigoPantalla',
			wcod_hab: 			cod_hab ,
			wnom_cpa:			$("#input_cambio_"+cod_hab).val()

		},function (data) {

		});

	}
	function traer_camas()
	{


		if($("#buscador_hab").val().length==0)
			$("#buscador_hab").attr('valor','');

		if($("#buscador_cco").val().length==0)
			$("#buscador_cco").attr('valor','');

		esperar();
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'traer_camas',
			cod_cco:		   $("#buscador_cco").attr('valor'),
			cod_hab:		   $("#buscador_hab").attr('valor')

		},function (data) {
			$("#div_habitaciones").html(data);
			$.unblockUI();
		});
	}

	function cargar_cco (ArrayValores)
	{
		var ccos	= new Array();
		var index		= -1;
		var tr ;
		var n = 1;
		tr ="";
		var wfc ;
		for (var cod_ccos in ArrayValores)
		{
			index++;
			ccos[index] = {};
			ccos[index].value  = cod_ccos+'-'+ArrayValores[cod_ccos];
			ccos[index].label  = cod_ccos+'-'+ArrayValores[cod_ccos];
			ccos[index].nombre = cod_ccos+'-'+ArrayValores[cod_ccos];
			ccos[index].valor  = cod_ccos;
		}

		$( "#buscador_cco" ).autocomplete({

			minLength: 	0,
			source: 	ccos,
			select: 	function( event, ui ){
				$( "#buscador_cco" ).val(ui.item.nombre);
				$( "#buscador_cco" ).attr('valor', ui.item.valor);
				traer_camas();
				return false;


			}

		});

	}
	function cargar_hab (ArrayValores)
	{
		var habs	= new Array();
		var index		= -1;
		var tr ;
		var n = 1;
		tr ="";
		var wfc ;
		for (var cod_habs in ArrayValores)
		{
			index++;
			habs[index] = {};
			habs[index].value  = cod_habs;
			habs[index].label  = cod_habs;
			habs[index].nombre = ArrayValores[cod_habs];
			habs[index].valor  = cod_habs;
		}

		$( "#buscador_hab" ).autocomplete({

			minLength: 	0,
			source: 	habs,
			select: 	function( event, ui ){
				$( "#buscador_hab" ).val(ui.item.nombre);
				$( "#buscador_hab" ).attr('valor', ui.item.valor);
				traer_camas();
				return false;
			}

		});

	}

	function validaClaseHosp(cod_hab)
	{

		if( $("#tipo_hab_hospitalaria_"+cod_hab).val()!='')
				$("#tipo_hab_hospitalaria_"+cod_hab).removeClass("campoRequerido");
		else
			$("#tipo_hab_hospitalaria_"+cod_hab).addClass("campoRequerido");

	}

	function grabar_tipo_habitacion_hospitalaria(cod_hab)
	{


		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'grabar_tipo_hab_hosp',
			cod_hab:		   cod_hab,
			cod_tip_hab:	   $("#tipo_hab_hospitalaria_"+cod_hab).val()


		},function (data) {
		});

	}

	function validaClaseFac(cod_hab)
	{

		if( $("#tipo_hab_facturacion_"+cod_hab).val()!='')
				$("#tipo_hab_facturacion_"+cod_hab).removeClass("campoRequerido");
		else{
			$("#tipo_hab_facturacion_"+cod_hab).addClass("campoRequerido");
		}


	}

	function grabar_tipo_habitacion_facturacion(cod_hab)
	{

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'grabar_tipo_hab_fact',
			cod_hab:		   cod_hab,
			cod_tip_hab:	   $("#tipo_hab_facturacion_"+cod_hab).val()


		},function (data) {

			$("#div_tar_"+cod_hab).html("<a style='cursor : pointer' onclick='traer_tarifas(\""+cod_hab+"\")'>Ver tarifa(s)</a>");

		});

	}

	function traer_tarifas(cod_hab)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      '',
			wemp_pmla:         $('#wemp_pmla').val(),
			accion:            'traer_tarifas',
			wcod_habs:		   $("#tipo_hab_facturacion_"+cod_hab).val()

		},function (data) {
			$("#div_tarifas").html(data);
			 $( "#div_tarifas" ).dialog({
				show: {
					effect: "blind",
					duration: 100
				},
				hide: {
					effect: "blind",
					duration: 100
				},
				height: 400,
				width:  400,
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Detalle de tarifa Habitacion tipo: "+$("#tipo_hab_facturacion_"+cod_hab).val()
				});
		});

	}






//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>




<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.ui-autocomplete{
			max-width:  230px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size:  9pt;
        }

		.fixed-dialog{
			 position: fixed;
			 top: 100px;
			 left: 100px;
		}

		.ui-dialog
		{
			background: #FFFEEB;
		}

		.ui-widget-overlay {
		  opacity: .20;
		  filter: Alpha(Opacity=20);
		}

		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:2px;opacity:1;}
		#tooltip h7, #tooltip div{margin:0; width:auto}
		.Titulo_azul{
			color:#3399ff;
			font-weight: bold;
			font-family: verdana;
			font-size: 10pt;
		}
		.BordeGris{
			border: 1px solid #999999;
		}
		.BordeNaranja{
			border: 1px solid orange;
		}
		.campoRequerido{
			border: 1px outset #3399ff ;
			background-color:lightyellow;
			color:gray;

		}
		.campoRequerido:focus{

			background-color:white;
			color:black;
		}

		.pad{
            padding: 3px;
        }

		.Scrollvertical {
			height: 340px;
			overflow: auto;
			overflow-y: scroll;
		}

		.letrapequena{
			font-size:  8pt;
		}

	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	// -->	ENCABEZADO
	cargar_hiddens_para_autocomplete();
	echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='permisos' value=''>";
	echo "<table id='tabla_contenedora' align='center' width='100%'>
			<tr>
				<td id='td_habitaciones' class='BordeNaranja' style='padding: 20px;' align='center'>
						<div align='left'>";
	echo					traer_buscadores();
	echo"				</div>
						<br>
						<div align='left' id='div_habitaciones' class='BordeGris Scrollvertical' style='padding:10px;'  >";

	echo "				</div>
						<br>
						<div align='left' id='div_tarifas' title='Detalle de tarifas'  >";

	echo "				</div>

				</td>
			</tr>
		 </table>";

	?>
	</BODY>
<!--=====================================================================================================================================================================
	F I N   B O D Y
=====================================================================================================================================================================-->
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L
//=======================================================================================================================================================
}

}//Fin de session
?>