<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='En Desarrollo, jerson';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
//
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
	

	include_once("root/comun.php");
	

	include_once("ips/funciones_facturacionERP.php");
	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

function traer_mercados($whistoria,$wingreso,$wemp_pmla)
{
	global $conex;
	$wbasedato 	 = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$haypaquetes = false;
	$hayprocedimientos = false;

	$q = "SELECT Mpapro ,Pronom
			FROM  ".$wbasedato."_000207 , ".$wbasedato."_000103
			WHERE  Mpahis = '".$whistoria."'
			  AND  Mpaing = '".$wingreso."'
			  AND  Mpapro = Procod
			GROUP BY Mpapro";

	$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());
	//echo $q;
	$i=0;
	$data = array();
	while($row = mysql_fetch_array($res))
	{

			$hayprocedimientos = true;
	}

	$q = "SELECT Mpapro, Paqnom
		    FROM ".$wbasedato."_000207 , ".$wbasedato."_000113
			WHERE  Mpahis = '".$whistoria."'
			  AND  Mpaing = '".$wingreso."'
			  AND  Mpapro = Paqcod
			GROUP BY Mpapro";

	$res	= mysql_query($q, $conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());



	while($row = mysql_fetch_array($res))
	{
		$haypaquetes = true;
	}

	if( $hayprocedimientos || $haypaquetes )
	{
		$data[1]['codigo'] = "Mercado";
		$data[1]['nombre'] = "Mercado General";
	}

	return $data;


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
		case 'cargar_mercados':
		{

			$data = traer_mercados ($whistoria,$wingreso,$wemp_pmla);
			echo json_encode($data);
			break;
			return;
		}

		case 'cargar_datos_paciente':
		{
			$data = cargar_datos($whistoria, $wing, $wcargos_sin_facturar, $welemento);
			echo json_encode($data);
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
  <title>...</title>
</head>

<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link rel="stylesheet" href="../../../include/ips/facturacionERP.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<script src="../../../include/ips/funcionInsumosqxERP.js" type="text/javascript"></script>
<script src="../../../include/root/toJson.js" type="text/javascript"></script>

<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================
var tipoEmpresa ="";
$(document).ready(function() {

		$( "#accordionDatosPaciente" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
		$( "#accordionContenido" ).accordion({
			collapsible: true,
			heightStyle: "content",
			active: -1
		});

		crear_div('grabardevolucion','divmercado','no', '',$("#responsable_original_tal").val(),$("#tarifa_original_tal").val(),'','','cc',$("#whistoria_tal").val(),$("#wing_tal").val(),tipoEmpresa,'off','');
});

//--------------------------------------------------------------------------
//	Funcion que hace el llamado para obtener los datos basicos del paciente
//--------------------------------------------------------------------------
function cargarDatosPaciente(elemento)
{
	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:      		'',
		wemp_pmla:         		$('#wemp_pmla').val(),
		accion:            		'cargar_datos_paciente',
		whistoria:		   		$('#whistoria').val(),
		wing:					$('#wing').val(),
		wcargos_sin_facturar:	'ok',
		welemento:				elemento

	},function(data){

		// --> data.prueba valida si la historia existe
		if(data.prueba == 'no')
		{
			alert('La historia no existe');
			$('#whistoria').val('');
			$('#wing').val('');
		}
		else
		{
			// --> data.error indica si hay un error  en el llamado de la funcion
			if(data.error ==1)
			{
				alert(data.mensaje);
			}
			else
			{
				// --> datos traidos desde la funcion

				// --> Historia
				$("#whistoria_tal").val($('#whistoria').val());

				// --> Ingreso
				$("#wing_tal").val(data.wwing);
				$("#wing").val(data.wwing);

				// --> Paciente
				$("#wno1_tal").val(data.wno1);
				$("#wno2_tal").val(data.wno2);
				$("#wap1_tal").val(data.wap1);
				$("#wap2_tal").val(data.wap2);
				$("#nombrePaciente").html(data.wno1+" "+data.wno2+" "+data.wap1+" "+data.wap2);

				// --> Documento
				$("#documento").html(data.wdoc);
				$("#wdoc_tal").val(data.wdoc);
				$("#div_documento_tal").val(data.wdoc);

				// --> Responsable
				$("#nombreResponsable").html(data.wcodemp+"-"+data.wnomemp);
				$("#wnomemp_tal").val(data.wnomemp);
				$("#div_responsable_tal").val(data.responsable);
				$("#responsable_original_tal").val(data.wcodemp);

				// --> Fecha de ingreso
				$("#fechaIngreso").html(data.wfecing);
				$("#wfecing_tal").val(data.wfecing);

				// --> Servicio de facturacion
				$("#nombreCco").html($("#wcco_tal").val()+"-"+$("#div_servicio_tal").val());

				// --> Tipo de ingreso
				$("#tipoIngreso").html(data.nombre_tipo_ingreso);

				// --> Tipo de servicio
				$("#wpactam_tal").val(data.wpactam);

				// --> Nombre del servicio de ingreso
				$("#wser_tal").val(data.wser);
				$("#nomservicio_tal").val(data.wnombreservicio);
				$("#servicio").html(data.wser+"-"+data.wnombreservicio);

				// --> Tarifa
				$("#nombreTarifa").html(data.tarifa);
				$("#div_tarifa_tal").val(data.tarifa);
				$("#tarifa_original_tal").val(data.wtar);

				$("#wtip_paciente_tal").val(data.wtip_paciente);
				$("#wtipo_ingreso_tal").val(data.tipo_ingreso);

				tipoEmpresa = data.tipoEmpresa;
				//ventana_insumo('grabarpaciente','divmercado','no',procedimiento,entidad,tarifa,nom_procedimiento,nom_entidad,nom_tarifa,whistoria,wingreso,cod_tipo_empresa,paquete,nom_tipo_empresa);

			}
		}
	},
	'json');
}


function crear_div(operacion,div,modal, procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,ant_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa)
{

	$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
	{
		consultaAjax:      		'',
		wemp_pmla:         		$('#wemp_pmla').val(),
		accion:            		'cargar_mercados',
		whistoria:		   		$('#whistoria').val(),
		wingreso:				$('#wing').val()

	},function(data){

		var data = eval('(' + data + ')');

		var index = 0;
		var c_procedimiento;
		for (var datos in data)
		{
			index++;
			c_procedimiento = data[index].codigo;
			n_procedimiento = data[index].nombre;


				$("#"+div).append("<br><div class='acordionmercado'><h3>"+n_procedimiento+"</h3><fieldset align='center' ><legend align='left'></legend><div id='div_"+c_procedimiento+"'></div></fieldset></div>");
				ventana_insumo(operacion,"div_"+c_procedimiento+"",modal, c_procedimiento,c_responsable,c_tarifa,n_tarifa,n_responsable,n_procedimiento,whistoria,wingreso,tipoEmpresa,paquete,n_tipo_empresa,'','','');

		}

		$( ".acordionmercado" ).accordion({
			collapsible: true,
			heightStyle: "content",
			active: false
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
			max-width: 	230px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	9pt;
		}
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:2px;opacity:1;}
		#tooltip h7, #tooltip div{margin:0; width:auto}
		.Titulo_azul{
			color:#3399ff;
			font-weight: bold;
			font-family: verdana;
			font-size: 10pt;
		}
		.Bordegris{
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
		.pad{
			padding: 	4px;
		}
		.filaDetalle{
			font-size	: 8pt;
			font-family': verdana;
		}

		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
			background-color: #FFCC66;
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
	//encabezado("", $wactualiz, 'clinica');



	echo"
	<div align='center'>
		<div width='95%' id='accordionDatosPaciente'>
			<h3>DATOS DEL PACIENTE</h3>
			<div class='pad' align='center' id='DatosPaciente'>
				<table width='90%' style='border: 1px solid #999999;background-color: #ffffff;'>
					<tr class='fila1' style='font-weight: bold;'>
						<td width='11%'>Historia:</td>
						<td width='15%'>Ingreso Nro:</td>
						<td colspan='2'>Paciente:</td>
						<td colspan='2'>Documento:</td>
					</tr>
					<tr class='fila2'>
						<td><input type='text' id='whistoria' size='15' onchange='cargarDatosPaciente(\"whistoria\")' value='".$whistoria."'></td>
						<td><input type='text' id='wing' 	   size='3'  onchange='cargarDatosPaciente(\"wing\")' value='".$wing."'></td>
						<td id='nombrePaciente' colspan='2'>".$wno1." ".$wno2." ".$wap1." ".$wap2."</td>
						<td colspan='2' id='documento'>".$wdoc."</td>
					</tr>
					<tr class='fila1' style='font-weight: bold;'>
						<td>Fecha Ing:</td>
						<td>Servicio de Ingreso:</td>
						<td width='12%'>Tipo de Ingreso:</td>
						<td>Responsable:</td>
						<td>Tarifa:</td>
						<td>Servicio de facturación:</td>
					</tr>
					<tr class='fila2'>
						<td id='fechaIngreso'>".$wfecing."</td>
						<td id='servicio' >".$nomservicio."</td>
						<td id='tipoIngreso' >".$wtipo_ingreso_nom_tal."</td>
						<td id='nombreResponsable' >".$div_responsable."</td>
						<td id='nombreTarifa'>".$div_tarifa."</td>
						<td id='nombreCco'>".$wcco."-".$div_servicio."</td>
					</tr>
				</table>
			</div>
		</div>
		<div width='95%' id='accordionContenido'>
			<h3>MERCADO DE CIRUGIA</h3>
			<div align='center'>
				<div  id='divmercado'>

				</div>
			</div>

		</div>
	</div>";
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
